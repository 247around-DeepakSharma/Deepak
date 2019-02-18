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
     private $userName = "lgfac-services-247around@paytm.com";
     private $password = "lgfac@2019";
     private $clientSecret = "54e126df-2367-4b71-b749-6c0ca0e3bf52";
     private $clientID = "internal-lgfac_staging";
     private $url = "https://persona-staging.paytm.com/oauth2/authorize";
     private $state = "a1b2c3d4";
     private $submit = "Secure Sign In";
     private $notredirect = "true";
     private $auth_call = true;

    function __Construct() {
        $this->My_CI = & get_instance();
        $this->My_CI->load->model('partner_model');
        $this->My_CI->load->model('booking_model');
    }
    
    function paytm_curl_call($url, $header, $postData){
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL,$url); 
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
       
        $response = curl_exec($ch);
        $err = curl_error($ch);
        
        curl_close($ch);
       
        $this->jsonResponseString['response'] = $response;
        $this->jsonResponseString['error'] = $err;

        $responseData = array("data" => $this->jsonResponseString);

        $activity = array(
            'partner_id' => PAYTM_ID,
            'activity' => $url,
            'header' => json_encode($this->header),
            'json_request_data' => $this->jsonRequestData,
            'json_response_string' => json_encode($responseData, JSON_UNESCAPED_SLASHES));

        $this->My_CI->partner_model->log_partner_activity($activity);
        
        if ($err) {
            log_message('info', "cURL Error #:" . $err);
        } else {
            log_message('info', "cURL Response #:" . $response);
            return $responseData;
        }
    }
    
    function update_booking_on_paytm($postData){
        $api_where = array(
            "entity_id" => PAYTM_ID,
            "entity_type" => _247AROUND_PARTNER_STRING,
            "url_tag" => "booking_update"
        );
        $authData = $this->partner_model->get_api_authentication_details("auth_token, url", $api_where);
        $header = array(
            'authtoken: '.$authData[0]['auth_token'],
            'Content-Type: application/json',
        );
        
        $response = $this->paytm_curl_call($authData[0]['url'], $header, $postData);
        if($response['status'] == "auth_token_failure"){
            if($this->$auth_call == true){
              $this->$auth_call = false;
              $this->get_auth_token();
              $this->update_booking_on_paytm($postData);
            }
            else{
                $this->send_error_mail($response); 
            }
        }
        else{
            $this->send_error_mail($response); 
        }
        
    }
    
    function get_post_data($data){
        $postData = array();
        $estimated_service_delivery = array(
            "from" => $data['booking_date'], 
            "scheduled_date" => $data['booking_date'], 
            "to" => $data['booking_date']
        );
        $event = array(
            "code" => $data['partner_current_status'],
            "reason_code" => $data['partner_internal_status'], 
            "remarks" => $data['247around_current_status'], 
            "time" => "2019-01-23T05:14:14.123Z"
        );
        $field_executive = array(
            "mobile_no" => "9555000247",
            "name" => "247Around"
        );
       
        $postData['estimated_service_delivery'] = $estimated_service_delivery;
        $postData['event'] = $event;
        $postData['field_executive'] = $field_executive;
        $postData['vendor_reference_id'] = "D1WQI15";
        $postData['vendor_reference_id'] = "247Around-INSTALL";
        return $postData;
    }
    
    function booking_updated_request($data){
        $postData = $this->get_post_data($data); 
        $response = $this->update_booking_on_paytm($postData);
    }
    
    function booking_completed_request($data){
        $user = $this->user_model->get_users_by_any(array("id" => $data['user_id']));
        $postData = array();
        $customer = array(
            "mobile_no" => $user[0]["phone_number"],
            "name" => $user[0]["name"],
            "signature_image" => "signature"
        );
        
        $event = array(
            "code" => $data['partner_current_status'], 
            "reason_code" => $data['partner_internal_status'],
            "remarks" => $data['247around_current_status'],
            "time" => "2019-01-23T05:14:14.123Z"
        );
        
        $service_attempt = array(
            "amount" => 800,
            "charge_description" => "Addtional pipes",
            "rating" => "4",
            "status" => true
        );
        
        $postData['customer'] = $customer;
        $postData['event'] = $event;
        $postData['service_attempt'] = $service_attempt;
        $postData['vendor_reference_id'] = "D1WQI15";
        $postData['vendor_reference_id'] = "247Around-INSTALL";
        $response = $this->update_booking_on_paytm($postData);
        
    }
    
    function booking_cancelled_request($data){
        $postData = array();
        
        $event = array(
            "code" => $data['partner_current_status'], 
            "reason_code" => $data['partner_internal_status'],
            "remarks" => $data['247around_current_status'],
            "time" => "2019-01-23T05:14:14.123Z"
        );
        
        $postData['event'] = $event;
        $postData['vendor_reference_id'] = "D1WQI15";
        $postData['vendor_reference_id'] = "247Around-INSTALL";
        $response = $this->update_booking_on_paytm($postData);
       
    }
    
    function send_error_mail($data){
        //$email_template = $this->booking_model->get_booking_email_template("paytm_booking_update_error_mail");
        //if (!empty($email_template)) {
            $to = "kalyanit@247around.com"; //$email_template[1];
            $cc = "kalyanitekpure07@gmail.com"; //$email_template[3];
            $bcc = "kalyanit@247around.com"; //$email_template[5];
            $subject = "booking update on paytm fail"; //$email_template[4];
            $emailBody = "booking update on paytm fail"; //$email_template[0]; //vsprintf($login_template[0], $login_email);
            $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, $bcc, $subject, $emailBody, "", "paytm_booking_update_error_mail");
        //} 
    }
    
    function get_auth_token(){
        $api_where = array(
            "entity_id" => PAYTM_ID,
            "entity_type" => _247AROUND_PARTNER_STRING,
            "url_tag" => "authtoken"
        );
        $api_select = "user_name, password, state, client_id, client_secret";
        $authData = $this->partner_model->get_api_authentication_details($api_select, $api_where);
        
        $postData = array(
            "username" => $authData[0]['user_name'],
            "password" => $authData[0]['password'],
            "state" => $authData[0]['state'],
            "submit" => $this->submit,
            "notredirect" => $this->notredirect,
            "client_id" => $authData[0]['client_id'],
            "response_type" => "code",
            "client_secret" => $authData[0]['client_secret']
        );
        $url = sprintf("%s?%s", $this->url, http_build_query($postData));
        $response = $this->paytm_curl_call($url, "", array());
        $response = json_decode($response);
        if($response["code"]){
            $api_where = array(
                "entity_id" => PAYTM_ID,
                "entity_type" => _247AROUND_PARTNER_STRING,
            );
            $update = $this->partner_model->update_api_authentication_details($api_where, array("auth_token"=>$response["code"]));
            if($update){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            echo false;
        }
    }
}
