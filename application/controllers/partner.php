<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

//error_reporting(E_ERROR);
//ini_set('display_errors', '1');

/**
 * REST APIs for Partners like Snapdeal to insert / update orders in our CRM
 *
 * @author anujaggarwal
 */
class Partner extends CI_Controller {

    private $token = null;
    private $header = null;
    private $partner = null;
    private $requestUrl = '';
    private $jsonRequestData = null;
    private $jsonResponseString;

    //private $debug;

    function __Construct() {
        parent::__Construct();

        $this->load->model('partner_model');
        $this->load->model('user_model');
        $this->load->model('booking_model');
        $this->load->model('vendor_model');
        $this->load->model('dealer_model');
        $this->load->model('reusable_model');
        $this->load->model('service_centers_model');
        $this->load->library("miscelleneous");
        $this->load->library('email');
        $this->load->library('notify');
        $this->load->library('partner_utilities');
        $this->load->model('upcountry_model');
        $this->load->library("asynchronous_lib");
        $this->load->library('booking_utilities');
        $this->load->library('initialized_variable');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('PHPReport');
        $this->load->library('push_notification_lib');
        $this->load->model('service_centre_charges_model');
        // $this->load->library('push_inbuilt_function_lib');
    }

    public function index() {
        log_message('info', "Entering: " . __METHOD__);

        $activity = array('activity' => __METHOD__);
        $this->partner_model->log_partner_activity($activity);

        echo "Success";
    }

    /**
     * Insert new order
     *
     * API to insert new order in the CRM
     *
     * @access	public
     * @return	Success / Error code as per the document
     */
    public function submitRequest() {
        log_message('info', "Entering: " . __METHOD__);
        $this->requestUrl = __METHOD__;
        $auth = $this->checkAuthentication();

        if ($auth) {
            log_message('info', __METHOD__ . ":: Token validated (Partner ID: " . $this->partner['id'] . ")");

            $input_d = $this->get_requestedData();

            if ($input_d) {
                $this->jsonRequestData = $input_d;
                $requestData = json_decode($input_d, TRUE);
                if (!(json_last_error() === JSON_ERROR_NONE)) {
                    log_message('info', __METHOD__ . ":: Invalid JSON");

                    //Invalid json
                    $this->jsonRequestData = $input_d;
                    $this->sendJsonResponse(array(ERR_INVALID_JSON_INPUT_CODE, ERR_INVALID_JSON_INPUT_MSG));
                } else {
                    if (!empty($requestData['brand'])) {
                        //Sanitizing Brands Before Adding
                        $requestData['brand'] = preg_replace('/[^A-Za-z0-9 ]/', '', $requestData['brand']);
                    }
                    
                    $orderID =  $requestData['orderID'];
            
                    if(isset($requestData['itemID']) && !empty($requestData['itemID'])){

                        $requestData['orderID'] = $requestData['orderID'] . "-" . $requestData['itemID'];
                    }
            
                    //Check whether the required details are present in the request
                    //And request doesn't exist in database
                    $is_valid = $this->validate_submit_request_data($requestData);
                    if ($is_valid['result'] == TRUE) {
                        log_message('info', __METHOD__ . ":: Request validated");

                        //Search for user
                        //Insert user if phone number doesn't exist
                        $output = $this->user_model->get_users_by_any(array("users.phone_number" => $requestData['mobile']));
                        $distict_details = $this->vendor_model->get_distict_details_from_india_pincode(trim($requestData['pincode']));


                        if (empty($output)) {
                            log_message('info', $requestData['mobile'] . ' does not exist');

                            //User doesn't exist
                            $user['name'] = $requestData['name'];
                            $user['phone_number'] = $requestData['mobile'];
                            $user['alternate_phone_number'] = (isset($requestData['alternatePhone']) ? $requestData['alternatePhone'] : "");
                            $user['user_email'] = (isset($requestData['email']) ? $requestData['email'] : "");

                            isset($requestData['landmark']) ?
                                            ($address = $requestData['address'] . ", " . $requestData['landmark']) :
                                            ($address = $requestData['address']);

                            $user['home_address'] = $address;
                            $user['pincode'] = $requestData['pincode'];
                            $user['city'] = $requestData['city'];

                            $user['state'] = $distict_details['state'];

                            $user_id = $this->user_model->add_user($user);

                            //echo print_r($user, true), EOL;
                            //Add sample appliances for this user
                            $count = $this->booking_model->getApplianceCountByUser($user_id);

                            //Add sample appliances if user has < 5 appliances in wallet
                            if ($count < 5) {
                                $this->booking_model->addSampleAppliances($user_id, 5 - intval($count));
                            }
                        } else {
                            log_message('info', $requestData['mobile'] . ' exists');
                            //User exists
                            $user['name'] = $requestData['name'];
                            $user['user_email'] = (isset($requestData['email']) ? $requestData['email'] : "");
                            $user_id = $output[0]['user_id'];
                        }

                        //if productType is valid then proceed else send invalid json response 
                        $is_productType_valid = $this->validate_product_type($requestData);

                        if (!isset($is_productType_valid['error'])) {
                            log_message('info', 'Product type: ' . $requestData['product']);
                            $prod = trim($requestData['product']);

                            //check service_id exist or not
                            if(!empty($requestData['brand'])){
                                $where = array('product_description' => trim($requestData['productType']),
                                                'brand' => $requestData['brand']);
                            }else{
                                $where = array('product_description' => trim($requestData['productType']));
                            }
                            $service_appliance_data = $this->booking_model->get_service_id_by_appliance_details($where);
                            $service_id = "";
                            if (!empty($service_appliance_data)) {
                                log_message('info', __FUNCTION__ . " Get Service ID  ");

                                $service_id = $service_appliance_data[0]['service_id'];
                                $lead_details['service_appliance_data'] = $service_appliance_data[0];
                                $lead_details['Product'] = $service_appliance_data[0]['services'];
                            } else {
                                if (stristr($prod, "Washing Machine") || stristr($prod, "WashingMachine") || stristr($prod, "Dryer")) {
                                    $lead_details['Product'] = 'Washing Machine';
                                }
                                if (stristr($prod, "Television") || stristr($prod, "Monitor") || stristr($prod, "Televisions")) {
                                    $lead_details['Product'] = 'Television';
                                }
                                if (stristr($prod, "Airconditioner") || stristr($prod, "Air Conditioner")) {
                                    $lead_details['Product'] = 'Air Conditioner';
                                }
                                if (stristr($prod, "Refrigerator")) {
                                    $lead_details['Product'] = 'Refrigerator';
                                }
                                if (stristr($prod, "Microwave")) {
                                    $lead_details['Product'] = 'Microwave';
                                }
                                if (stristr($prod, "Purifier")) {
                                    $lead_details['Product'] = 'Water Purifier';
                                }
                                if (stristr($prod, "Chimney")) {
                                    $lead_details['Product'] = 'Chimney';
                                }
                                if (stristr($prod, "Geyser")) {
                                    $lead_details['Product'] = 'Geyser';
                                }
                                if (stristr($prod, "Smart Speaker")) {
                                    $lead_details['Product'] = 'Smart Speaker';
                                }
                                if (stristr($prod, "Cooler")) {
                                    $lead_details['Product'] = 'Air Cooler';
                                }
                                if (stristr($prod, "Purifier")) {
                                    $lead_details['Product'] = 'Purifier';
                                }
                                if (stristr($prod, "Stove")) {
                                    $lead_details['Product'] = 'Gas Stove';
                                }
                                
                                if (stristr($prod, "Mixer Grinder") || stristr($prod, "Juicer Mixer Grinder") 
                                    || stristr($prod, "Juicer Mixer Grinder") 
                                    || stristr($prod, "Air Fryer") 
                                    || stristr($prod, "Cookware") 
                                    || stristr($prod, "Gas Burner") 
                                    || stristr($prod, "Hand Blender") 
                                    || stristr($prod, "Kettle")
                                    || stristr($prod, "Massager")
                                    || stristr($prod, "Nutri Blender") 
                                    || stristr($prod, "OTG") 
                                    || stristr($prod, "Steamer") 
                                    || stristr($prod, "Toaster") 
                                    || stristr($prod, "Vaccum Cleaner")) {

                                $lead_details['Product'] = 'SHA';
                            }


                                log_message('info', 'Product type matched: ' . $lead_details['Product']);

                                $service_id = $this->booking_model->getServiceId($lead_details['Product']);
                            }
                            if(!empty($service_id)){
                                //Assigning Booking Source and Partner ID for Brand Requested
                            // First we send Service id and Brand and get Partner_id from it
                            // Now we send state, partner_id and service_id 
                            $is_partner_id = $this->miscelleneous->_allot_source_partner_id_for_pincode($service_id, $distict_details['state'], $requestData['brand'], $this->partner['id'], true);

                            if (!empty($is_partner_id) ) {


                                $this->initialized_variable->fetch_partner_data($is_partner_id['partner_id']);

                                $booking['partner_id'] = $is_partner_id['partner_id'];
                                $booking['source'] = $this->initialized_variable->get_partner_data()[0]['code'];
                                $booking['create_date'] = date("Y-m-d H:i:s");
                                
                                $booking['origin_partner_id'] = $this->partner['id'];

                                $unit_details['partner_id'] = $booking['partner_id'];
                                $booking['order_id'] = $requestData['orderID'];
                                $appliance_details['brand'] = $unit_details['appliance_brand'] = $requestData['brand'];

                                $appliance_details['model_number'] = $unit_details['model_number'] = (isset($requestData['model']) ? $requestData['model'] : "");
                                $category = $capacity = "";
                                if (isset($requestData['category'])) {
                                    $category = $requestData['category'];
                                }

                                if (isset($requestData['subCategory'])) {
                                    $capacity = $requestData['subCategory'];
                                }

                                //Product description
                                $appliance_details['description'] = $unit_details['appliance_description'] = trim($requestData['productType']);

                                //Check for all optional parameters before setting them
                                $appliance_details['category'] = $unit_details['appliance_category'] = isset($lead_details['service_appliance_data']['category']) ? $lead_details['service_appliance_data']['category'] : $category;

                                $appliance_details['capacity'] = $unit_details['appliance_capacity'] = isset($lead_details['service_appliance_data']['capacity']) ? $lead_details['service_appliance_data']['capacity'] : $capacity;


//                                if ($this->initialized_variable->get_partner_data()[0]['partner_type'] == OEM) {
//                                    //if partner type is OEM then sent appliance brand in argument
//                                    $prices = $this->partner_model->getPrices($service_id, $unit_details['appliance_category'], $unit_details['appliance_capacity'], $booking['partner_id'], $requestData['requestType'], $unit_details['appliance_brand'], false);
//                                
//                                    
//                                } else 
                                if(!empty($is_partner_id['brand'])){ 
                                    
                                    $prices = $this->partner_model->getPrices($service_id, $unit_details['appliance_category'], $unit_details['appliance_capacity'], $booking['partner_id'], $requestData['requestType'], $unit_details['appliance_brand'], false);
                                    
                                } else {
                                    //if partner type is not OEM then dose not sent appliance brand in argument
                                    $prices = $this->partner_model->getPrices($service_id, $unit_details['appliance_category'], $unit_details['appliance_capacity'], $booking['partner_id'], $requestData['requestType'], "", false);
                                }
                                $booking['amount_due'] = '0';

                                //log_message('info', __FUNCTION__ . " => Prices Check ". print_r($prices));
                                $is_price = array();
                                if (!empty($prices) && count($prices) == 1) {
                                    log_message('info', __FUNCTION__ . " => Prices Found");
                                    $unit_details['price_tags'] = $prices[0]['service_category'];
                                    $unit_details['id'] = $prices[0]['id'];

                                    $unit_details['around_paid_basic_charges'] = $unit_details['around_net_payable'] = "0.00";
                                    $unit_details['partner_paid_basic_charges'] = $prices[0]['partner_net_payable'];
                                    $unit_details['partner_net_payable'] = $prices[0]['partner_net_payable'];
                                    $booking['amount_due'] = $prices[0]['customer_net_payable'];
                                    $is_price['customer_net_payable'] = $prices[0]['customer_net_payable'];
                                    $is_price['is_upcountry'] = $prices[0]['is_upcountry'];
                                }
                                if (isset($requestData['itemID'])) {
                                    $unit_details['sub_order_id'] = $requestData['itemID'];
                                }
                                $booking['booking_primary_contact_no'] = $requestData['mobile'];
                                $booking['booking_alternate_contact_no'] = (isset($requestData['alternatePhone']) ? $requestData['alternatePhone'] : "");

                                $booking['city'] = $requestData['city'];
                                $booking['booking_pincode'] = trim($requestData['pincode']);

                                $booking['booking_address'] = $requestData['address'] . ", " . (isset($requestData['landmark']) ? $requestData['landmark'] : "");
                                $booking['delivery_date'] = $this->getDateTime($requestData['deliveryDate']);
                                if(isset($requestData['servicePromiseDate'])){
                                    $booking['service_promise_date'] = $this->getDateTime($requestData['servicePromiseDate']);
                                }
                                $booking['request_type'] = $requestData['requestType'];
                                $booking['query_remarks'] = (isset($requestData['remarks']) ? $requestData['remarks'] : $requestData['requestType']);


                                //Add this as a Query now
                                $booking['booking_id'] = '';
                                $appliance_details['user_id'] = $booking['user_id'] = $user_id;
                                $appliance_details['service_id'] = $unit_details['service_id'] = $booking['service_id'] = $service_id;
                                log_message('info', __METHOD__ . ":: Service ID: " . $booking['service_id']);
                                //echo "Service ID: " . $booking['service_id'] . PHP_EOL;
                                $random_code = mt_rand(100, 999);   //return 3 digit random code to make booking id unique
                                $yy = date("y");
                                $mm = date("m");
                                $dd = date("d");
                                $booking['booking_id'] = str_pad($booking['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
                                $booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($booking['user_id'])) + 1);

                                $booking_id = $booking['source'] . "-" . $booking['booking_id'] . $random_code;

                                $unit_details['booking_id'] = $booking['booking_id'] = "Q-" . $booking_id;

                                $booking['quantity'] = '1';

                                $appliance_details['tag'] = $appliance_details['brand'] . " " . $lead_details['Product'];
                                $appliance_details['purchase_date'] = $unit_details['purchase_date'] = date('Y-m-d');

                                $appliance_details['last_service_date'] = date('Y-m-d');
                                //$booking['potential_value'] = '';
                                //Check vendor Availabilty for pincode and service id
                                $is_sms = $this->miscelleneous->check_upcountry($booking, $lead_details['Product'], $is_price, "delivered");
                                if ($is_sms) {
                                    $booking['sms_count'] = 1;
                                    $booking['internal_status'] = _247AROUND_FOLLOWUP;
                                } else {
                                    $booking['internal_status'] = SF_UNAVAILABLE_SMS_NOT_SENT;
                                }

                                $booking['current_status'] = _247AROUND_FOLLOWUP;
                                $booking['type'] = "Query";
                                $booking['booking_date'] = '';
                                $booking['booking_timeslot'] = '';
                                $booking['partner_source'] = 'STS';
                                $booking['amount_due'] = '';
                                $booking['booking_remarks'] = '';
                                $booking['state'] = $distict_details['state'];
                                $booking['district'] = $distict_details['district'];
                                $booking['taluk'] = $distict_details['taluk'];
                                $unit_details['booking_status'] = _247AROUND_FOLLOWUP;
                                if(isset($requestData['paidByCustomer'])){
                                    if(strtolower($requestData['paidByCustomer']) == "yes"){
                                        $booking['paid_by_customer'] = 1;
                                    } else {
                                        $booking['paid_by_customer'] = 0;
                                    }
                                }
                                
                                //check partner status from partner_booking_status_mapping table 
                                $actor = $next_action = 'NULL';
                                $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'], $booking['partner_id'], $booking['booking_id']);
                                if (!empty($partner_status)) {
                                    $booking['partner_current_status'] = $partner_status[0];
                                    $booking['partner_internal_status'] = $partner_status[1];
                                    $actor = $booking['actor'] = $partner_status[2];
                                    $next_action = $booking['next_action'] = $partner_status[3];
                                }

                                //Insert query
//                        echo print_r($booking, true) . "<br><br>";
//                        echo print_r($appliance_details, true) . "<br><br>";exit();
                                $this->booking_model->addbooking($booking);

                                //echo print_r($booking, true) . "<br><br>";
                                $unit_details['appliance_id'] = $this->booking_model->addappliance($appliance_details);

                                if (!empty($prices)) {
                                    $this->booking_model->insert_data_in_booking_unit_details($unit_details, $booking['state'], 0);
                                } else {
                                    $this->booking_model->addunitdetails($unit_details);
                                }

                                $p_login_details = $this->dealer_model->entity_login(array('entity_id' => $this->partner['id'], "user_id" => strtolower($this->partner['public_name']."-sts")));

                                $this->notify->insert_state_change($booking['booking_id'], _247AROUND_FOLLOWUP, _247AROUND_NEW_QUERY, $booking['query_remarks'], $p_login_details[0]['agent_id'], 
                                        $requestData['partnerName'],$actor,$next_action, $this->partner['id']);
                                
                                //Send sms to customer for asking to send its purchanse invoice in under warrenty calls
                                if($booking['partner_id'] == VIDEOCON_ID){
                                    if((stripos($booking['request_type'], 'In Warranty') !== false) || stripos($booking['request_type'], 'Extended Warranty') !== false){
                                        $url1 = base_url() . "employee/do_background_process/send_sms_email_for_booking";
                                        $send1['booking_id'] = $booking['booking_id'];
                                        $send1['state'] = "SendWhatsAppNo";
                                        $this->asynchronous_lib->do_background_process($url1, $send1);
                                    }
                                }
                                
                                // if (empty($booking['state'])) {
                                //$to = NITS_ANUJ_EMAIL_ID;
                                //$message = "Pincode " . $booking['booking_pincode'] . " not found for Booking ID: " . $booking['booking_id'];
                                //$this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", 'Pincode Not Found', $message, "");
                                // }
                                //Send response
                                $this->jsonResponseString['response'] = array(
                                    "orderID" => $orderID,
                                    "247aroundBookingID" => $booking_id,
                                    "247aroundBookingStatus" => $booking['current_status']);
                                $this->sendJsonResponse(array(SUCCESS_CODE, SUCCESS_MSG));
                            } else {
                                $this->jsonResponseString['response'] = NULL;
                                $this->sendJsonResponse(array(ERR_INVALID_SERVICE_AREA_CODE, ERR_INVALID_SERVICE_AREA_MSG));
                            }
                            } else {
                                $this->jsonResponseString['response'] = NULL;
                                $this->sendJsonResponse(array(ERR_INVALID_PRODUCT_CODE, ERR_INVALID_PRODUCT_MSG));
                            }
                            
                        } else {
                            log_message('info', __METHOD__ . ":: Request validation fails for product type. " . print_r($requestData['productType'], true));

                            $this->jsonResponseString['response'] = NULL;
                            $this->sendJsonResponse(array(ERR_INVALID_PRODUCT_TYPE_CODE, ERR_INVALID_PRODUCT_TYPE_MSG));
                        }
                    } else {
                        log_message('info', __METHOD__ . ":: Request validation fails. " . print_r($is_valid, true));

                        //Request validation fails
                        //If it is because of pre-existing order id, return booking id as a part of response
                        if ($is_valid['code'] == ERR_ORDER_ID_EXISTS_CODE) {
                            log_message('info', "Reason: ERR_ORDER_ID_EXISTS_CODE");

                            $lead_details = $is_valid['lead'];
                            $this->jsonResponseString['response'] = array(
                                "247aroundBookingID" => $lead_details['booking_id'],
                                "247aroundBookingStatus" => $lead_details['current_status'],
                                "247aroundBookingRemarks" => $lead_details['query_remarks']);

                            $this->sendJsonResponse(array($is_valid['code'], $is_valid['msg']));
                        } else {
                            $this->jsonResponseString['response'] = NULL;
                            $this->sendJsonResponse(array($is_valid['code'], $is_valid['msg']));
                        }
                    }
                }
            }
        }
    }

