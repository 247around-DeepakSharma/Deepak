<?php

class Booking_model extends CI_Model {
    /**
     * @desc load both db
     */
    function __construct() {
	parent::__Construct();

	$this->db_location = $this->load->database('default1', TRUE, TRUE);
	$this->db = $this->load->database('default', TRUE, TRUE);
    }

    function date_compare_queries($a, $b) {
	if ($a->booking_date == '' || $b->booking_date == '') {
	    if (strtotime($a->create_date) == strtotime($b->create_date)) {
		$t1 = $a->id;
		$t2 = $b->id;
	    } else {
		$t1 = strtotime($a->create_date);
		$t2 = strtotime($b->create_date);
	    }
	} else {
	    $t1 = strtotime($a->booking_date);
	    $t2 = strtotime($b->booking_date);
	}

	return $t2 - $t1;
    }

    function date_compare_bookings($a, $b) {
	$t1 = strtotime($a->booking_date);
	$t2 = strtotime($b->booking_date);

	return $t2 - $t1;
    }

    function date_compare_assign_pending_bookings($a, $b) {
	$t1 = strtotime($a['booking_date']);
	$t2 = strtotime($b['booking_date']);

	return $t1 - $t2;
    }

    /** @description:* add unit details for a booking
     *  @param : booking
     *  @return :
     */
    function addunitdetails($booking) {
	$units = $booking['quantity'];

	if ($units == 1) {
	    $unit_detail = array("booking_id" => $booking['booking_id'],
		"appliance_brand" => $booking['appliance_brand1'],
		"appliance_category" => $booking['appliance_category1'],
		"appliance_capacity" => $booking['appliance_capacity1'],
		"model_number" => $booking['model_number1'],
		"price_tags" => $booking['items_selected1'],
		"purchase_year" => $booking['purchase_year1'],
		"total_price" => $booking['total_price1'],
		"appliance_tag" => $booking['appliance_tags1']);

	    $this->db->insert('booking_unit_details', $unit_detail);
	} elseif ($units == 2) {
	    $unit_detail = array("booking_id" => $booking['booking_id'],
		"appliance_brand" => $booking['appliance_brand1'],
		"appliance_category" => $booking['appliance_category1'],
		"appliance_capacity" => $booking['appliance_capacity1'],
		"model_number" => $booking['model_number1'],
		"price_tags" => $booking['items_selected1'],
		"purchase_year" => $booking['purchase_year1'],
		"total_price" => $booking['total_price1'],
		"appliance_tag" => $booking['appliance_tags1']);

	    $this->db->insert('booking_unit_details', $unit_detail);

	    $unit_detail = array("booking_id" => $booking['booking_id'],
		"appliance_brand" => $booking['appliance_brand2'],
		"appliance_category" => $booking['appliance_category2'],
		"appliance_capacity" => $booking['appliance_capacity2'],
		"model_number" => $booking['model_number2'],
		"price_tags" => $booking['items_selected2'],
		"purchase_year" => $booking['purchase_year2'],
		"total_price" => $booking['total_price2'],
		"appliance_tag" => $booking['appliance_tags2']);

	    $this->db->insert('booking_unit_details', $unit_detail);
	} elseif ($units == 3) {
	    $unit_detail = array("booking_id" => $booking['booking_id'],
		"appliance_brand" => $booking['appliance_brand1'],
		"appliance_category" => $booking['appliance_category1'],
		"appliance_capacity" => $booking['appliance_capacity1'],
		"model_number" => $booking['model_number1'],
		"price_tags" => $booking['items_selected1'],
		"purchase_year" => $booking['purchase_year1'],
		"total_price" => $booking['total_price1'],
		"appliance_tag" => $booking['appliance_tags1']);

	    $this->db->insert('booking_unit_details', $unit_detail);

	    $unit_detail = array("booking_id" => $booking['booking_id'],
		"appliance_brand" => $booking['appliance_brand2'],
		"appliance_category" => $booking['appliance_category2'],
		"appliance_capacity" => $booking['appliance_capacity2'],
		"model_number" => $booking['model_number2'],
		"price_tags" => $booking['items_selected2'],
		"purchase_year" => $booking['purchase_year2'],
		"total_price" => $booking['total_price2'],
		"appliance_tag" => $booking['appliance_tags2']);

	    $this->db->insert('booking_unit_details', $unit_detail);

	    $unit_detail = array("booking_id" => $booking['booking_id'],
		"appliance_brand" => $booking['appliance_brand3'],
		"appliance_category" => $booking['appliance_category3'],
		"appliance_capacity" => $booking['appliance_capacity3'],
		"model_number" => $booking['model_number3'],
		"price_tags" => $booking['items_selected3'],
		"purchase_year" => $booking['purchase_year3'],
		"total_price" => $booking['total_price3'],
		"appliance_tag" => $booking['appliance_tags3']);

	    $this->db->insert('booking_unit_details', $unit_detail);
	} elseif ($units == 4) {
	    $unit_detail = array("booking_id" => $booking['booking_id'],
		"appliance_brand" => $booking['appliance_brand1'],
		"appliance_category" => $booking['appliance_category1'],
		"appliance_capacity" => $booking['appliance_capacity1'],
		"model_number" => $booking['model_number1'],
		"price_tags" => $booking['items_selected1'],
		"purchase_year" => $booking['purchase_year1'],
		"total_price" => $booking['total_price1'],
		"appliance_tag" => $booking['appliance_tags1']);

	    $this->db->insert('booking_unit_details', $unit_detail);

	    $unit_detail = array("booking_id" => $booking['booking_id'],
		"appliance_brand" => $booking['appliance_brand2'],
		"appliance_category" => $booking['appliance_category2'],
		"appliance_capacity" => $booking['appliance_capacity2'],
		"model_number" => $booking['model_number2'],
		"price_tags" => $booking['items_selected2'],
		"purchase_year" => $booking['purchase_year2'],
		"total_price" => $booking['total_price2'],
		"appliance_tag" => $booking['appliance_tags2']);

	    $this->db->insert('booking_unit_details', $unit_detail);

	    $unit_detail = array("booking_id" => $booking['booking_id'],
		"appliance_brand" => $booking['appliance_brand3'],
		"appliance_category" => $booking['appliance_category3'],
		"appliance_capacity" => $booking['appliance_capacity3'],
		"model_number" => $booking['model_number3'],
		"price_tags" => $booking['items_selected3'],
		"purchase_year" => $booking['purchase_year3'],
		"total_price" => $booking['total_price3'],
		"appliance_tag" => $booking['appliance_tags3']);

	    $this->db->insert('booking_unit_details', $unit_detail);

	    $unit_detail = array("booking_id" => $booking['booking_id'],
		"appliance_brand" => $booking['appliance_brand4'],
		"appliance_category" => $booking['appliance_category4'],
		"appliance_capacity" => $booking['appliance_capacity4'],
		"model_number" => $booking['model_number4'],
		"price_tags" => $booking['items_selected4'],
		"purchase_year" => $booking['purchase_year4'],
		"total_price" => $booking['total_price4'],
		"appliance_tag" => $booking['appliance_tags4']);

	    $this->db->insert('booking_unit_details', $unit_detail);
	}
	//}
    }

