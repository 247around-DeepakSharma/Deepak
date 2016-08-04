<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

/**
 * Description of Booking_utilities
 *
 * @author anujaggarwal
 */
class Booking_utilities {

    var $My_CI;

    function __Construct() {
	$this->My_CI = & get_instance();

	$this->My_CI->load->library('PHPReport');
	$this->My_CI->load->library('email');
	$this->My_CI->load->library('s3');
	$this->My_CI->load->library('form_validation');
	$this->My_CI->load->library("session");

	$this->My_CI->load->helper('download');
	$this->My_CI->load->helper(array('form', 'url'));

	$this->My_CI->load->model('employee_model');
	$this->My_CI->load->model('booking_model');
	$this->My_CI->load->model('reporting_utils');
    }

    public function lib_prepare_job_card_using_booking_id($booking_id) {
	log_message('info', __FUNCTION__);

	log_message('info', $booking_id);

	$file_names = array();

	$template = 'BookingJobCard_Template-v8.xlsx';
	//set absolute path to directory with template files
	$templateDir = __DIR__ . "/../controllers/";

	//set config for report
	$config = array(
	    'template' => $template,
	    'templateDir' => $templateDir
	);

	//load template
	$R = new PHPReport($config);
	//log_message('info', "PHP report");

	$booking_details = $this->My_CI->booking_model->getbooking_history($booking_id);
	//echo "Booking Details: " . "\n";
	//print_r($booking_details);

	$unit_details = $this->My_CI->booking_model->get_unit_details($booking_id);

	$R->load(array(
	    array(
		'id' => 'booking',
		//'repeat' => TRUE,
		'data' => $booking_details[0],
		//'minRows' => 2,
		'format' => array(
		    'booking_date' => array('datetime' => 'd/M/Y'),
		    'amount_due' => array('number' => array('prefix' => 'Rs. ')),
		)
	    ),
	    array(
		'id' => 'unit',
		'repeat' => TRUE,
		'data' => $unit_details,
		//'minRows' => 2,
		'format' => array(
		    //'create_date' => array('datetime' => 'd/M/Y'),
		    'total_price' => array('number' => array('prefix' => 'Rs. ')),
		)
	    ),
	    )
	);

	//Get populated XLS with data
	if ($booking_details[0]['current_status'] == "Rescheduled")
	    $output_file_suffix = "-RESC-" . $booking_details[0]['booking_date'];
	else
	    $output_file_suffix = "";

	$output_file_dir = "/tmp/";
	$output_file = "BookingJobCard-" . $booking_id . $output_file_suffix;

	$output_file_excel = $output_file_dir . $output_file . ".xlsx";
	$result = $R->render('excel', $output_file_excel);
	$output_file_pdf = $output_file_dir . $output_file . ".pdf";

	//Update output file name in DB
	$this->My_CI->reporting_utils->update_booking_jobcard($booking_details[0]['id'], $output_file . ".pdf");

	//$cmd = "curl -F file=@" . $output_file_excel . " http://do.convertapi.com/Excel2Pdf?apikey=278325305" . " -o " . $output_file_pdf;
	putenv('PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/opt/node/bin');
	$tmp_path = '/home/around/libreoffice_tmp';
	$tmp_output_file = '/home/around/libreoffice_tmp/output.txt';
	//$tmp_path = '/var/www/libreoffice';
	//$tmp_output_file = '/var/www/output.txt';
	$cmd = 'echo ' . $tmp_path . ' & echo $PATH & UNO_PATH=/usr/lib/libreoffice & ' .
	    '/usr/bin/unoconv --format pdf --output ' . $output_file_pdf . ' ' .
	    $output_file_excel . ' 2> ' . $tmp_output_file;

	//echo $cmd;
	$output = '';
	$result_var = '';
	exec($cmd, $output, $result_var);

	//Upload Excel & PDF files to AWS
	$bucket = 'bookings-collateral';

	$directory_xls = "jobcards-excel/" . $output_file . ".xlsx";
	$this->My_CI->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);

