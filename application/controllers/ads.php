<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ads extends CI_Controller {

  /**
   * @desc : load list modal and helpers
   */
      function __Construct(){
        parent::__Construct();
        $this->load->model('ads_model');
        $this->load->model('filter_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->library('s3');
        if (($this->session->userdata('loggedIn')==TRUE)&&($this->session->userdata('userType')=='admin'))  {
          return TRUE ;
        } else {
          redirect(base_url()."admin");
        }
    }


 
  /**
   *  @desc : This function will insert ads ,ads image and url  and call filter function 
   *  @param : void
   *  @return :void
   */
    public function index(){
      $validation = $this->checkValidation();
    if($validation){
      $ads['ads']    = $this->input->post('ads');
      $ads['url']    = $this->input->post('url');
      if(isset($_POST['file']))
      $ads['ads_picture'] = $this->input->post('file');
      $insert = $this->ads_model->addads($ads); 
      $output = "Added Successfully";
      $this->loadViews($output);
                
    } else {
      $result['service']       = $this->filter_model->getserviceforfilter();
      $result['agent']         = $this->filter_model->getagent();
      $this->load->view('admin/header',$result);
      $this->load->view('admin/ads');
    }
      

    }


  /**
   *  @desc : This function load output Message and filter
   *  @param : output message
   *  @return : load ads page
   */
   function loadViews($output) {
      $data['sucess'] =$output;
      $result['service']       = $this->filter_model->getserviceforfilter();
      $result['agent']         = $this->filter_model->getagent();
      $this->load->view('admin/header',$result);
      $this->load->view('admin/ads',$data);
       
    }

  /**
   *  @desc : This function for check validation
   *  @param : void
   *  @return : tue if validation true otherwise FALSE
   */
   
  public function checkValidation(){
    $this->form_validation->set_rules('ads', 'Advertise Name', 'required|trim|xss_clean');
    $this->form_validation->set_rules('url', 'URL', 'required|trim|xss_clean');
    $this->form_validation->set_rules('file', 'file ', 'requried|callback_uploadImage');
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
                     $directory = "advertise_photo/".$picName;
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
   *  @desc : This function for view ads and  call filter
   *  @param : void
   *  @return : void
   */

    function view(){
      $result['service']       = $this->filter_model->getserviceforfilter();
      $result['agent']         = $this->filter_model->getagent();
      $getads['ads'] = $this->ads_model->getads();
      $this->load->view('admin/header',$result);
      $this->load->view('admin/viewads',$getads);
    }

  

//end controllers 
}
