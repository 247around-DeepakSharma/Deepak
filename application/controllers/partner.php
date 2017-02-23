<?php

if (!defined('BASEPATH')){
    exit('No direct script access allowed');
}

//error_reporting(E_ERROR);
//ini_set('display_errors', '1');

/**
 * REST APIs for Partners like Snapdeal to insert / update orders in our CRM
 *
 * @author anujaggarwal
 */
define('SUCCESS_CODE', 247);
define('SUCCESS_MSG', 'Success');
define('ERR_BOOKING_NOT_INSERTED', -24700);
define('ERR_GENERIC_ERROR_CODE', -1000);
define('ERR_INVALID_AUTH_TOKEN_CODE', -1001);
define('ERR_MOBILE_NUM_MISSING_CODE', -1002);
define('ERR_ORDER_ID_EXISTS_CODE', -1003);
define('ERR_MANDATORY_PARAMETER_MISSING_CODE', -1004);
define('ERR_INVALID_PRODUCT_CODE', -1005);
define('ERR_INVALID_REQUEST_TYPE_CODE', -1006);
//define('ERR_ORDER_ID_NOT_FOUND_CODE', -1007);
define('ERR_INVALID_BOOKING_ID_CODE', -1008);
define('ERR_REQUEST_ALREADY_COMPLETED_CODE', -1009);
define('ERR_REQUEST_ALREADY_CANCELLED_CODE', -1010);
define('ERR_REQUEST_BEYOND_CUTOFF_TIME_CODE', -1011);
define('ERR_INVALID_DATE_FORMAT_CODE', -1012);
define('ERR_INVALID_TIMESLOT_FORMAT_CODE', -1013);
define('ERR_INVALID_INSTALLATION_TIMESLOT_CODE', -1014);
define('ERR_INVALID_PARTNER_NAME_CODE', -1015);
define('ERR_INVALID_JSON_INPUT_CODE', -1016);
define('ERR_INVALID_PRODUCT_TYPE_CODE', -1017);

