<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

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
	//Clear previous email
	$this->My_CI->email->clear(TRUE);

	//Attach file with mail
	if (!empty($attachment))
	    $this->My_CI->email->attach($attachment, 'attachment');

	$this->My_CI->email->from($from, '247around Team');
	$this->My_CI->email->to($to);
	$this->My_CI->email->bcc($bcc);
	$this->My_CI->email->cc($cc);
	$this->My_CI->email->subject($subject);
	$this->My_CI->email->message($message);

	if ($this->My_CI->email->send())
	    return true;
	else
	    return false;
    }

    function sendTransactionalSms($phone_number, $body) {
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

	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FAILONERROR, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));

	$http_result = curl_exec($ch);
	$error = curl_error($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	//print_r($ch);
	//echo exit();
	curl_close($ch);
    }


     /**
     *  @desc : This method is to use SMS templates
     *  @param : SMS tag,parameters and phone no.
     *  @return : if SMS send return true else false
     */
    function send_sms($sms){
  
    $template = $this->My_CI->vendor_model->getVendorSmsTemplate($sms['tag']); 
    if(!empty($template)){
        $smsBody = vsprintf($template, $sms['smsData']);
        $this->sendTransactionalSms($sms['phone_no'], $smsBody);
    } else {

        log_message('info', "Message Not Sent - Booking id: " . $sms['booking_id']. ", 
        		please recheck tag: '".$sms['tag']."' & Phone Number - ". $sms['phone_no']);
    	$subject = 'Booking SMS not sent';
    	$message = "Please check SMS tag and phone number. Booking id is : ". 
    		            $sms['booking_id']. " Tag is '".$sms['tag']."' & phone number is :".$sms['phone_no'];
    	$to = "anuj@247around.com, nits@247around.com";
    	$this->sendEmail("booking@247around.com", $to, "", "", $subject, $message, "");
        }
    }
}
