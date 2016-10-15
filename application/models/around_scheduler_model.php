<?php

class Around_scheduler_model extends CI_Model {
    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();

        $this->db = $this->load->database('default', TRUE, TRUE);
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
              WHERE partner_source IN ('Snapdeal-shipped-excel', 'Snapdeal-delivered-excel' )
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
}