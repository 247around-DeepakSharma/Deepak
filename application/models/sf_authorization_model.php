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

    function get_sf_details($finacial_year) {
        $select = 'id,name,company_name,address,pincode,state,district,landmark,appliances';
        $where = 'active = 1 AND is_sf = 1 AND ((auth_certificate_validate_year != "' . $finacial_year . '" OR auth_certificate_validate_year IS NULL)';
        $where .= ' OR (auth_certificate_validate_year = "' . $finacial_year . '" AND has_authorization_certificate = 0))';
        $query = $this->reusable_model->get_search_query('service_centres', $select, $where, NULL, NULL, NULL, NULL, NULL);
        return $query->result_array();
    }

    /*
     * Update certificate file and set authorozation status authorization certificate deatils
     *  by SF id     * 
     */

    function update_authorization_certificate_details($sf_id, $file_name, $finacial_year) {
        $data = array(
            'has_authorization_certificate' => 1,
            'auth_certificate_file_name' => $file_name,
            'auth_certificate_validate_year' => $finacial_year
        );
        $where = "id = $sf_id AND active = 1 AND is_sf = 1";
        return $this->reusable_model->update_table('service_centres', $data, $where);
    }

    /*
     * Get all active SF deatils for listinf SF
     */

    function get_all_active_sf_details() {
        $select = 'id,name,company_name,auth_certificate_validate_year,auth_certificate_file_name,has_authorization_certificate';
        $where = 'active = 1 AND is_sf = 1';
        $query = $this->reusable_model->get_search_query('service_centres', $select, $where, NULL, NULL, NULL, NULL, NULL);
        return $query->result_array();
    }

    /*
     * Get auth certificate images
     */

    function get_auth_certificate_setting() {
        $select = '*';
        $query = $this->reusable_model->get_search_query('sf_auth_certificate_setting', $select, '', NULL, NULL, NULL, NULL, NULL);
        return $query->result_array();
    }

}
