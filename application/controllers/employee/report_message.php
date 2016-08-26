<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_message extends CI_Controller {

  /**
   * @desc : load list modal and helpers
   */
      function __Construct(){
        parent::__Construct();
        $this->load->model('report_message_model');
        $this->load->model('filter_model'); 
        $this->load->model('employee_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('session');
         if (($this->session->userdata('loggedIn')==TRUE)&&($this->session->userdata('userType')=='employee'))  {
          return TRUE ;
        } else {
          redirect(base_url()."employee/login");
        }
    }
/**
 *  @desc : This function will update signup messgae
 *  @param :void
 *  @return : void
 */
  public function index(){
      if($_POST){
           $data['report_message'] = $this->input->post('report_message');
           $update = $this->report_message_model->updatereport_message($data);
           $report_message['report_message'] = $this->report_message_model->getreport_message();
           $report_message['sucess']    = "Updated signup message sucessfully";
           $results['service']          = $this->filter_model->getserviceforfilter();
           $results['agent']            = $this->filter_model->getagent();
           $employee_id                 = $this->session->userdata('employee_id');
           $results['one']              = $this->employee_model->verifylist($employee_id,'0');
           $results['three']            = $this->employee_model->verifylist($employee_id,'2');
           $results['forteen']          = $this->employee_model->verifylist($employee_id,'14');
           $this->load->view('employee/header',$results);
           $this->load->view('employee/report_message',$report_message);

              
      } else {
        $report_message['report_message'] = $this->report_message_model->getreport_message();
        $results['service']           = $this->filter_model->getserviceforfilter();
        $results['agent']             = $this->filter_model->getagent();
        $employee_id                  = $this->session->userdata('employee_id');
        $results['one']               = $this->employee_model->verifylist($employee_id,'0');
        $results['three']             = $this->employee_model->verifylist($employee_id,'2');
        $results['forteen']           = $this->employee_model->verifylist($employee_id,'14');
        $this->load->view('employee/header',$results);
        $this->load->view('employee/report_message',$report_message);

      } 

  }

//end of controllers

}
