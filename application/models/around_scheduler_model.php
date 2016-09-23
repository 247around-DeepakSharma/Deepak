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
     * @desc: This method is used to get data who has
     * Partner source => Snapdeal-shipped-excel
     * Internal Status => Missed_call_not_confirmed
     * EDD => Tommorrow
     * Current status => FollowUp
     */
    function get_reminder_installation_sms_data() {
	$sql = " SELECT booking_details.*, `services`.services from booking_details, services "
                . " where partner_source = 'Snapdeal-shipped-excel' AND internal_status = 'Missed_call_not_confirmed' "
	    . " AND estimated_delivery_date > CURDATE() AND estimated_delivery_date = (CURDATE() + INTERVAL 1 DAY) "
	    . " AND current_status= 'FollowUp' AND `booking_details`.service_id = `services`.id "
	    . " AND booking_pincode In (Select vendor_pincode_mapping.Pincode from vendor_pincode_mapping, "
	    . " service_centres where service_centres.id = vendor_pincode_mapping.Vendor_ID AND service_centres.active = '1' "
	    . " AND vendor_pincode_mapping.active = '1' );";

	$query = $this->db->query($sql);
        
        log_message ('info', __METHOD__ . "=> Booking  SQL ". $this->db->last_query());
        
    	return  $query->result_array();

    }
}