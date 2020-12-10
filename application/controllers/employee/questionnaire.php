<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Questionnaire extends CI_Controller {

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function __construct() {
        parent::__construct(); 
        
        $this->load->model('Questionnaire_model');
        $this->load->model('booking_model');
        $this->load->library("miscelleneous");
        
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            redirect(base_url() . "employee/login");
        }
    }

    public function index() {
        $form_value=$this->input->post('form_value');
        $product=$this->input->post('product');
        $panel=$this->input->post('panel');
        $data['selected_panel'] = $this->input->post('panel');
        $data['selected_product']=$this->input->post('product');
        $data['selected_form']=$this->input->post('form_value');
        if(empty($product)&&empty($form_value)&&empty($panel))
        {
            $data['data'] = $this->Questionnaire_model->get_questions();
            $this->miscelleneous->load_nav_header();
            $this->load->view('questionnaire/index', $data);
        }
        else
        {
            if(!empty($product) && $product!="0")
                $where['services.services']=$product;
            if(!empty($form_value) && $form_value!="0")
                $where['review_questionare.form']=$form_value;
            if(!empty($panel) && $panel!="0")
                $where['review_questionare.panel']=$panel;

            $data['data'] = $this->Questionnaire_model->get_questions($where);
            $this->miscelleneous->load_nav_header();
            $this->load->view('questionnaire/index', $data);
        }
    }
    
    /**
     * @desc: This function is used to activate and deactivate question
     * @params: void
     * @return: boolean
     */
    public function update_status() {
        $data = array(
            'active' => $this->input->post('status')
        );
        $where = array(
            'q_id' => $this->input->post('q_id')
        );
        $response = $this->Questionnaire_model->update_question_status($where, $data);
        echo $response;
    }
    
    /**
     * This function is used to load view to add/edit questions
     * 
     */
    public function add_question($id = "")
    {
        $q_data = [];
        if(!empty($id)){
            $where = ['review_questionare.q_id' => $id];
            $q_data = $this->Questionnaire_model->get_questions($where);
        }
        $services = $this->booking_model->selectservice();
        $panels = [1 => "Admin", 2 => "Partner"];
        $forms = [1 => "Booking Cancellation", 2 => "Booking Completion"];
        $this->miscelleneous->load_nav_header();
        $this->load->view('questionnaire/add_question', array('services' => $services, 'panels' => $panels, 'forms' => $forms, 'q_data' => $q_data));
    }    
    
    /**
     * Insert/Update Data from this method.
     * @return void
     */
    public function save_question() {
        $this->form_validation->set_rules('panel', 'Panel', 'required');
        $this->form_validation->set_rules('form', 'Form', 'required');
        $this->form_validation->set_rules('service_id', 'Product', 'required');
        $this->form_validation->set_rules('request_type', 'Request Type', 'required');
        $this->form_validation->set_rules('question', 'Question', 'required');
        $this->form_validation->set_rules('sequence', 'Sequence', 'required');
        $validation = $this->form_validation->run();
        $data = $this->input->post();
        if ($validation) {  
            $this->db->trans_start();
            // Insert data
            if(empty($data['q_id'])){
                $this->Questionnaire_model->save_question($data); 
            }
            // Update data
            else {
                $where = ['q_id' => $data['q_id']];
                $data['updated_by']=$this->input->post('id');
                $this->Questionnaire_model->update_question($where, $data); 
            }  
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_userdata(array('failed' => "Data can not be saved. Please try Again"));
            }
            else
            {
                $this->session->set_userdata(array('success' => "Data saved successfully."));
            }            
            redirect(base_url() . 'employee/questionnaire/index');
        }
        else 
        {
            $this->session->set_userdata(array('failed' => validation_errors()));
            if(empty($data['q_id'])){
                redirect(base_url() . 'employee/questionnaire/add_question');
            }
            else
            {
                redirect(base_url() . 'employee/questionnaire/add_question/'.$data['q_id']);
            }
        }                      
    }    
    
}
