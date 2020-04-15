<?php

class Web_footer_model extends CI_Model {

    /**
     * @desc Load database
     */
    function __construct() {
	parent::__Construct();

	$this->db = $this->load->database('default', TRUE, TRUE);
    }

    function get_brands() {
	$this->db->select('brand_name');
	$this->db->distinct();
	$this->db->order_by('brand_name');
	$query = $this->db->get('website_brands');

	return $query->result();
    }

    function get_appliance() {
	$this->db->select('services');
	$query = $this->db->get('services');
	return $query->result();
    }
    
    function get_users_count(){
        $sql = 'SELECT ROUND(COUNT(user_id) , -3) AS total_user FROM users';
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    function get_partners_count(){
        $sql = 'SELECT SUM((SELECT COUNT( DISTINCT company_name) FROM partners) +
               (SELECT COUNT(DISTINCT company_name) FROM service_centres)) AS sum_count';
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    function get_city_count(){
        $sql = 'SELECT ROUND(COUNT(DISTINCT vendor_pincode_mapping.City)) as total_city FROM service_centres
                JOIN vendor_pincode_mapping ON vendor_pincode_mapping.Vendor_ID = service_centres.id 
                WHERE service_centres.active=1';
        $query = $this->db->query($sql);
        return $query->result_array();
    }

}
