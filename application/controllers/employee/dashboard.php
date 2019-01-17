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
        $this->load->model('inventory_model');
        $this->load->model('bb_model');
        $this->load->model('cp_model');
        $this->load->library("miscelleneous");

        $this->load->library('table');

        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee' || $this->session->userdata('userType') == 'partner' || $this->session->userdata('userType') == 'service_center')) {
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
        if($this->session->userdata('partner_id')){
            redirect(base_url() . "employee/login");
        }
        else{
            $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
            if($this->session->userdata('user_group') == _247AROUND_ACCOUNTANT){
                redirect(base_url().'employee/invoice/invoice_partner_view');
            }else{
                if($this->session->userdata('user_group') == _247AROUND_AM){
                    $partnerWhere['account_manager_id'] = $this->session->userdata('id');
                }
                $partnerWhere['is_active'] = 1;
                $data['partners'] = $this->partner_model->getpartner_details('partners.id,partners.public_name',$partnerWhere);
                $serviceWhere['isBookingActive'] =1;
                $data['services'] = $this->reusable_model->get_search_result_data("services","*",$serviceWhere,NULL,NULL,array("services"=>"ASC"),NULL,NULL,array());
                $this->load->view("dashboard/".$this->session->userdata('user_group')."_dashboard",$data);
            }
            $this->load->view('dashboard/dashboard_footer');
            $this->load->view('employee/header/push_notification');
        }
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
        $partner_id = "";
        if ($this->input->post('partner_id')) {
            $partner_id = $this->input->post('partner_id');
        }
        if ($is_repeat_ajax) {
            $sDate = $this->input->post('sDate');
            $eDate = $this->input->post('eDate');
            $startDate = date('Y-m-d 00:00:00', strtotime($sDate));
            $endDate = date('Y-m-d 23:59:59', strtotime($eDate));
            $this->make_rm_final_bookings_data($startDate, $endDate, $partner_id);
        } else {
            $timestamp = strtotime(date("Y-m-d"));
            $startDate = date('Y-m-01 00:00:00', $timestamp);
            $endDate = date('Y-m-d 23:59:59', $timestamp);
            $this->make_rm_final_bookings_data($startDate, $endDate,$partner_id);
        }
    }
    
    /**
     * @desc: This function is used to make json for booking based on rm
     * @param string
     * @return array
     */
    private function make_rm_final_bookings_data($startDate, $endDate, $partnerid = "") {
        $rm_array = $this->employee_model->get_rm_details();
        $region = array();
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
            switch ($value['full_name']) {
                case EAST_RM:
                    $region[] = "East";
                break;
                case SOUTH_RM:
                    $region[] = "South";
                break;
                case WEST_RM:
                    $region[] = "West";
                break;
                case NORTH_RM:
                    $region[] = "North";
                break;
            }
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
        $json_data['region'] = implode(",",$region);
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
    function missing_pincode_full_view($agentID = NULL){
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        //SELECT sf.pincode, COUNT(sf.pincode) as pincodeCount, sf.state, sf.city FROM (sf_not_exist_booking_details sf) WHERE `sf`.`rm_id` = '11' AND `sf`.`active_flag` = 1 
        //AND `sf`.`is_pincode_valid` = 1 GROUP BY sf.pincode ORDER BY pincodeCount DESC
        $select = "sf.pincode,COUNT(sf.pincode) as pincodeCount,sf.state,sf.city,sf.service_id,services.services";
        if($agentID){
          $where['sf.rm_id'] = $agentID;  
        } 
        else{
             $where['rm_id IS NULL'] = NULL;  
        }
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
    function download_missing_sf_pincode_excel($rmID = NULL){
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
    if($this->session->userdata('partner_id')){
        $partner_id = $this->session->userdata('partner_id');
    }
    // get escalation data and booking data for all vendor related to rm
    $escalationBookingData = $this->dashboard_model->get_sf_escalation_by_rm_by_sf_by_date($startDate,$endDate,NULL,$rm_id,$groupBy,$partner_id);
    // get Service center name and id
    $sfArray = $this->reusable_model->get_search_result_data("service_centres","id,name,district",NULL,NULL,NULL,NULL,NULL,NULL,NULL);
    // Create an associative array for service Center and ID
    if($sfArray){
        foreach($sfArray as $sfData){
            $sfIDNameArray["vendor_".$sfData['id']]['name']= $sfData['name'];
            $sfIDNameArray["vendor_".$sfData['id']]['city']= $sfData['district'];
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
               $vendorName = $sfIDNameArray["vendor_".$escalationData['vendor_id']]['name'];
               $sf_name_for_partner = $sfIDNameArray["vendor_".$escalationData['vendor_id']]['city']."_247Around_Service_center_".$escalationData['vendor_id'];
           }
           if($vendorBooking !=0){
           $tempArray= array("esclation_per"=>round((($escalationData['total_escalation']*100)/$vendorBooking),2),"vendor_id"=>$escalationData['vendor_id'],
               "total_booking"=>$vendorBooking,"total_escalation"=>$escalationData['total_escalation'],"vendor_name"=>$vendorName,"startDate"=>$startDate,"endDate"=>$endDate,
               "sf_name_for_partner"=>$sf_name_for_partner);
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
    $data = $this->dashboard_model->get_sf_escalation_by_rm_by_sf_by_date($startDate,$endDate,$sf,NULL,$groupBy,NULL);
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
         if($this->session->userdata('userType') == 'employee'){
            $this->miscelleneous->load_nav_header();
        }
        else if($this->session->userdata('userType') == 'partner'){
            $this->miscelleneous->load_partner_nav_header();
        }
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
    function pending_booking_by_rm($rmID,$actor=NULL){
        $finalArray =$serviceCentersData = array();
        //Get Service Centers Associated to RM
        $serviceCentersIDArray= $this->vendor_model->get_employee_relation($rmID);
        if(!empty($serviceCentersIDArray)){
            $serviceCentersIDList = $serviceCentersIDArray[0]['service_centres_id'];
            $where = 'AND service_centres.active=1 AND service_centres.on_off=1 AND service_centres.id  IN (' . $serviceCentersIDList . ')';
            // All Booking Where request_type is not like repair Should be considered as Installation Bookings
            $where_installation = $where." AND (request_type NOT LIKE '%Repair%' AND request_type NOT LIKE '%Repeat%')";
            // All Booking Where request_type is like repair Should be considered as Repair Bookings
            $where_repair = $where." AND (request_type LIKE '%Repair%' OR request_type LIKE '%Repeat%')";
            $groupBY = "GROUP BY service_centres.name";
            //get Installation Booking Data
            $installationData = $this->reporting_utils->get_pending_booking_by_service_center_query_data($where_installation,$groupBY,$actor);
            //get Repair Booking Data
            $repairData = $this->reporting_utils->get_pending_booking_by_service_center_query_data($where_repair,$groupBY,$actor);
            //Club Repair and Installation in 1 array by SF
            $finalArray['last2DaysArray'] = $this->create_structure_array_for_sf_pending_bookings($installationData['data_last_2_day'],$repairData['data_last_2_day']);
            $finalArray['last3To5DaysArray'] = $this->create_structure_array_for_sf_pending_bookings($installationData['data_last_3_day'],$repairData['data_last_3_day']);
            $finalArray['moreThen5DaysArray'] = $this->create_structure_array_for_sf_pending_bookings($installationData['data_greater_than_5_days'],$repairData['data_greater_than_5_days']);
            $serviceCentersData = $this->club_all_pending_booking_sf_vise($finalArray,$serviceCentersIDArray[0]['service_centres_id']);
        }
        return $serviceCentersData;
    }
    function get_escalation_by_all_rm($startDate,$endDate){
    $partnerID = NULL;
    if($this->session->userdata('partner_id')){
        $partnerID = $this->session->userdata('partner_id');
    }
    $rmIDNameArray = array();
    $rmBookingArray = array();
    $rmEscalationArray = array();
    $esclationPercentage = array();
    //create groupby array for booking(group by rm and then vendor)
    $groupBy['booking'] = array("employee_relation.agent_id","booking_details.assigned_vendor_id");
    //create groupby array for escalation(group by rm and then vendor)
    $groupBy['escalation'] = array("employee_relation.agent_id","vendor_escalation_log.vendor_id");
    // get escalation data and booking data for all vendor related to rm
    $escalationBookingData = $this->dashboard_model->get_sf_escalation_by_rm_by_sf_by_date($startDate,$endDate,NULL,NULL,$groupBy,$partnerID);
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
           $zone = "";
           if(array_key_exists($RM, $rmBookingArray)){
               $RMBooking = $rmBookingArray[$RM];
           }
           if(array_key_exists("RM_".$escalationData['rm_id'], $rmBookingArray)){
               $RMName = $rmIDNameArray[$RM];
                switch ($RMName) {
                case EAST_RM:
                    $zone =  "East";
                break;
                case SOUTH_RM:
                    $zone = "South";
                break;
                case WEST_RM:
                    $zone = "West";
                break;
                case NORTH_RM:
                    $zone = "North";
                break;
                default:
                    $zone = "Undefined";
                }
           }
           $tempArray= array("esclation_per"=>round((($escalation*100)/$RMBooking),2),"rm_id"=>$RM,
               "total_booking"=>$RMBooking,"total_escalation"=>$escalation,"rm_name"=>$RMName,"startDate"=>$startDate,"endDate"=>$endDate,"zone"=>$zone);
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
    function pending_booking_count_by_rm($actor=NULL){
        $finalArray = array();
        // Get all RM
        $allRMArray = $this->reusable_model->get_search_result_data("employee","id,full_name",array('groups'=>'regionalmanager'),NULL,NULL,NULL,NULL,NULL,array());
        //Loop Through RM ID
        foreach($allRMArray as $rmIdArray){
            $tempRMArray['last_2_day_installation_booking_count'] = $tempRMArray['last_2_day_repair_booking_count'] = $tempRMArray['last_3_to_5_days_repair_count'] = 
            $tempRMArray['last_3_to_5_days_installation_count']  = $tempRMArray['more_then_5_days_repair_count'] = $tempRMArray['more_then_5_days_installation_count'] =  0;
            // Get Pending Booking BY SF (Specific to particular RM)
            $tempArray =  $this->pending_booking_by_rm($rmIdArray['id'],$actor);
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
            arsort($finalPincodeArray);
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
                                                    <th><?php echo $breakDownKey;?></th>
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
    function get_missing_pincode_data_group_by_district($agentID = NULL){
        $select = "COUNT(sf.pincode) as pincodeCount,services.services,UPPER(sf.city) as city ,sf.pincode";
        if($agentID){
             $where['sf.rm_id'] = $agentID;
        }
       else{
            $where['sf.rm_id IS NULL'] = NULL;
       }
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
    function get_missing_pincode_data_group_by_partner($agentID = NULL){
        $select = "partners.public_name,COUNT(sf.pincode) as pincodeCount,sf.city as District,sf.pincode";
        if($agentID){
          $where['sf.rm_id'] = $agentID;  
        }
        else{
            $where['sf.rm_id IS NULL'] = NULL;  
        }
        $where['sf.active_flag'] = 1;
        $where['sf.is_pincode_valid'] = 1;
        $orderBYArray['pincodeCount'] = 'DESC';
        $groupBY = array('partners.public_name','sf.city','sf.pincode');
        $join['partners']  = 'sf.partner_id=partners.id';
        $JoinTypeTableArray['services'] = 'left';
        $dataArray = $this->reusable_model->get_search_result_data("sf_not_exist_booking_details sf",$select,$where,$join,NULL,$orderBYArray,NULL,$JoinTypeTableArray,$groupBY);
        $finalPincodeArray = $this->missing_pincode_group_by_data_helper($dataArray,'public_name','District');
        $this->missing_pincode_group_by_view_helper($finalPincodeArray,'partner_appliance','Partner','District');
    }
    function get_missing_pincode_data_group_by_appliance($agentID = NULL){
        $select = "COUNT(sf.pincode) as pincodeCount,services.services,sf.city as District,sf.pincode";
        if($agentID){
            $where['sf.rm_id'] = $agentID;
        }
        else{
            $where['sf.rm_id IS NULL'] = NULL;
        }
        $where['sf.active_flag'] = 1;
        $where['sf.is_pincode_valid'] = 1;
        $orderBYArray['pincodeCount'] = 'DESC';
        $groupBY = array('services.services','sf.city','sf.pincode');
        $join['services']  = 'sf.service_id=services.id';
        $JoinTypeTableArray['services'] = 'left';
        $dataArray = $this->reusable_model->get_search_result_data("sf_not_exist_booking_details sf",$select,$where,$join,NULL,$orderBYArray,NULL,$JoinTypeTableArray,$groupBY);
        $finalPincodeArray = $this->missing_pincode_group_by_data_helper($dataArray,'services','District');
        $this->missing_pincode_group_by_view_helper($finalPincodeArray,'appliance_district','Appliance','District');
    }
    function get_TAT_days_total_completed_bookings($finalData){
        $tat_0_total = $tat_1_total = $tat_2_total = $tat_3_total = $tat_4_total =  $tat_5_total = $tat_8_total= $tat_16_total = 0;
         foreach($finalData as $values){
            if(!array_key_exists('TAT_0', $values)){
                $values['TAT_0'] = array();
            }
            if(!array_key_exists('TAT_1', $values)){
                $values['TAT_1'] = array();
            }
            if(!array_key_exists('TAT_2', $values)){
                $values['TAT_2'] = array();
            }
            if(!array_key_exists('TAT_3', $values)){
                $values['TAT_3'] = array();
            }
            if(!array_key_exists('TAT_4', $values)){
                $values['TAT_4'] = array();
            }
            if(!array_key_exists('TAT_5', $values)){
                $values['TAT_5'] = array();
            }
             if(!array_key_exists('TAT_8', $values)){
                $values['TAT_8'] = array();
            }
             if(!array_key_exists('TAT_16', $values)){
                $values['TAT_16'] = array();
            }
            $tat_0_total = count($values['TAT_0'])+$tat_0_total;
            $tat_1_total = count($values['TAT_1'])+$tat_1_total;
            $tat_2_total = count($values['TAT_2'])+$tat_2_total;
            $tat_3_total = count($values['TAT_3'])+$tat_3_total;
            $tat_4_total = count($values['TAT_4'])+$tat_4_total;
            $tat_5_total = count($values['TAT_5'])+$tat_5_total;
            $tat_8_total = count($values['TAT_8'])+$tat_8_total;
            $tat_16_total = count($values['TAT_16'])+$tat_16_total;
            $totalTempArray['TAT_0'] = count($values['TAT_0']); 
            $totalTempArray['TAT_1'] = $totalTempArray['TAT_0'] + count($values['TAT_1']);
            $totalTempArray['TAT_2'] = $totalTempArray['TAT_1'] + count($values['TAT_2']);
            $totalTempArray['TAT_3'] = $totalTempArray['TAT_2'] + count($values['TAT_3']);
            $totalTempArray['TAT_4'] = $totalTempArray['TAT_3'] + count($values['TAT_4']);
            $totalTempArray['TAT_5'] = $totalTempArray['TAT_4'] + count($values['TAT_5']);
            $totalTempArray['TAT_8'] = $totalTempArray['TAT_5'] + count($values['TAT_8']);
            $totalTempArray['TAT_16'] = $totalTempArray['TAT_8'] + count($values['TAT_16']);
            $totalTempArray['TAT_0_per'] = sprintf("%01.2f",(($totalTempArray['TAT_0']*100)/$totalTempArray['TAT_16']));
            $totalTempArray['TAT_1_per'] = sprintf("%01.2f",(($totalTempArray['TAT_1']*100)/$totalTempArray['TAT_16']));
            $totalTempArray['TAT_2_per'] = sprintf("%01.2f",(($totalTempArray['TAT_2']*100)/$totalTempArray['TAT_16']));
            $totalTempArray['TAT_3_per'] = sprintf("%01.2f",(($totalTempArray['TAT_3']*100)/$totalTempArray['TAT_16']));
            $totalTempArray['TAT_4_per'] = sprintf("%01.2f",(($totalTempArray['TAT_4']*100)/$totalTempArray['TAT_16']));
            $totalTempArray['TAT_5_per'] = sprintf("%01.2f",(($totalTempArray['TAT_5']*100)/$totalTempArray['TAT_16']));
            $totalTempArray['TAT_8_per'] = sprintf("%01.2f",(($totalTempArray['TAT_8']*100)/$totalTempArray['TAT_16']));
            $totalTempArray['TAT_16_per'] = sprintf("%01.2f",(($totalTempArray['TAT_16']*100)/$totalTempArray['TAT_16']));
            $totalTempArray["entity"] =  $values['entity_name'];
            $totalTempArray['id'] =  $values['entity_id'];
            $totalArray[] = $totalTempArray;
        }
        $totalTempArray['TAT_0'] = $tat_0_total;
        $totalTempArray['TAT_1'] = $tat_1_total + $totalTempArray['TAT_0'];
        $totalTempArray['TAT_2'] = $tat_2_total + $totalTempArray['TAT_1'];
        $totalTempArray['TAT_3'] = $tat_3_total + $totalTempArray['TAT_2'];
        $totalTempArray['TAT_4'] = $tat_4_total + $totalTempArray['TAT_3'];
        $totalTempArray['TAT_5'] = $tat_5_total + $totalTempArray['TAT_4'];
        $totalTempArray['TAT_8'] = $tat_8_total + $totalTempArray['TAT_5'];
        $totalTempArray['TAT_16'] = $tat_16_total + $totalTempArray['TAT_8'];
        $totalTempArray['TAT_0_per'] = sprintf("%01.2f",(($totalTempArray['TAT_0']*100)/$totalTempArray['TAT_16']));
        $totalTempArray['TAT_1_per'] = sprintf("%01.2f",(($totalTempArray['TAT_1']*100)/$totalTempArray['TAT_16']));
        $totalTempArray['TAT_2_per'] = sprintf("%01.2f",(($totalTempArray['TAT_2']*100)/$totalTempArray['TAT_16']));
        $totalTempArray['TAT_3_per'] = sprintf("%01.2f",(($totalTempArray['TAT_3']*100)/$totalTempArray['TAT_16']));
        $totalTempArray['TAT_4_per'] = sprintf("%01.2f",(($totalTempArray['TAT_4']*100)/$totalTempArray['TAT_16']));
        $totalTempArray['TAT_5_per'] = sprintf("%01.2f",(($totalTempArray['TAT_5']*100)/$totalTempArray['TAT_16']));
        $totalTempArray['TAT_8_per'] = sprintf("%01.2f",(($totalTempArray['TAT_8']*100)/$totalTempArray['TAT_16']));
        $totalTempArray['TAT_16_per'] = sprintf("%01.2f",(($totalTempArray['TAT_16']*100)/$totalTempArray['TAT_16']));
        $totalTempArray['entity'] =  "Total";
        $totalTempArray['id'] =  "00";
        $totalArray[] = $totalTempArray;
        return $totalArray;
    }
        function get_TAT_days_total_pending_bookings($finalData){
        foreach($finalData as $values){
            if(!array_key_exists('TAT_0', $values)){
                $values['TAT_0'] = array();
            }
            if(!array_key_exists('TAT_1', $values)){
                $values['TAT_1'] = array();
            }
            if(!array_key_exists('TAT_2', $values)){
                $values['TAT_2'] = array();
            }
            if(!array_key_exists('TAT_3', $values)){
                $values['TAT_3'] = array();
            }
            if(!array_key_exists('TAT_4', $values)){
                $values['TAT_4'] = array();
            }
            if(!array_key_exists('TAT_5', $values)){
                $values['TAT_5'] = array();
            }
             if(!array_key_exists('TAT_8', $values)){
                $values['TAT_8'] = array();
            }
             if(!array_key_exists('TAT_16', $values)){
                $values['TAT_16'] = array();
            }
            $tTempArray['TAT_0_bookings'] = implode(",",$values['TAT_0']);
            $tTempArray['TAT_1_bookings'] = implode(",",$values['TAT_1']);
            $tTempArray['TAT_2_bookings'] = implode(",",$values['TAT_2']);
            $tTempArray['TAT_3_bookings'] = implode(",",$values['TAT_3']);
            $tTempArray['TAT_4_bookings'] = implode(",",$values['TAT_4']);
            $tTempArray['TAT_5_bookings'] = implode(",",$values['TAT_5']);
            $tTempArray['TAT_8_bookings'] = implode(",",$values['TAT_8']);
            $tTempArray['TAT_16_bookings'] = implode(",",$values['TAT_16']);
            $tTempArray["entity"] =  $values['entity_name'];
            $tTempArray['id'] =  $values['entity_id'];
            $totalArray[] = $tTempArray;
        }
        $total_0 = $total_1 = $total_2 = $total_3 = $total_4 = $total_5 = $total_8 = $total_16 = $total_pending = 0;
        foreach($totalArray as $pendingDetails){
            $tArray = array();
            $tArray['TAT_0'] = $tArray['TAT_1'] = $tArray['TAT_2'] = $tArray['TAT_3'] = $tArray['TAT_4'] = $tArray['TAT_5'] =$tArray['TAT_8'] = $tArray['TAT_16'] = 0;
            $tArray['entity'] = $pendingDetails['entity'];
            $tArray['id'] = $pendingDetails['id'];
            if(strlen($pendingDetails['TAT_0_bookings']) != 0){
                $tArray['TAT_0'] = count(explode(",",$pendingDetails['TAT_0_bookings']));
            }
            if(strlen($pendingDetails['TAT_1_bookings']) != 0){
                $tArray['TAT_1'] = count(explode(",",$pendingDetails['TAT_1_bookings']));
             }
             if(strlen($pendingDetails['TAT_2_bookings']) != 0){
                $tArray['TAT_2'] = count(explode(",",$pendingDetails['TAT_2_bookings']));
             }
            if(strlen($pendingDetails['TAT_3_bookings']) != 0){
                $tArray['TAT_3'] = count(explode(",",$pendingDetails['TAT_3_bookings']));
            }
            if(strlen($pendingDetails['TAT_4_bookings']) != 0){
                $tArray['TAT_4'] = count(explode(",",$pendingDetails['TAT_4_bookings']));
            }
            if(strlen($pendingDetails['TAT_5_bookings']) != 0){
                $tArray['TAT_5'] = count(explode(",",$pendingDetails['TAT_5_bookings']));
            }
            if(strlen($pendingDetails['TAT_8_bookings']) != 0){
                $tArray['TAT_8'] = count(explode(",",$pendingDetails['TAT_8_bookings']));
            }
            if(strlen($pendingDetails['TAT_16_bookings']) != 0){
                $tArray['TAT_16'] = count(explode(",",$pendingDetails['TAT_16_bookings']));
            }
            $tArray['TAT_0_bookings'] = $pendingDetails['TAT_0_bookings'];
            $tArray['TAT_1_bookings'] = $pendingDetails['TAT_1_bookings'];
            $tArray['TAT_2_bookings'] = $pendingDetails['TAT_2_bookings'];
            $tArray['TAT_3_bookings'] = $pendingDetails['TAT_3_bookings'];
            $tArray['TAT_4_bookings'] = $pendingDetails['TAT_4_bookings'];
            $tArray['TAT_5_bookings'] = $pendingDetails['TAT_5_bookings'];
            $tArray['TAT_8_bookings'] = $pendingDetails['TAT_8_bookings'];
            $tArray['TAT_16_bookings'] = $pendingDetails['TAT_16_bookings'];
            $tArray['TAT_16_bookings'] = $pendingDetails['TAT_16_bookings'];
            $tArray['Total_Pending'] =  $tArray['TAT_0'] + $tArray['TAT_1'] + $tArray['TAT_2'] + $tArray['TAT_3'] + $tArray['TAT_4'] + $tArray['TAT_5']+ $tArray['TAT_8'] + $tArray['TAT_16'];
            $total_0 = $total_0+$tArray['TAT_0'];
            $total_1 = $total_1+$tArray['TAT_1'];
            $total_2 = $total_2+$tArray['TAT_2'];
            $total_3 = $total_3+$tArray['TAT_3'];
            $total_4 = $total_4+$tArray['TAT_4'];
            $total_5 = $total_5+$tArray['TAT_5'];
            $total_8 = $total_8+$tArray['TAT_8'];
            $total_16 = $total_16+$tArray['TAT_16'];
            $total_pending = $total_pending+$tArray['Total_Pending'];
            $tArray['TAT_0_per'] = sprintf("%01.0f",(($tArray['TAT_0']*100)/$tArray['Total_Pending']));
            $tArray['TAT_1_per'] = sprintf("%01.0f",(($tArray['TAT_1']*100)/$tArray['Total_Pending']));
            $tArray['TAT_2_per'] = sprintf("%01.0f",(($tArray['TAT_2']*100)/$tArray['Total_Pending']));
            $tArray['TAT_3_per'] = sprintf("%01.0f",(($tArray['TAT_3']*100)/$tArray['Total_Pending']));
            $tArray['TAT_4_per'] = sprintf("%01.0f",(($tArray['TAT_4']*100)/$tArray['Total_Pending']));
            $tArray['TAT_5_per'] = sprintf("%01.0f",(($tArray['TAT_5']*100)/$tArray['Total_Pending']));
            $tArray['TAT_8_per'] = sprintf("%01.0f",(($tArray['TAT_8']*100)/$tArray['Total_Pending']));
            $tArray['TAT_16_per'] = sprintf("%01.0f",(($tArray['TAT_16']*100)/$tArray['Total_Pending']));
            $tArray['TAT_total_per'] = sprintf("%01.0f",(($tArray['Total_Pending']*100)/$tArray['Total_Pending']));
            $outputArray[] = $tArray;
        }
        $totalTempArray['entity'] = "Total";
        $totalTempArray['id'] = "00";
        $totalTempArray['TAT_0'] = $total_0;
        $totalTempArray['TAT_1'] = $total_1;
        $totalTempArray['TAT_2'] =  $total_2;
        $totalTempArray['TAT_3'] = $total_3;
        $totalTempArray['TAT_4'] = $total_4;
        $totalTempArray['TAT_5'] = $total_5;
        $totalTempArray['TAT_8'] = $total_8;
        $totalTempArray['TAT_16'] = $total_16;
        $totalTempArray['Total_Pending'] = $total_pending;
        $totalTempArray['TAT_0_per'] = sprintf("%01.0f",(($totalTempArray['TAT_0']*100)/$totalTempArray['Total_Pending']));
        $totalTempArray['TAT_1_per'] = sprintf("%01.0f",(($totalTempArray['TAT_1']*100)/$totalTempArray['Total_Pending']));
        $totalTempArray['TAT_2_per'] = sprintf("%01.0f",(($totalTempArray['TAT_2']*100)/$totalTempArray['Total_Pending']));
        $totalTempArray['TAT_3_per'] = sprintf("%01.0f",(($totalTempArray['TAT_3']*100)/$totalTempArray['Total_Pending']));
        $totalTempArray['TAT_4_per'] = sprintf("%01.0f",(($totalTempArray['TAT_4']*100)/$totalTempArray['Total_Pending']));
        $totalTempArray['TAT_5_per'] = sprintf("%01.0f",(($totalTempArray['TAT_5']*100)/$totalTempArray['Total_Pending']));
        $totalTempArray['TAT_8_per'] = sprintf("%01.0f",(($totalTempArray['TAT_8']*100)/$totalTempArray['Total_Pending']));
        $totalTempArray['TAT_16_per'] = sprintf("%01.0f",(($totalTempArray['TAT_16']*100)/$totalTempArray['Total_Pending']));
        $totalTempArray['TAT_total_per'] = sprintf("%01.0f",(($totalTempArray['Total_Pending']*100)/$totalTempArray['Total_Pending']));
        $outputArray[] = $totalTempArray;
        return $outputArray;
    }
    function get_tat_data_in_structured_format_pending($data){
        $finalArray = array();
        foreach($data as $tatData){
            if($tatData['TAT']<0){
                $finalArray[$tatData['entity']]['TAT_0'] = $tatData['booking_id'];
                $finalArray[$tatData['entity']]['entity_name'] = $tatData['entity'];
                $finalArray[$tatData['entity']]['entity_id'] = $tatData['id'];
            }
            else if($tatData['TAT']>=0 && $tatData['TAT']<5){
                $finalArray[$tatData['entity']]['TAT_'.$tatData['TAT']][] = $tatData['booking_id'];
                $finalArray[$tatData['entity']]['entity_name'] = $tatData['entity'];
                $finalArray[$tatData['entity']]['entity_id'] = $tatData['id'];
            }
            else if($tatData['TAT']>4 && $tatData['TAT']<8){
                $finalArray[$tatData['entity']]['TAT_5'][] = $tatData['booking_id'];
                $finalArray[$tatData['entity']]['entity_name'] = $tatData['entity'];
                $finalArray[$tatData['entity']]['entity_id'] = $tatData['id'];
            }
            else if($tatData['TAT']>7 && $tatData['TAT']<16){
                $finalArray[$tatData['entity']]['TAT_8'][] = $tatData['booking_id'];
                $finalArray[$tatData['entity']]['entity_name'] = $tatData['entity'];
                $finalArray[$tatData['entity']]['entity_id'] = $tatData['id'];
            }
            else{
                $finalArray[$tatData['entity']]['TAT_16'][] = $tatData['booking_id'];
                $finalArray[$tatData['entity']]['entity_name'] = $tatData['entity'];
                $finalArray[$tatData['entity']]['entity_id'] = $tatData['id'];
            }
        }
         $structuredArray = $this->get_TAT_days_total_pending_bookings(array_values($finalArray));
         return $structuredArray;   
    }
    function get_tat_data_in_structured_format_completed($data){
       $finalArray = array();
        foreach($data as $tatData){
            if($tatData['TAT']<0){
                $finalArray[$tatData['entity']]['TAT_0'][] = $tatData['booking_id'];
                $finalArray[$tatData['entity']]['entity_name'] = $tatData['entity'];
                $finalArray[$tatData['entity']]['entity_id'] = $tatData['id'];
                $finalArray[$tatData['entity']]['total_bookings'][] = $tatData['booking_id'];
            }
            else if($tatData['TAT']>=0 && $tatData['TAT']<5){
                $finalArray[$tatData['entity']]['TAT_'.$tatData['TAT']][] = $tatData['booking_id'];
                $finalArray[$tatData['entity']]['entity_name'] = $tatData['entity'];
                $finalArray[$tatData['entity']]['entity_id'] = $tatData['id'];
                $finalArray[$tatData['entity']]['total_bookings'][] = $tatData['booking_id'];
            }
            else if($tatData['TAT']>4 && $tatData['TAT']<8){
                $finalArray[$tatData['entity']]['TAT_5'][] = $tatData['booking_id'];
                $finalArray[$tatData['entity']]['entity_name'] = $tatData['entity'];
                $finalArray[$tatData['entity']]['entity_id'] = $tatData['id'];
                $finalArray[$tatData['entity']]['total_bookings'][] = $tatData['booking_id'];
            }
            else if($tatData['TAT']>7 && $tatData['TAT']<16){
                $finalArray[$tatData['entity']]['TAT_8'][] = $tatData['booking_id'];
                $finalArray[$tatData['entity']]['entity_name'] = $tatData['entity'];
                $finalArray[$tatData['entity']]['entity_id'] = $tatData['id'];
                $finalArray[$tatData['entity']]['total_bookings'][] = $tatData['booking_id'];
            }
            else{
                $finalArray[$tatData['entity']]['TAT_16'][] = $tatData['booking_id'];
                $finalArray[$tatData['entity']]['entity_name'] = $tatData['entity'];
                $finalArray[$tatData['entity']]['entity_id'] = $tatData['id'];
                $finalArray[$tatData['entity']]['total_bookings'][] = $tatData['booking_id'];
            }
        }
        $structuredArray = $this->get_TAT_days_total_completed_bookings(array_values($finalArray));
        return $structuredArray;
    }
    function get_tat_data_in_structured_format($data,$is_pending){
        $structuredArray = array();
        if($is_pending){
          $structuredArray =  $this->get_tat_data_in_structured_format_pending($data);
        }
        else{
           $structuredArray =  $this->get_tat_data_in_structured_format_completed($data);
        }
        return $structuredArray;
    }
    function get_commom_filters_for_pending_and_completed_tat($startDate,$endDate,$status,$service_id,$request_type,$free_paid,$upcountry ,$partner_id){
        $where = $joinType  = $join = $requestTypeArray = array();
        //Filter on service ID
        if($service_id !="not_set"){
                 $where['booking_details.service_id'] = $service_id;
            }
            //Filter on request Type
            if($request_type !="not_set"){
                $requestTypeArray = explode(':',$request_type);
                $join['spare_parts_details'] = "spare_parts_details.booking_id = booking_details.booking_id";
                $joinType['spare_parts_details']  = "left";
                foreach($requestTypeArray as $request_type){
                    if($request_type == 'Repair_with_part'){
                        $where['booking_details.request_type LIKE "%Repair%"'] = NULL;                        
                        $where['spare_parts_details.booking_id IS NOT NULL'] = NULL;
                    }
                    else if($request_type == 'Repair_without_part'){
                        $where['booking_details.request_type LIKE "%Repair%"'] = NULL;
                        $where['spare_parts_details.booking_id IS NULL'] = NULL;
                    }
                    else if($request_type == 'Installation'){
                        $where['booking_details.request_type NOT LIKE "%Repair%"'] = NULL;
                        $where['spare_parts_details.booking_id IS NULL'] = NULL;
                    }
                }
                $count = count($requestTypeArray);
                if(array_key_exists('booking_details.request_type NOT LIKE "%Repair%"', $where) && array_key_exists('booking_details.request_type LIKE "%Repair%"', $where)){
                    unset($where['booking_details.request_type NOT LIKE "%Repair%"']);
                    unset($where['booking_details.request_type LIKE "%Repair%"']);
                }
                if(array_key_exists('spare_parts_details.booking_id IS NULL', $where) && array_key_exists('spare_parts_details.booking_id IS NOT NULL', $where)){
                    unset($where['spare_parts_details.booking_id IS NULL']);
                    unset($where['spare_parts_details.booking_id IS NOT NULL']);
                }
                if($count == 2 && in_array("Installation",$requestTypeArray) &&  in_array("Repair_with_part",$requestTypeArray)){
                    $where['(spare_parts_details.booking_id IS NOT NULL AND booking_details.request_type LIKE "%Repair%") OR (spare_parts_details.booking_id IS NULL AND booking_details.request_type NOT LIKE "%Repair%")']= NULL;
                }
            }
            //Filter on free or paid
            if($free_paid !="not_set"){ 
                if($free_paid == "Yes"){
                   $where['amount_due'] = '0';
                }
                else{
                   $where['amount_due != 0'] = NULL;
                }
            }
            //Filter on upcountry
            if($upcountry !="not_set"){
                 $upcountryValue = 0;
                 if($upcountry == 'Yes'){
                     $upcountryValue = 1;
                 }
                $where['booking_details.is_upcountry'] = $upcountryValue;
            }
            //Filter on partner ID
            if($this->input->post('partner_id')){
                $where['booking_details.partner_id'] = $this->input->post('partner_id');
            }
            if($partner_id != "not_set"){
                $where['booking_details.partner_id'] = $partner_id;
            }
            if($this->session->userdata('partner_id')){
               $where['booking_details.partner_id'] = $this->session->userdata('partner_id');
            }
            return array("where"=>$where,"joinType"=>$joinType,"join"=>$join);
    }
    function get_tat_conditions_by_filter_for_completed($startDate,$endDate,$status,$service_id,$request_type,$free_paid,$upcountry,$partner_id = NULL){
            $conditionArray = $this->get_commom_filters_for_pending_and_completed_tat($startDate,$endDate,$status,$service_id,$request_type,$free_paid,$upcountry ,$partner_id);
            //Filter For date
            if($startDate && $endDate){
                $conditionArray['where']["(date(booking_details.service_center_closed_date) >= '".$startDate."' AND date(booking_details.service_center_closed_date) <= '".$endDate."') "] = NULL;
            }
            //Filter on status
            if($status !="not_set"){
                if($status == 'Completed'){
                     $conditionArray['where']['!(current_status = "Cancelled" OR internal_status ="InProcess_Cancelled")'] = NULL; 
                }
                else{
                    $conditionArray['where']['(current_status = "Cancelled" OR internal_status = "InProcess_Cancelled")'] = NULL; 
                }
            }
            //only is sf closed date is not null
            $conditionArray['where']['service_center_closed_date IS NOT NULL'] = NULL;
            //Group by on booking_tat
            $conditionArray['groupBy'] = array("booking_tat.booking_id");
            //Default join on booking_tat
            $conditionArray['join']['booking_tat'] = "booking_tat.booking_id = booking_details.booking_id";
            return $conditionArray;
        }
        function get_tat_conditions_by_filter_for_pending($startDate,$endDate,$status,$service_id,$request_type,$free_paid,$upcountry,$partner_id = NULL){
            $conditionArray = $this->get_commom_filters_for_pending_and_completed_tat($startDate,$endDate,$status,$service_id,$request_type,$free_paid,$upcountry ,$partner_id);
            //Filter For date
            if($startDate && $endDate){
                $conditionArray['where']["((STR_TO_DATE(booking_details.initial_booking_date, '%d-%m-%Y')) >= '".$startDate."' AND (STR_TO_DATE(booking_details.initial_booking_date, '%d-%m-%Y')) <= '".$endDate."') "] = NULL;
            }
            $conditionArray['where']['!(internal_status = "InProcess_Cancelled" OR internal_status ="InProcess_completed")'] = NULL; 
            $conditionArray['where_in']['booking_details.current_status'] = array(_247AROUND_PENDING,_247AROUND_RESCHEDULED); 
            //Filter on status
            if($status !="not_set"){
                $conditionArray['where']['booking_details.actor'] = $status; 
            }
            $conditionArray['where']['booking_details.type != "Query"'] = NULL; 
             //Group by on booking_tat
            $conditionArray['groupBy'] = array("TAT","entity");
            //only is sf closed date is null
            $conditionArray['where']['service_center_closed_date IS NULL'] = NULL;
            return $conditionArray;
        }
        function get_booking_tat_report($startDate,$endDate,$status="not_set",$service_id="not_set",$request_type="not_set",$free_paid="not_set",$upcountry ="not_set",$for = "RM",$is_pending = FALSE,$partner_id = NULL){
        if($is_pending){
            $conditionsArray  = $this->get_tat_conditions_by_filter_for_pending($startDate,$endDate,$status,$service_id,$request_type,$free_paid,$upcountry,$partner_id);
            $startDateField = "CURRENT_TIMESTAMP";
        }
        else{
            $conditionsArray  = $this->get_tat_conditions_by_filter_for_completed($startDate,$endDate,$status,$service_id,$request_type,$free_paid,$upcountry,$partner_id);
            $startDateField = "service_center_closed_date";
        }
        $finalData = $data = array();
        if($for == "AM"){
           // $select = "employee.full_name as entity,partners.account_manager_id as id,booking_tat.booking_id,MAX(IFNULL(leg_1,'0')+IFNULL(leg_2,'0')) as TAT";
             $select = "employee.full_name as entity,partners.account_manager_id as id,  booking_details.booking_id as booking_id,"
                     . "DATEDIFF(".$startDateField." , STR_TO_DATE(booking_details.initial_booking_date, '%d-%m-%Y')) as TAT";
             if($is_pending){
                    $select = "employee.full_name as entity,partners.account_manager_id as id,GROUP_CONCAT(DISTINCT booking_details.booking_id) as booking_id,COUNT( DISTINCT booking_details.booking_id) as count,"
                            . "DATEDIFF(".$startDateField." , STR_TO_DATE(booking_details.initial_booking_date, '%d-%m-%Y')) as TAT";
                }
            $conditionsArray['join']['partners'] = "booking_details.partner_id = partners.id";
            $conditionsArray['join']['employee'] = "partners.account_manager_id = employee.id";
            $conditionsArray['where']['partners.is_active'] = 1;
            $data = $this->reusable_model->get_search_result_data("booking_details",$select,$conditionsArray['where'],$conditionsArray['join'],NULL,NULL,NULL,$conditionsArray['joinType'],$conditionsArray['groupBy']);
        }
        else if($for == "RM"){
            if($this->session->userdata('partner_id') ){
               //$select = "employee_relation.region as entity,employee_relation.agent_id as id,booking_tat.booking_id,MAX(IFNULL(leg_1,'0')+IFNULL(leg_2,'0')) as TAT";
                $select = "employee_relation.region as entity,employee_relation.agent_id as id,booking_details.booking_id,DATEDIFF(".$startDateField." , STR_TO_DATE(booking_details.initial_booking_date, '%d-%m-%Y')) as TAT";
                if($is_pending){
                    $select = "employee_relation.region as entity,employee_relation.agent_id as id,GROUP_CONCAT(DISTINCT booking_details.booking_id) as booking_id,COUNT(DISTINCT booking_details.booking_id) as count,"
                            . "DATEDIFF(".$startDateField." , STR_TO_DATE(booking_details.initial_booking_date, '%d-%m-%Y')) as TAT";
                }
            }
            else{
                //$select = "employee.full_name as entity,employee_relation.agent_id as id,booking_tat.booking_id,MAX(IFNULL(leg_1,'0')+IFNULL(leg_2,'0')) as TAT";
                $select = "employee.full_name as entity,employee_relation.agent_id as id,booking_details.booking_id,DATEDIFF(".$startDateField." , STR_TO_DATE(booking_details.initial_booking_date, '%d-%m-%Y')) as TAT";
                if($is_pending){
                    $select = "employee.full_name as entity,employee_relation.agent_id as id,GROUP_CONCAT(DISTINCT booking_details.booking_id) as booking_id,COUNT(DISTINCT booking_details.booking_id) as count,"
                            . "DATEDIFF(".$startDateField." , STR_TO_DATE(booking_details.initial_booking_date, '%d-%m-%Y')) as TAT";
                }
            }
            $conditionsArray['join']['employee_relation'] = "FIND_IN_SET(booking_details.assigned_vendor_id,employee_relation.service_centres_id)";
            $conditionsArray['join']['employee'] = "employee_relation.agent_id = employee.id";
            $data = $this->reusable_model->get_search_result_data("booking_details",$select,$conditionsArray['where'],$conditionsArray['join'],NULL,NULL,NULL,$conditionsArray['joinType'],$conditionsArray['groupBy']);
        }
        if(!empty($data)){
            $finalData = $this->get_tat_data_in_structured_format($data,$is_pending);  
        }
        echo json_encode($finalData);
    }
    
   function get_data_for_sf_tat_filters($conditionsArray,$rmID,$is_am,$is_pending){
        if($is_pending){
            $sfSelect = "CONCAT(service_centres.district,'_',service_centres.id) as id,service_centres.name as entity,GROUP_CONCAT(DISTINCT booking_details.booking_id) as booking_id,COUNT(DISTINCT booking_details.booking_id) as booking_count"
                    . ",DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.initial_booking_date, '%d-%m-%Y')) AS TAT";
        }
        else{
                    $sfSelect = "CONCAT(service_centres.district,'_',service_centres.id) as id,service_centres.name as entity,booking_tat.booking_id,DATEDIFF(service_center_closed_date , STR_TO_DATE(booking_details.initial_booking_date, '%d-%m-%Y')) AS TAT";
        }
        $sfData = array();
        //$sfSelect = "CONCAT(service_centres.district,'_',service_centres.id) as id,service_centres.name as entity,booking_tat.booking_id,MAX(IFNULL(leg_1,'0')+IFNULL(leg_2,'0')) AS TAT";
        if($is_am == 0){
            if($this->input->post('vendor_id')){
                $conditionsArray['where']['assigned_vendor_id'] = $this->input->post('vendor_id');
            }
            if($rmID != "00"){
                $conditionsArray['where']["employee_relation.agent_id"] = $rmID;    
            }
            $conditionsArray['join']['service_centres'] = "service_centres.id = booking_details.assigned_vendor_id";
            $conditionsArray['join']['employee_relation'] = "FIND_IN_SET(booking_details.assigned_vendor_id,employee_relation.service_centres_id)";
            $conditionsArray['join']['employee'] = "employee_relation.agent_id = employee.id";
        }
        else{
            if($rmID != "00"){
              $conditionsArray['where']["partners.account_manager_id"] = $rmID;
            }
            $conditionsArray['join']['service_centres'] = "service_centres.id = booking_details.assigned_vendor_id";
            $conditionsArray['join']['partners'] = "partners.id = booking_details.partner_id";
            $conditionsArray['join']['employee'] = "partners.account_manager_id = employee.id";
        }
        $sfRawData = $this->reusable_model->get_search_result_data("booking_details",$sfSelect,$conditionsArray['where'],$conditionsArray['join'],NULL,NULL,NULL,$conditionsArray['joinType'],$conditionsArray['groupBy']);
        if(!empty($sfRawData)){
            $sfDataTemp= $this->get_tat_data_in_structured_format($sfRawData,$is_pending);
            $sfData = $this->miscelleneous->multi_array_sort_by_key($sfDataTemp, 'TAT_2', SORT_ASC);
        }
        return $sfData;
    }
    function get_data_for_state_tat_filters($conditionsArray,$rmID,$is_am,$is_pending){
        if($is_pending){
            $stateSelect = "booking_details.State as id,(CASE WHEN booking_details.State = '' THEN 'Unknown' ELSE booking_details.State END ) as entity,"
                . "GROUP_CONCAT( DISTINCT booking_details.booking_id) as booking_id , COUNT(DISTINCT booking_details.booking_id) as booking_count,"
                    . "DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.initial_booking_date, '%d-%m-%Y')) AS TAT";
        }
        else{
            $stateSelect = "booking_details.State as id,(CASE WHEN booking_details.State = '' THEN 'Unknown' ELSE booking_details.State END ) as entity,"
                . "booking_details.booking_id,DATEDIFF(service_center_closed_date , STR_TO_DATE(booking_details.initial_booking_date, '%d-%m-%Y')) AS TAT";
        }
        $stateData = array();
        //$stateSelect = "booking_details.State as id,(CASE WHEN booking_details.State = '' THEN 'Unknown' ELSE booking_details.State END ) as entity,"
            //    . "booking_tat.booking_id,MAX(IFNULL(leg_1,'0')+IFNULL(leg_2,'0')) AS TAT";
        if($is_am == 0){
            if($rmID != "00"){
                $conditionsArray['where']["employee_relation.agent_id"] = $rmID;    
            }
            $conditionsArray['join']['employee_relation'] = "FIND_IN_SET(booking_details.assigned_vendor_id,employee_relation.service_centres_id)";
            $conditionsArray['join']['employee'] = "employee_relation.agent_id = employee.id";
        }
        else{
            if($rmID != "00"){
                $conditionsArray['where']["partners.account_manager_id"] = $rmID;
            }
            $conditionsArray['join']['partners'] = "partners.id = booking_details.partner_id";
            $conditionsArray['join']['employee'] = "partners.account_manager_id = employee.id";
        }
        //Get Data Group by State
        $stateRawData = $this->reusable_model->get_search_result_data("booking_details",$stateSelect,$conditionsArray['where'],$conditionsArray['join'],NULL,NULL,NULL,$conditionsArray['joinType'],$conditionsArray['groupBy']);
        if(!empty($stateRawData)){
            $stateDataTemp = $this->get_tat_data_in_structured_format($stateRawData,$is_pending);
            $stateData = $this->miscelleneous->multi_array_sort_by_key($stateDataTemp, 'TAT_2', SORT_ASC);
        }
        return $stateData;
    }
    function tat_calculation_full_view($rmID,$is_ajax=0,$is_am=0,$is_pending = FALSE){
        echo "<pre>";
        print_r($this->input->post());
        exit();
        $endDate = date("Y-m-d");
        $startDate =  date('Y-m-d', strtotime('-30 days'));
        $partner_id = $status =  "not_set";
        $request_type = 'Installation';
        $upcountry = 'No';
        if(!$is_pending){
            $status = 'Completed';
        }
        $service_id  = $free_paid = 'not_set';
        if($this->input->post('daterange_completed_bookings')){
            $dateArray = explode(" - ",$this->input->post('daterange_completed_bookings')); 
            $startDate = $dateArray[0];
            $endDate = $dateArray[1];
        }
        if($this->input->post('status')){
            $status = $this->input->post('status');
        }
        if($this->input->post('services')){
            $service_id = $this->input->post('services');
        }
        if($this->input->post('request_type')){
            if(is_array($this->input->post('request_type'))){
                $request_type = implode(":",$this->input->post('request_type'));
            }
            else{
                $request_type = $this->input->post('request_type');
            }
        }
        if($this->input->post('partner_id')){
            $partner_id = $this->input->post('partner_id');
        }
        if($this->input->post('upcountry')){
            $upcountry = $this->input->post('upcountry');
        }
        if($this->input->post('free_paid')){
            $free_paid = $this->input->post('free_paid');
        }
        if($is_pending){
            $conditionsArray  = $this->get_tat_conditions_by_filter_for_pending($startDate,$endDate,$status,$service_id,$request_type,$free_paid,$upcountry,$partner_id);
        }
        else{
            $conditionsArray  = $this->get_tat_conditions_by_filter_for_completed($startDate,$endDate,$status,$service_id,$request_type,$free_paid,$upcountry,$partner_id);
        }
        //Get Data Group BY State
       if(!$is_ajax){
            $stateData = $this->get_data_for_state_tat_filters($conditionsArray,$rmID,$is_am,$is_pending);
        }
        //Get Data Group BY SF
        $sfData = $this->get_data_for_sf_tat_filters($conditionsArray,$rmID,$is_am,$is_pending);
        if($is_am){
            if($rmID != "00"){
                $partnerWhere['account_manager_id'] = $rmID;
            }
        }
        $partnerWhere['is_active'] = 1;
        $serviceWhere['isBookingActive'] =1;
        $partners = $this->partner_model->getpartner_details('partners.id,partners.public_name',$partnerWhere);
        $services = $this->reusable_model->get_search_result_data("services","*",$serviceWhere,NULL,NULL,NULL,NULL,NULL,array());
        if(!$is_ajax){
            if($this->session->userdata('userType') == 'employee'){
                $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
            }
            else if($this->session->userdata('userType') == 'partner'){
                $this->miscelleneous->load_partner_nav_header();
            }
            $service_center_state = $this->reusable_model->get_search_result_data("service_centres","id,state",NULL,NULL,NULL,NULL,NULL,NULL,array());
            foreach($service_center_state as $sfState){
                $sfStateArray["sf_".$sfState['id']] = $sfState['state'];
            }
            if(!$this->input->post()){
                $_POST['status'] = $status;
                $_POST['service_id'] = $service_id;
                $_POST['upcountry'] = $upcountry;
                $_POST['request_type'][] = $request_type;
            }
            $this->load->view('dashboard/tat_calculation_full_view',array('state' => $stateData,'sf'=>$sfData,'partners'=>$partners,'rmID'=>$rmID,'filters'=>$this->input->post(),'services'=>$services,
                "is_am"=>$is_am,'sf_state'=>$sfStateArray,"is_pending" => $is_pending));
            $this->load->view('dashboard/dashboard_footer');   
        }
        else{
           echo  json_encode($sfData);
        }
    }
    
    function download_tat_report(){
        $data = json_decode($this->input->post('data'),true);
        $csv ="";
        foreach($data as $values){
            $tempArray = array();
            $tempArray[] = $values['entity'];
            $tempArray[] = $values['TAT_0'];
            $tempArray[] = $values['TAT_0_per'];
            $tempArray[] = $values['TAT_1'];
            $tempArray[] = $values['TAT_1_per'];
            $tempArray[] = $values['TAT_2'];
            $tempArray[] = $values['TAT_2_per'];
            $tempArray[] = $values['TAT_3'];
            $tempArray[] = $values['TAT_3_per'];
            $tempArray[] = $values['TAT_4'];
            $tempArray[] = $values['TAT_4_per'];
            $tempArray[] = $values['TAT_5'];
            $tempArray[] = $values['TAT_5_per'];
            $tempArray[] = $values['TAT_8'];
            $tempArray[] = $values['TAT_8_per'];
            $tempArray[] = $values['TAT_16'];
            $tempArray[] = $values['TAT_16_per'];
            $csv.=implode(",",$tempArray)."\n"; //Append data to csv
        }
        if(array_key_exists("SF", $values)){
                $headings[] = "SF";
            }
            $headings[] = "State";
             $headings[] = "TAT_0";
            $headings[] = "TAT_0_percentage";
            $headings[] = "TAT_1";
            $headings[] = "TAT_1_percentage";
            $headings[] = "TAT_2";
            $headings[] = "TAT_2_percentage";
            $headings[] = "TAT_3";
            $headings[] = "TAT_3_percentage";
            $headings[] = "TAT_4";
            $headings[] = "TAT_4_percentage";
            $headings[] = "TAT_5";
            $headings[] = "TAT_5_percentage";
            $headings[] = "TAT_8";
            $headings[] = "TAT_8_percentage";
            $headings[] = "Total";
            $headings[] = "Total_percentage";
            $finalcsv = implode(",",$headings)." \n".$csv;//Column headers
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . "Tat_Report.csv");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            echo $finalcsv;
            exit;
    }
    
    /**
     * @desc: This function is used to get inventory dashboard title data
     * @param void
     * @return void
     */
    function execute_inventory_title_query(){
        $data['inventory_details'] = $this->dashboard_model->get_inventory_header_count_data();
        $post['where'] = "spare_parts_details.partner_id = '" . 10 . "' AND  entity_type =  '"._247AROUND_SF_STRING."' AND status = '" . SPARE_PARTS_REQUESTED . "' "
                . " AND booking_details.current_status IN ('Pending', 'Rescheduled') ";
        
        $data['shipped_spare_by_wh_to_sf'] = $this->inventory_model->count_spare_parts($post);
        
        $post['where'] =  "spare_parts_details.partner_id = '" . 10 . "' AND spare_parts_details.entity_type = '"._247AROUND_SF_STRING."'"
                . " AND approved_defective_parts_by_partner = '1' AND status = '"._247AROUND_COMPLETED."'";
        
        $data['shipped_spare_by_wh_to_partner'] = $this->inventory_model->count_spare_parts($post);
        
        $brackets_details = $this->dashboard_model->get_sf_has_zero_stock_data();
        $data['brackets_count'] = count($brackets_details);
        $this->load->view('dashboard/inventory_dashboard_title', $data);
    }
    
    /**
     * @desc: This function is used to get partner total and oot of tat spare details
     * @param void
     * @return json
     */
    function get_oot_spare_parts_count_by_partner(){
        $data = $this->dashboard_model->get_oot_spare_parts_count_by_partner();
        $partners_id = [];
        $partners_name = [];
        $oot_count = [];
        $oot_amount = [];
        foreach( $data as $spare){
            $partners_id[$spare['public_name']] = $spare['partner_id'];
            array_push($partners_name, $spare['public_name']);
            array_push($oot_count, $spare['spare_count']);
            $oot_amount[$spare['public_name']] = intval($spare['spare_amount']);
        }
        $json_data['partner_id'] = $partners_id;
        $json_data['partner_name'] = implode(",", $partners_name);
        $json_data['spare_count'] = implode(",", $oot_count);
        $json_data['spare_amount'] = $oot_amount;

        echo json_encode($json_data);
    }
    
    /**
     * @desc: This function is used to show partner specific dashboard for spare parts
     * @param string $partner_name
     * @param string $partner_id
     * @return view
     */
    function partner_specific_spare_parts_dashboard($partner_name = "", $partner_id = "") {

        $data['partner_name'] = urldecode($partner_name);
        $data['partner_id'] = urldecode($partner_id);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('dashboard/partner_specific_spare_parts_dashboard', $data);
        $this->load->view('dashboard/dashboard_footer');
    }
    
    /**
     * @desc: This function is used to get spare details as per partner
     * @param void
     * @return json
     */
    function get_partner_specific_count_by_status(){
        $partner_id = $this->input->post('partner_id');
        $spare_details = $this->dashboard_model->get_spare_parts_count_group_by_status($partner_id);
        $json_data = array();
        foreach ($spare_details as $value){ 
            if($value['status'] === SPARE_PARTS_REQUESTED){
                $tmp_arr = array('name' => $value['status'],
                               'y' => intval($value['count']),
                               'drilldown' => true
                );
                array_push($json_data, $tmp_arr);
            }else if($value['status'] === DEFECTIVE_PARTS_SHIPPED){
                $tmp_arr = array('name' => $value['status'],
                               'y' => intval($value['count']),
                               'drilldown' => true
                );
                array_push($json_data, $tmp_arr);
            }else if($value['status'] === DEFECTIVE_PARTS_PENDING){
                $tmp_arr = array('name' => $value['status'],
                               'y' => intval($value['count']),
                               'drilldown' => true
                );
                array_push($json_data, $tmp_arr);
            }else if($value['status'] === SPARE_OOW_EST_REQUESTED){
                $tmp_arr = array('name' => $value['status'],
                               'y' => intval($value['count']),
                               'drilldown' => true
                );
                array_push($json_data, $tmp_arr);
            }else if($value['status'] === DEFECTIVE_PARTS_REJECTED){
                $tmp_arr = array('name' => $value['status'],
                               'y' => intval($value['count']),
                               'drilldown' => true
                );
                array_push($json_data, $tmp_arr);
            }
            
        }
        echo json_encode($json_data);
    }
    
    /**
     * @desc: This function is used to get partner sf  spare parts details
     * @param void
     * @return json
     */
    function get_spare_details_by_sf(){
        $is_show_all = $this->input->post('is_show_all');
        $partner_id = $this->input->post('partner_id');
        $data = $this->dashboard_model->get_spare_details_count_group_by_sf($is_show_all,$partner_id);
        $json_data = array();
        if(!empty($data)){
            $json_data = $data;
        }
        echo json_encode($json_data);
    }
    
    /**
     * @desc: This function is used to get sf  spare parts details
     * @param void
     * @return void
     */
    function sf_oot_spare_full_view($partner_id = NULL,$partner_name = NULL){
        
        $data['partner_id'] = urldecode($partner_id);
        $data['partner_name'] = urldecode($partner_name);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('dashboard/sf_oot_spare_full_view', $data);
        $this->load->view('dashboard/dashboard_footer');
    }
    
    
    /**
     * @desc: This function is used to get spare details for specific partner
     * (This function will return total spare (except requested,cancelled,completed), 
     * partner out of tat(30 days after parts shipped by partner), 
     * sf out of tat( 7 days after booking completion by sf))
     * @param void
     * @return json
     */
    function get_partner_spare_snapshot(){
        if($this->session->userdata('partner_id')){
            $partner_id = $this->session->userdata('partner_id');
             $data = $this->dashboard_model->get_partner_spare_snapshot($partner_id,0);
        }
        else{
            $partner_id = $this->input->post('partner_id');
             $data = $this->dashboard_model->get_partner_spare_snapshot($partner_id);
        }
        
        $status = array();
        $spare_count = array();
        $spare_amount = array();
        
        foreach ($data as $key => $value){
            array_push($status, $value[0]['spare_status']);
            array_push($spare_count, intval($value[0]['spare_count']));
            array_push($spare_amount, $value[0]['spare_amount']);
            $spare_amount[$value[0]['spare_status']] = intval($value[0]['spare_amount']);
        }
        $json_data['status'] = implode(',', $status);
        $json_data['spare_count'] = implode(',', $spare_count);
        $json_data['spare_amount'] = $spare_amount;
        
        echo json_encode($json_data);
        
    }
    
    function get_upcountry_data(){
        $data = $this->upcountry_model->get_waiting_for_approval_upcountry_charges("",0,0, array('upcountry_distance > "'.UPCOUNTRY_OVER_LIMIT_DISTANCE.'" ' => NULL));
         $template = array(
        'table_open' => '<table  '
            . ' class="table table-striped table-bordered jambo_table bulk_action">'
        );
        $this->table->set_template($template);
        $this->table->set_heading(array('State','Call Type', 'Age', 'Upcountry Distance'));
        foreach($data as $value){
            $age_requested = date_diff(date_create($value['upcountry_update_date']), date_create('today'));
            $this->table->add_row( $value['state'], $value['request_type'], $age_requested->days. " Days", $value['upcountry_distance']); 
        }
        echo $this->table->generate();
    }
}