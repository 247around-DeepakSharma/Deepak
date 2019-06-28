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
            case 'signup':
                $this->processSignupRequest();
                break;

            case 'login':
                $this->processLoginRequest();
                break;

            case 'forgotPassword':
                $this->processForgotPassRequest();
                break;

            case 'resetPassword':
                $this->processResetPassRequest();
                break;

            case 'startSearch':
                $this->processstartSearchRequest();
                break;

            case 'searchHandyman':
                $this->processSearchRequest();
                break;

            case 'getHandyman':
                $this->processGetHandyman();
                break;

            case 'reviewHandyMan':
                $this->processReviewHandyMan();
                break;

            case 'homePage':
                $this->processHomePage();
                break;

            case 'notFoundMessage':
                $this->processNotFoundMessage();
                break;

            case 'saveHandyMan':
                $this->processAddHandyMan();
                break;

            case 'updateUserProfile':
                $this->processUpdateUserprofile();
                break;

            case 'getUserProfile':
                $this->processGetUserprofile();
                break;

            case 'savedHandymans':
                $this->processGetUsedSavedHandymans();
                break;

            case 'usedHandyman':
                $this->processSaveUsedSavedHandymans();
                break;

            case 'deleteHandyman':
                $this->processDeleteHandymans();
                break;

            case 'getAllRewies':
                $this->processGetAllReviews();
                break;

            case 'verifyNumber':
                $this->processUserVerification();
                break;

            case 'verifyCode':
                $this->processUserVerificationCode();
                break;

            case 'homePageNew':
                $this->processHomePage();
                break;

            case 'share_address':
                $this->processShareAddress();
                break;

            case 'sendHandymanCallSms':
                $this->procesSendHandymanCallSms();
                break;

            case 'submitCallQualityFeedback':
                $this->processSaveCallQualityFeedback();
                break;

            case 'pushContacts':
                $this->processSaveContacts();
                break;

            case 'getBrandsPricing':
                $this->processGetBrandsPricing();
                break;

            case 'saveBooking':
                $this->processInsertBooking();
                break;

            case 'getBookingByUser':
                $this->processGetBookingByUser();
                break;

            case 'getBookingById':
                $this->processGetBookingById();
                break;

            case 'getAppMessages':
                $this->processGetAppMessages();
                break;

            case 'getCancellationReasons':
                $this->processGetCancellationReasons();
                break;

            case 'cancelBooking':
                $this->processCancelBooking();
                break;

            case 'rescheduleBooking':
                $this->processRescheduleBooking();
                break;

            case 'getTagHints':
                $this->processGetApplianceTagHints();
                break;

            case 'sendFeedback':
                $this->processSaveFeedback();
                break;

            case 'getApplianceByUser':
                $this->processGetApplianceByUser();
                break;

            case 'getApplianceDetailsById':
                $this->processGetApplianceById();
                break;

            case 'addNewAppliance':
                $this->processAddNewAppliance();
                break;

            case 'updateAppliance':
                $this->processUpdateAppliance();
                break;

            case 'removeAppliance':
                $this->processRemoveAppliance();
                break;

            case 'getAppDataForAppliances':
                $this->processGetAppDataForAppliances();
                break;

            case 'addAppliancePics':
                $this->processAddAppliancePics();
                break;

            case 'getDiagnosticsData':
                $this->processGetDiagnosticsData();
                break;
            case 'verifyCouponCode':
                $this->processVerifyCouponCode();
                break;
            case 'updateCompleteUserProfile':
                $this->processUpdateCompleteUserProfile();
                break;
            case 'engineerLogin':
                $this->processEngineerLogin();
                break;
            case 'searchBookingID':
                $this->processSearchBookingID();
                break;
            case 'getBookingAppliance':
                $this->processGetBookingApplianceDetails();
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
            
            case 'getCustomerQrCode':
                $this->getCustomerQrCode();
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

    /**
     * @input: void
     * @description: Send SMS to User when he contacts a Handyman. Also, send an email to
     * admin about User contacting Handyman.
     * @output: print response
     */
    public function procesSendHandymanCallSms() {
        //log_message ('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        $activity = array('activity' => 'Send sms when user calls a handyman',
            'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        //Get parameters from user request
        //Old App doesn't use Vendor Number masking feature so it passes vendor mobile number in
        //$requestData["phone_number"].
        //But New App passes vendor extn in this. So search for the vendor accordingly.
        $app_new = (strlen($requestData["phone_number"]) == 4 ? TRUE : FALSE);

        $extn = $requestData["phone_number"]; //this is phone number for old app
        $handyman_id = $requestData["handyman_id"];
        $user_id = $requestData["user_id"];

        ////log_message ('info', "User: " . $extn . " >>> " . __METHOD__);
        //log_message ('info', "User ID: " . $user_id . " >>> " . __METHOD__);
        //get handyman and user details from DB
        //TODO: Fix this API, it should return complete details
        $handyman = $this->apis->getHandymanName($handyman_id);
        $user = $this->apis->getuserProfileid($user_id);

        $handyman_name = $handyman[0]['name'];
        $service_id = $handyman[0]['service'];
        $user_number = $user[0]['phone_number'];
        $user_name = $user[0]['name'];

        $developer_phone = array('8826423424', '9810872244', '8130572244', '9899296372', '8447142491');

        if ($app_new) {
            //Do this for New Version of App
            //First get phone number from handyman extension
            $result = $this->apis->gethandymanfromextn($extn);

            if (count($result) > 0) {
                //fetch handyman phone num
                $phone_number = $result[0]['phone'];

                //log_message ('info', __METHOD__ . ": Phone number found: " . $phone_number);

                if (!in_array($user_number, $developer_phone)) {
                    $message = "User $user_name ($user_number) contacted handyman $handyman_name, $service_id, $phone_number";
                    $this->sendMail("Call Event", $message, false);
                }

                //Send SMS to Vendor
                $message = "AROUND के ग्राहक $user_number ने संपर्ककिया, अधिककाम के लिए rating कराएं 011-39595200";
                $this->notify->sendTransactionalSmsMsg91($phone_number, $message,SMS_WITHOUT_TAG);

     
                $this->jsonResponseString['response'] = $user_number;
                $this->sendJsonResponse(array('0000', 'success'));
                
            } else {
                //TODO: Handle error here
                //log_message ('info', __METHOD__ . ": Handyman not found, invalid extension");
            }
        } else {
            //Do this for Old Version of App
            //log_message ('info', __METHOD__ . ": Phone number passed from App: " . $extn);

            if (!in_array($user_number, $developer_phone)) {
                $message = "User $user_name ($user_number) contacted handyman $handyman_name, $service_id, $extn";
                $this->sendMail("Call Event", $message, false);
            }

            //Send SMS to Vendor
            $message = "AROUND के ग्राहक $user_number ने संपर्ककिया, अधिककाम के लिए rating कराएं। 011-39595200";
            $this->notify->sendTransactionalSmsMsg91($extn, $message,SMS_WITHOUT_TAG);

            $this->jsonResponseString['response'] = $user_number;
            $this->sendJsonResponse(array('0000', 'success'));
            
        }
    }

    /**
     * @input: void
     * @description: Save call quality feedback
     * @output: None
     */
    function processSaveCallQualityFeedback() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);

        $activity = array('activity' => 'process save call quality feedback',
            'data' => json_encode($requestData),
            'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $handyman_id = $requestData["handyman_id"];
        $user_id = $requestData["user_id"];
        $handyman_available = $requestData["handyman_available"];
        $call_rating = $requestData["call_rating"];

        //log_message('info', $handyman_id . ", " . $user_id . ", " . $handyman_available . ", " . $call_rating);

        $callQualityArray = array($handyman_id, $user_id, $handyman_available, $call_rating);
        $id = $this->apis->addCallQualityFeedback($callQualityArray);

        //log_message('info', "ID Returned: " . $id);

        $resArray = array();
        $resArray['notify'] = true;
        $this->jsonResponseString['response'] = $resArray;
        $this->sendJsonResponse(array('0000', 'success'));
    }
    
    public function processShareAddress() {
        //log_message ('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        $activity = array('activity' => 'Share address with handyman', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $user_id = $requestData["user_id"];
        $address = $requestData["address"];
        $save_as_home = $requestData["save_as_home"];
        $extn = $requestData["extension"];
        $handyman_id = "";
        $handyman_phone = "";

        if ($save_as_home == "Yes") {
            $updateData = array('home_address' => $address);
            $this->apis->updateUserProfile($this->deviceId, $updateData);
        }

        //fetch user phone number
        $user = $this->apis->getuserProfileid($user_id);
        $user_number = $user[0]['phone_number'];

        //Get handyman from extn
        $result = $this->apis->gethandymanfromextn($extn);
        if (count($result) > 0) {
            //fetch handyman phone number
            $handyman_id = $result[0]['id'];
            $handyman_phone = $result[0]['phone'];

            //log_message ( 'info', __METHOD__ . "Phone number found: " . $handyman_phone );
        } else {
            //TODO: Handle error here
            //log_message ( 'info', __METHOD__ . "Handyman not found, error");
        }

        $message = "AROUND के ग्राहक $user_number का पता $address समय पर पहुँचो";
        //$message = "AROUND ke grahak $user_number ka pata $address samay pe pahuncho";
        //log_message ('info', "SMS message for address: " . $message);
        //Send Address to Handyman thru SMS
        $this->notify->sendTransactionalSmsMsg91($handyman_phone, $message,SMS_WITHOUT_TAG);

        $this->jsonResponseString['response'] = 'notify';
        $this->sendJsonResponse(array('000012', 'success'));
    }

    /**
     * @input: void
     * @description: 1st step in user verification. Updates / inserts a new user record and 4 digit code
     * @output: print response
     */
    public function processUserVerification() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);

        //print_r($requestData);
        $activity = array('activity' => 'get 4 digit random number', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $phone_number = $requestData["phone_number"];
        $name = $requestData["name"];
        $email = $requestData["user_email"];
        $install_source = $requestData["install_source"];
        $account_email = $requestData["account_email"];
        $app_version = $requestData["app_version"];
        $existing_flags = "";

        //log_message('info', __METHOD__ . ": Mobile: " . $phone_number . ", Name: " . $name . ", Email: " . $email);
        //log_message('info', __METHOD__ . ": Source: " . $install_source . ", Account: " . $account_email);

        if (is_null($name))
            $name = "";
        if (is_null($email))
            $email = "";
        if (is_null($install_source))
            $install_source = "";
        if (is_null($account_email))
            $account_email = "";
        if (is_null($app_version))
            $app_version = "";

        log_message('info', __METHOD__ . ": Mobile: " . $phone_number . ", Name: " . $name . ", Email: " . $email);
        //Generate a 4-digit random value using mt_rand()
        $salt = mt_rand('1000', '9999');
        //log_message('info', "Salt: " . $salt);
        //Dev ID
        if ($this->apis->checkDevIDExists($this->deviceId))
            $existing_flags .= "Yes,";
        else
            $existing_flags .= "No,";

        //Phone num
        if ($this->apis->checkPhoneNumExists($phone_number))
            $existing_flags .= "Yes,";
        else
            $existing_flags .= "No,";

        //Dev ID & Phone
        if ($this->apis->checkDevIDAndPhoneExists($this->deviceId, $phone_number))
            $existing_flags .= "Yes,";
        else
            $existing_flags .= "No,";

        //Account EMail
        if ($account_email != "") {
            if ($this->apis->checkAccEmailExists($account_email))
                $existing_flags .= "Yes";
            else
                $existing_flags .= "No";
        } else {
            $existing_flags .= "No";
        }

        log_message('info', "Existing Flags: " . $existing_flags);

        //checks phone no
        $checkUserPhoneNumber = $this->apis->checkUserPhoneNumber($phone_number, $this->deviceId);

        if ($checkUserPhoneNumber) {
            //if phone no is there, update the verification code
            //update name & email as well if user is re-verifying his no because of installation/update etc.
            log_message('info', "User phone number found, update salt");

            //$this->apis->updateVerificationCode($phone_number, $name, $email, $this->deviceId, $salt);
            $this->apis->updateVerificationCode($phone_number, $name, $email, $this->deviceId, $salt, $install_source, $account_email, $existing_flags, $app_version);
        } else {
            //if phone no is not present, insert a new record
            log_message('info', "User phone number NOT found, inserting new record");

            //$this->apis->insertUserPhoneNumber($phone_number, $name, $email, $this->deviceId, $salt);
            $this->apis->insertUserPhoneNumber($phone_number, $name, $email, $this->deviceId, $salt, $install_source, $account_email, $existing_flags, $app_version);
        }

        //gets user by phone no & dev id and fetches 4 digit code as well
        $result = $this->apis->getUserByPhoneNumber($this->deviceId, $phone_number);

        $this->jsonResponseString['response'] = $result[0];
        $this->sendJsonResponse(array('000011', 'success'));
    }

    /**
     * @input: void
     * @description: verify process User Verification Code
     * @output: print response
     */
    public function processUserVerificationCode() {
        log_message('info', "Entering: " . __METHOD__);

        $loop_count = 20;
        $requestData = json_decode($this->jsonRequestData['qsh'], true);

        $activity = array('activity' => 'verify 4 digit random number', 'data' => json_encode($requestData),
            'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $phone_number = $requestData["phone_number"];

        $checkExotelRequest = $this->apis->checkPassThruLog($phone_number);

        $activity = array('activity' => 'check exotel log', 'data' => json_encode($checkExotelRequest), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $status = "True";

        if (empty($checkExotelRequest)) {
            log_message('info', "checkExotelRequest() returned empty");
            for ($i = 0; $i < $loop_count; $i++) {
                //Sleep for 2 seconds
                sleep(2);
                //Check the missed call table to see if user gave the miss call to our call center no
                $checkExotelRequest = $this->apis->checkPassThruLog($phone_number);

                $activity = array('activity' => 'check exotel log', 'data' => json_encode($checkExotelRequest),
                    'time' => $this->microtime_float());
                $this->apis->logTable($activity);

                if (!empty($checkExotelRequest)) {
                    //if there is an entry, check if the entry is verified or not
                    $userResult = $this->apis->checkUserPhoneNumberVeri($phone_number, $this->deviceId);
                    log_message('info', "if !empty() @ i = " . $i . " -> userResult: " . $userResult);

                    //if it is a verified entry, confirm to user that number verification is complete
                    //TODO: looks like 4-digit code hasnot been used
                    $this->verifyResponse($phone_number, $userResult);
                    $status = "Flase";
                    $i = $loop_count;
                }
            }

            log_message('info', "finally i = " . $i);
            if ($status == "True") {
                $userResult = $this->apis->checkUserPhoneNumberVeri($phone_number, $this->deviceId);
                log_message('info', "Status == true, one last time, userResult: " . $userResult);

                $this->verifyResponse($phone_number, $userResult);
            }
        } else {
            

            for ($j = 0; $j < $loop_count; $j++) {
                $userResult = $this->apis->checkUserPhoneNumberVeri($phone_number, $this->deviceId);

                if ($userResult) {
                    log_message('info', "Verified phone number found @ j = " . $j);

                    break;
                } else {
                    //sleep and then continue
                    sleep(2);
                }
            }

            $this->verifyResponse($phone_number, $userResult);
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

    function processGetAllReviews() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);

        $activity = array('activity' => 'process save handyman request', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);
        $handyman_id = $requestData["handyman_id"];
        $result = $this->apis->getreviewhandyman($handyman_id);
        $this->jsonResponseString['response'] = $result;
        $this->sendJsonResponse(array('0000', 'success'));
    }

    function processDeleteHandymans() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        $activity = array('activity' => 'process save handyman request', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);
        $saved_type = $requestData["saved_type"];
        $handyman_id = $requestData["handyman_id_array"];
        $handyman_id_array = json_decode($handyman_id, true);
        $checkStatus = $this->apis->deleteHandymans($saved_type, $this->deviceId, $handyman_id_array);
        if ($checkStatus) {
            $this->jsonResponseString['response'] = $checkStatus;
            $this->sendJsonResponse(array('0000', 'deleted'));
        }
    }

    /**
     * @input: void
     * @description: process User's saved and used marked handymans
     * @output: print response
     */
    function processSaveUsedSavedHandymans() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        $activity = array('activity' => 'process save handyman request',
            'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $saved_type = $requestData["saved_type"];
        $handyman_id = $requestData["handyman_id"];
        $report_msg = $requestData["report_msg"];

        if (is_null($report_msg))
            $report_msg = "";

        $checkStatus = $this->apis->checkUsedSavedHandyman($saved_type, $this->deviceId, $handyman_id, $report_msg);

        if ($checkStatus) {
            $saved_type = "already " . $saved_type;
            $resArray = array('saved_type' => $saved_type, 'id' => $checkStatus);
        } else {
            //log_message('info', "status not found");

            $saveHandyMan = $this->apis->saveUsedSavedHandyman($saved_type, $this->deviceId, $handyman_id, $report_msg);
            $resArray = array('saved_type' => $saved_type, 'id' => $saveHandyMan);
        }

        if ($saved_type == "report") {
            //log_message('info', "Saved type == report");
            //fetch user details frist from device_id
            $user = $this->apis->getUserByDeviceID($this->deviceId);
            $handyman = $this->apis->getHandymanProfile($handyman_id);

            $user_name = $user[0]['name'];
            $user_phone = $user[0]['phone_number'];
            $handyman_name = $handyman[0]['name'];
            $handyman_phone = $handyman[0]['phone'];

            $subject = "User $user_name ($user_phone) reported Handyman $handyman_name ($handyman_phone)";

            $this->sendMail($subject, "User feedback: " . $report_msg, false);
        }

        $this->jsonResponseString['response'] = $resArray;
        $this->sendJsonResponse(array('00000', 'savedHandymans'));
    }

    /**
     * @input: void
     * @description: get all the used/ saved handyman of a user by phone number
     * @output: print response
     */
    function processGetUsedSavedHandymans() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        $activity = array('activity' => 'process save handyman request', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);
        $saved_type = $requestData["saved_type"];
        $user_id = $requestData["user_id"];
        $area = "Current Location";
        $userLocation = $requestData["userLocation"];

        log_message('info', "User ID: " . $user_id . ", Area: " . $area . ", Saved Type: " .
            $saved_type . ", User Loc: " . $userLocation);
        $lat1 = "28.6100";
        $long1 = "77.2300";
        $getdistance = "0 km";
        $area = $this->getArea($area, $userLocation);

        $unit = 'k';
        $searchApi = $this->apis->GetUsedSavedHandymans($saved_type, $this->deviceId);
        log_message('info', "GetUsedSavedHandymans: " . count($searchApi) . " handyman found");

        if ($area) {
            $lat1 = $area['latitude'];
            $long1 = $area['longitude'];
            $this->apis->checkLocation($this->deviceId, $lat1, $long1);
        } else {
            $loc = $this->apis->getLocation($this->deviceId);
            if ($loc) {
                $lat1 = $loc['latitude'];
                $long1 = $loc['longitude'];
            }
        }
        if ($searchApi) {
            $array = array();
            $i = 0;
            foreach ($searchApi as $search) {
                $address = $search['address'];
                $handyman_id = $search['handyman_id'];
                $review = $this->apis->checkReview($handyman_id, $user_id);
                if ($review) {
                    $searchApi[$i]['review_by_user'] = "true";
                } else {
                    $searchApi[$i]['review_by_user'] = "false";
                }
                if (array_key_exists('location', $search)) {
                    $location = json_decode($search['location'], true);

                    $long2 = $location['longitude'];
                    $lat2 = $location['lattitude'];
                    $distance = $this->distance($lat1, $long1, $lat2, $long2, $unit);
                    $getdistance = round($distance, 2);
                }
                $total_review = $this->apis->gethandymanreview($handyman_id);

                $searchApi[$i]['total_review'] = count($total_review);
                $searchApi[$i]['profile_photo'] = $search['profile_photo'];
                $rating = $search['Rating_by_Agent'];
                if ($rating == "Good") {
                    $rating = 4.0;
                } else if ($rating == "Average") {
                    $rating = 3.0;
                } else if ($rating == "Exceptional") {
                    $rating = 5.0;
                } else if ($rating == "Bad") {
                    $rating = 2.0;
                } else if ($rating == "Very Bad") {
                    $rating = 1.0;
                }

                $searchApi[$i]['rating'] = $rating;
                $searchApi[$i]['id'] = $handyman_id;
                $searchApi[$i]['distance'] = $getdistance;
                $i = $i + 1;
            }
            log_message('info', $i . " search results found in searchApi, returning array");
            $resArray = $searchApi;
        } else {
            log_message('info', "No result found in searchApi, returning empty array");
            $resArray = array();
        }
        $this->jsonResponseString['response'] = $resArray;
        $this->sendJsonResponse(array('0000', 'success'));
    }

    function processGetUserprofile() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        $activity = array('activity' => 'process save handyman request',
            'data' => json_encode($requestData),
            'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $phone_number = $requestData["phone_number"];
        log_message('info', "Phone num: " . $phone_number);

        $resp = $this->apis->getuserProfileByDeviceID($this->deviceId, $phone_number);

        $this->jsonResponseString['response'] = $resp[0];
        $this->sendJsonResponse(array('00000', 'success'));
    }

    function processUpdateUserprofile() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        $activity = array('activity' => 'process save handyman request', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $username = $requestData["username"];
        $updatekey = $requestData["updatekey"];
        $updatevalue = $requestData["updatevalue"];
        $updateData = array($updatekey => $updatevalue);
        if ($updatekey == "user_image") {
            $image = $updatevalue;

            $binary = base64_decode($image);
            $pic = md5(uniqid(rand()));
            $image_name = $pic . ".png";
            $image_path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $image_name;
            $file = fopen($image_path, 'wb');
            fwrite($file, $binary);
            fclose($file);
            $input = S3::inputFile($image_path);
            if (S3::putObject($input, "boloaaka-images", "users-320x252/" . $image_name, S3::ACL_PUBLIC_READ)) {
                //echo "File uploaded.";
            } else {
                //echo "Failed to upload file.";
            }
            $updateData = array($updatekey => $image_name);
        }
        $this->apis->updateUserProfile($this->deviceId, $updateData);
        $resp = array("updatekey" => $updatekey, "updatevalue" => $updatevalue);
        $this->jsonResponseString['response'] = $resp;
        $this->sendJsonResponse(array('0000', 'success'));
    }

    /**
     * @input: void
     * @description: add handyman that anyone requested from mobile application
     * @output: print response
     */
    function processAddHandyMan() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        $activity = array('activity' => 'process save handyman request', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $username = $requestData["username"];
        $image = $requestData["image"];
        $names = $requestData["names"];
        $phone = $requestData["phone"];
        $services = $requestData["services"];
        $address = $requestData["address"];
        $experience = $requestData["experience"];
        $age = $requestData["age"];
        // $filename = $requestData["fileName"]; // no use of this variable in whole api -- commented by vishal
        $service_id = $this->apis->getservice_id($services);
        $image_name = "";

        if (!empty($image)) {
            $binary = base64_decode($image);
            $pic = md5(uniqid(rand()));
            $image_name = $pic . ".png";
            $image_path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $image_name;
            $file = fopen($image_path, 'wb');
            fwrite($file, $binary);
            fclose($file);
        }
        //$input = S3::inputFile($image_path);
        //if (S3::putObject($input, "boloaaka-images", "vendor-320x252/".$image_name,S3::ACL_PUBLIC_READ)) {
        //echo "File uploaded.";
        //} else {
        //echo "Failed to upload file.";
        //}
        $handymanArray = array($names, $phone, $service_id, $address, $experience, $age, $image_name, 0);
        //print_r($handymanArray);
        $this->apis->addHandyman($handymanArray, $this->deviceId);

        if ($username) {
            $username = $username;
            $user_profile = $this->apis->getuserProfileByDeviceID($this->deviceId);
            if ($user_profile) {
                $user_email = $user_profile[0]['user_email'];
                $user_name = $user_profile[0]['name'];
                $username .= ", $user_email, $user_name";
            }
        } else {
            $username = "a user";
        }

        $messages = "<html><head></head><body><p>Hi Admin,</p><p>New handyman added by $username. </p>";
        $messages .= "<p>Name : $names</p>";
        $messages .= "<p>Phone : $phone</p>";
        $messages .= "<p>Service : $services</p>";
        $messages .= "<p>Address : $address</p>";
        $messages .= "<p>Experience : $experience</p>";
        //$messages .= "<p>Age : $age</p>";
        //$messages .= "<p>Image : ".base_url()."uploads/$image_name</p>\n";
        $messages .= " <p>Sincerely,</p><p>The Around Team</p></body></html>";
        $subject = "New Handyman add request";

        $smResult = $this->sendMail($subject, $messages, false);

        if ($smResult == "Success") {
            $resArray = array();
            $resArray['notify'] = true;
            $this->jsonResponseString['response'] = $resArray;
            $this->sendJsonResponse(array('0000', 'success'));
        }
    }

    /**
     * @input: void
     * @description: process handyman not found message and email this to nitin
     * @output: print response
     */
    function processNotFoundMessage() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);

        //print_r($requestData);
        $activity = array(
            'activity' => 'processNotFoundMessage request',
            'data' => json_encode($requestData),
            'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        //echo $this->deviceId;
        $username = $requestData['username'];
        $user_profile = $this->apis->getuserProfileByDeviceID($this->deviceId);
        //$service = $requestData['service'];

        if ($user_profile) {
            $user_email = $user_profile[0]['user_email'];
            $user_name = $user_profile[0]['name'];
            $username .= ", $user_email, $user_name";
        }

        $message = $requestData['message'];
        $category = $requestData['category'];

        $messages = "<html><head></head><body><p>Hi Admin,</p><p>User ($username) did not find any handyman in $category category. </p>";
        $messages .= "<p>Message : $message</p>";
        $messages .= " <p>Sincerely,</p><p>The Around Team</p></body></html>";
        $subject = "Handyman not find mail";

        log_message('info', "Handyman Not Found -> " . "User: " . $username . ", Category: " . $category);

        $smResult = $this->sendMail($subject, $messages, false);

        if ($smResult == "Success") {
            $resArray = array();
            $resArray['notify'] = true;
            $this->jsonResponseString['response'] = $resArray;
            $this->sendJsonResponse(array('0000', 'success'));
        }
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

    /**
     * @input: void
     * @description: get all services for search/ landing page
     * @output: print response
     */
    function processHomePage() {
        //log_message ('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        $activity = array('activity' => 'home page request', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $this->jsonResponseString['response'] = $this->apis->GetAllServices();
        $this->jsonResponseString['popular_search'] = $this->apis->getPopularKeywords();
        $this->sendJsonResponse(array('0000', 'success'));
    }

    /**
     * @input: void
     * @description: save handyman review by users from app
     * @output: print response
     */
    function processReviewHandyMan() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        $activity = array('activity' => 'review handyman request', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);
        $handyman_id = $requestData['handyman_id'];

        $user_id = $requestData['username'];
        $expertise_rate = $requestData['expertise_rate'];
        $behavior_rate = $requestData['behavior_rate'];
        $review = $requestData['review'];

        $review = array('behaviour' => $behavior_rate, 'expertise' => $expertise_rate, 'review' => $review, 'handyman_id' => $handyman_id, 'user_id' => $user_id);
        $updatedetail = $this->apis->handymanReview($review);
        $this->jsonResponseString['response'] = "done";
        $this->sendJsonResponse(array('0000', 'success'));
    }

    /**
     * @input: void
     * @description: process get a handyman request by handyman id (handyman profile page)
     * @output: print response
     */
    function processGetHandyman() {
        //log_message ('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        $activity = array('activity' => 'start handyman_id request', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);
        $handyman_id = $requestData['handyman_id'];
        $user_id = $requestData['username'];

        $getHandyman = $this->getHandymanProfile($handyman_id, $user_id);

        $this->jsonResponseString['response'] = $getHandyman;
        $this->sendJsonResponse(array('0000', 'success'));
    }

    /**
     * @input: void
     * @description: process User's saved and used marked handymans
     * @output: print response
     */
    function processSaveContacts() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
    $user_id = $requestData["user_id"];
        log_message('info', "User ID:" . $user_id);

        //check whether contacts file is there
        if (isset($_FILES['csHMtmp']['name'])) {
            log_message('info', "file received: " . $_FILES['csHMtmp']['name'] . ", " .
                $_FILES['csHMtmp']['tmp_name']);

        $new_file = mt_rand('10000', '99990') . "_contacts_user_id_" . $user_id;
        $bucket = 'user-contacts';
        $directory_wc = "android-app/" . $new_file;
        $this->s3->putObjectFile($_FILES['csHMtmp']['tmp_name'], $bucket, $directory_wc, S3::ACL_PRIVATE);
    } else {
            log_message('info', "file NOT received");
        }

    /*
      $new_file = "./uploads/" . mt_rand('1000', '9999') . "_contacts_user_id_" . $user_id;
      if (move_uploaded_file($_FILES['csHMtmp']['tmp_name'], $new_file)) {
      log_message('info', "file copied successfully");
      }
     *
     */

    //save record in database
        $contact_details = array($user_id, $new_file);
        $this->apis->addUserContactsFileInfo($contact_details);

        $this->jsonResponseString['response'] = "done";
        $this->sendJsonResponse(array('0000', 'success'));
    }

    /**
     * @input: service category name
     * @description: process get brand names and pricing request
     * @output:
     */
    function processGetBrandsPricing() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        $activity = array('activity' => 'process get brands pricing', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $searched_service = $requestData['searched_service'];
        //$user_id = $requestData['username'];

        $getBrandNames = $this->apis->getBrandsForService($searched_service);

        $getPricingDetails = $this->apis->getPricingForService($searched_service);
        //log_message('info', print_r($getPricingDetails, TRUE));

        $this->jsonResponseString['response'] = array('brands' => $getBrandNames,
            'pricing' => $getPricingDetails);
        $this->sendJsonResponse(array('0000', 'success'));
    }

    /**
     * @input:
     * @description: Save new booking submitted by User
     * @output:
     */
    function processInsertBooking() {
        log_message('info', "Entering: " . __METHOD__);
        $requestData = json_decode($this->jsonRequestData['qsh'], true);

        //log_message('info'," Process Insert Booking ".$requestData);


        $activity = array('activity' => 'process save booking',
            'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);
        $searched_service = $requestData["searched_service"];
        $searched_service_id = $this->apis->getservice_id($searched_service);
            $vendors = $this->vendor_model->check_vendor_availability($requestData["booking_pincode"], $searched_service_id);
            $vendors_count = count($vendors);

            if ($vendors_count > 0) {

                $user_id = $requestData["user_id"];
                $booking['user_id'] = $user_id;

                $booking_date = trim($requestData["booking_date"]);

                $yy = date("y", strtotime($booking_date));
                $yyyy = date("Y", strtotime($booking_date));
                $mm = date("m", strtotime($booking_date));
                $dd = date("d", strtotime($booking_date));

                //Format = DD-MM-YYYY for database
                $booking_date_formatted = date("d-m-Y", strtotime($booking_date));
                $booking['booking_date'] = $booking_date_formatted;
                $booking['initial_booking_date'] = $booking_date_formatted;

                $booking_time = $requestData["booking_time"];
                $booking['booking_timeslot'] = $booking_time;
                $booking['booking_address'] = $requestData["booking_address"];
                $booking['booking_pincode'] = $requestData["booking_pincode"];
                $booking['booking_remarks'] = ($requestData["booking_comments"] == "" ? "" :
                                trim($requestData["booking_comments"]));
                $amount_due_intial = $requestData["amount_due"];

                $booking['discount_coupon'] = $requestData["discount_coupon"];
                $booking['discount_amount'] = $requestData["discount_amount"];

                if (is_null($booking['discount_coupon'])) {
                    $booking['discount_coupon'] = '';
                }
                if (is_null($booking['discount_amount'])) {
                    $booking['discount_amount'] = '0';
                }

                $booking['amount_due'] = intval($amount_due_intial) - intval($booking['discount_amount']);
                $distict_details = $this->vendor_model->get_distict_details_from_india_pincode(trim($booking['booking_pincode']));
                $booking['state'] = $distict_details['state'];
                $booking['district'] = $distict_details['district'];
                $booking['taluk'] = $distict_details['taluk'];

                $unit_details = $requestData["unit_details"];
//            log_message('info', "Unit details: " . print_r($unit_details, TRUE));

                $units = json_decode($unit_details, true);
                $booking['quantity'] = count($units);

                //Booking ID Format: USER_ID (4 digits) + YYMMDD + Bookings_done_by_user_till_now
                //Date format: 1 Jan, 2015
                $booking_id = str_pad($user_id, 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;

                $booking_id .= (intval($this->apis->getBookingCountByUser($user_id)) + 1);
                $booking_id = "SB-" . $booking_id;
                $booking['booking_id'] = $booking_id;
                $booking['partner_id'] = "247001";
                $booking['partner_source'] = "AndroidApp";
//            log_message('info', "Booking ID (generated): " . $booking_id);

                $appliance_id = $requestData["appliance_id"];

                //Save individual unit details first for this booking
                //Count of units saved is returned

                $user_profile = $this->apis->getuserProfileid($user_id);
                $user_name = $user_profile[0]['name'];
                $user_email = $user_profile[0]['user_email'];
                $user_phone = $user_profile[0]['phone_number'];

                $booking['booking_primary_contact_no'] = $user_phone;
                $booking['booking_alternate_contact_no'] = "";
                $booking['service_id'] = $searched_service_id;

//            log_message('info', $user_name . $user_email . $user_phone);
                //check whether booking image file is there
                if (isset($_FILES['bookingPic']['name'])) {
                    //log_message('info', "Booking Image file received: " . $_FILES['bookingPic']['name'] . ", " .
                    //    $_FILES['bookingPic']['tmp_name']);
                    //log_message('info', filesize($_FILES['contacts']['tmp_name']));
                    //log_message('info', file_get_contents($_FILES['contacts']['tmp_name']));
                    //log_message('info', $_FILES['bookingPic']['size']);

                    $booking['booking_picture_file'] = "./uploads/" . mt_rand('1000', '9999') .
                            "_booking_image_user_id_" . $user_id . ".jpg";
                    if (move_uploaded_file($_FILES['bookingPic']['tmp_name'], $booking['booking_picture_file'])) {
                        //log_message('info', "Image file copied successfully");
                    }
                } else {
                    $booking['booking_picture_file'] = "";
                    //log_message('info', "No Image file with the booking");
                    //log_message('info', "file count=" . $_FILES['csHMtmp']['name']);
                }

                $booking['type'] = "Booking";
                $booking['source'] = "SB";
                $booking['current_status'] = _247AROUND_PENDING;
                $booking['internal_status'] = _247AROUND__SCHEDULED;

//            log_message('info', "User ID:" . $user_id . ", service: " . $searched_service
//                    . ", date: " . $booking['booking_date'] . ", Address: " . $booking['booking_address']
//                    . ", Pincode: " . $booking['booking_pincode'] . ", time: " . $booking['booking_timeslot']);

                $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'], $booking['partner_id'], $booking_id);
                if (!empty($partner_status)) {
                    $booking['partner_current_status'] = $partner_status[0];
                    $booking['partner_internal_status'] = $partner_status[1];
                    $booking['actor'] = $partner_status[2];
                    $booking['next_action'] = $partner_status[3];
                }

                //Save Booking
                $status = $this->booking_model->addbooking($booking);
//            log_message('info', "Booking ID Returned (with appl, from wallet): " . $status);


                $add_appliance = $requestData["add_appliance"];

                $inserted_appliance_array = array();

                //Check for Appliance ID. If it is there, update appliance details as well
                if ($appliance_id != "0") {


                    //Update brand, category & capacity if required
                    $this->apis->updateApplianceCategoryCapacity($appliance_id, $units[0]['brand'], $units[0]['category'], $units[0]['capacity']);
                    $units_saved = $this->saveUnitDetails($unit_details, $booking_id, $booking['discount_amount'], $searched_service_id, $appliance_id, $booking['state']);
                    $count = count($units);
                    //Insert more appliances if required
                    if ($add_appliance == 'true') {
                        for ($c = 1; $c < $count; $c++) {
                            $appl = array($user_id, $searched_service_id,
                                $units[$c]['brand'], $units[$c]['category'], $units[$c]['capacity'], $units[$c]['applianceTag']);
//                        log_message('info', "Appliance details from simple booking (wallet): " . print_r($appl, true));

                            $r = $this->apis->addApplianceFromBooking($appl);
                            $inserted_appliance = $r[0];
//                        log_message('info', "Inserted Appliance ID (wallet):" . $inserted_appliance['id']);

                            array_push($inserted_appliance_array, $inserted_appliance);
                        }
                    }
                } else {
                    //NO appliance ID, check whether this appliance needs to be
                    //added into User Wallet
                    if ($add_appliance == "true") {


                        $appl = array($user_id, $searched_service_id,
                            $units[0]['brand'], $units[0]['category'], $units[0]['capacity'], $units[0]['applianceTag']);
//                    log_message('info', "Appliance details from simple booking: " . print_r($appl, true));

                        $r = $this->apis->addApplianceFromBooking($appl);
                        $inserted_appliance = $r[0];
//                    log_message('info', "Inserted Appliance:" . print_r($inserted_appliance, TRUE));

                        array_push($inserted_appliance_array, $inserted_appliance);

                        //Save booking
                        $appliance_id = $inserted_appliance['id'];
                        $units_saved = $this->saveUnitDetails($unit_details, $booking_id, $booking['discount_amount'], $searched_service_id, $appliance_id, $booking['state']);

                        //Now add remaining appliances if there are more than one
                        for ($c = 1; $c < count($units); $c++) {
                            $appl = array($user_id, $searched_service_id,
                                $units[$c]['brand'], $units[$c]['category'], $units[$c]['capacity'], $units[$c]['applianceTag']);
//                        log_message('info', "Appliance details from simple booking: " . print_r($appl, true));

                            $r = $this->apis->addApplianceFromBooking($appl);
                            $inserted_appliance = $r[0];
//                        log_message('info', "Inserted Appliance ID (extra units):" . $inserted_appliance['id']);

                            array_push($inserted_appliance_array, $inserted_appliance);
                        }
                    } else {
                        
                    }
                }

                //Send booking mails to Admin, Vendor and User
                $this->sendBookingMails($user_profile[0], $booking, $searched_service);

                $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
                $send['booking_id'] = $booking['booking_id'];
                $send['state'] = "Newbooking";
                $this->asynchronous_lib->do_background_process($url, $send);

                $this->jsonResponseString['response'] = array('booking_id' => $booking_id,
                    'inserted_appliances' => $inserted_appliance_array);
                $this->sendJsonResponse(array('0000', 'success'));
            } else {

                $this->miscelleneous->sf_not_exist_for_pincode(array('booking_id' => "Not_Generated", 'booking_pincode' => $requestData["booking_pincode"], 
                     'service_id' => $searched_service_id,'partner_id'=>'247001','city'=>'Not_Received','order_id'=>'Not_Received'));
                $this->sendJsonResponse(array('0008', 'failure'));
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
     * @input: User id
     * @description: Get orders submitted by User
     * @output:
     */
    function processGetBookingByUser() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        $activity = array('activity' => 'process get booking by user', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $user_id = $requestData['username'];
        log_message('info', "User ID: " . $user_id);

        $user_bookings = $this->apis->getBookingByUser($user_id);
        log_message('info', count($user_bookings) . " bookings found");

        if (count($user_bookings) > 0) {
            foreach ($user_bookings as $user_booking) {
                //log_message('info', print_r($user_booking, TRUE));
            }
        } else {
            $user_bookings = array();
        }

        $this->jsonResponseString['response'] = $user_bookings;
        $this->sendJsonResponse(array('0000', 'success'));
    }

    /**
     * @input: Booking details ID
     * @description: Get booking details by ID
     * @output:
     */
    function processGetBookingById() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        $activity = array('activity' => 'process get booking by id', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $booking_id = $requestData['booking_id'];
        //log_message('info', "Booking ID: " . $booking_id);

        $booking_details = $this->apis->getBookingById($booking_id);
        //log_message('info', print_r($booking_details[0], TRUE));

        $unit_details = $this->apis->getUnitDetailsByBookingId($booking_id);
        //log_message('info', print_r($unit_details, TRUE));

        $this->jsonResponseString['response'] = array('booking_details' => $booking_details[0],
            'unit_details' => $unit_details);
        $this->sendJsonResponse(array('0000', 'success'));
    }

    /**
     * @input: None
     * @description: Get various messages (about us, legal etc) as per the tag
     * @output:
     */
    function processGetAppMessages() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        $activity = array('activity' => 'process get about us', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $tag = $requestData['tag'];
        log_message('info', "Tag: " . $tag);

        $messages = $this->apis->getAroundMessgaes($tag);
        //log_message('info', print_r($messages[0]['message'], TRUE));
//        header('Content-Encoding: gzip');
//        $this->jsonResponseString['response'] = gzencode($messages[0]['message'], 9);

        $this->jsonResponseString['response'] = $messages[0]['message'];
        $this->sendJsonResponse(array('0000', 'success'));
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

    /**
     * @input: Service name
     * @description: Get appliance tag hints
     * @output:
     */
    function processGetApplianceTagHints() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        $activity = array('activity' => 'process get tag hints', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $service = $requestData['searched_service'];
        //log_message('info', "Service: " . $service);

        $hints = $this->apis->getApplianceTagHints($service);
        //log_message('info', print_r($hints, TRUE));

        $this->jsonResponseString['response'] = $hints;
        $this->sendJsonResponse(array('0000', 'success'));
    }

    /**
     * @input: Booking ID to be rescheduled, new date and time
     * @description: Cancel pre-existing booking
     * @output:
     */
    function processSaveFeedback() {
        //log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        $activity = array('activity' => 'process save feedback', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $user_id = $requestData['user_id'];
        $feedback = $requestData['feedback'];

        $user_profile = $this->apis->getuserProfileid($user_id);
        $user_name = $user_profile[0]['name'];
        $user_email = $user_profile[0]['user_email'];
        $user_phone = $user_profile[0]['phone_number'];

        //log_message('info', "User ID: " . $user_id . ", Feedback: " . $feedback);

        $result = $this->apis->saveFeedback($user_id, $feedback);
        //log_message('info', print_r($result, TRUE));
        //Send mail to Admin
        $subject = "Feedback Received for Android App";
        $message = "Dear Admin<br/><br/>";
        $message .= "User $user_name ($user_phone) submitted below feedback for 247Around Android App:<br/><br/>";
        $message .= $feedback;
        $message .= "<br/><br/>Thanks";

        $this->sendMail($subject, $message, TRUE);

        $this->jsonResponseString['response'] = "done";
        $this->sendJsonResponse(array('0000', 'success'));
    }

    /**
     * @input: User id
     * @description: Get Appliances for a User
     * @output:
     */
    function processGetApplianceByUser() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        //$activity = array('activity' => 'process get booking by user', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        //$this->apis->logTable($activity);

        $user_id = $requestData['user_id'];
        log_message('info', "User ID: " . $user_id);

        $user_appliances = $this->apis->getApplianceByUser($user_id);
        log_message('info', count($user_appliances) . " appliances found");

        if (count($user_appliances) > 0) {
            //foreach ($user_appliances as $appliance) {
                //log_message('info', print_r($appliance, TRUE));
           // }
        } else {
            $user_appliances = array();
        }

        $this->jsonResponseString['response'] = $user_appliances;
        $this->sendJsonResponse(array('0000', 'success'));
    }

    /**
     * @input:
     * @description: Save new appliance in Wallet submitted by User
     * @output:
     */
    function processAddNewAppliance() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        
        //$activity = array('activity' => 'process save booking',
        //    'data' => json_encode($requestData), 'time' => $this->microtime_float());
        //$this->apis->logTable($activity);

        $user_id = $requestData["user_id"];
        $service_id = $requestData["service_id"];
        $brand = $requestData["brand"];
        $category = $requestData["category"];
        $capacity = $requestData["capacity"];

        $model_number = $requestData["model_number"];
        $tag = $requestData["tag"];
        $purchase_date =  date("Y-m-d");
        $rating = $requestData["rating"];

        //check whether warranty card image file is there
        if (isset($_FILES['warrantyCardPic']['name'])) {
            //log_message('info', "Warranty Card Image file received: " . $_FILES['warrantyCardPic']['name'] . ", " .
            //    $_FILES['warrantyCardPic']['tmp_name']);
            //log_message('info', "Size: " . $_FILES['warrantyCardPic']['size']);

            $path = $_FILES['warrantyCardPic']['name'];
            $ext = pathinfo($path, PATHINFO_EXTENSION);

            $wc_file = md5(uniqid(rand())) . "." . $ext;
            $bucket = 'appliance-pics';
            $directory_wc = "warranty-cards/" . $wc_file;

            //log_message('info', "Ext: " . $ext);

            $this->s3->putObjectFile($_FILES['warrantyCardPic']['tmp_name'], $bucket, $directory_wc, S3::ACL_PUBLIC_READ);
        } else {
            $wc_file = "";
            //log_message('info', "No warranty card file with the booking");
            //log_message('info', "file count=" . $_FILES['csHMtmp']['name']);
        }

        //check whether invoice card image file is there
        if (isset($_FILES['invoiceCardPic']['name'])) {
            //log_message('info', "Invoice Card Image file received: " . $_FILES['invoiceCardPic']['name'] . ", " .
            //    $_FILES['invoiceCardPic']['tmp_name']);
            //log_message('info', "Size: " . $_FILES['invoiceCardPic']['size']);

            $path = $_FILES['invoiceCardPic']['name'];
            $ext = pathinfo($path, PATHINFO_EXTENSION);

            $invoice_file = md5(uniqid(rand())) . "." . $ext;
            $bucket = 'appliance-pics';
            $directory_invoice = "invoices/" . $invoice_file;

            //log_message('info', "Ext: " . $ext);

            $this->s3->putObjectFile($_FILES['invoiceCardPic']['tmp_name'], $bucket, $directory_invoice, S3::ACL_PUBLIC_READ);
        } else {
            $invoice_file = "";
            //log_message('info', "No warranty card file with the booking");
            //log_message('info', "file count=" . $_FILES['csHMtmp']['name']);
        }

        //Save appliance in database
        $appliance_details = array($user_id, $service_id, $brand, $category, $capacity,
            $model_number, $tag, $purchase_date, $rating,
            $wc_file, $invoice_file);
        $saved_appliance = $this->apis->addNewAppliance($appliance_details);

        //log_message('info', "Appliance ID Returned: " . $id_returned);

        $this->jsonResponseString['response'] = $saved_appliance;
        $this->sendJsonResponse(array('0000', 'success'));
    }

    /**
     * @input:
     * @description: Save new appliance pics submitted by User
     * @output:
     */
    function processAddAppliancePics() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        //$activity = array('activity' => 'process save booking',
        //    'data' => json_encode($requestData), 'time' => $this->microtime_float());
        //$this->apis->logTable($activity);
        //$user_id = $requestData["user_id"];
        $appliance_id = $requestData["appliance_id"];
        $picture_tag = $requestData["picture_tag"];
        $picture_file = "";

        if ($picture_tag == 'warrantyCardPic') {
            //check whether warranty card image file is there
            if (isset($_FILES['warrantyCardPic']['name'])) {
                //log_message('info', "Warranty Card Image file received: " . $_FILES['warrantyCardPic']['name'] . ", " .
                //    $_FILES['warrantyCardPic']['tmp_name']);
                //log_message('info', "Size: " . $_FILES['warrantyCardPic']['size']);

                $path = $_FILES['warrantyCardPic']['name'];
                $ext = pathinfo($path, PATHINFO_EXTENSION);

                $picture_file = md5(uniqid(rand())) . "." . $ext;
                $bucket = 'appliance-pics';
                $directory_wc = "warranty-cards/" . $picture_file;

                //log_message('info', "Ext: " . $ext);

                $this->s3->putObjectFile($_FILES['warrantyCardPic']['tmp_name'], $bucket, $directory_wc, S3::ACL_PUBLIC_READ);

                //Update appliance in database
                $this->apis->addAppliancePics($appliance_id, 'warrantyCardPic', $picture_file);
            } else {
                //log_message('info', "No warranty card file with the booking");
                //log_message('info', "file count=" . $_FILES['csHMtmp']['name']);
            }
        }

        if ($picture_tag == 'invoiceCardPic') {
            //check whether invoice card image file is there
            if (isset($_FILES['invoiceCardPic']['name'])) {
                //log_message('info', "Invoice Card Image file received: " . $_FILES['invoiceCardPic']['name'] . ", " .
                //    $_FILES['invoiceCardPic']['tmp_name']);
                //log_message('info', "Size: " . $_FILES['invoiceCardPic']['size']);

                $path = $_FILES['invoiceCardPic']['name'];
                $ext = pathinfo($path, PATHINFO_EXTENSION);

                $picture_file = md5(uniqid(rand())) . "." . $ext;
                $bucket = 'appliance-pics';
                $directory_invoice = "invoices/" . $picture_file;

                //log_message('info', "Ext: " . $ext);

                $this->s3->putObjectFile($_FILES['invoiceCardPic']['tmp_name'], $bucket, $directory_invoice, S3::ACL_PUBLIC_READ);

                //Update appliance in database
                $this->apis->addAppliancePics($appliance_id, 'invoiceCardPic', $picture_file);
            } else {
                //log_message('info', "No warranty card file with the booking");
                //log_message('info', "file count=" . $_FILES['csHMtmp']['name']);
            }
        }


        //log_message('info', "Appliance ID Returned: " . $id_returned);

        $this->jsonResponseString['response'] = $picture_file;
        $this->sendJsonResponse(array('0000', 'success'));
    }

    /**
     * @input:
     * @description: Update appliance in wallet
     * @output:
     */
    function processUpdateAppliance() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        //$activity = array('activity' => 'process save booking',
        //    'data' => json_encode($requestData), 'time' => $this->microtime_float());
        //$this->apis->logTable($activity);

        $appliance_id = $requestData["appliance_id"];
        $brand = $requestData["brand"];
        $category = $requestData["category"];
        $capacity = $requestData["capacity"];

        $model_number = $requestData["model_number"];
        $tag = $requestData["tag"];
        $purchase_date = date("Y-m-d");
        $rating = $requestData["rating"];

//        //check whether warranty card image file is there
//        if (isset($_FILES['warrantyCardPic']['name'])) {
//            //log_message('info', "Warranty Card Image file received: " . $_FILES['warrantyCardPic']['name'] . ", " .
//            //    $_FILES['warrantyCardPic']['tmp_name']);
//            //log_message('info', "Size: " . $_FILES['warrantyCardPic']['size']);
//
//            $path = $_FILES['warrantyCardPic']['name'];
//            $ext = pathinfo($path, PATHINFO_EXTENSION);
//
//            $wc_file = md5(uniqid(rand())) . "." . $ext;
//            $bucket = 'appliance-pics';
//            $directory_wc = "warranty-cards/" . $wc_file;
//
//            //log_message('info', "Ext: " . $ext);
//
//            $this->s3->putObjectFile($_FILES['warrantyCardPic']['tmp_name'], $bucket, $directory_wc, S3::ACL_PUBLIC_READ);
//        } else {
//            $wc_file = "";
//            //log_message('info', "No warranty card file with the booking");
//            //log_message('info', "file count=" . $_FILES['csHMtmp']['name']);
//        }
//
//        //check whether invoice card image file is there
//        if (isset($_FILES['invoiceCardPic']['name'])) {
//            //log_message('info', "Invoice Card Image file received: " . $_FILES['invoiceCardPic']['name'] . ", " .
//            //    $_FILES['invoiceCardPic']['tmp_name']);
//            //log_message('info', "Size: " . $_FILES['invoiceCardPic']['size']);
//
//            $path = $_FILES['invoiceCardPic']['name'];
//            $ext = pathinfo($path, PATHINFO_EXTENSION);
//
//            $invoice_file = md5(uniqid(rand())) . "." . $ext;
//            $bucket = 'appliance-pics';
//            $directory_invoice = "invoices/" . $invoice_file;
//
//            //log_message('info', "Ext: " . $ext);
//
//            $this->s3->putObjectFile($_FILES['invoiceCardPic']['tmp_name'], $bucket, $directory_invoice, S3::ACL_PUBLIC_READ);
//        } else {
//            $invoice_file = "";
//            //log_message('info', "No warranty card file with the booking");
//            //log_message('info', "file count=" . $_FILES['csHMtmp']['name']);
//        }
//
        //Update appliance in database
        $appliance_details = array(
            'brand' => $brand,
            'category' => $category,
            'capacity' => $capacity,
            'model_number' => $model_number,
            'tag' => $tag,
            'purchase_date' => $purchase_date,
            'rating' => $rating
        );
        $id_returned = $this->apis->updateApplianceDetails($appliance_id, $appliance_details);

        log_message('info', "Appliance ID Updated: " . $id_returned);

        $this->jsonResponseString['response'] = $id_returned;
        $this->sendJsonResponse(array('0000', 'success'));
    }

    /**
     * @input: User id
     * @description: Get Appliance details
     * @output:
     */
    function processGetApplianceById() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        //$activity = array('activity' => 'process get booking by user', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        //$this->apis->logTable($activity);

        $appliance_id = $requestData['appliance_id'];
        log_message('info', "Appliance ID: " . $appliance_id);

        $appliance = $this->apis->getApplianceDetailsById($appliance_id);

        $this->jsonResponseString['response'] = $appliance;
        $this->sendJsonResponse(array('0000', 'success'));
    }

    /**
     * @input: User id
     * @description: Remove Appliance from User Wallet
     * @output:
     */
    function processRemoveAppliance() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        //$activity = array('activity' => 'process get booking by user', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        //$this->apis->logTable($activity);

        $appliance_id = $requestData['appliance_id'];
        log_message('info', "Appliance ID: " . $appliance_id);

        $this->apis->removeAppliance($appliance_id);

        $this->jsonResponseString['response'] = "Done";
        $this->sendJsonResponse(array('0000', 'success'));
    }

    /**
     * @input: Get all appliance related data like tags, brands, categories, pricing etc
     * @description:
     * @output:
     */
    function processGetAppDataForAppliances() {
//        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        //$activity = array('activity' => 'process get brands pricing',
        //'data' => json_encode($requestData), 'time' => $this->microtime_float());
        //$this->apis->logTable($activity);
        //$searched_service = $requestData['searched_service'];
        //$user_id = $requestData['username'];

        $user_id = $requestData['user_id'];
//        log_message('info', "User ID: " . $user_id);

        $user_appliances = $this->apis->getApplianceByUser($user_id);
//        log_message('info', count($user_appliances) . " appliances found");

        $services = $this->apis->getAppliancesList();

        $appData = array();

        foreach ($services as $service) {
            //echo $service['services'];
            $hints = $this->apis->getApplianceTagHints($service['services']);
            $brands = $this->apis->getBrandsForServiceId($service['id']);
            $pricing = $this->apis->getPricingForServiceById($service['id'],$this->app_price_mapping_id);

            array_push($appData, array(
                'service_id' => $service['id'],
                'service_name' => $service['services'],
                'hints' => $hints,
                'brands' => $brands,
                'pricing' => $pricing)
            );
        }

        //print_r($appData);
        //TEMP, to be removed later
        //$all_services = $this->apis->GetAllServices();
        $popular_keywords = $this->apis->getPopularKeywords();

        //Get snack bar notification messages
        $msgs = $this->apis->getAroundMessgaes("snack_bar_msgs");
        if(!empty($msgs)){
            $snack_bar_msgs = explode("|", $msgs[0]['message']);
        } else {
            $snack_bar_msgs = array();
        }
        
        $this->jsonResponseString['response'] = array(
            "appData" => $appData,
            "userAppliances" => $user_appliances,
            "allServices" => $services,
            "popularKeywords" => $popular_keywords,
            "snack_bar_msgs" => $snack_bar_msgs
        ); //$appData;
//        log_message('info'," Appliance ". print_r($user_appliances, true));
        $this->sendJsonResponse(array('0000', 'success'));
    }

    /**
     * @input: Get all appliance diagnostics data
     * @description:
     * @output:
     */
    function processGetDiagnosticsData() {
        log_message('info', "Entering: " . __METHOD__);

    $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        //$activity = array('activity' => 'process get brands pricing',
        //'data' => json_encode($requestData), 'time' => $this->microtime_float());
        //$this->apis->logTable($activity);
        //$searched_service = $requestData['searched_service'];
        //$user_id = $requestData['username'];
        //$user_id = $requestData['user_id'];
        $appliance_type = $requestData['appliance_type'];

        //log_message('info', "User ID: " . $user_id);

        $years = $this->diagnostics->get_appliance_diagnostics_distinct_years($appliance_type);
        //log_message('info', "Data available for " . count($years) . " years");
        //log_message('info', "Yrs range " . print_r($years, true));

        $appliance_diagnostics_data = array();

        if (count($years) > 0) {
            $symptoms_array = $this->diagnostics->get_appliance_diagnostics_data_symptoms($appliance_type, "All");
            //log_message('info', "Symptoms (All): " . print_r($symptoms_array, true));


            for ($j = 0; $j < count($symptoms_array); $j++) {
                //Find tips for these symptoms now
                $symptom_tip1_val = $this->diagnostics->get_year_range_for_most_occurences_of_symptom($appliance_type, $symptoms_array[$j]['symptom']);
                $symptom_tip2_val = $this->diagnostics->get_avg_solution_cost_symptom($appliance_type, "All", $symptoms_array[$j]['symptom']);

                $symptom_tip1_text = "Most Issues Reported";
                $symptom_tip2_text = "Average Repair Cost";

                $symptoms_array[$j]['tips'] = array(
                    "tip1_val" => $symptom_tip1_val . " yrs",
                    "tip1_text" => $symptom_tip1_text,
                    "tip2_val" => $symptom_tip2_val,
                    "tip2_text" => $symptom_tip2_text
                );

                //Find L2 issues for these symptoms now
                $symptoms_array[$j]['l2_issues'] = $this->diagnostics->get_appliance_diagnostics_data_l2_issues(
                    $appliance_type, "All", $symptoms_array[$j]['symptom']);

                //Find solutions for all these l2 issues
                for ($m = 0; $m < count($symptoms_array[$j]['l2_issues']); $m++) {
                    $symptoms_array[$j]['l2_issues'][$m]['solutions'] = $this->diagnostics->get_appliance_diagnostics_data_solutions(
                        $appliance_type, "All", $symptoms_array[$j]['symptom'], $symptoms_array[$j]['l2_issues'][$m]['Level2_Problem']);
                    //log_message('info', "Solns: " . print_r($symptoms_array[$j]['solutions'], true));
                    //Find avg cost for each solution as well
                    for ($s = 0; $s < count($symptoms_array[$j]['l2_issues'][$m]['solutions']); $s++) {
                        $symptoms_array[$j]['l2_issues'][$m]['solutions'][$s]['avg_cost'] = $this->diagnostics->get_avg_solution_cost_symptom_solution(
                            $appliance_type, "All", $symptoms_array[$j]['symptom'], $symptoms_array[$j]['l2_issues'][$m]['solutions'][$s]['solution']);
                        //log_message('info', "Avg cost: " . $symptoms_array[$j]['solutions'][$s]['avg_cost']);
                    }
                }
            }

            array_push($appliance_diagnostics_data, array(
                'Age Range' => "All",
                'symptoms_array' => $symptoms_array
            ));
        }

        foreach ($years as $year) {
            $symptoms_array = $this->diagnostics->get_appliance_diagnostics_data_symptoms($appliance_type, $year['Age Range']);
            //log_message('info', "Symptoms (" . $year['Age Range'] . "): " . print_r($symptoms_array, true));

            for ($j = 0; $j < count($symptoms_array); $j++) {
                //Find tips for these symptoms now
                $symptom_tip1_val = $this->diagnostics->get_year_range_for_most_occurences_of_symptom(
                    $appliance_type, $symptoms_array[$j]['symptom']);
                $symptom_tip2_val = $this->diagnostics->get_avg_solution_cost_symptom(
                    $appliance_type, $year['Age Range'], $symptoms_array[$j]['symptom']);

                $symptom_tip1_text = "Most Issues Reported";
                $symptom_tip2_text = "Average Repair Cost";

                $symptoms_array[$j]['tips'] = array(
                    "tip1_val" => $symptom_tip1_val . " yrs",
                    "tip1_text" => $symptom_tip1_text,
                    "tip2_val" => $symptom_tip2_val,
                    "tip2_text" => $symptom_tip2_text
                );

                //Find L2 issues for these symptoms now
                $symptoms_array[$j]['l2_issues'] = $this->diagnostics->get_appliance_diagnostics_data_l2_issues(
                    $appliance_type, $year['Age Range'], $symptoms_array[$j]['symptom']);

                //Find solutions for all these l2 issues
                for ($m = 0; $m < count($symptoms_array[$j]['l2_issues']); $m++) {
                    $symptoms_array[$j]['l2_issues'][$m]['solutions'] = $this->diagnostics->get_appliance_diagnostics_data_solutions(
                        $appliance_type, $year['Age Range'], $symptoms_array[$j]['symptom'], $symptoms_array[$j]['l2_issues'][$m]['Level2_Problem']);
                    //log_message('info', "Solns: " . print_r($symptoms_array[$j]['solutions'], true));
                    //Find avg cost for each solution as well
                    for ($s = 0; $s < count($symptoms_array[$j]['l2_issues'][$m]['solutions']); $s++) {
                        $symptoms_array[$j]['l2_issues'][$m]['solutions'][$s]['avg_cost'] = $this->diagnostics->get_avg_solution_cost_symptom_solution(
                            $appliance_type, $year['Age Range'], $symptoms_array[$j]['symptom'], $symptoms_array[$j]['l2_issues'][$m]['solutions'][$s]['solution']);
                        //log_message('info', "Avg cost: " . $symptoms_array[$j]['solutions'][$s]['avg_cost']);
                    }
                }
            }

            array_push($appliance_diagnostics_data, array(
                'Age Range' => $year['Age Range'],
                'symptoms_array' => $symptoms_array
            ));
        }

        //log_message('info', "Diagnostics data: " . print_r($appliance_diagnostics_data, true));

        $tip1_val = $this->diagnostics->get_year_range_for_most_occurences_of_any_symptom($appliance_type);
        $tip2_val = $this->diagnostics->get_appliance_diagnostics_count($appliance_type);

        $tip1_text = "Most Issues Reported";
        $tip2_text = "Jobs Completed";

        $this->jsonResponseString['response'] = array(
            "appliance_diagnostics_data" => $appliance_diagnostics_data,
            "tips" => array(
                "tip1_val" => $tip1_val . " yrs",
                "tip1_text" => $tip1_text,
                "tip2_val" => (intval($tip2_val) * 10),
                "tip2_text" => $tip2_text
            )
        );

        $this->sendJsonResponse(array('0000', 'success'));
    }
    function processVerifyCouponCode() {
        log_message('info', "Entering: " . __METHOD__);
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        //$activity = array('activity' => 'process get brands pricing',
        //'data' => json_encode($requestData), 'time' => $this->microtime_float());
        //$this->apis->logTable($activity);
        $coupon_code = $requestData['coupon_code'];
        $service = $requestData['service'];
        $result = $this->discount->validate_coupon($coupon_code, $service);
        $this->jsonResponseString['response'] = $result;
        $this->sendJsonResponse(array('0000', 'success'));
    }

    function processUpdateCompleteUserProfile() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        //$activity = array('activity' => 'process get brands pricing',
        //'data' => json_encode($requestData), 'time' => $this->microtime_float());
        //$this->apis->logTable($activity);

        $user_id = $requestData['user_id'];
        $name = $requestData['user_name'];
        $user_email = $requestData['user_email'];
        $home_address = $requestData['user_address'];
        $pincode = $requestData['user_pincode'];

        $updateData = array(
            'name' => $name,
            'user_email' => $user_email,
            'home_address' => $home_address,
            'pincode' => $pincode
        );

        $this->apis->updateCompleteUserProfile($user_id, $updateData);

        $this->jsonResponseString['response'] = "Done";
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
    
    
    function processSearchBookingID() {
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        if (isset($requestData['service_center_id']) && !empty($requestData['search_text'])) {
            $post['length'] = -1;
            $post['search_value'] = trim($requestData['search_text']);
            $post['where'] = array('assigned_vendor_id' => $requestData['service_center_id']);
            $post['column_search'] = array('engineer_booking_action.booking_id', 'booking_details.booking_primary_contact_no',
                'booking_alternate_contact_no');
            $select = "services.services, booking_address, booking_details.booking_id, booking_details.booking_primary_contact_no, "
                    . "users.name as customername,engineer_booking_action.current_status, booking_details.amount_due, booking_details.booking_remarks";
            
            
            $data = $this->engineer_model->get_engineer_action_table_list($post, $select);
            if (!empty($data) && isset($data[0]->booking_id)) {
                $this->jsonResponseString['bookingDetails'] = $data;
                $this->sendJsonResponse(array('0000', 'success'));
            } else {
                $this->sendJsonResponse(array('0014', 'failure'));
            }
        } else {
            $this->sendJsonResponse(array('0015', 'failure'));
        }
    }
    
    function processGetBookingApplianceDetails(){
         $requestData = json_decode($this->jsonRequestData['qsh'], true);
        
         if(!empty($requestData["bookingID"])){
            $booking_id = $requestData["bookingID"];
            
            $unit_details = $this->booking_model->getunit_details($booking_id, "");
            if(!empty($unit_details)){
                $this->jsonResponseString['unitDetails'] = $unit_details;
                $this->sendJsonResponse(array('0000', 'success'));
            } else {
                 $this->sendJsonResponse(array('0017', 'failure'));
            }
         } else {
            $this->sendJsonResponse(array('0016', 'failure'));
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
    /**
     * @desc This is used to get qr url link from App
     */
    function getCustomerQrCode() {
        log_message("info", __METHOD__. " Entering..");
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        if (!empty($requestData["bookingID"])) {

            $response = $this->paytm_payment_lib->generate_qr_code($requestData["bookingID"], QR_CHANNEL_APP, 
                    $requestData["amountPaid"], $requestData["engineerNo"]);
            $result = json_decode($response, TRUE);
            if ($result['status'] == SUCCESS_STATUS) {
                $this->jsonResponseString['QrImageUrl'] = S3_WEBSITE_URL . $result['qr_url'];
                $this->sendJsonResponse(array('0000', 'success'));
            } else {
                log_message("info", __METHOD__ . " QR Failed " . print_r($result, true));
                $this->sendJsonResponse(array('0020', 'QR Not Generated'));
            }
        } else {
            log_message("info", __METHOD__ . " Booking ID Not Found " . print_r($result, true));
            $this->sendJsonResponse(array('0020', 'Booking ID Not Found'));
        }
    }
    
    
                function adjust_zero_pricing($old_pricing) {
        //log_message ('info', "Entering: " . __METHOD__);
        //$new_pricing = '{"service":[],"price":[]}' ;
        $new_pricing = '{"service":["No service pricing found !!!"],"price":[]}';

        $new_arr = json_decode($old_pricing, true);

        $service_new = $new_arr['service'];
        $price_new = $new_arr['price'];

        $service_new2 = array();
        $price_new2 = array();

        foreach ($price_new as $key => $value) {
            if ($value != "0") {
                //unset($service_new[$key]);
                //unset($price_new[$key]);
                array_push($service_new2, $service_new[$key]);
                array_push($price_new2, $price_new[$key]);
            }
        }

        if (count($service_new2) > 0) {
            $new_pricing = json_encode(array(
                'service' => array_filter($service_new2),
                'price' => array_filter($price_new2)), JSON_UNESCAPED_SLASHES);
        }

        return $new_pricing;
    }

    /**  @desc : This functon  for check review
     *   @param : requested hamdyman id and user id
     *   @return :  if review exit return true else false
     */
    function getHandymanProfile($handyman_id, $user_id) {
        //log_message ('info', "Entering: " . __METHOD__);

        $review = $this->apis->checkReview($handyman_id, $user_id);
        $total_review = $this->apis->gethandymanreview($handyman_id);

        $count_review = $this->apis->counthandymanreview($handyman_id);
        $handy = $this->apis->getshandyman($handyman_id);
        $sharetext = $this->apis->getSharetext();

        $i = 0;

        foreach ($handy as $getSearch) {
            $handy[$i]['profile_photo'] = $getSearch['profile_photo'];

            $rating = $getSearch['Rating_by_Agent'];
            if ($rating == "Good") {
                $rating = 4.0;
            } else if ($rating == "Average") {
                $rating = 3.0;
            } else if ($rating == "Exceptional") {
                $rating = 5.0;
            } else if ($rating == "Bad") {
                $rating = 2.0;
            } else if ($rating == "Very Bad") {
                $rating = 1.0;
            }

            $handy[$i]['total_review'] = $count_review + 1;
            if ($count_review > 0) {
                $rating = ($rating + $total_review) / 2;
            }

            $handy[$i]['rating'] = $rating;
            $handy[$i]['sharetext'] = $sharetext;
            if ($review) {
                $handy[$i]['review_by_user'] = "true";
            } else {
                $handy[$i]['review_by_user'] = "false";
            }
            $handy[$i]['reviews'] = $this->apis->getreviewhandyman($handyman_id);

            //remove pricing with 0 charges before returning to user
            $handy[$i]['pricing'] = $this->adjust_zero_pricing($getSearch['pricing']);

            $i = $i + 1;
        }

        return $handy;
    }

    /**
     * @input: void
     * @description:process search request
     * @output: print response
     */
    function processSearchRequest() {
        //log_message ('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);

        $activity = array('activity' => 'start search request', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $user_id = $requestData['username'];
        $area = $requestData['area'];
        $service = $requestData['service'];
        $userLocation = $requestData['userLocation'];
        $sory_by = $requestData['sory_by'];

        $filter_array = null;
        if (array_key_exists('filter_array', $requestData)) {
            $filter_array = json_decode($requestData['filter_array'], true);
        }

        // $marital_status = null;
        // if(array_key_exists('marital_status', $requestData)){
        //     $marital_status = $requestData['marital_status'];
        // }
        // $service_on_call = null;
        // if(array_key_exists('service_on_call', $requestData)){
        //     $marital_status = $requestData['service_on_call'];
        // }
        // $passport = null;
        // if(array_key_exists('passport', $requestData)){
        //     $passport = $requestData['passport'];
        // }
        // $has_id = null; // ID Card
        // if(array_key_exists("has_id", $requestData)) {
        //     $has_id = $requestData['has_id'];
        // }
        // $rating_by = null; // rating
        // if(array_key_exists("rating_by", $requestData)) {
        //     $rating_by = $requestData['rating_by'];
        // }
        // $filter_by = null;
        // if(array_key_exists("filter_by", $requestData)) {
        //     $filter_by = $requestData['filter_by'];
        // }
        // $filter_by_workdays = null;  // working days
        // if(array_key_exists("filter_by_workdays", $requestData)) {
        //     $filter_by_workdays = $requestData['filter_by_workdays'];
        // }
        // $filter_by_exp = null; // experience
        // if(array_key_exists("filter_by_exp", $requestData)) {
        //     $filter_by_exp = $requestData['filter_by_exp'];
        // }
        // $distance = null; // experience
        // if(array_key_exists("distance", $requestData)) {
        //     $distance = $requestData['distance'];
        // }

        $searched_keyword = "";
        if (array_key_exists("searched_keyword", $requestData)) {
            $searched_keyword = $requestData['searched_keyword'];
        }

        $searchHandyman = $this->searchHandymans($area, $service, $user_id, $userLocation, $sory_by, $searched_keyword, $filter_array);

        //$resultsT = print_r($searchHandyman, true);
        //log_message ('info', __METHOD__ . $resultsT);
    }

    function getArea($area, $userLocation) {
        if ($area == "Current Location" && ($userLocation != "" || $userLocation != "did not get user location" || $userLocation != null)) {
            $userLocation = json_decode($userLocation, true);
            return $userLocation;
        } else if ($area != "" && $area != null) {
            $userLocation = $this->calculateLatlonFromAddress($area);
            return $userLocation;
        }
        $ipaddress = getenv('REMOTE_ADDR');
        $new_area = $this->findLocationByIpNum($ipaddress);
        return $new_area;
    }

    /**
     * @input: IP Address
     * @description: find the location of the user according to the ip address
     * @output : array of area
     */
    function findLocationByIpNum($ipaddress) {

        //convert ip address into ip number
        $ipno = $this->Dot2LongIP($ipaddress);
        //find location according to ip number
        $area = array();
        $getLocationFromIpNo = $this->apis->getIp2Location($ipno);
        if ($getLocationFromIpNo) {
            $area['latitude'] = $getLocationFromIpNo[0]['latitude'];
            $area['longitude'] = $getLocationFromIpNo[0]['longitude'];
        }
        return $area;
    }

    function searchHandymans($area, $service, $user_id, $userLocation, $sory_by, $searched_keyword, $filter_array) {
        log_message('info', "Entering: " . __METHOD__);

        $searchApi = $this->apis->searchApiNew($service, $area, $searched_keyword, $this->deviceId, $userLocation, $user_id);

        $holidays = (isset($filter_array['holidays'])) ? $filter_array['holidays'] : null;
        $sunday = (isset($filter_array['sunday'])) ? $filter_array['sunday'] : null;
        $satureday = (isset($filter_array['satureday'])) ? $filter_array['satureday'] : null;
        $monday = (isset($filter_array['monday'])) ? $filter_array['monday'] : null;
        $tuesday = (isset($filter_array['tuesday'])) ? $filter_array['tuesday'] : null;
        $wednesday = (isset($filter_array['wednesday'])) ? $filter_array['wednesday'] : null;
        $thursday = (isset($filter_array['thursday'])) ? $filter_array['thursday'] : null;
        $friday = (isset($filter_array['friday'])) ? $filter_array['friday'] : null;
        $passport = (isset($filter_array['passport'])) ? $filter_array['passport'] : null;
        $service_on_call = (isset($filter_array['service_on_call'])) ? $filter_array['service_on_call'] : null;
        $id_card = (isset($filter_array['id_card'])) ? $filter_array['id_card'] : null;
        $married = (isset($filter_array['married'])) ? $filter_array['married'] : null;
        $single = (isset($filter_array['single'])) ? $filter_array['single'] : null;
        $rating_filter = (isset($filter_array['rating_filter'])) ? $filter_array['rating_filter'] : null;
        $experience_filter_min = (isset($filter_array['experience_filter_min'])) ? $filter_array['experience_filter_min'] : null;
        $experience_filter_max = (isset($filter_array['experience_filter_max'])) ? $filter_array['experience_filter_max'] : null;

        if ($searchApi != "Result Not Found") {
            $array = array();
            $i = 0;

            $newHandymanArray = array();
            if ($filter_array) {
                if ($holidays == 'holidays' || $sunday == 'sunday' || $satureday == 'satureday') {
                    foreach ($searchApi as $handyman) {
                        if ($handyman['works_on_weekends'] == "Yes") {
                            if (!in_array($handyman, $newHandymanArray)) {
                                array_push($newHandymanArray, $handyman);
                            }
                        }
                    }
                    $searchApi = $newHandymanArray;
                }
                if ($monday == 'monday' || $tuesday == 'tuesday' || $wednesday == 'wednesday' || $thursday == 'thursday' || $friday == 'friday') {
                    foreach ($searchApi as $handyman) {
                        if ($handyman['work_on_weekdays'] == "Yes") {
                            if (!in_array($handyman, $newHandymanArray)) {
                                array_push($newHandymanArray, $handyman);
                            }
                        }
                    }
                    $searchApi = $newHandymanArray;
                }
                if ($passport == 'passport') {
                    foreach ($searchApi as $handyman) {
                        if ($handyman['passport'] == "Yes") {
                            if (!in_array($handyman, $newHandymanArray)) {
                                array_push($newHandymanArray, $handyman);
                            }
                        }
                    }
                    $searchApi = $newHandymanArray;
                }
                if ($service_on_call == 'service_on_call') {
                    foreach ($searchApi as $handyman) {
                        if ($handyman['service_on_call'] == "Yes") {
                            if (!in_array($handyman, $newHandymanArray)) {
                                array_push($newHandymanArray, $handyman);
                            }
                        }
                    }
                    $searchApi = $newHandymanArray;
                }
                if ($id_card == 'id_card') {
                    foreach ($searchApi as $handyman) {
                        if ($handyman['id_proof_name'] != "N/A") {
                            if (!in_array($handyman, $newHandymanArray)) {
                                array_push($newHandymanArray, $handyman);
                            }
                        }
                    }
                    $searchApi = $newHandymanArray;
                }
                if ($single == 'single') {
                    foreach ($searchApi as $handyman) {
                        if ($handyman['marital_status'] == "Single") {
                            if (!in_array($handyman, $newHandymanArray)) {
                                array_push($newHandymanArray, $handyman);
                            }
                        }
                    }
                    $searchApi = $newHandymanArray;
                }
                if ($married == 'married') {
                    foreach ($searchApi as $handyman) {
                        if ($handyman['marital_status'] == "Married") {
                            if (!in_array($handyman, $newHandymanArray)) {
                                array_push($newHandymanArray, $handyman);
                            }
                        }
                    }
                    $searchApi = $newHandymanArray;
                }
                if (!empty($rating_filter)) {
                    foreach ($searchApi as $handyman) {
                        if ($handyman['rating'] >= 3.5) {
                            if (!in_array($handyman, $newHandymanArray)) {
                                array_push($newHandymanArray, $handyman);
                            }
                        }
                    }
                    $searchApi = $newHandymanArray;
                }
                if (!empty($experience_filter_min)) {
                    foreach ($searchApi as $handyman) {
                        if ($handyman['experience'] >= $filter_array['experience_filter_min']) {
                            if (!in_array($handyman, $newHandymanArray)) {
                                array_push($newHandymanArray, $handyman);
                            }
                        }
                    }
                    $searchApi = $newHandymanArray;
                }
                if (!empty($experience_filter_max)) {
                    foreach ($searchApi as $handyman) {
                        if ($handyman['experience'] <= $filter_array['experience_filter_max']) {
                            if (!in_array($handyman, $newHandymanArray)) {
                                array_push($newHandymanArray, $handyman);
                            }
                        }
                    }
                    $searchApi = $newHandymanArray;
                }
            }

            if ($sory_by == "experience") {
                $searchApi = $this->sortexperience($searchApi);
            } else if ($sory_by == "rating") {
                $searchApi = $this->sortrating($searchApi);
            }

            //$resultT = print_r($searchApi, true);
            //log_message ('info', __METHOD__ . $resultT);

            $this->jsonResponseString['response'] = $searchApi;
            $this->sendJsonResponse(array('0000', 'success'));
        } else {
            //return "Data not found";
            $this->jsonResponseString['response'] = array("Data not found");
            $this->sendJsonResponse(array('0010', 'success'));
        }
    }

    public function checkKey($key) {
        if (isset($key) && !empty($key)) {
            return $key;
        } else {
            return false;
        }
    }

    public function filterHandymanByMaritalStatus($searchApi, $marital_status) {
        $newHandymanArray = array();
        if ($marital_status == "Single") {
            foreach ($searchApi as $handyman) {
                if ($handyman['marital_status'] == "Married") {
                    array_push($newHandymanArray, $handyman);
                }
            }
        } else if ($marital_status == "Married") {
            foreach ($searchApi as $handyman) {
                if ($handyman['marital_status'] == "Single") {
                    array_push($newHandymanArray, $handyman);
                }
            }
        }
        return $newHandymanArray;
    }

    public function filterHandymansByDistance($searchApi, $distance) {
        $newHandymanArray = array();
        foreach ($searchApi as $handyman) {
            if ($handyman['distance'] <= $distance) {
                array_push($newHandymanArray, $distance);
            }
        }
        return $newHandymanArray;
    }

    function filterHandymanByExp($searchApi, $filter_by_exp) {
        $newHandymanArray = array();
        if ($filter_by_exp == "zero_two") {
            foreach ($searchApi as $handyman) {
                if ($handyman['experience'] >= 0 && $handyman['experience'] <= 2) {
                    array_push($newHandymanArray, $handyman);
                }
            }
        } else if ($filter_by_exp == "two_five") {
            foreach ($searchApi as $handyman) {
                if ($handyman['experience'] >= 2 && $handyman['experience'] <= 5) {
                    array_push($newHandymanArray, $handyman);
                }
            }
        } else if ($filter_by_exp == "six_ten") {
            foreach ($searchApi as $handyman) {
                if ($handyman['experience'] >= 5 && $handyman['experience'] <= 10) {
                    array_push($newHandymanArray, $handyman);
                }
            }
        } else if ($filter_by_exp == "ten_plus") {
            foreach ($searchApi as $handyman) {
                if ($handyman['experience'] >= 10) {
                    array_push($newHandymanArray, $handyman);
                }
            }
        }
        return $newHandymanArray;
    }

    function filterHandymanByWorkDays($searchApi, $filter_by_workdays) {
        $newHandymanArray = array();
        if ($filter_by_workdays == "Weekends") {
            foreach ($searchApi as $handyman) {
                if ($handyman['works_on_weekends'] == "Yes") {
                    array_push($newHandymanArray, $handyman);
                }
            }
        } else if ($filter_by_workdays == "Weekdays") {
            foreach ($searchApi as $handyman) {
                if ($handyman['work_on_weekdays'] == "Yes") {
                    array_push($newHandymanArray, $handyman);
                }
            }
        }
        return $newHandymanArray;
    }

    function filterHandymans($searchApi, $filter_by) {
        $newHandymanArray = array();
        if ($filter_by == "rating_plus") {
            foreach ($searchApi as $handyman) {
                if ($handyman['rating'] >= 3.5) {
                    array_push($newHandymanArray, $handyman);
                }
            }
        } else if ($filter_by == "ID") {
            foreach ($searchApi as $handyman) {
                if ($handyman['id_proof_name'] != "N/A") {
                    array_push($newHandymanArray, $handyman);
                }
            }
        } else if ($filter_by == "Married") {
            foreach ($searchApi as $handyman) {
                if ($handyman['marital_status'] == "Married") {
                    array_push($newHandymanArray, $handyman);
                }
            }
        } else if ($filter_by == "Single") {
            foreach ($searchApi as $handyman) {
                if ($handyman['marital_status'] == "Single") {
                    array_push($newHandymanArray, $handyman);
                }
            }
        } else if ($filter_by == "Passport") {
            foreach ($searchApi as $handyman) {
                if ($handyman['passport'] == "Yes") {
                    array_push($newHandymanArray, $handyman);
                }
            }
        } else if ($filter_by == "Service_On_Call") {
            foreach ($searchApi as $handyman) {
                if ($handyman['service_on_call'] == "Yes") {
                    array_push($newHandymanArray, $handyman);
                }
            }
        }
        return $newHandymanArray;
    }

    function removeIdLessHandyman($searchApi) {
        $newHandymanArray = array();
        foreach ($searchApi as $handyman) {
            if ($handyman['id_proof_name'] != "N/A") {
                array_push($newHandymanArray, $handyman);
            }
        }

        return $newHandymanArray;
    }

    function removeLessratingHandyman($searchApi) {
        $newHandymanArray = array();
        foreach ($searchApi as $handyman) {
            if ($handyman['rating'] >= 3.5) {
                array_push($newHandymanArray, $handyman);
            }
        }

        return $newHandymanArray;
    }

    /**
     *  @desc : This function for sort rating in asscending order
     *  param : array(searchApi)
     *  @return : sorted data
     */
    function sortrating(&$searchApi) {

        $code = "return strnatcmp(\$a['rating'], \$b['rating']);";
        usort($searchApi, create_function('$a,$b', $code));
        return array_reverse($searchApi);
    }

    /**
     *  @desc : This function for sort experience in asscending order
     *  @param : array(searchApi)
     *  @return : sorted data
     */
    function sortexperience(&$searchApi) {
        $code = "return strnatcmp(\$a['experience'], \$b['experience']);";
        usort($searchApi, create_function('$a,$b', $code));
        return array_reverse($searchApi);
    }

    function removeLongDistanceHandymanByService($searchApi, $service) {

        $serviceradius = $this->apis->getServiceRadius($service);
        $serviceradius = $serviceradius[0]['distance'];
        $newHandymanArray = array();
        foreach ($searchApi as $handyman) {
            if ($handyman['distance'] <= $serviceradius) {
                $handyman['distance'] = $handyman['distance'] . " " . 'km';
                array_push($newHandymanArray, $handyman);
            }
        }

        return $newHandymanArray;
    }

    function sort_array_distance($people) {
        $sortArray = array();

        foreach ($people as $person) {
            foreach ($person as $key => $value) {
                if (!isset($sortArray[$key])) {
                    $sortArray[$key] = array();
                }
                $sortArray[$key][] = $value;
            }
        }

        $orderby = "distance";
        $orderbyrate = "rating"; //change this to whatever key you want from the array

        array_multisort($sortArray[$orderby], SORT_ASC, $sortArray[$orderbyrate], SORT_DESC, $people);

        return $people;
    }

    /** @description* this function  is for to calculate Latitude and  Longitude
     *  @return :  Latitude and  Longitude
     */
    function distance($lat1, $lon1, $lat2, $lon2, $unit) {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    /**
     *  @desc : This function  get handyman detail for input id
     *  param : handyman id
     *  @return : array handyman detail
     */
    function getrating($handyman_id) {
        $review = $this->apis->gethandymanreview($handyman_id);
        return $review;
    }

    /**  @desc : This functon  for calculate latitude and longitude
     *   @param : area
     *   @return :  latitude and longitude
     */
    function calculateLatlonFromAddress($area) {

        $address = $area;
        $address = str_replace(" ", "+", $address);
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=$address&sensor=false";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response, true);
        $area = array();

        if (array_key_exists("results", $response_a)) {
            if (array_key_exists("geometry", $response_a['results'][0])) {
                if (array_key_exists("location", $response_a['results'][0]['geometry'])) {
                    $area['latitude'] = $response_a['results'][0]['geometry']['location']['lat'];
                    $area['longitude'] = $response_a['results'][0]['geometry']['location']['lng'];
                }
            }
        }
        return $area;
    }

    function processstartSearchRequest() {
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        $activity = array('activity' => 'start search request', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);
        $email = $requestData['username'];
        $password = $requestData['usertoken'];

        $GetAllServices = $this->apis->GetAllServiceNames();
        //print_r($GetAllServices);
        $this->jsonResponseString['response'] = $GetAllServices;
        $this->sendJsonResponse(array('0000', 'success'));
    }

    function processResetPassRequest() {
        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        $activity = array('activity' => 'reset password request', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $email = $requestData['username'];
        $password = $requestData['password'];
        $reset_token = $requestData["reset_token"];
        $reset_token = base64_encode(hash_hmac('sha256', $reset_token, "authToken"));
        $loginStatus = $this->apis->isAlreadyRegistered($email);

        if ($loginStatus) {
            $checkToken = $this->apis->getAuthToken($email);
            if ($checkToken && $checkToken[0]['user_token'] == $reset_token) {
                $this->resetPassword($email, $password, $reset_token);
            } else {
                $this->sendJsonResponse(array('0009', 'failure'));
            }
        } else {
            $this->sendJsonResponse(array('0008', 'failure'));
        }
    }

    function resetPassword($email, $password, $token) {
        $resArray = $this->apis->resetPassword($email, $password);

        $this->jsonResponseString['response'] = $resArray;
        $this->sendJsonResponse(array('0000', 'success'));
    }

    function processForgotPassRequest() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);
        //print_r($requestData);
        $activity = array('activity' => 'forgot password request', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $email = $requestData['username'];
        $loginStatus = $this->apis->isAlreadyRegistered($email);

        if ($loginStatus) {
            $this->sendResetPassToken($email);
        } else {
            $this->sendJsonResponse(array('0008', 'failure'));
        }
    }

    function sendResetPassToken($email) {
        $token = $this->apis->updateAuthToken($email);
        $this->sendPassResetMail($email, $token);
    }

    function sendPassResetMail($email, $token) {
        $this->load->library('email');
        $this->email->initialize(array(
            'protocol' => 'smtp',
            'smtp_host' => 'smtp.sendgrid.net',
            'smtp_user' => 'trakleaf',
            'smtp_pass' => 'Numetric12345!@#$%',
            'smtp_port' => 587,
            'crlf' => "\r\n",
            'newline' => "\r\n",
            'mailtype' => 'html'
            )
        );

        $this->email->from('support@boloaaka.com', 'Boloaaka Team');
        $this->email->to($email);
        $this->email->subject("Password Reset Token");
        $message = "<html><head></head><body><p>Hi,</p><p>You recently asked to reset your Boloaaka password. </p>";
        $message .= "<p>Token : $token</p><p>Use above token to reset your password.</p>";
        $message .= " <p>Sincerely,</p><p>The Boloaaka Team</p></body></html>";
        $this->email->message($message);
        if ($this->email->send()) {

            $resArray = array();
            $resArray['token'] = $token;
            $resArray['notify'] = true;
            $this->jsonResponseString['response'] = $resArray;
            $this->sendJsonResponse(array('0000', 'success'));
        }
    }

    function processLoginRequest() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);

        $activity = array('activity' => 'login request', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $email = $requestData['username'];
        $password = $requestData['password'];
        $type = $requestData['type'];


        //$deviceInfo = $requestData["deviceInfo"];
        //print_r($deviceInfo);

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $res = $this->apis->userLogin($email, $password, $type, $this->deviceId);
            if (array_key_exists("user_name", $requestData)) {
                $user_name = array("name" => $requestData['user_name']);
                $this->apis->updateUserProfile($this->deviceId, $user_name);
            }
            /* $this->apis->saveLocation();

              if(array_key_exists("location", $requestData)) {
              $location = $requestData["location"];
              $location = $this->makeAssocArray($location);

              $this->apis->saveLocation($location,$email ,$country);
              }
              else {
              $ipaddress = getenv('REMOTE_ADDR');
              $this->findLocationByIpNumber($ipaddress, $email);
              }
              $this->apis->saveDeviceinfo($deviceInfo, $email, $this->deviceId); */
            if ($res) {
                $resArray = array();
                $resArray['token'] = $res[0]['token'];
                //$resArray['country'] = $res[0]['country'];
                $resArray['email'] = $res[0]['email'];
                $resArray['notify'] = true;
                $this->jsonResponseString['response'] = $resArray;
                $this->sendJsonResponse(array('0000', 'success'));
            } else {
                $this->sendJsonResponse(array('0007', 'failure'));
            }
        } else {
            $this->sendJsonResponse(array('0005', 'failure'));
        }
    }

    function processSignupRequest() {
        log_message('info', "Entering: " . __METHOD__);

        $requestData = json_decode($this->jsonRequestData['qsh'], true);

        $activity = array('activity' => 'signup request', 'data' => json_encode($requestData), 'time' => $this->microtime_float());
        $this->apis->logTable($activity);

        $email = $requestData['username'];
        $password = $requestData['password'];
        $country = $requestData["country"];

        $deviceInfo = $requestData["deviceInfo"];
        //print_r($deviceInfo);

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $loginStatus = $this->apis->isAlreadyRegistered($email);

            if ($loginStatus) {
                $this->sendJsonResponse(array('0006', 'failure'));
            } else {
                $insertRegisterData = array('email' => $email, 'password' => $password, 'country' => $country);
                $res = $this->apis->insertRegisterData($insertRegisterData);
                //$this->apis->saveLocation();

                if (array_key_exists("location", $requestData)) {
                    $location = $requestData["location"];
                    $location = $this->makeAssocArray($location);

                    $this->apis->saveLocation($location, $email, $country);
                } else {
                    $ipaddress = getenv('REMOTE_ADDR');
                    $this->findLocationByIpNumber($ipaddress, $email);
                }
                $this->apis->saveDeviceinfo($deviceInfo, $email, $this->deviceId);
                $this->jsonResponseString['response'] = $res;
                $this->sendJsonResponse(array('0000', 'success'));
            }
        } else {
            $this->sendJsonResponse(array('0005', 'failure'));
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
    
    /**
     * @desc: This function gets called through the Exotel App  and insert the call details 
     * of those users who gave call after sending rating sms 
     * @retun:void()
     */
    public function pass_through_rating_missed_call(){
        if($this->input->post()){
            $callDetails = $this->input->post();
        }
        else{
            $activity = array('activity' => 'process exotel request', 'data' => json_encode($_GET), 'time' => $this->microtime_float());
            $this->apis->logTable($activity);

            //Refer: http://support.exotel.in/support/solutions/articles/48283-working-with-passthru-applet
            $callDetails['callSid'] = (isset($_GET['CallSid'])) ? $_GET['CallSid'] : null;
            $callDetails['from_number'] = (isset($_GET['From'])) ? ltrim($_GET['From'],'0') : null;
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
        
        //insert in database
        $insert_id = $this->apis->insertRatingPassthruCall($callDetails);
        if($insert_id){
            log_message('info', __METHOD__.'Call Details Added');
            //insert rating if missed call on good number
            if($callDetails['To'] === GOOD_MISSED_CALL_RATING_NUMBER || $callDetails['To'] === GOOD_MISSED_CALL_RATING_NUMBER_KNOWLARITY){
                $this->do_process_for_missed_call_rating($callDetails['from_number']);
            }
            else if($callDetails['To'] === POOR_MISSED_CALL_RATING_NUMBER || $callDetails['To'] === POOR_MISSED_CALL_RATING_NUMBER_KNOWLARITY){
                //Send Poor Rating Missed call  Notification to concern Person
                $receiverArray['employee'] = explode(",",POOR_RATING_MISSED_CALL_NOTIFICATION_EMPLOYYE_IDS);
                $this->miscelleneous->send_bad_rating_email("2",NULL,$callDetails['from_number'] );
                $this->push_notification_lib->create_and_send_push_notiifcation(POOR_RATING_MISSED_CALL_NOTIFICATION,$receiverArray,array());
            }
            
        }else{
            log_message('info', __METHOD__.'Error In Adding Call Details');
        }
        
        
        
        $this->output->set_header("HTTP/1.1 200 OK");
    }
    
    /**
     * @desc: This function is used to insert rating if only one booking is 
     * exist for the given missed call number and rating column is null and current status is completed
     * @param $from_number string 
     * @retun:void()
     */
     public function do_process_for_missed_call_rating($from_number) {

        $check_booking_count = $this->booking_model->get_missed_call_rating_booking_count($from_number);
        //insert rating if booking is not empty and have only 1 booking
        //otherwise log this and show on view
        if (!empty($check_booking_count)) {
            //insert rating in booking_details
            $insert_id = $this->booking_model->update_booking($check_booking_count[0]['booking_id'], array('rating_stars' => MISSED_CALL_DEFAULT_RATING));
            
            if (!empty($insert_id)) {
                log_message('info', __FUNCTION__ . 'booking_details updated successfully for missed call rating. booking_id: ' . $check_booking_count[0]['booking_id']);
                $remarks = 'Rating:' . MISSED_CALL_DEFAULT_RATING . '.good service.';
                $data = array('booking_id' => $check_booking_count[0]['booking_id'],
                    'old_state' => _247AROUND_COMPLETED,
                    'new_state' => RATING_NEW_STATE,
                    'remarks' => $remarks,
                    'agent_id' => _247AROUND_DEFAULT_AGENT,
                    'partner_id' => _247AROUND);
                
                $booking_state_change_insert_id = $this->booking_model->insert_booking_state_change($data);
                
                if ($booking_state_change_insert_id) {
                    log_message('info', __FUNCTION__ . 'Inserted successfully in state change for missed call rating. booking_id: ' . $check_booking_count[0]['booking_id']);
                } else {
                    log_message('info', __FUNCTION__ . 'Error In inserting rating data for missed call rating. booking_id: ' . $check_booking_count[0]['booking_id']);
                }
            } else {
                log_message('info', __FUNCTION__ . 'Error In updating rating data for missed call rating. booking_id: ' . $check_booking_count[0]['booking_id']);
            }
        } else {
            log_message('info', __METHOD__ . 'customer gave missed call on good number and more than one bookings found for this number:' . $from_number);
        }
    }
/*
 * This function is used to handle missed call for fake reschedule 
 */
    public function pass_through_fake_reschedule_call(){
        //Get Data and save into log_table 
        if($this->input->post()){
            $responseData = $this->input->post();
        }
        else{
            $responseData = $this->input->get();
            $activity = array('activity' => 'process exotel request', 'data' => json_encode($responseData), 'time' => $this->microtime_float());
            $this->apis->logTable($activity);
        }
        //$responseData = json_decode('{"CallSid":"de642f94743e8bf2a65e280c848f25d0","CallFrom":"9058523795","CallTo":"01139595200","Direction":"incoming","Created":"Wed, 03 Jan 2018 11:31:33","DialCallDuration":"242","RecordingUrl":"https:\/\/s3-ap-southeast-1.amazonaws.com\/exotelrecordings\/aroundhomz\/de642f94743e8bf2a65e280c848f25d0.mp3","StartTime":"2018-01-03 11:31:33","EndTime":"1970-01-01 05:30:00","DialCallStatus":"completed","CallType":"completed","DialWhomNumber":"08010155247","flow_id":"45714","tenant_id":"20524","From":"9058523795","To":"01139595200","RecordingAvailableBy":"Wed, 03 Jan 2018 11:40:52","CurrentTime":"2018-01-03 11:35:52","Legs":[{"Number":"08010155247","Type":"single","OnCallDuration":"229","CauseCode":"NORMAL_CLEARING","Cause":"16"}]}',TRUE);
        //Define Blank DataArray
        $dataArray['callSid'] = $dataArray['from_number']= $responseData['to_number'] = $responseData['StartTime'] = $responseData['EndTime'] = NULL;
        //Insert Value in DataArray
        if(isset($responseData['CallSid'])){
            $dataArray['callSid'] = $responseData['CallSid'];
        }
        if(isset($responseData['callSid'])){
            $dataArray['callSid'] = $responseData['callSid'];
        }
        if(isset($responseData['From'])){
            $dataArray['from_number'] =  ltrim($responseData['From'],'0');
        }
        if(isset($responseData['from_number'])){
            $dataArray['from_number'] =  ltrim($responseData['from_number'],'0');
        }
         if(isset($responseData['CallTo'])){
            $dataArray['to_number'] = $responseData['CallTo'];
        }
        if(isset($responseData['To'])){
            $dataArray['to_number'] = $responseData['To'];
        }
         if(isset($responseData['StartTime'])){
            $dataArray['StartTime'] = $responseData['StartTime'];
        }
         if(isset($responseData['EndTime'])){
            $dataArray['EndTime'] = $responseData['EndTime'];
        }
        //insert in fake_reschedule_missed_call
        $insert_id = $this->apis->insertFakeReschedulePassthruCall($dataArray);
        if($insert_id){
            log_message('info', __METHOD__.'Call Details Added');
            //Process Fake Reshedule handling
             $this->miscelleneous->fake_reschedule_handling($dataArray['from_number'],_247AROUND_DEFAULT_AGENT,_247AROUND_DEFAULT_AGENT_NAME,"Fake Rescheduled Miss Call");
        }else{
            log_message('info', __METHOD__.'Error In Adding Call Details');
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
        //$requestData = array("booking_id" => "PV-16565919062733", "service_id" => 28, "partner_id" => 236, "request_type" => 'Installation & Demo');
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
        //$requestData = array("technical_problem" => "1");
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
        //$requestData = array("technical_symptom" => "1", "technical_defect" => "1");
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
