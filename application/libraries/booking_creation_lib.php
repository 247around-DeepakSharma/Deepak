<?php
class booking_creation_lib {
    public $tatFaultyBookingCriteria = array();

    public function __construct() {
        $this->My_CI = & get_instance();
        $this->My_CI->load->helper(array('form', 'url'));
        $this->My_CI->load->library('email');
        $this->My_CI->load->library('partner_cb');
        $this->My_CI->load->library('initialized_variable');
        $this->My_CI->load->library('asynchronous_lib');
        $this->My_CI->load->library('booking_utilities');
        $this->My_CI->load->library('notify');
        $this->My_CI->load->library('push_notification_lib');
        $this->My_CI->load->library('send_grid_api');
        $this->My_CI->load->library('s3');
        $this->My_CI->load->library('PHPReport');
        $this->My_CI->load->model('vendor_model');
        $this->My_CI->load->model('reusable_model');
        $this->My_CI->load->model('booking_model');
        $this->My_CI->load->model('upcountry_model');
        $this->My_CI->load->model('partner_model');
        $this->My_CI->load->model('dealer_model');
        $this->My_CI->load->model('inventory_model');
        $this->My_CI->load->library('form_validation');
        $this->My_CI->load->model('service_centers_model');
        $this->My_CI->load->model('penalty_model');
        $this->My_CI->load->model('engineer_model');
        $this->My_CI->load->driver('cache');
        $this->My_CI->load->model('dashboard_model');
    }
    function get_edit_booking_form_helper_data($booking_id,$appliance_id,$is_repeat){
        log_message('info', __FUNCTION__ . " Start WIth  " . print_r($appliance_id, true) . " Booking ID: " . print_r($booking_id, true));
        if ($booking_id != "") {
            $booking_history = $this->My_CI->booking_model->getbooking_history($booking_id);
        } else {
            $booking_history = $this->My_CI->booking_model->getbooking_history_by_appliance_id($appliance_id);
        }
        if (!empty($booking_history)) {
            $booking = $this->My_CI->booking_model->get_city_source();
            $booking['booking_history'] = $booking_history;
            $booking['unit_details'] = $this->My_CI->booking_model->getunit_details($booking_id, $appliance_id);
            if(!empty($booking_history[0]['dealer_id'])){
                $condition = array(
                "where" => array('dealer_details.dealer_id' => $booking_history[0]['dealer_id']));
                $select = " dealer_details.dealer_id, dealer_name, dealer_phone_number_1";
                $condition['length'] = -1;
               
                 $dealer_details = $this->My_CI->dealer_model->get_dealer_mapping_details($condition, $select);
                 if($dealer_details){
                      $booking['booking_history'][0]['dealer_phone_number'] = $dealer_details[0]['dealer_phone_number_1'];
                      $booking['booking_history'][0]['dealer_name'] = $dealer_details[0]['dealer_name'];
                 }
            }
            
            $prepaid = $this->My_CI->miscelleneous->get_partner_prepaid_amount($booking_history[0]['partner_id']);
            $booking['active'] = $prepaid['active'];
            
            $booking['partner_type'] = $prepaid["partner_type"];
            if ($booking['partner_type'] == OEM) {
                $booking['services'] = $this->My_CI->partner_model->get_partner_specific_services($booking_history[0]['partner_id']);
            } else {
                $booking['services'] = $this->My_CI->booking_model->selectservice();
            }

            $service_category = array();
            $booking['capacity'] = array();
            $booking['category'] = array();
            $booking['brand'] = array();
            $booking['prices'] = array();
            $booking['model'] = array();
            $booking['appliance_id'] = $appliance_id;
            $where_internal_status = array("page" => "FollowUp", "active" => '1');
            $booking['follow_up_internal_status'] = $this->My_CI->booking_model->get_internal_status($where_internal_status);
            foreach ($booking['unit_details'] as $key => $value) {     
                 $isWbrand = "";           
                if ($booking['partner_type'] == OEM) {
                    $isWbrand = $value['brand'];
                    $where = array("partner_appliance_details.service_id" => $booking_history[0]['service_id'],
                        'partner_id' => $booking_history[0]['partner_id'], "active" => 1);
                    $select = 'brand As brand_name';
                    $brand = $this->My_CI->partner_model->get_partner_specific_details($where, $select, "brand");
                    $where['brand'] = $value['brand'];
                    $model_where = array(
                       "appliance_model_details.entity_id" =>  $booking_history[0]['partner_id'],
                       "appliance_model_details.entity_type" => _247AROUND_PARTNER_STRING,
                       "appliance_model_details.service_id" => $booking_history[0]['service_id'],
                       "appliance_model_details.active" => 1,
                       "partner_appliance_details.brand" => $value['brand']
                    );
                    $model = $this->My_CI->partner_model->get_model_number('appliance_model_details.model_number as model', $model_where);
                } else {

                    $whiteListBrand = $this->My_CI->partner_model->get_partner_blocklist_brand(array("partner_id" => $value['partner_id'], "brand" => $value['brand'],
            "service_id" => $booking_history[0]['service_id'], "whitelist" => 1), "*");
                    if(!empty($whiteListBrand)){
                        $isWbrand = $value['brand'];
                    }
                    
                    $brand = $this->My_CI->booking_model->getBrandForService($booking_history[0]['service_id']);
                    $model = array();
                }
                
                $category = $this->My_CI->booking_model->getCategoryForService($booking_history[0]['service_id'], $value['partner_id'], $isWbrand);
                $capacity = $this->My_CI->booking_model->getCapacityForCategory($booking_history[0]['service_id'], $value['category'], $isWbrand, $value['partner_id']);
                $prices = $this->My_CI->booking_model->getPricesForCategoryCapacity($booking_history[0]['service_id'], $value['category'], $value['capacity'], $value['partner_id'], $isWbrand);
                $where1 = array('service_id' => $booking_history[0]['service_id'], 'brand_name' => $value['brand']);
                $brand_id_array = $this->My_CI->booking_model->get_brand($where1);
                if (!empty($brand_id_array)) {

                    $booking['unit_details'][$key]['brand_id'] = $brand_id_array[0]['id'];
                } else {
                    $booking['unit_details'][$key]['brand_id'] = "";
                }
                array_push($booking['category'], $category);
                array_push($booking['brand'], $brand);
                array_push($booking['capacity'], $capacity);
                array_push($booking['prices'], $prices);
                array_push($booking['model'], $model);
                foreach ($value['quantity'] as $key => $price_tag) {
                    $price_tags1 = str_replace('(Free)', '', $price_tag['price_tags']);
                    $price_tags2 = str_replace('(Paid)', '', $price_tags1);
                    array_push($service_category, $price_tags2);
                }
            }
            $booking['booking_symptom'] = $this->My_CI->booking_model->getBookingSymptom($booking_id);
            $booking['file_type'] = $this->My_CI->booking_model->get_file_type();
            $booking['booking_files'] = $this->My_CI->booking_model->get_booking_files(array('booking_id' => $booking_id));
        
            $booking['symptom'] = array();
            if(!empty($service_category)) {
                $booking['symptom'] = $this->My_CI->booking_request_model->get_booking_request_symptom('symptom.id, symptom',
                        array('symptom.service_id' => $booking_history[0]['service_id'], 'symptom.active' => 1, 'symptom.partner_id' => $booking_history[0]['partner_id']), array('request_type.service_category' => $service_category));
            }
            if(count($booking['symptom']) <= 0) {
                $booking['symptom'][0] = array('id' => 0, 'symptom' => 'Default');
            }
            
            $booking['is_repeat'] = $is_repeat;
            $booking['c2c'] = $this->My_CI->booking_utilities->check_feature_enable_or_not(CALLING_FEATURE_IS_ENABLE);
            return $booking;
        } else {
            return false;
        }
    }
        /**
     * @desc: This function is used to validate Bookings New/Update
     * 
     * params: Array of inputs
     * return: void
     */
    function validate_booking() {
        $this->My_CI->form_validation->set_rules('service_id', 'Appliance', 'required');
        $this->My_CI->form_validation->set_rules('source_code', 'Source Code', 'required');
        $this->My_CI->form_validation->set_rules('type', 'Booking Type', 'required');
        $this->My_CI->form_validation->set_rules('grand_total_price', 'Total Price', 'required');
        $this->My_CI->form_validation->set_rules('city', 'City', 'required');
        $this->My_CI->form_validation->set_rules('booking_date', 'Date', 'required');
        $this->My_CI->form_validation->set_rules('appliance_brand', 'Appliance Brand', 'required');
        $this->My_CI->form_validation->set_rules('appliance_category', 'Appliance Category', 'required');
        $this->My_CI->form_validation->set_rules('partner_paid_basic_charges', 'Please Select Partner Charged', 'required');
        $this->My_CI->form_validation->set_rules('booking_primary_contact_no', 'Mobile', 'required|trim|regex_match[/^[6-9]{1}[0-9]{9}$/]');
        $this->My_CI->form_validation->set_rules('dealer_phone_number', 'Dealer Mobile Number', 'trim|regex_match[/^[6-9]{1}[0-9]{9}$/]');
        $this->My_CI->form_validation->set_rules('booking_timeslot', 'Time Slot', 'required');
        $this->My_CI->form_validation->set_rules('prices', 'Price Tag', 'required');
        $this->My_CI->form_validation->set_rules('support_file', 'Suppoart File', 'callback_validate_upload_orderId_support_file');
        return $this->My_CI->form_validation->run();
    }
    
