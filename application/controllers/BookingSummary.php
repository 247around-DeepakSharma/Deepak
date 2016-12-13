<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

error_reporting(E_ALL);
ini_set('display_errors', '1');

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 36000);

//ini_set('include_path', '/Applications/MAMP/htdocs/aroundlocalhost/system/libraries');
//ini_set('include_path', '/var/www/aroundhomzapp.com/public_html/system/libraries');
//require_once('simple_html_dom.php');

class BookingSummary extends CI_Controller {

    function __Construct() {
        parent::__Construct();

        $this->load->model('reporting_utils');
        $this->load->model('justdial_bookings');
        $this->load->model('user_model');
        $this->load->model('reporting_utils');
        $this->load->model('booking_model');
        $this->load->model('partner_model');

        $this->load->library('PHPReport');
        $this->load->library('notify');
        $this->load->library('email');
        $this->load->library('s3');
        $this->load->library('booking_utilities');
    }

    public function test($a = "a", $b = "b") {
        log_message('info', __FUNCTION__ . ": looks like things are working");
        echo "looks like things are working" . PHP_EOL;

        echo "A = " . $a . PHP_EOL;
        echo "B = " . $b . PHP_EOL;
    }

    public function get_pending_bookings($mail_flag) {
        log_message('info', __FUNCTION__ . ' => Entering, Mail flag: ' . $mail_flag);

        $template = 'BookingSummary_Template-v7.xls';
        //set absolute path to directory with template files
        $templateDir =  __DIR__ . "/../excel-templates/";

        //set config for report
        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );

        //load template
        $R = new PHPReport($config);

        $user_group = $this->session->userdata('user_group');
        
