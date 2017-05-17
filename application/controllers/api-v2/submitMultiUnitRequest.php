+<?php

if (!defined('BASEPATH')){
    exit('No direct script access allowed');
}

/**
 * REST APIs for Partners to insert orders in our CRM
 *
 * @author Abhay Anand
 */
class submitMultiUnitRequest extends CI_Controller {
    
    Private $partner = NULL;
    Private $ApiData = array();
    Private $jsonRequestData = NULL;

    function __Construct() {
        parent::__Construct();

        $this->load->model('partner_model');
        $this->load->model('user_model');
        $this->load->model('booking_model');
        $this->load->model('vendor_model');
        $this->load->library('booking_utilities');
        $this->load->library("miscelleneous");
        $this->load->library('email');
        $this->load->library('notify');
        $this->load->library('partner_utilities');
        $this->load->model('upcountry_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('asynchronous_lib');
        $this->load->library('initialized_variable');
    }
    
    /**
     * Insert new order
     *
     * API to insert new order in the CRM
     *
     * @access	public
     * @return	Success / Error code as per the document
     */
    function index() {
        log_message('info', "Entering: " . __METHOD__);
        $this->requestUrl = __METHOD__;
        //Default values
        $this->jsonResponseString['response'] = NULL;
        $this->jsonResponseString['code'] = ERR_GENERIC_ERROR_CODE;
        $this->jsonResponseString['result'] = ERR_GENERIC_ERROR_MSG;

        //Save header / ip address in DB
        $h = $this->getallheaders();
        if ($h === FALSE) {
            $this->header = json_encode($h);
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
                // store API data into ApiData
                $this->ApiData = json_decode($input_d, TRUE);

                if (!(json_last_error() === JSON_ERROR_NONE)) {
                    log_message('info', __METHOD__ . ":: Invalid JSON");

                    //Invalid json
                    $this->jsonRequestData = $input_d;
                    $this->sendJsonResponse(array(ERR_INVALID_JSON_INPUT_CODE, ERR_INVALID_JSON_INPUT_MSG));
                } else {
                    
                    $this->jsonRequestData = $input_d;
                    $this->initialized_variable->fetch_partner_data($this->partner['id']);
                    //Validate API Data
                    $is_valid = $this->validateSubmitRequestData();
                    if ($is_valid['result'] == TRUE) {
                        log_message('info', __METHOD__ . ":: Request validated");
                        // Get State from Pincode
                        $distict_details = $this->vendor_model->get_distict_details_from_india_pincode(trim($this->ApiData['pincode']));
                        $this->ApiData['state'] = $distict_details['state'];
                        $this->ApiData['taluk'] = $distict_details['taluk'];
                        $this->ApiData['district'] = $distict_details['district'];
                       
                        // Store User ID in the ApiData valriable
                        $this->setUserID();
                        
                        //Create Booking ID
                        $this->setBookingID();
                        
                        // Insert Booking Details in the DB
                        $booking_details = $this->insertBookingDetails();
  
                        if ($booking_details) {
                            // Insert Appliance and booking unit details
                            $this->insertAppliance_BookingUnitDetails();
                           
                            $remarks = (isset($this->ApiData['remarks']) ? $this->ApiData['remarks'] : "");
                            //Insert Data in booking state change
                            $this->notify->insert_state_change($this->ApiData['booking_id'], _247AROUND_FOLLOWUP , _247AROUND_NEW_QUERY ,$remarks , DEFAULT_PARTNER_AGENT, $this->ApiData['partnerName'], $this->partner['id']);
                            
                             // Send Mail if state not found
                            if (empty($this->ApiData['state'])) {
                                $to = NITS_ANUJ_EMAIL_ID;
                                $message = "Pincode " . $this->ApiData['booking_pincode'] . " not found for Booking ID: " . $this->ApiData['booking_id'];
                                $this->notify->sendEmail("booking@247around.com", $to, "", "", 'Pincode Not Found', $message, "");
                            }
                            
                            //Send response
                            $this->jsonResponseString['response'] = array(
                                "orderID" => $this->ApiData['orderID'],
                                "247aroundBookingID" => $this->ApiData['booking_id'],
                                "247aroundBookingStatus" => _247AROUND_FOLLOWUP);
                            $this->sendJsonResponse(array(SUCCESS_CODE, SUCCESS_MSG));
                            
                        } else {
                            log_message('info', __METHOD__ . ":: Booking Insertion Failed");
                            //remember  Need to change
                            $this->jsonResponseString['response'] = NULL;
                            $this->sendJsonResponse(array(ERR_INVALID_PRODUCT_TYPE_CODE, ERR_INVALID_PRODUCT_TYPE_MSG));
                        }
                    } else if ($is_valid['code'] == ERR_ORDER_ID_EXISTS_CODE) {
                        log_message('info', "Reason: ERR_ORDER_ID_EXISTS_CODE");

                        $this->orderIdExistMessage($is_valid);
                    } else {
                        $this->jsonResponseString['response'] = NULL;
                        $this->sendJsonResponse(array($is_valid['code'], $is_valid['msg']));
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
     * @desc echo response when order id exist in the DB
     * @param type $is_valid
     */
    function orderIdExistMessage($is_valid) {
        $lead_details = $is_valid['lead'];
        $this->jsonResponseString['response'] = array(
            "247aroundBookingID" => $lead_details['booking_id'],
            "247aroundBookingStatus" => $lead_details['current_status'],
            "247aroundBookingRemarks" => $lead_details['query_remarks']);

        $this->sendJsonResponse(array($is_valid['code'], $is_valid['msg']));
    }
    /**
     * In this method, we will search user by a phone number. If user does not 
     * found in the DB then we will insert user details. and set user Id in the ApiData Variable
     */
    function setUserID() {
        log_message('info', "Entering: " . __METHOD__);
        //Search User by Mobile Number
        $output = $this->user_model->search_user($this->ApiData['mobile']);
        
        if (empty($output)) {
            //User doesn't exist
            
            isset($this->ApiData['landmark']) ?
                            ($address = $this->ApiData['address'] . ", " . $this->ApiData['landmark']) :
                            ($address = $this->ApiData['address']);
             
            $user = array(
                "name" => $this->ApiData['name'],
                "phone_number" => $this->ApiData['mobile'],
                "alternate_phone_number" => (isset($this->ApiData['alternatePhone']) ? $this->ApiData['alternatePhone'] : ""),
                "user_email" => (isset($this->ApiData['email']) ? $this->ApiData['email'] : ""),
                "home_address" => $address,
                "pincode" => $this->ApiData['pincode'],
                "city" => $this->ApiData['city'],
                "state" => $this->ApiData['state'],
            );

            $this->ApiData['user_id'] = $this->user_model->add_user($user);

            //echo print_r($user, true), EOL;
            //Add sample appliances for this user
            $count = $this->booking_model->getApplianceCountByUser($this->ApiData['user_id']);

            //Add sample appliances if user has < 5 appliances in wallet
            if ($count < 5) {
                $this->booking_model->addSampleAppliances($this->ApiData['user_id'], 5 - intval($count));
            }
            
        } else {
            log_message('info', $this->ApiData['mobile'] . ' exists');
            //User exists
            $this->ApiData['user_id'] = $output[0]['user_id'];
        }
    }
    /**
     * @desc Set Appliance Id From product
     */
    function setApplianceID() {
        log_message('info', "Entering: " . __METHOD__);
        $prod = trim($this->ApiData['product']);
        //check service_id exist or not
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
        if($service_id){
            $this->ApiData['service_id'] = $service_id;
            $this->ApiData['service_name'] = $lead_details['Product'];
        }
    }
    /**
     * @desc This is used to create Booking ID and store in the ApiData variable
     */
    function setBookingID(){
        log_message('info', "Entering: " . __METHOD__);
        $booking_id = '';

	$yy = date("y");
        $mm = date("m");
        $dd = date("d");

	$booking_id_temp = str_pad($this->ApiData['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
	$booking_id_temp .= (intval($this->booking_model->getBookingCountByUser($this->ApiData['user_id'])) + 1);
        $random_code=  mt_rand(100, 999);   //return 3 digit random code to make booking id unique
	$booking_id = "Q-" . $this->initialized_variable->get_partner_data()[0]['code'] . "-" . $booking_id_temp.$random_code;

	$this->ApiData['booking_id'] = $booking_id;
    }
    /**
     * @desc: Insert Bookng Details in the DB
     * @return boolean
     */
    function insertBookingDetails(){
        log_message('info', "Entering: " . __METHOD__);
        $booking = array(
            "booking_id" => $this->ApiData['booking_id'],
            "partner_id" => $this->partner['id'],
            "source"     => $this->initialized_variable->get_partner_data()[0]['code'],
            "create_date" => date("Y-m-d H:i:s"),
            "service_id" => $this->ApiData['service_id'],
            "user_id"    => $this->ApiData['user_id'],
            "order_id"   => $this->ApiData['orderID'],
            "type"       => "Query",
            "quantity"   => $this->ApiData['units'],
            "city"       => $this->ApiData['city'],
            "state"      => $this->ApiData['state'],
            "taluk"      => $this->ApiData['taluk'],
            "district"    => $this->ApiData['district'],
            "booking_date"    => '',
            "booking_timeslot"=> '',
            "internal_status" => _247AROUND_FOLLOWUP,
            "partner_source"  => "API",
            "current_status"  => _247AROUND_FOLLOWUP,
            "query_remarks"   => (isset($this->ApiData['remarks']) ? $this->ApiData['remarks'] : ""),
            "booking_pincode" => $this->ApiData['pincode'],
            "delivery_date"   => $this->getDateTime($this->ApiData['deliveryDate']),
            "booking_address" => $this->ApiData['address'] . ", " . (isset($this->ApiData['landmark']) ? $this->ApiData['landmark'] : ""),
            "booking_primary_contact_no" => $this->ApiData["mobile"],
            "booking_alternate_contact_no" => $this->ApiData['alternatePhone']
        );
        
        $this->ApiData['booking_primary_contact_no'] = $this->ApiData["mobile"];
        $this->ApiData['booking_pincode'] = $this->ApiData["pincode"];
        
         //check partner status from partner_booking_status_mapping table 
        $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'],$booking['partner_id'], $booking['booking_id']);
        if(!empty($partner_status)){
            $booking['partner_current_status'] = $partner_status[0];
            $booking['partner_internal_status'] = $partner_status[1];
        }
        
        $booking_details_id =  $this->booking_model->addbooking($booking);
        if($booking_details_id){
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * @desc Insert Appliance details and unit details
     * @return type
     */
    function insertAppliance_BookingUnitDetails() {
        log_message('info', "Entering: " . __METHOD__);
        $is_price_send_upcountry_sms = true;
        $amount_due = 0;
        $is_upcountry = 0;
        foreach ($this->ApiData['unit_details'] as $key => $value) {
            // Insert Appliance details
            $appliance_id = $this->insertApplianceDetails($value, $key);
            //$unit_details= array();
            $unit_details = array(
                "partner_id" => $this->partner['id'],
                "service_id" => $this->ApiData['service_id'],
                "appliance_id" => $appliance_id,
                "appliance_brand" => $value['brand'],
                "model_number" => $value['model'],
                "booking_id" => $this->ApiData['booking_id'],
                "sub_order_id" => $value['subOrderId'],
                "appliance_description" => $value['productType'],
                'purchase_month' => isset($value['purchase_month']) ? $value['purchase_month'] : '',
                'purchase_year' => isset($value['purchase_year']) ? $value['purchase_year'] : ''
            );
            // If Appliance desccription is found in this key index
            if (isset($this->ApiData['appliance_description'][$key])) {
                $unit_details['appliance_capacity'] = $this->ApiData['appliance_description'][$key]['capacity'];
                $unit_details['appliance_category'] = $this->ApiData['appliance_description'][$key]['category'];
                // Check Partnre type is OEM. If OEM the we will use brand to get Price Details onther wise we are using Brand to get Price.
                if ($this->initialized_variable->get_partner_data()[0]['partner_type'] == OEM) {
                    //if partner type is OEM then sent appliance brand in argument
                    $prices = $this->partner_model->getPrices($this->ApiData['service_id'], $unit_details['appliance_category'], $unit_details['appliance_capacity'], $this->initialized_variable->get_partner_data()[0]['price_mapping_id'], $value['requestType'], $unit_details['appliance_brand']);
                } else {
                    //if partner type is not OEM then dose not sent appliance brand in argument
                    $prices = $this->partner_model->getPrices($this->ApiData['service_id'], $unit_details['appliance_category'], $unit_details['appliance_capacity'], $this->initialized_variable->get_partner_data()[0]['price_mapping_id'], $value['requestType'], "");
                }
  
                if (!empty($prices)) {
                    log_message('info', __FUNCTION__ . " => Prices Found");
                    $unit_details['price_tags'] = $value['requestType'];
                    $unit_details['id'] = $prices[0]['id'];
                    $unit_details['around_paid_basic_charges'] = $unit_details['around_net_payable'] = "0.00";
                    $unit_details['partner_paid_basic_charges'] = $prices[0]['partner_net_payable'];
                    $unit_details['partner_net_payable'] = $prices[0]['partner_net_payable'];
                    $amount_due += $prices[0]['customer_net_payable'];
                    if ($prices[0]['is_upcountry'] == 1) {
                        $is_upcountry = 1;
                    }

                    $this->booking_model->insert_data_in_booking_unit_details($unit_details, $this->ApiData['state'], 0);
                } else {
                    $is_price_send_upcountry_sms = false;
                    $unit_details['appliance_capacity'] = isset($value['subCategory']) ? $value['subCategory'] : '';
                    $unit_details['appliance_category'] = isset($value['category']) ? $value['category'] : '';
                    $this->booking_model->addunitdetails($unit_details);
                }
            } else {
                $is_price_send_upcountry_sms = false;
                $unit_details['appliance_capacity'] = isset($value['subCategory']) ? $value['subCategory'] : '';
                $unit_details['appliance_category'] = isset($value['category']) ? $value['category'] : '';

                $this->booking_model->addunitdetails($unit_details);
            }
        }
        
        
        
        $url = base_url() . "api-v2/submitMultiUnitRequest/Check_upcountry_send_sms";
        $this->ApiData['partner_id'] = $this->partner['id'];
        $send['is_price_send_upcountry_sms'] = $is_price_send_upcountry_sms;
        $send['Apidata'] = json_encode($this->ApiData, true);
        $send['is_upcountry'] = $is_upcountry;
        $send['appliance_category'] = $unit_details['appliance_category'];
        $send['amount_due'] = $amount_due;
        
        $this->asynchronous_lib->do_background_process($url, $send);
        $send = array();
        return true;
    }

    /**
     * 
     * @param Array $value
     * @param int $key (unit Array index)
     * @return Appliance Id
     */
    function insertApplianceDetails($value, $key){
        log_message('info', "Entering: " . __METHOD__);
       // $appliance = array();
        $appliance = array(
            'user_id'      => $this->ApiData['user_id'],
            'service_id'  =>$this->ApiData['service_id'],
            "brand"        => $value['brand'],
            'model_number' => isset($value['model'])?$value['model']:'',
            'description'  => $value['productType'],
            'purchase_year' => isset($value['purchase_month'])?$value['purchase_month']:'',
            'purchase_month' => isset($value['purchase_month'])?$value['purchase_month']:'',
            'last_service_date' => date('Y-m-d'),
            'tag'          => $value['brand'] . " " . $this->ApiData['service_name'], 
            'capacity'     =>  isset($this->ApiData['appliance_description'][$key]['capacity'])?$this->ApiData['appliance_description'][$key]['capacity']:$value['subCategory'],
            'category'     =>  isset($this->ApiData['appliance_description'][$key]['category'])
            ?$this->ApiData['appliance_description'][$key]['category']:$value['category']
        );
        
        return $this->booking_model->addappliance($appliance);
    }
    
    function getDateTime($dt){
    log_message('info', "Entering: " . __METHOD__);
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

    function getallheaders() {
        log_message('info', "Entering: " . __METHOD__);
        //Use this if you are using Nginx

        $headers = '';
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;
    }
    
    /**
     * Send final JSON response to Partner
     *
     * @access	private
     * @return	Echos the output
     */
    function sendJsonResponse($code) {
        log_message('info', "Entering: " . __METHOD__);
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
    
    function validateSubmitRequestData() {
        log_message('info', "Entering: " . __METHOD__);
        //Set Appice id in Object
        $this->setApplianceID();

        //Lead will store the booking entry if it exists
        $resultArr = array("result" => FALSE, "lead" => NULL, "code" => NULL, "msg" => NULL);
        $flag = TRUE;

        //Validate Partner Name
        if ($this->ApiData['partnerName'] != $this->partner['public_name']) {
            $resultArr['code'] = ERR_INVALID_PARTNER_NAME_CODE;
            $resultArr['msg'] = ERR_INVALID_PARTNER_NAME_MSG;

            $flag = FALSE;
        }

        //Mandatory Parameter Missing
        if (($flag === TRUE) &&
                (($this->ApiData['orderID'] == "") ||
                ($this->ApiData['product'] == "") ||
                ($this->ApiData['units'] == "") ||
                ($this->ApiData['units'] == 0) ||
                ($this->ApiData['name'] == "") ||
                ($this->ApiData['mobile'] == "") ||
                ($this->ApiData['address'] == "") ||
                ($this->ApiData['pincode'] == "") ||
                (count($this->ApiData['unit_details']) != $this->ApiData['units']) ||
                ($this->ApiData['city'] == "")
                )) {
            $resultArr['code'] = ERR_MANDATORY_PARAMETER_MISSING_CODE;
            $resultArr['msg'] = ERR_MANDATORY_PARAMETER_MISSING_MSG;

            $flag = FALSE;
        }

        // validate Order ID
        if ($flag === TRUE) {
            $lead = $this->partner_model->get_order_id_for_partner($this->partner['id'], $this->ApiData['orderID']);
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
                (in_array($this->ApiData['product'], $valid_products) == FALSE)) {
            //Do not return error as of now, just log this.
            log_message('info', $this->ApiData['product'] . ': Invalid product type');
        }

        //Check timeslot format validity

        if (($flag === TRUE) &&
                ($this->validateTimeslotFormat($this->ApiData['deliveryDate']) === FALSE)) {
            $resultArr['code'] = ERR_INVALID_TIMESLOT_FORMAT_CODE;
            $resultArr['msg'] = ERR_INVALID_TIMESLOT_FORMAT_MSG;

            $flag = FALSE;
        }
        
        if ($flag === TRUE) {
            $unitdata = $this->validateUnit();
            if (!empty($unitdata)) {
                $resultArr['code'] = $unitdata['code'];
                $resultArr['msg'] = $unitdata['msg'];

                $flag = FALSE;
            }
        }
        
        if(!isset($this->ApiData['service_id'])){
            $resultArr['code'] = ERR_MANDATORY_PARAMETER_MISSING_CODE;
            $resultArr['msg'] = ERR_MANDATORY_PARAMETER_MISSING_MSG;
            $flag = false;
        }
        
        //If code is still empty, it means data is valid.
        if ($resultArr['code'] == "") {
            $resultArr['result'] = TRUE;
        }

        return $resultArr;
    }
    /**
     * @desc Brand, request type should not be emplty. It request type shuold be Installation & Demo
     * @return type
     */
    function validateUnit() {
        log_message('info', "Entering: " . __METHOD__ );
        $resultArr = array();
       
        $is_service_id = false;
        if(isset($this->ApiData['service_id'])){
            $is_service_id = true;
        }
        foreach ($this->ApiData['unit_details'] as $key => $value) {
            // Brand Newver be empty
            if ($value['brand'] == '') {
                $resultArr['code'] = ERR_MANDATORY_PARAMETER_MISSING_CODE;
                $resultArr['msg'] = ERR_MANDATORY_PARAMETER_MISSING_MSG;
                break;
            }
            // Request type newver be empty
            if ($value['requestType'] == '') {
                $resultArr['code'] = ERR_MANDATORY_PARAMETER_MISSING_CODE;
                $resultArr['msg'] = ERR_MANDATORY_PARAMETER_MISSING_MSG;
                break;
            }
            // Product type Validate
            $p_status = $this->validateProductType($value['productType']);
            if (!empty($p_status)) {
                $resultArr = $p_status;
                break;
            }
            // Retuedt Type sholud ne Installation & Demo
            $valid_request_types = array("Installation & Demo");
            if (in_array($value['requestType'], $valid_request_types) == FALSE) {
                $resultArr['code'] = ERR_INVALID_REQUEST_TYPE_CODE;
                $resultArr['msg'] = ERR_INVALID_REQUEST_TYPE_MSG;

                break;
            }

           //Get Appliance details from Product type;
            $appliance_data = $this->booking_model->get_service_id_by_appliance_details(trim($value['productType']));
            if (!empty($appliance_data)) {
                $this->ApiData['service_id'] = $appliance_data[0]['service_id'];
                $this->ApiData['service_name'] = $appliance_data[0]['services'];
                $this->ApiData['appliance_description'][$key] = $appliance_data[0];
            }
            
        }

        return $resultArr;
    }

    function validateTimeslotFormat($timeslot) {
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
     * @desc: This function is used to validate the product type
     * @param array $data
     * @return array
     */
    function validateProductType($productType) {
        log_message('info', __FUNCTION__ . "=> Entering validation routine...");
        $invalid_data = array();
        // get unproductive description array
        $unproductive_description = $this->unproductiveProduct();
        foreach ($unproductive_description as $un_description) {
            if (stristr(trim($productType), $un_description)) {
                array_push($invalid_data, trim($productType));
            }
        }
        if (!empty($invalid_data)) {
            log_message('info', __FUNCTION__ . ' =>  Product description is not valid in JSON Request Data: ' .
                    print_r($invalid_data, true));

            $resultArr['code'] = ERR_INVALID_PRODUCT_TYPE_CODE;
            $resultArr['msg'] = ERR_INVALID_PRODUCT_TYPE_MSG;

            return $resultArr;
        } else {
            log_message('info', __FUNCTION__ . "=> Exiting validation routine: Under Control");
            return array();
        }
    }

    /**
     * @desc: This is used to store key. If this key exists in the JSON Request then we will remove them.
     * @return array
     */
    function unproductiveProduct() {
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
     * @desc Its called in asynchronous way to check upcountry and send sms.
     */
    function Check_upcountry_send_sms(){
        
        $is_price_send_upcountry_sms = $this->input->post("is_price_send_upcountry_sms");
        $this->ApiData = json_decode($this->input->post("Apidata"), true);
        $this->initialized_variable->fetch_partner_data( $this->ApiData['partner_id']);
        //$appliance_category = $this->input->post('appliance_category');
        $is_upcountry = $this->input->post('is_upcountry');
        $amount_due = $this->input->post("amount_due");
         
        // If $is_price_send_upcountry_sms variable is false then we will send sms in a old way( means send sms without Price)
        if ($is_price_send_upcountry_sms == false) {
            $is_sms = $this->miscelleneous->check_upcountry($this->ApiData, $this->ApiData['service_name'], array(), "shipped");
        } else {
            $is_price['is_upcountry'] = $is_upcountry;
            $is_price['customer_net_payable'] = $amount_due;
            $is_sms = $this->miscelleneous->check_upcountry($this->ApiData, $this->ApiData['service_name'], $is_price, "shipped");
        }

        //Check vendor Availabilty for pincode and service id
        if ($is_sms) {
            $booking['sms_count'] = 1;
            $booking['internal_status'] = _247AROUND_FOLLOWUP;
            $booking['amount_due'] = $amount_due;
        } else {
            $booking['internal_status'] = SF_UNAVAILABLE_SMS_NOT_SENT;
        }
        // Update Booking for internal status
        $this->booking_model->update_booking($this->ApiData['booking_id'], $booking);
    }
   

}
