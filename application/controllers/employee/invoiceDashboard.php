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
        $this->load->model("vendor_model");
        $this->load->model("booking_model");
        $this->load->model("penalty_model");
        $this->load->library('table');


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
    /**
     * @desc This is used to load all sf invoice summary Data
     */
    function process_invoice_summary_for_sf(){
        $date_range = $this->input->post("date_range"); 
        $explode_date_range = explode("-", $date_range);
        $template = array(
        'table_open' => '<table  '
            . ' class="table  table-striped table-bordered">'
        );

        $this->table->set_template($template);

       $this->table->set_heading(array('SF name', 'CASH Inst. Charge', 
            'CASH Add. Charge', 'CASH Misc Charge', '<strong>Total Cash Charge</strong>', 
            'FOC Inst. Charge', 'FOC Parts Charge', 'FOC Misc Charge', 'FOC Total Charge'));
        
        $vendor_details = $this->vendor_model->getActiveVendor('', 0);
        $total_cash_charge = 0;
        $total_foc_charge = 0;
        foreach ($vendor_details as $key => $value) {
            
            $cash = $this->invoices_model->get_vendor_cash_invoice($value['id'], 
                    $explode_date_range[0], $explode_date_range[1], FALSE);
           
            $foc = $this->invoices_model->get_vendor_foc_invoice($value['id'], $explode_date_range[0], $explode_date_range[1], FALSE);
           
            if(!empty($cash) && !empty($foc)){
                $this->table->add_row($value['name'], $cash['meta']['total_installation_charge'], 
                    $cash['meta']['additional_charges'], $cash['meta']['misc_charge'],
                    $cash['meta']['total_charge'], $foc['meta']['sub_service_cost'], 
                    $foc['meta']['sub_part'], $foc['meta']['total_misc_price'], $foc['meta']['grand_total_price']);
                
                $total_cash_charge += $cash['meta']['total_charge'];
                $total_foc_charge += $foc['meta']['grand_total_price'];
                
            } else if(!empty($cash) && empty($foc)){
                $this->table->add_row($value['name'], $cash['meta']['total_installation_charge'], 
                    $cash['meta']['additional_charges'], $cash['meta']['misc_charge'],
                    $cash['meta']['total_charge'], "", 
                    "", "", "");
                
                $total_cash_charge += $cash['meta']['total_charge'];
                
            } else if(empty($cash) && !empty($foc)){
                $this->table->add_row($value['name'], "", 
                    "", "","", $foc['meta']['sub_service_cost'], 
                    $foc['meta']['sub_part'], $foc['meta']['total_misc_price'], $foc['meta']['grand_total_price']);
                $total_foc_charge += $foc['meta']['grand_total_price'];
                
            } else if(empty($cash) && empty($foc)){
                 $this->table->add_row($value['name'], "", 
                    "", "","", "", "", "", "");
            }  
           
        }
        
        $this->table->add_row("Total", "", "", "","<strong>".$total_cash_charge."<strong>", "", "", "","<strong>". $total_foc_charge."</strong>");
         
        $t_data['table_data'] = $this->table->generate();
       
        $this->load->view('employee/sf_invoice_summary', $t_data);
        
    }
    /**
     * @desc This is uesd to load Invoice Summary form 
     */
    function get_invoice_summary_for_sf(){
        $this->load->view('employee/header/' . $this->session->userdata('user_group'));
        $this->load->view('employee/sf_invoice_summary', array("date_range"=>1));
    }
    
    
    
   
}