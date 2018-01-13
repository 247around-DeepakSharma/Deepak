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
    
    function insert_engineer_action_sign($data){
         $this->db->insert("engineer_table_sign", $data);
          return $this->db->insert_id();
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
        
        $this->db->join('engineer_table_sign', 'booking_details.booking_id = engineer_booking_action.booking_id '
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
}