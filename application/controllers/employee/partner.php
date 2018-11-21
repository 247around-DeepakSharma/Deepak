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
                                if (stristr($prod, "Television") || stristr($prod, "Monitor")) {
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

                                // if (empty($booking['state'])) {
                                //$to = NITS_ANUJ_EMAIL_ID;
                                //$message = "Pincode " . $booking['booking_pincode'] . " not found for Booking ID: " . $booking['booking_id'];
                                //$this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", 'Pincode Not Found', $message, "");
                                // }
                                //Send response
                                $this->jsonResponseString['response'] = array(
                                    "orderID" => $booking['order_id'],
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
        $this->notify->insert_state_change($booking_id,$booking['current_status'] , "Pending", "Cancelled By Partner API", _247AROUND_DEFAULT_AGENT, _247AROUND_DEFAULT_AGENT_NAME,$booking['actor'],$booking['next_action']);
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
                    if(isset($requestData['parent_booking'])){
                        $booking['parent_booking'] = $requestData['parent_booking'];
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
                        $booking['current_status'] = "Pending";
                        $booking['internal_status'] = "Scheduled";
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

                        if ($requestData['product_type'] == "Shipped") {
                            $this->initialized_variable->fetch_partner_data($this->partner['id']);

                            //check upcountry details and send sms to customer as well
                            $this->miscelleneous->check_upcountry($booking, $requestData['appliance_name'], $is_price, "shipped");

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
            $this->notify->insert_state_change($booking['booking_id'], PRODUCT_DELIVERED, _247AROUND_PENDING, PRODUCT_DELIVERED, $p_login_details[0]['agent_id'], $this->partner['public_name'], $this->partner['id']);

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

    function create_and_save_partner_report($partnerID){
            log_message('info', __FUNCTION__ . "Function Start For ".print_r($this->input->post(),true)." Partner ID : ".$partnerID);
            $postArray = $this->input->post();
            //Create Summary Report
            $newCSVFileName = $this->create_custom_summary_report($partnerID,$postArray);
             //Save File on AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "summary-excels/" . $newCSVFileName;
            $is_upload = $this->s3->putObjectFile(realpath(TMP_FOLDER . $newCSVFileName), $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            unlink(TMP_FOLDER . $newCSVFileName);
            if($is_upload == 1){
                //Save File log in report log table
                $data['entity_type'] = "Partner";
                $data['entity_id'] = $partnerID;
                $data['report_type'] = "partner_custom_summary_report";
                $data['filters'] = json_encode($postArray);
                $data['url'] =$directory_xls;
                $data['agent_id'] =$this->session->userdata('agent_id');
                $is_save = $this->reusable_model->insert_into_table("reports_log",$data);
                if($is_save){
                   $src=  base_url()."employee/partner/download_custom_summary_report/".$directory_xls;
                   echo  json_encode(array("response"=>"SUCCESS","url"=>$src));
                }
                else{
                    echo  json_encode(array("response"=>"FAILURE","url"=>$directory_xls));
                }
            }
    }
    function download_upcountry_report(){
        log_message('info', __FUNCTION__ . ' Function Start For Partner '.$this->session->userdata('partner_id'));
        $this->checkUserSession();
        $upcountryCsv= "Upcountry_Report" . date('j-M-Y-H-i-s') . ".csv";
        $csv = TMP_FOLDER . $upcountryCsv;
        $report = $this->upcountry_model->get_upcountry_non_upcountry_district();
        $delimiter = ",";
        $newline = "\r\n";
        $new_report = $this->dbutil->csv_from_result($report, $delimiter, $newline);
        log_message('info', __FUNCTION__ . ' => Rendered CSV');
        write_file($csv, $new_report);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($csv) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($csv));
        readfile($csv);
        exec("rm -rf " . escapeshellarg($csv));
        log_message('info', __FUNCTION__ . ' Function End');
        //unlink($csv);
    }
    function download_waiting_defective_parts(){
         log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id'));
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');
        $where = array(
            "spare_parts_details.defective_part_required" => 1,
            "approved_defective_parts_by_admin" => 1,
            "spare_parts_details.partner_id" => $partner_id,
            "status IN ('" . DEFECTIVE_PARTS_SHIPPED . "')  " => NULL,
            "defactive_part_received_date_by_courier_api IS NOT NULL" => NULL
        );
        $select = "CONCAT( '', GROUP_CONCAT((defective_part_shipped ) ) , '' ) as defective_part_shipped, "
                . " spare_parts_details.booking_id, users.name, courier_name_by_sf, awb_by_sf, spare_parts_details.sf_challan_number, spare_parts_details.partner_challan_number, "
                . "defective_part_shipped_date,remarks_defective_part_by_sf";
        $group_by = "spare_parts_details.booking_id";
        $order_by = "spare_parts_details.defective_part_shipped_date DESC";
        $newCSVFileName = "Waiting_Spare_Parts_".date("Y-m-d").".csv";
        $csv = TMP_FOLDER . $newCSVFileName;
        $report = $this->service_centers_model->get_spare_parts_booking($where, $select, $group_by, $order_by,FALSE,FALSE,0,1);
        $delimiter = ",";
        $newline = "\r\n";
        $new_report = $this->dbutil->csv_from_result($report, $delimiter, $newline);
        log_message('info', __FUNCTION__ . ' => Rendered CSV');
        write_file($csv, $new_report);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($csv) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($csv));
        readfile($csv);
        exec("rm -rf " . escapeshellarg($csv));
        unlink($csv);
    }
    function download_waiting_upcountry_bookings(){
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id'));
        $this->checkUserSession();
        $data = $this->upcountry_model->get_waiting_for_approval_upcountry_charges($this->session->userdata('partner_id'));
        $headings = array("Booking ID","Call Type","Customer Name","Customer Contact Number","Appliance","Brand","Category","Capacity","Address","City","Pincode","State","Upcountry Distance","Upcountry Charges");
        foreach($data as $upcountryBookings){
            $upcountryCharges = round($upcountryBookings['upcountry_distance'] * $upcountryBookings['partner_upcountry_rate'], 2);
            $tempArray = array_values($upcountryBookings);
            array_pop($tempArray);
            array_push($tempArray,$upcountryCharges);
            $CSVData[]  = $tempArray;
        }
        $this->miscelleneous->downloadCSV($CSVData, $headings, "Waiting_Upcountry_Bookings_".date("Y-m-d"));
    }
    function download_spare_part_shipped_by_partner(){
        ob_start();
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id'));
        $this->checkUserSession();
        $CSVData = array();
        $partner_id = $this->session->userdata('partner_id');
        $where = "booking_details.partner_id = '" . $partner_id . "' "
                . " AND status != 'Cancelled' AND parts_shipped IS NOT NULL  ";
        $data= $this->partner_model->get_spare_parts_booking_list($where, NULL, NULL, true);
        $headings = array("Booking ID",
            "SF City",
            "Courier Name",
            "Courier Price",
            "Partner AWB Number",
            "SF AWB Number",
            "Part Shipped By Partner",
            "Part Partner Shipped Date",
            "Partner Challan Number",
            "SF Challan Number",
            "Part Shipped By SF",
            "Part Type",
            "Parts Charge",
            "New Spare Part Received Date",
            "Defective Spare Part Received Date",
            "Booking Status",
            "Spare Status",
            "Is Spare Auto Acknowledge",
            "Remarks");
        
        foreach($data as $sparePartBookings){
            $tempArray = array();            
            $tempArray[] = $sparePartBookings['booking_id'];            
            $tempArray[] = $sparePartBookings['sf_city'];              
            $tempArray[] = $sparePartBookings['courier_name_by_partner'];
            $tempArray[] = $sparePartBookings['courier_price_by_partner'];            
            $tempArray[] = $sparePartBookings['awb_by_partner'];
            $tempArray[] = $sparePartBookings['awb_by_sf'];
            $tempArray[] = $sparePartBookings['parts_shipped'];    
            $tempArray[] = $sparePartBookings['shipped_date'];
            $tempArray[] = $sparePartBookings['partner_challan_number'];
            $tempArray[] = $sparePartBookings['sf_challan_number'];            
            $tempArray[] = $sparePartBookings['defective_part_shipped'];            
            $tempArray[] = $sparePartBookings['shipped_parts_type'];
            $tempArray[] = $sparePartBookings['challan_approx_value'];                   
            $tempArray[] = $sparePartBookings['acknowledge_date'];
            $tempArray[] = $sparePartBookings['received_defective_part_date'];
            $tempArray[] = $sparePartBookings['current_status'];     
            $tempArray[] = $sparePartBookings['status'];
            if($sparePartBookings['auto_acknowledeged']==1){
            $tempArray[] = "Yes";   
             }else{
            $tempArray[] = "No";   
             }                        
            $tempArray[] = $sparePartBookings['remarks_by_partner'];
            $CSVData[]  = $tempArray;            
        }  
        $this->miscelleneous->downloadCSV($CSVData, $headings, "Spare_Part_Shipped_By_Partner_".date("Y-m-d"));

    }
    function download_price_sheet(){
        $partnerID = $this->session->userdata('partner_id');
        $where['partner_id'] = $partnerID;
        $priceArray = $this->service_centre_charges_model->get_partner_price_data($where);
        $config = array('template' => "Price_Sheet.xlsx", 'templateDir' => __DIR__ . "/excel-templates/");
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
                . "GROUP_CONCAT(agent_filters.state) as  state,agent_filters.agent_id as agentid,entity_login_table.agent_id as login_agent_id",
                array("contact_person.entity_type" =>  "partner","contact_person.entity_id"=>$partner_id,"contact_person.is_active"=>1),
                array("entity_role"=>"contact_person.role = entity_role.id","agent_filters"=>"contact_person.id=agent_filters.contact_person_id","entity_login_table"=>"entity_login_table.contact_person_id = contact_person.id"), NULL, 
                array("name"=>'ASC'), NULL,  array("agent_filters"=>"left","entity_role"=>"left","entity_login_table"=>"left"),array("contact_person.id"));
         $data['department'] = $this->reusable_model->get_search_result_data("entity_role", 'DISTINCT department',array("entity_type" => 'partner'),NULL, NULL, array('department'=>'ASC'), NULL, NULL,array());  
         $data['select_state'] = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state) as state",NULL,NULL,NULL,array('state'=>'ASC'),NULL,NULL,array());
         $this->miscelleneous->load_partner_nav_header();
        $this->load->view('partner/contacts', $data);
        $this->load->view('partner/partner_footer');
    }

    
    function get_partner_roles($department){
       $data =  $this->reusable_model->get_search_result_data("entity_role","role,id",array('department'=>$department,"entity_type"=>"partner"),NULL,NULL,array('role'=>"ASC"),NULL,NULL,array());
       $option = "<option value='' disabled selected>Select Role</option>";
       foreach($data as $roles){
           $option = $option."<option value = '".$roles['id']."'>".$roles['role']."</option>";
       }
       echo $option;
    }
    
    function get_partner_roles_filters(){
       $data =  $this->reusable_model->get_search_result_data("entity_role","is_filter_applicable",array('id'=>$this->input->post('role')),NULL,
               NULL,array('role'=>"ASC"),NULL,NULL,array());
       echo  $data[0]['is_filter_applicable'];
    }
    
    /*
     * This function is used to add partner contact persons
     */
    function process_partner_contacts(){
        if($this->input->post('partner_id')){
            $partnerID = $this->input->post('partner_id'); 
            foreach($this->input->post('contact_person_email') as $index=>$contactEmails){
                $agent_id = NULL;
                $data['name'] = $loginData['agent_name']  =  $this->input->post('contact_person_name')[$index];
                $data['official_email'] = $loginData['email'] =  $contactEmails;
                $data['alternate_email'] = $this->input->post('contact_person_alt_email')[$index];
                $data['official_contact_number'] = $this->input->post('contact_person_contact')[$index];
                $data['alternate_contact_number'] = $this->input->post('contact_person_alt_contact')[$index];
                $data['permanent_address'] = $this->input->post('contact_person_address')[$index];
                $data['correspondence_address'] = $this->input->post('contact_person_c_address')[$index];
                $data['role'] = $this->input->post('contact_person_role')[$index];
                $data['entity_id'] = $loginData['entity_id'] = $stateData['entity_id'] = $partnerID;
                $data['entity_type'] = $loginData['entity'] = $stateData['entity_type'] = "partner";
                $data['agent_id'] = $this->session->userdata('id');
                $id = $this->reusable_model->insert_into_table("contact_person",$data);
                $loginData['contact_person_id'] = $stateData['contact_person_id'] = $id;
                // Create Login If Checkbox Checked
                if($this->input->post('checkbox_value_holder')[$index] == 'true'){
                        $password = mt_rand(100000, 999999);
                        $loginData['user_id'] = str_replace(" ","_",$data['name']."_".mt_rand(1,5));
                        $loginData['password'] = md5($password);
                        $loginData['clear_password'] = $password;
                        $loginData['active'] = 1;
                        $agent_id = $this->miscelleneous->create_entity_login($loginData);
                    }
                    if($agent_id){
                        // Map States in agent_filters table 
                        // If state is not selected then add all states
                        $stateString =  $this->input->post('states_value_holder')[$index];
                        if(!$stateString){
                            $states = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state) as state",NULL,NULL,NULL,array('state'=>'ASC'),NULL,NULL,array());
                            $all =1;
                        }
                        else{
                            $states = explode(",",$stateString);
                             $all =0; 
                        }
                        foreach ($states as $state){
                            $stateData['agent_id'] = $agent_id;
                            if($all ==  1){
                                $stateData['state'] = $state['state'];
                            }
                            else{
                                $stateData['state'] = $state;
                            }
                            $stateData['is_active'] = 1;
                            $finalStateData[] = $stateData; 
                        }
                        $this->reusable_model->insert_batch('agent_filters',$finalStateData);
                    }
            }
            if($id){
                $msg =  "Contact Persons has been Added successfully ";
            }
            else{
                $msg =  "Something went Wrong Please try again or contact to admin";
            }
        }
        else{
            $msg =  "Something went Wrong Please try again or contact to admin";
        }
       $this->session->set_userdata('success', $msg);
       if($this->session->userdata('partner_id')){
           redirect(base_url() . 'partner/contacts');
       }
       else{
            redirect(base_url() . 'employee/partner/editpartner/' . $partnerID);
       }
    }
    
    function process_booking_internal_conversation_email() {
        log_message('info', __FUNCTION__ . " Booking ID: " . $this->input->post('booking_id'));
        if ($this->session->userdata('partner_id')) {
            if ($this->input->post('booking_id')) {
                $to = explode(",", $this->input->post('to'));
                $join['entity_login_table'] = "entity_login_table.contact_person_id = contact_person.id";
                $from_email = $this->reusable_model->get_search_result_data("contact_person", "official_email", array("entity_login_table.agent_id" => $this->session->userdata('agent_id')), $join, NULL, NULL, NULL, NULL, array())[0]['official_email'];
                $cc = $this->input->post('cc') . "," . $from_email;
                $row_id = $this->miscelleneous->send_and_save_booking_internal_conversation_email("Partner", $this->input->post('booking_id'), implode(",", $to), $cc
                        , $from_email, $this->input->post('subject'), $this->input->post('msg'), $this->session->userdata('agent_id'), $this->session->userdata('partner_id'));
                if ($row_id) {
                    echo "Successfully Sent";
                } else {
                    echo "Please Try Again";
                }
            } else {
                echo "Please Try Again";
            }
        }
    }

    function get_partner_tollfree_numbers() {
        $data = $this->partner_model->get_tollfree_and_contact_persons();
        $this->miscelleneous->multi_array_sort_by_key($data, "name", "ASC");
        echo json_encode($data);
    }

      /**
     * @desc: This function is used to get display the warehouse information of a partner
     * @params: void
     * @return: warehouse details from table
     * 
     */
       function get_warehouse_details(){
        
        $id = $this->input->post('partner_id');
        $select = "warehouse_details.id as 'wh_id',warehouse_address_line1, warehouse_address_line2, warehouse_city, warehouse_region, warehouse_pincode, warehouse_state, name,contact_person.id as 'contact_person_id'";
        $where1 = array("warehouse_details.entity_id" => $id, "warehouse_details.entity_type" => "partner");
        $data= $this->inventory_model->get_warehouse_details($select, $where1,false);
        echo json_encode($data);

        
    }
    /**
     * @desc: This function is used to insert new warehouse information in the table
     * @params: void
     * @return: prints message if data inserted correctly or not
     * 
     */
    public function process_add_warehouse_details() {
        log_message('info',__METHOD__.' add warehouse details');
        $this->form_validation->set_rules('warehouse_address_line1', 'warehouse_address_line1', 'required|trim');
        $this->form_validation->set_rules('warehouse_city','warehouse_city', 'required|trim');
        $this->form_validation->set_rules('warehouse_region', 'warehouse_region','required|trim');
        $this->form_validation->set_rules('warehouse_pincode', 'warehouse_pincode','required|trim');
        $this->form_validation->set_rules('warehouse_state', 'warehouse_state','required|trim');
        $this->form_validation->set_rules('contact_person_id', 'Contact Person','required|trim');
        $this->form_validation->set_rules('warehouse_state_mapping', 'Wareshoue State','required');

        if ($this->form_validation->run() == TRUE) {
            $wh_data = array(
                'warehouse_address_line1' => $this->input->post('warehouse_address_line1'),
                'warehouse_address_line2' => $this->input->post('warehouse_address_line2'),
                'warehouse_city' => $this->input->post('warehouse_city'),
                'warehouse_region' => $this->input->post('warehouse_region'),
                'warehouse_pincode' => $this->input->post('warehouse_pincode'),
                'warehouse_state' => $this->input->post('warehouse_state'),
                'entity_id' => $this->input->post('partner_id'),
                'entity_type' => _247AROUND_PARTNER_STRING,
                'create_date' => date('Y-m-d H:i:s')
               
            );
            
            if(in_array('All', $this->input->post('warehouse_state_mapping'))){
                $state = array_column($this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state) as state",NULL,NULL,NULL,array('state'=>'ASC'),NULL,NULL,array()), 'state');
            }else{
                $state = $this->input->post('warehouse_state_mapping');
            }
            
            $wh_contact_person_mapping_data['contact_person_id'] = $this->input->post('contact_person_id');
            $wh_state_mapping_data = $state;
            $status = $this->inventory_model->insert_warehouse_details($wh_data,$wh_contact_person_mapping_data,$wh_state_mapping_data);
            if (!empty($status)) {
                log_message("info", __METHOD__ . " Data Entered Successfully");
                $this->session->set_userdata('success', 'Data Entered Successfully');
                redirect(base_url() . 'employee/partner/get_add_partner_form');
            } else {
                log_message("info", __METHOD__ . " Error in adding details");
                $this->session->set_userdata('failed', 'Data can not be inserted. Please Try Again...');
                redirect(base_url() . 'employee/partner/get_add_partner_form');
            }
        }else{
            $this->session->set_userdata('error', 'Please Select All Field');
            redirect(base_url() . 'employee/partner/editpartner/' . $this->input->post('partner_id'));
        } 
    }  
    //update a single contact
    function edit_partner_contacts(){
       if($this->input->post('partner_id')){
            $partnerID = $this->input->post('partner_id');
            $pid = $this->input->post('contact_id');
            $agent_id = $this->input->post('agentid');
            $data['name'] = $loginData['agent_name']  =  $this->input->post('contact_person_name');
            $data['official_email'] = $loginData['email'] =  $this->input->post('contact_person_email');
            $data['alternate_email'] = $this->input->post('contact_person_alt_email');
            $data['official_contact_number'] = $this->input->post('contact_person_contact');
            $data['alternate_contact_number'] = $this->input->post('contact_person_alt_contact');
            $data['permanent_address'] = $this->input->post('contact_person_address');
            $data['correspondence_address'] = $this->input->post('contact_person_c_address');
            $data['role'] = $this->input->post('contact_person_role');
            $data['entity_id'] = $loginData['entity_id'] = $stateData['entity_id'] = $partnerID;
            $data['entity_type'] = $loginData['entity'] = $stateData['entity_type'] = "partner";
            $data['agent_id'] = $this->session->userdata('id');
            $where = array('id' =>$pid);
            $update_data1 = $this->reusable_model->update_table("contact_person",$data,$where);
            $loginData['contact_person_id'] = $stateData['contact_person_id'] = $pid;
            // Create Login If Checkbox Checked
            if($this->input->post('checkbox_value_holder') == true && !$agent_id){
                    $password = mt_rand(100000, 999999);
                    $loginData['user_id'] = str_replace(" ","_",$data['name']."_".$partnerID."_".mt_rand(10, 99));
                    $loginData['password'] = md5($password);
                    $loginData['clear_password'] = $password;
                    $loginData['active'] = 1;
                    $agent_id = $this->miscelleneous->create_entity_login($loginData);
             }
                // If state is not selected then add all states
                if($agent_id){
                        $stateString =  $this->input->post('states_value_holder');
                        if(!$stateString){
                            $states = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state) as state",NULL,NULL,NULL,array('state'=>'ASC'),NULL,NULL,array());
                            $all =1;
                        }
                        else{
                            $states = explode(",",$stateString);
                             $all =0; 
                        }
                        foreach ($states as $state){
                            $stateData['agent_id'] = $agent_id;
                            if($all ==  1){
                                $stateData['state'] = $state['state'];
                            }
                            else{
                                $stateData['state'] = $state;
                            }
                            $stateData['is_active'] = 1;
                            $finalStateData[]= $stateData; 
                        }
                        $where= array('contact_person_id' =>$pid);
                        if($where)
                            $this->reusable_model->delete_from_table('agent_filters',$where);
                            $update_data2 = $this->reusable_model->insert_batch('agent_filters',$finalStateData);
                         }
            if($update_data1 || $update_data2){
                $msg =  "Contact Persons has been Updated successfully ";
            }
            else{
                $msg =  "No update done";
            }
        }
        else{
            $msg =  "Something went Wrong Please try again or contact to admin";
        }
        if($this->session->userdata('partner_id')){
            $this->session->set_userdata('success', $msg);
            redirect(base_url() . 'partner/contacts');
        }
        else{
            $this->session->set_userdata('success', $msg);
            redirect(base_url() . 'employee/partner/editpartner/' . $partnerID);
        }
    }
    
    function delete_partner_contacts($contact_id,$partnerID){
        $where["entity_id"] = $partnerID;
        $where["contact_person_id"] = $contact_id;
        if(!empty($where)){
            //Update Entity Login Table
            $this->reusable_model->update_table("entity_login_table",array("active"=>0),array("entity_id"=>$partnerID,"contact_person_id"=>$contact_id));
            //Update Agent Filter Table 
            $this->reusable_model->update_table('agent_filters',array("is_active"=>0),$where);
            //Update Contact Person Table
            $this->reusable_model->update_table('contact_person',array('is_active'=>0),array('id'=>$contact_id,'entity_id'=>$partnerID));
            $msg = "Contact deleted successfully";
            $this->session->set_userdata('success', $msg);
        }
        else{
            $msg = "Something Went Wrong , Please Try Again";
            $this->session->set_userdata('error', $msg);
        }
        if($this->session->userdata('partner_id')){
            redirect(base_url() . 'partner/contacts');
        }
       else{
             redirect(base_url() . 'employee/partner/editpartner/' . $partnerID);
       }
    }
    
    /**
     * @desc: This Function is used to search the docket number
     * @param: void
     * @return : void
     */
    function search_docket_number() {
        $this->checkUserSession();
        $this->miscelleneous->load_partner_nav_header();
        $this->load->view('partner/search_docket_number');
        $this->load->view('partner/partner_footer');
    }
    function partner_dashboard() {
        $this->checkUserSession();
        $this->miscelleneous->load_partner_nav_header();
        $serviceWhere['isBookingActive'] =1;
        $services = $this->reusable_model->get_search_result_data("services","*",$serviceWhere,NULL,NULL,array("services"=>"ASC"),NULL,NULL,array());
         if($this->session->userdata('user_group') == PARTNER_CALL_CENTER_USER_GROUP){
            $this->load->view('partner/partner_default_page_cc');
        }
        else{
            $this->load->view('partner/partner_dashboard',array('services'=>$services));
        }
        $this->load->view('partner/partner_footer');
        if(!$this->session->userdata("login_by")){
            $this->load->view('employee/header/push_notification');
        }
    }
    
    
    /**
     * @desc: This Function is used to edit warehouse deatails
     * @param: void
     * @return : JSON
     */
    function edit_warehouse_details() {
        log_message('info', 'edit warehouse details updated data ' . print_r($_POST, true));
        $wh_id = $this->input->post('wh_id');
        if (!empty($wh_id)) {
            $res = array();
            $wh_data = array(
                'warehouse_address_line1' => $this->input->post('wh_address_line1'),
                'warehouse_address_line2' => $this->input->post('wh_address_line2'),
                'warehouse_city' => $this->input->post('wh_city'),
                'warehouse_region' => $this->input->post('wh_region'),
                'warehouse_pincode' => $this->input->post('wh_pincode'),
                'warehouse_state' => $this->input->post('wh_state')
            );

            $update_wh = $this->inventory_model->edit_warehouse_details(array('id' => $wh_id), $wh_data);

            $updated_contact_person_id = $this->input->post('wh_contact_person_id');
            $old__contact_person_id = $this->input->post('old_contact_person_id');

            //if contact person change then update the contact person mapping in the warehouse_contact_person_mapping table
            //here we assume that every wh have only one contact person
            //if there are more than two contact person for the same warehouse than please change this logic
            if ($updated_contact_person_id !== $old__contact_person_id) {
                $update_wh_contact_pesron_mapping = $this->inventory_model->update_warehouse_contact_person_mapping(array('warehouse_id' => $wh_id), array('contact_person_id' => $updated_contact_person_id));
                if ($update_wh_contact_pesron_mapping) {
                    $res['status'] = true;
                    $res['msg'] = 'Details Updated Successfully';
                } else {
                    $res['status'] = false;
                    $res['msg'] = 'Details not updated. Please Try Again...';
                }
            }


            if (!empty(array_diff($this->input->post('wh_state_mapping'), explode(',', $this->input->post('old_mapped_state_data'))))) {
                $data['wh_id'] = $wh_id;
                $data['new_wh_state_mapping'] = $this->input->post('wh_state_mapping');
                $update_state_mapping = $this->inventory_model->update_wh_state_mapping_data($data);

                if ($update_state_mapping) {
                    $res['status'] = true;
                    $res['msg'] = 'Details Updated Successfully';
                } else {
                    $res['status'] = true;
                    $res['msg'] = 'State Mapping Not Updated . Please try again...';
                }
            }
            
            if(!empty($res)){
                $res = $res;
            }else if ($update_wh) {
                $res['status'] = true;
                $res['msg'] = 'Details Updated Successfully';
            } else {
                $res['status'] = false;
                $res['msg'] = 'Details not updated. Please Try Again...';
            }
        } else {
            $res['status'] = false;
            $res['msg'] = 'Warehouse Id can not be empty';
        }

        echo json_encode($res);
    }

    function get_warehouse_state_mapping(){
        $wh_id = $this->input->post('wh_id');
        if(!empty(trim($wh_id))){
            $wh_state_mapping_datails = $this->reusable_model->get_search_query('warehouse_state_relationship','state',array('warehouse_state_relationship.warehouse_id' => $wh_id),NULL,NULL,array('state'=>'ASC'),NULL,NULL)->result_array();
            if(!empty($wh_state_mapping_datails)){
                $res['status'] = TRUE;
                $res['msg'] = array_map(function($val){ return strtoupper($val);}, array_column($wh_state_mapping_datails, 'state'));
            }else{
                $res['status'] = FALSE;
                $res['msg'] = 'No Data Found';
            }
            
        }else{
            $res['status'] = FALSE;
            $res['msg'] = 'Warehouse ID can not be empty';
        }
        
        echo json_encode($res);
    }
    function download_real_time_summary_report($partnerID){
        $newCSVFileName = "Booking_summary_" . date('j-M-Y-H-i-s') . ".csv";
        $csv = TMP_FOLDER . $newCSVFileName;
        $report = $this->partner_model->get_partner_leads_csv_for_summary_email($partnerID,0);
        $delimiter = ",";
        $newline = "\r\n";
        $new_report = $this->dbutil->csv_from_result($report, $delimiter, $newline);
        log_message('info', __FUNCTION__ . ' => Rendered CSV');
        write_file($csv, $new_report);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($csv) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($csv));
        readfile($csv);
        exec("rm -rf " . escapeshellarg($csv));
         unlink($csv);
    }
      function checked_complete_review_booking() {
        $requested_bookings = $this->input->post('approved_booking');
        if($requested_bookings){
            $where['is_in_process'] = 0;
            $whereIN['booking_id'] = $requested_bookings; 
            $tempArray = $this->reusable_model->get_search_result_data("booking_details","booking_id",$where,NULL,NULL,NULL,$whereIN,NULL,array());
            foreach($tempArray as $values){
                $approved_booking[] = $values['booking_id'];
            }
            $inProcessBookings = array_diff($requested_bookings,$approved_booking);
            $this->session->set_flashdata('inProcessBookings', $inProcessBookings);
            $url = base_url() . "employee/do_background_process/complete_booking";
            if (!empty($approved_booking)) {
                $this->booking_model->mark_booking_in_process($approved_booking);
                $data['booking_id'] = $approved_booking;
                $data['agent_id'] = $this->session->userdata('agent_id');
                $data['agent_name'] = $this->session->userdata('partner_name');
                $data['partner_id'] = $this->input->post('partner_id');
                $data['approved_by'] = $this->input->post('approved_by'); 
                $this->asynchronous_lib->do_background_process($url, $data);
                $this->push_notification_lib->send_booking_completion_notification_to_partner($approved_booking);
            } else {
                //Logging
                log_message('info', __FUNCTION__ . ' Approved Booking Empty from Post');
            }
        }
       redirect(base_url() . 'partner/home'); 
    }
    function reject_booking_from_review(){
        if($this->input->post('booking_id')){
        $postArray = $this->input->post();
        $where['is_in_process'] = 0;
        $whereIN['booking_id'] = $postArray['booking_id']; 
        $tempArray = $this->reusable_model->get_search_result_data("booking_details","booking_id",$where,NULL,NULL,NULL,$whereIN,NULL,array());
        if(!empty($tempArray)){
            $this->booking_model->mark_booking_in_process(array($postArray['booking_id']));
            echo "Booking Updated Successfully";
            $postArray = $this->input->post();
            $this->miscelleneous->reject_booking_from_review($postArray);
        }
        else{
            echo "Someone Else is Updating the booking , Please check updated booking and try again";
        }
    }
    }
    function partner_review_bookings($offset = 0, $all = 0) {
        $this->checkUserSession();
        $data['is_ajax'] = $this->input->post('is_ajax');
        if(empty($this->input->post('is_ajax'))){
            $this->miscelleneous->load_partner_nav_header();
            $this->load->view('partner/get_waiting_to_review');
            $this->load->view('partner/partner_footer');
        }else{
            $this->load->view('partner/get_waiting_to_review');
        }
    }
    
    function download_partner_review_bookings($partnerID){
        ob_start();
        $finalArray = array();
        $data = $this->miscelleneous->get_review_bookings_for_partner($partnerID);
        foreach($data as $key => $values){
            $values['Booking_ID'] = $key;
            unset($values['booking_jobcard_filename']);
            unset($values['amount_due']);
            unset($values['partner_id']);
            ksort($values);
            $finalArray[] = $values;
        }
        if(!empty($finalArray)){
            $headings = array_keys($finalArray[0]);
            $this->miscelleneous->downloadCSV(array_values($finalArray), $headings, "Review_bookings");
        }
    }
    function get_pending_bookings(){
        $this->checkUserSession();
          $columnMappingArray = array("column_1"=>"booking_details.booking_id","column_3"=>"appliance_brand","column_4"=>"booking_details.partner_internal_status","column_7"=>"booking_details.city",
                "column_8"=>"booking_details.state","column_9"=>"STR_TO_DATE(booking_details.booking_date,'%d-%m-%Y')","column_10"=>"DATEDIFF(CURDATE(),STR_TO_DATE(booking_details.initial_booking_date,'%d-%m-%Y'))");
        $order['column'] = $columnMappingArray["column_10"];
        $order['sorting'] = "desc";
        $state = 0;
        if($this->session->userdata('is_filter_applicable') == 1){
            $state = 1;
        }
        $postData = $this->input->post();
        if(array_key_exists("order", $postData)){
            $order['column'] =$columnMappingArray["column_".$postData['order'][0]['column']];
            $order['sorting'] = $postData['order'][0]['dir'];
        }
        $bookingID = $this->input->post('booking_id');
        $finalArray = array();
        $partner_id = $this->session->userdata('partner_id');
        $selectData = "Distinct services.services,users.name as customername, users.phone_number,booking_details.*,appliance_brand,"
                . "DATEDIFF(CURDATE(),STR_TO_DATE(booking_details.initial_booking_date,'%d-%m-%Y')) as aging, count_escalation ";
        $selectCount = "Count(DISTINCT booking_details.booking_id) as count";
        $bookingsCount = $this->partner_model->getPending_booking($partner_id, $selectCount,$bookingID,$state,NULL,NULL,$this->input->post('state'))[0]->count;
        $bookings = $this->partner_model->getPending_booking($partner_id, $selectData,$bookingID,$state,$this->input->post('start'),$this->input->post('length'),$this->input->post('state'),$order);
        $sn_no = $this->input->post('start')+1;
        $upcountryString = "";
        foreach ($bookings as $key => $row) { 
             $tempArray = array();
             $upcountryString = $tempString = "";
             $tempString = "'".$row->booking_id."'";
             $tempString2 = "'".$row->amount_due."'";
              if ($row->is_upcountry == 1 && $row->upcountry_paid_by_customer == 0) {
                 $upcountryString = '<i style="color:red; font-size:20px;" onclick="open_upcountry_model('.$tempString.','.$tempString2.')"
                    class="fa fa-road" aria-hidden="true"></i>';
               } 
             $tempArray[] = $sn_no . $upcountryString;
             $tempArray[] = '<a style="color:blue;" href='.base_url().'partner/booking_details/'.$row->booking_id.' target="_blank" title="View">'.$row->booking_id.'</a>';
            $requestType =  $row->request_type;
            if (strpos($row->request_type, 'Installation') !== false) {
                $requestType =  "Installation";
            }
            else if(strpos($row->request_type, 'Repair') !== false){
                $requestType =  "Repair";
            }
            $tempArray[] = $row->services . "<br>". $requestType;
            $tempArray[]  = $row->appliance_brand; 
            $is_escalation = "";
             if ($row->count_escalation>0) {
                  $is_escalation =  '<i data-toggle="tooltip" title="Escalation" style="color:red; font-size:13px;" onclick="" class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></i>';
            } 
            $tempArray[] =  $is_escalation.$row->partner_internal_status;
            $tempArray[] = $row->customername;
            $tempArray[] = $row->booking_primary_contact_no;
            $tempArray[] = $row->city;
            $tempArray[] = $row->state;
            $tempArray[] = $row->booking_date;
            $tempArray[] = $row->aging;
            $bookingIdTemp = "'".$row->booking_id."'";
            $tempArray[] = '<a style="width: 36px;background: #5cb85c;border: #5cb85c;" class="btn btn-sm btn-primary  relevant_content_button" data-toggle="modal" title="Email"  onclick="create_email_form('.$bookingIdTemp.')"><i class="fa fa-envelope" aria-hidden="true"></i></a>';
            if ($row->type == _247AROUND_QUERY) { 
                $helperString = ' style="background-color: #26b99a;border-color:#26b99a;color:#fff;padding: 5px 0px;margin: 0px"';
            } 
            else { 
                $helperString = ' style="background-color: #26b99a;border-color:#26b99a;color:#fff;padding: 5px 0px;margin: 0px"';
            }
            if ($row->type != _247AROUND_QUERY) { 
                $tempArray[]= '<div class="dropdown">
                                                    <button class="btn btn-sm btn-primary" type="button" data-toggle="dropdown" style="border: 1px solid #2a3f54;background: #2a3f54;padding: 4px 24px;">Action
                                                    <span class="caret"></span></button>
                                                    <ul class="dropdown-menu" style="padding: 5px 5px 5px 5px;margin: 0px;min-width: 95px;position: inherit;z-index: 100;">
                                                        <li style="color: #fff;"><a class="btn btn-sm btn-primary" href="'.base_url().'partner/update_booking/'.$row->booking_id.'"  title="View" 
                                                            style="background-color:#2C9D9C; border-color: #2C9D9C;color:#fff;padding: 5px 0px;
        margin: 0px;">Update</a></li>
                                                        <li style="color: #fff;margin-top:5px;">
                                                            <a id="a_hover"'.$helperString.' href="'.base_url().'partner/get_reschedule_booking_form/'.$row->booking_id.'" id="reschedule" class="btn btn-sm btn-success" title ="Reschedule">Reschedule</a>
                                                        </li>
                                                         <li style="color: #fff;margin-top:5px;">
                                                             <a id="a_hover" style="background-color: #d9534f;border-color:#d9534f;color:#fff;padding: 5px 0px;margin: 0px;"href='.base_url().'partner/get_cancel_form/Pending/'.$row->booking_id.' class="btn btn-sm btn-danger" title="Cancel">Cancel</a>
                                                         </li>
                                                    </ul>
                                                </div>';
            }
            else{
              $tempArray[] =  "";
            }
            $tempArray[] =  '<a target="_blank" href="https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/'.$row->booking_jobcard_filename.'" class="btn btn-sm btn-primary btn-sm" target="_blank" ><i class="fa fa-download" aria-hidden="true"></i></a>';
            $initialBooking = strtotime($row->initial_booking_date);
            $now = time();
            $datediff = $now - $initialBooking;
            $days= $datediff / (60 * 60 * 24);
            $futureBookingDateMsg = "'Booking has future booking date so you can not escalate the booking'";
            $partnerDependencyMsg = "'Escalation can not be Processed, Because booking in ".$row->partner_internal_status." state'";
            if ($row->type == "Query") {
                $helperText_2 = 'style="pointer-events: none;background: #ccc;border-color:#ccc;"'; 
            }
            if($row->actor != 'Partner' && $days>=0){
               $helperText_2 =  'data-target="#myModal"';
            } 
            else if($days<0){  
              $helperText_2 =  'onclick="alert('.$futureBookingDateMsg.')"' ;
            }
            else{
              $helperText_2 = 'onclick="alert("'.$partnerDependencyMsg.'")"'; 
              }
            $tempArray[] = '<a  href="#" class="btn btn-sm btn-warning open-AddBookDialog" data-id= "'.$row->booking_id.'" '.$helperText_2.' data-toggle="modal" title="Escalate"><i class="fa fa-circle" aria-hidden="true"></i></a>';
            $finalArray[] = $tempArray;
             $sn_no++;
           }
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $bookingsCount,
            "recordsFiltered" =>  $bookingsCount,
            "data" => $finalArray,
        );
        echo json_encode($output);
    }
    function get_spare_bookings(){
      $agent_id = $this->session->userdata('agent_id');
      $finalArray = array();
      $postData = $this->input->post();
      $state = 0;
      $columnMappingArray = array("column_1"=>"spare_parts_details.booking_id","column_3"=>"DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(date_of_request, '%Y-%m-%d'))",
          "column_4"=>"GROUP_CONCAT(DISTINCT spare_parts_details.parts_requested)","column_7"=>"booking_details.state");    
      $order['column'] =$columnMappingArray["column_3"];
      $order['sorting'] = "desc";
      if(array_key_exists("order", $postData)){
            $order['column'] =$columnMappingArray["column_".$postData['order'][0]['column']];
            $order['sorting'] = $postData['order'][0]['dir'];
        }
       $partner_id = $this->session->userdata('partner_id');
       $where = "spare_parts_details.partner_id = '" . $partner_id . "' AND  spare_parts_details.entity_type =  '"._247AROUND_PARTNER_STRING."' AND status = '" . SPARE_PARTS_REQUESTED . "' "
                . " AND booking_details.current_status IN ('"._247AROUND_PENDING."', '"._247AROUND_RESCHEDULED."') "
                . " AND wh_ack_received_part != 0 ";
       if($this->input->post('state')){
           $state = $this->input->post('state');
           $where = $where." AND booking_details.state = '$state'";
       }
       if($this->input->post('booking_id')){
           $booking_id = $this->input->post('booking_id');
           $where = $where." AND booking_details.booking_id = '$booking_id'";
       }
       if($this->session->userdata('is_filter_applicable') == 1){
            $state = 1;
            $where .= " AND booking_details.state IN (SELECT state FROM agent_filters WHERE agent_id = ".$agent_id." AND agent_filters.is_active=1)";
        }
        $select = "spare_parts_details.booking_id, GROUP_CONCAT(DISTINCT spare_parts_details.parts_requested) as parts_requested, users.name, "
                . "booking_details.booking_primary_contact_no, booking_details.partner_id as booking_partner_id, booking_details.state, "
                . "booking_details.booking_address,booking_details.initial_booking_date, booking_details.is_upcountry, "
                . "booking_details.upcountry_paid_by_customer,booking_details.amount_due,booking_details.state, service_centres.name as vendor_name, "
                . "service_centres.address, service_centres.state, service_centres.gst_no, service_centres.pincode, "
                . "service_centres.district,service_centres.id as sf_id,service_centres.is_gst_doc,service_centres.signature_file, "
                . "DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(date_of_request, '%Y-%m-%d')) AS age_of_request,"
                . " GROUP_CONCAT(DISTINCT spare_parts_details.model_number) as model_number, "
                . " GROUP_CONCAT(DISTINCT spare_parts_details.serial_number) as serial_number,"
                . " GROUP_CONCAT(DISTINCT spare_parts_details.remarks_by_sc) as remarks_by_sc, spare_parts_details.partner_id, "
                . " GROUP_CONCAT(DISTINCT spare_parts_details.id) as spare_id, serial_number_pic ";
        $bookingData = $this->service_centers_model->get_spare_parts_on_group($where, $select, "spare_parts_details.booking_id", false, $postData['length'], $postData['start'],0,$order);
         $bookingCount = $this->service_centers_model->get_spare_parts_on_group($where, "count( Distinct spare_parts_details.booking_id) AS total_rows","spare_parts_details.booking_id", FALSE,-1,-1,1)[0]['total_rows'];
         $sn = $postData['start'];
         foreach ($bookingData as $key => $row) {
                    $tempArray = array();
                    $sn++;
                    $tempString = $tempString2 = $tempString3 = $tempString4 = $tempString5 ="";
                    if($row['is_upcountry'] == 1 && $row['upcountry_paid_by_customer'] == 0) {
                       $tempString = '<i style="color:red; font-size:20px;" onclick="open_upcountry_model('.$row['booking_id'].'", "'.$row['amount_due'].')" class="fa fa-road" aria-hidden="true"></i>';
                    }
                    $tempArray[] =  $sn. $tempString;
                    $tempArray[] =  '<a target="_blank"  style="color:blue;" href='.base_url().'partner/booking_details/'.$row['booking_id'].'  title="View">'.$row['booking_id'].'</a>';
                    $tempArray[] =  $row['name'];
                    $tempArray[] =  $row['age_of_request'];
                    $tempArray[] =  $row['parts_requested'];
                    $tempArray[] =  $row['model_number'];
                    $tempArray[] =  $row['serial_number'];
                    $tempArray[] =  $row['state'];
                    $tempArray[] =  $row['remarks_by_sc'];
                    $bookingIdTemp = "'".$row['booking_id']."'";
                    $tempArray[] =  '<a style="width: 36px;background: #5cb85c;border: #5cb85c;" class="btn btn-sm btn-primary  relevant_content_button" data-toggle="modal" title="Email"  onclick="create_email_form('.$bookingIdTemp.')"><i class="fa fa-envelope" aria-hidden="true"></i></a>';
                    $tempString2 =  '<div class="dropdown">
                            <button class="btn btn-sm btn-primary" type="button" data-toggle="dropdown" style="    border: 1px solid #2a3f54;background: #2a3f54;">Action
                            <span class="caret"></span></button>
                            <ul class="dropdown-menu" style="border: none;background: none;z-index: 100;position: inherit;min-width: 70px;">
                                <div class="action_holder" style="background: #fff;border: 1px solid #2c9d9c;padding: 1px;">
                                <li style="color: #fff;"><a href='.base_url().'partner/update_spare_parts_form/'.$row['booking_id'].' class="btn btn-sm btn-success" title="Update" style="color:#fff;margin: 0px;padding: 5px 12px;" ></i>Update</a></li>';
                    $explode = explode(",", $row['spare_id']);
                    if(count($explode) == 1){ 
                     $tempString3 =  '<li style="color: #fff;margin-top:5px;"><a href="#" data-toggle="modal" id="spare_parts"'.$row['spare_id'].'" data-url='.base_url().'employee/inventory/update_action_on_spare_parts/'.$row['spare_id'] . '/' . $row['booking_id'].'/CANCEL_PARTS data-booking_id="'.$row['booking_id'].'" data-target="#myModal2" class="btn btn-sm btn-danger open-adminremarks" title="Reject" style="color:#fff;margin: 0px;padding: 5px 14.4px;" >Reject</a></li>';
                    }
                     $tempString4 = '</ul>';
                     $tempArray[] =  $tempString2 . $tempString3 .$tempString4;
                     if(!empty($row['is_gst_doc'])){
                         $tempString5 = '<a class="btn btn-sm btn-success" href="#" title="GST number not available" style="background-color:#2C9D9C; border-color: #2C9D9C; cursor: not-allowed;"><i class="fa fa-close"></i></a>';
                     }
                     else if(empty ($row['signature_file'])) {
                           $tempString5 = '<a class="btn btn-sm btn-success" href="#" title="Signature file is not available" style="background-color:#2C9D9C; border-color: #2C9D9C;cursor: not-allowed;"><i class="fa fa-times"></i></a>';
                      }
                      else{
                            $tempString5 = '<a class="btn btn-sm btn-success" href='.base_url().'partner/download_sf_declaration/'.rawurlencode($row['sf_id']).'  title="Download Declaration" style="background-color:#2C9D9C; border-color: #2C9D9C;" target="_blank"><i class="fa fa-download"></i></a>';
                        }
                      $tempArray[] = $tempString5;
                      $tempArray[] = '<input type="checkbox" class="form-control checkbox_address"  name="download_address[]" onclick="check_checkbox(1)" value="'.$row['booking_id'].'" />';
                      $tempArray[] = '<input type="checkbox" class="form-control checkbox_manifest" name="download_courier_manifest[]" onclick="check_checkbox(0)" value="'.$row['booking_id'].'" />';
                      $finalArray[] = $tempArray;
           }
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $bookingCount,
            "recordsFiltered" =>  $bookingCount,
            "data" => $finalArray,
        );
        echo json_encode($output);
    }
     function get_defactive_part_shipped_by_sf_bookings(){
      $finalArray = array();
      $postData = $this->input->post();
      $state = 0;
      $where_internal_status = array("page" => "defective_parts", "active" => '1');
      $internal_status = $this->booking_model->get_internal_status($where_internal_status);
      $columnMappingArray = array("column_1"=>"spare_parts_details.booking_id","column_3"=>"CONCAT('',GROUP_CONCAT((defective_part_shipped ) ))",
          "column_4"=>"courier_name_by_sf","column_9"=>"spare_parts_details.defective_part_shipped_date");    
      $order_by = "spare_parts_details.defective_part_shipped_date DESC, spare_parts_details.booking_id DESC";
      if(array_key_exists("order", $postData)){
            $order_by = $columnMappingArray["column_".$postData['order'][0]['column']] ." ". $postData['order'][0]['dir'];
        }
       $partner_id = $this->session->userdata('partner_id');
            if($this->session->userdata('is_filter_applicable') == 1){
            $state = 1;
         }
        $where = array(
            "spare_parts_details.defective_part_required" => 1,
            "approved_defective_parts_by_admin" => 1,
            "spare_parts_details.partner_id" => $partner_id,
            "status IN ('" . DEFECTIVE_PARTS_SHIPPED . "')  " => NULL
        );
       if($this->input->post('state')){
           $where['booking_details.state'] = $this->input->post('state');
       }
       if($this->input->post('booking_id')){
           $where['spare_parts_details.booking_id'] = $this->input->post('booking_id');
       }
        $select = "defective_part_shipped,spare_parts_details.defactive_part_received_date_by_courier_api, "
                . " spare_parts_details.booking_id, users.name, courier_name_by_sf, awb_by_sf,defective_part_shipped_date,"
                . "remarks_defective_part_by_sf,spare_parts_details.sf_challan_number"
                . ",spare_parts_details.sf_challan_file,spare_parts_details.partner_challan_number, spare_parts_details.id";
        $group_by = "spare_parts_details.id";
        $bookingData = $this->service_centers_model->get_spare_parts_booking($where, $select, $group_by, $order_by, $postData['start'], $postData['length']);
         $bookingCount = $this->service_centers_model->count_spare_parts_booking($where, $select, $group_by,$state);
         $sn = $postData['start'];
         foreach ($bookingData as  $row) {
                    $tempArray = array();
                    $tempString = $tempString2 = $tempString3 = $tempString4 = $tempString5 = $tempString6 = $tempString7 = "";
                    $sn++;
                    $tempArray[] = $sn;
                    $tempArray[] = '<a target="_blank"  style="color:blue" href='.base_url().'partner/booking_details/'.$row['booking_id'].'  title="View">'.$row['booking_id'].'</a>';
                    $tempArray[] = $row['name'];
                    $tempArray[] = $row['defective_part_shipped'];
                    $tempArray[] = $row['courier_name_by_sf'];
                    $courier_name_by_sf = "'".$row['courier_name_by_sf']."'";
                    $awb_by_sf = "'".$row['awb_by_sf']."'";
                    $spareStatus = "'".DELIVERED_SPARE_STATUS."'";
                    if(!$row['defactive_part_received_date_by_courier_api']){
                        $spareStatus = "'".DEFECTIVE_PARTS_SHIPPED."'";
                    }
                    $container = "'awb_loader_".$row['awb_by_sf']."'";
                    $awbString = '<a href="javascript:void(0)" onclick="get_awb_details('.$courier_name_by_sf.','.$awb_by_sf.','.$spareStatus.','.$container.')">'.$row['awb_by_sf'].'</a> 
                                            <span id='.$container.' style="display:none;"><i class="fa fa-spinner fa-spin"></i></span>';
                    $tempArray[] = $awbString;
                    if(!empty($row['sf_challan_file'])) {  
                         $tempString = '<a style="color: blue;" href="https://s3.amazonaws.com/'.BITBUCKET_DIRECTORY.'/vendor-partner-docs/'.$row['sf_challan_file'].'" target="_blank">'.$row["sf_challan_number"].'</a>';
                    }
                    $tempArray[] = $tempString;
                     if(!empty($row['partner_challan_file'])) {
                        $tempString2 = '<a href="https://s3.amazonaws.com/'. BITBUCKET_DIRECTORY.'/vendor-partner-docs/'.$row['partner_challan_file'].'" target="_blank">'.$row["partner_challan_number"].'</a>';
                     }
                     else if(!empty($row['partner_challan_number'])) {
                         $tempString2 = $row['partner_challan_number'];
                    }
                    $tempArray[] = $tempString2;
                     $bookingIdTemp = "'".$row['booking_id']."'";
                     $tempArray[] = '<a style="width: 36px;background: #5cb85c;border: #5cb85c;" class="btn btn-sm btn-primary  relevant_content_button" data-toggle="modal" title="Email" onclick="create_email_form_2('.$bookingIdTemp.')"><i class="fa fa-envelope" aria-hidden="true"></i></a>';
                     if (!is_null($row['defective_part_shipped_date'])) {
                         $tempString3 =  date("d-m-Y", strtotime($row['defective_part_shipped_date']));
                     }
                    $tempArray[] = $tempString3;
                    $tempArray[] = $row['remarks_defective_part_by_sf'];
                     if (!empty($row['defective_part_shipped'])) {
                            if(empty($row['defective_part_shipped'])){
                             $tempString5 = 'disabled="disabled"';
                            }
                        $tempString4 = '<a style="background: #2a3f54; border-color: #2a3f54;" onclick="return confirm_received()" class="btn btn-sm btn-primary" id="defective_parts"
                                               href='.base_url().'partner/acknowledge_received_defective_parts/'.$row['id'].'/'.$row['booking_id'].'/'.$this->session->userdata("partner_id").' '.$tempString5.'>Receive</a>';
                     }
                     $tempArray[] = $tempString4;
                     if (!empty($row['defective_part_shipped'])) {
                            foreach ($internal_status as $value) {
                                  $tempString7 = $tempString7.'<li><a href='.base_url().'partner/reject_defective_part/'.$row['id'].'/'.$row['booking_id'].'/'.urlencode(base64_encode($value->status)).'>'.$value->status.'</a></li>';
                                  $tempString7 = $tempString7.'<li class="divider"></li>';
                             } 
                              $tempString6 = '<div class="dropdown">
                                            <a href="#" class="dropdown-toggle btn btn-sm btn-danger" type="button" data-toggle="dropdown">Reject<span class="caret"></span></a>
                                            <ul class="dropdown-menu" style="right: 0px;left: auto;">'.$tempString7.'</ul> </div>';
                       }
                       $tempArray[] = $tempString6;
                       $finalArray[] = $tempArray;
           }
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $bookingCount,
            "recordsFiltered" =>  $bookingCount,
            "data" => $finalArray,
        );
        echo json_encode($output);
    }
    function get_waiting_upcountry_charges(){
        $where = array();
        $finalArray = array();
        $postData = $this->input->post();
        $state = 0;
         $columnMappingArray = array("column_1"=>"bd.booking_id","column_2"=>"request_type","column_4"=>"services","column_5"=>"appliance_brand","column_6"=>"appliance_category","column_7"=>"appliance_capacity"
             ,"column_10"=>"bd.upcountry_distance","column_11"=>"bd.partner_upcountry_rate");    
         $order_by = "bd.booking_id";
         if($this->session->userdata('is_filter_applicable') == 1){
              $state = 1;
           }  
        if(array_key_exists("order", $postData)){
              $order_by = $columnMappingArray["column_".$postData['order'][0]['column']] ." ". $postData['order'][0]['dir'];
          }
         $partner_id = $this->session->userdata('partner_id');
         if($this->input->post('state')){
             $where['bd.state'] = $this->input->post('state');
         }
         if($this->input->post('booking_id')){
             $where['bd.booking_id'] = $this->input->post('booking_id');
         }
          $bookingCount = $this->upcountry_model->get_waiting_for_approval_upcountry_charges($partner_id,$state,1,$where)[0]['count'];
          $bookingData = $this->upcountry_model->get_waiting_for_approval_upcountry_charges($partner_id,$state,0,$where,$order_by,$postData['length'],$postData['start']);
           $sn = $postData['start'];
           foreach ($bookingData as $key => $row) {
                      $tempArray = array();
                      $tempString = "";
                      $sn++;
                      $tempArray[] = $sn;
                      $tempArray[] = '<a style="color:blue;" href='. base_url().'partner/booking_details/'.$row['booking_id'].'  title="View">'.$row['booking_id'].'</a>';
                      $tempArray[] = $row['request_type'];
                      $tempArray[] = $row['name'];
                      $tempArray[] = $row['services'];
                      $tempArray[] = $row['appliance_brand'];
                      $tempArray[] = $row['appliance_category'];
                      $tempArray[] = $row['appliance_capacity'];
                      $tempArray[] = $row['booking_address'] . ", " . $row['city'] . ", Pincode - " . $row['booking_pincode'] . ", " . $row['state'];
                      $age_requested = date_diff(date_create($row['upcountry_update_date']), date_create('today'));
                      $tempArray[] = $age_requested->days ." Days";
                      $tempArray[] = $row['upcountry_distance'] . " KM";
                      $tempArray[] = sprintf("%0.2f",$row['upcountry_distance'] * $row['partner_upcountry_rate']);
                      $tempString = '<div class="dropdown">
                                                <button class="btn btn-sm btn-primary" type="button" data-toggle="dropdown" style="border: 1px solid #2a3f54;background: #2a3f54;">Action
                                                <span class="caret"></span></button>
                                                <ul class="dropdown-menu" style="border: none;background: none;position: inherit;z-index: 100;min-width: 70px;">
                                                    <div class="action_holder" style="background: #fff;border: 1px solid #2c9d9c;padding: 1px;">
                                                    <li style="color: #fff;">
                                                        <a href='.base_url().'partner/upcountry_charges_approval/'.$row['booking_id'].'/1 class="btn btn-md btn-success" style="color:#fff;margin: 0px;padding: 5px 5.5px;">Approve</a></li>
                                                    <li style="color: #fff;margin-top:5px;">
                                                        <a style="color:#fff;margin: 0px;padding: 5px 11px;" href='.base_url().'partner/reject_upcountry_charges/'.$row['booking_id'].'/1 class="btn btn-md btn-danger">Reject</a>
                                                    </li>
                                           </div>
                                                </ul>
                                            </div>';
                      $tempArray[] = $tempString;
                      $finalArray[] = $tempArray;
             }
          $output = array(
              "draw" => $this->input->post('draw'),
              "recordsTotal" => $bookingCount,
              "recordsFiltered" =>  $bookingCount,
              "data" => $finalArray,
          );
          echo json_encode($output);
    }
    function get_review_booking_data(){
        $finalArray = array();
        $postData = $this->input->post();
        $columnMappingArray = array("column_2"=>"booking_details.request_type","column_3"=>"sc.cancellation_reason",
            "column_6"=>"booking_details.city", "column_7"=>"booking_details.state","column_8"=>"STR_TO_DATE(booking_details.initial_booking_date,'%d-%m-%Y')",
            "column_9"=>"DATEDIFF(CURDATE(),STR_TO_DATE(booking_details.initial_booking_date,'%d-%m-%Y'))");    
        $order_by = "ORDER BY booking_details.booking_id DESC";
        if(array_key_exists("order", $postData)){
               $order_by = "ORDER BY ".$columnMappingArray["column_".$postData['order'][0]['column']] ." ". $postData['order'][0]['dir'];
          }
         $partner_id = $this->session->userdata('partner_id');
         $statusData = $this->reusable_model->get_search_result_data("partners","partners.booking_review_for,partners.review_time_limit",array("booking_review_for IS NOT NULL"=>NULL,"id"=>$partner_id),NULL,NULL,NULL,NULL,NULL,array());
         $whereIN['booking_details.partner_id'] = array($partner_id);
         $where['DATEDIFF(CURRENT_TIMESTAMP,  sc.closed_date)<='.$statusData[0]['review_time_limit']] = NULL;
         if($this->input->post('booking_id')){
             $whereIN['booking_details.booking_id'] = array($this->input->post('booking_id'));
         }
           $bookingCount = $this->service_centers_model->get_admin_review_bookings(NULL,"Cancelled",$whereIN,1,-1,-1,$where,0,NULL,"COUNT(DISTINCT sc.booking_id) as count")[0]['count'];
           $bookingData = $this->service_centers_model->get_admin_review_bookings(NULL,"Cancelled",$whereIN,1,$postData['start'],$postData['length'],$where,1,$order_by);
           $sn = $postData['start'];
           foreach ($bookingData as $key => $row) {
                $tempArray = array();
                $tempString = $tempString2 = $tempString3 = $tempString4 = "";
                $sn++;
                if ($row['is_upcountry'] == 1) {
                      $tempString2 = '"'. $row['booking_id'].'"';
                      $tempString3 = '"'. $row['amount_due'].'"';
                      $tempString  ='<i style="color:red; font-size:20px;" onclick="open_upcountry_model('.$tempString2.'"," '.$tempString3.')"class="fa fa-road" aria-hidden="true"></i>';
                 }
                $tempArray[] = $sn.$tempString;
                $tempArray[] = '<a style="color:blue;" href='.base_url().'partner/booking_details/'.$row['booking_id'].' target="_blank" title="View">'.$row['booking_id'].'</a>';
                $tempString4 =  $row['request_type'];
                if (strpos($row['request_type'], 'Installation') !== false) {
                    $tempString4 =  "Installation";
                }
                else if(strpos($row['request_type'], 'Repair') !== false){
                    $tempString4 =  "Repair";
                }
                 $tempArray[] = $row['services']."</br>".$tempString4;
                 $tempArray[] = $row['cancellation_reason'];
                 $tempArray[] = $row['name'];
                 $tempArray[] = $row['booking_primary_contact_no'];
                 $tempArray[] = $row['city'];
                 $tempArray[] = $row['state'];
                 $tempArray[] = $row['booking_date'];
                 $tempArray[] = $row['age'];
                 $tempString5  = "'".$row['booking_id']."'";
                 $tempArray[] = '<input type="hidden" class="form-control" id="partner_id" name="partner_id['.$row['booking_id'].']" value = '.$row['partner_id'].'>
                                      <input id="approved_close" type="checkbox"  class="checkbox1" name="approved_booking[]" value="'.$row['booking_id'] .'">
                                      <input id="approved_by" type="hidden"   name="approved_by" value="'.$row['partner_id'].'>';
                 $tempArray[] = '<button style="min-width: 59px;" type="button" class="btn btn-primary btn-sm open-adminremarks" 
                                                                               data-toggle="modal" data-target="#myModal2" onclick="create_reject_form('.$tempString5.')">Reject</button>';
                $finalArray[] = $tempArray;
             }
          $output = array(
              "draw" => $this->input->post('draw'),
              "recordsTotal" => $bookingCount,
              "recordsFiltered" =>  $bookingCount,
              "data" => $finalArray,
          );
          echo json_encode($output);
    }
    function get_shipped_spare_waiting_for_confirmation(){
      $finalArray = array();
      $postData = $this->input->post();
      $state = 0;
      if($this->session->userdata('is_filter_applicable') == 1){
            $state = 1;
      }
      $columnMappingArray = array("column_1"=>"spare_parts_details.booking_id","column_3"=>"parts_shipped",
          "column_4"=>"courier_name_by_partner","column_5"=>"awb_by_partner","column_7"=>"shipped_date");    
     $order_by = "ORDER BY shipped_date DESC";
      if(array_key_exists("order", $postData)){
            $order_by = "ORDER BY ".$columnMappingArray["column_".$postData['order'][0]['column']] ." ". $postData['order'][0]['dir'];
        }
        $partner_id = $this->session->userdata('partner_id');
        //Parts Shipped by Partner But Did'nt Get by SF
        $where = "spare_parts_details.partner_id = '" . $partner_id . "'AND status IN ( '".SPARE_SHIPPED_BY_PARTNER."')  ";
       if($this->input->post('state')){
           $where = $where." AND booking_details.state = '".$this->input->post('state')."'";
       }
       if($this->input->post('booking_id')){
           $where = $where." AND spare_parts_details.booking_id = '".$this->input->post('booking_id')."'";
       }
       $bookingCount = $this->partner_model->get_spare_parts_booking_list($where, false, false, false,$state)[0]['total_rows'];
       $bookingData = $this->partner_model->get_spare_parts_booking_list($where, $postData['start'], $postData['length'], true,$state,NULL,FALSE,$order_by);
         $sn = $postData['start'];
         foreach ($bookingData as $key => $row) {
                    $tempArray = array();
                    $tempString = $tempString2 = $tempString3 = $tempString4 = $tempString5 = $tempString6 = $tempString7 = "";
                    $sn++;
                    $tempArray[] = $sn;
                    $tempArray[] = ' <a style="color:blue;"  href='.base_url().'partner/booking_details/'.$row['booking_id'].'  title="View">'.$row['booking_id'].'</a>';
                    $tempArray[] = $row['name'];
                    $tempArray[] = $row['parts_shipped'];
                    $tempArray[] = $row['courier_name_by_partner'];
                    $tempArray[] = $row['awb_by_partner'];
                    if(!empty($row['partner_challan_file'])) {
                          $tempString = '<a href="https://s3.amazonaws.com/'.BITBUCKET_DIRECTORY.'/vendor-partner-docs/'.$row['partner_challan_file'].' target="_blank">'.$row['partner_challan_number'].'</a>';
                    }
                    else if(!empty($row['partner_challan_number'])) {
                          $tempString =  $row['partner_challan_number'];
                    }
                    $tempArray[] = $tempString;
                    $tempArray[] = date("d-m-Y", strtotime($row['shipped_date']));
                    $tempArray[] = $row['remarks_by_partner'];
                    $finalArray[] = $tempArray;
           }
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $bookingCount,
            "recordsFiltered" =>  $bookingCount,
            "data" => $finalArray,
        );
        echo json_encode($output);
    }
    function get_sf_needs_to_send_spare(){
      $finalArray = array();
      $postData = $this->input->post();
      $state = 0;
        if($this->session->userdata('is_filter_applicable') == 1){
          $state = 1;
        }
      $columnMappingArray = array("column_1"=>"spare_parts_details.booking_id","column_3"=>"CONCAT('',GROUP_CONCAT((parts_shipped ) ))",
          "column_4"=>"courier_name_by_partner","column_5"=>"awb_by_partner","column_7"=>"DATEDIFF(CURDATE(),date(booking_details.service_center_closed_date))");    
      $order_by = "spare_parts_details.defective_part_shipped_date DESC";
      if(array_key_exists("order", $postData)){
            $order_by = $columnMappingArray["column_".$postData['order'][0]['column']] ." ". $postData['order'][0]['dir'];
        }
       $partner_id = $this->session->userdata('partner_id');
               $where = array(
            "spare_parts_details.defective_part_required" => 1,
            "spare_parts_details.partner_id" => $partner_id,
            "status IN ('" . DEFECTIVE_PARTS_PENDING . "', '".DEFECTIVE_PARTS_REJECTED."')  " => NULL
        );
       if($this->input->post('state')){
           $where['booking_details.state'] = $this->input->post('state');
       }
       if($this->input->post('booking_id')){
           $where['spare_parts_details.booking_id'] = $this->input->post('booking_id');
       }
        $select = "CONCAT( '', GROUP_CONCAT((parts_shipped ) ) , '' ) as defective_part_shipped, "
                . " spare_parts_details.booking_id, users.name,DATEDIFF(CURDATE(),date(booking_details.service_center_closed_date)) as aging,spare_parts_details.courier_name_by_partner, "
                . "spare_parts_details.awb_by_partner,spare_parts_details.partner_challan_number";
        $group_by = "spare_parts_details.booking_id";
        $bookingData = $this->service_centers_model->get_spare_parts_booking($where, $select, $group_by, $order_by, $postData['start'], $postData['length'],$state);
        $bookingCount =  $this->service_centers_model->count_spare_parts_booking($where, $select, $group_by,$state);
         $sn = $postData['start'];
         foreach ($bookingData as $key => $row) {
                    $tempArray = array();
                    $tempString = $tempString2 = $tempString3 = $tempString4 = $tempString5 = $tempString6 = $tempString7 = "";
                    $sn++;
                    $tempArray[] = $sn;
                    $tempArray[] = '<a  style="color:blue" href='.base_url().'partner/booking_details/'.$row['booking_id'].'  title="View">'.$row['booking_id'].'</a>';  
                    $tempArray[] = $row['name'];
                    $tempArray[] = $row['defective_part_shipped'];
                    $tempArray[] = $row['courier_name_by_partner'];
                    $tempArray[] = $row['awb_by_partner'];
                    if(!empty($row['partner_challan_file'])) {
                         $tempString ='<a href="https://s3.amazonaws.com/'.BITBUCKET_DIRECTORY.'/vendor-partner-docs/'.$row['partner_challan_file'].' target="_blank">'.$row['partner_challan_number'].'</a>';
                    }
                     else if(!empty($row['partner_challan_number'])) {
                          $tempString =  $row['partner_challan_number'];
                    }
                    $tempArray[] = $tempString;
                    $tempArray[] = $row['aging'];
                    $finalArray[] = $tempArray;
           }
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $bookingCount,
            "recordsFiltered" =>  $bookingCount,
            "data" => $finalArray,
        );
        echo json_encode($output);
    }
    function received_defactive_parts_by_partner(){
      $finalArray = array();
      $postData = $this->input->post();
      $state = 0;
        if($this->session->userdata('is_filter_applicable') == 1){
          $state = 1;
        }
      $columnMappingArray = array("column_1"=>"spare_parts_details.booking_id","column_3"=>"defective_part_shipped",
          "column_4"=>"received_defective_part_date","column_5"=>"awb_by_partner","column_6"=>"courier_name_by_partner");    
      $order_by = "ORDER BY spare_parts_details.defective_part_shipped_date DESC";
      if(array_key_exists("order", $postData)){
            $order_by = "ORDER BY ".$columnMappingArray["column_".$postData['order'][0]['column']] ." ". $postData['order'][0]['dir'];
        }
       $partner_id = $this->session->userdata('partner_id');
       $where = "spare_parts_details.partner_id = '" . $partner_id . "' AND approved_defective_parts_by_partner = '1' AND status != '"._247AROUND_CANCELLED."'";
       if($this->input->post('state')){
           $where =  $where.' AND booking_details.state = "' .$this->input->post('state').'"';
       }
       if($this->input->post('booking_id')){
           $where =  $where.' AND booking_details.booking_id = "' .$this->input->post('booking_id').'"';
       }
        $bookingData = $this->partner_model->get_spare_parts_booking_list($where, $postData['start'], $postData['length'], true,$state,NULL,FALSE,$order_by);
        $bookingCount =  $this->partner_model->get_spare_parts_booking_list($where, false, false, false,$state)[0]['total_rows'];
         $sn = $postData['start'];
         foreach ($bookingData as $key => $row) {
                    $tempArray = array();
                    $tempString = $tempString2 = $tempString3 = $tempString4 = $tempString5 = $tempString6 = $tempString7 = "";
                    $sn++;
                    $tempArray[] = $sn;
                    $tempArray[] = '<a  style="color:blue" href='.base_url().'partner/booking_details/'.$row['booking_id'].'  title="View">'.$row['booking_id'].'</a>';  
                    $tempArray[] = $row['name'];
                    $tempArray[] = $row['defective_part_shipped'];
                    if (!is_null($row['received_defective_part_date'])) {
                         $tempString2 =   date("d-m-Y", strtotime($row['received_defective_part_date']));
                    }
                    $tempArray[] = $tempString2;
                    $tempArray[] = $row['awb_by_partner'];
                    $tempArray[] = $row['courier_name_by_partner'];
                    if(!empty($row['partner_challan_file'])) {
                         $tempString ='<a href="https://s3.amazonaws.com/'.BITBUCKET_DIRECTORY.'/vendor-partner-docs/'.$row['partner_challan_file'].' target="_blank">'.$row['partner_challan_number'].'</a>';
                    }
                     else if(!empty($row['partner_challan_number'])) {
                          $tempString =  $row['partner_challan_number'];
                    }
                    $tempArray[] = $tempString;
                    $tempArray[] = $row['remarks_defective_part_by_sf'];
                    $finalArray[] = $tempArray;
           }
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $bookingCount,
            "recordsFiltered" =>  $bookingCount,
            "data" => $finalArray,
        );
        echo json_encode($output);
    }
     
    /**
     * @desc: This is used to show the partner contract list
     * @param void
     * @return void
     */
    function show_contract_list(){
        $select = 'partners.public_name, collateral.file, collateral_type.collateral_tag, collateral.document_description, collateral.start_date, collateral.end_date';
        $join['collateral'] = 'collateral.entity_id = partners.id AND collateral.entity_type = "partner" AND collateral.collateral_id = "7" AND start_date <= "'.date("Y-m-d").'" AND end_date >= "'.date("Y-m-d").'"';
        $join['collateral_type'] = 'collateral_type.id = collateral.collateral_id';
        
        $data['data'] = $this->partner_model->get_partner_contract_detail($select, null, $join, 'left');
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/show_contract_list', $data);
    }
    
 
    function update_spare_estimate_quote(){
        $response = $unit_response = $booking_response = FALSE;
        $booking_id = $this->input->post("booking_id");
        $vendor_id = $this->input->post("vendor_id");
        $amount_due = $this->input->post("amount_due");
        $spare_id = $this->input->post("spare_id");
        $updated_price = $this->input->post("updated_price");
        $partner_id = $this->input->post("partner_id");
        $agent_id = $this->input->post("agent_id");
        $booking_unit_id = $this->input->post("booking_unit_id");
        if($spare_id && $booking_unit_id && $booking_id && $updated_price && $vendor_id && $partner_id){
            //Update Spare Table
            $where = array('id' => $spare_id);
            $data['purchase_price'] = $updated_price;
            $data['sell_price'] = ($updated_price + $updated_price *SPARE_OOW_EST_MARGIN );
            $data['estimate_cost_given_date'] = date('Y-m-d');
            $response = $this->service_centers_model->update_spare_parts($where, $data);
            if ($response) {
                //Update Booking_unit_details_table
                $unit['vendor_basic_percentage'] = ($updated_price * REPAIR_OOW_VENDOR_PERCENTAGE)/$data['sell_price'];
                $unit['customer_total'] = $data['sell_price'];
                $unit['ud_update_date'] = date("Y-m-d H:i:s");
                $unit_where = array('id' => $booking_unit_id);
                $unit_response = $this->booking_model->update_booking_unit_details_by_any($unit_where,$unit);
            }
            if($unit_response){
                //Update Booking_details table
                $booking['amount_due'] = ($amount_due + $data['sell_price']);
                $booking_response = $this->booking_model->update_booking($booking_id, $booking);
            }
            if($booking_response){
                //Update Booking_History Table
                if($this->session->userdata('partner_id')){
                    $this->notify->insert_state_change($booking_id, SPARE_OOW_EST_UPDATED, SPARE_OOW_EST_GIVEN, "UPDATED Price - ".$updated_price, $agent_id, "", $actor,$next_action,$partner_id);
                }else if($this->session->userdata('service_center_id')){
                    $this->notify->insert_state_change($booking_id, SPARE_OOW_EST_UPDATED, SPARE_OOW_EST_GIVEN, "UPDATED Price - ".$updated_price, $agent_id, "", $actor,$next_action,NULL,$this->session->userdata('service_center_id'));
                } else {
                    $this->notify->insert_state_change($booking_id, SPARE_OOW_EST_UPDATED, SPARE_OOW_EST_GIVEN, "UPDATED Price - ".$updated_price, _247AROUND_DEFAULT_AGENT, "", $actor,$next_action, _247AROUND);
                }
            }
            if($response && $unit_response && $booking_response){
                //Update Job Card
                $this->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);
                //Send Price Updation Email
                $template = $this->booking_model->get_booking_email_template("oow_estimate_updated");
                if (!empty($template)) {
                    $to = "";
                    $am_data = $this->miscelleneous->get_am_data($partner_id);
                    if(!empty($am_data)){
                        $to = $am_data[0]['official_email'];
                    }
                    $rm_details = $this->vendor_model->get_rm_sf_relation_by_sf_id($vendor_id);
                    if(!empty($rm_details)){
                        $to = (!empty($to))? $to.", ".$rm_details[0]['official_email']: $rm_details[0]['official_email'];
                    }
                    if (!empty($to)) {
                        $to = $am_data[0]['official_email'];
                        $subject = vsprintf($template[4], $booking_id);
                        $emailBody = vsprintf($template[0], $updated_price);
                        $this->notify->sendEmail($template[2], $to, $template[3], '', $subject, $emailBody, "",'oow_estimate_updated');
                    }
                }
                return true;
            }
            else{
                return false;
            }
        }
    }
    
    /*
     * @desc - This function is used to save bank detail for partner
     * @param - form post
     * @retun - void
     */
    function process_add_bank_detail_details(){
        $check_file = '';
        $this->form_validation->set_rules('bank_name', 'bank_name', 'required|trim');
        $this->form_validation->set_rules('account_type','account_type', 'required|trim');
        $this->form_validation->set_rules('account_number', 'account_number','required|trim');
        $this->form_validation->set_rules('ifsc_code', 'ifsc_code', 'required|trim');
        $this->form_validation->set_rules('beneficiary_name', 'beneficiary_name','required|trim');
        if ($this->form_validation->run() == TRUE) { 
            //Processing cancelled check file
            if (($_FILES['cancelled_cheque_file']['error'] != 4) && !empty($_FILES['cancelled_cheque_file']['tmp_name'])) {
                $tmpFile = $_FILES['cancelled_cheque_file']['tmp_name'];
                $check_file = "Partner-" . preg_replace('/\s+/', '', strtolower($this->input->post('partner_id'))) . '-CANCELLED-CHECK' . "." . explode(".", $_FILES['cancelled_cheque_file']['name'])[1];
                move_uploaded_file($tmpFile, TMP_FOLDER . $check_file);

                //Upload files to AWS
                $bucket = BITBUCKET_DIRECTORY;
                $directory_xls = "vendor-partner-docs/" . $check_file;
                $this->s3->putObjectFile(TMP_FOLDER . $check_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                unlink(TMP_FOLDER . $check_file);

                //Logging success for file uppload
                log_message('info', __FUNCTION__ . ' CHECK FILE is being uploaded sucessfully.');
            } 
            $bank_data = array(
                'entity_id' => $this->input->post('partner_id'),
                'entity_type' => _247AROUND_PARTNER_STRING,
                'bank_name' => $this->input->post('bank_name'),
                'account_type' => $this->input->post('account_type'),
                'bank_account' => $this->input->post('account_number'),
                'ifsc_code' => $this->input->post('ifsc_code'),
                'cancelled_cheque_file' => $check_file,
                'beneficiary_name' => $this->input->post('beneficiary_name'),
                'agent_id' => $this->session->userdata('id'),
                'is_active' => '0'
            );
            if($this->input->post('BD_action') > 0 && $this->input->post('BD_action') != NULL){
                unset($bank_data['is_active']);
                if(!$check_file){
                    unset($bank_data['cancelled_cheque_file']);   
                }
                $action = $this->reusable_model->update_table('account_holders_bank_details', $bank_data, array('id'=>$this->input->post('BD_action')));
                $msg = "Data Updated Successfully";
            }
            else{
                $action = $this->reusable_model->insert_into_table('account_holders_bank_details', $bank_data);
                $msg = "Data Entered Successfully";
            }
            if($action){
                log_message("info", __METHOD__ .$msg);
                $this->session->set_userdata('success', 'Data Entered Successfully');
                redirect(base_url() . 'employee/partner/editpartner/' . $this->input->post('partner_id'));
            } else {
                log_message("info", __METHOD__ . " Error in adding details");
                $this->session->set_userdata('failed', 'Data can not be inserted. Please Try Again...');
                redirect(base_url() . 'employee/partner/editpartner/' . $this->input->post('partner_id'));
            }
        }else{
            $this->session->set_userdata('error', 'Please Fill All Bank Detail');
            redirect(base_url() . 'employee/partner/editpartner/' . $this->input->post('partner_id'));
        } 
    }
    
    /*
     * @desc - This function is used to save bank detail for partner
     * @param - form post
     * @retun - void
     */
    function process_add_annual_charges(){
            $partner_id = $this->input->post('partner_id');
            $data = array(
                'entity_type' => 'partner',
                'entity_id' => $partner_id,
                'charges_type' => 'annual-charges',
                'description' => 'Partner annual charges',
                'fixed_charges' => $this->input->post('annual_amount'),
                'validity_in_month' => $this->input->post('validity'),
                'hsn_code' => '123',
                'gst_rate' => '18'
            );
            $charge_exist = $this->invoices_model->get_variable_charge('id', array('entity_type' => 'partner', 'entity_id' => $partner_id));
            if(empty($charge_exist)){
                $data['create_date'] = date('Y-m-d H:i:s');
                $result = $this->invoices_model->insert_into_variable_charge($data);
            }
            else{
               $data['update_date'] = date('Y-m-d H:i:s');
               $result = $this->invoices_model->update_into_variable_charge(array('id'=>$charge_exist[0]['id']), $data); 
            }
            if($result){
                log_message("info", __METHOD__ .$msg);
                $this->session->set_userdata('success', 'Data Saved Successfully');
                redirect(base_url() . 'employee/partner/editpartner/' . $this->input->post('partner_id'));
            } else {
                log_message("info", __METHOD__ . " Error in Saving details");
                $this->session->set_userdata('failed', 'Data can not be inserted. Please Try Again...');
                redirect(base_url() . 'employee/partner/editpartner/' . $this->input->post('partner_id'));
            }
    }
    
     /*
     * @desc - This function is used to Active/Inactive bank detail for partner(only one bank detail active at a time)
     * @param - form post
     * @retun - void
     */
    function process_active_inactive_bank_detail(){
        if($this->input->post('is_active') == 0){
            if(!empty($this->input->post('partner_id'))){
                $this->reusable_model->update_table('account_holders_bank_details', array('is_active'=> 0), array('entity_id'=>$this->input->post('partner_id')));
                $update = $this->reusable_model->update_table('account_holders_bank_details', array('is_active'=> 1), array('id'=>$this->input->post('id')));  
            }
        }
        else{
            if(!empty($this->input->post('partner_id'))){
                $update = $this->reusable_model->update_table('account_holders_bank_details', array('is_active'=> 0), array('id'=>$this->input->post('id'))); 
            }
        }
        if($update){
            $this->session->set_userdata('success', 'Bank Data Updated Successfully');
            redirect(base_url() . 'employee/partner/editpartner/' . $this->input->post('partner_id'));
        }
        else{
            $this->session->set_userdata('failed', 'Data can not be updated. Please Try Again...');
            redirect(base_url() . 'employee/partner/editpartner/' . $this->input->post('partner_id'));
        }
    }
    /*
     * This function extracts channels list and partner name from database and loads it to the view in tabular format.
     */
    public function get_channels(){
        $select = 'partner_channel.*,partners.public_name';
        $fetch_data = $this->partner_model->get_channels($select);
        
        $this->miscelleneous->load_nav_header();
        $this->load->view("employee/get_channel_list", array('fetch_data' => $fetch_data));
    }
    
    public function get_partner_channel() {
         log_message('info', __FUNCTION__ . print_r($_POST, true));
        $select = 'partner_channel.id, partner_channel.channel_name';
        if(!empty($this->input->post('partner_id'))){ 
            $where = array(
                'partner_id = "'.$this->input->post('partner_id').'" OR is_default = 1'=>NULL
            );
            
        }
        else{
            $where = array('is_default' => 1);
        }
        
        $channel = $this->input->post('channel');
        $fetch_data = $this->partner_model->get_channels($select, $where);
        $html = '<option value="" selected disabled>Please select seller channel</option>';
        foreach ($fetch_data as $key => $value) {
           $html .= '<option ';
           if($channel ==$value['channel_name'] ){
               $html .= " selected ";
           }
           $html .=' >'.$value['channel_name'].'</option>'; 
        }
        echo $html;
    }
    
   /*
    * This function displays channel form in the browser.
    */
    public function add_channel(){ 
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/channel_form');
    }
    /*
     * This performs the process of adding channels to the form and submiting it to the database table.
     */
    public function process_add_channel(){
        $this->form_validation->set_rules('channel','Channel','required');
        $is_default = 0;
        if ($this->form_validation->run() == FALSE) {
            $this->add_channel();
        } else {
            $channel = $this->input->post("channel");
            if($this->input->post("partner_id") === 'All'){
                $partner_id = NULL;
                $is_default = 1;
            }
            else{
                $partner_id = $this->input->post("partner_id");
            }
            $data = array(
                'channel_name' => $channel,
                'partner_id' => $partner_id
                );
          
            $is_exist = $this->partner_model->get_channels('*', $data);
            
            if (empty($is_exist)) {
                $data['create_date'] = date('Y-m-d H:i:s');
                $data['is_default'] = $is_default;
                $channel_id = $this->partner_model->insert_new_channels($data);
                if ($channel_id){
                    $output = "Your data inserted successfully";
                    $userSession = array('success' => $output);
                    $this->session->set_userdata($userSession);
                    redirect(base_url() . "employee/partner/add_channel");
                }else{
                    $output = "Failed! Data did not insert";
                    $userSession = array('error' => $output);
                    $this->session->set_userdata($userSession);
                    redirect(base_url() . "employee/partner/add_channel");
                }
            }
            else {
                $output = "This Data already exist";
                $userSession = array('error' => $output);
                $this->session->set_userdata($userSession);
                redirect(base_url() . "employee/partner/add_channel");
            }
    }
}
/*
 * This function loads the tabular format of the view of update channel form 
 */
function update_channel($id) {
        $data = array(
            'partner_channel.id' => $id
        );
        
        $channel['fetch_data'] = $this->partner_model->get_channels(' partner_channel.* ', $data);
        //print_r($channel); die();
      
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/update_channel', $channel);
        
       
    }
    /*
     * This function supports in performing update functionalties to the form and further submiting it to the database. 
     */
    function process_update_channel($id) {
        $this->form_validation->set_rules('channel', 'Channel', 'required');
        $is_default = 0;
        if ($this->form_validation->run() == FALSE) {
            $this->update_channel($id);
        } else {
            $channel = $this->input->post("channel");
            if($this->input->post("partner_id") === 'All'){
                $partner_id = NULL;
                $is_default = 1;
            }
            else{
                $partner_id = $this->input->post("partner_id");
            }
            $data = array(
                'channel_name' => $channel,
                'partner_id' => $partner_id
            );
            $is_exist = $this->partner_model->get_channels('partner_channel.id', $data);
            if (empty($is_exist)) {
                $data['update_date'] = date('Y-m-d H:i:s');
                $data['is_default'] = $is_default;
                $status = $this->partner_model->update_channel($id, $data);
                if ($status) {
                    $output = "Your data updated successfully";
                    $userSession = array('success' => $output);
                    $this->session->set_userdata($userSession);
                    redirect(base_url() . "employee/partner/update_channel/" . $id);
                } else {
                    $output = "Failed! Data did not update";
                    $userSession = array('error' => $output);
                    $this->session->set_userdata($userSession);
                    redirect(base_url() . "employee/partner/update_channel/" . $id);
                }
            }  else {
                    $output = "This Data already exist";
                    $userSession = array('error' => $output);
                    $this->session->set_userdata($userSession);
                    redirect(base_url() . "employee/partner/update_channel/" . $id);
                }
            }
        }
        /**
     *  @desc : This function is to create Repeat booking
     *  We have already made a function to get_edit_booking_form, this method use that function to insert booking by parent booking
     *  @param : Parent booking ID
     */
    function get_repeat_booking_form($booking_id) {
         log_message('info', __FUNCTION__ . " Booking ID  " . print_r($booking_id, true));
        $openBookings = $this->reusable_model->get_search_result_data("booking_details","booking_id",array("parent_booking"=>$booking_id),NULL,NULL,NULL,NULL,NULL,array());
        if(empty($openBookings)){
            $this->get_editbooking_form($booking_id,"Repeat");
        }
        else{
            echo "<p style= 'text-align: center;background: #f35b5b;color: white;font-size: 20px;'>There is an open Repeat booking (".$openBookings[0]['booking_id'].") for ".$booking_id." , Untill repeat booking is not closed you can not create new repeat booking</p>";
        }
    }
    function get_booking_relatives($booking_id){
        $relativeData = $this->booking_model->get_parent_child_sibling_bookings($booking_id);
        if(!empty($relativeData)){
            echo  json_encode($relativeData[0]);
        }
        else{
            echo false;
        }
    }
 
     /*
     * @desc - This function is used to get the list of service centers by state.
     * @param - get post state val
     * @retun - Json
     */
       
     function get_state_waise_service_centers() { 
         $state = $this->input->post("state");
         if (!empty($state)) {
             $where = array('state' => $state);
             $select = "service_centres.id,service_centres.name,service_centres.state";
             $service_centres_list = $this->vendor_model->getVendorDetails($select, $where, 'state', '');
             if (!empty($service_centres_list)) {
                 echo json_encode($service_centres_list);
             } else {
                 echo json_encode(array('status' => 'fail'));

             }

         }
     
    }
    /*
     * @desc - This function is used to get partner service center where partner serivce valum high.
     * @param - get post multiple parameter
     * @render on same pages
     */    
    function process_partner_warehouse_config(){ 
        
        $is_wh = $this->input->post('is_wh');
        $partner_id = $this->input->post('partner_id');
        $micro = $this->input->post('micro');
        $is_micro_wh = $this->input->post('is_micro_wh');
        $is_defective_part_return_wh = $this->input->post('is_defective_part_return_wh');         
              
        if($is_micro_wh == 1){
           
            foreach($micro as $key =>  $value){
                $data = array();
                $wh_on_of_data =array();
                
                $data =array(
                    'partner_id'=>$partner_id,
                    'state'=>$value['micro_wh_state'],
                     'micro_warehouse_charges'=>$value['sf_amount']
                );
                
                $wh_on_of_data =array(
                    'partner_id'=>$partner_id,
                    'agent_id'=> $this->session->userdata('id'),
                    'active'=>1
                 ); 
                
                foreach($value['sf_id'] as $vendor_id){
                    $data['vendor_id'] = $vendor_id;
                    $wh_on_of_data['vendor_id'] = $vendor_id;  
                    $micro_wh_mapping_list = $this->inventory_model->get_micro_wh_mapping_list(array('micro_warehouse_state_mapping.vendor_id'=>$vendor_id), '*');
                    if(empty($micro_wh_mapping_list)){
                        
                        $last_inserted_id = $this->inventory_model->insert_query('micro_warehouse_state_mapping',$data);  
                        $inserted_id = $this->inventory_model->insert_query('warehouse_on_of_status',$wh_on_of_data);
                        $service_center = array('is_micro_wh'=>1);
                        $this->vendor_model->edit_vendor($service_center, $vendor_id); 
                    }                            
                }    
            }            
            $partner = array(               
               'is_micro_wh'=>1                            
            );
          $this->partner_model->edit_partner($partner,  $partner_id);             
        }       
             
        if($is_wh==1){          
           $partner = array(
               'is_wh'=>1,
               'is_defective_part_return_wh'=>$is_defective_part_return_wh               
           );
          $this->partner_model->edit_partner($partner,  $partner_id);           
        }elseif ($is_wh==1 && $is_micro_wh == 1) {           
           $partner = array(
               'is_wh'=>1,
               'is_micro_wh'=>1,
               'is_defective_part_return_wh'=>$is_defective_part_return_wh
           );
          $this->partner_model->edit_partner($partner,  $partner_id);  
        }
       
         redirect(base_url() . 'employee/partner/editpartner/' . $partner_id);              
    }
    
    /*
     * @desc - This function is used to remove service center from partner valum area.
     * @param -  get id
     * @render on same pages
     */ 
    
    function manage_micro_warehouse_by_status() {
        $micro_wh_mp_id = $this->input->post('micro_wh_mp_id');
        $wh_on_of_id = $this->input->post('wh_on_of_id');
        $active_status = $this->input->post('active_status');        
        if (!empty($micro_wh_mp_id)) {
            $return_type = $this->inventory_model->manage_micro_wh_from_list_by_id($micro_wh_mp_id, $active_status);
            if (!empty($return_type)) {
                $where = array('m.id' => $wh_on_of_id);
                $warehouse_on_off_list = $this->inventory_model->get_warehouse_on_of_status_list($where, 'w_on_off.partner_id,w_on_off.vendor_id');
                if (!empty($warehouse_on_off_list)) {                   
                    $wh_on_of_data = $warehouse_on_off_list[0];
                    $wh_on_of_data['active'] = $active_status; 
                    $wh_on_of_data['agent_id'] = $this->session->userdata('id');
                    $inserted_id = $this->inventory_model->insert_query('warehouse_on_of_status', $wh_on_of_data);
                    if (!empty($inserted_id)) {
                        echo json_encode(array('status' => 'success'));
                    } else {
                        echo json_encode(array('status' => 'failed'));
                    }
                }
            }
        }
    }

}
