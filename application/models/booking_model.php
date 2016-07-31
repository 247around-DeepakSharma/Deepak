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

    /**
     * @desc: Update appliace details
     * @param:  user id
     * @param: Array
     * @return : void
     */
    function update_appliances($appliance_id, $appliance_details){
        $this->db->where('id', $appliance_id);
        $this->db->update('appliance_details', $appliance_details);
    }

    /**
     *  @desc : add unit details for a booking
     *  @param : booking(appliance) details
     *  @return : none
     */

    function addunitdetails($booking){
        log_message ('info', __METHOD__ . "booking unit details data". print_r($booking, true));
        $this->db->insert('booking_unit_details', $booking);
        return $this->db->insert_id();
    }


    /** @description:* add booking
     *  @param : booking
     *  @return : array (booking)
     */

    function addbooking($booking){
        log_message ('info', __METHOD__ . "booking details data". print_r($booking, true));
        $this->db->insert('booking_details', $booking);
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


//    /**
//     *  @desc : This function gives us the booking details
//     *
//     *  Shows users name, phone number, services name.
//     *
//     *  Also shows complete booking details and also assigned service centre's basic
//     *  details for that particular booking.
//     *
//     *  @param: void
//     *  @return : array of booking details
//     */
//    public function viewbooking() {
//        $query = $this->db->query("Select services.services,
//            users.name as customername, users.phone_number,
//            booking_details.*, service_centres.name as service_centre_name,
//            service_centres.primary_contact_name,service_centres.primary_contact_phone_1
//            from booking_details
//            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
//            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
//            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`"
//        );
//
//        return $query->result();
//    }

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

//    /**
//     *  @desc : Function to view pending and rescheduled bookings in Descending
//     * order according to create date.
//     *
//     *  Here start and limit upto which we want to see bookings is also given
//     *
//     *  Shows users name, phone number, services name.
//     *
//     *  Also shows complete booking details and also assigned service centre's basic
//     *  details for that particular booking.
//     *
//     * This will return all the pending and rescheduled booking for any date.
//     *
//     *  @param: start and limit for records
//     *  @return : array of booking, users, services and service center details in sorted
//     *          format for create date in descending order.
//     */
//    public function view_booking($limit, $start) {
//        $this->db->limit($limit, $start);
//
//        $query = $this->db->query("Select services.services,
//            users.name as customername, users.phone_number,
//            booking_details.*, service_centres.name as service_centre_name,
//            service_centres.primary_contact_name,service_centres.primary_contact_phone_1
//            from booking_details
//            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
//            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
//            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
//            WHERE (booking_details.current_status='Pending' OR booking_details.current_status='Rescheduled')
//            ORDER BY create_date DESC
//            LIMIT $start,$limit"
//        );
//
//        return $query->result();
//    }

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
        $add_limit = "";
        if($limit != "All"){

             $add_limit = " LIMIT $start, $limit ";
        }

         $where = "";

        if($booking_id != ""){
            $where =  "  booking_id = '$booking_id' AND ";
        }

        $query = $this->db->query("Select services.services,
            users.name as customername, users.phone_number,
            booking_details.*, service_centres.name as service_centre_name,
            service_centres.district as city,
           service_centres.primary_contact_name,service_centres.primary_contact_phone_1
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


//    /**
//     *  @desc : Function to view pending and rescheduled bookings in current status sorted descending order.
//     *
//     *  Here start and limit upto which we want to see the output is given.
//     *
//     *  Shows users name, phone number and services name.
//     *
//     *  Also shows booking details and also assigned service centre's basic
//     *  details for that particular booking.
//     *
//     *  @param: start and limit of result
//     *  @return : array of booking, users, services and service center details in sorted
//     *          format by current status in descending order.
//     */
//    function status_sorted_booking($limit, $start) {
//        $query = $this->db->query("Select services.services,
//            users.name as customername, users.phone_number,
//            booking_details.*, service_centres.name as service_centre_name,
//            service_centres.primary_contact_name,service_centres.primary_contact_phone_1
//            from booking_details
//            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
//            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
//            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id` WHERE
//            `booking_id` NOT LIKE '%Q-%' AND
//            (booking_details.current_status='Pending' OR booking_details.current_status='Rescheduled')
//            ORDER BY current_status DESC
//            LIMIT $start,$limit"
//        );
//
//
//        return $query->result();
//    }

//    /**
//     *  @desc : Function to sort pending and rescheduled bookings with service center's name
//     *
//     * 	This method will display all the pending and rescheduled bookings present in
//     *      sorted manner in ascending order according to service centre's name assigned for the booking.
//     *
//     * 	This function is usefull to get all the bookings assigned to particular vendor together.
//     *
//     *  @param : start and limit of result
//     *  @return : assigned vendor sorted bookings
//     */
//    function service_center_sorted_booking($limit, $start) {
//        $query = $this->db->query("Select services.services,
//            users.name as customername, users.phone_number,
//            booking_details.*, service_centres.name as service_centre_name,
//            service_centres.primary_contact_name,service_centres.primary_contact_phone_1
//            from booking_details
//            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
//            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
//            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id` WHERE
//            `booking_id` NOT LIKE '%Q-%' AND
//            (booking_details.current_status='Pending' OR booking_details.current_status='Rescheduled')
//            ORDER BY service_centres.name ASC
//            LIMIT $start,$limit"
//        );
//
//
//        return $query->result();
//    }

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

        if($limit =="All"){
            usort($temp, array($this, 'date_compare_bookings'));
        }


        //return slice of the sorted array
        return array_slice($temp, $start, $limit);
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
        WHERE `booking_id` LIKE '%Q-%' $where  AND
        (booking_details.current_status='$status')";
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
    function cancelreason($reason_of) {
        $query = $this->db->query("Select id,reason from booking_cancellation_reasons where reason_of = '$reason_of' ");
        return $query->result();
    }


//    /**
//     * @desc : This funtion is to get complete booking details
//     * @param : start and limit of data required
//     * @return : array of returned data
//     */
//    public function get_booking($limit, $start) {
//        $this->db->limit($limit, $start);
//        //$this->db->order_by('priority asc');
//        $query = $this->db->get('booking_details');
//        if ($query->num_rows() > 0) {
//            foreach ($query->result() as $row) {
//                $data[] = $row;
//            }
//            return $data;
//            //return $query->result_array();
//        }
//        return false;
//    }

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

//    /**
//     * @desc : This funtion gives user details
//     *
//     * With the help of user id finds home address of that particular user
//     *
//     * @param : user id
//     * @return : user details
//     */
//    function user_details($user_id) {
//        $sql = "Select home_address from users where user_id='$user_id'";
//        $query = $this->db->query($sql);
//
//        return $query->result_array();
//    }
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
     * @desc: get booking history
     * @param: booking id, flag to make join with service center table
     * @return : array
     */
    function getbooking_history($booking_id, $join=""){

        $service_centre = "";
        $condition ="";
        $service_center_name ="";
        if($join !=""){
            $service_center_name =",service_centres.name as vendor_name, service_centres.district ";
            $service_centre = ", service_centres ";
            $condition = " and booking_details.assigned_vendor_id =  service_centres.id";
        }

        $sql = " SELECT `services`.`services`, users.*, booking_details.* ".  $service_center_name
               . "from booking_details, users, services " . $service_centre
               . "where booking_details.booking_id='$booking_id' and "
               . "booking_details.user_id = users.user_id and "
               . "services.id = booking_details.service_id  ". $condition;

        $query = $this->db->query($sql);

        return $query->result_array();
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

    function getCategoryForService($service_id, $state ="", $partner_id="") {
        $where = "";
        if($state != ""){
           $where .= "And state = '$state' AND partner_id = '$partner_id'";

        }
    	$sql = "Select distinct category from service_centre_charges where service_id=
    	'$service_id' $where and active='1' AND check_box ='1' ";

     	$query = $this->db->query($sql);
    	return $query->result_array();
    }

    function getCapacityForCategory($service_id, $category, $state="", $partner_id="") {
        $where = "";
        if($state !=""){
            $where .= "And state = '$state' AND partner_id ='$partner_id' ";
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

    function getPricesForCategoryCapacity($service_id, $category, $capacity, $partner_id ="", $state="") {

        $this->db->distinct();
        $this->db->select('id,service_category,customer_total, partner_net_payable, customer_net_payable');
        $this->db->where('service_id',$service_id);
        $this->db->where('category', $category);
        $this->db->where('active', 1);
        $this->db->where('check_box', 1);

	if($partner_id !=""){
            $this->db->where('partner_id', $partner_id);
            $this->db->where('state', $state);
        }

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
     *  @desc : Function to assign vendor for bookings
     *
     *  This function assigns a particular vendor to a particular booking so that
     *       vendor can complete tasks in the booking.
     *
     *  @param : booking id and service center id
     *  @return : return true is assigned else false
     */
    function assign_booking($booking_id, $service_center) {
        $sql = "Update booking_details set assigned_vendor_id='$service_center' where booking_id='$booking_id'";
        $query = $this->db->query($sql);

        return $query;
    }

    /**
     *  @desc : Function to set mail to vendor field as 1(mail sent)
     *
     *  @param : booking id
     *  @return : void
     */
    function set_mail_to_vendor($booking_id) {
//  unused variable $query, so removed it
//    	$query = $this->db->query("UPDATE booking_details set mail_to_vendor= 1 where booking_id
//                ='$booking_id'");
        $this->db->query("UPDATE booking_details set mail_to_vendor= 1 where booking_id ='$booking_id'");
    }

    /**
     *  @desc : Function to set mail to vendor field as 0(not sent)
     *  @param : booking id
     *  @return : void
     */
    //TODO: can be removed
    function set_mail_to_vendor_flag_to_zero($booking_id) {
//        unused variable $query, so commented it
//	$query = $this->db->query("UPDATE booking_details set mail_to_vendor= 0 where booking_id
//                ='$booking_id'");
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
        $this->db->update("booking_details", $data);
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
    function get_unit_details($booking_id="", $unit_details_id="") {
        $this->db->select('*');
        if($booking_id != ""){
            $this->db->where('booking_id', $booking_id);
        }

        if($unit_details_id !=""){
            $this->db->where('id', $unit_details_id);
        }

        $query = $this->db->get('booking_unit_details');

        return $query->result_array();
    }

//    /**
//     *  @desc : Function to get all booking details where booking type is FollowUp
//     *
//     *  @param : void
//     *  @return : booking detils
//     */
//    function find_followup_users() {
//        $sql = "Select * from booking_details where type='FollowUp'";
//        $query = $this->db->query($sql);
//
//        return $query->result_array();
//    }
//    /**
//     *  @desc : This function is to get jobcard file name.
//     *  @param : booking id
//     *  @return : void
//     */
//    function jobcard($booking_id) {
//        $sql = "Select booking_jobcard_filename from booking_details where booking_id=$booking_id
//                and booking_jobcard_filename is NULL";
//    }

    /**
     *  @desc : This function is to get appliance count of a user
     *
     *  Gives the number of appliances that are active for a particular user.
     *
     *  @param : user id
     *  @return : count of active appliances for a particular user
     */
    function getApplianceCountByUser($user_id) {
        //log_message('info', __METHOD__ . "=> User ID: " . $user_id);

        $this->db->where(array('user_id' => $user_id, 'is_active' => '1'));
        $this->db->from("appliance_details");

        $result = $this->db->count_all_results();

        //log_message('info', __METHOD__ . " -> Result: " . $result);

        return $result;
    }

    /**
     *  @desc : This function is to get appliance details
     *
     *  Gives the number of appliances that are active for a particular user.
     *
     *  @param : appliance id
     *  @return : appliance details
     */
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
//	$query = $this->db->query($sql); // as $query was not been used, this linecould be deleted
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

        $services = $query->result_array();
        return $services[0]['id'];
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
     *  @desc : This function is to update snapdael leads
     *
     *  @param : array_where(which leads to update) and data to update
     *  @return : void
     */
    function update_sd_lead($array_where, $array_data) {
        $this->db->where($array_where);
        $this->db->update("snapdeal_leads", $array_data);
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

    /**
     *  @desc : Function to get pending queries according to pagination.
     *          Queries which have booking date of future are not shown. Queries with
     *          empty booking dates are shown.
     *  @param : start and limit for the query
     *  @return : array(specific no of pending query detils)
     */
    function get_queries($limit, $start, $status, $booking_id = "") {
        $where = "";
        $add_limit = "";

        if ($booking_id != "") {
            $where .= "AND `booking_details`.`booking_id` = '$booking_id' AND (`booking_details`.current_status='Completed' OR `booking_details`.current_status='Cancelled') ";
        } else {
            if ($limit != 'All') {
                $where .= "AND (DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= 0 OR
                booking_details.booking_date='') AND `booking_details`.current_status='$status' ";

                $add_limit = " limit $start, $limit ";


            }
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
            WHERE `booking_id` LIKE '%Q-%' $where

        order by CASE booking_date
            WHEN '' THEN 'a'
            ELSE 'b'
            END, STR_TO_DATE(booking_date,'%d-%m-%Y') desc $add_limit";

        $query = $this->db->query($sql);

        $temp = $query->result();

        usort($temp, array($this, 'date_compare_queries'));

        $data = $this->searchPincodeAvailable($temp);

        return $data;

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
            $this->db->where('vendor_pincode_mapping.Appliance_ID', $value->service_id);
            $this->db->where('vendor_pincode_mapping.Pincode', $value->booking_pincode);
            $this->db->where('vendor_pincode_mapping.active', "1");
            $this->db->from('vendor_pincode_mapping');

            $this->db->join('service_centres', 'service_centres.id = vendor_pincode_mapping.Vendor_ID');

            $this->db->where('service_centres.active', "1");
            $data = $this->db->get();
            if ($data->num_rows() > 0) {
                $temp[$key]->vendor_status = $data->result_array();
            } else {
                $temp[$key]->vendor_status = "Vendor Not Available";
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

//    /**
//     *  @desc : Function to add single appliance while converting query to booking
//     *
//     *  @param : appliance details
//     *  @return : appliance details after insertion
//     */
//    function addsingleappliance($booking) {
//        $appliance_detail = array("user_id" => $booking['user_id'],
//            "service_id" => $booking['service_id'],
//            "brand" => $booking['appliance_brand'],
//            "category" => $booking['appliance_category'],
//            "capacity" => $booking['appliance_capacity'],
//            "model_number" => $booking['model_number'],
//            "purchase_year" => $booking['purchase_year'],
//            "tag" => $booking['appliance_tag'],
//            "last_service_date" => date('Y-m-d H:i:s'));
//        $this->db->insert('appliance_details', $appliance_detail);
//        $id = $this->db->insert_id();
//
//        //Just after insertion, use newly generated id to get inserted recods.
//        $sql = "SELECT * FROM appliance_details WHERE id = $id";
//        $query = $this->db->query($sql);
//        return $query->result_array();
//    }
//
//    /**
//     *  @desc : Function to add single unit details while working with query
//     *
//     *  Through this we can insert a single unit(single appliance)while taking booking
//     *
//     *  @param : appliance details
//     *  @return : void
//     */
//    function add_single_unit_details($booking) {
//        $unit_detail = array("booking_id" => $booking['booking_id'],
//            "appliance_brand" => $booking['appliance_brand'],
//            "appliance_category" => $booking['appliance_category'],
//            "appliance_capacity" => $booking['appliance_capacity'],
//            "model_number" => $booking['model_number'],
//            "price_tags" => $booking['items_selected'],
//            "purchase_year" => $booking['purchase_year'],
//            "total_price" => $booking['total_price'],
//            "appliance_tag" => $booking['appliance_tag']);
//
//        $this->db->insert('booking_unit_details', $unit_detail);
//    }

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

    /**
     *  @desc : Find potential SCs for an Appliance in a Pincode
     *  @param : appliance id and pincode
     *  @return : returns available service centers
     */
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
    //TODO: can be removed
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
    function get_price_mapping_partner_code($partner_code) {
	$this->db->select('price_mapping_id');
	$this->db->where('code', $partner_code);
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
    if($status !="")
	$this->db->where_not_in('current_status', $status);

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
        $status = array('Completed', 'Cancelled', 'Pending');
        $charges = $this->service_centers_model->getcharges_filled_by_service_center($booking_id, $status);
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
        $this->db->select("template, to, from");
        $this->db->where('tag', $email_tag);
        $this->db->where('active', 1);
        $query = $this->db->get('email_template');
        if ($query->num_rows > 0) {
            $template = $query->result_array();
            return array($template[0]['template'], $template[0]['to'], $template[0]['from']);
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

    /**
     * @desc : This function is used to get booking id with the help of order id.
     *
     *  Partner id and order id finds the exact booking id.
     *
     * @param : partner id and order id.
     * @return : Array(booking details)
     */
    //TODO: can be removed
    function getBookingId_by_orderId($partner_id, $order_id) {

        $booking = array();

        $partner_code = $this->partner_model->get_source_code_for_partner($partner_id);

        $union = "";
        if ($partner_code == "SS") {
            $union = "UNION

                   SELECT CRM_Remarks_SR_No as booking FROM  `snapdeal_leads` WHERE  Sub_Order_ID LIKE  '%$order_id%' ";
        }

        $sql = "SELECT 247aroundBookingID  as booking from partner_leads where OrderID LIKE '%$order_id%' And  PartnerID = '$partner_id'   " . $union;


        $query = $this->db->query($sql);

        $data = $query->result();

        if (count($data) > 0) {

            foreach ($data as $value) {
                $string = preg_replace("/[^0-9,.]/", "", $value->booking); //replace all character and symbol
                $booking_data = $this->search_bookings_by_booking_id($string);

                if (count($booking_data) > 0) {
                    array_push($booking, $booking_data[0]);
                }
            }

            return $booking;
        } else {

            return $booking;
        }
    }

    function check_price_tags_status($booking_id, $price_tags){
        $this->db->select('id, price_tags');
        $this->db->where('booking_id', $booking_id);
        $query = $this->db->get('booking_unit_details');
        if($query->num_rows>0){
            $result = $query->result_array();
            foreach ($result as $key => $value) {
                 if (in_array($value['price_tags'], $price_tags)) {
                   // echo "Match found";
                 }
                 else {
                    //echo "Match not found";
                   $data = array('booking_status' => "Cancelled" );
                   $this->db->where('id', $value['id']);
                   $this->db->update('booking_unit_details', $data);

                }
            }
        }
        return;
    }

    function getpricesdetails_with_tax($service_centre_charges_id){

        $sql =" SELECT service_category as price_tags, customer_total, partner_net_payable, rate as tax_rate, product_or_services from service_centre_charges, tax_rates where `service_centre_charges`.id = '$service_centre_charges_id' AND `tax_rates`.tax_code = `service_centre_charges`.tax_code AND `tax_rates`.state = `service_centre_charges`.state AND `tax_rates`.product_type = `service_centre_charges`.product_type AND (to_date is NULL or to_date >= CURDATE() ) AND `tax_rates`.active = 1 ";

        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * @desc: This method get prices details and check price tag is exist in unit details or not.
     * If price tags does not exist, it inserts data in booking unit details and if price tags exist,
     * it update booking unit details.
     * @param: Array
     * @return: Price tags.
     */
    function update_booking_in_booking_details($services_details){

        $data = $this->getpricesdetails_with_tax($services_details['id']);

        $result = array_merge($services_details, $data[0]);

        unset($result['id']);  // unset service center charge  id  because there is no need to insert id in the booking unit details table
         $result['customer_net_payable'] = $result['customer_total'] - $result['partner_paid_basic_charges'] - $result['around_paid_basic_charges'];
               //log_message ('info', __METHOD__ . "update booking_unit_details data". print_r($result));

        $this->db->select('id');
        $this->db->where('appliance_id', $services_details['appliance_id']);
        $this->db->where('price_tags', $data[0]['price_tags']);
        $this->db->where('booking_id', $services_details['booking_id']);
        $query = $this->db->get('booking_unit_details');

        if($query->num_rows >0){

            $unit_details = $query->result_array();
            $this->db->where('id',  $unit_details[0]['id']);
            $this->db->update('booking_unit_details', $result);

         } else {

            $this->db->insert('booking_unit_details', $result);
         }

         return $data[0]['price_tags'];
    }

    // Update Price in unit details
    function update_price_in_unit_details($data, $unit_details){

        $data['around_paid_basic_charges'] = $unit_details[0]['around_paid_basic_charges'];
        $data['partner_paid_basic_charges'] = $unit_details[0]['partner_paid_basic_charges'];
        $data['tax_rate'] = $unit_details[0]['tax_rate'];


        $vendor_total_basic_charges =  ($data['customer_paid_basic_charges'] + $data['partner_paid_basic_charges'] + $data['around_paid_basic_charges']) * basic_percentage;
        $around_total_basic_charges = ($data['customer_paid_basic_charges'] + $data['partner_paid_basic_charges'] + $data['around_paid_basic_charges'] - $vendor_total_basic_charges);

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

        $vendor_around_charge = ($data['customer_paid_basic_charges'] + $data['customer_paid_parts'] + $data['customer_paid_extra_charges']) - ($vendor_total_basic_charges + $total_vendor_addition_charge + $total_vendor_parts_charge );

        if($vendor_around_charge > 0){

            $data['vendor_to_around'] = $vendor_around_charge;
            $data['around_to_vendor'] = 0;

        } else {
            $data['vendor_to_around'] = 0;
            $data['around_to_vendor'] = abs($vendor_around_charge);
        }
        unset($data['internal_status']);
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

        } else if($appliance_id !=""){
            $where = " `booking_unit_details`.appliance_id = '$appliance_id' ";
        }

        $sql = "SELECT distinct(appliance_id), brand, booking_id, category, capacity, `appliance_details`.`model_number`,description, `appliance_details`.`purchase_month`, `appliance_details`.`purchase_year`, appliance_tag
            from booking_unit_details,  appliance_details Where $where  AND `appliance_details`.`id` = `booking_unit_details`.`appliance_id`  ";

        $query = $this->db->query($sql);
        $appliance =  $query->result_array();

        foreach ($appliance as $key => $value) {
            // get data from booking unit details table on the basis of appliance id
            $this->db->select('id as unit_id, price_tags, customer_total, around_net_payable, partner_net_payable, customer_net_payable, customer_paid_basic_charges, customer_paid_extra_charges, customer_paid_parts, booking_status, partner_paid_basic_charges,product_or_services');
            $this->db->where('appliance_id', $value['appliance_id']);
            $this->db->where('booking_id', $value['booking_id']);
            $query2 = $this->db->get('booking_unit_details');

            $result = $query2->result_array();
            $appliance[$key]['qunatity'] = $result; // add booking unit details array into quantity key of previous array
        }

        return $appliance;
    }

    /**
     * @desc: update price in booking unit details
     */
    function update_unit_details($data){
        // get booking unit data on the basis of id
        $this->db->select('around_net_payable, partner_net_payable, tax_rate, price_tags, partner_paid_basic_charges, around_paid_basic_charges');
        $this->db->where('id', $data['id']);
        $query = $this->db->get('booking_unit_details');
        $unit_details = $query->result_array();

        if($data['booking_status'] == "Completed"){

            $this->update_price_in_unit_details($data, $unit_details);

        } else {

            $data['customer_total'] = 0;
            $unit_details[0]['partner_net_payable'] = 0;
            $unit_details[0]['around_net_payable'] =0;
            $unit_details[0]['tax_rate'] = 0;
            $data['customer_net_payable'] = 0;
            $data['partner_paid_basic_charges'] = 0;
            $data['around_paid_basic_charges'] = 0;


            // Update price in unit table
            $this->update_price_in_unit_details($data, $unit_details);
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
    function insert_data_in_booking_unit_details($services_details){
        $data = $this->getpricesdetails_with_tax($services_details['id']);

        $result = array_merge($services_details, $data[0]);

        unset($result['id']);  // unset service center charge  id  because there is no need to insert id in the booking unit details table
        $result['customer_net_payable'] = $result['customer_total'] - $result['partner_paid_basic_charges'] - $result['around_paid_basic_charges'];
        log_message ('info', __METHOD__ . "booking_unit_details data". print_r($result, true));
        $this->db->insert('booking_unit_details', $result);

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

    //return $this->db->insert_id();
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
    $this->db->where(array('booking_id' => $booking_id, 'current_status' => $status));
    $this->db->update('booking_details', $data);
    }


}
