<?php

class Migration_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
	parent::__Construct();
	$this->db = $this->load->database('default', TRUE, TRUE);
    }

    /**
     *  @desc: This is method returns booking details and unit details, appliance description for june month
     * @return type
     */
    function c_get_all_booking_id() {


	$sql = "SELECT booking_details.booking_id, booking_details.partner_id, booking_details.service_id, 
                booking_details.appliance_id, booking_unit_details.appliance_capacity, 
                booking_unit_details.appliance_brand, booking_unit_details.appliance_category, 
                booking_unit_details.price_tags, booking_unit_details.appliance_tag, 
                booking_unit_details.purchase_month, booking_unit_details.purchase_year, 
                booking_unit_details.model_number, booking_details.service_charge, 
                booking_details.additional_service_charge, booking_details.parts_cost, 
                booking_details.internal_status, appliance_details.description as appliance_description
            from booking_details, booking_unit_details, appliance_details
            where `closed_date` >= '2016-06-01 00:00:00' AND `closed_date` < '2016-07-01 00:00:00' AND 
            (`current_status` = '%Completed%' OR `current_status` = '%Cancelled%') AND booking_unit_details.booking_id = booking_details.booking_id
            AND booking_details.appliance_id =  appliance_details.id

            ";

	$query = $this->db->query($sql);
	$result = $query->result_array();
	return $result;
    }
    /**
     *  @desc: this method returns  booking id, charges and internal status for completed booking  
     * @return type
     */
    function c_getbookingid() {
	$sql = "SELECT booking_details.booking_id, booking_details.service_charge, 
            booking_details.additional_service_charge, 
            booking_details.parts_cost, 
            booking_details.internal_status
            from booking_details
            where `closed_date` >= '2016-06-01 00:00:00' AND `closed_date` < '2016-07-01 00:00:00' 
            AND (`current_status` = '%Completed%' OR `current_status` = '%Cancelled%' )
            ";

	$query = $this->db->query($sql);
	$result = $query->result_array();
	return $result;
    }
    /**
     * @desc: update unit details
     * @param type $id
     * @param type $data
     * @return boolean
     */
    function update_unit_details_by_id($id, $data) {
	$this->db->where('id', $id);
	$this->db->update('booking_unit_details', $data);
	return true;
    }
    /**
     * @desc: Get unit details from booking id or unit details id
     * @param type $booking_id
     * @param type $unit_details_id
     * @return type
     */
    function get_unit_details($booking_id = "", $unit_details_id = "") {
	$this->db->select('*');
	if ($booking_id != "") {
	    $this->db->where('booking_id', $booking_id);
	}

	if ($unit_details_id != "") {
	    $this->db->where('id', $unit_details_id);
	}


	$query = $this->db->get('booking_unit_details');

	return $query->result_array();
    }
    /**
     * get charges from service center charges table on the basis of partner id, service i, category
     * @param type $service_id
     * @param type $category
     * @param type $capacity
     * @param type $partner_id
     * @param type $price_tags
     * @return type
     */
    function getPrices($service_id, $category, $capacity, $partner_id, $price_tags) {

	$this->db->distinct();
	$this->db->select('id,service_category,customer_total, partner_net_payable, customer_net_payable');
	$this->db->where('partner_id', $partner_id);
	$this->db->where('service_id', $service_id);
	$this->db->where('category', $category);
	if (!empty($capacity)) {
	    $this->db->where('capacity', $capacity);
	}
	$this->db->where('service_category', $price_tags);
	//$this->db->where('active', 1);

	$query = $this->db->get('service_centre_charges');

	return $query->result_array();
    }
    /**
     * @desc: Returns commpleted booking unit details and pincode whose closed between June 
     * @return type
     */
    function c_get_all_booking_unit() {

	$sql = " SELECT booking_unit_details.*, `booking_details`.booking_pincode "
                . "FROM `booking_unit_details`, booking_details "
	    . "WHERE `booking_unit_details`.booking_id = `booking_details`.`booking_id` AND "
	    . "`booking_unit_details`.`booking_id` in "
	    . "(SELECT booking_id FROM `booking_details` WHERE `closed_date` >= '2016-06-01 00:00:00' AND "
	    . "`closed_date` < '2016-07-01 00:00:00' AND (`current_status` = '%Completed%' OR `current_status` = '%Cancelled%'))";
	$query = $this->db->query($sql);
	return $query->result_array();
    }

    function return_source($booking_id) {
	$this->db->select('source');
	$this->db->where('booking_id', $booking_id);
	$query = $this->db->get('booking_details');
	return $query->result_array();
    }

    function getpricesdetails_with_tax($service_centre_charges_id, $state) {

	$sql = " SELECT service_category as price_tags, customer_total, "
                . " partner_net_payable, rate as tax_rate, product_or_services "
                . " from service_centre_charges, tax_rates "
                . " where `service_centre_charges`.id = '$service_centre_charges_id' "
                . " AND `tax_rates`.tax_code = `service_centre_charges`.tax_code "
                . " AND `tax_rates`.state = '$state'"
                . " AND `tax_rates`.product_type = `service_centre_charges`.product_type "
                . " AND (to_date is NULL or to_date >= CURDATE() ) /*AND `tax_rates`.active = 1 */ ";

	$query = $this->db->query($sql);
	return $query->result_array();
    }

    function update_prices($services_details, $booking_id, $state) {
	$unit_details_id = $services_details['unit_id'];

	$data = $this->getpricesdetails_with_tax($services_details['id'], $state);


	echo "<br/>";
	echo $unit_details_id . "-" . $booking_id, "--" . $services_details['id'];
	echo "<br/>";
	print_r($data);
	unset($services_details['unit_id']);

	if (!empty($data)) {
	    $result = array_merge($services_details, $data[0]);

	    unset($result['id']);  // unset service center charge  id  because there is no need to insert id in the booking unit details table
	    $result['customer_net_payable'] = $result['customer_total'] - $result['partner_net_payable'] - $result['around_paid_basic_charges'];
	    //log_message ('info', __METHOD__ . "update booking_unit_details data". print_r($result));
	    $result['partner_paid_basic_charges '] = $result['partner_net_payable'];

	    $this->db->where('id', $unit_details_id);
	    $this->db->update('booking_unit_details', $result);
	}
    }

    function update_unit_price($booking_id, $data) {
	$this->db->select('*');
	$this->db->where('booking_id', $booking_id);
	$query = $this->db->get('booking_unit_details');
	$result1 = $query->result_array();
	foreach ($result1 as $key) {

	    $this->db->select('around_net_payable, partner_net_payable, tax_rate, price_tags, partner_paid_basic_charges, around_paid_basic_charges');
	    $this->db->where('id', $result1[$key]['id']);
	    $query = $this->db->get('booking_unit_details');
	    $unit_details = $query->result_array();

	    // print_r($unit_details);
	    $data['id'] = $result1[$key]['id'];

	    /* echo "<br/>";
	      print_r($data);
	      echo "<br/>";
	      print_r($unit_details);
	      echo "<br/>"; */

	    $this->update_price_in_unit_details($data, $unit_details);
	}
    }

    // Update Price in unit details
    function update_price_in_unit_details($data, $unit_details) {

	$data['tax_rate'] = $unit_details[0]['tax_rate'];
	$data['around_paid_basic_charges'] = $unit_details[0]['around_paid_basic_charges'];
	// calculate partner paid tax amount
	$partner_paid_tax = ($unit_details[0]['partner_paid_basic_charges'] * $data['tax_rate']) / 100;
	// Calculate  total partner paid charges with tax
	$data['partner_paid_basic_charges'] = $unit_details[0]['partner_paid_basic_charges'] + $partner_paid_tax;

	$vendor_total_basic_charges = ($data['customer_paid_basic_charges'] + $data['partner_paid_basic_charges'] + $data['around_paid_basic_charges']) * basic_percentage;
	$around_total_basic_charges = ($data['customer_paid_basic_charges'] + $data['partner_paid_basic_charges'] + $data['around_paid_basic_charges'] - $vendor_total_basic_charges);

	$data['around_st_or_vat_basic_charges'] = $this->get_calculated_tax_charge($around_total_basic_charges, $data['tax_rate']);
	$data['vendor_st_or_vat_basic_charges'] = $this->get_calculated_tax_charge($vendor_total_basic_charges, $data['tax_rate']);

	$data['around_comm_basic_charges'] = $around_total_basic_charges - $data['around_st_or_vat_basic_charges'];
	$data['vendor_basic_charges'] = $vendor_total_basic_charges - $data['vendor_st_or_vat_basic_charges'];

	$total_vendor_addition_charge = $data['customer_paid_extra_charges'] * addtitional_percentage;
	$total_around_additional_charge = $data['customer_paid_extra_charges'] - $total_vendor_addition_charge;

	$data['around_st_extra_charges'] = $this->get_calculated_tax_charge($total_around_additional_charge, $data['tax_rate']);
	$data['vendor_st_extra_charges'] = $this->get_calculated_tax_charge($total_vendor_addition_charge, $data['tax_rate']);

	$data['around_comm_extra_charges'] = $total_around_additional_charge - $data['around_st_extra_charges'];
	$data['vendor_extra_charges'] = $total_vendor_addition_charge - $data['vendor_st_extra_charges'];

	$total_vendor_parts_charge = $data['customer_paid_parts'] * parts_percentage;
	$total_around_parts_charge = $data['customer_paid_parts'] - $total_vendor_parts_charge;
	$data['around_st_parts'] = $this->get_calculated_tax_charge($total_around_parts_charge, $data['tax_rate']);
	$data['vendor_st_parts'] = $this->get_calculated_tax_charge($total_vendor_parts_charge, $data['tax_rate']);
	$data['around_comm_parts'] = $total_around_parts_charge - $data['around_st_parts'];
	$data['vendor_parts'] = $total_vendor_parts_charge - $data['vendor_st_parts'];

	$vendor_around_charge = ($data['customer_paid_basic_charges'] + $data['customer_paid_parts'] + $data['customer_paid_extra_charges']) - ($vendor_total_basic_charges + $total_vendor_addition_charge + $total_vendor_parts_charge );

	if ($vendor_around_charge > 0) {

	    $data['vendor_to_around'] = $vendor_around_charge;
	    $data['around_to_vendor'] = 0;
	} else {
	    $data['vendor_to_around'] = 0;
	    $data['around_to_vendor'] = abs($vendor_around_charge);
	}

	unset($data['internal_status']);
	$this->db->where('id', $data['id']);
	$this->db->update('booking_unit_details', $data);
    }

    /**
     * @desc: calculate service charges and vat charges
     * @param : total charges and tax rate
     * @return calculate charges
     */
    function get_calculated_tax_charge($total_charges, $tax_rate) {
	//52.50 = (402.50 / ((100 + 15)/100)) * ((15)/100)
	//52.50 =  (402.50 / 1.15) * (0.15)
	$st_vat_charge = sprintf("%.2f", ($total_charges / ((100 + $tax_rate) / 100)) * (($tax_rate) / 100));
	return $st_vat_charge;
    }

    function p_get_all_booking_id() {
	$sql = "SELECT booking_details.booking_id, booking_details.partner_id, booking_details.service_id,
	    booking_details.appliance_id, booking_unit_details.appliance_capacity,
	    booking_unit_details.appliance_brand, booking_unit_details.appliance_category,
	    booking_unit_details.appliance_size,
	    booking_unit_details.serial_number,
	    booking_unit_details.price_tags, booking_unit_details.appliance_tag, booking_unit_details.purchase_month,
	    booking_unit_details.purchase_year, booking_unit_details.model_number, booking_details.service_charge,
	    booking_details.additional_service_charge, booking_details.parts_cost, booking_details.internal_status,
            appliance_details.description as appliance_description
            from booking_details, booking_unit_details,appliance_details
            where booking_details.`current_status` IN ('Pending', 'Rescheduled') AND 
            booking_unit_details.booking_id = booking_details.booking_id AND 
            appliance_details.id =  booking_details.appliance_details";

	$query = $this->db->query($sql);
	$result = $query->result_array();
	return $result;
    }

    function p_get_all_booking_unit() {
	$sql = " SELECT booking_unit_details.*, `booking_details`.booking_pincode FROM `booking_unit_details`, booking_details "
	    . "WHERE `booking_unit_details`.booking_id = `booking_details`.`booking_id` AND "
	    . "`booking_unit_details`.`booking_id` in "
	    . "(SELECT booking_id FROM `booking_details` WHERE "
	    . "`current_status` IN ('Pending', 'Rescheduled'));";

	$query = $this->db->query($sql);
	return $query->result_array();
    }

    

    function get_all_followUp() {
	$sql = "
	    SELECT `booking_details`.*, booking_unit_details.appliance_capacity,
	    booking_unit_details.appliance_brand, booking_unit_details.appliance_category,
	    booking_unit_details.price_tags, booking_unit_details.appliance_tag,
	    booking_unit_details.purchase_month, booking_unit_details.purchase_year,
	    booking_unit_details.model_number,booking_unit_details.appliance_size,
	    booking_unit_details.serial_number,
            appliance_details.description as appliance_description
	    FROM  `booking_details`, booking_unit_details, appliance_details
	    WHERE  `current_status` LIKE  '%FollowUp%' AND appliance_details.id =  booking_details.appliance_id AND 
	    booking_unit_details.booking_id = booking_details.booking_id;
	    ";

	$query = $this->db->query($sql);
	return $query->result_array();
    }

    function update_booking_unit_details($booking_id, $data) {
	$this->db->where('booking_id', $booking_id);
	$this->db->update('booking_unit_details', $data);
    }

    function addunitdetails($booking) {
	log_message('info', __METHOD__ . "booking unit details data" . print_r($booking, true));
	$this->db->insert('booking_unit_details', $booking);
	return $this->db->insert_id();
    }

    function q_get_all_booking_unit() {
	$sql = " SELECT booking_unit_details.*, `booking_details`.booking_pincode FROM `booking_unit_details`, booking_details "
	    . "WHERE `booking_unit_details`.booking_id = `booking_details`.`booking_id` AND "
	    . "`booking_unit_details`.`booking_id` in "
	    . "(SELECT booking_id FROM `booking_details` WHERE "
	    . "`current_status`  LIKE '%FollowUp%') ";

	$query = $this->db->query($sql);
	return $query->result_array();
    }

    function get_price_mapping_partner_code($partner_code, $partner_id = "") {
	$this->db->select('price_mapping_id');
	if ($partner_code != "") {
	    $this->db->where('code', $partner_code);
	} else {
	    $this->db->where('partner_id', $partner_id);
	}

	$query = $this->db->get('bookings_sources');
	if ($query->num_rows() > 0) {
	    $result = $query->result_array();
	    return $result[0]['price_mapping_id'];
	} else {
	    return "";
	}
    }

    function get_all_cancelled_query() {
	$sql = "SELECT `booking_details`.*, booking_unit_details.appliance_capacity, "
	    . "booking_unit_details.appliance_brand, booking_unit_details.appliance_category, "
	    . "booking_unit_details.price_tags, booking_unit_details.appliance_tag, "
	    . "booking_unit_details.purchase_month, booking_unit_details.purchase_year, "
	    . "booking_unit_details.model_number,booking_unit_details.appliance_size,"
	    . "appliance_details.description as appliance_description, booking_unit_details.serial_number "
	    . "FROM booking_details,  `booking_unit_details`, appliance_details "
	    . "WHERE   `current_status` LIKE  '%Cancelled%' AND "
	    . "booking_unit_details.booking_id = booking_details.booking_id  "
	    . "AND booking_details.create_date >= '2016-06-01' AND appliance_details.id = booking_details.appliance_id ";

	$query = $this->db->query($sql);
	return $query->result_array();
    }

    function c_q_get_booking_unit() {
	$sql = " SELECT booking_unit_details.*, `booking_details`.booking_pincode FROM `booking_unit_details`, booking_details "
	    . "WHERE `booking_unit_details`.booking_id = `booking_details`.`booking_id` AND "
	    . "`booking_unit_details`.`booking_id` in "
	    . "(SELECT booking_id FROM `booking_details` WHERE "
	    . "`current_status`  LIKE '%Cancelled%' AND booking_details.create_date >= '2016-06-01' ) ";

	$query = $this->db->query($sql);
	return $query->result_array();
    }

    function c_q_getbookingid() {
	$sql = "SELECT booking_details.booking_id, booking_details.service_charge, booking_details.additional_service_charge, booking_details.parts_cost, booking_details.internal_status
            from booking_details
            where booking_details.create_date >= '2016-06-01' AND `current_status` = '%Cancelled%'
            ";

	$query = $this->db->query($sql);
	$result = $query->result_array();
	return $result;
    }
    
    function get_service_center_inprocess(){
        $sql = "SELECT booking_details.`booking_id` FROM booking_details,  `service_center_booking_action`
               WHERE service_center_booking_action.current_status =  'InProcess'
               AND ( booking_details.current_status =  'Pending' OR booking_details.current_status =  'Rescheduled'
               ) AND service_center_booking_action.booking_id = booking_details.booking_id ";

	$query = $this->db->query($sql);
	$result = $query->result_array();
        
        $this->update_service_center_table($result, false);
    }
    
     function get_service_center_pending(){
        $sql = "SELECT booking_details.`booking_id` FROM booking_details,  `service_center_booking_action`
               WHERE service_center_booking_action.current_status =  'Pending'
               AND ( booking_details.current_status =  'Pending' OR booking_details.current_status =  'Rescheduled'
               ) AND service_center_booking_action.booking_id = booking_details.booking_id ";

	$query = $this->db->query($sql);
	$result = $query->result_array();
        
        $this->update_service_center_table($result, true);
    }
    
    function get_service_center_completed_or_cancelled(){
        $sql = "SELECt booking_details.booking_id from booking_details, service_center_booking_action"
                . " where service_center_booking_action.current_status = 'Completed' "
                . " AND (booking_details.current_status = '%Completed%' OR booking_details.current_status = '%Cancelled%' ) "
                . "AND service_center_booking_action.booking_id = booking_details.booking_id ";
        $query = $this->db->query($sql);
	$result = $query->result_array();
        $this->update_completed_service_center_table($result);
    }
    /**
     *  We get all completed or cancelled booking in parm.
     *  We get all unit details from booking id and count  unit details.
     *  If count of unit details is 2 then we  upadte service center table  and new one inseted
     *  If count of unit details is 1 then only we update service cente table
     * @param type $result
     */
    function update_completed_service_center_table($result){
        foreach ($result as $value){
            $this->db->select('booking_id, id, price_tags, booking_status');
            $this->db->where('booking_id', $value['booking_id']);
            $query1 = $this->db->get('booking_unit_details');
	    $result1 = $query1->result_array();
            if(count($result) ===2){
                $data['unit_details_id'] = $result1[0]['id'];
                $data['internal_status'] = $result1[0]['booking_status'];
                $data['current_status'] = $result1[0]['booking_status'];
                
                $this->db->where('booking_id', $result1[0]['booking_id']);
		$this->db->update('service_center_booking_action', $data);
                
                $this->db->select('*');
		$this->db->where('booking_id', $result1[0]['booking_id']);
		$query2 = $this->db->get('service_center_booking_action');
		$result2 = $query2->result_array();
		unset($result2[0]['id']);
		$result2[0]['unit_details_id'] = $result1[1]['id'];
		$result2[0]['current_status'] = $result1[1]['booking_status'];
                $result2[0]['internal_status'] = $result1[1]['booking_status'];
                
                $this->db->insert('service_center_booking_action', $result2[0]);
		echo "<br/>";
		echo "two";
		echo "<br/>";
		echo $result1[0]['booking_id'];
		echo "<br/>";
                
            } else if (count($result1) === 1) {
		echo "<br/>";
		echo "only one";
		echo "<br/>";
		echo $result1[0]['booking_id'];
		echo "<br/>";
                $data1['internal_status'] = $result1[0]['booking_status'];
                $data1['current_status'] = $result1[0]['booking_status'];
		$this->db->where('booking_id', $result1[0]['booking_id']);
		$this->db->update('service_center_booking_action', $data1);
	    }
        }
        
    }
    /**
     * We get all completed or cancelled booking in parm.
     * We get all unit details from booking id and count  unit details.
     * If count of unit details is 2 then we  upadte service center table  and new one inseted
     * If count of unit details is 1 then only we update service cente table
     * If internal status is ture then we update in current status and internal status is Pending
     * For only Inprocess -- First we get internal status from service center action table and then update service center action table
     * Otherwise InProcess
     * @param type $result
     * @param type $internal_status
     */
    function update_service_center_table($result, $internal_status = true) {
	
	foreach ($result as $value) {
	    $this->db->select('booking_id,id, price_tags, booking_status');
	    $this->db->where('booking_id', $value['booking_id']);
	    $query1 = $this->db->get('booking_unit_details');
	    $result1 = $query1->result_array();

	    if (count($result1) === 2) {
		print_r($result1);
		$this->db->where('booking_id', $result1[0]['booking_id']);
		$this->db->update('service_center_booking_action', array('unit_details_id ' => $result1[0]['id']));

		$this->db->select('*');
		$this->db->where('booking_id', $result1[0]['booking_id']);
		$query2 = $this->db->get('service_center_booking_action');
		$result2 = $query2->result_array();
		unset($result2[0]['id']);
		$result2[0]['unit_details_id'] = $result1[1]['id'];
		
                if($internal_status){
                    $result2[0]['current_status'] = "Pending";
                    $result2[0]['internal_status'] = "Pending";
                } else {
                     $result2[0]['current_status'] = "InProcess";
                     //$result2[0]['internal_status'] = $result1[1]['booking_status'];
                }
	
		$this->db->insert('service_center_booking_action', $result2[0]);
		echo "<br/>";
		echo "two";
		echo "<br/>";
		echo $result1[0]['booking_id'];
		echo "<br/>";
	    } else if (count($result1) === 1) {
		echo "<br/>";
		echo "only one";
		echo "<br/>";
		echo $result1[0]['booking_id'];
		echo "<br/>";
		$this->db->where('booking_id', $result1[0]['booking_id']);
		$this->db->update('service_center_booking_action', array('unit_details_id ' => $result1[0]['id']));
	    }
            
            if($internal_status){} else {
                $this->db->select('internal-status');
                $this->db->where('booking_id', $value['booking_id']);
                $this->db->where('current_status', 'InProcess');
                $query4 = $this->db->get('service_center_booking_action');
                $service_internal_status = $query4->result_array();
                
                if($service_internal_status[0]['internal_status'] == 'Completed TV Without Stand' || 
                        $service_internal_status[0]['internal_status'] == 'Completed With Demo' ||
                        $service_internal_status[0]['internal_status'] == 'Completed TV With Stand'){
                    $service_internal_status[0]['internal_status'] = "Completed";
                }
                $this->db->where('booking_id', $value['booking_id']);
                $this->db->update('service_center_booking_action', array('internal_status'=> $service_internal_status[0]['internal_status']));
            }
	}
    }

}