    function addappliancedetails($booking) {
	$units = $booking['quantity'];
	if ($units == 1) {
	    $appliance_detail = array("user_id" => $booking['user_id'],
		"service_id" => $booking['service_id'],
		"brand" => $booking['appliance_brand1'],
		"category" => $booking['appliance_category1'],
		"capacity" => $booking['appliance_capacity1'],
		"model_number" => $booking['model_number1'],
		"purchase_year" => $booking['purchase_year1'],
		"tag" => $booking['appliance_tags1'],
		"last_service_date" => date('Y-m-d H:i:s'));
	    $this->db->insert('appliance_details', $appliance_detail);
	    $id = $this->db->insert_id();
	} elseif ($units == 2) {
	    $appliance_detail = array("user_id" => $booking['user_id'],
		"service_id" => $booking['service_id'],
		"brand" => $booking['appliance_brand1'],
		"category" => $booking['appliance_category1'],
		"capacity" => $booking['appliance_capacity1'],
		"model_number" => $booking['model_number1'],
		"purchase_year" => $booking['purchase_year1'],
		"tag" => $booking['appliance_tags1'],
		"last_service_date" => date('Y-m-d H:i:s'));
	    $this->db->insert('appliance_details', $appliance_detail);
	    $appliance_detail = array("user_id" => $booking['user_id'],
		"service_id" => $booking['service_id'],
		"brand" => $booking['appliance_brand2'],
		"category" => $booking['appliance_category2'],
		"capacity" => $booking['appliance_capacity2'],
		"model_number" => $booking['model_number2'],
		"purchase_year" => $booking['purchase_year2'],
		"tag" => $booking['appliance_tags2'],
		"last_service_date" => date('Y-m-d H:i:s'));
	    $this->db->insert('appliance_details', $appliance_detail);
	    $id = $this->db->insert_id();
	} elseif ($units == 3) {
	    $appliance_detail = array("user_id" => $booking['user_id'],
		"service_id" => $booking['service_id'],
		"brand" => $booking['appliance_brand1'],
		"category" => $booking['appliance_category1'],
		"capacity" => $booking['appliance_capacity1'],
		"model_number" => $booking['model_number1'],
		"purchase_year" => $booking['purchase_year1'],
		"tag" => $booking['appliance_tags1'],
		"last_service_date" => date('Y-m-d H:i:s'));
	    $this->db->insert('appliance_details', $appliance_detail);
	    $appliance_detail = array("user_id" => $booking['user_id'],
		"service_id" => $booking['service_id'],
		"brand" => $booking['appliance_brand2'],
		"category" => $booking['appliance_category2'],
		"capacity" => $booking['appliance_capacity2'],
		"model_number" => $booking['model_number2'],
		"purchase_year" => $booking['purchase_year2'],
		"tag" => $booking['appliance_tags2'],
		"last_service_date" => date('Y-m-d H:i:s'));
	    $this->db->insert('appliance_details', $appliance_detail);
	    $appliance_detail = array("user_id" => $booking['user_id'],
		"service_id" => $booking['service_id'],
		"brand" => $booking['appliance_brand3'],
		"category" => $booking['appliance_category3'],
		"capacity" => $booking['appliance_capacity3'],
		"model_number" => $booking['model_number3'],
		"purchase_year" => $booking['purchase_year3'],
		"tag" => $booking['appliance_tags3'],
		"last_service_date" => date('Y-m-d H:i:s'));
	    $this->db->insert('appliance_details', $appliance_detail);
	    $id = $this->db->insert_id();
	} elseif ($units == 4) {
	    $appliance_detail = array("user_id" => $booking['user_id'],
		"service_id" => $booking['service_id'],
		"brand" => $booking['appliance_brand1'],
		"category" => $booking['appliance_category1'],
		"capacity" => $booking['appliance_capacity1'],
		"model_number" => $booking['model_number1'],
		"purchase_year" => $booking['purchase_year1'],
		"tag" => $booking['appliance_tags1'],
		"last_service_date" => date('Y-m-d H:i:s'));
	    $this->db->insert('appliance_details', $appliance_detail);
	    $appliance_detail = array("user_id" => $booking['user_id'],
		"service_id" => $booking['service_id'],
		"brand" => $booking['appliance_brand2'],
		"category" => $booking['appliance_category2'],
		"capacity" => $booking['appliance_capacity2'],
		"model_number" => $booking['model_number2'],
		"purchase_year" => $booking['purchase_year2'],
		"tag" => $booking['appliance_tags2'],
		"last_service_date" => date('Y-m-d H:i:s'));
	    $this->db->insert('appliance_details', $appliance_detail);
	    $appliance_detail = array("user_id" => $booking['user_id'],
		"service_id" => $booking['service_id'],
		"brand" => $booking['appliance_brand3'],
		"category" => $booking['appliance_category3'],
		"capacity" => $booking['appliance_capacity3'],
		"model_number" => $booking['model_number3'],
		"purchase_year" => $booking['purchase_year3'],
		"tag" => $booking['appliance_tags3'],
		"last_service_date" => date('Y-m-d H:i:s'));
	    $this->db->insert('appliance_details', $appliance_detail);
	    $appliance_detail = array("user_id" => $booking['user_id'],
		"service_id" => $booking['service_id'],
		"brand" => $booking['appliance_brand4'],
		"category" => $booking['appliance_category4'],
		"capacity" => $booking['appliance_capacity4'],
		"model_number" => $booking['model_number4'],
		"purchase_year" => $booking['purchase_year4'],
		"tag" => $booking['appliance_tags4'],
		"last_service_date" => date('Y-m-d H:i:s'));
	    $this->db->insert('appliance_details', $appliance_detail);
	    $id = $this->db->insert_id();
	}
	$sql = "SELECT * FROM appliance_details WHERE id = $id";
	$query = $this->db->query($sql);
	return $query->result_array();
    }

    function addexcelappliancedetails($booking) {
	$appliance_detail = array("user_id" => $booking['user_id'],
	    "service_id" => $booking['service_id'],
	    "brand" => $booking['appliance_brand'],
	    "category" => $booking['appliance_category'],
	    "capacity" => $booking['appliance_capacity'],
	    "model_number" => $booking['model_number'],
	    "description" => $booking['description'],
	    "purchase_month" => $booking['purchase_month'],
	    "purchase_year" => $booking['purchase_year'],
	    "last_service_date" => $booking['last_service_date'],
	    "tag" => $booking['appliance_tags']);
	$this->db->insert('appliance_details', $appliance_detail);
	$id = $this->db->insert_id();
	return $id;
    }

