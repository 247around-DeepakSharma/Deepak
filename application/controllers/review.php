<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Review extends CI_Controller {

  /**
   * @desc : load list modal and helpers
   */
      function __Construct(){
        parent::__Construct();
        $this->load->model('review_model'); 
        $this->load->model('filter_model'); 
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('session');
        if (($this->session->userdata('loggedIn')==TRUE)&&($this->session->userdata('userType')=='admin'))  {
          return TRUE ;
        } else {
          redirect(base_url()."admin");
        }
    }

  /**
   *  @desc : This function will insert review andload filter
   *  @param :void
   *  @return : void
   */
    public function index(){
      $validation = $this->checkValidation();
      if($validation){

         $getReview = $this->getReview();
         $insertReview =  $this->review_model->insertReview($getReview);
         $this->loadView();
         
      } else{
        $result['service']       = $this->filter_model->getserviceforfilter();
        $result['agent']         = $this->filter_model->getagent();
        $this->load->view('admin/header',$result);
        $this->load->view('admin/review');
    }

    }

  /**
   *  @desc : This function to for load review after success and load filter
   *  @param : void
   *  @return : load view
   */

   function loadView(){
     $output['success'] = "Added Review Successfully";
     $result['service']       = $this->filter_model->getserviceforfilter();
     $result['agent']         = $this->filter_model->getagent();
     $this->load->view('admin/header',$result);
     $this->load->view('admin/review',$output);   
   }

  /**
   *  @desc : This function to get form data for adding review
   *  @param : void
   *  @return : array(data)
   */


    public function getReview(){
        $insert['behaviour']    = $this->input->post('behaviour');
        $insert['expertise']    = $this->input->post('expertise');
        $insert['review']       = $this->input->post('review');
        $insert['handyman_id']  = $this->input->post('handyman_id');
        $insert['user_id']      = $this->input->post('user_id');
        $insert['create_date']  = date("Y-m-d h:i:s");
        return $insert;
    }


  /**
   *  @desc : This function for check validation
   *  @param : void
   *  @return : tue if validation true otherwise FALSE
   */

    public function checkValidation(){
      $this->form_validation->set_rules('behaviour', 'Behaviour', 'required|trim|numeric|greater_than[0]|less_than[6]|xss_clean');
      $this->form_validation->set_rules('expertise', 'expertise', 'required|trim|numeric|greater_than[0]|less_than[6]|xss_clean');
      $this->form_validation->set_rules('review', 'Review', 'required|xss_clean');
      $this->form_validation->set_rules('handyman_id', 'Handyman  ID', 'required|trim|numeric|xss_clean');
      $this->form_validation->set_rules('user_id', 'User ID', 'required|trim|numeric|xss_clean');
      $this->form_validation->set_error_delimiters('<div class="error" role="alert">', '</div>');
        if ($this->form_validation->run() == FALSE) {
            return FALSE;
        }
        else {
            return true;
        }

    }

  /**
   *  @desc : This function for view Review
   *  @param : void
   *  @return : all Review
   */

   public function viewReview(){
    $getReview['review'] = $this->review_model->getReview();
    $result['service']       = $this->filter_model->getserviceforfilter();
    $result['agent']         = $this->filter_model->getagent();
    $this->load->view('admin/header',$result);
    $this->load->view('admin/viewreview',$getReview);

   }

  /**
   *  @desc : This function for to do inactive
   *  @param : void
   *  @return : viewreview
   */

  public function toDoinactive($id){
    $inactive['status'] = '0';
    $result = $this->review_model->toDoinactive($id,$inactive);
    $output = " Your Review Deactivated Successfully.";
    $userSession = array('success' =>$output);
    $this->session->set_userdata($userSession);
    redirect(base_url()."review/viewReview");
  }

  /**
   *  @desc : This function for to do inactive
   *  @param : void
   *  @return :redirect  viewreview
   */

  public function toDoactive($id){
    $inactive['status'] = '1';
    $result = $this->review_model->toDoinactive($id,$inactive);
    $output = " Your Review Activated Successfully.";
    $userSession = array('success' =>$output);
    $this->session->set_userdata($userSession);
    redirect(base_url()."review/viewReview");
  }
  /**
   *  @desc : This function  for review message and load filter
   *  @param : void
   *  @return : viewreview
   */

 public function messgae(){
   
    if($_POST){
           $data['reviewmessage'] = $this->input->post('reviewmessage');
           $update = $this->review_model->updatereviewmessage($data);
           $reviewmessage['reviewmessage'] = $this->review_model->getreviewmessage();
           $reviewmessage['sucess']    = "Updated review message sucessfully";
           $result['service']       = $this->filter_model->getserviceforfilter();
           $result['agent']         = $this->filter_model->getagent();
           $this->load->view('admin/header',$result);
           $this->load->view('admin/reviewmessage',$reviewmessage);

              
      } else {
        $reviewmessage['reviewmessage'] = $this->review_model->getreviewmessage();
        $result['service']       = $this->filter_model->getserviceforfilter();
        $result['agent']         = $this->filter_model->getagent();
        $this->load->view('admin/header',$result);
        $this->load->view('admin/reviewmessage',$reviewmessage);

      } 
  }

  /**
   * @desc : This funtion  for sending mail
   * param : input request user email and comment
   *  @return : void
   */

   function sending_mail(){
   $check = $this->checkvalidtemail();
   if($check == TRUE){
       $this->load->library('email');
       $user_email = $this->input->post('user_email');
       $comment    = $this->input->post('comment');
       $this->email->from('feedback@aroundhomz.com', 'around');
       $this->email->to($user_email); 
       $this->email->subject('Reply from Around - Your Review Received');
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
    redirect(base_url()."review/viewReview") ; 
   } else{
    $getReview['review'] = $this->review_model->getReview();
    $result['service']       = $this->filter_model->getserviceforfilter();
    $result['agent']         = $this->filter_model->getagent();
    $this->load->view('admin/header',$result);
    $this->load->view('admin/viewreview',$getReview);

   }
 }

 /**
   *  @desc : This function for check validation 
   *  @param : void
   *  @return : if  validation true return true otherwise false
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

 /**
   *  @desc : This function delete review
   *  @param : id of review
   *  @return : redirect viewreview
   */

    function delete($id){

    $result = $this->review_model->delete($id);
    $output = " Deleted Successfully.";
    $userSession = array('inactive' =>$output);
    $this->session->set_userdata($userSession);
    redirect(base_url()."review/viewReview");

    }

//end controllers
}
