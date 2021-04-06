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
        $this->load->model('booking_request_model');
        $this->load->model('service_centre_charges_model');
        $this->load->model('warranty_model'); 
        $this->load->model('indiapincode_model');        
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
        $this->load->library('warranty_utilities');
        $this->load->library('booking_utilities');
        $this->load->library('partner_sd_cb');
        $this->load->library('asynchronous_lib');
        $this->load->library("initialized_variable");
        $this->load->library("push_notification_lib");
        $this->load->library("paytm_payment_lib");
        $this->load->library('paytmlib/encdec_paytm');
        $this->load->library('validate_serial_no');
        $this->load->library("invoice_lib");
        $this->load->library("booking_creation_lib");
        $this->load->library('user_agent');
        $this->load->helper('file');
        $this->load->dbutil();
        
        // Mention those functions whom you want to skip from employee specific validations
        $arr_functions_skip_from_validation = ['get_appliances', 'update_booking_by_sf','getPricesForCategoryCapacity','get_booking_upcountry_details', 'Api_getAllBookingInput', 'getCategoryCapacityForModel','get_posible_parent_id','getBrandForService','get_warranty_data','get_city_from_pincode'];
        $arr_url_segments = $this->uri->segments; 
        $allowedForSF = 0;
        if(!empty(array_intersect($arr_functions_skip_from_validation, $arr_url_segments))){        
            $allowedForSF = 1;
        }
        
        if(!$allowedForSF){
            if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
                return TRUE;
            } else {
                redirect(base_url() . "employee/login");
            } 
        }
       else{
            if(in_array("Api_getAllBookingInput", $arr_url_segments)){
                return TRUE;
            }
            else{
                if (($this->session->userdata('userType') == 'partner' && !empty($this->session->userdata('partner_id')) && $this->session->userdata('loggedIn') == TRUE) || (($this->session->userdata('userType') == 'service_center') && !empty($this->session->userdata('service_center_id')) && !empty($this->session->userdata('is_sf'))) || (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) || (($this->session->userdata('userType') == 'dealers') && !empty($this->session->userdata('dealer_id')))) {
                    return TRUE;
                } 
                else {
                    log_message('info', __FUNCTION__. " Session Expire for Service Center");
                    $this->session->sess_destroy();
                    redirect(base_url() . "service_center/login");
                }
            }
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
        //print_r($_POST);
        if ($this->input->post()) {
            $primary_contact_no = $this->input->post('booking_primary_contact_no');
            $user_id = $this->input->post("user_id");
            
            //Check Validation
            $checkValidation = $this->booking_creation_lib->validate_booking();
            if ($checkValidation) {
                log_message('info', __FUNCTION__);
                log_message('info', " Booking Insert Contact No: " . $primary_contact_no);
                $status = $this->getAllBookingInput($user_id, INSERT_NEW_BOOKING);
                if ($status) {  
                    log_message('info', __FUNCTION__ . " Booking ID " . $status['booking_id']);
                    
                    $this->partner_cb->partner_callback($status['booking_id']);
                    $this->session->set_userdata(['success' => 'Booking inserted successfully with Booking Id : '.$status["booking_id"]]);
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
            $heading = "Booking Error";
            $message = "Oops... No input provided !";
            $error = & load_class('Exceptions', 'core');
            echo $error->show_error($heading, $message, 'custom_error');
        }
    }
    
    function Api_getAllBookingInput($user_id, $booking_id){
        $_POST = json_decode(file_get_contents('php://input'), true);
        $this->getAllBookingInput($user_id, $booking_id);
    }

    /**
     *  @desc : This function is used to insert or update data in booking unit details and appliance details table
     *  @param : user id, booking id (optional)
     *  @return : Array(booking details)
     */
    function getAllBookingInput($user_id, $booking_id) { 
        log_message('info', __FUNCTION__);
        //log_message('info', " Booking Insert " . $user_id . " Booking ID" . $booking_id . " Done By " . $this->session->userdata('employee_id'));

        $updated_unit_id = array();
        
        // All brand comming in array eg-- array([0]=> LG, [1]=> BPL)
        $appliance_brand = $this->input->post('appliance_brand');
        //print_r($appliance_brand); die();
        $upcountry_data_json = $this->input->post('upcountry_data');
        $upcountry_data = json_decode($upcountry_data_json, TRUE);
        $booking = $this->insert_data_in_booking_details($booking_id, $user_id, count($appliance_brand));
        // Get Existing Price Tags
        $whereOldPrice['booking_id'] = $booking_id;
        $groupBY  = array('appliance_id');
        $oldPriceTags = $this->reusable_model->get_search_result_data('booking_unit_details','appliance_id,GROUP_CONCAT(price_tags) as price_tag',$whereOldPrice,NULL,NULL,NULL,NULL,NULL,$groupBY);
        // End
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
            $purchase_date = $this->input->post('purchase_date');
            $order_item_id = $this->input->post('order_item_id');

            $appliance_id_array = $this->input->post('appliance_id');
            $appliance_id = array();
            if (isset($appliance_id_array)) {
                if (!empty($appliance_id_array)) {
                    $appliance_id = array_unique($appliance_id_array);
                }
            }
            // Do not un comment until serial input added in the form
            // $serial_number = $this->input->post('serial_number');
            
            $serial_number = !empty($this->input->post('serial_number')) ? $this->input->post('serial_number') : "";
            $serial_number_pic = !empty($this->input->post('serial_number_pic')) ? $this->input->post('serial_number_pic') : "";

            $partner_net_payable = $this->input->post('partner_paid_basic_charges');
            $appliance_description = $this->input->post('appliance_description');
            // All discount comming in array.  Array ( [BPL] => Array ( [100] => Array ( [0] => 200 ) [102] => Array ( [0] => 100 ) [103] => Array ( [0] => 0 ) ) .. Key is Appliance brand, unit id and discount value.
            $discount = $this->input->post('discount');
            // All prices comming in array with pricing table id
            /* Array([BPL] => Array([0] => 100_300 [1] => 102_250) [Micromax] => Array([0] => 100_300)) */
            //Array ( ['brand'] => Array ( [0] => id_price ) )
            $pricesWithId = $this->input->post("prices");
            $user['user_email'] = $this->input->post('user_email');
            $user['alternate_phone_number'] = trim($this->input->post('booking_alternate_contact_no'));
            $result = array();
            $result['DEFAULT_TAX_RATE'] = 0;
            foreach ($appliance_brand as $key => $value) {
                
                $services_details = array();
                $appliances_details = array();
                $appliances_details['user_id'] = $booking['user_id'];

                $appliances_details['brand'] = $services_details['appliance_brand'] = $value; // brand
                // get category from appiance category array for only specific key.
                $appliances_details['category'] = $services_details['appliance_category'] = $appliance_category[$key];
                // get appliance_capacity from appliance_capacity array for only specific key.
                $appliances_details['capacity'] = $services_details['appliance_capacity'] = $appliance_capacity[$key];
                // get model_number from appliance_capacity array for only specific key such as $model_number[0].
                $appliances_details['model_number'] = $services_details['model_number'] = $model_number[$key];
                // get appliance tag from appliance_tag array for only specific key such as $appliance_tag[0].
                //$appliances_details['tag']  = $appliance_tags[$key];
                $appliances_details['purchase_date'] = $services_details['purchase_date'] =  $this->miscelleneous->convert_date_to_database_format($purchase_date[$key]);
                $services_details['booking_id'] = $booking['booking_id'];
                //$appliances_details['serial_number'] = $services_details['serial_number'] = $serial_number[$key];
                $appliances_details['description'] = $services_details['appliance_description'] = $appliance_description[$key];
                $appliances_details['service_id'] = $services_details['service_id'] = $booking['service_id'];
                $appliances_details['last_service_date'] = date('Y-m-d H:i:s');
                $services_details['partner_id'] = $booking['partner_id'];
                $services_details['sub_order_id'] = trim($order_item_id[$key]);
                // save serial number and serial number pic in booking_unit_details and service_center_booking_action if request type is changed           
                $services_details['serial_number'] = $serial_number;
                $services_details['serial_number_pic'] = $serial_number_pic;
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
                
                if (!empty($appliance_id[$key])) {
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
                $price_tag = array();
                
                //Array ( ['brand'] => Array ( [0] => id_price ) )
                if (!empty($pricesWithId[$brand_id][$key + 1])) { 
                foreach ($pricesWithId[$brand_id][$key + 1] as $b_key => $values) {

                    $prices = explode("_", $values);  // split string..
                    $services_details['id'] = $prices[0]; // This is id of service_centre_charges table.
                    if(!empty($discount[$brand_id][$key + 1][$services_details['id']][0]))
                    {
                        $services_details['around_paid_basic_charges'] = $discount[$brand_id][$key + 1][$services_details['id']][0];
                    }
                    else
                    {
                        $services_details['around_paid_basic_charges'] =    0;
                    }
                    if(!empty($partner_net_payable[$brand_id][$key + 1][$services_details['id']][0])){
                        $services_details['partner_paid_basic_charges'] = $partner_net_payable[$brand_id][$key + 1][$services_details['id']][0];
                    }
                    else
                    {
                        $services_details['partner_paid_basic_charges'] = 0;
                    }
                    $services_details['partner_net_payable'] = $services_details['partner_paid_basic_charges'];
                    $services_details['around_net_payable'] = $services_details['around_paid_basic_charges'];
                    if(isset($booking['current_status'])){
                        $services_details['booking_status'] = $booking['current_status'];
                    }
                    
                    log_message('info', __METHOD__ . " Before booking is insert/update: " . $booking_id);
                    
                    switch ($booking_id) {
                        case INSERT_NEW_BOOKING:
                            log_message('info', __METHOD__ . " Insert Booking Unit Details: ");
                            $result = $this->booking_model->insert_data_in_booking_unit_details($services_details, $booking['state'], $b_key);
                            array_push($price_tag, $result['price_tags']);
                            
                            break;
                        default:

                            log_message('info', __METHOD__ . " Update Booking Unit Details: " . " Previous booking id: " . $booking_id);
                            // save model and DOP values in SF_ columns while updating booking.
                            $services_details['purchase_date'] = date('Y-m-d', strtotime($services_details['purchase_date']));
                            $services_details['sf_model_number'] = $services_details['model_number'];
                            $services_details['sf_purchase_date'] = $services_details['purchase_date'];
                            // --------------------------------------------------------
                            if(!empty($this->session->userdata('id'))){ 
                                $agent_details['agent_id'] = $id =  $this->session->userdata('id');
                                $agent_details['agent_type'] = $agentType = _247AROUND_EMPLOYEE_STRING;
                            }
                            else{
                                    if(($this->input->post('call_from_api')) && ($this->input->post('sc_agent_id'))){
                                        $agent_details['agent_id'] = $id =  $this->input->post('sc_agent_id');
                                        $agent_details['agent_type'] =  $agentType = _247AROUND_SF_STRING;
                                    }
                                    else{
                                        $agent_details['agent_id'] = $id =  $this->session->userdata('service_center_agent_id');
                                        $agent_details['agent_type'] =  $agentType = _247AROUND_SF_STRING;
                                    }
                            }
                            $arr_result = $this->booking_model->update_booking_in_booking_details($services_details, $booking_id, $booking['state'], $b_key,$agent_details);
                            foreach ($arr_result as $result_key => $result) {
                                array_push($updated_unit_id, $arr_result[$result_key]['unit_id']);
                                array_push($price_tag, $arr_result[$result_key]['price_tags']);
                            }
                            
                            $booking_symptom = $this->booking_model->getBookingSymptom($booking_id);
                            if(count($booking_symptom)>0)
                            {
                                $bookingSymptom['symptom_id_booking_creation_time'] = $this->input->post('booking_request_symptom');
                                $rowsStatus = $this->booking_model->update_symptom_defect_details($booking_id, $bookingSymptom);
                            }
                            else {
                                $bookingSymptom['booking_id'] = $booking_id;
                                $bookingSymptom['symptom_id_booking_creation_time'] = $this->input->post('booking_request_symptom');
                                $bookingSymptom['create_date'] = date("Y-m-d H:i:s");
                                if($this->input->post('booking_request_symptom')) {
                                    $this->booking_model->addBookingSymptom($bookingSymptom);
                                }
                            }
                            break;
                    }
                }
                }
            }
            if (!empty($updated_unit_id)) {
                log_message('info', __METHOD__ . " UNIT ID: " . print_r($updated_unit_id, true));
                $sf_id = $this->reusable_model->get_search_query('booking_details','assigned_vendor_id',array('booking_id'=>$booking_id),NULL,NULL,NULL,NULL,NULL)->result_array();
                if(!empty($sf_id[0]['assigned_vendor_id'])){
                    $inventory_details = array('receiver_entity_id' => $sf_id[0]['assigned_vendor_id'],
                        'receiver_entity_type' => _247AROUND_SF_STRING,
                        'stock' => 1,
                        'agent_id' => $id,
                        'agent_type' => $agentType,
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
                            $b_id = $booking['booking_id'];
                            $async_data["partner_id[$b_id]"] = $booking['partner_id'];
                            $async_data["order_id"] = array($booking['booking_id'] =>$booking['order_id']);
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
            if (isset($upcountry_data['vendor_not_found'])) {
                $this->miscelleneous->sf_not_exist_for_pincode(array(
                    "booking_id" => $booking['booking_id'],
                    "booking_pincode" => $booking['booking_pincode'],
                    "service_id" => $appliances_details['service_id'],
                    "partner_id" => $booking['partner_id'],
                    "city" => $booking['city'],
                    "order_id" => $booking['order_id']
                ));
            }

            $this->booking_model->update_request_type($booking['booking_id'], $price_tag,$oldPriceTags);
            // save serial number and serial number pic in service_center_booking_action       
            $ssba_details['serial_number'] = $serial_number;
            $ssba_details['serial_number_pic'] = $serial_number_pic;
            $this->service_centers_model->update_service_centers_action_table($booking['booking_id'], $ssba_details);
            $this->engineer_model->update_engineer_table($ssba_details, ['booking_id' => $booking['booking_id']]);
            if($booking_id == INSERT_NEW_BOOKING){
                $this->send_sms_email($booking['booking_id'], "SendWhatsAppNo");
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
        $booking['cancellation_reason'] = NULL;
        $booking['repeat_reason'] = NULL;
        $actor = $next_action = NULL;
        $booking_update_flag = 0;
        // Do not validate Order Id, if Request type is changed before Completion.
        $is_sf_panel = !empty($this->input->post('is_sf_panel')) ? $this->input->post('is_sf_panel') : 0;
        switch ($booking_id) {
            case INSERT_NEW_BOOKING:
                $booking['booking_id'] = $this->create_booking_id($booking['user_id'], $booking['source'], $booking['type'], $booking['booking_date']);
                $booking_symptom['booking_id'] = $booking['booking_id'];
                $booking_symptom['symptom_id_booking_creation_time'] = $this->input->post('booking_request_symptom');

                $is_send_sms = 1;
                if ($booking['type'] == "Booking") {
                $booking_id_with_flag['new_state'] = _247AROUND_PENDING;
                $booking_id_with_flag['old_state'] = _247AROUND_NEW_BOOKING;
                 }
                 else
                {
                $booking_id_with_flag['new_state'] = _247AROUND_FOLLOWUP;
                $booking_id_with_flag['old_state'] = _247AROUND_NEW_QUERY;
                }
                $booking['service_center_closed_date'] = NULL;
                if ($booking['type'] == "Booking") {
                    $booking['initial_booking_date'] = $booking['booking_date'];
                    $booking['current_status'] =  _247AROUND_PENDING;
                    $booking['internal_status'] = 'Scheduled';
                    
                    $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'], $booking['partner_id'], $booking_id);
                    if (!empty($partner_status)) {               
                        $booking['partner_current_status'] = $partner_status[0];
                        $booking['partner_internal_status'] = $partner_status[1];
                        $actor = $booking['actor'] = $partner_status[2];
                        $next_action = $booking['next_action'] = $partner_status[3];
                    }
                    
                }

                log_message('info', "New Booking ID created" . print_r($booking['booking_id'], true));
                break;
            default :
                if ($booking['type'] == "Booking") {
                    //Query remarks has either query or booking remarks
                    $booking_id_with_flag = $this->change_in_booking_id($booking['type'], $booking_id, $this->input->post('query_remarks'));
                    if( $booking_id_with_flag['query_to_booking'] == "1"){
                        $booking['initial_booking_date'] = $booking['booking_date'];
                        $booking['current_status'] =  _247AROUND_PENDING;
                        $booking['internal_status'] = 'Scheduled';
                    }
                    $booking_update_flag = $booking_id_with_flag['booking_update_flag'];
                } else {
                    //Internal status has query remarks only
                    $booking_id_with_flag = $this->change_in_booking_id($booking['type'], $booking_id, $this->input->post('internal_status'));
                    $booking_update_flag = $booking_id_with_flag['booking_update_flag'];
                }
                unset($booking_id_with_flag['booking_update_flag']);
                $booking['booking_id'] = $booking_id_with_flag['booking_id'];
                $is_send_sms = $booking_id_with_flag['query_to_booking'];
                log_message('info', " Booking Updated: " . print_r($booking['booking_id'], true) . " Query to booking: " . print_r($is_send_sms, true));

                break;
        }
        if($this->input->post('repeat_reason') && $booking['parent_booking'] ){
            $booking['repeat_reason'] = $this->input->post('repeat_reason');
        }        
        $file_description_arr = $this->input->post('file_description');
        
        if (!empty($this->session->userdata('id'))) {
            $agent_id = $this->session->userdata('id');
        } else {
            $agent_id = $this->session->userdata('service_center_agent_id');
        }
        
        if (!empty($this->input->post('exit_support_file'))) {
            for ($j = 0; $j < count($this->input->post('exit_support_file')); $j++) {
                $pre_file_id = $this->input->post('exit_support_file_id')[$j];
                $file_id = $this->input->post('file_description')[$j];
                if ($file_id != $pre_file_id) {
                    $file_data['agent_id'] = $agent_id;
                    $file_data['booking_files.file_description_id'] = $file_id;
                    $this->booking_model->update_booking_file($file_data, array("booking_files.file_description_id" => $pre_file_id, "booking_files.booking_id" => $booking['booking_id']));
                }
            }
        }

        //add support file for booking id if it is uploaded
        if (!empty($_FILES['support_file']['tmp_name'])) {

            for ($i = 0; $i < count($_FILES['support_file']['tmp_name']); $i++) {
                if (!empty($_FILES['support_file']['tmp_name'][$i])) {
                    if (($_FILES['support_file']['error'][$i] != UPLOAD_ERR_NO_FILE)) {
                        $booking_files = array();
                        $support_file = $this->upload_orderId_support_file($booking['booking_id'], $_FILES['support_file']['tmp_name'][$i], $_FILES['support_file']['error'][$i], $_FILES['support_file']['name'][$i]);
                        if ($support_file) {
                            $booking_files['booking_id'] = $booking['booking_id'];
                            $booking_files['file_description_id'] = $file_description_arr[$i];
                            $booking_files['file_name'] = $support_file;
                            $booking_files['file_type'] = $_FILES['support_file']['type'][$i];
                            $booking_files['size'] = $_FILES['support_file']['size'][$i];
                            $booking_files['agent_id'] = $agent_id;
                            $booking_files['create_date'] = date("Y-m-d H:i:s");
                            $status = $this->booking_model->insert_booking_file($booking_files);
                            if (!$status) {
                                return false;
                            }
                        } else {
                            log_message('info', __FUNCTION__ . "Error in Uploading File  " . $_FILES['support_file']['tmp_name'][$i] . ", Error  " . $_FILES['support_file']['error'][$i] . ", Booking ID: " . $booking['booking_id']);
                        }
                    } else {
                        log_message('info', __FUNCTION__ . "Error file not uploaded  " . $_FILES['support_file']['tmp_name'][$i] . ", Error  " . $_FILES['support_file']['error'][$i] . ", Booking ID: " . $booking['booking_id']);
                    }
                }
            }
        } 


        $validate_order_id = $this->validate_order_id($booking['partner_id'], $booking['booking_id'], $booking['order_id'], $booking['amount_due']);
        if ($validate_order_id || !empty($is_sf_panel)) {
            $is_dealer = $this->dealer_process($booking['city'], $booking['partner_id'], $booking['service_id'], $booking['state']);
           
            if(!empty($is_dealer)){
                $booking['dealer_id'] = $is_dealer;
            }

            if ($booking['type'] == 'Booking') {
                
                $booking['booking_remarks'] = $remarks;
                $new_state = $booking_id_with_flag['new_state'];
                $old_state = $booking_id_with_flag['old_state'];
                // Do not change Booking Current status ,if request type is changed from Step 1 of Booking Completion flow
                if(empty($is_sf_panel)){
                    $booking['current_status'] =  _247AROUND_PENDING;
                }
            } else if ($booking['type'] == 'Query') {

                $booking['current_status'] = _247AROUND_FOLLOWUP;
                $internal_status = $this->input->post('internal_status');
                if (!empty($internal_status)) {
                    $booking['internal_status'] = $internal_status;
                    
                    $response = $this->miscelleneous->partner_completed_call_status_mapping($booking['partner_id'], $internal_status);
                    if(!empty($response)){

                        $this->booking_model->partner_completed_call_status_mapping($booking_id, array('partner_call_status_on_completed' => $response));
                    } else {
                        log_message('info', __METHOD__. " Staus Not found for partner ID ". $booking['partner_id']. " status ". $internal_status);
                    }
                
                } else {
                    $booking['internal_status'] = _247AROUND_FOLLOWUP;
                }
                if ($booking['internal_status'] == CUSTOMER_NOT_REACHABLE) {
                    $this->send_sms_email($booking_id, "Customer not reachable");
                }

                $booking['query_remarks'] = $remarks;

                $new_state = $booking_id_with_flag['new_state'];
                $old_state = $booking_id_with_flag['old_state'];
                
                $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'], $booking['partner_id'], $booking_id);
                if (!empty($partner_status)) {               
                    $booking['partner_current_status'] = $partner_status[0];
                    $booking['partner_internal_status'] = $partner_status[1];
                    $actor = $booking['actor'] = $partner_status[2];
                    $next_action = $booking['next_action'] = $partner_status[3];
                }
                    
            }

            // check partner status
            
            
            switch ($booking_id) {

                case INSERT_NEW_BOOKING:
                    // Save Booking Creater Id and Source
                    $created_by = !empty($this->session->userdata('id')) ? $this->session->userdata('id') : "";
                    $created_source = !empty($this->session->userdata('user_source')) ? $this->session->userdata('user_source') : "";
                    $created_by_agent_type = !empty($this->session->userdata('userType')) ? $this->session->userdata('userType') : "";
                    $booking['created_by_agent_type'] = $created_by_agent_type;
                    $booking['created_by_agent_id'] = $created_by;
                    $booking['created_source'] = $created_source;
                    $booking['create_date'] = date("Y-m-d H:i:s");
                    $booking_symptom['create_date'] = date("Y-m-d H:i:s");
                
                    $status = $this->booking_model->addbooking($booking);
                    if($this->input->post('booking_request_symptom')) {
                        $symptomStatus = $this->booking_model->addBookingSymptom($booking_symptom);
                    }
                    

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
                     if(!empty($this->session->userdata('service_center_id'))){
                         $booking['edit_by_sf'] = 1;
                     }
                    $status = $this->booking_model->update_booking($booking_id, $booking);
                    if ($status) {
                        $booking['is_send_sms'] = $is_send_sms;
                    } else {
                        return false;
                    }
                    break;
            }
            if(!empty($this->session->userdata('service_center_id'))){
                $e_id = $this->session->userdata('service_center_agent_id');
                $employeeId = $this->session->userdata('service_center_name');
                $stateChangePartnerID = NULL;
                $stateChangeSFID  = $this->session->userdata('service_center_id');
            }
            else{
                if(($this->input->post('call_from_api')) && ($this->input->post('sc_agent_id'))){
                    $e_id = $this->input->post('sc_agent_id');
                    $employeeId = "";
                    $stateChangePartnerID = NULL;
                    $stateChangeSFID  = $this->input->post('service_center_id');
                }
                else{
                    $e_id = $this->session->userdata('id');
                    $employeeId = $this->session->userdata('employee_id');
                    $stateChangePartnerID = _247AROUND;
                    $stateChangeSFID  = NULL;
                }
            }
            
            $this->notify->insert_state_change($booking['booking_id'], $new_state, $old_state, $remarks, $e_id,  $employeeId,$actor,$next_action,$stateChangePartnerID,$stateChangeSFID);
            if($booking['parent_booking']){
            $this->notify->insert_state_change($booking['booking_id'], "Repeat Booking", $new_state, "Parent ID - ".$booking['parent_booking'].", Repeat Reason - ".$booking['repeat_reason'], $e_id, $employeeId,
                    $actor,$next_action,$stateChangePartnerID,$stateChangeSFID);
            }
            if($booking_update_flag == 1){
                $this->notify->insert_state_change($booking['booking_id'], BOOKING_DETAILS_UPDATED, "", $this->input->post("query_remarks"), $e_id,$employeeId, ACTOR_NOT_DEFINE, NEXT_ACTION_NOT_DEFINE, $stateChangePartnerID,$stateChangeSFID);
            }
            return $booking;
        } else {
//            $this->session->set_userdata(['error_msg' => 'Order Id is not Valid / Filled against this Booking.']);
            return false;
        }
    }
    
    function dealer_process($city, $partner_id, $service_id, $state){
        $dealer_phone_number = $this->input->post("dealer_phone_number");
        $dealer_id = "";
        if(!empty($dealer_phone_number)){
            $data['city'] = $city;
            $data['state'] = $state;
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
        $booking['booking_address'] = trim($this->input->post('home_address'));
        $booking['city'] = trim($this->input->post('city'));
        $booking_date = $this->input->post('booking_date');
        if($this->input->post('partner_source')){
            $booking['partner_source'] = $this->input->post('partner_source');
        }
        else{
            $booking['partner_source'] = NULL;
        }
        $booking['booking_date'] = date('Y-m-d', strtotime($booking_date));
        $booking['booking_pincode'] = trim($this->input->post('booking_pincode'));
        // select state, taluk, district by pincode
        $distict_details = $this->vendor_model->get_distict_details_from_india_pincode(trim($booking['booking_pincode']));
        $booking['state'] = $distict_details['state'];
        $booking['district'] = $distict_details['district'];
        $booking['taluk'] = $distict_details['taluk'];
        $booking['booking_primary_contact_no'] = trim($this->input->post('booking_primary_contact_no'));
        $booking['order_id'] = $this->input->post('order_id');
        $booking['booking_alternate_contact_no'] = trim($this->input->post('booking_alternate_contact_no'));
        $booking['booking_timeslot'] = $this->input->post('booking_timeslot');
        $booking['update_date'] = date("Y-m-d H:i:s");
        $booking['partner_id'] = $this->input->post('partner_id');
        if($this->input->post('is_repeat')){
           $booking['parent_booking'] = $this->input->post('parent_id');
       }
       else{
           $booking['parent_booking'] = NULL;
       }
        if(empty($user_id)){
            $user['phone_number'] = trim($booking['booking_primary_contact_no']);
            $user['name'] = trim($this->input->post('user_name'));
            $user['user_email'] = trim($this->input->post('user_email'));
            $user['home_address'] = trim($booking['booking_address']);
            $user['city'] = trim($booking['city']);
            $user['state'] =  trim($booking['state']);
            $user['pincode'] = trim($booking['booking_pincode']) ;
            $user['alternate_phone_number'] = trim($booking['booking_alternate_contact_no']);
            $user['create_date'] = date("Y-m-d H:i:s");
        
            $user_id = $this->user_model->add_user($user);
            

            $this->booking_model->addSampleAppliances($user_id, 5);
        }
        
        $booking['user_id'] = $user_id;
        
        //$booking['booking_request_symptom'] = $this->input->post('booking_request_symptom');

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
                    $data['booking_update_flag'] = 0;
                    log_message('info', __FUNCTION__ . " Query Converted to Booking Booking ID" . print_r($data['booking_id'], true));
                } else {
                    //Booking to be updated to booking
                    $data['booking_id'] = $booking_id;
                    
                    $data['old_state'] = _247AROUND_PENDING;
                    $data['new_state'] = _247AROUND_PENDING;
                    $data['query_to_booking'] = '2';
                    $data['booking_update_flag'] = 1;
                    log_message('info', __FUNCTION__ . " Booking Updateded to Booking Booking ID" . print_r($data['booking_id'], true));
                }

                break;

            case "Query":
                if (strpos($booking_id, "Q-") === FALSE) {
                    //Booking to be converted to query
                    $data['booking_id'] = "Q-" . $booking_id;
                    log_message('info', __FUNCTION__ . " Booking to be Converted to Query Booking ID" . print_r($data['booking_id'], true));

                    // Delete Data from engineer_table_sign If any
                    $this->engineer_model->delete_engineer_sign(['booking_id' => $booking_id]);
                    $data['old_state'] = _247AROUND_PENDING;
                    $data['new_state'] = _247AROUND_FOLLOWUP;
                    $data['booking_update_flag'] = 0;
                    //Reset the assigned vendor ID for this booking
                    $this->booking_model->update_booking($booking_id, array("assigned_vendor_id" => NULL));
                } else {
                    //Query to be updated to query
                    $data['booking_id'] = $booking_id;
                    log_message('info', __FUNCTION__ . " Query to be updated to Query Booking ID" . print_r($data['booking_id'], true));
                    $data['old_state'] = _247AROUND_FOLLOWUP;
                    $data['new_state'] = _247AROUND_FOLLOWUP;
                    $data['booking_update_flag'] = 1;
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
        $data['user'] = $this->user_model->get_users_by_any(array("users.phone_number" => $phone_number));
        $data['phone_number'] = $phone_number;
        $where_internal_status = array("page" => "FollowUp", "active" => '1');
        $data['follow_up_internal_status'] = $this->booking_model->get_internal_status($where_internal_status);
        $data['file_type'] = $this->booking_model->get_file_type();
        $data['is_saas'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
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
        //Get Booking Details
        $data['booking_history'] = $this->booking_model->getbooking_history($booking_id);
        if(empty($data['booking_history'][0]['booking_id'])){
            echo "Booking Not Found"; exit;
        }
        //Get Booking Symptom Details
        $data['booking_symptom'] = $this->booking_model->getBookingSymptom($booking_id);
        //Get Booking Unit Details Data
        $data['booking_unit_details'] = $this->booking_model->getunit_details($booking_id);
        //Get Partner Details Like source and partner Type
        $source = $this->partner_model->getpartner_details('bookings_sources.source, partner_type', array('bookings_sources.partner_id' => $data['booking_history'][0]['partner_id']));
        //Add source name in booking_history array
        $data['booking_history'][0]['source_name'] = !empty($source[0]['source']) ? $source[0]['source'] : "";
        //Partner ID
        $partner_id = $data['booking_history'][0]['partner_id'];
        // Request Type
        $booking_request_type = $data['booking_history'][0]['request_type'];
        // Service Id
        $service_id = $data['booking_history'][0]['service_id'];
        // Booking primary Id
        $booking_primary_id = $data['booking_history'][0]['booking_primary_id']; 
        //Define Blank Price array
        $data['prices'] = array();
        //Define Upcountory Price as zero
        $upcountry_price = 0;
        
        $unit_price_tags = array();
        $data['is_invoice_generated'] = FALSE;
        //Process booking Unit Details Data Through loop
        foreach ($data['booking_unit_details'] as $keys => $value) {
            //If partner type is OEM then get price for booking unit brands
            if (!empty($source[0]['partner_type']) && $source[0]['partner_type'] == OEM) {
                $prices = $this->booking_model->getPricesForCategoryCapacity($data['booking_history'][0]['service_id'], $data['booking_unit_details'][$keys]['category'], $data['booking_unit_details'][$keys]['capacity'], $partner_id, $value['brand']);
            } 
            //If partner type is not OEM then check is brand white list for partner if brand is white listed then use brands if not then 
            else {
                $isWbrand = "";
                $whiteListBrand = $this->partner_model->get_partner_blocklist_brand(array("partner_id" => $partner_id, "brand" => $value['brand'],"service_id" => $data['booking_history'][0]['service_id'], "whitelist" => 1), "*");
                if(!empty($whiteListBrand)){
                    $isWbrand = $value['brand'];
                }
                $prices = $this->booking_model->getPricesForCategoryCapacity($data['booking_history'][0]['service_id'], $data['booking_unit_details'][$keys]['category'], $data['booking_unit_details'][$keys]['capacity'], $partner_id, $isWbrand);
            }

                $where = array(
                    "partner_appliance_details.partner_id" => $partner_id, 
                    'partner_appliance_details.service_id' => $data['booking_history'][0]['service_id'], 
                    'partner_appliance_details.brand' => $value['brand'],
                    'partner_appliance_details.active' => 1,
                    'appliance_model_details.active'=> 1, 
                    'partner_appliance_details.active' => 1,
                    "NULLIF(model, '') IS NOT NULL" => NULL
                );
                $data['booking_unit_details'][$keys]['model_dropdown'] = $this->partner_model->get_model_number("appliance_model_details.id, appliance_model_details.model_number", $where);
            
              //Process booking Unit Details Data Through loop
            foreach ($value['quantity'] as $key => $price_tag) {
                $price_tags1 = str_replace('(Free)', '', $price_tag['price_tags']);
                $price_tags2 = str_replace('(Paid)', '', $price_tags1);
                array_push($unit_price_tags, $price_tags2);
                $service_center_data = $this->service_centers_model->get_prices_filled_by_service_center($price_tag['unit_id'], $booking_id);
                // print_r($service_center_data);
                if (!empty($service_center_data)) {
                    $data['booking_unit_details'][$keys]['quantity'][$key]['customer_paid_basic_charges'] = $service_center_data[0]['service_charge'];
                    $data['booking_unit_details'][$keys]['quantity'][$key]['customer_paid_extra_charges'] = $service_center_data[0]['additional_service_charge'];
                    if(!empty($service_center_data[0]['serial_number'])){
                        $data['booking_unit_details'][$keys]['quantity'][$key]['serial_number'] = $service_center_data[0]['serial_number'];
                    }
                    if(!empty($service_center_data[0]['serial_number_pic'])){
                        $data['booking_unit_details'][$keys]['quantity'][$key]['serial_number_pic'] = $service_center_data[0]['serial_number_pic'];                
                    }
                    $data['booking_unit_details'][$keys]['quantity'][$key]['customer_paid_parts'] = $service_center_data[0]['parts_cost'];
                    $data['booking_unit_details'][$keys]['quantity'][$key]['is_sn_correct'] = $service_center_data[0]['is_sn_correct'];
                    $data['booking_unit_details'][$keys]['quantity'][$key]['sf_purchase_date'] = $service_center_data[0]['sf_purchase_date'];
                    $data['booking_unit_details'][$keys]['quantity'][$key]['sf_purchase_invoice'] = $service_center_data[0]['sf_purchase_invoice'];
                    $data['booking_unit_details'][$keys]['model_number'] = $service_center_data[0]['model_number'];
                }
                // Searched already inserted price tag exist in the price array (get all service category)
                $id = $this->search_for_key($price_tag['price_tags'], $prices);
                // remove array key, if price tag exist into price array
                unset($prices[$id]);
                // Set Upcountry Price from the Row of $service_center_data, where upcountry_charges are filled
                if (empty($upcountry_price) && !empty($service_center_data[0]['upcountry_charges'])) {
                    $upcountry_price = $service_center_data[0]['upcountry_charges'];
                }
                
                if(!in_array($this->session->userdata('user_group'), [_247AROUND_CLOSURE, _247AROUND_ADMIN, _247AROUND_DEVELOPER]) && !empty($price_tag['partner_invoice_id']) && empty($data['is_invoice_generated']) && in_array($data['booking_history'][0]['current_status'], [_247AROUND_COMPLETED, _247AROUND_CANCELLED])) {
                    $data['is_invoice_generated'] = TRUE;
                }
            }

            array_push($data['prices'], $prices);
        }
        $isPaytmTxn = $this->paytm_payment_lib->get_paytm_transaction_data($booking_id);
        if(!empty($isPaytmTxn)){
            if($isPaytmTxn['status']){
                $data['booking_history'][0]['onlinePaymentAmount'] = $isPaytmTxn['total_amount'];
            }
        }
        
        $data['technical_problem'] = $data['technical_defect'] = array();
        
        $symptom_id = "";
        if(count($data['booking_symptom'])>0) {
            $symptom_id = ((!is_null($data['booking_symptom'][0]['symptom_id_booking_completion_time'])) ? $data['booking_symptom'][0]['symptom_id_booking_completion_time'] : $data['booking_symptom'][0]['symptom_id_booking_creation_time']);
        }
        
        if(!empty($unit_price_tags)) {
            $data['technical_problem'] = $this->booking_request_model->get_booking_request_symptom('symptom.id, symptom',
                    array('symptom.service_id' => $data['booking_history'][0]['service_id'], 'symptom.active' => 1, 'symptom.partner_id' => $partner_id), array('request_type.service_category' => $unit_price_tags));
        }
        if((count($data['technical_problem']) <= 0) || ($symptom_id == 0)) {
            $data['technical_problem'][] = array('id' => 0, 'symptom' => 'Default');
        }
        $data['c2c'] = $this->booking_utilities->check_feature_enable_or_not(CALLING_FEATURE_IS_ENABLE);
        
        if($symptom_id !== "") {
            $data['technical_defect'] = $this->booking_request_model->get_defect_of_symptom('defect_id,defect', 
                    array('symptom_id' => $symptom_id, 'partner_id' => $partner_id));
        }
        if(count($data['technical_defect'])<=0) {
            $data['technical_defect'][0] = array('defect_id' => 0, 'defect' => 'Default');
        }
        
        $data['upcountry_charges'] = $upcountry_price;
        // if booking not completed by service center then display ok consumtion parts.
        if(!empty($data['booking_history'][0]) && !empty($data['booking_history'][0]['service_center_closed_date'])) {
            $consumption_where = 'consumed_part_status_id is null';
        } else {
            $consumption_where = '(spare_parts_details.consumed_part_status_id is null or spare_parts_details.consumed_part_status_id = '.OK_PART_BUT_NOT_USED_CONSUMPTION_STATUS_ID.')';
        }
        $data['spare_parts_details'] = $this->partner_model->get_spare_parts_by_any('spare_parts_details.*, inventory_master_list.part_number', ['booking_id' => $booking_id, 'spare_parts_details.status != "'._247AROUND_CANCELLED.'"' => NULL, 'parts_shipped is not null' => NULL, $consumption_where => NULL, 'defective_part_shipped is null' => NULL], FALSE, FALSE, FALSE, ['is_inventory' => true]);        
        $data['spare_consumed_status'] = $this->reusable_model->get_search_result_data('spare_consumption_status', 'id, consumed_status,status_description,tag',['active' => 1, "tag <> '".PART_NOT_RECEIVED_TAG."'" => NULL], NULL, NULL, ['consumed_status' => SORT_ASC], NULL, NULL);
        $data['is_spare_requested'] = $this->booking_utilities->is_spare_requested($data);
        // Get review questionnaire for Admin Panel, Complete Form, Booking Service Id, Booking Request Type
        $review_questionnaire = $this->get_review_questionnaire(1, 2, $service_id, $booking_request_type);
        // get options checklist against review questions
        $questionnaire_checklist = $this->get_questionnaire_checklist($review_questionnaire);
        // get filled answers against the Booking
        $questionnaire_answers_against_booking =  $this->get_questionnaire_answers_against_booking($booking_primary_id);
        // get questionnaire HTML
        $data['questionnaire_html'] = $this->get_questionnaire_html($booking_primary_id, $review_questionnaire, $questionnaire_checklist, $questionnaire_answers_against_booking);
        $this->miscelleneous->load_nav_header(); 
        $this->load->view('employee/completebooking', $data);
    }

    /**
     * @desc: This is method return index key, if service caregory matches with given price tags
     * @param: Price tag and Array
     * @return: key
     */
    function search_for_key($price_tag, $array) {
        
        return $this->miscelleneous->search_for_pice_tag_key($price_tag, $array);
        
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
        
        $check_invoice_generated = array_column($this->reusable_model->get_search_result_data('booking_unit_details', 'partner_invoice_id', ['booking_id' => $booking_id], NULL, NULL, NULL, NULL, NULL), 'partner_invoice_id');
        if(!empty(array_filter($check_invoice_generated)) && $this->session->userdata('user_group') != _247AROUND_ADMIN) {
            $data['is_invoice_generated'] = TRUE;
        } else {
            $data['is_invoice_generated'] = FALSE;
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

        $this->form_validation->set_rules('cancellation_reason', 'Cancellation Reason', 'required');
        $this->form_validation->set_rules('partner_id', 'Partner Id', 'required');
        $validation = $this->form_validation->run();
        if ($validation) {


            if (!$agent_id) {
                $agent_id = $this->session->userdata('id');
                $agent_name = $this->session->userdata('employee_id');
            }
            $partner_id = $this->input->post('partner_id');
            $cancellation_reason = $this->input->post('cancellation_reason');
            $cancellation_text = $this->input->post("cancellation_reason_text");

            $this->miscelleneous->process_cancel_form($booking_id, $status, $cancellation_reason, $cancellation_text, $agent_id, $agent_name, $partner_id, _247AROUND);
            //get the unit details data and update the inventory stock
            $booking_details = $this->reusable_model->get_search_query('booking_details', 'booking_details.assigned_vendor_id,booking_unit_details.price_tags,booking_unit_details.appliance_capacity', array('booking_details.booking_id' => $booking_id,"booking_unit_details.price_tags like '%"._247AROUND_WALL_MOUNT__PRICE_TAG."%'" => NULL,'booking_details.assigned_vendor_id IS NOT null'=>NULL), array('booking_unit_details'=>'booking_details.booking_id = booking_unit_details.booking_id'), NULL, NULL, NULL, NULL)->result_array();
            if (!empty($booking_details)) { 
            //Calculate TAT 
            //$this->miscelleneous->process_booking_tat_on_completion($booking_id);
            //Send Push Notification
            $rmArray = $this->vendor_model->get_rm_sf_relation_by_sf_id($booking_details[0]['assigned_vendor_id']);
            $receiverArray['vendor'] = array($booking_details[0]['assigned_vendor_id']);
            $receiverArray['employee'] = array($rmArray[0]['agent_id']);
            $notificationTextArray['title'] = array("Cancel");
            $notificationTextArray['msg'] = array($booking_id,"Cancel");
            $this->push_notification_lib->create_and_send_push_notiifcation(BOOKING_UPDATED_BY_247AROUND,$receiverArray,$notificationTextArray);
            //End Push Notification
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

        if (!empty($getbooking[0]['booking_id'])) {
             $spare_shipped_flag = FALSE;
               if (isset($getbooking['spare_parts'])) {

                    foreach ($getbooking['spare_parts'] as $sp) {
                        
                        if(($sp['auto_acknowledeged'] == 1 || $sp['auto_acknowledeged'] == 2)&& $sp['status'] == SPARE_DELIVERED_TO_SF ){
                            $spare_shipped_flag = TRUE;
                            $getbooking['spare_shipped_flag']=$spare_shipped_flag;
                        }
                    }
               }
            $c2c = $this->booking_utilities->check_feature_enable_or_not(CALLING_FEATURE_IS_ENABLE);
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/reschedulebooking', array('data' => $getbooking, 'c2c' => $c2c));
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
        $is_booking_able_to_reschedule = $this->booking_creation_lib->is_booking_able_to_reschedule($booking_id, $this->input->post('service_center_closed_date'));
        if ($is_booking_able_to_reschedule !== FALSE) {
            $data['booking_date'] = date('Y-m-d', strtotime($this->input->post('booking_date')));
            $data['booking_timeslot'] = $this->input->post('booking_timeslot');
            $data['service_center_closed_date'] = NULL;
            //$data['cancellation_reason'] = NULL;
            //$data['booking_remarks'] = $this->input->post('reason');
            $data['current_status'] = 'Rescheduled';
    //        $data['internal_status'] = 'Rescheduled';
            $data['update_date'] = date("Y-m-d H:i:s");
            $reason=!empty($this->input->post('reason'))?$this->input->post('reason'):'';
            $reason_remark=!empty($this->input->post('remark'))?$this->input->post('remark'):'';
            $data['reschedule_reason']=$reason.' - '.$reason_remark;
            //check partner status
            //$partner_id = $this->input->post('partner_id');
            $actor = $next_action = NULL;
    //        $partner_status = $this->booking_utilities->get_partner_status_mapping_data($data['current_status'], $data['internal_status'], $partner_id, $booking_id);
    //        if (!empty($partner_status)) {
    //            $data['partner_current_status'] = $partner_status[0];
    //            $data['partner_internal_status'] = $partner_status[1];
    //            $actor = $data['actor'] = $partner_status[2];
    //            $next_action =$data['next_action'] = $partner_status[3];
    //        }

            if ($data['booking_timeslot'] == "Select") {
                echo "Please Select Booking Timeslot.";
            } else {
                log_message('info', __FUNCTION__ . " Update booking  " . print_r($data, true));
                $this->booking_model->update_booking($booking_id, $data);
                $this->booking_model->increase_escalation_reschedule($booking_id, "count_reschedule");
                $reschedule_reason=$reason.' - '.$reason_remark;
                //Log this state change as well for this booking
                //param:-- booking id, new state, old state, employee id, employee name
                $this->notify->insert_state_change($booking_id, _247AROUND_RESCHEDULED, _247AROUND_PENDING,$reschedule_reason, $this->session->userdata('id'), 
                        $this->session->userdata('employee_id'),$actor,$next_action, _247AROUND);

                $service_center_data['internal_status'] = _247AROUND_PENDING;
                $service_center_data['current_status'] = _247AROUND_PENDING;
                $service_center_data['update_date'] = date("Y-m-d H:i:s");


                log_message('info', __FUNCTION__ . " Booking Id " . $booking_id . " Update Service center action table  " . print_r($service_center_data, true));

                $this->vendor_model->update_service_center_action($booking_id, $service_center_data);

                $send_data['booking_id'] = $booking_id;
                $send_data['state'] = "Rescheduled";
                $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
                $this->asynchronous_lib->do_background_process($url, $send_data);

                log_message('info', __FUNCTION__ . " Request to prepare Job Card  " . print_r($booking_id, true));

                $job_card = array();
                $job_card_url = base_url() . "employee/bookingjobcard/prepare_job_card_using_booking_id/" . $booking_id;
                $this->asynchronous_lib->do_background_process($job_card_url, $job_card);

                $email = array();
                $email_url = base_url() . "employee/bookingjobcard/send_mail_to_vendor/" . $booking_id;
                $this->asynchronous_lib->do_background_process($email_url, $email);

                log_message('info', __FUNCTION__ . " Partner Callback  " . print_r($booking_id, true));

                // Partner Call back
                $this->partner_cb->partner_callback($booking_id);
                log_message('info', 'Rescheduled- Booking id: ' . $booking_id . " Rescheduled By " . $this->session->userdata('employee_id') . " data " . print_r($data, true));
                 if($this->session->userdata('user_group') == PARTNER_CALL_CENTER_USER_GROUP){
                    redirect(base_url() . 'partner/dashboard');
                }
                else{
                   redirect(base_url() . DEFAULT_SEARCH_PAGE);
                }
            }
        } else {
            $this->session->set_userdata(['error' => BOOKING_RESCHEDULE_ERROR_MSG]);
            $this->get_reschedule_booking_form($booking_id);
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
     * @desc: This is used to get appliance list its called by Ajax
     */
    function get_appliances($selected_service_id) {
        $partner_id = $this->input->post('partner_id');
        $partner_details = $this->partner_model->getpartner_details("partners.id, public_name, "
                . "postpaid_credit_period, is_active, postpaid_notification_limit, postpaid_grace_period, is_prepaid,partner_type, "
                . "invoice_email_to,invoice_email_cc", array('partners.id' => $partner_id));
        
        $prepaid['active'] = true;
        $prepaid['is_notification'] = false;
        
        $data = array();
        
        if(!empty($partner_details)) {
            if($partner_details[0]['is_prepaid'] == 1){
                $prepaid = $this->miscelleneous->get_partner_prepaid_amount($partner_id);
            } else  if($partner_details[0]['is_prepaid'] == 0){

                $prepaid = $this->invoice_lib->get_postpaid_partner_outstanding($partner_details[0]);
            }
            
            if ($partner_details[0]['partner_type'] == OEM) {
                $services = $this->partner_model->get_partner_specific_services($partner_id);
            } else {
                $services = $this->booking_model->selectservice();
            }
            
            $data['partner_type'] = $partner_details[0]['partner_type'];
            $data['partner_id'] = $partner_id;
            $data['active'] = $prepaid['active'];
            if($prepaid['is_notification']){
                $data['prepaid_msg'] = PREPAID_LOW_AMOUNT_MSG_FOR_ADMIN;

            } else {
                $data['prepaid_msg'] = "";
            }
            $data['services'] = "<option selected disabled value=''>Select Product</option>";
            foreach ($services as $appliance) {
                $data['services'] .= "<option ";
                if ($selected_service_id == $appliance->id) {
                    $data['services'] .= " selected ";
                } else if (count($services) == 1) {
                    $data['services'] .= " selected ";
                }
                $data['services'] .=" value='" . $appliance->id . "'>$appliance->services</option>";
            }
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
            $isWbrand = "";
            $whiteListBrand = $this->partner_model->get_partner_blocklist_brand(array("partner_id" => $partner_id, "brand" => $brand,
            "service_id" => $service_id, "whitelist" => 1), "*");
            if(!empty($whiteListBrand)){
                $whiteListBrand = $brand;
            }
            $result = $this->booking_model->getCategoryForService($service_id, $partner_id, $isWbrand);
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
            $isWbrand = "";
            $whiteListBrand = $this->partner_model->get_partner_blocklist_brand(array("partner_id" => $partner_id, "brand" => $brand,
            "service_id" => $service_id, "whitelist" => 1), "*");
            if(!empty($whiteListBrand)){
                $isWbrand = $brand;
            }
            $result = $this->booking_model->getCapacityForCategory($service_id, $category, $isWbrand, $partner_id);
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
        $add_booking = NULL;
        $is_repeat = 0;
        $booking_id = "";
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
        $phone_number = trim($this->input->post('phone_number'));
        $is_saas = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $is_sf_panel = $this->input->post('is_sf_panel');
        $model_number = $this->input->post('model_number');
        $unit_details = array();
        $booking_details = array(); 
        if(!empty($this->input->post('booking_id'))){
            $booking_id = $this->input->post('booking_id');
            $unit_details = $this->booking_model->get_unit_details(['booking_id' => $booking_id, 'booking_status <> "Cancelled"' => NULL]);
            $booking_details = $this->booking_model->get_booking_details('*', ['booking_id' => $booking_id]);
        }
        // Array of price tags selected against booking
        $arr_selected_price_tags = [];
        if(!empty($this->input->post('selected_price_tags'))){
            $selected_price_tags = $this->input->post('selected_price_tags');
            $arr_selected_price_tags = explode(",", $selected_price_tags);
        }
        // Get saved Partner Discounts against Booking
        $arr_partner_discount = "";
        if(!empty($this->input->post('arr_partner_discount'))){
            $arr_partner_discount = $this->input->post('arr_partner_discount');
            $arr_partner_discount = json_decode($arr_partner_discount, true);
        }
        // Get saved 247around Discounts against Booking
        $arr_247around_discount = "";
        if(!empty($this->input->post('arr_247around_discount'))){
            $arr_247around_discount = $this->input->post('arr_247around_discount');
            $arr_247around_discount = json_decode($arr_247around_discount, true);
        }
        if($this->input->post('add_booking')){
            $add_booking = $this->input->post('add_booking');
        }
        if($this->input->post('is_repeat')){
            $is_repeat = $this->input->post('is_repeat');
        }
        if (empty($assigned_vendor_id)) {
            $assigned_vendor_id = FALSE;
        }
        $this->initialized_variable->fetch_partner_data($partner_id);
        
        $partner_data = $this->initialized_variable->get_partner_data();

        if ($partner_type == OEM) {
            $result = $this->booking_model->getPricesForCategoryCapacity($service_id, $category, $capacity, $partner_id, $brand,$add_booking);
        } else {
            $isWbrand = "";
            $whiteListBrand = $this->partner_model->get_partner_blocklist_brand(array("partner_id" => $partner_id, "brand" => $brand,
            "service_id" => $service_id, "whitelist" => 1), "*");
            if(!empty($whiteListBrand)){
                 $isWbrand = $brand;
                 
            } 
             
            $result = $this->booking_model->getPricesForCategoryCapacity($service_id, $category, $capacity, $partner_id, $isWbrand,$add_booking);
            
        }

        $where = array('service_id' => $service_id, 'brand_name' => $brand);
        $brand_id_array = $this->booking_model->get_brand($where);

        if (!empty($brand_id_array)) {
            $brand_id = $brand_id_array[0]['id'];
        } else {
            $brand_id = "";
        }

        if (!empty($result)) {
            if($is_sf_panel)
            {
                $html = "<thead><tr><th>Service Category</th><th style='display:none;'>Std. Charges</th><th>Customer Net Payable</th><th>Selected Services</th></tr></thead>";
            }
            elseif(!$is_saas){
                $html = "<thead><tr><th>Service Category</th><th>Std. Charges</th><th>Partner Discount</th><th>Final Charges</th><th>247around Discount</th><th>Selected Services</th></tr></thead>";
            }
            else{
                $html = "<thead><tr><th>Service Category</th><th>Std. Charges</th><th>Partner Discount</th><th>Final Charges</th><th>Selected Services</th></tr></thead>";
            }
            $i = 0;

            foreach ($result as $prices) {
                $checkboxClass = (($prices['service_category'] == REPEAT_BOOKING_TAG) ? "repeat_".$prices['product_or_services'] : $prices['product_or_services']);
                $html .="<tr><td>" . $prices['service_category'] . "</td>";
                // Prices should remain same if service category is not changed in case of update booking
                $ct = $prices['customer_total'];    
                $partner_net_payable = $prices['partner_net_payable']; 
                $customer_net_payable = $prices['customer_net_payable'];
                $around_net_payable = $prices['around_net_payable'];
                $is_partner_invoiced = '';
                if(isset($unit_details)){
                    foreach ($unit_details as  $tags) {
                        if(($tags['price_tags'] == $prices['service_category']) && ($tags['sf_model_number'] == $model_number)){
                            $ct = $tags['customer_total'];
                            $partner_net_payable = $tags['partner_net_payable'];
                            $customer_net_payable = $tags['customer_net_payable'];
                            $around_net_payable = $tags['around_net_payable']; 
                            $is_partner_invoiced = $tags['partner_invoice_id']; ;
                        }
                    }
                }
                
                if(!$is_sf_panel)
                {
                    $html .= "<td>" . $ct . "</td>";
                    $html .= "<td><input  type='text' class='form-control partner_discount' name= 'partner_paid_basic_charges[$brand_id][$clone_number][" . $prices['id'] . "][]'  id='partner_paid_basic_charges_" . $i . "_" . $clone_number . "' value = '" . $partner_net_payable . "' placeholder='Enter discount' readonly onblur='chkPrice($(this),". $ct.")'/></td>";
                    $html .= "<td>" . $customer_net_payable . "</td>";
                }
                else
                {
                    // Subtract Partner Discount and 247around discount from customer net Payable for selected service categories
                    $html .= "<td style='display:none;'>" . $ct . "</td>";
                    $html .= "<td>" . $customer_net_payable . "<input  type='hidden' class='form-control partner_discount' name= 'partner_paid_basic_charges[$brand_id][$clone_number][" . $prices['id'] . "][]'  id='partner_paid_basic_charges_" . $i . "_" . $clone_number . "' value = '" . $partner_net_payable . "' placeholder='Enter discount' readonly onblur='chkPrice($(this),". $ct.")'/><input  type='hidden' class='form-control discount' name= 'discount[$brand_id][$clone_number][" . $prices['id'] . "][]'  id='discount_" . $i . "_" . $clone_number . "' value = '". $around_net_payable."' placeholder='Enter discount' readonly></td>";
                        }                        
                
                if(!$is_sf_panel)
                {
                    if(!$is_saas){
                        $html .= "<td><input  type='text' class='form-control discount' name= 'discount[$brand_id][$clone_number][" . $prices['id'] . "][]'  id='discount_" . $i . "_" . $clone_number . "' value = '". $around_net_payable ."' placeholder='Enter discount' readonly onblur='chkPrice($(this),". $ct.")'></td>";
                    }
                    else{
                        $html .= "<td><input  type='hidden' class='form-control discount' name= 'discount[$brand_id][$clone_number][" . $prices['id'] . "][]'  id='discount_" . $i . "_" . $clone_number . "' value = '". $around_net_payable ."' placeholder='Enter discount' readonly></td>";
                    }
                }
                $html .= "<td><input type='hidden'name ='is_up_val'  data-customer_price = '".$prices['upcountry_customer_price']."' data-flat_upcountry = '".$prices['flat_upcountry']."' id='is_up_val_" . $i . "_" . $clone_number . "' value ='" . $prices['is_upcountry'] . "' /><input class='price_checkbox check_is_amc $checkboxClass'";
                if($is_repeat) {
                    if($prices['service_category'] == REPEAT_BOOKING_TAG) {
                        $html .= " checked ";
                    }
                    $html .= " style= 'pointer-events: none;'";
                }
                $html .=" type='checkbox' id='checkbox_" . $i . "_" . $clone_number . "'";
                if($prices['service_category'] == REPAIR_OOW_PARTS_PRICE_TAGS ){
                    $html .= " onclick='return false;' ";
                }
                $html .= " name='prices[$brand_id][$clone_number][]' tabindex='-1' ";
                
                // auto check price-tags that are stored in booking_unit_details table
                if(in_array($prices['service_category'], $arr_selected_price_tags)){
                    $html .= " checked ";
                    if(!empty($is_partner_invoiced)){
                        $html .= " style = 'pointer-events: none;'";
                    }
                }
                if($prices['service_category'] == REPEAT_BOOKING_TAG){
                    $html .= " onclick='check_booking_request(), final_price(), get_symptom(), enable_discount(this.id), set_upcountry(), check_service_category(this.id)' value='" . $prices['id'] . "_" . intval($ct) . "_" . $i . "_" . $clone_number."' data-toggle='modal' data-target='#repeat_booking_model' data-price_tag='".$prices['service_category']."'  data-pod='".$prices['pod']."'  readonly></td><tr>";
                }
                else{
                    $html .= " onclick='check_booking_request(), final_price(), get_symptom(), enable_discount(this.id), set_upcountry(), check_service_category(this.id)' value='" . $prices['id'] . "_" . intval($ct) . "_" . $i . "_" . $clone_number."' data-price_tag='".$prices['service_category']."'  data-pod='".$prices['pod']."' ></td><tr>";
                }
                $i++;
            }
            $data['price_table'] = $html;
            if (empty($assigned_vendor_id)) {
                $upcountry_data = $this->miscelleneous->check_upcountry_vendor_availability($booking_city, $booking_pincode, $service_id, $partner_data, $assigned_vendor_id, $brand);
            } else {

                $vendor_data = array();
                $vendor_data[0]['vendor_id'] = $assigned_vendor_id;
                $vendor_data[0]['city'] = $this->vendor_model->get_distict_details_from_india_pincode($booking_pincode)['district'];
                $min_upcountry_distance = $this->vendor_model->getVendorDetails("min_upcountry_distance", 
                        array('id' =>$assigned_vendor_id));
                if(!empty($min_upcountry_distance)) {
                    $vendor_data[0]['min_upcountry_distance'] = $min_upcountry_distance[0]['min_upcountry_distance'];
                }
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
            $c2c = $this->booking_utilities->check_feature_enable_or_not(CALLING_FEATURE_IS_ENABLE);
            // fetch disatisfactory reasons in case of Poor rating
            $poor_rating_dissatisfactory_reasons = $this->booking_model->get_dissatisfactory_reasons();
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/rating', array('data' => $getbooking, 'status' => $status, 'c2c' => $c2c, 'dissatisfactory_reasons' => $poor_rating_dissatisfactory_reasons));
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
        log_message('info', __FUNCTION__ . ' Received Data : '  . print_r($this->input->post(),true));
        if($this->input->post('mobile_no')){
            $user_id = $this->input->post('user_id');
            $phone_no = $this->input->post('mobile_no');
            log_message('info', __FUNCTION__ . ' Booking ID : ' . $booking_id . ' Status' . $status . " Done By " . $this->session->userdata('employee_id'));
            if($this->input->post('not_reachable')){
                $this->customer_not_reachable_for_rating($booking_id,$user_id,$phone_no);
            }
            else{
                if ($this->input->post('rating_star') != "Select") {
                    $data['rating_stars'] = $this->input->post('rating_star');
                    $data['rating_comments'] = $this->input->post('rating_comments');
                    // save dissatisfactory reason in booking_details in case of bad rating
                    $dissatisfactory_reason = "";
                    if(!empty($this->input->post('dissatisfactory_reason'))){
                        $dissatisfactory_reason = $this->input->post('dissatisfactory_reason');
                    }
                    $data['customer_dissatisfactory_reason'] = $dissatisfactory_reason;
                    $remarks = 'Rating'.':'.$data['rating_stars'].'. '.$data['rating_comments'];

                    $update = $this->booking_model->update_booking($booking_id, $data);
                    if($data['rating_stars']<3){
                        $this->miscelleneous->send_bad_rating_email($data['rating_stars'],$booking_id);
                    }
                    if ($update) {
                        //update state
                        $this->notify->insert_state_change($booking_id, RATING_NEW_STATE, $status, $remarks, $this->session->userdata('id'), $this->session->userdata('employee_id'),
                                ACTOR_BOOKING_RATING,RATING_NEXT_ACTION,_247AROUND);
                        //if 'do not send sms check then does not send sms'
                        if(!$this->input->post('not_send_sms'))
                            {
                            // send sms after rating
                               $this->send_rating_sms($phone_no, $data['rating_stars'],$user_id,$booking_id);
                            }
                    }
                }
            }
        }
        else{
           $this->session->set_userdata(array('rating_error' => "Rating Not submitted for ".$booking_id." Please Try Again"));
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
     *  @desc : This function is to view details of any particular booking.
     *
     *  We get all the details like User's details, booking details, and also the appliance's unit details.
     *
     *  @param : booking id
     *  @return : booking details and load view
     */
    function viewdetails($booking_id = null) {
        if (empty($booking_id)) {
            // This is the case when user hits view details URL directly from the browser, without entering any Booking Id
            // Manual Error
            return;
        }

        $data['booking_history'] = $this->booking_model->getbooking_filter_service_center($booking_id);
        $data['booking_symptom'] = $this->booking_model->getBookingSymptom($booking_id);
        $data['file_type'] = $this->booking_model->get_file_type();
        $data['booking_files'] = $this->booking_model->get_booking_files(array('booking_id' => $booking_id));
        if (!empty($data['booking_history'])) {
            if (empty($data['booking_history'][0]['assigned_vendor_id']) && ($data['booking_history'][0]['type'] == 'Booking') && ($data['booking_history'][0]['is_upcountry'] == '1')) {
                /* getting symptom */
                $arr = array('is_inventory' => 1, 'is_original_inventory' => 1, 'spare_cancel_reason' => 1,'symptom'=>1);
                $query1 = $this->partner_model->get_spare_parts_by_any('spare_parts_details.*,symptom.symptom as symptom_text,inventory_master_list.part_number,inventory_master_list.part_name as final_spare_parts,im.part_number as shipped_part_number,original_im.part_name as original_parts,original_im.part_number as original_parts_number, booking_cancellation_reasons.reason as part_cancel_reason,  IF(sc.name !="" ,sc.name, "Partner") AS send_defective_to, oow_spare_invoice_details.invoice_id as oow_invoice_id, oow_spare_invoice_details.invoice_date as oow_invoice_date, oow_spare_invoice_details.hsn_code as oow_hsn_code, oow_spare_invoice_details.gst_rate as oow_gst_rate, oow_spare_invoice_details.invoice_amount as oow_incoming_invoice_amount, oow_spare_invoice_details.invoice_pdf as oow_incoming_invoice_pdf,ccid.box_count as sf_box_count,ccid.billable_weight as sf_billable_weight,cc_invoice_details.box_count as wh_box_count,cc_invoice_details.billable_weight as wh_billable_weight, cci_details.box_count as p_box_count, cci_details.billable_weight as p_billable_weight, booking_details.partner_id as booking_partner_id', array('spare_parts_details.booking_id' => $booking_id), true, false, false, $arr, TRUE, TRUE,TRUE,TRUE,TRUE);
                if (!empty($query1)) {
                    $data['booking_history']['spare_parts'] = $query1;
                }
            }
            if (!empty($data['booking_history']['spare_parts'])) {
                $query1 = $data['booking_history']['spare_parts'];

                foreach ($query1 as $key1 => $sp) {

                    $query1[$key1]['btn'] = '';
                    if (!empty($data['booking_history'][0]["service_center_closed_date"])) {
                        if ($this->session->userdata('user_group') == "inventory_manager" || $this->session->userdata('user_group') == "admin" || $this->session->userdata('user_group') == "developer" || $this->session->userdata('user_group') == "accountmanager" || $this->session->userdata('user_group') == "accountant") {
                            if ($sp["defective_part_required"] == '0') {
                                $required_parts = 'REQUIRED_PARTS';
                                $text = '<i class="glyphicon glyphicon-ok-circle" style="font-size: 16px;"></i>';
                                $cl = "btn-primary";
                            } else {
                                $text = '<i class="glyphicon glyphicon-ban-circle" style="font-size: 16px;"></i>';
                                $required_parts = 'NOT_REQUIRED_PARTS_FOR_COMPLETED_BOOKING';
                                $cl = "btn-danger";
                            }

                            if (isset($sp['is_consumed']) && $sp['is_consumed'] != 1 && $required_parts == 'NOT_REQUIRED_PARTS_FOR_COMPLETED_BOOKING') {
                                if (empty($sp['reverse_purchase_invoice_id'])) {
                                    $query1[$key1]['btn'] = '<button type="button" disabled data-booking_id="' . $sp["booking_id"] . '" data-url="' . base_url() . 'employee/inventory/update_action_on_spare_parts/' . $sp["id"] . '/' . $sp["booking_id"] . '/' . $required_parts . '" class="btn btn-sm ' . $cl . ' open-adminremarks" data-toggle="modal" data-target="#myModal2">' . $text . '</button>';
                                } else {
                                    $query1[$key1]['btn'] = '<button type="button" disabled data-booking_id="' . $sp["booking_id"] . '" class="btn btn-sm ' . $cl . ' open-adminremarks">' . $text . '</button>';
                                }
                            } else {
                                if (empty($sp['reverse_purchase_invoice_id'])) {
                                    $query1[$key1]['btn'] = '<button type="button" data-booking_id="' . $sp["booking_id"] . '" data-url="' . base_url() . 'employee/inventory/update_action_on_spare_parts/' . $sp["id"] . '/' . $sp["booking_id"] . '/' . $required_parts . '" class="btn btn-sm ' . $cl . ' open-adminremarks" data-toggle="modal" data-target="#myModal2">' . $text . '</button>';
                                } else {
                                    $query1[$key1]['btn'] = '<button type="button" disabled data-booking_id="' . $sp["booking_id"] . '" class="btn btn-sm ' . $cl . ' open-adminremarks" data-toggle="modal">' . $text . '</button>';
                                }
                            }
                        }
                    }
                    $query1[$key1]['awb_by_wh_pod'] = '';
                    $query1[$key1]['awb_by_sf_pod'] = '';
                    $awb_by_wh =  $query1[$key1]['awb_by_wh'];
                    $awb_by_sf =  $query1[$key1]['awb_by_sf'];
                    if(!empty($awb_by_wh)){
                        $awb_by_wh_pod = $this->inventory_model->get_courier_company_invoice_details('courier_pod_file',array('awb_number'=>$awb_by_wh));
                        if(!empty($awb_by_wh_pod)){
                            $query1[$key1]['awb_by_wh_pod'] = $awb_by_wh_pod[0]['courier_pod_file'];
                        }
                    }
                    if(!empty($awb_by_sf)){
                        $awb_by_sf_pod = $this->inventory_model->get_courier_company_invoice_details('courier_pod_file',array('awb_number'=>$awb_by_sf));
                        if(!empty($awb_by_sf_pod)){
                            $query1[$key1]['awb_by_sf_pod'] = $awb_by_sf_pod[0]['courier_pod_file'];
                        }
                    }
                }
                $data['booking_history']['spare_parts'] = $query1;
            }
            $engineer_action_not_exit = false;
            $unit_where = array('booking_id' => $booking_id);
            $booking_unit_details = $this->booking_model->get_unit_details($unit_where);
            $data['penalty'] = $this->penalty_model->get_penalty_on_booking_by_booking_id($booking_id);
            if (!is_null($data['booking_history'][0]['sub_vendor_id'])) {
                $data['dhq'] = $this->upcountry_model->get_sub_service_center_details(array('id' => $data['booking_history'][0]['sub_vendor_id']));
            }

            foreach ($booking_unit_details as $key1 => $b) {

                $unitWhere = array("engineer_booking_action.booking_id" => $booking_id,
                    "engineer_booking_action.unit_details_id" => $b['id'], "service_center_id" => $data['booking_history'][0]['assigned_vendor_id']);
                $en = $this->engineer_model->getengineer_action_data("engineer_booking_action.*", $unitWhere);
                if (!empty($en)) {
                    $booking_unit_details[$key1]['en_serial_number'] = $en[0]['serial_number'];
                    $booking_unit_details[$key1]['en_serial_number_pic'] = $en[0]['serial_number_pic'];
                    $booking_unit_details[$key1]['en_is_broken'] = $en[0]['is_broken'];
                    $booking_unit_details[$key1]['en_internal_status'] = $en[0]['internal_status'];
                    $booking_unit_details[$key1]['en_current_status'] = $en[0]['current_status'];
                    $booking_unit_details[$key1]['en_amount_paid'] = $en[0]['amount_paid'];

                    $engineer_action_not_exit = true;
                }
                // print_r($service_center_data);
                $service_center_data = $this->service_centers_model->get_prices_filled_by_service_center($b['id'], $booking_id);

                if (!empty($service_center_data)) {
                    $booking_unit_details[$key1]['scba_booking_id'] = $service_center_data[0]['booking_id'];
                    $booking_unit_details[$key1]['scba_basic_charges'] = $service_center_data[0]['service_charge'];
                    $booking_unit_details[$key1]['scba_additional_charges'] = $service_center_data[0]['additional_service_charge'];
                    $booking_unit_details[$key1]['scba_serial_number'] = $service_center_data[0]['serial_number'];
                    $booking_unit_details[$key1]['scba_parts_cost'] = $service_center_data[0]['parts_cost'];
                    $booking_unit_details[$key1]['scba_serial_number_pic'] = $service_center_data[0]['serial_number_pic'];
                    $booking_unit_details[$key1]['scba_sf_purchase_date'] = $this->miscelleneous->get_formatted_date($service_center_data[0]['sf_purchase_date']);
                    $booking_unit_details[$key1]['scba_sf_closed_date'] = $this->miscelleneous->get_formatted_date($service_center_data[0]['closed_date'], true);
                    $booking_unit_details[$key1]['scba_sf_purchase_invoice'] = $service_center_data[0]['sf_purchase_invoice'];
                    $booking_unit_details[$key1]['scba_upcountry_charges'] = $service_center_data[0]['upcountry_charges'];
                    $booking_unit_details[$key1]['scba_model_number'] = $service_center_data[0]['model_number'];
                }
            }
            if (isset($engineer_action_not_exit)) {
                $sig_table = $this->engineer_model->getengineer_sign_table_data("*", array("booking_id" => $booking_id,
                    "service_center_id" => $data['booking_history'][0]['assigned_vendor_id']));
                $data['signature_details'] = $sig_table;
            }
            //get engineer name
            if ($data['booking_history'][0]['assigned_engineer_id']) {
                $engineer_name = $this->engineer_model->get_engineers_details(array("id" => $data['booking_history'][0]['assigned_engineer_id']), "name,phone");
                if (!empty($engineer_name)) {
                    $data['booking_history'][0]['assigned_engineer_name'] = $engineer_name[0]['name'];
                    $data['booking_history'][0]['engineer_phone'] = $engineer_name[0]['phone']; // Engineer Phone //
                }
            }

            $data['engineer_action_not_exit'] = $engineer_action_not_exit;

            $data['unit_details'] = $booking_unit_details;
            $response_db = $this->booking_utilities->getBookingCovidZoneAndContZone($data['booking_history'][0]['district']);
            
            if(isset($response_db[0]['zone']) && !empty($response_db[0]['zone'])){
            $response = json_decode($response_db[0]['zone'],true);
            if(!empty($response)){    
            $districtZoneType = $response['zone'];
            $data['booking_history'][0]['zone'] = $districtZoneType;
            }else{
            $districtZoneType = '-'; 
            $data['booking_history'][0]['zone'] = $districtZoneType;  
            }
            }
            $isPaytmTxn = $this->paytm_payment_lib->get_paytm_transaction_data($booking_id);

            if (!empty($isPaytmTxn)) {
                if ($isPaytmTxn['status']) {
                    $data['booking_history'][0]['onlinePaymentAmount'] = $isPaytmTxn['total_amount'];
                    $data['booking_history'][0]['channels'] = implode(", ", $isPaytmTxn['channels']);
                }
            }
            if (!empty($data['booking_history'][0]['dealer_id'])) {
                $dealer_detail = $this->dealer_model->get_dealer_details('dealer_name, dealer_phone_number_1', array('dealer_id' => $data['booking_history'][0]['dealer_id']));
                if (!empty($dealer_detail)) {
                    $data['booking_history'][0]['dealer_name'] = $dealer_detail[0]['dealer_name'];
                    $data['booking_history'][0]['dealer_phone_number_1'] = $dealer_detail[0]['dealer_phone_number_1'];
                } else {
                    $data['booking_history'][0]['dealer_name'] = 'Not Available';
                    $data['booking_history'][0]['dealer_phone_number_1'] = 'Not Avaialble';
                }
            }
            $account_manager = $this->booking_model->get_am_by_booking($booking_id, "employee.full_name");
            if (!empty($account_manager)) {
                $data['booking_history'][0]['account_manager_name'] = $account_manager[0]['full_name'];
            }
        } else {
            $data['booking_history'] = array();
        }
        $data['paytm_transaction'] = $this->paytm_payment_model->get_paytm_transaction_and_cashback($booking_id);
        $data['cashback_rules'] = $this->paytm_payment_model->get_paytm_cashback_rules(array("active" => 1, "tag" => PAYTM_CASHBACK_TAG));
        $data['symptom'] = array();
        $data['completion_symptom'] = array();
        $data['technical_solution'] = array();
        $data['technical_defect'] = array();
        if (count($data['booking_symptom']) > 0) {
            if (!is_null($data['booking_symptom'][0]['symptom_id_booking_creation_time'])) {
                $data['symptom'] = $this->booking_request_model->get_booking_request_symptom('symptom', array('symptom.id' => $data['booking_symptom'][0]['symptom_id_booking_creation_time']));

                if (count($data['symptom']) <= 0) {
                    $data['symptom'][0] = array("symptom" => "Default");
                }
            }

            if (!is_null($data['booking_symptom'][0]['symptom_id_booking_completion_time'])) {
                $data['completion_symptom'] = $this->booking_request_model->get_booking_request_symptom('symptom', array('symptom.id' => $data['booking_symptom'][0]['symptom_id_booking_completion_time']));

                if (count($data['completion_symptom']) <= 0) {
                    $data['completion_symptom'][0] = array("symptom" => "Default");
                }
            }
            if (!is_null($data['booking_symptom'][0]['defect_id_completion'])) {
                $cond['where'] = array('defect.id' => $data['booking_symptom'][0]['defect_id_completion']);
                $data['technical_defect'] = $this->booking_request_model->get_defects('defect', $cond);

                if (count($data['technical_defect']) <= 0) {
                    $data['technical_defect'][0] = array("defect" => "Default");
                }
            }
            if (!is_null($data['booking_symptom'][0]['solution_id'])) {
                $data['technical_solution'] = $this->booking_request_model->symptom_completion_solution('technical_solution', array('symptom_completion_solution.id' => $data['booking_symptom'][0]['solution_id']));

                if (count($data['technical_solution']) <= 0) {
                    $data['technical_solution'][0] = array("technical_solution" => "Default");
                }
            }
        } else {
            $data['symptom'][0] = array("symptom" => "Default");

            if (!empty($data['booking_history'][0]['internal_status']) && in_array($data['booking_history'][0]['internal_status'], array(SF_BOOKING_COMPLETE_STATUS, _247AROUND_COMPLETED))) {
                $data['completion_symptom'][0] = array("symptom" => "Default");
                $data['technical_defect'][0] = array("defect" => "Default");
                $data['technical_solution'][0] = array("technical_solution" => "Default");
            }
        }

        // fetch customer dissatisfactory reason saved against Booking
        $data['customer_dissatisfactory_reason'] = "";
        if(!empty($data['booking_history'][0]['customer_dissatisfactory_reason'])){
            $arr_where = ['id' => $data['booking_history'][0]['customer_dissatisfactory_reason']];
            $arr_dissatisfactory_reason = $this->booking_model->get_dissatisfactory_reasons($arr_where);
            if(!empty($arr_dissatisfactory_reason[0]['reason'])){
                $data['customer_dissatisfactory_reason'] = $arr_dissatisfactory_reason[0]['reason'];
            }
        }
        
            
//        if (!empty($data['booking_history']['spare_parts'])) {
//            $spare_parts_list = array();
//            foreach ($data['booking_history']['spare_parts'] as $key => $val) {
//                if (!empty($val['requested_inventory_id'])) {
//                    $inventory_spare_parts_details = $this->inventory_model->get_generic_table_details('inventory_master_list', 'inventory_master_list.part_number,inventory_master_list.part_name', array('inventory_master_list.inventory_id' => $val['requested_inventory_id']), array());
//                    if (!empty($inventory_spare_parts_details)) {
//                        $spare_parts_list[] = array_merge($val, array('final_spare_parts' => $inventory_spare_parts_details[0]['part_name']));
//                    }
//                }
//            }
//        }
//
//        if (!empty($spare_parts_list)) {
//            $data['booking_history']['spare_parts'] = $spare_parts_list;
//        }
/*
        $select = "courier_company_invoice_details.id, courier_company_invoice_details.awb_number, courier_company_invoice_details.company_name, courier_company_invoice_details.courier_charge, courier_company_invoice_details.billable_weight, courier_company_invoice_details.actual_weight, courier_company_invoice_details.create_date, courier_company_invoice_details.update_date, courier_company_invoice_details.partner_id, courier_company_invoice_details.basic_billed_charge_to_partner, courier_company_invoice_details.partner_invoice_id, courier_company_invoice_details.booking_id, courier_company_invoice_details.box_count, courier_company_invoice_details.courier_invoice_file, courier_company_invoice_details.shippment_date, courier_company_invoice_details.created_by, courier_company_invoice_details.is_exist";
        $spare_parts_details = $this->partner_model->get_spare_parts_by_any('spare_parts_details.awb_by_sf', array('spare_parts_details.booking_id' => $booking_id, 'spare_parts_details.awb_by_sf !=' => ''));
        if (!empty($spare_parts_details)) {
            $awb = $spare_parts_details[0]['awb_by_sf'];
            $courier_boxes_weight = $this->inventory_model->get_generic_table_details('courier_company_invoice_details', $select, array('awb_number' => $awb), array());
            if (!empty($courier_boxes_weight)) {
                $data['courier_boxes_weight_details'] = $courier_boxes_weight[0];
            }
        }

        $spare_parts_list = $this->partner_model->get_spare_parts_by_any('spare_parts_details.awb_by_wh', array('spare_parts_details.booking_id' => $booking_id, "spare_parts_details.awb_by_wh IS NOT NULL " => NULL));
        if (!empty($spare_parts_list)) {
            $courier_boxes_weight_wh = $this->inventory_model->get_generic_table_details('courier_company_invoice_details', $select, array('awb_number' => $spare_parts_list[0]['awb_by_wh']), array());
            if (!empty($courier_boxes_weight_wh)) {
                $data['wh_courier_boxes_weight_details'] = $courier_boxes_weight_wh[0];
            }
        }
 */
        if(!empty($data['booking_history'][0])) {
            if ($data['booking_history'][0]['request_type'] == AMC_PRICE_TAGS) {
                $data['requeste_type_is_amc'] = TRUE;
            } else {
                $data['requeste_type_is_amc'] = FALSE;
            }
        }
        //$data['spare_history'] = $this->partner_model->get_spare_state_change_tracking("spare_state_change_tracker.id,spare_state_change_tracker.spare_id,spare_state_change_tracker.action,spare_state_change_tracker.remarks,spare_state_change_tracker.agent_id,spare_state_change_tracker.entity_id,spare_state_change_tracker.entity_type, spare_state_change_tracker.create_date, spare_parts_details.parts_requested",array('spare_parts_details.booking_id' => $booking_id), true);
        $data['c2c'] = $this->booking_utilities->check_feature_enable_or_not(CALLING_FEATURE_IS_ENABLE);
        $data['saas_module'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
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
     *  Through this we add a new brand for selected service.
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
     *  Enters the new brand to our existing brand list for a particular service
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
//             <h3>New Brands added By  " . $this->session->userdata('employee_id') . "</h3>    
            $message = "
        <html>
        <head></head>
        <body>
           Dear Partner<br>
           We are glad to announce that we have added below new products to our existing brand. Please extend your support to provide great service to customers.
           
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
          $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, $bcc, $subject, $message, "",NEW_BRAND_ADDED_TAG);
        }

        redirect(base_url() . 'employee/booking/get_add_new_brand_form', 'refresh');
    }

    /**
     * @desc: load update booking form to update booking
     * @param: booking id
     * @return : void
     */
    function get_edit_booking_form($booking_id, $appliance_id = "",$is_repeat = NULL) {
        log_message('info', __FUNCTION__ . " Appliance ID  " . print_r($appliance_id, true) . " Booking ID: " . print_r($booking_id, true));
        $booking = $this->booking_creation_lib->get_edit_booking_form_helper_data($booking_id,$appliance_id,$is_repeat);      
        if($booking){            
            $booking['is_saas'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
            $booking['is_spare_requested'] = $this->booking_utilities->is_spare_requested($booking); 
            // Check if any line item against booking is invoiced to partner or not
            $arr_booking_unit_details = !empty($booking['unit_details'][0]['quantity']) ? $booking['unit_details'][0]['quantity'] : [];
            $booking['is_partner_invoiced'] = $this->booking_utilities->is_partner_invoiced($arr_booking_unit_details);            
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/update_booking', $booking);
        }
        else{
            echo "Booking Id Not Exist";
        }
    }

    /**
     * @desc: This function is used to update both Bookings and Queries.
     */
    function update_booking($user_id, $booking_id) {
        $bookings = array($booking_id);
        if($booking_id != INSERT_NEW_BOOKING){
            $bookings = $this->booking_model->getbooking_history($booking_id);
        }
        if (!empty($bookings)) {
            if ($this->input->post()) {
                $checkValidation = $this->booking_creation_lib->validate_booking();

                if ($checkValidation) {
                    log_message('info', __FUNCTION__ . " Booking ID  " . $booking_id . " User ID: " . $user_id);

                    $status = $this->getAllBookingInput($user_id, $booking_id);
                    if ($status) {
                        log_message('info', __FUNCTION__ . " Update Booking ID" . $status['booking_id']);
                        
                        $this->partner_cb->partner_callback($booking_id);

                        //Redirect to Default Search Page
                        if($booking_id == INSERT_NEW_BOOKING){
                        $this->session->set_userdata('success', 'Booking inserted successfully with Booking Id : '.$status['booking_id']);
                        }
                        else{
                            $this->session->set_userdata('success', 'Booking saved successfully with Booking Id : '.$status['booking_id']);
                        }
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
     *  @param : Customer Phone Number
     *  @param : $booking_primary_id : id of booking_details
     *  @return : none
     */
    function call_customer($cust_phone, $booking_primary_id = '') {
        $s1 = $_SERVER['HTTP_REFERER'];
        $s2 = base_url();
        $redirect_url = substr($s1, strlen($s2));
        $this->checkUserSession();
        //Get customer id
        $cust_id = '';
        $user = $this->user_model->get_users_by_any(array("users.phone_number" => $cust_phone));
        if ($user) {
            $cust_id = $user[0]['user_id'];
        }
        //Find agent phone from session
        $agent_id = $this->session->userdata('id');
        $agent_phone = $this->session->userdata('phone');
        //Save call log
        $call_log_id = $this->booking_model->insert_outbound_call_log(array(
            'agent_id' => $agent_id, 'customer_id' => $cust_id,
            'customer_phone' => $cust_phone
        ));
        if(CURRENT_TELEPHONY_SOLUTION == KNOWLARITY_STRING){        
            $this->notify->make_outbound_call_using_knowlarity($agent_phone, $cust_phone);
        }
        else{
            // Add Booking Id in Param , so that we can save call recording against booking Id
            // $booking_id contains the value of `id` column of `booking_details` Table
            $this->notify->make_outbound_call($agent_phone, $cust_phone, $booking_primary_id, $call_log_id);
        }
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
//  //insert in database
//  $this->apis->insertPassthruCall($callDetails);
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
        $postArray = $this->input->post();
        $where['is_in_process'] = 0;
        $whereIN['booking_id'] = $postArray['booking_id'];
        $whereIN['booking_details.current_status'] = array(_247AROUND_PENDING, _247AROUND_RESCHEDULED);
        $tempArray = $this->reusable_model->get_search_result_data("booking_details","booking_id, current_status",$where,NULL,NULL,NULL,$whereIN,NULL,array());
        if(!empty($tempArray)){
            if($this->input->post("internal_booking_status") == _247AROUND_COMPLETED){
                $reject_remarks = "Booking completion rejected by 247around";
                $actor = ACTOR_REJECT_FROM_REVIEW;
                $next_action = REJECT_FROM_REVIEW_NEXT_ACTION;
                $this->notify->insert_state_change($postArray['booking_id'], _247AROUND_COMPLETED_REJECTED, "InProcess_Completed", $reject_remarks, $this->session->userdata('id'), $this->session->userdata('employee_id'), $actor,$next_action,_247AROUND);
            
                $engineer_action = $this->engineer_model->getengineer_action_data("id", array("booking_id"=>$postArray['booking_id'], "internal_status"=>_247AROUND_COMPLETED, "current_status" => _247AROUND_COMPLETED));
                if(!empty($engineer_action)){
                    $eng_data = array(
                        "internal_status" => _247AROUND_PENDING,
                        "current_status" => _247AROUND_PENDING
                    );
                    $this->engineer_model->update_engineer_table($eng_data, array("booking_id"=>$postArray['booking_id']));
                }
            }
            else if($this->input->post("internal_booking_status") == _247AROUND_CANCELLED){
                $reject_remarks = "Booking cancellation rejected by 247around";
                $actor = ACTOR_REJECT_FROM_REVIEW;
                $next_action = REJECT_FROM_REVIEW_NEXT_ACTION;
                $this->notify->insert_state_change($postArray['booking_id'], _247AROUND_CANCELED_REJECTED, "InProcess_Completed", $reject_remarks, $this->session->userdata('id'), $this->session->userdata('employee_id'), $actor,$next_action,_247AROUND);
            
                $engineer_action = $this->engineer_model->getengineer_action_data("id", array("booking_id"=>$postArray['booking_id'], "internal_status"=>_247AROUND_CANCELLED, "current_status" => _247AROUND_CANCELLED));
                if(!empty($engineer_action)){
                    $eng_data = array(
                        "internal_status" => _247AROUND_PENDING,
                        "current_status" => _247AROUND_PENDING
                    );
                    $this->engineer_model->update_engineer_table($eng_data, array("booking_id"=>$postArray['booking_id']));
                }
            }
           
            echo "Booking Updated Successfully";
            $postArray = $this->input->post();
            $this->miscelleneous->reject_booking_from_review($postArray);
        }
        else{
            echo "Booking updated by someone else , Please check updated booking and try again";
        }
    }

    /**
     * @desc: This funtion is used to review bookings (All selected checkbox) which are
     * completed/cancelled by our vendors.
     * It completes/cancels these bookings in the background and returns immediately.
     * @param : void
     * @return : void
     */
    function checked_complete_review_booking() {
        $this->miscelleneous->checked_complete_review_booking($this->input->post());
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
        $whereIN = $where = $join = array();
        if($this->session->userdata('user_group') == _247AROUND_RM || $this->session->userdata('user_group') == _247AROUND_ASM){
            $sf_list = $this->vendor_model->get_employee_relation($this->session->userdata('id'));
            if (!empty($sf_list)) {
                $serviceCenters = $sf_list[0]['service_centres_id'];
                $whereIN =array("service_center_id"=>explode(",",$serviceCenters));
            }
        }
        if($this->session->userdata('is_am') == '1'){
            $am_id = $this->session->userdata('id');
            $where = array('agent_filters.agent_id' => $am_id,'agent_filters.is_active'=>1,'agent_filters.entity_type'=>_247AROUND_EMPLOYEE_STRING);
            $join['agent_filters'] =  "booking_details.partner_id=agent_filters.entity_id and service_centres.state=agent_filters.state AND agent_filters.entity_type = '"._247AROUND_EMPLOYEE_STRING."' ";
        }
        $data['data'] = $this->booking_model->review_reschedule_bookings_request($whereIN, $where, $join);
        
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/admin_booking_review', $data);
    }
    function get_booking_partner_will_review($bookingID = NULL){
        $bookingArray = array();
        $statusData = $this->reusable_model->get_search_result_data("partners","id,booking_review_for,review_time_limit",NULL,NULL,NULL,NULL,NULL,NULL,array());
        foreach($statusData as $values){
            if($values['booking_review_for']){
                $statusString = implode("','",explode(",",$values['booking_review_for']));
                $where[] = "(service_center_booking_action.internal_status IN ('".$statusString."') AND DATEDIFF(CURRENT_TIMESTAMP, service_center_booking_action.closed_date)<".$values['review_time_limit']." AND booking_details.partner_id ='".$values['id']."')";
            }
        }
        $tempArray = $this->booking_model->get_partner_review_booking($where,$bookingID);
        foreach ($tempArray as $bookings){
            $bookingArray[] = $bookings['booking_id'];
        }
        return $bookingArray;
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
        $partner_id_array = $this->input->post('partner_id');
        $employeeID = $this->session->userdata('employee_id');
        $id = $this->session->userdata('id');
        $this->miscelleneous->approved_rescheduled_bookings($reschedule_booking_id,$reschedule_booking_date,$reschedule_reason,$partner_id_array,$id,$employeeID);
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
        log_message('info', __METHOD__ . " Booking ID " . $booking_id . " POST Data " . json_encode($this->input->post()), TRUE);
        $this->form_validation->set_rules('pod', 'POD ', 'callback_validate_serial_no');
        if ($this->form_validation->run() == FALSE) {
            $this->get_complete_booking_form($booking_id);
        } else {
            $change_appliance_details = $this->input->post('change_appliance_details');

            if ($change_appliance_details == 1) {
                $this->update_completed_unit_applinace_details($booking_id);
            }
            $is_closure = 0;
            if ($this->session->userdata('user_group') == _247AROUND_CLOSURE) {
                $is_closure = 1;
            }
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
            if ($this->input->post('sn_remarks')) {
                $admin_remarks = $this->input->post('admin_remarks') . "Serial Number Comments : - " . $this->input->post('sn_remarks');
            }
            $serial_number = $this->input->post('serial_number');
            $serial_number_pic = $this->input->post('serial_number_pic');
            $purchase_date = $this->input->post('dop');
            $purchase_invoice = $this->input->post('appliance_purchase_invoice');
            $upcountry_charges = $this->input->post("upcountry_charges");
            $internal_status = _247AROUND_CANCELLED;
            $pincode = $this->input->post('booking_pincode');
            $state = $this->vendor_model->get_state_from_pincode($pincode);
            $partner_id = $this->input->post('partner_id');
            //$sp_required_id = json_decode($this->input->post("sp_required_id"), true);
            //$spare_parts_required = $this->input->post('spare_parts_required');
            //$price_tag_array = $this->input->post('price_tags');
            $model_number = $this->input->post('model_number');

            $technical_symptom = $this->input->post('closing_symptom');
            $technical_defect = $this->input->post('closing_defect');
            $technical_solution = $this->input->post('technical_solution');

            $booking_symptom['solution_id'] = $technical_solution;
            $booking_symptom['symptom_id_booking_completion_time'] = $technical_symptom;
            $booking_symptom['defect_id_completion'] = $technical_defect;

            // get Booking Primary Id
            $booking_primary_id = $this->input->post('booking_primary_id');
            $service_center_details = $this->booking_model->getbooking_charges($booking_id);
            $b_unit_details = array();
            if ($status == 1) {
                $b_unit_details = $this->booking_model->get_unit_details(array('booking_id' => $booking_id));
            }
            $k = 0;

            $purchase_invoice_file_name = '';
            if (!empty($_FILES['sf_purchase_invoice']['name'])) :
                $purchase_invoice_file_name = $this->upload_sf_purchase_invoice_file($booking_id, $_FILES['sf_purchase_invoice']['tmp_name'], ' ', $_FILES['sf_purchase_invoice']['name']);
            endif;
            foreach ($customer_basic_charge as $unit_id => $value) {
                // variable $unit_id  is existing id in booking unit details table of given booking id
                $data = array();
                $data['customer_paid_basic_charges'] = $value;
                $data['customer_paid_extra_charges'] = $additional_charge[$unit_id];
                $data['customer_paid_parts'] = $parts_cost[$unit_id];
                $trimSno = "";
                if (isset($serial_number)) {
                    $trimSno = str_replace(' ', '', trim($serial_number));
                    $serial_number_pic = trim($serial_number_pic);
                }
                //Model number Data
                $sf_model_number = "";
                if (isset($model_number[$unit_id])) {
                    $sf_model_number = $model_number[$unit_id];
                }
                $sf_purchase_date = NULL;
                if (!empty($purchase_date[0])) {
                    $sf_purchase_date = $this->miscelleneous->convert_date_to_database_format($purchase_date[0]);
                }

                if (!empty($purchase_invoice[$unit_id]) || !empty($purchase_invoice_file_name)) {
                    if (empty($purchase_invoice_file_name)) {
                        $purchase_invoice_file_name = $purchase_invoice[$unit_id];
                    }
                }

                if (!empty($serial_number_pic)) {
                    $this->partner_model->insert_partner_serial_number(array('partner_id' => $partner_id, "serial_number" => $trimSno, "active" => 1, "added_by" => "vendor"));
                    //$serialNumberMandatoryPartners = explode(',',SERIAL_NUMBER_MENDATORY);
//                if(!empty($insertd)  && in_array($partner_id, $serialNumberMandatoryPartners)){
//                    //$this->miscelleneous->inform_partner_for_serial_no($booking_id, $service_center_details[0]['service_center_id'$data['serial_number_pic']], $partner_id, $trimSno, $serial_number_pic);
//                }
                }

                if ($is_closure == 1) {
                    $data['serial_number'] = $trimSno;
                    $data['serial_number_pic'] = $serial_number_pic;
                    $data['sf_model_number'] = $sf_model_number;
                    $data['sf_purchase_date'] = $sf_purchase_date;
                }


                if (isset($customer_net_payable[$unit_id])) {
                    $data['customer_net_payable'] = $customer_net_payable[$unit_id];
                }

                $data['booking_status'] = _247AROUND_PENDING;

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


                            $closed_date = date('Y-m-d H:i:s');

                            if (!empty($b_unit_details)) {
                                $closed_date = $b_unit_details[0]['ud_closed_date'];
                            }

                            //$data['booking_status'] = _247AROUND_COMPLETED;
                            //$data['ud_closed_date'] = $closed_date;
                            $internal_status = _247AROUND_COMPLETED;
                            $data_service_center['internal_status'] = _247AROUND_COMPLETED;

                            $data_service_center['closed_date'] = $closed_date;

                            if ($is_closure == 1) {
                                $data['ud_closed_date'] = $closed_date;
                                $data['booking_status'] = _247AROUND_COMPLETED;
                                $data_service_center['current_status'] = $data_service_center['internal_status'] = _247AROUND_COMPLETED;
                            } else {
                                $data_service_center['current_status'] = SF_BOOKING_INPROCESS_STATUS;
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
                            $data_service_center['serial_number'] = $trimSno;
                            $data_service_center['serial_number_pic'] = $serial_number_pic;
                            $data_service_center['amount_paid'] = $total_amount_paid;
                            $data_service_center['model_number'] = $sf_model_number;
                            $data_service_center['sf_purchase_date'] = $sf_purchase_date;
                            if ($k == 0) {
                                $data_service_center['upcountry_charges'] = $upcountry_charges;
                            }


                            log_message('info', __FUNCTION__ . " New unit selected, service center action data " . print_r($data_service_center, true));
                            $this->vendor_model->insert_service_center_action($data_service_center);
                        }
                    }
                } else {
                    $unit_status = $booking_status[$unit_id];
                    $closed_date = date('Y-m-d H:i:s');
                    if (!empty($b_unit_details)) {
                        $closed_date = $b_unit_details[0]['ud_closed_date'];
                    }

                    if ($unit_status === _247AROUND_COMPLETED) {
                        $internal_status = _247AROUND_COMPLETED;
                    }

                    $service_center['closed_date'] = $closed_date;
                    $service_center['current_status'] = SF_BOOKING_INPROCESS_STATUS;
                    $service_center['internal_status'] = $unit_status;

                    if ($is_closure == 1) {
                        $data['booking_status'] = $unit_status;
                        $data['ud_closed_date'] = $closed_date;
                        $service_center['current_status'] = $data['booking_status'];
                        $service_center['internal_status'] = $data['booking_status'];
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

                    $service_center['unit_details_id'] = $unit_id;
                    $service_center['update_date'] = date('Y-m-d H:i:s');
                    $service_center['service_charge'] = $data['customer_paid_basic_charges'];
                    $service_center['additional_service_charge'] = $data['customer_paid_extra_charges'];
                    $service_center['parts_cost'] = $data['customer_paid_parts'];
                    $service_center['serial_number'] = $trimSno;
                    $service_center['serial_number_pic'] = $serial_number_pic;
                    $service_center['amount_paid'] = $total_amount_paid;
                    $service_center['model_number'] = $sf_model_number;
                    $service_center['sf_purchase_date'] = $sf_purchase_date;
                    if ($k == 0) {
                        $service_center['upcountry_charges'] = $upcountry_charges;
                    }

                    log_message('info', ": " . " update Service center data " . print_r($service_center, TRUE));

                    $this->vendor_model->update_service_center_action($booking_id, $service_center);
                    
                    if ($is_closure == 1) {
                        // Also Update Status in Engineer Booking Action Table
                        $eng_data = array(
                            "internal_status" => $data['booking_status'],
                            "current_status" => $data['booking_status']
                        );
                    } else {
                        // Also Update Status in Engineer Booking Action Table
                        $eng_data = array(
                            "internal_status" => SF_BOOKING_INPROCESS_STATUS,
                            "current_status" => $unit_status
                        );
                    }

                    $this->engineer_model->update_engineer_table($eng_data, array("booking_id" => $booking_id, "unit_details_id" => $unit_id));
                }
                $this->miscelleneous->update_appliance_details($unit_id);
                $k = $k + 1;
            }
            // insert in booking files.
            $booking_file = [];
            if (!empty($purchase_invoice_file_name)) {
                $booking_file['booking_id'] = $booking_id;
                $booking_file['file_description_id'] = SF_PURCHASE_INVOICE_FILE_TYPE;
                $booking_file['file_name'] = $purchase_invoice_file_name;
                $booking_file['file_type'] = 'image/' . pathinfo("https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/purchase-invoices/" . $purchase_invoice_file_name, PATHINFO_EXTENSION);
                $booking_file['create_date'] = date("Y-m-d H:i:s");
                $this->booking_model->insert_booking_file($booking_file);
            }
            if ($booking_symptom['symptom_id_booking_completion_time'] || $booking_symptom['defect_id_completion'] || $booking_symptom['solution_id']) {
                $rowsStatus = $this->booking_model->update_symptom_defect_details($booking_id, $booking_symptom);

                if (!$rowsStatus) {
                    $booking_symptom['booking_id'] = $booking_id;
                    $booking_symptom['symptom_id_booking_creation_time'] = 0;
                    $booking_symptom['create_date'] = date("Y-m-d H:i:s");
                    $this->booking_model->addBookingSymptom($booking_symptom);
                }
            }

            // update spare parts.
            $is_update_spare_parts = $this->miscelleneous->update_spare_consumption_status($this->input->post(), $booking_id, $service_center_details, $status);
            if ($is_update_spare_parts && $is_update_spare_parts != DEFECTIVE_PARTS_SHIPPED) {
                $booking['internal_status'] = DEFECTIVE_PARTS_PENDING;
            } else if ($is_update_spare_parts == DEFECTIVE_PARTS_SHIPPED) {
                $booking['internal_status'] = DEFECTIVE_PARTS_SHIPPED;
            } else {
                $booking['internal_status'] = $internal_status;
            }

            $booking['booking_id'] = $booking_id;
            $booking['customer_paid_upcountry_charges'] = $upcountry_charges;

            if ($is_closure == 1) {
                $booking['amount_paid'] = $total_amount_paid;
                $booking['closed_date'] = date('Y-m-d H:i:s');
                if (!empty($b_unit_details)) {
                    $booking['closed_date'] = $b_unit_details[0]['ud_closed_date'];
                }

                $booking['current_status'] = $internal_status;
                $p_c_status = _247AROUND_COMPLETED;
                
            } else {

                $p_c_status = SF_BOOKING_INPROCESS_STATUS;
            }

            // check spares are pending or shipped for current booking.
            $spare_parts_details = $this->partner_model->get_spare_parts_by_any('*', ['booking_id' => $booking_id, 'parts_shipped is not null' => null, 'status != "' . _247AROUND_CANCELLED . '"' => NULL], false, FALSE, false, array(), false, false, false, false, false, false);
            if (!empty($spare_parts_details)) {
                $booking['internal_status'] = $spare_parts_details[0]['status'];
            }
            // check partner status
            $actor = $next_action = 'NULL';
            $partner_status = $this->booking_utilities->get_partner_status_mapping_data($p_c_status, $booking['internal_status'], $partner_id, $booking_id);
            if (!empty($partner_status)) {
                $booking['partner_current_status'] = $partner_status[0];
                $booking['partner_internal_status'] = $partner_status[1];
                $actor = $booking['actor'] = $partner_status[2];
                $next_action = $booking['next_action'] = $partner_status[3];
            }

            $booking['cancellation_reason'] = NULL;

            if ($this->input->post('rating_stars') !== "") {
                $booking['rating_stars'] = $this->input->post('rating_stars');
                $booking['rating_comments'] = $this->input->post('rating_comments');
            }

            $booking['closing_remarks'] = $service_center['closing_remarks'];

            $booking['update_date'] = date('Y-m-d H:i:s');

            // this function is used to update booking details table
            if (!$this->input->post('service_center_closed_date')) {
                //get engineer close date
                $eng_booking = $this->engineer_model->getengineer_action_data("closed_date", array("booking_id" => $booking_id, "closed_date IS NOT NULL" => NULL));
                if (!empty($eng_booking)) {
                    $booking['service_center_closed_date'] = $eng_booking[0]['closed_date'];
                } else {
                    $booking['service_center_closed_date'] = date('Y-m-d H:i:s');
                }
            }

            if ($internal_status == _247AROUND_CANCELLED) {
                $booking['api_call_status_updated_on_completed'] = DEPENDENCY_ON_CUSTOMER;
            }
            $this->booking_model->update_booking($booking_id, $booking);
            $this->miscelleneous->process_booking_tat_on_completion($booking_id);
            $spare = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id, spare_parts_details.status, spare_parts_details.entity_type, spare_parts_details.partner_id, requested_inventory_id, spare_lost, spare_parts_details.parts_shipped ,spare_parts_details.defective_part_shipped, spare_parts_details.consumed_part_status_id, spare_parts_details.defective_part_required ", array('booking_id' => $booking_id, 'status NOT IN ("Completed","Cancelled")' => NULL), false);
            foreach ($spare as $sp) {
                //Update Spare parts details table

                if ($sp['status'] == SPARE_PARTS_REQUESTED && !empty($sp['requested_inventory_id']) && !empty($sp['entity']) && ($sp['entity'] == _247AROUND_SF_STRING)) {
                    
                    if (!$sp['spare_lost']) {
                        $this->service_centers_model->update_spare_parts(array('id' => $sp['id']), array('old_status' => $sp['status'], 'status' => _247AROUND_CANCELLED));
                    }
                } else if (in_array($sp['status'], [SPARE_PARTS_REQUESTED, SPARE_PART_ON_APPROVAL]) && !$sp['spare_lost']) {
                    $this->service_centers_model->update_spare_parts(array('id' => $sp['id']), array('old_status' => $sp['status'], 'status' => _247AROUND_CANCELLED));
                } else if (!$sp['spare_lost']) {
                    //        $this->service_centers_model->update_spare_parts(array('id'=> $sp['id']), array('old_status' => $sp['status'],'status' => $internal_status));
                } else if (!empty($sp['parts_shipped']) && empty($sp['defective_part_shipped'])) {
                    $status_to_be_updated = OK_PART_TO_BE_SHIPPED;
                    if (empty($sp['defective_part_required'])) {
                        $status_to_be_updated = _247AROUND_COMPLETED;
                    } else if (!empty($sp['consumed_part_status_id'])) {
                        $is_part_consumed = $this->reusable_model->get_search_result_data('spare_consumption_status', 'is_consumed', ['id' => $sp['consumed_part_status_id']], NULL, NULL, NULL, NULL, NULL)[0]['is_consumed'];
                        if (!empty($is_part_consumed)) {
                            $status_to_be_updated = DEFECTIVE_PARTS_PENDING;
                        }
                    }

                    $this->service_centers_model->update_spare_parts(array('id' => $sp['id']), array('old_status' => $sp['status'], 'status' => $status_to_be_updated));
                }
            }

            // save SF and Admin amount mismatch (if any) in booking_amount_differences table
            $sf_filled_amount = !empty($service_center_details[0]['amount_paid']) ? $service_center_details[0]['amount_paid'] : 0;
            $this->miscelleneous->save_booking_amount_history($booking_primary_id, $sf_filled_amount, $total_amount_paid);
            // Update spare handling charges only for central warehouse
            $spare_check = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id, spare_parts_details.status, spare_parts_details.entity_type, spare_parts_details.partner_id, "
                    . "requested_inventory_id, spare_lost, spare_parts_details.parts_shipped ,spare_parts_details.defective_part_shipped, spare_parts_details.consumed_part_status_id, "
                    . "spare_parts_details.defective_part_required ", array('booking_id' => $booking_id, 'status NOT IN ("Cancelled")' => NULL, 'parts_shipped IS NOT NULL ' => NULL, 
                        'part_warranty_status' => 1, 'is_micro_wh' => 2), false);
            if (!empty($spare_check)) {
                $this->check_and_update_partner_extra_spare($booking_id);
            }
            
            $msg = 'Booking Successfully Submitted for Review';
            if ($is_closure == 1) {
                $msg = 'Booking Completed Successfully';
            }
            if (empty($status)) {
                //Log this state change as well for this booking
                //param:-- booking id, new state, old state, employee id, employee name
                $this->notify->insert_state_change($booking_id, SF_BOOKING_COMPLETE_STATUS, _247AROUND_PENDING, $booking['closing_remarks'], $this->session->userdata('id'),
                        $this->session->userdata('employee_id'), $actor, $next_action, _247AROUND);

                if ($booking['internal_status'] == _247AROUND_COMPLETED) {
                    $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
                    $send['booking_id'] = $booking_id;
                    $send['state'] = $internal_status;
                    $this->asynchronous_lib->do_background_process($url, $send);

                    $cb_url = base_url() . "employee/do_background_process/send_request_for_partner_cb/" . $booking_id;
                    $pcb = array();
                    $this->asynchronous_lib->do_background_process($cb_url, $pcb);
                }

                if ($this->input->post('rating_stars') !== "") {
                    if ($this->input->post('rating_stars') < 3) {
                        $this->miscelleneous->send_bad_rating_email($this->input->post('rating_stars'), $booking_id);
                    }
                    //update rating state
                    $remarks = 'Rating' . ':' . $booking['rating_stars'] . '. ' . $booking['rating_comments'];
                    $this->notify->insert_state_change($booking_id, RATING_NEW_STATE, $status, $remarks, $this->session->userdata('id'), $this->session->userdata('employee_id'), ACTOR_BOOKING_RATING
                            , RATING_NEXT_ACTION, _247AROUND);
                    // send sms after rating
                    //if 'do not send sms check then does not send sms'
                    if (!$this->input->post('not_send_sms')) {
                        $this->send_rating_sms($this->input->post('booking_primary_contact_no'), $booking['rating_stars'], $this->input->post('customer_id'), $booking_id);
                    }
                }

                // Case if customer not reachable for rating
                if ($this->input->post('not_reachable')) {
                    $this->customer_not_reachable_for_rating($booking_id, $this->input->post('customer_id'), $this->input->post('booking_primary_contact_no'));
                }

                //Generate Customer payment Invoice
                if ($total_amount_paid > MAKE_CUTOMER_PAYMENT_INVOICE_GREATER_THAN && $internal_status == _247AROUND_COMPLETED) {
                    $invoice_url = base_url() . "employee/user_invoice/payment_invoice_for_customer/" . $booking_id . "/" . $this->session->userdata('id');
                    $payment = array();
                    $this->asynchronous_lib->do_background_process($invoice_url, $payment);
                } else {
                    log_message("info", " Amount Paid less then 5  for booking ID " . $booking_id . " Amount Paid " . $total_amount_paid);
                }

                // save questionnaire data against Booking
                if (!empty($this->input->post('review_questionnaire_booking_id')) && !empty($this->input->post('review_questionnaire'))) {
                    $review_questionnaire_booking_id = $this->input->post('review_questionnaire_booking_id');
                    $review_questionnaire_data = $this->input->post('review_questionnaire');
                    $this->save_review_questionnaire_data($review_questionnaire_booking_id, $review_questionnaire_data);
                }
                
                $this->session->set_userdata('success', $msg);
                redirect(base_url() . 'employee/booking/view_bookings_by_status/Pending');
            } else {
                
                $this->session->set_userdata('success', $msg);
                redirect(base_url() . 'employee/booking/view_bookings_by_status/' . $internal_status);
            }
        }
    }
    /**
     *  @desc : This function is used to update partner_extra_charges if partner_net_pay >0
     *  @param : string $booking_id
     *  @Author : Abhishek Awasthi 
     */

    function check_and_update_partner_extra_spare($booking_id) {

        $booking_unit_details = $this->booking_model->getunit_details_with_id($booking_id, "", TRUE);
        foreach ($booking_unit_details as $unit) {
            if ($unit['partner_net_pay'] > 0) {

                $brand = $unit['brand'];
                $category = $unit['category'];
                $capacity = $unit['capacity'];
                $partner_id = $unit['partner_id'];
                $price_tag = $unit['uprice_tag'];
                $service_id = $unit['service_id'];
//                $where = array(
//                    'brand' => $brand,
//                    'category' => $category,
//                    'capacity' => $capacity,
//                    'partner_id' => $partner_id,
//                    'service_category' => $price_tag
//                );

                $source = $this->partner_model->getpartner_details('bookings_sources.source, partner_type', array('bookings_sources.partner_id' => $partner_id));
                if (!empty($source[0]['partner_type']) && $source[0]['partner_type'] == OEM) {
                    $prices = $this->partner_model->getPrices($service_id, $category, $capacity, $partner_id, $price_tag, $brand, true, NULL, TRUE);
                } else {
                    $prices = $this->partner_model->getPrices($service_id, $category, $capacity, $partner_id, $price_tag, "", true, NULL, TRUE);
                }

                if (!empty($prices)) {
                    //            $select = "service_centre_charges.partner_spare_extra_charge";
//            $charges =  $this->service_centre_charges_model->get_service_caharges_data($select,$where);
                    $partner_spare_extra_charge = $prices[0]['partner_spare_extra_charge'];

                    $data_unit = array(
                        'partner_spare_extra_charge' => $partner_spare_extra_charge
                    );
                    $where_unit = array(
                        'id' => $unit['id']
                    );
                    $this->booking_model->update_booking_unit_details_by_any($where_unit, $data_unit);
                }
            }
        }
    }

    /**
     *  @desc : This function is used to upload the support file for order id to s3 and save into database
     *  @param : string $booking_primary_contact_no
     *  @return : boolean/string
     */
    function upload_sf_purchase_invoice_file($booking_id, $tmp_name, $error, $name) {

        $support_file_name = false;

        if (($error != 4) && !empty($tmp_name)) {

            $tmpFile = $tmp_name;
            $support_file_name = $booking_id . '_sf_purchase_invoice_' . substr(md5(uniqid(rand(0, 9))), 0, 15) . "." . explode(".", $name)[1];
            //move_uploaded_file($tmpFile, TMP_FOLDER . $support_file_name);
            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "purchase-invoices/" . $support_file_name;
            $upload_file_status = $this->s3->putObjectFile($tmpFile, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            if($upload_file_status){
                //Logging success for file uppload
                log_message('info', __METHOD__ . 'Sf purchase invoice has been uploaded sucessfully for booking_id: '.$booking_id);
                return $support_file_name;
            }else{
                //Logging success for file uppload
                log_message('info', __METHOD__ . 'Error In uploading support file for booking_id: '.$booking_id);
                return False;
            }

        }

        
    }    
        
    /**
     * @desc: this is used to validate duplicate serial no from Ajax
     */
    function validate_serial_no_from_ajax(){
        log_message('info', __METHOD__);
        $serial_number = $this->input->post('serial_number');
        $price_tags = $this->input->post('price_tags');
        $user_id = $this->input->post('user_id');
        $booking_id = $this->input->post('booking_id');
        $partner_id = $this->input->post('partner_id');
        $appliance_id = $this->input->post('appliance_id');
        
        $validate_serial_number_special_char = false;
        if (!ctype_alnum($serial_number) && !empty($validate_serial_number_special_char)) {
            $status= array('code' => '247', "message" => "Serial Number Entered With Special Character " . $serial_number . " . This is not allowed.");
            log_message('info', "Serial Number Entered With Special Character " . $serial_number . " . This is not allowed.");
            echo json_encode($status, true);
        }
        else {
            $status = $this->validate_serial_no->validateSerialNo($partner_id, trim($serial_number), $price_tags, $user_id, $booking_id,$appliance_id);
            if(!empty($status)){
                echo json_encode($status);
            } else {
                echo json_encode(array('code' => 247));
            }
        }
    }
    
    function validate_serial_no() {
        $serial_number = $this->input->post('serial_number');
        $trimSno = str_replace(' ', '', trim($serial_number));
        $upload_serial_number_pic = array();
        if(isset($_FILES['upload_serial_number_pic'])){
            $upload_serial_number_pic = $_FILES['upload_serial_number_pic'];
        }
        $pod = $this->input->post('pod');
        $price_tag = $this->input->post('selected_price_tags');
        if(!empty($price_tag)){
            $arr_price_tags = explode(",", $price_tag);
            $price_tag = $this->booking_utilities->get_booking_request_type($arr_price_tags); 
        }
        $booking_status = $this->input->post('booking_status');
        $partner_id = $this->input->post('partner_id');
        $user_id = $this->input->post('user_id');
        $booking_id = $this->input->post('booking_id');
        $service_id = $this->input->post('appliance_id');
        $return_status = true;
        $message = "";
        if (isset($pod)) {            
            if(!empty($serial_number)) {  
                $validate_serial_number_special_char = false;
                if (!ctype_alnum($serial_number) && !empty($validate_serial_number_special_char)) {
                    log_message('info', "Serial Number Entered With Special Character " . $serial_number . " . This is not allowed.");
                    $this->form_validation->set_message('validate_serial_no', "Serial Number Entered With Special Character " . $serial_number . " . This is not allowed.");
                    return FALSE;
                }
            }
            if($pod == '1'){
                if(isset($upload_serial_number_pic['name']) && ($upload_serial_number_pic['name'])){
                    $s =  $this->upload_insert_upload_serial_no($upload_serial_number_pic, $partner_id, $trimSno);
                    if(empty($s)){
                        $this->form_validation->set_message('validate_serial_no', 'Serial Number, File size or file type is not supported. Allowed extentions are png, jpg, jpeg and pdf. Maximum file size is 5 MB.');
                        $return_status = false;
                    }
                }
                else{
                    if(empty($this->input->post('serial_number_pic'))){
                        $return_status = false;
                        $s = $this->form_validation->set_message('validate_serial_no', "Please upload serial number image");
                    }
                }
                $status = $this->validate_serial_no->validateSerialNo($partner_id, trim($serial_number), $price_tag, $user_id, $booking_id, $service_id);
                if (!empty($status)) {  
                    if ($status['code'] == DUPLICATE_SERIAL_NO_CODE) {
                        $return_status = false;
                        $message = $status['message'];
                        log_message('info', " Duplicate Serial No " . trim($serial_number));
                    }
                }
            }
            else {
                // upload serial number image in case of POD = 0 also
                if(isset($upload_serial_number_pic['name']) && !empty($upload_serial_number_pic['name'])){
                    $s =  $this->upload_insert_upload_serial_no($upload_serial_number_pic, $partner_id, $trimSno);
                    if(empty($s)){
                        $this->form_validation->set_message('validate_serial_no', 'Serial Number, File size or file type is not supported. Allowed extentions are png, jpg, jpeg and pdf. Maximum file size is 5 MB.');
                        $return_status = false;
                    }
                }
            }
        }
        if ($return_status == true) {
            return true;
        }
        else {
            $this->form_validation->set_message('validate_serial_no', $message);
            return FALSE;
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
        $this->form_validation->set_rules('booking_date', 'Booking Date', 'required');
        $this->form_validation->set_rules('booking_timeslot', 'Booking Time Slot', 'required');
        $this->form_validation->set_rules('admin_remarks', 'Reason', 'required|trim');
        if ($this->form_validation->run() === false) {
            $this->get_convert_booking_to_pending_form($booking_id, $status);
        } else {
            $this->miscelleneous->reopen_booking($booking_id, $status);
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
        $this->notify->insert_state_change($booking_id, _247AROUND_PENDING, _247AROUND_CANCELLED, "", $this->session->userdata('id'), $this->session->userdata('employee_id'), 
                ACTOR_OPEN_CANCELLED_BOOKING_FORM,NEXT_ACTION_REJECT_FROM_REVIEW,_247AROUND);
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
        $actor = $next_action = 'not_define';
        if ($partner_id) {
            $partner_status = $this->booking_utilities->get_partner_status_mapping_data($status['current_status'], $status['internal_status'], $partner_id, $booking_id);
            if (!empty($partner_status)) {
                $status['partner_current_status'] = $partner_status[0];
                $status['partner_internal_status'] = $partner_status[1];
                $actor = $status['actor'] = $partner_status[2];
                $next_action = $status['next_action'] = $partner_status[3];
            }
        }
        $this->booking_model->update_booking($booking_id, $status);
        $this->booking_model->update_booking_unit_details_by_any(array('booking_id'=> $booking_id), array('booking_status'=> _247AROUND_FOLLOWUP));    

        //Log this state change as well for this booking
        $this->notify->insert_state_change($booking_id, _247AROUND_FOLLOWUP, _247AROUND_CANCELLED, "Cancelled_Query to FollowUp", $this->session->userdata('id'), 
                $this->session->userdata('employee_id'),$actor,$next_action, _247AROUND);

        redirect(base_url() . 'employee/booking/view_queries/FollowUp/' . PINCODE_ALL_AVAILABLE . '/' . $booking_id);
    }
    
    /**
     * @desc: This is used to show Booking Life Cycle of particular Booking
     * params: String Booking_ID
     * return: Array of Data for View
     */
    function get_booking_life_cycle($booking_id) { 
        $data['data'] = $this->booking_model->get_booking_state_change_by_id($booking_id);
        $data['request_type'] = $this->miscelleneous->get_request_type_life_cycle($booking_id);
        //Checking for 247Around user
        // $data['sms_sent_details'] = $this->booking_model->get_sms_sent_details($booking_id);
        // $data['email_sent_details'] = $this->booking_model->get_email_sent_details($booking_id);
        //$this->load->view('employee/header/'.$this->session->userdata('user_group'));

        $this->load->view('employee/show_booking_life_cycle', $data);
    }
    
     /**
     * @desc: This is used to show Email and Sms of particular Booking
     * params: String Booking_ID
     * return: Array of Data for View
     */
    function get_booking_email_sms($booking_id) { 
        $data['data'] = array();
        $data['sms_sent_details'] = $this->booking_model->get_sms_sent_details($booking_id);
        $data['email_sent_details'] = $this->booking_model->get_email_sent_details($booking_id);
        $where_whatsapp_log = array('booking_id' => $booking_id, "status not in ('failed')" => null);
        $data['whatsapp_logs'] = $this->booking_model->get_whatsapp_log_details($where_whatsapp_log);
        //$this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/show_booking_life_cycle', $data);
    }
    
    /**
     * @desc this is used to load comment for requested booking id
     * @param String $booking_id
     */
    function get_comment_section($booking_id , $comment_type){
        $data['comments'] = $this->booking_model->get_remarks(array('booking_id' => $booking_id, "isActive" => 1,'comment_type'=> $comment_type));
        $data['booking_id'] = $booking_id;
        $data['user_id'] = $this->session->userdata('id');
        $this->load->view('employee/comment_section', $data);
    }
    /**
     * @desc this is used to add new comment for the booking
     */
    function addComment() { 
        $this->form_validation->set_rules('booking_id', 'booking_id', 'required');
        $this->form_validation->set_rules('comment', 'comment', 'required');
        if ($this->form_validation->run() == TRUE) {
            $data['agent_id'] = $this->session->userdata('id');
            $data['comment_type'] = $this->input->post('comment_type');
            $data['remarks'] = trim($this->input->post('comment'));
            $data['booking_id'] = $this->input->post('booking_id');
            $data['entity_id'] = _247AROUND;
            $data['entity_type'] = '247around';
            $data['isActive'] = 1;
            $data['create_date'] = date("Y-m-d H:i:s");
            $status = $this->booking_model->add_comment($data);
            if($status){  
                $this->get_comment_section($data['booking_id'], $data['comment_type']);
            } else {
                echo "error";
            }
        } else {
            echo "error";
        }
    }
    /**
     * @desc this is used to update comment
     */      
    function update_Comment() {

        $this->form_validation->set_rules('comment', 'comment', 'required');
        $this->form_validation->set_rules('comment_id', 'comment_id', 'required');
        $this->form_validation->set_rules('booking_id', 'booking_id', 'required');
        if ($this->form_validation->run() == TRUE) {

            $data['remarks'] = trim($this->input->post('comment'));
            $comment_type = $this->input->post('comment_type');
            $data['update_date'] = date("Y-m-d H:i:s");
            $id = $this->input->post('comment_id');
            $booking_id = $this->input->post('booking_id');

            $status = $this->booking_model->update_comment(array('id' => $id), $data);
            if($status){
                $this->get_comment_section($booking_id, $comment_type);
            } else {
                echo "error";
            }
        } else {
            echo "error";
        }
    }
    
    /**
     * @desc this is used to delete comment 
     */
    function deleteComment(){
        $this->form_validation->set_rules('comment_id', 'comment_id', 'required');
        $this->form_validation->set_rules('booking_id', 'booking_id', 'required');
        if ($this->form_validation->run() == TRUE) {
            $comment_id = $this->input->post('comment_id');
            $comment_type = $this->input->post('comment_type');
            $data['isActive']=0;
            $where = array('id' => $comment_id);
            $booking_id = $this->input->post('booking_id');
            $status = $this->booking_model->update_comment($where, $data);
            if($status){
                $this->get_comment_section($booking_id ,$comment_type);
            } else {
                echo "error";
            }
        } else {
            echo "error";
        }
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
                if(!$this->input->post('is_repeat')){
                    if (!empty($order_id)) {
                        //Check only If booking is not Repeat
                            $partner_booking = $this->partner_model->get_order_id_for_partner($partner_id, $order_id, $booking_id,'Yes');
                            if(is_array($partner_booking))
                            $recordCount = count($partner_booking);
                            if (is_null($partner_booking)) {
                                return true;
                            } 
                            //If existing belongs to dealer
                            else if($recordCount == 1 && !empty($partner_booking[0]['dealer_id'])){
                                 return true;
                            }
                            else {
                                if($partner_booking[0]['current_status'] !== _247AROUND_CANCELLED){
                                    $output = "Duplicate Order ID";
                                    $userSession = array('error' => $output);
                                    $this->session->set_userdata($userSession);
                                    return FALSE;
                                }
                            }
                        }
                    else if(empty($dealer_phone_number) && $amount_due ==0){
                        $output = "Please Enter Order ID OR Dealer Mobile Number";
                        $userSession = array('error' => $output);
                        $this->session->set_userdata($userSession);
                        return FALSE;
                    } 
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
        $this->notify->insert_state_change("", _247AROUND_COMPLETED, _247AROUND_FOLLOWUP, "Lead Completed Phone: " . $missed_call_leads[0]['phone'], $this->session->userdata('id'), 
                $this->session->userdata('employee_id'),ACTOR_NOT_DEFINE,NEXT_ACTION_NOT_DEFINE, _247AROUND);

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
            $this->notify->insert_state_change("", _247AROUND_CANCELLED, _247AROUND_FOLLOWUP, $data['cancellation_reason'] . " Phone: " . $missed_call_leads[0]['phone'], 
                    $this->session->userdata('id'), $this->session->userdata('employee_id'),ACTOR_BOOKING_CANCELLED,NEXT_ACTION_CANCELLED_BOOKING, _247AROUND);

            $this->session->set_flashdata('cancel_leads', 'Leads has been cancelled for phone ' . $missed_call_leads[0]['phone']);
            redirect(base_url() . "employee/booking/get_missed_calls_view");
        } else {
            $this->session->set_flashdata('cancel_leads', 'Leads had already Cancelled ');
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
        $this->notify->insert_state_change("", _247AROUND_FOLLOWUP, _247AROUND_FOLLOWUP, $this->input->post('updation_reason') . " Phone: " . $missed_call_leads[0]['phone'], 
                $this->session->userdata('id'), $this->session->userdata('employee_id'),ACTOR_FOLLOW_UP,NEXT_ACTION_FOLLOW_UP, _247AROUND);

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

    function booking_upcountry_details($service_center_id, $booking_id, $is_customer_paid, $flat_upcountry) {
        if ($is_customer_paid > 0) {
            $is_customer_paid = 1;
        }
        
        if($flat_upcountry == 1){
            $is_customer_paid = 1;
        }
        $data['data'] = $this->upcountry_model->upcountry_booking_list($service_center_id, $booking_id, true, $is_customer_paid, $flat_upcountry);

        $this->load->view('service_centers/upcountry_booking_details', $data);
    }

    /**
     *  @desc : This function is used to validate the uploaded support file for order id
     *  @param : void
     *  @return : boolean
     */
    function validate_upload_orderId_support_file() {
        if(!empty($_FILES['support_file'])){
            for($i=0; $i< count(array($_FILES['support_file']['tmp_name'])); $i++) {
                if (!empty($_FILES['support_file']['tmp_name'][$i])) {
                    $MB = 1048576;
                    if ($_FILES['support_file']['size'][$i] < 5 * $MB) {
                        //return true;
                        continue;
                    } else {
                        $this->form_validation->set_message('validate_upload_orderId_support_file', 'Uploaded File Size Must be Less than 5MB');
                        return false;
                    }
                } else if(count($_FILES['support_file']['tmp_name']) > 1) {
                    //return true;
                    $this->form_validation->set_message('validate_upload_orderId_support_file', 'No File Selected!! ');
                    return false;
                }
            }
        }
        return true;
    }

    /**
     *  @desc : This function is used to upload the support file for order id to s3 and save into database
     *  @param : $booking_id,
     *  @param : $tmp_name,
     *   @param : $error,
     *  @param : $name,
     *  @return : boolean/string
     */
    function upload_orderId_support_file($booking_id, $tmp_name, $error, $name) {

        $support_file_name = false;
        //UPLOAD_ERR_NO_FILE mean: No file was uploaded
        if (($error != UPLOAD_ERR_NO_FILE) && !empty($tmp_name)) {

            $tmpFile = $tmp_name;
            $support_file_name = $booking_id . '_orderId_support_file_' . substr(md5(uniqid(rand(0, 9))), 0, 15) . "." . explode(".", $name)[1];
            //move_uploaded_file($tmpFile, TMP_FOLDER . $support_file_name);
            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "purchase-invoices/" . $support_file_name;
            $upload_file_status = $this->s3->putObjectFile($tmpFile, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            if($upload_file_status){
                //Logging success for file uppload
                //log_message('info', __METHOD__ . 'Support FILE has been uploaded sucessfully for booking_id: '.$booking_id);
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
        $data['c2c'] = $this->booking_utilities->check_feature_enable_or_not(CALLING_FEATURE_IS_ENABLE);
       
        $data['saas_module'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
       $this->miscelleneous->load_nav_header();
        if(strtolower($data['booking_status']) == 'pending'){
            $this->load->view('employee/view_pending_bookings', $data);
        }else{
            $this->load->view('employee/view_bookings_by_status', $data);
        }
    }
    /**
     *  @desc : This function is used to show view for booking based on booking status type
     *  @param : $status string
     *  @param : $booking_id string
     *  @return : void();
     */
    public function open_pending_bookings(){
        $data['booking_status'] = "Pending";
        $data['booking_id'] = "";
        $bookingIDString = $this->input->post('booking_id_status');
        $data['bookingIDString'] = $bookingIDString;
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/view_pending_bookings', $data);
    }
    /**
     * @desc This function is uses to load filter view in the Pending booking Page
     */
    function get_booking_filter_view($status){
        log_message('info', __METHOD__);
        $partnerWhere = [];
//        $partnerWhere['partners.is_active'] = 1;
        $vendorJoin = NULL;
        $vendorWhere['service_centres.active'] = 1;
        $is_am=0;
        if($this->session->userdata('is_am') == '1'){
            $am_id = $this->session->userdata('id');
            $partnerWhere = array('agent_filters.agent_id' => $am_id);
            $is_am=1;
            $data['state'] = $this->partner_model->getpartner_data('distinct agent_filters.state',$partnerWhere,"",null,1,$is_am);
        }
        else {
            $data['state'] = $this->booking_model->get_advance_search_result_data("state_code","DISTINCT(state)",NULL,NULL,NULL,array('state'=>'ASC'));
        }
        if($this->session->userdata('user_group') == _247AROUND_RM){
            $rm_id = $this->session->userdata('id');
            $vendorWhere['service_centres.rm_id'] = $rm_id;
        }
        if($this->session->userdata('user_group') == _247AROUND_ASM){
            $asm_id = $this->session->userdata('id');
            $vendorWhere['service_centres.asm_id'] = $asm_id;
        }
        $request_type=array('Repair','Installation');
        $data['request_type']=$request_type;
        $data['partners'] = $this->partner_model->getpartner_data('distinct partners.id,partners.public_name',$partnerWhere,"",null,1,$is_am);
        $data['sf'] = $this->reusable_model->get_search_result_data("service_centres","service_centres.id,name",$vendorWhere,$vendorJoin,NULL,array("name"=>"ASC"),NULL,array());
        $data['services'] = $this->booking_model->selectservice();
        $data['cities'] = $this->booking_model->get_advance_search_result_data("cities","DISTINCT(city)",NULL,NULL,NULL,array('city'=>'ASC'));
        $data['status'] = $status;
        $data['saas_module'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        if($status == _247AROUND_PENDING){
            $data['rm'] = $this->reusable_model->get_search_result_data("employee","employee.id,employee.full_name",array("groups IN ('"._247AROUND_RM."','"._247AROUND_ASM."')"=>NULL),NULL,NULL,array("full_name"=>"ASC"),NULL,array());
        
            echo $this->load->view('employee/pending_booking_filter_page', $data, TRUE);
        } else {
            echo $this->load->view('employee/closed_booking_filter_page', $data, TRUE);
        }
        
    }
    
    
    /**
     *  @desc : This function is used to get bookings based on booking status type
     *  @param : $status string
     *  @return : $output JSON
     */
    public function get_bookings_by_status($status){
        $booking_status = trim($status);
        //RM Specific Bookings
         $sfIDArray =array();
         $partnerArray = array();
        if($this->session->userdata('user_group') == _247AROUND_RM){
            $rm_id = $this->session->userdata('id');
            $rmServiceCentersData= $this->reusable_model->get_search_result_data("service_centres","group_concat(id) as service_centres_id",array("rm_id"=>$rm_id),NULL,NULL,NULL,NULL,NULL);
            $sfIDList = $rmServiceCentersData[0]['service_centres_id'];
            $sfIDArray = explode(",",$sfIDList);
        }
        if($this->session->userdata('user_group') == _247AROUND_ASM){
            $asm_id = $this->session->userdata('id');
            $rmServiceCentersData= $this->reusable_model->get_search_result_data("service_centres","group_concat(id) as service_centres_id",array("asm_id"=>$asm_id),NULL,NULL,NULL,NULL,NULL);
            $sfIDList = $rmServiceCentersData[0]['service_centres_id'];
            $sfIDArray = explode(",",$sfIDList);
        }
        //AM Specific Bookings
        if($this->session->userdata('is_am') == '1'){
            $am_id = $this->session->userdata('id');
            $where = array('agent_filters.agent_id' => $am_id,'partners.is_active'=>1,'agent_filters.entity_type'=>_247AROUND_EMPLOYEE_STRING);
            $join = array("agent_filters" => "booking_details.partner_id=agent_filters.entity_id and booking_details.state=agent_filters.state and agent_filters.entity_type = '"._247AROUND_EMPLOYEE_STRING."' ");            
        }
        $data = $this->get_bookings_data_by_status($booking_status,$sfIDArray,$partnerArray,'booking_id');
        $post = $data['post'];
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->booking_model->count_all_bookings_by_status($post),
            "recordsFiltered" =>  $this->booking_model->count_filtered_bookings_by_status($post,$sfIDArray,$partnerArray),
            "data" => $data['data'],
        );
        echo json_encode($output);
    }
    
    
    /**
     *  @desc : This function is used to get bookings based on booking status type
     *  @param : $booking_status string
     *  @return : $output Array()
     */
    private function get_bookings_data_by_status($booking_status,$sfIDArray,$partnerArray=array(),$group_by="") { 
        $post = $this->get_post_data();
        $new_post = $this->get_filterd_post_data($post,$booking_status,'booking');
         if($this->input->post('bulk_booking_id')){
             $select = "booking_details.id as booking_primary_id,services.services,users.name as customername,penalty_on_booking.active as penalty_active, booking_files.file_name as booking_files_bookings,
            users.phone_number, booking_details.*,service_centres.name as service_centre_name, employee.full_name as rm_name, emp_asm.full_name as asm_name,
            service_centres.district as city, service_centres.primary_contact_name,booking_unit_details.appliance_brand,DATE_FORMAT(STR_TO_DATE(booking_details.booking_date, '%Y-%m-%d'), '%d-%b-%Y') as booking_date,
            service_centres.primary_contact_phone_1,DATE_FORMAT(STR_TO_DATE(booking_details.booking_date,'%Y-%m-%d'),'%d-%b-%Y') as booking_day,booking_details.create_date,booking_details.partner_internal_status,
            DATE_FORMAT(STR_TO_DATE(booking_details.initial_booking_date,'%Y-%m-%d'),'%d-%b-%Y') as initial_booking_date_as_dateformat, (CASE WHEN spare_parts_details.booking_id IS NULL THEN 'no_spare' ELSE
            MIN(DATEDIFF(CURRENT_TIMESTAMP , spare_parts_details.acknowledge_date)) END) as spare_age,
            DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.initial_booking_date, '%Y-%m-%d')) as booking_age,service_centres.state,booking_details.request_type";
            $list = $this->booking_model->get_bookings_by_status($new_post,$select,$sfIDArray,0,'Spare',0,array(),array(),$group_by);
         }
         else{
             $select = "booking_details.id as booking_primary_id,services.services,users.name as customername,penalty_on_booking.active as penalty_active, booking_files.file_name as booking_files_bookings,
            users.phone_number, booking_details.*,service_centres.name as service_centre_name, employee.full_name as rm_name, emp_asm.full_name as asm_name,
            service_centres.district as city, service_centres.primary_contact_name,booking_unit_details.appliance_brand,DATE_FORMAT(STR_TO_DATE(booking_details.booking_date, '%Y-%m-%d'), '%d-%b-%Y') as booking_date,
            service_centres.primary_contact_phone_1,DATE_FORMAT(STR_TO_DATE(booking_details.booking_date,'%Y-%m-%d'),'%d-%b-%Y') as booking_day,booking_details.create_date,booking_details.partner_internal_status,
            DATE_FORMAT(STR_TO_DATE(booking_details.initial_booking_date,'%Y-%m-%d'),'%d-%b-%Y') as initial_booking_date_as_dateformat,
            DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.initial_booking_date, '%Y-%m-%d')) as booking_age,service_centres.state,booking_details.request_type";
            $list = $this->booking_model->get_bookings_by_status($new_post,$select,$sfIDArray,0,'Spare',0,array(),array(),$group_by);
         }
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
        $request_type_booking=$this->input->post('request_type_booking');
        $internal_status = $this->input->post('internal_status');
        $request_type = $this->input->post('request_type');
        $current_status = $this->input->post('current_status');
        $actor = $this->input->post('actor');
        $asm_id = $this->input->post('asm_id');
        $rm_id = $this->input->post('rm_id');
        $is_upcountry = $this->input->post('is_upcountry');
        $completed_booking=$this->input->post('completed_booking');
        $bulk_booking_id = NULL;
        if($this->session->userdata('is_am') == '1'){
            $am_id = $this->session->userdata('id');
        }
        if($this->input->post('bulk_booking_id')){
            $bulk_booking_id = $this->input->post('bulk_booking_id');
        }
         if($this->input->post('state')){
            $state = $this->input->post('state');
        }
        if($bulk_booking_id){
            $post['where_in']['booking_details.booking_id'] =  explode(",",$bulk_booking_id);
        }
        if($type == 'booking'){
            if($booking_status == _247AROUND_COMPLETED || $booking_status == _247AROUND_CANCELLED){
                   $post['where']['type']= 'Booking';
                   if(!empty($completed_booking)) {
                        switch ($completed_booking){
                                case 'a':
                                    if($booking_status == _247AROUND_COMPLETED){
                                        $post['where']['((service_center_closed_date IS NOT NULL AND booking_details.internal_status != "'.SF_BOOKING_CANCELLED_STATUS.'" AND booking_details.current_status != "'._247AROUND_CANCELLED.'") OR (booking_details.current_status="'.$booking_status.'"))'] = NULL;
                                    }
                                    else{
                                        $post['where']['((service_center_closed_date IS NOT NULL AND booking_details.internal_status = "'.SF_BOOKING_CANCELLED_STATUS.'" AND booking_details.current_status != "'._247AROUND_COMPLETED.'") OR (booking_details.current_status="'.$booking_status.'"))'] = NULL;
                                    }
                                    break;
                                case 'b':
                                     if($booking_status == _247AROUND_COMPLETED){
                                        $post['where']['(service_center_closed_date IS NOT NULL AND booking_details.internal_status != "'.SF_BOOKING_CANCELLED_STATUS.'")'] = NULL;
                                    }
                                    else{
                                        $post['where']['(service_center_closed_date IS NOT NULL AND booking_details.internal_status = "'.SF_BOOKING_CANCELLED_STATUS.'")'] = NULL;
                                    }
                                    $post['where_in']['booking_details.current_status'] =  array(_247AROUND_RESCHEDULED,_247AROUND_PENDING);
                                    break;
                                case 'c':
                                   $post['where']['booking_details.current_status'] = $booking_status;
                                    break;
                            }
                   }
                   else{
                       if($booking_status == _247AROUND_COMPLETED){
                          $post['where']['((service_center_closed_date IS NOT NULL AND booking_details.internal_status != "'.SF_BOOKING_CANCELLED_STATUS.'") OR (booking_details.current_status="'.$booking_status.'"))'] = NULL;
                       }
                       else{
                          $post['where']['((service_center_closed_date IS NOT NULL AND booking_details.internal_status = "'.SF_BOOKING_CANCELLED_STATUS.'") OR (booking_details.current_status="'.$booking_status.'"))'] = NULL;
                        }
                    }
            }else if(strtolower($booking_status) == 'pending' && empty ($booking_id)){
                if(($this->session->userdata('is_am') == '1') || ($this->session->userdata('user_group') == _247AROUND_RM) || ($this->session->userdata('user_group') == _247AROUND_ASM)){
                    $post['where']  = array("(booking_details.current_status = '"._247AROUND_RESCHEDULED."' OR (booking_details.current_status = '"._247AROUND_PENDING."' ))"=>NULL,
                        "service_center_closed_date IS NULL"=>NULL);
                    $post['where_not_in']['booking_details.internal_status']  = array(SPARE_PARTS_SHIPPED,SPARE_OOW_SHIPPED,SF_BOOKING_CANCELLED_STATUS,SF_BOOKING_COMPLETE_STATUS,SPARE_PARTS_SHIPPED_BY_WAREHOUSE);
                }
                else{
                    $post['where']  = array("booking_details.current_status IN ('"._247AROUND_PENDING."','"._247AROUND_RESCHEDULED."')" => NULL,"service_center_closed_date IS NULL"=>NULL);
                    $post['where_not_in']['booking_details.internal_status']  = array(SPARE_PARTS_SHIPPED,SPARE_OOW_SHIPPED,SF_BOOKING_CANCELLED_STATUS,SF_BOOKING_COMPLETE_STATUS,SPARE_PARTS_SHIPPED_BY_WAREHOUSE);
                }
                $post['order_performed_on_count'] = TRUE;
            }
        }else if($type == 'query'){
            $post['order'] = array(array('column' => 0,'dir' => 'desc'));
            $post['where']['booking_details.current_status'] = $booking_status;
            $post['order_by'] = "CASE WHEN booking_details.internal_status = 'Missed_call_confirmed' THEN 'a' WHEN  booking_details.booking_date = '' THEN 'b' WHEN  booking_details.booking_date = '' THEN 'b' ELSE 'c' END , booking_day";
        }
        if(!empty($internal_status)){
            $post['where_in']['booking_details.partner_internal_status'] =  explode(",",$internal_status);
        }
        if(!empty($is_upcountry)){
            $post['where_in']['booking_details.is_upcountry'] =  1;
            if($is_upcountry == 'no'){
                $post['where_in']['booking_details.is_upcountry'] =  0;
            }
        }
        if(!empty($current_status)){
            $post['where']['booking_details.current_status'] =  $current_status;
        }
        if(!empty($actor)){
             $post['where']['booking_details.actor'] =  $actor;
        }
        if(!empty($rm_id)){
             $post['where']['service_centres.rm_id'] =  $rm_id;             
        }
        if(!empty($am_id)){
             $post['where']["agent_filters.agent_id"] = $am_id;
             $post['where']["agent_filters.is_active"] = 1;
             $post['where']["agent_filters.entity_type"] = _247AROUND_EMPLOYEE_STRING;
             $post['join']['agent_filters'] =  "booking_details.partner_id=agent_filters.entity_id and booking_details.state=agent_filters.state and agent_filters.entity_type='"._247AROUND_EMPLOYEE_STRING."' ";
        }
        if(!empty($request_type)){
            $post['where_in']['booking_details.request_type'] =  explode(",",$request_type);
        }
        if(!empty($request_type_booking))
        {
            $request_type_first_string="SUBSTRING_INDEX(request_type, ' ', 1)";
            $repair='Repair';$installation='Installation';$repair_arr=array('Repair','Repeat');
           if($request_type_booking==$repair)
           {
                $post['where']['(request_type Like "%Repair%" Or request_type Like "%Repeat%")'] =  NULL;
           }
           elseif($request_type_booking==$installation)
           {
               $post['where']['request_type Not Like "%Repair%" AND request_type Not Like "%Repeat%"'] =  NULL;
           }
               
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
            $bookingDateArray = explode(" - ", $booking_date);
            $post['where']['STR_TO_DATE(booking_details.booking_date, "%Y-%m-%d") >= '] =  date("Y-m-d", strtotime(trim($bookingDateArray[0])));
            $post['where']['STR_TO_DATE(booking_details.booking_date, "%Y-%m-%d") < '] = date("Y-m-d", strtotime(trim($bookingDateArray[1])));
        }
        
        if(!empty($city)){
            $post['where']['booking_details.city = '] =  trim($city);
        }
        if(!empty($state)){
            $post['where']['service_centres.state = '] =  trim($state);
        }
        $post['column_order'] = array('booking_day',NULL,NULL,NULL,NULL,"initial_booking_date_as_dateformat","booking_age");
       // $post['column_order'] = array('booking_day',NULL,NULL,NULL,NULL,"initial_booking_date_as_dateformat");
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
        $search = str_replace( array( '\'', '"', ',' , ';', '<', '>' ), '', $this->input->post('search'));
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
        $saas_flag = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $response_db = $this->getBookingCovidZoneAndContZone($order_list->city);
        if(!empty($response_db)){
        $result = json_decode($response_db[0]['zone'],true);
        $response = $result;
        }
        if(!empty($response)){
        $districtZoneType = $response['zone'];

        if (strpos($districtZoneType, 'Red') !== false) {
        $districtZoneType = '<br><span class="label label-danger">COVID ZONE</span>';
        }
        if (strpos($districtZoneType, 'Orange') !== false) {
        $districtZoneType = '<br><span class="label label-warning">COVID ZONE</span>';
        }
        if (strpos($districtZoneType, 'Green') !== false) {
        $districtZoneType = '<br><span class="label label-success">COVID ZONE</span>';
        }   
        }else{

        $districtZoneType = '<span class=""></span>';   
        }
        if($order_list->is_upcountry === '1'){
            $sn = "<i class='fa fa-road' aria-hidden='true' onclick='";
            $sn .= "open_upcountry_model(".'"'.$order_list->assigned_vendor_id.'"';
            $sn .= ', "'.$order_list->booking_id.'"';
            $sn .= ', "'.$order_list->amount_due.'"';
            $sn .= ', "'.$order_list->flat_upcountry.'"';
            $sn .= ")' style='color:red; font-size:20px;'></i>";
        }else{
            $sn = "";
        }
        $c2c= $this->input->post('c2c');
        if($c2c){
            $booking_primary_id = (!empty($order_list->booking_primary_id) ? $order_list->booking_primary_id : "");
            $call_btn = "<button type='button' class='btn btn-sm btn-color' onclick='";            
            $call_btn .= "outbound_call(".'"'.$order_list->booking_primary_contact_no.'"';
            $call_btn .= ",$booking_primary_id)' '><i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i></button>";
        }
        
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
        
        
        
        $row[] = $no.$sn."<br>".$districtZoneType;
        $row[] = "<a href='"."https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/jobcards-pdf/".$order_list->booking_jobcard_filename."'>$order_list->booking_id</a>";
        $row[] = "<a class='col-md-12' href='".base_url()."employee/user/finduser?phone_number=".$order_list->phone_number."'>$order_list->customername</a>"."<b>".$order_list->booking_primary_contact_no."</b>";
        $row[] = $order_list->services;
        $row[] = "<a href='".base_url()."employee/vendor/viewvendor/".$order_list->assigned_vendor_id."'>$order_list->service_centre_name</a>";
        $row[] = $order_list->city;
        $row[] = $order_list->request_type;
        if(!empty($order_list->closed_date))
        {
            $row[] = $this->miscelleneous->get_formatted_date($order_list->closed_date);
        }
        else
        {
            $row[] ="" ;
        }
        if($c2c){
           $row[] = $call_btn;
         }
         if(!$saas_flag){
            if($booking_status === _247AROUND_COMPLETED){
                $row[] = "<a id='edit' class='btn btn-sm btn-color' href='".base_url()."employee/booking/get_complete_booking_form/".$order_list->booking_id."' title='Edit'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
                $row[] = "<a id='cancel' class='btn btn-sm btn-color' href='".base_url()."employee/booking/get_cancel_form/".$order_list->booking_id."' title='Cancel'><i class='fa fa-times' aria-hidden='true'></i></a>";
            }else if($booking_status === _247AROUND_CANCELLED){
                $row[] = "<a id='edit' class='btn btn-sm btn-color' href='".base_url()."employee/booking/get_complete_booking_form/".$order_list->booking_id."' title='Edit' target='_blank' disabled><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
            }
            if($booking_status === _247AROUND_COMPLETED &&  $order_list->amount_paid > 0){
                 $row[] = "<a id='open' class='btn btn-sm btn-color' href='".base_url()."employee/booking/get_convert_booking_to_pending_form/".$order_list->booking_id."/".$booking_status."' title='Open' target='_blank' disabled><i class='fa fa-calendar' aria-hidden='true'></i></a>";

            } else if($this->session->userdata('user_group') !== _247AROUND_ADMIN || $this->session->userdata('user_group') !== _247AROUND_DEVELOPER){
                if($booking_status === _247AROUND_CANCELLED && strtotime($order_list->closed_date) <= strtotime("-1 Months")){
                    $row[] = "<a id='open' class='btn btn-sm btn-color' href='".base_url()."employee/booking/get_convert_booking_to_pending_form/".$order_list->booking_id."/".$booking_status."' title='Open' target='_blank' disabled><i class='fa fa-calendar' aria-hidden='true'></i></a>";
                }else{
                    $row[] = "<a id='open' class='btn btn-sm btn-color' href='".base_url()."employee/booking/get_convert_booking_to_pending_form/".$order_list->booking_id."/".$booking_status."' title='Open' target='_blank'><i class='fa fa-calendar' aria-hidden='true'></i></a>";
                }
            }else{

                $row[] = "<a id='open' class='btn btn-sm btn-color' href='".base_url()."employee/booking/get_convert_booking_to_pending_form/".$order_list->booking_id."/".$booking_status."' title='Open' target='_blank'><i class='fa fa-calendar' aria-hidden='true'></i></a>";
            }
        }
        
        $row[] = "<a id='view' class='btn btn-sm btn-color' href='".base_url()."employee/booking/viewdetails/".$order_list->booking_id."' title='view' target='_blank'><i class='fa fa-eye' aria-hidden='true'></i></a>";
        
        if($booking_status === _247AROUND_COMPLETED){
            $unreachableCount = $order_list->rating_unreachable_count;
            if($order_list->rating_unreachable_count == 0){
                $unreachableCount = "";
            }
            $row[] = "<a class='btn btn-sm btn-color' href='".base_url()."employee/booking/get_rating_form/".$order_list->booking_id."/".$booking_status."' title='Rating' target='_blank' $rating_btn_disabled><i class='fa fa-star-o' aria-hidden='true' >"
                    . "</i></a><p style='text-align:center;color: red;'>$unreachableCount</p>";
        }
        
        if ($order_list->nrn_approved==0) {
            
            $row[] = "<a class='btn btn-sm btn-color col-md-12' href='".base_url()."employee/vendor/get_escalate_booking_form/".$order_list->booking_id."/".$booking_status."' title='Add Penalty' target='_blank'><i class='fa fa-plus-square' aria-hidden='true'></i></a>".$penalty_row;

        }else{
            $row[] = "<a style='background-color: #fa0202;' class='btn btn-sm  col-md-12' href='#' title='Add Penalty'><i class='fa fa-plus-square' aria-hidden='true'></i></a>".$penalty_row;

        }
        
        
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
        $data['brands'] = $this->booking_model->get_advance_search_result_data("appliance_brands","distinct(brand_name) as brand_name",NULL,NULL,NULL,array('brand_name'=>'ASC'));
        $data['category'] = $this->booking_model->get_advance_search_result_data("service_centre_charges","DISTINCT(category)",NULL,NULL,NULL,array('category'=>'ASC'));
        $data['capacity'] = $this->booking_model->get_advance_search_result_data("service_centre_charges","DISTINCT(capacity)",NULL,NULL,NULL,array('capacity'=>'ASC'));
        $data['request_type'] = $this->booking_model->get_advance_search_result_data("booking_details","DISTINCT(request_type) as request_type",NULL,NULL,NULL,array('request_type'=>'ASC'));
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
        if(!empty($receieved_Data['service_center_closed_date'])){
           $whereArray = $this->get_dates_range_where_array($receieved_Data['service_center_closed_date']," - ",$whereArray,$dbfield_mapinning_option['service_center_closed_date'],"Y-m-d");
        }
        if(!empty($receieved_Data['close_date'])){
          $whereArray = $this->get_dates_range_where_array($receieved_Data['close_date']," - ",$whereArray,$dbfield_mapinning_option['close_date'],"Y-m-d");
        }
        if(!empty($receieved_Data['created_date'])){
          $whereArray = $this->get_dates_range_where_array($receieved_Data['created_date']," - ",$whereArray,$dbfield_mapinning_option['created_date'],"Y-m-d");
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
    function get_advance_search_result_data($receieved_Data,$select,$selectarray=array(),$column_sort_array = array()){
        $finalArray = array();
        //array of filter options name and affected database field by them
        $dbfield_mapinning_option = array('booking_date'=>'STR_TO_DATE(booking_details.booking_date, "%Y-%m-%d")', 'service_center_closed_date'=>'date(booking_details.service_center_closed_date)', 'close_date'=>'date(booking_details.closed_date)', 'created_date'=>'date(booking_details.create_date)',
            'partner'=>'booking_details.partner_id','sf'=>'booking_details.assigned_vendor_id','city'=>'booking_details.city','current_status'=>'booking_details.current_status',
            'internal_status'=>'booking_details.internal_status','product_or_service'=>'booking_unit_details.product_or_services','upcountry'=>'booking_details.is_upcountry',
            'rating'=>'booking_details.rating_stars','service'=>'booking_details.service_id','categories'=>'booking_unit_details.appliance_category','capacity'=>'booking_unit_details.appliance_capacity',
            'brand'=>'booking_unit_details.appliance_brand','paid_by'=>'booking_unit_details.customer_net_payable','request_type'=>'booking_unit_details.price_tags','state'=>'booking_details.state');
        // array of filtered options and there selected values (which can be handled by direct where condition)
        $whereOptionArray = elements(array('partner','city','sf','internal_status','product_or_service','upcountry','rating','categories','capacity','brand'), $receieved_Data);
        //join condition array table name and join condition
        $joinDataArray = array("bookings_sources"=>"bookings_sources.partner_id=booking_details.partner_id",
            "service_centres"=>"service_centres.id=booking_details.assigned_vendor_id",
            "services"=>"services.id=booking_details.service_id","booking_unit_details"=>"booking_unit_details.booking_id=booking_details.booking_id",
            "users" => "booking_details.user_id = users.user_id",
            "booking_unit_details" => "booking_unit_details.booking_id=booking_details.booking_id",
            "spare_parts_details" => "booking_details.booking_id = spare_parts_details.booking_id",
            "inventory_master_list as requested_inventory" => "spare_parts_details.requested_inventory_id = requested_inventory.inventory_id",
            "inventory_master_list as shipped_inventory" => "spare_parts_details.shipped_inventory_id = shipped_inventory.inventory_id",
            "employee as emp_asm" => "service_centres.asm_id = emp_asm.id",
            "employee as emp_rm" => "service_centres.rm_id = emp_rm.id",
            "agent_filters" => "booking_details.partner_id = agent_filters.entity_id AND agent_filters.entity_type = '"._247AROUND_EMPLOYEE_STRING."' AND (TRIM(UPPER(agent_filters.state)) = TRIM(UPPER(booking_details.state)))",
            "employee as emp_am" => "agent_filters.agent_id = emp_am.id",
            "booking_cancellation_reasons" => "booking_details.cancellation_reason = booking_cancellation_reasons.id");
        // limit array for pagination
        $limitArray = array('length'=>$receieved_Data['length'],'start'=>$receieved_Data['start']);
       // all where condition array
        $whereArray = $this->get_all_where_array_for_advance_search($dbfield_mapinning_option,$whereOptionArray,$receieved_Data);
        //where in array
        $currentStatusArray = explode(",",$receieved_Data['current_status']);
        $serviceArray = explode(",",$receieved_Data['service']);
        $stateArray = explode(",",$receieved_Data['state']);
        $requestTypeArray = explode(",",$receieved_Data['request_type']);
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
         if($receieved_Data['request_type']){
            $whereInArray['booking_details.request_type'] = $requestTypeArray;
        }
        $JoinTypeTableArray = array('service_centres'=>'left','bookings_sources'=>'left','booking_unit_details'=>'left','services'=>'left', 'spare_parts_details'=>'left','inventory_master_list as requested_inventory' => 'left', 'inventory_master_list as shipped_inventory' => 'left', 'employee as emp_asm' => 'left', 'employee as emp_rm' => 'left', 'agent_filters' => 'left', 'employee as emp_am' => 'left', 'booking_cancellation_reasons' => 'left');
      
       //Performing Sorting on datatable
       if(!empty($receieved_Data['order']))
       {
            $order=$receieved_Data['order'];
            $column_sort=$order['0']['column'];
            $sort_type=$order['0']['dir'];
            if(!empty($selectarray))
            {
                $order_by_column=$selectarray[$column_sort];
                // If a column alias is used change use its original name for sorting instead of its alias. 
                if(!empty($column_sort_array[$column_sort])){
                    $order_by_column=$column_sort_array[$column_sort];
                }
                $sorting_type=$sort_type;
            }
       }
       else
       {
           $order_by_column='booking_details.booking_id';
           $sorting_type='ASC';
       }
        
        //process query and get result from database
        //After server side shorting
        $result = $this->booking_model->get_advance_search_result_data("booking_details",$select,$whereArray,$joinDataArray,$limitArray,array($order_by_column=>$sorting_type),$whereInArray,$JoinTypeTableArray);
        //convert database result into a required formate needed for datatales
        for($i=0;$i<count($result);$i++){
            $index = $receieved_Data['start']+($i+1);
            $tempArray = array_values($result[$i]);
            array_unshift($tempArray, $index);
            $finalArray[] = $tempArray;
        }
        //create final array required for database table
        $data['draw'] = $receieved_Data['draw'];
        // get filtered records from table
        $data['recordsFiltered'] = $this->booking_model->get_advance_search_result_count("booking_details",$select,$whereArray,$joinDataArray,NULL,NULL,$whereInArray,$JoinTypeTableArray);
        $data['data'] = $finalArray;
        return $data;
    }   
    function get_advance_search_result_view(){
        $receieved_Data = $this->input->post(); 
                // select field to display
        $select = "booking_details.booking_id,bookings_sources.source,booking_details.city,service_centres.company_name,services.services,booking_unit_details.appliance_brand,"
                . "booking_unit_details.appliance_category,booking_unit_details.appliance_capacity,booking_details.request_type,booking_unit_details.product_or_services,booking_details."
                . "current_status,booking_details.internal_status, emp_asm.full_name as asm_name,emp_rm.full_name as rm_name,emp_am.full_name as am_name,spare_parts_details.parts_requested,requested_inventory.part_number as requested_part_number,"
                . "spare_parts_details.parts_shipped,shipped_inventory.part_number as shipped_part_number,DATE_FORMAT(STR_TO_DATE(spare_parts_details.shipped_date, '%Y-%m-%d'), '%d-%b-%Y') as parts_shipped_date,booking_details.actor as Dependency";
        // Array of Original names of column, will be used while performing sorting.
        $column_order = [null,null,null,null,null,null,null,null,null,null,null,null,null,'emp_asm.full_name','emp_rm.full_name','emp_am.full_name',null,'requested_inventory.part_number',null,'shipped_inventory.part_number',null,'booking_details.actor'];
        $select_explode=explode(',',$select);
        array_unshift($select_explode,"s.no");
        $data = $this->get_advance_search_result_data($receieved_Data,$select,$select_explode,$column_order);
        // Save Query in Log against Logged-In Agent
        $this->miscelleneous->save_query_log('advance_search_log', $this->db->last_query());
        foreach ($data['data'] as $index=>$serachResultData){
            $booking_with_link = "<a href =".base_url() . "employee/booking/viewdetails/".$serachResultData[1]." target='_blank'>".$serachResultData[1]."</a>";
            $data['data'][$index][1] = $booking_with_link;
        }                                     
        // return Table data
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
       if(isset($receieved_Data['request_type'])){
            $receieved_Data['request_type'] = implode(',',$receieved_Data['request_type']);
       }
       else{
           $receieved_Data['request_type'] = '';
       }
       $is_not_empty = FALSE;
       foreach($receieved_Data as $values){
           if($values){
               $is_not_empty = TRUE;
               break;
           }
       }
      
        $select = "users.name as customer_name, booking_details.booking_id,booking_unit_details.sub_order_id,bookings_sources.source,booking_details.city,service_centres.company_name,services.services,booking_unit_details.appliance_brand,"
                . "booking_unit_details.appliance_category,booking_unit_details.appliance_capacity,booking_unit_details.model_number,booking_unit_details.price_tags,booking_unit_details.product_or_services,booking_details."
                . "current_status,booking_details.internal_status,booking_details.order_id,booking_details.type,booking_details.partner_source,booking_details.partner_current_status,booking_details.partner_internal_status,"
                . "booking_details.booking_address,booking_details.booking_pincode,booking_details.district,booking_details.state,"
                . "booking_details.booking_primary_contact_no,booking_details.booking_alternate_contact_no, DATE_FORMAT(`booking_details`.`booking_date`,'%d-%m-%Y'),DATE_FORMAT(`booking_details`.`initial_booking_date`,'%d-%m-%Y'), "
                ."(CASE WHEN current_status  IN ('"._247AROUND_PENDING."','"._247AROUND_RESCHEDULED."','"._247AROUND_FOLLOWUP."') THEN DATEDIFF(CURDATE(),STR_TO_DATE(booking_details.initial_booking_date,'%Y-%m-%d')) ELSE '' END) as age_of_booking, "
                ."(CASE WHEN current_status  IN('Completed','Cancelled') THEN DATEDIFF(date(booking_details.service_center_closed_date),STR_TO_DATE(booking_details.initial_booking_date,'%Y-%m-%d')) ELSE '' END) as TAT, "
                . "booking_details.booking_timeslot,booking_details.booking_remarks,"
                . "booking_details.query_remarks,booking_cancellation_reasons.reason as cancellation_reason,"
                . "booking_details.reschedule_reason,service_centres.name,booking_details.rating_stars,booking_details.rating_comments,"
                . "booking_details.closing_remarks,"
                . "booking_details.count_reschedule,booking_details.count_escalation,(CASE WHEN (booking_details.is_upcountry = 1) THEN 'Yes' ELSE 'No' END) as is_upcountry,booking_details.upcountry_pincode,"
                . "booking_details.upcountry_distance,(CASE WHEN booking_details.is_penalty = 1 THEN 'Yes' ELSE 'No' END) as is_penalty,booking_details.create_date,booking_details.update_date,"
                . "booking_details.service_center_closed_date as service_center_closed_date, "
                . "booking_details.closed_date as 247around_closed_date, "
                . "emp_asm.full_name as asm_name,emp_rm.full_name as rm_name,emp_am.full_name as am_name,spare_parts_details.parts_requested,requested_inventory.part_number as requested_part_number,"
                . "spare_parts_details.parts_shipped,shipped_inventory.part_number as shipped_part_number,DATE_FORMAT(STR_TO_DATE(spare_parts_details.shipped_date, '%Y-%m-%d'), '%d-%m-%Y') as parts_shipped_date,booking_details.actor as Dependency";
         
       if($is_not_empty){
                $receieved_Data['length'] = -1;
                $receieved_Data['start'] = 0;
                $receieved_Data['draw'] = 1;
                $data = $this->get_advance_search_result_data($receieved_Data,$select);
               
                $headings = array("S.no","Customer Name ","Booking ID","Sub Order ID","Partner","City","Service Center","Service","Brand","Category","Capacity","Model Number","Request Type","Product/Service","Final Status Level 1","Internal Status","Order_ID","Type",
                    "Partner Source","Partner Current Status","Final Status Level 2","Booking Address","Pincode","District","State","Primary Contact Number","Alternate Number ","Current Booking Date","First Booking Date","Age Of Booking",
                    "TAT","Booking Timeslot","Booking Remarks","Query Remarks","Cancellation Reason","Reschedule_reason","Vendor(SF)",
                    "Rating","Vendor Rating Comments","Closing Remarks","Count Reschedule","Count Escalation",
                    "Is Upcountry","Upcountry Pincode","Upcountry Distance","IS Penalty","Create Date","Update Date","Service Center Closed Date","247Around Closed","ASM Name","RM Name","AM Name","Part Name Requested", "Part Type Requested", "Part Name Shipped", "Part Type Shipped", "Part Shipped Date", "Dependency");
                $this->miscelleneous->downloadCSV($data['data'],$headings,"booking_search_summary");   
       }
       else{
           redirect(base_url() . "employee/booking/booking_advance_search");
       }
       ob_end_clean();
    }

    /**
     *  @desc : This function is used to get Last recieve spare
     *  @param : $booking_id
     *  @return : $Date
     */
    function getBookingLastSpare($booking_id){
     $receivedate =  $this->booking_model->getBookingLastSpare($booking_id)[0];
     return $receivedate->acknowledge_date;
    }



        /**
     * @desc this function is used get zone and contaminant center of booking pincode
     * @param  $pincode
     * @return Array
     */
    function getBookingCovidZoneAndContZone($city){
        $return_data = $this->indiapincode_model->getPinCoordinates($city);
        // if(!empty($coordinates)){
        // $lat = $coordinates[0]['latitude'];
        // $long = $coordinates[0]['longitude'];
        // }
        // else{
        // $url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$pincode."&key=".GEOCODING_GOOGLE_API_KEY;
        // $data = file_get_contents($url);
        // $result = json_decode($data, true);
        // if(isset($result['results'][0]['geometry'])){
        // $lat = $result['results'][0]['geometry']['location']['lat'];
        // $long = $result['results'][0]['geometry']['location']['lng'];   
        // }else{
        // $lat="";
        // $long=""; 
        // }

        // }

        if(!empty($return_data)){
        return $return_data;
        }else{
        return $return_data;
        }

    }

    /**
     * @desc this function is used get zone and contaminant center of booking pincode
     * @param  $pincode
     * @return Array
     */
    function getCovidZone($district){
        $return_data = $this->indiapincode_model->getPinCoordinates($district);
        if(!empty($response)){
        $districtZoneType = $response['zone_color'];
        if (strpos($districtZoneType, 'Red') !== false) {
        $districtZoneType = '<br><span class="label label-danger">COVID ZONE</span>';
        }
        if (strpos($districtZoneType, 'Orange') !== false) {
        $districtZoneType = '<br><span class="label label-warning">COVID ZONE</span>';
        }
        if (strpos($districtZoneType, 'Green') !== false) {
        $districtZoneType = '<br><span class="label label-success">COVID ZONE</span>';
        }
        }else{
        $districtZoneType = '<br><span></span>';   
        }
        echo $districtZoneType;
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
            $sn .= ', "'.$order_list->flat_upcountry.'"';
            $sn .= ")' style='color:red; font-size:20px;'></i>";
        }else{
            $sn = "";
        }

        $last_spare = $this->getBookingLastSpare($order_list->booking_id);
        $response_db = $this->booking_utilities->getBookingCovidZoneAndContZone($order_list->district);
        if(!empty($response_db)){
        $result = json_decode($response_db[0]['zone'],true);
        $response = $result;
        }
        if(!empty($response)){
        $districtZoneType = $response['zone'];

        if (strpos($districtZoneType, 'Red') !== false) {
        $districtZoneType = '<br><span class="label label-danger" >COVID ZONE</span>';
        }
        if (strpos($districtZoneType, 'Orange') !== false) {
        $districtZoneType = '<br><span  class="label label-warning" >COVID ZONE</span>';
        }
        if (strpos($districtZoneType, 'Green') !== false) {
        $districtZoneType = '<br><span class="label label-success" >COVID ZONE</span>';
        }
        }else{
        $districtZoneType = '<span class=""></span>';  
        }


        
        
        if ($order_list->current_status == 'Completed' && empty($order_list->rating_stars )){
            $rating_btn_disabled = "";
        }else{
            $rating_btn_disabled = "disabled";
        }
        
        if(empty($order_list->penalty_active)){
            $penalty_row = "<a class='btn btn-sm btn-color' href='javascript:void(0);' title='Remove Penalty' style='cursor:not-allowed;opacity:0.5;'><i class='fa fa-times-circle' aria-hidden='true'></i></a>";
        }else if($order_list->penalty_active === '1'){
            $penalty_modal = "onclick='";
            $penalty_modal .= "get_penalty_details(".'"'.$order_list->booking_id.'"';
            $penalty_modal .= ', "'.$booking_status.'"';
            $penalty_modal .= ', "'.$order_list->assigned_vendor_id.'"';
            $penalty_modal .= ")' ";
            $penalty_row = "<a class='btn btn-sm btn-color' href='javascript:void(0);' title='Remove Penalty' $penalty_modal><i class='fa fa-times-circle' aria-hidden='true'></i></a>";
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
        if(!empty($order_list->service_centre_name)){
            $state = $order_list->state;
        }else{
            $state = "";
        }
        if ($order_list->assigned_vendor_id == "") {
            $complete =  "<a target='_blank' class='btn btn-sm btn-color btn-sm disabled' "
            . "href=" . base_url() . "employee/booking/get_complete_booking_form/$order_list->booking_id title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true' ></i></a>";
        } else {
            if ($order_list->current_status == _247AROUND_PENDING || $order_list->current_status == _247AROUND_RESCHEDULED) {
                $redirect_url = base_url()."employee/booking/get_complete_booking_form/".$order_list->booking_id;
                $complete = "<a target='_blank' class='btn btn-sm btn-color btn-sm' "
                . "href=" . base_url() ."employee/booking/get_edit_request_type_form/".urlencode(base64_encode($order_list->booking_id))."/".urlencode(base64_encode($redirect_url))." title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true' ></i></a>";
            } else if ($order_list->current_status == 'Review') {
                $complete = "<a target='_blank' class='btn btn-sm btn-color btn-sm' "
                . "href=" . base_url() . "employee/booking/review_bookings/$order_list->booking_id title='Complete'><i class='fa fa-eye-slash' aria-hidden='true' ></i></a>";
            } else {
                $complete = "<a target='_blank' class='btn btn-sm btn-color btn-sm disabled' "
                . "href=" . base_url() . "employee/booking/get_complete_booking_form/$order_list->booking_id title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true' ></i></a>";
            }
        }
        
//        if (!is_null($order_list->assigned_vendor_id) && !is_null($order_list->booking_jobcard_filename) && ($order_list->mail_to_vendor == 0)) {
//            $mail =  "<a  id='b_notes" . $no . "' class='btn btn-sm btn-color' onclick='show(this.id)' title='Mail'><i class='fa fa-envelope-o' aria-hidden='true'></i></a>";
//            $mail .= "<div class='dialog' id='bookingMailForm" . $no . "'>";
//            $mail .= "<form class='mailform'>";
//            $mail .= "<textarea style='width:200px;height:80px;' id='valueFromMyButton" . $no . "' name='valueFromMyButton" . $no . "' placeholder='Enter Additional Notes'></textarea>";
//            $mail .= "<input type='hidden' id='booking_id" . $no . "' name='booking_id" . $no . "' value=$order_list->booking_id >";
//            $mail .= "<div align='center'>";
//            $mail .= "<a id='btnOK" . $no . "' class='btn btn-sm btn-success' onclick='send_email_to_vendor(" . $no . ");'>Ok</a>";
//            $mail .= "</div>";
//            $mail .= "</form>";
//            $mail .= "</div>";
//        } else {
//            $mail = "<a class='btn btn-sm btn-color disabled' href='" . base_url() . "employee/bookingjobcard/send_mail_to_vendor/$order_list->booking_id' title='Mail'><i class='fa fa-envelope-o' aria-hidden='true' ></i></a>";
//        }
        
//        if (!is_null($order_list->assigned_vendor_id) && !is_null($order_list->booking_jobcard_filename) && ($order_list->mail_to_vendor)) {
//            $r_mail = "<a id='r_notes" . $no . "' class='btn btn-sm btn-color' onclick='show(this.id)' title='Remainder Mail' ><i class='fa fa-clock-o' aria-hidden='true'></i></a>";
//            $r_mail .= "<div class='dialog' id='reminderMailForm" . $no . "'>";
//            $r_mail .= "<form class='remindermailform'>";
//            $r_mail .= "<textarea style='width:200px;height:80px;' id='reminderMailButton" . $no . "' name='reminderMailButton" . $no . "' placeholder='Enter Additional Notes'></textarea>";
//            $r_mail .= "<input type='hidden' id='booking_id" . $no . "' name='booking_id" . $no . "' value=$order_list->booking_id >";
//            $r_mail .= "<div align='center'>";
//            $r_mail .= "<a id='btnOK" . $no . "' class='btn btn-sm btn-success' onclick='send_reminder_email_to_vendor(" . $no . ");'>Ok</a>";
//            $r_mail .= "</div>";
//            $r_mail .= "</form>";
//            $r_mail .= "</div>";
//        } else {
//            $r_mail = "<a class='btn btn-sm btn-color disabled' href = '" . base_url() . "employee/bookingjobcard/send_reminder_mail_to_vendor/$order_list->booking_id ' title = 'Reminder Mail'><i class='fa fa-clock-o' aria-hidden='true'></i></a>";
//        }
        
        if(is_null($order_list->assigned_vendor_id)){
            $d_btn = "disabled";
        }else{
            $d_btn = "";
        }
        $saas_flag = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
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
        


        $ageString = $order_list->booking_age." days";
         if($this->input->post('bulk_booking_id')){
            if((($order_list->is_upcountry == '1' && $order_list->booking_age >2) || ($order_list->is_upcountry == '0' && $order_list->booking_age >1)) && ($order_list->spare_age >1 || $order_list->spare_age=='no_spare')){
                $ageString = $order_list->booking_age." days <i class='fa fa-exclamation-triangle' style = 'color:red';></i>";
            }
         }
        $row[] = $no.$sn.$districtZoneType;

        if($order_list->booking_files_bookings){
            $row[] = "<a href='"."https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/jobcards-pdf/".$order_list->booking_jobcard_filename."'>$order_list->booking_id</a><p><a target='_blank' href='https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$order_list->booking_files_bookings."'  title = 'Purchase Invoice Verified' aria-hidden = 'true'><img src='".base_url()."images/varified.png' style='width:20px; height: 20px;'></a></p>"
                    . "<p><span id='cancelled_reason_".$order_list->booking_id."' > <img  style='width: 83%;' src='".base_url()."images/loader.gif' /></span></p>"
                    . "<p><span > <img id='spare_delivered_".$order_list->booking_id."' style='width: 83%;' src='".base_url()."images/loader.gif' /></span></p>";
        }
        else{
            $row[] = "<a href='"."https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/jobcards-pdf/".$order_list->booking_jobcard_filename."'>$order_list->booking_id </a>"
                    . "<p><span id='cancelled_reason_".$order_list->booking_id."' > <img  style='width: 83%;' src='".base_url()."images/loader.gif' /></span></p>"
                    . "<p><span> <img id='spare_delivered_".$order_list->booking_id."' style='width: 83%;' src='".base_url()."images/loader.gif' /></span></p>";
        }
       
        $row[] = "<a class='col-md-12' href='".base_url()."employee/user/finduser?phone_number=".$order_list->phone_number."'>$order_list->customername</a>"."<b>".$order_list->booking_primary_contact_no."</b>";
        $row[] = "<b>".$order_list->services."</b>"."<br>".$order_list->request_type;
        $row[] = $order_list->appliance_brand;
        $row[] = $order_list->booking_date." / ".$order_list->booking_timeslot;
        $row[] = $ageString;
        // show internal_status instead of partner_internal_status because we are using internal_status in queries
        $row[] = $escalation." ".$order_list->internal_status." <br> Latest Ack Date : ".$last_spare;
        $row[] = "<a target = '_blank' href='".base_url()."employee/vendor/viewvendor/".$order_list->assigned_vendor_id."'>$sf</a><div id='cancelled_rejected_".$order_list->booking_id."'> <img style='width: 25%;' src='".base_url()."images/loader.gif' /></div>";
        $row[] = $order_list->asm_name;
        //$row[] = $order_list->rm_name;
        $row[] = $state;
        if(isset($saas_flag) && (!$saas_flag)) {
            $row[] = '<button type="button" title = "Booking Contacts" class="btn btn-sm btn-color" data-toggle="modal" data-target="#relevant_content_modal" id ="'.$order_list->booking_id.'" onclick="show_contacts(this.id,1)">'
                    . ' <span class="glyphicon glyphicon-user"></span></button>';
        }
        $row[] = "<a id ='view' class ='btn btn-sm btn-color' href='".base_url()."employee/booking/viewdetails/".$order_list->booking_id."' title = 'view' target = '_blank'><i class = 'fa fa-eye' aria-hidden = 'true'></i></a>";
        $row[] = "<a target = '_blank' id = 'edit' class = 'btn btn-sm btn-color ".((!empty($order_list->service_center_closed_date) || (!empty($order_list->service_center_current_status) && $order_list->service_center_current_status == SF_BOOKING_INPROCESS_STATUS)) ? 'disabled' : '')."' "
            . "href=" . base_url() . "employee/booking/get_reschedule_booking_form/$order_list->booking_id title='Reschedule'><i class = 'fa fa-calendar' aria-hidden='true' ></i></a>";
        $row[] = "<a target = '_blank' id = 'cancel' class = 'btn btn-sm btn-color' href = '".base_url()."employee/booking/get_cancel_form/".$order_list->booking_id."' title = 'Cancel'><i class = 'fa fa-times' aria-hidden = 'true'></i></a>";
        $row[] = $complete;
        $row[] ="<a target = '_blank' class = 'btn btn-sm btn-color' href = '" . base_url() . "employee/bookingjobcard/prepare_job_card_using_booking_id/$order_list->booking_id' title = 'Job Card'> <i class = 'fa fa-file-pdf-o' aria-hidden = 'true' ></i></a>";
        $row[] = "<a target ='_blank' class = 'btn btn-sm btn-color' href = '" . base_url() . "employee/booking/get_edit_booking_form/$order_list->booking_id' title = 'Edit Booking'> <i class = 'fa fa-pencil-square-o' aria-hidden = 'true'></i></a>";
        $row[] = "<a target ='_blank' class = 'btn btn-sm btn-color' href = '" . base_url() . "employee/vendor/get_reassign_vendor_form/$order_list->booking_id ' title = 'Re-assign' $d_btn> <i class = 'fa fa-repeat' aria-hidden = 'true'></i></a><script> $(document).ready(function(){load_cancelled_status_admin('".$order_list->booking_id."');booking_cancelled_rejected_count('".$order_list->booking_id."'); load_delivered_status('".$order_list->booking_id."')})</script>";


        if ($order_list->nrn_approved==0) {
             $row[] = "<a target = '_blank' class = 'btn btn-sm btn-color' href = '".base_url()."employee/vendor/get_vendor_escalation_form/$order_list->booking_id' title = 'Escalate' $esc><i class='fa fa-circle' aria-hidden='true'></i></a>";
        }else{

            $row[] = "<a style='background-color: #fa0202;' class = 'btn btn-sm btn-color' href = '#' title = 'Escalate' $esc><i class='fa fa-circle' aria-hidden='true'></i></a>";
        }
        $row[] = $penalty_row;
        $row[] = "<a class = 'btn btn-sm btn-color' title = 'Helper Document' data-toggle='modal' data-target='#showBrandCollateral' onclick=get_brand_collateral('".$order_list->booking_id."')><i class='fa fa-file-text-o' aria-hidden='true'></i></a>";
        
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
        
        $data['c2c'] = $this->booking_utilities->check_feature_enable_or_not(CALLING_FEATURE_IS_ENABLE);
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
        $select = "services.services,users.name as customername, users.phone_number,booking_details.* ,DATE_FORMAT(STR_TO_DATE(booking_details.booking_date, '%Y-%m-%d'), '%d-%b-%Y') as booking_day,booking_unit_details.appliance_description, booking_unit_details.appliance_brand,booking_details.id as booking_primary_id";

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
        $c2c = $this->input->post('c2c');
        $sms_json = json_encode(array('phone_number'=>$order_list->booking_primary_contact_no, 'booking_id'=>$order_list->booking_id, 'user_id' => $order_list->user_id, 'appliance_brand' => $order_list->appliance_brand, 'service' => $order_list->services, 'partner_id' => $order_list->partner_id, 'booking_state'=>$order_list->state));
        $row[] = $no." <div><input type = 'hidden' id = 'service_id_".$no."' value = '".$order_list->service_id."'><input type = 'hidden' id = 'pincode_".$no."' value = '".$order_list->booking_pincode."'></div>";
        $row[] = $order_list->booking_id;
        $row[] = "<a href='".base_url()."employee/user/finduser?phone_number=$order_list->phone_number'>$order_list->customername / <b>$order_list->phone_number </b></a>";
        
        $row[] = $order_list->services;
        $row[] = $order_list->booking_day . "/" . $order_list->booking_timeslot;
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
        if($c2c){
             $row[] = "<button type='button' class = 'btn btn-sm btn-color' onclick = 'outbound_call($order_list->booking_primary_contact_no, $order_list->booking_primary_id)'><i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i></button>";
             $row[] = "<button type='button' class = 'btn btn-sm btn-color' json-data='$sms_json' onclick = 'send_whtasapp_number(this)'><i class = 'fa fa-envelope-o fa-lg' aria-hidden = 'true'></i></button>";
        }
        
        
        $row[] = "<a id ='view' class ='btn btn-sm btn-color' href='".base_url()."employee/booking/viewdetails/".$order_list->booking_id."' title = 'view' target = '_blank'><i class = 'fa fa-eye' aria-hidden = 'true'></i></a>";
        if($query_status != _247AROUND_CANCELLED){
            $row[]  = "<a class = 'btn btn-sm btn-color' href = '" . base_url() . "employee/booking/get_edit_booking_form/$order_list->booking_id' title = 'Update' target ='_blank'><i class = 'fa fa-pencil-square-o' aria-hidden = 'true'></i></a>";
        }
        
        if($query_status == _247AROUND_CANCELLED){
            if(($this->session->userdata('user_group') !== _247AROUND_ADMIN || $this->session->userdata('user_group') !== _247AROUND_DEVELOPER) && strtotime($order_list->closed_date) <= strtotime("-1 Months")){
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
        $partnerArray = array();
        $this->miscelleneous->load_nav_header();
        $partners = $this->partner_model->getpartner();
        foreach($partners as $partnersDetails){
            $partnerArray[$partnersDetails['id']] = $partnersDetails['public_name'];
        }
        $this->load->view('employee/bulk_booking_search',array("partnerArray"=>$partnerArray));
    }
    function get_input_for_bulk_search($receieved_Data){
        $inputBulkData = $copy_inputBulkData = array();
        $inputBulkDataTemp = explode("\n",$receieved_Data['bulk_input']);
        array_map('trim', $inputBulkDataTemp);
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
        $copy_inputBulkData = $inputBulkData;
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
            foreach($copy_inputBulkData as $value) {
             $query_check='Q-';
             $start_string=substr($value,0,2);
             if($start_string!==$query_check){
                 $copy_inputBulkData[]=$query_check.$value;
                 if (($key = array_search($value,$inputBulkData)) !== false) {
                     unset($inputBulkData[$key]);
                 }
             }
         }
        }

        $where = array();
        if(array_key_exists('partner_id', $receieved_Data)){
            if($receieved_Data['partner_id'] != 'option_holder'){
                $where['booking_details.partner_id'] = $receieved_Data['partner_id'];
            }
        }
        return array("inputBulkData"=>$inputBulkData,"copy_inputBulkData"=>$copy_inputBulkData,"fieldName"=>$fieldName,"onlyName"=>$onlyName,"where"=>$where);
    }
    function get_bulk_search_result_data($receieved_Data,$select){
        $finalArray = array();
       
        if(array_key_exists("is_unit_details", $receieved_Data)){
            $joinDataArray["booking_unit_details"] = "booking_unit_details.booking_id=booking_details.booking_id";
        }
        $joinDataArray["bookings_sources"] = "bookings_sources.partner_id=booking_details.partner_id";
        $joinDataArray["service_centres"] = "service_centres.id=booking_details.assigned_vendor_id";
        $joinDataArray["services"] = "services.id=booking_details.service_id";
        $joinDataArray["users"] = "booking_details.user_id = users.user_id";

        // limit array for pagination
        $limitArray = array('length'=>$receieved_Data['length'],'start'=>$receieved_Data['start']);
        $inputData = $this->get_input_for_bulk_search($receieved_Data);
        $whereArray = NULL;
        $whereInArray = NULL;
        if($receieved_Data['bulk_input']){
            $whereInArray[$inputData['fieldName']] = $inputData['copy_inputBulkData'];
        }
        if($inputData['where']){
            $whereArray = $inputData['where'];
        }
        $JoinTypeTableArray = array('service_centres'=>'left','bookings_sources'=>'left','booking_unit_details'=>'left','services'=>'left');
       //process query and get result from database
        $result = $this->booking_model->get_advance_search_result_data("booking_details",$select,$whereArray,$joinDataArray,$limitArray,array("booking_details.booking_id"=>"ASC"),
                $whereInArray,$JoinTypeTableArray);
       //start Logic to create record for all not found entries
        if(isset($result[0])){
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
       
       $bookingDetailsSelect = "booking_details.booking_id,booking_details.request_type,booking_details.order_id,booking_details.booking_primary_contact_no,bookings_sources.source,booking_details.city,"
                . "service_centres.company_name,services.services,booking_details.current_status,booking_details.internal_status";
        $unitDetailsSelect =", 'Not_found' as purchase_date, 'Not_found' as appliance_brand,'Not_found' as appliance_category,'Not_found' as appliance_capacity,'Not_found' as price_tags,'Not_found' as product_or_services, 'Not_found' as partner_serial_number, 'Not Found' as serial_number, 'Not_found' as customer_total";
       if($this->input->post("is_unit_details")){
           $unitDetailsSelect = ",  booking_unit_details.purchase_date as purchase_date, booking_unit_details.appliance_brand,booking_unit_details.appliance_category,booking_unit_details.appliance_capacity,booking_unit_details.price_tags, "
         . "booking_unit_details.product_or_services, booking_unit_details.partner_serial_number, booking_unit_details.serial_number,customer_total";
       }
       $select = $bookingDetailsSelect.$unitDetailsSelect;
       $data = $this->get_bulk_search_result_data($receieved_Data,$select);
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
       $bookingDetailsSelect = "users.name as customer_name, booking_details.booking_id,bookings_sources.source,booking_details.city,service_centres.company_name,services.services,booking_details."
                . "current_status,booking_details.order_id,booking_details.type,booking_details.partner_source,booking_details.partner_current_status,booking_details.partner_internal_status,"
                . "booking_details.booking_address,booking_details.booking_pincode,booking_details.district,booking_details.state,"
                . "booking_details.booking_primary_contact_no,booking_details.booking_date,booking_details.initial_booking_date,"
                ."(CASE WHEN current_status  IN ('"._247AROUND_PENDING."','"._247AROUND_RESCHEDULED."','"._247AROUND_FOLLOWUP."') THEN DATEDIFF(CURDATE(),STR_TO_DATE(booking_details.initial_booking_date,'%Y-%m-%d')) ELSE '' END) as age_of_booking,"
                ."(CASE WHEN current_status  IN('Completed','Cancelled') THEN DATEDIFF(date(booking_details.service_center_closed_date),STR_TO_DATE(booking_details.initial_booking_date,'%Y-%m-%d')) ELSE '' END) as TAT, "
                . " booking_details.booking_timeslot,booking_details.booking_remarks,"
                . "booking_details.query_remarks,booking_details.cancellation_reason,"
                . "booking_details.reschedule_reason,service_centres.name,booking_details.vendor_rating_stars,booking_details.vendor_rating_comments,"
                . "booking_details.closing_remarks,"
                . "booking_details.count_reschedule,booking_details.count_escalation,booking_details.is_upcountry,booking_details.upcountry_pincode,"
                . "booking_details.upcountry_distance,booking_details.is_penalty,booking_details.create_date,booking_details.update_date,"
                . "booking_details.service_center_closed_date as service_center_closed_date, "
                . "booking_details.closed_date as 247around_closed_date";
        $unitDetailsSelect =", 'Not_found' as purchase_date, 'Not_found' as appliance_brand,'Not_found' as appliance_category,'Not_found' as appliance_capacity,'Not_found' as price_tags,'Not_found' as product_or_services, 'Not_found' as partner_serial_number, 'Not Found' as serial_number, 'Not_found' as customer_total";
       if($this->input->post("is_unit_details")){
           $unitDetailsSelect = ",booking_unit_details.purchase_date as purchase_date, booking_unit_details.appliance_brand,booking_unit_details.appliance_category,booking_unit_details.appliance_capacity,booking_unit_details.price_tags,"
         . "booking_unit_details.product_or_services, booking_unit_details.partner_serial_number, booking_unit_details.serial_number,customer_total";
       }
       $select = $bookingDetailsSelect.$unitDetailsSelect;
       $data = $this->get_bulk_search_result_data($receieved_Data,$select);
       $headings = array("S.no", "Customer Name","Booking ID","Partner","City","Service Center","Service","Current_status","Order_ID","Type",
                    "Partner Source","Partner Current Status","Partner Internal Status","Booking Address","Pincode","District","State","Primary Contact Number","Current Booking Date","First Booking Date",
                    "Age Of Booking","TAT","Booking Timeslot","Booking Remarks","Query Remarks","Cancellation Reason","Reschedule_reason","Vendor(SF)",
                    "Rating","Vendor Rating Comments","Closing Remarks","Count Reschedule","Count Escalation",
                    "Is Upcountry","Upcountry Pincode","Upcountry Distance","IS Penalty","Create Date","Update Date","Service Center Closed Date","Closed Date", "Purchase Date", "Brand","Category","Capacity","Request Type","Product/Service", "Partner Serial Number", "Sf Serial Number", "Call Charges");
       $this->miscelleneous->downloadCSV($data['data'],$headings,"booking_bulk_search_summary");   
       ob_end_clean();
    }
    
    /**
     * @desc: This function is used to get service_id from Ajax call
     * @params: void
     * @return: string
     */
    function get_service_id(){
        $appliance_list = $this->booking_model->selectservice();
        
        if($this->input->get('is_option_selected')){
            $option = '<option  selected="" disabled="" value="">Select Appliance</option>';
        }else{
            $option = '';
        }
        
        foreach ($appliance_list as $value) {
            $option .= "<option value='" . $value->id . "'";
            $option .= " > ";
            $option .= $value->services . "</option>";
        }
        $option .= '<option value="all" >All</option>';
        echo $option;
    }
    
     /**
     * @desc: This function is used to get cp_id from Ajax call
     * @params: void
     * @return: string
     */
    function get_cp_id(){
       $cp_list = $this->vendor_model->getVendorDetails("id, name", array('is_cp' => 1));
       $option = '';
       $option .= '<option value="all" >All</option>';
        foreach ($cp_list as $value) {
            $option .= "<option value='" . $value['id'] . "'";
            $option .= " > ";
            $option .= $value['name'] . "</option>";
        }
        echo $option;
    }
    
    /**
     * @desc: This function is used to download the data from vendor pincode mapping
     * @params: void
     * @return: string
     */
    function download_serviceability_data() {
        log_message('info', __FUNCTION__ . " Function Start ");
        $this->miscelleneous->create_serviceability_report_csv($this->input->post());
        echo json_encode(array("response" => "success", "path" => base_url() . "file_process/downloadFile/serviceability_report.csv"));
        // $output_file = TMP_FOLDER . "serviceability_report.csv";
        // $subject = 'Servicablity Report from 247Around';
        // $message = 'Hi , <br>Requested Report is ready please find attachment<br>Thanks!';
        // $this->notify->sendEmail(NOREPLY_EMAIL_ID, $this->session->userdata('official_email'), "", "", $subject, $message, $output_file,"Servicablity_Report");
        // log_message('info', __FUNCTION__ . " Function End ".$this->session->userdata('official_email'));
        // unlink($output_file);
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
    
    /**
     * @desc: This function is used show view to create a custom payment link for user
     * @params: void
     * @return: void
     */
    function create_booking_payment_link(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/booking_payment_link');
    }
    
    /**
     * @desc: This function is used to create a custom payment link for user
     * @params: void
     * @return: void
     */
    function process_create_booking_payment_link() {
        log_message("info", __METHOD__ . " Entering...");
        $booking_id_arr = $this->input->post('booking_id');

        if (!empty($booking_id_arr)) {

            $phone_number = $this->input->post('phone_number');
            $email = $this->input->post('email');
            //Either Phone number or email required
            if (empty($phone_number) && empty($email)) {
                $this->session->set_flashdata('err_msg', 'Please Enter Either Phone Number Or Email');
                redirect(base_url() . 'employee/booking/create_booking_payment_link');
            } else {
                /*check if link already created or not.
                 * if already created then do not create new link 
                */
                $data = $this->booking_model->get_payment_link_details('*', array('booking_id' => implode(',', $booking_id_arr)));

                if (empty($data)) {
                    //make booking id like 'SY-1234567899456' so that we can use it in the sql IN() function to process the query
                    $booking_ids = implode(',', array_map(function($id) {
                                return("'$id'");
                            }, $booking_id_arr));
                    
                    $select = "SUM(amount_due) as amount, GROUP_CONCAT(user_id SEPARATOR '_') as customer_id";
                    $where = array("Booking_id IN ($booking_ids)" => NULL);
                    $booking_details = $this->booking_model->get_bookings_count_by_any($select, $where);
                    
                    //if amount paid by customer is 0 then do not create link
                    if (!empty($booking_details[0]['amount'])) {

                        $param_list = array('phone_no' => $phone_number,
                            'email' => $email,
                            'amount' => $booking_details[0]['amount']
                        );

                        //create hash key to verify the payment link when user pay with link
                        $check_sum = preg_replace('^[/+=]^', '', $this->encdec_paytm->getChecksumFromArray($param_list, PAYTM_GATEWAY_MERCHANT_KEY));
                        $insert_data = array('booking_id' => implode(',', $booking_id_arr),
                            'customer_id' => $booking_details[0]['customer_id'],
                            'amount' => $booking_details[0]['amount'],
                            'phone_number' => $phone_number,
                            'email' => $email,
                            'hash_key' => md5($check_sum),
                            'status' => 0,
                            'create_date' => date('Y-m-d H:i:s')
                        );

                        $insert_id = $this->booking_model->insert_payment_link_details($insert_data);

                        if ($insert_id) {
                            log_message("info", __METHOD__ . " data inserted successfully.");

                            $url = base_url() . 'payment/verify_booking_payment/' . $check_sum;

                            $short_url = $this->miscelleneous->getShortUrl($url);
                            
                            $sms['tag'] = "gateway_payment_link_sms";
                            $sms['phone_no'] = $phone_number;
                            $sms['smsData']['link'] = $short_url;
                            $sms['smsData']['amount'] = $booking_details[0]['amount'];
                            $sms['booking_id'] = implode(',', $booking_id_arr);
                            $sms['type'] = "user";
                            $sms['type_id'] = $booking_details[0]['customer_id'];

                            $this->notify->send_sms_msg91($sms);
                            
                            log_message("info", __METHOD__ . " Sms Send to customer successfully");
                            $this->session->set_flashdata('success_msg', 'Sms Send to customer successfully');
                            redirect(base_url() . 'employee/booking/create_booking_payment_link');
                        } else {
                            log_message("info", __METHOD__ . " error in  inserting data.");
                            $this->session->set_flashdata('err_msg', 'Some error Occured!!! Please try again after some time...');
                            redirect(base_url() . 'employee/booking/create_booking_payment_link');
                        }
                    } else {
                        log_message("info", __METHOD__ . " Amount is 0, So link can not be created");
                        $this->session->set_flashdata('err_msg', 'Amount is 0, So link can not be created');
                        redirect(base_url() . 'employee/booking/create_booking_payment_link');
                    }
                } else {
                    log_message("info", __METHOD__ . " Link already created for this combination");
                    $this->session->set_flashdata('err_msg', 'Link already created for this combination');
                    redirect(base_url() . 'employee/booking/create_booking_payment_link');
                }
            }
        } else {
            $this->session->set_flashdata('err_msg', 'Booking Id Can not be empty');
            redirect(base_url() . 'employee/booking/create_booking_payment_link');
        }
    }
    function customer_not_reachable_for_rating($bookingID,$userID,$phone_number){
        //Update unreachable Count in booking state table
        $response = $this->booking_model->update_customer_not_reachable_count($bookingID);
        //Update History Table
        $this->notify->insert_state_change($bookingID, "Customer_unreachable_for_rating", "Completed", "Try to call for rating but customer is not reachbale", 
        $this->session->userdata('id'), $this->session->userdata('employee_id'),NULL,NULL,_247AROUND);
        //Send Rating SMS to Customer
        $userData = $this->reusable_model->get_search_result_data("users","name",array("user_id"=>$userID),NULL,NULL,NULL,NULL,NULL,array());
        $sms['tag'] = "customer_not_reachable_for_rating";
        $sms['smsData']['name'] = $userData[0]['name'];
        $sms['smsData']['poor_rating_number'] = POOR_MISSED_CALL_RATING_NUMBER;
        $sms['smsData']['good_rating_number'] = GOOD_MISSED_CALL_RATING_NUMBER;
        $sms['phone_no'] = $phone_number;
        $sms['type'] = "rating";
        $sms['type_id'] = $userID;
        $sms['booking_id'] = $bookingID;
        $this->notify->send_sms_msg91($sms);
    }
    
    function update_booking_address(){
        log_message('info', __METHOD__. " POST DATA ". print_r($this->input->post(), true));
        ob_clean();
        $address = $this->input->post("address");
        $booking_id = $this->input->post("booking_id");
        $this->booking_model->update_booking($booking_id, array('booking_address' => $address));
        $job_card = array();
        $job_card_url = base_url() . "employee/bookingjobcard/prepare_job_card_using_booking_id/" . $booking_id;
        $this->asynchronous_lib->do_background_process($job_card_url, $job_card);
        echo "Success";
    }
    
    /**
     * @desc This function is used to update price related filed from complete booking from
     * @param String $booking_id
     */
    function update_completed_unit_applinace_details($booking_id) {
        log_message('info', __METHOD__ . " Update " . $booking_id);
        
        $b_unit_id = $this->input->post('b_unit_id');
        $brand = $this->input->post('appliance_brand');
        $category = $this->input->post('appliance_category');
        $capacity = $this->input->post('appliance_capacity');
        $partner_type = $this->input->post('partner_type');

        foreach ($brand as $key => $value) {
            $services_details = array();
            $services_details['appliance_brand'] = $value;
            $services_details['appliance_category'] = $category[$key];
            $services_details['appliance_capacity'] = $capacity[$key];
            foreach ($b_unit_id[$key] as $unit_id) {
                log_message('info', __METHOD__ . " Update price for unit id " . $unit_id);
                $unit_details = $this->booking_model->get_unit_details(array('id' => $unit_id));

                if ($partner_type == OEM) {
                    $result = $this->partner_model->getPrices($unit_details[0]['service_id'], 
                            $services_details['appliance_category'], $services_details['appliance_capacity'], 
                            $unit_details[0]['partner_id'], $unit_details[0]['price_tags'],$services_details['appliance_brand']);
                } else {
                    $result = $this->partner_model->getPrices($unit_details[0]['service_id'], 
                            $services_details['appliance_category'], $services_details['appliance_capacity'], 
                            $unit_details[0]['partner_id'], $unit_details[0]['price_tags'],"");
                }

                if (!empty($result)) {
                    $services_details['vendor_basic_percentage'] = $result[0]['vendor_basic_percentage'];
                    $services_details['customer_total'] = $result[0]['customer_total'];
                    $services_details['partner_net_payable'] = $result[0]['partner_net_payable'];
                    if ($unit_details[0]['price_tags'] == REPAIR_OOW_PARTS_PRICE_TAGS) {
                        
                        $services_details['customer_total'] = $unit_details[0]['customer_total'];
                        $services_details['vendor_basic_percentage'] = $unit_details[0]['vendor_basic_percentage'];
                        
                    }

                    $services_details['around_paid_basic_charges'] = $unit_details[0]['around_paid_basic_charges'];
                    $services_details['around_net_payable'] = $unit_details[0]['around_paid_basic_charges'];
                    
                    $services_details['customer_net_payable'] = $services_details['customer_total'] - $services_details['partner_net_payable'] - $services_details['around_net_payable'];
                    $services_details['partner_paid_tax'] = ($services_details['partner_net_payable'] * $unit_details[0]['tax_rate'])/ 100;

                    $vendor_total_basic_charges =  ($services_details['customer_net_payable'] + $services_details['partner_net_payable'] + $services_details['around_paid_basic_charges'] ) * ($services_details['vendor_basic_percentage']/100);
                    $around_total_basic_charges = ($services_details['customer_net_payable'] + $services_details['partner_net_payable'] + $services_details['around_paid_basic_charges'] - $vendor_total_basic_charges);

                    $services_details['around_st_or_vat_basic_charges'] = $this->booking_model->get_calculated_tax_charge($around_total_basic_charges, $unit_details[0]['tax_rate'] );
                    $services_details['vendor_st_or_vat_basic_charges'] = $this->booking_model->get_calculated_tax_charge($vendor_total_basic_charges, $unit_details[0]['tax_rate'] );

                    $services_details['around_comm_basic_charges'] = $around_total_basic_charges - $services_details['around_st_or_vat_basic_charges'];
                    $services_details['vendor_basic_charges'] = $vendor_total_basic_charges - $services_details['vendor_st_or_vat_basic_charges'];
                    
                    $this->booking_model->update_booking_unit_details_by_any(array('id' => $unit_id), $services_details);
                }
            }
        }
        
        return true;
    }

    function test(){
        $this->notify->sendEmail("noreply@247around.com", "abhaya@247around.com", "", "", "Higehst Quotes", "test", TMP_FOLDER."updated_price_sheet_2020_01_18_09_26_59.xls",'buyback_price_sheet_with_quote');
        //$this->invoice_lib->generate_challan_file('SY-1824041809242', 129);
//        $this->partner_sd_cb->test();
        //$bucket = "bookings-collateral-test";
//        //$directory_xls = "invoices-excel/ARD-PV-1819-0073.pdf";
       // $this->s3->putObjectFile(TMP_FOLDER."17-Jan-2020-14-27-03-hq.xlsx", $bucket, "vendor-partner-docs/17-Jan-2020-14-27-03-hq.xlsx", S3::ACL_PUBLIC_READ);
//        $this->s3->putObjectFile(TMP_FOLDER."Around-1819-1621.xlsx", $bucket, "invoices-excel/Around-1819-1621.xlsx", S3::ACL_PUBLIC_READ);
//        $this->s3->putObjectFile(TMP_FOLDER."copy_Around-1819-1621.xlsx", $bucket, "invoices-excel/copy_Around-1819-1621.xlsx", S3::ACL_PUBLIC_READ);
       // $this->load->library('serial_no_validation');
      //  $a = $this->upcountry_model->getupcountry_for_partner_prepaid(247042);
       // echo "<pre/>"; print_r($a);
//        $this->partner_cb->partner_callback("SF-607901803235");
//        $array = array(
//            "ReferenceID" => "SP-1656351803085551" , 
//            "Status" => "PNDNG_ASGN", 
//            "RequestDetails" => array( 
//                "Reason"=> "ENA",
//                 "Remarks"=> "Engineer not availble"
//                )
//            );
//        
//        $postData['postData'] = json_encode($array, true);
        
        //$this->load->view('employee/paytmApiIntergration');
    }
    
    function getModelForService(){
        $service_id = $this->input->post('service_id');
        $category = $this->input->post('category');
        $brand = $this->input->post('brand');
        $partner_id = $this->input->post('partner_id');
        $capacity = $this->input->post('capacity');
        $partner_type = $this->input->post('partner_type');
        
        $where = array ('partner_appliance_details.service_id' => $service_id,
                        'partner_appliance_details.partner_id' => $partner_id,
                        'partner_appliance_details.category' => $category,
                        'partner_appliance_details.active' => 1,
                        'appliance_model_details.active' => 1, 
            );
        
        if(!empty($capacity)){
            $where['partner_appliance_details.capacity'] = $capacity;
        }
        
        if ($partner_type == OEM) {
            $where['partner_appliance_details.brand'] = $brand;
            $result = $this->partner_model->get_model_number('appliance_model_details.id, appliance_model_details.model_number, model', $where);
        } else {
            $result = $this->partner_model->get_model_number('appliance_model_details.id, appliance_model_details.model_number, model', $where);
        }
        
        if(!empty($result)){
            $flag = false;
            $option = "<option selected disabled>Select Model Number</option>";
            foreach ($result as $value) {
                if(!empty(trim($value['model']))){
                    $flag = true;
                    $option .= "<option value='".$value['model_number']."'>".$value['model_number']."</option>";
                }
                
            }
            if($flag)  {
                $res['status'] = TRUE;
                $res['msg'] = $option;
            } else {
                $res['status'] = FALSE;
                $res['msg'] = 'no data found';
            }
            
        }else{
            $res['status'] = FALSE;
            $res['msg'] = 'no data found';
        }
        echo json_encode($res);
        
    }
    function get_request_type($actor = ""){
        $where = array();
        if(!empty($actor) && $actor != 'blank'){
            $where['actor'] = $actor;
        }
        $whereIN['current_status'] = array(_247AROUND_PENDING,_247AROUND_RESCHEDULED) ;
        $requestTypeArray= $this->reusable_model->get_search_result_data("booking_details","DISTINCT(request_type)",$where,NULL,NULL,array("request_type"=>"ASC"),
        $whereIN,NULL,array());
        $select ="";
        foreach($requestTypeArray as $val){
            $select  = $select. "<option value='".$val['request_type']."'>".$val['request_type']."</option>";
        }
        echo $select;
    }
    function get_internal_status($actor){
        $where = array();
        if($actor != 'blank'){
            $where['actor'] = $actor;
        }
        $whereIN['current_status'] = array(_247AROUND_PENDING,_247AROUND_RESCHEDULED) ;
        $partnerStatusArray = $this->reusable_model->get_search_result_data("booking_details","DISTINCT(partner_internal_status)",$where,NULL,NULL,array("request_type"=>"ASC"),
                $whereIN,NULL,array());
        $select = "";
        foreach($partnerStatusArray as $val){
            $select  = $select. "<option value='".$val['partner_internal_status']."'>".$val['partner_internal_status']."</option>";
        }
        echo $select;
    }
    
    /**
     * @desc: This function is used to get service_id by partner from Ajax call
     * @params: void
     * @return: string
     */
    function get_service_id_by_partner(){
        
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




    
    function download_pending_bookings($status) {
        $arr_post = $this->input->post();
        $bulk_booking_id = !empty($arr_post['bookingIDString']) ? $arr_post['bookingIDString'] : "";
        $booking_status = trim($status);
        //RM Specific Bookings
         $sfIDArray =array();
         $partnerArray = array();
        if($this->session->userdata('user_group') == _247AROUND_RM){
            $rm_id = $this->session->userdata('id');
            $rmServiceCentersData=  $this->reusable_model->get_search_result_data("service_centres","group_concat(service_centres.id) as service_centres_id",array("rm_id"=>$rm_id),NULL,NULL,NULL,NULL,NULL);
            $sfIDList = $rmServiceCentersData[0]['service_centres_id'];
            $sfIDArray = explode(",",$sfIDList);
        }
        if($this->session->userdata('user_group') == _247AROUND_ASM){
            $asm_id = $this->session->userdata('id');
            $rmServiceCentersData=  $this->reusable_model->get_search_result_data("service_centres","group_concat(service_centres.id) as service_centres_id",array("asm_id"=>$asm_id),NULL,NULL,NULL,NULL,NULL);
            $sfIDList = $rmServiceCentersData[0]['service_centres_id'];
            $sfIDArray = explode(",",$sfIDList);
        }
        //AM Specific Bookings
        if($this->session->userdata('is_am') == '1'){
            $am_id = $this->session->userdata('id');
            $post['where']['agent_filters.agent_id'] = $am_id;
            $post['where']['partners.is_active'] = 1;
            $post['where']['agent_filters.entity_type'] = _247AROUND_EMPLOYEE_STRING;
        }
        
        $post['length'] = -1;
        $post['start'] = NULL;
        $post['search_value'] = NULL;
        $post['order'] = NULL;
        $post['draw'] = NULL;
        if(!empty($bulk_booking_id))
        {
            $post['where_in']['booking_details.booking_id'] =  explode(",",$bulk_booking_id);
        }
        if($booking_status == 'Pending'){
            $post['where']['service_center_closed_date IS NULL'] = NULL;
            $post['where']['booking_details.internal_status NOT IN ("'.SPARE_PARTS_SHIPPED.'","'.SPARE_OOW_SHIPPED.'","'.SF_BOOKING_CANCELLED_STATUS.'","'.SF_BOOKING_COMPLETE_STATUS.'","'.SPARE_PARTS_SHIPPED_BY_WAREHOUSE.'")'] = NULL; 
            // Join with employee Table to fetch AM name

            $post['join']['spare_parts_details'] = "booking_details.booking_id  = spare_parts_details.booking_id and spare_parts_details.status!='"._247AROUND_CANCELLED."'";

            $post['join']['partners'] = "booking_details.partner_id  = partners.id";
            $post['join']['agent_filters'] =  "partners.id=agent_filters.entity_id AND agent_filters.state = booking_details.state AND agent_filters.entity_type = '"._247AROUND_EMPLOYEE_STRING."'";
            $post['join']['employee as employee_am'] = "agent_filters.agent_id = employee_am.id";
            $post['join']['inventory_master_list as in_req'] = "spare_parts_details.requested_inventory_id = in_req.inventory_id";
            $post['join']['inventory_master_list as in_sh'] = "spare_parts_details.shipped_inventory_id = in_sh.inventory_id";
            
            // $post['join']['employee as emp_asm'] = "service_centres.asm_id = emp_asm.id";    
            $post['joinTypeArray'] = ['spare_parts_details' => "left",'partners' => "left",'agent_filters' => 'left', 'employee as employee_am' => "left",  'inventory_master_list as in_req' => 'left', 'inventory_master_list as in_sh' => 'left'];
            $post['unit_not_required'] = true;
            
            // Select Statement            
            $select = " booking_details.booking_id as 'Booking ID', "
                    . "DATEDIFF(CURDATE(),STR_TO_DATE(booking_details.initial_booking_date,'%Y-%m-%d')) as Ageing,"
                    . "partners.public_name as Partner, "
                    . "employee_am.full_name as AM,emp_asm.full_name  as  'ASM',service_centres.name as 'Service Center',"
                    . "services.services as 'Product',booking_details.request_type as 'Service Type',booking_details.partner_internal_status as 'Final Status Level 2',"
                    . "users.name as 'Customer Name',users.phone_number as 'Phone',booking_details.booking_pincode as 'Pincode',booking_details.city as 'City',"
                    . "CASE WHEN booking_details.is_upcountry = 1 THEN 'YES' ELSE 'No' END AS 'Is Upcountry',booking_details.upcountry_distance as 'Upcountry Distance',"
                    . "CASE WHEN spare_parts_details.parts_requested != '' THEN 'YES' ELSE 'No' END AS 'Is Part Involved',CASE WHEN (spare_parts_details.auto_acknowledeged = 1 or spare_parts_details.auto_acknowledeged = 2) THEN 'YES' ELSE 'No' END AS 'Is auto Acknowledge',"
                    . "spare_parts_details.shipped_date as 'Part Shipped Date',max(spare_parts_details.acknowledge_date) as 'SF Acknowledged Date',"
                    . "REPLACE(group_concat(spare_parts_details.shipped_parts_type),trim(','),' | ') as 'Shipped Part Type',"
                    . "spare_parts_details.model_number as 'Model',"                                        
                    . "booking_details.state as 'State',booking_details.booking_address as 'Booking Address',"
                    . "service_centres.primary_contact_name as 'SF Primary Contact Name',"
                    . "service_centres.primary_contact_phone_1 as 'SF Primary Contact No',engineer_details.name as 'Engineer Name',booking_details.create_date as 'Create Date',booking_details.initial_booking_date as 'First Booking Date',booking_details.booking_date as 'Current Booking Date',"
                    . "booking_details.booking_remarks as 'Booking Remarks', booking_details.reschedule_reason as 'Reschedule Remarks',"
                    . "booking_details.current_status as 'Final Status Level 1', booking_details.actor as 'Dependency On',"
                    . "REPLACE(group_concat(in_req.part_number),trim(','),' | ') as 'Requested Part Code', REPLACE(group_concat(spare_parts_details.parts_requested),trim(','),' | ') as 'Requested Part Name',"
                    . "REPLACE(group_concat(spare_parts_details.parts_requested_type),trim(','),' | ') as 'Requested Part Type',spare_parts_details.date_of_request as 'Part Requested Date',"
                    . "REPLACE(group_concat(in_sh.part_number),trim(','),' | ')  as 'Shipped Part Code', REPLACE(group_concat(spare_parts_details.parts_shipped),trim(','),' | ') as 'Shipped Part Name',"                                   
                    . "penalty_on_booking.active as 'Penalty Active',employee.full_name as 'RM'";
            // Show Distinct Bookings
            $post['group_by'] = 'booking_details.booking_id';            
            $list =  $this->booking_model->get_bookings_by_status($post,$select,$sfIDArray,1,'',0,array(),array());

        }
        else if($booking_status == 'Completed' || $booking_status == 'Cancelled'){
            $post['where']  = array('booking_details.current_status' => $booking_status,'type' => 'Booking'); 
            
            $select = "booking_details.booking_id as 'Booking ID', users.name as CustomerName, users.phone_number as 'Phone Number', "
                    . "services.services as Services, service_centres.name as 'Service Centre Name', "
                    . "service_centres.district as City, service_centres.primary_contact_name as SF_POC_Name,"
                    . " service_centres.primary_contact_phone_1 as SF_POC_NUMBER,
                        DATE_FORMAT(STR_TO_DATE(booking_details.booking_date,'%Y-%m-%d'),'%d-%b-%Y') as booking_day,booking_details.create_date as 'Create Date',booking_details.partner_internal_status as 'Partner Internal Status',
                       DATE_FORMAT(STR_TO_DATE(booking_details.initial_booking_date,'%Y-%m-%d'),'%d-%b-%Y') as 'Initial Booking Date',DATEDIFF(CURRENT_TIMESTAMP , 
                       STR_TO_DATE(booking_details.initial_booking_date, '%Y-%m-%d')) as 'Booking Age'";
            
            $list = $this->booking_model->get_bookings_by_status($post,$select,$sfIDArray, 2); 
        }
        $newCSVFileName = $booking_status."_booking_".date('j-M-Y-H-i-s') . ".csv";
        $csv = TMP_FOLDER . $newCSVFileName;
        $delimiter = ",";
        $newline = "\r\n";
        $new_report = $this->dbutil->csv_from_result($list, $delimiter, $newline);
        write_file($csv, $new_report);
         //Downloading Generated CSV  
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
    /**
     * @desc This is used to upload order support file from view details page.
     */
    function upload_order_supporting_file(){
        $booking_id = $this->input->post('booking_id');
        $id = $this->input->post('id');
        $file_description_id = $this->input->post('file_description_id');
        $booking_files = array();
        if(!empty($booking_id)){
            $checkValidation = $this->validate_upload_orderId_support_file();
            if($checkValidation) {
                $support_file = $this->upload_orderId_support_file($booking_id, $_FILES['support_file']['tmp_name'][0], $_FILES['support_file']['error'][0], $_FILES['support_file']['name'][0]);
                if(!empty($support_file)){
                    $booking_files['file_name'] = $support_file;
                    $booking_files['file_type'] = $_FILES['support_file']['type'][0];
                    $booking_files['size'] = $_FILES['support_file']['size'][0];
                    if(!empty($id)) {
                        $this->booking_model->update_booking_file($booking_files, array('booking_id' => $booking_id, 'id' => $id));
                    }
                    else {
                        $booking_files['booking_id'] = $booking_id;
                        $booking_files['file_description_id'] = $file_description_id;
                        $booking_files['create_date'] = date("Y-m-d H:i:s");
                        $Status = $this->booking_model->insert_booking_file($booking_files);
                        if(!$Status) {
                            echo json_encode(array('code' => "error", "message" => "Error while inserting support file!!"));
                        }
                    }
                    echo json_encode(array('code' => "success", "name" => $support_file));
                } else {
                    echo json_encode(array('code' => "error", "message" => "File size or file type is not supported"));
                }
            }
            else {
                echo json_encode(array('code' => "error", "message" => "File size or file type is not supported"));
            }
        }
        
    }
    /**
     * @desc This is used to delete support file from update booking page
     */
    function delete_order_supporting_file() {
        $id = $this->input->post('id');
        if(!empty($id)){
            $affectedRows = $this->booking_model->delete_booking_file(array('id' => $id));
            if($affectedRows > 0) {
                echo json_encode(array('status' => "success", "message" => "File Deleted Successfully!!"));
            } else {
                echo json_encode(array('status' => "error", "message" => "No File Deleted!!"));
            }
        }
        else {
            echo json_encode(array('status' => "error", "message" => "Error Ocurred While Deleting File!!"));
        }
    }
    function update_old_spare_booking_tat(){
      $spareData =   $this->reusable_model->get_search_result_data("spare_parts_details","id,booking_id",array("date(date_of_request)>'2018-03-31'"=>NULL),NULL,NULL,array("spare_parts_details.date_of_request"=>"ASC"),NULL,NULL,array());
        if(!empty($spareData)) { 
            foreach($spareData as $values){
                $this->miscelleneous->process_booking_tat_on_spare_request($values['booking_id'],$values['id']);
            }
        }
    }
    function update_old_completed_booking_tat(){
        $bookingData =   $this->reusable_model->get_search_result_data("booking_details","booking_id",array("date(closed_date)>'2018-03-31'"=>NULL),NULL,NULL,array("booking_details.closed_date"=>"ASC"),NULL,NULL,array());
        foreach($bookingData as $values){
            $this->miscelleneous->process_booking_tat_on_completion($values['booking_id']);
        }
    }
    function review_rescheduled_bookings($is_tab = 0){
      $whereIN = $where = $join = array();
        if($this->session->userdata('user_group') == _247AROUND_RM || $this->session->userdata('user_group') == _247AROUND_ASM){
            $sf_list = $this->vendor_model->get_employee_relation($this->session->userdata('id'));
            $serviceCenters = $sf_list[0]['service_centres_id'];
            $whereIN =array("service_center_id"=>explode(",",$serviceCenters));
        }
        if($this->session->userdata('is_am') == '1'){
            $am_id = $this->session->userdata('id');
            $where = array('agent_filters.agent_id' => $am_id,'agent_filters.is_active'=>1,'agent_filters.entity_type'=>_247AROUND_EMPLOYEE_STRING);
            $join['agent_filters'] =  "booking_details.partner_id=agent_filters.entity_id and service_centres.state=agent_filters.state AND agent_filters.entity_type = '"._247AROUND_EMPLOYEE_STRING."' ";
        }
        $data['data'] = $this->booking_model->review_reschedule_bookings_request($whereIN, $where, $join);
        $data['c2c'] = $this->booking_utilities->check_feature_enable_or_not(CALLING_FEATURE_IS_ENABLE);
        if($is_tab == 0){
         $this->miscelleneous->load_nav_header();
        }
        $this->load->view('employee/rescheduled_review', $data);
    }

    function download_review_rescheduled_bookings($is_tab = 0){
    $whereIN = $where = $join = array();
    if($this->session->userdata('user_group') == _247AROUND_RM || $this->session->userdata('user_group') == _247AROUND_ASM){
            $sf_list = $this->vendor_model->get_employee_relation($this->session->userdata('id'));
            $serviceCenters = $sf_list[0]['service_centres_id'];
            $whereIN =array("service_center_id"=>explode(",",$serviceCenters));
        }
        
    if($this->session->userdata('is_am') == '1'){
            $am_id = $this->session->userdata('id');
            $where = array('agent_filters.agent_id' => $am_id,'agent_filters.is_active'=>1,'agent_filters.entity_type'=>_247AROUND_EMPLOYEE_STRING);
            $join['agent_filters'] =  "booking_details.partner_id=agent_filters.entity_id and service_centres.state=agent_filters.state";
        }
     $data['data'] = $this->booking_model->review_reschedule_bookings_request($whereIN, $where, $join);
     $data['c2c'] = $this->booking_utilities->check_feature_enable_or_not(CALLING_FEATURE_IS_ENABLE);
     
     $ReorderdownloadRecord=array();
     $countRecord=0;
     if(is_array($data['data']) && count($data['data']) > 0)
     {
        foreach($data['data'] as $key => $value)
        {
            $ReorderdownloadRecord[$key]['countRecord']=++$countRecord;
            $ReorderdownloadRecord[$key]['booking_id']=$value['booking_id'];
            $ReorderdownloadRecord[$key]['service_center_name']=$value['service_center_name'];
            $ReorderdownloadRecord[$key]['customername']=$value['customername'];
            $ReorderdownloadRecord[$key]['booking_primary_contact_no']=$value['booking_primary_contact_no'];
            $ReorderdownloadRecord[$key]['initial_booking_date']=$this->miscelleneous->get_formatted_date($value['initial_booking_date']);
            $ReorderdownloadRecord[$key]['booking_date']=$this->miscelleneous->get_formatted_date($value['booking_date'])." / ".$value['booking_timeslot'];
            $ReorderdownloadRecord[$key]['reschedule_date_request']=$this->miscelleneous->get_formatted_date($value['reschedule_date_request']);
            $reschedule_reason = $value['reschedule_reason'];
            if(empty($value['reschedule_reason'])){
                $reschedule_reason = $value['internal_status'];
            }
            $ReorderdownloadRecord[$key]['reschedule_reason'] = $reschedule_reason;       
        }    
        $this->miscelleneous->downloadCSV($ReorderdownloadRecord, ['S.No.','Booking Id','Service Center','User Name','User Contact No.','Original Booking Date','Booking Date','Reschedule Booking Date','Reschedule Reason'], 'data_'.date('Ymd-His'));
     }
    }
    
    function review_bookings_by_status($review_status,$offset = 0,$is_partner = 0,$booking_id = NULL, $cancellation_reason_id = NULL, $partner_id = NULL, $state_code = NULL, $request_type = NULL, $min_review_age = 0, $max_review_age = 0, $sort_on = 0, $sort_order = 0, $service_id = 0,$free_paid = 0, $sf_id = 0, $sf_wise_data = 0){
        $arr_request_types = [1 => 'Installation & Demo (Paid)',2 => 'Installation & Demo (Free)', 3 => 'Repair - In Warranty', 4 => 'Repair - Out Of Warranty', 5 => 'Extended Warranty', 6 => 'Gas Recharge', 7 => 'PDI', 8 => 'Repeat Booking', 9 => 'Presale Repair'];
        $data_id = !empty($this->input->post('data_id')) ? $this->input->post('data_id') : "";
        $this->checkUserSession();
        $whereIN = $where = $join = $having = array();
        if(empty($booking_id)) {
            $booking_id  = NULL;
        }
        if($this->session->userdata('user_group') == _247AROUND_RM || $this->session->userdata('user_group') == _247AROUND_ASM){
            $sf_list = $this->vendor_model->get_employee_relation($this->session->userdata('id'));
            $serviceCenters = $sf_list[0]['service_centres_id'];
            $whereIN =array("sc.service_center_id"=>explode(",",$serviceCenters));
        }
        if($this->session->userdata('is_am') == '1'){
            $am_id = $this->session->userdata('id');
            $where['agent_filters.agent_id ='.$am_id] = NULL;
            $where['partners.is_active =1'] = NULL;
            $where["agent_filters.entity_type = '"._247AROUND_EMPLOYEE_STRING."'"] = NULL;
            $join['agent_filters'] =  "partners.id=agent_filters.entity_id AND agent_filters.state = booking_details.state AND agent_filters.entity_type = '"._247AROUND_EMPLOYEE_STRING."'";
        }
        
        $status=$review_status;
        if($review_status === "Completed") {
            $having['count(sc.booking_id)=sum(if(sc.added_by_SF=0,1,0))'] = NULL;
        }
        if($review_status === "Completed_By_SF") {
            $status="Completed";
            $whereIN['sc.added_by_SF'] = [1];
        }
        
        // Do not show Wrong Call Area Bookings in Bookings Cancelled by SF TAB
        //Wrong area bookings will be shown in seperate TAB
        if($review_status == _247AROUND_CANCELLED) {
            if(!empty($cancellation_reason_id)){
                $whereIN['sc.cancellation_reason'] = [$cancellation_reason_id];
            }
            else {
                $where['(sc.cancellation_reason IS NULL OR sc.cancellation_reason <> "'.CANCELLATION_REASON_WRONG_AREA_ID.'")'] = NULL;
            }
        }        
        
        $data['cancellation_reason'] = $this->reusable_model->get_search_result_data("booking_cancellation_reasons", "*", array('id <> '.CANCELLATION_REASON_WRONG_AREA_ID => NULL), NULL, NULL, NULL, NULL, NULL, array());
        $data['cancellation_reason_selected'] = $cancellation_reason_id;
        
        if(!empty($state_code)) {
           $state =  $this->reusable_model->get_search_result_data("state_code", "*", array('state_code' => $state_code), NULL, NULL, NULL, NULL, NULL, array())[0]['state'];
           $whereIN['booking_details.state'] = [$state];
        }
        $data['states'] = $this->reusable_model->get_search_result_data("state_code", "*", array(), NULL, NULL, NULL, NULL, NULL, array());
        $data['state_selected'] = $state_code;

        if(!empty($partner_id)) {
           $whereIN['booking_details.partner_id'] = [$partner_id];
        }
        if(!empty($request_type)) {           
           $request_type_selected = !empty($arr_request_types[$request_type]) ? $arr_request_types[$request_type] : "";   
           $request_type_selected = strtoupper(str_replace(" ", "", $request_type_selected));
           $where['REPLACE(UPPER(booking_details.request_type)," ","") LIKE "%'.$request_type_selected.'%"'] = NULL;
        }
        // Added filter for Review Range
        if(isset($min_review_age) && $min_review_age != FILTER_NOT_DEFINE && $min_review_age != '' && isset($max_review_age) && $max_review_age != FILTER_NOT_DEFINE && $max_review_age != '') {
           $where['(DATEDIFF(CURDATE(),STR_TO_DATE(booking_details.service_center_closed_date,"%Y-%m-%d")) BETWEEN "'.$min_review_age.'" AND "'.$max_review_age.'") '] = NULL;
        }
        // Added Filter of Service 
        if(!empty($service_id)){
            $whereIN['booking_details.service_id'] = [$service_id];
        }
        // Added Filter of Free/Paid
        if(!empty($free_paid)){
            if($free_paid == "Yes"){
               $where['booking_details.amount_due'] = '0';
            }
            else{
               $where['booking_details.amount_due != 0'] = NULL;
            }
        }
        // Added Filter of SF
        if(!empty($sf_id) && $sf_id != FILTER_NOT_DEFINE ){
             $whereIN['booking_details.assigned_vendor_id'] = [$sf_id];
        }
        // put all selected values in array to show these filter values above filtered data
        $data['partners'] = $this->reusable_model->get_search_result_data("partners", "*", array(), NULL, NULL, NULL, NULL, NULL, array());
        $data['request_types'] = $arr_request_types;
        $data['partner_selected'] = !empty($partner_id) ? $partner_id : 0;
        $data['request_type_selected'] = !empty($request_type) ? $request_type : 0;
        $data['min_review_age_selected'] = !empty($min_review_age) ? $min_review_age : '';
        $data['max_review_age_selected'] = !empty($max_review_age) ? $max_review_age : '';
        $data['sort_on_selected'] = $sort_on;
        $data['sort_order_selected'] = $sort_order;
        $data['service_selected'] = !empty($service_id) ? $service_id : 0;
        $data['free_paid_selected'] = !empty($free_paid) ? $free_paid : 0;
        $data['sf_selected'] = !empty($sf_id) ? $sf_id : 0;
        $data['sf_wise_data'] = $sf_wise_data;
        
        // Add sorting
        $orderBY = NULL;
        if(!empty($sort_on) && !empty($sort_order)){
            $orderBY = " order by ".$sort_on." ".$sort_order;
        }
        
        $total_rows = $this->service_centers_model->get_admin_review_bookings($booking_id,$status,$whereIN,$is_partner,NULL,-1,$where,0,NULL,NULL,0,$join,$having);
        
        if(!empty($total_rows) || !empty($sf_wise_data)){
            $data['per_page'] = 50;
            $data['offset'] = $offset;
            $data['charges'] = $this->booking_model->get_booking_for_review($booking_id,$status,$whereIN,$is_partner,$offset,$data['per_page'],$having, $where, $orderBY, $join);
            $data['status'] = $status;
            $data['review_status'] = $review_status;
            $data['total_rows'] = count($total_rows);
            $data['total_pages'] = $data['total_rows']/$data['per_page'];
            $data['is_partner'] = $is_partner;
            
            // Function to get Bookings for calculating warranty status
            $arrBookingsData = [];
            if(!empty($data['charges']))
            { 
                $arrBookingsData = array_map(function($recData) {
                    $arrData['partner_id'] = $recData['partner_id'];
                    $arrData['service_id'] = $recData['service_id'];
                    $arrData['booking_id'] = $recData['booking_id'];
                    $arrData['booking_create_date'] = $recData['booking_create_date'];
                    $arrData['model_number'] = !empty($recData['unit_details'][0]['model_number']) ? $recData['unit_details'][0]['model_number'] : "";
                    $arrData['purchase_date'] = !empty($recData['unit_details'][0]['sf_purchase_date']) ? $recData['unit_details'][0]['sf_purchase_date'] : "";
                    $arrData['in_warranty_period'] = 12;
                    $arrData['extended_warranty_period'] = 0;
                    // Choose only Videocon bookings whose model and dop exists
                    // ADDED THIS CONDITION ALSO ($recData['partner_id'] != VIDEOCON_ID) 
                    if(empty($arrData['purchase_date']) || $arrData['purchase_date'] == '0000-00-00'):
                        return;
                    endif;
                    return $arrData;
                },$data['charges']);
                
                $arrBookingsData = array_filter($arrBookingsData);                
                $arrBookingsData = array_chunk($arrBookingsData, 50);
                $data['bookings_data'] = $arrBookingsData;
            }
            if (!empty($data['charges'])) {
                foreach ($data['charges'] as $key => $value) {
                    $bookingID = $value['booking_id'];
                    $data['bookings_comment_count'][$bookingID] = count($this->booking_model->get_remarks(array('booking_id' => $bookingID, "isActive" => 1, 'comment_type' => 1)));
                }
            }
            // Function ends here
            $data['data_id'] = $data_id;
            if(!empty($sf_wise_data)){
                $this->miscelleneous->load_nav_header();
            }
            $this->load->view('employee/completed_cancelled_review', $data);
        }
        else{
            echo "<center style='margin-top:30px;'>No Booking Found</center>";
        }
    }
    
    function download_review_bookings_data() {
        
        $post_data = $this->input->get();
        $review_status = $post_data['review_status'];
        $is_partner = $post_data['is_partner'];
        $request_type = $post_data['request_type'];
        $review_age_min = !empty($post_data['review_age_min']) ? (int) $post_data['review_age_min'] : FILTER_NOT_DEFINE;
        $review_age_max = !empty($post_data['review_age_max']) ? (int) $post_data['review_age_max'] : FILTER_NOT_DEFINE;
        $service_id = $post_data['service_id'];
        $free_paid = $post_data['free_paid'];
        $sf_id = $post_data['sf_id'];
        $whereIN = $having = $where = [];
        $join = array();
        if($this->session->userdata('user_group') == _247AROUND_RM || $this->session->userdata('user_group') == _247AROUND_ASM){
            $sf_list = $this->vendor_model->get_employee_relation($this->session->userdata('id'));
            $serviceCenters = $sf_list[0]['service_centres_id'];
            $whereIN =array("sc.service_center_id"=>explode(",",$serviceCenters));
        }
        if($this->session->userdata('is_am') == '1'){
            $am_id = $this->session->userdata('id');
            $where['agent_filters.agent_id ='.$am_id] = NULL;
            $where['partners.is_active =1'] = NULL;
            $where["agent_filters.entity_type = '"._247AROUND_EMPLOYEE_STRING."'"] = NULL;
            $join['agent_filters'] =  "partners.id=agent_filters.entity_id AND agent_filters.entity_type = '"._247AROUND_EMPLOYEE_STRING."' ";
        }
        
        $status = $review_status;
        if($review_status === "Completed") {
            $having['count(sc.booking_id)=sum(if(sc.added_by_SF=0,1,0))'] = NULL;
        }
        if($review_status === "Completed_By_SF") {
            $status="Completed";
            $whereIN['sc.added_by_SF'] = [1];
        }

        if(!empty($post_data['partner_id'])) {
           $whereIN['booking_details.partner_id'] = [$post_data['partner_id']];
        }

        if(!empty($post_data['state_id'])) {
           $state =  $this->reusable_model->get_search_result_data("state_code", "*", array('state_code' => $post_data['state_id']), NULL, NULL, NULL, NULL, NULL, array())[0]['state'];
           $whereIN['booking_details.state'] = [$state];
        }

        // Do not show Wrong Call Area Bookings in Bookings Cancelled by SF TAB
        //Wrong area bookings will be shown in seperate TAB
        if($review_status == _247AROUND_CANCELLED) {
            if(!empty($post_data['cancellation_reason_id'])){
                $cancellation_reason =  $this->reusable_model->get_search_result_data("booking_cancellation_reasons", "*", array('id' => $post_data['cancellation_reason_id']), NULL, NULL, NULL, NULL, NULL, array())[0]['id'];
                $whereIN['sc.cancellation_reason'] = [$cancellation_reason];
            }
            else {
                $where['(sc.cancellation_reason IS NULL OR sc.cancellation_reason <> "'.CANCELLATION_REASON_WRONG_AREA_ID.'")'] = NULL;
            }
        } 
        // Added Request Type Filter
        if(!empty($post_data['request_type'])) {  
            $request_type = $post_data['request_type'];
            $arr_request_types = [1 => 'Installation & Demo (Paid)',2 => 'Installation & Demo (Free)', 3 => 'Repair - In Warranty', 4 => 'Repair - Out Of Warranty', 5 => 'Extended Warranty', 6 => 'Gas Recharge', 7 => 'PDI', 8 => 'Repeat Booking', 9 => 'Presale Repair'];        
            $request_type_selected = !empty($arr_request_types[$request_type]) ? $arr_request_types[$request_type] : "";   
            $request_type_selected = strtoupper(str_replace(" ", "", $request_type_selected));
            $where['REPLACE(UPPER(booking_details.request_type)," ","") LIKE "%'.$request_type_selected.'%"'] = NULL;
        }
        
        // Added Review Age Filter
        if(isset($review_age_min) && $review_age_min != FILTER_NOT_DEFINE && $review_age_min != '' && isset($review_age_max) && $review_age_max != FILTER_NOT_DEFINE && $review_age_max != '') {
           $where['(DATEDIFF(CURDATE(),STR_TO_DATE(booking_details.service_center_closed_date,"%Y-%m-%d")) BETWEEN "'.$review_age_min.'" AND "'.$review_age_max.'") '] = NULL;
        }
        
        // Added Filter of Service 
        if(!empty($service_id)){
            $whereIN['booking_details.service_id'] = [$service_id];
        }
        
        // Added Filter of Free/Paid
        if(!empty($free_paid)){
            if($free_paid == "Yes"){
               $where['booking_details.amount_due'] = '0';
            }
            else{
               $where['booking_details.amount_due != 0'] = NULL;
            }
        }
        
        // Added Filter of SF
        if(!empty($sf_id) && $sf_id != FILTER_NOT_DEFINE ){
             $whereIN['booking_details.assigned_vendor_id'] = [$sf_id];
        }
        
        $data = $this->booking_model->get_booking_for_review(NULL, $status,$whereIN, $is_partner,NULL,-1,$having, $where,NULL,$join);
        foreach($data as $k => $d) {
            unset($data[$k]['unit_details']);
            unset($data[$k]['service_id']);
            unset($data[$k]['booking']);
            unset($data[$k]['spare_parts']);
            unset($data[$k]['sf_purchase_invoice']);
            unset($data[$k]['booking_create_date']);
            unset($data[$k]['service_center_closed_date']);
            unset($data[$k]['partner_id']);
            unset($data[$k]['is_upcountry']);
            unset($data[$k]['flat_upcountry']);
            if($review_status == _247AROUND_CANCELLED) {
                unset($data[$k]['consumption_status']);
            }
        }
        
        // For Cancelled BOokings
        if($review_status == _247AROUND_CANCELLED) {
            $this->miscelleneous->downloadCSV($data, ['Booking Id', 'Brand Name', 'SF Name', 'Amount Paid',  'Admin Remarks', 'Cancellation Reason', 'Vendor Remarks', 'Request Type', 'City', 'State', 'Customer Name', 'Regd Mobile No', 'Alternate Mobile No', 'Appliance' ,'ASM Name', 'booking_date', 'Age','Review Age','Amount Due'], 'data_'.date('Ymd-His'));
        }
        else { // For Completed Bookings
            $this->miscelleneous->downloadCSV($data, ['Booking Id', 'Brand Name', 'SF Name', 'Amount Paid',  'Admin Remarks', 'Cancellation Reason', 'Vendor Remarks', 'Request Type', 'City', 'State', 'Customer Name', 'Regd Mobile No', 'Alternate Mobile No', 'Appliance' ,'ASM Name', 'Part Consumed' ,'booking_date', 'Age','Review Age','Amount Due'], 'data_'.date('Ymd-His'));
        }         
    }
    function sms_test($number,$text){
          $this->notify->sendTransactionalSmsMsg91($number,$text,SMS_WITHOUT_TAG);
    }
    
    function testDefective(){
       $where =  array("status" => DEFECTIVE_PARTS_PENDING, 'defective_part_required' => 1, 'sf_challan_number IS NOT NULL ' => NULL);
        $data  = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id, service_center_id, service_center_closed_date",
                $where, true);
        foreach ($data as $value) {
            $this->invoice_lib->generate_challan_file($value['id'], $value['service_center_id'], $data['service_center_closed_date']);
        }
    }
    
    /**
     *  @desc : This function is to create Repeat booking
     *  We have already made a function to get_edit_booking_form, this method use that function to insert booking by parent booking
     *  @param : Parent booking ID
     */
    function get_repeat_booking_form($booking_id) {
         log_message('info', __FUNCTION__ . " Booking ID  " . print_r($booking_id, true));
        $openBookings = $this->reusable_model->get_search_result_data("booking_details","booking_id",array("parent_booking"=>$booking_id,  "service_center_closed_date IS NULL" =>NULL),NULL,NULL,NULL,NULL,NULL,array());
        if(empty($openBookings)){
            $this->get_edit_booking_form($booking_id,"","Repeat");
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
    function get_posible_parent_id(){
        $this->miscelleneous->get_posible_parent_booking();
    }
    
     /**
     * @desc This is used to validate serial no image and insert serial no into DB
     * @param Array $upload_serial_number_pic
     * @param Int $unit
     * @param Strng $partner_id
     * @param String $serial_number
     * @return boolean
     */
    function upload_insert_upload_serial_no($upload_serial_number_pic, $partner_id, $serial_number){
        log_message('info', __METHOD__. " Enterring ...");
        if (!empty($upload_serial_number_pic['tmp_name'])) {           
            $pic_name = $this->upload_serial_no_image_to_s3($upload_serial_number_pic, 
                    "serial_number_pic_".$this->input->post('booking_id')."_", SERIAL_NUMBER_PIC_DIR, "serial_number_pic");
            if($pic_name){                
                return true;
            } else {              
                return false;
            }            
        } else {           
            return TRUE;
        }
    }

    /**
     * @desc This is used to upload serial no image to S3
     * @param Array $file
     * @param String $type
     * @param String $s3_directory
     * @param String $post_name
     * @return boolean|string
     */
    public function upload_serial_no_image_to_s3($file, $type, $s3_directory, $post_name) {
        log_message('info', __FUNCTION__ . " Enterring ");
        $allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
        $MB = 1048576;
        $temp = explode(".", $file['name']);
        $extension = end($temp);

        if ($file["name"] != null) {
            if (($file["size"] < 2 * $MB) && in_array($extension, $allowedExts)) {
                if ($file["error"] > 0) {
                   return false;
                } else {                   
                    $picName = $type . rand(10, 100) . "." . $extension;
                    $_POST[$post_name] = $picName;
                    $bucket = BITBUCKET_DIRECTORY;
                    $directory = $s3_directory . "/" . $picName;
                    $this->s3->putObjectFile($file["tmp_name"], $bucket, $directory, S3::ACL_PUBLIC_READ);
                    return $picName;
                }
            } else {                
                return FALSE;
            }
        } else {
            return FALSE;
        }
        log_message('info', __FUNCTION__ . " Exit ");
    }
    /**
    * @Desc - This is used to ask customer for sending appliance's invoice
    */
    function send_whatsapp_number($unit_query = false){
        $whatsapp_no = "";
        if($unit_query == true){
            $brand = $this->booking_model->get_unit_details(array("booking_id"=>$this->input->post("booking_id")), FALSE, "appliance_brand")[0]['appliance_brand'];
        }
        else{
           $brand =  $this->input->post("appliance_brand");
        }
        if($this->input->post("partner_id") == VIDEOCON_ID){
            $whatsapp_no = $this->notify->get_vediocon_state_whatsapp_number($this->input->post("booking_state"));
        }
        else{
            $whatsapp_no = _247AROUND_WHATSAPP_NUMBER;
        }
        $sms = array();
        $sms['status'] = "";
        $sms['phone_no'] = trim($this->input->post("phone_no"));
        $sms['booking_id'] = $this->input->post("booking_id");
        $sms['type'] = "user";
        $sms['type_id'] = $this->input->post("user_id");
        $sms['tag'] = SEND_WHATSAPP_NUMBER_TAG;
        $sms['smsData']['brand'] = $brand;
        $sms['smsData']['service'] = $this->input->post("service");
        $sms['smsData']['whatsapp_no'] = $whatsapp_no;
        $sms['smsData']['partner_brand'] = $brand;
        $this->notify->send_sms_msg91($sms);
    }
    /**
    * @Desc - This is used to show file type list
    */
    function show_file_type_list() {
        $data['file_type'] = $this->booking_model->get_file_type(array(), true);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/show_file_type_list', $data);
    }
    /*
     * This function is used to add file type
     */
    function process_file_type(){
        $id = $count = 0;
        $data = array();
        foreach($this->input->post('file_type') as $index=>$type){
            $data=array("file_type" => $type, "max_allowed_size" => $this->input->post('max_allowed_size')[$index], "allowed_type" => $this->input->post('allowed_type')[$index]);
            $record = $this->booking_model->get_file_type(array("file_type" => $type));
            if(empty($record)) {
                $id = $this->reusable_model->insert_into_table("file_type",$data);
            } else {
                ++$count;
            }
        }
        if($id){
            $msg =  "File Type has been Added successfully ";
            $this->session->set_userdata('success', $msg);
        }
        else if($count > 0) {
            $msg =  "File Type has already been added!!";
            $this->session->set_userdata('error', $msg);
        }
        else{
            $msg =  "Something went Wrong Please try again or contact to admin";
            $this->session->set_userdata('error', $msg);
        }
        redirect(base_url() . 'employee/booking/show_file_type_list');
    }
    /*
     * This function is used to edit file type
     */
    function edit_file_type(){        
        if($this->input->post('file_type_id')){
            $data['file_type'] = $this->input->post('file_type1');
            $data['max_allowed_size'] = $this->input->post('max_allowed_size1');
            $data['allowed_type'] = $this->input->post('allowed_type1');
            $where = array('id' => $this->input->post('file_type_id'));
            
            $record = $this->booking_model->get_file_type($data);
            
            if(empty($record)) {
                $update_data = $this->reusable_model->update_table("file_type",$data,$where);
                if($update_data){
                    $msg =  "File Type has been updated successfully ";
                    $this->session->set_userdata('success', $msg);
                }
                else{
                    $msg =  "No update done";
                    $this->session->set_userdata('error', $msg);
                }
            } else {
                $msg =  "File Type has already been added!";
                $this->session->set_userdata('error', $msg);
            }
        }
        else{
            $msg =  "Something went Wrong Please try again or contact to admin!";
            $this->session->set_userdata('error', $msg);
        }
        redirect(base_url() . 'employee/booking/show_file_type_list');
    }
    /*
     * This function is used to activate / deactivate file type
     */
    function activate_deactivate_type($id,$action){
        if($id){
            $data['is_active'] = $action;
            $where = array('id' => $id);
            $affected_rows =  $this->reusable_model->update_table("file_type",$data,$where);
            if($affected_rows){
                $v = "Deactivated";
                if($action){
                    $v = "Activated";
                }
                echo "File Type has been $v";
            }
            else{
                echo "Something Went Wrong Please Try Again";
            }
        }
    }

    /**
     * @desc: This function is used to update both Bookings and Queries.
     * @param type $user_id
     * @param type $booking_id
     * @param type $insert_state_change : defines whether to insert entry in booking state change table or not
     */
    function update_booking_by_sf($user_id, $booking_id, $do_not_insert_state_change = 0) {
        $bookings = array($booking_id);
        if($booking_id != INSERT_NEW_BOOKING){
            $bookings = $this->booking_model->getbooking_history($booking_id);
        }
        if (!empty($bookings)) {
            $arr_post = $this->input->post();
            $redirect_url = base_url() . 'employee/service_centers/get_sf_edit_booking_form/'.urlencode(base64_encode($booking_id));
            if ($this->agent->is_referral())
            {
                $redirect_url = $this->agent->referrer();
            }
            // If Price Tags are not selected, Redirect to same Page            
            if(empty($arr_post['prices'])){
                redirect($redirect_url);
            }
            else{
            if ($arr_post) {
                $checkValidation = $this->booking_creation_lib->validate_booking();
                $this->form_validation->set_rules('pod', 'POD ', 'callback_validate_serial_no');
                // Serial Number Validation fails
                if($this->form_validation->run() !== TRUE)
                {
                    $userSession = array('error' => validation_errors());
                    $this->session->set_userdata($userSession);
                    redirect($redirect_url);
                }
                if ($checkValidation) {
                    log_message('info', __FUNCTION__ . " Booking ID  " . $booking_id . " User ID: " . $user_id);

                    $status = $this->getAllBookingInput($user_id, $booking_id);
                    if ($status) {
                        log_message('info', __FUNCTION__ . " Update Booking ID" . $status['booking_id']);
                        
                        $this->partner_cb->partner_callback($booking_id);

                        //Redirect to Default Search Page
                        $userSession = array('success' => 'Booking Request type for '.$booking_id.' has been updated successfully');
                        $this->session->set_userdata($userSession);
                        if(!empty($arr_post['redirect_url']))
                        {
                            redirect($arr_post['redirect_url']);
                        }
                        else
                        {
                            redirect(base_url() . 'employee/service_centers/pending_booking');
                        }                        
                    } else {
                        //Redirect to edit booking page if validation err occurs
                        $userSession = array('error' => 'Order Id/Dealer Mobile Number is not Filled against this Booking. '.$booking_id);
                        $this->session->set_userdata($userSession);
                        if(!empty($arr_post['redirect_url']))
                        {
                            redirect($arr_post['redirect_url']);
                        }
                        else
                        {
                            redirect(base_url() . 'employee/service_centers/get_sf_edit_booking_form/'.urlencode(base64_encode($booking_id)));
                        }
                    }
                } else {
                    //Redirect to edit booking page if validation err occurs
                    $userSession = array('error' => validation_errors());
                    $this->session->set_userdata($userSession);
                    if(!empty($arr_post['redirect_url']))
                    {
                        redirect($arr_post['redirect_url']);
                    }
                    else
                    {
                        redirect(base_url() . 'employee/service_centers/get_sf_edit_booking_form/'.urlencode(base64_encode($booking_id)));
                    }
                }
            } else {
                //Logging error if No input is provided
                log_message('info', __FUNCTION__ . "Error in Update Booking ID  " . print_r($booking_id, true) . " User ID: " . print_r($user_id, true));
                $heading = "Booking Error";
                $message = "Oops... No input provided !";
                $error = & load_class('Exceptions', 'core');
                echo $error->show_error($heading, $message, 'custom_error');
            }
        }
        }
         else {
            echo "Booking Id Not Exist...\n Already Updated.";
        }
    }
  
    function get_summary_report() {
        
        $data['states'] = $this->reusable_model->get_search_result_data("state_code","state",array(),array(),NULL,array('state'=>'ASC'),NULL,array(),array());
        $data['services'] = $this->booking_model->selectservice();

        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/get_partner_summary_report',$data);
        
    }
    
    function get_summary_report_data($partnerID) {
       
        $summaryReportData = $this->reusable_model->get_search_result_data("reports_log","filters,date(create_date) as create_date,url",array("entity_type"=>"partner","entity_id"=>$partnerID),NULL,array("length"=>50,"start"=>""),
                array('id'=>'DESC'),NULL,NULL,array());
        
        $str_body = '';
        if(!empty($summaryReportData)) {
            foreach ($summaryReportData as $summaryReport) {
                $finalFilterArray = array();
                $filterArray = json_decode($summaryReport['filters'], true);
                // If no filters found, do not show any data
                if(!empty($filterArray)){
                foreach ($filterArray as $key => $value) {
                    if ($key == "Date_Range" && is_array($value) && !empty(array_filter($value))) {
                        $dArray = explode(" - ", $value);
                        $key = "Registration Date";
                        $startTemp = strtotime($dArray[0]);
                        $endTemp = strtotime($dArray[1]);
                        $startD = date('d-F-Y', $startTemp);
                        $endD = date('d-F-Y', $endTemp);
                        $value = $startD . " To " . $endD;
                    }
                    if ($key == "Completion_Date_Range" && is_array($value) && !empty(array_filter($value))) { 
                        $dArray = explode(" - ", $value);
                        $key = "Completion Date";
                        $startTemp = strtotime($dArray[0]);
                        $endTemp = strtotime($dArray[1]);
                        $startD = date('d-F-Y', $startTemp);
                        $endD = date('d-F-Y', $endTemp);
                        $value = $startD . " To " . $endD;
                        
                    }
                    $finalFilterArray[] = $key . " : " . $value;
                }}
                
                $str_body .=  '<tr>';
                $str_body .=  '<td>' . implode(", ", $finalFilterArray) .'</td>';
                $str_body .=  '<td>' . $summaryReport['create_date'] .'</td>';
                $str_body .= '<td><a class="btn btn-success" style="background: #2a3f54;" href="'. base_url() ."employee/partner/download_custom_summary_report/". $summaryReport['url'] .'">Download</a></td>';
                $str_body .=  '</tr>';
                
            }
        }
        
        echo $str_body;
    }
    
    /**
     * @desc: This funtion is used to get booking cancellation reason list.
     * @param : void
     * @return : void
     * @author : Prity Sharma
     * @date : 21-06-2019
     */
    public function cancellation_reasons()
    {
        $this->miscelleneous->load_nav_header();
        $data = $this->booking_model->cancelreason();
        $this->load->view('employee/view_cancellation_reasons', ['data' => $data]);
    }
    /**
     * @desc: This funtion is used to change booking cancellation reason decision flag.
     * This function is called from ajax
     * @param : void
     * @return : integer
     * @author : Prity Sharma
     * @date : 21-06-2019
     */
    public function change_booking_cancellation_flag()
    {
        $post_data = $this->input->post();
        $id = !empty($post_data['id']) ? substr($post_data['id'], 6) : "";
        $decision_flag = !empty($post_data['flag_value']) ? $post_data['flag_value'] : 0;
        if(!empty($id)):
            $data = array( 
                'id' => $id, 
                'decision_flag' => $decision_flag 
             ); 

            $this->db->set($data); 
            $this->db->where('id', $id);
            $this->db->update('booking_cancellation_reasons', $data);
            echo("success");
        endif;
        exit;
    }
    
    /**
     * this function is used to get the warranty status of booking, called from AJAX
     * function returns output in two formats : 
     * CASE 1 => return warranty status against booking 
     * CASE 2 => returns true/false after matching booking request type with warranty status and a response Message 
     * @author Prity Sharma
     * @date 20-08-2019
     * @return JSON
     */
    public function get_warranty_data($case = 1){
        $post_data = $this->input->post();
        $arrBookings = $post_data['bookings_data'];  
        if(empty($arrBookings)){
            return;
        }
        $arrBookingsWarrantyStatus = $this->warranty_utilities->get_warranty_status_of_bookings($arrBookings);   
            
        switch ($case) {
            case 1:
                echo json_encode($arrBookingsWarrantyStatus);
            break;
            case 2:                
                $warranty_result = $this->warranty_utilities->match_warranty_status_with_request_type($arrBookings, $arrBookingsWarrantyStatus);
                echo $warranty_result;
            break;
        }        
    }
    
    public function getCategoryCapacityForModel()
    {
        $partner_id = $this->input->post('partner_id');
        $model_number = $this->input->post('model_number');
        // selecting category, capacity and brand of model from partner_appliance_details table
        $model_details = $this->partner_model->get_model_number('category, capacity, partner_appliance_details.brand', array('appliance_model_details.model_number' => $model_number,
                        'appliance_model_details.entity_id' => $partner_id, 'appliance_model_details.active' => 1, 'partner_appliance_details.active' => 1));
        echo json_encode($model_details);
    }

    /**
     * 
     * @param type $booking_id
     * @param type $service_center_id
     * @param type $id
     * @param type $partner_id
     */
    function wrong_spare_part($booking_id) {
        
        $post_data = $this->input->post();
        $data['booking_id'] = $booking_id;
        $data['spare_part_detail_id'] = $post_data['spare_part_detail_id'];
        $data['part_name'] = $post_data['part_name'];
        $data['service_id'] = $post_data['service_id'];
        $data['shipped_inventory_id'] = $post_data['shipped_inventory_id'];
        $data['parts'] = $this->inventory_model->get_inventory_master_list_data('inventory_id, part_name, part_number', ['service_id' => $data['service_id'], 'inventory_id not in (1,2)' => NULL]);
        
        if(!empty($post_data['wrong_flag'])) {

            $wrong_part_detail = [];
            $wrong_part_detail['spare_id'] = $data['spare_part_detail_id'];
            $wrong_part_detail['part_name'] = $post_data['wrong_part_name'];
            if(!empty($data['shipped_inventory_id'])) {
                $wrong_part_detail['inventory_id'] = $post_data['wrong_part'];
            } else {
                $wrong_part_detail['inventory_id'] = NULL;
            }
            $wrong_part_detail['remarks'] = $post_data['remarks'];
            echo json_encode($wrong_part_detail);exit;
            
        }
        
        $this->load->view('employee/wrong_spare_part', $data);
    }
    
    function get_edit_request_type_form($booking_id, $redirect_url = null) {
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . " Booking ID: " . print_r($booking_id, true));
        $booking_id = base64_decode(urldecode($booking_id));
        $redirect_url = !empty($redirect_url) ? base64_decode(urldecode($redirect_url)) : "";

        $booking = $this->booking_creation_lib->get_edit_booking_form_helper_data($booking_id, NULL, NULL, true);
        if ($booking) {
            $booking['booking_history']['redirect_url'] = $redirect_url;
            if (isset($booking['booking_files'])) {
                $amc_file_array = array();
                foreach ($booking['booking_files'] as $value) {
                    if ($value['file_description_id'] == ANNUAL_MAINTENANCE_CONTRACT) {
                        array_push($amc_file_array, $value['file_name']);
                    }

            $booking['amc_file_lists'] = $amc_file_array;
        }
        
        $booking['booking_history']['redirect_url'] = $redirect_url;
        if($booking){
            $booking['booking_history']['redirect_url'] = $redirect_url;
            $is_spare_requested = $this->booking_utilities->is_spare_requested($booking);
            $booking['booking_history']['is_spare_requested'] = $is_spare_requested;
            // Check if any line item against booking is invoiced to partner or not
            $arr_booking_unit_details = !empty($booking['unit_details'][0]['quantity']) ? $booking['unit_details'][0]['quantity'] : [];
            $booking['booking_history']['is_partner_invoiced'] = $this->booking_utilities->is_partner_invoiced($arr_booking_unit_details);
            $booking['allow_skip_validations'] = 1;
            $this->miscelleneous->load_nav_header();
            // check is booking warranty type
            foreach ($booking["unit_details"][0]['quantity'] as $val) {
                if ($val['price_tags'] == AMC_PRICE_TAGS) {
                    $booking['amc_warranty_tag'] = TRUE;
                } else {
                    $booking['amc_warranty_tag'] = FALSE;
                }
            }
            $this->load->view('service_centers/update_booking', $booking);
        } else {
            echo "<p style='text-align: center;font: 20px sans-serif;background: #df6666; padding: 10px;color: #fff;'>Booking Id Not Exist</p>";
        }
    }

    /**
     * Method shows the view of combined booking & spare report.
     * @author Ankit Rajvanshi
     * @since 07-02-2020
     */
    function get_detailed_summary_report() {

        $data['states'] = $this->reusable_model->get_search_result_data("state_code","state",array(),array(),NULL,array('state'=>'ASC'),NULL,array(),array());
        $data['services'] = $this->booking_model->selectservice();

        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/get_detailed_summary_report',$data);
        
    }

    /**
     * Method shows the view of download history of combined booking & spare report.
     * @author Ankit Rajvanshi
     * @since 07-02-2020
     */
    function get_detailed_summary_report_data($partnerID) {
       
        $summaryReportData = $this->reusable_model->get_search_result_data("reports_log","filters,date(create_date) as create_date,url",array("entity_type"=>"partner","entity_id"=>$partnerID, 'report_type' => 'partner_detailed_summary_report'),NULL,array("length"=>50,"start"=>""),
                array('id'=>'DESC'),NULL,NULL,array());
        
        $str_body = '';
        if(!empty($summaryReportData)) {
            foreach ($summaryReportData as $summaryReport) {
                $finalFilterArray = array();
                $filterArray = json_decode($summaryReport['filters'], true);
                foreach ($filterArray as $key => $value) {
                    if ($key == "Date_Range" && is_array($value) && !empty(array_filter($value))) {
                        $dArray = explode(" - ", $value);
                        $key = "Registration Date";
                        $startTemp = strtotime($dArray[0]);
                        $endTemp = strtotime($dArray[1]);
                        $startD = date('d-F-Y', $startTemp);
                        $endD = date('d-F-Y', $endTemp);
                        $value = $startD . " To " . $endD;
                    }
                    if ($key == "Completion_Date_Range" && is_array($value) && !empty(array_filter($value))) { 
                        $dArray = explode(" - ", $value);
                        $key = "Completion Date";
                        $startTemp = strtotime($dArray[0]);
                        $endTemp = strtotime($dArray[1]);
                        $startD = date('d-F-Y', $startTemp);
                        $endD = date('d-F-Y', $endTemp);
                        $value = $startD . " To " . $endD;
                        
                    }
                    $finalFilterArray[] = $key . " : " . $value;
                }
                
                $str_body .=  '<tr>';
                $str_body .=  '<td>' . implode(", ", $finalFilterArray) .'</td>';
                $str_body .=  '<td>' . $summaryReport['create_date'] .'</td>';
                $str_body .= '<td><a class="btn btn-success" style="background: #2a3f54;" href="'. base_url() ."employee/partner/download_custom_summary_report/". $summaryReport['url'] .'">Download</a></td>';
                $str_body .=  '</tr>';
                
            }
        }
        
        echo $str_body;
    }
    function get_city_from_pincode() {
        $post_data = $this->input->post();
        $data = array();
        if (!empty($post_data['booking_pincode'])) {
            $data = $this->vendor_model->getDistrict_from_india_pincode('', $post_data['booking_pincode']);
        }
        echo json_encode($data);
    }

    /**
     * @desc : This method is use to show list of spare parts delivered to sf.
     * @author Ankit Rajvanshi
     */
    function courier_lost_parts($offset = '') {
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . ' Used by :' . $this->session->userdata('service_center_name'));
        $service_center_id = $this->session->userdata('service_center_id');

        $where = array (
            "spare_parts_details.status IN ('" . _247AROUND_COMPLETED . "')  " => NULL,
            "spare_parts_details.consumed_part_status_id" => 2,
            "spare_parts_details.spare_lost" => 1,
            "spare_parts_details.defective_part_required" => 0
        );

        $select = "booking_details.service_center_closed_date,booking_details.booking_primary_contact_no as mobile, spare_parts_details.*, "
                . " i.part_number, i.part_number as shipped_part_number, spare_consumption_status.consumed_status,  spare_consumption_status.is_consumed, users.name";

        $group_by = "spare_parts_details.id";
        $order_by = "spare_parts_details.booking_id ASC";


        $config['base_url'] = base_url() . 'employee/booking/courier_lost_parts';
        $config['total_rows'] = $this->service_centers_model->count_spare_parts_booking($where, $select);

        $config['per_page'] = 50;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['spare_parts'] = $this->service_centers_model->get_spare_parts_booking($where, $select, $group_by, $order_by, $offset, $config['per_page']);
        
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/courier_lost_parts', $data);
    }
    
    function get_city_state_from_pincode() {
        $post_data = $this->input->post();
        $data = array();
        if (!empty($post_data['booking_pincode'])) {
            $data = $this->vendor_model->getDistrict_State_from_india_pincode($post_data['booking_pincode']);
        }
        echo json_encode($data);
    }
    
    /**
     * @desc this is used to get review questionnaire for the given form
     * @param int $panel_id, int $form_id, int $service_id, varchar $request_type
     * @author Prity Sharma
     * @created_on 20-04-2020
    */
    function get_review_questionnaire($panel_id , $form_id, $service_id, $request_type){
        // set where condition
        $where_questions['review_questionare.panel'] = $panel_id;
        $where_questions['review_questionare.form'] = $form_id;
        $where_questions['request_type.service_id'] = $service_id;
        $where_questions['request_type.service_category'] = $request_type;
        
        // fetch questions for given panel, page and request type 
        $data['questions'] = $this->booking_model->get_questionnaire('*',$where_questions);
        return $data['questions'];
    }
    
    /**
     * @desc this is used to get options checklist against given questions
     * @param array $arr_questions
     * @author Prity Sharma
     * @created_on 20-04-2020
    */
    function get_questionnaire_checklist($arr_questionnaire){
        $options = array();
        // if no questions found, return Blank Array
        if(empty($arr_questionnaire)){
            return $options;
        }
        
        // get all questions Ids and set in where condition       
        $arr_questions = array_column($arr_questionnaire, 'q_id');
        $where_in['q_id'] = $arr_questions;
        
        // It will fetch all options against given questions
        $data['options'] = $this->booking_model->get_questionnaire_options_checklist('*', $where_in);
        
        // arrange all options question wise
        // set array index as question_id
        if(!empty($data['options'])){
            $arr_options = [];
            foreach ($data['options'] as $key => $value) {
                $arr_options[$value['q_id']][] = $value;
            }
            $data['options'] = $arr_options;
        }
        return $data['options'];
    }
    
    /**
     * @desc this is used to get HTML for questionnaire form
     * @param array $arr_questions, array $arr_answers
     * @author Prity Sharma
     * @created_on 20-04-2020
    */
    function get_questionnaire_html($booking_id, $arr_questions, $arr_answers, $arr_saved_answers){
        $html = '';
        if(!empty($arr_questions)){
            $html .= '<div class="panel panel-info">
                                <div class="panel-heading">Review Questionnaire</div>
                                <div class="panel-body">';
            $html .= ' <input type="hidden" name ="review_questionnaire_booking_id" id="review_questionnaire_booking_id" value="'.$booking_id.'" />';
            $html .= '<table class="table table-bordered">';
            foreach($arr_questions as $key => $arr_question)
            {
                $count = $key + 1;
                $html  .=  '<tr>
                                <td style="width:5px;font-weight:bold;">Q'.$count.'.</td>';
                
                // if some pre-defined options are found against a question, show them in dropdown
                // else show textbox to fill answer
                $html_ans = "";
                if(!empty($arr_answers[$arr_question["q_id"]])){                    
                    $html   .=  '<td>'.$arr_question["question"].'</td>';
                    $html_ans .= '<td style="width:200px;"><select class="form-control" name="review_questionnaire[checklist]['.$arr_question["q_id"].']">';
                    $html_ans .= '<option value="" disabled selected>Choose Option</option>';
                    foreach ($arr_answers[$arr_question["q_id"]] as $ques_id => $arr_answer) {
                        $selected = "";
                        // set selected option if review already filled
                        if(!empty($arr_saved_answers[$arr_question["q_id"]]) && ($arr_saved_answers[$arr_question["q_id"]] == $arr_answer["checklist_id"])){
                            $selected = " selected ";
                        }
                        $html_ans .= '<option value="'.$arr_answer["checklist_id"].'" '.$selected.'>'.$arr_answer["answer"].'</option>';
                    }
                    $html_ans .= '</select></td></tr>';
                }
                else
                {
                    $html   .=  '<td colspan=2>'.$arr_question["question"].'</td>';
                    $html_ans .= "</tr><tr><td colspan=3>";
                    $filled_remarks = "";
                    // copy already filled answer in textbox
                    if(!empty($arr_saved_answers[$arr_question["q_id"]])){
                        $filled_remarks = $arr_saved_answers[$arr_question["q_id"]];
                    }
                    $html_ans .= '<input type="textbox" class="form-control" name="review_questionnaire[remarks]['.$arr_question["q_id"].']" value="'.$filled_remarks.'">'; 
                    $html_ans .= "</td></tr>";
                }
                
                $html .= $html_ans;
            }
            $html .= '</table>';
            $html .= '</div></div>';            
        }
        return $html;
    }
       
    /**
     *  @desc : This function is to save review questionnaire data against booking
     *  @param : int booking_id (column id from booking_details),
     *  @param array $questionnaire_data :Array of answers for questions     *  
     */
    function save_review_questionnaire_data($booking_id, $questionnaire_data) {
        
        //  Array of answers for questions in which checklist is given (Dropdown answers)
        $checklist_data  = !empty($questionnaire_data['checklist']) ?  $questionnaire_data['checklist'] : [];
        // Array of answers for questions in which checklist is not given (Textbox answers)
        $remarks_data = !empty($questionnaire_data['remarks']) ?  $questionnaire_data['remarks'] : [];
        
        // save checklist data
        $this->booking_model->save_questionnaire_checklist_data($booking_id, $checklist_data, 'checklist_id');
        // save remarks data
        $this->booking_model->save_questionnaire_checklist_data($booking_id, $remarks_data, 'remarks');
    }
    
    /**
     * @desc this is used to saved answers for the questionnaire against booking
     * @param int $booking_id
     * @author Prity Sharma
     * @created_on 20-04-2020
    */
    function get_questionnaire_answers_against_booking($booking_id){
        // It will fetch answers for all questions against booking
        $data['answers'] = $this->booking_model->get_questionnaire_filled_answers('*', $booking_id);
        
        // arrange all answers question wise
        // set array index as question_id
        if(!empty($data['answers'])){
            $arr_answers = [];
            foreach ($data['answers'] as $key => $value) {
                $arr_answers[$value['q_id']] = $value['remarks'];
                if(!empty($value['checklist_id'])){
                    $arr_answers[$value['q_id']] = $value['checklist_id'];
                }
            }
            $data['answers'] = $arr_answers;
        }
        return $data['answers'];
    }
    
    /**
     * @desc: This is used to show Call Recordings of particular Booking
     * params: String Booking_primary_ID
     * return: Array of Data for View
     */
    function get_booking_recordings($booking_primary_id) { 
        $select = "agent_outbound_call_log.create_date, agent_outbound_call_log.recording_url, employee.full_name, entity_role.display_name";
        $data['data'] = $this->booking_model->get_booking_recordings_by_id($booking_primary_id, $select);
        $this->load->view('employee/show_booking_recordings', $data);
    }  
    
    function review_bookings_by_status_sf_wise($review_status){
        $this->checkUserSession();
        $post_data = $this->input->post();
    
        // Put all selected values in array to show these filter values above filtered data
        $data['status'] = $review_status;
        $data['partners'] = $this->reusable_model->get_search_result_data("partners", "*", array(), NULL, NULL, NULL, NULL, NULL, array());
        $data['states'] = $this->reusable_model->get_search_result_data("state_code", "*", array(), NULL, NULL, NULL, NULL, NULL, array());
        $data['services'] = $this->reusable_model->get_search_result_data("services", "*", array(), NULL, NULL, NULL, NULL, NULL, array());
        $data['request_types'] = [1 => 'Installation & Demo (Paid)',2 => 'Installation & Demo (Free)', 3 => 'Repair - In Warranty', 4 => 'Repair - Out Of Warranty', 5 => 'Extended Warranty', 6 => 'Gas Recharge', 7 => 'PDI', 8 => 'Repeat Booking', 9 => 'Presale Repair'];   
        // load View
        $this->load->view('employee/completed_cancelled_review_sf_wise', $data);
    }
    
    function get_review_bookings_data_sf_wise($review_status, $post_data, $all_records = 0)
    {
        $whereIN = $where = $join = $having = array();
        
        // For RM/ASM/AMs fetch bookings assigned to their SFs only
        if($this->session->userdata('user_group') == _247AROUND_RM || $this->session->userdata('user_group') == _247AROUND_ASM){
            $sf_list = $this->vendor_model->get_employee_relation($this->session->userdata('id'));
            $serviceCenters = $sf_list[0]['service_centres_id'];
            $whereIN =array("sc.service_center_id"=>explode(",",$serviceCenters));
        }
        if($this->session->userdata('is_am') == '1'){
            $am_id = $this->session->userdata('id');
            $where['agent_filters.agent_id ='.$am_id] = NULL;
            $where['partners.is_active =1'] = NULL;
            $where["agent_filters.entity_type = '"._247AROUND_EMPLOYEE_STRING."'"] = NULL;
            $join['agent_filters'] =  "partners.id=agent_filters.entity_id AND agent_filters.state = booking_details.state AND agent_filters.entity_type = '"._247AROUND_EMPLOYEE_STRING."' ";
        }
        
        if($review_status == _247AROUND_COMPLETED) {
            $having['count(sc.booking_id)=sum(if(sc.added_by_SF=0,1,0))'] = NULL;
        }
        
        if($review_status == _247AROUND_CANCELLED) {
            $where['(sc.cancellation_reason IS NULL OR sc.cancellation_reason <> "'.CANCELLATION_REASON_WRONG_AREA_ID.'")'] = NULL;
        }              

        // For Partner Filter
        if(!empty($post_data['partners'])) {
           $whereIN['booking_details.partner_id'] = [$post_data['partners']];
           $data['filters']['partner_id'] = $post_data['partners'];
        }
        
        // For Product Filter
        if(!empty($post_data['services'])) {
           $whereIN['booking_details.service_id'] = [$post_data['services']];
           $data['filters']['service_id'] = $post_data['services'];
        }

        // For Request Type Filter
        $arr_request_types = [1 => 'Installation & Demo (Paid)',2 => 'Installation & Demo (Free)', 3 => 'Repair - In Warranty', 4 => 'Repair - Out Of Warranty', 5 => 'Extended Warranty', 6 => 'Gas Recharge', 7 => 'PDI', 8 => 'Repeat Booking', 9 => 'Presale Repair'];
        if(!empty($post_data['request_type'])) {           
           $request_type_selected = !empty($arr_request_types[$post_data['request_type']]) ? $arr_request_types[$post_data['request_type']] : "";   
           $request_type_selected = strtoupper(str_replace(" ", "", $request_type_selected));
           $where['REPLACE(UPPER(booking_details.request_type)," ","") LIKE "%'.$request_type_selected.'%"'] = NULL;
           $data['filters']['request_type'] = $post_data['request_type'];
        }
        
        // For Free/Paid Filter
        if(!empty($post_data['free_paid'])) {
            if($post_data['free_paid'] == "Yes"){
               $where['booking_details.amount_due = 0'] = NULL;
            }
            else{
               $where['booking_details.amount_due != 0'] = NULL;
            }
            $data['filters']['free_paid'] = $post_data['free_paid'];
        }
        
        // For State Filter
        if(!empty($post_data['states'])) {
           $state =  $this->reusable_model->get_search_result_data("state_code", "*", array('state_code' => $post_data['states']), NULL, NULL, NULL, NULL, NULL, array())[0]['state'];
           $whereIN['booking_details.state'] = [$state];
           $data['filters']['states'] = $post_data['states'];
        }
        
        // Datatable Search
        if (!empty($post_data['search']['value'])) {
            $like = "";
            if(array_key_exists("column_search", $post_data)){
                foreach ($post_data['column_search'] as $key => $item) { 
                    // if datatable send POST for search
                    if ($key === 0) { // first loop
                        $like .= "( " . $item . " LIKE '%" . $post_data['search']['value'] . "%' ";

                    } else {
                        $like .= " OR " . $item . " LIKE '%" . $post_data['search']['value'] . "%' ";
                    }
                }
                $like .= ") ";
            }
            $where[$like] = NULL;
        }
        
        // Set Current Page Data
        $length = $post_data['length'];
        $start = $post_data['start'];
        if($all_records){
            $length = "-1";
        }
        $data = $this->service_centers_model->get_admin_review_bookings_sf_wise($review_status,$whereIN,0,$where,$join,$having,$length,$start);
        return $data;
    }
    
    /**
     *  @desc : This function is used to get count of review bookings (SF wise)    
     *  @author : Prity Sharma
     *  @created_by : 22-01-2021   
    */
    public function review_bookings_sf_wise($review_status){
        $post = $this->input->post();
        // Set Search
        $post['column_search'] = array('service_centres.state', 'service_centres.name');
        
        // Get all Data
        $list = $this->get_review_bookings_data_sf_wise($review_status, $post);
        $no = $post['start'];
        $data = array();
        foreach ($list as $sf_list) {
            $no++;
            $row = $this->review_bookings_sf_wise_table_data($review_status, $sf_list, $no);
            $data[] = $row;
        }
        // Add Total Column
        if(!empty($list)){
            $data[] = $this->review_bookings_sf_wise_table_data($review_status, $list, $no, 1);
        }
        
        $arr_count = $this->get_review_bookings_data_sf_wise($review_status, $post, 1);
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => count($arr_count),
            "recordsFiltered" => count($arr_count),
            "data" => $data,
        );

        echo json_encode($output);
    }
    
    function review_bookings_sf_wise_table_data($review_status, $values, $no, $total_row = 0) {
        $row = array();
        // For SF wise count
        if(!($total_row))
        {
            $row[] = $no;
            $row[] = "<p class='sf_".$review_status."' data-sf='".$values['sf_id']."'>"
                        . $values['sf_name']
                        . "<span id='penalty_".$review_status."_".$values['sf_id']."'></span>"
                        . "<span id='status_".$review_status."_".$values['sf_id']."'></span>"
                        . "</p>";
            $row[] = "<p>".$values['state']."</p>";
            $row[] = "<p><button data-sf='".$values['sf_id']."' data-review-age-min='0'  data-review-age-max='0' class='btn btn-count btn-success'>". $values['Day0'] ."</button></p>";
            $row[] = "<p><button data-sf='".$values['sf_id']."' data-review-age-min='1'  data-review-age-max='1' class='btn btn-count btn-success'>". $values['Day1'] ."</button></p>";
            $row[] = "<p><button data-sf='".$values['sf_id']."' data-review-age-min='2'  data-review-age-max='2' class='btn btn-count ".($values['Day2'] > 0 ? 'btn-danger' : 'btn-success')."'>".$values['Day2']."</button></p>";
            $row[] = "<p><button data-sf='".$values['sf_id']."' data-review-age-min='3'  data-review-age-max='3' class='btn btn-count ".($values['Day3'] > 0 ? 'btn-danger' : 'btn-success')."'>".$values['Day3']."</button></p>";
            $row[] = "<p><button data-sf='".$values['sf_id']."' data-review-age-min='4'  data-review-age-max='4' class='btn btn-count ".($values['Day4'] > 0 ? 'btn-danger' : 'btn-success')."'>".$values['Day4']."</button></p>";
            $row[] = "<p><button data-sf='".$values['sf_id']."' data-review-age-min='5'  data-review-age-max='7' class='btn btn-count ".($values['Day5-Day7'] > 0 ? 'btn-danger' : 'btn-success')."'>".$values['Day5-Day7']."</button></p>";
            $row[] = "<p><button data-sf='".$values['sf_id']."' data-review-age-min='8'  data-review-age-max='15' class='btn btn-count ".($values['Day8-Day15'] > 0 ? 'btn-danger' : 'btn-success')."'>".$values['Day8-Day15']."</button></p>";
            $row[] = "<p><button data-sf='".$values['sf_id']."' data-review-age-min='16' data-review-age-max='2000' class='btn btn-count ".($values['>Day15'] > 0 ? 'btn-danger' : 'btn-success')."'>".$values['>Day15']."</button></p>";
            $row[] = "<p><button data-sf='".$values['sf_id']."' data-review-age-min='".FILTER_NOT_DEFINE."'  data-review-age-max='".FILTER_NOT_DEFINE."' class='btn btn-count ".($values['Total'] > 0 ? 'btn-danger' : 'btn-success')."'>".$values['Total']."</button></p>";
        }
        // For Total Row
        else
        {
            $row[] = "<p>".++$no."</p>";
            $row[] = "<p>Total</p>";
            $row[] = "<p>All</p>";
            $row[] = "<p><button data-sf='".FILTER_NOT_DEFINE."' data-review-age-min='0'  data-review-age-max='0' class='btn btn-count btn-success'>". array_sum(array_column($values, 'Day0')) ."</button></p>";
            $row[] = "<p><button data-sf='".FILTER_NOT_DEFINE."' data-review-age-min='1'  data-review-age-max='1' class='btn btn-count btn-success'>". array_sum(array_column($values, 'Day1')) ."</button></p>";
            $row[] = "<p><button data-sf='".FILTER_NOT_DEFINE."' data-review-age-min='2'  data-review-age-max='2' class='btn btn-count ".(array_sum(array_column($values, 'Day2')) > 0 ? 'btn-danger' : 'btn-success')."'>".array_sum(array_column($values, 'Day2'))."</button></p>";
            $row[] = "<p><button data-sf='".FILTER_NOT_DEFINE."' data-review-age-min='3'  data-review-age-max='3' class='btn btn-count ".(array_sum(array_column($values, 'Day3')) > 0 ? 'btn-danger' : 'btn-success')."'>".array_sum(array_column($values, 'Day3'))."</button></p>";
            $row[] = "<p><button data-sf='".FILTER_NOT_DEFINE."' data-review-age-min='4'  data-review-age-max='4' class='btn btn-count ".(array_sum(array_column($values, 'Day4')) > 0 ? 'btn-danger' : 'btn-success')."'>".array_sum(array_column($values, 'Day4'))."</button></p>";
            $row[] = "<p><button data-sf='".FILTER_NOT_DEFINE."' data-review-age-min='5'  data-review-age-max='7' class='btn btn-count ".(array_sum(array_column($values, 'Day5-Day7')) > 0 ? 'btn-danger' : 'btn-success')."'>".array_sum(array_column($values, 'Day5-Day7'))."</button></p>";
            $row[] = "<p><button data-sf='".FILTER_NOT_DEFINE."' data-review-age-min='8'  data-review-age-max='15' class='btn btn-count ".(array_sum(array_column($values, 'Day8-Day15')) > 0 ? 'btn-danger' : 'btn-success')."'>".array_sum(array_column($values, 'Day8-Day15'))."</button></p>";
            $row[] = "<p><button data-sf='".FILTER_NOT_DEFINE."' data-review-age-min='16'  data-review-age-max='2000' class='btn btn-count ".(array_sum(array_column($values, '>Day15')) > 0 ? 'btn-danger' : 'btn-success')."'>".array_sum(array_column($values, '>Day15'))."</button></p>";
            $row[] = "<p><button data-sf='".FILTER_NOT_DEFINE."' data-review-age-min='".FILTER_NOT_DEFINE."'  data-review-age-max='".FILTER_NOT_DEFINE."' class='btn btn-count ".(array_sum(array_column($values, 'Total')) > 0 ? 'btn-danger' : 'btn-success')."'>".array_sum(array_column($values, 'Total'))."</button></p>";
        }
        return $row;
    }
    
    /**
     * This function shows OW completed bookings percentage against a SF
     * @param type $sf_id
     * @param type $time_period
     * @param type $status
     */
    function get_ow_completed_percentage($sf_id, $time_period, $status = 0){
        $data = $this->booking_model->get_sf_ow_completed_percentage($sf_id, $time_period);
        $ow_completed_percentage = 0;
        if(!empty($data[0]['ow_completed_percentage'])){
            $ow_completed_percentage = $data[0]['ow_completed_percentage'];
        }
        
        if($status){
            $str = "";
            if($ow_completed_percentage < OW_COMPLETED_THRESHOLD){
                $str = "<span style='color:red;margin-left:5px;font-weight:bold' data-toggle='tooltip' title='The SF OW booking completion is below 20%, in last 60 days.'>O</span>";
            }
            echo $str;
        }
        else
        {
            echo $ow_completed_percentage;
        }
    }
    
    /**
     * 
     * @param type $sf_id
     * @param type $time_period
     * @param type $status
     */
    function get_cancelled_percentage($sf_id, $time_period, $status = 0){
        $data = $this->booking_model->get_sf_cancelled_percentage($sf_id, $time_period);
        $cancelled_percentage = 0;
        if(!empty($data[0]['cancelled_percentage'])){
            $cancelled_percentage = $data[0]['cancelled_percentage'];
        }
        
        if($status){
            $str = "";
            if($cancelled_percentage > CANCELLATION_THRESHOLD){
                $str = "<span style='color:red;margin-left:5px;font-weight:bold' data-toggle='tooltip' title='The SF Booking Cancellation % is greater than 20%, in last 60 days.'>C</span>";
            }
            echo $str;
        }
        else
        {
            echo $cancelled_percentage;
        }
    }
        
}