define('ERR_BOOKING_NOT_INSERTED_MSG', 'Booking Insertion Failed');
define('ERR_GENERIC_ERROR_MSG', 'Unknown Error');
define('ERR_INVALID_AUTH_TOKEN_MSG', 'Invalid Auth Token');
define('ERR_MOBILE_NUM_MISSING_MSG', 'Mobile Number Missing');
define('ERR_ORDER_ID_EXISTS_MSG', 'Order ID Exists');
define('ERR_MANDATORY_PARAMETER_MISSING_MSG', 'Mandatory Parameter is Missing');
define('ERR_INVALID_PRODUCT_MSG', 'Invalid Product');
define('ERR_INVALID_REQUEST_TYPE_MSG', 'Invalid Request Type');
//define('ERR_ORDER_ID_NOT_FOUND_MSG', 'Order ID Not Found');
define('ERR_INVALID_BOOKING_ID_MSG', 'Invalid Booking ID');
define('ERR_REQUEST_ALREADY_COMPLETED_MSG', 'Request is Already Completed');
define('ERR_REQUEST_ALREADY_CANCELLED_MSG', 'Request is Already Cancelled');
define('ERR_REQUEST_BEYOND_CUTOFF_TIME_MSG', 'Request Beyond Cutoff Time');
define('ERR_INVALID_DATE_FORMAT_MSG', 'Invalid Date Format');
define('ERR_INVALID_TIMESLOT_FORMAT_MSG', 'Invalid Timeslot Format');
define('ERR_INVALID_INSTALLATION_TIMESLOT_MSG', 'Invalid Installation Timeslot');
define('ERR_INVALID_PARTNER_NAME_MSG', 'Invalid Partner Name');
define('ERR_INVALID_JSON_INPUT_MSG', 'Invalid JSON Input');
define('ERR_INVALID_PRODUCT_TYPE_MSG', 'DENIED BY VENDOR');



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
        $this->load->model('service_centers_model');
        $this->load->library("miscelleneous");
        $this->load->library('email');
        $this->load->library('notify');
        $this->load->library('partner_utilities');
        $this->load->model('upcountry_model');
        $this->load->library("asynchronous_lib");
        $this->load->library('booking_utilities');
        $this->load->helper(array('form', 'url'));
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
           
            if ($this->partner !== FALSE) {
                log_message('info', __METHOD__ . ":: Token validated (Partner ID: " . $this->partner['id'] . ")");

                //Token validated
                $input_d = file_get_contents('php://input');
                $requestData = json_decode($input_d, TRUE);
                
                if(!empty($requestData['brand'])){
                    //Sanitizing Brands Before Adding
                    $requestData['brand'] = preg_replace('/[^A-Za-z0-9 ]/', '', $requestData['brand']);
                }

                if (!(json_last_error() === JSON_ERROR_NONE)) {
                    log_message('info', __METHOD__ . ":: Invalid JSON");

                    //Invalid json
                    $this->jsonRequestData = $input_d;
                    $this->sendJsonResponse(array(ERR_INVALID_JSON_INPUT_CODE, ERR_INVALID_JSON_INPUT_MSG));
                } else {
                    $this->jsonRequestData = $input_d;
                    //Check whether the required details are present in the request
                    //And request doesn't exist in database
                    $is_valid = $this->validate_submit_request_data($requestData);
                    if ($is_valid['result'] == TRUE) {
                        log_message('info', __METHOD__ . ":: Request validated");

                        //Search for user
                        //Insert user if phone number doesn't exist
                        $output = $this->user_model->search_user($requestData['mobile']);
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
                        
                        if(!isset($is_productType_valid['error'])){                         
                        log_message('info', 'Product type: ' . $requestData['product']);
                        $prod = trim($requestData['product']);
                        
                        //check service_id exist or not
                        $service_appliance_data = $this->booking_model->get_service_id_by_appliance_details($requestData['productType']);
                        
                        if(!empty($service_appliance_data)){
                            log_message('info', __FUNCTION__ . " Get Service ID  ");
        
                            $service_id = $service_appliance_data[0]['service_id'];
                            $lead_details['service_appliance_data'] = $service_appliance_data[0];
                            $lead_details['Product'] = $this->booking_model->selectservicebyid($service_id)[0]['services'];
                        }
                        else{
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
                        
                        
                        //Assigning Booking Source and Partner ID for Brand Requested
                        
                        // First we send Service id and Brand and get Partner_id from it
                        // Now we send state, partner_id and service_id 
                        $data = $this->_allot_source_partner_id_for_pincode($service_id,$distict_details['state'],$requestData['brand']);
                        
                        $booking['partner_id'] = $data['partner_id'];
                        $booking['source'] = $data['source'];
                        
                        $unit_details['partner_id'] = $booking['partner_id'];
                        $booking['order_id'] = $requestData['orderID'];
                        $appliance_details['brand'] = $unit_details['appliance_brand'] = $requestData['brand'];

                        $appliance_details['model_number'] = $unit_details['model_number'] = (isset($requestData['model']) ? $requestData['model'] : "");

                        //Product description
                        $appliance_details['description'] = $unit_details['appliance_description'] = $requestData['productType'];
                        //Insert cateogry for Geyser
                        if($service_id == '32'){
                            //Check for all optional parameters before setting them
                            $unit_details['appliance_category'] = $appliance_details['category'] =  'Geyser-PAID';

                        } else {
                            //Check for all optional parameters before setting them
                            $appliance_details['category'] = $unit_details['appliance_category'] = isset($lead_details['service_appliance_data']['category'])?$lead_details['service_appliance_data']['category']:isset($requestData['category']) ? $requestData['category']:'';
                       
                        }
                        
                        $appliance_details['capacity'] = $unit_details['appliance_capacity'] =isset($lead_details['service_appliance_data']['capacity'])?$lead_details['service_appliance_data']['capacity']:isset($requestData['capacity']) ? $requestData['capacity'] : '';
                        //get partner data to check the price
                        $partner_data = $this->partner_model->get_partner_code($booking['partner_id']);
                        $partner_mapping_id = $partner_data[0]['price_mapping_id'];
                        if($partner_data[0]['partner_type'] == OEM){
                            $prices = $this->partner_model->getPrices($service_id, $unit_details['appliance_category'], $unit_details['appliance_capacity'], $partner_mapping_id,'Installation & Demo',$unit_details['appliance_brand']);
                        } else {
                            $prices = $this->partner_model->getPrices($service_id, $unit_details['appliance_category'], $unit_details['appliance_capacity'], $partner_mapping_id,'Installation & Demo',"");
                        }
                        $booking['amount_due'] = '0';
                        $is_price = false;
                        if(!empty($prices)){
                            $unit_details['id'] =  $prices[0]['id'];
                            $unit_details['around_paid_basic_charges'] =  $unit_details['around_net_payable'] = "0.00";
                            $unit_details['partner_paid_basic_charges'] = $prices[0]['partner_net_payable'];
                            $unit_details['partner_net_payable'] = $prices[0]['partner_net_payable'];
                            $booking['amount_due'] = $prices[0]['customer_net_payable'];
                            $is_price['customer_net_payable'] = $prices[0]['customer_net_payable'];
                            $is_price['is_upcountry'] = $prices[0]['is_upcountry'];
                        }
                        
                        //$lead_details['SubCategory'] = (isset($requestData['subCategory']) ? $requestData['subCategory'] : "");
                        
                        $booking['booking_primary_contact_no'] = $requestData['mobile'];
                        $booking['booking_alternate_contact_no'] = (isset($requestData['alternatePhone']) ? $requestData['alternatePhone'] : "");

                        $booking['city'] = $requestData['city'];
                        $booking['booking_pincode'] = $requestData['pincode'];
			
                        $booking['booking_address'] = $requestData['address'] . ", " . (isset($requestData['landmark']) ? $requestData['landmark'] : "");
                        $booking['delivery_date'] = $this->getDateTime($requestData['deliveryDate']);
                        $booking['request_type'] = $requestData['requestType'];
                        $booking['query_remarks'] = (isset($requestData['remarks']) ? $requestData['remarks'] : "");


                        //Add this as a Query now
                        $booking['booking_id'] = '';
                        $appliance_details['user_id'] = $booking['user_id'] = $user_id;
                        $appliance_details['service_id'] = $unit_details['service_id'] = $booking['service_id'] = $service_id;
                        log_message('info', __METHOD__ . ":: Service ID: " . $booking['service_id']);
                        //echo "Service ID: " . $booking['service_id'] . PHP_EOL;
                        $random_code=  mt_rand(100, 999);   //return 3 digit random code to make booking id unique
                        $yy = date("y");
                        $mm = date("m");
                        $dd = date("d");
                        $booking['booking_id'] = str_pad($booking['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
                        $booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($booking['user_id'])) + 1);

                        $unit_details['booking_id'] = $booking['booking_id'] = "Q-" . $booking['source'] . "-" . $booking['booking_id'].$random_code;

                        $booking['quantity'] = '1';
                        
                        $appliance_details['tag'] = $appliance_details['brand'] . " " . $lead_details['Product'];
                        $appliance_details['purchase_month'] = $unit_details['purchase_month'] = date('M');
                        $appliance_details['purchase_year'] = $unit_details['purchase_year'] = date('Y');

                        $appliance_details['last_service_date'] = date('Y-m-d');
                        $booking['potential_value'] = '';
                        //Check vendor Availabilty for pincode and service id
                        $is_sms = $this->check_upcountry($booking, $lead_details['Product'], $is_price, $unit_details['appliance_category'], $partner_data);
                        if(!$is_sms){
                             $booking['internal_status'] = SF_UNAVAILABLE_SMS_NOT_SENT;
                        }
                            
                        $booking['current_status'] = "FollowUp";
                        $booking['type'] = "Query";
                        $booking['booking_date'] = '';
                        $booking['booking_timeslot'] = '';
                        $booking['partner_source'] = 'STS';
                        $booking['amount_due'] = '';
                        $booking['booking_remarks'] = '';
                        $booking['state'] = $distict_details['state'];
                        $booking['district'] = $distict_details['district'];
                        $booking['taluk'] = $distict_details['taluk'];
                        $unit_details['booking_status'] = "FollowUp";
                        
                        //check partner status from partner_booking_status_mapping table 
                        $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'],$booking['partner_id'], $booking['booking_id']);
                        if(!empty($partner_status)){
                            $booking['partner_current_status'] = $partner_status[0];
                            $booking['partner_internal_status'] = $partner_status[1];
                        }
                        
                        //Insert query
//                        echo print_r($booking, true) . "<br><br>";
//                        echo print_r($appliance_details, true) . "<br><br>";exit();
                        $this->booking_model->addbooking($booking);
                        
                        //echo print_r($booking, true) . "<br><br>";
                        $unit_details['appliance_id'] = $this->booking_model->addappliance($appliance_details);
                        
                        if(!empty($prices)){
                            $this->booking_model->insert_data_in_booking_unit_details($unit_details, $booking['state']);
                        }else{
                            $this->booking_model->addunitdetails($unit_details);
                        }

                        $this->notify->insert_state_change($booking['booking_id'], _247AROUND_FOLLOWUP , _247AROUND_NEW_QUERY , $booking['query_remarks'], DEFAULT_PARTNER_AGENT, $requestData['partnerName'], $booking['partner_id']);
                        
                        if (empty($booking['state'])) {
			    $to = NITS_ANUJ_EMAIL_ID;
			    $message = "Pincode " . $booking['booking_pincode'] . " not found for Booking ID: " . $booking['booking_id'];
			    $this->notify->sendEmail("booking@247around.com", $to, "", "", 'Pincode Not Found', $message, "");
			}
                        
//                        //Check vendor Availabilty for pincode and service id
//                        $vendors = $this->vendor_model->check_vendor_availability($booking['booking_pincode'], $booking['service_id']);
//                        $vendors_count = count($vendors);
//
//                        if ($vendors_count > 0) {
//                            $this->send_sms_to_snapdeal_customer($lead_details['Product'],
//                            $booking['booking_primary_contact_no'], $user_id,
//                            $booking['booking_id'], $unit_details['appliance_category']);
//                        } else { //if ($vendors_count > 0) {
//                            log_message('info', __FUNCTION__ . ' =>  SMS not sent because of Vendor Unavailability for Booking ID: ' . $booking['booking_id']);
//                        }
//                        
                        //Send response
                        $this->jsonResponseString['response'] = array(
                            "orderID" => $booking['order_id'],
                            "247aroundBookingID" => $booking['booking_id'],
                            "247aroundBookingStatus" => $booking['current_status']);
                        $this->sendJsonResponse(array(SUCCESS_CODE, SUCCESS_MSG));
                        }else{
                            log_message('info', __METHOD__ . ":: Request validation fails for product type. " . print_r($requestData['productType'], true));
                        
                            $this->jsonResponseString['response'] = NULL;
                            $this->sendJsonResponse(array(ERR_INVALID_PRODUCT_TYPE_CODE,ERR_INVALID_PRODUCT_TYPE_MSG));
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
            } else {
                log_message('info', __METHOD__ . ":: Invalid token: " . $this->token);

                //invalid token
                $this->jsonResponseString['response'] = NULL;
                $this->sendJsonResponse(array(ERR_INVALID_AUTH_TOKEN_CODE, ERR_INVALID_AUTH_TOKEN_MSG));
            }
        }
    }
    /**
     * @desc: This method is used to send sms to customer while booking insert from STS
     * @param String $appliance
     * @param String $phone_number
     * @param String $user_id
     * @param String $booking_id
     */
    function send_sms_to_snapdeal_customer($appliance, $phone_number, $user_id, $booking_id, $category,$price) {

        $sms['tag'] = "sd_delivered_missed_call_initial";

        //ordering of smsData is important, it should be as per the %s in the SMS
        $sms['smsData']['service'] = $appliance;
        $sms['smsData']['missed_call_number'] = SNAPDEAL_MISSED_CALLED_NUMBER;
        /* If price exist then send sms according to that otherwise
         *  send sms by checking function get_product_free_not
         */
        if($price){
            $sms['smsData']['message'] = $price;
        }else{
            $sms['smsData']['message'] = $this->notify->get_product_free_not($appliance, $category);
        }
	$sms['phone_no'] = $phone_number;
	$sms['booking_id'] = $booking_id;
	$sms['type'] = "user";
	$sms['type_id'] = $user_id;

	$this->notify->send_sms_acl($sms);
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
            if ($this->partner !== FALSE) {
                //Token validated
                $input_d = file_get_contents('php://input');
                $requestData = json_decode($input_d, TRUE);

                if (!(json_last_error() === JSON_ERROR_NONE)) {
                    log_message('info', __METHOD__ . ":: Invalid JSON");

                    //invalid json
                    $this->jsonRequestData = $input_d;
                    $this->sendJsonResponse(array(ERR_INVALID_JSON_INPUT_CODE, ERR_INVALID_JSON_INPUT_MSG));
                } else {
                    $this->jsonRequestData = $input_d;

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
            } else {
                log_message('info', __METHOD__ . ":: Invalid token: " . $this->token);

                //invalid token
                $this->sendJsonResponse(array(ERR_INVALID_AUTH_TOKEN_CODE, ERR_INVALID_AUTH_TOKEN_MSG));
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
            if ($this->partner !== FALSE) {
                //Token validated
                $input_d = file_get_contents('php://input');
                $requestData = json_decode($input_d, TRUE);

                if (!(json_last_error() === JSON_ERROR_NONE)) {
                    log_message('info', __METHOD__ . ":: Invalid JSON");

                    //invalid json
                    $this->jsonRequestData = $input_d;
                    $this->sendJsonResponse(array(ERR_INVALID_JSON_INPUT_CODE, ERR_INVALID_JSON_INPUT_MSG));
                } else {
                    $this->jsonRequestData = $input_d;

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
            } else {
                log_message('info', __METHOD__ . ":: Invalid token: " . $this->token);

                //invalid token
                $this->sendJsonResponse(array(ERR_INVALID_AUTH_TOKEN_CODE, ERR_INVALID_AUTH_TOKEN_MSG));
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
            if ($this->partner !== FALSE) {
                //Token validated
                $input_d = file_get_contents('php://input');
                $requestData = json_decode($input_d, TRUE);

                if (!(json_last_error() === JSON_ERROR_NONE)) {
                    log_message('info', __METHOD__ . ":: Invalid JSON");

                    //invalid json
                    $this->jsonRequestData = $input_d;
                    $this->sendJsonResponse(array(ERR_INVALID_JSON_INPUT_CODE, ERR_INVALID_JSON_INPUT_MSG));
                } else {
                    $this->jsonRequestData = $input_d;

                    //1. Validate request - check all essential parameters are there
                    //2. Check order id and booking id corresponds to this partner and
                    //both are valid
                    $is_valid = $this->validate_get_request_data($requestData);
                    if ($is_valid['result'] == TRUE) {
                        //Send response
                        $lead_details = $is_valid['lead'];
                        $this->jsonResponseString['response'] = array(
                            "247aroundBookingID" => $lead_details['booking_id'],
                            "247aroundBookingStatus" => $lead_details['current_status'],
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
            if ($this->partner !== FALSE) {
                //Token validated
                $input_d = file_get_contents('php://input');
                $requestData = json_decode($input_d, TRUE);

                if (!(json_last_error() === JSON_ERROR_NONE)) {
                    log_message('info', __METHOD__ . ":: Invalid JSON");

                    //invalid json
                    $this->jsonRequestData = $input_d;
                    $this->sendJsonResponse(array(ERR_INVALID_JSON_INPUT_CODE, ERR_INVALID_JSON_INPUT_MSG));
                } else {
                    $this->jsonRequestData = $input_d;

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
            } else {
                log_message('info', __METHOD__ . ":: Invalid token: " . $this->token);

                //invalid token
                $this->sendJsonResponse(array(ERR_INVALID_AUTH_TOKEN_CODE, ERR_INVALID_AUTH_TOKEN_MSG));
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
        log_message('info', "Entering: " . __METHOD__ );

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
	    "Geyser");
	if (($flag === TRUE) &&
                (in_array($request['product'], $valid_products) == FALSE)) {
            //Do not return error as of now, just log this.
            log_message('info', $request['product'] . ': Invalid product type');

            //$resultArr['code'] = ERR_INVALID_PRODUCT_CODE;
            //$resultArr['msg'] = ERR_INVALID_PRODUCT_MSG;
            //$flag = FALSE;
        }

        //Check for Request type
        $valid_request_types = array("Installation", "Demo", "Installation and Demo");
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
                if ($lead['booking_id'] != $request['247aroundBookingID']) {
		    log_message('info', "Lead details: " . print_r($lead, true));

		    $resultArr['code'] = ERR_INVALID_BOOKING_ID_CODE;
                    $resultArr['msg'] = ERR_INVALID_BOOKING_ID_MSG;

                    $flag = FALSE;
                } else {
                    //Everything fine, return lead information
                    $resultArr['lead'] = $lead;
                }
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
            $unit_where = array('booking_id'=> $request['247aroundBookingID']);
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
        $booking['internal_status'] = $request['cancellationReason'];
        $booking['cancellation_reason'] = $details['cancellation_reason'] = "Other : " . $request['cancellationReason'];
        $booking['update_date'] = $booking['closed_date'] = date("Y-m-d H:i:s");
        
        //check partner status from partner_booking_status_mapping table  
        $partner_id=$request['id'];
             $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'],$partner_id, $booking_id);
                if(!empty($partner_status)){
                    $booking['partner_current_status'] = $partner_status[0];
                    $booking['partner_internal_status'] = $partner_status[1];
                }
        $this->booking_model->update_booking($booking_id, $booking);
        $this->booking_model->update_booking_unit_details($booking_id, $unit_details);

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

    function getallheaders() {
        //Use this if you are using Nginx

        $headers = '';
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
        if ($this->partner !== FALSE) {
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
            $output = $this->user_model->search_user($requestData['mobile']);
            $distict_details = $this->vendor_model->get_distict_details_from_india_pincode(trim($requestData['pincode']));
	
            $user['name'] = $requestData['name'];
            $user['phone_number'] = $requestData['mobile'];
            $user['alternate_phone_number'] = (isset($requestData['alternate_phone_number']) ? $requestData['alternate_phone_number'] : "");
            $user['user_email'] = (isset($requestData['email']) ? $requestData['email'] : "");
            if(isset($requestData['landmark']) && (!empty($requestData['landmark']))){
                $user['home_address'] = $requestData['address'].", " . $requestData['landmark'];
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
            $unit_details['appliance_brand'] = $appliance_details['brand'] =  $requestData['brand'];
            $appliance_details['model_number'] = $unit_details['model_number'] = (!empty($requestData['model']) ? $requestData['model'] : "");

            $booking['service_id'] = $requestData['service_id'];

            //Product description
            $unit_details['appliance_description'] = $appliance_details['description'] = $requestData['productType'];
            //Check for all optional parameters before setting them
            $unit_details['appliance_category'] = $appliance_details['category'] =  (isset($requestData['category']) ? $requestData['category'] : "");


            $unit_details['appliance_capacity'] = $appliance_details['capacity'] =  (isset($requestData['capacity']) ? $requestData['capacity'] : "");

            $booking['booking_primary_contact_no'] = $requestData['mobile'];
            $lead_details['booking_alternate_contact_no'] = (isset($requestData['alternate_phone_number']) ? $requestData['alternate_phone_number'] : "");
            $booking['booking_landmark'] = $requestData['landmark'];
            $booking['booking_pincode'] = $requestData['pincode'];
            $booking['city'] = $requestData['city'];

            $booking['request_type'] = $requestData['requestType'];
            $unit_details['price_tags'] = $requestData['requestType'];
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
            $partner_data = $this->partner_model->get_partner_code($this->partner['id']);
            $booking['source'] = $partner_data[0]['code'];
            $booking['booking_id'] = $booking['source'] . "-" . $booking['booking_id'];
            $unit_details['booking_id'] = $booking['booking_id'];
            
            $appliance_details['purchase_month'] = $unit_details['purchase_month'] = $requestData['purchase_month'];
            $appliance_details['purchase_year'] = $unit_details['purchase_year'] = $requestData['purchase_year'];

            $booking['quantity'] = '1';

            $appliance_details['serial_number'] = $unit_details['partner_serial_number'] = $requestData['serial_number'];

            $booking['potential_value'] = '';
            $appliance_details['last_service_date'] = date('d-m-Y');
            $booking['current_status'] = "Pending";
            $booking['internal_status'] = "Scheduled";
            $booking['type'] = "Booking";
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

            $partner_mapping_id = $partner_data[0]['price_mapping_id'];
            if($partner_data[0]['partner_type'] == OEM){
                $prices = $this->partner_model->getPrices($booking['service_id'], $unit_details['appliance_category'], $unit_details['appliance_capacity'], $partner_mapping_id, $unit_details['price_tags'], $unit_details['appliance_brand']);
            } else {
                $prices = $this->partner_model->getPrices($booking['service_id'], $unit_details['appliance_category'], $unit_details['appliance_capacity'], $partner_mapping_id, $unit_details['price_tags']);
            }

            $unit_details['id'] =  $prices[0]['id'];
            $unit_details['around_paid_basic_charges'] =  $unit_details['around_net_payable'] = "0.00";
            $unit_details['partner_paid_basic_charges'] = $prices[0]['partner_net_payable'];
            $unit_details['partner_net_payable'] = $prices[0]['partner_net_payable'];
            $booking['amount_due'] = $prices[0]['customer_net_payable'];
            $unit_details['booking_status'] = "Pending";
            //check partner status from partner_booking_status_mapping table  
            $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'],$booking['partner_id'], $booking['booking_id']);
            if(!empty($partner_status)){
                $booking['partner_current_status'] = $partner_status[0];
                $booking['partner_internal_status'] = $partner_status[1];
            }

            $partner_approval = 0;
            $mail = 0;
            $upcountry_data = array();
            $up_mail_data = array();
            $message1 = "";
            if(isset($requestData['upcountry_data'])){
                if(!empty($requestData['upcountry_data'])){
                    $upcountry_data = json_decode($requestData['upcountry_data'], TRUE);
                    if(!empty($upcountry_data)){
                        $booking['is_upcountry'] = 1;
                        $booking['upcountry_pincode'] = $upcountry_data['upcountry_pincode'];
                        $booking['sub_vendor_id'] = $upcountry_data['sub_vendor_id'];
                        $booking['upcountry_distance'] = $upcountry_data['upcountry_distance'];
                        $booking['sf_upcountry_rate'] = $upcountry_data['sf_upcountry_rate'];
                        $booking['partner_upcountry_rate'] = $upcountry_data['partner_upcountry_rate'];
                        $partner_approval = $upcountry_data['partner_approval'];
                        $booking['upcountry_paid_by_customer'] = $upcountry_data['upcountry_paid_by_customer'];
                        if($booking['upcountry_paid_by_customer'] == 1){
                            $booking['amount_due'] += ($booking['partner_upcountry_rate'] *$booking['upcountry_distance'] );
                        }
                        if($upcountry_data['partner_approval'] == '1'){
                           
                            $booking['internal_status'] = UPCOUNTRY_BOOKING_NEED_TO_APPROVAL;
                        }
                        if($upcountry_data['message'] == UPCOUNTRY_LIMIT_EXCEED){
                            $mail = 1;
                            $up_mail_data['name'] = $user['name'];
                            $up_mail_data['appliance'] = $this->booking_model->selectservicebyid($booking
                                    ['service_id'])[0]['services'];
                            $up_mail_data['booking_address'] = $booking['booking_address'];
                            $up_mail_data['city'] = $booking['city'];
                            $up_mail_data['state'] = $booking['state'];
                            $up_mail_data['booking_pincode'] = $booking['booking_pincode'];
                            $up_mail_data['booking_id'] = $booking['booking_id'];
                            $up_mail_data['booking_primary_contact_no'] = $booking['booking_primary_contact_no'];
                            $up_mail_data['price_tags'] = $unit_details['price_tags'];
                            $up_mail_data['appliance_brand'] = $unit_details['appliance_brand'];
                            $up_mail_data['appliance_category'] = $unit_details['appliance_category'];
                            $up_mail_data['appliance_capacity'] = $unit_details['appliance_capacity'];
                            $up_mail_data['upcountry_distance'] = $booking['upcountry_distance'];
                            $booking['upcountry_partner_approved']= '0';
                        }
                    }
                }
            }
            $return_id = $this->booking_model->addbooking($booking);
            if(!empty($return_id)){
            $unit_details['appliance_id'] = $this->booking_model->addappliance($appliance_details);
            $this->booking_model->insert_data_in_booking_unit_details($unit_details, $booking['state']);
            
        
            //-------Sending SMS on booking--------//
            $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
            $send['booking_id'] = $booking['booking_id'];
            $send['state'] = "Newbooking";
            $this->asynchronous_lib->do_background_process($url, $send);


            $this->notify->insert_state_change($booking['booking_id'], _247AROUND_PENDING , _247AROUND_NEW_BOOKING , 
                    $booking['booking_remarks'], $agent_id, $requestData['partnerName'], $booking['partner_id']);
            
            if (empty($booking['state'])) {
                    $to = NITS_ANUJ_EMAIL_ID;
                    $message = "Pincode " . $booking['booking_pincode'] . " not found for Booking ID: " . $booking['booking_id'];
                    $this->notify->sendEmail("booking@247around.com", $to, "", "", 'Pincode Not Found', $message, "");
	    }
            
            //Partner does not approve upcountry bookings and we have the SF ID so auto-assign
            //In case partner approves upcountry bookings, then we cant assign till we have the approval
            if($partner_approval ==0 && isset($requestData['vendor_id'])){
                if(!empty($requestData['vendor_id'])){
                     $assigned = $this->miscelleneous->assign_vendor_process($requestData['vendor_id'],$booking['booking_id']);
                     if($assigned){
                        $url = base_url() . "employee/do_background_process/assign_booking";
                        $this->notify->insert_state_change($booking['booking_id'], ASSIGNED_VENDOR , _247AROUND_PENDING , 
                            "Auto Assign vendor", _247AROUND_DEFAULT_AGENT, _247AROUND_DEFAULT_AGENT_NAME, _247AROUND);
                        $async_data['booking_id'] = array($booking['booking_id']=> $requestData['vendor_id']);
                        $this->asynchronous_lib->do_background_process($url, $async_data);
                     } 
                }
  
            }
            
            //SF could not be found for this upcountry booking so email to admin
//            if(isset($requestData['vendor_id'])){
//                if(empty($requestData['vendor_id']) && empty($upcountry_data) ){
//                    $message1 = "Upcountry did not calculate for ".$booking['booking_id'];
//                    $to = NITS_ANUJ_EMAIL_ID;
//                    $cc = "abhaya@247around.com";
//                    $this->notify->sendEmail("booking@247around.com", $to, $cc, "", 'Upcountry Failed Booking', $message1, "");
//                }
//            }
            
            //Send booking to partner for upcountry approval
            if($mail ==1 && !empty($up_mail_data)){
                $subject = "Upcountry Charges Approval Required - Booking ID ".$booking['booking_id'];
                $to = $this->partner['upcountry_approval_email'];
                $cc= NITS_ANUJ_EMAIL_ID;
                $message = $this->load->view('employee/upcountry_approval_template', $up_mail_data, TRUE);
                $this->notify->sendEmail("booking@247around.com", $to, $cc, "", $subject, $message, "");
            }
           
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
                "247aroundBookingID" => $booking['booking_id'],
                "247aroundBookingStatus" => $booking['current_status'],
                "247aroundBookingRemarks" => $booking['booking_remarks']);

                $this->sendJsonResponse(array($is_valid['code'], $is_valid['msg']));
            } else {
                $this->jsonResponseString['response'] = NULL;
                $this->sendJsonResponse(array($is_valid['code'], $is_valid['msg']));
            }
            }
        }
        } else {
        log_message('info', __METHOD__ . ":: Invalid token: " . $this->token);

        //invalid token
        $this->jsonResponseString['response'] = NULL;
        $this->sendJsonResponse(array(ERR_INVALID_AUTH_TOKEN_CODE, ERR_INVALID_AUTH_TOKEN_MSG));
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
        //($request['model'] == "") ||
        //($request['productType'] == "") ||
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

    //Invalid Product

    //Check for Request type
//    $valid_request_types = array("Installation", "Demo", "Installation and Demo", "Installation & Demo", "Repair - Out Of Warranty", "Repair - In Warranty");
//    if (($flag === TRUE) &&
//        (in_array($request['requestType'], $valid_request_types) == FALSE)) {
//        $resultArr['code'] = ERR_INVALID_REQUEST_TYPE_CODE;
//        $resultArr['msg'] = ERR_INVALID_REQUEST_TYPE_MSG;
//
//        $flag = FALSE;
//    }

    //Check timeslot format validity
    /*if (($flag === TRUE) &&
        ($this->validate_timeslot_format($request['deliveryDate']) === FALSE)) {
        $resultArr['code'] = ERR_INVALID_TIMESLOT_FORMAT_CODE;
        $resultArr['msg'] = ERR_INVALID_TIMESLOT_FORMAT_MSG;

        $flag = FALSE;
    }*/


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
    
    /**
     * @Desc: This function is used to _allot_source_partner_id_for_pincode
     * @params: String Pincode, brnad, default partner id(SS)
     * @return : Array
     * 
     */
    private function _allot_source_partner_id_for_pincode($service_id, $state, $brand) {
        log_message('info', __FUNCTION__ . ' ' . $service_id, $state, $brand);
        $data = [];

        $partner_array = $this->partner_model->get_active_partner_id_by_service_id_brand($brand, $service_id);
        
        if (!empty($partner_array)) {

            foreach ($partner_array as $value) {
                //Now getting details for each Partner 
                $filtered_partner_state = $this->partner_model->check_activated_partner_for_state_service($state, $value['partner_id'], $service_id);
                if ($filtered_partner_state) {
                    //Now assigning this case to Partner
                    $data['partner_id'] = $value['partner_id'];
                    $data['source'] = $this->partner_model->get_source_code_for_partner($value['partner_id']);
                } else {
                    //Now assigning this case to SS
                    $data['partner_id'] = SNAPDEAL_ID;
                    $data['source'] = 'SS';
                }
            }
        } else {
            log_message('info', ' No Active Partner has been Found in for Brand ' . $brand . ' and service_id ' . $service_id);
            //Now assigning this case to SS
            $data['partner_id'] = SNAPDEAL_ID;
            $data['source'] = 'SS';
        }
        return $data;
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
        foreach($unproductive_description as $un_description) {
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
     * @desc: This method is used to send sms on the basis of upcountry charges
     * @param Array $booking
     * @param String $appliance
     * @param Array/boolean $is_price
     * @param Array $appliance_category
     * @param String $file_type
     * @param String $partner_data
     * @return boolean
     */
    function check_upcountry($booking, $appliance, $is_price, $appliance_category, $partner_data) {
        if ($is_price) {
            log_message('info', __FUNCTION__ . ' Check Upcountry Vendor Availability');
            $data = $this->miscelleneous->check_upcountry_vendor_availability($booking['city'], $booking['booking_pincode'], $booking['service_id'], $partner_data, false);
            $charges = 0;
            if ($is_price['is_upcountry'] == 0) {
                $charges = "Rs. " . (($data['upcountry_distance'] * DEFAULT_UPCOUNTRY_RATE) +
                        $is_price['customer_net_payble']);
                log_message('info', __FUNCTION__ . ' Price Sent to Customer ' . $charges);
            } else {
                switch ($data['message']) {
                    case NOT_UPCOUNTRY_BOOKING:
                    case UPCOUNTRY_BOOKING:
                    case UPCOUNTRY_DISTANCE_CAN_NOT_CALCULATE:
                        log_message('info', __FUNCTION__ . ' UPCOUNTRY_BOOKING ');
                        $charges = "Rs. " . $is_price['customer_net_payable'];
                        log_message('info', __FUNCTION__ . ' Price Sent to Customer ' . $charges);

                        break;

                    case UPCOUNTRY_LIMIT_EXCEED:
                        log_message('info', __FUNCTION__ . ' UPCOUNTRY_LIMIT_EXCEED ');
                        if ($data['partner_upcountry_approval'] == 1) {
                            $charges = "Rs. " . $is_price['customer_net_payable'];
                            log_message('info', __FUNCTION__ . ' Price Sent to Customer ' . $charges);
                        } else {
                            log_message('info', __FUNCTION__ . ' Partner does not provide Approval. Not Sent SMS');
                            return false;
                        }
                        break;

                    case SF_DOES_NOT_EXIST:
                        return FALSE;
                    //break;
                }
            }
            $this->send_sms_to_snapdeal_customer($appliance, $booking['booking_primary_contact_no'], $booking['user_id'], $booking['booking_id'], $appliance_category, $charges);
        } else {                               
            $this->send_sms_to_snapdeal_customer($appliance, $booking['booking_primary_contact_no'], $booking['user_id'], $booking['booking_id'], $appliance_category, $is_price);
            return true;
        }
    }

}
