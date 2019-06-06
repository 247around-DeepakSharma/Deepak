<?php

class cancellation {
    public function __construct() {
        $this->C_CI = & get_instance();
        $this->C_CI->load->model('vendor_model');
        $this->C_CI->load->model('reusable_model');
        $this->C_CI->load->library("miscelleneous");
        $this->C_CI->load->library("asynchronous_lib");
        $this->C_CI->load->library("booking_utilities");
        $this->C_CI->load->library("notify");
    }
    /*
     * This function is used to handle cancellation flow from SF Panel
     * On the basis of cancellation reason flag this function will perform different actions
     */
    function process_cancellation_from_service_center_panel($booking_id,$inputArray){
        log_message('info', __FUNCTION__ . " Booking ID: " . $booking_id);
        if(array_key_exists('cancellation_reason', $inputArray)){
            $where['reason'] =  $inputArray['cancellation_reason'];
            $cancellationFLag = $this->C_CI->reusable_model->get_search_result_data("booking_cancellation_reasons","decision_flag",$where,NULL,NULL,NULL,NULL,NULL,array());
            $flagValue = DO_NOT_SEND_SMS_DO_NOT_CANCEL;
            if(!empty($cancellationFLag)){
                $flagValue = $cancellationFLag[0]['decision_flag'];
            }
            switch ($flagValue){
                case DIRECT_CANCEL:
                    $this->directly_cancelled_the_booking($booking_id,$inputArray);
                break;
                case DO_NOT_SEND_SMS_DO_NOT_CANCEL:
                    $this->send_booking_for_review($booking_id,$inputArray);
                break;
                case AUTO_APPROVE_ON_FAKE_CANCELLATION :
                case SHOW_HIGHLIGHTED_ON_FAKE_CANCELLATION :
                    $this->send_fake_cancellation_sms($booking_id,$inputArray);
                break;
                case APPROVE_DIRECTLY_AFTER_MORE_THEN_ONE_ATTAMPS:
                    return $this->approve_directly_after_more_then_one_attamps($booking_id,$inputArray);
                break;
               case NOT_IN_MY_AREA:
                     $this->notify_rm_for_wrong_area($booking_id,$inputArray);
                break;
                case PRODUCT_NOT_DELIVERED_TO_CUSTOMER_FLAG:
                    $this->C_CI->miscelleneous->convert_booking_to_query($booking_id,$inputArray['partner_id']);
                break;
                case CUSTOMER_GAVE_WRONG_PINCODE:
                    $this->reassign_booking_to_right_sf($booking_id,$inputArray);
                break;
            }
        }
        else{
            log_message('info', __FUNCTION__ . " Booking ID: " . $booking_id." Cancellation Reason Not recieved");
        }
    }
   function directly_cancelled_the_booking($booking_id,$inputArray){
        $cancellation_reason = trim($inputArray['cancellation_reason']);
        $cancellation_text = $inputArray['cancellation_reason_text'];
        $agent_id = $this->C_CI->session->userdata('service_center_agent_id');
        $agent_name = $this->C_CI->session->userdata('service_center_name');
        $service_center_id =$this->C_CI->session->userdata('service_center_id'); 
        $this->C_CI->miscelleneous->process_cancel_form($booking_id, _247AROUND_CANCELLED, $cancellation_reason, $cancellation_text, $agent_id, $agent_name, $service_center_id, _247AROUND);
    }
   function send_booking_for_review($booking_id,$inputArray){
        $cancellation_reason = trim($inputArray['cancellation_reason']);
        $cancellation_text = $inputArray['cancellation_reason_text'];
        $can_state_change = $inputArray['cancellation_reason'];
        $partner_id = $inputArray['partner_id'];
        if(!empty($cancellation_text)){
            $can_state_change = $cancellation_reason." - ".$cancellation_text;
        }
        $data['current_status'] = "InProcess";
        $data['internal_status'] = "Cancelled";
        $data['service_center_remarks'] = date("F j") . ":- " .$cancellation_text;
        $data['service_charge'] = $data['additional_service_charge'] = $data['parts_cost'] = $data['amount_paid'] = 0;
        $data['cancellation_reason'] = $cancellation_reason;
        $data['closed_date'] = $data['update_date'] = date('Y-m-d H:i:s');
        $this->C_CI->vendor_model->update_service_center_action($booking_id, $data);
        //Update Service Center Closed Date in booking Details Table, 
        //if current date time is before 12PM then take completion date before a day, 
        //if day is monday and  time is before 12PM then take completion date as saturday
        //Check if new completion date is equal to or greater then booking_date
        date_default_timezone_set('Asia/Kolkata');
        // get booking_date
        $booking_date = $this->C_CI->reusable_model->get_search_result_data("booking_details",'STR_TO_DATE(booking_details.booking_date,"%d-%m-%Y") as booking_date',array('booking_id'=>$booking_id),
                NULL,NULL,NULL,NULL,NULL,array())[0]['booking_date'];
        $bookingData['service_center_closed_date'] = date('Y-m-d H:i:s');
        // If time is before 12 PM then completion date will be yesturday's date
        //  if (date('H') < 12) {
            $bookingData['service_center_closed_date'] =  date('Y-m-d H:i:s',(strtotime ( '-1 day' , strtotime (date('Y-m-d H:i:s')) ) ));
            $dayofweek = date('w', strtotime(date('Y-m-d H:i:s')));
            // If day is monday then completion date will be saturday's date
            if($dayofweek == '1'){
              $bookingData['service_center_closed_date'] =  date('Y-m-d H:i:s',(strtotime ( '-2 day' , strtotime (date('Y-m-d H:i:s')) ) ));  
          //  }
        }
        $booking_timeStamp = strtotime($booking_date);
        $close_timeStamp = strtotime($bookingData['service_center_closed_date']);
        $datediff = $close_timeStamp - $booking_timeStamp;
        $booking_date_days = round($datediff / (60 * 60 * 24))-1;
        if($booking_date_days <= 0){
            $bookingData['service_center_closed_date'] = date('Y-m-d H:i:s');
        }
        $this->C_CI->reusable_model->update_table("booking_details",$bookingData,array('booking_id'=>$booking_id));
        //$this->miscelleneous->process_booking_tat_on_completion($booking_id);
        //End Update Service Center Closed Date
        $this->update_booking_internal_status($booking_id, "InProcess_Cancelled",  $partner_id);
        $this->insert_details_in_state_change($booking_id, 'InProcess_Cancelled', $can_state_change,"not_define","not_define");
        return true;
    }
    function send_fake_cancellation_sms($booking_id,$inputArray){
        $join["services"] = "services.id = booking_details.service_id";
        $data = $this->C_CI->reusable_model->get_search_result_data("booking_details","booking_details.user_id,booking_details.booking_primary_contact_no as phone_number,services.services",array("booking_details.booking_id"=>$booking_id),$join,NULL,NULL,NULL,NULL,array());
        $data[0]['phone_number'] = '8826186751';
        if(!empty($data[0])){
            $sms['tag'] = FAKE_CANCELLATION_SMS_TAG;
            $sms['phone_no'] = $data[0]['phone_number'];
            $sms['smsData']['service'] = $data[0]['services'];
            $sms['smsData']['booking_id'] = $booking_id;
            $sms['booking_id'] = $booking_id;
            $sms['type'] = "user";
            $sms['type_id'] = $data[0]['user_id'];
            $this->C_CI->notify->send_sms_msg91($sms);
        }
        $this->send_booking_for_review($booking_id,$inputArray);
    }
    function approve_directly_after_more_then_one_attamps($booking_id,$inputArray){
         $where['booking_id'] = $booking_id;
         $where['cancellation_count > 0'] = NULL;
         $alreadyCancelledBooking = $this->C_CI->reusable_model->get_search_result_data("service_center_booking_action",'DISTINCT booking_id',$where,NULL,NULL,NULL,NULL,NULL,array());
         if(empty($alreadyCancelledBooking)){
             $data['cancellation_count'] = "cancellation_count+1";
             unset($where['cancellation_count > 0']);
             $this->C_CI->reusable_model->update_table("service_center_booking_action",$data,$where);
             return "Updated";
         }
         else{
             $this->directly_cancelled_the_booking($booking_id,$inputArray);
             return "Cancelled";
         }     
    }
    function notify_rm_for_wrong_area($booking_id,$inputArray){
        if(array_key_exists('partner_id',$inputArray) && array_key_exists('city',$inputArray) && array_key_exists('booking_pincode',$inputArray)){
            $partner_id = $this->C_CI->input->post('partner_id');
            $city = $this->C_CI->input->post('city');
            $booking_pincode = $this->C_CI->input->post('booking_pincode');
            $this->send_mail_rm_for_wrong_area_picked($booking_id, $partner_id,$city,$booking_pincode,WRONG_PINCODE_TEMPLATE);
            $this->send_booking_for_review($booking_id,$inputArray);
        }
    }
    function reassign_booking_to_right_sf($booking_id,$inputArray){
        if(array_key_exists('correct_pincode', $inputArray) && array_key_exists('city', $inputArray)){
           if(!empty($inputArray['correct_pincode'])){
                $correctpin = $inputArray['correct_pincode'];
                $city = $inputArray['city'];
                $booking_pincode = $inputArray['booking_pincode'];
                $cancellation_reason = trim($inputArray['cancellation_reason']);
                if($cancellation_reason==_247AROUND_WRONG_PINCODE_CANCEL_REASON){
                $pinupdate = array('booking_pincode'=>$correctpin);
                $this->C_CI->booking_model->update_booking($booking_id,$pinupdate);
                $partner_id = $inputArray['partner_id'];            
                $partner_data = $this->C_CI->initialized_variable->get_partner_data();
                $booking['service_id']=$this->C_CI->input->post('service_id');
                $response = $this->C_CI->miscelleneous->check_upcountry_vendor_availability($city,$correctpin, $booking['service_id'], $partner_data, false);
                if (!empty($response)  && !isset($response['vendor_not_found'])) {
                    $url = base_url() . "employee/vendor/process_reassign_vendor_form/0";
                    $async_data['service'] = $response['vendor_id'];
                    $async_data['booking_id'] =$booking_id;
                    $async_data['remarks'] ="Booking Reassigned While Cancellation by Sf";
                    $this->C_CI->asynchronous_lib->do_background_process($url, $async_data);
                }
                $this->send_mail_rm_for_wrong_area_picked($booking_id, $partner_id,$city,$booking_pincode,WRONG_PINCODE_TEMPLATE,$correctpin);
                }
            }
        }
        else{
             $this->send_booking_for_review($booking_id,$inputArray);
        }
    }
    
