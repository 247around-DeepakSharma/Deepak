<?php

class Booking_request_model extends CI_Model {
    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();

    }
    /**
     * @desc This function is used to get symptom of the booking. 
     * This is used when we are creating new booking.
     * @param String $select
     * @param Array $where
     * @param Array $where_in
     * @return Array
     */
    function get_booking_request_symptom($select, $where = array(), $where_in = array()){
        $this->db->select($select);
        $this->db->distinct();
        if(!empty($where)){
            $this->db->where($where);
        }
        
        if(!empty($where_in)){
            foreach ($where_in as $key => $value) {
                $this->db->where_in($key, $value);
            }
        }
        $this->db->from('symptom');
        $this->db->join('symptom_defect_solution_mapping', 'symptom.id = symptom_defect_solution_mapping.symptom_id');
        $this->db->join('request_type', 'symptom_defect_solution_mapping.request_id = request_type.id');
        $this->db->join('services', 'services.id = request_type.service_id');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * @desc This function is used to insert symptom for the booking request
     * @param Array $data
     * @return int
     */
    function insert_data($data, $table){
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }
    /**
     * @desc This is used to update booking request symptom
     * @param Array $where
     * @param Array $data
     */
    function update_table($where, $data, $table){
        $this->db->where($where);
        $this->db->update($table, $data);
    }
    
    /**
     * @desc  This function is used to get symptom while spare requesting
     * @param String $select
     * @param Array $where
     * @param Array $where_in
     * @return Array
     */
    function get_spare_request_symptom($select, $where = array(), $where_in = array()){
        $this->db->select($select);
        if(!empty($where)){
            $this->db->where($where);
        }
        
        if(!empty($where_in)){
            foreach ($where_in as $key => $value) {
                $this->db->where_in($key, $value);
            }
        }
        $this->db->from('symptom_spare_request');
        $this->db->join('request_type', 'symptom_spare_request.request_type = request_type.id');
        $this->db->join('services', 'services.id = request_type.service_id');
        $query = $this->db->get();
        return $query->result_array();
        
    }
    /**
     * @desc This function is used to return technical solution list
     * @param String $select
     * @param Array $where
     * @param Array $where_in
     * @return String
     */
    function symptom_completion_solution($select, $where = array(), $where_in = array()){
        $this->db->select($select);
        $this->db->distinct();
        if(!empty($where)){
            $this->db->where($where);
        }
        
        if(!empty($where_in)){
            foreach ($where_in as $key => $value) {
                $this->db->where_in($key, $value);
            }
        }
        $this->db->from('symptom_completion_solution');
        $this->db->join('symptom_defect_solution_mapping', 'symptom_completion_solution.id = symptom_defect_solution_mapping.solution_id');
        $this->db->join('request_type', 'symptom_defect_solution_mapping.request_id = request_type.id');
        $this->db->join('services', 'services.id = request_type.service_id');
        $query = $this->db->get();
        return $query->result_array();
    }
    /**
     * @desc This function is used to get defect list
     * @param String $select
     * @param Array $where
     * @param Array $where_in
     * @return String
     */
    function get_defects($select, $condition){
        $this->db->select($select);
        $this->db->distinct();
        $this->db->from('defect');
        $this->db->join('partners', 'partners.id = defect.partner_id');
        $this->db->join('services', 'services.id = defect.service_id');
        
        $this->conditions($condition);
        
        $query = $this->db->get();
        return $query->result_array();
    }
    /**
     * @desc This function is used to get symptom list
     * @param String $select
     * @param Array $where
     * @param Array $where_in
     * @return String
     */
    function get_symptoms($select, $condition){
        $this->db->select($select);
        $this->db->distinct();
        $this->db->from('symptom');
        $this->db->join('partners', 'partners.id = symptom.partner_id');
        $this->db->join('services', 'services.id = symptom.service_id');
        
        $this->conditions($condition);
        
        $query = $this->db->get();
        return $query->result_array();
    }
    /**
     * @desc This function is used to get solution list
     * @param String $select
     * @param Array $where
     * @param Array $where_in
     * @return String
     */
    function get_solutions($select, $condition){
        $this->db->select($select);
        $this->db->distinct();
        $this->db->from('symptom_completion_solution');
        $this->db->join('partners', 'partners.id = symptom_completion_solution.partner_id');
        $this->db->join('services', 'services.id = symptom_completion_solution.service_id');
        
        $this->conditions($condition);
        
        $query = $this->db->get();
        return $query->result_array();
    }
    /**
     * @desc This function is used to get defect based on symptoms
     * @param String $select
     * @param Array $where
     * @return Array
     */
    function get_defect_of_symptom($select, $where = array()){
        $this->db->select($select);
        $this->db->distinct();
        if(!empty($where)){
            $this->db->where($where);
        }
        
        $this->db->from('symptom_defect_solution_mapping');
        $this->db->join('defect', 'symptom_defect_solution_mapping.defect_id = defect.id');
        $query = $this->db->get();
        return $query->result_array();
    }
    /**
     * @desc This function is used to get solutions based on symptoms & defects
     * @param String $select
     * @param Array $where
     * @return Array
     */
    function get_solution_of_symptom($select, $where = array()){
        $this->db->select($select);
        $this->db->distinct();
        if(!empty($where)){
            $this->db->where($where);
        }
        
        $this->db->from('symptom_defect_solution_mapping');
        $this->db->join('symptom_completion_solution', 'symptom_defect_solution_mapping.solution_id = symptom_completion_solution.id AND product_id=service_id');
        $query = $this->db->get();
        return $query->result_array();
    }
    /**
     * @desc This function is used to get symptom, defect & solution mapping list
     * @param String $select
     * @param Array $where
     * @param Array $where_in
     * @return String
     */
    function get_symptom_defect_solution_mapping($condition, $select){
        $this->db->select($select);
        $this->db->distinct();
        $this->db->from('symptom_defect_solution_mapping');
        $this->db->join('request_type', 'symptom_defect_solution_mapping.request_id = request_type.id');
        $this->db->join('services', 'services.id = request_type.service_id');
        
        $this->conditions($condition);
        
        $query = $this->db->get();//echo $this->db->last_query();
        //log_message("info",__METHOD__. $this->db->last_query());
        return $query->result_array();
    }
    
    function conditions($condition) {
        if(!empty($condition['join'])){
            foreach($condition['join'] as $key=>$values){
                $this->db->join($key, $values);
            }
        }
        
        if(!empty($condition['where'])){
            $this->db->where($condition['where']);
        }
        
        if (isset($condition['where_in'])) {
            foreach ($condition['where_in'] as $index => $value) {
                $this->db->where_in($index, $value);
            }
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
        
        if (!empty($condition['search_value'])) {
            $like = "";
            foreach ($condition['column_search'] as $key => $item) { // loop column 
                // if datatable send POST for search
                if ($key === 0) { // first loop
                    $like .= "( " . $item . " LIKE '%" . $condition['search_value'] . "%' ";
                } else {
                    $like .= " OR " . $item . " LIKE '%" . $condition['search_value'] . "%' ";
                }
            }
            $like .= ") ";

            $this->db->where($like, null, false);
        }
        if(isset($condition['length'])){
            if ($condition['length'] != -1) {
                $this->db->limit($condition['length'], $condition['start']);
            }
        }
        
        if(!empty($condition['order_by'])){
            $this->db->order_by($condition['order_by']);
        }else if(!empty ($condition['order'])){
            $this->db->order_by($condition['column_order'][$condition['order'][0]['column']], $condition['order'][0]['dir']);
        }
    }
}
