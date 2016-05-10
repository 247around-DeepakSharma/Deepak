<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Signup_message extends CI_Controller {

  /**
   * @desc : load list modal and helpers
   */
      function __Construct(){
        parent::__Construct();
        $this->load->model('apis');
        $this->load->model('signup_message_model');
        $this->load->model('filter_model'); 
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
       if (($this->session->userdata('loggedIn')==TRUE)&&($this->session->userdata('userType')=='employee')&&($this->session->userdata('signup')=='1'))  {
          return TRUE ;
        } else {
          redirect(base_url()."employee/login");
        }
    }
/**
 *  @desc : This function will update signup messgae and load filter and verifylist
 *  @param :void
 *  @return : void
 */
  public function index(){
      if($_POST){
           $data['signup_message'] = $this->input->post('signup_message');
           $update = $this->signup_message_model->updatesignup_message($data);
           $signup_message['signup_message'] = $this->signup_message_model->getsignup_message();
           $signup_message['sucess']     = "Updated signup message sucessfully";
           $results['service']           = $this->filter_model->getserviceforfilter();
           $results['agent']             = $this->filter_model->getagent();
           $employee_id                  = $this->session->userdata('employee_id');
           $results['one']               = $this->employee_model->verifylist($employee_id,'0');
           $results['three']             = $this->employee_model->verifylist($employee_id,'2');
           $results['forteen']           = $this->employee_model->verifylist($employee_id,'14');
           $this->load->view('employee/header',$results);
           $this->load->view('employee/signup_message',$signup_message);

              
      } else {
        $results['service']       = $this->filter_model->getserviceforfilter();
        $results['agent']         = $this->filter_model->getagent();
        $signup_message['signup_message'] = $this->signup_message_model->getsignup_message();
        $employee_id                  = $this->session->userdata('employee_id');
        $results['one']               = $this->employee_model->verifylist($employee_id,'0');
        $results['three']             = $this->employee_model->verifylist($employee_id,'2');
        $results['forteen']           = $this->employee_model->verifylist($employee_id,'14');
        $this->load->view('employee/header',$results);
        $this->load->view('employee/signup_message',$signup_message);

      } 

  }

//end of controllers

}
