<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Employees extends CI_Controller {

  /**
   * @desc : load list modal and helpers
   */
      function __Construct(){
        parent::__Construct();
        $this->load->model('employee_model');
        $this->load->model('filter_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->library("pagination");
        if (($this->session->userdata('loggedIn')==TRUE)&&($this->session->userdata('userType')=='admin'))  {
          return TRUE ;
        } else {
          redirect(base_url()."admin");
        }
    }

 /**
   *  @desc : This function create employee and right and call filter
   *  @param :  void
   *  @return : void
   */

  public function index(){
     $validation = $this->checkValidation();
     
     if($_POST){
          if($validation){
              $data = $this->getdata();
              $data['create_date']   = date("Y-m-d h:i:s");
              $insertData  = $this->employee_model->insertData($data);
              $result['service']       = $this->filter_model->getserviceforfilter();
              $result['agent']         = $this->filter_model->getagent(); 
              $data['sucess'] = "Employee created";
              $this->load->view('admin/header',$result);
              $this->load->view('admin/employee',$data);    
            } else {
                   
                    $data = $this->getdata();
                    $result['service']       = $this->filter_model->getserviceforfilter();
                    $result['agent']         = $this->filter_model->getagent(); 
                    $this->load->view('admin/header',$result);
                    $this->load->view('admin/employee',$data);

              }

        } else {
 
        $result['service']       = $this->filter_model->getserviceforfilter();
        $result['agent']         = $this->filter_model->getagent(); 
        $this->load->view('admin/header',$result);
        $this->load->view('admin/employee');

        }
  }



  /**
   *  @desc : This function get right
   *  @param : void
   *  @return : array(data)
   */

  public function getdata(){
    $data['employee_id']                            = $this->input->post('employee_id');
    if(!empty($_POST['employee_password']))
    $data['employee_password']                      = md5($this->input->post('employee_password'));
    $data['right_for_add_handyman']                 = $this->input->post('right_for_add_handyman');   
    $data['right_for_add_service']                  = $this->input->post('right_for_add_service');
    $data['right_for_activate_deactivate_handyman'] = $this->input->post('right_for_activate_deactivate_handyman');
    $data['right_for_activate_deactivate_service']  = $this->input->post('right_for_activate_deactivate_service');
    $data['right_for_xls_for_handyman']             = $this->input->post('right_for_xls_for_handyman');
    $data['right_for_add_employee']                 = $this->input->post('right_for_add_employee');
    $data['right_for_report_messgae']               = $this->input->post('right_for_report_messgae'); 
    $data['right_for_signup_message']               = $this->input->post('right_for_signup_message');
    $data['right_for_review_message']               = $this->input->post('right_for_review_message') ;
    $data['right_for_update_employee']              = $this->input->post('right_for_update_employee');
    $data['right_for_approve_handyman']             = $this->input->post('right_for_approve_handyman');
    $data['right_for_delete']                       = $this->input->post('right_for_delete');
    $data['right_for_verifyhandyman']               = $this->input->post('right_for_verifyhandyman');
    $data['right_for_popularsearch']                = $this->input->post('right_for_popularsearch');
    $data['right_for_review']                       = $this->input->post('right_for_review');

    return $data;

  }

   /**
   *  @desc : This function for check validation
   *  @param : void
   *  @return : tue if validation true otherwise FALSE
   */

    public function checkValidation(){
        $this->form_validation->set_rules('employee_id', 'Employee Id/Name', 'required|xss_clean|is_unique[employee.employee_id]');
        $this->form_validation->set_rules('employee_password', 'Employee Password', 'required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            return FALSE;
        }
        else {
            return true;
        }

    }

   /**
   *  @desc : this function view employee and call filter
   *  @param : offset
   *  @return : view employee
   */ 

    function viewemployee($offset = 0) {
     
      $offset = ($this->uri->segment(3) != '' ? $this->uri->segment(3): 0);
      $config['total_rows'] = $this->employee_model->total_employee();
      $config['per_page'] = 15;
      $config['first_link'] = 'First';
      $config['last_link'] = 'Last';
      $config['uri_segment'] = 3;
      $config['base_url'] = base_url().'/employees/viewemployee';
      $config['suffix'] = http_build_query($_GET, '', "&"); 
      $this->pagination->initialize($config);
      $this->data['paginglinks'] = $this->pagination->create_links();
      if($this->data['paginglinks']!= ''){
       $this->data['pagermessage'] = 'Showing '.((($this->pagination->cur_page-1)*$this->pagination->per_page)+1).' to '.($this->pagination->cur_page*$this->pagination->per_page).' of '.$this->pagination->total_rows;
      }
      $this->data['result'] = $this->employee_model->get_employee($config["per_page"],$offset);
      $result['service']       = $this->filter_model->getserviceforfilter();
      $result['agent']         = $this->filter_model->getagent(); 
      $this->load->view('admin/header',$result);
      $this->load->view('admin/viewemployee',$this->data);


    }

  /**
   *  @desc : this function update employee
   *  @param : id of employee
   *  @return : view employee
   */ 

    function update($id){
     $this->form_validation->set_rules('employee_id', 'Employee Id/Name', 'required|xss_clean');
     $this->form_validation->set_rules('employee_password', 'Employee Password', 'xss_clean');
     $this->form_validation->set_error_delimiters('<div class="error" role="alert">', '</div>');

         if ($this->form_validation->run() == TRUE) {
             
                   $data = $this->getdata();
                   $updatedetail = $this->employee_model->update($id,$data);
                   $data['employee'] = $this->employee_model->getemployeefromid($id); 
                   $result['service']       = $this->filter_model->getserviceforfilter();
                   $result['agent']         = $this->filter_model->getagent(); 
                   $data['sucess'] = "Updated Successfully";
                   $this->load->view('admin/header',$result);
                   $this->load->view('admin/updateemployee',$data);
             

        } else {
          $data['employee'] = $this->employee_model->getemployeefromid($id); 
          $result['service']       = $this->filter_model->getserviceforfilter();
          $result['agent']         = $this->filter_model->getagent(); 
          $this->load->view('admin/header',$result);
          $this->load->view('admin/updateemployee',$data);

        }

    }

 /**
   *  @desc : this function approve list
   *  @param : void
   *  @return : view employee
   */ 

    function approvebyemployeehandyman($date=0){
     $result['service']        = $this->filter_model->getserviceforfilter();
     $result['agent']          = $this->filter_model->getagent(); 
     $result['result']         =  $this->employee_model->last14daysapproved($date);
     $result['one']            = $this->employee_model->last14daysapproved('0');
     $result['three']          = $this->employee_model->last14daysapproved('2');
     $result['forteen']        = $this->employee_model->last14daysapproved('14');
     $this->load->view('admin/header',$result);
     $this->load->view('admin/approvebyemployeehandyman');
    }

 /**
   *  @desc : this function for verify list
   *  @param : day
   *  @return : view employee
   */ 

    function verifylist($date =0){
     $result['service']         = $this->filter_model->getserviceforfilter();
     $result['agent']           = $this->filter_model->getagent(); 
     $result['result']          =  $this->employee_model->last14daysverify($date);
     $results['one']            = $this->employee_model->last14daysverify('0');
     $results['three']          = $this->employee_model->last14daysverify('2');
     $results['forteen']        = $this->employee_model->last14daysverify('14');
     $this->load->view('admin/header',$result);
     $this->load->view('admin/verifylist',$results);

    }
  /**
   *  @desc : this function for delete employee
   *  @param : employee id
   *  @return : void
   */ 


   function delete($id){
    $delete = $this->employee_model->deleteemployee($id);
    $output = " Deleted successfully";
    $userSession = array('success' =>$output);
    $this->session->set_userdata($userSession);
    redirect(base_url()."employees/viewemployee");

   }




// end of controllers
}
