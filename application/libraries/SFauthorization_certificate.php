<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
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
                if ($vendor_id != NULL && $vendor_id != $sf_detail['id']) {
                    continue;
                }
                $sf_id = $sf_detail['id'];
                $html = $this->create_auth_certificate_html($sf_detail);
                if ($html != '') {
                    // pdf file name
                    $filename = preg_replace('/[\s\/]+/', '', $sf_detail['company_name']) . '_auth_certificate_' . time();
                    // store autorization certificate into S3 bucket
                    $s3_folder = 'authorization_certificate';
                    $backgournd_url = 'url("' . base_url('images/247_letter_head.jpg') . '")';
                    // Convert HTML to pdf and upload pdf to S3 bucket and get responce data
                    $response_data = $this->CI->miscelleneous->convert_html_to_pdf($html, $sf_detail['id'], $filename, $s3_folder, $backgournd_url);
                    $response_data = json_decode($response_data, true);
                    if ($response_data['response'] == 'Success') {
                        $response_file_name = $response_data['output_pdf_file'];
                        $this->CI->sf_authorization_model->update_authorization_certificate_details($sf_id, $response_file_name, $finacial_year);
                    }
                }
            }
        }
        return TRUE;
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
            $html = $this->CI->load->view('employee/sf_certificate_view', $data, true);
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
