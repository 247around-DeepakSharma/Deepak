<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

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
        $this->load->model('invoices_model');
        $this->load->library("pagination");
        $this->load->library("session");
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('notify');


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

        if ($this->session->flashdata('result') != '')
            $data['success'] = $this->session->flashdata('result');
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

        if ($this->session->flashdata('result') != '')
            $data['success'] = $this->session->flashdata('result');
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

        if ($this->session->flashdata('result') != '')
            $data['success'] = $this->session->flashdata('result');

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
        $data['booking_history'] = $this->booking_model->getbooking_history($booking_id);
        $data['unit_details'] = $this->booking_model->get_unit_details($booking_id);


        log_message('info', 'Partner view booking details booking  partner id' . $this->session->userdata('partner_id') . " Partner name" . $this->session->userdata('partner_name'). " data ". print_r($data, true));

        $this->load->view('partner/header');
        $this->load->view('partner/booking_details', $data);
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
        $validate =  $this->set_form_validation();
        log_message('info', 'Partner initiate add booking' .$this->session->userdata('partner_name'));

        if($validate){
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
                    . '"city" : "'. $this->input->post('city').'",'
                    . '"requestType" : "'. $this->input->post('price_tag').'",'
                    . '"alternatePhone" : "'. $this->input->post('booking_alternate_contact_no').'",'
                    . '"landmark" : "'. $this->input->post('landmark').'",'
                    . '"product" : "'. $this->input->post('service_name').'",'
                    . '"brand" : "'. $this->input->post('appliance_brand').'",'
                    . '"productType" : "'. $description.'",'
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
             log_message('info', ' Partner ' .$this->session->userdata('partner_name')."  booking not Inserted error mgs". print_r($response, true) );
            // Decode the response
            $responseData = json_decode($response, TRUE);


            if(isset($responseData['data']['result'])){

                if($responseData['data']['result'] != "Success"){
                    log_message('info', ' Partner ' .$this->session->userdata('partner_name')."  booking not Inserted ". print_r($_POST, true)." error mgs". print_r($responseData['data'], true) );
                   $this->insertion_failure($_POST);
                   $output = "Soory, Booking is not inserted. ";
                   $userSession = array('success' =>$output);
                   $this->session->set_userdata($userSession);
                   $data = $this->booking_model->get_city_booking_source_services($this->input->post('booking_primary_contact_no'));
                   $this->load->view('partner/header');
                   $this->load->view('partner/get_addbooking', $data);

                } else {
                     $output = "Booking Inserted.";
                    $userSession = array('success' =>$output);
                    $this->session->set_userdata($userSession);

                    log_message('info', 'Partner ' .$this->session->userdata('partner_name')."  booking Inserted ". print_r($_POST, true));
                    // Print the date from the response
                    //echo $responseData['data'];
                     redirect(base_url()."partner/pending_booking");
                }
             } else {
                log_message('info', 'Partner ' .$this->session->userdata('partner_name')."  booking not Inserted ". print_r($_POST, true)." error mgs". print_r($responseData['data'], true) );
                $this->insertion_failure($_POST);
                $output = "Soory, Booking is not inserted. ";
                $userSession = array('success' =>$output);
                $this->session->set_userdata($userSession);
                $data = $this->booking_model->get_city_booking_source_services($this->input->post('booking_primary_contact_no'));
                $this->load->view('partner/header');
                $this->load->view('partner/get_addbooking', $data);

             }

            } else {
                log_message('info', 'Partner ' .$this->session->userdata('partner_name')."  Authentication failed");
                //echo "Authentication fail:";
            }
        } else {
            log_message('info', 'Partner add booking' .$this->session->userdata('partner_name')." Validation failed ". print_r($_POST, true));
            $data = $this->booking_model->get_city_booking_source_services($this->input->post('booking_primary_contact_no'));
            $this->load->view('partner/header');
            $this->load->view('partner/get_addbooking', $data);
        }
    }

    function insertion_failure($post){
        $to = "anuj@247around.com, abhaya@247around.com";
	$cc = "";
        $bcc = "";
        $subject = "Booking Insertion Failure By ".$this->session->userdata('partner_name');
        $message = $post;
        $this->notify->sendEmail("booking@247around.com", $to, $cc, $bcc, $subject, $message, "");

    }

    function set_form_validation(){
        $this->form_validation->set_rules('user_name', 'User Name', 'required|xss_clean');
        $this->form_validation->set_rules('booking_primary_contact_no', 'Mobile Number', 'trim|required|exact_length[10]|xss_clean');
        $this->form_validation->set_rules('city', 'City', 'trim|required|xss_clean');
        $this->form_validation->set_rules('booking_address', 'Booking Address', 'required');
        $this->form_validation->set_rules('landmark', 'LandMark', 'trim');
        $this->form_validation->set_rules('appliance_capacity', 'Appliance Capacity', 'trim|required|xss_clean');
        $this->form_validation->set_rules('alternate_phone_number', 'Alternate Number', 'trim|xss_clean');
        $this->form_validation->set_rules('purchase_year', 'Purchase Year', 'trim|xss_clean');
        $this->form_validation->set_rules('purchase_month', 'Purchase Month', 'trim|xss_clean');
        $this->form_validation->set_rules('model_number', 'Model Number', 'trim|xss_clean');
        $this->form_validation->set_rules('order_id', 'Order ID', 'trim|xss_clean');
        $this->form_validation->set_rules('serial_number', 'Serial Number', 'trim|xss_clean');
        $this->form_validation->set_rules('appliance_category', 'Appliance Category', 'required');
        $this->form_validation->set_rules('partner_source', 'Booking Source', 'required');
        $this->form_validation->set_rules('service_name', 'Service Name', 'required');
        $this->form_validation->set_rules('booking_date', 'Booking Date', 'required');
        $this->form_validation->set_rules('query_remarks', 'Problem Description', 'required');
        $this->form_validation->set_rules('booking_pincode', 'Booking Pincode', 'trim|required|exact_length[6]');
        $this->form_validation->set_rules('price_tag', 'Call Type', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            return FALSE;
        }
        else {
            return true;
        }
    }

    /**
     * @desc: get invoice details and bank transacton details to display in partner invoice view
     * Get partner Id from session.
     */
    function invoices_details() {
        $this->checkUserSession();
        $partner_id =  $this->session->userdata('partner_id');
        $data['vendor_partner'] = "partner";
        $data['vendor_partner_id'] = $partner_id;
        $invoice['invoice_array'] = $this->invoices_model->getInvoicingData($data);

        $data2['partner_vendor'] = "partner";
        $data2['partner_vendor_id'] = $partner_id;
        $invoice['bank_statement'] = $this->invoices_model->get_bank_transactions_details($data2);
        $this->load->view('partner/header');
        $this->load->view('partner/invoice_summary', $invoice);
    }


}