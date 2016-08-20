<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('include_path', '/Applications/MAMP/htdocs/aroundlocalhost/system/libraries');
//ini_set('include_path', '/var/www/aroundhomzapp.com/public_html/system/libraries');
//require_once('simple_html_dom.php');

class BookingSummary extends CI_Controller {

    function __Construct() {
	parent::__Construct();

	$this->load->model('reporting_utils');
	$this->load->model('justdial_bookings');
	$this->load->model('user_model');
	$this->load->model('booking_model');
	$this->load->model('partner_model');

	$this->load->library('PHPReport');
	$this->load->library('notify');
	$this->load->library('email');
	$this->load->library('s3');
    }

    public function test($a = "a", $b = "b") {
	log_message('info', __FUNCTION__ . ": looks like things are working");
	echo "looks like things are working" . PHP_EOL;

	echo "A = " . $a . PHP_EOL;
	echo "B = " . $b . PHP_EOL;
    }

    public function get_pending_bookings() {
	//log_message('info', __FUNCTION__);

	$template = 'BookingSummary_Template-v7.xls';
	//set absolute path to directory with template files
	$templateDir = __DIR__ . "/";

	//set config for report
	$config = array(
	    'template' => $template,
	    'templateDir' => $templateDir
	);

	//load template
	$R = new PHPReport($config);

	//fetch pending bookings
	$pending_bookings = $this->reporting_utils->get_pending_bookings();
	$count = count($pending_bookings);
	//log_message('info', "Count: " . $count);

	if ($count > 0) {
	    //Get num of pending bookings for each vendor
	    $sc_pending_bookings = $this->reporting_utils->get_num_pending_bookings_for_all_sc();

	    $R->load(array(
		array(
		    'id' => 'meta',
		    'data' => array('date' => date('Y-m-d'), 'count' => $count),
		    'format' => array(
			'date' => array('datetime' => 'd/M/Y')
		    )
		),
		array(
		    'id' => 'booking',
		    'repeat' => true,
		    'data' => $pending_bookings,
		    //'minRows' => 2,
		    'format' => array(
			'create_date' => array('datetime' => 'd/M/Y'),
			'total_price' => array('number' => array('prefix' => 'Rs. ')),
		    )
		),
		array(
		    'id' => 'sc',
		    'repeat' => true,
		    'data' => $sc_pending_bookings,
		),
		)
	    );

	    //Get populated XLS with data
	    $output_file = "BookingSummary-" . date('d-M-Y') . ".xls";
	    $R->render('excel2003', $output_file);

	    //log_message('info', "Report generated with $count records");
	    //Send report via email
	    $this->email->from('booking@247around.com', '247around Team');
	    $this->email->to("nits@247around.com, anuj@247around.com, booking@247around.com, sales@247around.com, suresh@247around.com");
	    //$this->email->to("anuj.aggarwal@gmail.com");

	    $this->email->subject("Booking Summary: " . date('Y-m-d'));
	    $this->email->message("Bookings pending as of today: " . $count . "<br/>");
	    $this->email->attach($output_file, 'attachment');

	    if ($this->email->send()) {
		//log_message('info', __METHOD__ . ": Mail sent successfully");
	    } else {
		log_message('error', __METHOD__ . ": Mail could not be sent");
	    }

	    //Upload Excel to AWS
	    $bucket = 'bookings-collateral';
	    $directory_xls = "summary-excels/" . $output_file;
	    $this->s3->putObjectFile(realpath($output_file), $bucket, $directory_xls, S3::ACL_PRIVATE);

	    //Delete this file
	    exec("rm -rf " . $output_file);
	}

	exit(0);
    }