    /**
     * Cancel an existing order
     *
     * API to cancel an existing order in the CRM
     *
     * @access	public
     * @return	Success / Error code as per the document
     */
    public function cancelRequest() {
        log_message('info', "Entering: " . __METHOD__);
        $this->requestUrl = __METHOD__;

        $auth = $this->checkAuthentication();

        if ($auth) {
            //Token validated
            $input_d = $this->get_requestedData();

            if ($input_d) {
                $this->jsonRequestData = $input_d;
                $requestData = json_decode($input_d, TRUE);

                //1. Validate request - check all essential parameters are there
                //2. Check order id and booking id corresponds to this partner and
                //both are valid
                $is_valid = $this->validate_cancel_request_data($requestData);
                if ($is_valid['result'] == TRUE) {
                    //Cancel current request
                    $lead_details = $is_valid['lead'];
                    if ($this->process_cancel_request($requestData) === TRUE) {
                        //Send response
                        $this->jsonResponseString['response'] = array(
                            "247aroundBookingID" => $lead_details['booking_id'],
                            "247aroundBookingStatus" => "Cancelled");
                        $this->sendJsonResponse(array(SUCCESS_CODE, SUCCESS_MSG));
                    } else {
                        log_message('info', __METHOD__ . ":: Cancellation fails.");

                        //Some error occured while cancelling
                        $this->sendJsonResponse(array(ERR_GENERIC_ERROR_CODE,
                            ERR_GENERIC_ERROR_MSG));
                    }
                } else {
                    log_message('info', __METHOD__ . ":: Request validation fails. " . print_r($is_valid, true));

                    //Request validation fails
                    $this->sendJsonResponse(array($is_valid['code'], $is_valid['msg']));
                }
            }
        }
    }

    /**
     * Schedule timeslot for an order
     *
     * Update an existing order for a new timeslot
     *
     * @access	public
     * @return	Success / Error code as per the document
     */
    public function updateRequestTimeslot() {
        log_message('info', "Entering: " . __METHOD__);
        $this->requestUrl = __METHOD__;

        $auth = $this->checkAuthentication();

        if ($auth) {
            //Token validated
            //Token validated
            $input_d = $this->get_requestedData();

            if ($input_d) {
                $this->jsonRequestData = $input_d;
                $requestData = json_decode($input_d, TRUE);

                //1. Validate request - check all essential parameters are there
                //2. Check order id and booking id corresponds to this partner and
                //both are valid
                $is_valid = $this->validate_update_timeslot_data($requestData);
                if ($is_valid['result'] == TRUE) {
                    //Schedule request
                    $lead_details = $is_valid['lead'];
                    $sch_req = $this->process_schedule_request($requestData);

                    if ($sch_req['result'] === TRUE) {
                        //Scheduling successful, Send response
                        $this->jsonResponseString['response'] = array(
                            "247aroundBookingID" => $lead_details['booking_id'],
                            "installationTimeslotStart" => $sch_req['timeslotStart'],
                            "installationTimeslotEnd" => $sch_req['timeslotEnd']);
                        $this->sendJsonResponse(array(SUCCESS_CODE, SUCCESS_MSG));
                    } else {
                        log_message('info', __METHOD__ . ":: Scheduling fails. " . print_r($sch_req, true));

                        //Some error occured while cancelling
                        $this->sendJsonResponse(array(ERR_GENERIC_ERROR_CODE,
                            ERR_GENERIC_ERROR_MSG));
                    }
                } else {
                    log_message('info', __METHOD__ . ":: Request validation fails. " . print_r($is_valid, true));

                    //Request validation fails
                    $this->sendJsonResponse(array($is_valid['code'], $is_valid['msg']));
                }
            }
        }
    }

    /**
     * Get status
     *
     * API to find out the current status of an existing order
     *
     * @access	public
     * @return	Success / Error code as per the document
     */
    public function getRequestStatus() {
        log_message('info', "Entering: " . __METHOD__);
        $this->requestUrl = __METHOD__;

        $auth = $this->checkAuthentication();

        if ($auth) {
            //Token validated
            //Token validated
            $input_d = $this->get_requestedData();

            if ($input_d) {
                $this->jsonRequestData = $input_d;
                $requestData = json_decode($input_d, TRUE);

                //1. Validate request - check all essential parameters are there
                //2. Check order id and booking id corresponds to this partner and
                //both are valid
                $is_valid = $this->validate_get_request_data($requestData);
                if ($is_valid['result'] == TRUE) {
                    //Send response
                    $lead_details = $is_valid['lead'];
                    $this->jsonResponseString['response'] = array(
                        "247aroundBookingID" => $lead_details['booking_id'],
                        "247aroundBookingStatus" => $lead_details['partner_internal_status'],
                        "247aroundBookingRemarks" => $lead_details['booking_remarks']);
                    $this->sendJsonResponse(array(SUCCESS_CODE, SUCCESS_MSG));
                } else {
                    log_message('info', __METHOD__ . ":: Request validation fails. " . print_r($is_valid, true));

                    //Request validation fails
                    $this->sendJsonResponse(array($is_valid['code'], $is_valid['msg']));
                }
            }
        } else {
            log_message('info', __METHOD__ . ":: Invalid token: " . $this->token);

            //invalid token
            $this->sendJsonResponse(array(ERR_INVALID_AUTH_TOKEN_CODE, ERR_INVALID_AUTH_TOKEN_MSG));
        }
    }

    /**
     * Get booking details
     *
     * API to find out details for a booking. This API is used by 247around internally
     * and is not exposed to any partner.
     *
     * @access	public
     * @return	Success / Error code as per the document
     */
    public function getBookingDetails() {
        log_message('info', "Entering: " . __METHOD__);
        $this->requestUrl = __METHOD__;

        $auth = $this->checkAuthentication();

        if ($auth) {
            //Token validated
            //Token validated
            $input_d = $this->get_requestedData();

            if ($input_d) {
                $this->jsonRequestData = $input_d;
                $requestData = json_decode($input_d, TRUE);

                //1. Validate request - check all essential parameters are there
                $is_valid = $this->validate_get_booking_details_data($requestData);
                if ($is_valid['result'] == TRUE) {
                    //Send response: complete booking details
                    $lead_details = $is_valid['lead'];
                    $this->jsonResponseString['response'] = array(
                        "booking_id" => $lead_details['query1'][0]['booking_id'],
                        "name" => $lead_details['query1'][0]['name'],
                        "booking_primary_contact_no" => $lead_details['query1'][0]['booking_primary_contact_no'],
                        "booking_alternate_contact_no" => $lead_details['query1'][0]['booking_alternate_contact_no'],
                        "city" => $lead_details['query1'][0]['city'],
                        "home_address" => $lead_details['query1'][0]['home_address'],
                        "pincode" => $lead_details['query1'][0]['pincode'],
                        "description" => $lead_details['query2'][0]['appliance_description'],
                        "services" => $lead_details['query1'][0]['services'],
                        "appliance_brand" => $lead_details['query2'][0]['appliance_brand'],
                        "order_id" => $lead_details['query1'][0]['order_id'],
                    );
                    $this->sendJsonResponse(array(SUCCESS_CODE, SUCCESS_MSG));
                } else {
                    log_message('info', __METHOD__ . ":: Request validation fails. " . print_r($is_valid, true));

                    //Request validation fails
                    $this->sendJsonResponse(array($is_valid['code'], $is_valid['msg']));
                }
            }
        }
    }

