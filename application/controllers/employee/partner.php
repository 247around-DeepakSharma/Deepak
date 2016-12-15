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
                log_message('info', __FUNCTION__ . ' Logging details have been captured for partner ' . $login_data['employee_name']);
            } else {
                log_message('info', __FUNCTION__ . ' Err in capturing logging details for partner ' . $login_data['employee_name']);
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
            log_message('info', __FUNCTION__ . ' Logging details have been captured for partner ' . $login_data['employee_name']);
        } else {
            log_message('info', __FUNCTION__ . ' Err in capturing logging details for partner ' . $login_data['employee_name']);
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
                $this->partner_model->edit_partner($this->input->post(), $this->input->post('id'));

                redirect(base_url() . 'employee/partner/viewpartner', 'refresh');
            }else{
                //If Partner not present, Partner is being added
                $_POST['is_active'] = '1';
                //Temporary value
                $_POST['auth_token'] = rand(1,100);

                //Sending POST array to Model
                $partner_id = $this->partner_model->add_partner($this->input->post());
                //Set Flashdata on success or on Error of Data insert in table
                if(!empty($partner_id)){
                    $this->session->set_flashdata('success','Partner added successfully.');

                    //Echoing inserted ID in Log file
                    log_message('info',__FUNCTION__.' New Partner has been added with ID '.  $partner_id." Done By " . $this->session->userdata('employee_id'));
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
        if ($this->form_validation->run() == FALSE) {
            return FALSE;
        } else {
            return TRUE;
        }
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

        $this->load->view('employee/viewpartner', array('query' => $data));
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
            //search user by phone number
            $output = $this->user_model->search_user($phone_number);

            if (empty($output)) {
                //if user not found set error session data
                $this->session->set_flashdata('error', 'Booking Not Found');

                redirect(base_url() . 'employee/partner/get_user_form');
            } else {
                //if entered detail matches it will be displayed in a page
                $page = 0;
                $offset = 0;
                if ($page == 0) {
                    $page = 50;
                }
                $offset = ($this->uri->segment(7) != '' ? $this->uri->segment(7) : 0);

                $config['base_url'] = base_url() . "employee/partner/finduser/" . $offset . "/" . $page . "/" . $phone_number;
                $config['total_rows'] = $this->partner_model->total_user_booking($output[0]['user_id'], $partner_id);
                $config['per_page'] = $page;
                $config['uri_segment'] = 7;
                $config['first_link'] = 'First';
                $config['last_link'] = 'Last';

                $this->pagination->initialize($config);
                $data['links'] = $this->pagination->create_links();

                $data['data'] = $this->user_model->partner_booking_history($phone_number, $partner_id, $config['per_page'], $offset);

                if (empty($data['data'])) {

                    $data['data'] = $output;
                }

                $data['appliance_details'] = $this->user_model->appliance_details($phone_number);


                $this->load->view('partner/header');
                $this->load->view('partner/bookinghistory', $data);
            }
        } elseif ($booking_id != "") {  //if booking id given and matched, will be displayed
            $where = array('booking_details.booking_id' => $booking_id);
            $data['Bookings'] = $this->booking_model->search_bookings($where,$partner_id);

            $data_value = search_for_key($data['Bookings']);

            if (isset($data_value['Pending']) || isset($data_value['Cancelled']) || isset($data_value['Completed'])){
                $this->load->view('partner/header');
                $this->load->view('partner/search_result',$data);
            }else{
                //if user not found set error session data
                $this->session->set_flashdata('error', 'Booking Not Found');

                redirect(base_url() . 'employee/partner/get_user_form');
            }


        } else if (!empty($order_id)) {

            $where = array('order_id' => $order_id);
            $data['Bookings'] = $this->booking_model->search_bookings($where, $partner_id);
            $data['search'] = "Search";

           $data_value = search_for_key($data['Bookings']);

            if (isset($data_value['Pending']) || isset($data_value['Cancelled']) || isset($data_value['Completed'])){
                $this->load->view('partner/header');
                $this->load->view('partner/search_result',$data);
            }else{
                //if user not found set error session data
                $this->session->set_flashdata('error', 'Booking Not Found');

                redirect(base_url() . 'employee/partner/get_user_form');
            }
        } else if (!empty($serial_no)) {

            $where = array('partner_serial_number' => $serial_no);
            $data['Bookings'] = $this->booking_model->search_bookings($where, $partner_id);
            $data['search'] = "Search";

            $data_value = search_for_key($data['Bookings']);

            if (isset($data_value['Pending']) || isset($data_value['Cancelled']) || isset($data_value['Completed'])){
                $this->load->view('partner/header');
                $this->load->view('partner/search_result',$data);
            }else{
                //if user not found set error session data
                $this->session->set_flashdata('error', 'Booking Not Found');

                redirect(base_url() . 'employee/partner/get_user_form');
            }
        }
    }

    /**
     *  @desc : This function is to view details of any particular booking to partner
     *
     * 	We get all the details like User's details, booking details, and also the appliance's unit details of particular partner
     *
     *  @param : booking id, partner ID
     *  @return : booking details and load view
     */
    function viewdetails($booking_id, $partner_id) {
         $this->checkUserSession();
        $data['booking_history'] = $this->booking_model->getbooking_history($booking_id);
        $unit_where = array('booking_id'=>$booking_id, 'partner_id' => $partner_id);
        $data['unit_details'] = $this->booking_model->get_unit_details($unit_where);

        $data['service_center'] = $this->booking_model->selectservicecentre($booking_id);

        $this->load->view('partner/header');
        $this->load->view('partner/viewdetails', $data);
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
     * Atleast one booking/Query cancellation reason must be selected.
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
        log_message('info', __FUNCTION__ . " Booking Id: " . $booking_id);
        
        $this->checkUserSession();
        
        $this->form_validation->set_rules('escalation_reason_id', 'Escalation Reason', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->escalation_form($booking_id);
        } else {
            $escalation['escalation_reason'] = $this->input->post('escalation_reason_id');
            $bookinghistory = $this->vendor_model->getbooking_history($booking_id);
            
            $escalation['booking_id'] = $booking_id;
            if(!is_null($bookinghistory[0]['assigned_vendor_id'])){
                $escalation['vendor_id'] = $bookinghistory[0]['assigned_vendor_id'];
                $vendorContact = $this->vendor_model->getVendorContact($escalation['vendor_id']);
                $to = $vendorContact[0]['primary_contact_email'];
                $cc = $vendorContact[0]['owner_email'].",nits@247around.com,escalations@247around.com";
                
            } else {
                $escalation['vendor_id'] = "";
                $to = "escalations@247around.com"; 
                $cc = "nits@247around.com";
            }
            
            $escalation['booking_date'] = date('Y-m-d', strtotime($bookinghistory[0]['booking_date']));
            $escalation['booking_time'] = $bookinghistory[0]['booking_timeslot'];
            
            log_message('info', __FUNCTION__ . " escalation_reason  " . print_r($escalation, true));
            
            //inserts vendor escalation details
            $escalation_id = $this->vendor_model->insertVendorEscalationDetails($escalation);
            $escalation_reason  = $this->vendor_model->getEscalationReason(array('id'=>$escalation['escalation_reason']));
            $this->notify->insert_state_change($escalation['booking_id'], 
                    "Escalation" , _247AROUND_PENDING , $escalation_reason[0]['escalation_reason'], 
                    $this->session->userdata('agent_id'), $this->session->userdata('partner_name'),
                    $this->session->userdata('partner_id'));
            if($escalation_id){
                log_message('info', __FUNCTION__ . " Escalation INSERTED ");
                $from = "escalations@247around.com";
                $bcc=""; $attachment = "";
                
                $subject = "Booking " . $booking_id . " Escalated By Partner " . $this->session->userdata('partner_name');
                $message = "Booking " . $booking_id . " Escalated By Partner " . $this->session->userdata('partner_name');
                
                $is_mail = $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment);
                
                if($is_mail){
                    log_message('info', __FUNCTION__ . " Escalation Mail Sent ");
                    
                    $reason_flag['escalation_policy_flag'] = json_encode(array('mail_to_escalation_team'=>1), true);
                    
                    $this->vendor_model->update_esclation_policy_flag($escalation_id, $reason_flag, $booking_id);
                }
            }
            
            log_message('info', __FUNCTION__ . " Exiting");
            
            $this->session->set_flashdata('success', 'Booking '. $booking_id. " has been escalated, our team will look into this immediately.");

            redirect(base_url() . "partner/escalation_form/".$booking_id);
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
    function get_spare_parts_booking(){
        log_message('info', __FUNCTION__ ." Pratner ID: ".  $this->session->userdata('partner_id'));
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');
        $where = array('spare_parts_details.partner_id'=> $partner_id, 'status'=> SPARE_PARTS_REQUESTED);
        $where_in = array('Pending','Rescheduled');
        $data['spare_parts'] = $this->partner_model->get_spare_parts_booking($where, $where_in);
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
        $where_in = array('Pending','Rescheduled');
        $where = array('spare_parts_details.partner_id'=> $partner_id, 'status'=> SPARE_PARTS_REQUESTED, 'spare_parts_details.booking_id'=> $booking_id);
        $data['spare_parts'] = $this->partner_model->get_spare_parts_booking($where, $where_in);
        
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
            //$data['edd'] = $this->input->post('edd');
           
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
        $where_in = array('Pending','Rescheduled');
        $where = array('spare_parts_details.partner_id'=> $partner_id, 'status'=> SPARE_PARTS_REQUESTED);
        $data = $this->partner_model->get_spare_parts_booking($where, $where_in);
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
     * @desc: This method is used to download Shippemnt Address as Pdf file
     * @param type $booking_id
     */
    function download_sc_address($booking_id){
         $this->checkUserSession();
        log_message('info', __FUNCTION__. " Booking_id". $booking_id);
        $booking_history  = $this->booking_model->getbooking_history($booking_id, "join");
        $template = 'Address_Printout.xlsx';
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
                    'id' => 'meta',
                    'data' => $booking_history[0],
                ),
	    )
	);
        
        $output_file_excel  = TMP_FOLDER."shippment_address-".$booking_id.".xlsx";
        $output_file_pdf = TMP_FOLDER."shippment_address-".$booking_id.".pdf";
        $R->render('excel', $output_file_excel);
        
        putenv('PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/opt/node/bin');
        $tmp_path = libreoffice_pdf;
        $tmp_output_file = libreoffice_output_file;
	$cmd = 'echo ' . $tmp_path . ' & echo $PATH & UNO_PATH=/usr/lib/libreoffice & ' .
	    '/usr/bin/unoconv --format pdf --output ' . $output_file_pdf . ' ' .
	    $output_file_excel . ' 2> ' . $tmp_output_file;
         
	$output = '';
	$result_var = '';
	exec($cmd, $output, $result_var);
        //Download PDF file
        if (file_exists($output_file_pdf)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($output_file_pdf).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($output_file_pdf));
                readfile($output_file_pdf);
                exec("rm -rf " . escapeshellarg($output_file_pdf));
                exec("rm -rf " . escapeshellarg($output_file_excel));
                exit;
         }
        
        log_message('info', __FUNCTION__ . " => Exiting, Booking ID: " . $booking_id);
        
    }
    
    function download_courier_manifest($booking_id){
         $this->checkUserSession();
        log_message('info', __FUNCTION__. " Booking_id". $booking_id);
        $booking_history  = $this->booking_model->getbooking_history($booking_id);
        $where = array('spare_parts_details.booking_id'=> $booking_id);
        $where_in = array('Pending','Rescheduled');
        $spare_parts_details = $this->partner_model->get_spare_parts_booking($where, $where_in);
        $template = 'Courier_Manifest.xlsx';
        
        $date1=date_create($booking_history[0]['create_date']);
        $date2=date_create(date('Y-m-d H:i:s'));
        $diff=date_diff($date1,$date2);
        $age['booking_age'] = $diff->days;
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
                    'id' => 'meta',
                    'data' => $booking_history[0],
                ),
                array(
                    'id' => 'meta2',
                    'data' => $spare_parts_details[0],
                ),
                array(
                    'id' => 'meta1',
                    'data' => $age,
                ),
	    )
	);
        
        $output_file_excel  = TMP_FOLDER."courier_manifest-".$booking_id.".xlsx";
        $output_file_pdf = TMP_FOLDER."courier_manifest-".$booking_id.".pdf";
        $R->render('excel', $output_file_excel);
        
        putenv('PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/opt/node/bin');
        $tmp_path = libreoffice_pdf;
        $tmp_output_file = libreoffice_output_file;
	$cmd = 'echo ' . $tmp_path . ' & echo $PATH & UNO_PATH=/usr/lib/libreoffice & ' .
	    '/usr/bin/unoconv --format pdf --output ' . $output_file_pdf . ' ' .
	    $output_file_excel . ' 2> ' . $tmp_output_file;
         
	$output = '';
	$result_var = '';
	exec($cmd, $output, $result_var);
       
        //Download PDF file
        if (file_exists($output_file_pdf)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($output_file_pdf).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($output_file_pdf));
                readfile($output_file_pdf);
                exec("rm -rf " . escapeshellarg($output_file_pdf));
                exec("rm -rf " . escapeshellarg($output_file_excel));
                
                exit;
         }
         
        
        log_message('info', __FUNCTION__ . " => Exiting, Booking ID: " . $booking_id);
        
        
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

            redirect(base_url() . "partner/get_spare_parts_booking");   
        }
    }

}
