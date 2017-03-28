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
        $this->load->model('dashboard_model');

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

        $data_report['query'] = $this->vendor_model->get_around_dashboard_queries();
        $data_report['data'] = $this->vendor_model->execute_around_dashboard_query($data_report['query']);

        $timestamp = strtotime(date("Y-m-d"));
        $startDate = date('Y-m-01 00:00:00', $timestamp);
        $endDate = date('Y-m-d 23:59:59', $timestamp);
        $bookingStatus = 'Completed';
        $data_report['partner_data'] = $this->reporting_utils->get_partners_booking_report_chart_data($startDate, $endDate, $bookingStatus);
        $flag = "";
        $data['data'] = $this->reporting_utils->get_agent_daily_reports($flag,$startDate,$endDate);
        $agent_name = [];
        $query_cancel = [];
        $query_booking = [];
        $calls_placed = [];
        $calls_received = [];
        foreach($data['data'] as $key => $value){
            array_push($agent_name,$value['employee_id']);
            array_push($query_cancel,$value['followup_to_cancel']);
            array_push($query_booking,$value['followup_to_pending']);
            array_push($calls_placed,$value['calls_placed']);
            array_push($calls_received,$value['calls_recevied']);
        }    
        $data_report['agent_name'] = json_encode($agent_name);
        $data_report['query_cancel'] = implode(",", $query_cancel);
        $data_report['query_booking'] = implode(",", $query_booking);
        $data_report['calls_placed'] = implode(",", $calls_placed);
        $data_report['calls_received'] = implode(",", $calls_received);
        $data_report['foc_and_paid'] = $this->dashboard_model->get_paid_foc_booking_count($startDate,$endDate,$bookingStatus);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('dashboard/main_dashboard', $data_report);
        $this->load->view('dashboard/dashboard_footer');
    }
    
    /**
     * @desc: get paid or foc booking count on ajax call
     * @param void
     * @return json
     */
    function get_paid_foc_count_ajax(){
        $sDate = $this->input->post('sDate');
        $eDate = $this->input->post('eDate');
        $current_status = $this->input->post('current_status');
        $partner_id="";
        if($this->input->post('partner_id')){
            $partner_id = $this->input->post('partner_id');
        }
        $startDate = date('Y-m-d 00:00:00', strtotime($sDate));
        $endDate = date('Y-m-d 23:59:59', strtotime($eDate));
        $count= $this->dashboard_model->get_paid_foc_booking_count($startDate,$endDate,$current_status,$partner_id);
        $data['foc'] = $count[0]['FOC'];
        $data['paid'] = $count[0]['Paid'];
        echo json_encode($data);
    }
    
    function get_total_foc_or_paid_booking(){
        $sDate = $this->input->post('sDate');
        $eDate = $this->input->post('eDate');
        $type =  $this->input->post('type');
        $current_status = $this->input->post('current_status');
        $startDate = date('Y-m-d 00:00:00', strtotime($sDate));
        $endDate = date('Y-m-d 23:59:59', strtotime($eDate));
        $data= $this->dashboard_model->get_total_foc_or_paid_booking($startDate,$endDate,$type,$current_status);
        $partner_name = [];
        $count = [];
        foreach($data as $key => $value){
            array_push($partner_name,$value['public_name']);
            array_push($count,$value['count']);
        }
        $json_data['partner_name'] = implode(",", $partner_name);
        $json_data['count'] = implode(",", $count);
        echo json_encode($json_data);
        //print_r($data);
    }
    
    function get_partner_booking_based_on_services(){
        $sDate = $this->input->post('sDate');
        $eDate = $this->input->post('eDate');
        $partner_id =  $this->input->post('partner_id');
        $current_status = $this->input->post('current_status');
        $startDate = date('Y-m-d 00:00:00', strtotime($sDate));
        $endDate = date('Y-m-d 23:59:59', strtotime($eDate));
        $data= $this->dashboard_model->get_partner_booking_based_on_services($startDate,$endDate,$current_status,$partner_id);
        echo json_encode($data);
    }
    
    function partner_reports($partner_name="",$partner_id=""){
        $data['partner_name'] = $partner_name;
        $data['partner_id'] = $partner_id;
        $timestamp = strtotime(date("Y-m-d"));
        $startDate = date('Y-m-01 00:00:00', $timestamp);
        $endDate = date('Y-m-d 23:59:59', $timestamp);
        $data['booking']= $this->dashboard_model->get_partner_bookings_data($startDate,$endDate,$partner_id);
        $count= $this->dashboard_model->get_paid_foc_booking_count($startDate,$endDate,'',$partner_id);
        $data['foc'] = $count[0]['FOC'];
        $data['paid'] = $count[0]['Paid'];
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('dashboard/partner_specific_dashboard',$data);
        $this->load->view('dashboard/dashboard_footer');
    }
    
    function get_partner_booking_ajax_data(){
        $sDate = $this->input->post('sDate');
        $eDate = $this->input->post('eDate');
        $partner_id =  $this->input->post('partner_id');
        $startDate = date('Y-m-d 00:00:00', strtotime($sDate));
        $endDate = date('Y-m-d 23:59:59', strtotime($eDate));
        $bookings= $this->dashboard_model->get_partner_bookings_data($startDate,$endDate,$partner_id);
        $data['Completed'] = $bookings[0]['Completed'];
        $data['Cancelled'] = $bookings[0]['Cancelled'];
        $data['Pending'] = $bookings[0]['Pending'];
        $data['Rescheduled'] = $bookings[0]['Rescheduled'];
        $data['FollowUp'] = $bookings[0]['FollowUp'];

        echo json_encode($data);
    }
    
    function get_paid_or_foc_booking_groupby_services(){
        $sDate = $this->input->post('sDate');
        $eDate = $this->input->post('eDate');
        $type =  $this->input->post('type');
        $partner_id =  $this->input->post('partner_id');
        $startDate = date('Y-m-d 00:00:00', strtotime($sDate));
        $endDate = date('Y-m-d 23:59:59', strtotime($eDate));
        $data = $this->dashboard_model->get_paid_or_foc_booking_groupby_services($startDate,$endDate,$type,'',$partner_id);
        $services = [];
        $count = [];
        foreach($data as $key => $value){
            array_push($services,$value['services']);
            array_push($count,$value['total']);
        }
        $json_data['services'] = implode(",", $services);
        $json_data['count'] = implode(",", $count);
        echo json_encode($json_data);
    }

}
