<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//error_reporting(E_ERROR);
//ini_set('display_errors', '0');

class dealerApi extends CI_Controller {

    private $token;
    private $header;
    private $requestId;
    private $deviceId;
    private $requestUrl;
    private $jsonRequestData;
    private $jsonResponseString;
    private $debug;
    private $tokenArray;
    private $statusCode;
    private $app_price_mapping_id = 247001;

    function __Construct() {
        parent::__Construct();

        $this->load->model('apis');
        $this->load->model('diagnostics');
        $this->load->model('discount');
        $this->load->model('booking_model');
        $this->load->model('partner_model');
        $this->load->model('vendor_model');
        $this->load->model('user_model');
        $this->load->model('partner_model');
        $this->load->model('engineer_model');
        $this->load->model("dealer_model");
        $this->load->model("reusable_model");
        $this->load->model("service_centers_model");
        $this->load->model('indiapincode_model');
        $this->load->library('notify');
        $this->load->library("miscelleneous");
        $this->load->library('booking_utilities');
        $this->load->library('s3');
        $this->load->library('email');
        $this->load->helper(array('form', 'url'));
        $this->load->library('asynchronous_lib');
        $this->load->library('paytm_payment_lib');
        $this->load->library('validate_serial_no');
        $this->load->library('warranty_utilities');
        $this->load->library('booking_creation_lib');
        $this->load->library('invoice_lib');
        $this->load->library('around_generic_lib');
    }

    /**
     * @input: void
     * @description: accepts post request only and basic validations
     * @output: void
     */
    public function index() {
        log_message('info', "Entering: " . __METHOD__ . json_encode($_POST, true));
        // echo json_encode($_POST, true); exit();

        ob_end_clean();
        //$input_d = file_get_contents('php://input');
        //$_POST = json_decode($input_d, true);
        //$_POST = json_decode($str, true);
        //print_r($_POST); exit();
        $this->debug = true;
        $this->jsonResponseString = null;
        $this->user = "";

        if ($_POST && array_key_exists("request", $_POST)) {

            $jsonRequestData = $_POST['request'];


            $requestData = json_decode($jsonRequestData, true);

            $this->token = $requestData['token'];
// temporary check for version update check for key also for older version apps///
            // if(!isset($requestData["app_version"])  || $requestData["app_version"]!= APP_VERSION ){
            // log_message('info', "Force update error");
            // $this->sendJsonResponse(array(APP_VERSION_RESPONSE_CODE, 'Please update your app , then try again !'));
            // exit;
            // }

            //username is user email address, not her name
            if (array_key_exists("username", $requestData)) {
                $this->user = $requestData['username'];
            }

            $this->requestId = $requestData['requestId'];
        /*  Configure to skip device info in request from spalsh screen if device id come or not */
            if(isset($requestData['deviceId']) && !empty($requestData['deviceId'])){
                $this->deviceId =   $requestData['deviceId'];
            }else{
                $this->deviceId =   ACCESS_FROM_SPLASH_SCREEN;
            }

            $this->requestUrl = $requestData['requestUrl'];

            if ($this->requestUrl == "saveHandyMan") {
                header('Content-Type: bitmap; charset=utf-8');
            }

            $this->tokenArray = explode('.', $this->token);
            $header = $this->tokenArray[0];
            $jsonData = $this->tokenArray[1];
            $signature = $this->tokenArray[2];
            $type = 'post';

            $details = array(
                'header' => base64_decode($this->tokenArray[0]),
                'request' => base64_decode($this->tokenArray[1]),
                'signature' => base64_decode($this->tokenArray[2]),
                'request_id' => $this->requestId,
                'device_id' => $this->deviceId,
                'email_id' => $this->user,
                'browser_information' => $_SERVER['HTTP_USER_AGENT'],
                'ip_address' => $_SERVER["REMOTE_ADDR"],
                'type' => $type);
            //$this->apis->saveRequestData($details);

            $activity = array('activity' => 'data input', 'data' => json_encode($details), 'time' => $this->microtime_float());
            //$this->apis->logTable($activity);

            $this->validateRequest();
        } else {
            log_message('info', "request key NOT exists");
            $this->sendJsonResponse(array('0001', 'failure'));
        }
    }

    /**
     * @input: void
     * @description: accepts get request only and basic validations
     * @output: void
     */
    public function jsonPost() {
        log_message('info', "Entering: " . __METHOD__);

        $this->debug = true;
        $this->jsonResponseString = null;
        $this->user = "";
        $this->user = $this->input->get('username');
        $this->token = $this->input->get('jwt');
        /*  Configure to skip device info in request from spalsh screen if device id come or not */
        $deviceID =  $this->input->get('deviceId');
        if(isset($deviceID) && !empty($deviceID)){
                $this->deviceId =   $deviceID;
        }else{
                $this->deviceId =   ACCESS_FROM_SPLASH_SCREEN;
        }

        $this->requestId = $this->input->get('requestId');
        $this->requestUrl = $this->input->get('requestUrl');

        // $authToken = $this->apis->getAuthToken($this->user);

        if (!empty($this->token)) {

            $this->tokenArray = explode('.', $this->token);
            //$header = $this->tokenArray[0];
            //$jsonData = $this->tokenArray[1];
            //$signature = $this->tokenArray[2];
            $type = 'post';

            $details = array(
                'header' => base64_decode($this->tokenArray[0]),
                'request' => base64_decode($this->tokenArray[1]),
                'signature' => base64_decode($this->tokenArray[2]),
                'request_id' => $this->requestId,
                'device_id' => $this->deviceId,
                'email_id' => $this->user,
                'browser_information' => $_SERVER['HTTP_USER_AGENT'],
                'ip_address' => $_SERVER["REMOTE_ADDR"],
                'type' => $type);
            //$this->apis->saveRequestData($details);

            $activity = array('activity' => 'data input', 'data' => json_encode($details), 'time' => $this->microtime_float());
            //$this->apis->logTable($activity);

            $this->validateRequest();
        } else {
            log_message('info', "request key NOT exists in jsonPost");
            $this->sendJsonResponse(array('0001', 'failure'));
        }
    }