    public function get_pending_bookings_for_vendors() {
	//log_message('info', __FUNCTION__);

	$file_names = array();

	$template = 'BookingSummary_Template-v5.xls';
	//set absolute path to directory with template files
	$templateDir = __DIR__ . "/";

	//set config for report
	$config = array(
	    'template' => $template,
	    'templateDir' => $templateDir
	);

	//fetch all active vendors
	$service_centers = $this->reporting_utils->find_service_centers();
	//echo print_r($service_centers, true);

	foreach ($service_centers as $sc) {
	    //log_message('info', "fetch pending bookings for service center id: " . $sc['id']);
	    $pending_bookings = $this->reporting_utils->get_pending_bookings_by_sc($sc['id']);
	    $count = count($pending_bookings);
	    //log_message('info', "Count: " . $count);

	    if ($count > 0) {
		//load template
		$R = new PHPReport($config);

		//Find unit details first for all bookings
		$unit_details = $this->reporting_utils->get_all_unit_details();
		//log_message('info', "Units fetched: " . count($unit_details));

		$R->load(array(
		    array(
			'id' => 'meta',
			'data' => array('date' => date('Y-m-d'), 'count' => $count),
			'format' => array(
			    'date' => array('datetime' => 'd/M/Y')
			)
		    ),
		    array(
			'id' => 'booking',
			'repeat' => true,
			'data' => $pending_bookings,
			//'minRows' => 2,
			'format' => array(
			    'create_date' => array('datetime' => 'd/M/Y'),
			    'total_price' => array('number' => array('prefix' => 'Rs. ')),
			)
		    ),
		    )
		);

		//Get populated XLS with data
		$output_file = "BookingSummary-" . $sc['name'] . "-" . date('d-M-Y') . ".xls";
		$R->render('excel2003', $output_file);

		//log_message('info', "Report generated with $count records");
		//Send report via email
		$this->email->clear(TRUE);
		$this->email->from('booking@247around.com', '247around Team');
		$this->email->to($sc['primary_contact_email']);
		$cc = $sc['owner_email'];
		$this->email->cc($cc);
		//$this->email->bcc("anuj@247around.com");

		$subject = "247Around - " . $sc['name'] . " - Pending Bookings: " . date('d-M-Y');
		$this->email->subject($subject);

		$message = "Dear " . $sc['primary_contact_name'] . ",<br/><br/>";
		$message .= "Please find attached excel sheet containing " . $count . " pending bookings till date. ";
		$message .= "Kindly update the status in the excel and revert ASAP.";
		$message .= "<br><br>With Regards,
                        <br>Devendra - 247Around - 9555118612
                        <br>247Around is part of Businessworld Startup Accelerator & Google Bootcamp 2015
                        <br>Follow us on Facebook: www.facebook.com/247Around | Website:
                        <br>www.247Around.com
                        <br>You will Love our Video Advertisements:
                        <br>https://www.youtube.com/watch?v=y8sBWDPHAhI
                        <br>Playstore - 247Around -
                        <br>https://play.google.com/store/apps/details?id=com.handymanapp";

		$this->email->message($message);
		$this->email->attach($output_file, 'attachment');

		//Save filenames to delete later on
		array_push($file_names, $output_file);

		if ($this->email->send()) {
		    //log_message('info', __METHOD__ . ": Mail sent successfully");
		} else {
		    log_message('error', __METHOD__ . ": Mail could not be sent");
		}

		//Upload Excel files to AWS
		$bucket = 'bookings-collateral';
		$directory_xls = "summary-excels/" . $output_file;
		$this->s3->putObjectFile(realpath($output_file), $bucket, $directory_xls, S3::ACL_PRIVATE);
	    }
	}

	//Delete XLS files now
	foreach ($file_names as $file_name)
	    exec("rm -rf " . escapeshellarg($file_name));

	exit(0);
    }

