<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Notify library to send Mails and SMSs
 *
 * @author anujaggarwal
 */
class Notify {

    var $My_CI;

    function __Construct() {
	$this->My_CI = & get_instance();

	$this->My_CI->load->helper(array('form', 'url'));
	$this->My_CI->load->library('email');
        $this->My_CI->load->library('miscelleneous');
        $this->My_CI->load->model('partner_model');
	$this->My_CI->load->model('vendor_model');
	$this->My_CI->load->model('booking_model');
    }

    /**
     *  @desc : This method is used to send mail
     *  @param : From, To, CC, BCC, Subject, Message, Attachment
     *  @return : if mail send return true else false
     */
    function sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment,$template_tag, $attachment2 = "", $booking_id = "") {
	switch (ENVIRONMENT) {
	    case 'production':
		//Clear previous email
                if(!empty($to)){
                    $this->My_CI->email->clear(TRUE);

                    //Attach file with mail
                    if (!empty($attachment)) {
                        $this->My_CI->email->attach($attachment, 'attachment');
                    }
                    
                    if(!empty($attachment2)){
                        $this->My_CI->email->attach($attachment2, 'attachment');
                    }

                    $this->My_CI->email->from($from, '247around Team');

                    $this->My_CI->email->to($to);
                    $this->My_CI->email->bcc($bcc);
                    $this->My_CI->email->cc($cc);

                    $this->My_CI->email->subject($subject);
                    $this->My_CI->email->message($message);

                    if ($this->My_CI->email->send()) {
                        $this->add_email_send_details($from, $to, $cc, $bcc, $subject, $message, $attachment,$template_tag, $booking_id);
                        return true;
                    } else {
                        return false;
                    }
                }

		break;
	}
    }

    /*
     * Desc: Used to send SMS
     * param: phone, sms body
     * return: Array
     */

    function sendTransactionalSms($phone_number, $body) {
	$data = array();
	switch (ENVIRONMENT) {
	    case 'production':
		$post_data = array(
		    // 'From' doesn't matter; For transactional, this will be replaced with your SenderId;
		    // For promotional, this will be ignored by the SMS gateway
		    'From' => '01143092150',
		    'To' => $phone_number,
		    'Body' => $body,
		);

		$exotel_sid = "aroundhomz";
		$exotel_token = "a041058fa6b179ecdb9846ccf0e4fd8e09104612";

		$url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FAILONERROR, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));

		$data['content'] = curl_exec($ch);
		$data['error'] = curl_error($ch);
		$data['info'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);
		break;
	}
	return $data;
    }

    /* This function is used to save sent sms to the database
     * Desc: Saves the succesffuly sent SMS to the Database else Log's the error
     *
     * params: user_id, user_type, phone no., sms content, booking ID
     * return: Null
     */

    function add_sms_sent_details($type_id, $type, $phone, $content, $booking_id, $sms_tag, $result = NULL) {
	$data = array();

	$data['type_id'] = $type_id;
	$data['type'] = $type;
	$data['phone'] = $phone;
	$data['booking_id'] = $booking_id;
	$data['content'] = $content;
        $data['sms_tag'] = $sms_tag;
        $data['result'] = $result;

	//Add SMS to Database
	$sms_id = $this->My_CI->booking_model->add_sms_sent_details($data);
	if (!empty($sms_id)) {
	    //Echoing success message on Log
	    log_message('info', __FUNCTION__ . ' SMS has been saved to Database "sms_sent_details" with ID ' . $sms_id);
	} else {
	    //Echoing Error message in Log
	    log_message('info', __FUNCTION__ . ' Error on saving SMS to Database "sms_sent_details" ' . print_r($data, TRUE));
	}
    }

    /**
     *  @desc : This method is to use SMS templates
     *  @param : SMS tag,parameters and phone no.
     *  @return : if SMS send return true else false
     */
    function send_sms($sms) {
	$template = $this->My_CI->vendor_model->getVendorSmsTemplate($sms['tag']);
        
	if (!empty($template)) {
	    $smsBody = vsprintf($template, $sms['smsData']);
	    $response = $this->sendTransactionalSms($sms['phone_no'], $smsBody);
            
	    if (isset($response['info']) && $response['info'] == '200') {
		$this->add_sms_sent_details($sms['type_id'], $sms['type'], $sms['phone_no'], 
                        $smsBody, $sms['booking_id'], $sms['tag']);
	    }
	} else {
	    log_message('info', "Message Not Sent - Booking id: " . $sms['booking_id'] . ",
        		please recheck tag: '" . $sms['tag'] . "' & Phone Number - " . $sms['phone_no']);
            
	    $subject = 'Booking SMS not sent';
	    $message = "Please check SMS tag and phone number. Booking id is : " .
		$sms['booking_id'] . " Tag is '" . $sms['tag'] . "' & phone number is :" . $sms['phone_no'];
	    $to = ANUJ_EMAIL_ID;
            
	    $this->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", $subject, $message, "","booking_sms_not_sent");
            
	}
    }

    /**
     *  @desc : This method is to use email templates
     *  @param : email tag and other booking details.
     *  @return : if Email send return true else false
     */
    function send_email($email) {

	$template = $this->My_CI->booking_model->get_booking_email_template($email['tag']);
	log_message('info', " Email Body" . print_r($email, true));


	if (!empty($template)) {
	    $emailBody = vsprintf($template[0], $email);
	    log_message('info', " Template" . print_r($email, true));
	    $from = $template[2];
	    $to = $template[1];
	    $cc = $template[3];
	    $bcc = $template[5];
	    $subject = $email['subject'];
	    $message = $emailBody;
	    $attachment = "";
	    log_message('info', " Email Message" . print_r($message, true));

	    $this->sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment,$email['tag']);
	} else {
                       $permission = $this->My_CI->partner_model->get_partner_permission(array('permission_type' => DO_NOT_SEND_BOOKING_EMAIL_NOT_SENT_EMAIL, 'is_on' => 1));
                       if(empty($permission)){
                            log_message('info', "Email Not Sent - Booking id: " . $email['booking_id'] . ",
                                        please recheck tag: '" . $email['tag'] . "' & Phone Number - " . $email['phone_no']);
                            $subject = 'Booking Email not sent';
                            $message = "Please check email tag and phone number. Booking id is : " .
                                $email['booking_id'] . " Tag is '" . $email['tag'] . "' & phone number is :" . $email['phone_no'];
                            $to = NITS_ANUJ_EMAIL_ID;
                            $this->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", $subject, $message, "",'booking_email_not_sent');
                       }
	}
    }

    /**
     *  @desc : This method is used to make an outbound call to a customer.
     * This API will connect the two numbers given for the agent and customer. It will connect ‘From’ number
     * first. Once the person at ‘From’ end picks up the phone, it will connect to the number provided in the ‘To’.
     * You can choose which number to be connected first by giving that number in the ‘From’ field.
     *
     * http://support.exotel.in/support/solutions/articles/48259-outbound-call-to-connect-an-agent-to-a-customer-
     *
     *  @param :
     *
     *  @return :
     */
    function make_outbound_call_using_knowlarity($agent_phone, $customer_phone) {
        switch (ENVIRONMENT) {
            case 'production':
            //case 'testing_chhavi':
            $agentNumber = substr($agent_phone, '-10');
            $customerNumber = substr($customer_phone, '-10');
            $postData = array("k_number"=>KNOWLARITY_NUMBER, "agent_number"=>"+91".$agentNumber, "customer_number"=>"+91".$customerNumber, "caller_id"=> "+91".$customerNumber);
            $postDataJSon =  json_encode($postData);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, KNOWLARITY_OUTGOING_CALL_URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$postDataJSon);
            curl_setopt($ch, CURLOPT_POST, 1);
            $headers = array();
            $headers[] = "Content-Type: application/json";
            $headers[] = "Accept: application/json";
            $headers[] = "Authorization: ". KNOWLARITY_API_KEY;
            $headers[] = "X-Api-Key: ".KNOWLARITY_APPLICATION_KEY;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close ($ch);
            break;
        }
    }

    function make_outbound_call($agent_phone, $customer_phone) {
	//Callback fn called by Exotel
	switch (ENVIRONMENT) {
	    case 'production':
		$post_data = array(
		    'From' => $agent_phone,
		    'To' => $customer_phone,
		    'CallerId' => '01141170701', //247around call centre exophone number
		    'CallType' => 'trans'
		);

		$exotel_sid = "aroundhomz";
		$exotel_token = "a041058fa6b179ecdb9846ccf0e4fd8e09104612";
		$url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Calls/connect";

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FAILONERROR, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));

		$http_result = curl_exec($ch);
		$error = curl_error($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		log_message('info', __FUNCTION__ . "=> " . print_r(array($error, $http_code), TRUE));
		curl_close($ch);

		break;
	}
    }

    /**
     * @desc: This is used to insert agent record while updating a booking
     * @param: Booking ID
     * @param: New State - New booking state
     * @param: Old State - Old booking state
     * @param: Remarks - Any remarks which were added during the state change
     * @param: Agent ID - Agent ID (agent could belong to 247around or partner)
     * @param: Agent Name - Agent name
     * @param: Partner ID - Partner to which Agent belongs to, NOT the Partner ID who provided this booking
     * 
     * @return: void 
     */
    function insert_state_change($booking_id, $new_state, $old_state, $remarks, $agent_id, $agent_name, $actor, $next_action, $partner_id = NULL, $service_center_id = NULL) {
        //Log this state change as well for this booking
        $state_change['booking_id'] = $booking_id;
        //$state_change['old_state'] = $old_state;
        $state_change['new_state'] = $new_state;
        $state_change['remarks'] = $remarks;
        $state_change['agent_id'] = $agent_id;
        $state_change['partner_id'] = $partner_id;
        $state_change['service_center_id'] = $service_center_id;
        if(!empty($actor)){
            $state_change['actor'] = $actor;
            $state_change['next_action'] = $next_action;
        }
        
        /*
         * Send correct old_state from the calling function instead, do not change
         * the old state here
         * */
        $state_change['old_state'] = $old_state;
        if (!empty($booking_id)) {
            $booking_state_change = $this->My_CI->booking_model->get_booking_state_change($state_change['booking_id']);
            if ($booking_state_change > 0) {
                $state_change['old_state'] = $booking_state_change[count($booking_state_change) - 1]['new_state'];
            }
        }
        $insert_id = $this->My_CI->booking_model->insert_booking_state_change($state_change);

        if ($insert_id) {
            log_message('info', 'Booking Status Change - Booking id: ' . $booking_id . $new_state . "  By " . $agent_name);
        } else {
            log_message('info', 'Error in Booking Status Change - Booking id: ' . $booking_id . $new_state . "  By " . $agent_name);
        }
    }

    /**
     * @desc: this is used to send sms and email while complete or cancel booking
     * @param: booking id current status
     * @return: true
     */
    function send_sms_email_for_booking($booking_id, $current_status) { 
	log_message("info",__METHOD__);
	$query1 = $this->My_CI->booking_model->getbooking_filter_service_center($booking_id);
	if (!empty($query1)) {

	    //SMS and Emails are sent using Templates which are defined in sms_template and
	    //email_template tables. These templates are given arguments like service name, customer
	    //name etc and then the email and sms is created and sent.
	    //Do not change the arguments order for email and sms unless you have already
	    //changed the template.

	    $sms['phone_no'] = $query1[0]['booking_primary_contact_no'];
	    $sms['booking_id'] = $query1[0]['booking_id'];

	    switch ($current_status) {
		case 'Completed':
		    $email = array();
		    $email['name'] = $query1[0]['name'];
		    $email['phone_no'] = $query1[0]['phone_number'];
		    $email['user_email'] = $query1[0]['user_email'];
		    $email['booking_id'] = $query1[0]['booking_id'];
		    $email['service'] = $query1[0]['services'];
		    $email['booking_date'] = $query1[0]['booking_date'];
		    $email['closed_date'] = $query1[0]['closed_date'];
		    $email['amount_paid'] = $query1[0]['amount_paid'];
		    $email['closing_remarks'] = $query1[0]['closing_remarks'];

		    if (isset($query1[0]['vendor_name'])) {
			$email['vendor_name'] = $query1[0]['vendor_name'];
			$email['city'] = $query1[0]['district'];
		    }

		    $email['tag'] = "complete_booking";
		    $email['subject'] = "Booking Completion-247AROUND";

                                               // $this->My_CI->miscelleneous->send_completed_booking_email_to_customer(array($email['booking_id']));
		    //Send internal mails now
		    $this->send_email($email);
                    
                    $partner_type = $this->My_CI->reusable_model->get_search_query('bookings_sources','partner_type' , array('partner_id'=>$query1[0]['partner_id']),NULL, NULL ,NULL,NULL,NULL)->result_array()[0]['partner_type'];
                    
                    $sms['tag'] = "complete_booking";
		    $call_type = explode(" ", $query1[0]['request_type']);
		    $sms['smsData']['service'] = $query1[0]['services'];
                    $sms['smsData']['call_type'] = $call_type[0];
                    $sms['smsData']['booking_id'] = $query1[0]['booking_id'];
                    if ($query1[0]['partner_id'] == JEEVES_ID) {
                        $sms['smsData']['public_name'] = "";
                    } 
                    else if($partner_type === OEM){ 
                        $brand_name = $this->My_CI->booking_model->get_unit_details(array('booking_id'=>$booking_id), false, 'appliance_brand');
                        if(!empty($brand_name)){
                            $sms['smsData']['public_name'] = $brand_name[0]['appliance_brand'];
                        } else {
                            $sms['smsData']['public_name'] = $query1[0]['public_name'];
                        }
                    }
                    else { 
                        $sms['smsData']['public_name'] = $query1[0]['public_name'];
                    }
                    $sms['smsData']['good_rating_number'] = GOOD_MISSED_CALL_RATING_NUMBER;
                    $sms['smsData']['poor_rating_number'] = POOR_MISSED_CALL_RATING_NUMBER;
		    $sms['booking_id'] = $query1[0]['booking_id'];
		    $sms['type'] = "user";
		    $sms['type_id'] = $query1[0]['user_id'];
		    $this->send_sms_msg91($sms);
                    //send msg to dealer on booking completion
                     if(!empty($query1[0]['dealer_id']))
                      {
                        // Request of %s for %s with booking_id %s is completed.
                         $dealer_id=$query1[0]['dealer_id'];
                         $dealer_phone_no=$this->My_CI->reusable_model->get_search_query('dealer_details','dealer_phone_number_1',array('dealer_id'=>$dealer_id),NULL, NULL ,NULL,NULL,NULL)->result_array()[0]['dealer_phone_number_1'];
                         $dealerSms['phone_no'] = $dealer_phone_no;
                         $dealerSms['tag'] = "sms_to_dealer_on_booking_completed_cancelled";
                         $dealerSms['type'] = "dealer";
                         $dealerSms['type_id'] = $query1[0]['dealer_id'];
                         $dealerSms['booking_id'] = $query1[0]['booking_id'];
                         $dealerSms['smsData']['service'] = $query1[0]['services'];
                         $dealerSms['smsData']['call_type'] = $call_type[0];
                         $dealerSms['smsData']['booking_id'] = $query1[0]['booking_id'];
                         $dealerSms['smsData']['booking_type']='completed';
                         $this->send_sms_msg91($dealerSms);
                      }

		    break;

		case 'Cancelled':
		    $email = array();
		    if (substr($booking_id, 0, 2) === 'Q-') {
			$email_data['name'] = $query1[0]['name'];
			$email_data['phone_no'] = $query1[0]['phone_number'];
			$email_data['booking_id'] = $query1[0]['booking_id'];
			$email_data['cancellation_reason'] = $query1[0]['cancellation_reason'];

			$email_data['tag'] = "cancel_query";
			$email_data['service'] = $query1[0]['services'];
			$email_data['booking_date'] = $query1[0]['booking_date'];
			$email_data['subject'] = "Pending Query Cancellation - 247AROUND";

			//Send internal mails now
			$this->send_email($email_data);
		    } else {

                        if($query1[0]['partner_id'] == VIDEOCON_ID){
                            $this->vediocon_cancelled_booking_sms($query1[0]);  
                        }
                        else{
                            $call_type = explode(" ", $query1[0]['request_type']);
                            $sms['smsData']['service'] = $query1[0]['services'];
                            $sms['smsData']['call_type'] = $call_type[0];
                            $sms['tag'] = "cancel_booking";
                            $sms['booking_id'] = $query1[0]['booking_id'];
                            $sms['type'] = "user";
                            $sms['type_id'] = $query1[0]['user_id'];

                            if($query1[0]['partner_id'] == JEEVES_ID){
                                $sms['smsData']['number'] = JEEVES_CALLCENTER_NUMBER;
                                $sms['smsData']['name'] = JEEVES_WEBSITE;
                            }else{
                                $sms['smsData']['number'] = _247AROUND_CALLCENTER_NUMBER;
                                $sms['smsData']['name'] = _247AROUND_DEFAULT_AGENT_NAME;
                            }

                            $this->send_sms_msg91($sms);
                        }
                        //SEND MSG TO DEALER ON BOOKING REJECTION
                        if(!empty($query1[0]['dealer_id']))
                      {
                         $dealer_id=$query1[0]['dealer_id'];
                         $dealer_phone_no=$this->My_CI->reusable_model->get_search_query('dealer_details','dealer_phone_number_1',array('dealer_id'=>$dealer_id),NULL, NULL ,NULL,NULL,NULL)->result_array()[0]['dealer_phone_number_1'];
                         $dealerSms['phone_no'] = $dealer_phone_no;
                         $dealerSms['tag'] = "sms_to_dealer_on_booking_completed_cancelled";
                         $dealerSms['type'] = "dealer";
                         $dealerSms['type_id'] = $query1[0]['dealer_id'];
                         $dealerSms['booking_id'] = $query1[0]['booking_id'];
                         $dealerSms['smsData']['service'] = $query1[0]['services'];
                         $dealerSms['smsData']['call_type'] = $call_type[0];
                         $dealerSms['smsData']['booking_id'] = $query1[0]['booking_id'];
                         $dealerSms['smsData']['booking_type']='Cancelled';
                         $this->send_sms_msg91($dealerSms);
                      }
			
		    }

		    break;

		case 'Rescheduled':
		    $email = array();
		    $email['name'] = $query1[0]['name'];
		    $email['phone_no'] = $query1[0]['phone_number'];
		    $email['user_email'] = $query1[0]['user_email'];
		    $email['booking_id'] = $query1[0]['booking_id'];
		    $email['service'] = $query1[0]['services'];
		    $email['booking_date'] = $query1[0]['booking_date'];
		    $email['booking_timeslot'] = $query1[0]['booking_timeslot'];
		    $email['update_date'] = $query1[0]['update_date'];
		    $email['booking_address'] = $query1[0]['booking_address'] . ", "
			. $query1[0]['city'] . ", " . $query1[0]['booking_pincode'];
		    $email['tag'] = "reschedule_booking";
		    $email['subject'] = "Booking Rescheduled-247AROUND";
		    //Send internal mails now
		    $this->send_email($email);

		    
		    $sms['tag'] = "reschedule_booking";
		    $sms['smsData']['service'] = $query1[0]['services'];
                    $sms['smsData']['booking_id'] = $query1[0]['booking_id'];
		    $sms['smsData']['booking_date'] = date("d-M-Y", strtotime($query1[0]['booking_date']));
		    //$sms['smsData']['booking_timeslot'] = $query1[0]['booking_timeslot'];
		    $sms['booking_id'] = $query1[0]['booking_id'];
		    $sms['type'] = "user";
		    $sms['type_id'] = $query1[0]['user_id'];
                    log_message("info", "sdgsdg ".print_r($sms,true));
		    $this->send_sms_msg91($sms);
		    

		    break;

		case 'OpenBooking':
		    $email = array();
		    $email['booking_id'] = $query1[0]['booking_id'];
		    $email['name'] = $query1[0]['name'];
		    $email['phone_no'] = $query1[0]['phone_number'];
		    $email['service'] = $query1[0]['services'];
		    $email['booking_date'] = $query1[0]['booking_date'];
		    $email['booking_timeslot'] = $query1[0]['booking_timeslot'];

		    if (isset($query1[0]['vendor_name'])) {
			$email['vendor_name'] = $query1[0]['vendor_name'];
			$email['city'] = $query1[0]['district'];
		    }
		    $email['agent'] = "";

		    if ($query1[0]['current_status'] == "Completed") {
			$email['tag'] = "open_completed_booking";
		    } else {
			$email['tag'] = "open_cancelled_booking";
		    }

		    $email['subject'] = "Closed Booking Reopened - 247AROUND";

		    //Send internal mails now
		    $this->send_email($email);

		    break;

		case 'Customer not reachable':
                    //Max name length = 15 to fit in 1 SMS
		    //$sms['smsData']['name'] = substr($query1[0]['name'], 0, 15);
		    //$sms['smsData']['service'] = $query1[0]['services'];
                    if($query1[0]['partner_id'] == VIDEOCON_ID){
                        $this->vediocon_call_not_picked_sms($query1[0]);
                    }
                    else{
                        $call_type = explode(" ", $query1[0]['request_type']);
                        $sms['smsData']['service'] = $query1[0]['services']." ".$call_type[0];
                        $sms['tag'] = "call_not_picked_other";
                        $sms['booking_id'] = $query1[0]['booking_id'];
                        $sms['type'] = "user";
                        $sms['type_id'] = $query1[0]['user_id'];

                        $this->send_sms_msg91($sms);
                    }
		    break;

		case 'Newbooking':
                    $partner_type = $this->My_CI->reusable_model->get_search_query('bookings_sources','partner_type' , array('partner_id'=>$query1[0]['partner_id']),NULL, NULL ,NULL,NULL,NULL)->result_array()[0]['partner_type'];
                    if($query1[0]['partner_id'] == GOOGLE_FLIPKART_PARTNER_ID){
                        $sms['tag'] = "flipkart_google_scheduled_sms";
                        $sms['smsData'] = array();
                    }else{ 
                        $booking_id=$query1[0]['booking_id'];
                        $jobcard="BookingJobCard-".$booking_id.".pdf";
                        $jobcard_link=S3_WEBSITE_URL."jobcards-pdf/".$jobcard;
                        log_message('info', __METHOD__. " ". print_r($jobcard,true));
                        log_message('info', __METHOD__. " ". print_r($jobcard_link,true));
                        //make tiny url
                        $jobcard_link_new=str_replace(" ", "%20", $jobcard_link);
                        $tinyUrl = $this->My_CI->miscelleneous->getShortUrl($jobcard_link_new);
                        $call_type = explode(" ", $query1[0]['request_type']);
                        $sms['smsData']['service'] = $query1[0]['services'];
                        $sms['smsData']['call_type'] = $call_type[0];
                        if($query1[0]['is_upcountry'] == 1){
                            // Do not add booking date in the SMS
                        } else {
                            $sms['smsData']['booking_date'] = date("d-M-Y", strtotime($query1[0]['booking_date']));
                        }
                        
                        //$sms['smsData']['booking_timeslot'] = explode("-",$query1[0]['booking_timeslot'])[1];
                        $sms['smsData']['booking_id'] = $query1[0]['booking_id'];
                        $cc_number = ""; 
                        if($query1[0]['partner_id'] == VIDEOCON_ID){
                            $cc_number = "0120-4500600";
                        }
                        else if($query1[0]['partner_id'] == SHARP_ID){
                            $cc_number = SHARP_CALLCENTER_NUMBER;
                        }
                        else{
                            $cc_number = _247AROUND_CALLCENTER_NUMBER;
                        }
                        $sms['smsData']['cc_number'] = $cc_number;
                        
                        if($query1[0]['is_upcountry'] == 1){
                            $sms['tag'] = "upcountry_add_new_booking";
                        } else {
                            $sms['tag'] = "add_new_booking";
                        }
                        
                        log_message('info', __METHOD__. " ". print_r($sms, true));
                        if ($query1[0]['partner_id'] == JEEVES_ID) {
                            $sms['smsData']['public_name'] = "";
                        } 
                        else if($partner_type === OEM){ 
                            $brand_name = $this->My_CI->booking_model->get_unit_details(array('booking_id'=>$booking_id), false, 'appliance_brand');
                            if(!empty($brand_name)){
                                $sms['smsData']['public_name'] = $brand_name[0]['appliance_brand'];
                            }
                        }
                        else { 
                            $sms['smsData']['public_name'] = $query1[0]['public_name'];
                        }
                        
                        $sms['smsData']['url']=$tinyUrl;
                        
                    }
		   //$sms['smsData']['jobcard'] = S3_WEBSITE_URL."jobcards-excel/".$query1[0]['booking_jobcard_filename'];
		    $sms['booking_id'] = $query1[0]['booking_id'];
		    $sms['type'] = "user";
		    $sms['type_id'] = $query1[0]['user_id'];
                    log_message('info', __METHOD__. " ". print_r($sms, true));
		    $this->send_sms_msg91($sms);
                    
                    //send sms to dealer
                    if(!empty($query1[0]['dealer_id'])){
                        $dealerPhoneNumber = $this->My_CI->reusable_model->get_search_query('dealer_details','dealer_phone_number_1' , array('dealer_id'=>$query1[0]['dealer_id']),NULL, NULL ,NULL,NULL,NULL)->result_array()[0]['dealer_phone_number_1'];
                        $dealerSms['phone_no'] = $dealerPhoneNumber;
                        $dealerSms['tag'] = "booking_details_to_dealer";
                        $dealerSms['type'] = "dealer";
                        $dealerSms['type_id'] = $query1[0]['dealer_id'];
                        $dealerSms['booking_id'] = $query1[0]['booking_id'];
                        $dealerSms['smsData']['service'] = $query1[0]['services']. " ".$call_type[0];
                        $dealerSms['smsData']['customer_name'] = substr($query1[0]['name'], 0, 20);
                        $dealerSms['smsData']['booking_date'] = date("d/M", strtotime($query1[0]['booking_date']));
                        $dealerSms['smsData']['booking_timeslot'] = explode("-",$query1[0]['booking_timeslot'])[1];
                        $dealerSms['smsData']['booking_id'] = $query1[0]['booking_id'];
                        $dealerSms['smsData']['customer_phone_no'] = $query1[0]['booking_primary_contact_no'];
                        
                        $this->send_sms_msg91($dealerSms);
                    }
                    // send sms to user for  Brand Collateral file link
                    $data = $this->My_CI->service_centers_model->get_collateral_for_service_center_bookings($query1[0]['booking_id']);
                    if(!empty($data))
                    {
                            $finalString = 'Your Brand Collateral ->   '.nl2br ("\n");
                            $index =1;
                            foreach($data as $collatralData){
                                   if($collatralData['is_file']){
                                    $brand_coll_url = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/".$collatralData['file'];
                                    $brand_coll_url=str_replace(" ", "%20", $brand_coll_url);
                                 }
                                else{
                                    $brand_coll_url = $collatralData['file'];
                                    $brand_coll_url=str_replace(" ", "%20", $brand_coll_url);
                                }
                                //make tiny url
                                $brand_coll_tinyUrl = $this->My_CI->miscelleneous->getShortUrl($brand_coll_url);
                                $finalString .= ' '.$index.'.     ';
                                $finalString .= $collatralData['collateral_type'].'        ';
                                $finalString .=  '       '.$brand_coll_tinyUrl.'          ';
                                $finalString .=nl2br ("\n");
                                $index++;
                            }
                            $smsbody=$finalString;
                          //  $status  = $this->My_CI->notify->sendTransactionalSmsMsg91($query1[0]['booking_primary_contact_no'],$smsbody, SMS_WITHOUT_TAG);
            
                            //For saving SMS to the database on sucess
                           // $this->My_CI->notify->add_sms_sent_details($query1[0]['user_id'], 'user' , $query1[0]['booking_primary_contact_no'],
                                 //   $smsbody, $query1[0]['booking_id'],"brand_collateral_file_to_user", $status['content']);
                     }
                              
		    break;
                    
                case 'SendWhatsAppNo':
                    if($query1[0]['partner_id'] == VIDEOCON_ID){
                            if((stripos($query1[0]['request_type'], 'In Warranty') !== false) || stripos($query1[0]['request_type'], 'Extended Warranty') !== false){
                                //Send sms to customer for asking to send its purchanse invoice in under warrenty calls
                                /*
                                $whatsapp_details = $this->My_CI->partner_model->get_partner_additional_details("whatsapp_number", array("partner_id"=>$query1[0]['partner_id'], "is_whatsapp" => 1));
                                $whatsapp_no = "";
                                if(!empty($whatsapp_details)){
                                   $whatsapp_no =  $whatsapp_details[0]['whatsapp_number'];
                                }
                                else{
                                    $whatsapp_no = _247AROUND_WHATSAPP_NUMBER;
                                }
                                */ 
                                
                                $whatsapp_no = $this->get_vediocon_state_whatsapp_number($query1[0]['state']);
                                $brand_name = $this->My_CI->booking_model->get_unit_details(array('booking_id'=>$query1[0]["booking_id"]), false, 'appliance_brand');
                                if(!empty($brand_name)){
                                    $brand = $brand_name[0]['appliance_brand'];
                                } else {
                                    $brand = $query1[0]['public_name'];
                                }

                                $sms['type'] = "user";
                                $sms['type_id'] = trim($query1[0]['user_id']);
                                $sms['tag'] = SEND_WHATSAPP_NUMBER_TAG;
                                $sms['smsData']['brand'] = $brand;
                                $sms['smsData']['service'] = $query1[0]['services'];
                                $sms['smsData']['whatsapp_no'] = $whatsapp_no;
                                $sms['smsData']['partner_brand'] = $query1[0]['public_name'];
                                $this->send_sms_msg91($sms);
                            }
                        }
                    break;

		case 'Default_tax_rate':
		    sleep(180);
		    $to = NITS_ANUJ_EMAIL_ID;
		    //$to = "abhaya@247around.com";
		    $default_tax_rate = " Default Tax Rate is used in the Booking ID: " . $query1[0]['booking_id'];
		    $this->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", ' Default Tax Rate is used ', $default_tax_rate, "",'default_tax_rates');
		    break;

		case 'Pincode_not_found':
		    log_message('info', __METHOD__." Pincode Not Found ". $query1[0]['booking_pincode'] );
		    sleep(180);
		    $to = NITS_ANUJ_EMAIL_ID;
		    //$to = "abhaya@247around.com";
		    $state_not_found_message = " Pincode(" . $query1[0]['booking_pincode'] . ") is not found booking id is " . $query1[0]['booking_id'];
		    //$this->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", ' Pincode Not Found', $state_not_found_message, "");
		    break;
		   
	    }
	}
    }
    /**
     * @desc This is used to send email to sf when some one cancel booking
     * @param Array $query
     */
    function send_email_to_sf_when_booking_cancelled($booking_id) {
        log_message('info', __METHOD__ . " Booking ID " . $booking_id);
        $query = $this->My_CI->booking_model->getbooking_filter_service_center($booking_id);
        if (!empty($query) && !empty($query[0]['assigned_vendor_id'])) {
            $join['service_centres'] = 'booking_details.assigned_vendor_id = service_centres.id';
            $JoinTypeTableArray['service_centres'] = 'left';
            $booking_state = $this->My_CI->reusable_model->get_search_query('booking_details','service_centres.state',array('booking_details.booking_id' => $booking_id),$join,NULL,NULL,NULL,$JoinTypeTableArray)->result_array();
            
            //$get_partner_details = $this->My_CI->partner_model->getpartner_details('account_manager_id, primary_contact_email, owner_email', array('partners.id' => $query[0]['partner_id']));
            $get_partner_details = $this->My_CI->partner_model->getpartner_data("group_concat(distinct agent_filters.agent_id) as account_manager_id,primary_contact_email,owner_email", 
                            array('partners.id' => $query[0]['partner_id'], 'agent_filters.state' => $booking_state[0]['state']),"",0,1,1,"partners.id");
            $am_email = "";
            if (!empty($get_partner_details[0]['account_manager_id'])) {
                //$am_email = $this->My_CI->employee_model->getemployeefromid($get_partner_details[0]['account_manager_id'])[0]['official_email'];
                $am_email = $this->My_CI->employee_model->getemployeeMailFromID($get_partner_details[0]['account_manager_id'])[0]['official_email'];
            }

            $email_template = $this->My_CI->booking_model->get_booking_email_template("inform_to_sf_for_cancellation");
            if (!empty($email_template)) {
                log_message('info', __METHOD__ . " Template Found ");
                $vendor_details = $this->My_CI->vendor_model->getVendorDetails('primary_contact_email,owner_email', array('id' => $query[0]['assigned_vendor_id']));
                $to = $vendor_details[0]['primary_contact_email'] . "," . $vendor_details[0]['owner_email'];

                $sid = $query[0]['assigned_vendor_id'];
                $rm = $this->My_CI->vendor_model->get_rm_sf_relation_by_sf_id($sid);
                $rm_email = "";
                if (!empty($rm)) {
                    $rm_email = ", " . $rm[0]['official_email'];
                }

                $bcc = $email_template[5];
                $subject = vsprintf($email_template[4], array($query[0]['booking_id']));
                $message = vsprintf($email_template[0], array($query[0]['booking_id'], $query[0]['cancellation_reason']));
                if (!empty($am_email)) {
                    $from = $am_email;
                    $cc = $email_template[3] . "," . $am_email . $rm_email;
                } else {
                    $from = $email_template[2];
                    $cc = $email_template[3] . $rm_email;
                }
                $this->sendEmail($from, $to, $cc, $bcc, $subject, $message, "", 'inform_to_sf_for_cancellation');
                log_message('info', __METHOD__ . " Booking ID " . $booking_id . " mail Sent to " . $to);
            } else {
                log_message('info', __METHOD__ . " Template Not Found ");
            }
        } else {
            log_message('info', __METHOD__ . " Booking is not assigned ");
        }
    }

    /**
     * @desc: This is used to return free or empty according to given appliance
     * @param string $appliance
     * @return string free or ''
     */
    function get_product_free_not($appliance, $category) {
	//$status = '';
        $status = 'To be Paid';
        
//	switch ($appliance) {            
//
//            case 'Television':
//            case 'Microwave':
//            case 'Refrigerator':
//	    case 'Washing Machine':
//            case 'Water Purifier':
//	    case 'Air Conditioner':
//            case 'Chimney': 
//            case 'Geyser': 
//               
//                break;
//         
//	}
        
	return $status;
    }
    
    function sendTransactionalSmsAcl($phone_number, $body) {
      
        switch (ENVIRONMENT) {
	    case 'production':
                $message = urlencode($body);
                $url = "https://push3.maccesssmspush.com/servlet/com.aclwireless.pushconnectivity.listeners.TextListener?userId=blackmalt&pass=blackmalt67&appid=blackmalt&subappid=blackmalt&contenttype=1&"
                            . "to=" . $phone_number . "&from=AROUND&text=" . $message . "&selfid=true&alert=1&dlrreq=true";
                
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_exec($ch);
                curl_close($ch);
        }
    }
    function send_sms_using_knowlarity($phone_number, $body){
        $params = json_encode(array("client_id"=>KNOWLARITY_CLIENT_ID,"passphrase"=>KNOWLARITY_PASSPHRASE,"sender_id"=>KNOWLARITY_SENDER_ID,"sms_text"=>$body,"sms_number"=>"+91".$phone_number));
        $session = curl_init(KNOWLARITY_SMS_URL);
        curl_setopt_array($session, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'auth_key: 8b89e0a5-9c0a-11e8-8f5a-02d35676b79a',
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => $params
        ));
        $response = curl_exec($session);
        $responseAarray = json_decode($response);
        $data['content'] = $responseAarray->status;      
        return $data;
    }
    function send_sms_using_msg91($phone_number,$body){
        $data = array();
        $message = urlencode($body);
        $url = "https://control.msg91.com/api/sendhttp.php?authkey=".MSG91_AUTH_KEY."&mobiles="
                . $phone_number . "&message=" . $message
                . "&sender=".MSG91_SENDER_NAME."&route=4&country=91";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data['content'] = curl_exec($ch);
                        log_message('info', __METHOD__. "Transactional SMS91 Log: ".$data['content']);
                        curl_close($ch);
        return  $data;
    }
    function sendTransactionalSmsMsg91($phone_number, $body,$tag) {
        $this->validate_sms_length($phone_number,$body,$tag);
        $data = array();
        log_message("info",__METHOD__);
        switch (ENVIRONMENT) {
                case 'production':
                switch (CURRENT_SMS_SOLUTION) {
                    case KNOWLARITY_STRING :
                        $data = $this->send_sms_using_knowlarity($phone_number, $body);
                        break;
                        default:
                        $data  = $this->send_sms_using_msg91($phone_number, $body);
                }
        break;
        }
        
        return $data;
    }

    function send_sms_acl($sms) {
        $template = $this->My_CI->vendor_model->getVendorSmsTemplate($sms['tag']);
        if (!empty($template)) {
            $smsBody = vsprintf($template, $sms['smsData']);
            $this->sendTransactionalSmsAcl($sms['phone_no'], $smsBody);
            $this->add_sms_sent_details($sms['type_id'], $sms['type'], $sms['phone_no'], 
                    $smsBody, $sms['booking_id'], $sms['tag']);
        }
    }
    
    function send_sms_msg91($sms) {
        $template = $this->My_CI->vendor_model->getVendorSmsTemplate($sms['tag']);
        if (!empty($template)) {
            $smsBody = vsprintf($template, $sms['smsData']);
            if ($smsBody) {
                $status = $this->sendTransactionalSmsMsg91($sms['phone_no'], $smsBody,$sms['tag']);

                log_message('info', __METHOD__ . print_r($status, 1));

                //sometimes we get a 24 char random value, other times we get 'success'
                if ((ctype_alnum($status['content']) && strlen($status['content']) == 24) || (ctype_alnum($status['content']) && strlen($status['content']) == 25) 
                        || ($status['content'] == 'success') || (isset($status['message']) && ($status['message'] == "success") )){
                    $this->add_sms_sent_details($sms['type_id'], $sms['type'], $sms['phone_no'], $smsBody, $sms['booking_id'], $sms['tag'], $status['content']);
                } else {
                    $this->add_sms_sent_details($sms['type_id'], $sms['type'], $sms['phone_no'], $smsBody, $sms['booking_id'], $sms['tag'], $status['content']);
                    log_message('info', "Message Not Sent - Booking id: " . $sms['booking_id'] . ",
        		please recheck tag: '" . $sms['tag'] . "' & Phone Number - " . $sms['phone_no']);

                    $subject = 'SMS Sending Failed';
                    $message = "Please check SMS tag and phone number. Booking id is : " .
                            $sms['booking_id'] . " Tag is '" . $sms['tag'] . "' & phone number is :" . $sms['phone_no'] . " Result:"
                            . " " . $status['content'];
                    $to = DEVELOPER_EMAIL;

                    $this->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", $subject, $message, "",'sms_sending_failed');
                }
            } else {
                log_message('info', "Message Not Sent - Booking id: " . $sms['booking_id'] . ",
        		please recheck tag: '" . $sms['tag'] . "' & Phone Number - " . $sms['phone_no']);

                $subject = 'SMS not sent - Template Not Found';
                $message = "Please check SMS tag and phone number. Booking id is : " .
                        $sms['booking_id'] . " Tag is '" . $sms['tag'] . "' & phone number is :" . $sms['phone_no'] . " Result:"
                        . " " . $smsBody;
                $to = DEVELOPER_EMAIL;

                $this->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", $subject, $message, "",'sms_not_sent_template_not_found');
            }
        }
    }

    /**
     * @desc: This method is used to send sms to Engineer while assigned booking
     */
    function send_sms_to_assigned_engineer() {
        $engineers_id_with_booking_id = $this->input->post('booking_id_with_engineer_id');
        foreach ($engineers_id_with_booking_id as $booking_id => $engineer_id) {
            if (!empty($engineer_id)) {
                $query1 = $this->My_CI->booking_model->getbooking_filter_service_center($booking_id);
                log_message('info', __METHOD__ . "Assigned Engineer");

                $date1 = date('d-m-Y', strtotime('now'));
                $date2 = $query1[0]['booking_date'];
                $datediff = ($date1 - $date2) / (60 * 60 * 24);

                $month = date("m", strtotime($query1[0]['booking_date']));
                $dd = date("d", strtotime($query1[0]['booking_date']));
                $months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
                $mm = $months[$month - 1];

                if ($datediff == 0) {
                    $bookingdate = "Today";
                } elseif ($datediff == 1) {
                    $bookingdate = "Tomorrow";
                } else {
                    $bookingdate = $dd . " " . $mm;
                }

                $sms['type'] = "engineer";
                $sms['type_id'] = $query1[0]['assigned_engineer_id'];
                $sms['tag'] = "assigned_engineer";
                $sms['smsData']['customer_name'] = $query1[0]['name'];
                $sms['smsData']['phone_number'] = $query1[0]['booking_primary_contact_no'];
                $sms['smsData']['services'] = $query1[0]['services'];
                $sms['smsData']['booking_date'] = $bookingdate;
                $sms['smsData']['booking_timeslot'] = $query1[0]['booking_timeslot'];
                $sms['smsData']['booking_address'] = $query1[0]['booking_address'];
                $sms['smsData']['booking_timeslot'] = $query1[0]['booking_timeslot'];
                $this->send_sms_msg91($sms);
                break;
            }
        }
    }
    
    /* This function is used to save send email to the database
     * params: user_id, user_type, phone no., sms content, booking ID
     * return: Null
     */

    function add_email_send_details($email_from, $email_to, $cc, $bcc, $subject, $message, $attachment_link,$template_tag, $booking_id="") {
	$data = array();

	$data['email_from'] = $email_from;
	$data['email_to'] = $email_to;
	$data['cc'] = $cc;
	$data['bcc'] = $bcc;
	$data['subject'] = $subject;
        $data['message'] = $message;
        $data['attachment_link'] = $attachment_link;
        $data['email_tag'] = $template_tag;
        $data['booking_id'] = $booking_id;

	//Add Email to Database
	$insert_id = $this->My_CI->booking_model->add_email_send_details($data);
	if (!empty($insert_id)) {
	    log_message('info', __FUNCTION__ . ' Email has been saved to Database "email_sent" with ID ' . print_r($data, TRUE));
	} else {
	    log_message('info', __FUNCTION__ . ' Error on saving Email to Database "email_sent" ' . print_r($data, TRUE));
	}
    }
    /*
     * This Function is use to validate sms length
     */
    function validate_sms_length($phone,$body,$tag){
        $stringLength = strlen($body);
        if($stringLength > SMS_ALLOWED_LENGTH){
            $is_exception =  $this->My_CI->vendor_model->getVendorSmsTemplate($tag,true);
            if(!$is_exception){
                log_message('info', "Message Length is more than: " . SMS_ALLOWED_LENGTH. "phone". $phone . ",please check tag: '" . $tag);
                $subject = 'SMS Length is greater than '.SMS_ALLOWED_LENGTH;
                $message = "SMS Details is below <br>";
                $message .= "Phone Number : ".$phone."<br>";
                $message .= "SMS Tag :".$tag."<br>";
                $message .= "Body :".$body."<br><br>";
                $message .= "<b>Note</b> If SMS tag is 'tag_not_available' please create a template for this sms and mark is_exception_for_length TRUE (If length is needed more than 160 Character)";
                $to = 'chhavid@247around.com';
                $this->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", $subject, $message, "","sms_length_overruns");
            }
        }
    }
    
    function vediocon_call_not_picked_sms($data){
        //get partner whatsapp number
        /*
        $wh_number = "";
        $wh_detail = $this->My_CI->partner_model->get_partner_additional_details("whatsapp_number", array("partner_id"=>$data['partner_id'], "is_whatsapp"=>1));
        if(!empty($wh_detail)){
            $wh_number = $wh_detail[0]['whatsapp_number'];
        }
        else{
            $wh_number = _247AROUND_WHATSAPP_NUMBER;
        }
        */
        $wh_number = $this->get_vediocon_state_whatsapp_number($data['state']);
        $sms['smsData']['public_name'] = $data['public_name'];
        $sms['smsData']['service'] = $data['services'];
        $sms['smsData']['wh_number'] = $wh_number;
        $sms['smsData']['public_name_2'] = $data['public_name'];
        $sms['phone_no'] = $data['booking_primary_contact_no'];
        $sms['tag'] = VIDEOCON_NOT_PICKED_SMS_TAG;
        $sms['booking_id'] = $data['booking_id'];
        $sms['type'] = "user";
        $sms['type_id'] = $data['user_id'];

        $this->send_sms_msg91($sms);
    }
    
    function vediocon_cancelled_booking_sms($data){
        //get partner customer care number
        $cc_number = "";
        $cc_detail = $this->My_CI->partner_model->get_partner_additional_details("customer_care_number", array("partner_id"=>$data['partner_id'], "is_customer_care"=>1));
        if(!empty($cc_detail)){
            $cc_number = $cc_detail[0]['customer_care_number'];
        }
        else{
            $cc_number = _247AROUND_WHATSAPP_NUMBER;
        }
        $sms['smsData']['service'] = $data['services'];
        $sms['smsData']['cc_number'] = $cc_number;
        $sms['smsData']['public_name'] = $data['public_name'];
        $sms['phone_no'] = $data['booking_primary_contact_no'];
	$sms['tag'] = VIDEOCON_CANCELLED_BOOKING_TAG;
        $sms['booking_id'] = $data['booking_id'];
        $sms['type'] = "user";
        $sms['type_id'] = $data['user_id'];

        $this->send_sms_msg91($sms);
    }
    
    function get_vediocon_state_whatsapp_number($state){
        $videocon_states_number = array("Uttar Pradesh" => "8448759247", "Delhi" => "8130070247", "Maharashtra" => "8130070247", "Gujarat" => "8130070247");
        if (array_key_exists($state, $videocon_states_number)){ 
            return $videocon_states_number[$state];
        }
        else{
            return "9810594247";
        }
        
    }
}
