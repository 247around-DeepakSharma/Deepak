<?php

/**
 * Description of paytm_cb - This library is used to update booking status on paytm server
 */
class paytm_cb {
   
     private $My_CI;
     private $jsonResponseString;
     private $submit = "Secure Sign In";
     private $notredirect = "true";
     private $auth_call = "true";

    function __Construct() {
        $this->My_CI = & get_instance();
        $this->My_CI->load->model('partner_model');
        $this->My_CI->load->model('booking_model');
	$this->My_CI->load->model('user_model');
    }
    
    function paytm_curl_call($url, $header, $postData){
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL,$url); 
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if($header){ 
         curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
        }
        $response = curl_exec($ch);
        $err = curl_error($ch);
        
        curl_close($ch);
       
        $this->jsonResponseString['response'] = $response;
        $this->jsonResponseString['error'] = $err;

        $responseData = array("data" => $this->jsonResponseString);
        
        /*
        $activity = array(
            'partner_id' => PAYTM_ID,
            'activity' => json_encode(array('activity' => __METHOD__,'url'=>$url)),
            'header' => json_encode($header, JSON_UNESCAPED_SLASHES),
            'json_request_data' => $postData,
            'json_response_string' => json_encode($responseData, JSON_UNESCAPED_SLASHES)
        );
        */
        
        $activity = array(
            'partner_id' => PAYTM_ID,
            'activity' => __METHOD__,
            'header' => json_encode($header, JSON_UNESCAPED_SLASHES),
            'json_request_data' => $postData,
            'json_response_string' => json_encode($responseData, JSON_UNESCAPED_SLASHES)
        );

        $this->My_CI->partner_model->log_partner_activity($activity);
        
