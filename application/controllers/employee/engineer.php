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
    
    function review_engineer_action(){
      
        $this->load->view('service_centers/header');
        $where['where'] = array("engineer_booking_action.current_status" => "InProcess");
        $data = $this->engineer_model->get_engineer_action_table_list($where, "engineer_booking_action.booking_id, amount_due, engineer_table_sign.amount_paid");
        foreach ($data as $key => $value) {
            $unitWhere = array("engineer_booking_action.booking_id" => $value->booking_id);
            $data[$key]->lineItem = $this->engineer_model->get_engineer_action_table_list($unitWhere, "engineer_booking_action.*,"
                    . " booking_unit_details.appliance_brand,booking_unit_details.appliance_category,booking_unit_details.appliance_capacity, price_tags");
            
        }
        $this->load->view("service_centers/review_engineer_action", array("data" => $data));
      
    }
}
