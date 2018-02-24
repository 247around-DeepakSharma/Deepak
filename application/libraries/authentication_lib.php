<?php

class authentication_lib {
    public function __construct() {
        $this->A_N = & get_instance();
        $this->A_N->load->model('partner_model');
    }
   function checkAPIAuthentication() {
        log_message('info', __FUNCTION__ . "=> Entering ");
        $h = $this->getallheaders();
        if (empty($h)) {
            log_message('info', __METHOD__ . ":: Empty Header: ");
            return array(false,ERR_GENERIC_ERROR_CODE, ERR_GENERIC_ERROR_MSG);
        } else {
            $token = $h['Authorization'];
            //Validate token
            $this->partner = $this->A_N->partner_model->validate_partner($token);
            if ($this->partner) {
                return array(true,$h);
            } else {
                log_message('info', __METHOD__ . ":: Invalid token: " . $token);
                //invalid token
                return array(false,ERR_INVALID_AUTH_TOKEN_CODE, ERR_INVALID_AUTH_TOKEN_MSG);
            }
        }
    }
    function getallheaders() {
        //Use this if you are using Nginx

        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;

        //It works only with Apache
//        return getallheaders();
    }
}
