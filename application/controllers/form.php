<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Form extends CI_Controller {


  /**
   *  @load list modal and helpers
   */
      function __Construct(){
        parent::__Construct();
        $this->load->model('apis');
        $this->load->model('form_model');
        $this->load->model('handyman_model'); 
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->model('user_model');
        $this->load->model('filter_model');
    }

  /**
   * Index Page for this controller.
   *
   * Maps to the following URL
   *    http://example.com/index.php/welcome
   *  - or -  
   *    http://example.com/index.php/welcome/index
   *  - or -
   * Since this controller is set as the default controller in 
   * config/routes.php, it's displayed at http://example.com/
   *
   * So any other public methods not prefixed with an underscore will
   * map to /index.php/welcome/<method_name>
   * @see http://codeigniter.com/user_guide/general/urls.html
   */
  public function index()
  {
     
  }
/*public function elastichandyman(){
    $handyman['handyman'] = $this->form_model->elastichandyman();
    $this->load->view('result',$handyman);
   }
    public function elasticservice(){
    $service['service'] = $this->form_model->elasticsearvice();
    //print_r(json_encode($handyman,JSON_UNESCAPED_SLASHES));
   $this->load->view('result',$service);
   }
   public function elasticpopular(){
    $popular['popular'] = $this->form_model->elasticpopular();
    $this->load->view('result',$popular);
 
   }*/

  
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