    /*
     * Dummy function to test Partner Update Status API.
     * It just returns the POST data it has received.
     */

    function updateStatus() {
        log_message('info', "Entering: " . __METHOD__);
        $this->requestUrl = __METHOD__;

        //Default values
        $this->jsonResponseString['response'] = NULL;
        $this->jsonResponseString['code'] = ERR_GENERIC_ERROR_CODE;
        $this->jsonResponseString['result'] = ERR_GENERIC_ERROR_MSG;

        //Save header / ip address in DB
        $h = $this->getallheaders();
        if ($h === FALSE) {
//	    $activity = array('activity' => __METHOD__ . '::Headers', 'data' => NULL);
//	    $this->partner_model->log_partner_activity($activity);
//	    $this->sendJsonResponse(array(ERR_GENERIC_ERROR_CODE, ERR_GENERIC_ERROR_MSG));

            log_message('info', __METHOD__ . "=> " . ERR_GENERIC_ERROR_MSG);
        } else {
            $this->header = json_encode($h);

//	    $activity = array('activity' => __METHOD__ . '::Headers', 'data' => json_encode($h));
//	    $this->partner_model->log_partner_activity($activity);

            $input_d = file_get_contents('php://input');
            $requestData = json_decode($input_d, TRUE);

            $this->jsonRequestData = $input_d;

            $this->jsonResponseString['response'] = array("headers" => $h, "data" => $requestData);
            $this->sendJsonResponse(array(SUCCESS_CODE, SUCCESS_MSG));

            log_message('info', __METHOD__ . "=> " . json_encode(array("headers" => $h, "data" => $requestData), JSON_UNESCAPED_SLASHES));
        }
    }

    //Validate new request data
    function validate_submit_request_data($request) {
        log_message('info', "Entering: " . __METHOD__);

        //Lead will store the booking entry if it exists
        $resultArr = array("result" => FALSE, "lead" => NULL, "code" => NULL, "msg" => NULL);
        $flag = TRUE;

        //Validate Partner Name
        if ($request['partnerName'] != $this->partner['public_name']) {
            $resultArr['code'] = ERR_INVALID_PARTNER_NAME_CODE;
            $resultArr['msg'] = ERR_INVALID_PARTNER_NAME_MSG;

            $flag = FALSE;
        }

        if (($flag === TRUE) &&
                (!isset($request['orderID']) ||
                !isset($request['product']) ||
                !isset($request['brand']) ||
                //($request['model'] == "") ||
                !isset($request['productType']) ||
                !isset($request['name']) ||
                !isset($request['mobile']) ||
                !isset($request['address']) ||
                !isset($request['pincode']) ||
                !isset($request['city']) ||
                !isset($request['requestType'])
                )) {
            $resultArr['code'] = ERR_MANDATORY_PARAMETER_MISSING_CODE;
            $resultArr['msg'] = ERR_MANDATORY_PARAMETER_MISSING_MSG;

            $flag = FALSE;
        }

        //Mandatory Parameter Missing
        if (($flag === TRUE) &&
                (($request['orderID'] == "") ||
                ($request['product'] == "") ||
                ($request['brand'] == "") ||
                //($request['model'] == "") ||
                ($request['productType'] == "") ||
                ($request['name'] == "") ||
                ($request['mobile'] == "") ||
                ($request['address'] == "") ||
                ($request['pincode'] == "") ||
                ($request['city'] == "") ||
                ($request['requestType'] == "")
                )) {
            $resultArr['code'] = ERR_MANDATORY_PARAMETER_MISSING_CODE;
            $resultArr['msg'] = ERR_MANDATORY_PARAMETER_MISSING_MSG;

            $flag = FALSE;
        }

        //SD wants booking ID to be returned in case of order ID exists
        //So changing below implementation
        /*
          //Order ID already exists - Return error
          if (($flag === TRUE) && ($this->partner_model->check_partner_lead_exists_by_order_id($request['orderID']) == TRUE)) {
          $resultArr['code'] = ERR_ORDER_ID_EXISTS_CODE;
          $resultArr['msg'] = ERR_ORDER_ID_EXISTS_MSG;

          $flag = FALSE;
          }
         *
         */
        if ($flag === TRUE) {
            $lead = $this->partner_model->get_order_id_for_partner($this->partner['id'], $request['orderID']);
            if (!is_null($lead)) {
                log_message('info', "Lead details: " . print_r($lead, true));

                //order id exists, return booking id
                $resultArr['code'] = ERR_ORDER_ID_EXISTS_CODE;
                $resultArr['msg'] = ERR_ORDER_ID_EXISTS_MSG;
                $resultArr['lead'] = $lead;

                $flag = FALSE;
            }
        }

        //Invalid Product
        $valid_products = array("Washing Machines & Dryers", "Televisions", "Air Conditioner",
            "Refrigerator", "Microwave Ovens & OTGs", "Water Purifiers", "Chimney & Hoods",
            "Geyser", "Audio System");
        if (($flag === TRUE) &&
                (in_array($request['product'], $valid_products) == FALSE)) {
            //Do not return error as of now, just log this.
            log_message('info', $request['product'] . ': Invalid product type');

            //$resultArr['code'] = ERR_INVALID_PRODUCT_CODE;
            //$resultArr['msg'] = ERR_INVALID_PRODUCT_MSG;
            //$flag = FALSE;
        }

        //Check for Request type
        $valid_request_types = array("Installation", "Installation & Demo","Repair - In Warranty",
            "Uninstallation", "Validation", "Visit Inspection", "Repair - In Warranty", "Repair - Out Of Warranty",
            "Gas Recharge", "Wet Service", "Repair - In Warranty (Home Visit)", "Repair - In Warranty (Service Center Visit)",
            "Repair - Out Of Warranty (Home Visit)", "Repair - Out Of Warranty (Service Center Visit)", "Installation & Demo (Free)", 
            "Installation & Demo (Paid)");
        if (($flag === TRUE) &&
                (in_array($request['requestType'], $valid_request_types) == FALSE)) {
            $resultArr['code'] = ERR_INVALID_REQUEST_TYPE_CODE;
            $resultArr['msg'] = ERR_INVALID_REQUEST_TYPE_MSG;

            $flag = FALSE;
        }

        //Check timeslot format validity

        if (($flag === TRUE) &&
                ($this->validate_timeslot_format($request['deliveryDate']) === FALSE)) {
            $resultArr['code'] = ERR_INVALID_TIMESLOT_FORMAT_CODE;
            $resultArr['msg'] = ERR_INVALID_TIMESLOT_FORMAT_MSG;

            $flag = FALSE;
        }
        
        if($this->partner['id'] == JEEVES_ID && $flag === TRUE){
            if(!isset($request['servicePromiseDate'])){
                
                $resultArr['code'] = ERR_SPD_DATE_MANDATORY_CODE;
                $resultArr['msg'] = ERR_SPD_DATE_MANDATORY_MSG;
                $flag = FALSE;
            } else if(empty($request['servicePromiseDate'])){
                
                $resultArr['code'] = ERR_SPD_DATE_MANDATORY_CODE;
                $resultArr['msg'] = ERR_SPD_DATE_MANDATORY_MSG;
                $flag = FALSE;
                
            } else if($this->validate_timeslot_format($request['servicePromiseDate']) === FALSE){
                
                $resultArr['code'] = ERR_INVALID_SPD_DATE_CODE;
                $resultArr['msg'] = ERR_INVALID_SPD_DATE_MSG;
                $flag = FALSE;
            }
        }


        /*
          if ($request['Mobile'] == "") {
          $resultArr['code'] = ERR_MOBILE_NUM_MISSING_CODE;
          $resultArr['msg'] = ERR_MOBILE_NUM_MISSING_MSG;
          }
         *
         */

        //If code is still empty, it means data is valid.
        if ($resultArr['code'] == "") {
            $resultArr['result'] = TRUE;
        }

        return $resultArr;
    }

    //Validate get order status API data
    function validate_get_request_data($request) {
        log_message('info', "Entering: " . __METHOD__ . ", Request data: " . print_r($request, true));

        //Lead will store the booking entry if it exists
        $resultArr = array("result" => FALSE, "lead" => NULL, "code" => NULL, "msg" => NULL);
        $flag = TRUE;

        //Validate Partner Name
        if ($request['partnerName'] != $this->partner['public_name']) {
            $resultArr['code'] = ERR_INVALID_PARTNER_NAME_CODE;
            $resultArr['msg'] = ERR_INVALID_PARTNER_NAME_MSG;

            $flag = FALSE;
        }

        //Mandatory Parameter Missing
        if (($flag === TRUE) &&
                (($request['orderID'] == "") ||
                ($request['247aroundBookingID'] == ""))
        ) {
            $resultArr['code'] = ERR_MANDATORY_PARAMETER_MISSING_CODE;
            $resultArr['msg'] = ERR_MANDATORY_PARAMETER_MISSING_MSG;

            $flag = FALSE;
        }

        //Order ID / Booking ID validation
        if ($flag === TRUE) {
            $lead = $this->partner_model->get_order_id_for_partner($this->partner['id'], $request['orderID']);
            if (!is_null($lead)) {
                //order id found, check booking id
//                if ($lead['booking_id'] != $request['247aroundBookingID']) {
//                    log_message('info', "Lead details: " . print_r($lead, true));
//
//                    $resultArr['code'] = ERR_INVALID_BOOKING_ID_CODE;
//                    $resultArr['msg'] = ERR_INVALID_BOOKING_ID_MSG;
//
//                    $flag = FALSE;
//                } else {
//                    
//                }
                //Everything fine, return lead information
                $resultArr['lead'] = $lead;
            } else {
                //Order id not found
                $resultArr['code'] = ERR_ORDER_ID_NOT_FOUND_CODE;
                $resultArr['msg'] = ERR_ORDER_ID_NOT_FOUND_MSG;

                $flag = FALSE;
            }
        }

        //If code is still empty, it means data is valid.
        if ($resultArr['code'] == "") {
            $resultArr['result'] = TRUE;
        }

        return $resultArr;
    }

    //Validate get booking details API data
    function validate_get_booking_details_data($request) {
        log_message('info', "Entering: " . __METHOD__ . ", Request data: " . print_r($request, true));

        //Lead will store the booking entry if it exists
        $resultArr = array("result" => FALSE, "lead" => NULL, "code" => NULL, "msg" => NULL);
        $flag = TRUE;

        //Validate Partner Name
        if ($request['partnerName'] != $this->partner['public_name']) {
            $resultArr['code'] = ERR_INVALID_PARTNER_NAME_CODE;
            $resultArr['msg'] = ERR_INVALID_PARTNER_NAME_MSG;

            $flag = FALSE;
        }

        //Mandatory Parameter Missing
        if (($flag === TRUE) &&
                ($request['247aroundBookingID'] == "")
        ) {
            $resultArr['code'] = ERR_MANDATORY_PARAMETER_MISSING_CODE;
            $resultArr['msg'] = ERR_MANDATORY_PARAMETER_MISSING_MSG;

            $flag = FALSE;
        }

        //Order ID / Booking ID validation
        if ($flag === TRUE) {
            $lead['query1'] = $this->booking_model->getbooking_history($request['247aroundBookingID']);
            $unit_where = array('booking_id' => $request['247aroundBookingID']);
            $lead['query2'] = $this->booking_model->get_unit_details($unit_where);

            $resultArr['lead'] = $lead;
        }

        //If code is still empty, it means data is valid.
        if ($resultArr['code'] == "") {
            $resultArr['result'] = TRUE;
        }

        return $resultArr;
    }

    //Validate cancel order API data
    function validate_cancel_request_data($request) {
        log_message('info', "Entering: " . __METHOD__ . ", Request data: " . print_r($request, true));

        //Lead will store the booking entry if it exists
        $resultArr = array("result" => FALSE, "lead" => NULL, "code" => NULL, "msg" => NULL);
        $flag = TRUE;

        //Validate Partner Name
        if ($request['partnerName'] != $this->partner['public_name']) {
            $resultArr['code'] = ERR_INVALID_PARTNER_NAME_CODE;
            $resultArr['msg'] = ERR_INVALID_PARTNER_NAME_MSG;

            $flag = FALSE;
        }

        //Mandatory Parameter Missing
        if (($flag === TRUE) &&
                (($request['orderID'] == "") ||
                ($request['247aroundBookingID'] == "") ||
                ($request['cancellationReason'] == ""))
        ) {
            $resultArr['code'] = ERR_MANDATORY_PARAMETER_MISSING_CODE;
            $resultArr['msg'] = ERR_MANDATORY_PARAMETER_MISSING_MSG;

            $flag = FALSE;
        }

        //Order ID / Booking ID validation
        if ($flag === TRUE) {
            $lead = $this->partner_model->get_order_id_for_partner($this->partner['id'], $request['orderID']);
            if (!is_null($lead)) {
                $resultArr['lead'] = $lead;

                //order id found, check booking id
                if ($lead['booking_id'] != $request['247aroundBookingID']) {
                    log_message('info', "Lead details: " . print_r($lead, true));

                    $resultArr['code'] = ERR_INVALID_BOOKING_ID_CODE;
                    $resultArr['msg'] = ERR_INVALID_BOOKING_ID_MSG;

                    $flag = FALSE;
                } else {
                    //Check request status
                    if ($lead['current_status'] == "Cancelled") {
                        log_message('info', "Lead details: " . print_r($lead, true));

                        $resultArr['code'] = ERR_REQUEST_ALREADY_CANCELLED_CODE;
                        $resultArr['msg'] = ERR_REQUEST_ALREADY_CANCELLED_MSG;

                        $flag = FALSE;
                    }

                    if (($flag === TRUE) &&
                            ($lead['current_status'] == "Completed")) {
                        log_message('info', "Lead details: " . print_r($lead, true));

                        $resultArr['code'] = ERR_REQUEST_ALREADY_COMPLETED_CODE;
                        $resultArr['msg'] = ERR_REQUEST_ALREADY_COMPLETED_MSG;

                        $flag = FALSE;
                    }

                    //Check for cut-off time
                    //if cancel request time is 2 hrs prior to cutoff time, then ok
                    //else not ok
                    //todo:
                    //Check cancellation reason
                    //todo:
                }
            } else {
                //Order id not found
                $resultArr['code'] = ERR_ORDER_ID_NOT_FOUND_CODE;
                $resultArr['msg'] = ERR_ORDER_ID_NOT_FOUND_MSG;

                $flag = FALSE;
            }
        }

        //Check request status
        //If code is still empty, it means data is valid.
        if ($resultArr['code'] == "") {
            $resultArr['result'] = TRUE;
        }

        return $resultArr;
    }

