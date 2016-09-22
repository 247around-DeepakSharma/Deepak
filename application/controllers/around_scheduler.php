<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//error_reporting(E_ERROR);
//ini_set('display_errors', '0');

class Around_scheduler extends CI_Controller {
	 function __Construct() {
        parent::__Construct();

        $this->load->model('around_scheduler_model');

        $this->load->library('s3');
        $this->load->library('email');
        $this->load->library('notify');
        $this->load->helper(array('form', 'url'));
    }

    /**
     * @desc: This is used to send SMS to customer who have given miss call to customer support number.
     * Note: SMS will not be sent to those customers who has EDD as tomorrow and vendor is not available
     * in their pincode.
     */
    function send_reminder_installation_sms() {
	$data = $this->around_scheduler_model->get_reminder_installation_sms_data();
	$sms['tag'] = "sd_edd_missed_call_reminder";
	foreach ($data as $value) {
	    $sms['phone_no'] = $value['booking_primary_contact_no'];
	    $sms['smsData']['service'] = $value['services'];
	    $sms['booking_id'] = $value['booking_id'];
	    $sms['type'] = "user";
	    $sms['type_id'] = $value['user_id'];
	    $this->notify->send_sms($sms);
	}
    }


}