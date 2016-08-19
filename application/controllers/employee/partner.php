<?php

if (!defined('BASEPATH')){
    exit('No direct script access allowed');
}

class Partner extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();

        $this->load->model('booking_model');
        $this->load->model('partner_model');
        $this->load->model('vendor_model');
        $this->load->model('user_model');
        $this->load->library("pagination");
        $this->load->library("session");
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

    }

    /**
     * @desc: This is used to load Partner  Login Page
     *
     * @param: void
     * @return: void
     */
    function index() {
       $this->load->view('partner/partner_login');

    }

     /**
     * @desc: This is used to login
     *
     * If user name and password matches allowed to login and redirect pending booking, else error message appears.
     *
     * @param: void
     * @return: void
     */
    function partner_login() {

        $data['user_name'] = $this->input->post('user_name');
        $data['password'] = md5($this->input->post('password'));
        $partner_id = $this->partner_model->partner_login($data);

        if ($partner_id) {
        //get partner details now
        $partner_details = $this->partner_model->getpartner($partner_id);

        $this->setSession($partner_details[0]['id'], $partner_details[0]['name']);
        log_message('info', 'Partner loggedIn  partner id' . $partner_details[0]['id'] . " Partner name" . $partner_details[0]['name']);

        redirect(base_url() . "partner/pending_booking");
        } else {

            $userSession = array('error' => 'Please enter correct user name and password' );
            $this->session->set_userdata($userSession);
            redirect(base_url() . "partner/login");
        }
    }

     /**
     * @desc: this is used to load pending booking
     * @param: Offset and page no.
     * @return: void
     */
    function pending_booking($offset = 0) {
       $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');
        $config['base_url'] = base_url() . 'partner/pending_booking';
        $config['total_rows'] = $this->partner_model->getPending_booking("count","",$partner_id);

        $config['per_page'] = 50;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['bookings'] = $this->partner_model->getPending_booking($config['per_page'], $offset, $partner_id);

        if ($this->session->flashdata('result') != ''){
            $data['success'] = $this->session->flashdata('result');
        }
         log_message('info', 'Partner view Pending booking  partner id' . $partner_id . " Partner name" . $this->session->userdata('partner_name')." data ". print_r($data, true));
        $this->load->view('partner/header');
        $this->load->view('partner/pending_booking', $data);
    }

    /**
     * @desc: this is used to load pending booking
     * @param: Offset and page no.
     * @return: void
     */
    function pending_queries($offset = 0){
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');
        $config['base_url'] = base_url() . 'partner/pending_queries';
        $config['total_rows'] = $this->partner_model->getPending_queries("count","",$partner_id);

        $config['per_page'] = 50;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['bookings'] = $this->partner_model->getPending_queries($config['per_page'], $offset, $partner_id);

        if ($this->session->flashdata('result') != ''){
            $data['success'] = $this->session->flashdata('result');
        }
         log_message('info', 'Partner view Pending query  partner id' . $partner_id . " Partner name" . $this->session->userdata('partner_name')." data ". print_r($data, true));
        $this->load->view('partner/header');
        $this->load->view('partner/pending_queries', $data);

    }


    /**
     * @desc: this is used to display completed booking for specific service center
     */
    function closed_booking($state, $offset = 0){
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');

        $config['base_url'] = base_url() . 'partner/closed_booking/'.$state;
        $config['total_rows'] = $this->partner_model->getclosed_booking("count","",$partner_id, $state);

        $config['per_page'] = 50;
        $config['uri_segment'] = 4;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['bookings'] = $this->partner_model->getclosed_booking($config['per_page'], $offset, $partner_id, $state);

        if ($this->session->flashdata('result') != ''){
            $data['success'] = $this->session->flashdata('result');
        }

        $data['status'] = $state;

        log_message('info', 'Partner view '.$state.' booking  partner id' . $partner_id . " Partner name" . $this->session->userdata('partner_name'). " data ". print_r($data, true));

        $this->load->view('partner/header');
        $this->load->view('partner/closed_booking', $data);

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

        log_message('info', 'Partner view booking details booking  partner id' . $this->session->userdata('partner_id') . " Partner name" . $this->session->userdata('partner_name'). " data ". print_r($data, true));

        $this->load->view('partner/header');
        $this->load->view('partner/booking_details', $data);
    }

     /**
     * @desc: This method loads abb booking form
     * it gets user details(if exist), city, source, services
     */
    function get_addbooking_form($phone_number){
        $data = $this->booking_model->get_city_booking_source_services($phone_number);
        $this->load->view('partner/header');
        $this->load->view('partner/get_addbooking', $data);
    }
    
    /**
     * @desc: This method is used to process to add booking by partner
     */
    function process_addbooking(){
        
        $booking_date =  date('d-m-Y', strtotime($this->input->post('booking_date')));
        $order_id = $this->input->post('order_id');
        
        $description = $this->input->post('description');
        
        $authToken =  $this->partner_model->get_authentication_code($this->session->userdata('partner_id'));
        if($authToken){
        $postData = '{'
                . '"partnerName" : "'.$this->session->userdata('partner_name').'",'
                . '"orderID" : "'.$order_id.'",'
                . '"name" : "'.$this->input->post('user_name').'",'
                . '"mobile" : "'. $this->input->post('booking_primary_contact_no').'",'
                . '"email" : "'. $this->input->post('user_email').'",'
                . '"address" : "'. $this->input->post('booking_address').'",'
                . '"pincode" : "'. $this->input->post('booking_pincode').'",'
                . '"city" : "'. $this->input->post('booking_address').'",'
                . '"requestType" : "'. $this->input->post('price_tag').'",'
                . '"alternatePhone" : "'. $this->input->post('booking_alternate_contact_no').'",'
                . '"landmark" : "'. $this->input->post('landmark').'",'
                . '"product" : "'. $this->input->post('service_name').'",'
                . '"brand" : "'. $this->input->post('appliance_brand').'",'
                . '"productType" : "'. $description.'",'
                . '"deliveryDate" : {"year" : "0000", "month" : "00", "day" : "00", "hour" : "00", "minute" :"00"},'
                . '"category" : "'. $this->input->post('appliance_category').'",'
                . '"capacity" : "'. $this->input->post('appliance_capacity').'",'
                . '"model" : "'. $this->input->post('model_number').'",'
                . '"serial_number" : "'. $this->input->post('serial_number').'",'
                . '"booking_date" : "'. $booking_date.'",'
                . '"purchase_month" : "'. $this->input->post('purchase_month').'",'
                . '"purchase_year" : "'. $this->input->post('purchase_year').'",'
                . '"partner_source" : "'. $this->input->post('partner_source').'",'
                . '"remarks" : "'. $this->input->post('query_remarks').'"'
                . '}';
        
        $ch = curl_init(base_url().'partner/insertBookingByPartner');
        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Authorization: '.$authToken,
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => $postData
        ));

        // Send the request
        $response = curl_exec($ch);

        //echo $response;
        // Check for errors
        if($response === FALSE){
            die(curl_error($ch));
        }
        // Decode the response
        //$responseData = json_decode($response, TRUE);

        // Print the date from the response
        //echo $responseData['data'];
        redirect(base_url()."partner/pending_booking");
        } else {
            echo "Authentication fail:";
        }
    }


    /**
     * @desc: This function Sets Session
     * @param: Partrner id
     * @param: Partner name
     * @return: void
     */
    function setSession($partner_id, $partner_name) {
    $userSession = array(
        'session_id' => md5(uniqid(mt_rand(), true)),
        'partner_id' => $partner_id,
        'partner_name' => $partner_name,
        'sess_expiration' => 30000,
        'loggedIn' => TRUE,
        'userType' => 'partner'
    );

        $this->session->set_userdata($userSession);
    }

    /**
     * @desc: This funtion will check Session
     * @param: void
     * @return: true if details matches else session is distroyed.
     */
    function checkUserSession() {
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'partner')) {
            return TRUE;
        } else {
            $this->session->sess_destroy();
            redirect(base_url() . "partner/login");
        }
    }

    /**
     * @desc : This funtion for logout
     * @param: void
     * @return: void
     */
    function logout() {
        log_message('info', 'Partner logout  partner id' . $this->session->userdata('partner_id') . " Partner name" . $this->session->userdata('partner_name'));

        $this->session->sess_destroy();
        redirect(base_url() . "partner/login");
    }


}