    /**
     * @input: void
     * @description: validate each request
     * @output: void
     */
    function validateRequest() {
        if ($this->checkSignature() == true and $this->checkAppKeyAndTimeout() == true) {
            $this->processRequest();
        }
    }

    /**
     * @input: void
     * @description: check api key and timeout
     * @output: void
     */
    function checkAppKeyAndTimeout() {
        $activity = array('activity' => 'checking appkey and timeout', 'data' => json_encode($this->jsonRequestData), 'time' => $this->microtime_float());
        //$this->apis->logTable($activity);

        $appKey = $this->jsonRequestData['iss'];
        $expTime = $this->jsonRequestData['exp'];
        $curTime = time();
        return true;
        if ($appKey == 'boloaaka-mobile-application' and $curTime <= $expTime) {
            return true;
        } else {
            $this->sendJsonResponse(array('0004', 'failure'));
        }
    }

    /**
     * @input: void
     * @description: check signature and vaidate signarure
     * @output: void
     */
    function checkSignature() {
        $activity = array('activity' => 'checking signarure', 'data' => json_encode($this->tokenArray), 'time' => $this->microtime_float());
       // $this->apis->logTable($activity);
        if (count($this->tokenArray) == 3) {
            $header = $this->tokenArray[0];
            $claims = $this->tokenArray[1];
            $signature = $this->tokenArray[2];
            $secret = $this->doCalculateHmacSignature("username", "boloaaka-signup-request");
            $this->jsonRequestData = base64_decode($claims);
            $this->jsonRequestData = json_decode($this->jsonRequestData, true);
            //print_r($this->jsonRequestData);
            return $this->verifySignature($header, $claims, $signature, $secret);
        } else {
            $this->sendJsonResponse(array('0002', 'failure'));
        }
    }

     /**
     * @input: Array having code (numeric) and result (string) as 1st and 2nd elements
     * @description: send success and failure response
     * @output: Echoes response which gets returned to the Client (Android App) through the REST API
     */
    function sendJsonResponse($code) {

        $this->jsonResponseString['code'] = $code[0];
        $this->jsonResponseString['result'] = $code[1];

        if ($this->debug == "true") {
            $responseData = array("data" => $this->jsonResponseString);
            $activity = array('activity' => 'sending response', 'data' => json_encode($responseData), 'time' => $this->microtime_float());
            //$this->apis->logTable($activity);
            $response = json_encode($responseData, JSON_UNESCAPED_SLASHES);

            echo $response;
        } else if ($this->debug == "false") {
            $message = array("appid" => $this->appId, "data" => $this->jsonResponseString);
            $message = json_encode($message, JSON_UNESCAPED_SLASHES);
            $signature = $this->doCalculateHmacSignature($message, $this->appSecrete);
            header("x-pingoo:" . $signature);
            $responseData = array("appid" => $this->appId, "data" => $this->jsonResponseString);
            $responseData = json_encode($responseData, JSON_UNESCAPED_SLASHES);
            $response = base64_encode($responseData);

            echo $response;
        } else {
            $responseData = array("appid" => $this->appId, "debug" => $this->debug, "data" => $this->jsonResponseString);
            $response = json_encode($responseData, JSON_UNESCAPED_SLASHES);

            echo $response;
        }
    }



      /* @Desc - This function is used to validate keys exist in array or not
     * @Param - $keysArray(array), $requestArray(array)
     * @return - $response(array)
     */

    function validateKeys($keysArray, $requestArray) {
        $response = array();
        $missing_key = "";
        $check = true;
        if (!empty($requestArray)) {
            if (!empty($keysArray)) {
                foreach ($keysArray as $key) {
                    if (!array_key_exists($key, $requestArray)) {
                        $check = false;
                        $missing_key = $key;
                        break;
                    }
                }
                if ($check) {
                    $response['status'] = true;
                    $response['message'] = "Success";
                } else {
                    $response['status'] = false;
                    $response['message'] = "Request key missing - " . $missing_key;
                }
            } else {
                $response['status'] = false;
                $response['message'] = "Keys Array Not Found";
            }
        } else {
            $response['status'] = false;
            $response['message'] = "Requested Array Not Found";
        }
        return $response;
    }


