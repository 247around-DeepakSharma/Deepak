<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class SFauthorization_certificate {

    var $CI;

    function __construct() {
        $this->CI = & get_instance();
        $this->CI->load->model('sf_authorization_model');
    }

    /*
     * This function will create new authorization certificate for SF while onboarding of SF
     * and start new finacial year 1-April
     */

    function create_new_certificate($vendor_id = NULL) {
        $finacial_year = $this->finacial_year();
        $sf_details = $this->CI->sf_authorization_model->get_sf_details($finacial_year);
        if (!empty($sf_details)) {
            foreach ($sf_details as $sf_detail) {
                if($vendor_id != NULL && $vendor_id != $sf_detail['id']){
                    continue;
                }
                $sf_id = $sf_detail['id'];
                $html = $this->create_auth_certificate_html($sf_detail);
                if ($html != '') {
                    // pdf file name
                    $filename = str_replace(' ', '', $sf_detail['company_name']) . '_auth_certificate_' . time();
                    // store autorization certificate into S3 bucket
                    $s3_folder = 'authorization_certificate';
                    // Convert HTML to pdf and upload pdf to S3 bucket and get responce data
                    $response = $this->convert_html_to_pdf($html, $filename, $s3_folder, $sf_id);
                    if ($response['response'] == 'Success') {
                        $response_file_name = $response['output_pdf_file'];
                        $this->CI->sf_authorization_model->update_authorization_certificate_details($sf_id, $response_file_name,$finacial_year);
                    }
                }
            }
        }
        return TRUE;
    }

    /*
     * Convert certificate HTMl to PDF and upload pdf to s3 bucket
     * retun array
     */

    function convert_html_to_pdf($html, $filename, $s3_folder, $sf_id) {

        log_message('info', __FUNCTION__ . " => Entering, SF ID: " . $sf_id);
        require_once __DIR__ . '/pdf/vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf();
        $t = $mpdf->WriteHTML($html);

        $tempfilePath = TMP_FOLDER . $filename;
        $mpdf->Output($tempfilePath, 'F');
        $response_data = array();
        if ($mpdf) {
            $is_file = $this->CI->s3->putObjectFile($tempfilePath, BITBUCKET_DIRECTORY, $s3_folder . "/" . $filename, S3::ACL_PUBLIC_READ);
            if ($is_file) {
                $response_data = array('response' => 'Success',
                    'response_msg' => 'PDF generated Successfully and uploaded on S3',
                    'output_pdf_file' => $filename,
                    'bucket_dir' => BITBUCKET_DIRECTORY,
                    'id' => $sf_id
                );
                //unlink($tempfilePath);
                return $response_data;
            } else {
                //return this response when PDF generated successfully but unable to upload on S3
                $response_data = array('response' => 'Error',
                    'response_msg' => 'PDF generated Successfully But Unable To Upload on S3',
                    'output_pdf_file' => $filename,
                    'bucket_dir' => BITBUCKET_DIRECTORY,
                    'id' => $sf_id
                );
                return $response_data;
            }
        } else {
            $response_data = array('response' => 'Error',
                'response_msg' => 'Error In Generating PDF File',
            );
            $to = DEVELOPER_EMAIL;
            $cc = "";
            $subject = "Job Card Not Generated By Mpdf";
            $msg = "There are some issue while creating pdf for SFauthorization_certificate/create_new_certificate $sf_id. Check the issue and fix it immediately";
            $this->CI->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $msg, JOB_CARD_NOT_GENERATED);
            return $response_data;
        }
       
    }

    /*
     * Create certificate HTMl
     * return html
     */

    function create_auth_certificate_html($sf_deatils) {
        $html = '';
        if ($sf_deatils) {
            $financial_year = '';
            $current_month = date('m');
            if ($current_month > 3) {
                $financial_year = '01 April ' . date('Y') . ' to 31 March ' . (date('Y') + 1);
            } else {
                $financial_year = '01 April ' . (date('Y') - 1) . ' to 31 March ' . date('Y');
            }
            $data['sf_deatils'] = $sf_deatils;
            $data['financial_year'] = $financial_year;
            $html = $this->CI->load->view('employee/sf_certificate_view',$data,true);
        }
        return $html;
    }

    /*
     * Current financial year
     */

    function finacial_year() {
        $financial_year = '';
        $current_month = date('m');
        if ($current_month > 3) {
            $financial_year = date('Y') . '-' . (date('Y') + 1);
        } else {
            $financial_year = (date('Y') - 1) . '-' . date('Y');
        }
        return $financial_year;
    }

}
