<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


/**
 * Partner wise Bookings summary 
 *
 * Generate Partner wise Booking summary repory on daily basis 
 * @author Prity Sharma
 */
class booking_summary {

    private $My_CI;

    function __Construct() {
	$this->My_CI = & get_instance();

	$this->My_CI->load->model('partner_model');
        $this->My_CI->load->dbutil();
        $this->My_CI->load->library('s3');
        $this->My_CI->load->library('notify');
    }
    
    /**
     * This function generates partner's agent wise booking detail report and send email for the same
     * @date 24-04-2019
     * @author Prity Sharma
     * @param type $partner_id
     * @param type $date_report
     */
    function send_booking_summary_mail_to_partner($partner_id = "", $date_report = "") {
        
        $newCSVFileName = "Booking_summary_" . date('j-M-Y', strtotime($date_report)) . ".csv";
        $csv = TMP_FOLDER . $newCSVFileName;
        $bucket = BITBUCKET_DIRECTORY;
        $directory_xls = "booking-summary-excels/" . $newCSVFileName;
        
        if(!empty($partner_id) && !empty($date_report))
        {
            $report = $this->My_CI->partner_model->get_agent_wise_booking_summary($partner_id,$date_report);
            $delimiter = ",";
            $newline = "\r\n";
            $new_report = $this->My_CI->dbutil->csv_from_result($report, $delimiter, $newline);
            log_message('info', __FUNCTION__ . ' => Rendered CSV');
            write_file($csv, $new_report);
            //Upload File On AWS and save link in file_upload table
            $this->My_CI->s3->putObjectFile($csv, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);   
            $subject = $newCSVFileName;
            $email_body = "PFA";
            $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID,"pritys@247around.com", "","", $subject, $email_body, $csv,$newCSVFileName);
            if(file_exists($csv))
            {
                unlink($csv);
            }       
        }  
    }

}
