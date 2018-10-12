<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Assets extends CI_Controller {

    function __Construct() {
        parent::__Construct();
        $this->load->helper(array('form', 'url', 'array'));
        $this->load->library('form_validation');
        $this->load->model('assets_model');
        $this->load->library("miscelleneous");
        $this->load->model('employee_model');
        $this->load->library("session");
    }

    /**
     * 
     * @desc This function fetches data from the database table called "asset_list" and loads the view that displays
     *       the assigned assets to the given employee.
     *      
     */
    public function get_assets() {

        $fetch_data = $this->assets_model->get_assets();
        $this->miscelleneous->load_nav_header();
        $this->load->view("employee/get_assetlist", array('fetch_data' => $fetch_data));
    }

    /**
     * @desc This function adds "assets-form" to the browsers view.  
     */
    public function add_assets() {

        $data = $this->employee_model->get_employee();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/add_assets', array('data' => $data));
    }

    /**
     * @desc This function performs adding of assets info in the form in case the validation passes. 
     */
    function process_add_assets() {
        $this->form_validation->set_rules('assets', 'Assets', 'required');
        $this->form_validation->set_rules('serial_no','Serial number', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->add_assets();
        } else {
            $assets = $this->input->post("assets");
            $serial_no = $this->input->post("serial_no");
            $employee_id = $this->input->post("employee_id");
            $data = array(
                'assets_name' => $assets,
                'serial_number' => $serial_no
            );
            $is_exist = $this->assets_model->get_assets($data);
            if (empty($is_exist)) {
                $data['create_date'] = date('Y-m-d H:i:s');
                $data['employee_id'] = $employee_id;
                $asset_id = $this->assets_model->insert_new_assets($data);
                if ($asset_id) {
                    if (!empty($employee_id)) {
                        $insert['asset_id'] = $asset_id;
                        $insert['employee_id'] = $employee_id;
                        $insert['agent_id'] = $this->session->userdata('id');
                        $this->assets_model->assigned_assets($insert);
                    }
                    $output = "Your data inserted successfully";
                    $userSession = array('success' => $output);
                    $this->session->set_userdata($userSession);
                    redirect(base_url() . "employee/assets/add_assets");
                } else {
                    $output = "Failed! Data did not insert";
                    $userSession = array('error' => $output);
                    $this->session->set_userdata($userSession);
                    redirect(base_url() . "employee/assets/add_assets");
                }
            }else {
                $output = "This Data already exist";
                $userSession = array('error' => $output);
                $this->session->set_userdata($userSession);
                redirect(base_url() . "employee/assets/add_assets");
            }
        }
    }

    /**
     * 
     * @param int $id  This function simply creates a data array containing id's of assets_list table and passes this 
     *                 parameter to view for performing update operate to the form.
     */
    function update_asset($id) {
        $data = array(
            'assets_list.id' => $id
        );
        $data['fetch_data'] = $this->assets_model->get_assets($data);
        $data['data'] = $this->employee_model->get_employee();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/update_asset', $data);
    }

    /**
     * @desc    This function allows to perform update operation to the form data on being triggered by the update button
     *         It does so by taking as well as getting up the form data via input->post and get_method of model and 
     *         performing update functionality on it. 
     * 
     */
    function process_update_assets($id) {
        $this->form_validation->set_rules('assets', 'Assets', 'required');
        $this->form_validation->set_rules('serial_no', 'Serial number', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->update_asset($id);
        } else {
            $assets = $this->input->post("assets");
            $serial_no = $this->input->post("serial_no");
            $employee_id = $this->input->post("employee_id");
            $data = array(
                'assets_name' => $assets,
                'serial_number' => $serial_no
            );
            $is_exist = $this->assets_model->get_assets($data);
            if (empty($is_exist)) {
                $data['update_date'] = date('Y-m-d H:i:s');
                $data['employee_id'] = $employee_id;
                $status = $this->assets_model->update_assets($id, $data);
                if ($status) {
                    if (!empty($employee_id)) {
                        $insert['asset_id'] = $id;
                        $insert['employee_id'] = $employee_id;
                        $insert['agent_id'] = $this->session->userdata('id');
                        $this->assets_model->assigned_assets($insert);
                    }
                    $output = "Your data updated successfully";
                    $userSession = array('success' => $output);
                    $this->session->set_userdata($userSession);
                    redirect(base_url() . "employee/assets/update_asset/" . $id);
                } else {
                    $output = "Failed! Data did not update";
                    $userSession = array('error' => $output);
                    $this->session->set_userdata($userSession);
                    redirect(base_url() . "employee/assets/update_asset/" . $id);
                }
            } else if ($employee_id != $is_exist[0]['employee_id']) {
                $data['update_date'] = date('Y-m-d H:i:s');
                $data['employee_id'] = $employee_id;
                $status = $this->assets_model->update_assets($id, $data);
                if (!empty($employee_id)) {
                    $insert['asset_id'] = $id;
                    $insert['employee_id'] = $employee_id;
                    $insert['agent_id'] = $this->session->userdata('id');
                    $this->assets_model->assigned_assets($insert);

                    $output = "Your data updated successfully";
                    $userSession = array('success' => $output);
                    $this->session->set_userdata($userSession);
                    redirect(base_url() . "employee/assets/update_asset/" . $id);
                } else {
                    $output = "This Data already exist";
                    $userSession = array('error' => $output);
                    $this->session->set_userdata($userSession);
                    redirect(base_url() . "employee/assets/update_asset/" . $id);
                }
            }
        }
    }
      /*
       * @desc    This function supplemnts the extra condition required for showing the assigned history details of 
       *          assets on the browser.
       */
    public function assigned_history($asset_id) {
        $where = array('asset_id' => $asset_id);
        $data = $this->assets_model->get_assigned_history($where);
        $this->load->view('employee/asset_assigned_history', array('data' => $data));
    }
}
