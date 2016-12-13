<?php

class Around_scheduler_model extends CI_Model {
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
        $sql = "SELECT booking_details.*, `services`.services from booking_details, services 
              WHERE partner_source IN ('Snapdeal-shipped-excel', 'Snapdeal-delivered-excel', 'STS' )
	      AND booking_date IN (
              DATE_FORMAT( CURDATE(),  '%d-%m-%Y' ),
              ''
              )
	      AND current_status = 'FollowUp' AND internal_status != 'Missed_call_confirmed'
              AND `booking_details`.service_id = `services`.id;";
        
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
        
}