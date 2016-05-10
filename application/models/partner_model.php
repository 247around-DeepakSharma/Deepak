<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Partner_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
    	parent::__Construct();

    	$this->db_location = $this->load->database('default1', TRUE, TRUE);
    	$this->db = $this->load->database('default', TRUE, TRUE);
    }

    function log_partner_activity($activity) {
    	$this->db->insert("log_partner_table", $activity);
    }

    function validate_partner($auth_token) {
	//TODO: Deactivate partner account if auth token mismatch happens 3 or more times in a day

    	$this->db->where(array("auth_token" => $auth_token, "is_active" => '1'));
    	$query = $this->db->get('partners');

    	if (count($query->result_array()) > 0) {
	    //Return partner details in case of success
    		return $query->result_array()[0];
    	} else {
    		return FALSE;
    	}
    }

    function insert_partner_lead($details) {
    	$this->db->insert('partner_leads', $details);

    	return $this->db->insert_id();
    }

    function get_partner_lead_by_id($id) {
    	$this->db->where(array("id" => $id));
	//$query = $this->db->query("SELECT * FROM partner_leads WHERE id='$id'");
    	$query = $this->db->get("partner_leads");
    	$results = $query->result_array();

    	if (count($results) > 0) {
    		return $results[0];
    	} else {
    		return NULL;
    	}
    }

    //Find order id for a partner
    function get_partner_lead_by_order_id($partner_id, $order_id) {
    	$this->db->where(array("PartnerID" => $partner_id, "OrderID" => $order_id));
    	$query = $this->db->get("partner_leads");
    	$results = $query->result_array();

    	if (count($results) > 0) {
    		return $results[0];
    	} else {
    		return NULL;
    	}
    }

    //Find OrderID for 247aroundBooking ID
    function get_order_id_by_booking_id($booking_id) {
    	$this->db->like(array("247aroundBookingID" => $booking_id));
    	$query = $this->db->get("partner_leads");
    	$results = $query->result_array();

    	if (count($results) > 0) {
    		return $results[0];
    	} else {
    		return NULL;
    	}
    }

    function update_partner_lead($array_where, $array_data) {
    	/*
    	 * Standard method of Update wasn't working because of the LIKE clause
    	 * hence using this method.
    	 */

    	$booking_id = $array_where['247aroundBookingID'];
    	$where = "247aroundBookingID LIKE '%$booking_id%'";
    	$sql = $this->db->update_string('partner_leads', $array_data, $where);

    	if ($this->db->query($sql) === FALSE) {
    		log_message('error', __METHOD__ . "=> Update command failed" . $this->db->last_query());
    	} else {
    		$result = (bool) ($this->db->affected_rows() > 0);
    		log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);
    	}
    }

    //Return true/false depending on lead with order_id and some case for partner id exists or not
    function check_partner_lead_exists_by_order_id($order_id, $partner_id ="") {
        if($partner_id !="")
            $this->db->where("PartnerID", $partner_id);

    	$this->db->where("OrderID" , $order_id);
    	$query = $this->db->get('partner_leads');

    	if (count($query->result_array()) > 0)
    		return TRUE;
    	else
    		return FALSE;
    }

    //return booking source code
    function get_source_code_for_partner($partner_id) {
    	$this->db->where(array("partner_id" => $partner_id));
    	$query = $this->db->get('bookings_sources');
    	$results = $query->result_array();

    	if (count($results) > 0) {
    		return $results[0]['code'];
    	} else {
    		return "SO";
    	}
    }

    function get_all_partner_source(){
    	$this->db->select("partner_id,source,code");
        $this->db->where('partner_id !=', 'NULL');
    	$query = $this->db->get("bookings_sources");
    	return $query->result_array();
    }

    function insert_data_in_batch($table_name, $rows){
        return $this->db->insert_batch($table_name, $rows);
    }

}
