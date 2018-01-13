<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 36000);

define('Partner_Integ_Complete', TRUE);

class Booking extends CI_Controller {

    /**
     * load list model and helpers
     */
    function __Construct() {
        parent::__Construct();
        $this->load->model('employee_model');
        $this->load->model('booking_model');
        $this->load->model('user_model');
        $this->load->model('reusable_model');
        $this->load->model('vendor_model');
        $this->load->model('invoices_model');
        $this->load->model('service_centers_model');
        $this->load->model('partner_model');
        $this->load->model('inventory_model');
        $this->load->model('upcountry_model');
        $this->load->model('penalty_model');
        $this->load->model("dealer_model");
        $this->load->library('partner_sd_cb');
        $this->load->library('partner_cb');
        $this->load->library('notify');
        $this->load->helper(array('form', 'url','array'));
        $this->load->library("miscelleneous");
        $this->load->library('form_validation');
        $this->load->library("pagination");
        $this->load->library("session");
        $this->load->library('s3');
        $this->load->library('email');
        $this->load->library('notify');
        $this->load->library('booking_utilities');
        $this->load->library('partner_sd_cb');
        $this->load->library('asynchronous_lib');
        $this->load->library("initialized_variable");
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            redirect(base_url() . "employee/login");
        }
    }

    /**
     *  @desc : This method is used to add a NEW Booking or Query. This is NOT used
     * to update existing Booking or Query.
     *
     * This function will get all the booking details.
     * These booking details are the details which are inserted in booking details table
     * while taking the actual booking.
     *
     * After insertion of booking details, if it is not a query, then an email and SMS
     * will be sent to the user for booking confirmation.

     *  @param : user id
     *
     *  @return : void
     */
    public function index() {
        if ($this->input->post()) {
            $primary_contact_no = $this->input->post('booking_primary_contact_no');
            $user_id = $this->input->post("user_id");
            
            //Check Validation
            $checkValidation = $this->validate_booking();
            if ($checkValidation) {
                log_message('info', __FUNCTION__);
                log_message('info', " Booking Insert Contact No: " . $primary_contact_no);
                $status = $this->getAllBookingInput($user_id, INSERT_NEW_BOOKING);
                if ($status) {
                    log_message('info', __FUNCTION__ . " Partner callback  " . $status['booking_id']);
                    $this->partner_cb->partner_callback($status['booking_id']);
                    //Redirect to Default Search Page
                    redirect(base_url() . DEFAULT_SEARCH_PAGE);
                } else {

                    $this->addbooking($primary_contact_no);
                }
            } else {
                //Redirect to edit booking page if validation err occurs
                if (!empty($primary_contact_no)) {
                    $this->addbooking($primary_contact_no);
                } else {
                    //Redirect to Default Search Page if Primary Phone number not found in Post
                    redirect(base_url() . DEFAULT_SEARCH_PAGE);
                }
            }
        } else {
            //Logging error message if No input is provided
            log_message('info', __FUNCTION__ . " Error in Booking Insert User ID: " . $user_id);
            $heading = "247Around Booking Error";
            $message = "Oops... No input provided !";
            $error = & load_class('Exceptions', 'core');
            echo $error->show_error($heading, $message, 'custom_error');
        }
    }

    /**
     *  @desc : This function is used to insert or update data in booking unit details and appliance details table
     *  @param : user id, booking id (optional)
     *  @return : Array(booking details)
     */
    function getAllBookingInput($user_id, $booking_id) {
        log_message('info', __FUNCTION__);
        log_message('info', " Booking Insert " . $user_id . " Booking ID" . $booking_id . " Done By " . $this->session->userdata('employee_id'));

        $updated_unit_id = array();

        // All brand comming in array eg-- array([0]=> LG, [1]=> BPL)
        $appliance_brand = $this->input->post('appliance_brand');
        $upcountry_data_json = $this->input->post('upcountry_data');
        $upcountry_data = json_decode($upcountry_data_json, TRUE);
        $booking = $this->insert_data_in_booking_details($booking_id, $user_id, count($appliance_brand));
        
        $user['user_id'] = $user_id = $booking['user_id'];
        
        if ($booking) {

            // All category comming in array eg-- array([0]=> TV-LCD, [1]=> TV-LED)
            $appliance_category = $this->input->post('appliance_category');
            // All capacity comming in array eg-- array([0]=> 19-30, [1]=> 31-42)
            $appliance_capacity = $this->input->post('appliance_capacity');
            // All model number comming in array eg-- array([0]=> ABC123, [1]=> CDE1478)
            $model_number = $this->input->post('model_number');
            // All price tag comming in array  eg-- array([0]=> Appliance tag1, [1]=> Appliance tag1)
            //$appliance_tags = $this->input->post('appliance_tags');
            // All purchase year comming in array eg-- array([0]=> 2016, [1]=> 2002)
            $purchase_year = $this->input->post('purchase_year');
            // All purchase month comming in array eg-- array([0]=> Jan, [1]=> Feb)
            $months = $this->input->post('purchase_month');

            $appliance_id_array = $this->input->post('appliance_id');
            $appliance_id = array();
            if (isset($appliance_id_array)) {
                if (!empty($appliance_id_array)) {
                    $appliance_id = array_unique($appliance_id_array);
                }
            }
            // Do not un comment until serial input added in the form
            // $serial_number = $this->input->post('serial_number');

            $partner_net_payable = $this->input->post('partner_paid_basic_charges');
            $appliance_description = $this->input->post('appliance_description');

            // All discount comming in array.  Array ( [BPL] => Array ( [100] => Array ( [0] => 200 ) [102] => Array ( [0] => 100 ) [103] => Array ( [0] => 0 ) ) .. Key is Appliance brand, unit id and discount value.
            $discount = $this->input->post('discount');
            // All prices comming in array with pricing table id
            /* Array([BPL] => Array([0] => 100_300 [1] => 102_250) [Micromax] => Array([0] => 100_300)) */
            //Array ( ['brand'] => Array ( [0] => id_price ) )
            $pricesWithId = $this->input->post("prices");
            $user['user_email'] = $this->input->post('user_email');
            $user['alternate_phone_number'] = $this->input->post('booking_alternate_contact_no');
            $result = array();
            $result['DEFAULT_TAX_RATE'] = 0;
            foreach ($appliance_brand as $key => $value) {

                $services_details = array();
                $appliances_details = array();
                $appliances_details['user_id'] = $booking['user_id'];;

                $appliances_details['brand'] = $services_details['appliance_brand'] = $value; // brand
                // get category from appiance category array for only specific key.
                $appliances_details['category'] = $services_details['appliance_category'] = $appliance_category[$key];
                // get appliance_capacity from appliance_capacity array for only specific key.
                $appliances_details['capacity'] = $services_details['appliance_capacity'] = $appliance_capacity[$key];
                // get model_number from appliance_capacity array for only specific key such as $model_number[0].
                $appliances_details['model_number'] = $services_details['model_number'] = $model_number[$key];
                // get appliance tag from appliance_tag array for only specific key such as $appliance_tag[0].
                //$appliances_details['tag']  = $appliance_tags[$key];
                // get purchase year from purchase year array for only specific key such as $purchase_year[0].
                $appliances_details['purchase_year'] = $services_details['purchase_year'] = $purchase_year[$key];
                $services_details['booking_id'] = $booking['booking_id'];

                //$appliances_details['serial_number'] = $services_details['serial_number'] = $serial_number[$key];

                $appliances_details['description'] = $services_details['appliance_description'] = $appliance_description[$key];
                // get purchase months from months array for only specific key such as $months[0].
                $appliances_details['purchase_month'] = $services_details['purchase_month'] = $months[$key];
                $appliances_details['service_id'] = $services_details['service_id'] = $booking['service_id'];
                $appliances_details['last_service_date'] = date('Y-m-d H:i:s');

                $services_details['partner_id'] = $booking['partner_id'];

                log_message('info', __METHOD__ . "Appliance ID" . print_r($appliance_id, true));
                /* if appliance id exist the initialize appliance id in array and update appliance 
                 * details other wise insert appliance details and return appliance id
                 * */
                if (!empty($appliances_details['description'])) {
                    // check appliance description exist
                    $check_product_type = $this->reusable_model->get_search_query('appliance_product_description','*',array('product_description' => trim($appliances_details['description'])),NULL,NULL,NULL,NULL,NULL)->result_array();
                    
                    /*if appliance description does not exist then verify the details and insert into the table
                     * and if appliance description exist and not verified then verify the details and update the table
                     * and if appliance description exist and verified then do not perform any action
                    */
                    if((isset($check_product_type[0]['id']) && empty($check_product_type[0]['is_verified'])) || empty($check_product_type)){
                        
                        //verify appliance details
                        $verified_capacity = $this->miscelleneous->verified_appliance_capacity($appliances_details);
                        
                        //if appliance description does not exist then insert the verified data else update the verified data
                        if (empty($check_product_type)) {
                            $insert_data = array('service_id' => $appliances_details['service_id'],
                                'category' => isset($verified_capacity['category'])?$verified_capacity['category']:$appliances_details['category'],
                                'capacity' => isset($verified_capacity['capacity'])?$verified_capacity['capacity']:$appliances_details['capacity'],
                                'brand' => isset($verified_capacity['brand'])?$verified_capacity['brand']:$appliances_details['brand'],
                                'product_description' => trim($appliances_details['description']),
                                'is_verified' => $verified_capacity['is_verified']);
                            $this->booking_model->insert_appliance_details($insert_data);
                        }else{
                            $new_appliance_data = array(
                                        'category' => isset($verified_capacity['category'])?$verified_capacity['category']:$appliances_details['category'],
                                        'capacity' => isset($verified_capacity['capacity'])?$verified_capacity['capacity']:$appliances_details['capacity'],
                                        'brand' => isset($verified_capacity['brand'])?$verified_capacity['brand']:$appliances_details['brand'],
                                        'is_verified' => $verified_capacity['is_verified']
                                    );

                            $appliance_where = array(
                                        'category' => $check_product_type[0]['category'],
                                        'capacity' => $check_product_type[0]['capacity'],
                                        'product_description' => trim($appliances_details['description']),
                                        'brand' => $check_product_type[0]['brand']
                                    );
                            $this->booking_model->update_appliance_description_details($new_appliance_data,$appliance_where);
                        }
                    }
                    
                }

                if (isset($appliance_id[$key])) {

                    $services_details['appliance_id'] = $appliance_id[$key];
                    $this->booking_model->update_appliances($services_details['appliance_id'], $appliances_details);
                } else {

                    $services_details['appliance_id'] = $this->booking_model->addappliance($appliances_details);
                    log_message('info', __METHOD__ . " New Appliance ID created: " . print_r($services_details['appliance_id'], true));
                }
                log_message('info', __METHOD__ . "Appliance details data" . print_r($appliances_details, true));

                $where = array('service_id' => $booking['service_id'], 'brand_name' => trim($value));
                $brand_id_array = $this->booking_model->get_brand($where);

                if (!empty($brand_id_array)) {
                    $brand_id = $brand_id_array[0]['id'];
                } else {
                    $brand_id = "";
                }

                //Array ( ['brand'] => Array ( [0] => id_price ) )
                foreach ($pricesWithId[$brand_id][$key + 1] as $b_key => $values) {

                    $prices = explode("_", $values);  // split string..
                    $services_details['id'] = $prices[0]; // This is id of service_centre_charges table.

                    $services_details['around_paid_basic_charges'] = $discount[$brand_id][$key + 1][$services_details['id']][0];
                    $services_details['partner_paid_basic_charges'] = $partner_net_payable[$brand_id][$key + 1][$services_details['id']][0];
                    $services_details['partner_net_payable'] = $services_details['partner_paid_basic_charges'];
                    $services_details['around_net_payable'] = $services_details['around_paid_basic_charges'];
                    $services_details['booking_status'] = $booking['current_status'];
                    log_message('info', __METHOD__ . " Before booking is insert/update: " . $booking_id);

                    switch ($booking_id) {
                        case INSERT_NEW_BOOKING:
                            log_message('info', __METHOD__ . " Insert Booking Unit Details: ");
                            $result = $this->booking_model->insert_data_in_booking_unit_details($services_details, $booking['state'], $b_key);
                            break;
                        default:

                            log_message('info', __METHOD__ . " Update Booking Unit Details: " . " Previous booking id: " . $booking_id);
                            $agent_details['agent_id'] = $this->session->userdata('id');
                            $agent_details['agent_type'] = _247AROUND_EMPLOYEE_STRING;
                            $result = $this->booking_model->update_booking_in_booking_details($services_details, $booking_id, $booking['state'], $b_key,$agent_details);

                            array_push($updated_unit_id, $result['unit_id']);
                            break;
                    }
                }
            }
            if (!empty($updated_unit_id)) {
                log_message('info', __METHOD__ . " UNIT ID: " . print_r($updated_unit_id, true));
                $sf_id = $this->reusable_model->get_search_query('booking_details','assigned_vendor_id',array('booking_id'=>$booking_id),NULL,NULL,NULL,NULL,NULL)->result_array();
                if(!empty($sf_id)){
                    $inventory_details = array('receiver_entity_id' => $sf_id[0]['assigned_vendor_id'],
                                                'receiver_entity_type' => _247AROUND_SF_STRING,
                                                'stock' => 1,
                                                'agent_id' => $this->session->userdata('id'),
                                                'agent_type' => _247AROUND_EMPLOYEE_STRING,
                                                );
                }else{
                    $inventory_details = array();
                }
                $this->booking_model->check_price_tags_status($booking['booking_id'], $updated_unit_id,$inventory_details);
            }
            if($booking_id != INSERT_NEW_BOOKING){
                $this->user_model->edit_user($user);
            }            
            
            if ($booking['type'] == 'Booking') {

                if (empty($booking['state'])) {
                    log_message('info', __FUNCTION__ . " Pincode Not Found Booking Id: " . $booking['booking_pincode']);
                    $this->send_sms_email($booking_id, "Pincode_not_found");
                }

                //1 means booking is getting inserted for the first time
                //2 means booking is getting updated
                if ($booking['is_send_sms'] == 1) {
                    //Query converted to Booking OR New Booking Inserted
                    $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
                    $send['booking_id'] = $booking['booking_id'];
                    $send['state'] = "Newbooking";
                    $this->asynchronous_lib->do_background_process($url, $send);

                    //Assign Vendor
                    //log_message("info"," upcountry_data", print_r($upcountry_data). " Booking id ". $booking['booking_id']);
                    switch ($upcountry_data['message']) {
                        case UPCOUNTRY_BOOKING:
                        case UPCOUNTRY_LIMIT_EXCEED:
                        case NOT_UPCOUNTRY_BOOKING:
                        case UPCOUNTRY_DISTANCE_CAN_NOT_CALCULATE:
                            $url = base_url() . "employee/vendor/process_assign_booking_form/";
                            $async_data['service_center'] = array($booking['booking_id'] => $upcountry_data['vendor_id']);
                            $async_data['agent_id'] = _247AROUND_DEFAULT_AGENT;
                            $async_data['agent_name'] = _247AROUND_DEFAULT_AGENT_NAME;
                            $async_data['agent_type'] = _247AROUND_EMPLOYEE_STRING;
                            $this->asynchronous_lib->do_background_process($url, $async_data);

                            break;
                        case SF_DOES_NOT_EXIST:
                            break;
                    }
                } else if ($booking['is_send_sms'] == 2 || $booking_id != INSERT_NEW_BOOKING) {
                    //Pending booking getting updated, not query getting converted to booking
                    $up_flag = 1;

                    $url = base_url() . "employee/vendor/update_upcountry_and_unit_in_sc/" . $booking['booking_id'] . "/" . $up_flag;
                    $async_data['booking'] = array();
                    $this->asynchronous_lib->do_background_process($url, $async_data);
                }
            }
            if(isset($upcountry_data['vendor_not_found'])){
                                $this->miscelleneous->sf_not_exist_for_pincode(array(
                                    "booking_id" => $booking['booking_id'],
                                    "city" => $booking['city'],
                                    "booking_pincode" => $booking['booking_pincode'],
                                     "service_id" => $appliances_details['service_id'],
                                      "partner_id" => $booking['partner_id']
                                ));
                            }
            return $booking;
        } else {
            log_message('info', __FUNCTION__ . " Booking Failed!");
            return false;
        }
    }

    /**
     * @desc: This method get input file dand insert booking details
     * @param String $booking_id
     * @param String $user_id
     * @param String $quantity
     * @return boolean
     */
    function insert_data_in_booking_details($booking_id, $user_id, $quantity) {
        $booking = $this->get_booking_input($user_id);
        
        $remarks = $this->input->post('query_remarks');

        $booking['quantity'] = $quantity;
       
        switch ($booking_id) {
            case INSERT_NEW_BOOKING:
                $booking['booking_id'] = $this->create_booking_id($booking['user_id'], $booking['source'], $booking['type'], $booking['booking_date']);
                $is_send_sms = 1;
                $booking_id_with_flag['new_state'] = _247AROUND_PENDING;
                $booking_id_with_flag['old_state'] = _247AROUND_NEW_BOOKING;

                log_message('info', "New Booking ID created" . print_r($booking['booking_id'], true));
                break;
            default :
                if ($booking['type'] == "Booking") {
                    //Query remarks has either query or booking remarks
                    $booking_id_with_flag = $this->change_in_booking_id($booking['type'], $booking_id, $this->input->post('query_remarks'));
                } else {
                    //Internal status has query remarks only
                    $booking_id_with_flag = $this->change_in_booking_id($booking['type'], $booking_id, $this->input->post('internal_status'));
                }

                $booking['booking_id'] = $booking_id_with_flag['booking_id'];
                $is_send_sms = $booking_id_with_flag['query_to_booking'];
                log_message('info', " Booking Updated: " . print_r($booking['booking_id'], true) . " Query to booking: " . print_r($is_send_sms, true));

                break;
        }
        
        //add support file for order id if it is uploaded
        $support_file = $this->upload_orderId_support_file($booking['booking_id']);
        if ($support_file) {
            $booking['support_file'] = $support_file;
        }
        

        $validate_order_id = $this->validate_order_id($booking['partner_id'], $booking['booking_id'], $booking['order_id'], $booking['amount_due']);
      
        if ($validate_order_id) {
            $is_dealer = $this->dealer_process($booking['city'], $booking['partner_id'], $booking['service_id']);
           
            if(!empty($is_dealer)){
                $booking['dealer_id'] = $is_dealer;
            }

            if ($booking['type'] == 'Booking') {
                $booking['current_status'] = 'Pending';
                $booking['internal_status'] = 'Scheduled';
                $booking['initial_booking_date'] = $booking['booking_date'];
                $booking['booking_remarks'] = $remarks;
                $new_state = $booking_id_with_flag['new_state'];
                $old_state = $booking_id_with_flag['old_state'];
            } else if ($booking['type'] == 'Query') {

                $booking['current_status'] = "FollowUp";
                $internal_status = $this->input->post('internal_status');
                if (!empty($internal_status)) {
                    $booking['internal_status'] = $internal_status;
                } else {
                    $booking['internal_status'] = "FollowUp";
                }
                if ($booking['internal_status'] == INT_STATUS_CUSTOMER_NOT_REACHABLE) {
                    $this->send_sms_email($booking_id, "Customer not reachable");
                }

                $booking['query_remarks'] = $remarks;

                $new_state = $booking_id_with_flag['new_state'];
                $old_state = $booking_id_with_flag['old_state'];
            }

            // check partner status
            $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'], $booking['partner_id'], $booking_id);
            if (!empty($partner_status)) {
                $booking['partner_current_status'] = $partner_status[0];
                $booking['partner_internal_status'] = $partner_status[1];
            }

            switch ($booking_id) {

                case INSERT_NEW_BOOKING:
                    $booking['create_date'] = date("Y-m-d H:i:s");
                
                    $status = $this->booking_model->addbooking($booking);
                    if ($status) {
                        $booking['is_send_sms'] = $is_send_sms;
                        if ($booking['is_send_sms'] == 1) {
                            $upcountry_data_json = $this->input->post('upcountry_data');
                            $upcountry_data = json_decode($upcountry_data_json, TRUE);

                            switch ($upcountry_data['message']) {
                                case UPCOUNTRY_BOOKING:
                                case UPCOUNTRY_LIMIT_EXCEED:
                                    $booking['is_upcountry'] = 1;
                                    break;
                            }
                        }
                    } else {
                        return false;
                    }

                    break;

                default :
                    $status = $this->booking_model->update_booking($booking_id, $booking);
                    if ($status) {
                        $booking['is_send_sms'] = $is_send_sms;
                    } else {
                        return false;
                    }
                    break;
            }

            $this->notify->insert_state_change($booking['booking_id'], $new_state, $old_state, $remarks, $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);

            return $booking;
        } else {
            return false;
        }
    }
    
    function dealer_process($city, $partner_id, $service_id){
        $dealer_phone_number = $this->input->post("dealer_phone_number");
        $dealer_id = "";
        if(!empty($dealer_phone_number)){
            $data['city'] = $city;
            $data['dealer_id'] = $this->input->post("dealer_id");
            $data['dealer_name'] = $this->input->post("dealer_name");
            $data['dealer_phone_number'] = $dealer_phone_number;
            $data['service_id'] = $service_id;
            $data['brand'] = $this->input->post('appliance_brand')[0];
            $is_dealer_id = $this->miscelleneous->dealer_process($data, $partner_id);
            if (!empty($is_dealer_id)) {
                $dealer_id = $is_dealer_id;
            }
        }
        
        return $dealer_id;
    }

    /**
     * @desc: This method is used to send sms while Customer not reachable in Pending Queries.
     * This is Asynchronous Process
     * @param  $booking_id
     */
    function send_sms_email($booking_id, $state) {
        log_message('info', __FUNCTION__ . " Booking ID :" . print_r($booking_id, true));
        $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
        $send['booking_id'] = $booking_id;
        $send['state'] = $state;
        $this->asynchronous_lib->do_background_process($url, $send);
    }

    /**
     * @desc: this method returns Booking data in array
     * @return Array
     */
    function get_booking_input($user_id) {
        log_message('info', __FUNCTION__);
        $booking['service_id'] = $this->input->post('service_id');
        $booking['source'] = $this->input->post('source_code');
        $booking['type'] = $this->input->post('type');
        $booking['amount_due'] = $this->input->post('grand_total_price');
        $booking['booking_address'] = $this->input->post('home_address');
        $booking['city'] = $this->input->post('city');
        $booking_date = $this->input->post('booking_date');
        $booking['partner_source'] = $this->input->post('partner_source');
        $booking['booking_date'] = date('d-m-Y', strtotime($booking_date));
        $booking['booking_pincode'] = trim($this->input->post('booking_pincode'));
        // select state, taluk, district by pincode
        $distict_details = $this->vendor_model->get_distict_details_from_india_pincode(trim($booking['booking_pincode']));
        $booking['state'] = $distict_details['state'];
        $booking['district'] = $distict_details['district'];
        $booking['taluk'] = $distict_details['taluk'];
        $booking['booking_primary_contact_no'] = $this->input->post('booking_primary_contact_no');
        $booking['order_id'] = $this->input->post('order_id');
        $booking['booking_alternate_contact_no'] = $this->input->post('booking_alternate_contact_no');
        $booking['booking_timeslot'] = $this->input->post('booking_timeslot');
        $booking['update_date'] = date("Y-m-d H:i:s");
        $booking['partner_id'] = $this->input->post('partner_id');
        
        if(empty($user_id)){
            $user['phone_number'] = $booking['booking_primary_contact_no'];
            $user['name'] = $this->input->post('user_name');
            $user['user_email'] = $this->input->post('user_email');
            $user['home_address'] = $booking['booking_address'];
            $user['city'] = $booking['city'];
            $user['state'] =  $booking['state'];
            $user['pincode'] = $booking['booking_pincode'] ;
            $user['alternate_phone_number'] = $booking['booking_alternate_contact_no'];
            $user['create_date'] = date("Y-m-d H:i:s");
        
            $user_id = $this->user_model->add_user($user);
            

            $this->booking_model->addSampleAppliances($user_id, 5);
        }
        
        $booking['user_id'] = $user_id;

        return $booking;
    }

    /**
     * @desc: This method returns booking id when booking is updated:
     * Pending Booking to Pending Query
     * OR Pending Query to Pending Booking
     * OR Pending Booking to Pending booking
     * OR Pending Query to Pending Query
     *
     * @param type $booking_type - New type to which booking would be converted
     * @param type $booking_id
     *
     * @return booking id
     */
    function change_in_booking_id($booking_type, $booking_id) {
        $data['booking_id'] = $booking_id;
        $data['query_to_booking'] = '0';

        log_message('info', __FUNCTION__ . " Booking ID: " . $booking_id." Booking Type: ".$booking_type);

        switch ($booking_type) {
            case "Booking":
                if (strpos($booking_id, "Q-") !== FALSE) {
                    //Query to be converted to Booking
                    $booking_id_array = explode("Q-", $booking_id);
                    $data['booking_id'] = $booking_id_array[1];
                    $data['query_to_booking'] = '1';

                    $data['old_state'] = _247AROUND_FOLLOWUP;
                    $data['new_state'] = _247AROUND_PENDING;

                    log_message('info', __FUNCTION__ . " Query Converted to Booking Booking ID" . print_r($data['booking_id'], true));
                } else {
                    //Booking to be updated to booking
                    $data['booking_id'] = $booking_id;

                    $data['old_state'] = _247AROUND_PENDING;
                    $data['new_state'] = _247AROUND_PENDING;
                    $data['query_to_booking'] = '2';

                    log_message('info', __FUNCTION__ . " Booking Updateded to Booking Booking ID" . print_r($data['booking_id'], true));
                }

                break;

            case "Query":
                if (strpos($booking_id, "Q-") === FALSE) {
                    //Booking to be converted to query
                    $data['booking_id'] = "Q-" . $booking_id;
                    log_message('info', __FUNCTION__ . " Booking to be Converted to Query Booking ID" . print_r($data['booking_id'], true));

                    $data['old_state'] = _247AROUND_PENDING;
                    $data['new_state'] = _247AROUND_FOLLOWUP;

                    //Reset the assigned vendor ID for this booking
                    $this->booking_model->update_booking($booking_id, array("assigned_vendor_id" => NULL));
                } else {
                    //Query to be updated to query
                    $data['booking_id'] = $booking_id;
                    log_message('info', __FUNCTION__ . " Query to be updated to Query Booking ID" . print_r($data['booking_id'], true));

                    $data['old_state'] = _247AROUND_FOLLOWUP;
                    $data['new_state'] = _247AROUND_FOLLOWUP;
                }

                break;
        }

        return $data;
    }

    /**
     * @desc: this method generates booking id. booking id is the combination of booking source, 4 digit random number, date and month
     * @param: user id, booking source, booking type
     * @return: booking id
     */
    function create_booking_id($user_id, $source, $type, $booking_date) {
        $booking['booking_id'] = '';

        $yy = date("y", strtotime($booking_date));
        $mm = date("m", strtotime($booking_date));
        $dd = date("d", strtotime($booking_date));

        $booking['booking_id'] = str_pad($user_id, 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
        $booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($user_id)) + 1);


        //Add source
        $booking['source'] = $source;
        if ($type == "Booking") {
            $booking['booking_id'] = $booking['source'] . "-" . $booking['booking_id'];
        } else {
            $booking['booking_id'] = "Q-" . $booking['source'] . "-" . $booking['booking_id'];
        }

        return $booking['booking_id'];
    }

    /**
     * @desc : This function loads add booking form with city, booking source, service and user details
     * @param: String(Phone Number)
     * @return : void
     */
    function addbooking($phone_number) {
        $data = $this->booking_model->get_city_source();
        $data['user'] = $this->user_model->search_user($phone_number);
        $data['phone_number'] = $phone_number;
        $where_internal_status = array("page" => "FollowUp", "active" => '1');
        $data['follow_up_internal_status'] = $this->booking_model->get_internal_status($where_internal_status);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/addbooking', $data);
    }

    /**
     *  @desc : This function displays list of pending bookings according to pagination and also show all booking if $page is All.
     *  @param : Starting page & number of results per page
     *  @return : pending bookings according to pagination
     */
    function view($page = 0, $offset = '0', $booking_id = "") {

        if ($page == 0) {
            $page = 50;
        }
        // $offset = ($this->uri->segment(5) != '' ? $this->uri->segment(5) : 0);

        $config['base_url'] = base_url() . 'employee/booking/view/' . $page;
        $config['total_rows'] = $this->booking_model->total_pending_booking($booking_id);

        if ($offset != "All") {
            $config['per_page'] = $page;
        } else {
            $config['per_page'] = $config['total_rows'];
        }

        $config['uri_segment'] = 5;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';

        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['Count'] = $config['total_rows'];
        $data['Bookings'] = $this->booking_model->date_sorted_booking($config['per_page'], $offset, $booking_id);
        
        if ($this->session->flashdata('result') != '') {
            $data['success'] = $this->session->flashdata('result');
        }
        if (isset($_SESSION['result'])) {
            unset($_SESSION['result']);
        }
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/booking', $data);
    }

    /**
     *  @desc : This function returns the cancelation reason for booking
     *  @param : void
     *  @return : all the cancelation reasons present in the database
     */
    function cancelreason() {
        $where = array('reason_of' => '247around');
        $query = $this->booking_model->cancelreason($where);
        $data['reason'] = null;
        if ($query) {

            $data['reason'] = $query;
        }

        $this->load->view('employee/cancelbooking', $data);
    }

    /**
     *  @desc : This function is to select booking to be completed
     *
     * Opens a form with basic booking details and feilds to be filled before completing the booking like amount collected, amount collected by, etc.
     *
     *  @param : booking id
     *  @return : user details and booking history to view
     */
    function get_complete_booking_form($booking_id) {
        log_message('info', __FUNCTION__ . " Booking ID: " . print_r($booking_id, true));
        $data['booking_id'] = $booking_id;
        $data['booking_history'] = $this->booking_model->getbooking_history($booking_id);
        $data['booking_unit_details'] = $this->booking_model->getunit_details($booking_id);
        $source = $this->partner_model->getpartner_details('bookings_sources.source, partner_type', 
                array('bookings_sources.partner_id' => $data['booking_history'][0]['partner_id']));
        $data['booking_history'][0]['source_name'] = $source[0]['source'];

        $partner_id = $data['booking_history'][0]['partner_id'];
        $data['prices'] = array();
        //log_message('info', __FUNCTION__ . " data " . print_r($data, true));
        $upcountry_price = 0;
        foreach ($data['booking_unit_details'] as $keys => $value) {
            if ($source[0]['partner_type'] == OEM) {
                $prices = $this->booking_model->getPricesForCategoryCapacity($data['booking_history'][0]['service_id'], $data['booking_unit_details'][$keys]['category'], $data['booking_unit_details'][$keys]['capacity'], $partner_id, $value['brand']);
            } else {
                $prices = $this->booking_model->getPricesForCategoryCapacity($data['booking_history'][0]['service_id'], $data['booking_unit_details'][$keys]['category'], $data['booking_unit_details'][$keys]['capacity'], $partner_id, "");
            }
            $upcountry_price = 0;
            //log_message('info', __FUNCTION__ . " Prices " . print_r($prices, true));
            foreach ($value['quantity'] as $key => $price_tag) {
                $service_center_data = $this->service_centers_model->get_prices_filled_by_service_center($price_tag['unit_id'], $booking_id);

                // print_r($service_center_data);
                if (!empty($service_center_data)) {
                    $data['booking_unit_details'][$keys]['quantity'][$key]['customer_paid_basic_charges'] = $service_center_data[0]['service_charge'];
                    $data['booking_unit_details'][$keys]['quantity'][$key]['customer_paid_extra_charges'] = $service_center_data[0]['additional_service_charge'];
                    $data['booking_unit_details'][$keys]['quantity'][$key]['serial_number'] = $service_center_data[0]['serial_number'];
                    $data['booking_unit_details'][$keys]['quantity'][$key]['customer_paid_parts'] = $service_center_data[0]['parts_cost'];
                }

                // Searched already inserted price tag exist in the price array (get all service category)
                $id = $this->search_for_key($price_tag['price_tags'], $prices);
                // remove array key, if price tag exist into price array
                unset($prices[$id]);
                if ($keys == 0) {
                    $upcountry_price = isset($service_center_data[0]['upcountry_charges']) ? $service_center_data[0]['upcountry_charges'] : "";
                }
            }

            array_push($data['prices'], $prices);
        }

        $data['upcountry_charges'] = $upcountry_price;
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/completebooking', $data);
    }

    /**
     * @desc: This is method return index key, if service caregory matches with given price tags
     * @param: Price tag and Array
     * @return: key
     */
    function search_for_key($price_tag, $array) {
        foreach ($array as $key => $val) {
            if ($val['service_category'] === $price_tag) {
                return $key;
            }
        }
        return null;
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
    function get_cancel_form($booking_id, $status = "") {
        log_message('info', __FUNCTION__ . " Booking ID: " . $booking_id);

        $data['user_and_booking_details'] = $this->booking_model->getbooking_history($booking_id);
        $where = array('reason_of' => '247around');
        $data['reason'] = $this->booking_model->cancelreason($where);
        if ($status == _247AROUND_FOLLOWUP) {
            $where_internal_status = array("page" => "Cancel", "active" => '1');
            $data['internal_status'] = $this->booking_model->get_internal_status($where_internal_status);
        }
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/cancelbooking', $data);
    }

    /**
     *  @desc : This function is to cancels the booking/Query
     *
     * Accepts the cancellation reason provided in cancel booking/Query form and then cancels booking with the reason provided.
     *
     *  @param : booking id
     *  @return : cancels the booking and load view
     */
    function process_cancel_form($booking_id, $status, $agent_id = false, $agent_name = false) {
        log_message('info', __FUNCTION__ . " Booking ID: " . $booking_id . " Done By " . $this->session->userdata('employee_id')." And Status:".$status);

        $this->form_validation->set_rules('cancellation_reason', 'Cancellation Reason', 'required|xss_clean');
        $this->form_validation->set_rules('partner_id', 'Partner Id', 'required|xss_clean');
        $validation = $this->form_validation->run();
        if ($validation) {


            if (!$agent_id) {
                $agent_id = $this->session->userdata('id');
                $agent_name = $this->session->userdata('employee_id');
            }
            $partner_id = $this->input->post('partner_id');
            $cancellation_reason = $this->input->post('cancellation_reason');
            $cancellation_text = $this->input->post("cancellation_reason_text");

            $this->miscelleneous->process_cancel_form($booking_id, $status, $cancellation_reason, $cancellation_text, $agent_id, $agent_name, $partner_id);
            //get the unit details data and update the inventory stock
            $booking_details = $this->reusable_model->get_search_query('booking_details', 'booking_details.assigned_vendor_id,booking_unit_details.price_tags,booking_unit_details.appliance_capacity', array('booking_details.booking_id' => $booking_id,"booking_unit_details.price_tags like '%"._247AROUND_WALL_MOUNT__PRICE_TAG."%'" => NULL,'booking_details.assigned_vendor_id IS NOT null'=>NULL), array('booking_unit_details'=>'booking_details.booking_id = booking_unit_details.booking_id'), NULL, NULL, NULL, NULL)->result_array();
            if (!empty($booking_details)) { 
                //process each unit if price tag is wall mount
                foreach($booking_details as $value){
                    $match = array();
                    //get the size from the capacity to know the part number
                    preg_match('/[0-9]+/', $value['appliance_capacity'], $match);
                    if (!empty($match)) {
                        if ($match[0] <= 32) {
                            $data['part_number'] = LESS_THAN_32_BRACKETS_PART_NUMBER;
                        } else if ($match[0] > 32) {
                            $data['part_number'] = GREATER_THAN_32_BRACKETS_PART_NUMBER;
                        }

                        $data['receiver_entity_id'] = $value['assigned_vendor_id'];
                        $data['receiver_entity_type'] = _247AROUND_SF_STRING;
                        $data['stock'] = 1;
                        $data['booking_id'] = $booking_id;
                        $data['agent_id'] = $agent_id;
                        $data['agent_type'] = _247AROUND_EMPLOYEE_STRING;

                        $this->miscelleneous->process_inventory_stocks($data);
                    }
                }
            }
            redirect(base_url() . DEFAULT_SEARCH_PAGE);
        } else {
            log_message('info', __FUNCTION__ . " Validation Failed Booking ID: " . $booking_id . " Done By " . $this->session->userdata('employee_id'));
            $this->get_cancel_form($booking_id, $status);
        }
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
        $getbooking = $this->booking_model->getbooking_history($booking_id);

        if ($getbooking) {
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/reschedulebooking', array('data' => $getbooking));
        } else {
            echo "This Id doesn't Exists";
        }
    }

    /**
     *  @desc : This function is to reschedule the booking.
     *
     * Accepts the new booking date and timeslot provided in form and then reschedules booking
     * accordingly.
     *
     *  @param : booking id
     *  @return : reschedules the booking and load view
     */
    function process_reschedule_booking_form($booking_id) {
        log_message('info', __FUNCTION__ . " Booking Id  " . print_r($booking_id, true));

        $data['booking_date'] = date('d-m-Y', strtotime($this->input->post('booking_date')));
        $data['booking_timeslot'] = $this->input->post('booking_timeslot');
        //$data['booking_remarks'] = $this->input->post('reason');
        $data['current_status'] = 'Rescheduled';
        $data['internal_status'] = 'Rescheduled';
        $data['update_date'] = date("Y-m-d H:i:s");
        $data['mail_to_vendor'] = 0;

        //check partner status
        $partner_id = $this->input->post('partner_id');
        $partner_status = $this->booking_utilities->get_partner_status_mapping_data($data['current_status'], $data['internal_status'], $partner_id, $booking_id);
        if (!empty($partner_status)) {
            $data['partner_current_status'] = $partner_status[0];
            $data['partner_internal_status'] = $partner_status[1];
        }

        if ($data['booking_timeslot'] == "Select") {
            echo "Please Select Booking Timeslot.";
        } else {
            log_message('info', __FUNCTION__ . " Update booking  " . print_r($data, true));
            $this->booking_model->update_booking($booking_id, $data);
            $this->booking_model->increase_escalation_reschedule($booking_id, "count_reschedule");

            //Log this state change as well for this booking
            //param:-- booking id, new state, old state, employee id, employee name
            $this->notify->insert_state_change($booking_id, _247AROUND_RESCHEDULED, _247AROUND_PENDING, _247AROUND_RESCHEDULED, $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);

            $service_center_data['internal_status'] = "Pending";
            $service_center_data['current_status'] = "Pending";
            $service_center_data['update_date'] = date("Y-m-d H:i:s");


            log_message('info', __FUNCTION__ . " Booking Id " . $booking_id . " Update Service center action table  " . print_r($service_center_data, true));

            $this->vendor_model->update_service_center_action($booking_id, $service_center_data);

            $send_data['booking_id'] = $booking_id;
            $send_data['current_status'] = "Rescheduled";
            $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
            $this->asynchronous_lib->do_background_process($url, $send_data);

            log_message('info', __FUNCTION__ . " Request to prepare Job Card  " . print_r($booking_id, true));

            $job_card = array();
            $job_card_url = base_url() . "employee/bookingjobcard/prepare_job_card_using_booking_id/" . $booking_id;
            $this->asynchronous_lib->do_background_process($job_card_url, $job_card);

            log_message('info', __FUNCTION__ . " Partner Callback  " . print_r($booking_id, true));

            // Partner Call back
            $this->partner_cb->partner_callback($booking_id);
            log_message('info', 'Rescheduled- Booking id: ' . $booking_id . " Rescheduled By " . $this->session->userdata('employee_id') . " data " . print_r($data, true));

            redirect(base_url() . DEFAULT_SEARCH_PAGE);
        }
    }

    /**
     * @desc : This function will get all the brands for that particular service with help of service_id on ajax call
     * @param: service_id of booking
     * @return : all present brands
     */
    function getBrandForService() {
        $service_id = $this->input->post('service_id');
        $source_code = $this->input->post('source_code');
        $booking_source = $this->booking_model->get_booking_source($source_code);

        if ($booking_source[0]['partner_type'] == OEM) {
            $where = array("partner_appliance_details.service_id" => $service_id,
                'partner_id' => $booking_source[0]['partner_id'], "active" => 1);
            $select = 'brand As brand_name';

            $result = $this->partner_model->get_partner_specific_details($where, $select, "brand");
        } else {
            $result = $this->booking_model->getBrandForService($service_id);
        }


        $data['partner_type'] = $booking_source[0]['partner_type'];
        $data['brand'] = "<option selected disabled> Select Brand</option>";
        foreach ($result as $brand) {
            $data['brand'] .= "<option>$brand[brand_name]</option>";
        }

        print_r(json_encode($data, true));
    }

    /**
     * @desc: This is used to get appliabce list its called by Ajax
     */
    function get_appliances($selected_service_id) {
        $partner_id = $this->input->post('partner_id');

        $prepaid = $this->miscelleneous->get_partner_prepaid_amount($partner_id);

        if ($prepaid['partner_type'] == OEM) {
            $services = $this->partner_model->get_partner_specific_services($partner_id);
        } else {
            $services = $this->booking_model->selectservice();
        }
        $data['partner_type'] = $prepaid['partner_type'];
        $data['partner_id'] = $partner_id;
        $data['active'] = $prepaid['active'];
        if($prepaid['is_notification']){
            $data['prepaid_msg'] = PREPAID_LOW_AMOUNT_MSG_FOR_ADMIN;
            
        } else {
            $data['prepaid_msg'] = "";
        }
        $data['services'] = "<option selected disabled>Select Service</option>";
        foreach ($services as $appliance) {
            $data['services'] .= "<option ";
            if ($selected_service_id == $appliance->id) {
                $data['services'] .= " selected ";
            } else if (count($services) == 1) {
                $data['services'] .= " selected ";
            }
            $data['services'] .=" value='" . $appliance->id . "'>$appliance->services</option>";
        }
        print_r(json_encode($data, true));
        
    }

    /**
     * @desc : This function will load category with help of service_id on ajax call
     * this method get get category on the basis of service id, state, price mapping id
     * @input: service id, city, partner_code
     * @return : displays category
     */
    function getCategoryForService() {

        $service_id = $this->input->post('service_id');
        $brand = $this->input->post('brand');
        $partner_type = $this->input->post('partner_type');
            
        $partner_id = $this->input->post('partner_id');
        if ($partner_type == OEM) {
            $result = $this->booking_model->getCategoryForService($service_id, $partner_id, $brand);
        } else {

            $result = $this->booking_model->getCategoryForService($service_id, $partner_id, "");
        }

        echo "<option selected disabled>Select Appliance Category</option>";
        foreach ($result as $category) {
            echo "<option>$category[category]</option>";
        }
    }

    /**
     * @desc : This function will load capacity with help of Category and service_id on ajax call
     * @param: Category and service_id of booking
     * @return : displays capacity
     */
    function getCapacityForCategory() {
        $service_id = $this->input->post('service_id');
        $category = $this->input->post('category');
        $brand = $this->input->post('brand');
        $partner_id = $this->input->post('partner_id');
        $partner_type = $this->input->post('partner_type');

        
        if ($partner_type == OEM) {
            $result = $this->booking_model->getCapacityForCategory($service_id, $category, $brand, $partner_id);
        } else {
            $result = $this->booking_model->getCapacityForCategory($service_id, $category, "", $partner_id);
        }

        foreach ($result as $capacity) {
            echo "<option>$capacity[capacity]</option>";
        }
    }

    /**
     * @desc : This function will show the price and services for ajax call
     * this method returns price list on the basis of service id, category, capacity, price mapping id, state
     * @input: service_id,category, capacity, brand, partner code, city, clone number
     * @return : services name and there prices
     */
    function getPricesForCategoryCapacity() {

        $service_id = $this->input->post('service_id');
        $category = $this->input->post('category');
        $capacity = $this->input->post('capacity');
        $booking_city = $this->input->post('booking_city');
        $booking_pincode = $this->input->post('booking_pincode');
        $brand = $this->input->post('brand');
        
        $partner_type = $this->input->post('partner_type');
        $clone_number = $this->input->post('clone_number');
        $partner_id =  $this->input->post('partner_id');
       
        $assigned_vendor_id = $this->input->post('assigned_vendor_id');
        if (empty($assigned_vendor_id)) {
            $assigned_vendor_id = FALSE;
        }
        $this->initialized_variable->fetch_partner_data($partner_id);
        
        $partner_data = $this->initialized_variable->get_partner_data();

        if ($partner_type == OEM) {
            $result = $this->booking_model->getPricesForCategoryCapacity($service_id, $category, $capacity, $partner_id, $brand);
        } else {
            $result = $this->booking_model->getPricesForCategoryCapacity($service_id, $category, $capacity, $partner_id, "");
        }

        $where = array('service_id' => $service_id, 'brand_name' => $brand);
        $brand_id_array = $this->booking_model->get_brand($where);

        if (!empty($brand_id_array)) {
            $brand_id = $brand_id_array[0]['id'];
        } else {
            $brand_id = "";
        }

        if (!empty($result)) {

            $html = "<thead><tr><th>Service Category</th><th>Std. Charges</th><th>Partner Discount</th><th>Final Charges</th><th>247around Discount</th><th>Selected Services</th></tr></thead>";
            $i = 0;

            foreach ($result as $prices) {

                $html .="<tr><td>" . $prices['service_category'] . "</td>";
                $html .= "<td>" . $prices['customer_total'] . "</td>";
                $html .= "<td><input  type='text' class='form-control partner_discount' name= 'partner_paid_basic_charges[$brand_id][$clone_number][" . $prices['id'] . "][]'  id='partner_paid_basic_charges_" . $i . "_" . $clone_number . "' value = '" . $prices['partner_net_payable'] . "' placeholder='Enter discount' readonly/></td>";
                $html .= "<td>" . $prices['customer_net_payable'] . "</td>";
                $html .= "<td><input  type='text' class='form-control discount' name= 'discount[$brand_id][$clone_number][" . $prices['id'] . "][]'  id='discount_" . $i . "_" . $clone_number . "' value = '0' placeholder='Enter discount' readonly></td>";
                $html .= "<td><input type='hidden'name ='is_up_val' id='is_up_val_" . $i . "_" . $clone_number . "' value ='" . $prices['is_upcountry'] . "' /><input class='price_checkbox'";

                $html .=" type='checkbox' id='checkbox_" . $i . "_" . $clone_number . "'";
                $html .= "name='prices[$brand_id][$clone_number][]'";
                $html .= "  onclick='final_price(), enable_discount(this.id), set_upcountry()'" .
                        "value=" . $prices['id'] . "_" . intval($prices['customer_total']) . "_" . $i . "_" . $clone_number . " ></td><tr>";

                $i++;
            }
            $data['price_table'] = $html;
            if (empty($assigned_vendor_id)) {
                $upcountry_data = $this->miscelleneous->check_upcountry_vendor_availability($booking_city, $booking_pincode, $service_id, $partner_data, $assigned_vendor_id);
            } else {

                $vendor_data = array();
                $vendor_data[0]['vendor_id'] = $assigned_vendor_id;
                $vendor_data[0]['city'] = $this->vendor_model->get_distict_details_from_india_pincode($booking_pincode)['district'];
                $vendor_data[0]['min_upcountry_distance'] = $this->vendor_model->getVendorDetails("min_upcountry_distance", 
                        array('id' =>$assigned_vendor_id))[0]['min_upcountry_distance'];
            
                $upcountry_data = $this->upcountry_model->action_upcountry_booking($booking_city, $booking_pincode, $vendor_data, $partner_data);
            }

            $data['upcountry_data'] = json_encode($upcountry_data, true);
            print_r(json_encode($data, true));
        } else {
            $data['html'] = "Price Table Not Found";
            print_r(json_encode($data, true));
        }
    }

    /**
     *  @desc : Ajax call(This function is to get non working days for particular vendor)
     *
     *  To know the non working days for the selected vendor.
     *
     *  @param : vendor's id(service centre id)
     *  @return : Non working days for particular vendor
     */
    function get_non_working_days_for_vendor($service_centre_id) {
        $result = $this->vendor_model->get_non_working_days_for_vendor($service_centre_id);
        if (empty($result)) {
            echo "No non working days found";
        }
        $non_working_days = $result[0]['non_working_days'];
        echo $non_working_days;
    }

    /**
     *  @desc : This function is to select completed booking to be rated
     *  @param : booking id
     *  @return : user details to view
     */
    function get_rating_form($booking_id, $status) {
        $getbooking = $this->booking_model->getbooking_history($booking_id);
        if ($getbooking) {
            $this->session->userdata('employee_id');
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/rating', array('data' => $getbooking, 'status' => $status));
        } else {
            echo "Id doesn't exist";
        }
    }

    /**
     *  @desc : This function is to save ratings for booking and for vendors
     *
     * With the help of this form you can rate the booking as per user experience and for vendors for the quality of service provided by the vendor.
     *
     *  @param : booking id
     *  @return : rate for booking and load view
     */
    function process_rating_form($booking_id, $status) {
        log_message('info', __FUNCTION__ . ' Booking ID : ' . $booking_id . ' Status' . $status . " Done By " . $this->session->userdata('employee_id'));
        if ($this->input->post('rating_star') != "Select") {
            $data['rating_stars'] = $this->input->post('rating_star');
            $data['rating_comments'] = $this->input->post('rating_comments');
            $phone_no = $this->input->post('mobile_no');
            $user_id = $this->input->post('user_id');
            $remarks = 'Rating'.':'.$data['rating_stars'].'. '.$data['rating_comments'];
            
            $update = $this->booking_model->update_booking($booking_id, $data);

            if ($update) {
                //update state
                $this->notify->insert_state_change($booking_id, RATING_NEW_STATE, $status, $remarks, $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);
                // send sms after rating
                $this->send_rating_sms($phone_no, $data['rating_stars'],$user_id,$booking_id);
            }
        }

        redirect(base_url() . 'employee/booking/view_bookings_by_status/' . $status);
    }

    /**
     *  @desc : This function is to save ratings for vendors
     *
     * With the help of this form you can rate for vendors for the quality of service provided by the vendor.
     *
     *  @param : booking id
     *  @return : rate for booking and load view
     */
    function vendor_rating($booking_id) {
        $this->booking_model->vendor_rating($booking_id, $data);
        $query = $this->booking_model->viewbooking();
        $data['Bookings'] = null;
        if ($query) {
            $data['Bookings'] = $query;
        }
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/booking', $data);
    }

    /**
     *  @desc : This function is to view deatils of any particular booking.
     *
     * 	We get all the details like User's details, booking details, and also the appliance's unit details.
     *
     *  @param : booking id
     *  @return : booking details and load view
     */
    function viewdetails($booking_id) {
        $data['booking_history'] = $this->booking_model->getbooking_filter_service_center($booking_id);
        if(!empty($data['booking_history'])){
            $unit_where = array('booking_id' => $booking_id);
            $data['unit_details'] = $this->booking_model->get_unit_details($unit_where);
            $data['penalty'] = $this->penalty_model->get_penalty_on_booking_by_booking_id($booking_id);
            if (!is_null($data['booking_history'][0]['sub_vendor_id'])) {
                $data['dhq'] = $this->upcountry_model->get_sub_service_center_details(array('id' => $data['booking_history'][0]['sub_vendor_id']));
            }
            
        }else{
            $data['booking_history'] = "";
        }
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/viewdetails', $data);
    }

    /**
     *  @desc : This function is to select particular appliance for booking.
     *  We have already made a function to get_edit_booking_form, this method use that function to insert booking by appliance id
     *  @param : appliance id
     *  @return : user's and appliance details to view
     */
    function get_appliance_booking_form($appliance_id) {
        log_message('info', __FUNCTION__ . " Appliance ID  " . print_r($appliance_id, true));
        $this->get_edit_booking_form("", $appliance_id);
    }

    /**
     *  @desc : This function is to get add new brand page
     *
     * 	Through this we add a new brand for selected service.
     *
     *  @param : void
     *  @return : list of active services present
     */
    function get_add_new_brand_form() {
        $services = $this->booking_model->selectservice();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/addnewbrand', array('services' => $services));
    }

    /**
     *  @desc : This function is to add new brand.
     *
     * 	Enters the new brand to our existing brand list for a particular service
     *
     *  @param : void
     *  @return : add new brand and load view
     */
    function process_add_new_brand_form() {
        $service_details = $this->input->post('new_brand');
        $brand_details = $this->input->post('brand_name');
        $data = array();
        foreach ($service_details as $key => $service_id) {
            if ($service_id != "Select") {
                if (!empty($brand_details[$key])) {

                    $is_exits = $this->booking_model->check_brand_exists($service_id, trim($brand_details[$key]));
                    if (!$is_exits) {
                        $service_name = $this->booking_model->selectservicebyid($service_id);
                        $is_insert = $this->booking_model->addNewApplianceBrand($service_id, trim($brand_details[$key]));
                        array_push($data, array("service_id" => $service_name[0]['services'], "brand_name" => trim($brand_details[$key])));
                    }
                }
            }
        }
        if (!empty($data)) {
            $to = ANUJ_EMAIL_ID;
            $cc = "";
            $bcc = "";
            $subject = "New Brand Added By " . $this->session->userdata('employee_id');
            $message = "
        <html>
        <head></head>
        <body>
            <h3>New Brands added By  " . $this->session->userdata('employee_id') . "</h3>    
            <table style='border-collapse:collapse; border: 1px solid black;'> 
                <thead>
                    <tr style='border-collapse:collapse; border: 1px solid black;'>
                        <th>Services</th>
                        <th>Brand Name</th>
                    </tr>    
                </thead>
                <tbody>";
            foreach ($data as $val) {
                $message .="<tr>
                            <td style='border-collapse:collapse; border: 1px solid black;'>" . $val['service_id'] . "</td>
                            <td style='border-collapse:collapse; border: 1px solid black;'>" . $val['brand_name'] . "</td>
                        </tr>";
            }
            $message .= "</tbody>
            </table>
            <hr />     
        </body>
        </html>";
            $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, $bcc, $subject, $message, "");
        }

        redirect(base_url() . 'employee/booking/get_add_new_brand_form', 'refresh');
    }

    /**
     * @desc: load update booking form to update booking
     * @param: booking id
     * @return : void
     */
    function get_edit_booking_form($booking_id, $appliance_id = "") {
        log_message('info', __FUNCTION__ . " Appliance ID  " . print_r($appliance_id, true) . " Booking ID: " . print_r($booking_id, true));

        if ($booking_id != "") {
            $booking_history = $this->booking_model->getbooking_history($booking_id);
        } else {
            $booking_history = $this->booking_model->getbooking_history_by_appliance_id($appliance_id);
        }
        if (!empty($booking_history)) {
            $booking = $this->booking_model->get_city_source();
            $booking['booking_history'] = $booking_history;
            $booking['unit_details'] = $this->booking_model->getunit_details($booking_id, $appliance_id);
            if(!empty($booking_history[0]['dealer_id'])){
                $condition = array(
                "where" => array('dealer_details.dealer_id' => $booking_history[0]['dealer_id']));
                $select = " dealer_details.dealer_id, dealer_name, dealer_phone_number_1";
                $condition['length'] = -1;
               
                 $dealer_details = $this->dealer_model->get_dealer_mapping_details($condition, $select);
                 if($dealer_details){
                      $booking['booking_history'][0]['dealer_phone_number'] = $dealer_details[0]['dealer_phone_number_1'];
                      $booking['booking_history'][0]['dealer_name'] = $dealer_details[0]['dealer_name'];
                 }
            }
            
            $prepaid = $this->miscelleneous->get_partner_prepaid_amount($booking_history[0]['partner_id']);
            $booking['active'] = $prepaid['active'];
            
            $booking['partner_type'] = $prepaid["partner_type"];
            if ($booking['partner_type'] == OEM) {
                $booking['services'] = $this->partner_model->get_partner_specific_services($booking_history[0]['partner_id']);
            } else {
                $booking['services'] = $this->booking_model->selectservice();
            }

            $booking['capacity'] = array();
            $booking['category'] = array();
            $booking['brand'] = array();
            $booking['prices'] = array();
            $booking['appliance_id'] = $appliance_id;
            $where_internal_status = array("page" => "FollowUp", "active" => '1');
            $booking['follow_up_internal_status'] = $this->booking_model->get_internal_status($where_internal_status);

            foreach ($booking['unit_details'] as $key => $value) {
                if ($booking['partner_type'] == OEM) {
                    $where = array("partner_appliance_details.service_id" => $booking_history[0]['service_id'],
                        'partner_id' => $booking_history[0]['partner_id'], "active" => 1);
                    $select = 'brand As brand_name';

                    $brand = $this->partner_model->get_partner_specific_details($where, $select, "brand");
                    $category = $this->booking_model->getCategoryForService($booking_history[0]['service_id'], $value['partner_id'], $value['brand']);

                    $capacity = $this->booking_model->getCapacityForCategory($booking_history[0]['service_id'], $value['category'], $value['brand'], $value['partner_id']);

                    $prices = $this->booking_model->getPricesForCategoryCapacity($booking_history[0]['service_id'], $value['category'], $value['capacity'], $value['partner_id'], $value['brand']);
                } else {
                    $brand = $this->booking_model->getBrandForService($booking_history[0]['service_id']);
                    $category = $this->booking_model->getCategoryForService($booking_history[0]['service_id'], $value['partner_id'], "");
                    $capacity = $this->booking_model->getCapacityForCategory($booking_history[0]['service_id'], $value['category'], "", $value['partner_id']);
                    $prices = $this->booking_model->getPricesForCategoryCapacity($booking_history[0]['service_id'], $value['category'], $value['capacity'], $value['partner_id'], "");
                }



                $where1 = array('service_id' => $booking_history[0]['service_id'], 'brand_name' => $value['brand']);
                $brand_id_array = $this->booking_model->get_brand($where1);
                if (!empty($brand_id_array)) {

                    $booking['unit_details'][$key]['brand_id'] = $brand_id_array[0]['id'];
                } else {
                    $booking['unit_details'][$key]['brand_id'] = "";
                }

                array_push($booking['category'], $category);
                array_push($booking['brand'], $brand);
                array_push($booking['capacity'], $capacity);
                array_push($booking['prices'], $prices);
            }
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/update_booking', $booking);
        } else {
            echo "Booking Id Not Exist";
        }
    }

    /**
     * @desc: This function is used to update both Bookings and Queries.
     */
    function update_booking($user_id, $booking_id) {
        $bookings = $this->booking_model->getbooking_history($booking_id);
        if (!empty($bookings)) {
            if ($this->input->post()) {
                $checkValidation = $this->validate_booking();

                if ($checkValidation) {
                    log_message('info', __FUNCTION__ . " Booking ID  " . $booking_id . " User ID: " . $user_id);

                    $status = $this->getAllBookingInput($user_id, $booking_id);
                    if ($status) {
                        log_message('info', __FUNCTION__ . " Partner callback  " . $status['booking_id']);
                        $this->partner_cb->partner_callback($status['booking_id']);

                        //Redirect to Default Search Page
                        redirect(base_url() . DEFAULT_SEARCH_PAGE);
                    } else {
                        //Redirect to edit booking page if validation err occurs
                        $this->get_edit_booking_form($booking_id);
                    }
                } else {
                    //Redirect to edit booking page if validation err occurs
                    $this->get_edit_booking_form($booking_id);
                }
            } else {
                //Logging error if No input is provided
                log_message('info', __FUNCTION__ . "Error in Update Booking ID  " . print_r($booking_id, true) . " User ID: " . print_r($user_id, true));
                $heading = "247Around Booking Error";
                $message = "Oops... No input provided !";
                $error = & load_class('Exceptions', 'core');
                echo $error->show_error($heading, $message, 'custom_error');
            }
        } else {
            echo "Booking Id Not Exist...\n Already Updated.";
        }
    }


    /**
     *  @desc : This function is used to call customer from admin panel
     *  @param : Phone Number
     *  @return : none
     */
    function call_customer($cust_phone) {
        // log_message('info', __FUNCTION__);

        $s1 = $_SERVER['HTTP_REFERER'];
        //$s2 = "https://www.aroundhomzapp.com/";
        $s2 = base_url();
        $redirect_url = substr($s1, strlen($s2));

        $this->checkUserSession();

        //Get customer id
        $cust_id = '';
        $user = $this->user_model->search_user($cust_phone);
        if ($user) {
            $cust_id = $user[0]['user_id'];
        }

        //Find agent phone from session
        $agent_id = $this->session->userdata('id');
        $agent_phone = $this->session->userdata('phone');

        //Save call log
        $this->booking_model->insert_outbound_call_log(array(
            'agent_id' => $agent_id, 'customer_id' => $cust_id,
            'customer_phone' => $cust_phone
        ));

        //Make call to customer now
        $this->notify->make_outbound_call($agent_phone, $cust_phone);

        //Redirect to the page from where you landed in this function, do not refresh
        redirect(base_url() . $redirect_url);
    }

    /**
     *  @desc : Callback fn called after agent finishes customer call
     *  @param : Phone Number
     *  @return : none
     */
    function call_customer_status_callback() {
        log_message('info', "Entering: " . __METHOD__);

        //http://support.exotel.in/support/solutions/articles/48259-outbound-call-to-connect-an-agent-to-a-customer-
        $callDetails['call_sid'] = (isset($_GET['CallSid'])) ? $_GET['CallSid'] : null;
        $callDetails['status'] = (isset($_GET['Status'])) ? $_GET['Status'] : null;
        $callDetails['recording_url'] = (isset($_GET['RecordingUrl'])) ? $_GET['RecordingUrl'] : null;
        $callDetails['date_updated'] = (isset($_GET['DateUpdated'])) ? $_GET['DateUpdated'] : null;

        log_message('info', print_r($callDetails, true));
//	//insert in database
//	$this->apis->insertPassthruCall($callDetails);
    }

    /**
     * @desc :This funtion will check user session for an eemplouee.
     */
    function checkUserSession() {
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            $this->session->sess_destroy();
            redirect(base_url() . "employee/login");
        }
    }

    /**
     * @desc: Reject Booking from review page
     * @param: void
     * @return: void
     */
    function reject_booking_from_review() {
        log_message('info', __FUNCTION__);
        $booking_id = $this->input->post('booking_id');
        $admin_remarks = $this->input->post('admin_remarks');
        $data['internal_status'] = "Pending";
        $data['current_status'] = "Pending";
        $data['update_date'] = date("Y-m-d H:i:s");
        $data['serial_number'] = "";
        $data['service_center_remarks'] = NULL;
        $data['booking_date'] = $data['booking_timeslot'] = NUll;
        $data['closed_date'] = NUll;
        $data['service_charge'] = $data['additional_service_charge'] = $data['parts_cost'] = "0.00";
        $data['admin_remarks'] = date("F j") . "  :-" . $admin_remarks;
        log_message('info', __FUNCTION__ . " Booking_id " . $booking_id . " Update service center action table: " . print_r($data, true));
        $this->vendor_model->update_service_center_action($booking_id, $data);

        $this->notify->insert_state_change($booking_id, "Rejected", "InProcess_Completed", $admin_remarks, $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);
    }

    /**
     * @desc: This funtion is used to review bookings (All selected checkbox) which are
     * completed/cancelled by our vendors.
     * It completes/cancels these bookings in the background and returns immediately.
     * @param : void
     * @return : void
     */
    function checked_complete_review_booking() {
        log_message('info', __FUNCTION__);
        $approved_booking = $this->input->post('approved_booking');
        $url = base_url() . "employee/do_background_process/complete_booking";
        $agent_id = $this->session->userdata('id');
        $agent_name = $this->session->userdata('employee_id');
        $partner_id = $this->input->post('partner_id');
        if (!empty($approved_booking)) {
            foreach ($approved_booking as $booking_id) {
                $data = array();
                $data['booking_id'] = $booking_id;
                $data['agent_id'] = $agent_id;
                $data['agent_name'] = $agent_name;
                $data['partner_id'] = $partner_id;
                log_message('info', __FUNCTION__ . " Approved Booking: " . print_r($data, true));
                $this->asynchronous_lib->do_background_process($url, $data);
            }
        } else {
            //Logging
            log_message('info', __FUNCTION__ . ' Approved Booking Empty from Post');
        }


        redirect(base_url() . 'employee/booking/review_bookings');
    }

    /**
     * @desc: This funtion is used to review booking which is completed/cancelled by our vendors.
     * Sends the charges filled by vendor while completing the booking to review booking page
     * It completes/cancels the particular booking in the background and returns immediately.
     * @param : $booking_id
     * @return : array of charges to view
     */
    function review_bookings($booking_id = "") {
        $whereIN = array();
        if($this->session->userdata('user_group') == 'regionalmanager'){
            $sf_list = $this->vendor_model->get_employee_relation($this->session->userdata('id'));
            $serviceCenters = $sf_list[0]['service_centres_id'];
            $whereIN =array("service_center_id"=>explode(",",$serviceCenters));
        }
        log_message('info', __FUNCTION__ . " Booking ID: " . print_r($booking_id, true));
        $data['charges'] = $this->booking_model->get_booking_for_review($booking_id,$whereIN);
        $data['data'] = $this->booking_model->review_reschedule_bookings_request($whereIN);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/review_booking', $data);
    }

    /**
     * @desc: This method is used to approve reschedule booking requests in admin panel and
     * upadte current status and internal status (Pending) of bookings in service center
     * booking action table.
     *
     */
    function process_review_reschedule_bookings() {
        log_message('info', __FUNCTION__);
        $reschedule_booking_id = $this->input->post('reschedule');
        $reschedule_booking_date = $this->input->post('reschedule_booking_date');
        $reschedule_reason = $this->input->post('reschedule_reason');
        $partner_id = $this->input->post('partner_id');
        $employeeID = $this->session->userdata('employee_id');
        $id = $this->session->userdata('id');
        $this->miscelleneous->approved_rescheduled_bookings($reschedule_booking_id,$reschedule_booking_date,$reschedule_reason,$partner_id,$id,$employeeID);
         redirect(base_url() . "employee/booking/review_bookings");
    }

    /**
     * @desc: This is used to complete booking by admin. It gets booking id and status as parameter. 
     * if status is 0 then redirect pending booking other wise redirect completed booking page
     * status 1 means booking already completed and status 0 means booking current status is Pending
     * @param: String Array, string
     * @return :void
     */
    function process_complete_booking($booking_id, $status = "") {
        log_message('info', __FUNCTION__ . " Booking id: " . $booking_id . " Status: " . $status . " Done By " . $this->session->userdata('employee_id'));
       
        // customer paid basic charge is comming in array
        // Array ( [100] =>  500 , [102] =>  300 )
        $customer_basic_charge = $this->input->post('customer_basic_charge');
        // Additional service charge is comming in array
        $additional_charge = $this->input->post('additional_charge');
        // Parts cost is comming in array
        $parts_cost = $this->input->post('parts_cost');
        $customer_net_payable = $this->input->post('customer_net_payable');
        $booking_status = $this->input->post('booking_status');
        $total_amount_paid = $this->input->post('grand_total_price');
        $admin_remarks = $this->input->post('admin_remarks');
        $serial_number = $this->input->post('serial_number');
        $upcountry_charges = $this->input->post("upcountry_charges");
        $internal_status = "Cancelled";
        $pincode = $this->input->post('booking_pincode');
        $state = $this->vendor_model->get_state_from_pincode($pincode);
        $service_center_details = $this->booking_model->getbooking_charges($booking_id);
        $b_unit_details = array();
        if($status == 1){
            $b_unit_details = $this->booking_model->get_unit_details(array('booking_id'=>$booking_id));
        }
        $k = 0;
        foreach ($customer_basic_charge as $unit_id => $value) {
            // variable $unit_id  is existing id in booking unit details table of given booking id
            $data = array();
            $data['customer_paid_basic_charges'] = $value;
            $data['customer_paid_extra_charges'] = $additional_charge[$unit_id];
            $data['customer_paid_parts'] = $parts_cost[$unit_id];
            if (isset($serial_number[$unit_id])) {
                $data['serial_number'] = $serial_number[$unit_id];
            } else {
                $data['serial_number'] = "";
            }

            if (isset($customer_net_payable[$unit_id])) {
                $data['customer_net_payable'] = $customer_net_payable[$unit_id];
            }

            // it checks string new in unit_id variable
            if (strpos($unit_id, 'new') !== false) {
                if (isset($booking_status[$unit_id])) {
                    if ($booking_status[$unit_id] == "Completed") {
                        // if new line item selected then coming unit id variable is the combination of unit id & new(string) and service charges id
                        // e.g- 12new103
                        $remove_string_new = explode('new', $unit_id);
                        $unit_id = $remove_string_new[0];
                        $service_charges_id = $remove_string_new[1];
                        $data['booking_id'] = $booking_id;
                        $data['booking_status'] = "Completed";
                        $internal_status = "Completed";
                        if ($status == 1) {
                            if(!empty($b_unit_details)){
                                $data_service_center['closed_date'] = $data['ud_closed_date'] = $b_unit_details[0]['ud_closed_date'];
                            } else {
                                $data_service_center['closed_date'] = $data['ud_closed_date'] = date('Y-m-d H:i:s');
                            }
                           
                        } else {
                            $data_service_center['closed_date'] = $data['ud_closed_date'] = date('Y-m-d H:i:s');
                        }
                        

                        log_message('info', __FUNCTION__ . " New unit selected, previous unit " . print_r($unit_id, true)
                                . " Service charges id: "
                                . print_r($service_charges_id, true)
                                . " Data: " . print_r($data, true) . " State: " . print_r($state['state'], true));
                        $new_unit_id = $this->booking_model->insert_new_unit_item($unit_id, $service_charges_id, $data, $state['state']);

                        $data_service_center['booking_id'] = $booking_id;
                        $data_service_center['unit_details_id'] = $new_unit_id;
                        $data_service_center['service_center_id'] = $service_center_details[0]['service_center_id'];
                        $data_service_center['update_date'] = date('Y-m-d H:i:s');
                        $data_service_center['service_charge'] = $data['customer_paid_basic_charges'];
                        $data_service_center['additional_service_charge'] = $data['customer_paid_extra_charges'];
                        $data_service_center['parts_cost'] = $data['customer_paid_parts'];
                        $data_service_center['serial_number'] = $data['serial_number'];
                        $data_service_center['current_status'] = $data_service_center['internal_status'] = "Completed";
                        $data_service_center['amount_paid'] = $total_amount_paid;
                        if ($k == 0) {
                            $data_service_center['upcountry_charges'] = $upcountry_charges;
                        }


                        log_message('info', __FUNCTION__ . " New unit selected, service center action data " . print_r($data_service_center, true));
                        $this->vendor_model->insert_service_center_action($data_service_center);
                    }
                }
            } else {
                $data['booking_status'] = $booking_status[$unit_id];

                if ($data['booking_status'] === _247AROUND_COMPLETED) {
                    $internal_status = _247AROUND_COMPLETED;
                }
                if ($status == 1) {
                    if(!empty($b_unit_details)){
                         $service_center['closed_date'] = $data['ud_closed_date'] = $b_unit_details[0]['ud_closed_date'];
                    } else {
                        $service_center['closed_date'] = $data['ud_closed_date'] = date('Y-m-d H:i:s');
                    }
                } else {
                    $service_center['closed_date'] = $data['ud_closed_date'] = date('Y-m-d H:i:s');
                }

                $data['id'] = $unit_id;

                log_message('info', ": " . " update booking unit details data " . print_r($data, TRUE));

                // update price in the booking unit details page
                $this->booking_model->update_unit_details($data);

                $service_center['closing_remarks'] = "";

                if (!empty($service_center_details)) {
                    if (!empty($service_center_details[0]['service_center_remarks']) && !empty($admin_remarks)) {
                        $service_center['closing_remarks'] = "Service Center Remarks:- " . $service_center_details[0]['service_center_remarks'] .
                                "  Admin:-  " . $admin_remarks;
                    } else if (!empty($service_center_details[0]['service_center_remarks']) && empty($admin_remarks)) {

                        $service_center['closing_remarks'] = "Service Center Remarks:- " . $service_center_details[0]['service_center_remarks'];
                    } else if (empty($service_center_details[0]['service_center_remarks']) && !empty($admin_remarks)) {

                        $service_center['closing_remarks'] = "Admin:-  " . $admin_remarks;
                    }
                } else if (!empty($admin_remarks)) {
                    $service_center['closing_remarks'] = "Admin:-  " . $admin_remarks;
                }

                $service_center['internal_status'] = $service_center['current_status'] = $data['booking_status'];
                $service_center['unit_details_id'] = $unit_id;
                $service_center['update_date'] = date('Y-m-d H:i:s');
                $service_center['service_charge'] = $data['customer_paid_basic_charges'];
                $service_center['additional_service_charge'] = $data['customer_paid_extra_charges'];
                $service_center['parts_cost'] = $data['customer_paid_parts'];
                $service_center['serial_number'] = $data['serial_number'];
                $service_center['amount_paid'] = $total_amount_paid;
                if ($k == 0) {
                    $service_center['upcountry_charges'] = $upcountry_charges;
                }

                log_message('info', ": " . " update Service center data " . print_r($service_center, TRUE));
                $this->vendor_model->update_service_center_action($booking_id, $service_center);
            }
            $k = $k + 1;
        }

        $booking['current_status'] = $internal_status;
        $booking['internal_status'] = $internal_status;
        $booking['booking_id'] = $booking_id;
        $booking['upcountry_paid_by_customer'] = $upcountry_charges;

        // check partner status
        $partner_id = $this->input->post('partner_id');
        $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'], $partner_id, $booking_id);
        if (!empty($partner_status)) {
            $booking['partner_current_status'] = $partner_status[0];
            $booking['partner_internal_status'] = $partner_status[1];
        }

        if ($this->input->post('rating_stars') !== "") {
            $booking['rating_stars'] = $this->input->post('rating_stars');
            $booking['rating_comments'] = $this->input->post('rating_comments');
        }

        $booking['closing_remarks'] = $service_center['closing_remarks'];
        
        $booking['update_date'] = date('Y-m-d H:i:s');
        if ($status == 1) {
            if(!empty($b_unit_details)){
               $booking['closed_date'] = $b_unit_details[0]['ud_closed_date'];
            } else {
                $booking['closed_date'] = date('Y-m-d H:i:s');
            }
        } else {
            $booking['closed_date'] = date('Y-m-d H:i:s');
        }

        $booking['amount_paid'] = $total_amount_paid;

        //update booking_details table
        log_message('info', ": " . " update booking details data (" . $booking['current_status'] . ")" . print_r($booking, TRUE));
        // this function is used to update booking details table
        $this->booking_model->update_booking($booking_id, $booking);
        $spare = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id, spare_parts_details.status", array('booking_id' => $booking_id, 'status NOT IN ("Completed","Cancelled")' =>NULL ), false);
        foreach($spare as $sp){
            //Update Spare parts details table
            $this->service_centers_model->update_spare_parts(array('id'=> $sp['id']), array('old_status' => $sp['status'],'status' => $internal_status));
        }
        
        if ($status == 0) {
            //Log this state change as well for this booking
            //param:-- booking id, new state, old state, employee id, employee name
            $this->notify->insert_state_change($booking_id, $internal_status, _247AROUND_PENDING, $booking['closing_remarks'], $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);
            $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
            $send['booking_id'] = $booking_id;
            $send['state'] = $internal_status;
            $this->asynchronous_lib->do_background_process($url, $send);

            $this->partner_cb->partner_callback($booking_id);
            redirect(base_url() . 'employee/booking/view_bookings_by_status/Pending');
        } else {
            redirect(base_url() . 'employee/booking/view_bookings_by_status/' . $internal_status);
        }
    }

    /**
     *  @desc : This function is to present form to open completed bookings
     *
     * It converts a Completed Booking into Pending booking and schedule it to
     * a new booking date & time.
     *
     *  @param : String (Booking Id)
     *  @return :
     */
    function get_convert_booking_to_pending_form($booking_id, $status) {
        $bookings = $this->booking_model->getbooking_history($booking_id);
        $bookings[0]['status'] = $status;
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/complete_to_pending', $bookings[0]);
    }

    /**
     *  @desc : This function is to process form to open completed/cancelled bookings
     *
     * Accepts the new booking date and timeslot povided in form and then opens
     * a completed or cancelled booking.
     *
     *  @param : booking id
     *  @return : Converts the Completed/Cancelled booking to Pending stage and load view
     */
    function process_convert_booking_to_pending_form($booking_id, $status) {
        log_message('info', __FUNCTION__ . " Booking id: " . $booking_id . " status: " . $status . " Done By " . $this->session->userdata('employee_id'));

        $data['booking_date'] = date('d-m-Y', strtotime($this->input->post('booking_date')));
        $data['booking_timeslot'] = $this->input->post('booking_timeslot');
        $data['current_status'] = 'Pending';
        $data['internal_status'] = "Booking Opened From ".$status;
        $data['update_date'] = date("Y-m-d H:i:s");
        $data['cancellation_reason'] = NULL;
        $data['closed_date'] = NULL;
        $data['vendor_rating_stars'] = NULL;
        $data['vendor_rating_comments'] = NULL;
        $data['amount_paid'] = NULL;
        $data['rating_stars'] = NULL;
        $data['rating_comments'] = NULL;
        $data['closing_remarks'] = NULL;
        $data['booking_jobcard_filename'] = NULL;
        $data['mail_to_vendor'] = 0;
        //$data['booking_remarks'] = $this->input->post('reason');
        //check partner status from partner_booking_status_mapping table  
        $partner_id = $this->input->post('partner_id');
        $partner_status = $this->booking_utilities->get_partner_status_mapping_data($data['current_status'], $data['internal_status'], $partner_id, $booking_id);
        if (!empty($partner_status)) {
            $data['partner_current_status'] = $partner_status[0];
            $data['partner_internal_status'] = $partner_status[1];
        }

        if ($data['booking_timeslot'] == "Select") {
            echo "Please Select Booking Timeslot.";
        } else {
            log_message('info', __FUNCTION__ . " Convert booking, data : " . print_r($data, true));
            $this->booking_model->update_booking($booking_id, $data);
            
            $assigned_vendor_id = $this->input->post("assigned_vendor_id");
            if (!empty($assigned_vendor_id)) {
                $service_center_data['internal_status'] = "Pending";
                $service_center_data['current_status'] = "Pending";
                $service_center_data['update_date'] = date("Y-m-d H:i:s");
                $service_center_data['serial_number'] = "";
                $service_center_data['cancellation_reason'] = NULL;
                $service_center_data['reschedule_reason'] = NULL;
                $service_center_data['admin_remarks'] = NULL;
                $service_center_data['service_center_remarks'] = $service_center_data['admin_remarks'] = NULL;
                $service_center_data['booking_date'] = $service_center_data['booking_timeslot'] = NUll;
                $service_center_data['closed_date'] = NULL;
                $service_center_data['service_charge'] = $service_center_data['additional_service_charge'] = $service_center_data['parts_cost'] = "0.00";
                log_message('info', __FUNCTION__ . " Convert booking, Service center data : " . print_r($service_center_data, true));
                $this->vendor_model->update_service_center_action($booking_id, $service_center_data);
                //if booking status is cancelled then do action on inventory
                if ($status === _247AROUND_CANCELLED) {
                    //get the unit details data and update the inventory stock
                    $booking_unit_details = $this->reusable_model->get_search_query('booking_unit_details', 'booking_unit_details.price_tags,booking_unit_details.appliance_capacity', array('booking_unit_details.booking_id' => $booking_id, "booking_unit_details.price_tags like '%" . _247AROUND_WALL_MOUNT__PRICE_TAG . "%'" => NULL), NULL, NULL, NULL, NULL, NULL)->result_array();
                    if (!empty($booking_unit_details)) {
                        //process each unit if price tag is wall mount
                        foreach ($booking_unit_details as $value) {
                            $match = array();
                            //get the size from the capacity to know the part number
                            preg_match('/[0-9]+/', $value['appliance_capacity'], $match);
                            if (!empty($match)) {
                                if ($match[0] <= 32) {
                                    $data['part_number'] = LESS_THAN_32_BRACKETS_PART_NUMBER;
                                } else if ($match[0] > 32) {
                                    $data['part_number'] = GREATER_THAN_32_BRACKETS_PART_NUMBER;
                                }

                                $data['receiver_entity_id'] = $assigned_vendor_id;
                                $data['receiver_entity_type'] = _247AROUND_SF_STRING;
                                $data['stock'] = -1;
                                $data['booking_id'] = $booking_id;
                                $data['agent_id'] = $this->session->userdata('id');
                                $data['agent_type'] = _247AROUND_EMPLOYEE_STRING;

                                $this->miscelleneous->process_inventory_stocks($data);
                            }
                        }
                    }
                }
            }


            $unit_details['booking_status'] = _247AROUND_PENDING;
            $unit_details['vendor_to_around'] = "0.00";
            $unit_details['around_to_vendor'] = "0.00";
            $unit_details['ud_closed_date'] = NULL;

            log_message('info', __FUNCTION__ . " Convert Unit Details - data : " . print_r($unit_details, true));

            $this->booking_model->update_booking_unit_details($booking_id, $unit_details);
            
            $spare = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id, spare_parts_details.old_status", array('booking_id' => $booking_id ), false);
            foreach($spare as $sp){
                //Update Spare parts details table
                $this->service_centers_model->update_spare_parts(array('id'=> $sp['id']), array('status' => $sp['old_status']));
            }

            //Log this state change as well for this booking          
            $this->notify->insert_state_change($booking_id, _247AROUND_PENDING, $status, "", $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);
            if (!empty($assigned_vendor_id)) {

                $this->miscelleneous->assign_upcountry_booking($booking_id, $this->session->userdata('id'), $this->session->userdata('employee_id'));
            }
            //Creating Job Card to Booking ID
            $this->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);
            if (!empty($assigned_vendor_id)) {
                $this->booking_utilities->lib_send_mail_to_vendor($booking_id, "");
            }

            $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
            $send['booking_id'] = $booking_id;
            $send['state'] = "OpenBooking";
            $this->asynchronous_lib->do_background_process($url, $send);

            log_message('info', $status . ' Booking Opened - Booking id: ' . $booking_id . " Opened By: " . $this->session->userdata('employee_id') . " => " . print_r($data, true));

            redirect(base_url() . DEFAULT_SEARCH_PAGE);
        }
    }

    /**
     *  @desc : This function is to present form to open cancelled bookings
     *
     * It converts a Cancelled Booking into Pending booking and schedule it to
     * a new booking date & time.
     *
     *  @param : String (Booking Id)
     *  @return :
     */
    function get_convert_cancelled_booking_to_pending_form($booking_id) {
        $bookings = $this->booking_model->booking_history_by_booking_id($booking_id);
        $this->notify->insert_state_change($booking_id, _247AROUND_PENDING, _247AROUND_CANCELLED, "", $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/cancelled_to_pending', $bookings[0]);
    }

    /**
     *  @desc : This function is used to open a cancelled query
     *  @param : String (Booking Id)
     *  @return : redirect user controller
     */
    function open_cancelled_query($booking_id) {
        log_message('info', __FUNCTION__ ."Booking_ID: " .$booking_id);
        
        $status = array("current_status" => "FollowUp",
            "internal_status" => "Cancelled Query to FollowUp",
            "cancellation_reason" => NULL,
            "closed_date" => NULL);

        //check partner status from partner_booking_status_mapping table  
        $getbooking = $this->booking_model->getbooking_history($booking_id);
        $partner_id = $getbooking[0]['partner_id'];
        if ($partner_id) {
            $partner_status = $this->booking_utilities->get_partner_status_mapping_data($status['current_status'], $status['internal_status'], $partner_id, $booking_id);
            if (!empty($partner_status)) {
                $status['partner_current_status'] = $partner_status[0];
                $status['partner_internal_status'] = $partner_status[1];
            }
        }
        $this->booking_model->update_booking($booking_id, $status);
        $this->booking_model->update_booking_unit_details_by_any(array('booking_id'=> $booking_id), array('booking_status'=> _247AROUND_FOLLOWUP));    

        //Log this state change as well for this booking
        $this->notify->insert_state_change($booking_id, _247AROUND_FOLLOWUP, _247AROUND_CANCELLED, "Cancelled_Query to FollowUp", $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);

        redirect(base_url() . 'employee/booking/view_queries/FollowUp/' . PINCODE_ALL_AVAILABLE . '/' . $booking_id);
    }
    
    /**
     * @desc: This is used to show Booking Life Cycle of particular Booking
     * params: String Booking_ID
     * return: Array of Data for View
     */
    function get_booking_life_cycle($booking_id) {
        $data['data'] = $this->booking_model->get_booking_state_change_by_id($booking_id);
        //Checking for 247Around user
        if ($this->session->userdata('userType') == 'employee') {
            //Getting Name of SF Agent and SF Name
            foreach ($data['data'] as $key => $value) {
                //Checking for SF Details
                if (!empty($value['service_center_id']) && empty($value['partner_id'])) {
                    $data['data'][$key]['full_name'] = $this->service_centers_model->get_sc_login_details_by_id($value['service_center_id'])[0]['full_name'];
                    $data['data'][$key]['source'] = $this->vendor_model->getVendorDetails('name', array('id' => $value['service_center_id']))[0]['name']; 
                }
            }
        }
        $data['booking_details'] = $this->booking_model->getbooking_history($booking_id);
        $data['sms_sent_details'] = $this->booking_model->get_sms_sent_details($booking_id);

        //$this->load->view('employee/header/'.$this->session->userdata('user_group'));

        $this->load->view('employee/show_booking_life_cycle', $data);
    }

    /**
     * @desc: This function is used to validate Bookings New/Update
     * 
     * params: Array of inputs
     * return: void
     */
    function validate_booking() {
        $this->form_validation->set_rules('service_id', 'Appliance', 'required|xss_clean');
        $this->form_validation->set_rules('source_code', 'Source Code', 'required|xss_clean');
        $this->form_validation->set_rules('type', 'Booking Type', 'required|xss_clean');
        $this->form_validation->set_rules('grand_total_price', 'Total Price', 'required');
        $this->form_validation->set_rules('city', 'City', 'required|xss_clean');
        $this->form_validation->set_rules('booking_date', 'Date', 'required');
        $this->form_validation->set_rules('appliance_brand', 'Appliance Brand', 'required');
        $this->form_validation->set_rules('appliance_category', 'Appliance Category', 'required');

        $this->form_validation->set_rules('partner_paid_basic_charges', 'Please Select Partner Charged', 'required');
        $this->form_validation->set_rules('booking_primary_contact_no', 'Mobile', 'required|trim|xss_clean|regex_match[/^[7-9]{1}[0-9]{9}$/]');
        $this->form_validation->set_rules('dealer_phone_number', 'Dealer Mobile Number', 'trim|xss_clean|regex_match[/^[7-9]{1}[0-9]{9}$/]');
        $this->form_validation->set_rules('booking_timeslot', 'Time Slot', 'required|xss_clean');
        $this->form_validation->set_rules('support_file', 'Suppoart File', 'callback_validate_upload_orderId_support_file');

        return $this->form_validation->run();
    }

    /**
     * @desc Validate Order ID
     * @return boolean
     */
    function validate_order_id($partner_id, $booking_id, $order_id, $amount_due) {

        switch ($partner_id) {
            case '247001':
            
                return true;
            // break;
            default :
                $dealer_phone_number = $this->input->post("dealer_phone_number");
                if (!empty($order_id)) {
                    $partner_booking = $this->partner_model->get_order_id_for_partner($partner_id, $order_id, $booking_id);
                    if (is_null($partner_booking)) {
                        return true;
                    } else {
                        $output = "Duplicate Order ID";
                        $userSession = array('error' => $output);
                        $this->session->set_userdata($userSession);
                        return FALSE;
                    }
                } else if(empty($dealer_phone_number) && $amount_due ==0){
                    
                    $output = "Please Enter Order ID OR Dealer Mobile Number";
                    $userSession = array('error' => $output);
                    $this->session->set_userdata($userSession);
                    return FALSE;
                } 
                return true;
               // break;
        }
    }

    /**
     * @desc: This function is used to update inventory of vendor
     * parmas: Booking ID
     * @return: void
     * 
     */
    function update_vendor_inventory($booking_id) {
        log_message('info', __FUNCTION__ ." Booking_ID: " .$booking_id);
        
        //Managing Vendor Inventory
        $_19_24_current_count = 0;
        $_26_32_current_count = 0;
        $_36_42_current_count = 0;
        $_43_current_count = 0;

        $booking_details = $this->booking_model->get_unit_details($booking_id);
        $service_center_details = $this->booking_model->getbooking_charges($booking_id);
        //Checking if Booking is of Tv and price tags is of Wall Mount Stand
        foreach ($booking_details as $value) {
            if ($value['service_id'] == 46 && $value['price_tags'] == 'Wall Mount Stand') {
                $stand_inch = explode(' ', $value['appliance_capacity'])[0];
                //Checking Brackets Capacity in inches
                if ($stand_inch >= 19 && $stand_inch <= 24) {
                    $_19_24_current_count = 1;
                } elseif ($stand_inch >= 26 && $stand_inch <= 32) {
                    $_26_32_current_count = 1;
                } elseif ($stand_inch >= 36 && $stand_inch <= 42) {
                    $_36_42_current_count = 1;
                } elseif ($stand_inch >= 43) {
                    $_43_current_count = 1;
                }
            }
        }

        //Checking if Booking ID data already exists in Inventory Database, then add row from last row of data minus current booking of stands inch
        $check_vendor = $this->inventory_model->check_data($service_center_details[0]['service_center_id']);
        if (!empty($check_vendor)) {

            //Getting last row of return array from Database    
            $last_updated_array = end($check_vendor);
            //Updating data in Inventory Database for particular Order ID and remarks as  _247AROUND_BRACKETS_RECEIVED   
            $updated_received_data[] = array(
                'vendor_id' => $service_center_details[0]['service_center_id'],
                'order_booking_id' => $booking_id,
                '19_24_current_count' => $last_updated_array['19_24_current_count'] - $_19_24_current_count,
                '26_32_current_count' => $last_updated_array['26_32_current_count'] - $_26_32_current_count,
                '36_42_current_count' => $last_updated_array['36_42_current_count'] - $_36_42_current_count,
                '43_current_count' => $last_updated_array['43_current_count'] - $_43_current_count,
                'increment/decrement' => 0,
                'remarks' => 'Booking ID'
            );

            $update_shipped_data_flag = $this->inventory_model->insert_inventory($updated_received_data);
            if ($update_shipped_data_flag) {
                //Logging Success
                log_message('info', __FUNCTION__ . '  Data has been Added in Inventory from Complete Booking ' . print_r($updated_received_data, TRUE));
            } else {
                //Logging Error
                log_message('info', __FUNCTION__ . ' Error in Adding data in Inventory from Complete Booking ' . print_r($updated_received_data, TRUE));
            }
        }
        //End inventory
    }

    /**
     * @Desc: This function is used to show Missed Calls view for Offline Partners work
     * @parmas: void
     * @return: view
     * 
     */
    function get_missed_calls_view() {
        $data['data'] = $this->partner_model->get_missed_calls_details();
        $data['cancellation_reason'] = $this->partner_model->get_missed_calls_cancellation_reason();
        $data['updation_reason'] = $this->partner_model->get_missed_calls_updation_reason();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/get_missed_calls_view', $data);
    }

    /**
     * @Desc: This function is used to update partner missed calls details on completion
     *          It is being called by AJAX
     * @params: id, status
     * @return: Boolean
     */
    function update_partner_missed_calls($id, $status) {
        $missed_call_leads = $this->partner_model->get_missed_calls_leads_by_id($id);
        //Incrementing counter by 1 , from its LATEST value
        $data['counter'] = ($missed_call_leads[0]['counter'] + 1);
        $data['status'] = $status;
        $data['update_date'] = date('Y-m-d H:i:s');
        $where = array('id' => $id);
        $update = $this->partner_model->update_partner_missed_calls($where, $data);
        //Add Log
        log_message('info', __FUNCTION__ . ' Partner Missed calls leads has been Completed for id ' . $id);
        //Adding details in Booking State Change
        $this->notify->insert_state_change("", _247AROUND_COMPLETED, _247AROUND_FOLLOWUP, "Lead Completed Phone: " . $missed_call_leads[0]['phone'], $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);

        echo $update;
    }

    /**
     * @Desc: This function is used to Cancel Leads
     * @params: POST ARRAY
     * @return: view
     * 
     */
    function cancel_missed_calls_lead() {
        $id = $this->input->post('id');
        $missed_call_leads = $this->partner_model->get_missed_calls_leads_by_id($id);
        if (!empty($missed_call_leads)) {
            //Incrementing counter by 1 , from its LATEST value
            $data['counter'] = ($missed_call_leads[0]['counter'] + 1);
            $data['status'] = 'Cancelled';
            $data['update_date'] = date('Y-m-d H:i:s');
            $data['cancellation_reason'] = $this->input->post('cancellation_reason');
            $where = array('id' => $id);
            $this->partner_model->update_partner_missed_calls($where, $data);

            //Add Log
            log_message('info', __FUNCTION__ . ' Partner Missed calls leads has been Cancelled for id ' . $id);

            //Adding details in Booking State Change
            $this->notify->insert_state_change("", _247AROUND_CANCELLED, _247AROUND_FOLLOWUP, $data['cancellation_reason'] . " Phone: " . $missed_call_leads[0]['phone'], $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);

            //$this->session->set_flashdata('cancel_leads', 'Leads has been cancelled for phone ' . $missed_call_leads[0]['phone']);
            redirect(base_url() . "employee/booking/get_missed_calls_view");
        } else {
            //$this->session->set_flashdata('cancel_leads', 'Leads had already Cancelled ');
            redirect(base_url() . "employee/booking/get_missed_calls_view");
        }
    }

    /**
     * @Desc: This function is used to Update Leads
     * @params: POST ARRAY
     * @return: view
     * 
     */
    function update_missed_calls_lead() {
        $id = $this->input->post('id');
        $missed_call_leads = $this->partner_model->get_missed_calls_leads_by_id($id);

        //When Customer Not Pick Call
        if ($this->input->post('updation_reason') == "Customer Not Picking Call") {
            //Incrementing counter by 1 , from its LATEST value
            $data['counter'] = ($missed_call_leads[0]['counter'] + 1);
            $data['update_date'] = date('Y-m-d H:i:s');
            $data['action_date'] = date("Y-m-d H:i:s", strtotime('+5 hours'));
            $data['updation_reason'] = $this->input->post('updation_reason');
            $where = array('id' => $id);
            $update = $this->partner_model->update_partner_missed_calls($where, $data);

            //Add Log
            log_message('info', __FUNCTION__ . ' Partner Missed calls leads has been Updated for id ' . $id);
        } // 1 Day scheduled
        else if ($this->input->post('updation_reason') == "Customer asked to call after 1 day") {

            //Incrementing counter by 1 , from its LATEST value
            $data['counter'] = ($missed_call_leads[0]['counter'] + 1);
            $data['update_date'] = date('Y-m-d H:i:s');
            $data['action_date'] = date("Y-m-d ", strtotime('+1 days'));
            $data['updation_reason'] = $this->input->post('updation_reason');
            $where = array('id' => $id);
            $update = $this->partner_model->update_partner_missed_calls($where, $data);

            //Add Log
            log_message('info', __FUNCTION__ . ' Partner Missed calls leads has been Updated for 1 Days - id ' . $id);
        }   // 2 Day Scheduled
        else if ($this->input->post('updation_reason') == "Customer asked to call after 2 day") {

            //Incrementing counter by 1 , from its LATEST value
            $data['counter'] = ($missed_call_leads[0]['counter'] + 1);
            $data['update_date'] = date('Y-m-d H:i:s');
            $data['action_date'] = date("Y-m-d ", strtotime('+2 days'));
            $data['updation_reason'] = $this->input->post('updation_reason');
            $where = array('id' => $id);
            $update = $this->partner_model->update_partner_missed_calls($where, $data);

            //Add Log
            log_message('info', __FUNCTION__ . ' Partner Missed calls leads has been Updated for 2 Days - id ' . $id);
        } // 3 day scheduled
        else if ($this->input->post('updation_reason') == "Customer asked to call after 3 day") {

            //Incrementing counter by 1 , from its LATEST value
            $data['counter'] = ($missed_call_leads[0]['counter'] + 1);
            $data['update_date'] = date('Y-m-d H:i:s');
            $data['action_date'] = date("Y-m-d ", strtotime('+3 days'));
            $data['updation_reason'] = $this->input->post('updation_reason');
            $where = array('id' => $id);
            $update = $this->partner_model->update_partner_missed_calls($where, $data);

            //Add Log
            log_message('info', __FUNCTION__ . ' Partner Missed calls leads has been Updated for 3 Days - id ' . $id);
        }

        //Adding details in Booking State Change
        $this->notify->insert_state_change("", _247AROUND_FOLLOWUP, _247AROUND_FOLLOWUP, $this->input->post('updation_reason') . " Phone: " . $missed_call_leads[0]['phone'], $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);

        $this->session->set_flashdata('update_leads', 'Leads has been Updated for phone ' . $missed_call_leads[0]['phone']);
        redirect(base_url() . "employee/booking/get_missed_calls_view");
    }

    /**
     * @Desc: This function is used to update the pay to sf flag in booking details table
     * @parmas: void
     * @return: view
     * 
     */
    function update_not_pay_to_sf_booking() {
        log_message('info', __FUNCTION__);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/update_pay_to_sf_booking');
    }

    /**
     * @Desc: This function is used to update the pay to sf flag in booking details table
     * @parmas: void
     * @return: view
     * 
     */
    function process_update_not_pay_to_sf_booking() {
        log_message('info', __FUNCTION__);

        $booking_id = ($this->input->post('booking_id'));
        if (!empty($booking_id)) {
            foreach ($booking_id as $value) {
                if (!empty($value)) {
                    $is_wall_mount_exist = $this->booking_model->get_unit_details(array('booking_id' => trim($value), 'price_tags' => 'Installation & Demo'));
                    if (!empty($is_wall_mount_exist)) {
                        $this->booking_model->update_booking_unit_details_by_any(array('booking_id' => trim($value), 'price_tags' => 'Installation & Demo'), array('pay_to_sf' => '0'));
                        log_message('info', __FUNCTION__ . ' Pay To SF update in booking_unit_details for Booking ID = ' . trim($value));
                    }
                }
            }
            $this->session->set_flashdata('msg', 'Booking Updated Successfully');
            redirect(base_url() . "employee/booking/update_not_pay_to_sf_booking");
        } else {
            redirect(base_url() . "employee/booking/update_not_pay_to_sf_booking");
        }
    }

    function auto_assigned_booking() {
        $data['data'] = $this->vendor_model->auto_assigned_booking();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/auto_assigned_booking', $data);
    }

    /**
     *  @desc : This function displays list of pending bookings according to pagination and partner_id also show all booking if $page is All.
     *  @param : Starting page & number of results per page
     *  @return : pending bookings according to pagination
     */
    function get_pending_booking_by_partner_id($page = 0, $offset = '0') {

        if ($page == 0) {
            $page = 50;
        }
        // $offset = ($this->uri->segment(5) != '' ? $this->uri->segment(5) : 0);
        $partner_id = true;

        $config['base_url'] = base_url() . 'employee/booking/get_pending_booking_by_partner_id/' . $page;
        $config['total_rows'] = $this->booking_model->total_pending_booking("", "", $partner_id);

        if ($offset != "All") {
            $config['per_page'] = $page;
        } else {
            $config['per_page'] = $config['total_rows'];
        }

        $config['uri_segment'] = 5;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';

        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['Count'] = $config['total_rows'];
        $data['Bookings'] = $this->booking_model->date_sorted_booking($config['per_page'], $offset, "", "", $partner_id);


        if ($this->session->flashdata('result') != '') {
            $data['success'] = $this->session->flashdata('result');
        }
        if (isset($_SESSION['result'])) {
            unset($_SESSION['result']);
        }
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/booking', $data);
    }

    function booking_upcountry_details($service_center_id, $booking_id, $is_customer_paid) {
        if ($is_customer_paid > 0) {
            $is_customer_paid = 1;
        }
        $data['data'] = $this->upcountry_model->upcountry_booking_list($service_center_id, $booking_id, true, $is_customer_paid);

        $this->load->view('service_centers/upcountry_booking_details', $data);
    }

    /**
     *  @desc : This function is used to validate the uploaded support file for order id
     *  @param : void
     *  @return : boolean
     */
    function validate_upload_orderId_support_file() {
        if (!empty($_FILES['support_file']['tmp_name'])) {
            $MB = 1048576;
            if ($_FILES['support_file']['size'] < 5 * $MB) {
                return true;
            } else {
                $this->form_validation->set_message('validate_upload_orderId_suppoart_file', 'Uploaded File Size Must be Less than 5MB');
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     *  @desc : This function is used to upload the support file for order id to s3 and save into database
     *  @param : string $booking_primary_contact_no
     *  @return : boolean/string
     */
    function upload_orderId_support_file($booking_id) {

        $support_file_name = false;

        if (($_FILES['support_file']['error'] != 4) && !empty($_FILES['support_file']['tmp_name'])) {

            $tmpFile = $_FILES['support_file']['tmp_name'];
            $support_file_name = $booking_id . '_orderId_support_file_' . substr(md5(uniqid(rand(0, 9))), 0, 15) . "." . explode(".", $_FILES['support_file']['name'])[1];
            //move_uploaded_file($tmpFile, TMP_FOLDER . $support_file_name);
            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "misc-images/" . $support_file_name;
            $upload_file_status = $this->s3->putObjectFile($tmpFile, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            if($upload_file_status){
                //Logging success for file uppload
                log_message('info', __METHOD__ . 'Support FILE has been uploaded sucessfully for booking_id: '.$booking_id);
                return $support_file_name;
            }else{
                //Logging success for file uppload
                log_message('info', __METHOD__ . 'Error In uploading support file for booking_id: '.$booking_id);
                return False;
            }

        }

        
    }
    
    /**
     *  @desc : This function is used to send sms to user after rating
     *  @param : string $phone_no
     *  @param : string $booking_primary_contact_no
     *  @param : string $rating
     *  @param : string $booking_id
     *  @return : void
     */
    public function send_rating_sms($phone_no, $rating,$user_id,$booking_id) {
        if ($rating < '3') {
            $sms['tag'] = "poor_rating_on_completion";
        }else if($rating === '3'){
            $sms['tag'] = "avg_rating_on_completion";
        }else if ($rating > '3') {
            $sms['tag'] = "good_rating_on_completion";
        }
        
        $sms['smsData']['rating_no'] = $rating;
        $sms['phone_no'] = $phone_no;
        $sms['booking_id'] = $booking_id;
        $sms['type'] = "rating";
        $sms['type_id'] = $user_id;
        
        $this->notify->send_sms_msg91($sms);
        log_message('info', __METHOD__ . ' SMS Sent for rating' . $phone_no);
    }
    
    
    /**
     *  @desc : This function is used to show those numbers who gave missed call after sending rating sms
     *  @param : void
     *  @return : void
     */
    public function show_missed_call_rating_data(){
        $data['missed_call_rating_data'] = $this->booking_model->get_missed_call_rating_not_taken_booking_data();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/show_missed_call_rating_data', $data);
    }
    
    
    /**
     *  @desc : This function is used to show view for booking based on booking status type
     *  @param : $status string
     *  @param : $booking_id string
     *  @return : void();
     */
    public function view_bookings_by_status($status,$booking_id=""){
        $data['booking_status'] = trim($status);
        $data['booking_id'] = trim($booking_id);
        $data['partners'] = $this->partner_model->getpartner_details('partners.id,partners.public_name',array('is_active'=> '1'));
        $data['sf'] = $this->vendor_model->getVendorDetails('id,name',array('active' => '1'));
        $data['services'] = $this->booking_model->selectservice();
        $data['cities'] = $this->booking_model->get_advance_search_result_data("booking_details","DISTINCT(city)",NULL,NULL,NULL,array('city'=>'ASC'));
       $this->miscelleneous->load_nav_header();
        if(strtolower($data['booking_status']) == 'pending'){
            $this->load->view('employee/view_pending_bookings', $data);
        }else{
            $this->load->view('employee/view_bookings_by_status', $data);
        }
        
    }
    
    
    /**
     *  @desc : This function is used to get bookings based on booking status type
     *  @param : $status string
     *  @return : $output JSON
     */
    public function get_bookings_by_status($status){
        
        $booking_status = trim($status);
        $data = $this->get_bookings_data_by_status($booking_status);
        
        $post = $data['post'];
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->booking_model->count_all_bookings_by_status($post),
            "recordsFiltered" =>  $this->booking_model->count_filtered_bookings_by_status($post),
            "data" => $data['data'],
        );
        
        echo json_encode($output);
    }
    
    
    /**
     *  @desc : This function is used to get bookings based on booking status type
     *  @param : $booking_status string
     *  @return : $output Array()
     */
    private function get_bookings_data_by_status($booking_status) {
        $post = $this->get_post_data();
        $new_post = $this->get_filterd_post_data($post,$booking_status,'booking');
        
        $select = "services.services,users.name as customername,penalty_on_booking.active as penalty_active,
            users.phone_number, booking_details.*, service_centres.name as service_centre_name,
            service_centres.district as city, service_centres.primary_contact_name,
            service_centres.primary_contact_phone_1,STR_TO_DATE(booking_details.booking_date,'%d-%m-%Y') as booking_day";
        //RM Specific Bookings
         $sfIDArray =array();
        if($this->session->userdata('user_group') == 'regionalmanager'){
            $rm_id = $this->session->userdata('id');
            $rmServiceCentersData= $this->reusable_model->get_search_result_data("employee_relation","service_centres_id",array("agent_id"=>$rm_id),NULL,NULL,NULL,NULL,NULL);
            $sfIDList = $rmServiceCentersData[0]['service_centres_id'];
            $sfIDArray = explode(",",$sfIDList);
        }
        
        $list = $this->booking_model->get_bookings_by_status($new_post,$select,$sfIDArray);
        unset($new_post['order_performed_on_count']);
        $data = array();
        $no = $post['start'];
        if(strtolower($booking_status) == 'pending'){
            foreach ($list as $order_list) {
                $no++;
                $row = $this->get_pending_bookings_table($order_list, $no,$booking_status);
                $data[] = $row;
            }
        }else{
            foreach ($list as $order_list) {
                $no++;
                $row = $this->get_completed_cancelled_bookings_table($order_list, $no,$booking_status);
                $data[] = $row;
            }
        }

        return array(
            'data' => $data,
            'post' => $new_post
            
        );
    }
    
    
    /**
     *  @desc : This function is used to make filter logic for booking based on status type
     *  @param : $post string
     *  @param : $booking_status string
     *  @return : $post Array()
     */
    private function get_filterd_post_data($post,$booking_status,$type){
        $partner_id = $this->input->post('partner_id');
        $sf_id = $this->input->post('sf_id');
        $date_range = $this->input->post('booking_date_range');
        $booking_id = $this->input->post('booking_id');
        $ratings = $this->input->post('ratings');
        $appliance = $this->input->post('appliance');
        $booking_date = $this->input->post('booking_date');
        $city = $this->input->post('city');
        
        if($type == 'booking'){
            if($booking_status == _247AROUND_COMPLETED || $booking_status == _247AROUND_CANCELLED){
                $post['where']  = array('current_status' => $booking_status,'type' => 'Booking');
            }else if(strtolower($booking_status) == 'pending' && empty ($booking_id)){
                $post['where']  = array("current_status IN ('Pending','Rescheduled')" => NULL,
                    "DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= -1" => NULL );
                $post['order'] = array(array('column' => 0,'dir' => 'desc'));
                $post['order_performed_on_count'] = TRUE;
            }
        }else if($type == 'query'){
            $post['where']['current_status'] = $booking_status;
            $post['order_by'] = "CASE WHEN booking_details.internal_status = 'Missed_call_confirmed' THEN 'a' WHEN  booking_details.booking_date = '' THEN 'b' WHEN  booking_details.booking_date = '' THEN 'b' ELSE 'c' END , booking_day";
        }
        
        
        if(!empty($booking_id)){
            $post['where']['booking_details.booking_id'] =  $booking_id;
        }
        if(!empty($partner_id)){
            $post['where']['booking_details.partner_id'] =  $partner_id;
        }
        if(!empty($sf_id)){
            $post['where']['booking_details.assigned_vendor_id'] =  $sf_id;
        }
        if(!empty($date_range)){
            $booking_closed_date = explode("-", $date_range);
            $post['where']['booking_details.closed_date >= '] =  date("Y-m-d", strtotime(trim($booking_closed_date[0])));
            $post['where']['booking_details.closed_date < '] = date('Y-m-d', strtotime('+1 day', strtotime(trim($booking_closed_date[1]))));
        }
        if(!empty($ratings)){
            switch ($ratings){
                case 'a':
                    $post['where']['rating_stars IS NOT NULL'] = NULL;
                    break;
                case 'b':
                    $post['where']['rating_stars IS NULL'] = NULL;
                    break;
                case 'c':
                    '';
                    break;
            }
        }
        
        if(!empty($appliance)){
            $post['where']['booking_details.service_id'] =  $appliance;
        }
        
        if(!empty($booking_date)){
            $post['where']['booking_details.booking_date = '] =  trim($booking_date);
        }
        
        if(!empty($city)){
            $post['where']['booking_details.city = '] =  trim($city);
        }
        
        $post['column_order'] = array('booking_day');
        $post['column_search'] = array('booking_details.booking_id','booking_details.partner_id','booking_details.assigned_vendor_id','booking_details.closed_date','booking_details.booking_primary_contact_no','booking_details.query_remarks','booking_unit_details.appliance_brand','booking_unit_details.appliance_category','booking_unit_details.appliance_description','booking_details.city');
        
        return $post;
    }

    /**
     *  @desc : This function is used to get the post data for booking by status
     *  @param : void()
     *  @return : $post Array()
     */
    private function get_post_data(){
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');

        return $post;
    }
    
    /**
     *  @desc : This function is used to make the table for bookings by status
     *  @param : $order_list string
     *  @param : $no string
     *  @param : $booking_status string
     *  @return : $row Array()
     */
    private function get_completed_cancelled_bookings_table($order_list, $no, $booking_status){
        $row = array();
        if($order_list->is_upcountry === '1'){
            $sn = "<i class='fa fa-road' aria-hidden='true' onclick='";
            $sn .= "open_upcountry_model(".'"'.$order_list->assigned_vendor_id.'"';
            $sn .= ', "'.$order_list->booking_id.'"';
            $sn .= ', "'.$order_list->amount_due.'"';
            $sn .= ")' style='color:red; font-size:20px;'></i>";
        }else{
            $sn = "";
        }
        
        $call_btn = "<button type='button' class='btn btn-sm btn-color' onclick='";
        $call_btn .= "outbound_call(".'"'.$order_list->booking_primary_contact_no.'"';
        $call_btn .= ")' '><i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i></button>";
        
        if ($order_list->current_status == 'Completed' && empty($order_list->rating_stars )){
            $rating_btn_disabled = "";
        }else{
            $rating_btn_disabled = "disabled";
        }
        
        if(empty($order_list->penalty_active)){
            $penalty_row = "<a class='btn btn-sm btn-color col-md-12' href='javascript:void(0);' title='Remove Penalty' target='_blank' style='margin-top:10px;cursor:not-allowed;opacity:0.5;'><i class='fa fa-times-circle' aria-hidden='true'></i></a>";
        }else if($order_list->penalty_active === '1'){
            $penalty_modal = "onclick='";
            $penalty_modal .= "get_penalty_details(".'"'.$order_list->booking_id.'"';
            $penalty_modal .= ', "'.$booking_status.'"';
            $penalty_modal .= ', "'.$order_list->assigned_vendor_id.'"';
            $penalty_modal .= ")' ";
            $penalty_row = "<a class='btn btn-sm btn-color col-md-12' href='javascript:void(0);' title='Remove Penalty' target='_blank' style='margin-top:10px;' $penalty_modal><i class='fa fa-times-circle' aria-hidden='true'></i></a>";
        }
        
        
        
        $row[] = $no.$sn;
        $row[] = "<a href='"."https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/jobcards-pdf/".$order_list->booking_jobcard_filename."'>$order_list->booking_id</a>";
        $row[] = "<a class='col-md-12' href='".base_url()."employee/user/finduser?phone_number=".$order_list->phone_number."'>$order_list->customername</a>"."<b>".$order_list->booking_primary_contact_no."</b>";
        $row[] = $order_list->services;
        $row[] = "<a href='".base_url()."employee/vendor/viewvendor/".$order_list->assigned_vendor_id."'>$order_list->service_centre_name</a>";
        $row[] = $order_list->city;
        $row[] = date("d-m-Y", strtotime($order_list->closed_date));
        $row[] = $call_btn;
        if($booking_status === _247AROUND_COMPLETED){
            $row[] = "<a id='edit' class='btn btn-sm btn-color' href='".base_url()."employee/booking/get_complete_booking_form/".$order_list->booking_id."' title='Edit'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
            $row[] = "<a id='cancel' class='btn btn-sm btn-color' href='".base_url()."employee/booking/get_cancel_form/".$order_list->booking_id."' title='Cancel'><i class='fa fa-times' aria-hidden='true'></i></a>";
        }else if($booking_status === _247AROUND_CANCELLED){
            $row[] = "<a id='edit' class='btn btn-sm btn-color' href='".base_url()."employee/booking/get_complete_booking_form/".$order_list->booking_id."' title='Edit'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
        }
        
        if($this->session->userdata('user_group') === _247AROUND_CALLCENTER){
            if($booking_status === _247AROUND_CANCELLED && strtotime($order_list->closed_date) <= strtotime("-1 Months")){
                $row[] = "<a id='open' class='btn btn-sm btn-color' href='".base_url()."employee/booking/get_convert_booking_to_pending_form/".$order_list->booking_id."/".$booking_status."' title='Open' target='_blank' disabled><i class='fa fa-calendar' aria-hidden='true'></i></a>";
            }else{
                $row[] = "<a id='open' class='btn btn-sm btn-color' href='".base_url()."employee/booking/get_convert_booking_to_pending_form/".$order_list->booking_id."/".$booking_status."' title='Open' target='_blank'><i class='fa fa-calendar' aria-hidden='true'></i></a>";
            }
        }else{
            $row[] = "<a id='open' class='btn btn-sm btn-color' href='".base_url()."employee/booking/get_convert_booking_to_pending_form/".$order_list->booking_id."/".$booking_status."' title='Open' target='_blank'><i class='fa fa-calendar' aria-hidden='true'></i></a>";
        }
        
        $row[] = "<a id='view' class='btn btn-sm btn-color' href='".base_url()."employee/booking/viewdetails/".$order_list->booking_id."' title='view' target='_blank'><i class='fa fa-eye' aria-hidden='true'></i></a>";
        
        if($booking_status === _247AROUND_COMPLETED){
            $row[] = "<a class='btn btn-sm btn-color' href='".base_url()."employee/booking/get_rating_form/".$order_list->booking_id."/".$booking_status."' title='Rating' target='_blank' $rating_btn_disabled><i class='fa fa-star-o' aria-hidden='true' ></i></a>";
        }
        
        $row[] = "<a class='btn btn-sm btn-color col-md-12' href='".base_url()."employee/vendor/get_escalate_booking_form/".$order_list->booking_id."/".$booking_status."' title='Add Penalty' target='_blank'><i class='fa fa-plus-square' aria-hidden='true'></i></a>".$penalty_row;
        
        
        return $row;
    }
    /*
     * This function use to fetch view for booking advance search
     */
    function booking_advance_search(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/advanced_search');
    }
    
    /*
     * This function use to fetch options for advance search on bookings
     * convert final array into json and echo that, angular js will pick it automatically
     */
    function get_booking_advanced_search_optionlist(){
        $data['partners'] = $this->booking_model->get_advance_search_result_data("bookings_sources","partner_id,source",NULL,NULL,NULL,array('source'=>'ASC'));
        $data['service_centers'] = $this->booking_model->get_advance_search_result_data("service_centres","id,name",array('active'=>1),NULL,NULL,array('name'=>'ASC'));
        $data['internal_status'] = $this->booking_model->get_advance_search_result_data("booking_details","DISTINCT(internal_status) as status",NULL,NULL,NULL,array('internal_status'=>'ASC'));
        $data['current_status'] = $this->booking_model->get_advance_search_result_data("booking_details","DISTINCT(current_status)as current_status",NULL,NULL,NULL,array('current_status'=>'ASC'));
        $data['cities'] = $this->booking_model->get_advance_search_result_data("booking_details","DISTINCT(city)",NULL,NULL,NULL,array('city'=>'ASC'));
        $data['states'] = $this->booking_model->get_advance_search_result_data("booking_details","DISTINCT(state)",NULL,NULL,NULL,array('state'=>'ASC'));
        $data['service'] = $this->booking_model->get_advance_search_result_data("services","services,id",array('isBookingActive'=>1),NULL,NULL,array('services'=>'ASC'));
        $data['brands'] = $this->booking_model->get_advance_search_result_data("appliance_brands","brand_name",NULL,NULL,NULL,array('brand_name'=>'ASC'));
        $data['category'] = $this->booking_model->get_advance_search_result_data("service_centre_charges","DISTINCT(category)",NULL,NULL,NULL,array('category'=>'ASC'));
        $data['capacity'] = $this->booking_model->get_advance_search_result_data("service_centre_charges","DISTINCT(capacity)",NULL,NULL,NULL,array('capacity'=>'ASC'));
        $data['request_type'] = $this->booking_model->get_advance_search_result_data("booking_unit_details","DISTINCT(price_tags) as request_type",NULL,NULL,NULL,array('price_tags'=>'ASC'));
        $data['is_upcountry'] = array(array("option"=>'yes',"value"=>'1'),array("option"=>'No',"value"=>'0'));
        $data['paid_by'] = array(array("option"=>'Customer'),array("option"=>'Partner'));
        $data['product_or_service'] = array(array("option"=>'Product'),array("option"=>'Service'));
        $data['ratings'] = array(array("option"=>'-1'),array("option"=>'1'),array("option"=>'2'),array("option"=>'3'),array("option"=>'4'),array("option"=>'5'));
        echo json_encode($data);
    }
    
    /*
     * This function use to create where array for common where search
     * @input 1st- $dbfieldArray(associative array key(option name which we get in request from filter) value(database field associated to that option name) )
     * @input 2nd - $optionArray(associative array key(option name which we get in request from filter (which can be handled from direct where condition)) value(selected value against those option names))
     * @output - $whereArray( where condition array which we can directly pass to any query)
     */ 
    function get_common_where_array($dbfieldArray,$optionArray){
        $whereArray = array();
        foreach($optionArray as $column => $value){
            if(!empty($value)){
                $whereArray[$dbfieldArray[$column]] =$value;
            }
        }
       return $whereArray;     
    }
    
    /*
     * This function use to where array for common where search
     * @input - $dateRange((String)),$separator((String) to seprate both dates),$whereArray((Array)already have some where condition values,$optionName((String)database column affected by this date range),$requiredFormat((String) ('Y-m-d' or 'd-Y-m') in which format you want output)
     * @output - $whereArray(where condition array which we can directly pass to any query)
     */ 
    function get_dates_range_where_array($dateRange,$separator,$whereArray=NULL,$optionName,$requiredFormat){
        $datesArray = explode($separator,$dateRange);
        $whereArray[$optionName. " >= "] = date($requiredFormat, strtotime(trim($datesArray[0])));
        $whereArray[$optionName. " < "] = date($requiredFormat, strtotime('+1 day', strtotime(trim($datesArray[1]))));
        return $whereArray;
    }
    
     /*
     * This function use to where array for common where search
     * @input 1st- $dbfieldArray(associative array key(option name which we get in request from filter) value(database field associated to that option name) )
     * @input 2nd - $whereOptionArray(associative array key(option name which we get in request from filter (which can be handled from direct where condition)) value(selected value against those option names))
     * @input 3rd - receieved_data(data we receieved from filter result request)
     * @output - $whereArray( where condition array which we can directly pass to any query)
     */ 
    function get_all_where_array_for_advance_search($dbfield_mapinning_option,$whereOptionArray,$receieved_Data){
        $whereArray = $this->get_common_where_array($dbfield_mapinning_option,$whereOptionArray);
        if(!empty($receieved_Data['booking_date'])){
           $whereArray = $this->get_dates_range_where_array($receieved_Data['booking_date']," - ",$whereArray,$dbfield_mapinning_option['booking_date'],"Y-m-d");
        }
        if(!empty($receieved_Data['close_date'])){
          $whereArray = $this->get_dates_range_where_array($receieved_Data['close_date']," - ",$whereArray,$dbfield_mapinning_option['close_date'],"Y-m-d");
        }
        if(!empty($receieved_Data['paid_by'])){
            if($receieved_Data['paid_by'] == 'Customer'){
                $whereArray['booking_unit_details.customer_net_payable >'] = '0';
            }
            else{
                $whereArray['booking_unit_details.customer_net_payable'] = '0';
            }
        }
        return $whereArray;
    }
    /*
     * This function use to send search result data back to filter
     * @output - it's print required json, which is automatically used by dataTables
     */ 
    function get_advance_search_result_data($receieved_Data){
        $finalArray = array();
        //array of filter options name and affected database field by them
        $dbfield_mapinning_option = array('booking_date'=>'STR_TO_DATE(booking_details.booking_date, "%d-%m-%Y")', 'close_date'=>'date(booking_details.closed_date)',
            'partner'=>'booking_details.partner_id','sf'=>'booking_details.assigned_vendor_id','city'=>'booking_details.city','current_status'=>'booking_details.current_status',
            'internal_status'=>'booking_details.internal_status','product_or_service'=>'booking_unit_details.product_or_services','upcountry'=>'booking_details.is_upcountry',
            'rating'=>'booking_details.rating_stars','service'=>'booking_details.service_id','categories'=>'booking_unit_details.appliance_category','capacity'=>'booking_unit_details.appliance_capacity',
            'brand'=>'booking_unit_details.appliance_brand','paid_by'=>'booking_unit_details.customer_net_payable','request_type'=>'booking_unit_details.price_tags','state'=>'booking_details.state');
        // array of filtered options and there selected values (which can be handled by direct where condition)
        $whereOptionArray = elements(array('partner','city','sf','internal_status','product_or_service','upcountry','rating','categories','capacity','brand','request_type'), $receieved_Data);
        //join condition array table name and join condition
        $joinDataArray = array("bookings_sources"=>"bookings_sources.partner_id=booking_details.partner_id","service_centres"=>"service_centres.id=booking_details.assigned_vendor_id",
            "services"=>"services.id=booking_details.service_id","booking_unit_details"=>"booking_unit_details.booking_id=booking_details.booking_id");
        // select field to display
        $select = "booking_details.booking_id,bookings_sources.source,booking_details.city,service_centres.company_name,services.services,booking_unit_details.appliance_brand,"
                . "booking_unit_details.appliance_category,booking_unit_details.appliance_capacity,booking_unit_details.price_tags,booking_unit_details.product_or_services,booking_details."
                . "current_status";
        // limit array for pagination
        $limitArray = array('length'=>$receieved_Data['length'],'start'=>$receieved_Data['start']);
       // all where condition array
        $whereArray = $this->get_all_where_array_for_advance_search($dbfield_mapinning_option,$whereOptionArray,$receieved_Data);
        //where in array
        $currentStatusArray = explode(",",$receieved_Data['current_status']);
        $serviceArray = explode(",",$receieved_Data['service']);
        $stateArray = explode(",",$receieved_Data['state']);
        $whereInArray = NULL;
        if($receieved_Data['current_status']){
            $whereInArray['booking_details.current_status'] = $currentStatusArray;
        }
        if($receieved_Data['service']){
            $whereInArray['booking_details.service_id'] = $serviceArray;
        }
        if($receieved_Data['state']){
            $whereInArray['booking_details.state'] = $stateArray;
        }
        $JoinTypeTableArray = array('service_centres'=>'left','bookings_sources'=>'left','booking_unit_details'=>'left','services'=>'left');
       //process query and get result from database
        $result = $this->booking_model->get_advance_search_result_data("booking_details",$select,$whereArray,$joinDataArray,$limitArray,array("booking_details.booking_id"=>"ASC"),
                $whereInArray,$JoinTypeTableArray);
        //convert database result into a required formate needed for datatales
        for($i=0;$i<count($result);$i++){
            $index = $receieved_Data['start']+($i+1);
            $tempArray = array_values($result[$i]);
            array_unshift($tempArray, $index);
            $finalArray[] = $tempArray;
        }
        //create final array required for database table\
        $data['draw'] = $receieved_Data['draw'];
        //get all records from table
        $data['recordsTotal'] = $this->booking_model->get_advance_search_result_count("booking_details",$select,NULL,$joinDataArray,NULL,NULL,NULL,$JoinTypeTableArray);
       // get filtered records from tabble
        $data['recordsFiltered'] = $this->booking_model->get_advance_search_result_count("booking_details",$select,$whereArray,$joinDataArray,NULL,NULL,$whereInArray,$JoinTypeTableArray);
        $data['data'] = $finalArray;
        return $data;
    }   
    function get_advance_search_result_view(){
        $receieved_Data = $this->input->post(); 
        $data = $this->get_advance_search_result_data($receieved_Data);
        foreach ($data['data'] as $index=>$serachResultData){
            $booking_with_link = "<a href =".base_url() . "employee/booking/viewdetails/".$serachResultData[1]." target='_blank'>".$serachResultData[1]."</a>";
            $data['data'][$index][1] = $booking_with_link;
        }
        echo json_encode($data);
    }
       
    function download_booking_snapshot(){
       ob_start();
       $receieved_Data = $this->input->post();
       if(isset($receieved_Data['current_status'])){
            $receieved_Data['current_status'] = implode(',',$receieved_Data['current_status']);
       }
       else{
           $receieved_Data['current_status'] = '';
       }
       if(isset($receieved_Data['service'])){
            $receieved_Data['service'] = implode(',',$receieved_Data['service']);
       }
       else{
           $receieved_Data['service'] = '';
       }
        if(isset($receieved_Data['state'])){
            $receieved_Data['state'] = implode(',',$receieved_Data['state']);
       }
       else{
           $receieved_Data['state'] = '';
       }
       $is_not_empty = FALSE;
       foreach($receieved_Data as $values){
           if($values){
               $is_not_empty = TRUE;
               break;
           }
       }
       if($is_not_empty){
                $receieved_Data['length'] = -1;
                $receieved_Data['start'] = 0;
                $receieved_Data['draw'] = 1;
                $data = $this->get_advance_search_result_data($receieved_Data);
                $headings = array("S.no","Booking ID","Partner","City","Service Center","Service","Brand","Category","Capacity","Request Type","Product/Service","Current_status");
                $this->miscelleneous->downloadCSV($data['data'],$headings,"booking_search_summary");   
       }
       else{
           redirect(base_url() . "employee/booking/booking_advance_search");
       }
    }
    
    /**
     *  @desc : This function is used to make the table for pending bookings
     *  @param : $order_list string
     *  @param : $no string
     *  @param : $booking_status string
     *  @return : $row Array()
     */
    private function get_pending_bookings_table($order_list, $no, $booking_status){
        $row = array();
        if($order_list->is_upcountry === '1'){
            $sn = "<i class='fa fa-road' aria-hidden='true' onclick='";
            $sn .= "open_upcountry_model(".'"'.$order_list->assigned_vendor_id.'"';
            $sn .= ', "'.$order_list->booking_id.'"';
            $sn .= ', "'.$order_list->amount_due.'"';
            $sn .= ")' style='color:red; font-size:20px;'></i>";
        }else{
            $sn = "";
        }
        
        $call_btn = "<button type='button' class='btn btn-sm btn-color' onclick='";
        $call_btn .= "outbound_call(".'"'.$order_list->booking_primary_contact_no.'"';
        $call_btn .= ")' '><i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i></button>";
        
        if ($order_list->current_status == 'Completed' && empty($order_list->rating_stars )){
            $rating_btn_disabled = "";
        }else{
            $rating_btn_disabled = "disabled";
        }
        
        if(empty($order_list->penalty_active)){
            $penalty_row = "<a class='btn btn-sm btn-color col-md-12' href='javascript:void(0);' title='Remove Penalty' target='_blank' style='margin-top:10px;cursor:not-allowed;opacity:0.5;'><i class='fa fa-times-circle' aria-hidden='true'></i></a>";
        }else if($order_list->penalty_active === '1'){
            $penalty_modal = "onclick='";
            $penalty_modal .= "get_penalty_details(".'"'.$order_list->booking_id.'"';
            $penalty_modal .= ', "'.$booking_status.'"';
            $penalty_modal .= ', "'.$order_list->assigned_vendor_id.'"';
            $penalty_modal .= ")' ";
            $penalty_row = "<a class='btn btn-sm btn-color col-md-12' href='javascript:void(0);' title='Remove Penalty' target='_blank' style='margin-top:10px;' $penalty_modal><i class='fa fa-times-circle' aria-hidden='true'></i></a>";
        }
        
        if($order_list->count_escalation > 0){
            $escalation = "<div class='esclate blink'>Escalated</div> <b>".$order_list->count_escalation." <b> Times";
        }else{
            $escalation = "";
        }
        
        if(!empty($order_list->service_centre_name)){
            $sf = $order_list->service_centre_name." / ".$order_list->primary_contact_name." / ".$order_list->primary_contact_phone_1;
        }else{
            $sf = "";
        }
        
        if ($order_list->assigned_vendor_id == "") {
            $complete =  "<a target='_blank' class='btn btn-sm btn-color btn-sm disabled' "
            . "href=" . base_url() . "employee/booking/get_complete_booking_form/$order_list->booking_id title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true' ></i></a>";
        } else {
            if ($order_list->current_status == 'Pending' || $order_list->current_status == 'Rescheduled') {
                $complete = "<a target='_blank' class='btn btn-sm btn-color btn-sm' "
                . "href=" . base_url() . "employee/booking/get_complete_booking_form/$order_list->booking_id title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true' ></i></a>";
            } else if ($order_list->current_status == 'Review') {
                $complete = "<a target='_blank' class='btn btn-sm btn-color btn-sm' "
                . "href=" . base_url() . "employee/booking/review_bookings/$order_list->booking_id title='Complete'><i class='fa fa-eye-slash' aria-hidden='true' ></i></a>";
            } else {
                $complete = "<a target='_blank' class='btn btn-sm btn-color btn-sm disabled' "
                . "href=" . base_url() . "employee/booking/get_complete_booking_form/$order_list->booking_id title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true' ></i></a>";
            }
        }
        
        if (!is_null($order_list->assigned_vendor_id) && !is_null($order_list->booking_jobcard_filename) && ($order_list->mail_to_vendor == 0)) {
            $mail =  "<a  id='b_notes" . $no . "' class='btn btn-sm btn-color' onclick='show(this.id)' title='Mail'><i class='fa fa-envelope-o' aria-hidden='true'></i></a>";
            $mail .= "<div class='dialog' id='bookingMailForm" . $no . "'>";
            $mail .= "<form class='mailform'>";
            $mail .= "<textarea style='width:200px;height:80px;' id='valueFromMyButton" . $no . "' name='valueFromMyButton" . $no . "' placeholder='Enter Additional Notes'></textarea>";
            $mail .= "<input type='hidden' id='booking_id" . $no . "' name='booking_id" . $no . "' value=$order_list->booking_id >";
            $mail .= "<div align='center'>";
            $mail .= "<a id='btnOK" . $no . "' class='btn btn-sm btn-success' onclick='send_email_to_vendor(" . $no . ");'>Ok</a>";
            $mail .= "</div>";
            $mail .= "</form>";
            $mail .= "</div>";
        } else {
            $mail = "<a class='btn btn-sm btn-color disabled' href='" . base_url() . "employee/bookingjobcard/send_mail_to_vendor/$order_list->booking_id' title='Mail'><i class='fa fa-envelope-o' aria-hidden='true' ></i></a>";
        }
        
        if (!is_null($order_list->assigned_vendor_id) && !is_null($order_list->booking_jobcard_filename) && ($order_list->mail_to_vendor)) {
            $r_mail = "<a id='r_notes" . $no . "' class='btn btn-sm btn-color' onclick='show(this.id)' title='Remainder Mail' ><i class='fa fa-clock-o' aria-hidden='true'></i></a>";
            $r_mail .= "<div class='dialog' id='reminderMailForm" . $no . "'>";
            $r_mail .= "<form class='remindermailform'>";
            $r_mail .= "<textarea style='width:200px;height:80px;' id='reminderMailButton" . $no . "' name='reminderMailButton" . $no . "' placeholder='Enter Additional Notes'></textarea>";
            $r_mail .= "<input type='hidden' id='booking_id" . $no . "' name='booking_id" . $no . "' value=$order_list->booking_id >";
            $r_mail .= "<div align='center'>";
            $r_mail .= "<a id='btnOK" . $no . "' class='btn btn-sm btn-success' onclick='send_reminder_email_to_vendor(" . $no . ");'>Ok</a>";
            $r_mail .= "</div>";
            $r_mail .= "</form>";
            $r_mail .= "</div>";
        } else {
            $r_mail = "<a class='btn btn-sm btn-color disabled' href = '" . base_url() . "employee/bookingjobcard/send_reminder_mail_to_vendor/$order_list->booking_id ' title = 'Reminder Mail'><i class='fa fa-clock-o' aria-hidden='true'></i></a>";
        }
        
        if(is_null($order_list->assigned_vendor_id)){
            $d_btn = "disabled";
        }else{
            $d_btn = "";
        }
        
        $b_date = date("Y-m-d", strtotime($order_list->booking_date));
        $date1 = date_create($b_date);
        $date2 = date_create(date("Y-m-d"));
        $diff = date_diff($date2, $date1);
        $b_days = $diff->days;
        if ($diff->invert == 1) {
            $b_days = -$diff->days;
        }
        $b_time = explode("-", $order_list->booking_timeslot);
        $b_timeslot = date("H", strtotime($b_time[0]));
        $esc = "";
        if( $order_list->current_status != "Rescheduled") { 
            if ( $order_list->assigned_vendor_id == null){ 
                $esc =  "disabled"; 
            } 
            else if($b_days >0){ 
                $esc = "disabled";
            } 
            else if($b_days == 0){ 
                if($b_timeslot > date("H")){ 
                    $esc =  "disabled";
                }
            }else{
                $esc = "";
            } 
        }else{
            $esc = "";
        }



        $row[] = $no.$sn;
        $row[] = "<a href='"."https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/jobcards-pdf/".$order_list->booking_jobcard_filename."'>$order_list->booking_id</a>";
        $row[] = "<a class='col-md-12' href='".base_url()."employee/user/finduser?phone_number=".$order_list->phone_number."'>$order_list->customername</a>"."<b>".$order_list->booking_primary_contact_no."</b>";
        $row[] = $order_list->services;
        $row[] = $order_list->booking_date." / ".$order_list->booking_timeslot;
        $row[] = $escalation." ".$order_list->current_status;
        $row[] = "<a target = '_blank' href='".base_url()."employee/vendor/viewvendor/".$order_list->assigned_vendor_id."'>$sf</a>";
        $row[] = $call_btn;
        $row[] = "<a id ='view' class ='btn btn-sm btn-color' href='".base_url()."employee/booking/viewdetails/".$order_list->booking_id."' title = 'view' target = '_blank'><i class = 'fa fa-eye' aria-hidden = 'true'></i></a>";
        $row[] = "<a target = '_blank' id = 'edit' class = 'btn btn-sm btn-color' "
            . "href=" . base_url() . "employee/booking/get_reschedule_booking_form/$order_list->booking_id title='Reschedule'><i class = 'fa fa-calendar' aria-hidden='true' ></i></a>";
        $row[] = "<a target = '_blank' id = 'cancel' class = 'btn btn-sm btn-color' href = '".base_url()."employee/booking/get_cancel_form/".$order_list->booking_id."' title = 'Cancel'><i class = 'fa fa-times' aria-hidden = 'true'></i></a>";
        $row[] = $complete;
        $row[] ="<a target = '_blank' class = 'btn btn-sm btn-color' href = '" . base_url() . "employee/bookingjobcard/prepare_job_card_using_booking_id/$order_list->booking_id' title = 'Job Card'> <i class = 'fa fa-file-pdf-o' aria-hidden = 'true' ></i></a>";
        $row[] = $mail;
        $row[] = $r_mail;
        $row[] = "<a target ='_blank' class = 'btn btn-sm btn-color' href = '" . base_url() . "employee/booking/get_edit_booking_form/$order_list->booking_id' title = 'Edit Booking'> <i class = 'fa fa-pencil-square-o' aria-hidden = 'true'></i></a>";
        $row[] = "<a target ='_blank' class = 'btn btn-sm btn-color' href = '" . base_url() . "employee/vendor/get_reassign_vendor_form/$order_list->booking_id ' title = 'Re-assign' $d_btn> <i class = 'fa fa-repeat' aria-hidden = 'true'></i></a>";
        $row[] = "<a target = '_blank' class = 'btn btn-sm btn-color' href = '".base_url()."employee/vendor/get_vendor_escalation_form/$order_list->booking_id' title = 'Escalate' $esc><i class='fa fa-circle' aria-hidden='true'></i></a>";
        $row[] = $penalty_row;
        
        
        return $row;
    }
    /**
     *  @desc : This function is used to show queries based on query status
     *  @param : $status string
     *  @return : $output JSON
     */
    public function view_queries($status, $p_av,$booking_id=""){
        $data['query_status'] = trim($status);
        $data['pincode_status'] = trim($p_av);
        $data['booking_id'] = trim($booking_id);
        $data['partners'] = $this->partner_model->getpartner_details('partners.id,partners.public_name',array('is_active'=> '1'));
        $data['services'] = $this->booking_model->selectservice();
        $data['cities'] = $this->booking_model->get_advance_search_result_data("booking_details","DISTINCT(city)",NULL,NULL,NULL,array('city'=>'ASC'));
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/view_pending_queries', $data);
    }
    
    /**
     *  @desc : This function is used to get queries based on query status
     *  @param : $status string
     *  @return : $output JSON
     */
    public function get_queries_data($status){
        
        $booking_status = trim($status);
        $pincode_status = trim($this->input->post('pincode_status'));
        $data = $this->get_queries_detailed_data($booking_status,$pincode_status);
        
        $post = $data['post'];
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->booking_model->count_all_queries($post,$pincode_status,$booking_status),
            "recordsFiltered" =>  $this->booking_model->count_filtered_queries($post,$pincode_status,$booking_status),
            "data" => $data['data'],
        );
        
        echo json_encode($output);
    }
    
    public function get_queries_detailed_data($query_status,$pincode_status) {
        $post = $this->get_post_data();
        $new_post = $this->get_filterd_post_data($post, $query_status, "query");
        
        $select = "services.services,users.name as customername, users.phone_number,booking_details.* ,STR_TO_DATE(booking_details.booking_date,'%d-%m-%Y') as booking_day,booking_unit_details.appliance_description";

        $list = $this->booking_model->get_queries($new_post,$pincode_status,$query_status,$select);
        unset($new_post['order_performed_on_count']);
        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row = $this->get_queries_table($order_list, $no, $query_status,$pincode_status);
            $data[] = $row;
        }

        return array(
            'data' => $data,
            'post' => $new_post
        );
    }
    
    private function get_queries_table($order_list, $no, $query_status,$pincode_status){
        $row = array();

        $row[] = $no." <div><input type = 'hidden' id = 'service_id_".$no."' value = '".$order_list->service_id."'><input type = 'hidden' id = 'pincode_".$no."' value = '".$order_list->booking_pincode."'></div>";
        $row[] = $order_list->booking_id;
        $row[] = "<a href='".base_url()."employee/user/finduser?phone_number=$order_list->phone_number'>$order_list->customername / <b>$order_list->phone_number </b></a>";
        $row[] = $order_list->services;
        $row[] = $order_list->booking_date . "/" . $order_list->booking_timeslot;
        if($query_status != _247AROUND_CANCELLED){
            $status = $order_list->current_status;
            if ($order_list->current_status != $order_list->internal_status){
                $status .= "<br> (" . $order_list->internal_status . ") <div>";
            }
            $row[] = $status;
        }
        $row[] = $order_list->city;
        $row[] = $order_list->query_remarks;
        $row[] = $order_list->appliance_description;
        if($query_status != _247AROUND_CANCELLED){
            if($pincode_status == PINCODE_NOT_AVAILABLE){
               $pincode =  "<a href='javascript:void(0)' style='color: red;' onclick='form_submit(".'"'.$order_list->booking_id.'"'.")'>$order_list->booking_pincode</a>";
            }else if($pincode_status == PINCODE_ALL_AVAILABLE || $pincode_status == PINCODE_AVAILABLE ){
                $pincode = "<select id='av_vendor".$no."' style='max-width:100px;'>";
                $pincode .= "<option>Vendor Available</option>";
                $pincode .= "</select>";
                
                $pincode .= "<a id='av_pincode".$no."' href='javascript:void(0)' style='color: red; display:none;' onclick='form_submit(".'"'.$order_list->booking_id.'"'.")'>$order_list->booking_pincode</a>";
            }
            
            $row[] = $pincode;
        }
        $row[] = "<button type='button' class = 'btn btn-sm btn-color' onclick = 'outbound_call($order_list->booking_primary_contact_no)'><i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i></button>";
        $row[] = "<a id ='view' class ='btn btn-sm btn-color' href='".base_url()."employee/booking/viewdetails/".$order_list->booking_id."' title = 'view' target = '_blank'><i class = 'fa fa-eye' aria-hidden = 'true'></i></a>";
        if($query_status != _247AROUND_CANCELLED){
            $row[]  = "<a class = 'btn btn-sm btn-color' href = '" . base_url() . "employee/booking/get_edit_booking_form/$order_list->booking_id' title = 'Update' target ='_blank'><i class = 'fa fa-pencil-square-o' aria-hidden = 'true'></i></a>";
        }
        
        if($query_status == _247AROUND_CANCELLED){
            if($this->session->userdata('user_group') === _247AROUND_CALLCENTER && strtotime($order_list->closed_date) <= strtotime("-1 Months")){
                $row[]  = "<a class = 'btn btn-sm btn-color' href = '" . base_url() . "employee/booking/open_cancelled_query/$order_list->booking_id' title = 'Open' target ='_blank' disabled><i class = 'fa fa-calendar' aria-hidden = 'true'></i></a>";
            }else{
                $row[]  = "<a class = 'btn btn-sm btn-color' href = '" . base_url() . "employee/booking/open_cancelled_query/$order_list->booking_id' title = 'Open' target ='_blank'><i class = 'fa fa-calendar' aria-hidden = 'true'></i></a>";
            }
            
        }else{
            $row[]  = "<a class = 'btn btn-sm btn-color' href = '" . base_url() . "employee/booking/get_cancel_form/$order_list->booking_id/FollowUp' title = 'Cancel' target ='_blank'><i class = 'fa fa-times' aria-hidden = 'true'></i></a>";
        }
        
        
        return $row;
        
    }
    
    /**
     * @desc: This function is used to show editable grid for non verified appliance data from appliance_description_table
     * @param: void
     * @return: void
     * 
     */
    function get_appliance_description_editable_grid(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/appliance_description_editable_grid');
        
    }
    /**
     * @desc: This funtion is called from AJAX to get non verfied appliance details
     * @params: void
     * @return: JSON
     */
    function get_non_verified_appliance_template() {
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $limit = isset($_POST['rows']) ? $_POST['rows'] : 10;
        $sidx = isset($_POST['sidx']) ? $_POST['sidx'] : 'name';
        $sord = isset($_POST['sord']) ? $_POST['sord'] : '';
        $start = $limit * $page - $limit;
        $start = ($start < 0) ? 0 : $start;

        $where['(is_verified = 0 OR is_verified is null)'] = null ;
        $searchField = isset($_POST['searchField']) ? $_POST['searchField'] : false;
        $searchOper = isset($_POST['searchOper']) ? $_POST['searchOper'] : false;
        $searchString = isset($_POST['searchString']) ? $_POST['searchString'] : false;

        if ($_POST['_search'] == 'true') {
            $ops = array(
                'eq' => '=',
                'ne' => '<>',
                'lt' => '<',
                'le' => '<=',
                'gt' => '>',
                'ge' => '>=',
                'bw' => 'LIKE',
                'bn' => 'NOT LIKE',
                'in' => 'LIKE',
                'ni' => 'NOT LIKE',
                'ew' => 'LIKE',
                'en' => 'NOT LIKE',
                'cn' => 'LIKE',
                'nc' => 'NOT LIKE',
                'nu' => 'IS NULL',
                'nn' => 'IS NOT NULL'
            );
            foreach ($ops as $key => $value) {
                if ($searchOper == $key) {
                    $ops = $value;
                }
            }
            if ($searchOper == 'eq'){
                $searchString = $searchString;   
            }
            if ($searchOper == 'bw' || $searchOper == 'bn'){
                $searchString .= '%';
            }   
            if ($searchOper == 'ew' || $searchOper == 'en'){
                $searchString = '%' . $searchString;
            }
            if ($searchOper == 'cn' || $searchOper == 'nc' || $searchOper == 'in' || $searchOper == 'ni'){
                $searchString = '%' . $searchString . '%';
            }
            if ($searchOper == 'nu'){
                $searchString = $searchString;   
            }
            if ($searchOper == 'nn'){
                $searchString = '';   
            }
            
            if($searchOper == 'nu' || $searchOper == 'nn'){
                $where["$searchField $ops"] = NULL;
            }else{
                $where["$searchField $ops '$searchString'"] = NULL;
            }
            
            
        }

        if (!$sidx){
            $sidx = 1;
        }
        $count = $this->reusable_model->get_search_query('appliance_product_description', 'count(id) as count', $where,NULL,NULL,NULL,NULL,NULL)->result()[0]->count;
        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }

        if ($page > $total_pages){
            $page = $total_pages;
        }
       
        $query = $this->reusable_model->get_search_query('appliance_product_description', '*', $where,NULL,array('length' => $limit,'start'=> $start),array($sidx => $sord),NULL,NULL)->result();
        $response = new StdClass;
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $i = 0;
                
        foreach ($query as $row) {
            $response->rows[$i]['id'] = $row->id;
            $response->rows[$i]['cell'] = array($row->product_description, $row->category, $row->capacity,$row->brand);
            $i++;
        }
 
        echo json_encode($response);
    }
    
    /**
     * @desc: This function is called from AJAX to modify non verified appliance details based on different cases
     * @params: void
     * @return: void
     */
    function update_appliance_description_template() {
        $data = $this->input->post();
        $operation = $data['oper'];

        switch ($operation) {
            case 'edit':
                //Initializing array for updating data
                $update_data = [];
                //Setting insert array data
                $update_data['category'] = $data['category'];
                $update_data['capacity'] = $data['capacity'];
                $update_data['brand'] = $data['brand'];
                $update_data['is_verified'] = 1;
                if(!empty($update_data)){
                    $update_id = $this->booking_model->update_appliance_description_details($update_data,array('id'=>$data['id']));
                
                    if ($update_id) {
                        log_message('info', __FUNCTION__ . ' Appliance Details has been updated with ID ' . $update_id);
                    } else {
                        log_message('info', __FUNCTION__ . ' Err in updating Appliance Details');
                    }
                }  
                
                break;
        }
    }
    /**
     * @desc This is used to get Repair- OOW Booking
     */
    function get_oow_booking(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/get_oow_booking');
    }
  
    function booking_bulk_search(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/bulk_booking_search');
    }
    function get_input_for_bulk_search($receieved_Data){
        $inputBulkData = array();
        $inputBulkDataTemp = explode("\n",$receieved_Data['bulk_input']);
        $result = array_map('trim', $inputBulkDataTemp);
        foreach($inputBulkDataTemp as $value){
            $value = str_replace('.', '', $value); // remove dots
            $value = str_replace(' ', '', $value); // remove spaces
            $value = str_replace("\t", '', $value); // remove tabs
            $value = str_replace("\n", '', $value); // remove new lines
            $value = str_replace("\r", '', $value);
            if($value){
                    $inputBulkData[]=$value;
            }
        }
         if($receieved_Data['select_type'] == 'mobile'){
            $fieldName = 'booking_details.booking_primary_contact_no';
            $onlyName = "booking_primary_contact_no";
        }
        else if($receieved_Data['select_type'] == 'order_id'){
            $fieldName = 'booking_details.order_id';
            $onlyName = "order_id";
        }
        else{
            $fieldName = 'booking_details.booking_id';
            $onlyName = "booking_id";
        }
        return array("inputBulkData"=>$inputBulkData,"fieldName"=>$fieldName,"onlyName"=>$onlyName);
    }
    function get_bulk_search_result_data($receieved_Data){
        $finalArray = array();
        $joinDataArray = array("bookings_sources"=>"bookings_sources.partner_id=booking_details.partner_id","service_centres"=>"service_centres.id=booking_details.assigned_vendor_id","services"=>"services.id=booking_details.service_id","booking_unit_details"=>"booking_unit_details.booking_id=booking_details.booking_id");
        // select field to display
        $select = "booking_details.booking_id,booking_details.order_id,booking_details.booking_primary_contact_no,bookings_sources.source,booking_details.city,service_centres.company_name,services.services,booking_unit_details.appliance_brand,booking_unit_details.appliance_category,booking_unit_details.appliance_capacity,booking_unit_details.price_tags,booking_unit_details.product_or_services,booking_details.current_status";
        // limit array for pagination
        $limitArray = array('length'=>$receieved_Data['length'],'start'=>$receieved_Data['start']);
        $inputData = $this->get_input_for_bulk_search($receieved_Data);
        $whereArray = NULL;
        $whereInArray = NULL;
        if($receieved_Data['bulk_input']){
            $whereInArray[$inputData['fieldName']] = $inputData['inputBulkData'];
        }
        $JoinTypeTableArray = array('service_centres'=>'left','bookings_sources'=>'left','booking_unit_details'=>'left','services'=>'left');
       //process query and get result from database
        $result = $this->booking_model->get_advance_search_result_data("booking_details",$select,$whereArray,$joinDataArray,$limitArray,array("booking_details.booking_id"=>"ASC"),$whereInArray,$JoinTypeTableArray);
       //start Logic to create record for all not found entries
        $foundResultArray= array_column($result, $inputData['onlyName'] );
        $notFoundArray=array_diff($inputData['inputBulkData'],$foundResultArray);
        $selectedFields = array_keys($result[0]);
       foreach ($notFoundArray as $notFoundColumn){
           foreach($selectedFields as $fieldName){
                $helperArray[$fieldName] = "Not_found";
               if($fieldName == $inputData['onlyName']){
                   $helperArray[$fieldName] = $notFoundColumn;
               }
           }
           $result[] = $helperArray;
       }
       //End Logic to create record for all not found entries
       
        //convert database result into a required formate needed for datatales
        for($i=0;$i<count($result);$i++){
            $index = $receieved_Data['start']+($i+1);
            $tempArray = array_values($result[$i]);
            array_unshift($tempArray, $index);
            $finalArray[] = $tempArray;
        }
        //create final array required for database table\
        $data['draw'] = $receieved_Data['draw'];
        //get all records from table
        $data['recordsTotal'] = $this->booking_model->get_advance_search_result_count("booking_details",$select,NULL,$joinDataArray,NULL,NULL,NULL,$JoinTypeTableArray);
       // get filtered records from tabble
        $data['recordsFiltered'] = $this->booking_model->get_advance_search_result_count("booking_details",$select,$whereArray,$joinDataArray,NULL,NULL,$whereInArray,$JoinTypeTableArray);
        $data['data'] = $finalArray;
        return $data;
    }
    function get_bulk_search_result_view(){
       $receieved_Data = $this->input->post(); 
       $data = $this->get_bulk_search_result_data($receieved_Data);
       foreach ($data['data'] as $index=>$serachResultData){
            $booking_with_link = "<a href =".base_url() . "employee/booking/viewdetails/".$serachResultData[1]." target='_blank'>".$serachResultData[1]."</a>";
            $data['data'][$index][1] = $booking_with_link;
        }
        echo json_encode($data);
    }
    function download_booking_bulk_search_snapshot(){
       ob_start();
       $receieved_Data = $this->input->post(); 
       $receieved_Data['length'] = -1;
       $receieved_Data['start'] = 0;
       $receieved_Data['draw'] = 1;
       $data = $this->get_bulk_search_result_data($receieved_Data);
       $headings = array("S.no","Booking ID","OrderID","Contact","Partner","City","Service Center","Service","Brand","Category","Capacity","Request Type","Product/Service");
       $this->miscelleneous->downloadCSV($data['data'],$headings,"booking_bulk_search_summary");   
    }
    
    /**
     * @desc: This function is used to get service_id from Ajax call
     * @params: void
     * @return: string
     */
    function get_service_id(){
        $appliance_list = $this->booking_model->selectservice();
        $option = '';

        foreach ($appliance_list as $value) {
            $option .= "<option value='" . $value->id . "'";
            $option .= " > ";
            $option .= $value->services . "</option>";
        }
        $option .= '<option value="all" >All</option>';
        echo $option;
    }
    
    /**
     * @desc: This function is used to download the data from vendor pincode mapping
     * @params: void
     * @return: string
     */
    function download_serviceability_data(){
        $service_id = $this->input->post('service_id');
        $checked_option = $this->input->post('pincode_optradio');
        $excel_file = array();
        $col = "services.services as Appliance,vendor_pincode_mapping.City, vendor_pincode_mapping.State ";
        if($checked_option){
            $col .= ",vendor_pincode_mapping.Pincode";
            $template = '247around_serviceability_details_with_pincode.xlsx';
        }else{
            $template = '247around_serviceability_details_without_pincode.xlsx';
        }
        
        if(in_array('all', $service_id)){
            $service_id = array_column($this->booking_model->selectservice(), 'id');
        }
        
        foreach ($service_id as $key => $value) {
            $where = array('Appliance_ID' => $value);
            $data = $this->vendor_model->get_pincode_mapping_form_col($col, $where);
            if(!empty($data)){
                $excel_file[$key]['service_id'] = $value;
                $excel_file[$key]['file'] = $this->miscelleneous->generate_excel_data($template, $value, $data);
                unset($data);
            }
        }

        $main_excel = $this->combined_excel_sheets($excel_file);
        
        if ($main_excel) {
            
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($main_excel) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($main_excel));
            readfile($main_excel);
            
            foreach ($excel_file as $key => $value) {
                if(file_exists($value['file'])){
                    unlink($value['file']);
                }
            }
            
            $res1 = 0;
            system(" chmod 777 " . $main_excel , $res1);
            if(file_exists($main_excel)){
                unlink($main_excel);
            }
        }
    }
    
    /**
     * @desc: This function is used to combined the excel sheet
     * @params: array $excel_file_list
     * @return: string $combined_excel
     */
    function combined_excel_sheets($excel_file_list) {
        $objPHPExcel1 ="";
        foreach($excel_file_list as $key => $file_path){
            
            switch ($file_path['service_id']){
                case _247AROUND_AC_SERVICE_ID:
                    $sheet_title = 'AC';
                    break;
                case _247AROUND_MICROWAVE_SERVICE_ID:
                    $sheet_title = 'Microwave';
                    break;
                case _247AROUND_REFRIGERATOR_SERVICE_ID:
                    $sheet_title = 'Refrigerator';
                    break;
                case _247AROUND_TV_SERVICE_ID:
                    $sheet_title = 'Television';
                    break;
                case _247AROUND_GEYSER_SERVICE_ID:
                    $sheet_title = 'Geyser';
                    break;
                case _247AROUND_WASHING_MACHINE_SERVICE_ID:
                    $sheet_title = 'Washing Machine';
                    break;
                case _247AROUND_WATER_PURIFIER_SERVICE_ID:
                    $sheet_title = 'Water Purifier';
                    break;
                case _247AROUND_CHIMNEY_SERVICE_ID:
                    $sheet_title = 'Chimney';
                    break;
                case _247AROUND_AUDIO_SYSTEM_SERVICE_ID:
                    $sheet_title = 'Audio System';
                    break;
                default :
                    $sheet_title = $file_path['service_id'];
                    break;
            }
            
            if($key == 0){
                 $objPHPExcel1 = PHPExcel_IOFactory::load($file_path['file']);
                 $objPHPExcel1->getActiveSheet()->setTitle($sheet_title);
            } else {
                $objPHPExcel2 = PHPExcel_IOFactory::load($file_path['file']);
                $objPHPExcel2->getActiveSheet()->setTitle($sheet_title);
                // Copy worksheets from $objPHPExcel2 to $objPHPExcel1
                foreach ($objPHPExcel2->getAllSheets() as $sheet) {
                    $objPHPExcel1->addExternalSheet($sheet);
                }
            }
        }
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel1, "Excel2007");
        $combined_excel = TMP_FOLDER.'serviceability_details.xlsx';
        $objWriter->save($combined_excel);
        return $combined_excel;
    }
    /*
     * This function is used to Cancel Reschedule request  manually (IF anyone found reschedule is fake, not requested by customer)
     */
    function cancel_rescheduled_booking(){
        $userPhone = $this->input->post('p_number');
        $id = $this->input->post('id');
        $employeeID = $this->input->post('employeeID');
        $remarks = $this->input->post('remarks');
        $booking_id = $this->input->post('booking_id');
        echo $this->miscelleneous->fake_reschedule_handling($userPhone,$id,$employeeID,$remarks,$booking_id);
    }
    function get_all_brands($serviceID){
        $data = array();
        $allBrands = $this->reusable_model->get_search_result_data("appliance_brands","DISTINCT(brand_name)",array("service_id"=>$serviceID),NULL,NULL,array("brand_name"=>"ASC"),NULL,NULL,array());
        if($allBrands){
            foreach($allBrands as $brand){
                $data[]=$brand['brand_name'];
            }
        }
        echo json_encode($data);
    }
}