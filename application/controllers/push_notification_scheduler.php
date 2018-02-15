<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');


ini_set('memory_limit', '-1');
//3600 seconds = 60 minutes
ini_set('max_execution_time', 360000);

class Push_notification_scheduler extends CI_Controller {

    function __Construct() {
        parent::__Construct();

        $this->load->model('penalty_model');
        $this->load->library('push_notification_lib');
    }
    function update_booking_request_to_service_centers(){
        $query = $this->penalty_model->get_update_booking_penalty_data();
        $result = $query->result_array();
        foreach($result as $data){
            $bookingCount =  count(explode(",",$data['booking_group']));
             //Send Push Notification
                $receiverArray['vendor'] = array($data['assigned_vendor_id']);
                $notificationTextArray['msg'] = array($bookingCount,$data['booking_group']);
                $this->push_notification_lib->create_and_send_push_notiifcation(UPDATE_BOOKING_TO_AVOID_PENALTY,$receiverArray,$notificationTextArray);
                //End Push Notification
        }
    }
}