    //Validate update timeslot API data
    function validate_update_timeslot_data($request) {
        log_message('info', "Entering: " . __METHOD__ . ", Request data: " . print_r($request, true));

        //Lead will store the booking entry if it exists
        $resultArr = array("result" => FALSE, "lead" => NULL, "code" => NULL, "msg" => NULL);
        $flag = TRUE;

        //Validate Partner Name
        if ($request['partnerName'] != $this->partner['public_name']) {
            $resultArr['code'] = ERR_INVALID_PARTNER_NAME_CODE;
            $resultArr['msg'] = ERR_INVALID_PARTNER_NAME_MSG;

            $flag = FALSE;
        }

        //Mandatory Parameter Missing
        if (($flag === TRUE) &&
                (($request['orderID'] == "") ||
                ($request['247aroundBookingID'] == "") ||
                ($request['installationTimeslotStart'] == "") ||
                ($request['installationTimeslotEnd'] == ""))
        ) {
            $resultArr['code'] = ERR_MANDATORY_PARAMETER_MISSING_CODE;
            $resultArr['msg'] = ERR_MANDATORY_PARAMETER_MISSING_MSG;

            $flag = FALSE;
        }

        //Order ID / Booking ID validation
        if ($flag === TRUE) {
            $lead = $this->partner_model->get_order_id_for_partner($this->partner['id'], $request['orderID']);
            if (!is_null($lead)) {
                $resultArr['lead'] = $lead;

                //order id found, check booking id
                if ($lead['booking_id'] != $request['247aroundBookingID']) {
                    log_message('info', "Lead details: " . print_r($lead, true));

                    $resultArr['code'] = ERR_INVALID_BOOKING_ID_CODE;
                    $resultArr['msg'] = ERR_INVALID_BOOKING_ID_MSG;

                    $flag = FALSE;
                } else {
                    //Check request status
                    if ($lead['current_status'] == "Cancelled") {
                        log_message('info', "Lead details: " . print_r($lead, true));

                        $resultArr['code'] = ERR_REQUEST_ALREADY_CANCELLED_CODE;
                        $resultArr['msg'] = ERR_REQUEST_ALREADY_CANCELLED_MSG;

                        $flag = FALSE;
                    }

                    if (($flag === TRUE) &&
                            ($lead['current_status'] == "Completed")) {
                        log_message('info', "Lead details: " . print_r($lead, true));

                        $resultArr['code'] = ERR_REQUEST_ALREADY_COMPLETED_CODE;
                        $resultArr['msg'] = ERR_REQUEST_ALREADY_COMPLETED_MSG;

                        $flag = FALSE;
                    }

                    //Check timeslot format validity
                    if (
                            ($flag === TRUE) &&
                            (($this->validate_timeslot_format($request['installationTimeslotStart']) === FALSE) ||
                            ($this->validate_timeslot_format($request['installationTimeslotEnd']) === FALSE))
                    ) {
                        $resultArr['code'] = ERR_INVALID_TIMESLOT_FORMAT_CODE;
                        $resultArr['msg'] = ERR_INVALID_TIMESLOT_FORMAT_MSG;

                        $flag = FALSE;
                    }

                    //Check timeslot is valid or not
                    if ($flag === TRUE) {
                        $tsStart = $request['installationTimeslotStart'];
                        $tsEnd = $request['installationTimeslotEnd'];

                        //Check yr, month and day - all should be same
                        //Timeslot minutes == 00
                        if (($tsStart['year'] != $tsEnd['year']) ||
                                ($tsStart['month'] != $tsEnd['month']) ||
                                ($tsStart['day'] != $tsEnd['day']) ||
                                ($tsStart['minute'] != "00") ||
                                ($tsEnd['minute'] != "00")
                        ) {
                            $resultArr['code'] = ERR_INVALID_INSTALLATION_TIMESLOT_CODE;
                            $resultArr['msg'] = ERR_INVALID_INSTALLATION_TIMESLOT_MSG;

                            $flag = FALSE;
                        }

                        /*
                         * Valid timeslots: 10-13, 13-16, 16-19
                         * SD asked to remove this limitation as they have different timeslots than us
                         * We are free to return our timeslot
                         *
                          if (!
                          (($tsStart['hour'] == '10' && $tsEnd['hour'] == '13') ||
                          ($tsStart['hour'] == '13' && $tsEnd['hour'] == '16') ||
                          ($tsStart['hour'] == '16' && $tsEnd['hour'] == '19'))
                          ) {
                          $resultArr['code'] = ERR_INVALID_INSTALLATION_TIMESLOT_CODE;
                          $resultArr['msg'] = ERR_INVALID_INSTALLATION_TIMESLOT_MSG;

                          $flag = FALSE;
                          }
                         *
                         */
                    }
                }
            } else {
                //Order id not found
                $resultArr['code'] = ERR_ORDER_ID_NOT_FOUND_CODE;
                $resultArr['msg'] = ERR_ORDER_ID_NOT_FOUND_MSG;

                $flag = FALSE;
            }
        }

        //Check request status
        //If code is still empty, it means data is valid.
        if ($resultArr['code'] == "") {
            $resultArr['result'] = TRUE;
        }

        return $resultArr;
    }

    //Cancels an order, returns true/false if cancellation is success/failure
    function process_cancel_request($request) {
        log_message('info', "Entering: " . __METHOD__ . ", Request data: " . print_r($request, true));

        $booking_id = $request['247aroundBookingID'];
        $booking['current_status'] = $details['current_status'] = $details['internal_status'] = $unit_details['booking_status'] = "Cancelled";
        $booking['internal_status'] = "Cancelled";
        $booking['cancellation_reason'] = $details['cancellation_reason'] = "Other : " . $request['cancellationReason'];
        $booking['update_date'] = $booking['closed_date'] = date("Y-m-d H:i:s");

        //check partner status from partner_booking_status_mapping table  
        $partner_id = $this->partner['id'];
        $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'], $partner_id, $booking_id);
        $booking['actor'] = $booking['next_action'] = 'not_define';
        if (!empty($partner_status)) {
            $booking['partner_current_status'] = $partner_status[0];
            $booking['partner_internal_status'] = $partner_status[1];
            $booking['actor'] = $partner_status[2];
            $booking['next_action'] = $partner_status[3];
        }
        $this->booking_model->update_booking($booking_id, $booking);
        $this->booking_model->update_booking_unit_details($booking_id, $unit_details);
        // Save Data in booking History Table
        $this->notify->insert_state_change($booking_id,$booking['current_status'] , _247AROUND_PENDING, CANCELLED_BY_PARTNER_API, _247AROUND_DEFAULT_AGENT, _247AROUND_DEFAULT_AGENT_NAME,$booking['actor'],$booking['next_action'], _247AROUND);
        $this->service_centers_model->update_service_centers_action_table($booking_id, $details);
        return TRUE;
    }

    /*
     * Return true/false & final timeslot if rescheduling is success/failure
     * Valid timeslots from SD - 9-12, 12-3, 3-6. We remap these timeslots to our
     * timeslots currently and return them.
     */

    function process_schedule_request($request) {
        log_message('info', "Entering: " . __METHOD__ . ", Request data: " . print_r($request, true));

        $booking_id = $request['247aroundBookingID'];

        $tsStart = $request['installationTimeslotStart'];
        $tsEnd = $request['installationTimeslotEnd'];

        //Make hour=0 since we don't store time info in sch_date
        $tsEnd['hour'] = "00";
        //Get DateTime
        $sch_date = $this->getDateTime($tsEnd);

        $booking['booking_date'] = date('d-m-Y', strtotime($sch_date));

        switch ($tsStart['hour']) {
            //9-12 PM
            case "9":
                $booking['booking_timeslot'] = "10AM-1PM";
                break;

            //12-3 PM
            case "12":
                $booking['booking_timeslot'] = "1PM-4PM";
                break;

            //3-6 PM
            case "15":
                $booking['booking_timeslot'] = "4PM-7PM";
                break;

            default:
                //Use this in case of error
                $booking['booking_timeslot'] = "1PM-4PM";
                break;
        }

        $sch_time = $booking['booking_timeslot'];
        $booking['query_remarks'] = (isset($request['remarks']) ? $request['remarks'] : "");
        $booking['update_date'] = date("Y-m-d H:i:s");

        $this->booking_model->update_booking($booking_id, $booking);

        //Return 247around time slot
        $resultArr['timeslotStart'] = json_encode($request['installationTimeslotStart'], JSON_UNESCAPED_SLASHES);
        $resultArr['timeslotEnd'] = json_encode($request['installationTimeslotEnd'], JSON_UNESCAPED_SLASHES);
        $resultArr['result'] = TRUE;

        return $resultArr;
    }

    function validate_timeslot_format($timeslot) {
        log_message('info', "Entering: " . __METHOD__ . ", Timeslot: ");
        //json_decode($timeslot);

        if (!(json_last_error() === JSON_ERROR_NONE)) {
            return FALSE;
        } else {
            if (
                    (array_key_exists('year', $timeslot)) &&
                    (array_key_exists('month', $timeslot)) &&
                    (array_key_exists('day', $timeslot)) &&
                    (array_key_exists('hour', $timeslot)) &&
                    (array_key_exists('minute', $timeslot))) {
                if (
                        ($timeslot['year'] === "") ||
                        ($timeslot['month'] === "") ||
                        ($timeslot['day'] === "") ||
                        ($timeslot['hour'] === "") ||
                        ($timeslot['minute'] === "")
                ) {
                    //something is missing
                    return FALSE;
                } else {
                    return TRUE;
                }
            } else {
                return FALSE;
            }
        }
    }

    /**
     * Send final JSON response to Partner
     *
     * @access	private
     * @return	Echos the output
     */
    function sendJsonResponse($code) {
        $this->jsonResponseString['code'] = $code[0];
        $this->jsonResponseString['result'] = $code[1];

        $responseData = array("data" => $this->jsonResponseString);

        //$activity = array('activity' => 'sending response', 'data' => json_encode($responseData), 'time' => $this->microtime_float());
        //$this->apis->log_partner_activity($activity);
        $activity = array(
            'partner_id' => $this->partner['id'],
            'activity' => $this->requestUrl,
            'header' => $this->header,
            'json_request_data' => $this->jsonRequestData,
            'json_response_string' => json_encode($responseData));
        $this->partner_model->log_partner_activity($activity);

        header('Content-Type: application/json');
        //header('Content-type:application/json;charset=utf-8');
        $response = json_encode($responseData, JSON_UNESCAPED_SLASHES);

        echo $response;
    }
    /**
     * @desc This is only used to send api response for Paytm
     * @param Array $code
     */
    function sendPaytmJsonResponse($code) {
        $this->jsonResponseString['code'] = $code[0];
        $this->jsonResponseString['result'] = $code[1];

        $responseData =  $this->jsonResponseString;

        //$activity = array('activity' => 'sending response', 'data' => json_encode($responseData), 'time' => $this->microtime_float());
        //$this->apis->log_partner_activity($activity);
        $activity = array(
            'partner_id' => $this->partner['id'],
            'activity' => $this->requestUrl,
            'header' => $this->header,
            'json_request_data' => $this->jsonRequestData,
            'json_response_string' => json_encode($responseData));
        $this->partner_model->log_partner_activity($activity);

        header('Content-Type: application/json');
        //header('Content-type:application/json;charset=utf-8');
        $response = json_encode($responseData, JSON_UNESCAPED_SLASHES);

        echo $response;
    }

    function getallheaders() {
        //Use this if you are using Nginx

        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;

        //It works only with Apache
//        return getallheaders();
    }

    function getDateTime($dt) {
        $date = $dt['year'] . "-" . $dt['month'] . "-" . $dt['day'];
        $time = $dt['hour'] . ":" . $dt['minute'] . ":00";

        //TODO: check for error
        try {
            $new_dt = date_create($date . " " . $time);
        } catch (Exception $e) {
            log_message('info', $e->getMessage());

            $new_dt = date_create("now");
        }

        if ($new_dt !== FALSE)
            return $new_dt->format("Y-m-d H:i:s");
    }

