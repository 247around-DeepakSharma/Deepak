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
        $this->load->model('penalty_model');
        $this->load->library('miscelleneous');
        $this->load->library('PHPReport');
        $this->load->library('send_grid_api');
        $this->load->library('notify');
        $this->load->library('email');
        $this->load->library('session');
        $this->load->library('s3');
        $this->load->library('booking_utilities');
        $this->load->library('booking_summary');
        
        $this->load->helper('url');

        $this->load->dbutil();
        $this->load->helper('file');
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
        $templateDir = FCPATH . "application/controllers/excel-templates/";

        //set config for report
        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );

        //load template
        if(ob_get_length() > 0) {
            ob_end_clean();
        }
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
                $output_file = TMP_FOLDER . "BookingSummary-" . date('d-M-Y') . ".xls";
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
            //Getting list of RM and Admin details
            $employee_for_cron_mail_list = $this->employee_model->get_employee_for_cron_mail();
            foreach ($employee_for_cron_mail_list as $value) {

                $sf_list = $this->vendor_model->get_employee_relation($value['id']);
                if (!empty($sf_list)) {
                    $sf_list = $sf_list[0]['service_centres_id'];
                }
                $to = $value['official_email'];

                //Fetching pending bookings
                $pending_bookings = $this->reporting_utils->get_pending_bookings($sf_list);
                $count = count($pending_bookings);
                log_message('info', "Count: " . $count);

                if ($count > 0) {
                    //Get num of pending bookings for each vendor
                    $sc_pending_bookings = $this->reporting_utils->get_num_pending_bookings_for_all_sc($sf_list);
                    log_message('info', print_r($sc_pending_bookings, TRUE));

                    //load template
                    if(ob_get_length() > 0) {
                        ob_end_clean();
                    }
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
                    $output_file = TMP_FOLDER . "BookingSummary-" . date('d-M-Y') . ".xlsx";
                    $R->render('excel', $output_file);
                    log_message('info', 'Rendered');

                    if ($mail_flag) {
                        //Cleaning Email Variables
                        $this->email->clear(TRUE);

                        log_message('info', "Report generated with $count records");
                        //Send report via email
                        $this->email->from(NOREPLY_EMAIL_ID, '247around Team');
                        $this->email->to($to);

                        $this->email->subject("Booking Summary: " . date('Y-m-d H:i:s'));
                        $this->email->message("Bookings pending as of today: " . $count . "<br/>");
                        $this->email->attach($output_file, 'attachment');

                        if ($this->email->send()) {
                            log_message('info', __METHOD__ . ": Mail sent successfully");
                        } else {
                            log_message('info', __METHOD__ . ": Mail could not be sent :" . $this->email->print_debugger());
                        }
                        //Upload Excel to AWS
                        $bucket = BITBUCKET_DIRECTORY;
                        $directory_xls = "summary-excels/" . "BookingSummary-" . date('d-M-Y') . ".xlsx";
                        $this->s3->putObjectFile(realpath($output_file), $bucket, $directory_xls, S3::ACL_PRIVATE);
                        //Delete this file
                        exec("rm -rf " . $output_file, $out, $return);
                        // Return will return non-zero upon an error
                        if (!$return) {
                            //Logging
                            log_message('info', __FUNCTION__ . ' Executed Sucessfully ' . "BookingSummary-" . date('d-M-Y') . ".xlsx");
                        }
                    }
                }
            }

            //Processing Get Pending Bookings for Closure Team of ALL SF

            $where = array('groups' => 'closure');
            $closure = $this->employee_model->get_employee_by_group($where);

            $tmp = "";
            foreach ($closure as $value) {
                $tmp .= $value['official_email'] . ',';
            }
            $to = (rtrim($tmp, ","));
            $sf_list = ''; // Setting empty for all SF List value
            //Fetching pending bookings
            $pending_bookings = $this->reporting_utils->get_pending_bookings($sf_list);
            $count = count($pending_bookings);
            log_message('info', "Count: " . $count);

            if ($count > 0) {
                //Get num of pending bookings for each vendor
                $sc_pending_bookings = $this->reporting_utils->get_num_pending_bookings_for_all_sc($sf_list);

                //load template
                if(ob_get_length() > 0) {
                    ob_end_clean();
                }
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
                $output_file = TMP_FOLDER . "BookingSummary-" . date('d-M-Y') . ".xlsx";
                $R->render('excel', $output_file);

                if ($mail_flag) {
                    //Cleaning Email Variables
                    $this->email->clear(TRUE);

                    //Send report via email
                    $this->email->from(NOREPLY_EMAIL_ID, '247around Team');
                    $this->email->to($to);

                    $this->email->subject("Booking Summary: " . date('Y-m-d H:i:s'));
                    $this->email->message("Bookings pending as of today: " . $count . "<br/>");
                    $this->email->attach($output_file, 'attachment');

                    if ($this->email->send()) {
                        log_message('info', __METHOD__ . ": Mail sent successfully to " . $value['full_name'] . ' of Closure Team');
                    } else {
                        log_message('info', __METHOD__ . ": Mail could not be sent to " . $value['full_name'] . ' of Closure Team');
                    }

                    //Upload Excel to AWS
                    $bucket = BITBUCKET_DIRECTORY;
                    $directory_xls = "summary-excels/" . "BookingSummary-" . date('d-M-Y') . ".xlsx";
                    $this->s3->putObjectFile(realpath($output_file), $bucket, $directory_xls, S3::ACL_PRIVATE);

                    //Delete this file
                    exec("rm -rf " . $output_file, $out, $return);
                    // Return will return non-zero upon an error

                    if (!$return) {
                        //Logging
                        log_message('info', __FUNCTION__ . ' Executed Sucessfully ' . "BookingSummary-" . date('d-M-Y') . ".xlsx");
                    }
                }
            }
        }
        //Adding Details in Scheduler tasks table

        $this->reporting_utils->insert_scheduler_tasks_log(__FUNCTION__);
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
                if(ob_get_length() > 0) {
                ob_end_clean();
                }
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
                $this->email->from(NOREPLY_EMAIL_ID, '247around Team');
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
                $bucket = BITBUCKET_DIRECTORY;
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

        $today_install_req = $partner_summary_params['today_install_req'];
        $yday_install_req = $partner_summary_params['yday_install_req'];
        $month_install_req = $partner_summary_params['month_install_req'];

        $today_install_sched = $partner_summary_params['today_install_sched'];
        $yday_install_sched = $partner_summary_params['yday_install_sched'];
        $month_install_sched = $partner_summary_params['month_install_sched'];

        $today_install_compl = $partner_summary_params['today_install_compl'];
        $yday_install_compl = $partner_summary_params['yday_install_compl'];
        $month_install_compl = $partner_summary_params['month_install_compl'];

        $today_followup_pend = $partner_summary_params['today_followup_pend'];
        $yday_followup_pend = $partner_summary_params['yday_followup_pend'];
        $month_followup_pend = $partner_summary_params['month_followup_pend'];

        $today_install_cancl = $partner_summary_params['today_install_cancl'];
        $yday_install_cancl = $partner_summary_params['yday_install_cancl'];
        $month_install_cancl = $partner_summary_params['month_install_cancl'];

        $message = <<<EOD
    <table border="1" cellspacing="0" cellpadding="5px">
        <tr>
        <td>Date</td>
        <td>Requests Received</td>
        <td>Requests Completed</td>
        <td>Requests Scheduled</td>
        <td>To be Followed Up</td>
        <td>Requests Cancelled</td>
        </tr>

        <tr>
        <td>Yesterday</td>
        <td>$yday_install_req</td>
        <td>$yday_install_compl</td>
        <td>$yday_install_sched</td>
        <td>$yday_followup_pend</td>
        <td>$yday_install_cancl</td>
        </tr>

        <tr>
        <td>Today</td>
        <td>$today_install_req</td>
        <td>$today_install_compl</td>
        <td>$today_install_sched</td>
        <td>$today_followup_pend</td>
        <td>$today_install_cancl</td>
        </tr>

         <tr>
        <td>Month</td>
        <td>$month_install_req</td>
        <td>$month_install_compl</td>
        <td>$month_install_sched</td>
        <td>$month_followup_pend</td>
        <td>$month_install_cancl</td>
        </tr>

    </table>