    /**
     * Simple function to replicate PHP 5 behaviour
     */
    function microtime_float() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }

    /**
     * @input: void
     * @description: verify signarure
     * @output: print response
     */
    public function verifySignature($header, $claims, $signature, $secret) {

        $headerArray = json_decode(base64_decode($header), true);

        if ($headerArray['typ'] === 'JWT') {

            $algo = $headerArray['alg'];
            $string = $header . "." . $claims;

            $sig = base64_encode(hash_hmac($algo, $string, $secret));

            if ($sig === $signature) {
                return true;
            } else {
                //$this->statusCode = '0002';
                $this->sendJsonResponse(array('0002', 'failure'));
            }
        } else {
            //$this->statusCode = '0003';
            $this->sendJsonResponse(array('0003', 'failure'));
        }
    }

    /**
     * @input: JSON String and App secret
     * @description: Create signature from JSON String and App secret
     * @output: Signature
     */
    function doCalculateHmacSignature($message, $secretKey) {
        $hmac = hash_hmac("sha256", $message, $secretKey);

        $hmacBase64 = base64_encode($hmac);

        return $hmacBase64;
    }

    /**
     * @input: void
     * @description: acts as a router to process different request
     * @output: void
     */
    function processRequest() {
        log_message('info', "Entering: " . __METHOD__ . ", Request type UPDATED: " . $this->requestUrl);

        switch ($this->requestUrl) {

            case 'checkForUpgrade':
              $this->check_for_upgrade();  // this function is used to check the app version and hard/soft upgrade //
              break;
             
            case 'dealerLogin':
                $this->processDealerLogin();
                break;
            
            case 'searchData':
                $this->getSearchData();
                break;

            case 'getTrackingData':
                $this->getTrackingData(); /* get Tracking Details API */
                break;

            case 'getBookingData':
                $this->getBookingDetails(); /* get Booking Details API */
                break;

            case 'getSpareHistory':
                $this->getSpareTrackingHistory(); /* get Booking Details API */
                break;

            case 'getSpareTabData':
                $this->getSpareTabDataForBooking(); /* get Spare Details API */
                break;

            case 'getEscalationReason':
                $this->getEscalationReason(); /* get Spare Details API */
                break;

            case 'submitEscalation':
                $this->submitEscalation(); /* get Spare Details API */
                break;
            
            case 'homeFilters':
                $this->gethomeFilters(); /* get homeFilters API */
                break;
            
            case 'homeDashboard':
                $this->getHomeDashboard(); /* get getHomeDashboard API */
                break;
           
            case 'getTopRatingSf':
                $this->getTopRatingSfs(); /* get getTopRatingSfs API */
                break;
            
            case 'getStateTATData':
                $this->getStateTATData(); /* get getStateTATData API */
                break;

            default:
                break;
        }
    }

    function verifyResponse($phone_number, $userResult) {
        log_message('info', "Entering: " . __METHOD__);

        $result_print = array();
        $code = "0011";
        $status = "error";
        $message = "Welcome to 247around, your dealer app. You can use it for Appliance Repair/Service/Installation requirements and check our unique repair diagnostics.";
        $developer_phone = array('8826423424', '7275746702');
        //$developer_phone = array();

        if ($userResult) {
            //Confirm user about number verification
            $this->notify->sendTransactionalSmsMsg91($phone_number, $message, SMS_WITHOUT_TAG);
            $name = $userResult[0]['name'];
            $email = $userResult[0]['user_email'];
            $user_id = $userResult[0]['user_id'];

            //Inform Admin as well about the new user

            if (!in_array($phone_number, $developer_phone)) {
                /*
                  $this->sendMail("New User Added", $name . " with phone number " . $phone_number
                  . " and email " . $email . " joi`ned Around !!!", false);
                 */

                $this->sendNewUserEmail($userResult);
            }


            //Create sample wallet if required
            //Check no of appliances in User's wallet
            $count = $this->apis->getApplianceCountByUser($user_id);
            //log_message('info', "Appliance Count: " . $count);
            //Add sample appliances if user has < 5 appliances in wallet
            if ($count < 5) {
                $this->apis->addSampleAppliances($user_id, 5 - intval($count));
            }

            $result = $userResult[0];
            $result_print = array(
                'user_id' => $result['user_id'],
                'phone_number' => $result['phone_number'],
                'name' => $result['name']
            );

            $code = "0000";
            $status = 'success';
        }

        $this->jsonResponseString['response'] = $result_print;
        $this->sendJsonResponse(array($code, $status));
    }

   /*
     * @Desc - This function is used to process the login of the dealer
     * @param - 
     * @response - json
     * @Author  - Abhishek Awasthi
     */ 

    function processDealerLogin() {
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //log_message('info', "Request Login: " .print_r($requestData,true));
        $data = $this->dealer_model->entity_login(array("entity" => "dealer",
            "active" => 1, "user_id" => $requestData["mobile"]));
        if (!empty($data)) {
            $login = $this->dealer_model->entity_login(array("active" => 1, "user_id" => $requestData["mobile"], "password" => md5($requestData["password"])));
            if (!empty($login)) {
          /*  Token Update */
            
            $update_dealer =array();
            if(isset($requestData['device_firebase_token']) && !empty($requestData['device_firebase_token'])){
                $update_dealer = array(
                    'device_firebase_token' => $requestData['device_firebase_token']
                );
                }else{
                $update_dealer = array(
                    'device_firebase_token' => NULL
                );  
            }

            $this->dealer_model->update_dealer($update_dealer,array('dealer_id'=>$login[0]['entity_id']));
////// LOGIN LOGIC ///
                $this->jsonResponseString['response'] = $login[0];
                $this->sendJsonResponse(array('0000', 'success'));

            } else {
                $this->sendJsonResponse(array('0013', 'Invalid User Id or Password'));
            }
        } else {
            $this->sendJsonResponse(array('0014', 'User Id does not exist or user not active'));
        }
    }



    /* @author Abhishek Awasthi
     *@Desc - This function is used to check app upgrade
     *@param - 
     *@return - json
     */

function check_for_upgrade(){

        log_message("info", __METHOD__ . " Entering..in upgrade");
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        $validation = $this->validateKeys(array("app_version"), $requestData);
        if ($requestData['app_version']!=DEALER_APP_VERSION) { 
                // get configuration data from table for App version upgrade // 
                $response = $this->engineer_model->get_engineer_config(DEALER_FORCE_UPGRADE); 
                $this->jsonResponseString['response'] = array('configuration_type'=>$response[0]->configuration_type,'config_value'=>$response[0]->config_value); // chnage again acc to umesh  // Response one up according to umesh//
                $this->sendJsonResponse(array('0000', 'success')); // send success response //
               
        } else {
            log_message("info", __METHOD__ . $validation['message']);
            $this->jsonResponseString['response'] = array(); /// Response one up according to umesh//
            $this->sendJsonResponse(array("9998",'Upgrade not required')); // Syntax Error Solve //
        }

}


