<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mobile_entry extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();

        $this->load->model('user_model');
        $this->load->model('employee_model');
        $this->load->model('partner_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('miscelleneous');
        $this->load->library("pagination");
        $this->load->library("session");
    }

    /**
     * @desc : This function is used to load the view to mobile entry form
     * 
     */

    public function index() {

        $results['user_id'] = $this->session->userdata('id');
        $results['emp_name'] = $this->session->userdata("emp_name");



        $select = "Select count(agent_id) as total, agent ";
        $where = "";
        $result['group_by'] = " GROUP BY agent_id";
        $result['order_by'] = " ORDER BY id";
        $results['partner'] = $this->partner_model->get_all_partner_source();
        $results['data'] = $this->user_model->getMobileEntry($select,$where,$result);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/mobileEntryFrom', $results);
    }

    /**
     * @desc : This function is used to validate mobile number
     * 
     */

    function process_mobile_number_validation(){

        $mobile_number = $this->input->post('mobile_number');
        $select = "Select * ";
        $where = " WHERE mobile = '$mobile_number'";
        $result['group_by'] = "";
        $result['order_by'] = "";

        $results['data'] = $this->user_model->getMobileEntry($select,$where,$result);

        if($results['data']){
            echo true;
        }else{
            echo false;
        }
    }

    /**
     * @desc : This function is used to save mobile entry data
     * 
     */

    function save_mobile_entry_data(){

        $post['agent_id'] = $this->input->post('entity_id');
        $post['agent'] = $this->input->post('entity');
        $post['full_name'] = $this->input->post('fullname');
        $post['mobile'] = $this->input->post('mobile_number');
        $post['brand_name'] = $this->input->post('brand_name');
        $post['entity_type'] = $this->input->post('role');
        $post['created_date'] = date("Y-m-d H:i:s");
        //save the data
        $user_id = $this->user_model->save_mobile_entry($post);
        if($user_id){
            echo TRUE;
        }else{
            echo FALSE;
        }

    }
}