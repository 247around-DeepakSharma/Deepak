<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SF_authorization_certificate
 *
 * @author gautam
 */
class SF_authorization_certificate extends CI_Controller{
    function __construct(){
        parent::__construct();
        $this->load->model('sf_authorization_model');
        $this->load->library('SFauthorization_certificate');
        $this->load->library('miscelleneous');
    }
    
    public function index() {
        $data['service_centers'] = $this->sf_authorization_model->get_all_active_sf_details();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/sf_authorization_certificates', $data);
    }
}
