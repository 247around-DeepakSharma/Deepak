<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Marketingmail extends CI_Controller {

  /**
   * load list modal and helpers
   */
     function __Construct(){
     parent::__Construct();
     $this->load->model('user_model');
     $this->load->model('filter_model');
     $this->load->helper(array('form', 'url'));
     $this->load->library('form_validation');
    }

  /**
   *  @desc : This function send mail 
   *  @param : void
   *  @return : true
   */

    public  function index(){
       $this->load->library('email');
       $user_email  = $this->user_model->get_email();
       $message     = $this->user_model->getmail_message();
       foreach ($user_email as $key => $value) {
        $this->email->from('suneel@numetriclabz.com', 'boloaaka');
        $this->email->to($value['user_email']); 
        $this->email->subject($message[0]['subject']);
        $this->email->message($message[0]['message']);  
        $this->email->send();
     }
     return true;

    }

  /**
   *  @desc : This function save message and subject to send email 
   *  @param : void
   *  @return : void
   */

     function marketing_mail_message(){
    $checkmessage = $this->checkmessagevalidation();
    if($checkmessage==true){
        $message['message'] = $this->input->post('message');
        $message['subject'] = $this->input->post('subject');
        $sending_mail = $this->user_model->mail_messageSave($message);
        $this->index();
        $output['success'] = "Mail Sent";
        $output['message']  = $this->user_model->getmail_message();
        $result['service']       = $this->filter_model->getserviceforfilter();
        $result['agent']         = $this->filter_model->getagent();
        $this->load->view('admin/header',$result);
        $this->load->view('admin/mail_message',$output);


    } else {
      $output['message']  = $this->user_model->getmail_message();
      $result['service']       = $this->filter_model->getserviceforfilter();
      $result['agent']         = $this->filter_model->getagent();
      $this->load->view('admin/header',$result);
      $this->load->view('admin/mail_message',$output);
    }
  }

  /**
   *  @desc : This function for check validation
   *  @param : void
   *  @return : tue if validation true otherwise FALSE
   */

    public function checkmessagevalidation(){
        $this->form_validation->set_rules('message', 'Message ', 'requried');
        if ($this->form_validation->run() == FALSE) {
            return FALSE;
        }
        else {
            return true;
        }

    }
}
