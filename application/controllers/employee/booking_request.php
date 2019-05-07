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
            if(!empty($data['service_id'])) {
                $cond['where'] = array('service_id' => trim($data['service_id']), 'symptom' => $data['symptom']);
                $is_exist = $this->booking_request_model->get_symptoms('*', $cond);
                
                if(empty($is_exist)){
                
                $insert_id = $this->booking_request_model->insert_data(array('service_id' => trim($data['service_id']), 'symptom' => $data['symptom']), "symptom");
                
                } else if(!empty($is_exist) && $is_exist[0]['active'] == 0){

                    $this->booking_request_model->update_table(array('id' => $is_exist[0]['id']), array('active' => 1), "symptom");
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
     * @desc This function is used to update booking request symptom
     */
    function update_symptom_booking_request(){
        $data = $this->input->post("data");
        $id = $this->input->post("id");
        if(!empty($data)){
            if(isset($data['active'])){
                $this->booking_request_model->update_table(array("id"=>$id), $data, "symptom");
                echo $this->db->last_query();
            }
            else{
                $cond['where'] = array('service_id' => $data['service_id'], 'symptom' => $data['symptom']);
                $is_exist = $this->booking_request_model->get_symptoms('*', $cond);
                if(empty($is_exist)){
                    $this->booking_request_model->update_table(array("id"=>$id), $data, "symptom");
                    echo true;
                } 
                else{
                   echo false;
                } 
            }
        }
        else{
            echo false;
        }
    }
    
    /**
     * @desc This function is used to show list of booking request symptom
     */
    function get_booking_request_symptom(){
        $data['data'] = $this->booking_request_model->get_booking_request_symptom('*, symptom.id as tid');
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
        $data = $this->booking_request_model->get_booking_request_symptom('symptom.id, symptom_defect_solution_mapping.request_id, symptom', array('symptom.service_id' => $service_id), array('service_category' => $request_type));
        if (!empty($data)) {

            $option = "";
            foreach ($data as $value) {
                $option .= "<option ";
                if ($b_symptom === $value['id']) {
                    $option .= " selected ";
                } else if (count($data) == 1) {
                    $option .= " selected ";
                }
                $option .= " value='" . $value['id'] . "'>" . $value['symptom'] . "</option>";
            }
            echo $option;
        } else {
            //echo 'Error';
            echo $option = "<option value='0' selected>Default</option>";
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
        $data['data'] = $this->booking_request_model->get_spare_request_symptom('symptom_spare_request.*, services, services.id as service_id, request_type.service_category, request_type.id as request_type_id');
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
     * @desc this function is used to show list of symptom 
     */
    function symptom_list($id = ""){
        $data['id'] = $id;
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/show_symptom_list',$data);
    }
    
    /**
     * @desc load view to add new completion technical problem
     */
    function add_new_symptom(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/add_symptom');
    }
    
     /**
     * @desc This function is used to update symptom
     */
   function update_symptom(){
        $data = $this->input->post("data");
        $id = $this->input->post("id");
        if(!empty($data)){
            if(isset($data['active'])){
                $this->booking_request_model->update_table(array("id"=>$id), $data, "symptom");
                echo true;
            }
            else{
                $cond['where'] = array('service_id' => $data['service_id'], 'partner_id' => $data['partner_id'], 'symptom' => $data['symptom']);
                $is_exist = $this->booking_request_model->get_symptoms('*', $cond);
                if(empty($is_exist)){
                    $this->booking_request_model->update_table(array("id"=>$id), $data, "symptom");
                    echo true;
                } 
                else{
                   echo false;
                } 
            }
        }
        else{
            echo false;
        }
    }
    
    
    /**
     * @desc This function is used to process to add new symptom
     */
    function process_add_new_symptom(){
        log_message('info', __METHOD__);
        $this->form_validation->set_rules('service_id', 'Appliance', 'required');
        $this->form_validation->set_rules('partner_id', 'Partner', 'required');
        $this->form_validation->set_rules('symptom', 'Symptom', 'required');
        $validation = $this->form_validation->run();
        if ($validation) {
            $data = $this->input->post();
            if(!empty($data['service_id'])) {
                $select = "symptom.*, services";
                $cond['where'] = array('service_id' => $data['service_id'], 'partner_id' => $data['partner_id'], 'symptom' => $data['symptom']);
                $is_exist = $this->booking_request_model->get_symptoms($select,$cond);
                if(empty($is_exist)){

                    $insert_id = $this->booking_request_model->insert_data(array('service_id' => $data['service_id'], 'partner_id' => $data['partner_id'], 'symptom' => $data['symptom']), 'symptom');

                } else if(!empty($is_exist) && $is_exist[0]['active'] == 0){

                    $this->booking_request_model->update_table(array('id' => $is_exist[0]['id']), array('active' => 1), 'symptom');
                    $insert_id = true;
                } else{
                    $this->session->set_userdata(array('error' => "Symptom is already added"));
                    $insert_id = FALSE;
                }
            }
            
            if($insert_id){
                $this->session->set_userdata(array('success' => "Successfully, added"));
                redirect(base_url(). "employee/booking_request/add_new_symptom");
            } else{
                redirect(base_url(). "employee/booking_request/add_new_symptom");
            }
        } else {
            $this->add_new_symptom();
        }
    }
    
    function get_symptom_data(){
        $post = $this->get_post_data();
        $new_post = $this->get_symptom_filtered_data($post);
        $select = "symptom.*, services, public_name as partner";
        $list = $this->booking_request_model->get_symptoms($select,$new_post);
        $data = array();
        $no = $post['start'];
        //print_r($list);exit();
        foreach ($list as $key => $value) {
            $no++;
            $row =  $this->symptom_data($list[$key], $no);
            $data[] = $row;
        }
        
        $new_post['length'] = -1;
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->booking_request_model->get_symptoms('count(distinct(symptom.id)) as numrows',$new_post)[0]['numrows'],
            "recordsFiltered" =>  $this->booking_request_model->get_symptoms('count(distinct(symptom.id)) as numrows',$new_post)[0]['numrows'],
            "data" => $data,
        );
        
        echo json_encode($output);
    }
    
    function get_symptom_filtered_data($data){
        $id = $this->input->post('id');
        
        if(!empty($id)){
            $data['where']['symptom.id'] =  $id;
        }

        
        $data['column_order'] = array(NULL,'public_name','services','symptom',NULL);
        $data['column_search'] = array('public_name','services','symptom');
        
        return $data;
    }
    
    function symptom_data($symptom_list, $no){
        $row = array();
        $jsonData = json_encode(array("service_id" => (isset($symptom_list['service_id'])?$symptom_list['service_id']:''), "partner_id"=> (isset($symptom_list['partner_id'])?$symptom_list['partner_id']:''), "symptom" => (isset($symptom_list['symptom'])?$symptom_list['symptom']:'')));
        $row[] = $no;
        $row[] = (isset($symptom_list['partner'])?$symptom_list['partner']:'');
        $row[] = (isset($symptom_list['services'])?$symptom_list['services']:'');
        $row[] = (isset($symptom_list['symptom'])?$symptom_list['symptom']:'');
        $row[] = "<a class='btn btn-md btn-success' data-id='".$jsonData."' onclick='update_symptom_data(this, \"".$symptom_list['id']."\")'>Update</a>"
                . "<a class='btn btn-md btn-warning' style='margin-left:10px;' onclick='update_status(\"".$symptom_list['active']."\", \"".$symptom_list['id']."\")'>".(($symptom_list['active'] == "0")? "Active" : "Deactive" )."</a>";
        
        return $row;
        
    }
    /**
     * @desc this function is used to show list of defect 
     */
    function defect_list($id = ""){
        $data['id'] = $id;
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/show_defect_list',$data);
    }
    
    /**
     * @desc load view to add new completion technical problem
     */
    function add_new_defect(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/add_defect');
    }
    
     /**
     * @desc This function is used to update defect
     */
    function update_defect(){
        $data = $this->input->post("data");
        $id = $this->input->post("id");
        if(!empty($data)){
            if(isset($data['active'])){
                $this->booking_request_model->update_table(array("id"=>$id), $data, "defect");
                echo true;
            }
            else{
                $cond['where'] = array('service_id' => $data['service_id'], 'partner_id' => $data['partner_id'], 'defect' => $data['defect']);
                $is_exist = $this->booking_request_model->get_defects('*', $cond);
                if(empty($is_exist)){
                    $this->booking_request_model->update_table(array("id"=>$id), $data, "defect");
                    echo true;
                } 
                else{
                   echo false;
                } 
            }
        }
        else{
            echo false;
        }
    }
    
    
    /**
     * @desc This function is used to process to add new defect
     */
    function process_add_new_defect(){
        log_message('info', __METHOD__);
        $this->form_validation->set_rules('service_id', 'Appliance', 'required');
        $this->form_validation->set_rules('partner_id', 'Partner', 'required');
        $this->form_validation->set_rules('defect', 'Defect', 'required');
        $validation = $this->form_validation->run();
        if ($validation) {
            $data = $this->input->post();
            if(!empty($data['service_id'])) {
                $select = "defect.*, services";
                $cond['where'] = array('service_id' => $data['service_id'], 'partner_id' => $data['partner_id'], 'defect' => $data['defect']);
                $is_exist = $this->booking_request_model->get_defects($select,$cond);
                if(empty($is_exist)){

                    $insert_id = $this->booking_request_model->insert_data(array('service_id' => $data['service_id'], 'partner_id' => $data['partner_id'], 'defect' => $data['defect']), 'defect');

                } else if(!empty($is_exist) && $is_exist[0]['active'] == 0){

                    $this->booking_request_model->update_table(array('id' => $is_exist[0]['id']), array('active' => 1), 'defect');
                    $insert_id = true;
                } else{
                    $this->session->set_userdata(array('error' => "Defect is already added"));
                    $insert_id = FALSE;
                }
            }
            
            if($insert_id){
                $this->session->set_userdata(array('success' => "Successfully, added"));
                redirect(base_url(). "employee/booking_request/add_new_defect");
            } else{
                redirect(base_url(). "employee/booking_request/add_new_defect");
            }
        } else {
            $this->add_new_defect();
        }
    }
    
    function get_defect_data(){
        $post = $this->get_post_data();
        $new_post = $this->get_defect_filtered_data($post);
        $select = "defect.*, services, public_name as partner";
        $list = $this->booking_request_model->get_defects($select,$new_post);
        $data = array();
        $no = $post['start'];
        //print_r($list);exit();
        foreach ($list as $key => $value) {
            $no++;
            $row =  $this->defect_data($list[$key], $no);
            $data[] = $row;
        }
        
        $new_post['length'] = -1;
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->booking_request_model->get_defects('count(distinct(defect.id)) as numrows',$new_post)[0]['numrows'],
            "recordsFiltered" =>  $this->booking_request_model->get_defects('count(distinct(defect.id)) as numrows',$new_post)[0]['numrows'],
            "data" => $data,
        );
        
        echo json_encode($output);
    }
    
    function get_defect_filtered_data($data){
        $id = $this->input->post('id');
        
        if(!empty($id)){
            $data['where']['defect.id'] =  $id;
        }

        
        $data['column_order'] = array(NULL,'public_name','services','defect',NULL);
        $data['column_search'] = array('public_name','services','defect');
        
        return $data;
    }
    
    function defect_data($defect_list, $no){
        $row = array();
        $jsonData = json_encode(array("service_id" => (isset($defect_list['service_id'])?$defect_list['service_id']:''), "partner_id"=> (isset($defect_list['partner_id'])?$defect_list['partner_id']:''), "defect" => (isset($defect_list['defect'])?$defect_list['defect']:'')));
        $row[] = $no;
        $row[] = (isset($defect_list['partner'])?$defect_list['partner']:'');
        $row[] = (isset($defect_list['services'])?$defect_list['services']:'');
        $row[] = (isset($defect_list['defect'])?$defect_list['defect']:'');
        $row[] = "<a class='btn btn-md btn-success' data-id='".$jsonData."' onclick='update_defect_data(this, \"".$defect_list['id']."\")'>Update</a>"
                . "<a class='btn btn-md btn-warning' style='margin-left:10px;' onclick='update_status(\"".$defect_list['active']."\", \"".$defect_list['id']."\")'>".(($defect_list['active'] == "0")? "Active" : "Deactive" )."</a>";
        
        return $row;
        
    }
    
    /**
     * @desc this function is used to show list of solution 
     */
    function solution_list($id = ""){
        $data['id'] = $id;
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/show_solution_list',$data);
    }
    
    /**
     * @desc load view to add new completion technical problem
     */
    function add_new_solution(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/add_solution');
    }
    
     /**
     * @desc This function is used to update solution
     */
    function update_solution(){
        $data = $this->input->post("data");
        $id = $this->input->post("id");
        if(!empty($data)){
            if(isset($data['active'])){
                $this->booking_request_model->update_table(array("id"=>$id), $data, "symptom_completion_solution");
                echo true;
            }
            else{
                $cond['where'] = array('service_id' => $data['service_id'], 'partner_id' => $data['partner_id'], 'technical_solution' => $data['technical_solution']);
                $is_exist = $this->booking_request_model->get_solutions('*', $cond);
                if(empty($is_exist)){
                    $this->booking_request_model->update_table(array("id"=>$id), $data, "symptom_completion_solution");
                    echo true;
                } 
                else{
                   echo false;
                } 
            }
        }
        else{
            echo false;
        }
    }
    
    
    /**
     * @desc This function is used to process to add new solution
     */
    function process_add_new_solution(){
        log_message('info', __METHOD__);
        $this->form_validation->set_rules('service_id', 'Appliance', 'required');
        $this->form_validation->set_rules('partner_id', 'Partner', 'required');
        $this->form_validation->set_rules('solution', 'Solution', 'required');
        $validation = $this->form_validation->run();
        if ($validation) {
            $data = $this->input->post();
            if(!empty($data['service_id'])) {
                $select = "symptom_completion_solution.*, services";
                $cond['where'] = array('service_id' => $data['service_id'], 'partner_id' => $data['partner_id'], 'technical_solution' => $data['solution']);
                $is_exist = $this->booking_request_model->get_solutions($select,$cond);
                if(empty($is_exist)){

                    $insert_id = $this->booking_request_model->insert_data(array('service_id' => $data['service_id'], 'partner_id' => $data['partner_id'], 'technical_solution' => $data['solution']), 'symptom_completion_solution');

                } else if(!empty($is_exist) && $is_exist[0]['active'] == 0){

                    $this->booking_request_model->update_table(array('id' => $is_exist[0]['id']), array('active' => 1), 'symptom_completion_solution');
                    $insert_id = true;
                } else{
                    $this->session->set_userdata(array('error' => "Solution is already added"));
                    $insert_id = FALSE;
                }
            }
            
            if($insert_id){
                $this->session->set_userdata(array('success' => "Successfully, added"));
                redirect(base_url(). "employee/booking_request/add_new_solution");
            } else{
                redirect(base_url(). "employee/booking_request/add_new_solution");
            }
        } else {
            $this->add_new_solution();
        }
    }
    
    function get_solution_data(){
        $post = $this->get_post_data();
        $new_post = $this->get_solution_filtered_data($post);
        $select = "symptom_completion_solution.*, services, public_name as partner";
        $list = $this->booking_request_model->get_solutions($select,$new_post);
        $data = array();
        $no = $post['start'];
        //print_r($list);exit();
        foreach ($list as $key => $value) {
            $no++;
            $row =  $this->solution_data($list[$key], $no);
            $data[] = $row;
        }
        
        $new_post['length'] = -1;
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->booking_request_model->get_solutions('count(distinct(symptom_completion_solution.id)) as numrows',$new_post)[0]['numrows'],
            "recordsFiltered" =>  $this->booking_request_model->get_solutions('count(distinct(symptom_completion_solution.id)) as numrows',$new_post)[0]['numrows'],
            "data" => $data,
        );
        
        echo json_encode($output);
    }
    
    function get_solution_filtered_data($data){
        $id = $this->input->post('id');
        
        if(!empty($id)){
            $data['where']['symptom_completion_solution.id'] =  $id;
        }

        
        $data['column_order'] = array(NULL,'public_name','services','technical_solution',NULL);
        $data['column_search'] = array('public_name','services','technical_solution');
        
        return $data;
    }
    
    function solution_data($solution_list, $no){
        $row = array();
        $jsonData = json_encode(array("service_id" => (isset($solution_list['service_id'])?$solution_list['service_id']:''), "partner_id"=> (isset($solution_list['partner_id'])?$solution_list['partner_id']:''), "technical_solution" => (isset($solution_list['technical_solution'])?$solution_list['technical_solution']:'')));
        $row[] = $no;
        $row[] = (isset($solution_list['partner'])?$solution_list['partner']:'');
        $row[] = (isset($solution_list['services'])?$solution_list['services']:'');
        $row[] = (isset($solution_list['technical_solution'])?$solution_list['technical_solution']:'');
        $row[] = "<a class='btn btn-md btn-success' data-id='".$jsonData."' onclick='update_solution_data(this, \"".$solution_list['id']."\")'>Update</a>"
                . "<a class='btn btn-md btn-warning' style='margin-left:10px;' onclick='update_status(\"".$solution_list['active']."\", \"".$solution_list['id']."\")'>".(($solution_list['active'] == "0")? "Active" : "Deactive" )."</a>";
        
        return $row;
        
    }
    
    /**
     * @desc this function is used to show list of completion technical problem
     */
    function get_technical_solution_symptom(){
        log_message('info', __METHOD__);
        $data['data'] = $this->booking_request_model->symptom_completion_solution('symptom_completion_solution.*, services, request_type.service_category, services.id as service_id, request_type.id as request_type_id');
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
            if(!empty($data['service_id'])) {
                $is_exist = $this->booking_request_model->get_solutions('*', array('service_id' => $data['service_id'], 'technical_solution' => $data['symptom']));
                if(empty($is_exist)){

                    $insert_id = $this->booking_request_model->insert_data(array('service_id' => $data['service_id'], 'technical_solution' => $data['symptom']), 'symptom_completion_solution');

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
    /*
     * Desc - This function is used to update symptom_completion solution
     */
    function update_symptom_completion_solution(){
        $data = $this->input->post("data");
        $id = $this->input->post("id");
        if(!empty($data)){
            if(isset($data['active'])){
                $this->booking_request_model->update_table(array("id"=>$id), $data, "symptom_completion_solution");
                echo true;
            }
            else{ 
                $is_exist = $this->booking_request_model->get_solutions('*', array('service_id' => $data['service_id'], 'technical_solution' => $data['technical_solution']));
                if(empty($is_exist)){
                    $this->booking_request_model->update_table(array("id"=>$id), $data, "symptom_completion_solution");
                    echo true;
                } 
                else{
                   echo false;
                } 
            }
        }
        else{
            echo false;
        }
    }
    
    /*
     * Desc - This function is used to update symptom spare request
     */
    function update_symptom_spare_request(){
        $data = $this->input->post("data");
        $id = $this->input->post("id");
        if(!empty($data)){
            if(isset($data['active'])){
                $this->booking_request_model->update_table(array("id"=>$id), $data, "symptom_spare_request");
                echo true;
            }
            else{ 
                $is_exist = $this->booking_request_model->get_spare_request_symptom('*', array('request_type' => $data['request_type'], 'spare_request_symptom' => $data['spare_request_symptom']));
                if(empty($is_exist)){
                    $this->booking_request_model->update_table(array("id"=>$id), $data, "symptom_spare_request");
                    echo true;
                } 
                else{
                   echo false;
                } 
            }
        }
        else{
            echo false;
        }
    }
    
    /**
     * @desc this function is used to show list of symptom, defect & solution 
     */
    function symptom_defect_solution_mapping($id = ""){
        $data['id'] = $id;
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/show_symptom_defect_solution_list',$data);
    }
    
    /**
     * @desc load view to add new symptom, defect & solution
     */
    function add_symptom_defect_solution(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/add_symptom_defect_solution');
    }
    
    function process_add_new_symptom_defect_solution(){
        log_message('info', __METHOD__);
        $this->form_validation->set_rules('service_id', 'Appliance', 'required');
        $this->form_validation->set_rules('request_type', 'Request', 'required');
        $this->form_validation->set_rules('symptom', 'Symptom', 'required');
        $this->form_validation->set_rules('defect', 'Defect', 'required');
        $this->form_validation->set_rules('solution', 'Solution', 'required');
        $validation = $this->form_validation->run();
        if ($validation) {
            $data = $this->input->post();
            foreach ($data['request_type'] as $request_type) {
                $select = "symptom_defect_solution_mapping.*, services, request_type.service_category, services.id as service_id, request_type.id as request_type_id";
                $cond['where'] = array('product_id' => $data['service_id'], 'request_id' => $request_type, 'symptom_id' => $data['symptom'], 'defect_id' => $data['defect'], 'solution_id' => $data['solution']);
                $is_exist = $this->booking_request_model->get_symptom_defect_solution_mapping($cond,$select);
                if(empty($is_exist)){

                    $insert_id = $this->booking_request_model->insert_data(array('product_id' => $data['service_id'], 'request_id' => $request_type, 'symptom_id' => $data['symptom'], 'defect_id' => $data['defect'], 'solution_id' => $data['solution']), 'symptom_defect_solution_mapping');

                } else if(!empty($is_exist) && $is_exist[0]['active'] == 0){

                    $this->booking_request_model->update_table(array('id' => $is_exist[0]['id']), array('active' => 1), 'symptom_defect_solution_mapping');
                    $insert_id = true;
                } else{
                    $this->session->set_userdata(array('error' => "Mapping Data is already added"));
                    $insert_id = FALSE;
                }
            }
            
            if($insert_id){
                $this->session->set_userdata(array('success' => "Successfully, added"));
                redirect(base_url(). "employee/booking_request/add_symptom_defect_solution");
            } else{
                redirect(base_url(). "employee/booking_request/add_symptom_defect_solution");
            }
        } else {
            $this->add_symptom_defect_solution();
        }
    }
    /*
     * Desc - This function is used to update symptom, defect & solution
     */
    function update_symptom_defect_solution(){
        $data = $this->input->post("data");
        $id = $this->input->post("id");
        if(!empty($data)){
            if(isset($data['is_active'])){
                $this->booking_request_model->update_table(array("id"=>$id), $data, "symptom_defect_solution_mapping");
                echo true;
            }
            else{
                $select = "symptom_defect_solution_mapping.*, services, request_type.service_category, services.id as service_id, request_type.id as request_type_id";
                $cond['where'] = array('product_id' => $data['product_id'], 'request_id' => $data['request_id'], 'symptom_id' => $data['symptom_id'], 'defect_id' => $data['defect_id'], 'solution_id' => $data['solution_id']);
                $is_exist = $this->booking_request_model->get_symptom_defect_solution_mapping($cond,$select);
                if(empty($is_exist)){
                    $this->booking_request_model->update_table(array("id"=>$id), $data, "symptom_defect_solution_mapping");
                    echo true;
                } 
                else{
                   echo false;
                } 
            }
        }
        else{
            echo false;
        }
    }
    
    function get_symptom_defect_solution_mapping(){
        $post = $this->get_post_data();
        $new_post = $this->get_filtered_data($post);
        $new_post['join']['symptom'] =  "FIND_IN_SET( symptom.id , symptom_defect_solution_mapping.symptom_id )";
        $new_post['join']['defect'] =  "FIND_IN_SET( defect.id , symptom_defect_solution_mapping.defect_id )";
        $new_post['join']['symptom_completion_solution'] =  "FIND_IN_SET( symptom_completion_solution.id , symptom_defect_solution_mapping.solution_id )";
        $select = "symptom_defect_solution_mapping.*, services, request_type.service_category, services.id as service_id, request_type.id as request_type_id";
        $list = $this->booking_request_model->get_symptom_defect_solution_mapping($new_post,$select);
        $data = array();
        $no = $post['start'];
        foreach ($list as $key => $value) {
            $no++;
            $symptom_cond['where'] = array('symptom.id' => $value['symptom_id']);
            $defect_cond['where'] = array('defect.id' => $value['defect_id']);
            $solution_cond['where'] = array('symptom_completion_solution.id' => $value['solution_id']);
            $symptom = $this->booking_request_model->get_symptoms('symptom', $symptom_cond);
            $defect = $this->booking_request_model->get_defects('defect', $defect_cond);
            $solution = $this->booking_request_model->get_solutions('technical_solution as solution', $solution_cond);
            
            if(!empty($symptom))
                $list[$key]['symptom'] = $symptom[0]['symptom'];
            if(!empty($defect))
                $list[$key]['defect'] = $defect[0]['defect'];
            if(!empty($solution))
                $list[$key]['solution'] = $solution[0]['solution'];
            $row =  $this->symptom_solution_mapping_data($list[$key], $no);
            $data[] = $row;
        }
        
        $new_post['length'] = -1;
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->booking_request_model->get_symptom_defect_solution_mapping($new_post,'count(distinct(symptom_defect_solution_mapping.id)) as numrows')[0]['numrows'],
            "recordsFiltered" =>  $this->booking_request_model->get_symptom_defect_solution_mapping($new_post,'count(distinct(symptom_defect_solution_mapping.id)) as numrows')[0]['numrows'],
            "data" => $data,
        );
        
        echo json_encode($output);
    }
    
    function get_post_data(){
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
        $post['status'] = $this->input->post('status');

        return $post;
    }
    
    function get_filtered_data($data){
        $id = $this->input->post('id');
        
        if(!empty($id)){
            $data['where']['symptom_defect_solution_mapping.id'] =  $id;
        }

        
        $data['column_order'] = array(NULL,'services','service_category','symptom', 'defect', 'technical_solution',NULL);
        $data['column_search'] = array('services','service_category','symptom', 'defect', 'technical_solution');
        
        return $data;
    }
    
    function symptom_solution_mapping_data($mapping_list, $no){
        $row = array();
        $jsonData = json_encode(array("service" => (isset($mapping_list['service_id'])?$mapping_list['service_id']:''), "request_type"=> (isset($mapping_list['request_type_id'])?$mapping_list['request_type_id']:''), "symptom" => (isset($mapping_list['symptom_id'])?$mapping_list['symptom_id']:''), "defect" => (isset($mapping_list['defect_id'])?$mapping_list['defect_id']:''), "solution" => (isset($mapping_list['solution_id'])?$mapping_list['solution_id']:'')));
        $row[] = $no;
        $row[] = (isset($mapping_list['services'])?$mapping_list['services']:'');
        $row[] = (isset($mapping_list['service_category'])?$mapping_list['service_category']:'');
        $row[] = (isset($mapping_list['symptom'])?$mapping_list['symptom']:'');
        $row[] = (isset($mapping_list['defect'])?$mapping_list['defect']:'');
        $row[] = (isset($mapping_list['solution'])?$mapping_list['solution']:'');
        $row[] = "<a class='btn btn-md btn-success' data-id='".$jsonData."' onclick='update_symptom_defect_solution(this, \"".$mapping_list['id']."\")'>Update</a>"
                . "<a class='btn btn-md btn-warning' style='margin-left:10px;' onclick='update_status(\"".$mapping_list['is_active']."\", \"".$mapping_list['id']."\")'>".(($mapping_list['is_active'] == "0")? "Active" : "Deactive" )."</a>";
        
        return $row;
        
    }
    
    /**
     * @desc: This function is used to get symptom from Ajax call
     * @params: void
     * @return: string
     */
    function get_symptoms(){
        $post = $this->input->post();
        $symptom_list = $this->booking_request_model->get_symptoms('symptom.id,symptom',$post);
        if($post['is_option_selected']){
            $option = '<option  selected="" disabled="" value="">Select Symptom</option>';
        }else{
            $option = '';
        }
        
        foreach ($symptom_list as $value) {
            $option .= "<option value='" . $value['id'] . "'";
            $option .= " > ";
            $option .= $value['symptom'] . "</option>";
        }
        $option .= '<option value="all" >All</option>';
        echo $option;
    }
    
    /**
     * @desc: This function is used to get defect from Ajax call
     * @params: void
     * @return: string
     */
    function get_defects(){
        $post = $this->input->post();
        $defect_list = $this->booking_request_model->get_defects('defect.id,defect',$post);
        
        if($post['is_option_selected']){
            $option = '<option  selected="" disabled="" value="">Select Defect</option>';
        }else{
            $option = '';
        }
        
        foreach ($defect_list as $value) {
            $option .= "<option value='" . $value['id'] . "'";
            $option .= " > ";
            $option .= $value['defect'] . "</option>";
        }
        $option .= '<option value="all" >All</option>';
        echo $option;
    }
    
    /**
     * @desc: This function is used to get solution from Ajax call
     * @params: void
     * @return: string
     */
    function get_solutions(){
        $post = $this->input->post();
        $solution_list = $this->booking_request_model->get_solutions('symptom_completion_solution.id,technical_solution as solution',$post);
        
        if($post['is_option_selected']){
            $option = '<option  selected="" disabled="" value="">Select Solution</option>';
        }else{
            $option = '';
        }
        
        foreach ($solution_list as $value) {
            $option .= "<option value='" . $value['id'] . "'";
            $option .= " > ";
            $option .= $value['solution'] . "</option>";
        }
        $option .= '<option value="all" >All</option>';
        echo $option;
    }
}
