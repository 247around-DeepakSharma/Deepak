<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Service extends CI_Controller {

  /**
   * load list modal and helpers
   */
     function __Construct(){
     parent::__Construct();
     $this->load->model('service_model');
     $this->load->model('filter_model');
     $this->load->model('employee_model');
     $this->load->helper(array('form', 'url'));
     $this->load->library('form_validation');
     $this->load->library("pagination");
     $this->load->library('s3');
     if (($this->session->userdata('loggedIn')==TRUE)&&($this->session->userdata('userType')=='employee'))  {
          return TRUE ;
        } else {
          redirect(base_url()."employee/login");
        }
    }

  
  /**
   *  @desc : This function will load service and Add Services 
   *  @param: void
   *  @return : print Service on Service Page
   */

    public function index(){

    $validation = $this->checkValidation();
    if($validation){
      $service['services']    = $this->input->post('services');
      $service['keywords']    = $this->input->post('keywords');
      $service['distance']    = $this->input->post('distance');
      $service['create_date'] = date("Y-m-d H:i:s");

      if(isset($_POST['service_image']))
      $service['service_image'] = $this->input->post('service_image');
      //if(isset($_POST['image']))
      //$service['image'] = $this->input->post('image');
      $updatedetail = $this->service_model->addservice($service); 
      $output = "Added Successfully";
      $this->loadViews($output);
                
    } else {
      $results['service']       = $this->filter_model->getserviceforfilter();
      $results['agent']         = $this->filter_model->getagent();
      $employee_id              = $this->session->userdata('employee_id');
      $results['one']           = $this->employee_model->verifylist($employee_id,'0');
      $results['three']         = $this->employee_model->verifylist($employee_id,'2');
      $results['forteen']       = $this->employee_model->verifylist($employee_id,'14');
      $this->load->view('employee/header',$results);
      $this->load->view('employee/addservice');
    }

  }
  /**
   *  @desc : This function for check validation
   *  @param : void
   *  @return : tue if validation true otherwise FALSE
   */
   
  public function checkValidation(){
    $this->form_validation->set_rules('services', 'Service', 'required|trim|xss_clean|is_unique[services.services]');
    $this->form_validation->set_rules('distance', 'Distance', 'trim|xss_clean|numeric');
    $this->form_validation->set_rules('service_image', 'Service Image', 'callback_uploadImage');
    // $this->form_validation->set_rules('image','Image','callback_imageupload');
    $this->form_validation->set_error_delimiters('<div class="error" role="alert">', '</div>');
    if ($this->form_validation->run() == FALSE) {
            return FALSE;
        }
        else {
            return true;
        }
  }
  /**
   *  @desc : This function for check image validation and upload image
   *  @param : void
   *  @return : if  is not image  false return false
   */

   public function uploadImage() {
        $allowedExts = array("png", "jpg", "jpeg","JPG","JPEG","bmp","BMP","GIF","PNG");
        $temp        = explode(".", $_FILES["service_image"]["name"]);
        $extension   = end($temp);
        $filename    = prev($temp);
        if($_FILES["service_image"]["name"]!=null) {
            if (($_FILES["service_image"]["size"] < 2e+6)&& in_array($extension, $allowedExts)) {
                if ($_FILES["service_image"]["error"] > 0) {
                    $this->form_validation->set_message('uploadImage', $files["service_image"]["error"]);
                    
                } 
                else {
                    $pic        = md5(uniqid(rand()));
                    $picName = $pic.".".$extension;
                    $_POST['service_image']  = $picName;
                   // move_uploaded_file($_FILES["service_image"]["tmp_name"], "./uploads/" . $picName);
                    $bucket = 'boloaaka-images';
                    $directory = "service-320x252/".$picName;
                    $this->s3->putObjectFile($_FILES["service_image"]["tmp_name"], $bucket ,$directory, S3::ACL_PUBLIC_READ);
                }
            }
            else{
                $this->form_validation->set_message('uploadImage', 'File size or file type is not supported.Allowed extentions are "png", "jpg", "jpeg". Maximum file size is 2MB.');
                return FALSE;
            }
        } 
    }

  /**
   *  @desc : This function for image upload
   *  @param : void
   *  @return : if  is not image  false return false
   */

  /* public function imageupload() {
        $allowedExts = array("png", "jpg", "jpeg","JPG","JPEG","bmp","BMP","GIF","PNG");
        $temp        = explode(".", $_FILES["image"]["name"]);
        $extension   = end($temp);
        $filename    = prev($temp);
        if($_FILES["image"]["name"]!=null) {
            if (($_FILES["image"]["size"] < 2e+6)&& in_array($extension, $allowedExts)) {
                if ($_FILES["image"]["error"] > 0) {
                    $this->form_validation->set_message('imageupload', $files["image"]["error"]);
                    
                } 
                else {
                    $pic        = md5(uniqid(rand()));
                    $picName = $pic.".".$extension;
                    $_POST['image']  = $picName;
                   // move_uploaded_file($_FILES["service_image"]["tmp_name"], "./uploads/" . $picName);
                    $bucket = 'boloaaka-images';
                    $directory = "service-320x252/".$picName;
                    $this->s3->putObjectFile($_FILES["image"]["tmp_name"], $bucket ,$directory, S3::ACL_PUBLIC_READ);
                }
            }
            else{
                $this->form_validation->set_message('imageupload', 'File size or file type is not supported.Allowed extentions are "png", "jpg", "jpeg". Maximum file size is 2MB.');
                return FALSE;
            }
        } 
    }*/

   


  /**
   *  @desc : This function load output Message
   *  @param : output message
   *  @return : Print output and services on service page
   */
   function loadViews($output) {
      $data['sucess'] =$output;
      $results['service']       = $this->filter_model->getserviceforfilter();
      $results['agent']         = $this->filter_model->getagent();
      $employee_id              = $this->session->userdata('employee_id');
      $results['one']           = $this->employee_model->verifylist($employee_id,'0');
      $results['three']         = $this->employee_model->verifylist($employee_id,'2');
      $results['forteen']       = $this->employee_model->verifylist($employee_id,'14');
      $this->load->view('employee/header',$results);
      $this->load->view('employee/addservice',$data);
       
    }

  /**
   *  @desc : this function for get all service for admin
   *  @param : offset and page(no. of data showon page)
   *  @return : print Service on admin/service page
   */ 

    function viewservices($offset = 0,$page = 0) {
      if($page ==0){ $page =10;}
      $offset = ($this->uri->segment(4) != '' ? $this->uri->segment(4): 0);
      $config['total_rows'] = $this->service_model->total_service();
      $config['per_page'] = $page;
      $config['first_link'] = 'First';
      $config['last_link'] = 'Last';
      $config['uri_segment'] = 4;
      $config['base_url'] = base_url().'employee/service/viewservices';
      $config['suffix'] = http_build_query($_GET, '', "&"); 
      $this->pagination->initialize($config);
      $this->data['paginglinks'] = $this->pagination->create_links();
      if($this->data['paginglinks']!= ''){
       $this->data['pagermessage'] = 'Showing '.((($this->pagination->cur_page-1)*$this->pagination->per_page)+1).' to '.($this->pagination->cur_page*$this->pagination->per_page).' of '.$this->pagination->total_rows;
      }
      $this->data['result'] = $this->service_model->get_service($config["per_page"],$offset);
      $results['service']       = $this->filter_model->getserviceforfilter();
      $results['agent']         = $this->filter_model->getagent();
      $employee_id              = $this->session->userdata('employee_id');
      $results['one']           = $this->employee_model->verifylist($employee_id,'0');
      $results['three']         = $this->employee_model->verifylist($employee_id,'2');
      $results['forteen']       = $this->employee_model->verifylist($employee_id,'14');
      $this->load->view('employee/header',$results);
      $this->load->view('employee/services',$this->data);


    }





  /**
   *  @desc : This function update action 0 for service not active
   *  @param : service id, offset, page
   *  @return : Print service 
   */

   function deleteService($serviceid,$offset,$page = 0){
    $check = $this->checksessionactive();
    if($check== TRUE){
    $update['action'] = 0;
    $removeuser = $this->service_model->updateService($serviceid,$update);
    $output = "Deactivate  Successfully";
    $userSession = array('error' =>$output);
    $this->session->set_userdata($userSession);
    redirect(base_url()."employee/service/viewservices/$offset/$page");
   }
   }

  /**
   *  @desc : This function update update service
   *  @param : service id,offset,page
   *  @return : service detail on cervice page
   */

   function updateService($serviceid,$offset,$page =0){

    $this->form_validation->set_rules('services', 'Service', 'required|trim|xss_clean');
    $this->form_validation->set_rules('distance', 'Distance', 'trim|xss_clean|numeric');
    $this->form_validation->set_rules('service_image', 'Service Image', 'callback_uploadImage');
    //$this->form_validation->set_rules('image','Image','callback_imageupload');
    $this->form_validation->set_error_delimiters('<div class="error" role="alert">', '</div>');

         if ($this->form_validation->run() == TRUE) {
         
          
               $service['services']     = $this->input->post('services');
               $service['distance']     = $this->input->post('distance');
               $service['keywords']     = $this->input->post('keywords');
               $service['update_date']  = date("Y-m-d H:i:s");


               if(isset($_POST['service_image']))
               $service['service_image'] = $this->input->post('service_image');
              // if(isset($_POST['image']))
              // $service['image']         = $this->input->post('image');
               $updatedetail             = $this->service_model->updateService($serviceid,$service);
               $data['services']         = $this->service_model->getserviceid($serviceid);
               $data['off']              = $offset;
               $data['sucess']           = "Updated Successfully";
               $data['page']             = $page;
               $results['service']       = $this->filter_model->getserviceforfilter();
               $results['agent']         = $this->filter_model->getagent();
               $employee_id              = $this->session->userdata('employee_id');
               $results['one']           = $this->employee_model->verifylist($employee_id,'0');
               $results['three']         = $this->employee_model->verifylist($employee_id,'2');
               $results['forteen']       = $this->employee_model->verifylist($employee_id,'14');
               $this->load->view('employee/header',$results);
               $this->load->view('employee/EditService',$data);

        

        } else {
          $data['services'] = $this->service_model->getserviceid($serviceid);
          $data['page']             = $page;
          $data['off']              = $offset; 
          $results['service']       = $this->filter_model->getserviceforfilter();
          $results['agent']         = $this->filter_model->getagent();
          $employee_id              = $this->session->userdata('employee_id');
          $results['one']           = $this->employee_model->verifylist($employee_id,'0');
          $results['three']         = $this->employee_model->verifylist($employee_id,'2');
          $results['forteen']       = $this->employee_model->verifylist($employee_id,'14');   
          $this->load->view('employee/header',$results);
          $this->load->view('employee/EditService',$data);

        }
   }


   
  /**
   *  @desc : This function for drag table of services
   *  @param :  old index & new index
   *  @return : void
   */

   function servicedrag(){
    $updateRecordsArray   = $this->input->post('recordsArray');
    $i = 0;
    foreach ($updateRecordsArray as $key => $value)  { 
     $split =  explode("|",$updateRecordsArray[$i]);
     $id[$i]['id'] = $split[0];
     $priority[$i]['priority'] = $split[1];
     $i= $i+1;
    }

  array_multisort($priority, SORT_ASC);
  $result =  $this->service_model->servicedrag($id,$priority);
   }

  /**
   *  @desc : This function update action 1 for service  active
   *  @param : service id,offfset,page(no. of data show on page)
   *  @return : Print service 
   */

   function ActivateService($serviceid,$offset,$page = 0){
    $check = $this->checksessionactive();
    if($check== TRUE){
    $update['action'] = 1;
    $removeuser = $this->service_model->updateService($serviceid,$update);
    $output = "Activate  Successfully";
    $userSession = array('success' =>$output);
    $this->session->set_userdata($userSession);
    redirect(base_url()."employee/service/viewservices/$offset/$page");
     }
   }
   
  /**
   *  @desc : This function for check validation active or deactive
   *  @param : void
   *  @return : tue if validation true otherwise FALSE
   */

    public function checksessionactive(){
      if (($this->session->userdata('loggedIn')==TRUE)&&($this->session->userdata('userType')=='employee')&&($this->session->userdata('activate/deactivate_service')=='1'))  {
          return TRUE ;
        } else {
          echo "<script>alert('Sory You are not Authorised')</script>";
          echo"<script>window.history.back()</script>";
        }
    }


//end of controllers    
}
