<?php

class Around_scheduler_model extends CI_Model {
    Private $BIG_MAINDATA = array(); 
    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }

    /*
     * @desc: This method is used to send reminder SMS to users for whom:
     * Partner source => Snapdeal-shipped-excel, Snapdeal-delivered-excel
     * Booking Date => Today / ''
     * Current status => FollowUp
     * Vendor => Available
     */
    function get_reminder_installation_sms_data_today() {
        //Filter using booking_date instead of EDD
        $sql = "SELECT DISTINCT ss1.type_id,ss1.type,bd.booking_primary_contact_no,
                ss1.booking_id, ss1.content FROM booking_details AS bd 
                JOIN sms_sent_details AS ss1 ON (ss1.booking_id = bd.booking_id ) 
                
                WHERE partner_source IN (
                    'Snapdeal-shipped-excel', 
                    'Snapdeal-delivered-excel',
                    'STS', 
                    'Paytm-delivered-excel',
                    'Jeeves-delivered-excel'
                ) AND booking_date IN (
                DATE_FORMAT( CURDATE(),  '%d-%m-%Y' ),
                ''
                )
                AND current_status = 'FollowUp' AND internal_status != 'Missed_call_confirmed'
                AND sms_count < 3
                AND ss1.sms_tag IN ('sd_delivered_missed_call_initial',  'sd_shipped_missed_call_initial',
                'missed_call_initial_prod_desc_not_found', 'partner_missed_call_for_installation')";

        $query = $this->db->query($sql);
        
        log_message ('info', __METHOD__ . "=> Booking  SQL ". $this->db->last_query());
        
    	return  $query->result();
    }
    
    
    /*
     * @desc: This method is used to send reminder SMS to users for whom:
     * Partner source => Snapdeal-shipped-excel, Snapdeal-delivered-excel
     * EDD => Tommorrow (T+1), T+2, T+3 days
     * Current status => FollowUp
     * Vendor => Available
     */
    function get_reminder_installation_sms_data_future() {
        /*
	$sql = " SELECT booking_details.*, `services`.services from booking_details, services "
                . " where partner_source = 'Snapdeal-shipped-excel' AND internal_status = 'Missed_call_not_confirmed' "
	    . " AND estimated_delivery_date > CURDATE() AND estimated_delivery_date = (CURDATE() + INTERVAL 1 DAY) "
	    . " AND current_status= 'FollowUp' AND `booking_details`.service_id = `services`.id "
	    . " AND booking_pincode In (Select vendor_pincode_mapping.Pincode from vendor_pincode_mapping, "
	    . " service_centres where service_centres.id = vendor_pincode_mapping.Vendor_ID AND service_centres.active = '1' "
	    . " AND vendor_pincode_mapping.active = '1' );";
         * 
         */

        //Filter using booking_date instead of EDD
        $sql = "SELECT booking_details.*, `services`.services from booking_details, services 
              WHERE partner_source IN ('Snapdeal-shipped-excel', 'Snapdeal-delivered-excel' )
	      AND booking_date IN (
              DATE_FORMAT( CURDATE() + INTERVAL 1 DAY ,  '%d-%m-%Y' ),
              DATE_FORMAT( CURDATE() + INTERVAL 2 DAY ,  '%d-%m-%Y' ),
              DATE_FORMAT( CURDATE() + INTERVAL 3 DAY ,  '%d-%m-%Y' )
              )
	      AND current_status= 'FollowUp' AND internal_status != 'Missed_call_confirmed'
              AND `booking_details`.service_id = `services`.id;";
        
	$query = $this->db->query($sql);
        
        log_message ('info', __METHOD__ . "=> Booking  SQL ". $this->db->last_query());
        
    	return  $query->result();
    }
    
    
    /*
     * @desc: This method is used to send reminder SMS to users for whom:
     * Partner source => Snapdeal-shipped-excel, Snapdeal-delivered-excel
     * EDD => Past (T-1), T-2, T-3 days
     * Current status => FollowUp
     * Vendor => Available
     */
    function get_reminder_installation_sms_data_past() {
        //Filter using booking_date instead of EDD
        $sql = "SELECT booking_details.*, `services`.services from booking_details, services 
              WHERE partner_source IN ('Snapdeal-shipped-excel', 'Snapdeal-delivered-excel' )
	      AND booking_date IN (
              DATE_FORMAT( CURDATE() - INTERVAL 1 DAY ,  '%d-%m-%Y' ),
              DATE_FORMAT( CURDATE() - INTERVAL 2 DAY ,  '%d-%m-%Y' ),
              DATE_FORMAT( CURDATE() - INTERVAL 3 DAY ,  '%d-%m-%Y' ),
              DATE_FORMAT( CURDATE() - INTERVAL 4 DAY ,  '%d-%m-%Y' ),
              DATE_FORMAT( CURDATE() - INTERVAL 5 DAY ,  '%d-%m-%Y' ),
              DATE_FORMAT( CURDATE() - INTERVAL 6 DAY ,  '%d-%m-%Y' ),
              DATE_FORMAT( CURDATE() - INTERVAL 7 DAY ,  '%d-%m-%Y' ),
              DATE_FORMAT( CURDATE() - INTERVAL 8 DAY ,  '%d-%m-%Y' ),
              DATE_FORMAT( CURDATE() - INTERVAL 9 DAY ,  '%d-%m-%Y' ),
              DATE_FORMAT( CURDATE() - INTERVAL 10 DAY ,  '%d-%m-%Y' )
              )
	      AND current_status= 'FollowUp' AND internal_status != 'Missed_call_confirmed'
              AND `booking_details`.service_id = `services`.id;";
        
	$query = $this->db->query($sql);
        
        log_message ('info', __METHOD__ . "=> Booking  SQL ". $this->db->last_query());
        
    	return  $query->result();
    }

    
    function get_reminder_installation_sms_data_geyser_delhi() {
        //Filter using booking_date instead of EDD
        $sql = "SELECT booking_details.* from booking_details 
                WHERE partner_source IN ('Snapdeal-shipped-excel', 'Snapdeal-delivered-excel' )
                AND booking_date IN (
                    DATE_FORMAT( CURDATE() - INTERVAL 1 DAY,  '%d-%m-%Y' ),
                    DATE_FORMAT( CURDATE(),  '%d-%m-%Y' ),
                    ''
                )
                AND current_status = 'FollowUp' AND internal_status != 'Missed_call_confirmed'
                AND service_id=32 and booking_pincode regexp '^11';";
        
	$query = $this->db->query($sql);
        
        log_message ('info', __METHOD__ . "=> Booking  SQL ". $this->db->last_query());
        
    	return  $query->result();
    }
    /**
     * @desc: Get bookings, When booking date is empty, then getting those bookings which has 
     * difference between delivery date and current date are greater than 2.
     * AND When booking date is not empty, then getting those bookings which has difference between 
     *  delivery date and current date are greater than 5
     * @return Array
     */
    function get_old_pending_query(){
        $sql = " SELECT booking_id FROM booking_details WHERE booking_id LIKE '%Q-%' "
                . " AND partner_id = '1' "
                . " AND current_status = 'FollowUp' "
                . " AND CASE WHEN booking_date='' THEN DATEDIFF(CURRENT_TIMESTAMP , delivery_date) > 4 "
                . " WHEN booking_date !='' THEN DATEDIFF(CURRENT_TIMESTAMP , delivery_date) > 4 "
                . " END ";
        $query  = $this->db->query($sql);
        $result = $query->result_array();
        
        log_message ('info', __METHOD__ . "=> Count  Query to be Cancelled ". count($result));
        return $result;
    }
    /**
     * @desc: Get All bookings, who has not given Missed Call
     */
    function get_all_query(){
        $sql = "SELECT booking_details.* ,`services`.services from booking_details, services 
                    WHERE  `booking_id` LIKE  '%Q-%'
                    AND  `partner_id` =1
                    AND  `current_status` LIKE  'FollowUp'
                    AND `services`.id = `booking_details`.service_id ";
        
        $query  = $this->db->query($sql);
        $result = $query->result();
        
        log_message ('info', __METHOD__ . "=> Count  All Query ". count($result));
        return $result;
    }
    
    
    /**
     * @desc: This function is used get the user phone number to send promotional sms
     * @param:void()
     * @retun:void()
     */
    function get_user_phone_number($case){
        
        switch ($case){
            case 'completed' :
                $where = "current_status = '"._247AROUND_COMPLETED."'";
                $data = $this->get_completed_cancelled_booking_user_phn_number($where);
                break;
            case 'cancelled' :
                $where = "current_status = '"._247AROUND_CANCELLED."'";
                $data = $this->get_completed_cancelled_booking_user_phn_number($where);
                break;
            case 'query':
                $data = $this->get_cancelled_query_booking_user_phn_number();
                break;
            case 'not_exist':
                $data = $this->get_user_booking_not_exist_phn_number();
                break;
            case 'all':
                $data = $this->get_all_user_booking_phn_number();
                break;
        }
        
        if(!empty($data)){
            return $data;
        }else{
            return FALSE;
        }
    }
    
    
    /**
     * @desc: This function is used get the user phone number for completed booking
     * @param: $where array();
     * @retun:array();
     */
    function get_completed_cancelled_booking_user_phn_number($where){
        
        $sql = "SELECT DISTINCT booking_primary_contact_no as phn_number, current_status,user_id
                FROM booking_details JOIN partners 
                ON booking_details.partner_id = partners.id
                WHERE booking_primary_contact_no REGEXP '^[7-9]{1}[0-9]{9}$' 
                AND partners.is_sms_allowed = '1'
                AND $where AND DAY(closed_date) = DAY(CURDATE()) 
                UNION 
                SELECT DISTINCT booking_alternate_contact_no as phn_number,current_status,user_id
                FROM booking_details JOIN partners 
                ON booking_details.partner_id = partners.id
                WHERE booking_alternate_contact_no REGEXP '^[7-9]{1}[0-9]{9}$'
                AND partners.is_sms_allowed = '1'
                AND $where AND DAY(closed_date) = DAY(CURDATE())";
        
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    
    /**
     * @desc: This function is used get the user phone number for cancelled query
     * @param: void();
     * @retun:array();
     */
    function get_cancelled_query_booking_user_phn_number(){
        $sql = "SELECT DISTINCT booking_primary_contact_no as phn_number, 'Query' as current_status,user_id
                FROM booking_details JOIN partners 
                ON booking_details.partner_id = partners.id
                WHERE booking_primary_contact_no REGEXP '^[7-9]{1}[0-9]{9}$' 
                AND partners.is_sms_allowed = '1'
                AND booking_details.type = 'Query' AND booking_details.current_status = '"._247AROUND_CANCELLED."' AND DAY(closed_date) = DAY(CURDATE()) 
                UNION 
                SELECT DISTINCT booking_alternate_contact_no as phn_number, 'Query' as current_status,user_id
                FROM booking_details JOIN partners 
                ON booking_details.partner_id = partners.id
                WHERE booking_alternate_contact_no REGEXP '^[7-9]{1}[0-9]{9}$'
                AND partners.is_sms_allowed = '1'
                AND booking_details.type = 'Query' AND booking_details.current_status = '"._247AROUND_CANCELLED."' AND DAY(closed_date) = DAY(CURDATE())";
        
        $query = $this->db->query($sql);
        return $query->result_array();
        
    }
    
    /**
     * @desc: This function is used get the user phone number which booking does not 
     * exist in booking_details table
     * @param: void();
     * @retun:array();
     */
    function get_user_booking_not_exist_phn_number(){
        $sql = "SELECT users.user_id, users.phone_number as phn_number,'no_status' as 'current_status'
                FROM users LEFT JOIN booking_details ON users.user_id = booking_details.user_id 
                WHERE booking_details.user_id IS NULL AND users.phone_number REGEXP '^[7-9]{1}[0-9]{9}$'
                UNION
                SELECT users.user_id, users.alternate_phone_number as phn_number,'no_status' as 'current_status'
                FROM users LEFT JOIN booking_details ON users.user_id = booking_details.user_id 
                WHERE booking_details.user_id IS NULL AND users.alternate_phone_number REGEXP '^[7-9]{1}[0-9]{9}$'";
        
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    
    /**
     * @desc: This function is used get the all user phone number for all cases
     * @param: void();
     * @retun:array();
     */
    function get_all_user_booking_phn_number(){
        //get the data
        $completed_cancelled = "current_status = '"._247AROUND_COMPLETED."' OR current_status = '"._247AROUND_CANCELLED."' " ;
        $completed_cancelled_data = $this->get_completed_cancelled_booking_user_phn_number($completed_cancelled);
        $cancelled_query_data = $this->get_cancelled_query_booking_user_phn_number();
        $not_exist_booking_data = $this->get_user_booking_not_exist_phn_number();
        
        //merge data to form an array BIG_MAINDATA
        $this->BIG_MAINDATA = array_merge($completed_cancelled_data,$cancelled_query_data);
        
        //get the unique phone number from BIG_MAINDATA
        $unique_phn_number = array_unique(array_column($this->BIG_MAINDATA, 'phn_number'));
        
        $serach_phn_number = array();
        $i = 0;
        foreach ($unique_phn_number as $value) {
            $serach_phn_number[$i] = $this->search_phn_number_index($value);
            $i++;
        }
        $this->BIG_MAINDATA = array();
        
        //make a final data array to return
        $final_unique_phn_number_data = array_merge($serach_phn_number,$not_exist_booking_data);
        return $final_unique_phn_number_data;
        
    }
    
    
    /**
     * @desc: This function is used get the only unique number of all user and bookings
     * @param: $value_to_search string
     * @retun: array();
     */
    function search_phn_number_index($value_to_search) {

        $temp_arr = array();
        $return_arr = array();
        $i = 0;
        
        //get the index of those number which exist more than 1 times
        foreach ($this->BIG_MAINDATA as $key => $val) {
            if ($val['phn_number'] === $value_to_search) {
                $temp_arr[$i] = $key;
                $i++;
            }
        }

        $com = false;
        $can = false;
        $q_can = false;
        foreach ($temp_arr as $val) {
            if ($this->BIG_MAINDATA[$val]['current_status'] === 'Completed') {
                $com = $val;
            }
            if ($this->BIG_MAINDATA[$val]['current_status'] === 'Cancelled') {
                $can = $val;
            }
            if ($this->BIG_MAINDATA[$val]['current_status'] === 'Query') {
                $q_can = $val;
            }
        }
        
        // return data based on the booking status
        if ($com) {
            $key = $com;
        } else if ($can) {
            $key = $can;
        } else if ($q_can) {
            $key = $q_can;
        }

        $return_arr['phn_number'] = $this->BIG_MAINDATA[$key]['phn_number'];
        $return_arr['current_status'] = $this->BIG_MAINDATA[$key]['current_status'];
        $return_arr['user_id'] = $this->BIG_MAINDATA[$key]['user_id'];
        
        return $return_arr;
    }
    
    function get_status_changes_booking_with_in_hour($hour){
       
        $sql  = "SELECT DISTINCT bd.order_id, bd.partner_current_status, bd.booking_date, cancellation_reason, amount_paid"
                . " FROM booking_details as bd, "
                . " booking_state_change as bs WHERE "
                . " replace('Q-','',bd.booking_id) =  replace('Q-','',bs.booking_id) "
                . " AND bd.update_date >= DATE_ADD(NOW(), INTERVAL -$hour HOUR) AND bd.partner_id = '". JEEEVES_ID."'"
                . " AND old_state IN ('"._247AROUND_COMPLETED."', '"._247AROUND_FOLLOWUP."', "
                . " '"._247AROUND_PENDING."', '"._247AROUND_CANCELLED ."', '"._247AROUND_NEW_QUERY."', "
                . " '"._247AROUND_NEW_BOOKING."', 'Rescheduled') "
                . " AND new_state IN ('"._247AROUND_COMPLETED."', '"._247AROUND_FOLLOWUP."', "
                . " '"._247AROUND_PENDING."', '"._247AROUND_CANCELLED ."', '"._247AROUND_NEW_QUERY."', "
                . " '"._247AROUND_NEW_BOOKING."', 'Rescheduled')  AND new_state != old_state ";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    /**
     * @desc: This function is used get phone number for those user which booking 
     * is completed but rating is not taken yet 
     * @param: $from string
     * @param $to string
     * @retun:array();
     */
    function get_data_for_bookings_without_rating($from,$to){
        $where = "";
        if($from !== "" && $to !== ""){
            $from = date('Y-m-d', strtotime('-1 day', strtotime($from)));
            $to = date('Y-m-d', strtotime('+1 day', strtotime($to)));
            $where = "AND closed_date > '$from' AND closed_date < '$to'";
        }
        $sql = "SELECT DISTINCT booking_primary_contact_no as phn_number,user_id,booking_id
                FROM booking_details 
                WHERE booking_primary_contact_no REGEXP '^[7-9]{1}[0-9]{9}$' 
                AND rating_stars IS NULL AND current_status= '"._247AROUND_COMPLETED."' $where
                UNION
                SELECT DISTINCT booking_alternate_contact_no as phn_number,user_id,booking_id
                FROM booking_details 
                WHERE booking_alternate_contact_no REGEXP '^[7-9]{1}[0-9]{9}$' 
                AND rating_stars IS NULL AND current_status= '"._247AROUND_COMPLETED."' $where";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

}