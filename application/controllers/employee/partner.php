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
        $this->load->library("pagination");
        $this->load->library("session");
        $this->load->library('form_validation');
        $this->load->library('notify');
        $this->load->library('asynchronous_lib');
        $this->load->library('booking_utilities');
        $this->load->library('miscelleneous');
        $this->load->library('booking_utilities');
        $this->load->library('user_agent');
        $this->load->library("initialized_variable");
        $this->load->model("push_notification_model");
        $this->load->library('table');

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
        $this->load->view('partner/partner_login', $data);
    }

    /**
     * @desc: this is used to load pending booking
     * @param: Offset and page no., all flag to get all data, Booking id
     * @return: void
     */
    function pending_booking($offset = 0, $all = 0, $booking_id = '') {
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');
        $config['base_url'] = base_url() . 'partner/pending_booking';

        if (!empty($booking_id)) {
            $total_rows = $this->partner_model->getPending_booking($partner_id, $booking_id);
        } else {
            $total_rows = $this->partner_model->getPending_booking($partner_id);
        }
        $config['total_rows'] = count($total_rows);

        if ($all == 1) {
            $config['per_page'] = count($total_rows);
        } else {
            $config['per_page'] = 50;
        }
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['bookings'] = array_slice($total_rows, $offset, $config['per_page']);
        $data['escalation_reason'] = $this->vendor_model->getEscalationReason(array('entity' => 'partner', 'active' => '1'));

        if ($this->session->flashdata('result') != '') {
            $data['success'] = $this->session->flashdata('result');
        }

        log_message('info', 'Partner View: Pending booking: Partner id: ' . $partner_id . ", Partner name: " .
                $this->session->userdata('partner_name'));
        $data['is_ajax'] = $this->input->post('is_ajax');
        if(empty($this->input->post('is_ajax'))){
            $select = "partner_logo,alt_text";
            $where = array('partner_logo IS NOT NULL' => NULL,'partner_id' => $this->session->userdata('partner_id'));
            $header_data['partner_logo'] = $this->booking_model->get_partner_logo($select,$where);
            $this->load->view('partner/header',$header_data);
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

        $this->load->view('partner/header');
        $this->load->view('partner/pending_queries', $data);
    }

    /**
     * @desc: this is used to display completed booking for specific service center
     */
    function closed_booking($state, $offset = 0, $booking_id = "") {
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');

        $config['base_url'] = base_url() . 'partner/closed_booking/' . $state;
        if (!empty($booking_id)) {
            $config['total_rows'] = $this->partner_model->getclosed_booking("count", "", $partner_id, $state, $booking_id);
        } else {
            $config['total_rows'] = $this->partner_model->getclosed_booking("count", "", $partner_id, $state);
        }

        $config['per_page'] = 50;
        $config['uri_segment'] = 4;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        if (!empty($booking_id)) {
            $data['bookings'] = $this->partner_model->getclosed_booking($config['per_page'], $offset, $partner_id, $state, $booking_id);
        } else {
            $data['bookings'] = $this->partner_model->getclosed_booking($config['per_page'], $offset, $partner_id, $state);
        }

        if ($this->session->flashdata('result') != '')
            $data['success'] = $this->session->flashdata('result');

        $data['status'] = $state;

        log_message('info', 'Partner view ' . $state . ' booking  partner id' . $partner_id . " Partner name" . $this->session->userdata('partner_name') . " data " . print_r($data, true));

        $select = "partner_logo,alt_text";
        $where = array('partner_logo IS NOT NULL' => NULL,'partner_id' => $this->session->userdata('partner_id'));
        $header_data['partner_logo'] = $this->booking_model->get_partner_logo($select,$where);
        $this->load->view('partner/header',$header_data);
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
        $unit_where = array('booking_id' => $booking_id);
        $data['unit_details'] = $this->booking_model->get_unit_details($unit_where);
        if (!is_null($data['booking_history'][0]['sub_vendor_id'])) {
            $data['dhq'] = $this->upcountry_model->get_sub_service_center_details(array('id' => $data['booking_history'][0]['sub_vendor_id']));
        }
        log_message('info', 'Partner view booking details booking  partner id' . $this->session->userdata('partner_id') . " Partner name" . $this->session->userdata('partner_name'));

        $select = "partner_logo,alt_text";
        $where = array('partner_logo IS NOT NULL' => NULL,'partner_id' => $this->session->userdata('partner_id'));
        $header_data['partner_logo'] = $this->booking_model->get_partner_logo($select,$where);
        $this->load->view('partner/header',$header_data);
        $this->load->view('partner/booking_details', $data);
        $this->load->view('partner/partner_footer');
    }

    /**
     * @desc: This funtion will check Session
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
            $data['user'] = $this->user_model->search_user($phone_number);
            $partner_id = $this->session->userdata('partner_id');
            $partner_data = $this->partner_model->get_partner_code($partner_id);
            $partner_type = $partner_data[0]['partner_type'];
            $data['partner_type'] = $partner_type;

            $data['partner_code'] = $partner_data[0]['code'];
            if ($partner_type == OEM) {

                $data['appliances'] = $this->partner_model->get_partner_specific_services($this->session->userdata('partner_id'));
            } else {
                $data['appliances'] = $services = $this->booking_model->selectservice();
            }

            $data['phone_number'] = $phone_number;
            $select = "partner_logo,alt_text";
            $where = array('partner_logo IS NOT NULL' => NULL,'partner_id' => $this->session->userdata('partner_id'));
            $header_data['partner_logo'] = $this->booking_model->get_partner_logo($select,$where);
            $this->load->view('partner/header',$header_data);
            $this->load->view('partner/get_addbooking', $data);
            $this->load->view('partner/partner_footer');
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
        $post['name'] = $this->input->post('user_name');
        $post['mobile'] = $this->input->post('booking_primary_contact_no');
        $post['email'] = $this->input->post('user_email');
        $post['address'] = $this->input->post('booking_address');
        $post['pincode'] = $this->input->post('booking_pincode');
        $post['city'] = $this->input->post('city');
        $post['requestType'] = $this->input->post('prices');
        $post['landmark'] = $this->input->post('landmark');
        $post['service_id'] = $this->input->post('service_id');
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
        $post['assigned_vendor_id'] = $this->input->post('assigned_vendor_id');
        $post['upcountry_data'] = $this->input->post('upcountry_data');
        $post['alternate_phone_number'] = $this->input->post('alternate_phone_number');
        $post['booking_date'] = $booking_date;
        $post['partner_type'] = $this->input->post('partner_type');
        $post['appliance_unit'] = $this->input->post('appliance_unit');
        $post['partner_code'] = $this->input->post('partner_code');
        $post['amount_due'] = $this->input->post('grand_total');
        $post['product_type'] = $this->input->post('product_type');
        $post['appliance_name'] = $this->input->post('appliance_name');
        $post['dealer_name'] = $this->input->post('dealer_name');
        $post['dealer_phone_number'] = $this->input->post('dealer_phone_number');
        $post['dealer_id'] = $this->input->post('dealer_id');
        return $post;
    }

    function insertion_failure($post) {
        $to = DEVELOPER_EMAIL;
        $cc = "";
        $bcc = "";
        $subject = "Booking Insertion Failure By " . $this->session->userdata('partner_name');
        $message = $post;
        $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, $bcc, $subject, $message, "");
    }

    function set_form_validation() {
        $this->form_validation->set_rules('user_name', 'User Name', 'required|xss_clean');
        $this->form_validation->set_rules('booking_primary_contact_no', 'Mobile Number', 'trim|required|exact_length[10]|xss_clean');
        $this->form_validation->set_rules('city', 'City', 'trim|required|xss_clean');
        $this->form_validation->set_rules('booking_address', 'Booking Address', 'required');
        $this->form_validation->set_rules('landmark', 'LandMark', 'trim');
        $this->form_validation->set_rules('appliance_capacity', 'Appliance Capacity', 'trim|xss_clean');
        $this->form_validation->set_rules('alternate_phone_number', 'Alternate Number', 'trim|xss_clean');
        $this->form_validation->set_rules('purchase_year', 'Purchase Year', 'trim|xss_clean');
        $this->form_validation->set_rules('purchase_month', 'Purchase Month', 'trim|xss_clean');
        $this->form_validation->set_rules('model_number', 'Model Number', 'trim|xss_clean');
        $this->form_validation->set_rules('order_id', 'Order ID', 'trim|xss_clean');
        $this->form_validation->set_rules('serial_number', 'Serial Number', 'trim|xss_clean');
        $this->form_validation->set_rules('appliance_category', 'Appliance Category', 'required');
        $this->form_validation->set_rules('partner_source', 'Booking Source', 'required');
        $this->form_validation->set_rules('service_id', 'Service Name', 'required');
        $this->form_validation->set_rules('booking_date', 'Booking Date', 'required');
        $this->form_validation->set_rules('query_remarks', 'Problem Description', 'required');
        $this->form_validation->set_rules('booking_pincode', 'Booking Pincode', 'trim|required|exact_length[6]');
        $this->form_validation->set_rules('prices', 'Service Category', 'required');
        $this->form_validation->set_rules('grand_total', 'Grand Total', 'trim');
        $this->form_validation->set_rules('dealer_name', 'Dealer Name', 'trim|xss_clean');
        $this->form_validation->set_rules('dealer_phone_number', 'Dealer Phone Number', 'trim|xss_clean');

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
        $results['select_state'] = $this->vendor_model->getall_state();
        $partner_code = $this->partner_model->get_availiable_partner_code();
        foreach ($partner_code as $row) {
            $code[] = $row['code']; // add each partner code to the array
        }
        $results['partner_code'] = $code;
        $employee_list = $this->employee_model->get_employee_by_group(array("groups NOT IN ('developer') AND active = '1'" => NULL));
        $results['collateral_type'] = $this->reusable_model->get_search_result_data("collateral_type", '*', array("collateral_tag" => "Contract"), NULL, NULL, array("collateral_type" => "ASC"), NULL, NULL);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/addpartner', array('results' => $results, 'employee_list' => $employee_list));
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
                $edit_partner_data['partner']['upcountry_max_distance_threshold'] = $edit_partner_data['partner']['upcountry_max_distance_threshold'] + 25;
                $edit_partner_data['partner']['update_date'] = date("Y-m-d h:i:s");
                $edit_partner_data['partner']['agent_id'] = $this->session->userdata('id');
                $this->partner_model->edit_partner($edit_partner_data['partner'], $partner_id);
                //Getting Logged Employee Full Name
                $logged_user_name = $this->employee_model->getemployeefromid($this->session->userdata('id'))[0]['full_name'];
                //Logging
                log_message('info', __FUNCTION__ . ' Partner has been Updated : ' . print_r($this->input->post(), TRUE));
                $msg = "Partner Updated Successfully";
                $this->session->set_userdata('success', $msg);
                //Adding details in Booking State Change
                //$this->notify->insert_state_change('', PARTNER_UPDATED, PARTNER_UPDATED, 'Partner ID : ' . $partner_id, $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);
                //Sending Mail for Updated details
                $html = "<p>Following Partner has been Updated :</p><ul>";
                foreach ($edit_partner_data['partner'] as $key => $value) {
                    $html .= "<li><b>" . $key . '</b> =>';
                    $html .= " " . $value . '</li>';
                }
                $html .= "</ul>";
                $to = ANUJ_EMAIL_ID;
                $subject = "Partner Updated :  " . $this->input->post('public_name') . ' - By ' . $logged_user_name;
                //Cleaning Email Variables
                $this->email->clear(TRUE);
                //Send report via email
                $this->email->from(NOREPLY_EMAIL_ID, '247around Team');
                $this->email->to($to);
                $this->email->subject($subject);
                $this->email->message($html);
                if ($this->email->send()) {
                    $this->notify->add_email_send_details(NOREPLY_EMAIL_ID, $to, "", "", $subject, $html, "");
                    log_message('info', __METHOD__ . ": Mail sent successfully to " . $to);
                } else {
                    log_message('info', __METHOD__ . ": Mail could not be sent to " . $to);
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
                $partner_operation_state = $this->input->post('select_state');
                //Getting Partner code
                $code = $this->input->post('partner_code');
                //Add Customer Care Number
                $return_data['partner']['customer_care_contact'] = $this->input->post("customer_care_contact");
                $return_data['partner']['upcountry_max_distance_threshold'] = $return_data['partner']['upcountry_max_distance_threshold'] + 25;
                $partner_id = $this->partner_model->add_partner($return_data['partner']);
                //Set Flashdata on success or on Error of Data insert in table
                if (!empty($partner_id)) {
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
                    $html = "<p>Following Partner has been Added :</p><ul>";
                    foreach ($return_data['partner'] as $key => $value) {
                        $html .= "<li><b>" . $key . '</b> =>';
                        $html .= " " . $value . '</li>';
                    }
                    $html .= "</ul>";
                    $to = ANUJ_EMAIL_ID;
                    $subject = "New Partner Added " . $this->input->post('public_name') . ' - By ' . $logged_user_name;
                    //Cleaning Email Variables
                    $this->email->clear(TRUE);
                    //Send report via email
                    $this->email->from(NOREPLY_EMAIL_ID, '247around Team');
                    $this->email->to($to);
                    $this->email->subject($subject);
                    $this->email->message($html);
                    if ($this->email->send()) {
                        $this->notify->add_email_send_details(NOREPLY_EMAIL_ID, $to, "", "", $subject, $html, "");
                        log_message('info', __METHOD__ . ": Mail sent successfully to " . $to);
                    } else {
                        log_message('info', __METHOD__ . ": Mail could not be sent to " . $to);
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

    function get_partner_form_data() {
        $return_data['company_name'] = $this->input->post('company_name');
        $return_data['company_type'] = $this->input->post('company_type');
        $return_data['public_name'] = $this->input->post('public_name');
        $return_data['address'] = $this->input->post('address');
        $return_data['landmark'] = $this->input->post('landmark');
        $return_data['state'] = $this->input->post('state');
        $return_data['district'] = $this->input->post('district');
        $return_data['pincode'] = $this->input->post('pincode');
        $return_data['primary_contact_name'] = $this->input->post('primary_contact_name');
        $return_data['primary_contact_email'] = $this->input->post('primary_contact_email');
        $return_data['customer_care_contact'] = $this->input->post('customer_care_contact');
        $return_data['primary_contact_phone_1'] = $this->input->post('primary_contact_phone_1');
        $return_data['primary_contact_phone_2'] = $this->input->post('primary_contact_phone_2');
        $return_data['owner_name'] = $this->input->post('owner_name');
        $return_data['owner_email'] = $this->input->post('owner_email');
        $return_data['owner_alternate_email'] = $this->input->post('owner_alternate_email');
        $return_data['owner_phone_1'] = $this->input->post('owner_phone_1');
        $return_data['owner_phone_2'] = $this->input->post('owner_phone_2');
        $return_data['summary_email_to'] = $this->input->post('summary_email_to');
        $return_data['summary_email_cc'] = $this->input->post('summary_email_cc');
        $return_data['invoice_email_to'] = $this->input->post('invoice_email_to');
        $return_data['invoice_email_cc'] = $this->input->post('invoice_email_cc');
        $return_data['invoice_courier_name'] = $this->input->post('invoice_courier_name');
        $return_data['invoice_courier_address'] = $this->input->post('invoice_courier_address');
        $return_data['invoice_courier_phone_number'] = $this->input->post('invoice_courier_phone_number');
        $return_data['is_def_spare_required'] = $this->input->post('is_def_spare_required');
        $partner_code = $this->input->post('partner_code');
        $return_data['account_manager_id'] = $this->input->post('account_manager_id');
        $return_data['spare_notification_email'] = $this->input->post('spare_notification_email');
        $return_data['prepaid_amount_limit'] = $this->input->post('prepaid_amount_limit');
        $return_data['prepaid_notification_amount'] = $this->input->post('prepaid_notification_amount');
        $return_data['grace_period_date'] = $this->input->post('grace_period_date');
        $is_prepaid = $this->input->post('is_prepaid');
        if (!empty($is_prepaid)) {
            $return_data['is_prepaid'] = 1;
        } else {
            $return_data['is_prepaid'] = 0;
        }

        if (empty($partner_code)) {
            $return_data['is_active'] = 0;
        }

        if ($this->input->post('is_reporting_mail') == 'on') {
            $return_data['is_reporting_mail'] = '1';
        } else {
            $return_data['is_reporting_mail'] = '0';
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
        } else {
            $return_data['is_upcountry'] = 0;
            $return_data['upcountry_rate'] = 0;
            $return_data['upcountry_min_distance_threshold'] = 0;
            $return_data['upcountry_max_distance_threshold'] = 0;
            $return_data['upcountry_rate1'] = 0;
            $return_data['upcountry_mid_distance_threshold'] = 0;
            $return_data['upcountry_approval_email'] = NULL;
            $return_data['upcountry_approval'] = 0;
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
        $this->form_validation->set_rules('company_name', 'Company Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('public_name', 'Public Name', 'trim|required|xss_clean');
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
        $data = [];
        $query = $this->partner_model->get_partner_details_with_soucre_code($partner_id);
        foreach ($query as $key => $value) {
            //Getting Appliances and Brands details for partner
            $service_brands[] = $this->partner_model->get_service_brands_for_partner($value['id']);
        }
        $pushNotification = $this->push_notification_model->get_push_notification_subscribers_by_entity(_247AROUND_PARTNER_STRING);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/viewpartner', array('query' => $query, 'service_brands' => $service_brands,'push_notification' => $pushNotification));
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

        $get_partner_details = $this->partner_model->getpartner_details('partners.public_name,account_manager_id,primary_contact_email,owner_email', array('partners.id' => $id));
        $am_email = "";
        if (!empty($get_partner_details[0]['account_manager_id'])) {
            $am_email = $this->employee_model->getemployeefromid($get_partner_details[0]['account_manager_id'])[0]['official_email'];
        }

        //send email
        $email_template = $this->booking_model->get_booking_email_template("partner_activate_email");
        $to = $get_partner_details[0]['primary_contact_email'] . "," . $get_partner_details[0]['owner_email'];
        $cc = NITS_ANUJ_EMAIL_ID . "," . $am_email;
        $subject = $email_template[4];
        $message = $email_template[0];

        $this->notify->sendEmail($email_template[2], $to, $cc, "", $subject, $message, "");
        $this->partner_model->activate($id);
        //Storing State change values in Booking_State_Change Table
        $this->notify->insert_state_change('', _247AROUND_PARTNER_ACTIVATED, _247AROUND_PARTNER_DEACTIVATED, 'Partner ID = ' . $id, $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);
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

        $get_partner_details = $this->partner_model->getpartner_details('partners.public_name,account_manager_id,primary_contact_email,owner_email', array('partners.id' => $id));
        $am_email = "";
        if (!empty($get_partner_details[0]['account_manager_id'])) {
            $am_email = $this->employee_model->getemployeefromid($get_partner_details[0]['account_manager_id'])[0]['official_email'];
        }

        //send email

        $email_template = $this->booking_model->get_booking_email_template("partner_deactivate_email");
        $to = $get_partner_details[0]['primary_contact_email'] . "," . $get_partner_details[0]['owner_email'];
        $cc = NITS_ANUJ_EMAIL_ID . "," . $am_email;
        $subject = $email_template[4];
        $message = $email_template[0];

        $this->notify->sendEmail($email_template[2], $to, $cc, "", $subject, $message, "");

        $this->partner_model->deactivate($id);
        //Storing State change values in Booking_State_Change Table
        $this->notify->insert_state_change('', _247AROUND_PARTNER_DEACTIVATED, _247AROUND_PARTNER_ACTIVATED, 'Partner ID = ' . $id, $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);


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
        $results['select_state'] = $this->vendor_model->getall_state();
        $results['services'] = $this->vendor_model->selectservice();
        //Getting Login Details for this partner
        $results['partner_code'] = $this->partner_model->get_partner_code($id);
        $partner_code = $this->partner_model->get_availiable_partner_code();
        foreach ($partner_code as $row) {
            $code[] = $row['code']; // add each partner code to the array
        }
        $results['partner_code_availiable'] = $code;
        //Getting Parnter Operation Region Details
        $where = array('partner_id' => $id);
        $results['partner_operation_region'] = $this->partner_model->get_partner_operation_region($where);
        $results['brand_mapping'] = $this->partner_model->get_partner_specific_details($where, "service_id, brand, active");
       
        $results['partner_contracts'] = $this->reusable_model->get_search_result_data("collateral", 'collateral.document_description,collateral.file,collateral.start_date,collateral.end_date,collateral_type.collateral_type', array("entity_id" => $id, "entity_type" => "partner"), array("collateral_type" => "collateral_type.id=collateral.collateral_id"), NULL, NULL, NULL, NULL);
        $results['collateral_type'] = $this->reusable_model->get_search_result_data("collateral_type", '*', array("collateral_tag" => "Contract"), NULL, NULL, array("collateral_type" => "ASC"), NULL, NULL);
        $employee_list = $this->employee_model->get_employee_by_group(array("groups NOT IN ('developer') AND active = '1'" => NULL));
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/addpartner', array('query' => $query, 'results' => $results, 'employee_list' => $employee_list, 'form_type' => 'update'));
    }

    /**
     * @desc: This is used to get find user form in Partner CRM
     * params: void
     * return: View form to find user
     */
    function get_user_form() {
        $this->checkUserSession();
        $select = "partner_logo,alt_text";
        $where = array('partner_logo IS NOT NULL' => NULL,'partner_id' => $this->session->userdata('partner_id'));
        $header_data['partner_logo'] = $this->booking_model->get_partner_logo($select,$where);
        $this->load->view('partner/header',$header_data);
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
                $select = "partner_logo,alt_text";
                $where = array('partner_logo IS NOT NULL' => NULL,'partner_id' => $this->session->userdata('partner_id'));
                $header_data['partner_logo'] = $this->booking_model->get_partner_logo($select,$where);
                $this->load->view('partner/header',$header_data);
                $this->load->view('partner/bookinghistory', $data);
                $this->load->view('partner/partner_footer');
            } else {
                $msg = "User Not Exist.";
                $this->session->set_userdata('error', $msg);

                redirect(base_url() . 'employee/partner/get_user_form');
            }
        } else if ($search_type === 'booking_id') {  //if booking id given and matched, will be displayed
            $where = array('booking_details.booking_id' => $search_value);
            $Bookings = $this->booking_model->search_bookings($where, $this->session->userdata('partner_id'));
            $data['data'] = json_decode(json_encode($Bookings), True);
            $select = "partner_logo,alt_text";
            $header_where = array('partner_logo IS NOT NULL' => NULL,'partner_id' => $this->session->userdata('partner_id'));
            $header_data['partner_logo'] = $this->booking_model->get_partner_logo($select,$header_where);
            $this->load->view('partner/header',$header_data);
            $this->load->view('partner/bookinghistory', $data);
            $this->load->view('partner/partner_footer');
        } else if ($search_type === 'order_id') {

            $where = array('order_id' => $search_value);
            $Bookings = $this->booking_model->search_bookings($where, $this->session->userdata('partner_id'));
            $data['data'] = json_decode(json_encode($Bookings), True);
            $select = "partner_logo,alt_text";
            $header_where = array('partner_logo IS NOT NULL' => NULL,'partner_id' => $this->session->userdata('partner_id'));
            $header_data['partner_logo'] = $this->booking_model->get_partner_logo($select,$header_where);
            $this->load->view('partner/header',$header_data);
            $this->load->view('partner/bookinghistory', $data);
            $this->load->view('partner/partner_footer');
        } else if ($search_type === 'serial_number') {

            $where = array('partner_serial_number' => $search_value);
            $Bookings = $this->booking_model->search_bookings($where, $this->session->userdata('partner_id'));
            $data['data'] = json_decode(json_encode($Bookings), True);
            $data['search'] = "Search";
            $select = "partner_logo,alt_text";
            $header_where = array('partner_logo IS NOT NULL' => NULL,'partner_id' => $this->session->userdata('partner_id'));
            $header_data['partner_logo'] = $this->booking_model->get_partner_logo($select,$header_where);
            $this->load->view('partner/header',$header_data);
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
        $data['vendor_partner'] = "partner";
        $data['vendor_partner_id'] = $partner_id;
        $invoice['invoice_array'] = $this->invoices_model->getInvoicingData($data);

        $where = array(
                'partner_id' => $partner_id,
                'partner_invoice_id is null' => NULL,
                'create_date >= "2017-01-01" ' => NULL,
                'partner_net_payable > 0 '=> NULL,
                'booking_status IN ("' . _247AROUND_PENDING . '", "'  . _247AROUND_COMPLETED . '")' => NULL
        );
        // sum of partner payable amount whose booking is in followup, pending and completed(Invoice not generated) state.
        
        $unbilled_data  = $this->booking_model->get_unit_details($where, false, 'booking_id, partner_net_payable');
        
        $unbilled_amount = 0;
        if(!empty($unbilled_data)){
            $unbilled_amount = (array_sum(array_column($unbilled_data, 'partner_net_payable')));
        }
       
        $invoice['unbilled_amount'] = $unbilled_amount;
        $invoice['unbilled_data'] = $unbilled_data;
        $invoice['invoice_amount'] = $this->invoices_model->getsummary_of_invoice("partner",array('id' => $partner_id))[0];
      
        $select = "partner_logo,alt_text";
        $where = array('partner_logo IS NOT NULL' => NULL,'partner_id' => $this->session->userdata('partner_id'));
        $header_data['partner_logo'] = $this->booking_model->get_partner_logo($select,$where);
        $this->load->view('partner/header',$header_data);
        $this->load->view('partner/invoice_summary', $invoice);
        $this->load->view('partner/partner_footer');
    }
    
    function get_bank_transaction(){
         $this->checkUserSession();
         $partner_id = $this->session->userdata('partner_id');
         $data2['partner_vendor'] = "partner";
         $data2['partner_vendor_id'] = $partner_id;
         $invoice['bank_statement'] = $this->invoices_model->get_bank_transactions_details('*', $data2);
         $select = "partner_logo,alt_text";
         $where = array('partner_logo IS NOT NULL' => NULL,'partner_id' => $partner_id);
         $header_data['partner_logo'] = $this->booking_model->get_partner_logo($select,$where);
         $this->load->view('partner/header',$header_data);
         $this->load->view('partner/bank_transaction', $invoice);
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
            $select = "partner_logo,alt_text";
            $where = array('partner_logo IS NOT NULL' => NULL,'partner_id' => $this->session->userdata('partner_id'));
            $header_data['partner_logo'] = $this->booking_model->get_partner_logo($select,$where);
            $this->load->view('partner/header',$header_data);
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
        $data['closed_date'] = $data['update_date'] = date("Y-m-d H:i:s");
        $data['current_status'] = _247AROUND_CANCELLED;
        $data['internal_status'] = $data['cancellation_reason'] = $this->input->post('cancellation_reason');
        $data['closing_remarks'] = $this->input->post('remarks');

        //check partner status from partner_booking_status_mapping table  
        $partner_id = $this->input->post("partner_id");
        $partner_status = $this->booking_utilities->get_partner_status_mapping_data($data['current_status'], $data['internal_status'], $partner_id, $booking_id);
        if (!empty($partner_status)) {
            $data['partner_current_status'] = $partner_status[0];
            $data['partner_internal_status'] = $partner_status[1];
        }

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
                //get the unit details data and update the inventory stock
                $booking_unit_details = $this->reusable_model->get_search_query('booking_unit_details', 'booking_unit_details.price_tags,booking_unit_details.appliance_capacity', array('booking_unit_details.booking_id' => $booking_id,"booking_unit_details.price_tags like '%"._247AROUND_WALL_MOUNT__PRICE_TAG."%'" => NULL),NULL, NULL, NULL, NULL, NULL)->result_array();
                if (!empty($booking_unit_details)) {    
                    //process each unit if price tag is wall mount
                    foreach($booking_unit_details as $value){
                        $match = array();
                        //get the size from the capacity to know the part number
                        preg_match('/[0-9]+/', $value['appliance_capacity'], $match);
                        if (!empty($match)) {
                            if ($match[0] <= 32) {
                                $data['part_number'] = LESS_THAN_32_BRACKETS_PART_NUMBER;
                            } else if ($match[0] > 32) {
                                $data['part_number'] = GREATER_THAN_32_BRACKETS_PART_NUMBER;
                            }

                            $data['receiver_entity_id'] = $booking_data[0]['assigned_vendor_id'];
                            $data['receiver_entity_type'] = _247AROUND_SF_STRING;
                            $data['stock'] = 1;
                            $data['booking_id'] = $booking_id;
                            $data['agent_id'] = $this->session->userdata('agent_id');
                            $data['agent_type'] = _247AROUND_PARTNER_STRING;

                            $this->miscelleneous->process_inventory_stocks($data);
                        }
                    }
                }
            }

            //Log this state change as well for this booking
            //param:-- booking id, new state, old state, remarks, agent_id, partner  name, partner id
            $this->notify->insert_state_change($booking_id, $data['current_status'], $status, $data['cancellation_reason'], $this->session->userdata('agent_id'), $this->session->userdata('partner_name'), $this->session->userdata('partner_id'));

            // this is used to send email or sms while booking cancelled
            $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
            $send['booking_id'] = $booking_id;
            $send['state'] = $data['current_status'];
            $this->asynchronous_lib->do_background_process($url, $send);
            $msg = $booking_id . " Booking Cancelled.";
            $this->session->set_userdata('success', $msg);

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

            $select = "partner_logo,alt_text";
            $where = array('partner_logo IS NOT NULL' => NULL,'partner_id' => $this->session->userdata('partner_id'));
            $header_data['partner_logo'] = $this->booking_model->get_partner_logo($select,$where);
            $this->load->view('partner/header',$header_data);
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

        if ($this->form_validation->run() == FALSE) {
            $this->get_reschedule_booking_form($booking_id);
        } else {
            log_message('info', __FUNCTION__ . " Booking Id  " . $booking_id);
            $booking_date = $this->input->post('booking_date');

            $data['booking_date'] = date('d-m-Y', strtotime($booking_date));
            $data['current_status'] = 'Rescheduled';
            $data['internal_status'] = 'Rescheduled';
            $data['update_date'] = date("Y-m-d H:i:s");

            //check partner status from partner_booking_status_mapping table  
            $partner_id = $this->input->post('partner_id');
            $partner_status = $this->booking_utilities->get_partner_status_mapping_data($data['current_status'], $data['internal_status'], $partner_id, $booking_id);
            if (!empty($partner_status)) {
                $data['partner_current_status'] = $partner_status[0];
                $data['partner_internal_status'] = $partner_status[1];
            }

            $update_status = $this->booking_model->update_booking($booking_id, $data);
            if ($update_status) {
                $this->notify->insert_state_change($booking_id, _247AROUND_RESCHEDULED, _247AROUND_PENDING, " Rescheduled Booking BY Partner ", $this->session->userdata('agent_id'), $this->session->userdata('partner_name'), $this->session->userdata('partner_id'));


                $service_center_data['internal_status'] = "Pending";
                $service_center_data['current_status'] = "Pending";
                $service_center_data['update_date'] = date("Y-m-d H:i:s");

                log_message('info', __FUNCTION__ . " Update Service center action table  " . print_r($service_center_data, true));

                $this->vendor_model->update_service_center_action($booking_id, $service_center_data);

                $send_data['booking_id'] = $booking_id;
                $send_data['current_status'] = "Rescheduled";
                $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
                $this->asynchronous_lib->do_background_process($url, $send_data);

                log_message('info', __FUNCTION__ . " Request to prepare Job Card  " . print_r($booking_id, true));

                //Prepare job card
                $this->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);
                $this->booking_utilities->lib_send_mail_to_vendor($booking_id, "");
                $msg = $booking_id . " Booking Rescheduled.";
                $this->session->set_userdata('success', $msg);

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
    function escalation_form($booking_id) {
        log_message('info', __FUNCTION__ . " Booking Id  " . print_r($booking_id, true));
        $this->checkUserSession();
        $data['escalation_reason'] = $this->vendor_model->getEscalationReason(array('entity' => 'partner', 'active' => '1'));
        $data['booking_id'] = $booking_id;

        $this->load->view('partner/header');
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
        $this->form_validation->set_rules('escalation_reason_id', 'Escalation Reason', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->escalation_form($booking_id);
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

            log_message('info', __FUNCTION__ . " escalation_reason  " . print_r($escalation, true));

            //inserts vendor escalation details
            $escalation_id = $this->vendor_model->insertVendorEscalationDetails($escalation);

            $this->notify->insert_state_change($escalation['booking_id'], "Escalation", _247AROUND_PENDING, $remarks, $this->session->userdata('agent_id'), $this->session->userdata('partner_name'), $this->session->userdata('partner_id'));
            if ($escalation_id) {
                log_message('info', __FUNCTION__ . " Escalation Inserted ");
                $this->booking_model->increase_escalation_reschedule($booking_id, "count_escalation");
                $bcc = "";
                $attachment = "";
                $partner_details = $this->dealer_model->entity_login(array('agent_id' => $this->session->userdata('agent_id')))[0];
                $rm_mail = $this->vendor_model->get_rm_sf_relation_by_sf_id($bookinghistory[0]['assigned_vendor_id'])[0]['official_email'];
                $partner_mail_to = $partner_details['email'];
                $partner_mail_cc = NITS_ANUJ_EMAIL_ID . ",escalations@247around.com ," . $rm_mail;
                $partner_subject = "Booking " . $booking_id . " Escalated ";
                $partner_message = "<p>This booking is ESCALATED to 247around, we will look into this very soon.</p><br><b>Booking ID : </b>" . $booking_id . " Escalated <br><br><strong>Remarks : </strong>" . $remarks;
                $this->notify->sendEmail(NOREPLY_EMAIL_ID, $partner_mail_to, $partner_mail_cc, $bcc, $partner_subject, $partner_message, $attachment);

                log_message('info', __FUNCTION__ . " Escalation Mail Sent ");

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

                if (!empty($time_difference) && $time_difference < 0) {
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
        }
    }

    /**
     * @desc: This is used to load update booking form
     * @param String $booking_id
     */
    function get_editbooking_form($booking_id) {
        log_message('info', __FUNCTION__ . " Booking Id: " . $booking_id);
        $this->checkUserSession();

        $booking_history = $this->booking_model->getbooking_history($booking_id);

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
            $price_tag = array();
            foreach ($data['unit_details'] as $unit) {
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

            $select = "partner_logo,alt_text";
            $where = array('partner_logo IS NOT NULL' => NULL,'partner_id' => $this->session->userdata('partner_id'));
            $header_data['partner_logo'] = $this->booking_model->get_partner_logo($select,$where);
            $this->load->view('partner/header',$header_data);
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
            $upcountry_data = json_decode($post['upcountry_data'], TRUE);

            $unit_details['service_id'] = $appliance_details['service_id'] = $booking_details['service_id'];
            $unit_details['appliance_brand'] = $appliance_details['brand'] = $post['brand'];
            $unit_details['appliance_description'] = $appliance_details['description'] = $post['productType'];
            $unit_details['appliance_category'] = $appliance_details['category'] = $post['category'];
            $unit_details['appliance_capacity'] = $appliance_details['capacity'] = $post['capacity'];
            $unit_details['model_number'] = $appliance_details['model_number'] = $post['model'];
            $unit_details['partner_serial_number'] = $appliance_details['serial_number'] = $post['serial_number'];
            $unit_details['purchase_month'] = $appliance_details['purchase_month'] = $post['purchase_month'];
            $unit_details['purchase_year'] = $appliance_details['purchase_year'] = $post['purchase_year'];
            $unit_details['partner_id'] = $post['partner_id'];
            $unit_details['booking_id'] = $booking_details['booking_id'] = $booking_id;
            if ($post['product_type'] == "Delivered") {
                $booking_details['current_status'] = _247AROUND_PENDING;
                $booking_details['internal_status'] = _247AROUND_PENDING;
                $unit_details['booking_id'] = $booking_id;
                $unit_details['booking_status'] = _247AROUND_PENDING;
                $booking_details['type'] = "Booking";
                if (strpos($booking_id, "Q-", 0) !== FALSE) {
                    $booking_id_array = explode("Q-", $booking_id);
                    $unit_details['booking_id'] = $booking_details['booking_id'] = $booking_id_array[1];
                }
            } else {
                $booking_details['current_status'] = _247AROUND_FOLLOWUP;
                $booking_details['internal_status'] = _247AROUND_FOLLOWUP;
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
            $customer_net_payable = 0;
            foreach ($post['requestType'] as $key => $sc) {
                $explode = explode("_", $sc);

                $unit_details['id'] = $explode[0];
                $unit_details['around_paid_basic_charges'] = $unit_details['around_net_payable'] = "0.00";
                $unit_details['partner_paid_basic_charges'] = $explode[2];
                $unit_details['partner_net_payable'] = $explode[2];
                $unit_details['ud_update_date'] = date('Y-m-d H:i:s');
                $unit_details['booking_status'] = "Pending";
                $customer_net_payable += ($explode[1] - $explode[2]);
                
                $agent_details['agent_id'] = $this->session->userdata('agent_id');
                $agent_details['agent_type'] = _247AROUND_PARTNER_STRING;
                $result = $this->booking_model->update_booking_in_booking_details($unit_details, $booking_id, $booking_details['state'], $key,$agent_details);
                array_push($updated_unit_id, $result['unit_id']);
            }

            if (!empty($updated_unit_id)) {
                log_message('info', __METHOD__ . " UNIT ID: " . print_r($updated_unit_id, true));
                $sf_id = $this->reusable_model->get_search_query('booking_details','assigned_vendor_id',array('booking_id'=>$booking_id),NULL,NULL,NULL,NULL,NULL)->result_array();
                if(!empty($sf_id)){
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

                $this->insert_details_in_state_change($booking_id, _247AROUND_PENDING, $booking_details['booking_remarks']);
                if ($this->OLD_BOOKING_STATE == _247AROUND_CANCELLED) {
                    $sc_data['current_status'] = _247AROUND_PENDING;
                    $sc_data['internal_status'] = _247AROUND_PENDING;
                    $booking_details['cancellation_reason'] = NULL;
                    $booking_details['closed_date'] = NULL;
                    
                    $booking_details['internal_status'] = "Booking Opened From Cancelled";

                    $this->service_centers_model->update_service_centers_action_table($booking_id, $sc_data);
                }
            } else {
                // IN the Shipped Case
                $price_array['is_upcountry'] = $booking_details['is_upcountry'];
                $price_array['customer_net_payable'] = round($customer_net_payable, 0);
                $this->initialized_variable->fetch_partner_data($post['partner_id']);

                $this->miscelleneous->check_upcountry($booking_details, $post['appliance_name'], $price_array, "shipped");

                $this->insert_details_in_state_change($booking_id, _247AROUND_FOLLOWUP, $booking_details['booking_remarks']);
                $booking_details['assigned_vendor_id'] = NULL;
            }
            $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking_details['current_status'], $booking_details['internal_status'], $this->session->userdata('partner_id'), $booking_id);
            if (!empty($partner_status)) {
                $booking_details['partner_current_status'] = $partner_status[0];
                $booking_details['partner_internal_status'] = $partner_status[1];
            }
            $this->booking_model->update_booking($booking_id, $booking_details);
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
    function get_spare_parts_booking($offset = 0, $all = 0) {
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id'));
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');
        $where = "spare_parts_details.partner_id = '" . $partner_id . "' AND status = '" . SPARE_PARTS_REQUESTED . "' "
                . " AND booking_details.current_status IN ('Pending', 'Rescheduled') ";

        $config['base_url'] = base_url() . 'partner/get_spare_parts_booking';
        $total_rows = $this->partner_model->get_spare_parts_booking_list($where, false, false, false);
        $config['total_rows'] = $total_rows[0]['total_rows'];

        if ($all == 1) {
            $config['per_page'] = $total_rows[0]['total_rows'];
        } else {
            $config['per_page'] = 50;
        }
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['spare_parts'] = $this->partner_model->get_spare_parts_booking_list($where, $offset, $config['per_page'], true);
        $data['is_ajax'] = $this->input->post('is_ajax');
        if(empty($this->input->post('is_ajax'))){
            $select = "partner_logo,alt_text";
            $where = array('partner_logo IS NOT NULL' => NULL,'partner_id' => $this->session->userdata('partner_id'));
            $header_data['partner_logo'] = $this->booking_model->get_partner_logo($select,$where);
            $this->load->view('partner/header',$header_data);
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
    function insert_details_in_state_change($booking_id, $new_state, $remarks, $is_cron = "") {
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id') . " Booking ID: " . $booking_id . ' new_state: ' . $new_state . ' remarks: ' . $remarks);
        //Save state change
        $state_change['booking_id'] = $booking_id;
        $state_change['new_state'] = $new_state;

        $booking_state_change = $this->booking_model->get_booking_state_change($state_change['booking_id']);

        if ($booking_state_change > 0) {
            $state_change['old_state'] = $booking_state_change[count($booking_state_change) - 1]['new_state'];
            $this->OLD_BOOKING_STATE = $state_change['old_state'];
        } else { //count($booking_state_change)
            $state_change['old_state'] = "Pending";
        }

        if (empty($is_cron)) {
            $state_change['agent_id'] = $this->session->userdata('agent_id');
            $state_change['partner_id'] = $this->session->userdata('partner_id');
        } else {
            $state_change['agent_id'] = '1';
            $state_change['partner_id'] = _247AROUND;
        }
        $state_change['remarks'] = $remarks;

        // Insert data into booking state change
        $state_change_id = $this->booking_model->insert_booking_state_change($state_change);
        if ($state_change_id) {
            
        } else {
            log_message('info', __FUNCTION__ . '=> Booking details is not inserted into state change ' . print_r($state_change, true));
        }
    }

    /**
     * @desc: This method is used to load update form(spare parts).
     * @param String $id
     */
    function update_spare_parts_form($id) {
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id') . " Spare Parts ID: " . $id);
        $this->checkUserSession();
        $where['length'] = -1;
        $where['where'] = array('spare_parts_details.id' => $id);
        $where['select'] = "booking_details.booking_id, users.name, booking_primary_contact_no,parts_requested, model_number,serial_number,date_of_purchase, invoice_pic,"
                . "serial_number_pic,defective_parts_pic,spare_parts_details.id, booking_details.request_type, purchase_price, estimate_cost_given_date";

        $data['spare_parts'] = $this->inventory_model->get_spare_parts_query($where);
        $select = "partner_logo,alt_text";
        $where = array('partner_logo IS NOT NULL' => NULL,'partner_id' => $this->session->userdata('partner_id'));
        $header_data['partner_logo'] = $this->booking_model->get_partner_logo($select,$where);
        $this->load->view('partner/header',$header_data);
        $this->load->view('partner/update_spare_parts_form', $data);
        $this->load->view('partner/partner_footer');
    }

    /**
     * @desc: This method is used to update spare parts. If gets input from form.
     * Insert data into booking state change and update sc action table
     * @param String $booking_id
     */
    function process_update_spare_parts($booking_id, $id) {
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id') . " Spare id: " . $id);
        $this->checkUserSession();
        $this->form_validation->set_rules('shipped_parts_name', 'Parts Name', 'trim|required');
        $this->form_validation->set_rules('remarks_by_partner', 'Remarks', 'trim|required');
        $this->form_validation->set_rules('courier_name', 'Courier Name', 'trim|required');
        $this->form_validation->set_rules('awb', 'AWB', 'trim|required');
        $this->form_validation->set_rules('incoming_invoice', 'Invoice', 'callback_spare_incoming_invoice');
        //$this->form_validation->set_rules('partner_challan_number', 'Partner Challan Number', 'trim|required');
        $this->form_validation->set_rules('approx_value', 'Approx Value', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            log_message('info', __FUNCTION__ . '=> Form Validation is not updated by Partner ' . $this->session->userdata('partner_id') .
                    " Spare id " . $id . " Data" . print_r($this->input->post(), true));
            $this->update_spare_parts_form($id);
        } else {
            //check upload challan file
            $MB = 1048576;
            if ($_FILES['challan_file']['size'] >= 2 * $MB) {
                log_message('info', __FUNCTION__ . '=> Uploaded File is greater than 2 Mb ' . $this->session->userdata('partner_id') .
                        " Spare id " . $id . " Data" . print_r($this->input->post(), true));
                $this->form_validation->set_message('challan_file', "Uploaded File Must be Less Than 2Mb in size");
                $this->update_spare_parts_form($id);
            } else {
                $challan_file = $this->upload_challan_file($id);
                if($challan_file){
                    $data['partner_challan_file'] = $challan_file;
                }
                $partner_id = $this->session->userdata('partner_id');
                $data['parts_shipped'] = $this->input->post('shipped_parts_name');
                $data['courier_name_by_partner'] = $this->input->post('courier_name');
                $data['awb_by_partner'] = $this->input->post('awb');
                $data['remarks_by_partner'] = $this->input->post('remarks_by_partner');
                $data['shipped_date'] = $this->input->post('shipment_date');
                $data['partner_challan_number'] = $this->input->post('partner_challan_number');
                $data['challan_approx_value'] = $this->input->post('approx_value');
                $incoming_invoice_pdf = $this->input->post("incoming_invoice_pdf");
                if (!empty($incoming_invoice_pdf)) {
                    $data['incoming_invoice_pdf'] = $incoming_invoice_pdf;
                }
                $data['status'] = "Shipped";
                $where = array('id' => $id, 'partner_id' => $partner_id);
                $response = $this->service_centers_model->update_spare_parts($where, $data);
                if ($response) {

                    $this->insert_details_in_state_change($booking_id, SPARE_PARTS_SHIPPED, "Partner acknowledged to shipped spare parts");

                    $sc_data['current_status'] = "InProcess";
                    $sc_data['internal_status'] = SPARE_PARTS_SHIPPED;
                    $this->vendor_model->update_service_center_action($booking_id, $sc_data);

                    $booking['internal_status'] = SPARE_PARTS_SHIPPED;

                    $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, $booking['internal_status'], $partner_id, $booking_id);
                    if (!empty($partner_status)) {
                        $booking['partner_current_status'] = $partner_status[0];
                        $booking['partner_internal_status'] = $partner_status[1];
                    }

                    $this->booking_model->update_booking($booking_id, $booking);
                    if (!empty($incoming_invoice_pdf)) {
                        // Send OOW invoice to aditya
                        $url = base_url() . "employee/invoice/generate_oow_parts_invoice/" . $id;
                        $async_data['booking_id'] = $booking_id;
                        $this->asynchronous_lib->do_background_process($url, $async_data);
                    }

                    $userSession = array('success' => 'Parts Updated');
                    $this->session->set_userdata($userSession);
                    redirect(base_url() . "partner/get_spare_parts_booking");
                } else { //if($response){
                    log_message('info', __FUNCTION__ . '=> Spare parts booking is not updated by Partner ' . $this->session->userdata('partner_id') .
                            " booking id " . $booking_id . " Data" . print_r($this->input->post(), true));
                    $userSession = array('success' => 'Parts Not Updated');
                    $this->session->set_userdata($userSession);
                    redirect(base_url() . "partner/update_spare_parts_form/" . $booking_id);
                }
            }
        }
    }

    /**
     * @desc This is used to upload and send Repair OOW Parts Invoice
     * @return boolean
     */
    function spare_incoming_invoice() {
        log_message('info', __FUNCTION__);

        $request_type = $this->input->post("request_type");
        $booking_id = $this->input->post("booking_id");

        if ($request_type == REPAIR_OOW_TAG) {
            $allowedExts = array("PDF", "pdf");
            $invoice_name = $this->miscelleneous->upload_file_to_s3($_FILES["incoming_invoice"], "sp_parts_invoice", $allowedExts, $booking_id, "invoices-excel", "incoming_invoice_pdf");
            if (!empty($invoice_name)) {
                $template = $this->booking_model->get_booking_email_template("OOW_invoice_sent");
                if (!empty($template)) {
                    $attachment = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/invoices-excel/" . $invoice_name;
                    $subject = vsprintf($template[4], $booking_id);
                    $emailBody = vsprintf($template[0], $this->input->post("invoice_amount"));
                    $this->notify->sendEmail($template[2], $template[1], $template[3], '', $subject, $emailBody, $attachment);
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

    function download_spare_parts() {
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id'));
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');
        $where = "spare_parts_details.partner_id = '" . $partner_id . "' AND status = '" . SPARE_PARTS_REQUESTED . "' "
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
        // send empty beacuse there is no need to display sms to partner panel
        $data['sms_sent_details'] = array();

        //$this->load->view('partner/header');

        $this->load->view('employee/show_booking_life_cycle', $data);
    }

    /**
     * @desc: This is used to print  address for selected booking
     * @param Array $booking_address
     */
    function download_shippment_address($booking_address) {
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id'));

        $booking_history['details'] = array();
        foreach ($booking_address as $key => $value) {
            $booking_history['details'][$key] = $this->booking_model->getbooking_history($value, "join")[0];
            $booking_history['details'][$key]['partner'] = $this->partner_model->getpartner($this->session->userdata('partner_id'))[0];
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
                    . " AND booking_details.current_status IN ('Pending', 'Rescheduled') ";
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
        $partner_id = $this->session->userdata('partner_id');
        $where = "spare_parts_details.partner_id = '" . $partner_id . "' "
                . " AND status IN ('Delivered', 'Shipped', '" . DEFECTIVE_PARTS_PENDING . "', '" . DEFECTIVE_PARTS_SHIPPED . "')  ";

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


        $select = "partner_logo,alt_text";
        $where = array('partner_logo IS NOT NULL' => NULL,'partner_id' => $this->session->userdata('partner_id'));
        $header_data['partner_logo'] = $this->booking_model->get_partner_logo($select,$where);
        $this->load->view('partner/header',$header_data);
        $this->load->view('partner/shipped_spare_part_booking', $data);
        $this->load->view('partner/partner_footer');
    }

    /**
     * @desc: Pending Defective Parts list 
     */
    function get_waiting_defective_parts($offset = 0, $all = 0) {
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id'));
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');

        $where = array(
            "spare_parts_details.defective_part_required" => 1,
            "approved_defective_parts_by_admin" => 1,
            "spare_parts_details.partner_id" => $partner_id,
            "status IN ('" . DEFECTIVE_PARTS_SHIPPED . "')  " => NULL
        );

        $select = "CONCAT( '', GROUP_CONCAT((defective_part_shipped ) ) , '' ) as defective_part_shipped, "
                . " spare_parts_details.booking_id, name, courier_name_by_sf, awb_by_sf,defective_part_shipped_date,remarks_defective_part_by_sf";

        $group_by = "spare_parts_details.booking_id";
        $order_by = "spare_parts_details.defective_part_shipped_date DESC";

        $config['base_url'] = base_url() . 'partner/get_waiting_defective_parts';
        $config['total_rows'] = $this->service_centers_model->count_spare_parts_booking($where, $select, $group_by);

        if ($all == 1) {
            $config['per_page'] = $config['total_rows'];
        } else {
            $config['per_page'] = 50;
        }
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['spare_parts'] = $this->service_centers_model->get_spare_parts_booking($where, $select, $group_by, $order_by, $offset, $config['per_page']);
        $where_internal_status = array("page" => "defective_parts", "active" => '1');
        $data['internal_status'] = $this->booking_model->get_internal_status($where_internal_status);
        $data['is_ajax'] = $this->input->post('is_ajax');
        if(empty($this->input->post('is_ajax'))){
            $select = "partner_logo,alt_text";
            $where = array('partner_logo IS NOT NULL' => NULL,'partner_id' => $this->session->userdata('partner_id'));
            $header_data['partner_logo'] = $this->booking_model->get_partner_logo($select,$where);
            $this->load->view('partner/header',$header_data);
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
    function acknowledge_received_defective_parts($booking_id, $partner_id, $is_cron = "") {
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id') . " Booking Id " . $booking_id);

        if (empty($is_cron)) {
            $this->checkUserSession();
        }

        $response = $this->service_centers_model->update_spare_parts(array('booking_id' => $booking_id), array('status' => DEFECTIVE_PARTS_RECEIVED,
            'approved_defective_parts_by_partner' => '1', 'remarks_defective_part_by_partner' => DEFECTIVE_PARTS_RECEIVED,
            'received_defective_part_date' => date("Y-m-d H:i:s")));
        if ($response) {

            log_message('info', __FUNCTION__ . " Received Defective Spare Parts " . $booking_id
                    . " Partner Id" . $this->session->userdata('partner_id'));
            $this->insert_details_in_state_change($booking_id, DEFECTIVE_PARTS_RECEIVED, "Partner Received Defective Spare Parts", $is_cron);

            $sc_data['current_status'] = "InProcess";
            $sc_data['internal_status'] = _247AROUND_COMPLETED;
            $this->vendor_model->update_service_center_action($booking_id, $sc_data);
            
            $booking['internal_status'] = DEFECTIVE_PARTS_RECEIVED;
        
            $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, $booking['internal_status'], $partner_id, $booking_id);
            if (!empty($partner_status)) {
                $booking['partner_current_status'] = $partner_status[0];
                $booking['partner_internal_status'] = $partner_status[1];
            }
            
            $this->booking_model->update_booking($booking_id, $booking);

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
     * @desc: Partner rejected Defective Parts with reason.
     * @param Sting $booking_id
     * @param Urlencoded $status (Rejection Reason)
     */
    function reject_defective_part($booking_id, $status) {
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id') . " Booking Id " . $booking_id . ' status: ' . $status);
        $this->checkUserSession();
        $rejection_reason = base64_decode(urldecode($status));

        $response = $this->service_centers_model->update_spare_parts(array('booking_id' => $booking_id), array('status' => DEFECTIVE_PARTS_REJECTED,
            'remarks_defective_part_by_partner' => $rejection_reason,
            'approved_defective_parts_by_partner' => '0'));
        if ($response) {
            log_message('info', __FUNCTION__ . " Sucessfully updated Table " . $booking_id
                    . " Partner Id" . $this->session->userdata('partner_id'));
            $this->insert_details_in_state_change($booking_id, $rejection_reason, DEFECTIVE_PARTS_REJECTED);

            $sc_data['current_status'] = "InProcess";
            $sc_data['internal_status'] = $rejection_reason;
            $this->vendor_model->update_service_center_action($booking_id, $sc_data);
            
            $booking['internal_status'] = DEFECTIVE_PARTS_REJECTED;
        
            $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, $booking['internal_status'], 
                    $this->session->userdata('partner_id'), $booking_id);
            if (!empty($partner_status)) {
                $booking['partner_current_status'] = $partner_status[0];
                $booking['partner_internal_status'] = $partner_status[1];
            }
            
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
        $partner_data = $this->partner_model->get_partner_code($partner_id);
        $partner_type = $partner_data[0]['partner_type'];
        if ($partner_type == OEM) {
            //Getting Unique values of Brands for Particular Partner and service id
            $where = array('partner_id' => $partner_id, 'service_id' => $service_id, "active" => 1);
            $data = $this->partner_model->get_partner_specific_details($where, "brand  As brand_name", "brand");
        } else {
            $data = $this->booking_model->getBrandForService($service_id);
        }

        $option = "";
        foreach ($data as $value) {
            $option .= "<option ";
            if ($appliace_brand == $value['brand_name']) {
                $option .= " selected ";
            }
            $option .= " value='" . $value['brand_name'] . "'>" . $value['brand_name'] . "</option>";
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
    function get_category_from_service() {
        $partner_id = $this->input->post('partner_id');
        $service_id = $this->input->post('service_id');
        $category = $this->input->post('category');
        $brand = $this->input->post('brand');
        $partner_type = $this->input->post('partner_type');
        
        if($this->input->post('is_mapping')){
            $where = array("service_id" => $service_id);
               
            $data = $this->service_centre_charges_model->getServiceCategoryMapping($where, "category","category");
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
        
        if($this->input->post("is_mapping")){
            
            $where = array("service_id" => $service_id);
            $where_in = array("category" => $category);
            $data = $this->service_centre_charges_model->getServiceCategoryMapping($where, "capacity","capacity");
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
    function get_model_for_partner() {
        $partner_id = $this->input->post('partner_id');
        $service_id = $this->input->post('service_id');
        $brand = $this->input->post('brand');
        $category = $this->input->post('category');
        $capacity = $this->input->post('capacity');
        $model_number = $this->input->post('model');
        $partner_type = $this->input->post('partner_type');

        if ($partner_type == OEM) {
            //Getting Unique values of Model for Particular Partner ,service id and brand
            $where = array("partner_id" => $partner_id, 'service_id' => $service_id, 'brand' => $brand, 'category' => $category, 'active'=> 1, 'capacity' => $capacity);

            $data = $this->partner_model->get_partner_specific_details($where, "model", "model");
        } else {
            $data[0]['model'] = "";
        }


        if (!empty($data[0]['model'])) {
            $model = "";
            foreach ($data as $value) {
                $model .= "<option ";
                if (trim($model_number) === trim($value['model'])) {
                    $model .= " selected ";
                } else if (count($data) == 1) {
                    $model .= " selected ";
                }
                $model .= " value='" . $value['model'] . "'>" . $value['model'] . "</option>";
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
        log_message('info', __FUNCTION__ . " Pratner ID: " . $this->session->userdata('partner_id'));

        $partner_id = $this->session->userdata('partner_id');
        $where = "spare_parts_details.partner_id = '" . $partner_id . "' "
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

        $select = "partner_logo,alt_text";
        $where = array('partner_logo IS NOT NULL' => NULL,'partner_id' => $this->session->userdata('partner_id'));
        $header_data['partner_logo'] = $this->booking_model->get_partner_logo($select,$where);
        $this->load->view('partner/header',$header_data);
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
        $data['escalation_reason'] = $this->vendor_model->getEscalationReason(array('entity' => 'partner', 'active' => '1'));
        $where = "spare_parts_details.partner_id = '" . $partner_id . "' AND status = '" . SPARE_PARTS_REQUESTED . "' "
                . " AND booking_details.current_status IN ('Pending', 'Rescheduled') ";
        $total_rows = $this->partner_model->get_spare_parts_booking_list($where, false, false, false);
        $data['spare_parts'] = $total_rows[0]['total_rows'];
        $select = "partner_logo,alt_text";
        $where = array('partner_logo IS NOT NULL' => NULL,'partner_id' => $this->session->userdata('partner_id'));
        $header_data['partner_logo'] = $this->booking_model->get_partner_logo($select,$where);
        $this->load->view('partner/header',$header_data);
        $this->load->view('partner/partner_default_page', $data);
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
            $select = "partner_logo,alt_text";
            $where = array('partner_logo IS NOT NULL' => NULL,'partner_id' => $this->session->userdata('partner_id'));
            $header_data['partner_logo'] = $this->booking_model->get_partner_logo($select,$where);
            $this->load->view('partner/header',$header_data);
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
        $result = array();

        if ($partner_type == OEM) {
            $result = $this->partner_model->getPrices($service_id, $category, $capacity, $partner_id, "", $brand);
        } else {
            $result = $this->partner_model->getPrices($service_id, $category, $capacity, $partner_id, "", "");
        }
        if (!empty($result)) {
            $p_where = array('id' => $partner_id);
            $partner_details = $this->partner_model->get_all_partner($p_where);
            if (empty($assigned_vendor_id)) {
                $data = $this->miscelleneous->check_upcountry_vendor_availability($city, $pincode, $service_id, $partner_details, NULL);
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

                $html .= "<tr class='text-center'><td>" . $prices['service_category'] . "</td>";
                $html .= "<td>" . $prices['customer_net_payable'] . "</td>";
                $html .= "<td><input type='hidden'name ='is_up_val' id='is_up_val_" . $i . "' value ='" . $prices['is_upcountry'] . "' /><input class='price_checkbox'";
                $html .= " type='checkbox' id='checkbox_" . $i . "'";
                $html .= "name='prices[]'";
                if (in_array($prices['service_category'], $explode)) {
                    $html .= " checked ";
                }
                $customer_total = $prices['customer_total'];
                $partner_net_payable = $prices['partner_net_payable'];
                if($prices['service_category'] == REPAIR_OOW_PARTS_PRICE_TAGS){
                     $html .= " readonly ";
                     if(!empty($booking_id)){
                         $unit_details = $this->booking_model->get_unit_details(array('booking_id' => $booking_id, "price_tags" =>REPAIR_OOW_PARTS_PRICE_TAGS), 
                                 false, "customer_total, partner_net_payable");
                         if(!empty($unit_details)){
                            $customer_total = $unit_details[0]['customer_total'];
                             $partner_net_payable = $unit_details[0]['partner_net_payable'];
                         }
                     }
                     
                }
                $html .= "  onclick='final_price(),set_upcountry()'" .
                        "value=" . $prices['id'] . "_" . intval($customer_total) . "_" . intval($partner_net_payable) . "_" . $i . " ></td><tr>";

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
        $select = 'vendor_pincode_mapping.City as district';
        $post_city = $this->input->post('city');
        $where = array(
            'service_centres.active' => 1,
            'service_centres.on_off' => 1,
            'vendor_pincode_mapping.Pincode' => $pincode,
            'vendor_pincode_mapping.Appliance_ID' => $service_id);
        $city = $this->vendor_model->get_vendor_mapping_data($where, $select);
        if (!empty($city)) {
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
        } else {
            $booking = array('booking_id' => NULL, 'booking_pincode' => $pincode, 'city' => NULL, 'service_id' => $service_id, 'partner_id' => $this->session->userdata('partner_id'));
            $this->miscelleneous->sf_not_exist_for_pincode($booking);
            echo 'ERROR';
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
                    $agent_id = _247AROUND_DEFAULT_AGENT;
                    $agent_name = _247AROUND_DEFAULT_AGENT_NAME;
                    $partner_id = _247AROUND;
                    $type = " Email";
                    $agent_type = _247AROUND_EMPLOYEE_STRING;
                } else {
                    $agent_id = $this->session->userdata('agent_id');
                    $agent_name = $this->session->userdata('partner_name');
                    $partner_id = $this->session->userdata('partner_id');
                    $type = " Panel";
                    $agent_type = _247AROUND_PARTNER_STRING;
                }
                // Insert log into booking state change
                $this->notify->insert_state_change($booking_id, UPCOUNTRY_CHARGES_APPROVED, _247AROUND_PENDING, "Upcountry Charges Approved From " . $type, $agent_id, $agent_name, $partner_id);

                $assigned = $this->miscelleneous->assign_vendor_process($data[0]['service_center_id'], $booking_id,$agent_id,$agent_type);
                if ($assigned) {

                    log_message('info', __FUNCTION__ . " => Continue Process" . $booking_id);
                    //Send SMS to customer
                    $sms['tag'] = "service_centre_assigned";
                    $sms['phone_no'] = $data[0]['booking_primary_contact_no'];
                    $sms['booking_id'] = $booking_id;
                    $sms['type'] = "user";
                    $sms['type_id'] = $data[0]['user_id'];
                    $sms['smsData'] = "";

                    $this->notify->send_sms_msg91($sms);
                    log_message('info', "Send SMS to customer: " . $booking_id);

                    //Prepare job card
                    $this->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);
                    $this->booking_utilities->lib_send_mail_to_vendor($booking_id, "");
                    log_message('info', "Async Process to create Job card: " . $booking_id);

                    $this->notify->insert_state_change($booking_id, ASSIGNED_VENDOR, UPCOUNTRY_CHARGES_APPROVED, "Service Center Id: " . $data[0]['service_center_id'], $agent_id, $agent_name, $partner_id);

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
            $to = NITS_ANUJ_EMAIL_ID;
            $cc = "vijaya@247around.com, abhaya@247around.com";
            $message = "Partner try to approve Booking Id " . $booking_id . " but somehow it failed. <br/>Please check this booking.";
            $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, '', 'UpCountry Approval Failed', $message, '');
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
            $partner_current_status = "";
            $partner_internal_status = "";
            $partner_status = $this->booking_utilities->get_partner_status_mapping_data("Cancelled", UPCOUNTRY_CHARGES_NOT_APPROVED, $data[0]['partner_id'], $booking_id);
            if (!empty($partner_status)) {
                $partner_current_status = $partner_status[0];
                $partner_internal_status = $partner_status[1];
            }
            if ($status == 0) {// means request from mail
                $agent_id = _247AROUND_DEFAULT_AGENT;
                $agent_name = _247AROUND_DEFAULT_AGENT_NAME;
                $partner_id = _247AROUND;
                $type = " Email";
            } else {
                $agent_id = $this->session->userdata('agent_id');
                $agent_name = $this->session->userdata('partner_name');
                $partner_id = $this->session->userdata('partner_id');
                $type = "Panel";
            }
            $this->booking_model->update_booking($booking_id, array("current_status" => "Cancelled", "internal_status" => UPCOUNTRY_CHARGES_NOT_APPROVED,
                'cancellation_reason' => UPCOUNTRY_CHARGES_NOT_APPROVED, "partner_current_status" => $partner_current_status,
                'partner_internal_status' => $partner_internal_status));

            $this->booking_model->update_booking_unit_details($booking_id, array('booking_status' => 'Cancelled'));
            $this->notify->insert_state_change($booking_id, UPCOUNTRY_CHARGES_NOT_APPROVED, _247AROUND_PENDING, "Upcountry Charges Rejected By Partner From " . $type, $agent_id, $agent_name, $partner_id);
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
        log_message('info', __FUNCTION__);
        $partner_id = $this->session->userdata('partner_id');
        $data['booking_details'] = $this->upcountry_model->get_waiting_for_approval_upcountry_charges($partner_id);
        $data['is_ajax'] = $this->input->post('is_ajax');
        if(empty($this->input->post('is_ajax'))){
            $select = "partner_logo,alt_text";
            $where = array('partner_logo IS NOT NULL' => NULL,'partner_id' => $this->session->userdata('partner_id'));
            $header_data['partner_logo'] = $this->booking_model->get_partner_logo($select,$where);
            $this->load->view('partner/header',$header_data);
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
                $partner_status = $this->booking_utilities->get_partner_status_mapping_data("Cancelled", UPCOUNTRY_CHARGES_NOT_APPROVED, $value['partner_id'], $value['booking_id']);
                if (!empty($partner_status)) {
                    $partner_current_status = $partner_status[0];
                    $partner_internal_status = $partner_status[1];
                }
                $this->booking_model->update_booking($value['booking_id'], array("current_status" => "Cancelled", "internal_status" => UPCOUNTRY_CHARGES_NOT_APPROVED,
                    'cancellation_reason' => UPCOUNTRY_CHARGES_NOT_APPROVED, "partner_current_status" => $partner_current_status,
                    'partner_internal_status' => $partner_internal_status));

                $this->booking_model->update_booking_unit_details($value['bookng_id'], array('booking_status' => 'Cancelled'));
                $this->notify->insert_state_change($value['booking_id'], UPCOUNTRY_CHARGES_NOT_APPROVED, _247AROUND_PENDING, "Upcountry Charges Rejected From " . "AUTO ", $agent_id, $agent_name, $partner_id);
            }

            //Notify
            $this->notify->sendEmail(NOREPLY_EMAIL_ID, ANUJ_EMAIL_ID, '', '', 'Upcountry Bookings Cancelled', print_r($data, TRUE), '');
        }
    }

    /**
     * @desc: This method is used to show the partner brand logo upload from
     * @param: void
     * @return:void
     */
    function upload_partner_brand_logo($id = "", $name = "") {
        $data['partner'] = array('partner_id' => $id,
            'public_name' => urldecode($name));
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
                $file_name = $partner_name . preg_replace("/[^a-zA-Z]+/", "", $_FILES["partner_brand_logo"]["name"][$key]) . rand(10, 100) . "." . end($ext);
                if (!file_exists(FCPATH . 'images/' . $file_name)) {
                    move_uploaded_file($tmpFile, FCPATH . 'images/' . $file_name);
                    //Uploading images to S3 
                    $bucket = BITBUCKET_DIRECTORY;
                    $directory = "misc-images/" . $file_name;
                    $this->s3->putObjectFile(TMP_FOLDER . $file_name, $bucket, $directory, S3::ACL_PUBLIC_READ);
                    $data['partner_id'] = $partner_id;
                    $data['partner_logo'] = 'images/' . $file_name;
                    $data['alt_text'] = $partner_name;

                    //insert partner brand logo path into database
                    $res[$key] = $this->partner_model->upload_partner_brand_logo($data);
                }
            }
            if ($res) {
                $this->session->set_flashdata('success', 'Partner Logo has been inserted successfully');
                redirect(base_url() . "employee/partner/upload_partner_brand_logo/" . $partner_id . "/" . $partner_name, 'refresh');
            } else {
                $this->session->set_flashdata('failed', 'Error in Inserting Partner Logo. Please Try Again...');
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
        $select = "partner_logo,alt_text";
        $where = array('partner_logo IS NOT NULL' => NULL,'partner_id' => $this->session->userdata('partner_id'));
        $header_data['partner_logo'] = $this->booking_model->get_partner_logo($select,$where);
        $this->load->view('partner/header',$header_data);
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
                $sendmail = $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, " ", " ", $subject, $message, "");

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
    function booking_upcountry_details($booking_id, $is_customer_paid) {
        if ($is_customer_paid > 0) {
            $is_customer_paid = 1;
        }
        $data['data'] = $this->upcountry_model->upcountry_booking_list("", $booking_id, false, $is_customer_paid);

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
                'DATEDIFF(defective_part_shipped_date,now()) <= -7' => null,
                "spare_parts_details.status IN ('Defective Part Shipped By SF')" => null,
                "booking_details.current_status IN ('Pending', 'Rescheduled')" => null);
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

                $sendmail = $this->notify->sendEmail($email_template[2], $to, $cc, "", $subject, $message, "");

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

            $select = "spare_parts_details.booking_id,DATE_FORMAT(spare_parts_details.defective_part_shipped_date, '%D %b %Y') as date";
            $where = array('spare_parts_details.partner_id' => $partner['id'],
                'DATEDIFF(defective_part_shipped_date,now()) <= -14' => null,
                "spare_parts_details.status IN ('Defective Part Shipped By SF')" => null,
                "booking_details.current_status IN ('Pending', 'Rescheduled')" => null);
            $defective_parts_acknowledge_data = $this->partner_model->get_spare_parts_by_any($select, $where, true);
            if (!empty($defective_parts_acknowledge_data)) {

                //update acknowledge
                foreach ($defective_parts_acknowledge_data as $value) {
                    $this->acknowledge_received_defective_parts($value['booking_id'], $partner['id'], true);
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

                $sendmail = $this->notify->sendEmail($email_template[2], $to, $cc, "", $subject, $message, "");

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
        $select = "DATE_FORMAT(closed_date, '%b') AS month, "
                . "SUM(IF(booking_details.current_status ='Completed' , 1, 0)) AS completed,"
                . "SUM(IF(booking_details.current_status ='Cancelled', 1, 0)) AS cancelled, "
                . "SUM(IF(booking_details.current_status ='Completed' AND booking_details.is_upcountry = '1' AND booking_details.upcountry_partner_approved = '1' AND booking_details.upcountry_paid_by_customer = '0' , 1, 0)) AS upcountry_completed ,"
                . "SUM(IF(booking_details.current_status ='Cancelled' AND booking_details.is_upcountry = '1' AND booking_details.upcountry_partner_approved = '1' AND booking_details.upcountry_paid_by_customer = '0', 1, 0)) AS upcountry_cancelled";
        $where = array('partner_id' => $partner_id, "booking_details.closed_date >= (DATE_FORMAT(CURDATE(), '%Y-%m-01') - INTERVAL 1 MONTH)" => NULL);
        $order_by = "YEAR(booking_details.closed_date),MONTH(booking_details.closed_date)";
        $group_by = "month";
        $data['bookings_count'] = $this->booking_model->get_bookings_count_by_any($select, $where, $order_by, $group_by);
        if(!empty($data['bookings_count']) && count($data['bookings_count']) == 2){
            $data['completed_booking'] = $data['bookings_count'][1]['completed'];
            $data['last_month_completed_booking'] = $data['bookings_count'][0]['completed'];
            $data['completed_booking_percentage_change'] = (($data['bookings_count'][1]['completed']/$data['bookings_count'][0]['completed'])*100)-100;
            $data['cancelled_booking'] = $data['bookings_count'][1]['cancelled'];
            $data['last_month_cancelled_booking'] = $data['bookings_count'][0]['cancelled'];
            $data['cancelled_booking_percentage_change'] = (($data['bookings_count'][1]['cancelled']/$data['bookings_count'][0]['cancelled'])*100)-100;
        }else if(!empty($data['bookings_count']) && count($data['bookings_count']) == 1){
            $data['completed_booking'] = $data['bookings_count'][0]['completed'];
            $data['last_month_completed_booking'] = 0;
            $data['completed_booking_percentage_change'] = (($data['bookings_count'][0]['completed']/$data['bookings_count'][0]['completed'])*100)-100;
            $data['cancelled_booking'] = $data['bookings_count'][0]['cancelled'];
            $data['last_month_cancelled_booking'] = 0;
            $data['cancelled_booking_percentage_change'] = (($data['bookings_count'][0]['cancelled']/$data['bookings_count'][0]['cancelled'])*100)-100;
        }else{
             $data['completed_booking'] = 0;
             $data['completed_booking_percentage_change'] = 0;
             $data['last_month_completed_booking'] = 0;
             $data['cancelled_booking'] = 0;
             $data['last_month_cancelled_booking'] = 0;
             $data['cancelled_booking_percentage_change'] = 0;
             
        }
        //get escalation percentage
        $data['escalation_percentage'] = $this->partner_model->get_booking_escalation_percantage($partner_id);
        if (!empty($this->session->userdata('is_prepaid'))) {
            $data['prepaid_amount'] = $this->get_prepaid_amount($partner_id);
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

        $where = array('active' => '1', 'on_off' => '1');
        $select = "district,state,pincode,appliances,non_working_days";
        $vendor = $this->vendor_model->getVendorDetails($select, $where, 'state');

        $template = 'Consolidated_SF_List_Template.xlsx';
        //set absolute path to directory with template files
        $templateDir = __DIR__ . "/../excel-templates/";
        //set config for report
        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );
        //load template
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

        $data = $this->partner_model->get_serviceability_by_pincode();

        $newCSVFileName = "serviceability_pincode_list_" . date('j-M-Y') . ".csv";
        $csv = TMP_FOLDER . $newCSVFileName;
        $delimiter = ",";
        $newline = "\r\n";
        $new_report = $this->dbutil->csv_from_result($data, $delimiter, $newline);
        write_file($csv, $new_report);

        //Downloading Generated CSV
        if (file_exists($csv)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($csv) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($csv));
            readfile($csv);
            exec("rm -rf " . escapeshellarg($csv));
            exit;
        }
    }

    /**
     * @desc This is used to get prepaid amount for requested partner 
     * @param int $partner_id
     * @return Array
     */
    function get_prepaid_amount($partner_id) {
        log_message("info", __METHOD__ . " Partner Id " . $partner_id);
        $p_details = $this->miscelleneous->get_partner_prepaid_amount($partner_id);

        if ($p_details['is_notification']) {

            $d['prepaid_amount'] = '<strong class="blink" style="color:red;">' . $p_details['prepaid_amount'] . '</strong> ';
        } else {
            $d['prepaid_amount'] = '<strong style="color:green;">' . $p_details['prepaid_amount'] . '</strong>';
        }

        $d['prepaid_msg'] = $p_details['prepaid_msg'];

        $userSession = array('status' => $p_details['active']);
        $this->session->set_userdata($userSession);
        return $d;
    }

    public function get_contact_us_page($partner_id) {
        $data['account_manager_details'] = $this->miscelleneous->get_am_data($partner_id);
        $data['rm_details'] = $this->employee_model->get_employee_by_group(array('groups' => 'regionalmanager', 'active' => 1));
        $select = "partner_logo,alt_text";
        $where = array('partner_logo IS NOT NULL' => NULL,'partner_id' => $this->session->userdata('partner_id'));
        $header_data['partner_logo'] = $this->booking_model->get_partner_logo($select,$where);
        $this->load->view('partner/header',$header_data);
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
            $pan_file = "Partner-" . $this->input->post('public_name') . '-PAN' . "." . explode(".", $_FILES['pan_file']['name'])[1];
            move_uploaded_file($tmpFile, TMP_FOLDER . $pan_file);

            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "vendor-partner-docs/" . $pan_file;
            $this->s3->putObjectFile(TMP_FOLDER . $pan_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            $return_data['partner']['pan_file'] = $pan_file;

            $attachment_pan = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $pan_file;

            //Logging success for file uppload
            log_message('info', __FUNCTION__ . ' PAN FILE is being uploaded sucessfully.');
        }

        //Processing Registration File
        if (($_FILES['registration_file']['error'] != 4) && !empty($_FILES['registration_file']['tmp_name'])) {
            $tmpFile = $_FILES['registration_file']['tmp_name'];
            $registration_file = "Partner-" . $this->input->post('public_name') . '-Registration' . "." . explode(".", $_FILES['registration_file']['name'])[1];
            move_uploaded_file($tmpFile, TMP_FOLDER . $registration_file);

            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "vendor-partner-docs/" . $registration_file;
            $this->s3->putObjectFile(TMP_FOLDER . $registration_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            $return_data['partner']['registration_file'] = $registration_file;

            $attachment_registration_file = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $registration_file;

            //Logging success for file uppload
            log_message('info', __FUNCTION__ . ' Registration FILE is being uploaded sucessfully.');
        }
        //Processing TIN File
        if (($_FILES['tin_file']['error'] != 4) && !empty($_FILES['tin_file']['tmp_name'])) {
            $tmpFile = $_FILES['tin_file']['tmp_name'];
            $tin_file = "Partner-" . $this->input->post('public_name') . '-TIN' . "." . explode(".", $_FILES['tin_file']['name'])[1];
            move_uploaded_file($tmpFile, TMP_FOLDER . $tin_file);

            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "vendor-partner-docs/" . $tin_file;
            $this->s3->putObjectFile(TMP_FOLDER . $tin_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            $return_data['partner']['tin_file'] = $tin_file;

            $attachment_tin_file = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $tin_file;

            //Logging success for file uppload
            log_message('info', __FUNCTION__ . ' TIN FILE is being uploaded sucessfully.');
        }
        //Processing CST File
        if (($_FILES['cst_file']['error'] != 4) && !empty($_FILES['cst_file']['tmp_name'])) {
            $tmpFile = $_FILES['cst_file']['tmp_name'];
            $cst_file = "Partner-" . $this->input->post('public_name') . '-CST' . "." . explode(".", $_FILES['cst_file']['name'])[1];
            move_uploaded_file($tmpFile, TMP_FOLDER . $cst_file);

            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "vendor-partner-docs/" . $cst_file;
            $this->s3->putObjectFile(TMP_FOLDER . $cst_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            $return_data['partner']['cst_file'] = $cst_file;

            $attachment_cst_file = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $cst_file;

            //Logging success for file uppload
            log_message('info', __FUNCTION__ . ' CST FILE is being uploaded sucessfully.');
        }
        //Processing Service Tax File
        if (($_FILES['service_tax_file']['error'] != 4) && !empty($_FILES['service_tax_file']['tmp_name'])) {
            $tmpFile = $_FILES['service_tax_file']['tmp_name'];
            $service_tax_file = "Partner-" . $this->input->post('public_name') . '-CST' . "." . explode(".", $_FILES['service_tax_file']['name'])[1];
            move_uploaded_file($tmpFile, TMP_FOLDER . $service_tax_file);

            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "vendor-partner-docs/" . $service_tax_file;
            $this->s3->putObjectFile(TMP_FOLDER . $service_tax_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            $return_data['partner']['service_tax_file'] = $registration_file;

            $attachment_service_tax_file = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $service_tax_file;

            //Logging success for file uppload
            log_message('info', __FUNCTION__ . ' Service Tax FILE is being uploaded sucessfully.');
        }
        $return_data['partner']['gst_number'] = $this->input->post("gst_number");
        $return_data['partner']['pan'] = $this->input->post("pan");
        $return_data['partner']['registration_no'] = $this->input->post("registration_no");
        $return_data['partner']['tin'] = $this->input->post("tin");
        $return_data['partner']['cst_no'] = $this->input->post("cst_no");
        $return_data['partner']['service_tax'] = $this->input->post("service_tax");
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
        $partnerName = $this->reusable_model->get_search_result_data("partners", "public_name", array('id' => $partner_id), NULL, NULL, NULL, NULL, NULL)[0]['public_name'];
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
                //Logging success for file uppload
                log_message('info', __FUNCTION__ . ' CONTRACT FILE is being uploaded sucessfully.');
                $insertArray = array("entity_id" => $partner_id, "entity_type" => "partner", "collateral_id" => $contract_type,
                    "document_description" => $contract_description_array[$index], 'file' => $contract_file, "start_date" => $start_date_array[$index], 'end_date' => $end_date_array[$index]);
                $finalInsertArray[] = $insertArray;
            }
        }
        if ($finalInsertArray) {
            $affacted_rows = $this->reusable_model->insert_batch("collateral", $finalInsertArray);
            if ($affacted_rows > 0) {
                $msg = "Partner Contracts has been Updated Successfully";
                $this->session->set_userdata('success', $msg);
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
                foreach ($services as $value) {
                    // checking, array has service id as a key 
                    if (array_key_exists($value->id, $formdata['brand'])) {
                        $where = array("partner_id" => $partner_id, "service_id" => $value->id);
                        $existingdata = $this->partner_model->get_partner_specific_details($where, "brand");
                        $existing = array_column($existingdata, 'brand');
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
        $select = "partner_logo,alt_text";
        $where = array('partner_logo IS NOT NULL' => NULL,'partner_id' => $this->session->userdata('partner_id'));
        $header_data['partner_logo'] = $this->booking_model->get_partner_logo($select,$where);
        $this->load->view('partner/header',$header_data);
        $this->load->view('partner/reset_partner_passsword');
        $this->load->view('partner/partner_footer');
    }
    
    /**
     * @desc: This function is used to get partner details from Ajax call
     * @params: void
     * @return: string
     */
    function get_partner_list(){
        $partner_list = $this->partner_model->get_all_partner(array('is_active'=>1));
        $option = '<option selected="" disabled="">Select Partner</option>';

        foreach ($partner_list as $value) {
            $option .= "<option value='" . $value['id'] . "'";
            $option .= " > ";
            $option .= $value['public_name'] . "</option>";
        }
        echo $option;
    }
    
     /**
     * @desc: This function is used to upload the challan file when partner shipped spare parts
     * @params: void
     * @return: $res
     */
    function upload_challan_file($id) {
        if (empty($_FILES['challan_file']['error'])) {
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

}
