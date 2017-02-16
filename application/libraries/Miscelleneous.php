<?php

class Miscelleneous {

    public function __construct() {
	$this->My_CI = & get_instance();
        $this->My_CI->load->helper(array('form', 'url'));
	$this->My_CI->load->library('email');
        $this->My_CI->load->library('partner_cb');
        $this->My_CI->load->library('asynchronous_lib');
        $this->My_CI->load->library('booking_utilities');
        $this->My_CI->load->library('notify');
	$this->My_CI->load->model('vendor_model');
	$this->My_CI->load->model('booking_model');
        $this->My_CI->load->model('upcountry_model');
       
        $this->My_CI->load->model('service_centers_model');
    }
    /**
     * @desc This method is used to check upcountry avaliablity on the basis of booking pincode, service id.
     * @param String $booking_city
     * @param String $booking_pincode
     * @param String $service_id
     * @return Array
     */
    function check_upcountry_vendor_availability($booking_city, $booking_pincode, $service_id, $assigned_vendor_id= false){
         log_message('info', __FUNCTION__ . ' => booking city' . $booking_city." booking pincode ". $booking_pincode
                 ." service id ".$service_id );
        //Get Available Vendor in this pincode who work this service
        $check_vendor = $this->My_CI->upcountry_model->get_vendor_upcountry($booking_pincode, $service_id, $assigned_vendor_id);
        $sf_city = $this->My_CI->vendor_model->get_city_from_india_pincode($booking_pincode)['district'];
        $data1 = array();
        $is_return = 0;
        $mesg1 = array();
        
        // if $check_vendor is empty then return because we are are providing service in this pincode
        if(!empty($check_vendor)){
            // If count is one, means only one vebdor is avaliable in this pincode
            if(count($check_vendor) ==1){
                if($check_vendor[0]['is_upcountry'] ==1){
                    $data['vendor_id'] = $check_vendor[0]['Vendor_ID'];
                    $data['city'] = $sf_city;
                    array_push($data1, $data);
                } else {
                    $msg['vendor_id'] = $check_vendor[0]['Vendor_ID'];
                    $msg['message'] = NOT_UPCOUNTRY_BOOKING;
                   
                    return $msg;
                }
            } else if(count($check_vendor > 1)){
                foreach($check_vendor as $vendor){
                    if($vendor['is_upcountry'] ==1){
                        $data['vendor_id'] = $vendor['Vendor_ID'];
                        $data['city'] = $sf_city;
                        
                        array_push($data1, $data);
                    } else {
                        $msg['vendor_id'] = $vendor['Vendor_ID'];
                        $msg['message'] = NOT_UPCOUNTRY_BOOKING;
                        $is_return = 1;
                        array_push($mesg1, $msg);
                    }
                }
                
                if($is_return ==1){
                   
                    if(count($mesg1) > 1){
                        $msg['message'] = SF_DOES_NOT_EXIST;
                        return $msg;
                        
                    } else {
                        
                        return $mesg1[0];
                    }
                }
            }
           
            return $this->My_CI->upcountry_model->action_upcountry_booking($booking_city,
                    $booking_pincode, $data1);
           
            
        
            
        } else {
            $msg['message'] = SF_DOES_NOT_EXIST;
            return $msg;
        }
        
    }
    /**
     * @desc This method is used to assign service center to booking
     * @param String $service_center_id
     * @param String $booking_id
     * @return boolean
     */
    function assign_vendor_process($service_center_id, $booking_id){
        log_message('info', __FUNCTION__ . " Entering...... booking_id " . $booking_id . " service center id " . $service_center_id);
        $b['assigned_vendor_id'] = $service_center_id;
        // Set Default Engineer 
        if (IS_DEFAULT_ENGINEER == TRUE) {
            $b['assigned_engineer_id'] = DEFAULT_ENGINEER;
        } else {
            $engineer = $this->My_CI->vendor_model->get_engineers($service_center_id);
            if (!empty($engineer)) {
                $b['assigned_engineer_id'] = $engineer[0]['id'];
            }
        }
        $b['upcountry_partner_approved'] = '1';
        //Assign service centre and engineer
        $assigned = $this->My_CI->vendor_model->assign_service_center_for_booking($booking_id, $b);
        if ($assigned) {
            log_message('info', __FUNCTION__ . " Assigned...... booking_id " . $booking_id);
            // Data to be insert in service center
            $sc_data['current_status'] = "Pending";
            $sc_data['update_date'] = date('Y-m-d H:i:s');
            $sc_data['internal_status'] = "Pending";
            $sc_data['service_center_id'] = $service_center_id;
            $sc_data['booking_id'] = $booking_id;

            // Unit Details Data
            $where = array('booking_id' => $booking_id);
            $unit_details = $this->My_CI->booking_model->get_unit_details($where);
            foreach ($unit_details as $value) {
                $sc_data['unit_details_id'] = $value['id'];
                $sc_id = $this->My_CI->vendor_model->insert_service_center_action($sc_data);

                if (!$sc_id) {
                    log_message('info', __METHOD__ . "=> Data is not inserted into service center "
                            . "action table booking_id: " . $booking_id . ", data: " . print_r($sc_data, true));
                }
            }
            
            log_message('info', __FUNCTION__ . " Exit...... booking_id " . $booking_id . " service center id " . $service_center_id);
            return true;
        } else {
            log_message('info', __METHOD__ . "=> Not Assign for Sc "
                    . $service_center_id);
            return false;
        }
    }
    
