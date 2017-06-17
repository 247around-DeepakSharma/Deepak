<?php

class Bb_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }
    
    /**
     * @desc get order details
     * @param Array $where
     * @param String $select
     * @return Array
     */
    function get_bb_order_details($where, $select){
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get("bb_order_details");
        
        return $query->result_array();
    }
    /**
     * @desc Get data from shop address
     * @param Array $where
     * @param String $select
     * @return Arary
     */
    function get_cp_shop_address_details($where, $select){
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get("bb_shop_address");
        return $query->result_array();
    }
    
    function insert_bb_order_details($data){
        $this->db->insert('bb_order_details', $data);
        return $this->db->insert_id();
    }
    
    function insert_bb_unit_details($data){
        $this->db->insert('bb_unit_details', $data);
        return $this->db->insert_id();
    }
    
    function get_bb_state_change($where){
        $this->db->select('*');
        $this->db->where($where);
        $query = $this->db->get("bb_state_change");
        return $query->result_array();
    }
    
    function insert_bb_state_change($details){
        $this->db->insert('bb_state_change', $details);
        return $this->db->insert_id();
          
    }
    
    function update_bb_order_details($where, $data){
        $this->db->where($where);
        return $this->db->update('bb_order_details', $data);
    }
    
    function update_bb_unit_details($where, $data){
        $this->db->where($where);
        return $this->db->update('bb_unit_details', $data);
    }
    
    
    /**
     * @desc This function is used to insert charges list excel data
     * @para, $charges_data array
     * @return boolean
     */
    function insert_charges_data_in_batch($charges_data){
        $this->db->truncate("bb_charges");
        return $this->db->insert_batch("bb_charges", $charges_data);
    }
    
    
    
    
}