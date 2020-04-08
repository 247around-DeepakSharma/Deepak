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
        $this->load->model("service_centers_model");
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
            $this->apis->saveRequestData($details);

            $activity = array('activity' => 'data input', 'data' => json_encode($details), 'time' => $this->microtime_float());
            $this->apis->logTable($activity);

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
            $this->apis->saveRequestData($details);

            $activity = array('activity' => 'data input', 'data' => json_encode($details), 'time' => $this->microtime_float());
            $this->apis->logTable($activity);

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
        $this->apis->logTable($activity);

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
        $this->apis->logTable($activity);
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
            $this->apis->logTable($activity);
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
            case 'getStates':
                $this->getAllStates();
                break;

             case 'getStatesCities':
                $this->getStatesCities();
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


    /**
     *  @desc : This function is to get all states.
     *
     *  All the distinct states of India in Ascending order From Table state_code
     *
     *  @param : void
     *  @return : json of states
     *  @author : Abhishek Awasthi
     */


function getAllStates(){
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        $validation = $this->validateKeys(array("entity_type"), $requestData);
        $response=array();
        if (!empty($requestData['entity_type'])) { 

                if(!empty($requestData['entity_type']) == _247AROUND_DEALER_STRING){
                    /// Will Come Dealer States Mapped ///
                    $response =  $this->around_generic_lib->getDealerStateMapped($requestData['entity_id']);
                }else{
                    $result =  $this->around_generic_lib->getAllStates();
                    $response = $result['data'];
                }
                
                $this->jsonResponseString['response'] = $response;
                $this->sendJsonResponse(array($response['code'], $response['message'])); // send success response //
               
        } else {
            log_message("info", __METHOD__ . $validation['message']);
            $this->jsonResponseString['response'] = array(); 
            $this->sendJsonResponse(array("1002", "You are now allowed to perform action . Please login again!")); 
        }

}

    /**
     *  @desc : This function is to get all cities of state.
     *
     *  All the distinct states of India in Ascending order  
     *
     *  @param : void
     *  @return : json of cities
     *  @author : Abhishek Awasthi
     */
 

function getStatesCities(){
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        $validation = $this->validateKeys(array("state_code"), $requestData);
        $response=array();
        if (!empty($requestData['state_code'])) { 

        	    if(!empty($requestData['entity_type']) == _247AROUND_DEALER_STRING){
                    /// Will Come Dealer State Cities Mapped ///
                    $response =  $this->around_generic_lib->getDealerStateCitiesMapped($requestData['entity_id'],$requestData['state_code']);
                }else{
                    $result =  $this->around_generic_lib->getStateCities($requestData['state_code']);
                    $response = $result['data'];
                }
                //$response =  $this->around_generic_lib->getStateCities($requestData['state_code']); 
                 $this->jsonResponseString['response'] = $response;
                 $this->sendJsonResponse(array($response['code'], $response['message'])); // send success response //
               
        } else {
            log_message("info", __METHOD__ . $validation['message']);
            $this->jsonResponseString['response'] = array(); 
            $this->sendJsonResponse(array("1002", "You are now allowed to perform action . Please login again!")); 
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

                        $unit_data = $this->booking_model->get_unit_details(array("booking_id" => $value['booking_id']), false, "appliance_brand, appliance_category, appliance_capacity");
                        $data['Bookings'][$key]['appliance_brand'] = $unit_data[0]['appliance_brand'];
                        $data['Bookings'][$key]['appliance_category'] = $unit_data[0]['appliance_category'];
                        $data['Bookings'][$key]['appliance_capacity'] = $unit_data[0]['appliance_capacity'];
                        // Abhishek Send Spare Details of booking //
                        $spares_details = $this->around_generic_lib->getSpareDetailsOfBooking($value['booking_id']);
                        $data['Bookings'][$key]['spares'] =  $spares_details;
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
        $validation = $this->validateKeys(array("booking_id","appliance_id","is_repeat","show_all_capacity"), $requestData);
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





}