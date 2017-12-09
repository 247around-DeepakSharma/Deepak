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

class Dashboard extends CI_Controller {

    /**
     * load modal, library and helper
     */
    function __Construct() {
        parent::__Construct();

        $this->load->helper(array('form', 'url'));
        $this->load->model('vendor_model');
        $this->load->model('reporting_utils');
        $this->load->model('employee_model');
        $this->load->model('invoices_model');
        $this->load->model('dashboard_model');
        $this->load->model('bb_model');
        $this->load->model('cp_model');
        $this->load->library("miscelleneous");

        $this->load->library('table');

        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            echo PHP_EOL . 'Terminal Access Not Allowed' . PHP_EOL;
            redirect(base_url() . "employee/login");
        }
    }

    /**
     * @desc: load dashboard view according to user
     * @param void
     * @return void
     */
    function index() {
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        
        if($this->session->userdata('user_group') == _247AROUND_ACCOUNTANT){
            redirect(base_url().'employee/invoice/invoice_partner_view');
        }else{
            $this->load->view("dashboard/".$this->session->userdata('user_group')."_dashboard");
        }
        
        $this->load->view('dashboard/dashboard_footer');
    }
    
    function execute_title_query(){
        if(($this->session->userdata('user_group') == _247AROUND_DEVELOPER)){
            $where = array('active' => 1,'type'=> 'service',"role like '%"._247AROUND_DEVELOPER."%'" => NULL);
        }else if($this->session->userdata('user_group') == _247AROUND_ADMIN){
            $where = array('active' => 1,'type'=> 'service',"role like '%"._247AROUND_ADMIN."%'" => NULL);
        }else if($this->session->userdata('user_group') == _247AROUND_CLOSURE){
            $where = array('active' => 1,'type'=> 'service',"role like '%"._247AROUND_CLOSURE."%'" => NULL);
        }else if($this->session->userdata('user_group') == _247AROUND_CALLCENTER){
            $where = array('active' => 1,'type'=> 'service',"role like '%"._247AROUND_CALLCENTER."%'" => NULL);
        }else{
            $where = array('active' => 1,'type'=> 'service');
        }
        $data_report['query'] = $this->vendor_model->get_around_dashboard_queries($where);
        $data_report['data'] = $this->vendor_model->execute_dashboard_query($data_report['query']);
        $this->load->view('dashboard/dashboard_title', $data_report);
    }
    
    function get_count_unit_details() {

        $sDate = $this->input->post('sDate');
        $eDate = $this->input->post('eDate');
        $current_status = "Completed";
        $startDate = date('Y-m-d 00:00:00', strtotime($sDate));
        $endDate = date('Y-m-d 23:59:59', strtotime($eDate));
        $data_report = $this->reporting_utils->get_partners_booking_unit_report_chart_data($startDate, $endDate, $current_status);
        $partner_category = array();
        $data = array();
        foreach ($data_report as  $value){
            array_push($partner_category, $value['public_name']);
            array_push($data, array("name" =>$value['public_name'], "y" =>intval($value['count'])) );
            
        }
        $array = array(
            'partner_category' => $partner_category,
            'data' => $data
        );
        echo json_encode($array);
    }

    /**
     * @desc: get paid or foc booking count on ajax call
     * case 1: when partner_id is null then show data for all partner
     * case 2: when partner is not null then show data according to partner_id
     * @param void
     * @return json
     */
    function get_paid_foc_count_ajax() {
        $sDate = $this->input->post('sDate');
        $eDate = $this->input->post('eDate');
        $current_status = $this->input->post('current_status');
        $partner_id = "";
        if ($this->input->post('partner_id')) {
            $partner_id = $this->input->post('partner_id');
        }
        $startDate = date('Y-m-d 00:00:00', strtotime($sDate));
        $endDate = date('Y-m-d 23:59:59', strtotime($eDate));
        $count = $this->dashboard_model->get_paid_foc_booking_count($startDate, $endDate, $current_status, $partner_id);
        $data['foc'] = $count[0]['FOC'];
        $data['paid'] = $count[0]['Paid'];
        echo json_encode($data);
    }
    
    /**
     * @desc: get paid or foc booking count on ajax call
     * @param void
     * @return json
     */
    function get_total_foc_or_paid_booking() {
        $sDate = $this->input->post('sDate');
        $eDate = $this->input->post('eDate');
        $type = $this->input->post('type');
        $current_status = $this->input->post('current_status');
        $startDate = date('Y-m-d 00:00:00', strtotime($sDate));
        $endDate = date('Y-m-d 23:59:59', strtotime($eDate));
        $data = $this->dashboard_model->get_total_foc_or_paid_booking($startDate, $endDate, $type, $current_status);
        $partner_name = [];
        $count = [];
        foreach ($data as $key => $value) {
            array_push($partner_name, $value['public_name']);
            array_push($count, $value['count']);
        }
        $json_data['partner_name'] = implode(",", $partner_name);
        $json_data['count'] = implode(",", $count);
        echo json_encode($json_data);
        //print_r($data);
    }
    
    /**
     * @desc: This functon is used to get booking group by appliance for perticular partners
     * @param void
     * @return json
     */
    function get_partner_booking_based_on_services() {
        $sDate = $this->input->post('sDate');
        $eDate = $this->input->post('eDate');
        $partner_id = $this->input->post('partner_id');
        $current_status = $this->input->post('current_status');
        $startDate = date('Y-m-d 00:00:00', strtotime($sDate));
        $endDate = date('Y-m-d 23:59:59', strtotime($eDate));
        $data = $this->dashboard_model->get_partner_booking_based_on_services($startDate, $endDate, $current_status, $partner_id);
        echo json_encode($data);
    }
    
    /**
     * @desc: This function is used to show partner specific dashboard
     * @param string
     * @return view
     */
    function partner_reports($partner_name = "", $partner_id = "", $current_status = "", $sDate = "", $eDate = "") {

        $data['partner_name'] = urldecode($partner_name);
        $data['partner_id'] = urldecode($partner_id);
        $startDate = date('Y-m-d 00:00:00', strtotime($sDate));
        $endDate = date('Y-m-d 23:59:59', strtotime($eDate));
        $data['booking'] = $this->dashboard_model->get_partner_bookings_data($startDate, $endDate, $partner_id);
        $count = $this->dashboard_model->get_paid_foc_booking_count($startDate, $endDate, '', $partner_id);
        $data['foc'] = $count[0]['FOC'];
        $data['paid'] = $count[0]['Paid'];
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('dashboard/partner_specific_dashboard', $data);
        $this->load->view('dashboard/dashboard_footer');
    }
    
    /**
     * @desc: This function is used to show partner specific booking data group by 
     * appliance
     * @param void
     * @return json
     */
    function get_partner_booking_ajax_data() {
        $sDate = $this->input->post('sDate');
        $eDate = $this->input->post('eDate');
        $partner_id = $this->input->post('partner_id');
        $startDate = date('Y-m-d 00:00:00', strtotime($sDate));
        $endDate = date('Y-m-d 23:59:59', strtotime($eDate));
        $bookings = $this->dashboard_model->get_partner_bookings_data($startDate, $endDate, $partner_id);
        $data['Completed'] = $bookings[0]['Completed'];
        $data['Cancelled'] = $bookings[0]['Cancelled'];
        $data['Pending'] = $bookings[0]['Pending'];
        $data['Rescheduled'] = $bookings[0]['Rescheduled'];
        $data['FollowUp'] = $bookings[0]['FollowUp'];

        echo json_encode($data);
    }
    
    /**
     * @desc: This function is used to get partner specific paid OR FOC data group by 
     * appliance
     * @param void
     * @return json
     */
    function get_paid_or_foc_booking_groupby_services() {
        $sDate = $this->input->post('sDate');
        $eDate = $this->input->post('eDate');
        $type = $this->input->post('type');
        $partner_id = $this->input->post('partner_id');
        $startDate = date('Y-m-d 00:00:00', strtotime($sDate));
        $endDate = date('Y-m-d 23:59:59', strtotime($eDate));
        $data = $this->dashboard_model->get_paid_or_foc_booking_groupby_services($startDate, $endDate, $type, '', $partner_id);
        $services = [];
        $count = [];
        foreach ($data as $key => $value) {
            array_push($services, $value['services']);
            array_push($count, $value['total']);
        }
        $json_data['services'] = implode(",", $services);
        $json_data['count'] = implode(",", $count);
        echo json_encode($json_data);
    }
    
    /**
     * @desc: This function is used to show rest of the div on page scroll by ajax
     * @param void
     * @return json
     */
    function get_data_onScroll($is_repeat_ajax = "") {
        if ($is_repeat_ajax) {
            $sDate = $this->input->post('sDate');
            $eDate = $this->input->post('eDate');
            $partner_id = "";
            if ($this->input->post('partner_id')) {
                $partner_id = $this->input->post('partner_id');
            }
            $startDate = date('Y-m-d 00:00:00', strtotime($sDate));
            $endDate = date('Y-m-d 23:59:59', strtotime($eDate));
            $data = $this->dashboard_model->get_data_onScroll($startDate, $endDate, $partner_id);
            if (!empty($data)) {
                echo json_encode($data);
            } else {
                echo "No Data Found";
            }
        } else {
            $timestamp = strtotime(date("Y-m-d"));
            $startDate = date('Y-m-01 00:00:00', $timestamp);
            $endDate = date('Y-m-d 23:59:59', $timestamp);
            $data = $this->dashboard_model->get_data_onScroll($startDate, $endDate);
            echo json_encode($data);
        }
    }
    
    /**
     * @desc: This function is used to show booking data based on current status and 
     * group by request_type
     * @param void
     * @return json
     */
    function get_bookings_data_by_request_type_current_status() {
        $sDate = $this->input->post('sDate');
        $eDate = $this->input->post('eDate');
        $request_type = $this->input->post('type');
        $partner_id = "";
        if ($this->input->post('partner_id')) {
            $partner_id = $this->input->post('partner_id');
        }
        $startDate = date('Y-m-d 00:00:00', strtotime($sDate));
        $endDate = date('Y-m-d 23:59:59', strtotime($eDate));
        $data = $this->dashboard_model->get_bookings_basedon_request_type_status($startDate, $endDate, $request_type, $partner_id);
        $current_status = [];
        $count = [];
        foreach ($data as $key => $value) {
            array_push($current_status, $value['current_status']);
            array_push($count, $value['total']);
        }
        $json_data['current_status'] = implode(",", $current_status);
        $json_data['count'] = implode(",", $count);
        echo json_encode($json_data);
    }
    
    /**
     * @desc: This function is used to get booking based on RM 
     * @param void
     * @return json
     */
    function get_booking_data_by_region($is_repeat_ajax = "") {
        if ($is_repeat_ajax) {
            $sDate = $this->input->post('sDate');
            $eDate = $this->input->post('eDate');
            $partner_id = "";
            if ($this->input->post('partner_id')) {
                $partner_id = $this->input->post('partner_id');
            }
            $startDate = date('Y-m-d 00:00:00', strtotime($sDate));
            $endDate = date('Y-m-d 23:59:59', strtotime($eDate));
            $this->make_rm_final_bookings_data($startDate, $endDate, $partner_id);
        } else {
            $timestamp = strtotime(date("Y-m-d"));
            $startDate = date('Y-m-01 00:00:00', $timestamp);
            $endDate = date('Y-m-d 23:59:59', $timestamp);
            $this->make_rm_final_bookings_data($startDate, $endDate);
        }
    }
    
    /**
     * @desc: This function is used to make json for booking based on rm
     * @param string
     * @return array
     */
    private function make_rm_final_bookings_data($startDate, $endDate, $partnerid = "") {
        $rm_array = $this->employee_model->get_rm_details();
        $rm = [];
        $cancelled = [];
        $completed = [];
        $pending = [];
        $total = [];
        if ($partnerid != "") {
            $partner_id = $partnerid;
        } else {
            $partner_id = "";
        }
        foreach ($rm_array as $value) {

            $sf_list = $this->vendor_model->get_employee_relation($value['id']);
            if (!empty($sf_list)) {
                $sf_id = $sf_list[0]['service_centres_id'];
                $region_data = $this->dashboard_model->get_booking_data_by_rm_region($startDate, $endDate, $sf_id, $partner_id);
                array_push($rm, $value['full_name']);
                foreach ($region_data[0] as $key => $value) {
                    switch ($key) {
                        case 'Cancelled':
                            if (!empty($value)) {
                                array_push($cancelled, $value);
                            } else {
                                array_push($cancelled, '0');
                            }
                            break;
                        case 'Completed':
                            if (!empty($value)) {
                                array_push($completed, $value);
                            } else {
                                array_push($completed, '0');
                            }
                            break;
                        case 'Pending':
                            if (!empty($value)) {
                                array_push($pending, $value);
                            } else {
                                array_push($pending, '0');
                            }
                            break;
                        case 'Total':
                            if (!empty($value)) {
                                array_push($total, $value);
                            } else {
                                array_push($total, '0');
                            }
                            break;
                    }
                }
            }
        }
        $json_data['rm'] = implode(",", $rm);
        $json_data['cancelled'] = implode(",", $cancelled);
        $json_data['completed'] = implode(",", $completed);
        $json_data['pending'] = implode(",", $pending);
        $json_data['total'] = implode(",", $total);
        echo json_encode($json_data);
    }

    /**
     * @desc: This function is used to get booking entered and scheduled data
     * @param string
     * @return json
     */
    function partners_booking_inflow($isajax, $startDate = "", $endDate = "") {
        $partner_name = [];
        $followUP = [];
        $pending = [];
        if ($isajax) {
            $sDate = $this->input->post('sDate');
            $eDate = $this->input->post('eDate');
            $startDate = date('Y-m-d 00:00:00', strtotime($sDate));
            $endDate = date('Y-m-d 23:59:59', strtotime($eDate));
            $inflow_data = $this->dashboard_model->get_partners_inflow_data($startDate, $endDate);
            foreach ($inflow_data as $value) {
                array_push($partner_name, $value['partner_name']);
                array_push($followUP, $value['booking_entered']);
                array_push($pending, $value['booking_pending']);
            }
            $json_data['partner_name'] = implode(",", $partner_name);
            $json_data['followup'] = implode(",", $followUP);
            $json_data['pending'] = implode(",", $pending);
            echo json_encode($json_data);
        } else {
            $inflow_data = $this->dashboard_model->get_partners_inflow_data($startDate, $endDate);
            foreach ($inflow_data as $value) {
                array_push($partner_name, $value['partner_name']);
                array_push($followUP, $value['booking_entered']);
                array_push($pending, $value['booking_pending']);
            }
            $json_data['partner_name'] = json_encode($partner_name);
            $json_data['followup'] = implode(",", $followUP);
            $json_data['pending'] = implode(",", $pending);
            return $json_data;
        }
    }
    
    /**
     * @desc: This function is used to get partner completed booking data 
     * based on month
     * @param string
     * @return array
     */
    function get_bookings_data_by_month(){
        $partner_id = $this->input->post('partner_id');
        $data = $this->dashboard_model->get_bookings_data_by_month($partner_id);
        $month = [];
        $year = [];
        $completed_booking = [];
        foreach ($data as $key => $value){
            $temp_str = $value['month']."(".$value['year'].")";
            array_push($month, $temp_str);
            array_push($year, $value['year']);
            array_push($completed_booking, $value['completed_booking']);
        }
        array_shift($month);
        array_shift($completed_booking);
        $json_data['month'] = implode(",", $month);
        $json_data['completed_booking'] = implode(",", $completed_booking);
        echo json_encode($json_data);
    }
    
    function get_booking_based_on_partner_source() {
        $sDate = $this->input->post('sDate');
        $eDate = $this->input->post('eDate');
        $partner_id = "";
        if ($this->input->post('partner_id')) {
            $partner_id = $this->input->post('partner_id');
        }
        $startDate = date('Y-m-d 00:00:00', strtotime($sDate));
        $endDate = date('Y-m-d 23:59:59', strtotime($eDate));
        $data = $this->dashboard_model->get_booking_based_on_partner_source_data($startDate, $endDate, $partner_id);
        
        $count = [];
        $booking = [];

        foreach ($data as $key => $value) {
            array_push($count, $value['count']);
            array_push($booking, $value['partner_source']);
        }

        $json_data['count'] = implode(",", $count);
        $json_data['partner_source_booking'] = implode(",", $booking);

        echo json_encode($json_data);
        
    }
    
    function buyback_dashboard(){
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('dashboard/buyback_dashboard');
        $this->load->view('dashboard/dashboard_footer');
    }
    
    function get_buyback_balanced_amount(){
        
        if($this->session->userdata('user_group') === 'regionalmanager'){
            $sf_id = $this->vendor_model->get_employee_relation($this->session->userdata('id'));
            if(!empty($sf_id)){
                $sf_id = $sf_id[0]['service_centres_id'];
                $cp = $this->vendor_model->getVendorDetails("id, name", array('is_cp' => 1, "id IN ($sf_id)" => null));
            }else{
                $cp = '';
            }
            
        }else{
            $cp = $this->vendor_model->getVendorDetails("id, name", array('is_cp' => 1));
        }
        
        $total_advance_paid = 0;
        $total_un_settle = 0;
        $total_un_billed_delivered = 0;
        $total_un_billed_in_transit= 0;
        $total_balance= 0;
       
        $template = array(
        'table_open' => '<table  '
            . ' class="table table-striped table-bordered jambo_table bulk_action">'
        );

        $this->table->set_template($template);
        $this->table->set_heading(array('Name', 'Advance Paid', 'Un-Settle Invoice (Rs)', 'Un-billed Delivered (Rs)', 'Un-billed In-transit (Rs)', 'Balance (Rs)', "Login"));

        if(!empty($cp)){
            foreach ($cp as  $value) {
                $amount_cr_deb = $this->miscelleneous->get_cp_buyback_credit_debit($value['id']);

                 $shop_data = $this->cp_model->get_cp_shop_address_list(array('where' => array('cp_id' => $value['id'])));
                 $star = "";
                 $name = "";
                 $name  = "<div class='dropdown'>
                                 <a class='dropdown-toggle' data-toggle='dropdown' data-hover='dropdown' style='cursor: pointer;'>".$value['name']."
                                </a>
                                 <ul class='dropdown-menu' >
                                 <li role='presentation'><a  href='#' style='font-weight:bold; font-size:16px;'>Region</a></li>

                                 ";

                 foreach ($shop_data as $shop){
                     if($shop->active == 1){
                         $star .= '    <i class="fa fa-star" style="color:green;" aria-hidden="true"></i>';
                         $name .= " <li role='presentation'><a href='#' style='color:green;font-size:16px;'>".$shop->shop_address_region."</a></li>";
                     } else{

                        $star .= '    <i class="fa fa-star" style="color:red;" aria-hidden="true"></i>';
                        $name .= " <li role='presentation'><a href='#' style='color:red;font-size:16px;'>".$shop->shop_address_region."</a></li>";

                     }

                 }
                 $name .= "</ul></div>";

                 $class ="";

                 if($amount_cr_deb['total_balance'] > 0){
                     $class = ' <i class="success pull-right fa fa-caret-up fa-2x text-success"></i>';
                 } else if($amount_cr_deb['total_balance'] < 0){
                    $class = ' <i class="error pull-right fa fa-caret-down fa-2x text-danger"></i>'; 
                 }

                 $total_advance_paid += abs($amount_cr_deb['advance']);
                 $total_un_settle += $amount_cr_deb['unbilled'];
                 $total_un_billed_delivered += $amount_cr_deb['cp_delivered'];
                 $total_un_billed_in_transit += $amount_cr_deb['cp_transit'];
                 $total_balance += $amount_cr_deb['total_balance'];
                 $login_button = '<a href="javascript:void(0)" style="background: #4b5056;border:1px solid #4b5056" '
                         . 'class="btn btn-md btn-success" onclick="return login_to_vendor('.$value['id'].')" ">Login</a>';
                 $this->table->add_row($name .$star,abs($amount_cr_deb['advance']),-$amount_cr_deb['unbilled'], 
                         -$amount_cr_deb['cp_delivered'],-$amount_cr_deb['cp_transit'], 
                         "<a target='_blank' href='".  base_url()."employee/invoice/invoice_summary/vendor/".$value['id']."'>".
                         $amount_cr_deb['total_balance'].$class. "</a>", $login_button);

             }
        }
        
        $this->table->add_row("<b>Total</b>",
                "<b>".$total_advance_paid."</b>",
                "<b>".$total_un_settle."</b>",
                "<b>".$total_un_billed_delivered."</b>",
                "<b>".$total_un_billed_in_transit,
                "<b>".$total_balance."</b>", "");
        echo $this->table->generate();
    }
    
    
    /**
     * @desc: This function is used to get partner completed and cancelled bookings data 
     * @param string
     * @return array
     */
    function get_partners_booking_report_chart(){
        $sDate = $this->input->post('sDate');
        $eDate = $this->input->post('eDate');
        $startDate = date('Y-m-d 00:00:00', strtotime($sDate));
        $endDate = date('Y-m-d 23:59:59', strtotime($eDate));
        $data = $this->dashboard_model->get_partners_booking_data($startDate,$endDate);
        $partners_id = [];
        $partners_name = [];
        $completed_bookings = [];
        $cancelled_bookings = [];
        foreach( $data as $bookings){
            $partners_id[$bookings['public_name']] = $bookings['partner_id'];
            array_push($partners_name, $bookings['public_name']);
            array_push($completed_bookings, $bookings['Completed']);
            array_push($cancelled_bookings, $bookings['Cancelled']);
        }
        $json_data['partner_id'] = $partners_id;
        $json_data['partner_name'] = implode(",", $partners_name);
        $json_data['completed_bookings_count'] = implode(",", $completed_bookings);
        $json_data['cancelled_bookings_count'] = implode(",", $cancelled_bookings);

        echo json_encode($json_data);
    }
    
    /**
     * @desc: This function is used to get partner completed booking unit data 
     * based on month
     * @param void
     * @return json
     */
    function get_bookings_unit_data_by_month(){
        $partner_id = $this->input->post('partner_id');
        $data = $this->dashboard_model->get_bookings_unit_data_by_month($partner_id);
        $month = [];
        $year = [];
        $completed_booking = [];
        foreach ($data as $key => $value){
            $temp_str = $value['month']."(".$value['year'].")";
            array_push($month, $temp_str);
            array_push($year, $value['year']);
            array_push($completed_booking, $value['completed_booking']);
        }
        array_shift($month);
        array_shift($completed_booking);
        $json_data['month'] = implode(",", $month);
        $json_data['completed_booking'] = implode(",", $completed_booking);
        echo json_encode($json_data);
    }
    /*
     * This is a helper function to create missing pincodes view for rm
     * This function use to create a hidden form which will be submit when rm will click on action button
     * This function will send required data through post to the add pincode to sf form
     */
    function get_pincode_form(){
        $form = "<form method='post' action=".base_url()."employee/vendor/insert_pincode_form id='pincodeForm' target='_blank'>";
        $form .=  "<input type='hidden' value='' name='pincode' id='pincode'>";
        $form .=  "<input type='hidden' value='' name='city' id='city'>";
        $form .=  "<input type='hidden' value='' name='state' id='state'>";
        $form .=  "<input type='hidden' value='' name='service' id='service'>";
        $form .=  "</form>";
        return $form;
    }
    /*
     * This is a helper function to create missing pincodes view for rm
     * This Function contains the html for detailed view 
     * Dynamically Javascript will change the number for this view
     */
    function get_missing_pincode_detailed_view(){
        ?>
        <div id="missingPincodeDetails" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Missing Pincodes Detailed View</h4>
      </div>
      <div class="modal-body">
          <table class="table table-bordered" id="mssingPincodeTable">
    <thead>
      <tr>
        <th>Pincode</th>
        <th>City</th>
        <th>State</th>
        <th>Service</th>
        <th>PincodeCount</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
          </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
     <?php   
    }
    /*
     * This is a helper function to create missing pincodes view for rm
     * This function convert data (which we get directly from db) into a structured format
     */
    function get_missing_pincode_data_structured_format($pincodeResult){
          $structuredArray = array();
          foreach($pincodeResult as $key=>$data){   
                    if(array_key_exists($data['pincode'], $structuredArray)){
                              $structuredArray[$data['pincode']]['totalCount'] = $structuredArray[$data['pincode']]['totalCount']+$data['pincodeCount'];
                    }
                    else{
                        
                              $structuredArray[$data['pincode']]['totalCount'] = $data['pincodeCount'];
                    }   
                    $structuredArray[$data['pincode']]['pincode'] = $data['pincode'];
                    $structuredArray[$data['pincode']]['city'] = $data['city'];
                    $structuredArray[$data['pincode']]['state'] = $data['state'];
                    $structuredArray[$data['pincode']]['rm'] = $data['full_name'];
                    $temp['service_id'] = $data['service_id'];
                    $temp['pincodeCount'] = $data['pincodeCount'];
                    $temp['service_name'] = $data['services'];
                    $structuredArray[$data['pincode']]['service'][] = $temp;
          }
            return $structuredArray;
    }
    /*
     * This function use to create the missing pincode RM view
     * @input Limit - Number which defines how many records will get returned
     * @output - Table View for missing pincodes 
     */
    function get_pincode_not_found_sf_details($limit=NULL){
        if($this->session->userdata('rm_id')){
            $agentID = $this->session->userdata('rm_id');
            $this->session->unset_userdata('rm_id');
        }
        else{
                $agentID = $this->session->userdata('id');
        }
        $pincodeResult =  $this->dashboard_model->get_pincode_data_for_not_found_sf($agentID,NULL);
        $template = array(
        'table_open' => '<table  '
            . ' class="table table-striped table-bordered jambo_table bulk_action">'
        );
        $this->table->set_template($template);
        $this->table->set_heading(array('S.N','Pincode','State','City', 'Pending Bookings','action'));
        echo $this->get_pincode_form();
        $this->get_missing_pincode_detailed_view();
        $structuredPincodeArray = $this->get_missing_pincode_data_structured_format($pincodeResult);
        $i=1;
        foreach($structuredPincodeArray as $pincode=>$structuredData){ 
          if($limit){
              if($i>$limit){
                  break;
              }
          }
                   $this->table->add_row($i,$pincode,$structuredData['state'],$structuredData['city'],"<button onclick='missingPincodeDetailedView(".json_encode($structuredData).")' style='margin: 0px;padding: 0px 6px;' type='button' class='btn btn-info btn-lg' data-toggle='modal' data-target='#missingPincodeDetails'>".$structuredData['totalCount']."</button>","<button style='margin: 0px;padding: 6px;' class='btn btn-info ' onclick='submitPincodeForm(".json_encode($structuredData).")'>Add Service Center</button>"); 
                   $i++;
        }
        echo $this->table->generate();
    }
    /*
     * This function use to create a dashboard for RM
     */
    function rm_dashboard(){
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('dashboard/rm_dashboard');
        $this->load->view('dashboard/dashboard_footer');
    }
    /*
     * This function use to create full view of missing pincode table
     */
    function missing_pincode_full_view(){
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('dashboard/missing_pincodes_full_view');
        $this->load->view('dashboard/dashboard_footer');
    }
    /*
     * This is a helper function for admin missing pincode view
     * Pass Group by rm query data, it will return a associatve array in which rm will be key
     */
    function get_missing_pincode_admin_data_structured_format($pincodeResult){
        $rmDataArray = array();
        foreach($pincodeResult as $data){
            if(array_key_exists($data['full_name'], $rmDataArray)){
                     $rmDataArray[$data['full_name']]['count'] = $rmDataArray[$data['full_name']]['count']+$data['pincodeCount'];
            }
            else{
                   $rmDataArray[$data['full_name']]['count'] = $data['pincodeCount'];
                   $rmDataArray[$data['full_name']]['id'] = $data['rm_id'];
            }
        }
        arsort($rmDataArray);
        return $rmDataArray;
    }
    /*
     * This function use to create sf_not_found  view for admin
     * It will shows missing pincode pending queries group by rm 
     */
    function get_pincode_not_found_sf_details_admin(){
        $pincodeResult =  $this->dashboard_model->get_pincode_data_for_not_found_sf();
        $template = array(
        'table_open' => '<table  '
            . ' class="table table-striped table-bordered jambo_table bulk_action">'
        );
        $this->table->set_template($template);
        $this->table->set_heading(array('S.N','RM', 'Pending Queries'));
        $rmDataArray = $this->get_missing_pincode_admin_data_structured_format($pincodeResult);
        $i=1;
        foreach($rmDataArray as $rm=>$rmData){
            if($rmData['id']==''){
                $rmData['id'] = -1;
            }
                  $this->table->add_row($i,"<a target='_blank' href=".base_url()."employee/dashboard/missing_pincode_full_view?rm_id=".$rmData['id']." style='margin: 0px;padding: 6px;' class='btn btn-info'>".$rm."</a>",$rmData['count']); 
                   $i++;
        }
        echo $this->table->generate();
    }
    /*
    * This function willl download the missing pincode data on the basis of rm
     * @input - rm_id
     * @output - Excel
     */
    function download_missing_sf_pincode_excel($rmID){
        ob_start();
        $pincodeArray =  $this->dashboard_model->get_pincode_data_for_not_found_sf($rmID);
        $config = array('template' => "missing_sf_pincode.xlsx", 'templateDir' => __DIR__ . "/../excel-templates/");
        $this->miscelleneous->downloadExcel($pincodeArray,$config);
    }
    
    /**
     * @desc: This is used to call from cron to populate invoice check table
     */
    function getinvoice_checkdata() {
        $data = $this->vendor_model->get_around_dashboard_queries(array('role' => 'developer', 'type' => 'invoice_check'));
        if (!empty($data)) {
            if (!empty($data[0]['result'])) {
                $d = json_decode($data[0]['result'], true);
                if (!empty($d)) {
                    $this->load->view("dashboard/invoice_check_table", array('data' =>$d));
                } else {
                    echo "Data Not Found";
                }
            } else {
                echo "Data Not Found";
            }
        } else {
            echo "Data Not Found";
        }
    }
