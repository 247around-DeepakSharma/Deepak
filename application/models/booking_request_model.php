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
        if(!empty($where)){
            $this->db->where($where);
        }
        
        if(!empty($where_in)){
            foreach ($where_in as $key => $value) {
                $this->db->where_in($key, $value);
            }
        }
        $this->db->from('symptom');
        $this->db->join('request_type', 'symptom.request_type = request_type.id');
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
     * @desc This function is used  get technical problem while booking completion
     * @param String $select
     * @param Array $where
     * @param Array $where_in
     * @return Array
     */
    function get_completion_symptom($select, $where = array(), $where_in = array()){
        $this->db->select($select);
        if(!empty($where)){
            $this->db->where($where);
        }
        
        if(!empty($where_in)){
            foreach ($where_in as $key => $value) {
                $this->db->where_in($key, $value);
            }
        }
        $this->db->from('symptom');
        $this->db->join('request_type', 'symptom.request_type = request_type.id');
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
        if(!empty($where)){
            $this->db->where($where);
        }
        
        if(!empty($where_in)){
            foreach ($where_in as $key => $value) {
                $this->db->where_in($key, $value);
            }
        }
        $this->db->from('symptom_completion_solution');
        $this->db->join('request_type', 'symptom_completion_solution.request_type = request_type.id');
        $this->db->join('services', 'services.id = request_type.service_id');
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
        $this->db->join('symptom_completion_solution', 'symptom_defect_solution_mapping.solution_id = symptom_completion_solution.id');
        $query = $this->db->get();
        return $query->result_array();
    }
}
