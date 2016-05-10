<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Employee extends CI_Controller {

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
        if (($this->session->userdata('loggedIn')==TRUE)&&($this->session->userdata('userType')=='employee'))  {
          return TRUE ;
        } else {
          redirect(base_url()."employee/login");
        }
    }

 /**
   *  @desc : This function create employee and right and load filter
   *  @param : void
   *  @return : void
   */

  public function index(){
     $validation = $this->checkValidation();
     
     if($_POST){
          if($validation){
              $data = $this->getdata();
              
              $result['service']       = $this->filter_model->getserviceforfilter();
              $result['agent']         = $this->filter_model->getagent();
              $data['create_date']   = date("Y-m-d h:i:s");
              $insertData  = $this->employee_model->insertData($data);
              $data['sucess'] = "Employee created";
              $employee_id              = $this->session->userdata('employee_id');
              $result['one']            = $this->employee_model->verifylist($employee_id,'0');
              $result['three']          = $this->employee_model->verifylist($employee_id,'2');
              $result['forteen']        = $this->employee_model->verifylist($employee_id,'14');
              $this->load->view('employee/header',$result);
              $this->load->view('employee/employee',$data);    
            } else {
                   
                    $data = $this->getdata();
                    $employee_id              = $this->session->userdata('employee_id');
                    $result['one']            = $this->employee_model->verifylist($employee_id,'0');
                    $result['three']          = $this->employee_model->verifylist($employee_id,'2');
                    $result['forteen']        = $this->employee_model->verifylist($employee_id,'14');
                    $result['service']        = $this->filter_model->getserviceforfilter();
                    $result['agent']          = $this->filter_model->getagent();
                    $this->load->view('employee/header',$result);
                    $this->load->view('employee/employee',$data);

              }

        } else {
 
       
       
        $result['service']        = $this->filter_model->getserviceforfilter();
        $result['agent']          = $this->filter_model->getagent();
        $employee_id              = $this->session->userdata('employee_id');
        $result['one']            = $this->employee_model->verifylist($employee_id,'0');
        $result['three']          = $this->employee_model->verifylist($employee_id,'2');
        $result['forteen']        = $this->employee_model->verifylist($employee_id,'14');
        $this->load->view('employee/header',$result);
        $this->load->view('employee/employee');

        }
  }



  /**
   *  @desc : This function getemployee right
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
   *  @desc : this function view employee and load filter and load verify list
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
      $config['base_url'] = base_url().'/employee/employee/viewemployee';
      $config['suffix'] = http_build_query($_GET, '', "&"); 
      $this->pagination->initialize($config);
      $this->data['paginglinks'] = $this->pagination->create_links();
      if($this->data['paginglinks']!= ''){
       $this->data['pagermessage'] = 'Showing '.((($this->pagination->cur_page-1)*$this->pagination->per_page)+1).' to '.($this->pagination->cur_page*$this->pagination->per_page).' of '.$this->pagination->total_rows;
      }
      $this->data['result']    = $this->employee_model->get_employee($config["per_page"],$offset);
      $result['service']       = $this->filter_model->getserviceforfilter();
      $result['agent']         = $this->filter_model->getagent();
      $employee_id              = $this->session->userdata('employee_id');
      $result['one']            = $this->employee_model->verifylist($employee_id,'0');
      $result['three']          = $this->employee_model->verifylist($employee_id,'2');
      $result['forteen']        = $this->employee_model->verifylist($employee_id,'14');
      $this->load->view('employee/header',$result);
      $this->load->view('employee/viewemployee',$this->data);


    }

   /**
   *  @desc : this function for update  employee right and loas filter and verify list
   *  @param : employe id
   *  @return : void
   */ 

    function update($id){
     $check = $this->checksessionupdate();
     if($check == TRUE){
     $this->form_validation->set_rules('employee_id', 'Employee Id/Name', 'required|xss_clean');
     $this->form_validation->set_rules('employee_password', 'Employee Password', 'xss_clean');
     $this->form_validation->set_error_delimiters('<div class="error" role="alert">', '</div>');

         if ($this->form_validation->run() == TRUE) {
        
               $data = $this->getdata();
               $updatedetail = $this->employee_model->update($id,$data);
               $data['employee'] = $this->employee_model->getemployeefromid($id); 
               $data['sucess'] = "Updated Successfully";
               $result['service']       = $this->filter_model->getserviceforfilter();
               $result['agent']         = $this->filter_model->getagent();
               $employee_id              = $this->session->userdata('employee_id');
               $result['one']            = $this->employee_model->verifylist($employee_id,'0');
               $result['three']          = $this->employee_model->verifylist($employee_id,'2');
               $result['forteen']        = $this->employee_model->verifylist($employee_id,'14');
               $this->load->view('employee/header',$result);
               $this->load->view('employee/updateemployee',$data);

        

        } else {
          $data['employee'] = $this->employee_model->getemployeefromid($id); 
          $result['service']       = $this->filter_model->getserviceforfilter();
          $result['agent']         = $this->filter_model->getagent();
          $employee_id              = $this->session->userdata('employee_id');
          $result['one']            = $this->employee_model->verifylist($employee_id,'0');
          $result['three']          = $this->employee_model->verifylist($employee_id,'2');
          $result['forteen']        = $this->employee_model->verifylist($employee_id,'14');
          $this->load->view('employee/header',$result);
          $this->load->view('employee/updateemployee',$data);

        }
      }

    }
 /**
   *  @desc : This function for check validation active or deactive
   *  @param : void
   *  @return : tue if validation true otherwise FALSE
   */

    function checksessionupdate(){
      if (($this->session->userdata('loggedIn')==TRUE)&&($this->session->userdata('userType')=='employee')&&($this->session->userdata('update_employee')==1))  {
          return TRUE ;
        } else {
         echo "<script>alert('Sory You are not Authorised')</script>";
         echo"<script>window.history.back()</script>";
        }
    

    }
  /**
   *  @desc : This function for show number of handyman approve by employee  and load approe handymanlist
   *  @param : day
   *  @return : void
   */


    function approvedhandymanlist($date = 0){
    $employee_id              = $this->session->userdata('employee_id');
    $result['result']         = $this->employee_model->approvehandymanlist($employee_id,$date);
    $result['one']            = $this->employee_model->approvehandymanlist($employee_id,'0');
    $result['three']          = $this->employee_model->approvehandymanlist($employee_id,'2');
    $result['forteen']        = $this->employee_model->approvehandymanlist($employee_id,'14');
    $results['service']       = $this->filter_model->getserviceforfilter();
    $results['agent']         = $this->filter_model->getagent();
  
    $results['one']            = $this->employee_model->verifylist($employee_id,'0');
    $results['three']         = $this->employee_model->verifylist($employee_id,'2');
    $results['forteen']       = $this->employee_model->verifylist($employee_id,'14');
    $this->load->view('employee/header',$results);
    $this->load->view('employee/approvedlist',$result);
   }

  /**
   *  @desc : This function for show number of handyman verify by employee  and verifylist
   *  @param : day
   *  @return : void
   */

   function verifylist($date=0){
    $employee_id              = $this->session->userdata('employee_id');
    $result['result']         = $this->employee_model->verifylist($employee_id,$date);
    $result['one']            = $this->employee_model->verifylist($employee_id,'0');
    $result['three']          = $this->employee_model->verifylist($employee_id,'2');
    $result['forteen']        = $this->employee_model->verifylist($employee_id,'14');
    $result['service']       = $this->filter_model->getserviceforfilter();
    $result['agent']         = $this->filter_model->getagent();
    $this->load->view('employee/header',$result);
    $this->load->view('employee/verifylist',$result);
   


   }

  /**
   *  @desc : This funtion get handyman handyman input resquested
   *  @param : input for filter
   *  @return : filter
   */

    function getfilterdata(){

    if(isset($_POST['service']))
     $data['service_id']              = $this->input->post('service');
     if(isset($_POST['experience']))
     $data['experience']              = $this->input->post('experience');
     if(isset($_POST['Rating_by_Agent']))
     $data['Rating_by_Agent']         = $this->input->post('Rating_by_Agent');
   if(isset($_POST['Agent']))
     $data['Agent']                   = $this->input->post('Agent');
    if(isset($_POST['address']))
     $data['address']                 = $this->input->post('address');
    if(isset($_POST['service_on_call']))
     $data['service_on_call']         = $this->input->post('service_on_call');
    if(isset($_POST['work_on_weekdays']))
     $data['work_on_weekdays']        = $this->input->post('work_on_weekdays');
    if(isset($_POST['works_on_weekends']))
     $data['works_on_weekends']       = $this->input->post('works_on_weekends');
    if(!empty($data))
    return $data;
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
    redirect(base_url()."employee/employee/viewemployee");

   }



// end of controllers
}
