<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

error_reporting(E_ERROR);
ini_set('display_errors', '0');

class Around_scheduler extends CI_Controller {
	 function __Construct() {
        parent::__Construct();

        $this->load->model('around_scheduler_model');
        $this->load->model('booking_model');

        $this->load->library('s3');
        $this->load->library('email');
        $this->load->library('notify');
        $this->load->helper(array('form', 'url'));
    }

    /**
     * @desc: This is used to send SMS to customers for whom delivery is scheduled for Today
     * to inform them about free installation when they give a missed call.
     */
    function send_reminder_installation_sms_today() {
        log_message ('info', __METHOD__ . '=> Entering...');
        
        //Get all queries where SMS needs to be sent
	$data1 = $this->around_scheduler_model->get_reminder_installation_sms_data_today();
        
        //Tag queries where Vendor is not available
        $data2 = $this->booking_model->searchPincodeAvailable($data1, PINCODE_AVAILABLE);
        
        //Send SMS to only customers where Vendor is Available
	$sms['tag'] = "sd_edd_missed_call_reminder";
        
	foreach ($data2 as $value) {
            $sms['phone_no'] = $value->booking_primary_contact_no;

            //Ordering of SMS data is important, check SMS template before changing it
            $sms['smsData']['message'] = $this->notify->get_product_free_not($value->services);
            $sms['smsData']['service'] = $value->services;

            $sms['booking_id'] = $value->booking_id;
            $sms['type'] = "user";
            $sms['type_id'] = $value->user_id;

            $this->notify->send_sms_acl($sms);                
            
	}
        
        log_message ('info', __METHOD__ . '=> Exiting...');
    }

    /**
     * @desc: This is used to send SMS to customers for whom delivery is scheduled for tomorrow 
     * or later to inform them about free installation when they give a missed call.
     */
    function send_reminder_installation_sms_future() {
        log_message ('info', __METHOD__ . '=> Entering...');
        
        //Get all queries where SMS needs to be sent
	$data1 = $this->around_scheduler_model->get_reminder_installation_sms_data_future();
        
        //Tag queries where Vendor is not available
        $data2 = $this->booking_model->searchPincodeAvailable($data1);
        
        //Send SMS to only customers where Vendor is Available
	$sms['tag'] = "sd_edd_missed_call_reminder";
        
	foreach ($data2 as $value) {
            if ($value->vendor_status !== "Vendor Not Available") {
                $sms['phone_no'] = $value->booking_primary_contact_no;

                //Ordering of SMS data is important, check SMS template before changing it
                $sms['smsData']['message'] = $this->notify->get_product_free_not($value->services);
                $sms['smsData']['service'] = $value->services;

                $sms['booking_id'] = $value->booking_id;
                $sms['type'] = "user";
                $sms['type_id'] = $value->user_id;

                $this->notify->send_sms_acl($sms);                
            }
	}
        
        log_message ('info', __METHOD__ . '=> Exiting...');
    }

    /**
     * @desc: This is used to send SMS to customers for whom delivery was scheduled for past 
     * or earlier to inform them about free installation when they give a missed call.
     */
    function send_reminder_installation_sms_past() {
        log_message ('info', __METHOD__ . '=> Entering...');
        
        //Get all queries where SMS needs to be sent
	$data1 = $this->around_scheduler_model->get_reminder_installation_sms_data_past();
        
        //Tag queries where Vendor is not available
        $data2 = $this->booking_model->searchPincodeAvailable($data1);
        
        //Send SMS to only customers where Vendor is Available
	$sms['tag'] = "sd_edd_missed_call_reminder";
        
	foreach ($data2 as $value) {
            if ($value->vendor_status !== "Vendor Not Available") {
                $sms['phone_no'] = $value->booking_primary_contact_no;

                //Ordering of SMS data is important, check SMS template before changing it
                $sms['smsData']['message'] = $this->notify->get_product_free_not($value->services);
                $sms['smsData']['service'] = $value->services;

                $sms['booking_id'] = $value->booking_id;
                $sms['type'] = "user";
                $sms['type_id'] = $value->user_id;

                $this->notify->send_sms_acl($sms);                
            }
	}
        
        log_message ('info', __METHOD__ . '=> Exiting...');
    }

    /**
     * @desc: This is used to send SMS to customers for whom Geyser delivery was scheduled for today 
     * and yesterday to inform them about free installation when they give a missed call.
     */
    function send_reminder_installation_sms_geyser_in_delhi() {
        log_message ('info', __METHOD__ . '=> Entering...');
        
        //Get all queries where SMS needs to be sent
	$data1 = $this->around_scheduler_model->get_reminder_installation_sms_data_geyser_delhi();
        
        //Send SMS to only customers where Vendor is Available
	$sms['tag'] = "sd_edd_missed_call_reminder";
        
	foreach ($data1 as $value) {
            $sms['phone_no'] = $value->booking_primary_contact_no;

            //Ordering of SMS data is important, check SMS template before changing it
            $sms['smsData']['message'] = "Free";
            $sms['smsData']['service'] = "Geyser";

            $sms['booking_id'] = $value->booking_id;
            $sms['type'] = "user";
            $sms['type_id'] = $value->user_id;

            $this->notify->send_sms_acl($sms) ;               
	}
        
        log_message ('info', __METHOD__ . '=> Exiting...');
    }


}