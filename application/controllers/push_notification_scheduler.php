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
        $this->load->model('reusable_model');
        $this->load->model('invoices_model');
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
    /*
     * This function is used to send pending spare parts count to partner via push notification
     * This function will run through crone, once in a day
     */
    function pending_spare_parts_count_to_partners(){
        //query - SELECT COUNT(spare_parts_details.booking_id) as pending_count, spare_parts_details.partner_id FROM (spare_parts_details) JOIN booking_details ON 
        //booking_details.booking_id=spare_parts_details.booking_id WHERE `spare_parts_details`.`status` = 'Spare Parts Requested' AND booking_details.current_status IN ('Pending', 'Rescheduled') 
        //GROUP BY spare_parts_details.partner_id
        $data = $this->reusable_model->get_search_result_data("spare_parts_details","COUNT(spare_parts_details.booking_id) as pending_count,spare_parts_details.partner_id",
                array("spare_parts_details.status"=>SPARE_PARTS_REQUESTED),
                array("booking_details"=>"booking_details.booking_id=spare_parts_details.booking_id"),
                NULL,NULL,array("booking_details.current_status"=>array(_247AROUND_PENDING, _247AROUND_RESCHEDULED)),NULL,array("spare_parts_details.partner_id"));
        foreach($data as $values){
            //Send Push Notification
            $receiverArray['partner'] = array($values['partner_id']);
            $notificationTextArray['msg'] = array($values['pending_count']);
            $this->push_notification_lib->create_and_send_push_notiifcation(PENDING_SPARE_PARTS_TO_PARTNER,$receiverArray,$notificationTextArray);
            //End Push Notification
        }
    }
    /*
     * This Function is used to send "To Be Paid" amount to partners via push notification
     * This function will run through crone, once in a day
     */
    function pending_invoice_amount_to_postpaid_partners(){
        $invoicing_summary = $this->invoices_model->getsummary_of_invoice("partner", array('active' => '1','is_prepaid'=>0));
        foreach($invoicing_summary as $values){
            //Send Push Notification
            $receiverArray['partner'] = array($values['id']);
            $notificationTextArray['msg'] = array(abs($values['final_amount']));
            $this->push_notification_lib->create_and_send_push_notiifcation(PENDING_INVOICES_TO_PARTNERS_POSTPAID,$receiverArray,$notificationTextArray);
            //End Push Notification
        }
    }
}

