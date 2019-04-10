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
        $this->My_CI->load->library('paytm_cb');
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
                $call_details = $this->callback_array($data['origin_partner_id'], $data['current_status']);
                if ($call_details) { 
                    $get_callback_library = $this->get_callback_library($data['origin_partner_id']);
                    $this->My_CI->$get_callback_library->$call_details($data);
                    return true;
                }
                else{
                    return false;
                }
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
     * @return: function name
     */
    function callback_array($partner_id, $state) {

	$callback_array = array();

	$callback_array[SNAPDEAL_ID]['Completed'] = 'update_status_complete_booking';
	$callback_array[SNAPDEAL_ID]['Cancelled'] = 'update_status_cancel_booking';
	$callback_array[SNAPDEAL_ID]['Pending'] = 'update_status_schedule_booking';
	$callback_array[SNAPDEAL_ID]['FollowUp'] = 'update_status_schedule_booking';
	$callback_array[SNAPDEAL_ID]['Rescheduled'] = 'update_status_reschedule_booking';
        
        
        $callback_array[JEEVES_ID]['Pending'] = 'update_jeeves_status_schedule_booking';
	$callback_array[JEEVES_ID]['FollowUp'] = 'update_jeeves_status_schedule_booking';
	$callback_array[JEEVES_ID]['Rescheduled'] = 'update_jeeves_status_schedule_booking';
        $callback_array[JEEVES_ID]['Completed'] = 'update_jeeves_status_schedule_booking';
	$callback_array[JEEVES_ID]['Cancelled'] = 'update_jeeves_status_schedule_booking';
        
        $callback_array[AKAI_ID]['Pending'] = 'addNewAkaiBooking';
        $callback_array[AKAI_ID]['Completed'] = 'update_akai_closed_details';
        $callback_array[AKAI_ID]['Cancelled'] = 'update_akai_closed_details';
        
        $callback_array[PAYTM]['Pending'] = 'booking_updated_request';
	$callback_array[PAYTM]['FollowUp'] = 'booking_updated_request';
	$callback_array[PAYTM]['Rescheduled'] = 'booking_updated_request';
        $callback_array[PAYTM]['Completed'] = 'booking_completed_request';
	$callback_array[PAYTM]['Cancelled'] = 'booking_cancelled_request';

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

	$library[SNAPDEAL_ID] = 'partner_sd_cb';
        $library[JEEVES_ID] = 'partner_sd_cb';
        $library[PAYTM] = 'paytm_cb';
        $library[AKAI_ID] = 'partner_sd_cb';

	return $library[$partner_id];
    }

}