    function assign_upcountry_booking($booking_id, $agent_id, $agent_name){
        log_message('info', __METHOD__ . " => Entering " . $booking_id);
        $query1 = $this->My_CI->booking_model->getbooking_history($booking_id);
        $vendor_data = array();

        $vendor_data[0]['vendor_id'] = $query1[0]['assigned_vendor_id'];
        
        //$vendor_data[0]['city'] = $query1[0]['city'];
        $vendor_data[0]['city'] = $this->My_CI->vendor_model->get_city_from_india_pincode($query1[0]['booking_pincode'])['district'];
        
        $return_status = 0;
        $data = $this->My_CI->upcountry_model->action_upcountry_booking($query1[0]['city'], 
                $query1[0]['booking_pincode'], $vendor_data);

        switch ($data['message']) {
            case UPCOUNTRY_BOOKING:
            case UPCOUNTRY_LIMIT_EXCEED:
                log_message('info', __METHOD__ . " => " . $data['message'] . " booking_id " . $booking_id);
                $partner_details = $this->My_CI->partner_model->get_all_partner($query1[0]['partner_id']);
                if ($partner_details[0]['is_upcountry'] == 1) {
                    if ($partner_details[0]['upcountry_mid_distance_threshold'] > $data['upcountry_distance']) {
                        $upcountry_price = $partner_details[0]['upcountry_rate'] * $data['upcountry_distance'];
                        $data['partner_upcountry_rate'] = $partner_details[0]['upcountry_rate'];
                    } else {

                        $data['partner_upcountry_rate'] = $partner_details[0]['upcountry_rate1'];
                        $upcountry_price = $partner_details[0]['upcountry_rate1'] * $data['upcountry_distance'];
                    }
                    $partner_approval = $partner_details[0]['upcountry_approval'];
                } else{
                    $data['partner_upcountry_rate'] = DEFAULT_UPCOUNTRY_RATE;
                    $upcountry_price = DEFAULT_UPCOUNTRY_RATE * $data['upcountry_distance'];
                    $partner_approval = 0;
                }

                $booking['is_upcountry'] = 1;
                $booking['upcountry_pincode'] = $data['upcountry_pincode'];
                $booking['sub_vendor_id'] = $data['sub_vendor_id'];
                $booking['upcountry_distance'] = $data['upcountry_distance'];
                $booking['sf_upcountry_rate'] = $data['sf_upcountry_rate'];
                $booking['partner_upcountry_rate'] = $data['partner_upcountry_rate'];
                $is_upcountry = $this->My_CI->upcountry_model->is_upcountry_booking($booking_id);
                if(!empty($is_upcountry)){
                    
                    if($data['message'] !== UPCOUNTRY_LIMIT_EXCEED){
                        
                        log_message('info', __METHOD__ . " => Upcountry Booking Free Booking " . $booking_id);
                        $booking['upcountry_paid_by_customer'] = 0;
                        $this->My_CI->booking_model->update_booking($booking_id, $booking);
                        $return_status = TRUE;
                    } else if($partner_approval == 1 && $data['message'] == UPCOUNTRY_LIMIT_EXCEED){
                       
                        log_message('info', __METHOD__ . " => Upcountry Waiting for Approval " . $booking_id);
                        $booking['assigned_vendor_id'] = NULL;
                        $booking['internal_status'] = UPCOUNTRY_BOOKING_NEED_TO_APPROVAL;
                        $booking['upcountry_partner_approved'] = '0';
                        $booking['upcountry_paid_by_customer'] = 0;

                        $this->My_CI->booking_model->update_booking($booking_id, $booking);
                        $this->My_CI->service_centers_model->delete_booking_id($booking_id);

                        $this->My_CI->notify->insert_state_change($booking_id, "Waiting Partner Approval", 
                                _247AROUND_PENDING, "Waiting Upcountry to Approval", 
                                $agent_id, $agent_name, _247AROUND);
                        $unit_details = $this->My_CI->booking_model->get_unit_details(array('booking_id'=> $booking_id));

                        $up_mail_data['name'] = $query1[0]['name'];
                        $up_mail_data['appliance'] = $query1[0]['services'];
                        $up_mail_data['booking_address'] = $query1[0]['booking_address'];
                        $up_mail_data['city'] = $query1[0]['city'];
                        $up_mail_data['state'] = $query1[0]['state'];
                        $up_mail_data['booking_pincode'] = $query1[0]['booking_pincode'];
                        $up_mail_data['booking_id'] = $query1[0]['booking_id'];
                        $up_mail_data['booking_primary_contact_no'] = $query1[0]['booking_primary_contact_no'];
                        $up_mail_data['price_tags'] = $unit_details[0]['price_tags'];
                        $up_mail_data['appliance_brand'] = $unit_details[0]['appliance_brand'];
                        $up_mail_data['appliance_category'] = $unit_details[0]['appliance_category'];
                        $up_mail_data['appliance_capacity'] = $unit_details[0]['appliance_capacity'];
                        $up_mail_data['upcountry_distance'] = $booking[0]['upcountry_distance'];

                        $message1 = $this->My_CI->load->view('employee/upcountry_approval_template', $up_mail_data, true);
                        $cc = NITS_ANUJ_EMAIL_ID;
                        $subject = "Upcountry charges approval required - Booking ID " . $query1[0]['booking_id'];
                        $to = $partner_details[0]['upcountry_approval_email'];

                        $this->My_CI->notify->sendEmail("booking@247around.com", $to, $cc, "", $subject, $message1, "");

                        $return_status = FALSE;
                        
                    } else if ($partner_approval == 0 && $data['message'] == UPCOUNTRY_LIMIT_EXCEED) {

                        log_message('info', __METHOD__ . " => Upcountry, partner not provide approval" . $booking_id);
                        $this->My_CI->booking_model->update_booking($booking_id, $booking);
                        $this->process_cancel_form($booking_id, "Pending", UPCOUNTRY_CHARGES_NOT_APPROVED, "", $agent_id, $agent_name,$query1[0]['partner_id']);

                        $return_status = FALSE;
                    }
                    
                } else {
              
                    log_message('info', __METHOD__ . " => Partner does not provide Upcountry charges " . $booking_id);
                    $booking['upcountry_paid_by_customer'] = 1;
                    if($query1[0]['is_upcountry'] == 0){
                        log_message('info', __METHOD__ . " => Amount due added " . $booking_id);
                        $booking['amount_due'] = $query1[0]['amount_due'] + ($booking['partner_upcountry_rate'] * $booking['upcountry_distance']);
                    } else {
                        log_message('info', __METHOD__ . " => Amount due nt added" . $booking_id);
                        $booking['amount_due'] = ($booking['partner_upcountry_rate'] * $booking['upcountry_distance']);
                    }
                    
                    $this->My_CI->booking_model->update_booking($booking_id, $booking);
                     $return_status = TRUE;
                }

                break;

            case NOT_UPCOUNTRY_BOOKING:
                
                log_message('info', __METHOD__ . " => Not Upcountry Booking" . $booking_id);
                $return_status = TRUE;
                break;
            case UPCOUNTRY_DISTANCE_CAN_NOT_CALCULATE:
                
                log_message('info', __METHOD__ . " => Upcountry distance cannot calculate" . $booking_id);
                $to = NITS_ANUJ_EMAIL_ID.", sales@247around.com";
                $message1 = "Upcountry did not calculate for " . $booking_id;
                $this->My_CI->notify->sendEmail("booking@247around.com", $to, "", "", 'Upcountry Failed', $message1, "");
                $return_status = FALSE;
                break;
        }

        if ($return_status) {
            log_message('info', __METHOD__ . " => Upcountry return True" . $booking_id);
            return $query1;
        } else {
            log_message('info', __METHOD__ . " => Upcountry return False" . $booking_id);
            return FALSE;
        }
    }
    
