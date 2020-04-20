<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Category extends CI_Controller {

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function __construct() {
        parent::__construct();
        
        $this->load->model('Category_model');
        $this->load->library("miscelleneous");
        
        if ($this->session->userdata('loggedIn') == TRUE) {
            return TRUE;
        } else {
            redirect(base_url() . "employee/login");
        }
    }

    public function index() {
        $data['data'] = $this->Category_model->get_category();
        $this->miscelleneous->load_nav_header();
        $this->load->view('category/index', $data);
    }

    /**
     * @desc: This function is used to activate and deactivate category
     * @params: void
     * @return: boolean
     */
    public function update_status() {
        $data = array(
            'active' => $this->input->post('status')
        );
        $where = array(
            'id' => $this->input->post('id')
        );
        $response = $this->Category_model->update_status($where, $data);
        echo $response;
    }

    /**
     * Insert/Update Data from this method.
     *
     * @return void
     */
    public function save() {
        $data = $this->input->post();
        $this->Category_model->save_data($data);                
        redirect(base_url() . 'category');
    }

    /**
     * Update Data from this method.
     *
     * @return Response
     */
    public function get_category_data() {
        $data = $this->input->post();
        $id = !empty($data['id']) ? $data['id'] : "";
        $category = [];
        if(!empty($id))
        {
            $category = $this->db->get_where('category', array('id' => $id))->row();
        }
        
        echo(json_encode($category));
    }

    /**
     * Delete Data from this method.
     *
     * @return Response
     */
    public function delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('category');
        redirect(base_url() . 'category');
    }
    
    public function validate_form()
    {
        $data = $this->input->post();
        $key = strtoupper(preg_replace("/[^a-zA-Z0-9.-]/", "", $data['name'])); 
        $category_id = $data['category_id'];
        $query = $this->db->get_where('category', array('private_key' => $key));
        if(!empty($category_id)){
            $query = $this->db->get_where('category', array('private_key' => $key, 'id != ' => $category_id)); 
        }
        $res = $query->result();
        $count = count($res);
        if($count > 0)
        {
            echo("fail");
        }
        exit;
    }
}
