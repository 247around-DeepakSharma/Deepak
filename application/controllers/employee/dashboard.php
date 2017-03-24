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
        $data['partner_data'] = $this->reporting_utils->get_partners_booking_report_chart_data($startDate, $endDate, $bookingStatus);
        $partner_name = [];
        $booking_count = [];
        foreach ($data['partner_data'] as $value) {
            array_push($partner_name,(string)$value['public_name']);
            array_push($booking_count,$value['count']);
        }
        $data_report['partner_name'] = json_encode($partner_name);
        $data_report['completed_booking'] = implode(",", $booking_count);
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
        $data_report['foc_and_paid'] = $this->dashboard_model->get_paid_foc_booking_count($startDate,$endDate);
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
        $startDate = date('Y-m-d 00:00:00', strtotime($sDate));
        $endDate = date('Y-m-d 23:59:59', strtotime($eDate));
        $count= $this->dashboard_model->get_paid_foc_booking_count($startDate,$endDate);
        $data['foc'] = $count[0]['FOC'];
        $data['paid'] = $count[0]['Paid'];
        echo json_encode($data);
    }

}
