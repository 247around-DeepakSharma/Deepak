<?php

class Engineer_model extends CI_Model {
    
    var $order = array('booking_unit_details.product_or_services' => 'desc'); // default order 

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }
    
         
     function insert_engineer_action($data){
         $this->db->insert("engineer_booking_action", $data);
          return $this->db->insert_id();
     }
     
    /**
     * @desc This is used to delete booking id from Engineer Action table
     * @param type $booking_id
     */
    function delete_booking_from_engineer_table($booking_id) {
        if (!empty($booking_id) || $booking_id != "0") {
            $result = $this->getengineer_action_data("booking_id",array("booking_id" => $booking_id));
           
            if (!empty($result)) {
                log_message('info', __METHOD__ . "=> Booking ID: " . $booking_id . "=> Old vendor data " .
                        print_r($result, TRUE));

                $this->db->where('booking_id', $booking_id);
                $this->db->delete("engineer_booking_action");
                log_message('info', __METHOD__ . "=> Delete SQL: " . $this->db->last_query());
            }
        }
    }
    
    function delete_engineer_table($where){
        if(!empty($where)){
            $this->db->where($where);
            $this->db->delete('engineer_booking_action');
        }
        log_message('info', __FUNCTION__ . '=> Delete sc unit details: ' .$this->db->last_query());
    }
    /**
     * @desc This is used to select engineer action data 
     * @param String $select
     * @param Array $where
     * @return Array
     */
    function getengineer_action_data($select, $where){
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get("engineer_booking_action");
        return $query->result_array();
    }
    
    function getengineer_sign_table_data($select, $where){
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get("engineer_table_sign");
        return $query->result_array();
    }
    
    function update_engineer_table($data, $where){
        $this->db->where($where);
        $this->db->update("engineer_booking_action", $data);
    }
    
    function update_engineer_action_sig($data, $where){
        $this->db->where($where);
        $this->db->update("engineer_table_sign", $data);
    }
    
    function insert_engineer_action_sign($data){
        $this->db->insert("engineer_table_sign", $data);
        return $this->db->insert_id();
    }
    
    function get_engineer_sign($select, $where){
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get("engineer_table_sign");
        return $query->result_array();
        
    }
     
     function get_engineer_action_table_list($post, $select) {
        $this->_get_engineer_action_table_list($post, $select);
        if(isset($post['length'])){
            if ($post['length'] != -1) {
                $this->db->limit($post['length'], $post['start']);
            }
        }
        $query = $this->db->get();

        return $query->result();
    }
    
   
    public function _get_engineer_action_table_list($post, $select) {
        $this->db->from('engineer_booking_action');
        $this->db->distinct();
        $this->db->select($select);
        $this->db->join('booking_details', 'booking_details.booking_id = engineer_booking_action.booking_id '
                . ' AND booking_details.assigned_vendor_id = engineer_booking_action.service_center_id ');
       
        $this->db->join('service_center_booking_action', 'engineer_booking_action.booking_id = service_center_booking_action.booking_id '
                . ' AND service_center_booking_action.service_center_id = engineer_booking_action.service_center_id '
                . ' AND service_center_booking_action.unit_details_id = engineer_booking_action.unit_details_id');
        
         $this->db->join('booking_unit_details', 'booking_unit_details.booking_id = service_center_booking_action.booking_id '
                . ' AND booking_unit_details.id = service_center_booking_action.unit_details_id ');
        
        $this->db->join('engineer_table_sign', 'booking_details.booking_id = engineer_table_sign.booking_id '
                . ' AND booking_details.assigned_vendor_id = engineer_table_sign.service_center_id ', 'left');
       
        $this->db->join('users', 'users.user_id = booking_details.user_id', 'left');

        $this->db->join('services', 'services.id = booking_details.service_id');
        if (!empty($post['where'])) {
            $this->db->where($post['where'], FALSE);
        }
        if (isset($post['where_in'])) {
            foreach ($post['where_in'] as $index => $value) {

                $this->db->where_in($index, $value);
            }
        }

        if (!empty($post['search_value'])) {
            $like = "";
            foreach ($post['column_search'] as $key => $item) { // loop column 
                // if datatable send POST for search
                if ($key === 0) { // first loop
                    $like .= "( " . $item . " LIKE '%" . $post['search_value'] . "%' ";
                    // $this->db->like($item, $post['search_value']);
                } else {
                    $like .= " OR " . $item . " LIKE '%" . $post['search_value'] . "%' ";
                    //$this->db->or_like($item, $post['search_value']);
                }
            }
            $like .= ") ";

            $this->db->where($like, null, false);
        }

        if (!empty($post['order'])) { // here order processing
            $this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
    
    function get_engineers_details($where, $select){
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get("engineer_details");
        return $query->result_array();
        
    }
    
    /* 
     * @Desc - This function is used to insert data into engineer_appliance_mapping table      
     */
    function insert_engineer_appliance_mapping($data){
        $this->db->insert_batch("engineer_appliance_mapping", $data);
        return $this->db->insert_id();
    }
    
     /* 
     * @Desc - This function is used to get data from engineer_appliance_mapping table      
     */
    function get_engineer_appliance($where, $select='*'){
        $this->db->select($select);
        if(!empty($where)){
           $this->db->where($where); 
        }
        $query = $this->db->get("engineer_appliance_mapping");
        return $query->result_array();
    }
    
     /* 
     * @Desc - This function is used to update data from engineer_appliance_mapping table      
     */
    function update_engineer_appliance($where, $data){
        $this->db->where($where);
        $this->db->update("engineer_appliance_mapping", $data);
    }
    
    function update_engineer_appliance_mapping($engineer_id, $services){
        $this->update_engineer_appliance(array("engineer_id"=>$engineer_id), array("is_active"=>0));
        foreach ($services as $key => $value) {
            $data = array();
            $where = array(
                "engineer_id" => $engineer_id,
                "service_id" => $value,
            );
            
            $check_service = $this->get_engineer_appliance($where, "id");
            if(empty($check_service)){
                array_push($data, $where);
                $this->insert_engineer_appliance_mapping($data);
            }
            else{
                $this->update_engineer_appliance(array("id"=>$check_service[0]['id']), array("is_active"=>1));
            }
        }
    }
    
    function get_service_based_engineer($where, $select = "*"){
        $this->db->select($select);
        $this->db->join('engineer_appliance_mapping', 'engineer_appliance_mapping.engineer_id = engineer_details.id');
        if($where){
           $this->db->where($where);  
        }
        $query = $this->db->get("engineer_details");
        return $query->result_array();
    }
    
    function get_engineer_booking_details($select="*", $where = array(), $is_user = false, $is_service = false, $is_unit = false, $is_partner = false, $is_vendor = false){
        $this->db->select($select, false);
        $this->db->from('engineer_booking_action');
        $this->db->where($where);
        $this->db->join("booking_details", "booking_details.booking_id = engineer_booking_action.booking_id");
        if($is_service){
            $this->db->join("services", "services.id = booking_details.service_id");
        }
        if($is_user){
            $this->db->join('users',' users.user_id = booking_details.user_id');
        }
        if($is_unit){
            $this->db->join('booking_unit_details', 'booking_unit_details.booking_id = booking_details.booking_id');
        }
        if($is_partner){
            $this->db->join('partners', 'booking_details.partner_id = partners.id'); 
        }
        if($is_vendor){
            $this->db->join('service_centres', 'booking_details.assigned_vendor_id = service_centres.id'); 
        }
        $query = $this->db->get();
        echo $this->db->last_query(); die();
        return $query->result_array();
    }
}