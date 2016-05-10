<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sharetext extends CI_Controller {

  /**
   * @desc : load list modal and helpers
   */
      function __Construct(){
        parent::__Construct();
        $this->load->model('sharetext_model'); 
        $this->load->model('filter_model'); 
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
    
        if (($this->session->userdata('loggedIn')==TRUE)&&($this->session->userdata('userType')=='admin'))  {
          return TRUE ;
        } else {
          redirect(base_url()."admin");
        }
    }


  /**
   *  @desc : This function will update and view share button text and load filter
   *  @param : void
   *  @return : void
   */
    public function index(){
      if($_POST){
           $data['sharetext'] = $this->input->post('sharetext');
           $update = $this->sharetext_model->updatesharetext($data);
           $sharetext['sharetext'] = $this->sharetext_model->getsharetext();
           $result['service']       = $this->filter_model->getserviceforfilter();
           $result['agent']         = $this->filter_model->getagent();
           $sharetext['sucess']    = "Updated share text sucessfully";
           $this->load->view('admin/header',$result);
           $this->load->view('admin/viewsharetext',$sharetext);

              
      } else {
        $sharetext['sharetext'] = $this->sharetext_model->getsharetext();
        $result['service']       = $this->filter_model->getserviceforfilter();
        $result['agent']         = $this->filter_model->getagent();
        $this->load->view('admin/header',$result);
        $this->load->view('admin/viewsharetext',$sharetext);

      } 
    }
//controller end    
}