	$directory_pdf = "jobcards-pdf/" . $output_file . ".pdf";
	$this->My_CI->s3->putObjectFile($output_file_pdf, $bucket, $directory_pdf, S3::ACL_PUBLIC_READ);

//	$this->My_CI->session->set_flashdata('result', 'Job card generated successfully');
//	redirect(base_url() . 'employee/booking/view', 'refresh');*/
    }

    //This function sends email to the assigned vendor
    function lib_send_mail_to_vendor($booking_id, $additional_note) {
	//log_message('info', __FUNCTION__);

	$getbooking = $this->My_CI->booking_model->getbooking($booking_id);
	$booking_details = $this->My_CI->booking_model->search_bookings_by_booking_id($booking_id);
	if ($getbooking) {
	    $serviceName = $this->My_CI->booking_model->selectservicebyid($getbooking[0]['service_id']);
	    $servicecentredetails = $this->My_CI->booking_model->selectservicecentre($booking_id);

	    $salutation = "Dear " . $servicecentredetails[0]['primary_contact_name'];
	    $heading = "<br><br>Please find attached job card " . $getbooking[0]['booking_id'] . " for "
		. $serviceName[0]['services'] .
		"<br><br>Date: " . $getbooking[0]['booking_date'] .
		"<br>Time Slot: " . $getbooking[0]['booking_timeslot'];

	    $booking_remarks = "<br><br>Booking remarks: " . $getbooking[0]['booking_remarks'];
	    $heading .= $booking_remarks;
	    $note = "<br>Special Note: " . urldecode($additional_note) . "<br>";

	    $fixedPara = "<br><br>Engineer should follow below guidelines:<br>
                        <br>1. Be very polite,
                        <br>2. Carry tools, No service to be attended without tools,
                        <br>3. Carry soap in case of AC Service,
                        <br>4. Helper to clean the service area after completing service.
                        <br>5. Be very careful while Uninstalling and Installing any Appliance.
                        <br>6. RUN AND CHECK THE Appliance after repair for 5-10 minutes..
                        <br><br>With Regards
                        <br>Devendra - 247Around - 9555118612
                        <br>247Around is part of Businessworld Startup Accelerator & Google Bootcamp 2015
                        <br>Follow us on Facebook: www.facebook.com/247Around | Website:
                        <br>www.247Around.com
                        <br>You will Love our Video Advertisements:
                        <br>https://www.youtube.com/watch?v=y8sBWDPHAhI
                        <br>Playstore - 247Around -
                        <br>https://play.google.com/store/apps/details?id=com.handymanapp
                        ";

	    $message = $salutation . $heading . $note . $fixedPara;

	    $to = $servicecentredetails[0]['primary_contact_email'];
	    $owner = $servicecentredetails[0]['owner_email'];
	    $cc = $owner;
	    $bcc = 'anuj@247around.com';

	    $subject = "247Around / Job Card " . $getbooking[0]['booking_id'] . " / " . $getbooking[0]['booking_date'] .
		" / " . $getbooking[0]['booking_timeslot'];

	    $file_pdf = $getbooking[0]['booking_jobcard_filename'];
	    $output_file_pdf = "/tmp/" . $getbooking[0]['booking_jobcard_filename'];

	    $cmd = "curl https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/" . $file_pdf . " -o " . $output_file_pdf;
	    exec($cmd);

	    //Clear previous email
	    $this->My_CI->email->clear(TRUE);

	    //Attach this PDF file
	    $this->My_CI->email->attach($output_file_pdf, 'attachment');

	    $this->My_CI->email->from('booking@247around.com', '247around Team');
	    $this->My_CI->email->to($to);
	    $this->My_CI->email->cc($cc);
	    $this->My_CI->email->bcc($bcc);
	    $this->My_CI->email->subject($subject);
	    $this->My_CI->email->message($message);

	    $date1 = date('d-m-Y', strtotime('now'));
	    $date2 = $getbooking[0]['booking_date'];
	    $datediff = ($date1 - $date2) / (60 * 60 * 24);

	    $mm = date("m", strtotime($getbooking[0]['booking_date']));
	    $dd = date("d", strtotime($getbooking[0]['booking_date']));
	    $months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

	    $mm = $months[$mm - 1];

	    if ($datediff == 0) {
		$bookingdate = "Today";
	    } elseif ($datediff == 1) {
		$bookingdate = "Tomorrow";
	    } else {
		$bookingdate = $dd . " " . $mm;
	    }

	    if ($booking_details[0]->booking_timeslot = '10AM-1PM') {
	    	$booking_details[0]->booking_timeslot= '10AM';
	    }elseif ($booking_details[0]->booking_timeslot = '1PM-4PM') {
	    	$booking_details[0]->booking_timeslot= '1PM';
	    }
	    else {
	    	$booking_details[0]->booking_timeslot= '4PM';
	    }


	    $smsBody = "Booking - " . $booking_details[0]->customername . ", " . $booking_details[0]->phone_number . ", " . $serviceName[0]['services'] . ", " . $bookingdate ."/" . $booking_details[0]->booking_timeslot .  ", " . $getbooking[0]['booking_address'] . ", ". $booking_details[0]->booking_pincode . ". 247around";

	    //Send SMS to vendor
	    $this->sendTransactionalSms($servicecentredetails[0]['primary_contact_phone_1'], $smsBody);
	    //Save email in database
	    $details = array("booking_id" => $booking_id, "subject" => $subject,
		"body" => $message, "type" => "Booking",
		"attachment" => $getbooking[0]['booking_jobcard_filename']);
	    $this->My_CI->booking_model->save_vendor_email($details);

	    if ($this->My_CI->email->send()) {
//		$data['success'] = "Mail sent to Service Center successfully.";
//		$this->My_CI->session->set_flashdata('result', 'Mail sent to Service Center successfully');
		//Setting flag to 1, once mail is sent.
		$this->My_CI->booking_model->set_mail_to_vendor($booking_id);
	    } else {
//		$data['success'] = "Mail could not be sent, please try again.";
//		$this->My_CI->session->set_flashdata('result', 'Mail could not be sent, please try again');
	    }

//	    redirect(base_url() . 'employee/booking/view');
	} else {
//	    echo "Booking does not exist.";
	}
    }

    //This function sends reminder email to the assigned vendor
    function lib_send_reminder_mail_to_vendor($booking_id, $additional_note) {
	$getbooking = $this->My_CI->booking_model->getbooking($booking_id);

	if ($getbooking) {
	    $servicecentredetails = $this->My_CI->booking_model->selectservicecentre($booking_id);

	    //Find last mail sent for this booking id and append it in the bottom
	    $last_mail = $this->My_CI->booking_model->get_last_vendor_mail($booking_id);
	    if ($last_mail[0]['type'] == 'Booking') {
		//Send 1st Reminder Email
		$new_sub = $last_mail[0]['subject'] . " - Reminder-1";
		$type = "Reminder-1";
	    } else {
		//Send another Reminder Email
		$t = explode("-", $last_mail[0]['type']);
		$type = "Reminder-" . (intval($t[1]) + 1);
		$old_sub = $last_mail[0]['subject'];
		$substr = " - Reminder-X";
		$new_sub = substr($old_sub, 0, 0 - strlen($substr)) . " - " . $type;
	    }

	    //New message for this email
	    $salutation = "Dear " . $servicecentredetails[0]['primary_contact_name'];
	    $heading = "<br><br>" . $type;
	    $heading .= "<br><br>Please revert back on the current status of this booking.";

	    if ($additional_note != "") {
		$note = "<br><b>Note:</b> " . urldecode($additional_note) . "<br><br>";
	    } else {
		$note = "<br><br>";
	    }

	    $message = $salutation . $heading . $note . "Regards,<br><br>Devendra";

	    $message = $message . "<br><br>--------------------------------------------------<br><br>" .
		$last_mail[0]['body'];

	    $to = $servicecentredetails[0]['primary_contact_email'];
	    $owner = $servicecentredetails[0]['owner_email'];
	    $cc = ($owner . ', nits@247around.com, anuj@247around.com');
//	    $cc = $owner;

	    $this->My_CI->email->clear(TRUE);
	    $this->My_CI->email->from('booking@247around.com', '247around Team');
	    $this->My_CI->email->to($to);
	    $this->My_CI->email->cc($cc);
	    $this->My_CI->email->subject($new_sub);
	    $this->My_CI->email->message($message);

	    //Save email in database
	    $details = array("booking_id" => $booking_id, "subject" => $new_sub,
		"body" => $message, "type" => $type);
	    $this->My_CI->booking_model->save_vendor_email($details);

	    if ($this->My_CI->email->send()) {
//		$data['success'] = "Reminder mail sent to Service Center successfully.";
//		$this->My_CI->session->set_flashdata('email_result', 'Reminder mail sent to Service Center successfully.');
	    } else {
//		$data['success'] = "Reminder mail could not be sent, please try again.";
//		$this->My_CI->session->set_flashdata('email_result', 'Reminder mail could not be sent, please try again.');
	    }

//	    redirect(base_url() . 'employee/booking/view');
	} else {
//	    echo "Booking ID does not exist";
	}
    }

    function sendTransactionalSms($phone_number, $body) {

	//log_message ('info', "Entering: " . __METHOD__ . ": Phone num: " . $phone_number);

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

}
