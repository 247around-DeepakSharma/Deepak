<?php

class Initialized_variable {
    Private $PartnerData = array();
    Private $BuybackOderDetails = array();
    Private $t_delivered = 0;
    Private $t_inserted = 0;
    Private $t_updated = 0;
    Private $t_not_assigned = 0;

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
                                . " upcountry_rate1, upcountry_rate, partners.is_upcountry, public_name, partners.auth_token, "
                                . "upcountry_approval_email,group_concat(distinct agent_filters.agent_id) as account_manager_id, upcountry_bill_to_partner";
                                //. "upcountry_approval_email,partners.account_manager_id, upcountry_bill_to_partner";
        //$this->PartnerData = $this->My_CI->partner_model->getpartner_details($select, $where_get_partner);
        $this->PartnerData = $this->My_CI->partner_model->getpartner_data($select, $where_get_partner,"",0,1,1,"partners.id");
    }
    /**
     * Get Partner details. First Call fetch_partner_data then call this method
     * @return type
     */
    function get_partner_data(){
        return $this->PartnerData;
    }
    /**
     * @desc Set buyback post data
     * @param Array $order_details
     */
    function set_post_buyback_order_details($order_details){
        $this->BuybackOderDetails = $order_details;
    }
    /**
     * @desc get buyback post data
     * @return Array
     */
    function get_post_buyback_order_details(){
        return $this->BuybackOderDetails;
    }
    /**
     * @desc tottal order updated or inserted for delivered
     * @return int
     */
    function delivered_count(){
        return $this->t_delivered++;
    }
    /**
     * @desc return total order inserted in buyback
     * @return Int
     */
    function total_inserted(){
        return $this->t_inserted++;
    }
    /**
     * @desc return totla order updated for buyback
     * @return Int
     */
    function total_updated(){
        return $this->t_updated++;
    }
    
    function not_assigned_order(){
        return $this->t_not_assigned++;
    }
}