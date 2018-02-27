<?php

class authentication_lib {
    public function __construct() {
        $this->A_N = & get_instance();
        $this->A_N->load->model('partner_model');
    }
   function checkAPIAuthentication() {
        log_message('info', __FUNCTION__ . "=> Entering ");
        $h = $this->getallheaders();
        if (empty($h)) 
        {
            log_message('info', __METHOD__ . ":: Empty Header: ");
            return array(false,$h,ERR_GENERIC_ERROR_CODE, ERR_GENERIC_ERROR_MSG);
        } 
        else {
            if(array_key_exists("Mid", $h)){
                if($h['Mid'] == MERCHANT_GUID){
                    return array(true,$h);  
                }
                else {
                     return array(false,$h,ERR_INVALID_MERCHANT_GUID, ERR_INVALID_MERCHANT_GUID_MSG);
                }
        }
        else{
            return array(false,$h,ERR_INVALID_MERCHANT_GUID, MID_NOT_AVAILABLE_MSG);
        }
        }
    }
    function getallheaders() {
        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}
