<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Login extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();
        $this->load->model('employeelogin');
        $this->load->model('employee_model');
        $this->load->model('filter_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
    }

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     * 	- or -
     * 		http://example.com/index.php/welcome/index
     * 	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     *  @desc : Login Function
     *  @return : Admin Login
     */

    /**
     *  @desc : This funtion is to check login details of an employee
     *  @param : void
     *  @return : void
     */
    public function index() {

        if ($_POST) {
            $employee_id = $this->input->post('employee_id');
            $employee_password = $this->input->post('employee_password');
            $login = $this->employeelogin->login($employee_id, md5($employee_password));
            if ($login) {
                $this->session->unset_userdata('email');
                $this->setSession($login[0]['employee_id'], $login[0]['id'], $login[0]['phone']);
                $this->dashboard();
            } else {
                $output = "Employee Name/ID or password is incorrect.";
                $this->loadView($output);
            }
        } else {
            $this->load->view('employee/login');
        }
    }

    /**
     *  @desc : This funtion load index page
     *  @param : void
     *  @return : void
     */
    function dashboard() {
        $this->checkUserSession();


        //$this->load->view('employee/header',$result);
        //$this->load->view('employee/finduser',$result);
        redirect(base_url() . DEFAULT_SEARCH_PAGE);
    }

    /**
     *  @desc : This function load Error Message
     *  param : output/Sucess Message
     *  @return : Error on Admin Login Page
     */
    function loadView($output) {
        $data['error'] = $output;
        $this->load->view('employee/login', $data);
    }

    /**
     *  @desc : This function is to Set Session of a particular employee once he/she is logged in.
     *  @param : employee_id- id of employee for whom session is created
     *  @return : void
     */
    function setSession($employee_id, $id, $phone) {

        $userSession = array(
            'session_id' => md5(uniqid(mt_rand(), true)),
            'employee_id' => $employee_id,
            'id' => $id,
            'phone' => $phone,
            'sess_expiration' => 30000,
            'loggedIn' => TRUE,
            'userType' => 'employee'
        );

        $this->session->set_userdata($userSession);
    }

    /**
     *  @desc :This funtion will check Session of an employee before taking any
     *         action that he is logged in or not and is his session active or expired.
     *  param : void
     *  @return : void
     */
    function checkUserSession() {
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            $this->session->sess_destroy();
            redirect(base_url() . "employee/login");
        }
    }

    /**
     *  @desc : This funtion is for logout of an employee
     *
     *  This function distroys the login session of an employee.
     *
     *  param : void
     *  @return : void
     */
    function logout() {
        $this->session->sess_destroy();
        redirect(base_url() . "employee/login");
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