    // Helper Functions
       function update_booking_internal_status($booking_id, $internal_status, $partner_id){
        $booking['internal_status'] = $internal_status;
        $partner_status = $this->C_CI->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, $booking['internal_status'], $partner_id, $booking_id);
        if (!empty($partner_status)) {
            $booking['partner_current_status'] = $partner_status[0];
            $booking['partner_internal_status'] = $partner_status[1];
            $booking['actor'] = $partner_status[2];
            $booking['next_action'] = $partner_status[3];
        } 
        $this->C_CI->booking_model->update_booking($booking_id, $booking);
        log_message('info', __METHOD__. " Partner ID ". $partner_id. " Status ". $internal_status);
        $response = $this->C_CI->miscelleneous->partner_completed_call_status_mapping($partner_id, $internal_status);
        if(!empty($response)){
            
            $this->C_CI->booking_model->partner_completed_call_status_mapping($booking_id, array('partner_call_status_on_completed' => $response));
        } else {
            log_message('info', __METHOD__. " Staus Not found for partner ID ". $partner_id. " status ". $internal_status);
        }
        
        if($internal_status == "InProcess_Cancelled" || $internal_status == "InProcess_Completed"){
            log_message("info", __METHOD__. " DO Not Call patner callback");
        } else {
            $cb_url = base_url() . "employee/do_background_process/send_request_for_partner_cb/".$booking_id;
            $pcb = array();
            $this->C_CI->asynchronous_lib->do_background_process($cb_url, $pcb);
        }
    }
    function insert_details_in_state_change($booking_id, $new_state, $remarks,$actor,$next_action){
        log_message('info', __FUNCTION__ ." SF ID: ".  $this->C_CI->session->userdata('service_center_id'). " Booking ID: ". $booking_id. ' new_state: '.$new_state.' remarks: '.$remarks);
        $agent_id = $this->C_CI->session->userdata('service_center_agent_id');
        $agent_name = $this->C_CI->session->userdata('service_center_name');
        $service_center_id =$this->C_CI->session->userdata('service_center_id');    
        $this->C_CI->notify->insert_state_change($booking_id, $new_state, "", $remarks, $agent_id, $agent_name,$actor,$next_action, NULL, $service_center_id);
    }
        /**
     * @desc This function is used to send email to RM for Booking Not available in your area
     * @param String $booking_id
     * @param int $partner_id
     */
    function send_mail_rm_for_wrong_area_picked($booking_id, $partner_id,$city="",$pincode="",$templet="",$correctpin="") {
         $email_template = $this->C_CI->booking_model->get_booking_email_template($templet);
        if (!empty($email_template)) {
            $rm_email = $this->get_rm_email($this->session->userdata('service_center_id'));
            $join['service_centres'] = 'booking_details.assigned_vendor_id = service_centres.id';
            $JoinTypeTableArray['service_centres'] = 'left';
            $booking_state = $this->C_CI->reusable_model->get_search_query('booking_details','service_centres.state',array('booking_details.booking_id' => $booking_id),$join,NULL,NULL,NULL,$JoinTypeTableArray)->result_array();

            //$get_partner_details = $this->partner_model->getpartner_details('account_manager_id,', array('partners.id' => $partner_id));
            $get_partner_details = $this->C_CI->partner_model->getpartner_data("group_concat(distinct agent_filters.agent_id) as account_manager_id", 
                        array('partners.id' => $partner_id, 'agent_filters.entity_type' => "247around", 'agent_filters.state' => $booking_state[0]['state']),"",0,1,1,"partners.id");
            $am_email = "";
            if (!empty($get_partner_details[0]['account_manager_id'])) {
                $am_email = $this->C_CI->employee_model->getemployeeMailFromID($get_partner_details[0]['account_manager_id'])[0]['official_email'];
            }
            $to = $rm_email.",".$am_email;
            $cc = $email_template[3];
            $bcc = $email_template[5];
            $subject = vsprintf($email_template[4], array($booking_id));
            $emailBody = vsprintf($email_template[0], array($booking_id,$city,$pincode,$correctpin));
            $this->C_CI->notify->sendEmail($email_template[2], $to, $cc, $bcc, $subject, $emailBody, "",$templet, "", $booking_id);
        }
    }
    function fake_cancellation_missed_call_handling($userPhone,$id,$employeeID,$remarks){
        log_message('info', __METHOD__."Phone number ".$userPhone);
        $whereArray["service_center_booking_action.internal_status"] = "Cancelled";
        $whereArray["service_center_booking_action.current_status"] = "InProcess";
        $whereArray["users.phone_number"] = $userPhone;
        $whereArray["DATEDIFF(CURRENT_DATE,sms_sent_details.created_on) < '".OPEN_CANCELLATION_BOOKING_ON_FAKE_CANCELLATION_MISSED_CALL_DAYS."'"] = NULL;
        //Check if booking on review page
        $bookingDetails = $this->get_fake_cancellation_booking_details($userPhone,$whereArray);
        if(!empty($bookingDetails)){
            log_message('info', __METHOD__."Booking is on review Page  ".print_r($bookingDetails,TRUE));
            $numberOfBookings = count($bookingDetails);
            if($numberOfBookings == 1){
                switch ($bookingDetails[0]['decision_flag']){
                case AUTO_APPROVE_ON_FAKE_CANCELLATION:
                    $this->reject_booking_from_review($bookingDetails,$id,$employeeID);
                break;
                }
                //Reject Booking From Review Page
                
            }
            else{
                log_message('info', __METHOD__."More then 1 booking found on Review Page  ".print_r($bookingDetails,TRUE));
            }
        }
        //Check If booking already Cancelled
        else{
            $whereArray["service_center_booking_action.current_status"] = "Cancelled";
            $bookingDetails = $this->get_fake_cancellation_booking_details($userPhone,NULL,$whereArray);
            $numberOfBookings = count($bookingDetails);
            // Reopen Cancelled Booking
            if(!empty($bookingDetails) && $numberOfBookings == 1){
                //Logic to get booking Date
                if(date('H') < '13'){$updatedBookingDate = date("Y-m-d"); $updatedTimeSlot = '4PM-7PM';}else{ $updatedBookingDate =  date("Y-m-d", strtotime("+1 day")); $updatedTimeSlot = '10AM-1PM';} 
                $dataArray['assigned_vendor_id'] = $bookingDetails[0]['assigned_vendor_id'];
                $dataArray['booking_date'] = $updatedBookingDate;
                $dataArray['booking_timeslot'] = $updatedTimeSlot;
                $dataArray['admin_remarks'] = "Booking Reopen by fake cancellation Missed Call";
                $dataArray['partner_id'] = $bookingDetails[0]['partner_id'];
                $this->C_CI->miscelleneous->reopen_booking($bookingDetails[0]['booking_id'], _247AROUND_CANCELLED,$dataArray);
            }
            else{
                 log_message('info', __METHOD__.'Booking Not Found or More than 1 booking found '.print_r($bookingDetails,TRUE));
            }
        }
    }
        /*
     * This is a helper function for fake_cancellation_missed_call_handling , This function is used to get fake Cancelled booking data
     */
    function get_fake_cancellation_booking_details($userPhone,$whereArray){
        log_message('info', __METHOD__.'Function Start With Contact '.$userPhone);
        $join['users'] = "users.user_id=booking_details.user_id";
        $join['service_center_booking_action'] = "service_center_booking_action.booking_id=booking_details.booking_id";
        $join['sms_sent_details'] = "sms_sent_details.booking_id=booking_details.booking_id AND sms_sent_details.sms_tag = '".FAKE_CANCELLATION_SMS_TAG."'";
        $join['booking_cancellation_reasons'] = "service_center_booking_action.cancellation_reason=booking_cancellation_reasons.reason";
        $groupBy  = array("booking_details.booking_id");
        //get Booking id
        $bookingDetails = $this->C_CI->reusable_model->get_search_result_data("booking_details","booking_details.booking_id,booking_details.booking_date,booking_details.assigned_vendor_id,"
                . "booking_details.booking_timeslot,booking_details.assigned_vendor_id,booking_cancellation_reasons.decision_flag",
                $whereArray,$join,NULL,NULL,NULL,NULL,$groupBy);
         log_message('info', __METHOD__.'Function End');
         return $bookingDetails;
    }
      function reject_booking_from_review($bookingDetails,$id,$employee_id) {
        $postArray['booking_id'] = $bookingDetails[0]['booking_id'];
        $postArray['admin_remarks'] = "Booking Rejeceted by Fake Cancellation Missed Call";
        $postArray['rejected_by'] = _247AROUND;
        $postArray['internal_booking_status'] = _247AROUND_CANCELLED;
        $where['is_in_process'] = 0;
        $whereIN['booking_id'] = $postArray['booking_id'];
        $whereIN['current_status'] = array(_247AROUND_PENDING, _247AROUND_RESCHEDULED);
        $tempArray = $this->C_CI->reusable_model->get_search_result_data("booking_details","booking_id, current_status",$where,NULL,NULL,NULL,$whereIN,NULL,array());
        if(!empty($tempArray)){
            $reject_remarks = "Booking cancellation rejected by fake Cancellation Missed Call";
            $actor = ACTOR_REJECT_FROM_REVIEW;
            $next_action = REJECT_FROM_REVIEW_NEXT_ACTION;
            $this->C_CI->notify->insert_state_change($postArray['booking_id'], "Fake_Cancellation", "InProcess_Cancelled", $reject_remarks,$id, $employee_id, $actor,$next_action,_247AROUND);
            $this->C_CI->miscelleneous->reject_booking_from_review($postArray);
        }
    }
}
