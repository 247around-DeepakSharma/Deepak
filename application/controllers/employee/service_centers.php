<?php

if (!defined('BASEPATH')){
    exit('No direct script access allowed');
}

error_reporting(E_ALL);
            ini_set('display_errors', 1);
            ini_set('memory_limit', -1);

class Service_centers extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();
        $this->load->model('service_centers_model');
        $this->load->model('service_centre_charges_model');
        $this->load->model('booking_model');
        $this->load->model('reporting_utils');
        $this->load->model('partner_model');
        $this->load->model('upcountry_model');
        $this->load->model('vendor_model');
        $this->load->model('user_model');
        $this->load->model('employee_model');
        $this->load->model('invoices_model');
        $this->load->model('inventory_model');
        $this->load->library("pagination");
        $this->load->library('asynchronous_lib');
        $this->load->library('booking_utilities');
        $this->load->library("session");
        $this->load->library('s3');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('PHPReport');
        $this->load->helper('download');
        $this->load->library('user_agent');
        $this->load->library('notify');
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
        $agent = $this->service_centers_model->service_center_login($data);

        if ($agent) {
            //get sc details now
            $sc_details = $this->vendor_model->getVendorContact($agent['service_center_id']);
            if (!empty($sc_details)) {
                $this->setSession($sc_details[0]['id'], $sc_details[0]['company_name'], $agent['id'], $sc_details[0]['is_update'], $sc_details[0]['is_upcountry']);

                //Saving Login Details in Database
                $login_data['browser'] = $this->agent->browser();
                $login_data['agent_string'] = $this->agent->agent_string();
                $login_data['ip'] = $this->session->all_userdata()['ip_address'];
                $login_data['action'] = _247AROUND_LOGIN;
                $login_data['entity_type'] = $this->session->all_userdata()['userType'];
                $login_data['agent_id'] = $this->session->all_userdata()['service_center_agent_id'];
                $login_data['entity_id'] = $this->session->all_userdata()['service_center_id'];

                $login_id = $this->employee_model->add_login_logout_details($login_data);
                //Adding Log Details
                if ($login_id) {
                    log_message('info', __FUNCTION__ . ' Logging details have been captured for service center ' . $login_data['agent_id']);
                } else {
                    log_message('info', __FUNCTION__ . ' Err in capturing logging details for service center ' . $login_data['agent_id']);
                }

                redirect(base_url() . "service_center/pending_booking");
            } else {
                $userSession = array('error' => 'Please enter correct user name and password');
                $this->session->set_userdata($userSession);
                redirect(base_url() . "service_center");
            }
        } else {
            $userSession = array('error' => 'Please enter correct user name and password');
            $this->session->set_userdata($userSession);
            redirect(base_url() . "service_center");
        }
    }

    /**
     * @desc: this is used to load pending booking
     * @param: booking id (optional)
     * @return: void
     */
    function pending_booking($booking_id="") {
        $this->checkUserSession();
        
       
        $data['booking_id'] = $booking_id;
        

        $this->load->view('service_centers/header');
        $this->load->view('service_centers/pending_booking', $data);
        
    }
    
    function get_header_summary(){
        $service_center_id = $this->session->userdata('service_center_id');
        $data['eraned_details'] =  $this->service_centers_model->get_sc_earned($service_center_id);
        $data['cancel_booking'] = $this->service_centers_model->count_cancel_booking_sc($service_center_id);
        if($this->session->userdata('is_upcountry') == 1){
            $data['upcountry'] = $this->upcountry_model->upcountry_service_center_3_month_price($service_center_id);
        }
        $this->load->view("service_centers/header_summary", $data);
        
    }
    
    function pending_booking_on_tab($booking_id = ""){
        $service_center_id = $this->session->userdata('service_center_id');
        $data['bookings'] = $this->service_centers_model->pending_booking($service_center_id, $booking_id);
        if($this->session->userdata('is_update') == 1){
            //$data['engineer_details'] = $this->vendor_model->get_engineers($service_center_id);
            $data['spare_parts_data'] = $this->service_centers_model->get_updated_spare_parts_booking($service_center_id);

        }
        $this->load->view('service_centers/pending_on_tab', $data);
    }


    /**
     * @desc: this is used to get booking details by using booking ID And load booking booking details view
     * @param: booking id
     * @return: void
     */
    function booking_details($code) {
        log_message('info', __FUNCTION__ . " Booking ID: " . base64_decode(urldecode($code)));
        $this->checkUserSession();
        $booking_id =base64_decode(urldecode($code));
        $data['booking_history'] = $this->booking_model->getbooking_history($booking_id);
        $unit_where = array('booking_id'=>$booking_id);
        $data['unit_details'] = $this->booking_model->get_unit_details($unit_where);
        // This is commented because we are not showing in booking details
//        $data['upcountry_details'] = $this->upcountry_model->upcountry_booking_list(
//                $this->session->userdata('service_center_id'), $booking_id,true, 
//                $data['booking_history'][0]['upcountry_paid_by_customer']);


        $this->load->view('service_centers/header');
        $this->load->view('service_centers/booking_details', $data);
    }

    /**
     * @desc: This is used to get complete booking form.
     * @param: booking id
     * @return: void
     */
    function complete_booking_form($code) {
        log_message('info', __FUNCTION__ . " Booking ID: " . base64_decode(urldecode($code)));
        $this->checkUserSession();
        $booking_id = base64_decode(urldecode($code));
        $data['booking_id'] = $booking_id;
        $data['booking_history'] = $this->booking_model->getbooking_history($booking_id);
        $data['bookng_unit_details'] = $this->booking_model->getunit_details($booking_id);
        
        foreach ($data['bookng_unit_details'] as $key => $value) {
            foreach ($value['quantity'] as $keys => $line_item) {
                $partner_id = $this->booking_model->get_price_mapping_partner_code("", $data['booking_history'][0]['partner_id']);

            $result = $this->partner_model->getPrices($value['service_id'], $value['category'], $value['capacity'], $partner_id, $line_item['price_tags']);
        
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

        if (($this->form_validation->run() == FALSE) || ($booking_id =="") || (is_null($booking_id))) {
            $this->complete_booking_form(urlencode(base64_encode($booking_id)));
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
            $spare_parts_required = $this->input->post('spare_parts_required');
            $upcountry_charges = $this->input->post("upcountry_charges");
            $is_update_spare_parts = FALSE;
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
                 if($booking_status[$unit_id] == _247AROUND_COMPLETED && $spare_parts_required == 1){
                     $data['internal_status'] = DEFECTIVE_PARTS_PENDING;
                     $is_update_spare_parts = TRUE;
                 } else {
                     $data['internal_status'] = $booking_status[$unit_id];
                 }
                 $data['current_status'] = "InProcess";
                 $data['closed_date'] = date('Y-m-d H:i:s');
                 $data['booking_id'] =  $booking_id;
                 $data['amount_paid'] = $total_amount_paid;
                 $data['update_date'] = date("Y-m-d H:i:s");
                 if( $i == 0){
                    $data['upcountry_charges'] = $upcountry_charges;
                 }
                 if(isset($serial_number[$unit_id])){
                    $data['serial_number'] =  $serial_number[$unit_id];
                 }
                 
                 if (!empty($getremarks[0]['service_center_remarks'])) {

                    $data['service_center_remarks'] = date("F j") . ":- " . $closing_remarks." ". $getremarks[0]['service_center_remarks'];

                 } else {
                     if(!empty($closing_remarks)){
                         $data['service_center_remarks'] = date("F j") . ":- " .$closing_remarks;
                     }
                 }
                 $i++;
                $this->vendor_model->update_service_center_action($booking_id, $data);

            }
            // Insert data into booking state change
            $this->insert_details_in_state_change($booking_id, 'InProcess_Completed', $closing_remarks);
            
            if($is_update_spare_parts){
                
                $sp['status'] = DEFECTIVE_PARTS_PENDING;
                $this->service_centers_model->update_spare_parts(array('booking_id'=>$booking_id), $sp);
                redirect(base_url()."service_center/get_defective_parts_booking");
                
            } else {
                redirect(base_url() . "service_center/pending_booking");
            } 
        }
    }

    /**
     * @desc: This is used to get cancel booking form.
     * @param: booking id
     * @return: void
     */
    function cancel_booking_form($code) {
        log_message('info', __FUNCTION__ . " Booking ID: " . base64_decode(urldecode($code)));
        $this->checkUserSession();
        $booking_id = base64_decode(urldecode($code));
        $data['user_and_booking_details'] = $this->booking_model->getbooking_history($booking_id);
        $where = array('reason_of' => 'vendor');
        $data['reason'] = $this->booking_model->cancelreason($where);

        $this->load->view('service_centers/header');
        $this->load->view('service_centers/cancel_booking_form', $data);
    }

    /**
     * @desc: This is used to cancel booking for service center.
     * @param: booking id
     * @return: void
     */
    function process_cancel_booking($booking_id) {
        log_message('info', __FUNCTION__ . " Booking ID: " . $booking_id);
        $this->checkUserSession();
        $this->form_validation->set_rules('cancellation_reason', 'Cancellation Reason', 'required');

        if (($this->form_validation->run() == FALSE) || $booking_id =="" || $booking_id == NULL) {
            log_message('info', __FUNCTION__ . " Form validation failed Booking ID: " . $booking_id);
            $this->cancel_booking_form(urlencode(base64_encode($booking_id)));
        } else {
           
            $cancellation_reason = $this->input->post('cancellation_reason');
            $cancellation_text = $this->input->post('cancellation_reason_text');
            $can_state_change = $cancellation_reason;
            $partner_id = $this->input->post('partner_id');
            if(!empty($cancellation_text)){
                $can_state_change = $cancellation_reason." - ".$cancellation_text;
            }
            
            
            switch ($cancellation_reason){
                case PRODUCT_NOT_DELIVERED_TO_CUSTOMER :
                    //Called when sc choose Product not delivered to customer 
                    $this->convert_booking_to_query($booking_id,$partner_id);
                    
                    break;
                default :
                    $data['service_center_id'] = $this->session->userdata('service_center_id');
                   
                    $data['current_status'] = "InProcess";
                    $data['internal_status'] = "Cancelled";
                    $data['service_center_remarks'] = $cancellation_text;
                    $data['service_charge'] = $data['additional_service_charge'] = $data['parts_cost'] = $data['amount_paid'] = 0;
                    $data['cancellation_reason'] = $cancellation_reason;
                    $data['closed_date'] = date('Y-m-d H:i:s');
                    $data['update_date'] = date('Y-m-d H:i:s');

                    $this->vendor_model->update_service_center_action($booking_id, $data);

                    $this->insert_details_in_state_change($booking_id, 'InProcess_Cancelled', $can_state_change);
                    redirect(base_url() . "service_center/pending_booking");
                    break;
            }
        }
    }
    /**
     * @desc: This is used to convert booking into Query.
     * @param String $booking_id
     */
    function convert_booking_to_query($booking_id,$partner_id){
        log_message('info', __FUNCTION__ . " Booking ID: " . $booking_id);
        $booking['booking_id'] = "Q-".$booking_id;
        $booking['current_status'] = "FollowUp";
        $booking['type'] = "Query";
        $booking['internal_status'] = "FollowUp";
        $booking['assigned_vendor_id'] = NULL;
        $booking['assigned_engineer_id'] = NULL;
        $booking['mail_to_vendor'] = '0';
        $booking['booking_date'] = date('d-m-Y');
        
        //Get Partner 
        $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'],$partner_id, $booking['booking_id']);
        if(!empty($partner_status)){
            $booking['partner_current_status'] = $partner_status[0];
            $booking['partner_internal_status'] = $partner_status[1];
        }                
        //Update Booking unit details
        $this->booking_model->update_booking($booking_id, $booking);
        
        $unit_details['booking_id'] = "Q-".$booking_id;
        $unit_details['booking_status'] = "FollowUp";
        //update unit details
        $this->booking_model->update_booking_unit_details($booking_id, $unit_details);
        // Delete booking from sc action table
        $this->service_centers_model->delete_booking_id($booking_id);
        //Insert Data into Booking state change
        $this->insert_details_in_state_change($booking_id, PRODUCT_NOT_DELIVERED_TO_CUSTOMER, "Convert Booking to Query");
        redirect(base_url() . "service_center/pending_booking");  
    }

    /**
     * @desc: This function Sets Session
     * @param: Service center id
     * @param: Service center name
     * @param: Agent Id
     * @param: is update
     * @return: void
     */
    function setSession($service_center_id, $service_center_name, $sc_agent_id, $update, $is_upcountry) {
	$userSession = array(
	    'session_id' => md5(uniqid(mt_rand(), true)),
	    'service_center_id' => $service_center_id,
	    'service_center_name' => $service_center_name,
            'service_center_agent_id' => $sc_agent_id,
            'is_upcountry' => $is_upcountry,
            'is_update' => $update,
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
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'service_center') && !empty($this->session->userdata('service_center_id'))) {
            return TRUE;
        } else {
            log_message('info', __FUNCTION__. " Session Expire for Service Center");
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
        $this->checkUserSession();
       //Saving Login Details in Database
        $login_data['browser'] = $this->agent->browser();
        $login_data['ip'] = $this->session->userdata('ip_address');
        $login_data['action'] = _247AROUND_LOGOUT;
        $login_data['agent_string'] = $this->agent->agent_string();
        $login_data['entity_type'] = $this->session->userdata('userType');
        $login_data['entity_id'] = $this->session->userdata('service_center_id');
        $login_data['agent_id'] = $this->session->userdata('service_center_agent_id');

        $logout_id = $this->employee_model->add_login_logout_details($login_data);
        //Adding Log Details
        if ($logout_id) {
            log_message('info', __FUNCTION__ . ' Logging details have been captured for service center ' . $login_data['entity_id']);
        } else {
            log_message('info', __FUNCTION__ . ' Err in capturing logging details for service center ' . $login_data['entity_id']);
        }
        
        $this->session->sess_destroy();
        redirect(base_url() . "service_center");
    }

    /**
     * @desc: this is used to display completed booking for specific service center
     */
    function completed_booking($offset = 0, $page = 0, $booking_id=""){
        $this->checkUserSession();
    if ($page == 0) {
        $page = 50;
    }

        $service_center_id = $this->session->userdata('service_center_id');

        $offset = ($this->uri->segment(3) != '' ? $this->uri->segment(3) : 0);
        $config['base_url'] = base_url() . 'service_center/completed_booking';
        $config['total_rows'] = $this->service_centers_model->getcompleted_or_cancelled_booking("count","",$service_center_id,"Completed", $booking_id);

        $config['per_page'] = $page;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['bookings'] = $this->service_centers_model->getcompleted_or_cancelled_booking($config['per_page'], $offset, $service_center_id, "Completed", $booking_id);

        if ($this->session->flashdata('result') != '') {
            $data['success'] = $this->session->flashdata('result');
        }

        $data['status'] = "Completed";

        $this->load->view('service_centers/header');
        $this->load->view('service_centers/completed_booking', $data);

    }

    /**
     * @desc: this is used to display completed booking for specific service center
     */
    function cancelled_booking($offset = 0, $page = 0, $booking_id=""){
        $this->checkUserSession();
        if ($page == 0) {
            $page = 50;
        }

        $service_center_id = $this->session->userdata('service_center_id');

        $offset = ($this->uri->segment(3) != '' ? $this->uri->segment(3) : 0);
        $config['base_url'] = base_url() . 'service_center/cancelled_booking';
        $config['total_rows'] = $this->service_centers_model->getcompleted_or_cancelled_booking("count","",$service_center_id,"Cancelled", $booking_id);

        $config['per_page'] = $page;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['bookings'] = $this->service_centers_model->getcompleted_or_cancelled_booking($config['per_page'], $offset, $service_center_id, "Cancelled", $booking_id);

        if ($this->session->flashdata('result') != '') {
            $data['success'] = $this->session->flashdata('result');
        }

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
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . '=> Booking Id: '. $this->input->post('booking_id'));
        $this->form_validation->set_rules('booking_id', 'Booking ID', 'trim|required');
        $this->form_validation->set_rules('booking_date', 'Booking Date', 'required');
        $this->form_validation->set_rules('reason_text', 'Reascheduled Reason', 'trim');

        if ($this->form_validation->run() == FALSE ) {
             log_message('info', __FUNCTION__ . '=> Rescheduled Booking Validation failed ');
            echo "Please Select Rescheduled Date";
        } else {
            log_message('info', __FUNCTION__ . '=> Reascheduled Booking: ');
            $booking_id = $this->input->post('booking_id');
            $data['booking_date'] = date('Y-m-d',strtotime($this->input->post('booking_date')));
            $data['current_status'] = "InProcess";
            $data['internal_status'] = 'Reschedule';
            $reason = $this->input->post('reason');
            if(!empty($reason)){
                
                $data['reschedule_reason'] = $this->input->post('reason');
            } else {
                
                $data['reschedule_reason'] = $this->input->post('reason_text');
            }
           
            $data['update_date'] = date("Y-m-d H:i:s");
            $this->vendor_model->update_service_center_action($booking_id, $data);

            $this->insert_details_in_state_change($booking_id, "InProcess_Rescheduled", $data['reschedule_reason']);
           
            $userSession = array('success' => 'Booking Updated');
            $this->session->set_userdata($userSession);
            redirect(base_url() . "service_center/pending_booking");
            
        }
    }
    /**
     * @desc: This method is used to insert action log into state change table. 
     * Just pass booking id, new state and remarks as parameter
     * @param String $booking_id
     * @param String $new_state
     * @param String $remarks
     */
    function insert_details_in_state_change($booking_id, $new_state, $remarks){
           //Save state change
            $state_change['booking_id'] = $booking_id;
            $state_change['new_state'] =  $new_state;
           
            $booking_state_change = $this->booking_model->get_booking_state_change($state_change['booking_id']);
            
            if ($booking_state_change > 0) {
                $state_change['old_state'] = $booking_state_change[count($booking_state_change) - 1]['new_state'];
            } else { //count($booking_state_change)
                $state_change['old_state'] = "Pending";
            }
          
            $state_change['agent_id'] = $this->session->userdata('service_center_agent_id');
            $state_change['service_center_id'] = $this->session->userdata('service_center_id');
            $state_change['remarks'] = $remarks;

            // Insert data into booking state change
            $state_change_id = $this->booking_model->insert_booking_state_change($state_change);
            if($state_change_id){} else {
                log_message('info', __FUNCTION__ . '=> Booking details is not inserted into state change '. print_r($state_change_id, true));
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

    /**
     * @desc: This is used to update assigned engineer in booking details and insert data into state change and update sc sction table
     * Send SMS to Engineer
     * It gets input in Array Like Array([SY-199171609091] => 1(engineer id))
     * Insert data into Assigned Engineer or State change table
     */
    function assigned_engineers() {
    log_message('info', __FUNCTION__. " Service_center ID: ". $this->session->userdata('service_center_id'));
    $this->checkUserSession();
    $engineers_id_with_booking_id = $this->input->post('engineer');

    foreach ($engineers_id_with_booking_id as $booking_id => $engineer_id) {
        if (!empty($engineer_id)) {
                log_message('info', __FUNCTION__ . '=> Engineer ID: ' . $engineer_id . "Booking ID" . $booking_id);

                $data['assigned_engineer_id'] = $engineer_id;
                $data['internal_status'] = ENGG_ASSIGNED;
                // Update Assigned Engineer
                $updated_status = $this->booking_model->update_booking($booking_id, $data);
                if ($updated_status) {
                    // Update service center internal status in service center action table
                    $this->service_centers_model->update_service_centers_action_table($booking_id, array('internal_status' => ENGG_ASSIGNED, 'update_date' => date('Y-m-d H:i:s')));

                    $assigned['booking_id'] = $booking_id;
                    $assigned['current_state'] = ENGG_ASSIGNED;

                    // Check, Is engineer already installed
                    $is_engineer_assigned = $this->vendor_model->get_engineer_assigned($assigned);
                    if (!empty($is_engineer_assigned)) {

                        $assigned['current_state'] = RE_ASSIGNED_ENGINEER;
                    }

                    $assigned['engineer_id'] = $engineer_id;
                    $assigned['service_center_id'] = $this->session->userdata('service_center_id');
                    // Insert data into Assigned Engineer Table
                    $inserted_id = $this->vendor_model->insert_assigned_engineer($assigned);
                    if ($inserted_id) {
                        $this->insert_details_in_state_change($booking_id, $assigned['current_state'], "Engineer Id: " . $engineer_id);

                    } else { // if ($inserted_id) {
                        log_message('info', '=> Engineer details is not inserted into Assigned Engineer table: '
                                . $booking_id . " Data" . print_r($assigned, true));
                    }
                } else {
                    log_message('info', '=> Booking is not updated: ' . $booking_id);
                }
            }
        }
        // Send SMS to  Engineer to inform booking details                 
        $url = base_url() . "employee/do_background_process/send_sms_to_assigned_engineer";
        $send['booking_id_with_engineer_id'] = $engineers_id_with_booking_id;
        $this->asynchronous_lib->do_background_process($url, $send);
    }
    
    /**
     * @desc: This is used to load update form for service center
     * @param String Base_endode form - $booking_id
     */
    function update_booking_status($code) {
    log_message('info', __FUNCTION__ . " Booking ID: " . base64_decode(urldecode($code)));
        $this->checkUserSession();
        $booking_id = base64_decode(urldecode($code));
        $data['booking_id'] = $booking_id;
        $where_internal_status = array("page" => "update_sc", "active" => '1');
        
        $unit_details = $this->booking_model->get_unit_details(array('booking_id' => $booking_id));
        $data['bookinghistory'] = $this->booking_model->getbooking_history($booking_id);
        
        if(!empty($data['bookinghistory'][0])){
        
        $current_date = date_create(date('Y-m-d'));
        $current_booking_date = date_create(date('Y-m-d', strtotime($data['bookinghistory'][0]['booking_date'])));
       
        $date_diff = date_diff($current_date, $current_booking_date);
        // We will not display internal status after 1st day.
        if($date_diff->days <1){
            $data['internal_status'] = $this->booking_model->get_internal_status($where_internal_status);
            $data['days'] = 0;
            
        } else if($date_diff->days ===1){
            $data['days'] = $date_diff->days;
            $arr = array('status'=> CUSTOMER_NOT_REACHABLE);
            $data['internal_status']= Array((object) $arr);
           
        } else{
            $data['internal_status'] = array();
            $data['days'] = 0;
        }

        //IF spare parts is zero then we will not display spare parts checkbox.
        // Its check price tags. If Price tags is Repair then we will set spare_flag 1 and we will display spare parts checkbox. 
        $data['spare_flag'] = 0;
        //around_flag 1 means. This booking is our booking otherwise partner's booking 
        $data['around_flag'] = 0;
        foreach ($unit_details as $value) {
            if (stristr($value['price_tags'], "Repair")) {
                $data['spare_flag'] = 1;
            }
            // These all Partner id is 247around Id
            switch ($value['partner_id']) {
                case _247AROUND:
                case _247AROUND2:
                case _247AROUND3:
                case _247AROUND99:
                    $data['around_flag'] = 1;

                    break;
            }
        }

        $this->load->view('service_centers/header');
        $this->load->view('service_centers/get_update_form', $data);
        } else{
            echo "Booking Not Found. Please Retry Again";
        }
    }
    
    /**
     * @desc: This is used to update booking by SF. 
     *  IF Rescheduled option ( checkbox) is selected Then it perform save_reschedule_request Method 
     *  IF Spare Parts is selected then call update_spare_parts method
     *  Otherwise its get method name from table. If method name is not exist in the table default_update perform.  
     */
    function process_update_booking(){
       log_message('info', __FUNCTION__. " Service_center ID: ". $this->session->userdata('service_center_id')." Booking Id: ".  $this->input->post('booking_id'));
        // Check User Session
        $this->checkUserSession();
        // Check form validation
        $f_status = $this->checkvalidation_for_update_by_service_center();
        if($f_status){
            $reason = $this->input->post('reason');

            switch ($reason) {
                case CUSTOMER_ASK_TO_RESCHEDULE:
                    log_message('info', __FUNCTION__. CUSTOMER_ASK_TO_RESCHEDULE." Request: ". $this->session->userdata('service_center_id'));
                    $this->save_reschedule_request();
                    break;

                 case PRODUCT_NOT_DELIVERED_TO_CUSTOMER:
                    log_message('info', __FUNCTION__.PRODUCT_NOT_DELIVERED_TO_CUSTOMER. " Request: ". $this->session->userdata('service_center_id'));
                    $this->save_reschedule_request();
                    break;

                case SPARE_PARTS_REQUIRED:
                    log_message('info', __FUNCTION__. " Spare Parts Required Request: ". $this->session->userdata('service_center_id'));
                    $this->update_spare_parts();
                    break;

                 case CUSTOMER_NOT_REACHABLE:
                     log_message('info', __FUNCTION__. CUSTOMER_NOT_REACHABLE. $this->session->userdata('service_center_id'));
                        $day = $this->input->post('days');
                        if($day ==1){
                            $booking_id = $this->input->post('booking_id');
                            $_POST['cancellation_reason'] = CUSTOMER_NOT_REACHABLE;
                            $_POST['cancellation_reason_text'] = CUSTOMER_NOT_REACHABLE;
                            $this->process_cancel_booking($booking_id);

                        } else {
                            $this->default_update(true, true);
                        }
                        break;

                  case "Engineer on route":    
                      log_message('info', __FUNCTION__. "Engineer on route". $this->session->userdata('service_center_id'));
                      $this->default_update(true, true);
                      break;

            }
        } else {
            echo "Update Failed Please Retry Again";
        }
        
        log_message('info', __FUNCTION__. " Exit Service_center ID: ". $this->session->userdata('service_center_id'));
    }
    /**
     * @desc:
     * @param boolean $redirect
     * @param boolean $state_change
     */
    function default_update($redirect, $state_change){
        $this->checkUserSession();
        log_message('info', __FUNCTION__. " Service_center ID: ". $this->session->userdata('service_center_id')." Booking Id: ". 
        $this->input->post('booking_id'));
        $booking_id = $this->input->post('booking_id');
        $sc_data['internal_status'] =  $this->input->post('reason');
        $sc_data['current_status'] = 'InProcess';
        // Update Service center Action table
        $this->service_centers_model->update_service_centers_action_table($booking_id, $sc_data);
        if($state_change){
            // Insert data into state change
            $this->insert_details_in_state_change($booking_id, $sc_data['internal_status'], "" );
            // Send sms to customer while customer not reachable
            if($sc_data['internal_status'] == CUSTOMER_NOT_REACHABLE){
                log_message('info', __FUNCTION__." Send Sms to customer => Customer not reachable");
                $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
                $send['booking_id'] = $booking_id;
                $send['state'] = CUSTOMER_NOT_REACHABLE;
                $this->asynchronous_lib->do_background_process($url, $send);
            }
        }
        log_message('info', __FUNCTION__. " Exit Service_center ID: ". $this->session->userdata('service_center_id'));
        if ($redirect) {
            $userSession = array('success' => 'Booking Updated');
            $this->session->set_userdata($userSession);
            redirect(base_url() . "service_center/pending_booking");
        }
        
    }
    /**
     * 
     * @return boolean
     */
    function checkvalidation_for_update_by_service_center() {
	$this->checkUserSession();
	$this->form_validation->set_rules('booking_id', 'Booking Id', 'trim|required|xss_clean');
	$this->form_validation->set_rules('reason', 'Reason', 'trim|required');
	$this->form_validation->set_rules('reason_text', 'reason_text', 'trim|');
	if ($this->form_validation->run() == FALSE) {
            log_message('info', __FUNCTION__. " Service_center ID: ". $this->session->userdata('service_center_id'));
	    return FALSE;
	} else {
	    return true;
	}
    }
    
    /**
     * @desc: This is used to insert spare parts details into table provided by SF
     * IF Booking date is not empty means its 247Around booking. We reschedule that booking.
     */
    function update_spare_parts() {
        log_message('info', __FUNCTION__. " Service_center ID: ". $this->session->userdata('service_center_id')." Booking Id: ".  $this->input->post('booking_id'));
        $this->checkUserSession();
        $booking_id = $this->input->post('booking_id');
        $data['model_number'] = $this->input->post('model_number');
        $data['serial_number'] = $this->input->post('serial_number');
        $data['parts_requested'] = $this->input->post('parts_name');
        $data['date_of_purchase'] = $this->input->post('dop');
        $data['partner_id'] = $this->input->post('partner_id');
        $booking_date = $this->input->post('booking_date');
        $reason = $this->input->post('reason');

        if(isset($_FILES["invoice_image"])){
            $invoice_name = $this->upload_spare_pic($_FILES["invoice_image"], "Invoice");
            if (isset($invoice_name)) {
                $data['invoice_pic'] = $invoice_name;
            }
        }
        
        if(isset($_FILES["serial_number_pic"])){

            $serial_number_pic = $this->upload_spare_pic($_FILES["serial_number_pic"],"Serial_NO");
            if (isset($serial_number_pic)) {
                $data['serial_number_pic'] = $serial_number_pic;
            }
        }
        
         if(isset($_FILES["defective_parts_pic"])){

            $defective_parts_pic = $this->upload_spare_pic($_FILES["defective_parts_pic"],"Defective_Parts");
            if (isset($defective_parts_pic)) {
                $data['defective_parts_pic'] = $defective_parts_pic;
            }
        }
        

        $data['date_of_request'] = $data['create_date'] = date('Y-m-d H:i:s');
        $data['remarks_by_sc'] = $this->input->post('reason_text');
        $data['service_center_id'] = $this->session->userdata('service_center_id');
        $data['booking_id'] = $booking_id;
        $data['status'] = SPARE_PARTS_REQUESTED;
        $where = array('booking_id'=> $booking_id, 'service_center_id'=> $data['service_center_id']);
        $status_spare = $this->service_centers_model->spare_parts_action($where, $data);
        if($status_spare){

            $this->insert_details_in_state_change($booking_id, $reason, $data['remarks_by_sc']);

            $sc_data['current_status'] = "InProcess";
            $sc_data['internal_status'] = $reason;

            if($booking_date !=""){
                $sc_data['current_status'] = "Pending";
                $sc_data['booking_date'] = date('Y-m-d H:i:s',strtotime($booking_date));
                $sc_data['reschedule_reason'] = $data['remarks_by_sc'];
               // $sc_data['internal_status'] = 'Reschedule';
                 $booking['booking_date'] = date('d-m-Y',strtotime($booking_date));
                 $this->booking_model->update_booking($booking_id, $booking);
            }

            $sc_data['service_center_remarks'] = $data['remarks_by_sc'];
            $sc_data['update_date'] = date("Y-m-d H:i:s");

            $this->vendor_model->update_service_center_action($booking_id,$sc_data);

            $userSession = array('success' => 'Booking Updated');
            $this->session->set_userdata($userSession);
            redirect(base_url() . "service_center/pending_booking");
        } else { // if($status_spare){
            log_message('info', __FUNCTION__. " Not update Spare parts Service_center ID: ". $this->session->userdata('service_center_id'). " Data: ". print_r($data));
            
            $userSession = array('success' => 'Booking Not Updated');
            $this->session->set_userdata($userSession);
            redirect(base_url() . "service_center/pending_booking");
        }
        
         log_message('info', __FUNCTION__. " Exit Service_center ID: ". $this->session->userdata('service_center_id'));

    }
    /**
     * @esc: This method upload invoice image OR panel image to S3
     * @param _FILE $file
     * @return boolean|string
     */
     public function upload_spare_pic($file, $type) {
         log_message('info', __FUNCTION__. " Enterring Service_center ID: ". $this->session->userdata('service_center_id'));
        $this->checkUserSession();
	$allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
	$temp = explode(".", $file['name']);
	$extension = end($temp);
	//$filename = prev($temp);

	if ($file["name"] != null) {
	    if (($file["size"] < 2e+6) && in_array($extension, $allowedExts)) {
		if ($file["error"] > 0) {
		    $this->form_validation->set_message('upload_spare_pic', $file["error"]);
		} else {
		    $pic = str_replace(' ', '-', $this->input->post('booking_id'));
		    $picName = $type. $pic . "." . $extension;
		    $bucket = "bookings-collateral";
                    
		    $directory = "misc-images/" . $picName;
		    $this->s3->putObjectFile($file["tmp_name"], $bucket, $directory, S3::ACL_PUBLIC_READ);

		    return $picName;
		}
	    } else {
		$this->form_validation->set_message('upload_spare_pic', 'File size or file type is not supported. Allowed extentions are "png", "jpg", "jpeg" and "pdf". '
		    . 'Maximum file size is 2 MB.');
		return FALSE;
	    }
	}
        log_message('info', __FUNCTION__. " Exit Service_center ID: ". $this->session->userdata('service_center_id'));
    }
    
    /**
     * @desc: This is used to update acknowledge date by SF
     * @param String $booking_id
     */
    function acknowledge_delivered_spare_parts($booking_id, $service_center_id){
        log_message('info', __FUNCTION__. " Booking ID: ". $booking_id);
      //  $this->checkUserSession();
        if (!empty($booking_id)) {
           
            $where = array('booking_id' => $booking_id, 'service_center_id' => $service_center_id);
            $sp_data['service_center_id'] = $service_center_id;
            $sp_data['acknowledge_date'] = date('Y-m-d');
            $sp_data['status'] = "Delivered";
            //Update Spare Parts table
            $ss = $this->service_centers_model->spare_parts_action($where, $sp_data);
            if ($ss) { //if($ss){
                $booking['booking_date'] = date('d-m-Y', strtotime('+1 days'));
                $b_status = $this->booking_model->update_booking($booking_id, $booking);
                if ($b_status) {
                    $state_change['booking_id'] = $booking_id;
                    $state_change['new_state'] =  SPARE_PARTS_DELIVERED;
           
                    $booking_state_change = $this->booking_model->get_booking_state_change($state_change['booking_id']);
            
                    if ($booking_state_change > 0) {
                        $state_change['old_state'] = $booking_state_change[count($booking_state_change) - 1]['new_state'];
                    } else { //count($booking_state_change)
                        $state_change['old_state'] = "Pending";
                    }
          
                $state_change['agent_id'] = 1;
                $state_change['partner_id'] = _247AROUND;
                $state_change['remarks'] = "SF acknowledged to receive spare parts";

                // Insert data into booking state change
                $this->booking_model->insert_booking_state_change($state_change);

                    $sc_data['current_status'] = "Pending";
                    $sc_data['internal_status'] = SPARE_PARTS_DELIVERED;
                    $sc_data['update_date'] = date("Y-m-d H:i:s");
                    $this->vendor_model->update_service_center_action($booking_id, $sc_data);

                  //  $userSession = array('success' => 'Booking Updated');
                  //  $this->session->set_userdata($userSession);
                } else {//if ($b_status) {
                    
                        log_message('info', __FUNCTION__ . " Booking is not updated. Service_center ID: " 
                                . $service_center_id .
                                "Booking ID: " . $booking_id);
//                        $userSession = array('success' => 'Please Booking is not updated');
//                        $this->session->set_userdata($userSession);
                    }
                } else {
                    log_message('info', __FUNCTION__ . " Spare parts ack date is not updated Service_center ID: "
                            . $service_center_id .
                            "Booking ID: " . $booking_id);
//                    $userSession = array('success' => 'Please Booking is not updated');
//                    $this->session->set_userdata($userSession);
                }
            }
            log_message('info', __FUNCTION__. " Exit Service_center ID: ". $service_center_id);
           // redirect(base_url() . "service_center/pending_booking");

    }
    /**
     * @desc: This method called by Cron.
     * This method is used to convert Shipped spare part booking into Pending
     */
    function get_booking_id_to_convert_pending_for_spare_parts(){
        $data = $this->service_centers_model->get_booking_id_to_convert_pending_for_spare_parts();
        foreach($data as $value){
            $this->acknowledge_delivered_spare_parts($value['booking_id'], $value['service_center_id']);
        }
    }
    
    /**
     * @desc: This method is used to display whose booking updated by SC.
     */
    function convert_updated_booking_to_pending(){
        $this->service_centers_model->get_updated_booking_to_convert_pending();
        // Inserting values in scheduler tasks log
        $this->reporting_utils->insert_scheduler_tasks_log(__FUNCTION__);
        
    }
    
    /**
     * @desc: This is used to get search form in SC CRM
     * params: void
     * return: View form to find user
     */
    function get_search_form() {
        log_message('info', __FUNCTION__. "  Service_center ID: ". $this->session->userdata('service_center_id'));
        $this->checkUserSession();
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/search_form');
    }
    
    /**
     * @desc: SF search booking by Phone number or Booking id
     */
    function search(){
        log_message('info', __FUNCTION__ . "  Service_center ID: " . $this->session->userdata('service_center_id'));
        $this->checkUserSession();
        $searched_text = trim($this->input->post('searched_text'));
        $service_center_id = $this->session->userdata('service_center_id');
        $data['data'] = $this->service_centers_model->search_booking_history(trim($searched_text), $service_center_id);

        if (!empty($data['data'])) {
            $this->load->view('service_centers/header');
            $this->load->view('service_centers/bookinghistory', $data);
        } else {
            //if user not found set error session data
            $this->session->set_flashdata('error', 'Booking Not Found');

            redirect(base_url() . 'service_center/pending_booking');
        }
    }

    /**
     * @Desc: This function is used to download Pending Bookings Excel list
     * params: void
     * @return: void
     * 
     */
    function download_sf_pending_bookings_list_excel(){
        log_message('info', __FUNCTION__);
        //Getting Logged SF details
        $service_center_id = $this->session->userdata('service_center_id');
        //Getting Pending bookings for service center id
        $bookings = $this->service_centers_model->pending_booking($service_center_id, "");
        $booking_details = json_decode(json_encode($bookings[1]),true);
        $template = 'SF-Pending-Bookings-List-Template.xlsx';
        //set absolute path to directory with template files
        $templateDir = __DIR__ . "/../excel-templates/";
        //set config for report
        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );
        //load template
        $R = new PHPReport($config);
        
        $R->load(array(

                 'id' => 'bookings',
                'repeat' => TRUE,
                'data' => $booking_details
            ));
        
        $output_file_dir = TMP_FOLDER;
        $output_file = "SF-".$service_center_id."-Pending-Bookings-List-" . date('y-m-d');
        $output_file_name = $output_file . ".xls";
        $output_file_excel = $output_file_dir . $output_file_name;
        $R->render('excel2003', $output_file_excel);
        
        //Downloading File
        if(file_exists($output_file_excel)){

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header("Content-Disposition: attachment; filename=\"$output_file_name\""); 
            readfile($output_file_excel);
            exit;
        }

    }
    
    /**
     * @Desc: This function is used to download the SC charges excel
     * @params: void
     * @return: void
     * 
     */
    function download_sf_charges_excel(){
        log_message('info', __FUNCTION__.' Used by :'.$this->session->userdata('service_center_name'));
         $this->checkUserSession();
        //Getting SC ID from session
        $service_center_id  =  $this->session->userdata('service_center_id');
        if(!empty($service_center_id)){
            //Getting SF Details
            $sc_details = $this->vendor_model->getVendorContact($service_center_id);
            //Getting Charges Data
            $sc_charges_data = $this->service_centre_charges_model->get_service_centre_charges($sc_details[0]['state']);
            //Looping through all the values 
            foreach ($sc_charges_data as $value) {
                //Getting Details from Booking Sources
                $booking_sources = $this->partner_model->get_booking_sources_by_price_mapping_id($value['partner_id']);
                $code_source = $booking_sources[0]['code'];
                
                //Calculating vendor base charge 
                $vendor_base_charge = $value['vendor_total']/(1+($value['rate']/100));
                //Calculating vendor tax - [Vendor Total - Vendor Base Charge]
                $vendor_tax = $value['vendor_total'] - $vendor_base_charge;
                
                $array_final['sc_code'] = $code_source;
                $array_final['product'] = $value['product'];
                $array_final['category'] = $value['category'];
                $array_final['capacity'] = $value['capacity'];
                $array_final['service_category'] = $value['service_category'];
                $array_final['vendor_basic_charges'] = round($vendor_base_charge,0);
                $array_final['vendor_tax_basic_charges'] = round($vendor_tax,0);
                $array_final['vendor_total'] = round($value['vendor_total'],0);
                $array_final['customer_net_payable'] = round($value['customer_net_payable'],0);
                $array_final['pod'] = $value['pod'];
                
                $final_array[] = $array_final;
            }
            $data['final_array'] = $final_array;
            $this->load->view('service_centers/header');
            $this->load->view('service_centers/download_sf_charges_excel', $data);

        }else{
            echo 'Sorry, Session has expired, please log in again!';
        }
    }
    
    /**
     * @Desc: This function is used to show vendor details
     * @params: void
     * @return: void
     * 
     */
    function show_vendor_details(){
        $this->checkUserSession();
        log_message('info', __FUNCTION__.' Used by :'.$this->session->userdata('service_center_name'));
        $id = $this->session->userdata('service_center_id');
        if(!empty($id)){
            
            $query = $this->vendor_model->editvendor($id);

            $results['services'] = $this->vendor_model->selectservice();
            $results['brands'] = $this->vendor_model->selectbrand();
            $results['select_state'] = $this->vendor_model->getall_state();
            $results['employee_rm'] = $this->employee_model->get_rm_details();

            $appliances = $query[0]['appliances'];
            $selected_appliance_list = explode(",", $appliances);
            $brands = $query[0]['brands'];
            $selected_brands_list = explode(",", $brands);

            $rm = $this->vendor_model->get_rm_sf_relation_by_sf_id($id);

            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            $non_working_days = $query[0]['non_working_days'];
            $selected_non_working_days = explode(",", $non_working_days);
            $this->load->view('service_centers/header');

            $this->load->view('service_centers/show_vendor_details', array('query' => $query, 'results' => $results, 'selected_brands_list'
                => $selected_brands_list, 'selected_appliance_list' => $selected_appliance_list,
                'days' => $days, 'selected_non_working_days' => $selected_non_working_days,'rm'=>$rm));
            
        }else{
            echo 'Sorry, Session has Expired, Please Log In Again!';
        }
    }
    /**
     * @desc: This method is used to display list of booking which need to be ship defective parts by SF
     * @param Integer $offset
     */
    function get_defective_parts_booking($offset = 0){
        $this->checkUserSession();
        log_message('info', __FUNCTION__.' Used by :'.$this->session->userdata('service_center_name'));
        $service_center_id = $this->session->userdata('service_center_id');
        $where = "spare_parts_details.service_center_id = '".$service_center_id."' "
                . " AND status IN ('Delivered', '".DEFECTIVE_PARTS_PENDING."', '".DEFECTIVE_PARTS_REJECTED."')  ";
          
        $config['base_url'] = base_url() . 'service_center/get_defective_parts_booking';
        $total_rows = $this->partner_model->get_spare_parts_booking_list($where, false, false, false);
        $config['total_rows'] = $total_rows[0]['total_rows'];

        $config['per_page'] = 50;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['spare_parts'] = $this->partner_model->get_spare_parts_booking_list($where, $offset, $config['per_page'], true);
        
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/defective_parts', $data);
    }
    
    /**
     * @desc: This method is used to display list of booking which received by Partner
     * @param Integer $offset
     */
    function get_approved_defective_parts_booking($offset = 0){
        $this->checkUserSession();
        log_message('info', __FUNCTION__.' Used by :'.$this->session->userdata('service_center_name'));
        $service_center_id = $this->session->userdata('service_center_id');
        $where = "spare_parts_details.service_center_id = '".$service_center_id."' "
                . " AND approved_defective_parts_by_partner = '1' ";
          
        $config['base_url'] = base_url() . 'service_center/get_approved_defective_parts_booking';
        $total_rows = $this->partner_model->get_spare_parts_booking_list($where, false, false, false);
        $config['total_rows'] = $total_rows[0]['total_rows'];

        $config['per_page'] = 50;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['spare_parts'] = $this->partner_model->get_spare_parts_booking_list($where, $offset, $config['per_page'], true);
        
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/approved_defective_parts', $data);
    }
    /**
     * @desc: This method is used to load update form(defective shipped parts)
     * @param String $booking_id
     */
    function update_defective_parts($booking_id){
        $this->checkUserSession();
        log_message('info', __FUNCTION__.' Used by :'.$this->session->userdata('service_center_name'));
        $service_center_id = $this->session->userdata('service_center_id');
        
        $where = "spare_parts_details.service_center_id = '".$service_center_id."'  "
               . " AND spare_parts_details.booking_id = '".$booking_id."' ";
        $data['spare_parts'] = $this->partner_model->get_spare_parts_booking($where);
        if(!empty($data['spare_parts'])){
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/update_defective_spare_parts_form', $data);
        } else {
            echo "Please Try Again Later";
        }
    }
    /**
     * @desc: Process to update defective spare parts
     * @param type $booking_id
     */
    function process_update_defective_parts($booking_id){
        $this->checkUserSession();
        log_message('info', __FUNCTION__.' Used by :'.$this->session->userdata('service_center_name'));
        $this->form_validation->set_rules('defective_part_shipped', 'Parts Name', 'trim|required');
        $this->form_validation->set_rules('remarks_defective_part', 'Remarks', 'trim|required');
        $this->form_validation->set_rules('courier_name_by_sf', 'Courier Name', 'trim|required');
        $this->form_validation->set_rules('awb_by_sf', 'AWB', 'trim|required');
        $this->form_validation->set_rules('defective_part_shipped_date', 'AWB', 'trim|required');
        $this->form_validation->set_rules('courier_charges_by_sf', 'Courier Charges', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
             log_message('info', __FUNCTION__ . '=> Form Validation is not updated by Service center '. $this->session->userdata('service_center_name').
                        " booking id ". $booking_id. " Data". print_r($this->input->post(), true));
            $this->update_spare_parts_form($booking_id);
        } else {
            $service_center_id = $this->session->userdata('service_center_id');
            $data['defective_part_shipped'] = $this->input->post('defective_part_shipped');
            $data['remarks_defective_part_by_sf'] = $this->input->post('remarks_defective_part');
            $data['courier_name_by_sf'] = $this->input->post('courier_name_by_sf');
            $data['defective_part_shipped_date'] = $this->input->post('defective_part_shipped_date');
            $data['awb_by_sf'] = $this->input->post('awb_by_sf');
            $data['courier_charges_by_sf'] = $this->input->post('courier_charges_by_sf');
            $data['status'] = DEFECTIVE_PARTS_SHIPPED;
            $where  = array('booking_id'=> $booking_id, 'service_center_id'=> $service_center_id);
            $response = $this->service_centers_model->update_spare_parts($where, $data);
            if($response){
                
                $this->insert_details_in_state_change($booking_id, DEFECTIVE_PARTS_SHIPPED, $data['remarks_defective_part_by_sf']);
               
                $sc_data['current_status'] = "InProcess";
                $sc_data['update_date'] = date('Y-m-d H:i:s');
                $sc_data['internal_status'] = DEFECTIVE_PARTS_SHIPPED;
                $this->vendor_model->update_service_center_action($booking_id, $sc_data);
                
                $userSession = array('success' => 'Parts Updated');
                $this->session->set_userdata($userSession);
                redirect(base_url()."service_center/get_defective_parts_booking");
                
            } else {
                log_message('info', __FUNCTION__ . '=> Defective Spare parts booking is not updated by SF '. $this->session->userdata('service_center_name').
                        " booking id ". $booking_id. " Data". print_r($this->input->post(), true));
                $userSession = array('success' => 'Parts Not Updated');
                $this->session->set_userdata($userSession);
                redirect(base_url()."service_center/get_defective_parts_booking");
            }
            
        }
        
    }
    /**
     * @desc: This is used to print booking partner Address
     */
    function print_partner_address(){
        $this->checkUserSession();
        log_message('info', __FUNCTION__.' Used by :'.$this->session->userdata('service_center_name'));
        $booking_address = $this->input->post('download_address');
        $booking_history['details'] = array();
        $i=0;
        if(!empty($booking_address)){
            foreach ($booking_address as $partner_id=> $booking_id) {
                $booking_history['details'][$i]  = $this->partner_model->getpartner($partner_id)[0];
                $booking_history['details'][$i]['vendor'] = $this->vendor_model->getVendor($booking_id)[0];
                $booking_history['details'][$i]['booking_id'] = $booking_id;
                $i++;
            }
        }else{
           //Logging
            log_message('info',__FUNCTION__.' No Download Address from POST');
        }
        $this->load->view('service_centers/print_partner_address',$booking_history);
       
    }
    /**
     * @desc: Call by Ajax to load group upcountry details
     * @param String $booking_id
     */
    function pending_booking_upcountry_price($booking_id, $is_customer_paid){
        $this->checkUserSession();
        log_message('info', __FUNCTION__.' Used by :'.$this->session->userdata('service_center_name'));
        $service_center_id  = $this->session->userdata('service_center_id');
        if(empty($is_customer_paid)){
            $is_customer_paid = 0;
        }
        $data['data'] = $this->upcountry_model->upcountry_booking_list($service_center_id, $booking_id, true, $is_customer_paid);
       // $this->load->view('service_centers/header');
        $this->load->view('service_centers/upcountry_booking_details', $data);
    }
    
    
    /**
     * @Desc: This function is used to show brackets details list
     * @params: void
     * @return: void
     * 
     */
    function show_brackets_list($page = "",$offset=""){
        $this->checkUserSession();
        if ($page == 0) {
	    $page = 50;
	}
	// $offset = ($this->uri->segment(5) != '' ? $this->uri->segment(5) : 0);
   
	$config['base_url'] = base_url() . 'employee/service_centers/show_brackets_list/'.$page;
	$config['total_rows'] = $this->inventory_model->get_total_brackets_given_count($this->session->userdata('service_center_id'));
	
	if($offset != "All"){
		$config['per_page'] = $page;
	} else {
		$config['per_page'] = $config['total_rows'];
	}	
	
	$config['uri_segment'] = 5;
	$config['first_link'] = 'First';
	$config['last_link'] = 'Last';

	$this->pagination->initialize($config);
	$data['links'] = $this->pagination->create_links();
        $data['Count'] = $config['total_rows'];        
        $data['brackets'] = $this->inventory_model->get_total_brackets_given($config['per_page'], $offset,$this->session->userdata('service_center_id'));
        //Getting name for order received from  to vendor
        foreach($data['brackets'] as $key=>$value){
            $data['order_received_from'][$key] = $this->vendor_model->getVendorContact($value['order_received_from'])[0];
        
            // Getting name for order given to vendor
            
            $data['order_given_to'][$key] = $this->vendor_model->getVendorContact($value['order_given_to'])[0]['name'];
        }
        $this->load->view('service_centers/header');
        $this->load->view("service_centers/show_vender_brackets_list", $data);
    }
    
    /**
     * @Desc: This function is used to show brackets order history for order_id
     * @params: Int order_id
     * @return :View
     * 
     */
    function show_brackets_order_history($order_id){
        $data['data'] = $this->inventory_model->get_brackets_by_order_id($order_id);
        $data['order_id'] = $order_id;
        $data['brackets'] = $this->inventory_model->get_brackets_by_id($order_id);
        $data['order_received_from'] = $this->vendor_model->getVendorContact($data['brackets'][0]['order_received_from'])[0]['name'];
        $data['order_given_to'] = $this->vendor_model->getVendorContact($data['brackets'][0]['order_given_to'])[0]['name'];
        
        $this->load->view('service_centers/header');
        $this->load->view("service_centers/show_vender_brackets_order_history", $data);

    }
    
    /**
     * @Desc: This function is used to update shipment
     * @params: Int order id
     * @return : view
     */
    function get_update_shipment_form($order_id){
        $data['brackets'] = $this->inventory_model->get_brackets_by_id($order_id);
        $data['shipped_flag'] = TRUE;
        $data['order_id'] = $order_id;
        $data['order_given_to'] = $this->vendor_model->getVendorContact($data['brackets'][0]['order_given_to'])[0]['name'];
        $data['order_received_from'] = $this->vendor_model->getVendorContact($data['brackets'][0]['order_received_from'])[0]['name'];
        $this->load->view('service_centers/header');
        $this->load->view("service_centers/update_vender_brackets", $data);
    }
    
    /**
     * @Desc: This function is used to process update shipment form
     * @params: Array
     * @return: void
     */
    function process_vender_update_shipment_form(){
        //Saving Uploading file.
        if($_FILES['shipment_receipt']['error'] != 4 && !empty($_FILES['shipment_receipt']['tmp_name'])){
            $tmpFile = $_FILES['shipment_receipt']['tmp_name'];
            //Assigning File Name for uploaded shipment receipt
            $fileName = "Shipment-Receipt-".$this->input->post('order_id').'.'.explode('.',$_FILES['shipment_receipt']['name'])[1];
            move_uploaded_file($tmpFile, TMP_FOLDER.$fileName);
            
             //Uploading images to S3 
            $bucket = BITBUCKET_DIRECTORY;
            $directory = "misc-images/" . $fileName;
            $this->s3->putObjectFile(TMP_FOLDER.$fileName, $bucket, $directory, S3::ACL_PUBLIC_READ);
            
            $data['shipment_receipt'] = $fileName;
        }
        $order_id = $this->input->post('order_id');
        $order_received_from = $this->input->post('order_received_from');
        $data['19_24_shipped'] = $this->input->post('19_24_shipped');
        $data['26_32_shipped'] = $this->input->post('26_32_shipped');
        $data['36_42_shipped'] = $this->input->post('36_42_shipped');
        $data['43_shipped'] = $this->input->post('43_shipped');
        $data['total_shipped'] = $this->input->post('total_shipped');
        $data['shipment_date'] = !empty($this->input->post('shipment_date'))?$this->input->post('shipment_date'):date('Y-m-d H:i:s');
        $data['is_shipped'] = 1;
        
        
        $attachment = "";
        if(!empty($fileName)){
            $data['shipment_receipt'] = $fileName;
             $attachment = TMP_FOLDER.$fileName;
        }

        //Updating value in Brackets
        $update_brackets = $this->inventory_model->update_brackets($data, array('order_id' => $order_id));
        if($update_brackets){
            //Loggin success
            log_message('info',__FUNCTION__.' Brackets Shipped has been updated '. print_r($data, TRUE));
            
            //Adding value in Booking State Change
            $this->insert_details_in_state_change($order_id, "Brackets_Shipped", "Brackets Shipped");    
            //$this->notify->insert_state_change($order_id, _247AROUND_BRACKETS_SHIPPED, _247AROUND_BRACKETS_PENDING, "Brackets Shipped", $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);
            //Logging Success
            log_message('info', __FUNCTION__ . ' Brackets Pending - Shipped state have been added in Booking State Change ');
                
            // Sending mail to order_received_from vendor
            $order_received_from_email = $this->vendor_model->getVendorContact($order_received_from);
            $vendor_poc_mail = $order_received_from_email[0]['primary_contact_email'];
            $vendor_owner_mail = $order_received_from_email[0]['owner_email'];
            $to = $vendor_poc_mail.','.$vendor_owner_mail;
            
             // Sending brackets Shipped Mail to order received from vendor
                   $email = array();
                   //Getting template from Database
                   $template = $this->booking_model->get_booking_email_template("brackets_shipment_mail");
                   
                   if(!empty($template)){
                        $email['order_id'] = $order_id;
                        $subject = "Brackets Shipped by ".$order_received_from_email[0]['company_name'];
                        $emailBody = vsprintf($template[0], $email);
                        $this->notify->sendEmail($template[2], $to , $template[3].','.$this->get_rm_email($order_received_from), '', $subject , $emailBody, $attachment);
                   }
            
            //Loggin send mail success
            log_message('info',__FUNCTION__.' Shipped mail has been sent to order_received_from vendor '. $emailBody);
            
            //Setting success session data 
            $this->session->set_userdata('brackets_update_success', 'Brackets Shipped updated Successfully');
            
            redirect(base_url() . 'employee/service_centers/show_brackets_list');
        }else{
            //Loggin error
            log_message('info',__FUNCTION__.' Brackets Shipped updated Error '. print_r($data, TRUE));
            
            //Setting error session data 
            $this->session->set_userdata('brackets_update_error', 'No changes made to be updated.');
            $this->get_update_shipment_form($order_id);
        }
    }
    
    /**
     * @Desc: This function is used to get RM email (:POC) details for the corresponding vendor 
     * @params: vendor 
     * @return : string
     */
    private function get_rm_email($vendor_id) {
        $employee_rm_relation = $this->vendor_model->get_rm_sf_relation_by_sf_id($vendor_id);
        $rm_id = $employee_rm_relation[0]['agent_id'];
        $rm_details = $this->employee_model->getemployeefromid($rm_id);
        $rm_poc_email = $rm_details[0]['official_email'];
        return $rm_poc_email;
    }
    
            }