function get_sf_escalation_by_rm($rm_id,$startDate=NULL,$endDate=NULL){
    $groupBy['booking'] = array("employee_relation.agent_id","booking_details.assigned_vendor_id");
    $groupBy['escalation'] = array("employee_relation.agent_id","vendor_escalation_log.vendor_id");
    $escalationBookingData = $this->dashboard_model->get_sf_escalation_by_rm_by_sf_by_date($startDate,$endDate,NULL,$rm_id,$groupBy);
    $sfArray = $this->reusable_model->get_search_result_data("service_centres","id,name",NULL,NULL,NULL,NULL,NULL,NULL,NULL);
    foreach($sfArray as $sfData){
        $sfIDNameArray["vendor_".$sfData['id']]= $sfData['name'];
    }
    foreach($escalationBookingData['booking'] as $bookingData){
        if($bookingData['assigned_vendor_id'] !=''){
            $SfBookingArray["vendor_".$bookingData['assigned_vendor_id']] = $bookingData['total_booking'];
        }
    }
    foreach($escalationBookingData['escalation'] as $escalationData){
        if($escalationData['vendor_id'] !=0 ){
           $tempArray= array("esclation_per"=>round((($escalationData['total_escalation']*100)/$SfBookingArray["vendor_".$escalationData['vendor_id']]),2),"vendor_id"=>$escalationData['vendor_id'],
               "total_booking"=>$SfBookingArray["vendor_".$escalationData['vendor_id']],"total_escalation"=>$escalationData['total_escalation'],"vendor_name"=>$sfIDNameArray["vendor_".$escalationData['vendor_id']]);
           $esclationPercentage[]=$tempArray;
       }
    }
    echo json_encode($esclationPercentage);
}
function get_escalation_data($sfID,$startDate=NULL,$endDate=NULL){
    $escalation_where["vendor_escalation_log.vendor_id"] =$sfID;
    if(!($startDate) && !($endDate)){
            $escalation_where["month(vendor_escalation_log.create_date) = month(now()) AND year(vendor_escalation_log.create_date) = year(now())"] =NULL;
       }
       else{
            $escalation_where["date(vendor_escalation_log.create_date) >= '".$startDate."' AND date(vendor_escalation_log.create_date) < '".$endDate."'"] =  NULL;
       }
    $data = $this->reusable_model->get_search_result_data('vendor_escalation_log',"count(DISTINCT booking_details.booking_id) AS total_booking,count(vendor_escalation_log.booking_id) "
            . "AS total_escalation,booking_details.assigned_vendor_id,	services.services,service_centres.name,booking_details.is_upcountry,booking_details.request_type",$escalation_where,
                    array("booking_details"=>"vendor_escalation_log.booking_id=booking_details.booking_id","services"=>"services.id=booking_details.service_id"
                        ,"service_centres"=>"service_centres.id=booking_details.assigned_vendor_id"),
                    NULL,array("total_escalation"=>"DESC"),NULL,
                    NULL,array("booking_details.service_id","booking_details.is_upcountry","booking_details.request_type"));
    return $data;
}
function get_escalation_chart_data_by_one_matrix($data,$key){
    $applianceEscalationData = array();
    foreach($data as $escalationData){
        if(array_key_exists($escalationData[$key], $applianceEscalationData)){
            $applianceEscalationData[$escalationData[$key]] = $escalationData['total_escalation']+$applianceEscalationData[$escalationData[$key]];
        }
        else{
             $applianceEscalationData[$escalationData[$key]] = $escalationData['total_escalation'];
        }
    }
    return $applianceEscalationData;
}
function get_escalation_chart_data_by_two_matrix($data,$baseKey,$otherKey){
    $resultArray= array();
    foreach ($data as $escalationData){
        if(array_key_exists($escalationData[$baseKey], $resultArray)){
            if(array_key_exists($escalationData[$otherKey], $resultArray[$escalationData[$baseKey]])){
                $resultArray[$escalationData[$baseKey]][$escalationData[$otherKey]] = $resultArray[$escalationData[$baseKey]][$escalationData[$otherKey]]+$escalationData['total_booking'];
            }
            else{
                $resultArray[$escalationData[$baseKey]][$escalationData[$otherKey]] = $escalationData['total_booking'];
            }
        }
        else{
            $resultArray[$escalationData[$baseKey]][$escalationData[$otherKey]] = $escalationData['total_booking'];
        }
    }
    return $resultArray;
}
 function get_escalations_chart_data($sfID,$startDate=NULL,$endDate=NULL){    
        $requestTypeNewArray['Installation'] = $requestTypeNewArray['Repair'] = 0;
        $data = $this->get_escalation_data($sfID,$startDate,$endDate);
        // get escalation by upcountry
        $upcountryData= $this->get_escalation_chart_data_by_one_matrix($data,"is_upcountry");
        if(array_key_exists("1", $upcountryData)){
            $finalData['upcountry']['upcountry'] = $upcountryData[1];
        }
         if(array_key_exists("0", $upcountryData)){
            $finalData['upcountry']['non_upcountry'] = $upcountryData[0];
         }
        // get escalation by appliance
        $finalData['appliance'] = $this->get_escalation_chart_data_by_one_matrix($data,"services");
        // get escalation by request type
        $requestTypeData = $this->get_escalation_chart_data_by_one_matrix($data,"request_type");
        // convert all request type into installation and Repair
        foreach($requestTypeData as $requestName => $esclation){
            if (strpos($requestName, 'repair') !== false) {
                $requestTypeNewArray['Repair'] = $esclation+$requestTypeNewArray['Repair'];
            }
            else{
                 $requestTypeNewArray['Installation'] = $esclation+$requestTypeNewArray['Installation'];
            }
        }
        $finalData['request_type'] = $requestTypeNewArray;
        $applianceUpcountryArray = $this->get_escalation_chart_data_by_two_matrix($data,"services","is_upcountry");
        foreach($applianceUpcountryArray as $key=>$value){
            if(array_key_exists("1", $value)){
            $finalData['service_upcountry'][$key][] = array("Upcountry",$value[1]);
        }
         if(array_key_exists("0", $value)){
            $finalData['service_upcountry'][$key][] = array("Non_Upcountry",$value[0]);
         }
        }
        echo json_encode($finalData);
    }
    function get_sf_performance_bar_chart_data($sf){
    //Current Date    
    $monthlyData = array();
    $endDate=date('Y-m-d');
    $startMonth = date("m")+1;
    $lastYear = date("Y")-1;
    if($startMonth==13){
        $startMonth=1;
        $lastYear=date('Y-m-d');
    }
    $startDateTemp = strtotime($startMonth.'/01/'.$lastYear);
    // End Date will be - "1st Day of current Month before a year ago"
    $startDate = date('Y-m-d',$startDateTemp);
    $groupBy['booking'] = array("MONTHNAME(STR_TO_DATE(booking_details.booking_date,'%d-%m-%Y'))");
    $groupBy['escalation'] = array("MONTHNAME(vendor_escalation_log.create_date)");
    $data = $this->dashboard_model->get_sf_escalation_by_rm_by_sf_by_date($startDate,$endDate,$sf,NULL,$groupBy);
    foreach($data['escalation'] as $escalationData){
        $escalationAssociativeArray[$escalationData['escalation_month']]= $escalationData['total_escalation'];
    }
    foreach($data['booking'] as $bookingData){
        if(array_key_exists($bookingData['booking_month'], $escalationAssociativeArray)){
            $escalation = $escalationAssociativeArray[$bookingData['booking_month']]; 
        }
        else{
            $escalation = 0; 
        }
        $monthlyData['bookings'][] = $bookingData['total_booking'];
        $monthlyData['escalation'][] = $escalation; 
        $monthlyData['escalationPercentage'][] = round((($escalation*100)/$bookingData['total_booking']),2);
        $monthlyData['months'][] = $bookingData['booking_month'];
    }
    echo json_encode($monthlyData);
    }
    function escalation_full_view(){
        $data['full_view_data'] = json_decode($this->input->post("sf_json_data"),true);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('dashboard/escalation_full_view',$data);
        $this->load->view('dashboard/dashboard_footer');
    }