    function get_new_bookings_from_justdial() {
	$html = new simple_html_dom();

	//open inbox
	$imap = imap_open("{mail.247around.com:143/imap}INBOX", "sales@247around.com", "vikassales247")
	    or die("can't connect: " . imap_last_error());

	//read messages which are unflagged and have keyword in subject
	$emails = imap_search($imap, 'UNFLAGGED SUBJECT "enquiry for you at"');
	$c = count($emails);
	echo 'Count of JD emails: ' . $c . PHP_EOL;

	$c = 5;

	for ($i = 0; $i < $c; $i++) {
	    $mail = $emails[$i];

	    $headerInfo = imap_headerinfo($imap, $mail);

	    //if message is not a lead from justdial
	    if (strstr($headerInfo->subject, "from VN Number")) {
		echo PHP_EOL . "Subject contains from VN Number, leave this message......." . PHP_EOL;
	    } else {
		echo PHP_EOL . "Process this message......." . PHP_EOL;

		$to_be_cancelled = 0;

		$msg_id = $headerInfo->message_id;
		$msg_date = $headerInfo->date;
		echo "MSG Date Time: " . $msg_date . PHP_EOL;

		$body = imap_body($imap, $mail, FT_PEEK);

		//get the required table from the message body
		$html_str = strstr($body, "<table");
		$html_str = strstr($html_str, "table>", true);
		$html_str .= "table>";
		$html_str = strstr($html_str, "<table width");

		// Load HTML from a string
		$html->load($html_str);
		$tds = $html->find('td');
		//echo 'Count TDs: ' . count($tds) . PHP_EOL;

		$row1 = explode(" from ", $tds[2]->__get('plaintext'));
		$name = $row1[0];
		echo "Name: " . $name . PHP_EOL;

		$place = "";
		if (count($row1) >= 2) {
		    $place = $row1[1];
		    echo "Place: " . $place . PHP_EOL;
		}

		$row2 = explode("-", $tds[4]->__get('plaintext'));
		$appliance = $row2[0];
		echo "Appliance: " . $appliance . PHP_EOL;

		$brand = "";
		if (count($row2) >= 2) {
		    $brand = $row2[1];
		    echo "Brand: " . $brand . PHP_EOL;
		}

		$datetime = $tds[6]->__get('plaintext');
		echo "Date Time: " . $datetime . PHP_EOL;

		//check if there are 2 or more phone nos
		$phones = explode(",", $tds[12]->__get('plaintext'));
		//Remove +91
		$mobile = substr($phones[0], -10);
		echo "Phone: " . $mobile . PHP_EOL;

		$alt_phone = "";
		if (count($phones) >= 2) {
		    $alt_phone = $phones[1];
		    echo "Alt No: " . $alt_phone . PHP_EOL;
		}

		$email = "";
		if (count($tds) > 13) {
		    $email = $tds[14]->__get('plaintext');
		    echo "Email: " . $email . PHP_EOL;
		}

		$jd_booking = array(
		    "msg_id" => $msg_id,
		    "name" => $name,
		    "place" => $place,
		    "appliance" => $appliance,
		    "brand" => $brand,
		    "lead_date_time" => $datetime,
		    "email" => $email,
		    "mobile" => $mobile,
		    "alt_phone" => $alt_phone,
		    "msg_date" => $msg_date
		);

		//insert object in justdial_booking table
		$this->justdial_bookings->insert_booking($jd_booking);

		//create new query in bookings, appliance_details and unit_details tables
		//for this, first check whether user exists or not
		//if it does not exist, create user first
		$output = $this->user_model->search_user($mobile);
		if (empty($output)) {
		    echo 'User does not exist' . PHP_EOL;
		    $user['name'] = $name;
		    $user['phone_number'] = $mobile;
		    $user['user_email'] = $email;
		    $user['home_address'] = $place;

		    $booking['user_id'] = $this->user_model->add_user($user);

		    $to_be_cancelled = 1;
		    //echo print_r($booking['user_id'], true);
		} else {
		    echo 'User exists' . PHP_EOL;
		    $booking['user_id'] = $output[0]['user_id'];
		    //echo print_r($booking['user_id'], true);
		    //Find bookings for this user
		    $num_bookings = $this->booking_model->getBookingCountByUser($booking['user_id']);
		    if ($num_bookings > 0) {
			echo $num_bookings . " booking already inserted, leave this user, break ...." . PHP_EOL;

			continue;
		    } else {
			echo "No booking found for this user, insert new query" . PHP_EOL;
		    }
		}

		//Add sample appliances for this user
		$count = $this->booking_model->getApplianceCountByUser($booking['user_id']);
		//Add sample appliances if user has < 5 appliances in wallet
		if ($count < 5) {
		    $this->booking_model->addSampleAppliances($booking['user_id'], 5 - intval($count));
		}

		//Add this query now
		if (strstr($appliance, "LED TV")) {
		    $appliance = 'Television';
		}
		$booking['service_id'] = $this->booking_model->getServiceId($appliance);
		//echo "Service ID: " . $booking['service_id'] . PHP_EOL;

		$yy = date("y");
		$mm = date("m");
		$dd = date("d");
		$booking['booking_id'] = str_pad($booking['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
		$booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($booking['user_id'])) + 1);

		//Add source
		$booking['source'] = "SJ";
		$booking['partner_id'] = $this->partner_model->get_partner_id_from_booking_source_code($booking['source']);
		$booking['booking_id'] = "Q-" . $booking['source'] . "-" . $booking['booking_id'];

		$booking['quantity'] = '1';
		$booking['appliance_brand1'] = $brand;
		$booking['appliance_category1'] = 'TV-LED';
		$booking['appliance_capacity1'] = '';
		$booking['appliance_tags1'] = '';
		$booking['purchase_year1'] = '';
		$booking['model_number1'] = '';
		$booking['items_selected1'] = '';
		$booking['total_price1'] = '';
		$booking['potential_value'] = '500';

		$appliance_id = $this->booking_model->addappliancedetails($booking);
		$this->booking_model->addunitdetails($booking);

		$booking['current_status'] = "FollowUp";
		$booking['type'] = "Query";
		$booking['booking_primary_contact_no'] = $mobile;

		$booking['booking_alternate_contact_no'] = $alt_phone;
		$booking['booking_date'] = '';
		$booking['booking_timeslot'] = '';
		$booking['booking_address'] = $place;
		$booking['booking_pincode'] = '';
		$booking['amount_due'] = '';
		$booking['booking_remarks'] = '';
		$booking['query_remarks'] = '';

		//Insert query
		$this->booking_model->addbooking($booking, $appliance_id[0]['id']);

		//Cancel this query as well. All
		//potential oppurtunities have been logged already.
		$data['closing_remarks'] = "";
		$data['cancellation_reason'] = "Your problem is resolved.";
		$data['update_date'] = date("Y-m-d H:i:s");
		$data['current_status'] = "Cancelled";
		$insertData = $this->booking_model->cancel_booking($booking['booking_id'], $data);
		echo $booking['booking_id'] . " booking cancelled" . PHP_EOL;

		//mark this message as flagged
		imap_setflag_full($imap, $mail, "\\FLAGGED", ST_UID);
		echo "Mail marked as FLAGGED" . PHP_EOL;

		//exit(0);
	    }
	}

	imap_close($imap);
    }

