<?php
/**
 * Description of SFAgreement
 *
 */
class Sf_agreement extends CI_Controller {

    //put your code here
    function __construct() {
        parent::__construct();
        $this->load->model('around_scheduler_model');
        $this->load->model('reusable_model');
        $this->load->model('booking_model');
        $this->load->library('miscelleneous');
        $this->load->library("session");
    }

    function process_sf_agreement_request() {
        if (!$this->input->is_ajax_request()) {
            die;
        }
        $sf_id = $this->input->post('sf_id');
        $sf_deatils = $this->around_scheduler_model->get_sf_details($sf_id);
        if (!empty($sf_deatils)) {
            if (empty($sf_deatils[0]['owner_email'])) {
                echo json_encode(array('success' => 0), true);
                die;
            }
            // get email template
            $email_template = $this->booking_model->get_booking_email_template(AGREEMENT_EMAIL);
            if (!empty($email_template)) {
                
            }
            $template = $this->reusable_model->get_search_result_data('sf_agreement_template', '*', '', '', '', '', '', '');
            if (!empty($email_template)) {
                $agreement_template = $template[0];

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
                    // Genrate agreement copy of SF
                    $data['sf_details'] = $sf_deatils[0];
                    $data['agreement_ip_address'] = $this->getIPAddress();
                    $data['email'] = $sf_deatils[0]['owner_email'];
                    $data['agreement_sign_datetime'] = date('Y-m-d H:i:s');
//                    $agreement_template['template'] .= '<table><tr><td>' . $data['agreement_ip_address'] . '</td></tr>';
//                    $agreement_template['template'] .= '<tr><td>' . $data['email'] . '</td></tr>';
//                    $agreement_template['template'] .= '<tr><td>' . $data['agreement_sign_datetime'] . '</td></tr></table>';
                    
                    $agreement_template['template'] .= '';

                    //$html = $this->load->view('employee/sf_agreement_view', $data, true);
                    $filename = str_replace(' ', '', $sf_deatils[0]['company_name']) . '_agreement_' . time() . '.pdf';
                    $s3_folder = 'sf_agreements';
                    $response_data1 = $this->miscelleneous->convert_html_to_pdf($agreement_template['template'], $sf_deatils[0]['id'], $filename, $s3_folder, '');
                    $response_data = json_decode($response_data1, true);
                    if ($response_data['file_name'] != '' && $response_data['temp_path'] != '') {

                        $capture_data = array('is_accepted' => 1, 'agreement_file' => $filename, 'ip_address' =>
                            $this->getIPAddress(), 'agent_id' => $this->session->userdata('service_center_agent_id'),
                            'accepted_at' => date('Y-m-d H:i:s'));
                        $where = 'sf_id = ' . $sf_deatils[0]['id'];
                        $this->reusable_model->update_table('sf_agreement_status', $capture_data, $where);

                        // Send Email
                        $subject = 'SF Agreement document';

                        $body = '<p>Hi ' . $sf_deatils[0]['owner_name'] . ',<br />
                            Please download your attached signed agreement. <br />';
                        $this->notify->sendEmail($from, $to, $cc, NULL, $subject, $body, $response_data['temp_path'], NULL);
                        unlink($response_data['temp_path']);
                        echo json_encode(array('success' => 1), true);
                        die;
                    }
                }
            }
        }
        echo json_encode(array('success' => 0), true);
        die;
    }

    function getIPAddress() {
        //whether ip is from the share internet  
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        //whether ip is from the proxy  
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
//whether ip is from the remote address  
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

}