    /**
     * Insert new order
     *
     * API to insert new order in the CRM
     *
     * @access  public
     * @return  Success / Error code as per the document
     */
    public function insertBookingByPartner() {
        log_message('info', "Entering: " . __METHOD__);
        $this->requestUrl = __METHOD__;

        $auth = $this->checkAuthentication();

        if ($auth) {
            log_message('info', __METHOD__ . ":: Token validated (Partner ID: " . $this->partner['id'] . ")");

            //Token validated
            $input_d = file_get_contents('php://input');
            $requestData = json_decode($input_d, TRUE);

            if (!(json_last_error() === JSON_ERROR_NONE)) {
                log_message('info', __METHOD__ . ":: Invalid JSON");

                //Invalid json
                $this->jsonRequestData = $input_d;
                $this->sendJsonResponse(array(ERR_INVALID_JSON_INPUT_CODE, ERR_INVALID_JSON_INPUT_MSG));
            } else {
                $this->jsonRequestData = $input_d;
                //Check whether the required details are present in the request
                //And request doesn't exist in database
                $is_valid = $this->validate_request_data($requestData);
                if ($is_valid['result'] == TRUE) {

                    log_message('info', __METHOD__ . ":: Request validated");

                    //Search for user
                    //Insert user if phone number doesn't exist
                    $output = $this->user_model->get_users_by_any(array("users.phone_number" => $requestData['mobile']));
                    $distict_details = $this->vendor_model->get_distict_details_from_india_pincode(trim($requestData['pincode']));

                    $user['name'] = $requestData['name'];
                    $user['phone_number'] = $requestData['mobile'];
                    $user['alternate_phone_number'] = (isset($requestData['alternate_phone_number']) ? $requestData['alternate_phone_number'] : "");
                    $user['user_email'] = (isset($requestData['email']) ? $requestData['email'] : "");
                    if (isset($requestData['landmark']) && (!empty($requestData['landmark']))) {
                        $user['home_address'] = $requestData['address'] . ", " . $requestData['landmark'];
                    } else {
                        $user['home_address'] = $requestData['address'];
                    }

                    $user['pincode'] = $requestData['pincode'];
                    $user['city'] = $requestData['city'];

                    $user['state'] = $distict_details['state'];

                    if (empty($output)) {
                        log_message('info', $requestData['mobile'] . ' does not exist');

                        //User doesn't exist
                        $user_id = $this->user_model->add_user($user);

                        //echo print_r($user, true), EOL;
                        //Add sample appliances for this user
                        $count = $this->booking_model->getApplianceCountByUser($user_id);

                        //Add sample appliances if user has < 5 appliances in wallet
                        if ($count < 5) {
                            $this->booking_model->addSampleAppliances($user_id, 5 - intval($count));
                        }
                    } else {
                        log_message('info', $requestData['mobile'] . ' exists');
                        //User exists
                        $user_id = $output[0]['user_id'];
                        $user['user_id'] = $user_id;
                        $this->user_model->edit_user($user);
                    }

                    $booking['partner_id'] = $unit_details['partner_id'] = $this->partner['id'];
                    $booking['order_id'] = $requestData['orderID'];
                    $unit_details['appliance_brand'] = $appliance_details['brand'] = $requestData['brand'];
                    $appliance_details['model_number'] = $unit_details['model_number'] = (!empty($requestData['model']) ? $requestData['model'] : "");

                    $booking['service_id'] = $requestData['service_id'];

                    //Product description
                    $unit_details['appliance_description'] = $appliance_details['description'] = $requestData['productType'];
                    //Check for all optional parameters before setting them
                    $unit_details['appliance_category'] = $appliance_details['category'] = (isset($requestData['category']) ? $requestData['category'] : "");


                    $unit_details['appliance_capacity'] = $appliance_details['capacity'] = (isset($requestData['capacity']) ? $requestData['capacity'] : "");

                    $booking['booking_primary_contact_no'] = $requestData['mobile'];
                    $booking['booking_alternate_contact_no'] = $lead_details['booking_alternate_contact_no'] = (isset($requestData['alternate_phone_number']) ? $requestData['alternate_phone_number'] : "");
                    $booking['booking_landmark'] = $requestData['landmark'];
                    $booking['booking_pincode'] = trim($requestData['pincode']);
                    $booking['city'] = $requestData['city'];
                    //$booking['booking_request_symptom'] = $requestData['booking_request_symptom'];
                    if(isset($requestData['parent_booking'])){
                        $booking['parent_booking'] = $requestData['parent_booking'];
                    }
                    $booking['repeat_reason'] = NULL;
                    if(isset($requestData['repeat_reason'])){
                        $booking['repeat_reason'] = $requestData['repeat_reason'];
                    }
                    $agent_id = $requestData['agent_id'];

                    //Add this as a Booking now
                    $booking['booking_id'] = '';
                    $booking['user_id'] = $user_id;
                    $appliance_details['user_id'] = $booking['user_id'];

                    $unit_details['service_id'] = $appliance_details['service_id'] = $booking['service_id'];
                    log_message('info', __METHOD__ . ":: Service ID: " . $booking['service_id']);
                    //echo "Service ID: " . $booking['service_id'] . PHP_EOL;

                    $yy = date("y");
                    $mm = date("m");
                    $dd = date("d");
                    $booking['booking_id'] = str_pad($booking['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
                    $booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($booking['user_id'])) + 1);

                    //Add partner code from sources table
                    //All partners should have a valid partner code in the bookings_sources table

                    $booking['source'] = $requestData['partner_code'];
                    if ($requestData['product_type'] == "Delivered") {
                        $booking['booking_id'] = $booking['source'] . "-" . $booking['booking_id'];
                    } else {
                        $booking['booking_id'] = "Q-" . $booking['source'] . "-" . $booking['booking_id'];
                    }

                    $unit_details['booking_id'] = $booking['booking_id'];
                    $p_date = date("Y-m-d", strtotime($requestData['purchase_date']));
                    $appliance_details['purchase_date'] = $unit_details['purchase_date'] = $p_date;

                    $booking['quantity'] = $requestData['appliance_unit'];
                    $appliance_details['serial_number'] = $unit_details['partner_serial_number'] = $requestData['serial_number'];

                    //$booking['potential_value'] = '';
                    $appliance_details['last_service_date'] = date('d-m-Y');

                    $booking['booking_date'] = $requestData['booking_date'];
                    $booking['initial_booking_date'] = $requestData['booking_date'];
                    $booking['booking_timeslot'] = '';
                    $booking['booking_address'] = $user['home_address'];

                    $booking['booking_remarks'] = $requestData['remarks'];
                    $booking['query_remarks'] = "";
                    $booking['partner_source'] = $requestData['partner_source'];
                    $booking['booking_timeslot'] = "4PM-7PM";
                    $booking['state'] = $distict_details['state'];
                    $booking['district'] = $distict_details['district'];
                    $booking['taluk'] = $distict_details['taluk'];
                    $booking['amount_due'] = $requestData['amount_due'];
                    $upcountry_data = json_decode($requestData['upcountry_data'], TRUE);
                    $booking['is_upcountry'] = 0;
                    $booking['create_date'] = date("Y-m-d H:i:s");
                    
                    $booking_symptom['booking_id'] = $booking['booking_id'];
                    $booking_symptom['symptom_id_booking_creation_time'] = $requestData['booking_request_symptom'];;
                    $booking_symptom['create_date'] = date("Y-m-d H:i:s");
                    
                    if ($requestData['product_type'] == "Shipped") {
                        $booking['current_status'] = _247AROUND_FOLLOWUP;
                        $booking['internal_status'] = _247AROUND_FOLLOWUP;
                        $booking['type'] = "Query";

                        if (isset($upcountry_data['is_upcountry'])) {
                            if (($upcountry_data['is_upcountry'] == 1)) {
                                $booking['is_upcountry'] = 1;
                            }
                        }
                    } else {
                        $booking['current_status'] = _247AROUND_PENDING;
                        $booking['internal_status'] = _247AROUND__SCHEDULED;
                        $booking['type'] = "Booking";

                        if (isset($upcountry_data['is_upcountry'])) {
                            if (($upcountry_data['is_upcountry'] == 1)) {
                                $booking['is_upcountry'] = 1;
                            }
                        }
                    }

                    /* check dealer exist or not in the database
                     * if dealer does not exist into the database then
                     * insert dealer details in dealer_details table and dealer_brand_mapping table 
                     */

                    if (isset($requestData['dealer_phone_number']) && !empty($requestData['dealer_phone_number'])) {
                        $requestData['state'] = $distict_details['state'];
                        $is_dealer_id = $this->miscelleneous->dealer_process($requestData, $this->partner['id']);
                        if (!empty($is_dealer_id)) {
                            $booking['dealer_id'] = $is_dealer_id;
                        }
                    }
                    //check partner status from partner_booking_status_mapping table  
                    $actor = $next_action = 'NULL';
                    $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'], $booking['partner_id'], $booking['booking_id']);
                    if (!empty($partner_status)) {
                        $booking['partner_current_status'] = $partner_status[0];
                        $booking['partner_internal_status'] = $partner_status[1];
                        $actor = $booking['actor'] = $partner_status[2];
                        $next_action = $booking['next_action'] = $partner_status[3];
                    }
                    $return_id = $this->booking_model->addbooking($booking);
                    $symptomStatus = $this->booking_model->addBookingSymptom($booking_symptom);
                    if (!empty($return_id)) {
                        //Send Push Notification to Partner
//                        if($booking['partner_id'] !=''){
//                            $receiverArrayPartner['partner'] = array($booking['partner_id']); 
//                            $notificationTextArrayPartner['msg'] = array($booking['booking_id']);
//                            $this->push_notification_lib->create_and_send_push_notiifcation(NEW_BOOKING_FOR_PARTNER,$receiverArrayPartner,$notificationTextArrayPartner);
//                        }
                        //End Push Notification
                        $customer_net_payable = 0;
                        $price_tag = array();
                        for ($i = 0; $i < $requestData['appliance_unit']; $i++) {
                            $unit_details['appliance_id'] = $this->booking_model->addappliance($appliance_details);
                            foreach ($requestData['requestType'] as $key => $sc) {
                                //$sc has service_centre_charges_id + customer_total + partner_offer separated by '_'
                                $explode = explode("_", $sc);

                                $unit_details['id'] = $explode[0];
                                $unit_details['around_paid_basic_charges'] = $unit_details['around_net_payable'] = "0.00";
                                $unit_details['partner_paid_basic_charges'] = $explode[2];
                                $unit_details['partner_net_payable'] = $explode[2];
                                $unit_details['booking_status'] = "Pending";

                                //find customer net payable by subtracting partner offer
                                $customer_net_payable += ($explode[1] - $explode[2]);
                                $res = $this->booking_model->insert_data_in_booking_unit_details($unit_details, $booking['state'], $key);
                                array_push($price_tag, $res['price_tags']);
                            }
                        }
                        
                        
                        $this->booking_model->update_request_type($booking['booking_id'], $price_tag);
                        $is_price['customer_net_payable'] = $customer_net_payable;
                        $is_price['is_upcountry'] = $booking['is_upcountry'];
                        
                        $url1 = base_url() . "employee/do_background_process/send_sms_email_for_booking";
                        $send1['booking_id'] = $booking['booking_id'];
                        $send1['state'] = "SendWhatsAppNo";
                        $this->asynchronous_lib->do_background_process($url1, $send1);

                        if ($requestData['product_type'] == "Shipped") {
                            $this->initialized_variable->fetch_partner_data($this->partner['id']);

                            //check upcountry details and send sms to customer as well
                            if(($booking['type'] == _247AROUND_QUERY) && $booking['partner_id'] == VIDEOCON_ID){ }
                            else{
                                
                                $booking_details_data = $this->booking_model->get_booking_details("request_type", array("booking_id" => $booking['booking_id']));
                                $booking['request_type'] = $booking_details_data[0]['request_type'];
                                $this->miscelleneous->check_upcountry($booking, $requestData['appliance_name'], $is_price, "shipped");
                                unset($booking['request_type']);
                            }
                                
                            //insert in state change table
                            $this->notify->insert_state_change($booking['booking_id'], _247AROUND_FOLLOWUP, _247AROUND_NEW_QUERY, $booking['booking_remarks'], $agent_id, 
                                    $requestData['partnerName'],$actor,$next_action, $booking['partner_id']);
                        } else {
                            
                            $this->notify->insert_state_change($booking['booking_id'], _247AROUND_PENDING, _247AROUND_NEW_BOOKING, $booking['booking_remarks'], $agent_id, 
                                    $requestData['partnerName'],$actor,$next_action, $booking['partner_id']);

                            //Assigned vendor Id
                            if (isset($upcountry_data['message'])) {
                                switch ($upcountry_data['message']) {
                                    case UPCOUNTRY_BOOKING:
                                    case UPCOUNTRY_LIMIT_EXCEED:
                                    case NOT_UPCOUNTRY_BOOKING:
                                    case UPCOUNTRY_DISTANCE_CAN_NOT_CALCULATE:
                                        //assign vendor
                                        $assigned = $this->miscelleneous->assign_vendor_process($upcountry_data['vendor_id'], $booking['booking_id'], $booking['partner_id'],$agent_id, _247AROUND_PARTNER_STRING);

                                        if ($assigned) {
                                            $url = base_url() . "employee/do_background_process/assign_booking";
                                            $this->notify->insert_state_change($booking['booking_id'], ASSIGNED_VENDOR, _247AROUND_PENDING, "Auto Assign vendor", _247AROUND_DEFAULT_AGENT, 
                                                    _247AROUND_DEFAULT_AGENT_NAME, $actor,$next_action,_247AROUND);
                                            
                                            //check upcountry and send sms
                                            $async_data['booking_id'] = array($booking['booking_id'] => $upcountry_data['vendor_id']);
                                            $async_data['agent_id'] = _247AROUND_DEFAULT_AGENT;
                                            $this->asynchronous_lib->do_background_process($url, $async_data);
                                        }

                                        break;

                                    case SF_DOES_NOT_EXIST:
                                        //SF does not exist in vendor pincode mapping table OR if two or more vendors are found which
                                        //do not provide upcountry services
                                        if (isset($upcountry_data['vendor_not_found'])) {
//                                            $to = RM_EMAIL . ", " . SF_NOT_EXISTING_IN_PINCODE_MAPPING_FILE_TO;
//                                            $cc = SF_NOT_EXISTING_IN_PINCODE_MAPPING_FILE_CC;
//
//                                            $subject = "SF Does Not Exist In Pincode: " . $booking['booking_pincode'];
//                                            $message = "Booking ID " . $booking['booking_id'] . " Booking City: " . $booking['city'] . " <br/>  Booking Pincode: " . $booking['booking_pincode'];
//                                            $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $message, "");

//                                            $this->vendor_model->insert_booking_details_sf_not_exist(array(
//                                                "booking_id" => $booking['booking_id'],
//                                                "city" => $booking['city'],
//                                                "pincode" => $booking['booking_pincode'],
//                                                "service_id" => $booking['service_id'],
//                                                "partner_id" => $booking['partner_id']
//                                            ));
                                            $this->miscelleneous->sf_not_exist_for_pincode(array(
                                                    "booking_id" => $booking['booking_id'],
                                                    "booking_pincode" => $booking['booking_pincode'],
                                                    "service_id" => $booking['service_id'],
                                                    "partner_id" => $booking['partner_id'],
                                                    "city" => $booking['city'],
                                                    "order_id" => $booking['order_id']
                                                ));
                                        }
                                        break;
                                }
                            }
                        }

                        //if state is not found
//                        if (empty($booking['state'])) {
//                            $to = NITS_ANUJ_EMAIL_ID;
//                            $message = "Pincode " . $booking['booking_pincode'] . " not found for Booking ID: " . $booking['booking_id'];
//                            $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", 'Pincode Not Found', $message, "");
//                        }
                        $this->partner_cb->partner_callback($booking['booking_id']);
                        //Send response
                        $this->jsonResponseString['response'] = array(
                            "orderID" => $booking['order_id'],
                            "247aroundBookingID" => $booking['booking_id'],
                            "247aroundBookingStatus" => $booking['current_status']);
                        $this->sendJsonResponse(array(SUCCESS_CODE, SUCCESS_MSG));
                    } else {
                        log_message('info', __FUNCTION__ . ' Error Partner booking details not inserted: ' . print_r($booking, true));
                        //Send response
                        $this->jsonResponseString['response'] = array(
                            "orderID" => $booking['order_id'],
                            "247aroundBookingID" => $booking['booking_id'],
                            "247aroundBookingStatus" => $booking['current_status']);
                        $this->sendJsonResponse(array(ERR_BOOKING_NOT_INSERTED, ERR_BOOKING_NOT_INSERTED_MSG));
                    }
                    
                    if(!$symptomStatus) {
                        log_message('info', __FUNCTION__ . ' Error Partner booking symptom details not inserted: ' . print_r($booking_symptom, true));
                        //Send response
                        $this->jsonResponseString['response'] = NULL;
                        $this->sendJsonResponse(array(ERR_BOOKING_NOT_INSERTED, ERR_BOOKING_NOT_INSERTED_MSG));
                    }
                } else {
                    log_message('info', __METHOD__ . ":: Request validation fails. " . print_r($is_valid, true));

                    //Request validation fails
                    //If it is because of pre-existing order id, return booking id as a part of response
                    if ($is_valid['code'] == ERR_ORDER_ID_EXISTS_CODE) {
                        log_message('info', "Reason: ERR_ORDER_ID_EXISTS_CODE");

                        $lead_details = $is_valid['lead'];
                        $this->jsonResponseString['response'] = array(
                            "247aroundBookingID" => $lead_details['booking_id'],
                            "247aroundBookingStatus" => $lead_details['current_status'],
                            "247aroundBookingRemarks" => $lead_details['booking_remarks']);

                        $this->sendJsonResponse(array($is_valid['code'], $is_valid['msg']));
                    } else {
                        $this->jsonResponseString['response'] = NULL;
                        $this->sendJsonResponse(array($is_valid['code'], $is_valid['msg']));
                    }
                }
            }
        }
    }

