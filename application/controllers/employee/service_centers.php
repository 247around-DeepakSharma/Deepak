<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

error_reporting(E_ALL);
            ini_set('display_errors', 1);

class Service_centers extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();
        $this->load->model('service_centers_model');
        $this->load->model('booking_model');
        $this->load->model('partner_model');
        $this->load->model('vendor_model');
        $this->load->model('invoices_model');
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
        $this->setSession($sc_details[0]['id'], $sc_details[0]['name'], $service_center_id['id']);

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
        $config['base_url'] = base_url() . 'service_center/pending_booking';
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
        $data['booking_history'] = $this->booking_model->getbooking_history($booking_id);
        $data['unit_details'] = $this->booking_model->get_unit_details($booking_id);


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

        $data['booking_id'] = $booking_id;
        $data['booking_history'] = $this->booking_model->getbooking_history($booking_id);
        $data['bookng_unit_details'] = $this->booking_model->getunit_details($booking_id);
        foreach ($data['bookng_unit_details'] as $key => $value) {
            foreach ($value['quantity'] as $keys => $line_item) {
                $partner_id = $this->booking_model->get_price_mapping_partner_code("", $data['booking_history'][0]['partner_id']);

            $result = $this->partner_model->getPrices($data['booking_history'][0]['service_id'], $value['category'], $value['capacity'], $partner_id, $line_item['price_tags']);
        
            $data['bookng_unit_details'][$key]['quantity'][$keys]['pod'] = $result[0]['pod'];

            }
        }
         
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/complete_booking_form', $data);
    }

    /**
     * @desc: This is used to complete the booking once all the required details are filled.
     * @param: booking id
     * @return: void
     */
    function process_complete_booking($booking_id) {
        $this->checkUserSession();
        $this->form_validation->set_rules('customer_basic_charge', 'Basic Charge', 'required');
        $this->form_validation->set_rules('additional_charge', 'Additional Service Charge', 'required');
        $this->form_validation->set_rules('parts_cost', 'Parts Cost', 'required');
        $this->form_validation->set_rules('booking_status', 'Status', 'required');

        if ($this->form_validation->run() == FALSE || $booking_id =="" || $booking_id == NULL) {
            $this->complete_booking_form($booking_id);
        } else {
          // customer paid basic charge is comming in array
       // Array ( [100] =>  500 , [102] =>  300 )  
       $customer_basic_charge = $this->input->post('customer_basic_charge');
        // Additional service charge is comming in array
       $additional_charge =  $this->input->post('additional_charge');
        // Parts cost is comming in array
       $parts_cost =  $this->input->post('parts_cost');
       $booking_status = $this->input->post('booking_status');
       $total_amount_paid =  $this->input->post('grand_total_price');
       $closing_remarks = $this->input->post('closing_remarks');
       $serial_number = $this->input->post('serial_number');
       //$internal_status = "Cancelled";
       $getremarks = $this->booking_model->getbooking_charges($booking_id);
       $i = 0;
       foreach ($customer_basic_charge as $unit_id => $value) {
            // variable $unit_id  is existing id in booking unit details table of given booking id 
            $data = array();
            $data['unit_details_id'] = $unit_id;
            $data['service_center_id'] = $this->session->userdata('service_center_id');
            $data['service_charge'] = $value;
            $data['additional_service_charge'] = $additional_charge[$unit_id];
            $data['parts_cost'] = $parts_cost[$unit_id];
            $data['internal_status'] = $booking_status[$unit_id];
            $data['current_status'] = "InProcess";
            $data['closed_date'] = date('Y-m-d H:i:s');
            $data['booking_id'] =  $booking_id;
            $data['amount_paid'] = $total_amount_paid;
            $data['serial_number'] =  $serial_number[$unit_id];

            if (!empty($getremarks[0]['service_center_remarks'])) {
                
               $data['service_center_remarks'] = date("F j") . ":- " . $closing_remarks." ". $getremarks[0]['service_center_remarks'];

            } else {
                if(!empty($closing_remarks)){
                    $data['service_center_remarks'] = date("F j") . ":- " .$closing_remarks;
                }
            }

            $i++;

            $this->vendor_model->update_service_center_action($data);

       }

        $state_change['booking_id'] = $booking_id;
        $state_change['new_state'] = 'InProcess';
        $state_change['old_state'] = "Pending";
        $state_change['agent_id'] = $this->session->userdata('service_center_agent_id');
        $state_change['service_center_id'] = $this->session->userdata('service_center_id');
        $state_change['remarks'] = $closing_remarks;
            // Insert data into booking state change
        $this->booking_model->insert_booking_state_change($state_change);

        redirect(base_url() . "service_center/pending_booking");
        }
    }

    /**
     * @desc: This is used to get cancel booking form.
     * @param: booking id
     * @return: void
     */
    function cancel_booking_form($booking_id) {
        $this->checkUserSession();
        $data['user_and_booking_details'] = $this->booking_model->getbooking_history($booking_id);
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
        $this->form_validation->set_rules('cancellation_reason', 'Cancellation Reason', 'required');

        if ($this->form_validation->run() == FALSE || $booking_id =="" || $booking_id == NULL) {
            $this->cancel_booking_form($booking_id);
        } else {

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

        $state_change['booking_id'] = $booking_id;
        $state_change['new_state'] = 'Cancelled';
        $state_change['old_state'] = "Pending";
        $state_change['agent_id'] = $this->session->userdata('service_center_agent_id');
        $state_change['service_center_id'] = $this->session->userdata('service_center_id');
        $state_change['remarks'] = $data['cancellation_reason'];
            // Insert data into booking state change
        $this->booking_model->insert_booking_state_change($state_change);

        redirect(base_url() . "service_center/pending_booking");
        }
    }

    /**
     * @desc: This function Sets Session
     * @param: Service center id
     * @param: Service center name
     * @return: void
     */
    function setSession($service_center_id, $service_center_name, $sc_agent_id) {
	$userSession = array(
	    'session_id' => md5(uniqid(mt_rand(), true)),
	    'service_center_id' => $service_center_id,
	    'service_center_name' => $service_center_name,
            'service_center_agent_id' => $sc_agent_id,
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
        $config['base_url'] = base_url() . 'service_center/completed_booking';
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
        $config['base_url'] = base_url() . 'service_center/cancelled_booking';
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
        $this->form_validation->set_rules('booking_id', 'Booking ID', 'trim|required');
       

        if ($this->form_validation->run() == FALSE ) {
            echo "Please Reschedule Again";
        } else {
        $data['booking_id'] = $this->input->post('booking_id');
        $data['booking_date'] = date('Y-m-d',strtotime($this->input->post('booking_date')));
        $data['booking_timeslot'] = $this->input->post('booking_timeslot');
        $data['current_status'] = "InProcess";
        $data['internal_status'] = 'Reschedule';
        $data['reschedule_reason'] = $this->input->post('remarks');
        $data['update_date'] = date("Y-m-d H:i:s");
        $this->vendor_model->update_service_center_action($data);

        $state_change['booking_id'] = $data['booking_id'];
        $state_change['new_state'] = 'Reschedule';
        $state_change['old_state'] = "Pending";
        $state_change['agent_id'] = $this->session->userdata('service_center_agent_id');
        $state_change['service_center_id'] = $this->session->userdata('service_center_id');
        $state_change['remarks'] = $data['reschedule_reason'];
            // Insert data into booking state change
        $this->booking_model->insert_booking_state_change($state_change);
        print_r("success");
        }
    }
    /**
     * @desc: get invoice details and bank transacton details to display in view
     * Get Service center Id from session.
     */
    function invoices_details() {
        $this->checkUserSession();
        $data['vendor_partner'] = "vendor";
        $data['vendor_partner_id'] = $this->session->userdata('service_center_id');
        $invoice['invoice_array'] = $this->invoices_model->getInvoicingData($data);

        $data2['partner_vendor'] = "vendor";
        $data2['partner_vendor_id'] = $this->session->userdata('service_center_id');
        $invoice['bank_statement'] = $this->invoices_model->get_bank_transactions_details($data2);
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/invoice_summary', $invoice);
    }



}
