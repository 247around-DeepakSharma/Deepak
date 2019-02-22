<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 36000);

define('Partner_Integ_Complete', TRUE);


class Booking_request extends CI_Controller {
    /**
     * load list model and helpers
     */
    function __Construct() {
        parent::__Construct();
        
            $this->load->model("booking_request_model");
        
            $this->load->helper(array('form', 'url','array'));
            $this->load->library('form_validation');
            $this->load->library("miscelleneous");
            
            $this->load->dbutil();
           
    }
    /**
     * @desc This function is used to load view to add new booking request symptom
     */
    function add_new_booking_request_symptom(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/booking_request_symptom');
    }
    /**
     * @desc This function is used to process new booking request symptom
     */
    function process_add_new_booking_request_symptom(){
        log_message('info', __METHOD__);
        $this->form_validation->set_rules('service_id', 'Appliance', 'required');
        $this->form_validation->set_rules('request_type', 'Request', 'required');
        $this->form_validation->set_rules('symptom', 'Symptom', 'required');
        $validation = $this->form_validation->run();
        if ($validation) {
            $data = $this->input->post();
            foreach ($data['request_type'] as $request_type) {
                $is_exist = $this->booking_request_model->get_booking_request_symptom('*', array('request_type' => trim($request_type), 'booking_request_symptom' => $data['symptom']));
                
                if(empty($is_exist)){
                
                $insert_id = $this->booking_request_model->insert_data(array('request_type' => trim($request_type), 'booking_request_symptom' => $data['symptom']), "symptom_booking_request");
                
                } else if(!empty($is_exist) && $is_exist[0]['active'] == 0){

                    $this->booking_request_model->update_table(array('id' => $is_exist[0]['id']), array('active' => 1), "symptom_booking_request");
                    $insert_id = true;
                } else{
                    $this->session->set_userdata(array('error' => "Symptom is already added"));
                    $insert_id = FALSE;
                }
            }
            
            
            if($insert_id){
                $this->session->set_userdata(array('success' => "Successfully, Symptom added"));
                redirect(base_url(). "employee/booking_request/add_new_booking_request_symptom");
            } else{
                redirect(base_url(). "employee/booking_request/add_new_booking_request_symptom");
            }
        } else {
            $this->add_new_booking_request_symptom();
        }
    }
    
    /**
     * @desc This function is used to show list of booking request symptom
     */
    function get_booking_request_symptom(){
        $data['data'] = $this->booking_request_model->get_booking_request_symptom('*');
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/get_booking_request_symptom', $data);
        
    }
    /**
     * @desc This function is used to show drop down list of booking request symptom
     */
    function get_booking_request_dropdown() {
        log_message('info', __METHOD__);

        $service_id = $this->input->post('service_id');
        $request_type = $this->input->post('request_type');
        $b_symptom = $this->input->post('booking_request_symptom');
        $data = $this->booking_request_model->get_booking_request_symptom('symptom_booking_request.id, symptom_booking_request.request_type, booking_request_symptom', array('service_id' => $service_id), array('service_category' => $request_type));
        if (!empty($data)) {

            $option = "";
            foreach ($data as $value) {
                $option .= "<option ";
                if ($b_symptom === $value['id']) {
                    $option .= " selected ";
                } else if (count($data) == 1) {
                    $option .= " selected ";
                }
                $option .= " value='" . $value['id'] . "'>" . $value['booking_request_symptom'] . "</option>";
            }
            echo $option;
        } else {
            echo 'Error';
        }
    }
    /**
     * @desc get spare request 
     */
    function get_spare_request_dropdown() {
        log_message('info', __METHOD__);

        $service_id = $this->input->post('service_id');
        $request_type = $this->input->post('request_type');
        $b_symptom = $this->input->post('spare_request_symptom');
        $data = $this->booking_request_model->get_spare_request_symptom('symptom_spare_request.id, symptom_spare_request.request_type, spare_request_symptom', array('service_id' => $service_id), array('service_category' => $request_type));
        if (!empty($data)) {

            $option = "";
            foreach ($data as $value) {
                $option .= "<option ";
                if ($b_symptom === $value['id']) {
                    $option .= " selected ";
                } else if (count($data) == 1) {
                    $option .= " selected ";
                }
                $option .= " value='" . $value['id'] . "'>" . $value['spare_request_symptom'] . "</option>";
            }
            echo $option;
        } else {
            echo 'Error';
        }
    }
    
    function get_spare_request_symptom(){
        log_message('info', __METHOD__);
        $data['data'] = $this->booking_request_model->get_spare_request_symptom('symptom_spare_request.*, services, request_type.service_category');
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/get_spare_request_symptom', $data);
    }
    
    function add_new_spare_request_symptom(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/spare_request_symptom');
    }
    
