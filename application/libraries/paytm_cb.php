<?php

/**
 * Description of paytm_cb
 *
 * @author abhay
 */
class paytm_cb {
   
    private $My_CI;
    private $header = null;
    private $partner = null;
    private $requestUrl = '';
    private $jsonRequestData = null;
    private $jsonResponseString;
    private $appName = "24x7Around";
    private $secretKey = "Z1DBK3EH01ZUMPJU";
    private $salt = "QW8QQW4VVKQEQYXVRRY3TTKMTXRHNCNSOPSXFZFF9LI37ZZZXQUSDUN8EGFTRQKN";
    private $appConstant = "6VFKKLZ1Y4";
    private $url = "http://sandbox.servify.in:5009/api/v1/ServiceRequest/fulfillRequest";

    function __Construct() {
        $this->My_CI = & get_instance();
        $this->My_CI->load->model('partner_model');
    }
    
    function bookingUpdateStatusCallback($data){
        log_message('info', __METHOD__ . "=> Booking ID: " . $data['booking_id']);
        $this->requestUrl = __METHOD__;
         
        $this->partner = $data['partner_id'];
        
        $postData = array(
                "ReferenceID" => $data['booking_id'],
                "Status" => $data['current_status'],
                "orderId" => $data['order_id'],
                "RequestDetails" => array(
                    "Reason" => $data['ENA'],
                    "Remarks" => ""
                )
                
            );
        return $this->post_data($postData);
    }
    
    function post_data($postData) {
        $curl = curl_init();
        
        //JSON with the Application Constant and the current unix timestamp in milliseconds
        $app = json_encode(array("appC" => $this->appConstant, "tzU" => time()), true);
        
        //Using the SECRET_KEY and SALT, constructed a key using the PBKDF2 function
        $key = hash_pbkdf2("sha256", $this->secretKey, $this->salt, 100000, 16);
        
        //Encrypt the stringified JSON with the key, and an initialization vector (iv).
        $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
        //$encryptionMethod = "AES256";
        
        $encryptedMessage = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $app,  MCRYPT_MODE_CBC, $iv);

        $this->header = array(
            "app: " . $this->appName,
            "dr9se2q: " . $key,
            "co1cx2: " . $encryptedMessage,
            "content-type: application/json"
        );

        $this->jsonRequestData = json_encode($postData);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $this->jsonRequestData,
            CURLOPT_HTTPHEADER => $this->header,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        //Capture both response as well as error messages
        $this->jsonResponseString['response'] = $response;
        $this->jsonResponseString['error'] = $err;

        $responseData = array("data" => $this->jsonResponseString);

        $activity = array(
            'partner_id' => $this->partner,
            'activity' => $this->requestUrl,
            'header' => json_encode($this->header),
            'json_request_data' => $this->jsonRequestData,
            'json_response_string' => json_encode($responseData, JSON_UNESCAPED_SLASHES));

        $this->My_CI->partner_model->log_partner_activity($activity);

        if ($err) {
            log_message('info', "cURL Error #:" . $err);
            return "cURL Error #:" . $err;
        } else {
            log_message('info', "cURL Response #:" . $response);
            return $response;
        }
    }
}
