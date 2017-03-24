<?php

class dashboard_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }

    function get_paid_foc_booking_count($startDate = "", $endDate = "" , $partnerid = "") {
        $where = "where create_date >= '$startDate' AND create_date <= '$endDate'";
        if($partnerid != ""){
            $where .= "AND partner_id = $partnerid";
        }
        $sql = "SELECT 
                SUM(IF(partner_net_payable > 0, 1, 0)) AS FOC,
                SUM(IF(partner_net_payable = 0 , 1, 0)) AS Paid
                FROM 
                booking_unit_details $where";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

}
