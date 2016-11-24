<?php

if (!defined('BASEPATH')){
    exit('No direct script access allowed');
}

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
        $this->load->model('user_model');
        $this->load->model('invoices_model');
        $this->load->library("pagination");
        $this->load->library('asynchronous_lib');
        $this->load->library("session");
        $this->load->library('s3');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('PHPReport');
        $this->load->helper('download');
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
            $this->setSession($sc_details[0]['id'], $sc_details[0]['name'], $agent['id'], $sc_details[0]['is_update']);

	    redirect(base_url() . "service_center/pending_booking");
        } else {
            $userSession = array('error' => 'Please enter correct user name and password' );
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
        $service_center_id = $this->session->userdata('service_center_id');
        $data['bookings'] = $this->service_centers_model->pending_booking($service_center_id, $booking_id);
        $data['eraned_details'] =  $this->service_centers_model->get_sc_earned($service_center_id);
        $data['cancel_booking'] = $this->service_centers_model->count_cancel_booking_sc($service_center_id);
        if($this->session->userdata('is_update') == 1){
        //$data['engineer_details'] = $this->vendor_model->get_engineers($service_center_id);
        $data['spare_parts_data'] = $this->service_centers_model->get_updated_spare_parts_booking($service_center_id);
        
        }

        $this->load->view('service_centers/header');
        $this->load->view('service_centers/pending_booking', $data);
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

        if (($this->form_validation->run() == FALSE) || ($booking_id =="") || (is_null($booking_id))) {
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

                 $this->vendor_model->update_service_center_action($data);

            }

             $state_change['booking_id'] = $booking_id;
             $state_change['new_state'] = 'InProcess_Completed';
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
            $this->cancel_booking_form($booking_id);
        } else {
           
            $cancellation_reason = $this->input->post('cancellation_reason');
            $cancellation_text = $this->input->post('cancellation_reason_text');
            
            switch ($cancellation_reason){
                case PRODUCT_NOT_DELIVERED_TO_CUSTOMER :
                    //Called when sc choose Product not delivered to customer 
                    $this->convert_booking_to_query($booking_id);
                    
                    break;
                default :
                    $data['service_center_id'] = $this->session->userdata('service_center_id');
                    $data['booking_id'] = $booking_id;
                    $data['current_status'] = "InProcess";
                    $data['internal_status'] = "Cancelled";
                    $data['service_center_remarks'] = $cancellation_text;
                    $data['service_charge'] = $data['additional_service_charge'] = $data['parts_cost'] = $data['amount_paid'] = 0;
                    $data['cancellation_reason'] = $cancellation_reason;
                    $data['closed_date'] = date('Y-m-d H:i:s');

                    $this->vendor_model->update_service_center_action($data);

                    $this->insert_details_in_state_change($booking_id, 'InProcess_Cancelled', $data['cancellation_reason']);
                    redirect(base_url() . "service_center/pending_booking");
                    break;
            }
        }
    }
    /**
     * @desc: This is used to convert booking into Query.
     * @param String $booking_id
     */
    function convert_booking_to_query($booking_id){
        log_message('info', __FUNCTION__ . " Booking ID: " . $booking_id);
        $booking['booking_id'] = "Q-".$booking_id;
        $booking['current_status'] = "FollowUp";
        $booking['type'] = "Query";
        $booking['internal_status'] = "FollowUp";
        $booking['assigned_vendor_id'] = NULL;
        $booking['assigned_engineer_id'] = NULL;
        $booking['mail_to_vendor'] = '0';
        $booking['booking_date'] = date('d-m-Y');
        //Update Booking unit details
        $this->booking_model->update_booking($booking_id, $booking);
        
        $unit_details['booking_id'] = "Q-".$booking_id;
        //update unit details
        $this->booking_model->update_booking_unit_details($booking_id, $unit_details);
        // Delete booking from sc action table
        $this->service_centers_model->delete_booking_id($booking_id);
        //Insert Data into Booking state change
        $this->insert_details_in_state_change($booking_id, PRODUCT_NOT_DELIVERED_TO_CUSTOMER, PRODUCT_NOT_DELIVERED_TO_CUSTOMER);
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
    function setSession($service_center_id, $service_center_name, $sc_agent_id, $update) {
	$userSession = array(
	    'session_id' => md5(uniqid(mt_rand(), true)),
	    'service_center_id' => $service_center_id,
	    'service_center_name' => $service_center_name,
            'service_center_agent_id' => $sc_agent_id,
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
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'service_center')) {
            return TRUE;
        } else {
            log_message('info', __FUNCTION__. " Session Expire Service_center ID: ". $this->session->userdata('service_center_id'));
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
        log_message('info', __FUNCTION__ . '=> Booking Id: '. $this->input->post('booking_id'));
        $this->form_validation->set_rules('booking_id', 'Booking ID', 'trim|required');
        $this->form_validation->set_rules('booking_date', 'Booking Date', 'required');
        $this->form_validation->set_rules('reason_text', 'Reascheduled Reason', 'trim');

        if ($this->form_validation->run() == FALSE ) {
             log_message('info', __FUNCTION__ . '=> Rescheduled Booking Validation failed ');
            echo "Please Select Rescheduled Date";
        } else {
            log_message('info', __FUNCTION__ . '=> Reascheduled Booking: ');
            $data['booking_id'] = $this->input->post('booking_id');
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
            $this->vendor_model->update_service_center_action($data);

            $this->insert_details_in_state_change($data['booking_id'], "InProcess_Rescheduled", $data['reschedule_reason']);
           
            $userSession = array('success' => 'Booking Updated');
            $this->session->set_userdata($userSession);
            redirect(base_url() . "service_center/pending_booking");
            
        }
    }
    
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
        $this->checkvalidation_for_update_by_service_center();
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
            default :
                // this get method name and redirect url from database and call it other wise call default method
            log_message('info', __FUNCTION__. " Default ". $this->session->userdata('service_center_id'));
//                $where_updation = array('status' => $reason, 'active' => 1, 'sf_update_active' => 1);
//                $get_status_details = $this->booking_model->get_internal_status($where_updation);
//
//                if ($get_status_details[0]->method_name) {
//                    $method_name = explode(",", $get_status_details[0]->method_name);
//                    $redirect_url = explode(",", $get_status_details[0]->redirect_url);
//                    foreach ($method_name as $value) {
//
//                        if ($get_status_details[0]->redirect_url == 0) {
//                            $redirect_url = FALSE;
//                            
//                        } else if ($get_status_details[0]->redirect_url) {
//                            $redirect_url = TRUE;
//                        }
//                      
//                        $this->$value($redirect_url);
//                    }
//                } else { //$get_status_details
//                   
//                    $this->default_update(true, true);
//                }
                switch ($reason){
                    case CUSTOMER_NOT_REACHABLE:
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
                    default :
                        $this->default_update(true, true);
                        break;
                }
                
                break;
        }
        
        log_message('info', __FUNCTION__. " Exit Service_center ID: ". $this->session->userdata('service_center_id'));
    }
    /**
     * @desc:
     * @param boolean $redirect
     * @param boolean $state_change
     */
    function default_update($redirect, $state_change){
        log_message('info', __FUNCTION__. " Service_center ID: ". $this->session->userdata('service_center_id')." Booking Id: ". 
                $this->input->post('booking_id'));
        $sc_data['booking_id'] = $this->input->post('booking_id');
        $sc_data['internal_status'] =  $this->input->post('reason');
        $sc_data['current_status'] = 'InProcess';
        // Update Service center Action table
        $this->service_centers_model->update_service_centers_action_table($sc_data['booking_id'], $sc_data);
        if($state_change){
            // Insert data into state change
            $this->insert_details_in_state_change($sc_data['booking_id'], $sc_data['internal_status'], "" );
            // Send sms to customer while customer not reachable
            if($sc_data['internal_status'] == CUSTOMER_NOT_REACHABLE){
                log_message('info', __FUNCTION__." Send Sms to customer => Customer not reachable");
                $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
                $send['booking_id'] = $sc_data['booking_id'];
                $send['state'] = CUSTOMER_NOT_REACHABLE;
                $this->asynchronous_lib->do_background_process($url, $send);
            }
        }
        
        if ($redirect) {
            $userSession = array('success' => 'Booking Updated');
            $this->session->set_userdata($userSession);
            redirect(base_url() . "service_center/pending_booking");
        }
        log_message('info', __FUNCTION__. " Exit Service_center ID: ". $this->session->userdata('service_center_id'));
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
            $invoice_name = $this->upload_spare_pic($_FILES["invoice_image"]);
            if (isset($invoice_name)) {
                $data['invoice_pic'] = $invoice_name;
            }
        }
        
        if(isset($_FILES["panel_pic"])){

            $panel_pic = $this->upload_spare_pic($_FILES["panel_pic"]);
            if (isset($panel_pic)) {
                $data['panel_pic'] = $panel_pic;
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

            $sc_data['booking_id'] = $booking_id;
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

            $this->vendor_model->update_service_center_action($sc_data);

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
     public function upload_spare_pic($file) {
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
		    $picName = $pic . "." . $extension;
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
    function acknowledge_delivered_spare_parts($booking_id){
        log_message('info', __FUNCTION__. "  Service_center ID: ". $this->session->userdata('service_center_id'). " Booking ID: ". $booking_id);
        $this->checkUserSession();
        if (!empty($booking_id)) {
            $service_center_id = $this->session->userdata('service_center_id');
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
                    $this->insert_details_in_state_change($booking_id, SPARE_PARTS_DELIVERED, "SF acknowledged to receive spare parts");
                    $sc_data['booking_id'] = $booking_id;
                    $sc_data['current_status'] = "Pending";
                    $sc_data['internal_status'] = SPARE_PARTS_DELIVERED;
                    $this->vendor_model->update_service_center_action($sc_data);

                    $userSession = array('success' => 'Booking Updated');
                    $this->session->set_userdata($userSession);
                } else {//if ($b_status) {
                    
                        log_message('info', __FUNCTION__ . " Booking is not updated. Service_center ID: " 
                                . $this->session->userdata('service_center_id') .
                                "Booking ID: " . $booking_id);
                        $userSession = array('success' => 'Please Booking is not updated');
                        $this->session->set_userdata($userSession);
                    }
                } else {
                    log_message('info', __FUNCTION__ . " Spare parts ack date is not updated Service_center ID: "
                            . $this->session->userdata('service_center_id') .
                            "Booking ID: " . $booking_id);
                    $userSession = array('success' => 'Please Booking is not updated');
                    $this->session->set_userdata($userSession);
                }
            }
            log_message('info', __FUNCTION__. " Exit Service_center ID: ". $this->session->userdata('service_center_id'));
            redirect(base_url() . "service_center/pending_booking");

    }
    /**
     * @desc: This method is used to display whose booking updated by SC.
     */
    function convert_updated_booking_to_pending(){
        $this->service_centers_model->get_updated_booking_to_convert_pending();
        
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
        $data['data'] = $this->service_centers_model->search_booking_history($searched_text, $service_center_id);

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
        
        $output_file_dir = "/tmp/";
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




}
