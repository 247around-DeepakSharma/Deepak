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
 * @author anujaggarwal
 */
class partner_cb {

    private $My_CI;
   
    function __Construct() {
	    $this->My_CI = & get_instance();

	    $this->My_CI->load->model('partner_model');
        $this->My_CI->load->library('partner_sd_cb');
    }

    function partner_callback($booking_id){
        log_message('info', __METHOD__ . "=> Booking ID: " . $booking_id);
        // it return data to call partner api, if need to call partner api other wise return false 
        $data  = $this->My_CI->partner_model->get_data_for_partner_callback($booking_id);
        if(!empty($data)){

            $call_details = $this->callback_array($data['partner_id'], $data['current_status']);
            if($call_details){

                $get_callback_library = $this->get_callback_library($data['partner_id']);

                $this->My_CI->$get_callback_library->$call_details($data);

            } else {

               return false;
            }
          
        } else {

            return true;
        }

    }

    function callback_array($partner_id, $state){

        $callback_array = array() ;

        $callback_array['1']['Completed'] = 'update_status_complete_booking' ;
        $callback_array['1']['Cancelled'] = 'update_status_cancel_booking' ;
        $callback_array['1']['Pending']   = 'update_status_schedule_booking' ;
        $callback_array['1']['FollowUp']  = 'update_status_schedule_booking' ;
        $callback_array['1']['Rescheduled'] = 'update_status_reschedule_booking' ;

        if(isset($callback_array[$partner_id][$state])){

            return $callback_array[$partner_id][$state];

        } else {

            return false;
        }

    }

    function get_callback_library($partner_id){

        $library['1'] = 'partner_sd_cb' ;

        return $library[$partner_id];
    }

}