/*
     * @Desc - This function is used to get booking deatails related to search value which is either booking id or user phone number
     * @param - $search_value
     * @response - json
     */

    function getSearchData() {
        log_message("info", __METHOD__ . " Entering..");
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        $phone_number = "";
        $booking_id = "";
        $data = array();
        $validation = $this->validateKeys(array("search_value"), $requestData);
        if ($validation['status']) {
            $search = preg_replace('/[^A-Za-z0-9\-]/', '', trim($requestData['search_value']));
            //echo $search; die();
            if (!empty($search)) {
                if (preg_match("/^[6-9]{1}[0-9]{9}$/", $search)) {
                    $phone_number = $search;
                } else {
                    $booking_id = $search;
                }
            }
            // Add alternate number ///
            $select = "services.services, users.phone_number,users.alternate_phone_number,users.name as name, users.phone_number, booking_details.*";
            $post['length'] = -1;
            if (!empty($booking_id)) {
                $post['search_value'] = $booking_id;
                $post['column_search'] = array('booking_details.booking_id');
                $post['order'] = array(array('column' => 0, 'dir' => 'asc'));
                $post['order_performed_on_count'] = TRUE;
                $post['column_order'] = array('booking_details.booking_id');
                $post['unit_not_required'] = true;
                $post['where']['nrn_approved'] = 0; // Do not Show booking which are NRN Approved //

                $data['Bookings'] = $this->booking_model->get_bookings_by_status($post, $select, array(), 2)->result_array();
            } else {
                // Search   booking  on phone number
                $data['Bookings'] = $this->dealer_model->dealer_partner_bookings_on_user($phone_number, $requestData['entity_id'] , $requestData['entity_type']);
            }

            if (!empty($data['Bookings'])) {
                $dealer_pincode = $requestData["dealer_pincode"];
                foreach ($data['Bookings'] as $key => $value) {
                    if ($dealer_pincode) {
/*  Make True if want calculation from google API */
                    $calculate_ddistance = FALSE;
                    $distance = "0"; 
                    if($calculate_ddistance){
                        $distance_details = $this->upcountry_model->calculate_distance_between_pincode($dealer_pincode, "", $value['booking_pincode'], "");
                        $distance_array = explode(" ", $distance_details['distance']['text']);
                        $distance = sprintf("%.2f", str_pad($distance_array[0], 2, "0", STR_PAD_LEFT));
                        }
                        $data['Bookings'][$key]['booking_distance'] = $distance;

                        $unit_data = $this->booking_model->get_unit_details(array("booking_id" => $value['booking_id']), false, "appliance_brand, appliance_category, appliance_capacity,sf_model_number,model_number,serial_number,price_tags,customer_total,appliance_description");
                        $data['Bookings'][$key]['appliance_brand'] = $unit_data[0]['appliance_brand'];
                        $data['Bookings'][$key]['appliance_category'] = $unit_data[0]['appliance_category'];
                        $data['Bookings'][$key]['appliance_capacity'] = $unit_data[0]['appliance_capacity'];
                        // Abhishek Send Spare Details of booking //
                        $spares_details = $this->around_generic_lib->getSpareDetailsOfBooking($value['booking_id']);
                        $data['Bookings'][$key]['spares'] =  $spares_details;
                        $data['Bookings'][$key]['unit_details'] =  $unit_data; // Unit Details Data
                        $query_scba = $this->vendor_model->get_service_center_booking_action_details('*', array('booking_id' => $value['booking_id'], 'current_status' => 'InProcess'));
                        $data['Bookings'][$key]['service_center_booking_action_status'] = "Pending";
                        if (!empty($query_scba)) {
                            $data['Bookings'][$key]['service_center_booking_action_status'] = "InProcess";
                        }
                    }
                }
                $this->jsonResponseString['response'] = $data;
                $this->sendJsonResponse(array('0000', "Details found successfully"));
            } else {
                log_message("info", __METHOD__ . "Data not found");
                $this->sendJsonResponse(array("1003", "Data not found"));
            }
        } else {
            log_message("info", __METHOD__ . $validation['message']);
            $this->sendJsonResponse(array("1004", $validation['message']));
        }
    }

  /*
     * @Desc - This function is used to get booking deatails 
     * @param - $booking_id
     * @response - json
     * @Author  - Abhishek Awasthi
     */

function getBookingDetails(){

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        $validation = $this->validateKeys(array("entity_id","appliance_id","is_repeat","show_all_capacity"), $requestData);
        if (!empty($requestData['booking_id']) && !empty($requestData['appliance_id'])) { 
                $response =  $this->around_generic_lib->getBookingDetails($requestData['booking_id'],$requestData['appliance_id'],$requestData['is_repeat'],$requestData['show_all_capacity']); 
                 $this->jsonResponseString['response'] = $response;
                 $this->sendJsonResponse(array('0000', "Details found successfully")); // send success response //
               
        } else {
            log_message("info", __METHOD__ . $validation['message']);
            $this->jsonResponseString['response'] = array(); 
            $this->sendJsonResponse(array("1005", "Booking Details Not Found !")); 
        }

}

     /*
     * @Desc - This function is used to get Booking TAT of the dealer
     * @param - 
     * @response - json
     * @Author  - Abhishek Awasthi
     */