    //Validate new request data
    function validate_request_data($request) {
        log_message('info', "Entering: " . __METHOD__ . ", Request data: " . print_r($request, true));

        //Lead will store the booking entry if it exists
        $resultArr = array("result" => FALSE, "lead" => NULL, "code" => NULL, "msg" => NULL);
        $flag = TRUE;

        //Validate Partner id
        if ($request['partner_id'] != $this->partner['id']) {
            $resultArr['code'] = ERR_INVALID_PARTNER_NAME_CODE;
            $resultArr['msg'] = ERR_INVALID_PARTNER_NAME_MSG;

            $flag = FALSE;
        }

        //Mandatory Parameter Missing
        if (($flag === TRUE) &&
                (($request['service_id'] == "") ||
                ($request['brand'] == "") ||
                ($request['name'] == "") ||
                ($request['mobile'] == "") ||
                ($request['address'] == "") ||
                ($request['pincode'] == "") ||
                ($request['city'] == "") ||
                ($request['requestType'] == "")
                )) {
            $resultArr['code'] = ERR_MANDATORY_PARAMETER_MISSING_CODE;
            $resultArr['msg'] = ERR_MANDATORY_PARAMETER_MISSING_MSG;

            $flag = FALSE;
        }

        //Same Order ID can not be given for 2 bookings but same serial no can 
        //be used to insert multiple bookings
        if (!empty($request['orderID'])) {
            $lead = $this->partner_model->get_order_id_for_partner($this->partner['id'], $request['orderID']);
            if (!is_null($lead)) {
                log_message('info', "Lead details: " . print_r($lead, true));
                if($lead['current_status'] != _247AROUND_CANCELLED){
                    
                    //order id exists, return booking id
                    $resultArr['code'] = ERR_ORDER_ID_EXISTS_CODE;
                    $resultArr['msg'] = ERR_ORDER_ID_EXISTS_MSG;
                    $resultArr['lead'] = $lead;

                    $flag = FALSE;
                }
            }
        }

        //If code is still empty, it means data is valid.
        if ($resultArr['code'] == "") {
            $resultArr['result'] = TRUE;
        }

        return $resultArr;
    }

    /**
     * @desc: This function is used to validate the product type
     * @param array $data
     * @return array
     */
    function validate_product_type($data) {
        log_message('info', __FUNCTION__ . "=> Entering validation routine...");
        $invalid_data = array();
        $prod = trim($data['productType']);
        // get unproductive description array
        $unproductive_description = $this->unproductive_product();
        foreach ($unproductive_description as $un_description) {
            if (stristr($prod, $un_description)) {
                array_push($invalid_data, $prod);
            }
        }
        if (!empty($invalid_data)) {
            log_message('info', __FUNCTION__ . ' =>  Product description is not valid in JSON Request Data: ' .
                    print_r($invalid_data, true));

            $data['error'] = true;
            $data['invalid_product'] = $invalid_data;
            // $data['error']['invalid_title'][] =  "Product description is not valid in Excel data:";
            // Add Only user
            // $this->add_user_for_invalid($data);
        }
        log_message('info', __FUNCTION__ . "=> Exiting validation routine: Under Control");

        return $data;
    }

    /**
     * @desc: This is used to store key. If this key exists in the JSON Request then we will remove them.
     * @return array
     */
    function unproductive_product() {
        $unproductive_description = array(
            'Tds Meter',
            'Water Purifier Accessories',
            'Room Heater',
            'Immersion Rod',
            '(PNG /LPG) Geyser',
            'Gas Geyser',
            'Set of 2',
            'Drinking Water Pump',
            'Set of 24 pcs',
            'Casseroles',
            'Spun Filter Cartridge',
            'Oil Filled Radiator',
            'Immersion Water Heater Rod',
            '10" Filter Housing Transparent',
            'Blow Hot Element Heater',
            'Bajaj Fan Heater',
            'Gas Geyser',
            'Ro Body Cover',
            'Pack Of 24 Pcs',
            'Mineral Water Pot Offline Non Electric Water Purifer Filter',
            'Membrane Ro Water Purifier',
            '15 Filter',
            'Hevy Duty 5000 Cartridge',
            'Cleanwell Filter',
            'CSM MEMBRANE 80 GPD',
            'Spun Filter pack of ',
            'Zero B Filter',
            'Tower Heater',
            'Oil Filled Heater'
        );

        return $unproductive_description;
    }

    /**
     * @desc This is used to update Status of booking
     */
    function updateBookingRequest() {
        log_message('info', "Entering: " . __METHOD__);
        $this->requestUrl = __METHOD__;
        $auth = $this->checkAuthentication();
        if ($auth) {
            //Token validated
            $input_d = $this->get_requestedData();

            if ($input_d) {
                $this->jsonRequestData = $input_d;
                $requestData = json_decode($input_d);

                $is_valid = $this->validate_manditory_data($requestData);
                if ($is_valid) {
                    $this->updateBookingStatus($is_valid, $requestData);
                }
            }
        }
    }

    function updateBookingStatus($booking, $requestData) {
        $s_change = $this->booking_model->getbooking_state_change_by_any(array('booking_id' => $booking['booking_id'], "new_state" => PRODUCT_DELIVERED));

        if (!empty($s_change)) {

            $this->jsonResponseString['response'] = NULL;
            $this->sendJsonResponse(array(ERR_BOOKING_ALREADY_SCHEDULED_CODE, ERR_BOOKING_ALREADY_SCHEDULED_MSG));
        } else {
            if (isset($requestData->data->remarks)) {
                $data['query_remarks'] = $requestData->data->remarks;
            }
            $data['internal_status'] = PRODUCT_DELIVERED;
            $data['booking_date'] = '';
            $actor = $next_action = 'not_define';
            $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], PRODUCT_DELIVERED, $booking['partner_id'], $booking['partner_id']);
            if (!empty($partner_status)) {
                $data['partner_current_status'] = $partner_status[0];
                $data['partner_internal_status'] = $partner_status[1];
                $actor = $data['actor'] = $partner_status[2];
                $next_action = $data['next_action'] = $partner_status[3];
            }

            if ($booking['type'] == "Query") {

                $data['booking_date'] = '';
            } else if (!empty($booking['assigned_vendor_id'])) {

                $new_booking_date = date('d-m-Y');
                if (date('H') > 12) {
                    $new_booking_date = date('d-m-Y', strtotime("+1 days"));
                }

                $sf = $this->reusable_model->get_search_result_data("service_centres", "service_centres.non_working_days "
                        , array("service_centres.id" => $booking['assigned_vendor_id']), NULL, NULL, NULL, NULL, NULL, array());

                if (!empty($sf[0]['non_working_days'])) {

                    $non_workng_days = explode(",", $sf[0]['non_working_days']);

                    $slot = $this->getWorkingDays($non_workng_days, $new_booking_date);
                    $new_booking_date = date('d-m-Y', $slot[0]['Slot'][0]['StartTime']);
                }

                $data['booking_date'] = $new_booking_date;
            }

            $this->booking_model->update_booking($booking['booking_id'], $data);

            $p_login_details = $this->dealer_model->entity_login(array('entity_id' => $this->partner['id'], "user_id" => strtolower($this->partner['public_name'] . "-STS")));
            $this->notify->insert_state_change($booking['booking_id'], PRODUCT_DELIVERED, _247AROUND_PENDING, PRODUCT_DELIVERED, $p_login_details[0]['agent_id'], $this->partner['public_name'],$actor,$next_action, $this->partner['id']);

            $up_flag = 1;
            $url = base_url() . "employee/vendor/update_upcountry_and_unit_in_sc/" . $booking['booking_id'] . "/" . $up_flag;
            $async_data['booking'] = array();
            $this->asynchronous_lib->do_background_process($url, $async_data);

            if ($booking['type'] == "Query") {

                $data['booking_date'] = '';
            } else if (!empty($booking['assigned_vendor_id'])) {

                $new_booking_date = date('d-m-Y');
                if (date('H') > 12) {
                    $new_booking_date = date('d-m-Y', strtotime("+1 days"));
                }

                $sf = $this->reusable_model->get_search_result_data("service_centres", "service_centres.non_working_days "
                        , array("service_centres.id" => $booking['assigned_vendor_id']), NULL, NULL, NULL, NULL, NULL, array());

                if (!empty($sf[0]['non_working_days'])) {

                    $non_workng_days = explode(",", $sf[0]['non_working_days']);

                    $slot = $this->getWorkingDays($non_workng_days, $new_booking_date);
                    $new_booking_date = date('d-m-Y', $slot[0]['Slot'][0]['StartTime']);
                }

                $data['booking_date'] = $new_booking_date;
            }

