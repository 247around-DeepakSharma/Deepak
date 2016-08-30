<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Database_testing_model extends CI_Model {

    function __construct() {
	parent::__Construct();

	$this->db = $this->load->database('default', TRUE, TRUE);
    }

    /**
     * check booking id, partner_id, service id, appliance id is  zero
     * @return boolean
     */
    function check_unit_details() {
	$this->db->select('booking_id, partner_id, service_id, appliance_id');
	$this->db->or_where_in('booking_id', '0');
	$this->db->or_where_in('partner_id', '0');
	$this->db->or_where_in('appliance_id', '0');
	$this->db->where('create_date >=', '2016-07-01 00:00:00');
	$query = $this->db->get('booking_unit_details');
	if ($query->num_rows > 0) {
	    return $query->result_array();
	} else {
	    return false;
	}
    }

    /**
     * check price tag is empty or null when booking status is Pending, Completed, Cancelled, Rescgeduled
     * @return boolean
     */
    function check_price_tags() {
	$this->db->select('booking_unit_details.booking_id, price_tags');
	$this->db->from('booking_unit_details');
	$this->db->or_where_in('price_tags', '');
	$this->db->or_where_in('price_tags', NULL);
	$this->db->join('booking_details', 'booking_details.booking_id = booking_unit_details.booking_id');
	$this->db->where_in('booking_details.current_status', array('Pending', 'Completed', 'Cancelled', 'Rescheduled'));
	$this->db->where('booking_details.create_date >=', '2016-07-01 00:00:00');
	$query = $this->db->get();
	if ($query->num_rows > 0) {
	    return $query->result_array();
	} else {
	    return false;
	}
    }

    /**
     * Check price is not empty or null for Pending, Completed, Cancelled or Rescheduled booking
     * @return boolean
     */
    function check_tax_rate() {
	$this->db->select('booking_unit_details.booking_id, tax_rate');
	$this->db->from('booking_unit_details');
	$this->db->where('tax_rate <=', '0');
	$this->db->join('booking_details', 'booking_details.booking_id = booking_unit_details.booking_id');
	$this->db->where_in('booking_details.current_status', array('Pending', 'Completed', 'Cancelled', 'Rescheduled'));
	$this->db->where('booking_details.create_date >=', '2016-07-01 00:00:00');
	$query = $this->db->get();
	if ($query->num_rows > 0) {
	    return $query->result_array();
	} else {
	    return false;
	}
    }

    /**
     *  check booking status is empty or null when booking current status is ccompleted or cancelled.
     * @return boolean
     */
    function check_booking_unit_details_status() {
	$this->db->select('booking_details.booking_id,booking_unit_details.booking_status');
	$this->db->from('booking_unit_details');
	$this->db->or_where('booking_status', '');
	$this->db->or_where('booking_status', NULL);
	$this->db->join('booking_details', 'booking_details.booking_id = booking_unit_details.booking_id');
	$this->db->where_in('booking_details.current_status', array('Completed', 'Cancelled'));
	$this->db->where('booking_details.create_date >=', '2016-07-01 00:00:00');
	$query = $this->db->get();
	if ($query->num_rows > 0) {
	    return $query->result_array();
	} else {
	    return false;
	}
    }

    /**
     * check booking id, partner_id, service id, appliance id is  zero
     * @return boolean
     */
    function check_booking_details() {
	$this->db->select('booking_id, partner_id, service_id, user_id,source, type, booking_pincode, city, booking_pincode');
	$this->db->or_where_in('booking_id', '0');
	$this->db->or_where_in('partner_id', '0');
	$this->db->or_where_in('service_id', '0');
	$this->db->or_where_in('user_id', '0');
	$this->db->or_where_in('source', '');
	$this->db->or_where_in('source', NULL);
	$this->db->or_where_in('type', NULL);
	$this->db->or_where_in('booking_pincode', NULL);
	$this->db->or_where_in('city', NULL);
	$this->db->or_where_in('booking_primary_contact_no', '');
	$this->db->or_where_in('booking_primary_contact_no', '0');
	$this->db->where('booking_details.create_date >=', '2016-07-01 00:00:00');
	$query = $this->db->get('booking_details');
	if ($query->num_rows > 0) {
	    return $query->result_array();
	} else {
	    return false;
	}
    }
    /**
     * Get booking id who is exist in booking details and not exist in the booking unit details
     * @return boolean
     */
    function check_booking_exist_in_unit_details() {
	$sql = "SELECT  `booking_id`  FROM  `booking_unit_details` WHERE  `booking_id` NOT IN "
	    . " ( SELECT booking_id FROM booking_details WHERE create_date >=  '2016-07-01 00:00:00')";
	$query = $this->db->query($sql);
	if ($query->num_rows > 0) {
	    return $query->result_array();
	} else {
	    return false;
	}
    }

    function check_booking_exist_in_service_center() {
	$sql = "SELECT  `booking_details`.`booking_id` "
	    . " FROM (`booking_details`) WHERE booking_details.`assigned_vendor_id` IS NOT NULL  "
	    . " AND  `booking_details`.`create_date` >=  '2016-07-01 00:00:00'";

	$query = $this->db->query($sql);
	$data = $query->result_array();
	$data_result = array();

	foreach ($data as $value) {
	    $this->db->select('count(booking_id) as unit_count');
	    $this->db->where('booking_id', $value['booking_id']);
	    $query1 = $this->db->get('booking_unit_details');
	    $result1 = $query1->result_array();

	    $this->db->select('count(booking_id) as service_count');
	    $this->db->where('booking_id', $value['booking_id']);
	    $query2 = $this->db->get('service_center_booking_action');
	    $result2 = $query2->result_array();

	    if (count($result1[0]['unit_count']) != $result2[0]['service_count']) {
		array_push($data_result, array('booking_id' => $value['booking_id']));
	    }
	}
	
	if (!empty($data_result)) {
	    return $data_result;
	} else {
	    return false;
	}
    }

}