    function process_cancel_form($booking_id, $status,$cancellation_reason, $cancellation_text,
        $agent_id, $agent_name, $partner_id) {
        log_message('info', __METHOD__ . " => Entering " . $booking_id);
        $data['internal_status'] = $data['cancellation_reason'] = $cancellation_reason;
        $data['closed_date'] = $data['update_date'] = date("Y-m-d H:i:s");

        $data['current_status'] = _247AROUND_CANCELLED;
        if (!empty($cancellation_text)) {
            $data['closing_remarks'] = $cancellation_text;
        }
        $data_vendor['cancellation_reason'] = $data['cancellation_reason'];

        $partner_status = $this->My_CI->booking_utilities->get_partner_status_mapping_data($data['current_status'], $data['internal_status'], $partner_id, $booking_id);
        if (!empty($partner_status)) {
            $data['partner_current_status'] = $partner_status[0];
            $data['partner_internal_status'] = $partner_status[1];
        }


        log_message('info', __FUNCTION__ . " Update booking  " . print_r($data, true));

        $this->My_CI->booking_model->update_booking($booking_id, $data);

        //Update this booking in vendor action table
        $data_vendor['update_date'] = date("Y-m-d H:i:s");
        $data_vendor['current_status'] = $data_vendor['internal_status'] = _247AROUND_CANCELLED;
        log_message('info', __FUNCTION__ . " Update Service center action table  " . print_r($data_vendor, true));
        $this->My_CI->vendor_model->update_service_center_action($booking_id, $data_vendor);

        $this->update_price_while_cancel_booking($booking_id);

        //Update Spare parts details table
        $this->My_CI->service_centers_model->update_spare_parts(array('booking_id' => $booking_id), array('status' => _247AROUND_CANCELLED));

        //Log this state change as well for this booking
        //param:-- booking id, new state, old state, employee id, employee name
        $this->My_CI->notify->insert_state_change($booking_id, $data['current_status'], $status, $data['cancellation_reason'], $agent_id, $agent_name, _247AROUND);
        // Not send Cancallation sms to customer for Query booking
        // this is used to send email or sms while booking cancelled
        $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
        $send['booking_id'] = $booking_id;
        $send['state'] = $data['current_status'];
        $this->My_CI->asynchronous_lib->do_background_process($url, $send);

        // call partner callback
        $this->My_CI->partner_cb->partner_callback($booking_id);
        log_message('info', __METHOD__ . " => Exit " . $booking_id);
    }
    
