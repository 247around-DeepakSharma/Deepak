<?php

/**
 * Consumer app for Sending SMS to mobile devices.
 *
 * @author anujaggarwal
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

error_reporting(E_ALL);
ini_set('display_errors', '1');

class Rmq_test_consumer extends CI_Controller {

    function __Construct() {
        parent::__Construct();
        
        $this->load->library('rabbitmq/send_sms');
    }

    public function rmq_test_send_sms() {
        log_message('info', __FUNCTION__ . ": looks like things are working");

//        $this->send_sms->send_sms_msg91("8826423424", "Hellos" . gettimeofday(true));
        
        $i=1;
        while (1) {
            $this->send_sms->send_sms_msg91("8826423424", "Hellos " . $i++);
            
            sleep(1);
        }
    }    

}
