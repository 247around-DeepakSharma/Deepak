<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//error_reporting(E_ERROR);
//ini_set('display_errors', '0');

class Api extends CI_Controller {

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
        $this->load->library('notify');
        $this->load->library("miscelleneous");
        $this->load->library('booking_utilities');
        $this->load->library('s3');
        $this->load->library('email');
        $this->load->helper(array('form', 'url'));
        $this->load->library('asynchronous_lib');
        $this->load->library('paytm_payment_lib');
    }

    /**
     * @input: void
     * @description: accepts post request only and basic validations
     * @output: void
     */
    public function index() {
        log_message('info', "Entering: " . __METHOD__. json_encode($_POST, true));
        // echo json_encode($_POST, true); exit();
        
        ob_end_clean();
        //$input_d = file_get_contents('php://input');
        //$_POST = json_decode($input_d, true);
        
        //$str = '{"request":"{\"requestId\":\"249c0d03-5123-487a-9ad9-cbe487ed95bc\",\"requestUrl\":\"engineerLogin\",\"deviceId\":\"MzU4MjQwMDUxMTExMTEwLWU0YmNiNTcwM2MyZjczMGUtMDI6MDA6MDA6MDA6MDA6MDAtbnVsbA\\u003d\\u003d\",\"token\":\"eyJhbGciOiJzaGEyNTYiLCJ0eXAiOiJKV1QifQ\\u003d\\u003d.eyJleHAiOjE1NTYwOTA4NzYsImlhdCI6MTU1NjA5MDY5NiwiaXNzIjoiYm9sb2Fha2EtbW9iaWxlLWFwcGxpY2F0aW9uIiwicXNoIjoie1wicGFzc3dvcmRcIjpcIjc4MjA5Mzk0NjlcIixcIm1ldGhvZFwiOlwicG9zdFwiLFwiYXBwX3ZlcnNpb25cIjpcIjEuMFwiLFwiYXBpUGF0aFwiOlwiYXBpXCIsXCJtb2JpbGVcIjpcIjc4MjA5Mzk0NjlcIixcImRldmljZUluZm9cIjpcIntcXFwiaXNFbXVsYXRvclxcXCI6XFxcImZhbHNlXFxcIixcXFwib3NcXFwiOlxcXCIzLjE4LjkxK1xcXCIsXFxcIm1vZGVsVmVyc2lvblxcXCI6XFxcImdlbmVyaWNfeDg2XFxcIixcXFwicGxhdGZvcm1WZXJzaW9uXFxcIjpcXFwiOC4wLjBcXFwiLFxcXCJtb2RlbFxcXCI6XFxcIkdvb2dsZVxcXCIsXFxcImlzUm9vdGVkXFxcIjpcXFwidHJ1ZVxcXCJ9XCJ9In0\\u003d.NWM2ZjY0NDEyODRmY2ZkNzQ5ZTk1NWVlOGEzOWYzYTJmZTA5MmI2MTk3YzNkZTJjYzJiNWMzZDUwM2JjNWYyNA\\u003d\\u003d\"}"}';
        //$str = '{"request":"{\"requestId\":\"249c0d03-5123-487a-9ad9-cbe487ed95bc\",\"requestUrl\":\"solutionCompleteBooking\",\"deviceId\":\"MzU4MjQwMDUxMTExMTEwLWU0YmNiNTcwM2MyZjczMGUtMDI6MDA6MDA6MDA6MDA6MDAtbnVsbA\\u003d\\u003d\",\"token\":\"eyJhbGciOiJzaGEyNTYiLCJ0eXAiOiJKV1QifQ\\u003d\\u003d.eyJleHAiOjE1NTYwOTA4NzYsImlhdCI6MTU1NjA5MDY5NiwiaXNzIjoiYm9sb2Fha2EtbW9iaWxlLWFwcGxpY2F0aW9uIiwicXNoIjoie1wicGFzc3dvcmRcIjpcIjc4MjA5Mzk0NjlcIixcIm1ldGhvZFwiOlwicG9zdFwiLFwiYXBwX3ZlcnNpb25cIjpcIjEuMFwiLFwiYXBpUGF0aFwiOlwiYXBpXCIsXCJtb2JpbGVcIjpcIjc4MjA5Mzk0NjlcIixcImRldmljZUluZm9cIjpcIntcXFwiaXNFbXVsYXRvclxcXCI6XFxcImZhbHNlXFxcIixcXFwib3NcXFwiOlxcXCIzLjE4LjkxK1xcXCIsXFxcIm1vZGVsVmVyc2lvblxcXCI6XFxcImdlbmVyaWNfeDg2XFxcIixcXFwicGxhdGZvcm1WZXJzaW9uXFxcIjpcXFwiOC4wLjBcXFwiLFxcXCJtb2RlbFxcXCI6XFxcIkdvb2dsZVxcXCIsXFxcImlzUm9vdGVkXFxcIjpcXFwidHJ1ZVxcXCJ9XCJ9In0\\u003d.NWM2ZjY0NDEyODRmY2ZkNzQ5ZTk1NWVlOGEzOWYzYTJmZTA5MmI2MTk3YzNkZTJjYzJiNWMzZDUwM2JjNWYyNA\\u003d\\u003d\"}"}';

        //$_POST = json_decode($str, true);
            
        //print_r($_POST); exit();
        $this->debug = true;
        $this->jsonResponseString = null;
        $this->user = "";

        if ($_POST && array_key_exists("request", $_POST)) {

            $jsonRequestData = $_POST['request'];

            $requestData = json_decode($jsonRequestData, true);
            
            $this->token = $requestData['token'];

            //username is user email address, not her name
            if (array_key_exists("username", $requestData)) {
                $this->user = $requestData['username'];
            }

            $this->requestId = $requestData['requestId'];
            $this->deviceId = $requestData['deviceId'];
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
        $this->deviceId = $this->input->get('deviceId');
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
            case 'getCancellationReasons':
                $this->processGetCancellationReasons();
                break;

            case 'cancelBooking':
                $this->processCancelBooking();
                break;

            case 'rescheduleBooking':
                $this->processRescheduleBooking();
                break;

            case 'engineerLogin':
                $this->processEngineerLogin();
                break;
          
            case 'completeBookingByEngineer':
                $this->processCompleteBookingByEngineer();
                break;
                
            case 'getCancellationReason':
                $this->getCancellationReason();
                break;
                
            case 'cancelBookingByEngineer':
                $this->processCancelBookingByEngineer();
                break;
            
            case 'engineerHomeScreen':
                $this->getEngineerHomeScreen();
                break;
            
            case 'missedBookings':
                $this->getMissedBookings();
                break;
            
            case 'tommorowBookings':
                $this->getTommorowBookings();
                break;
            
            case 'techSupport':
                $this->getTechSupport();
                break;
            
            case 'engineerBookingsByStatus':
                $this->getEngineerBookingsByStatus();
                break;
            
            case 'engineerHeplingDocuments':
                $this->getEngineerHeplingDocuments();
                break;
            
            case 'engineerProfile':
                $this->getEngineerProfile();
                break;
            
            case 'engineerSparePartOrder':
                $this->getEngineerSparePartOrder();
                break;
            
            case 'partTypeOnModelNumber':
                $this->getPartTypeOnModelNumber();
                break;
            
            case 'sparePartName':
                $this->getSparePartName();
                break;
            
            case 'submitSparePartsOrder':
                $this->processSubmitSparePartsOrder();
                break;
            
            case 'bookingProductDetails':
                $this->getBookingProductDetails();
                break;
            
            case 'symptomCompleteBooking':
                $this->getSymptomCompleteBooking();
                break;
            
            case 'defectCompleteBooking':
                $this->getDefectCompleteBooking();
                break;
            
            case 'solutionCompleteBooking':
                $this->getSolutionCompleteBooking();
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
        //$message = "Thanks for joining Aroundhomz. Avail off-season AC service at just Rs290 & get AC cover worth Rs500 free. Offer on App Booking goo.gl/m0iAcS / Call 011-39595200";
        //$message = "Thanks for joining Aroundhomz. Avail off-season AC service at Rs210. All Service charges carry 1 Month Warranty. Book on App goo.gl/m0iAcS / Call 011-39595200";
        //$message = "Thanks for interest in 247Around. Please call us for TV, Refrigerator, Washing Machine, Microwave etc Repair/Service/Installation requirement in Delhi & NCR.";
        $message = "Welcome to 247around, your appliance buddy app. You can use it for Appliance Repair/Service/Installation requirements and check our unique repair diagnostics.";
        $developer_phone = array('8826423424', '9810872244', '8130572244', '9899296372', '8447142491');
        //$developer_phone = array();

        if ($userResult) {
            //Confirm user about number verification
            $this->notify->sendTransactionalSmsMsg91($phone_number, $message,SMS_WITHOUT_TAG);
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

    /**
     * @input: void
     *
     * @description: This function gets called through the Exotel Missed call App Passthru Applet.
     * That applet makes a call to the URL:
     *      https://aroundhomzapp.com/pass-through
     * Through the $route['pass-through'] = 'api/pass_through'; mechanism defined in routes.php,
     * call reaches to this function below which fetches information passed while making a call to
     * this URL and stores all details in the boloaaka.passthru_misscall_log table (function
     * apis->insertPassthruCall($callDetails)).
     * This table is checked again and again in processUserVerificationCode() to see if valid entry
     * is there or not. Once the entry is found, it is parsed and appropriate code is returned.
     *
     * After saving call details, it marks the calling no as Verified in DB only if call was made
     * to app verification no 30017601 and set 200 OK in header.
     *
     * @output: None
     */
    public function pass_through() {
        log_message('info', "Entering: " . __METHOD__);
        
        if($this->input->post()){
            $callDetails = $this->input->post();
        }
        else{
            $activity = array('activity' => 'process exotel request', 'data' => json_encode($_GET), 'time' => $this->microtime_float());
            $this->apis->logTable($activity);

            //Refer: http://support.exotel.in/support/solutions/articles/48283-working-with-passthru-applet
            $callDetails['callSid'] = (isset($_GET['CallSid'])) ? $_GET['CallSid'] : null;
            $callDetails['from_number'] = (isset($_GET['From'])) ? $_GET['From'] : null;
            $callDetails['To'] = (isset($_GET['To'])) ? $_GET['To'] : null;
            $callDetails['Direction'] = (isset($_GET['Direction'])) ? $_GET['Direction'] : null;
            $callDetails['DialCallDuration'] = (isset($_GET['DialCallDuration'])) ? $_GET['DialCallDuration'] : null;
            $callDetails['StartTime'] = (isset($_GET['StartTime'])) ? $_GET['StartTime'] : null;
            $callDetails['EndTime'] = (isset($_GET['EndTime'])) ? $_GET['EndTime'] : null;
            $callDetails['CallType'] = (isset($_GET['CallType'])) ? $_GET['CallType'] : null;
            $callDetails['DialWhomNumber'] = (isset($_GET['DialWhomNumber'])) ? $_GET['DialWhomNumber'] : null;
            $callDetails['digits'] = (isset($_GET['digits'])) ? $_GET['digits'] : null;
            $callDetails['create_date'] = null;
        }
        log_message('info', "call_details_array: " . print_r($callDetails, TRUE));

        //var_dump($apiDetails);
        //insert in database
        $this->apis->insertPassthruCall($callDetails);

        //fetches only the 10 digits of the mobile no without the country code
        $num = substr($callDetails['from_number'], '-10');
        //var_dump($num);

    //User could give missed call on 011-30017601 to verify the App
    //as well as to confirm her istallation. Try both steps below and then
    //leave.
    //If user has given a missed call on 011-30017601 to verify the App,
    //verify the user no in the database.
    //Also, If user has given a missed call on 011-30017601 to confirm installation,
    //tag the booking accordingly.
    if ($callDetails['To'] == PARTNERS_MISSED_CALLED_NUMBER || $callDetails['To'] == PARTNERS_MISSED_CALLED_NUMBER_KNOWLARITY || $callDetails['To'] == PARTNERS_MISSED_CALLED_NUMBER_2) {
            //Send Notification to concerned employee for missed call notification
            $receiverArray['employee'] = explode(",",INSTALLATION_MISSED_CALL_NOTIFICATION_EMPLOYYE_IDS);
            $this->push_notification_lib->create_and_send_push_notiifcation(INSTALLATION_MISSED_CALL_NOTIFICATION,$receiverArray,array());
            
            //verify user phone no first
            $this->apis->verifyUserNumber($num);

            //Check if call has been made from APP
            //Handling case when User is not being Found in DB, sending Installation and Request
            // welcome SMS to the corresponding user and adding the details in Partner Missed Calls table as well
            //1. Sending SMS to the user
            $sms['tag'] = "partner_missed_call_welcome_sms";
            $sms['phone_no'] = $num;
            $sms['smsData'] = '';
            $sms['booking_id'] = '';
            $sms['type'] = "user";
            $sms['type_id'] = '';

            $this->notify->send_sms_msg91($sms);
            //Logging
            log_message('info', __FUNCTION__ . ' Partner Missed Call Welcome SMS has been sent to ' . $num);


            //2. Now adding details in partner_missed_calls table
            //Checking the Case when Number is already present in Table
            //Getting FollowUp Leads
            $leads_followUp = $this->partner_model->get_partner_leads_by_phone_status($num, 'FollowUp');
            //Getting Completed Leads
            $leads_completed = $this->partner_model->get_partner_leads_by_phone_status($num, 'Completed');
            //Getting Cancelled Leads
            $leads_cancelled = $this->partner_model->get_partner_leads_by_phone_status($num, 'Cancelled');
            // a . First checking if FollowUp leads is Present
            if (!empty($leads_followUp)) {

                //Updating Previously present Row, by changing Dates when Phone is present in FollowUp state
                $data['action_date'] = date('Y-m-d H:i:s');
                $data['create_date'] = date('Y-m-d H:i:s');
                $data['update_date'] = date('Y-m-d H:i:s');
                $where = array('id' => $leads_followUp[0]['id']);
                $inserted_id = $this->partner_model->update_partner_missed_calls($where, $data);
                if ($inserted_id) {
                    //Logging
                    log_message('info', __FUNCTION__ . ' Previous Phone has been updated in partner_missed_calls table with no: ' . $num);
                    //Adding details in Booking State Change
                    $this->notify->insert_state_change("", _247AROUND_FOLLOWUP, _247AROUND_FOLLOWUP, "Lead Updated Phone: " . $num, _247AROUND_DEFAULT_AGENT, 
                            _247AROUND_DEFAULT_AGENT_NAME, ACTOR_FOLLOW_UP,NEXT_ACTION_FOLLOW_UP,_247AROUND);
                } else {
                    //Logging
                    log_message('info', __FUNCTION__ . ' Error in adding Phone to partner_missed_calls details ' . $num);
                }
            }
            // b. Checking case when leads is Completed or Cancelled
            else if (!empty($leads_cancelled) || !empty($leads_completed)) {

                // Adding a new Row in Partner missed calls details in case of completed or cancelled
                $data['phone'] = $num;
                $data['action_date'] = date('Y-m-d H:i:s');
                $data['create_date'] = date('Y-m-d H:i:s');
                $inserted_id = $this->partner_model->insert_partner_missed_calls_detail($data);
                if ($inserted_id) {
                    //Logging
                    log_message('info', __FUNCTION__ . ' New Entry for SAME PHONE has been added in partner_missed_calls table with no: ' . $num);
                    //Adding details in Booking State Change
                    $this->notify->insert_state_change("", _247AROUND_FOLLOWUP, _247AROUND_NEW_PARTNER_LEAD, "Lead Added Phone: " . $num, _247AROUND_DEFAULT_AGENT,
                            _247AROUND_DEFAULT_AGENT_NAME, ACTOR_FOLLOW_UP,NEXT_ACTION_FOLLOW_UP,_247AROUND);
                } else {
                    //Logging
                    log_message('info', __FUNCTION__ . ' Error in adding Phone to partner_missed_calls details ' . $num);
                }
            }
            // c. No leads is Present
            else {

                //Condition when Phone is Not Present - Insert New Row
                $data['phone'] = $num;
                $data['action_date'] = date('Y-m-d H:i:s');
                $data['create_date'] = date('Y-m-d H:i:s');
                $inserted_id = $this->partner_model->insert_partner_missed_calls_detail($data);
                if ($inserted_id) {
                    //Logging
                    log_message('info', __FUNCTION__ . ' New Phone has been added in partner_missed_calls table with no: ' . $num);
                    //Adding details in Booking State Change
                    $this->notify->insert_state_change("", _247AROUND_FOLLOWUP, _247AROUND_NEW_PARTNER_LEAD, "Lead Added Phone: " . $num, _247AROUND_DEFAULT_AGENT, 
                            _247AROUND_DEFAULT_AGENT_NAME,ACTOR_FOLLOW_UP,NEXT_ACTION_FOLLOW_UP, _247AROUND);
                } else {
                    //Logging
                    log_message('info', __FUNCTION__ . ' Error in adding Phone to partner_missed_calls details ' . $num);
                }
            }


            //Considering the case for Snapdeal Missed Calls
        } 
        else if($callDetails['To'] == SNAPDEAL_MISSED_CALLED_NUMBER || $callDetails['To'] == SNAPDEAL_MISSED_CALLED_NUMBER_KNOWLARITY){
            //Logging
            log_message('info', __FUNCTION__ . ' Missed call given by Snapdeal customer - Number: ' . $num);
            
            //verify user phone no first
        $this->apis->verifyUserNumber($num);
            
            //find all pending queries for this user now
            $bookings = $this->user_model->booking_history($num, 100, 0);

            //change internal status to show missed call activity if it is
            //a pending query waiting for confirmation and user has given missed
            //call to confirm the installation
            if (count($bookings) > 0) {
                foreach ($bookings as $b) {
                    if (($b['type'] === 'Query' && $b['current_status'] === 'FollowUp') ||
                            $b['current_status'] === "Cancelled" && $b['type'] === 'Query' &&
                            (date('Y-m-d', strtotime($b['create_date'])) > date('Y-m-d',strtotime('-30 days')))) {
                        $d = array('internal_status' => 'Missed_call_confirmed',
                            'closed_date' => NULL,
                            'cancellation_reason' => NULL,
                            'service_center_closed_date' => NULL,
                            'booking_date' => '', 'booking_timeslot' => '',
                            'delivery_date' => date('Y-m-d H:i:s'),
                            'current_status' => 'FollowUp',
                            'query_remarks' => 'Missed call received, Convert to Booking NOW !!!');

                        //check partner status from partner_booking_status_mapping table  
                        $partner_status = $this->booking_model->get_partner_status($b['partner_id'], $d['current_status'], $d['internal_status']);
                        if (!empty($partner_status[0]['partner_current_status']) && !empty($partner_status[0]['partner_internal_status'])) {
                            $d['partner_current_status'] = $partner_status[0]['partner_current_status'];
                            $d['partner_internal_status'] = $partner_status[0]['partner_internal_status'];
                        } else {
                            $d['partner_current_status'] = 'PENDING';
                            $d['partner_internal_status'] = 'Customer_Not_Available';
                            $this->booking_utilities->send_mail_When_no_data_found($d['current_status'], $d['internal_status'], $b['booking_id'], $b['partner_id']);
                        }

                        $r = $this->booking_model->update_booking($b['booking_id'], $d);

                        $this->send_missed_call_confirmation_sms($b);

                        if ($r === FALSE) {
                            log_message('info', __METHOD__ . '=> Booking confirmation '
                                    . 'through missed call failed for ' . $b['booking_id']);

                            //Send email
                            $this->notify->sendEmail(NOREPLY_EMAIL_ID, "anuj@247around.com", "", "", "Query update Failed after Missed Call for Booking ID: " . $b['booking_id'], "", "",QUERY_UPDATE_FAILED_MISSED_CALL, "", $b['booking_id']);
                        } else {
                            log_message('info', __METHOD__ . '=> Booking confirmation '
                                    . 'through missed call succeeded for ' . $b['booking_id']);
                            $u = array('booking_status' => _247AROUND_FOLLOWUP, 'ud_closed_date' => NULL);
                            //Update unit details
                            $this->booking_model->update_booking_unit_details($b['booking_id'], $u);
                             $this->notify->insert_state_change($b['booking_id'], _247AROUND_FOLLOWUP, $b['current_status'], 
                                     "Booking Open After Customer Missed Call",_247AROUND_DEFAULT_AGENT, 
                                     _247AROUND_DEFAULT_AGENT_NAME,ACTOR_FOLLOW_UP,NEXT_ACTION_FOLLOW_UP, _247AROUND);
                        }
                    }
                    else if($b['type'] === 'Booking' && $b['current_status'] === 'Cancelled'){
                        // If Cancelled date belongs to last 7 days only 
                        $today = strtotime(date("Y-m-d"));
                        $cancelled_date = strtotime($b['closed_date']);
                        $datediff = round(($today - $cancelled_date) / (60 * 60 * 24));
                        if($datediff < 8){
                            $postArray['assigned_vendor_id'] =$b['assigned_vendor_id'];
                            $nextDay = date('Y-m-d', strtotime("+1 days"));
                            $postArray['booking_date'] = $nextDay;
                            if(date('w', strtotime($nextDay)) == 7){
                                $postArray['booking_date'] = date('Y-m-d', strtotime("+2 days"));
                            }
                            $postArray['booking_timeslot'] = "4PM-7PM";
                            $postArray['admin_remarks'] = "Booking get Reopend through customer missed call";
                            $postArray['partner_id'] = $b['partner_id'];
                            $reopenBookingUrl = base_url() . "employee/do_background_process/reopen_booking/".$b['booking_id']."/".$b['current_status'];
                            $this->asynchronous_lib->do_background_process($reopenBookingUrl, $postArray);
                        }
                    }
                }
            }else{
                /* When No bookings found for the snapdeal customers on missed call then send sms*/
                $this->send_missed_call_booking_not_found_sms($num);
                log_message('info', __FUNCTION__ . ' Missed call given by customer from 247AROUND App - Number: ' . $num);
            }
        }

        $this->output->set_header("HTTP/1.1 200 OK");
    }

    
    public function pass_through_ac_service() {
        //log_message('info', "Entering: " . __METHOD__);
        
        if($this->input->post()){
            $callDetails = $this->input->post();
        }
        else{
            $activity = array('activity' => 'AC Service Request', 'data' => json_encode($_GET), 'time' => $this->microtime_float());
            $this->apis->logTable($activity);

            //Refer: http://support.exotel.in/support/solutions/articles/48283-working-with-passthru-applet
            $callDetails['callSid'] = (isset($_GET['CallSid'])) ? $_GET['CallSid'] : null;
            $callDetails['from_number'] = (isset($_GET['From'])) ? $_GET['From'] : null;
            $callDetails['To'] = (isset($_GET['To'])) ? $_GET['To'] : null;
            $callDetails['Direction'] = (isset($_GET['Direction'])) ? $_GET['Direction'] : null;
            $callDetails['DialCallDuration'] = (isset($_GET['DialCallDuration'])) ? $_GET['DialCallDuration'] : null;
            $callDetails['StartTime'] = (isset($_GET['StartTime'])) ? $_GET['StartTime'] : null;
            $callDetails['EndTime'] = (isset($_GET['EndTime'])) ? $_GET['EndTime'] : null;
            $callDetails['CallType'] = (isset($_GET['CallType'])) ? $_GET['CallType'] : null;
            $callDetails['DialWhomNumber'] = (isset($_GET['DialWhomNumber'])) ? $_GET['DialWhomNumber'] : null;
            $callDetails['digits'] = (isset($_GET['digits'])) ? $_GET['digits'] : null;
            $callDetails['create_date'] = null;
        }

        //var_dump($apiDetails);
        //insert in database
        $this->apis->insertPassthruCall($callDetails);

        //fetches only the 10 digits of the mobile no without the country code
        $num = substr($callDetails['from_number'], '-10');
        //var_dump($num);

    //User would give missed call on 011-39595450 to make AC service request
        //Once missed call is received, send customer details on email to the team
        //so that the booking can be inserted.
    if ($callDetails['To'] == AC_SERVICE_MISSED_CALLED_NUMBER || $callDetails['To'] == AC_SERVICE_MISSED_CALLED_NUMBER_KNOWLARITY) {
            log_message('info', "AC Service Missed Call Received from: " . $num);
            
            //send email
            $from = NOREPLY_EMAIL_ID;
            $to = NOREPLY_EMAIL_ID;
            $cc = NITS_ANUJ_EMAIL_ID;
            $bcc = '';
            $sub = "AC Service Missed Call Received from: " . $num;
            $body = 'Please schedule AC service for this customer';
                    
            $this->notify->sendEmail($from, $to, $cc, $bcc, $sub, $body, "",AC_MISSED_CALL);
        }
        
    }
    
    /**
     * @desc: This is used to send sms when customer gave a missed call and booking is found
     * @param string $booking
     */
    function send_missed_call_confirmation_sms($booking) {
        //log_message ('info', __METHOD__);

        if($booking['partner_id'] === GOOGLE_FLIPKART_PARTNER_ID){
            $sms['tag'] = "missed_call_confirmed_for_google";
        }else{
            $sms['tag'] = "missed_call_confirmed";
        }
        $sms['phone_no'] = $booking['booking_primary_contact_no'];
        $sms['smsData']['message'] = '';
        $sms['smsData']['service'] = $booking['services'];
        // Check time is greater than 1PM. If time is greater than 1 PM,
        // then set installation date Tommorrow otherwise Today.
        if(date("l") == "Sunday"){
            
            $sms['smsData']['date'] = "Tomorrow";
            
        } else if (date('H') > 13) {
            $sms['smsData']['date'] = "Tomorrow";
        } else {
            $sms['smsData']['date'] = "Today";
        }

        $sms['smsData']['booking_id'] = $booking['booking_id'];
        $sms['booking_id'] = $booking['booking_id'];
        $sms['type'] = "user";
        $sms['type_id'] = $booking['user_id'];

        $this->notify->send_sms_msg91($sms);
    }

    /**
     * @desc: This is used to send sms when customer gave a missed call and booking is NOT found
     * @param string Mobile no
     */
    function send_missed_call_booking_not_found_sms($mobile) {
        //log_message ('info', __METHOD__);
        
    $sms['tag'] = "missed_call_booking_not_found";
    $sms['phone_no'] = $mobile;
        $sms['smsData'] = '';
    $sms['booking_id'] = '';
    $sms['type'] = "user";
    $sms['type_id'] = '';

    $this->notify->send_sms_msg91($sms);
    }

    /**
     * @input: void
     *
     * @description: This function gets called through the Exotel Vendor extn call App Passthru Applet.
     * That applet makes a call to the URL:
     *      https://aroundhomzapp.com/vendor-extn
     * Through the $route['vendor-extn'] = 'api/vendor_extn'; mechanism defined in routes.php,
     * call reaches to this function below which fetches information passed while making a call to
     * this URL and the Vendor extn which gets passed through the Gather Applet.
     *
     * After saving call details, it marks the calling no as Verified in DB and set 200 OK in header.
     *
     * @output: None
     */
    public function vendor_extn() {
        log_message('info', "Entering: " . __METHOD__);

        $activity = array('activity' => 'process vendor extn request', 'data' => json_encode($_GET), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        //Refer: http://support.exotel.in/support/solutions/articles/48283-working-with-passthru-applet
        $callDetails['callSid'] = (isset($_GET['CallSid'])) ? $_GET['CallSid'] : null;
        $callDetails['from_number'] = (isset($_GET['From'])) ? $_GET['From'] : null;
        $callDetails['To'] = (isset($_GET['To'])) ? $_GET['To'] : null;
        $callDetails['Direction'] = (isset($_GET['Direction'])) ? $_GET['Direction'] : null;
        $callDetails['DialCallDuration'] = (isset($_GET['DialCallDuration'])) ? $_GET['DialCallDuration'] : null;
        $callDetails['StartTime'] = (isset($_GET['StartTime'])) ? $_GET['StartTime'] : null;
        $callDetails['EndTime'] = (isset($_GET['EndTime'])) ? $_GET['EndTime'] : null;
        $callDetails['CallType'] = (isset($_GET['CallType'])) ? $_GET['CallType'] : null;
        $callDetails['DialWhomNumber'] = (isset($_GET['DialWhomNumber'])) ? $_GET['DialWhomNumber'] : null;
        $callDetails['digits'] = (isset($_GET['digits'])) ? $_GET['digits'] : null;
        //$callDetails['create_date'] = $this->microtime_float();
        //$callDetails['update_date'] = $this->microtime_float();
        //send test mail with all this info - SEND MAIL DIDN'T WORK HERE SO RELY ON DB TABLE ONLY
        //sendMail("Vendor extn test", print_r($callDetails, true), true);
        //fetches the vendor extn using the 'digits' parameter.
        //NOTE: This parameter comes with a double quote (") before and after the number. You'll have to trim()
        //this parameter for double quotes (") to get the actual digits.
        $extn = trim($callDetails['digits'], "\"");
        log_message('info', "Extn: " . $extn);

        //TODO: Validate extn and set HTTP header accordingly
        $result = $this->apis->gethandymanfromextn($extn);
        if (count($result) > 0) {
            //fetch handyman
            $callDetails['handyman_id'] = $result[0]['id'];
            $callDetails['handyman_phone'] = $result[0]['phone'];

            log_message('info', "Phone number found: " . $callDetails['handyman_phone']);
        } else {
            //TODO: Handle error here
            log_message('info', "Handyman not found, invalid extension");
        }

        //insert in database
        $this->apis->insertPassthruVendorExtnCall($callDetails);

        $this->output->set_header("HTTP/1.1 200 OK");
    }

    /**
     * @input:
     *
     * @description: This function gets called through the Exotel Vendor extn call App Connect Applet.
     * That applet makes a call to the URL:
     *      https://aroundhomzapp.com/get-vendor-phone
     * to get a vendor number as per the vendor selected by the user (through the extn passed).
     *
     * @output: None
     */
    public function getVendorPhoneFromExtn() {
        log_message('info', "Entering: " . __METHOD__);

        $activity = array('activity' => 'get vendor number from extn request', 'data' => json_encode($_GET),
            'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $callSid = (isset($_GET['CallSid'])) ? $_GET['CallSid'] : null;

        //returns the vendor mobile num using the extn parameter.
        //TODO: Should we have some flag which marks the request as serviced????
        $result = $this->apis->checkPassThruVendorExtnLog($callSid);
        if (count($result) > 0) {
            //return handyman mobile number
            log_message('info', "Phone number found & returned");

            $mob_num = $result[0]['handyman_phone'];
        } else {
            //TODO: Handle error here
            log_message('info', "Phone number not found, return Around call center number");

            $mob_num = "08046809276";
        }

        echo $mob_num;
        log_message('info', "Mobile no returned: " . $mob_num);
    }

    /**
     * @input: Subject and Message strings. Flag isTesting is used to send test mails only to Anuj
     * @description: send email
     * @output: Return response string: Success or Fail
     */
    function sendMail($subject, $message, $isTesting) {
        $this->load->library('email');
        $this->email->initialize(array(
            'protocol' => 'smtp',
            'smtp_host' => 'smtp.sendgrid.net',
            'smtp_user' => 'nitinmalhotra',
            'smtp_pass' => 'mandatory16',
            'smtp_port' => 587,
            'crlf' => "\r\n",
            'newline' => "\r\n",
            'mailtype' => 'html'
            )
        );

        $activity = array('activity' => 'send email', 'data' => "Subject: $subject, Message: $message",
            'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $this->email->from('feedback@247around.com', '247around Team');

    if ($isTesting) {
            $this->email->to("anuj.aggarwal@gmail.com");
        } else {
            $this->email->to(NITS_ANUJ_EMAIL_ID);
            //$this->email->cc("anuj.aggarwal@gmail.com");
    }

        $this->email->subject($subject);
        $this->email->message($message);

        if ($this->email->send()) {
            log_message('info', __METHOD__ . ": Mail sent successfully");

            return "Success";
        } else {
            log_message('error', __METHOD__ . ": Mail could not be sent");

            return "Fail";
        }
    }

    /**
     * @input: Subject and Message strings. Flag isTesting is used to send test mails only to Anuj
     * @description: send email
     * @output: Return response string: Success or Fail
     */
    function sendBookingMailToUser($user, $subject, $message, $file, $isTesting) {
        //$activity = array('activity' => 'send booking email to user', 'data' => "Subject: $subject, Message: $message",
        //    'time' => $this->microtime_float());
        //$this->apis->logTable($activity);

        $this->email->from(NOREPLY_EMAIL_ID, '247around Team');

    if ($isTesting) {
            $this->email->to($user);
            $this->email->bcc(ANUJ_EMAIL_ID);
        } else {
            $this->email->to($user);
            $this->email->bcc(NITS_ANUJ_EMAIL_ID);
    }

        $this->email->subject($subject);
        $this->email->message($message);

        if ($file != "")
            $this->email->attach($file, 'attachment', 'booking-snapshot.jpg');

        if ($this->email->send()) {
//            log_message('info', __METHOD__ . ": Mail sent successfully");

            return "Success";
        } else {
//            log_message('error', __METHOD__ . ": Mail could not be sent");

            return "Fail";
        }
    }

    function saveUnitDetails($unit_details, $booking_id, $discount_amount, $service_id, $appliance_id, $state) {
        log_message('info', "Entering: " . __METHOD__);

        $units = json_decode($unit_details, true);
        $count = count($units);
        $i= 0;
        $price_tag = array();
        //Insert unit details corresponding to this booking ID
        foreach ($units as $unit) {
        $b_unit['partner_id'] = _247AROUND;
        $b_unit['appliance_brand'] = $unit['brand'];
        $b_unit['service_id'] = $service_id;
        $b_unit['booking_id'] = $booking_id;
        $b_unit['appliance_id'] = $appliance_id;
        $b_unit['appliance_capacity'] = $unit['capacity'];
        $b_unit['appliance_category'] = $unit['category'];
        $b_unit['model_number'] = $unit['modelNo'];
        $price_tags = $unit['priceTags'];
        $p_explode = explode(",", $price_tags);
        foreach($p_explode as $key => $p_tags){
            $s_charges = $this->partner_model->getPrices( $b_unit['service_id'], $b_unit['appliance_category'], 
                    $b_unit['appliance_capacity'], $this->app_price_mapping_id, trim($p_tags),"");
            if($i == 0){
                $b_unit['around_paid_basic_charges'] = $discount_amount;
                $b_unit['around_net_payable'] = $discount_amount;
            } else{
                $b_unit['around_paid_basic_charges'] = 0;
                $b_unit['around_net_payable'] = 0;
            }
            
            $b_unit['partner_paid_basic_charges'] = 0;
            $b_unit['partner_net_payable'] = 0;
            
            $b_unit['id'] = $s_charges[0]['id'];
            $b_unit['booking_status'] = _247AROUND_PENDING;
            
             $result = $this->booking_model->insert_data_in_booking_unit_details($b_unit, $state , $key);
             array_push($price_tag, $result['price_tags']);
            $i++;
        }
           
        //log_message('info', "Unit Inserted: " . $id_returned);
        }
        
         $this->booking_model->update_request_type($booking_id, $price_tag,array());

//        log_message('info', "No of Units: " . $count);
        return $count;
    }

 
    /**
     * @input: None
     * @description: Get booking calcellation reasons
     * @output:
     */
    function processGetCancellationReasons() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        $activity = array('activity' => 'process get cancellation reasons', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $reasons = $this->apis->getCancellationReasons();
        log_message('info', print_r($reasons, TRUE));

        $this->jsonResponseString['response'] = $reasons;
        $this->sendJsonResponse(array('0000', 'success'));
    }

    /**
     * @input: Booking ID to be cancelled
     * @description: Cancel pre-existing booking
     * @output:
     */
    function processCancelBooking() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        $activity = array('activity' => 'process cancel booking', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $booking_id = $requestData['booking_id'];
        $cancellation_reason = $requestData['reason'];
        
        $data = $this->booking_model->getbooking_history($booking_id);
        $result = $data[0];

        log_message('info', "Booking ID: " . $booking_id . ", Reason: " . $cancellation_reason);

        $this->miscelleneous->process_cancel_form($booking_id, _247AROUND_PENDING, $cancellation_reason, "Cancelled By Customer through Mobile APP", _247AROUND_DEFAULT_AGENT, _247AROUND_DEFAULT_AGENT, _247AROUND, _247AROUND);

        //Send message to User
        $user_profile = $this->apis->getuserProfileid($result['user_id']);
//        log_message('info', "Formatted date: " . $booking_date_formatted);

        //Send cancellation mails to Admin, Vendor and User
        $this->sendCancellationMails($user_profile[0], $result);

        $this->jsonResponseString['response'] = "done";
        $this->sendJsonResponse(array('0000', 'success'));
        
    }

    /**
     * @input: Booking ID to be rescheduled, new date and time
     * @description: Cancel pre-existing booking
     * @output:
     */
    function processRescheduleBooking() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        $activity = array('activity' => 'process reschedule booking', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $booking_id = $requestData['booking_id'];
        $booking_date = $requestData['booking_date'];
        //Format = DD-MM-YYYY for database
        $booking_date_formatted = date("d-m-Y", strtotime($booking_date));
        $booking_time = $requestData['booking_time'];
        $reschedule_date = date('Y-m-d H:i:s');

//        log_message('info', "Booking ID: " . $booking_id);

        $reschedule_details = array(
            'current_status' => 'Rescheduled',
            'update_date' => $reschedule_date,
            'booking_date' => $booking_date_formatted,
            'booking_timeslot' => $booking_time
        );

        $result = $this->apis->updateBooking($booking_id, $reschedule_details);
//        log_message('info', print_r($result, TRUE));

        //Send message to User
        $user_profile = $this->apis->getuserProfileid($result['user_id']);
        $user_phone = $user_profile[0]['phone_number'];

        $booking_date_strings = explode(",", $result['booking_date']);
        $booking_time_strings = explode("-", $result['booking_timeslot']);
        $booking_time_by = trim($booking_time_strings[1]);

        $services = $this->apis->getServiceById($result['service_id']);
        $searched_service = $services[0]['services'];

        //Send cancellation mails to Admin, Vendor and User
        $this->sendRescheduleMails($user_profile[0], $result);

        $message = "Got It ! Your request for $searched_service Repair is rescheduled to $booking_date_strings[0], $booking_time_by. Like us on Facebook goo.gl/Y4L6Hj / 9555000247 - 247Around";

        //log_message('info', "SMS text: " . $message);

        $this->notify->sendTransactionalSmsMsg91($user_phone, $message,SMS_WITHOUT_TAG);
        //$notify = "Sms Sent";

        $this->jsonResponseString['response'] = "done";
        $this->sendJsonResponse(array('0000', 'success'));
        
    }

    function processEngineerLogin(){ 
         $requestData = json_decode($this->jsonRequestData['qsh'], true);
         
         $data = $this->dealer_model->entity_login(array("entity" => "engineer", 
            "active" =>1, "user_id" => $requestData["mobile"], "password" => md5($requestData["password"])));
        if(!empty($data)){ 
            $engineer  = $this->engineer_model->get_engineers_details(array("id" => $data[0]['entity_id']), "service_center_id, name");
            if(!empty($engineer)){
                $sc_agent = $this->service_centers_model->get_sc_login_details_by_id($engineer[0]['service_center_id']);
                $data[0]['service_center_id'] = $engineer[0]['service_center_id'];
                $data[0]['sc_agent_id'] = $sc_agent[0]['id'];
                $data[0]['agent_name'] = $engineer[0]['name'];
                $device['deviceInfo'] = $requestData["deviceInfo"];
                $device["device_id"] = $this->deviceId;
                $device['app_version'] = $requestData["app_version"];
                $this->partner_model->update_login_details($device, array("agent_id" => $data[0]['agent_id']));
                $this->jsonResponseString['response'] = $data[0];
                $this->sendJsonResponse(array('0000', 'success'));
            } else {
                $this->sendJsonResponse(array('0013', 'failure'));
            }
            
        } else {
            $this->sendJsonResponse(array('0012', 'failure'));
        }
    }
    
    function processCompleteBookingByEngineer(){
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        $unitDetails = json_decode($requestData["UnitArray"], true);
     
        $booking_id = $requestData["bookingID"];
        $validation = true;
        foreach($unitDetails as $value){
            $data = array();
            $data["current_status"] = "InProcess";
           
            if($value['isDelivered'] == "false"){
                $data["internal_status"] = _247AROUND_CANCELLED;
            } else {
                
                $data["internal_status"] = _247AROUND_COMPLETED;
            }
            if($value["applianceBroken"] == "false"){
                $data["is_broken"] = 0;
            } else {
                $data["is_broken"] = 1;
            }
           

            if($value['pod'] == "1"){
                if(isset($value["serialNo"])){
                    $data['serial_number'] = $value["serialNo"];
                    $sn_pic_url = $value['bookingID']."_" . $value["unitID"]."_serialNO_".rand(10,100).".png";
                    
                    $this->miscelleneous->generate_image($value["serialNoImage"],$sn_pic_url,"engineer-uploads");
                    
                    $data["serial_number_pic"] = $sn_pic_url;
                    
                } else {
                    $validation = false;
                    break;
                }
               
            }
            $data["closed_date"] = date("Y-m-d H:i:s");
            $data["engineer_id"] = $requestData['engineer_id'];
           // $data["agent_id"] = $requestData['agent_id'];
            $this->engineer_model->update_engineer_table($data, array("unit_details_id" => $value["unitID"], "booking_id" =>$value["bookingID"] ));
        }
        
        if($validation){
            $sign_pic_url = $booking_id."_sign_".rand(10,100).".png";
                   
            $this->miscelleneous->generate_image($requestData["SignatureEncode"],$sign_pic_url,"engineer-uploads");
            
            $en["amount_paid"] = $requestData["amountPaid"];
            $en["booking_id"] = $booking_id;
            $en["signature"] = $sign_pic_url;
            $en['closed_date'] = date("Y-m-d H:i:s");
            $bookinghistory = $this->booking_model->getbooking_history($booking_id);
            if(!empty($requestData['location']) ){
                $location = json_decode($requestData['location'], true);
                $en["pincode"] = $location['pincode'];
                if($bookinghistory[0]['booking_pincode'] != $location['pincode']){
                    $en['mismatch_pincode']  = 1;
                }
                $en["city"] = $location['city'];
                $en["address"] = $location['address'];
                $en["latitude"] = $location['latitude'];
                $en["longitude"] = $location['longitude'];
               
            }
            $en["remarks"] = $requestData['remarks'];
            $en["service_center_id"] = $requestData['service_center_id'];
            $en["engineer_id"] = $requestData['engineer_id'];
            $is_exist = $this->engineer_model->get_engineer_sign("id", array("service_center_id" => $requestData['service_center_id'], "booking_id" => $booking_id));
            if(!empty($is_exist)){
                $this->engineer_model->update_engineer_action_sig(array("id"=> $is_exist[0]['id']), $en);
            } else {
                $this->engineer_model->insert_engineer_action_sign($en);
            }
            $actor = $next_action = 'not_define';
            $partner_status = $this->booking_utilities->get_partner_status_mapping_data($data["current_status"] , $data['internal_status'], "", $booking_id);
            if (!empty($partner_status)) {
                $booking['partner_current_status'] = $partner_status[0];
                $booking['partner_internal_status'] = $partner_status[1];
                $actor = $booking['actor'] = $partner_status[2];
                $next_action = $booking['next_action'] = $partner_status[3];
            }
            $this->booking_model->update_booking($booking_id, $booking);
            $this->notify->insert_state_change($booking_id, ENGINEER_COMPLETE_STATUS, _247AROUND_PENDING, "Booking Updated By Engineer From App", 
                    $requestData['sc_agent_id'], "", $actor,$next_action,NULL, $requestData['service_center_id']);
            
            $this->sendJsonResponse(array('0000', 'success'));
        } else {
            
            $this->sendJsonResponse(array('0018', 'Please Add Serial Number'));
        }  
    }
    
    function getCancellationReason(){
        $where = array('reason_of' => 'vendor', 'show_on_app'=> 1);
        $reason = $this->booking_model->cancelreason($where);
        $this->jsonResponseString['cancellationReason'] = $reason;
        $this->sendJsonResponse(array('0000', 'success'));
    }
    
    function processCancelBookingByEngineer(){
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        if(!empty($requestData["bookingID"]) && !empty($requestData["cancellationReason"])){
            
            $data["booking_id"] = $requestData["bookingID"];
            $data['engineer_id'] = $requestData["engineer_id"];
            $data['current_status'] = "InProcess";
            $data['internal_status'] = _247AROUND_CANCELLED;
            $data["closed_date"] = date("Y-m-d H:i:s");
            $this->engineer_model->update_engineer_table($data, array( "booking_id" =>$requestData["bookingID"] ));
            
            $en["booking_id"] = $requestData["bookingID"];
            $en["remarks"] = $requestData["cancellationReason"];
            $en['closed_date'] = date("Y-m-d H:i:s");
            $bookinghistory = $this->booking_model->getbooking_history($requestData["bookingID"]);
            if(!empty($requestData['location']) ){
                $location = json_decode($requestData['location'], true);
                $en["pincode"] = $location['pincode'];
                $en["city"] = $location['city'];
                $en["address"] = $location['address'];
                $en["latitude"] = $location['latitude'];
                $en["longitude"] = $location['longitude'];
                if($bookinghistory[0]['booking_pincode'] != $location['pincode']){
                    $en['mismatch_pincode']  = 1;
                }
               
            }
            $en["service_center_id"] = $requestData['service_center_id'];
            $en["engineer_id"] = $requestData['engineer_id'];
            $is_exist = $this->engineer_model->get_engineer_sign("id", array("service_center_id" => $requestData['service_center_id'], "booking_id" => $data["booking_id"]));
            if(!empty($is_exist)){
                $this->engineer_model->update_engineer_action_sig(array("id"=> $is_exist[0]['id']), $en);
            } else {
                $this->engineer_model->insert_engineer_action_sign($en);
            }
           
            $this->notify->insert_state_change($requestData["bookingID"], $requestData["cancellationReason"], _247AROUND_PENDING, 
                    "Booking Cancelled By Engineer From App", 
                    $requestData['sc_agent_id'], "",ACTOR_BOOKING_CANCELLED,NEXT_ACTION_CANCELLED_BOOKING, NULL, $requestData['service_center_id']);
            
            $this->sendJsonResponse(array('0000', 'success'));
             
        } else {
            $this->sendJsonResponse(array('0019', 'Failure'));
        }
    }

    function makeRequestDataArray() {
        $reqData = json_decode($this->jsonRequestData['qsh'], true);
        $array = array();
        for ($i = 0; $i < count($reqData); $i++) {
            $val = $reqData[$i];
            $array[$val['name']] = $val['value'];
        }
        return $array;
    }

    function makeAssocArray($data) {
        $reqData = json_decode($data, true);
        $array = array();
        for ($i = 0; $i < count($reqData); $i++) {
            $val = $reqData[$i];
            $array[$val['name']] = $val['value'];
        }
        return $array;
    }

    /**
     * @input: child key array and parent of child keys
     * @description: check valid JSON keys
     * @output: valid JSON keys
     */
    function checkJsonKeys($childArray, $parent = null) {
        $childArray = array_change_key_case($childArray, CASE_LOWER);
        $childArrayKey = array_keys($childArray);
        foreach ($childArrayKey as $childArrayKeys) {
            if (is_string($childArray[$childArrayKeys])) {
                $this->number = $this->checkValidKeys(strtolower($childArrayKeys), $parent) * $this->number;
            } else if (is_bool($childArray[$childArrayKeys])) {
                $this->number = $this->checkValidKeys(strtolower($childArrayKeys), $parent) * $this->number;
            } else if ($this->isAssociative($childArray[$childArrayKeys]) == 1) {
                $this->checkJsonKeys($childArray[$childArrayKeys], $childArrayKeys);
            } else if (is_array($childArray[$childArrayKeys])) {
                for ($i = 0; $i < sizeof($childArray[$childArrayKeys]); $i++) {
                    if (!is_string($childArray[$childArrayKeys][$i])) {
                        $this->checkJsonKeys($childArray[$childArrayKeys][$i], $childArrayKeys);
                    }
                }
            } else {
                return array("1001.1", "failure");
            }
        }
    }

    /**
     * @input: IP Address
     * @description: find the location of the user according to the ip address
     */
    function findLocationByIpNumber($ipaddress, $email) {

        //convert ip address into ip number
        $ipno = $this->Dot2LongIP($ipaddress);
        //find location according to ip number
        $getLocationFromIpNo = $this->apis->getIp2Location($ipno);
        if ($getLocationFromIpNo) {
            $latitude = $getLocationFromIpNo[0]['latitude'];
            $longitude = $getLocationFromIpNo[0]['longitude'];
            $country = $getLocationFromIpNo[0]['country_name'];

            $getLocation = $this->apis->getLastLocation($latitude, $longitude, $email);
            if (!$getLocation) {
                //saving location into database
                $location = array('latitude' => $latitude, 'longitude' => $longitude);

                $this->apis->saveLocation($location, $email, $country);
            }
        }
    }

    /**
     * @input: Ipaddress
     * @description: Converts ipaddress to ip number
     * @output: Ip number
     */
    function Dot2LongIP($Ipaddress) {
        if ($Ipaddress == "") {
            return 0;
        } else {
            $ips = explode(".", $Ipaddress);
            //print_r($ips);
            return ($ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256);
        }
    }

    /**
     * @input: Array
     * @description: Check array is associative or not
     * @output: true/false
     */
    function isAssociative($array) {
        return (bool) count(array_filter(array_keys($array), 'is_string'));
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

    /**
     * Simple function to replicate PHP 5 behaviour
     */
    function microtime_float() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }


    /**
     * @description: Function to send New User notification email to Admin
     * @param : User details object and flags array
     * @return : None
     */
    function sendNewUserEmail($userResult) {
        log_message('info', __METHOD__);

        $id = $userResult[0]['user_id'];
        $name = $userResult[0]['name'];
        $email = $userResult[0]['user_email'];
        $device_id = $userResult[0]['device_id'];
        $phone_number = $userResult[0]['phone_number'];
        $install_source = $userResult[0]['install_source'];
        $account_email = $userResult[0]['account_email'];
        $existing_flags = explode(",", $userResult[0]['existing_flags']);
        $src1 = "com.google.android";
        $src2 = "com.android.vending";

        log_message('info', "Flags: " . $existing_flags[0] . $existing_flags[1] .
            $existing_flags[2] . $existing_flags[3]);

        if ($userResult[0]['existing_flags'] == "Yes,Yes,Yes,Yes")
            $subject = "Existing User Re-installed Around";
        else {
            if ($existing_flags[3] == "No")
                $subject = "New User Joined Around";
            else
                $subject = "User Installed Around - Check further details";
        }

        $message = $name . " with phone number " . $phone_number
            . " and email " . $email . " joined Around !!! <br/><br/>";

        $message .= "<p>Notes:</p>";

        if (strstr($install_source, $src1) || strstr($install_source, $src2))
            $message = $message . "Install Source: Google" . "<br/>";
        else
            $message = $message . "Install Source: " . $install_source . "<br/>";

        $message = $message . "Playstore Email <b>" . $account_email . "</b> existed: " .
            $existing_flags[3] . "<br/>";

        $message = $message . "Phone number <b>" . $phone_number . "</b> existed: " .
            $existing_flags[1] . "<br/>";

        $message = $message . "Device ID <b>" . $device_id . "</b> existed: " .
            $existing_flags[0] . "<br/>";

        $message = $message . "Phone number & Dev Id combination existed:" .
            $existing_flags[2] . "<br/>";

        $this->sendMail($subject, $message, false);
    }

    function getservice($services) {
        $service_id = $this->apis->getservice_id($services);
        print_r($service_id);
    }

    function sendBookingMails($user, $booking, $service_name) {
        log_message('info', __METHOD__);

        //User details
        $name = $user['name'];
       // $user_email = $user['user_email'];
        $phone_number = $user['phone_number'];
//        log_message('info', "Name: " . $name  . ", Phone: " . $phone_number);

        //Booking details
       // $service_id = $booking['service_id'];

        $booking_id = $booking['booking_id'];
        $booking_date = $booking['booking_date'];
        $booking_time = $booking['booking_timeslot'];
        $booking_comments = $booking['booking_remarks'];
        $units_saved = $booking['quantity'];
        $booking_address = $booking['booking_address'];
        $booking_pincode = $booking['booking_pincode'];
        $discount = $booking['discount_amount'];
        $amount_due = $booking['amount_due'];

//        log_message('info', print_r($booking, TRUE));

        //TEMP: Need to use template for sending email to user
        $user_email = "anuj@247around.com, booking@247around.com";

        $subject = "Booking confirmation from 247Around Mobile App";

        $message = "Dear $name ($phone_number),<br/><br/>Thank you for allowing us to assist you. Please note down your booking details:<br/>";
        $message .= "Booking ID: " . $booking_id . "<br/>";
        $message .= "Appliance: " . $service_name . "<br/>";
        $message .= "Booking Date: " . $booking_date . ", Time: " . $booking_time . "<br/>";
        $message .= "Booking comments: " . $booking_comments . "<br/>";
        $message .= "Number of Appliances: " . $units_saved . "<br/>";
        $message .= "Booking address / pincode: " . $booking_address . " / " . $booking_pincode . "<br/>";
        $message .= "Discount: " . $discount . "<br/>";
        $message .= "<br/>Total amount due: Rs. " . $amount_due . "<br/>";

//        log_message('info', "Booking message: " . $message);

        $this->sendBookingMailToUser($user_email, $subject, $message, "", FALSE);
    }

    function sendCancellationMails($user, $booking) {
        log_message('info', __METHOD__);

        //User details
        $name = $user['name'];
        $user_email = $user['user_email'];
        $phone_number = $user['phone_number'];
        log_message('info', "Name: " . $name . ", Email: " . $user_email . ", Phone: " . $phone_number);

        //Booking details
        $service_id = $booking['service_id'];
        $services = $this->apis->getServiceById($service_id);
        $service_name = $services[0]['services'];

        $booking_id = $booking['booking_id'];
        $booking_date = $booking['booking_date'];
        $booking_time = $booking['booking_timeslot'];
        $units_saved = 1;
        $booking_address = $booking['booking_address'];
        $booking_pincode = $booking['booking_pincode'];
        $amount_due = $booking['amount_due'];
        $cancellation_reason = $booking['cancellation_reason'];

        log_message('info', print_r($booking, TRUE));

        //TEMP: Need to use template for sending email to user
        $user_email = DEVELOPER_EMAIL.', booking@247around.com';

        $subject = "Booking Cancellation from 247Around";

        $message = "Dear $name ($phone_number),<br/><br/>Booking with below details stands cancelled as per your request:<br/>";
        $message .= "Booking ID: " . $booking_id . "<br/>";
        $message .= "Appliance: " . $service_name . "<br/>";
        $message .= "Booking Date: " . $booking_date . ", Time: " . $booking_time . "<br/>";
        $message .= "Number of Appliances: " . $units_saved . "<br/>";
        $message .= "Booking address / pincode: " . $booking_address . " / " . $booking_pincode . "<br/>";
        $message .= "Total amount due: Rs. " . $amount_due . "<br/>";

        $message .= "<br/>Cancellation reason " . $cancellation_reason . "<br/>";

        log_message('info', "Booking message: " . $message);

        $this->sendBookingMailToUser($user_email, $subject, $message, "", FALSE);
    }

    function sendRescheduleMails($user, $booking) {
        log_message('info', __METHOD__);

        //User details
        $name = $user['name'];
        $user_email = $user['user_email'];
        $phone_number = $user['phone_number'];
        log_message('info', "Name: " . $name . ", Email: " . $user_email . ", Phone: " . $phone_number);

        //Booking details
        $service_id = $booking['service_id'];
        $services = $this->apis->getServiceById($service_id);
        $service_name = $services[0]['services'];

        $booking_id = $booking['booking_id'];
        $booking_date = $booking['booking_date'];
        $booking_time = $booking['booking_timeslot'];
        $units_saved = $booking['quantity'];
        $booking_address = $booking['booking_address'];
        $booking_pincode = $booking['booking_pincode'];
        $amount_due = $booking['amount_due'];

        log_message('info', print_r($booking, TRUE));

        //TEMP: Need to use template for sending email to user
        $user_email = DEVELOPER_EMAIL.', booking@247around.com';

        $subject = "Booking Rescheduling from 247Around";

        $message = "Dear $name ($phone_number),<br/><br/>Booking with below details stands rescheduled as per your request:<br/>";
        $message .= "Booking ID: " . $booking_id . "<br/>";
        $message .= "Appliance: " . $service_name . "<br/>";
        $message .= "New Booking Date: " . $booking_date . ", New Time: " . $booking_time . "<br/>";
        $message .= "Number of Appliances: " . $units_saved . "<br/>";
        $message .= "Booking address / pincode: " . $booking_address . " / " . $booking_pincode . "<br/>";
        $message .= "Total amount due: Rs. " . $amount_due . "<br/>";

        log_message('info', "Booking message: " . $message);

        $this->sendBookingMailToUser($user_email, $subject, $message, "", FALSE);
       
        $this->notify->insert_state_change($booking_id, _247AROUND_RESCHEDULED, _247AROUND_PENDING, "Booking Rescheduled By Customer From App", 
               _247AROUND_DEFAULT_AGENT, "247Around", ACTOR_RESCHEDULED_BY_CUSTOMER,RESCHEDULED_BY_CUSTOMER_NEXT_ACTION,_247AROUND);
    }
    
    /**
     * @input: void
     *
     * @description: This function gets called through the Exotel Missed call App Passthru Applet.
     * That applet makes a call to the URL:
     *      https://aroundhomzapp.com/pass_through_android_app
     * Through the $route['pass-through-android-app'] = 'api/pass_through_android_app'; mechanism defined in routes.php,
     * call reaches to this function below which fetches information passed while making a call to
     * this URL and stores all details in the boloaaka.passthru_misscall_log table (function
     * apis->insertPassthruCall($callDetails)).
     * This table is checked again and again in processUserVerificationCode() to see if valid entry
     * is there or not. Once the entry is found, it is parsed and appropriate code is returned.
     *
     * After saving call details, it marks the calling no as Verified in DB only if call was made
     * to app verification no 30017601 and set 200 OK in header.
     *
     * @output: None
     */
    public function pass_through_android_app() {
        //log_message('info', "Entering: " . __METHOD__);
        if($this->input->post()){
            $callDetails = $this->input->post();
        }
        else{
            $activity = array('activity' => 'process exotel request', 'data' => json_encode($_GET), 'time' => $this->microtime_float());
            $this->apis->logTable($activity);

            //Refer: http://support.exotel.in/support/solutions/articles/48283-working-with-passthru-applet
            $callDetails['callSid'] = (isset($_GET['CallSid'])) ? $_GET['CallSid'] : null;
            $callDetails['from_number'] = (isset($_GET['From'])) ? $_GET['From'] : null;
            $callDetails['To'] = (isset($_GET['To'])) ? $_GET['To'] : null;
            $callDetails['Direction'] = (isset($_GET['Direction'])) ? $_GET['Direction'] : null;
            $callDetails['DialCallDuration'] = (isset($_GET['DialCallDuration'])) ? $_GET['DialCallDuration'] : null;
            $callDetails['StartTime'] = (isset($_GET['StartTime'])) ? $_GET['StartTime'] : null;
            $callDetails['EndTime'] = (isset($_GET['EndTime'])) ? $_GET['EndTime'] : null;
            $callDetails['CallType'] = (isset($_GET['CallType'])) ? $_GET['CallType'] : null;
            $callDetails['DialWhomNumber'] = (isset($_GET['DialWhomNumber'])) ? $_GET['DialWhomNumber'] : null;
            $callDetails['digits'] = (isset($_GET['digits'])) ? $_GET['digits'] : null;
            $callDetails['create_date'] = null;
        }

        //var_dump($apiDetails);
        //insert in database
        $this->apis->insertPassthruCall($callDetails);

        //fetches only the 10 digits of the mobile no without the country code
        $num = substr($callDetails['from_number'], '-10');
        //var_dump($num);

    //User could give missed call on 01139585684 to verify the App

    if ($callDetails['To'] == ANDROID_APP_MISSED_CALLED_NUMBER || $callDetails['To'] == ANDROID_APP_MISSED_CALLED_NUMBER_KNOWLARITY) {
        //verify user phone no first
        $this->apis->verifyUserNumber($num);
            
        //Adding details in Log File
        log_message('info', __FUNCTION__ . ' Missed call given by customer from 247AROUND App - Number: ' . $num);
    }

        $this->output->set_header("HTTP/1.1 200 OK");
    }
    
   

    
    function getEngineerHomeScreen(){
        log_message("info", __METHOD__. " Entering..");
        $response = array();
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //$requestData = array("engineer_id" => 1, "service_center_id" => 1);
        if (!empty($requestData["engineer_id"]) && !empty($requestData["service_center_id"])) {
            $select = "count(booking_details.booking_id) as bookings";
            $slot_select = 'booking_details.booking_id, booking_details.booking_date, users.name, booking_details.booking_address, booking_details.state, booking_unit_details.appliance_brand, services.services, booking_details.request_type,'
                    . 'booking_pincode, booking_primary_contact_no, booking_timeslot, booking_unit_details.appliance_category, booking_unit_details.appliance_capacity, booking_details.amount_due, booking_details.partner_id, booking_details.service_id';
            $missed_bookings_count = $this->getMissedBookingList($select, $requestData["service_center_id"], $requestData["engineer_id"]);
            $tommorow_bookings_count = $this->getTommorowBookingList($select, $requestData["service_center_id"], $requestData["engineer_id"]);
            $morning_slot_bookings = $this->getTodaysSlotBookingList($slot_select, TIMESLOT_10AM_TO_1PM, $requestData["service_center_id"], $requestData["engineer_id"]);
            $noon_slot_bookings = $this->getTodaysSlotBookingList($slot_select, TIMESLOT_1PM_TO_4PM, $requestData["service_center_id"], $requestData["engineer_id"]);
            $evening_slot_bookings = $this->getTodaysSlotBookingList($slot_select, TIMESLOT_4PM_TO_7PM, $requestData["service_center_id"], $requestData["engineer_id"]);
            
            $response['missedBookingsCount'] = $missed_bookings_count[0]['bookings'];
            $response['tomorrowBookingsCount'] = $tommorow_bookings_count[0]['bookings'];
            $response['todayMorningBooking'] = $morning_slot_bookings;
            $response['todayAfternoonBooking'] = $noon_slot_bookings;
            $response['todayEveningBooking'] = $evening_slot_bookings;
            
            log_message("info", __METHOD__ . "Bookings Found Successfully");
            $this->jsonResponseString['response'] = $response;
            $this->sendJsonResponse(array('0000', 'success'));
        }
        else{
            log_message("info", __METHOD__ . " Engineer ID Not Found - " . $requestData["engineer_id"]." or Service Center Id not found - ".$requestData["service_center_id"]);
            $this->sendJsonResponse(array('0022', 'Booking ID Not Found'));
        }
    }
    
    function getMissedBookingList($select, $service_center_id, $engineer_id){
            $missed_where = array(
                        "assigned_vendor_id" => $service_center_id,
                        "assigned_engineer_id" => $engineer_id,
                        "engineer_booking_action.internal_status != '"._247AROUND_CANCELLED."'" => NULL,
                        "(booking_details.current_status = '"._247AROUND_PENDING."' OR booking_details.current_status = '"._247AROUND_RESCHEDULED."')" => NULL
                    );
            $missed_slots = $this->apis->getMissedBookingSlots();
            if($missed_slots){
                if(count($missed_slots) == "1"){
                    $missed_where["(DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_date, '%d-%m-%Y')) > 0) OR  ( (DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_date, '%d-%m-%Y')) = 0) AND booking_timeslot = '".$missed_slots[0]."')"] = NULL;
                }
                if(count($missed_slots) == "2"){
                    $missed_where["(DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_date, '%d-%m-%Y')) > 0) OR  ( (DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_date, '%d-%m-%Y')) = 0) AND (booking_timeslot = '".$missed_slots[0]."' OR booking_timeslot = '".$missed_slots[1]."'))"] = NULL;
                }
            }
            else{
                $missed_where["(DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_date, '%d-%m-%Y')) > 0)"] = NULL;
            }
            
            $missed_bookings = $this->engineer_model->get_engineer_booking_details($select, $missed_where, true, true, true, false, false);
            return $missed_bookings;
    }
    
    function getTommorowBookingList($select, $service_center_id, $engineer_id){
        log_message("info", __METHOD__. " Entering..");
        $where = array(
                    "assigned_vendor_id" => $service_center_id,
                    "assigned_engineer_id" => $engineer_id,
                    "engineer_booking_action.internal_status != '"._247AROUND_CANCELLED."'" => NULL,
                    "(DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_date, '%d-%m-%Y')) = -1)" => NULL,
                    "(booking_details.current_status = '"._247AROUND_PENDING."' OR booking_details.current_status = '"._247AROUND_RESCHEDULED."')" => NULL
                );
        $tommorow_bookings = $this->engineer_model->get_engineer_booking_details($select, $where, true, true, true, false, false);
        return $tommorow_bookings;
    }
    
    function getTodaysSlotBookingList($select, $slot, $service_center_id, $engineer_id){
        log_message("info", __METHOD__. " Entering..");
        $where = array(
                    "assigned_vendor_id" => $service_center_id,
                    "assigned_engineer_id" => $engineer_id,
                    "booking_timeslot" => $slot,
                    "engineer_booking_action.internal_status != '"._247AROUND_CANCELLED."'" => NULL,
                    "(DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_date, '%d-%m-%Y')) = 0)" => NULL,
                    "(booking_details.current_status = '"._247AROUND_PENDING."' OR booking_details.current_status = '"._247AROUND_RESCHEDULED."')" => NULL
                );
        $bookings = $this->engineer_model->get_engineer_booking_details($select, $where, true, true, true, false, false);
        return $bookings;
    }
    
    function getMissedBookings(){
        log_message("info", __METHOD__. " Entering..");
        $response = array();
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //$requestData = array("engineer_id" => 1, "service_center_id" => 1);
        if (!empty($requestData["engineer_id"]) && !empty($requestData["service_center_id"])) {
            $select = "booking_details.booking_id, booking_details.booking_date, users.name, booking_details.booking_address, booking_details.state, booking_unit_details.appliance_brand, services.services, booking_details.request_type,"
                    . "booking_pincode, booking_primary_contact_no, booking_timeslot, booking_unit_details.appliance_category, booking_unit_details.appliance_category, booking_unit_details.appliance_capacity, booking_details.amount_due, booking_details.partner_id, booking_details.service_id";
            $response['missedBooking'] = $this->getMissedBookingList($select, $requestData["service_center_id"], $requestData["engineer_id"]);
            log_message("info", __METHOD__ . "Missed Bookings Found Successfully");
            $this->jsonResponseString['response'] = $response;
            $this->sendJsonResponse(array('0000', 'success'));
        }
        else{
            log_message("info", __METHOD__ . " Engineer ID Not Found - " . $requestData["engineer_id"]." or Service Center Id not found - ".$requestData["service_center_id"]);
            $this->sendJsonResponse(array('0023', 'Engineer ID or Service Center Id not found'));
        }
    }
    
    function getTommorowBookings(){
        log_message("info", __METHOD__. " Entering..");
        $response = array();
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //$requestData = array("engineer_id" => 1, "service_center_id" => 1);
        if (!empty($requestData["engineer_id"]) && !empty($requestData["service_center_id"])) {
            $select = "booking_details.booking_id, booking_details.booking_date, users.name, booking_details.booking_address, booking_details.state, booking_unit_details.appliance_brand, services.services, booking_details.request_type,"
                    . "booking_pincode, booking_primary_contact_no, booking_timeslot, booking_unit_details.appliance_category, booking_unit_details.appliance_category, booking_unit_details.appliance_capacity, booking_details.amount_due, booking_details.partner_id, booking_details.service_id";
            $response['tomorrowBooking'] = $this->getTommorowBookingList($select, $requestData["service_center_id"], $requestData["engineer_id"]);
            log_message("info", __METHOD__ . "Tommorow Bookings Found Successfully");
            $this->jsonResponseString['response'] = $response;
            $this->sendJsonResponse(array('0000', 'success'));
        }
        else{
            log_message("info", __METHOD__ . " Engineer ID Not Found - " . $requestData["engineer_id"]." or Service Center Id not found - ".$requestData["service_center_id"]);
            $this->sendJsonResponse(array('0024', 'Engineer ID or Service Center Id not found'));
        }
    }
    
    function getEngineerBookingsByStatus(){ 
        log_message("info", __METHOD__. " Entering..");
        $response = array();
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //$requestData = array("engineer_id" => 1, "service_center_id" => 1, "booking_status" => "Completed");
        if (!empty($requestData["engineer_id"]) && !empty($requestData["service_center_id"]) && !empty($requestData["booking_status"])) {
            if($requestData["booking_status"] == _247AROUND_CANCELLED || $requestData["booking_status"] == _247AROUND_COMPLETED){
                $select = "booking_details.booking_id, booking_details.booking_date, users.name, booking_details.booking_address, booking_details.state, booking_unit_details.appliance_brand, services.services, booking_details.request_type,"
                    . "booking_pincode, booking_primary_contact_no, booking_timeslot, booking_unit_details.appliance_category, booking_unit_details.appliance_category, booking_unit_details.appliance_capacity, booking_details.amount_due, booking_details.partner_id, booking_details.service_id";
            
                $where = array(
                    "assigned_vendor_id" => $requestData["service_center_id"],
                    "assigned_engineer_id" => $requestData["engineer_id"],
                );

                if($requestData["booking_status"] == _247AROUND_CANCELLED){
                    $where["engineer_booking_action.internal_status = '"._247AROUND_CANCELLED."'"] = NULL;
                }
                else{
                    $where["engineer_booking_action.internal_status = '"._247AROUND_COMPLETED."'"] = NULL;
                }
                $response['cancelledBookings'] = $this->engineer_model->get_engineer_booking_details($select, $where, true, true, true, false, false);

                log_message("info", __METHOD__ . "Bookings Found Successfully");
                $this->jsonResponseString['response'] = $response;
                $this->sendJsonResponse(array('0000', 'success'));
            }
            else{
                log_message("info", __METHOD__ . "Incorrect Booking Status");
                $this->sendJsonResponse(array('0025', 'Incorrect Booking Status'));
            }
        }
        else{
            log_message("info", __METHOD__ . " Engineer ID Not Found - " . $requestData["engineer_id"]." or Service Center Id not found - ".$requestData["service_center_id"]);
            $this->sendJsonResponse(array('0026', 'Engineer ID Not Found or Service Center Id or Booking Status Not found'));
        }
    }
    
    function getTechSupport(){
        log_message("info", __METHOD__. " Entering..");
        $response = array();
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //$requestData = array("booking_id" => "PB-16565919051532");
        if (!empty($requestData["booking_id"])) {
            $tech_support = $this->apis->techSupportNumberForEngineer($requestData["booking_id"]);
            if(!empty($tech_support)){
                $response = $tech_support[0];
                log_message("info", __METHOD__ . "Tech Support Numbers Founded Successfully");
                $this->jsonResponseString['response'] = $response;
                $this->sendJsonResponse(array('0000', 'success'));
            }
            else{
                log_message("info", __METHOD__ . "Booking Id not found".$requestData["booking_id"]);
            $this->sendJsonResponse(array('0027', 'Support Details not found'));
            }
        }
        else{
            log_message("info", __METHOD__ . "Booking Id not found".$requestData["booking_id"]);
            $this->sendJsonResponse(array('0028', 'Booking id not found'));
        }
    }
    
    function getEngineerHeplingDocuments(){
        log_message("info", __METHOD__. " Entering..");
        $response = array();
        $pdf_docs = array();
        $video_docs = array();
        $other_docs = array();
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //$requestData = array("booking_id" => "SP-2502017111426");
        if (!empty($requestData["booking_id"])) {
            $documets =  $this->service_centers_model->get_collateral_for_service_center_bookings($requestData["booking_id"]);
            $i = 0;
            foreach ($documets as $key => $value) {
                if($value['document_type'] == "pdf"){
                    $pdf['document_type'] = $value['document_type'];
                    $pdf['document_description'] = $value['document_description'];
                    $pdf['file'] = S3_WEBSITE_URL."vendor-partner-docs/".$value['file'];
                    array_push($pdf_docs, $pdf);
                }
                else if($value['document_type'] == "video"){
                    $video['document_type'] = $value['document_type'];
                    $video['document_description'] = $value['document_description'];
                    $video['file'] = S3_WEBSITE_URL."vendor-partner-docs/".$value['file'];
                    array_push($video_docs, $video);
                }
                else{
                    $others['document_type'] = $value['document_type'];
                    $others['document_description'] = $value['document_description'];
                    $others['file'] = S3_WEBSITE_URL."vendor-partner-docs/".$value['file'];
                    array_push($other_docs, $others);
                }
                $i++;
            }
            
            $response['pdf'] = $pdf_docs;
            $response['video'] = $video_docs;
            $response['others'] = $other_docs;
            
            log_message("info", __METHOD__ . "Helping Documents Found Successfully");
            $this->jsonResponseString['response'] = $response;
            $this->sendJsonResponse(array('0000', 'success'));
        }
        else{
            log_message("info", __METHOD__ . "Booking Id not found - ".$requestData["booking_id"]);
            $this->sendJsonResponse(array('0029', 'Booking Id not found'));
        }
    }
    
    function getEngineerProfile(){
        log_message("info", __METHOD__. " Entering..");
        $response = array();
        
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //$requestData = array("engineer_id" => "1");
        if (!empty($requestData["engineer_id"])) {
            $eng_profile =  $this->engineer_model->engineer_profile_data($requestData['engineer_id']);
            if(!empty($eng_profile)){
                $response = $eng_profile[0];
                log_message("info", __METHOD__ . "Enngineer Profile Found Successfully");
                $this->jsonResponseString['response'] = $response;
                $this->sendJsonResponse(array('0000', 'success'));
            }
            else{
                log_message("info", __METHOD__ . "Engineer Profile not found - ".$requestData["engineer_id"]);
                $this->sendJsonResponse(array('0030', 'Engineer Profile not found'));
            }
        }
        else{
            log_message("info", __METHOD__ . "Engineer id not found - ".$requestData["engineer_id"]);
            $this->sendJsonResponse(array('0031', 'Engineer Id not found'));
        }
    }
    
    function getEngineerSparePartOrder(){
        log_message("info", __METHOD__. " Entering..");
        $response = array();
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //$requestData = array("partner_id" => "1", "service_id" => "1");
        if (!empty($requestData["partner_id"]) && !empty($requestData["service_id"])) {
            $where = array('entity_id' => $requestData['partner_id'], 'entity_type' => _247AROUND_PARTNER_STRING, 'service_id' => $requestData['service_id'], 'active' => 1);
            $model_detail = $this->inventory_model->get_inventory_mapped_model_numbers('appliance_model_details.id,appliance_model_details.model_number',$where);
            if(!empty($model_detail)){
                $response['sparePartsOrder']['modelNumberList'] = $model_detail;
            }
            else{
                $parts_type_details = $this->inventory_model->get_inventory_parts_type_details('inventory_parts_type.part_type', array('service_id' => $requestData['service_id']), FALSE);
                $response['sparePartsOrder']['partTypeList'] = $parts_type_details;
            }
            log_message("info", __METHOD__ . "Model Number or Part Type found successfully");
            $this->jsonResponseString['response'] = $response;
            $this->sendJsonResponse(array('0000', 'success'));
        }
        else{
            log_message("info", __METHOD__ . "Partner Id not found - ".$requestData["partner_id"]." OR Service Id not found ".$requestData["service_id"]);
            $this->sendJsonResponse(array('0032', 'Partner Id or Service Id not found'));
        }
    }
    
    function getPartTypeOnModelNumber(){
        log_message("info", __METHOD__. " Entering..");
        $response = array();
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //$requestData = array("model_number_id" => "157");
        if(!empty($requestData["model_number_id"])) {
            $response['partTypeList'] = $this->inventory_model->get_inventory_model_mapping_data('inventory_master_list.type as part_type', array('model_number_id' => $requestData["model_number_id"]));
            log_message("info", __METHOD__ . "Part Type found successfully");
            $this->jsonResponseString['response'] = $response;
            $this->sendJsonResponse(array('0000', 'success'));
        }
        else{
            log_message("info", __METHOD__ . "Model Number Id not found - ".$requestData["model_number_id"]);
            $this->sendJsonResponse(array('0033', 'Model Number Id not found'));
        }
    }
    
    function getSparePartName(){
        log_message("info", __METHOD__. " Entering..");
        $response = array();
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //$requestData = array("part_type"=> "Main Board", "partner_id"=>"247010", "service_id" => "46");
        if(!empty($requestData["part_type"]) && !empty($requestData["partner_id"]) && !empty($requestData["service_id"])) {
            $where = array();
            if (!empty($requestData["model_number_id"])) {
                $where['model_number_id'] = $requestData["model_number_id"];
            }

            if (!empty($requestData["part_type"])) {
                $where['type'] = $requestData["part_type"];
            }

            $where['inventory_master_list.service_id'] = $requestData['service_id'];
            $where['inventory_master_list.entity_id'] = $requestData['partner_id'];
            $where['inventory_master_list.entity_type'] = _247AROUND_PARTNER_STRING;;
            $select = "inventory_master_list.part_name, inventory_master_list.inventory_id, inventory_model_mapping.max_quantity, inventory_master_list.part_number, CAST((price + (price*gst_rate/100) + (price*oow_around_margin/100) + (price*oow_vendor_margin/100)) as INT) as amount";
            $response = $this->inventory_model->get_inventory_model_mapping_data($select, $where);
            log_message("info", __METHOD__ . "Spare Part Name found successfully");
            $this->jsonResponseString['response'] = $response;
            $this->sendJsonResponse(array('0000', 'success'));
            
        }
        else{
            log_message("info", __METHOD__ . "Part Type not found - ".$requestData["part_type"]." or Partner Id not found - ".$requestData["partner_id"]." or Service Id not found -".$requestData["service_id"]);
            $this->sendJsonResponse(array('0034', 'Part Type or Partner Id or Service Id not found'));
        }
    }
    
    function processSubmitSparePartsOrder(){ 
        log_message("info", __METHOD__. " Entering..");
        $requestData = json_decode($qsh->submitSparePartsOrder, true);
        $requestData["call_from_api"] = TRUE;
        $validation = $this->validateSparePartsOrderRequest($requestData);
        if($validation['status']){ 
            //Call curl for updating spare parts using code from where service center ask for spare parts
            $url = base_url()."employee/service_centers/update_spare_parts"; 
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($requestData));
            $curl_response = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($curl_response);
            if($response->status){
                log_message("info", __METHOD__ . "Part  Updated successfully");
                $this->jsonResponseString['response'] = $response->message;
                $this->sendJsonResponse(array('0000', 'success'));
            }
            else{
                log_message("info", __METHOD__ . "Part Not Updated Error - ".$response->message);
                $this->sendJsonResponse(array('0035', $response->message));
            }
        }
        else{
            log_message("info", __METHOD__ . "Request validation failed ".$validation['message']);
            $this->sendJsonResponse(array('0036', $validation['message']));
        }
    }
    
    function validateSparePartsOrderRequest($requestData){
        $response = array();
        $response['status'] = false;
        if(!isset($requestData['booking_id'])){
            $response['message'] = "Booking id not found!";
        }
        else if(!isset($requestData['amount_due'])){
            $response['message'] = "Amount Due not found";
        }
        else if(!isset($requestData['partner_id'])){
            $response['message'] = "Partner Id not found";
        }
        else if(!isset($requestData['price_tags'])){
            $response['message'] = "Request Type not found";
        }
        else if(!isset($requestData['partner_flag'])){
            $response['message'] = "Partner Flag not found";
        }
        else if(!isset($requestData['spare_shipped'])){
            $response['message'] = "Shipped Spare not found";
        }
        else if(!isset($requestData['reason'])){
            $response['message'] = "Spare Remarks not found";
        }
        else if(!isset($requestData['days'])){
            $response['message'] = "Days not found";
        }
        else if(!isset($requestData['model_number_id'])){
            $response['message'] = "Model Number Id not found";
        }
        else if(!isset($requestData['model_number'])){
            $response['message'] = "Model Number not found";
        }
        else if(!isset($requestData['dop'])){
            $response['message'] = "Date of purchase not found";
        }
        else if(!isset($requestData['serial_number'])){
            $response['message'] = "Serial Number not found";
        }
        else if(!isset($requestData['service_center_id'])){
            $response['message'] = "Service Center Id not found";
        }
        else if(!isset($requestData['part'])){
            $response['message'] = "Parts Array not found";
        }
        else if(isset($requestData['part'])){
           
            $check = true;
            $keys = array("part_warranty_status", "parts_type", "parts_name", "requested_inventory_id", "quantity");
            foreach($requestData['part'] as $parts){
                foreach ($keys as $key){
                    if (!array_key_exists($key, $parts)){ 
                        $check = false; 
                    }
                }
            }
            if($check){
                $response['status'] = true;
                $response['message'] = "success";
            }
            else{
                $response['message'] = $check;
            }
        }
        else{
            $response['status'] = true;
            $response['message'] = "success";
        }
        
        if($response['status']){
            foreach ($requestData['part'] as $key => $value){
                //upload defective front part pic
                if($value["defective_front_parts"]){
                    $defective_part_pic = "Defective_Parts_".date("YmdHis").".png";
                    $this->miscelleneous->generate_image($value["defective_front_parts"], $defective_part_pic, "misc-images");
                    $requestData['part'][$key]['defective_parts'] = $defective_part_pic;
                }
                
                //upload defective back part pick
                if($value["defective_back_parts"]){
                    $defective_back_part_pic = "Defective_Parts_".date("YmdHis").".png";
                    $this->miscelleneous->generate_image($value["defective_back_parts"], $defective_back_part_pic, "misc-images");
                    $requestData['part'][$key]['defective_back_parts_pic'] = $defective_back_part_pic;
                }
            }
        
        
            if($requestData['serial_number_pic_exist']){
                $serial_number_pic = "serial_number_pic_".date("YmdHis").".png";
                $this->miscelleneous->generate_image($requestData['serial_number_pic_exist'], $serial_number_pic, "misc-images");
                $requestData['serial_number_pic'] = $serial_number_pic;
            }

            if($requestData['invoice_number_pic_exist']){
                $invoice_pic = "invoice_".$requestData['booking_id']."_".date("YmdHis").".png";
                $this->miscelleneous->generate_image($requestData['invoice_number_pic_exist'], $invoice_pic, "misc-images");
                $requestData['invoice_pic'] = $invoice_pic;
            }
        
        }
        return $response;
    }
    
    function getSymptomCompleteBooking(){ 
        log_message("info", __METHOD__. " Entering..");
        $response = array();
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        $requestData = array("booking_id" => "PV-16565919062733", "service_id" => 28, "partner_id" => 236, "request_type" => 'Installation & Demo');
        if(!empty($requestData["booking_id"]) && !empty($requestData["service_id"]) && !empty($requestData["partner_id"]) && !empty($requestData["request_type"])){
            $response['booking_symptom'] = $this->booking_model->getBookingSymptom($requestData["booking_id"]);
            $price_tags = str_replace('(Free)', '', $requestData["request_type"]);
            $price_tags1 = str_replace('(Paid)', '', $requestData["request_type"]);
            $where = array(
                'symptom.service_id' => $requestData["service_id"], 
                'symptom.active' => 1, 
                'symptom.partner_id' => $requestData["partner_id"]
            );
            $where_in = array(
                'request_type.service_category' => $price_tags1
            );
            $response['symptoms'] = $this->booking_request_model->get_booking_request_symptom('symptom.id, symptom', $where, $where_in);
            
            $defect_where = array(
                'symptom_id' => $response['booking_symptom'][0]['symptom_id_booking_creation_time'],
                'partner_id' => $requestData["partner_id"]
            );
            $response['defect'] = $this->booking_request_model->get_defect_of_symptom('defect_id,defect', $defect_where);
            
            if(!empty($response['symptoms']) || !empty($response['defect'])){
                log_message("info", __METHOD__ . "Symptoms or Defects found successfully successfully");
                $this->jsonResponseString['response'] = $response;
                $this->sendJsonResponse(array('0000', 'success'));
            }
            else{
                log_message("info", __METHOD__ . "Symptom or Defects not found");
                $this->sendJsonResponse(array('0037', 'Symptom not found'));
            }
        }
        else{
            log_message("info", __METHOD__ . "Service Id - ".$requestData["service_id"]." or Partner Id - ".$requestData["partner_id"]." or Request Type - ".$requestData["request_type"]." not found");
            $this->sendJsonResponse(array('0037', 'Booking Id or Service Id or Partner Id or Request Type not found'));
        }
    }
    
    function getDefectCompleteBooking(){
        log_message("info", __METHOD__. " Entering..");
        $response = array();
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        $requestData = array("technical_problem" => "1");
        if(!empty($requestData["technical_problem"])){
            $response = $this->booking_request_model->get_defect_of_symptom('defect_id,defect', array('symptom_id' => $requestData['technical_problem']));
            if(count($response)<=0) {
                $response = array('defect_id' => 0, 'defect' => 'Default');
            }
            if(!empty($response)){
                log_message("info", __METHOD__ . "Defects found successfully");
                $this->jsonResponseString['response'] = $response;
                $this->sendJsonResponse(array('0000', 'success'));
            }
            else{
                log_message("info", __METHOD__ . "Defects not found");
                $this->sendJsonResponse(array('0038', 'Defects not found'));
            }
        }
        else{
            log_message("info", __METHOD__ . " ");
            $this->sendJsonResponse(array('0039', 'Technical Problem not found'));
        }
    }
    
    function getSolutionCompleteBooking(){
        log_message("info", __METHOD__. " Entering..");
        $response = array();
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        $requestData = array("technical_symptom" => "1", "technical_defect" => "1");
        if(!empty($requestData["technical_symptom"]) && !empty($requestData["technical_defect"])){
            $response = $this->booking_request_model->get_solution_of_symptom('solution_id,technical_solution', array('symptom_id' => $requestData["technical_symptom"], 'defect_id' => $requestData["technical_defect"]));
            if(count($response)<=0) {
                $response = array('solution_id' => 0, 'technical_solution' => 'Default');
            }
            if(!empty($response)){
                log_message("info", __METHOD__ . "Solution found successfully");
                $this->jsonResponseString['response'] = $response;
                $this->sendJsonResponse(array('0000', 'success'));
            }
            else{
                log_message("info", __METHOD__ . "Solution not found");
                $this->sendJsonResponse(array('0040', 'Solution not found'));
            }
        }
        else{
            log_message("info", __METHOD__ . " ");
            $this->sendJsonResponse(array('0041', 'Technical Problem or Defect not found'));
        }
    }
    
    function getBookingProductDetails(){
        
    }
}
