<?php

if (!defined('BASEPATH')){
    exit('No direct script access allowed');
}

error_reporting(E_ALL);
            ini_set('display_errors', 1);
            ini_set('memory_limit', -1);

class Engineer extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();
        $this->load->model('service_centers_model');

        $this->load->model('partner_model');
        $this->load->model('vendor_model');
        $this->load->library("pagination");
        $this->load->library('asynchronous_lib');
        $this->load->library('booking_utilities');
        $this->load->library("session");
        $this->load->library('s3');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');


        $this->load->library("miscelleneous");
        
    }
    
    function index(){
        echo "ACESS DENIED";
    }
    
    function review_engineer_action_form(){
      
        $where['where'] = array("engineer_booking_action.current_status" => "InProcess");
        $data = $this->engineer_model->get_engineer_action_table_list($where, "engineer_booking_action.booking_id, amount_due, engineer_table_sign.amount_paid");
       
        foreach ($data as $key => $value) {
            $unitWhere = array("engineer_booking_action.booking_id" => $value->booking_id);
            $ac_data = $this->engineer_model->getengineer_action_data("engineer_booking_action.engineer_id, internal_status,", $unitWhere);
            $status = _247AROUND_CANCELLED;
            foreach ($ac_data as $ac_table) {
                if($ac_table['internal_status'] == _247AROUND_COMPLETED){
                    $status = _247AROUND_COMPLETED;
                }
            }
           
            $data[$key]->status = $status;
            if(!empty($ac_data[0]['engineer_id'])){
                $data[$key]->engineer_name = $this->engineer_model->get_engineers_details(array("id" => $ac_data[0]['engineer_id']), "name");
                
            } else {
                $data[$key]->engineer_name = "";
            }
        }
        //$this->load->view('service_centers/header');
        $this->load->view("service_centers/review_engineer_action", array("data" => $data));
      
    }
    
    function get_approve_booking_form($booking_id) {

        $data['booking_id'] = $booking_id;
        $data['booking_history'] = $this->booking_model->getbooking_history($booking_id);

        $bookng_unit_details = $this->booking_model->getunit_details($booking_id);

        foreach($bookng_unit_details as $key1 => $b){
            $broken = 0;
            foreach ($b['quantity'] as $key2 => $u) {

                $unitWhere = array("engineer_booking_action.booking_id" => $booking_id, 
                    "engineer_booking_action.unit_details_id" => $u['unit_id'], "service_center_id" => $data['booking_history'][0]['assigned_vendor_id']);
                $en = $this->engineer_model->getengineer_action_data("engineer_booking_action.*", $unitWhere);
                $bookng_unit_details[$key1]['quantity'][$key2]['en_serial_number'] = $en[0]['serial_number'];
                $bookng_unit_details[$key1]['quantity'][$key2]['en_serial_number_pic'] = $en[0]['serial_number_pic'];
                $bookng_unit_details[$key1]['quantity'][$key2]['en_is_broken'] = $en[0]['is_broken'];
                $bookng_unit_details[$key1]['quantity'][$key2]['en_internal_status'] = $en[0]['internal_status'];
                if($en[0]['is_broken'] == 1){
                    $broken = 1;
                }
            }
           $bookng_unit_details[$key1]['is_broken'] = $broken; 
                 
        }
        $sig_table = $this->engineer_model->getengineer_sign_table_data("*", array("booking_id" => $booking_id,
            "service_center_id" => $data['booking_history'][0]['assigned_vendor_id']));
        if (!empty($sig_table)) {
            $data['signature'] = $sig_table[0]['signature'];
            $data['amount_paid'] = $sig_table[0]['amount_paid'];
        } else {
             $data['amount_paid'] = 0;
             $data['signature'] ="";
        }

        $data['bookng_unit_details'] = $bookng_unit_details;

        //$this->load->view('service_centers/header');
        $this->load->view("service_centers/approve_booking", $data);
    }
    
    function review_engineer_action_by_admin(){
        
        $where['where_in'] = array("engineer_booking_action.current_status" => array("InProcess", "Completed", "Cancelled"),
            "booking_details.current_status" => array(_247AROUND_PENDING, _247AROUND_RESCHEDULED));
        $data = $this->engineer_model->get_engineer_action_table_list($where, "engineer_booking_action.booking_id, amount_due, engineer_table_sign.amount_paid,"
                . "engineer_table_sign.pincode as en_pincode, engineer_table_sign.address as en_address, "
                . "booking_details.booking_pincode, booking_details.assigned_vendor_id, booking_details.booking_address");
       
        foreach ($data as $key => $value) {
            $is_broken = false;
            $unitWhere = array("engineer_booking_action.booking_id" => $value->booking_id);
            $ac_data = $this->engineer_model->getengineer_action_data("engineer_booking_action.engineer_id, internal_status,engineer_booking_action.is_broken", $unitWhere);
            $status = _247AROUND_CANCELLED;
            foreach ($ac_data as $ac_table) {
                if($ac_table['internal_status'] == _247AROUND_COMPLETED){
                    $status = _247AROUND_COMPLETED;
                }
                if($ac_table['is_broken'] ==  1){
                    $is_broken = true;
                }
            }
           
            $data[$key]->status = $status;
            $data[$key]->is_broken = $is_broken;
            if(!empty($ac_data[0]['engineer_id'])){
                $data[$key]->engineer_name = $this->engineer_model->get_engineers_details(array("id" => $ac_data[0]['engineer_id']), "name");
                
                
            } else {
                $data[$key]->engineer_name = "";
            }
            
            $data[$key]->sf_name = $this->vendor_model->getVendorDetails("name", array("id" => $data[0]->assigned_vendor_id))[0]['name'];
        }
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/review_engineer_action', array("data" => $data));

    }

}
