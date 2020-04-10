<?php

class nrn_model extends CI_Model {

    public $table_247around_nrn_details = '247around_nrn_details';

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }

    function insert_nrn_details($data) {
        $this->db->insert($this->table_247around_nrn_details, $data);
        return $this->db->insert_id();
    }

    function get_all_nrn_records() {
        $this->db->select('*');
        $this->db->from($this->table_247around_nrn_details);
        $query = $this->db->get();
        if ($query !== FALSE && $query->num_rows() > 0) {
            return $query->result_array();
        }
        return NULL;
    }

    function get_nrn_records($nrn_id = NULL) {
        $this->db->select('*');
        $this->db->from($this->table_247around_nrn_details);
        if ($nrn_id != NULL) {
            $this->db->where('nrn_id', $nrn_id);
        }
        $query = $this->db->get();
        if ($query !== FALSE && $query->num_rows() > 0) {
            return $query->result_array();
        }
        return NULL;
    }
    
    function update_nrn_details($nrn_details,$nrn_id){
        if($nrn_id != ''){
            $this->db->where('nrn_id',$nrn_id);
            $this->db->update($this->table_247around_nrn_details,$nrn_details);
            if($this->db->affected_rows() > 0){
                return TRUE;
            }
            return FALSE;
        }
    }

}
