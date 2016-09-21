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
     * Internal Status => Missed_call_confirmed
     * Edd => Tommorrow
     * Current status => FollowUp
     */
    function send_remainder_installation_sms(){
    	$sql = " SELECT booking_details.*, `services`.services from booking_details, services "
                . "where partner_source = 'Snapdeal-shipped-excel' AND internal_status = 'Missed_call_confirmed' "
                . "AND estimated_delivery_date > CURDATE() AND estimated_delivery_date = (CURDATE() + INTERVAL 1 DAY) "
                . "AND current_status= 'FollowUp' AND `booking_details`.service_id = `services`.id "
                . "AND booking_pincode In (Select Pincode from vendor_pincode_mapping);";

    	$query = $this->db->query($sql);
    	return  $query->result_array();
    	
    }
}