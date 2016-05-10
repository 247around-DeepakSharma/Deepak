<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Signup_message extends CI_Controller {

  /**
   * @desc : load list modal and helpers
   */
      function __Construct(){
        parent::__Construct();
        $this->load->model('apis');
        $this->load->model('filter_model');
        $this->load->model('signup_message_model'); 
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
 *  @desc : This function will update signup messgae and load filter
*   @param :void
 *  @return : void
 */
  public function index(){
      if($_POST){
           $data['signup_message'] = $this->input->post('signup_message');
           $update = $this->signup_message_model->updatesignup_message($data);
           $signup_message['signup_message'] = $this->signup_message_model->getsignup_message();
           $result['service']       = $this->filter_model->getserviceforfilter();
           $result['agent']         = $this->filter_model->getagent();
           $signup_message['sucess']    = "Updated signup message sucessfully";
           $this->load->view('admin/header',$result);
           $this->load->view('admin/signup_message',$signup_message);

              
      } else {
        $signup_message['signup_message'] = $this->signup_message_model->getsignup_message();
        $result['service']       = $this->filter_model->getserviceforfilter();
        $result['agent']         = $this->filter_model->getagent();
        $this->load->view('admin/header',$result);
        $this->load->view('admin/signup_message',$signup_message);

      } 

  }

//end of controllers

}