    public function get_sd_summary_table() {
	$snapdeal_summary_params = $this->reporting_utils->get_snapdeal_summary_params_new();

	$total_install_req = $snapdeal_summary_params['total_install_req'];
	$today_install_req = $snapdeal_summary_params['today_install_req'];
	$yday_install_req = $snapdeal_summary_params['yday_install_req'];

	$total_install_sched = $snapdeal_summary_params['total_install_sched'];
	$today_install_sched = $snapdeal_summary_params['today_install_sched'];
	$yday_install_sched = $snapdeal_summary_params['yday_install_sched'];

	$total_install_compl = $snapdeal_summary_params['total_install_compl'];
	$today_install_compl = $snapdeal_summary_params['today_install_compl'];
	$yday_install_compl = $snapdeal_summary_params['yday_install_compl'];

	$total_followup_pend = $snapdeal_summary_params['total_followup_pend'];
	$today_followup_pend = $snapdeal_summary_params['today_followup_pend'];
	$yday_followup_pend = $snapdeal_summary_params['yday_followup_pend'];

	$total_install_cancl = $snapdeal_summary_params['total_install_cancl'];
	$today_install_cancl = $snapdeal_summary_params['today_install_cancl'];
	$yday_install_cancl = $snapdeal_summary_params['yday_install_cancl'];

	$tat = $snapdeal_summary_params['tat'];

	$message = <<<EOD
	<table border="1">
	    <tr>
		<td>Date</td>
		<td>Requests Received</td>
		<td>Requests Completed</td>
		<td>Requests Scheduled</td>
		<td>To be Followed Up</td>
		<td>Requests Cancelled</td>
		<td>TAT (%)</td>
	    </tr>

	    <tr>
		<td>Yesterday</td>
		<td>$yday_install_req</td>
		<td>$yday_install_compl</td>
		<td>$yday_install_sched</td>
		<td>$yday_followup_pend</td>
		<td>$yday_install_cancl</td>
		<td>NA</td>
	    </tr>

	    <tr>
		<td>Today</td>
		<td>$today_install_req</td>
		<td>$today_install_compl</td>
		<td>$today_install_sched</td>
		<td>$today_followup_pend</td>
		<td>$today_install_cancl</td>
		<td>NA</td>
	    </tr>

	    <tr>
		<td>Total</td>
		<td>$total_install_req</td>
		<td>$total_install_compl</td>
		<td>$total_install_sched</td>
		<td>$total_followup_pend</td>
		<td>$total_install_cancl</td>
		<td>$tat</td>
	    </tr>

	</table>
EOD;

	return $message;
    }

