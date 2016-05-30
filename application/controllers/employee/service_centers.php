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
     * This is used lo load vendor Login Page
     */
    function index(){
        $this->load->view('service_centers/service_center_login');
    }

    
    function service_center_login(){
        $data['user_name'] = $this->input->post('user_name');
        $data['password'] = md5($this->input->post('password'));
        $service_center_id = $this->service_centers_model->service_center_login($data);
        
        if($service_center_id){
            $this->setSession($service_center_id, $data['user_name']);
            redirect(base_url() . "service_center/pending_booking");

        } else {
            $data['error'] = "Please enter correct user name and password";
            $this->load->view('service_centers/service_center_login', $data);
        }
    }
    
    /**
     * @desc: this is used to load pending booking
     * @param: Offset and page no.
     * @return: void 
     */
    function pending_booking($offset = 0, $page = 0){

        $this->checkUserSession();
        if ($page == 0) {
                $page = 50;
        }

        $service_center_id = $this->session->userdata('service_center_id');

        $offset = ($this->uri->segment(3) != '' ? $this->uri->segment(3) : 0);
        $config['base_url'] = base_url() . 'service_centers/pending_booking';
        $config['total_rows'] = $this->booking_model->total_pending_booking("", $service_center_id);

        $config['per_page'] = $page;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['bookings'] = $this->booking_model->date_sorted_booking($config['per_page'], $offset, "", $service_center_id);

        if ($this->session->flashdata('result') != '')
            $data['success'] = $this->session->flashdata('result');

        $this->load->view('service_centers/header');
        $this->load->view('service_centers/pending_booking', $data);
    }
    /**
     * @desc: this is used to get booking details by using booking ID And load booking booking details view
     * @param: booking id
     */
    function booking_details($booking_id){
        $this->checkUserSession();
        $data['query1'] = $this->booking_model->booking_history_by_booking_id($booking_id);
        $data['query2'] = $this->booking_model->get_unit_details($booking_id);
        $data['query4'] = $this->booking_model->getdescription_about_booking($booking_id);

        $this->load->view('service_centers/header');
        $this->load->view('service_centers/booking_details', $data);
    }

    function complete_booking_form($booking_id){
        $this->checkUserSession();
        $data['query2'] = $this->booking_model->get_unit_details($booking_id);
        $data['booking'] = $this->booking_model->booking_history_by_booking_id($booking_id);
        $data['booking_id'] = $booking_id;
        $data['charges'] = $this->booking_model->getbooking_charges($booking_id);
        $data['internal_status'] = $this->booking_model->get_internal_status("Complete");
 
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/complete_booking_form', $data);

    }

    function process_complete_booking($booking_id){
        $this->checkUserSession();
        $data['booking_id'] =$booking_id;
        $data['service_charge'] = $this->input->post('service_charge');
        $data['service_center_id'] = $this->session->userdata('service_center_id');
        $data['additional_service_charge'] = $this->input->post('additional_service_charge');
        $data['internal_status'] = $this->input->post('internal_status');
        $data['parts_cost'] = $this->input->post('parts_cost');
        $data['amount_paid'] = $this->input->post('total_charge');
        $closing_remarks = $this->input->post('closing_remarks');
        $charges = $this->booking_model->getbooking_charges($booking_id);
       
        if(!empty($charges)){
            // remove previous text, added in closing_remarks column.
            $string = str_replace($charges[0]['service_center_remarks']," ", $closing_remarks);
            // Add current and previous text in admin_remarks column
            $data['service_center_remarks'] = $charges[0]['service_center_remarks']." <br/>".date("F j").":- ". $string;
    
            $this->vendor_model->update_service_center_action($data);

        } else {
            $data['service_center_remarks'] = date("F j") .":- ". $closing_remarks;
            $this->vendor_model->insert_service_center_action($data);
           
        }
        
        redirect(base_url() . "service_center/pending_booking");
    }

    /**
     *  @desc : This function Set Session
     *  param : Service center id
     */
    function setSession($service_center_id, $user_name) {

        $userSession = array(
            'session_id' => md5(uniqid(mt_rand(), true)),
            'service_center_id' => $service_center_id,
            'user_name' => $user_name,
            'sess_expiration' => 30000,
            'loggedIn' => TRUE,
            'userType' => 'service_center'
        );

        $this->session->set_userdata($userSession);
    }

    /**
     * @desc :This funtion will check Session
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
     * @desc :This funtion for logout
     */
    function logout() {
        $this->session->sess_destroy();
        redirect(base_url() . "service_center");
    }


}