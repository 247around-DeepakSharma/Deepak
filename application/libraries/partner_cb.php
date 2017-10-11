<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

/**
 * Partner  Callback APIs for Status Updates
 *
 * This will be called from our Controllers like Booking when an action is
 * performed on a booking so that the status update happens in the Partner
 * CRM as well.
 *
 * @author Abhay
 */
class partner_cb {

    private $My_CI;

    function __Construct() {
	$this->My_CI = & get_instance();

	$this->My_CI->load->model('partner_model');
	$this->My_CI->load->library('partner_sd_cb');
    }

    /**
     * @desc: This method is used to check partner api callback needs to be called or not.
     * if partner api needs to be called, then get partner library and function name from array and pass the data
     * to callback fn.
     *
     * @param: booking id
     */
    function partner_callback($booking_id) {
	log_message('info', __METHOD__ . "=> Booking ID: " . $booking_id);

	// it return data to call partner api, if need to call partner api other wise return false
	$data = $this->My_CI->partner_model->get_data_for_partner_callback($booking_id);
	if (!empty($data)) {

	    $call_details = $this->callback_array($data['partner_id'], $data['current_status']);
	    if ($call_details) {

		$get_callback_library = $this->get_callback_library($data['partner_id']);

		$this->My_CI->$get_callback_library->$call_details($data);

		return true;
	    } else {

		return false;
	    }
	} else {

	    return true;
	}
    }

    /**
     * @desc: This metod stores partner call back function name in the array.
     * This array stores -- 1st index -- Partner Id
     * This array stores -- 2nd index -- booking status
     * @param: Partner Id
     * @param: status
     * @return: funtion name
     */
    function callback_array($partner_id, $state) {
	$snapdeal_partner_id = 1;
        $jeeves_partner_id = 247030;

	$callback_array = array();

	$callback_array[$snapdeal_partner_id]['Completed'] = 'update_status_complete_booking';
	$callback_array[$snapdeal_partner_id]['Cancelled'] = 'update_status_cancel_booking';
	$callback_array[$snapdeal_partner_id]['Pending'] = 'update_status_schedule_booking';
	$callback_array[$snapdeal_partner_id]['FollowUp'] = 'update_status_schedule_booking';
	$callback_array[$snapdeal_partner_id]['Rescheduled'] = 'update_status_reschedule_booking';
        
        
        $callback_array[$jeeves_partner_id]['Pending'] = 'update_jeeves_status_schedule_booking';
	$callback_array[$jeeves_partner_id]['FollowUp'] = 'update_jeeves_status_schedule_booking';
	$callback_array[$jeeves_partner_id]['Rescheduled'] = 'update_jeeves_status_schedule_booking';
        $callback_array[$jeeves_partner_id]['Completed'] = 'update_jeeves_status_schedule_booking';
	$callback_array[$jeeves_partner_id]['Cancelled'] = 'update_jeeves_status_schedule_booking';

	if (isset($callback_array[$partner_id][$state])) {
	    return $callback_array[$partner_id][$state];
	} else {
	    return false;
	}
    }

    /**
     * @desc: This method stores partner api callback library
     * @param: Partner ID
     * @return: library name
     */
    function get_callback_library($partner_id) {
	$snapdeal_partner_id = 1;
        $jeeves_partner_id = 247030;

	$library[$snapdeal_partner_id] = 'partner_sd_cb';
        $library[$jeeves_partner_id] = 'partner_sd_cb';

	return $library[$partner_id];
    }

}
