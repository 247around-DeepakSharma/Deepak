<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

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

	$this->load->library("pagination");
        $this->load->library("session");
	$this->load->library('form_validation');
	$this->load->library('notify');

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
        $partner_id = $this->partner_model->partner_login($data);

        if ($partner_id) {
        //get partner details now
        $partner_details = $this->partner_model->getpartner($partner_id);

        $this->setSession($partner_details[0]['id'], $partner_details[0]['public_name']);
        log_message('info', 'Partner loggedIn  partner id' . $partner_details[0]['id'] . " Partner name" . $partner_details[0]['public_name']);

        redirect(base_url() . "partner/pending_booking");
        } else {

            $userSession = array('error' => 'Please enter correct user name and password' );
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
        $config['total_rows'] = $this->partner_model->getPending_booking("count","",$partner_id);

        $config['per_page'] = 50;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['bookings'] = $this->partner_model->getPending_booking($config['per_page'], $offset, $partner_id);

        if ($this->session->flashdata('result') != '')
            $data['success'] = $this->session->flashdata('result');
        
        log_message('info', 'Partner View: Pending booking: Partner id: ' . $partner_id . ", Partner name: " . $this->session->userdata('partner_name'));
        
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
        $config['total_rows'] = $this->partner_model->getPending_queries("count","",$partner_id);

        $config['per_page'] = 50;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['bookings'] = $this->partner_model->getPending_queries($config['per_page'], $offset, $partner_id);

        if ($this->session->flashdata('result') != '')
            $data['success'] = $this->session->flashdata('result');
         log_message('info', 'Partner view Pending query  partner id' . $partner_id . " Partner name" . $this->session->userdata('partner_name')." data ". print_r($data, true));
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
        $data['booking_history'] = $this->booking_model->getbooking_history($booking_id);
        $data['unit_details'] = $this->booking_model->get_unit_details($booking_id);


        log_message('info', 'Partner view booking details booking  partner id' . $this->session->userdata('partner_id') . " Partner name" . $this->session->userdata('partner_name'). " data ". print_r($data, true));

        $this->load->view('partner/header');
        $this->load->view('partner/booking_details', $data);
    }


    /**
     * @desc: This function Sets Session
     * @param: Partrner id
     * @param: Partner name
     * @return: void
     */
    function setSession($partner_id, $partner_name) {
    $userSession = array(
        'session_id' => md5(uniqid(mt_rand(), true)),
        'partner_id' => $partner_id,
        'partner_name' => $partner_name,
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

        $this->session->sess_destroy();
        redirect(base_url() . "partner/login");
    }

    /**
     * @desc: This method loads abb booking form
     * it gets user details(if exist), city, source, services
     */
    function get_addbooking_form($phone_number){
        $this->checkUserSession();
        $data = $this->booking_model->get_city_booking_source_services($phone_number);
        $this->load->view('partner/header');
        $this->load->view('partner/get_addbooking', $data);
    }

 /**
     * @desc: This method is used to process to add booking by partner
     */
    function process_addbooking() {
    $this->checkUserSession();
    $validate = $this->set_form_validation();
    log_message('info', 'Partner initiate add booking' . $this->session->userdata('partner_name'));

    if ($validate) {
        $booking_date = date('d-m-Y', strtotime($this->input->post('booking_date')));
        $order_id = $this->input->post('order_id');

        $description = $this->input->post('description');

        $authToken = $this->partner_model->get_authentication_code($this->session->userdata('partner_id'));
        if ($authToken) {
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
        $post['productType'] = $description;
        $post['category'] = $this->input->post('appliance_category');
        $post['capacity'] = $this->input->post('appliance_capacity');
        $post['model'] = $this->input->post('model_number');
        $post['serial_number'] = $this->input->post('serial_number');
        $post['purchase_month'] = $this->input->post('purchase_month');
        $post['purchase_year'] = $this->input->post('purchase_year');
        $post['partner_source'] = $this->input->post('partner_source');
        $post['remarks'] = $this->input->post('query_remarks');
        $post['orderID'] = $order_id;
        $post['booking_date'] = $booking_date;
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
        $this->load->view('employee/header');
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
                    log_message('info',__FUNCTION__.' New Partner has been added with ID '.  $partner_id);
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
        $query = $this->partner_model->viewpartner($partner_id);

        $this->load->view('employee/header');

        $this->load->view('employee/viewpartner', array('query' => $query));
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
        $this->load->view('employee/header');
        $this->load->view('employee/addpartner', array('query' => $query,'results'=>$results));
    }

    /**
     * @desc: This is used to get find user form in Partner CRM
     * params: void
     * return: View form to find user
     */
    function get_user_form() {

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

            $where = array('serial_number' => $serial_no);
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
        $data['booking_history'] = $this->booking_model->getbooking_history($booking_id);
        $data['unit_details'] = $this->booking_model->get_unit_details($booking_id, $partner_id);

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

}
