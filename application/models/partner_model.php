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

    function get_order_id($booking_id){

        $union = "UNION

            SELECT Sub_Order_ID  as order_id FROM  `snapdeal_leads` WHERE  CRM_Remarks_SR_No LIKE  '%$booking_id%' ";


        $sql = "SELECT OrderID AS order_id from partner_leads where 247aroundBookingID LIKE '%$booking_id%'   " . $union;


        $query = $this->db->query($sql);
        return $query->result_array();

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

    //Return Partner ID from Booking Source
    //Default 'Other'
    function get_partner_id_from_booking_source_code($source) {
	$this->db->where(array("code" => $source));
	$query = $this->db->get('bookings_sources');
	$results = $query->result_array();

	return $results[0]['partner_id'];
    }

    function get_all_partner_source($flag = "") {
	$this->db->select("partner_id,source,code");
        $this->db->order_by('source','ASC');
        if($flag =="")
        $this->db->where('partner_id !=', 'NULL');
    	$query = $this->db->get("bookings_sources");
    	return $query->result_array();
    }

    function insert_data_in_batch($table_name, $rows){
        return $this->db->insert_batch($table_name, $rows);
    }

    function getpartner($partner_id=""){
        if($partner_id !=""){
            $this->db->where('id', $partner_id);
        }
        $this->db->select('id,public_name as name');
        $this->db->where('is_active','1');
        $query = $this->db->get('partners');
        return $query->result_array();
    }

     /**
     * @desc: check partner login and return pending booking
     * @param: Array(username, password)
     * @return : Array(Pending booking)
     */
    function partner_login($data){
       $this->db->select('partner_id');
       $this->db->where('user_name',$data['user_name']);
       $this->db->where('password',$data['password']);
       $this->db->where('active',1);
       $query = $this->db->get('partner_login');

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0]['partner_id'];

      } else {

        return false;
      }

    }

    /**
      * @desc: this is used to get pending booking for specific partner id
      * @param: end limit, start limit, partner id
      * @return: Pending booking
      */
     function getPending_booking($limit="", $start="", $partner_id ){
        $where = "";
        $where .= " AND partner_id = '" . $partner_id . "'";
        //do not show bookings for future as of now
        //$where .= " AND DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= 0";

	      $query = $this->db->query("Select services.services,
            users.name as customername, users.phone_number,
            booking_details.*

            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`

            WHERE
            `booking_details`.booking_id NOT LIKE 'Q-%' $where AND
            (booking_details.current_status='Pending' OR booking_details.current_status='Rescheduled')"
        );


        if($limit =="count"){
            $temp1 = $query->result_array();
           // echo $this->db->last_query();
            return count($temp1);

        } else {
            $temp = $query->result();
            usort($temp, array($this, 'date_compare_bookings'));

            return array_slice($temp, $start, $limit);
        }
     }


    /**
      * @desc: this is used to get pending queries for specific partner id
      * @param: end limit, start limit, partner id
      * @return: Pending Queries
      */
     function getPending_queries($limit="", $start="", $partner_id ){
        $where = "";
        $where .= " AND partner_id = '" . $partner_id . "'";
	$where .= " AND (DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= 0 OR
			    booking_details.booking_date='')";

	$query = $this->db->query("Select services.services,
            users.name as customername, users.phone_number,
            booking_details.*

            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`

            WHERE
            `booking_details`.booking_id LIKE 'Q-%' $where AND
             booking_details.current_status='FollowUp' "
        );


        if($limit =="count"){
            $temp1 = $query->result_array();
           // echo $this->db->last_query();
            return count($temp1);

        } else {
            $temp = $query->result();
            usort($temp, array($this, 'date_compare_bookings'));

            return array_slice($temp, $start, $limit);
        }

     }

     /**
      * @desc: This is used to get close booking of custom partner
      * @param: End limit
      * @param: Start limit
      * @param: Partner Id
      * @param: Booking Status(Cancelled or Completed)
      * @return: Array()
      */
     function getclosed_booking($limit="", $start="", $partner_id, $status){
        if($limit!="count"){
            $this->db->limit($limit, $start);
        }

        $this->db->select('booking_details.booking_id, users.name as customername, booking_details.booking_primary_contact_no, services.services, booking_details.booking_date, booking_details.closing_remarks, booking_details.booking_timeslot, booking_details.city, booking_details.cancellation_reason');
        $this->db->from('booking_details');
        $this->db->join('services','services.id = booking_details.service_id');
        $this->db->join('users','users.user_id = booking_details.user_id');
        $this->db->where('booking_details.current_status', $status);
        $this->db->where('partner_id',$partner_id);
        $query = $this->db->get();

        $result = $query->result_array();

        foreach ($result as $key => $value) {
           $order_id = $this->get_order_id($value['booking_id']);
           if(!empty($order_id[0]['order_id'])){

               $result[$key]['order_id'] = $order_id[0]['order_id'];

           } else {
               $result[$key]['order_id'] = "";
           }
        }

        if($limit == "count"){

            return count($result);
        }
        return $result;


     }

     function date_compare_bookings($a, $b) {
        $t1 = strtotime($a->booking_date);
        $t2 = strtotime($b->booking_date);

        return $t2 - $t1;
    }

}
