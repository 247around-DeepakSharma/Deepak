<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Partner wise Bookings summary 
 *
 * Generate Partner wise Booking summary repory on daily basis 
 * @author Prity Sharma
 */
class reporting_lib {

    private $My_CI;

    function __Construct() {
        $this->My_CI = & get_instance();

        $this->My_CI->load->model('reusable_model');
        $this->My_CI->load->model('partner_model');
        $this->My_CI->load->model('booking_model');
        $this->My_CI->load->dbutil();
        $this->My_CI->load->library('s3');
        $this->My_CI->load->library('notify');
    }

    /**
     * This function generates partner's agent wise booking detail report and send email for the same
     * This function will be call from Cron on daily basis
     * @date 24-04-2019
     * @author Prity Sharma
     * @param type $partner_id
     * @param type $date_report
     */
    function send_call_center_summary_mail_to_partner($partner_id = "", $date_report_start = "", $date_report_end = "") {

        $newCSVFileName = "call_center_summary_" . date('j-M-Y', strtotime($date_report_start)) . ".csv";
        $csv = TMP_FOLDER . $newCSVFileName;
        $bucket = BITBUCKET_DIRECTORY;
        $directory_xls = "call-center-summary-excels/" . $newCSVFileName;

        if (!empty($partner_id) && !empty($date_report_start)) {
//            $select = "partners.id, partners.summary_email_to, partners.summary_email_cc, "
//                    . " partners.summary_email_bcc, partners.public_name";
//            $where_get_partner = array('partners.id' => $partner_id, 'partners.is_active' => '1');
//            $partners = $this->My_CI->partner_model->getpartner_details($select, $where_get_partner, '1');

            $report = $this->My_CI->partner_model->get_agent_wise_call_center_booking_summary($partner_id, $date_report_start, $date_report_end);
            $delimiter = ",";
            $newline = "\r\n";
            $new_report = $this->My_CI->dbutil->csv_from_result($report, $delimiter, $newline);
            log_message('info', __FUNCTION__ . ' => Rendered CSV');
            write_file($csv, $new_report);
            $this->put_file_data($partner_id, $csv, $bucket, $directory_xls);
            $this->sendmail($date_report_start, $newCSVFileName, $csv, 'videocon_callcenter_report');
            if (file_exists($csv)) {
                unlink($csv);
            }
        }
    }

    function put_file_data($partner_id, $csv, $bucket, $directory_xls) {
        //Upload File On AWS and save link in file_upload table
        $this->My_CI->s3->putObjectFile($csv, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
        $fileData['entity_type'] = "Partner";
        $fileData['entity_id'] = $partner_id;
        $fileData['file_type'] = "Call_Center_Summary_Reports";
        $fileData['file_name'] = $directory_xls;
        $this->My_CI->reusable_model->insert_into_table("file_uploads", $fileData);
    }

    function sendmail($date_report, $newCSVFileName, $csv, $template) {
        
        $email_template = $this->My_CI->booking_model->get_booking_email_template($template);
        $subject = vsprintf($email_template[4], array($date_report));
        $message = $email_template[0];
        $email_from = $email_template[2];
        $to = $email_template[1];
        $cc = $email_template[3];
        $bcc = $email_template[5];
        $this->My_CI->notify->sendEmail($email_from, $to, $cc, $bcc, $subject, $message, $csv, $newCSVFileName);
    }

}