//        function pending_booking_by_rm($rmID){
//        $serviceCentersIDArray= $this->vendor_model->get_employee_relation($rmID);
//        if(!empty($serviceCentersIDArray)){
//            $serviceCentersIDList = $serviceCentersIDArray[0]['service_centres_id'];
//            $sfArray = $this->reusable_model->get_search_result_data("service_centres","id,name",NULL,NULL,NULL,NULL,array("id"=>explode(",",$serviceCentersIDList)),NULL,NULL);
//            foreach($sfArray as $sfData){
//                $sfIDNameArray["vendor_".$sfData['id']]= $sfData['name'];
//            }
//        }
//        $data = $this->reporting_utils->get_booking_by_service_center($serviceCentersIDList); 
//        foreach($data['data'] as $sfID=>$sfData){
//            $final_data['monthly_completed'] = 0;
//            $final_data['month_cancelled'] =0;
//            $final_data['last_2_day'] =0;
//            $final_data['last_3_day'] =0;
//            $final_data['greater_than_5_days'] =0;
//            $final_data['id'] =$sfID;
//            $final_data['name'] =$sfIDNameArray["vendor_".$sfID];
//            if(array_key_exists('month_completed', $sfData)){
//                $final_data['monthly_completed'] = $sfData['month_completed']['completed'];
//            }
//            if(array_key_exists('month_cancelled', $sfData)){
//                $final_data['month_cancelled'] = $sfData['month_cancelled']['cancelled'];
//            }
//            if(array_key_exists('last_2_day', $sfData)){
//                $final_data['last_2_day'] = $sfData['last_2_day']['booked'];
//            }
//            if(array_key_exists('last_3_day', $sfData)){
//                $final_data['last_3_day'] = $sfData['last_3_day']['booked'];
//            }
//             if(array_key_exists('greater_than_5_days', $sfData)){
//                $final_data['greater_than_5_days'] = $sfData['greater_than_5_days']['booked'];
//             }
//             $ServiceCenterBookingData[]= $final_data;
//        }
//        echo json_encode($ServiceCenterBookingData);
//    }
}
