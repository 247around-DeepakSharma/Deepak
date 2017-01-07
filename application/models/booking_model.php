<?php

class Booking_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
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

     /**
     * @desc: this is used to add appliances
     * @param: Array(Appliances details)
     * @return: appliance id
     */
    function addappliance($appliance_detail){
        //log_message ('info', __METHOD__ . "appliance_detail data". print_r($appliance_detail, true));
        $this->db->insert('appliance_details', $appliance_detail);

        return $this->db->insert_id();
    }

    /**
     * @desc: Update appliace details
     * @param:  user id
     * @param: Array
     * @return : void
     */
    function update_appliances($appliance_id, $appliance_details){
        $this->db->where('id', $appliance_id);
        $this->db->update('appliance_details', $appliance_details);
        log_message ('info', __METHOD__ . "=> Booking  SQL ". $this->db->last_query());
    }

    /**
     *  @desc : add unit details for a booking
     *  @param : booking(appliance) details
     *  @return : none
     */

    function addunitdetails($booking){
        log_message ('info', __METHOD__);
        $this->db->insert('booking_unit_details', $booking);
        log_message ('info', __METHOD__ . "=> Booking  SQL ". $this->db->last_query());
        return $this->db->insert_id();
    }
    /**
     * @desc: Update unit details
     * @param String $booking_id
     * @param Array $data
     * @return Array
     */
    function update_booking_unit_details($booking_id, $data){
        $this->db->where('booking_id', $booking_id);
        $result = $this->db->update('booking_unit_details', $data);
        log_message ('info', __METHOD__ . "=> Booking  SQL ". $this->db->last_query());
        return $result;
    }
    
    function update_booking_unit_details_by_any($where, $data){
        $this->db->where($where);
        $result = $this->db->update('booking_unit_details', $data);
        //log_message ('info', __METHOD__ . "=> Booking  SQL ". $this->db->last_query());
        return $result;
    }


    /** @description:* add booking
     *  @param : booking
     *  @return : array (booking)
     */

    function addbooking($booking){
	$this->db->insert('booking_details', $booking);
        
        log_message ('info', __METHOD__ . "=> Booking  SQL ". $this->db->last_query());

        return $this->db->insert_id();
    }


    /**
     *  @desc : this function is to get a particular service.
     *
     * 	This function gets the service name with the help of its id
     *
     *  @param : service id
     *  @return : service
     */
    function selectservicebyid($service_id) {
        $query = $this->db->query("SELECT services from services where id='$service_id'");
        return $query->result_array();
    }

    /**
     *  @desc : to select the services.
     *
     *  The services we get are the once that are active from our end
     *
     *  @param : void
     *  @return : array with active services
     */
    function selectservice() {
        $query = $this->db->query("Select id,services from services where isBookingActive='1' order by services");
	return $query->result();
    }

    /**
     *  @desc : Function to view completed bookings in Descending order according by close date.
     *
     *  Here start and limit upto which we want to see the output is given.
     *
     *  Shows users name, phone number and services name.
     *
     *  Also shows complete booking details and also assigned service centre's basic
     *  details for that particular booking.
     *
     *  @param: start and limit of result
     *  @return : array of booking, users, services and service center details in sorted
     *          format by closed date in descending order.
     */
    function view_completed_or_cancelled_booking($limit, $start, $status, $booking_id= "") {
        $add_limit = ""; $where = "";
        if($limit != "All"){
             $add_limit = " LIMIT $start, $limit ";
        }
        if($booking_id != ""){
            $where =  "  booking_id = '$booking_id' AND ";
        }
        $query = $this->db->query("Select services.services,users.name as customername,
            users.phone_number, booking_details.*, service_centres.name as service_centre_name,
            service_centres.district as city, service_centres.primary_contact_name,
            service_centres.primary_contact_phone_1
            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
            WHERE `booking_id` NOT LIKE '%Q-%' AND $where
            (booking_details.current_status = '$status')
	    ORDER BY closed_date DESC $add_limit "
        );
       $temp = $query->result();
       usort($temp, array($this, 'date_compare_bookings'));
       return $temp;
    }

    /**
     *  @desc : Function to get service center details
     *
     * 	This method helps to get basic service center details like primary contact name, phone number.
     *
     * 	This function get those service center's details that are assigned to particular booking by matching
     *      service center's id with assigned vendor id for a booking.
     *
     *  @param : void
     *  @return : assigned vendor's basic details
     */
    function service_center_details() {
        $query = $this->db->query("Select service_centres.primary_contact_name,
    service_centres.primary_contact_phone_1 from service_centres,booking_details
    where booking_details.assigned_vendor_id=service_centres.id ");

        return $query->result();
    }

    /**
     * @desc : This funtion counts total number of bookings
     * @param : void
     * @return : total number bookings
     */
    public function total_booking() {
        return $this->db->count_all_results("booking_details");
    }

    /**
     * @desc : This funtion counts total number of pending or rescheduled bookings
     *
     * Also matches users id from users and booking details table.
     *
     * @param : booking id and service center id
     * @return : total number of pending or rescheduled bookings
     */
    public function total_pending_booking($booking_id = "", $service_center_id = "") {
        $where = "";

        if ($booking_id != "") {
            $where .= "AND `booking_details`.`booking_id` = '$booking_id'";
        } else {
            $where .= "AND DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= -1";
        }

        if ($service_center_id != "") {
            $where .= " AND assigned_vendor_id = '" . $service_center_id . "'";
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

    /**
     * @desc : This funtion used to sort bookings based on their booking date
     *
     * Also matches users id from users and booking details table.
     *
     * @param : start and limit of result, booking id and service center id
     * @return : date sorted booking for pending or rescheduled bookings, booking details,
     *          basic user's and service center details.
     */
    function date_sorted_booking($limit, $start, $booking_id = "", $service_center_id = "") {
        $where = "";

        if ($booking_id != "") {
            $where .= "AND `booking_details`.`booking_id` = '$booking_id'";

        } else {
            $where .= " AND DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= -1";
        }

        if ($service_center_id != "") {
            $where .= " AND assigned_vendor_id = '" . $service_center_id . "'";
            $where .= " AND DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= -1";
        }

        $add_limit = "";

        if($start !== "All"){
            $add_limit = " limit $start, $limit ";
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
            (booking_details.current_status='Pending' OR booking_details.current_status='Rescheduled') order by STR_TO_DATE(`booking_details`.booking_date,'%d-%m-%Y') desc $add_limit"
        );

       // echo $this->db->last_query();
        return $query->result();

    }


    /**
     * @desc : This funtion counts total number of completed or cancelled bookings
     *
     * Also matches users id from users and booking details table.
     *
     * @param : void
     * @return : total number of completed or cancelled bookings
     */
    public function total_closed_booking($status = "", $booking_id = "") {
        $where = "";

        if($booking_id != ""){
            $where =  "  booking_id = '$booking_id' AND ";
        }

        $query = $this->db->query("Select count(*) as count from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
            WHERE `booking_id` NOT LIKE '%Q-%' AND $where
            (booking_details.current_status = '$status')"
        );

        $count = $query->result_array();
        return $count[0]['count'];
    }

    /**
     * @desc : This funtion counts total number of pending queries.
     *
     * Also matches users id from users and booking details table.
     *
     * @param : booking id
     * @return : total number of pending queries
     */
    public function total_queries($status, $booking_id = "") {
        $where = "";

	if ($booking_id != "")
	    $where .= "AND `booking_details`.`booking_id` = '$booking_id'";

	$sql = "SELECT count(*) as count from booking_details
        JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
        JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
        LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
        WHERE `booking_id` LIKE '%Q-%' $where
        AND (DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= 0 OR
                booking_details.booking_date='') AND `booking_details`.current_status='$status'";
	$query = $this->db->query($sql);
	$count = $query->result_array();


	return $count[0]['count'];
    }


    /**
     * @desc : This funtion counts total number of bookings for a particular user
     *
     * Counts the number of bookings with same user id.
     *
     * @param : user id
     * @return : total number of bookings for particular user
     */
    public function total_user_booking($user_id) {
        $this->db->where("user_id = '$user_id'");
        $result = $this->db->count_all_results("booking_details");
        return $result;
    }

    /**
     * @desc : This funtion gives all the cancellation reasons present for partner or vendor or admin panel
     *
     * @param : void
     * @return : all the cancellation reasons present
     */
    function cancelreason($where) {
        $this->db->where($where);
        $query = $this->db->get('booking_cancellation_reasons');
        return $query->result();
    }

    /**
     * @desc : This funtion is to get all booking details of particular booking.
     *
     * Finds all the booking details of particular booking of a particular user.
     *
     * @param : booking id
     * @return : array of booking details
     */
    function getbooking($booking_id) {
        $this->db->select('*');
        $this->db->where('booking_id', $booking_id);
        $query = $this->db->get('booking_details');
        return $query->result_array();
    }

    /**
     * @desc : This funtion is to count number of bookings for a particular user.
     *
     * Searches and counts bookings having same user id.
     *
     * @param : user id
     * @return : count of bookings for specific user in numbers
     */
    function getBookingCountByUser($user_id) {
        $this->db->where("user_id", $user_id);
        $this->db->from("booking_details");

        //$query = $this->db->get();
        $result = $this->db->count_all_results();

        return $result;
    }

    /**
     * @desc : This funtion gives the name of the service
     *
     * Finds out the name of the service for a particular service id
     *
     * @param : service id
     * @return : service name
     */
    function service_name($service_id) {
        $sql = "Select services from services where id='$service_id'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

     /**
     * @desc: get booking history
     * @param: booking id, flag to make join with service center table
     * @return : array
     */
    function getbooking_history($booking_id, $join=""){

        $service_centre = "";
        $condition ="";
        $service_center_name ="";
        if($join !=""){
            $service_center_name = ",service_centres.name as vendor_name, service_centres.district,service_centres.address, service_centres.state, service_centres.pincode, "
		. "service_centres.primary_contact_name, service_centres.owner_email,service_centres.owner_name, "
		. "service_centres.primary_contact_phone_1,service_centres.primary_contact_phone_2, service_centres.primary_contact_email ";
	    $service_centre = ", service_centres ";
            $condition = " and booking_details.assigned_vendor_id =  service_centres.id";
        }

        $sql = " SELECT `services`.`services`, users.*, booking_details.* ".  $service_center_name
               . "from booking_details, users, services " . $service_centre
               . "where booking_details.booking_id='$booking_id' and "
               . "booking_details.user_id = users.user_id and "
               . "services.id = booking_details.service_id  ". $condition;

        $query = $this->db->query($sql);
        $result = $query->result_array();
        
        $this->db->Select('*');
        $this->db->where('booking_id', $booking_id);
        $query1 = $this->db->get('spare_parts_details');
        if($query1->num_rows > 0){
            $result1 = $query1->result_array();
           
            $result['spare_parts'] = $result1[0];
           
        }
        return $result;
    }

    function getbooking_filter_service_center($booking_id){

        $this->db->select('assigned_vendor_id');
        $this->db->where('assigned_vendor_id is NOT NULL', NULL, true);
        $this->db->where('booking_id', $booking_id);
        $query = $this->db->get('booking_details');

        if($query->num_rows > 0){
            // NOT NUll
            $data = $this->getbooking_history($booking_id, "Join");

            log_message('info', __METHOD__ . $this->db->last_query());
            return $data;

        } else {
            //NUll
            $sql = " SELECT `services`.`services`, users.*, booking_details.* "
               . "from booking_details, users, services "
               . "where booking_details.booking_id='$booking_id' and "
               . "booking_details.user_id = users.user_id and "
               . "services.id = booking_details.service_id  ";

        $query = $this->db->query($sql);
       // log_message('info', __METHOD__ . $this->db->last_query());

        return $query->result_array();
        }

    }


    function getbooking_history_by_appliance_id($appliance_id){

        $sql = " SELECT `services`.`services`, users.*, `booking_unit_details`.service_id, `booking_details`.* "
               . "from booking_unit_details, booking_details, users, services "
               . "where booking_unit_details.appliance_id='$appliance_id' and "
               . " booking_details.booking_id = booking_unit_details.booking_id and "
               . "booking_details.user_id = users.user_id and "
               . "services.id = booking_details.service_id  ";

        $query = $this->db->query($sql);

        return $query->result_array();
    }

    /**
     *  @desc : function to get service center details assigned to a particular booking
     *  @param : $booking_id
     *  @return : array of service center details.
     */
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

    /**
     *  @desc : function to get brand's list available for a particular service
     *  @param : service_id
     *  @return : list of all brands for that service
     */
    function getBrandForService($service_id) {
        $this->db->where(array('service_id' => $service_id, 'seo' => 1));
        $this->db->select('brand_name');
	    $this->db->order_by('brand_name');
	    $query = $this->db->get('appliance_brands');
        return $query->result_array();
    }

    function getCategoryForService($service_id, $state ="", $partner_id) {
        //And state = '$state'
        $where = "";
        if($partner_id != ""){
           $where .= " AND partner_id = '$partner_id'";

        }
    	$sql = "Select distinct category from service_centre_charges where service_id=
    	'$service_id' $where and active='1' AND check_box ='1' ";

     	$query = $this->db->query($sql);
    	return $query->result_array();
    }

    function getCapacityForCategory($service_id, $category, $state="", $partner_id) {
        //And state = '$state'
        $where = "";
        if($partner_id !=""){
            $where .= " AND partner_id ='$partner_id' ";
        }

    	$sql = "Select distinct capacity from service_centre_charges where service_id='$service_id'
    	and category='$category' and active='1' AND check_box ='1' $where";

    	$query = $this->db->query($sql);

    	return $query->result_array();
    }

    function getCapacityForAppliance($service_id) {
	//echo $category;
	$sql = "Select distinct capacity from service_centre_charges where service_id='$service_id' and active='1'";

	$query = $this->db->query($sql);

	return $query->result_array();
    }
    /*
     * @desc: This method return Price details. It filters according to service id, category, capacity, partner id
     */
    function getPricesForCategoryCapacity($service_id, $category, $capacity, $partner_id, $state) {

        $this->db->distinct();
        $this->db->select('id,service_category,customer_total, partner_net_payable, customer_net_payable, pod');
        $this->db->where('service_id',$service_id);
        $this->db->where('category', $category);
        $this->db->where('active', 1);
        $this->db->where('check_box', 1);
        $this->db->where('partner_id', $partner_id);
        //$this->db->where('state', $state);

    	if (!empty($capacity)) {
    		$this->db->where('capacity', $capacity);
    	}

    	$query = $this->db->get('service_centre_charges');

    	return $query->result_array();
    }

    /**
     *  @desc : Function to get service centers
     *
     * Finds out the service centers that are in active state.
     *
     *  @param : void
     *  @return : details of active service centers
     */
    function select_service_center() {
        $query = $this->db->query("Select id, non_working_days, primary_contact_email, owner_email, name
                            from service_centres
                            where active=1");
        return $query->result();
    }

    /**
     *  @desc : Function to get pending and reschedule bookings where vendor is not assigned
     *
     *  This function gives those bookings in which still no vendor is assigned to complete the booking,
     *      so that we can assigh vendor to these bookings as well.
     *
     *  @param : void
     *  @return : list of bookings with unassigned vendors
     */
    //TODO: can be removed
    function pendingbookings() {
        $sql = "Select services.services, "
                . "users.name, users.phone_number,"
                . "booking_details.* "
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

    /**
     *  @desc : Function to set mail to vendor field as 1(mail sent)
     *
     *  @param : booking id
     *  @return : void
     */
    function set_mail_to_vendor($booking_id) {
        $this->db->query("UPDATE booking_details set mail_to_vendor= 1 where booking_id ='$booking_id'");
        echo $this->db->last_query();
    }

    /**
     *  @desc : Function to set mail to vendor field as 0(not sent)
     *  @param : booking id
     *  @return : void
     */
    //TODO: can be removed
    function set_mail_to_vendor_flag_to_zero($booking_id) {
        $this->db->query("UPDATE booking_details set mail_to_vendor= 0 where booking_id
                ='$booking_id'");
    }

    /**
     *  @desc : Function to update booking details
     *  @param : booking id and booking details to update
     *  @return : void
     */
    //TODO: Merge with update_booking_details function
    function update_booking($booking_id, $data) {
        $this->db->where(array("booking_id" => $booking_id));
        $result =  $this->db->update("booking_details", $data);

        log_message ('info', __METHOD__ . "=> Booking  SQL ". $this->db->last_query());

        return $result;
    }

    function update_booking_by_order_id($order_id, $data) {
        $this->db->where(array("order_id" => $order_id, "current_status" => "FollowUp"));
        $result =  $this->db->update("booking_details", $data);

        log_message ('info', __METHOD__ . "=> Booking SQL ". $this->db->last_query() . ", Result: " . $result);

        //return corresponding booking_id if booking gets updated else return false
        if ($this->db->affected_rows() > 0) {
            $this->db->select('booking_id');
            $this->db->where('order_id', $order_id);
            $query = $this->db->get('booking_details');
            return $query->result_array()[0]['booking_id'];
        } else {
            return FALSE;
        }
    }

    /**
     *  @desc : Function to update booking(vendor's) details
     *
     *  This function updates the rating and comments given to the vendors for the quality
     *      of service he provided.
     *
     *  @param : booking id and vendor's details to update
     *  @return : true if update done else false
     */
    function vendor_rating($booking_id, $data) {
        $sql = "UPDATE booking_details set vendor_rating_stars='$data[vendor_rating_star]',"
                . "vendor_rating_comments='$data[vendor_rating_comments]' where booking_id='$booking_id'";
        $query = $this->db->query($sql);
        return $query;
    }

    /**
     *  @desc : Function to get unit booking details
     *
     *  This function gives all the unit details of a particular booking from booking_unit_details.
     *
     *  @param : booking id
     *  @return : all the unit booking detais
     */
     function get_unit_details($where, $like= FALSE) {
        $this->db->select('*');
        if($like == TRUE){
            $this->db->like($where);
        } else {
            $this->db->where($where);
        }
        
        $query = $this->db->get('booking_unit_details');
       // log_message('info', __METHOD__ . " SQL" . $this->db->last_query());

        return $query->result_array();
    }

    /**
     *  @desc : This function is to get appliance count of a user
     *
     *  Gives the number of appliances that are active for a particular user.
     *
     *  @param : user id
     *  @return : count of active appliances for a particular user
     */
    function getApplianceCountByUser($user_id) {
        log_message('info', __METHOD__ . "=> User ID: " . $user_id);

        $this->db->where(array('user_id' => $user_id, 'is_active' => '1'));
        $this->db->from("appliance_details");

        $result = $this->db->count_all_results();
        return $result;
    }

    /**
     *  @desc : This function is to add sample appliances for a new user
     *
     *  Sample appliances are added in user's appliance details once a user get registered with us.
     *
     *  Five(5) sample appliances are added to user's appliance details for our
     *      main 5 services.(1 for each service)
     *
     *  @param : user id and count of appliances
     *  @return : void
     */
    function addSampleAppliances($user_id, $count) {
        log_message('info', "Entering: " . __METHOD__);

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
        }
    }

    /**
     *  @desc : This function is to add new brand to our database for a service.
     *
     *  This helps to add any new brand found(told by customer) for any
     *      service(like for Television, Refrigerator, etc)
     *
     *  @param : service id and new brand
     *  @return : void
     */
    function addNewApplianceBrand($service_id, $newbrand) {
        $sql = "INSERT into appliance_brands(service_id,brand_name) values('$service_id','$newbrand')";
        $this->db->query($sql);
    }

    /**
     *  @desc : This function finds all the details for the particular service name/
     *      get all service from database(mentioned earliar)
     *
     *  @param : service name
     *  @return : array (service)
     */
    function getServiceId($service_name) {
        $sql = "SELECT * FROM services WHERE services='$service_name'";
        $query = $this->db->query($sql);

        if($query->num_rows > 0){
            $services = $query->result_array();
            return $services[0]['id'];
        } else {
            return false;
        }

    }

    /**
     *  @desc : This function selects specific source for the given source code
     *
     *  @param : source code
     *  @return : source name of the code
     */
    function get_booking_source($source_code) {
        $query = $this->db->query("SELECT source FROM bookings_sources WHERE code='$source_code'");
        return $query->result_array();
    }

    /**
     *  @desc : This function is to insert snapdeal leads details
     *
     *  @param : snapdeal leads details
     *  @return : insert_id after insertion
     */
    //TODO: can be removed
    function insert_sd_lead($details) {
        $this->db->insert('snapdeal_leads', $details);

        return $this->db->insert_id();
    }

    /**
     *  @desc : This function is to get snapdeal leads details
     *
     *  @param : snapdeal_leads  id
     *  @return : snapdeal leads details for particular id
     */
    //TODO: can be removed
    function get_sd_lead($id) {
        $query = $this->db->query("SELECT * FROM snapdeal_leads WHERE id='$id'");
        $results = $query->result_array();

        return $results[0];
    }

    /*
     * @desc: Shows all unassigned bookings from Snapdeal
     * @param: void
     * @return: array of snapdeal leads details
     */
    //TODO: can be removed

    function get_sd_unassigned_bookings() {
        $query = $this->db->query("SELECT * FROM snapdeal_leads WHERE Status_by_247around='NewLead'");
        return $query->result_array();
    }

    /**
     *  @desc : This function is to get all snapdeal booking details
     *
     *  The deatils will be in sorted manner in descending order by create date.
     *
     *  @param : void
     *  @return : array of snapdeal booking details
     */
    function get_all_sd_bookings() {
        $query = $this->db->query("SELECT * FROM snapdeal_leads ORDER BY create_date DESC");
        return $query->result_array();
    }

    /**
     *  @desc : This function is to check snapdeal leads with their order id weather
     *      they exists or not.
     *
     *  @param : array of sub order id's
     *  @return : if exists returns true else false
     */
    function check_sd_lead_exists_by_order_id($sub_order_id) {
	$this->db->where(array("Sub_Order_ID" => $sub_order_id));
	$query = $this->db->get('snapdeal_leads');

	if (count($query->result_array()) > 0) {
	    return TRUE;
	} else {
	    return FALSE;
	}
    }

    /**
     *  @desc : This function is to check whether order id exists for a specific
     * partner. Order id is unique for each partner and is tied to a unique
     * booking id.
     *
     *  @param : String	$partner_id Partner ID
     *  @param : String	$order_id Order ID
     *
     *  @return : If exists, returns booking details specific to this order id else false
     */
    function check_order_id_exists($partner_id, $order_id) {
	$this->db->where(array("partner_id" => $partner_id, "order_id" => $order_id));
	$query = $this->db->get('booking_details');

	if (count($query->result_array()) > 0) {
	    return $query->result_array()[0];
	} else {
	    return FALSE;
	}
    }

    /**
     *  @desc : This function is to check snapdeal leads with their booking id weather
     *      they exists or not.
     *
     *  @param : array of booking id's
     *  @return : if exists returns true else false
     */
    function check_sd_lead_exists_by_booking_id($booking_id) {
        $this->db->where(array("CRM_Remarks_SR_No" => $booking_id));
        $query = $this->db->get('snapdeal_leads');

        if (count($query->result_array()) > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     *  @desc : This function is to get snapdeal leads with their order id
     *
     *  @param : array of Sub_Order_Id
     *  @return : array of snapdeal leads
     */
    function get_sd_lead_by_order_id($sub_order_id) {
        $this->db->where(array("Sub_Order_ID" => $sub_order_id));
        $query = $this->db->get('snapdeal_leads');

        return $query->result_array();
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

    /** get_queries
     *
     *  @desc : Function to get pending queries according to pagination and vendor availability.
     * It can work in different ways:
     *
     * 1. Return count of pending queries
     * 2. Return data for pending queries
     *
     * Queries which have booking date of future are not shown. Queries with
     * empty booking dates are shown.
     *
     * @param : start and limit for the query
     * @param : $status - Completed or Cancelled
     * @p_av : Type of queries: Vendor Available or Vendor Not Available
     *
     *  @return : Count of Queries or Data for Queries
     */
    function get_queries($limit, $start, $status, $p_av, $booking_id = "") {
        $check_vendor_status = "";
        $where = "";
        $add_limit = "";
        $get_field = " services.services,
            users.name as customername, users.phone_number,
            bd.* ";

        if ($booking_id != "") {
            $where = "AND `bd`.`booking_id` = '$booking_id' AND `bd`.current_status='$status'  ";
            if($start == 'All') {
                $get_field = " Count(bd.booking_id) as count ";
            }

        } else {
            if ($start != 'All') {

                $add_limit = " limit $start, $limit ";


            } else if($start == 'All') {

                $get_field = " Count(bd.booking_id) as count ";
            }

            $where = "AND (DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(bd.booking_date, '%d-%m-%Y')) >= 0 OR
                bd.booking_date='') AND `bd`.current_status='$status' ";
        }

        if($p_av == PINCODE_AVAILABLE ){
            $is_exist = ' EXISTS ';

        } else if($p_av == PINCODE_NOT_AVAILABLE){
            $is_exist = ' NOT EXISTS ';
        } else if($p_av == PINCODE_ALL_AVAILABLE){
            $is_exist = '';

        }

        // If request for FollowUp then check Vendor Available or Not
        if($status != "Cancelled"){
            if($p_av != PINCODE_ALL_AVAILABLE){
            $check_vendor_status = " AND $is_exist
                (SELECT 1
                FROM (`vendor_pincode_mapping`)
                JOIN `service_centres` ON `service_centres`.`id` = `vendor_pincode_mapping`.`Vendor_ID`
                WHERE `vendor_pincode_mapping`.`Appliance_ID` = bd.service_id
                AND `vendor_pincode_mapping`.`Pincode` = bd.booking_pincode
                AND `service_centres`.`active` = '1' AND `service_centres`.on_off = '1')  ";
            }
        }

        $sql = "SELECT $get_field
            from booking_details as bd
            JOIN  `users` ON  `users`.`user_id` =  `bd`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `bd`.`service_id`
            WHERE `bd`.booking_id LIKE '%Q-%' $where
                $check_vendor_status

            order by
                CASE
                WHEN `bd`.internal_status = 'Missed_call_confirmed' THEN 'a'

                WHEN  `bd`.booking_date = '' THEN 'b'
                ELSE 'c'
            END, STR_TO_DATE(`bd`.booking_date,'%d-%m-%Y') desc $add_limit";

        $query = $this->db->query($sql);
        //log_message('info', __METHOD__ . "=> " . $this->db->last_query());

        if($status == "FollowUp" && ($p_av == PINCODE_ALL_AVAILABLE) && !empty($booking_id) && $start !="All"){
            $temp = $query->result();
            $data = $this->searchPincodeAvailable($temp, $p_av);
            return $data;

        }else {
            return $query->result();
        }


    }

    /**
     * @desc : In this function, we will pass Array and search active pincode and vendor.
     * If pincode available then insert vendor name in the same key.
     * @param : Array
     * @return : Array
     */
    function searchPincodeAvailable($temp, $pv) {

        foreach ($temp as $key => $value) {

            $this->db->distinct();
            $this->db->select('count(Vendor_ID) as count');
            $this->db->where('vendor_pincode_mapping.Appliance_ID', $value->service_id);
            $this->db->where('vendor_pincode_mapping.Pincode', $value->booking_pincode);
            $this->db->from('vendor_pincode_mapping');

            $this->db->join('service_centres', 'service_centres.id = vendor_pincode_mapping.Vendor_ID');

            $this->db->where('service_centres.active', "1");
            $data = $this->db->get();

            $count = $data->result_array()[0]['count'];
            if ($count > 0) {
                if($pv == PINCODE_AVAILABLE){
                    $temp[$key]->vendor_status ="Vendor Available";
                } else if($pv == PINCODE_NOT_AVAILABLE) {
                    unset($temp[$key]);
                } else if($pv == PINCODE_ALL_AVAILABLE){
                     $temp[$key]->vendor_status = "Vendor Available";
                }

            } else {
                if($pv == PINCODE_AVAILABLE){
                    unset($temp[$key]);
                } else if($pv == PINCODE_NOT_AVAILABLE) {
                    $temp[$key]->vendor_status = "Vendor Not Available";
                } else if($pv == PINCODE_ALL_AVAILABLE){
                    $temp[$key]->vendor_status = "Vendor Not Available";
                }

            }
        }

        return $temp;
    }


    /**
     *  @desc : Function to get distinct area/pincode/city/state
     *  @param : String place type
     *  @return : Array Distinct places
     */
    function get_distinct_place($place_type) {
        $this->db->distinct($place_type);
        $this->db->group_by($place_type);
        $query = $this->db->get('vendor_pincode_mapping');

        //log_message('info', __METHOD__ . '=> Last query: ' . $this->db->last_query());

        return $query->result_array();
    }

    function search_bookings($where, $partner_id = "") {
    // Need to get brand to send to vendor pincode mapping add form, So we will use join with booking_unit_details

    $this->db->select("services.services, users.name as customername,
            users.phone_number, booking_details.*");
    $this->db->from('booking_details');
    $this->db->join('users',' users.user_id = booking_details.user_id');
    $this->db->join('services', 'services.id = booking_details.service_id');
    if($partner_id !=""){
        $this->db->join('booking_unit_details', 'booking_unit_details.booking_id = booking_details.booking_id');
        $this->db->where('booking_details.partner_id', $partner_id);
    }

    $this->db->like($where);
    $query =  $this->db->get();
    $temp = $query->result();
    for ($i=0; $i < count($temp) ; $i++) {
       if(!empty($temp[$i]->assigned_vendor_id)){
           $this->db->select('service_centres.name as service_centre_name,
            service_centres.primary_contact_name,service_centres.primary_contact_phone_1 ');
           $this->db->where('id', $temp[$i]->assigned_vendor_id);
           $query1 = $this->db->get('service_centres');
           $result = $query1->result_array();

           $temp[$i]->service_centre_name =  $result[0]['service_centre_name'];
           $temp[$i]->primary_contact_name = $result[0]['primary_contact_name'];
           $temp[$i]->primary_contact_phone_1 = $result[0]['primary_contact_phone_1'];

    }
    }


    usort($temp, array($this, 'date_compare_queries'));
    if($query->num_rows>0){

        if (strpos($temp[0]->booking_id, "Q-") !== FALSE) {

            $data = $this->searchPincodeAvailable($temp, PINCODE_ALL_AVAILABLE);
            return $data;
        }
    }

        return  $temp;
    }

    /**
     *  @desc : This function is to get internal status from database
     *  @param : void
     *  @return : all internal status present in database
     */
    function get_internal_status($where) {
        $this->db->select('*');
        $this->db->where($where);
        $query = $this->db->get('internal_status');
        
        if($query->num_rows > 0){
            return $query->result();   
        } else {
            return FALSE;
        }
    }

    /**
     *  @desc : Find potential SCs for an Appliance in a Pincode
     *  @param : appliance id and pincode
     *  @return : returns available service centers
     */
    function find_sc_by_pincode_and_appliance($appliance, $pincode) {
        $query = $this->db->query("SELECT DISTINCT(`service_centres`.`id`) FROM (`vendor_pincode_mapping`)
	    JOIN `service_centres` ON `service_centres`.`id` = `vendor_pincode_mapping`.`Vendor_ID`
    		WHERE `Appliance_ID` = '$appliance' AND `vendor_pincode_mapping`.`Pincode` = '$pincode'
	    AND `service_centres`.`active` = '1'
            AND `service_centres`.`on_off` = '1'");

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
    //TODO: can be removed
    function change_booking_status($booking_id) {
        $status = array("current_status" => "FollowUp",
            "internal_status" => "FollowUp",
            "cancellation_reason" => NULL,
            "closed_date" => NULL);

        $this->db->where("booking_id", $booking_id);
        $this->db->update("booking_details", $status);

        $booking_status = array('booking_status' => 'FollowUp');
	$this->db->where("booking_id", $booking_id);
	$this->db->update("booking_unit_details", $booking_status);

	return true;
    }

    /**
     * Get product Type means description about booking from both table snapdeal_leads and partner_leads.
     * @param: bookinf id
     * @return : string
     */
    //TODO: can be removed
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
    function get_price_mapping_partner_code($partner_code, $partner_id="") {
    $this->db->select('price_mapping_id');
    if($partner_code !=""){
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

    /**
     * @desc: this function is used to get services charges to be filled by service centers
     * @param: booking id
     * @return: Array()
     */
    function getbooking_charges($booking_id = "", $status="") {

	if ($booking_id != "") {
	    $this->db->where('booking_id', $booking_id);
	}

	//Status should NOT be Completed or Cancelled
    if($status !=""){
	$this->db->where_not_in('current_status', $status);
    }

        $this->db->where_not_in('internal_status', "Reschedule");
	$query = $this->db->get('service_center_booking_action');

	log_message('info', __METHOD__ . "=> " . $this->db->last_query());

	return $query->result_array();
    }

    /**
     * @desc: this function is used to get bookings to review the details(charges) filled by service centers
     *
     * Through this the charges collected of particular booking by vendor is displayed.
     *
     * @param: void
     * @return: Array of charges
     */
    function get_booking_for_review($booking_id) {
       
        $charges = $this->service_centers_model->getcharges_filled_by_service_center($booking_id);
        foreach ($charges as $key => $value) {
            $charges[$key]['service_centres'] = $this->vendor_model->getVendor($value['booking_id']);
            $charges[$key]['booking'] = $this->getbooking_history($value['booking_id']);
        }

        return $charges;
    }

    /**
     * @desc: this function is used insert the logs for outbound calls.
     *
     * Through this the customers id, name and agents name is inserted.
     *
     * @param: customer and agent details.
     * @return: void
     */
    function insert_outbound_call_log($details) {
        $this->db->insert('agent_outbound_call_log', $details);
    }

    /**
     * @desc: This function is used to get booking email tempalate.
     *
     * Through this the email tag is user to get its template from database.
     *
     * With this also the enail to and from field is also selected.
     *
     * @param: email tag
     * @return: Array of email template
     */
    function get_booking_email_template($email_tag) {
        $this->db->select("template, to, from,cc");
        $this->db->where('tag', $email_tag);
        $this->db->where('active', 1);
        $query = $this->db->get('email_template');
        if ($query->num_rows > 0) {
            $template = $query->result_array();
            return array($template[0]['template'], $template[0]['to'], $template[0]['from'],$template[0]['cc']);
        } else {
            return "";
        }
    }

    /**
     * @desc: this is used to display reschedule request by service center in admin panel
     * @param: void
     * @return: void
     */
    function review_reschedule_bookings_request(){

        $this->db->select('distinct(service_center_booking_action.booking_id), users.name as customername, booking_details.booking_primary_contact_no, services.services, booking_details.booking_date, booking_details.booking_timeslot, service_center_booking_action.booking_date as reschedule_date_request,  service_center_booking_action.booking_timeslot as reschedule_timeslot_request, service_centres.name as service_center_name, booking_details.quantity, service_center_booking_action.reschedule_reason');
        $this->db->from('service_center_booking_action');
        $this->db->join('booking_details','booking_details.booking_id = service_center_booking_action.booking_id');

        $this->db->join('services','services.id = booking_details.service_id');
        $this->db->join('users','users.user_id = booking_details.user_id');
        $this->db->join('service_centres','service_centres.id = booking_details.assigned_vendor_id');
        $this->db->where('service_center_booking_action.internal_status', "Reschedule");

        $query = $this->db->get();

        $result = $query->result_array();

        return $result;
    }

     //Find order id for a partner
    function get_booking_from_order_id($partner_id, $order_id) {
        $this->db->select('*');
        $this->db->where('partner_id', $partner_id);
        $this->db->like('order_id' , $order_id);

        $query = $this->db->get("booking_details");

         $temp = $query->result();

        usort($temp, array($this, 'date_compare_queries'));

        if (strpos($temp[0]->booking_id, "Q-") !== FALSE) {

            $data = $this->searchPincodeAvailable($temp);
            return $data;
        }


    }

    function check_price_tags_status($booking_id, $price_tags){
        $this->db->select('id, price_tags');
        $this->db->like('booking_id', $booking_id);
        $query = $this->db->get('booking_unit_details');
        if($query->num_rows>0){
            $result = $query->result_array();
            foreach ($result as $value) {
                 if (in_array($value['price_tags'], $price_tags)) {
                   // echo "Match found";
                 }
                 else {
                    //echo "Match not found";
                  // $data = array('booking_status' => "Cancelled" );
                   $this->db->where('id', $value['id']);
                   $this->db->delete('booking_unit_details');

                }
            }
        }
        return;
    }

    function getpricesdetails_with_tax($service_centre_charges_id, $state){

        $sql =" SELECT service_category as price_tags,tax_code, product_type,vendor_basic_percentage, customer_total, partner_net_payable, product_or_services  from service_centre_charges where `service_centre_charges`.id = '$service_centre_charges_id' ";

        $query = $this->db->query($sql);
        $result =  $query->result_array();

        $sql1 = " SELECT rate as tax_rate from tax_rates where `tax_rates`.state = '$state'
                  AND `tax_rates`.tax_code = '".$result[0]['tax_code']."' AND  `tax_rates`.product_type = '".$result[0]['product_type']."' AND (to_date is NULL or to_date >= CURDATE() ) AND `tax_rates`.active = 1 ";

        $query1 = $this->db->query($sql1);
        $result1 =  $query1->result_array();

        if(!empty($result1)){

            $result[0]['tax_rate'] = $result1[0]['tax_rate'];
            //Default tax rate is not used
            $result['DEFAULT_TAX_RATE'] = 0;

        } else {
             //Default tax rate is used
            $result[0]['tax_rate'] = DEFAULT_TAX_RATE;
            $result['DEFAULT_TAX_RATE'] = 1;
        }
        unset($result[0]['tax_code']);
        unset($result[0]['product_type']);

        return $result;


    }

    /**
     * @desc: This method get prices details and check price tag is exist in unit details or not.
     * If price tags does not exist, it inserts data in booking unit details and if price tags exist,
     * it update booking unit details.
     * @param: Array
     * @return: Price tags.
     */
    function update_booking_in_booking_details($services_details, $booking_id, $state){

        $data = $this->getpricesdetails_with_tax($services_details['id'], $state);

        $result = array_merge($data[0], $services_details);
        unset($result['id']);  // unset service center charge  id  because there is no need to insert id in the booking unit details table
        $result['customer_net_payable'] = $result['customer_total'] - $result['partner_paid_basic_charges'] - $result['around_paid_basic_charges'];
        $result['partner_paid_tax'] = ($result['partner_paid_basic_charges'] * $result['tax_rate'])/ 100;
        
        $vendor_total_basic_charges =  ($result['customer_net_payable'] + $result['partner_net_payable'] + $result['around_paid_basic_charges'] ) * ($result['vendor_basic_percentage']/100);
        $around_total_basic_charges = ($result['customer_net_payable'] + $result['partner_net_payable'] + $result['around_paid_basic_charges'] - $vendor_total_basic_charges);
         
        $result['around_st_or_vat_basic_charges'] = $this->get_calculated_tax_charge($around_total_basic_charges, $result['tax_rate'] );
        $result['vendor_st_or_vat_basic_charges'] = $this->get_calculated_tax_charge($vendor_total_basic_charges, $result['tax_rate'] );
        
        $result['around_comm_basic_charges'] = $around_total_basic_charges - $result['around_st_or_vat_basic_charges'];
        $result['vendor_basic_charges'] = $vendor_total_basic_charges - $result['vendor_st_or_vat_basic_charges'];
        log_message('info', __METHOD__ . " update booking_unit_details data " . print_r($result, true) . " Price data with tax: " . print_r($data, true));
        // Update request type If price tags is installation OR repair
        if (stristr($result['price_tags'], "Installation")) {
            $this->update_booking($result['booking_id'], array('request_type'=>$result['price_tags']));
             
        } else if (stristr($result['price_tags'], "Repair")) {
            $this->update_booking($result['booking_id'], array('request_type'=>$result['price_tags']));
        }
        
        $this->db->select('id');
        $this->db->where('appliance_id', $services_details['appliance_id']);
        $this->db->where('price_tags', $data[0]['price_tags']);
        $this->db->like('booking_id', preg_replace("/[^0-9]/","",$booking_id));
        $query = $this->db->get('booking_unit_details');
       // log_message('info', __METHOD__ . " Get Unit Details SQl" . $this->db->last_query());

        if ($query->num_rows > 0) {
            //if found, update this entry
            $unit_details = $query->result_array();
            log_message('info', __METHOD__ . " update booking_unit_details ID: " . print_r($unit_details[0]['id'], true));
            $this->db->where('id', $unit_details[0]['id']);
            $this->db->update('booking_unit_details', $result);
        } else {
            //trim booking only digit
            $trimed_booking_id = preg_replace("/[^0-9]/","",$booking_id);
            $unit_where = array('booking_id' => $trimed_booking_id);
            $unit_num = $this->get_unit_details($unit_where, TRUE);
   
            log_message('info', __METHOD__ . " count previous unit: " . count($unit_num));
            log_message('info', __METHOD__ . " Price tags not found ");
            if (!empty($unit_num)) {

                if (count($unit_num) > 1) {

                    $this->db->insert('booking_unit_details', $result);
                    log_message('info', __METHOD__ . " Insert New Unit details SQL" . $this->db->last_query());
                } else {
                    //$this->db->where('booking_id',  $booking_id);
                    if (empty($unit_num[0]['price_tags'])) {
                        $this->db->where('id', $unit_num[0]['id']);
                        $this->db->update('booking_unit_details', $result);
                        log_message('info', __METHOD__ . " Update Unit details SQL" . $this->db->last_query());
                    } else {
                        $this->db->insert('booking_unit_details', $result);
                        log_message('info', __METHOD__ . " Insert New Unit details SQL" . $this->db->last_query());
                    }
                }
            } else {
                $this->db->insert('booking_unit_details', $result);
                log_message('info', __METHOD__ . " Insert New Unit details SQL" . $this->db->last_query());
            }
        }
        $return_details['price_tags'] = $data[0]['price_tags'];
        $return_details['DEFAULT_TAX_RATE'] = $data['DEFAULT_TAX_RATE'];

        return $return_details;
    }

    // Update Price in unit details
    function update_price_in_unit_details($data, $unit_details){

        $data['tax_rate'] = $unit_details[0]['tax_rate'];
        $data['around_paid_basic_charges'] = $unit_details[0]['around_paid_basic_charges'];
        // calculate partner paid tax amount
        $data['partner_paid_tax'] =  ($unit_details[0]['partner_paid_basic_charges'] * $data['tax_rate'])/ 100;
        // Calculate  total partner paid charges with tax
        $data['partner_paid_basic_charges'] = $unit_details[0]['partner_paid_basic_charges'] + $data['partner_paid_tax'];

        $vendor_total_basic_charges =  ($data['customer_paid_basic_charges'] + $unit_details[0]['partner_paid_basic_charges'] + $data['around_paid_basic_charges']) * ($unit_details[0]['vendor_basic_percentage']/100 );
        $around_total_basic_charges = ($data['customer_paid_basic_charges'] + $unit_details[0]['partner_paid_basic_charges'] + $data['around_paid_basic_charges'] - $vendor_total_basic_charges);

        $data['around_st_or_vat_basic_charges'] = $this->get_calculated_tax_charge($around_total_basic_charges, $data['tax_rate'] );
        $data['vendor_st_or_vat_basic_charges'] = $this->get_calculated_tax_charge($vendor_total_basic_charges, $data['tax_rate'] );

        $data['around_comm_basic_charges'] = $around_total_basic_charges - $data['around_st_or_vat_basic_charges'];
        $data['vendor_basic_charges'] = $vendor_total_basic_charges - $data['vendor_st_or_vat_basic_charges'];

        $total_vendor_addition_charge = $data['customer_paid_extra_charges'] * addtitional_percentage;
        $total_around_additional_charge = $data['customer_paid_extra_charges'] - $total_vendor_addition_charge;

        $data['around_st_extra_charges'] = $this->get_calculated_tax_charge($total_around_additional_charge, $data['tax_rate']);
        $data['vendor_st_extra_charges'] = $this->get_calculated_tax_charge($total_vendor_addition_charge, $data['tax_rate']  );

        $data['around_comm_extra_charges'] = $total_around_additional_charge - $data['around_st_extra_charges'];
        $data['vendor_extra_charges'] = $total_vendor_addition_charge - $data['vendor_st_extra_charges'] ;

        $total_vendor_parts_charge = $data['customer_paid_parts'] * parts_percentage;
        $total_around_parts_charge =  $data['customer_paid_parts'] - $total_vendor_parts_charge;
        $data['around_st_parts'] = $this->get_calculated_tax_charge($total_around_parts_charge, $data['tax_rate'] );
        $data['vendor_st_parts'] =  $this->get_calculated_tax_charge($total_vendor_parts_charge,  $data['tax_rate']);
        $data['around_comm_parts'] =  $total_around_parts_charge - $data['around_st_parts'];
        $data['vendor_parts'] = $total_vendor_parts_charge - $data['vendor_st_parts'] ;
        // Check vendor has service tax for the service
        if($unit_details[0]['product_or_services'] == 'Service' && $data['customer_paid_basic_charges'] == 0){
            $st_where = 'service_centres.service_tax_no IS NOT NULL '; 
            $is_service_tax = $this->vendor_model->is_tax_for_booking($unit_details[0]['booking_id'], $st_where);
            if(!$is_service_tax ){
                $vendor_total_basic_charges =  $vendor_total_basic_charges - $data['vendor_st_or_vat_basic_charges'];
                $data['vendor_st_or_vat_basic_charges'] = 0;
            } 
            // Check vendor has tin OR cst for the product
        } else if($unit_details[0]['product_or_services'] == 'Product' && $data['customer_paid_basic_charges'] == 0){
            $vat_where = ' (service_centres.tin_no IS NOT NULL OR service_centres.cst_no IS NOT NULL ) '; 
            $is_vat_tax = $this->vendor_model->is_tax_for_booking($unit_details[0]['booking_id'], $vat_where);
            
            if(!$is_vat_tax ){
                $vendor_total_basic_charges =  $vendor_total_basic_charges - $data['vendor_st_or_vat_basic_charges'];
                $data['vendor_st_or_vat_basic_charges'] = 0;
            } 
        }

        $vendor_around_charge = ($data['customer_paid_basic_charges'] + $data['customer_paid_parts'] + $data['customer_paid_extra_charges']) - ($vendor_total_basic_charges + $total_vendor_addition_charge + $total_vendor_parts_charge );

        if($vendor_around_charge > 0){

            $data['vendor_to_around'] = $vendor_around_charge;
            $data['around_to_vendor'] = 0;

        } else {
            $data['vendor_to_around'] = 0;
            $data['around_to_vendor'] = abs($vendor_around_charge);
        }
        if(isset($data['internal_status'])){
            unset($data['internal_status']);
        }
        $this->db->where('id', $data['id']);
        $this->db->update('booking_unit_details',$data);
    }

    /**
     * @desc: calculate service charges and vat charges
     * @param : total charges and tax rate
     * @return calculate charges
     */
    function get_calculated_tax_charge($total_charges, $tax_rate){
          //52.50 = (402.50 / ((100 + 15)/100)) * ((15)/100)
          //52.50 =  (402.50 / 1.15) * (0.15)
        $st_vat_charge = sprintf ("%.2f", ($total_charges / ((100 + $tax_rate)/100)) * (($tax_rate)/100));
        return $st_vat_charge;
    }

    /**
     * @desc: get booking unit details(Prices) from booking id or appliance id. it gets all prices in the array of key value quantity.
     * @param: booking id, appliance id
     * @return:  Array
     */
    function getunit_details($booking_id="", $appliance_id=""){
        $where = "";

        if($booking_id !=""){
           $where = " `booking_unit_details`.booking_id = '$booking_id' ";
            $sql = "SELECT distinct(appliance_id), appliance_brand as brand, service_id, booking_id, appliance_category as category, appliance_capacity as capacity, `booking_unit_details`.`model_number`, appliance_description as description, `booking_unit_details`.`purchase_month`, `booking_unit_details`.`purchase_year`
            from booking_unit_details Where $where  ";

        } else if ($appliance_id != "") {

	    $where = " `booking_unit_details`.appliance_id = '$appliance_id' ";

            $sql = "SELECT distinct(appliance_id), brand, booking_id, category, capacity, `appliance_details`.`model_number`,description, `appliance_details`.`purchase_month`, `appliance_details`.`purchase_year`, `appliance_details`.serial_number
            from booking_unit_details,  appliance_details Where $where  AND `appliance_details`.`id` = `booking_unit_details`.`appliance_id`  ";

        }

        $query = $this->db->query($sql);
        $appliance =  $query->result_array();

        foreach ($appliance as $key => $value) {
            // get data from booking unit details table on the basis of appliance id
            $this->db->select('id as unit_id, price_tags, customer_total, around_net_payable, partner_net_payable, customer_net_payable, customer_paid_basic_charges, customer_paid_extra_charges, customer_paid_parts, booking_status, partner_paid_basic_charges,product_or_services, serial_number, around_paid_basic_charges');
            $this->db->where('appliance_id', $value['appliance_id']);
            $this->db->where('booking_id', $value['booking_id']);
            $query2 = $this->db->get('booking_unit_details');

            $result = $query2->result_array();
            $appliance[$key]['quantity'] = $result; // add booking unit details array into quantity key of previous array
        }

        return $appliance;
    }

    /**
     * @desc: update price in booking unit details
     */
    function update_unit_details($data){

        if($data['booking_status'] == "Completed"){
            // get booking unit data on the basis of id
            $this->db->select('booking_id, around_net_payable,booking_status, partner_net_payable, '
                    . 'tax_rate, price_tags, partner_paid_basic_charges, around_paid_basic_charges, product_or_services, vendor_basic_percentage');
            $this->db->where('id', $data['id']);
            $query = $this->db->get('booking_unit_details');
            $unit_details = $query->result_array();
            
            if($unit_details[0]['booking_status'] == "Completed"){
                
                $unit_details[0]['partner_paid_basic_charges'] = $unit_details[0]['partner_net_payable'];  
            } 

            $this->update_price_in_unit_details($data, $unit_details);

        } else if($data['booking_status'] == "Cancelled") {
            $closed_date = date("Y-m-d H:i:s");
            if(isset($data['ud_closed_date'])){
                $closed_date = $data['ud_closed_date'];
            } 
            // Update price in unit table
            $this->db->where('id', $data['id']);
            $this->db->update('booking_unit_details', array('booking_status' => 'Cancelled',
                'ud_closed_date'=> $closed_date));
        }

    }

    /**
     * @desc: this method is used to get city, services, sources and user details
     * @param : user phone no.
     * @return : array()
     */
    function get_city_booking_source_services($phone_number){
        $query1['services'] = $this->selectservice();
        $query2['city'] = $this->vendor_model->getDistrict();
        $query3['sources'] = $this->partner_model->get_all_partner_source("0");
        $query4['user'] = $this->user_model->search_user($phone_number);

        return $query = array_merge($query1, $query2, $query3, $query4);

    }

     /**
     * @desc: this is used to copy price and tax rate of custom service center id and insert into booking unit details table with
     * booking id and details.
     * @param: Array()
     * @return : Array()
     */
    function insert_data_in_booking_unit_details($services_details, $state) {
	log_message('info', __FUNCTION__);
	$data = $this->getpricesdetails_with_tax($services_details['id'], $state);

        log_message('info', __METHOD__ . " Get Price with Taxes" . print_r($data, true));

        $result = array_merge($data[0], $services_details);
        unset($result['id']);  // unset service center charge  id  because there is no need to insert id in the booking unit details table
        $result['customer_net_payable'] = $result['customer_total'] - $result['partner_paid_basic_charges'] - $result['around_paid_basic_charges'];
        $result['partner_paid_tax'] = ($result['partner_paid_basic_charges'] * $result['tax_rate'])/ 100;
        
        $vendor_total_basic_charges =  ($result['customer_net_payable'] + $result['partner_paid_basic_charges'] + $result['around_paid_basic_charges'] ) * ($result['vendor_basic_percentage']/100);
        $around_total_basic_charges = ($result['customer_net_payable'] + $result['partner_paid_basic_charges'] + $result['around_paid_basic_charges'] - $vendor_total_basic_charges);
         
        $result['around_st_or_vat_basic_charges'] = $this->get_calculated_tax_charge($around_total_basic_charges, $result['tax_rate'] );
        $result['vendor_st_or_vat_basic_charges'] = $this->get_calculated_tax_charge($vendor_total_basic_charges, $result['tax_rate'] );
        
        $result['around_comm_basic_charges'] = $around_total_basic_charges - $result['around_st_or_vat_basic_charges'];
        $result['vendor_basic_charges'] = $vendor_total_basic_charges - $result['vendor_st_or_vat_basic_charges'];
          
     
        log_message('info', __METHOD__ . " Insert booking_unit_details data" . print_r($result, true));
	$this->db->insert('booking_unit_details', $result);
       // $result['id'] = $this->db->insert_id();
        //Update request type If price tags is installation OR repair
        if (stristr($result['price_tags'], "Installation")) {
            $this->update_booking($result['booking_id'], array('request_type'=>$result['price_tags']));
             
        } else if (stristr($result['price_tags'], "Repair")) {
            $this->update_booking($result['booking_id'], array('request_type'=>$result['price_tags']));
        }
        
        $result['DEFAULT_TAX_RATE'] = $data['DEFAULT_TAX_RATE'];
        return $result;
        }

    /**
     *  @desc : This function is to insert booking state changes.
     *
     *  @param : Array $details Booking state change details
     *  @return :
     */
    function insert_booking_state_change($details) {
        $this->db->insert('booking_state_change', $details);

        return $this->db->insert_id();
    }

    /**
     *  @desc : This function converts a Completed/Cancelled Booking into Pending booking
     * and schedules it to new booking date & time.
     *
     *  @param : String $booking_id Booking Id
     *  @param : Array $data New Booking Date and Time
     *  @param : current_status
     *  @return :
     */
    function convert_booking_to_pending($booking_id, $data, $status) {
    // update booking details
    $this->db->where(array('booking_id' => $booking_id, 'current_status' => $status));
    $this->db->update('booking_details', $data);
    //update unit details
    $this->db->where('booking_id', $booking_id);
    $this->db->update('booking_unit_details', array('booking_status' => '' ));
    // get service center id
    $this->db->select('assigned_vendor_id');
    $this->db->where('booking_id', $booking_id);
    $query = $this->db->get('booking_details');
    if($query->num_rows >0){
        $result = $query->result_array();

        $service_center_data['internal_status'] = "Pending";
        $service_center_data['current_status'] = "Pending";
        $service_center_data['update_date'] = date("Y-m-d H:i:s");
        //update service center action table
        $this->db->where('booking_id', $booking_id);
        $this->db->where('service_center_id', $result[0]['assigned_vendor_id']);
        $this->db->update('service_center_booking_action', $service_center_data);
    }

    }

    /**
     * @desc: this method inser new line item while booking completion
     * @param: Unit id for copy appliance details into new line
     * @param: service charges id for cop prices nad tax
     * @param: Array, filled drom input form
     */
    function insert_new_unit_item($unit_id, $service_charge_id, $data, $state){
        $price_data = $this->getpricesdetails_with_tax($service_charge_id, $state);
        $this->db->select('booking_id,partner_id,service_id,appliance_id,appliance_brand,appliance_category, appliance_capacity,appliance_size, serial_number, appliance_description, model_number, appliance_tag, purchase_month, purchase_year');

        $this->db->where('id', $unit_id);
        $query = $this->db->get('booking_unit_details');
        $unit_details = $query->result_array();
        if(!empty($data)){
            $result = array_merge($price_data[0], $unit_details[0]);
        } else {
            $result = $unit_details[0];
        }

        $result['booking_status'] = $data['booking_status'];
        $result['partner_paid_basic_charges'] = $result['partner_net_payable'];
        $result['around_paid_basic_charges'] = $result['around_net_payable'] = 0;

        log_message('info', ": " . " insert new item in booking unit details data " . print_r($result, TRUE));

        $this->db->insert('booking_unit_details', $result);
        $new_unit_id = $this->db->insert_id();

        log_message('info', ": " . " insert new item in booking unit details returned id " . print_r($new_unit_id, TRUE));

        $data['id'] = $new_unit_id;
        $data['ud_closed_date'] = date("Y-m-d H:i:s");
        log_message('info', ": " . " update booking unit details data " . print_r($data, TRUE));

        $this->update_unit_details($data);

        return $new_unit_id;
    }

    /**
     *  @desc : This function is to check booking details with their order id weather
     *      they exists or not.
     *
     *  @param : array of order id
     *  @return : if exists returns true else false
     */
    function check_booking_exists_by_order_id($order_id, $partner_src) {
    $this->db->where(array("order_id" => $order_id, "partner_source" => $partner_src));
    $query = $this->db->get('booking_details');

        if (count($query->result_array()) > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function get_brand($where){
        $this->db->select('*');
        $this->db->where($where);
        $query = $this->db->get('appliance_brands');

        return $query->result_array();

    }

    /**
     * @desc: This function is used to insert Sent SMS to database
     * params: Array of data  to be inserted in sms_send_details
     * return: Int(ID) of inserted sms
     */
    function add_sms_sent_details($data){
        $this->db->insert('sms_sent_details', $data);
        return $this->db->insert_id();
    }
    /**
     * @desc: This is used to get Booking_state_change data
     * params: String Booking ID
     * return: Array of data
     *
     */
    function get_booking_state_change_by_id($booking_id){
        $trimed_booking_id = preg_replace("/[^0-9]/","",$booking_id);
        $this->db->select('booking_state_change.agent_id,booking_state_change.partner_id,'
                . ' booking_state_change.service_center_id,booking_state_change.old_state,'
                . ' booking_state_change.new_state,booking_state_change.remarks,booking_state_change.create_date');
        $this->db->like('booking_state_change.booking_id',$trimed_booking_id);
        $this->db->from('booking_state_change');
       
        $this->db->order_by('booking_state_change.id');
        $query = $this->db->get();
        $data =  $query->result_array();
        
        foreach ($data as $key => $value){
            if(!is_null($value['partner_id'])){
                // If Partner Id is 247001
                if($value['partner_id'] == _247AROUND){
                    $sql = "SELECT full_name, bookings_sources.source FROM employee, bookings_sources WHERE "
                            . " bookings_sources.partner_id = '"._247AROUND."' AND employee.id = '".$value['agent_id']."'";
                   
                    $query1 = $this->db->query($sql);
                    $data1 = $query1->result_array();
                   
                    $data[$key]['full_name'] = $data1[0]['full_name'];
                    $data[$key]['source'] = $data1[0]['source'];
                   
                    
                } else {
                    // For Partner
                    $this->db->select('full_name, bookings_sources.source');
                    $this->db->from('partner_login');
                    $this->db->join('bookings_sources','bookings_sources.partner_id = partner_login.partner_id');
                    $this->db->where('partner_login.id', $value['agent_id']);
                    $query1 = $this->db->get();
                    $data1 = $query1->result_array();
                    $data[$key]['full_name'] = $data1[0]['full_name'];
                    $data[$key]['source'] = $data1[0]['source'];
                }
            } else if(!is_null($value['service_center_id'])){
                // For Service center
                $this->db->select("CONCAT('Agent Id: ',service_centers_login.id ) As full_name , CONCAT('SF Id: ',service_centres.id ) As source");
                $this->db->from('service_centers_login');
                $this->db->where('service_centers_login.id', $value['agent_id']);
                $this->db->join('service_centres', 'service_centres.id = service_centers_login.service_center_id');
                $query1 = $this->db->get();
                $data1 = $query1->result_array();
                $data[$key]['full_name'] = $data1[0]['full_name'];
                $data[$key]['source'] = $data1[0]['source'];
            }
            
        }
       
        return $data;

    }
    /**
     * @desc: Get State Change  details
     * @param String $booking_id
     * @return boolean
     */
    function get_booking_state_change($booking_id){
        $trimed_booking_id = preg_replace("/[^0-9]/","",$booking_id);
        $this->db->like('booking_id',$trimed_booking_id);
        $this->db->order_by('booking_state_change.id');
        $query = $this->db->get('booking_state_change');

        if($query->num_rows){
            return $query->result_array();
        } else {
            return false;
        }
    }
    /**
     * @desc: Get booking updation reason
     * parmas: old state, new state, reason_of
     * return: Array
     */
    function get_booking_updation_reason($old_state,$new_state,$reason_of){
        $this->db->select('*');
        $this->db->where('old_state',$old_state);
        $this->db->where('new_state',$new_state);
        $this->db->where('reason_of',$reason_of);
        $this->db->where('active',1);
        $query = $this->db->get('booking_updation_reasons');

        return $query->result_array();
    }

    /**
     * @desc: This is used to get daily and monthly bookings completed and reports
     * params: void
     * return: array
     *
     */
    function get_completed_booking_details(){
        $where_rating = "where DATE_FORMAT(closed_date,'%m') = MONTH(CURDATE()) AND (rating_stars IS NOT NULL OR rating_stars !='')" ;
        $where_booking = "where DATE_FORMAT(closed_date,'%m') = MONTH(CURDATE()) AND current_status='"._247AROUND_COMPLETED."'";
        $where_booking_previous = "where DATE_FORMAT(closed_date,'%m') = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND current_status='"._247AROUND_COMPLETED."'";
        $sql_rating = "SELECT COUNT(id) as ratings from booking_details $where_rating";
        $sql_booking = "SELECT COUNT(id) as bookings from booking_details $where_booking";
        $sql_booking_previous = "SELECT COUNT(id) as bookings from booking_details $where_booking_previous";

        $data['ratings'] = $this->db->query($sql_rating)->row();
        $data['bookings'] = $this->db->query($sql_booking)->row();
        $data['bookings_previous'] = $this->db->query($sql_booking_previous)->row();
        return $data;
    }
    
    /**
     * @desc : This funtion is to get all booking details of particular booking based on booking_primary_contact_no.
     *
     * Finds all the booking details of particular booking of a particular user.
     *
     * @param : booking_primary_contact_no
     * @return : array of booking details
     */
    function getbooking_by_phone($phone) {
        $this->db->select('*');
        $this->db->where('booking_primary_contact_no', $phone);
        $query = $this->db->get('booking_details');
        return $query->result_array();
    }
    
    /**
     * @desc: This fuction is used to delete particular email template from 247around_email_template table
     * params: Int id of the mail template
     * return : Boolean
     * 
     */
    function delete_mail_template_by_id($id) {
        $this->db->where('id', $id);
        $this->db->delete('247around_email_template');

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @desc : This funtion is to get all booking details of particular booking based on booking_primary_contact_no.
     *
     * Finds all the booking details of particular booking of a particular user.
     *
     * @param : booking_primary_contact_no
     * @return : array of booking details
     */
    function get_spare_parts_booking($limit, $start){
        if($limit == "All"){
            $select = "count(spare_parts_details.booking_id) as count";
        } else {
            $select = "spare_parts_details.*, users.name, booking_details.booking_primary_contact_no, service_centres.name as sc_name, bookings_sources.source, booking_details.current_status";
            $this->db->limit($limit, $start);
        }
        $this->db->select($select);
        $this->db->from('spare_parts_details'); 
        $this->db->join('booking_details', 'booking_details.booking_id = spare_parts_details.booking_id');
        $this->db->join('users', 'users.user_id = booking_details.user_id');
        $this->db->join('service_centres','service_centres.id = booking_details.assigned_vendor_id');
        $this->db->join('bookings_sources','bookings_sources.partner_id = booking_details.partner_id');
        $this->db->order_by('spare_parts_details.create_date', 'desc');
        
        $query = $this->db->get();
      
        return $query->result_array();
        
    }
    
    /**
     * @desc: This method increase count in the case reschedule or escalation
     * @param String $booking_id
     * @param String $column_name
     */
    function increase_escalation_reschedule($booking_id, $column_name){
        $sql = "UPDATE booking_details SET $column_name= ($column_name+1) where booking_id = '$booking_id' ";
        return $this->db->query($sql);
    }
    
    function test(){
        $sql = " SELECT DISTINCT vendor_pincode_mapping.`Pincode` FROM `vendor_pincode_mapping`, `service_centres` WHERE `Vendor_ID` = `service_centres`.`id` AND `service_centres`.`active` = 1 ";
        $query = $this->db->query($sql);
        $result = $query->result_array();

        foreach ($result as $key => $value) {
            //print_r($value);
            $sql1 = " SELECT DISTINCT(services) FROM vendor_pincode_mapping, services, service_centres WHERE `vendor_pincode_mapping`.Pincode = '".$value['Pincode']."' AND Vendor_ID != '0' AND Appliance_ID = services.id AND `Vendor_ID` = `service_centres`.`id` AND `service_centres`.`active` = 1";
             $query1 = $this->db->query($sql1);
             $result1 = $query1->result_array();

             $appliance = array();
              foreach ($result1 as $key => $value1) {
                $appliance[] = $value1['services'];
              }
              $str =  implode(", ", $appliance);
              $this->db->where('Pincode', $value['Pincode']);
              $this->db->update('vendor_pincode_mapping_test', array('Appliance'=>$str));
            

        }
        echo "Completed";

    }

    /**
     * @desc: This method  is used to find duplicate booking. If found then cancel booking and return internal status
     * @param Array $requestData
     * @param String $service_id
     */
    function cancel_duplicate_booking_for_sts($requestData, $service_id){
        /*
        log_message('info', __METHOD__);
        $this->db->select('*');
        $this->db->from("booking_details");
        $this->db->join("booking_unit_details","booking_unit_details.booking_id = booking_details.booking_id");
        $this->db->where('booking_primary_contact_no',$requestData['mobile']);
        $this->db->where('booking_pincode',$requestData['pincode']);
        $this->db->where('booking_details.service_id',$service_id);
        $this->db->where('booking_details.partner_id', 1);
        $this->db->where('current_status', "FollowUp");
        $this->db->where('partner_source !=', "STS");
        $this->db->where('booking_unit_details.appliance_description', $requestData['productType']);
        $this->db->order_by("booking_details.id","desc");
        $query = $this->db->get();
        log_message('info', __METHOD__ . $this->db->last_query());
        if($query->num_rows > 0){
            $result = $query->result_array();
            log_message('info', __METHOD__ . " Booking Id found: for cancel: ".$result[0]['booking_id']);
            $data['internal_status'] = "Cancelled";
            $data['cancellation_reason'] = "Duplicate Booking (STS)";
            // Update booking details table
            $this->update_booking($result[0]['booking_id'], $data);
            // Update booking unit details
            $this->update_booking_unit_details($result[0]['booking_id'], array('booking_status'=> 'Cancelled'));
            
            $data1['booking_id'] = $result[0]['booking_id'];
            $data1['new_state'] = "Cancelled";
            $data1['old_state'] = "FollowUp";
            $data1['remarks'] = "Duplicate Booking (STS)";
            $data1['agent_id'] = 1;
            $data1['partner_id'] = _247AROUND;
            // Insert data into booking state change table
            $this->insert_booking_state_change($data1);
            
            return $result[0]['internal_state_change'];
            
        } else {
            return FALSE;
        }
         * 
         */
        
        return FALSE;
    }
    
    /**
     * @Desc: This function is used to get SMS sent for particular booking id
     * @params: booking_id
     * @return: array
     * 
     */
    function get_sms_sent_details($booking_id){
        $trimed_booking_id = preg_replace("/[^0-9]/","",$booking_id);
        $this->db->select('*');
        $this->db->like('booking_id',$trimed_booking_id);
        $query = $this->db->get('sms_sent_details');
        return $query->result_array();
    }


}
