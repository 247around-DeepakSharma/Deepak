<?php

class Buyback {

    Private $POST_DATA = array();

    public function __construct() {
        $this->My_CI = & get_instance();
        $this->My_CI->load->helper(array('form', 'url'));
        $this->My_CI->load->library("initialized_variable");
        $this->My_CI->load->library("session");
        $this->My_CI->load->library("s3");
        $this->My_CI->load->library("table");
        $this->My_CI->load->model("service_centre_charges_model");
        $this->My_CI->load->model("bb_model");
        $this->My_CI->load->model("cp_model");
        $this->My_CI->load->model("booking_model");
        $this->My_CI->load->model("service_centre_charges_model");
    }

    /**
     * @desc This is used to check order exist in the db or not. If order not exist 
     * then it call methd to insert other wise update status
     */
    function check_action_order_details() {
        //Get bb data from global variable
        $this->POST_DATA = $this->My_CI->initialized_variable->get_post_buyback_order_details();
        //Check order exist in the database
        $where_bb_order = array('partner_id' => $this->POST_DATA['partner_id'], 'partner_order_id' => $this->POST_DATA['partner_order_id']);
        $is_exist_order = $this->My_CI->bb_model->get_bb_order($where_bb_order, 'bb_order_details.id, bb_order_details.current_status, '
                . 'bb_order_details.internal_status, city, partner_tracking_id, bb_order_details.partner_order_id, is_delivered');
        if ($is_exist_order) {
            //Order already exiting
            return $this->update_bb_order($is_exist_order);
        } else {
            //New order insert
            return $this->new_bb_order();
        }
    }

    /**
     * @desc It inserts new order
     * @return boolean
     */
    function new_bb_order() {
        // Get CP Id
        $cp_data = $this->get_cp_id_from_region( $this->POST_DATA['city']);
        
        $bb_charges = array();
        $service_id = 0;
        $cp_id = NULL;
        if (!empty($cp_data)) {
            //Get Charges list
            if(stripos($this->POST_DATA['order_key'],'IMEI') !== FALSE){
                $file_order_key = rtrim(trim(explode('IMEI',$this->POST_DATA['order_key'])[0]),":");
            }else{
                $file_order_key = $this->POST_DATA['order_key'];
            }
            
            $s_order_key = str_replace(":","",$file_order_key);
            $s_order_key1 = str_replace("_","",$s_order_key);
            $b_charges = array();
            
            foreach($cp_data as $cp_unique_data){
                $bb_charges = $this->My_CI->service_centre_charges_model->get_bb_charges(array(
                    'partner_id' => $this->POST_DATA['partner_id'],
                    'city' => $cp_unique_data['shop_address_city'],
                    'order_key' => $s_order_key1,
                    'cp_id' => $cp_unique_data['cp_id'],
                        ), '*');
                
                if(!empty($bb_charges)){
                    array_push($b_charges, $bb_charges[0]);
                }
            }

            if (count($b_charges) == 1) {
                $cp_id = $b_charges[0]['cp_id'];
                $service_id = $b_charges[0]['service_id'];
            } else {
                $this->My_CI->initialized_variable->not_assigned_order();
                $this->My_CI->table->add_row($this->POST_DATA['partner_order_id']);
                $cp_id = NULL;
            }
        } else {
            $this->My_CI->initialized_variable->not_assigned_order();
            $this->My_CI->table->add_row($this->POST_DATA['partner_order_id']);
        }
        if (empty($service_id)) {
            $service_id = $this->get_service_id_by_appliance();
        }
        
        // Insert bb order details
        $current_status = ($this->POST_DATA['current_status'] == "PLACED")? "In-Transit" :$this->POST_DATA['current_status'];
        $is_insert = $this->insert_bb_order_details($cp_id);
        if ($is_insert) {
            // Insert bb unit details
            if (count($b_charges) == 1) {
                $is_unit = $this->insert_bb_unit_details($b_charges, $service_id);
            } else {
                $is_unit = $this->insert_bb_unit_details(array(), $service_id);
            }
            
            if ($is_unit) {
                if (!empty($cp_id)) {
                    $this->My_CI->cp_model->insert_bb_cp_order_action(array(
                        "partner_order_id" => $this->POST_DATA['partner_order_id'],
                        "cp_id" => $cp_id,
                        "create_date" => date('Y-m-d H:i:s'),
                        "current_status" => 'Pending',
                        "internal_status" => $current_status
                    ));
                }
                // Insert state change
                $this->insert_bb_state_change($this->POST_DATA['partner_order_id'], $current_status, $this->POST_DATA['order_key'], _247AROUND_DEFAULT_AGENT, _247AROUND, NULL);
                if ($this->POST_DATA['current_status'] == 'Delivered') {
                    $this->My_CI->initialized_variable->delivered_count();
                }

                $this->My_CI->initialized_variable->total_inserted();
                return TRUE;
            } else {

                return FALSE;
            }
        } else {

            return FALSE;
        }
    }

