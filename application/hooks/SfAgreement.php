<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SfAgreement
 *
 * @author gautam
 */
class SfAgreement extends CI_Hooks {

    private $CI;

    /**
     *  @desc : get Codeigniter instance
     */
    function __construct() {
        
        $this->CI = &get_instance();
        
        $this->CI->load->model('reusable_model');
        $this->CI->load->model('vendor_model');
        $this->CI->load->model('around_scheduler_model');
    }

    /**
     * @desc : This function will check user session. If session is distroy send it to login page
     * @return : void
     */
    public function check_sf_login() {
        
        if ($this->CI->input->is_ajax_request()) {
            return;
        }
        $segments = $this->CI->uri->segments;
        $this->is_sf_login($segments);
    }

    function show_agreement($sf_id = null) {
        $data = array();
        if (empty($sf_id) || $sf_id == null) {
            return;
        }
        $sf_details = $this->is_sf_exist($sf_id);
        if ($sf_id != null && !$sf_details['result']) {
            $sf_details = array(
                'sf_id' => $sf_id,
                'is_accepted' => 0
            );
            $this->CI->reusable_model->insert_into_table('sf_agreement_status',$sf_details);
            $sf_details = $this->is_sf_exist($sf_id);
        } else if ($sf_id != null && $sf_details['result'] && $sf_details['sf_data']['is_accepted'] == 1) {
            return;
        }

        $created_date = date_create(date('Y-m-d', strtotime($sf_details['sf_data']['created_at'])));
        $current_date = date_create(date('Y-m-d'));
        $diff_days = date_diff($current_date, $created_date);
        $data['sf_id'] = $sf_id;
        $data['days'] = $diff_days->format('%a');
        $data['skip_btn'] = ($diff_days->format('%a') >= 0 && $diff_days->format('%a') < $sf_details['sf_data']['allow_days']) ? true : false;

        $template = $this->CI->reusable_model->get_search_result_data('sf_agreement_template','*', array('active' => 1),'','','','','');
        
        $sf = $this->CI->around_scheduler_model->get_sf_details($sf_id);
        $sf_login = $this->CI->reusable_model->get_search_result_data('service_centers_login','*',array('id' => $this->CI->session->userdata('service_center_agent_id')),'','','','','');
        
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
        if(!empty($sf[0]['rm_email'])){
            $rm_email =", ".$sf[0]['rm_email'];
        }

        $asm_poc_email = "";
        if (!empty($sf[0]['asm_email'])) {
            $asm_poc_email = ", ".$sf[0]['asm_email'];
        }
        
        $tmpData['email_send'] = $sf[0]['owner_email'].$rm_email.$asm_poc_email ;

        $data['template']['template'] = vsprintf($template[0]['template'], $tmpData);
        $this->CI->load->view('service_centers/sf_agreement_view', $data);
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

    /**
     *  @desc : This function will Capcture agent action
     *  @return : 
     */
    function is_sf_login($segment) {
        if ($this->CI->session->userdata('loggedIn') == TRUE && $this->CI->session->userdata('userType') == 'service_center' && !empty($this->CI->session->userdata('service_center_id')) && $this->CI->session->userdata('is_sf') == 1 && $this->CI->session->userdata('is_wh') == 0) {
            $sf_skip_sf_id = 'sf_skip_'.$this->CI->session->userdata('service_center_id');
            if((isset($_COOKIE[$sf_skip_sf_id])) && $_COOKIE[$sf_skip_sf_id] == 1){
                return;
            }
            $this->show_agreement($this->CI->session->userdata('service_center_id'));
        }
    }

    /**
     *  @desc : check sf is exist or not in sf_agreement_status table
     *  @return : Boolean true/false
     */
    function is_sf_exist($sf_id) {
        $where = 'sf_id = '.$sf_id;
        $sf_details = $this->CI->reusable_model->get_search_result_data('sf_agreement_status','*',$where,'','','','','');
        if (count($sf_details) > 0) {
            return array('sf_data' => $sf_details[0], 'result' => true);
        }
        return array('sf_data' => null, 'result' => false);
    }

}