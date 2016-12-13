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
	$this->load->model('reporting_utils');
	$this->load->helper(array('form', 'url'));
    }

    function penalty_on_service_center() {
	//$this->penalty_model->penalty_on_service_center_for_assigned_engineer();

        if( date('l') != "Sunday"){
            
	    $this->penalty_model->penalty_on_service_center_for_update_booking();
            // Inserting values in scheduler tasks log
            $this->reporting_utils->insert_scheduler_tasks_log(__FUNCTION__);
        } else {
            log_message('info', __FUNCTION__ ." Today is Sunday");
        }
    }
}
