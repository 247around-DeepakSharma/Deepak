<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sf_authorization_model
 * 
 */
class SF_authorization_model extends CI_Model {
    //put your code here

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }

    /*
     * Get SF deatils and SF's RM details and in which services SF deals
     */

    function get_sf_details($sf_id) {
        $this->db->select('id,name,company_name,address,pincode,state,district,landmark,appliances');
        $this->db->from('service_centres');
        $this->db->where('active', '1');
        $this->db->where('is_sf', '1');
        $query = $this->db->get();
        return $query->row_array();
    }

    /*
     * Update certificate file and set authorozation status authorization certificate deatils
     *  by SF id     * 
     */

    function update_authorization_certificate_details($sf_id, $file_name) {
        $this->db->set('has_authorization_certificate', 1);
        $this->db->set('auth_certificate_file_name', $file_name);
        $this->db->where('id', $sf_id);
        $this->db->where('active', '1');
        $this->db->where('is_sf', '1');
        $query = $this->db->update('service_centres');
        return $query->affected_rows();
    }

}
