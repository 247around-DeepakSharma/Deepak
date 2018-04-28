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
        $this->load->model('dealer_model');
        $this->load->model('partner_model');
        $this->load->model('upcountry_model');
        $this->load->model('vendor_model');
        $this->load->model('user_model');
        $this->load->model('employee_model');
        $this->load->model('invoices_model');
        $this->load->model('penalty_model');
        $this->load->model('inventory_model');
        $this->load->model('cp_model');
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
        $this->load->library('buyback');
        $this->load->library("partner_cb");
        $this->load->library("miscelleneous");
        $this->load->library("push_notification_lib");
        $this->load->library("paytm_payment_lib");
    }

    /**
     * @desc: This is used to load vendor Login Page
     *
     * @param: void
     * @return: void
     */
    function index() {
        $select = "partner_logo,alt_text";
        $where = array('partner_logo IS NOT NULL' => NULL);
        $data['partner_logo'] = $this->booking_model->get_partner_logo($select, $where);
        $this->load->view('service_centers/service_center_login' ,$data);
    }

    /**
     * @desc: this is used to load pending booking
     * @param: booking id (optional)
     * @return: void
     */
    function pending_booking($booking_id="") {
        $this->checkUserSession();
        $data['booking_id'] = $booking_id;
        $rating_data = $this->service_centers_model->get_vendor_rating_data($this->session->userdata('service_center_id'));
        if(!empty($rating_data[0]['rating'])){
            $data['rating'] =  $rating_data[0]['rating'];
            $data['count'] =  $rating_data[0]['count'];
        }else{
            $data['rating'] = 0;
            $data['count'] =  $rating_data[0]['count'];
        }
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/pending_booking', $data);
        if(!$this->session->userdata("login_by")){
            $this->load->view('employee/header/push_notification');
        }
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
        //$data['collateral'] = $this->service_centers_model->get_collateral_for_service_center_bookings($service_center_id);
        $data['service_center_id'] = $service_center_id;
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
        $unit_where = array('booking_id'=>$booking_id, 'pay_to_sf' => '1');
        $booking_unit_details = $this->booking_model->get_unit_details($unit_where);
        $data['booking_state_change_data'] = $this->booking_model->get_booking_state_change_by_id($booking_id);
        $data['sms_sent_details'] = $this->booking_model->get_sms_sent_details($booking_id);

        if (!is_null($data['booking_history'][0]['sub_vendor_id'])) {
            $data['dhq'] = $this->upcountry_model->get_sub_service_center_details(array('id' => $data['booking_history'][0]['sub_vendor_id']));
        }
        $engineer_action_not_exit = false;
        if($this->session->userdata('is_engineer_app') == 1){
            foreach($booking_unit_details as $key1 => $b){

                    $unitWhere = array("engineer_booking_action.booking_id" => $booking_id, 
                        "engineer_booking_action.unit_details_id" => $b['id'], "service_center_id" => $data['booking_history'][0]['assigned_vendor_id']);
                    $en = $this->engineer_model->getengineer_action_data("engineer_booking_action.*", $unitWhere);
                    if(!empty($en)){
                        $booking_unit_details[$key1]['en_serial_number'] = $en[0]['serial_number'];
                        $booking_unit_details[$key1]['en_serial_number_pic'] = $en[0]['serial_number_pic'];
                        $booking_unit_details[$key1]['en_is_broken'] = $en[0]['is_broken'];
                        $booking_unit_details[$key1]['en_internal_status'] = $en[0]['internal_status'];
                        $booking_unit_details[$key1]['en_current_status'] = $en[0]['current_status'];

                        $engineer_action_not_exit = true;
                    } 
            }
            if(isset($engineer_action_not_exit)){
                $sig_table = $this->engineer_model->getengineer_sign_table_data("*", array("booking_id" => $booking_id,
                "service_center_id" => $data['booking_history'][0]['assigned_vendor_id']));
                $data['signature_details'] = $sig_table;
            }
        }
        
        $isPaytmTxn = $this->paytm_payment_lib->get_paytm_transaction_data($booking_id);
        if(!empty($isPaytmTxn)){
            if($isPaytmTxn['status']){
                $data['booking_history'][0]['onlinePaymentAmount'] = $isPaytmTxn['total_amount'];
            }
        }
        
        $data['engineer_action_not_exit'] = $engineer_action_not_exit;
        
        $data['unit_details'] = $booking_unit_details;
        $data['penalty'] = $this->penalty_model->get_penalty_on_booking_by_booking_id($booking_id);
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
        $bookng_unit_details = $this->booking_model->getunit_details($booking_id);
        if($this->session->userdata('is_engineer_app') == 1){
        foreach($bookng_unit_details as $key1 => $b){
           $broken = 0;
                foreach ($b['quantity'] as $key2 => $u) {

                    $unitWhere = array("engineer_booking_action.booking_id" => $booking_id, 
                        "engineer_booking_action.unit_details_id" => $u['unit_id'], "service_center_id" => $data['booking_history'][0]['assigned_vendor_id']);
                    $en = $this->engineer_model->getengineer_action_data("engineer_booking_action.*", $unitWhere);
                    if(!empty($en)){
                        $bookng_unit_details[$key1]['quantity'][$key2]['en_serial_number'] = $en[0]['serial_number'];
                        $bookng_unit_details[$key1]['quantity'][$key2]['en_serial_number_pic'] = $en[0]['serial_number_pic'];
                        $bookng_unit_details[$key1]['quantity'][$key2]['en_is_broken'] = $en[0]['is_broken'];
                        $bookng_unit_details[$key1]['quantity'][$key2]['en_internal_status'] = $en[0]['internal_status'];
                        if($en[0]['is_broken'] == 1){
                            $broken = 1;
                        }
                    }
                    
                }
               $bookng_unit_details[$key1]['is_broken'] = $broken; 
                 
            }
            $sig_table = $this->engineer_model->getengineer_sign_table_data("*", array("booking_id" => $booking_id, 
                "service_center_id" => $data['booking_history'][0]['assigned_vendor_id']));
            if(!empty($sig_table)){
                $data['signature'] = $sig_table[0]['signature'];
                $data['amount_paid'] = $sig_table[0]['amount_paid'];
                $data['mismatch_pincode'] = $sig_table[0]['mismatch_pincode'];
            }
            
        }
        
        $isPaytmTxn = $this->paytm_payment_lib->get_paytm_transaction_data($booking_id);
        if(!empty($isPaytmTxn)){
            if($isPaytmTxn['status']){
                $data['booking_history'][0]['onlinePaymentAmount'] = $isPaytmTxn['total_amount'];
            }
        }
        
        $data['bookng_unit_details'] = $bookng_unit_details;
        
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/complete_booking_form', $data);
    }

    /**
     * @desc: This is used to complete the booking once all the required details are filled.
     * @param: booking id
     * @return: void
     */
    function process_complete_booking($booking_id) {
        log_message('info', __FUNCTION__ . ' booking_id: ' . $booking_id);
        $this->checkUserSession();

        $this->form_validation->set_rules('customer_basic_charge', 'Basic Charge', 'required');
        $this->form_validation->set_rules('additional_charge', 'Additional Service Charge', 'required');
        $this->form_validation->set_rules('parts_cost', 'Parts Cost', 'required');
        $this->form_validation->set_rules('booking_status', 'Status', 'required');
        $this->form_validation->set_rules('pod', 'POD ', 'callback_validate_serial_no');

        if (($this->form_validation->run() == FALSE) || ($booking_id == "") || (is_null($booking_id))) {
            $this->complete_booking_form(urlencode(base64_encode($booking_id)));
        } else {

            $booking_state_change = $this->booking_model->get_booking_state_change($booking_id);
            $old_state = $booking_state_change[count($booking_state_change) - 1]['new_state'];
            
            if (!in_array($old_state, array(SF_BOOKING_COMPLETE_STATUS,_247AROUND_COMPLETED))) {
                // customer paid basic charge is comming in array
                // Array ( [100] =>  500 , [102] =>  300 )  
                $customer_basic_charge = $this->input->post('customer_basic_charge');
                // Additional service charge is comming in array
                $additional_charge = $this->input->post('additional_charge');
                // Parts cost is comming in array
                $parts_cost = $this->input->post('parts_cost');
                $booking_status = $this->input->post('booking_status');
                $total_amount_paid = $this->input->post('grand_total_price');
                $closing_remarks = $this->input->post('closing_remarks');
                $serial_number = $this->input->post('serial_number');
                $spare_parts_required = $this->input->post('spare_parts_required');
                $upcountry_charges = $this->input->post("upcountry_charges");
                $serial_number_pic = $this->input->post("serial_number_pic");
                $broken = $this->input->post("appliance_broken");
                $mismatch_pincode = $this->input->post("mismatch_pincode");
                $is_update_spare_parts = FALSE;
                $sp_required_id = json_decode($this->input->post("sp_required_id"), true);

                //$internal_status = "Cancelled";
                $getremarks = $this->booking_model->getbooking_charges($booking_id);
                $approval = $this->input->post("approval");
                $i = 0;

                foreach ($customer_basic_charge as $unit_id => $value) {
                    // variable $unit_id  is existing id in booking unit details table of given booking id 
                    $data = array();
                    $data['unit_details_id'] = $unit_id;
                    $data['closed_date'] = date('Y-m-d H:i:s');
                    $data['is_broken'] = $broken[$unit_id];
                    $data['mismatch_pincode'] = $mismatch_pincode;

//                 if(!empty($approval)){
//                    $unitWhere = array("engineer_booking_action.booking_id" => $booking_id, "engineer_booking_action.unit_details_id" => $unit_id);
//                    $en = $this->engineer_model->get_engineer_action_table_list($unitWhere, "engineer_booking_action.*");
//                   
//                    $data['is_broken'] = $en[0]->is_broken;
//                    //$data['closed_date'] = $en[0]->closed_date;
//                    
//                 }
                    $data['service_charge'] = $value;
                    $data['additional_service_charge'] = $additional_charge[$unit_id];
                    $data['parts_cost'] = $parts_cost[$unit_id];
                    if ($booking_status[$unit_id] == _247AROUND_COMPLETED && $spare_parts_required == 1) {
                        if ($this->session->userdata('is_engineer_app') == 1) {
                            $unitWhere1 = array("engineer_booking_action.booking_id" => $booking_id, "engineer_booking_action.unit_details_id" => $unit_id);
                            $this->engineer_model->update_engineer_table(array("current_status" => _247AROUND_COMPLETED, "internal_status" => _247AROUND_COMPLETED), $unitWhere1);
                        }
                        $data['internal_status'] = DEFECTIVE_PARTS_PENDING;
                        $is_update_spare_parts = TRUE;
                    } else {
                        $data['internal_status'] = $booking_status[$unit_id];
                        if ($this->session->userdata('is_engineer_app') == 1) {
                            $unitWhere1 = array("engineer_booking_action.booking_id" => $booking_id, "engineer_booking_action.unit_details_id" => $unit_id);
                            $this->engineer_model->update_engineer_table(array("current_status" => $booking_status[$unit_id], "internal_status" => $booking_status[$unit_id]), $unitWhere1);
                        }
                    }
                    $data['current_status'] = "InProcess";

                    $data['booking_id'] = $booking_id;
                    $data['amount_paid'] = $total_amount_paid;
                    $data['update_date'] = date("Y-m-d H:i:s");
                    if ($i == 0) {
                        $data['upcountry_charges'] = $upcountry_charges;
                    }
                    if (isset($serial_number[$unit_id])) {
                        $data['serial_number'] = trim($serial_number[$unit_id]);
                        $data['serial_number_pic'] = trim($serial_number_pic[$unit_id]);
                    }

                    if (!empty($getremarks[0]['service_center_remarks'])) {

                        $data['service_center_remarks'] = date("F j") . ":- " . $closing_remarks . " " . $getremarks[0]['service_center_remarks'];
                    } else {
                        if (!empty($closing_remarks)) {
                            $data['service_center_remarks'] = date("F j") . ":- " . $closing_remarks;
                        }
                    }
                    $i++;
                    $this->vendor_model->update_service_center_action($booking_id, $data);
                }
                //Send Push Notification to clouser group
                $clouserAccountArray = array();
                $getClouserAccountHolderID = $this->reusable_model->get_search_result_data("employee", "id", array("groups" => "closure"), NULL, NULL, NULL, NULL, NULL, array());
                foreach ($getClouserAccountHolderID as $employeeID) {
                    $clouserAccountArray['employee'][] = $employeeID['id'];
                }
                $textArray['msg'] = array($booking_id, $this->session->userdata('service_center_name'));
                $textArray['title'] = array($this->session->userdata('service_center_name'), $booking_id);
                $this->push_notification_lib->create_and_send_push_notiifcation(CUSTOMER_UPDATE_BOOKING_PUSH_NOTIFICATION_EMPLOYEE_TAG, $clouserAccountArray, $textArray);
                //End Push Notification
                //Update Service Center Closed Date in booking Details Table, 
                //if current date time is before 12PM then take completion date before a day, 
                //if day is monday and  time is before 12PM then take completion date as saturday
                //Check if new completion date is equal to or greater then booking_date
                date_default_timezone_set('Asia/Kolkata');
                // get booking_date
                $booking_date = $this->reusable_model->get_search_result_data("booking_details", 'STR_TO_DATE(booking_details.booking_date,"%d-%m-%Y") as booking_date', array('booking_id' => $booking_id), NULL, NULL, NULL, NULL, NULL, array())[0]['booking_date'];
                $bookingData['service_center_closed_date'] = date('Y-m-d H:i:s');
                // If time is before 12 PM then completion date will be yesturday's date
                if (date('H') < 13) {
                    $bookingData['service_center_closed_date'] = date('Y-m-d H:i:s', (strtotime('-1 day', strtotime(date('Y-m-d H:i:s')))));
                    $dayofweek = date('w', strtotime(date('Y-m-d H:i:s')));
                    // If day is monday then completion date will be saturday's date
                    if ($dayofweek == '1') {
                        $bookingData['service_center_closed_date'] = date('Y-m-d H:i:s', (strtotime('-2 day', strtotime(date('Y-m-d H:i:s')))));
                    }
                }
                $booking_timeStamp = strtotime($booking_date);
                $close_timeStamp = strtotime($bookingData['service_center_closed_date']);
                $datediff = $close_timeStamp - $booking_timeStamp;
                $booking_date_days = round($datediff / (60 * 60 * 24)) - 1;
                if($booking_date_days >= 0){
                    $bookingData['service_center_closed_date'] = date('Y-m-d H:i:s');
                }
                $this->reusable_model->update_table("booking_details", $bookingData, array('booking_id' => $booking_id));
                //End Update Service Center Closed Date
                // Insert data into booking state change
                $this->insert_details_in_state_change($booking_id, SF_BOOKING_COMPLETE_STATUS, $closing_remarks, "247Around", "Review the Booking");
                $partner_id = $this->input->post("partner_id");
                if ($is_update_spare_parts) {
                    foreach ($sp_required_id as $sp_id) {

                        $sp['status'] = DEFECTIVE_PARTS_PENDING;
                        $this->service_centers_model->update_spare_parts(array('id' => $sp_id), $sp);
                    }

                    $this->update_booking_internal_status($booking_id, DEFECTIVE_PARTS_PENDING, $partner_id);

                    redirect(base_url() . "service_center/get_defective_parts_booking");
                } else {
                    $this->update_booking_internal_status($booking_id, "InProcess_Completed", $partner_id);
                    redirect(base_url() . "service_center/pending_booking");
                }
            }else{
                $this->session->set_userdata('error',"You already marked this booking : $booking_id as completed");
                redirect(base_url() . "service_center/pending_booking");
            }
        }
    }

    /**
     * @desc: Validate Serial Number. If pod is 1 then serial number should not empty
     * @return boolean
     */
    function validate_serial_no() {
        $serial_number = $this->input->post('serial_number');
        $pod = $this->input->post('pod');
        $booking_status = $this->input->post('booking_status');
        $return_status = true;
        if (isset($_POST['pod'])) {
            foreach ($pod as $unit_id => $value) {
                if ($booking_status[$unit_id] == _247AROUND_COMPLETED) {
                    if ($value == 1 && empty(trim($serial_number[$unit_id]))) {
                        $return_status = false;
                    } else if ($value == 1 && is_numeric($serial_number[$unit_id]) && $serial_number[$unit_id] == 0) {
                        $return_status = false;
                    }
                }
            }

            if ($return_status == true) {
                return true;
            } else {
                $this->form_validation->set_message('validate_serial_no', 'Please Enter Serial Number');
                return FALSE;
            }
        } else {
            return TRUE;
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
                    
                    $data['current_status'] = "InProcess";
                    $data['internal_status'] = "Cancelled";
                    $data['service_center_remarks'] = date("F j") . ":- " .$cancellation_text;
                    $data['service_charge'] = $data['additional_service_charge'] = $data['parts_cost'] = $data['amount_paid'] = 0;
                    $data['cancellation_reason'] = $cancellation_reason;
                    $data['closed_date'] = date('Y-m-d H:i:s');
                    $data['update_date'] = date('Y-m-d H:i:s');

                    $this->vendor_model->update_service_center_action($booking_id, $data);
                   //Update Service Center Closed Date in booking Details Table, 
            //if current date time is before 12PM then take completion date before a day, 
            //if day is monday and  time is before 12PM then take completion date as saturday
            //Check if new completion date is equal to or greater then booking_date
            date_default_timezone_set('Asia/Kolkata');
                    // get booking_date
                    $booking_date = $this->reusable_model->get_search_result_data("booking_details",'STR_TO_DATE(booking_details.booking_date,"%d-%m-%Y") as booking_date',array('booking_id'=>$booking_id),
                            NULL,NULL,NULL,NULL,NULL,array())[0]['booking_date'];
                    $bookingData['service_center_closed_date'] = date('Y-m-d H:i:s');
                    // If time is before 12 PM then completion date will be yesturday's date
                    if (date('H') < 12) {
                        $bookingData['service_center_closed_date'] =  date('Y-m-d H:i:s',(strtotime ( '-1 day' , strtotime (date('Y-m-d H:i:s')) ) ));
                        $dayofweek = date('w', strtotime(date('Y-m-d H:i:s')));
                        // If day is monday then completion date will be saturday's date
                        if($dayofweek == '1'){
                          $bookingData['service_center_closed_date'] =  date('Y-m-d H:i:s',(strtotime ( '-2 day' , strtotime (date('Y-m-d H:i:s')) ) ));  
                        }
                    }
                    $booking_timeStamp = strtotime($booking_date);
                    $close_timeStamp = strtotime($bookingData['service_center_closed_date']);
                    $datediff = $close_timeStamp - $booking_timeStamp;
                    $booking_date_days = round($datediff / (60 * 60 * 24))-1;
                    if($booking_date_days >= 0){
                        $bookingData['service_center_closed_date'] = date('Y-m-d H:i:s');
                    }
                    $this->reusable_model->update_table("booking_details",$bookingData,array('booking_id'=>$booking_id));
                   //End Update Service Center Closed Date
                    $this->update_booking_internal_status($booking_id, "InProcess_Cancelled",  $partner_id);
                    $this->insert_details_in_state_change($booking_id, 'InProcess_Cancelled', $can_state_change,"not_define","not_define");
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
        log_message('info', __FUNCTION__ . " Booking ID: " . $booking_id. ' Partner_id: '.$partner_id);
        $booking['booking_id'] = "Q-".$booking_id;
        $booking['current_status'] = "FollowUp";
        $booking['type'] = "Query";
        $booking['internal_status'] = PRODUCT_NOT_DELIVERED_TO_CUSTOMER;
        $booking['assigned_vendor_id'] = NULL;
        $booking['assigned_engineer_id'] = NULL;
        $booking['mail_to_vendor'] = '0';
        $booking['booking_date'] = date('d-m-Y');
        
        //Get Partner 
        $actor = $next_action = 'not_define';
        $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'],$partner_id, $booking['booking_id']);
        if(!empty($partner_status)){
            $booking['partner_current_status'] = $partner_status[0];
            $booking['partner_internal_status'] = $partner_status[1];
            $actor = $booking['actor'] = $partner_status[2];
            $next_action = $booking['next_action'] = $partner_status[3];
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
        $this->insert_details_in_state_change($booking_id, PRODUCT_NOT_DELIVERED_TO_CUSTOMER, "Convert Booking to Query",$actor,$next_action);
        
        
        $cb_url = base_url() . "employee/do_background_process/send_request_for_partner_cb/".$booking_id;
        $pcb = array();
        $this->asynchronous_lib->do_background_process($cb_url, $pcb);
        
        redirect(base_url() . "service_center/pending_booking");  
    }


    /**
     * @desc: This funtion will check Session
     * @param: void
     * @return: true if details matches else session is distroyed.
     */
    function checkUserSession() {
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'service_center') 
                && !empty($this->session->userdata('service_center_id')) && !empty($this->session->userdata('is_sf'))) {
            return TRUE;
        } else {
            log_message('info', __FUNCTION__. " Session Expire for Service Center");
            $this->session->sess_destroy();
            redirect(base_url() . "service_center/login");
        }
    }
    
    /**
     * @desc: This funtion will check Session
     * @param: void
     * @return: true if details matches else session is distroyed.
     */
    function check_BB_UserSession() {
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'service_center') 
                && !empty($this->session->userdata('service_center_id')) && !empty($this->session->userdata('is_cp'))) {
            return TRUE;
        } else {
            log_message('info', __FUNCTION__. " Session Expire for Service Center");
            $this->session->sess_destroy();
            redirect(base_url() . "service_center/login");
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
        redirect(base_url() . "service_center/login");
    }

    /**
     * @desc: this is used to display completed booking for specific service center
     */
    function completed_booking($offset = 0, $page = 0, $booking_id = "") {
        $this->checkUserSession();
        if ($page == 0) {
            $page = 50;
        }

        $service_center_id = $this->session->userdata('service_center_id');

        $offset = ($this->uri->segment(3) != '' ? $this->uri->segment(3) : 0);
        $config['base_url'] = base_url() . 'service_center/completed_booking';
        $config['total_rows'] = $this->service_centers_model->getcompleted_or_cancelled_booking("count", "", $service_center_id, "Completed", $booking_id);

        $config['per_page'] = $page;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $bookings = $this->service_centers_model->getcompleted_or_cancelled_booking($config['per_page'], $offset, $service_center_id, "Completed", $booking_id);
        if (!empty($bookings)) {
            foreach ($bookings as $key => $value) {

                $res =$this->miscelleneous->get_SF_payout($value['booking_id'], $service_center_id, $value['amount_due']);
                $bookings[$key]['sf_earned'] = $res['sf_earned'];
                $bookings[$key]['penalty'] = $res['penalty'];
            }
        }
        $data['serial_number'] = $offset;
        $data['bookings'] = $bookings;
        $data['status'] = "Completed";

        $this->load->view('service_centers/header');
        $this->load->view('service_centers/completed_booking', $data);
    }
    
    function get_sf_payout($booking_id, $service_center_id, $amount_due){
        $res = $this->miscelleneous->get_SF_payout($booking_id, $service_center_id, $amount_due);
        echo "Total SF Payout &nbsp;&nbsp;<i class='fa fa-inr'></i> <b>".$res['sf_earned']."</b>";
        
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
        $data['status'] = "Cancelled";
        $data['serial_number'] = $offset;

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
        $this->form_validation->set_rules('sc_remarks', 'Reascheduled Remarks', 'trim');

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
            $sc_remarks = $this->input->post('sc_remarks');
            if(!empty($reason)){
                
                $data['reschedule_reason'] = $this->input->post('reason');
            } else {
                
                $data['reschedule_reason'] = $this->input->post('reason_text');
            }
            $data['reschedule_reason'] = $data['reschedule_reason']. " - ". $sc_remarks;
            $data['update_date'] = date("Y-m-d H:i:s");
            date_default_timezone_set('Asia/Calcutta'); 
            $data['reschedule_request_date'] = date("Y-m-d H:i:s");
            $this->vendor_model->update_service_center_action($booking_id, $data);
            $this->send_reschedule_confirmation_sms($booking_id);
            $this->insert_details_in_state_change($booking_id, "InProcess_Rescheduled", $data['reschedule_reason'],"not_define","not_define");
            $partner_id = $this->input->post("partner_id");
            $this->update_booking_internal_status($booking_id, $reason,  $partner_id);
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
    function insert_details_in_state_change($booking_id, $new_state, $remarks,$actor,$next_action){
        log_message('info', __FUNCTION__ ." SF ID: ".  $this->session->userdata('service_center_id'). " Booking ID: ". $booking_id. ' new_state: '.$new_state.' remarks: '.$remarks);
           //Save state change
            
            $agent_id = $this->session->userdata('service_center_agent_id');
            $agent_name = $this->session->userdata('service_center_name');
            $service_center_id =$this->session->userdata('service_center_id');
            
            $this->notify->insert_state_change($booking_id, $new_state, "", $remarks, $agent_id, $agent_name,$actor,$next_action, NULL, $service_center_id);
            
    }
    /**
     * @desc: get invoice details and bank transacton details to display in view
     * Get Service center Id from session.
     */
    function invoices_details() {
        //$this->checkUserSession();
        if(!empty($this->session->userdata('service_center_id'))){
            $data['vendor_partner'] = "vendor";
            $data['vendor_partner_id'] = $this->session->userdata('service_center_id');
            $invoice['invoice_array'] = $this->invoices_model->getInvoicingData($data);

            $data2['partner_vendor'] = "vendor";
            $data2['partner_vendor_id'] = $this->session->userdata('service_center_id');
            $invoice['bank_statement'] = $this->invoices_model->get_bank_transactions_details('*',$data2);
            $this->load->view('service_centers/header');
            $this->load->view('service_centers/invoice_summary', $invoice);
        }else{
            $this->session->sess_destroy();
            redirect(base_url() . "service_center/login");
        }
        
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
                        $this->insert_details_in_state_change($booking_id, $assigned['current_state'], "Engineer Id: " . $engineer_id,"not_define","not_define");

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
        if (!empty($booking_id) || $booking_id != 0) {
            $data['booking_id'] = $booking_id;
            $where_internal_status = array("page" => "update_sc", "active" => '1');

            $unit_details = $this->booking_model->get_unit_details(array('booking_id' => $booking_id));
            $data['bookinghistory'] = $this->booking_model->getbooking_history($booking_id);
           

            if (!empty($data['bookinghistory'][0])) {
                $spare_shipped_flag = false;
                $data['internal_status'] = array();
                $current_date = date_create(date('Y-m-d'));
                $current_booking_date = date_create(date('Y-m-d', strtotime($data['bookinghistory'][0]['booking_date'])));
                $is_est_approved = false;
                $spareShipped = false;
                if (isset($data['bookinghistory']['spare_parts'])) {

                    foreach ($data['bookinghistory']['spare_parts'] as $sp) {
                        if ($sp['status'] == SPARE_OOW_EST_GIVEN) {
                            array_push($data['internal_status'], array("status" => ESTIMATE_APPROVED_BY_CUSTOMER));
                            $is_est_approved = true; 
                        }
                        
                        if($sp['auto_acknowledeged'] == 1 && $sp['status'] == SPARE_DELIVERED_TO_SF ){
                            $spare_shipped_flag = TRUE;
                        }
                        switch ($sp['status']){
                               case SPARE_SHIPPED_BY_PARTNER:
                               case DEFECTIVE_PARTS_PENDING:
                               case DEFECTIVE_PARTS_RECEIVED:
                               case DEFECTIVE_PARTS_REJECTED:
                               case DEFECTIVE_PARTS_SHIPPED:
                               case SPARE_DELIVERED_TO_SF: 
                                  $spareShipped = TRUE;
                                   break;
                           }
                    }
                }
                
                $data['spare_shipped'] = $spareShipped;
                $date_diff = date_diff($current_date, $current_booking_date);
                $data['Service_Center_Visit'] = 0;
                // We will not display internal status after 1st day.
                if ($date_diff->days < 1) {
                    $int = $this->booking_model->get_internal_status($where_internal_status, true);
                    $data['internal_status'] = array_merge($data['internal_status'], $int);
                    $data['days'] = 0;
                } else if ($date_diff->days < 3) {
                    $data['days'] = $date_diff->days;
                    array_push($data['internal_status'], array('status' => CUSTOMER_NOT_REACHABLE));
                } else {

                    $data['days'] = 0;
                }

                $data['spare_flag'] = SPARE_PART_RADIO_BUTTON_NOT_REQUIRED;
                foreach ($unit_details as $value) {
                    if (strcasecmp($value['price_tags'], REPAIR_OOW_TAG) == 0) {
                        if(!$is_est_approved){
                            $data['spare_flag'] = SPARE_OOW_EST_REQUESTED;
                            $data['price_tags'] = $value['price_tags'];
                        }
                    } else if (stristr($value['price_tags'], "Repair")) {

                        $data['spare_flag'] = SPARE_PARTS_REQUIRED;
                        $data['price_tags'] = $value['price_tags'];
                    }
                    if (stristr($value['price_tags'], "Service Center Visit")) {
                        array_push($data['internal_status'], array("status" => CUSTOMER_NOT_VISTED_TO_SERVICE_CENTER));
                    }
                }
                $data['spare_shipped_flag'] = $spare_shipped_flag;

                $this->load->view('service_centers/header');
                $this->load->view('service_centers/get_update_form', $data);
            } else {
                echo "Booking Not Found. Please Retry Again";
            }
        } else {
            echo "Booking Not Found. Please Retry Again";
        }
    }

    /**
     * @desc: This is used to update booking by SF. 
     *  IF Rescheduled option ( checkbox) is selected Then it perform save_reschedule_request Method 
     *  IF Spare Parts is selected then call update_spare_parts method
     *  Otherwise its get method name from table. If method name is not exist in the table default_update perform.  
     */
    function process_update_booking() {
        log_message('info', __FUNCTION__ . " Service_center ID: " . $this->session->userdata('service_center_id') . " Booking Id: " . $this->input->post('booking_id'));
        // Check User Session
        $this->checkUserSession();

        // Check form validation
        $f_status = $this->checkvalidation_for_update_by_service_center();
        if ($f_status) {
            $reason = $this->input->post('reason');

            switch ($reason) {
                
                 CASE PRODUCT_NOT_DELIVERED_TO_CUSTOMER:
                 CASE RESCHEDULE_FOR_UPCOUNTRY: 
                 CASE SPARE_PARTS_NOT_DELIVERED_TO_SF: 
                     log_message('info', __FUNCTION__ ." ". $this->input->post('reason') . " Request: " . $this->session->userdata('service_center_id'));
                     $this->save_reschedule_request();
                     break;
               
                CASE CUSTOMER_ASK_TO_RESCHEDULE:
                    log_message('info', __FUNCTION__ ." ". $this->input->post('reason') . " Request: " . $this->session->userdata('service_center_id'));
                    $this->save_reschedule_request();
                    $booking_id = $this->input->post('booking_id');
                    $this->booking_model->increase_escalation_reschedule($booking_id, "count_reschedule");
                    
                    break;
                CASE ESTIMATE_APPROVED_BY_CUSTOMER:
                    log_message('info', __FUNCTION__ . ESTIMATE_APPROVED_BY_CUSTOMER . " Request: " . $this->session->userdata('service_center_id'));
                    $booking_id = $this->input->post('booking_id');
                    $this->approve_oow($booking_id);
                    break;

                CASE SPARE_PARTS_REQUIRED:
                CASE SPARE_OOW_EST_REQUESTED:
                    log_message('info', __FUNCTION__ . " " . $reason . " :" . $this->session->userdata('service_center_id'));
                    $this->update_spare_parts();
                    break;

                CASE CUSTOMER_NOT_REACHABLE:
                    log_message('info', __FUNCTION__ . CUSTOMER_NOT_REACHABLE . $this->session->userdata('service_center_id'));
                    $day = $this->input->post('days');
                    $sc_remarks = $this->input->post('sc_remarks');
                    $spare_shipped = $this->input->post("spare_shipped");
                    if (!$spare_shipped) {
                        if ($day == 2) {
                            $booking_id = $this->input->post('booking_id');
                            $_POST['cancellation_reason'] = CUSTOMER_NOT_REACHABLE;
                            $_POST['cancellation_reason_text'] = $sc_remarks;
                            $this->process_cancel_booking($booking_id);

                            $to = NITS_ANUJ_EMAIL_ID;
                            $cc = "";
                            $bcc = "";
                            $subject = "Auto Cancelled Booking - 3rd Day Customer Not Reachable.";
                            $message = "Auto Cancelled Booking " . $booking_id;
                            $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, $bcc, $subject, $message, "",AUTO_CANCELLED_BOOKING);
                        } else {
                            $this->default_update(true, true);
                        }
                    } else {
                        $this->default_update(true, true);
                    }
                    
                    break;

                case ENGINEER_ON_ROUTE:
                case CUSTOMER_NOT_VISTED_TO_SERVICE_CENTER:
                    log_message('info', __FUNCTION__ . " " . $reason . " " . $this->session->userdata('service_center_id'));
                    $this->default_update(true, true);
                    break;
            }
        } else {
            echo "Update Failed Please Retry Again";
        }

        log_message('info', __FUNCTION__ . " Exit Service_center ID: " . $this->session->userdata('service_center_id'));
    }
    
    function update_booking_internal_status($booking_id, $internal_status, $partner_id){
       
        $booking['internal_status'] = $internal_status;
        $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, $booking['internal_status'], $partner_id, $booking_id);
        if (!empty($partner_status)) {
            $booking['partner_current_status'] = $partner_status[0];
            $booking['partner_internal_status'] = $partner_status[1];
            $booking['actor'] = $partner_status[2];
            $booking['next_action'] = $partner_status[3];
        }
        
        $this->booking_model->update_booking($booking_id, $booking);
        
        log_message('info', __METHOD__. " Partner ID ". $partner_id. " Status ". $internal_status);
        $response = $this->miscelleneous->partner_completed_call_status_mapping($partner_id, $internal_status);
        if(!empty($response)){
            
            $this->booking_model->partner_completed_call_status_mapping($booking_id, array('partner_call_status_on_completed' => $response));
        } else {
            log_message('info', __METHOD__. " Staus Not found for partner ID ". $partner_id. " status ". $internal_status);
        }
        
        if($internal_status == "InProcess_Cancelled" || $internal_status == "InProcess_Completed"){
            log_message("info", __METHOD__. " DO Not Call patner callback");
        } else {
            $cb_url = base_url() . "employee/do_background_process/send_request_for_partner_cb/".$booking_id;
            $pcb = array();
            $this->asynchronous_lib->do_background_process($cb_url, $pcb);
        }
        
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
        $sc_data['service_center_remarks'] = date("F j") . ":- " .$this->input->post('sc_remarks');
        // Update Service center Action table
        $this->service_centers_model->update_service_centers_action_table($booking_id, $sc_data);
        if($state_change){
            // Insert data into state change
            $this->insert_details_in_state_change($booking_id, $sc_data['internal_status'], $sc_data['service_center_remarks'],"not_define","not_define");
            // Send sms to customer while customer not reachable
            if($sc_data['internal_status'] == CUSTOMER_NOT_REACHABLE){
                log_message('info', __FUNCTION__." Send Sms to customer => Customer not reachable");
                $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
                $send['booking_id'] = $booking_id;
                $send['state'] = "Customer not reachable";
                $this->asynchronous_lib->do_background_process($url, $send);
            }
        }
        $partner_id = $this->input->post("partner_id");
        $this->update_booking_internal_status($booking_id,  $sc_data['internal_status'], $partner_id);
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
        log_message('info', __FUNCTION__ . " Service_center ID: " . $this->session->userdata('service_center_id') . " Booking Id: " . $this->input->post('booking_id'));
        $this->checkUserSession();
        $this->form_validation->set_rules('booking_id', 'Booking Id', 'trim|required|xss_clean');
        if ($this->form_validation->run()) {
            $allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
            $booking_id = $this->input->post('booking_id');
            $data['model_number'] = $this->input->post('model_number');
            $data['serial_number'] = $this->input->post('serial_number');
            $data['parts_requested'] = $this->input->post('parts_name');
            $data['date_of_purchase'] = $this->input->post('dop');
            $data['partner_id'] = $this->input->post('partner_id');
            $booking_date = $this->input->post('booking_date');
            $reason = $this->input->post('reason');
            $price_tags = $this->input->post('price_tags');
            if (stristr($price_tags, "Out Of Warranty")) {
                
                $data['defective_part_required'] = 0;
                $status = SPARE_OOW_EST_REQUESTED;
                $sc_data['internal_status'] = SPARE_OOW_EST_REQUESTED;
                
            } else {
                $data['defective_part_required'] = $this->partner_model->getpartner_details("is_def_spare_required", 
                array('partners.id' => $data['partner_id']))[0]['is_def_spare_required'];
                
                $status = SPARE_PARTS_REQUESTED;
                $sc_data['internal_status'] = $reason;
                //Send Push Notification
               $receiverArray['partner'] = array($data['partner_id']);
               $notificationTextArray['msg'] = array($data['parts_requested'],$booking_id);
               $this->push_notification_lib->create_and_send_push_notiifcation(SPARE_PART_REQUEST_TO_PARTNER,$receiverArray,$notificationTextArray);
               //End Push Notification
            }

            if (isset($_FILES["invoice_image"])) {
                $invoice_name = $this->miscelleneous->upload_file_to_s3($_FILES["invoice_image"], 
                        "Invoice", $allowedExts, $booking_id, "misc-images", "sp_parts");
                if (isset($invoice_name)) {
                    $data['invoice_pic'] = $invoice_name;
                }
            }

            if (isset($_FILES["serial_number_pic"])) {

                $serial_number_pic = $this->miscelleneous->upload_file_to_s3($_FILES["serial_number_pic"], 
                        "Serial_NO", $allowedExts, $booking_id, "misc-images", "sp_parts");
                if (isset($serial_number_pic)) {
                    $data['serial_number_pic'] = $serial_number_pic;
                }
            }

            if (isset($_FILES["defective_parts_pic"])) {

                $defective_parts_pic = $this->miscelleneous->upload_file_to_s3($_FILES["defective_parts_pic"], 
                        "Defective_Parts", $allowedExts, $booking_id, "misc-images", "sp_parts");
                if (isset($defective_parts_pic)) {
                    $data['defective_parts_pic'] = $defective_parts_pic;
                }
            }


            $data['date_of_request'] = $data['create_date'] = date('Y-m-d H:i:s');
            $data['remarks_by_sc'] = $this->input->post('reason_text');
            
            $data['booking_id'] = $booking_id;
            $data['status'] = $status;
            $data['service_center_id'] = $this->session->userdata('service_center_id');
            //$where = array('booking_id' => $booking_id, 'service_center_id' => $data['service_center_id']);
            $status_spare = $this->service_centers_model->insert_data_into_spare_parts($data);
            if ($status_spare) {

                $this->insert_details_in_state_change($booking_id, $reason, $data['remarks_by_sc'],"not_define","not_define");

                $sc_data['current_status'] = "InProcess";
                
                if (!empty($booking_date) ) {
                    $sc_data['current_status'] = "Pending";
                    $sc_data['booking_date'] = date('Y-m-d H:i:s', strtotime($booking_date));
                    $sc_data['reschedule_reason'] = $data['remarks_by_sc'];
                    // $sc_data['internal_status'] = 'Reschedule';
                    $booking['booking_date'] = date('d-m-Y', strtotime($booking_date));
                    $this->booking_model->update_booking($booking_id, $booking);
                }

                $sc_data['service_center_remarks'] = date("F j") . ":- " .$data['remarks_by_sc'];
                $sc_data['update_date'] = date("Y-m-d H:i:s");

                $this->vendor_model->update_service_center_action($booking_id, $sc_data);
                
                $this->update_booking_internal_status($booking_id, $status,  $data['partner_id']);

                $userSession = array('success' => 'Booking Updated');
                $this->session->set_userdata($userSession);
                redirect(base_url() . "service_center/pending_booking");
            } else { // if($status_spare){
                log_message('info', __FUNCTION__ . " Not update Spare parts Service_center ID: " . $this->session->userdata('service_center_id') . " Data: " . print_r($data));

                $userSession = array('error' => 'Booking Not Updated');
                $this->session->set_userdata($userSession);
                redirect(base_url() . "service_center/pending_booking");
            }
        } else {
            log_message('info', __FUNCTION__ . " Not update Spare parts Service_center ID: " . $this->session->userdata('service_center_id') . " Data: " . print_r($data));

            $userSession = array('error' => 'Booking Not Updated');
            $this->session->set_userdata($userSession);
            redirect(base_url() . "service_center/pending_booking");
        }

        log_message('info', __FUNCTION__ . " Exit Service_center ID: " . $this->session->userdata('service_center_id'));
    }
    
    function upload_defective_spare_pic(){
        $allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
        $booking_id = $this->input->post("booking_id");
        $defective_courier_receipt = $this->miscelleneous->upload_file_to_s3($_FILES["defective_courier_receipt"], 
                "defective_courier_receipt", $allowedExts, $booking_id, "misc-images", "sp_parts");
        if($defective_courier_receipt){
           return true;
        } else {
            $this->form_validation->set_message('upload_defective_spare_pic', 'File size or file type is not supported. Allowed extentions are "png", "jpg", "jpeg" and "pdf". '
		    . 'Maximum file size is 5 MB.');
            return false;
        }
    }
    
    /**
     * @desc: This is used to update acknowledge date by SF
     * @param String $booking_id
     */
    function acknowledge_delivered_spare_parts($booking_id, $service_center_id, $id, $partner_id, $autoAck = false){
        log_message('info', __FUNCTION__. " Booking ID: ". $booking_id.' service_center_id: '.$service_center_id.' id: '.$id);
        if(empty($autoAck)){
            $this->checkUserSession();
        }
        if (!empty($booking_id)) {
           
            $where = array('id' => $id);
            $sp_data['service_center_id'] = $service_center_id;
            $sp_data['acknowledge_date'] = date('Y-m-d');
            $sp_data['status'] = SPARE_DELIVERED_TO_SF;
            if(!empty($autoAck)){
                $sp_data['auto_acknowledeged'] = 1;
            } else {
                $sp_data['auto_acknowledeged'] = 0;
            }
            //Update Spare Parts table
            $ss = $this->service_centers_model->update_spare_parts($where, $sp_data);
            if ($ss) { //if($ss){
                $booking['booking_date'] = date('d-m-Y', strtotime('+1 days'));
                $booking['update_date'] =  date("Y-m-d H:i:s");
                $booking['internal_status'] = SPARE_PARTS_DELIVERED;
        
                $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, SPARE_PARTS_DELIVERED, $partner_id, $booking_id);
                $actor = $next_action ='not_define';
                if (!empty($partner_status)) {
                    $booking['partner_current_status'] = $partner_status[0];
                    $booking['partner_internal_status'] = $partner_status[1];
                    $actor = $booking['actor'] = $partner_status[2];
                    $next_action = $booking['next_action'] = $partner_status[3];
                }
                $b_status = $this->booking_model->update_booking($booking_id, $booking);
                $state_change['actor'] = $actor;
                $state_change['next_action'] = $next_action;
                if ($b_status) {
                    $state_change['booking_id'] = $booking_id;
                    $state_change['new_state'] =  SPARE_PARTS_DELIVERED;
           
                    $booking_state_change = $this->booking_model->get_booking_state_change($state_change['booking_id']);
            
                    if ($booking_state_change > 0) {
                        $state_change['old_state'] = $booking_state_change[count($booking_state_change) - 1]['new_state'];
                    } else { //count($booking_state_change)
                        $state_change['old_state'] = "Pending";
                    }
           
        
                if($this->session->userdata('service_center_id')){
                    $state_change['agent_id'] = $this->session->userdata('service_center_agent_id');
                    $state_change['service_center_id'] = $login_data['entity_id'] = $this->session->userdata('service_center_id');
                } else {
                    $state_change['agent_id'] = _247AROUND_DEFAULT_AGENT;
                    $state_change['partner_id'] = _247AROUND;
                }
                
                $state_change['remarks'] = "SF acknowledged to receive spare parts";

                // Insert data into booking state change
                $this->booking_model->insert_booking_state_change($state_change);

                    $sc_data['current_status'] = "Pending";
                    $sc_data['internal_status'] = SPARE_PARTS_DELIVERED;
                    $sc_data['update_date'] = date("Y-m-d H:i:s");
                    $this->vendor_model->update_service_center_action($booking_id, $sc_data);
                    if( $this->session->userdata('service_center_id')){
                        $userSession = array('success' => 'Booking Updated');
                        $this->session->set_userdata($userSession);
                    }
                $cb_url = base_url() . "employee/do_background_process/send_request_for_partner_cb/".$booking_id;
                $pcb = array();
                $this->asynchronous_lib->do_background_process($cb_url, $pcb);

                } else {//if ($b_status) {
                    
                        log_message('info', __FUNCTION__ . " Booking is not updated. Service_center ID: " 
                                . $service_center_id .
                                "Booking ID: " . $booking_id);
                        if( $this->session->userdata('service_center_id')){
                            $userSession = array('success' => 'Please Booking is not updated');
                            $this->session->set_userdata($userSession);
                        }
                    }
                } else {
                    log_message('info', __FUNCTION__ . " Spare parts ack date is not updated Service_center ID: "
                            . $service_center_id .
                            "Booking ID: " . $booking_id);
                    if( $this->session->userdata('service_center_id')){
                        $userSession = array('success' => 'Please Booking is not updated');
                        $this->session->set_userdata($userSession);
                    }
                }
            }
            log_message('info', __FUNCTION__. " Exit Service_center ID: ". $service_center_id);
            if( $this->session->userdata('service_center_id')){
                redirect(base_url() . "service_center/pending_booking");
            }

    }
    /**
     * @desc: This method called by Cron.
     * This method is used to convert Shipped spare part booking into Pending
     */
    function get_booking_id_to_convert_pending_for_spare_parts(){
        $data = $this->service_centers_model->get_booking_id_to_convert_pending_for_spare_parts();
        foreach($data as $value){
            $this->acknowledge_delivered_spare_parts($value['booking_id'], $value['service_center_id'], $value['id'], $value['partner_id'], TRUE);
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
            
            $output = "Booking Not Found";
            $userSession = array('error' => $output);
            $this->session->set_userdata($userSession);

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
            $filter_option = $this->service_centre_charges_model->get_service_centre_charges_by_any(array('tax_rates.state' =>$sc_details[0]['state'],'length' => -1),'distinct services.id,services.services as product,category,capacity,service_category');
            $data['category'] = array_unique(array_column($filter_option, 'category'));
            $data['capacity'] = array_unique(array_column($filter_option, 'capacity'));
            $data['service_category'] = array_unique(array_column($filter_option, 'service_category'));
            $data['appliance'] = array_unique(array_column($filter_option,'product','id'));
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
        //$this->checkUserSession();
        log_message('info', __FUNCTION__.' Used by :'.$this->session->userdata('service_center_name'));
        $id = $this->session->userdata('service_center_id');
        if(!empty($id)){
            
            $query = $this->vendor_model->viewvendor($id);

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

        $where = array(
            "spare_parts_details.defective_part_required"=>1,
            "spare_parts_details.service_center_id" => $service_center_id,
            "status IN ('".DEFECTIVE_PARTS_PENDING."', '".DEFECTIVE_PARTS_REJECTED."')  " => NULL
            
        );
        
        $select = "CONCAT( '', GROUP_CONCAT((parts_shipped ) ) , '' ) as parts_shipped, "
                . " spare_parts_details.booking_id, name, "
                . "CONCAT( '', GROUP_CONCAT((remarks_defective_part_by_partner ) ) , '' ) as remarks_defective_part_by_partner, "
                . "CONCAT( '', GROUP_CONCAT((remarks_by_partner ) ) , '' ) as remarks_by_partner, booking_details.partner_id";
        
        $group_by = "spare_parts_details.booking_id";
        $order_by = "status = '". DEFECTIVE_PARTS_REJECTED."', spare_parts_details.create_date ASC";
       
          
        $config['base_url'] = base_url() . 'service_center/get_defective_parts_booking';
        $config['total_rows'] = $this->service_centers_model->count_spare_parts_booking($where, $select);
                
        $config['per_page'] = 50;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['spare_parts'] = $this->service_centers_model->get_spare_parts_booking($where, $select, $group_by, $order_by, $offset, $config['per_page']);
       
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
     * @param String $id
     */
    function update_defective_parts($booking_id) {
        $this->checkUserSession();
        if (!empty($booking_id) || $booking_id != '' || $booking_id != 0) {
            log_message('info', __FUNCTION__ . ' Used by :' . $this->session->userdata('service_center_name'));
           
            $service_center_id = $this->session->userdata('service_center_id');

            $where = "spare_parts_details.service_center_id = '" . $service_center_id . "'  "
                    . " AND spare_parts_details.booking_id = '" . $booking_id . "' ";
            $data['spare_parts'] = $this->partner_model->get_spare_parts_booking($where);
            if (!empty($data['spare_parts'])) {
                $this->load->view('service_centers/header');
                $this->load->view('service_centers/update_defective_spare_parts_form', $data);
            } else {
                echo "Please Try Again Later";
            }
        } else {
            redirect(base_url()."service_center/get_defective_parts_booking");
        }
    }

    /**
     * @desc: Process to update defective spare parts
     * @param type $booking_id
     */
    function process_update_defective_parts($booking_id) {
        log_message('info', __FUNCTION__ . ' sf_id: ' . $this->session->userdata('service_center_id') . " booking id " . $booking_id );
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . ' Used by :' . $this->session->userdata('service_center_name'));
        
        $this->form_validation->set_rules('remarks_defective_part', 'Remarks', 'trim|required');
        $this->form_validation->set_rules('courier_name_by_sf', 'Courier Name', 'trim|required');
        $this->form_validation->set_rules('awb_by_sf', 'AWB', 'trim|required');
        $this->form_validation->set_rules('defective_part_shipped_date', 'AWB', 'trim|required');
        $this->form_validation->set_rules('courier_charges_by_sf', 'Courier Charges', 'trim|required');
        $this->form_validation->set_rules('defective_courier_receipt', 'Courier Invoice', 'callback_upload_defective_spare_pic');

        if ($this->form_validation->run() == FALSE) {
            log_message('info', __FUNCTION__ . '=> Form Validation is not updated by Service center ' . $this->session->userdata('service_center_name') .
                    " booking id " . $booking_id . " Data" . print_r($this->input->post(), true));
            $this->update_defective_parts($booking_id);
        } else {
            
            $defective_courier_receipt = $this->input->post("sp_parts");
           
            if (!empty($defective_courier_receipt)) {
                $data['defective_courier_receipt'] = $this->input->post("sp_parts");
                $service_center_id = $this->session->userdata('service_center_id');
                $defective_part_shipped = $this->input->post('defective_part_shipped');
                $data['remarks_defective_part_by_sf'] = $this->input->post('remarks_defective_part');
                $data['defective_part_shipped_date'] = $this->input->post('defective_part_shipped_date');
                $data['status'] = DEFECTIVE_PARTS_SHIPPED;
                $k =0;
                $sf_details = $this->vendor_model->getVendorDetails('name,address,sc_code,is_gst_doc,owner_name,signature_file,gst_no,is_signature_doc',array('id'=>$service_center_id));
                $partner_id = $this->input->post('partner_id');
                $partner_details = $this->partner_model->getpartner_details('company_name,address,gst_number',array('partners.id'=> $partner_id));
                foreach ($defective_part_shipped as $id => $value) {
                    if($k ==0){
                        $data['courier_charges_by_sf'] = $this->input->post('courier_charges_by_sf');
                        
                    } else {
                        $data['courier_charges_by_sf'] = 0;
                        
                    }
                    $data['awb_by_sf'] = $this->input->post('awb_by_sf');
                    $data['courier_name_by_sf'] = $this->input->post('courier_name_by_sf');
                    $data['defective_part_shipped'] = $value;
                    
                    $data['sf_challan_number'] = $this->create_sf_challan_id($sf_details[0]['sc_code']);
                    $data['sf_challan_file'] = $this->create_sf_challan_file($sf_details,$partner_details,$data['sf_challan_number'],$id);
                    $where = array('id' => $id);
                    $this->service_centers_model->update_spare_parts($where, $data);
                    $k++;
                }

                $this->insert_details_in_state_change($booking_id, DEFECTIVE_PARTS_SHIPPED, $data['remarks_defective_part_by_sf'],"not_define","not_define");
                $sc_data['current_status'] = "InProcess";
                $sc_data['update_date'] = date('Y-m-d H:i:s');
                $sc_data['internal_status'] = DEFECTIVE_PARTS_SHIPPED;
                $this->vendor_model->update_service_center_action($booking_id, $sc_data);
                
                $this->update_booking_internal_status($booking_id,  DEFECTIVE_PARTS_SHIPPED, $partner_id);
                
                $rm_email = $this->get_rm_email($service_center_id);
                $from = NOREPLY_EMAIL_ID;

                $to = "booking@247around.com";
                $cc= $rm_email.", nits@247around.com";
               
                $subject = $this->session->userdata('service_center_name')." Updated Courier Details for Booking ID ".$booking_id;
                $message = "Please Find Courier Invoice Attachment"."<br/>Courier Details:- <br/>";
                $message .= "AWB ".$data['awb_by_sf']."<br/>";
                $message .= "Courier Name ".$data['courier_name_by_sf']."<br/>";
                $message .= "Courier Charge ".$this->input->post('courier_charges_by_sf')."<br/>";
                $message .= "Shipped Date ".$data['defective_part_shipped_date']."<br/>";
                $attachment = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$defective_courier_receipt;
                $this->notify->sendEmail($from, $to, $cc, "", $subject, $message, $attachment,COURIER_DETAILS);
                $userSession = array('success' => 'Parts Updated.');

                $this->session->set_userdata($userSession);
                redirect(base_url() . "service_center/get_defective_parts_booking");

            } else {
                log_message('info', __FUNCTION__ . '=> Defective Spare parts booking is not updated by SF ' . $this->session->userdata('service_center_name') .
                        " booking id " . $booking_id . " Data" . print_r($this->input->post(), true));
                $userSession = array('success' => 'Parts Not Updated. Please Upload Less Than 2 MB File.');
                $this->session->set_userdata($userSession);
                redirect(base_url() . "service_center/get_defective_parts_booking");
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
        $order_received_from_vendor_details = $this->vendor_model->getVendorContact($data['brackets'][0]['order_received_from']);
        $data['order_received_from'] = $order_received_from_vendor_details[0]['name'];
        $data['order_received_from_address'] = $order_received_from_vendor_details[0]['address'].','.$order_received_from_vendor_details[0]['district'].','.$order_received_from_vendor_details[0]['state'].','.$order_received_from_vendor_details[0]['pincode'];
        $data['order_given_to'] = $this->vendor_model->getVendorContact($data['brackets'][0]['order_given_to'])[0]['name'];
        $data['primary_contact_name'] = $order_received_from_vendor_details[0]['primary_contact_name'];
        $data['phone_number'] = $order_received_from_vendor_details[0]['primary_contact_phone_1'].", ".$order_received_from_vendor_details[0]['primary_contact_phone_2'];
        
        $this->load->view('service_centers/header');
        $this->load->view("service_centers/show_vender_brackets_order_history", $data);

    }
    
    /**
     * @Desc: This function is used to update shipment
     * @params: Int order id
     * @return : view
     */
    function get_update_shipment_form($order_id) {
        if (!empty($order_id) || $order_id != '') {
            $data['brackets'] = $this->inventory_model->get_brackets_by_id($order_id);
            $data['shipped_flag'] = TRUE;
            $data['order_id'] = $order_id;
            $data['order_given_to'] = $this->vendor_model->getVendorContact($data['brackets'][0]['order_given_to'])[0]['name'];
            $data['order_received_from'] = $this->vendor_model->getVendorContact($data['brackets'][0]['order_received_from'])[0]['name'];
            $this->load->view('service_centers/header');
            $this->load->view("service_centers/update_vender_brackets", $data);
        } else {
            echo "Please Try Again! Order Id Not Exist";
        }
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
        $order_given_to = $this->input->post('order_given_to');
//        $data['19_24_shipped'] = $this->input->post('19_24_shipped');
        $data['19_24_shipped'] = '0';
        $data['26_32_shipped'] = $this->input->post('26_32_shipped');
        $data['36_42_shipped'] = $this->input->post('36_42_shipped');
//        $data['43_shipped'] = $this->input->post('43_shipped');
        $data['43_shipped'] = '0';
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
            $this->insert_details_in_state_change($order_id, "Brackets_Shipped", "Brackets Shipped","not_define","not_define");    
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
                        $subject = vsprintf($template[4], $order_received_from_email[0]['company_name']);
                        $emailBody = vsprintf($template[0], $email);
                        $this->notify->sendEmail($template[2], $to , $template[3].','.$this->get_rm_email($order_received_from), '', $subject , $emailBody, $attachment,'brackets_shipment_mail');
                   }
            //2. Sending mail to order_given_to vendor
            $order_given_to_email_to = $this->vendor_model->getVendorContact($order_given_to);
            $to = $order_given_to_email_to[0]['primary_contact_email'].','.$order_given_to_email_to[0]['owner_email'];
            $order_given_to_email = array();
                   //Getting template from Database
                   $template1 = $this->booking_model->get_booking_email_template("brackets_shipment_mail_to_order_given_to");
                   
                   if(!empty($template)){
                        $order_given_to_email['order_recieved_from'] = $order_received_from_email[0]['company_name'];
                        $order_given_to_email['order_id'] = $order_id;
                        $subject = vsprintf($template1[4], $order_received_from_email[0]['company_name']);
                        $emailBody = vsprintf($template1[0], $order_given_to_email);
                        
                        $this->notify->sendEmail($template1[2], $to , $template1[3].','.$this->get_rm_email($order_given_to), '', $subject , $emailBody, '','brackets_shipment_mail_to_order_given_to');
                   
                        //Loggin send mail success
                        log_message('info',__FUNCTION__.' Shipped mail has been sent to order_given_to vendor '. $emailBody);
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
    
    
    /**
     * @desc Used to show buyback order data as requested
     * @param void
     * @return json $output 
     */
    public function view_delivered_bb_order_details(){    
        $this->check_BB_UserSession();
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/bb_order_details');
    }
    
    /**
     * @desc Used to show the buyback order details on cp panel
     * @param $order_id string
     * @param $service_id string
     * @param $city string
     * @return void
     */
    function update_bb_report_issue_order_details($order_id,$service_id,$city,$cp_id){
        $this->check_BB_UserSession();
        $data['order_id'] = rawurldecode($order_id);
        $data['service_id'] = rawurldecode($service_id);
        $data['city'] = rawurldecode($city);
        $data['cp_id'] = rawurldecode($cp_id);
        $data['products'] = $this->booking_model->selectservice();
        $data['cp_basic_charge'] = $this->bb_model->get_bb_order_appliance_details(array('partner_order_id'=> $data['order_id']),'cp_basic_charge');
        
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/update_bb_order_details',$data);
    }
    
    
    /**
     * @desc Used to get buyback order brand from ajax call
     * @param void
     * @return $option string
     */
    function get_bb_order_brand(){
        //$this->check_BB_UserSession();
        $service_id = $this->input->post('service_id');
        $cp_id = $this->input->post('cp_id');
        $where = array('cp_id'=>$cp_id,'service_id' => $service_id, 'brand != " "' => null,'visible_to_cp' => '1');
        $select = "brand";
        $brands = $this->service_centre_charges_model->get_bb_charges($where,$select,TRUE);
        $option = '<option selected disabled>Select Brand</option>';
        if(!empty($brands)){
           //print_r($brands);

            foreach ($brands as $value) {
                $option .= "<option value='" . $value['brand'] . "'";
                if(count($brands) == 1){
                    $option .= " selected "; 
                }
                $option .= " > ";
                $option .= $value['brand'] . "</option>";
            }
 
        }else{
            
            $option .= "<option value=''>Others</option>";
                
        }
        
        echo $option;
    }
    
    
    /**
     * @desc Used to get buyback order physical condition from ajax call
     * @param void
     * @return $option string
     */
    function get_bb_order_physical_condition() {
        //$this->check_BB_UserSession();
        $category = $this->input->post('category');
        $service_id = $this->input->post('service_id');
        $cp_id = $this->input->post('cp_id');
        $where = array('cp_id' => $cp_id,
            'service_id' => $service_id, 'category' => $category, 'physical_condition != " " ' => null,'visible_to_cp' => '1');
        $select = "physical_condition";
        $physical_condition = $this->service_centre_charges_model->get_bb_charges($where, $select, TRUE);
        
        if (!empty($physical_condition)) {
            $option = '<option selected disabled>Select Physical Condition</option>';

            foreach ($physical_condition as $value) {
                $option .= "<option value='" . $value['physical_condition'] . "'";
                if(count($physical_condition) == 1){
                    $option .= " selected "; 
                }
                $option .= " > ";
                $option .= $value['physical_condition'] . "</option>";
            }

            echo $option;
        }else{
            echo "empty";
        }
    }
    
    
    /**
     * @desc Used to get buyback order working condition from ajax call
     * @param void
     * @return $option string
     */
    function get_bb_order_working_condition() {
        //$this->check_BB_UserSession();
        $category = $this->input->post('category');
        $service_id = $this->input->post('service_id');
        $physical_condition = $this->input->post('physical_condition');
        $cp_id = $this->input->post('cp_id');
        if(!empty($physical_condition)){
            $where = array('cp_id' => $cp_id, 'service_id' => $service_id, 'category' => $category,'physical_condition'=>$physical_condition,'visible_to_cp' => '1');
        }else{
            $where = array('cp_id' => $cp_id, 'service_id' => $service_id, 'category' => $category,'visible_to_cp' => '1');
        }
        $select = "working_condition";
        $working_condition = $this->service_centre_charges_model->get_bb_charges($where, $select, TRUE);
        
        if (!empty($working_condition)) {
            $option = '<option selected disabled>Select Working Condition</option>';

            foreach ($working_condition as $value) {
                $option .= "<option value='" . $value['working_condition'] . "'";
                if(count($working_condition) == 1){
                    $option .= " selected "; 
                }
                $option .= " > ";
                $option .= $value['working_condition'] . "</option>";
            }

            echo $option;
        }
    }
    
    
    /**
     * @desc Used to check buyback order key from ajax call
     * @param void
     * @return string
     */
    function check_bb_order_key(){
        //$this->check_BB_UserSession();
        $category = $this->input->post('category');
        $service_id = $this->input->post('services');
        $physical_condition = $this->input->post('physical_condition');
        $working_condition = $this->input->post('working_condition');
        $brand = $this->input->post('brand');
        $city = $this->input->post('city');
       // $order_id = $this->input->post('order_id');
        $cp_id = $this->input->post('cp_id');
        $where = array('cp_id' => $cp_id, 
                        'service_id' => $service_id, 
                        'category' => $category,
                        'physical_condition'=>$physical_condition,
                        'working_condition' => $working_condition,
                        'brand'=>$brand,
                        'city'=>$city);
        $select = "order_key, (cp_basic + cp_tax) as cp_charge";
        $order = $this->service_centre_charges_model->get_bb_charges($where, $select, TRUE);
        if(!empty($order)){
            $array = array("order_key" => $order[0]['order_key'], "cp_charge" => $order[0]['cp_charge']);
            echo json_encode($array, true);
        } else {
            echo "Not Found";
        }
    }
    
    
    /**
     * @desc Used to process the  buyback update order form
     * @param void
     * @return void
     */
    function process_report_issue_bb_order_details() {
        $this->check_BB_UserSession();
        $request_data['select'] = "bb_cp_order_action.current_status";
        $request_data['length'] = -1;
        $request_data['where_in'] = array();
        $request_data['where'] = array('bb_cp_order_action.current_status' => _247AROUND_BB_IN_PROCESS,
            "bb_cp_order_action.partner_order_id" => $this->input->post('order_id'));
        $is_inProcess = $this->cp_model->get_bb_cp_order_list($request_data);

        if (!empty($is_inProcess)) {
            $this->session->set_userdata('error', 'Order Already Updated');
            redirect(base_url() . 'service_center/buyback/update_order_details/' . $this->input->post('order_id') . '/' .
                    $this->input->post('service_id') . '/' . $this->input->post('city') . '/' . $this->input->post('cp_id'));
        } else {
            //check for validation
            $this->form_validation->set_rules('order_id', 'Order Id', 'trim|required');
            $this->form_validation->set_rules('remarks', 'Remarks', 'trim|required');
            $this->form_validation->set_rules('order_working_condition', 'Order Working Condition', 'trim');
            $this->form_validation->set_rules('category', 'Category', 'trim|required');
            $this->form_validation->set_rules('cp_id', 'Collection Partner Id', 'trim|required');
            $this->form_validation->set_rules('claimed_price', 'Claimed Price', 'trim');

            if ($this->form_validation->run() === false) {
                $msg = "Please fill all required field";
                $this->session->set_userdata('error', $msg);
                redirect(base_url() . 'service_center/buyback/update_order_details/' . $this->input->post('order_id') . '/' . $this->input->post('service_id') . '/' . $this->input->post('city') . '/' . $this->input->post('cp_id'));
            } else {

                $order_id = $this->input->post('order_id');
                $remarks = $this->input->post('remarks');
                $working_condition = $this->input->post('order_working_condition');
                $category = $this->input->post('category');
                $cp_id = $this->input->post('cp_id');
                $cp_claimed_price = $this->input->post('claimed_price');
                $order_brand = $this->input->post('order_brand');
                $order_key = $this->input->post('partner_order_key');
                $physical_condition = $this->input->post('order_physical_condition');

                $upload_images = $this->buyback->process_bb_report_issue_upload_image($this->input->post());

                if (isset($upload_images['status']) && $upload_images['status'] == 'error') {
                    $this->session->set_userdata('error', $upload_images['msg']);
                    redirect(base_url() . 'service_center/buyback/update_order_details/' . $this->input->post('order_id') . '/' . $this->input->post('service_id') . '/' . $this->input->post('city') . '/' . $this->input->post('cp_id'));
                } else {
                    $physical_condition = isset($physical_condition) ? $physical_condition : '';
                    if (!empty($physical_condition)) {
                        $physical_condition = $physical_condition;
                    } else {
                        $physical_condition = '';
                    }
                   
                    $data = array(
                        'category' => $category,
                        'physical_condition' => $physical_condition,
                        'working_condition' => $working_condition,
                        'remarks' => $remarks,
                        'brand' => $order_brand,
                        'current_status' => _247AROUND_BB_IN_PROCESS,
                        'internal_status' => _247AROUND_BB_Damaged_STATUS,
                        'order_key' => $order_key,
                        'cp_claimed_price' => $cp_claimed_price,
                        'acknowledge_date' => date('Y-m-d H:i:s'));

                    $where = array('partner_order_id' => $order_id, 'cp_id' => $cp_id);
                    //update bb_cp_action_table
                    $update_id = $this->cp_model->update_bb_cp_order_action($where, $data);
                    if ($update_id) {
                        log_message("info", __METHOD__ . "Cp Action table updated for order id: " . $order_id);
                        //update order details table
                        $order_details_update_id = $this->bb_model->update_bb_order_details(array('partner_order_id' => $order_id, 'assigned_cp_id' => $cp_id), array('is_delivered' => '1'));
                        if (!empty($order_details_update_id)) {
                            $this->buyback->insert_bb_state_change($order_id, _247AROUND_BB_IN_PROCESS, $remarks, $this->session->userdata('service_center_agent_id'), NULL, $cp_id);
                            $this->session->set_userdata('success', 'Order has been updated successfully');
                            redirect(base_url() . 'service_center/buyback/bb_order_details');
                        } else {
                            $this->session->set_userdata('error', 'Oops!!! There are some issue in updating order. Please Try Again...');
                            redirect(base_url() . 'service_center/buyback/update_order_details/' . $this->input->post('order_id') . '/' . $this->input->post('service_id') . '/' . $this->input->post('city') . '/' . $this->input->post('cp_id'));
                        }
                    }
                }
            }
        }
    }
    
    function validate_claimed_price(){
        $cp_claimed_price = $this->input->post('claimed_price');
        $cp_basic_charge = $this->input->post('cp_basic_charge');
        $final_price = $cp_basic_charge * .30;
        
        if($cp_claimed_price < $final_price){
            $flag = FALSE;
        }else{
            $flag = TRUE;
        }
        
        return $flag;
    }
    
    
    /**
     * @desc Used to get the buyback order category
     * @param void
     * @return string
     */
    function get_bb_order_category_size(){
        //$this->check_BB_UserSession();
        $service_id = $this->input->post('product_service_id');
        $cp_id = $this->input->post('cp_id');
        $where = array('service_id'=> $service_id,'cp_id'=>$cp_id);
        $select = "category";
        $categories = $this->service_centre_charges_model->get_bb_charges($where,$select,TRUE);
        $option = '<option selected disabled>Select Category</option>';
        if (!empty($categories)) {
            
            foreach ($categories as $value) {
                $option .= "<option value='" . $value['category'] . "'";
                $option .= " > ";
                $option .= $value['category'] . "</option>";
            }
            
        }else{
            $option .= "<option value='' disabled=''>No Data Found</option>";
        }
        
        echo $option;
    }
    
    
    /**
     * @desc Used to get  buyback form to update received bb order
     * @param $order_id string
     * @param $service_id string
     * @param $city string
     * @param $cp_id string
     * @return void
     */
    function update_received_bb_order($order_id, $service_id, $city, $cp_id) {
        $this->check_BB_UserSession();

        $data['order_id'] = rawurldecode($order_id);
        $data['service_id'] = rawurldecode($service_id);
        $data['city'] = rawurldecode($city);
        $data['cp_id'] = rawurldecode($cp_id);
        $data['agent_id'] = $this->session->userdata('service_center_agent_id');
        
        $response = $this->buyback->process_update_received_bb_order_details($data);

        if ($response['status'] === 'success') {
            $this->session->set_userdata('success', $response['msg']);
            redirect(base_url() . 'service_center/buyback/bb_order_details');
        } else if ($response['status'] === 'error') {
            $this->session->set_userdata('error', $response['msg']);
            redirect(base_url() . 'service_center/buyback/buyback/bb_order_details');
        }
    }

    /**
     * @desc Used to update not received bb order
     * @param $order_id string
     * @param $service_id string
     * @param $city string
     * @param $cp_id string
     * @return void
     */
    function update_not_received_bb_order($order_id, $service_id, $city) {
        $this->check_BB_UserSession();

        $data['order_id'] = rawurldecode($order_id);
        $data['service_id'] = rawurldecode($service_id);
        $data['city'] = rawurldecode($city);
        $data['cp_id'] = $this->session->userdata('service_center_id');
        $agent_id = $this->session->userdata('service_center_agent_id');
        $request_data['select'] = "bb_cp_order_action.current_status";
        $request_data['length'] = -1;
        $request_data['where_in'] = array();
        $request_data['where'] = array('bb_cp_order_action.current_status' => _247AROUND_BB_IN_PROCESS,
            "bb_cp_order_action.partner_order_id" => $data['order_id']);
        $is_inProcess = $this->cp_model->get_bb_cp_order_list($request_data);

        if (!empty($is_inProcess)) {

            $this->session->set_userdata('error', 'Order Already Updated');
            redirect(base_url() . 'service_center/buyback/bb_order_details');
        } else {
            $update_data = array('current_status' => _247AROUND_BB_IN_PROCESS,
                'internal_status' => _247AROUND_BB_ORDER_NOT_RECEIVED_INTERNAL_STATUS,
                'acknowledge_date' => date('Y-m-d H:i:s')
            );

            $update_where = array('partner_order_id' => $data['order_id'], 'cp_id' => $data['cp_id']);

            //update cp action table
            $update_id = $this->cp_model->update_bb_cp_order_action($update_where, $update_data);
            if ($update_id) {
                $this->buyback->insert_bb_state_change($data['order_id'], _247AROUND_BB_IN_PROCESS, _247AROUND_BB_ORDER_NOT_RECEIVED_INTERNAL_STATUS, 
                        $agent_id, Null, $data['cp_id']);

                $this->session->set_userdata('success', 'Order has been updated successfully');
                redirect(base_url() . 'service_center/buyback/bb_order_details');
            } else {
                $this->session->set_userdata('error', 'Oops!!! There are some issue in updating order. Please Try Again...');
                redirect(base_url() . 'service_center/buyback/bb_order_details');
            }
        }
    }
    /**
     * @desc It check if sc update gst form first then show its profile otherwies GST form
     */
    function gst_update_form(){
        //$this->checkUserSession();
        log_message('info', __METHOD__ . $this->session->userdata('service_center_id'));
        $data = $this->reusable_model->get_search_result_data("service_centres","id as service_center_id,company_name,address as company_address,pan_no as company_pan_number"
                . ",is_gst_doc as is_gst,gst_no as company_gst_number,gst_file as gst_certificate_file,signature_file",
               array("id"=>$this->session->userdata('service_center_id')),NULL,NULL,NULL,NULL,NULL,array());
        //echo "<pre>";        print_r($data);exit();
        if($data[0]['is_gst'] == 1 && !empty($data[0]['gst_certificate_file'])){
            $this->load->view('service_centers/header'); 
            $this->load->view('service_centers/gst_details_view', $data[0]);
        } else {
            $this->load->view('service_centers/header');
            $this->load->view('service_centers/gst_update_form',$data[0]);
            
        }
    }
    /**
     * @desc This is used to insert gst for data.
     */
    function process_gst_update() {

        //$this->checkUserSession();
        log_message('info', __METHOD__ . $this->session->userdata('service_center_id'));
        $this->load->library('table');

        $this->form_validation->set_rules('company_name', 'Company Name', 'trim|required');
        $this->form_validation->set_rules('company_address', 'Company Address', 'trim|required');
        $this->form_validation->set_rules('pan_number', 'PAN NUmber', 'required|trim|min_length[10]|max_length[10]');
        $this->form_validation->set_rules('is_gst', 'Have You GST No.', 'required');
        if(empty($this->input->post('is_signature_aval'))){
            $this->form_validation->set_rules('signature_file', 'Signature file', 'callback_upload_signature');
        }

        if ($this->form_validation->run() === false) {
            $this->gst_update_form();
        } else {

            $status_flag = true;
            $is_gst = $this->input->post('is_gst');
            $is_gst_number = NULL;
            $gst_file_name = NULL;
            $gst_number = NULL;

            if ($is_gst == 1) {
                $this->form_validation->set_rules('gst_number', 'Company GST Number', 'required|trim|min_length[15]|max_length[15]|regex_match[/^[0-9]{2}[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[0-9]{1}[a-zA-Z]{1}[a-zA-Z0-9]{1}/]');
                $this->form_validation->set_rules('file', 'Company GST File', 'callback_upload_gst_certificate_file');

                if ($this->form_validation->run() === false) {

                    $this->gst_update_form();
                    $status_flag = false;
                } else {
                    $is_gst_number = $this->input->post('gst_number');
                    $gst_file_name = $this->input->post('gst_cer_file');
                    $gst_number = $this->input->post('gst_number');
                }
            }

            if (!empty($this->input->post('is_signature_doc'))) {
                $gst_details['signature_file'] = $this->input->post('signature_file_name');
                $sc['is_signature_doc'] = 1;
                $sc['signature_file'] = $gst_details['signature_file'];
            }


            // It not Accessed When validation failed above
            if ($status_flag) {
                $gst_details['service_center_id'] = $this->session->userdata('service_center_id');
                $gst_details['company_name'] = $this->input->post('company_name');
                $gst_details['company_address'] = $this->input->post('company_address');
                $gst_details['company_pan_number'] = $this->input->post('pan_number');
                $gst_details['is_gst'] = $this->input->post('is_gst');
                $gst_details['company_gst_number'] = $gst_number;
                $gst_details['gst_certificate_file'] = $gst_file_name;
                $gst_details['create_date'] = date('Y-m-d H:i:s');

                $sc['is_gst_doc'] = $gst_details['is_gst'];
                $sc['gst_no'] = $gst_details['company_gst_number'];
                $sc['gst_file'] = $gst_details['gst_certificate_file'];

                $sc['agent_id'] = _247AROUND_DEFAULT_AGENT;
                $this->vendor_model->edit_vendor($sc, $this->session->userdata('service_center_id'));

                $template = array(
                    'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
                );

                $this->table->set_template($template);

                $this->table->set_heading(array('SC Name', 'Company Name', 'Company Address', 'Pan', 'IS GST', 'GST NUmber', 'GST FILE', 'Signature File'));
                $this->table->add_row($this->session->userdata('service_center_name'), $gst_details['company_name'], $gst_details['company_address'], $gst_details['company_pan_number'], !empty($gst_details['is_gst']) ? "YES" : "NO", $gst_details['company_gst_number'], !empty($sc['gst_file']) ? "https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/" . $sc['gst_file'] : '', !empty($sc['signature_file']) ? "https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/" . $sc['signature_file'] : '');

                $to = NITS_ANUJ_EMAIL_ID;

                $rm_details = $this->vendor_model->get_rm_sf_relation_by_sf_id($this->session->userdata('service_center_id'));
                $cc = $rm_details[0]['official_email'];

                $subject = "GST Form Updated By " . $this->session->userdata('service_center_name');
                $message = "";
                $message .= $this->table->generate();

                $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $message, "",GST_FORM_UPDATED);

                redirect(base_url() . "service_center/gst_details");
            }
        }
    }

    function upload_signature() {
        $allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "bmp", "BMP", "GIF", "PNG");
        $temp = explode(".", $_FILES["signature_file"]["name"]);
        $extension = end($temp);
        if (($_FILES['signature_file']['error'] != 4) && !empty($_FILES['signature_file']['tmp_name'])) {
            if ($_FILES["signature_file"]["name"] != null) {
                if (($_FILES["signature_file"]["size"] < 2e+6) && in_array($extension, $allowedExts)) {
                    if ($_FILES["signature_file"]["error"] > 0) {
                        $this->form_validation->set_message('upload_signature', $_FILES["signature_file"]["error"]);
                        return FALSE;
                    } else {
                        $pic = md5(uniqid(rand()));
                        $picName = $pic . "." . $extension;
                        $_POST['signature_file_name'] = $picName;

                        $bucket = BITBUCKET_DIRECTORY;
                        $directory = "vendor-partner-docs/" . $picName;
                        $this->s3->putObjectFile($_FILES["signature_file"]["tmp_name"], $bucket, $directory, S3::ACL_PUBLIC_READ);
                        $_POST['is_signature_doc'] = 1;
                        return TRUE;
                    }
                } else {
                    $this->form_validation->set_message('upload_signature', 'File size or File type is not supported.Allowed extentions are "png", "jpg", "jpeg". Maximum file size is 2MB.');
                    return FALSE;
                }
            }
        } else {

            $this->form_validation->set_message('upload_signature', 'Please Attach Signature Image File');
            return false;
        }
    }

    /**
     * @desc Upload GST Certificate FIle to S3
     * @return String
     */
    function upload_gst_certificate_file(){
       log_message('info', __METHOD__ . " :".$this->session->userdata('service_center_id'));
      
        if (($_FILES['file']['error'] != 4) && !empty($_FILES['file']['tmp_name']) ) {

            $tmpFile = $_FILES['file']['tmp_name'];
            $extention =  explode(".", $_FILES['file']['name'])[1];
            $gst_file = $this->session->userdata('service_center_id').'-gst-' 
                    . substr(md5(uniqid(rand(0, 9))), 0, 10) . "." .$extention;
            //move_uploaded_file($tmpFile, TMP_FOLDER . $support_file_name);
            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "vendor-partner-docs/" . $gst_file;
            $upload_file_status = $this->s3->putObjectFile($tmpFile, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            if($upload_file_status){
                //Logging success for file uppload
                log_message('info', __METHOD__ . 'GST Certificate File Uploaded: '.$this->session->userdata('service_center_id'));
                $_POST['gst_cer_file'] = $gst_file;
                return true;
            }else{
                //Logging success for file uppload
                log_message('info', __METHOD__ . 'Error In uploading sGST Certificate : '.$this->session->userdata('service_center_id'));
                $this->form_validation->set_message('upload_gst_certificate_file', 'Please Valid GST File.');

                return false;
            }

        } else {
          
            $this->form_validation->set_message('upload_gst_certificate_file', 'Please Attach GST Certificate File.');
            return false;
        }
    }
    
    function get_vendor_rating(){
        $rating_data = $this->service_centers_model->get_vendor_rating_data($this->session->userdata('service_center_id'));
        if(!empty($rating_data)){
            echo $rating_data[0]['rating'];
        }else{
            echo '0';
        }
    }
    
    /**
     * @desc Used to get data as requested and also search 
     */
    function get_bb_order_details() {
        //log_message("info",__METHOD__);
        $data = array();
        switch ($this->input->post('status')){
            case 0:
                $data = $this->get_delivered_data();
                break;
            case 1:
                $data = $this->get_pending_data();
                break;
            case 2:
                $data = $this->get_acknowledge_data();
                break;
        }
        
        $post = $data['post'];
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->cp_model->cp_order_list_count_all($post),
            "recordsFiltered" =>  $this->cp_model->cp_order_list_count_filtered($post),
            "data" => $data['data'],
        );
        unset($post);
        unset($data);
        echo json_encode($output);
    }
    
    
    /**
     * @desc Used to get  delivered buyback data
     * @param void
     * @return array
     */
    function get_delivered_data(){
        //log_message("info",__METHOD__);      
        $post = $this->get_post_view_data();
        $post['where'] = array('assigned_cp_id' => $this->session->userdata('service_center_id'),
            'bb_cp_order_action.current_status' => 'Pending', 'bb_order_details.internal_status' => 'Delivered','bb_order_details.current_status' => 'Delivered');
        $post['where_in'] = array();
        $post['column_order'] = array( NULL,'bb_order_details.partner_order_id','bb_order_details.partner_tracking_id','services','category',
              'cp_basic_charge','category','delivery_date',NULL,NULL);
        $post['column_search'] = array('bb_order_details.partner_order_id','bb_order_details.partner_tracking_id', 'services', 'city',
            'order_date', 'delivery_date', 'bb_cp_order_action.current_status');
        $list = $this->cp_model->get_bb_cp_order_list($post);
        
        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row =  $this->get_delivered_table_data($order_list, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
            
                );

    }
    
    /**
     * @desc Used to get pending buyback data
     * @param void
     * @return array
     */
    function get_pending_data(){
        //log_message("info",__METHOD__);
        $post = $this->get_post_view_data();
        $post['where'] = array('assigned_cp_id' => $this->session->userdata('service_center_id'),
            'bb_cp_order_action.current_status' => 'Pending');
        $post['where_in'] = array('bb_order_details.internal_status' => array('In-Transit', 'New Item In-transit', 'Attempted'),
            'bb_order_details.current_status' => array('In-Transit', 'New Item In-transit', 'Attempted'));
        $post['column_order'] = array( NULL,'bb_order_details.partner_order_id','bb_order_details.partner_tracking_id','services', 'category',
              'order_date','cp_basic_charge',NULL,NULL);
        $post['column_search'] = array('bb_order_details.partner_order_id','bb_order_details.partner_tracking_id', 'services', 'bb_unit_details.category','order_date', 'cp_basic_charge');
        $list = $this->cp_model->get_bb_cp_order_list($post);
        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row =  $this->get_pending_table_data($order_list, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
            
                );
        
    }
    
    /**
     * @desc Used to get acknowledge buyback data
     * @param void
     * @return array
     */
    function get_acknowledge_data(){
        $post = $this->get_post_view_data();
        $post['where'] = array('assigned_cp_id' => $this->session->userdata('service_center_id'));
        $post['where_in'] = array('bb_cp_order_action.current_status' => array('Delivered', 'InProcess', 'Not Delivered', 'Damaged'),
                                  'bb_cp_order_action.internal_status' => array('Delivered', 'Not Delivered', 'Refunded','Damaged'));
        $post['column_order'] = array( NULL,'bb_order_details.partner_order_id','bb_order_details.partner_tracking_id','services','category',
                                'order_date','delivery_date','cp_basic_charge',NULL,NULL);
        $post['column_search'] = array('bb_order_details.partner_order_id','bb_order_details.partner_tracking_id', 'services', 'city',
            'order_date', 'delivery_date', 'bb_cp_order_action.current_status');
        $list = $this->cp_model->get_bb_cp_order_list($post);
        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row =  $this->get_acknowledge_table_data($order_list, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
            
                );
    }
    
    
    /**
     * @desc Used to get  delivered buyback data table
     * @param $order_list
     * @param $no
     * @return array
     */
    function get_delivered_table_data($order_list, $no) {
        //log_message("info", __METHOD__);
        $row = array();
        $datetime1 = date_create(date("Y-m-d"));
        $datetime2 = date_create(date('Y-m-d', strtotime($order_list->delivery_date)));

        $interval = date_diff($datetime1, $datetime2);
        $days = $interval->days;
        if ($interval->invert == 1) {
            $days = -$days;
        }
        $row[] = $no;
        $row[] = "<a target='_blank' href='" . base_url() . "service_center/buyback/view_bb_order_details/" .
                $order_list->partner_order_id . "'>$order_list->partner_order_id</a>";
        $row[] = $order_list->partner_tracking_id;
        $row[] = $order_list->services;
        $row[] = $order_list->category;
        $row[] = ($order_list->cp_basic_charge + $order_list->cp_tax_charge);
        $row[] = $order_list->order_date;
        $row[] = $order_list->delivery_date;
        $row[] = "<div class='truncate_text' data-toggle='popover' title='" . $order_list->admin_remarks . "'>$order_list->admin_remarks</div>";
        $a = "<div class='dropdown'>
                            <button class='btn btn-default dropdown-toggle' type='button' id='menu1' data-toggle='dropdown'>Actions
                            <span class='caret'></span></button>
                            <ul class='dropdown-menu' role='menu' aria-labelledby='menu1'>
                              <li role='presentation'><a role='menuitem' tabindex='-1' onclick=showConfirmDialougeBox('" . base_url() . "service_center/buyback/update_received_bb_order/" . rawurlencode($order_list->partner_order_id) . "/" . rawurlencode($order_list->service_id) . "/" . rawurlencode($order_list->city) . "/" . rawurlencode($order_list->assigned_cp_id) . "')>Received</a></li>";
        ;
        if ($days < NO_OF_DAYS_NOT_SHOW_NOT_RECEIVED_BUTTON) {
          
            $a .= "<li role='presentation'><a role='menuitem' tabindex='-1' onclick=showConfirmDialougeBox('" . base_url() . "service_center/buyback/update_not_received_bb_order/" . rawurlencode($order_list->partner_order_id) . "/" . rawurlencode($order_list->service_id) . "/" . rawurlencode($order_list->city) . "/" . rawurlencode($order_list->assigned_cp_id) . "')>Not Received</a></li>";
        }
        $a .= "<li role='presentation'><a role='menuitem' tabindex='-1' target='_blank' href='" . base_url() . "service_center/buyback/update_order_details/" . rawurlencode($order_list->partner_order_id) . "/" . rawurlencode($order_list->service_id) . "/" . rawurlencode($order_list->city) . "/" . rawurlencode($order_list->assigned_cp_id) . "'>Broken/Wrong Product</a></li>
                            </ul>
                          </div>";
        $row[] = $a;

        return $row;
    }

    /**
     * @desc Used to get pending buyback data table
     * @param $order_list
     * @param $no
     * @return array
     */
    function get_pending_table_data($order_list, $no) {
        //log_message("info", __METHOD__);
        $row = array();
        $row[] = $no;
        $row[] = "<a target='_blank' href='".base_url()."service_center/buyback/view_bb_order_details/".
                $order_list->partner_order_id."'>$order_list->partner_order_id</a>";
        $row[] = $order_list->partner_tracking_id;
        $row[] = $order_list->services;
        $row[] = $order_list->category;
        $row[] = $order_list->order_date;
        $row[] = ($order_list->cp_basic_charge + $order_list->cp_tax_charge);
        $row[] = "<div class='dropdown'>
                            <button class='btn btn-default dropdown-toggle' type='button' id='menu1' data-toggle='dropdown'>Actions
                            <span class='caret'></span></button>
                            <ul class='dropdown-menu' role='menu' aria-labelledby='menu1'>
                              <li role='presentation'><a role='menuitem' tabindex='-1' onclick=showConfirmDialougeBox('" . base_url() . "service_center/buyback/update_received_bb_order/" . rawurlencode($order_list->partner_order_id) . "/" . rawurlencode($order_list->service_id) . "/" . rawurlencode($order_list->city) . "/" . rawurlencode($order_list->assigned_cp_id) . "')>Received</a></li>
                              <li role='presentation'><a role='menuitem' tabindex='-1' target='_blank' href='" . base_url() . "service_center/buyback/update_order_details/" . rawurlencode($order_list->partner_order_id) . "/" . rawurlencode($order_list->service_id) . "/" . rawurlencode($order_list->city) . "/" . rawurlencode($order_list->assigned_cp_id) . "'>Broken/Wrong Product</a></li>
                            </ul>
                          </div>";
       
        
        return $row;
    }
    
    /**
     * @desc Used to get acknowledge buyback data table
     * @param $order_list
     * @param $no
     * @return array
     */
    function get_acknowledge_table_data($order_list, $no) {
        //log_message("info", __METHOD__);
        $row = array();
        $row[] = $no;
        $row[] = "<a target='_blank' href='".base_url()."service_center/buyback/view_bb_order_details/".
                $order_list->partner_order_id."'>$order_list->partner_order_id</a>";
        $row[] = $order_list->partner_tracking_id;
        $row[] = $order_list->services;
        $row[] = $order_list->category;
        $row[] = $order_list->order_date;
        $row[] = $order_list->delivery_date;
        $row[] = ($order_list->cp_basic_charge + $order_list->cp_tax_charge);
        $row[] = $order_list->current_status."<b> (".$order_list->internal_status." )</b>";
        return $row;
    }
    
    /**
     * @desc Used to get  post data from the datatable
     * @param void
     * @return $post array()
     */
    function get_post_view_data(){
        //log_message("info",__METHOD__);
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
        $post['status'] = $this->input->post('status');
        
        return $post;
    }
    
    /**
     * @desc Used to get the bb price list according to cp
     * @param void
     * @return void
     */
    function show_bb_price_list(){
        $this->check_BB_UserSession();
        $select = 'service_id,s.services';
        $where['cp_id'] = $this->session->userdata('service_center_id');
        $data['appliance_list'] = $this->bb_model->get_bb_price_data($select, $where, true, true);
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/show_show_bb_price_list',$data);
    }
    
    /**
     * @desc This function is used to the filtered charges data from bb_charges table
     * @param void()
     * @return void()
     */
    function get_bb_price_list(){
        $response = $this->buyback->get_bb_price_list($this->input->post());
        echo $response;
    }
    
    function get_bb_cp_charges($cp_id) {

        $where['length'] = -1;
        $data = array();
        //get delivered charges by month
        $where['where_in'] = array('current_status' => array('Delivered', 'Completed'));
        $select = "SUM(CASE WHEN ( bb_unit_details.cp_claimed_price > 0) 
                THEN (bb_unit_details.cp_claimed_price) 
                ELSE (bb_unit_details.cp_basic_charge) END ) as cp_delivered_charge, count(bb_order_details.partner_order_id) as total_delivered_order";
        for ($i = 0; $i < 3; $i++) {
            if ($i == 0) {
                //$delivery_date = "bb_order_details.delivery_date >=  '" . date('Y-m-01') . "'";
                $delivery_date = "(CASE WHEN acknowledge_date IS NOT Null THEN `bb_order_details`.`acknowledge_date` >= '" . date('Y-m-01') . "' ELSE `bb_order_details`.`delivery_date` >= '" . date('Y-m-01') . "'  END)";
                $select .= ", date(now()) As month";
            }else if ($i == 1) {
                $delivery_date = "(CASE WHEN acknowledge_date IS NOT Null THEN bb_order_details.acknowledge_date  >=  DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01')
			    AND bb_order_details.acknowledge_date < DATE_FORMAT(NOW() ,'%Y-%m-01') ELSE bb_order_details.delivery_date  >=  DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01')
			    AND bb_order_details.delivery_date < DATE_FORMAT(NOW() ,'%Y-%m-01') END) ";
                $select .= ", DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01') as month";
            }else if ($i == 2) {
                $delivery_date = "(CASE WHEN acknowledge_date IS NOT Null THEN bb_order_details.acknowledge_date  >=  DATE_FORMAT(NOW() - INTERVAL 2 MONTH, '%Y-%m-01')
			    AND bb_order_details.acknowledge_date < DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01') ELSE bb_order_details.delivery_date  >=  DATE_FORMAT(NOW() - INTERVAL 2 MONTH, '%Y-%m-01')
			    AND bb_order_details.delivery_date < DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01') END)";
                $select .= ", DATE_FORMAT(NOW() - INTERVAL 2 MONTH, '%Y-%m-01') as month";
            }
            
            $where['where'] = array('assigned_cp_id' => $cp_id,"$delivery_date" => NULL);
            $cp_delivered_charge[$i] = $this->bb_model->get_bb_order_list($where, $select);
            
        }
        
        //get total delivered charges data
        $where['where'] = array('assigned_cp_id' =>$cp_id);
        
        
        //get in_transit data by month
        $where['where_in'] = array('current_status' => array('In-Transit', 'New Item In-transit', 'Attempted'));
        $select_in_transit = "SUM(CASE WHEN ( bb_unit_details.cp_claimed_price > 0) 
                THEN (bb_unit_details.cp_claimed_price) 
                ELSE (bb_unit_details.cp_basic_charge) END ) as cp_in_transit_charge,count(bb_order_details.partner_order_id) as total_inTransit_order";
        for ($i = 0; $i < 3; $i++) {
            if ($i == 0) {
                $in_transit_date = "bb_order_details.order_date >=  '" . date('Y-m-01') . "'";
                $select_in_transit .= ", date(now()) As month";
            }else if ($i == 1) {
                $in_transit_date = "bb_order_details.order_date  >=  DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01')
			    AND bb_order_details.order_date < DATE_FORMAT(NOW() ,'%Y-%m-01')  ";
                $select_in_transit .= ", DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01') as month";
            }else if ($i == 2) {
                $in_transit_date = "bb_order_details.order_date  >=  DATE_FORMAT(NOW() - INTERVAL 2 MONTH, '%Y-%m-01')
			    AND bb_order_details.order_date < DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01')";
                $select_in_transit .= ", DATE_FORMAT(NOW() - INTERVAL 2 MONTH, '%Y-%m-01') as month";
            }
            
            $where['where'] = array('assigned_cp_id' => $cp_id,"$in_transit_date" => NULL);
            $cp_in_transit_charge[$i] = $this->bb_model->get_bb_order_list($where, $select_in_transit);
        }

        //get total in transit charges data
        
       
       $amount_cr_deb = $this->miscelleneous->get_cp_buyback_credit_debit($cp_id);
            
            
        $data['delivered_charges'] = $cp_delivered_charge;
        $data['in_transit_charges'] = $cp_in_transit_charge;
        $data['total_charges'] = $amount_cr_deb['total_balance'];
        $this->load->view('service_centers/show_bb_charges_summary',$data);
    }
    
    /**
     * @desc Used to get data as requested and also search 
     */
    function get_sf_data() {
        $data = array();
        switch ($this->input->post('status')){
            case 0:
                $data = $this->get_sf_charges_data();
                break;
        }
        
        $post = $data['post'];
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->service_centre_charges_model->count_all_charges($post),
            "recordsFiltered" =>  $this->service_centre_charges_model->count_filtered_charges($post),
            "data" => $data['data'],
        );
        
        unset($post);
        unset($data);
        echo json_encode($output);
    }
    
    /**
     * @desc Used to get sf charges data
     * @param void
     * @return array
     */
    function get_sf_charges_data(){
        $this->checkUserSession();
        //Getting SC ID from session
        $service_center_id  =  $this->session->userdata('service_center_id');
        if(!empty($service_center_id)){
            //Getting SF Details
            $sc_details = $this->vendor_model->getVendorContact($service_center_id);
            
            $post = $this->get_post_view_data();
            $new_post = $this->get_filterd_post_data($post,$sc_details[0]['state']);
            
            $select = "service_centre_charges.category,service_centre_charges.capacity,"
                    . "service_centre_charges.service_category,service_centre_charges.vendor_total,service_centre_charges.partner_id, "
                    . "service_centre_charges.customer_net_payable,service_centre_charges.pod,tax_rates.rate , services.services as product";
            
            //Getting Charges Data
            $list = $this->service_centre_charges_model->get_service_centre_charges_by_any($new_post,$select);
            $data = array();
            $no = $post['start'];
            foreach ($list as $charges_list) {
                $no++;
                $row = $this->get_charges_list_table($charges_list, $no);
                $data[] = $row;
            }
        
        }
        
        return array(
                'data' => $data,
                'post' => $new_post
                );

    }
    
    /**
     *  @desc : This function is used to make filter logic for pagination
     *  @param : $post string
     *  @param : $state string
     *  @return : $post Array()
     */
    private function get_filterd_post_data($post,$state){
        $product = $this->input->post('product');
        $category = $this->input->post('category');
        $capacity = $this->input->post('capacity');
        $service_category = $this->input->post('service_category');
        
        $post['where']  = array('tax_rates.state' => $state);
        
        if(!empty($product)){
            $post['where']['service_id'] =  $product;
        }
        if(!empty($category)){
            $post['where']['category'] =  $category;
        }
        if(!empty($capacity)){
            $post['where']['capacity'] =  $capacity;
        }
        if(!empty($service_category)){
            $post['where']['service_category'] =  $service_category;
        }
        
        $post['column_order'] = array(NULL,NULL,'service_id','category','capacity','service_category',NULL,NULL,'vendor_total','customer_net_payable','pod');
        $post['column_search'] = array();
        
        return $post;
    }
    
    function get_charges_list_table($charges_list, $no) {
        $row = array();
        
        //Getting Details from Booking Sources
//        $booking_sources = $this->partner_model->get_booking_sources_by_price_mapping_id($charges_list->partner_id);
//        $code_source = $booking_sources[0]['code'];

        //Calculating vendor base charge 
        $vendor_base_charge = $charges_list->vendor_total / (1 + ($charges_list->rate / 100));
        //Calculating vendor tax - [Vendor Total - Vendor Base Charge]
        $vendor_tax = $charges_list->vendor_total - $vendor_base_charge;
        
        $row[] = $no;
        //$row[] = $code_source;
        $row[] = $charges_list->product;
        $row[] = $charges_list->category;
        $row[] = $charges_list->capacity;
        $row[] = $charges_list->service_category;
        $row[] = round($vendor_base_charge, 0);
        $row[] = round($vendor_tax, 0);
        $row[] = round($charges_list->vendor_total, 0);
        $row[] = round($charges_list->customer_net_payable, 0);
        $row[] = $charges_list->pod;

        return $row;
    }
    
    function view_bb_order_details($partner_order_id){
        $data['partner_order_id'] = $partner_order_id;
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/view_bb_order_details', $data);
    }
    
    /**
     * @desc Used to get the order details data to take action 
     * @param $partner_order_id string
     * @return $data json
     */
    function get_bb_order_details_data($partner_order_id){
        $this->check_BB_UserSession();
        log_message("info",__METHOD__);
        if($partner_order_id){
            $data = $this->bb_model->get_bb_order_details(
                    array('bb_order_details.partner_order_id' =>$partner_order_id),
                    'bb_order_details.*, name as cp_name, public_name as partner_name');
            print_r(json_encode($data));
        }
        
    }
    
    
    /**
     * @desc Used to get order history data
     * @param $partner_order_id string
     * @return $data json
     */
    function get_bb_order_history_details($partner_order_id){
        log_message("info",__METHOD__);
        if($partner_order_id){
            $data = $this->bb_model->get_bb_order_history($partner_order_id);
            print_r(json_encode($data));
        }
    }
    
    
    /**
     * @desc Used to get the order appliance details
     * @param $partner_order_id string
     * @return $data json
     */
    function get_bb_order_appliance_details($partner_order_id){
        log_message("info",__METHOD__);
        if($partner_order_id){
            $select = 'bb_unit.category, bb_unit.physical_condition, 
                bb_unit.working_condition,
                round(bb_unit.cp_basic_charge + bb_unit.cp_tax_charge) as cp_tax,
                bb_unit.partner_sweetner_charges,s.services as service_name,bb_unit.cp_claimed_price,bb_unit.cp_invoice_id';
            $data = $this->bb_model->get_bb_order_appliance_details(array('partner_order_id' => $partner_order_id), $select);
            print_r(json_encode($data));
        }
    }
    
    /**
     * @desc Used to get sf escalation percentage
     * @param $sf_id string
     * @return $escalation_per string
     */
    function get_sf_escalation($sf_id){
        if(!empty($sf_id)){
            $total_escalation_per = "";
            $current_month_escalation_per = "";
            $total_booking = $this->reusable_model->get_search_query('booking_details','count(booking_id) AS total_booking',array('assigned_vendor_id' => $sf_id),NULL,NULL,NULL,NULL,NULL)->result_array();
            $total_escalation = $this->reusable_model->get_search_query('vendor_escalation_log','count(booking_id) AS total_escalation',array('vendor_id' => $sf_id),NULL,NULL,NULL,NULL,NULL)->result_array();
            if(!empty($total_booking[0]['total_booking'])){
                $total_escalation_per = ($total_escalation[0]['total_escalation']*100)/$total_booking[0]['total_booking'];
            }
            
            $current_month_booking = $this->reusable_model->get_search_query('booking_details','count(booking_id) AS total_booking',array('assigned_vendor_id' => $sf_id,"month(STR_TO_DATE(booking_details.booking_date,'%d-%m-%Y')) = month(now()) AND year(STR_TO_DATE(booking_details.booking_date,'%d-%m-%Y')) = year(now())" => NULL),NULL,NULL,NULL,NULL,NULL)->result_array();
            $current_month__escalation = $this->reusable_model->get_search_query('vendor_escalation_log','count(booking_id) AS total_escalation',array('vendor_id' => $sf_id,"month(create_date) = month(now()) AND year(create_date) = year(now())" => NULL),NULL,NULL,NULL,NULL,NULL)->result_array();
            if(!empty($current_month_booking[0]['total_booking'])){
                $current_month_escalation_per = ($current_month__escalation[0]['total_escalation']*100)/$current_month_booking[0]['total_booking'];
            }
            
            $response['total_escalation_per'] = !empty($total_escalation_per)?sprintf("%1\$.2f",$total_escalation_per):0;
            $response['current_month_escalation_per'] = !empty($current_month_escalation_per)?sprintf("%1\$.2f",$current_month_escalation_per):0;
            
            echo json_encode($response);
            
        }else{
            echo 'empty';
        }
        
    }
    /**
     * @desc This is uesd to for buyback search. It will get the data from Order ID/Tracking ID
     */
    function search_for_buyback(){
        $this->check_BB_UserSession();
        $post['search_value'] = trim($this->input->post('search'));
        $post['column_search'] = array('bb_order_details.partner_order_id', 'bb_order_details.partner_tracking_id');
        $post['where'] = array('assigned_cp_id' =>  $this->session->userdata('service_center_id'));
        $post['where_in'] = array();
        $post['column_order'] = array();
        $post['length'] = -1;
        
        $list['list'] = $this->cp_model->get_bb_cp_order_list($post);
        $this->load->view('service_centers/search_for_buyback',$list);
            
    }
    
    public function get_contact_us_page(){
        //$this->checkUserSession();
        $data['rm_details'] = $this->vendor_model->get_rm_sf_relation_by_sf_id($this->session->userdata('service_center_id'));
        $this->load->view('service_centers/contact_us',$data);
    }
    /**
     * @desc This is used to Approve Spare Estimate by SF
     * @param String $booking_id
     */
    function approve_oow($booking_id) {
        log_message("info",__METHOD__. "Enterring");
        if (!empty($booking_id)) {
            $req['where'] = array("spare_parts_details.booking_id" => $booking_id, "status" => SPARE_OOW_EST_GIVEN);
            $req['length'] = -1;
            $req['select'] = "spare_parts_details.id";
            $sp_data =$this->inventory_model->get_spare_parts_query($req);
            if(!empty($sp_data)){
                log_message("info",__METHOD__. "Spare parts Not found". $booking_id);
                $sc['current_status'] = "InProcess";
                $sc['update_date'] = date('Y-m-d H:i:s');
                $sc['internal_status'] = SPARE_PARTS_REQUIRED;
                // UPDATE SC Action Table
                $this->service_centers_model->update_service_centers_action_table($booking_id, $sc);

                // UPDATE Spare Parts
                $this->service_centers_model->update_spare_parts(array('id' => $sp_data[0]->id), array("status" => SPARE_PARTS_REQUESTED, 'date_of_request' => date('Y-m-d')));

                $this->insert_details_in_state_change($booking_id, SPARE_PARTS_REQUESTED, ESTIMATE_APPROVED_BY_CUSTOMER,"not_define","not_define");
                $partner_id = $this->input->post("partner_id");
                $this->update_booking_internal_status($booking_id, ESTIMATE_APPROVED_BY_CUSTOMER,  $partner_id);
                
                $userSession = array('success' => 'Booking Updated');
                $this->session->set_userdata($userSession);
                redirect(base_url() . "service_center/pending_booking");
            } else {
                log_message("info",__METHOD__. "Spare Not not found ". $booking_id);
                $userSession = array('error' => 'Booking Not Updated');
                $this->session->set_userdata($userSession);
                redirect(base_url() . "service_center/pending_booking");
            }
            
        } else {
            log_message("info",__METHOD__. "Booking ID not found ". $booking_id);
            $userSession = array('error' => 'Booking Not Updated');
            $this->session->set_userdata($userSession);
            redirect(base_url() . "service_center/pending_booking");
        }
    }
    /*
     * This Function send SMS to Customer When SF request For Booking Reschedule (To Know is Reschedule Fake)
     */
    function send_reschedule_confirmation_sms($booking_id){
        $join["users"] = "users.user_id = booking_details.user_id";
        $join["services"] = "services.id = booking_details.service_id";
        $data = $this->reusable_model->get_search_result_data("booking_details","users.user_id,users.phone_number,services.services",array("booking_details.booking_id"=>$booking_id),
                $join,NULL,NULL,NULL,NULL,array());
        if(!empty($data[0])){
            $sms['tag'] = BOOKING_RESCHEDULED_CONFIRMATION_SMS;
            $sms['phone_no'] = $data[0]['phone_number'];
            $sms['smsData'] = array("service"=>$data[0]['services']);
            $sms['booking_id'] = $booking_id;
            $sms['type'] = "user";
            $sms['type_id'] = $data[0]['user_id'];
            $this->notify->send_sms_msg91($sms);
        }
    }
    
    /**
     * @desc this function is used to create the challan number 
     * @param String $name
     * @return String challan number
     */
    function create_sf_challan_id($name){
        $challan_id_tmp = $name."-DC-";
        $where['length'] = -1;
        $where['where'] = array("( sf_challan_number LIKE '%".$challan_id_tmp."%' )" => NULL);
        $where['select'] = "sf_challan_number";
        $challan_no_temp = $this->inventory_model->get_spare_parts_query($where);
        $challan_no = 1;
        $int_challan_no = array();
        if (!empty($challan_no_temp)) {
            foreach ($challan_no_temp as  $value) {
                 $explode = explode($challan_id_tmp, $value->sf_challan_number);
                 array_push($int_challan_no, $explode[1] + 1);
            }
            rsort($int_challan_no);
            $challan_no = $int_challan_no[0];
        }
        
        return trim($challan_id_tmp . sprintf("%'.04d\n", $challan_no));
    }
    
    
    /**
     * @desc this function is used to generate the challan PDF file
     * @param array $sf_details
     * @param array $partner_details
     * @param String $sf_challan_number
     * @param String $spare_id
     * @return String $output_pdf_file_name
     */
    function create_sf_challan_file($sf_details, $partner_details, $sf_challan_number, $spare_id) {
        $excel_data['sf_name'] = $sf_details[0]['name'];
        $excel_data['sf_address'] = $sf_details[0]['address'];
        $excel_data['partner_name'] = $partner_details[0]['company_name'];
        $excel_data['partner_address'] = $partner_details[0]['address'];
        $excel_data['partner_gst'] = $partner_details[0]['gst_number'];
        $excel_data['partner_challan_no'] = $this->input->post('partner_challan_number')[$spare_id];
        $excel_data['sf_challan_no'] = $sf_challan_number;
        $excel_data['date'] = date('Y-m-d');
        $excel_data['value'] = $this->input->post('challan_approx_value')[$spare_id];
        $excel_data['booking_id'] = $this->input->post('booking_id');
        $excel_data['spare_desc'] = $this->input->post('parts_requested')[$spare_id];
        $excel_data['qty'] = 1;
        $excel_data['total_qty'] = 1;
        $excel_data['total_value'] = $excel_data['value'];
        if ($sf_details[0]['is_gst_doc'] == 1) {
            $excel_data['sf_gst'] = $sf_details[0]['gst_no'];
            $template = 'delivery_challan_with_gst.xlsx';
            $cell = FALSE;
            $signature_file = FALSE;
        } else if ($sf_details[0]['is_gst_doc'] == 0 && $sf_details[0]['is_signature_doc'] == 1) {
            $excel_data['sf_gst'] = '';
            $excel_data['sf_owner_name'] = $sf_details[0]['owner_name'];
            $template = "delivery_challan_without_gst.xlsx";
            $s3_bucket = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/".$sf_details[0]['signature_file'];
            //get signature file from s3 and save it to server
            copy($s3_bucket,TMP_FOLDER.$sf_details[0]['signature_file']);
            system(" chmod 777 " . TMP_FOLDER.$sf_details[0]['signature_file']);
            $cell = 'B46';
            $signature_file = TMP_FOLDER.$sf_details[0]['signature_file'];
        }else{
            $template = "";
        }
        
       if(!empty($template)){
            //generate pdf file 
            $output_file = "delivery_challan_" .$excel_data['booking_id']."_".$spare_id."_". date('d_M_Y_H_i_s');
            $excel_file = $this->miscelleneous->generate_excel_data($template, $output_file, $excel_data, false,$cell,$signature_file);
            $output_pdf_file_name = $excel_file;
            if (file_exists($excel_file)) {
                $json_result = $this->miscelleneous->convert_excel_to_pdf($excel_file, $excel_data['booking_id'], 'vendor-partner-docs');
                log_message('info', __FUNCTION__ . ' PDF JSON RESPONSE' . print_r($json_result, TRUE));
                $pdf_response = json_decode($json_result, TRUE);

                if ($pdf_response['response'] === 'Success') {
                    $output_pdf_file_name = $pdf_response['output_pdf_file'];
                    log_message('info', __FUNCTION__ . ' Generated PDF File Name' . $output_pdf_file_name);
                } else if ($pdf_response['response'] === 'Error') {

                    log_message('info', __FUNCTION__ . ' Error in Generating PDF File');
                }
            }
       }else{
           $output_pdf_file_name = "";
       }
        
        return $output_pdf_file_name;
    }

function get_learning_collateral_for_bookings(){
    $booking_id = $this->input->post('booking_id');
    $data = $this->service_centers_model->get_collateral_for_service_center_bookings($booking_id);
    if(!empty($data)){
        $finalString = '<table class="table">
        <thead>
          <tr>
          <th>S.N</th>
            <th>Document Type</th>
            <th>File</th>
          </tr>
        </thead>
        <tbody>';
        $index =0;
        foreach($data as $collatralData){
            $url = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/".$collatralData['file'];
            $index++;
            $finalString .= '<tr><td>'.$index.'</td>';
            $finalString .= '<td>'.$collatralData['collateral_type'].'</td>';
            $finalString .=  '<td>'.$this->miscelleneous->get_reader_by_file_type($collatralData['document_type'],$url,"400").'</td>';
            $finalString .='</tr>';
        }
       $finalString .='</tbody></table>';
    }
    else{
        $finalString = "<p style='text-align:center;'>Brand Collateral is not available</p>";
    }

   
   echo $finalString;
}

    function customer_invoice_details(){
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/customer_invoice_details');
    }

}
