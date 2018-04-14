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
  var  $pincodeResult = array();
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
        $this->load->view('employee/header/push_notification');
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
        }else if($this->session->userdata('user_group') == _247AROUND_RM){
            $where = array('active' => 1,'type'=> 'service',"role like '%"._247AROUND_RM."%'" => NULL);
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
                 $this->table->add_row($name .$star,round(abs($amount_cr_deb['advance']),0),-round($amount_cr_deb['unbilled'],0), 
                         -round($amount_cr_deb['cp_delivered'],0),-round($amount_cr_deb['cp_transit'],0), 
                         "<a target='_blank' href='".  base_url()."employee/invoice/invoice_summary/vendor/".$value['id']."'>".
                        round($amount_cr_deb['total_balance'],0).$class. "</a>", $login_button);

             }
        }
        
        $this->table->add_row("<b>Total</b>",
                "<b>".round($total_advance_paid,0)."</b>",
                "<b>".round($total_un_settle,0)."</b>",
                "<b>".round($total_un_billed_delivered,0)."</b>",
                "<b>".round($total_un_billed_in_transit,0),
                "<b>".round($total_balance,0)."</b>", "");
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
    function missing_pincode_full_view($agentID){
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        //SELECT sf.pincode, COUNT(sf.pincode) as pincodeCount, sf.state, sf.city FROM (sf_not_exist_booking_details sf) WHERE `sf`.`rm_id` = '11' AND `sf`.`active_flag` = 1 
        //AND `sf`.`is_pincode_valid` = 1 GROUP BY sf.pincode ORDER BY pincodeCount DESC
        $select = "sf.pincode,COUNT(sf.pincode) as pincodeCount,sf.state,sf.city,sf.service_id,services.services";
        $where['sf.rm_id'] = $agentID;
        $where['sf.active_flag'] = 1;
        $where['sf.is_pincode_valid'] = 1;
        $orderBYArray['pincodeCount'] = 'DESC';
        $groupBY = array('sf.pincode','sf.service_id');
        $join['services']  = 'sf.service_id=services.id';
        $JoinTypeTableArray['services'] = 'left';
        $tempPincode = $this->reusable_model->get_search_result_data("sf_not_exist_booking_details sf",$select,$where,$join,NULL,$orderBYArray,NULL,$JoinTypeTableArray,$groupBY);
        $finalPincodeArray = array();
        foreach($tempPincode as $pincodes){
            if(array_key_exists($pincodes['pincode'], $finalPincodeArray)){
                $finalPincodeArray[$pincodes['pincode']]['count'] = $finalPincodeArray[$pincodes['pincode']]['count']+$pincodes['pincodeCount'];
                $finalPincodeArray[$pincodes['pincode']]['state'] = $pincodes['state'];
                $finalPincodeArray[$pincodes['pincode']]['service_id'][] = $pincodes['service_id'];
                $finalPincodeArray[$pincodes['pincode']]['services'][] = $pincodes['services'];
                $finalPincodeArray[$pincodes['pincode']]['city'] = $pincodes['city'];
                $finalPincodeArray[$pincodes['pincode']]['services_count'][] = $pincodes['pincodeCount'];
            }
            else{
                $finalPincodeArray[$pincodes['pincode']]['count'] = $pincodes['pincodeCount'];
                $finalPincodeArray[$pincodes['pincode']]['state'] = $pincodes['state'];
                $finalPincodeArray[$pincodes['pincode']]['service_id'][] = $pincodes['service_id'];
                $finalPincodeArray[$pincodes['pincode']]['services'][] = $pincodes['services'];
                $finalPincodeArray[$pincodes['pincode']]['services_count'][] = $pincodes['pincodeCount'];
                $finalPincodeArray[$pincodes['pincode']]['city'] = $pincodes['city'];
            }
        }
        arsort($finalPincodeArray);
        $data['pincodeResult'] = $finalPincodeArray;
        $data['agent'] = $agentID;
        $this->load->view('dashboard/missing_pincodes_full_view',$data);
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
        $pincodeResult =  $this->dashboard_model->get_missing_pincode_query_count_by_admin();
        $template = array(
        'table_open' => '<table  '
            . ' class="table table-striped table-bordered jambo_table bulk_action">'
        );
        $this->table->set_template($template);
        $this->table->set_heading(array('S.N','RM', 'Pending Queries'));
        for($i=0;$i<count($pincodeResult);$i++){
            $this->table->add_row($i,"<a target='_blank' href=".base_url()."employee/dashboard/missing_pincode_full_view/".$pincodeResult[$i]['id']." "
                    . "style='margin: 0px;padding: 6px;' class='btn btn-info'>".$pincodeResult[$i]['full_name']."</a>",$pincodeResult[$i]['pincodeCount']); 
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
    /*
     * This Function is used to get all sf's escalaltion related to a specific RM, 
     * This function get data from Both tables(Booking,Escalation) to get escalaltion %
     * @input - RM id , Dates are optional
     */
function get_sf_escalation_by_rm($rm_id,$startDate,$endDate){
    $sfIDNameArray = array();
    $SfBookingArray = array();
    $esclationPercentage = array();
    //create groupby array for booking(group by rm and then vendor)
    $groupBy['booking'] = array("employee_relation.agent_id","booking_details.assigned_vendor_id");
    //create groupby array for escalation(group by rm and then vendor)
    $groupBy['escalation'] = array("employee_relation.agent_id","vendor_escalation_log.vendor_id");
    // get escalation data and booking data for all vendor related to rm
    $escalationBookingData = $this->dashboard_model->get_sf_escalation_by_rm_by_sf_by_date($startDate,$endDate,NULL,$rm_id,$groupBy);
    // get Service center name and id
    $sfArray = $this->reusable_model->get_search_result_data("service_centres","id,name",NULL,NULL,NULL,NULL,NULL,NULL,NULL);
    // Create an associative array for service Center and ID
    if($sfArray){
        foreach($sfArray as $sfData){
            $sfIDNameArray["vendor_".$sfData['id']]= $sfData['name'];
        }
    }
    //Create Associative array for Vendor booking(Pass Vendor ID get vendor Booking)
    if($escalationBookingData['booking']){
        foreach($escalationBookingData['booking'] as $bookingData){
            if($bookingData['assigned_vendor_id'] !=''){
                $SfBookingArray["vendor_".$bookingData['assigned_vendor_id']] = $bookingData['total_booking'];
            }
        }
    }
    //Run Escalation Data through loop to calculate final matrix(total_escalation,total_booking,escalation% etc)For each and every vendor 
    if($escalationBookingData['escalation']){
    foreach($escalationBookingData['escalation'] as $escalationData){
        if($escalationData['vendor_id'] !=0 ){
           $vendorBooking = 0;
           $vendorName = "";
           if(array_key_exists("vendor_".$escalationData['vendor_id'], $SfBookingArray)){
               $vendorBooking = $SfBookingArray["vendor_".$escalationData['vendor_id']];
           }
           if(array_key_exists("vendor_".$escalationData['vendor_id'], $sfIDNameArray)){
               $vendorName = $sfIDNameArray["vendor_".$escalationData['vendor_id']];
           }
           if($vendorBooking !=0){
           $tempArray= array("esclation_per"=>round((($escalationData['total_escalation']*100)/$vendorBooking),2),"vendor_id"=>$escalationData['vendor_id'],
               "total_booking"=>$vendorBooking,"total_escalation"=>$escalationData['total_escalation'],"vendor_name"=>$vendorName,"startDate"=>$startDate,"endDate"=>$endDate);
           $esclationPercentage[]=$tempArray;
           }
       }
    }
    }
    //Echo final matrix array to use for Angular JS
    echo json_encode($esclationPercentage);
}
/*
 * This is a helper function to get escalation data by Vendor For get_escalations_chart_data()
 * This function get all escalation for a vendor and then get appliance,upcountry,request_type information for all escalated bookings
 * @input - Vendor ID, Dates are optional
 */
function get_escalation_data($sfID,$startDate=NULL,$endDate=NULL){
    //Create Where Array for escalation table
    $escalation_where["vendor_escalation_log.vendor_id"] =$sfID;
    //if dates are there then add given dates in where condition  
    if(!($startDate) && !($endDate)){
            $escalation_where["month(vendor_escalation_log.create_date) = month(now()) AND year(vendor_escalation_log.create_date) = year(now())"] =NULL;
       }
       //if dates are not set get current month escalaltion data
       else{
            $escalation_where["date(vendor_escalation_log.create_date) >= '".$startDate."' AND date(vendor_escalation_log.create_date) < '".$endDate."'"] =  NULL;
       }
       //get vendor total escalation total booking group by serviceID,Upcountry and requestType
    $data = $this->reusable_model->get_search_result_data('vendor_escalation_log',"count(DISTINCT booking_details.booking_id) AS total_booking,count(vendor_escalation_log.booking_id) "
            . "AS total_escalation,booking_details.assigned_vendor_id,	services.services,service_centres.name,booking_details.is_upcountry,booking_details.request_type",$escalation_where,
                    array("booking_details"=>"vendor_escalation_log.booking_id=booking_details.booking_id","services"=>"services.id=booking_details.service_id"
                        ,"service_centres"=>"service_centres.id=booking_details.assigned_vendor_id"),
                    NULL,array("total_escalation"=>"DESC"),NULL,
                    NULL,array("booking_details.service_id","booking_details.is_upcountry","booking_details.request_type"));
    return $data;
}
/* 
 * This is a helper function to get data for pi chart (eg- Pass upcountry as key it will return an associative array for all upcountry total and non upcountry ) For function get_escalations_chart_data()
 * @input - Data(Which we get From get_escalation_data function),Key(On which basis you want to break down the data eg- (Upcountry,Appliance,RequestType))
 */
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
/*
 * This is a helper function For get_escalations_chart_data() to get data breack down for pi chart on the basis of 2 keys
 */
function get_escalation_chart_data_by_two_matrix($data,$baseKey,$otherKey){
    
    $resultArray= array();
    foreach ($data as $escalationData){
        if(array_key_exists($escalationData[$baseKey], $resultArray)){
            if(array_key_exists($escalationData[$otherKey], $resultArray[$escalationData[$baseKey]])){
                $resultArray[$escalationData[$baseKey]][$escalationData[$otherKey]] = $resultArray[$escalationData[$baseKey]][$escalationData[$otherKey]]+$escalationData['total_escalation'];
            }
            else{
                $resultArray[$escalationData[$baseKey]][$escalationData[$otherKey]] = $escalationData['total_escalation'];
            }
        }
        else{
            $resultArray[$escalationData[$baseKey]][$escalationData[$otherKey]] = $escalationData['total_escalation'];
        }
    }
    return $resultArray;
}
/*
 * This function is used to get escalation data for vendor PI chart
 * On the basis of request it returns break down of data into 1 key or 2 key(Key - Upcountry,Appliance,Request Type) 
 * @input- Vendor ID , Dates are optional
 */
 function get_escalations_chart_data($sfID,$startDate=NULL,$endDate=NULL){    
     //Create blank request type array (All request type will be divide only in Installation and Repair)
        $finalData['upcountry']= array();
        $finalData['request_type'] = array();
        $requestTypeNewArray['Installation'] = $requestTypeNewArray['Repair'] = 0;
        // Get Escalation Data For Vendor
        $data = $this->get_escalation_data($sfID,$startDate,$endDate);
        // get escalation by upcountry
        $upcountryData= $this->get_escalation_chart_data_by_one_matrix($data,"is_upcountry");
        $finalData['upcountry']['upcountry'] = $finalData['upcountry']['non_upcountry'] =0;
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
        if(!empty($requestTypeData)){
            foreach($requestTypeData as $requestName => $esclation){
                if (strpos($requestName, 'repair') !== false) {
                    $requestTypeNewArray['Repair'] = $esclation+$requestTypeNewArray['Repair'];
                }
                else{
                     $requestTypeNewArray['Installation'] = $esclation+$requestTypeNewArray['Installation'];
                }
            }
            $finalData['request_type'] = $requestTypeNewArray;
        }
        // Get Data Breack Down into Appliance And then Upcountry 
        $applianceUpcountryArray = $this->get_escalation_chart_data_by_two_matrix($data,"services","is_upcountry");
        // Convert Upcountry O key as  non-upcountry and key 1 as upcountry
        foreach($applianceUpcountryArray as $key=>$value){
            if(array_key_exists("1", $value)){
                $finalData['service_upcountry'][$key][] = array("Upcountry",$value[1]);
            }  
            else{
                 $finalData['service_upcountry'][$key][] = array("Upcountry",0);
            }
            if(array_key_exists("0", $value)){
               $finalData['service_upcountry'][$key][] = array("Non_Upcountry",$value[0]);
            }
             else{
                 $finalData['service_upcountry'][$key][] = array("Non_Upcountry",0);
            }
        }
        echo json_encode($finalData);
    }
    /*
     * This function is used to create Vendor Escalaltion Performance Chart(Bar Chart)
     */
    function get_sf_performance_bar_chart_data($sf){
     $escalationAssociativeArray = array();
    // Create Start Date and End Date to Get Data For last 1 year from Today
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
    //Create Group by array for booking and escalation
    $groupBy['booking'] = array("MONTHNAME(STR_TO_DATE(booking_details.booking_date,'%d-%m-%Y'))");
    $groupBy['escalation'] = array("MONTHNAME(vendor_escalation_log.create_date)");
    // Get escalation by vendor group by date
    $data = $this->dashboard_model->get_sf_escalation_by_rm_by_sf_by_date($startDate,$endDate,$sf,NULL,$groupBy);
    // Create Associative array for escalation (Pass Vendor ID Return Escalation number)
    foreach($data['escalation'] as $escalationData){
        $escalationAssociativeArray[$escalationData['escalation_month']]= $escalationData['total_escalation'];
    }
    // Loop through all booking's Unique Vendor  to get month vise escalation,booking,percentageescalation
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
        $monthlyData['months'][] = date('M', mktime(0, 0, 0, $bookingData['booking_month'], 10))." (".$bookingData['booking_year'].")";
    }
    echo json_encode($monthlyData);
    }
    /*
     * This Function is used to get Full view Of escalation
     */
    function escalation_full_view($RM,$startDate,$endDate){
        $data['rm']=$RM;
        $data['startDate']=$startDate;
        $data['endDate']=$endDate;
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('dashboard/escalation_full_view',$data);
        $this->load->view('dashboard/dashboard_footer');
    }
    /* 
     * This is a helper function for pending_booking_by_rm function 
     * This is used to get repair booking and installation booking in 1 Array 
     */
    private function create_structure_array_for_sf_pending_bookings($installationData,$repairData){
        $installationIDArray = $repairIDArray = $repairDataAssociativeArray = $tempArray = $finalArray = array();
        foreach($installationData as $installationDataID){
            $installationIDArray[] = $installationDataID['service_center_id'];
        }
        foreach($repairData as $repairDataID){
            $repairIDArray[] = $repairDataID['service_center_id'];
            $repairDataAssociativeArray[$repairDataID['service_center_id']] = $repairDataID;
        }
        $extraIDInRepairArray = array_diff($repairIDArray,$installationIDArray);
        foreach($installationData as $iData){
            $tempArray['repair_pending'] = 0;
            $tempArray['repair_booking_id_list'] = '';
            $tempArray['repair_remarks'] = '';
            $tempArray['repair_internal_status'] = '';
            $tempArray['id'] = $iData['service_center_id'];
            $tempArray['installation_pending'] = $iData['booked'];
            $tempArray['name'] = $iData['service_center_name'];
            $tempArray['installation_booking_id_list'] = $iData['booking_id_list'];
            $tempArray['installation_remarks'] = $iData['booking_remarks'];
            $tempArray['installation_internal_status'] = $iData['partner_internal_status'];
            if(array_key_exists($iData['service_center_id'], $repairDataAssociativeArray)){
                $tempArray['repair_pending'] = $repairDataAssociativeArray[$iData['service_center_id']]['booked'];
                $tempArray['repair_booking_id_list'] = $repairDataAssociativeArray[$iData['service_center_id']]['booking_id_list'];
                $tempArray['repair_remarks'] = $repairDataAssociativeArray[$iData['service_center_id']]['booking_remarks'];
                $tempArray['repair_internal_status'] = $repairDataAssociativeArray[$iData['service_center_id']]['partner_internal_status'];
            }
            $finalArray[$iData['service_center_id']] = $tempArray;
        }
        foreach($extraIDInRepairArray as $id){
            $tempArray['installation_pending'] = 0;
            $tempArray['installation_booking_id_list'] = '';
            $tempArray['installation_remarks'] = '';
            $tempArray['installation_internal_status'] = '';
            $tempArray['repair_pending'] = $repairDataAssociativeArray[$id]['booked'];
            $tempArray['repair_booking_id_list'] = $repairDataAssociativeArray[$id]['booking_id_list'];
            $tempArray['repair_remarks'] = $repairDataAssociativeArray[$id]['booking_remarks'];
            $tempArray['repair_internal_status'] = $repairDataAssociativeArray[$id]['partner_internal_status'];
            $tempArray['id'] = $id;
            $tempArray['name'] = $repairDataAssociativeArray[$id]['service_center_name'];
            $finalArray[$id] = $tempArray;
        }
        return $finalArray;
    }
    /*
     * This is a helper function for pending_booking_by_rm
     * This function club all 2_days,3_to_5_days,more_than_5 days data into a single array and return that data
     */
    private function club_all_pending_booking_sf_vise($finalArray,$serviceCentersIDArray){
        $outputArray = array();
        $allServiceCenters = explode(",",$serviceCentersIDArray);
        foreach($allServiceCenters as $id){
            $tempArray['last_2_days_repair_pending'] = $tempArray['last_2_days_installation_pending'] = $tempArray['last_3_to_5_days_repair_pending'] = 
            $tempArray['last_3_to_5_days_installation_pending'] = $tempArray['more_then_5_days_repair_pending'] = $tempArray['more_then_5_days_installation_pending'] = 0;
            $tempArray['last_2_days_installation_booking_list'] = $tempArray['last_2_days_repair_booking_list'] = $tempArray['last_3_to_5_days_installation_booking_list'] =
            $tempArray['last_3_to_5_days_repair_booking_list'] =  $tempArray['more_then_5_days_installation_booking_list'] = $tempArray['more_then_5_days_repair_booking_list'] ='';
            $tempArray['last_2_days_installation_remarks'] = $tempArray['last_2_days_repair_remarks'] = $tempArray['last_3_to_5_days_installation_remarks'] =
            $tempArray['last_3_to_5_days_repair_remarks'] =  $tempArray['more_then_5_days_installation_remarks'] = $tempArray['more_then_5_days_repair_remarks'] ='';
            $tempArray['last_2_days_installation_status'] = $tempArray['last_2_days_repair_status'] = $tempArray['last_3_to_5_days_installation_status'] =
            $tempArray['last_3_to_5_days_repair_status'] =  $tempArray['more_then_5_days_installation_status'] = $tempArray['more_then_5_days_repair_status'] ='';
            $tempVariable = 0;
            if(!empty($finalArray['last2DaysArray'])){
                if(array_key_exists($id, $finalArray['last2DaysArray'])){
                    $tempArray['last_2_days_repair_pending'] = $finalArray['last2DaysArray'][$id]['repair_pending'];
                    $tempArray['last_2_days_installation_pending'] = $finalArray['last2DaysArray'][$id]['installation_pending'];
                    $tempArray['last_2_days_installation_booking_list'] = $finalArray['last2DaysArray'][$id]['installation_booking_id_list'];
                    $tempArray['last_2_days_installation_remarks'] = $finalArray['last2DaysArray'][$id]['installation_remarks'];
                    $tempArray['last_2_days_installation_status'] = $finalArray['last2DaysArray'][$id]['installation_internal_status'];
                    $tempArray['last_2_days_repair_booking_list'] = $finalArray['last2DaysArray'][$id]['repair_booking_id_list'];
                    $tempArray['last_2_days_repair_remarks'] = $finalArray['last2DaysArray'][$id]['repair_remarks'];
                    $tempArray['last_2_days_repair_status'] = $finalArray['last2DaysArray'][$id]['repair_internal_status'];
                    $tempArray['id'] = $id;
                    $tempArray['name'] = $finalArray['last2DaysArray'][$id]['name'];
                    $tempVariable++;
                }
            }
             if(array_key_exists($id, $finalArray['last3To5DaysArray'])){
                $tempArray['last_3_to_5_days_repair_pending'] = $finalArray['last3To5DaysArray'][$id]['repair_pending'];
                $tempArray['last_3_to_5_days_installation_pending'] = $finalArray['last3To5DaysArray'][$id]['installation_pending'];
                $tempArray['last_3_to_5_days_installation_booking_list'] = $finalArray['last3To5DaysArray'][$id]['installation_booking_id_list'];
                $tempArray['last_3_to_5_days_installation_remarks'] = $finalArray['last3To5DaysArray'][$id]['installation_remarks'];
                $tempArray['last_3_to_5_days_installation_status'] = $finalArray['last3To5DaysArray'][$id]['installation_internal_status'];
                $tempArray['last_3_to_5_days_repair_booking_list'] = $finalArray['last3To5DaysArray'][$id]['repair_booking_id_list'];
                $tempArray['last_3_to_5_days_repair_remarks'] = $finalArray['last3To5DaysArray'][$id]['repair_remarks'];
                $tempArray['last_3_to_5_days_repair_status'] = $finalArray['last3To5DaysArray'][$id]['repair_internal_status'];
                $tempArray['id'] = $id;
                $tempArray['name'] = $finalArray['last3To5DaysArray'][$id]['name'];
                $tempVariable++;
            }
            if(array_key_exists($id, $finalArray['moreThen5DaysArray'])){
                $tempArray['more_then_5_days_repair_pending'] = $finalArray['moreThen5DaysArray'][$id]['repair_pending'];
                $tempArray['more_then_5_days_installation_pending'] = $finalArray['moreThen5DaysArray'][$id]['installation_pending'];
                $tempArray['more_then_5_days_installation_booking_list'] = $finalArray['moreThen5DaysArray'][$id]['installation_booking_id_list'];
                $tempArray['more_then_5_days_installation_remarks'] = $finalArray['moreThen5DaysArray'][$id]['installation_remarks'];
                $tempArray['more_then_5_days_installation_status'] = $finalArray['moreThen5DaysArray'][$id]['installation_internal_status'];
                $tempArray['more_then_5_days_repair_booking_list'] = $finalArray['moreThen5DaysArray'][$id]['repair_booking_id_list'];
                $tempArray['more_then_5_days_repair_remarks'] = $finalArray['moreThen5DaysArray'][$id]['repair_remarks'];
                $tempArray['more_then_5_days_repair_status'] = $finalArray['moreThen5DaysArray'][$id]['repair_internal_status'];
                $tempArray['id'] = $id;
                $tempArray['name'] = $finalArray['moreThen5DaysArray'][$id]['name'];
                $tempVariable++;
            }
            if($tempVariable !=0){
                $tempArray['total_pending_repair'] = $tempArray['more_then_5_days_repair_pending']+$tempArray['last_3_to_5_days_repair_pending']+$tempArray['last_2_days_repair_pending'];
                $tempArray['total_pending_installation'] = $tempArray['more_then_5_days_installation_pending']+$tempArray['last_3_to_5_days_installation_pending']+
                        $tempArray['last_2_days_installation_pending'];
                $outputArray[$id] = $tempArray;
            }
        }
        return $outputArray;
    }
    /*
     * This Function is used to get SF(Related to Particular RM) pending Booking Data(Installation,Repair)(last_2_days,3_to_5_days,more_than_5_days) 
     */
    function pending_booking_by_rm($rmID){
        $finalArray =$serviceCentersData = array();
        //Get Service Centers Associated to RM
        $serviceCentersIDArray= $this->vendor_model->get_employee_relation($rmID);
        if(!empty($serviceCentersIDArray)){
            $serviceCentersIDList = $serviceCentersIDArray[0]['service_centres_id'];
            $where = 'AND service_centres.active=1 AND service_centres.on_off=1 AND service_centres.id  IN (' . $serviceCentersIDList . ') AND booking_details.actor="vendor"';
            // All Booking Where request_type is not like repair Should be considered as Installation Bookings
            $where_installation = $where." AND (request_type NOT LIKE '%Repair%' AND request_type NOT LIKE '%Repeat%')";
            // All Booking Where request_type is like repair Should be considered as Repair Bookings
            $where_repair = $where." AND (request_type LIKE '%Repair%' OR request_type LIKE '%Repeat%')";
            $groupBY = "GROUP BY service_centres.name";
            //get Installation Booking Data
            $installationData = $this->reporting_utils->get_pending_booking_by_service_center_query_data($where_installation,$groupBY);
            //get Repair Booking Data
            $repairData = $this->reporting_utils->get_pending_booking_by_service_center_query_data($where_repair,$groupBY);
            //Club Repair and Installation in 1 array by SF
            $finalArray['last2DaysArray'] = $this->create_structure_array_for_sf_pending_bookings($installationData['data_last_2_day'],$repairData['data_last_2_day']);
            $finalArray['last3To5DaysArray'] = $this->create_structure_array_for_sf_pending_bookings($installationData['data_last_3_day'],$repairData['data_last_3_day']);
            $finalArray['moreThen5DaysArray'] = $this->create_structure_array_for_sf_pending_bookings($installationData['data_greater_than_5_days'],$repairData['data_greater_than_5_days']);
            $serviceCentersData = $this->club_all_pending_booking_sf_vise($finalArray,$serviceCentersIDArray[0]['service_centres_id']);
        }
        return $serviceCentersData;
    }
    function get_escalation_by_all_rm($startDate,$endDate){
    $rmIDNameArray = array();
    $rmBookingArray = array();
    $rmEscalationArray = array();
    $esclationPercentage = array();
    //create groupby array for booking(group by rm and then vendor)
    $groupBy['booking'] = array("employee_relation.agent_id","booking_details.assigned_vendor_id");
    //create groupby array for escalation(group by rm and then vendor)
    $groupBy['escalation'] = array("employee_relation.agent_id","vendor_escalation_log.vendor_id");
    // get escalation data and booking data for all vendor related to rm
    $escalationBookingData = $this->dashboard_model->get_sf_escalation_by_rm_by_sf_by_date($startDate,$endDate,NULL,NULL,$groupBy);
    // get Service center name and id
    $rmArray = $this->reusable_model->get_search_result_data("employee","id,full_name",NULL,NULL,NULL,NULL,NULL,NULL,NULL);
    // Create an associative array for service Center and ID
    if($rmArray){
        foreach($rmArray as $RMData){
            $rmIDNameArray["RM_".$RMData['id']]= $RMData['full_name'];
        }
    }
    //Create Associative array for Vendor booking(Pass Vendor ID get vendor Booking)
    if($escalationBookingData['booking']){
        foreach($escalationBookingData['booking'] as $bookingData){
            if(array_key_exists("RM_".$bookingData['rm_id'], $rmBookingArray)){
                $rmBookingArray["RM_".$bookingData['rm_id']] = $rmBookingArray["RM_".$bookingData['rm_id']] +$bookingData['total_booking'];
            }
            else{
                $rmBookingArray["RM_".$bookingData['rm_id']] = $bookingData['total_booking'];
            }
        }
    }
    if($escalationBookingData['escalation']){
        foreach($escalationBookingData['escalation'] as $escalationData){
            if(array_key_exists("RM_".$escalationData['rm_id'], $rmEscalationArray)){
                $rmEscalationArray["RM_".$escalationData['rm_id']] = $rmEscalationArray["RM_".$escalationData['rm_id']] +$escalationData['total_escalation'];
            }
            else{
                $rmEscalationArray["RM_".$escalationData['rm_id']] = $escalationData['total_escalation'];
            }
        }
    }
    //Run Escalation Data through loop to calculate final matrix(total_escalation,total_booking,escalation% etc)For each and every vendor 
    if(!empty($rmEscalationArray)){
    foreach($rmEscalationArray as $RM=>$escalation){
        if($escalation !=0 ){
           $RMBooking = 0;
           $RMName = "";
           if(array_key_exists($RM, $rmBookingArray)){
               $RMBooking = $rmBookingArray[$RM];
           }
           if(array_key_exists("RM_".$escalationData['rm_id'], $rmBookingArray)){
               $RMName = $rmIDNameArray[$RM];
           }
           $tempArray= array("esclation_per"=>round((($escalation*100)/$RMBooking),2),"rm_id"=>$RM,
               "total_booking"=>$RMBooking,"total_escalation"=>$escalation,"rm_name"=>$RMName,"startDate"=>$startDate,"endDate"=>$endDate);
           $esclationPercentage[]=$tempArray;
       }
    }
    }
    //Echo final matrix array to use for Angular JS
    echo json_encode($esclationPercentage);
    }
    function wrong_pincode_handler($pincode){
        $this->reusable_model->update_table("sf_not_exist_booking_details",array("is_pincode_valid"=>0,"invalid_pincode_marked_by"=>$this->session->userdata('id')),array("pincode"=>$pincode));
        $this->session->set_userdata(array("wrong_pincode_msg"=>"Pincode has been marked as Wrong Pincode Successfully"));
        redirect(base_url().'employee/dashboard');
    }
    /*
     * This Function is used to get Pending Booking Data BY all RM
     */
    function pending_booking_count_by_rm(){
        $finalArray = array();
        // Get all RM
        $allRMArray = $this->reusable_model->get_search_result_data("employee","id,full_name",array('groups'=>'regionalmanager'),NULL,NULL,NULL,NULL,NULL,array());
        //Loop Through RM ID
        foreach($allRMArray as $rmIdArray){
            $tempRMArray['last_2_day_installation_booking_count'] = $tempRMArray['last_2_day_repair_booking_count'] = $tempRMArray['last_3_to_5_days_repair_count'] = 
            $tempRMArray['last_3_to_5_days_installation_count']  = $tempRMArray['more_then_5_days_repair_count'] = $tempRMArray['more_then_5_days_installation_count'] =  0;
            // Get Pending Booking BY SF (Specific to particular RM)
            $tempArray =  $this->pending_booking_by_rm($rmIdArray['id']);
            if(!empty($tempArray)){
                // Loop through Vendor Data
                foreach($tempArray as $vendorBookingArray){
                    $tempRMArray['last_2_day_installation_booking_count'] = $tempRMArray['last_2_day_installation_booking_count']+$vendorBookingArray['last_2_days_installation_pending'];
                    $tempRMArray['last_2_day_repair_booking_count'] = $tempRMArray['last_2_day_repair_booking_count']+$vendorBookingArray['last_2_days_repair_pending'];
                    $tempRMArray['last_3_to_5_days_repair_count'] = $tempRMArray['last_3_to_5_days_repair_count']+$vendorBookingArray['last_3_to_5_days_repair_pending'];
                    $tempRMArray['last_3_to_5_days_installation_count'] = $tempRMArray['last_3_to_5_days_installation_count']+$vendorBookingArray['last_3_to_5_days_installation_pending'];
                    $tempRMArray['more_then_5_days_repair_count'] = $tempRMArray['more_then_5_days_repair_count']+$vendorBookingArray['more_then_5_days_repair_pending'];
                    $tempRMArray['more_then_5_days_installation_count'] = $tempRMArray['more_then_5_days_installation_count']+$vendorBookingArray['more_then_5_days_installation_pending'];
                }
                $tempRMArray['total_pending'] = $tempRMArray['last_2_day_installation_booking_count']+$tempRMArray['last_2_day_repair_booking_count']+
                        $tempRMArray['last_3_to_5_days_repair_count']+$tempRMArray['last_3_to_5_days_installation_count']+$tempRMArray['more_then_5_days_repair_count']+
                        $tempRMArray['more_then_5_days_installation_count'];
                $tempRMArray['rm'] = $rmIdArray['full_name'];
                $tempRMArray['rmID'] = $rmIdArray['id'];
                $finalArray[] = $tempRMArray;
            }
        }
        echo json_encode($finalArray);
    }
    /*
     * This function is used to call view of Pending booking  by sf 
     */
    function pending_full_view_by_sf($rm_id){
        $data['rm']=$rm_id;
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('dashboard/pending_booking_full_view_by_sf',$data);
        $this->load->view('dashboard/dashboard_footer');
    }
    /*
     * This function is use to send data for SF Pending Booking View Page (By Installation,BY Repair) 
     */
    function pending_booking_by_rm_view($rm_id){
        $data =  $this->pending_booking_by_rm($rm_id);
        echo json_encode(array_values($data));
    }
    
    function brackets_snapshot_full_view(){
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('dashboard/brackets_snapshot_view');
        $this->load->view('dashboard/dashboard_footer');
    }
    function missing_pincode_group_by_data_helper($dataArray,$groupByKeyName,$secondKeyName){
        $finalPincodeArray = array();
            foreach($dataArray as $pincodesData){
                    if(array_key_exists($pincodesData[$groupByKeyName], $finalPincodeArray)){
                        $finalPincodeArray[$pincodesData[$groupByKeyName]]['finalCount'] =  $finalPincodeArray[$pincodesData[$groupByKeyName]]['finalCount']+$pincodesData['pincodeCount'];
                        if(array_key_exists($pincodesData[$secondKeyName],$finalPincodeArray[$pincodesData[$groupByKeyName]][$secondKeyName])){
                            $finalPincodeArray[$pincodesData[$groupByKeyName]][$secondKeyName][$pincodesData[$secondKeyName]]['count'] =
                                    $finalPincodeArray[$pincodesData[$groupByKeyName]][$secondKeyName][$pincodesData[$secondKeyName]]['count']+$pincodesData['pincodeCount'];  
                            if(array_key_exists($pincodesData['pincode'],$finalPincodeArray[$pincodesData[$groupByKeyName]][$secondKeyName][$pincodesData[$secondKeyName]])){
                                $finalPincodeArray[$pincodesData[$groupByKeyName]][$secondKeyName][$pincodesData[$secondKeyName]]['pincodes'][$pincodesData['pincode']] =
                                    $finalPincodeArray[$pincodesData[$groupByKeyName]][$secondKeyName][$pincodesData[$secondKeyName]]['pincodes'][$pincodesData['pincode']] +$pincodesData['pincodeCount'];  
                            }
                            else{
                                $finalPincodeArray[$pincodesData[$groupByKeyName]][$secondKeyName][$pincodesData[$secondKeyName]]['pincodes'][$pincodesData['pincode']] = $pincodesData['pincodeCount'];  
                            }
                        }
                        else{
                            $finalPincodeArray[$pincodesData[$groupByKeyName]][$secondKeyName][$pincodesData[$secondKeyName]]['count'] = $pincodesData['pincodeCount'];  
                             $finalPincodeArray[$pincodesData[$groupByKeyName]][$secondKeyName][$pincodesData[$secondKeyName]]['pincodes'][$pincodesData['pincode']] = $pincodesData['pincodeCount'];  
                        }
                    }
                    else{
                          $finalPincodeArray[$pincodesData[$groupByKeyName]]['finalCount'] =  $pincodesData['pincodeCount'];
                          $finalPincodeArray[$pincodesData[$groupByKeyName]][$secondKeyName][$pincodesData[$secondKeyName]]['count'] = $pincodesData['pincodeCount'];
                          $finalPincodeArray[$pincodesData[$groupByKeyName]][$secondKeyName][$pincodesData[$secondKeyName]]['pincodes'][$pincodesData['pincode']] = $pincodesData['pincodeCount'];
                 }
            } 
            return $finalPincodeArray;
    }
    function missing_pincode_group_by_view_helper($finalPincodeArray,$divID,$heading,$breakDownKey){
        ?>
                    <table class="table table-bordered">
                                    <tr style="background: #2a3f54;color: #fff;">
                                        <th>S.N</th>
                                        <th><?php echo $heading; ?></th>
                                        <th>Pending Query Count</th>
                                    </tr>
                                <?php
                                $sn = 1;
                                foreach($finalPincodeArray as $district=>$districtData){
                                    ?>
                                    <tr>
                                        <td><?php echo $sn;?></td>
                                        <td style="width: 800px;"><button style="margin: 0px;padding: 3px 9px;font-size: 15px;" type="button" class="btn btn-info" id="district_level" onclick='$("#<?php echo $divID."_".$sn?>").toggle();'>
                                            <?php echo $district." +";?></button>   
                                            <div id="<?php echo $divID."_".$sn?>" style="display: none;">
                                                <table class="table table-bordered" style="margin-top: 10px;">
                                                    <tr>
                                                    <th>Service</th>
                                                     <th>Pending Query Count</th>
                                                </tr>
                                                <?php
                                                foreach($districtData[$breakDownKey] as $key=>$value) {
                                                    ?>
                                                <tr>
                                                    <td><?php echo $key; ?></td>
                                                     <td><button onclick='group_by_district_for_appliance(<?php echo json_encode($value['pincodes'])?>)'  style="margin: 0px;padding: 3px 9px; font-size:15px;" 
                                                                 type="button" class="btn btn-info" data-toggle="modal" 
                                                    data-target="#missingPincodeDetails"><?php echo $value['count']; ?></button></td>
                                                    </tr>
                                                <?php
                                                }
                                                ?>
                                                </table>
                                                </div>
                                        </td>
                                        <td><?php echo $districtData['finalCount'];?></td>
                                    </tr>
                                    <?php
                                    $sn++;
                                }
                                ?>
                                </table>
<?php
    }
    function get_missing_pincode_data_group_by_district($agentID){
        $select = "COUNT(sf.pincode) as pincodeCount,services.services,UPPER(sf.city) as city ,sf.pincode";
        $where['sf.rm_id'] = $agentID;
        $where['sf.active_flag'] = 1;
        $where['sf.is_pincode_valid'] = 1;
        $orderBYArray['pincodeCount'] = 'DESC';
        $groupBY = array('city,sf.service_id,sf.pincode');
        $join['services']  = 'sf.service_id=services.id';
        $JoinTypeTableArray['services'] = 'left';
        $dataArray = $this->reusable_model->get_search_result_data("sf_not_exist_booking_details sf",$select,$where,$join,NULL,$orderBYArray,NULL,$JoinTypeTableArray,$groupBY);
        $finalPincodeArray = $this->missing_pincode_group_by_data_helper($dataArray,'city','services');
        $this->missing_pincode_group_by_view_helper($finalPincodeArray,'district_appliance','District','services');
    }
    function get_missing_pincode_data_group_by_partner($agentID){
        $select = "partners.public_name,COUNT(sf.pincode) as pincodeCount,sf.city,sf.pincode";
        $where['sf.rm_id'] = $agentID;
        $where['sf.active_flag'] = 1;
        $where['sf.is_pincode_valid'] = 1;
        $orderBYArray['pincodeCount'] = 'DESC';
        $groupBY = array('partners.public_name','sf.city','sf.pincode');
        $join['partners']  = 'sf.partner_id=partners.id';
        $JoinTypeTableArray['services'] = 'left';
        $dataArray = $this->reusable_model->get_search_result_data("sf_not_exist_booking_details sf",$select,$where,$join,NULL,$orderBYArray,NULL,$JoinTypeTableArray,$groupBY);
        $finalPincodeArray = $this->missing_pincode_group_by_data_helper($dataArray,'public_name','city');
        $this->missing_pincode_group_by_view_helper($finalPincodeArray,'partner_appliance','Partner','city');
    }
    function get_missing_pincode_data_group_by_appliance($agentID){
        $select = "COUNT(sf.pincode) as pincodeCount,services.services,sf.city,sf.pincode";
        $where['sf.rm_id'] = $agentID;
        $where['sf.active_flag'] = 1;
        $where['sf.is_pincode_valid'] = 1;
        $orderBYArray['pincodeCount'] = 'DESC';
        $groupBY = array('services.services','sf.city','sf.pincode');
        $join['services']  = 'sf.service_id=services.id';
        $JoinTypeTableArray['services'] = 'left';
        $dataArray = $this->reusable_model->get_search_result_data("sf_not_exist_booking_details sf",$select,$where,$join,NULL,$orderBYArray,NULL,$JoinTypeTableArray,$groupBY);
        $finalPincodeArray = $this->missing_pincode_group_by_data_helper($dataArray,'services','city');
        $this->missing_pincode_group_by_view_helper($finalPincodeArray,'appliance_district','Appliance','city');
    }
}