EOD;

        return $message;
    }

    function send_leads_summary_mail_to_partners($partner_id = "") {

        $newCSVFileName = "Booking_summary_" . date('j-M-Y-H-i-s') . ".csv";
        $csv = TMP_FOLDER . $newCSVFileName;
        
        if(!empty($partner_id))
        {
            log_message('info', __FUNCTION__ . ' => Summaray Downloaded By Partner_id =' . $partner_id);
            $where_get_partner = array('partners.id' => $partner_id, 'partners.is_active' => '1');
            $select = "partners.id, partners.summary_email_to, partners.summary_email_cc, "
                    . " partners.summary_email_bcc, partners.public_name";
            $partners = $this->partner_model->getpartner_details($select, $where_get_partner, '1');
            if(!empty($partners))
            {
                $report = $this->partner_model->get_partner_leads_csv_for_summary_email($partners[0]['id'],0);                
                $delimiter = ",";
                $newline = "\r\n";
                $new_report = $this->dbutil->csv_from_result($report, $delimiter, $newline);
                log_message('info', __FUNCTION__ . ' => Rendered CSV');
                write_file($csv, $new_report);                
                //Upload File On AWS and save link in file_upload table
                $this->save_partner_summary_report($partners[0]['id'],$newCSVFileName,$csv);
                if($this->session->userdata('employee_id'))
                {
                    $this->generate_partner_summary_email_data($partners[0],$csv,$newCSVFileName,false);
                }
                else
                {
                     //Downloading Generated CSV  
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . basename($csv) . '"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($csv));
                    readfile($csv);
                    exec("rm -rf " . escapeshellarg($csv));
                }
                
                if(file_exists($csv))
                {
                    unlink($csv);
                }
                if($this->session->userdata('employee_id')){
                    redirect(base_url() . 'employee/partner/viewpartner', 'refresh');
                }else{
                    redirect(base_url().'partner/home','refresh');
                }
            }else{
                if($this->session->userdata('employee_id')){
                    redirect(base_url() . 'employee/partner/viewpartner', 'refresh');
                }else{
                    redirect(base_url().'partner/home','refresh');
                }
            }
        }
        else
        {
            log_message('info', __FUNCTION__ . ' => Partner Summary send to partners by Cron');

            $where_get_partner = array('partners.is_active' => '1','partners.is_reporting_mail'=>'1');
            $select = "partners.id, partners.summary_email_to, partners.summary_email_cc, "
                    . " partners.summary_email_bcc, partners.public_name";
            //Get all Active partners who has "is_reporting_mail" column 1
            $partners = $this->partner_model->getpartner_details($select, $where_get_partner, '1');
            foreach ($partners as $key => $p)
            {
                $newCSVFileName = "Booking_summary_" . date('j-M-Y-H-i-s') . "_".$p['id'].".csv";
                $csv = TMP_FOLDER . $newCSVFileName;
                $report = $this->partner_model->get_partner_leads_csv_for_summary_email($p['id'],0);
                $delimiter = ",";
                $newline = "\r\n";
                $new_report = $this->dbutil->csv_from_result($report, $delimiter, $newline);
                write_file($csv, $new_report);
                
                //Upload File On AWS and save link in file_upload table
                $this->save_partner_summary_report($p['id'],$newCSVFileName,$csv);
                
                $this->generate_partner_summary_email_data($partners[$key],$csv,$newCSVFileName,false);
                //Delete this file
                $out = '';
                $return = 0;
                exec("rm -rf " . escapeshellarg($csv), $out, $return);
                
                // Return will return non-zero upon an error
                if (!$return) {
                    // exec() has been executed sucessfully
                    // Inserting values in scheduler tasks log
                    $this->reporting_utils->insert_scheduler_tasks_log(__FUNCTION__);
                    //Logging
                    log_message('info', __FUNCTION__ . ' Executed Sucessfully ' . $csv);
                }
            }
        }  
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
                      <p><b>Total Ratings in  ' . date('M') . ' : ' . $today_ratings['ratings']->ratings . '</b></p>
                      <p><b>Total Bookings Completed in ' . date('M') . ' :  ' . $today_ratings['bookings']->bookings . '</b></p>
                      <p><b>Total Bookings Completed in ' . date("M", strtotime("-1 months")) . ' :  ' . $today_ratings['bookings_previous']->bookings . '</b></p>
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
                    . "<td style='text-align: center;border: 1px solid #ddd;'>" . $value['source'] . "</td><td style='text-align: center;border: 1px solid #ddd;'>" . ($value['total'] + $data['today_completed'][$key] + $data['today_cancelled'][$key]) . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['scheduled'] . " </td></td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['queries'] . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $data['today_completed'][$key] . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $data['today_cancelled'][$key] . " </td></tr>";
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
                    . "<td style='text-align: center;border: 1px solid #ddd;'>" . $value['source'] . "</td><td style='text-align: center;border: 1px solid #ddd;'>" . ($value['total'] + $data['month_completed'][$key] + $data['month_cancelled'][$key]) . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['scheduled'] . " </td></td><td style='text-align: center;border: 1px solid #ddd;'>" . $value['queries'] . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $data['month_completed'][$key] . " </td><td style='text-align: center;border: 1px solid #ddd;'>" . $data['month_cancelled'][$key] . " </td></tr>";
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

        $to = NITS_ANUJ_EMAIL_ID;
        $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", "Booking Summary", $html, "",BOOKING_REPORT);
        log_message('info', __FUNCTION__ . 'Booking Report mail sent.');
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
    function set_data_in_table($data) {
        $html = "";
        for ($i = 0; $i < 3; $i++) {
            $total_booking = 0;
            $total_followup = 0;
            $total_scheduled = 0;
            $total_completed = 0;
            $total_cancelled = 0;
            $html .= $this->set_mail_table_head();
            foreach ($data['data' . $i] as $value) {

                $total_booking += $value['total_booking'];
                $total_followup += $value['queries'];
                $total_scheduled += $value['scheduled'];
                $total_completed += $value['completed'];
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
                    . "<td style='text-align: center;border: 1px solid #ddd;'>" . $total_booking . "</td>"
                    . "<td style='text-align: center;border: 1px solid #ddd;'>" . $total_followup . "</td>"
                    . "<td style='text-align: center;border: 1px solid #ddd;'>" . $total_scheduled . "</td>"
                    . "<td style='text-align: center;border: 1px solid #ddd;'>" . $total_completed . "</td>"
                    . "<td style='text-align: center;border: 1px solid #ddd;'>" . $total_cancelled . "</td>";

            $html .= "</body></html>";
            if ($i == 0) {
                $html .= '<p>Today Booking Summary</p>';
            } else if ($i == 1) {
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
    function new_send_service_center_report_mail() {
        //Geting Array of RM and Admin list
        $employee_for_cron_mail_list = $this->employee_model->get_employee_for_cron_mail();
        //Looping for each RM to send their corresponding reports of SF
        foreach ($employee_for_cron_mail_list as $value) {
            //Getting RM to SF Relation
            $sf_list = $this->vendor_model->get_employee_relation($value['id']);
            if (!empty($sf_list)) {
                $sf_list = $sf_list[0]['service_centres_id'];
            }
            $html = $this->booking_utilities->booking_report_for_new_service_center($sf_list);
            $to = $value['official_email'];

            $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", "New Service Center Report " . date('d-M,Y'), $html, "",NEW_SERVICE_CENTERS_REPORT);
            log_message('info', __FUNCTION__ . ' New Service Center Report mail sent to ' . $to);

            // Inserting values in scheduler tasks log
            $this->reporting_utils->insert_scheduler_tasks_log(__FUNCTION__);
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
        //Dumping data in sf_snapshot's table
        $final_data = [];
        $data = $this->reporting_utils->get_booking_by_service_center(""); //Getting All SF List Details
        foreach ($data['service_center_id'] as $value) {
            $data_dump['sc_id'] = $value;
            $data_dump['yesterday_booked'] = isset($data['data'][$value]['yesterday_booked']['booked']) ? $data['data'][$value]['yesterday_booked']['booked'] : '';
            $data_dump['yesterday_completed'] = isset($data['data'][$value]['yesterday_completed']['completed']) ? $data['data'][$value]['yesterday_completed']['completed'] : '';
            $data_dump['yesterday_cancelled'] = isset($data['data'][$value]['yesterday_cancelled']['cancelled']) ? $data['data'][$value]['yesterday_cancelled']['cancelled'] : '';
            $data_dump['month_completed'] = isset($data['data'][$value]['month_completed']['completed']) ? $data['data'][$value]['month_completed']['completed'] : '';
            $data_dump['month_cancelled'] = isset($data['data'][$value]['month_cancelled']['cancelled']) ? $data['data'][$value]['month_cancelled']['cancelled'] : '';
            $data_dump['last_2_day'] = isset($data['data'][$value]['last_2_day']['booked']) ? $data['data'][$value]['last_2_day']['booked'] : '';
            $data_dump['last_3_day'] = isset($data['data'][$value]['last_3_day']['booked']) ? $data['data'][$value]['last_3_day']['booked'] : '';
            $data_dump['greater_than_5_days'] = isset($data['data'][$value]['greater_than_5_days']['booked']) ? $data['data'][$value]['greater_than_5_days']['booked'] : '';
            $final_data[] = $data_dump;
        }

        //Inserting batch data
        if ($this->reporting_utils->insert_batch_sf_snapshot($final_data)) {
            //Logging
            log_message('info', __FUNCTION__ . ' SF Snapshot data has been Dumped into table sf_snapshot');
        } else {
            //Logging
            log_message('info', __FUNCTION__ . ' Error in dumping data in sf_snapshot table');
        }

        //Geting Array of RM's and Admin
        $employee_for_cron_mail_list = $this->employee_model->get_employee_for_cron_mail();
        //Looping for each RM
        foreach ($employee_for_cron_mail_list as $value) {
            //Getting RM to SF relation
            $sf_list = $this->vendor_model->get_employee_relation($value['id']);
            if (!empty($sf_list)) {
                $sf_list = $sf_list[0]['service_centres_id'];
            }
            $html = $this->booking_utilities->booking_report_by_service_center($sf_list, 1);
            $to = $value['official_email'];

            $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", "Service Center Report " . date('d-M,Y'), $html, "",SERVICE_CENTERS_REPORT);
            log_message('info', __FUNCTION__ . ' Service Center Report mail sent to ' . $to);

            // Inserting values in scheduler tasks log
            $this->reporting_utils->insert_scheduler_tasks_log(__FUNCTION__);
        }
    }

    /**
     * @Desc: This function is used to get distance between vednor and customer for completed bookings
     *        based on their Pincodes
     * @param void
     * @return: int(No. of vendors whose pincode is not present)
     *
     */
    function get_vendor_customer_distance_by_pincode() {
        //Getting booking details
        //$completed_bookings = [];
        $no_pincode = 0;
        $done = 0;

        $bookings = $this->reporting_utils->get_completed_month_bookings();
        echo 'Bookings found: ' . count($bookings) . '\n';

        foreach ($bookings as $key => $value) {
            if ($value['service_center_pincode'] == '') {
                echo 'Pincode not found' . PHP_EOL;
                $no_pincode++;
            } else {
                //Process to get distance between vendor and customer pincode
                $csv_array = $value;
                //Using file_get_content to get json response for GET Request
                $du = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?origins=" .
                        $value['service_center_pincode'] . ",India" .
                        "&destinations=" .
                        $value['booking_details_pincode'] . ",India" .
                        "&key=AIzaSyDYYGttub8nTWcXVZBG9iMuQwZfFaBNcbQ");
                $djd = json_decode(utf8_encode($du), true);
                $csv_array['distance'] = $djd['rows'][0]['elements'][0]['distance']['text'];

                //Creating csv file and appending data
                $file_name = TMP_FOLDER . 'Vendor-Cutomer-Distance-Wybor.csv';

                if (file_exists($file_name)) {
                    $file = fopen($file_name, 'a');
                    fputcsv($file, $csv_array);
                } else {
                    $file = fopen($file_name, 'w');
                    fputcsv($file, $csv_array);
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
    function get_sc_crimes($is_mail = 0) {
        log_message('info', __FUNCTION__);

        if ($is_mail == 0) {
            // The function is being called from system Manually
            $where = "";
            $id = $this->session->userdata('id');
            //Getting Employee Relation if present for Logged in User
            $sf_list = $this->vendor_model->get_employee_relation($id);
            if (!empty($sf_list)) {
                $sf_list = $sf_list[0]['service_centres_id'];
                $service_center_array = explode(",",$sf_list);
                $sc_id = implode("','",$service_center_array);
                $where = "AND service_centres.id IN ('" . $sc_id . "')";
            }
            $data['data'] = $this->reporting_utils->get_sc_crimes($where);
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/get_crimes', $data);
        } else {
            //The function is being called from CRON
            if (date('l') != "Sunday") {
                //Geting Array of RM and Admin group
                $employee_for_cron_mail_list = $this->employee_model->get_employee_for_cron_mail();
                foreach ($employee_for_cron_mail_list as $value) {
                    //Getting RM to SF relation
                    $where = "";
                    $sf_list = $this->vendor_model->get_employee_relation($value['id']);
                    if (!empty($sf_list)) {
                        $sf_list = $sf_list[0]['service_centres_id'];
                        $where = "AND service_centres.id IN (" . $sf_list . ")";
                    }

                    $data['data'] = $this->reporting_utils->send_sc_crimes_report_mail_data($where);
                    if (!empty($data['data'])) {
                        //Loading view
                        $view = $this->load->view('employee/get_crimes', $data, TRUE);
                        $subject = "SF Crimes Report " . date("d-M-Y");
                        $to = $value['official_email'];
                        $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", $subject, $view, "",SC_CRIME_REPORT);
                    } else {
                        log_message('info', __FUNCTION__ . " Empty Data get");
                    }
                }
            } else {
                log_message('info', __FUNCTION__ . " Today is Sunday, Hence report would not generate");
            }
            // Inserting values in scheduler tasks log
            $this->reporting_utils->insert_scheduler_tasks_log(__FUNCTION__);
        }

        log_message('info', __FUNCTION__ . " Exit");
    }

    /**
     * @desc: This method is used send a report to SF. In this report, SF will see count those booking which is not updated
     */
    function get_sc_crimes_for_sf() {
        log_message('info', __FUNCTION__);
        if (date('l') != "Sunday") {
            $vendor_details = $this->vendor_model->getVendorDetails("*", array('is_sf' => 1));
            foreach ($vendor_details as $value) {
                if ($value['is_update'] == '1') {
                    $where = " AND id = '" . $value['id'] . "'";
                    $data['data'] = $this->reporting_utils->send_sc_crimes_report_mail_data($where);
                    if (!empty($data['data']) && $data['data'][0]['not_update'] > 0) {
                        
                        //get rm email
                        $rm_email = "";
                        $rm_details = $this->vendor_model->get_rm_sf_relation_by_sf_id($value['id']);
                        if(!empty($rm_details)){
                            $rm_email = $rm_details[0]['official_email'];
                        }
                        
                        $view = $this->load->view('employee/get_crimes', $data, TRUE);
                        $file_data = $this->penalty_model->get_penalty_on_booking_any(array('penalty_on_booking.service_center_id' => $data['data'][0]['service_center_id'],
                            'penalty_on_booking.criteria_id' => '2', 'penalty_on_booking.create_date >=' => date('Y-m-d', strtotime("-1 days"))), 'booking_id');

                        $file_path = "";
                        if (!empty($file_data)) {
                            $file_path = TMP_FOLDER . $data['data'][0]['service_center_name'] . "-" . date('Y-m-d');
                            $file = fopen($file_path . ".txt", "a+") or die("Unable to open file!");

                            foreach ($file_data as $booking_id) {

                                fwrite($file, $booking_id['booking_id'] . "\n");
                            }
                            fclose($file);
                        }

                        $to = $value['primary_contact_email'] . "," . $value['owner_email'];

                        $bcc = "";
                        $cc = $rm_email;
                        $subject = $value['name'] . " - Bookings Not Updated Report - " . date("d-M-Y");

                        $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, $bcc, $subject, $view, $file_path . ".txt",SC_CRIME_REPORT_FOR_SF);
                        exec("rm -rf " . escapeshellarg($file_path));
                    } else {
                        log_message('info', __FUNCTION__ . " Empty Data Get");
                    }
                }
            }
        } else {
            log_message('info', __FUNCTION__ . " Today is Sunday, Hence report would not generate");
        }
        // Inserting values in scheduler tasks log
        $this->reporting_utils->insert_scheduler_tasks_log(__FUNCTION__);

        log_message('info', __FUNCTION__ . " Exit");
    }

    /**
     * @desc: If is_mal flag is 0 then it displays a table. In the table, we will show Today and Past Un-assignd booking
     * If is_mail flag is 1 then send an email with attach a table, In the table, we will show Today and Past Un-assignd booking
     */
    function get_un_assigned_crimes_for_247around($is_mail = 0) {
        log_message('info', __FUNCTION__);
        $data['data'] = $this->reporting_utils->get_unassigned_crimes();

        if ($is_mail == 0) {
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/unassigned_table', $data);
        } else if ($is_mail == 1) {

            $view = $this->load->view('employee/unassigned_table', $data, TRUE);
            $to = NITS_ANUJ_EMAIL_ID;
            $subject = "SF Engineer Assigned Report " . date("d-M-Y");
            $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", $subject, $view, "",UN_ASSIGNED_BOOKING);
        }
    }

    /**
     * @desc: This is used to send mail to SC(POC AND OWNER) with Un-Assigned booking
     */
    function get_un_assigned_crimes_for_sc() {
        log_message('info', __FUNCTION__);
        $data = $this->reporting_utils->get_unassigned_crimes();
        foreach ($data as $value) {

            //get rm email
            $rm_email = "";
            $rm_details = $this->vendor_model->get_rm_sf_relation_by_sf_id($value['sf_id']);
            if (!empty($rm_details)) {
                $rm_email = $rm_details[0]['official_email'];
            }
            
            $view = $this->load->view('employee/unassigned_table', $value, TRUE);
            $to = $value['primary_contact_email'] . "," . $value['owner_email'];
            $cc = $rm_email;
            $subject = $value['service_center_name'] . " Assigned Report " . date("d-M-Y");
            $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $view, "",UN_ASSIGNED_BOOKING_TO_SF);
        }
    }

    /**
     * @desc: This function is used to get agent current day working details
     * params: void
     * retun :void
     *
     */
    function agent_working_details($flag = "") {
        log_message('info', __FUNCTION__ . ": Fetched Agent Daily Working Report");

        $data['data'] = $this->reporting_utils->get_agent_daily_reports($flag);

        //print_r($data);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/agent_working_details', $data);

        //echo json_encode($data);
    }

    /**
     * @desc: This function is used to get agent working details through ajax
     * params: void
     * retun :void
     *
     */
    function agent_working_details_ajax($flag = "") {
        $sDate = $this->input->post('sDate');
        $eDate = $this->input->post('eDate');
        $startDate = date('Y-m-d 00:00:00', strtotime($sDate));
        $endDate = date('Y-m-d 23:59:59', strtotime($eDate));
        $data['data'] = $this->reporting_utils->get_agent_daily_reports($flag, $startDate, $endDate);
        $agent_name = [];
        $query_cancel = [];
        $query_booking = [];
        $calls_placed = [];
        $calls_received = [];
        $rating = [];
        foreach ($data['data'] as $key => $value) {
            array_push($agent_name, $value['employee_id']);
            array_push($query_cancel, $value['followup_to_cancel']);
            array_push($query_booking, $value['followup_to_pending']);
            array_push($calls_placed, $value['calls_placed']);
            array_push($calls_received, $value['calls_recevied']);
            array_push($rating, $value['rating']);
        }
        $data_report['agent_name'] = implode(",", $agent_name);
        $data_report['query_cancel'] = implode(",", $query_cancel);
        $data_report['query_booking'] = implode(",", $query_booking);
        $data_report['calls_placed'] = implode(",", $calls_placed);
        $data_report['calls_received'] = implode(",", $calls_received);
        $data_report['rating'] = implode(",", $rating);
        echo json_encode($data_report);
    }

    /**
     * @desc: This function is used to insert agent daily calls report into table
     * params: void
     * retun :void
     *
     */
    function agent_working_details_cron($flag = "") {
        log_message('info', __FUNCTION__ . ": Fetched Agent Daily Working Report");

        $data = $this->reporting_utils->get_agent_daily_reports($flag);
        if (!empty($data)) {
            $insert = $this->reporting_utils->insert_agent_daily_reports($data);
        }

        if ($insert) {
            log_message('info', __FUNCTION__ . ": Agent Daily Working Report Inserted Successfully");
        }
    }

    /**
     * @Desc: This function is used to get RM's specific crime reports
     * @params: void
     * @return: void
     *
     */
    function get_rm_crimes($flag = 0) {
        //Getting RM Array
        $final_array = [];
        $rm_array = $this->employee_model->get_rm_details();
        foreach ($rm_array as $value) {
            $old_crimes = 0;
            $update = 0;
            $not_update = 0;
            $total_crimes = 0;
            $escalations = 0;
            //Getting RM to SF relation
            $where = "";
            $sf_list = $this->vendor_model->get_employee_relation($value['id']);
            if (!empty($sf_list)) {
                $sf_list = $sf_list[0]['service_centres_id'];
                $service_center_array = explode(",",$sf_list);
                $sc_id = implode("','",$service_center_array);
                $where = "AND service_centres.id IN ('" . $sc_id . "')";
            }
            //Getting Crimes for particular RM for its corresponding SF
            $data[$value['id']] = $this->reporting_utils->get_sc_crimes($where);
            //Summing up values for this RM
            foreach ($data[$value['id']] as $val) {
                $old_crimes +=$val['monthly_total_crimes'];
                $update +=$val['update'];
                $not_update +=$val['not_update'];
                $total_crimes +=$val['total_booking'];
                $escalations +=$val['monthly_escalations'];
            }

            $temp['monthly_total_crimes'] = $old_crimes;
            $temp['update'] = $update;
            $temp['not_update'] = $not_update;
            $temp['total_booking'] = $total_crimes;
            $temp['rm_name'] = $this->employee_model->getemployeefromid($value['id'])[0]['full_name'];
            $temp['monthly_escalations'] = $escalations;
            //Finalizing Array for corresponding RM's ID
            $final_array['data'][] = $temp;
        }
        //Processing for SF's who have not yet being assigned to RM
        //Getting List of All vendors
        $all_vendors = $this->vendor_model->getAllVendor();
        $not_assigned_vendors = '';
        foreach ($all_vendors as $val) {
            if (empty($this->vendor_model->get_rm_sf_relation_by_sf_id($val['id']))) {
                $not_assigned_vendors .= $val['id'] . ',';
            }
        }
        if(!empty($not_assigned_vendors))
            $not_assigned_vendors_crime = $this->reporting_utils->get_sc_crimes("AND service_centres.id IN (" . rtrim($not_assigned_vendors, ',') . ")");

        $not_assigned_old_crimes = 0;
        $not_assigned_update = 0;
        $not_assigned_not_update = 0;
        $not_assigned_total_crimes = 0;
        $not_assigned_escalations = 0;
        foreach ($not_assigned_vendors_crime as $vals) {
            $not_assigned_old_crimes += $vals['monthly_total_crimes'];
            $not_assigned_update += $vals['update'];
            $not_assigned_not_update += $vals['not_update'];
            $not_assigned_total_crimes += $vals['total_booking'];
            $not_assigned_escalations += $vals['monthly_escalations'];
        }
        $temp_not_assigned['monthly_total_crimes'] = $not_assigned_old_crimes;
        $temp_not_assigned['update'] = $not_assigned_update;
        $temp_not_assigned['not_update'] = $not_assigned_not_update;
        $temp_not_assigned['total_booking'] = $not_assigned_total_crimes;
        $temp_not_assigned['rm_name'] = 'No RM Assigned';
        $temp_not_assigned['monthly_escalations'] = $not_assigned_escalations;
        //Finalizing Array for corresponding RM's ID
        $final_not_assigned_array[] = $temp_not_assigned;

        //Adding Value to Final Array for View
        $final_array['data'][] = $final_not_assigned_array[0];



        //Checking flag to display in CRM
        if ($flag == 0) {
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/get_rm_crimes', $final_array);
        } else {

            //Creating view for this Report

            $report_view = $this->load->view('employee/get_rm_crimes', $final_array, TRUE);
            //Sending Mail to ALL RM's and ADMIN employee
            $mail_list = $this->employee_model->get_employee_for_cron_mail();
            $to = "";
            foreach ($mail_list as $value) {
                $to .= $value['official_email'];
                $to .=", ";
            }
            $to = rtrim($to, ', ');

            $subject = " RM Crimes Report " . date("d-M-Y");
            $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", $subject, $report_view, "",RM_CRIME_REPORT);

            //Logging
            log_message('info', __FUNCTION__ . ' RM Crime Report has been sent successfully');
        }
    }

    /**
     * @Desc: This function is to show Pictorial Representation of Reports Table
     * @params: void
     * #return: void
     *
     */
    function show_reports_chart() {
        //1. RM crimes report
        //Getting RM Array
        $final_array = [];
        $rm_array = $this->employee_model->get_rm_details();
        foreach ($rm_array as $value) {
            $old_crimes = 0;
            $update = 0;
            $not_update = 0;
            $total_crimes = 0;
            $escalations = 0;
            //Getting RM to SF relation
            $where = "";
            $sf_list = $this->vendor_model->get_employee_relation($value['id']);
            if (!empty($sf_list)) {
                $sf_list = $sf_list[0]['service_centres_id'];
                $service_center_array = explode(",",$sf_list);
                $sc_id = implode("','",$service_center_array);
                $where = "AND service_centres.id IN ('" . $sc_id . "')";
            }
            //Getting Crimes for particular RM for its corresponding SF
            $data[$value['id']] = $this->reporting_utils->get_sc_crimes($where);
            //Summing up values for this RM
            foreach ($data[$value['id']] as $val) {
                $old_crimes +=$val['monthly_total_crimes'];
                $update +=$val['update'];
                $not_update +=$val['not_update'];
                $total_crimes +=$val['total_booking'];
                $escalations +=$val['monthly_escalations'];
            }

            $temp['monthly_total_crimes'] = $old_crimes;
            $temp['update'] = $update;
            $temp['not_update'] = $not_update;
            $temp['total_booking'] = $total_crimes;
            $temp['rm_name'] = $this->employee_model->getemployeefromid($value['id'])[0]['full_name'];
            $temp['monthly_escalations'] = $escalations;
            //Finalizing Array for corresponding RM's ID
            $final_array['data'][] = $temp;
        }
        //Making Final Array
        $data = [];
        $rm_name_st = "";
        $monthly_total_crimes_st = "";
        $update_st = "";
        $not_update_st = "";
        $total_booking_st = "";
        $monthly_escalations_st = "";
        foreach ($final_array['data'] as $value) {
            $rm_name_st .= "'" . $value['rm_name'] . "'" . ',';
            $monthly_total_crimes_st .= $value['monthly_total_crimes'] . ',';
            $update_st .= $value['update'] . ',';
            $not_update_st .= $value['not_update'] . ',';
            $total_booking_st .= $value['total_booking'] . ',';
            $monthly_escalations_st .= $value['monthly_escalations'] . ',';
        }
        $rm_name_st_trimmed = rtrim($rm_name_st, ',');
        $monthly_total_crimes_st_trimmed = rtrim($monthly_total_crimes_st, ',');
        $update_st_trimmed = rtrim($update_st, ',');
        $not_update_st_trimmed = rtrim($not_update_st, ',');
        $total_booking_st_trimmed = rtrim($total_booking_st, ',');
        $monthly_escalations_st_trimmed = rtrim($monthly_escalations_st, ',');

        //Final Data to be passed to View
        $data['rm'] = $rm_name_st_trimmed;
        $data['monthly_total_crimes'] = $monthly_total_crimes_st_trimmed;
        $data['updated'] = $update_st_trimmed;
        $data['not_updated'] = $not_update_st_trimmed;
        $data['total_booking'] = $total_booking_st_trimmed;
        $data['monthly_escalations'] = $monthly_escalations_st_trimmed;

        //2. Processing for sf snapshot to RM
        foreach ($rm_array as $val) {
            //Getting RM to SF relation
            $sf_list = $this->vendor_model->get_employee_relation($val['id']);
            if (!empty($sf_list)) {
                $sf_list = $sf_list[0]['service_centres_id'];
            }
            $sf_snapshot_data = $this->reporting_utils->get_booking_by_service_center($sf_list);

            $month_completed = 0;
            $month_cancelled = 0;
            $last_2_day = 0;
            $last_3_day = 0;
            $greater_than_5_days = 0;
            foreach ($sf_snapshot_data['data'] as $key => $value) {
                $month_completed += isset($value['month_completed']['completed']) ? $value['month_completed']['completed'] : 0;
                $month_cancelled += isset($value['month_cancelled']['cancelled']) ? $value['month_cancelled']['cancelled'] : 0;
                $last_2_day += isset($value['last_2_day']['booked']) ? $value['last_2_day']['booked'] : 0;
                $last_3_day += isset($value['last_3_day']['booked']) ? $value['last_3_day']['booked'] : 0;
                $greater_than_5_days += isset($value['greater_than_5_days']['booked']) ? $value['greater_than_5_days']['booked'] : 0;
            }

            $temp['month_completed'] = $month_completed;
            $temp['month_cancelled'] = $month_cancelled;
            $temp['last_2_day'] = $last_2_day;
            $temp['last_3_day'] = $last_3_day;
            $temp['greater_than_5_days'] = $greater_than_5_days;
            $final[] = $temp;
        }
        //Making Final Array
        $month_completed_st = "";
        $month_cancelled_st = "";
        $last_2_day_st = "";
        $last_3_day_st = "";
        $greater_than_5_days_st = "";
        foreach ($final as $value) {
            $month_completed_st .= $value['month_completed'] . ',';
            $month_cancelled_st .= $value['month_cancelled'] . ',';
            $last_2_day_st .= $value['last_2_day'] . ',';
            $last_3_day_st .= $value['last_3_day'] . ',';
            $greater_than_5_days_st .= $value['greater_than_5_days'] . ',';
        }
        $month_completed_st_trimmed = rtrim($month_completed_st, ',');
        $month_cancelled_st_trimmed = rtrim($month_cancelled_st, ',');
        $last_2_day_st_trimmed = rtrim($last_2_day_st, ',');
        $last_3_day_st_trimmed = rtrim($last_3_day_st, ',');
        $greater_than_5_days_st_trimmed = rtrim($greater_than_5_days_st, ',');

        //Final Data to be passed to View
        $data['month_completed'] = $month_completed_st_trimmed;
        $data['month_cancelled'] = $month_cancelled_st_trimmed;
        $data['last_2_day'] = $last_2_day_st_trimmed;
        $data['last_3_day'] = $last_3_day_st_trimmed;
        $data['greater_than_5_days'] = $greater_than_5_days_st_trimmed;
        $data['user_group'] = $this->session->userdata('user_group');
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/show_reports_chart', $data);
    }

    /**
     * @Desc: This function is used to show RM specific Snapshot Report
     *          It is being called from RM Charts View page
     * @params: RM Full_name
     * @return: view
     *
     */
    function show_rm_specific_snapshot($rm_fullname) {
        $decode_rm_fullname = urldecode($rm_fullname);

        //Getting RM id from Employee table
        $rm_details = $this->employee_model->get_employee_by_full_name($decode_rm_fullname);
        //Getting RM to SF relation
        $sf_list = $this->vendor_model->get_employee_relation($rm_details[0]['id']);
        if (!empty($sf_list)) {
            $sf_list = $sf_list[0]['service_centres_id'];
        }
        $data['html'] = $this->booking_utilities->booking_report_by_service_center($sf_list, '');
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/show_service_center_report', $data);
    }

    /**
     * @Desc: This function is used to show Partner completed bookings
     * @params: void()
     * @return: void()
     *
     */
    function partners_booking_report_chart() {
        $timestamp = strtotime(date("Y-m-d"));
        $startDate = date('Y-m-01 00:00:00', $timestamp);
        $endDate = date('Y-m-d 23:59:59', $timestamp);
        $bookingStatus = 'Completed';
        $data['data'] = $this->reporting_utils->get_partners_booking_report_chart_data($startDate, $endDate, $bookingStatus);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/show_partners_booking_report_chart', $data);
    }

    /**
     * @Desc: This function is used to show Partner completed bookings on ajax call
     * on the basis of selected range
     * @params: void()
     * @return: view
     *
     */
    function get_partners_booking_report_chart() {
        $sDate = $this->input->post('sDate');
        $eDate = $this->input->post('eDate');
        $bookingStatus = $this->input->post('booking_status');
        $startDate = date('Y-m-d 00:00:00', strtotime($sDate));
        $endDate = date('Y-m-d 23:59:59', strtotime($eDate));
        $data['data'] = $this->reporting_utils->get_partners_booking_report_chart_data($startDate, $endDate, $bookingStatus);
        $data['ajax_call'] = true;
        print_r(json_encode($data['data']));
    }

    /**
     * @Desc: This function is used to show the view of latest file uploaded in s3
     * @params:void
     * @return:void
     *
     */
    function download_latest_uploaded_file() {
        log_message('info', __FUNCTION__ . ' => Entering, Agent_id:' . $this->session->userdata('id'));
        $data['latest_file'] = $this->reporting_utils->get_all_latest_uploaded_file();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/download_latest_uploaded_file', $data);
    }

    /**
     * @Desc: This function is used to download latest file uploaded in s3
     * @params:string
     * @return:void
     *
     */
    function download_latest_file($flag) {
        //Getting latest entry form pincode_mapping_s3_upload_details table
        if ($flag === 'pincode') {
            $where = array('bucket_name' => 'vendor-pincodes');
        } elseif ($flag === 'price') {
            $where = array('file_type' => 'SF-Price-List');
        } elseif ($flag === 'appliance') {
            $where = array('file_type' => 'Partner-Appliance-Details');
        }
        $latest_file = $this->reporting_utils->get_latest_file($where);
        $filename = $latest_file[0]['file_name'];

        //S3 file Path
        if ($flag === 'price' || $flag == 'appliance') {
            $file_path = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $filename;
        } elseif ($flag === 'pincode') {
            $file_path = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-pincodes/" . $filename;
        }

        //Downloading File
        if (!empty($filename)) {

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            readfile($file_path);
            exit;
        } else {
            //Logging_error
            log_message('info', __FUNCTION__ . ' No latest file has been found to be uploaded.');
        }
    }
    
    /**
     * @Desc: This function is used to send summary email to partner 
     * @params:$partner_data array
     * @params:$csv_file array
     * @params:$send_as_attachment boolean => Set to true if wants to send file as attachment and false if wants to send link to the file. 
     * @return:$response boolean
     *
     */
    function generate_partner_summary_email_data($partner_data, $csv_file,$csv_file_name = "", $send_as_attachment = true) {
        
        $subject = "247around Services Report - " . $partner_data['public_name'] . " - " . date('d-M-Y');
        $accountManagerData = $this->miscelleneous->get_am_data($partner_data['id']);
        $am_email = "";
        if(!empty($accountManagerData)){
            $am_email = ", ".$accountManagerData[0]['official_email'];
        }
        if($this->session->userdata('employee_id')){
            $cc = $partner_data['summary_email_cc'].",".$this->session->userdata('official_email');
        }else{
            $cc = $partner_data['summary_email_cc'].$am_email;
        }
        $emailBasicDataArray['to'] = $partner_data['summary_email_to'];
        $emailBasicDataArray['cc'] = $cc;
        $emailBasicDataArray['subject'] = $subject;
        $emailBasicDataArray['from'] = NOREPLY_EMAIL_ID;
        $emailBasicDataArray['fromName'] = "247around Team";        
        //$emailTemplateDataArray['templateId'] = PARTNER_SUMMARY_EMAIL_TEMPLATE;
        $emailTemplateDataArray['dynamicParams'] = $this->partner_model->get_partner_summary_params($partner_data['id']);
        $emailTemplateDataArray['send_as_attachment'] = $send_as_attachment;
        $emailTemplateDataArray['original_file_name'] = $csv_file_name;
        if(!empty($emailTemplateDataArray['dynamicParams'])){
            $emailAttachmentDataArray['type'] = "csv";
            $emailAttachmentDataArray['fileName'] = "247around-Services-Consolidated-Data - " . date('d-M-Y');
            $emailAttachmentDataArray['filePath'] = $csv_file;
            $email_body = $this->load->view('employee/partner_summary_email_template',$emailTemplateDataArray,true);
            if(!$send_as_attachment)
            {
                $csv_file = "";
            }
            $send_email = $this->notify->sendEmail($emailBasicDataArray['from'], $emailBasicDataArray['to'], $emailBasicDataArray['cc'], "", $subject, $email_body, $csv_file,"summary_report");
            //$emailStatus = $this->send_grid_api->send_email_using_send_grid_templates($emailBasicDataArray, $emailTemplateDataArray, $emailAttachmentDataArray);
            if ($send_email) {
                log_message('info', __METHOD__ . ": Mail sent successfully for Partner: " . $partner_data['public_name']);
                $response = true;
            } else {
                log_message('info', __METHOD__ . ": Mail could not be sent for Partner: " . $partner_data['public_name']);
                $response = false;
            }
        }else{
            log_message('info', __METHOD__ . ": No data found for : " . $partner_data['public_name']);
            $response = true;
        }
        
        
        
        return $response;
    }
    function send_jeeves_requested_format_report($partnerID){
        $subject = "247around Services Report  - Jeeves - " . date('d-M-Y');
        $newCSVFileName = "Booking_summary_" . date('j-M-Y-H-i-s') ."_".$partnerID.".csv";
        $csv = TMP_FOLDER . $newCSVFileName;
        $report = $this->partner_model->get_partner_leads_csv_for_summary_email($partnerID,1);
        $delimiter = ",";
        $newline = "\r\n";
        $new_report = $this->dbutil->csv_from_result($report, $delimiter, $newline);
        log_message('info', __FUNCTION__ . ' => Rendered CSV');
        write_file($csv, $new_report);
        //Upload File On AWS and save link in file_upload table
        $this->save_partner_summary_report($partnerID,$newCSVFileName,$csv);
        $emailTemplateDataArray['jeevesDate'] = $this->partner_model->get_partner_report_overview_in_percentage_format($partnerID,"date(booking_details.create_date)");
        $emailTemplateDataArray['aroundDate'] = $this->partner_model->get_partner_report_overview_in_percentage_format($partnerID,"STR_TO_DATE(booking_details.initial_booking_date,'%d-%m-%Y')");
        $email_body = $this->load->view('employee/partner_report',$emailTemplateDataArray,true);
        $this->notify->sendEmail(NOREPLY_EMAIL_ID,"bsdflipkart@jeeves.co.in, radha.c@jeeves.co.in, naveen.n@jeeves.co.in, manish.agarwal@flipkart.com, vinesh.poojari@flipkart.com, sathis.s@mnw.co.in, dilipkumar.ms@flipkart.com", "anuj@247around.com,nits@247around.com", "", 
                $subject, $email_body,
                $csv,"partner_summary_report_percentage_format");
        unlink($csv);
    }
    /*
     * This Function is use to upload Partner Summary Report on Aws and saved that link in file_upload table
     */
    function save_partner_summary_report($partnerID,$fileName,$filePath){
        $bucket = BITBUCKET_DIRECTORY;
        $directory_xls = "summary-excels/" . $fileName;
        $this->s3->putObjectFile($filePath, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
        $fileData['entity_type'] = "Partner";
        $fileData['entity_id'] = $partnerID;
        $fileData['file_type'] = "Partner_Summary_Reports";
        $fileData['file_name'] = $directory_xls;
        $this->reusable_model->insert_into_table("file_uploads",$fileData);
    }
    function old_summary_report_view($partnerID){
        $where['entity_type'] = 'Partner';
        $where['entity_id'] = $partnerID;
        $where['file_type'] = "Partner_Summary_Reports";
        $limitArray['length'] = 50;
        $limitArray['start'] = "";
        $join['partners'] = "partners.id = file_uploads.entity_id";
        $orderBYArray["file_uploads.create_date"] = "DESC";
        $data['summaryReports'] = $this->reusable_model->get_search_result_data("file_uploads","file_name,date(file_uploads.create_date) as date,partners.public_name",
                $where,$join,$limitArray,$orderBYArray,NULL,NULL,array());
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/partner_summary_report_list', $data);
    }
    /*
     * This Function is used to send D0 Report to Partners
     */
    function send_days_format_reports_to_all_partners(){
        $partnersArray[0] = array("id"=>WYBOR_ID,"public_name"=>'Wybor');
        $partnersArray[1] = array("id"=>AKAI_ID,"public_name"=>'Akai');
        $partnersArray[2] = array("id"=>SALORA_ID,"public_name"=>'Salora');
        $partnersArray[3] = array("id"=>T_SERIES_PARTNER_ID,"public_name"=>'T-Series');
        $partnersArray[4] = array("id"=>MURPHY_ID,"public_name"=>'Murphy');
        $partnersArray[4] = array("id"=>JEEVES_ID,"public_name"=>'Jeeves');
        foreach($partnersArray as $partners){
            $emailTemplateDataArray['aroundDate'] = $this->partner_model->get_partner_report_overview_in_percentage_format($partners['id'],"STR_TO_DATE(booking_details.initial_booking_date,'%d-%m-%Y')");
            $email_body = $this->load->view('employee/partner_report',$emailTemplateDataArray,true);
            $subject = "247around Services Report  - ".$partners['public_name']." - " . date('d-M-Y');
            $this->notify->sendEmail(NOREPLY_EMAIL_ID,"anuj@247around.com,nits@247around.com", "arunk@247around.com,souvikg@247around.com,suresh@247around.com,oza@247around.com",
                    "chhavid@247around.com", $subject, $email_body,"","partner_summary_report_percentage_format");
        }
    }
    
    /**
     * This function sends mail to the partner for agent wise bookings summary report on a specific date
     * @author Prity Sharma
     * @date 24-06-2019
     * @param type $partner_id
     * @param type $date_report
     * @return NULL
     */
    function send_booking_summary_mail_to_partner($partner_id = "", $date_report = "") {
        $partner_id = !empty($partner_id) ? $partner_id : '247130';
        $date_report = !empty($date_report) ? $date_report : date('d-m-Y', strtotime(' -1 day'));
        $this->booking_summary->send_booking_summary_mail_to_partner($partner_id, $date_report);
    }
}
