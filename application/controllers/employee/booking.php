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
	$this->load->model('vendor_model');
	$this->load->model('invoices_model');
	$this->load->model('service_centers_model');
	$this->load->model('partner_model');
	$this->load->model('inventory_model');
        $this->load->model('upcountry_model');
        $this->load->model('penalty_model');
	$this->load->library('partner_sd_cb');
	$this->load->library('partner_cb');
	$this->load->library('notify');
	$this->load->helper(array('form', 'url'));
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
    public function index($user_id) {
        if($this->input->post()){
        $primary_contact_no = $this->input->post('booking_primary_contact_no');
        //Check Validation
        $checkValidation  = $this->validate_booking();
            
        if($checkValidation){
        log_message('info', __FUNCTION__);
        log_message('info', " Booking Insert User ID: " . $user_id);
        $this->getAllBookingInput($user_id,INSERT_NEW_BOOKING);

        //Redirect to Default Search Page
        redirect(base_url() . DEFAULT_SEARCH_PAGE);
        
        }else{  
               //Redirect to edit booking page if validation err occurs
                if(!empty($primary_contact_no)){
                $this->addbooking($primary_contact_no);
                }else{
                    //Redirect to Default Search Page if Primary Phone number not found in Post
                    redirect(base_url() . DEFAULT_SEARCH_PAGE);
                }
        }
        }else{
                //Logging error message if No input is provided
                log_message('info', __FUNCTION__." Error in Booking Insert User ID: " . $user_id);
                $heading = "247Around Booking Error";
                $message = "Oops... No input provided !";
                $error =& load_class('Exceptions', 'core');
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
        log_message('info', " Booking Insert User ID: " . $user_id . " Booking ID" . $booking_id." Done By " . $this->session->userdata('employee_id'));

        $user['user_id'] = $booking['user_id'] = $user_id;
        $price_tags = array();

        // All brand comming in array eg-- array([0]=> LG, [1]=> BPL)
        $appliance_brand = $this->input->post('appliance_brand');
        $upcountry_data_json = $this->input->post('upcountry_data');
        $upcountry_data = json_decode($upcountry_data_json, TRUE);

        $booking = $this->insert_data_in_booking_details($booking_id, $user_id, count($appliance_brand));
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

            $serial_number = $this->input->post('serial_number');

            $partner_net_payable = $this->input->post('partner_paid_basic_charges');
            $appliance_description = $this->input->post('appliance_description');

            // All discount comming in array.  Array ( [BPL] => Array ( [100] => Array ( [0] => 200 ) [102] => Array ( [0] => 100 ) [103] => Array ( [0] => 0 ) ) .. Key is Appliance brand, unit id and discount value.
            $discount = $this->input->post('discount');
            // All prices comming in array with pricing table id
            /* Array([BPL] => Array([0] => 100_300 [1] => 102_250) [Micromax] => Array([0] => 100_300)) */
            //Array ( ['brand'] => Array ( [0] => id_price ) )
            $pricesWithId = $this->input->post("prices");
            $user['user_email'] = $this->input->post('user_email');
            $result =array();
            $result['DEFAULT_TAX_RATE'] = 0;

            foreach ($appliance_brand as $key => $value) {

                $services_details = "";
                $appliances_details = "";
                $appliances_details['user_id'] = $user_id;

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
                $appliances_details['serial_number'] = $services_details['serial_number'] = $serial_number[$key];
                $appliances_details['description'] = $services_details['appliance_description'] = $appliance_description[$key];
                // get purchase months from months array for only specific key such as $months[0].
                $appliances_details['purchase_month'] = $services_details['purchase_month'] = $months[$key];
                $appliances_details['service_id'] = $services_details['service_id'] = $booking['service_id'];
                $appliances_details['last_service_date'] = date('Y-m-d H:i:s');

                $services_details['partner_id'] = $booking['partner_id'];

                log_message('info', __METHOD__ . "Appliance ID" . print_r($appliance_id, true));
                /* if appliance id exist the initialize appliance id in array and update appliance details other wise it insert appliance details and return appliance id
                 * */
                $check_product_type = $this->booking_model->get_service_id_by_appliance_details(trim($appliances_details['description']));
                if(!$check_product_type){
                    $insert_data =array('service_id' => $appliances_details['service_id'],
                                 'category' =>$appliances_details['category'],
                                 'capacity'=>$appliances_details['capacity'],
                                 'brand'=>$appliances_details['brand'],
                                 'product_description'=>trim($appliances_details['description']));
                    $insert_data_id = $this->booking_model->insert_appliance_details($insert_data);
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
                foreach ($pricesWithId[$brand_id] as $values) {

                    $prices = explode("_", $values);  // split string..
                    $services_details['id'] = $prices[0]; // This is id of service_centre_charges table.
                    // discount for appliances. Array ( [BPL] => Array ( [100] => Array ( [0] => 200 ) [102] => Array ( [0] => 100 ) [103] => Array ( [0] => 0 ) )
                    $services_details['around_paid_basic_charges'] = $discount[$brand_id][$services_details['id']][0];
                    $services_details['partner_paid_basic_charges'] = $partner_net_payable[$brand_id][$services_details['id']][0];
                    $services_details['partner_net_payable'] = $services_details['partner_paid_basic_charges'];
                    $services_details['around_net_payable'] = $services_details['around_paid_basic_charges'];
                    $services_details['booking_status'] = $booking['current_status'];
                    log_message('info', __METHOD__ . " Before booking is insert/update: " . $booking_id);
                    
                    switch ($booking_id){
                        case INSERT_NEW_BOOKING:
                            log_message('info', __METHOD__ . " Insert Booking Unit Details: " );
                            $result = $this->booking_model->insert_data_in_booking_unit_details($services_details, $booking['state']);
                            break;
                        default:
                            
                            log_message('info', __METHOD__ . " Update Booking Unit Details: " . " Previous booking id: " . $booking_id);
                            $result = $this->booking_model->update_booking_in_booking_details($services_details, $booking_id, $booking['state']);

                            array_push($price_tags, $result['price_tags']);
                            break;
                    }
                }
            }
            if (!empty($price_tags)) {
                log_message('info', __METHOD__ . " Price Tags: " . print_r($price_tags, true));
                $this->booking_model->check_price_tags_status($booking['booking_id'], $price_tags);
            }

            $this->user_model->edit_user($user);

            if ($booking['type'] == 'Booking') {

                if ($result['DEFAULT_TAX_RATE'] == 1) {
                    log_message('info', __METHOD__ . " Default_tax_rate: " . $result['DEFAULT_TAX_RATE']);
                    $this->send_sms_email($booking['booking_id'], "Default_tax_rate");
                }

                if (empty($booking['state'])) {
                    log_message('info', __FUNCTION__ . " Pincode Not Found Booking Id: " . $booking['booking_pincode']);
                    $this->send_sms_email($booking_id, "Pincode_not_found");
                }
                
                if ($booking['is_send_sms'] == 1) {
                    //Query converted to Booking OR New Booking Inserted
                    $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
                    $send['booking_id'] = $booking['booking_id'];
                    $send['state'] = "Newbooking";
                    $this->asynchronous_lib->do_background_process($url, $send);
                    
                    //Assign Vendor
                    //log_message("info"," upcountry_data", print_r($upcountry_data). " Booking id ". $booking['booking_id']);
                    switch ($upcountry_data['message']){
                        case UPCOUNTRY_BOOKING:
                        case UPCOUNTRY_LIMIT_EXCEED:
                        case NOT_UPCOUNTRY_BOOKING:
                        case UPCOUNTRY_DISTANCE_CAN_NOT_CALCULATE:
                            $url = base_url() . "employee/vendor/process_assign_booking_form/";
                            $async_data['service_center'] = array($booking['booking_id'] => $upcountry_data['vendor_id']);
                            $async_data['agent_id'] = _247AROUND_DEFAULT_AGENT;
                            $async_data['agent_name'] = _247AROUND_DEFAULT_AGENT_NAME;
                            $this->asynchronous_lib->do_background_process($url, $async_data);
                            
                            break;
                        case SF_DOES_NOT_EXIST:
                            break;
                    }
                } else if($booking['is_send_sms'] == 2 || $booking_id != INSERT_NEW_BOOKING) {
                    //Pending booking getting updated
                    $url = base_url() . "employee/vendor/check_unit_exist_in_sc/".$booking['booking_id'];
                    $async_data['booking'] = array();
                    $this->asynchronous_lib->do_background_process($url, $async_data);
                    
                }
            }  

            return $booking;
        } else {
            echo "Booking Insert/Update Failed";
            
            log_message('info', __FUNCTION__. " Booking Failed!");
            
            exit();
        }
    }

    /**
     * @desc: This method get input file dand insert booking details
     * @param String $booking_id
     * @param String $user_id
     * @param String $quantity
     * @return boolean
     */
    function insert_data_in_booking_details($booking_id, $user_id, $quantity){
        $booking = $this->get_booking_input();
       
        $remarks = $this->input->post('query_remarks');
        $partner_id = $this->partner_model->get_all_partner_source("", $booking['source']);
        $booking['partner_id'] = $partner_id[0]['partner_id'];
        $booking['quantity'] = $quantity;
        $booking['user_id'] = $user_id;

        switch ($booking_id) {
            case INSERT_NEW_BOOKING:
                $booking['booking_id'] = $this->create_booking_id($user_id, $booking['source'], $booking['type'], $booking['booking_date']);
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
        $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'],$booking['partner_id'], $booking_id);
        if(!empty($partner_status)){
            $booking['partner_current_status'] = $partner_status[0];
            $booking['partner_internal_status'] = $partner_status[1];
        }

        switch ($booking_id) {

            case INSERT_NEW_BOOKING:
                
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
        
        $this->notify->insert_state_change($booking['booking_id'], $new_state,
                $old_state , $remarks , 
                $this->session->userdata('id'), 
                $this->session->userdata('employee_id'),
                _247AROUND);
        
        return $booking;
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
    function get_booking_input() {
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
//	$booking['potential_value'] = $this->input->post('potential_value');
        $booking['booking_alternate_contact_no'] = $this->input->post('booking_alternate_contact_no');
        $booking['booking_timeslot'] = $this->input->post('booking_timeslot');
        $booking['update_date'] = date("Y-m-d H:i:s");
        

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
        
	log_message('info', __FUNCTION__ . " Booking ID: " . $booking_id);
        
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

                    //Since booking has been converted to query, delete this entry from
                    //service center booking action table as well.
                    log_message('info', __FUNCTION__ . " Request to delete booking from service center action table Booking ID" . $data['booking_id']);
                    $this->service_centers_model->delete_booking_id($booking_id);

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
        $data['user'] =  $this->user_model->search_user($phone_number);
        $where_internal_status = array("page" => "FollowUp", "active" => '1');
	$data['follow_up_internal_status'] = $this->booking_model->get_internal_status($where_internal_status);
	$this->load->view('employee/header/'.$this->session->userdata('user_group'));
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
   
	$config['base_url'] = base_url() . 'employee/booking/view/'.$page;
	$config['total_rows'] = $this->booking_model->total_pending_booking($booking_id);
	
	if($offset != "All"){
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
        
	if ($this->session->flashdata('result') != ''){
	    $data['success'] = $this->session->flashdata('result');
        }

	$this->load->view('employee/header/'.$this->session->userdata('user_group'));
	$this->load->view('employee/booking', $data);
    }

    /**
     *  @desc : This function displays list of completed and Cancelled bookings according to pagination. If $status is Completed, it gets Completed booking and if $status is Cancelled, it gets Cancelled booking
     *
     * This method will show only that number of bookings which are being selected from the pagination section(50/100/200/All).
     *
     *  @param : Starting page & number of results per page
     *  @return : completed bookings according to pagination
     */
    function viewclosedbooking($status, $page = 0, $offset = 0, $booking_id = "") {
	if ($page == '0') {
	    $page = 50;
	}

	//$offset = ($this->uri->segment(5) != '' ? $this->uri->segment(5) : 0);
	$config['base_url'] = base_url() . 'employee/booking/viewclosedbooking/' . $status."/".$page;
	$config['total_rows'] = $this->booking_model->total_closed_booking($status, $booking_id);
	$config['per_page'] = $page;
	$config['uri_segment'] = 6;
	$config['first_link'] = 'First';
	$config['last_link'] = 'Last';

	$this->pagination->initialize($config);
	$data['links'] = $this->pagination->create_links();
	$data['Bookings'] = $this->booking_model->view_completed_or_cancelled_booking($config['per_page'], $offset, $status, $booking_id);
	$this->load->view('employee/header/'.$this->session->userdata('user_group'));

	$this->load->view('employee/viewcompletedbooking', $data);
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
	$source = $this->partner_model->get_all_partner_source("0", $data['booking_history'][0]['source']);
	$data['booking_history'][0]['source_name'] = $source[0]['source'];

	$partner_id = $this->booking_model->get_price_mapping_partner_code($data['booking_history'][0]['source']);
	$data['prices'] = array();
	//log_message('info', __FUNCTION__ . " data " . print_r($data, true));
	foreach ($data['booking_unit_details'] as $keys => $value) {
            if($source[0]['partner_type'] == OEM){
	        $prices = $this->booking_model->getPricesForCategoryCapacity($data['booking_history'][0]['service_id'], 
                    $data['booking_unit_details'][$keys]['category'],
                    $data['booking_unit_details'][$keys]['capacity'], $partner_id,$value['brand']);
            } else {
                $prices = $this->booking_model->getPricesForCategoryCapacity($data['booking_history'][0]['service_id'], 
                    $data['booking_unit_details'][$keys]['category'],
                    $data['booking_unit_details'][$keys]['capacity'], $partner_id, "");
            }
            $upcountry_price = 0;
	    //log_message('info', __FUNCTION__ . " Prices " . print_r($prices, true));
	    foreach ($value['quantity'] as $key => $price_tag) {
		$service_center_data = $this->service_centers_model->get_prices_filled_by_service_center($price_tag['unit_id'], $booking_id);

		$result = $this->partner_model->getPrices($data['booking_history'][0]['service_id'], $value['category'], $value['capacity'], $partner_id, $price_tag['price_tags']);

            $data['booking_unit_details'][$keys]['quantity'][$key]['pod'] = isset($result[0]['pod'])?$result[0]['pod']:"";


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
                if($keys == 0){
                    $upcountry_price = isset($service_center_data[0]['upcountry_charges'])?$service_center_data[0]['upcountry_charges']:"";
                }
	    }

	    array_push($data['prices'], $prices);
	}

        $data['upcountry_charges'] = $upcountry_price;

	$this->load->view('employee/header/'.$this->session->userdata('user_group'));
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
	if ($status == _247AROUND_FOLLOWUP ) {
            $where_internal_status = array("page" => "Cancel", "active" => '1');
	    $data['internal_status'] = $this->booking_model->get_internal_status($where_internal_status);
	}

	$this->load->view('employee/header/'.$this->session->userdata('user_group'));
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
    function process_cancel_form($booking_id, $status, $agent_id= false, $agent_name= false) {
	log_message('info', __FUNCTION__ . " Booking ID: " . $booking_id." Done By " . $this->session->userdata('employee_id'));

        $this->form_validation->set_rules('cancellation_reason', 'Cancellation Reason', 'required|xss_clean');
        $this->form_validation->set_rules('partner_id', 'Partner Id', 'required|xss_clean');
        $validation = $this->form_validation->run();
        if($validation){


            if(!$agent_id){
                $agent_id = $this->session->userdata('id');
                $agent_name = $this->session->userdata('employee_id');
            }
            $partner_id =$this->input->post('partner_id');
            $cancellation_reason = $this->input->post('cancellation_reason');
            $cancellation_text = $this->input->post("cancellation_reason_text");
            
            $this->miscelleneous->process_cancel_form($booking_id, $status,$cancellation_reason, 
                    $cancellation_text,$agent_id, $agent_name,$partner_id);

            redirect(base_url() . DEFAULT_SEARCH_PAGE);
        } else {
            log_message('info', __FUNCTION__ . " Validation Failed Booking ID: " . $booking_id." Done By " . $this->session->userdata('employee_id'));
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

	    $this->load->view('employee/header/'.$this->session->userdata('user_group'));
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
        $partner_id=$this->input->post('partner_id');
        $partner_status = $this->booking_utilities->get_partner_status_mapping_data($data['current_status'], $data['internal_status'],$partner_id, $booking_id);
            if(!empty($partner_status)){
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
	    $this->notify->insert_state_change($booking_id, _247AROUND_RESCHEDULED , _247AROUND_PENDING , _247AROUND_RESCHEDULED, $this->session->userdata('id'), $this->session->userdata('employee_id'),_247AROUND);

	    $service_center_data['internal_status'] = "Pending";
	    $service_center_data['current_status'] = "Pending";
	    $service_center_data['update_date'] = date("Y-m-d H:i:s");
            
	    
            log_message('info', __FUNCTION__ . " Booking Id ".$booking_id." Update Service center action table  " . print_r($service_center_data, true));
	    
            $this->vendor_model->update_service_center_action($booking_id, $service_center_data);

	    $send_data['booking_id'] = $booking_id;
	    $send_data['current_status'] = "Rescheduled";
	    $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
	    $this->asynchronous_lib->do_background_process($url, $send_data);	    
           
	    log_message('info', __FUNCTION__ . " Request to prepare Job Card  " . print_r($booking_id, true));
	    
            $job_card = array();
	    $job_card_url = base_url() . "employee/bookingjobcard/prepare_job_card_using_booking_id/".$booking_id;
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
        
        if($booking_source[0]['partner_type'] == OEM){
            $where = array("partner_appliance_details.service_id" =>$service_id,
            'partner_id'=> $booking_source[0]['partner_id']);
            $select = 'brand As brand_name';

            $result = $this->partner_model->get_partner_specific_details($where, $select,"brand");
        } else {
            $result = $this->booking_model->getBrandForService($service_id);
        } 
        
        
        $data['partner_type'] =  $booking_source[0]['partner_type'];
	$data['brand'] = "<option selected disabled> Select Brand</option>";
	foreach ($result as $brand) {
	    $data['brand'] .= "<option>$brand[brand_name]</option>";
	}
        
        print_r(json_encode($data, true));
    }
    
    /**
     * @desc: This is used to get appliabce list its called by Ajax
     */
    function get_appliances($selected_service_id){
        $source_code = $this->input->post('source_code');
       
        $booking_source = $this->booking_model->get_booking_source($source_code);
          if($booking_source[0]['partner_type'] == OEM){
               $services = $this->partner_model->get_partner_specific_services($booking_source[0]['partner_id']);
              
          } else {
              $services = $this->booking_model->selectservice();
              
          }
          $data['partner_type'] =  $booking_source[0]['partner_type'];
          $data['services'] = "<option selected disabled>Select Service</option>";
          foreach ($services as $appliance) {
            $data['services'] .= "<option ";
            if($selected_service_id == $appliance->id){
                $data['services'] .= " selected ";
            } else if(count($services) ==1){
                $data['services'] .= " selected ";
            }
            $data['services']  .=" value='".$appliance->id."'>$appliance->services</option>";
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
	$partner = $this->input->post('partner_code');
        $partner_type = $this->input->post('partner_type');

	$partner_id = $this->booking_model->get_price_mapping_partner_code($partner);
        if($partner_type == OEM){
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
	$parter_code = $this->input->post('partner_code');
        $partner_type = $this->input->post('partner_type');

	$partner_price_mapping_id = $this->booking_model->get_price_mapping_partner_code($parter_code);
        if($partner_type == OEM){
            $result = $this->booking_model->getCapacityForCategory($service_id, $category, $brand, $partner_price_mapping_id);
            
        } else {
            $result = $this->booking_model->getCapacityForCategory($service_id, $category, "", $partner_price_mapping_id);
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
	$partner_code = $this->input->post('partner_code');
        $partner_type = $this->input->post('partner_type');
	$clone_number = $this->input->post('clone_number');
	
        $where_get_partner = array('bookings_sources.code'=>$partner_code);
        $select = "bookings_sources.partner_id,bookings_sources.price_mapping_id, "
                . " partners.upcountry_approval, upcountry_mid_distance_threshold,"
                . " upcountry_min_distance_threshold, upcountry_max_distance_threshold, "
                . " upcountry_rate1, upcountry_rate, partners.is_upcountry";
        $partner_data = $this->partner_model->getpartner_details($select,$where_get_partner);
        $partner_mapping_id = $partner_data[0]['price_mapping_id'];
       
        if($partner_type == OEM){
	   $result = $this->booking_model->getPricesForCategoryCapacity($service_id, $category, $capacity, $partner_mapping_id, $brand);
        } else {
             $result = $this->booking_model->getPricesForCategoryCapacity($service_id, $category, $capacity, $partner_mapping_id, "");
        }
	
        $where  = array('service_id' => $service_id,'brand_name' => $brand);
        $brand_id_array  = $this->booking_model->get_brand($where);

        if(!empty($brand_id_array)){
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
		$html .= "<td><input  type='text' class='form-control partner_discount' name= 'partner_paid_basic_charges[$brand_id][" . $prices['id'] . "][]'  id='partner_paid_basic_charges_" . $i . "_" . $clone_number . "' value = '" . $prices['partner_net_payable'] . "' placeholder='Enter discount' readonly/></td>";
		$html .= "<td>" . $prices['customer_net_payable'] . "</td>";
		$html .= "<td><input  type='text' class='form-control discount' name= 'discount[$brand_id][" . $prices['id'] . "][]'  id='discount_" . $i . "_" . $clone_number . "' value = '0' placeholder='Enter discount' readonly></td>";
		$html .= "<td><input type='hidden'name ='is_up_val' id='is_up_val_" . $i . "_" . $clone_number . "' value ='".$prices['is_upcountry']."' /><input class='price_checkbox'";
//		if ($prices['service_category'] == 'Repair') {
//		    $html .= "checked";
//		}

		$html .=" type='checkbox' id='checkbox_" . $i . "_" . $clone_number . "'";
		$html .= "name='prices[$brand_id][]'";
		$html .= "  onclick='final_price(), enable_discount(this.id), set_upcountry()'" .
		    "value=" . $prices['id'] . "_" . intval($prices['customer_total'])."_".$i ."_".$clone_number. " ></td><tr>";

		$i++;
	    }
	    $data['price_table'] = $html;
            $upcountry_data = $this->miscelleneous->check_upcountry_vendor_availability($booking_city, $booking_pincode, $service_id, $partner_data, FALSE);
            
            
            $data['upcountry_data'] = json_encode($upcountry_data,true);
            print_r(json_encode($data,true));
            
            
	} else {
            $data['html']= "Price Table Not Found";
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
	    
	    $this->load->view('employee/header/'.$this->session->userdata('user_group'));
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
        log_message('info', __FUNCTION__.' Booking ID : '.$booking_id.' Status'. $status." Done By " . $this->session->userdata('employee_id'));
	if ($this->input->post('rating_star') != "Select") {
	    $data['rating_stars'] = $this->input->post('rating_star');
	    $data['rating_comments'] = $this->input->post('rating_comments');

	    $this->booking_model->update_booking($booking_id, $data);
           
            $this->notify->insert_state_change($booking_id, 
                    "Rating: ".$data['rating_stars'], "Rating", 
                    $data['rating_comments'] , 
                    $this->session->userdata('id'), $this->session->userdata('employee_id'),
                    _247AROUND);
	}

	redirect(base_url() . 'employee/booking/viewclosedbooking/' . $status);
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
	$this->load->view('employee/header/'.$this->session->userdata('user_group'));
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
	$data['booking_history'] = $this->booking_model->getbooking_history($booking_id);
        $unit_where = array('booking_id'=>$booking_id);
	$data['unit_details'] = $this->booking_model->get_unit_details($unit_where);

	$data['service_center'] = $this->booking_model->selectservicecentre($booking_id);
        $data['penalty'] = $this->penalty_model->get_penalty_on_booking_by_booking_id($booking_id);
        foreach($data['penalty'] as $key=> $value){
            if($value['active'] == 0){
                $where=array('id'=> $value['penalty_remove_agent_id']);
                $data1 = $this->employee_model->get_employee_by_group($where);
                $data['penalty'][$key]['agent_name'] = $data1[0]['full_name'];
            }else if($value['active'] == 1){
                $where=array('id'=> $value['agent_id']);
                $data1 = $this->employee_model->get_employee_by_group($where);
                $data['penalty'][$key]['agent_name'] = $data1[0]['full_name'];
            }
        }
        if(!is_null($data['booking_history'][0]['sub_vendor_id'])){
            $data['dhq'] = $this->upcountry_model->get_sub_service_center_details(array('id' =>$data['booking_history'][0]['sub_vendor_id']));
        }


	$this->load->view('employee/header/'.$this->session->userdata('user_group'));
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

	$this->load->view('employee/header/'.$this->session->userdata('user_group'));
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
	    if ($service_id != "Select" ) {
                if(!empty($brand_details[$key])){
                    
                    $is_exits = $this->booking_model->check_brand_exists($service_id, trim($brand_details[$key]));
                    if(!$is_exits){
                        $service_name = $this->booking_model->selectservicebyid($service_id);
                        $is_insert = $this->booking_model->addNewApplianceBrand($service_id, trim($brand_details[$key]));
                        array_push($data, array("service_id"=> $service_name[0]['services'], "brand_name"=>trim($brand_details[$key])));

                    } 
                }
                
	    }
	}
        if(!empty($data))
        {
            $to = ANUJ_EMAIL_ID;
            $cc = "";
            $bcc = "";
            $subject = "New Brand Added By ".$this->session->userdata('employee_id');
            $message = "
        <html>
        <head></head>
        <body>
            <h3>New Brands added By  ".$this->session->userdata('employee_id')."</h3>    
            <table style='border-collapse:collapse; border: 1px solid black;'> 
                <thead>
                    <tr style='border-collapse:collapse; border: 1px solid black;'>
                        <th>Services</th>
                        <th>Brand Name</th>
                    </tr>    
                </thead>
                <tbody>";
                    foreach($data as $val) { 
                        $message .="<tr>
                            <td style='border-collapse:collapse; border: 1px solid black;'>" . $val['service_id']."</td>
                            <td style='border-collapse:collapse; border: 1px solid black;'>".$val['brand_name']."</td>
                        </tr>";
                     } 
                $message .= "</tbody>
            </table>
            <hr />     
        </body>
        </html>";
            $this->notify->sendEmail("booking@247around.com", $to, $cc, $bcc, $subject, $message, "");
        }

	redirect(base_url() . 'employee/booking/get_add_new_brand_form', 'refresh');
    }

    /**
     *  @desc : This function is to view pending queries according to pagination
     *  @param : offset and per page number
     *  @return : list of pending queries according to pagination
     */
    function view_queries($status, $p_av, $page = 0, $offset = '0', $booking_id = "") {
	if ($page == 0) {
	    $page = 50;
	}

	//$offset = ($this->uri->segment(7) != '' ? $this->uri->segment(7) : 0);
	$config['base_url'] = base_url() . 'employee/booking/view_queries/' . $status."/".$p_av."/".$page;
        
        //Get count of all pending queries
	$total_queries = $this->booking_model->get_queries(0, "All", $status, $p_av, $booking_id);
        
	$config['total_rows'] = $total_queries[0]->count;
	if($offset == "All"){
		$config['per_page'] = $config['total_rows'];

	} else {
		$config['per_page'] = $page;  
	}
	
	$config['uri_segment'] = 7;
	$config['first_link'] = 'First';
	$config['last_link'] = 'Last';

	$this->pagination->initialize($config);
	$data['links'] = $this->pagination->create_links();
        
        //Get actual data for all pending queries now
	$data['Bookings'] = $this->booking_model->get_queries($config['per_page'], $offset, $status, $p_av, $booking_id);

        $data['p_av'] = $p_av;

	$this->load->view('employee/header/'.$this->session->userdata('user_group'));
	$this->load->view('employee/viewpendingqueries', $data);
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
        if(!empty($booking_history)){
	$booking = $this->booking_model->get_city_source();
	$booking['booking_history'] = $booking_history;
	$booking['unit_details'] = $this->booking_model->getunit_details($booking_id, $appliance_id);
	$partner_id = $this->booking_model->get_price_mapping_partner_code($booking_history[0]['source']);
        $booking['partner_type'] = "";
        
        foreach ($booking['sources'] as $value) {
            if($value['partner_id'] == $booking_history[0]['partner_id']){
                $booking['partner_type'] = $value['partner_type'];
            }
        }
        if($booking['partner_type'] == OEM){
             $booking['services'] = $this->partner_model->get_partner_specific_services($booking_history[0]['partner_id']);
        } else {
            $booking['services'] =  $this->booking_model->selectservice();
        }


	$booking['capacity'] = array();
        $booking['category'] = array();
        $booking['brand'] = array();
        $booking['prices'] = array();
	$booking['appliance_id'] = $appliance_id;
        $where_internal_status = array("page" => "FollowUp", "active" => '1');
	$booking['follow_up_internal_status'] = $this->booking_model->get_internal_status($where_internal_status);

	foreach ($booking['unit_details'] as $key => $value) {
            if( $booking['partner_type'] == OEM){
                $where = array("partner_appliance_details.service_id" =>$booking_history[0]['service_id'],
            'partner_id'=> $booking_history[0]['partner_id']);
                $select = 'brand As brand_name';
            
                $brand = $this->partner_model->get_partner_specific_details($where, $select, "brand");
                $category = $this->booking_model->getCategoryForService($booking_history[0]['service_id'], 
                        $partner_id, $value['brand']);
                
                $capacity = $this->booking_model->getCapacityForCategory($booking_history[0]['service_id'],
                        $value['category'], $value['brand'], $partner_id);
                
                $prices = $this->booking_model->getPricesForCategoryCapacity($booking_history[0]['service_id'], 
                        $value['category'], $value['capacity'], $partner_id, $value['brand']);
                
            } else {
                $brand = $this->booking_model->getBrandForService($booking_history[0]['service_id']);
                $category = $this->booking_model->getCategoryForService($booking_history[0]['service_id'],$partner_id,"");
                $capacity = $this->booking_model->getCapacityForCategory($booking_history[0]['service_id'], $value['category'],"", $partner_id);
                $prices = $this->booking_model->getPricesForCategoryCapacity($booking_history[0]['service_id'], 
                        $value['category'], $value['capacity'], $partner_id, "");

            }
	    

	   
        $where  = array('service_id' => $booking_history[0]['service_id'],'brand_name' => $value['brand']);
        $brand_id_array  = $this->booking_model->get_brand($where);
        if(!empty($brand_id_array)){

    	 $booking['unit_details'][$key]['brand_id'] = $brand_id_array[0]['id'];
        } else {
    	$booking['unit_details'][$key]['brand_id'] = "";
        }

	    array_push($booking['category'], $category);
	    array_push($booking['brand'], $brand);
            array_push($booking['capacity'], $capacity);
	    array_push($booking['prices'], $prices);
	}
       
	$this->load->view('employee/header/'.$this->session->userdata('user_group'));
	$this->load->view('employee/update_booking', $booking);
        } else {
           echo  "Booking Id Not Exist";
        }
    }

    /**
     * @desc: This function is used to update both Bookings and Queries.
     */
    function update_booking($user_id, $booking_id) {
        $bookings = $this->booking_model->getbooking_history($booking_id);
        if(!empty($bookings)){
        if ($this->input->post()) {
            $checkValidation = $this->validate_booking();

            if ($checkValidation) {
                log_message('info', __FUNCTION__ . " Booking ID  " . $booking_id . " User ID: " . $user_id);

                $this->getAllBookingInput($user_id, $booking_id);
                
                log_message('info', __FUNCTION__ . " Partner callback  " . $booking_id);
                $this->partner_cb->partner_callback($booking_id);

                //Redirect to Default Search Page
                redirect(base_url() . DEFAULT_SEARCH_PAGE);
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
     *  @desc : This function is used to rebook cancel query
     *  @param : String (Booking Id)
     *  @param : String(Phone Number)
     *  @return : refirect user controller
     */
    function cancelled_booking_re_book($booking_id, $phone) {
         $status = array("current_status" => "FollowUp",
            "internal_status" => "FollowUp",
            "cancellation_reason" => NULL,
            "closed_date" => NULL);
         
        $partner_id_data = $this->partner_model->get_order_id_by_booking_id($booking_id);
        $partner_id=$partner_id_data['partner_id'];
        if($partner_id){
            $partner_status = $this->booking_utilities->get_partner_status_mapping_data($status['current_status'], $status['internal_status'],$partner_id, $booking_id);
            if(!empty($partner_status)){
                $status['partner_current_status'] = $partner_status[0];
                $status['partner_internal_status'] = $partner_status[1];
            }
        }
	$this->booking_model->change_booking_status($booking_id,$status);
	redirect(base_url() . 'employee/user/finduser/0/0/' . $phone, 'refresh');
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
	log_message('info', __FUNCTION__ ." Booking_id ".$booking_id. " Update service center action table: " . print_r($data, true));
	$this->vendor_model->update_service_center_action($booking_id, $data);
        
         $this->notify->insert_state_change($booking_id, 
                    "Rejected" , "InProcess_Completed" , 
                    $admin_remarks , 
                    $this->session->userdata('id'), $this->session->userdata('employee_id'),
                    _247AROUND);
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
        if(!empty($approved_booking)){
            foreach ($approved_booking as $booking_id) {
                $data = array();
                $data['booking_id'] = $booking_id;
                $data['agent_id'] = $agent_id;
                $data['agent_name'] = $agent_name;
                $data['partner_id'] = $partner_id;
                log_message('info', __FUNCTION__ . " Approved Booking: " . print_r($data, true));
                $this->asynchronous_lib->do_background_process($url, $data);
            }
        }else{
            //Logging
            log_message('info',__FUNCTION__.' Approved Booking Empty from Post');
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
	log_message('info', __FUNCTION__ . " Booking ID: " . print_r($booking_id, true));
	$data['charges'] = $this->booking_model->get_booking_for_review($booking_id);
	$data['data'] = $this->booking_model->review_reschedule_bookings_request();

	$this->load->view('employee/header/'.$this->session->userdata('user_group'));
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
	//$reschedule_booking_timeslot = $this->input->post('reschedule_booking_timeslot');
	$reschedule_reason = $this->input->post('reschedule_reason');

	foreach ($reschedule_booking_id as $booking_id) {
	    $booking['booking_date'] = date('d-m-Y', strtotime($reschedule_booking_date[$booking_id]));
	    //$booking['booking_timeslot'] = $reschedule_booking_timeslot[$booking_id];
	    $send['state'] = $booking['current_status'] = 'Rescheduled';
	    $booking['internal_status'] = 'Rescheduled';
            
	    $booking['update_date'] = date("Y-m-d H:i:s");
	    $send['booking_id']  = $booking_id;
	    $booking['reschedule_reason'] = $reschedule_reason[$booking_id];
            
            //check partner status from partner_booking_status_mapping table  
            $partner_id=$this->input->post('partner_id');
                $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'],$partner_id, $booking_id);
                if(!empty($partner_status)){
                    $booking['partner_current_status'] = $partner_status[0];
                    $booking['partner_internal_status'] = $partner_status[1];
                }
            log_message('info', __FUNCTION__ . " update booking: " . print_r($booking, true));
	    $this->booking_model->update_booking($booking_id, $booking);
            $this->booking_model->increase_escalation_reschedule($booking_id, "count_reschedule");
	    $data['internal_status'] = "Pending";
	    $data['current_status'] = "Pending";
	    log_message('info', __FUNCTION__ . " update service cenetr action table: " . print_r($data, true));
	    $this->vendor_model->update_service_center_action($booking_id, $data);

	    $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
	    $this->asynchronous_lib->do_background_process($url, $send);

	    //Log this state change as well for this booking
	    //param:-- booking id, new state, old state, employee id, employee name
           
	    $this->notify->insert_state_change($booking_id, 
                    _247AROUND_RESCHEDULED , _247AROUND_PENDING , 
                    $booking['reschedule_reason'] , 
                    $this->session->userdata('id'), $this->session->userdata('employee_id'),
                    _247AROUND);

	    log_message('info', __FUNCTION__ . " Set Mail flag to 0 : " . print_r($booking_id, true));
	    //Setting mail to vendor flag to 0, once booking is rescheduled
	    $this->booking_model->set_mail_to_vendor_flag_to_zero($booking_id);
	    log_message('info', __FUNCTION__ . " partner callback: " . print_r($booking_id, true));
	    $this->partner_cb->partner_callback($booking_id);

	    log_message('info', 'Rescheduled- Booking id: ' . $booking_id . " Rescheduled By " . $this->session->userdata('employee_id') . " data " . print_r($data, true));
	}

	redirect(base_url() . "employee/booking/review_bookings");
    }

    /**
     * @desc: This is used to complete booking by admin. It gets booking id and status as parameter. if status is 0 then redirect pending booking other wise redirect completed booking page
     * @param: String Array, string
     * @return :void
     */
    function process_complete_booking($booking_id, $status = "") {
	log_message('info', __FUNCTION__ . " Booking id: " . $booking_id . " Status: " . $status." Done By " . $this->session->userdata('employee_id'));
	// customer paid basic charge is comming in array
	// Array ( [100] =>  500 , [102] =>  300 )
	$customer_basic_charge = $this->input->post('customer_basic_charge');
	// Additional service charge is comming in array
	$additional_charge = $this->input->post('additional_charge');
	// Parts cost is comming in array
	$parts_cost = $this->input->post('parts_cost');
	$booking_status = $this->input->post('booking_status');
	$total_amount_paid = $this->input->post('grand_total_price');
	$admin_remarks = $this->input->post('admin_remarks');
	$serial_number = $this->input->post('serial_number');
        $upcountry_charges = $this->input->post("upcountry_charges");
	$internal_status = "Cancelled";
	$pincode = $this->input->post('booking_pincode');
	$state = $this->vendor_model->get_state_from_pincode($pincode);
	$service_center_details = $this->booking_model->getbooking_charges($booking_id);
        $k = 0;
	foreach ($customer_basic_charge as $unit_id => $value) {
	    // variable $unit_id  is existing id in booking unit details table of given booking id
	    $data = array();
	    $data['customer_paid_basic_charges'] = $value;
	    $data['customer_paid_extra_charges'] = $additional_charge[$unit_id];
	    $data['customer_paid_parts'] = $parts_cost[$unit_id];
            if(isset($serial_number[$unit_id])){
                $data['serial_number'] = $serial_number[$unit_id];
            } else {
                $data['serial_number'] = "";
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
                        if(!empty($service_center_details)){
                        if($service_center_details[0]['closed_date'] === NULL){
                            $data_service_center['closed_date'] =  $data['ud_closed_date'] = date('Y-m-d H:i:s');
                        }else{
                            $data_service_center['closed_date'] = $data['ud_closed_date']= $service_center_details[0]['closed_date'];
                        }
                        } else {
                             $data_service_center['closed_date'] = $data['ud_closed_date']= date('Y-m-d H:i:s');
                        }
			log_message('info', __FUNCTION__ . " New unit selected, previous unit " . print_r($unit_id, true)
			    . " Service charges id: "
			    . print_r($service_charges_id, true)
			    . " Data: " . print_r($data, true) . " State: " . print_r($state['state'], true));
			$new_unit_id = $this->booking_model->insert_new_unit_item($unit_id, $service_charges_id, $data, $state['state']);

			$data_service_center['booking_id'] = $booking_id;
			$data_service_center['unit_details_id'] = $new_unit_id;
			$data_service_center['service_center_id'] = $service_center_details[0]['service_center_id'];
			$data_service_center['update_date']  = date('Y-m-d H:i:s');
			$data_service_center['service_charge'] = $data['customer_paid_basic_charges'];
			$data_service_center['additional_service_charge'] = $data['customer_paid_extra_charges'];
			$data_service_center['parts_cost'] = $data['customer_paid_parts'];
			$data_service_center['serial_number'] = $data['serial_number'];
			$data_service_center['current_status'] = $data_service_center['internal_status'] = "Completed";
			$data_service_center['amount_paid'] = $total_amount_paid;
                        if($k == 0){
                            $data_service_center['upcountry_charges'] = $upcountry_charges;
                        }
                        
                       
			log_message('info', __FUNCTION__ . " New unit selected, service center action data " . print_r($data_service_center, true));
			$this->vendor_model->insert_service_center_action($data_service_center);
		    }
		}
	    } else {
		$data['booking_status'] = $booking_status[$unit_id];

		if ($data['booking_status'] === _247AROUND_COMPLETED ) {
		    $internal_status = _247AROUND_COMPLETED ;
		}
                
                if(!empty($service_center_details)){
                if($service_center_details[0]['closed_date'] === NULL){
                    $service_center['closed_date'] = $data['ud_closed_date'] = date('Y-m-d H:i:s');
                }else{
                    $service_center['closed_date'] = $data['ud_closed_date'] = $service_center_details[0]['closed_date'];
                }
                } else {
                     $service_center['closed_date'] = $data['ud_closed_date'] = date('Y-m-d H:i:s');
                }

		$data['id'] = $unit_id;

		log_message('info', ": " . " update booking unit details data " . print_r($data, TRUE));

		// update price in the booking unit details page
		$this->booking_model->update_unit_details($data);

                $service_center['closing_remarks'] = "";
               
                if(!empty($service_center_details) ){
                    if(!empty($service_center_details[0]['service_center_remarks'] ) && !empty($admin_remarks)){
                         $service_center['closing_remarks'] = "Service Center Remarks:- " . $service_center_details[0]['service_center_remarks'] .
			"  Admin:-  " . $admin_remarks;
                    } else if(!empty($service_center_details[0]['service_center_remarks']) && empty ($admin_remarks)){
                    
                     $service_center['closing_remarks'] = "Service Center Remarks:- " . $service_center_details[0]['service_center_remarks'];
                    } else if(empty($service_center_details[0]['service_center_remarks']) && !empty ($admin_remarks)){

                        $service_center['closing_remarks'] = "Admin:-  " . $admin_remarks;
                    }

                } else if(!empty ($admin_remarks)){
                    $service_center['closing_remarks'] = "Admin:-  " . $admin_remarks;
                }

		$service_center['internal_status'] = $service_center['current_status'] = $data['booking_status'];
		$service_center['unit_details_id'] = $unit_id;
		$service_center['update_date'] =  date('Y-m-d H:i:s');
		$service_center['service_charge'] = $data['customer_paid_basic_charges'];
		$service_center['additional_service_charge'] = $data['customer_paid_extra_charges'];
		$service_center['parts_cost'] = $data['customer_paid_parts'];
		$service_center['serial_number'] = $data['serial_number'];
		$service_center['amount_paid'] = $total_amount_paid;
                if($k == 0){
                    $service_center['upcountry_charges'] = $upcountry_charges;
                }
                
		log_message('info', ": " . " update Service center data " . print_r($service_center, TRUE));
		$this->vendor_model->update_service_center_action($booking_id, $service_center);
	    }
            $k = $k+1;
	}

	$booking['current_status'] = $internal_status;
	$booking['internal_status'] = $internal_status;
	$booking['booking_id'] = $booking_id;
        $booking['upcountry_paid_by_customer'] = $upcountry_charges;
        
        // check partner status
        $partner_id=$this->input->post('partner_id');
        $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'],$partner_id, $booking_id);
        if(!empty($partner_status)){
            $booking['partner_current_status'] = $partner_status[0];
            $booking['partner_internal_status'] = $partner_status[1];
        }
	
        if ($this->input->post('rating_stars') !== "") {
	    $booking['rating_stars'] = $this->input->post('rating_stars');
	    $booking['rating_comments'] = $this->input->post('rating_comments');
	}

	$booking['closing_remarks'] = $service_center['closing_remarks'];
        if(!empty($service_center_details)){
        if($service_center_details[0]['closed_date'] === NULL){
            $booking['closed_date'] = date('Y-m-d H:i:s');
        }else{
            $booking['closed_date'] = $service_center_details[0]['closed_date'];
        }
        } else {
           $booking['closed_date'] = date('Y-m-d H:i:s'); 
        }
	$booking['amount_paid'] = $total_amount_paid;

	//update booking_details table
	log_message('info', ": " . " update booking details data (" . $booking['current_status'] . ")" . print_r($booking, TRUE));
	// this function is used to update booking details table
	$this->booking_model->update_booking($booking_id, $booking);
        //Update Spare parts details table
        $this->service_centers_model->update_spare_parts(array('booking_id'=> $booking_id), 
                 array('status'=> $internal_status));

	//Log this state change as well for this booking
	//param:-- booking id, new state, old state, employee id, employee name
	$this->notify->insert_state_change($booking_id, $internal_status, _247AROUND_PENDING ,
                $booking['closing_remarks'], $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);

	$url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
	$send['booking_id'] = $booking_id;
	$send['state'] = $internal_status;
	$this->asynchronous_lib->do_background_process($url, $send);

	$this->partner_cb->partner_callback($booking_id);

	if ($status == "0") {
	    redirect(base_url() . 'employee/booking/view');
	} else {
	    redirect(base_url() . 'employee/booking/viewclosedbooking/' . $internal_status);
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
	$this->load->view('employee/header/'.$this->session->userdata('user_group'));
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
	log_message('info', __FUNCTION__ . " Booking id: " . $booking_id . " status: " . $status." Done By " . $this->session->userdata('employee_id'));
        
	$data['booking_date'] = date('d-m-Y', strtotime($this->input->post('booking_date')));
	$data['booking_timeslot'] = $this->input->post('booking_timeslot');
	$data['current_status'] = 'Pending';
	$data['internal_status'] = 'Scheduled';
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
        $partner_id=$this->input->post('partner_id');
        $partner_status = $this->booking_utilities->get_partner_status_mapping_data($data['current_status'], $data['internal_status'],$partner_id, $booking_id);
        if(!empty($partner_status)){
            $data['partner_current_status'] = $partner_status[0];
            $data['partner_internal_status'] = $partner_status[1];
        }
	
        if ($data['booking_timeslot'] == "Select") {
	    echo "Please Select Booking Timeslot.";
	} else {
	    log_message('info', __FUNCTION__ . " Convert booking, data : " . print_r($data, true));
	    $this->booking_model->convert_booking_to_pending($booking_id, $data, $status);

	   
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

	   
	    $unit_details['booking_status'] = "Pending";
	    $unit_details['vendor_to_around'] = "0.00";
	    $unit_details['around_to_vendor'] = "0.00";
            $unit_details['ud_closed_date'] = NULL;

	    log_message('info', __FUNCTION__ . " Convert Unit Details - data : " . print_r($unit_details, true));

	    $this->booking_model->update_booking_unit_details($booking_id, $unit_details);

	    //Log this state change as well for this booking          
            $this->notify->insert_state_change($booking_id, _247AROUND_PENDING , $status,
                    "", $this->session->userdata('id'), $this->session->userdata('employee_id'),_247AROUND);
            
            //Creating Job Card to Booking ID
            $this->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);

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
        $this->notify->insert_state_change($booking_id, _247AROUND_PENDING , _247AROUND_CANCELLED,
                    "", $this->session->userdata('id'), $this->session->userdata('employee_id'),_247AROUND);

	$this->load->view('employee/header/'.$this->session->userdata('user_group'));
	$this->load->view('employee/cancelled_to_pending', $bookings[0]);
    }

    /**
     *  @desc : This function is used to open a cancelled query
     *  @param : String (Booking Id)
     *  @return : redirect user controller
     */
    function open_cancelled_query($booking_id) {
        $status = array("current_status" => "FollowUp",
            "internal_status" => "FollowUp",
            "cancellation_reason" => NULL,
            "closed_date" => NULL);
        
        //check partner status from partner_booking_status_mapping table  
        $partner_id_data = $this->partner_model->get_order_id_by_booking_id($booking_id);
        $partner_id=$partner_id_data['partner_id'];
        if($partner_id){
            $partner_status = $this->booking_utilities->get_partner_status_mapping_data($status['current_status'], $status['internal_status'],$partner_id, $booking_id);
            if(!empty($partner_status)){
                $status['partner_current_status'] = $partner_status[0];
                $status['partner_internal_status'] = $partner_status[1];
            }
        }
        
	$this->booking_model->change_booking_status($booking_id,$status);

        //Log this state change as well for this booking
        $this->notify->insert_state_change($booking_id, _247AROUND_FOLLOWUP , _247AROUND_CANCELLED ,
                "Cancelled_Query to FollowUp", $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);
        
	redirect(base_url() . 'employee/booking/view_queries/FollowUp/'.PINCODE_ALL_AVAILABLE.'/0/0/' . $booking_id);
    }
    /**
     * @desc: This is used to show Booking Life Cycle of particular Booking
     * params: String Booking_ID
     * return: Array of Data for View
     */
    function get_booking_life_cycle($booking_id){
        $data['data'] = $this->booking_model->get_booking_state_change_by_id($booking_id);
        //Checking for 247Around user
        if($this->session->userdata('userType') == 'employee'){
            //Getting Name of SF Agent and SF Name
            foreach($data['data'] as $key=>$value){
                //Checking for SF Details
                if(!empty($value['service_center_id']) && empty($value['partner_id'])){
                    $data['data'][$key]['full_name'] = $this->service_centers_model->get_sc_login_details_by_id($value['service_center_id'])[0]['full_name'];
                    $data['data'][$key]['source'] = $this->vendor_model->getVendorContact($value['service_center_id'])[0]['name'];
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
    function validate_booking(){
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
            $this->form_validation->set_rules('booking_timeslot', 'Time Slot', 'required|xss_clean');
            
            return $this->form_validation->run();
    }  
   
   /**
     * @desc: This function is used to update inventory of vendor
     * parmas: Booking ID
     * @return: void
     * 
     */
    function update_vendor_inventory($booking_id) {
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
                }
                  elseif($stand_inch >=43){
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
    function get_missed_calls_view(){
        $data['data'] = $this->partner_model->get_missed_calls_details();
        $data['cancellation_reason'] = $this->partner_model->get_missed_calls_cancellation_reason();
        $data['updation_reason'] = $this->partner_model->get_missed_calls_updation_reason();
        
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
	$this->load->view('employee/get_missed_calls_view', $data);
    }
    
    /**
     * @Desc: This function is used to update partner missed calls details on completion
     *          It is being called by AJAX
     * @params: id, status
     * @return: Boolean
     */
    function update_partner_missed_calls($id, $status){
        $missed_call_leads = $this->partner_model->get_missed_calls_leads_by_id($id);
        //Incrementing counter by 1 , from its LATEST value
        $data['counter'] = ($missed_call_leads[0]['counter']+1);
        $data['status'] = $status;
        $data['update_date'] = date('Y-m-d H:i:s');
        $where = array('id'=>$id);
        $update = $this->partner_model->update_partner_missed_calls($where,$data);
        //Add Log
        log_message('info',__FUNCTION__.' Partner Missed calls leads has been Completed for id '.$id);
        //Adding details in Booking State Change
        $this->notify->insert_state_change("", _247AROUND_COMPLETED, _247AROUND_FOLLOWUP ,"Lead Completed Phone: ".$missed_call_leads[0]['phone'], $this->session->userdata('id'), $this->session->userdata('employee_id'),_247AROUND);
        
        echo $update;
        
    }
    
    /**
     * @Desc: This function is used to Cancel Leads
     * @params: POST ARRAY
     * @return: view
     * 
     */
    function cancel_missed_calls_lead(){
        $id = $this->input->post('id');
        $missed_call_leads = $this->partner_model->get_missed_calls_leads_by_id($id);
        //Incrementing counter by 1 , from its LATEST value
        $data['counter'] = ($missed_call_leads[0]['counter']+1);
        $data['status'] = 'Cancelled';
        $data['update_date'] = date('Y-m-d H:i:s');
        $data['cancellation_reason'] = $this->input->post('cancellation_reason');
        $where = array('id'=>$id);
        $update = $this->partner_model->update_partner_missed_calls($where,$data);
        
        //Add Log
        log_message('info',__FUNCTION__.' Partner Missed calls leads has been Cancelled for id '.$id);
        
        //Adding details in Booking State Change
        $this->notify->insert_state_change("", _247AROUND_CANCELLED, _247AROUND_FOLLOWUP ,$data['cancellation_reason']." Phone: ".$missed_call_leads[0]['phone'], $this->session->userdata('id'), $this->session->userdata('employee_id'),_247AROUND);
        
        $this->session->set_flashdata('cancel_leads', 'Leads has been cancelled for phone '.$missed_call_leads[0]['phone']);
        redirect(base_url() . "employee/booking/get_missed_calls_view");
    }
    
    /**
     * @Desc: This function is used to Update Leads
     * @params: POST ARRAY
     * @return: view
     * 
     */
    function update_missed_calls_lead(){
        $id = $this->input->post('id');
        $missed_call_leads = $this->partner_model->get_missed_calls_leads_by_id($id);
        
        //When Customer Not Pick Call
        if($this->input->post('updation_reason') == "Customer Not Picking Call"){
            //Incrementing counter by 1 , from its LATEST value
            $data['counter'] = ($missed_call_leads[0]['counter'] + 1);
            $data['update_date'] = date('Y-m-d H:i:s');
            $data['action_date'] = date("Y-m-d H:i:s", strtotime('+5 hours'));
            $data['updation_reason'] = $this->input->post('updation_reason');
            $where = array('id' => $id);
            $update = $this->partner_model->update_partner_missed_calls($where,$data);
            
            //Add Log
            log_message('info',__FUNCTION__.' Partner Missed calls leads has been Updated for id '.$id);
            
        } // 1 Day scheduled
        else if($this->input->post('updation_reason') == "Customer asked to call after 1 day"){
            
            //Incrementing counter by 1 , from its LATEST value
            $data['counter'] = ($missed_call_leads[0]['counter'] + 1);
            $data['update_date'] = date('Y-m-d H:i:s');
            $data['action_date'] = date("Y-m-d ", strtotime('+1 days'));
            $data['updation_reason'] = $this->input->post('updation_reason');
            $where = array('id' => $id);
            $update = $this->partner_model->update_partner_missed_calls($where,$data);
            
            //Add Log
            log_message('info',__FUNCTION__.' Partner Missed calls leads has been Updated for 1 Days - id '.$id);
            
        }   // 2 Day Scheduled
        else if($this->input->post('updation_reason') == "Customer asked to call after 2 day"){
            
            //Incrementing counter by 1 , from its LATEST value
            $data['counter'] = ($missed_call_leads[0]['counter'] + 1);
            $data['update_date'] = date('Y-m-d H:i:s');
            $data['action_date'] = date("Y-m-d ", strtotime('+2 days'));
            $data['updation_reason'] = $this->input->post('updation_reason');
            $where = array('id' => $id);
            $update = $this->partner_model->update_partner_missed_calls($where,$data);
            
            //Add Log
            log_message('info',__FUNCTION__.' Partner Missed calls leads has been Updated for 2 Days - id '.$id);
            
        } // 3 day scheduled
        else if($this->input->post('updation_reason') == "Customer asked to call after 3 day"){
            
            //Incrementing counter by 1 , from its LATEST value
            $data['counter'] = ($missed_call_leads[0]['counter'] + 1);
            $data['update_date'] = date('Y-m-d H:i:s');
            $data['action_date'] = date("Y-m-d ", strtotime('+3 days'));
            $data['updation_reason'] = $this->input->post('updation_reason');
            $where = array('id' => $id);
            $update = $this->partner_model->update_partner_missed_calls($where,$data);
            
            //Add Log
            log_message('info',__FUNCTION__.' Partner Missed calls leads has been Updated for 3 Days - id '.$id);
            
        }
        
        //Adding details in Booking State Change
        $this->notify->insert_state_change("", _247AROUND_FOLLOWUP, _247AROUND_FOLLOWUP ,$this->input->post('updation_reason')." Phone: ".$missed_call_leads[0]['phone'], $this->session->userdata('id'), $this->session->userdata('employee_id'),_247AROUND);
        
        $this->session->set_flashdata('update_leads', 'Leads has been Updated for phone '.$missed_call_leads[0]['phone']);
        redirect(base_url() . "employee/booking/get_missed_calls_view");
    }
    
    /**
     * @Desc: This function is used to update the pay to sf flag in booking details table
     * @parmas: void
     * @return: view
     * 
     */
    function update_not_pay_to_sf_booking(){
        log_message('info', __FUNCTION__);
        
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/update_pay_to_sf_booking');
    }
    
    /**
     * @Desc: This function is used to update the pay to sf flag in booking details table
     * @parmas: void
     * @return: view
     * 
     */
    function  process_update_not_pay_to_sf_booking(){
        log_message('info', __FUNCTION__);
        
        $booking_id = $this->input->post('booking_id');
        if(!empty($booking_id)){
            foreach ($booking_id as $value) {
                if(!empty($value)){
                    $is_wall_mount_exist = $this->booking_model->get_unit_details(array('booking_id'=>$value,'price_tags'=>'Installation & Demo'));
                    if(!empty($is_wall_mount_exist)){
                        $this->booking_model->update_booking_unit_details_by_any(array('booking_id'=> $value,'price_tags'=>'Installation & Demo'),array('pay_to_sf' =>'0'));
                        log_message('info',__FUNCTION__.' Pay To SF update in booking_unit_details for Booking ID = '.$value);
                    }
                }
                
            }
            $this->session->set_flashdata('msg', 'Booking Updated Successfully');
            redirect(base_url() . "employee/booking/update_not_pay_to_sf_booking");
        }else{
            redirect(base_url() . "employee/booking/update_not_pay_to_sf_booking");
        }
        
    }
    
    function auto_assigned_booking(){
       $data['data'] =  $this->vendor_model->auto_assigned_booking();
       $this->load->view('employee/header/'.$this->session->userdata('user_group'));
       $this->load->view('employee/auto_assigned_booking',$data);
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
        $partner_id= true;
   
	$config['base_url'] = base_url() . 'employee/booking/get_pending_booking_by_partner_id/'.$page;
	$config['total_rows'] = $this->booking_model->total_pending_booking("","",$partner_id);
	
	if($offset != "All"){
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
        
        
	if ($this->session->flashdata('result') != ''){
	    $data['success'] = $this->session->flashdata('result');
        }

	$this->load->view('employee/header/'.$this->session->userdata('user_group'));
	$this->load->view('employee/booking', $data);
    }
}
