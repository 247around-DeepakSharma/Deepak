<?php

if (!defined('BASEPATH')){
    exit('No direct script access allowed');
}

class Partner extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();

        $this->load->model('booking_model');
        $this->load->model('partner_model');
        $this->load->model('vendor_model');
        $this->load->model('user_model');
        $this->load->model('invoices_model');
        $this->load->model('service_centers_model');
        $this->load->library("pagination");
        $this->load->library("session");
        $this->load->library('form_validation');
        $this->load->library('notify');
        $this->load->library('asynchronous_lib');
        $this->load->library('booking_utilities');
        $this->load->library('user_agent');

        $this->load->helper(array('form', 'url'));
    }

    /**
     * @desc: This is used to load Partner  Login Page
     *
     * @param: void
     * @return: void
     */
    function index() {
       $this->load->view('partner/partner_login');

    }

     /**
     * @desc: This is used to login
     *
     * If user name and password matches allowed to login and redirect pending booking, else error message appears.
     *
     * @param: void
     * @return: void
     */
    function partner_login() {

        $data['user_name'] = $this->input->post('user_name');
        $data['password'] = md5($this->input->post('password'));
        $partner = $this->partner_model->partner_login($data);
        if ($partner) {
            //get partner details now
            $partner_details = $this->partner_model->getpartner($partner['partner_id']);

            $this->setSession($partner_details[0]['id'], $partner_details[0]['public_name'], $partner['id']);
            log_message('info', 'Partner loggedIn  partner id' .
                    $partner_details[0]['id'] . " Partner name" . $partner_details[0]['public_name']);

            //Saving Login Details in Database
            $login_data['browser'] = $this->agent->browser();
            $login_data['agent_string'] = $this->agent->agent_string();
            $login_data['ip'] = $this->session->all_userdata()['ip_address'];
            $login_data['action'] = _247AROUND_LOGIN;
            $login_data['entity_type'] = $this->session->all_userdata()['userType'];
            $login_data['agent_id'] = $this->session->all_userdata()['agent_id'];
            $login_data['entity_id'] = $this->session->all_userdata()['partner_id'];

            $login_id = $this->employee_model->add_login_logout_details($login_data);
            //Adding Log Details
            if ($login_id) {
                log_message('info', __FUNCTION__ . ' Logging details have been captured for partner ' . $login_data['agent_id']);
            } else {
                log_message('info', __FUNCTION__ . ' Err in capturing logging details for partner ' . $login_data['agent_id']);
            }

            redirect(base_url() . "partner/get_spare_parts_booking");
        } else {

            $userSession = array('error' => 'Please enter correct user name and password');
            $this->session->set_userdata($userSession);
            redirect(base_url() . "partner/login");
        }
    }

     /**
     * @desc: this is used to load pending booking
     * @param: Offset and page no.
     * @return: void
     */
    function pending_booking($offset = 0) {
       $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');
        $config['base_url'] = base_url() . 'partner/pending_booking';
        $total_rows = $this->partner_model->getPending_booking($partner_id);
        $config['total_rows'] = count($total_rows);

        $config['per_page'] = 50;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['bookings'] = array_slice($total_rows, $offset, $config['per_page']);
        $data['escalation_reason'] = $this->vendor_model->getEscalationReason(array('entity'=>'partner', 'active'=> '1'));
               
        if ($this->session->flashdata('result') != '') {
            $data['success'] = $this->session->flashdata('result');
        }
        
        log_message('info', 'Partner View: Pending booking: Partner id: ' . $partner_id . ", Partner name: " . 
                $this->session->userdata('partner_name'));
        
        $this->load->view('partner/header');
        $this->load->view('partner/pending_booking', $data);
    }

    /**
     * @desc: this is used to load pending booking
     * @param: Offset and page no.
     * @return: void
     */
    function pending_queries($offset = 0){
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');
        $config['base_url'] = base_url() . 'partner/pending_queries';
        $total_rows = $this->partner_model->getPending_queries($partner_id);
        $config['total_rows'] = count($total_rows);

        $config['per_page'] = 50;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['bookings'] = array_slice($total_rows, $offset, $config['per_page']);

        if ($this->session->flashdata('result') != '') {
            $data['success'] = $this->session->flashdata('result');
        }
        
        log_message('info', 'Partner View: Pending query: Partner id' . $partner_id . ", Partner name: " . 
                $this->session->userdata('partner_name'));
        
        $this->load->view('partner/header');
        $this->load->view('partner/pending_queries', $data);

    }


    /**
     * @desc: this is used to display completed booking for specific service center
     */
    function closed_booking($state, $offset = 0){
       $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');

        $config['base_url'] = base_url() . 'partner/closed_booking/'.$state;
        $config['total_rows'] = $this->partner_model->getclosed_booking("count","",$partner_id, $state);

        $config['per_page'] = 50;
        $config['uri_segment'] = 4;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['bookings'] = $this->partner_model->getclosed_booking($config['per_page'], $offset, $partner_id, $state);

        if ($this->session->flashdata('result') != '')
            $data['success'] = $this->session->flashdata('result');

        $data['status'] = $state;

        log_message('info', 'Partner view '.$state.' booking  partner id' . $partner_id . " Partner name" . $this->session->userdata('partner_name'). " data ". print_r($data, true));

        $this->load->view('partner/header');
        $this->load->view('partner/closed_booking', $data);
    }

    /**
     * @desc: this is used to get booking details by using booking ID And load booking booking details view
     * @param: booking id
     * @return: void
     */
    function booking_details($booking_id) {
        $this->checkUserSession();
        $data['booking_history'] = $this->booking_model->getbooking_filter_service_center($booking_id);
        $unit_where = array('booking_id'=>$booking_id);
        $data['unit_details'] = $this->booking_model->get_unit_details($unit_where);
        
        log_message('info', 'Partner view booking details booking  partner id' . $this->session->userdata('partner_id') . " Partner name" . $this->session->userdata('partner_name'));

        $this->load->view('partner/header');
        $this->load->view('partner/booking_details', $data);
    }


    /**
     * @desc: This function Sets Session
     * @param: Partrner id
     * @param: Partner name
     * @return: void
     */
    function setSession($partner_id, $partner_name, $agent_id) {
    $userSession = array(
        'session_id' => md5(uniqid(mt_rand(), true)),
        'partner_id' => $partner_id,
        'partner_name' => $partner_name,
        'agent_id' => $agent_id,
        'sess_expiration' => 600000,
        'loggedIn' => TRUE,
        'userType' => 'partner'
    );

        $this->session->set_userdata($userSession);
    }

    /**
     * @desc: This funtion will check Session
     * @param: void
     * @return: true if details matches else session is distroyed.
     */
    function checkUserSession() {
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'partner')) {
            return TRUE;
        } else {
            $this->session->sess_destroy();
            redirect(base_url() . "partner/login");
        }
    }

    /**
     * @desc : This funtion for logout
     * @param: void
     * @return: void
     */
    function logout() {
        $this->checkUserSession();
        log_message('info', 'Partner logout  partner id' . $this->session->userdata('partner_id') . " Partner name" . $this->session->userdata('partner_name'));
        
        //Saving Logout Details in Database
        $login_data['browser'] = $this->agent->browser();
        $login_data['agent_string'] = $this->agent->agent_string();
        $login_data['ip'] = $this->session->all_userdata()['ip_address'];
        $login_data['action'] = _247AROUND_LOGOUT;
        $login_data['entity_type'] = $this->session->all_userdata()['userType'];
        $login_data['agent_id'] = $this->session->all_userdata()['agent_id'];
        $login_data['entity_id'] = $this->session->all_userdata()['partner_id'];

        $logout_id = $this->employee_model->add_login_logout_details($login_data);
        //Adding Log Details
        if ($logout_id) {
            log_message('info', __FUNCTION__ . ' Logging details have been captured for partner ' . $login_data['agent_id']);
        } else {
            log_message('info', __FUNCTION__ . ' Err in capturing logging details for partner ' . $login_data['agent_id']);
        }

        $this->session->sess_destroy();
        redirect(base_url() . "partner/login");
    }

    /**
     * @desc: This method loads abb booking form
     * it gets user details(if exist), city, source, services
     */
    function get_addbooking_form(){
        $this->checkUserSession();
        $this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|required|regex_match[/^[7-9]{1}[0-9]{9}$/]');

        if ($this->form_validation->run() == FALSE) {
            redirect(base_url()."partner/get_user_form");
        } else {
            $phone_number = $this->input->post('phone_number');
            $data = $this->booking_model->get_city_booking_source_services($phone_number);
            $data['appliances'] = $this->partner_model->get_appliances_for_partner($this->session->userdata('partner_id'));
            $this->load->view('partner/header');
            $this->load->view('partner/get_addbooking', $data);
        }
        
    }

 /**
     * @desc: This method is used to process to add booking by partner
     */
    function process_addbooking() {
        $this->checkUserSession();
        $validate = $this->set_form_validation();
        log_message('info', 'Partner initiate add booking' . $this->session->userdata('partner_name'));

        if ($validate) {

            $authToken = $this->partner_model->get_authentication_code($this->session->userdata('partner_id'));
            if ($authToken) {
                $post = $this->get_booking_form_data();

                $postData = json_encode($post, true);

                $ch = curl_init(base_url() . 'partner/insertBookingByPartner');
                curl_setopt_array($ch, array(
                    CURLOPT_POST => TRUE,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: ' . $authToken,
                        'Content-Type: application/json'
                    ),
                    CURLOPT_POSTFIELDS => $postData
                ));

                // Send the request
                $response = curl_exec($ch);
                log_message('info', ' Partner ' . $this->session->userdata('partner_name') . "  booking not Inserted error mgs" . print_r($response, true));
                // Decode the response
                $responseData = json_decode($response, TRUE);
                
                if (isset($responseData['data']['result'])) {

                    if ($responseData['data']['result'] != "Success") {
                        log_message('info', ' Partner ' . $this->session->userdata('partner_name') . "  booking not Inserted " . print_r($postData, true) . " error mgs" . print_r($responseData['data'], true));
                        $this->insertion_failure($postData);

                        $output = "Sorry, Booking could not be inserted. Please check the input and try again.";
                        $userSession = array('success' => $output);
                        $this->session->set_userdata($userSession);

                        $data = $this->booking_model->get_city_booking_source_services($this->input->post('booking_primary_contact_no'));
                        $data['appliances'] = $this->partner_model->get_appliances_for_partner($this->session->userdata('partner_id'));
                        $this->load->view('partner/header');
                        $this->load->view('partner/get_addbooking', $data);
                    } else {
                        $output = "Booking inserted successfully.";
                        $userSession = array('success' => $output);
                        $this->session->set_userdata($userSession);

                        log_message('info', 'Partner ' . $this->session->userdata('partner_name') . "  booking Inserted " . print_r($postData, true));
                        // Print the date from the response
                        //echo $responseData['data'];
                        redirect(base_url() . "partner/pending_booking");
                    }
                } else {
                    log_message('info', 'Partner ' . $this->session->userdata('partner_name') . "  booking not Inserted " . print_r($postData, true) . " error mgs" . print_r($responseData['data'], true));
                    $this->insertion_failure($postData);

                    $output = "Sorry, Booking could not be inserted. Please check the input and try again.";
                    $userSession = array('success' => $output);
                    $this->session->set_userdata($userSession);

                    $data = $this->booking_model->get_city_booking_source_services($this->input->post('booking_primary_contact_no'));
                    $data['appliances'] = $this->partner_model->get_appliances_for_partner($this->session->userdata('partner_id'));
                    $this->load->view('partner/header');
                    $this->load->view('partner/get_addbooking', $data);
                }
            } else {
                log_message('info', 'Partner ' . $this->session->userdata('partner_name') . "  Authentication failed");
                //echo "Authentication fail:";
            }
        } else {
            log_message('info', 'Partner add booking' . $this->session->userdata('partner_name') . " Validation failed ");
            $data = $this->booking_model->get_city_booking_source_services($this->input->post('booking_primary_contact_no'));
            $data['appliances'] = $this->partner_model->get_appliances_for_partner($this->session->userdata('partner_id'));
            $this->load->view('partner/header');
            $this->load->view('partner/get_addbooking', $data);
        }
    }
    
    function get_booking_form_data(){
        $booking_date = date('d-m-Y', strtotime($this->input->post('booking_date')));
        $post['partnerName'] = $this->session->userdata('partner_name');
        $post['agent_id'] = $this->session->userdata('partner_id');
        $post['name'] = $this->input->post('user_name');
        $post['mobile'] = $this->input->post('booking_primary_contact_no');
        $post['email'] = $this->input->post('user_email');
        $post['address'] = $this->input->post('booking_address');
        $post['pincode'] = $this->input->post('booking_pincode');
        $post['city'] = $this->input->post('city');
        $post['requestType'] = $this->input->post('price_tag');
        $post['landmark'] = $this->input->post('landmark');
        $post['product'] = $this->input->post('service_name');
        $post['brand'] = $this->input->post('appliance_brand');
        $post['productType'] = '';
        $post['category'] = $this->input->post('appliance_category');
        $post['capacity'] = $this->input->post('appliance_capacity');
        $post['model'] = $this->input->post('model_number');
        $post['serial_number'] = $this->input->post('serial_number');
        $post['purchase_month'] = $this->input->post('purchase_month');
        $post['purchase_year'] = $this->input->post('purchase_year');
        $post['partner_source'] = $this->input->post('partner_source');
        $post['remarks'] = $this->input->post('query_remarks');
        $post['orderID'] = $this->input->post('order_id');
        $post['alternate_phone_number'] = $this->input->post('alternate_phone_number');
        $post['booking_date'] = $booking_date;
        
        return $post;
        
    }

    function insertion_failure($post){
        $to = "anuj@247around.com, abhay@247around.com";
        $cc = "";
        $bcc = "";
        $subject = "Booking Insertion Failure By ".$this->session->userdata('partner_name');
        $message = $post;
        $this->notify->sendEmail("booking@247around.com", $to, $cc, $bcc, $subject, $message, "");

    }

    function set_form_validation(){
        $this->form_validation->set_rules('user_name', 'User Name', 'required|xss_clean');
        $this->form_validation->set_rules('booking_primary_contact_no', 'Mobile Number', 'trim|required|exact_length[10]|xss_clean');
        $this->form_validation->set_rules('city', 'City', 'trim|required|xss_clean');
        $this->form_validation->set_rules('booking_address', 'Booking Address', 'required');
        $this->form_validation->set_rules('landmark', 'LandMark', 'trim');
        $this->form_validation->set_rules('appliance_capacity', 'Appliance Capacity', 'trim|required|xss_clean');
        $this->form_validation->set_rules('alternate_phone_number', 'Alternate Number', 'trim|xss_clean');
        $this->form_validation->set_rules('purchase_year', 'Purchase Year', 'trim|xss_clean');
        $this->form_validation->set_rules('purchase_month', 'Purchase Month', 'trim|xss_clean');
        $this->form_validation->set_rules('model_number', 'Model Number', 'trim|xss_clean');
        $this->form_validation->set_rules('order_id', 'Order ID', 'trim|xss_clean');
        $this->form_validation->set_rules('serial_number', 'Serial Number', 'trim|xss_clean');
        $this->form_validation->set_rules('appliance_category', 'Appliance Category', 'required');
        $this->form_validation->set_rules('partner_source', 'Booking Source', 'required');
        $this->form_validation->set_rules('service_name', 'Service Name', 'required');
        $this->form_validation->set_rules('booking_date', 'Booking Date', 'required');
        $this->form_validation->set_rules('query_remarks', 'Problem Description', 'required');
        $this->form_validation->set_rules('booking_pincode', 'Booking Pincode', 'trim|required|exact_length[6]');
        $this->form_validation->set_rules('price_tag', 'Call Type', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            return FALSE;
        }
        else {
            return true;
        }
    }

    /**
     * @desc: This function is used to edit/add partner
     *
     * @param: void
     * @return : array(result) to view
     */
    function get_add_partner_form() {

        $results['services'] = $this->vendor_model->selectservice();
        $results['brands'] = $this->vendor_model->selectbrand();
        $results['select_state'] = $this->vendor_model->getall_state();
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/addpartner', array('results' => $results));
    }

    /**
     * @desc : This function is used to Add/Edit Partner details.
     * @call : This function is called on Form Submit for Add/Edit Partner details.
     * Partner details like- partner's name, owner's name, phone no., email, POC(point of contact) details
     *      are added/edited.
     *
     * @param : void
     * @return : void
     */
    function process_add_edit_partner_form(){
        //Check form validation
        $checkValidation = $this->check_partner_Validation();
        if ($checkValidation) {
            // Used when we edit a particular Partner
            if (!empty($this->input->post('id'))) {
                //if vendor exists, details are edited
                $partner_id = $this->input->post('id');
                
                //Getting partner operation regions details from POST
                $partner_operation_state = $this->input->post('select_state');
                unset($_POST['select_state']);
                
                //Getting Brands Details
                $partner_service_brand = $this->input->post('select_brands');
                unset($_POST['select_brands']);
               
                //Getting Login Details
                $login['user_name'] = $this->input->post('username');
                $login['password'] = md5($this->input->post('password'));
                $login['clear_text'] = $this->input->post('password');
                
                //Unsetting Username and Password
                unset($_POST['username']);
                unset($_POST['password']);
                
                //Editing User Login Details
                $where = array('partner_id' =>$partner_id);
                $update_login = $this->partner_model->update_partner_login_details($login,$where);
                
                //Updating Partner Operation Region
                //Processing Partner Operation Region
                    if (!empty($partner_operation_state)) {
                        $all_flag = FALSE;
                        foreach ($partner_operation_state as $key => $value) {
                            foreach($value as $val){
                                //Checking if ALL state has been selected
                                if($val == 'all'){
                                    $all_states = $this->vendor_model->getall_state();
                                    foreach ($all_states as $value) {
                                        $data['partner_id'] = $partner_id;
                                        $data['service_id'] = $key;
                                        $data['state'] = $value['state'];
                                        $data['active'] = 1;
                                        $data_final[] = $data;
                                    }
                                    break;
                                }
                                $data['partner_id'] = $partner_id;
                                $data['service_id'] = $key;
                                $data['state'] = $val;
                                $data['active'] = 1;
                                $data_final[] = $data;
                            }
                        }
                        
                        //Deleting Previous Values
                        $this->partner_model->delete_partner_operation_region($partner_id);
                        
                        //Inserting Array in batch in partner operation region
                        $operation_update_flag = $this->partner_model->insert_batch_partner_operation_region($data_final,$where);
                        if ($operation_update_flag) {
                            //Loggin Success
                            log_message('info', 'Parnter Operation Region has been added sucessfully for partner ' . print_r($partner_id));
                        }
                    } else {
                        //Echoing message in Log file
                        log_message('error', __FUNCTION__ . ' No Input provided for Partner Operation Region Relation  ');
                    }
                
                
                //Updating Partner Brands Details
                    
                    if (!empty($partner_service_brand)) {
                        foreach ($partner_service_brand as $key => $value) {
                            foreach($value as $val){
                                $data_brands['partner_id'] = $partner_id;
                                $data_brands['service_id'] = $key;
                                $data_brands['brand'] = $val;
                                $data_brands['active'] = 1;
                                $data_final_brands[] = $data_brands;
                            }
                        }
                        //Deleting Previous Values
                        $this->partner_model->delete_partner_brand_relation($partner_id);
                        
                        //Inserting Array in batch in partner brand relation
                        $operation_update_brand_flag = $this->partner_model->insert_batch_partner_brand_relation($data_final_brands);
                        if ($operation_update_brand_flag) {
                            //Loggin Success
                            log_message('info', 'Parnter Brand Relation has been added sucessfully for partner ' . print_r($partner_id));
                        }
                    } else {
                        //Echoing message in Log file
                        log_message('error', __FUNCTION__ . ' No Input provided for Partner Brand Relation  ');
                    }
                
                $this->partner_model->edit_partner($this->input->post(), $partner_id);

                redirect(base_url() . 'employee/partner/viewpartner', 'refresh');
            }else{
                //If Partner not present, Partner is being added
                $_POST['is_active'] = '1';
                //Temporary value
                $_POST['auth_token'] = rand(1,100);
                
                //Getting partner operation regions details from POST
                $partner_operation_state = $this->input->post('select_state');
                unset($_POST['select_state']);
                
                //Getting Brands Details
                $partner_service_brand = $this->input->post('select_brands');
                unset($_POST['select_brands']);
               
                //Getting Login Details
                $login['user_name'] = $this->input->post('username');
                $login['password'] = md5($this->input->post('password'));
                $login['clear_text'] = $this->input->post('password');
                
                //Unsetting Username and Password
                unset($_POST['username']);
                unset($_POST['password']);
                
                
                //Sending POST array to Model
                $partner_id = $this->partner_model->add_partner($this->input->post());
                //Set Flashdata on success or on Error of Data insert in table
                if(!empty($partner_id)){
                    $this->session->set_flashdata('success','Partner added successfully.');

                    //Echoing inserted ID in Log file
                    log_message('info',__FUNCTION__.' New Partner has been added with ID '.  $partner_id." Done By " . $this->session->userdata('employee_id'));
                    
                     //Processing Inputs for Partner Login Username and Password
                    
                    $login['partner_id'] = $partner_id;
                    $login['full_name'] = $this->input->post('primary_contact_name');
                    $login['active'] = 1; 
                    
                    $login_details = $this->partner_model->add_partner_login($login);
                    if($login_details){
                        log_message('info',' Parnter Login Details has been addded '.print_r($login_details,TRUE));
                    }else{
                        log_message('info',' Error in Parnter Login Details has been addded '.print_r($login_details,TRUE));
                    }
                
                
                    
                    //Processing Partner Operation Region
                    if (!empty($partner_operation_state)) {
                        $all_flag = FALSE;
                        foreach ($partner_operation_state as $key => $value) {
                            foreach($value as $val){
                                //Checking if ALL state has been selected
                                if($val == 'all'){
                                    $all_states = $this->vendor_model->getall_state();
                                    foreach ($all_states as $value) {
                                        $data['partner_id'] = $partner_id;
                                        $data['service_id'] = $key;
                                        $data['state'] = $value['state'];
                                        $data['active'] = 1;
                                        $data_final[] = $data;
                                    }
                                    break;
                                }
                                $data['partner_id'] = $partner_id;
                                $data['service_id'] = $key;
                                $data['state'] = $val;
                                $data['active'] = 1;
                                $data_final[] = $data;
                            }
                        }
                        
                        //Inserting Array in batch in partner operation region
                        $operation_insert_flag = $this->partner_model->insert_batch_partner_operation_region($data_final);
                        if ($operation_insert_flag) {
                            //Loggin Success
                            log_message('info', 'Parnter Operation Region has been added sucessfully for partner ' . print_r($partner_id));
                        }
                    } else {
                        //Echoing message in Log file
                        log_message('error', __FUNCTION__ . ' No Input provided for Partner Operation Region Relation  ');
                    }

                    // Processing Partner Brands Relation
                    
                    if (!empty($partner_service_brand)) {
                        foreach ($partner_service_brand as $key => $value) {
                            foreach($value as $val){
                                $data_brands['partner_id'] = $partner_id;
                                $data_brands['service_id'] = $key;
                                $data_brands['brand'] = $val;
                                $data_brands['active'] = 1;
                                $data_final_brands[] = $data_brands;
                            }
                        }
                        //Inserting Array in batch in partner brand relation
                        $operation_insert_brand_flag = $this->partner_model->insert_batch_partner_brand_relation($data_final_brands);
                        if ($operation_insert_brand_flag) {
                            //Loggin Success
                            log_message('info', 'Parnter Brand Relation has been added sucessfully for partner ' . print_r($partner_id));
                        }
                    } else {
                        //Echoing message in Log file
                        log_message('error', __FUNCTION__ . ' No Input provided for Partner Brand Relation  ');
                    }
                    
                }else{
                    $this->session->set_flashdata('error','Error in adding Partner.');

                    //Echoing message in Log file
                    log_message('error',__FUNCTION__.' Error in adding Partner  '. print_r($this->input->post(),TRUE));
                }
                
                
                
           redirect(base_url() . 'employee/partner/get_add_partner_form');
            }
        } else {
            $this->get_add_partner_form();
        }

    }

    /**
     * @desc: This function is used to check validation of Add/Edit Partner form
     *
     * @param: void
     * @return : If validation ok returns true else false
     */
    function check_partner_Validation() {
        $this->form_validation->set_rules('company_name', 'Company Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('public_name', 'Public Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('address', 'Partner Address', 'trim|required');
        $this->form_validation->set_rules('state', 'State', 'trim|required');
        $this->form_validation->set_rules('district', 'District', 'trim|required');
        return $this->form_validation->run(); 
    }

    /**
     * @desc: This function is to view Partner's list
     *
     * Will display all the details of a particular partner
     *
     * @param: vendor id
     * @return : array(of details) to view
     */
    function viewpartner($partner_id = "") {
        $data = [];
        $query = $this->partner_model->viewpartner($partner_id);
        
        foreach($query as $value){
            //Getting Appliances and Brands details for partner
            $service_brands[] = $this->partner_model->get_service_brands_for_partner($value['id']);
            
            $login = $this->partner_model->get_partner_login_details($value['id']);
            if (!empty($login)) {
                foreach ($login as $val) {
                    if ($val['clear_text'] != '') {
                        $value['user_name'] = $val['user_name'];
                        $value['clear_text'] = $val['clear_text'];
                        break;
                    } else {
                        $value['user_name'] = '';
                        $value['clear_text'] = '';
                    }
                }
            } else {
                $value['user_name'] = '';
                $value['clear_text'] = '';
            }
            $data[] = $value;
        }
        
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));

        $this->load->view('employee/viewpartner', array('query' => $data,'service_brands' =>$service_brands));
    }

    /**
     * @desc: This function is to activate a particular partner
     *
     * For this the partner must be already registered with us and should be non-active(is_active = 0)
     *
     * @param: partner id
     * @return : void
     */
    function activate($id) {
        $this->partner_model->activate($id);
        //Storing State change values in Booking_State_Change Table
        $this->notify->insert_state_change('', _247AROUND_PARTNER_ACTIVATED, _247AROUND_PARTNER_DEACTIVATED, 'Partner ID = '.$id, $this->session->userdata('id'), $this->session->userdata('employee_id'),_247AROUND);
        redirect(base_url() . 'employee/partner/viewpartner', 'refresh');
    }

    /**
     * @desc: This function is to deactivate a particular partner
     *
     * For this the partner must be already registered with us and should be active(is_active = 1)
     *
     * @param: partner id
     * @return : void
     */
    function deactivate($id) {
        $this->partner_model->deactivate($id);
        //Storing State change values in Booking_State_Change Table
        $this->notify->insert_state_change('', _247AROUND_PARTNER_DEACTIVATED, _247AROUND_PARTNER_ACTIVATED, 'Partner ID = '.$id, $this->session->userdata('id'), $this->session->userdata('employee_id'),_247AROUND);
        redirect(base_url() . 'employee/partner/viewpartner', 'refresh');
    }

    /**
     * @desc: This function is to edit partner's details
     *
     * Existing details will be be displayed in respective fields(allowed to edit)
     *      and rest of the fields will be displayed blank.
     *
     * @param: partner id
     * @return : array(of details) to view
     */
    function editpartner($id) {
        $query = $this->partner_model->viewpartner($id);
        $results['select_state'] = $this->vendor_model->getall_state();
        $results['services'] = $this->vendor_model->selectservice();
        $results['brands'] = $this->vendor_model->selectbrand();
        //Getting Login Details for this partner
        $results['login_details'] = $this->partner_model->get_partner_login_details($id);
        //Getting Parnter Operation Region Details
        $where = array('partner_id' => $id);
        $results['partner_operation_region'] = $this->partner_model->get_partner_operation_region($where);
        //Getting Partner Brands Relation from partner_service_brand_relation
        $results['partner_brands'] = $this->partner_model->get_partner_service_brand_relation($where);
        
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/addpartner', array('query' => $query, 'results' => $results));
    }

    /**
     * @desc: This is used to get find user form in Partner CRM
     * params: void
     * return: View form to find user
     */
    function get_user_form() {
        $this->checkUserSession();
        $this->load->view('partner/header');
        $this->load->view('partner/finduser');
    }

    /**
     * @desc : This function is to find/search user in Partner CRM
     *
     * Searches user details with booking id, order id/serial number and phone number
     *
     * Complete or partial detail entered to search will show all the matching users/bookings in a list,
     *      from which we can select the required one by looking at other details shown.
     *
     * @param: offset, per page number and phone number
     * @return : print Booking on Booking Page
     */
    
     function finduser($offset = 0, $page = 0, $phone_number = '') {
        $this->checkUserSession();
        $booking_id = $this->input->post('booking_id');
        $order_id = $this->input->post('order_id');
        $serial_no = $this->input->post('serial_number');
        $partner_id = $this->session->userdata('partner_id');
        if ($this->input->post('phone_number')) {
            $phone_number = $this->input->post('phone_number');
        }
        
        if ($phone_number != "") {
            $page = 0;
                
            if ($page == 0) {
                $page = 50;
            }

            $offset = ($this->uri->segment(5) != '' ? $this->uri->segment(5) : 0);

            $config['base_url'] = base_url() . "employee/partner/finduser/" . $offset . "/" . $page . "/" . $phone_number;
           
            $output_data = $this->user_model->search_by_partner($phone_number, $partner_id, $offset, $page);
            if(!empty($output_data)){
                $config['per_page'] = $page;
                $config['uri_segment'] = 7;
                $config['first_link'] = 'First';
                $config['last_link'] = 'Last';

                $this->pagination->initialize($config);
                $data['links'] = $this->pagination->create_links();

                $data['data'] = $output_data;
                $this->load->view('partner/header');
                $this->load->view('partner/bookinghistory', $data);
            } else {
                $this->session->set_flashdata('error', 'User Not Exist');

                redirect(base_url() . 'employee/partner/get_user_form');
            }

        } else if ($booking_id != "") {  //if booking id given and matched, will be displayed
            $where = array('booking_details.booking_id' => $booking_id);
            $data['Bookings'] = $this->booking_model->search_bookings($where,$partner_id);
          
            $this->load->view('partner/header');
            $this->load->view('partner/search_result',$data);
            
        } else if(!empty($order_id)) {

            $where = array('order_id' => $order_id);
            $data['Bookings'] = $this->booking_model->search_bookings($where, $partner_id);
            
            $this->load->view('partner/header');
            $this->load->view('partner/search_result',$data);
        } else if (!empty($serial_no)) {

            $where = array('partner_serial_number' => $serial_no);
            $data['Bookings'] = $this->booking_model->search_bookings($where, $partner_id);
           
            $data['search'] = "Search";
            $this->load->view('partner/header');
            $this->load->view('partner/search_result',$data);
        } else {
            $this->session->set_flashdata('error', 'User Not Exist');
            redirect(base_url() . 'employee/partner/get_user_form');
        }
        
    }

    /**
     * @desc: get invoice details and bank transacton details to display in partner invoice view
     * Get partner Id from session.
     */
    function invoices_details() {
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');
        $data['vendor_partner'] = "partner";
        $data['vendor_partner_id'] = $partner_id;
        $invoice['invoice_array'] = $this->invoices_model->getInvoicingData($data);

        $data2['partner_vendor'] = "partner";
        $data2['partner_vendor_id'] = $partner_id;
        $invoice['bank_statement'] = $this->invoices_model->get_bank_transactions_details($data2);
        $this->load->view('partner/header');
        $this->load->view('partner/invoice_summary', $invoice);
    }

    /**
     *  @desc : This function is to select booking/Query to be canceled.
     *
     * If $status is followup means it Query and its load internal status
     *
     * Opens a form with user's name and option to be choosen to cancel the booking.
     *
     * Atleast one booking/Query cancellation reasbon must be selected.
     *
     * If others option is choosen, then the cancellation reason must be entered in the textarea.
     *
     *  @param : booking id
     *  @return : user details and booking history to view
     */
    function get_cancel_form($status, $booking_id) {
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . " Booking ID: " . $booking_id);
        $data['user_and_booking_details'] = $this->booking_model->getbooking_history($booking_id);
        if (!empty($data['user_and_booking_details'])) {
            $where = array('reason_of' => 'partner');
            $data['reason'] = $this->booking_model->cancelreason($where);
            $data['status'] = $status;
            $this->load->view('partner/header');
            $this->load->view('partner/cancel_form', $data);
        } else {
            echo "Booking Id is not exist";
        }
    }

    /**
     *  @desc : This function is to cancels the booking/Query
     *
     * Accepts the cancellation reason provided in cancel booking/Query form and then cancels booking with the reason provided.
     *
     *  @param : booking id
     *  @return : cancels the booking and load view
     */
    function process_cancel_form($booking_id, $status) {
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . " Booking ID: " . print_r($booking_id, true));
        $data['cancellation_reason'] = $this->input->post('cancellation_reason');
        $data['closed_date'] = $data['update_date'] = date("Y-m-d H:i:s");
        $data['current_status'] = $data['internal_status'] = _247AROUND_CANCELLED;
        $update_status = $this->booking_model->update_booking($booking_id, $data);
        if ($update_status) {
            //Update in booking uunit details
            $this->update_price_while_cancel_booking($booking_id);
            $booking_data = $this->booking_model->getbooking_history($booking_id);
            // Update in service center action table is booking is assigned
            if (!is_null($booking_data[0]['assigned_vendor_id'])) {

                $data_vendor['cancellation_reason'] = $data['cancellation_reason'];
                //Update this booking in vendor action table
                $data_vendor['update_date'] = date("Y-m-d H:i:s");
                $data_vendor['current_status'] = $data_vendor['internal_status'] = _247AROUND_CANCELLED;
               
                $this->vendor_model->update_service_center_action($booking_id, $data_vendor);
            }

            //Log this state change as well for this booking
            //param:-- booking id, new state, old state, remarks, agent_id, partner  name, partner id
            $this->notify->insert_state_change($booking_id, $data['current_status'],
                    $status, $data['cancellation_reason'], 
                    $this->session->userdata('agent_id'),
                    $this->session->userdata('partner_name'),
                    $this->session->userdata('partner_id'));

            // this is used to send email or sms while booking cancelled
            $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
            $send['booking_id'] = $booking_id;
            $send['state'] = $data['current_status'];
            $this->asynchronous_lib->do_background_process($url, $send);
            $this->session->set_flashdata('success', $booking_id . ' Booking Cancelled');

            redirect(base_url() . "partner/get_user_form");
        } else {
            // Booking isnot updated
            log_message('info', __FUNCTION__ . " Booking is not updated  " . print_r($data, true));
        }
    }

    /**
     * @desc: This method calls for cancel booking to update booking unit details
     * @param: String $booking_id
     */
    function update_price_while_cancel_booking($booking_id) {
        log_message('info', __FUNCTION__ . " Booking Id  " . print_r($booking_id, true));
        $unit_details['booking_status'] = "Cancelled";
        $unit_details['vendor_to_around'] = $unit_details['around_to_vendor'] = 0;
        log_message('info', __FUNCTION__ . " Update unit details  " . print_r($unit_details, true));
        $this->booking_model->update_booking_unit_details($booking_id, $unit_details);
    }

    /**
     *  @desc : This function is to select booking to be rescheduled
     *
     * Opens a form with user's name and current date and timeslot.
     *
     * Select the new date and timeslot for current booking.
     *
     *  @param : booking id
     *  @return : user details and booking history to view
     */
    function get_reschedule_booking_form($booking_id) {
        log_message('info', __FUNCTION__ . " Booking Id  " . print_r($booking_id, true));
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . " Booking Id  " . $booking_id);
        $getbooking = $this->booking_model->getbooking_history($booking_id);
        if ($getbooking) {

            $this->load->view('partner/header');
            $this->load->view('partner/reschedulebooking', array('data' => $getbooking));
        } else {
            echo "This Id doesn't Exists";
        }
    }

    function process_reschedule_booking($booking_id) {
        log_message('info', __FUNCTION__ . " Booking Id  " . print_r($booking_id, true));
        $this->checkUserSession();
        $this->form_validation->set_rules('booking_date', 'Booking Date', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->get_reschedule_booking_form($booking_id);
        } else {
            log_message('info', __FUNCTION__ . " Booking Id  " . $booking_id);
            $booking_date = $this->input->post('booking_date');

            $data['booking_date'] = date('d-m-Y', strtotime($booking_date));
            $data['current_status'] = 'Rescheduled';
            $data['internal_status'] = 'Rescheduled';
            $data['update_date'] = date("Y-m-d H:i:s");
            $update_status = $this->booking_model->update_booking($booking_id, $data);
            if ($update_status) {
                $this->notify->insert_state_change($booking_id,
                        _247AROUND_RESCHEDULED,
                        _247AROUND_PENDING, 
                        " Rescheduled Booking BY Partner ",
                        $this->session->userdata('agent_id'), 
                        $this->session->userdata('partner_name'), 
                        $this->session->userdata('partner_id'));

               
                $service_center_data['internal_status'] = "Pending";
                $service_center_data['current_status'] = "Pending";
                $service_center_data['update_date'] = date("Y-m-d H:i:s");

                log_message('info', __FUNCTION__ . " Update Service center action table  " . print_r($service_center_data, true));

                $this->vendor_model->update_service_center_action($booking_id, $service_center_data);
               
                $send_data['booking_id'] = $booking_id;
                $send_data['current_status'] = "Rescheduled";
                $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
                $this->asynchronous_lib->do_background_process($url, $send_data);
                log_message('info', __FUNCTION__ . " Set mail to vendor flag to 0  " . print_r($booking_id, true));

                //Setting mail to vendor flag to 0, once booking is rescheduled
                $this->booking_model->set_mail_to_vendor_flag_to_zero($booking_id);
                log_message('info', __FUNCTION__ . " Request to prepare Job Card  " . print_r($booking_id, true));

                //Prepare job card
                $this->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);

                $this->session->set_flashdata('success', $booking_id . ' Booking Rescheduled');
                redirect(base_url() . "partner/get_user_form");
            } else {
                log_message('info', __FUNCTION__ . " Booking is not updated  " . print_r($data, true));
            }
        }
    }
    /**
     * @desc: Load escalation form  in the partner panel. Partner esclates on booking.
     * That will send notification to 247Around.
     * @param String $booking_id
     */
    function escalation_form($booking_id){
        log_message('info', __FUNCTION__ . " Booking Id  " . print_r($booking_id, true));
        $this->checkUserSession();
        $data['escalation_reason'] = $this->vendor_model->getEscalationReason(array('entity'=>'partner', 'active'=> '1'));
        $data['booking_id'] = $booking_id;

        $this->load->view('partner/header');
        $this->load->view('partner/escalation_form', $data);
    }
    /**
     * @desc: This is used to insert escalation into escalation log table. 
     * Upadte escalation log table when mail sent
     * @param String $booking_id
     */
    function process_escalation($booking_id){
        
        $this->checkUserSession();
        $this->form_validation->set_rules('escalation_reason_id', 'Escalation Reason', 'trim|required');
        
        if ($this->form_validation->run() == FALSE) {
            $this->escalation_form($booking_id);
        } else {
            
            $escalation['escalation_reason'] = $this->input->post('escalation_reason_id');
            $escalation_remarks = $this->input->post('escalation_remarks');
            $bookinghistory = $this->booking_model->getbooking_history($booking_id);
           
            $escalation['booking_id'] = $booking_id;
            if(!is_null($bookinghistory[0]['assigned_vendor_id'])){
                $escalation['vendor_id'] = $bookinghistory[0]['assigned_vendor_id'];
                $vendorContact = $this->vendor_model->getVendorContact($escalation['vendor_id']);
                $to = $vendorContact[0]['primary_contact_email'];
                $cc = $vendorContact[0]['owner_email'].",nits@247around.com,escalations@247around.com";
                
                $message = "Booking " . $booking_id . " Escalated By Partner " . $this->session->userdata('partner_name'). " SF State ". 
                        $vendorContact[0]['state']. " SF City ". $vendorContact[0]['city'];
                
            } else {
                $escalation['vendor_id'] = "";
                $to = "escalations@247around.com"; 
                $cc = "nits@247around.com";
                $message = "Booking " . $booking_id . " Escalated By Partner " . $this->session->userdata('partner_name'). " SF State ";
            }
            
            $escalation['booking_date'] = date('Y-m-d', strtotime($bookinghistory[0]['booking_date']));
            $escalation['booking_time'] = $bookinghistory[0]['booking_timeslot'];
            
            log_message('info', __FUNCTION__ . " escalation_reason  " . print_r($escalation, true));
          
            //inserts vendor escalation details
            $escalation_id = $this->vendor_model->insertVendorEscalationDetails($escalation);
            $escalation_reason  = $this->vendor_model->getEscalationReason(array('id'=>$escalation['escalation_reason']));
            if(!empty($escalation_remarks)){
                $remarks = $escalation_reason[0]['escalation_reason']." -".
                    $escalation_remarks;
            } else {
                $remarks = $escalation_reason[0]['escalation_reason'];
            }
            $this->notify->insert_state_change($escalation['booking_id'], 
                    "Escalation" , _247AROUND_PENDING , $remarks, 
                    $this->session->userdata('agent_id'), $this->session->userdata('partner_name'),
                    $this->session->userdata('partner_id'));
            if($escalation_id){
                log_message('info', __FUNCTION__ . " Escalation Inserted ");
                $this->booking_model->increase_escalation_reschedule($booking_id, "count_escalation");
                $from = "escalations@247around.com";
                $bcc=""; $attachment = "";
                
                $subject = "Booking " . $booking_id . " Escalated By Partner " . $this->session->userdata('partner_name');

                $is_mail = $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment);
                $partner_details = $this->partner_model->getpartner($this->session->userdata('partner_id'))[0];
                $partner_mail_to = $partner_details['primary_contact_email'];
                $partner_mail_cc = "nits@247around.com,escalations@247around.com";
                $partner_subject = "Booking " . $booking_id . " Escalated ";
                $partner_message = "Booking " . $booking_id . " Escalated" ;
                $this->notify->sendEmail($from, $partner_mail_to, $partner_mail_cc, $bcc, $partner_subject, $partner_message, $attachment);
                
                if($is_mail){
                    log_message('info', __FUNCTION__ . " Escalation Mail Sent ");
                    
                    $reason_flag['escalation_policy_flag'] = json_encode(array('mail_to_escalation_team'=>1), true);

                    $this->vendor_model->update_esclation_policy_flag($escalation_id, $reason_flag, $booking_id);
                    
                }
            }
            
            log_message('info', __FUNCTION__ . " Exiting");
            
            $this->session->set_flashdata('success', 'Booking '. $booking_id. " has been escalated, our team will look into this immediately.");

          //  redirect(base_url() . "partner/escalation_form/".$booking_id);
       }
        
    }
    /**
     * @desc: This is used to load update booking form
     * @param String $booking_id
     */ 
    function get_editbooking_form($booking_id){
        log_message('info', __FUNCTION__ . " Booking Id: " . $booking_id);
        $this->checkUserSession();

        $booking_history = $this->booking_model->getbooking_history($booking_id);
        if(!empty($booking_history)){
            $data = $this->booking_model->get_city_booking_source_services($booking_history[0]['booking_primary_contact_no']);
            $data['booking_history'] = $booking_history;
            $unit_where = array('booking_id'=>$booking_id);
            $data['unit_details'] = $this->booking_model->get_unit_details($unit_where);
            $this->load->view('partner/header');
            $this->load->view('partner/edit_booking', $data);

        } else {
            echo "Booking Not Find";
        }
        
    }
    /**
     * @desc: This method is used to upade booking by Partner Panel
     * @param String $booking_id
     */
    function process_editbooking($booking_id){
        log_message('info', __FUNCTION__ . " Booking Id: " . $booking_id);
        $this->checkUserSession();
        $validate = $this->set_form_validation();
        log_message('info', 'Partner initiate Edit booking' . $this->session->userdata('partner_name'));
       // $authToken = $this->partner_model->get_authentication_code($this->session->userdata('partner_id'));
      
        if ($validate == true && !empty($booking_id)) {
            log_message('info', 'Edit booking validation true' . $this->session->userdata('partner_name'));
            $post = $this->get_booking_form_data();
            $user['name'] = $post['name'];
            $user['phone_number'] = $post['mobile'];
            $user['user_email'] = $post['email'];
            $user['city'] = $post['city'];
            $user['pincode'] = $post['pincode'];
            $user['home_address'] = $post['address'];
            $user['alternate_phone_number'] = $post['alternate_phone_number'];
            $state = $this->vendor_model->get_state_from_pincode($post['pincode']);

            $user['state'] = $state['state'];
            $booking_details['booking_date'] = $post['booking_date'];
            $booking_details['booking_primary_contact_no'] = $post['mobile'];
            $booking_details['booking_alternate_contact_no'] = $post['alternate_phone_number'];
            $booking_details['booking_address'] = $post['address'];
            $booking_details['booking_pincode'] = $post['pincode'];
            $booking_details['state'] = $state['state'];
            $booking_details['city'] = $post['city'];
            $booking_details['request_type'] = $post['requestType'];
            $booking_details['booking_landmark'] = $post['landmark'];
            $booking_details['partner_source'] = $post['partner_source'];
            $booking_details['order_id'] = $post['orderID'];
            $booking_details['service_id'] =  $this->booking_model->getServiceId($post['product']);
            $booking_details['booking_remarks'] =  $post['remarks'];

            $unit_details['price_tags'] = $post['requestType'];
            $unit_details['service_id'] = $appliance_details['service_id'] = $booking_details['service_id'];
            $unit_details['appliance_brand'] = $appliance_details['brand'] = $post['brand'];
            $unit_details['appliance_description'] = $appliance_details['description'] = $post['productType'];
            $unit_details['appliance_category'] =  $appliance_details['category'] = $post['category'];
            $unit_details['appliance_capacity'] = $appliance_details['capacity'] = $post['capacity'];
            $unit_details['model_number'] = $appliance_details['model_number'] =  $post['model'];
            $unit_details['partner_serial_number'] = $appliance_details['serial_number'] =  $post['serial_number'];
            $unit_details['purchase_month'] = $appliance_details['purchase_month'] = $post['purchase_month'];
            $unit_details['purchase_year'] = $appliance_details['purchase_year'] = $post['purchase_year'];
            // Update booking details table
            $update_status = $this->booking_model->update_booking($booking_id, $booking_details);
            if($update_status){
                $user['user_id'] = $this->input->post('user_id');
                // Update users Table
                $user_status = $this->user_model->edit_user($user);
                if($user_status){} else {
                    log_message('info', 'User table is not updated booking Id: '. $booking_id . " User Id". print_r($user, true) );
                    
                }
                $unit_details['appliance_id'] = $this->input->post('appliance_id');
                //Update appliance_details table
                $appliance_status = $this->booking_model->update_appliances($unit_details['appliance_id'],$appliance_details );
                if($appliance_status){} else {
                    log_message('info', 'Appliance is not update in Appliance details: '. $booking_id . " Appliance data". print_r($appliance_details, true). "Appliamce id ". $unit_details['appliance_id'] );
                }
                //Update Booking unit details table
                $unit_details_status = $this->booking_model->update_booking_unit_details($booking_id, $unit_details);
                if($unit_details_status){
                    $partner_id = $this->session->userdata('partner_id');
                    //Get Partner Price Mapping Id
                    $partner_mapping_id = $this->booking_model->get_price_mapping_partner_code("", $partner_id);
                    // Get Price details Array
                    $prices = $this->partner_model->getPrices($booking_details['service_id'], $unit_details['appliance_category'], $unit_details['appliance_capacity'], $partner_mapping_id, $unit_details['price_tags']);

                    $unit_details['id'] =  $prices[0]['id'];
                    $unit_details['around_paid_basic_charges'] =  $unit_details['around_net_payable'] = "0.00";
                    $unit_details['partner_paid_basic_charges'] = $prices[0]['partner_net_payable'];
                    $unit_details['partner_net_payable'] = $prices[0]['partner_net_payable'];
                    //Update price in unit details table
                    $unit_status = $this->booking_model->update_booking_in_booking_details($unit_details, $booking_id, $booking_details['state']);
                    if($unit_status) {} else {
                        log_message('info', 'Booking unit details data is not update in : '. $booking_id . " Appliance data". print_r($unit_details, true) );
                    }
                    
                    $this->notify->insert_state_change($booking_id, 
                    _247AROUND_PENDING , _247AROUND_PENDING ,  $booking_details['booking_remarks'], 
                    $this->session->userdata('agent_id'), $this->session->userdata('partner_name'),
                    $this->session->userdata('partner_id'));
                    
                    $this->session->set_flashdata('success', $booking_id . ' Booking  is updated.');
                    redirect(base_url() . "partner/get_user_form");
                }
                $this->session->set_flashdata('error', $booking_id . ' Booking  is not updated.');
                $this->get_editbooking_form($booking_id); 
            }
            $this->session->set_flashdata('error', $booking_id . ' Booking  is not updated.');
            $this->get_editbooking_form($booking_id); 
            
            
        } else {
            $this->get_editbooking_form($booking_id); 
        }
        
    }
    
    /**
     * @desc: This is used to get those booking who has requested to spare parts by SF
     */
    function get_spare_parts_booking($offset = 0){
        log_message('info', __FUNCTION__ ." Pratner ID: ".  $this->session->userdata('partner_id'));
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');
        $where = "spare_parts_details.partner_id = '".$partner_id."' AND status = '".SPARE_PARTS_REQUESTED."' "
                . " AND booking_details.current_status IN ('Pending', 'Rescheduled') ";
        
        $config['base_url'] = base_url() . 'partner/get_spare_parts_booking';
        $total_rows = $this->partner_model->get_spare_parts_booking_list($where, false, false, false);
        $config['total_rows'] = $total_rows[0]['total_rows'];

        $config['per_page'] = 50;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['spare_parts'] = $this->partner_model->get_spare_parts_booking_list($where, $offset, $config['per_page'], true);
        
        $this->load->view('partner/header');
        $this->load->view('partner/spare_parts_booking', $data);
    }
   
    /**
     * @desc: This is used to insert details into insert change table
     * @param String $booking_id
     * @param String $new_state
     * @param String $remarks
     */
    function insert_details_in_state_change($booking_id, $new_state, $remarks){
           log_message('info', __FUNCTION__ ." Pratner ID: ".  $this->session->userdata('partner_id'). " Booking ID: ". $booking_id);
           //Save state change
            $state_change['booking_id'] = $booking_id;
            $state_change['new_state'] =  $new_state;
           
            $booking_state_change = $this->booking_model->get_booking_state_change($state_change['booking_id']);
            
            if ($booking_state_change > 0) {
                $state_change['old_state'] = $booking_state_change[count($booking_state_change) - 1]['new_state'];
            } else { //count($booking_state_change)
                $state_change['old_state'] = "Pending";
            }
            $state_change['agent_id'] = $this->session->userdata('agent_id');
            $state_change['partner_id'] = $this->session->userdata('partner_id');
            $state_change['remarks'] = $remarks;

            // Insert data into booking state change
            $state_change_id = $this->booking_model->insert_booking_state_change($state_change);
            if($state_change_id){} else {
                log_message('info', __FUNCTION__ . '=> Booking details is not inserted into state change '. print_r($state_change, true));
            }
    }
    
    /**
     * @desc: This method is used to load update form(spare parts).
     * @param String $booking_id
     */
    function update_spare_parts_form($booking_id){
        log_message('info', __FUNCTION__ ." Pratner ID: ".  $this->session->userdata('partner_id'). " Booking ID: ". $booking_id);
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');
        
        $where = "spare_parts_details.partner_id = '".$partner_id."' AND status = '".SPARE_PARTS_REQUESTED."' "
               . " AND spare_parts_details.booking_id = '".$booking_id."' "
               . " AND booking_details.current_status IN ('Pending', 'Rescheduled') ";
        $data['spare_parts'] = $this->partner_model->get_spare_parts_booking($where);
        
        $this->load->view('partner/header');
        $this->load->view('partner/update_spare_parts_form', $data);
    }
    
    /**
     * @desc: This method is used to update spare parts. If gets input from form.
     * Insert data into booking state change and update sc action table
     * @param String $booking_id
     */
    function process_update_spare_parts($booking_id){
        log_message('info', __FUNCTION__ ." Pratner ID: ".  $this->session->userdata('partner_id'));
        $this->checkUserSession();
        $this->form_validation->set_rules('shipped_parts_name', 'Parts Name', 'trim|required');
        $this->form_validation->set_rules('remarks_by_partner', 'Remarks', 'trim|required');
        $this->form_validation->set_rules('courier_name', 'Courier Name', 'trim|required');
        $this->form_validation->set_rules('awb', 'AWB', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
             log_message('info', __FUNCTION__ . '=> Form Validation is not updated by Partner '. $this->session->userdata('partner_id').
                        " booking id ". $booking_id. " Data". print_r($this->input->post(), true));
            $this->update_spare_parts_form($booking_id);
        } else { // if ($this->form_validation->run() == FALSE) {
            $partner_id = $this->session->userdata('partner_id');
            $data['parts_shipped'] = $this->input->post('shipped_parts_name');
            $data['courier_name_by_partner'] = $this->input->post('courier_name');
            $data['awb_by_partner'] = $this->input->post('awb');
            $data['remarks_by_partner'] = $this->input->post('remarks_by_partner');
            $data['shipped_date'] = $this->input->post('shipment_date');
            
            $data['status'] = "Shipped";
            $where  = array('booking_id'=> $booking_id, 'partner_id'=> $partner_id);
            $response = $this->service_centers_model->update_spare_parts($where, $data);
            if($response){
                
                $this->insert_details_in_state_change($booking_id, SPARE_PARTS_SHIPPED, "Partner acknowledged to shipped spare parts");
               
                $sc_data['current_status'] = "InProcess";
                $sc_data['internal_status'] = SPARE_PARTS_SHIPPED;
                $this->vendor_model->update_service_center_action($booking_id, $sc_data);
                
                $userSession = array('success' => 'Parts Updated');
                $this->session->set_userdata($userSession);
                redirect(base_url()."partner/get_spare_parts_booking");
                
            } else { //if($response){
                log_message('info', __FUNCTION__ . '=> Spare parts booking is not updated by Partner '. $this->session->userdata('partner_id').
                        " booking id ". $booking_id. " Data". print_r($this->input->post(), true));
                 $userSession = array('success' => 'Parts Not Updated');
                $this->session->set_userdata($userSession);
                redirect(base_url()."partner/update_spare_parts_form/".$booking_id);
                
            }
        }
    }
    
    function download_spare_parts(){
        log_message('info', __FUNCTION__ ." Pratner ID: ".  $this->session->userdata('partner_id'));
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');
        $where = "spare_parts_details.partner_id = '".$partner_id."' AND status = '".SPARE_PARTS_REQUESTED."' "
                . " AND booking_details.current_status IN ('Pending', 'Rescheduled') ";
        $data = $this->partner_model->get_spare_parts_booking($where);
        $template = 'download_spare_parts.xlsx';
	//set absolute path to directory with template files
	$templateDir = __DIR__ . "/../excel-templates/";
        $config = array(
		'template' => $template,
		'templateDir' => $templateDir
            );

        //load template
        $R = new PHPReport($config);
        
        $R->load(array(
                array(
                    'id' => 'booking',
                    'repeat' => true,
                    'data' => $data,
                ),
	    )
	);
        
        $output_file_excel  = TMP_FOLDER."spare_parts-".date('Y-m-d').".xlsx";
        $R->render('excel', $output_file_excel);
        if (file_exists($output_file_excel)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($output_file_excel).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($output_file_excel));
                readfile($output_file_excel);
                exec("rm -rf " . escapeshellarg($output_file_excel));
                exit;
         }
        
    }
    
    
    /**
     * @desc: This is used to show Booking Life Cycle of particular Booking
     * params: String Booking_ID
     * return: Array of Data for View
     */
    function get_booking_life_cycle($booking_id){
        $this->checkUserSession();
        log_message('info', __FUNCTION__. " Booking_id". $booking_id);
        $data['data'] = $this->booking_model->get_booking_state_change_by_id($booking_id);
        $data['booking_details'] = $this->booking_model->getbooking_history($booking_id);
        // send empty beacuse there is no need to display sms to partner panel
        $data['sms_sent_details'] = array();
       
        $this->load->view('partner/header');

        $this->load->view('employee/show_booking_life_cycle', $data);

    }
    /**
     * @desc: This is used to print  address for selected booking
     * @param Array $booking_address
     */
    function download_shippment_address($booking_address){
        $this->checkUserSession();
        log_message('info', __FUNCTION__ ." Pratner ID: ".  $this->session->userdata('partner_id'));
       
        $booking_history['details'] = array();
        foreach ($booking_address as $key=> $value) {
            $booking_history['details'][$key]  = $this->booking_model->getbooking_history($value, "join")[0];
            $booking_history['details'][$key]['partner'] = $this->partner_model->getpartner($this->session->userdata('partner_id'))[0];
        }
       
        $this->load->view('partner/print_address',$booking_history);
        
    }
    /**
     * @desc: This is used to print courier manifest or address for selected booking
     */
    function print_all(){
        $this->checkUserSession();
        log_message('info', __FUNCTION__ ." Pratner ID: ".  $this->session->userdata('partner_id'));
        $booking_address = $this->input->post('download_address');
        $booking_manifest = $this->input->post('download_courier_manifest');
    
        if(!empty($booking_address)){
            
            $this->download_shippment_address($booking_address);
            
        } else if(!empty($booking_manifest)){
            
            $this->download_mainfest($booking_manifest);

        } else if(empty($booking_address) && empty($booking_manifest)){
            echo "Please Select Any Checkbox";
        }
 
    }
    /**
     * @desc: This is used to print courier manifest for selected booking
     * @param type $booking_manifest
     */
    function download_mainfest($booking_manifest){
        $this->checkUserSession();
        log_message('info', __FUNCTION__ ." Pratner ID: ".  $this->session->userdata('partner_id'));
        $spare_parts_details['courier_manifest'] = array();
        foreach ($booking_manifest as $key => $value) {
          
            $where = "spare_parts_details.booking_id = '".$value."' AND status = '".SPARE_PARTS_REQUESTED."' "
                . " AND booking_details.current_status IN ('Pending', 'Rescheduled') ";
            $spare_parts_details['courier_manifest'][$key] = $this->partner_model->get_spare_parts_booking($where)[0];
            $spare_parts_details['courier_manifest'][$key]['brand'] = $this->booking_model->get_unit_details(array('booking_id'=> $value))[0]['appliance_brand'];
        }
        
        $this->load->view('partner/courier_manifest', $spare_parts_details);
    }
    
    /**
     * @Desc: This function is used to login to particular Partner
     *          This function is being called using AJAX
     * @params: partner id
     * @return: void
     * 
     */
    function allow_log_in_to_partner($partner_id){
        //Getting partner details
        $partner_details = $this->partner_model->get_partner_login_details($partner_id);
        $data['user_name'] = strtolower($partner_details[0]['user_name']);
        $data['password'] = $partner_details[0]['password'];
        
         //Loggin to SF Panel with username and password
         
        $partner = $this->partner_model->partner_login($data);

        if ($partner) {
            //get partner details now
            $partner_details = $this->partner_model->getpartner($partner['partner_id']);

            $this->setSession($partner_details[0]['id'], $partner_details[0]['public_name'], $partner['id']);
            log_message('info', 'Partner loggedIn  partner id' .
                    $partner_details[0]['id'] . " Partner name" . $partner_details[0]['public_name']);

            //Saving Login Details in Database
            $login_data['browser'] = $this->agent->browser();
            $login_data['agent_string'] = $this->agent->agent_string();
            $login_data['ip'] = $this->session->all_userdata()['ip_address'];
            $login_data['action'] = _247AROUND_LOGIN;
            $login_data['entity_type'] = $this->session->all_userdata()['userType'];
            $login_data['agent_id'] = $this->session->all_userdata()['agent_id'];
            $login_data['entity_id'] = $this->session->all_userdata()['partner_id'];

            $login_id = $this->employee_model->add_login_logout_details($login_data);
            //Adding Log Details
            if ($login_id) {
                log_message('info', __FUNCTION__ . ' Logging details have been captured for partner ' . $login_data['employee_name']);
            } else {
                log_message('info', __FUNCTION__ . ' Err in capturing logging details for partner ' . $login_data['employee_name']);
            }

        }
             log_message('info',__FUNCTION__." No partner Details has been found for Login");
    }
    /**
     * @desc: Display list of Shipped Parts in the Partner Panel
     */
    function get_shipped_parts_list($offset= 0){
        log_message('info', __FUNCTION__ ." Pratner ID: ".  $this->session->userdata('partner_id'));
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');
        $where = "spare_parts_details.partner_id = '".$partner_id."' "
                . " AND status IN ('Delivered', 'Shipped', '".DEFECTIVE_PARTS_PENDING."', '".DEFECTIVE_PARTS_SHIPPED."')  ";
          
        $config['base_url'] = base_url() . 'partner/get_shipped_parts_list';
        $total_rows = $this->partner_model->get_spare_parts_booking_list($where, false, false, false);
        $config['total_rows'] = $total_rows[0]['total_rows'];

        $config['per_page'] = 50;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['spare_parts'] = $this->partner_model->get_spare_parts_booking_list($where, $offset, $config['per_page'], true);
        
        
        $this->load->view('partner/header');
        $this->load->view('partner/shipped_spare_part_booking', $data);
    }
    /**
     * @desc: Pending Defective Parts list 
     */
    function get_waiting_defective_parts($offset = 0){
        log_message('info', __FUNCTION__ ." Pratner ID: ".  $this->session->userdata('partner_id'));
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');
        $where = "spare_parts_details.partner_id = '".$partner_id."' "
                . " AND status IN ('".DEFECTIVE_PARTS_PENDING."','".DEFECTIVE_PARTS_SHIPPED ."')  ";
          
        $config['base_url'] = base_url() . 'partner/get_waiting_defective_parts';
        $total_rows = $this->partner_model->get_spare_parts_booking_list($where, false, false, false);
        $config['total_rows'] = $total_rows[0]['total_rows'];

        $config['per_page'] = 50;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['spare_parts'] = $this->partner_model->get_spare_parts_booking_list($where, $offset, $config['per_page'], true);
        $where_internal_status = array("page" => "defective_parts", "active" => '1');
        $data['internal_status'] = $this->booking_model->get_internal_status($where_internal_status);
        $this->load->view('partner/header');
        $this->load->view('partner/waiting_defective_parts', $data);
        
    }
    /**
     * @desc: Partner acknowledge to receive defective spare parts
     * @param String $booking_id
     */
    function acknowledge_received_defective_parts($booking_id) {
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id'). " Booking Id ". $booking_id);
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');
        $where = array('booking_id' => $booking_id, 'partner_id' => $partner_id);
        $response = $this->service_centers_model->update_spare_parts($where, array('status' => DEFECTIVE_PARTS_RECEIVED,
            'approved_defective_parts_by_partner'=> '1', 'remarks_defective_part_by_partner'=> NULL,
            'received_defective_part_date' => date("Y-m-d H:i:s")));
        if ($response) {
            log_message('info', __FUNCTION__ . " Received Defective Spare Parts ".$booking_id
                    ." Partner Id". $this->session->userdata('partner_id'));
            $this->insert_details_in_state_change($booking_id, DEFECTIVE_PARTS_RECEIVED, "Partner Received Defective Spare Parts");

            $sc_data['current_status'] = "InProcess";
            $sc_data['internal_status'] = _247AROUND_COMPLETED;
            $this->vendor_model->update_service_center_action($booking_id, $sc_data);

            $userSession = array('success' => ' Received Defective Spare Parts');
            $this->session->set_userdata($userSession);
            redirect(base_url() . "partner/get_waiting_defective_parts");
        } else { //if($response){
            log_message('info', __FUNCTION__ . '=> Defective Spare Parts not udated  by Partner ' . $this->session->userdata('partner_id') .
                    " booking id " . $booking_id);
            $userSession = array('success' => 'There is some error. Please try again.');
            $this->session->set_userdata($userSession);
            redirect(base_url() . "partner/get_waiting_defective_parts");
        }
    }
    /**
     * @desc: Partner rejected Defective Parts with reason.
     * @param Sting $booking_id
     * @param Urlencoded $status (Rejection Reason)
     */
    function reject_defective_part($booking_id,$status){
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id'). " Booking Id ". $booking_id);
        $this->checkUserSession();
        $rejection_reason = base64_decode(urldecode($status));
        $partner_id = $this->session->userdata('partner_id');
        $where = array('booking_id' => $booking_id, 'partner_id' => $partner_id);
        $response = $this->service_centers_model->update_spare_parts($where, array('status' => DEFECTIVE_PARTS_REJECTED, 
            'remarks_defective_part_by_partner' => $rejection_reason, 
            'approved_defective_parts_by_partner'=> '0',
            'defective_part_shipped' => NULL,
            'defective_part_shipped_date' =>NULL,
            'awb_by_sf'=> NULL,
            'courier_name_by_sf'=> NULL,
            'remarks_defective_part_by_sf'=> NULL));
        if ($response) {
            log_message('info', __FUNCTION__ . " Sucessfully updated Table ".$booking_id
                    ." Partner Id". $this->session->userdata('partner_id'));
            $this->insert_details_in_state_change($booking_id, $rejection_reason, DEFECTIVE_PARTS_REJECTED);

            $sc_data['current_status'] = "InProcess";
            $sc_data['internal_status'] = $rejection_reason;
            $this->vendor_model->update_service_center_action($booking_id, $sc_data);

            $userSession = array('success' => 'Defective Parts Rejected To SF');
            $this->session->set_userdata($userSession);
            redirect(base_url() . "partner/get_waiting_defective_parts");
        } else { //if($response){
            log_message('info', __FUNCTION__ . '=> Defective Spare Parts Not Updated by Partner' . $this->session->userdata('partner_id') .
                    " booking id " . $booking_id);
            $userSession = array('success' => 'There is some error. Please try again.');
            $this->session->set_userdata($userSession);
            redirect(base_url() . "partner/get_waiting_defective_parts");
        }
    }
    
    /**
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> b70e85a... Partner Add Booking Form changes for Services, Brands, Category, Capacity, Model
     * @Desc: This function is used to get Brands for selected Services of particular Partner 
     *          This is being called from AJAX
     * @params: partner_id, service_name
     * @return: String
     * 
     */
    function get_brands_from_service(){
        $partner_id = $this->input->post('partner_id');
        $service_id = $this->input->post('service_id');
        //Getting Unique values of Brands for Particular Partner and service id
        $where = array('partner_id'=>$partner_id, 'service_id'=>$service_id);
        $data = $this->partner_model->get_partner_service_brands($where);
        $option = "";
        foreach($data as $value){
            $option .="<option value='".$value['brand']."'>".$value['brand']."</option>";
        }
        echo $option;
        
    }
    
    /**
     * @Desc: This function is used to get Category Details for Partner
     *          This is being called from AJAX
     * @params: partner_id, service_name, brand name
     * @return: String
     * 
     */
    function get_category_from_service(){
        $partner_id = $this->input->post('partner_id');
        $service_id = $this->input->post('service_id');
        $brand = $this->input->post('brand');
        //Getting Unique values of Category for Particular Partner ,service id and brand
        $where = array('partner_id'=>$partner_id, 'service_id'=>$service_id,'brand'=>$brand);
        $data = $this->partner_model->get_category_service_brands($where);
        $option = "";
        foreach($data as $value){
            $option .="<option value='".$value['category']."'>".$value['category']."</option>";
        }
        echo $option;
        
    }
    
    /**
     * @Desc: This function is used to get Capacity Model for Partner for particular Brand, service_id and category
     *      This is being called from AJAX
     * @params: partner_id, service_name, brand_name, category
     * $return: Json
     * 
     */
    function get_capacity_for_partner(){
        $partner_id = $this->input->post('partner_id');
        $service_id = $this->input->post('service_id');
        $brand = $this->input->post('brand');
        $category = $this->input->post('category');
        //Getting Unique values of Category for Particular Partner ,service id and brand
        $where = array('partner_id'=>$partner_id, 'service_id'=>$service_id,'brand'=>$brand,'category'=>$category);
        $data = $this->partner_model->get_partner_appliance_details($where);
        $capacity = "";
        foreach($data as $value){
            $capacity .="<option value='".$value['capacity']."'>".$value['capacity']."</option>";
        }
        $option['capacity'] = $capacity;
        
        print_r(json_encode($option));
    }
    
    /**
     * @Desc: This function is used to get  Model for Partner for particular Brand, service_id, capacity and category
     *      This is being called from AJAX
     * @params: partner_id, service_name, brand_name, category
     * $return: Json
     * 
     */
    function get_model_for_partner(){
        $partner_id = $this->input->post('partner_id');
        $service_id = $this->input->post('service_id');
        $brand = $this->input->post('brand');
        $category = $this->input->post('category');
        $capacity = $this->input->post('capacity');
        //Getting Unique values of Model for Particular Partner ,service id and brand
        $where = array('partner_id'=>$partner_id, 'service_id'=>$service_id,'brand'=>$brand,'category'=>$category,'capacity'=>$capacity);
        $data = $this->partner_model->get_partner_appliance_details($where);
        
        $model = "";
        foreach($data as $value){
            $model .="<option value='".$value['model']."'>".$value['model']."</option>";
        }
        $option['model'] = $model;
        print_r(json_encode($option));
    }
    
    /**
     * @desc: This method is used to display list of booking which received by Partner
     * @param Integer $offset
     */
    function get_approved_defective_parts_booking($offset = 0){
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id'));
        
        $partner_id = $this->session->userdata('partner_id');
        $where = "spare_parts_details.partner_id = '".$partner_id."' "
                . " AND approved_defective_parts_by_partner = '1' ";
          
        $config['base_url'] = base_url() . 'partner/get_approved_defective_parts_booking';
        $total_rows = $this->partner_model->get_spare_parts_booking_list($where, false, false, false);
        $config['total_rows'] = $total_rows[0]['total_rows'];
        $config['per_page'] = 50;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();
        $data['count'] = $config['total_rows'];
        $data['spare_parts'] = $this->partner_model->get_spare_parts_booking_list($where, $offset, $config['per_page'], true);
        
        $this->load->view('partner/header');
        $this->load->view('partner/approved_defective_parts', $data);
    }
}
