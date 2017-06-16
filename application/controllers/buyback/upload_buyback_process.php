<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Upload_buyback_process extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();

        $this->load->helper(array('form', 'url'));

        $this->load->library('form_validation');

        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            echo PHP_EOL . 'Terminal Access Not Allowed' . PHP_EOL;
            redirect(base_url() . "employee/login");
        }
    }
    
    function index(){
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('buyback/order_details_file_upload');
        $this->load->view('dashboard/dashboard_footer');
    }
    
    function upload_file(){
        if($_FILES['file']['name'] && $_FILES['file']['size'] > 0){
            echo json_encode(array("code"=>"247","msg"=>"success"));
        }else{
            echo json_encode(array("code"=>"-247","msg"=>"error"));
        }
        
    }
    
}