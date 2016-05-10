<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Handyman extends CI_Controller {

  /**
   * @desc : load list modal and helpers
   */
      function __Construct(){
        parent::__Construct();
       
        $this->load->model('handyman_model'); 
        $this->load->model('filter_model');
        $this->load->model('employee_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library("pagination");
        $this->load->library('session');
        $this->load->library('s3');
        if (($this->session->userdata('loggedIn')==TRUE)&&($this->session->userdata('userType')=='employee'))  {
          return TRUE ;
        } else {
          redirect(base_url()."employee/login");
        }
    }

  /**
   *  @desc : This function will insert handyman data and load filter and verifylist
   *  @param :void
   *  @return : void
   */
    public function index(){
       $validation = $this->checkValidation();
     
     if($_POST){
          if($validation){
              $data = $this->getdata();
              $data['current_time']   = date("Y-m-d h:i:s");
              $insertData  = $this->handyman_model->insertData($data);
              $this->loadView($insertData);
               
            } else {
                    $GetAllServices = $this->getService();
                    $GetAllServicesInfo = $this->getdata();
                    $GetAllServicesInfo['GetAllServicesInfo'] =  $GetAllServices;
                    $results['service']        = $this->filter_model->getserviceforfilter();
                    $results['agent']          = $this->filter_model->getagent();
                    $employee_id               = $this->session->userdata('employee_id');
                    $results['one']            = $this->employee_model->verifylist($employee_id,'0');
                    $results['three']          = $this->employee_model->verifylist($employee_id,'2');
                    $results['forteen']        = $this->employee_model->verifylist($employee_id,'14');
                    $this->load->view('employee/header',$results);
                    $this->load->view('employee/Addhandyman',$GetAllServicesInfo);

              }

        } else {
 
        $GetAllServices = $this->getService();
        $GetAllServicesInfo['GetAllServicesInfo'] =  $GetAllServices;
        $results['service']        = $this->filter_model->getserviceforfilter();
        $results['agent']          = $this->filter_model->getagent();
        $employee_id               = $this->session->userdata('employee_id');
        $results['one']            = $this->employee_model->verifylist($employee_id,'0');
        $results['three']          = $this->employee_model->verifylist($employee_id,'2');
        $results['forteen']        = $this->employee_model->verifylist($employee_id,'14');
        $this->load->view('employee/header',$results);
        $this->load->view('employee/Addhandyman',$GetAllServicesInfo);

        }


    }

  /**
   *  @desc : This function to get form data for adding handyman
   *  @param : void
   *  @return : array(data)
   */


    public function getdata(){

              $data['name']             = $this->input->post('name');
              $data['phone']            = $this->input->post('phone');
              $data['service_id']       = $this->input->post('service_id');
              $data['address']          = $this->input->post('address');
              $data['experience']       = $this->input->post('experience');
              $data['age']              = $this->input->post('age');
              $data['is_paid']          = $this->input->post('paid');
              $data['passport']         = $this->input->post('passport');
              $data['identity']         = $this->input->post('identity');
              $data['marital_status']   = $this->input->post('married');
              $data['work_on_weekdays'] = $this->input->post('work_on_weekdays');
              $data['works_on_weekends']= $this->input->post('weekends');
              $data['service_on_call']  = $this->input->post('service_on_call');   
              $data['bank_ac_no']       = $this->input->post('bank_account_no');
              $data['is_disabled']      = $this->input->post('is_disabled');
              $data['vendors_area_of_operation']   = $this->input->post('vendors_area_of_operation');
              $data['Rating_by_Agent']             = $this->input->post('rating_by_agent');
              $location                            = $this->input->post('location');
              if($location){
              $loc                                 = explode("|",$location);
              $loc                                 = array("lattitude" => $loc[0], "longitude" => $loc[1]);
              $data['location']                    = json_encode($loc);
              }
              $data['police_verification']         = $this->input->post('police_verification');
              if(!empty($data['marital_status'])){ $data['marital_status'] = "Married";} else { $data['marital_status'] = "Single" ;}
              if(empty($data['bank_ac_no'])){ $data['bank_account']  = 'No'; } else { $data['bank_account']  = 'Yes'; }
              if(empty($data['service_on_call'])){ $data['service_on_call']  = 'No'; } else { $data['service_on_call']  = 'Yes'; }
              if(empty($data['work_on_weekdays'])){ $data['work_on_weekdays']  = 'No'; } else { $data['work_on_weekdays']  = 'Yes'; }
              if(empty($data['works_on_weekends'])){ $data['works_on_weekends']  = 'No'; } else { $data['works_on_weekends']  = 'Yes'; }
              if(empty($data['passport'])){ $data['passport']  = 'No'; } else { $data['passport']  = 'Yes'; }
              if(empty($data['identity'])){ $data['identity']  = 'No'; } else { $data['identity']  = 'Yes'; }
              if(empty($data['is_disabled'])){ $data['is_disabled']  = 'No'; } else { $data['is_disabled']  = 'Yes'; }
              if(empty($data['police_verification'])){ $data['police_verification']  = 'No'; } else { $data['police_verification']  = 'Yes'; }
              return $data;

    }


  /**
   *  @desc : This function for check validation
   *  @param : void
   *  @return : tue if validation true otherwise FALSE
   */

    public function checkValidation(){
        $this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
        $this->form_validation->set_rules('phone', 'Mobile Number', 'trim|exact_length[10]|required|xss_clean');
        $this->form_validation->set_rules('experience', 'Experience', 'trim|required|xss_clean');
        $this->form_validation->set_rules('age', 'Age', 'trim|numeric|max_length[2]|xss_clean');
        $this->form_validation->set_rules('location', 'Location', 'required');
        $this->form_validation->set_rules('address', 'address', 'required');
        $this->form_validation->set_rules('service_id', 'Service', 'required');
      
        if ($this->form_validation->run() == FALSE) {
            return FALSE;
        }
        else {
            return true;
        }

    }

  /**
   *  @desc : This function load  Success Message
   *  @param : output/success Message
   *  @return : redirect handyman update
   */

   function loadView($id) {
        $handyman_id = $id;
        $output      = "Add successfully . Can Insert Other field";
        $userSession = array('success' =>$output);
        $this->session->set_userdata($userSession);
        redirect(base_url()."employee/handyman/update/$handyman_id/0?tab=upload");
    }

  /**
   *  @desc : This function get service
   *  @param : void
   *  @return : array(service)
   */


    function getService(){
      $GetAllServices   = $this->handyman_model->GetAllServices();
      return $GetAllServices;
    }

   /**  @desc : get handyman information
    *   param : handyman id,offset
    *  @return : handyman details on update handyman page
    */

    function update($handyman_id,$offset){
     
     $gethandyman = $this->handyman_model->gethandyman($handyman_id);
     if($gethandyman) {
      $employee_id               = $this->session->userdata('employee_id');
      $data['off'] = $offset;
      $data['handyman_id'] = $gethandyman;
      $GetAllServices = $this->getService();
      $data['GetAllServicesInfo'] =  $GetAllServices;
      $results['service']       = $this->filter_model->getserviceforfilter();
      $results['agent']         = $this->filter_model->getagent();
      $results['one']            = $this->employee_model->verifylist($employee_id,'0');
      $results['three']          = $this->employee_model->verifylist($employee_id,'2');
      $results['forteen']        = $this->employee_model->verifylist($employee_id,'14');
      $this->load->view('employee/header',$results);
      $this->load->view('employee/updateHandyman',$data);
      } else {
       echo  "This Id doesn't Available";
       }
     

    }


  /**
   * @desc : This funtion get handyman id and upload handyman photo
   * @param : handyman id,offset
   * @return : update handyman profile
   */

    function uploadphoto($handyman_id,$offset){
             
             $check            = $this->CheckImageValidation($_FILES["file"]["type"]);
            if($check ==TRUE){
                $upload  = $this->uploadProfile($_FILES['file']['tmp_name'],$_FILES["file"]["name"]);
                $profile_photo  = array('profile_photo'=>$upload);
                $Updationimage  = $this->handyman_model->UpdateHandyman($handyman_id,$profile_photo);
                $output         = "Photo Uploaded";
                $userSession = array('success' =>$output);
                $this->session->set_userdata($userSession);
                redirect(base_url()."employee/handyman/update/$handyman_id/$offset?tab=upload");

          }  else {
              $output = "File size or file type is not supported.Allowed extentions are  png, jpg, jpeg, JPG, JPEG, PNG, GIF.";
              $userSession = array('error' =>$output);
              $this->session->set_userdata($userSession);
              redirect(base_url()."employee/handyman/update/$handyman_id/$offset?tab=upload");
             
          
          }

    }

    /**
   * @desc : This funtion is used to check image validation
   * @param : file type
   *  @return : TRUE
   */

    function CheckImageValidation($files){
        $validextensions  = array("png", "jpg", "jpeg","JPG","JPEG","PNG","GIF");
        $temporary        = explode(".", $_FILES["file"]["name"]);
        $file_extension   = end($temporary);
        if ((($files == "image/png") || ($files == "image/jpg") || ($files == "image/jpeg") || ($files == "image/JPG") || ($files == "image/JPEG") || ($files == "image/PNG") || ($files == "image/GIF")
        ) && ($files < 100000)//Approx. 100kb files can be uploaded.
        && in_array($file_extension, $validextensions)) {
            return TRUE;
          } 

    }

  /**
   * @desc : This funtion for upload image
   * @param : temporary name and name of image
   * @return : name for insert profile pic
   */

    function uploadProfile($tmp,$name){
      $temp        = explode(".", $_FILES["file"]["name"]);
      $extension   = end($temp);
      $pic        = md5(uniqid(rand()));
      $sourcePath = $tmp;
      $targetPath = $pic.".".$extension;
     // move_uploaded_file($sourcePath,$targetPath);
      $bucket = 'boloaaka-images';
      $directory = "vendor-320x252/".$targetPath;
      $this->s3->putObjectFile($sourcePath, $bucket ,$directory , S3::ACL_PUBLIC_READ);
      return $targetPath;

    }

   


   
  /**
   *  @desc : This function for check validation
   *  @param : void
   *  @return : tue if validation true otherwise FALSE
   */

    public function checkValidate(){
        //$this->form_validation->set_rules('id_proof_name', 'Identity Proof Name', 'required|xss_clean');
       // $this->form_validation->set_rules('id_proof_no', 'Id Proof No.', 'required|xss_clean');
        $this->form_validation->set_rules('file', 'file ', 'callback_uploadImage');
     
        if ($this->form_validation->run() == FALSE) {
            return FALSE;
        }
        else {
            return true;
        }

    }

  /**
   * @desc : This funtion get handyman id and update identity section
   * @param : handyman id,offset
   * @return : update handyman profile
   */
    function identity($handyman_id,$offset){
      $validation = $this->checkValidate();
      if($validation){
        if(isset($_POST['file']))
        $data['id_proof_photo']   = $this->input->post('file');
        $data['id_proof_name']    = $this->input->post('id_proof_name');
        $data['id_proof_no']      = $this->input->post('id_proof_no');
        $Updationimage  = $this->handyman_model->UpdateHandyman($handyman_id,$data);
        $output = "Updated successfully";
        $userSession = array('success' =>$output);
        $this->session->set_userdata($userSession);
        redirect(base_url()."employee/handyman/update/$handyman_id/$offset?tab=identity");
      } else{
           $output = "File size or file type is not supported.Allowed extentions are  png, jpg, jpeg, JPG, JPEG, PNG, GIF. Maximum file size is 2MB";
           $userSession = array('error' =>$output);
           $this->session->set_userdata($userSession);
           redirect(base_url()."employee/handyman/update/$handyman_id/$offset?tab=identity");
      }
    }

  /**
   *  @desc : This function for check image validation and upload image
   *  @param : void
   *  @return : if  is not image  false return false
   */

   public function uploadImage() {
        $allowedExts = array("png", "jpg", "jpeg","JPG","JPEG","PNG","GIF");
        $temp        = explode(".", $_FILES["file"]["name"]);
        $extension   = end($temp);
        $filename    = prev($temp);
        if($_FILES["file"]["name"]!=null) {
            if (($_FILES["file"]["size"] < 2e+6)&& in_array($extension, $allowedExts)) {
                if ($_FILES["file"]["error"] > 0) {
                    $this->form_validation->set_message('uploadImage', $files["file"]["error"]);
                    
                } 
                else {
                    $pic        = md5(uniqid(rand()));
                    $picName = $pic.".".$extension;
                    $_POST['file']  = $picName;
                   // move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/" . $picName);
                     $bucket = "boloaaka-images";
                     $directory = "identity-proof-image/".$picName;
                     $this->s3->putObjectFile($_FILES["file"]["tmp_name"], $bucket ,$directory, S3::ACL_PUBLIC_READ);
                }
            }
            else{
                $this->form_validation->set_message('uploadImage', 'File size or file type is not supported.Allowed extentions are "png", "jpg", "jpeg". Maximum file size is 2MB.');
                return FALSE;
            }
        } 
    }


  /**
   * @desc : This funtion get handyman id and update handyman profile 
   * @param : handyman id,offset
   * @return : update handyman profile
   */

   function updateHandyman($handyman_id,$offset){
    
            $validation = $this->checkValidation();
           if($validation){
               
                    $data = $this->getdata();
                    $data['updatedate']    = date("Y-m-d h:i:s");
                    $insertData  = $this->handyman_model->updatehandyman($handyman_id,$data);
                    $output = " Profile Updated successFully";
                    $userSession = array('success' =>$output);
                    $this->session->set_userdata($userSession);
                    redirect(base_url()."employee/handyman/update/$handyman_id/$offset?tab=home");
                  } else {
                    $gethandyman = $this->handyman_model->gethandyman($handyman_id);
   
                    $data['handyman_id'] = $gethandyman;
                    $GetAllServices = $this->getService();
                    $data['off'] = $offset;
                    $data['GetAllServicesInfo'] =  $GetAllServices;
                    $results['service']         = $this->filter_model->getserviceforfilter();
                    $results['agent']           = $this->filter_model->getagent();
                    $employee_id                = $this->session->userdata('employee_id');
                    $results['one']             = $this->employee_model->verifylist($employee_id,'0');
                    $results['three']           = $this->employee_model->verifylist($employee_id,'2');
                    $results['forteen']         = $this->employee_model->verifylist($employee_id,'14');
                    $this->load->view('employee/header',$results);
                    $this->load->view('employee/updateHandyman',$data);
                  }
              
    }

  /**
   * @desc : This funtion get handyman id and upload update agent section
   * @param : handyman id,offset
   * @return : update handyman profile
   */

    function agent($handyman_id,$offset){
        $data['Agent']                         = $this->input->post('agent_name');
        $data['date_of_collection']            = $this->input->post('date_of_collection');
        $data['time_of_data_collection']       = $this->input->post('time_of_data_collection');
        $data['handyman_previous_customers']   = $this->input->post('handyman_previous_customers');
        $data['other_handyman_contact']        = $this->input->post('other_handyman_contact');
        
        $updatedetail = $this->handyman_model->UpdateHandyman($handyman_id,$data); 
        $output = "successfully updated";
        $userSession = array('success' =>$output);
        $this->session->set_userdata($userSession);
        redirect(base_url()."employee/handyman/update/$handyman_id/$offset?tab=agent");

    }

  /**
   *  @desc :  This funtion get all information about handyman 
   *  @param : offset 
   *  @return : Print all information about handyman on handyman page
   */

  
  public function viewhandyman($offset = 0,$page = 0){
    if($page ==0){ $page =10;}
    $offset = ($this->uri->segment(4) != '' ? $this->uri->segment(4): 0);
    $config['total_rows'] = $this->handyman_model->total_count();
    $config['per_page']= $page;
    $config['first_link'] = 'First';
    $config['last_link'] = 'Last';
    $config['uri_segment'] = 4;
    $config['base_url']= base_url().'employee/handyman/viewhandyman'; 
    $config['suffix'] = http_build_query($_GET, '', "&"); 
    $this->pagination->initialize($config);
    $this->data['paginglinks'] = $this->pagination->create_links();
        // Showing total rows count 
    if($this->data['paginglinks']!= '') {
      $this->data['pagermessage'] = 'Showing '.((($this->pagination->cur_page-1)*$this->pagination->per_page)+1).' to '.($this->pagination->cur_page*$this->pagination->per_page).' of '.$this->pagination->total_rows;
    }   
    $this->data['result'] = $this->handyman_model->get_handyman($config["per_page"], $offset);  
    $results['service']        = $this->filter_model->getserviceforfilter();
    $results['agent']          = $this->filter_model->getagent();
    $employee_id               = $this->session->userdata('employee_id');
    $results['one']            = $this->employee_model->verifylist($employee_id,'0');
    $results['three']          = $this->employee_model->verifylist($employee_id,'2');
    $results['forteen']        = $this->employee_model->verifylist($employee_id,'14');
    $this->load->view('employee/header',$results);
    $this->load->view('employee/handyman', $this->data);
   
   }

  /**
   *  @desc :  This funtion  to make active handyman
   *  @param :handyman id and offset
   *  @return : void
   */

   function activatehandyman($id,$offset=0){
    $check = $this->checksessionactive();
    if($check== TRUE){
    $updateAction = array('action' =>'1' );
    $removeuser = $this->handyman_model->UpdateHandyman($id,$updateAction);
    $output = $removeuser." Activate successfully";
    $userSession = array('success' =>$output);
    $this->session->set_userdata($userSession);
    
    }
   }

  /**
   *  @desc : This funtion get handyman id for inactive handyman
   *  @param : handyman id (to be deleted),offset
   *  @return : hide handyman 
   */

   function deactivate($id,$offset=0){
    $check = $this->checksessionactive();
    if($check== TRUE){
    $updateAction = array('action' =>'0' );
    $removeuser = $this->handyman_model->UpdateHandyman($id,$updateAction);
    $output = $removeuser." deactivate successfully";
    $userSession = array('error' =>$output);
    $this->session->set_userdata($userSession);
    
    }
    }

  /**
   *  @desc : This function for check validation active or deactive
   *  @param : void
   *  @return : tue if validation true otherwise FALSE
   */

    public function checksessionactive(){
      if (($this->session->userdata('loggedIn')==TRUE)&&($this->session->userdata('userType')=='employee')&&($this->session->userdata('activate/deactivate')==1))  {
          return TRUE ;
        } else {
           echo "Sory You are not Authorised";
        
          //echo"<script>window.history.back()</script>";
         // print_r("<script>alert('Sory You are not Authorised')</script>");
        }
    }

 /**
   *  @desc :  This funtion for get new handyman (in active handyman)
   *  @param :offset
   *  @return : view handyman
   */

  function verifiedhandyman($offset = 0,$page=0){
     if($page ==0){ $page =10;}
    $offset = ($this->uri->segment(4) != '' ? $this->uri->segment(4): 0);
    $config['total_rows'] = $this->handyman_model->total_count_inactive();
    $config['per_page']= $page;
    $config['first_link'] = 'First';
    $config['last_link'] = 'Last';
    $config['uri_segment'] = 4;
    $config['base_url']= base_url().'employee/handyman/verifiedhandyman'; 
    $config['suffix'] = http_build_query($_GET, '', "&"); 
    $this->pagination->initialize($config);
    $this->data['paginglinks'] = $this->pagination->create_links();
        
    if($this->data['paginglinks']!= '') {
      $this->data['pagermessage'] = 'Showing '.((($this->pagination->cur_page-1)*$this->pagination->per_page)+1).' to '.($this->pagination->cur_page*$this->pagination->per_page).' of '.$this->pagination->total_rows;
    }   
    $this->data['result']      = $this->handyman_model->get_handyman_approve($config["per_page"], $offset);
    $results['service']        = $this->filter_model->getserviceforfilter();
    $results['agent']          = $this->filter_model->getagent();
    $employee_id               = $this->session->userdata('employee_id');
    $results['one']            = $this->employee_model->verifylist($employee_id,'0');
    $results['three']          = $this->employee_model->verifylist($employee_id,'2');
    $results['forteen']        = $this->employee_model->verifylist($employee_id,'14');  
    $this->load->view('employee/header',$results);
    $this->load->view('employee/newhandyman', $this->data);

  }

  /**
   *  @desc :  This funtion for to make approve  handyman
   *  @param : handyman id and offset
   *  @return : view handyman
   */

   function approve($id,$offset=0,$page=0){
    $check = $this->checkapprovesession();
    if($check ==TRUE){
    $date = date("Y-m-d h:i:s");
    $employee_id = $this->session->userdata('employee_id');
    $updateAction = array('approved' =>'1','action' =>'1','approve_by' =>$employee_id,'approve_date'=>$date );
    $removeuser = $this->handyman_model->UpdateHandyman($id,$updateAction);
    $output = $removeuser." Approve successfully";
    $userSession = array('success' =>$output);
    $this->session->set_userdata($userSession);
    redirect(base_url()."employee/handyman/verifiedhandyman/$offset/$page") ;
  }
  }

  /**
   *  @desc :  This funtion check session approve handyman
   *  @param : void
   *  @return : last history
   */
 

   function checkapprovesession(){
     if (($this->session->userdata('loggedIn')==TRUE)&&($this->session->userdata('userType')=='employee')&&($this->session->userdata('approvehandyman')==1))  {
          return TRUE ;
        } else {
         // redirect(base_url()."employee/login");
            //echo "Sory You are not Authorised";
          echo"<script>alert('Sory You are not Authorised')</script>";
        }

   }


  /**
   *  @desc :  This funtion check session approve handyman
   *  @param :void
   *  @return : last history
   */
 

   function checkverify(){
     if (($this->session->userdata('loggedIn')==TRUE)&&($this->session->userdata('userType')=='employee')&&($this->session->userdata('verify')==1))  {
          return TRUE ;
        } else {
         // redirect(base_url()."employee/login");
           echo"<script>alert('Sory You are not Authorised')</script>";
          //echo"<script>window.history.back()</script>";
        }

   }

  /**
   *  @desc :  This funtion for get unverified handyman and load verifylist
   *  @param :void
   *  @return : array(handyman)
   */

   function unverifiedhandyman(){
    $result['result']          = $this->handyman_model->unverifiedhandyman(); 
    $results['service']        = $this->filter_model->getserviceforfilter();
    $results['agent']          = $this->filter_model->getagent();
    $employee_id               = $this->session->userdata('employee_id'); 
    $results['one']            = $this->employee_model->verifylist($employee_id,'0');
    $results['three']          = $this->employee_model->verifylist($employee_id,'2');
    $results['forteen']        = $this->employee_model->verifylist($employee_id,'14');
    $this->load->view('employee/header',$results);
    $this->load->view('employee/handyman', $result);
   }

  /**
   *  @desc :  This funtion for verify handyman
   *  @param :handyman id
   *  @return : void
   */

   function verify($id){
     $check = $this->checkverify();
    if($check ==TRUE){
      $date = date("Y-m-d h:i:s");
      $employee_id = $this->session->userdata('employee_id');
      $updateAction = array('approved' =>'0','action' =>'0','verify_by' =>$employee_id,'verify_date'=>$date, 'verified' =>'1' );
      $removeuser = $this->handyman_model->UpdateHandyman($id,$updateAction);
      $output = $removeuser." Verify successfully";
      $userSession = array('success' =>$output);
      $this->session->set_userdata($userSession);
      redirect(base_url()."employee/handyman/unverifiedhandyman") ;

    }
   }

  /**
   *  @desc :  This funtion delete verified handyman
   *  @param : handyman id,offset
   *  @return : void
   */

   function newdelete($id,$offset =0){
    $check = $this->checkdeletes();
    if($check ==TRUE){
    $this->handyman_model->delete($id);
    $output = " Delete successfully";
    $userSession = array('error' =>$output);
    $this->session->set_userdata($userSession);
    redirect(base_url()."employee/handyman/verifiedhandyman/$offset") ;
   // echo"<script>window.history.back()</script>";
 }
   }

  /**
   *  @desc :  This funtion check delet authority
   *  @param : void
   *  @return : true if validation true otherwise not authorised message
   */


   function  checkdeletes(){
    if (($this->session->userdata('loggedIn')==TRUE)&&($this->session->userdata('userType')=='employee')&&($this->session->userdata('deletehandyman')==1))  {
          return TRUE ;
        } else {
         // redirect(base_url()."employee/login");
           echo"<script>alert('Sory You are not Authorised')</script>";
          //echo"<script>window.history.back()</script>";
        }

   }

  /**
   *  @desc :  This funtion delete unverified handyman
   *  @param :handyman id,offst
   *  @return : void
   */


    function unverifieddelete($id,$offset =0){
    $check = $this->checkdeletes();
    if($check ==TRUE){
    $this->handyman_model->delete($id);
    $output = " Delete successfully";
    $userSession = array('error' =>$output);
    $this->session->set_userdata($userSession);
    redirect(base_url()."employee/handyman/unverifiedhandyman") ;
   }

   }

  /**
   *  @desc :  This funtion for delete handyman
   *  @param :handymanid and offset
   *  @return : void
   */

   function delete($id,$offset =0){
    $check = $this->checkdelete();
    if($check ==TRUE){
    $this->handyman_model->delete($id);
    $output = " Deleted successfully";
    $userSession = array('error' =>$output);
    $this->session->set_userdata($userSession);
    
     }
   }

  /**
   *  @desc :  This funtion check session delete handyman
   *  @param : void
   *  @return : last history
   */

   function  checkdelete(){
    if (($this->session->userdata('loggedIn')==TRUE)&&($this->session->userdata('userType')=='employee')&&($this->session->userdata('deletehandyman')==1))  {
          return TRUE ;
        } else {
         // redirect(base_url()."employee/login");
          echo "Sory You are not Authorised";
          //echo"<script>window.history.back()</script>";
        }

   }

  /**
   *  @desc :  This funtion for show all view handyman
   *  @param: void
   *  @return :void
   */

function Allview(){
      $result['service']       = $this->filter_model->getserviceforfilter();
      $result['agent']         = $this->filter_model->getagent(); 
      $handyman['result']      =  $this->filter_model->filter($data='');
      $this->load->view('employee/header',$result);
      $this->load->view('employee/handyman',$handyman);
    }

  /**
   *  @desc :  This funtion for insert pricing
   *  @param: handyman  id
   *  @return :void
   */


    function pricing($handyman_id,$offset =0){
      $service                = $this->input->post('service');
      $price                  = $this->input->post('price');
      if(!empty($service[0])){
      $data['pricing'] = json_encode(array('service' =>$service ,'price' => $price ),JSON_UNESCAPED_SLASHES);
      $this->handyman_model->UpdateHandyman($handyman_id,$data);
      
      } else {
             $datas['pricing'] = '';
             $this->handyman_model->UpdateHandyman($handyman_id,$datas);
             }
      redirect(base_url()."employee/handyman/update/$handyman_id/$offset?tab=price");
      //print_r($data);
    }
   /**
   *  @desc :  This funtion for addcertification 
   *  @param: handyman  id
   *  @return :void
   */

    function addcertification($handyman_id,$offset =0){
           $certification['certification1'] = $this->input->post('certification1');
           $certification['certification2'] = $this->input->post('certification2');
           $addcertification = $this->handyman_model->UpdateHandyman($handyman_id,$certification);
           $output = " Added Certification successfully";
           $userSession = array('success' =>$output);
           $this->session->set_userdata($userSession);
           redirect(base_url()."employee/handyman/update/$handyman_id/$offset?tab=certification");

    }

  /**
   *  @desc :  This funtion get all verified handyman
   *  @param: void
   *  @return :void
   */
    function Allverified(){
      $results['service']        = $this->filter_model->getserviceforfilter();
      $results['agent']          = $this->filter_model->getagent();
      $employee_id               = $this->session->userdata('employee_id');
      $results['one']            = $this->employee_model->verifylist($employee_id,'0');
      $results['three']          = $this->employee_model->verifylist($employee_id,'2');
      $results['forteen']        = $this->employee_model->verifylist($employee_id,'14');  
      $handyman['result']      =  $this->handyman_model->getallverifiedhandyman();
      $this->load->view('employee/header',$result);
      $this->load->view('employee/newhandyman',$handyman);
    }
  
// end of controllers    
}
