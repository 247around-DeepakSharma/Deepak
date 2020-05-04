<?php

class nrn_model extends CI_Model {

    public $table_247around_nrn_details = '247around_nrn_details';
    public $table_service_centre_charges = 'service_centre_charges';
    public $table_partner_appliance_details = 'partner_appliance_details';
    public $table_appliance_model_details = 'appliance_model_details';
    public $table_services = 'services';

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }

    /*
     * Insert new NRN/TR records 
     * return last insert id
     */

    function insert_nrn_details($data) {
        $this->db->insert($this->table_247around_nrn_details, $data);
        return $this->db->insert_id();
    }

    /*
     * Get all NRN/TR records 
     * return Array/NULL
     */

    function get_all_nrn_records() {
        $this->db->select('*');
        $this->db->from($this->table_247around_nrn_details);
        $query = $this->db->get();
        if ($query !== FALSE && $query->num_rows() > 0) {
            return $query->result_array();
        }
        return NULL;
    }

    /*
     * Get particular NRN/TR record by nrn_id 
     * return Array/NULL
     */

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

    /*
     * Upadte deatils of a particular NRN/TR record by nrn_id 
     * return TRUE/FALSE 
     */

    function update_nrn_details($nrn_details, $nrn_id) {
        if ($nrn_id != '') {
            $this->db->where('nrn_id', $nrn_id);
            $this->db->update($this->table_247around_nrn_details, $nrn_details);
            if ($this->db->affected_rows() > 0) {
                return TRUE;
            }
            return FALSE;
        }
    }

}
