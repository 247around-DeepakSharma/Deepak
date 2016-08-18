<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Service_centers extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();
        $this->load->model('service_centers_model');
        $this->load->model('booking_model');
        $this->load->model('vendor_model');
        $this->load->library("pagination");
        $this->load->library("session");
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
    }

    /**
     * @desc: This is used to load vendor Login Page
     *
     * @param: void
     * @return: void
     */
    function index() {
        $this->load->view('service_centers/service_center_login');
    }

    /**
     * @desc: This is used to login
     *
     * If user name and password matches allowed to login, else error message appears.
     *
     * @param: void
     * @return: void
     */
    function service_center_login() {
        $data['user_name'] = $this->input->post('user_name');
        $data['password'] = md5($this->input->post('password'));
        $service_center_id = $this->service_centers_model->service_center_login($data);

        if ($service_center_id) {
	    //get sc details now
	    $sc_details = $this->vendor_model->getVendorContact($service_center_id);
	    $this->setSession($sc_details[0]['id'], $sc_details[0]['name']);

	    redirect(base_url() . "service_center/pending_booking");
        } else {
            $userSession = array('error' => 'Please enter correct user name and password' );
            $this->session->set_userdata($userSession);
            redirect(base_url() . "service_center");
        }
    }

    /**
     * @desc: this is used to load pending booking
     * @param: Offset and page no.
     * @return: void
     */
    function pending_booking($offset = 0, $page = 0) {
	$this->checkUserSession();
	if ($page == 0) {
	    $page = 50;
	}

        $service_center_id = $this->session->userdata('service_center_id');

        $offset = ($this->uri->segment(3) != '' ? $this->uri->segment(3) : 0);
        $config['base_url'] = base_url() . 'service_centers/pending_booking';
        $config['total_rows'] = $this->service_centers_model->getPending_booking("count","",$service_center_id);

        $config['per_page'] = $page;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['bookings'] = $this->service_centers_model->getPending_booking($config['per_page'], $offset, $service_center_id);

        if ($this->session->flashdata('result') != '')
            $data['success'] = $this->session->flashdata('result');

        $this->load->view('service_centers/header');
        $this->load->view('service_centers/pending_booking', $data);
    }

    /**
     * @desc: this is used to get booking details by using booking ID And load booking booking details view
     * @param: booking id
     * @return: void
     */
    function booking_details($booking_id) {
        $this->checkUserSession();
        $data['query1'] = $this->booking_model->booking_history_by_booking_id($booking_id);
        $data['query2'] = $this->booking_model->get_unit_details($booking_id);
        $data['query4'] = $this->booking_model->getdescription_about_booking($booking_id);
        $data['query3'] = $this->booking_model->getbooking_charges($booking_id);

        $this->load->view('service_centers/header');
        $this->load->view('service_centers/booking_details', $data);
    }

    /**
     * @desc: This is used to get complete booking form.
     * @param: booking id
     * @return: void
     */
    function complete_booking_form($booking_id) {
        $this->checkUserSession();
        $data['query2'] = $this->booking_model->get_unit_details($booking_id);
        $data['booking'] = $this->booking_model->booking_history_by_booking_id($booking_id);
        $data['booking_id'] = $booking_id;
        $data['charges'] = $this->booking_model->getbooking_charges($booking_id);
        $data['internal_status'] = $this->booking_model->get_internal_status("Complete");

        $this->load->view('service_centers/header');
        $this->load->view('service_centers/complete_booking_form', $data);
    }

    /**
     * @desc: This is used to complete the booking once all the required details are filled.
     * @param: booking id
     * @return: void
     */
    function process_complete_booking($booking_id) {
	log_message('info', __METHOD__ . '=> ' . $booking_id);

	$this->checkUserSession();
        $data['booking_id'] = $booking_id;
	$data['serial_number'] = $this->input->post('serial_number');
	$data['service_charge'] = $this->input->post('service_charge');
        $data['service_center_id'] = $this->session->userdata('service_center_id');
        $data['additional_service_charge'] = $this->input->post('additional_service_charge');
        $data['internal_status'] = $this->input->post('internal_status');
        $data['parts_cost'] = $this->input->post('parts_cost');
        $data['amount_paid'] = $this->input->post('total_charge');
        $closing_remarks = $this->input->post('closing_remarks');
        $data['current_status'] = "InProcess";
        //$charges = $this->booking_model->getbooking_charges($booking_id);
	$data['closed_date'] = date('Y-m-d H:i:s');
	$data['service_center_remarks'] = date("F j") . ":- " . $closing_remarks;

	log_message('info', $booking_id . print_r($data, TRUE));

	$this->vendor_model->update_service_center_action($data);

	redirect(base_url() . "service_center/pending_booking");
    }

    /**
     * @desc: This is used to get cancel booking form.
     * @param: booking id
     * @return: void
     */
    function cancel_booking_form($booking_id) {
        $this->checkUserSession();
        $data['user_and_booking_details'] = $this->booking_model->booking_history_by_booking_id($booking_id);
        $data['reason'] = $this->booking_model->cancelreason("vendor");

        $this->load->view('service_centers/header');
        $this->load->view('service_centers/cancel_booking_form', $data);
    }

    /**
     * @desc: This is used to cancel booking for service center.
     * @param: booking id
     * @return: void
     */
    function process_cancel_booking($booking_id) {
        $this->checkUserSession();

        $cancellation_reason = $this->input->post('cancellation_reason');
        if ($cancellation_reason === 'Other') {
            $cancellation_reason = "Other : " . $cancellation_reason;
        }

        $data['service_center_id'] = $this->session->userdata('service_center_id');
        $data['booking_id'] = $booking_id;
        $data['current_status'] = "InProcess";
        $data['internal_status'] = "Cancelled";
        $data['service_charge'] = $data['additional_service_charge'] = $data['parts_cost'] = $data['amount_paid'] = 0;
        $data['cancellation_reason'] = $cancellation_reason;
        $data['closed_date'] = date('Y-m-d H:i:s');

        $this->vendor_model->update_service_center_action($data);

        redirect(base_url() . "service_center/pending_booking");
    }

    /**
     * @desc: This function Sets Session
     * @param: Service center id
     * @param: Service center name
     * @return: void
     */
    function setSession($service_center_id, $service_center_name) {
	$userSession = array(
	    'session_id' => md5(uniqid(mt_rand(), true)),
	    'service_center_id' => $service_center_id,
	    'service_center_name' => $service_center_name,
	    'sess_expiration' => 30000,
	    'loggedIn' => TRUE,
	    'userType' => 'service_center'
	);

        $this->session->set_userdata($userSession);
    }

    /**
     * @desc: This funtion will check Session
     * @param: void
     * @return: true if details matches else session is distroyed.
     */
    function checkUserSession() {
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'service_center')) {
            return TRUE;
        } else {
            $this->session->sess_destroy();
            redirect(base_url() . "service_center");
        }
    }

    /**
     * @desc : This funtion for logout
     * @param: void
     * @return: void
     */
    function logout() {
        $this->session->sess_destroy();
        redirect(base_url() . "service_center");
    }

    /**
     * @desc: this is used to display completed booking for specific service center
     */
    function completed_booking($offset = 0, $page = 0){
        $this->checkUserSession();
    if ($page == 0) {
        $page = 50;
    }

        $service_center_id = $this->session->userdata('service_center_id');

        $offset = ($this->uri->segment(3) != '' ? $this->uri->segment(3) : 0);
        $config['base_url'] = base_url() . 'service_centers/completed_booking';
        $config['total_rows'] = $this->service_centers_model->getcompleted_or_cancelled_booking("count","",$service_center_id,"Completed");

        $config['per_page'] = $page;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['bookings'] = $this->service_centers_model->getcompleted_or_cancelled_booking($config['per_page'], $offset, $service_center_id, "Completed");

        if ($this->session->flashdata('result') != '')
            $data['success'] = $this->session->flashdata('result');

        $data['status'] = "Completed";

        $this->load->view('service_centers/header');
        $this->load->view('service_centers/completed_booking', $data);

    }

    /**
     * @desc: this is used to display completed booking for specific service center
     */
    function cancelled_booking($offset = 0, $page = 0){
        $this->checkUserSession();
    if ($page == 0) {
        $page = 50;
    }

        $service_center_id = $this->session->userdata('service_center_id');

        $offset = ($this->uri->segment(3) != '' ? $this->uri->segment(3) : 0);
        $config['base_url'] = base_url() . 'service_centers/completed_booking';
        $config['total_rows'] = $this->service_centers_model->getcompleted_or_cancelled_booking("count","",$service_center_id,"Cancelled");

        $config['per_page'] = $page;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['bookings'] = $this->service_centers_model->getcompleted_or_cancelled_booking($config['per_page'], $offset, $service_center_id, "Cancelled");

        if ($this->session->flashdata('result') != '')
            $data['success'] = $this->session->flashdata('result');

        $data['status'] = "Cancelled";

        $this->load->view('service_centers/header');
        $this->load->view('service_centers/completed_booking', $data);

    }

    /**
     * @desc: this method save reschedule request in service center action table.
     * @param: void
     * @return : void
     */
    function save_reschedule_request(){
        $data['booking_id'] = $this->input->post('booking_id');
        $data['booking_date'] = date('Y-m-d',strtotime($this->input->post('booking_date')));
        $data['booking_timeslot'] = $this->input->post('booking_timeslot');
        $data['current_status'] = "InProcess";
        $data['internal_status'] = 'Reschedule';
        $data['reschedule_reason'] = $this->input->post('remarks');
	$data['update_date'] = date("Y-m-d H:i:s");

	$this->vendor_model->update_service_center_action($data);
        print_r("success");
    }



}
