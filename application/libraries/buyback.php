<?php

class Buyback {

    Private $POST_DATA = array();

    public function __construct() {
        $this->My_CI = & get_instance();
        $this->My_CI->load->helper(array('form', 'url'));
        $this->My_CI->load->library("initialized_variable");
        $this->My_CI->load->library("session");
        $this->My_CI->load->library("s3");
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
                . 'bb_order_details.internal_status, city, partner_tracking_id, bb_order_details.partner_order_id');
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
        $array = array('shop_address_city' => $this->POST_DATA['city'], 'active' => 1);

        //Get CP id from shop address table.
        $cp_shop_ddress = $this->My_CI->bb_model->get_cp_shop_address_details($array, '*');
        $bb_charges = array();
        $cp_id = FALSE;
        $service_id = 0;
        if (!empty($cp_shop_ddress)) {
            //Get Charges list
            $bb_charges = $this->My_CI->service_centre_charges_model->get_bb_charges(array(
                'partner_id' => $this->POST_DATA['partner_id'],
                'city' => $this->POST_DATA['city'],
                'order_key' => $this->POST_DATA['order_key'],
                'cp_id' => $cp_shop_ddress[0]['cp_id']
                    ), '*');
            if (!empty($bb_charges)) {
                $cp_id = $bb_charges[0]['cp_id'];
                $service_id = $bb_charges[0]['service_id'];
            }
        }
        if (empty($service_id)) {
            $service_id = $this->get_service_id_by_appliance();
        }
        if (empty($cp_id)) {
            $this->My_CI->initialized_variable->not_assigned_order();
        }
        // Insert bb order details
        $is_insert = $this->insert_bb_order_details($cp_id);
        if ($is_insert) {
            // Insert bb unit details
            $is_unit = $this->insert_bb_unit_details($bb_charges, $service_id);
            if ($is_unit) {
                if (!empty($cp_id)) {
                    $this->My_CI->cp_model->insert_bb_cp_order_action(array(
                        "partner_order_id" => $this->POST_DATA['partner_order_id'],
                        "cp_id" => $cp_id,
                        "create_date" => date('Y-m-d H:i:s'),
                        "current_status" => 'Pending',
                        "internal_status" => $this->POST_DATA['current_status']
                    ));
                }
                // Insert state change
                $this->insert_bb_state_change($this->POST_DATA['partner_order_id'], $this->POST_DATA['current_status'], $this->POST_DATA['order_key'], _247AROUND_DEFAULT_AGENT, _247AROUND, NULL);
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

        if (stristr($this->POST_DATA['subcat'], "Laundry")) {
            $appliance_name = 'Washing Machine';
        }
        if (stristr($this->POST_DATA['subcat'], "Air Conditioners")) {
            $appliance_name = 'Air Conditioner';
        }
        if (stristr($this->POST_DATA['subcat'], "Refrigerators")) {
            $appliance_name = 'Refrigerator';
        }
        if (stristr($this->POST_DATA['subcat'], "Tv")) {
            $appliance_name = 'Television';
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
            'current_status' => $this->POST_DATA['current_status'],
            'internal_status' => $this->POST_DATA['current_status'],
            'create_date' => date('Y-m-d H:i:s'),
            'assigned_cp_id' => (!empty($cp_id) ? $cp_id : NULL),
            'delivery_date' => (!empty($this->POST_DATA['delivery_date']) ? $this->POST_DATA['delivery_date'] : NULL),
            'partner_tracking_id' => (isset($this->POST_DATA['tracking_id']) ? $this->POST_DATA['tracking_id'] : NULL)
        );

        return $this->My_CI->bb_model->insert_bb_order_details($bb_order_details);
    }

    /**
     * @desc insert unit details
     * @param Array $bb_charges
     * @return Array
     */
    function insert_bb_unit_details($bb_charges, $service_id) {

        $bb_unit_details = array(
            'partner_id' => $this->POST_DATA['partner_id'],
            'partner_order_id' => $this->POST_DATA['partner_order_id'],
            'category' => (!empty($bb_charges) ? $bb_charges[0]['category'] : NULL),
            'brand' => (!empty($bb_charges) ? $bb_charges[0]['brand'] : NULL),
            'physical_condition' => (!empty($bb_charges) ? $bb_charges[0]['physical_condition'] : NULL),
            'working_condition' => (!empty($bb_charges) ? $bb_charges[0]['working_condition'] : NULL),
            'order_status' => $this->POST_DATA['current_status'],
            'partner_basic_charge' => $this->POST_DATA['partner_basic_charge'],
            'cp_basic_charge' => (!empty($bb_charges) ? $bb_charges[0]['cp_basic'] : 0),
            'cp_tax_charge' => (!empty($bb_charges) ? $bb_charges[0]['cp_tax'] : 0),
            'around_commision_basic_charge' => (!empty($bb_charges) ? $bb_charges[0]['around_basic'] : 0),
            'around_commision_tax' => (!empty($bb_charges) ? $bb_charges[0]['around_tax'] : 0),
            'partner_sweetner_charges' => $this->POST_DATA['partner_sweetner_charges'],
            'create_date' => date('Y-m-d'),
            'order_key' => $this->POST_DATA['order_key'],
            'service_id' => (!empty($bb_charges) ? $bb_charges[0]['service_id'] : $service_id),
        );


        return $this->My_CI->bb_model->insert_bb_unit_details($bb_unit_details);
    }

    /**
     * @desc Order already exists in the db. It update the new status. 
     * When Old status is deleiverd or completed then we will not update 
     * @param Array $order_data
     * @return boolean
     */

    function update_bb_order($order_data){
        if($order_data[0]['current_status'] != "Delivered" || 
                $order_data[0]['current_status'] != "Completed" || 
                $order_data[0]['current_status'] != $this->POST_DATA['current_status']){
            

            $bb_order_details = array(
                'current_status' => $this->POST_DATA['current_status'],
                'internal_status' => $this->POST_DATA['current_status'],
                'delivery_date' => (!empty($this->POST_DATA['delivery_date']) ? $this->POST_DATA['delivery_date'] : NULL)
            );
            if($this->POST_DATA['tracking_id'] != 0 ||$order_data[0]['partner_tracking_id'] != $this->POST_DATA['tracking_id']){
                $bb_order_details['partner_tracking_id'] = $this->POST_DATA['tracking_id'];
            }
            if($this->POST_DATA['city'] != 0 || $order_data[0]['city'] != $this->POST_DATA['city']){
                $bb_order_details['city'] = $this->POST_DATA['city'];
            }
            if($this->POST_DATA['partner_order_id'] != 0 || $order_data[0]['partner_order_id'] != $this->POST_DATA['partner_order_id']){
                $bb_order_details['partner_order_id'] = $this->POST_DATA['partner_order_id'];
            }
            $where_bb_order = array('id' => $order_data[0]['id']);
            $is_status = $this->My_CI->bb_model->update_bb_order_details($where_bb_order, $bb_order_details);
            if ($is_status) {
                $bb_unit_details = array(
                    'order_status' => $this->POST_DATA['current_status']
                );
                $this->My_CI->bb_model->update_bb_unit_details($where_bb_order, $bb_unit_details);
                $this->insert_bb_state_change($this->POST_DATA['partner_order_id'], $this->POST_DATA['current_status'], NULL, _247AROUND_DEFAULT_AGENT, _247AROUND, NULL);
                if ($this->POST_DATA['current_status'] == 'Delivered') {
                    $this->My_CI->initialized_variable->delivered_count();
                }
                $this->My_CI->initialized_variable->total_updated();
                return true;
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
            'service_id' => $service_id, 'category' => $data['category'], 'physical_condition != " " ' => null);
        $select = "physical_condition";
        $physical_condition = $this->My_CI->service_centre_charges_model->get_bb_charges($where, $select, TRUE);
        //if physical condition is empty then get working condition
        if (!empty($physical_condition)) {
            $data['physical_condition'] = $physical_condition;
        } else {
            $data['physical_condition'] = '';
            $where = array('cp_id' => $cp_id, 'service_id' => $service_id, 'category' => $data['category'], 'physical_condition' => $data['physical_condition']);
            $select = "working_condition";
            $data['working_condition'] = $this->My_CI->service_centre_charges_model->get_bb_charges($where, $select, TRUE);
        }

        return $data;
    }
    
    
    /**
     * @desc This function is used to update the buyback order when order received by
     *       the collection partner.
     * @param $post_data array()
     * @return $response array()
     */
    function process_update_received_bb_order_details($post_data) {
        $this->POST_DATA = $post_data;

        $order_id = $this->POST_DATA['order_id'];
        $physical_condition = $this->POST_DATA['order_physical_condition'];
        if (!empty($physical_condition)) {
            $physical_condition = $this->POST_DATA['order_physical_condition'];
        } else {
            $physical_condition = '';
        }
        //get order key
        $where = array('cp_id' => $this->POST_DATA['cp_id'],
            'service_id' => $this->POST_DATA['service_id'],
            'category' => $this->POST_DATA['category'],
            'physical_condition' => $physical_condition,
            'working_condition' => $this->POST_DATA['order_working_condition'],
            'brand' => $this->POST_DATA['brand'],
            'city' => $this->POST_DATA['city']);
        $select = "order_key";
        $order_key_data = $this->My_CI->service_centre_charges_model->get_bb_charges($where, $select, TRUE);
        if (!empty($order_key_data)) {
            $order_key = $order_key_data[0]['order_key'];
        } else {
            $order_key = '';
        }
        $data = array('category' => $this->POST_DATA['category'],
            'physical_condition' => $physical_condition,
            'working_condition' => $this->POST_DATA['order_working_condition'],
            'remarks' => $this->POST_DATA['remarks'],
            'brand' => $this->POST_DATA['brand'],
            'current_status' => _247AROUND_BB_ORDER_COMPLETED_CURRENT_STATUS,
            'internal_status' => _247AROUND_BB_ORDER_COMPLETED_INTERNAL_STATUS,
            'order_key' => $order_key,
            'create_date' => date('Y-m-d H:i:s'));

        $update_where = array('partner_order_id' => $order_id,
            'cp_id' => $this->POST_DATA['cp_id']);
        $update_id = $this->My_CI->cp_model->update_bb_cp_order_action($update_where, $data);

        if ($update_id) {

            //update order_details
            $where = array('partner_order_id' => $order_id);
            $data = array('current_status' => _247AROUND_BB_ORDER_COMPLETED_CURRENT_STATUS, 'internal_status' => _247AROUND_BB_ORDER_COMPLETED_INTERNAL_STATUS);
            $order_details_update_id = $this->My_CI->bb_model->update_bb_order_details($where, $data);
            if ($order_details_update_id) {
                // Insert state change
                if (!empty($this->My_CI->session->userdata('service_center_id'))) {
                    $this->insert_bb_state_change($order_id, _247AROUND_BB_ORDER_COMPLETED_CURRENT_STATUS, $this->POST_DATA['remarks'], $this->POST_DATA['cp_id'], Null, $this->POST_DATA['cp_id']);
                } else {
                    $this->insert_bb_state_change($order_id, _247AROUND_BB_ORDER_COMPLETED_CURRENT_STATUS, $this->POST_DATA['remarks'], $this->My_CI->session->userdata('id'), _247AROUND, Null);
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
    
    
    /**
     * @desc This function is used to update not received buyback order.
     * @param $post_data array()
     * @return $response array()
     */
    function process_update_not_received_bb_order_details($post_data){
        
        $this->POST_DATA = $post_data;
        
        $update_data = array('current_status' => _247AROUND_BB_DELIVERED,
                             'internal_status' => _247AROUND_BB_ORDER_NOT_RECEIVED_INTERNAL_STATUS
                            );
        
        $update_where = array('partner_order_id' => $this->POST_DATA['order_id'],
                            'cp_id' => $this->POST_DATA['cp_id']);
        
        //update cp action table
        $update_id = $this->My_CI->cp_model->update_bb_cp_order_action($update_where,$update_data);
        
        if ($update_id) {
            
            //update order_details
            $where = array('partner_order_id' => $this->POST_DATA['order_id']);
            $data = array('current_status' => _247AROUND_BB_DELIVERED, 'internal_status' => _247AROUND_BB_ORDER_NOT_RECEIVED_INTERNAL_STATUS);
            $order_details_update_id = $this->My_CI->bb_model->update_bb_order_details($where, $data);

            if($order_details_update_id){
                // Insert state change
                if (!empty($this->My_CI->session->userdata('service_center_id'))) {
                    $this->insert_bb_state_change($this->POST_DATA['order_id'], _247AROUND_BB_NOT_DELIVERED_IN_PROCESS, '', $this->POST_DATA['cp_id'], Null, $this->POST_DATA['cp_id']);
                } else {
                    $this->insert_bb_state_change($this->POST_DATA['order_id'], _247AROUND_BB_NOT_DELIVERED_IN_PROCESS, '', $this->My_CI->session->userdata('id'), _247AROUND, Null);
                }
                
                $response['status'] = "success";
                $response['msg'] = "Order has been updated successfully";
            }else{
                $response['status'] = "error";
                $response['msg'] = "Oops!!! There are some issue in updating order. Please Try Again...";
            }

        }else{
             $response['status'] = "error";
             $response['msg'] = "Oops!!! There are some issue in updating order. Please Try Again...";
        }
        
        return $response;
    }
    
    
    /**
     * @desc Update Those order for which report issue was claimed by collection partner
     * @param $post_data array()
     * @return $response array()
     */
    function process_bb_order_report_issue_update($post_data) {
        log_message("info",__METHOD__);
        $this->POST_DATA = $post_data;
        
        $order_id = $this->POST_DATA['order_id'];
        //allowed only images
        $allowed_types = array('image/gif', 'image/jpg', 'image/png', 'image/jpeg');
        //process upload images
        if (($_FILES['order_files']['error'] != 4) && !empty($_FILES['order_files']['tmp_name'])) {
                $file_type = $_FILES['order_files']['type'];
                if (in_array($file_type, $allowed_types)) {
                    $tmp_name = $_FILES['order_files']['tmp_name'];
                    $file_name = str_replace(' ', '_', $_FILES['order_files']['name']);
                    $upload_order_file_new_name = "Order_id_".$order_id . "_" . explode(".", $file_name)[0] . "_" . substr(md5(uniqid(rand(0, 9))), 0, 15) . "." . explode(".", $file_name)[1];
                    $upload_image_id = $this->upload_bb_report_issue_file($tmp_name,$upload_order_file_new_name, _247AROUND_BB_ORDER_ID_IMAGE_TAG);
                } else {
                    $response['status'] = "error";
                    $response['msg'] = "Please Upload valid image files only";
                    return $response;
                }
        }

        if (($_FILES['damaged_order_files']['error'] != 4) && !empty($_FILES['damaged_order_files']['tmp_name'])) {
            $filesCount = count($_FILES['damaged_order_files']['name']);
            for ($i = 0; $i < $filesCount; $i++) {
                $file_type = $_FILES['damaged_order_files']['type'][$i];
                if (in_array($file_type, $allowed_types)) {
                    $tmp_name = $_FILES['damaged_order_files']['tmp_name'][$i];
                    $file_name = str_replace(' ', '_', $_FILES['damaged_order_files']['name'][$i]);
                    $upload_order_file_new_name = "Damaged_file_".$order_id . "_" . explode(".", $file_name)[0] . "_" . substr(md5(uniqid(rand(0, 9))), 0, 15) . "." . explode(".", $file_name)[1];
                    $upload_image_id = $this->upload_bb_report_issue_file($tmp_name,$upload_order_file_new_name, _247AROUND_BB_DAMAGED_ORDER_IMAGE_TAG);
                } else {
                    $response['status'] = "error";
                    $response['msg'] = "Please Upload valid image files only";
                    return $response;
                }
            }
        }

        $physical_condition = isset($this->POST_DATA['order_physical_condition'])?$this->POST_DATA['order_physical_condition']:'';
        if (!empty($physical_condition)) {
            $physical_condition = $this->POST_DATA['order_physical_condition'];
        } else {
            $physical_condition = '';
        }

        $data = array(
            'category' =>  $this->POST_DATA['category'],
            'physical_condition' =>  $physical_condition,
            'working_condition' => $this->POST_DATA['order_working_condition'],
            'remarks' =>  $this->POST_DATA['remarks'],
            'brand' =>  $this->POST_DATA['order_brand'],
            'current_status' => _247AROUND_BB_IN_PROCESS,
            'internal_status' => _247AROUND_BB_REPORT_ISSUE_INTERNAL_STATUS,
            'order_key' => $this->POST_DATA['partner_order_key']);

        $where = array('partner_order_id' => $order_id,
            'cp_id' => $this->POST_DATA['cp_id']);
        
        //update bb_cp_action_table
        $update_id = $this->My_CI->cp_model->update_bb_cp_order_action($where, $data);
        if ($update_id) {
            log_message("info",__METHOD__. "Cp Action table updated for order id: ".$order_id);
            // Insert state change
            if (!empty($this->My_CI->session->userdata('service_center_id'))) {
                $this->insert_bb_state_change($order_id, _247AROUND_BB_REPORT_ISSUE_IN_PROCESS, $this->POST_DATA['remarks'], $this->My_CI->session->userdata('service_center_agent_id'), NULL, $this->My_CI->session->userdata('service_center_id'));
                $response['status'] = "success";
                $response['msg'] = "Order has been updated successfully";
            } else {
                
                $order_details_data = array('current_status' => _247AROUND_BB_IN_PROCESS,
                          'internal_status' => _247AROUND_BB_REPORT_ISSUE_INTERNAL_STATUS
                );

                $order_details_where = array('partner_order_id' => $order_id,
                               'assigned_cp_id' => $this->POST_DATA['cp_id']);
                
                //update order details table
                $update_id = $this->My_CI->bb_model->update_bb_order_details($order_details_where, $order_details_data);
                
                if($update_id){
                    
                    log_message("info",__METHOD__. "Order Details table updated for order id: ".$order_id);
                    $this->insert_bb_state_change($order_id, _247AROUND_BB_REPORT_ISSUE_IN_PROCESS,$this->POST_DATA['remarks'], $this->My_CI->session->userdata('id'), _247AROUND, Null);
                    $response['status'] = "success";
                    $response['msg'] = "Order has been updated successfully";
                }else{
                    
                    log_message("info",__METHOD__. "error In Updating Cp Action table for order id: ".$order_id);
                    $response['status'] = "error";
                    $response['msg'] = "Oops!!! There are some issue in updating order. Please Try Again...";
                }
            }
        } else {
            $response['status'] = "error";
            $response['msg'] = "Oops!!! There are some issue in updating order. Please Try Again...";
        }
        
        return $response;
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
            $insert_id = $this->My_CI->cp_model->insert_bb_order_image($insert_file_data);
        }
    }

}
