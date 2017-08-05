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
        $this->load->model('booking_model');
        $this->load->model('dealer_model');
        $this->load->model('vendor_model');
        $this->load->model('service_centers_model');
        $this->load->model('partner_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library("session");
        $this->load->library('user_agent');
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
                $this->session->sess_create();
                $this->setSession($login[0]['employee_id'], $login[0]['id'], $login[0]['phone'],$login[0]['official_email']);
                
                //Saving Login Details in Database
                $data['browser'] = $this->agent->browser();
                $data['agent_string'] = $this->agent->agent_string();
                $data['ip'] = $this->session->all_userdata()['ip_address'];
                $data['action'] = _247AROUND_LOGIN;
                $data['entity_type'] = $this->session->all_userdata()['userType'];
                $data['agent_id'] = $this->session->all_userdata()['id'];
                $data['entity_id'] = _247AROUND;
                
                $login_id = $this->employee_model->add_login_logout_details($data);
                //Adding Log Details
                if($login_id){
                    log_message('info',__FUNCTION__.' Logging details have been captured for employee. Details are : '.print_r($data, TRUE));
                }else{
                    log_message('info',__FUNCTION__.' Err in capturing logging details for employee. Details are :  '. print_r($data, TRUE));
                }
               
                $this->dashboard();
            } else {
                $output = "Employee Name/ID or password is incorrect.";
                $this->loadView($output);
            }
        } else {
            $data['partner_logo'] = $this->booking_model->get_partner_logo();
            $this->load->view('employee/login',$data);
        }
    }

    /**
     *  @desc : This funtion load index page
     *  @param : void
     *  @return : void
     */
    function dashboard() {
        $this->checkUserSession();

        if($this->session->userdata('user_group') === 'admin') {
            redirect(base_url() . 'employee/dashboard');
        } else {
            redirect(base_url() . DEFAULT_SEARCH_PAGE);
        }
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
    function setSession($employee_id, $id, $phone,$official_email) {
        // Getting values for Groups of particular employee
        $groups = $this->employeelogin->get_employee_group_name($employee_id);
        if($groups){
        $userSession = array(
            'session_id' => md5(uniqid(mt_rand(), true)),
            'employee_id' => $employee_id,
            'id' => $id,
            'phone' => $phone,
            'sess_expiration' => 30000,
            'loggedIn' => TRUE,
                'userType' => 'employee',
                'user_group'=> $groups,
            'official_email'=>$official_email
        );
        }
        else{
            //Logging Message 
            log_message('info',__FUNCTION__.' No group has been assigned to employee with ID : '.$employee_id);
            echo 'Sorry, this User is Not assigned in any groups.';
            exit;
        }

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
        $this->checkUserSession();
        //Saving Logout Details in Database

        $data['browser'] = $this->agent->browser();
        $data['agent_string'] = $this->agent->agent_string();
        $data['ip'] = $this->session->userdata('ip_address');
        $data['action'] = _247AROUND_LOGOUT;
        $data['entity_type'] = $this->session->userdata('userType');
        $data['agent_id'] = $this->session->userdata('id');
        $data['entity_id'] = _247AROUND;
        
        $logout_id = $this->employee_model->add_login_logout_details($data);
        //Adding Log Details
        if ($logout_id) {
            log_message('info', __FUNCTION__ . ' LOG OUT  details have been captured for employee ' . print_r($data,TRUE));
        } else {
            log_message('info', __FUNCTION__ . ' Err in capturing LOG OUT details for employee ' . print_r($data, TRUE));
        }

        $this->session->sess_destroy();
        redirect(base_url() . "employee/login");
    }
    
    function dealer_login_form(){
        //$this->session->sess_destroy();
        $sess = $this->session->userdata('dealer_id');
        if(isset($sess)){
            $data['partner_logo'] = $this->booking_model->get_partner_logo();
            $this->load->view('dealers/login' ,$data);
        } else {
            echo $this->session->userdata('userType');
            echo 'redirect';
        }
    }
    
    function dealer_login_process(){
        
        $data['user_id'] = $this->input->post("user_id");
        $data['password'] = md5($this->input->post('password'));
        $data['entity'] = "dealer";
        $data['active'] = 1;
        $agent = $this->dealer_model->entity_login($data);
        if($agent){
          
            $select = 'partner_id, dealer_name,dealer_phone_number_1';
            $condition = array(
            "where" => array('dealer_details.dealer_id' => $agent[0]['entity_id'],'dealer_brand_mapping.dealer_id' => $agent[0]['entity_id'], 
                'dealer_brand_mapping.active' => 1, 'dealer_details.active'=> 1),
            "where_in" => array(),
            "search" => array(),
            "order_by" => "");
            $partner_data = $this->dealer_model->get_dealer_mapping_details($condition, $select);
            if(!empty($partner_data)){
                $partner_id = array();
                foreach ($partner_data as $value) {
                    array_push($partner_id, $value['partner_id']);
                }
                $this->session->sess_create();
                $this->set_dealer_session($agent[0]['entity_id'],$agent[0]['agent_name'], 
                        $agent[0]['agent_id'], $partner_id, $partner_data[0]['dealer_name'],
                        $partner_data[0]['dealer_phone_number_1']);
                redirect(base_url() . 'dealers/add_booking');
                
            } else {
                $userSession = array('error' => 'Login De-Activated! Please contact 247Around Team');
                $this->session->set_userdata($userSession);
                redirect(base_url() . "dealers");
            }
        } else {
            $userSession = array('error' => 'Please enter correct user name and password');
            $this->session->set_userdata($userSession);
            redirect(base_url() . "dealers");
        }
    }
    
    function set_dealer_session($dealer_id, $agent_name, $agent_id, $partner_data, $dealer_name, $dealer_ph){
        $userSession = array(
            'session_id' => md5(uniqid(mt_rand(), true)),
            'dealer_id' => $dealer_id,
            'agent_name' => $agent_name,
            'dealer_name' => $dealer_name,
            'dealer_phone_number' => $dealer_ph,
            'agent_id' => $agent_id,
            'partners' => $partner_data,
            'sess_expiration' => 30000,
            'loggedIn' => TRUE,
            'userType' => 'dealers'
        );
        
         $this->session->set_userdata($userSession);
    }
    
    function dealer_logout() {
        $this->checkDealerSession();
        //Saving Logout Details in Database

        $data['browser'] = $this->agent->browser();
        $data['agent_string'] = $this->agent->agent_string();
        $data['ip'] = $this->session->userdata('ip_address');
        $data['action'] = _247AROUND_LOGOUT;
        $data['entity_type'] = $this->session->userdata('userType');
        $data['agent_id'] = $this->session->userdata('agent_id');
        $data['entity_id'] = $this->session->userdata('dealer_id');
        
        $logout_id = $this->employee_model->add_login_logout_details($data);
        //Adding Log Details
        if ($logout_id) {
            log_message('info', __FUNCTION__ . ' LOG OUT  details have been captured for employee ' . print_r($data,TRUE));
        } else {
            log_message('info', __FUNCTION__ . ' Err in capturing LOG OUT details for employee ' . print_r($data, TRUE));
        }

        $this->session->sess_destroy();
        redirect(base_url() . "dealers");
    }
    
    function checkDealerSession(){
        log_message("info", __METHOD__);
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'dealers') 
                && !empty($this->session->userdata('dealer_id')) ) {
            return TRUE;
        } else {
            log_message('info', __FUNCTION__. " Session Expire for Service Center");
            $this->session->sess_destroy();
            redirect(base_url() . "dealers");
        }
    }
    
    /**
     * @Desc: This function is used to login to particular Partner
     *          This function is being called using AJAX
     * @params: partner id
     * @return: void
     * 
     */
    function allow_log_in_to_partner($partner_id){
        //Getting partner details
        $this->session->sess_create();
       // $partner_id = $this->input->post('partner_id');
        $data['entity'] = "partner";
        $data['entity_id'] = $partner_id;
        $data['active'] = 1;
        $agent = $this->dealer_model->entity_login($data);
        
        if (!empty($agent)) {
            //get partner details now
            $partner_details = $this->partner_model->getpartner($partner_id);

            $this->setPartnerSession($partner_details[0]['id'], $partner_details[0]['public_name'], $agent[0]['agent_id'], $partner_details[0]['is_active']);
            log_message('info', 'Partner loggedIn  partner id' .
                    $partner_details[0]['id'] . " Partner name" . $partner_details[0]['public_name']);
            
           // redirect(base_url() . "partner/home");

        }
            log_message('info',__FUNCTION__." No partner Details has been found for Login");
    }
    
    /**
     * @desc: This function Sets Session
     * @param: Partrner id
     * @param: Partner name
     * @return: void
     */
    function setPartnerSession($partner_id, $partner_name, $agent_id,$status) {
        $userSession = array(
            'session_id' => md5(uniqid(mt_rand(), true)),
            'partner_id' => $partner_id,
            'partner_name' => $partner_name,
            'agent_id' => $agent_id,
            'sess_expiration' => 600000,
            'loggedIn' => TRUE,
            'userType' => 'partner',
            'status' => $status
        );

        $this->session->set_userdata($userSession);
        
        //Saving Login Details in Database
        $login_data['browser'] = $this->agent->browser();
        $login_data['agent_string'] = $this->agent->agent_string();
        $login_data['ip'] = $this->input->ip_address();
        $login_data['action'] = _247AROUND_LOGIN;
        $login_data['entity_type'] = $this->session->all_userdata()['userType'];
        $login_data['agent_id'] = $this->session->all_userdata()['agent_id'];
        $login_data['entity_id'] = $this->session->all_userdata()['partner_id'];

        $login_id = $this->employee_model->add_login_logout_details($login_data);
        //Adding Log Details
        if ($login_id) {
            log_message('info', __FUNCTION__ . ' Logging details have been captured for partner ' .$login_data['agent_id']);
        } else {
            log_message('info', __FUNCTION__ . ' Err in capturing logging details for partner ' . $login_data['agent_id']);
        }
    }
    
     function partner_login() {
        $data['user_id'] = $this->input->post('user_name');
        $data['password'] = md5($this->input->post('password'));
        $data['entity'] = "partner";
        $data['active'] = 1;
        $agent = $this->dealer_model->entity_login($data);
       
        if ($agent) {
            //get partner details now
            $partner_details = $this->partner_model->getpartner($agent[0]['entity_id'],FALSE);
            if($partner_details){
                $this->setPartnerSession($partner_details[0]['id'], $partner_details[0]['public_name'], $agent[0]['agent_id'],
                        $partner_details[0]['is_active']);
                log_message('info', 'Partner loggedIn  partner id' .
                        $partner_details[0]['id'] . " Partner name" . $partner_details[0]['public_name']);

                //Adding Log Details
                if ($partner_details[0]['is_active'] == 1) {
                    
                    redirect(base_url() . "partner/home");
                } else  if ($partner_details[0]['is_active'] == 0) { 
                    redirect(base_url() . "partner/invoice");
                } 

            }else{
                $userSession = array('error' => 'Sorry, your Login has been De-Activated');
                $this->session->set_userdata($userSession);
                redirect(base_url() . "partner/login");
            }
        } else {

            $userSession = array('error' => 'Please enter correct user name and password');
            $this->session->set_userdata($userSession);
            redirect(base_url() . "partner/login");
        }
    }
    
    /**
     * @Desc: This function is used to login to particular vendor
     *          This function is being called using AJAX
     * @params: vendor id
     * @return: void
     * 
     */
    function allow_log_in_to_vendor($vendor_id) {
        //Getting vendor details
        $this->session->sess_create();
       
        $agent = $this->service_centers_model->get_sc_login_details_by_id($vendor_id);
        if (!empty($agent)) {
            //get sc details now
            $sc_details = $this->vendor_model->getVendorContact($vendor_id);
            
            //Setting logging vendor session details
            $this->setVendorSession($sc_details[0]['id'], $sc_details[0]['company_name'], 
                    $agent[0]['id'], $sc_details[0]['is_update'], 
                    $sc_details[0]['is_upcountry'],$sc_details[0]['is_sf'], $sc_details[0]['is_cp']);
           
            if ($this->session->userdata('is_sf') === '1') {
                echo "service_center/pending_booking";
            } else if ($this->session->userdata('is_cp') === '1') {
                echo "service_centers/bb_oder_details";
            }
        }
    }

    /**
     * @desc: This function Sets Session
     * @param: Service center id
     * @param: Service center name
     * @param: Agent Id
     * @param: is update
     * @return: void
     */
    function setVendorSession($service_center_id, $service_center_name, $sc_agent_id, $update, $is_upcountry,$sf, $cp) {
	$userSession = array(
	    'session_id' => md5(uniqid(mt_rand(), true)),
	    'service_center_id' => $service_center_id,
	    'service_center_name' => $service_center_name,
            'service_center_agent_id' => $sc_agent_id,
            'is_upcountry' => $is_upcountry,
            'is_update' => $update,
	    'sess_expiration' => 30000,
	    'loggedIn' => TRUE,
	    'userType' => 'service_center',
            'is_sf' => $sf,
            'is_cp' => $cp
	);

        $this->session->set_userdata($userSession);
        
        //Saving Login Details in Database
        $login_data['browser'] = $this->agent->browser();
        $login_data['agent_string'] = $this->agent->agent_string();
        $login_data['ip'] = $this->input->ip_address();
        $login_data['action'] = _247AROUND_LOGIN;
        $login_data['entity_type'] = $this->session->all_userdata()['userType'];
        $login_data['agent_id'] = $this->session->all_userdata()['service_center_agent_id'];
        $login_data['entity_id'] = $this->session->all_userdata()['service_center_id'];

        $login_id = $this->employee_model->add_login_logout_details($login_data);
        //Adding Log Details
        if ($login_id) {
            log_message('info', __FUNCTION__ . ' Logging details have been captured for service center ' . $login_data['agent_id']);
        } else {
            log_message('info', __FUNCTION__ . ' Err in capturing logging details for service center ' . $login_data['agent_id']);
        }
    }
    
    /**
     * @desc: This is used to login
     *
     * If user name and password matches allowed to login, else error message appears.
     *
     * @param: void
     * @return: void
     */
    function service_center_login() {
        $data['user_name'] = $this->input->post('user_name');
        $data['password'] = md5($this->input->post('password'));
        $agent = $this->service_centers_model->service_center_login($data);
        if ($agent) {
            //get sc details now
            $sc_details = $this->vendor_model->getVendorContact($agent['service_center_id']);
            if (!empty($sc_details)) {
                $this->setVendorSession($sc_details[0]['id'], $sc_details[0]['company_name'], 
                        $agent['id'], $sc_details[0]['is_update'], 
                        $sc_details[0]['is_upcountry'],$sc_details[0]['is_sf'], 
                        $sc_details[0]['is_cp']);
                
                if($this->session->userdata('is_sf') === '1'){
                    redirect(base_url() . "service_center/pending_booking");
                }else if($this->session->userdata('is_cp') === '1'){
                    redirect(base_url() . "service_center/bb_oder_details");
                }
            } else {
                $userSession = array('error' => 'Please enter correct user name and password');
                $this->session->set_userdata($userSession);
                redirect(base_url() . "service_center");
            }
        } else {
            $userSession = array('error' => 'Please enter correct user name and password');
            $this->session->set_userdata($userSession);
            redirect(base_url() . "service_center");
        }
    }


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
