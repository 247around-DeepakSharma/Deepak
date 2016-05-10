<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Popularsearch extends CI_Controller {

  /**
   * @desc : load list modal and helpers
   */
      function __Construct(){
        parent::__Construct();
        $this->load->model('Popularsearch_model');
        $this->load->model('filter_model');
        $this->load->model('employee_model'); 
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->library("pagination");
        if (($this->session->userdata('loggedIn')==TRUE)&&($this->session->userdata('userType')=='employee') &&($this->session->userdata('popularsearch')=='1'))  {
          return TRUE ;
        } else {
          redirect(base_url()."admin");
        }
    }

  /**
   *  @desc : This function will insert popular search keyword and load view populae search and load verify list
   *  @param :void
   *  @return : void
   */
    public function index(){
     $validation = $this->checkValidation();
        if($validation){
             $data['searchkeyword']    = $this->input->post('search');
             $data['create_date']      = date("Y-m-d h:i:s");
             $insert                   = $this->Popularsearch_model->AddPopularSearchKeyword($data);
             $output['sucess']         = "Popular Search Keyword Added";
             $employee_id              = $this->session->userdata('employee_id');
             $result['one']            = $this->employee_model->verifylist($employee_id,'0');
             $result['three']          = $this->employee_model->verifylist($employee_id,'2');
             $result['forteen']        = $this->employee_model->verifylist($employee_id,'14');
             $result['service']        = $this->filter_model->getserviceforfilter();
             $result['agent']          = $this->filter_model->getagent();
             $this->load->view('admin/header',$result);
             $this->load->view('admin/popularsearch',$output);

        } else {
           $employee_id              = $this->session->userdata('employee_id');
           $result['one']            = $this->employee_model->verifylist($employee_id,'0');
           $result['three']          = $this->employee_model->verifylist($employee_id,'2');
           $result['forteen']        = $this->employee_model->verifylist($employee_id,'14');
           $result['service']        = $this->filter_model->getserviceforfilter();
           $result['agent']          = $this->filter_model->getagent();
           $this->load->view('employee/header',$result);
           $this->load->view('employee/popularsearch');

        }
       

    }

  /**
   *  @desc : This function for check validation
   *  @param : void
   *  @return : tue if validation true otherwise FALSE
   */

    public function checkValidation(){
        $this->form_validation->set_rules('search', 'Search Keyword', 'required|xss_clean');
         if ($this->form_validation->run() == FALSE) {
            return FALSE;
        }
        else {
            return true;
        }
    }

    
  /**
   *  @desc :  This funtion for view popular search keyword
   *  @param : offset  
   *  @return : view popular search
   */

  
  public function viewsearch($offset = 0){
    $offset = ($this->uri->segment(3) != '' ? $this->uri->segment(3): 0);
    $config['total_rows'] = $this->Popularsearch_model->total_countsearchkeyword();
    $config['per_page']= 10;
    $config['first_link'] = 'First';
    $config['last_link'] = 'Last';
    $config['uri_segment'] = 3;
    $config['base_url']= base_url().'employee/popularsearch/viewsearch'; 
    $config['suffix'] = http_build_query($_GET, '', "&"); 
    $this->pagination->initialize($config);
    $this->data['paginglinks'] = $this->pagination->create_links();
        // Showing total rows count 
    if($this->data['paginglinks']!= '') {
      $this->data['pagermessage'] = 'Showing '.((($this->pagination->cur_page-1)*$this->pagination->per_page)+1).' to '.($this->pagination->cur_page*$this->pagination->per_page).' of '.$this->pagination->total_rows;
    }   
    $this->data['result']     = $this->Popularsearch_model->get_popularsearchkeyword($config["per_page"], $offset);
    $employee_id              = $this->session->userdata('employee_id');
    $result['one']            = $this->employee_model->verifylist($employee_id,'0');
    $result['three']          = $this->employee_model->verifylist($employee_id,'2');
    $result['forteen']        = $this->employee_model->verifylist($employee_id,'14');
    $result['service']        = $this->filter_model->getserviceforfilter();
    $result['agent']          = $this->filter_model->getagent(); 
    $this->load->view('employee/header',$result);
    $this->load->view('employee/viewpopularsearch', $this->data);
   }


  /**
   *  @desc :  This funtion for delete search keyword
   *  @param : search keyword id and offset
   *  @return : void
   */


   public function  DeleteSearchkeyword($id,$offset){
    $remove = $this->Popularsearch_model->DeleteSearchkeyword($id);
    $output = "Delete"." $remove "." Successfully";
    $userSession = array('success' =>$output);
    $this->session->set_userdata($userSession);
    redirect(base_url()."popularsearch/viewsearch/$offset");

   }

  /**
   *  @desc :  This funtion for update search keyword andload verifylist
   *  @param : search keyword id 
   *  @return : void
   */

   public function editserachkeyword($id){
     $validation = $this->checkValidation();
        if($validation){
             $data['searchkeyword'] = $this->input->post('search');
             $data['update_date']   = date("Y-m-d h:i:s");
             $insert = $this->Popularsearch_model->UpdatePopularSearchKeyword($id,$data);
             $search = $this->Popularsearch_model->getsearchkeyword($id);
             foreach ($search as  $value) 
             $output['id'] = $value['id'];
             $output['searchkeyword']  = $value['searchkeyword'];
             $output['sucess']         = "Popular Search Keyword Updated";
             $employee_id              = $this->session->userdata('employee_id');
             $result['one']            = $this->employee_model->verifylist($employee_id,'0');
             $result['three']          = $this->employee_model->verifylist($employee_id,'2');
             $result['forteen']        = $this->employee_model->verifylist($employee_id,'14');
             $result['service']        = $this->filter_model->getserviceforfilter();
             $result['agent']          = $this->filter_model->getagent();
             $this->load->view('admin/header',$result);
             $this->load->view('admin/updatepopularsearch',$output);


        } else {
            $search = $this->Popularsearch_model->getsearchkeyword($id);
            foreach ($search as  $value) 
             $data['id'] = $value['id'];
             $data['searchkeyword']    = $value['searchkeyword'];
             $employee_id              = $this->session->userdata('employee_id');
             $result['one']            = $this->employee_model->verifylist($employee_id,'0');
             $result['three']          = $this->employee_model->verifylist($employee_id,'2');
             $result['forteen']        = $this->employee_model->verifylist($employee_id,'14');
             $result['service']        = $this->filter_model->getserviceforfilter();
             $result['agent']          = $this->filter_model->getagent();
             $this->load->view('admin/header',$result);
             $this->load->view('admin/updatepopularsearch',$data);
        }

   }
}
