<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//error_reporting(E_ALL);
//ini_set('display_errors', '1');
//ini_set('include_path', '/Applications/MAMP/htdocs/aroundlocalhost/system/libraries');
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

    public function get_partner_summary_table($partner_id) {
	$partner_summary_params = $this->partner_model->get_partner_summary_params($partner_id);

	$total_install_req = $partner_summary_params['total_install_req'];
	$today_install_req = $partner_summary_params['today_install_req'];
	$yday_install_req = $partner_summary_params['yday_install_req'];
	$month_install_req = $partner_summary_params['month_install_req'];

	$total_install_sched = $partner_summary_params['total_install_sched'];
	$today_install_sched = $partner_summary_params['today_install_sched'];
	$yday_install_sched = $partner_summary_params['yday_install_sched'];
	$month_install_sched = $partner_summary_params['month_install_sched'];

	$total_install_compl = $partner_summary_params['total_install_compl'];
	$today_install_compl = $partner_summary_params['today_install_compl'];
	$yday_install_compl = $partner_summary_params['yday_install_compl'];
	$month_install_compl = $partner_summary_params['month_install_compl'];

	$total_followup_pend = $partner_summary_params['total_followup_pend'];
	$today_followup_pend = $partner_summary_params['today_followup_pend'];
	$yday_followup_pend = $partner_summary_params['yday_followup_pend'];
	$month_followup_pend = $partner_summary_params['month_followup_pend'];

	$total_install_cancl = $partner_summary_params['total_install_cancl'];
	$today_install_cancl = $partner_summary_params['today_install_cancl'];
	$yday_install_cancl = $partner_summary_params['yday_install_cancl'];
	$month_install_cancl = $partner_summary_params['month_install_cancl'];

	$tat = $partner_summary_params['tat'];

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
		<td>Month</td>
		<td>$month_install_req</td>
		<td>$month_install_compl</td>
		<td>$month_install_sched</td>
		<td>$month_followup_pend</td>
		<td>$month_install_cancl</td>
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

    public function send_summary_mail_to_partners() {
	log_message('info', __FUNCTION__);

	$template = 'SD_Summary_Template-v2.xlsx';
	$templateDir = __DIR__ . "/excel-templates/";
	//print_r($templateDir);
	//set config for report
	$config = array(
	    'template' => $template,
	    'templateDir' => $templateDir
	);

	$where_get_partner = array('is_active' => '1');

	//Get all Active partners who has "is_reporting_mail" column 1
	$partners = $this->partner_model->getpartner_details($where_get_partner, '1');

	foreach ($partners as $p) {
	    //load template
	    $R = new PHPReport($config);

	    //Fetch partners' bookings
	    $leads = $this->partner_model->get_partner_leads_for_summary_email($p['id']);

	    $R->load(array(
		array(
		    'id' => 'bd',
		    'repeat' => true,
		    'data' => $leads,
		),
	    ));

	    //Get populated XLS with data
	    $output_file = "247around-Services-Consolidated-Data - " . date('d-M-Y') . ".xlsx";
	    //for xlsx: excel, for xls: excel2003
	    $R->render('excel', $output_file);
	    //Send report via email
	    $this->email->clear(TRUE);
	    $this->email->from('booking@247around.com', '247around Team');
	    $this->email->to($p['summary_email_to']);
	    $this->email->cc($p['summary_email_cc']);
	    $this->email->bcc($p['summary_email_bcc']);

	    $this->email->subject("247around Services Report - " . date('d-M-Y'));
	    $summary_table = $this->get_partner_summary_table($p['id']);

	    $message = "Dear Partner,<br/><br/>";
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
		log_message('info', __METHOD__ . ": Mail sent successfully for Partner: " . $p['public_name']);
	    } else {
		log_message('info', __METHOD__ . ": Mail could not be sent for Partner: " . $p['public_name']);
	    }
            
	    //Upload Excel to AWS/FTP
	    $bucket = 'bookings-collateral';
	    $directory_xls = "summary-excels/" . $output_file;
	    $this->s3->putObjectFile(realpath($output_file), $bucket, $directory_xls, S3::ACL_PRIVATE);
	    //Delete this file
	    exec("rm -rf " . escapeshellarg($output_file));
	}

	exit(0);
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
	    . "soumendra.choudhury@snapdeal.com, shivalini.verma@snapdeal.com,"
	    . "rameen.khan@snapdeal.com, sunil.gurubhagwatla@snapdeal.com,"
	    . "ashish.dudeja@snapdeal.com, sanju.khatri@snapdeal.com, harjinder.singh01@snapdeal.com"
	    . "abhinaw.sinha@snapdeal.com, "
	    . "nits@247around.com, anuj@247around.com";
	$this->email->cc($cc);
	$this->email->bcc("anuj.aggarwal@gmail.com");

	$this->email->subject("247around Installation Report - " . date('d-M-Y'));
	$summary_table = $this->get_sd_summary_table();

	$message = "Dear Partner,<br/><br/>";
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
        $data = $this->reporting_utils->get_report_data();
        $today_ratings = $this->booking_model->get_completed_booking_details();
        $html = '
                    <html xmlns="http://www.w3.org/1999/xhtml">
                      <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                      </head>
                      <body>
                      <p><b>Total Ratings in  '.date('M'). ' : '.$today_ratings['ratings']->ratings.'</b></p>
                      <p><b>Total Bookings Completed in '.date('M'). ' :  '.$today_ratings['bookings']->bookings.'</b></p>
                      <p><b>Total Bookings Completed in '.date("M", strtotime("-1 months")).' :  '.$today_ratings['bookings_previous']->bookings.'</b></p>
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
        foreach ($data['data1'] as $key => $value) {
            $html .= "<tr style='padding: 8px;line-height: 1.42857143;vertical-align: top; border-top: 1px solid #ddd;border: 1px solid #ddd;'>"
                    . "<td style='text-align: center;border: 1px solid #ddd;'>" . $value['source'] . "</td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['total'] . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['scheduled'] . " </td></td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['queries'] . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['completed'] . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['cancelled'] . " </td></tr>";
            $total_today += $value['total'];
            $total_today_scheduled += $value['scheduled'];
            $total_today_completed += $value['completed'];
            $total_total_cancelled += $value['cancelled'];
            $total_today_queries += $value['queries'];

            $html .= "</tr>";
        }

        $html .="<tr style='padding: 8px;line-height: 1.42857143;vertical-align: top; border-top: 1px solid #ddd;border: 1px solid #ddd;'>"
                . "<td style='text-align: center;border: 1px solid #ddd;'>Total</td>"
                . "<td style='text-align: center;border: 1px solid #ddd;'>" . $total_today . " </td>"
                . "<td style='text-align: center;border: 1px solid #ddd;'>" . $total_today_scheduled . " </td></td>"
                . "<td style='text-align: center;border: 1px solid #ddd;'>" . $total_today_queries . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $total_today_completed . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $total_total_cancelled . " </td></tr>";


        $html .= '</tbody>
                          </table>
                        </div>';

        $html .= ' <p style="margin-top: 30px;" >MTD Booking Summary:</p>
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
        foreach ($data['data2'] as $key => $value) {
            $html .= "<tr style='padding: 8px;line-height: 1.42857143;vertical-align: top; border-top: 1px solid #ddd;border: 1px solid #ddd;'>"
                    . "<td style='text-align: center;border: 1px solid #ddd;'>" . $value['source'] . "</td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['total'] . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['scheduled'] . " </td></td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['queries'] . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['completed'] . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['cancelled'] . " </td></tr>";

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

    /**
     * @desc: this method set header for summary table
     * @return string
     */
    function set_mail_table_head() {
        $html = ' <html xmlns="http://www.w3.org/1999/xhtml"><head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>
                      <body style="margin-top:40px;"><div style="margin-top: 30px;">
                          <table style="width: 100%;max-width: 100%;margin-bottom: 20px;border: 1px solid #ddd;">
                            <thead><tr style="padding: 8px;line-height: 1.42857143;vertical-align: top; border-top: 1px solid #ddd">
                                <th style="border: 1px solid #ddd;">Source</th>
                                <th style="border: 1px solid #ddd;">Request Received</th>
                                <th style="border: 1px solid #ddd;">FollowUp</th>
                                <th style="border: 1px solid #ddd;">Scheduled</th>
                                <th style="border: 1px solid #ddd;">Completed</th>
                                <th style="border: 1px solid #ddd;">Cancel</th>
                              </tr>
                            </thead>
                            <tbody >';
        return $html;
    }
    /**
     * @desc: This method fill the data into summary table
     * @param type $data
     * @return string
     */
    function set_data_in_table($data){
       $html = "";
        for($i =0; $i <3;$i++ ){
        $total_booking = 0; $total_followup = 0; $total_scheduled = 0;
        $total_completed = 0; $total_cancelled = 0;
        $html .= $this->set_mail_table_head();
        foreach ($data['data'.$i] as $value){

            $total_booking += $value['total_booking'];
            $total_followup += $value['queries'];
            $total_scheduled += $value['scheduled']; $total_completed += $value['completed'];
            $total_cancelled += $value['cancelled'];

            $html .= "<tr style='padding: 8px;line-height: 1.42857143;vertical-align: top; border-top: 1px solid #ddd;border: 1px solid #ddd;'>"
                    . " <td style='text-align: center;border: 1px solid #ddd;'>" . $value['partner_source'] . "</td>"
                    . "<td style='text-align: center;border: 1px solid #ddd;'>" . $value['total_booking'] . "</td>"
                    . "<td style='text-align: center;border: 1px solid #ddd;'>" . $value['queries'] . " </td>"
                    . "<td style='text-align: center;border: 1px solid #ddd;'>" . $value['scheduled'] . " </td>"
                    . "<td style='text-align: center;border: 1px solid #ddd;'>" . $value['completed'] . " </td>"
                    . "<td style='text-align: center;border: 1px solid #ddd;'>" . $value['cancelled'] . " </td>"
                    . "</td></tr>";
        }

        $html .="<tr style='padding: 8px;line-height: 1.42857143;vertical-align: top; border-top: 1px solid #ddd;border: 1px solid #ddd;'>"
                . "<td  style='text-align: center;border: 1px solid #ddd;' >Total</td>"
                ."<td style='text-align: center;border: 1px solid #ddd;'>" . $total_booking . "</td>"
                . "<td style='text-align: center;border: 1px solid #ddd;'>" . $total_followup . "</td>"
                . "<td style='text-align: center;border: 1px solid #ddd;'>" . $total_scheduled . "</td>"
                . "<td style='text-align: center;border: 1px solid #ddd;'>" . $total_completed . "</td>"
                . "<td style='text-align: center;border: 1px solid #ddd;'>" . $total_cancelled . "</td>";

        $html .= "</body></html>";
        if($i ==0){
            $html .= '<p>Today Booking Summary</p>';
        } else if($i==1){
            $html .= '<p>Month Booking Summary</p>';
        } else {
            $html .= '<p>Overall Booking Summary</p>';
        }

        }

        return $html;
    }

}
