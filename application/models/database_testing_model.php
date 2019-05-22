<?php

define("DATE_FROM", "2019-02-01");
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Database_testing_model extends CI_Model {

    function __construct() {
	parent::__Construct();
    }

    /**
     * check booking id, partner_id, service id, appliance id is  zero
     * @return boolean
     */
    function check_unit_details() {
	log_message('info', __METHOD__);

	$sql = "SELECT id, `booking_id`, `partner_id`, `service_id`, `appliance_id` "
                . " FROM (`booking_unit_details`) "
                . " WHERE (`booking_id` = '0' OR `partner_id` = '0' "
                . " OR `appliance_id` = '0') "
                . " AND `create_date` >= '".DATE_FROM."'";

	$query = $this->db->query($sql);
	//log_message('info', $this->db->last_query());
	//echo $this->db->last_query() . PHP_EOL;


	if ($query->num_rows > 0) {
	    return $query->result_array();
	} else {
	    return array();
	}
    }

    /**
     * check price tag is empty or null when booking status is Pending, Completed, Cancelled, Rescgeduled
     * @return boolean
     */
    function check_price_tags() {
	$this->db->select('booking_unit_details.id, booking_unit_details.booking_id, price_tags, booking_unit_details.create_date');
	$this->db->from('booking_unit_details');
	$where = array('', NULL);
	$this->db->where_in('price_tags', $where);
//	$this->db->or_where('price_tags', NULL);
	$this->db->join('booking_details', 'booking_details.booking_id = booking_unit_details.booking_id');
	$this->db->not_like('booking_details.booking_id', 'Q-');
	$this->db->where_in('booking_details.current_status', array('Pending', 'Completed', 'Rescheduled'));
	$this->db->where('booking_details.create_date >=', DATE_FROM);
	$query = $this->db->get();

	//log_message('info', $this->db->last_query());
	//echo $this->db->last_query() . PHP_EOL;

	if ($query->num_rows > 0) {
	   // echo "check_price_tags failed..." . PHP_EOL;
	    return $query->result_array();
	} else {
	    return array();
	}
    }

    /**
     * Check Tax  is not less than or equal to 0 for Pending, Completed, Cancelled or Rescheduled booking
     * @return boolean
     */
    function check_tax_rate() {
	$this->db->select('booking_unit_details.id, booking_unit_details.booking_id, tax_rate, booking_unit_details.create_date');
	$this->db->from('booking_unit_details');
	$this->db->where('tax_rate <=', '0');
	$this->db->join('booking_details', 'booking_details.booking_id = booking_unit_details.booking_id');
	$this->db->where_in('booking_details.current_status', array('Pending', 'Completed', 'Rescheduled'));
	$this->db->where('booking_details.create_date >=', DATE_FROM);
	$this->db->where_in('booking_unit_details.booking_status', array('Completed', ''));
	$query = $this->db->get();

	//log_message('info', $this->db->last_query());
	//echo $this->db->last_query() . PHP_EOL;

	if ($query->num_rows > 0) {
	    return $query->result_array();
	} else {
	    return array();
	}
    }

    /**
     *  check booking status is empty or null when booking current status is ccompleted or cancelled.
     * @return boolean
     */
    function check_booking_unit_details_status() {
	$this->db->select('booking_unit_details.id, booking_details.booking_id,booking_unit_details.booking_status, booking_unit_details.create_date');
	$this->db->from('booking_unit_details');
	$where = array('', NULL);
	$this->db->where_in('booking_status', $where);
	$this->db->join('booking_details', 'booking_details.booking_id = booking_unit_details.booking_id');
	$this->db->where_in('booking_details.current_status', array('Completed'));
	$this->db->where('booking_details.create_date >=', DATE_FROM);
	$query = $this->db->get();
	//log_message('info', $this->db->last_query());
	//echo $this->db->last_query() . PHP_EOL;
	if ($query->num_rows > 0) {
	    return $query->result_array();
	} else {
	    return array();
	}
    }

    /**
     * check booking id, partner_id, service id, appliance id is  zero
     * @return boolean
     */
//    function check_booking_details() {
//	$sql = "SELECT `booking_id`, `partner_id`, `service_id`, `user_id`, `source`, `type`, `booking_pincode`, `city`, `booking_pincode`, `booking_details`.`create_date` FROM (`booking_details`) WHERE (`booking_id` = '0' OR `partner_id` = '0' OR `service_id` = '0' OR `user_id` = '0' OR `source` = '' OR `source` IS NULL OR `type` IS NULL OR `current_status` IS NULL OR `current_status` = '0' OR `internal_status` = '0' OR `booking_primary_contact_no` = '' OR `booking_primary_contact_no` = '0') AND `booking_details`.`create_date` >= '2016-09-01 00:00:00'";
//	$query = $this->db->query($sql);
//	log_message('info', $this->db->last_query());
//	echo $this->db->last_query() . PHP_EOL;
//	if ($query->num_rows > 0) {
//	    return $query->result_array();
//	} else {
//	    return false;
//	}
//    }

//    /**
//     * Get booking id who is exist in booking details and not exist in the booking unit details
//     * @return boolean
//     */
//    function check_booking_exist_in_unit_details() {
//	$sql = "SELECT `booking_id`, create_date FROM `booking_unit_details` WHERE create_date >= '2016-08-01 00:00:00' AND `booking_id` NOT IN ( SELECT booking_id FROM booking_details WHERE create_date >= '2016-08-01 00:00:00')";
//	$query = $this->db->query($sql);
//	log_message('info', $this->db->last_query());
//	echo $this->db->last_query() . PHP_EOL;
//	if ($query->num_rows > 0) {
//	    echo __FUNCTION__ . " failed \n\n";
//	    return $query->result_array();
//	} else {
//	    return false;
//	}
//    }
    /**
     * @desc: Number of line item in the unit details should be same in service  center Table
     * @return boolean|array
     */
    function check_booking_exist_in_service_center() {
	$sql = "SELECT  `booking_details`.`booking_id` "
	    . " FROM (`booking_details`) WHERE booking_details.`assigned_vendor_id` IS NOT NULL  "
	    . " AND  `booking_details`.`create_date` >=  '2019-02-01'";

	$query = $this->db->query($sql);
	log_message('info', $this->db->last_query());
	//echo $this->db->last_query() . PHP_EOL;
	$data = $query->result_array();
	$data_result = array();
        echo count($data).PHP_EOL;
	foreach ($data as $key => $value) {
            echo "..5.".$key. PHP_EOL;
	    $this->db->select('count(booking_id) as unit_count');
	    $this->db->where('booking_id', $value['booking_id']);
	    $query1 = $this->db->get('booking_unit_details');
	    $result1 = $query1->result_array();

	    $this->db->select('count(booking_id) as service_count');
	    $this->db->where('booking_id', $value['booking_id']);
	    $query2 = $this->db->get('service_center_booking_action');
	    $result2 = $query2->result_array();

	    if ($result1[0]['unit_count'] != $result2[0]['service_count']) {
		array_push($data_result, array('booking_id' => $value['booking_id']));
	    }
	}
        echo count($data_result)."..".PHP_EOL;
	if (!empty($data_result)) {
	  //  echo __FUNCTION__ . " failed \n\n";
	    return $data_result;
	} else {
	    return array();
	}
    }

    /**
     * @desc: This method checks booking id is zero,
     * unit details id is zero or null,
     * service center id is zero,
     * current status and internal status is zero
     * @return boolean
     */
    function check_service_center_action() {

	$sql = "SELECT `booking_id`, `unit_details_id`, `service_center_id`,"
                . "  `current_status`, `internal_status`, `create_date`"
                . "  FROM (`service_center_booking_action`) "
                . " WHERE (`booking_id` = '0' OR `unit_details_id` IS NULL OR `unit_details_id` = '0' OR `service_center_id` = '0' OR `current_status` = '0' OR `internal_status` = '0') "
                . " AND `create_date` >= '".DATE_FROM."'";
	$query = $this->db->query($sql);
	//log_message('info', $this->db->last_query());
	//echo $this->db->last_query() . PHP_EOL;

	if ($query->num_rows > 0) {
	    //echo __FUNCTION__ . " failed \n\n";
	    return $query->result_array();
	} else {
	   return array();
	}
    }

    /**
     * @desc: this method check if booking is pending or rescheduled in booking details,
     * it must be pending or Inprocess in service center action table
     * @return boolean
     */
    function check_pending_booking_in_action_table() {
	$this->db->select('booking_id, create_date');
	$this->db->where_in('booking_details.current_status', array('Pending', 'Rescheduled'));
	$this->db->where('booking_details.create_date >=', "2019-02-01");
	$query = $this->db->get('booking_details');
	//log_message('info', $this->db->last_query());
        
	//echo $this->db->last_query() . PHP_EOL;
	if ($query->num_rows > 0) {
	    $data = $query->result_array();
            echo count($data).PHP_EOL;
	    $data_array = array();
	    foreach ($data as $key => $value) {
                echo "..7.".$key. PHP_EOL;
		$this->db->select('booking_id');
		$this->db->where('booking_id', $value['booking_id']);
		$this->db->where_not_in('current_status', array('Pending', 'Inprocess'));
		$query1 = $this->db->get('service_center_booking_action');
		if ($query1->num_rows > 0) {
		    //echo __FUNCTION__ . " failed \n\n";
		    array_push($data_array, array('booking_id' => $value['booking_id']));
		}
	    }
	    return $data_array;
	} else {
	    return array();
	}
    }

    /**
     * @desc: This is used to check assigned booking.
     * It must be exist in service center action table
     */
    function check_booking_exist_in_service_center_action_table() {
	$sql = "SELECT `booking_id`, `assigned_vendor_id`, current_status, internal_status
				FROM `booking_details` as BD WHERE BD.`current_status` in ('Pending', 'Rescheduled')
				AND `assigned_vendor_id` is not null
				AND BD.booking_id NOT IN (select booking_id from service_center_booking_action) AND BD.create_date >= '".DATE_FROM."'";
	$query = $this->db->query($sql);
	//log_message('info', $this->db->last_query());
	if ($query->num_rows > 0) {
	    return $query->result_array();
	} else {
	   return array();
	}
    }

    /**
     * @desc: This is used to check booking status. It should not empty for cloased booking
     */
    function check_in_closed_booking_booking_status_notempty() {
	$sql = "SELECT ud.id, ud.booking_id FROM `booking_details` as bd, booking_unit_details as ud WHERE bd.`closed_date` >= '".DATE_FROM."' AND bd.`current_status`='Completed' AND bd.`booking_id`=ud.`booking_id` AND ud.`booking_status`='' ";

	$query = $this->db->query($sql);

	log_message('info', $this->db->last_query());
	//echo $this->db->last_query() . PHP_EOL;
	if ($query->num_rows > 0) {
	    return $query->result_array();
	} else {
	    return array();
	}
    }

    /**
     * @desc: This method checks product_or_services for completed booking. It should not be empty
     */
    function check_product_or_services() {
	$sql = "SELECT ud.id, ud.booking_id  FROM `booking_details` as bd, booking_unit_details as ud WHERE bd.`closed_date` >= '".DATE_FROM."' AND bd.`current_status`='Completed' AND bd.`booking_id`=ud.`booking_id` AND ud.`product_or_services`=''; ";
	$query = $this->db->query($sql);

	//log_message('info', $this->db->last_query());
	//echo $this->db->last_query() . PHP_EOL;
	if ($query->num_rows > 0) {
	    return $query->result_array();
	} else {
	   return array();
	}
    }

    /**
     * @desc: This is used to check Prices. Prices never be neagtive
     */
    function check_prices_should_not_be_negative() {
        $sql = " SELECT booking_unit_details.id,booking_unit_details.booking_id  FROM booking_unit_details where (customer_net_payable <0 OR customer_total < 0 OR partner_net_payable <0 OR around_net_payable <0  OR customer_paid_basic_charges< 0 OR partner_paid_basic_charges<0 OR around_paid_basic_charges<0 OR around_comm_basic_charges<0 OR around_st_or_vat_basic_charges<0 OR vendor_basic_charges <0 OR vendor_to_around <0 OR around_to_vendor<0 OR vendor_st_or_vat_basic_charges<0 OR customer_paid_extra_charges< 0 OR around_comm_extra_charges<0 OR around_st_extra_charges<0 OR vendor_extra_charges< 0 OR vendor_st_extra_charges<0 OR customer_paid_parts<0 OR around_comm_parts<0 OR around_st_parts<0 OR vendor_parts<0 OR vendor_st_parts<0) AND create_date >= '".DATE_FROM."'";
        $query = $this->db->query($sql);
        //log_message('info', $this->db->last_query());
        //echo $this->db->last_query() . PHP_EOL;
        if ($query->num_rows > 0) {
            return $query->result_array();
        } else {
            return array();
        }
    }

    /**
     * @desc: This is used to check Assigned Booking. mail_to_vendor should not be 0.
     */
    function check_assigned_vendor_email_flag() {
	$sql = " SELECT booking_id  FROM booking_details "
                . " WHERE assigned_vendor_id IS NOT NULL "
                . " AND mail_to_vendor =0 "
                . " AND create_date >=  '".DATE_FROM."'"
                . " AND current_status IN  ('Pending', 'Rescheduled' )";
	$query = $this->db->query($sql);
	//log_message('info', $this->db->last_query());
	//echo $this->db->last_query() . PHP_EOL;
	if ($query->num_rows > 0) {
	    return $query->result_array();
	} else {
	    return array();
	}
    }
    /**
     * @desc: This is used to check partner paid basic charge, Partner Paid basic charge should be satify formula
     * @return boolean
     */
    function check_partner_paid_basic_charge(){
        $sql = " SELECT id, `booking_id` ,  `partner_net_payable` ,  `partner_paid_basic_charges` ,  `tax_rate` 
            FROM  `booking_unit_details` WHERE  `partner_net_payable` >0 
            AND  `partner_paid_basic_charges` != ( partner_net_payable + (  `partner_net_payable` *  `tax_rate` ) /100 )  
            AND create_date >=  '".DATE_FROM."'
            AND booking_status =  'Completed'";
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
	    return $query->result_array();
	} else {
	    return array();
	}
        
    }
    
    /**
     * @desc: return Array, Ac service which was closed at 0 prices.
     * @return boolean
     */
    function check_customer_paid_basic_charge(){
        $sql = "SELECT id, booking_id FROM  `booking_unit_details` "
                . " WHERE  `booking_status` LIKE  'Completed' "
                . " AND  `customer_net_payable` >0 "
                . " AND  `customer_paid_basic_charges` =0 "
                . " AND create_date >=  '".DATE_FROM."'";
        
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
	    return $query->result_array();
	} else {
	   return array();
	}
    }
    
    /**
     * @DESC: This method checks stand is add in the unit details for particular Brand
     */
    function check_stand(){
        $sql = "SELECT b1.booking_id  FROM booking_details AS b1, booking_unit_details AS u1 
            WHERE u1.appliance_brand
            IN (
             'Sony',  'Panasonic',  'LG',  'Samsung'
            )
            AND b1.current_status =  'Completed'
            AND b1.closed_date >=  '".DATE_FROM."'
            AND b1.service_id =  '46'
            AND b1.booking_id = u1.booking_id
            AND b1.current_status != 'Cancelled'
            AND b1.partner_id
            IN (
             '1',  '3'
            )
            AND NOT 
            EXISTS (

                SELECT * 
                FROM booking_unit_details AS u2
                WHERE b1.booking_id = u2.booking_id
                AND u2.price_tags =  'Wall Mount Stand'
            )
            
            ORDER BY  `b1`.`create_date` DESC ";
        
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
	    return $query->result_array();
	} else {
	    return array();
	}
    }
    
    function check_duplicate_entry(){
        $sql = " SELECT DISTINCT (
            b1.`booking_id`
            ), b1.`price_tags` 
            FROM  `booking_unit_details` AS b1,  `booking_unit_details` AS b2
            WHERE b1.`booking_id` = b2.`booking_id` 
            AND b1.`price_tags` = b2.`price_tags` 
            AND b1.id != b2.id
            AND b1.create_date >=  '".DATE_FROM."'
           
            AND (b1.booking_status !=  'Cancelled' OR b2.booking_status !=  'Cancelled' )
            
            AND (b2.booking_status
            IN (
            'Completed',  ''
            )  OR b1.booking_status
            IN (
            'Completed',  ''
            ))";
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
	    return $query->result_array();
	} else {
	    return array();
	}
    }
    /**
     * @desc: Is booking exist in unit details table who has inserted in booking details
     */
    function check_booking_exist_in_unit_details(){
        $sql = "SELECT b1.booking_id, b1.create_date FROM booking_details AS b1
                WHERE NOT 
                EXISTS (

                SELECT booking_id
                FROM booking_unit_details
                WHERE b1.booking_id = booking_unit_details.booking_id
                )
                AND create_date >=  '2019-02-01'
                AND b1.current_status != 'Cancelled'";
        
         $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
	    return $query->result_array();
	} else {
	    return array();
	}
    }
    /**
     * @desc: Check customer total is zero
     */
    
    function check_customer_total(){
        $sql = "SELECT booking_details.booking_id, booking_details.create_date
                FROM booking_unit_details, booking_details
                WHERE customer_total =0
                AND booking_details.create_date >=  '2019-02-01'
                AND booking_details.booking_id = booking_unit_details.booking_id
                AND booking_details.current_status
                IN (
                'Pending',  'Rescheduled',  'Completed'
                )
                AND booking_status
                IN (
                'Completed',  '')";
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
	    return $query->result_array();
	} else {
	    return array();
	}
    }
    
    /**
     * @desc: Check pending bookings which are without job card
     */
    
    function count_pending_bookings_without_job_card(){
        $sql = "SELECT booking_id
                FROM `booking_details` 
                WHERE `current_status` 
                IN (
                'Pending', 'Rescheduled'
                )
                AND `assigned_vendor_id` IS NOT NULL 
                AND `booking_jobcard_filename` IS NULL";
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
	    return $query->result_array();
	} else {
	    return array();
	}
    }
    
    function get_booking_id_without_pdf_jobcards($date) {
        $sql = "Select booking_id from booking_details WHERE 
                   `booking_jobcard_filename` LIKE '%.xlsx%' AND `create_date` >= 
                   '$date' ";
        $data = $this->db->query($sql)->result_array();
        return $data;
    }

}
