<?php

class Initialized_variable {
    Private $PartnerData = array();
    Private $BuybackOderDetails = array();

    public function __construct() {
	$this->My_CI = & get_instance();
        $this->My_CI->load->helper(array('form', 'url'));
        $this->My_CI->load->model('partner_model');
	
    }
    /**
     * @desc Fetch Partner details from Partner ID
     * @param Int $partner_id
     */
    function fetch_partner_data($partner_id){
        $where_get_partner = array('bookings_sources.partner_id' => $partner_id);
        $select = "partners.id, bookings_sources.partner_id,bookings_sources.partner_type, bookings_sources.source, bookings_sources.code, "
                                . " partners.upcountry_approval, upcountry_mid_distance_threshold,"
                                . " upcountry_min_distance_threshold, upcountry_max_distance_threshold, "
                                . " upcountry_rate1, upcountry_rate, partners.is_upcountry, public_name";
        $this->PartnerData = $this->My_CI->partner_model->getpartner_details($select, $where_get_partner);
    }
    /**
     * Get Partner details. First Call fetch_partner_data then call this method
     * @return type
     */
    function get_partner_data(){
        return $this->PartnerData;
    }
    
    function set_post_buyback_order_details($order_details){
        $this->BuybackOderDetails = $order_details;
    }
    
    function get_post_buyback_order_details(){
        return $this->BuybackOderDetails;
    }
}