    function is_booking_able_to_reschedule($booking_id, $sf_closed_date = NULL) {
        if(empty($sf_closed_date)) {
            $sf_closed_date = $this->My_CI->reusable_model->get_search_result_data('booking_details', 'service_center_closed_date', ['booking_id' => $booking_id], NULL, NULL, NULL, NULL, NULL)[0]['service_center_closed_date'];
        }
        
        if(!empty($sf_closed_date)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    function findInArray($ar, $val) {
        for ($i = 0,$len = count($ar); $i < $len; $i++) {
            if ( $ar[$i] === $val ) { // strict equality test
                return $i;
            }
        }
        return -1;
    }
    
    function checkPriceTagValidation($delivered_price_tags){
        $repair_flag = false;
        $repair_out_flag = false;
        $installation_flag = false;
        $pdi = false;
        $extended_warranty = false;
        $pre_sales = false;
        $array =[];

        if(($this->findInArray($delivered_price_tags, 'Repair - In Warranty (Home Visit)') > -1 
                || $this->findInArray($delivered_price_tags, 'Repair - In Warranty (Service Center Visit)') > -1 
                || $this->findInArray($delivered_price_tags, 'Repair - In Warranty (Customer Location)') > -1 
                )){
            
            $repair_flag = true;
            array_push($array, $repair_flag);
         } 
         
         if(($this->findInArray($delivered_price_tags, 'Repair - Out Of Warranty (Home Visit)') > -1 
                || $this->findInArray($delivered_price_tags, 'Repair - Out Of Warranty (Home Visit)') > -1
                || $this->findInArray($delivered_price_tags, 'Repair - Out Of Warranty (Customer Location)') > -1
                || $this->findInArray($delivered_price_tags, 'Repair - Out Of Warranty (Service Center Visit)') > -1)){
            
            $repair_out_flag = true;
            array_push($array, $repair_out_flag);
         }
         
         if($this->findInArray($delivered_price_tags, 'Extended Warranty') > -1 ){
            $extended_warranty = true;
            array_push($array, $extended_warranty);
         }
         
         if($this->findInArray($delivered_price_tags, 'Presale Repair') > -1 ){
            $pre_sales = true;
            array_push($array, $pre_sales);
         }
         
         if($this->findInArray($delivered_price_tags, 'Installation & Demo (Free)') > -1 
                || $this->findInArray($delivered_price_tags, 'Installation & Demo (Paid)') > -1){
                   $installation_flag = true;
                   array_push($array, $installation_flag);
         }
         
         if($this->findInArray($delivered_price_tags, 'Pre-Dispatch Inspection PDI - With Packing') > -1
                || $this->findInArray($delivered_price_tags, 'Pre-Dispatch Inspection PDI - With Packing') > -1
                || $this->findInArray($delivered_price_tags, 'Pre-Dispatch Inspection PDI - Without Packing') > -1
                || $this->findInArray($delivered_price_tags, 'Pre-Dispatch Inspection PDI - Without Packing') > -1){
                    $pdi = true;
                    array_push($array, $pdi);
                }
                
         if(count($array) > 1){
             return false;
         } else {
             return true;
         }
    }
}