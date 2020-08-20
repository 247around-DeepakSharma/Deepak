<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/**
 * Description of SF_authorization_certificate
 *
 * @author gautam
 */
class SF_authorization_certificate extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('sf_authorization_model');
        $this->load->model('reusable_model');
        $this->load->library('SFauthorization_certificate');
        $this->load->library('miscelleneous');
        if ($this->session->userdata('loggedIn') == TRUE) {
            return TRUE;
        } else {
            redirect(base_url() . "employee/login");
        }
    }
    /*
     * List all active SF with thier authorization certificate and status
     */
    public function index() {
        $user_profile = $this->session->userdata;
        $fillter = '';
        if(strtolower(trim($user_profile['user_group'])) === _247AROUND_RM){
            $fillter = 'rm_id =' . $user_profile['id'];
        }else if(strtolower(trim($user_profile['user_group'])) === _247AROUND_ASM){
            $fillter = 'asm_id =' . $user_profile['id'];
        }
         
        $data['service_centers'] = $this->sf_authorization_model->get_all_active_sf_details($fillter);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/sf_authorization_certificates', $data);
    }
    /*
     * Handle Ajax request send authorization certificate to particular SF
     * return JSON
     */
    function send_auth_certificate() {
        $vendor_id = $this->input->post('vendor_id') != '' ? $this->input->post('vendor_id') : '';
        if ($vendor_id != '') {
            if ($this->sfauthorization_certificate->create_new_certificate($vendor_id)) {
                echo json_encode(array('success' => 1), true);
                die;
            } else {
                echo json_encode(array('success' => 0), true);
                die;
            }
        }
    }

}
