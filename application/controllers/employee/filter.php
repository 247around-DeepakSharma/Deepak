<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Filter extends CI_Controller {

    /**
     * @desc : load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();

        $this->load->model('filter_model');
        $this->load->model('handyman_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->library("pagination");

        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee') || ($this->session->userdata('userType') == 'admin')) {
            return TRUE;
        } else {
            redirect(base_url() . "employee/login");
        }
    }

    /**
     *  @desc : This funtion load filter page
     *  @param : void
     *  @return : view filter page
     */
    public function index() {



        $data = $this->getfilterdata();
        $result['handyman'] = $this->filter_model->filter($data);
        $result['service'] = $this->filter_model->getserviceforfilter();
        $result['handyman_name'] = $this->filter_model->gethandyman_name();
        $result['agent'] = $this->filter_model->getagent();


        $this->load->view('employee/header', $result);
        $this->load->view('employee/filtersideview', $result);
    }

    /**
     *  @desc : This funtion get filtered handyman
     *  @param : void
     *  @return : view fiter handyman
     */
    function viewdata() {


        $data = $this->getfilterdata();
        $result['handyman'] = $this->filter_model->filter($data);
        $result['service'] = $this->filter_model->getserviceforfilter();
        $result['handyman_name'] = $this->filter_model->gethandyman_name();
        $result['agent'] = $this->filter_model->getagent();
        $html = $this->load->view('employee/filterview', $result);

        print_r($html);
    }

    /**
     *  @desc : This funtion get filtered handyman
     *  @param : void
     *  @return : view fiter handyman
     */
    function viewhandyman() {

        $data = $this->getfilterdata();
        $result['handyman'] = $this->filter_model->filter($data);
        $result['service'] = $this->filter_model->getserviceforfilter();
        $result['handyman_name'] = $this->filter_model->gethandyman_name();
        $result['agent'] = $this->filter_model->getagent();
        $this->load->view('employee/header', $result);
        $this->load->view('employee/filterview', $result);
    }

    /**
     *  @desc : This funtion get handyman handyman input resquested
     *  @param : input for filter handyman
     *  @return : filter
     */
    function getfilterdata() {

        if (isset($_POST['service']))
            $data['service_id'] = $this->input->post('service');
        if (isset($_POST['experience'])) {
            $data['experience'] = array();
            $experience = $this->input->post('experience');
            foreach ($experience as $key => $value) {
                $expereinece = explode("-", $value);
                array_push($data['experience'], $expereinece);
            }
        }
        if (isset($_POST['Rating_by_Agent']))
            $data['Rating_by_Agent'] = $this->input->post('Rating_by_Agent');
        if (isset($_POST['Agent']))
            $data['Agent'] = $this->input->post('Agent');
        if (isset($_POST['address']))
            $data['address'] = $this->input->post('address');
        if (isset($_POST['service_on_call']))
            $data['service_on_call'] = $this->input->post('service_on_call');
        if (isset($_POST['action']))
            $data['action'] = $this->input->post('action');
        if (isset($_POST['approved']))
            $data['approved'] = $this->input->post('approved');
        if (isset($_POST['verified']))
            $data['verified'] = $this->input->post('verified');
        if (isset($_POST['telecaller']))
            $data['verify_by'] = $this->input->post('telecaller');
        if (!empty($data))
            return $data;
    }

    /**
     *  @desc : This funtion check approve handyman authority
     *  @param : void
     *  @return : if session true return true otherwise not authorised message
     */
    function checkapprovesession() {
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee') && ($this->session->userdata('approvehandyman') == 1)) {
            return TRUE;
        } else {
            // redirect(base_url()."employee/login");
            echo "<script>alert('Sory You are not Authorised')</script>";
            // echo"<script>window.history.back()</script>";
        }
    }

    /**
     *  @desc : This funtion get handyman id for hide handyman
     *  @param : handyman id (to be deleted),offset
     *  @return : hide handyman 
     */
    function deletehandyman($id, $offset = 0) {
        $check = $this->checksessionactive();
        if ($check == TRUE) {
            $updateAction = array('action' => '0');
            $removeuser = $this->handyman_model->UpdateHandyman($id, $updateAction);
            $output = $removeuser . " deactivate successfully";
            $userSession = array('error' => $output);
            $this->session->set_userdata($userSession);
        }
    }

    /**
     *  @desc :  This funtion for to make active  handyman
     *  @param : handyman id and offset
     *  @return :void
     */
    function activatehandyman($id, $offset = 0) {
        $check = $this->checksessionactive();
        if ($check == TRUE) {
            $updateAction = array('action' => '1');
            $removeuser = $this->handyman_model->UpdateHandyman($id, $updateAction);
            $output = $removeuser . " Activate successfully";
            $userSession = array('success' => $output);
            $this->session->set_userdata($userSession);
        }
    }

    /**
     *  @desc :  This funtion for to make approve  handyman
     *  @param : handyman id and offset
     *  @return :void
     */
    function approvefilter($id, $offset = 0) {
        $check = $this->checkapprovesession();
        if ($check == TRUE) {
            $date = date("Y-m-d H:i:s");
            $updateAction = array('approved' => '1', 'action' => '1', 'approve_by' => $this->session->userdata('employee_id'), 'approve_date' => $date);
            $removeuser = $this->handyman_model->UpdateHandyman($id, $updateAction);
            $output = $removeuser . " Approved successfully";
            $userSession = array('success' => $output);
            $this->session->set_userdata($userSession);
        }
    }

    /**
     *  @desc :  This funtion for to make verify handyman
     *  @param : handyman id
     *  @return :void
     */
    function verify($id) {
        $check = $this->checkverify();
        if ($check == TRUE) {
            $date = date("Y-m-d H:i:s");
            $updateAction = array('approved' => '0', 'action' => '0', 'verify_by' => $this->session->userdata('employee_id'), 'verify_date' => $date, 'verified' => '1');
            $removeuser = $this->handyman_model->UpdateHandyman($id, $updateAction);
            $output = $removeuser . " Approved successfully";
            $userSession = array('success' => $output);
            $this->session->set_userdata($userSession);
        }
    }

    /**
     *  @desc : This function for check validation active or deactive
     *  @param : void
     *  @return : tue if validation true otherwise FALSE
     */
    function checksessionactive() {
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee') && ($this->session->userdata('activate/deactivate') == 1)) {
            return TRUE;
        } else {
            echo "<script>alert('Sory You are not Authorised')</script>";
            // echo"<script>window.history.back()</script>";
        }
    }

    /**
     *  @desc : This function for check validation verify
     *  @param : void
     *  @return : tue if validation true otherwise FALSE
     */
    function checkverify() {
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee') && ($this->session->userdata('verify') == 1)) {
            return TRUE;
        } else {
            echo "<script>alert('Sory You are not Authorised')</script>";
            // echo"<script>window.history.back()</script>";
        }
    }

    /**
     *  @desc : This function for delete
     *  @param : id
     *  @return : void
     */
    function delete($id) {
        $check = $this->checksessiondelete();
        if ($check) {
            $this->handyman_model->delete($id);
            $output = " Delete successfully";
            $userSession = array('error' => $output);
            $this->session->set_userdata($userSession);
        }
    }

    /**
     *  @desc : This function for check validation active or deactive
     *  @param : void
     *  @return : tue if validation true otherwise FALSE
     */
    function checksessiondelete() {
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee') && ($this->session->userdata('deletehandyman') == 1)) {
            return TRUE;
        } else {
            echo "<script>alert('Sorry You are not Authorised')</script>";
            // echo"<script>window.history.back()</script>";
        }
    }

}
