<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

class Do_background_process extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
	parent::__Construct();

	$this->load->helper(array('form', 'url'));
	$this->load->model('booking_model');
	$this->load->library('booking_utilities');
	$this->load->library('asynchronous_lib');
	$this->load->library('s3');
	$this->load->library('email');
    }

    /**
     *  @desc : Function to assign vendors for pending bookings,
     *  @param : service center
     *  @return : void
     */
    function assign_booking() {
	log_message('info', "Entering: " . __METHOD__);

	$service_center = $this->input->post('service_center');
	foreach ($service_center as $booking_id => $service) {
	    if ($service != "Select") {
		log_message('info', "Booking ID: " . $booking_id . ", Service centre: " . $service);

		//Assign service centre
		$this->booking_model->assign_booking($booking_id, $service);

		//Prepare job card
		$this->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);

		//Send mail to vendor, no Note to vendor as of now
		$message = "";
		$this->booking_utilities->lib_send_mail_to_vendor($booking_id, $message);
	    }
	}
    }

}
