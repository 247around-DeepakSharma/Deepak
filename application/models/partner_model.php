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
      $query = $this->db->get("partner_leads");
      $results = $query->result_array();

      if (count($results) > 0) {
        return $results[0];
      } else {
        return NULL;
      }
    }

    //Find order id for a partner
    function get_order_id_for_partner($partner_id, $order_id) {
      $this->db->where(array("partner_id" => $partner_id, "order_id" => $order_id));
      $query = $this->db->get("booking_details");
      $results = $query->result_array();

      if (count($results) > 0) {
        return $results[0];
      } else {
        return NULL;
      }
    }

    //Find OrderID for 247aroundBooking ID
    function get_order_id_by_booking_id($booking_id) {
      $this->db->like(array("booking_id" => $booking_id));
      $query = $this->db->get("booking_details");
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

    //Return Partner ID from Booking Source
    //Default 'Other'
    function get_partner_id_from_booking_source_code($source) {
  $this->db->where(array("code" => $source));
  $query = $this->db->get('bookings_sources');
  $results = $query->result_array();

  return $results[0]['partner_id'];
    }

    function get_all_partner_source($flag="", $source= ""){
      $this->db->select("bookings_sources.partner_id,source,code,price_mapping_id, partner_type");
        $this->db->order_by('source','ASC');
        if($flag ==""){
        $this->db->where('partner_id !=', 'NULL');
        }

        if($source !=""){
            $this->db->where('code', $source);
        }

      $query = $this->db->get("bookings_sources");
      return $query->result_array();
    }

    function insert_data_in_batch($table_name, $rows,$flag=""){
        if($flag == ""){
            $this->db->truncate($table_name);
        }
        return $this->db->insert_batch($table_name, $rows);
    }

    /*
     * @desc: This is used to get active partner details and also get partner details by partner id
     */
    function getpartner($partner_id = "") {
	    if ($partner_id != "") {
	        $this->db->where('id', $partner_id);
	    }
	    $this->db->select('*');
	    $this->db->where('is_active', '1');
	    $query = $this->db->get('partners');

	    return $query->result_array();
    }

    /**
 * @desc: this method return partner data if need to call partner api other wise return false
 * @param: booking id
 */
    function get_data_for_partner_callback($booking_id){
        $this->db->select('*');
        $this->db->where('booking_id', $booking_id);
        $this->db->where('partner_id IS NOT NULL', null, false);
        $query = $this->db->get('booking_details');
        if($query->num_rows >0 ){

           $result = $query->result_array();
           $this->db->select('*');
           $this->db->where('partner_id', $result[0]['partner_id']);
           $this->db->where('callback_string',$result[0]['partner_source']);
           $this->db->where('active',"1");
           $query1 = $this->db->get('partner_callback');

           if($query1->num_rows > 0){

               return $result[0];

           } else {

              return false;
           }


        } else {

            return false;
        }

    }

     /**
     * @desc: check partner login and return pending booking
     * @param: Array(username, password)
     * @return : Array(Pending booking)
     */
    function partner_login($data){
       $this->db->select('id, partner_id');
       $this->db->where('user_name',$data['user_name']);
       $this->db->where('password',$data['password']);
       $this->db->where('active',1);
       $query = $this->db->get('partner_login');

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0];

      } else {

        return false;
      }

    }

    /**
      * @desc: this is used to get pending booking for specific partner id
      * @param: end limit, start limit, partner id
      * @return: Pending booking
      */
     function getPending_booking($partner_id ,$booking_id = ''){
        $where = "";
        $where .= " AND booking_details.partner_id = '" . $partner_id . "'";
        if(!empty($booking_id)){
            $where .= " AND `booking_details`.booking_id = '".$booking_id."'";
        }
        //do not show bookings for future as of now
        //$where .= " AND DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= 0";

          $query = $this->db->query("Select services.services,
            users.name as customername, users.phone_number,
            booking_details.*, status

            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN spare_parts_details ON spare_parts_details.booking_id = booking_details.booking_id

            WHERE
            `booking_details`.booking_id NOT LIKE 'Q-%' $where AND
            (booking_details.current_status='Pending' OR booking_details.current_status='Rescheduled') 
            AND booking_details.upcountry_partner_approved ='1'"
        );
          
          $temp = $query->result();
          usort($temp, array($this, 'date_compare_bookings'));
          return $temp;
     }


    /**
      * @desc: this is used to get pending queries for specific partner id
      * @param: end limit, start limit, partner id
      * @return: Pending Queries
      */
     function getPending_queries($partner_id ){
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
        
        $temp = $query->result();
        usort($temp, array($this, 'date_compare_bookings'));

        return $temp;

     }

     /**
      * @desc: This is used to get close booking of custom partner
      * @param: End limit
      * @param: Start limit
      * @param: Partner Id
      * @param: Booking Status(Cancelled or Completed)
      * @return: Array()
      */
      function getclosed_booking($limit, $start, $partner_id, $status, $booking_id = ""){
        if($limit!="count"){
            $this->db->limit($limit, $start);
        }

        $this->db->select('request_type,booking_details.booking_id, users.name as customername, booking_details.booking_primary_contact_no, services.services, booking_details.booking_date, booking_details.closing_remarks, booking_details.booking_timeslot, booking_details.city, booking_details.cancellation_reason, booking_details.order_id');
        $this->db->from('booking_details');
        $this->db->join('services','services.id = booking_details.service_id');
        $this->db->join('users','users.user_id = booking_details.user_id');
        $this->db->where('booking_details.current_status', $status);
        if(!empty($booking_id)){
            $this->db->where('booking_details.booking_id', $booking_id);
        }
        $this->db->where('partner_id',$partner_id);
        $this->db->order_by('booking_details.closed_date','desc');
        $query = $this->db->get();
        
        $result = $query->result_array();

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

    function get_authentication_code($partner_id) {
	$this->db->select('auth_token');
	$this->db->where('id', $partner_id);
	$this->db->where('is_active', '1');
	$query = $this->db->get('partners');
	if ($query->num_rows > 0) {
	    return $query->result_array()[0]['auth_token'];
	} else {
	    return false;
	}
    }

    /**
     * @desc: This method gets price details for partner
     */
    function getPrices($service_id, $category, $capacity, $partner_id, $service_category,$brand ="") {
	$this->db->distinct();
	$this->db->select('id,service_category,customer_total, partner_net_payable, customer_net_payable, pod, is_upcountry');
	$this->db->where('service_id', $service_id);
	$this->db->where('category', $category);
	$this->db->where('active', 1);
	$this->db->where('check_box', 1);
	$this->db->where('partner_id', $partner_id);
        if($service_category !=""){
	   $this->db->where('service_category', $service_category);
        }

	if (!empty($capacity)) {
	    $this->db->where('capacity', $capacity);
	}
        if(!empty($brand)){
            $this->db->where('brand', $brand);
        }

	$query = $this->db->get('service_centre_charges');

	return $query->result_array();
    }
    
    function get_service_category($service_id, $category, $capacity, $partner_id, $service_category,$brand ="") {
	$this->db->distinct();
	$this->db->select('service_category');
	$this->db->where('service_id', $service_id);
	$this->db->where('category', $category);
	$this->db->where('active', 1);
	$this->db->where('check_box', 1);
	$this->db->where('partner_id', $partner_id);
        if($service_category !=""){
	   $this->db->where('service_category', $service_category);
        }

	if (!empty($capacity)) {
	    $this->db->where('capacity', $capacity);
	}
        if(!empty($brand)){
            $this->db->where('brand', $brand);
        }

	$query = $this->db->get('service_centre_charges');

	return $query->result_array();
    }
    
    //Return all leads shared by Partner in the last 30 days in CSV
    function get_partner_leads_csv_for_summary_email($partner_id)
    {


        return $query = $this->db->query("SELECT distinct '' AS 'Unique id',
            order_id AS 'Sub Order ID',
            booking_details.create_date AS 'Referred Date and Time', 
            ud.appliance_brand AS 'Brand', 
            IFNULL(model_number,'') AS 'Model', 
            services AS 'Product', 
            ud.appliance_description As 'Description',
            name As 'Customer', 
            home_address AS 'Customer Address', 
            booking_pincode AS 'Pincode', 
            booking_details.city As 'City', 
            booking_primary_contact_no AS Phone, 
            user_email As 'Email ID', 
            request_type AS 'Call Type (Installation /Table Top Installation/Demo/ Service)', 
            partner_current_status AS 'Status By Brand', 
            '' AS 'Remarks by Brand',
            'Service sent to vendor' AS 'Status by Partner', 
            booking_date As 'Scheduled Appointment Date(DD/MM/YYYY)', 
            booking_timeslot AS 'Scheduled Appointment Time(HH:MM:SS)', 
            partner_internal_status AS 'Final Status'
            FROM  booking_details , booking_unit_details AS ud, services, users
            WHERE booking_details.booking_id = ud.booking_id 
            AND booking_details.service_id = services.id 
            AND booking_details.user_id = users.user_id
            AND booking_details.partner_id = $partner_id
            AND booking_details.create_date > (CURDATE() - INTERVAL 1 MONTH) 
            AND booking_details.partner_current_status != 'DUPLICATE_BOOKING'");

    } 
    
    //Return all leads shared by Partner in the last 30 days
    function get_partner_leads_for_summary_email($partner_id) {
	$query = $this->db->query("SELECT DISTINCT BD.booking_id, order_id, booking_date, booking_timeslot,
			BD.current_status, BD.cancellation_reason, rating_stars,BD.partner_current_status,BD.partner_internal_status,
			DATE_FORMAT(BD.create_date, '%d/%M') as create_date,
			services,
			UD.appliance_brand as brand, UD.model_number, UD.appliance_description as description,
			name, phone_number, home_address, pincode, users.city
			FROM booking_details as BD, users, services, booking_unit_details as UD
			WHERE BD.booking_id NOT REGEXP '^Q-' AND
			BD.booking_id = UD.booking_id AND
			BD.service_id = services.id AND
			BD.user_id = users.user_id AND
			BD.partner_id = $partner_id AND
			BD.create_date > (CURDATE() - INTERVAL 1 MONTH) AND
			DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(BD.booking_date, '%d-%m-%Y')) >= 0");

	return $query->result_array();
    }

    //Get partner summary parameters for daily report
    function get_partner_summary_params($partner_id) {
	$partner_source_code = $this->get_source_code_for_partner($partner_id);

	//Count all partner leads
	$this->db->like('partner_id', $partner_id);
	$total_install_req = $this->db->count_all_results('booking_details');

	//Count today leads which has create_date as today
	$this->db->where('partner_id', $partner_id);
	$this->db->where('create_date >= ', date('Y-m-d'));
	$today_install_req = $this->db->count_all_results('booking_details');

	//Count y'day leads
	$this->db->where('partner_id', $partner_id);
	$this->db->where('create_date >= ', date('Y-m-d', strtotime("-1 days")));
	$this->db->where('create_date < ', date('Y-m-d'));
	$yday_install_req = $this->db->count_all_results('booking_details');

	//Count This month leads
	$sql = "SELECT * FROM booking_details WHERE partner_id = '" . $partner_id . "'"
	    . " AND create_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
	$query = $this->db->query($sql);
	$month_array = $query->result_array();
	$month_install_req = count($month_array);


	//Count total installations scheduled
	$this->db->where('partner_id', $partner_id);
	$this->db->where_in('current_status', array('Pending', 'Rescheduled'));
	$total_install_sched = $this->db->count_all_results('booking_details');

	//Count today installations scheduled
        $this->db->distinct();
        $this->db->select('count(booking_id) as count');
	$this->db->like('booking_id', $partner_source_code);
	$this->db->where('new_state', 'Pending');
	$this->db->where('create_date >= ', date('Y-m-d'));
        $sched_count = $this->db->get('booking_state_change');
        $sched_c1 = $sched_count->result_array();
	$today_install_sched = $sched_c1[0]['count'];

	//Count y'day installations scheduled
        $this->db->distinct();
        $this->db->select('count(booking_id) as count');
	$this->db->like('booking_id', $partner_source_code);
	$this->db->where('new_state', 'Pending');
	$this->db->where('create_date >= ', date('Y-m-d', strtotime("-1 days")));
	$this->db->where('create_date < ', date('Y-m-d'));
        $y_day_count = $this->db->get('booking_state_change');
        $yday_c1 = $y_day_count->result_array();
	$yday_install_sched = $yday_c1[0]['count'];

	//Count This month installation scheduled
	$sql = "SELECT distinct(booking_id) FROM booking_state_change WHERE booking_id LIKE '%" . $partner_source_code . "%'"
	    . " AND create_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH) "
	    . " AND (new_state = 'Pending' OR new_state = 'Rescheduled') "
	    . " AND (old_state = 'FollowUp' OR old_state = 'New_Booking' ) ";
	$install_query = $this->db->query($sql);
	$month__scheduled = $install_query->result_array();
	$month_install_scheduled = count($month__scheduled);

	//Count total installations completed
	$this->db->where('partner_id', $partner_id);
	$this->db->where_in('current_status', array('Completed'));
	$total_install_compl = $this->db->count_all_results('booking_details');

	//Count today installations completed
	$this->db->where('partner_id', $partner_id);
	$this->db->where_in('current_status', array('Completed'));
	$this->db->where('closed_date >= ', date('Y-m-d'));
	$today_install_compl = $this->db->count_all_results('booking_details');

	//Count y'day installations completed
	$this->db->where('partner_id', $partner_id);
	$this->db->where_in('current_status', array('Completed'));
	$this->db->where('closed_date >= ', date('Y-m-d', strtotime("-1 days")));
	$this->db->where('closed_date < ', date('Y-m-d'));
	$yday_install_compl = $this->db->count_all_results('booking_details');

	//Count this month installations completed
	$sql = "SELECT * FROM booking_details WHERE partner_id = '" . $partner_id . "'"
	    . " AND closed_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH) "
	    . "AND current_status = 'Completed' ";
	$comp_query = $this->db->query($sql);
	$month_comp = $comp_query->result_array();
	$month_install_comp = count($month_comp);

	//Count total follow-ups pending
	$this->db->where('partner_id', $partner_id);
	$this->db->where('current_status', 'FollowUp');
	$total_followup_pend = $this->db->count_all_results('booking_details');

	//Count today follow-ups pending
	$today = date("d-m-Y");
	$where_today = "`partner_id` =  '" . $partner_id . "' AND `current_status`='FollowUp' AND (`booking_date`='' OR `booking_date`=$today)";
	$this->db->where($where_today);
	$today_followup_pend = $this->db->count_all_results('booking_details');

	//Count yday follow-ups pending
	$yday = date("d-m-Y", strtotime("-1 days"));
	$where_yday = "`partner_id` = '" . $partner_id . "' AND `current_status`='FollowUp' AND `booking_date`=$yday";
	$this->db->where($where_yday);
	$yday_followup_pend = $this->db->count_all_results('booking_details');

	//Count this follow-ups pending
	$sql = "SELECT distinct(booking_id) FROM booking_state_change WHERE booking_id LIKE '%" . $partner_source_code . "%'"
	    . " AND create_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH) "
	    . "AND (new_state = 'FollowUp' OR old_state = 'New_Query' ) ";
	$followUp_query = $this->db->query($sql);
	$followUp_comp = $followUp_query->result_array();
	$month_followup_pend = count($followUp_comp);

	//Count total installations Cancelled
	$this->db->where('source', $partner_source_code);
	$this->db->where('current_status', 'Cancelled');
	$total_install_cancl = $this->db->count_all_results('booking_details');

	//Count today installations Cancelled
	$this->db->like('booking_id', $partner_source_code);
	$this->db->where('new_state', 'Cancelled');
	$this->db->where('create_date >= ', date('Y-m-d'));
	$today_install_cancl = $this->db->count_all_results('booking_state_change');

	//Count y'day installations Cancelled
	$this->db->like('booking_id', $partner_source_code);
	$this->db->where('new_state', 'Cancelled');
	$this->db->where('create_date >= ', date('Y-m-d', strtotime("-1 days")));
	$this->db->where('create_date < ', date('Y-m-d'));
	$yday_install_cancl = $this->db->count_all_results('booking_state_change');

	//Count this month installations Cancelled
	$sql = "SELECT * FROM booking_details WHERE partner_id = '" . $partner_id . "'"
	    . " AND closed_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH) "
	    . "AND current_status = 'Cancelled' ";
	$can_query = $this->db->query($sql);
	$month_install_can = $can_query->result_array();
	$month_install_cancl = count($month_install_can);

	//TAT calculation
	$tat = "100";
	//SELECT DATEDIFF(`closed_date`, STR_TO_DATE(`booking_date`,"%d-%m-%Y")) FROM `booking_details` where source=$partner_source_code AND current_status='Completed'
	//Average Rating
//	$this->db->where('Rating_Stars !=', '');
//	$this->db->select_avg('Rating_Stars');
//	$query = $this->db->get('snapdeal_leads');
//	$avg_rating = $query->result_array()[0]['Rating_Stars'];

	$result = array(
	    "total_install_req" => $total_install_req,
	    "today_install_req" => $today_install_req,
	    "yday_install_req" => $yday_install_req,
	    "month_install_req" => $month_install_req,
	    "total_install_sched" => $total_install_sched,
	    "today_install_sched" => $today_install_sched,
	    "yday_install_sched" => $yday_install_sched,
	    "month_install_sched" => $month_install_scheduled,
	    "total_install_compl" => $total_install_compl,
	    "today_install_compl" => $today_install_compl,
	    "yday_install_compl" => $yday_install_compl,
	    "month_install_compl" => $month_install_comp,
	    "total_followup_pend" => $total_followup_pend,
	    "today_followup_pend" => $today_followup_pend,
	    "yday_followup_pend" => $yday_followup_pend,
	    "month_followup_pend" => $month_followup_pend,
	    "total_install_cancl" => $total_install_cancl,
	    "today_install_cancl" => $today_install_cancl,
	    "yday_install_cancl" => $yday_install_cancl,
	    "month_install_cancl" => $month_install_cancl,
	    "tat" => $tat,
	);

	return $result;
    }
    /**
     * @desc: This function is to add a new partner
     *
     * partner details like Service Center's name, owners name, ph no., email, poc name, email, services, brands covered,
     *      bank details, etc.
     *
     * @param: $partner
     *          - partner details to be added.
     * @return: ID for the new partner
     */
    function add_partner($partner) {
        $this->db->insert('partners', $partner);
        return $this->db->insert_id();
    }
    /**
     * @desc: This function is to view partner details
     *
     * If partner_id is given then the details of the specific partner will come else
     *  the details of all the partner will be be returned
     *
     * @param: $partner_id
     * @return: array of partner details
     */
    function viewpartner($partner_id = "") {
        $where = "";

        if ($partner_id != "") {
            $where .= "where id= '$partner_id'";
            $sql = "Select * from partners $where";
        } else {
            $where .="JOIN partner_login ON partner_login.partner_id = partners.id";
            $sql = "Select partners.id, partners.company_name, partners.public_name, partners.address, partners.primary_contact_name,"
                    . "partners.primary_contact_phone_1, partners.primary_contact_email, partners.owner_name,"
                    . "partners.owner_phone_1, partners.owner_email, partners.is_active, partner_login.user_name from partners $where";
        }
        $query = $this->db->query($sql);

        return $query->result_array();
    }

    /**
     * @desc: This function is to activate partner who is already registered with us and are inactive/deactivated.
     *
     * @param: $id
     *         - Id of partner to whom we would like to activate
     * @return: void
     */
    function activate($id) {
        $sql = "Update partners set is_active= 1 where id='$id'";
        $this->db->query($sql);
    }

    /**
     * @desc: This function is to deactivate partner who is already registered with us and are active.
     *
     * @param: $id
     *         - Id of partner to whom we would like to deactivate
     * @return: void
     */
    function deactivate($id) {
        $sql = "Update partners set is_active= 0 where id='$id'";
        $this->db->query($sql);
    }
    /**
     * @desc: This function edits partner's details
     *
     * If details of partner that are edited will be modified else others details will remain same.
     *
     * @param: $partner
     *          - Array of all the partner details to be edited.
     * @param: $id(partner_id)
     *          - Id of partner which is to be edited.
     * @return: none
     */
    function edit_partner($partner, $id) {
        $this->db->where('id', $id);
        $this->db->update('partners', $partner);
    }

    /**
     * @desc : This funtion counts total number of bookings for a particular user of the concerned partner
     *
     * Counts the number of bookings with same user id and partner  id
     *
     * @param : user id, partner ID
     * @return : total number of bookings for particular user and partner
     */
    public function total_user_booking($user_id,$partner_id) {
        $this->db->where("user_id = '$user_id'");
        $this->db->where("partner_id = '$partner_id'");
        $result = $this->db->count_all_results("booking_details");
        return $result;
    }

    /**
     *
     * @param Array $where
     * @param String $is_reporting_mail (O or 1)
     * @return Array
     */
    function getpartner_details($select, $where, $is_reporting_mail="") {

	$this->db->select($select);
	$this->db->where($where);
        $this->db->from('partners');
	if ($is_reporting_mail != "") {
	    $this->db->where_in('is_reporting_mail', $is_reporting_mail);
	}
        $this->db->join('bookings_sources','bookings_sources.partner_id = partners.id','right');
	$query = $this->db->get();

	return $query->result_array();
    }
    
    /**
     * @desc: This is used to get required Spare Parts Booking
     * @param Array $where
     * @return Array
     */
    function get_spare_parts_booking($where){
        $sql = "SELECT spare_parts_details.*, users.name, booking_details.booking_primary_contact_no, "
                . " booking_details.booking_address,booking_details.initial_booking_date,"
                . " service_centres.name as vendor_name, service_centres.address, service_centres.state, "
                . " service_centres.pincode, service_centres.district"
                . " FROM spare_parts_details,booking_details,users, "
                . " service_centres WHERE booking_details.booking_id = spare_parts_details.booking_id"
                . " AND users.user_id = booking_details.user_id AND service_centres.id = spare_parts_details.service_center_id "
                . " AND ".$where . "  ORDER BY spare_parts_details.create_date ASC";
        $query = $this->db->query($sql);
       
        return $query->result_array();
    }
    /**
     * @desc: This is used to return Spare booking List
     * @param String $where
     * @param integer/boolean $start
     * @param integer/boolean $end
     * @param boolean $flag_select
     * @return Array
     */
    function get_spare_parts_booking_list($where, $start, $end,$flag_select){
        $limit = "";
        $select = " ";
        if($flag_select){
            $select = "SELECT spare_parts_details.*, users.name, booking_details.booking_primary_contact_no, "
                . " booking_details.booking_address,booking_details.initial_booking_date,"
                . " service_centres.name as vendor_name, service_centres.address, service_centres.state, "
                . " service_centres.pincode, service_centres.district,"
                . " DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(date_of_request, '%Y-%m-%d')) AS age_of_request ";
            $limit = "LIMIT $start, $end";
        } else {
            $select = "SELECT count(spare_parts_details.id) as total_rows ";
        }
        $sql =   $select
                ." FROM spare_parts_details,booking_details,users, "
                . " service_centres WHERE booking_details.booking_id = spare_parts_details.booking_id"
                . " AND users.user_id = booking_details.user_id AND service_centres.id = spare_parts_details.service_center_id "
                . " AND ".$where . "  ORDER BY status = '". DEFECTIVE_PARTS_REJECTED."', spare_parts_details.create_date ASC $limit";
        $query = $this->db->query($sql);
       
        return $query->result_array();
    }

    /**
     * @desc: This function is used to get partner username password of partiular partner
     * @params: Int 
     *          ID of partner
     * @return: Array
     */
    function get_partner_login_details($partner_id){
        $this->db->select('id,user_name,password,clear_text,email');
        $this->db->where('partner_id', $partner_id);
        $query = $this->db->get('partner_login');
        return $query->result_array();
    }
    
    /**
     * @Desc: This function is used to get booking sources details from price_mapping_id
     * @param int price_mapping_id
     * @return: Array
     * 
     */
    function get_booking_sources_by_price_mapping_id($price_mapping_id){
        $this->db->select('*');
        $this->db->where('price_mapping_id', $price_mapping_id);
        $query =  $this->db->get('bookings_sources');
        return $query->result_array();
    }
    
    /**
     * @Desc: This function is used to add partner operation region table values
     * @params: Array
     * @return: BOOLEAN
     */
    function insert_batch_partner_operation_region($data){
        return $this->db->insert_batch('partner_operation_region', $data);
    }
    
    /**
     * @Desc: This function is used to add partner operation region table values
     * @params: Array
     * @return: BOOLEAN
     */
    function insert_batch_partner_brand_relation($data){
        return $this->db->insert_batch('partner_appliance_details', $data);
    }
    
    /**
     * @Desc: This function is used to add Partner Login details in Partner Login Table
     * @params: Array
     * @return: Boolean
     * 
     */
    function add_partner_login($data){
        $this->db->insert("partner_login", $data);
        if($this->db->affected_rows() > 0){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    
    /**
     * @Desc: This function is used to get Partner Operation Region Details for particular Partner
     * @params: Array
     * @return: Array
     * 
     * 
     */
    function get_partner_operation_region($where){
        $this->db->select('*');
        $this->db->where($where);
        $query = $this->db->get('partner_operation_region');
        return $query->result_array();
    }
    
    /**
     * @Desc: This function is used to Update Partner Login Details
     * @params: Array
     * @return: Boolean
     * 
     */
    function update_partner_login_details($data,$where){
        $this->db->where($where);
        $this->db->update('partner_login',$data);
        if($this->db->affected_rows() > 0 ){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    
    /**
     * @Desc: This funtion is used to delete partner operation region
     * @params:Array
     * @return: Boolean
     * 
     */
    function delete_partner_operation_region($partner_id){
        $this->db->where('partner_id',$partner_id);
        $this->db->delete('partner_operation_region');
        if($this->db->affected_rows() > 0 ){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    
    /**
     * @Desc: This funtion is used to delete partner service brand relation
     * @params:Array
     * @return: Boolean
     * 
     */
    function delete_partner_brand_relation($partner_id){
        $this->db->where('partner_id',$partner_id);
        $this->db->delete('partner_appliance_details');
        if($this->db->affected_rows() > 0 ){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    
   
    /**
     * @Desc: This function is used to get Partner Services and Brands details
     * @params: Partner ID
     * @return: Array
     * 
     */
    function get_service_brands_for_partner($partner_id){
        $sql = "Select Distinct partner_appliance_details.brand, services.services  "
                . "From partner_appliance_details, services "
                . "where partner_appliance_details.service_id = services.id "
                . "AND partner_appliance_details.partner_id = '".$partner_id."'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    /**
     * @Desc: This funtion is used to get Partner by Brands and Service ID from partner_appliance_details
     *         We also get only those Partner whose relation is being Active
     * @params: String Brands, service id
     * @return : Array
     */
    function get_active_partner_id_by_service_id_brand($brands, $service_id){
        $this->db->select('partner_appliance_details.partner_id');
        $this->db->where('partner_appliance_details.brand',$brands);
        $this->db->where('partner_appliance_details.service_id',$service_id);
        $this->db->where('partner_appliance_details.active',1);
        $this->db->where('partners.is_active',1);
        $this->db->join('partners','partner_appliance_details.partner_id = partners.id');
        $query = $this->db->get('partner_appliance_details');
        
        return $query->result_array();
        
        
    }
    
    /**
     * @Desc: This function is used to check for partner for particular state and service in partner_operation_region
     *          This is for ACtivated Partner
     * @params: state, partner_id, service_id
     * @return: Boolean
     * 
     */
    function check_activated_partner_for_state_service($state, $partner_id, $service_id){
        $this->db->select('partner_id');
        $this->db->where('partner_id',$partner_id);
        $this->db->where('service_id',$service_id);
        $this->db->where('state',$state);
        $this->db->where('active',1);
        $query = $this->db->get('partner_operation_region');
        if($query->num_rows() > 0){
            return TRUE;
        }else{
            return FALSE;
        }
        
    }
    
    /**
     * @Desc: This function is used to get Distinct Appliances for Partner
     * @params: Partner ID
     * @return: Array
     */
    function get_appliances_for_partner($partner_id){
        $sql = "Select Distinct services.services, services.id  "
                . "From partner_appliance_details, services "
                . "where partner_appliance_details.service_id = services.id "
                . "AND partner_appliance_details.partner_id = '".$partner_id."'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }    
    
    /**
     * @Desc: This function is used to get Patner codes from bookings_sources
     * @params: void
     * @return: Array
     * 
     */
    function get_availiable_partner_code(){
        $this->db->distinct();
        $this->db->select('code');
        $query = $this->db->get('bookings_sources');
        return $query->result_array();
    }
    
    /**
     * @Desc: This function is used to add parter code in bookings_sources table
     * @params: Array
     * @return: Int
     * 
     */
    function add_partner_code($data) {
        $this->db->insert('bookings_sources', $data);
        return $this->db->insert_id();
    }
    
    /**
     * @Desc: This function is used to get code for particular partner
     * @params: partner id
     * @return: array
     * 
     */
    function get_partner_code($partner_id){
        $this->db->select('partner_type, code, price_mapping_id');
        $this->db->where('partner_id',$partner_id);
        $query = $this->db->get('bookings_sources');
        return $query->result_array();
    }
    
    /**
     * @Desc: This function is used to update partner details in bookings_sources table
     * @params: where array, data array
     * 
     */
    function update_partner_code($where, $data){
        $this->db->where($where);
        $this->db->update('bookings_sources',$data);
        if($this->db->affected_rows() > 0 ){
            return TRUE;
        }else{
            return FALSE;
        }
        
    }
    
    /**
     * @Desc: This function is used to get latest price_mapping_id from bookings_sources table
     * @params: void
     * @return: void
     * 
     */
    function get_latest_price_mapping_id(){
        $this->db->select('price_mapping_id');
        $this->db->order_by('create_date','desc');
        $query = $this->db->get('bookings_sources');
        return $query->first_row();
    }
    
    /*
     * @desc: This is used to get active partner details and also get partner details by partner id
     *          Without looking for Active or Disabled
     */
    function get_all_partner($partner_id = "") {
	    if ($partner_id != "") {
	        $this->db->where('id', $partner_id);
	    }
	    $this->db->select('*');
	    $query = $this->db->get('partners');

	    return $query->result_array();
    }
    
    /**
     * @Desc: This function is used to insert value in partner_missed_calls table
     * @params: array
     * @return: Boolean
     * 
     */
    function insert_partner_missed_calls_detail($data){
        $this->db->insert("partner_missed_calls", $data);
        $result = (bool) ($this->db->affected_rows() > 0);
        return $result;
    }
    
    /**
     * @Desc: This function is used to get missed calls details
     *        Only those rows will be taken whose status is FollowUp and action date in on or before current time
     * @params: void 
     * @return: Array
     */
    function get_missed_calls_details(){
        $sql = "Select * from partner_missed_calls where status = 'FollowUp' "
                . "AND action_date <= NOW() ORDER BY action_date Desc " ;
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    /**
     * @Desc: This function is used to get value for particular Missed calls Leads in partner_missed_calls table by ID
     * @params: id
     * @return Array
     * 
     */
    function get_missed_calls_leads_by_id($id){
        $this->db->select('*');
        $this->db->where('id',$id);
        $query = $this->db->get('partner_missed_calls');
        return $query->result_array();
    }
    
    /**
     * @Desc: This function is used to Updated partner_missed_calls lead table
     * @params: Where Array, Data Array
     * @return: Boolean
     * 
     */
    function update_partner_missed_calls($where, $data){
        $this->db->where($where);
        $this->db->update('partner_missed_calls',$data);
        $result = (bool) ($this->db->affected_rows() > 0);
        return $result;
    }
    
    /**
     * @Desc: This function is used to get Partner Missed calls cancellation reason
     * @params: void
     * @return: Array
     * 
     * 
     */
    function get_missed_calls_cancellation_reason(){
        $this->db->select('*');
        $this->db->where('reason_of','missed_cancellation');
        $query = $this->db->get('booking_cancellation_reasons');
        return $query->result_array();
        
    }
    
    /**
     * 
     * @Desc: This function is used get partner missed calls updation reason
     * @params: void
     * return: Array
     * 
     */
    function get_missed_calls_updation_reason(){
        $this->db->select('*');
        $this->db->where('reason_of','missed_updation');
        $query = $this->db->get('booking_cancellation_reasons');
        return $query->result_array();
    }
    
    /**
     * @Desc: This function is used to get details from partner_missed_calls table by Phone number
     * @params: Phone
     * @return: Array
     * 
     */
    function get_partner_leads_by_phone_status($num,$status){
        $this->db->select('*');
        $this->db->where('phone',$num);
        $this->db->where('status',$status);
        $query = $this->db->get('partner_missed_calls');
        return $query->result_array();
    }
   
    /**
     * @Desc: This function is used to add values in file uploads table
     * @params: Array
     * @return: Boolean
     * 
     */
    function add_file_upload_details($data) {
        $this->db->insert('file_uploads', $data);

        return $this->db->insert_id();
    }
    
    /**
     * @desc: This method is used to search booking by phone number or booking id
     * this is called by Partner panel
     * @param String $searched_text
     * @param String $partner_id
     * @return Array
     */
    function search_booking_history($searched_text,$partner_id) {
        //Sanitizing Searched text - Getting only Numbers, Alphabets and '-'
        $searched_text = preg_replace('/[^A-Za-z0-9-]/', '', $searched_text);
        
        $where_phone = "AND (`booking_primary_contact_no` = '$searched_text' OR `booking_alternate_contact_no` = '$searched_text')";
        $where_booking_id = "AND `booking_id` LIKE '%$searched_text%'";
       
        $sql = "SELECT `booking_id`,`booking_date`,`booking_timeslot` ,`order_id` , users.name as customername, services.services, current_status, assigned_engineer_id "
                . " FROM `booking_details`,users, services "
                . " WHERE users.user_id = booking_details.user_id "
                . " AND services.id = booking_details.service_id "
                . " AND `partner_id` = '$partner_id' ". $where_phone

                . " UNION "
                . "SELECT `booking_id`,`booking_date`,`booking_timeslot`,`order_id`, users.name as customername, services.services, current_status, assigned_engineer_id "
                . " FROM `booking_details`,users, services "
                . " WHERE users.user_id = booking_details.user_id "
                . " AND services.id = booking_details.service_id "
                . " AND `partner_id` = '$partner_id' ". $where_booking_id
                . " ";
        $query = $this->db->query($sql);
        
        //log_message('info', __FUNCTION__ . '=> Update Spare Parts: ' .$this->db->last_query());
        return $query->result_array();
    }
    
    /**
     * @Desc: This function is used to get username for particular partner
     * @params: Array
     * @return: Mix
     * 
     */
    function get_partner_username($data) {
        $this->db->select('user_name');
        $this->db->where('user_name', $data['user_name']);
        $this->db->where('partner_id', $data['partner_id']);
        $query = $this->db->get('partner_login');
        
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0];
        } else {

            return false;
        }
    }
    
    /**
     * @desc: This is used to return Partner Specific Brand details
     * @param Array $where
     * @return Array
     */
    function get_partner_specific_details($where, $select, $order_by){
        
        $this->db->distinct();
        $this->db->select($select);
        $this->db->where($where);
        $this->db->order_by($order_by, 'asc');
        $this->db->where('partner_appliance_details.active',1);
        $query = $this->db->get('partner_appliance_details');
 
        return $query->result_array();
         
    }
    /**
     * @desc: This is used to return partner sepcific  services
     * @param String $partner_id
     * @return Array
     */
    function get_partner_specific_services($partner_id){
        $this->db->distinct();
        $this->db->select("services.id, services");
        $this->db->where('partner_id', $partner_id);
        $this->db->from('partner_appliance_details');
        $this->db->join("services","services.id = partner_appliance_details.service_id");
        $this->db->where('partner_appliance_details.active',1);
        $this->db->order_by('services', 'asc');
        $query = $this->db->get();
        return $query->result();
    }
    
    function upload_partner_brand_logo($data){
        $this->db->insert('partner_brand_logo',$data);
        return $this->db->insert_id();
    }
}