        if ($err) {
            log_message('info', "cURL Error #:" . $err);
	    return $responseData;
        } else {
            log_message('info', "cURL Response #:" . $response);
            return $responseData;
        }
    }
    
    function update_booking_on_paytm($postData, $booking_id){
	$failurePostData = $postData;
	$postData = json_encode($postData);
        $api_where = array(
            "entity_id" => PAYTM_ID,
            "entity_type" => _247AROUND_PARTNER_STRING,
            "url_tag" => "booking_update"
        );
        $authData = $this->My_CI->partner_model->get_api_authentication_details("auth_token, url", $api_where);
        $header = array(
            'authtoken: '.$authData[0]['auth_token'],
            'Content-Type: application/json',
        );
        
        $response = $this->paytm_curl_call($authData[0]['url'], $header, $postData);
        if($response['data']['error']){
        	//$this->send_error_mail($response['data'], $booking_id);
		return false;
        }
        else{
        $response = json_decode($response['data']['response']);
        if($response->success == false){
            if($this->auth_call == "true"){
              $this->auth_call = "false";
              $this->getCode();
              $this->update_booking_on_paytm($failurePostData, $booking_id);
            }
            else{ 
                //$this->send_error_mail($response, $booking_id);
		return false; 
            }
        }
        else{
            return true; 
        }
       }
    }
    
    function get_post_data($data){
        $postData = array();
        $estimated_service_delivery = array(
            "from" => date('Y-m-d\TH:i:s.v\Z', strtotime($data['booking_date'])), 
            "scheduled_date" => date('Y-m-d\TH:i:s.v\Z', strtotime($data['booking_date'])),
            "to" =>date('Y-m-d\TH:i:s.v\Z', strtotime($data['booking_date']))
        );
        $event = array(
            "code" => $data['partner_current_status'],
            "reason_code" => $data['partner_internal_status'], 
            "remarks" => $data['current_status'], 
            "time" => date('Y-m-d\TH:i:s.v\Z')
        );
        $field_executive = array(
            "mobile_no" => "9555000247",
            "name" => "247Around"
        );
       
        $postData['estimated_service_delivery'] = $estimated_service_delivery;
        $postData['event'] = $event;
        $postData['field_executive'] = $field_executive;
        $postData['vendor_reference_id'] = str_replace('Q-', '', $data['booking_id']);
        $postData['vendor_name'] = "247Around-INSTALL";
        return $postData;
    }
    
    function booking_updated_request($data){
        $postData = $this->get_post_data($data); 
        return $this->update_booking_on_paytm($postData, $data['booking_id']);
    }
    
    function booking_completed_request($data){
	$user = $this->My_CI->user_model->get_users_by_any(array("user_id" => $data['user_id']));
        $postData = array();
        $customer = array(
            "mobile_no" => $user[0]["phone_number"],
            "name" => $user[0]["name"],
            "signature_image" => "signature"
        );
        
        $event = array(
            "code" => $data['partner_current_status'], 
            "reason_code" => $data['partner_internal_status'],
            "remarks" => $data['current_status'],
            "time" => date('Y-m-d\TH:i:s.v\Z')
        );
        
        $service_attempt = array(
            "amount" => null,
            "charge_description" => null,
            "rating" => null,
            "status" => true,
	    "serialNo" => ""
        );
        
        $postData['customer'] = $customer;
        $postData['event'] = $event;
        $postData['service_attempt'] = $service_attempt;
        $postData['vendor_reference_id'] = str_replace('Q-', '', $data['booking_id']);
        $postData['vendor_name'] = "247Around-INSTALL";
        return $this->update_booking_on_paytm($postData, $data['booking_id']);
        
    }
    
    function booking_cancelled_request($data){
        $postData = array();
        
        $event = array(
            "code" => $data['partner_current_status'], 
            "reason_code" => $data['partner_internal_status'],
            "remarks" => $data['current_status'],
            "time" => date('Y-m-d\TH:i:s.v\Z')
        );
        
        $postData['event'] = $event;
        $postData['vendor_reference_id'] = str_replace('Q-', '', $data['booking_id']);
        $postData['vendor_name'] = "247Around-INSTALL";
        return $this->update_booking_on_paytm($postData, $data['booking_id']);
       
    }
    
    function send_error_mail($data, $booking_id){
        $email_template = $this->My_CI->booking_model->get_booking_email_template("paytm_booking_updation_fail");
        if (!empty($email_template)) {
            $to = $email_template[1];
            $cc = $email_template[3];
            $bcc = $email_template[5];
            $subject = vsprintf($email_template[4], $booking_id);
            $emailBody = vsprintf($email_template[0],json_encode($data));
            $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, $bcc, $subject, $emailBody, "", "paytm_booking_updation_fail");
        } 
    } 


    function getCode(){
	 $api_where = array(
             "entity_id" => PAYTM_ID,
             "entity_type" => _247AROUND_PARTNER_STRING,
             "url_tag" => "code"
         );
         $api_select = "user_name, password, state, client_id, client_secret, url";
         $authData = $this->My_CI->partner_model->get_api_authentication_details($api_select, $api_where);
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
	$url = sprintf("%s?%s", $authData[0]['url'], http_build_query($postData));
	$response = $this->paytm_curl_call($url, "",  json_encode(array()));
        if($response['data']['response']){
	   $code = json_decode($response['data']['response'])->code;
	   $tokenHeader = array(
             'Content-Type: application/x-www-form-urlencoded',
           );
           $api_token = array(
             "entity_id" => PAYTM_ID,
             "entity_type" => _247AROUND_PARTNER_STRING,
             "url_tag" => "authtoken"
           );
           $authTokenData = $this->My_CI->partner_model->get_api_authentication_details("url, client_id, client_secret", $api_token);
           $tokenPostData = array(
	     "code" => $code,
             "client_id" => $authTokenData[0]['client_id'],
             "grant_type" => "authorization_code",
             "client_secret" => $authTokenData[0]['client_secret']
           );
           $urlToken = sprintf("%s?%s", $authTokenData[0]['url'], http_build_query($tokenPostData));
           $responseToken = $this->paytm_curl_call($urlToken, $tokenHeader,  json_encode(array()));
	   if($responseToken['data']['response']){
	      $auth_token = json_decode($responseToken['data']['response'])->access_token; 
              $this->My_CI->partner_model->update_api_authentication_details(array("entity_id"=>PAYTM_ID,"entity_type"=>"partner"), array("auth_token"=> $auth_token));
	    }
	}
     } 
}
