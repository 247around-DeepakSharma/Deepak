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
        $sf = $this->around_scheduler_model->get_sf_details($sf_id);
        if (!empty($sf)) {
            if (empty($sf[0]['owner_email'])) {
                echo json_encode(array('success' => 0), true);
                die;
            }
            // get email template
            $email_template = $this->booking_model->get_booking_email_template(AGREEMENT_EMAIL);
            if (!empty($email_template)) {
                $template = $this->reusable_model->get_search_result_data('sf_agreement_template', '*', '', '', '', '', '', '');
                if (!empty($email_template)) {

                    // prepare mail
                    $to = $sf[0]['owner_email'];
                    $from = $email_template[2];
                    if ($to != '') {
                        $sf_login = $this->reusable_model->get_search_result_data('service_centers_login','*',array('id' => $this->session->userdata('service_center_agent_id')),'','','','','');

                        // Genrate agreement copy of SF
                        $tmpData['current_date'] = date('d/M/Y');
                        $tmpData['onboading_date'] = date('d/M/Y', strtotime($sf[0]['create_date']));
                        $tmpData['company_name'] = $sf[0]['company_name'];
                        $tmpData['owner_name'] = $sf[0]['owner_name'];
                        $tmpData['pan_number'] = $sf[0]['pan_no'];
                        $tmpData['gst_number'] = $sf[0]['gst_no'];
                        $tmpData['company_address'] = $sf[0]['address'];
                        $tmpData['district'] = $sf[0]['district'];
                        $tmpData['state'] = $sf[0]['state'];
                        $tmpData['full_name'] = $sf_login[0]['full_name'];
                        $tmpData['email'] = $sf_login[0]['email'];
                        $tmpData['ip_address'] = $this->getIPAddress();
                        $tmpData['accepted_date'] = date('d/M/Y H:i:s');
                        $rm_email = "";
                        if (!empty($sf[0]['rm_email'])) {
                            $rm_email = ", " . $sf[0]['rm_email'];
                        }

                        $asm_poc_email = "";
                        if (!empty($sf[0]['asm_email'])) {
                            $asm_poc_email = ", " . $sf[0]['asm_email'];
                        }
                        $tmpData['email_send'] = $sf[0]['owner_email'].$sf[0]['primary_contact_email']  . $rm_email . $asm_poc_email . "," . $email_template[3];

                        $agreement_template['template'] = vsprintf($template[0]['template'], $tmpData);

                        $cc = $email_template[3] . $sf[0]['owner_email'] .", ".$sf[0]['primary_contact_email']. $rm_email . $asm_poc_email;

                        $bcc = $email_template[5];
                        $filename = str_replace(' ', '', $sf[0]['company_name']) . '_agreement_' . time() . '.pdf';
                        $s3_folder = 'sf_agreements';
                        $response_data1 = $this->miscelleneous->convert_html_to_pdf($agreement_template['template'], $sf[0]['id'], $filename, $s3_folder, '');
                        $response_data = json_decode($response_data1, true);
                        if ($response_data['file_name'] != '' && $response_data['temp_path'] != '') {

                            $capture_data = array('is_accepted' => 1, 'agreement_file' => $filename, 'ip_address' =>
                                $this->getIPAddress(), 'agent_id' => $this->session->userdata('service_center_agent_id'),
                                'accepted_at' => date('Y-m-d H:i:s'));
                            $where = 'sf_id = ' . $sf[0]['id'];
                            $this->reusable_model->update_table('sf_agreement_status', $capture_data, $where);

                            // Send Email
                            $subject = $email_template[4];
                            log_message('info', __METHOD__. " ". $subject);

                            $body = $email_template[0];
                            $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $body, $response_data['temp_path'], NULL);
                            unlink($response_data['temp_path']);
                            echo json_encode(array('success' => 1), true);
                            die;
                        }
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