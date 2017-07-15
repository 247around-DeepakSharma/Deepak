<?php

class Dealer_model extends CI_Model {
    var $dealer_search_column = array('dealer_phone_number_1','owner_phone_number_1');
    
    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }

    /**
     * @desc: check dealer login 
     * @param: Array(userid, password)
     * @return : Array()  
     */
    function entity_login($data) {
        $this->db->select('*');
        $this->db->where($data);
        $query = $this->db->get('entity_login_table');
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        } else {

            return false;
        }
    }

    function get_dealer_mapping_details($condition, $select){
        $this->db->distinct();
        $this->db->select($select);
        $this->db->from('dealer_details');
        $this->db->join('dealer_brand_mapping', 'dealer_details.dealer_id = dealer_brand_mapping.dealer_id',"Left");
        $this->db->join('services', 'services.id = dealer_brand_mapping.service_id');
        
        if(!empty($condition['where'])){
             $this->db->where($condition['where']);
        }
       
        
        if(!empty($condition['where_in'])){
            $this->db->where_in($condition['where_in']);
        }
        if (!empty($condition['search'])) {
            $key = 0;
            $like = "";
            foreach ($condition['search'] as $index => $item) {
                if ($key === 0) { // first loop
                   // $this->db->like($index, $item);
                    $like .= "( ".$index." LIKE '%".$item."%' ";
                } else {
                    $like .= " OR ".$index." LIKE '%".$item."%' ";
                }
                $key++;
            }
            $like .= ") ";

            $this->db->where($like, null, false);
        }
        if(!empty($condition['order_by'])){
            $this->db->order_by($condition['order_by']);
        }
        
        $query = $this->db->get();
        //log_message("info",__METHOD__. $this->db->last_query());
        return $query->result_array();
    }
    
    /**
     * @desc: This is used to insert the dealer details into database
     * @param Array $data
     * @return Array
     */
    function insert_dealer_details($data){
        $this->db->insert("dealer_details", $data);
        $insert_id = $this->db->insert_id();
        return  $insert_id;
    }
    
    function insert_dealer_mapping_batch($data){
        $this->db->insert_batch("dealer_brand_mapping", $data);
        $insert_id = $this->db->insert_id();
        return  $insert_id;
    }
    
    function insert_entity_login($data){
        $this->db->insert("entity_login_table", $data);
        $insert_id = $this->db->insert_id();
        return  $insert_id;
    }
}