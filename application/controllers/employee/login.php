<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

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
    public function index() {

	if ($_POST) {
	    $employee_id = $this->input->post('employee_id');
	    $employee_password = $this->input->post('employee_password');
	    $login = $this->employeelogin->login($employee_id, md5($employee_password));
	    if ($login) {
		$this->session->unset_userdata('email');
		$this->setSession($login[0]['employee_id'], $login[0]['id'], $login[0]['phone'], $login[0]['right_for_add_handyman'], $login[0]['right_for_add_service'], $login[0]['right_for_activate_deactivate_handyman'], $login[0]['right_for_activate_deactivate_service'], $login[0]['right_for_xls_for_handyman'], $login[0]['right_for_add_employee'], $login[0]['right_for_update_employee'], $login[0]['right_for_report_messgae'], $login[0]['right_for_signup_message'], $login[0]['right_for_review_message'], $login[0]['right_for_approve_handyman'], $login[0]['right_for_delete'], $login[0]['right_for_verifyhandyman'], $login[0]['right_for_popularsearch'], $login[0]['right_for_review']);
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
     * This funtion load index page
     */
    function dashboard() {
	$this->checkUserSession();
	$employee_id = $this->session->userdata('employee_id');
	$result['result'] = $this->employee_model->verifylist($employee_id, $date = 0);
	$result['one'] = $this->employee_model->verifylist($employee_id, '0');
	$result['three'] = $this->employee_model->verifylist($employee_id, '2');
	$result['forteen'] = $this->employee_model->verifylist($employee_id, '14');
	$result['service'] = $this->filter_model->getserviceforfilter();
	$result['agent'] = $this->filter_model->getagent();

	//$this->load->view('employee/header',$result);
	//$this->load->view('employee/finduser',$result);
	redirect('employee/booking/view_all_pending_queries');
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
     *  @desc : This function Set Session
     *  param : Email and user id
     */
    function setSession($employee_id, $id, $phone, $addhandyman, $addservice, $activate_deactivate, $active_service, $xls_for_handyman, $create_employee, $update_employee, $report, $signup, $review_messgae, $approvehandyman, $delete, $verify, $popularsearch, $review) {

	$userSession = array(
	    'session_id' => md5(uniqid(mt_rand(), true)),
	    'employee_id' => $employee_id,
	    'id' => $id,
	    'phone' => $phone,
	    'add handyman' => $addhandyman,
	    'add service' => $addservice,
	    'activate/deactivate' => $activate_deactivate,
	    'activate/deactivate_service' => $active_service,
	    'xls_for_handyman' => $xls_for_handyman,
	    'create_employee' => $create_employee,
	    'update_employee' => $update_employee,
	    'report' => $report,
	    'signup' => $signup,
	    'review_messgae' => $review_messgae,
	    'approvehandyman' => $approvehandyman,
	    'deletehandyman' => $delete,
	    'verify' => $verify,
	    'popularsearch' => $popularsearch,
	    'review' => $review,
	    'sess_expiration' => 30000,
	    'loggedIn' => TRUE,
	    'userType' => 'employee'
	);

	$this->session->set_userdata($userSession);
    }

    /**
     * @desc :This funtion will check Session
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
     * @desc :This funtion for logout
     */
    function logout() {
	$this->session->sess_destroy();
	redirect(base_url() . "employee/login");
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
