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
class SFAgreement extends CI_Controller {

    //put your code here
    function __construct() {
        parent::__construct();
        $this->load->model('around_scheduler_model');
        $this->load->model('reusable_model');
        $this->load->model('booking_model');
        $this->load->library('miscelleneous');
    }

    function index() {
        $sf_email = base64_decode($this->input->get('sf_email'));
        $secret_code = base64_decode($this->input->get('code'));
        if (!empty($sf_email) && !empty($secret_code)) {

            $sf_details = $this->around_scheduler_model->get_sf_details($sf_email, 1);
            if ($sf_details[0]['owner_email'] != $sf_email || $sf_details[0]['agreement_secret_code'] != $secret_code) {
                echo 'Invalid url. please contact your RM/ASM';
                die;
            }
            $data['sf_details'] = $sf_details[0];
            $data['sf_email'] = $sf_email;
            $data['secret_code'] = $secret_code;
            $data['file_path'] = S3_WEBSITE_URL . 'sf_agreements/' . $sf_details[0]['agreement_file_name'];
            $this->load->view('employee/sf_agreement_sign_view', $data);
        }
    }

    function capture_sf_details() {

        $sf_email = $this->input->post('sf_email');
        $sf_ip = $this->input->post('sf_ip');
        $secret_code = $this->input->post('secret_code');
        $sf_deatils = $this->around_scheduler_model->get_sf_details($sf_email,1);
        if (!empty($sf_deatils)) {
            if (empty($sf_deatils[0]['owner_email'])) {
                return;
            }
            // get email template
            $email_template = $this->booking_model->get_booking_email_template('agreement_email_template');

            // prepare mail
            $to = $sf_deatils[0]['owner_email'];
            $from = $email_template[2];
            $cc = '';
            if (!empty($sf_deatils[0]['rm_email'])) {
                $cc = $email_template[3] . ',' . $sf_deatils[0]['rm_email'];
            }
            if (!empty($sf_deatils[0]['asm_email'])) {
                $cc = $email_template[3] . ',' . $sf_deatils[0]['asm_email'];
            }
            if ($to != '') {
                // Genrate secret code
                $secret_code = '';
                // Genrate agreement copy of SF
                $data['sf_details'] = $sf_deatils[0];
                $data['agreement_ip_address'] = $sf_ip;
                $data['email'] = $sf_email;
                $data['agreement_sign_datetime'] = date('Y-m-d H:i:s');
                $html = $this->load->view('employee/sf_agreement_view', $data, true);
                $filename = str_replace(' ', '', $sf_deatils[0]['company_name']) . '_agreement_' . time();
                $s3_folder = 'sf_agreements';
                $response_data = $this->miscelleneous->convert_html_to_pdf($html, $sf_deatils[0]['id'], $filename, $s3_folder);
                $response_data = json_decode($response_data, true);
                if ($response_data['response'] == 'Success') {
                    $response_file_name = $response_data['output_pdf_file'];
                    $capture_data = array(
                        'agreement_email_sent' => 0,
                        'agreement_secret_code' => $secret_code,
                        'agreement_email_reminder_date' => '0000-00-00',
                        'agreement_file_name' => $response_file_name,
                        'is_sf_agreement_signed' => 1,
                        'agreement_sign_datetime' => date('Y-m-d H:i:s')
                    );
                    $where = 'id = ' . $sf_deatils[0]['id'];
                    $this->reusable_model->update_table('service_centres', $capture_data, $where);

                    // Send Email
                    $subject = 'Agreement sign link';

                    $link_url = S3_URL . $s3_folder . '/' . $response_file_name;

                    $body = '<p>Hi ' . $sf_deatils[0]['owner_name'] . ',<br />
                            Please download your signed agreement by click on below link add sign into agreement document. <br />';
                    $body .= $link_url;
                    $this->notify->sendEmail($from, $to, $cc, NULL, $subject, $body, NULL, NULL);
                    echo json_encode(array('success' => 1), true);
                    die;
                }
            }
        }
        echo json_encode(array('success' => 0), true);
        die;
    }

}
