<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();
        $this->load->model('user_model');
        $this->load->model('employee_model');
        $this->load->model('booking_model');
        $this->load->model('partner_model');
        $this->load->model('vendor_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library("pagination");
        $this->load->library("session");
        $this->load->library('s3');
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee') && ($this->session->userdata('add service') == '1')) {
            return TRUE;
        } else {
            redirect(base_url() . "employee/login");
        }
    }

    public function index() {
        $data['partner'] = $this->partner_model->get_all_partner_source();
        $this->load->view('employee/header');
        $this->load->view('employee/finduser', $data);
    }

    function finduser($offset = 0,$page = 0,$phone_number ='' ) {

        $booking_id = $this->input->post('booking_id');
        $order_id = $this->input->post('order_id');
        $partner_code = $this->input->post('partner');

        if($this->input->post('phone_number')){
            $phone_number = $this->input->post('phone_number');
        }

        if(!empty($order_id)){
            $this->search_by_OrderId($partner_code, $order_id);
        }

        if(!empty($_POST['userName']))
            $this->search_user_by_name();

        if ($phone_number != "") {
            $output = $this->user_model->search_user($phone_number);

            if (empty($output)) {
                $output['phone_number'] = $phone_number;
                $this->loadViews($output);

            } else {

                //$this->user_details($phone_number,$offset = 0, $page = 0)
                $page = 0;
                $offset = 0;
                if ($page == 0) {
                    $page = 50;
                }

                $offset = ($this->uri->segment(5) != '' ? $this->uri->segment(5) : 0);

                $config['base_url'] = base_url() . "employee/user/finduser/".$offset."/".$page."/".$phone_number;
                $config['total_rows'] = $this->booking_model->total_user_booking($output[0]['user_id']);
                $config['per_page'] = $page;
                $config['uri_segment'] = 5;
                $config['first_link'] = 'First';
                $config['last_link'] = 'Last';

                $this->pagination->initialize($config);
                $data['links'] = $this->pagination->create_links();

                $data['data'] = $this->user_model->booking_history($phone_number, $config['per_page'], $offset);

                if(empty($data['data'])){

                    $data['data'] = $output;
                }

                $data['appliance_details'] = $this->user_model->appliance_details($phone_number);


                $this->load->view('employee/header');
                $this->load->view('employee/bookinghistory', $data);
            }
        } elseif ($booking_id != "") {
            $data['Bookings'] = $this->booking_model->search_bookings_by_booking_id($booking_id);

            $this->load->view('employee/header');


            $this->load->view('employee/booking', $data);
        }
    }

    /**
     * @desc : This function is used to find use by order id
     * @param : partner code and order id
     * @return : void
     */

    function search_by_OrderId($partner_code, $order_id){
       $data['Bookings'] = $this->user_model->getBookingId_by_orderId($partner_code, $order_id);
       $data['search'] = "Search";
    
       $this->load->view('employee/header');
       $this->load->view('employee/booking', $data);
    }

    function search_user_by_name(){
        $userName = $this->input->post('userName');
        $this->data['result'] = $this->user_model->get_searched_user(trim($userName));
	    $this->load->view('employee/header');
        $this->load->view('employee/search_user_list', $this->data);
    }

    public function CheckPhoneValidation() {
        $this->form_validation->set_rules('phone_number', 'phone_number', 'required');
        $this->form_validation->set_error_delimiters('<div class="error" role="alert">', '</div>');

        if ($this->form_validation->run() == FALSE) {
            return FALSE;
        } else {
            return true;
        }
    }

    function loadViews($output) {

        $results['user'] = $output;
        $results['state'] = $this->vendor_model->selectSate();
        $this->load->view('employee/header');
        $this->load->view('employee/adduser', $results);
    }

    function adduser() {
        $user['phone_number'] = $this->input->post("phone_number");
        $user['name'] = $this->input->post('name');
        $user['user_email'] = $this->input->post('user_email');
        $user['home_address'] = $this->input->post('home_address');
        $user['city'] = $this->input->post('city');
        $user['state'] = $this->input->post('state');
        $user['pincode'] = $this->input->post('pincode');
        $user['alternate_phone_number'] = $this->input->post('alternate_phone_number');
        $user['create_date'] = date("Y-m-d h:i:s");
        $user_id = $this->user_model->add_user($user);

        //Add sample appliances for this user
        $output = $this->booking_model->addSampleAppliances($user_id, 5);

        $data1 = $this->user_model->search_user($user['phone_number']);
        $appliance_details = $this->user_model->appliance_details($user['phone_number']);
        redirect(base_url().'employee/user/finduser/0/0/'.$user['phone_number']);
    }

    public function checkValidation() {

        $this->form_validation->set_rules('phone_number', 'phone_number', 'trim|exact_length[10]|required|xss_clean');
        $this->form_validation->set_rules('name', 'name', 'required');
        $this->form_validation->set_rules('home_address', 'home_address', 'required');
        $this->form_validation->set_error_delimiters('<div class="error" role="alert">', '</div>');

        if ($this->form_validation->run() == FALSE) {
            //echo "Ok";
            return FALSE;
        } else {
            return true;
        }
    }

    /**
     *  @desc : This function is to edit particular user's details
     *  @param : void
     *  @return : user's details to view
     */
    function get_edit_user_form($phone_number ="") {
        if($this->input->post('phone_number')){
            
            $phone_number = $this->input->post('phone_number');
        }
        
        $data['user'] = $this->user_model->search_user($phone_number);
        $data['state'] = $this->vendor_model->selectSate();
        $this->load->view('employee/header');
        //$this->load->view('employee/addbooking');
        $this->load->view('employee/edituser', $data);
    }

    /**
     *  @desc : This function is to edit users details
     *  @param : void
     *  @return : edit user details and load view
     */
    function process_edit_user_form() {
        $edit['user_id'] = $this->input->post('user_id');
        $edit['home_address'] = $this->input->post('home_address');
        $edit['user_email'] = $this->input->post('user_email');
        $edit['phone_number'] = $this->input->post('phone_number');
        $edit['city'] = $this->input->post('city');
        $edit['state'] = $this->input->post('state');
        $edit['name'] = $this->input->post('name');
        $edit['alternate_phone_number'] = $this->input->post('alternate_phone_number');
        $edit['pincode'] = $this->input->post('pincode');
        $output = $this->user_model->edit_user($edit);
        redirect(base_url().'employee/user/finduser/0/0/'.$edit['phone_number']);

    }

    function user_details($offset = 0, $page = 0, $phone_number="") {

        $output = $this->user_model->search_user($phone_number);

        if (empty($output)) {
            $output['phone_number'] = $phone_number;
            $this->loadViews($output);
        } else {
            if ($page == 0) {
                $page = 50;
            }

            $offset = ($this->uri->segment(5) != '' ? $this->uri->segment(5) : 0);

            $config['base_url'] = base_url() . "employee/user/user_details/0/0/$phone_number";
            $config['total_rows'] = $this->booking_model->total_user_booking($output[0]['user_id']);
            $config['per_page'] = $page;
            $config['uri_segment'] = 5;
            $config['first_link'] = 'First';
            $config['last_link'] = 'Last';

            $this->pagination->initialize($config);
            $links = $this->pagination->create_links();

            $data1 = $output;
            $query = $this->user_model->booking_history($phone_number, $config['per_page'], $offset);
            $data = $query;

            $appliance_details = $this->user_model->appliance_details($phone_number);

            $this->load->view('employee/header');
            $this->load->view('employee/bookinghistory', array('data1' => $data1, 'data' => $data, 'links' =>
                $links, 'appliance_details' => $appliance_details));
        }
    }

     /**
     * @desc : this function is used to load form to get user month wise
     * @param : void
     * @return : load view
     */

    function get_user_count_view(){
        $data = $this->user_model->get_city_source();

        $this->load->view('employee/header');
        $this->load->view('employee/getusers', $data);
    }
    
    /**
     * @desc : this function is used to count total user,  completed booking and cancelled booking
     * @param : void
     * @return : load table
     */
    function getusercount(){
        $data['city'] = $this->input->post('city');
        $data['type'] = $this->input->post('type');
        $data['source'] = $this->input->post('source');

        $user['user'] = $this->user_model->get_count_user($data);
        $this->load->view('employee/getusers', $user);
    }
    
    /**
     * 
     */
    function user_count(){
        $data = $this->user_model->get_city_source();       
        $this->load->view('employee/header');
        $this->load->view('employee/transactionalusers',$data);

    }
    function post_transactional_users(){
        $data['type'] = $this->input->post('type');
        $data['source'] = $this->input->post('source');
        $user['user'] = $this->user_model->get_count_transactional_user($data);
        $this->load->view('employee/transactionalusers', $user);
    }
}
