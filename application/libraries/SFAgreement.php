<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SFAgreement
 *
 * @author gautam
 */
class SFAgreement {

    var $CI;

    function __construct() {
        $this->CI = & get_instance();
        $this->CI->load->model('around_scheduler_model');
        $this->CI->load->model('booking_model');
        $this->CI->load->library('email');
        $this->CI->load->library('notify');
        $this->CI->load->library('Miscelleneous');
    }

    function send_email_agreement_to_sf($sf_email = NULL) {
        $sf_details = $this->CI->around_scheduler_model->get_sf_details($sf_email);
        if (!empty($sf_details) && count($sf_details) > 0) {
            foreach ($sf_details as $sf_detail) {
                if (empty($sf_detail['owner_email'])) {
                    return;
                }
                // get email template
                $email_template = $this->CI->booking_model->get_booking_email_template('agreement_email_template');

                // prepare mail
                $to = $sf_detail['owner_email'];
                $from = $email_template[2];
                $cc = '';
                if (!empty($sf_detail['rm_email'])) {
                    $cc = $email_template[3] . ',' . $sf_detail['rm_email'];
                }
                if (!empty($sf_detail['asm_email'])) {
                    $cc = $email_template[3] . ',' . $sf_detail['asm_email'];
                }
                if ($to != '') {
                    // Genrate secret code
                    $secret_code = $this->generate_string(10);
                    // Genrate agreement copy of SF
                    $data['sf_details'] = $sf_detail;
                    $html = $this->CI->load->view('employee/sf_agreement_view', $data, true);
                    $filename = str_replace(' ', '', $sf_detail['company_name']) . '_agreement_' . time();
                    $s3_folder = 'sf_agreements';
                    $response_data = $this->CI->miscelleneous->convert_html_to_pdf($html, $sf_detail['id'], $filename, $s3_folder);
                    $response_data = json_decode($response_data, true);
                    if ($response_data['response'] == 'Success') {
                        $response_file_name = $response_data['output_pdf_file'];
                        $agreement_data = array(
                            'agreement_email_sent' => 1,
                            'agreement_secret_code' => $secret_code,
                            'agreement_email_sent_date' => date('Y-m-d'),
                            'agreement_email_reminder_date' => date('Y-m-d', strtotime("+7 day")),
                            'agreement_file_name' => $response_file_name,
                            'is_sf_agreement_signed' => 0
                        );
                        $where = 'id = ' . $sf_detail['id'];
                        $this->CI->reusable_model->update_table('service_centres', $agreement_data, $where);

                        // Send Email
                        $subject = 'Agreement sign link';

                        $link_url = base_url() . 'employee/SFAgreement?sf_email=' . base64_encode($to) . '&code=' . base64_encode($secret_code);

                        $body = '<p>Hi ' . $sf_detail['owner_name'] . ',<br />
                            Please click on below link add sign into agreement document. <br />';
                        $body .= $link_url;
                        $this->CI->notify->sendEmail($from, $to, $cc, NULL, $subject, $body, NULL, NULL);
                    }
                }
            }
        }
        return true;
    }

    // Genrate alphanumeric secret code
    function generate_string($strength = 16) {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $input_length = strlen($permitted_chars);
        $random_string = '';
        for ($i = 0; $i < $strength; $i++) {
            $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }

        return $random_string;
    }

    // Send reminder weekly
    function send_reminder($reminder_date = NULL) {
        if ($reminder_date != NULL) {
            $sf_details = $this->CI->around_scheduler_model->get_sf_details(null, 1, 1, $reminder_date);
            if (!empty($sf_details) && count($sf_details) > 0) {
                foreach ($sf_details as $sf_detail) {
                    if (empty($sf_detail['owner_email'])) {
                        return;
                    }
                    // get email template
                    $email_template = $this->CI->booking_model->get_booking_email_template('agreement_email_template');

                    // prepare mail
                    $to = $sf_detail['owner_email'];
                    $from = $email_template[2];
                    $cc = '';
                    if (!empty($sf_detail['rm_email'])) {
                        $cc = $email_template[3] . ',' . $sf_detail['rm_email'];
                    }
                    if (!empty($sf_detail['asm_email'])) {
                        $cc = $email_template[3] . ',' . $sf_detail['asm_email'];
                    }
                    if ($to != '') {
                        $secret_code = $this->generate_string(10);
                        $agreement_data = array(
                            'agreement_secret_code' => $secret_code,
                            'agreement_email_sent_date' => date('Y-m-d'),
                            'agreement_email_reminder_date' => date('Y-m-d', strtotime("+7 day")),
                            'is_sf_agreement_signed' => 0
                        );
                        $where = 'id = ' . $sf_detail['id'];
                        $this->CI->reusable_model->update_table('service_centres', $agreement_data, $where);

                        // Send Email
                        $subject = 'Reminder : Agreement sign link';

                        $link_url = base_url() . 'employee/SFAgreement?sf_email=' . base64_encode($to) . '&code=' . base64_encode($secret_code);

                        $body = '<p>Hi ' . $sf_detail['owner_name'] . ',<br />
                            Please click on below link add sign into agreement document. <br />';
                        $body .= $link_url;
                        $this->CI->notify->sendEmail($from, $to, $cc, NULL, $subject, $body, NULL, NULL);
                    }
                }
            }
        }
        return true;
    }

}