    /**
     * @desc This is called to get service id by appliance name
     * @return int
     */
    function get_service_id_by_appliance() {
        $appliance_name = "";

        if (stristr($this->POST_DATA['subcat'], "Laundry") || stristr($this->POST_DATA['subcat'], "WashingMachine")) {
            $appliance_name = 'Washing Machine';
        }
        if (stristr($this->POST_DATA['subcat'], "Air Conditioners") || strstr($this->POST_DATA['subcat'], "AC")) {
            $appliance_name = 'Air Conditioner';
        }
        if (stristr($this->POST_DATA['subcat'], "Refrigerators") || stristr($this->POST_DATA['subcat'], "Refrigerator")) {
            $appliance_name = 'Refrigerator';
        }
        if (stristr($this->POST_DATA['subcat'], "Tv")) {
            $appliance_name = 'Television';
        }
        if (stristr($this->POST_DATA['subcat'], "mobile")) {
            $appliance_name = 'Mobile';
        }

        if (!empty($appliance_name)) {
            $service_id = $this->My_CI->booking_model->getServiceId($appliance_name);
            if ($service_id) {
                return $service_id;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
     * @desc Insert bb order details
     * @param int $cp_id
     * @return Array
     */
    function insert_bb_order_details($cp_id) {

        $bb_order_details = array(
            'partner_id' => $this->POST_DATA['partner_id'],
            'partner_order_id' => $this->POST_DATA['partner_order_id'],
            'order_date' => $this->POST_DATA['order_date'],
            'city' => $this->POST_DATA['city'],
            'current_status' => ($this->POST_DATA['current_status'] == "PLACED")? "In-Transit" :$this->POST_DATA['current_status'],
            'internal_status' =>($this->POST_DATA['current_status'] == "PLACED")? "In-Transit" :$this->POST_DATA['current_status'],
            'create_date' => date('Y-m-d H:i:s'),
            'assigned_cp_id' => (!empty($cp_id) ? $cp_id : NULL),
            'delivery_date' => (!empty($this->POST_DATA['delivery_date']) ? $this->POST_DATA['delivery_date'] : NULL),
            'partner_tracking_id' => (isset($this->POST_DATA['tracking_id']) ? $this->POST_DATA['tracking_id'] : NULL),
            'is_delivered' => (($this->POST_DATA['current_status'] == 'Delivered') ? 1 : 0),
            'file_received_date' => $this->POST_DATA['file_received_date']
        );

        return $this->My_CI->bb_model->insert_bb_order_details($bb_order_details);
    }

    /**
     * @desc insert unit details
     * @param Array $bb_charges
     * @return Array
     */
    function insert_bb_unit_details($bb_charges, $service_id) {
        log_message("info", __METHOD__."Order ID =>".$this->POST_DATA['partner_order_id']." BB Charge ". print_r($bb_charges, TRUE), " service_id ". $service_id);
        $gst_amount = 0;
        if(!empty($bb_charges)){
            //$partner_amount =  $this->POST_DATA['partner_basic_charge'] + $this->POST_DATA['partner_sweetner_charges'];
            $partner_amount =  $this->POST_DATA['partner_basic_charge'];
            $profit = ($bb_charges[0]['cp_basic'] + $bb_charges[0]['cp_tax']) - $partner_amount;
            if ($profit > 0) {
                $s_id = (!empty($bb_charges) ? $bb_charges[0]['service_id'] : $service_id);
                $gst_tax_rate = DEFAULT_TAX_RATE;
                if(!empty($s_id) && ($s_id == _247AROUND_AC_SERVICE_ID || $s_id == _247AROUND_TV_SERVICE_ID)){
                    $gst_tax_rate = DEFAULT_PARTS_TAX_RATE;
                }
                $gst_amount = $this->My_CI->booking_model->get_calculated_tax_charge($profit, $gst_tax_rate);
               
            } 
        }
        
        $bb_unit_details = array(
            'partner_id' => $this->POST_DATA['partner_id'],
            'partner_order_id' => $this->POST_DATA['partner_order_id'],
            'category' => (!empty($bb_charges) ? $bb_charges[0]['category'] : NULL),
            'brand' => (!empty($bb_charges) ? $bb_charges[0]['brand'] : NULL),
            'physical_condition' => (!empty($bb_charges) ? $bb_charges[0]['physical_condition'] : NULL),
            'working_condition' => (!empty($bb_charges) ? $bb_charges[0]['working_condition'] : NULL),
            'order_status' => ($this->POST_DATA['current_status'] == "PLACED")? "In-Transit" :$this->POST_DATA['current_status'],
            'partner_basic_charge' => $this->POST_DATA['partner_basic_charge'],
            'cp_basic_charge' => (!empty($bb_charges) ? $bb_charges[0]['cp_basic'] : 0),
            'cp_tax_charge' => (!empty($bb_charges) ? $bb_charges[0]['cp_tax'] : 0),
            'around_commision_basic_charge' => (!empty($bb_charges) ? $bb_charges[0]['around_basic'] : 0),
            'around_commision_tax' => (!empty($bb_charges) ? $bb_charges[0]['around_tax'] : 0),
            'partner_sweetner_charges' => $this->POST_DATA['partner_sweetner_charges'],
            'gst_amount' => $gst_amount,
            'create_date' => date('Y-m-d'),
            'order_key' => $this->POST_DATA['order_key'],
            'service_id' => (!empty($bb_charges) ? $bb_charges[0]['service_id'] : $service_id),
        );
        
        if($this->My_CI->input->post('qc_svc')){
            $bb_unit_details['qc_svc'] = $this->My_CI->input->post('qc_svc');
        }


        return $this->My_CI->bb_model->insert_bb_unit_details($bb_unit_details);
    }

    /**
     * @desc Order already exists in the db. It update the new status. 
     * When Old status is delivered or completed then we will not update 
     * @param Array $order_data
     * @return boolean
     */

    function update_bb_order($order_data) {
        if($this->POST_DATA['current_status'] == "PLACED" || $this->POST_DATA['current_status'] == "Unknown" || !$this->POST_DATA['current_status']){
            return false;
        }
        $remarks = NULL;
        if ($order_data[0]['is_delivered'] == 0) {
                if ($order_data[0]['current_status'] != $this->POST_DATA['current_status']) {
                    
                    //get auto acknowledge date
                    $auto_acknowledge_date = NULL;
                    if(!empty($this->POST_DATA['delivery_date'])){
//                        $datetime1 = date_create(date("Y-m-d"));
//                        $datetime2 = date_create(date('Y-m-d', strtotime($this->POST_DATA['delivery_date'])));
//                        $interval = date_diff($datetime1, $datetime2);
//                        $days = $interval->days;
                          $auto_acknowledge_date = date('Y-m-d', strtotime(date("Y-m-d"). ' + 3 days'));
//                        $auto_acknowledge_date = date('Y-m-d', strtotime($this->POST_DATA['delivery_date']. ' + 10 days'));
//                        if ($days < NO_OF_DAYS_NOT_SHOW_NOT_RECEIVED_BUTTON) {
//                            $auto_acknowledge_date = date('Y-m-d', strtotime(date("Y-m-d"). ' + 7 days'));
//                        }
                    }
                    $bb_order_details = array(
                        'current_status' => $this->POST_DATA['current_status'],
                        'internal_status' => $this->POST_DATA['current_status'],
                        'delivery_date' => (!empty($this->POST_DATA['delivery_date']) ? $this->POST_DATA['delivery_date'] : NULL),
                        'auto_acknowledge_date' => $auto_acknowledge_date
                    );
                    if ($this->POST_DATA['tracking_id'] != 0 || $order_data[0]['partner_tracking_id'] != $this->POST_DATA['tracking_id']) {
                        $bb_order_details['partner_tracking_id'] = $this->POST_DATA['tracking_id'];
                    }
                    if ($this->POST_DATA['city'] && $order_data[0]['city'] != $this->POST_DATA['city']) {
                        $bb_order_details['city'] = $this->POST_DATA['city'];
                    }
                    if ($this->POST_DATA['partner_order_id'] != 0 || $order_data[0]['partner_order_id'] != $this->POST_DATA['partner_order_id']) {
                        $bb_order_details['partner_order_id'] = $this->POST_DATA['partner_order_id'];
                    }
                    $where_bb_order = array('id' => $order_data[0]['id']);
                    if ($this->POST_DATA['current_status'] == 'Delivered') {
                            $this->My_CI->initialized_variable->delivered_count();
                            $bb_order_details['is_delivered'] = 1;
                            $remarks = "Delivery Date is ". (!empty($this->POST_DATA['delivery_date']) ? $this->POST_DATA['delivery_date'] : NULL);
                    }
                    $is_status = $this->My_CI->bb_model->update_bb_order_details($where_bb_order, $bb_order_details);
                    if ($is_status) {
                        $bb_unit_details = array(
                            'order_status' => $this->POST_DATA['current_status']
                        );
                        $this->My_CI->bb_model->update_bb_unit_details(array('partner_order_id' => $this->POST_DATA['partner_order_id'] ), $bb_unit_details);
                        $this->insert_bb_state_change($this->POST_DATA['partner_order_id'], $this->POST_DATA['current_status'], $remarks, _247AROUND_DEFAULT_AGENT, _247AROUND, NULL);
                        
                        $this->My_CI->initialized_variable->total_updated();
                        return true;
                    } else {

                        return false;
                    }
                } else {
                    return false;
                }
        } else {
            return false;
        }
    }

    /**
     * @desc Insert State change ,log
     * @param type $order_id
     * @param type $new_state
     * @param type $remarks
     * @param type $agent_id
     * @param type $partner_id
     * @param type $service_center_id
     * @return boolean
     */
    function insert_bb_state_change($order_id, $new_state, $remarks, $agent_id, $partner_id, $service_center_id) {

        //Save state change
        $state_change['order_id'] = $order_id;
        $state_change['new_state'] = $new_state;

        $order_state_change = $this->My_CI->bb_model->get_bb_state_change(array('order_id' => $state_change['order_id']));

        if (count($order_state_change) > 0) {
            $state_change['old_state'] = $order_state_change[count($order_state_change) - 1]['new_state'];
        } else {
            $state_change['old_state'] = $new_state;
        }

        $state_change['agent_id'] = $agent_id;
        $state_change['service_center_id'] = $service_center_id;
        $state_change['partner_id'] = $partner_id;
        $state_change['remarks'] = $remarks;

        // Insert data into booking state change
        $state_change_id = $this->My_CI->bb_model->insert_bb_state_change($state_change);
        if ($state_change_id) {
            return true;
        } else {
            return false;
        }
    }
    
    
    
    /**
     * @desc This function is used to get the physical condition of the order details.
     *       If physical condition is not found then get the working condition of that order.
     * @param $order_id string
     * @param $service_id string
     * @param $cp_id string
     * @return $data array()
     */
    function get_bb_physical_working_condition($order_id, $service_id, $cp_id) {
        //get category,brand from bb unit charges table
        $select_unit = 'bb_unit.category,bb_unit.brand';
        $unit_data = $this->My_CI->bb_model->get_bb_order_appliance_details(array('partner_order_id' => $order_id), $select_unit);
        $data['category'] = $unit_data[0]['category'];
        $data['brand'] = $unit_data[0]['brand'];

        //get physical condition
        $where = array('cp_id' => $cp_id,
            'service_id' => $service_id, 'category' => $data['category'], 'physical_condition != " " ' => null,'visible_to_cp' => '1');
        $select = "physical_condition";
        $physical_condition = $this->My_CI->service_centre_charges_model->get_bb_charges($where, $select, TRUE);
        //if physical condition is empty then get working condition
        if (!empty($physical_condition)) {
            $data['physical_condition'] = $physical_condition;
        } else {
            $data['physical_condition'] = '';
            $where = array('cp_id' => $cp_id, 'service_id' => $service_id, 'category' => $data['category'], 'physical_condition' => $data['physical_condition'],'visible_to_cp' => '1');
            $select = "working_condition";
            $data['working_condition'] = $this->My_CI->service_centre_charges_model->get_bb_charges($where, $select, TRUE);
        }

        return $data;
    }
    
    
    /**
     * @desc This function is used to update the buyback order when order received by
     *       the collection partner and our crm.
     * @param $post_data array()
     * @return $response array()
     */
    function process_update_received_bb_order_details($post_data) {
        $this->POST_DATA = $post_data;

        $order_id = $this->POST_DATA['order_id'];
        
        $data = array('current_status' => _247AROUND_BB_DELIVERED,
            'internal_status' => _247AROUND_BB_DELIVERED,
            'acknowledge_date' => date('Y-m-d H:i:s'));

        $update_where = array('partner_order_id' => $order_id,
            'cp_id' => $this->POST_DATA['cp_id']);
        $update_id = $this->My_CI->cp_model->update_bb_cp_order_action($update_where, $data);

        if ($update_id) {

            //update order_details
            $where = array('partner_order_id' => $order_id);
            $data = array('current_status' => _247AROUND_COMPLETED, 'internal_status' => _247AROUND_COMPLETED,'acknowledge_date' => date('Y-m-d H:i:s'),'is_delivered' => '1');
            $order_details_update_id = $this->My_CI->bb_model->update_bb_order_details($where, $data);
            if ($order_details_update_id) {
                $gst_amount = $this->gst_amount_on_profit($order_id, 0, 0);
                
                $this->My_CI->bb_model->update_bb_unit_details(array('partner_order_id' => $order_id),array('order_status' => _247AROUND_BB_DELIVERED, 
                    "gst_amount" => $gst_amount));
                // Insert state change
                if (!empty($this->My_CI->session->userdata('service_center_id'))) {
                    $this->insert_bb_state_change($order_id, _247AROUND_COMPLETED, _247AROUND_BB_DELIVERED, $this->POST_DATA['agent_id'], Null, $this->POST_DATA['cp_id']);
                } else {
                    $this->insert_bb_state_change($order_id, _247AROUND_COMPLETED, _247AROUND_BB_DELIVERED, $this->My_CI->session->userdata('id'), _247AROUND, Null);
                }

                $response['status'] = "success";
                $response['msg'] = "Order has been updated successfully";
            }
        } else {
            $response['status'] = "error";
            $response['msg'] = "Oops!!! There are some issue in updating order. Please Try Again...";
        }
        
        return $response;
    }
    
    function gst_amount_on_profit($order_id, $claimed_price, $partner_discount = 0) {
        log_message("info", __METHOD__. " Order ID ". $order_id. " Claimed Price ".$claimed_price);
        $gst_amount = 0;
        $con['where'] = array("bb_order_details.partner_order_id" => $order_id);
        $con['length'] = -1;

        $bb_unit = $this->My_CI->bb_model->get_bb_order_list($con, "bb_unit_details.service_id, bb_unit_details.partner_discount, bb_unit_details.partner_basic_charge,partner_tax_charge, bb_unit_details.partner_sweetner_charges, cp_basic_charge, cp_tax_charge");
        if (!empty($bb_unit)) {
            if($partner_discount > 0){
                $partner_amount = $bb_unit[0]->partner_basic_charge + $bb_unit[0]->partner_tax_charge - $partner_discount;
            } else {
                $partner_amount = $bb_unit[0]->partner_basic_charge + $bb_unit[0]->partner_tax_charge - $bb_unit[0]->partner_discount;
            }
            
            if($claimed_price > 0){
                $cp_amount = $claimed_price;
            } else {
                $cp_amount = $bb_unit[0]->cp_basic_charge + $bb_unit[0]->cp_tax_charge;
            }
            
            $profit = $cp_amount - $partner_amount;
            if ($profit > 0) {
                $gst_tax_rate = DEFAULT_TAX_RATE;
                if($bb_unit[0]->service_id  == _247AROUND_AC_SERVICE_ID || $bb_unit[0]->service_id == _247AROUND_TV_SERVICE_ID){
                    $gst_tax_rate = DEFAULT_PARTS_TAX_RATE;
                } else if($bb_unit[0]->service_id  == _247AROUND_MOBILE_SERVICE_ID){
                    $gst_tax_rate = DEFAULT_MOBILE_TAX_RATE;
                }
                $gst_amount = $this->My_CI->booking_model->get_calculated_tax_charge($profit, $gst_tax_rate);
            }
        }
        return $gst_amount;
    }

    /**
     * @desc Process Upload Images of those order for which report issue was claimed by collection partner
     * @param void()
     * @return void()
     */
    
    function process_bb_report_issue_upload_image($post_data){
        $this->POST_DATA = $post_data;
        //allowed only images
        $allowed_types = array('image/gif', 'image/jpg', 'image/png', 'image/jpeg');
        
        //upload order id image
        if (($_FILES['order_files']['error'] !== 4) && !empty($_FILES['order_files']['tmp_name'])) {
                $file_type = $_FILES['order_files']['type'];
                if (in_array($file_type, $allowed_types)) {
                    $tmp_name = $_FILES['order_files']['tmp_name'];
                    $file_name = str_replace(' ', '_', $_FILES['order_files']['name']);
                    $upload_order_file_new_name = "Order_id_".$this->POST_DATA['order_id'] . "_" . explode(".", $file_name)[0] . "_" . substr(md5(uniqid(rand(0, 9))), 0, 15) . "." . explode(".", $file_name)[1];
                    $upload_image_id = $this->upload_bb_report_issue_file($tmp_name,$upload_order_file_new_name, _247AROUND_BB_ORDER_ID_IMAGE_TAG);
                } else {
                    $response['status'] = "error";
                    $response['msg'] = "Please Upload valid image files only";
                    return $response;
                }
        }else{
            $response['status'] = "error";
            $response['msg'] = "Order File Is Required";
            return $response;
        }
        
        //upload damaged order images
        if (!empty($_FILES['damaged_order_files']['tmp_name'][0])) {
            $filesCount = count($_FILES['damaged_order_files']['name']);
            for ($i = 0; $i < $filesCount; $i++) {
                $file_type = $_FILES['damaged_order_files']['type'][$i];
                if (in_array($file_type, $allowed_types)) {
                    $tmp_name = $_FILES['damaged_order_files']['tmp_name'][$i];
                    $file_name = str_replace(' ', '_', $_FILES['damaged_order_files']['name'][$i]);
                    $upload_order_file_new_name = "Damaged_file_".$this->POST_DATA['order_id'] . "_" . explode(".", $file_name)[0] . "_" . substr(md5(uniqid(rand(0, 9))), 0, 15) . "." . explode(".", $file_name)[1];
                    $upload_image_id = $this->upload_bb_report_issue_file($tmp_name,$upload_order_file_new_name, _247AROUND_BB_DAMAGED_ORDER_IMAGE_TAG);
                } else {
                    $response['status'] = "error";
                    $response['msg'] = "Please Upload valid image files only";
                    return $response;
                }
            }
        }else{
            $response['status'] = "error";
            $response['msg'] = "Damaged Order File Is Required";
            return $response;
        }
    }
    
    
    /**
     * @desc Upload Images of those order for which report issue was claimed by collection partner
     * @param $tmp_name string()
     * @param $file_name string()
     * @param $tag string()
     * @return void()
     */
    function upload_bb_report_issue_file($tmp_name,$file_name, $tag = "") {
        log_message("info",__METHOD__);
        $bucket = BITBUCKET_DIRECTORY;
        $directory_xls = "misc-images/" . $file_name;
        $upload_file_status = $this->My_CI->s3->putObjectFile($tmp_name, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
        if ($upload_file_status) {
            $insert_file_data['partner_order_id'] = $this->POST_DATA['order_id'];
            $insert_file_data['cp_id'] = $this->POST_DATA['cp_id'];
            $insert_file_data['image_name'] = $file_name;
            $insert_file_data['tag'] = $tag;
            $this->My_CI->cp_model->insert_bb_order_image($insert_file_data);
        }
    }
    /**
     * @desc. If in one region only one CP is present, return cp id,  DO NOT check for ACTIVE flag.
     * If in one region two CPs are active or NOT active (basically in the same state), DO NOT return cp id.
     * If in one region more than 1 CPs are there but ONLY one is active and the others are not active, return active cp
     * @param String $region
     * @return boolean|array
     */
    function get_cp_id_from_region($region){
        
        //Get CP id from shop address table.
        $cp_shop_ddress = $this->My_CI->bb_model->get_cp_shop_address_details(array("find_in_set('$region',shop_address_region) != " => 0), 'cp_id, shop_address_city,bb_shop_address.active');
        if(count($cp_shop_ddress) ==1){
            
            return $cp_shop_ddress;
            
        } else if(count($cp_shop_ddress) > 1){
            
            $ac_cp = array();
            foreach ($cp_shop_ddress as $value) {
                if($value['active'] == 1){
                    $tmp_arr = array('cp_id' => $value['cp_id'],'shop_address_city' => $value['shop_address_city']);
                    array_push($ac_cp, $tmp_arr);
                } 
            }
            
            if(count($ac_cp) > 0){
                return $ac_cp; 

            } 
        }
        
        return false;
    }
    /**
     * @desc This is used to update assign cp process
     * @param Array $where_bb_charges
     * @return Array
     */
    function update_assign_cp_process($where_bb_charges, $order_id, $agent, $internal_status) {
        $bb_charges = array();
        foreach ($where_bb_charges as $value) {
            $b_charges = $this->My_CI->service_centre_charges_model->get_bb_charges($value, '*');
            if (!empty($b_charges)) {
                array_push($bb_charges, $b_charges[0]);
            }
        }


        if (!empty($bb_charges)) {
            if (count($bb_charges) == 1) {
                $cp_amount = $bb_charges[0]['cp_basic'] + $bb_charges[0]['cp_tax'];
                $gst_amount = $this->gst_amount_on_profit($order_id, $cp_amount, 0);
                $unit_data = array('category' => $bb_charges[0]['category'],
                    'brand' => $bb_charges[0]['brand'],
                    'physical_condition' => $bb_charges[0]['physical_condition'],
                    'working_condition' => $bb_charges[0]['working_condition'],
                    'cp_basic_charge' => $bb_charges[0]['cp_basic'],
                    'cp_tax_charge' => $bb_charges[0]['cp_tax'],
                    'gst_amount' => $gst_amount,
                    'around_commision_basic_charge' => $bb_charges[0]['around_basic'],
                    'around_commision_tax' => $bb_charges[0]['around_tax']
                );

                $where_bb_order = array('partner_order_id' => $order_id, 'partner_id' => $bb_charges[0]['partner_id']);
                $update_unit_details = $this->My_CI->bb_model->update_bb_unit_details($where_bb_order, $unit_data);


                if ($update_unit_details) {
                    $bb_order_details['assigned_cp_id'] = $bb_charges[0]['cp_id'];
                    $bb_order_details['city'] = $bb_charges[0]['city'];
                    $is_status = $this->My_CI->bb_model->update_bb_order_details($where_bb_order, $bb_order_details);
                    if ($is_status) {
                        $this->My_CI->cp_model->action_bb_cp_order_action(array('partner_order_id' => $order_id), array('cp_id' => $bb_charges[0]['cp_id'], "partner_order_id" => $order_id,
                            "create_date" => date('Y-m-d H:i:s'), "current_status" => 'Pending',
                            "internal_status" => $internal_status));

                        $this->insert_bb_state_change($order_id, ASSIGNED_VENDOR, 'Assigned CP ID: ' . $bb_charges[0]['cp_id'], $agent, _247AROUND, NULL);
                    } else {
                        log_message('info', __METHOD__ . " Error In log for this partner_order_id: " . $order_id);
                        return array('status' => false, "msg" => " Error In assigning cp");
                    }
                } else {

                    log_message('info', __METHOD__ . " Error In assigning cp_id for this partner_order_id: " . $order_id);
                    return array('status' => false, "msg" => "Charges Not Updated ");
                }
            } else {
                log_message('info', __METHOD__ . " Multiple CP Found Error In assigning cp_id for this partner_order_id: " . $order_id);
                 return array('status' => false, "msg" => " Multiple City Found" );
            }
        } else {
            return array('status' => false, "msg" => 'Charges Not Found');
        }

        return array('status' => TRUE);
    }

    /**
     * @desc This function is used to the filtered charges data from bb_charges table
     * @param void()
     * @return void()
     */
    function get_bb_price_list($post_data){
        $this->POST_DATA = $post_data;
        $where['cp_id'] = $this->POST_DATA['cp_id'];
        $where['service_id'] = $this->POST_DATA['service_id'];
        $where['physical_condition'] = $this->POST_DATA['physical_condition'];
        $where['working_condition'] = $this->POST_DATA['working_condition'];
        if(isset($this->POST_DATA['is_hide_field'])){
            $cp['hide_field'] = FALSE;
        }else{
            $cp['hide_field'] = TRUE;
        }
        
        $select = 'category , brand , city , partner_total , cp_total , around_total,visible_to_partner,visible_to_cp';
        
        $cp['charges_data'] = $this->My_CI->bb_model->get_bb_price_data($select,$where);
        
        $view = $this->My_CI->load->view('buyback/show_bb_charges', $cp);
        return $view;
    }
    
    /**
     * @desc This function is used to get the appliance SVC by using service Id
     * @param int $service_id
     * @return string $bb_qc_svc
     */
    function get_bb_qc_svc_details($service_id){
        $bb_qc_svc = "";
        switch ($service_id){
            case _247AROUND_TV_SERVICE_ID:
                $bb_qc_svc = TV_SVC;
                break;
            case _247AROUND_MOBILE_SERVICE_ID:
                $bb_qc_svc = MOBILE_SVC;
                break;
            case _247AROUND_WASHING_MACHINE_SERVICE_ID:
            case _247AROUND_AC_SERVICE_ID:
            case _247AROUND_REFRIGERATOR_SERVICE_ID:
                $bb_qc_svc = LA_SVC;
                break;
            default :
                $bb_qc_svc = "";
                break;
        }
        
        return$bb_qc_svc;
    }
    /*
     * This Function is used to get order's Summary which are non billed.
     *  Not Billed to CP
     *  Not any reimbursement invoice
     */
    function get_orders_without_invoices_and_without_reimbursement(){
         $select = 'COUNT(bb.partner_order_id) as count,bb_cp_order_action.current_status as status,round(SUM(bb_unit_details.partner_basic_charge+bb_unit_details.partner_tax_charge)) as amount';
         $data = $this->My_CI->bb_model->get_orders_without_invoices($select,1,NULL,1);
         $temp['status'] = "Total";
         $temp['count'] = $temp['amount'] = 0;
         foreach($data as $values){
             $temp['count'] = $values['count'] + $temp['count'];
             $temp['amount'] = $values['amount'] + $temp['amount'];
         }
         $data[] = $temp;
         return  json_encode($data);
    }
     /*
     * This Function is used to get order's Summary which are billed to cp on claimed price but don't get reimbursement
     *   Billed to CP on Claimed Price
     *  Not any reimbursement 
     */
   function get_orders_with_cp_invoice_and_without_reimbursement(){
         $select = 'COUNT(bb.partner_order_id) as count,bb_cp_order_action.current_status as status,round(SUM(bb_unit_details.partner_discount)) as amount';
         $data = $this->My_CI->bb_model->get_orders_without_invoices($select,1,NULL,1,false);
         $temp['status'] = "Total";
         $temp['count'] = $temp['amount'] = 0;
         foreach($data as $values){
             $temp['count'] = $values['count'] + $temp['count'];
             $temp['amount'] = $values['amount'] + $temp['amount'];
         }
         $data[] = $temp;
         return  json_encode($data);
    }
     /*
     * This Function is used to get order's Summary which are on review Page
     */
    function get_review_page_orders(){
        $select = 'COUNT(bb_cp_order_action.partner_order_id) as count,round(SUM(bb_unit_details.partner_basic_charge+bb_unit_details.partner_tax_charge)) as amount,'
                . ' CASE WHEN DATEDIFF(CURRENT_DATE,order_date) > 30 THEN "Older than 30 Days" ELSE "Within 30 Days" END as status';
        $table = 'bb_cp_order_action';
        $where['bb_cp_order_action.current_status = "InProcess"'] = NULL;
        $join['bb_unit_details'] = 'bb_unit_details.partner_order_id = bb_cp_order_action.partner_order_id';
        $join['bb_order_details'] = 'bb_order_details.partner_order_id = bb_cp_order_action.partner_order_id';
        $groupBY = array('status');
        $data = $this->My_CI->reusable_model->get_search_result_data($table,$select,$where,$join,NULL,NULL,NULL,NULL,$groupBY);
        $temp['status'] = "Total";
        $temp['count'] = $temp['amount'] = 0;
         foreach($data as $values){
             $temp['count'] = $values['count'] + $temp['count'];
             $temp['amount'] = $values['amount'] + $temp['amount'];
         }
         $data[] = $temp;
         return  json_encode($data);
   }
}