    function addapplianceunitdetails($booking) {
	$unit_detail = array("booking_id" => $booking['booking_id'],
	    "appliance_brand" => $booking['appliance_brand'],
	    "appliance_category" => $booking['appliance_category'],
	    "appliance_capacity" => $booking['appliance_capacity'],
	    "model_number" => $booking['model_number'],
	    "price_tags" => $booking['items_selected'],
	    "purchase_year" => $booking['purchase_year'],
	    "total_price" => $booking['total_price'],
	    "appliance_tag" => $booking['appliance_tags']);
	return $this->db->insert('booking_unit_details', $unit_detail);
    }

    /** @description:* add booking
     *  @param : booking
     *  @return : array (booking)
     */
    function addbooking($booking, $appliance_id, $city = "", $state = "") {
	$booking_detail = array(
	    "user_id" => $booking['user_id'],
	    "service_id" => $booking['service_id'],
	    "booking_id" => $booking['booking_id'],
	    "appliance_id" => $appliance_id,
	    "booking_address" => $booking['booking_address'],
	    "booking_pincode" => $booking['booking_pincode'],
	    "booking_primary_contact_no" => $booking['booking_primary_contact_no'],
	    "booking_alternate_contact_no" => $booking['booking_alternate_contact_no'],
	    "booking_date" => $booking['booking_date'],
	    "booking_timeslot" => $booking['booking_timeslot'],
	    "booking_remarks" => $booking['booking_remarks'],
	    "query_remarks" => $booking['query_remarks'],
	    "current_status" => $booking['current_status'],
	    "internal_status" => $booking['internal_status'],
	    "type" => $booking['type'],
	    "source" => $booking['source'],
	    "quantity" => $booking['quantity'],
	    "potential_value" => $booking['potential_value'],
	    "amount_due" => $booking['amount_due']
	);
	// Added city coming from snapdeal
	if ($city != "") {
	    $booking_detail['city'] = $city;
	}

	if ($state != "") {
	    $booking_detail['state'] = $state;
	}

	$this->db->insert('booking_details', $booking_detail);
	return $this->db->insert_id();
    }

    function selectservice() {
	$query = $this->db->query("Select id,services from services where isBookingActive='1'");
	return $query->result();
    }

    function selectservicebyid($service_id) {
	$query = $this->db->query("SELECT services from services where id='$service_id'");
	return $query->result_array();
    }