            $this->booking_model->update_booking($booking['booking_id'], $data);
            $p_login_details = $this->dealer_model->entity_login(array('entity_id' => $this->partner['id'], "user_id" => strtolower($this->partner['public_name'] . "-STS")));
             $this->notify->insert_state_change($booking['booking_id'], _247AROUND_FOLLOWUP, PRODUCT_DELIVERED, $data['query_remarks'], $p_login_details[0]['agent_id'], 
                    $this->partner['public_name'],$actor,$next_action, $this->partner['id']);
            $up_flag = 1;
            $url = base_url() . "employee/vendor/update_upcountry_and_unit_in_sc/" . $booking['booking_id'] . "/" . $up_flag;
            $async_data['booking'] = array();
            $this->asynchronous_lib->do_background_process($url, $async_data);
            $this->jsonResponseString['response'] = NULL;
            $this->sendJsonResponse(array(SUCCESS_CODE, SUCCESS_UPDATED_MSG));
        }
    }

    private function checkAuthentication() {
        log_message('info', __FUNCTION__ . "=> Entering ");
        //Default values
        $this->jsonResponseString['response'] = NULL;
        $this->jsonResponseString['code'] = ERR_GENERIC_ERROR_CODE;
        $this->jsonResponseString['result'] = ERR_GENERIC_ERROR_MSG;

        //Save header / ip address in DB
        $h = $this->getallheaders();

        if ($h === FALSE) {
            $this->sendJsonResponse(array(ERR_GENERIC_ERROR_CODE, ERR_GENERIC_ERROR_MSG));
        } else {
            $this->header = json_encode($h);
            $this->token = $h['Authorization'];

            //Validate token
            $this->partner = $this->partner_model->validate_partner($this->token);
            if ($this->partner) {
                return true;
            } else {
                log_message('info', __METHOD__ . ":: Invalid token: " . $this->token);

                //invalid token
                $this->jsonResponseString['response'] = NULL;
                $this->sendJsonResponse(array(ERR_INVALID_AUTH_TOKEN_CODE, ERR_INVALID_AUTH_TOKEN_MSG));
            }
        }
    }

    private function get_requestedData() {
        log_message('info', __FUNCTION__ . "=> Entering ");
        $input_d = file_get_contents('php://input');
        if (!(json_last_error() === JSON_ERROR_NONE)) {
            log_message('info', __METHOD__ . ":: Invalid JSON");

            //Invalid json
            $this->jsonRequestData = $input_d;
            $this->sendJsonResponse(array(ERR_INVALID_JSON_INPUT_CODE, ERR_INVALID_JSON_INPUT_MSG));
            return false;
        } else {
            return $input_d;
        }
    }

    function validate_manditory_data($requestData) {
        log_message('info', __FUNCTION__ . "=> Entering ");
        //Check Parameter Exist or not
        if ((!isset($requestData->orderID)) || (!isset($requestData->updateType)) || ( (!isset($requestData->data)))) {
            $this->jsonResponseString['response'] = NULL;
            $this->sendJsonResponse(array(ERR_MANDATORY_PARAMETER_MISSING_CODE, ERR_MANDATORY_PARAMETER_MISSING_MSG));
            return false;
        }
        //Check updateType value is Status
        switch ($requestData->updateType) {
            case PRODUCT_DELIVERED:
                if (!isset($requestData->data->remarks)) {
                    $this->jsonResponseString['response'] = NULL;
                    $this->sendJsonResponse(array(ERR_MANDATORY_PARAMETER_MISSING_CODE, ERR_MANDATORY_PARAMETER_MISSING_MSG));
                    return false;
                } else if (empty($requestData->data->remarks)) {
                    $this->jsonResponseString['response'] = NULL;
                    $this->sendJsonResponse(array(ERR_STATUS_EMPTY_CODE, ERR_STATUS_EMPTY_MSG));
                    return false;
                }

                break;
            default :

                $this->jsonResponseString['response'] = NULL;
                $this->sendJsonResponse(array(ERR_UPDATE_TYPE_NOT_FOUND_CODE, ERR_UPDATE_TYPE_NOT_FOUND_MSG));
                return FALSE;
            //break;
        }
        //Check Order Id Is exist or not    
        if (!empty($requestData->orderID)) {
            $lead = $this->partner_model->get_order_id_for_partner($this->partner['id'], $requestData->orderID);
            if (empty($lead)) {

                $this->jsonResponseString['response'] = NULL;
                $this->sendJsonResponse(array(ERR_ORDER_ID_NOT_FOUND_CODE, ERR_ORDER_ID_NOT_FOUND_MSG));
                return FALSE;
            } else {
                //Order ID Exist
                return $lead;
            }
        } else {

            $this->jsonResponseString['response'] = NULL;
            $this->sendJsonResponse(array(ERR_ORDER_ID_NOT_FOUND_CODE, ERR_ORDER_ID_NOT_FOUND_MSG));
            return FALSE;
        }

        return False;
    }

    /**
     * @desc: This is used to get distnace between pincode
     */
    function getDistanceBetweenPincode() {
        $origins = $this->input->get("origin");
        $destinations = $this->input->get("destination");

        if (strlen($origins) != 6 && strlen($destinations) != 6) {
            $distance['status'] = "REQUEST_DENIED";
            print_r(json_encode($distance, true));
            exit();
        }

        if (is_numeric($origins) && is_numeric($destinations)) {
            
        } else {
            $distance['status'] = "REQUEST_DENIED";
            print_r(json_encode($distance, true));
            exit();
        }
        if (!empty($origins) && !empty($destinations)) {
            $is_distance = $this->upcountry_model->get_distance_between_pincodes($origins, $destinations);
            if (!empty($is_distance)) {
                $distance['destination_addresses'] = array($destinations, "India");
                $distance['origin_addresses'] = array($origins, "India");
                $distance['distance'] = array("text" => $is_distance[0]['distance'] . " KM", "value" => $is_distance[0]['distance']);
                $distance['status'] = "OK";
                echo $html = "<p style='float:right;'><img style='width:268px;' src='" . base_url() . "images/powered-by-google.png'></p><p id='data'>" . json_encode($distance, true) . "</p>";
            } else {
                $is_distance1 = $this->upcountry_model->calculate_distance_between_pincode($origins, "", $destinations, "");

                if ($is_distance1) {
                    $distance1 = (round($is_distance1['distance']['value'] / 1000, 2));
                    $distance['destination_addresses'] = array($destinations, "India");
                    $distance['origin_addresses'] = array($origins, "India");
                    $distance['distance'] = array("text" => $distance1 . " KM", "value" => $distance1);
                    $distance['status'] = "OK";

                    echo "<p style='float:right;'><img style='width:268px;' src='" . base_url() . "images/powered-by-google.png'></p><p id='data'>" . json_encode($distance, true) . "</p>";
                } else {
                    $distance['destination_addresses'] = array($destinations, "India");
                    $distance['origin_addresses'] = array($origins, "India");
                    $distance['status'] = "REQUEST_DENIED";

                    print_r(json_encode($distance, true));
                }
            }
        } else {
            $distance['status'] = "REQUEST_DENIED";
            print_r(json_encode($distance, true));
        }
    }
    
    function uploadpincodefile(){
        $this->load->view("upload_pincode");
    }
    
    function processUploadPincode() {
        log_message("info", __METHOD__ . " Enterring.. Email " . $this->input->post("email_id"));
        $this->form_validation->set_rules('email_id', 'Email', 'trim|required|valid_email');
        if ($this->form_validation->run()) {
            
                if (!empty($_FILES['file']['name']) && $_FILES['file']['size'] > 0) {
                    $pathinfo = pathinfo($_FILES["file"]["name"]);
                    if ($pathinfo['extension'] == "xlsx" || $pathinfo['extension'] == "xls") {
                        if (move_uploaded_file($_FILES['file']['tmp_name'], TMP_FOLDER . $_FILES["file"]["name"])) { //check if it the file move successfully.
                            $send_data['email_id'] = $this->input->post("email_id");
                           // $_FILES['file']['tmp_name'] = TMP_FOLDER . $_FILES["file"]["name"];
                            $send_data['file_name'] = $_FILES["file"]["name"];
                           // $send_data['file'] = $_FILES;
                            $url = base_url() . "partner/process_file_upload";
                            $this->asynchronous_lib->do_background_process($url, $send_data);
                            echo "File uploaded successfully!!";
                        } else {
                            log_message("info", __METHOD__ . " Enterring.. File Upload Failed " . $this->input->post("email_id"));
                            echo "failed";
                        }
                    } else {
                        log_message("info", __METHOD__ . " Enterring.. Invalid File Format " . $this->input->post("email_id"));
                        echo "Invalid file format..";
                    }
                } else {
                    log_message("info", __METHOD__ . " Enterring.. File not Select" . $this->input->post("email_id"));
                    echo "Please select a file..!";
                }
            
        } else {
            log_message("info", __METHOD__ . " Enterring.. Invalid Email ID" . $this->input->post("email_id"));
            echo "Invalid email format..";
        }
    }

    function process_file_upload(){
        log_message("info",__METHOD__. " Enterring.. ".$this->input->post("email_id"));
        //$_FILES =  $this->input->post("file");
        $email_id = $this->input->post("email_id");
        //log_message("info",__METHOD__." Pincode Data ".print_r($_FILES, true));
        $array = array();
        $file_name = $this->input->post("file_name");
        $array['file']['tmp_name'] = TMP_FOLDER.$file_name;
        $array['file']['name'] = $file_name;
        $data = $this->miscelleneous->excel_to_Array_converter($array);
        
        if (isset($data[0]['ServiceCentrePincode']) && isset($data[0]['CustomerPincode'])) {
            $excelData = array();
            $excelPincodeData['ServiceCentrePincode'] = "Service Centre Pincode";
            $excelPincodeData['CustomerPincode'] = "Customer Pincode";
            $excelPincodeData['distance'] = "Distance";
            array_push($excelData, $excelPincodeData);
            log_message("info",__METHOD__." Enterring in Loop");
            foreach ($data as $value) {
                $excelPincodeData = array();
                $excelPincodeData['ServiceCentrePincode'] = $value['ServiceCentrePincode'];
                $excelPincodeData['CustomerPincode'] = $value['CustomerPincode'];

                if (strlen($value['ServiceCentrePincode']) == 6 && strlen($value['CustomerPincode']) == 6 && is_numeric($value['ServiceCentrePincode']) && is_numeric($value['CustomerPincode'])) {

                    $is_distance = $this->upcountry_model->get_distance_between_pincodes($value['ServiceCentrePincode'], $value['CustomerPincode']);
                    $distance = "";
                    if (!empty($is_distance)) {
                        $distance = $is_distance[0]['distance'];
                    } else {
                        $is_distance1 = $this->upcountry_model->calculate_distance_between_pincode($value['ServiceCentrePincode'], "", $value['CustomerPincode'], "");
                        $distance = (round($is_distance1['distance']['value'] / 1000, 2));
                    }

                    if (!empty($distance)) {
                        $excelPincodeData['distance'] = $distance;
                        //log_message("info",__METHOD__." Pincode Data ".print_r($distance, true));
                       // $distict_details = $this->vendor_model->get_distict_details_from_india_pincode(trim($value['CustomerPincode']));
                      //  $municipal = $this->upcountry_model->get_data_from_municipal_limit(array("district" => $distict_details["district"]), "*");
//                        if (!empty($municipal)) {
//                            $excelPincodeData["municipal_limit"] = $municipal[0]["municipal_limit"];
//                        } else {
//                            $excelPincodeData["municipal_limit"] = "";
//                        }
                    } else {
                      //  $excelPincodeData['status'] = "REQUEST_DENIED";
                      //  $excelPincodeData["municipal_limit"] = "";
                        $excelPincodeData['distance'] = "REQUEST_DENIED";
                        log_message("info",__METHOD__." Pincode Data REQUEST_DENIED");
                    }
                } else {
                  //  $excelPincodeData['status'] = "REQUEST_DENIED";
                 //   $excelPincodeData["municipal_limit"] = "";
                    $excelPincodeData['distance'] = "REQUEST_DENIED";
                    log_message("info",__METHOD__." Pincode Data REQUEST_DENIED");
                }

                array_push($excelData, $excelPincodeData);
            }
            log_message("info",__METHOD__." Exit from Loop");
            
            $output_file_excel = TMP_FOLDER . "distance_".date("YmdHis").".csv";
            $output = fopen($output_file_excel, "a+") or die("Unable to open file!");
           
            // create a file pointer connected to the output stream
            
            foreach ($excelData as $line) {
               // log_message('info', __FUNCTION__ . ' File line ' . print_r($line, true));
                fputcsv($output, $line);
            }
            fclose($output);
            $res1 = 0;
            log_message('info', __FUNCTION__ . ' File created ' . $output_file_excel);

            if (file_exists($output_file_excel)) {
                system(" chmod 777 " . $output_file_excel, $res1);
                $email_template = $this->booking_model->get_booking_email_template("distance_pincode_api");
                $subject = $email_template[4];
                $email_from = $email_template[2];
                $message = $email_template[0];
                $bcc = $email_template[5];
          
                $this->notify->sendEmail($email_from, $email_id, "", $bcc, $subject, $message, $output_file_excel,'distance_pincode_api');
                
                log_message("info",__METHOD__." Mail Sent.. to ".$email_id);
            } else {
                $message = "Please Try Again, File Genaeration failed!";
                $email_template = $this->booking_model->get_booking_email_template("distance_pincode_api");
                $subject = $email_template[4];
                $email_from = $email_template[2];
                $bcc = $email_template[5];
          
                $this->notify->sendEmail($email_from, $email_id, "", $bcc, $subject, $message, "",'distance_pincode_api');
                log_message("info",__METHOD__." Mail Not Sent.. to ");
            }
        } else {
            log_message("info",__METHOD__."Invalid File Format");
            $email_template = $this->booking_model->get_booking_email_template("distance_pincode_api");
            $subject = $email_template[4];
//            $message = $email_template[0];
            $email_from = $email_template[2];
            $bcc = $email_template[5];
           
            $message = "Invalid File Format";
            $this->notify->sendEmail($email_from, $email_id, "", $bcc, $subject, $message, "",'distance_pincode_api');
        }
    }
    
    function getTimeslotRequest(){
        $auth = $this->checkAuthentication();
        if($auth){
            log_message('info', __METHOD__ . ":: Token validated (Partner ID: " . $this->partner['id'] . ")");

            $input_d = $this->get_requestedData();
             if ($input_d) {
                $this->jsonRequestData = $input_d;
                $requestData = json_decode($input_d, TRUE);
                 $is_valid = $this->validateTimeSlotRequest($requestData);
                if ($is_valid['result'] == TRUE) {
                    
                        $this->jsonResponseString = array(
                            "success" => TRUE,
                            "msg" => "Slots have been sent successfully.",
                            "data" => array("SlotList"=> $is_valid['SlotList']));

                        $this->sendPaytmJsonResponse(array(SUCCESS_CODE, SUCCESS_MSG));
                } else {
                    $this->jsonResponseString['success'] = FALSE;
                    $this->sendPaytmJsonResponse(array($is_valid['code'], $is_valid['msg']));
                }
             }
        }
    }
    
    function validateTimeSlotRequest($request){
        log_message('info', "Entering: " . __METHOD__);

        //Lead will store the booking entry if it exists
        $resultArr = array("result" => FALSE, "code" => NULL, "msg" => NULL);
        $flag = TRUE;

        //Validate Partner Name
        if ($request['partnerName'] != $this->partner['public_name']) {
            $resultArr['code'] = ERR_INVALID_PARTNER_NAME_CODE;
            $resultArr['msg'] = ERR_INVALID_PARTNER_NAME_MSG;

            $flag = FALSE;
        }

        if (($flag === TRUE) && (!isset($request['247BookingID']))) {
            $resultArr['code'] = ERR_MANDATORY_PARAMETER_MISSING_CODE;
            $resultArr['msg'] = ERR_MANDATORY_PARAMETER_MISSING_MSG;

            $flag = FALSE;
        }
        
        if(isset($request['247BookingID'])){
            $join["service_centres"] = "service_centres.id = booking_details.assigned_vendor_id";
            $data = $this->reusable_model->get_search_result_data("booking_details","service_centres.non_working_days, "
                    . "booking_details.booking_date, booking_details.booking_timeslot",array("booking_details.booking_id"=>$request['247BookingID']),
                     $join,NULL,NULL,NULL,NULL,array());
            
            if(!empty($data)){
               // $workingDate = array();
                //$nextDay = date("Y-m-d", strtotime('+1 day', strtotime($data[0]['booking_date'])));
                $nextDay = date("Y-m-d", strtotime('+1 day'));
                if(!empty($data[0]['non_working_days'])){
                    $non_workng_days = explode(",", $data[0]['non_working_days']);
                    
                    $resultArr['SlotList'] = $this->getWorkingDays($non_workng_days, $nextDay);
                                    } else {
                    $resultArr['SlotList'] = $this->getWorkingDays(array(), $nextDay);
                } 
            } else{
                 // Not Assigned
                $resultArr['code'] = ERR_BOOKING_NOT_ASSIGNED_CODE;
                $resultArr['msg'] = ERR_BOOKING_NOT_ASSIGNED_MSG;
                $flag = FALSE;
               
            }
            
        }

        //If code is still empty, it means data is valid.
        if ($resultArr['code'] == "") {
            $resultArr['result'] = TRUE;
        }

        return $resultArr;
    }
    /*
     * @desc This is used to return next working days with timeslot in Array
     */
    function getWorkingDays($nonWorkingDays, $date, $workingDate = array()){
      
       for ($i = 0; $i< 3; $i++){
           if (!in_array(date("l", strtotime('+'.$i.' day', strtotime($date))), $nonWorkingDays)){
               $slot = array();
               
               $slot1 = strtotime(date("Y-m-d 10:00:00", strtotime('+'.$i.' day', strtotime($date))));
               $slot2 = strtotime(date("Y-m-d 13:00:00", strtotime('+'.$i.' day', strtotime($date))));
               $slot3 = strtotime(date("Y-m-d 16:00:00", strtotime('+'.$i.' day', strtotime($date))));
               $slot4 = strtotime(date("Y-m-d 19:00:00", strtotime('+'.$i.' day', strtotime($date))));
               
               array_push($slot, array("StartTimeDisplay" => "10 AM", "EndTimeDisplay" => "1 PM", "StartTime"=> $slot1, "EndTime" => $slot2, "IsActive" => FALSE));
               array_push($slot, array("StartTimeDisplay" => "1 PM", "EndTimeDisplay" => "4 PM", "StartTime"=> $slot2, "EndTime" => $slot3, "IsActive" => TRUE));
               array_push($slot, array("StartTimeDisplay" => "4 PM", "EndTimeDisplay" => "7 PM", "StartTime"=> $slot3, "EndTime" => $slot4, "IsActive" => TRUE));
               
               array_push($workingDate, array("Slot" =>$slot));
               
           } 
       }
        
       if(count($workingDate) < 2){
            return $this->getWorkingDays($nonWorkingDays, date("Y-m-d", strtotime('+'.$i.' day', strtotime($date))),$workingDate);
   
       } else {
            return $workingDate;
       }
    }
    function download_price_sheet(){
        $partnerID = $this->session->userdata('partner_id');
        $where['partner_id'] = $partnerID;
        $priceArray = $this->service_centre_charges_model->get_partner_price_data($where);
        $config = array('template' => "Price_Sheet.xlsx", 'templateDir' => __DIR__ . "/excel-templates/");
        if(ob_get_length() > 0) {
            ob_end_clean();
        }
        $R = new PHPReport($config);
        $R->load(array(array('id' => 'order', 'repeat' => true, 'data' => $priceArray),));
        $output_file_excel = TMP_FOLDER . $config['template'];
        $res1 = 0;
        if (file_exists($output_file_excel)) {
            system(" chmod 777 " . $output_file_excel, $res1);
            unlink($output_file_excel);
        }
        $R->render('excel', $output_file_excel);
        if (file_exists($output_file_excel)) {
            system(" chmod 777 " . $output_file_excel, $res1);
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            //header('Content-Disposition: attachment; filename='.$config['template'].'_'.$partnerID.'_'.date("Y-m-d")');
            header('Content-Disposition: attachment; filename="Price_Sheet"'.date("Y-m-d").".xlsx");
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($output_file_excel));
            readfile($output_file_excel);
            exec("rm -rf " . escapeshellarg($output_file_excel));
            exit;
        }
    }
    
    
    
