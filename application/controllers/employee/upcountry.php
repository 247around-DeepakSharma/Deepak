<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Upcountry extends CI_Controller {

    /**
     * load list model and helpers
     */
    function __Construct() {
	parent::__Construct();

	$this->load->model('upcountry_model');
        $this->load->model('vendor_model');

	if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
	    return TRUE;
	} else {
	    redirect(base_url() . "employee/login");
	}
    }
    
    function index(){}
    /**
     * @desc: This method is used to load view to assign SC for Upcountry
     */
    function assign_sc_to_upcountry(){
        log_message('info', __FUNCTION__);
        $data['service_center_id'] = $this->input->post("service_center_id");
        $data['state'] = $this->input->post("state");
        if(!empty($data['service_center_id'])){
            $data['all_state'] = $this->vendor_model->selectSate();

            $this->load->view('employee/header/'.$this->session->userdata('user_group'));
            $this->load->view('employee/assign_vendor_to_upcountry',$data);
        } else {
            echo "Please Vist Again";
        }
    }
    /**
     * @desc: This method is used to assign upcountry to SF.
     * This method get input field inthe Array form.
     * Insert All data into table As a Batch
     * @param String $service_center_id
     */
    function add_sub_sf_upcountry($service_center_id){
        log_message('info', __FUNCTION__. " Service Center Id ". $service_center_id);
        $state = $this->input->post('state');
        $district = $this->input->post('city');
        $pincode = $this->input->post('pincode');
        $charges = $this->input->post('charges');
        $data = array();
        foreach ($state as $key => $value) {
            $data[$key]['state'] = $value;
            $data[$key]['district'] = $district[$key];
            $data[$key]['pincode'] = $pincode[$key];
            $data[$key]['upcountry_rate'] = $charges[$key];
            $data[$key]['service_center_id'] = $service_center_id; 
            $data[$key]['create_date'] = date("Y-m-d H:i:s"); 
        }
       
        if(!empty($data)){
            $response = $this->upcountry_model->insert_batch_sub_sc_details($data);
            if($response){
                $userSession = array('success' => 'Upcountry Charges Added');
                $this->session->set_userdata($userSession);
                log_message('info', __FUNCTION__. " Added Upcountry Charges for SC id ". $service_center_id);
            } else {
                $userSession = array('error' => 'Upcountry Charges Insertion Failed');
                $this->session->set_userdata($userSession);
                log_message('info', __FUNCTION__. " Upcountry Charges Not Added ". print_r($data));
            }
            redirect(base_url()."employee/vendor/viewvendor");
        } else {
            $userSession = array('error' => 'Upcountry Charges Insertion Failed');
            $this->session->set_userdata($userSession);
            log_message('info', __FUNCTION__. " Upcountry Charges Not Added  service center id". print_r($service_center_id));
            redirect(base_url()."employee/vendor/viewvendor");
        }
    }
    
    function get_upcountry_failed_details(){
        $upcountry_details['details'] = $this->upcountry_model->get_upcountry_failed_details();
        foreach ($upcountry_details['details'] as $key => $value) {
            $where1 = array('service_center_id' => $value['assigned_vendor_id']);
            
            $upcountry_details['details'][$key]['pincode_details'] = $this->upcountry_model->get_sub_service_center_details($where1);
        }
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/upcountry_failed_details', $upcountry_details);
    }
}