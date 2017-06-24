<?php

class Buyback {

    Private $POST_DATA = array();
   
    public function __construct() {
        $this->My_CI = & get_instance();
        $this->My_CI->load->helper(array('form', 'url'));
        $this->My_CI->load->library("initialized_variable");
        $this->My_CI->load->model("service_centre_charges_model");
        $this->My_CI->load->model("bb_model");

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
        $is_exist_order = $this->My_CI->bb_model->get_bb_order_details($where_bb_order, 'id, current_status, internal_status');
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
      
        if (!empty($cp_shop_ddress)) {
            //Get Charges list
            $bb_charges = $this->My_CI->service_centre_charges_model->get_bb_charges(array(
                'partner_id' => $this->POST_DATA['partner_id'],
                'city' => $this->POST_DATA['city'],
                'order_key' => $this->POST_DATA['order_key'],
                'cp_id' => $cp_shop_ddress[0]['cp_id']
                    ), '*');
            if(!empty($bb_charges)){
                $cp_id = $bb_charges[0]['cp_id'];
            }
        }    
        
        // Insert bb order details
        $is_insert = $this->insert_bb_order_details($cp_id);
        if ($is_insert) {
            // Insert bb unit details
            $is_unit = $this->insert_bb_unit_details($bb_charges);
            if ($is_unit) {
                // Insert state change
                $this->insert_bb_state_change($this->POST_DATA['partner_order_id'],
                        $this->POST_DATA['current_status'], $this->POST_DATA['order_key'],
                        _247AROUND_DEFAULT_AGENT, _247AROUND, NULL);
                if($this->POST_DATA['current_status'] == 'Delivered'){
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
    function insert_bb_unit_details($bb_charges) {
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
            'service_id' => (!empty($bb_charges) ? $bb_charges[0]['service_id'] : 0),
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
                $order_data[0]['current_status'] != "Completed"){
            
            $bb_order_details = array(
                'current_status' => $this->POST_DATA['current_status'],
                'internal_status' => $this->POST_DATA['current_status'],
                'delivery_date' => (!empty($this->POST_DATA['delivery_date']) ? $this->POST_DATA['delivery_date'] : NULL)
            );
            $where_bb_order = array('partner_id' => $this->POST_DATA['partner_id'], 'partner_order_id' => $this->POST_DATA['partner_id']);
            $is_status = $this->My_CI->bb_model->update_bb_order_details($where_bb_order, $bb_order_details);
            if($is_status){
                    $bb_unit_details = array(
                    'order_status' => $this->POST_DATA['current_status'],
                    'delivery_date' => $this->POST_DATA['delivery_date']
                );
                $this->My_CI->bb_model->update_bb_unit_details($where_bb_order, $bb_unit_details);
                $this->insert_bb_state_change($this->POST_DATA['partner_order_id'],
                        $this->POST_DATA['current_status'], NULL,
                        _247AROUND_DEFAULT_AGENT, _247AROUND, NULL);
                if($this->POST_DATA['current_status'] == 'Delivered'){
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
    function insert_bb_state_change($order_id, $new_state, $remarks, $agent_id, $partner_id, $service_center_id){
        
            //Save state change
            $state_change['order_id'] = $order_id;
            $state_change['new_state'] =  $new_state;
           
            $order_state_change = $this->My_CI->bb_model->get_bb_state_change(array('order_id'=>  $state_change['order_id']));
            
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
            if($state_change_id){
                return true;
            } else {
                return false;
               
            }
    }

}  