    public function send_summary_mail_to_snapdeal() {
	log_message('info', __FUNCTION__);

	$template = 'SD_Summary_Template-v2.xlsx';
	$templateDir = __DIR__ . "/excel-templates/";

	//set config for report
	$config = array(
	    'template' => $template,
	    'templateDir' => $templateDir
	);

	//load template
	$R = new PHPReport($config);

	//Fetch SD bookings
	$leads = $this->reporting_utils->get_all_sd_leads();

	$R->load(array(
	    array(
		'id' => 'bd',
		'repeat' => true,
		'data' => $leads,
	    ),
	));

	//Get populated XLS with data
	$output_file = "247around Installation Consolidated Data - " . date('d-M-Y') . ".xlsx";
	//for xlsx: excel, for xls: excel2003
	$R->render('excel', $output_file);
	//Send report via email
	$this->email->from('booking@247around.com', '247around Team');
	$this->email->to("alok.singh@snapdeal.com");
	$cc = "dhananjay.shashidharan@snapdeal.com,"
	    . "soumendra.choudhury@snapdeal.com, somya.kaila@snapdeal.com, "
	    . "shivalini.verma@snapdeal.com, abhinaw.sinha@snapdeal.com"
	    . "nits@247around.com, anuj@247around.com";
	$this->email->cc($cc);
	$this->email->bcc("anuj.aggarwal@gmail.com");

	$this->email->subject("247around Installation Report - " . date('d-M-Y'));
	$summary_table = $this->get_sd_summary_table();

	$message = "Dear Alok,<br/><br/>";
	$message .= "Please find updated summary table below.<br/><br/>";
	$message .= $summary_table;
	$message .= "<br><br>Best Regards,
                        <br>247around Team
                        <br><br>247around is part of Businessworld Startup Accelerator & Google Bootcamp 2015
                        <br>Follow us on Facebook: www.facebook.com/247around | Website: www.247around.com
                        <br>Playstore - 247around -
                        <br>https://play.google.com/store/apps/details?id=com.handymanapp";

	$this->email->message($message);
	$this->email->attach($output_file, 'attachment');
	if ($this->email->send()) {
	    //log_message('info', __METHOD__ . ": Mail sent successfully");
	} else {
	    log_message('error', __METHOD__ . ": Mail could not be sent");
	}
	//Upload Excel to AWS/FTP
	$bucket = 'bookings-collateral';
	$directory_xls = "summary-excels/" . $output_file;
	$this->s3->putObjectFile(realpath($output_file), $bucket, $directory_xls, S3::ACL_PRIVATE);

	//Delete this file
	exec("rm -rf " . escapeshellarg($output_file));

	exit(0);
    }

