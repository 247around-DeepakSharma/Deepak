<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * This controller have all Saas Specific Functions
 */

class Saas_Partner extends CI_Controller {
   function __Construct() {
        parent::__Construct();
        $this->load->model('partner_model');
        $this->load->library('miscelleneous');
    }
    
    function checkEmployeeUserSession(){
         if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee') && !empty($this->session->userdata('id'))) {
            return TRUE;
        } else {
            $this->session->sess_destroy();
            redirect(base_url() . "employee/login");
        }
    }
    
    function profile($partner_id=""){
        $this->checkEmployeeUserSession();
        $partner_not_like ='';
        $service_brands = array();
        $active = 1;
        $ac= 'All';
        $partnerType= array(OEM);
        $query = $this->partner_model->get_partner_details_with_soucre_code($active,$partnerType,$ac,$partner_not_like,$partner_id, null);
        foreach ($query as $key => $value) {
            //Getting Appliances and Brands details for partner
            $service_brands[] = $this->partner_model->get_service_brands_for_partner($value['id']);
        }
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/profile', array('query' => $query, 'service_brands' => $service_brands,'active'=>$active,'partnerType'=>$partnerType,'ac'=>$ac));
    }
}
