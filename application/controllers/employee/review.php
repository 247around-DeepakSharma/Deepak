<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Review extends CI_Controller {

  /**
   * @desc : load list modal and helpers
   */
      function __Construct(){
        parent::__Construct();
        $this->load->model('review_model'); 
        $this->load->model('filter_model');
        $this->load->model('employee_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('session');
        if (($this->session->userdata('loggedIn')==TRUE)&&($this->session->userdata('userType')=='employee')) {
          return TRUE ;
        } else {
          redirect(base_url()."employee/login");
        }
    }

  /**
   *  @desc : This function will insert review and load verifylist
   *  @param : void
   *  @return : void
   */
    public function index(){
    $getReview['review']      = $this->review_model->getReview();
    $results['service']       = $this->filter_model->getserviceforfilter();
    $results['agent']         = $this->filter_model->getagent();
    $employee_id              = $this->session->userdata('employee_id');
    $results['one']           = $this->employee_model->verifylist($employee_id,'0');
    $results['three']         = $this->employee_model->verifylist($employee_id,'2');
    $results['forteen']       = $this->employee_model->verifylist($employee_id,'14');
    $this->load->view('employee/header',$results);
    $this->load->view('employee/viewreview',$getReview);

    }

 

  /**
   *  @desc : This function for to do inactive
   *  @param : void
   *  @return : viewreview
   */

  public function toDoinactive($id){
    $inactive['status'] = '1';
    $result = $this->review_model->toDoinactive($id,$inactive);
    $output = " Your Review Inactive Successfully.";
    $userSession = array('inactive' =>$output);
    $this->session->set_userdata($userSession);
    redirect(base_url()."employee/review");
  }

  /**
   *  @desc : This function for to do inactive
   *  @param : void
   *  @return : viewreview
   */

  public function toDoactive($id){
    $inactive['status'] = '0';
    $result = $this->review_model->toDoinactive($id,$inactive);
    $output = " Your Review Active Successfully.";
    $userSession = array('success' =>$output);
    $this->session->set_userdata($userSession);
    redirect(base_url()."employee/review");
  }
  /**
   * @desc : This funtion  for sending mail
   * param : input request user email and comment
   *  @return : 
   */

   function sending_mail(){
   $check = $this->checkvalidtemail();
   if($check == TRUE){
       $this->load->library('email');
       $user_email = $this->input->post('user_email');
       $comment    = $this->input->post('comment');
       $this->email->from('suneel@numetriclabz.com', 'boloaaka');
       $this->email->to($user_email); 
       $this->email->subject('reply review reoprt');
       $message = $comment;
       $this->email->message($message);
   if(isset($_POST['file'])){
      $file     = $this->input->post('file');
      $filePath = "./uploads/".$file;
      $this->email->attach($filePath);
   }
   $this->email->send();
  //echo $this->email->print_debugger();
    $output = " Mail Sent Successfully.";
    $userSession = array('success' =>$output);
    $this->session->set_userdata($userSession);
   redirect(base_url()."employee/review") ; 
   } else{
         $this->loadreview();
   }
 }

  /**
   * @desc : This funtion  load review report
   * @param : 
   *  @return : 
   */


 function loadreview(){
    $getReview['review']      = $this->review_model->getReview();
    $getReview['review']      = $this->review_model->getReview();
    $results['service']       = $this->filter_model->getserviceforfilter();
    $results['agent']         = $this->filter_model->getagent();
    $employee_id              = $this->session->userdata('employee_id');
    $results['one']           = $this->employee_model->verifylist($employee_id,'0');
    $results['three']         = $this->employee_model->verifylist($employee_id,'2');
    $results['forteen']       = $this->employee_model->verifylist($employee_id,'14');
    $this->load->view('employee/header',$results);
    $this->load->view('employee/viewreview',$getReview);

 }

 /**
   * @desc : This funtion  validation 
   * @param : 
   *  @return : 
   */


 function checkvalidtemail(){
  $this->form_validation->set_rules('user_email', 'Email', 'required');
  $this->form_validation->set_rules('comment', 'Comment', 'required');
  $this->form_validation->set_rules('file', 'Image', 'callback_uploadImage');
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
                    move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/" . $picName);
                   
                }
            }
            else{
                $this->form_validation->set_message('uploadImage', 'File size or file type is not supported.Allowed extentions are "png", "jpg", "jpeg". Maximum file size is 2MB.');
                return FALSE;
            }
        } 
    }

    function delete($id){

    $result = $this->review_model->delete($id);
    $output = " Deleted Successfully.";
    $userSession = array('inactive' =>$output);
    $this->session->set_userdata($userSession);
    redirect(base_url()."employee/review");

    }


  


//end controllers
}