    function booking_report() {
	$data = $this->reporting_utils->booking_report();
	$html = '
                    <html xmlns="http://www.w3.org/1999/xhtml">
                      <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                      </head>
                      <body>
                      <p>Today Booking Summary:</p>
                        <div style="margin-top: 30px;">
                          <table style="width: 100%;max-width: 100%;margin-bottom: 20px;border: 1px solid #ddd;">
                            <thead>
                              <tr style="padding: 8px;line-height: 1.42857143;vertical-align: top; border-top: 1px solid #ddd">

                                <th style="border: 1px solid #ddd;">Source</th>
                                <th style="border: 1px solid #ddd;">Total</th>
                                <th style="border: 1px solid #ddd;">Scheduled</th>
                                <th style="border: 1px solid #ddd;">Follow Up</th>
                                <th style="border: 1px solid #ddd;">Completed</th>
                                <th style="border: 1px solid #ddd;">Cancel</th>
                              </tr>
                            </thead>
                            <tbody >';
	$total_today = 0;
	$total_today_scheduled = 0;
	$total_today_queries = 0;
	$total_today_completed = 0;
	$total_total_cancelled = 0;
	foreach ($data['data2'] as $key => $value) {
	    $html .= "<tr style='padding: 8px;line-height: 1.42857143;vertical-align: top; border-top: 1px solid #ddd;border: 1px solid #ddd;'><td style='text-align: center;border: 1px solid #ddd;'>" . $value['source'] . "</td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['total'] . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['scheduled'] . " </td></td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['queries'] . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['completed'] . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['cancelled'] . " </td></tr>";
	    $total_today += $value['total'];
	    $total_today_scheduled += $value['scheduled'];
	    $total_today_completed += $value['completed'];
	    $total_total_cancelled += $value['cancelled'];
	    $total_today_queries += $value['queries'];

	    $html .= "</tr>";
	}

	$html .="<tr style='padding: 8px;line-height: 1.42857143;vertical-align: top; border-top: 1px solid #ddd;border: 1px solid #ddd;'><td style='text-align: center;border: 1px solid #ddd;'>Total</td><td style='text-align: center;border: 1px solid #ddd;'>" . $total_today . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $total_today_scheduled . " </td></td><td style='text-align: center;border: 1px solid #ddd;'>" . $total_today_queries . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $total_today_completed . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $total_total_cancelled . " </td></tr>";


	$html .= '</tbody>
                          </table>
                        </div>';

	$html .= ' <p style="margin-top: 30px;" >Overall Booking Summary:</p>
                        <div style="margin-top: 30px;" >
                          <table style="width: 100%;max-width: 100%;margin-bottom: 20px;border: 1px solid #ddd;">
                            <thead>
                              <tr style="padding: 8px;line-height: 1.42857143;vertical-align: top; border-top: 1px solid #ddd">

                                <th style="border: 1px solid #ddd;">Source</th>
                                <th style="border: 1px solid #ddd;">Total</th>
                                <th style="border: 1px solid #ddd;">Scheduled</th>
                                <th style="border: 1px solid #ddd;">Follow Up</th>
                                <th style="border: 1px solid #ddd;">Completed</th>
                                <th style="border: 1px solid #ddd;">Cancel</th>
                              </tr>
                            </thead>
                            <tbody >';
	$total = 0;
	$scheduled = 0;
	$completed = 0;
	$cancelled = 0;
	$queries = 0;
	foreach ($data['data1'] as $key => $value) {
	    $html .= "<tr style='padding: 8px;line-height: 1.42857143;vertical-align: top; border-top: 1px solid #ddd;border: 1px solid #ddd;'><td style='text-align: center;border: 1px solid #ddd;'>" . $value['source'] . "</td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['total'] . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['scheduled'] . " </td></td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['queries'] . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['completed'] . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['cancelled'] . " </td></tr>";

	    $total += $value['total'];
	    $scheduled += $value['scheduled'];
	    $completed += $value['completed'];
	    $cancelled += $value['cancelled'];
	    $queries += $value['queries'];
	}

	$html .="<tr style='padding: 8px;line-height: 1.42857143;vertical-align: top; border-top: 1px solid #ddd;border: 1px solid #ddd;'><td style='text-align: center;border: 1px solid #ddd;'>Total</td><td style='text-align: center;border: 1px solid #ddd;'>" . $total . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $scheduled . " </td></td><td style='text-align: center;border: 1px solid #ddd;'>" . $queries . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $completed . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $cancelled . " </td></tr>";


	$html .= '</tbody>
                          </table>
                        </div>';


	$html .= '</body>
                    </html>';

	$to = "anuj@247around.com, nits@247around.com";

	$this->notify->sendEmail("booking@247around.com", $to, "", "", "Booking Summary", $html, "");
    }

}
