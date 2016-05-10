<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

  /**
   * load list modal and helpers
   */
     function __Construct(){
     parent::__Construct();
     $this->load->model('handyman_model');
     $this->load->model('user_model');
     $this->load->model('filter_model');
     $this->load->helper(array('form', 'url'));
     $this->load->library('form_validation');
     $this->load->library("pagination");
     
     if (($this->session->userdata('loggedIn')==TRUE)&&($this->session->userdata('userType')=='admin'))  {
          return TRUE ;
        } else {
          redirect(base_url()."admin");
        }
    }

  /**
   * @desc : This funtion get user report and load filter
   * @param : void
   *  @return : report info
   */

   public function index(){
    $getreport['report'] = $this->user_model->getuserreport();
    $result['service']       = $this->filter_model->getserviceforfilter();
    $result['agent']         = $this->filter_model->getagent();
    $this->load->view('admin/header',$result);
    $this->load->view('admin/viewuserreport',$getreport);
    
   }

  /**
   * @desc : This funtion get user detail and load filter
   * @param : offset
   *  @return : void
   */



   public function viewuser($offset = 0){
    $offset = ($this->uri->segment(3) != '' ? $this->uri->segment(3): 0);
    $config['total_rows'] = $this->user_model->total_user();
    $config['per_page']= 10;
    $config['first_link'] = 'First';
    $config['last_link'] = 'Last';
    $config['uri_segment'] = 3;
    $config['base_url']= base_url().'/user/viewuser'; 
    $config['suffix'] = http_build_query($_GET, '', "&"); 
    $this->pagination->initialize($config);
    $this->data['paginglinks'] = $this->pagination->create_links();

    if($this->data['paginglinks']!= '') {
      $this->data['pagermessage'] = 'Showing '.((($this->pagination->cur_page-1)*$this->pagination->per_page)+1).' to '.($this->pagination->cur_page*$this->pagination->per_page).' of '.$this->pagination->total_rows;
    }   
    $this->data['result'] = $this->user_model->getuser($config["per_page"], $offset);  
    $result['service']       = $this->filter_model->getserviceforfilter();
    $result['agent']         = $this->filter_model->getagent();
    $this->load->view('admin/header',$result);
    $this->load->view('admin/user', $this->data);
   }

  /**
   * @desc : This funtion for deactive user 
   * param : user id,offset
   *  @return : hide user
   */

   function deactivate($user_id,$offset=0){

    $updateAction = array('action' =>'0' );
    $removeuser = $this->user_model->removeuser($user_id,$updateAction);
    $output = $removeuser." deactivate successfully";
    $userSession = array('success' =>$output);
    $this->session->set_userdata($userSession);
    redirect(base_url()."user/viewuser/$offset") ;
   }
  /**
   * @desc : This funtion  active 
   * param : user id ,offset
   *  @return : hide user
   */


   function toDooactive($user_id,$offset=0){
    $updateAction = array('action' =>'1' );
    $removeuser = $this->user_model->removeuser($user_id,$updateAction);
    $output = $removeuser." activate successfully";
    $userSession = array('success' =>$output);
    $this->session->set_userdata($userSession);
    redirect(base_url()."user/viewuser/$offset") ;
   }

  /**
   * @desc : This funtion  for sending mail
   * param : input request user email and comment
   *  @return : redirect user index
   */

   function sending_mail(){
   
   $this->load->library('email');
   $user_email = $this->input->post('user_email');
   $comment    = $this->input->post('comment');
   //print_r($user_email);
   $this->email->from('suneel@numetriclabz.com', 'boloaaka');
   $this->email->to($user_email); 
   $this->email->subject('reply report');
   $message = $comment;
   $this->email->message($message);  
   $this->email->send();
   //echo $this->email->print_debugger();
    $output  =" Sent Mail Successfully";
    $userSession = array('success' =>$output);
    $this->session->set_userdata($userSession);
    redirect(base_url()."user") ; 
   }

  

  /**
   *  @desc : This function  deactivate user report and add comment
   *  @param : void
   *  @return : tue if validation true otherwise FALSE
   */

  function deactivate_report(){
    $id                = $this->input->post('id');
    $insert['comment'] = $this->input->post('comment');
    $insert['isreport_active']  = '0';
    $this->user_model->add_comment_report($id,$insert);
    $this->user_model->add_verificationlist($id);
    $output = " Deactivate successfully";
    $userSession = array('success' =>$output);
    $this->session->set_userdata($userSession);
    redirect(base_url().'user');
   // print_r($insert);

  }


  /**
   *  @desc : This function save message and subject to sena mail for markting
   *  @param : void
   *  @return : tue if validation true otherwise FALSE
   */


  function marketing_mail_message(){
    $checkmessage = $this->checkmessagevalidation();
    if($checkmessage==true){
        $message['message'] = $this->input->post('message');
        $message['subject'] = $this->input->post('subject');
        $sending_mail = $this->user_model->mail_messageSave($message);
        $output['success'] = "Message Saved";
        $output['message']  = $this->user_model->getmail_message();
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