//    function test() {
//        //Initialize constant
//        $appName = "24x7Around";
//        $secretKey = "Z1DBK3EH01ZUMPJU";
//        $salt = "QW8QQW4VVKQEQYXVRRY3TTKMTXRHNCNSOPSXFZFF9LI37ZZZXQUSDUN8EGFTRQKN";
//        $appConstant = "6VFKKLZ1Y4";
//        $url = "http://sandbox.servify.in:5009/api/v1/ServiceRequest/fulfillRequest";
//       
//        
//        //JSON with the Application Constant and the current unix timestamp in milliseconds
//        $app = json_encode(array("appC" => $appConstant, "tzU" => time() * 1000), true);
//        
//        //Using the SECRET_KEY and SALT, constructed a key using the PBKDF2 function
//        $key = hash_pbkdf2("sha256", $secretKey, $salt, 100000, 16);
//
//       // $encryptedMessage = $this->encrypt_e_openssl($app, $key); 
//        
//        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
//        $iv = openssl_random_pseudo_bytes($ivlen);
//        $ciphertext_raw = openssl_encrypt($app, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
//        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
//        $encryptedMessage = base64_encode( $iv.$hmac.$ciphertext_raw );
//        
//        $fromtime = strtotime(date("Y-m-d 10:00:00", strtotime('+1 day')));
//        $totime = strtotime(date("Y-m-d 13:00:00", strtotime('+1 day')));
//        $array = array(
//            "ReferenceID" => "SP-1656351803085551" , 
//            "Status" => "PNDNG_ASGN", 
//            "RequestDetails" => array( 
//                "Reason"=> "ENA",
//                 "Remarks"=> "Engineer not availble"
//                )
//            );
//        
//        $postData = json_encode($array, true);
//        
//       // $iv   = "@@@@&&&&####$$$$";
//       
//        //echo $postData. "<br/>";
//        
//        $ch = curl_init($url);
//        curl_setopt_array($ch, array(
//             CURLOPT_POST => TRUE,
//             CURLOPT_RETURNTRANSFER => TRUE,
//             CURLOPT_HTTPHEADER => array(
//                'Content-Type: application/json',
//                'app: ' . $appName,
//                'dr9se2q: ' . $encryptedMessage,
//                'co1cx2: ' . $iv
//            ),
//            CURLOPT_POSTFIELDS => $postData
//        ));
//
//        // Send the request
//        $response = curl_exec($ch);
//        print_r($response);
//    }
    
    function get_IV_Constant() {
        $key3 = "";
        $key1 = "";
        for($i = 0; $i < 8; $i++) {
            $key1 = intval(10000000 +  rand()* 89999999);
            $key3 += $key1;
        }
       
        return substr($key3, 0, 16);
    }
    
    function strToHex($string) {
        $hex = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $hex .= dechex(ord($string[$i]));
        }
        return $hex;
    }

    function Hex2String($hex) {

        $string = '';
        for ($i = 0; $i < strlen($hex) - 1; $i+=2) {
            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }
        return $string;
    }
    
    function encrypt_e($input, $ky) {
	$key = $ky;
	$size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'cbc');
	$input = pkcs5_pad_e($input, $size);
	$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', 'cbc', '');
	$iv = "@@@@&&&&####$$$$";
	mcrypt_generic_init($td, $key, $iv);
        
	$data = mcrypt_generic($td, $input);
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	$data = base64_encode($data);
	return $data;
}

    function Apitest(){
        $appName = "24x7Around";
        $secretKey = "Z1DBK3EH01ZUMPJU";
        $salt = "QW8QQW4VVKQEQYXVRRY3TTKMTXRHNCNSOPSXFZFF9LI37ZZZXQUSDUN8EGFTRQKN";
        $appConstant = "6VFKKLZ1Y4";
        $url = "http://sandbox.servify.in:5009/api/v1/ServiceRequest/fulfillRequest";
       
        //JSON with the Application Constant and the current unix timestamp in milliseconds
        $app = json_encode(array("appC" => $appConstant, "tzU" => time() * 1000), true);
        
        //Using the SECRET_KEY and SALT, constructed a key using the PBKDF2 function
       echo $hexsalt = $this->strToHex($salt); echo "<br/>";
        $key = hash_pbkdf2("SHA1", $secretKey, $hexsalt, 64, 8);

        $iv = $this->strToHex($this->get_IV_Constant());
       // echo $hex = $this->Hex2String($iv);
        //$encryptedMessage = base64_encode(openssl_encrypt($app, "AES-256-CBC", $key, OPENSSL_RAW_DATA, $iv));
        
//        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'cbc');
//	$input = pkcs5_pad_e($app, $size);
	$td = mcrypt_module_open(MCRYPT_RIJNDAEL_256, '', 'cbc', '');
	
	mcrypt_generic_init($td, $key, $iv);
        
	$data1 = mcrypt_generic($td, $app);
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
        
	$encryptedMessage = base64_encode($data1);
exit();
        $array = array(
            "ReferenceID" => "SP-1656351803085551" , 
            "Status" => "PNDNG_ASGN", 
            "RequestDetails" => array( 
                "Reason"=> "ENA",
                 "Remarks"=> "Engineer not availble"
                )
            );
        
        $postData = json_encode($array, true);
        $header = array(
                'Content-Type: application/json',
                'app: ' . $appName,
                'dr9se2q: ' . $encryptedMessage,
                'co1cx2: ' . $iv
            );
       
        echo json_encode($header);

        $ch = curl_init($url);
        curl_setopt_array($ch, array(
             CURLOPT_POST => TRUE,
             CURLOPT_RETURNTRANSFER => TRUE,
             CURLOPT_HTTPHEADER => $header,
            CURLOPT_POSTFIELDS => $postData
        ));

        $response = curl_exec($ch);
        $activity = array(
            'partner_id' => "3",
            'activity' => $url,
            'header' => json_encode($header),
            'json_request_data' => $postData,
            'json_response_string' => json_encode($response, JSON_UNESCAPED_SLASHES));

        $this->partner_model->log_partner_activity($activity);
        print_r($response);
    }
    
    function paytmApitest(){
        log_message('info', __METHOD__);
        $dr9se2q = $this->input->post("dr9se2q", true);
        $co1cx2 = $this->input->post("co1cx2", true);
        //$postData = $this->input->post('data');
        log_message("info", __METHOD__. " Data ". $dr9se2q. " co1cx2 ". $co1cx2);
        $array = array(
            "ReferenceID" => "SP-1656351803085551" , 
            "Status" => "PNDNG_ASGN", 
            "RequestDetails" => array( 
                "Reason"=> "ENA",
                 "Remarks"=> "Engineer not availble"
                )
            );
        
        $postData = json_encode($array, true);
        log_message("info", __METHOD__. " POST Data ". $postData);
        
        $appName = "24x7Around";
       
        $url = "http://sandbox.servify.in:5009/api/v1/ServiceRequest/fulfillRequest";
        
        $header = array(
                'Content-Type: application/json',
                'app: ' . $appName,
                'dr9se2q: ' . $dr9se2q,
                'co1cx2: ' . $co1cx2
            );

        $ch = curl_init($url);
        curl_setopt_array($ch, array(
             CURLOPT_POST => TRUE,
             CURLOPT_RETURNTRANSFER => TRUE,
             CURLOPT_HTTPHEADER => $header,
            CURLOPT_POSTFIELDS => $postData
        ));

        $response = curl_exec($ch);
        log_message('info', __METHOD__. " Response ". print_r($response, true));
        echo $response;
    }
                /*
     * This function show login li
     */
    function manage_partner_contacts(){
        $partner_id = $this->session->userdata('partner_id');
        $data['contact_persons'] =  $this->reusable_model->get_search_result_data("contact_person",  "contact_person.*,entity_role.role,entity_role.id as  role_id,entity_role.department,"
                . "GROUP_CONCAT(agent_filters.state) as  state,entity_login_table.agent_id as login_agent_id,entity_login_table.active as login_active",
                array("contact_person.entity_type" =>  "partner","contact_person.entity_id"=>$partner_id),
                array("entity_role"=>"contact_person.role = entity_role.id","agent_filters"=>"contact_person.id=agent_filters.contact_person_id","entity_login_table"=>"entity_login_table.contact_person_id = contact_person.id"), NULL, 
                array("name"=>'ASC'), NULL,  array("agent_filters"=>"left","entity_role"=>"left","entity_login_table"=>"left"),array("contact_person.id"));
         $data['department'] = $this->reusable_model->get_search_result_data("entity_role", 'DISTINCT department',array("entity_type" => 'partner'),NULL, NULL, array('department'=>'ASC'), NULL, NULL,array());  
         $data['select_state'] = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state) as state",NULL,NULL,NULL,array('state'=>'ASC'),NULL,NULL,array());
         $this->miscelleneous->load_partner_nav_header();
        $this->load->view('partner/contacts', $data);
        $this->load->view('partner/partner_footer');
    }
}
