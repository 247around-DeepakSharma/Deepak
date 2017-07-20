<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

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
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee') ) {
            return TRUE;
        } else {
            redirect(base_url() . "employee/login");
        }
    }

    public function index() {
        $data['partner'] = $this->partner_model->get_all_partner_source();
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/finduser', $data);
    }

    /**
     * @desc : This function is to find/search user
     *
     * Searches user details with booking id, order id and partner code
     *
     * Also searches user details with user's name.
     *
     * Complete or partial detail entered to search will show all the matching users/bookings in a list,
     *      from which we can select the required one by looking at other details shown.
     *
     * @param: offset, per page number and phone number
     * @return : print Booking on Booking Page
     */
    
    function finduser($offset = 0, $page = 0, $phone_number = ''){
        $booking_id = trim($this->input->post('booking_id'));
        $order_id = trim($this->input->post('order_id'));
        $userName = $this->input->post('userName');
        $partner_id = $this->input->post('partner');
        if ($this->input->post('phone_number')) {
            $phone_number = $this->input->post('phone_number');
        }
        
        //search user by name
        if (!empty($userName)) {
            
            $this->search_user_by_name();
            
        } else if(!empty($booking_id)){ // Search by booking id
            
            $where  = array('booking_details.booking_id' => $booking_id );
            $data['Bookings'] = $this->booking_model->search_bookings($where);
            $this->load_search_view($data);
            
        } else if(!empty($order_id)){ // search by order id
            $where  = array('order_id' => $order_id );
            $data['Bookings'] = $this->booking_model->search_bookings($where, $partner_id);
            $data['search'] = "Search";

            $this->load_search_view($data);
        } else if(!empty ($phone_number)){ // search by phone number
            
                $page = 0;
                
                if ($page == 0) {
                    $page = 50;
                }

                $offset = ($this->uri->segment(5) != '' ? $this->uri->segment(5) : 0);

                $config['base_url'] = base_url() . "employee/user/finduser/" . $offset . "/" . $page . "/" . $phone_number;
                $output_data = $this->user_model->search_user($phone_number, $offset, $page);
                if(!empty($output_data)){
                
                    $config['total_rows'] = count($output_data);
                    $config['per_page'] = $page;
                    $config['uri_segment'] = 5;
                    $config['first_link'] = 'First';
                    $config['last_link'] = 'Last';

                    $this->pagination->initialize($config);
                    $data['links'] = $this->pagination->create_links();

                    $data['data'] = $output_data;
                    $data['appliance_details'] = $this->user_model->appliance_details($phone_number);
                    $data['sms_sent_details'] = $this->booking_model->get_sms_sent_details_for_empty_bookings($phone_number);

                    $this->load->view('employee/header/'.$this->session->userdata('user_group'));
                    $this->load->view('employee/bookinghistory', $data);
                } else {
                    $output['phone_number'] = $phone_number;
                    $this->loadViews($output);
                }

        } else {
         
            echo "Please Select Any Field";
            
        }
        
    }

    /**
     * @desc: this is used to load view on the basis of booking or query and its current status
     *  @param: Array
     */
    function load_search_view($data){
        $view = 'employee/search_result';
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view($view, $data);
    }


    /**
     * @desc : This function is used to find user by their name
     *
     * The name entered to search could be user's complete name or partial name as well.
     *
     * @param : void
     * @return : array of data(searched results) to the view
     */
    function search_user_by_name() {
        $userName = $this->input->post('userName');
        $this->data['result'] = $this->user_model->get_searched_user(trim($userName));
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/search_user_list', $this->data);
    }

    /**
     * @desc : This function is used to check phone number validation
     *
     * @param : void
     * @return : returns true if validation is true else false
     */
    public function CheckPhoneValidation() {
        $this->form_validation->set_rules('phone_number', 'phone_number', 'required');
        $this->form_validation->set_error_delimiters('<div class="error" role="alert">', '</div>');

        if ($this->form_validation->run() == FALSE) {
            return FALSE;
        } else {
            return true;
        }
    }

    /**
     * @desc : This function is used to load the view to add user
     *
     * Also sends user's detail(phone no.) and all states list to view
     *
     * @param : user's details
     * @return : void
     */
    function loadViews($output) {

        $results['user'] = $output;
        
        //gets all states while adding user as users can be of any state
        $results['state'] = $this->vendor_model->getall_state();
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/adduser', $results);
    }

    /**
     * @desc : This function is used to add a new user
     *
     * @param : void
     * @return : takes to booking history page of newly added user
     */
    function adduser() {
        $user['phone_number'] = $this->input->post("phone_number");
        $user['name'] = $this->input->post('name');
        $user['user_email'] = $this->input->post('user_email');
        $user['home_address'] = $this->input->post('home_address');
        $user['city'] = $this->input->post('city');
        $user['state'] = $this->input->post('state');
        $user['pincode'] = $this->input->post('pincode');
        $user['alternate_phone_number'] = $this->input->post('alternate_phone_number');
        $user['create_date'] = date("Y-m-d H:i:s");

        //Add the user
        $user_id = $this->user_model->add_user($user);

        //Add sample appliances for this user
//        $output = $this->booking_model->addSampleAppliances($user_id, 5);
        $this->booking_model->addSampleAppliances($user_id, 5);

        //Below two queries are running for no use, remove after confermation
//        $data1 = $this->user_model->search_user($user['phone_number']);
//        $appliance_details = $this->user_model->appliance_details($user['phone_number']);
        redirect(base_url() . 'employee/user/finduser/0/0/' . $user['phone_number']);
    }

    /**
     * @desc : This function is used to check validation for different fields
     *
     * @param : void
     * @return : returns true if validation satifies else false
     */
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
     *  @desc : This function is to get form to edit user's details.
     *  @param : phone number
     *  @return : user's details to view
     */
    function get_edit_user_form($phone_number = "") {
        if ($this->input->post('phone_number')) {

            $phone_number = $this->input->post('phone_number');
        }

        $data['user'] = $this->user_model->search_user($phone_number);
        $data['state'] = $this->vendor_model->getall_state();
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        
        $this->load->view('employee/edituser', $data);
    }

    /**
     *  @desc : This function is to save edited user's details
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
        //Edits user details
        $this->user_model->edit_user($edit);
        
        //Logging Details
        log_message('info',__FUNCTION__.' User Details has been updated '.print_r($edit, TRUE));
        
        redirect(base_url() . 'employee/user/finduser/0/0/' . $edit['phone_number']);
    }

    /**
     *  @desc : This function is to get user's details
     *  @param : start, per page limit and phone number
     *  @return : user details and load view
     */
    function user_details($offset = 0, $page = 0, $phone_number = "") {

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

            $this->load->view('employee/header/'.$this->session->userdata('user_group'));
            $this->load->view('employee/bookinghistory', array('data1' => $data1, 'data' => $data, 'links' =>
                $links, 'appliance_details' => $appliance_details));
        }
    }

    /**
     * @desc : this function is used to load form to get user month wise
     * @param : void
     * @return : load view
     */
    function get_user_count_view() {
        $data = $this->user_model->get_city_source();

        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/getusers', $data);
    }

    /**
     * @desc : this function is used to count total user,  completed booking and cancelled booking
     * @param : void
     * @return : load table
     */
    function getusercount() {
        $data['city'] = $this->input->post('city');
        $data['type'] = $this->input->post('type');
        $data['source'] = $this->input->post('source');

        $user['user'] = $this->user_model->get_count_user($data);
        $this->load->view('employee/getusers', $user);
    }

    /**
     * @desc : this function is used to count all the transactional users
     * @param : void
     * @return : load table
     */
    function user_count() {
        $data = $this->user_model->get_city_source();
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/transactionalusers', $data);
    }

    /**
     * @desc : this function is used to count all the trandactional users
     * @param : void
     * @return : load view
     */
    function post_transactional_users() {
        $data['type'] = $this->input->post('type');
        $data['source'] = $this->input->post('source');
        $user['user'] = $this->user_model->get_count_transactional_user($data);
        $this->load->view('employee/transactionalusers', $user);
    }
    
    /**
     * @Desc: This function is used for new employee registration
     * @params: void
     * @return: view
     * 
     */
    function add_employee(){
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/employee_add_edit');
}
    
    /**
     * @Desc: This function is used to process employee add form
     * @parmas: POST Array
     * @return: void
     * 
     */
    function process_add_employee(){
        $data = $this->input->post();
        $data['employee_password'] = md5($this->input->post('employee_password'));
        $data['clear_password'] = $this->input->post('employee_password');
        $data['create_date'] = date('Y-m-d H:i:s');
        $id = $this->employee_model->insertData($data);
        $this->session->set_userdata('success','Employee Added Sucessfully.');
        
        redirect(base_url() . "employee/user/show_employee_list");
    }
    
    /**
     * @Desc: This function is used to show employee list
     * @params: void
     * @return: view
     * 
     */
    function show_employee_list(){
        $data['data'] = $this->employee_model->get_employee();
        $data['session_data'] = $this->session->all_userdata();
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/employee_list',$data);
    }
    
    /**
     * @Desc: This function is used to edit an employee
     * @parmas: id of employee
     * @return: view
     * 
     */
    function update_employee($id){
        $data['id'] = $id;
        $data['query'] = $this->employee_model->getemployeefromid($id);
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/employee_add_edit',$data);
    }
    
    /**
     * 
     * @Desc: This function is used to prcess update employee form
     * @params: POST array
     * @return: view
     */
    function process_edit_employee(){
        $data = $this->input->post();
        if(!empty($this->input->post('employee_password'))){
            $data['employee_password'] = md5($this->input->post('employee_password'));
            $data['clear_password'] = $this->input->post('employee_password');
        }
        $this->employee_model->update($data['id'],$data);
        
        $this->session->set_userdata('success','Employee Updated Sucessfully.');
        
        redirect(base_url() . "employee/user/show_employee_list");
    }
    
    /**
     *@Desc: This function is used to show holiday list to employees
     * @params: void
     * @return: void 
     * 
     */
    function show_holiday_list(){
        $data['data'] = $this->employee_model->get_holiday_list();
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/show_holiday_list',$data);
    }
     
}