function  getHomeDashboard(){
    
       $requestData = json_decode($this->jsonRequestData['qsh'], true);
       $validation = $this->validateKeys(array("entity_id","entity_type"), $requestData);
          
        if (!empty($requestData['entity_id']) && !empty($requestData['entity_type'])) {
            
            
            
            /* Getting State And their Cities */
                    $state_with_cities = array();
                    $response_state = $this->indiapincode_model->get_allstates();
                    foreach($response_state as $state){    
                    $cities = $this->indiapincode_model->getStateCities($state['state_code']);
                    $state_with_cities[] = array('state'=>$state['state'],'cities'=>$cities);   
                    } 

                    if(isset($requestData['status']) && !empty($requestData['status']) && $requestData['status']!='All'){
                       $status= $requestData['status'];
                    }else if($requestData['status']=='All'){
                       $status="not_set";  
                    }else{
                        $status="not_set";
                    }
                    
                    if(isset($requestData['service_id']) && !empty($requestData['service_id']) && $requestData['service_id']!='All'){
                       $service_id = $requestData['service_id'];
                    } else if($requestData['service_id']=='All'){
                        $service_id ="not_set"; 
                    }else{
                       $service_id ="not_set";  
                    }
                   
                    if(isset($requestData['request_type']) && !empty($requestData['request_type']) && $requestData['request_type']!='All'){
                       $request_type = $requestData['request_type'];
                    }else if($requestData['request_type']=='All'){
                       $request_type ="not_set";  
                    }else{
                      $request_type ="not_set";  
                    }
                    
                    if(isset($requestData['free_paid']) && !empty($requestData['free_paid']) && $requestData['free_paid']!='All'){
                       $free_paid = $requestData['free_paid'];
                    }else if($requestData['free_paid']=='All'){
                       $free_paid ="not_set";  
                    }else{
                       $free_paid ="not_set";
                    }
                    
                    if(isset($requestData['upcountry']) && !empty($requestData['upcountry']) && $requestData['upcountry']!='All'){
                       $upcountry = $requestData['upcountry'];
                    }else if($requestData['upcountry']=='All'){
                       $upcountry ="not_set";  
                    }else{
                       $upcountry ="not_set";  
                    }
                    
                    
                    if(isset($requestData['partner_id']) && !empty($requestData['partner_id']) && $requestData['partner_id']!='All'){
                       $partner_id = $requestData['partner_id'];
                    }else if($requestData['partner_id']=='All'){
                        $partner_id = "not_set";
                    }else{
                        $partner_id = "not_set"; 
                    }
                    
                    
                    if(isset($requestData['state']) && !empty($requestData['state']) && $requestData['state']!='All'){
                        $state = $requestData['state'];
                    }else if($requestData['city']=='All'){
                        $state = "not_set";
                    }else{
                        $state = "not_set"; 
                    }
                    
                    
                    if(isset($requestData['city']) && !empty($requestData['city']) && $requestData['city']!='All'){
                        $city = $requestData['city'];
                    }else if($requestData['city']=='All'){
                        $city = "not_set";
                    }else{
                        $city = "not_set"; 
                    }
                    
                   
                    $is_pending = 0;
                    
                    //Call curl for TAT
                    $postData = array();
                    $url = base_url() . "employee/dashboard/get_booking_tat_report/".$requestData['startDate']."/".$requestData['endDate']."/".$status."/".$service_id."/".$request_type."/".$free_paid."/".$upcountry."/RM/".$is_pending."/".$partner_id."/".$state."/".$city;
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
                    $curl_response = json_decode(curl_exec($ch));   
                    if($curl_response==null || empty($curl_response)){
                    $curl_response->state_city = $state_with_cities;
                    $this->jsonResponseString['response'] = $curl_response;
                    $this->sendJsonResponse(array('1020', "Details not found")); // send success response //  
                    }else{
                     $curl_response->state_city = $state_with_cities;
                     $this->jsonResponseString['response'] = $curl_response;
                     $this->sendJsonResponse(array('0000', "Details found successfully")); // send success response //
                    }
              //  
                $this->jsonResponseString['response'] = $curl_response;
                $this->sendJsonResponse(array('0000', "Details found successfully")); // send success response //
               
        } else {
            log_message("info", __METHOD__ . $validation['message']);
            $this->jsonResponseString['response'] = array(); 
            $this->sendJsonResponse(array("1005", "Booking Details Not Found !")); 
        }  
    
}




/*
     * @Desc - This function is used to get tracking details
     * @param - 
     * @response - json
     * @Author  - Abhishek Awasthi
     */

function getTrackingData(){

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        $validation = $this->validateKeys(array("carrier_code","awb_number"), $requestData);
        if (!empty($requestData['carrier_code']) && !empty($requestData['awb_number'])) { 
            /* getting tracking data of AWB from trackmoreAPI */
                $response =  $this->around_generic_lib->getTrackingData($requestData['carrier_code'],$requestData['awb_number']); 
                 $this->jsonResponseString['response'] = $response;
                 $this->sendJsonResponse(array('0000', "Tracking details found successfully")); // send success response //
               
        } else {
            log_message("info", __METHOD__ . $validation['message']);
            $this->jsonResponseString['response'] = array(); 
            $this->sendJsonResponse(array("1006", "Tracking details not found !")); 
        }

}


  /*
     * @Desc - This function is used to get spare tracking details
     * @param - 
     * @response - json
     * @Author  - Abhishek Awasthi
     */

function getSpareTrackingHistory(){

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        $validation = $this->validateKeys(array("spare_id"), $requestData);
        if (!empty($requestData['spare_id'])) { 
            /* Get Spare tracking data from DB */
                $response =  $this->around_generic_lib->getSpareTrackingHistory($requestData['spare_id']); 
                 $this->jsonResponseString['response'] = $response;
                 $this->sendJsonResponse(array('0000', "Spare tracking details found successfully")); // send success response //
               
        } else {
            log_message("info", __METHOD__ . $validation['message']);
            $this->jsonResponseString['response'] = array(); 
            $this->sendJsonResponse(array("1007", "Tracking details not found !")); 
        }

}



  /*
     * @Desc - This function is used to get escalation reasons 
     * @param - 
     * @response - json
     * @Author  - Abhishek Awasthi
  */


function getEscalationReason(){

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        $validation = $this->validateKeys(array("entity_type"), $requestData);
        if (!empty($requestData['entity_type'])) { 
                $response =  $this->around_generic_lib->getEscalationReason($requestData['entity_type']); 
                 $this->jsonResponseString['response'] = $response;
                 $this->sendJsonResponse(array('0000', "Escalation details found successfully")); // send success response //
               
        } else {
            log_message("info", __METHOD__ . $validation['message']);
            $this->jsonResponseString['response'] = array(); 
            $this->sendJsonResponse(array("1008", "Escalation details not found !")); 
        }


}

  /*
     * @Desc - This function is used to submit escalation  
     * @param - 
     * @response - json
     * @Author  - Abhishek Awasthi
  */

