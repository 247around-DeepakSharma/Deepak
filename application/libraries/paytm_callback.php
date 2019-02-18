<?php

/**
 * Description of paytm_cb
 *
 * 
 */
class paytm_callback {
    
    
    
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


     function __Construct() {
         $this->My_CI = & get_instance();
         $this->My_CI->load->model('partner_model');
     }

     function getCode(){
         $curl = curl_init();

         $postData = array(
             "username" => $this->userName,
             "password" => $this->password,
             "state" => $this->state,
             "submit" => $this->submit,
             "notredirect" => $this->notredirect,
             "client_id" => $this->clientID,
             "response_type" => "code",
             "client_secret" => $this->clientSecret

         );
         $url = sprintf("%s?%s", $this->url, http_build_query($postData));

          curl_setopt_array($curl, array(
             CURLOPT_URL => $url,
             CURLOPT_RETURNTRANSFER => true,
             CURLOPT_HTTPGET => true,
         ));

         $response = curl_exec($curl);
         $err = curl_error($curl);

         $this->jsonResponseString['response'] = $response;
         $this->jsonResponseString['error'] = $err;

        return $response;
    }
    
    function getServiceAccepted(){}
    
}
