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
            $data[$key]->lineItem = $this->engineer_model->get_engineer_action_table_list($unitWhere, "engineer_booking_action.*,"
                    . " booking_unit_details.appliance_brand,booking_unit_details.appliance_category,booking_unit_details.appliance_capacity, price_tags");
            
        }
        $this->load->view('service_centers/header');
        $this->load->view("service_centers/review_engineer_action", array("data" => $data));
      
    }
    
    function get_approve_booking_form($booking_id){
       
        $data['booking_id'] = $booking_id;
        $data['booking_history'] = $this->booking_model->getbooking_history($booking_id);
        
        $bookng_unit_details = $this->booking_model->getunit_details($booking_id);
        $data['engineer'] = $this->engineer_model->getengineer_sign_table_data("*", array("booking_id" => $booking_id, "service_center_id" =>  
            $data['booking_history'][0]['assigned_vendor_id']));
        
        foreach($bookng_unit_details as $key1 => $b){
           $broken = 0;
            foreach ($b['quantity'] as $key2 => $u) {
                
                $unitWhere = array("engineer_booking_action.booking_id" => $booking_id, 
                    "engineer_booking_action.unit_details_id" => $u['unit_id'], "service_center_id" => $data['booking_history'][0]['assigned_vendor_id']);
                $en = $this->engineer_model->getengineer_action_data("engineer_booking_action.*", $unitWhere);
                if(!empty($en)){
                    $bookng_unit_details[$key1]['quantity'][$key2]['en_serial_number'] = $en[0]['serial_number'];
                    $bookng_unit_details[$key1]['quantity'][$key2]['en_serial_number_pic'] = $en[0]['serial_number_pic'];
                    $bookng_unit_details[$key1]['quantity'][$key2]['en_is_broken'] = $en[0]['is_broken'];
                    $bookng_unit_details[$key1]['quantity'][$key2]['en_internal_status'] = $en[0]['internal_status'];
                    if($en[0]['is_broken'] == 1){
                        $broken = 1;
                    }
                }
            }
            $bookng_unit_details[$key1]['is_broken'] = $broken;

        }
        
        $data['bookng_unit_details'] = $bookng_unit_details;
       
        //$this->load->view('service_centers/header');
        $this->load->view("service_centers/approve_booking", $data);
    } 
}