        //Checking function is called from CRON or from System Manually
        if (!empty($user_group)) {
            //Function is being called manually

            $id = $this->session->userdata('id');
            $sf_list = $this->vendor_model->get_employee_relation($id);
            if (!empty($sf_list)) {
                $sf_list = $sf_list[0]['service_centres_id'];
            }

            //Fetching pending bookings
            $pending_bookings = $this->reporting_utils->get_pending_bookings($sf_list);
            $count = count($pending_bookings);
            
            if ($count > 0) {
                //Get num of pending bookings for each vendor
                $sc_pending_bookings = $this->reporting_utils->get_num_pending_bookings_for_all_sc($sf_list);

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
                $output_file = "/tmp/BookingSummary-" . date('d-M-Y') . ".xls";
                $R->render('excel2003', $output_file);
                //Downloading of Excel generated
                if (file_exists($output_file)) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . basename($output_file) . '"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($output_file));
                    readfile($output_file);
                    exit;
                }
            }
        } else {
            //Function is being called from CRON
            
            //Getting list of RM details
            $rms = $this->employee_model->get_rm_details();
            foreach($rms as $value){
                
                $sf_list = $this->vendor_model->get_employee_relation($value['id'])[0]['service_centres_id'];
                $to = $value['official_email'];
                
                //Fetching pending bookings
                $pending_bookings = $this->reporting_utils->get_pending_bookings($sf_list);
                $count = count($pending_bookings);
        //log_message('info', "Count: " . $count);

        if ($count > 0) {
            //Get num of pending bookings for each vendor
                    $sc_pending_bookings = $this->reporting_utils->get_num_pending_bookings_for_all_sc($sf_list);

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
            $output_file = "/tmp/BookingSummary-" . date('d-M-Y') . ".xls";
            $R->render('excel2003', $output_file);
            
            if($mail_flag){

            //log_message('info', "Report generated with $count records");
            //Send report via email
            $this->email->from('booking@247around.com', '247around Team');
                        $this->email->to($to);

            $this->email->subject("Booking Summary: " . date('Y-m-d H:i:s'));
            $this->email->message("Bookings pending as of today: " . $count . "<br/>");
            $this->email->attach($output_file, 'attachment');

            if ($this->email->send()) {
                log_message('info', __METHOD__ . ": Mail sent successfully");
            } else {
                log_message('info', __METHOD__ . ": Mail could not be sent");
            }

            //Upload Excel to AWS
            $bucket = 'bookings-collateral';
            $directory_xls = "summary-excels/" . $output_file;
            $this->s3->putObjectFile(realpath($output_file), $bucket, $directory_xls, S3::ACL_PRIVATE);
            
            //Delete this file
            exec("rm -rf " . $output_file, $out, $return);
            // Return will return non-zero upon an error
            
            if(!$return){
                // exec() has been executed sucessfully
                // Inserting values in scheduler tasks log
                $this->reporting_utils->insert_scheduler_tasks_log(__FUNCTION__);
                //Logging
                log_message('info',__FUNCTION__.' Executed Sucessfully '.$output_file);
                
            }
            
            } else {
                if (file_exists($output_file)) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="'.basename($output_file).'"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($output_file));
                    readfile($output_file);
                    exit;
                }
            }
        }
            }
        }


        log_message('info', __FUNCTION__ . ' => Exiting');
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
        log_message('info', __FUNCTION__ . ' => Fetched active partners');

	foreach ($partners as $p) {
	    //load template
	    $R = new PHPReport($config);

	    //Fetch partners' bookings
	    $leads = $this->partner_model->get_partner_leads_for_summary_email($p['id']);
            log_message('info', __FUNCTION__ . ' => Fetched partner bookings');
            // Check Other string exist in the Cancellation reason. 
            // If exist then replace cancellation_reason with other
            foreach ($leads as $key => $value) {
                if($value['current_status'] != "Cancelled"){
                    
                    $leads[$key]['cancellation_reason'] = $value['current_status'];
                             
                } else if (stristr($value['cancellation_reason'], "Other :")){
                  
                    $leads[$key]['cancellation_reason'] = "Other";
                    
                }
            }
            
	    $R->load(array(
		array(
		    'id' => 'bd',
		    'repeat' => true,
		    'data' => $leads,
		),
	    ));

	    //Get populated XLS with data
	    $output_file = "/tmp/247around-Services-Consolidated-Data - " . date('d-M-Y') . ".xlsx";
	    //for xlsx: excel, for xls: excel2003
	    $R->render('excel', $output_file);
	    log_message('info', __FUNCTION__ . ' => Rendered excel');
            
	    $this->email->clear(TRUE);
	    $this->email->from('booking@247around.com', '247around Team');
	    $this->email->to($p['summary_email_to']);
	    $this->email->cc($p['summary_email_cc']);
	    $this->email->bcc($p['summary_email_bcc']);

	    $this->email->subject("247around Services Report - " . $p['public_name'] . " - " . date('d-M-Y'));
	    $summary_table = $this->get_partner_summary_table($p['id']);
            log_message('info', __FUNCTION__ . ' => Prepared summary report');

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
	    exec("rm -rf " . escapeshellarg($output_file), $out, $return);
            // Return will return non-zero upon an error
            
            if(!$return){
                // exec() has been executed sucessfully
                // Inserting values in scheduler tasks log
                $this->reporting_utils->insert_scheduler_tasks_log(__FUNCTION__);
                //Logging
                log_message('info',__FUNCTION__.' Executed Sucessfully '.$output_file);
                
            }
	}

	exit(0);
    }

    function booking_report() {
        $data = $this->reporting_utils->get_report_data();
        $today_ratings = $this->booking_model->get_completed_booking_details();
        $agents_data = $this->reporting_utils->get_247_agent_report_data();
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
                    . "<td style='text-align: center;border: 1px solid #ddd;'>" . $value['source'] . "</td><td style='text-align: center;border: 1px solid #ddd;'>" . ($value['total']+$data['today_completed'][$key]+$data['today_cancelled'][$key]) . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['scheduled'] . " </td></td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['queries'] . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $data['today_completed'][$key] . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $data['today_cancelled'][$key] . " </td></tr>";
            $total_today += $value['total'];
            $total_today_scheduled += $value['scheduled'];
            $total_today_completed += $data['today_completed'][$key];
            $total_total_cancelled += $data['today_cancelled'][$key];
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
                    . "<td style='text-align: center;border: 1px solid #ddd;'>" . $value['source'] . "</td><td style='text-align: center;border: 1px solid #ddd;'>" . ($value['total']+$data['month_completed'][$key]+$data['month_cancelled'][$key]) . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['scheduled'] . " </td></td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['queries'] . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $data['month_completed'][$key] . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $data['month_cancelled'][$key] . " </td></tr>";
            $total += $value['total'];
            $scheduled += $value['scheduled'];
            $completed += $data['month_completed'][$key];
            $cancelled += $data['month_cancelled'][$key];
            $queries += $value['queries'];
        }

        $html .="<tr style='padding: 8px;line-height: 1.42857143;vertical-align: top; border-top: 1px solid #ddd;border: 1px solid #ddd;'><td style='text-align: center;border: 1px solid #ddd;'>Total</td><td style='text-align: center;border: 1px solid #ddd;'>" . $total . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $scheduled . " </td></td><td style='text-align: center;border: 1px solid #ddd;'>" . $queries . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $completed . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $cancelled . " </td></tr>";


        $html .= '</tbody>
                          </table>
                        </div>';
        
        $html .= ' <p style="margin-top: 30px;" >Today Agent Summary:</p>
                        <div style="margin-top: 30px;" >
                          <table style="width: 100%;max-width: 100%;margin-bottom: 20px;border: 1px solid #ddd;">
                            <thead>
                              <tr style="padding: 8px;line-height: 1.42857143;vertical-align: top; border-top: 1px solid #ddd">

                                <th style="border: 1px solid #ddd;">Agent</th>
                                <th style="border: 1px solid #ddd;">Total</th>
                                <th style="border: 1px solid #ddd;">Scheduled</th>
                                <th style="border: 1px solid #ddd;">Re-Scheduled</th>
                                <th style="border: 1px solid #ddd;">Follow Up</th>
                                <th style="border: 1px solid #ddd;">Completed</th>
                                <th style="border: 1px solid #ddd;">Cancelled Query</th>
                                <th style="border: 1px solid #ddd;">Cancelled Booking</th>
                              </tr>
                            </thead>
                            <tbody >';
        $total = 0;
        $scheduled = 0;
        $rescheduled = 0;
        $completed = 0;
        $cancelled_query = 0;
        $cancelled_booking = 0;
        $queries = 0;
        foreach ($agents_data as $key => $value) {
            $html .= "<tr style='padding: 8px;line-height: 1.42857143;vertical-align: top; border-top: 1px solid #ddd;border: 1px solid #ddd;'>"
                    . "<td style='text-align: center;border: 1px solid #ddd;'>" . $value['employee_id'] . 
                    "</td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['total'] . 
                    " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['scheduled'] . 
                    " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['rescheduled'] . 
                    " </td></td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['queries'] . 
                    " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['completed'] . 
                    " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['cancelled_query'] . 
                    " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['cancelled_booking'] . 
                    " </td></tr>";

            $total += $value['total'];
            $scheduled += $value['scheduled'];
            $rescheduled += $value['rescheduled'];
            $completed += $value['completed'];
            $cancelled_query += $value['cancelled_query'];
            $cancelled_booking += $value['cancelled_booking'];
            $queries += $value['queries'];
        }

        $html .="<tr style='padding: 8px;line-height: 1.42857143;vertical-align: top; border-top: 1px solid #ddd;border: 1px solid #ddd;'><td style='text-align: center;border: 1px solid #ddd;'>Total</td><td style='text-align: center;border: 1px solid #ddd;'>" . $total . " "
                . "</td><td style='text-align: center;border: 1px solid #ddd;'>" . $scheduled . " "
                . "</td><td style='text-align: center;border: 1px solid #ddd;'>" . $rescheduled . " "
                . "</td></td><td style='text-align: center;border: 1px solid #ddd;'>" . $queries . " "
                . "</td><td style='text-align: center;border: 1px solid #ddd;'>" . $completed . " "
                . "</td><td style='text-align: center;border: 1px solid #ddd;'>" . $cancelled_query . " "
                . "</td><td style='text-align: center;border: 1px solid #ddd;'>" . $cancelled_booking . " "
                . "</td></tr>";


        $html .= '</tbody>
                          </table>
                        </div>';


        $html .= '</body>
                    </html>'; 
        
        $to = "anuj@247around.com, nits@247around.com";
        $this->notify->sendEmail("booking@247around.com", $to, "", "", "Booking Summary", $html, "");
         log_message('info',__FUNCTION__.'Booking Report mail sent.');
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
    
    /**
     * @desc: This function is used to send new service center report mail(CRON)
     *          To ALL the RM's for their corresponding vendors
     * params: void
     * retunr :void
     *
     */
    function new_send_service_center_report_mail(){
        //Geting Array of RM's
        $rms = $this->employee_model->get_rm_details();
        //Looping for each RM to send their corresponding reports of SF
        foreach($rms as $value){
            //Getting RM to SF Relation
            $sf_list = $this->vendor_model->get_employee_relation($value['id']);
            if(!empty($sf_list)){
                $html = $this->booking_utilities->booking_report_for_new_service_center($sf_list[0]['service_centres_id']);
                $to = $sf_list[0]['official_email'];

                $this->notify->sendEmail("booking@247around.com", $to, "", "", "New Service Center Report ".date('d-M,Y'), $html, "");
                log_message('info', __FUNCTION__ . ' New Service Center Report mail sent to '. $to);
                
                // Inserting values in scheduler tasks log
                $this->reporting_utils->insert_scheduler_tasks_log(__FUNCTION__);
            }
        }
    }
    
    /**
     * @desc: This function is used to send service center report mail(CRON)
     *          To all the RM for their corresponding  SF
     * params: void
     * retunr :void
     *
     */
    
    function send_service_center_report_mail() {
        //Geting Array of RM's
        $rms = $this->employee_model->get_rm_details();
        //Looping for each RM 
        foreach($rms as $value){
            //Getting RM to SF relation
            $sf_list = $this->vendor_model->get_employee_relation($value['id']);
            if(!empty($sf_list)){
                $html = $this->booking_utilities->booking_report_by_service_center($sf_list[0]['service_centres_id']);
                $to = $sf_list[0]['official_email'];
        
                $this->notify->sendEmail("booking@247around.com", $to, "", "", "Service Center Report ".date('d-M,Y'), $html, "");
                log_message('info', __FUNCTION__ . ' Service Center Report mail sent to '. $to);
                
                // Inserting values in scheduler tasks log
                $this->reporting_utils->insert_scheduler_tasks_log(__FUNCTION__);
            }
        }
    }

    /**
     * @Desc: This function is used to get distance between vednor and customer for completed bookings
     *        based on their Pincodes
     * @param void
     * @return: int(No. of vendors whose pincode is not present)
     * 
     */
    function get_vendor_customer_distance_by_pincode(){
        //Getting booking details
        //$completed_bookings = [];
        $no_pincode = 0;
        $done=0;
        
        $bookings = $this->reporting_utils->get_completed_month_bookings();
        echo 'Bookings found: ' . count($bookings) . '\n';
        
        foreach ($bookings as $key=>$value){
           if($value['service_center_pincode'] == ''){
               echo 'Pincode not found' . PHP_EOL;
               $no_pincode++;
           }else{
               //Process to get distance between vendor and customer pincode
               $csv_array  = $value;
               //Using file_get_content to get json response for GET Request
               $du = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . 
                       $value['service_center_pincode'] . ",India" .  
                       "&destinations=" . 
                       $value['booking_details_pincode'] . ",India" .  
                       "&key=AIzaSyDYYGttub8nTWcXVZBG9iMuQwZfFaBNcbQ");
               $djd = json_decode(utf8_encode($du),true);
               $csv_array['distance'] = $djd['rows'][0]['elements'][0]['distance']['text'];
               
               //Creating csv file and appending data
               $file_name = '/tmp/Vendor-Cutomer-Distance-Wybor.csv';
               
               if (file_exists($file_name)) {
                    $file = fopen($file_name, 'a');
                    fputcsv($file,$csv_array);
                } else {
                    $file = fopen($file_name, 'w');
                    fputcsv($file,$csv_array);
                }
              
              echo $done++ . '.....' . PHP_EOL;
           }
        }
        
        //Closing csv file 
        fclose($file);
        
        echo "No pincodes: " . $no_pincode;
        
        exit(0);
        //return $no_pincode;
    }

    /**
     * @desc: 
     * @param integer $is_mail
     */
    function get_sc_crimes($is_mail = 0){
        log_message('info', __FUNCTION__ );
        
        if($is_mail ==0){
            // The function is being called from system Manually
            $where = "";
            $id = $this->session->userdata('id');
            //Getting Employee Relation if present for Logged in User
            $sf_list = $this->vendor_model->get_employee_relation($id);
            if (!empty($sf_list)) {
                $sf_list = $sf_list[0]['service_centres_id'];
                $where = "AND service_centres.id IN (" . $sf_list . ")";
            }
            $data['data'] = $this->reporting_utils->get_sc_crimes($where);

            $this->load->view('employee/header/' . $this->session->userdata('user_group'));
            $this->load->view('employee/get_crimes', $data);
        } else {
            //The function is being called from CRON
            if( date('l') != "Sunday"){
            $where = "";
            $data['data'] = $this->reporting_utils->get_sc_crimes($where);
            //Loading view
            $view = $this->load->view('employee/get_crimes', $data, TRUE);
            $subject = "SF Crimes Report " . date("d-M-Y");
            $to = 'anuj@247around.com, nits@247around.com';
            $this->notify->sendEmail("booking@247around.com", $to, "", "", $subject, $view, "");
            } else {
                log_message('info', __FUNCTION__ ." Today is Sunday, Hence report would not generate");
            }
            // Inserting values in scheduler tasks log
            $this->reporting_utils->insert_scheduler_tasks_log(__FUNCTION__); 
        }
        
         log_message('info', __FUNCTION__ ." Exit");

    }
    /**
     * @desc: This method is used send a report to SF. In this report, SF will see count those booking which is not updated
     */
    function get_sc_crimes_for_sf(){
        log_message('info', __FUNCTION__ );
       if( date('l') != "Sunday"){
        $vendor_details = $this->vendor_model->getactive_vendor();
        foreach ($vendor_details as $value) {
            if($value['is_update'] == '1'){
                $where = " AND id = '".$value['id']."'";
                $data['data']= $this->reporting_utils->get_sc_crimes($where);
                $view =  $this->load->view('employee/get_crimes', $data, TRUE); 
    
                $to  = $value['primary_contact_email'].",".$value['owner_email'];
                //$cc = "anuj@247around.com, nits@247around.com";
                $cc = "";
                $subject = $value['name']." - Bookings Not Updated Report - " . date("d-M-Y");
                $this->notify->sendEmail("booking@247around.com", $to, $cc, "", $subject, $view, "");
                
            }
        }
        } else {
             log_message('info', __FUNCTION__ ." Today is Sunday, Hence report would not generate");
        }
        // Inserting values in scheduler tasks log
        $this->reporting_utils->insert_scheduler_tasks_log(__FUNCTION__);
        
        log_message('info', __FUNCTION__ ." Exit");
        
    }
    
    /**
     * @desc: If is_mal flag is 0 then it displays a table. In the table, we will show Today and Past Un-assignd booking
     * If is_mail flag is 1 then send an email with attach a table, In the table, we will show Today and Past Un-assignd booking
     */
    
    function get_un_assigned_crimes_for_247around($is_mail = 0){
        log_message('info', __FUNCTION__ );
        $data['data'] = $this->reporting_utils->get_unassigned_crimes();
       
        if($is_mail == 0){
            
             $this->load->view('employee/header/'.$this->session->userdata('user_group'));
             $this->load->view('employee/unassigned_table', $data);
        } else if($is_mail == 1){
            
            $view =  $this->load->view('employee/unassigned_table', $data, TRUE); 
            $to = "anuj@247around.com, nits@247around.com";
            $subject = "SF Engineer Assigned Report " . date("d-M-Y");
            $this->notify->sendEmail("booking@247around.com", $to, "", "", $subject, $view, "");
        }
    }
    
    /**
     * @desc: This is used to send mail to SC(POC AND OWNER) with Un-Assigned booking
     */
    function get_un_assigned_crimes_for_sc(){
        log_message('info', __FUNCTION__ );
        $data = $this->reporting_utils->get_unassigned_crimes();
        foreach ($data as $value){
            $view = $this->load->view('employee/unassigned_table', $value, TRUE);
            $to = $value['primary_contact_email'].",". $value['owner_email'];
            //$to = "abhaya@247around.com";
            $subject = $value['service_center_name']." Assigned Report " . date("d-M-Y");
            $this->notify->sendEmail("booking@247around.com", $to, "", "", $subject, $view, "");
        }
    }
}
