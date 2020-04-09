<?php

class Web_booking_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
	parent::__Construct();

	//$this->db_location = $this->load->database('default1', TRUE, TRUE);
	$this->db = $this->load->database('default', TRUE, TRUE);
    }

    function addappliancedetails($booking) {
	$units = $booking['quantity'];
	if ($units == 1) {
	    $appliance_detail = array("user_id" => $booking['user_id'],
		"service_id" => $booking['service_id'],
		"brand" => $booking['appliance_brand1'],
		"category" => $booking['appliance_category1'],
		"capacity" => $booking['appliance_capacity1'],
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
		"purchase_year" => $booking['purchase_year1'],
		"tag" => $booking['appliance_tags1'],
		"last_service_date" => date('Y-m-d H:i:s'));
	    $this->db->insert('appliance_details', $appliance_detail);
	    $appliance_detail = array("user_id" => $booking['user_id'],
		"service_id" => $booking['service_id'],
		"brand" => $booking['appliance_brand2'],
		"category" => $booking['appliance_category2'],
		"capacity" => $booking['appliance_capacity2'],
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
		"purchase_year" => $booking['purchase_year1'],
		"tag" => $booking['appliance_tags1'],
		"last_service_date" => date('Y-m-d H:i:s'));
	    $this->db->insert('appliance_details', $appliance_detail);
	    $appliance_detail = array("user_id" => $booking['user_id'],
		"service_id" => $booking['service_id'],
		"brand" => $booking['appliance_brand2'],
		"category" => $booking['appliance_category2'],
		"capacity" => $booking['appliance_capacity2'],
		"purchase_year" => $booking['purchase_year2'],
		"tag" => $booking['appliance_tags2'],
		"last_service_date" => date('Y-m-d H:i:s'));
	    $this->db->insert('appliance_details', $appliance_detail);
	    $appliance_detail = array("user_id" => $booking['user_id'],
		"service_id" => $booking['service_id'],
		"brand" => $booking['appliance_brand3'],
		"category" => $booking['appliance_category3'],
		"capacity" => $booking['appliance_capacity3'],
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
		"purchase_year" => $booking['purchase_year1'],
		"tag" => $booking['appliance_tags1'],
		"last_service_date" => date('Y-m-d H:i:s'));
	    $this->db->insert('appliance_details', $appliance_detail);
	    $appliance_detail = array("user_id" => $booking['user_id'],
		"service_id" => $booking['service_id'],
		"brand" => $booking['appliance_brand2'],
		"category" => $booking['appliance_category2'],
		"capacity" => $booking['appliance_capacity2'],
		"purchase_year" => $booking['purchase_year2'],
		"tag" => $booking['appliance_tags2'],
		"last_service_date" => date('Y-m-d H:i:s'));
	    $this->db->insert('appliance_details', $appliance_detail);
	    $appliance_detail = array("user_id" => $booking['user_id'],
		"service_id" => $booking['service_id'],
		"brand" => $booking['appliance_brand3'],
		"category" => $booking['appliance_category3'],
		"capacity" => $booking['appliance_capacity3'],
		"purchase_year" => $booking['purchase_year3'],
		"tag" => $booking['appliance_tags3'],
		"last_service_date" => date('Y-m-d H:i:s'));
	    $this->db->insert('appliance_details', $appliance_detail);
	    $appliance_detail = array("user_id" => $booking['user_id'],
		"service_id" => $booking['service_id'],
		"brand" => $booking['appliance_brand4'],
		"category" => $booking['appliance_category4'],
		"capacity" => $booking['appliance_capacity4'],
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
	    "purchase_date" => $booking['purchase_date'],
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
	    "price_tags" => $booking['items_selected'],
	    "purchase_date" => $booking['purchase_date'],
	    "total_price" => $booking['total_price'],
	    "appliance_tag" => $booking['appliance_tags']);
	$this->db->insert('booking_unit_details', $unit_detail);
    }

    function addunitdetails($booking){
        log_message ('info', __METHOD__ . "booking unit details data". print_r($booking, true));
        $this->db->insert('booking_unit_details', $booking);
        return $this->db->insert_id();
    }

    /**
     * @desc: this is used to add appliances
     * @param: Array(Appliances details)
     * @return: appliance id
     */
    function addappliance($appliance_detail){
        log_message ('info', __METHOD__ . "appliance_detail data". print_r($appliance_detail, true));
        $this->db->insert('appliance_details', $appliance_detail);
        return $this->db->insert_id();
    }

    /** @description:* add booking
     *  @param : booking
     *  @return : array (booking)
     */

    function addbooking($booking){
        log_message('info', __METHOD__ . "booking details data: " . print_r($booking, true));
	$this->db->insert('booking_details', $booking);
        return $this->db->insert_id();
    }

    function selectservice() {
        $this->db->select('id,services');
        $this->db->where('isBookingActive','1');
		$this->db->order_by('services');
        $query = $this->db->get('services');
	return $query->result();
    }

    function selectservicebyid($service_id) {
        $this->db->select('services');
        $this->db->where('id',$service_id);
        $query = $this->db->get('services');
	return $query->result();
	//return $query->result_array();
    }

    function selectidbyservice($service) {
        $this->db->select('id');
        $this->db->where('services',$service);
        $query = $this->db->get('services');
	return $query->result_array();
	//return $query->result_array();
    }

    function selectbrand() {
        $this->db->distinct();
        $this->db->select('brand_name');
        $this->db->order_by('brand_name');
        $query= $this->db->get('appliance_brands');
	return $query->result();
    }

    function selectcategory() {
        $this->db->distinct();
        $this->db->select('category');
        $query= $this->db->get('service_centre_charges');
	return $query->result();
    }

    function selectcapacity() {
        $this->db->distinct();
        $this->db->select('capacity');
        $query= $this->db->get('service_centre_charges');
	return $query->result();
    }
    
    function get_partner_logo(){
        $this->db->select('partner_logo, alt_text');
        $this->db->where('partner_logo !=' , 'Null');
        $query = $this->db->get('partner_brand_logo');
        return $query->result_array();
    }

    function finduser($phone) {
	$query = $this->db->query("Select user_id,name,user_email from users where phone_number='$phone' AND is_verified='1'");
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

    public function viewpendingbooking() {
	$query = $this->db->query("Select services.services,
            users.name as customername, users.phone_number,
            booking_details.*, service_centres.name as service_centre_name,
            service_centres.primary_contact_name,service_centres.primary_contact_phone_1
            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id` WHERE (booking_details.current_status='Pending' OR booking_details.current_status='Rescheduled')"
	);
	return $query->result();
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
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id` WHERE (booking_details.current_status='Pending' OR booking_details.current_status='Rescheduled')
            ORDER BY create_date DESC
            LIMIT $start,$limit"
	);

	return $query->result();
    }

    function view_completed_booking($limit, $start) {

	$query = $this->db->query("Select services.services,
            users.name as customername, users.phone_number,
            booking_details.*, service_centres.name as service_centre_name,
           service_centres.primary_contact_name,service_centres.primary_contact_phone_1
            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
            WHERE (booking_details.current_status = 'Completed' OR booking_details.current_status = 'Cancelled')
            ORDER BY create_date DESC LIMIT $start,$limit"
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

    function date_sorted_booking($limit, $start) {
	$query = $this->db->query("Select services.services,
            users.name as customername, users.phone_number,
            booking_details.*, service_centres.name as service_centre_name,
            service_centres.primary_contact_name,service_centres.primary_contact_phone_1
            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id` WHERE
            UNIX_TIMESTAMP( STR_TO_DATE( `booking_date`, '%d-%m-%Y' ) ) >= UNIX_TIMESTAMP('01-01-2010') AND
            `booking_id` NOT LIKE '%Q-%' AND
            (booking_details.current_status='Pending' OR booking_details.current_status='Rescheduled')
            ORDER BY booking_date DESC, create_date DESC
            LIMIT $start,$limit"
	);


	return $query->result();
    }

    function ab($limit, $start) {
	$this->db->limit($limit, $start);
	$query = $this->db->get("booking_details");

	return $query->result();
    }

    function service_center_details() {
	$query = $this->db->query("Select service_centres.primary_contact_name,service_centres.primary_contact_phone_1 from service_centres,booking_details where booking_details.assigned_vendor_id=service_centres.id ");

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

    public function total_pending_booking() {
	$this->db->where("current_status = 'Pending' OR current_status = 'Rescheduled' OR current_status='FollowUp'");
	$result = $this->db->count_all_results("booking_details");
	return $result;
    }

    public function total_completed_booking() {
	$this->db->where("current_status = 'Completed' OR current_status = 'Cancelled'");
	$result = $this->db->count_all_results("booking_details");

	return $result;
    }

    public function total_pending_queries() {
	$this->db->where("type = 'Query' AND current_status = 'FollowUp'");
	$result = $this->db->count_all_results("booking_details");
	return $result;
    }

    public function total_cancelled_queries() {
	$this->db->where("type = 'Query' AND current_status = 'Cancelled'");
	$result = $this->db->count_all_results("booking_details");
	return $result;
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
	$sql = "Update booking_details SET update_date='" . $data['update_date'] .
	    "', cancellation_reason='" . $data['cancellation_reason'] .
	    "', current_status='Cancelled', closing_remarks=' " . $data['closing_remarks'] .
	    "where booking_id='$booking_id' and " .
	    "(current_status='Pending' or current_status='Rescheduled')";

	$query = $this->db->query($sql);

	return $query;
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
	    . "additional_service_charge='$data[additional_service_charge]', "
	    . "additional_service_charge_collected_by='$data[additional_service_charge_collected_by]', "
	    . "parts_cost='$data[parts_cost]', parts_cost_collected_by='$data[parts_cost_collected_by]',"
	    . "rating_stars='$data[rating_star]',rating_comments='$data[rating_comments]', "
	    . "vendor_rating_stars='$data[vendor_rating_stars]', vendor_rating_comments='$data[vendor_rating_comments]' "
	    . "where booking_id='$booking_id' and (current_status='Rescheduled' or current_status='Pending')";
	//echo "<pre>";print_r($sql);exit;
	$query = $this->db->query($sql);
	return $query;
    }

    function reschedule_booking($booking_id, $data) {
	$query = $this->db->query("Update booking_details set current_status='Rescheduled',update_date='$data[update_date]',booking_date='$data[booking_date]',booking_timeslot='$data[booking_timeslot]' where booking_id='$booking_id' and (current_status='Pending' or current_status='Rescheduled')");

	return $query;
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

    function booking_history($user_id) {

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

    //desc:for selecting particular booking details to be sent through email

    function booking_history1($booking_id) {
	/*
	  $sql = "Select services.services,users.user_email,users.user_id,users.phone_number,users.home_address,"
	  . "users.name, booking_details.*, booking_unit_details.*"
	  . "from booking_details, users, services, booking_unit_details "
	  . "where booking_details.booking_id='$booking_id' and "
	  . "booking_unit_details.booking_id='$booking_id' and "
	  . "booking_details.user_id = users.user_id and services.id = booking_details.service_id";
	 *
	 */

	$sql = "Select services.services,users.user_email,users.user_id,users.phone_number,users.home_address,"
	    . "users.name, booking_details.* "
	    . "from booking_details, users, services "
	    . "where booking_details.booking_id='$booking_id' and "
	    . "booking_details.user_id = users.user_id and services.id = booking_details.service_id";

	$query = $this->db->query($sql);

	return $query->result_array();
    }

    function selectservicecentre($booking_id) {
	$query = $this->db->query("SELECT booking_details.assigned_vendor_id, service_centres.name as service_centre_name, service_centres.primary_contact_name, service_centres.primary_contact_email, service_centres.owner_email, service_centres.primary_contact_phone_1 from service_centres,booking_details where booking_details.booking_id='$booking_id' and booking_details.assigned_vendor_id=service_centres.id");
	return $query->result_array();
    }

    function getBrandForService($service_id) {
		$this->db->where(array('service_id' => $service_id));
    	$this->db->select('brand_name');
		$this->db->order_by('brand_name');
	    $query = $this->db->get('appliance_brands');
        return $query->result_array();
    }

    function getCategoryForService($service_id, $partner_id, $brand ="") {

        if($brand != ""){
          
           $this->db->where('brand',$brand);

        }
        $this->db->distinct();
        $this->db->select('category');
        $this->db->where('service_id',$service_id);
        $this->db->where('active','1');
        $this->db->where('check_box','1');
        $this->db->where('partner_id',$partner_id);
        $query = $this->db->get('service_centre_charges');
        
    	return $query->result_array();
    }

    function getCapacityForCategory($service_id, $category, $brand, $partner_id) {
       
        if($brand !=""){
            $this->db->where('brand', $brand);
        }
        
        $this->db->distinct();
        $this->db->select('capacity');
        $this->db->where('service_id', $service_id);
        $this->db->where('category', $category);
        $this->db->where('active', '1');
        $this->db->where('check_box', '1');
        $this->db->where('partner_id', $partner_id);
        $query = $this->db->get('service_centre_charges');
      
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
	    $sql = "Select service_category,total_charges from service_centre_charges where service_id='$service_id' and category='$category' and capacity='$capacity' and active='1'";
	} else {
	    $sql = "Select service_category,total_charges from service_centre_charges where service_id='$service_id' and category='$category' and active='1'";
	}
	$query = $this->db->query($sql);

	return $query->result_array();
    }

    function select_service_center() {
	$query = $this->db->query("Select id,name from service_centres where active=1");
	return $query->result();
    }

    function pendingbookings() {
	$sql = "Select services.services, "
	    . "users.name, users.phone_number,"
	    . "booking_details.user_id, booking_details.service_id, booking_details.booking_id, "
	    . "booking_details.booking_date, booking_details.booking_timeslot, booking_details.appliance_brand,"
	    . "booking_details.appliance_category, booking_details.booking_address "
	    . "from booking_details, users, services "
	    . "where booking_details.user_id = users.user_id and "
	    . "services.id = booking_details.service_id and "
	    . "current_status IN ('Pending', 'Rescheduled') and "
	    . "assigned_vendor_id is NULL";
	$query = $this->db->query($sql);

	return $query->result_array();
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

    function rate($booking_id, $data) {
	$sql = "Update booking_details set rating_stars='$data[rating_star]', rating_comments='$data[rating_comments]', "
	    . "vendor_rating_stars='$data[vendor_rating_star]', vendor_rating_comments='$data[vendor_rating_comments]' "
	    . "where booking_id='$booking_id'";

	$query = $this->db->query($sql);
	return $query;

	$query = $this->db->query($sql);
	return $query;
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

    function update_booking_details($booking_id, $booking) {
	$sql = "Update booking_details set booking_id = '$booking[booking_id]', "
	    . "booking_primary_contact_no = '$booking[booking_primary_contact_no]', "
	    . "booking_alternate_contact_no = '$booking[booking_alternate_contact_no]', "
	    . "booking_date = '$booking[booking_date]', booking_timeslot = '$booking[booking_timeslot]', "
	    . "booking_address = '$booking[booking_address]', booking_pincode = '$booking[booking_pincode]', "
	    . "booking_remarks = '$booking[booking_remarks]', appliance_id = '$booking[appliance_id]', "
	    . "query_remarks = '$booking[query_remarks]', "
	    . "total_price = '$booking[total_price]', amount_due = '$booking[amount_due]', "
	    . "current_status = '$booking[current_status]', type = '$booking[type]' "
	    . "WHERE booking_id = '$booking_id'";

	$query = $this->db->query($sql);

	return $query;
    }

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
	return $query;
    }

    function cancel_followup($booking_id, $booking) {
	$sql = "Update booking_details set current_status='$booking[current_status]', "
	    . "cancellation_reason='$booking[cancellation_reason]', "
	    . "closing_remarks='$booking[closing_remarks]' "
	    . "where booking_id='$booking_id'";

	$query = $this->db->query($sql);
	return $query;
    }

    function jobcard($booking_id) {
	$sql = "Select booking_jobcard_filename from booking_details where booking_id=$booking_id and booking_jobcard_filename is NULL";
    }

    function getApplianceCountByUser($user_id) {
	//log_message('info', __METHOD__ . "=> User ID: " . $user_id);

	$this->db->where(array('user_id' => $user_id, 'is_active' => '1'));
	$this->db->from("appliance_details");

	$result = $this->db->count_all_results();

	//log_message('info', __METHOD__ . " -> Result: " . $result);

	return $result;
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
		. "`model_number`, `tag`, `purchase_date`,  `rating`, `user_id`)"
		. "VALUES (?,?,?,?,?, ?,?,?,?)";

	    $this->db->query($sql2, $appl[$i]);

//            $result = (bool) ($this->db->affected_rows() > 0);
	    //log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);
	}
    }

    function addNewApplianceBrand($service_id, $newbrand) {
	$sql = "INSERT into appliance_brands(service_id,brand_name) values('$service_id','$newbrand')";
	$query = $this->db->query($sql);
    }

    function edit_query($booking_id, $data) {
	$sql = "UPDATE booking_details set booking_date='$data[booking_date]',booking_timeslot=
        '$data[booking_timeslot]',query_remarks='$data[query_remarks]' where booking_id=
        '$booking_id'";
	$query = $this->db->query($sql);
    }

    function edit_completed_booking($booking_id, $data) {
	$sql = "UPDATE booking_details set service_charge='$data[service_charge]',service_charge_collected_by='$data[service_charge_collected_by]', additional_service_charge='$data[additional_service_charge]',
   additional_service_charge_collected_by='$data[additional_service_charge_collected_by]', parts_cost='$data[parts_cost]', parts_cost_collected_by='$data[parts_cost_collected_by]', closing_remarks='$data[closing_remarks]', booking_remarks='$data[booking_remarks]', amount_paid='$data[amount_paid]' where booking_id=
        '$booking_id'";
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
	$query = $this->db->query("SELECT * FROM snapdeal_leads WHERE around_booking_status='NewLead'");
	return $query->result_array();
    }

    function get_all_sd_bookings() {
	$query = $this->db->query("SELECT * FROM snapdeal_leads ORDER BY create_date DESC");
	return $query->result_array();
    }

    function update_sd_lead($id, $booking_id, $status) {
	$this->db->query("UPDATE `snapdeal_leads` "
	    . "SET `around_booking_id`='$booking_id', `around_booking_status`='$status' "
	    . "WHERE `id`='$id'");
    }

    function update_sd_lead_status($booking_id, $status) {
	$this->db->query("UPDATE `snapdeal_leads` "
	    . "SET `around_booking_status`='$status' "
	    . "WHERE `around_booking_id`='$booking_id'");
    }

    //Save mail sent to vendor regarding bookings
    function save_vendor_email($details) {
	$this->db->insert('vendor_mail_details', $details);
    }

    //Get all mails sent to vendor regarding specific booking
    function get_vendor_mails($booking_id) {
	$sql = "SELECT * from vendor_mail_details where booking_id='$booking_id'";
	$query = $this->db->query($sql);
	return $query->result_array();
    }

    //Get all mails sent to vendor regarding specific booking
    function get_last_vendor_mail($booking_id) {
	$sql = "SELECT * FROM vendor_mail_details WHERE booking_id = '$booking_id' order by create_date DESC LIMIT 1";
	$query = $this->db->query($sql);
	return $query->result_array();
    }

    //Count the no of mails sent for a particular booking
    function count_no_of_email_sent($booking_id) {
	$sql = "SELECT count(*) from vendor_mail_details where type like 'Reminder%' && booking_id='$booking_id'";
	$result = $this->db->query($sql);
	return $result;
    }

    //Function to get all pending queries
    function get_all_pending_queries($limit, $start) {
	$sql = "SELECT services.services,
            users.name as customername, users.phone_number,
            booking_details.*, service_centres.name as service_centre_name,
            service_centres.primary_contact_name,service_centres.primary_contact_phone_1
            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
            WHERE
            UNIX_TIMESTAMP( STR_TO_DATE( `booking_date`, '%d-%m-%Y' ) ) >= UNIX_TIMESTAMP('01-01-2010') AND
            `booking_id` LIKE '%Q-%' AND
            (booking_details.current_status='FollowUp')
            ORDER BY booking_date DESC, create_date DESC
            LIMIT $start,$limit";

	$query = $this->db->query($sql);

	return $query->result();
    }

    //Function to get all cancelled queries
    function get_all_cancelled_queries($limit, $start) {
	$sql = "SELECT services.services,
            users.name as customername, users.phone_number,
            booking_details.*, service_centres.name as service_centre_name,
            service_centres.primary_contact_name,service_centres.primary_contact_phone_1
            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
            WHERE
            UNIX_TIMESTAMP( STR_TO_DATE( `booking_date`, '%d-%m-%Y' ) ) >= UNIX_TIMESTAMP('01-01-2010') AND
            `booking_id` LIKE '%Q-%' AND
            (booking_details.current_status='Cancelled')
            ORDER BY booking_date DESC, create_date DESC
            LIMIT $start,$limit";

	$query = $this->db->query($sql);

	return $query->result();
    }

    //Function to add single appliance while converting query to booking
    function addsingleappliance($booking) {
	$appliance_detail = array("user_id" => $booking['user_id'],
	    "service_id" => $booking['service_id'],
	    "brand" => $booking['appliance_brand'],
	    "category" => $booking['appliance_category'],
	    "capacity" => $booking['appliance_capacity'],
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
	    . "service_id='$booking[user_id]',brand='$booking[appliance_brand]',"
	    . "category='$booking[appliance_category]',
                capacity='$booking[appliance_capacity]',"
	    . "model_number='$booking[model_number]',tag='$booking[appliance_tag]',"
	    . "purchase_year='$booking[purchase_year]' where id='$booking[appliance_id]'";

	$query = $this->db->query($sql);

	return $query;
    }

    //Function to get all active cities to display on Main Page and Footer
    function get_active_city() {
	//log_message('info', __FUNCTION__);

	$this->db->distinct();
	$this->db->order_by('City');
	$this->db->select('vendor_pincode_mapping.City');
	$this->db->where('service_centres.active', 1);
	$this->db->from('service_centres');
	$this->db->join('vendor_pincode_mapping', 'vendor_pincode_mapping.Vendor_ID = service_centres.id');
	$this->db->order_by('City');
	$query = $this->db->get();

	//log_message('info', 'Count of Cities: ' . count($query->result_array()));

	return $query->result_array();
    }
    /**
     * get active city based on selected appliance
     */
    
    function get_appliance_active_city($appliance_id){
        //log_message('info', __FUNCTION__);
        
        $this->db->distinct();
	$this->db->select('vendor_pincode_mapping.City');
        $this->db->from('service_centres');
        $this->db->join('vendor_pincode_mapping', 'vendor_pincode_mapping.Vendor_ID = service_centres.id');
	$this->db->where('vendor_pincode_mapping.Appliance_ID', $appliance_id);
        $this->db->where('service_centres.active', 1);
        $this->db->order_by('City');
	
	$query = $this->db->get();
        return $query->result_array();
    }

    /**
     *  @desc : This function is to get all states.
     *
     *  All the distinct states of India in Ascending order
     *
     *  @param : $city
     *  @return : array of states
     */
    function getall_state($city = "") {
        $this->db->distinct();
        $this->db->select('state');
        if ($city != "") {
            $this->db->where('district', $city);
        }
        $this->db->order_by('state');
        $query = $this->db->get('india_pincode');

        return $query->result_array();
    }

    function update_booking($booking_id, $data){
    	$this->db->where('booking_id', $booking_id);
    	$this->db->update('booking_details', $data);

        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {

           return FALSE;
        }
    }

    /**
     * @desc: This method get flag to schedule booking from website(if flag is 1)
     * @param: booking id
     * @return : flag
     */
    function get_scheduling_flag($booking_id){
    	$this->db->select('*');
    	$this->db->where('booking_id', $booking_id);
    	$query =  $this->db->get('booking_scheduling_url');
    	if($query->num_rows > 0){
    		return $query->result_array()[0]['flag'];
    	}

    	return 0;

    }

    function update_flag($booking_id, $flag){
    	$this->db->where('booking_id', $booking_id);
    	$this->db->update('booking_scheduling_url', $flag);
    }
    /**
     * @desc: Insert booking data into web booking table
     * @param Array $data
     * @return string id
     */
    function insert_web_booking($data) {
	$this->db->insert('web_booking', $data);
	return $this->db->insert_id();
    }
    /**
     * @desc: This is used to verify otp number and update is_verified column to 1
     * @param String md5 $request_verification_code
     * @param String $otp_number
     * @return boolean
     */
    function verify_otp($code) {
	$this->db->select('*');
	$this->db->where('request_verification_code', $code['request_verification_code']);
	$this->db->where('otp_number', $code['otp_number']);
	$this->db->where('is_verified', '0');
	$query = $this->db->get('web_booking');
	if ($query->num_rows > 0) {
	    $result = $query->result_array();
	    $this->db->where('id', $result[0]['id']);
	    $this->db->update('web_booking', array('is_verified' => '1', 
                'pincode' => $code['pincode'], 'address' => $code['address'], 
                'booking_date'=> $code['booking_date'],
                'booking_remarks'=> $code['booking_remarks'],
                ));
	    return $result;
	} else {
	    return false;
	}
    }
    
     /**
     *  @desc : This function is to insert booking state changes.
     *
     *  @param : Array $details Booking state change details
     *  @return :id
     */
    function insert_booking_state_change($details) {
        $this->db->insert('booking_state_change', $details);

        return $this->db->insert_id();
    }
    
    
    function get_employee_details(){
        $this->db->distinct();
        $this->db->select('full_name, designation, image_link,linkedin_link');
        $this->db->where(array('active'=> '1','priority >'=>'0'));
        $this->db->order_by('priority,full_name');
        $query = $this->db->get('employee');
        return $query->result_array();
    }
    
    
    /**
     *  @desc : This function is used to get the counter value
     *
     *  @param : void
     *  @return :array
     */
    function get_counter_data(){
        
        $pincode_sql = "SELECT count(DISTINCT Pincode) as pincodes FROM `vendor_pincode_mapping`";
        $city_sql = "SELECT count(DISTINCT city) as city FROM `vendor_pincode_mapping`";
        //$rating_sql = "SELECT ROUND(AVG(rating_stars),1) as rating FROM booking_details";
        $pincodes = $this->db->query($pincode_sql);
        $city = $this->db->query($city_sql);
        //$ratings = $this->db->query($rating_sql);
        
        $data['customers'] = ceil($this->db->count_all('booking_unit_details')/100)*100;
        $data['pincodes'] = ceil($pincodes->result_array()[0]['pincodes']/100)*100;
        $data['city'] = ceil($city->result_array()[0]['city']/100)*100;
        $data['ratings'] = '4.7'; //$ratings->result_array()[0]['rating'];
        
        return $data;
    }
    
    /**
     *  @desc : This function is used to insert contact us query
     *  @param : $data
     *  @return :insert_id
     */
    function insert_contact_us_query($data){
        $this->db->insert('contact_us_query',$data);
        return $this->db->insert_id();
    }
    
    function get_paytm_payment_qr_code($where){
        $this->db->where($where);
        $this->db->select("*");
        $query = $this->db->get("paytm_payment_qr_code");
        return $query->result_array();
    }

}
