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
      $this->db->select("partner_id,source,code");
        $this->db->order_by('source','ASC');
        if($flag =="")
        $this->db->where('partner_id !=', 'NULL');

        if($source !="")
            $this->db->where('code', $source);

      $query = $this->db->get("bookings_sources");
      return $query->result_array();
    }

    function insert_data_in_batch($table_name, $rows){
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
     function getPending_booking($partner_id ){
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
      function getclosed_booking($limit, $start, $partner_id, $status){
        if($limit!="count"){
            $this->db->limit($limit, $start);
        }

        $this->db->select('booking_details.booking_id, users.name as customername, booking_details.booking_primary_contact_no, services.services, booking_details.booking_date, booking_details.closing_remarks, booking_details.booking_timeslot, booking_details.city, booking_details.cancellation_reason, booking_details.order_id');
        $this->db->from('booking_details');
        $this->db->join('services','services.id = booking_details.service_id');
        $this->db->join('users','users.user_id = booking_details.user_id');
        $this->db->where('booking_details.current_status', $status);
        $this->db->where('partner_id',$partner_id);
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
    function getPrices($service_id, $category, $capacity, $partner_id, $service_category) {
	$this->db->distinct();
	$this->db->select('id,service_category,customer_total, partner_net_payable, customer_net_payable, pod');
	$this->db->where('service_id', $service_id);
	$this->db->where('category', $category);
	$this->db->where('active', 1);
	$this->db->where('check_box', 1);
	$this->db->where('partner_id', $partner_id);
	$this->db->where('service_category', $service_category);

	if (!empty($capacity)) {
	    $this->db->where('capacity', $capacity);
	}

	$query = $this->db->get('service_centre_charges');

	return $query->result_array();
    }

    //Return all leads shared by Partner in the last 30 days
    function get_partner_leads_for_summary_email($partner_id) {
	$query = $this->db->query("SELECT BD.booking_id, order_id, booking_date, booking_timeslot,
			BD.current_status, BD.internal_status, rating_stars,
			DATE_FORMAT(BD.create_date, '%d/%M') as create_date,
			services,
			UD.appliance_brand as brand, UD.model_number, UD.appliance_description as description,
			name, phone_number, home_address, pincode, users.city
			FROM booking_details as BD, users, services, booking_unit_details as UD
			WHERE BD.booking_id = UD.booking_id AND
			BD.service_id = services.id AND
			BD.user_id = users.user_id AND
			BD.partner_id = $partner_id AND
			BD.create_date > (CURDATE() - INTERVAL 1 MONTH)");

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
    function getpartner_details($where, $is_reporting_mail) {

	$this->db->select('*');
	$this->db->where($where);
	if ($is_reporting_mail != "") {
	    $this->db->where_in('is_reporting_mail', $is_reporting_mail);
	}
	$query = $this->db->get('partners');

	return $query->result_array();
    }

}

