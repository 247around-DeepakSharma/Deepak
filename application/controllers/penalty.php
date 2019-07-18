<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 36000);

class Penalty extends CI_Controller {

    /**
     * load list model and helpers
     */
    function __Construct() {
	parent::__Construct();

	$this->load->model('penalty_model');
        $this->load->model('booking_model');
	$this->load->model('reporting_utils');
        $this->load->library('miscelleneous');
        $this->load->model('reusable_model');
	$this->load->helper(array('form', 'url'));
    }

    function penalty_on_service_center() {
	//$this->penalty_model->penalty_on_service_center_for_assigned_engineer();
        
	    $this->penalty_model->penalty_on_service_center_for_update_booking();
            // Inserting values in scheduler tasks log
            $this->reporting_utils->insert_scheduler_tasks_log(__FUNCTION__);
    }
    
     /**
     * @desc: This function is to view panelty details.
     *
     * Will display all the panelty details
     *
     * @return : array(of details) to view
     */
    function view_penalty_details() {
 
        $active = 1;
        $ac= 'All';
        if($this->input->post()){
           $active = $this->input->post('active');
        }

        $penalty_details = $this->penalty_model->get_panelty_details_data();
        
        $this->miscelleneous->load_nav_header();
        $this->load->view('penalty/view_penalty_details', array('penalty_details' => $penalty_details));
    }
    
    function get_penalty_detail_form($id = NULL) {
        
        if($this->input->post()) {
            $penalty_detail = [];
            $penalty_detail = $this->input->post();
            unset($penalty_detail['save']);
           
            if(is_null($id)) { 
                // insert into vendor_escalation_policy
                $vendor_escalation_policy_data = [
                    'escalation_reason' => $penalty_detail['escalation'],
                    'entity' => _247AROUND_EMPLOYEE_STRING,
                    'process_type' => 'escalation',
                    'active' => 1
                ];
                
                $escalation_id = $this->reusable_model->insert_into_table('vendor_escalation_policy', $vendor_escalation_policy_data);
                if(!empty($penalty_detail['apply_penalty'])) {
                    unset($penalty_detail['apply_penalty']);
                    unset($penalty_detail['escalation']);
                    $penalty_detail['escalation_id'] = $escalation_id;
                    $this->reusable_model->insert_into_table('penalty_details', $penalty_detail);
                }
                $this->session->set_userdata(['success' => 'Data has been saved successfully.']);
            } else {
                // updation of vendor escalation policy.
                $escalation_id = $id;
                $this->reusable_model->update_table('vendor_escalation_policy', ['escalation_reason' => $penalty_detail['escalation']], ['id' => $escalation_id]);
                // updation of penalty detail.
                $this->reusable_model->delete_from_table('penalty_details', ['escalation_id' => $escalation_id]);
                if(!empty($penalty_detail['apply_penalty'])) {
                    unset($penalty_detail['apply_penalty']);
                    unset($penalty_detail['escalation']);
                    $penalty_detail['escalation_id'] = $escalation_id;
                    $this->reusable_model->insert_into_table('penalty_details', $penalty_detail);
                }              
                $this->session->set_userdata(['success' => 'Data has been updated successfully.']);
            }
            redirect(base_url() . "penalty/get_penalty_detail_form/".(!empty($escalation_id) ? $escalation_id : NULL));
        }
        
        // load existing penalty detail record of $id.
        $penalty = [];
        if(!empty($id)) {
            $penalty = $this->penalty_model->get_panelty_details_data($id)[0];
        }
        
        // load view.
        $this->miscelleneous->load_nav_header();
        $this->load->view('penalty/add_detail', array('penalty' => $penalty));
    }
    
    function edit_penalty_detail($id) {
        if(!empty($this->input->get('action'))) {
            $data['active'] = ($this->input->get('action') == 'activate' ? 1 : 0); 
            
            $this->reusable_model->update_table('vendor_escalation_policy',$data,['id' => $id]);
            if(!empty($this->input->get('escalation_id'))) {
                $this->reusable_model->update_table('penalty_details',$data,['escalation_id' => $this->input->get('escalation_id')]);
            }
        }
        
        redirect(base_url() . "penalty/view_penalty_details");
    }

}