    function selectbrand() {
	$query = $this->db->query("Select DISTINCT brand_name from appliance_brands
                                    order by brand_name");
	return $query->result();
    }

    function selectcategory() {

	$query = $this->db->query("Select DISTINCT category from service_centre_charges");
	return $query->result();
    }

    function selectcapacity() {

	$query = $this->db->query("Select DISTINCT capacity from service_centre_charges");
	return $query->result();
    }

    function finduser($phone) {
	$query = $this->db->query("Select user_id,name,user_email from users
                                where phone_number='$phone' AND is_verified='1'");
	return $query->result();
    }

    /**
     *  @desc : This function will load bookings
     *  @param: void
     *  @return : print Booking on Booking Page
     */
    public function viewbooking() {
	$query = $this->db->query("Select services.services,
            users.name as customername, users.phone_number,
            booking_details.*, service_centres.name as service_centre_name,
            service_centres.primary_contact_name,service_centres.primary_contact_phone_1
            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`"
	);

	return $query->result();
    }

    //Function to view all pending bookings
    public function viewallpendingbooking() {
	$query = $this->db->query("Select services.services,
            users.name as customername, users.phone_number,
            booking_details.*, service_centres.name as service_centre_name,
            service_centres.primary_contact_name,service_centres.primary_contact_phone_1
            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
            WHERE (booking_details.current_status='Pending' OR booking_details.current_status='Rescheduled')"
	);

	$temp = $query->result();

	usort($temp, array($this, 'date_compare_bookings'));

	return $temp;
    }

    public function view_booking($limit, $start) {
	$this->db->limit($limit, $start);

	$query = $this->db->query("Select services.services,
            users.name as customername, users.phone_number,
            booking_details.*, service_centres.name as service_centre_name,
            service_centres.primary_contact_name,service_centres.primary_contact_phone_1
            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
            WHERE (booking_details.current_status='Pending' OR booking_details.current_status='Rescheduled')
            ORDER BY create_date DESC
            LIMIT $start,$limit"
	);

	return $query->result();
    }

    function view_all_completed_booking() {
	$query = $this->db->query("Select services.services,
            users.name as customername, users.phone_number,
            booking_details.*, service_centres.name as service_centre_name,
            service_centres.district as city,
           service_centres.primary_contact_name,service_centres.primary_contact_phone_1
            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
            WHERE `booking_id` NOT LIKE '%Q-%' AND
            (booking_details.current_status = 'Completed')
            ORDER BY closed_date DESC"
	);

	return $query->result();
    }

    function view_completed_booking($limit, $start) {

	$query = $this->db->query("Select services.services,
            users.name as customername, users.phone_number,
            booking_details.*, service_centres.name as service_centre_name,
            service_centres.district as city,
           service_centres.primary_contact_name,service_centres.primary_contact_phone_1
            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
            WHERE `booking_id` NOT LIKE '%Q-%' AND
            (booking_details.current_status = 'Completed')
	    ORDER BY closed_date DESC LIMIT $start, $limit"
	);

	return $query->result();
    }

    function view_cancelled_booking($limit, $start) {

	$query = $this->db->query("Select services.services,
            users.name as customername, users.phone_number,
            booking_details.*, service_centres.name as service_centre_name,
            service_centres.district as city,
           service_centres.primary_contact_name,service_centres.primary_contact_phone_1
            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
            WHERE `booking_id` NOT LIKE '%Q-%' AND
            (booking_details.current_status = 'Cancelled')
	    ORDER BY closed_date DESC LIMIT $start, $limit"
	);

	return $query->result();
    }

    function view_all_cancelled_booking() {
	$query = $this->db->query("Select services.services,
            users.name as customername, users.phone_number,
            booking_details.*, service_centres.name as service_centre_name,
           service_centres.primary_contact_name,service_centres.primary_contact_phone_1
            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
            WHERE `booking_id` NOT LIKE '%Q-%' AND
            (booking_details.current_status = 'Cancelled')
            ORDER BY closed_date DESC"
	);

	return $query->result();
    }

    function status_sorted_booking($limit, $start) {
	$query = $this->db->query("Select services.services,
            users.name as customername, users.phone_number,
            booking_details.*, service_centres.name as service_centre_name,
            service_centres.primary_contact_name,service_centres.primary_contact_phone_1
            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id` WHERE
            `booking_id` NOT LIKE '%Q-%' AND
            (booking_details.current_status='Pending' OR booking_details.current_status='Rescheduled')
            ORDER BY current_status DESC
            LIMIT $start,$limit"
	);


	return $query->result();
    }

    function service_center_sorted_booking($limit, $start) {
	$query = $this->db->query("Select services.services,
            users.name as customername, users.phone_number,
            booking_details.*, service_centres.name as service_centre_name,
            service_centres.primary_contact_name,service_centres.primary_contact_phone_1
            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id` WHERE
            `booking_id` NOT LIKE '%Q-%' AND
            (booking_details.current_status='Pending' OR booking_details.current_status='Rescheduled')
            ORDER BY service_centres.name ASC
            LIMIT $start,$limit"
	);


	return $query->result();
    }

    function service_center_details() {
	$query = $this->db->query("Select service_centres.primary_contact_name,
    service_centres.primary_contact_phone_1 from service_centres,booking_details
    where booking_details.assigned_vendor_id=service_centres.id ");

	return $query->result();
    }

    /**
     * @desc : This funtion count total no of bookings
     * @param : void
     * @return : total no bookings
     */
    public function total_booking() {
	return $this->db->count_all_results("booking_details");
    }

    //Returns count of total pending bookings
    public function total_pending_booking($booking_id = "", $service_center_id = "") {
	$where = "";

	if ($booking_id != "") {
	    $where .= "AND `booking_details`.`booking_id` = '$booking_id'";
	}

	if ($service_center_id != "") {
	    $where .= " AND assigned_vendor_id = '" . $service_center_id . "'";
	    $where .= "AND DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= -1";
	} else {
	    $where .= "AND DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= -1";
	}

	$query = $this->db->query("Select count(*) as count from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id` WHERE
            `booking_id` NOT LIKE 'Q-%' $where AND
            (booking_details.current_status='Pending' OR booking_details. current_status='Rescheduled')"
	);

	$count = $query->result_array();

	return $count[0]['count'];
    }

    function date_sorted_booking($limit, $start, $booking_id = "", $service_center_id = "") {
	$where = "";

	if ($booking_id != "") {
	    $where .= "AND `booking_details`.`booking_id` = '$booking_id'";
	}

	if ($service_center_id != "") {
	    $where .= " AND assigned_vendor_id = '" . $service_center_id . "'";
	    $where .= " AND DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= -1";
	} else {
	    $where .= " AND DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= -1";
	}

	$query = $this->db->query("Select services.services,
            users.name as customername, users.phone_number,
            booking_details.*, service_centres.name as service_centre_name,
            service_centres.primary_contact_name,service_centres.primary_contact_phone_1
            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id` WHERE
    		`booking_id` NOT LIKE 'Q-%' $where AND
            (booking_details.current_status='Pending' OR booking_details.current_status='Rescheduled')"
	);

	$temp = $query->result();

	foreach ($temp as $key => $value) {
	    $this->db->select('*');
	    $this->db->where('booking_id', $value->booking_id);
	    $status = array('Pending', 'Rescheduled');
	    $this->db->where_in('current_status', $status);
	    $query2 = $this->db->get('service_center_booking_action');

	    if ($query2->num_rows > 0) {
		if ($service_center_id != "") {
		    $result2 = $query2->result_array();
		    $temp[$key]->current_status = "In Process";
		    $temp[$key]->admin_remarks = $result2[0]['admin_remarks'];
		} else {
		    $temp[$key]->current_status = "Review";
		}
	    }
	}

	usort($temp, array($this, 'date_compare_bookings'));

	//return slice of the sorted array
	return array_slice($temp, $start, $limit);
    }

    public function total_completed_booking() {

	$query = $this->db->query("Select count(*) as count from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
            WHERE `booking_id` NOT LIKE '%Q-%' AND
            (booking_details.current_status = 'Completed' OR booking_details.current_status = 'Cancelled')"
	);

	$count = $query->result_array();
	return $count[0]['count'];
    }

    public function total_pending_queries($booking_id = "") {
	$where = "";

	if ($booking_id != "")
	    $where .= "AND `booking_details`.`booking_id` = '$booking_id'";
	$sql = "SELECT count(*) as count from booking_details
        JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
        JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
        LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
        WHERE `booking_id` LIKE '%Q-%' $where  AND
        (booking_details.current_status='FollowUp')";
	$query = $this->db->query($sql);
	$count = $query->result_array();

	return $count[0]['count'];
    }

    public function total_cancelled_queries() {
	$sql = "SELECT count(*) as count from booking_details
        JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
        JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
        LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
        WHERE `booking_id` LIKE '%Q-%' AND
        (booking_details.current_status='Cancelled')";

	$query = $this->db->query($sql);
	$count = $query->result_array();

	return $count[0]['count'];
    }

    public function total_user_booking($user_id) {
	$this->db->where("user_id = '$user_id'");
	$result = $this->db->count_all_results("booking_details");
	return $result;
    }

    function cancelreason() {
	$query = $this->db->query("Select id,reason from booking_cancellation_reasons");
	return $query->result();
    }

    function cancel_booking($booking_id, $data) {
	$states = array('Pending', 'Rescheduled');
	$this->db->where(array('booking_id' => $booking_id));
	$this->db->where_in('current_status', $states);
	$this->db->update('booking_details', $data);
    }

    public function get_booking($limit, $start) {
	$this->db->limit($limit, $start);
	//$this->db->order_by('priority asc');
	$query = $this->db->get('booking_details');
	if ($query->num_rows() > 0) {
	    foreach ($query->result() as $row) {
		$data[] = $row;
	    }
	    return $data;
	    //return $query->result_array();
	}
	return false;
    }

    function getbooking($booking_id) {
	$this->db->select('*');

	$this->db->where('booking_id', $booking_id);
	$query = $this->db->get('booking_details');
	return $query->result_array();
    }

    function getBookingCountByUser($user_id) {
	$this->db->where("user_id", $user_id);
	$this->db->from("booking_details");

	//$query = $this->db->get();
	$result = $this->db->count_all_results();

	return $result;
    }

    function complete_booking($booking_id, $data) {
	$sql = "Update booking_details set current_status='Completed',closed_date='$data[closed_date]', "
	    . "closing_remarks='$data[closing_remarks]',amount_paid='$data[amount_paid]',"
	    . "service_charge='$data[service_charge]', service_charge_collected_by='$data[service_charge_collected_by]',"
	    . "additional_service_charge='$data[additional_service_charge]',internal_status='$data[internal_status]', "
	    . "additional_service_charge_collected_by='$data[additional_service_charge_collected_by]', "
	    . "parts_cost='$data[parts_cost]', parts_cost_collected_by='$data[parts_cost_collected_by]',"
	    . "rating_stars='$data[rating_star]',rating_comments='$data[rating_comments]', "
	    . "vendor_rating_stars='$data[vendor_rating_stars]', vendor_rating_comments='$data[vendor_rating_comments]' "
	    . "where booking_id='$booking_id' and (current_status='Rescheduled' or current_status='Pending')";
	//echo "<pre>";print_r($sql);exit;
	$query = $this->db->query($sql);
	return $query;
    }

    //Schedule bookings given by Partner like Snapdeal
    function schedule_booking($booking_id, $data) {
	$states = array('FollowUp');
	$this->db->where(array('booking_id' => $booking_id));
	$this->db->where_in('current_status', $states);
	$this->db->update('booking_details', $data);
    }

    function reschedule_booking($booking_id, $data) {
	$states = array('Pending', 'Rescheduled');
	$this->db->where(array('booking_id' => $booking_id));
	$this->db->where_in('current_status', $states);
	$this->db->update('booking_details', $data);

//	$query = $this->db->query("Update booking_details set current_status='Rescheduled',
//	    internal_status='Rescheduled',
//	update_date='$data[update_date]',booking_date='$data[booking_date]',
//	booking_timeslot='$data[booking_timeslot]'
//	where booking_id='$booking_id'
//	and (current_status='Pending' or current_status='Rescheduled')");
//
//        return $query;
    }

    function service_name($service_id) {

	$sql = "Select services from services where id='$service_id'";
	$query = $this->db->query($sql);
	return $query->result_array();
    }

    function user_details($user_id) {

	$sql = "Select home_address from users where user_id='$user_id'";
	$query = $this->db->query($sql);

	return $query->result_array();
    }

    //Please don't delete this function, as if now it is not been used anywhere. But can effect somewhere
    //so after some time we can remove this
    /**
     *  @desc : All bookings history for a particular user
     *  @param : user_id
     *  @return : array(userdetails,servicename and bookingdetails)
     */
    /*    function booking_history_by_user_id($user_id) {

      $sql = "Select services.services,"
      . "users.name,users.user_email,users.user_id,users.phone_number,users.home_address,"
      . "booking_details.* "
      . "from booking_details,users,services where "
      . "users.user_id='$user_id' and "
      . "booking_details.user_id=users.user_id and "
      . "services.id=booking_details.service_id";

      $query = $this->db->query($sql);

      return $query->result_array();
      }
     */

    /**
     *  @desc : for selecting particular booking details to be sent through email
     *  @param : $booking_id
     *  @return : array(userdetails,servicename and bookingdetails)
     */
    function booking_history_by_booking_id($booking_id, $join = "") {

	/*
	  $sql = "Select services.services, users.*, booking_details.*"
	  . "from booking_details, users, services "
	  . "where booking_details.booking_id='$booking_id' and "
	  . "booking_details.user_id = users.user_id and services.id = booking_details.service_id";
	 */
	$service_centre = "";
	$condition = "";
	$service_center_name = "";
	if ($join != "") {
	    $service_center_name = ",service_centres.name as vendor_name, service_centres.district ";
	    $service_centre = ", service_centres ";
	    $condition = " and booking_details.assigned_vendor_id =  service_centres.id";
	}

	$sql = "Select services.services, users.*, booking_details.*, appliance_details.description  " . $service_center_name
	    . "from booking_details, users, services, appliance_details " . $service_centre
	    . "where booking_details.booking_id='$booking_id' and "
	    . "booking_details.user_id = users.user_id and "
	    . "services.id = booking_details.service_id and "
	    . "booking_details.appliance_id = appliance_details.id " . $condition;

	$query = $this->db->query($sql);

	return $query->result_array();
    }

    function selectservicecentre($booking_id) {
	$query = $this->db->query("SELECT booking_details.assigned_vendor_id,
            service_centres.name as service_centre_name, service_centres.primary_contact_name,
            service_centres.primary_contact_email, service_centres.owner_email,
            service_centres.primary_contact_phone_1
            from service_centres,booking_details
            where booking_details.booking_id='$booking_id'
            and booking_details.assigned_vendor_id=service_centres.id");

	return $query->result_array();
    }

    function getBrandForService($service_id) {
	$sql = "Select  brand_name from appliance_brands where service_id='$service_id'";
	$query = $this->db->query($sql);
	return $query->result_array();
    }

    function getCategoryForService($service_id) {
	$sql = "Select distinct category from service_centre_charges where service_id=
                '$service_id' and active='1'";
	$query = $this->db->query($sql);
	return $query->result_array();
    }

    function getCapacityForCategory($service_id, $category) {
	//echo $category;
	$sql = "Select distinct capacity from service_centre_charges where service_id='$service_id'
                and category='$category' and active='1'";

	$query = $this->db->query($sql);

	return $query->result_array();
    }

    function getCapacityForAppliance($service_id) {
	//echo $category;
	$sql = "Select distinct capacity from service_centre_charges where service_id='$service_id' and active='1'";

	$query = $this->db->query($sql);

	return $query->result_array();
    }

    function getPricesForCategoryCapacity($service_id, $category, $capacity) {
	if ($capacity != "NULL") {
	    $sql = "Select service_category,total_charges from service_centre_charges
            where service_id='$service_id' and category='$category' and capacity='$capacity'
            and active='1'";
	} else {
	    $sql = "Select service_category,total_charges from service_centre_charges
              where service_id='$service_id' and category='$category' and active='1'";
	}
	$query = $this->db->query($sql);

	return $query->result_array();
    }

    function select_service_center() {
	$query = $this->db->query("Select id, non_working_days, primary_contact_email, owner_email, name
                            from service_centres
                            where active=1");
	return $query->result();
    }

    function pendingbookings() {
	$sql = "Select services.services, "
	    . "users.name, users.phone_number,"
	    . "booking_details.user_id, booking_details.service_id, booking_details.booking_id, "
	    . "booking_details.booking_date, booking_details.booking_timeslot, booking_details.appliance_brand,"
	    . "booking_details.appliance_category, booking_details.booking_address, booking_details.booking_pincode "
	    . "from booking_details, users, services "
	    . "where booking_details.user_id = users.user_id and "
	    . "services.id = booking_details.service_id and "
	    . "current_status IN ('Pending', 'Rescheduled') and "
	    . "assigned_vendor_id is NULL";
	$query = $this->db->query($sql);

	$temp = $query->result_array();

	usort($temp, array($this, 'date_compare_assign_pending_bookings'));

	//return sorted array
	return $temp;
    }

    function assign_booking($booking_id, $service_center) {
	$sql = "Update booking_details set assigned_vendor_id='$service_center' where booking_id='$booking_id'";
	$query = $this->db->query($sql);

	return $query;
    }

    function set_mail_to_vendor($booking_id) {
	$query = $this->db->query("UPDATE booking_details set mail_to_vendor= 1 where booking_id
                ='$booking_id'");
    }

    function set_mail_to_vendor_flag_to_zero($booking_id) {
	$query = $this->db->query("UPDATE booking_details set mail_to_vendor= 0 where booking_id
                ='$booking_id'");
    }

    //TODO: Merge with update_booking_details function
    function update_booking($booking_id, $data) {
	$this->db->where(array("booking_id" => $booking_id));
	$this->db->update("booking_details", $data);

//	$sql = "Update booking_details set rating_stars='$data[rating_star]', rating_comments='$data[rating_comments]', "
//            . "vendor_rating_stars='$data[vendor_rating_star]', vendor_rating_comments='$data[vendor_rating_comments]' "
//            . "where booking_id='$booking_id'";
//
//        $query = $this->db->query($sql);
//        return $query;
    }

    function vendor_rating($booking_id, $data) {
	$sql = "UPDATE booking_details set vendor_rating_stars='$data[vendor_rating_star]',"
	    . "vendor_rating_comments='$data[vendor_rating_comments]' where booking_id='$booking_id'";
	$query = $this->db->query($sql);
	return $query;
    }

    function get_unit_details($booking_id) {
	$sql = "Select * from booking_unit_details where booking_id='$booking_id'";

	$query = $this->db->query($sql);

	return $query->result_array();
    }

    function find_followup_users() {
	$sql = "Select * from booking_details where type='FollowUp'";
    }

    /**
     *  @desc : This function is to update booking details
     *  @param : old booking id, new booking details
     *  @return : void
     */
    function update_booking_details($booking_id, $booking) {
	// remove some keys in booking array to while upade booking details table
	unset($booking['unit_id']);
	unset($booking['purchase_year']);
	unset($booking['appliance_tag']);
	unset($booking['model_number']);
	unset($booking['current_booking_date']);
	unset($booking['current_booking_timeslot']);
	unset($booking['new_booking_date']);
	unset($booking['new_booking_timeslot']);
	//..............................................................................
	$this->db->where("booking_id", $booking_id);
	$this->db->update('booking_details', $booking);

	return 1;
    }

    /**
     *  @desc : This function is to update unit booking details
     *  @param : old booking id, new booking details
     *  @return : void
     */
    function update_booking_unit_details($booking_id, $booking) {
	$sql = "Update booking_unit_details set booking_id='$booking[booking_id]', "
	    . "appliance_brand='$booking[appliance_brand]', "
	    . "appliance_category='$booking[appliance_category]', "
	    . "appliance_capacity='$booking[appliance_capacity]', "
	    . "model_number='$booking[model_number]', "
	    . "total_price='$booking[total_price]',"
	    . "price_tags = '$booking[items_selected]',"
	    . "purchase_year='$booking[purchase_year]',"
	    . "appliance_tag = '$booking[appliance_tag]'"
	    . "where booking_id='$booking_id'";

	$query = $this->db->query($sql);
    }

    /**
     *  @desc : This function is to cancel query
     *  @param : booking id, booking details
     *  @return : void
     */
    function cancel_followup($booking_id, $booking) {
	$this->db->where(array("booking_id" => $booking_id));
	$this->db->update('booking_details', $booking);

//	$sql = "Update booking_details set current_status='$booking[current_status]', "
//            . "cancellation_reason='$booking[cancellation_reason]', "
//            . "closing_remarks='$booking[closing_remarks]', "
//            . "internal_status='$booking[internal_status]' "
//            . "where booking_id='$booking_id'";
//
//        $query = $this->db->query($sql);
    }

    function jobcard($booking_id) {
	$sql = "Select booking_jobcard_filename from booking_details where booking_id=$booking_id
                and booking_jobcard_filename is NULL";
    }

    function getApplianceCountByUser($user_id) {
	//log_message('info', __METHOD__ . "=> User ID: " . $user_id);

	$this->db->where(array('user_id' => $user_id, 'is_active' => '1'));
	$this->db->from("appliance_details");

	$result = $this->db->count_all_results();

	//log_message('info', __METHOD__ . " -> Result: " . $result);

	return $result;
    }

    function getApplianceById($appliance_id) {
	//log_message('info', __METHOD__ . "=> User ID: " . $user_id);
	$this->db->where(array('id' => $appliance_id));
	$this->db->from("appliance_details");
	$query = $this->db->get();
	return $query->result_array();
	//$result = $this->db->count_all_results();
	//log_message('info', __METHOD__ . " -> Result: " . $result);
	//return $result;
    }

    function addSampleAppliances($user_id, $count) {
	//log_message('info', "Entering: " . __METHOD__);

	$sql1 = "SELECT * FROM sample_appliances";
	$query = $this->db->query($sql1);

	$appl = $query->result_array();

	for ($i = 0; $i < $count; $i++) {
	    $appl[$i]['user_id'] = $user_id;
	    //log_message('info', "Sample Appl: " . print_r($appl, TRUE));

	    $sql2 = "INSERT INTO appliance_details "
		. "(`service_id`, `brand`, `category`, `capacity`, "
		. "`model_number`, `tag`, `purchase_month`, `purchase_year`, `rating`, `user_id`)"
		. "VALUES (?,?,?,?,?, ?,?,?,?,?)";

	    $this->db->query($sql2, $appl[$i]);

//            $result = (bool) ($this->db->affected_rows() > 0);
	    //log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);
	}
    }

    function addNewApplianceBrand($service_id, $newbrand) {
	$sql = "INSERT into appliance_brands(service_id,brand_name) values('$service_id','$newbrand')";
	$query = $this->db->query($sql);
    }

    /*
      function edit_query($booking_id, $data) {
      $sql = "UPDATE booking_details set booking_date='$data[booking_date]',booking_timeslot=
      '$data[booking_timeslot]',query_remarks='$data[query_remarks]' where booking_id=
      '$booking_id'";
      $query = $this->db->query($sql);
      }
     *
     */

    /**
     *  @desc : This function is to edit completed booking
     *  @param : booking id, booking details
     *  @return : void
     */
    function edit_completed_booking($booking_id, $data) {
	$sql = "UPDATE booking_details set service_charge='$data[service_charge]',
    service_charge_collected_by='$data[service_charge_collected_by]',
    additional_service_charge='$data[additional_service_charge]',
   additional_service_charge_collected_by='$data[additional_service_charge_collected_by]',
   parts_cost='$data[parts_cost]', parts_cost_collected_by='$data[parts_cost_collected_by]',
   closing_remarks='$data[closing_remarks]', booking_remarks='$data[booking_remarks]',
            amount_paid='$data[amount_paid]' where booking_id='$booking_id'";

	$query = $this->db->query($sql);
    }

    function get_appliance_details($id) {
	$sql = "SELECT * from appliance_details WHERE id='$id'";
	$query = $this->db->query($sql);
	return $query->result_array();
    }

    function get_user_details($user_id) {
	$sql = "SELECT * from users WHERE user_id='$user_id'";
	$query = $this->db->query($sql);
	return $query->result_array();
    }

    /** @description* get all service from database
     *  @return : array (service)
     */
    function getServiceId($service_name) {
	$sql = "SELECT * FROM services WHERE services='$service_name'";
	$query = $this->db->query($sql);

	$services = $query->result_array();
	return $services[0]['id'];
    }

    function select_booking_source() {
	$query = $this->db->query("SELECT source, code FROM bookings_sources");
	return $query->result();
    }

    function get_booking_source($source_code) {
	$query = $this->db->query("SELECT source FROM bookings_sources WHERE code='$source_code'");
	return $query->result_array();
    }

    function insert_sd_lead($details) {
	$this->db->insert('snapdeal_leads', $details);

	return $this->db->insert_id();
    }

    function get_sd_lead($id) {
	$query = $this->db->query("SELECT * FROM snapdeal_leads WHERE id='$id'");
	$results = $query->result_array();

	return $results[0];
    }

    function get_sd_unassigned_bookings() {
	$query = $this->db->query("SELECT * FROM snapdeal_leads WHERE Status_by_247around='NewLead'");
	return $query->result_array();
    }

    function get_all_sd_bookings() {
	$query = $this->db->query("SELECT * FROM snapdeal_leads ORDER BY create_date DESC");
	return $query->result_array();
    }

    function update_sd_lead($array_where, $array_data) {
	$this->db->where($array_where);
	$this->db->update("snapdeal_leads", $array_data);
    }

    function check_sd_lead_exists_by_order_id($sub_order_id) {
	$this->db->where(array("Sub_Order_ID" => $sub_order_id));
	$query = $this->db->get('snapdeal_leads');

	if (count($query->result_array()) > 0)
	    return TRUE;
	else
	    return FALSE;
    }

    function check_sd_lead_exists_by_booking_id($booking_id) {
	$this->db->where(array("CRM_Remarks_SR_No" => $booking_id));
	$query = $this->db->get('snapdeal_leads');

	if (count($query->result_array()) > 0)
	    return TRUE;
	else
	    return FALSE;
    }

    /**
     *  @desc : Save(Insert) mail sent to vendor regarding bookings
     *  @param : Email details
     *  @return : void
     */
    function save_vendor_email($details) {
	$this->db->insert('vendor_mail_details', $details);
    }

    /**
     *  @desc : Get all mails sent to vendor regarding specific booking
     *  @param : booking_id
     *  @return : array(mail details)
     */
    function get_vendor_mails($booking_id) {
	$sql = "SELECT * from vendor_mail_details where booking_id='$booking_id'";
	$query = $this->db->query($sql);
	return $query->result_array();
    }

    /**
     *  @desc : Get last mail's details sent to vendor regarding specific booking
     *  @param : booking_id
     *  @return : array(last email details)
     */
    function get_last_vendor_mail($booking_id) {
	$sql = "SELECT * FROM vendor_mail_details WHERE booking_id = '$booking_id' order by create_date DESC LIMIT 1";
	$query = $this->db->query($sql);
	return $query->result_array();
    }

    /**
     *  @desc : Count the no of mails sent for a particular booking
     *  @param : booking_id
     *  @return : number of mails sent to paticular booking
     */
    function count_no_of_email_sent($booking_id) {
	$sql = "SELECT count(*) from vendor_mail_details where type like 'Reminder%' && booking_id='$booking_id'";
	$result = $this->db->query($sql);
	return $result;
    }

    /**
     *  @desc : Function to get pending queries according to pagination.
     *          Queries which have booking date of future are not shown. Queries with
     *          empty booking dates are shown.
     *  @param : start and limit for the query
     *  @return : array(specific no of pending query detils)
     */
    function get_pending_queries($limit, $start, $booking_id = "") {
	$where = "";

	if ($booking_id != "") {
	    $where .= "AND `booking_details`.`booking_id` = '$booking_id'";
	} else {
	    $where .= "AND (DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= 0 OR
	    booking_details.booking_date='')";
	}

	//TODO: Use standard SQL here
	//order by STR_TO_DATE(booking_date,'%d-%m-%Y') desc
	$sql = "SELECT services.services,
            users.name as customername, users.phone_number,
            booking_details.*, service_centres.name as service_centre_name,
            service_centres.primary_contact_name,service_centres.primary_contact_phone_1, partner_leads.OrderID
            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
            LEFT JOIN `partner_leads` ON `booking_details`.`booking_id` = `partner_leads`.`247aroundBookingID`
            WHERE `booking_id` LIKE '%Q-%' $where AND
	    (booking_details.current_status='FollowUp')
	    order by CASE booking_date
			WHEN '' THEN 'a'
			ELSE 'b'
		    END, STR_TO_DATE(booking_date,'%d-%m-%Y') desc; ";

	$query = $this->db->query($sql);

	$temp = $query->result_array();

	//usort($temp, array($this, 'date_compare_queries'));

	$data = $this->searchPincodeAvailable($temp);
	//return slice of the sorted array
	if ($limit != -1) {
	    return array_slice($data, $start, $limit);
	} else {
	    return $data;
	}
    }

    /**
     * @desc : In this function, we will pass Array and search active pincode and vendor.
     * If pincode available then insert vendor name in the same key.
     * @param : Array
     * @return : Array
     */
    function searchPincodeAvailable($temp) {
	foreach ($temp as $key => $value) {
	    $this->db->distinct();
	    $this->db->select('Vendor_ID, Vendor_Name');
	    $this->db->where('vendor_pincode_mapping.Appliance_ID', $value['service_id']);
	    $this->db->where('vendor_pincode_mapping.Pincode', $value['booking_pincode']);
	    $this->db->where('vendor_pincode_mapping.active', "1");
	    $this->db->from('vendor_pincode_mapping');

	    $this->db->join('service_centres', 'service_centres.id = vendor_pincode_mapping.Vendor_ID');

	    $this->db->where('service_centres.active', "1");
	    $data = $this->db->get();
	    if ($data->num_rows() > 0) {
		$temp[$key]['vendor_status'] = $data->result_array();
	    } else {
		$temp[$key]['vendor_status'] = "Vendor Not Available";
	    }
	}

	return $temp;
    }

//    /**
//     *  @desc : Function to get all pending queries
//     *  @param : void
//     *  @return : array(specific no of pending query detils)
//     */
//    //
//    function view_all_pending_queries() {
//	$sql = "SELECT services.services,
//            users.name as customername, users.phone_number,
//            booking_details.*, service_centres.name as service_centre_name,
//            service_centres.primary_contact_name,service_centres.primary_contact_phone_1
//            from booking_details
//            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
//            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
//            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
//            WHERE `booking_id` LIKE '%Q-%' AND
//	    (booking_details.current_status='FollowUp')";
//
//	$query = $this->db->query($sql);
//
//	$temp = $query->result_array();
//
//	usort($temp, array($this, 'date_compare_queries'));
//
//	return $temp;
//    }
//Function to get cancelled queries according to pagination
    function get_cancelled_queries($limit, $start) {
	$sql = "SELECT services.services,
            users.name as customername, users.phone_number,
            booking_details.*, service_centres.name as service_centre_name,
            service_centres.primary_contact_name,service_centres.primary_contact_phone_1
            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
            WHERE `booking_id` LIKE '%Q-%' AND
            (booking_details.current_status='Cancelled')";

	$query = $this->db->query($sql);

	$temp = $query->result();

	usort($temp, array($this, 'date_compare_queries'));

	//return slice of the sorted array
	return array_slice($temp, $start, $limit);
    }

    //Function to view all the cancelled queries
    function view_all_cancelled_queries() {
	$sql = "SELECT services.services,
            users.name as customername, users.phone_number,
            booking_details.*, service_centres.name as service_centre_name,
            service_centres.primary_contact_name,service_centres.primary_contact_phone_1
            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
            WHERE `booking_id` LIKE '%Q-%' AND
            (booking_details.current_status='Cancelled')";

	$query = $this->db->query($sql);

	$temp = $query->result();

	usort($temp, array($this, 'date_compare_queries'));

	return $temp;
    }

    //Function to add single appliance while converting query to booking
    function addsingleappliance($booking) {
	$appliance_detail = array("user_id" => $booking['user_id'],
	    "service_id" => $booking['service_id'],
	    "brand" => $booking['appliance_brand'],
	    "category" => $booking['appliance_category'],
	    "capacity" => $booking['appliance_capacity'],
	    "model_number" => $booking['model_number'],
	    "purchase_year" => $booking['purchase_year'],
	    "tag" => $booking['appliance_tag'],
	    "last_service_date" => date('Y-m-d H:i:s'));
	$this->db->insert('appliance_details', $appliance_detail);
	$id = $this->db->insert_id();

	$sql = "SELECT * FROM appliance_details WHERE id = $id";
	$query = $this->db->query($sql);
	return $query->result_array();
    }

    //Function to add single unit details while working with query
    function add_single_unit_details($booking) {
	$unit_detail = array("booking_id" => $booking['booking_id'],
	    "appliance_brand" => $booking['appliance_brand'],
	    "appliance_category" => $booking['appliance_category'],
	    "appliance_capacity" => $booking['appliance_capacity'],
	    "model_number" => $booking['model_number'],
	    "price_tags" => $booking['items_selected'],
	    "purchase_year" => $booking['purchase_year'],
	    "total_price" => $booking['total_price'],
	    "appliance_tag" => $booking['appliance_tag']);

	$this->db->insert('booking_unit_details', $unit_detail);
    }

    //Function to update single appliance details
    function update_appliance_details($booking) {
	$sql = "Update appliance_details set user_id='$booking[user_id]',"
	    . "service_id='$booking[service_id]',brand='$booking[appliance_brand]',"
	    . "category='$booking[appliance_category]',
                capacity='$booking[appliance_capacity]',"
	    . "model_number='$booking[model_number]',tag='$booking[appliance_tag]',"
	    . "purchase_year='$booking[purchase_year]' where id='$booking[appliance_id]'";

	$query = $this->db->query($sql);

	return $query;
    }

    /** @description : Function to search bookings with booking id from find user page
     *  @param : booking id
     *  @return : array(matching bookings)
     */
    function search_bookings_by_booking_id($booking_id) {
	$query = $this->db->query("Select services.services,
            users.name as customername, users.phone_number,
            booking_details.*, service_centres.name as service_centre_name,
            service_centres.primary_contact_name,service_centres.primary_contact_phone_1
            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id`
            = `service_centres`.`id` WHERE booking_id like '%$booking_id%'"
	);

	return $query->result();
    }

    /**
     *  @desc : This function is to get internal status from database
     *  @param : void
     *  @return : all internal status present in database
     */
    function get_internal_status($page) {
	$this->db->select('*');
	$this->db->from("internal_status");
	$this->db->where(array("page" => $page, "active" => '1'));
	$query = $this->db->get();
	return $query->result();
    }

    //Find potential SCs for an Appliance in a Pincode
    function find_sc_by_pincode_and_appliance($appliance, $pincode) {
	$query = $this->db->query("SELECT DISTINCT(`service_centres`.`id`) FROM (`vendor_pincode_mapping`)
	    JOIN `service_centres` ON `service_centres`.`id` = `vendor_pincode_mapping`.`Vendor_ID`
    		WHERE `Appliance_ID` = '$appliance' AND `vendor_pincode_mapping`.`Pincode` = '$pincode'
	    AND `vendor_pincode_mapping`.`active` = 1
	    AND `service_centres`.`active` = '1'");

	$service_centre_ids = $query->result_array();

	$service_centres = array();

	if (count($service_centre_ids) > 0) {
	    //Service centres exist in this pincode for this appliance
	    foreach ($service_centre_ids as $sc) {
		$this->db->select("id, name");
		$this->db->from("service_centres");
		$this->db->where(array("id" => $sc['id']));
		$query2 = $this->db->get();
		array_push($service_centres, $query2->result_array()[0]);
	    }
	} else {
	    //No service centre found, return all SCs as of now
	    $this->db->select("id, name");
	    $this->db->from("service_centres");
	    $this->db->where(array("active" => '1'));
	    $query2 = $this->db->get();
	    foreach ($query2->result_array() as $r) {
		array_push($service_centres, $r);
	    }
	}

	return $service_centres;
    }

    /**
     *  @desc : This function is used to change status in Booking Details Table
     *  @param : String (Booking Id)
     *  @return : true
     */
    function change_booking_status($booking_id) {
	$status = array("current_status" => "FollowUp",
	    "internal_status" => "FollowUp",
	    "cancellation_reason" => NULL);

	$this->db->where("booking_id", $booking_id);
	$this->db->update("booking_details", $status);
	return true;
    }

    /**
     * Get product Type means description about booking from both table snapdeal_leads and partner_leads.
     * @param: bookinf id
     * @return : string
     */
    function getdescription_about_booking($booking_id) {

	$query = $this->db->query("SELECT Sub_Order_ID as order_id, Product_Type as description from snapdeal_leads where CRM_Remarks_SR_No = '$booking_id'

                              UNION

                              SELECT OrderID as order_id, ProductType from partner_leads as description where 247aroundBookingID = '$booking_id' ");

	return $query->result_array();
    }

    /**
     *  @desc : This function is used to get partner code to map pricing table
     *  @param : String (partner code)
     *  @return : true
     */
    function get_price_mapping_partner_code($partner_code) {
	$this->db->select('price_mapping_code');
	$this->db->where('code', $partner_code);
	$query = $this->db->get('bookings_sources');
	if ($query->num_rows() > 0) {
	    $result = $query->result_array();
	    return $result[0]['price_mapping_code'];
	} else {
	    return "";
	}
    }

    /**
     * @desc; this function is used to get services charges to be filled by service centers
     * @param: booking id
     * @return: Array()
     */
    function getbooking_charges($booking_id = "") {
	$array = array('current_status !=' => "Completed");
	$this->db->select('*');
	if ($booking_id != "")
	    $this->db->where('booking_id', $booking_id);
	$this->db->where($array);
	$query = $this->db->get('service_center_booking_action');
	return $query->result_array();
    }

    function get_booking_for_review() {
	$charges = $this->getbooking_charges();
	foreach ($charges as $key => $value) {
	    $charges[$key]['service_centres'] = $this->vendor_model->getVendor($value['booking_id']);
	    $charges[$key]['query2'] = $this->get_unit_details($value['booking_id']);
	    $charges[$key]['booking'] = $this->booking_history_by_booking_id($value['booking_id']);
	}

	return $charges;
    }

    function insert_outbound_call_log($details) {
	$this->db->insert('agent_outbound_call_log', $details);
    }

}
