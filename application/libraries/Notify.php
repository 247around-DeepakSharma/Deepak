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
	$this->My_CI->load->model('vendor_model');
	$this->My_CI->load->model('booking_model');
    }

    /**
     *  @desc : This method is used to send mail
     *  @param : From, To, CC, BCC, Subject, Message, Attachment
     *  @return : if mail send return true else false
     */
    function sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment) {
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

    function add_sms_sent_details($type_id, $type, $phone, $content, $booking_id, $sms_tag) {
	$data = array();

	$data['type_id'] = $type_id;
	$data['type'] = $type;
	$data['phone'] = $phone;
	$data['booking_id'] = $booking_id;
	$data['content'] = $content;
        $data['sms_tag'] = $sms_tag;

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
            
	    $this->sendEmail("booking@247around.com", $to, "", "", $subject, $message, "");
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
	    $cc = "";
	    $bcc = "";
	    $subject = $email['subject'];
	    $message = $emailBody;
	    $attachment = "";
	    log_message('info', " Email Message" . print_r($message, true));

	    $this->sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment);
	} else {
	    log_message('info', "Email Not Sent - Booking id: " . $email['booking_id'] . ",
        		please recheck tag: '" . $email['tag'] . "' & Phone Number - " . $email['phone_no']);
	    $subject = 'Booking Email not sent';
	    $message = "Please check email tag and phone number. Booking id is : " .
		$email['booking_id'] . " Tag is '" . $email['tag'] . "' & phone number is :" . $email['phone_no'];
	    $to = NITS_ANUJ_EMAIL_ID;
	    $this->sendEmail("booking@247around.com", $to, "", "", $subject, $message, "");
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
    function insert_state_change($booking_id, $new_state, $old_state, $remarks, $agent_id, $agent_name, $partner_id) {
	//Log this state change as well for this booking
	$state_change['booking_id'] = $booking_id;
	$state_change['old_state'] = $old_state;
	$state_change['new_state'] = $new_state;
        $state_change['remarks'] = $remarks;
	$state_change['agent_id'] = $agent_id;
	$state_change['partner_id'] = $partner_id;
            
        /*
         * Send correct old_state from the calling function instead, do not change
         * the old state here
         * 
        $booking_state_change = $this->My_CI->booking_model->get_booking_state_change($state_change['booking_id']);
        
        if ($booking_state_change > 0) {
            $state_change['old_state'] = $booking_state_change[count($booking_state_change) - 1]['new_state'];
        } else { //count($booking_state_change)
            $state_change['old_state'] = $old_state;
        }
         * 
         */
        
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
	//Is this SD booking?
	if (strpos($booking_id, "SS") !== FALSE) {
	    $is_sd = TRUE;
	} else {
	    $is_sd = FALSE;
	}

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

		    //Send internal mails now
		    $this->send_email($email);

		    if ($is_sd == FALSE) {

			$sms['tag'] = "complete_booking";
		    } else {

			$sms['tag'] = "complete_booking_snapdeal";
		    }

		    $sms['smsData']['service'] = $query1[0]['services'];
		    $sms['booking_id'] = $query1[0]['booking_id'];
		    $sms['type'] = "user";
		    $sms['type_id'] = $query1[0]['user_id'];
		    $this->send_sms_acl($sms);

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
			$email['name'] = $query1[0]['name'];
			$email['phone_no'] = $query1[0]['phone_number'];
			$email['user_email'] = $query1[0]['user_email'];
			$email['booking_id'] = $query1[0]['booking_id'];
			$email['service'] = $query1[0]['services'];
			$email['booking_date'] = $query1[0]['booking_date'];
			$email['booking_timeslot'] = $query1[0]['booking_timeslot'];
			$email['closed_date'] = $query1[0]['closed_date'];
			$email['cancellation_reason'] = $query1[0]['cancellation_reason'];
			if (isset($query1[0]['vendor_name'])) {
			    $email['vendor_name'] = $query1[0]['vendor_name'];
			    $email['city'] = $query1[0]['district'];
			} else {
			    $email['vendor_name'] = "";
			    $email['city'] = "";
			}
			$email['tag'] = "cancel_booking";
			$email['subject'] = "Pending Booking Cancellation - 247AROUND";
			//Send internal mails now
			$this->send_email($email);

			if ($is_sd == FALSE) {
			    $sms['smsData']['service'] = $query1[0]['services'];
			    $sms['tag'] = "cancel_booking";
			    $sms['booking_id'] = $query1[0]['booking_id'];
			    $sms['type'] = "user";
			    $sms['type_id'] = $query1[0]['user_id'];

			    $this->send_sms_acl($sms);
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

		    if ($is_sd == FALSE) {
			$sms['tag'] = "reschedule_booking";
			$sms['smsData']['service'] = $query1[0]['services'];
			$sms['smsData']['booking_date'] = $query1[0]['booking_date'];
			$sms['smsData']['booking_timeslot'] = $query1[0]['booking_timeslot'];
			$sms['booking_id'] = $query1[0]['booking_id'];
			$sms['type'] = "user";
			$sms['type_id'] = $query1[0]['user_id'];
			$this->send_sms_acl($sms);
		    }

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
		    $sms['smsData']['name'] = $query1[0]['name'];
		    $sms['smsData']['service'] = $query1[0]['services'];

		    if ($is_sd) {
			$sms['tag'] = "call_not_picked_snapdeal";
		    } else {
			$sms['tag'] = "call_not_picked_other";
		    }

		    $sms['booking_id'] = $query1[0]['booking_id'];
		    $sms['type'] = "user";
		    $sms['type_id'] = $query1[0]['user_id'];

		    $this->send_sms_acl($sms);
		    break;

		case 'Newbooking':
		    $sms['smsData']['service'] = $query1[0]['services'];
		    $sms['smsData']['booking_date'] = $query1[0]['booking_date'];
		    $sms['smsData']['booking_timeslot'] = explode("-",$query1[0]['booking_timeslot'])[1];

		    if ($is_sd == FALSE) {
			$sms['tag'] = "add_new_booking";
		    } else {
			$sms['tag'] = "new_snapdeal_booking";
		    }

		    $sms['booking_id'] = $query1[0]['booking_id'];
		    $sms['type'] = "user";
		    $sms['type_id'] = $query1[0]['user_id'];

		    $this->send_sms_acl($sms);

		    break;

		case 'Default_tax_rate':
		    sleep(180);
		    $to = NITS_ANUJ_EMAIL_ID;
		    //$to = "abhaya@247around.com";
		    $default_tax_rate = " Default Tax Rate is used in the Booking ID: " . $query1[0]['booking_id'];
		    $this->sendEmail("booking@247around.com", $to, "", "", ' Default Tax Rate is used ', $default_tax_rate, "");
		    break;

		case 'Pincode_not_found':
		    log_message('info', __METHOD__." Pincode Not Found ". $query1[0]['booking_pincode'] );
		    sleep(180);
		    $to = NITS_ANUJ_EMAIL_ID;
		    //$to = "abhaya@247around.com";
		    $state_not_found_message = " Pincode(" . $query1[0]['booking_pincode'] . ") is not found booking id is " . $query1[0]['booking_id'];
		    $this->sendEmail("booking@247around.com", $to, "", "", ' Pincode Not Found', $state_not_found_message, "");


		    break;
		   
	    }
	}
    }

    /**
     * @desc: This is used to return free or empty according to given appliance
     * @param string $appliance
     * @return string free or ''
     */
    function get_product_free_not($appliance, $category) {
	$status = '';
        
	switch ($appliance) {            

            case 'Television':
            case 'Microwave':
            case 'Refrigerator':
	    case 'Washing Machine':
            case 'Water Purifier':
		$status = 'FREE';
		break;

	    case 'Air Conditioner':
                $status = 'To be Paid';
                break;
            
	    case 'Chimney':
                $status = 'To be Paid';
		break;
            case 'Geyser':
                switch ($category){
                
                case 'Geyser-PAID':
                    $status = 'Rs 250';
                    break;
                default :
                    
                    $status = 'FREE';
                    break;
                }
                
                break;
	}
        
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

    function send_sms_acl($sms) {
        $template = $this->My_CI->vendor_model->getVendorSmsTemplate($sms['tag']);
        if (!empty($template)) {
            $smsBody = vsprintf($template, $sms['smsData']);
            $this->sendTransactionalSmsAcl($sms['phone_no'], $smsBody);
            $this->add_sms_sent_details($sms['type_id'], $sms['type'], $sms['phone_no'], 
                    $smsBody, $sms['booking_id'], $sms['tag']);
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
                $this->send_sms($sms);
                break;
            }
        }
    }


}
