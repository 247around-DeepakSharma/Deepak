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

        $template = $this->CI->reusable_model->get_search_result_data('sf_agreement_template','*','','','','','','');
        $data['template'] = $template[0];
        $this->CI->load->view('service_centers/sf_agreement_view', $data);
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