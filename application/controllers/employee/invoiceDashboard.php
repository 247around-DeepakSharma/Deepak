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
        $this->load->library("miscelleneous");
        $this->load->library("invoice_lib");


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
       $this->miscelleneous->load_nav_header();
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
        $main_invoice = $this->invoices_model->generate_partner_invoice($partner_id, $explode_date_range[0],$explode_date_range[1]);
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
        $this->miscelleneous->load_nav_header();
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
    
    function get_customer_paid_basic_charge_less_than_cnp(){
        $date_range = $this->input->post("date_range");
        $explode_date_range = explode("-", $date_range);
        $data = $this->invoice_dashboard_model->get_customer_paid_basic_charge_less_than_customer_net_payable($explode_date_range[0],$explode_date_range[1]);
        print_r(json_encode($data, TRUE));
    }
    
    function around_to_vendor_to_around(){
        $date_range = $this->input->post("date_range");
        $explode_date_range = explode("-", $date_range);
        $data = $this->invoice_dashboard_model->around_to_vendor_to_around($explode_date_range[0],$explode_date_range[1]);
        print_r(json_encode($data, TRUE));
    }
    
    function upcountry_booking_check(){
        $date_range = $this->input->post("date_range");
        $explode_date_range = explode("-", $date_range);
        $data = $this->invoice_dashboard_model->get_upcountry_paid_less_than_expected($explode_date_range[0],$explode_date_range[1]);
        print_r(json_encode($data, TRUE));
    }
    function stand_not_added(){
        $date_range = $this->input->post("date_range");
        $explode_date_range = explode("-", $date_range);
        $data = $this->invoice_dashboard_model->get_stand_not_added($explode_date_range[0],$explode_date_range[1]);
        print_r(json_encode($data, TRUE));
    }
    function installation_not_added_sf(){
        $date_range = $this->input->post("date_range");
        $explode_date_range = explode("-", $date_range);
        $data = $this->invoice_dashboard_model->get_installation_not_added($explode_date_range[0],$explode_date_range[1]);
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

       $this->table->set_heading(array('SF name', 'CASH Commision Charge','GST Amount','<strong>Total Cash Charge</strong>', 
            'FOC Taxable Charge', 'FOC GST Amount', 'Total FOC Charge'));
        $select = "service_centres.name, service_centres.id, active";
        $vendor_details = $this->vendor_model->getVendorDetails($select);
        $total_cash_charge = 0;
        $total_foc_charge = 0;
        foreach ($vendor_details as $value) {
            
            $cash = $this->invoices_model->get_vendor_cash_invoice($value['id'], 
                    $explode_date_range[0], $explode_date_range[1], FALSE);
            
            
           
            $foc = $this->invoices_model->get_vendor_foc_invoice($value['id'], $explode_date_range[0], $explode_date_range[1], FALSE);
  
            $style="<span>";
            if($value['active'] == 0){
                $style = "<span style='color:red'> ";
            }
            if(!empty($cash) && !empty($foc)){
                $this->table->add_row($style.$value['name']."</span>", $cash['meta']['total_taxable_value'], 
                        ($cash['meta']['cgst_total_tax_amount'] + $cash['meta']['sgst_total_tax_amount'] + $cash['meta']['igst_total_tax_amount']),
                    "<strong>".round($cash['meta']['sub_total_amount'],0)."</strong>", $foc['meta']['total_taxable_value'], 
                    ($foc['meta']['cgst_total_tax_amount'] + $foc['meta']['sgst_total_tax_amount'] + $foc['meta']['igst_total_tax_amount']),
                        "<strong>".round($foc['meta']['sub_total_amount'],0)."</strong>");
                
                $total_cash_charge += $cash['meta']['sub_total_amount'];
                $total_foc_charge += $foc['meta']['sub_total_amount'];
                
            } else if(!empty($cash) && empty($foc)){
                $this->table->add_row($style.$value['name']."</span>", $cash['meta']['total_taxable_value'], 
                        ($cash['meta']['cgst_total_tax_amount'] + $cash['meta']['sgst_total_tax_amount'] + $cash['meta']['igst_total_tax_amount']),
                    "<strong>".round($cash['meta']['sub_total_amount'],0)."</strong>", "", 
                    "", "");
                
                $total_cash_charge += $cash['meta']['sub_total_amount'];
                
            } else if(empty($cash) && !empty($foc)){
                $this->table->add_row($style.$value['name']."</span>", "", 
                    "", "", round($foc['meta']['total_taxable_value'],0),
                    ($foc['meta']['cgst_total_tax_amount'] + $foc['meta']['sgst_total_tax_amount'] + $foc['meta']['igst_total_tax_amount']),
                        "<strong>".$foc['meta']['sub_total_amount']."</strong>");
                $total_foc_charge += $foc['meta']['sub_total_amount'];
                
            } else if(empty($cash) && empty($foc)){
               
                 $this->table->add_row($style.$value['name']."</span>", "", "","", "", "","");
            }  
        }
        
        $this->table->add_row("Total", "", "","<strong>".round($total_cash_charge,0)."<strong>", "", "","<strong>". $total_foc_charge."</strong>");
         
        $t_data['table_data'] = $this->table->generate();
        
        // Send SF Invoice Summary mail
        $email_template = $this->booking_model->get_booking_email_template(SF_INVOICE_SUMMARY);
        
        $email_from = $email_template[2];
        $to = $email_template[1];
        $cc = $email_template[3];
        $subject = vsprintf($email_template[4], array(date('d-m-Y', strtotime($explode_date_range[0])),date('d-m-Y', strtotime($explode_date_range[1]))));
        $message = vsprintf($email_template[0], array($t_data['table_data']));
        $this->notify->sendEmail($email_from, $to, $cc, "", $subject, $message, "", SF_INVOICE_SUMMARY);
//        $this->load->view('employee/sf_invoice_summary', $t_data);
        
    }
    /**
     * @desc This is uesd to load Invoice Summary form 
     */
    function get_invoice_summary_for_sf(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/sf_invoice_summary', array("date_range"=>1));
    }
    
    function get_invoice_summary_for_partner(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/partner_invoice_summary', array("date_range"=>1));
    }
    
    function process_invoice_summary_for_partner(){
        $date_range = $this->input->post("date_range");
        $explode_date_range = explode("-", $date_range);
        $template = array(
        'table_open' => '<table  '
            . ' class="table  table-striped table-bordered">'
        );

        $this->table->set_template($template);

        $this->table->set_heading(array('Name', 'Taxable Charge', 
            'GST Amount', 'Total'));
        $partner = $this->partner_model->get_all_partner_source();
        $total_basic = 0;$total_gst = 0; $sub_total=0;
        foreach ($partner as $value) {
            $invoices = $this->invoices_model->generate_partner_invoice($value['partner_id'], trim($explode_date_range[0]), trim($explode_date_range[1]));
            if(isset($invoices['meta']) && !empty($invoices['meta'])){
                $this->table->add_row($value['source'], $invoices['meta']['total_taxable_value'], 
                    ($invoices['meta']['igst_total_tax_amount'] + $invoices['meta']['sgst_total_tax_amount'] + $invoices['meta']['cgst_total_tax_amount']),
                    $invoices['meta']['sub_total_amount']);
                $total_basic += $invoices['meta']['total_taxable_value'];
                $total_gst += (($invoices['meta']['igst_total_tax_amount'] + $invoices['meta']['sgst_total_tax_amount'] + $invoices['meta']['cgst_total_tax_amount']));
                $sub_total += $invoices['meta']['sub_total_amount'];
            } else {
                $this->table->add_row($value['source'],"", "","");
            }
       }
       $this->table->add_row("Total","<strong>".$total_basic."<strong>", $total_gst,"<strong>". $sub_total."</strong>");
       echo $this->table->generate();
    }
    
    
   
}