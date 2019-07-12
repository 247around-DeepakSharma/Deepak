<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Capacity extends CI_Controller {

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function __construct() {
        parent::__construct();
        
        $this->load->model('Capacity_model');
        $this->load->library("miscelleneous");
        
        if ($this->session->userdata('loggedIn') == TRUE) {
            return TRUE;
        } else {
            redirect(base_url() . "employee/login");
        }
    }

    public function index() {
        $data['data'] = $this->Capacity_model->get_capacity();
        $this->miscelleneous->load_nav_header();
        $this->load->view('capacity/index', $data);
    }

    /**
     * @desc: This function is used to activate and deactivate capacity
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
        $response = $this->Capacity_model->update_status($where, $data);
        echo $response;
    }

    /**
     * Insert/Update Data from this method.
     *
     * @return void
     */
    public function save() {
        $data = $this->input->post();
        $this->Capacity_model->save_data($data);                
        redirect(base_url() . 'capacity');
    }

    /**
     * Update Data from this method.
     *
     * @return Response
     */
    public function get_capacity_data() {
        $data = $this->input->post();
        $id = !empty($data['id']) ? $data['id'] : "";
        $capacity = [];
        if(!empty($id))
        {
            $capacity = $this->db->get_where('capacity', array('id' => $id))->row();
        }
        
        echo(json_encode($capacity));
    }

    /**
     * Delete Data from this method.
     *
     * @return Response
     */
    public function delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('capacity');
        redirect(base_url() . 'capacity');
    }
    
    public function validate_form()
    {
        $data = $this->input->post();
        $key = strtoupper(preg_replace("/[^a-zA-Z0-9.-]/", "", $data['name'])); 
        $capacity_id = $data['capacity_id'];
        $query = $this->db->get_where('capacity', array('private_key' => $key));
        if(!empty($capacity_id)){
            $query = $this->db->get_where('capacity', array('private_key' => $key, 'id != ' => $capacity_id)); 
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