    function process_add_new_spare_symptom(){
        log_message('info', __METHOD__);
        $this->form_validation->set_rules('service_id', 'Appliance', 'required');
        $this->form_validation->set_rules('request_type', 'Request', 'required');
        $this->form_validation->set_rules('symptom', 'Symptom', 'required');
        $validation = $this->form_validation->run();
        if ($validation) {
            $data = $this->input->post();
            foreach ($data['request_type'] as $request_type) {
                $is_exist = $this->booking_request_model->get_spare_request_symptom('*', array('request_type' => $request_type, 'spare_request_symptom' => $data['symptom']));
                if(empty($is_exist)){
                
                $insert_id = $this->booking_request_model->insert_data(array('request_type' => $request_type, 'spare_request_symptom' => $data['symptom']), 'symptom_spare_request');
                
                } else if(!empty($is_exist) && $is_exist[0]['active'] == 0){

                    $this->booking_request_model->update_table(array('id' => $is_exist[0]['id']), array('active' => 1), 'symptom_spare_request');
                    $insert_id = true;
                } else{
                    $this->session->set_userdata(array('error' => "Symptom is already added"));
                    $insert_id = FALSE;
                }
            }

            if($insert_id){
                $this->session->set_userdata(array('success' => "Successfully, Symptom added"));
                redirect(base_url(). "employee/booking_request/add_new_spare_request_symptom");
            } else{
                redirect(base_url(). "employee/booking_request/add_new_spare_request_symptom");
            }
        } else {
            $this->add_new_spare_request_symptom();
        }
    }
    /**
     * @desc this function is used to show list of completion technical problem
     */
    function get_completion_symptom(){
        log_message('info', __METHOD__);
        $data['data'] = $this->booking_request_model->get_completion_symptom('symptom_completion_request.*, services, request_type.service_category');
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/complete_technical_problem', $data);
    }
    
    /**
     * @desc load view to add new completion technical problem
     */
    function add_new_completion_technical_problem(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/add_completion_technical_problem');
    }
    /**
     * @desc This function is used to process to add new completion technical problem
     */
    function process_add_new_completion_technical_problem(){
        log_message('info', __METHOD__);
        $this->form_validation->set_rules('service_id', 'Appliance', 'required');
        $this->form_validation->set_rules('request_type', 'Request', 'required');
        $this->form_validation->set_rules('symptom', 'Symptom', 'required');
        $validation = $this->form_validation->run();
        if ($validation) {
            $data = $this->input->post();
            foreach ($data['request_type'] as $request_type) {
                $is_exist = $this->booking_request_model->get_completion_symptom('*', array('request_type' => $request_type, 'completion_request_symptom' => $data['symptom']));
                if(empty($is_exist)){
                
                    $insert_id = $this->booking_request_model->insert_data(array('request_type' => $request_type, 'completion_request_symptom' => $data['symptom']), 'symptom_completion_request');
                
                } else if(!empty($is_exist) && $is_exist[0]['active'] == 0){

                    $this->booking_request_model->update_table(array('id' => $is_exist[0]['id']), array('active' => 1), 'symptom_completion_request');
                    $insert_id = true;
                } else{
                    $this->session->set_userdata(array('error' => "Symptom is already added"));
                    $insert_id = FALSE;
                }
            }

            if($insert_id){
                $this->session->set_userdata(array('success' => "Successfully, Symptom added"));
                redirect(base_url(). "employee/booking_request/add_new_completion_technical_problem");
            } else{
                redirect(base_url(). "employee/booking_request/add_new_completion_technical_problem");
            }
        } else {
            $this->add_new_completion_technical_problem();
        }
    }
    
    /**
     * @desc this function is used to show list of completion technical problem
     */
    function get_technical_solution_symptom(){
        log_message('info', __METHOD__);
        $data['data'] = $this->booking_request_model->symptom_completion_solution('symptom_completion_solution.*, services, request_type.service_category');
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/get_technical_solution', $data);
    }
    
    /**
     * @desc load view to add new completion technical problem
     */
    function add_technical_solution(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/add_completion_technical_solution');
    }
    
    function process_add_new_completion_technical_solution(){
        log_message('info', __METHOD__);
        $this->form_validation->set_rules('service_id', 'Appliance', 'required');
        $this->form_validation->set_rules('request_type', 'Request', 'required');
        $this->form_validation->set_rules('symptom', 'Symptom', 'required');
        $validation = $this->form_validation->run();
        if ($validation) {
            $data = $this->input->post();
            foreach ($data['request_type'] as $request_type) {
                $is_exist = $this->booking_request_model->symptom_completion_solution('*', array('request_type' => $request_type, 'technical_solution' => $data['symptom']));
                if(empty($is_exist)){

                    $insert_id = $this->booking_request_model->insert_data(array('request_type' => $request_type, 'technical_solution' => $data['symptom']), 'symptom_completion_solution');

                } else if(!empty($is_exist) && $is_exist[0]['active'] == 0){

                    $this->booking_request_model->update_table(array('id' => $is_exist[0]['id']), array('active' => 1), 'symptom_completion_solution');
                    $insert_id = true;
                } else{
                    $this->session->set_userdata(array('error' => "Symptom is already added"));
                    $insert_id = FALSE;
                }
            }
            
            if($insert_id){
                $this->session->set_userdata(array('success' => "Successfully, Symptom added"));
                redirect(base_url(). "employee/booking_request/add_technical_solution");
            } else{
                redirect(base_url(). "employee/booking_request/add_technical_solution");
            }
        } else {
            $this->add_technical_solution();
        }
    }
}
