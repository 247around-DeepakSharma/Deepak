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

		break;
	}
    }

    function sendTransactionalSms($phone_number, $body) {
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

		curl_exec($ch);
		curl_error($ch);
		curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);

		break;
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
	    $this->sendTransactionalSms($sms['phone_no'], $smsBody);
	} else {
	    log_message('info', "Message Not Sent - Booking id: " . $sms['booking_id'] . ",
        		please recheck tag: '" . $sms['tag'] . "' & Phone Number - " . $sms['phone_no']);
	    $subject = 'Booking SMS not sent';
	    $message = "Please check SMS tag and phone number. Booking id is : " .
		$sms['booking_id'] . " Tag is '" . $sms['tag'] . "' & phone number is :" . $sms['phone_no'];
	    $to = "anuj@247around.com, nits@247around.com";
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
	    $to = "anuj@247around.com, nits@247around.com";
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
		$cb = base_url() . 'call-customer-status-callback';
		$post_data = array(
		    'From' => $agent_phone,
		    'To' => $customer_phone,
		    'CallerId' => '01139595200', //247around call centre exophone number
		    'CallType' => 'trans',
		    'StatusCallback' => $cb
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
     * @desc: this is used to insert agent record while update booking
     * @param: String(booking id, agent id, agent name, booking status)
     * @return: void
     */
    function insert_state_change($booking_id, $new_state, $old_state, $agent_id, $agent_name) {
	//Log this state change as well for this booking
	//Log this state change as well for this booking
	$state_change['booking_id'] = $booking_id;
	$state_change['old_state'] = $old_state;
	$state_change['new_state'] = $new_state;
	$state_change['agent_id'] = $agent_id;
	$this->My_CI->booking_model->insert_booking_state_change($state_change);
	log_message('info', 'Booking Status Change - Booking id: ' . $booking_id . $new_state . "  By " . $agent_name);
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


	$sms['smsData']['service'] = $query1[0]['services'];
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
		$email['booking_timeslot'] = $query1[0]['booking_timeslot'];
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

		$this->send_sms($sms);

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
		    }
		    $email['tag'] = "cancel_booking";
		    $email['subject'] = "Pending Booking Cancellation - 247AROUND";
		    //Send internal mails now
		    $this->send_email($email);

		    if ($is_sd == FALSE) {
			$sms['tag'] = "cancel_booking";
			$this->send_sms($sms);
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
		    $sms['smsData']['booking_date'] = $query1[0]['booking_date'];
		    $sms['smsData']['booking_timeslot'] = $query1[0]['booking_timeslot'];
		    $this->send_sms($sms);
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
		if ($is_sd) {
		    $sms['tag'] = "call_not_picked_snapdeal";
		} else {
		    $sms['tag'] = "call_not_picked_other";
		}
		$sms['smsData']['name'] = $query1[0]['name'];

		$this->send_sms($sms);
		break;

	    case 'Newbooking':
		if ($is_sd == FALSE) {
		    $sms['tag'] = "add_new_booking";
		    $sms['smsData']['booking_date'] = $query1[0]['booking_date'];
		    $sms['smsData']['booking_timeslot'] = $query1[0]['booking_timeslot'];

		    $this->notify->send_sms($sms);
		} else {
		    $sms['tag'] = "new_snapdeal_booking";
		    $sms['smsData']['booking_date'] = $query1[0]['booking_date'];
		    $sms['smsData']['booking_timeslot'] = $query1[0]['booking_timeslot'];

		    $this->notify->send_sms($sms);
		}
	}
    }

}
