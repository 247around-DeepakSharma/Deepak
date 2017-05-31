<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 36000000);

class InvoiceDashboard extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();

        $this->load->helper(array('form', 'url'));

        $this->load->model("invoices_model");
        $this->load->model("invoice_dashboard_model");
        $this->load->model("partner_model");
        $this->load->model("upcountry_model");


        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            echo PHP_EOL . 'Terminal Access Not Allowed' . PHP_EOL;
            redirect(base_url() . "employee/login");
        }
    }
    /**
     * @desc: this is used to load invoice dashboard view
     */
    function index(){
       //$data['partners'] = $this->partner_model->get_all_partner_source("0");
       $this->load->view('employee/header/'.$this->session->userdata('user_group'));
       $this->load->view('employee/invoice_dashboard');
    }
    /**
     * @desc This is used to get count completed  line item
     */
    function get_count_unit_details(){
        $date_range = $this->input->post("date_range");
        $explode_date_range = explode("-", $date_range);
        $data = $this->invoice_dashboard_model->get_count_unit_details($explode_date_range[0],$explode_date_range[1]);
        print_r(json_encode($data, TRUE));
    } 
    /**
     * @desc  get count by appliance and price tag
     * @param String $partner_id
     */
    function get_count_services($partner_id){
        $date_range = $this->input->post("date_range");
        $explode_date_range = explode("-", $date_range);

        $data = $this->invoice_dashboard_model->get_count_services($partner_id,$explode_date_range[0],$explode_date_range[1]);
        print_r(json_encode($data, TRUE));
    }
    /**
     * @desc: Main Invoice data
     * @param String $partner_id
     */
    function get_main_invoice($partner_id){
        $date_range = $this->input->post("date_range");
        $explode_date_range = explode("-", $date_range);
        $main_invoice = $this->invoices_model->generate_partner_invoice($partner_id, $explode_date_range[0],$explode_date_range[1], false);
        $data = $main_invoice['booking'];
        print_r(json_encode($data, TRUE));
    }
    /**
     * @desc: Duplicate entry in unit details
     * @param String $partner_id
     */
    function check_duplicate_completed_booking($partner_id){
        $date_range = $this->input->post("date_range");
        $explode_date_range = explode("-", $date_range);
       
        $data = $this->invoice_dashboard_model->check_duplicate_completed_booking($partner_id,$explode_date_range[0],$explode_date_range[1]);
        print_r(json_encode($data, TRUE));
        
    }
    /**
     * @desc: Wall Mount stand added but installation not added
     * @param String $partner_id
     */
    function installation_not_added($partner_id){
        $date_range = $this->input->post("date_range");
        $explode_date_range = explode("-", $date_range);
        $data = $this->invoice_dashboard_model->installation_not_added($partner_id, $explode_date_range[0],$explode_date_range[1]);
        print_r(json_encode($data, TRUE));
    }
    /**
     * @desc load Service Center invoice check page 
     */
    function service_center_invoice(){
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/sf_invoice_check');

    }
    /**
     * @desc This is used to get count completed  line item for SF
     */
    function get_completd_booking_for_sf(){
        $date_range = $this->input->post("date_range");
        $explode_date_range = explode("-", $date_range);
       
        $data = $this->invoice_dashboard_model->get_completd_booking_for_sf($explode_date_range[0],$explode_date_range[1]);
        print_r(json_encode($data, TRUE));
    }
    /**
     * @desc Vendor Baisc Charge not match
     */
    function get_mis_match_vendor_basic(){
        $date_range = $this->input->post("date_range");
        $explode_date_range = explode("-", $date_range);
        $data = $this->invoice_dashboard_model->get_mis_match_vendor_basic( $explode_date_range[0],$explode_date_range[1]);
        print_r(json_encode($data, TRUE));
    }
    /**
     * @desc Total customer paid is less than Due
     */
    function get_customer_paid_less_than_due(){
        $date_range = $this->input->post("date_range");
        $explode_date_range = explode("-", $date_range);
        $data = $this->invoice_dashboard_model->get_customer_paid_less_than_due($explode_date_range[0],$explode_date_range[1]);
        print_r(json_encode($data, TRUE));
    }
    /**
     * @desc SUM of Customer net payable, Around Net Payable, Partner Net Payable should not zero
     */
    function charges_total_should_not_zero(){
         $date_range = $this->input->post("date_range");
        $explode_date_range = explode("-", $date_range);
        $data = $this->invoice_dashboard_model->charges_total_should_not_zero( $explode_date_range[0],$explode_date_range[1]);
        print_r(json_encode($data, TRUE));
    }
    
    function around_to_vendor_to_around(){
        $date_range = $this->input->post("date_range");
        $explode_date_range = explode("-", $date_range);
        $data = $this->invoice_dashboard_model->around_to_vendor_to_around($explode_date_range[0],$explode_date_range[1]);
        print_r(json_encode($data, TRUE));
    }
}