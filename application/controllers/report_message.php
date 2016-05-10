<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_message extends CI_Controller {

  /**
   * @desc : load list modal and helpers
   */
      function __Construct(){
        parent::__Construct();
        $this->load->model('report_message_model');
        $this->load->model('filter_model'); 
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('session');
        if (($this->session->userdata('loggedIn')==TRUE)&&($this->session->userdata('userType')=='admin'))  {
          return TRUE ;
        } else {
          redirect(base_url()."admin");
        }
    }
/**
 *  @desc : This function will update signup messgae and call filter
 *  @param :void
 *  @return : void
 */
  public function index(){
      if($_POST){
           $data['report_message'] = $this->input->post('report_message');
           $update = $this->report_message_model->updatereport_message($data);
           $report_message['report_message'] = $this->report_message_model->getreport_message();
           $report_message['sucess']    = "Updated signup message sucessfully";
           $result['service']       = $this->filter_model->getserviceforfilter();
           $result['agent']         = $this->filter_model->getagent();
           $this->load->view('admin/header',$result);
           $this->load->view('admin/report_message',$report_message);

              
      } else {
        $report_message['report_message'] = $this->report_message_model->getreport_message();
        $result['service']       = $this->filter_model->getserviceforfilter();
        $result['agent']         = $this->filter_model->getagent();
        $this->load->view('admin/header',$result);
        $this->load->view('admin/report_message',$report_message);

      } 

  }

//end of controllers

}
