
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
        $this->load->library('miscelleneous');
        $this->load->library('notify');
        $this->load->library('booking_utilities');
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee') ) {
            return TRUE;
        } else {
            redirect(base_url() . "employee/login");
        }
    }

    public function index() {
        $data['partner'] = $this->partner_model->get_all_partner_source();
        $this->miscelleneous->load_nav_header();
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
    
    function finduser(){
        $booking_id = preg_replace('/[^A-Za-z0-9\-]/', '',trim($this->input->get('booking_id')));
        $order_id = preg_replace('/[^A-Za-z0-9\-]/', '',trim($this->input->get('order_id')));
        //$userName = preg_replace('/[^A-Za-z0-9\-]/', '',trim($this->input->get('userName')));
        $tempuserName = ltrim($this->input->get('userName'));
        $userName = rtrim($tempuserName);
        $partner_id = $this->input->get('partner');
        $search = preg_replace('/[^A-Za-z0-9\-]/', '',trim($this->input->get('search_value')));
        $post['length'] = -1;
        $phone_number = preg_replace('/[^A-Za-z0-9\-]/', '',$this->input->get('phone_number'));
        $is_flag = true;
        if (!empty($search)) {
            if (preg_match("/^[6-9]{1}[0-9]{9}$/", $search)) {
                $phone_number = $search;
            } else {
                $booking_id = $search;
            }
        }
        $select = "services.services, service_centres.name as service_centre_name,
            service_centres.primary_contact_phone_1, service_centres.primary_contact_name,
            users.phone_number, users.name as customername,booking_details.type,
            users.phone_number, booking_details.*,penalty_on_booking.active as penalty_active, users.user_id";
        if(!empty($booking_id)){
            
            $post['search_value'] = $booking_id;
            $post['column_search'] = array('booking_details.booking_id');
            $post['order'] = array(array('column' => 0,'dir' => 'asc'));
            $post['order_performed_on_count'] = TRUE;
            $post['column_order'] = array('booking_details.booking_id');
            $post['unit_not_required'] = true;
            
            
            $view = "employee/search_result";
            
        } else if(!empty($order_id)){
            $post['search_value'] = $order_id;
            $post['column_search'] = array('booking_details.order_id');
            $post['where'] = array('booking_details.partner_id' =>$partner_id);
            $post['unit_not_required'] = true;
            
            $view = "employee/search_result";
           
            
        } else if(!empty($userName)){
            
            $select = "users.name as customername,
            users.phone_number, users.user_email, users.home_address, users.pincode, users.account_email";
            $post['search_value'] = $userName;
            $post['column_search'] = array('users.name');
            $post['order'] = array(array('column' => 0,'dir' => 'asc'));
            $post['order_performed_on_count'] = TRUE;
            $post['column_order'] = array('users.name');
            $post['unit_not_required'] = true;
            $view = "employee/search_user_list";
            
            
        }  else if(!empty($phone_number)){
            
//            $post['search_value'] = $phone_number;
//            $post['column_search'] = array('booking_details.booking_primary_contact_no',
//                 'booking_alternate_contact_no', 'users.phone_number');
            $data['Bookings'] = $this->user_model->search_user($phone_number, "", "", TRUE);
            $is_flag = false;
            $view = "employee/bookinghistory";
            
        } else{
            echo "Please Select Atlease One Input Field.";
            exit();
        }
        if($is_flag){
            $data['Bookings'] = $this->booking_model->get_bookings_by_status($post,$select);
        } 
        
        if(!empty($phone_number) && empty($data['Bookings'])){
          //  $output['phone_number'] = $phone_number;
            redirect(base_url()."employee/booking/addbooking/".$phone_number);
            //$this->loadViews($output);
        } else {
             $this->load_search_view($data, $view);
        }
    }
    
    function get_sms_Send_detail_and_user_applinace($phone_number){
        log_message("info", __METHOD__);
        $data['appliance_details'] = $this->user_model->appliance_details($phone_number);
        $data['sms_sent_details'] = $this->booking_model->get_sms_sent_details_for_empty_bookings($phone_number);
        $this->load->view("employee/user_appliance_sms_send_detail", $data);
        
    }

    /**
     * @desc: this is used to load view on the basis of booking or query and its current status
     *  @param: Array
     */
    function load_search_view($data,$view){
        $this->miscelleneous->load_nav_header();
        $data['c2c'] = $this->booking_utilities->check_feature_enable_or_not(CALLING_FEATURE_IS_ENABLE);
        $data['saas_module'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view($view, $data);
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
        $results['state'] = $this->vendor_model->get_allstates();
        $this->miscelleneous->load_nav_header();
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
        redirect(base_url() . 'employee/user/finduser?phone_number=' . $user['phone_number']);
    }

    /**
     * @desc : This function is used to check validation for different fields
     *
     * @param : void
     * @return : returns true if validation satifies else false
     */
    public function checkValidation() {

        $this->form_validation->set_rules('phone_number', 'phone_number', 'trim|exact_length[10]|required');
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
        $data['state'] = $this->vendor_model->get_allstates();
        $data['c2c'] = $this->booking_utilities->check_feature_enable_or_not(CALLING_FEATURE_IS_ENABLE);
        $this->miscelleneous->load_nav_header();
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
        
        redirect(base_url() . 'employee/user/finduser?phone_number=' . $edit['phone_number']);
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
            $this->miscelleneous->load_nav_header();
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
        $this->miscelleneous->load_nav_header();
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
        $this->miscelleneous->load_nav_header();
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
        $cond= array('where' => array('entity_type'=>'247Around'), 'order_by' => 'department');
        $data['employee_dept'] = $this->employee_model->get_entity_role('department',$cond);//$this->employee_model->get_employee_groups();
        $data['employee_list'] = $this->employee_model->get_employee();
        $data['error'] = $this->session->flashdata('error');
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/employee_add_edit', $data);
    }
    
    /**
     * @Desc: This function is used to process employee add form
     * @parmas: POST Array
     * @return: void
     * 
     */
    function process_add_employee(){
        $data = $this->input->post();
        
        $removeKeys = array('manager', 'subordinate');
        $data1=array_diff_key($data, array_flip($removeKeys));
        
        if($data == $data1)
            exit("Please add manager or subordinate!");
        
        $data1['groups']= str_replace(' ', '', $data1['groups']);
        
        $data1['clear_password'] = $this->randomPassword();
        $data1['employee_password'] = md5($data1['clear_password']);
        $data1['create_date'] = date('Y-m-d H:i:s');
        
        $maxid = 0;
        $row = $this->db->query('SELECT MAX(id) maxid FROM employee')->row();
        if ($row) {
            $maxid = $row->maxid; 
        }

        $maxid=10000+$maxid;

        do {
            ++$maxid;
            $row = $this->db->query('SELECT * FROM employee where employee_id='.$maxid)->result_array();
        }while (count($row)>0);
        $data1['employee_id'] = $maxid;
        
        $id = $this->employee_model->insertData($data1);
        $data2 = array();
        
        if(isset($data['manager'])) {
            $manager=$this->input->post('manager');
            
            $data2[]=array("id" => $id, "manager" => $manager);
            
        }
        
        if(isset($data['subordinate'])) {
            $subordinate=$this->input->post('subordinate');
            
            foreach($subordinate as $key=>$val) {
                $data2[]=array("id" => $val, "manager" => $id);
            }
        }
        
        if(count($data2) > 0)
            $this->employee_model->insertManagerData($data2);
        $tag='employee_login_details';
        if(!$this->process_mail_to_employee($tag,$id,$manager)) {
            //Logging error if there is some error in sending mail
            log_message('info', __FUNCTION__ . " Sending Mail Error..  ");
            $error = ' Sending Mail Error..  ';
            $this->session->set_flashdata('error', $error);
            redirect(base_url() . "employee/user/add_employee");
        }
        
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
        foreach($data['data'] as $key => $value) {
            $data['data'][$key]['manager'] = $this->employee_model->getemployeeManagerDetails("employee.*",array('employee_hierarchy_mapping.employee_id' => $value['id']));
        }
        
        $data['session_data'] = $this->session->all_userdata();
        $data['c2c'] = $this->booking_utilities->check_feature_enable_or_not(CALLING_FEATURE_IS_ENABLE);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/employee_list',$data);
    }
    
    /**
     * @Desc: This function is used to edit an employee
     * @parmas: id of employee
     * @return: view
     * 
     */
    function update_employee($id = ""){   
        $data['id'] = $this->session->userdata('id');
        $data['user_group'] = $this->session->userdata('user_group');
        
        $id = ((!$id) ? $data['id'] : $id);
                
        $data['query'] = $this->employee_model->getemployeefromid($id);
        $employee_list = $this->employee_model->get_employee($id);
        $data['employee_list']=array_filter($employee_list, function($v) use($id) {return $v['id'] != $id;});
        
        $cond= array('where' => array('entity_type'=>'247Around'), 'order_by' => 'department');
        $data['employee_dept'] = $this->employee_model->get_entity_role('department',$cond);
        if(!empty($data['employee_dept'])) {
            $cond= array('where' => array('entity_type'=>'247Around', 'department' => $data['query'][0]['department']), 'order_by' => 'role');
            $data['employee_role'] = $this->employee_model->get_entity_role('role',$cond);
        }
        
        $manager=$this->employee_model->getemployeeManagerDetails("employee_hierarchy_mapping.*",array('employee_hierarchy_mapping.employee_id' => $id));
        $subordinate=$this->employee_model->getemployeeManagerDetails("employee_hierarchy_mapping.*",array('employee_hierarchy_mapping.manager_id' => $id));
        
        if(!empty($manager))
        $data['manager']=$manager[0]['manager_id'];
        if(!empty($subordinate))
        $data['subordinate']=$subordinate;
        
        $data['error'] = $this->session->flashdata('error');
        $this->miscelleneous->load_nav_header();
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
        $removeKeys = array('manager', 'subordinate');
        $data1=array_diff_key($data, array_flip($removeKeys));
        
        $data1['groups']= str_replace(' ', '', $data1['groups']);
        
        $this->employee_model->update($data1['id'],$data1);
        
        $data2 = array();
        if(isset($data['manager'])) {
            $manager=$this->input->post('manager');
            
            $data2[]=array("id" => $data1['id'], "manager" => $manager);
            
        }
        
        if(count($data2) > 0) {
            $data3=$this->employee_model->getemployeeManagerDetails("employee_hierarchy_mapping.*",array('employee_hierarchy_mapping.employee_id' => $data1['id']));

            if(count($data3) <= 0)
                $this->employee_model->insertManagerData($data2);
            else
                $this->employee_model->updateManager($data2);
        }
        
        
        $data2 = array();
        
        if(isset($data['subordinate'])) {
            $subordinate=$this->input->post('subordinate');
            
            foreach($subordinate as $key=>$val) {
                $data2[]=array("id" => $val, "manager" => $data1['id']);
            }
        }
        
        $data3=$this->employee_model->getemployeeManagerDetails("employee_hierarchy_mapping.*",array('employee_hierarchy_mapping.manager_id' => $data1['id']));
        
        if(count($data3) > 0)
            $this->employee_model->deleteManager("manager_id in (".$data1['id'].")");
        if(count($data2) > 0) {
            $this->employee_model->insertManagerData($data2);
        }
        
        $this->session->set_userdata('success','Employee Updated Sucessfully.');
        
        redirect(base_url() . "employee/user/show_employee_list");
    }
    
    /**
     * 
     * @Desc: This function is used to inactive employee
     * @params: employee id
     * @return: view
     */
    function deactive_employee($id){
        $data = array("active"=>0);
        $this->employee_model->update($id,$data);
        $this->session->set_userdata('success','Employee Updated Sucessfully.');
        redirect(base_url() . "employee/user/show_employee_list");
    }
    
    /**
     * 
     * @Desc: This function is used to reset employee password
     * @params: employee id
     * @return: view
     */
    function reset_password($id){
        $data['clear_password'] = $this->randomPassword();
        $data['employee_password'] = md5($data['clear_password']);
        $this->employee_model->update($id,$data);
        $manager=$this->employee_model->getemployeeManagerDetails("employee_hierarchy_mapping.*",array('employee_hierarchy_mapping.employee_id' => $id));
        if(!empty($manager))
            $manager = $manager[0]['manager_id'];
        $tag='employee_reset_password';
        if(!$this->process_mail_to_employee($tag,$id,$manager)) {
            //Logging error if there is some error in sending mail
            log_message('info', __FUNCTION__ . " Sending Mail Error..  ");
            echo json_encode(array('status' => "error", "message" => " Sending Mail Error..  "));
        }
        else {
            echo json_encode(array('status' => "success", "message" => "Password Reset Sucessfully."));
        }
    }
    /**
     *@Desc: This function is used to show holiday list to employees
     * @params: void
     * @return: void 
     * 
     */
    function show_holiday_list(){
        $data['data'] = $this->employee_model->get_holiday_list();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/show_holiday_list',$data);
    }
    
    /**
     *@Desc: This function is used to get random password of length 8 
     * @params: void
     * @return: void 
     * 
     */
    function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
    /**
     * @desc: This function is used to send mail to newly created employee or if password is reset
     * params: employee id
     * return: void
     * 
     */
    function process_mail_to_employee($tag,$id,$manager_id) {
        //Setting flag as TRUE ->Success and FALSE -> Failure
        $flag = TRUE;
        $attachment = "";
        //Get email template values for employee
        $email = array();
        $email['where'] = array(
            'entity' => 'employee',
            'template' => $tag
        );
        $email['select'] = '*';
        $email_template = $this->vendor_model->get_247around_email_template($email);
        
        if (!empty($email_template)) {
            $template_value = $email_template[0]['template_values'];
            //Making array for template values 
            $template_array = explode(',', $template_value);
            
            //Getting value in array from template_values column
            foreach ($template_array as $val) {
                $table['table_name'] = explode('.', $val)[0];
                $table['column_name'] = explode('.', $val)[1];
                $table['primary_key'] = explode('.', $val)[2];
                $template[] = $table;
            }
            

            $employee_details = $this->employee_model->getemployeefromid($id);
            
            //Setting TO for Email
            $to = (($employee_details[0]['official_email'] != '')?$employee_details[0]['official_email']:'');
            
            if(!empty($manager_id)) {
                $manager_details = $this->employee_model->getemployeefromid($manager_id);
                $to .= (($manager_details[0]['official_email'] != '')? (','.$manager_details[0]['official_email']):'');
            }
            
            if(!empty($email_template[0]['to'])) {
                $to .= ','.$email_template[0]['to'];
            }
            
            $to = trim($to,',');
            
            if($to == '') {
                //Logging error if there is some error in sending mail
                log_message('info', __FUNCTION__ . " No Sender for email.  ");
                return FALSE;
            }
            
            $temp=array();
            foreach ($template as $value) {
                $value['id'] = $id;
                //Getting employee details
                $employee_data = $this->vendor_model->get_data($value);

                if ($employee_data) {
                    $temp[] = $employee_data[0][$value['column_name']];
                } else {
                    //Logging error when values not found
                    log_message('info', __FUNCTION__ . ' Mail send Error. No data found to the following employee ID ' . $employee_details[0]['id']);
                    log_message('info', __FUNCTION__ . ' Template values are - ' . print_r($value, TRUE));
                    //Set Flag to check success or error of AJAX call
                    $flag = FALSE;
                }
            }
            //Sending Mail to the employee
            if (isset($temp)) {
                $emailBody = vsprintf($email_template[0]['body'], $temp);
                //Sending Mail
                $this->notify->sendEmail($email_template[0]['from'], $to, $email_template[0]['cc'], $email_template[0]['bcc'], $email_template[0]['subject'], $emailBody, $attachment,$email_template[0]['template']);
                //Login send mail details
                log_message('info', __FUNCTION__ . ' Mail send to the following employee ID ' . $employee_details[0]['id']);
                //Set Flag to check success or error
                $flag = TRUE;
            }
        }
        return $flag;
    }
    /**
     * @Desc: This function is used to get role based on department
     * @params: void
     * @return: view
     * 
     */
    function get_role_on_department(){
        $department = $this->input->post('department');
        
        $data = array();
        if(!empty($department))
        {
            $cond= array('where' => array('entity_type'=>'247Around', 'department' => $department), 'order_by' => 'role');
            $data = $this->employee_model->get_entity_role('role',$cond);
        }
        echo json_encode($data);
    }
     /* @desc function change password of admin.
     * @author Ankit Rajvanshi.
     * @since 17-May-2019.
     */
    function change_password() {
        
        if($_POST) :
            // declaring variables.
            $id = $this->session->userdata['id'];
            $employee_id = $this->session->userdata['employee_id'];
            $old_password = md5($_POST['old_password']);
            // fetch record.
            $employee = $this->reusable_model->get_search_result_data('employee', '*', ['id' => $id, 'employee_password' => $old_password],null,null,null,null,null,[]);
        endif;
        
        if($this->input->is_ajax_request()) : // verify old password.
            if(!empty($employee)) :
                echo '1';exit;
            else :
                echo'0';exit;
            endif;
        elseif($_POST) :
            // Update password.
            $affected_rows = $this->reusable_model->update_table('employee', ['employee_password' => md5($_POST['new_password']), 'clear_password'=> $_POST['new_password']], ['id' => $id]);
            // setting feedback message for user.
            $this->session->set_userdata(['success' => 'Password has been changed successfully.']);
            redirect(base_url() . "employee/user/change_password");
        endif;
        
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/change_password');
    }
     
}
