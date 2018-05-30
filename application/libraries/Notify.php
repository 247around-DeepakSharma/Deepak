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
    function sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment,$template_tag) {
	switch (ENVIRONMENT) {
	    case 'production':
		//Clear previous email
                if(!empty($to)){
                    $this->My_CI->email->clear(TRUE);

                    //Attach file with mail
                    if (!empty($attachment)) {
                        $this->My_CI->email->attach($attachment, 'attachment');
                    }

                    $this->My_CI->email->from($from, '247around Team');

                    $this->My_CI->email->to($to);
                    $this->My_CI->email->bcc($bcc);
                    $this->My_CI->email->cc($cc);

                    $this->My_CI->email->subject($subject);
                    $this->My_CI->email->message($message);

                    if ($this->My_CI->email->send()) {
                        $this->add_email_send_details($from, $to, $cc, $bcc, $subject, $message, $attachment,$template_tag);
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
		    'From' => '01130017601',
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
	    log_message('info', "Email Not Sent - Booking id: " . $email['booking_id'] . ",
        		please recheck tag: '" . $email['tag'] . "' & Phone Number - " . $email['phone_no']);
	    $subject = 'Booking Email not sent';
	    $message = "Please check email tag and phone number. Booking id is : " .
		$email['booking_id'] . " Tag is '" . $email['tag'] . "' & phone number is :" . $email['phone_no'];
	    $to = NITS_ANUJ_EMAIL_ID;
	    $this->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", $subject, $message, "",'booking_email_not_sent');
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
    function make_outbound_call($agent_phone, $customer_phone) {
	//Callback fn called by Exotel
	switch (ENVIRONMENT) {
	    case 'production':
		$post_data = array(
		    'From' => $agent_phone,
		    'To' => $customer_phone,
		    'CallerId' => '01139595200', //247around call centre exophone number
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
    function insert_state_change($booking_id, $new_state, $old_state, $remarks, $agent_id, $agent_name,$actor,$next_action, $partner_id = NULL, $service_center_id = NULL) {
	//Log this state change as well for this booking
	$state_change['booking_id'] = $booking_id;
	//$state_change['old_state'] = $old_state;
	$state_change['new_state'] = $new_state;
                     $state_change['remarks'] = $remarks;
	$state_change['agent_id'] = $agent_id;
	$state_change['partner_id'] = $partner_id;
                    $state_change['service_center_id'] = $service_center_id;
                    $state_change['actor'] = $actor;
                    $state_change['next_action'] = $next_action;
            
        /*
         * Send correct old_state from the calling function instead, do not change
         * the old state here
         * */
                   $state_change['old_state'] = $old_state;
                   if(!empty($booking_id)){
                          $booking_state_change = $this->My_CI->booking_model->get_booking_state_change($state_change['booking_id']);  
                          if ($booking_state_change > 0) {
                              $state_change['old_state'] = $booking_state_change[count($booking_state_change) - 1]['new_state'];
                          } 
                    }
        $insert_id = $this->My_CI->booking_model->insert_booking_state_change($state_change);
        
        if($insert_id){
            log_message('info', 'Booking Status Change - Booking id: ' . $booking_id . $new_state . "  By " . $agent_name);
        }else{
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

	            $sms['tag'] = "complete_booking";
		    $call_type = explode(" ", $query1[0]['request_type']);
		    $sms['smsData']['service'] = $query1[0]['services'];
                    $sms['smsData']['call_type'] = $call_type[0];
                    $sms['smsData']['booking_id'] = $query1[0]['booking_id'];
                    $sms['smsData']['good_rating_number'] = GOOD_MISSED_CALL_RATING_NUMBER;
                    $sms['smsData']['poor_rating_number'] = POOR_MISSED_CALL_RATING_NUMBER;
		    $sms['booking_id'] = $query1[0]['booking_id'];
		    $sms['type'] = "user";
		    $sms['type_id'] = $query1[0]['user_id'];
		    $this->send_sms_msg91($sms);

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


			$call_type = explode(" ", $query1[0]['request_type']);
                        $sms['smsData']['call_type'] = $call_type[0];
                        $sms['smsData']['service'] = $query1[0]['services'];
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
                    $call_type = explode(" ", $query1[0]['request_type']);
                    $sms['smsData']['service'] = $query1[0]['services']." ".$call_type[0];
		    $sms['tag'] = "call_not_picked_other";
		    $sms['booking_id'] = $query1[0]['booking_id'];
		    $sms['type'] = "user";
		    $sms['type_id'] = $query1[0]['user_id'];

		    $this->send_sms_msg91($sms);
		    break;

		case 'Newbooking':
                    
                    if($query1[0]['partner_id'] == GOOGLE_FLIPKART_PARTNER_ID){
                        $sms['tag'] = "flipkart_google_scheduled_sms";
                        $sms['smsData'] = array();
                    }else{
                        $call_type = explode(" ", $query1[0]['request_type']);
                        $sms['smsData']['service'] = $query1[0]['services'];
                        $sms['smsData']['call_type'] = $call_type[0];
                        $sms['smsData']['booking_date'] = date("d-M-Y", strtotime($query1[0]['booking_date']));
                        //$sms['smsData']['booking_timeslot'] = explode("-",$query1[0]['booking_timeslot'])[1];
                        $sms['smsData']['booking_id'] = $query1[0]['booking_id'];

                        if ($query1[0]['partner_id'] == JEEVES_ID) {
                            $sms['smsData']['public_name'] = "";
                        } else {
                            $sms['smsData']['public_name'] = $query1[0]['public_name']. " Partner";
                        }

                        $sms['tag'] = "add_new_booking";
                    }
		    
		    
		    $sms['booking_id'] = $query1[0]['booking_id'];
		    $sms['type'] = "user";
		    $sms['type_id'] = $query1[0]['user_id'];

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
            $get_partner_details = $this->My_CI->partner_model->getpartner_details('account_manager_id, primary_contact_email, owner_email', array('partners.id' => $query[0]['partner_id']));
            $am_email = "";
            if (!empty($get_partner_details[0]['account_manager_id'])) {

                $am_email = $this->My_CI->employee_model->getemployeefromid($get_partner_details[0]['account_manager_id'])[0]['official_email'];
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

    function sendTransactionalSmsMsg91($phone_number, $body) {
        log_message("info",__METHOD__);
        $data = array();
        switch (ENVIRONMENT) {
                case 'production':
                $message = urlencode($body);
                $url = "https://control.msg91.com/api/sendhttp.php?authkey=141750AFjh6p9j58a80789&mobiles="
                        . $phone_number . "&message=" . $message
                        . "&sender=AROUND&route=4&country=91";
                
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                
                $data['content'] = curl_exec($ch);
				log_message('info', __METHOD__. "Transactional SMS91 Log: ".$data['content']);
				curl_close($ch);
                
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
                $status = $this->sendTransactionalSmsMsg91($sms['phone_no'], $smsBody);

                log_message('info', __METHOD__ . print_r($status, 1));

                //sometimes we get a 24 char random value, other times we get 'success'
                if ((ctype_alnum($status['content']) && strlen($status['content']) == 24) || ($status['content'] == 'success')){
                    $this->add_sms_sent_details($sms['type_id'], $sms['type'], $sms['phone_no'], $smsBody, $sms['booking_id'], $sms['tag'], $status['content']);
                } else {
                    $this->add_sms_sent_details($sms['type_id'], $sms['type'], $sms['phone_no'], $smsBody, $sms['booking_id'], $sms['tag'], $status['content']);
                    log_message('info', "Message Not Sent - Booking id: " . $sms['booking_id'] . ",
        		please recheck tag: '" . $sms['tag'] . "' & Phone Number - " . $sms['phone_no']);

                    $subject = 'SMS Sending Failed';
                    $message = "Please check SMS tag and phone number. Booking id is : " .
                            $sms['booking_id'] . " Tag is '" . $sms['tag'] . "' & phone number is :" . $sms['phone_no'] . " Result:"
                            . " " . $status['content'];
                    $to = "anuj@247around.com, sachinj@247around.com";

                    $this->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", $subject, $message, "",'sms_sending_failed');
                }
            } else {
                log_message('info', "Message Not Sent - Booking id: " . $sms['booking_id'] . ",
        		please recheck tag: '" . $sms['tag'] . "' & Phone Number - " . $sms['phone_no']);

                $subject = 'SMS not sent - Template Not Found';
                $message = "Please check SMS tag and phone number. Booking id is : " .
                        $sms['booking_id'] . " Tag is '" . $sms['tag'] . "' & phone number is :" . $sms['phone_no'] . " Result:"
                        . " " . $status['content'];
                $to = "anuj@247around.com, sachinj@247around.com";

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

    function add_email_send_details($email_from, $email_to, $cc, $bcc, $subject, $message, $attachment_link,$template_tag) {
	$data = array();

	$data['email_from'] = $email_from;
	$data['email_to'] = $email_to;
	$data['cc'] = $cc;
	$data['bcc'] = $bcc;
	$data['subject'] = $subject;
        $data['message'] = $message;
        $data['attachment_link'] = $attachment_link;
                    $data['email_tag'] = $template_tag;

	//Add Email to Database
	$insert_id = $this->My_CI->booking_model->add_email_send_details($data);
	if (!empty($insert_id)) {
	    log_message('info', __FUNCTION__ . ' Email has been saved to Database "email_sent" with ID ' . $subject);
	} else {
	    log_message('info', __FUNCTION__ . ' Error on saving Email to Database "email_sent" ' . print_r($data, TRUE));
	}
    }
}