function submitEscalation(){


        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        $validation = $this->validateKeys(array("entity_type","booking_id","escalation_reason_id"), $requestData);
        if (!empty($requestData['entity_type'])) { 
                    $postData = array(
                        "escalation_reason_id" => $requestData['escalation_reason_id'],
                        "escalation_remarks" => $requestData['escalation_remarks']
                    );
                    //Call curl for updating booking 
                    $url = base_url() . "employee/partner/process_escalation/".$requestData['booking_id'];
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
                    $curl_response = curl_exec($ch);
                    curl_close($ch);
  
                 $this->jsonResponseString['response'] = $curl_response;
                 $this->sendJsonResponse(array('0000', "Escalation details updated successfully")); // send success response //
               
        } else {
            log_message("info", __METHOD__ . $validation['message']);
            $this->jsonResponseString['response'] = array(); 
            $this->sendJsonResponse(array("1009", "Escalation details not updated !")); 
        }






}

  /*
     * @Desc - This function is used get spare data for tabs 
     * @param - 
     * @response - json
     * @Author  - Abhishek Awasthi
     */

    function getSpareTabDataForBooking() {

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        $validation = $this->validateKeys(array("booking_id"), $requestData);
        if (!empty($requestData['booking_id'])) {
            $select = "spare_parts_details.*,inventory_master_list.part_number,inventory_master_list.part_name as final_spare_parts,im.part_number as shipped_part_number,original_im.part_name as original_parts,original_im.part_number as original_parts_number, booking_cancellation_reasons.reason as part_cancel_reason,spare_consumption_status.consumed_status, spare_consumption_status.is_consumed, wrong_part_shipped_details.part_name as wrong_part_name, wrong_part_shipped_details.remarks as wrong_part_remarks, sc.name AS send_defective_to, oow_spare_invoice_details.invoice_id as oow_invoice_id, oow_spare_invoice_details.invoice_date as oow_invoice_date, oow_spare_invoice_details.hsn_code as oow_hsn_code, oow_spare_invoice_details.gst_rate as oow_gst_rate, oow_spare_invoice_details.invoice_amount as oow_incoming_invoice_amount, oow_spare_invoice_details.invoice_pdf as oow_incoming_invoice_pdf, ccid.box_count as sf_box_count,ccid.billable_weight as sf_billable_weight,cc_invoice_details.box_count as wh_box_count,cc_invoice_details.billable_weight as wh_billable_weight, cci_details.box_count as p_box_count, cci_details.billable_weight as p_billable_weight";
            $where = array('spare_parts_details.booking_id' => $requestData['booking_id']);
            $post = array();
            $post['is_inventory'] = 1;
            $post['is_original_inventory'] = 1;
            $post['spare_cancel_reason'] = 1;
            $post['wrong_part'] = 1;
            $spare_data = $this->partner_model->get_spare_parts_by_any($select, $where, FALSE, FALSE, FALSE, $post, TRUE, TRUE, TRUE, TRUE, TRUE, TRUE);
            $spare_request = array();
            $spare_shipped = array();
            $spare_defective = array();
            $spare_invoice = array();
            $spare_oow = array();
            $estimate_given = false;
            $parts_shipped = false;
            $defective_parts_shipped = FALSE;
            foreach ($spare_data as $key => $spare) {

                if (!is_null($spare['parts_shipped'])) {
                    $parts_shipped = true;
                } if (!empty($spare['defective_part_shipped'])) {
                    $defective_parts_shipped = TRUE;
                }
                if ($spare['purchase_price'] > 0) {
                    $estimate_given = TRUE;
                }

/// REQ DETAILS //
                $spare_request[$key]['id'] = $spare['id'];
                $spare_request[$key]['entity_type'] = $spare['entity_type'];
                $spare_request[$key]['model_number'] = $spare['model_number'];
                $spare_request[$key]['part_number'] = $spare['part_number'];
                $spare_request[$key]['original_parts_number'] = $spare['original_parts_number'];
                $spare_request[$key]['parts_requested'] = $spare['parts_requested'];
                $spare_request[$key]['parts_requested_type'] = $spare['parts_requested_type'];
                if ($spare['part_warranty_status'] == 1) {
                $spare_request[$key]['part_warranty_status'] = 'In - Warranty';
                } else {
                $spare_request[$key]['part_warranty_status'] = 'Out Of Warranty';
                }
                $spare_request[$key]['quantity'] = $spare['quantity'];
                $spare_request[$key]['date_of_request'] = $spare['date_of_request'];
                $spare_request[$key]['spare_approval_date'] = $spare['spare_approval_date'];
                $spare_request[$key]['date_of_purchase'] = $spare['date_of_purchase'];
                $spare_request[$key]['invoice_pic'] = $spare['invoice_pic'];
                $spare_request[$key]['serial_number_pic'] = $spare['serial_number_pic'];
                $spare_request[$key]['defective_parts_pic'] = $spare['defective_parts_pic'];
                $spare_request[$key]['defective_back_parts_pic'] = $spare['defective_back_parts_pic'];
                $spare_request[$key]['serial_number'] = $spare['serial_number'];
                $spare_request[$key]['acknowledge_date'] = $spare['acknowledge_date'];
                $spare_request[$key]['remarks_by_sc'] = $spare['remarks_by_sc'];
                $spare_request[$key]['status'] = $spare['status'];
                $spare_request[$key]['part_cancel_reason'] = $spare['part_cancel_reason'];
                if ($spare['is_consumed'] == 1) {
                $spare_request[$key]['is_consumed'] = 'Yes';
                } else {
                $spare_request[$key]['is_consumed'] = 'No';
                }
                $spare_request[$key]['consumed_status'] = $spare['consumed_status'];
                $spare_request[$key]['consumption_remarks'] = $spare['consumption_remarks'];
                $spare_request[$key]['consumed_status'] = $spare['consumed_status'];


// Shipped details//
                if ($parts_shipped) {
                    $spare_shipped[$key]['id'] = $spare['id'];
                    $spare_shipped[$key]['entity_type'] = $spare['entity_type'];
                    $spare_shipped[$key]['parts_shipped'] = $spare['parts_shipped'];
                    $spare_shipped[$key]['shipped_part_number'] = $spare['shipped_part_number'];
                    $spare_shipped[$key]['shipped_parts_type'] = $spare['shipped_parts_type'];
                    $spare_shipped[$key]['shipped_quantity'] = $spare['shipped_quantity'];

                    if ($spare['around_pickup_from_service_center'] == COURIER_PICKUP_REQUEST) {
                    $spare_shipped[$key]['around_pickup_from_service_center'] = 'Pickup Requested';
                    }

                    if ($spare['around_pickup_from_service_center'] == COURIER_PICKUP_SCHEDULE) {
                    $spare_shipped[$key]['around_pickup_from_service_center'] = 'Pickup Schedule';
                    }

                    $spare_shipped[$key]['courier_name_by_partner'] = $spare['courier_name_by_partner'];
                    $spare_shipped[$key]['awb_by_partner'] = $spare['awb_by_partner'];
                    $spare_shipped[$key]['p_box_count'] = $spare['p_box_count'];
                    if (!empty($spare['p_billable_weight'])) {
                        $expl_data = explode('.', $spare['p_billable_weight']);
                        if (!empty($expl_data[0])) {
                            $kg = $expl_data[0] . ' KG ';
                        }
                        if (!empty($expl_data[1])) {
                            $gm = $expl_data[1] . ' Gram';
                        }
                        $spare_shipped[$key]['p_billable_weight'] = $kg . " " . $gm;
                    }
                    $spare_shipped[$key]['shipped_date'] = $spare['shipped_date'];
                    $spare_shipped[$key]['edd'] = $spare['edd'];
                    $spare_shipped[$key]['remarks_by_partner'] = $spare['remarks_by_partner'];
                    $spare_shipped[$key]['partner_challan_number'] = $spare['partner_challan_number'];
                    $spare_shipped[$key]['challan_approx_value'] = $spare['challan_approx_value'];
                    $spare_shipped[$key]['partner_challan_file'] = S3_WEBSITE_URL . 'vendor-partner-docs/' . $spare['partner_challan_file'];
                    $spare_shipped[$key]['courier_pic_by_partner'] = S3_WEBSITE_URL . 'vendor-partner-docs/' . $spare['courier_pic_by_partner'];
                }
/// DEFECTIVE DETAILS//  
                if ($defective_parts_shipped) {
                    $spare_defective[$key]['id'] = $spare['id'];
                    if (!empty($sp['send_defective_to'])) {
                     $spare_defective[$key]['send_defective_to'] = $spare['send_defective_to'];
                    } else {
                     $spare_defective[$key]['send_defective_to'] = ucfirst(_247AROUND_PARTNER_STRING);
                    }

                    $spare_defective[$key]['defective_part_shipped'] = $spare['defective_part_shipped'];
                    $spare_defective[$key]['shipped_part_number'] = $spare['shipped_part_number'];
                    $spare_defective[$key]['shipped_quantity'] = $spare['shipped_quantity'];
                    $spare_defective[$key]['courier_name_by_sf'] = $spare['courier_name_by_sf'];
                    $spare_defective[$key]['awb_by_sf'] = $spare['awb_by_sf'];
                    $spare_defective[$key]['sf_box_count'] = $spare['sf_box_count'];
                    if (!empty($spare['awb_by_sf'])) {
                        if (!empty($spare['sf_billable_weight'])) {
                            $expl_data = explode('.', $spare['sf_billable_weight']);
                            if (!empty($expl_data[0])) {

                                $kg = $expl_data[0] . ' KG ';
                            }
                            if (!empty($expl_data[1])) {
                                $gm = $expl_data[1] . ' Gram';
                            }
                        }
                        $spare_defective[$key]['sf_billable_weight'] = $kg . " " . $gm;
                    }
                    $spare_defective[$key]['courier_charges_by_sf'] = $spare['courier_charges_by_sf'];
                    $spare_defective[$key]['defective_courier_receipt'] = S3_WEBSITE_URL . 'bookings-collateral/misc-images/' . $spare['defective_courier_receipt'];
                    $spare_defective[$key]['defective_part_shipped_date'] = $spare['defective_part_shipped_date'];
                    $spare_defective[$key]['remarks_defective_part_by_sf'] = $spare['remarks_defective_part_by_sf'];
                    $spare_defective[$key]['remarks_defective_part_by_partner'] = $spare['remarks_defective_part_by_partner'];
                    $spare_defective[$key]['received_defective_part_pic_by_wh'] = S3_WEBSITE_URL . 'bookings-collateral/misc-images/' . $spare['defective_courier_receipt'];
                    $spare_defective[$key]['rejected_defective_part_pic_by_wh'] = S3_WEBSITE_URL . 'bookings-collateral/misc-images/' . $spare['rejected_defective_part_pic_by_wh'];
                    $spare_defective[$key]['sf_challan_number'] = $spare['sf_challan_number'];
                }
/// INVOICE DETAILS //
                $spare_invoice[$key]['id'] = $spare['id'];
                $spare_invoice[$key]['model_number_shipped'] = $spare['model_number_shipped'];
                $spare_invoice[$key]['parts_shipped'] = $spare['parts_shipped'];
                $spare_invoice[$key]['shipped_part_number'] = $spare['shipped_part_number'];
                $spare_invoice[$key]['shipped_parts_type'] = $spare['shipped_parts_type'];
                $spare_invoice[$key]['purchase_invoice_id'] = $spare['purchase_invoice_id'];
                $spare_invoice[$key]['sell_invoice_id'] = $spare['sell_invoice_id'];
                $spare_invoice[$key]['reverse_purchase_invoice_id'] = $spare['reverse_purchase_invoice_id'];
                $spare_invoice[$key]['reverse_sale_invoice_id'] = $spare['reverse_sale_invoice_id'];
                $spare_invoice[$key]['warehouse_courier_invoice_id'] = $spare['warehouse_courier_invoice_id'];
                $spare_invoice[$key]['partner_courier_invoice_id'] = $spare['partner_courier_invoice_id'];
                $spare_invoice[$key]['vendor_courier_invoice_id'] = $spare['vendor_courier_invoice_id'];



/// OOW  DETAILS //
                if ($estimate_given) {
                    $spare_oow[$key]['id'] = $spare['id'];
                    if ($spare['entity_type'] == _247AROUND_PARTNER_STRING) {
                        $spare_oow[$key]['entity_type'] = 'Partner';
                    } else {
                        $spare_oow[$key]['entity_type'] = 'Warehouse';
                    }
                    $spare_oow[$key]['purchase_price'] = $spare['purchase_price'];
                    $spare_oow[$key]['estimate_cost_given_date'] = $spare['estimate_cost_given_date'];

                    if (!is_null($spare['incoming_invoice_pdf'])) {
                        if ($spare['incoming_invoice_pdf'] !== '0')
                            $spare_oow[$key]['incoming_invoice_pdf'] = S3_WEBSITE_URL . 'invoices-excel/' . $spare['incoming_invoice_pdf'];
                    }
                    $spare_oow[$key]['sell_invoice_id'] = $spare['sell_invoice_id'];
                    $spare_oow[$key]['status'] = $spare['status'];
                }
            }

            $response['spare_requested'] = $spare_request;
            $response['spare_shipped'] = $spare_shipped;
            $response['spare_defective'] = $spare_defective;
            $response['spare_invoice'] = $spare_invoice;
            $response['spare_oow'] = $spare_oow;
            $this->jsonResponseString['response'] = $response;
            $this->sendJsonResponse(array('0000', "Escalation details updated successfully")); // send success response //
        } else {
            log_message("info", __METHOD__ . $validation['message']);
            $this->jsonResponseString['response'] = array();
            $this->sendJsonResponse(array("1010", "Data Not Found !"));
        }
    }
    
     /*
     * @Desc - This function is used get dashboard filter data
     * @param - 
     * @response - json
     * @Author  - Abhishek Awasthi
     */    
    function gethomeFilters(){
        
        
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        $validation = $this->validateKeys(array("entity_id","entity_type"), $requestData);
        if (!empty($requestData['entity_type'])) { 
        	/* Get Partner , Service , warramty data from  DB */
                  $response = array(); 
                  
                  $response['request_type'] = array('not_set'=>'All','Installation'=>'Installations','Repair_with_part'=>'Repair With Spare','Repair_without_part'=>'Repair Without Spare');
                  $response['warranty'] = array('not_set'=>'All','Yes'=>'Yes (In Warranty)','No'=>'No (Out Of Warranty)');
                  $response['is_upcountry'] = array('not_set'=>'All','Yes'=>'Yes','No'=>'No');
                  $response['booking_status'] = array('not_set'=>'All','Completed'=>'Completed','Cancelled'=>'Cancelled');
                  $response['partners'] = $this->partner_model->getpartner();
                  $serviceWhere['isBookingActive'] =1;
                  $response['services'] = $this->reusable_model->get_search_result_data("services","*",$serviceWhere,NULL,NULL,array("services"=>"ASC"),NULL,NULL,array());
                  $this->jsonResponseString['response'] = $response;
                  
                  $this->sendJsonResponse(array('0000', "Dashboard filters found successfully")); // send success response //
               
        } else {
            log_message("info", __METHOD__ . $validation['message']);
            $this->jsonResponseString['response'] = array(); 
            $this->sendJsonResponse(array("1010", "Dashboard Filters  not found !")); 
        }
        
    }
   
     /*
     * @Desc - This function is used get top 5 SFs
     * @param - 
     * @response - json
     * @Author  - Abhishek Awasthi
     */  
    function getTopRatingSfs(){
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        $validation = $this->validateKeys(array("entity_type","sf_limit"), $requestData);
        if (!empty($requestData['entity_type'])) { 
                 $response = $rating_data = $this->service_centers_model->get_vendor_rating_data_top_5($requestData['sf_limit']);
                 $this->jsonResponseString['response'] = $response;
                 $this->sendJsonResponse(array('0000', "Ratings found successfully")); // send success response //
               
        } else {
            log_message("info", __METHOD__ . $validation['message']);
            $this->jsonResponseString['response'] = array(); 
            $this->sendJsonResponse(array("1008", "Rating details not found !")); 
        }
    }
    
     /*
     * @Desc - This function is used get States TAT Data
     * @param - 
     * @response - json
     * @Author  - Abhishek Awasthi
     */    
    function getStateTATData(){
      
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        $validation = $this->validateKeys(array("entity_type"), $requestData);
        if (!empty($requestData['entity_type'])) { 
            
                   $postData = array(
                      //  "escalation_reason_id" => $requestData['escalation_reason_id'],
                        "call_from_api" => TRUE
                    );
                    //Call curl for updating booking 
                    $url = base_url() . "employee/dashboard/tat_calculation_full_view/00";
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
                    $curl_response = json_decode(curl_exec($ch));
                    curl_close($ch);
                    
                    foreach ($curl_response->TAT as $key=>$value){
                     $state =   $value->entity; 
                     if($state!='Total'){
                     $return['D0'][]  = array('state'=>ucwords($state),'percent'=>$value->TAT_0)  ;
                     //$return['D0'][]['state'][]  = $value->TAT_0  ;
                     $return['D1'][]  = array('state'=>ucwords($state),'percent'=>$value->TAT_1)  ;
                     $return['D2'][] = array('state'=>ucwords($state),'percent'=>$value->TAT_2)  ;
                     $return['D4'][]  = array('state'=>ucwords($state),'percent'=>$value->TAT_3)  ;
                     //$return['D1'][]['state'][]  = $value->TAT_1  ;
                    }
                    }
                    $this->jsonResponseString['response'] = $return;
                    $this->sendJsonResponse(array('0000', "Data found successfully"));
               
        } else {
            log_message("info", __METHOD__ . $validation['message']);
            $this->jsonResponseString['response'] = array(); 
            $this->sendJsonResponse(array("1018", "Data not found !")); 
        }
        
        
    }
    
}