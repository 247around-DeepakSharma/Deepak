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
      $this->db->select('partners.id, public_name');
      $this->db->from("partners");
      $this->db->where(array("partners.auth_token" => $auth_token, "partners.is_active" => '1'));
      
      $query = $this->db->get();

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

    //Find order id for a partner
    function get_order_id_for_partner($partner_id, $order_id, $booking_id = "") {
      $this->db->where(array("partner_id" => $partner_id, "order_id" => $order_id));
      if($booking_id != ""){
           $this->db->not_like('booking_id', preg_replace("/[^0-9]/","",$booking_id));
      }
      $query = $this->db->get("booking_details");
      $results = $query->result_array();

      if (count($results) > 0) {
        return $results[0];
      } else {
        return NULL;
      }
    }

    function get_all_partner_source($flag="", $source= ""){
      $this->db->select("bookings_sources.partner_id,source,code, partner_type");
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
    function getpartner($partner_id = "", $is_active = true) {
        if ($partner_id != "") {
            $this->db->where('id', $partner_id);
        }
        $this->db->select('*');

        $this->db->order_by('public_name');

        if ($is_active) {
            $this->db->where('is_active', '1');
        }


        $query = $this->db->get('partners');
        //echo $this->db->last_query(); die();
        return $query->result_array();
    }

 /**
 * @desc: this method return partner data if need to call partner api other wise return false
 * @param: booking id
 */
function get_data_for_partner_callback($booking_id) {
        $this->db->select("*");
        $this->db->from("booking_details");
        $this->db->where("booking_id", $booking_id);
        $this->db->join("partner_callback", "partner_callback.partner_id = booking_details.partner_id AND callback_string = partner_source");
        $this->db->where("partner_callback.active", 1);
        $query = $this->db->get();
        if ($query->num_rows > 0) {

            $result = $query->result_array();
            return $result[0];
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
     function getPending_booking($partner_id ,$select,$booking_id = '',$state=0,$offset = NULL,$limit = NULL,$stateValue = NULL,$order = array()){
         $join = "";
         $where = "";
         if($state == 1){
             $join = "JOIN agent_filters ON agent_filters.state = booking_details.state";
             $where = "agent_filters.agent_id= ".$this->session->userdata('agent_id')." AND agent_filters.is_active =1 AND" ;
         }
         $limitSuubQuery = "";
         if($limit){
             $limitSuubQuery = "LIMIT $offset, $limit";
         }
        $where .= "  booking_details.partner_id = '" . $partner_id . "'";
        if(!empty($booking_id)){
            $where .= " AND `booking_details`.booking_id = '".$booking_id."' "
                    . " AND (booking_details.current_status IN ('Pending','Rescheduled','FollowUp')) ";
        } else {
            $where .= " AND (booking_details.current_status IN ('Pending', 'Rescheduled')) AND booking_details.service_center_closed_date IS NULL ";
        }
         if($stateValue){
              $where .= " AND (booking_details.state = '$stateValue') ";
         }
         $orderSubQuery = "";
         if(!empty($order)){
             $orderSubQuery = " ORDER BY " .$order['column']." ".$order['sorting'];
         }
        //do not show bookings for future as of now
        //$where .= " AND DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= 0";

          $query = $this->db->query("Select $select from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            ".$join."
            LEFT JOIN  `booking_unit_details` ON  `booking_unit_details`.`booking_id` =  `booking_details`.`booking_id`
            WHERE  $where AND booking_details.upcountry_partner_approved ='1' AND NOT EXISTS (SELECT 1 FROM service_center_booking_action sc JOIN booking_details bd ON "
                  . "bd.booking_id = sc.booking_id JOIN partners ON bd.partner_id = partners.id WHERE sc.booking_id = booking_details.booking_id AND sc.current_status = 'InProcess' "
                  . "AND (partners.booking_review_for IS NOT NULL "
                  . "AND bd.amount_due = 0) AND NOT EXISTS (SELECT 1 FROM service_center_booking_action sc_sub WHERE sc_sub.booking_id = sc.booking_id AND sc_sub.internal_status ='Completed' LIMIT 1) "
                  . "AND bd.partner_id IN ('".$partner_id."') AND sc.internal_status IN ('Cancelled','Completed')) "
                  . "$orderSubQuery $limitSuubQuery"
        );
          $temp = $query->result();
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
      function getclosed_booking($limit, $start, $partner_id, $status, $booking_id = "",$state=0){
        if($limit!="count"){
            $this->db->limit($limit, $start);
        }
        $this->db->_protect_identifiers = FALSE;
        $this->db->select('request_type,booking_details.booking_id, booking_details.closed_date, users.name as customername, '
                . ' booking_details.booking_primary_contact_no, services.services, '
                . ' booking_details.booking_date, booking_details.closing_remarks, '
                . ' booking_details.booking_timeslot, booking_details.city, booking_details.state,'
                . ' booking_details.cancellation_reason, booking_details.order_id,booking_details.is_upcountry,amount_due, upcountry_paid_by_customer'
                . ',(CASE WHEN DATEDIFF(date(booking_details.service_center_closed_date),STR_TO_DATE(booking_details.initial_booking_date,"%d-%m-%Y"))<0 THEN 0 ELSE '
                . 'DATEDIFF(date(booking_details.service_center_closed_date),STR_TO_DATE(booking_details.initial_booking_date,"%d-%m-%Y")) END )  as tat');
        $this->db->from('booking_details');
        $this->db->join('services','services.id = booking_details.service_id');
        $this->db->join('users','users.user_id = booking_details.user_id');
        if($state == 1){
            $this->db->join('agent_filters','agent_filters.state = booking_details.state');
            $this->db->where('agent_filters.agent_id', $this->session->userdata('agent_id'));
        }
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
    function getPrices($service_id, $category, $capacity, $partner_id, $service_category,$brand ="", $not_like = TRUE,$is_repeat = NULL) {
	$this->db->distinct();
	$this->db->select('id,service_category,customer_total, partner_net_payable, customer_net_payable, pod, is_upcountry, vendor_basic_percentage');
	$this->db->where('service_id', $service_id);
	$this->db->where('category', $category);
	$this->db->where('active', 1);
	$this->db->where('check_box', 1);
	$this->db->where('partner_id', $partner_id);
//        if(!$is_repeat){
//            $where['service_category != "'.REPEAT_BOOKING_TAG.'"'] = NULL;
//            $this->db->where($where);
//        }
        if($service_category !=""){
            if($not_like){
                $this->db->where('service_category', $service_category);
            } else {
                $this->db->like('service_category', $service_category);
            }
        }

	if (!empty($capacity)) {
	    $this->db->where('capacity', $capacity);
	}
        if(!empty($brand)){
            $this->db->where('brand', $brand);
        }
        
        $this->db->order_by('service_category', 'asc');

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
        
        $this->db->order_by('service_category', 'asc');
                        
	$query = $this->db->get('service_centre_charges');

	return $query->result_array();
    }
    function get_partner_report_overview_in_percentage_format($partner_id,$bookingDateColumn){
        $finalArray = array();
        $query = $this->db->query("SELECT COUNT(booking_id) as count,DATEDIFF(date(booking_details.service_center_closed_date),$bookingDateColumn) as TAT FROM booking_details "
                . "WHERE partner_id = '".$partner_id."' AND service_center_closed_date IS NOT NULL AND !(current_status = 'Cancelled' OR internal_status ='InProcess_Cancelled') AND "
                . "DATEDIFF(CURDATE(),date(booking_details.service_center_closed_date)) < 31 GROUP BY TAT");
        $overViewData = $query->result_array();
        foreach($overViewData as $overView){
            if($overView['TAT']<=0){
                if(!array_key_exists('day_0', $finalArray)){
                    $finalArray['day_0'] = 0;
                }
                $finalArray['day_0'] = $overView['count']+$finalArray["day_0"];
            }
            else{
                if($overView['TAT']>5){
                        if(!array_key_exists('day_5', $finalArray)){
                            $finalArray['day_5'] = 0;
                        }
                        $finalArray["day_5"] = $overView['count']+$finalArray["day_5"];
               }
               else{
                        $finalArray["day_".$overView['TAT']] = $overView['count'];
               }
            }
        }
        return $finalArray;
    }
    function get_partner_summary_report_fields($partner_id){
        $data = $this->reusable_model->get_search_result_data("partner_summary_report_mapping","*",array("is_default =1 OR partner_id LIKE '%".$partner_id."%'"=>NULL),NULL,NULL,
                array("index_in_report"=>"ASC"),NULL,NULL,array());
        return $data;
    }

    //Return all leads shared by Partner in the last 30 days in CSV
    function get_partner_leads_csv_for_summary_email($partner_id,$percentageLogic,$whereConditions=NULL){
        $mappingData = $this->get_partner_summary_report_fields($partner_id);
        foreach($mappingData as $values){
            $subQueryArray[$values['Title']] = $values['sub_query'];
        }
        if(!$whereConditions){
            $where = "((booking_details.create_date > (CURDATE() - INTERVAL 1 MONTH)) OR (booking_details.current_status NOT IN ('Cancelled','Completed')))";
        }
        else{
            $where = $whereConditions;
        }
        if($partner_id == AKAI_ID){
            $subQueryArray['Dependency'] = 'IF(dependency_on =1, "'.DEPENDENCY_ON_AROUND.'", "'.DEPENDENCY_ON_CUSTOMER.'") as Dependency ';
        } 
        if ($percentageLogic == 1){
            $subQueryArray['TAT']  = '(CASE WHEN service_center_closed_date IS NOT NULL AND !(booking_details.current_status = "Cancelled" OR booking_details.internal_status ="InProcess_Cancelled") '
                    . 'THEN (CASE WHEN DATEDIFF(date(booking_details.service_center_closed_date),STR_TO_DATE(booking_details.initial_booking_date,"%d-%m-%Y")) < 0 THEN 0 ELSE'
                . ' DATEDIFF(date(booking_details.service_center_closed_date),STR_TO_DATE(booking_details.initial_booking_date,"%d-%m-%Y")) END) ELSE "" END) as TAT';
             $subQueryArray['Ageing']  = '(CASE WHEN booking_details.service_center_closed_date IS NULL THEN DATEDIFF(CURDATE(),STR_TO_DATE(booking_details.initial_booking_date,"%d-%m-%Y")) ELSE "" END) as Ageing';
        }
        $subQueryString = implode(",", array_values($subQueryArray));
        return $query = $this->db->query("SELECT $subQueryString
            FROM booking_details JOIN booking_unit_details ud  ON booking_details.booking_id = ud.booking_id 
            JOIN services ON booking_details.service_id = services.id 
            JOIN users ON booking_details.user_id = users.user_id
            LEFT JOIN booking_comments on booking_comments.booking_id = booking_details.booking_id
            LEFT JOIN dealer_details on dealer_details.dealer_id = booking_details.dealer_id
            LEFT JOIN spare_parts_details ON spare_parts_details.booking_id = booking_details.booking_id
            LEFT JOIN service_center_booking_action ON service_center_booking_action.booking_id = booking_details.booking_id
            WHERE product_or_services != 'Product' AND booking_details.partner_id = $partner_id AND $where GROUP BY ud.booking_id");
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
    function bookings_by_status($bookingTempArray){
        foreach($bookingTempArray as $values){
            if($values->closed_date != "" && ($values->current_status == 'Pending' || $values->current_status == 'Rescheduled' )){
                if($values->internal_status == 'InProcess_Cancelled'){
                    $status = 'Cancelled';
                }
                else{
                    $status = 'Completed';
                }
            }
            else{
                $status = $values->current_status;
            }
           $values->current_status = $status;
        }
        return $bookingTempArray;
    }

    //Get partner summary parameters for daily report
    function get_partner_summary_params($partner_id) {
        
        $where1 = array('booking_details.partner_id' => $partner_id, 'MONTH(booking_details.create_date) = MONTH(CURDATE())' => NULL, 'YEAR(booking_details.create_date) = YEAR(CURDATE())' => NULL);
        $current_month_booking_temp = $this->booking_model->get_bookings_count_by_any( 'DISTINCT current_status,booking_details.initial_booking_date,booking_details.internal_status,booking_details.create_date,booking_details.service_center_closed_date as closed_date,booking_details.request_type,booking_details.booking_date',$where1, "", "", true);
        $current_month_booking = $this->bookings_by_status($current_month_booking_temp);
        
        $where2 = array('booking_details.partner_id' => $partner_id, 'DATE(booking_details.create_date) = CURDATE()' => NULL);
        $today_booking_temp = $this->booking_model->get_bookings_count_by_any('DISTINCT current_status,booking_details.initial_booking_date,booking_details.internal_status,booking_details.create_date,booking_details.service_center_closed_date as closed_date,booking_details.request_type,booking_details.booking_date', $where2, "", "", true );
        $today_booking = $this->bookings_by_status($today_booking_temp);
        
        $where3 = array('booking_details.partner_id' => $partner_id, 'DATE(booking_details.create_date) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY))' => NULL);
        $yesterday_booking_temp = $this->booking_model->get_bookings_count_by_any('DISTINCT current_status,booking_details.initial_booking_date,booking_details.internal_status,booking_details.create_date,booking_details.service_center_closed_date as closed_date,booking_details.request_type,booking_details.booking_date', $where3, "", "", true );
        $yesterday_booking = $this->bookings_by_status($yesterday_booking_temp);
        
        $where4 = array('booking_details.partner_id' => $partner_id, "booking_details.current_status IN ('"._247AROUND_PENDING."', '"._247AROUND_RESCHEDULED."')" => NULL,"booking_details.service_center_closed_date IS NULL"=>NULL);
        
        $totalPending = $this->booking_model->get_bookings_count_by_any('DISTINCT current_status,booking_details.initial_booking_date,booking_details.create_date,booking_details.service_center_closed_date as closed_date,booking_details.request_type,booking_details.booking_date', $where4, "", "", true);

        $current_month_status = array_count_values(array_column($current_month_booking, 'current_status'));

        if (count($today_booking) !== 0 || count($yesterday_booking) !== 0 || (isset($current_month_status['Pending']) && !empty($current_month_status['Pending'])) || (isset($current_month_status['Rescheduled']) && !empty($current_month_status['Rescheduled']))) {
            $result['current_month_installation_booking_requested'] = 0;
            $result['current_month_installation_booking_completed'] = 0;
            $result['current_month_installation_booking_cancelled'] = 0;
            $result['current_month_installation_booking_followup'] = 0;
            $result['zero_to_two_days_installation_booking_pending'] = 0;
            $result['three_to_five_days_installation_booking_pending'] = 0;
            $result['greater_than_5_days_installation_booking_pending'] = 0;
            $result['today_installation_booking_requested'] = 0;
            $result['today_installation_booking_completed'] = 0;
            $result['today_installation_booking_cancelled'] = 0;
            $result['today_installation_booking_pending'] = 0;
            $result['today_installation_booking_followup'] = 0;
            $result['yesterday_installation_booking_requested'] = 0;
            $result['yesterday_installation_booking_completed'] = 0;
            $result['yesterday_installation_booking_cancelled'] = 0;
            $result['yesterday_installation_booking_followup'] = 0;
            $result['yesterday_installation_booking_pending'] = 0;

            $result['current_month_repair_booking_requested'] = 0;
            $result['current_month_repair_booking_completed'] = 0;
            $result['current_month_repair_booking_cancelled'] = 0;
            $result['current_month_repair_booking_followup'] = 0;
            $result['zero_to_two_days_repair_booking_pending'] = 0;
            $result['three_to_five_days_repair_booking_pending'] = 0;
            $result['greater_than_5_days_repair_booking_pending'] = 0;
            $result['today_repair_booking_requested'] = 0;
            $result['today_repair_booking_completed'] = 0;
            $result['today_repair_booking_cancelled'] = 0;
            $result['today_repair_booking_pending'] = 0;
            $result['today_repair_booking_followup'] = 0;
            $result['yesterday_repair_booking_requested'] = 0;
            $result['yesterday_repair_booking_completed'] = 0;
            $result['yesterday_repair_booking_cancelled'] = 0;
            $result['yesterday_repair_booking_followup'] = 0;
            $result['yesterday_repair_booking_pending'] = 0;

            foreach ($current_month_booking as $value) {

                if (strpos($value->request_type, 'Repair') !== false || strpos($value->request_type, 'Repeat') !== false) {
                    $result['current_month_repair_booking_requested'] ++;
                    switch ($value->current_status) {
                        case _247AROUND_COMPLETED:
                            $result['current_month_repair_booking_completed'] ++;
                            break;
                        case _247AROUND_CANCELLED:
                            $result['current_month_repair_booking_cancelled'] ++;
                            break;
                        case _247AROUND_PENDING:
                        case _247AROUND_RESCHEDULED:
                            if (date('Y-m-d', strtotime($value->initial_booking_date)) <= date('Y-m-d') && (date('Y-m-d', strtotime($value->initial_booking_date)) >= date("Y-m-d", strtotime("-2 days"))) || date('Y-m-d', strtotime($value->initial_booking_date)) >= date('Y-m-d')) {
                                $result['zero_to_two_days_repair_booking_pending'] ++;
                            } else if ((date("Y-m-d", strtotime($value->initial_booking_date)) < date("Y-m-d", strtotime("-2 days"))) && (date("Y-m-d", strtotime($value->initial_booking_date)) >= date("Y-m-d", strtotime("-5 days")))) {
                                $result['three_to_five_days_repair_booking_pending'] ++;
                            } else if (date('Y-m-d', strtotime($value->initial_booking_date)) < date('Y-m-d', strtotime("-5 days"))) {
                                $result['greater_than_5_days_repair_booking_pending'] ++;
                            }
                            break;
                        case _247AROUND_FOLLOWUP:
                            $result['current_month_repair_booking_followup'] ++;
                            break;
                    }
                } else {
                    $result['current_month_installation_booking_requested'] ++;
                    switch ($value->current_status) {
                        case _247AROUND_COMPLETED:
                            $result['current_month_installation_booking_completed'] ++;
                            break;
                        case _247AROUND_CANCELLED:
                            $result['current_month_installation_booking_cancelled'] ++;
                            break;
                        case _247AROUND_PENDING:
                        case _247AROUND_RESCHEDULED:
                            if (date('Y-m-d', strtotime($value->initial_booking_date)) <= date('Y-m-d') && (date('Y-m-d', strtotime($value->initial_booking_date)) >= date("Y-m-d", strtotime("-2 days"))) || date('Y-m-d', strtotime($value->initial_booking_date)) >= date('Y-m-d')) {
                                $result['zero_to_two_days_installation_booking_pending'] ++;
                            } else if ((date("Y-m-d", strtotime($value->initial_booking_date)) < date("Y-m-d", strtotime("-2 days"))) && (date("Y-m-d", strtotime($value->initial_booking_date)) >= date("Y-m-d", strtotime("-5 days")))) {
                                $result['three_to_five_days_installation_booking_pending'] ++;
                            } else if (date('Y-m-d', strtotime($value->initial_booking_date)) < date('Y-m-d', strtotime("-5 days"))) {
                                $result['greater_than_5_days_installation_booking_pending'] ++;
                            }
                            break;
                        case _247AROUND_FOLLOWUP:
                            $result['current_month_installation_booking_followup'] ++;
                            break;
                    }
                }
            }

            foreach ($today_booking as $value) {
                if (strpos($value->request_type, 'Repair') !== false || strpos($value->request_type, 'Repeat') !== false) {
                    $result['today_repair_booking_requested'] ++;
                    switch ($value->current_status) {
                        case _247AROUND_COMPLETED:
                            $result['today_repair_booking_completed'] ++;
                            break;
                        case _247AROUND_CANCELLED:
                            $result['today_repair_booking_cancelled'] ++;
                            break;
                        case _247AROUND_PENDING:
                        case _247AROUND_RESCHEDULED:
                            if (date('Y-m-d', strtotime($value->initial_booking_date)) >= date('Y-m-d')){
                                $result['today_repair_booking_pending'] ++;
                            }
                            break;
                        case _247AROUND_FOLLOWUP:
                            $result['today_repair_booking_followup'] ++;
                            break;
                    }
                } else {
                    $result['today_installation_booking_requested'] ++;
                    switch ($value->current_status) {
                        case _247AROUND_COMPLETED:
                            $result['today_installation_booking_completed'] ++;
                            break;
                        case _247AROUND_CANCELLED:
                            $result['today_installation_booking_cancelled'] ++;
                            break;
                        case _247AROUND_PENDING:
                        case _247AROUND_RESCHEDULED:
                            if (date('Y-m-d', strtotime($value->initial_booking_date)) >= date('Y-m-d')){
                                $result['today_installation_booking_pending'] ++;
                            }
                            break;
                        case _247AROUND_FOLLOWUP:
                            $result['today_installation_booking_followup'] ++;
                            break;
                    }
                }
            }
            foreach ($yesterday_booking as $value) {
                if (strpos($value->request_type, 'Repair') !== false || strpos($value->request_type, 'Repeat') !== false) {
                    $result['yesterday_repair_booking_requested'] ++;
                    switch ($value->current_status) {
                        case _247AROUND_COMPLETED:
                            $result['yesterday_repair_booking_completed'] ++;
                            break;
                        case _247AROUND_CANCELLED:
                            $result['yesterday_repair_booking_cancelled'] ++;
                            break;
                        case _247AROUND_PENDING:
                        case _247AROUND_RESCHEDULED:
                            if (date('Y-m-d', strtotime($value->initial_booking_date)) == date("Y-m-d", strtotime("-1 days"))){
                                $result['yesterday_repair_booking_pending'] ++;
                            }
                            break;
                        case _247AROUND_FOLLOWUP:
                            $result['yesterday_repair_booking_followup'] ++;
                            break;
                    }
                } else {
                    $result['yesterday_installation_booking_requested'] ++;
                    switch ($value->current_status) {
                        case _247AROUND_COMPLETED:
                            $result['yesterday_installation_booking_completed'] ++;
                            break;
                        case _247AROUND_CANCELLED:
                            $result['yesterday_installation_booking_cancelled'] ++;
                            break;
                        case _247AROUND_PENDING:
                        case _247AROUND_RESCHEDULED:
                            if (date('Y-m-d', strtotime($value->initial_booking_date)) == date("Y-m-d", strtotime("-1 days"))){
                                $result['yesterday_installation_booking_pending'] ++;
                            }
                            break;
                        case _247AROUND_FOLLOWUP:
                            $result['yesterday_installation_booking_followup'] ++;
                            break;
                    }
                }
            }
            $result['total_zero_to_two_days_repair_booking_pending'] = 0;
            $result['total_three_to_five_days_repair_booking_pending'] = 0;
            $result['total_greater_than_5_days_repair_booking_pending'] =0;
            $result['total_zero_to_two_days_installation_booking_pending'] = 0;
            $result['total_three_to_five_days_installation_booking_pending'] = 0;
            $result['total_greater_than_5_days_installation_booking_pending'] = 0;
            foreach ($totalPending as $key => $value) {
                if (strpos($value->request_type, 'Repair') !== false || strpos($value->request_type, 'Repeat') !== false) {
                    if (date('Y-m-d', strtotime($value->initial_booking_date)) <= date('Y-m-d') && (date('Y-m-d', strtotime($value->initial_booking_date)) >= date("Y-m-d", strtotime("-2 days"))) || date('Y-m-d', strtotime($value->initial_booking_date)) >= date('Y-m-d')) {
                        $result['total_zero_to_two_days_repair_booking_pending'] ++;
                    } else if ((date("Y-m-d", strtotime($value->initial_booking_date)) < date("Y-m-d", strtotime("-2 days"))) && (date("Y-m-d", strtotime($value->initial_booking_date)) >= date("Y-m-d", strtotime("-5 days")))) {
                        $result['total_three_to_five_days_repair_booking_pending'] ++;
                    } else if (date('Y-m-d', strtotime($value->initial_booking_date)) < date('Y-m-d', strtotime("-5 days"))) {
                        $result['total_greater_than_5_days_repair_booking_pending'] ++;
                    }
                } else {
                    if (date('Y-m-d', strtotime($value->initial_booking_date)) <= date('Y-m-d') && (date('Y-m-d', strtotime($value->initial_booking_date)) >= date("Y-m-d", strtotime("-2 days"))) || date('Y-m-d', strtotime($value->initial_booking_date)) >= date('Y-m-d')) {
                        $result['total_zero_to_two_days_installation_booking_pending'] ++;
                    } else if ((date("Y-m-d", strtotime($value->initial_booking_date)) < date("Y-m-d", strtotime("-2 days"))) && (date("Y-m-d", strtotime($value->initial_booking_date)) >= date("Y-m-d", strtotime("-5 days")))) {
                        $result['total_three_to_five_days_installation_booking_pending'] ++;
                    } else if (date('Y-m-d', strtotime($value->initial_booking_date)) < date('Y-m-d', strtotime("-5 days"))) {
                        $result['total_greater_than_5_days_installation_booking_pending'] ++;
                    }
                }
            }

            //convert int to string
            $data = array();
            foreach ($result as $key => $value) {
                $data[$key] = (string) $value;
            }
        } else {
            $data = array();
        }

        return $data;
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
           
        } 
        $sql = "Select partners.*, '' as user_name from partners $where";
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
    function activate($id,$data) {
        $this->db->where(array("id" => $id));
        $this->db->update("partners", $data);
        if ($this->db->affected_rows() > 0) {
            $res = TRUE;
        } else {
            $res = False;
        }
        
        return $res;
    }

    /**
     * @desc: This function is to deactivate partner who is already registered with us and are active.
     *
     * @param: $id
     *         - Id of partner to whom we would like to deactivate
     * @return: void
     */
    function deactivate($id,$data) {
        $this->db->where(array("id" => $id));
        $this->db->update("partners", $data);
        if ($this->db->affected_rows() > 0) {
            $res = TRUE;
        } else {
            $res = False;
        }
        
        return $res;
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
        $result = $this->db->update('partners', $partner);
        return $result;
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
    function getpartner_details($select, $where = "", $is_reporting_mail="",$is_am_details = null) {

	$this->db->select($select);
        if(!empty($where)){
            $this->db->where($where);
        }
        $this->db->from('partners');
	if ($is_reporting_mail != "") {
	    $this->db->where_in('is_reporting_mail', $is_reporting_mail);
	}
        $this->db->join('bookings_sources','bookings_sources.partner_id = partners.id','right');
        if(!empty($is_am_details)){
            $this->db->join('employee','partners.account_manager_id = employee.id','left');
        }
        $this->db->order_by('partners.public_name', "ASC");
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
                . " booking_details.booking_address,booking_details.initial_booking_date,booking_details.request_type, "
                . " service_centres.name as vendor_name, service_centres.address, service_centres.state, "
                . " service_centres.pincode, service_centres.district,booking_details.partner_id as booking_partner_id"
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
    function get_spare_parts_booking_list($where, $start, $end,$flag_select,$state=0,$is_stock_needed = null,$is_unit_details = false,$orderBy = false){
        if($state ==1){
            $where = $where." AND booking_details.state IN (SELECT state FROM agent_filters WHERE agent_id = ".$this->session->userdata('agent_id')." AND agent_filters.is_active=1)";
        }
        $limit = "";
        $select = " ";
        $join = "";
        $group_by = "";
        if($flag_select){
            $select = "SELECT spare_parts_details.*, users.name, users.phone_number as customer_mobile, booking_details.booking_primary_contact_no, booking_details.partner_id as booking_partner_id,"
                . " booking_details.booking_address,booking_details.initial_booking_date, booking_details.is_upcountry, booking_details.upcountry_paid_by_customer,"
                    . "booking_details.amount_due,booking_details.state, booking_details.current_status,"
                . " service_centres.name as vendor_name, service_centres.address, service_centres.district as sf_city,service_centres.state, service_centres.gst_no, "
                . " service_centres.pincode, service_centres.district,service_centres.id as sf_id,service_centres.is_gst_doc,service_centres.signature_file, service_centres.primary_contact_phone_1,"
                . " DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(date_of_request, '%Y-%m-%d')) AS age_of_request ";
            if($end){
                $limit = "LIMIT $start, $end";
            }
            if($is_unit_details){
                $select = $select.", GROUP_CONCAT(DISTINCT booking_unit_details.appliance_brand) as brands";
                $join = "JOIN booking_unit_details ON booking_unit_details.booking_id =  spare_parts_details.booking_id";
                $group_by = " GROUP BY spare_parts_details.id";
            }
        } else {
            $select = "SELECT count(spare_parts_details.id) as total_rows ";
        }
        if(!$orderBy){
            $orderBy = " ORDER BY status = '". DEFECTIVE_PARTS_REJECTED."'";
        }
        
        if (!empty($is_stock_needed)) {
            $sql = $select.' , inventory_stocks.stock'
                    . ' FROM spare_parts_details'
                    . ' JOIN booking_details ON spare_parts_details.booking_id = booking_details.booking_id'
                    . ' JOIN service_centres ON spare_parts_details.service_center_id = service_centres.id'
                    . ' JOIN users ON users.user_id = booking_details.user_id '.$join
                    . ' LEFT JOIN inventory_stocks ON spare_parts_details.requested_inventory_id = inventory_stocks.inventory_id'
                    . " WHERE $where $group_by ORDER BY spare_parts_details.purchase_invoice_id DESC,spare_parts_details.create_date $limit";
        }else{
                        if($is_unit_details){
                $sql =   $select
                ." FROM spare_parts_details,booking_details,users,booking_unit_details, "
                . " service_centres WHERE booking_details.booking_id = spare_parts_details.booking_id AND booking_unit_details.booking_id = spare_parts_details.booking_id"
                . " AND users.user_id = booking_details.user_id AND service_centres.id = spare_parts_details.service_center_id "
                . " AND ".$where . $group_by."  ORDER BY status = '". DEFECTIVE_PARTS_REJECTED."', spare_parts_details.create_date ASC $limit";
            }
            else{
                
                $sql =   $select
                    ." FROM spare_parts_details,booking_details,users, "
                    . " service_centres WHERE booking_details.booking_id = spare_parts_details.booking_id"
                    . " AND users.user_id = booking_details.user_id AND service_centres.id = spare_parts_details.service_center_id "
                    . " AND ".$where . $orderBy.", spare_parts_details.create_date ASC $limit";
            }
            }
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
    function get_booking_sources_by_price_mapping_id($partner_id){
        $this->db->select('*');
        $this->db->where('partner_id', $partner_id);
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
    function update_login_details($data,$where){
        $this->db->where($where);
        $this->db->update('entity_login_table',$data);
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
    function delete_partner_brand_relation($partner_id,$service_id = ''){
        $this->db->where('partner_id',$partner_id);
        if(!empty($service_id)){
            $this->db->where('service_id',$service_id);
        }
        $this->db->delete('partner_appliance_details');
        if($this->db->affected_rows() > 0 ){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    
    function get_tollfree_and_contact_persons(){
        $sql = "SELECT official_contact_number as contact, name,partners.public_name as partner  FROM contact_person JOIN partners ON partners.id =  contact_person.entity_id UNION SELECT customer_care_contact as contact, 'Toll Free Number' as name , partners.public_name as partner FROM partners ";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
   
    /**
     * @Desc: This function is used to get Partner Services and Brands details
     * @params: Partner ID
     * @return: Array
     * 
     */
    function get_service_brands_for_partner($partner_id){
        $sql = "Select Distinct partner_appliance_details.brand, services.services,services.id  "
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
        $this->db->distinct();
        $this->db->select('partner_appliance_details.partner_id, bookings_sources.code');
        $this->db->where('partner_appliance_details.brand',$brands);
        $this->db->where('partner_appliance_details.service_id',$service_id);
        $this->db->where('partner_appliance_details.active',1);
        $this->db->where('partners.is_active',1);
        $this->db->join('partners','partner_appliance_details.partner_id = partners.id');
        $this->db->join('bookings_sources','bookings_sources.partner_id = partners.id');
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
        $this->db->select('partner_id');
        $this->db->order_by('create_date','desc');
        $query = $this->db->get('bookings_sources');
        return $query->first_row();
    }
    
    /*
     * @desc: This is used to get active partner details and also get partner details by partner id
     *          Without looking for Active or Disabled
     */
    function get_all_partner($where = "") {

        $this->db->select('*');
        if(!empty($where)){
           $this->db->where($where);
        }
        $this->db->order_by("public_name", "asc");         
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
     * @Desc: This function is used to update values in file uploads table
     * @params: Array
     * @return: Boolean
     * 
     */
    function update_file_upload_details($where, $data){
        if(!empty($where)){
            $this->db->where($where);
            return $this->db->update("file_uploads",$data);
        }
        return TRUE;
    }
    
    /**
     * @desc: This method is used to search booking by phone number or booking id
     * this is called by Partner panel
     * @param String $searched_text_tmp
     * @param String $partner_id
     * @return Array
     */
    function search_booking_history($searched_text_tmp,$partner_id) {
        //Sanitizing Searched text - Getting only Numbers, Alphabets and '-'
        $searched_text = preg_replace('/[^A-Za-z0-9-]/', '', $searched_text_tmp);
        log_message("info", $searched_text);
        if(!empty($searched_text)){
            $where_phone = "AND (`booking_primary_contact_no` = '$searched_text' OR `booking_alternate_contact_no` = '$searched_text' OR `booking_id` LIKE '%$searched_text%')";
      
       
            $sql = "SELECT `booking_id`,`booking_date`,`booking_timeslot` ,`order_id` , users.name as customername, users.phone_number, services.services, current_status, assigned_engineer_id,date(closed_date) as closed_date "
                    . " FROM `booking_details`,users, services "
                    . " WHERE users.user_id = booking_details.user_id "
                    . " AND services.id = booking_details.service_id "
                    . " AND `partner_id` = '$partner_id' ". $where_phone
                    . " ";
            $query = $this->db->query($sql);
            $response = $query->result_array();
        }else{
            $response = FALSE;
        }
        
        return $response;
    }
    
    
    /**
     * @desc: This is used to return Partner Specific Brand details
     * @param Array $where
     * @return Array
     */
    function get_partner_specific_details($where, $select, $order_by ="", $where_in = ""){
        
        $this->db->distinct();
        $this->db->select($select);
        $this->db->where($where);
       
        if(!empty($where_in)){
            foreach($where_in as $index => $value){
                $this->db->where_in($index, $value);
            } 
        }
        if(!empty($order_by)){
             $this->db->order_by($order_by, 'asc');
        }
       
        $query = $this->db->get('partner_appliance_details');
       
        log_message("info", $this->db->last_query());
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
    
    /**
     * @desc: This is used to upload the partner brand logo path in datanase
     * @param array
     * @return string
     */
    function upload_partner_brand_logo($data){
        $this->db->insert('partner_brand_logo',$data);
        return $this->db->insert_id();
    }
    
    /**
     * @desc: This is used to get the partners details with source code
     * @param String $partner_id
     * @return Array
     */
    function get_partner_details_with_soucre_code($active,$partnerType,$ac,$partner_not_like=NULL,$partner_id="", $is_prepaid=null){
        $where = array();
        $where_in = array();
        $this->db->select('partners.*,bookings_sources.code,bookings_sources.partner_type');
        if ($partner_id != "") {
            $where['partners.id']  = $partner_id;
        }
        else{
            if($active !='All'){
                $where['partners.is_active'] = $active;
            }
            if($is_prepaid){
               $where['partners.is_prepaid'] = $is_prepaid; 
            }
            if($partnerType){
                //$where['bookings_sources.partner_type']  = $partnerType;
                $where_in = $partnerType;
            }
            if($ac != 'All'){
                $where['partners.account_manager_id']  = $ac;
            }
        }
        if($partner_not_like){
               $where['bookings_sources.partner_type != "'.$partner_not_like.'"']  = NULL;
        }
        
        $this->db->join('bookings_sources','partners.id=bookings_sources.partner_id');
        $this->db->where($where);   
        $this->db->where_in('bookings_sources.partner_type', $where_in);
        $query = $this->db->get('partners');
        return $query->result_array();
    }
    
    function partner_login_details($where){
        $this->db->select('*');
        $this->db->where($where);
        $query = $this->db->get('partner_login');
        return $query->result_array();
    }
    
    /**
     * @Desc: This function is used to get the spare part details by any option
     * @params: $select string
     * @params: $where array
     * @return: array()
     * 
     */
    function get_spare_parts_by_any($select,$where,$is_join=false,$sf_details = FALSE, $group_by = false){
        $this->db->select($select,FALSE);
        $this->db->where($where,false);
        $this->db->from('spare_parts_details');
        if($is_join){
            $this->db->join('booking_details','spare_parts_details.booking_id = booking_details.booking_id');
        }
        if($sf_details){
            $this->db->join('service_centres','spare_parts_details.service_center_id = service_centres.id');
        }
        if($group_by){
            
            $this->db->group_by($group_by);
        }
        $query = $this->db->get();
        return $query->result_array();
        
    }
    
    
    /**
     * @Desc: This function is used to get the escalation percentage of bookings by request type
     * @params: $partner_id string
     * @return: $query array();
     * 
     */
    function get_booking_escalation_percantage($partner_id){
        $sql = "SELECT 
                ((new_table.total_installation_escalate_booking*100)/installation_booking) as total_installation_escalate_percentage,
                ((new_table.unique_installation_escalate_booking*100)/installation_booking) as unique_installation_escalate_percentage,
                ((new_table.total_repair_escalate_booking*100)/repair_booking) as total_repair_escalate_percentage,
                ((new_table.unique_repair_escalate_booking*100)/repair_booking) as unique_repair_escalate_percentage,
                ((new_table.total_upcountry_escalate_booking*100)/upcountry_booking) as total_upcountry_escalate_percentage,
                ((new_table.unique_upcountry_escalate_booking*100)/upcountry_booking) as unique_upcountry_escalate_percentage
                 FROM (SELECT
                    SUM(IF(request_type LIKE '%Installation%' ,1,0)) as installation_booking,
                    SUM(IF(request_type LIKE '%Installation%' ,count_escalation,0)) as total_installation_escalate_booking,
                    SUM(IF(request_type LIKE '%Installation%' AND count_escalation > 0 ,1,0)) as unique_installation_escalate_booking,
                    SUM(IF(request_type LIKE '%Repair%' ,1,0)) as repair_booking,
                    SUM(IF(request_type LIKE '%Repair%' ,count_escalation,0)) as total_repair_escalate_booking,
                    SUM(IF(request_type LIKE '%Repair%' AND count_escalation > 0 ,1,0)) as unique_repair_escalate_booking,
                    SUM(IF(is_upcountry = '1' AND upcountry_partner_approved = '1' AND upcountry_paid_by_customer = '0',1,0)) as upcountry_booking,
                    SUM(IF(is_upcountry = '1' AND upcountry_partner_approved = '1' AND upcountry_paid_by_customer = '0' AND count_escalation > 0,count_escalation,0)) as total_upcountry_escalate_booking,
                    SUM(IF(is_upcountry = '1' AND upcountry_partner_approved = '1' AND upcountry_paid_by_customer = '0' AND count_escalation > 0,1,0)) as unique_upcountry_escalate_booking
                    FROM booking_details
                    WHERE partner_id = '".$partner_id."' AND type = 'Booking' ) as new_table";
        
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    function get_serviceability_by_pincode(){
        $sql = "SELECT vendor_pincode_mapping.City, vendor_pincode_mapping.State, vendor_pincode_mapping.Pincode,GROUP_CONCAT( DISTINCT services.services SEPARATOR ',') as appliance
                FROM vendor_pincode_mapping JOIN services ON services.id = vendor_pincode_mapping.Appliance_ID 
                JOIN service_centres on service_centres.id = vendor_pincode_mapping.Vendor_ID AND service_centres.active = 1
                WHERE service_centres.active = 1
                GROUP BY vendor_pincode_mapping.Pincode
                ORDER BY vendor_pincode_mapping.City";
        return $this->db->query($sql);
    }
    /**
     * @desc Update partner appliance_details table
     * @param Array $where
     * @param Array $data
     * @return boolean
     */
    function update_partner_appliance_details($where, $data){
        if(!empty($where)){
            $this->db->where($where);
            return $this->db->update("partner_appliance_details",$data);
        }
        return FALSE;
    }
    
    /**
     * @desc This is used to get partner blocked brand 
     * @param Array $where
     * @param String $select
     * @return Array
     */
    function get_partner_blocklist_brand($where, $select){
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get("blacklist_brand");
        return $query->result_array();
        
    }
    
    /**
     * @desc This is used to get details from partner_file_upload_header_mapping
     * @param Array $post
     * @param String $select
     * @return Array
     */
    function get_file_upload_header_mapping_data($post,$select){
        
        $this->db->distinct();
        $this->db->select($select);
        $this->db->from('partner_file_upload_header_mapping');
        $this->db->join('partners', 'partner_file_upload_header_mapping.partner_id  = partners.id');
        $this->db->join('employee', 'partner_file_upload_header_mapping.agent_id  = employee.id');
        if (!empty($post['where'])) {
            $this->db->where($post['where']);
        }
        
        if (!empty($post['search_value'])) {
            $like = "";
            foreach ($post['column_search'] as $key => $item) { // loop column 
                // if datatable send POST for search
                if ($key === 0) { // first loop
                    $like .= "( " . $item . " LIKE '%" . $post['search_value'] . "%' ";
                } else {
                    $like .= " OR " . $item . " LIKE '%" . $post['search_value'] . "%' ";
                }
            }
            $like .= ") ";

            $this->db->where($like, null, false);
        }

        if (!empty($post['order'])) {
            $this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
        } else {
            $this->db->order_by('partner_id','DESC');
        }
        
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * @desc This is used to insert details into partner_file_upload_header_mapping
     * @param Array $details
     * @return string
     */
    function insert_partner_file_upload_header_mapping($details) {
      $this->db->insert('partner_file_upload_header_mapping', $details);
      return $this->db->insert_id();
    }
    
    /**
     * @desc This is used to update details of partner_file_upload_header_mapping table
     * @param Array $where
     * @param Array $data
     * @return boolean
     */
    function update_partner_file_upload_header_mapping($where, $data){
        $this->db->where($where);
        $this->db->update('partner_file_upload_header_mapping',$data);
        if($this->db->affected_rows() > 0 ){
            return TRUE;
        }else{
            return FALSE;
        }
        
    }
    function insert_paytm_payment_details($data){
        $this->db->insert('payment_transaction', $data);
        return $this->db->insert_id();
    }
    function get_partners_pending_bookings($partner_id,$percentageLogic=0,$allPending=0,$status){
        $agingSubQuery = "";
        if($status == 'Pending'){
            $where = "booking_details.current_status IN ('Pending','Rescheduled')";
            $agingSubQuery = ', DATEDIFF(CURDATE(),STR_TO_DATE(booking_details.initial_booking_date,"%d-%m-%Y")) as Aging';
        }
        else if($status == 'Completed'){
            $where = "booking_details.current_status IN ('Completed')";
        }
        else if($status == 'Cancelled'){
            $where = "booking_details.current_status IN ('Cancelled')";
        }
        
        return $query = $this->db->query("SELECT 
            order_id AS 'Sub Order ID',
            booking_details.booking_id AS '247BookingID',
            date(booking_details.create_date) AS 'Referred Date',
            ud.appliance_brand AS 'Brand', 
            IFNULL(ud.model_number,'') AS 'Model',
            CASE WHEN(ud.serial_number IS NULL OR ud.serial_number = '') THEN '' ELSE (CONCAT('''', ud.serial_number))  END AS 'Serial Number',
            services AS 'Product', 
            ud.appliance_description As 'Description',
            name As 'Customer', users.phone_number as 'Phone Number',
            booking_pincode AS 'Pincode', 
            booking_details.city As 'City', 
            booking_details.state As 'State', 
            booking_details.booking_address As 'Booking Address',
            user_email As 'Email ID', 
            ud.price_tags AS 'Call Type (Installation /Table Top Installation/Demo/ Service)',
            CASE WHEN(current_status = 'Completed' || current_status = 'Cancelled') THEN (closing_remarks) ELSE (reschedule_reason) END AS 'Remarks',
            booking_date As 'Scheduled Appointment Date(DD/MM/YYYY)', 
            booking_timeslot AS 'Scheduled Appointment Time(HH:MM:SS)', 
            initial_booking_date As 'First Booking Date', 
            partner_internal_status AS 'Final Status',
            GROUP_CONCAT(spare_parts_details.parts_requested) As 'Requested Part', 
            GROUP_CONCAT(spare_parts_details.date_of_request) As 'Part Request Date', 
            GROUP_CONCAT(spare_parts_details.parts_shipped) As 'Shipped Part', 
            GROUP_CONCAT(spare_parts_details.shipped_date) As 'Part Shipped Date', 
            GROUP_CONCAT(spare_parts_details.defective_part_shipped) As 'Shipped Defective Part', 
            GROUP_CONCAT(spare_parts_details.defective_part_shipped_date) As 'Defactive Part Shipped Date'
            ".$agingSubQuery.",
            IFNULL(dealer_details.dealer_name,'') AS 'Dealer Name',
            IFNULL(dealer_details.dealer_phone_number_1,'') AS 'Dealer Phone Number'
            FROM booking_details JOIN booking_unit_details ud  ON booking_details.booking_id = ud.booking_id 
            JOIN services ON booking_details.service_id = services.id 
            JOIN users ON booking_details.user_id = users.user_id
            LEFT JOIN spare_parts_details ON spare_parts_details.booking_id = booking_details.booking_id
            LEFT JOIN dealer_details on dealer_details.dealer_id = booking_details.dealer_id
            WHERE product_or_services != 'Product' AND booking_details.partner_id = $partner_id AND $where GROUP BY ud.booking_id");
    }
    
    function getpartner_serialno($where){
        $this->db->select('*');
        $this->db->where($where);
        $query = $this->db->get('partner_serial_no');
        return $query->result_array();
    }
    /**
     * @desc This is used to insert new serial number 
     * @param Array $data
     * @return int
     */
    function insert_partner_serial_number($data){
        $this->db->insert_ignore('partner_serial_no', $data);
        return $this->db->insert_id();
    }
    /**
     * @desc This is used to insert serial no data in a batch
     * @param Array $data
     * @return boolean
     */
    function insert_partner_serial_number_in_batch($data){
        $this->db->insert_ignore_duplicate_batch('partner_serial_no', $data);
        return $this->db->insert_id();
    }
        
      /**
     * @desc: This function is used to get the contact persons of warehouse from contact_person table
     * @params: $id
     * @return: string
     * 
     */
    function select_contact_person($id) {
        $query = $this->db->query("Select id, name from contact_person where entity_id= '".$id."' AND is_active='1' AND entity_type = 'partner' AND name IS NOT NULL order by name");
        return $query->result();
    }

      /**
     * @desc: This function is used to get the email of POC and AM from partner table
     * @params: $id
     * @return: string
     * 
     */
    function select_POC_and_AM_email($id) {
        $this->db->select('p.primary_contact_email, e.official_email');
        $this->db->from('partners p');
        $this->db->join('employee e', 'e.id = p.account_manager_id'); 
        $this->db->where('p.id', $id);  
        $query = $this->db->get();
        return $query->result();
    }
    function get_booking_review_data($where = array(), $whereIN = array(),$booking_id = NULL){
        $this->db->select("service_center_booking_action.booking_id,services.services,booking_details.request_type,booking_details.partner_id,booking_details.is_upcountry,"
                . "booking_details.amount_due, GROUP_CONCAT(service_center_booking_action.internal_status) as combined_status,"
                . "GROUP_CONCAT(booking_unit_details.appliance_brand) as appliance_brand,booking_details.booking_jobcard_filename,"
                . "service_center_booking_action.internal_status,users.name,booking_details.booking_primary_contact_no,booking_details.city,booking_details.state,"
                . "STR_TO_DATE(booking_details.initial_booking_date,'%d-%m-%Y') as initial_booking_date,"
                . "DATEDIFF(CURRENT_TIMESTAMP,  service_center_booking_action.closed_date) as age,service_center_booking_action.cancellation_reason",FALSE);
        $this->db->join("booking_details","booking_details.booking_id = service_center_booking_action.booking_id");
        $this->db->join("services","booking_details.service_id = services.id");
        $this->db->join("booking_unit_details","booking_unit_details.booking_id = service_center_booking_action.booking_id");
        $this->db->join("users","users.user_id = booking_details.user_id");
        $this->db->group_by("service_center_booking_action.booking_id");
        if(!empty($whereIN)){
            foreach ($whereIN as $fieldName=>$conditionArray){
                    $this->db->where_in($fieldName, $conditionArray);
            }
        }
         if(!empty($where)){
            $this->db->where($where,FALSE);
        }
        $query = $this->db->get("service_center_booking_action");
        return $query->result_array();
    }
    
    /*
     * @desc: This function is used to get partner contract detail
     * @param: $select
     * @param: $post
     */
    
    function get_partner_contract_detail($select, $where=NULL, $join=NULL, $joinType=NULL){
        $this->db->from('partners');
        $this->db->select($select, FALSE);
       
        if (!empty($join)) {
            foreach ($join as $kay=>$value){
               $this->db->join($kay, $value, $joinType); 
            }
            
        }
        
        if (!empty($where)) {
            $this->db->where($where, FALSE);
        }
        
        $query = $this->db->get();
        return $query->result();
    }
    function insert_new_channels($details) {
        $this->db->insert('partner_channel', $details);
        return $this->db->insert_id();
    }
    public function get_channels($select = '*', $data = array()) {

        $this->db->select($select);
        if(!empty($data)){
            $this->db->where($data);
        }
        $this->db->from('partner_channel');
         $this->db->join('partners', 'partner_channel.partner_id = partners.id', 'left');
        $query = $this->db->get();
        return $query->result_array();
    }
    function update_channel($id, $data) {
        $this->db->where("id", $id);
        $this->db->update("partner_channel", $data);
        return true;
    
    }
    
    /*
     * @desc: This is used to get partner code from partner code table
     */
    function get_all_partner_code($select='*', $whereIn=array()) {
        $this->db->select($select);
        if(!empty($whereIn)){
            $this->db->where_in('series', $whereIn);
        }
        $this->db->order_by('code', 'ASC');
        $query = $this->db->get('partner_code');
        return $query->result_array();
    }
}

