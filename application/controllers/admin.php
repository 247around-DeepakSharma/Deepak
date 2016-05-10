<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

  /**
   * load list modal and helpers
   */
     function __Construct(){
     parent::__Construct();
     $this->load->model('handyman_model');
     $this->load->model('filter_model');
     $this->load->helper(array('form', 'url'));
     $this->load->library('form_validation');
     $this->load->library("pagination");
    }

  /**
   * Index Page for this controller.
   *  @desc : Login Function
   *  @param : void
   *  @return : Admin Login
   */

    public function index()
    {
		
      if($_POST){
      $email    = $this->input->post('email');
      $password = $this->input->post('password');
      $login    = $this->handyman_model->login($email,md5($password));
        if($login){
           $this->session->unset_userdata('employee_id');
           $this->setSession($login[0]['email'],$login[0]['id']);
           $this->dashboard();
        }
        else {
            $output= "Email or password is incorrect.";
            $this->loadView($output);
        }

    } else {
      $this->load->view('login');

    }
	}

  /**
    * @desc : This funtion load index page after login
    * @param : offset 
    * @return : void
    */
    function dashboard($offset =0){
          $this->checkUserSession();
          $offset = ($this->uri->segment(3) != '' ? $this->uri->segment(3): 0);
          $config['total_rows'] = $this->handyman_model->total_count();
          $config['per_page']= 10;
          $config['first_link'] = 'First';
          $config['last_link'] = 'Last';
          $config['uri_segment'] = 3;
          $config['base_url']= base_url().'/admin/dashboard'; 
          $config['suffix'] = http_build_query($_GET, '', "&"); 
          $this->pagination->initialize($config);
          $this->data['paginglinks'] = $this->pagination->create_links();
              // Showing total rows count 
          if($this->data['paginglinks']!= '') {
            $this->data['pagermessage'] = 'Showing '.((($this->pagination->cur_page-1)*$this->pagination->per_page)+1).' to '.($this->pagination->cur_page*$this->pagination->per_page).' of '.$this->pagination->total_rows;
          }   
          $this->data['result'] = $this->handyman_model->get_handyman($config["per_page"], $offset); 
          $result['service']       = $this->filter_model->getserviceforfilter();
          $result['agent']         = $this->filter_model->getagent(); 
          $this->load->view('admin/header',$result);
          $this->load->view('admin/handyman',$this->data);
        
      
    }



  /**
   *  @desc : This function load Error Message
   *  param : output/Sucess Message
   *  @return : Error on Admin Login Page
   */

    function loadView($output) {
        $data['error'] = $output;
        $this->load->view('login',$data);
    }

  /**
   *  @desc : This function Set Session
   *  @param : Email and user id
   *  @param : void
   */

    function setSession($email,$id) {
    
        $userSession = array(
                                'session_id'      => md5(uniqid(mt_rand(), true)),
                                'email'           => $email,
                                'id'              => $id,
                                'sess_expiration' => 3600,
                                'loggedIn'        => TRUE,
                                'userType'        => 'admin'
                            );

        $this->session->set_userdata($userSession);
    }
   
  
  /**
   * @desc :This funtion will check Session
   * @param : void
   * @return : true if admin login otherwise redirtect admin index function
   */
    function checkUserSession() {
       if (($this->session->userdata('loggedIn')==TRUE)&&($this->session->userdata('userType')=='admin'))  {
          return TRUE ;
        } else {
           $this->session->sess_destroy();
           redirect(base_url()."admin");
        }
    }   

  /**
   * @desc :This funtion for logout
   */
    function logout(){
       $this->session->sess_destroy();
       redirect(base_url()."admin"); 
    }

  /**
   * @desc :This funtion change admin password
   * @param :void
   * @return : void
   */

    function reset_password(){
      $validate = $this->validate();
      if($validate){
           $reset['email']    = $this->input->post('email');
           $reset['password'] = md5($this->input->post('password'));
           $resetpassword = $this->handyman_model->reset_password($reset);
           $output = "Reset Successfully.";
           $userSession = array('success' =>$output);
           $this->session->set_userdata($userSession);
           $this->loadrestPassword();
      } else {
            $this->loadrestPassword();
        }
    }

    function validate(){
      $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
      $this->form_validation->set_rules('password', 'Password', 'required|matches[passconf]|min_length[5]');
      $this->form_validation->set_rules('passconf', 'Password Confirmation', 'required');
      $this->form_validation->set_rules('oldpassword', 'Password', 'required|callback_checkoldpassword');
        if ($this->form_validation->run() == FALSE) {
            return FALSE;
        }
        else {
            return true;
        }
    }

   function checkoldpassword(){
      $check = $this->handyman_model->checkoldpassword(md5($_POST['oldpassword']));
      if($check){
        return true;
      } else {
            $this->form_validation->set_message('checkoldpassword', 'Old Password is wrong.');
             return FALSE;
         }
   }

  /**
   * @desc :This funtion for load change password view
   * @param :void
   * @return : void
   */

    function loadrestPassword(){
        $result['service']       = $this->filter_model->getserviceforfilter();
        $result['agent']         = $this->filter_model->getagent(); 
        $this->load->view('admin/header',$result);
        $this->load->view('admin/changePassword');
      }
   
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