    function update_price_while_cancel_booking($booking_id) {
	log_message('info', __FUNCTION__ . " Booking Id  " . print_r($booking_id, true));
	$unit_details['booking_status'] = "Cancelled";
	$unit_details['vendor_to_around'] = $unit_details['around_to_vendor'] = 0;
        $unit_details['ud_closed_date'] = date("Y-m-d H:i:s");

	log_message('info', __FUNCTION__ . " Update unit details  " . print_r($unit_details, true));
	$this->My_CI->booking_model->update_booking_unit_details($booking_id, $unit_details);
    }
    /**
     * @desc: This is used to insert unit in sc table when booking updated and unit not not exist
     * If units do not exist in sc table then it insert into sc table. 
     * And if Sc table has extra unit means unit deleted from uit details table then it will be delete from sc table.
     * And create a job card
     * @param String $booking_id
     */
    function check_unit_in_sc($booking_id){
       log_message('info', __FUNCTION__ . " Booking Id  " . print_r($booking_id, true));
       $data = $this->My_CI->booking_model->getbooking_history($booking_id);
       if(!empty($data)){
           if(!is_null($data[0]['assigned_vendor_id'])){
               log_message('info', __FUNCTION__ . " Booking Assigned");
               $unit_details = $this->My_CI->booking_model->get_unit_details(array('booking_id'=> $booking_id));
               if(!empty($unit_details)){
                    log_message('info', __FUNCTION__ . " Booking Unit details exist");
                    foreach ($unit_details as $value) {
                        $sc_data = $this->My_CI->service_centers_model->get_service_center_action_details("unit_details_id", 
                                array('unit_details_id'=>$value['id'], 'booking_id'=>$booking_id));
                        if(empty($sc_data)){
                            $sc_data['current_status'] = "Pending";
                            $sc_data['update_date'] = date('Y-m-d H:i:s');
                            $sc_data['internal_status'] = "Pending";
                            $sc_data['service_center_id'] = $data[0]['assigned_vendor_id'];
                            $sc_data['booking_id'] = $booking_id;
                            $sc_data['unit_details_id'] = $value['id'];
                            $sc_id = $this->My_CI->vendor_model->insert_service_center_action($sc_data);
                            if (!$sc_id) {
                                log_message('info', __METHOD__ . "=> Data is not inserted into service center "
                                        . "action table booking_id: " . $booking_id . ", data: " . print_r($sc_data, true));
                            }
                        } else{
                            log_message('info', __FUNCTION__ . " Booking Id  " . print_r($booking_id, true). " Unit exist in sc table ". $value['id']);
                        } 
                   }
                    $sc_data1 = $this->My_CI->service_centers_model->get_service_center_action_details("unit_details_id", 
                                array('booking_id'=>$booking_id));
                   if(!empty($sc_data1)){
                       foreach ($sc_data1 as $value1) {
                           $unit_details = $this->My_CI->booking_model->get_unit_details(array('id'=> $value1['unit_details_id'], 'booking_id'=>$booking_id));
                           if(empty($unit_details)){
                               log_message('info', __FUNCTION__ . " Booking Unit details not exist  unit_id" . $value['unit_details_id']);
                               $this->My_CI->service_centers_model->delete_sc_unit_details(array('unit_details_id' => $value1['unit_details_id'], 'booking_id' => $booking_id));
                           }
                       }
                   }
               } else {
                   log_message('info', __FUNCTION__ . " Booking Unit details not exist  Booking Id  " . print_r($booking_id, true));
               }
           } else {
               log_message('info', __FUNCTION__ . " Booking Not Assign-  Booking Id  " . print_r($booking_id, true));
           }
           //Prepare job card
            $this->My_CI->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);
            log_message('info', "Async Process to create Job card: " . $booking_id);
       } else {
           log_message('info', __FUNCTION__ . " Booking Id Not Exist  " . print_r($booking_id, true));
       }
    }
    
}