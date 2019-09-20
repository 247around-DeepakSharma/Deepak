<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Partner extends CI_Controller {

    Private $OLD_BOOKING_STATE = "";

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
        $this->load->model('dealer_model');
        $this->load->model('service_centers_model');
        $this->load->model('penalty_model');
        $this->load->model("inventory_model");
        $this->load->model("service_centre_charges_model");
        $this->load->model('around_scheduler_model'); 
        $this->load->model('accounting_model');
        $this->load->library("pagination");
        $this->load->library("session");
        $this->load->library('form_validation');
        $this->load->library('notify');
        $this->load->library('asynchronous_lib');
        $this->load->library('booking_utilities');
        $this->load->library('miscelleneous');
        $this->load->library('user_agent');
        $this->load->library("initialized_variable");
        $this->load->model("push_notification_model");
        $this->load->library("booking_creation_lib");
        $this->load->library('table');
        $this->load->library("invoice_lib");
        $this->load->library("paytm_cb");
        $this->load->helper(array('form', 'url', 'file', 'array'));
        $this->load->dbutil();
    }

    /**
     * @desc: This is used to load Partner  Login Page
     *
     * @param: void
     * @return: void
     */
    function index() {
        $select = "partner_logo,alt_text";
        $where = array('partner_logo IS NOT NULL' => NULL);
        $data['partner_logo'] = $this->booking_model->get_partner_logo($select, $where);
        $data['is_saas'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view('partner/partner_login', $data);
    }

    /**
     * @desc: this is used to load pending booking
     * @param: Offset and page no., all flag to get all data, Booking id
     * @return: void
     */
    function pending_booking($booking_id = "") {
        $this->checkUserSession();
        $data['escalation_reason'] = $this->vendor_model->getEscalationReason(array('entity' => 'partner', 'active' => '1'));
        $agent_id = $this->session->userdata('agent_id');
        if($this->session->userdata('is_filter_applicable') == 1){
           $data['states'] = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state_code.state) as state",array("agent_filters.agent_id"=>$agent_id),array("agent_filters"=>"agent_filters.state=state_code.state"),NULL,array('state'=>'ASC'),NULL,array("agent_filters"=>"left"),array()); 
        }
        else{
            $data['states'] = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state) as state",NULL,NULL,NULL,array('state'=>'ASC'),NULL,NULL,array());
        }
        $data['is_ajax'] = $this->input->post('is_ajax');
        $data['booking_id'] = $booking_id;
        if(empty($this->input->post('is_ajax'))){
            $this->miscelleneous->load_partner_nav_header();
            $this->load->view('partner/pending_booking', $data);
            $this->load->view('partner/partner_footer');
        }else{
            $this->load->view('partner/pending_booking', $data);
        }
    }

    /**
     * @desc: this is used to load pending booking
     * @param: Offset and page no.
     * @return: void
     */
    function pending_queries($offset = 0) {
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
        $this->miscelleneous->load_partner_nav_header();
       // $this->load->view('partner/header');
        $this->load->view('partner/pending_queries', $data);
    }

    /**
     * @desc: this is used to display completed booking for specific service center
     */
    function closed_booking($state, $offset = 0, $booking_id = "") {
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');
        $stateCity = 0;
        if($this->session->userdata('is_filter_applicable') == 1){
            $stateCity = 1;
        }
        $config['base_url'] = base_url() . 'partner/closed_booking/' . $state;
        if (!empty($booking_id)) {
            $config['total_rows'] = $this->partner_model->getclosed_booking("count", "", $partner_id, $state, $booking_id,$stateCity);
        } else {
            $config['total_rows'] = $this->partner_model->getclosed_booking("count", "", $partner_id, $state,"",$stateCity);
        }

        $config['per_page'] = 50;
        $config['uri_segment'] = 4;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();
        $data['count'] = $config['total_rows'];
        if (!empty($booking_id)) {
            $data['bookings'] = $this->partner_model->getclosed_booking($config['per_page'], $offset, $partner_id, $state, $booking_id,$stateCity);
        } else {
            $data['bookings'] = $this->partner_model->getclosed_booking($config['per_page'], $offset, $partner_id, $state,"",$stateCity);
        }

        if ($this->session->flashdata('result') != '')
            $data['success'] = $this->session->flashdata('result');

        $data['status'] = $state;
       $data['states'] = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state) as state",NULL,NULL,NULL,array('state'=>'ASC'),NULL,NULL,array());
        log_message('info', 'Partner view ' . $state . ' booking  partner id' . $partner_id . " Partner name" . $this->session->userdata('partner_name') . " data " . print_r($data, true));
        $this->miscelleneous->load_partner_nav_header();
        //$this->load->view('partner/header');
        $this->load->view('partner/closed_booking', $data);
        $this->load->view('partner/partner_footer');
    }

    /**
     * @desc: this is used to get booking details by using booking ID And load booking booking details view
     * @param: booking id
     * @return: void
     */
    function booking_details($booking_id) {
        $this->checkUserSession();
        $data['booking_history'] = $this->booking_model->getbooking_filter_service_center($booking_id);
        $data['booking_symptom'] = $this->booking_model->getBookingSymptom($booking_id);
        $data['defective_history'] = $this->inventory_model->getDefecvtive_history($booking_id);
        $data['booking_files'] = $this->booking_model->get_booking_files(array('booking_id' => $booking_id));
        if($data['booking_history'][0]['dealer_id']){ 
            $dealer_detail = $this->dealer_model->get_dealer_details('dealer_name, dealer_phone_number_1', array('dealer_id'=>$data['booking_history'][0]['dealer_id']));
            $data['booking_history'][0]['dealer_name'] = $dealer_detail[0]['dealer_name'];
            $data['booking_history'][0]['dealer_phone_number_1'] = $dealer_detail[0]['dealer_phone_number_1'];
        }
        $unit_where = array('booking_id' => $booking_id);
        $data['unit_details'] = $this->booking_model->get_unit_details($unit_where);
        if (!is_null($data['booking_history'][0]['sub_vendor_id'])) {
            $data['dhq'] = $this->upcountry_model->get_sub_service_center_details(array('id' => $data['booking_history'][0]['sub_vendor_id']));
            if(!empty($data['dhq'])){
                $dis = $this->vendor_model->getDistrict_from_india_pincode("",$data['dhq'][0]['pincode'] );
                if(!empty($dis)){
                    $data['dhq'][0]['original_district'] = $dis[0]['district'];
                } else {
                    $data['dhq'][0]['original_district'] = $data['dhq'][0]['district'];
                }
            }
            
        }
        
        $data['symptom'] =  $data['completion_symptom'] = $data['technical_defect'] = $data['technical_solution'] = array();
        
        if(count($data['booking_symptom'])>0) {
            if(!is_null($data['booking_symptom'][0]['symptom_id_booking_creation_time'])){
                $data['symptom'] = $this->booking_request_model->get_booking_request_symptom('symptom', array('symptom.id' => $data['booking_symptom'][0]['symptom_id_booking_creation_time']));
                
                if(count($data['symptom'])<=0) {
                    $data['symptom'][0] = array("symptom" => "Default");
                }
        
            } 
            if(!is_null($data['booking_symptom'][0]['symptom_id_booking_completion_time'])){
                $data['completion_symptom'] = $this->booking_request_model->get_booking_request_symptom('symptom', array('symptom.id' => $data['booking_symptom'][0]['symptom_id_booking_completion_time']));
                
                if(count($data['completion_symptom'])<=0) {
                    $data['completion_symptom'][0] = array("symptom" => "Default");
                }
            }
            if(!is_null($data['booking_symptom'][0]['defect_id_completion'])){
                $cond['where'] = array('defect.id' => $data['booking_symptom'][0]['defect_id_completion']);
                $data['technical_defect'] = $this->booking_request_model->get_defects('defect', $cond);
                
                if(count($data['technical_defect'])<=0) {
                    $data['technical_defect'][0] = array("defect" => "Default");
                }
            }
            if(!is_null($data['booking_symptom'][0]['solution_id'])){
                $data['technical_solution'] = $this->booking_request_model->symptom_completion_solution('technical_solution', array('symptom_completion_solution.id' => $data['booking_symptom'][0]['solution_id']));
                
                if(count($data['technical_solution'])<=0) {
                    $data['technical_solution'][0] = array("technical_solution" => "Default");
                }
            }
        }
        else {
            $data['symptom'][0] = array("symptom" => "Default");
            
            if(in_array($data['booking_history'][0]['internal_status'], array(SF_BOOKING_COMPLETE_STATUS,_247AROUND_COMPLETED))) {
                $data['completion_symptom'][0] = array("symptom" => "Default");
                $data['technical_defect'][0] = array("defect" => "Default");
                $data['technical_solution'][0] = array("technical_solution" => "Default");
            }
        }
        
        if (!empty($data['booking_history']['spare_parts'])) {
            $spare_parts_list = array();
            foreach ($data['booking_history']['spare_parts'] as $key => $val) {
                if (!empty($val['requested_inventory_id'])) {
                    $inventory_spare_parts_details = $this->inventory_model->get_generic_table_details('inventory_master_list', 'inventory_master_list.part_number,inventory_master_list.part_name', array('inventory_master_list.inventory_id' => $val['requested_inventory_id']), array());
                    if (!empty($inventory_spare_parts_details)) {
                        $spare_parts_list[] = array_merge($val, array('final_spare_parts' => $inventory_spare_parts_details[0]['part_name']));
                    }
                }
            }
        }

        if (!empty($spare_parts_list)) {
            $data['booking_history']['spare_parts'] = $spare_parts_list;
        }
        
         $spare_parts_details = $this->partner_model->get_spare_parts_by_any('spare_parts_details.awb_by_sf', array('spare_parts_details.booking_id' => $booking_id, 'spare_parts_details.awb_by_sf !=' => ''));
         $awb = NULL;
        if (!empty($spare_parts_details)) {           
             $awb =$spare_parts_details[0]['awb_by_sf'];
             $courier_boxes_weight = $this->inventory_model->get_generic_table_details('courier_company_invoice_details', '*', array('awb_number' => $awb), array());
            
           if(!empty($courier_boxes_weight)){
               $data['courier_boxes_weight_details'] = $courier_boxes_weight[0];
           }
            
        }
        
        log_message('info', 'Partner view booking details booking  partner id' . $this->session->userdata('partner_id') . " Partner name" . $this->session->userdata('partner_name'));

        //$this->load->view('partner/header');
        $this->miscelleneous->load_partner_nav_header();
        $this->load->view('partner/booking_details', $data);
        $this->load->view('partner/partner_footer');
    }

    /**
     * @desc: This function will check Session
     * @param: void
     * @return: true if details matches else session is distroyed.
     */
    function checkUserSession() {
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'partner') && !empty($this->session->userdata('partner_id'))) {
            return TRUE;
        } else {
            $this->session->sess_destroy();
            redirect(base_url() . "partner/login");
        }
    }
    function checkEmployeeUserSession(){
         if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee') && !empty($this->session->userdata('id'))) {
            return TRUE;
        } else {
            $this->session->sess_destroy();
            redirect(base_url() . "employee/login");
        }
    }

    /**
     * @desc : This function for logout
     * @param: void
     * @return: void
     */
    function logout() {
        $this->checkUserSession();
        log_message('info', 'Partner logout  partner id' . $this->session->userdata('partner_id') . " Partner name" . $this->session->userdata('partner_name'));

        //Saving Logout Details in Database
        $login_data['browser'] = $this->agent->browser();
        $login_data['agent_string'] = $this->agent->agent_string();
        $login_data['ip'] = $this->session->userdata('ip_address');
        $login_data['action'] = _247AROUND_LOGOUT;
        $login_data['entity_type'] = $this->session->userdata('userType');
        $login_data['agent_id'] = $this->session->userdata('agent_id');
        $login_data['entity_id'] = $this->session->userdata('partner_id');

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
    function get_addbooking_form($phone_number = "") { 
        $this->checkUserSession();
        if (!empty($phone_number)) {
            $_POST['phone_number'] = $phone_number;
        }
        $this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|required|regex_match[/^[6-9]{1}[0-9]{9}$/]');

        if ($this->form_validation->run() == FALSE) {
            $output = "Please Enter Valid Mobile Number";
            $userSession = array('error' => $output);
            $this->session->set_userdata($userSession);
            redirect(base_url() . "partner/home");
        } else {
            $phone_number = $this->input->post('phone_number');
            $data['user'] = $this->user_model->get_users_by_any(array("users.phone_number" => $phone_number));
            $partner_id = $this->session->userdata('partner_id');
            $partner_data = $this->partner_model->get_partner_code($partner_id);
            $partner_type = $partner_data[0]['partner_type'];
            $data['partner_type'] = $partner_type;

            $data['partner_code'] = $partner_data[0]['code'];
            if ($partner_type == OEM ) {

                $data['appliances'] = $this->partner_model->get_partner_specific_services($this->session->userdata('partner_id'));
            } else {
                $data['appliances'] = $services = $this->booking_model->selectservice();
            }
            
            $data['prepaid_amount'] = $this->get_prepaid_amount($this->session->userdata('partner_id'));
            
            $data['phone_number'] = trim($phone_number);
            $this->miscelleneous->load_partner_nav_header();
            //$this->load->view('partner/header');
            $this->load->view('partner/get_addbooking', $data);
            $this->load->view('partner/partner_footer');
        }
    }
    function create_booking_or_query(){
        if($this->input->post('prices')){
            $prices = $this->input->post('prices');
            $where['(service_category LIKE "%Installation%" OR service_category LIKE "%Repair - Out Of Warranty%")'] = NULL;
            foreach($prices as $values){
                $temp = explode("_",$values);
                $serviceChargeID[] = $temp[0];
            }
            $whereIN['id'] = $serviceChargeID;
            $is_exists = $this->reusable_model->get_search_result_data("service_centre_charges","id",$where,NULL,NULL,NULL,$whereIN,NULL,array());
            if(!empty($is_exists)){
               $_POST['product_type'] = 'Delivered';
            }
            else{
                $_POST['product_type'] = 'Shipped';
            }
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
//                if($this->session->userdata('partner_id') == VIDEOCON_ID) { 
//                    $this->create_booking_or_query();
//                }   
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

                log_message('info', ' Partner ' . $this->session->userdata('partner_name') . "  booking response mgs" . print_r($response, true));
                // Decode the response
                $responseData = json_decode($response, TRUE);

                if (isset($responseData['data']['code'])) {

                    if ($responseData['data']['code'] == -1003) {
                        $output = "Order ID Already Exists, Booking ID: " . $responseData['data']['response']['247aroundBookingID'];
                        $userSession = array('success' => $output);
                        $this->session->set_userdata($userSession);

                        redirect(base_url() . "partner/pending_booking");
                    } else if ($responseData['data']['code'] == 247) {
                        $output = "Booking Inserted Successfully, Booking ID: " . $responseData['data']['response']['247aroundBookingID'];
                        $userSession = array('success' => $output);
                        $this->session->set_userdata($userSession);

                        log_message('info', 'Partner ' . $this->session->userdata('partner_name') . "  booking Inserted " . print_r($postData, true));
                        redirect(base_url() . "partner/pending_booking");
                    } else {
                        log_message('info', ' Partner ' . $this->session->userdata('partner_name') . "  booking not Inserted " . print_r($postData, true) . " error mgs" . print_r($responseData['data'], true));
                        $this->insertion_failure($postData);

                        $output = "Sorry, Booking Could Not be Inserted. Please Try Again.";
                        $userSession = array('error' => $output);
                        $this->session->set_userdata($userSession);
                        redirect(base_url() . "partner/pending_booking");
                    }
                } else {
                    log_message('info', 'Partner ' . $this->session->userdata('partner_name') . "  booking not Inserted " . print_r($postData, true) . " error mgs" . print_r($responseData['data'], true));
                    $this->insertion_failure($postData);

                    $output = "Sorry, Booking Could Not Be Inserted. 247around Team Is Looking Into This.";
                    $userSession = array('error' => $output);
                    $this->session->set_userdata($userSession);

                    redirect(base_url() . "partner/pending_booking");
                }
            } else {
                log_message('info', 'Partner ' . $this->session->userdata('partner_name') . "  Authentication failed");
                //echo "Authentication fail:";
                $output = "Authentication Failed. Please Contact to 247Around Team";
                $userSession = array('error' => $output);
                $this->session->set_userdata($userSession);
                redirect(base_url() . "partner/pending_booking");
            }
        } else {
            log_message('info', 'Partner add booking' . $this->session->userdata('partner_name') . " Validation failed ");
            $phone_number = $this->input->post('booking_primary_contact_no');
            $_POST['phone_number'] = $phone_number;
            $this->get_addbooking_form();
        }
    }

    function get_booking_form_data() {
        $booking_date = date('d-m-Y', strtotime($this->input->post('booking_date')));
        $post['partnerName'] = $this->session->userdata('partner_name');
        $post['partner_id'] = $this->session->userdata('partner_id');
        $post['agent_id'] = $this->session->userdata('agent_id');
        $post['name'] = trim($this->input->post('user_name'));
        $post['mobile'] = trim($this->input->post('booking_primary_contact_no'));
        $post['email'] = $this->input->post('user_email');
        $post['address'] = trim($this->input->post('booking_address'));
        $post['pincode'] = trim($this->input->post('booking_pincode'));
        $post['city'] = trim($this->input->post('city'));
        $post['requestType'] = $this->input->post('prices');
        $post['landmark'] = trim($this->input->post('landmark'));
        $post['service_id'] = $this->input->post('service_id');
        $post['brand'] = $this->input->post('appliance_brand');
        $post['productType'] = '';
        $post['category'] = $this->input->post('appliance_category');
        $post['capacity'] = $this->input->post('appliance_capacity');
        $post['model'] = $this->input->post('model_number');
        $post['serial_number'] = $this->input->post('serial_number');
        $post['purchase_date'] = $this->input->post('purchase_date');
        $post['partner_source'] = $this->input->post('partner_source');
        $post['remarks'] = $this->input->post('query_remarks');
        $post['orderID'] = $this->input->post('order_id');
        $post['assigned_vendor_id'] = $this->input->post('assigned_vendor_id');
        $post['upcountry_data'] = $this->input->post('upcountry_data');
        $post['alternate_phone_number'] = $this->input->post('alternate_phone_number');
        $post['booking_date'] = $booking_date;
        $post['partner_type'] = $this->input->post('partner_type');
        $appliance_unit = $this->input->post('appliance_unit');
        if(empty($appliance_unit)){
            $appliance_unit =  1;
        }
        $post['appliance_unit'] = $appliance_unit;
        $post['partner_code'] = $this->input->post('partner_code');
        $post['amount_due'] = $this->input->post('grand_total');
        $post['product_type'] = $this->input->post('product_type');
        $post['appliance_name'] = $this->input->post('appliance_name');
        $post['dealer_name'] = $this->input->post('dealer_name');
        $post['dealer_phone_number'] = $this->input->post('dealer_phone_number');
        $post['dealer_id'] = $this->input->post('dealer_id');
        $post['parent_booking']  = NULL;
        $post['repeat_reason']  = NULL;
        $post['booking_request_symptom'] = $this->input->post('booking_request_symptom');
        if($this->input->post('parent_booking')){
            $post['parent_booking'] = $this->input->post('parent_booking');
        }
        if($this->input->post('repeat_reason')){
            $post['repeat_reason'] = $this->input->post('repeat_reason');
        }
        return $post;
    }

    function insertion_failure($post) {
        $to = DEVELOPER_EMAIL;
        $cc = "";
        $bcc = "";
        $subject = "Booking Insertion Failure By " . $this->session->userdata('partner_name');
        $message = $post;
        $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, $bcc, $subject, $message, "",BOOKING_INSERTION_FAILURE);
    }

    function set_form_validation() {
        $this->form_validation->set_rules('user_name', 'User Name', 'required');
        $this->form_validation->set_rules('booking_primary_contact_no', 'Mobile Number', 'trim|required|exact_length[10]');
        $this->form_validation->set_rules('city', 'City', 'trim|required');
        $this->form_validation->set_rules('booking_address', 'Booking Address', 'required');
        $this->form_validation->set_rules('landmark', 'LandMark', 'trim');
        $this->form_validation->set_rules('appliance_capacity', 'Appliance Capacity', 'trim');
        $this->form_validation->set_rules('alternate_phone_number', 'Alternate Number', 'trim');
        $this->form_validation->set_rules('model_number', 'Model Number', 'trim');
        $this->form_validation->set_rules('order_id', 'Order ID', 'trim');
        $this->form_validation->set_rules('serial_number', 'Serial Number', 'trim');
        $this->form_validation->set_rules('appliance_category', 'Appliance Category', 'required');
        $this->form_validation->set_rules('partner_source', 'Booking Source', 'required');
        $this->form_validation->set_rules('service_id', 'Service Name', 'required');
        $this->form_validation->set_rules('booking_date', 'Booking Date', 'required');
        $this->form_validation->set_rules('query_remarks', 'Problem Description', 'required');
        $this->form_validation->set_rules('booking_pincode', 'Booking Pincode', 'trim|required|exact_length[6]');
        $this->form_validation->set_rules('prices', 'Service Category', 'required');
        $this->form_validation->set_rules('grand_total', 'Grand Total', 'trim');
        $this->form_validation->set_rules('dealer_name', 'Dealer Name', 'trim');
        $this->form_validation->set_rules('dealer_phone_number', 'Dealer Phone Number', 'trim');

        if ($this->form_validation->run() == FALSE) {
            return FALSE;
        } else {
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
        $results['select_state'] = $this->vendor_model->get_allstates();
        $saas_flag = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $partner_code = $this->partner_model->get_availiable_partner_code();
        foreach ($partner_code as $row) {
            $code[] = $row['code']; // add each partner code to the array
        }
        $results['partner_code'] = $code;
        $all_partner_code = $this->partner_model->get_all_partner_code('code', array('R', 'S', 'P', 'L', 'M'));
        foreach ($all_partner_code as $row) {
            $all_code[] = $row['code']; 
        }
        $results['all_partner_code'] = $all_code;
        $employee_list = $this->employee_model->get_employee_by_group(array("groups NOT IN ('developer') AND active = '1'" => NULL));
        $results['collateral_type'] = $this->reusable_model->get_search_result_data("collateral_type", '*', array("collateral_tag" => "Contract"), NULL, NULL, array("collateral_type" => "ASC"), NULL, NULL);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/addpartner', array('results' => $results, 'employee_list' => $employee_list,'saas_flag' => $saas_flag));
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
    function process_add_edit_partner_form() { 
        //Check form validation
        $checkValidation = $this->check_partner_Validation();
        if ($checkValidation) {
            $bookings_sources['partner_type'] = $this->input->post('partner_type');
            // Used when we edit a particular Partner
            if (!empty($this->input->post('id'))) { 
                //if vendor exists, details are edited
                $partner_id = $this->input->post('id');
                $edit_partner_data['partner'] = $this->get_partner_form_data();
                //Where Clause
                $where = array('partner_id' => $partner_id);
                //updating Partner code in Bookings_sources table
                $bookings_sources['source'] = $this->input->post('public_name');
                $bookings_sources['code'] = $this->input->post('partner_code');
                if ($this->partner_model->update_partner_code($where, $bookings_sources)) {
                    log_message('info', ' Parnter code has been Updated in Bookings_sources table ' . print_r($bookings_sources, TRUE));
                } else {
                    log_message('info', ' Error in Updating Parnter code has been added in Bookings_sources table ' . print_r($bookings_sources, TRUE));
                }
                $edit_partner_data['partner']['upcountry_max_distance_threshold'] = $edit_partner_data['partner']['upcountry_max_distance_threshold'];
                $edit_partner_data['partner']['update_date'] = date("Y-m-d h:i:s");
                $edit_partner_data['partner']['agent_id'] = $this->session->userdata('id');
                
                /* show notification on partner's panal if grace period increases */
                if($edit_partner_data['partner']['grace_period_date'] > $this->input->post("old_grace_period_date")){
                    $dashboard_data = array(
                        "entity_type" => "partner",
                        "entity_id" => $partner_id,
                        "notification_type" => 8,
                        "message" => "Grace Period extended till ".date("d-m-Y", strtotime($edit_partner_data['partner']['grace_period_date'])),
                        "marquee" => 1,
                        "start_date" => date("Y-m-d H:i:s"),
                        "end_date" => date('Y-m-d H:i:s', strtotime("+1 day", strtotime(date("Y-m-d H:i:s")))),
                    );
                    $this->dashboard_model->insert_dashboard_notification_any($dashboard_data);
                }
                /* End */
                
                /**** Get POC and AM email and send updated fields only ****/
               
                $old_partner_array = $obj2 = array_map('strval', $this->partner_model->viewpartner($this->input->post('id'))[0]);
                $new_partner_array = array_map('strval', $edit_partner_data['partner']);
                $updated_fields=array_diff($new_partner_array, $old_partner_array);
                //unset($updated_fields['update_date']);
                /******* End ********/
                
                $this->partner_model->edit_partner($edit_partner_data['partner'], $partner_id);
                //Getting Logged Employee Full Name
                $logged_user_name = $this->employee_model->getemployeefromid($this->session->userdata('id'))[0]['full_name'];
                //Logging
                log_message('info', __FUNCTION__ . ' Partner has been Updated : ' . print_r($this->input->post(), TRUE));
                $msg = "Partner Updated Successfully";
                $this->session->set_userdata('success', $msg);
                
               
                //Adding details in Booking State Change
                //$this->notify->insert_state_change('', PARTNER_UPDATED, PARTNER_UPDATED, 'Partner ID : ' . $partner_id, $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);
               
                //$am_email = $this->employee_model->getemployeefromid($edit_partner_data['partner']['account_manager_id'])[0]['official_email'];
                $get_partner_am_id = $this->partner_model->getpartner_data("group_concat(distinct agent_filters.agent_id) as account_manager_id", 
                        array('partners.id' => $partner_id),"",0,0,1,"partners.id");
               
                if (!empty($get_partner_am_id[0]['account_manager_id'])) {
                    $am_email = $this->employee_model->getemployeeMailFromID($get_partner_am_id[0]['account_manager_id'])[0]['official_email'];
                }
                if(!empty($am_email)){ 
                    //Sending Mail for Updated details
                    $html = "<p>Following Partner has been Updated :</p><ul>";
                    foreach ($updated_fields as $key => $value) {
                        $html .= "<li><b>" . $key . '</b> =>';
                        $html .= " " . $value . '</li>';
                    }
                    /*
                    foreach ($edit_partner_data['partner'] as $key => $value) {
                        $html .= "<li><b>" . $key . '</b> =>';
                        $html .= " " . $value . '</li>';
                    }
                    */
                    $html .= "</ul>";
                    $to = $am_email. ",". $this->session->userdata("official_email");
                    $cc = ACCOUNTANT_EMAILID.", ".ANUJ_EMAIL_ID;
                    $subject = "Partner Updated :  " . $this->input->post('public_name') . ' - By ' . $logged_user_name;
                    
                    $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $html, "",PARTNER_DETAILS_UPDATED);
                }
                redirect(base_url() . 'employee/partner/editpartner/' . $partner_id);
            } else { 
                //If Partner not present, Partner is being added
                $return_data['partner'] = $this->get_partner_form_data();
                $return_data['partner']['is_active'] = '1';
                $return_data['partner']['is_verified'] = '1';
                //Temporary value
                $return_data['partner']['auth_token'] = substr(md5($return_data['partner']['public_name'] . rand(1, 100)), 0, 16);
                //Getting partner operation regions details from POST
                //$partner_operation_state = $this->input->post('select_state');
                //Getting Partner code
                $code = $this->input->post('partner_code');
                //Add Customer Care Number
                $return_data['partner']['customer_care_contact'] = $this->input->post("customer_care_contact");
                $return_data['partner']['upcountry_max_distance_threshold'] = $return_data['partner']['upcountry_max_distance_threshold'] + 25;
                $partner_id = $this->partner_model->add_partner($return_data['partner']);
                // Save partner default contact person login.
                $this->save_partner_default_contact_person_login($partner_id);
                //Set Flashdata on success or on Error of Data insert in table
                if (!empty($partner_id)) {
                    //Create Login For Partner
                    if($this->input->post('partner_type') == OEM){
                        $loginData['temp_partner_type'] = "OEM";
                        $loginData['temp_partner_source'] = "autologin";
                    }
                    else{
                        $loginData['temp_partner_type'] = "NOT_OEM";
                        $loginData['temp_partner_source'] = "autologin";
                    }
                    $loginData['partner_id'] = $partner_id;
                    $loginData['contact_person_name'][] = $return_data['partner']['primary_contact_name'];
                    $loginData['contact_person_email'][] = $return_data['partner']['primary_contact_email'];
                    $loginData['contact_person_contact'][] = $return_data['partner']['primary_contact_phone_1'];
                    $loginData['final_checkbox_value_holder'] = 'true';
                    $loginData['contact_person_role'][] = PARTNER_POC_ROLE_ID;
                    $sendUrl = base_url().'employee/partner/process_partner_contacts';
                    $this->asynchronous_lib->do_background_process($sendUrl, $loginData);
                    
                    //End Login
                    $msg = "Partner added successfully Please update documents and Operation Regions.";
                    $this->session->set_userdata('success', $msg);
                    //Getting Logged Employee Full Name
                    $logged_user_name = $this->employee_model->getemployeefromid($this->session->userdata('id'))[0]['full_name'];
                    //Echoing inserted ID in Log file
                    log_message('info', __FUNCTION__ . ' New Partner has been added with ID ' . $partner_id . " Done By " . $this->session->userdata('employee_id'));
                    log_message('info', __FUNCTION__ . ' Partner Added Details : ' . print_r($this->input->post(), TRUE));
                    //Adding details in Booking State Change
                    // $this->notify->insert_state_change('', NEW_PARTNER_ADDED, NEW_PARTNER_ADDED, 'Partner ID : ' . $partner_id, $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);
                    //Sending Mail for Updated details
                    /* This is old template for email */
                    $cc="";
                    $html = "<p>Following Partner has been Added :</p><ul>";
                    foreach ($return_data['partner'] as $key => $value) {
                        $html .= "<li><b>" . $key . '</b> =>';
                        $html .= " " . $value . '</li>';
                    }
                    $html .= "</ul>";
                    if( $this->session->userdata("official_email") == ANUJ_EMAIL_ID){
                        $to = ANUJ_EMAIL_ID;
                    } else {
                        $to = ANUJ_EMAIL_ID. ",". $this->session->userdata("official_email");
                    }
                    $subject = "New Partner Added " . $this->input->post('public_name') . ' - By ' . $logged_user_name;
                    $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $html, "", NEW_PARTNER_ADDED_EMAIL_TAG);
                     
                    // Send new brand onboard notification email to all employee
                    $email_template = $this->booking_model->get_booking_email_template(NEW_PARTNER_ONBOARD_NOTIFICATION);
                    if(!empty($email_template)){
                        $template = array(
                            'table_open' => '<table border="1" cellpadding="4" cellspacing="0">'
                        );
                        $this->table->set_template($template);
                        $this->table->set_heading(array('Company Name', 'Public Name', 'Partner Type'));
                        $this->table->add_row(array($this->input->post('company_name'),$this->input->post('public_name'), $this->input->post('partner_type')));
                        $html_table = $this->table->generate();
                        
                        $to = $email_template[1];//ALL_EMP_EMAIL//all-emp@247around.com;

                        $cc = $email_template[3];
                        $subject = vsprintf($email_template[4], array($this->input->post('public_name')));
                        $message = vsprintf($email_template[0], array($html_table));
                        $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $message, "", NEW_PARTNER_ONBOARD_NOTIFICATION);
                    }
                    
                    //Adding Partner code in Bookings_sources table
                    $bookings_sources['source'] = $this->input->post('public_name');
                    $bookings_sources['code'] = $code;
                    $bookings_sources['partner_id'] = $partner_id;
                    $partner_code = $this->partner_model->add_partner_code($bookings_sources);
                    if ($partner_code) {
                        log_message('info', ' Parnter code has been added in Bookings_sources table ' . print_r($bookings_sources, TRUE));
                    } else {
                        log_message('info', ' Error in adding Parnter code has been added in Bookings_sources table ' . print_r($bookings_sources, TRUE));
                    }
                } else {
                    $msg = "Error in adding Partner.";
                    $this->session->set_userdata('error', $msg);
                    //Echoing message in Log file
                    log_message('error', __FUNCTION__ . ' Error in adding Partner  ' . print_r($this->input->post(), TRUE));
                    $partner_id = 0;
                }
                redirect(base_url() . 'employee/partner/editpartner/' . $partner_id);
            }
        } else {
            $this->get_add_partner_form();
        }
    }
    
    /**
     * Function creates default contact person login for partner.
     * @param type $partner_id
     */
    function save_partner_default_contact_person_login($partner_id) {
        $password = mt_rand(100000, 999999);
        $loginData['entity_id'] = $partner_id;
        $loginData['entity'] = $loginData['entity_name'] = _247AROUND_PARTNER_STRING;
        $loginData['user_id'] = _247AROUND_EMPLOYEE_STRING."_".mt_rand(1,5);
        $loginData['password'] = md5($password);
        $loginData['clear_password'] = $password;
        $loginData['contact_person_id'] = PARTNER_DEFAULT_CONTACT_PERSON_ID;
        $loginData['create_date'] = date('Y-m-d H:i:s');
        $loginData['active'] = 1;
        $agent_id = $this->miscelleneous->create_entity_login($loginData);
        return $agent_id;
    }
    
    function get_partner_form_data() {
        $return_data['company_name'] = trim($this->input->post('company_name'));
        $return_data['company_type'] = trim($this->input->post('company_type'));
        $return_data['public_name'] = trim($this->input->post('public_name'));
        $return_data['address'] = trim($this->input->post('address'));
        $return_data['landmark'] = trim($this->input->post('landmark'));
        $return_data['state'] = trim($this->input->post('state'));
        $return_data['district'] = trim($this->input->post('district'));
        $return_data['pincode'] = trim($this->input->post('pincode'));
        $return_data['primary_contact_name'] = trim($this->input->post('primary_contact_name'));
        $return_data['primary_contact_email'] = trim($this->input->post('primary_contact_email'));
        $return_data['customer_care_contact'] = $this->input->post('customer_care_contact');
        $return_data['primary_contact_phone_1'] = trim($this->input->post('primary_contact_phone_1'));
        $return_data['primary_contact_phone_2'] = trim($this->input->post('primary_contact_phone_2'));
        $return_data['owner_name'] = trim($this->input->post('owner_name'));
        $return_data['owner_email'] = trim($this->input->post('owner_email'));
        $return_data['owner_alternate_email'] = trim($this->input->post('owner_alternate_email'));
        $return_data['owner_phone_1'] = trim($this->input->post('owner_phone_1'));
        $return_data['owner_phone_2'] = trim($this->input->post('owner_phone_2'));
        $return_data['summary_email_to'] = $this->input->post('summary_email_to');
        $return_data['summary_email_cc'] = $this->input->post('summary_email_cc');
        $return_data['invoice_email_to'] = $this->input->post('invoice_email_to');
        $return_data['invoice_email_cc'] = $this->input->post('invoice_email_cc');
        $return_data['invoice_courier_name'] = trim($this->input->post('invoice_courier_name'));
        $return_data['invoice_courier_address'] = trim($this->input->post('invoice_courier_address'));
        $return_data['invoice_courier_phone_number'] = trim($this->input->post('invoice_courier_phone_number'));
        $return_data['is_def_spare_required'] = $this->input->post('is_def_spare_required');
        $partner_code = $this->input->post('partner_code');
        //$return_data['account_manager_id'] = $this->input->post('account_manager_id');
        $return_data['spare_notification_email'] = $this->input->post('spare_notification_email');
        $return_data['prepaid_amount_limit'] = $this->input->post('prepaid_amount_limit');
        $return_data['prepaid_notification_amount'] = $this->input->post('prepaid_notification_amount');
        $return_data['grace_period_date'] = $this->input->post('grace_period_date');
        $return_data['oot_spare_to_be_shipped'] = $this->input->post('oot_spare_to_be_shipped');
        $return_data['is_wh'] = $this->input->post('is_wh');
        if(empty($return_data['is_wh'])) {
            $return_data['is_wh'] = $this->input->post('is_warehouse');
        }
        $is_prepaid = $this->input->post('is_prepaid');
        $return_data['is_prepaid'] = 2; // Default set
        if ($is_prepaid == 1) {
            $return_data['is_prepaid'] = 1;
            if($return_data['prepaid_amount_limit'] > 0){
                $return_data['prepaid_amount_limit'] = - $return_data['prepaid_amount_limit'];
            }
            
        }
        $postpaid = $this->input->post('is_postpaid');
        if($postpaid == 1){
            $return_data['is_prepaid'] = 0;
        }
        
        $return_data['postpaid_credit_period'] = $this->input->post('postpaid_credit_period');
        $return_data['postpaid_notification_limit'] = $this->input->post('postpaid_notification_limit');
        $return_data['postpaid_grace_period'] = $this->input->post('postpaid_grace_period');
        if (empty($partner_code)) {
            $return_data['is_active'] = 0;
        }

        if ($this->input->post('is_reporting_mail') == 'on') {
            $return_data['is_reporting_mail'] = '1';
        } else {
            $return_data['is_reporting_mail'] = '0';
        }

        if($this->input->post('is_review')){
            $return_data['booking_review_for'] = 'Cancelled';
        }
        if($this->input->post('review_time_limit')){
         $return_data['review_time_limit'] = $this->input->post('review_time_limit');
        }
        //Checking for Upcountry
        $upcountry = $this->input->post('is_upcountry');
        if (isset($upcountry) && $upcountry == 'on') {
            //Setting Flag as 1
            $return_data['is_upcountry'] = 1;
            $return_data['upcountry_rate'] = $this->input->post('upcountry_rate');
            $return_data['upcountry_min_distance_threshold'] = $this->input->post('upcountry_min_distance_threshold');
            $return_data['upcountry_max_distance_threshold'] = $this->input->post('upcountry_max_distance_threshold');
            $return_data['upcountry_rate1'] = $this->input->post('upcountry_rate1');
            $return_data['upcountry_mid_distance_threshold'] = $this->input->post('upcountry_mid_distance_threshold');
            $return_data['upcountry_approval_email'] = $this->input->post('upcountry_approval_email');
            $upcountry_approval = $this->input->post('upcountry_approval');
            $return_data['upcountry_approval'] = (!empty($upcountry_approval)) ? 1 : 0;
            $return_data['upcountry_bill_to_partner'] =$this->input->post('upcountry_bill_to_partner');
        } else {
            $return_data['is_upcountry'] = 0;
            $return_data['upcountry_rate'] = 0;
            $return_data['upcountry_min_distance_threshold'] = 0;
            $return_data['upcountry_max_distance_threshold'] = 0;
            $return_data['upcountry_rate1'] = 0;
            $return_data['upcountry_mid_distance_threshold'] = 0;
            $return_data['upcountry_approval_email'] = NULL;
            $return_data['upcountry_approval'] = 0;
            $return_data['upcountry_bill_to_partner'] = 0;
        }
//        $partner_data_final['partner'] = $return_data;
        return $return_data;
    }

    /**
     * @desc: This function is used to check validation of Add/Edit Partner form
     *
     * @param: void
     * @return : If validation ok returns true else false
     */
    function check_partner_Validation() {
        $this->form_validation->set_rules('company_name', 'Company Name', 'trim|required');
        $this->form_validation->set_rules('public_name', 'Public Name', 'trim|required');
        $this->form_validation->set_rules('address', 'Partner Address', 'trim|required');
        $this->form_validation->set_rules('state', 'State', 'trim|required');
        $this->form_validation->set_rules('district', 'District', 'trim|required');
        $this->form_validation->set_rules('partner_type', 'Partner Type', 'trim|required');
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
        $this->checkEmployeeUserSession();
        $partner_not_like ='';
        $partnerType = '';
        $service_brands = array();
        $active = 1;
        $ac= 'All';
        if($this->input->post()){
           $active = $this->input->post('active');
           $partnerType = $this->input->post('partnerType');
           $ac = $this->input->post('accountManager');
        }
        if(!$partnerType){
           $partner_not_like = INTERNALTYPE;
           $partnerType= array(OEM, EXTWARRANTYPROVIDERTYPE, ECOMMERCETYPE);
        }
        $query = $this->partner_model->get_partner_details_with_soucre_code($active,$partnerType,$ac,$partner_not_like,$partner_id, null);
        foreach ($query as $key => $value) {
            //Getting Appliances and Brands details for partner
            $service_brands[] = $this->partner_model->get_service_brands_for_partner($value['id']);
        }
        $pushNotification = $this->push_notification_model->get_push_notification_subscribers_by_entity(_247AROUND_PARTNER_STRING);
        $accountManagerArray = $this->reusable_model->get_search_result_data("employee","id,employee_id,full_name",array('active'=>1),NULL,NULL,array('employee_id'=>"DESC"),NULL,NULL,array());
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/viewpartner', array('query' => $query, 'service_brands' => $service_brands,'push_notification' => $pushNotification,'active'=>$active,'partnerType'=>$partnerType,
            'accountManagerArray'=>$accountManagerArray,'ac'=>$ac));
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

        //$get_partner_details = $this->partner_model->getpartner_details('partners.public_name,account_manager_id,primary_contact_email,owner_email', array('partners.id' => $id));
        $get_partner_details = $this->partner_model->getpartner_data("partners.public_name,group_concat(distinct agent_filters.agent_id) as account_manager_id,primary_contact_email,owner_email", 
                        array('partners.id' => $id),"",0,1,1,"partners.id");
        $am_email = "";
        if (!empty($get_partner_details[0]['account_manager_id'])) {
            //$am_email = $this->employee_model->getemployeefromid($get_partner_details[0]['account_manager_id'])[0]['official_email'];
            $am_email = $this->employee_model->getemployeeMailFromID($get_partner_details[0]['account_manager_id'])[0]['official_email'];
        }
        
        $data = array('is_active' => 1,
                       'agent_id' => $this->session->userdata('id'),
                        'update_date' => date('Y-m-d H:i:s'));
        $result = $this->partner_model->activate($id,$data);

        if (!empty($result)) {

            //Storing State change values in Booking_State_Change Table
            $this->notify->insert_state_change('', _247AROUND_PARTNER_ACTIVATED, _247AROUND_PARTNER_DEACTIVATED, 'Partner ID = ' . $id, $this->session->userdata('id'), 
                    $this->session->userdata('employee_id'),ACTOR_NOT_DEFINE, NEXT_ACTION_NOT_DEFINE, $id);
            //send email
            $email_template = $this->booking_model->get_booking_email_template("partner_activate_email");
            $to = $get_partner_details[0]['primary_contact_email'] . "," . $get_partner_details[0]['owner_email'];
            $cc = $email_template[3] . "," . $am_email;
            $subject = $email_template[4];
            $message = $email_template[0];

            $this->notify->sendEmail($email_template[2], $to, $cc, "", $subject, $message, "",'partner_activate_email');
            $this->session->set_userdata(array('success' => 'Partner Activated Successfully'));
            log_message("info", __METHOD__ . " Partner Id " . $id . " Updated by " . $this->session->userdata('id'));
        } else {
            $this->session->set_userdata(array('error' => 'Error In Activating Partner'));
            log_message("info", __METHOD__ . " Error In Updating Partner Id " . $id . " by " . $this->session->userdata('id'));
        }

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

        //$get_partner_details = $this->partner_model->getpartner_details('partners.public_name,account_manager_id,primary_contact_email,owner_email', array('partners.id' => $id));
        $get_partner_details = $this->partner_model->getpartner_data("partners.public_name,group_concat(distinct agent_filters.agent_id) as account_manager_id,primary_contact_email,owner_email", 
                        array('partners.id' => $id),"",0,1,1,"partners.id");
        $am_email = "";
        if (!empty($get_partner_details[0]['account_manager_id'])) {
            //$am_email = $this->employee_model->getemployeefromid($get_partner_details[0]['account_manager_id'])[0]['official_email'];
            $am_email = $this->employee_model->getemployeeMailFromID($get_partner_details[0]['account_manager_id'])[0]['official_email'];
        }
        $data = array('is_active' => 0,
                       'agent_id' => $this->session->userdata('id'),
                        'update_date' => date('Y-m-d H:i:s'));
        $result = $this->partner_model->deactivate($id,$data);
        if (!empty($result)) {

            //Storing State change values in Booking_State_Change Table
            $this->notify->insert_state_change('', _247AROUND_PARTNER_DEACTIVATED, _247AROUND_PARTNER_ACTIVATED, 'Partner ID = ' . $id, $this->session->userdata('id'), 
                    $this->session->userdata('employee_id'), ACTOR_NOT_DEFINE, NEXT_ACTION_NOT_DEFINE, $id);

            //send email

            $email_template = $this->booking_model->get_booking_email_template("partner_deactivate_email");
            $to = $get_partner_details[0]['primary_contact_email'] . "," . $get_partner_details[0]['owner_email'];
            $cc = $email_template[3] . "," . $am_email;
            $subject = $email_template[4];
            $message = $email_template[0];

            $this->notify->sendEmail($email_template[2], $to, $cc, "", $subject, $message, "",'partner_deactivate_email');
            $this->session->set_userdata(array('success' => 'Partner De-activated Successfully'));
            log_message("info", __METHOD__ . " Partner Id " . $id . " Updated by " . $this->session->userdata('id'));
        } else {
            $this->session->set_userdata(array('error' => 'Error In De-activating Partner'));
            log_message("info", __METHOD__ . " Error In Updating Partner Id " . $id . " by " . $this->session->userdata('id'));
        }


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
        log_message('info', __FUNCTION__ . ' partner_id:' . $id);
        $query = $this->partner_model->viewpartner($id);
        $results['select_state'] = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state) as state",NULL,NULL,NULL,array('state'=>'ASC'),NULL,NULL,array());
        $results['services'] = $this->vendor_model->selectservice();
        $saas_flag = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $partner_am_flag = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_STATE_APPLIANCE);
        //Getting Login Details for this partner
        $results['partner_code'] = $this->partner_model->get_partner_code($id);
        $partner_code = $this->partner_model->get_availiable_partner_code();
        foreach ($partner_code as $row) {
            $code[] = $row['code']; // add each partner code to the array
        }
        $results['partner_code_availiable'] = $code;
        $partner_code_arr = ((isset($saas_flag) && !$saas_flag) ? array('R', 'S', 'P', 'L', 'M') : array('Z'));
        $all_partner_code = $this->partner_model->get_all_partner_code('code', $partner_code_arr);
        foreach ($all_partner_code as $row) {
            $all_code[] = $row['code']; 
        }
        if(isset($all_code))
        $results['all_partner_code'] = $all_code;
        //Getting Sample no picture details
        $sample_no_pic_arr=$this->reusable_model->get_search_result_data('partner_sample_no_picture','*',array('partner_id'=>$id,'active'=>'1'),NULL,NULL,array(),NULL,NULL,array());
        $results['sample_no_pic']=$sample_no_pic_arr;
        //Getting Parnter Operation Region Details
        $where = array('partner_id' => $id);
        $group_by_arr=array('collateral.file','collateral.collateral_id');
        $results['partner_operation_region'] = $this->partner_model->get_partner_operation_region($where);
        $results['brand_mapping'] = $this->partner_model->get_partner_specific_details($where, "service_id, brand, active");
        $results['partner_contracts'] = $this->reusable_model->get_search_result_data("collateral", 'collateral.id,collateral.document_description,collateral.file,collateral.is_file,collateral.start_date,collateral.model,'
                . 'collateral.end_date,collateral_type.collateral_type,collateral_type.collateral_tag,services.services,collateral.brand,collateral.category,collateral.capacity,'
                . 'collateral_type.document_type,GROUP_CONCAT(DISTINCT collateral.request_type) as request_type,collateral.appliance_id,collateral.collateral_id',
                array("entity_id" => $id, "entity_type" => "partner","is_valid"=>1), array("collateral_type" => "collateral_type.id=collateral.collateral_id","services"=>"services.id=collateral.appliance_id"), 
                NULL, NULL, NULL, array('services'=>'LEFT'),$group_by_arr);
        $results['collateral_type'] = $this->reusable_model->get_search_result_data("collateral_type", '*', array("collateral_tag" => "Contract"), NULL, NULL, array("collateral_type" => "ASC"), NULL, NULL);
        $employee_list = $this->employee_model->get_employee_by_group(array("groups IN ('accountmanager') AND active = '1'" => NULL));
        $departmentArray = $this->reusable_model->get_search_result_data("entity_role", 'DISTINCT department',array("entity_type" => 'partner'),NULL, NULL, array('department'=>'ASC'), NULL, NULL,array());  
        $results['contact_persons'] =  $this->reusable_model->get_search_result_data("contact_person",  "contact_person.*,entity_role.role,entity_role.id as  role_id,entity_role.department,"
                . "GROUP_CONCAT(agent_filters.state) as  state,entity_login_table.agent_id as login_agent_id,contact_person.is_active,"
                . "entity_login_table.active as login_active",
                array("contact_person.entity_type" =>  "partner","contact_person.entity_id"=>$id),
                array("entity_role"=>"contact_person.role = entity_role.id","agent_filters"=>"contact_person.id=agent_filters.contact_person_id","entity_login_table"=>"entity_login_table.contact_person_id = contact_person.id"), NULL, 
                array("name"=>'ASC'), NULL,  array("agent_filters"=>"left","entity_role"=>"left","entity_login_table"=>"left"),array("contact_person.id"));
       $results['contact_name'] = $this->partner_model->select_contact_person($id);

       $is_wh = $this->reusable_model->get_search_result_data("partners","is_wh",array('id'=>$id),NULL,NULL,NULL,NULL,NULL,array());
       $results['bank_detail'] = $this->reusable_model->get_search_result_data("account_holders_bank_details", '*',array("entity_id"=>$id, "entity_type" => 'partner'),NULL, NULL, array('is_active'=>'DESC'), NULL, NULL, array()); 
       $results['variable_charges'] = $this->accounting_model->get_vendor_partner_variable_charges("fixed_charges, vendor_partner_variable_charges.validity_in_month, vendor_partner_variable_charges.id as partner_charge_id, vendor_partner_variable_charges.status, variable_charges_type.*", array('entity_type'=>'partner', 'entity_id'=>$id), true);
       $charges_type = $this->accounting_model->get_variable_charge("id, type, description");
       $select = 'micro_wh_mp.id,micro_wh_mp.state, micro_wh_mp.active,micro_wh_mp.vendor_id,micro_wh_mp.id as wh_on_of_id,micro_wh_mp.update_date,service_centres.name,micro_wh_mp.id as micro_wh_mp_id,micro_wh_mp.micro_warehouse_charges';
       $micro_wh_lists = $this->inventory_model->get_micro_wh_lists_by_partner_id($select, array('micro_wh_mp.partner_id' => $id)); 
       $results['partner_am_mapping'] = $this->partner_model->getpartner_data("partners.public_name, agent_filters.*, employee.full_name, employee.groups", array("partners.id" => $id, "agent_filters.entity_id IS NOT NULL" => NULL),"",TRUE,0,1);
       $this->miscelleneous->load_nav_header();
       $this->load->view('employee/addpartner', array('query' => $query, 'results' => $results, 'employee_list' => $employee_list, 'form_type' => 'update','department'=>$departmentArray, 
           'charges_type'=>$charges_type, 'micro_wh_lists'=>$micro_wh_lists,'is_wh'=>$is_wh,'saas_flag' => $saas_flag));
    }

    /**
     * @desc: This is used to get find user form in Partner CRM
     * params: void
     * return: View form to find user
     */
    function get_user_form() {
        $this->checkUserSession();
        $this->miscelleneous->load_partner_nav_header();
        //$this->load->view('partner/header');
        $this->load->view('partner/finduser');
        $this->load->view('partner/partner_footer');
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
        $search_value = trim($this->input->post('search_value'));
        $search_type = trim($this->input->post('optradio'));
        if ($search_type === 'phone_number') {
            $phone_number = $this->input->post('phone_number');
        }

        if ($phone_number != "") {
            $page = 0;

            if ($page == 0) {
                $page = 50;
            }

            $offset = ($this->uri->segment(5) != '' ? $this->uri->segment(5) : 0);

            $config['base_url'] = base_url() . "employee/partner/finduser/" . $offset . "/" . $page . "/" . $search_value;

            $output_data = $this->user_model->search_by_partner($phone_number, $this->session->userdata('partner_id'), $offset, $page);
            if (!empty($output_data)) {
                $config['per_page'] = $page;
                $config['uri_segment'] = 7;
                $config['first_link'] = 'First';
                $config['last_link'] = 'Last';

                $this->pagination->initialize($config);
                $data['links'] = $this->pagination->create_links();

                $data['data'] = $output_data;
                //$this->load->view('partner/header');
                $this->miscelleneous->load_partner_nav_header();
                $this->load->view('partner/bookinghistory', $data);
                $this->load->view('partner/partner_footer');
            } else {
                $msg = "User Not Exist.";
                $this->session->set_userdata('error', $msg);

                redirect(base_url() . 'employee/partner/get_user_form');
            }
        } else if ($search_type === 'booking_id') {  //if booking id given and matched, will be displayed
            $where['booking_details.booking_id LIKE "%'.$search_value.'%"'] = NULL;
            $Bookings = $this->booking_model->search_bookings($where, $this->session->userdata('partner_id'));
            $data['data'] = json_decode(json_encode($Bookings), True);
            $this->miscelleneous->load_partner_nav_header();
           // $this->load->view('partner/header');
            $this->load->view('partner/bookinghistory', $data);
            $this->load->view('partner/partner_footer');
        } else if ($search_type === 'order_id') {
            $where['order_id LIKE "%'.$search_value.'%"'] = NULL;
            $Bookings = $this->booking_model->search_bookings($where, $this->session->userdata('partner_id'));
            $data['data'] = json_decode(json_encode($Bookings), True);
            $this->miscelleneous->load_partner_nav_header();
            //$this->load->view('partner/header');
            $this->load->view('partner/bookinghistory', $data);
            $this->load->view('partner/partner_footer');
        } else if ($search_type === 'serial_number') {
            $serialNumberSearc = "(partner_serial_number LIKE '%".$search_value."%' OR serial_number LIKE '%".$search_value."%')";
            $where[$serialNumberSearc] = NULL;
            $Bookings = $this->booking_model->search_bookings($where, $this->session->userdata('partner_id'));
            $data['data'] = json_decode(json_encode($Bookings), True);
            $data['search'] = "Search";
            $this->miscelleneous->load_partner_nav_header();
            //$this->load->view('partner/header');
            $this->load->view('partner/bookinghistory', $data);
            $this->load->view('partner/partner_footer');
        } else {
            $msg = "User Not Exist.";
            $this->session->set_userdata('error', $msg);

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
//        $data['vendor_partner'] = "partner";
//        $data['vendor_partner_id'] = $partner_id;
//        $invoice['invoice_array'] = $this->invoices_model->getInvoicingData($data);
        $where = array(
                'partner_id' => $partner_id,
                'partner_invoice_id is null' => NULL,
                'create_date >= "2017-01-01" ' => NULL,
                'partner_net_payable > 0 '=> NULL,
                'booking_status IN ("' . _247AROUND_PENDING . '", "'  . _247AROUND_COMPLETED . '")' => NULL
        );
        // sum of partner payable amount whose booking is in followup, pending and completed(Invoice not generated) state.
        
        $unbilled_data  = $this->booking_model->get_unit_details($where, false, 'booking_id, partner_net_payable, create_date, booking_status');
        
        $unbilled_amount = 0;
        $msic_charge = 0;
        if(!empty($unbilled_data)){
            $unbilled_amount = (array_sum(array_column($unbilled_data, 'partner_net_payable')));
        }
        
        $misc_select = 'miscellaneous_charges.partner_charge,booking_details.current_status, miscellaneous_charges.booking_id, miscellaneous_charges.description, miscellaneous_charges.create_date';

        $misc = $this->invoices_model->get_misc_charges_invoice_data($misc_select, "miscellaneous_charges.partner_invoice_id IS NULL", false, FALSE, "booking_details.partner_id", $partner_id, "partner_charge");
        if(!empty($misc)){
            $msic_charge = (array_sum(array_column($unbilled_data, 'partner_charge')));
        }
        
        $upcountry = $this->upcountry_model->getupcountry_for_partner_prepaid($partner_id);
        $upcountry_basic = 0;
        if(!empty($upcountry)){
            $upcountry_basic = $upcountry[0]['total_upcountry_price'];
            
        }
        $invoice['upcountry'] = $upcountry_basic;
        $invoice['misc'] = $misc;
        $invoice['unbilled_amount'] = ($unbilled_amount + $upcountry_basic + $msic_charge)* (1 + SERVICE_TAX_RATE);
        $invoice['unbilled_data'] = $unbilled_data;
        $invoice['invoice_amount'] = $this->invoices_model->get_summary_invoice_amount("partner", $partner_id)[0];
        $this->miscelleneous->load_partner_nav_header();
        //$this->load->view('partner/header');
        $this->load->view('partner/invoice_summary', $invoice);
        $this->load->view('partner/partner_footer');
    }
    
    function get_bank_transaction(){
         $this->checkUserSession();
         //$partner_id = $this->session->userdata('partner_id');
         //$data2['partner_vendor'] = "partner";
         //$data2['partner_vendor_id'] = $partner_id;
         //$invoice['bank_statement'] = $this->invoices_model->get_bank_transactions_details('*', $data2);
         //$this->load->view('partner/header');
         $this->miscelleneous->load_partner_nav_header();
         $this->load->view('partner/bank_transaction');
         $this->load->view('partner/partner_footer');
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
        log_message('info', __FUNCTION__ . " Booking ID: " . $booking_id . ' Status: ' . $status);
        $data['user_and_booking_details'] = $this->booking_model->getbooking_history($booking_id);
        if (!empty($data['user_and_booking_details'])) {
            $where = array('reason_of' => 'partner');
            $data['reason'] = $this->booking_model->cancelreason($where);
            $data['status'] = $status;
            //$this->load->view('partner/header');
            $this->miscelleneous->load_partner_nav_header();
            $this->load->view('partner/cancel_form', $data);
            $this->load->view('partner/partner_footer');
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
        log_message('info', __FUNCTION__ . " Booking ID: " . print_r($booking_id, true) . ' status: ' . $status);
        $cancellation_reason = $this->input->post('cancellation_reason');
        $historyRemarks = $this->input->post('remarks');

        $partner_id = $this->input->post("partner_id");
        $agent_id = $this->session->userdata('agent_id');

        $this->miscelleneous->process_cancel_form($booking_id, $status, $cancellation_reason, $historyRemarks, $agent_id, $this->session->userdata('partner_name'), $partner_id, $partner_id);

        redirect(base_url() . "partner/get_user_form");
    }

    /**
     * @desc: This method calls for cancel booking to update booking unit details
     * @param: String $booking_id
     */
    function update_price_while_cancel_booking($booking_id) {
        log_message('info', __FUNCTION__ . " Booking Id  " . print_r($booking_id, true));
        $unit_details['booking_status'] = "Cancelled";

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
            $this->miscelleneous->load_partner_nav_header();
           // $this->load->view('partner/header');
            $this->load->view('partner/reschedulebooking', array('data' => $getbooking));
            $this->load->view('partner/partner_footer');
        } else {
            echo "This Id doesn't Exists";
        }
    }

    function process_reschedule_booking($booking_id) {
        log_message('info', __FUNCTION__ . " Booking Id  " . print_r($booking_id, true));
        $this->checkUserSession();
        $this->form_validation->set_rules('booking_date', 'Booking Date', 'trim|required');
        $is_booking_able_to_reschedule = $this->booking_creation_lib->is_booking_able_to_reschedule($booking_id, $this->input->post('service_center_closed_date'));
        if ($this->form_validation->run() == FALSE || $is_booking_able_to_reschedule === FALSE) {
            if($is_booking_able_to_reschedule === FALSE) {
                $this->session->set_userdata(['error' => 'Booking can not be rescheduled because booking is already closed by service center.']);
            }
            $this->get_reschedule_booking_form($booking_id);
        } else {
            log_message('info', __FUNCTION__ . " Booking Id  " . $booking_id);
            $booking_date = $this->input->post('booking_date');

            $data['booking_date'] = date('d-m-Y', strtotime($booking_date));
//            $data['current_status'] = 'Rescheduled';
//            $data['internal_status'] = 'Rescheduled';
            $data['update_date'] = date("Y-m-d H:i:s");
            $rescheduled_reason = NULL;
            if($this->input->post('r_reason')){
                $rescheduled_reason = $this->input->post('r_reason');
            }
            $data['reschedule_reason'] = $rescheduled_reason;

            //check partner status from partner_booking_status_mapping table  
//            $partner_id = $this->input->post('partner_id');
            $actor = $next_action = 'not_define';
//            $partner_status = $this->booking_utilities->get_partner_status_mapping_data($data['current_status'], $data['internal_status'], $partner_id, $booking_id);
//            if (!empty($partner_status)) {
//                $data['partner_current_status'] = $partner_status[0];
//                $data['partner_internal_status'] = $partner_status[1];
//                $actor = $data['actor'] = $partner_status[2];
//                $next_action = $data['next_action'] = $partner_status[3];
//            }

            $update_status = $this->booking_model->update_booking($booking_id, $data);
            if ($update_status) {
                $this->notify->insert_state_change($booking_id, _247AROUND_RESCHEDULED, _247AROUND_PENDING, " Rescheduled Booking BY Partner ", $this->session->userdata('agent_id'), 
                        $this->session->userdata('partner_name'), $actor,$next_action,$this->session->userdata('partner_id'));


//                $service_center_data['internal_status'] = "Pending";
//                $service_center_data['current_status'] = "Pending";
                $service_center_data['update_date'] = date("Y-m-d H:i:s");

                log_message('info', __FUNCTION__ . " Update Service center action table  " . print_r($service_center_data, true));

                $this->vendor_model->update_service_center_action($booking_id, $service_center_data);

                $send_data['booking_id'] = $booking_id;
                $send_data['current_status'] = "Rescheduled";
                $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
                $this->asynchronous_lib->do_background_process($url, $send_data);

                log_message('info', __FUNCTION__ . " Request to prepare Job Card  " . print_r($booking_id, true));

                //Prepare job card
                $job_card = array();
                $job_card_url = base_url() . "employee/bookingjobcard/prepare_job_card_using_booking_id/" . $booking_id;
                $this->asynchronous_lib->do_background_process($job_card_url, $job_card);
                
                
                $email = array();
                $email_url = base_url() . "employee/bookingjobcard/send_mail_to_vendor/" . $booking_id;
                $this->asynchronous_lib->do_background_process($email_url, $email);
                
                $msg = $booking_id . " Booking Rescheduled.";
                $this->session->set_userdata('success', $msg);

                redirect(base_url() . "partner/get_user_form");
            } else {
                log_message('info', __FUNCTION__ . " Booking is not updated  " . print_r($data, true));
            }
        }
    }
    
    function check_escalation_already_applied(){
        if($this->input->post("escalation_reason_id")){
            $escalation_reason_id = $this->input->post("escalation_reason_id");
            $booking_id= $this->input->post('booking_id');
            if(!empty($escalation_reason_id)){
                $where = array("booking_id" => $booking_id, "escalation_reason" => $escalation_reason_id,
                "create_date >=  curdate() " => NULL,  "create_date  between (now() - interval ".PARTNER_PENALTY_NOT_APPLIED_WITH_IN." minute) and now()" => NULL);
                $data =$this->vendor_model->getvendor_escalation_log($where, "*");
                log_message("info", $this->db->last_query());
                if(empty($data)){
                    return true;
                } else {
                    $this->form_validation->set_message('check_escalation_already_applied', 'Booking is already escalated.');
                    return false;
                }
            } else {
              $this->form_validation->set_message('check_escalation_already_applied', 'The Escalation Reason field is required');
              return false;  
            }
        } else {
            $this->form_validation->set_message('check_escalation_already_applied', 'The Escalation Reason field is required');
            return false;
        }    
    }

    /**
     * @desc: Load escalation form  in the partner panel. Partner esclates on booking.
     * That will send notification to 247Around.
     * @param String $booking_id
     */
    function escalation_form($booking_id) {
        log_message('info', __FUNCTION__ . " Booking Id  " . print_r($booking_id, true));
        $this->checkUserSession();
        $data['escalation_reason'] = $this->vendor_model->getEscalationReason(array('entity' => 'partner', 'active' => '1'));
        $data['booking_id'] = $booking_id;
        $this->miscelleneous->load_partner_nav_header();
        //$this->load->view('partner/header');
        $this->load->view('partner/escalation_form', $data);
    }

    /**
     * @desc: This is used to insert escalation into escalation log table. 
     * Upadte escalation log table when mail sent
     * @param String $booking_id
     */
    function process_escalation($booking_id) {
        log_message('info', __FUNCTION__ . ' booking_id: ' . $booking_id);
        $this->checkUserSession();
        $this->form_validation->set_rules('escalation_reason_id', 'Escalation Reason', 'callback_check_escalation_already_applied');

        if ($this->form_validation->run() == FALSE) {
            echo validation_errors();
        } else {

            $escalation['escalation_reason'] = $this->input->post('escalation_reason_id');
            $escalation_remarks = $this->input->post('escalation_remarks');
            $bookinghistory = $this->booking_model->getbooking_history($booking_id);

            $escalation_reason = $this->vendor_model->getEscalationReason(array('id' => $escalation['escalation_reason']));
            if (!empty($escalation_remarks)) {
                $remarks = $escalation_reason[0]['escalation_reason'] . " -" .
                        $escalation_remarks;
            } else {
                $remarks = $escalation_reason[0]['escalation_reason'];
            }

            $escalation['booking_id'] = $booking_id;
            $escalation['booking_date'] = date('Y-m-d', strtotime($bookinghistory[0]['booking_date']));
            $escalation['booking_time'] = $bookinghistory[0]['booking_timeslot'];
            $escalation['vendor_id'] = $bookinghistory[0]['assigned_vendor_id'];

            log_message('info', __FUNCTION__ . " escalation_reason  " . print_r($escalation, true));

            $escalation_id = "";
            if($escalation['vendor_id']){
                //inserts vendor escalation details
                $escalation_id = $this->vendor_model->insertVendorEscalationDetails($escalation);
            }
            $this->notify->insert_state_change($escalation['booking_id'], "Escalation", _247AROUND_PENDING, $remarks, $this->session->userdata('agent_id'), $this->session->userdata('partner_name'), 
                    ACTOR_ESCALATION,NEXT_ACTION_ESCALATION,$this->session->userdata('partner_id'));
            //Send Email
            //get account manager details
                $am_email = "";
                $accountManagerData = $this->miscelleneous->get_am_data($this->session->userdata('partner_id'),$bookinghistory[0]['state']);

                if (!empty($accountManagerData)) {
                    $am_email = $accountManagerData[0]['official_email'];
                }
                
                $partner_details = $this->dealer_model->entity_login(array('agent_id' => $this->session->userdata('agent_id')))[0];
                //Getting template from Database
                $template = $this->booking_model->get_booking_email_template("escalation_on_booking_from_partner_panel");
                if (!empty($template)) {  
                    //From will be currently logged in user
                    $from = $partner_details['email'];
                    //getting rm email
                    $rm_mail = $this->vendor_model->get_rm_sf_relation_by_sf_id($bookinghistory[0]['assigned_vendor_id'])[0]['official_email'];
                    $to = $am_email;
                    $cc = $rm_mail.','.$partner_details['email'];
                    $email['booking_id'] = $booking_id;
                    $email['remarks'] = $remarks;
                    $emailBody = vsprintf($template[0], $email);
                    $subject['booking_id'] = $booking_id;
                    $subjectBody = vsprintf($template[4], $subject);
                    //Sending Mail
                    $this->notify->sendEmail($from, $to, $template[3] . "," . $cc, '', $subjectBody, $emailBody, "",'escalation_on_booking_from_partner_panel', "", $booking_id);
                    //Logging
                    log_message('info', " Escalation Mail Send successfully " . $emailBody);
                } else {
                    //Logging Error Message
                    log_message('info', " Error in Getting Email Template for Escalation Mail");
                }
            if ($escalation_id) {
                log_message('info', __FUNCTION__ . " Escalation Inserted ");
                $this->booking_model->increase_escalation_reschedule($booking_id, "count_escalation");
                
                $reason_flag['escalation_policy_flag'] = json_encode(array('mail_to_escalation_team' => 1), true);

                $this->vendor_model->update_esclation_policy_flag($escalation_id, $reason_flag, $booking_id);

                //Processing Penalty on Escalations when Booking Time solt exceed 1hour
                $last_booking_time_slots = trim(explode('-', $escalation['booking_time'])[1]);

                $time_limit = '';
                if ($last_booking_time_slots == '1PM') {
                    $time = $escalation['booking_date'] . ' 14:01:00';
                    $time_limit = strtotime(date($time));
                } else if ($last_booking_time_slots == '4PM') {
                    $time = $escalation['booking_date'] . ' 16:01:00';
                    $time_limit = strtotime($time);
                } else if ($last_booking_time_slots == '7PM') {
                    $time = $escalation['booking_date'] . ' 21:01:00';
                    $time_limit = strtotime(date($time));
                }

                if (!empty($time_limit)) {
                    $time_difference = $time_limit - strtotime(date('Y-m-d H:i:s'));
                } else {
                    $time_difference = "";
                }

                if (!empty($time_difference) && $time_difference < 0 && !empty($bookinghistory[0]['assigned_vendor_id'])) {
                    $value['booking_id'] = $escalation['booking_id'];
                    $value['assigned_vendor_id'] = $bookinghistory[0]['assigned_vendor_id'];
                    $value['current_state'] = "Escalation";
                    $value['agent_id'] = $partner_details['entity_id'];
                    $value['agent_type'] = 'partner';
                    $value['remarks'] = $escalation_remarks;
                    $where = array('escalation_id' => ESCALATION_PENALTY, 'active' => '1');
                    //Adding values in penalty on booking table
                    $this->penalty_model->get_data_penalty_on_booking($value, $where);

                    log_message('info', 'Penalty added for Escalations - Booking : ' . $escalation['booking_id']);
                }
            }

            log_message('info', __FUNCTION__ . " Exiting");
            echo "success";
        }
    }

    /**
     * @desc: This is used to load update booking form
     * @param String $booking_id
     */
    function get_editbooking_form($booking_id , $is_repeat = NULL) {
        log_message('info', __FUNCTION__ . " Booking Id: " . $booking_id);
        $this->checkUserSession();

        $booking_history = $this->booking_model->getbooking_history($booking_id);
        $data['booking_symptom'] = $this->booking_model->getBookingSymptom($booking_id);

        if (!empty($booking_history)) {
            $data['booking_history'] = $booking_history;
            $partner_id = $this->session->userdata('partner_id');
            $partner_data = $this->partner_model->get_partner_code($partner_id);
            $partner_type = $partner_data[0]['partner_type'];
            $data['partner_type'] = $partner_type;

            $data['partner_code'] = $partner_data[0]['code'];
            if ($partner_type == OEM) {

                $data['appliances'] = $this->partner_model->get_partner_specific_services($partner_id);
            } else {
                $data['appliances'] = $services = $this->booking_model->selectservice();
            }

            $unit_where = array('booking_id' => $booking_id);
            $data['unit_details'] = $this->booking_model->get_unit_details($unit_where);
            $price_tag = $service_category = array();
            foreach ($data['unit_details'] as $unit) {
                $price_tags1 = str_replace('(Free)', '', $unit['price_tags']);
                $price_tags2 = str_replace('(Paid)', '', $price_tags1);
                array_push($service_category, $price_tags2);
                array_push($price_tag, $unit['price_tags']);
            }
            $data['price_tags'] = implode(",", $price_tag);

            if (isset($booking_history[0]['dealer_id']) && !empty($booking_history[0]['dealer_id'])) {

                $condition = array(
                    "where" => array('dealer_details.dealer_id' => $booking_history[0]['dealer_id']),
                    "where_in" => array(),
                    "search" => array(),
                    "order_by" => "");
                $select = "dealer_details.dealer_id, dealer_name, dealer_phone_number_1";
                $condition['length'] = -1;
                $dealer_data = $this->dealer_model->get_dealer_mapping_details($condition, $select);

                if (!empty($dealer_data)) {
                    $data['dealer_data'] = $dealer_data[0];
                }
            }
            $data['symptom'] = array();
            if(!empty($service_category)) {
                $data['symptom'] = $this->booking_request_model->get_booking_request_symptom('symptom.id, symptom',
                        array('symptom.service_id' => $booking_history[0]['service_id'], 'symptom.active' => 1, 'symptom.partner_id' => $partner_id), array('request_type.service_category' => $service_category));
            }
            if(count($data['symptom']) <= 0) {
                $data['symptom'][0] = array('id' => 0, 'symptom' => 'Default');
            }
            
            $data['is_repeat'] = $is_repeat;
            $this->miscelleneous->load_partner_nav_header();
            //$this->load->view('partner/header');
            $this->load->view('partner/edit_booking', $data);
            $this->load->view('partner/partner_footer');
        } else {
            echo "Booking Not Found";
        }
    }

    /**
     * @desc: This method is used to update booking by Partner Panel
     * @param String $booking_id
     */
    function process_editbooking($booking_id) {
        log_message('info', __FUNCTION__ . " Booking Id: " . $booking_id);
        $this->checkUserSession();
        $validate = $this->set_form_validation();
        log_message('info', 'Partner initiate Edit booking' . $this->session->userdata('partner_name'));
        // $authToken = $this->partner_model->get_authentication_code($this->session->userdata('partner_id'));

        if ($validate == true && !empty($booking_id)) {
//            if($this->session->userdata('partner_id') == VIDEOCON_ID) { 
//                $this->create_booking_or_query();
//             }   
            log_message('info', 'Edit booking validation true' . $this->session->userdata('partner_name'));
            $post = $this->get_booking_form_data();
            $user['name'] = $post['name'];
            $user['phone_number'] = $post['mobile'];
            $user['user_email'] = $post['email'];
            $user['city'] = $post['city'];
            $user['pincode'] = $post['pincode'];
            $user['home_address'] = $post['address'];
            $user['user_id'] = $this->input->post('user_id');
            $user['alternate_phone_number'] = $post['alternate_phone_number'];
            $distict_details = $this->vendor_model->get_distict_details_from_india_pincode(trim($post['pincode']));

            $user['state'] = $distict_details['state'];
            $booking_details['parent_booking'] = $post['parent_booking'];
            $booking_details['booking_date'] = $post['booking_date'];
            $booking_details['partner_id'] = $post['partner_id'];
            $booking_details['booking_primary_contact_no'] = $post['mobile'];
            $booking_details['booking_alternate_contact_no'] = $post['alternate_phone_number'];
            $booking_details['booking_address'] = $post['address'];
            $booking_details['booking_pincode'] = $post['pincode'];
            $booking_details['state'] = $distict_details['state'];
            $booking_details['district'] = $distict_details['district'];
            $booking_details['taluk'] = $distict_details['taluk'];
            $booking_details['city'] = $post['city'];
            $booking_details['booking_landmark'] = $post['landmark'];
            $booking_details['partner_source'] = $post['partner_source'];
            $booking_details['order_id'] = $post['orderID'];
            $booking_details['service_id'] = $post['service_id'];
            $booking_details['booking_remarks'] = $post['remarks'];
            $booking_details['user_id'] = $user['user_id'];
            $booking_details['service_center_closed_date'] = NULL;
            $booking_details['cancellation_reason'] = NULL;
            $booking_details['booking_request_symptom'] = $post['booking_request_symptom'];
            $upcountry_data = json_decode($post['upcountry_data'], TRUE);

            $unit_details['service_id'] = $appliance_details['service_id'] = $booking_details['service_id'];
            $unit_details['appliance_brand'] = $appliance_details['brand'] = $post['brand'];
            $unit_details['appliance_description'] = $appliance_details['description'] = $post['productType'];
            $unit_details['appliance_category'] = $appliance_details['category'] = $post['category'];
            $unit_details['appliance_capacity'] = $appliance_details['capacity'] = $post['capacity'];
            $unit_details['model_number'] = $appliance_details['model_number'] = $post['model'];
            $unit_details['partner_serial_number'] = $appliance_details['serial_number'] = $post['serial_number'];
            $unit_details['purchase_date'] = $appliance_details['purchase_date'] = $post['purchase_date'];
            $unit_details['partner_id'] = $post['partner_id'];
            $unit_details['booking_id'] = $booking_details['booking_id'] = $booking_id;
            // Get Existing Price Tags
            $whereOldPrice['booking_id'] = $booking_id;
            $groupBY  = array('appliance_id');
            $oldPriceTags = $this->reusable_model->get_search_result_data('booking_unit_details','appliance_id,GROUP_CONCAT(price_tags) as price_tag',$whereOldPrice,NULL,NULL,NULL,NULL,NULL,$groupBY);
            // End
            if ($post['product_type'] == "Delivered") {
//                $booking_details['current_status'] = _247AROUND_PENDING;
//                $booking_details['internal_status'] = _247AROUND_PENDING;
                //$unit_details['booking_id'] = $booking_id;
                //$unit_details['booking_status'] = _247AROUND_PENDING;
                $booking_details['type'] = "Booking";
                if (strpos($booking_id, "Q-", 0) !== FALSE) {
                    $booking_id_array = explode("Q-", $booking_id);
                    $unit_details['booking_id'] = $booking_details['booking_id'] = $booking_id_array[1];
                }
            } else {
//                $booking_details['current_status'] = _247AROUND_FOLLOWUP;
//                $booking_details['internal_status'] = _247AROUND_FOLLOWUP;
                if (strpos($booking_id, "Q-", 0) === FALSE) {
                    $unit_details['booking_id'] = "Q-" . $booking_id;
                    $booking_details['booking_id'] = "Q-" . $booking_id;
                }

                $booking_details['type'] = "Query";
                $unit_details['booking_status'] = _247AROUND_FOLLOWUP;
            }

            /* check dealer exist or not in the database
             * if dealer does not exist into the database then
             * insert dealer details in dealer_details table and dealer_brand_mapping table 
             */
            if (isset($post['dealer_phone_number']) && !empty($post['dealer_phone_number'])) {
                $is_dealer_id = $this->miscelleneous->dealer_process($post, $this->session->userdata('partner_id'));
                if (!empty($is_dealer_id)) {
                    $booking_details['dealer_id'] = $is_dealer_id;
                }
            }

            // Update users Table
            $user_status = $this->user_model->edit_user($user);
            if ($user_status) {
                
            } else {
                log_message('info', 'User table is not updated booking Id: ' . $booking_id . " User Id" . print_r($user, true));
            }
            $unit_details['appliance_id'] = $this->input->post('appliance_id');
            //Update appliance_details table
            $appliance_status = $this->booking_model->update_appliances($unit_details['appliance_id'], $appliance_details);
            if ($appliance_status) {
                
            } else {
                log_message('info', 'Appliance is not update in Appliance details: ' . $booking_id . " Appliance data" . print_r($appliance_details, true) . "Appliamce id " . $unit_details['appliance_id']);
            }
            $updated_unit_id = array();
            $price_array = array();
            $price_tag = array();
            $customer_net_payable = 0;
            foreach ($post['requestType'] as $key => $sc) {
                $explode = explode("_", $sc);

                $unit_details['id'] = $explode[0];
                $unit_details['around_paid_basic_charges'] = $unit_details['around_net_payable'] = "0.00";
                $unit_details['partner_paid_basic_charges'] = $explode[2];
                $unit_details['partner_net_payable'] = $explode[2];
                $unit_details['ud_update_date'] = date('Y-m-d H:i:s');
                //$unit_details['booking_status'] = _247AROUND_PENDING;
                $customer_net_payable += ($explode[1] - $explode[2]);
                
                $agent_details['agent_id'] = $this->session->userdata('agent_id');
                $agent_details['agent_type'] = _247AROUND_PARTNER_STRING;
                $result = $this->booking_model->update_booking_in_booking_details($unit_details, $booking_id, $booking_details['state'], $key,$agent_details);
                array_push($price_tag, $result['price_tags']);
                array_push($updated_unit_id, $result['unit_id']);
            }

            if (!empty($updated_unit_id)) {
                log_message('info', __METHOD__ . " UNIT ID: " . print_r($updated_unit_id, true));
                $sf_id = $this->reusable_model->get_search_query('booking_details','assigned_vendor_id',array('booking_id'=>$booking_id),NULL,NULL,NULL,NULL,NULL)->result_array();
                if(!empty($sf_id[0]['assigned_vendor_id'])){
                    $inventory_details = array('receiver_entity_id' => $sf_id[0]['assigned_vendor_id'],
                                                'receiver_entity_type' => _247AROUND_SF_STRING,
                                                'stock' => 1,
                                                'agent_id' => $this->session->userdata('agent_id'),
                                                'agent_type' => _247AROUND_PARTNER_STRING,
                                                );
                }else{
                    $inventory_details = array();
                }
                $this->booking_model->check_price_tags_status($booking_id, $updated_unit_id,$inventory_details);
            }
            
            $this->booking_model->update_request_type($booking_id, $price_tag,$oldPriceTags);

            $booking_details['amount_due'] = $post['amount_due'];
            if (!empty($upcountry_data)) {
                switch ($upcountry_data['message']) {
                    case UPCOUNTRY_BOOKING:
                    case UPCOUNTRY_LIMIT_EXCEED:

                        $booking_details['is_upcountry'] = 1;


                        break;
                    default :

                        $booking_details['is_upcountry'] = 0;

                        break;
                }
            }
            if ($post['product_type'] == "Delivered") {
                $tempStatus = BOOKING_DETAILS_UPDATED; //booking update case
//                    $sc_data['current_status'] = _247AROUND_PENDING;
//                    $sc_data['internal_status'] = _247AROUND_PENDING;
//                    $booking_details['cancellation_reason'] = NULL;
//                    $booking_details['closed_date'] = NULL;
//                    
//                    $booking_details['internal_status'] = "Booking Opened From Cancelled";

                    //$this->service_centers_model->update_service_centers_action_table($booking_id, $sc_data);
            } else {
                // IN the Shipped Case
                $price_array['is_upcountry'] = $booking_details['is_upcountry'];
                $price_array['customer_net_payable'] = round($customer_net_payable, 0);
                $this->initialized_variable->fetch_partner_data($post['partner_id']);
                
                $booking_details_data = $this->booking_model->get_booking_details("request_type", array("booking_id" => $booking_id));
                $booking_details['request_type'] = $booking_details_data[0]['request_type'];
                $this->miscelleneous->check_upcountry($booking_details, $post['appliance_name'], $price_array, "shipped");
                unset($booking_details['request_type']);
                $tempStatus = _247AROUND_FOLLOWUP;
                $booking_details['assigned_vendor_id'] = NULL;
            }
            //$partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking_details['current_status'], $booking_details['internal_status'], $this->session->userdata('partner_id'), $booking_id);
           $actor = $next_action = 'not_define';
//            if (!empty($partner_status)) {
//                $booking_details['partner_current_status'] = $partner_status[0];
//                $booking_details['partner_internal_status'] = $partner_status[1];
//                $actor = $booking_details['actor'] = $partner_status[2];
//                $next_action = $booking_details['next_action'] = $partner_status[3];
//            }
            $this->insert_details_in_state_change($booking_id, $tempStatus, $booking_details['booking_remarks'],$actor,$next_action);
            $this->booking_model->update_booking($booking_id, $booking_details);
            $booking_symptom = $this->booking_model->getBookingSymptom($booking_id);
            if(count($booking_symptom)>0)
            {
                $bookingSymptom['symptom_id_booking_creation_time'] = $post['booking_request_symptom'];
                $rowsStatus = $this->booking_model->update_symptom_defect_details($booking_id, $bookingSymptom);
            }
            else {
                $bookingSymptom['booking_id'] = $booking_id;
                $bookingSymptom['symptom_id_booking_creation_time'] = $post['booking_request_symptom'];
                $bookingSymptom['create_date'] = date("Y-m-d H:i:s");
                
                if($post['booking_request_symptom']) {
                    $this->booking_model->addBookingSymptom($bookingSymptom);
                }
            }
            $up_flag = 1;
            $url = base_url() . "employee/vendor/update_upcountry_and_unit_in_sc/" . $booking_details['booking_id'] . "/" . $up_flag;
            $async_data['booking'] = array();
            $this->asynchronous_lib->do_background_process($url, $async_data);

            $userSession = array('success' => 'Booking Updated');
            $this->session->set_userdata($userSession);

            redirect(base_url() . "partner/pending_booking");
        } else {
            $this->get_editbooking_form($booking_id);
        }
    }

    /**
     * @desc: This is used to get those booking who has requested to spare parts by SF
     */
    function get_spare_parts_booking() {
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id'));
        $this->checkUserSession();
        $agent_id = $this->session->userdata('agent_id');
        if($this->session->userdata('is_filter_applicable') == 1){
            $data['states'] = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state_code.state) as state",array("agent_filters.agent_id"=>$agent_id),array("agent_filters"=>"agent_filters.state=state_code.state"),NULL,array('state'=>'ASC'),NULL,array("agent_filters"=>"left"),array());
        }
        else{
            $data['states'] = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state_code.state) as state",NULL,NULL,NULL,array('state'=>'ASC'),NULL,NULL,array());
        }
        $data['is_ajax'] = $this->input->post('is_ajax');
        if(empty($this->input->post('is_ajax'))){
            $this->miscelleneous->load_partner_nav_header();
           $this->load->view('partner/spare_parts_booking', $data);
            $this->load->view('partner/partner_footer');
        }else{
            $this->load->view('partner/spare_parts_booking', $data);
        }
    }

    /**
     * @desc: This is used to insert details into insert change table
     * @param String $booking_id
     * @param String $new_state
     * @param String $remarks
     */
    function insert_details_in_state_change($booking_id, $new_state, $remarks,$actor,$next_action, $is_cron = "") {
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id') . " Booking ID: " . $booking_id . ' new_state: ' . $new_state . ' remarks: ' . $remarks);
        //Save state change
        $state_change['booking_id'] = $booking_id;
        $state_change['new_state'] = $new_state;

        $booking_state_change = $this->booking_model->get_booking_state_change($state_change['booking_id']);

        if ($booking_state_change > 0) {
            $state_change['old_state'] = $booking_state_change[count($booking_state_change) - 1]['new_state'];
            $this->OLD_BOOKING_STATE = $state_change['old_state'];
        } else { //count($booking_state_change)
            $state_change['old_state'] = _247AROUND_PENDING;
        }

        if (empty($is_cron)) {
            $state_change['agent_id'] = $this->session->userdata('agent_id');
            $state_change['partner_id'] = $this->session->userdata('partner_id');
        } else {
            $state_change['agent_id'] = '1';
            $state_change['partner_id'] = _247AROUND;
        }
        $state_change['remarks'] = $remarks;
        $state_change['actor'] = $actor;
        $state_change['next_action'] = $next_action;

        // Insert data into booking state change
        $state_change_id = $this->booking_model->insert_booking_state_change($state_change);
        if ($state_change_id) {
            
        } else {
            log_message('info', __FUNCTION__ . '=> Booking details is not inserted into state change ' . print_r($state_change, true));
        }
    }

    /**
     * @desc: This method is used to load update form(spare parts).
     * @param String $booking_id
     */
    function update_spare_parts_form($booking_id) {
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id') . " Spare Parts ID: " . $booking_id);
        $this->checkUserSession();
        $where['length'] = -1;
        $where['where'] = array('spare_parts_details.booking_id' => $booking_id, "status" => SPARE_PARTS_REQUESTED, "spare_parts_details.entity_type" => _247AROUND_PARTNER_STRING);
        $where['select'] = "inventory_master_list.part_number, booking_details.booking_id, users.name, "
                . "booking_primary_contact_no,parts_requested, "
                . "spare_parts_details.model_number, spare_parts_details.quantity,spare_parts_details.serial_number,date_of_purchase, invoice_pic,"
                . "serial_number_pic,defective_parts_pic,spare_parts_details.id, booking_details.request_type, "
                . "purchase_price, estimate_cost_given_date,booking_details.partner_id,"
                . "booking_details.assigned_vendor_id,booking_details.service_id,spare_parts_details.parts_requested_type,spare_parts_details.part_warranty_status, requested_inventory_id,booking_details.service_id";
        $where['is_inventory'] = true;
        $data['spare_parts'] = $this->inventory_model->get_spare_parts_query($where);
        
        if(!empty($data['spare_parts'])){
         $data['request_type'] =  $data['spare_parts'][0]->request_type; 
        }
        
        $where = array();
        if(!empty($data['spare_parts'])) {
            $where = array('entity_id' => $data['spare_parts'][0]->partner_id, 'entity_type' => _247AROUND_PARTNER_STRING, 'service_id' => $data['spare_parts'][0]->service_id,'inventory_model_mapping.active' => 1);
        }
        $data['inventory_details'] = $this->inventory_model->get_inventory_mapped_model_numbers('appliance_model_details.id,appliance_model_details.model_number',$where);
        if(!empty($data['spare_parts'])) {
        $where1 = array('entity_id' => $data['spare_parts'][0]->partner_id, 'entity_type' => _247AROUND_PARTNER_STRING, 'service_id' => $data['spare_parts'][0]->service_id);
        
        $data['appliance_model_details'] = $this->inventory_model->get_appliance_model_details('id,model_number',$where1);
        }
        
        $data['courier_details'] = $this->inventory_model->get_courier_services('*');
        
        
        $this->miscelleneous->load_partner_nav_header();
        $this->load->view('partner/update_spare_parts_form', $data);
        $this->load->view('partner/partner_footer');
    }

    /**
     * @desc: This method is used to update spare parts. If gets input from form.
     * Insert data into booking state change and update sc action table
     * @param String $booking_id
     */
    function process_update_spare_parts($booking_id) {
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id') . " Booking id: " . $booking_id);
        $this->checkUserSession();
        $part_warranty_status = $this->input->post('part_warranty_status');

        if (!empty($this->input->post('courier_status'))) {
            $this->form_validation->set_rules('courier_name', 'Courier Name', 'trim|required');
            $this->form_validation->set_rules('awb', 'AWB', 'trim|required');
           //$this->form_validation->set_rules('incoming_invoice', 'Invoice', 'callback_spare_incoming_invoice');
           //$this->form_validation->set_rules('partner_challan_number', 'Partner Challan Number', 'trim|required');  
            if ($part_warranty_status != SPARE_PART_IN_OUT_OF_WARRANTY_STATUS) {
                $this->form_validation->set_rules('approx_value', 'Approx Value', 'trim|required|numeric|less_than[100000]|greater_than[0]');
            }
                     
            /*
              if ($this->input->post('request_type') !== REPAIR_OOW_TAG) {
              $this->form_validation->set_rules('approx_value', 'Approx Value', 'trim|required|numeric|less_than[100000]|greater_than[0]');
              } */

            if ($this->form_validation->run() == FALSE) {
                log_message('info', __FUNCTION__ . '=> Form Validation is not updated by Partner ' . $this->session->userdata('partner_id') .
                        " Booking id " . $booking_id . " Data" . print_r($this->input->post(), true));
                $this->update_spare_parts_form($booking_id);
            } else {
                //check upload challan file
                $MB = 1048576;
                if ($_FILES['challan_file']['size'] >= 2 * $MB) {
                    log_message('info', __FUNCTION__ . '=> Uploaded File is greater than 2 Mb ' . $this->session->userdata('partner_id') .
                            " Spare id " . $booking_id . " Data" . print_r($this->input->post(), true));
                    $this->form_validation->set_message('challan_file', "Uploaded File Must be Less Than 2Mb in size");
                    $this->update_spare_parts_form($booking_id);
                }
            }
        }
    
        $partner_id = $this->session->userdata('partner_id');
        if (!empty($this->input->post('courier_status'))) {

            //$request_type = $this->input->post('request_type');
            $challan_file = $this->upload_challan_file(rand(10, 100));
            if ($challan_file) {
                $data['partner_challan_file'] = $challan_file;
            }
            $data['courier_name_by_partner'] = $this->input->post('courier_name');
            $data['awb_by_partner'] = $this->input->post('awb');
            $data['shipped_date'] = $this->input->post('shipment_date');
            //if ($this->input->post('request_type') !== REPAIR_OOW_TAG) {
            $data['partner_challan_number'] = $this->input->post('partner_challan_number');
            $data['challan_approx_value'] = $this->input->post('approx_value');
            //} 
            
            $incoming_invoice_pdf = $this->input->post("incoming_invoice_pdf");
            if (!empty($incoming_invoice_pdf)) {
            $data['incoming_invoice_pdf'] = $incoming_invoice_pdf;
            }   
        }
        
        if ($part_warranty_status == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS) {
            $is_file = $this->validate_invoice_data();
        }

        $shipped_part_details = $this->input->post("part");
        
        if (!empty($shipped_part_details)) {
            $spare_id_array = array();
            $invoide_data = array();
            $current_status = "";
            $internal_status = "";
            foreach ($shipped_part_details as $key => $value) {               
                if ($value['shippingStatus'] == 1) {
                    //$data['status'] = SPARE_SHIPPED_BY_PARTNER;
                    /*
                      if($request_type == REPAIR_OOW_TAG){
                      $data['status'] = SPARE_OOW_SHIPPED;
                      } else {
                      $data['status'] = SPARE_SHIPPED_BY_PARTNER;
                      } */

                    if ($part_warranty_status == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS) {
                        $data['status'] = SPARE_OOW_SHIPPED;
                    } else {
                        $data['status'] = SPARE_SHIPPED_BY_PARTNER;
                    }

                    $data['parts_shipped'] = $value['shipped_parts_name'];
                    $data['model_number_shipped'] = $value['shipped_model_number'];
                    $data['shipped_parts_type'] = $value['shipped_part_type'];
                    if(isset($value['quantity']) && $value['shipped_quantity']){
                    $data['quantity'] = $value['quantity'];
                    $data['shipped_quantity']=$value['shipped_quantity'];  
                    }
                    $data['remarks_by_partner'] = $value['remarks_by_partner'];
                    if (!empty($value['requested_inventory_id'])) {
                        $data['shipped_inventory_id'] = $value['requested_inventory_id'];
                    }
                    
                    if (!empty($value['spare_id'])) {
                        $spare_id = $value['spare_id'];
                        $where = array('id' => $spare_id, 'partner_id' => $partner_id, 'entity_type' => _247AROUND_PARTNER_STRING);
                        $response = $this->service_centers_model->update_spare_parts($where, $data);
                    } else {
                        $data['defective_return_to_entity_id'] = $this->session->userdata('partner_id');
                        $data['defective_return_to_entity_type'] = _247AROUND_PARTNER_STRING;
                        $spare_id = $this->inset_new_spare_request($booking_id, $data, $value);
                    }
                    
                    if ($part_warranty_status == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS) {

                        if (!empty($spare_id)) {
                            $invoide_data = array("invoice_id" => $value['invoice_id'],
                                "spare_id" => $spare_id, "invoice_date" => $value['invoice_date'], "hsn_code" => $value['hsn_code'],
                                "gst_rate" => $value['gst_rate'], "invoice_amount" => $value['invoiceamount'], "invoice_pdf" => $value['incoming_invoice']);
                            $this->service_centers_model->insert_data_into_spare_invoice_details($invoide_data);
                        }
                    }
                    array_push($spare_id_array, $spare_id);
                    $current_status = "InProcess";
                    /*
                      if($request_type == REPAIR_OOW_TAG){
                      $internal_status = SPARE_OOW_SHIPPED;
                      } else {
                      $internal_status = SPARE_PARTS_SHIPPED;

                      }
                     */

                    if ($part_warranty_status == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS) {
                        $internal_status = SPARE_OOW_SHIPPED;
                    } else {
                        $internal_status = SPARE_PARTS_SHIPPED;
                    }
                } else if ($value['shippingStatus'] == -1) {
                    $this->insert_details_in_state_change($booking_id, "SPARE TO BE SHIP", "Partner Update - " . $value['shipped_parts_name'] . " To Be Shipped", "", "");
                } else if ($value['shippingStatus'] == 0) {

                    $current_status = _247AROUND_PENDING;
                    $internal_status = _247AROUND_PENDING;

                    $this->insert_details_in_state_change($booking_id, SPARE_PARTS_CANCELLED, "Partner Reject Spare Part", "", "");
                    $response = $this->service_centers_model->update_spare_parts(array("id" => $value['spare_id']), array('status' => _247AROUND_CANCELLED, "old_status" => SPARE_PARTS_REQUESTED));
                }
            }

            if (!empty($current_status)) {

                $sc_data['current_status'] = $current_status;
                /*
                  if($request_type == REPAIR_OOW_TAG){
                  $sc_data['internal_status'] = SPARE_OOW_SHIPPED;
                  } else {
                  $sc_data['internal_status'] = $internal_status;

                  } */

                if ($part_warranty_status == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS) {
                    $sc_data['internal_status'] = SPARE_OOW_SHIPPED;
                } else {
                    $sc_data['internal_status'] = $internal_status;
                }

                $this->vendor_model->update_service_center_action($booking_id, $sc_data);

                $booking['internal_status'] = $internal_status;
                $actor = $next_action = 'not_define';
                $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, $booking['internal_status'], $partner_id, $booking_id);
                if (!empty($partner_status)) {
                    $booking['partner_current_status'] = $partner_status[0];
                    $booking['partner_internal_status'] = $partner_status[1];
                    $actor = $booking['actor'] = $partner_status[2];
                    $next_action = $booking['next_action'] = $partner_status[3];
                }
                $this->insert_details_in_state_change($booking_id, $internal_status, "Partner acknowledged to shipped spare parts", $actor, $next_action);

                $this->booking_model->update_booking($booking_id, $booking);
//                        if (!empty($incoming_invoice_pdf) && !empty($spare_id_array)) {
//                            foreach($spare_id_array as $s_value){
//                                // Send OOW invoice to Inventory Manager
//                                $url = base_url() . "employee/invoice/generate_oow_parts_invoice/" . $s_value;
//                                $async_data['booking_id'] = $booking_id;
//                                $this->asynchronous_lib->do_background_process($url, $async_data);
//                            }
//                            
//                        }

                $userSession = array('success' => 'Parts Updated');
                $this->session->set_userdata($userSession);
                redirect(base_url() . "partner/get_spare_parts_booking/0/1");
            } else { //if($response){
                log_message('info', __FUNCTION__ . '=> Spare parts booking NOT SHIP updated by Partner ' . $this->session->userdata('partner_id') .
                        " booking id " . $booking_id . " Data" . print_r($this->input->post(), true));
                $userSession = array('success' => 'Parts Updated');
                $this->session->set_userdata($userSession);
                redirect(base_url() . "partner/update_spare_parts_form/" . $booking_id);
            }
        } else {
            log_message('info', __FUNCTION__ . '=> Spare parts booking is not updated by Partner ' . $this->session->userdata('partner_id') .
                    " booking id " . $booking_id . " Data" . print_r($this->input->post(), true));
            $userSession = array('success' => 'Parts Not Updated');
            $this->session->set_userdata($userSession);
            redirect(base_url() . "partner/update_spare_parts_form/" . $booking_id);
        }
    }

    function inset_new_spare_request($booking_id, $data, $part_details){
        $sp_details = $this->partner_model->get_spare_parts_by_any("*", array('booking_id' => $booking_id));
        $data['entity_type'] =_247AROUND_PARTNER_STRING;
        $data['booking_id'] = $booking_id;
        $data['partner_id'] = $this->session->userdata("partner_id");
        $data['service_center_id'] = $sp_details[0]['service_center_id'];
        $data['model_number'] = $part_details['shipped_model_number'];
        $data['serial_number'] = $sp_details[0]['serial_number'];
        $data['requested_inventory_id'] = $part_details['inventory_id'];
        $data['date_of_purchase'] = $sp_details[0]['date_of_purchase'];
        $data['date_of_request'] = date("Y-m-d");
        $data['create_date'] = date('Y-m-d H:i:s');
        $data['invoice_pic'] = $sp_details[0]['invoice_pic'];
        $data['defective_parts_pic'] = $sp_details[0]['defective_parts_pic'];
        $data['defective_back_parts_pic'] = $sp_details[0]['defective_back_parts_pic'];
        $data['serial_number_pic'] = $sp_details[0]['serial_number_pic'];
        if(!empty($part_details['shipped_part_type'])){
            $data['parts_requested_type'] = $part_details['shipped_part_type'];
        } else {
            $data['parts_requested_type'] = $part_details['shipped_parts_name'];
        }
        
        $data['parts_requested'] = $part_details['shipped_parts_name'];


        return $this->service_centers_model->insert_data_into_spare_parts($data);
    }

    /**
     * @desc This is used to upload and send Repair OOW Parts Invoice
     * @return boolean
     */
    function spare_incoming_invoice() {
        log_message('info', __FUNCTION__);
        $part_warranty_status = $this->input->post("part_warranty_status");
        $booking_id = $this->input->post("booking_id");        
        if ($part_warranty_status == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS) {
            $allowedExts = array("PDF", "pdf");
            $invoice_name = $this->miscelleneous->upload_file_to_s3($_FILES["incoming_invoice"], "sp_parts_invoice", $allowedExts, $booking_id, "invoices-excel", "incoming_invoice_pdf");
            if (!empty($invoice_name)) {
                $template = $this->booking_model->get_booking_email_template("OOW_invoice_sent");
                if (!empty($template)) {
                    $attachment = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/invoices-excel/" . $invoice_name;
                    $subject = vsprintf($template[4], $booking_id);
                    $emailBody = vsprintf($template[0], $this->input->post("invoice_amount"));
                    $this->notify->sendEmail($template[2], $template[1], $template[3], '', $subject, $emailBody, $attachment,'OOW_invoice_sent', "", $booking_id);
                }

                return true;
            } else {
                $this->form_validation->set_message('spare_incoming_invoice', 'File size or file type is not supported. Allowed extentions is "pdf". '
                        . 'Maximum file size is 5 MB.');
                return FALSE;
            }
        } else {
            return true;
        }
    }
    
    
    
    function validate_invoice_data(){
        $allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
        $booking_id = $this->input->post("booking_id");
        $incoming_invoice = array();
        if(!empty($_FILES['incominginvoice'])){
            foreach($_FILES['incominginvoice']['name'] as $key1 => $val){
                $a = array();
                $a['name'] = $_FILES['incominginvoice']['name'][$key1];
                $a['type'] = $_FILES['incominginvoice']['type'][$key1];
                $a['tmp_name'] = $_FILES['incominginvoice']['tmp_name'][$key1];
                $a['error'] = $_FILES['incominginvoice']['error'][$key1];
                $a['size'] = $_FILES['incominginvoice']['size'][$key1];
                $incoming_invoice[$key1] = $a;
                //array_push($defective_parts, $a);
            }
            
        }
       
        $message['code'] = true;
        if(!empty($incoming_invoice)){
            foreach($incoming_invoice as $key => $value){
                $d = $this->miscelleneous->upload_file_to_s3($value, 
                    "sp_parts_invoice", $allowedExts, $booking_id, "invoices-excel", "incoming_invoice_pdf");
                if(!empty($d)){
                    $_POST['part'][$key]['incoming_invoice'] = $d;
                } else {
                    $message['code'] = false;
                    $message['message'] = "Defective Front Parts Image is not supported. Allow maximum file size is 2 MB. It supported only PNG/JPG";
                    break;
                }
            }
        } else {
            $message['code'] = false;
            $message['message'] = "Please upload Defective Front Parts Image";
        }
        
        if(!empty($message['code'])){
                $template = $this->booking_model->get_booking_email_template("OOW_invoice_sent");
                if (!empty($template)) {
                    $attachment = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/invoices-excel/" . $d;
                    $subject = vsprintf($template[4], $booking_id);
                    $emailBody = vsprintf($template[0], $this->input->post("invoice_amount"));
                    $this->notify->sendEmail($template[2], $template[1], $template[3], '', $subject, $emailBody, $attachment,'OOW_invoice_sent', "", $booking_id);
                } 
        }
       
        return $message;
    }

    function download_spare_parts() {
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id'));
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');
        $where = "spare_parts_details.partner_id = '" . $partner_id . "' AND status = '" . SPARE_PARTS_REQUESTED . "' "
                . " AND booking_details.current_status IN ('"._247AROUND_PENDING."', '"._247AROUND_RESCHEDULED."') ";
        $data = $this->partner_model->get_spare_parts_booking($where);
        $template = 'download_spare_parts.xlsx';
        //set absolute path to directory with template files
        $templateDir = __DIR__ . "/../excel-templates/";
        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );

        //load template
        if(ob_get_length() > 0) {
            ob_end_clean();
        }
        $R = new PHPReport($config);

        $R->load(array(
            array(
                'id' => 'booking',
                'repeat' => true,
                'data' => $data,
            ),
                )
        );

        $output_file_excel = TMP_FOLDER . "spare_parts-" . date('Y-m-d') . ".xlsx";
        $R->render('excel', $output_file_excel);
        if (file_exists($output_file_excel)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($output_file_excel) . '"');
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
    function get_booking_life_cycle($booking_id) { 
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . " Booking_id" . $booking_id);
        $data['data'] = $this->booking_model->get_booking_state_change_by_id($booking_id);
        $data['booking_details'] = $this->booking_model->getbooking_history($booking_id);
        $data['sms_sent_details'] = $this->booking_model->get_sms_sent_details($booking_id);

        $data['request_type'] = $this->miscelleneous->get_request_type_life_cycle($booking_id);
        //$this->load->view('partner/header');

        $this->load->view('employee/show_booking_life_cycle', $data);
    }
    
    /**
     * @desc: This is used to show Comment Section of particular Booking
     * params: String Booking_ID
     * return: Array of Data for View
     */
    function get_comment_section($booking_id, $comment_type){ 
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . " Booking_id" . $booking_id);
        $data['comments'] = $this->booking_model->get_remarks(array('booking_id' => $booking_id, "isActive" => 1,'comment_type' => $comment_type));
        $data['booking_id'] = $booking_id;
        $data['user_id'] = $this->session->userdata('id');
        $this->load->view('employee/comment_section', $data);
    }

    /**
     * @desc: This is used to print  address for selected booking
     * @param Array $booking_address
     */
    function download_shippment_address($booking_address) {
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id'));
        
        $partner_on_saas = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $main_partner = $this->partner_model->get_main_partner_invoice_detail($partner_on_saas);
        if(!empty($main_partner)){
            $main_company_public_name = $main_partner['main_company_public_name'];
            $main_company_logo = $main_partner['main_company_logo'];
        }
        else{
            $main_company_public_name = "";
            $main_company_logo = "";
        }

        $booking_history['details'] = array();
        foreach ($booking_address as $key => $value) {
            
            $select = "contact_person.name as  primary_contact_name,contact_person.official_contact_number as primary_contact_phone_1,contact_person.alternate_contact_number as primary_contact_phone_2,"
                    . "concat(warehouse_address_line1,',',warehouse_address_line2) as address,warehouse_details.warehouse_city as district,"
                    . "warehouse_details.warehouse_pincode as pincode,"
                    . "warehouse_details.warehouse_state as state";

            $where = array('contact_person.entity_id' => $this->session->userdata('partner_id'), 'contact_person.entity_type' => _247AROUND_PARTNER_STRING);

            $wh_address_details = $this->inventory_model->get_warehouse_details($select, $where, FALSE);
            
            $partner_details = $this->partner_model->getpartner($this->session->userdata('partner_id'))[0];
            
            $booking_history['details'][$key] = $this->booking_model->getbooking_history($value, "join")[0];
            $b_spare = $this->partner_model->get_spare_parts_by_any("Distinct parts_requested", array("booking_id" => $value, "entity_type" => "partner", "partner_id" => $this->session->userdata('partner_id')));
            if(!empty($b_spare)){
                $part_name = implode(", ",array_unique(array_map(function ($k) {
                        return $k['parts_requested'];
                    }, $b_spare)));
                    
                $booking_history['details'][$key]['part_name'] = $part_name;
            } else {
                $booking_history['details'][$key]['part_name'] = "";
            }
            
            $b_unit = $this->booking_model->get_unit_details(array('booking_id' => $value), false, "appliance_brand");
            if(!empty($b_unit)){
                $brand_name = implode(", ",array_unique(array_map(function ($k) {
                        return $k['appliance_brand'];
                    }, $b_unit)));
                $booking_history['details'][$key]['brand_name'] = $brand_name;
            } else {
                $booking_history['details'][$key]['brand_name'] = "";
            }
            
            if (!empty($wh_address_details)) {
                $wh_address_details[0]['company_name'] = $partner_details['company_name'];
                $booking_history['details'][$key]['partner'] = $wh_address_details[0];
            } else {
                $booking_history['details'][$key]['partner'] = $partner_details;
            }
            
            $booking_history['details'][$key]['main_company_public_name'] = $main_company_public_name;
            $booking_history['details'][$key]['main_company_logo'] = $main_company_logo;
        }
        
        $this->load->view('partner/print_address', $booking_history);
    }

    /**
     * @desc: This is used to print courier manifest or address for selected booking
     */
    function print_all() {
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id'));
        $booking_address = $this->input->post('download_address');
        $booking_manifest = $this->input->post('download_courier_manifest');

        if (!empty($booking_address)) {

            $this->download_shippment_address($booking_address);
        } else if (!empty($booking_manifest)) {

            $this->download_mainfest($booking_manifest);
        } else if (empty($booking_address) && empty($booking_manifest)) {
            echo "Please Select Any Checkbox";
        }
    }

    /**
     * @desc: This is used to print courier manifest for selected booking
     * @param type $booking_manifest
     */
    function download_mainfest($booking_manifest) {
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id'));
        $spare_parts_details['courier_manifest'] = array();
        foreach ($booking_manifest as $key => $value) {

            $where = "spare_parts_details.booking_id = '" . $value . "' AND status = '" . SPARE_PARTS_REQUESTED . "' "
                    . " AND booking_details.current_status IN ('"._247AROUND_PENDING."', '"._247AROUND_RESCHEDULED."') ";
            $spare_parts_details['courier_manifest'][$key] = $this->partner_model->get_spare_parts_booking($where)[0];
            $spare_parts_details['courier_manifest'][$key]['brand'] = $this->booking_model->get_unit_details(array('booking_id' => $value))[0]['appliance_brand'];
        }

        $this->load->view('partner/courier_manifest', $spare_parts_details);
    }

    /**
     * @desc: Display list of Shipped Parts in the Partner Panel
     */
    function get_shipped_parts_list($offset = 0) {
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id'));
        $this->checkUserSession();
        $agent_id = $this->session->userdata('agent_id');
        if($this->session->userdata('is_filter_applicable') == 1){
            $data['states'] = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state_code.state) as state",array("agent_filters.agent_id"=>$agent_id),array("agent_filters"=>"agent_filters.state=state_code.state"),NULL,array('state'=>'ASC'),NULL,array("agent_filters"=>"left"),array());
        }
        else{   
            $data['states'] = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state) as state",NULL,NULL,NULL,array('state'=>'ASC'),NULL,NULL,array());
        }
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id'));
        $this->miscelleneous->load_partner_nav_header();
        $this->load->view('partner/shipped_spare_part_booking', $data);
        $this->load->view('partner/partner_footer');
    }

    /**
     * @desc: Pending Defective Parts list 
     */
    function get_waiting_defective_parts() {
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id'));
        $this->checkUserSession();
        $agent_id = $this->session->userdata('agent_id');
        if($this->session->userdata('is_filter_applicable') == 1){
            $data['states'] = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state_code.state) as state",array("agent_filters.agent_id"=>$agent_id),array("agent_filters"=>"agent_filters.state=state_code.state"),NULL,array('state'=>'ASC'),NULL,array("agent_filters"=>"left"),array());
        }
        else{
            $data['states'] = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state_code.state) as state",NULL,NULL,NULL,array('state'=>'ASC'),NULL,NULL,array());
        }
        $data['is_ajax'] = $this->input->post('is_ajax');
        if(empty($this->input->post('is_ajax'))){
            $this->miscelleneous->load_partner_nav_header();
            $this->load->view('partner/waiting_defective_parts', $data);
            $this->load->view('partner/partner_footer');
        }else{
            $this->load->view('partner/waiting_defective_parts', $data);
        }
    }

    /**
     * @desc: Partner acknowledge to receive defective spare parts
     * @param String $booking_id
     */
    function acknowledge_received_defective_parts($spare_id, $booking_id, $partner_id, $is_cron = "") {
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id') . " Booking Id " . $booking_id);

        if (empty($is_cron)) {
            $this->checkUserSession();
        }

        $response = $this->service_centers_model->update_spare_parts(array('id' => $spare_id), array('status' => DEFECTIVE_PARTS_RECEIVED,
            'approved_defective_parts_by_partner' => '1', 'remarks_defective_part_by_partner' => DEFECTIVE_PARTS_RECEIVED,
            'received_defective_part_date' => date("Y-m-d H:i:s")));
        
        
        
        if ($response) {

            log_message('info', __FUNCTION__ . " Received Defective Spare Parts " . $booking_id
                    . " Partner Id" . $this->session->userdata('partner_id'));
            
            $sendUrl = base_url().'employee/invoice/generate_micro_reverse_sale_invoice/'.$spare_id;
            $this->asynchronous_lib->do_background_process($sendUrl, array());
            
            $psendUrl = base_url().'employee/invoice/generate_reverse_micro_purchase_invoice/'.$spare_id;
            $this->asynchronous_lib->do_background_process($psendUrl, array());
            
            $is_exist = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id", 
                    array('spare_parts_details.booking_id' => $booking_id, 'spare_parts_details.defective_part_required' => 1, "status NOT IN  ('"._247AROUND_CANCELLED."', '"._247AROUND_COMPLETED
                        ."', '".DEFECTIVE_PARTS_RECEIVED."') " => NULL));
            
            
            $actor = $next_action = 'not_define';
            if(empty($is_exist)){
                $sc_data['current_status'] = "InProcess";
                $sc_data['internal_status'] = _247AROUND_COMPLETED;
                $this->vendor_model->update_service_center_action($booking_id, $sc_data);
                
                $booking['internal_status'] = DEFECTIVE_PARTS_RECEIVED;
                

                $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, $booking['internal_status'], $partner_id, $booking_id);
                
                if (!empty($partner_status)) {
                    $booking['partner_current_status'] = $partner_status[0];
                    $booking['partner_internal_status'] = $partner_status[1];
                    $actor = $booking['actor'] = $partner_status[2];
                    $next_action = $booking['next_action'] = $partner_status[3];
                }
                $this->insert_details_in_state_change($booking_id, DEFECTIVE_PARTS_RECEIVED, "Partner Received Defective Spare Parts", $actor,$next_action,$is_cron);

                $this->booking_model->update_booking($booking_id, $booking);
            } else {
                $this->insert_details_in_state_change($booking_id, DEFECTIVE_PARTS_RECEIVED, "Partner Received Defective Spare Parts", $actor,$next_action,$is_cron);
            }

            $is_oow_return = $this->partner_model->get_spare_parts_by_any("booking_unit_details_id, purchase_price, sell_price, sell_invoice_id", 
                    array('spare_parts_details.id' => $spare_id, 
                        'booking_unit_details_id IS NOT NULL' => NULL,
                        'sell_price > 0 ' => NULL,
                        'sell_invoice_id IS NOT NULL' => NULL,
                        'estimate_cost_given_date IS NOT NULL' => NULL,
                        'spare_parts_details.part_warranty_status' => 2,
                        'defective_part_required' => 1,
                        'approved_defective_parts_by_partner' => 1,
                        'status' => DEFECTIVE_PARTS_RECEIVED,
                        '(reverse_sale_invoice_id IS NULL OR reverse_purchase_invoice_id)' => NULL),
                    true);
            if(!empty($is_oow_return)){
                $url = base_url() . "employee/invoice/generate_reverse_oow_invoice/".$spare_id;
                $async_data['booking_id'] = $booking_id;
                $this->asynchronous_lib->do_background_process($url, $async_data);
            }

            if (empty($is_cron)) {
                $userSession = array('success' => ' Received Defective Spare Parts');
                $this->session->set_userdata($userSession);
                redirect(base_url() . "partner/get_waiting_defective_parts");
            }
        } else { //if($response){
            log_message('info', __FUNCTION__ . '=> Defective Spare Parts not udated  by Partner ' . $this->session->userdata('partner_id') .
                    " booking id " . $booking_id);
            if (empty($is_cron)) {
                $userSession = array('success' => 'There is some error. Please try again.');
                $this->session->set_userdata($userSession);
                redirect(base_url() . "partner/get_waiting_defective_parts");
            }
        }
    }
    /**
     * @desc This function is used when partner acknowledge to receive part from Warehouse.
     * @param int $spare_id
     * @param String $booking_id
     * @param int $partner_id
     * @param int $is_cron
     */
    function acknowledge_defective_parts_sent_by_wh($spare_id, $booking_id, $partner_id, $is_cron = "", $spare_qty_mgmt_id = ''){
        log_message('info', __METHOD__ . " Spare ID ".$spare_id);
        if (empty($is_cron)) {
            $this->checkUserSession();
        }

        $response = $this->service_centers_model->update_spare_parts(array('id' => $spare_id), array('status' => PARTNER_ACK_DEFECTIVE_PARTS_SEND_BY_WH,
            
            'received_defective_part_date' => date("Y-m-d H:i:s")));
        
        if(!empty($spare_qty_mgmt_id)) {
            $this->inventory_model->update_qty_ledger_mgmt(['status' => PARTNER_ACK_DEFECTIVE_PARTS_SEND_BY_WH], ['id' => $spare_qty_mgmt_id, 'spare_id' => $spare_id]);
        }
        
        if($response){
            
            $psendUrl = base_url().'employee/invoice/generate_reverse_micro_purchase_invoice/'.$spare_id;
            $this->asynchronous_lib->do_background_process($psendUrl, array());
            
            $agent_id = $this->session->userdata('agent_id');
            $agent_name = $this->session->userdata('partner_name');
            $actor = ACTOR_NOT_DEFINE;
            $next_action = NEXT_ACTION_NOT_DEFINE;

            $this->notify->insert_state_change($booking_id, PARTNER_ACK_DEFECTIVE_PARTS_SEND_BY_WH, "", PARTNER_ACK_DEFECTIVE_PARTS_SEND_BY_WH, $agent_id, $agent_name, $actor, $next_action, $partner_id);
            $userSession = array('success' => ' Received Defective Spare Parts');
            $this->session->set_userdata($userSession);
            redirect(base_url() . "partner/get_waiting_defective_parts");
               
        }
    }
    /**
     * @desc: Partner rejected Defective Parts with reason (Part sent By warehouse).
     * @param Sting $booking_id
     * @param Urlencoded $status (Rejection Reason)
     */
    function reject_defective_part_sent_by_wh($spare_id, $booking_id, $status, $spare_qty_mgmt_id = ''){
        log_message('info', __METHOD__ . " Spare ID ".$spare_id);
        $this->checkUserSession();
        $rejection_reason = base64_decode(urldecode($status));
        $response = $this->service_centers_model->update_spare_parts(array('id' => $spare_id), array('status' => DEFECTIVE_PARTS_REJECTED,
            'remarks_defective_part_by_partner' => $rejection_reason,
            'defective_part_rejected_by_partner'=>1,
            'approved_defective_parts_by_partner' => '0'));
        
        if(!empty($spare_qty_mgmt_id)) {
            $this->inventory_model->update_qty_ledger_mgmt(['status' => DEFECTIVE_PARTS_REJECTED], ['id' => $spare_qty_mgmt_id, 'spare_id' => $spare_id]);
        }
        
        if ($response) {
           
            $actor = ACTOR_NOT_DEFINE;
            $next_action = NEXT_ACTION_NOT_DEFINE;
            $this->insert_details_in_state_change($booking_id, $rejection_reason, DEFECTIVE_PARTS_REJECTED,$actor,$next_action);
            $userSession = array('success' => 'Defective Parts Rejected To SF');
            $this->session->set_userdata($userSession);
            redirect(base_url() . "partner/get_waiting_defective_parts");
        } else {
            log_message('info', __FUNCTION__ . '=> Defective Spare Parts Not Updated by Partner' . $this->session->userdata('partner_id') .
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
    function reject_defective_part($spare_id, $booking_id, $status) {
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id') . " Booking Id " . $booking_id . ' status: ' . $status);
        $this->checkUserSession();
        $rejection_reason = base64_decode(urldecode($status));

        $response = $this->service_centers_model->update_spare_parts(array('id' => $spare_id), array('status' => DEFECTIVE_PARTS_REJECTED,
            'remarks_defective_part_by_partner' => $rejection_reason,
            'approved_defective_parts_by_partner' => '0'));
        if ($response) {
            log_message('info', __FUNCTION__ . " Sucessfully updated Table " . $booking_id
                    . " Partner Id" . $this->session->userdata('partner_id'));

            $sc_data['current_status'] = "InProcess";
            $sc_data['internal_status'] = $rejection_reason;
            $this->vendor_model->update_service_center_action($booking_id, $sc_data);
            
            $booking['internal_status'] = DEFECTIVE_PARTS_REJECTED;
        
            $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, $booking['internal_status'], 
                    $this->session->userdata('partner_id'), $booking_id);
            $actor = $next_action = 'not_define';
            if (!empty($partner_status)) {
                $booking['partner_current_status'] = $partner_status[0];
                $booking['partner_internal_status'] = $partner_status[1];
                $actor = $booking['actor'] = $partner_status[2];
                $next_action = $booking['next_action'] = $partner_status[3];
            }
            $this->insert_details_in_state_change($booking_id, $rejection_reason, DEFECTIVE_PARTS_REJECTED,$actor,$next_action);
            $this->booking_model->update_booking($booking_id, $booking);

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
     * @Desc: This function is used to get Brands for selected Services of particular Partner 
     *          This is being called from AJAX
     * @params: partner_id, service_name
     * @return: String
     * 
     */
    function get_brands_from_service() {    
        $partner_id = $this->input->post('partner_id');
        $service_id = $this->input->post('service_id');
        $appliace_brand = $this->input->post('brand');
        $is_repeat = $this->input->post('is_repeat');
        $partner_data = $this->partner_model->get_partner_code($partner_id);
        $partner_type = $partner_data[0]['partner_type'];
        if ($partner_type == OEM) {
            //Getting Unique values of Brands for Particular Partner and service id
            $where = array('partner_id' => $partner_id, 'service_id' => $service_id, "active" => 1);
            $data = $this->partner_model->get_partner_specific_details($where, "brand  As brand_name", "brand");
        } else {
            $data = $this->booking_model->getBrandForService($service_id);
        }

        $option = "<option selected disabled value=''>Select Brand</option>";
        foreach ($data as $value) {
            $option .= "<option ";
            if ($appliace_brand == $value['brand_name'] || count($data) == 1) {
                $option .= " ";
            }
            else{
                 if($is_repeat){
                $option .= " disabled ";
            }
            }
            $option .= " value='" . $value['brand_name'] . "'>" . $value['brand_name'] . "</option>";
        }

//         $data['services'] = "<option selected disabled value=''>Select Product</option>";
//            foreach ($services as $appliance) {
//                $data['services'] .= "<option ";
//                if ($selected_service_id == $appliance->id) {
//                    $data['services'] .= " selected ";
//                } else if (count($services) == 1) {
//                    $data['services'] .= " selected ";
//                }
//                $data['services'] .=" value='" . $appliance->id . "'>$appliance->services</option>";
//            }
        echo $option;
    }

    /**
     * @Desc: This function is used to get Category Details for Partner
     *          This is being called from AJAX
     * @params: partner_id, service_name, brand name
     * @return: String
     * 
     */
    function get_category_from_service() {
        $partner_id = $this->input->post('partner_id');
        $service_id = $this->input->post('service_id');
        $category = $this->input->post('category');
        $brand = $this->input->post('brand');
        $partner_type = $this->input->post('partner_type');
        $is_repeat = $this->input->post('is_repeat');
        if($this->input->post('is_mapping')){     
                $select = 'category.name as category';
                $where = array('partner_appliance_mapping.partner_id' => $partner_id, 'service_category_mapping.service_id' => $service_id);
                $join['partner_appliance_mapping']  = 'service_category_mapping.id = partner_appliance_mapping.appliance_configuration_id';
                $JoinTypeTableArray['partner_appliance_mapping'] = 'left';            
                $data = $this->service_centre_charges_model->getServiceCategoryMapping($where, $select,"category.name", NULL, $join, $JoinTypeTableArray);           
        } else {
            $where_in = array();
            
            if($partner_type == OEM){
                
                $where_in = array("brand" => $brand);
            }
            $where = array('partner_id' => $partner_id, 'service_id' => $service_id);
            
            
            $data = $this->service_centre_charges_model->get_service_caharges_data("category", $where,"category", $where_in);
        } 
 
        $option = "";
        foreach ($data as $value) {
            $option .= "<option ";
            if ($category === $value['category']) {
                $option .= " selected ";
            } else if (count($data) == 1) {
                $option .= " selected ";
            }
            else{
                if($is_repeat && (trim($category) !== '')){
                    $option .= " disabled ";
                }
            }
            
            $option .= " value='" . $value['category'] . "'>" . $value['category'] . "</option>";
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
    function get_capacity_for_partner() {
        $partner_id = $this->input->post('partner_id');
        $service_id = $this->input->post('service_id');
        $brand = $this->input->post('brand');
        $category = $this->input->post('category');
        $appliance_capacity = $this->input->post('capacity');
        $partner_type = $this->input->post('partner_type');
        $is_repeat = $this->input->post('is_repeat');
        
        if($this->input->post("is_mapping")){
            
            $where = array('partner_appliance_mapping.partner_id' => $partner_id, 'service_category_mapping.service_id' => $service_id);
            $where_in = array("category.name" => $category);
            $join['partner_appliance_mapping']  = 'service_category_mapping.id = partner_appliance_mapping.appliance_configuration_id';
            $join['capacity']  = 'service_category_mapping.capacity_id = capacity.id';
            $JoinTypeTableArray['capacity'] = 'left';
            $JoinTypeTableArray['partner_appliance_mapping'] = 'left';
            $data = $this->service_centre_charges_model->getServiceCategoryMapping($where, "capacity.name as capacity","capacity.name", $where_in, $join, $JoinTypeTableArray);
        } else {
            
            $where_in = array("category" => $category);
            if($partner_type == OEM){
                $where_in['brand'] = $brand;
            }
            $where = array('partner_id' => $partner_id, 'service_id' => $service_id);
             
            
            $data = $this->service_centre_charges_model->get_service_caharges_data("capacity", $where,"capacity", $where_in);
        }
        
        $capacity = "";
        foreach ($data as $value) {

            $capacity .= "<option ";
            if ($appliance_capacity === $value['capacity']) {
                $capacity .= " selected ";
            } else if (count($data) == 1) {
                $capacity .= " selected ";
            }
            else{
                if($is_repeat && (trim($appliance_capacity) !== '')){
                    $capacity .= " disabled ";
                }
            }
            $capacity .= " value='" . $value['capacity'] . "'>" . $value['capacity'] . "</option>";
        }

        echo $capacity;
    }

    /**
     * @Desc: This function is used to get  Model for Partner for particular Brand, service_id, capacity and category
     *      This is being called from AJAX
     * @params: partner_id, service_name, brand_name, category
     * $return: Json
     * 
     */
    function get_model_for_partner_with_brand() {
        $partner_id = $this->input->post('partner_id');
        $service_id = $this->input->post('service_id');
        $brand = $this->input->post('brand');
 
        
       
            $where = array(
                "partner_appliance_details.partner_id" => $partner_id,
                'partner_appliance_details.service_id' => $service_id,
                'partner_appliance_details.brand' => $brand,
                'appliance_model_details.active'=> 1
            );
            
           
            $data = $this->partner_model->get_model_number("appliance_model_details.id, appliance_model_details.model_number, model", $where);
       


        if (!empty($data[0]['model'])) {
            $model = "";
            foreach ($data as $value) {
                $model .= "<option ";
//                if (trim($model_number) === trim($value['model_number'])) {
//                    $model .= " selected ";
//                } else if (count($data) == 1) {
//                    $model .= " selected ";
//                }
//                else{
//                    if($is_repeat){
//                        $model .= " disabled ";
//                    }
//                }
                
                $model .= " value='" . $value['model_number'] . "'>" . $value['model_number'] . "</option>";
            }
            echo $model;
        } else {
            echo "Data Not Found";
        }
    }
    
    /**
     * @Desc: This function is used to get  Model for Partner for particular Brand, service_id, capacity and category
     *      This is being called from AJAX
     * @params: partner_id, service_name, brand_name, category
     * $return: Json
     * 
     */
    function get_model_for_partner() {
        $partner_id = $this->input->post('partner_id');
        $service_id = $this->input->post('service_id');
        $brand = $this->input->post('brand');
        $category = $this->input->post('category');
        $capacity = $this->input->post('capacity');
        $model_number = $this->input->post('model');
        $partner_type = $this->input->post('partner_type');
        $is_repeat = $this->input->post('is_repeat');
        
        if ($partner_type == OEM) {
            //Getting Unique values of Model for Particular Partner ,service id and brand
            $where = array(
                "partner_appliance_details.partner_id" => $partner_id,
                'partner_appliance_details.service_id' => $service_id,
                'partner_appliance_details.brand' => $brand,
                'partner_appliance_details.category' => $category,
                'appliance_model_details.active'=> 1
            );
            
            if(empty($capacity)){
                $where['(partner_appliance_details.capacity = "" OR partner_appliance_details.capacity IS NULL)'] = NULL;
            }
            else{
                $where['partner_appliance_details.capacity'] = $capacity;
            }

            $data = $this->partner_model->get_model_number("appliance_model_details.id, appliance_model_details.model_number, model", $where);
        } else {
            $data[0]['model'] = "";
        }


        if (!empty($data[0]['model'])) {
            $model = "";
            foreach ($data as $value) {
                $model .= "<option ";
                if (trim($model_number) === trim($value['model_number'])) {
                    $model .= " selected ";
                } else if (count($data) == 1) {
                    $model .= " selected ";
                }
                else{
                    if($is_repeat){
                        $model .= " disabled ";
                    }
                }
                
                $model .= " value='" . $value['model_number'] . "'>" . $value['model_number'] . "</option>";
            }
            echo $model;
        } else {
            echo "Data Not Found";
        }
    }

    /**
     * @Desc: This function is used to remove images from partner add/edit form
     *          It is being called using AJAX Request
     * params: partner id
     * return: Boolean
     */
    function remove_contract_image() {
        $partner['contract_file'] = '';
        //Making Database Entry as Empty for contract file
        $this->partner_model->edit_partner($partner, $this->input->post('id'));

        //Logging 
        log_message('info', __FUNCTION__ . ' Contract File has been removed sucessfully for partner id ' . $this->input->post('id'));
        echo TRUE;
    }

    /**
     * @desc: This method is used to display list of booking which received by Partner
     * @param Integer $offset
     */
    function get_approved_defective_parts_booking($offset = 0) {
        $this->checkUserSession();
        $agent_id = $this->session->userdata('agent_id');
        if($this->session->userdata('is_filter_applicable') == 1){
            $data['states'] = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state_code.state) as state",array("agent_filters.agent_id"=>$agent_id),array("agent_filters"=>"agent_filters.state=state_code.state"),NULL,array('state'=>'ASC'),NULL,array("agent_filters"=>"left"),array());
        }
        else{
            $data['states'] = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state) as state",NULL,NULL,NULL,array('state'=>'ASC'),NULL,NULL,array());
        }
        $this->miscelleneous->load_partner_nav_header();
        $this->load->view('partner/approved_defective_parts', $data);
        $this->load->view('partner/partner_footer');
    }

    /**
     * @Desc: This function is used to remove images from partner add/edit form
     *          It is being called using AJAX Request
     * params: partner id
     * return: Boolean
     */
    function remove_uploaded_image() {
        $partner[$this->input->post('type')] = '';
        //Making Database Entry as Empty for selected file
        $this->partner_model->edit_partner($partner, $this->input->post('id'));

        //Logging 
        log_message('info', __FUNCTION__ . $this->input->post('type') . '  File has been removed sucessfully for partner id ' . $this->input->post('id'));
        echo TRUE;
    }

    /**
     * @Desc: This function is used to open partner Add/Edit Login details form
     * @params: Partner ID
     * @return: view
     * 
     */
    function get_partner_login_details_form($partner_id) {
        //Getting details for Login for this Partner
        $login = $this->dealer_model->entity_login(array('entity' => 'partner', 'entity_id' => $partner_id));
        if (!empty($login)) {
            //setting flag for New Add
            $login['add'] = TRUE;
        } else {
            //Setting flag for Update
            $login['edit'] = TRUE;
        }
        $login['partner_id'] = $partner_id;
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/partner_login_details_form', array('login' => $login));
    }

    /**
     * @Desc: This function is used to process partner login add/edit form
     * @params: POST Array
     * @return: void
     * 
     */
    function process_partner_login_details_form() {
            $choice = $this->input->post('choice');
            $partner_id = $this->input->post('partner_id');
            $login_id_array = $this->input->post('id');
            $email_array = $this->input->post('email');
            $password_array = $this->input->post('password');
            $retype_password_array = $this->input->post('retype_password');
            $username_array = $this->input->post('username');
        if (!empty($choice)) {
            foreach ($choice as $key => $value) {
                $password = $password_array[$key];
                $retype_password = $retype_password_array[$key];
                $username = $username_array[$key];
                $email = $email_array[$key];

                //checking for password and retype password value
                if (strcmp($password, $retype_password) == 0) {
                    if (!empty($login_id_array[$value])) {

                        //Checking for Already Present Username
                        $check_username = $this->dealer_model->entity_login(array('entity' => 'partner', 'user_id' => $username));
                        if (!empty($check_username)) {

                            //Updating values when password matches 
                            $where = array('agent_id' => $login_id_array[$value]);
                            $data['user_id'] = $username;
                            $data['email'] = $email;
                            $data['password'] = md5($password);
                            $data['clear_password'] = $password;

                            if ($this->partner_model->update_login_details($data, $where)) {
                                //Log Message
                                log_message('info', __FUNCTION__ . ' Partner Login has been updated for id : ' . $partner_id . ' with values ' . print_r($data, TRUE));
                                
                                //Getting template from Database to send mail
                                $accountManagerData = $this->miscelleneous->get_am_data($partner_id);
                                $login_template = $this->booking_model->get_booking_email_template("partner_login_details");
                                if (!empty($login_template)) {

                                    $login_email['username'] = $data['user_id'];
                                    $login_email['password'] = $data['clear_password'];
                                    $cc = $login_template[3];
                                    if($accountManagerData){
                                        $accountManagerEmail = $accountManagerData[0]['official_email'];
                                        $cc = $login_template[3].",".$accountManagerEmail;
                                    }

                                    $login_subject = $login_template[4];
                                    $login_emailBody = vsprintf($login_template[0], $login_email);

                                    $this->notify->sendEmail($login_template[2], $data['email'], $cc, "",$login_subject, $login_emailBody, "",'partner_login_details');

                                    log_message('info', $login_subject . " Email Send successfully" . $login_emailBody);
                                } else {
                                    //Logging Error
                                    log_message('info', " Error in Getting Email Template for New Vendor Login credentials Mail");
                                }
                            } else {
                                //Log Message
                                log_message('info', __FUNCTION__ . ' Error in updating Partner Login for id : ' . $partner_id . ' with values ' . print_r($data, TRUE));
                            }
                        } else {
                            //Redirecting with Error message
                            //Setting error session data 

                            $userSession = array('login_error' => 'Username Already Exists');
                            $this->session->set_userdata($userSession);
                            redirect(base_url() . 'employee/partner/get_partner_login_details_form/' . $partner_id);
                        }
                    } else {
                        //Add New Row in Partner Login Table
                        $data['entity_id'] = $partner_id;
                        $data['entity'] = "partner";
                        $data['user_id'] = $username;
                        $data['email'] = $email;
                        $data['password'] = md5($password);
                        $data['clear_password'] = $password;
                        $data['active'] = 1;

                        $check_username = $this->dealer_model->entity_login(array('entity' => 'partner', 'user_id' => $username));
                        
                        if (empty($check_username)) {
                            $p_where = array('id' => $partner_id);
                            //Getting name of Partner by Partner ID
                            $partner_details = $this->partner_model->get_all_partner($p_where);
                            $data['agent_name'] = $partner_details[0]['public_name'];
                            $data['entity_name'] = $partner_details[0]['public_name'];
                            $s1 = $this->dealer_model->insert_entity_login($data);
                            if ($s1) {
                                //Log Message
                                log_message('info', __FUNCTION__ . ' Partner Login has been Added for id : ' . $partner_id . ' with values ' . print_r($data, TRUE));
                                //Getting template from Database to send mail
                                $accountManagerData = $this->miscelleneous->get_am_data($partner_id);
                                $login_template = $this->booking_model->get_booking_email_template("partner_login_details");
                                if (!empty($login_template)) {
                                    $login_email['username'] = $data['user_id'];
                                    $login_email['password'] = $data['clear_password'];
                                    $cc = $login_template[3];
                                    if($accountManagerData){
                                        $accountManagerEmail = $accountManagerData[0]['official_email'];
                                        $cc = $login_template[3].",".$accountManagerEmail;
                                    }

                                    $login_subject = $login_template[4];
                                    $login_emailBody = vsprintf($login_template[0], $login_email);

                                    $this->notify->sendEmail($login_template[2], $data['email'], $cc, "",$login_subject, $login_emailBody, "",'partner_login_details');

                                    log_message('info', $login_subject . " Email Send successfully" . $login_emailBody);
                                } else {
                                    //Logging Error
                                    log_message('info', " Error in Getting Email Template for New Vendor Login credentials Mail");
                                }
                            } else {
                                //Log Message
                                log_message('info', __FUNCTION__ . ' Error in Adding Partner Login Details for id : ' . $partner_id . ' with values ' . print_r($data, TRUE));
                            }
                        } else {
                            //Redirecting with Error message
                            //Setting error session data 

                            $userSession = array('login_error' => 'Username Already Exists ');
                            $this->session->set_userdata($userSession);
                            redirect(base_url() . 'employee/partner/get_partner_login_details_form/' . $partner_id);
                        }
                    }
                } else {

                    //When password dosen't matches
                    //Setting error session data 
                    $userSession = array('login_error' => 'Passwords does not match for Login ' . ($value + 1));
                    $this->session->set_userdata($userSession);

                    redirect(base_url() . 'employee/partner/get_partner_login_details_form/' . $partner_id);
                }
            }

            //Setting success session data 
            $userSession = array('login_success' => 'Partner Login has been Added');
            $this->session->set_userdata($userSession);

            redirect(base_url() . 'employee/partner/get_partner_login_details_form/' . $partner_id);
        } else {
            //Setting error session data 
            $userSession = array('login_error' => 'No Row has been selected for Add / Edit');
            $this->session->set_userdata($userSession);
            redirect(base_url() . 'employee/partner/get_partner_login_details_form/' . $partner_id);
        }
    }

    /**
     * @Desc: This function is used to show default Partner Login Page
     * @params: void
     * @return: view
     * 
     */
    function partner_default_page() {
        $this->checkUserSession();
        //Getting Spare Parts Details
        $partner_id = $this->session->userdata('partner_id');
        $state = 0;
        if($this->session->userdata('is_filter_applicable') == 1){
            $state = 1;
        }
        $data['escalation_reason'] = $this->vendor_model->getEscalationReason(array('entity' => 'partner', 'active' => '1'));
        $where = "spare_parts_details.partner_id = '" . $partner_id . "' AND status = '" . SPARE_PARTS_REQUESTED . "' "
                . " AND booking_details.current_status IN ('"._247AROUND_PENDING."', '"._247AROUND_RESCHEDULED."') ";
        $total_rows = $this->partner_model->get_spare_parts_booking_list($where, false, false, false,$state);
        $data['spare_parts'] = $total_rows[0]['total_rows'];
        $this->miscelleneous->load_partner_nav_header();
        //$this->load->view('partner/header');
        if($this->session->userdata('user_group') == PARTNER_CALL_CENTER_USER_GROUP){
            $this->load->view('partner/partner_default_page_cc', $data);
        }
        else{
            $this->load->view('partner/partner_default_page',$data);
        }
        $this->load->view('partner/partner_footer');
        if(!$this->session->userdata("login_by")){
            $this->load->view('employee/header/push_notification');
        }
    }

    /**
     * @desc: Partner search booking by Phone number or Booking id
     */
    function search() {
        log_message('info', __FUNCTION__ . "  Partner ID: " . $this->session->userdata('partner_id'));
        $this->checkUserSession();
        $searched_text = trim($this->input->post('searched_text'));
        $partner_id = $this->session->userdata('partner_id');
        $data['data'] = $this->partner_model->search_booking_history(trim($searched_text), $partner_id);

        if (!empty($data['data'])) {
            $this->miscelleneous->load_partner_nav_header();
            //$this->load->view('partner/header');
            $this->load->view('partner/bookinghistory', $data);
            $this->load->view('partner/partner_footer');
        } else {
            //if user not found set error session data
            $output = "Booking Not Found";
            $userSession = array('error' => $output);
            $this->session->set_userdata($userSession);
            if (preg_match("/^[6-9]{1}[0-9]{9}$/", $searched_text)) {
                redirect(base_url() . 'partner/booking_form/' . $searched_text);
            } else {
                redirect(base_url() . 'partner/home');
            }
        }
    }

    /**
     * @desc: This is used to return customer net payable, Its called by Ajax
     */
    function get_price_for_partner() {
        $add_booking = NULL;
        log_message('info', __FUNCTION__ . "  Partner ID: " . $this->session->userdata('partner_id'));
        $this->checkUserSession();
        $service_id = $this->input->post('service_id');
        $brand = $this->input->post('brand');
        $category = $this->input->post('category');
        $capacity = $this->input->post('capacity');
        $city = $this->input->post('city');
        $pincode = $this->input->post('pincode');
        $service_category = $this->input->post('service_category');
        $partner_id = $this->session->userdata('partner_id');
        $booking_id = $this->input->post('booking_id');
        $partner_type = $this->input->post('partner_type');
        $assigned_vendor_id = $this->input->post("assigned_vendor_id");
        $is_repeat = $this->input->post("is_repeat");
        $contact = $this->input->post("contact");
        if($this->input->post("add_booking")){
            $add_booking = $this->input->post("add_booking");
        }
        $result = array();

        if ($partner_type == OEM) {
            $result = $this->partner_model->getPrices($service_id, $category, $capacity, $partner_id, "", $brand,TRUE,$add_booking);
        } else {
            $result = $this->partner_model->getPrices($service_id, $category, $capacity, $partner_id, "", "",TRUE,$add_booking);
        }
        if (!empty($result)) {
            $p_where = array('id' => $partner_id);
            $partner_details = $this->partner_model->get_all_partner($p_where);
            if (empty($assigned_vendor_id)) {
                $data = $this->miscelleneous->check_upcountry_vendor_availability($city, $pincode, $service_id, $partner_details, NULL, $brand);
            } else {

                $vendor_data = array();
                $vendor_data[0]['vendor_id'] = $assigned_vendor_id;
                $vendor_data[0]['city'] = $city;
                $vendor_data[0]['min_upcountry_distance'] = $this->vendor_model->getVendorDetails("min_upcountry_distance", array('id' => $assigned_vendor_id))[0]['min_upcountry_distance'];
                $data = $this->upcountry_model->action_upcountry_booking($city, $pincode, $vendor_data, $partner_details);
            }

            $html = "<table class='table priceList table-striped table-bordered'><thead><tr><th class='text-center'>Service Category</th>"
                    . "<th class='text-center'>Final Charges</th>"
                    . "<th class='text-center' id='selected_service'>Selected Services</th>"
                    . "</tr></thead><tbody>";
            $i = 0;
            $explode = array();
            if (!empty($service_category)) {
                $explode = explode(",", $service_category);
            }
            foreach ($result as $prices) {
                
                $customer_total = $prices['customer_total'];
                $partner_net_payable = $prices['partner_net_payable'];
                $customer_net_payable = $prices['customer_net_payable'];
                
                if($prices['service_category'] == REPAIR_OOW_PARTS_PRICE_TAGS){
                     
                     if(!empty($booking_id)){
                         $unit_details = $this->booking_model->get_unit_details(array('booking_id' => $booking_id, "price_tags" =>REPAIR_OOW_PARTS_PRICE_TAGS), 
                                 false, "customer_total, partner_net_payable, customer_net_payable");
                         if(!empty($unit_details)){
                            $customer_total = $unit_details[0]['customer_total'];
                            $partner_net_payable = $unit_details[0]['partner_net_payable'];
                            $customer_net_payable = $unit_details[0]['customer_net_payable'];
                         }
                     }
                     
                }
                $checkboxClass = $prices['product_or_services'];
                $ch  = "check_active_paid('".$i."')";
               $onclick = 'onclick="final_price(), '.$ch.', set_upcountry(), disableCheckbox(this), get_symptom()"';
                $tempHelperString = "";
               if($is_repeat){
                    if($prices['service_category'] ==  REPEAT_BOOKING_TAG){
                        $tempHelperString.= " checked ";
                        $checkboxClass = "repeat_".$prices['product_or_services'];
                    }
                    $tempHelperString.=  "style= 'pointer-events: none;'";
                }
                else{
                    if (in_array($prices['service_category'], $explode)) {
                        $tempHelperString .= " checked ";
                        if($prices['service_category'] ==  REPEAT_BOOKING_TAG){
                            $checkboxClass = "repeat_".$prices['product_or_services'];
                            $tempString = "'".$contact."','".$service_id."','".$partner_id."',this.checked,true";
                            $onclick = 'onclick="final_price(),'.$ch.', set_upcountry(), get_symptom(),disableCheckbox(this),get_parent_booking('.$tempString.')"';
                            //$onclick = 'onclick="get_parent_booking('.$tempString.')"';
                         }
                    }
                    else{
                         if($prices['service_category'] ==  REPEAT_BOOKING_TAG){
                             $checkboxClass = "repeat_".$prices['product_or_services'];
                            $tempString = "'".$contact."','".$service_id."','".$partner_id."',this.checked,false";
                            $onclick = 'onclick="final_price(),'.$ch.', get_symptom(), set_upcountry(), disableCheckbox(this), get_parent_booking('.$tempString.')"';
                            //$onclick = 'onclick="get_parent_booking('.$tempString.')"';
                         }
                    }
                }
                if($prices['service_category'] == REPAIR_OOW_PARTS_PRICE_TAGS ){
                    if($customer_net_payable == 0 ){
                        $tempHelperString .= " disabled onclick='return false;' ";
                    } else {
                        $tempHelperString .= " onclick='return false;' ";
                    }   
                }
                $html .= "<tr class='text-center'><td>" . $prices['service_category'] . "</td>";
                $html .= "<td>" . $customer_net_payable . "</td>";
                $html .= "<td><input type='hidden'name ='is_up_val' data-customer_price = '".$prices['upcountry_customer_price']."' data-flat_upcountry = '".$prices['flat_upcountry']."' id='is_up_val_" . $i . "' value ='" . $prices['is_upcountry'] . "' /><input class='price_checkbox $checkboxClass'";
                $html .= " type='checkbox' id='checkbox_" . $i . "'";
                $html .= "name='prices[]'";
                $html .= $tempHelperString.$onclick."value=" . $prices['id'] . "_" . intval($customer_total) . "_" . intval($partner_net_payable) . "_" . $i . "  data-price_tag='".$prices['service_category']."' ></td><tr>";
                $i++;
            }
            $html .= "<tr class='text-center'><td>Upcountry Services</td>";
            $html .= "<td id='upcountry_charges'>0.00</td>";
            $html .= "<td><input type='checkbox' id='checkbox_upcountry' onclick='final_price()'"
                    . " name='upcountry_checkbox' value='upcountry_0_0' disabled ></td></tbody></table>";
            $form_data['table'] = $html;
            $form_data['upcountry_data'] = json_encode($data, TRUE);

           print_r(json_encode($form_data, TRUE));
        } else {
            echo "ERROR";
        }
    }

    /**
     * @desc: This is called by Ajax to return City
     * @param String $pincode
     */
    function get_district_by_pincode($pincode, $service_id) {
        $select = 'vendor_pincode_mapping.City as district,vendor_pincode_mapping.State as state';
        $post_city = $this->input->post('city');
        $where = array(
            'service_centres.active' => 1,
            'service_centres.on_off' => 1,
            'vendor_pincode_mapping.Pincode' => $pincode,
            'vendor_pincode_mapping.Appliance_ID' => $service_id);
        $city = $this->vendor_model->get_vendor_mapping_data($where, $select);
        if(empty($city)){
            $saas_flag = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
            if($saas_flag){
                $select = 'india_pincode.district as district,india_pincode.state as state';
                $post_city = $this->input->post('city');
                $where = array('india_pincode.pincode' => $pincode);
                $city = $this->reusable_model->get_search_result_data('india_pincode',$select,$where,NULL,NULL,NULL,NULL,NULL,array());
            }
            else{
               echo 'ERROR';
            }
            $booking = array('booking_id' => 'Not_Generated', 'booking_pincode' => $pincode,'service_id' => $service_id, 'partner_id' => $this->session->userdata('partner_id'),'city'=>'Not_Received',
            'order_id'=>'Not_Received');
            $this->miscelleneous->sf_not_exist_for_pincode($booking);
        }
        if(!empty($city)) {
            $stateWhere['service_id'] = $service_id;
            $stateWhere['state'] = $city[0]['state'];
            $stateWhere['partner_id'] = $this->session->userdata('partner_id');
            $doWeServe = array("1");
            if($this->session->userdata('partner_id') == VIDEOCON_ID){
                $doWeServe = $this->partner_model->get_partner_operation_region($stateWhere);
            }
            if(!empty($doWeServe)){
                $option = "";
                foreach ($city as $district) {
                    $option .= "<option value='" . $district['district'] . "'";
                    if (count($district) == 1) {
                        $option .= " selected ";
                    } else if (!empty($city)) {
                        if ($post_city === $district['district']) {
                            $option .= "selected";
                        }
                    }
                    $option .= "  >" . $district['district'] . "</option>";
                }
                echo $option;
            }
            else{
                echo "Not_Serve";
            }
        } 
    }

    /**
     * @desc Approve Upcountry charges by Partner. $status o means call from mail 
     * @param String $booking_id
     * @param Integer $status (0 & 1)
     */
    function upcountry_charges_approval($booking_id, $status) {
        log_message('info', __FUNCTION__ . " => Booking Id" . $booking_id . ' status: ' . $status);

        $data = $this->upcountry_model->get_upcountry_service_center_id_by_booking($booking_id);
        if (!empty($data)) {
            if ($data[0]['upcountry_partner_approved'] == 0 & empty($data[0]['assigned_vendor_id'])) {
                log_message('info', __FUNCTION__ . " => On Approval Booking Id" . $booking_id);
                
                if ($status == 0) {// means request from mail
                    $partner_id = $data[0]['partner_id'];
                    $agent = $this->dealer_model->entity_login(array('entity' => 'partner', 'entity_id' =>$partner_id, "active" => 1 ));
                    if(!empty($agent)){
                        $agent_id = $agent[0]['agent_id'];
                        $agent_name = $agent[0]['entity_name'];
                        $agent_type = _247AROUND_PARTNER_STRING;
                    } else {
                        $partner_id = _247AROUND;
                        $agent_id = _247AROUND_DEFAULT_AGENT;
                        $agent_name = _247AROUND_DEFAULT_AGENT_NAME;
                        $agent_type = _247AROUND_EMPLOYEE_STRING;
                    }
                    $type = " Email";
                   
                } else {
                    $agent_id = $this->session->userdata('agent_id');
                    $agent_name = $this->session->userdata('partner_name');
                    $partner_id = $this->session->userdata('partner_id');
                    $type = " Panel";
                    $agent_type = _247AROUND_PARTNER_STRING;
                }
                
                if(date('l' == 'Sunday')){
                    $booking_date = date('d-m-Y', strtotime("+1 days"));
                } else if(date('H') > 12){
                    $booking_date = date('d-m-Y', strtotime("+1 days"));
                } else {
                    $booking_date = date('d-m-Y');
                }
                
                $this->booking_model->update_booking($booking_id, array('initial_booking_date' => $booking_date, 'booking_date' => $booking_date));
                
                // Insert log into booking state change
                $this->notify->insert_state_change($booking_id, UPCOUNTRY_CHARGES_APPROVED, _247AROUND_PENDING, "Upcountry Charges Approved From " . $type.". Booking date is reset as upcountry charges approved", $agent_id, $agent_name, 
                        ACTOR_UPCOUNTRY_CHARGES_APPROV_BY_PARTNER,NEXT_ACTION_UPCOUNTRY_CHARGES_APPROV_BY_PARTNER,$partner_id);
                
                

                $assigned = $this->miscelleneous->assign_vendor_process($data[0]['service_center_id'], $booking_id, 
                        $data[0]['partner_id'],$agent_id,$agent_type);
                if ($assigned) {

                    log_message('info', __FUNCTION__ . " => Continue Process" . $booking_id);
                    //Send SMS to customer
                    $sms['tag'] = "service_centre_assigned";
                    $sms['phone_no'] = $data[0]['booking_primary_contact_no'];
                    $sms['booking_id'] = $booking_id;
                    $sms['type'] = "user";
                    $sms['type_id'] = $data[0]['user_id'];
                    if($data[0]['partner_id'] == VIDEOCON_ID){
                        $sms['smsData']['cc_number'] = "0120-4500600";
                    }
                    else if($data[0]['partner_id'] == SHARP_ID){
                        $sms['smsData']['cc_number'] = SHARP_CALLCENTER_NUMBER;
                    }
                    else{
                       $sms['smsData']['cc_number'] = _247AROUND_CALLCENTER_NUMBER; 
                    }
                    
                    $this->notify->send_sms_msg91($sms);
                    log_message('info', "Send SMS to customer: " . $booking_id);

                    //Prepare job card
                    $this->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);
                    $this->booking_utilities->lib_send_mail_to_vendor($booking_id, "");
                    log_message('info', "Async Process to create Job card: " . $booking_id);

                    $this->notify->insert_state_change($booking_id, ASSIGNED_VENDOR, UPCOUNTRY_CHARGES_APPROVED, "Service Center Id: " . $data[0]['service_center_id'], $agent_id, 
                            $agent_name,ACTOR_UPCOUNTRY_CHARGES_APPROV_BY_PARTNER,NEXT_ACTION_UPCOUNTRY_CHARGES_APPROV_BY_PARTNER, $partner_id);

                    if ($status == 0) {
                        echo "<script>alert('Thanks For Approving Upcountry Charges');</script>";
                    } else {
                        $userSession = array('success' => 'Booking Approved Successfully.');
                        $this->session->set_userdata($userSession);
                        redirect(base_url() . "partner/get_waiting_for_approval_upcountry_charges");
                    }
                } else {
                    log_message('info', __FUNCTION__ . " => Not Assigned Booking Id" . $booking_id);
                    $msg = "Thanks, Booking Has Been Already Approved.";
                }
            } else {
                log_message('info', __FUNCTION__ . " => Already Approve Booking Id" . $booking_id);
                $msg = "Thanks, Booking Has Been Already Approved.";
            }
        } else {
            log_message('info', __FUNCTION__ . " => Failed: Partner try to approve Booking Id" . $booking_id);
            $to = "abhaya@247around.com";
            $cc = "vijaya@247around.com";
            $message = "Partner try to approve Booking Id " . $booking_id . " but somehow it failed. <br/>Please check this booking.";
            $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, '', 'UpCountry Approval Failed', $message, '',PARTNER_APPROVAL_FAILED, "", $booking_id);
            $msg = "Your request has been submitted. We will fix it shortly.";
        }

        if ($status == 0) {
            echo "<script>alert('" . $msg . "');</script>";
        } else {
            $userSession = array('error' => $msg);
            $this->session->set_userdata($userSession);
            redirect(base_url() . "partner/get_waiting_for_approval_upcountry_charges");
        }
    }

    /**
     * @desc This is uesd to reject Upcountry charges. $status o means reject from EMail
     * @param String $booking_id
     * @param String $status
     */
    function reject_upcountry_charges($booking_id, $status) {
        log_message('info', __FUNCTION__ . " => Booking Id" . $booking_id . ' status: ' . $status);
        $data = $this->booking_model->getbooking_history($booking_id);
        if (is_null($data[0]['assigned_vendor_id']) && $data[0]['current_status'] != _247AROUND_CANCELLED) {
            if ($status == 0) {// means request from mail
                
                $partner_id = $data[0]['partner_id'];
              
                $agent = $this->dealer_model->entity_login(array('entity' => 'partner', 'entity_id' =>$partner_id, "active" => 1 ));
                if(!empty($agent)){
                    $agent_id = $agent[0]['agent_id'];
                    $agent_name = $agent[0]['entity_name'];
                } else {
                    $partner_id = _247AROUND;
                    $agent_id = _247AROUND_DEFAULT_AGENT;
                    $agent_name = _247AROUND_DEFAULT_AGENT_NAME;
                }
                
                $type = " Email";
            } else {
                $agent_id = $this->session->userdata('agent_id');
                $agent_name = $this->session->userdata('partner_name');
                $partner_id = $this->session->userdata('partner_id');
                $type = "Panel";
            }
            
            $this->miscelleneous->process_cancel_form($booking_id, _247AROUND_PENDING, UPCOUNTRY_CHARGES_NOT_APPROVED, "Upcountry Charges Rejected By Partner From " . $type, $agent_id, $agent_name, $data[0]['partner_id'], $partner_id);

            if ($status == 0) {
                echo "<script>alert('Upcountry Charges Rejected Successfully');</script>";
            } else {
                $userSession = array('success' => 'Upcountry Charges Rejected Successfully');
                $this->session->set_userdata($userSession);
                redirect(base_url() . "partner/get_waiting_for_approval_upcountry_charges");
            }
        } else {
            log_message('info', __FUNCTION__ . " => Booking is not rejected. Booking Id" . $booking_id);
            if ($status == 0) {
                echo "<script>alert('Upcountry Charges Already Rejected');</script>";
            } else {
                $userSession = array('error' => 'Upcountry Charges Already Rejected');
                $this->session->set_userdata($userSession);
                redirect(base_url() . "partner/get_waiting_for_approval_upcountry_charges");
            }
        }
    }

    /**
     * @desc: used to display list of waiting to approve upcountry charges
     */
    function get_waiting_for_approval_upcountry_charges() {
        $this->checkUserSession();
        $agent_id = $this->session->userdata('agent_id');
        if($this->session->userdata('is_filter_applicable') == 1){
            $data['states'] = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state_code.state) as state",array("agent_filters.agent_id"=>$agent_id),array("agent_filters"=>"agent_filters.state=state_code.state"),NULL,array('state'=>'ASC'),NULL,array("agent_filters"=>"left"),array());
        }
        else{
            $data['states'] = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state) as state",NULL,NULL,NULL,array('state'=>'ASC'),NULL,NULL,array());
        }
        $data['is_ajax'] = $this->input->post('is_ajax');
        if(empty($this->input->post('is_ajax'))){
            $this->miscelleneous->load_partner_nav_header();
            $this->load->view('partner/get_waiting_to_approval_upcountry', $data);
            $this->load->view('partner/partner_footer');
        }else{
            $this->load->view('partner/get_waiting_to_approval_upcountry', $data);
        }
    }

    /**
     * @desc: This method Cancelled those upcountry booking(3 days old) who has not approved by partner
     */
    function auto_reject_upcountry_charges() {
        log_message('info', __FUNCTION__);
        $data = $this->booking_model->get_booking_to_cancel_not_approved_upcountry();
        if (!empty($data)) {
            foreach ($data as $value) {
                log_message('info', __FUNCTION__ . " => Cancel Booking Id" . $value['booking_id']);
                $agent_id = _247AROUND_DEFAULT_AGENT;
                $agent_name = _247AROUND_DEFAULT_AGENT_NAME;
                $partner_id = _247AROUND;
                $partner_current_status = "";
                $partner_internal_status = "";
                $actor = $next_action = 'NULL';
                $partner_status = $this->booking_utilities->get_partner_status_mapping_data("Cancelled", UPCOUNTRY_CHARGES_NOT_APPROVED, $value['partner_id'], $value['booking_id']);
                if (!empty($partner_status)) {
                    $partner_current_status = $partner_status[0];
                    $partner_internal_status = $partner_status[1];
                    $actor = $partner_status[2];
                    $next_action = $partner_status[3];
                }
                $this->booking_model->update_booking($value['booking_id'], array("current_status" => "Cancelled", "internal_status" => UPCOUNTRY_CHARGES_NOT_APPROVED,
                    'cancellation_reason' => UPCOUNTRY_CHARGES_NOT_APPROVED, "partner_current_status" => $partner_current_status,
                    'partner_internal_status' => $partner_internal_status,'actor'=>$actor,'next_action'=>$next_action));
                
                $this->service_centers_model->update_spare_parts(array('booking_id' => $value['booking_id']), array('status' => _247AROUND_CANCELLED));

                $this->booking_model->update_booking_unit_details($value['bookng_id'], array('booking_status' => 'Cancelled'));
                $this->notify->insert_state_change($value['booking_id'], UPCOUNTRY_CHARGES_NOT_APPROVED, _247AROUND_PENDING, "Upcountry Charges Rejected From " . "AUTO ", $agent_id, 
                        $agent_name, $actor,$next_action,$partner_id);
            }

            //Notify
            $this->notify->sendEmail(NOREPLY_EMAIL_ID, ANUJ_EMAIL_ID, '', '', 'Upcountry Bookings Cancelled', print_r($data, TRUE), '',UPCOUNTRY_BOOKING_CANCELLED, "", $value['booking_id']);
        }
    }

    /**
     * @desc: This method is used to show the partner brand logo upload from
     * @param: void
     * @return:void
     */
    function upload_partner_brand_logo($id = "", $name = "") {
        $partner_logo_deatil = $this->booking_model->get_partner_logo('*', array('partner_id'=>$id));
        $data['partner'] = array('partner_id' => $id,
            'public_name' => urldecode($name),
            'partner_logo_detail' => $partner_logo_deatil,
        );
        if(empty($partner_logo_deatil)){
            $priority = $this->booking_model->get_partner_logo('max(logo_priority) as logo_priority', array())[0]['logo_priority'];
            $data['partner']['logo_priority'] = $priority + 1;
        }
        else{
            $data['partner']['logo_priority'] = $partner_logo_deatil[0]['logo_priority'];
        }
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/upload_partner_brand_logo', $data);
    }

    /**
     * @desc: This method is used to insert the partner brand logo into database
     * @param: void
     * @return:void
     */
    function process_upload_partner_brand_logo() {
        $partner_name = $this->input->post('partner_name');
        $partner_id = $this->input->post('partner_id');
        if (!empty($partner_name) && !empty($partner_id)) {
            //Do not remove unused $tmp_name. 
            foreach ($_FILES["partner_brand_logo"]["tmp_name"] as $key => $tmp_name) {

                $tmpFile = $_FILES['partner_brand_logo']['tmp_name'][$key];
                $ext = explode('.', $_FILES["partner_brand_logo"]["name"][$key]);
                $file_name = preg_replace('/\s+/', '', $partner_name). rand(10, 100) . "." . end($ext);
                if (!file_exists(FCPATH . 'images/' . $file_name)) {
                    //move_uploaded_file($tmpFile, FCPATH . 'images/' . $file_name);
                    //Uploading images to S3 
                    $bucket = BITBUCKET_DIRECTORY;
                    $directory = "misc-images/" . $file_name;
                    $this->s3->putObjectFile($tmpFile, $bucket, $directory, S3::ACL_PUBLIC_READ);
                    $data['partner_id'] = $partner_id;
                    $data['partner_logo'] = $file_name;
                    $data['alt_text'] = $partner_name;
                    $data['logo_priority'] = $this->input->post("logo_priority");

                    //insert partner brand logo path into database
                    $res[$key] = $this->partner_model->upload_partner_brand_logo($data);
                }
            }
            if ($res) {
                $this->session->set_flashdata('success', 'Partner Logo has been saved successfully');
                redirect(base_url() . "employee/partner/upload_partner_brand_logo/" . $partner_id . "/" . $partner_name, 'refresh');
            } else {
                $this->session->set_flashdata('failed', 'Error in saving Partner Logo. Please Try Again...');
                redirect(base_url() . "employee/partner/upload_partner_brand_logo/" . $partner_id . "/" . $partner_name, 'refresh');
            }
        } else {
            $this->session->set_flashdata('failed', 'Please Select Partner Name');
            redirect(base_url() . "employee/partner/upload_partner_brand_logo" . $partner_id . "/" . $partner_name, 'refresh');
        }
    }

    /**
     * @desc: This method is used to edit the partner details from partner CRM
     * @param: void
     * @return:void
     */
    function show_partner_edit_details_form() {
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');
        $data['partner_details'] = $this->partner_model->getpartner($partner_id);
        $this->miscelleneous->load_partner_nav_header();
        //$this->load->view('partner/header');
        $this->load->view('partner/edit_partner_details', $data);
        $this->load->view('partner/partner_footer');
    }

    /**
     * @desc: This method is used to process the edit form of the partner details from partner CRM
     * @param: void
     * @return:void
     */
    function process_partner_edit_details() {
        log_message('info', __FUNCTION__ . ' partner_id: ' . $this->session->userdata('partner_id'));
        $this->checkUserSession();

        //store POST data into array
        $partner_data = array();
        $partner_id = $this->input->post('id');
        $partner_data['company_name'] = $this->input->post('company_name');
        $partner_data['public_name'] = $this->input->post('public_name');
        $partner_data['address'] = $this->input->post('address');
        $partner_data['landmark'] = $this->input->post('landmark');
        $partner_data['pincode'] = $this->input->post('pincode');
        $partner_data['district'] = $this->input->post('district');
        $partner_data['state'] = $this->input->post('state');
        $partner_data['primary_contact_name'] = $this->input->post('primary_contact_name');
        $partner_data['primary_contact_email'] = $this->input->post('primary_contact_email');
        $partner_data['primary_contact_phone_1'] = $this->input->post('primary_contact_phone_1');
        $partner_data['primary_contact_phone_2'] = $this->input->post('primary_contact_phone_2');
        $partner_data['owner_name'] = $this->input->post('owner_name');
        $partner_data['owner_email'] = $this->input->post('owner_email');
        $partner_data['owner_phone_1'] = $this->input->post('owner_phone_1');
        $partner_data['owner_phone_2'] = $this->input->post('owner_phone_2');
        $partner_data['owner_alternate_email'] = $this->input->post('owner_alternate_email');
        $partner_data['pan'] = $this->input->post('pan');
        $partner_data['tin'] = $this->input->post('tin');
        $partner_data['registration_no'] = $this->input->post('registration_no');
        $partner_data['cst_no'] = $this->input->post('cst_no');
        $partner_data['spare_notification_email'] = $this->input->post('spare_notification_email');

        if (!empty($partner_data) && !empty($partner_id)) {
            $update_id = $this->partner_model->edit_partner($partner_data, $partner_id);
            if ($update_id) {
                log_message('info', __FUNCTION__ . 'Partner Details has been updated successfully' . $partner_id . " " . print_r($partner_data, true));

                // send mail
                $html = "";
                foreach ($partner_data as $key => $value) {
                    $html .= '<b>' . $key . '</b>' . " = " . $value . '<br>';
                }

                $to = ANUJ_EMAIL_ID;
                $subject = $partner_data['public_name'] . "  : Partner Details Has been Updated";
                $message = "Following details has been updated by partner: " . $this->session->userdata('partner_name');
                $message .= "<br>" . $html;
                $sendmail = $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, " ", " ", $subject, $message, "",PARTNER_DETAILS_UPDATED);

                if ($sendmail) {
                    log_message('info', __FUNCTION__ . 'Mail Send successfully');
                } else {
                    log_message('info', __FUNCTION__ . 'Error in Sending Mail');
                }

                //redirect to details page
                $success_msg = "Details has been updated successfully";
                $this->session->set_flashdata('success_msg', $success_msg);
                redirect(base_url() . 'employee/partner/show_partner_edit_details_form');
            } else {
                log_message('info', __FUNCTION__ . 'Error in updating partner details' . $partner_id . " " . print_r($partner_data, true));
                $error_msg = "Error!!! Please Try Again";
                $this->session->set_flashdata('error_msg', $error_msg);
                redirect(base_url() . 'employee/partner/show_partner_edit_details_form');
            }
        } else {
            log_message('info', __FUNCTION__ . 'Error in updating partner details' . $partner_id . " " . print_r($partner_data, true));
            $error_msg = "Error!!! Please Try Again";
            $this->session->set_flashdata('error_msg', $error_msg);
            redirect(base_url() . 'employee/partner/show_partner_edit_details_form');
        }
    }

    /**
     * @desc Get upcountry details for partner booking
     * @param String $booking_id
     * @param int $is_customer_paid
     */
    function booking_upcountry_details($booking_id, $is_customer_paid, $flat_upcountry) {
        if ($is_customer_paid > 0) {
            $is_customer_paid = 1;
        }
        if($flat_upcountry == 1){
            $is_customer_paid = 1;
        }
        $data['data'] = $this->upcountry_model->upcountry_booking_list("", $booking_id, false, $is_customer_paid, $flat_upcountry);

        $this->load->view('service_centers/upcountry_booking_details', $data);
    }

    function get_dealer_details() {
        $partner_id = $this->input->post('partner_id');
        $search_term = $this->input->post('search_term');
        $column = $this->input->post("dealer_field");
        $condition = array(
            "where" => array('partner_id' => $partner_id),
            "where_in" => array(),
            "search" => array($column => $search_term),
            "order_by" => $column);
        $select = "dealer_name, dealer_details.dealer_id, dealer_phone_number_1";
        $condition['length'] = -1;
        $dealer_data = $this->dealer_model->get_dealer_mapping_details($condition, $select);
        $response = "<ul id='dealer_list'>";
        if (!empty($dealer_data)) {

            foreach ($dealer_data as $value) {
                $response .= "<li onclick ='";
                $response .= "selectDealer(";
                $response .= '"' . $value['dealer_name'] . '", "' . $value['dealer_phone_number_1'] . '", "' . $value['dealer_id'] . '"';
                $response .= ")'> ";
                $response .= $value['dealer_name'] . "(<b>" . $value['dealer_phone_number_1'] . "</b>)";
                $response .= '</li>';
            }
        }
        $response .= "</ul>";
        echo $response;
    }

    /**
     * @Desc: This function is used to show Partner Login Page for inactive partner
     * @params: void
     * @return: view
     * 
     */
    function inactive_partner_default_page() {

        $partner_id = $this->session->userdata('partner_id');
        $data['vendor_partner'] = "partner";
        $data['vendor_partner_id'] = $partner_id;
        $invoice['invoice_array'] = $this->invoices_model->getInvoicingData($data);

        $data2['partner_vendor'] = "partner";
        $data2['partner_vendor_id'] = $partner_id;
        $invoice['bank_statement'] = $this->invoices_model->get_bank_transactions_details('*', $data2);
        $this->load->view('partner/inactive_partner_header');
        $this->load->view('partner/invoice_summary', $invoice);
    }

    /**
     * @Desc: This function is used to send the reminder email to partners
     * for defective parts not shipped 
     * @params: void()
     * @return: void()
     * 
     */
    function get_defective_parts_acknowledge_reminder_data() {
        log_message('info', __FUNCTION__ . ' => Defective Parts Acknowledge Reminder By Cron');

        $where_get_partner = array('partners.is_active' => '1');
        $select = "partners.id, partners.primary_contact_email, partners.public_name,partners.primary_contact_name,partners.spare_notification_email";
        //Get all Active partners
        $partners = $this->partner_model->getpartner_details($select, $where_get_partner, '1');
        foreach ($partners as $partner) {

            //fetch spare parts sent 7 days or more ago
            $select = "spare_parts_details.booking_id,DATE_FORMAT(spare_parts_details.defective_part_shipped_date, '%D %b %Y') as date";
            $where = array('spare_parts_details.partner_id' => $partner['id'],
                'defective_part_shipped_date IS NOT NULL' => null,
                "spare_parts_details.status IN ('".DEFECTIVE_PARTS_SHIPPED."')" => null,
                "booking_details.current_status IN ('"._247AROUND_PENDING."', '"._247AROUND_RESCHEDULED."')" => null);
            $defective_parts_acknowledge_data = $this->partner_model->get_spare_parts_by_any($select, $where, true);

            if (!empty($defective_parts_acknowledge_data)) {
                $this->table->set_heading('Booking Id', 'Defective Part Shipped Date');
                $template = array(
                    'table_open' => '<table border="1" cellpadding="4" cellspacing="0">'
                );

                $this->table->set_template($template);
                $html_table = $this->table->generate($defective_parts_acknowledge_data);

                //send email
                $email_template = $this->booking_model->get_booking_email_template("defective_parts_acknowledge_reminder");
                $to = !empty($partner['spare_notification_email']) ? $partner['spare_notification_email'] : $partner['primary_contact_email'];
                $cc = $email_template[3];
                $subject = $email_template[4];
                $message = vsprintf($email_template[0], $html_table);

                $sendmail = $this->notify->sendEmail($email_template[2], $to, $cc, "", $subject, $message, "",'defective_parts_acknowledge_reminder');

                if ($sendmail) {
                    log_message('info', __FUNCTION__ . 'Defective Spares Yet to be Acknowledged Mail has been sent to partner ' . $partner['public_name'] . ' successfully');
                } else {
                    log_message('info', __FUNCTION__ . 'Error in Sending Defective Spares Yet to be Acknowledged Mail to partner ' . $partner['public_name']);
                }
            }
        }
    }

    /**
     * @Desc: This function is used to auto acknowledge the defective parts after 14 days 
     * @params: void()
     * @return: void()
     * 
     */
    function auto_acknowledge_defective_parts() {
        log_message('info', __FUNCTION__ . ' => Auto Acknowledge Defective Parts');

        $where_get_partner = array('partners.is_active' => '1');
        $select = "partners.id, partners.primary_contact_email, partners.public_name,partners.primary_contact_name,partners.spare_notification_email";
        //Get all Active partners
        $partners = $this->partner_model->getpartner_details($select, $where_get_partner, '1');
        foreach ($partners as $partner) {

            $select = "spare_parts_details.booking_id,spare_parts_details.id, DATE_FORMAT(spare_parts_details.defective_part_shipped_date, '%D %b %Y') as date";
            $where = array('spare_parts_details.defective_return_to_entity_id' => $partner['id'],
                'spare_parts_details.defective_part_required' => 1 ,
                'spare_parts_details.defective_part_rejected_by_partner'=>0,
                'spare_parts_details.defective_return_to_entity_type' => _247AROUND_PARTNER_STRING,
                'DATEDIFF(defective_part_shipped_date,now()) <= -14' => null,
                "spare_parts_details.status IN ('".DEFECTIVE_PARTS_SHIPPED."')" => null,
                "booking_details.current_status IN ('"._247AROUND_PENDING."', '"._247AROUND_RESCHEDULED."')" => null);
            
            $defective_parts_acknowledge_data = $this->partner_model->get_spare_parts_by_any($select, $where, true);
            if (!empty($defective_parts_acknowledge_data)) {

                //update acknowledge
                foreach ($defective_parts_acknowledge_data as $value) {
                    $this->acknowledge_received_defective_parts($value['id'], $value['booking_id'], $partner['id'], true);
                }

                $this->table->set_heading('Booking Id', 'Defective Part Shipped Date');
                $template = array(
                    'table_open' => '<table border="1" cellpadding="4" cellspacing="0">'
                );

                $this->table->set_template($template);
                $html_table = $this->table->generate($defective_parts_acknowledge_data);


                //send email

                $email_template = $this->booking_model->get_booking_email_template("auto_acknowledge_defective_parts");
                $to = !empty($partner['spare_notification_email']) ? $partner['spare_notification_email'] : $partner['primary_contact_email'];
                $cc = $email_template[3];
                $subject = $email_template[4];
                $message = vsprintf($email_template[0], $html_table);

                $sendmail = $this->notify->sendEmail($email_template[2], $to, $cc, "", $subject, $message, "",'auto_acknowledge_defective_parts');

                if ($sendmail) {
                    log_message('info', __FUNCTION__ . 'Report Mail has been send to partner ' . $partner['public_name'] . ' successfully');
                } else {
                    log_message('info', __FUNCTION__ . 'Error in Sending Mail to partner ' . $partner['public_name']);
                }
            }
        }
    }

    /**
     * @Desc: This function is used to show booking_summary with escalation percentage on partner homepage
     * @params: $partner_id string
     * @return: void()
     * 
     */
    function get_partner_booking_summary_data($partner_id) {

        //get bookings count by month 
        $select = "DATE_FORMAT(service_center_closed_date, '%b') AS month, "
                . "SUM(IF(!(current_status = 'Cancelled' OR internal_status ='InProcess_Cancelled') , 1, 0)) AS completed, "
                . "SUM(IF((current_status = 'Cancelled' OR internal_status = 'InProcess_Cancelled'), 1, 0)) AS cancelled, "
                . "SUM(IF(!(current_status = 'Cancelled' OR internal_status ='InProcess_Cancelled') AND booking_details.is_upcountry = '1' AND booking_details.upcountry_partner_approved = '1' AND booking_details.upcountry_paid_by_customer = '0' , 1, 0)) AS upcountry_completed ,"
                . "SUM(IF((current_status = 'Cancelled' OR internal_status = 'InProcess_Cancelled') AND booking_details.is_upcountry = '1' AND booking_details.upcountry_partner_approved = '1' AND booking_details.upcountry_paid_by_customer = '0', 1, 0)) AS upcountry_cancelled";
        $where = array('partner_id' => $partner_id, "booking_details.service_center_closed_date >= (DATE_FORMAT(CURDATE(), '%Y-%m-01') - INTERVAL 1 MONTH)" => NULL);
        $order_by = "YEAR(booking_details.service_center_closed_date),MONTH(booking_details.service_center_closed_date)";
        $group_by = "month";
        $data['bookings_count'] = $this->booking_model->get_bookings_count_by_any($select, $where, $order_by, $group_by);
        if(!empty($data['bookings_count']) && count($data['bookings_count']) == 2){
            $data['completed_booking'] = $data['bookings_count'][1]['completed'];
            $data['last_month_completed_booking'] = $data['bookings_count'][0]['completed'];
            //$data['completed_booking_percentage_change'] = (($data['bookings_count'][1]['completed']/$data['bookings_count'][0]['completed'])*100)-100;
            $data['cancelled_booking'] = $data['bookings_count'][1]['cancelled'];
            $data['last_month_cancelled_booking'] = $data['bookings_count'][0]['cancelled'];
            //$data['cancelled_booking_percentage_change'] = (($data['bookings_count'][1]['cancelled']/$data['bookings_count'][0]['cancelled'])*100)-100;
        }else if(!empty($data['bookings_count']) && count($data['bookings_count']) == 1){
            $data['completed_booking'] = $data['bookings_count'][0]['completed'];
            $data['last_month_completed_booking'] = 0;
            //$data['completed_booking_percentage_change'] = (($data['bookings_count'][0]['completed']/$data['bookings_count'][0]['completed'])*100)-100;
            $data['cancelled_booking'] = $data['bookings_count'][0]['cancelled'];
            $data['last_month_cancelled_booking'] = 0;
            //$data['cancelled_booking_percentage_change'] = (($data['bookings_count'][0]['cancelled']/$data['bookings_count'][0]['cancelled'])*100)-100;
        }else{
             $data['completed_booking'] = 0;
             //$data['completed_booking_percentage_change'] = 0;
             $data['last_month_completed_booking'] = 0;
             $data['cancelled_booking'] = 0;
             $data['last_month_cancelled_booking'] = 0;
             //$data['cancelled_booking_percentage_change'] = 0;
             
        }
        //get escalation percentage
        $data['escalation_percentage'] = $this->partner_model->get_booking_escalation_percantage($partner_id);
        $data['pincode_covered'] = $this->reusable_model->get_search_query('vendor_pincode_mapping','count(distinct pincode) as pincode',NULL,NULL,NULL,NULL,NULL,NULL)->result_array()[0]['pincode'];
        $data['avg_rating'] = $this->reusable_model->get_search_query('booking_details','ROUND( AVG( rating_stars ) , 2 ) AS rating_avg',
                array("current_status"=>"Completed","rating_stars IS NOT NULL"=>NULL,'partner_id'=>$partner_id),NULL,NULL,NULL,NULL,NULL)->result_array()[0]['rating_avg'];
       
        $p = $this->get_prepaid_amount($partner_id);
        if(!empty($p)){
            $data['prepaid_amount'] = $p;
        }
        

        $this->load->view('partner/show_partner_booking_summary', $data);
    }

    /**
     * @Desc: This function is used to download Active vendors list
     *      in Excel
     * params: void
     * @return: void
     * 
     */
    function download_sf_list_excel() {
        $where = array('service_centres.active' => '1', 'service_centres.on_off' => '1',is_CP => '0');
        $select = "service_centres.id,service_centres.district,service_centres.state,service_centres.pincode,service_centres.appliances,service_centres.non_working_days,GROUP_CONCAT(sub_service_center_details.district) as upcountry_districts";
        //$vendor = $this->vendor_model->getVendorDetails($select, $where, 'state');
             $vendor =  $this->reusable_model->get_search_result_data("service_centres",$select,$where,array("sub_service_center_details"=>"sub_service_center_details.service_center_id = service_centres.id"),
                NULL,array("service_centres.state"=>"ASC"),NULL,array("sub_service_center_details"=>"left"),array("service_centres.id"));
        foreach ($vendor as $key => $value){
            $rm_details = $this->vendor_model->get_rm_sf_relation_by_sf_id($value['id']);
            if(!empty($rm_details)){
                $vendor[$key]['rm_email'] = $rm_details[0]['official_email'];
                $vendor[$key]['rm_phone'] = $rm_details[0]['phone'];
            } else {
                $vendor[$key]['rm_email'] = "";
                $vendor[$key]['rm_phone'] = "";
            }
        }
        
        $template = 'Consolidated_SF_List_Template.xlsx';
        //set absolute path to directory with template files
        $templateDir = __DIR__ . "/../excel-templates/";
        //set config for report
        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );
        //load template
        if(ob_get_length() > 0) {
            ob_end_clean();
        }
        $R = new PHPReport($config);

        $R->load(array(
            'id' => 'vendor',
            'repeat' => TRUE,
            'data' => $vendor
        ));

        $output_file_dir = TMP_FOLDER;
        $output_file = "SF_List_" . date('y-m-d');
        $output_file_name = $output_file . ".xlsx";
        $output_file_excel = $output_file_dir . $output_file_name;
        $R->render('excel', $output_file_excel);

        //Downloading File
        if (file_exists($output_file_excel)) {

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header("Content-Disposition: attachment; filename=\"$output_file_name\"");
            readfile($output_file_excel);
            exit;
        }
    }

    function get_serviceability_by_pincode() {
        log_message('info', __FUNCTION__ . " Function Start ");
        $this->miscelleneous->create_serviceability_report_csv($this->input->post());
        echo json_encode(array("response" => "success", "path" => base_url() . "file_process/downloadFile/serviceability_report.csv"));
    }

    /**
     * @desc This is used to get prepaid amount for requested partner 
     * @param int $partner_id
     * @return Array
     */
    function get_prepaid_amount($partner_id) {
        log_message("info", __METHOD__ . " Partner Id " . $partner_id);
        if ($this->session->userdata('is_prepaid') == 1) {
            $p_details = $this->miscelleneous->get_partner_prepaid_amount($partner_id);

            if ($p_details['is_notification']) {

                $d['prepaid_amount'] = '<strong class="blink" style="color:red;">' . $p_details['prepaid_amount'] . '</strong> ';
            } else {
                $d['prepaid_amount'] = '<strong style="color:green;">' . $p_details['prepaid_amount'] . '</strong>';
            }

            $d['prepaid_msg'] = $p_details['prepaid_msg'];

            $userSession = array('status' => $p_details['active'], "message" => $p_details['prepaid_msg']);
            $this->session->set_userdata($userSession);
            return $d;
        } else if ($this->session->userdata('is_prepaid') == 0) {
            $this->check_postpaid_partner_active($partner_id);
            return array();
        } else {
            $userSession = array('status' => true);
            $this->session->set_userdata($userSession);
            return array();
        }
        
    }
    /**
     * @desc This function is used to check active/De-activate for Postpaid Partner
     * @param String $partner_id
     * @return boolean
     */
    function check_postpaid_partner_active($partner_id){
        log_message('info', __METHOD__. " POSPAID PARTNER ". $partner_id);
        $partner_details = $this->partner_model->getpartner_details("partners.id, public_name, "
                . "postpaid_credit_period, is_active, postpaid_notification_limit, postpaid_grace_period, "
                . "invoice_email_to,invoice_email_cc, is_prepaid", array('partners.id' => $partner_id));
        $postpaid = $this->invoice_lib->get_postpaid_partner_outstanding($partner_details[0]);

        $userSession = array('status' => $postpaid['active'], "message" => $postpaid['notification_msg']);
        log_message("info", __METHOD__. " POSTPAID partner is active ". $postpaid['active']);
        $this->session->set_userdata($userSession);
        return true;
    }

    public function get_contact_us_page() {
        $partner_id = $this->session->userdata('partner_id');
        //$data['account_manager_details'] = $this->miscelleneous->get_am_data($partner_id);
        $data['account_manager_details'] = $this->partner_model->getpartner_data("employee.*,group_concat(distinct agent_filters.state separator ', ') as state", 
                array('partners.id' => $partner_id),"",1,1,1,"employee.id");
        $state_arr = explode(", ", $data['account_manager_details'][0]['state']);
        $arr_state = $this->booking_model->get_state();
        if(count($state_arr) === count($arr_state)) {
            $data['account_manager_details'][0]['state'] = "Pan India";
        }
        $data['rm_details'] = $this->employee_model->get_employee_by_group(array('groups' => 'regionalmanager', 'active' => 1));
        $data['holidayList'] = $this->employee_model->get_holiday_list();
        //$this->load->view('partner/header');
        $this->miscelleneous->load_partner_nav_header();
        $this->load->view('partner/contact_us', $data);
        $this->load->view('partner/partner_footer');
    }

    /*
     * This function load the view for bracket allocation
     */

    function bracket_allocation() {
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/bracket_allocation');
    }

    /*
     * This function return data to show bracket allocation table view 
     */

    function get_bracket_allocation_data() {
        $receieved_Data = $this->input->post();
        $limitArray = array('length' => $receieved_Data['length'], 'start' => $receieved_Data['start']);
        $joinDataArray = array("partners" => "partners.id=is_bracket_over_brand_partner.partner_id");
        $result = $this->reusable_model->get_search_result_data("is_bracket_over_brand_partner", "partners.public_name,brand,CASE WHEN is_bracket=0 THEN 'No' ELSE 'YES' END AS is_bracket,partner_id", NULL, $joinDataArray, $limitArray, array("partners.public_name" => "ASC"), NULL, NULL);
        for ($i = 0; $i < count($result); $i++) {
            $index = $receieved_Data['start'] + ($i + 1);
            $link = "<button type='button' class='btn btn-info' data-toggle='modal' data-target='#myModal' onclick=createStandEditForm('" . $result[$i]['brand'] . "','" . $result[$i]['partner_id'] . "','" . $result[$i]['is_bracket'] . "') style='margin:0px 10px;'>Edit</button>";
            unset($result[$i]['partner_id']);
            $tempArray = array_values($result[$i]);
            array_push($tempArray, $link);
            array_unshift($tempArray, $index);
            $finalArray[] = $tempArray;
        }
        $data['draw'] = $receieved_Data['draw'];
        $data['recordsTotal'] = $this->reusable_model->get_search_result_count("is_bracket_over_brand_partner", "brand,partners.public_name,is_bracket", NULL, $joinDataArray, NULL, array("brand" => "ASC"), NULL, NULL);
        $data['recordsFiltered'] = $this->reusable_model->get_search_result_count("is_bracket_over_brand_partner", "brand,partners.public_name,is_bracket", NULL, $joinDataArray, NULL, array("brand" => "ASC"), NULL, NULL);
        $data['data'] = $finalArray;
        echo json_encode($data);
    }

    /*
     * This functrion return the data needed to create Insert bracket allocation form
     */

    function get_bracket_allocation_form_data() {
        $data['partner'] = $this->booking_model->get_advance_search_result_data("partners", "id,public_name", NULL, NULL, NULL, array('public_name' => 'ASC'));
        $data['brand'] = $this->booking_model->get_advance_search_result_data("appliance_brands", "DISTINCT(brand_name)", NULL, NULL, NULL, array('brand_name' => 'ASC'));
        echo json_encode($data);
    }

    /*
     * This function update or insert the data for bracket allocation
     */

    function process_bracket_combination() {
        $data = $this->input->post();
        if ($data['add_delete'] == 'add') {
            unset($data['add_delete']);
            $affectedRows = $this->reusable_model->insert_into_table('is_bracket_over_brand_partner', $data);
        } else {
            $is_bracket = $data['is_bracket'];
            unset($data['add_delete']);
            unset($data['is_bracket']);
            $affectedRows = $this->reusable_model->update_table('is_bracket_over_brand_partner', array('is_bracket' => $is_bracket), $data);
        }
        $msg = "Somethong Went wrong, Please try again";
        if ($affectedRows > 0) {
            $msg = 'Successfully Done';
        }
        $this->session->set_userdata(array('bracket_msg' => $msg));
        redirect(base_url() . "employee/partner/bracket_allocation");
    }

    function process_partner_document_form() { 
        $return_data = array();
        $partner_id = $this->input->post("partner_id");
        //Processing Pan File
        if (($_FILES['pan_file']['error'] != 4) && !empty($_FILES['pan_file']['tmp_name'])) {
            $tmpFile = $_FILES['pan_file']['tmp_name'];
            $pan_file = "Partner-" . preg_replace('/\s+/', '', strtolower($this->input->post('partner_public_name'))) . '-PAN' . "." . explode(".", $_FILES['pan_file']['name'])[1];
            move_uploaded_file($tmpFile, TMP_FOLDER . $pan_file);

            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "vendor-partner-docs/" . $pan_file;
            $this->s3->putObjectFile(TMP_FOLDER . $pan_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            $return_data['partner']['pan_file'] = $pan_file;

            $attachment_pan = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $pan_file;
            unlink(TMP_FOLDER . $pan_file);

            //Logging success for file uppload
            log_message('info', __FUNCTION__ . ' PAN FILE is being uploaded sucessfully.');
        }

        //Processing Registration File
        if (($_FILES['registration_file']['error'] != 4) && !empty($_FILES['registration_file']['tmp_name'])) {
            $tmpFile = $_FILES['registration_file']['tmp_name'];
            $registration_file = "Partner-" . preg_replace('/\s+/', '', strtolower($this->input->post('partner_public_name'))) . '-Registration' . "." . explode(".", $_FILES['registration_file']['name'])[1];
            move_uploaded_file($tmpFile, TMP_FOLDER . $registration_file);

            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "vendor-partner-docs/" . $registration_file;
            $this->s3->putObjectFile(TMP_FOLDER . $registration_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            $return_data['partner']['registration_file'] = $registration_file;

            $attachment_registration_file = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $registration_file;
             unlink(TMP_FOLDER . $registration_file);

            //Logging success for file uppload
            log_message('info', __FUNCTION__ . ' Registration FILE is being uploaded sucessfully.');
        }
        //Processing TIN File
        if (($_FILES['tin_file']['error'] != 4) && !empty($_FILES['tin_file']['tmp_name'])) {
            $tmpFile = $_FILES['tin_file']['tmp_name'];
            $tin_file = "Partner-" . preg_replace('/\s+/', '', strtolower($this->input->post('partner_public_name'))) . '-TIN' . "." . explode(".", $_FILES['tin_file']['name'])[1];
            move_uploaded_file($tmpFile, TMP_FOLDER . $tin_file);

            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "vendor-partner-docs/" . $tin_file;
            $this->s3->putObjectFile(TMP_FOLDER . $tin_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            $return_data['partner']['tin_file'] = $tin_file;

            $attachment_tin_file = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $tin_file;
            unlink(TMP_FOLDER . $tin_file);

            //Logging success for file uppload
            log_message('info', __FUNCTION__ . ' TIN FILE is being uploaded sucessfully.');
        }
        //Processing CST File
        if (($_FILES['cst_file']['error'] != 4) && !empty($_FILES['cst_file']['tmp_name'])) {
            $tmpFile = $_FILES['cst_file']['tmp_name'];
            $cst_file = "Partner-" . preg_replace('/\s+/', '', strtolower($this->input->post('partner_public_name'))) . '-CST' . "." . explode(".", $_FILES['cst_file']['name'])[1];
            move_uploaded_file($tmpFile, TMP_FOLDER . $cst_file);

            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "vendor-partner-docs/" . $cst_file;
            $this->s3->putObjectFile(TMP_FOLDER . $cst_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            $return_data['partner']['cst_file'] = $cst_file;

            $attachment_cst_file = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $cst_file;
            unlink(TMP_FOLDER . $cst_file);

            //Logging success for file uppload
            log_message('info', __FUNCTION__ . ' CST FILE is being uploaded sucessfully.');
        }
        //Processing Service Tax File
        if (($_FILES['service_tax_file']['error'] != 4) && !empty($_FILES['service_tax_file']['tmp_name'])) {
            $tmpFile = $_FILES['service_tax_file']['tmp_name'];
            $service_tax_file = "Partner-" . preg_replace('/\s+/', '', strtolower($this->input->post('partner_public_name'))) . '-CST' . "." . explode(".", $_FILES['service_tax_file']['name'])[1];
            move_uploaded_file($tmpFile, TMP_FOLDER . $service_tax_file);

            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "vendor-partner-docs/" . $service_tax_file;
            $this->s3->putObjectFile(TMP_FOLDER . $service_tax_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            $return_data['partner']['service_tax_file'] = $registration_file;

            $attachment_service_tax_file = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $service_tax_file;
            unlink(TMP_FOLDER . $service_tax_file);

            //Logging success for file uppload
            log_message('info', __FUNCTION__ . ' Service Tax FILE is being uploaded sucessfully.');
        }
         //Processing GST Number File
        if (($_FILES['gst_number_file']['error'] != 4) && !empty($_FILES['gst_number_file']['tmp_name'])) {
            $tmpFile = $_FILES['gst_number_file']['tmp_name'];
            $gst_number_file = "Partner-" . $this->input->post('public_name') . '-GST_Number' . "." . explode(".", $_FILES['gst_number_file']['name'])[1];
                   
            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "vendor-partner-docs/" . $gst_number_file;
            $this->s3->putObjectFile($tmpFile, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            $return_data['partner']['gst_number_file'] = $gst_number_file;

            $attachment_gst_number_file = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $gst_number_file;

            //Logging success for file uppload
            log_message('info', __FUNCTION__ . ' GST Number FILE is being uploaded sucessfully.');
        }
        $return_data['partner']['gst_number'] = trim($this->input->post("gst_number"));
        if($this->input->post("gst_number")){
            $return_data['partner']['gst_type'] = trim($this->input->post("gst_type"));
            $return_data['partner']['gst_status'] = trim($this->input->post("gst_status"));
        }
        else{
            $return_data['partner']['gst_type'] = "";
            $return_data['partner']['gst_status'] = "";
        }
        $return_data['partner']['pan'] = trim($this->input->post("pan"));
        $return_data['partner']['registration_no'] = trim($this->input->post("registration_no"));
        $return_data['partner']['tin'] = trim($this->input->post("tin"));
        $return_data['partner']['cst_no'] = trim($this->input->post("cst_no"));
        $return_data['partner']['service_tax'] = trim($this->input->post("service_tax"));
        $return_data['partner']['update_date'] = date("Y-m-d h:i:s");
        $return_data['partner']['agent_id'] = $this->session->userdata('id');
        if ($return_data) {
            $affected_rows = $this->reusable_model->update_table("partners", $return_data['partner'], array("id" => $partner_id));
        }
        if ($affected_rows > 0) {
            $msg = "Partner Documents has been updated successfully";
            $this->session->set_userdata('success', $msg);
        }
        redirect(base_url() . 'employee/partner/editpartner/' . $partner_id);
    }

    function process_partner_operation_region_form() {
        $partner_operation_state = $this->input->post('select_state');
        $partner_id = $this->input->post('partner_id');
        if (!empty($partner_operation_state)) {
            $all_flag = FALSE;
            foreach ($partner_operation_state as $key => $value) {
                foreach ($value as $val) {
                    //Checking if ALL state has been selected
                    if ($val == 'all') {
                        $all_states = $this->vendor_model->get_allstates();
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
            $this->partner_model->delete_partner_operation_region($partner_id);
            //Inserting Array in batch in partner operation region
            $operation_insert_flag = $this->partner_model->insert_batch_partner_operation_region($data_final);
            if ($operation_insert_flag) {
                $msg = "Partner Operation Regions has been updated successfully";
                $this->session->set_userdata('success', $msg);
                //Loggin Success
                log_message('info', 'Parnter Operation Region has been added sucessfully for partner ' . print_r($partner_id));
            }
        } else {
            //Echoing message in Log file
            log_message('error', __FUNCTION__ . ' No Input provided for Partner Operation Region Relation  ');
        }

        redirect(base_url() . 'employee/partner/editpartner/' . $partner_id);
    }

    function process_partner_contracts() {
        $partner_id = $this->input->post('partner_id');
        //$p = $this->reusable_model->get_search_result_data("partners", "public_name, account_manager_id", array('id' => $partner_id), NULL, NULL, NULL, NULL, NULL);
        $p = $this->partner_model->getpartner_data("partners.public_name, group_concat(distinct agent_filters.agent_id) as account_manager_id", 
                array('partners.id' => $partner_id),"",0,0,1,"partners.id");
        $partnerName = $p[0]['public_name'];
        $start_date_array = $this->input->post('agreement_start_date');
        $end_date_array = $this->input->post('agreement_end_date');
        $contract_type_array = $this->input->post('contract_type');
        $contract_description_array = $this->input->post('contract_description');
        $finalInsertArray = array();
        foreach ($contract_type_array as $index => $contract_type) {
            if (($_FILES['contract_file']['error'][$index] != 4) && !empty($_FILES['contract_file']['tmp_name'][$index])) {
                $tmpFile = $_FILES['contract_file']['tmp_name'][$index];
                $contract_file = "Partner-" . $partnerName . '-Contract_' . $contract_type . "_" . date('Y-m-d') . "." . explode(".", $_FILES['contract_file']['name'][$index])[1];
                move_uploaded_file($tmpFile, TMP_FOLDER . $contract_file);
                //Upload files to AWS
                $bucket = BITBUCKET_DIRECTORY;
                $directory_xls = "vendor-partner-docs/" . $contract_file;
                $this->s3->putObjectFile(TMP_FOLDER . $contract_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                $attachment_contract = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $contract_file;
                unlink(TMP_FOLDER . $contract_file);
                //Logging success for file uppload
                log_message('info', __FUNCTION__ . ' CONTRACT FILE is being uploaded sucessfully.');
                $insertArray = array("entity_id" => $partner_id, "entity_type" => "partner", "collateral_id" => $contract_type,
                    "document_description" => $contract_description_array[$index], 'file' => $contract_file, "start_date" => $start_date_array[$index], 'end_date' => $end_date_array[$index]);
                $finalInsertArray[] = $insertArray;
                $contract_type_tag = $this->reusable_model->execute_custom_select_query("SELECT `collateral_tag`, collateral_type FROM `collateral_type` WHERE `id`='".$contract_type[$index]."'");
                $emailArray = array("Contract_Type"=>$contract_type_tag[0]['collateral_type'], "Partnership_Start_Date"=>$start_date_array[$index], "Partnership_End_Date"=>$end_date_array[$index], "Contract_Description" => $contract_description_array[$index]);
            }
        }
        if ($finalInsertArray) {
            $affacted_rows = $this->reusable_model->insert_batch("collateral", $finalInsertArray);
            if ($affacted_rows > 0) {
                $msg = "Partner Contracts has been Updated Successfully";
                $this->session->set_userdata('success', $msg);
                //Send mail
                $html = "<p>Following Partner has been Updated : </p>";
                foreach ($emailArray as $key => $value) {
                    $html .= "<li><b>" . $key . '</b> =>';
                    $html .= " " . $value . '</li>';
                }
                //$logged_user_name = $this->employee_model->getemployeefromid($p[0]['account_manager_id']);
                if (!empty($p[0]['account_manager_id'])) {
                    $logged_user_name = $this->employee_model->getemployeeMailFromID($p[0]['account_manager_id']);
                }
                
                if(!empty($logged_user_name)){
                    $to = $logged_user_name[0]['official_email']. ",". $this->session->userdata('official_email');
                    $subject = "Partner Updated By " . $this->session->userdata('emp_name');
                    $cc = ANUJ_EMAIL_ID;
                    $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $html, "",PARTNER_DETAILS_UPDATED);
                    
                }
            }
        }
        redirect(base_url() . 'employee/partner/editpartner/' . $partner_id);
    }
    /**
     * @desc This function is used to map brand to partner (Appliance Wise)
     */
    function process_partner_brand_mapping() {
        $partner_id = $this->input->post("partner_id");
        $services = $this->vendor_model->selectservice();
        //Partner id should not be empty
        if (!empty($partner_id)) {
            $formdata = $this->input->post();
            // index brand of array hould not be empty
            if (!empty($formdata['brand'])) {
                $data = array();
                $newaddedbrand=array();
                foreach ($services as $value) {
                    // checking, array has service id as a key 
                    if (array_key_exists($value->id, $formdata['brand'])) {
                        $where = array("partner_id" => $partner_id, "service_id" => $value->id);
                        $existingdata = $this->partner_model->get_partner_specific_details($where, "brand");
                        $existing = array_column($existingdata, 'brand');
                        $activewhere=array("partner_id" => $partner_id, "service_id" => $value->id,"active"=>1);
                        $active_existing_data=$this->partner_model->get_partner_specific_details($activewhere, "brand");
                        $active_existing=array_column($active_existing_data, 'brand');
                       
                        //checking all brand from form has in the db , if not the push in the new array else activate brand
                        foreach ($formdata['brand'][$value->id] as $brand) {
                            if (!empty($existingdata)) {
                                if (in_array($brand, $existing)) {
                                    $this->partner_model->update_partner_appliance_details(array("partner_id" => $partner_id, "brand" => $brand,
                                        "service_id" => $value->id), array("active" => 1));
                                    } else {
                                    array_push($data, array("partner_id" => $partner_id, "active" => 1, "service_id" => $value->id,
                                        "brand" => $brand, "create_date" => date("Y-m-d H:i:s")));
                                   
                                }
                            } else {
                                array_push($data, array("partner_id" => $partner_id, "active" => 1, "service_id" => $value->id,
                                    "brand" => $brand, "create_date" => date("Y-m-d H:i:s")));
                                
                            }
                            //find only newly added brand 
                            if (!empty($active_existing_data)) {
                                if (!in_array($brand, $active_existing)) {
                                    array_push($newaddedbrand, array("partner_id" => $partner_id,"service_id" => $value->id,
                                        "brand" => $brand,));
                                    } 
                            } else {
                                array_push($newaddedbrand, array("partner_id" => $partner_id,"service_id" => $value->id,
                                    "brand" => $brand,));
                                
                            }
                            
                        }
                        //checking existing brand exist in the form brand array
                        foreach ($existing as $value2) {

                            if (!in_array($value2, $formdata['brand'][$value->id])) {

                                $this->partner_model->update_partner_appliance_details(array("partner_id" => $partner_id,
                                    "service_id" => $value->id, "brand" => $value2), array("active" => 0));
                            }
                        }
                    } else {
                        $this->partner_model->update_partner_appliance_details(array("partner_id" => $partner_id, "service_id" => $value->id), array("active" => 0));
                    }
                }
                if (!empty($data)) {
                    // Inert Partner Appliance Details
                    $this->partner_model->insert_batch_partner_brand_relation($data);
                    foreach($data as $b_value){
                        $is_exits = $this->booking_model->check_brand_exists($b_value['service_id'], trim($b_value["brand"]));
                        if (!$is_exits) {
                            // Add new Brand in appliance brand table
                           $this->booking_model->addNewApplianceBrand($b_value['service_id'], trim($b_value["brand"]));
                           
                        }
                    }
                }
            } else {
                //De- Activate this partner in partner_appliace_description
                $this->partner_model->update_partner_appliance_details(array("partner_id" => $partner_id), array("active" => 0));
            }
            $eData['partner_id'] = $partner_id;
            $eData['data'] = $this->input->post();
            $eData['services'] = $services;
            $eData['newappliancebrand']=$newaddedbrand;
            $sendUrl = base_url().'employee/do_background_process/send_email_to_sf_on_partner_brand_updation';
            $this->asynchronous_lib->do_background_process($sendUrl, $eData);
            $msg = "Partner Brand has been Updated Successfully";
            $this->session->set_userdata('success', $msg);
            redirect(base_url() . 'employee/partner/editpartner/' . $partner_id);
        }
    }
    
    /**
     * @desc: this function is used to reset the partner login details
     * @param: void
     * @return: void
     */
    function reset_partner_password(){
        $this->checkUserSession();
        //$this->load->view('partner/header');
        $this->miscelleneous->load_partner_nav_header();
        $this->load->view('partner/reset_partner_passsword');
        $this->load->view('partner/partner_footer');
    }
    
    /**
     * @desc: This function is used to get partner details from Ajax call
     * @params: void
     * @return: string
     */
    function get_partner_list(){
       //  $is_wh = $this->input->post('is_wh');
          $is_wh = 0;

        if(!empty($is_wh)){
            $where = array('is_active'=>1,'(is_wh = 1 OR is_micro_wh = 1)' => NULL);
        }else{
            $where = array('is_active'=>1);
        }
        $partner_list = $this->partner_model->get_all_partner($where);
        $option = '<option selected="" disabled="">Select Partner</option>';
        if(!empty($this->input->post('is_all_partner'))){
          $option .= '<option value="all">All</option>';  
        }

        foreach ($partner_list as $value) {
            $option .= "<option value='" . $value['id'] . "'";
            if(count($partner_list) == 1){
                $option .= " selected> ";
            }else{
               $option .= "> "; 
            }
            
            $option .= $value['public_name'] . "</option>";
        }
        echo $option;
    }



        function get_partner_list_warehouse(){

        $where = array('warehouse_details.entity_type'=>_247AROUND_PARTNER_STRING);
 
        $partner_list = $this->partner_model->get_all_partner_warehouse($where);
        $option = '<option selected="" disabled="">Select Partner Warehouse Hub</option>';

        foreach ($partner_list as $value) {
            $option .= "<option value='" . $value['id'] . "'";
            $option .= " > ";
            $option .= $value['name']. ' ( '. $value['city'] . ' ) ' . "</option>";  //address
        }
        echo $option;
    }



    
     /**
     * @desc: This function is used to upload the challan file when partner shipped spare parts
     * @params: void
     * @return: $res
     */
    function upload_challan_file($id) {
        if (empty($_FILES['challan_file']['error']) && $_FILES['challan_file']['name']) {
            $challan_file = "partner_challan_file_" . $this->input->post('booking_id'). "_".$id."_" . str_replace(" ", "_", $_FILES['challan_file']['name']);
            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "vendor-partner-docs/" . $challan_file;
            $this->s3->putObjectFile($_FILES['challan_file']['tmp_name'], $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            
            $res = $challan_file;
        } else {
            $res = FALSE;
        }
        
        return $res;
    }
    /*
     * This function is used for Partner Brand Collateral section to get brand category capacity collateral type for apartner
     */
    function get_service_details(){
        $service_id = $this->input->post('service_id');
        $partner_id = $this->input->post('partner_id');
        $data['brand'] = $this->reusable_model->get_search_result_data("service_centre_charges","DISTINCT brand",array('service_id'=>$service_id,'partner_id'=>$partner_id),NULL,NULL,NULL,NULL,NULL,array());
        $data['category'] = $this->reusable_model->get_search_result_data("service_centre_charges","DISTINCT category",array('service_id'=>$service_id,'partner_id'=>$partner_id),NULL,NULL,NULL,NULL,NULL,array());
        $data['capacity'] = $this->reusable_model->get_search_result_data("service_centre_charges","DISTINCT capacity",array('service_id'=>$service_id,'partner_id'=>$partner_id),NULL,NULL,NULL,NULL,NULL,array());
        $data['model'] = $this->reusable_model->get_search_result_data("appliance_model_details","DISTINCT model_number as model",array('service_id'=>$service_id,'entity_id'=>$partner_id),NULL,NULL,NULL,NULL,NULL,array());
        $data['collateral_type'] = $this->reusable_model->get_search_result_data("collateral_type","id,concat(collateral_type, '_', document_type) as collateral_type",array('collateral_tag'=>LEARNING_DOCUMENT),NULL,NULL,NULL,NULL,NULL,array());
        echo json_encode($data);
    }
    /*
     * This function is used to get service for a partner in brand collateral
     * called by ajax
     */
    function get_partner_services(){
        $partner_id = $this->input->post('partner_id');
        $data = $this->reusable_model->get_search_result_data("service_centre_charges","DiSTINCT service_centre_charges.service_id,services.services",array('service_centre_charges.partner_id'=>$partner_id),
                array("services"=>"service_centre_charges.service_id = services.id"),NULL,NULL,NULL,NULL,array());
        echo json_encode($data);
    }
    /*
     * This function is used to validate brand collateral file
     */
    function brand_collaterals_file_validations($file,$formatType){
        $type = $file['type'];
        if (strpos($type, $formatType) === false) {
            $this->session->set_userdata('error', "Please Choose Correct File For Collateral Type");
             return false;
        }
        else if (strpos($type, 'video') !== false) {
//            if (strpos($type, 'mp4') === false) {
//                $this->session->set_userdata('error', "Only Mp4 is allowed for video type file");
//                return false;
//            }
            if($file['size']>"104857600" ){
                $this->session->set_userdata('error', "Video File Size Must be less then 100MB");
                return false;
            }
        }
        else if (strpos($type, 'audio') !== false) {
//            if (strpos($type, 'mp3') === false) {
//                $this->session->set_userdata('error', "Only Mp3 is allowed for audio type file");
//                return false;
//            }
            if($file['size']>"52428800‬" ){
                $this->session->set_userdata('error', "Audio File Size Must be less then 50MB");
                return false;
            }
        }
       else if (strpos($type, 'pdf') !== false) {
            if($file['size']>"52428800‬" ){
                $this->session->set_userdata('error', "Pdf File Size Must be less then 50MB");
                return false;
            }
        }
        else{
            $this->session->set_userdata('error', "File Type Only Should be audio,video and pdf");
            return false;
        }
        return true;
    }
    /*
     * This function is used to process brand collaterals uploading form.
     * This function creates every posible combination of service,category,brand,capacity on the basis of input against the input file and save in database
     */
    function process_partner_learning_collaterals(){
        $partner = $this->input->post('partner_id');
        if(!empty($this->input->post('l_c_model')) && !empty($this->input->post('l_c_capacity'))){
            $this->session->set_userdata('error', 'Either Select Capacity OR Select Model, Please Do not select Both Together');
            redirect(base_url() . 'employee/partner/editpartner/' . $partner);
            return FALSE;
        }
        $validation = TRUE;
        $file = 0;
        $contract_typeTemp = $this->input->post('l_c_type');
        $tArray = explode("_",$contract_typeTemp);
        $contract_type = $tArray[0];
        if(!$this->input->post('l_c_url')){
             $validation =  $this->brand_collaterals_file_validations($_FILES['l_c_file'],$tArray[2]);
             $file = 1;
        }
        else{
            $contract_file = $this->input->post('l_c_url');
        }
        if($validation){
            if($file){
                if (($_FILES['l_c_file']['error'] != 4) && !empty($_FILES['l_c_file']['tmp_name'])) {
                        $tmpFile = $_FILES['l_c_file']['tmp_name'];
                        $contract_file = "Partner-" . 'Brand_Collateral_' . $contract_type . "_" . date('Y-m-d') . "." .$_FILES['l_c_file']['name'];
                        move_uploaded_file($tmpFile, TMP_FOLDER . $contract_file);
                        //Upload files to AWS
                        $bucket = BITBUCKET_DIRECTORY;
                        $directory_xls = "vendor-partner-docs/" . $contract_file;
                        $this->s3->putObjectFile(TMP_FOLDER . $contract_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                        unlink(TMP_FOLDER . $contract_file);
                        //Logging success for file uppload
                        log_message('info', __FUNCTION__ . ' Learning Collateral FILE is being uploaded sucessfully.');
                }
            }
            $l_c_capacity =array();
            $l_c_model = array();
            $l_c_brands = $this->input->post('l_c_brands');
            $l_c_category = $this->input->post('l_c_category');
            $appliance_id = $this->input->post('l_c_service');
            $request_type = $this->input->post('l_c_request_type');
            $description = '';
            if($this->input->post('l_c_capacity') && !empty($this->input->post('l_c_capacity'))){
              $l_c_capacity = $this->input->post('l_c_capacity');  
              $is_model = 0;
            }
            if($this->input->post('l_c_model') && !empty($this->input->post('l_c_model'))){
              $l_c_capacity = $this->input->post('l_c_model');  
              $is_model =  1;
            }
             if($this->input->post('description') && $this->input->post('description') !=''){
                 $description = $this->input->post('description');
             }
            foreach($l_c_category as $category){
                if(!empty($this->input->post('l_c_brands'))){
                    foreach($l_c_brands as $brands){
                        foreach($request_type as $requestType){
                        if(!empty($l_c_capacity)){
                            foreach($l_c_capacity as $capacity){
                                $temp['brand'] = $brands;
                                $temp['is_file'] = $file;
                                $temp['collateral_id'] = $contract_type;
                                $temp['category'] = $category;
                                $temp['appliance_id'] = $appliance_id;
                                $temp['entity_id'] = $partner;
                                $temp['entity_type'] = 'partner';
                                $temp['start_date'] = date('Y-m-d');
                                if($is_model){
                                    $temp['model'] = $capacity;
                                }
                                else{
                                    $temp['capacity'] = $capacity;
                                }
                                $temp['document_description'] = $description;
                                $temp['file'] = $contract_file;
                                $temp['request_type'] = $requestType;
                                $data[] = $temp;
                            }
                        }
                        else{
                                $temp['brand'] = $brands;
                                $temp['collateral_id'] = $contract_type;
                                $temp['category'] = $category;
                                $temp['appliance_id'] = $appliance_id;
                                $temp['entity_id'] = $partner;
                                $temp['entity_type'] = 'partner';
                                $temp['start_date'] = date('Y-m-d');
                                $temp['capacity'] = NULL;
                                $temp['document_description'] = $description;
                                $temp['file'] = $contract_file;
                                $temp['request_type'] = $requestType;
                                $data[] = $temp;
                        }
                    }
                    }
                }
            }
            $id = $this->reusable_model->insert_batch('collateral',$data);
            if($id){
                $msg =  "Learning Collateral has been uploded successfully ";
            }
            else{
                $msg =  "Something went Wrong Please try again or contact to admin";
            }
                $this->session->set_userdata('success', $msg);
            }
        redirect(base_url() . 'employee/partner/editpartner/' . $partner);
    }
    
    /**
     * @desc: This function is used to download the partner details 
     * @params: void
     * @return: string
     */
    function download_partner_summary_details(){
       
       
        $partner_details = array();
        $select = "partners.id,public_name,company_type,primary_contact_name,"
                . "primary_contact_email,primary_contact_phone_1,"
                . "owner_name,owner_email,owner_phone_1,gst_number,pan,"
                . "customer_care_contact as customer_care_num,address,GROUP_CONCAT(DISTINCT employee.full_name) as am_name,GROUP_CONCAT(DISTINCT employee.official_email) as am_email, agreement_start_date, agreement_end_date,"
                . "upcountry_rate, CASE WHEN is_upcountry = 1 THEN 'Yes' ELSE 'No' END as upcountry, upcountry_max_distance_threshold, CASE WHEN upcountry_approval = 1 THEN 'Yes' ELSE 'No' END as upcountry_approval,"
                . "upcountry_approval_email, invoice_email_to, invoice_email_cc, invoice_email_bcc,"
                . "CASE WHEN is_prepaid = 0 THEN 'PostPaid' WHEN is_prepaid = 1 THEN 'PrePaid' ELSE ' ' END as is_prepaid, prepaid_amount_limit, prepaid_notification_amount,"
                . "postpaid_credit_period, postpaid_notification_limit, postpaid_grace_period";
        $where = array('partners.is_active' => 1);
        $group_by = "partners.id";

        //$partner_details['excel_data_line_item'] = $this->partner_model->getpartner_details($select,$where,"",TRUE);//,TRUE
        $partner_details['excel_data_line_item'] = $this->partner_model->getpartner_data($select, $where, "",1,1,1,$group_by);
        $service_brands=array();
        //add appliance of partner
        foreach ($partner_details['excel_data_line_item'] as $key => $value) {
            //Getting Appliances and Brands details for partner
            $service_brands = $this->partner_model->get_service_brands_for_partner($value['id']);
             if(!empty($service_brands))
            {
               $str = "";
               foreach($service_brands as $key1=>$value1)
               {
                    $str .= '  ' . $value1['services'] . '  - ' . $value1['brand'] . ' ,'; 
               }
                $partner_details['excel_data_line_item'][$key]['applliance']=rtrim($str, ",");
            }
            else
            {
                $partner_details['excel_data_line_item'][$key]['applliance']="";
            }
                                 
        }
        $template = 'partner_summary_details.xlsx';
        $output_file = "partner_summary_details". date('d_M_Y_H_i_s');
        $partner_details['excel_data'] = array();
        $generated_file = $this->miscelleneous->generate_excel_data($template,$output_file,$partner_details);
        
        if (file_exists($generated_file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($generated_file) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($generated_file));
            readfile($generated_file);
            
            $res1 = 0;
            system(" chmod 777 " . $generated_file , $res1);
            unlink($generated_file);
        }else{
            echo "Please Try Again!!! Error in generating file";
        }
    }
    /**
     * @desc: This function is used to download SF declaration who don't have GST number hen Partner update spare parts
     * @params: String $sf_id
     * @return: void
     */
    
    function download_sf_declaration($sf_id) {
        log_message("info", __METHOD__." SF Id ". $sf_id);
        $this->checkUserSession();
        $pdf_details = $this->miscelleneous->generate_sf_declaration($sf_id);
        
        if($pdf_details['status']){
            if(!empty($pdf_details['file_name'])){
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename(TMP_FOLDER . $pdf_details['file_name']) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize(TMP_FOLDER . $pdf_details['file_name']));
                readfile(TMP_FOLDER . $pdf_details['file_name']);

                unlink(TMP_FOLDER . $pdf_details['file_name']);
            }
            log_message("info", __METHOD__." file details  ". print_r($pdf_details,true));
        }else{
            log_message("info", __METHOD__." file details  ". print_r($pdf_details,true));
            echo $pdf_details['message'];
        }
    }
    
    
    /**
     * @desc: This function is used to get the details from the email_attachment_parser 
     * @params: void
     * @return: JSON
     */
    function get_partner_file_details(){
        $this->partner_id = trim($this->input->post('partner_id'));



         
        
        if(!empty($this->partner_id)){
            $data = $this->around_scheduler_model->get_data_for_parsing_email_attachments(array('partner_id' => $this->partner_id));
            if(!empty($data)){
                $res['msg'] = 'success';
                $res['data'] = $data[0];
            }else{
                $res['msg'] = 'failed';
                $res['data'] = 'No Data Found';
            }
        }else{
            $res['msg'] = 'failed';
            $res['data'] = 'No Data Found';
        }
        
        echo json_encode($res);
    }
    
    function get_contracts(){
        $id = $this->session->userdata('partner_id');
        $data['contracts'] = $this->reusable_model->get_search_result_data("collateral", 'collateral.*,collateral_type.*',
                    array("entity_id" => $id, "entity_type" => "partner",'collateral_type.collateral_tag'=>'Contract'), array("collateral_type" => "collateral_type.id=collateral.collateral_id"), 
                    NULL, NULL, NULL,NULL);
        $this->miscelleneous->load_partner_nav_header();
        $this->load->view('partner/get_contracts',$data);
        $this->load->view('partner/partner_footer');
    }   
    
    /**
     * @desc: This function is used to show the payment details page to partner
     * @params: void
     * @return: void
     */
    function payment_details(){
        $this->checkUserSession();
        //$this->load->view('partner/header');
        $this->miscelleneous->load_partner_nav_header();
        $this->load->view('paytm_gateway/payment_details');
        $this->load->view('partner/partner_footer');
    }
    
    function download_partner_pending_bookings($partnerID,$status){ 
        ob_start();
        $report = $this->partner_model->get_partners_pending_bookings($partnerID,0,1,$status);
        $newCSVFileName = $status."_booking_" . date('j-M-Y-H-i-s') . ".csv";
        $csv = TMP_FOLDER . $newCSVFileName;
        $delimiter = ",";
        $newline = "\r\n";
        $new_report = $this->dbutil->csv_from_result($report, $delimiter, $newline);
        write_file($csv, $new_report);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($csv) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($csv));
        readfile($csv);
        exec("rm -rf " . escapeshellarg($csv));
        unlink($csv);
     }
    
    
    /**
     *  @desc : This function is used to show the current stock of partner inventory in 247around warehouse.
     *  @param : void
     *  @return : void
     */
    function inventory_stock_list(){
        $this->checkUserSession();
        $this->miscelleneous->load_partner_nav_header();
        //$this->load->view('partner/header');
        $this->load->view('partner/inventory_stock_list');
        $this->load->view('partner/partner_footer');
    }
    
     /**
     *  @desc : This function is used to show the current alternate spare parts stock of partner inventory in 247around warehouse.
     *  @param : void
     *  @param : void
     *  @param : void
     *  @return : void
     */
    function alternate_inventory_stock_list($inventory_id, $service_id) {
        
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');
        $where = array(
            'inventory_master_list.entity_id' => $partner_id,
            'inventory_master_list.entity_type' => _247AROUND_PARTNER_STRING,
            'inventory_master_list.inventory_id' => $inventory_id,
            'inventory_master_list.service_id' => $service_id,
        );

        $inventory_list = $this->inventory_model->get_inventory_master_list_data('inventory_master_list.part_name', $where, array());
        $data = array();
        $data['inventory_id'] = $inventory_id;
        $data['service_id'] = $service_id;
        $data['partner_id'] = $partner_id;
         if (!empty($inventory_list)) {
            $data['part_name'] = $inventory_list[0]['part_name'];
        }        
        $this->miscelleneous->load_partner_nav_header();  
        $this->load->view('partner/alternate_inventory_stock_list',$data);
        $this->load->view('partner/partner_footer');
    }
    
    
    /**
     *  @desc : This function is used to show alternate parts inventory  list 
     *  @param : void
     *  @return : void
     */
    function alternate_parts_list() {
        $this->checkUserSession();
        $data = array();
        $data['partner_id'] = $this->session->userdata('partner_id');
        $this->miscelleneous->load_partner_nav_header();  
        $this->load->view("partner/alternate_parts_list",$data);
        $this->load->view('partner/partner_footer');
    }
    
    function get_pending_part_on_sf(){
         log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id'));
        $this->checkUserSession();
        $agent_id = $this->session->userdata('agent_id');
        if($this->session->userdata('is_filter_applicable') == 1){
            $data['states'] = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state_code.state) as state",array("agent_filters.agent_id"=>$agent_id),array("agent_filters"=>"agent_filters.state=state_code.state"),NULL,array('state'=>'ASC'),NULL,array("agent_filters"=>"left"),array());
        }else{
            $data['states'] = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state) as state",NULL,NULL,NULL,array('state'=>'ASC'),NULL,NULL,array());
        }
            $this->miscelleneous->load_partner_nav_header();
            $this->load->view('partner/sf_needs_to_send_parts', $data);
            $this->load->view('partner/partner_footer');
    }
    function get_reports(){
        $this->checkUserSession();
        $partnerID = $this->session->userdata('partner_id');
        $agent_id = $this->session->userdata('agent_id');
        if($this->session->userdata('is_filter_applicable') == 1){
            $data['states'] = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state_code.state) as state",array("agent_filters.agent_id"=>$agent_id),array("agent_filters"=>"agent_filters.state=state_code.state"),NULL,array('state'=>'ASC'),NULL,array("agent_filters"=>"left"),array());
        }
        else{
            $data['states'] = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state) as state",NULL,NULL,NULL,array('state'=>'ASC'),NULL,NULL,array());
        }
        $data['services'] = $this->booking_model->selectservice();
        $data['summaryReportData'] = $this->reusable_model->get_search_result_data("reports_log","filters,date(create_date) as create_date,url",array("entity_type"=>"partner","entity_id"=>$partnerID),NULL,array("length"=>50,"start"=>""),
                array('id'=>'DESC'),NULL,NULL,array());
        $this->miscelleneous->load_partner_nav_header();
        $this->load->view('partner/report',$data);
        $this->load->view('partner/partner_footer');
    }
    private function create_custom_summary_report($partnerID,$postArray){
        if(!empty($postArray['Date_Range'])) {
            $dateArray  = explode(" - ",$postArray['Date_Range']);
            $start = date('Y-m-d',strtotime($dateArray[0]));
            $end = date('Y-m-d',strtotime($dateArray[1]));
            
            $where[] = "(date(booking_details.create_date)>='".$start."' AND date(booking_details.create_date)<='".$end."')";
        }
        
        $status = $postArray['Status'];
        if($postArray['State']){
            $state = explode(",",$postArray['State']);
        }
        else{
            $state =array('All');
        }
        
        if(!empty($postArray['Completion_Date_Range'])) {
            $completionDateArray = explode(" - ",$postArray['Completion_Date_Range']);
            $completion_start_date = date('Y-m-d',strtotime($completionDateArray[0]));
            $completion_end_date = date('Y-m-d',strtotime($completionDateArray[1]));
            
            $where[] = "(date(booking_details.service_center_closed_date)>='".$completion_start_date."' AND date(booking_details.service_center_closed_date)<='".$completion_end_date."')";
        }
        
        $newCSVFileName = "Booking_summary_" . date('Y-m-d').($partnerID+211).rand(10,100000000). ".csv";
        $csv = TMP_FOLDER . $newCSVFileName;
        
        if($status != 'All'){
            if($status == _247AROUND_PENDING){
                $where[] = "booking_details.current_status NOT IN ('Cancelled','Completed')";
                $where[] = "booking_details.service_center_closed_date IS NULL";
            }
            else if($status == _247AROUND_COMPLETED){
                    $where[] = "!(booking_details.current_status = 'Cancelled' OR booking_details.internal_status = 'InProcess_Cancelled')";
                    $where[] = "booking_details.service_center_closed_date IS NOT NULL";
            }
            else{
                $where[] = "(booking_details.current_status = 'Cancelled' OR booking_details.internal_status = 'InProcess_Cancelled')";
                $where[] = "booking_details.service_center_closed_date IS NOT NULL";
            }
            }
            if(!in_array('All',$state)){
                $where[] = "booking_details.state IN ('".implode("','",$state)."')";
            }
           log_message('info', __FUNCTION__ . "Where ".print_r($where,true));
           
           if(!empty($this->session->userdata('service_center_id'))) {
              $where[] = "booking_details.assigned_vendor_id = ". $this->session->userdata('service_center_id');
           }
        $report =  $this->partner_model->get_partner_leads_csv_for_summary_email($partnerID,0,implode(' AND ',$where));
        $delimiter = ",";
        $newline = "\r\n";
        $new_report = $this->dbutil->csv_from_result($report, $delimiter, $newline);
        write_file($csv, $new_report);
        return $newCSVFileName;
    }
    /*
     * This function is use to create and  save partner's custom summary report 
     */
    function create_and_save_partner_report($partnerID){
            log_message('info', __FUNCTION__ . "Function Start For ".print_r($this->input->post(),true)." Partner ID : ".$partnerID);
            $postArray = $this->input->post();
            //Create Summary Report
            $newCSVFileName = $this->create_custom_summary_report($partnerID,$postArray);
             //Save File on AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "summary-excels/" . $newCSVFileName;
            $is_upload = $this->s3->putObjectFile(realpath(TMP_FOLDER . $newCSVFileName), $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            unlink(TMP_FOLDER . $newCSVFileName);
            if($is_upload == 1){
                //Save File log in report log table
                if(!empty($partnerID)) {
                    $data['entity_type'] = _247AROUND_PARTNER_STRING;
                } else {
                    $data['entity_type'] = _247AROUND_SF_STRING;
                }
                if(!empty($partnerID)) {
                    $data['entity_id'] = $partnerID;
                } else {
                    $data['entity_id'] = $this->session->userdata('service_center_id');
                }
                
                $data['report_type'] = "partner_custom_summary_report";
                $data['filters'] = json_encode($postArray);
                $data['url'] =$directory_xls;
                $data['agent_id'] =$this->session->userdata('agent_id');
                $is_save = $this->reusable_model->insert_into_table("reports_log",$data);
                if($is_save){
                   $src=  base_url()."employee/partner/download_custom_summary_report/".$directory_xls;
                   echo  json_encode(array("response"=>"SUCCESS","url"=>$src));
                }
                else{
                    echo  json_encode(array("response"=>"FAILURE","url"=>$directory_xls));
                }
            }
    }
    function download_upcountry_report($isAdmin=0){
        if($isAdmin == 0) {
            log_message('info', __FUNCTION__ . ' Function Start For Partner '.$this->session->userdata('partner_id'));
            $this->checkUserSession();
        }
        else
        {
            $this->checkEmployeeUserSession();
        }
        $upcountryCsv= "Upcountry_Report" . date('j-M-Y-H-i-s') . ".csv";
        $csv = TMP_FOLDER . $upcountryCsv;
        $report = $this->upcountry_model->get_upcountry_non_upcountry_district();
        $delimiter = ",";
        $newline = "\r\n";
        $new_report = $this->dbutil->csv_from_result($report, $delimiter, $newline);
        log_message('info', __FUNCTION__ . ' => Rendered CSV');
        write_file($csv, $new_report);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($csv) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($csv));
        readfile($csv);
        exec("rm -rf " . escapeshellarg($csv));
        log_message('info', __FUNCTION__ . ' Function End');
        //unlink($csv);
    }
    function download_waiting_defective_parts(){
         log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id'));
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');
        $where = array(
            "spare_parts_details.defective_part_required" => 1,
            "approved_defective_parts_by_admin" => 1,
            "spare_parts_details.defective_return_to_entity_id" => $partner_id,
            "spare_parts_details.defective_return_to_entity_type" => _247AROUND_PARTNER_STRING,
            "status IN ('" . DEFECTIVE_PARTS_SHIPPED . "')  " => NULL
        );
        $select = "CONCAT( '', GROUP_CONCAT((defective_part_shipped ) ) , '' ) as defective_part_shipped, i.part_number as part_code, "
                . " spare_parts_details.booking_id, users.name, courier_name_by_sf, awb_by_sf, spare_parts_details.sf_challan_number, spare_parts_details.partner_challan_number, "
                . "defective_part_shipped_date,remarks_defective_part_by_sf";
        $group_by = "spare_parts_details.booking_id";
        $order_by = "spare_parts_details.defective_part_shipped_date DESC";
        $newCSVFileName = "Waiting_Spare_Parts_".date("Y-m-d").".csv";
        $csv = TMP_FOLDER . $newCSVFileName;
        $report = $this->service_centers_model->get_spare_parts_booking($where, $select, $group_by, $order_by,FALSE,FALSE,0,1);
        $delimiter = ",";
        $newline = "\r\n";
        $new_report = $this->dbutil->csv_from_result($report, $delimiter, $newline);
        log_message('info', __FUNCTION__ . ' => Rendered CSV');
        write_file($csv, $new_report);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($csv) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($csv));
        readfile($csv);
        exec("rm -rf " . escapeshellarg($csv));
        unlink($csv);
    }
    function download_waiting_upcountry_bookings(){
        ob_start();
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id'));
        $this->checkUserSession();
        $data = $this->upcountry_model->get_waiting_for_approval_upcountry_charges($this->session->userdata('partner_id'));
        $headings = array("Booking ID","Call Type","Customer Name","Customer Contact Number","Appliance","Brand","Category","Capacity","Address","City","Pincode","State","Upcountry Distance","Upcountry Charges");
        foreach($data as $upcountryBookings){
            $upcountryCharges = round($upcountryBookings['upcountry_distance'] * $upcountryBookings['partner_upcountry_rate'], 2);
            $tempArray = array_values($upcountryBookings);
            array_pop($tempArray);
            array_push($tempArray,$upcountryCharges);
            $CSVData[]  = $tempArray;
        }
        $this->miscelleneous->downloadCSV($CSVData, $headings, "Waiting_Upcountry_Bookings_".date("Y-m-d"));
    }
    
    function download_spare_part_shipped_by_partner($isAdmin=0,$partner_post=0){
        ini_set('memory_limit', '-1');
        ob_start();
        $where = '1';
        if($isAdmin == 0 && $partner_post==0) {
             log_message('info', __FUNCTION__ . ' Function Start For Partner '.$this->session->userdata('partner_id'));
             $this->checkUserSession();
             $partner_id = $this->session->userdata('partner_id');
             $where = "booking_details.partner_id = '" . $partner_id . "' ";
         }
         else
         {
             $this->checkEmployeeUserSession();
             $partner_id = $partner_post;
             $where = "booking_details.partner_id = '" . $partner_id . "' ";
            
         }
         
          $where .= " AND status != 'Cancelled' AND parts_shipped IS NOT NULL  ";
          
          $this->gotToDownloadCSV($where);
         
    }
    
    function download_all_spare_parts($isAdmin = 0, $partner_post = 0) {
           
        ob_start();
        $where = '1';
        if ($isAdmin == 0 && $partner_post == 0) {
            log_message('info', __FUNCTION__ . ' Function Start For Partner ' . $this->session->userdata('partner_id'));
            $this->checkUserSession();
            $partner_id = $this->session->userdata('partner_id');
            $where = "booking_details.partner_id = '" . $partner_id . "' ";
        } else {
            $this->checkEmployeeUserSession();
            $partner_id = $partner_post;
            $where = "booking_details.partner_id = '" . $partner_id . "' ";
        }

        //$where .= " AND status != 'Cancelled'";
                
        $this->gotToDownloadCSV($where);
    }

    function gotToDownloadCSV($where){
        ini_set('memory_limit', '-1');
        $CSVData = array();
                
        $data= $this->partner_model->get_spare_parts_booking_list($where, NULL, NULL, true);
        $headings = array("Booking ID",
            "Booking Create Date",
            "Initial Booking Date",
            "Current Booking Date",
            "Booking Completion Date",
            "Product",
            "Booking Request Type",
            "Part Warranty Status",
            "Requested On Partner/Warehouse",
            "Spare Status",
            "Booking Status Level 1",
            "Booking Status Level 2",
            "SF Name",
            "SF City",
            "SF State",
            "SF Remarks",
            "Requested Part Code",
            "Requested Part Name",
            "Requested Model Number",
            "Requested Quantity",
            "Requested Part Type",
            "Requested Part Date",
            "Date Of Purchase",
            "Parts Charge",
            "Dispatched Part Code (To SF)",
            "Dispatched Part Name (To SF)",
            "Dispatched Model Number (To SF)",
            "Dispatched Quantity (To SF)",
            "Dispatched Part Type (To SF)",
            "Dispatched Part Date (To SF)",
            "Dispatched Invoice Number (To SF)",
            "Dispatched Challan Number",
            "Dispatched AWB Number (To SF)",
            "Courier Name (Dispatched To SF)",
            "Courier Price (Dispatched To SF)",
            "Part Acknowledge Date By SF",
            "Remarks by Partner/Warehouse",
            "Defective Part Shipped By SF",
            "Defective Received Date By Partner/Warehouse",
            "Defective Part Shipped Date",
            "Defective Part Remarks by SF",
            "SF Challan Number",
            "SF AWB Number (Defective Shipped)",
            "Is Spare Auto Acknowledge By SF",
            "Consumption"
            );
        
        foreach($data as $sparePartBookings){
            $tempArray = array();            
            $tempArray[] = $sparePartBookings['booking_id'];
            $tempArray[] = ((!empty($sparePartBookings['create_date']))?date("d-m-Y",strtotime($sparePartBookings['create_date'])):'');
            $tempArray[] = ((!empty($sparePartBookings['initial_booking_date']))?date("d-m-Y",strtotime($sparePartBookings['initial_booking_date'])):'');
            $tempArray[] = ((!empty($sparePartBookings['booking_date']))?date("d-m-Y",strtotime($sparePartBookings['booking_date'])):'');
            $tempArray[] = ((!empty($sparePartBookings['service_center_closed_date']))?date("d-m-Y",strtotime($sparePartBookings['service_center_closed_date'])):'');
            $tempArray[] = $sparePartBookings['services'];
            $tempArray[] = $sparePartBookings['request_type'];
            $tempArray[] = (($sparePartBookings['part_warranty_status'] == 1)? "In- Warranty" :(($sparePartBookings['part_warranty_status'] == 2)? "Out of Warranty" : ""));
            $tempArray[] = (($sparePartBookings['is_micro_wh'] == 0)? "Partner" :(($sparePartBookings['is_micro_wh'] == 1)? "Micro Warehouse - " : "").$sparePartBookings['warehouse_name']);
            $tempArray[] = $sparePartBookings['status'];
            $tempArray[] = $sparePartBookings['partner_current_status'];     
            $tempArray[] = $sparePartBookings['partner_internal_status'];     
            $tempArray[] = $sparePartBookings['vendor_name'];            
            $tempArray[] = $sparePartBookings['sf_city'];              
            $tempArray[] = $sparePartBookings['sf_state'];
            $tempArray[] = $sparePartBookings['remarks_by_sc'];
            $tempArray[] = $sparePartBookings['part_number'];
            $tempArray[] = $sparePartBookings['part_name'];
            $tempArray[] = $sparePartBookings['model_number'];
            $tempArray[] = $sparePartBookings['quantity'];
            $tempArray[] = $sparePartBookings['type'];
            $tempArray[] = ((!empty($sparePartBookings['date_of_request']))?date("d-m-Y",strtotime($sparePartBookings['date_of_request'])):'');
            $tempArray[] = ((!empty($sparePartBookings['date_of_purchase']))?date("d-m-Y", strtotime($sparePartBookings['date_of_purchase'])):'');
            $tempArray[] = $sparePartBookings['challan_approx_value'];
            $tempArray[] = $sparePartBookings['shipped_part_number'];
            $tempArray[] = $sparePartBookings['shipped_part_name'];
            $tempArray[] = $sparePartBookings['model_number_shipped'];
            $tempArray[] = $sparePartBookings['shipped_quantity'];
            $tempArray[] = $sparePartBookings['shipped_part_type'];
            $tempArray[] = ((!empty($sparePartBookings['shipped_date']))?date("d-m-Y",strtotime($sparePartBookings['shipped_date'])):'');
            $tempArray[] = $sparePartBookings['purchase_invoice_id'];
            $tempArray[] = $sparePartBookings['partner_challan_number'];
            $tempArray[] = $sparePartBookings['awb_by_partner'];
            $tempArray[] = $sparePartBookings['courier_name_by_partner'];
            $tempArray[] = $sparePartBookings['courier_price_by_partner'];            
            $tempArray[] = ((!empty($sparePartBookings['acknowledge_date']))?date("d-m-Y",strtotime($sparePartBookings['acknowledge_date'])):'');            
            $tempArray[] = $sparePartBookings['remarks_by_partner'];
            $tempArray[] = $sparePartBookings['defective_part_shipped'];
            $tempArray[] = ((!empty($sparePartBookings['received_defective_part_date']))?date("d-m-Y",strtotime($sparePartBookings['received_defective_part_date'])):'');
            $tempArray[] = ((!empty($sparePartBookings['defective_part_shipped_date']))?date("d-m-Y",strtotime($sparePartBookings['defective_part_shipped_date'])):'');
            $tempArray[] = $sparePartBookings['remarks_defective_part_by_sf'];
            $tempArray[] = $sparePartBookings['sf_challan_number'];
            $tempArray[] = $sparePartBookings['awb_by_sf'];
            if($sparePartBookings['auto_acknowledeged']==1){
            $tempArray[] = "Yes";   
             }else{
            $tempArray[] = "No";   
             }
            
             if($sparePartBookings['consumption']==1){
            $tempArray[] = "Yes";   
             }else{
            $tempArray[] = "No";   
             }
             
            $CSVData[]  = $tempArray;            
        }  

        $this->miscelleneous->downloadCSV($CSVData, $headings, "Spare_Part_Shipped_By_Partner_".date("Y-m-d"));
    }
    
    function download_spare_part_shipped_by_partner_not_acknowledged(){
        ob_start();
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id'));
        $this->checkUserSession();
        $CSVData = array();
        $partner_id = $this->session->userdata('partner_id');
        $where = "spare_parts_details.partner_id = '" . $partner_id . "' AND status IN ('".Shipped."') ";
        $data= $this->partner_model->get_spare_parts_booking_list($where, NULL, NULL, true);
        $headings = array("Customer Name","Booking ID","Shipped Parts","Part Code","Courier Name","AWB","Challan","Shipped Date","Remarks");
        foreach($data as $sparePartBookings){
            $tempArray = array();
            $tempArray[] = $sparePartBookings['name'];
            $tempArray[] = $sparePartBookings['booking_id'];
            $tempArray[] = $sparePartBookings['parts_shipped'];
            $tempArray[] = $sparePartBookings['part_number'];
            $tempArray[] = $sparePartBookings['courier_name_by_partner'];
            $tempArray[] = $sparePartBookings['awb_by_partner'];
            $tempArray[] = $sparePartBookings['partner_challan_number'];
            $tempArray[] = $sparePartBookings['shipped_date'];
            $tempArray[] = $sparePartBookings['remarks_by_partner'];
            $CSVData[]  = $tempArray;
        }
        $this->miscelleneous->downloadCSV($CSVData, $headings, "Spare_Part_Shipped_By_Partner_".date("Y-m-d"));
    }
    function download_sf_needs_to_send_parts(){
        ob_start();
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id'));
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');
        $where = array(
            "spare_parts_details.defective_part_required" => 1,
            "spare_parts_details.partner_id" => $partner_id,
            "status IN ('" . DEFECTIVE_PARTS_PENDING . "', '".DEFECTIVE_PARTS_REJECTED."')  " => NULL
        );
        $select = "CONCAT( '', GROUP_CONCAT((parts_shipped ) ) , '' ) as defective_part_shipped, i.part_number as part_code, "
                . " spare_parts_details.booking_id, users.name,spare_parts_details.courier_name_by_partner,spare_parts_details.awb_by_partner, spare_parts_details.partner_challan_number, "
                . "DATEDIFF(CURDATE(),date(booking_details.service_center_closed_date)) as aging, service_centres.company_name,primary_contact_phone_1, service_centres.address,"
                . "service_centres.district, service_centres.pincode, service_centres.state";
        $group_by = "spare_parts_details.booking_id";
        $order_by = "spare_parts_details.defective_part_shipped_date DESC";
        $data = $this->service_centers_model->get_spare_parts_booking($where, $select, $group_by, $order_by);
        $headings = array("Parts","Part Code","Booking ID","Name","Courier","AWB","Challan","Aging", "SF Company Name","SF Contatct Number", "SF Address", "SF District", "SF Pincode", "SF State");
        foreach($data as $sparePartBookings){
            $tempArray = array_values($sparePartBookings);
            $CSVData[]  = $tempArray;
        }
        $this->miscelleneous->downloadCSV($CSVData, $headings, "Spare_Part_Shipped_By_Partner_".date("Y-m-d"));
    }
    function download_received_spare_by_partner(){
        ob_start();
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id'));
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');
        $where = "spare_parts_details.defective_return_to_entity_id = '" . $partner_id . "' "
                . " AND defective_return_to_entity_type = '"._247AROUND_PARTNER_STRING."' "
                . " AND approved_defective_parts_by_partner = '1' ";
        $data = $this->partner_model->get_spare_parts_booking_list($where, NULL,NULL, true);
        $headings = array("Name","Booking ID","Received Parts","Part Code","Received Date","AWB","Courier Name","Challan","SF Remarks");
        $CSVData = array();
        foreach($data as $sparePartBookings){
            $tempArray = array();
            $tempArray[] = $sparePartBookings['name'];
            $tempArray[] = $sparePartBookings['booking_id'];
            $tempArray[] = $sparePartBookings['defective_part_shipped'];
            $tempArray[] = $sparePartBookings['part_number'];
            $tempArray[] = $sparePartBookings['received_defective_part_date'];
            $tempArray[] = $sparePartBookings['awb_by_partner'];
            $tempArray[] = $sparePartBookings[' courier_name_by_partner'];
            $tempArray[] = $sparePartBookings[' partner_challan_number'];
            $tempArray[] = $sparePartBookings['remarks_defective_part_by_sf'];
            $CSVData[]  = $tempArray;
        }
                
        if(!empty($CSVData)){
            $this->miscelleneous->downloadCSV($CSVData, $headings, "Spare_Part_Received_By_Partner_".date("Y-m-d"));
        }
        
    }
    
    function ack_spare_send_by_wh(){
        $this->checkUserSession();
        $this->miscelleneous->load_partner_nav_header();
       // $this->load->view('partner/header');
        $this->load->view('partner/ack_spare_send_by_wh');
        $this->load->view('partner/partner_footer');
    }
    function download_custom_summary_report($folder,$file){
       $this->miscelleneous->download_csv_from_s3($folder,$file);
    }
    
    /**
     * @desc: This function is used to get service_id from Ajax call
     * @params: void
     * @return: string
     */
    function get_service_id(){
        $partner_id = $this->input->get('partner_id');
        if($partner_id){
            $appliance_list = $this->partner_model->get_service_brands_for_partner($partner_id);
            if($this->input->get('is_option_selected')){
                $option = '<option  selected="" disabled="">Select Appliance</option>';
            }else{
                $option = '';
            }

            foreach ($appliance_list as $value) {
                $option .= "<option value='" . $value['id'] . "'";
                $option .= " > ";
                $option .= $value['services'] . "</option>";
            }
            
            if($this->input->get('is_all_option')){
                $option .= '<option value="all" >All</option>';
            }
            echo $option;
        }else{
            echo FALSE;
        }
    }
    
    /**
     * @desc: This function is used to get service_id from Ajax call
     * @params: void
     * @return: string
     */
    function get_partner_specific_appliance() {
        $partner_id = $this->input->get('partner_id');

        if (!empty($this->input->get('is_not_all_services'))) {
            $is_all_option = false;
        } else {
            $is_all_option = true;
        }

        if ($partner_id) {
            $appliance_list = $this->partner_model->get_partner_specific_services($partner_id);
            if ($this->input->get('is_option_selected')) {
                $option = '<option  selected="" disabled="">Select Appliance</option>';
                if ($is_all_option == true) {
                    $option = $option . '<option id="allappliance" value="all" >All</option>';
                }
            } else {
                $option = '';
            }

            foreach ($appliance_list as $value) {
                $option .= "<option value='" . $value->id . "'";
                $option .= " > ";
                $option .= $value->services . "</option>";
            }
            echo $option;
        } else {
            echo FALSE;
        }
    }

    /**
     * @desc: This function is used to show the inventory details of the partner
     * @params: void
     * @return: void
     */
    function show_inventory_master_details(){
        $this->checkUserSession();
        $this->miscelleneous->load_partner_nav_header();
        $this->load->view('partner/partner_inventory_master_list');
        $this->load->view('partner/partner_footer');
    }
    
    /**
     * @desc: This function is used to show the inventory appliance model details of the partner
     * @params: void
     * @return: void
     */
    function show_appliance_model_list(){
        $this->checkUserSession();
        $this->miscelleneous->load_partner_nav_header();
        $this->load->view('partner/partner_appliance_model_details');
        $this->load->view('partner/partner_footer');
    }
    
    /**
     *  @desc : This function is used to show the view so that partner can tag spare invoice send by him
     *  @param : void
     *  @return :void
     */
    function tag_spare_invoice() {
        $this->checkUserSession();
        $this->miscelleneous->load_partner_nav_header();
        $partner_details = $this->partner_model->getpartner_details("partners.is_defective_part_return_wh", array('partners.id' => $this->session->userdata('partner_id')), "", "");
        $data['courier_details'] = $this->inventory_model->get_courier_services('*');
        if (!empty($partner_details)) {
            $data['is_defective_part_return_wh'] = $partner_details[0]['is_defective_part_return_wh'];
        }

        $this->load->view("partner/tag_spare_invoice_send_by_partner", $data);
        $this->load->view('partner/partner_footer');
    }

    function get_partner_roles($department){
       $data =  $this->reusable_model->get_search_result_data("entity_role","role,id",array('department'=>$department,"entity_type"=>"partner"),NULL,NULL,array('role'=>"ASC"),NULL,NULL,array());
       $option = "<option value='' disabled selected>Select Role</option>";
       foreach($data as $roles){
           $option = $option."<option value = '".$roles['id']."'>".$roles['role']."</option>";
       }
       echo $option;
    }
    
    function get_partner_roles_filters(){
       $data =  $this->reusable_model->get_search_result_data("entity_role","is_filter_applicable",array('id'=>$this->input->post('role')),NULL,
               NULL,array('role'=>"ASC"),NULL,NULL,array());
       echo  $data[0]['is_filter_applicable'];
    }
    
    /*
     * This function is used to add partner contact persons
     */
    function process_partner_contacts(){
        if($this->input->post('partner_id')){
            $checkbox_array = explode(",",$this->input->post('final_checkbox_value_holder')); 
            $partnerID = $this->input->post('partner_id'); 
            foreach($this->input->post('contact_person_email') as $index=>$contactEmails){
                $agent_id = NULL;
                $data['name'] = $loginData['agent_name']  =  $this->input->post('contact_person_name')[$index];
                $data['official_email'] = $loginData['email'] =  $contactEmails;
                $data['alternate_email'] = $this->input->post('contact_person_alt_email')[$index];
                $data['official_contact_number'] = $this->input->post('contact_person_contact')[$index];
                $data['alternate_contact_number'] = $this->input->post('contact_person_alt_contact')[$index];
                $data['permanent_address'] = $this->input->post('contact_person_address')[$index];
                $data['correspondence_address'] = $this->input->post('contact_person_c_address')[$index];
                $data['role'] = $this->input->post('contact_person_role')[$index];
                $data['entity_id'] = $loginData['entity_id'] = $stateData['entity_id'] = $partnerID;
                $data['entity_type'] = $loginData['entity'] = $stateData['entity_type'] = "partner";
                $data['agent_id'] = $this->session->userdata('id');
                $id = $this->reusable_model->insert_into_table("contact_person",$data);
                $loginData['contact_person_id'] = $stateData['contact_person_id'] = $id;
                if($this->input->post('temp_partner_type') == "NOT_OEM" && $this->input->post('temp_partner_source')== "autologin"){
                    $loginData['email_not_sent'] = "NOT";
                }
                // Create Login If Checkbox Checked
                if($checkbox_array[$index] == 'true'){
                        $password = mt_rand(100000, 999999);
                        $loginData['user_id'] = str_replace(" ","_",$data['name']."_".mt_rand(1,5));
                        $loginData['password'] = md5($password);
                        $loginData['clear_password'] = $password;
                        $loginData['active'] = 1;
                        $agent_id = $this->miscelleneous->create_entity_login($loginData);
                    }
                    if($agent_id){
                        // Map States in agent_filters table 
                        // If state is not selected then add all states
                        $stateString =  $this->input->post('states_value_holder')[$index];
                        if(!$stateString){
                            $states = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state) as state",NULL,NULL,NULL,array('state'=>'ASC'),NULL,NULL,array());
                            $all =1;
                        }
                        else{
                            $states = explode(",",$stateString);
                             $all =0; 
                        }
                        foreach ($states as $state){
                            $stateData['agent_id'] = $agent_id;
                            if($all ==  1){
                                $stateData['state'] = $state['state'];
                            }
                            else{
                                $stateData['state'] = $state;
                            }
                            $stateData['is_active'] = 1;
                            $finalStateData[] = $stateData; 
                        }
                        $this->reusable_model->insert_batch('agent_filters',$finalStateData);
                    }
            }
            if($id){
                $msg =  "Contact Persons has been Added successfully ";
            }
            else{
                $msg =  "Something went Wrong Please try again or contact to admin";
            }
        }
        else{
            $msg =  "Something went Wrong Please try again or contact to admin";
        }
       $this->session->set_userdata('success', $msg);
       if($this->session->userdata('partner_id')){
           redirect(base_url() . 'partner/contacts');
       }
       else{
            redirect(base_url() . 'employee/partner/editpartner/' . $partnerID);
       }
    }
    /*
     * This function is used to add account manager of Partner
     */
    function process_partner_am_mapping(){
        if($this->input->post('partner_id')){
            $partnerID = $this->input->post('partner_id');
            $get_partner_details = $this->partner_model->getpartner_data("company_name, public_name, partner_type", 
                    array('partners.id' => $partnerID),"",0,1);
            $company_name = $get_partner_details[0]['company_name'];
            $public_name = $get_partner_details[0]['public_name'];
            $partner_type = $get_partner_details[0]['partner_type'];
            $states_arr = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state) as state",NULL,NULL,NULL,array('state'=>'ASC'),NULL,NULL,array());
            foreach($states_arr as $value) {
                $states[] = $value['state'];
            }
            $id = $count = 0;
            foreach($this->input->post('am') as $index=>$am){
                $arr_states = $this->input->post('am_state')[$index];
                foreach($this->input->post('am_state')[$index] as $key=>$state){
                    if(strtolower($state) === 'all') {
                        $arr_states = $states;
                    }
                }
                foreach($arr_states as $key=>$state){
                    $data=array("entity_type" => _247AROUND_EMPLOYEE_STRING, "entity_id" => $partnerID, "state" => $state);//, "agent_id" => $am
                    $am_data = $this->partner_model->get_am_data("*", $data);
                    if(empty($am_data)) {
                        $data["agent_id"] = $am;
                        $id = $this->reusable_model->insert_into_table("agent_filters",$data);
                    } else {
                        ++$count;
                    }
                }
            }
            if($id){
                $msg =  "Account Manager has been Added successfully ";
                $this->session->set_userdata('success', $msg);
                
                $am_id='';
                // Send new brand am notification email to all employee
                $email_template = $this->booking_model->get_booking_email_template(NEW_PARTNER_AM_NOTIFICATION);

                if(!empty($email_template)){
                    $template = array(
                        'table_open' => '<table border="1" cellpadding="4" cellspacing="0">'
                    );
                    $this->table->set_template($template);
                    $this->table->set_heading(array('Company Name', 'Public Name', 'Partner Type', 'Account Manager'));
                    foreach($this->input->post('am') as $index=>$am){
                        if($am_id !== $am) {
                            $account_manager_name = $this->employee_model->getemployeefromid($am)[0]['full_name'];
                        }
                        foreach($this->input->post('am_state')[$index] as $key=>$state){
                            $state_str = ((strtolower($state) !== 'all') ? (" - ".$state) : ' - All States');
                            $this->table->add_row(array($company_name,$public_name, $partner_type, $account_manager_name.$state_str));
                        }
                        $am_id = $am;
                    }
                    $html_table = $this->table->generate();

                    $to = $email_template[1];//ALL_EMP_EMAIL//all-emp@247around.com;

                    $cc = $email_template[3];
                    $subject = vsprintf($email_template[4], array($public_name));
                    $message = vsprintf($email_template[0], array($html_table));
                    $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $message, "", NEW_PARTNER_AM_NOTIFICATION);
                }
            }
            else if($count > 0) {
                $msg =  "Account Manager already added to this state!!";
                $this->session->set_userdata('error', $msg);
            }
            else{
                $msg =  "Something went Wrong Please try again or contact to admin";
                $this->session->set_userdata('error', $msg);
            }
        }
        else{
            $msg =  "Something went Wrong Please try again or contact to admin!!";
            $this->session->set_userdata('error', $msg);
        }
        redirect(base_url() . 'employee/partner/editpartner/' . $partnerID);
    }
    /*
     * This function is used to edit account manager of Partner
     */
    function edit_partner_am_mapping(){
       if($this->input->post('partner_id')){
            $partnerID = $this->input->post('partner_id');
            $get_partner_details = $this->partner_model->getpartner_data("company_name, public_name, partner_type", 
                    array('partners.id' => $partnerID),"",0,1);
            $company_name = $get_partner_details[0]['company_name'];
            $public_name = $get_partner_details[0]['public_name'];
            $partner_type = $get_partner_details[0]['partner_type'];
            
            $data['state'] = $this->input->post('state1');
            $data['agent_id'] = $this->input->post('am1');
            $where = array("entity_type" => _247AROUND_EMPLOYEE_STRING, "entity_id" => $partnerID, 'state' => $data['state']);//, 'agent_id' => $data['agent_id']
                
            $am_data = $this->partner_model->get_am_data("*", $where);
            if(empty($am_data) || ($am_data[0]['agent_id'] !== $data['agent_id'])) {
                //$where = array('id' => $this->input->post('mapping_id'));
                if(strtolower($data['state']) !== 'all') {
                    $update_data = $this->reusable_model->update_table("agent_filters",$data,$where);
                }
                else {
                    $states_arr = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state) as state",NULL,NULL,NULL,array('state'=>'ASC'),NULL,NULL,array());
                    foreach($states_arr as $value) {
                        $states[] = $value['state'];
                    }
                    $count = 0;
                    foreach($states as $key=>$state){
                        $insert_data=array("entity_type" => _247AROUND_EMPLOYEE_STRING, "entity_id" => $partnerID, "state" => $state);
                        $am_data = $this->partner_model->get_am_data("*", $insert_data);
                        if(empty($am_data)) {
                            $insert_data["agent_id"]=$data['agent_id'];
                            $update_data = $this->reusable_model->insert_into_table("agent_filters",$insert_data);
                        } else {
                            ++$count;
                        }
                    }
                }
                if($update_data){
                    $msg =  "Account Manager has been updated successfully ";
                    $this->session->set_userdata('success', $msg);
                    
                    // Send updated brand am notification email to all employee
                    $email_template = $this->booking_model->get_booking_email_template(UPDATE_PARTNER_AM_NOTIFICATION);
                    
                    if(!empty($email_template)){
                        $template = array(
                            'table_open' => '<table border="1" cellpadding="4" cellspacing="0">'
                        );
                        $this->table->set_template($template);
                        $this->table->set_heading(array('Company Name', 'Public Name', 'Partner Type', 'Account Manager'));

                        $account_manager_name = $this->employee_model->getemployeefromid($data['agent_id'])[0]['full_name'];

                        $state_str = ((strtolower($data['state']) !== 'all') ? (" - ".$data['state']) : " - All States");
                        $this->table->add_row(array($company_name,$public_name, $partner_type, $account_manager_name.$state_str));

                        $html_table = $this->table->generate();

                        $to = $email_template[1];//ALL_EMP_EMAIL//all-emp@247around.com;

                        $cc = $email_template[3];
                        $subject = vsprintf($email_template[4], array($public_name));
                        $message = vsprintf($email_template[0], array($html_table));
                        $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $message, "", UPDATE_PARTNER_AM_NOTIFICATION);
                    }
                }
                else{
                    $msg =  "No update done";
                    $this->session->set_userdata('error', $msg);
                }
            } else {
                $msg =  "Account Manager already added to this state!!";
                $this->session->set_userdata('error', $msg);
            }
        }
        else{
            $msg =  "Something went Wrong Please try again or contact to admin!";
            $this->session->set_userdata('error', $msg);
        }
        redirect(base_url() . 'employee/partner/editpartner/' . $partnerID);
    }
    /*
     * This function is used to activate / deactivate account manager of Partner
     */
    function activate_deactivate_mapping($id,$action){
        if($id){
            $data['is_active'] = $action;
            $where = array('id' => $id);
            $affected_rows =  $this->reusable_model->update_table("agent_filters",$data,$where);
            if($affected_rows){
                $v = "Deactivated";
                if($action){
                    $v = "Activated";
                }
                echo "Account Manager has been $v";
            }
            else{
                echo "Something Went Wrong Please Try Again";
            }
        }
    }
    /*
     * This function is used to delete account manager of Partner
     */
    function delete_partner_am() {
        if($this->input->post('partner_id')){
            $partnerID = $this->input->post('partner_id');
            if(!empty($this->input->post('id'))) {
                $am_data = $this->partner_model->get_am_data("*", array("entity_type" => _247AROUND_EMPLOYEE_STRING, "entity_id" => $partnerID),"","",0,array('id' => $this->input->post('id')));
                if(!empty($am_data)) {
                    $sql = "DELETE FROM agent_filters WHERE entity_type='"._247AROUND_EMPLOYEE_STRING."' AND entity_id='".$partnerID."' AND id in ('".implode("','",$this->input->post('id'))."') ";
                    $affected_rows =  $this->reusable_model->execute_custom_insert_update_delete_query($sql);

                    if($affected_rows){
                        echo "Account Managers has been deleted successfully ";
                    }
                    else{
                        echo "No deletion done";
                    }
                }
            }
        }
        else{
            echo "Something went Wrong Please try again or contact to admin!";
        }
    }
    
    function process_booking_internal_conversation_email() {
        log_message('info', __FUNCTION__ . " Booking ID: " . $this->input->post('booking_id'));
        if ($this->session->userdata('partner_id')) {
            if ($this->input->post('booking_id')) {
                $to = explode(",", $this->input->post('to'));
                $join['entity_login_table'] = "entity_login_table.contact_person_id = contact_person.id";
                $from_email = $this->reusable_model->get_search_result_data("contact_person", "official_email", array("entity_login_table.agent_id" => $this->session->userdata('agent_id')), $join, NULL, NULL, NULL, NULL, array())[0]['official_email'];
                $cc = $this->input->post('cc') . "," . $from_email;
                $row_id = $this->miscelleneous->send_and_save_booking_internal_conversation_email("Partner", $this->input->post('booking_id'), implode(",", $to), $cc
                        , $from_email, $this->input->post('subject'), $this->input->post('msg'), $this->session->userdata('agent_id'), $this->session->userdata('partner_id'));
                if ($row_id) {
                    echo "Successfully Sent";
                } else {
                    echo "Please Try Again";
                }
            } else {
                echo "Please Try Again";
            }
        }
    }

    function get_partner_tollfree_numbers() {
        $data = $this->partner_model->get_tollfree_and_contact_persons();
        $this->miscelleneous->multi_array_sort_by_key($data, "name", "ASC");
        echo json_encode($data);
    }

      /**
     * @desc: This function is used to get display the warehouse information of a partner
     * @params: void
     * @return: warehouse details from table
     * 
     */
       function get_warehouse_details(){
        
        $id = $this->input->post('partner_id');
        $select = "warehouse_details.id as 'wh_id',warehouse_address_line1, warehouse_address_line2, warehouse_city, warehouse_region, warehouse_pincode, warehouse_state, name,contact_person.id as 'contact_person_id'";
        $where1 = array("warehouse_details.entity_id" => $id, "warehouse_details.entity_type" => _247AROUND_PARTNER_STRING);
        $data= $this->inventory_model->get_warehouse_details($select, $where1,false);
        echo json_encode($data);

        
    }
    /**
     * @desc: This function is used to insert new warehouse information in the table
     * @params: void
     * @return: prints message if data inserted correctly or not
     * 
     */
    public function process_add_warehouse_details() {
        log_message('info',__METHOD__.' add warehouse details');
        $this->form_validation->set_rules('warehouse_address_line1', 'warehouse_address_line1', 'required|trim');
        $this->form_validation->set_rules('warehouse_city','warehouse_city', 'required|trim');
        $this->form_validation->set_rules('warehouse_region', 'warehouse_region','required|trim');
        $this->form_validation->set_rules('warehouse_pincode', 'warehouse_pincode','required|trim');
        $this->form_validation->set_rules('warehouse_state', 'warehouse_state','required|trim');
        $this->form_validation->set_rules('contact_person_id', 'Contact Person','required|trim');
        $this->form_validation->set_rules('warehouse_state_mapping', 'Wareshoue State','required');

        if ($this->form_validation->run() == TRUE) {
            $wh_data = array(
                'warehouse_address_line1' => $this->input->post('warehouse_address_line1'),
                'warehouse_address_line2' => $this->input->post('warehouse_address_line2'),
                'warehouse_city' => $this->input->post('warehouse_city'),
                'warehouse_region' => $this->input->post('warehouse_region'),
                'warehouse_pincode' => $this->input->post('warehouse_pincode'),
                'warehouse_state' => $this->input->post('warehouse_state'),
                'entity_id' => $this->input->post('partner_id'),
                'entity_type' => _247AROUND_PARTNER_STRING,
                'create_date' => date('Y-m-d H:i:s')
               
            );
            
            if(in_array('All', $this->input->post('warehouse_state_mapping'))){
                $state = array_column($this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state) as state",NULL,NULL,NULL,array('state'=>'ASC'),NULL,NULL,array()), 'state');
            }else{
                $state = $this->input->post('warehouse_state_mapping');
            }
            
            $wh_contact_person_mapping_data['contact_person_id'] = $this->input->post('contact_person_id');
            $wh_state_mapping_data = $state;
            $status = $this->inventory_model->insert_warehouse_details($wh_data,$wh_contact_person_mapping_data,$wh_state_mapping_data);
            if (!empty($status)) {
                log_message("info", __METHOD__ . " Data Entered Successfully");
                $this->session->set_userdata('success', 'Data Entered Successfully');
                redirect(base_url() . 'employee/partner/editpartner/' . $this->input->post('partner_id'));
            } else {
                log_message("info", __METHOD__ . " Error in adding details");
                $this->session->set_userdata('failed', 'Data can not be inserted. Please Try Again...');
                redirect(base_url() . 'employee/partner/editpartner/' . $this->input->post('partner_id'));
            }
        }else{
            $this->session->set_userdata('error', 'Please Select All Field');
            redirect(base_url() . 'employee/partner/editpartner/' . $this->input->post('partner_id'));
        } 
    }  
    //update a single contact
    function edit_partner_contacts(){
       if($this->input->post('partner_id')){
            $partnerID = $this->input->post('partner_id');
            $pid = $this->input->post('contact_id');
            $agent_id = $this->input->post('agentid');
            $data['name'] = $loginData['agent_name']  =  $this->input->post('contact_person_name');
            $data['official_email'] = $loginData['email'] =  $this->input->post('contact_person_email');
            $data['alternate_email'] = $this->input->post('contact_person_alt_email');
            $data['official_contact_number'] = $this->input->post('contact_person_contact');
            $data['alternate_contact_number'] = $this->input->post('contact_person_alt_contact');
            $data['permanent_address'] = $this->input->post('contact_person_address');
            $data['correspondence_address'] = $this->input->post('contact_person_c_address');
            $data['role'] = $this->input->post('contact_person_role');
            $data['entity_id'] = $loginData['entity_id'] = $stateData['entity_id'] = $partnerID;
            $data['entity_type'] = $loginData['entity'] = $stateData['entity_type'] = "partner";
            $data['agent_id'] = $this->session->userdata('id');
            $where = array('id' =>$pid);
            $update_data1 = $this->reusable_model->update_table("contact_person",$data,$where);
            $loginData['contact_person_id'] = $stateData['contact_person_id'] = $pid;
            // Create Login If Checkbox Checked
            if($this->input->post('checkbox_value_holder') == 'true' && !$agent_id){
                    $password = mt_rand(100000, 999999);
                    $user_id = str_replace(" ","_",$data['name']."_".mt_rand(10, 99));
                    $loginData['user_id'] = preg_replace('/[^A-Za-z0-9-]/', '', $user_id);
                    $loginData['password'] = md5($password);
                    $loginData['clear_password'] = $password;
                    $loginData['active'] = 1;
                    $agent_id = $this->miscelleneous->create_entity_login($loginData);
             }
             else if($this->input->post('checkbox_value_holder') == 'false' && $agent_id){
                 $this->partner_model->activate_deactivate_login("0",NULL,$agent_id);
             }
             else if($this->input->post('checkbox_value_holder') == 'true'  && $agent_id){
                  $this->partner_model->activate_deactivate_login("1",NULL,$agent_id);
             }
                // If state is not selected then add all states
                if($agent_id){
                        $stateString =  $this->input->post('states_value_holder');
                        if(!$stateString){
                            $states = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state) as state",NULL,NULL,NULL,array('state'=>'ASC'),NULL,NULL,array());
                            $all =1;
                        }
                        else{
                            $states = explode(",",$stateString);
                             $all =0; 
                        }
                        foreach ($states as $state){
                            $stateData['agent_id'] = $agent_id;
                            if($all ==  1){
                                $stateData['state'] = $state['state'];
                            }
                            else{
                                $stateData['state'] = $state;
                            }
                            $stateData['is_active'] = 1;
                            $finalStateData[]= $stateData; 
                        }
                        $where= array('contact_person_id' =>$pid);
                        if($where)
                            $this->reusable_model->delete_from_table('agent_filters',$where);
                            $update_data2 = $this->reusable_model->insert_batch('agent_filters',$finalStateData);
                         }
            if($update_data1 || $update_data2){
                $msg =  "Contact Persons has been Updated successfully ";
            }
            else{
                $msg =  "No update done";
            }
        }
        else{
            $msg =  "Something went Wrong Please try again or contact to admin";
        }
        if($this->session->userdata('partner_id')){
            $this->session->set_userdata('success', $msg);
            redirect(base_url() . 'partner/contacts');
        }
        else{
            $this->session->set_userdata('success', $msg);
            redirect(base_url() . 'employee/partner/editpartner/' . $partnerID);
        }
    }
    
    
    /**
     * @desc: This Function is used to search the docket number
     * @param: void
     * @return : void
     */
    function search_docket_number() {
        $this->checkUserSession();
        $this->miscelleneous->load_partner_nav_header();
        $this->load->view('partner/search_docket_number');
        $this->load->view('partner/partner_footer');
    }
    function partner_dashboard() {
        $this->checkUserSession();
        $this->miscelleneous->load_partner_nav_header();
        $serviceWhere['isBookingActive'] =1;
        $services = $this->reusable_model->get_search_result_data("services","*",$serviceWhere,NULL,NULL,array("services"=>"ASC"),NULL,NULL,array());
         if($this->session->userdata('user_group') == PARTNER_CALL_CENTER_USER_GROUP){
            $data['escalation_reason'] = $this->vendor_model->getEscalationReason(array('entity' => 'partner', 'active' => '1'));
            $this->load->view('partner/partner_default_page_cc', $data);
        }
        else{
            $this->load->view('partner/partner_dashboard',array('services'=>$services));
        }
        $this->load->view('partner/partner_footer');
        if(!$this->session->userdata("login_by")){
            $this->load->view('employee/header/push_notification');
        }
    }
    
    
    /**
     * @desc: This Function is used to edit warehouse deatails
     * @param: void
     * @return : JSON
     */
    function edit_warehouse_details() {
        log_message('info', 'edit warehouse details updated data ' . print_r($_POST, true),true);
        $wh_id = $this->input->post('wh_id');
        
        if (!empty($wh_id)) {
            $res = array();
            $wh_data = array(
                'warehouse_address_line1' => $this->input->post('wh_address_line1'),
                'warehouse_address_line2' => $this->input->post('wh_address_line2'),
                'warehouse_city' => $this->input->post('wh_city'),
                'warehouse_region' => $this->input->post('wh_region'),
                'warehouse_pincode' => $this->input->post('wh_pincode'),
                'warehouse_state' => $this->input->post('wh_state')
            );

            $update_wh = $this->inventory_model->edit_warehouse_details(array('id' => $wh_id), $wh_data);

            $updated_contact_person_id = $this->input->post('wh_contact_person_id');
            $old__contact_person_id = $this->input->post('old_contact_person_id');

            //if contact person change then update the contact person mapping in the warehouse_contact_person_mapping table
            //here we assume that every wh have only one contact person
            //if there are more than two contact person for the same warehouse than please change this logic
            if ($updated_contact_person_id !== $old__contact_person_id) {
                $update_wh_contact_pesron_mapping = $this->inventory_model->update_warehouse_contact_person_mapping(array('warehouse_id' => $wh_id), array('contact_person_id' => $updated_contact_person_id));
                if ($update_wh_contact_pesron_mapping) {
                    $res['status'] = true;
                    $res['msg'] = 'Details Updated Successfully';
                } else {
                    $res['status'] = false;
                    $res['msg'] = 'Details not updated. Please Try Again...';
                }
            }



 
            if (!empty(array_intersect($this->input->post('wh_state_mapping'), explode(',', $this->input->post('old_mapped_state_data'))))) {
                $data['wh_id'] = $wh_id;
                $data['new_wh_state_mapping'] = $this->input->post('wh_state_mapping');
                $update_state_mapping = $this->inventory_model->update_wh_state_mapping_data($data);

                if ($update_state_mapping) {
                    $res['status'] = true;
                    $res['msg'] = 'Details Updated Successfully';
                } else {
                    $res['status'] = true;
                    $res['msg'] = 'State Mapping Not Updated . Please try again...';
                }
            }else{

                $res['status'] = false;
                $res['msg'] = 'Details not updated. problem in selecting states';

            }

            if(!empty($res)){
                $res = $res;
            }else if ($update_wh) {
                $res['status'] = true;
                $res['msg'] = 'Details Updated Successfully';
            } else {
                $res['status'] = false;
                $res['msg'] = 'Details not updated. Please Try Again...';
            }
        } else {
            $res['status'] = false;
            $res['msg'] = 'Warehouse Id can not be empty';
        }

        echo json_encode($res);
    }

    function get_warehouse_state_mapping(){
        $wh_id = $this->input->post('wh_id');
        if(!empty(trim($wh_id))){
            $wh_state_mapping_datails = $this->reusable_model->get_search_query('warehouse_state_relationship','state',array('warehouse_state_relationship.warehouse_id' => $wh_id),NULL,NULL,array('state'=>'ASC'),NULL,NULL)->result_array();
            if(!empty($wh_state_mapping_datails)){
                $res['status'] = TRUE;
                $res['msg'] = array_map(function($val){ return strtoupper($val);}, array_column($wh_state_mapping_datails, 'state'));
            }else{
                $res['status'] = FALSE;
                $res['msg'] = 'No Data Found';
            }
            
        }else{
            $res['status'] = FALSE;
            $res['msg'] = 'Warehouse ID can not be empty';
        }
        
        echo json_encode($res);
    }
    function download_real_time_summary_report($partnerID){
        ini_set('memory_limit', '-1');
        $newCSVFileName = "Booking_summary_" . date('j-M-Y-H-i-s') . ".csv";
        $csv = TMP_FOLDER . $newCSVFileName;
        $report = $this->partner_model->get_partner_leads_csv_for_summary_email($partnerID,0);
        $delimiter = ",";
        $newline = "\r\n";
        $new_report = $this->dbutil->csv_from_result($report, $delimiter, $newline);
        log_message('info', __FUNCTION__ . ' => Rendered CSV');
        write_file($csv, $new_report);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($csv) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($csv));
        readfile($csv);
        exec("rm -rf " . escapeshellarg($csv));
         unlink($csv);
    }
      function checked_complete_review_booking() {
        $requested_bookings = $this->input->post('approved_booking');
        if($requested_bookings){
            $where['is_in_process'] = 0;
            $whereIN['booking_id'] = $requested_bookings; 
            $tempArray = $this->reusable_model->get_search_result_data("booking_details","booking_id",$where,NULL,NULL,NULL,$whereIN,NULL,array());
            foreach($tempArray as $values){
                $approved_booking[] = $values['booking_id'];
            }
            $inProcessBookings = array_diff($requested_bookings,$approved_booking);
            $this->session->set_flashdata('inProcessBookings', $inProcessBookings);
            $url = base_url() . "employee/do_background_process/complete_booking";
            if (!empty($approved_booking)) {
                //$this->booking_model->mark_booking_in_process($approved_booking);
                $data['booking_id'] = $approved_booking;
                $data['agent_id'] = $this->session->userdata('agent_id');
                $data['agent_name'] = $this->session->userdata('partner_name');
                $data['partner_id'] = $this->input->post('partner_id');
                $data['approved_by'] = $this->input->post('approved_by'); 
                $this->asynchronous_lib->do_background_process($url, $data);
                $this->push_notification_lib->send_booking_completion_notification_to_partner($approved_booking);
            } else {
                //Logging
                log_message('info', __FUNCTION__ . ' Approved Booking Empty from Post');
            }
        }
       redirect(base_url() . 'partner/home'); 
    }
    function reject_booking_from_review(){
        if($this->input->post('booking_id')){
        $postArray = $this->input->post();
        $where['is_in_process'] = 0;
        $whereIN['booking_id'] = $postArray['booking_id']; 
        $tempArray = $this->reusable_model->get_search_result_data("booking_details","booking_id",$where,NULL,NULL,NULL,$whereIN,NULL,array());
        if(!empty($tempArray)){
            //$this->booking_model->mark_booking_in_process(array($postArray['booking_id']));
            echo "Booking Updated Successfully";
            $postArray = $this->input->post();
            $this->miscelleneous->reject_booking_from_review($postArray);
        }
        else{
            echo "Someone Else is Updating the booking , Please check updated booking and try again";
        }
    }
    }
    function partner_review_bookings($offset = 0, $all = 0) {
        $this->checkUserSession();
        $agent_id = $this->session->userdata('agent_id');
        if($this->session->userdata('is_filter_applicable') == 1){
            $data['states'] = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state_code.state) as state",array("agent_filters.agent_id"=>$agent_id),array("agent_filters"=>"agent_filters.state=state_code.state"),NULL,array('state'=>'ASC'),NULL,array("agent_filters"=>"left"),array());
        }
        else{
            $data['states'] = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state_code.state) as state",NULL,NULL,NULL,array('state'=>'ASC'),NULL,NULL,array());
        }
       
        $data['is_ajax'] = $this->input->post('is_ajax');
        
        if(empty($this->input->post('is_ajax'))){
            $this->miscelleneous->load_partner_nav_header();
            $this->load->view('partner/get_waiting_to_review',$data);
            $this->load->view('partner/partner_footer');
        }else{
            $this->load->view('partner/get_waiting_to_review',$data);
        }
    }
    
    function download_partner_review_bookings($partnerID){
        ob_start();
        $finalArray = array();
        $data = $this->miscelleneous->get_review_bookings_for_partner($partnerID);
        foreach($data as $key => $values){
            $values['Booking_ID'] = $key;
            unset($values['booking_jobcard_filename']);
            unset($values['amount_due']);
            unset($values['partner_id']);
            ksort($values);
            $finalArray[] = $values;
        }
        if(!empty($finalArray)){
            $headings = array_keys($finalArray[0]);
            $this->miscelleneous->downloadCSV(array_values($finalArray), $headings, "Review_bookings");
        }
    }
    function get_pending_bookings(){
        $this->checkUserSession();
          $columnMappingArray = array("column_1"=>"booking_details.booking_id","column_3"=>"appliance_brand","column_4"=>"booking_details.partner_internal_status","column_7"=>"booking_details.city",
                "column_8"=>"booking_details.state","column_9"=>"STR_TO_DATE(booking_details.booking_date,'%d-%m-%Y')","column_10"=>"DATEDIFF(CURDATE(),STR_TO_DATE(booking_details.initial_booking_date,'%d-%m-%Y'))");
        $order['column'] = $columnMappingArray["column_10"];
        $order['sorting'] = "desc";
        $state = 0;
        if($this->session->userdata('is_filter_applicable') == 1){
            $state = 1;
        }
        $postData = $this->input->post();
        if(array_key_exists("order", $postData)){
            $order['column'] =$columnMappingArray["column_".$postData['order'][0]['column']];
            $order['sorting'] = $postData['order'][0]['dir'];
        }
        $bookingID = $this->input->post('booking_id');
        $finalArray = array();
        $partner_id = $this->session->userdata('partner_id');
        $selectData = "Distinct services.services,users.name as customername, users.phone_number,booking_details.*,appliance_brand,"
                . "DATEDIFF(CURDATE(),STR_TO_DATE(booking_details.initial_booking_date,'%d-%m-%Y')) as aging, count_escalation, booking_files.file_name as booking_files_purchase_inv";
        $selectCount = "Count(DISTINCT booking_details.booking_id) as count";
        $bookingsCount = $this->partner_model->getPending_booking($partner_id, $selectCount,$bookingID,$state,NULL,NULL,$this->input->post('state'))[0]->count;
        $bookings = $this->partner_model->getPending_booking($partner_id, $selectData,$bookingID,$state,$this->input->post('start'),$this->input->post('length'),$this->input->post('state'),$order);
        $sn_no = $this->input->post('start')+1;
        $upcountryString = "";
        foreach ($bookings as $key => $row) { 
             $tempArray = array();
             $upcountryString = $tempString = "";
             $tempString = "'".$row->booking_id."'";
             $tempString2 = "'".$row->amount_due."'";
             $tempString3 = "'".$row->flat_upcountry."'";
              if ($row->is_upcountry == 1 && $row->upcountry_paid_by_customer == 0) {
                 $upcountryString = '<i style="color:red; font-size:20px;" onclick="open_upcountry_model('.$tempString.','.$tempString2.','.$tempString3.')"
                    class="fa fa-road" aria-hidden="true"></i>';
               } 
             $tempArray[] = $sn_no . $upcountryString;
            if($row->booking_files_purchase_inv){
                $tempArray[] = '<a style="color:blue;" href='.base_url().'partner/booking_details/'.$row->booking_id.' target="_blank" title="View">'.$row->booking_id.'</a><br><a target="_blank" href="https://s3.amazonaws.com/'.BITBUCKET_DIRECTORY.'/misc-images/'.$row->booking_files_purchase_inv.'" title = "Purchase Invoice Verified" aria-hidden="true"><img src="http://localhost/247around-adminp-aws/images/varified.png" style="width:20px; height: 20px;"></a>';
            }
            else{
                $tempArray[] = '<a style="color:blue;" href='.base_url().'partner/booking_details/'.$row->booking_id.' target="_blank" title="View">'.$row->booking_id.'</a>';
            }
            $requestType =  $row->request_type;
            if (strpos($row->request_type, 'Installation') !== false) {
                $requestType =  "Installation";
            }
            else if(strpos($row->request_type, 'Repair') !== false){
                $requestType =  "Repair";
            }
            $tempArray[] = $row->services . "<br>". $requestType;
            $tempArray[]  = $row->appliance_brand; 
            $is_escalation = "";
             if ($row->count_escalation>0) {
                  $is_escalation =  '<i data-toggle="tooltip" title="Escalation" style="color:red; font-size:13px;" onclick="" class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></i>';
            } 
            $tempArray[] =  $is_escalation.$row->partner_internal_status;
            $tempArray[] = $row->customername;
            $tempArray[] = $row->booking_primary_contact_no;
            $tempArray[] = $row->city;
            $tempArray[] = $row->state;
            $tempArray[] = $row->booking_date;
            $tempArray[] = $row->aging;
            $bookingIdTemp = "'".$row->booking_id."'";
            $tempArray[] = '<a style="width: 36px;background: #5cb85c;border: #5cb85c;" class="btn btn-sm btn-primary  relevant_content_button" data-toggle="modal" title="Email"  onclick="create_email_form('.$bookingIdTemp.')"><i class="fa fa-envelope" aria-hidden="true"></i></a>';
            if ($row->type == _247AROUND_QUERY) { 
                $helperString = ' style="background-color: #26b99a;border-color:#26b99a;color:#fff;padding: 5px 0px;margin: 0px"';
            } 
            else { 
                $helperString = ' style="background-color: #26b99a;border-color:#26b99a;color:#fff;padding: 5px 0px;margin: 0px"';
            }
            if ($row->type != _247AROUND_QUERY) {
                if($row->partner_id == $this->session->userdata('partner_id')){
                $tempArray[]= '<div class="dropdown">
                                                    <button class="btn btn-sm btn-primary" type="button" data-toggle="dropdown" style="border: 1px solid #2a3f54;background: #2a3f54;padding: 4px 24px;">Action
                                                    <span class="caret"></span></button>
                                                    <ul class="dropdown-menu" style="padding: 5px 5px 5px 5px;margin: 0px;min-width: 95px;position: inherit;z-index: 100;">
                                                        <li style="color: #fff;"><a class="btn btn-sm btn-primary" href="'.base_url().'partner/update_booking/'.$row->booking_id.'"  title="View" 
                                                            style="background-color:#2C9D9C; border-color: #2C9D9C;color:#fff;padding: 5px 0px;
        margin: 0px;">Update</a></li>
                                                        <li style="color: #fff;margin-top:5px;">
                                                            <a id="a_hover"'.$helperString.' href="'.base_url().'partner/get_reschedule_booking_form/'.$row->booking_id.'" id="reschedule" class="btn btn-sm btn-success '.(!empty($row->service_center_closed_date) ? 'disabled' : '').'" title ="Reschedule">Reschedule</a>
                                                        </li>
                                                         <li style="color: #fff;margin-top:5px;">
                                                             <a id="a_hover" style="background-color: #d9534f;border-color:#d9534f;color:#fff;padding: 5px 0px;margin: 0px;"href='.base_url().'partner/get_cancel_form/'._247AROUND_PENDING.'/'.$row->booking_id.' class="btn btn-sm btn-danger" title="Cancel">Cancel</a>
                                                         </li>
                                                    </ul>
                                                </div>';
                } else {
                    $tempArray[] =  "";
                }
            }
            else{
              $tempArray[] =  "";
            }
            $tempArray[] =  '<a target="_blank" href="https://s3.amazonaws.com/'.BITBUCKET_DIRECTORY.'/jobcards-pdf/'.$row->booking_jobcard_filename.'" class="btn btn-sm btn-primary btn-sm" target="_blank" ><i class="fa fa-download" aria-hidden="true"></i></a>';
            $initialBooking = strtotime($row->initial_booking_date);
            $now = time();
            $datediff = $now - $initialBooking;
            $days= $datediff / (60 * 60 * 24);
            $futureBookingDateMsg = "'Booking has future booking date so you can not escalate the booking'";
            $partnerDependencyMsg = "'Escalation can not be Processed, Because booking in ".$row->partner_internal_status." state'";
            if ($row->type == "Query") {
                $helperText_2 = 'style="pointer-events: none;background: #ccc;border-color:#ccc;"'; 
            }
            if($row->actor != 'Partner' && $days>=0){
               $helperText_2 =  'data-target="#myModal"';
            } 
           else if($days<0){  
              $helperText_2 =  'onclick="alert('.$futureBookingDateMsg.')"' ;
            }
            else{
              $helperText_2 = 'onclick="alert('.$partnerDependencyMsg.')"'; 
            }
              
            $disable = "";
            $toggle = "modal";
            if(!empty($row->service_center_closed_date)){
                $disable = "Disabled";
                $toggle = "tooltip";
                
            }
            $tempArray[] = '<a  href="javascript:void(0)" title="Escalate" data-disabled ="'.$disable.'" '
                    . 'class="btn btn-sm btn-warning open-AddBookDialog" data-id= "'.$row->booking_id.'" '.$helperText_2.''
                    . ' data-toggle="'.$toggle.'"><i class="fa fa-circle" aria-hidden="true"></i></a>';
            $tempArray[] = '<a  href="#" class="btn btn-sm btn-warning btn-sm" title="Helper Document" data-toggle="modal" data-target="#showBrandCollateral" onclick=get_brand_collateral("'.$row->booking_id.'")><i class="fa fa-file-text-o" aria-hidden="true"></i></a>';
            $finalArray[] = $tempArray;
             $sn_no++;
           }
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $bookingsCount,
            "recordsFiltered" =>  $bookingsCount,
            "data" => $finalArray,
        );
        echo json_encode($output);
    }
    function get_spare_bookings(){
      $agent_id = $this->session->userdata('agent_id');
      $finalArray = array();
      $postData = $this->input->post();
      $state = 0;
      $columnMappingArray = array("column_1"=>"spare_parts_details.booking_id","column_3"=>"DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(date_of_request, '%Y-%m-%d'))",
          "column_4"=>"GROUP_CONCAT(DISTINCT spare_parts_details.parts_requested)","column_7"=>"booking_details.state");    
      $order['column'] =$columnMappingArray["column_3"];
      $order['sorting'] = "desc";
      if(array_key_exists("order", $postData)){
            $order['column'] =$columnMappingArray["column_".$postData['order'][0]['column']];
            $order['sorting'] = $postData['order'][0]['dir'];
        }
       $partner_id = $this->session->userdata('partner_id');
       $where = "spare_parts_details.partner_id = '" . $partner_id . "' AND  spare_parts_details.entity_type =  '"._247AROUND_PARTNER_STRING."' AND status = '" . SPARE_PARTS_REQUESTED . "' "
                . " AND booking_details.current_status IN ('"._247AROUND_PENDING."', '"._247AROUND_RESCHEDULED."') "
                . " AND wh_ack_received_part != 0 ";
       if($this->input->post('state')){
           $state = $this->input->post('state');
           $where = $where." AND booking_details.state = '$state'";
       }
       if($this->input->post('booking_id')){
           $booking_id = $this->input->post('booking_id');
           $where = $where." AND booking_details.booking_id = '$booking_id'";
       }
       if($this->session->userdata('is_filter_applicable') == 1){
            $state = 1;
            $where .= " AND booking_details.state IN (SELECT state FROM agent_filters WHERE agent_id = ".$agent_id." AND agent_filters.is_active=1)";
        }
        $select = "spare_parts_details.booking_id,GROUP_CONCAT(spare_parts_details.quantity) as quantity,services.services, i.part_number, GROUP_CONCAT(DISTINCT spare_parts_details.parts_requested) as parts_requested, users.name, "
                . "booking_details.booking_primary_contact_no, booking_details.partner_id as booking_partner_id, booking_details.state, "
                . "booking_details.booking_address,booking_details.initial_booking_date, booking_details.is_upcountry, i.part_number, "
                . "booking_details.upcountry_paid_by_customer,booking_details.amount_due, booking_details.flat_upcountry,booking_details.state, service_centres.name as vendor_name, "
                . "service_centres.address, service_centres.state, service_centres.gst_no, service_centres.pincode, "
                . "service_centres.district,service_centres.id as sf_id,service_centres.is_gst_doc,service_centres.signature_file, "
                . "DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(date_of_request, '%Y-%m-%d')) AS age_of_request,"
                . " GROUP_CONCAT(DISTINCT spare_parts_details.model_number) as model_number, "
                . " GROUP_CONCAT(DISTINCT spare_parts_details.serial_number) as serial_number,"
                . " GROUP_CONCAT(DISTINCT spare_parts_details.remarks_by_sc) as remarks_by_sc, spare_parts_details.partner_id, "
                . " GROUP_CONCAT(DISTINCT spare_parts_details.id) as spare_id, serial_number_pic ";
         $bookingData = $this->service_centers_model->get_spare_parts_on_group($where, $select, "spare_parts_details.booking_id", false, $postData['length'], $postData['start'],0,$order);
         $bookingCount = $this->service_centers_model->get_spare_parts_on_group($where, "count( Distinct spare_parts_details.booking_id) AS total_rows","spare_parts_details.booking_id", FALSE,-1,-1,1)[0]['total_rows'];
         $sn = $postData['start'];
         foreach ($bookingData as $key => $row) {
                    $tempArray = array();
                    $sn++;
                    $tempString = $tempString2 = $tempString3 = $tempString4 = $tempString5 ="";
                    if($row['is_upcountry'] == 1 && $row['upcountry_paid_by_customer'] == 0) {
                       $tempString = '<i style="color:red; font-size:20px;" onclick="open_upcountry_model('.$row['booking_id'].'", "'.$row['amount_due'].'", "'.$row['flat_upcountry'].')" class="fa fa-road" aria-hidden="true"></i>';
                    }
                    $tempArray[] =  $sn. $tempString;
                    $tempArray[] =  '<a target="_blank"  style="color:blue;" href='.base_url().'partner/booking_details/'.$row['booking_id'].'  title="View">'.$row['booking_id'].'</a>';
                    $tempArray[] =  $row['services'];
                    $tempArray[] =  $row['name'];
                    $tempArray[] =  $row['age_of_request'];
                    $tempArray[] =  "<span style='word-break: break-all;'>". $row['parts_requested'] ."</span>";
                    $tempArray[] =  "<span style='word-break: break-all;'>". $row['part_number'] ."</span>";
                    $tempArray[] =  $row['quantity'];
                    $tempArray[] =  $row['model_number'];
                    $tempArray[] =  $row['serial_number'];
                    $tempArray[] =  $row['state'];
                    $tempArray[] =  $row['remarks_by_sc'];
                    $bookingIdTemp = "'".$row['booking_id']."'";
                    $tempArray[] =  '<a style="width: 36px;background: #5cb85c;border: #5cb85c;" class="btn btn-sm btn-primary  relevant_content_button" data-toggle="modal" title="Email"  onclick="create_email_form('.$bookingIdTemp.')"><i class="fa fa-envelope" aria-hidden="true"></i></a>';
                    $tempString2 =  '<div class="dropdown">
                            <button class="btn btn-sm btn-primary" type="button" data-toggle="dropdown" style="    border: 1px solid #2a3f54;background: #2a3f54;">Action
                            <span class="caret"></span></button>
                            <ul class="dropdown-menu" style="border: none;background: none;z-index: 100;position: inherit;min-width: 70px;">
                                <div class="action_holder" style="background: #fff;border: 1px solid #2c9d9c;padding: 1px;">
                                <li style="color: #fff;"><a href='.base_url().'partner/update_spare_parts_form/'.$row['booking_id'].' class="btn btn-sm btn-success" title="Update" style="color:#fff;margin: 0px;padding: 5px 12px;" ></i>Update</a></li>';
                    $explode = explode(",", $row['spare_id']);
                    if(count($explode) == 1){ 
                     $tempString3 =  '<li style="color: #fff;margin-top:5px;"><a href="#" data-toggle="modal" id="spare_parts"'.$row['spare_id'].'" data-url='.base_url().'employee/inventory/update_action_on_spare_parts/'.$row['spare_id'] . '/' . $row['booking_id'].'/CANCEL_PARTS data-booking_id="'.$row['booking_id'].'" data-target="#myModal2" class="btn btn-sm btn-danger open-adminremarks" title="Reject" style="color:#fff;margin: 0px;padding: 5px 14.4px;" >Reject</a></li>';
                    }
                     $tempString4 = '</ul>';
                     $tempArray[] =  $tempString2 . $tempString3 .$tempString4;
                     if(empty($row['gst_no']) && empty ($row['signature_file'])){
                         $tempString5 = '<a class="btn btn-sm btn-success" href="#" title="GST number not available" style="background-color:#2C9D9C; border-color: #2C9D9C; cursor: not-allowed;"><i class="fa fa-close"></i></a>';
                     }
                     else if(empty ($row['signature_file'])) {
                           $tempString5 = '<a class="btn btn-sm btn-success" href="#" title="Signature file is not available" style="background-color:#2C9D9C; border-color: #2C9D9C;cursor: not-allowed;"><i class="fa fa-times"></i></a>';
                      }
                      else{
                            $tempString5 = '<a class="btn btn-sm btn-success" href='.base_url().'partner/download_sf_declaration/'.rawurlencode($row['sf_id']).'  title="Download Declaration" style="background-color:#2C9D9C; border-color: #2C9D9C;" target="_blank"><i class="fa fa-download"></i></a>';
                        }
                      $tempArray[] = $tempString5;
                      $tempArray[] = '<input type="checkbox" class="form-control checkbox_address"  name="download_address[]" onclick="check_checkbox(1)" value="'.$row['booking_id'].'" />';
                      $tempArray[] = '<input type="checkbox" class="form-control checkbox_manifest" name="download_courier_manifest[]" onclick="check_checkbox(0)" value="'.$row['booking_id'].'" />';

                      $tempArray[] =  "<a href='#' class='btn btn-info approve_nrn_booking' data-toggle='modal'  data-target='#myModal77'   data-booking_id='".$row['booking_id']."' >Approve</a>";

                      $finalArray[] = $tempArray;
           }
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $bookingCount,
            "recordsFiltered" =>  $bookingCount,
            "data" => $finalArray,
        );
        echo json_encode($output);
    }
     function get_defactive_part_shipped_by_sf_bookings(){
      $finalArray = array();
      $postData = $this->input->post();
      $state = 0;
      $where_internal_status = array("page" => "defective_parts", "active" => '1');
      $internal_status = $this->booking_model->get_internal_status($where_internal_status);
      $columnMappingArray = array("column_1"=>"spare_parts_details.booking_id","column_3"=>"CONCAT('',GROUP_CONCAT((defective_part_shipped ) ))",
          "column_4"=>"courier_name_by_sf","column_9"=>"spare_parts_details.defective_part_shipped_date");    
      $order_by = "spare_parts_details.defective_part_shipped_date DESC, spare_parts_details.booking_id DESC";
      if(array_key_exists("order", $postData)){
            $order_by = $columnMappingArray["column_".$postData['order'][0]['column']] ." ". $postData['order'][0]['dir'];
        }
       $partner_id = $this->session->userdata('partner_id');
            if($this->session->userdata('is_filter_applicable') == 1){
            $state = 1;
         }
        $where = array(
            "spare_parts_details.defective_part_required" => 1,
            "approved_defective_parts_by_admin" => 1,
            '((spare_parts_details.defective_return_to_entity_id ="'.$partner_id.'" '
            . 'AND spare_parts_details.defective_return_to_entity_type = "'._247AROUND_PARTNER_STRING.'" '
            . ' AND status IN("'.DEFECTIVE_PARTS_SHIPPED.'","'.DEFECTIVE_PARTS_SHIPPED_PENDING.'")  ) OR '
            . '(booking_details.current_status ="'._247AROUND_COMPLETED.'" AND '
            . 'spare_parts_details.defective_return_to_entity_type = "'._247AROUND_SF_STRING.'"'
            . 'AND booking_details.partner_id = "'.$partner_id.'" '
            . 'AND spare_parts_details.status = "'.DEFECTIVE_PARTS_SEND_TO_PARTNER_BY_WH.'"))' => NULL
        );

       // print_r($where);  exit;
       if($this->input->post('state')){
           $where['booking_details.state'] = $this->input->post('state');
       }
       if($this->input->post('booking_id')){
           $where['spare_parts_details.booking_id'] = $this->input->post('booking_id');
       }
        $select = "defective_part_shipped,spare_parts_details.defactive_part_received_date_by_courier_api, "
                . " spare_parts_details.booking_id, users.name, courier_name_by_sf, awb_by_sf,defective_part_shipped_date,"
                . "remarks_defective_part_by_sf,spare_parts_details.sf_challan_number"
                . ",spare_parts_details.sf_challan_file, spare_parts_details.shipped_quantity   as qty,spare_parts_details.partner_challan_number, spare_parts_details.id, spare_parts_details.status, i.part_number";
        //$group_by = "spare_qty_mgmt.spare_id";
                $group_by =FALSE;
        $bookingData = $this->service_centers_model->get_spare_parts_booking($where, $select, $group_by, $order_by, $postData['start'], $postData['length'],0,NULL,TRUE);

         $bookingCount = $this->service_centers_model->count_spare_parts_booking($where, $select, $group_by,$state);
         $sn = $postData['start'];
         foreach ($bookingData as  $row) {
                    $tempArray = array();
                    $tempString = $tempString2 = $tempString3 = $tempString4 = $tempString5 = $tempString6 = $tempString7 = "";
                    $sn++;
                    $tempArray[] = $sn;
                    $tempArray[] = '<a target="_blank"  style="color:blue" href='.base_url().'partner/booking_details/'.$row['booking_id'].'  title="View">'.$row['booking_id'].'</a>';
                    $tempArray[] = $row['name'];
                    $tempArray[] = "<span style='word-break: break-all;'>". $row['defective_part_shipped'] ."</span>";
                    $tempArray[] = "<span style='word-break: break-all;'>". $row['part_number'] ."</span>";
                    $tempArray[] = "<span>". $row['shipped_quantity'] ."</span>";
                    $tempArray[] = $row['courier_name_by_sf'];
                    $courier_name_by_sf = "'".$row['courier_name_by_sf']."'";
                    $awb_by_sf = "'".$row['awb_by_sf']."'";
                    $spareStatus = "'".DELIVERED_SPARE_STATUS."'";
                    if(!$row['defactive_part_received_date_by_courier_api']){
                        $spareStatus = "'".DEFECTIVE_PARTS_SHIPPED."'";
                    }
                    $container = "'awb_loader_".$row['awb_by_sf']."'";
                    $awbString = '<a href="javascript:void(0)" onclick="get_awb_details('.$courier_name_by_sf.','.$awb_by_sf.','.$spareStatus.','.$container.')">'.$row['awb_by_sf'].'</a> 
                                            <span id='.$container.' style="display:none;"><i class="fa fa-spinner fa-spin"></i></span>';
                    $tempArray[] = $awbString;
                    if(!empty($row['sf_challan_file'])) {  
                         $tempString = '<a style="color: blue;" href="https://s3.amazonaws.com/'.BITBUCKET_DIRECTORY.'/vendor-partner-docs/'.$row['sf_challan_file'].'" target="_blank">'.$row["sf_challan_number"].'</a>';
                    }
                    $tempArray[] = $tempString;
                     if(!empty($row['partner_challan_file'])) {
                        $tempString2 = '<a href="https://s3.amazonaws.com/'. BITBUCKET_DIRECTORY.'/vendor-partner-docs/'.$row['partner_challan_file'].'" target="_blank">'.$row["partner_challan_number"].'</a>';
                     }
                     else if(!empty($row['partner_challan_number'])) {
                         $tempString2 = $row['partner_challan_number'];
                    }
                    $tempArray[] = $tempString2;
                    
                     if (!is_null($row['defective_part_shipped_date'])) {
                         $tempString3 =  date("d-m-Y", strtotime($row['defective_part_shipped_date']));
                     }
                    $tempArray[] = $tempString3;
                    
                     $bookingIdTemp = "'".$row['booking_id']."'";
                     $tempArray[] = '<a style="width: 36px;background: #5cb85c;border: #5cb85c;" class="btn btn-sm btn-primary  relevant_content_button" data-toggle="modal" title="Email" onclick="create_email_form_2('.$bookingIdTemp.')"><i class="fa fa-envelope" aria-hidden="true"></i></a>';
                   
                    $tempArray[] = $row['remarks_defective_part_by_sf'];
                    if (!empty($row['defective_part_shipped'])) {
                            if(empty($row['defective_part_shipped'])){
                             $tempString5 = 'disabled="disabled"';
                            }
                            
                        if($row['status'] == DEFECTIVE_PARTS_SEND_TO_PARTNER_BY_WH){
                            $tempString4 = '<a style="background: #2a3f54; border-color: #2a3f54;" onclick="return confirm_received()" class="btn btn-sm btn-primary" id="defective_parts"
                                               href='.base_url().'partner/acknowledge_defective_parts_sent_by_wh/'.$row['id'].'/'.$row['booking_id'].'/'.$this->session->userdata("partner_id").'/0/'.$row['spare_qty_mgmt_id'].' '.$tempString5.'>Receive</a>';
                    
                            
                        } else {
                            $tempString4 = '<a style="background: #2a3f54; border-color: #2a3f54;" onclick="return confirm_received()" class="btn btn-sm btn-primary" id="defective_parts"
                                               href='.base_url().'partner/acknowledge_received_defective_parts/'.$row['id'].'/'.$row['booking_id'].'/'.$this->session->userdata("partner_id").'/0/'.$row['spare_qty_mgmt_id'].' '.$tempString5.'>Receive</a>';
                    
                        }
                    }
                     $tempArray[] = $tempString4;
                     if (!empty($row['defective_part_shipped'])) {
                            foreach ($internal_status as $value) {
                                if($row['status'] == DEFECTIVE_PARTS_SEND_TO_PARTNER_BY_WH){
                                  $tempString7 = $tempString7.'<li><a href='.base_url().'partner/reject_defective_part_sent_by_wh/'.$row['id'].'/'.$row['booking_id'].'/'.urlencode(base64_encode($value->status)).'>'.$value->status.'/'.$row['spare_qty_mgmt_id'].'</a></li>';
                                  $tempString7 = $tempString7.'<li class="divider"></li>';
                                } else {
                                    $tempString7 = $tempString7.'<li><a href='.base_url().'partner/reject_defective_part/'.$row['id'].'/'.$row['booking_id'].'/'.urlencode(base64_encode($value->status)).'>'.$value->status.'/'.$row['spare_qty_mgmt_id'].'</a></li>';
                                  $tempString7 = $tempString7.'<li class="divider"></li>';
                                }
                             } 
                              $tempString6 = '<div class="dropdown">
                                            <a href="#" class="dropdown-toggle btn btn-sm btn-danger" type="button" data-toggle="dropdown">Reject<span class="caret"></span></a>
                                            <ul class="dropdown-menu" style="right: 0px;left: auto;">'.$tempString7.'</ul> </div>';
                       }
                       $tempArray[] = $tempString6;
                       $finalArray[] = $tempArray;
           }
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $bookingCount,
            "recordsFiltered" =>  $bookingCount,
            "data" => $finalArray,
        );
        echo json_encode($output);
    }
    function get_waiting_upcountry_charges(){
        $where = array();
        $finalArray = array();
        $postData = $this->input->post();
        $state = 0;
         $columnMappingArray = array("column_1"=>"bd.booking_id","column_2"=>"request_type","column_4"=>"services","column_5"=>"appliance_brand","column_6"=>"appliance_category","column_7"=>"appliance_capacity"
             ,"column_9"=>"sbs.district","column_10"=>"sbs.pincode","column_11"=>"bd.upcountry_update_date","column_12"=>"bd.upcountry_distance","column_13"=>"bd.partner_upcountry_rate");    
         $order_by = "bd.booking_id";
         if($this->session->userdata('is_filter_applicable') == 1){
              $state = 1;
           }  
        if(array_key_exists("order", $postData)){
              $order_by = $columnMappingArray["column_".$postData['order'][0]['column']] ." ". $postData['order'][0]['dir'];
          }
         $partner_id = $this->session->userdata('partner_id');
         if($this->input->post('state')){
             $where['bd.state'] = $this->input->post('state');
         }
         if($this->input->post('booking_id')){
             $where['bd.booking_id'] = $this->input->post('booking_id');
         }
          $bookingCount = $this->upcountry_model->get_waiting_for_approval_upcountry_charges($partner_id,$state,1,$where)[0]['count'];
          $bookingData = $this->upcountry_model->get_waiting_for_approval_upcountry_charges($partner_id,$state,0,$where,$order_by,$postData['length'],$postData['start']);
           $sn = $postData['start'];
           foreach ($bookingData as $key => $row) {
                      $tempArray = array();
                      $tempString = "";
                      $sn++;
                      $tempArray[] = $sn;
                      $tempArray[] = '<a style="color:blue;" href='. base_url().'partner/booking_details/'.$row['booking_id'].'  title="View">'.$row['booking_id'].'</a>';
                      $tempArray[] = $row['request_type'];
                      $tempArray[] = $row['name'];
                      $tempArray[] = $row['services'];
                      $tempArray[] = $row['appliance_brand'];
                      $tempArray[] = $row['appliance_category'];
                      $tempArray[] = $row['appliance_capacity'];
                      $tempArray[] = $row['booking_address'] . ", " . $row['city'] . ", Pincode - " . $row['booking_pincode'] . ", " . $row['state'];
                      $tempArray[] = $row['upcountry_district'];
                      $tempArray[] = $row['upcountry_pincode'];
                      $age_requested = date_diff(date_create($row['upcountry_update_date']), date_create('today'));
                      $tempArray[] = $age_requested->days ." Days";
                      $tempArray[] = $row['upcountry_distance'] . " KM";
                      $tempArray[] = sprintf("%0.2f",$row['upcountry_distance'] * $row['partner_upcountry_rate']);
                      $tempString = '<div class="dropdown">
                                                <button class="btn btn-sm btn-primary" type="button" data-toggle="dropdown" style="border: 1px solid #2a3f54;background: #2a3f54;">Action
                                                <span class="caret"></span></button>
                                                <ul class="dropdown-menu" style="border: none;background: none;position: inherit;z-index: 100;min-width: 70px;">
                                                    <div class="action_holder" style="background: #fff;border: 1px solid #2c9d9c;padding: 1px;">
                                                    <li style="color: #fff;">
                                                        <a href='.base_url().'partner/upcountry_charges_approval/'.$row['booking_id'].'/1 class="btn btn-md btn-success" style="color:#fff;margin: 0px;padding: 5px 5.5px;">Approve</a></li>
                                                    <li style="color: #fff;margin-top:5px;">
                                                        <a style="color:#fff;margin: 0px;padding: 5px 11px;" href='.base_url().'partner/reject_upcountry_charges/'.$row['booking_id'].'/1 class="btn btn-md btn-danger">Reject</a>
                                                    </li>
                                           </div>
                                                </ul>
                                            </div>';
                      $tempArray[] = $tempString;
                      $finalArray[] = $tempArray;
             }
          $output = array(
              "draw" => $this->input->post('draw'),
              "recordsTotal" => $bookingCount,
              "recordsFiltered" =>  $bookingCount,
              "data" => $finalArray,
          );
          echo json_encode($output);
    }


   

    function get_review_booking_data(){
        $finalArray = array();
        $state=0;
        $postData = $this->input->post();
        $columnMappingArray = array("column_2"=>"booking_details.request_type","column_3"=>"sc.cancellation_reason",
            "column_6"=>"booking_details.city", "column_7"=>"booking_details.state","column_8"=>"STR_TO_DATE(booking_details.initial_booking_date,'%d-%m-%Y')",
            "column_9"=>"DATEDIFF(CURDATE(),STR_TO_DATE(booking_details.initial_booking_date,'%d-%m-%Y'))");    
        $order_by = "ORDER BY booking_details.booking_id DESC";
        if(array_key_exists("order", $postData)){
               $order_by = "ORDER BY ".$columnMappingArray["column_".$postData['order'][0]['column']] ." ". $postData['order'][0]['dir'];
          }
         $partner_id = $this->session->userdata('partner_id');
          if($this->session->userdata('is_filter_applicable') == 1){
            $state = 1;
         }
         $statusData = $this->reusable_model->get_search_result_data("partners","partners.booking_review_for,partners.review_time_limit",array("booking_review_for IS NOT NULL"=>NULL,"id"=>$partner_id),NULL,NULL,NULL,NULL,NULL,array());
         $whereIN['booking_details.partner_id'] = array($partner_id);
          if($this->input->post('state')){
           $where['booking_details.state ="'.$this->input->post('state').'"'] = NULL;
          }
         $where['DATEDIFF(CURRENT_TIMESTAMP,  sc.closed_date)<='.$statusData[0]['review_time_limit']] = NULL;
         if($this->input->post('booking_id')){
             $whereIN['booking_details.booking_id'] = array($this->input->post('booking_id'));
         }
           $bookingCount = $this->service_centers_model->get_admin_review_bookings(NULL,"Cancelled",$whereIN,1,-1,-1,$where,0,NULL,"COUNT(DISTINCT sc.booking_id) as count",$state)[0]['count'];
           $bookingData = $this->service_centers_model->get_admin_review_bookings(NULL,"Cancelled",$whereIN,1,$postData['start'],$postData['length'],$where,1,$order_by,NULL,$state);
           $sn = $postData['start'];
           foreach ($bookingData as $key => $row) {
                $tempArray = array();
                $tempString = $tempString2 = $tempString3 = $tempString4 = "";
                $sn++;
                if ($row['is_upcountry'] == 1) {
                      $tempString2 = '"'. $row['booking_id'].'"';
                      $tempString3 = '"'. $row['amount_due'].'"';
                      $tempString4 = '"'. $row['flat_upcountry'].'"';
                      $tempString  ='<i style="color:red; font-size:20px;" onclick="open_upcountry_model('.$tempString2.'"," '.$tempString3.'"," '.$tempString4.')"class="fa fa-road" aria-hidden="true"></i>';
                 }
                $tempArray[] = $sn.$tempString;
                $tempArray[] = '<a style="color:blue;" href='.base_url().'partner/booking_details/'.$row['booking_id'].' target="_blank" title="View">'.$row['booking_id'].'</a>';
                $tempString4 =  $row['request_type'];
                if (strpos($row['request_type'], 'Installation') !== false) {
                    $tempString4 =  "Installation";
                }
                else if(strpos($row['request_type'], 'Repair') !== false){
                    $tempString4 =  "Repair";
                }
                 $tempArray[] = $row['services']."</br>".$tempString4;
                 $tempArray[] = $row['cancellation_reason'];
                 $tempArray[] = $row['name'];
                 $tempArray[] = $row['booking_primary_contact_no'];
                 $tempArray[] = $row['city'];
                 $tempArray[] = $row['state'];
                 $tempArray[] = $row['booking_date'];
                 $tempArray[] = $row['age'];
                 $tempString5  = "'".$row['booking_id']."'";
                 $tempArray[] = '<input type="hidden" class="form-control" id="partner_id" name="partner_id['.$row['booking_id'].']" value = '.$row['partner_id'].'>
                                      <input id="approved_close" type="checkbox"  class="checkbox1" name="approved_booking[]" value="'.$row['booking_id'] .'">
                                      <input id="approved_by" type="hidden"   name="approved_by" value="'.$row['partner_id'].'>';
                 $tempArray[] = '<button style="min-width: 59px;" type="button" class="btn btn-primary btn-sm open-adminremarks" 
                                                                               data-toggle="modal" data-target="#myModal2" onclick="create_reject_form('.$tempString5.')">Reject</button>';
                $finalArray[] = $tempArray;
             }
          $output = array(
              "draw" => $this->input->post('draw'),
              "recordsTotal" => $bookingCount,
              "recordsFiltered" =>  $bookingCount,
              "data" => $finalArray,
          );
       echo json_encode($output);
    }
    function get_shipped_spare_waiting_for_confirmation(){
      $finalArray = array();
      $postData = $this->input->post();
      $state = 0;
      if($this->session->userdata('is_filter_applicable') == 1){
            $state = 1;
      }
      $columnMappingArray = array(
          "column_1"=>"spare_parts_details.booking_id",
          "column_5"=>"date_of_purchase",
          "column_6"=>"parts_shipped",
          "column_7"=>"part_number",
          "column_8"=>"quantity",
          "column_10"=>"courier_name_by_partner",
          "column_13"=>"shipped_date",
          "column_14"=>"remarks_by_partner"
      );
     $order_by = "ORDER BY shipped_date DESC";
      if(array_key_exists("order", $postData)){
            $order_by = "ORDER BY ".$columnMappingArray["column_".$postData['order'][0]['column']] ." ". $postData['order'][0]['dir'];
        }
        $partner_id = $this->session->userdata('partner_id');
        //Parts Shipped by Partner But Did'nt Get by SF
        $where = "spare_parts_details.partner_id = '" . $partner_id . "'AND status IN ( '".SPARE_SHIPPED_BY_PARTNER."')  ";
       if($this->input->post('state')){
           $where = $where." AND booking_details.state = '".$this->input->post('state')."'";
       }
       if($this->input->post('booking_id')){
           $where = $where." AND spare_parts_details.booking_id = '".$this->input->post('booking_id')."'";
       }
       $bookingCount = $this->partner_model->get_spare_parts_booking_list($where, false, false, false,$state)[0]['total_rows'];
       $bookingData = $this->partner_model->get_spare_parts_booking_list($where, $postData['start'], $postData['length'], true,$state,NULL,FALSE,$order_by);
         $sn = $postData['start'];
         foreach ($bookingData as $key => $row) {
                    $tempArray = array();
                    $tempString = $tempString2 = $tempString3 = $tempString4 = $tempString5 = $tempString6 = $tempString7 = "";
                    $sn++;
                    $tempArray[] = $sn;
                    $tempArray[] = ' <a style="color:blue;"  href='.base_url().'partner/booking_details/'.$row['booking_id'].'  title="View">'.$row['booking_id'].'</a>';
                    $tempArray[] = $row['name'];
                    $tempArray[] = $row['services'];
                    $tempArray[] = $row['model_number'];
                    $tempArray[] = $row['date_of_purchase'];
                    $tempArray[] = "<span style='word-break: break-all;'>". $row['parts_shipped'] ."</span>";
                    $tempArray[] = "<span style='word-break: break-all;'>". $row['part_number'] ."</span>";
                    $tempArray[] = "<span>". $row['quantity'] ."</span>";
                    $tempArray[] = $row['shipped_quantity'];
                    $tempArray[] = $row['courier_name_by_partner'];
                    $tempArray[] = $row['awb_by_partner'];
                    if(!empty($row['partner_challan_file'])) {
                          $tempString = '<a href="https://s3.amazonaws.com/'.BITBUCKET_DIRECTORY.'/vendor-partner-docs/'.$row['partner_challan_file'].' target="_blank">'.$row['partner_challan_number'].'</a>';
                    }
                    else if(!empty($row['partner_challan_number'])) {
                          $tempString =  $row['partner_challan_number'];
                    }
                    $tempArray[] = $tempString;
                    $tempArray[] = date("d-m-Y", strtotime($row['shipped_date']));
                    $tempArray[] = $row['remarks_by_partner'];
                    $finalArray[] = $tempArray;
           }
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $bookingCount,
            "recordsFiltered" =>  $bookingCount,
            "data" => $finalArray
        );
        echo json_encode($output);
    }
    function get_sf_needs_to_send_spare(){
      $finalArray = array();
      $postData = $this->input->post();
      $state = 0;
        if($this->session->userdata('is_filter_applicable') == 1){
          $state = 1;
        }
      $columnMappingArray = array(
            "column_1"=>"spare_parts_details.booking_id",
            "column_5"=>"spare_parts_details.date_of_purchase",
            "column_6"=>"defective_part_shipped",
            "column_8"=>"spare_parts_details.quantity",
            "column_11"=>"spare_parts_details.partner_challan_number",
            "column_12"=>"DATEDIFF(CURDATE(),date(booking_details.service_center_closed_date))"
      );    
      $order_by = "spare_parts_details.defective_part_shipped_date DESC";
      if(array_key_exists("order", $postData)){
            $order_by = $columnMappingArray["column_".$postData['order'][0]['column']] ." ". $postData['order'][0]['dir'];
        }
       $partner_id = $this->session->userdata('partner_id');
               $where = array(
            "spare_parts_details.defective_part_required" => 1,
            "booking_details.partner_id" => $partner_id,
            "status IN ('" . DEFECTIVE_PARTS_PENDING . "','".DEFECTIVE_PARTS_SHIPPED_PENDING."' ,'".DEFECTIVE_PARTS_REJECTED."')  " => NULL
        );
       if($this->input->post('state')){
           $where['booking_details.state'] = $this->input->post('state');
       }
       if($this->input->post('booking_id')){
           $where['spare_parts_details.booking_id'] = $this->input->post('booking_id');
       }
        $select = "CONCAT( '', GROUP_CONCAT((parts_shipped ) ) , '' ) as defective_part_shipped,"
                ." spare_parts_details.booking_id,"
                ."spare_parts_details.date_of_purchase as 'dop',"
                ." spare_parts_details.model_number as 'model',"
                ."spare_parts_details.date_of_purchase as 'dop',"
                ." spare_parts_details.shipped_quantity, services.services as 'product_name',"
                ." users.name,DATEDIFF(CURDATE(),date(booking_details.service_center_closed_date)) as aging,"
                ." spare_parts_details.courier_name_by_partner,"
                ." spare_parts_details.awb_by_partner,spare_parts_details.partner_challan_number, i.part_number";
        $group_by = "spare_parts_details.booking_id";
        $bookingData = $this->service_centers_model->get_spare_parts_booking($where, $select, $group_by, $order_by, $postData['start'], $postData['length'],$state);
        $bookingCount =  $this->service_centers_model->count_spare_parts_booking($where, $select, $group_by,$state);
         $sn = $postData['start'];
         foreach ($bookingData as $key => $row) {
                    $tempArray = array();
                    $tempString = $tempString2 = $tempString3 = $tempString4 = $tempString5 = $tempString6 = $tempString7 = "";
                    $sn++;
                    $tempArray[] = $sn;
                    $tempArray[] = '<a  style="color:blue" href='.base_url().'partner/booking_details/'.$row['booking_id'].'  title="View">'.$row['booking_id'].'</a>';  
                    $tempArray[] = $row['name'];
                    $tempArray[] = $row['product_name'];
                    $tempArray[] = $row['model'];
                    $tempArray[] = $row['dop'];
                    $tempArray[] = "<span style='word-break: break-all;'>". $row['defective_part_shipped'].'</span>';
                    $tempArray[] = "<span style='word-break: break-all;'>". $row['part_number'].'</span>';
                    $tempArray[] = $row['shipped_quantity'];
                    $tempArray[] = $row['courier_name_by_partner'];
                    $tempArray[] = $row['awb_by_partner'];
                    if(!empty($row['partner_challan_file'])) {
                         $tempString ='<a href="https://s3.amazonaws.com/'.BITBUCKET_DIRECTORY.'/vendor-partner-docs/'.$row['partner_challan_file'].' target="_blank">'.$row['partner_challan_number'].'</a>';
                    }
                     else if(!empty($row['partner_challan_number'])) {
                          $tempString =  $row['partner_challan_number'];
                    }
                    $tempArray[] = $tempString;
                    $tempArray[] = $row['aging'];
                    $finalArray[] = $tempArray;
           }
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $bookingCount,
            "recordsFiltered" =>  $bookingCount,
            "data" => $finalArray,
        );
        echo json_encode($output);
    }
    function received_defactive_parts_by_partner(){
      $finalArray = array();
      $postData = $this->input->post();
      $state = 0;
        if($this->session->userdata('is_filter_applicable') == 1){
          $state = 1;
        }
      $columnMappingArray = array("column_1"=>"spare_parts_details.booking_id","column_3"=>"defective_part_shipped",
          "column_4"=>"received_defective_part_date","column_5"=>"awb_by_partner","column_6"=>"courier_name_by_partner");    
      $order_by = "ORDER BY spare_parts_details.defective_part_shipped_date DESC";
      if(array_key_exists("order", $postData)){
            $order_by = "ORDER BY ".$columnMappingArray["column_".$postData['order'][0]['column']] ." ". $postData['order'][0]['dir'];
        }
       $partner_id = $this->session->userdata('partner_id');
       $where = "spare_parts_details.defective_return_to_entity_id = '" . $partner_id . "' AND spare_parts_details.defective_return_to_entity_type = '"._247AROUND_PARTNER_STRING."'  AND approved_defective_parts_by_partner = '1' AND status != '"._247AROUND_CANCELLED."'";
       if($this->input->post('state')){
           $where =  $where.' AND booking_details.state = "' .$this->input->post('state').'"';
       }
       if($this->input->post('booking_id')){
           $where =  $where.' AND booking_details.booking_id = "' .$this->input->post('booking_id').'"';
       }
        $bookingData = $this->partner_model->get_spare_parts_booking_list($where, $postData['start'], $postData['length'], true,$state,NULL,FALSE,$order_by);
        $bookingCount =  $this->partner_model->get_spare_parts_booking_list($where, false, false, false,$state)[0]['total_rows'];
         $sn = $postData['start'];
         foreach ($bookingData as $key => $row) {
                    $tempArray = array();
                    $tempString = $tempString2 = $tempString3 = $tempString4 = $tempString5 = $tempString6 = $tempString7 = "";
                    $sn++;
                    $tempArray[] = $sn;
                    $tempArray[] = '<a  style="color:blue" href='.base_url().'partner/booking_details/'.$row['booking_id'].'  title="View">'.$row['booking_id'].'</a>';  
                    $tempArray[] = $row['name'];
                    $tempArray[] = "<span style='word-break: break-all;'>". $row['defective_part_shipped'] ."</span>";
                    $tempArray[] = "<span style='word-break: break-all;'>". $row['part_number'] ."</span>";      
                    $tempArray[] = $row['quantity'];
                    if (!is_null($row['received_defective_part_date'])) {
                         $tempString2 =   date("d-m-Y", strtotime($row['received_defective_part_date']));
                    }
                    $tempArray[] = $tempString2;
                    $tempArray[] = $row['awb_by_partner'];
                    $tempArray[] = $row['courier_name_by_partner'];
                    if(!empty($row['partner_challan_file'])) {
                         $tempString ='<a href="https://s3.amazonaws.com/'.BITBUCKET_DIRECTORY.'/vendor-partner-docs/'.$row['partner_challan_file'].' target="_blank">'.$row['partner_challan_number'].'</a>';
                    }
                     else if(!empty($row['partner_challan_number'])) {
                          $tempString =  $row['partner_challan_number'];
                    }
                    $tempArray[] = $tempString;
                    $tempArray[] = $row['remarks_defective_part_by_sf'];
                    $finalArray[] = $tempArray;
           }
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $bookingCount,
            "recordsFiltered" =>  $bookingCount,
            "data" => $finalArray,
        );
        echo json_encode($output);
    }
     
    /**
     * @desc: This is used to show the partner contract list
     * @param void
     * @return void
     */
    function show_contract_list(){
        $select = 'partners.public_name, collateral.file, collateral_type.collateral_tag, collateral.document_description, collateral.start_date, collateral.end_date, collateral_type.collateral_type';
        $join['collateral'] = 'collateral.entity_id = partners.id AND collateral.entity_type = "partner" AND start_date <= "'.date("Y-m-d").'" AND end_date >= "'.date("Y-m-d").'"';
        $join['collateral_type'] = 'collateral_type.id = collateral.collateral_id AND collateral_type.collateral_tag = "Contract"';
        
        $data['data'] = $this->partner_model->get_partner_contract_detail($select, array('is_active'=>1), $join, 'left');
        
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/show_contract_list', $data);
    }
    
 
    function update_spare_estimate_quote(){
        $response = $unit_response = $booking_response = FALSE;
        $booking_id = $this->input->post("booking_id");
        $vendor_id = $this->input->post("vendor_id");
        $amount_due = $this->input->post("amount_due");
        $spare_id = $this->input->post("spare_id");
        $updated_price = $this->input->post("updated_price");
        $partner_id = $this->input->post("partner_id");
        $agent_id = $this->input->post("agent_id");
        $booking_unit_id = $this->input->post("booking_unit_id");
        if($spare_id && $booking_unit_id && $booking_id && $updated_price && $vendor_id && $partner_id){
            //Update Spare Table
            $where = array('id' => $spare_id);
            $gst_rate1 = $this->input->post('gst_rate');
            $spare_data = $this->partner_model->get_spare_parts_by_any('parts_requested_type, booking_details.service_id, requested_inventory_id', array('spare_parts_details.id' => $spare_id), true);
            
            $margin = $this->inventory_model->get_oow_margin($spare_data[0]['requested_inventory_id'], array('part_type' => $spare_data[0]['parts_requested_type'],
                    'service_id' => $spare_data[0]['service_id']));
               
            $spare_oow_est_margin = $margin['oow_est_margin']/100;
            $repair_oow_vendor_percentage = $margin['oow_vendor_margin'];
            //$gst_rate = !(empty($margin['gst_rate']))?$margin['gst_rate']: $gst_rate1;

            $data['purchase_price'] = $updated_price;
            $data['sell_price'] = ($updated_price + $updated_price * $spare_oow_est_margin );
            $data['estimate_cost_given_date'] = date('Y-m-d');
            $response = $this->service_centers_model->update_spare_parts($where, $data);
            if ($response) {
                //Update Booking_unit_details_table
                $unit['vendor_basic_percentage'] = ($updated_price * $repair_oow_vendor_percentage)/$data['sell_price'];
                $unit['customer_total'] = $data['sell_price'];
                $unit['ud_update_date'] = date("Y-m-d H:i:s");
                $unit_where = array('id' => $booking_unit_id);
                $unit_response = $this->booking_model->update_booking_unit_details_by_any($unit_where,$unit);
            }
            if($unit_response){
                //Update Booking_details table
                $booking['amount_due'] = ($amount_due + $data['sell_price']);
                $booking_response = $this->booking_model->update_booking($booking_id, $booking);
            }
            if($booking_response){
                //Update Booking_History Table
                if($this->session->userdata('partner_id')){
                    $this->notify->insert_state_change($booking_id, SPARE_OOW_EST_UPDATED, SPARE_OOW_EST_GIVEN, "UPDATED Price - ".$updated_price, $agent_id, "", $actor,$next_action,$partner_id);
                }else if($this->session->userdata('service_center_id')){
                    $this->notify->insert_state_change($booking_id, SPARE_OOW_EST_UPDATED, SPARE_OOW_EST_GIVEN, "UPDATED Price - ".$updated_price, $agent_id, "", $actor,$next_action,NULL,$this->session->userdata('service_center_id'));
                } else {
                    $this->notify->insert_state_change($booking_id, SPARE_OOW_EST_UPDATED, SPARE_OOW_EST_GIVEN, "UPDATED Price - ".$updated_price, _247AROUND_DEFAULT_AGENT, "", $actor,$next_action, _247AROUND);
                }
            }
            if($response && $unit_response && $booking_response){
                //Update Job Card
                $this->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);
                //Send Price Updation Email
                $template = $this->booking_model->get_booking_email_template("oow_estimate_updated");
                if (!empty($template)) {
                    $to = "";
                    $am_data = $this->miscelleneous->get_am_data($partner_id);
                    if(!empty($am_data)){
                        $to = $am_data[0]['official_email'];
                    }
                    $rm_details = $this->vendor_model->get_rm_sf_relation_by_sf_id($vendor_id);
                    if(!empty($rm_details)){
                        $to = (!empty($to))? $to.", ".$rm_details[0]['official_email']: $rm_details[0]['official_email'];
                    }
                    if (!empty($to)) {
                        $to = $am_data[0]['official_email'];
                        $subject = vsprintf($template[4], $booking_id);
                        $emailBody = vsprintf($template[0], $updated_price);
                        $this->notify->sendEmail($template[2], $to, $template[3], '', $subject, $emailBody, "",'oow_estimate_updated', "", $booking_id);
                    }
                }
                return true;
            }
            else{
                return false;
            }
        }
    }
    
    /*
     * @desc - This function is used to save bank detail for partner
     * @param - form post
     * @retun - void
     */
    function process_add_bank_detail_details(){
        $check_file = '';
        $this->form_validation->set_rules('bank_name', 'bank_name', 'required|trim');
        $this->form_validation->set_rules('account_type','account_type', 'required|trim');
        $this->form_validation->set_rules('account_number', 'account_number','required|trim');
        $this->form_validation->set_rules('ifsc_code', 'ifsc_code', 'required|trim');
        $this->form_validation->set_rules('beneficiary_name', 'beneficiary_name','required|trim');
        if ($this->form_validation->run() == TRUE) { 
            //Processing cancelled check file
            if (($_FILES['cancelled_cheque_file']['error'] != 4) && !empty($_FILES['cancelled_cheque_file']['tmp_name'])) {
                $tmpFile = $_FILES['cancelled_cheque_file']['tmp_name'];
                $check_file = "Partner-" . preg_replace('/\s+/', '', strtolower($this->input->post('partner_id'))) . '-CANCELLED-CHECK' . "." . explode(".", $_FILES['cancelled_cheque_file']['name'])[1];
                move_uploaded_file($tmpFile, TMP_FOLDER . $check_file);

                //Upload files to AWS
                $bucket = BITBUCKET_DIRECTORY;
                $directory_xls = "vendor-partner-docs/" . $check_file;
                $this->s3->putObjectFile(TMP_FOLDER . $check_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                unlink(TMP_FOLDER . $check_file);

                //Logging success for file uppload
                log_message('info', __FUNCTION__ . ' CHECK FILE is being uploaded sucessfully.');
            } 
            $bank_data = array(
                'entity_id' => $this->input->post('partner_id'),
                'entity_type' => _247AROUND_PARTNER_STRING,
                'bank_name' => $this->input->post('bank_name'),
                'account_type' => $this->input->post('account_type'),
                'bank_account' => $this->input->post('account_number'),
                'ifsc_code' => $this->input->post('ifsc_code'),
                'cancelled_cheque_file' => $check_file,
                'beneficiary_name' => $this->input->post('beneficiary_name'),
                'ifsc_code_api_response' => $this->input->post('ifsc_validation'),
                'agent_id' => $this->session->userdata('id'),
                'is_active' => '0'
            );
            if($this->input->post('BD_action') > 0 && $this->input->post('BD_action') != NULL){
                unset($bank_data['is_active']);
                if(!$check_file){
                    unset($bank_data['cancelled_cheque_file']);   
                }
                $action = $this->reusable_model->update_table('account_holders_bank_details', $bank_data, array('id'=>$this->input->post('BD_action')));
                $msg = "Data Updated Successfully";
            }
            else{
                $action = $this->reusable_model->insert_into_table('account_holders_bank_details', $bank_data);
                $msg = "Data Entered Successfully";
            }
            if($action){
                log_message("info", __METHOD__ .$msg);
                $this->session->set_userdata('success', 'Data Entered Successfully');
                redirect(base_url() . 'employee/partner/editpartner/' . $this->input->post('partner_id'));
            } else {
                log_message("info", __METHOD__ . " Error in adding details");
                $this->session->set_userdata('failed', 'Data can not be inserted. Please Try Again...');
                redirect(base_url() . 'employee/partner/editpartner/' . $this->input->post('partner_id'));
            }
        }else{
            $this->session->set_userdata('error', 'Please Fill All Bank Detail');
            redirect(base_url() . 'employee/partner/editpartner/' . $this->input->post('partner_id'));
        } 
    }
    
    
     /*
     * @desc - This function is used to Active/Inactive bank detail for partner(only one bank detail active at a time)
     * @param - form post
     * @retun - void
     */
    function process_active_inactive_bank_detail(){
        if($this->input->post('is_active') == 0){
            if(!empty($this->input->post('partner_id'))){
                $this->reusable_model->update_table('account_holders_bank_details', array('is_active'=> 0), array('entity_id'=>$this->input->post('partner_id')));
                $update = $this->reusable_model->update_table('account_holders_bank_details', array('is_active'=> 1), array('id'=>$this->input->post('id')));  
            }
        }
        else{
            if(!empty($this->input->post('partner_id'))){
                $update = $this->reusable_model->update_table('account_holders_bank_details', array('is_active'=> 0), array('id'=>$this->input->post('id'))); 
            }
        }
        if($update){
            $this->session->set_userdata('success', 'Bank Data Updated Successfully');
            redirect(base_url() . 'employee/partner/editpartner/' . $this->input->post('partner_id'));
        }
        else{
            $this->session->set_userdata('failed', 'Data can not be updated. Please Try Again...');
            redirect(base_url() . 'employee/partner/editpartner/' . $this->input->post('partner_id'));
        }
    }
    /*
     * This function extracts channels list and partner name from database and loads it to the view in tabular format.
     */
    public function get_channels(){
        $select = 'partner_channel.*,partners.public_name';
        $fetch_data = $this->partner_model->get_channels($select);
        
        $this->miscelleneous->load_nav_header();
        $this->load->view("employee/get_channel_list", array('fetch_data' => $fetch_data));
    }
    
    public function get_partner_channel() {
         log_message('info', __FUNCTION__ . print_r($_POST, true));
        $select = 'partner_channel.id, partner_channel.channel_name';
        $is_repeat = $this->input->post('is_repeat');
        if(!empty($this->input->post('partner_id'))){ 
            $where = array(
                'partner_id = "'.$this->input->post('partner_id').'" OR is_default = 1'=>NULL
            );
            
        }
        else{
            $where = array('is_default' => 1);
        }
        
        $channel = $this->input->post('channel');
        $fetch_data = $this->partner_model->get_channels($select, $where);
        $html = '<option value="" selected disabled>Please select seller channel</option>';
        foreach ($fetch_data as $key => $value) {
           $html .= '<option ';
           if($channel ==$value['channel_name'] ){
               $html .= " selected ";
           }
           else{
               if($is_repeat){
                    $html .= " disabled ";
               }
           }
           $html .=' >'.$value['channel_name'].'</option>'; 
        }
        echo $html;
    }
    
   /*
    * This function displays channel form in the browser.
    */
    public function add_channel(){ 
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/channel_form');
    }
    /*
     * This performs the process of adding channels to the form and submiting it to the database table.
     */
    public function process_add_channel(){
        $this->form_validation->set_rules('channel','Channel','required');
        $is_default = 0;
        if ($this->form_validation->run() == FALSE) {
            $this->add_channel();
        } else {
            $channel = $this->input->post("channel");
            if($this->input->post("partner_id") === 'All'){
                $partner_id = NULL;
                $is_default = 1;
            }
            else{
                $partner_id = $this->input->post("partner_id");
            }
            $data = array(
                'channel_name' => $channel,
                'partner_id' => $partner_id
                );
          
            $is_exist = $this->partner_model->get_channels('*', $data);
            
            if (empty($is_exist)) {
                $data['create_date'] = date('Y-m-d H:i:s');
                $data['is_default'] = $is_default;
                $channel_id = $this->partner_model->insert_new_channels($data);
                if ($channel_id){
                    $output = "Your data inserted successfully";
                    $userSession = array('success' => $output);
                    $this->session->set_userdata($userSession);
                    redirect(base_url() . "employee/partner/add_channel");
                }else{
                    $output = "Failed! Data did not insert";
                    $userSession = array('error' => $output);
                    $this->session->set_userdata($userSession);
                    redirect(base_url() . "employee/partner/add_channel");
                }
            }
            else {
                $output = "This Data already exist";
                $userSession = array('error' => $output);
                $this->session->set_userdata($userSession);
                redirect(base_url() . "employee/partner/add_channel");
            }
    }
}
    /*
     * This function loads the tabular format of the view of update channel form 
     */
    function update_channel($id) {
        $data = array(
            'partner_channel.id' => $id
        );
        
        $channel['fetch_data'] = $this->partner_model->get_channels(' partner_channel.* ', $data);
        //print_r($channel); die();
      
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/update_channel', $channel);
        
       
    }
    /*
     * This function supports in performing update functionalties to the form and further submiting it to the database. 
     */
    function process_update_channel($id) {
        $this->form_validation->set_rules('channel', 'Channel', 'required');
        $is_default = 0;
        if ($this->form_validation->run() == FALSE) {
            $this->update_channel($id);
        } else {
            $channel = $this->input->post("channel");
            if($this->input->post("partner_id") === 'All'){
                $partner_id = NULL;
                $is_default = 1;
            }
            else{
                $partner_id = $this->input->post("partner_id");
            }
            $data = array(
                'channel_name' => $channel,
                'partner_id' => $partner_id
            );
            $is_exist = $this->partner_model->get_channels('partner_channel.id', $data);
            if (empty($is_exist)) {
                $data['update_date'] = date('Y-m-d H:i:s');
                $data['is_default'] = $is_default;
                $status = $this->partner_model->update_channel($id, $data);
                if ($status) {
                    $output = "Your data updated successfully";
                    $userSession = array('success' => $output);
                    $this->session->set_userdata($userSession);
                    redirect(base_url() . "employee/partner/update_channel/" . $id);
                } else {
                    $output = "Failed! Data did not update";
                    $userSession = array('error' => $output);
                    $this->session->set_userdata($userSession);
                    redirect(base_url() . "employee/partner/update_channel/" . $id);
                }
            }  else {
                    $output = "This Data already exist";
                    $userSession = array('error' => $output);
                    $this->session->set_userdata($userSession);
                    redirect(base_url() . "employee/partner/update_channel/" . $id);
                }
            }
        }
        /**
     *  @desc : This function is to create Repeat booking
     *  We have already made a function to get_edit_booking_form, this method use that function to insert booking by parent booking
     *  @param : Parent booking ID
     */
    function get_repeat_booking_form($booking_id) {
         log_message('info', __FUNCTION__ . " Booking ID  " . print_r($booking_id, true));
        $openBookings = $this->reusable_model->get_search_result_data("booking_details","booking_id",array("parent_booking"=>$booking_id,  "current_status not in ('Cancelled','Completed') " =>NULL),NULL,NULL,NULL,NULL,NULL,array());
        if(empty($openBookings)){
            $this->get_editbooking_form($booking_id,"Repeat");
        }
        else{
            echo "<p style= 'text-align: center;background: #f35b5b;color: white;font-size: 20px;'>There is an open Repeat booking (".$openBookings[0]['booking_id'].") for ".$booking_id." , Until repeat booking is not closed you can not create new repeat booking</p>";
        }
    }
    function get_booking_relatives($booking_id){
        $relativeData = $this->booking_model->get_parent_child_sibling_bookings($booking_id);
        if(!empty($relativeData)){
            echo  json_encode($relativeData[0]);
        }
        else{
            echo false;
        }
    }
 
     /*
     * @desc - This function is used to get the list of service centers by state.
     * @param - get post state val
     * @retun - Json
     */
       
     function get_state_waise_service_centers() { 
         $state = $this->input->post("state");
         if (!empty($state)) {
             $where = array('state' => $state);
             $select = "service_centres.id,service_centres.name,service_centres.state";
             $service_centres_list = $this->vendor_model->getVendorDetails($select, $where, 'state', '');
             if (!empty($service_centres_list)) {
                 echo json_encode($service_centres_list);
             } else {
                 echo json_encode(array('status' => 'fail'));

             }

         }
     
    }
    /*
     * @desc - This function is used to get partner service center where partner serivce valum high.
     * @param - get post multiple parameter
     * @render on same pages
     */    
    function process_partner_warehouse_config() {
        $partner_id = $this->input->post('partner_id');
        $micro = $this->input->post('micro');
        $is_micro_wh = $this->input->post('is_micro_wh');
        $is_defective_part_return_wh = $this->input->post('is_defective_part_return_wh');
        if ($is_micro_wh == 1) {
            foreach ($micro as $key => $value) {
                $data = array(
                    'partner_id' => $partner_id,
                    'state' => $value['micro_wh_state'],
                    'micro_warehouse_charges' => $value['sf_amount']
                );
                $wh_on_of_data = array(
                    'partner_id' => $partner_id,
                    'agent_id' => $this->session->userdata('id'),
                    'active' => 1
                );
                foreach ($value['sf_id'] as $vendor_id) {
                    $data['vendor_id'] = $vendor_id;
                    $wh_on_of_data['vendor_id'] = $vendor_id;
                    $this->miscelleneous->create_micro_warehouse($data, $wh_on_of_data);
                }
            }
            $partner = array(
                'is_micro_wh' => 1,
                'is_defective_part_return_wh' => $is_defective_part_return_wh
            );
            $this->partner_model->edit_partner($partner, $partner_id);
        }
        redirect(base_url() . 'employee/partner/editpartner/' . $partner_id);
    }

    /*
     * @desc - This function is used to activate and deactivate micro warehouse
     * @param -  
     * @render 
     */ 
    
    function manage_micro_warehouse_by_status() {
        $micro_wh_mp_id = $this->input->post('micro_wh_mp_id');
        $wh_on_of_id = $this->input->post('wh_on_of_id');
        $active_status = $this->input->post('active_status');        
        if (!empty($micro_wh_mp_id)) {
            $return_type = $this->inventory_model->manage_micro_wh_from_list_by_id($micro_wh_mp_id, $active_status);
            if (!empty($return_type)) {
                $where = array('m.id' => $wh_on_of_id);
                $warehouse_on_off_list = $this->inventory_model->get_warehouse_on_of_status_list($where, 'w_on_off.partner_id,w_on_off.vendor_id');
                if (!empty($warehouse_on_off_list)) {                   
                    $wh_on_of_data = $warehouse_on_off_list[0];
                    $wh_on_of_data['active'] = $active_status; 
                    $wh_on_of_data['agent_id'] = $this->session->userdata('id');
                    $inserted_id = $this->inventory_model->insert_query('warehouse_on_of_status', $wh_on_of_data);
                    if (!empty($inserted_id)) {
                        echo json_encode(array('status' => 'success'));
                    } else {
                        echo json_encode(array('status' => 'failed'));
                    }
                }
            }
        }
    }
    
     /*
     * @desc - This function is used to deactivate brand collateral
     */ 
    function deactivate_brand_collateral(){
         $collateralID = $this->input->post('collateral_id');
         $explode_collataralID=explode(',',$collateralID);
         foreach($explode_collataralID as $value)
            {
               if(!empty($value))
                {
                        $explode_arr=explode('+',$value);
                        $collateral_id=$explode_arr[0];
                        $service_id=$explode_arr[1];
                        $brand_name=$explode_arr[2];
                        $explode_array[]=array(
                            'collateral_id'=>$collateral_id,
                            'brand'=>$brand_name,
                            'appliance_id'=>$service_id
                        );
                }
            }
           
            if($explode_array){
            $affected_rows = $this->partner_model->deactivate_collateral($explode_array);
             if($affected_rows){
                echo "Collateral has been deactivated successfully";
            }
        }
        else{
            echo "Something Went Wrong Please Try Again";
        }
    }

    function resend_login_details($agentID){
        log_message('info', __FUNCTION__ . " Agent ID  " . $agentID);
        $agentLoginDetails = $this->partner_model->get_login_details($agentID);
        $login_template = $this->booking_model->get_booking_email_template("resend_partner_login_details");
        if (!empty($login_template)) {
            $login_email['username'] = $agentLoginDetails[0]['user_id'];
            $login_email['password'] = $agentLoginDetails[0]['clear_password'];
            $cc = $login_template[3];
            $bcc = $login_template[5];
            $login_subject = $login_template[4];
            $login_emailBody = vsprintf($login_template[0], $login_email);
            $this->notify->sendEmail($login_template[2], $agentLoginDetails[0]['email'], $cc, $bcc,$login_subject, $login_emailBody, "",'resend_partner_login_details');
            log_message('info', __FUNCTION__ . " Email Send successfully" . $login_emailBody);
            echo "Details Sent Successfully";
         } 
         else {
               echo "Something Went Wrong Please Try Again";
               log_message('info', __FUNCTION__ . " Template Not Available ");
          }
    }
    function activate_deactivate_contacts($contactID,$action){
        if($contactID){
            $affected_rows =  $this->partner_model->activate_deactivate_contact_person($contactID,$action);
            if($affected_rows){
                 $v = "Deactivated";
                if($action){
                    $v = "Activated";
                }
                if($this->session->userdata('userType') == 'employee'){
                    $agent = $this->session->userdata('id');
                    $agentName = $this->session->userdata('emp_name');
                    $partner_id = _247AROUND;
                }
                else{
                    $agent = $this->session->userdata('agent_id');
                    $agentName = $this->session->userdata('partner_name');
                    $partner_id = $this->session->userdata('partner_id');
                }
                $this->notify->insert_state_change($contactID, "Contact Person - ".$v,"Contact Person", $contactID." has been ".$v, $agent, $agentName, 
                        'not_define','not_define',$partner_id);
                echo "Successfully Done";
            }
            else{
                echo "Something Went Wrong Please Try Again";
            }
        }
    }

    function get_posible_parent_id(){
        $this->miscelleneous->get_posible_parent_booking();
    }
    function process_partner_sample_no_pic()
    {
        $partner_id=$this->input->post('partner_id');
        $errormsg="";
        $allowed_filetype=array('image/jpeg',
        'image/jpg',
        'image/gif',
        'image/png');
        $allowed_size=2097152;
         if(isset($_FILES))
        {
            $sample_no_pic=$_FILES['SamplePicfile'];
            $cpt = count($_FILES['SamplePicfile']['name']);
            $sample_no_pic_array=array();
            for($i=0; $i<$cpt; $i++)
                {   
                    $_FILE['SamplePicfile']['name']= $_FILES['SamplePicfile']['name'][$i];
                    $_FILE['SamplePicfile']['type']= $_FILES['SamplePicfile']['type'][$i];
                    $_FILE['SamplePicfile']['tmp_name']= $_FILES['SamplePicfile']['tmp_name'][$i];
                    $_FILE['SamplePicfile']['error']= $_FILES['SamplePicfile']['error'][$i];
                    $_FILE['SamplePicfile']['size']= $_FILES['SamplePicfile']['size'][$i];    
                    $size=$_FILE['SamplePicfile']['size'];
                    $name=$_FILE['SamplePicfile']['name'];
                    $type=$_FILE['SamplePicfile']['type'];
                    //Processing Sample Pic File
                   
                    if (($_FILE['SamplePicfile']['error'] != 4) && !empty($_FILE['SamplePicfile']['tmp_name'])) 
                        {
                        if(in_array($type,$allowed_filetype) && ($size<=$allowed_size))
                          {
                                    $tmpFile = $_FILE['SamplePicfile']['tmp_name'];
                                    $extension=explode(".", $_FILE['SamplePicfile']['name'])[1];
                                    $sample_file = "sample_number_pic_".$partner_id.'_'. rand(10, 100) . "." . $extension;
                                    move_uploaded_file($tmpFile, TMP_FOLDER . $sample_file);

                                    //Upload files to AWS
                                    $bucket = BITBUCKET_DIRECTORY;
                                    $directory_xls = "vendor-partner-docs/" . $sample_file;
                                    $this->s3->putObjectFile(TMP_FOLDER . $sample_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                                    $data=array(
                                    'partner_id'=>$partner_id,
                                    'sample_no_pic'=>$sample_file,
                                    'created_date'=>date('Y-m-d'),
                                    'active'=>'1'
                                    );

                                  //update sample_no_pic
                                    $sample_pic_id = $this->partner_model->insert_sample_no_pic($data);

                                    $attachment_sample_no_pic = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $sample_file;
                                    unlink(TMP_FOLDER . $sample_file);

                                    //Logging success for file uppload
                                    log_message('info', __FUNCTION__ . ' SampleNoPicture is being uploaded sucessfully.');
                          }
                          else
                          {
                              $errormsg=$errormsg.$name.'  File should have jpeg,png,jpg type and size should be less than 2 MB.  ';
                          }
                      }
                       else
                      {
                          $errormsg=$errormsg.$name.'  The uploaded file exceeds the upload_max_filesize.  ';
                      }
                   
                }
                if(!empty($errormsg))
                {
                    $this->session->set_userdata('error', $errormsg);
                }
                      $msg = "Partner Sample Pic has been updated successfully";
                       $this->session->set_userdata('success', $msg);
                  redirect(base_url() . 'employee/partner/editpartner/' . $partner_id);

        }
        
    }
    public function deletePartnerSampleNo()
    {
        $id=$this->input->post('id');
        $partner_id=$this->input->post('partner_id');
        $data=array('active'=>'0');
        $where=array('id'=>$id);
        $data=$this->reusable_model->update_table('partner_sample_no_picture',$data,$where);
        if($data>0)
        {
           $msg = "Partner Sample Pic has been Deleted successfully";
          
        }
        else
        {
            $msg = "Partner Sample Pic has not been Deleted successfully";
           
        }
        echo $msg;
        
    }
    /**
     * @desc: This function is used to show the appliance model mapping of the partner
     * @params: void
     * @return: void
     */
    function show_appliance_model_mapping(){
        $this->checkUserSession();
        $this->miscelleneous->load_partner_nav_header();
        $this->load->view('partner/show_appliance_model_mapping');
        $this->load->view('partner/partner_footer');
    }
    
    public function brandCollateral()
    {
        $partnerArray = array();
        $this->miscelleneous->load_nav_header();
        $partners = $this->partner_model->getpartner();
        foreach($partners as $partnersDetails){
            $partnerArray[$partnersDetails['id']] = $partnersDetails['public_name'];
        }        
        $this->load->view('partner/brand_collateral_partner_filter',array("partnerArray"=>$partnerArray));
       
    }
    public function brandCollateralPartner()
    {
       $coloumnarr=array('sno','`collateral_type`.`collateral_type`','`services`.`services`','`collateral`.`brand`','`collateral`.`request_type`','file','`collateral`.`document_description`','delete','date');
       $receieved_Data = $this->get_post_data();
       $new_receieved_Data = $this->get_brand_partner_filtered_data($receieved_Data);
       if(!empty($new_receieved_Data['order']))
       {
            $order=$new_receieved_Data['order'];
            $column_sort=$order['0']['column'];
            $sort_type=$order['0']['dir'];
            if(!empty($coloumnarr))
            {
                $order_by_column=$coloumnarr[$column_sort];
                $sorting_type=$sort_type;
            }
       }
       else
       {
           $order_by_column='collateral.id';
           $sorting_type='ASC';
       }
       $results['partner_contracts'] = $this->partner_model->get_brand_collateral_data($new_receieved_Data,$order_by_column,$sorting_type);
       $data=array();
       $result_final=$results['partner_contracts'];
       $count=count($result_final);
       $no = $new_receieved_Data['start'];
       if(!empty($results['partner_contracts']))
       {
            foreach ($results['partner_contracts'] as $filter_result) {
                 $no++;
                 $row = $this->get_brand_partner_filter($filter_result,$no);
                 $data[] = $row;
             }
       }
       $output = array(
            "draw" => $new_receieved_Data['draw'],
            "recordsTotal" => $count,
            "recordsFiltered" => $count,
            "data" => $data,
            
        );
        echo json_encode($output);
    }
    
    function get_post_data(){
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
        $post['status'] = $this->input->post('status');
        $post['partner_id'] = $this->input->post('partner_id');
        $post['service_id']=$this->input->post('service_id');
        $post['brand']=$this->input->post('brand');
        $post['model']=$this->input->post('model');
        $post['request_type']=$this->input->post('request_type');

        return $post;
    }
    
    function get_brand_partner_filtered_data($data){
        $id = $data['partner_id'];
        $service_id=$data['service_id'];
        $brand=$data['brand'];
        $request_type=$data['request_type'];
        $model=$data['model'];
        
        if(!empty($id)){
            $data['where']['collateral.entity_id'] =  $id;
        }
        if(!empty($service_id))
            $data['where']['collateral.appliance_id'] =  $service_id;
        if(!empty($brand))
            $data['where']['collateral.brand'] =  $brand;
        if(!empty($request_type))
            $data['where_in']['request_type'] =  $request_type;
        if(!empty($model))
            $data['where']['collateral.model'] =  $model;
        
        $data['column_order'] = array(NULL,'collateral_type','model','category', 'capacity',NULL, NULL,'start_date');
        $data['column_search'] = array('collateral_type','model','category', 'capacity','document_description');
        
        return $data;
    }
    
    public function get_brand_partner_filter($filter_result,$no)
    {
        $row=array();
//        if($filter_result['collateral_tag'] == LEARNING_DOCUMENT)
//            {
              if($filter_result['is_file'])
                  {
                      $url = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/".$filter_result['file'];
                  }
                  else
                  {
                         $url = $filter_result['file'];
                  }
               $row[]=$no;                
               $row[]=$filter_result['collateral_type'];
               $row[]= $filter_result['model'];
               $row[]=$filter_result['category'] ;
               $row[]=$filter_result['capacity'];
               $row[]=$this->miscelleneous->get_reader_by_file_type($filter_result['document_type'],$url,"200");
               $row[]=$filter_result['document_description'];
              // $row[]="<div class='checkbox'><input type='checkbox' name='coll_id[]' value='". $filter_result['id']."'> </div>";
               $row[]=date('d-m-Y',strtotime($filter_result['start_date']));
          //  }
            
            return $row;
           
    }
    
    /**
     * @desc: This function is used to tag margin on spare parts
     * @params: void
     * @return: void
     */
    function process_to_tag_marging_on_spare_parts() {
        log_message('info', __FUNCTION__ . " Margin of Spare Parts " . json_encode($_POST));
        $partner_id = $this->input->post('partner_id');
        $part = $this->input->post('part');
        $around_margin = $part[0]['oow_around_margin'];
        $vendor_margin = $part[0]['oow_vendor_margin'];

        if (!empty($part) && (!empty($around_margin) && $around_margin > 0 && $around_margin <= 30 ) &&
                (!empty($vendor_margin) && $vendor_margin > 0 && $vendor_margin <= 15 ) &&
                ($around_margin >= $vendor_margin)) {
            $flag = false;
            foreach ($part as $key => $parts_deails) {
                $oow_around_margin = $parts_deails['oow_around_margin'];
                $oow_vendor_margin = $parts_deails['oow_vendor_margin'];
                $parts_type_list = $parts_deails['parts_type'];

                if (!empty($parts_type_list)) {
                    $parts_type_ids = implode(',', $parts_type_list);
                    $select = 'set_oow_part_type_margin.partner_id,set_oow_part_type_margin.part_type_id';
                    $oow_part_type_margin_list = $this->inventory_model->get_oow_part_type_margin_details($select, array('partner_id' => $partner_id), array('part_type_id' => $parts_type_ids));

                    if (empty($oow_part_type_margin_list)) {
                        foreach ($parts_type_list as $key => $part_type_id) {
                            $data['partner_id'] = $partner_id;
                            $data['oow_around_margin'] = $oow_around_margin;
                            $data['oow_vendor_margin'] = $oow_vendor_margin;
                            $data['part_type_id'] = $part_type_id;

                            $last_insert_id = $this->inventory_model->insert_query('set_oow_part_type_margin', $data);
                            if ($last_insert_id) {
                                $flag = true;
                            }
                        }
                    } else {
                        $this->session->set_userdata(array('error' => 'Duplicate entry'));
                        redirect(base_url() . 'employee/partner/editpartner/' . $partner_id);
                    }
                } else {
                    $this->session->set_userdata(array('error' => 'Please Fill Form Details Properly.'));
                }
            }

            if ($flag) {
                $this->session->set_userdata(array('success' => 'Successfuly Inserted.'));
                redirect(base_url() . 'employee/partner/editpartner/' . $partner_id);
            }
        } else {
            $this->session->set_userdata(array('error' => 'Please Check Around Margin and Vendor Margin.'));
            redirect(base_url() . 'employee/partner/editpartner/' . $partner_id);
        }
    }

    /* @desc: This method is used to load view for setting logo priority on web site
     * @param: void
     * @return:view
     */
    function partner_logo_priority(){
        $data['data'] = $this->reusable_model->get_search_query("partner_brand_logo", "partner_brand_logo.id, partners.public_name, partner_logo", array(), array("partners"=>"partners.id = partner_brand_logo.partner_id"), "", array("logo_priority"=>"ASC"), "", "")->result_array();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/partner_brand_logo_priority_form', $data);
    }
    
    /**
     * @desc: This method is used to save the logo priority in partner_brand_logo table
     * @param: void
     * @return:json
     */
    function save_partner_logo_priority(){
        $priority_array = $this->input->post("priority_array");
        $return = array();
        $queries = array();
        foreach ($priority_array as $key => $value) {
            $queries[] = "Update partner_brand_logo set logo_priority = '".$value['priority']."' where id = '".$value['partner_brand_logo_id']."'";
        }
        if(!empty($queries)){
            $rows =  $this->partner_model->update_partner_brand_logo($queries);
            if($rows){
               $return['status'] = true;
               $return['message'] = "Priority Saved Successfully";
            }
            else{
                $return['status'] = false;
                $return['message'] = "Priority Not Saved, Contact Tech Team";
            }
        }
        else{
            $return['status'] = false;
            $return['message'] = "Priority Not Saved, Contact Tech Team"; 
       }
        echo json_encode($return);
    }
    
    /*
    * @desc - This function is used to List the partner details 
    * @param - void    
    * @return - array
    */
    
    function partners_managed_by_account_manager(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/partners_list_managed_by_account_manager');
    }
    
    
    /**
     * @desc Get POST data from DataTable
     * @return Array
     */
    function getPartnerDataTablePost(){
        
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
       if(!empty($search['value'])){
           $post['search_value'] = trim($search['value']); 
        }
        $post['where']['partners.is_active'] = 1;
        if(!empty($this->input->post("group_by"))){
            $post['group_by'] = $this->input->post("group_by");
        }
        return $post;
    }
    
    
    /**
     * @desc This function is generalize used to get the data for partners datatable
     * @param request_type
     */
    function get_partners_searched_data(){
        log_message("info", __METHOD__);
        $post = $this->getPartnerDataTablePost();
        $post['column_order'] = array(NULL, 'employee.full_name','partners.public_name','agent_filters.state');
        $post['column_search'] = array('employee.full_name','partners.public_name','agent_filters.state');
        $data = array();
        
        switch ($this->input->post('request_type')){
            case 'partners_managed_by_account_manager':                  
                $data = $this->getPartnersManagedByAccountManagerData($post);
                break;           
            default :
               break; 
        }
        
       
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->partner_model->count_all_partners($post),
            "recordsFiltered" =>  $this->partner_model->count_filtered_partner('*', $post),
            "data" => $data,
        );
        
        echo json_encode($output);
        
    }    
     /**
     * @desc Filter Partner data 
     * @param type $post
     * @return type
     */
     
    function getPartnersManagedByAccountManagerData($post){
        $select = "partners.id as partner_id, partners.company_name, partners.public_name, partners.company_type, partners.address, partners.district, partners.state, partners.pincode,"
                . " partners.primary_contact_name, partners.primary_contact_email, partners.customer_care_contact, partners.pan, partners.gst_number, employee.full_name, employee.phone, "
                . "employee.official_email, agent_filters.state as am_state";
        $list = $this->partner_model->searchPartnersListData($select, $post);
        $no = $post['start'];
        $data = array();
        foreach ($list as $partners_list) {
            $no++;
            $row =  $this->Partners_datatable($partners_list, $no);
            $data[] = $row;
        }
        return $data;
    }
    
       /**
     * @desc This is used to generate Data table row
     * @param Array $invoice_list
     * @param int $no
     * @return Array
     */
    function Partners_datatable($partners_list, $no){
        $row = array();
         $row[] = $no;
         $row[] = $partners_list->full_name;
         $row[] = $partners_list->public_name;
         $row[] = $partners_list->am_state;
        return $row;
    }
    
     /**
     * @desc: This function is used to get partner whose booking file can be upload
     * @params: void
     * @return: string
     */
    function get_booking_file_upload_partner(){
        $where = array('is_active'=>1);
        $partner_list = $this->partner_model->get_booking_file_upload_partner($where);
        $option = '<option selected="" disabled="">Select Partner</option>';

        foreach ($partner_list as $value) {
            $option .= "<option value='" . $value['id'] . "'";
            if(count($partner_list) == 1){
                $option .= " selected>";
            }else{
               $option .= "> "; 
            }
            $option .= $value['public_name'] . "</option>";
        }
        echo $option;
    }
    
    /**
     * @desc: This function is used to get partner activation/deactivation history 
     * @params: partner_id
     * @return: JSON
     */
    function get_activation_deactivation_history(){
        $this->partner_id = trim($this->input->post('partner_id'));
        if(!empty($this->partner_id)){
            $data = $this->partner_model->get_activation_deactivation_history($this->partner_id);
            
            $arr[] = array('status'=>$data[0]['status'], 'date'=>date('d-m-Y H:i:s', strtotime($data[0]['date'])));
            $status = $data[0]['status'];
            
            foreach($data as $value) {
                if($value['status'] !== $status)
                {
                    $arr[] = array('status'=>$value['status'], 'date'=>date('d-m-Y H:i:s', strtotime($value['date'])));
                    $status = $value['status'];
                }
            }
            
            if(!empty($data)){
                $res['msg'] = 'success';
                $res['data'] = $arr;
            }else{
                $res['msg'] = 'failed';
                $res['data'] = 'No Data Found';
            }
        }else{
            $res['msg'] = 'failed';
            $res['data'] = 'No Data Found!';
        }
        
        echo json_encode($res);
    }


    /**
     * @desc: This function is used to show  history for parts send by partner to Sfs

     */
    function spare_shipped_history(){
        $this->miscelleneous->load_partner_nav_header();
        $this->load->view('partner/parts_send_by-partner_to_sf');
        $this->load->view('partner/partner_footer');

    }

    
    /**
     * @Desc: This function is used to get  Model for Partner for particular service_id  
     * This is being called from AJAX
     * @params: partner_id, service_name
     * $return: Json
     * 
     */
    function get_model_for_partner_service_wise() {
        $appliace_model = "";
        $partner_id = $this->input->post('partner_id');
        $service_id = $this->input->post('service_id');
        $where = array('entity_type' => 'partner', 'entity_id' => $partner_id, 'service_id' => $service_id, "active" => 1);
        $data = $this->partner_model->get_model_number_partner_service_wise($where);

        $option = "";
        foreach ($data as $value) {
            $option .= "<option ";
            if ($appliace_model == $value['model_number'] || count($data) == 1) {
                $option .= " selected ";
            }
            $option .= " value='" . $value['id'] . "'>" . $value['model_number'] . "</option>";
        }

        echo $option;
    }
    
    function old_summary_report_view(){
        $this->checkUserSession();
        $where['entity_type'] = 'Partner';
        $where['entity_id'] = $this->session->userdata('partner_id');
        $where['file_type'] = "Partner_Summary_Reports";
        $limitArray['length'] = 50;
        $limitArray['start'] = "";
        $join['partners'] = "partners.id = file_uploads.entity_id";
        $orderBYArray["file_uploads.create_date"] = "DESC";
        $data['summaryReports'] = $this->reusable_model->get_search_result_data("file_uploads","file_name,date(file_uploads.create_date) as date,partners.public_name",
                $where,$join,$limitArray,$orderBYArray,NULL,NULL,array());
        $this->miscelleneous->load_partner_nav_header();
        $this->load->view('partner/partner_summary_report_list', $data);
        $this->load->view('partner/partner_footer');
    }
    
    /**
     * @desc: This function is used to download serviceable BOM
     * @params: void
     * @return: void
     */
    function show_download_serviceable_bom(){
        $this->checkUserSession();
        $this->miscelleneous->load_partner_nav_header();
        $this->load->view('partner/get_download_serviceable_bom');
        $this->load->view('partner/partner_footer');
    }
    
    
     /**
     * @desc: This function is used to download serviceable BOM
     * @params: void
     * @return: void
     */
    function show_download_missing_serviceable_bom(){
        $this->checkUserSession();
        $this->miscelleneous->load_partner_nav_header();
        $this->load->view('partner/get_download_missing_serviceable_bom');
        $this->load->view('partner/partner_footer');
    }
     
    /**
     * @desc: This function is used to download Part Master
     * @params: void
     * @return: void
     */
    function show_download_part_master(){
        $this->checkUserSession();
        $this->miscelleneous->load_partner_nav_header();
        $this->load->view('partner/get_download_part_number');
        $this->load->view('partner/partner_footer');
    }
    
     /**
     * @desc: This function is used to download alternate parts for partner
     * @params: void
     * @return: void
     */
    function show_download_alternate_parts(){
        $this->checkUserSession();
        $this->miscelleneous->load_partner_nav_header();
        $this->load->view('partner/download_alternate_parts.php');
        $this->load->view('partner/partner_footer');
    }


    function get_nrn_approval(){

        $this->checkUserSession();
        $agent_id = $this->session->userdata('agent_id');
        if($this->session->userdata('is_filter_applicable') == 1){
            $data['states'] = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state_code.state) as state",array("agent_filters.agent_id"=>$agent_id),array("agent_filters"=>"agent_filters.state=state_code.state"),NULL,array('state'=>'ASC'),NULL,array("agent_filters"=>"left"),array());
        }
        else{
            $data['states'] = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state) as state",NULL,NULL,NULL,array('state'=>'ASC'),NULL,NULL,array());
        }
        $data['is_ajax'] = $this->input->post('is_ajax');
        if(empty($this->input->post('is_ajax'))){
            $this->miscelleneous->load_partner_nav_header();
            $this->load->view('partner/nrn_approval', $data);
            $this->load->view('partner/partner_footer');
        }else{
            $this->load->view('partner/nrn_approval', $data);
        }


    }



      function get_nrn_approval_table(){
      $agent_id = $this->session->userdata('agent_id');
      $finalArray = array();
      $postData = $this->input->post();
      $state = 0;
      $nrn =true;
      $columnMappingArray = array("column_1"=>"spare_parts_details.booking_id","column_3"=>"DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(date_of_request, '%Y-%m-%d'))",
          "column_4"=>"GROUP_CONCAT(DISTINCT spare_parts_details.parts_requested)","column_7"=>"booking_details.state");    
      $order['column'] =$columnMappingArray["column_3"];
      $order['sorting'] = "desc";
      // if(array_key_exists("order", $postData)){
      //       $order['column'] =$columnMappingArray["column_".$postData['order'][0]['column']];
      //       $order['sorting'] = $postData['order'][0]['dir'];
      //   }
       $partner_id = $this->session->userdata('partner_id');
       $where = "spare_parts_details.partner_id = '" . $partner_id . "' AND  spare_parts_details.entity_type =  '"._247AROUND_PARTNER_STRING."' AND status IN('".NRN_APPROVED_BY_PARTNER."') " 
                . " AND booking_details.current_status IN ('"._247AROUND_PENDING."', '"._247AROUND_RESCHEDULED."') "
                . " ";
       if($this->input->post('state')){
           $state = $this->input->post('state');
           $where = $where." AND booking_details.state = '$state'";
       }
       if($this->input->post('booking_id')){
           $booking_id = $this->input->post('booking_id');
           $where = $where." AND booking_details.booking_id = '$booking_id'";
       }
       if($this->session->userdata('is_filter_applicable') == 1){
            $state = 1;
            $where .= " AND booking_details.state IN (SELECT state FROM agent_filters WHERE agent_id = ".$agent_id." AND agent_filters.is_active=1)";
        }
        $select = "spare_nrn_approval.remark as nrn_remark,spare_nrn_approval.approval_file,spare_parts_details.booking_id,spare_parts_details.nrn_approv_by_partner,spare_parts_details.quantity,services.services, i.part_number, GROUP_CONCAT(DISTINCT spare_parts_details.parts_requested) as parts_requested, users.name, "
                . "booking_details.booking_primary_contact_no, booking_details.partner_id as booking_partner_id, booking_details.state, "
                . "booking_details.booking_address,booking_details.initial_booking_date, booking_details.is_upcountry, i.part_number, "
                . "booking_details.upcountry_paid_by_customer,booking_details.amount_due, booking_details.flat_upcountry,booking_details.state, service_centres.name as vendor_name, "
                . "service_centres.address, service_centres.state, service_centres.gst_no, service_centres.pincode, "
                . "service_centres.district,service_centres.id as sf_id,service_centres.is_gst_doc,service_centres.signature_file, "
                . "DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(date_of_request, '%Y-%m-%d')) AS age_of_request,"
                . " GROUP_CONCAT(DISTINCT spare_parts_details.model_number) as model_number, "
                . " GROUP_CONCAT(DISTINCT spare_parts_details.serial_number) as serial_number,"
                . " GROUP_CONCAT(DISTINCT spare_parts_details.remarks_by_sc) as remarks_by_sc, spare_parts_details.partner_id, "
                . " GROUP_CONCAT(DISTINCT spare_parts_details.id) as spare_id, serial_number_pic ";
         $bookingData = $this->service_centers_model->get_spare_parts_on_group($where, $select, "spare_parts_details.booking_id", false, $postData['length'], $postData['start'],0,$order,$nrn);
         $bookingCount = $this->service_centers_model->get_spare_parts_on_group($where, "count( Distinct spare_parts_details.booking_id) AS total_rows","spare_parts_details.booking_id", FALSE,-1,-1,1)[0]['total_rows'];
         $sn = $postData['start'];
         foreach ($bookingData as $key => $row) {
                    $tempArray = array();
                    $sn++;
                    $tempString = $tempString2 = $tempString3 = $tempString4 = $tempString5 ="";
                    if($row['is_upcountry'] == 1 && $row['upcountry_paid_by_customer'] == 0) {
                       $tempString = '<i style="color:red; font-size:20px;" onclick="open_upcountry_model('.$row['booking_id'].'", "'.$row['amount_due'].'", "'.$row['flat_upcountry'].')" class="fa fa-road" aria-hidden="true"></i>';
                    }
                    $tempArray[] =  $sn. $tempString;
                    $tempArray[] =  '<a target="_blank"  style="color:blue;" href='.base_url().'partner/booking_details/'.$row['booking_id'].'  title="View">'.$row['booking_id'].'</a>';
                    $tempArray[] =  $row['services'];
                    $tempArray[] =  $row['name'];
                    $tempArray[] =  $row['age_of_request'];
                    $tempArray[] =  "<span style='word-break: break-all;'>". $row['parts_requested'] ."</span>";
                    $tempArray[] =  "<span style='word-break: break-all;'>". $row['part_number'] ."</span>";
                    $tempArray[] =  $row['quantity'];
                    $tempArray[] =  $row['model_number'];
                    $tempArray[] =  $row['serial_number'];
                    $tempArray[] =  $row['state'];
                    $tempArray[] =  $row['remarks_by_sc'];
                    $tempArray[] =  $row['nrn_remark'];
                    if ($row['approval_file']=='0') {
                       $tempArray[] =  '<span style="color: red;font-size:40px;cursor: not-allowed;"><i class="fa fa-window-close" aria-hidden="true"></i></span>';
                    }else{
                     $tempArray[] =  '<a download  target="_blank" href='.S3_WEBSITE_URL.'nrn_approvals_files/'.$row['approval_file'].'  ><span style="color: #0ce10c;font-size:40px;"><i class="fa fa-download" aria-hidden="true"></i></span></a>';  
                    }
                    
                    $tempArray[] =  "<span class='btn btn-success approved_nrn_booking' data-booking_id='".$row['booking_id']."' ><i class='fa fa-check' aria-hidden='true'></i></span>";
                    
                    
                    $finalArray[] = $tempArray;
           }
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $bookingCount,
            "recordsFiltered" =>  $bookingCount,
            "data" => $finalArray,
        );
        echo json_encode($output);
    }




    function do_partner_nrn_approval(){

        $booking_id = trim($this->input->post('booking_id'));
        $partner_id = $this->session->userdata('partner_id');
        if(empty($partner_id)){
         $partner_id = $this->input->post('partner_id');   
        }
        $email="Not Given";
        $remarks = $this->input->post('remarks');
        if (isset($_POST['email']) && !empty($_POST['email'])) {
           $email = $this->input->post('email');
        }
      //  $allowedExts = array("PDF", "pdf",'jpg','jpeg','png','PNG',);
        $allowedExts = array("PDF", "pdf",'jpg','jpeg','png','PNG','docx','DOCX','doc','DOC');
        $approval_file_name = "Not Uploaded";
        if(isset($_FILES["approval_file"]) && !empty($_FILES["approval_file"])){
           $approval_file_name = $this->miscelleneous->upload_file_to_s3($_FILES["approval_file"], "nrn_approval", $allowedExts, $booking_id, "nrn_approvals_files", "incoming_approve_nrn");
        }

        $data_nrn = array(
            'booking_id'=>$booking_id,
            'email_to'=>trim($email),
            'approval_file'=>$approval_file_name,
            'remark'=>trim($remarks)
        );

 $where_shipped = array('booking_id' => trim($booking_id),'shipped_date IS NOT NULL'=>NULL);
 $check_shipped_status = $this->partner_model->get_spare_parts_by_any("*",$where_shipped);
        $response = $this->partner_model->insert_nrn_approval($data_nrn);
        if ($response  && empty($check_shipped_status)) {

            $select_invemtory = "partner_id,requested_inventory_id,quantity,booking_id,status,entity_type";
            $where_inventory = array('booking_id' => trim($booking_id),'entity_type'=>_247AROUND_SF_STRING,'status'=>SPARE_PARTS_REQUESTED);
            $spare_inventory_update = $this->partner_model->get_spare_parts_by_any($select_invemtory,$where_inventory);

            foreach ($spare_inventory_update as  $update_pending) {
                 
                $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $update_pending['partner_id'], $update_pending['requested_inventory_id'], -$update_pending['quantity']);
            }

        
                $where = array('booking_id' => trim($booking_id));
                $data = array(
                    'status'=>NRN_APPROVED_BY_PARTNER,
                    'nrn_approv_by_partner'=>1
                );
                $response = $this->service_centers_model->update_spare_parts($where, $data);

                    $booking['internal_status'] =NRN_APPROVED_BY_PARTNER;
                    $booking['current_status'] = _247AROUND_PENDING;
                    $actor="";
                    $next_action="";
                    $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING,NRN_APPROVED_BY_PARTNER, $partner_id, $booking_id);
                
                if (!empty($partner_status)) {
                    $booking['partner_current_status'] = $partner_status[0];
                    $booking['partner_internal_status'] = $partner_status[1];
                    $actor = $booking['actor'] = $partner_status[2];
                    $next_action = $booking['next_action'] = $partner_status[3];
                }
                $this->booking_model->update_booking($booking_id, $booking);

               $data_service_center=array(
                        'current_status'=>_247AROUND_PENDING,
                        'internal_status'=>NRN_APPROVED_BY_PARTNER
                );
               $this->vendor_model->update_service_center_action($booking_id, $data_service_center);

                $new_state=NRN_APPROVED_BY_PARTNER;
                    $this->notify->insert_state_change($booking_id, $new_state,SPARE_PART_ON_APPROVAL, NRN_TO_BE_SHIPPED_BY_PARTNER." - ".$remarks, $this->session->userdata('agent_id'), $this->session->userdata('partner_name'), $actor,$next_action, NRN_TO_BE_APPROVED_BY_PARTNER);
                echo "1";   
        }else{
           echo "0";
        }
       
    }



}
