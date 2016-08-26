<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class  Review_messgae extends CI_Controller {

  /**
   * @desc : load list modal and helpers
   */
      function __Construct(){
        parent::__Construct();
        $this->load->model('review_model'); 
        $this->load->model('filter_model');
        $this->load->model('employee_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('session');
        if (($this->session->userdata('loggedIn')==TRUE)&&($this->session->userdata('userType')=='employee')) {
          return TRUE ;
        } else {
          redirect(base_url()."employee/login");
        }
    }

  /**
   *  @desc : This function will insert review and load filter and verifylist
   *  @param :void
   *  @return : void
   */
    public function index(){
       if($_POST){
           $data['reviewmessage'] = $this->input->post('reviewmessage');
           $update = $this->review_model->updatereviewmessage($data);
           $reviewmessage['reviewmessage'] = $this->review_model->getreviewmessage();
           $reviewmessage['sucess']  = "Updated review message sucessfully";
           $results['service']       = $this->filter_model->getserviceforfilter();
           $results['agent']         = $this->filter_model->getagent();
           $employee_id              = $this->session->userdata('employee_id');
           $results['one']           = $this->employee_model->verifylist($employee_id,'0');
           $results['three']         = $this->employee_model->verifylist($employee_id,'2');
           $results['forteen']       = $this->employee_model->verifylist($employee_id,'14');
           $this->load->view('employee/header',$results);
           $this->load->view('employee/reviewmessage',$reviewmessage);

              
      } else {
        $reviewmessage['reviewmessage'] = $this->review_model->getreviewmessage();
        $results['service']       = $this->filter_model->getserviceforfilter();
        $employee_id              = $this->session->userdata('employee_id');
        $results['one']           = $this->employee_model->verifylist($employee_id,'0');
        $results['three']         = $this->employee_model->verifylist($employee_id,'2');
        $results['forteen']       = $this->employee_model->verifylist($employee_id,'14');
        $results['agent']         = $this->filter_model->getagent();
        $this->load->view('employee/header',$results);
        $this->load->view('employee/reviewmessage',$reviewmessage);

      } 
  }
    
}
