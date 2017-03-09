<?php

class Invoice_dashboard_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }
    /**
     * @desc This is used to get count completed  line item
     */
    function get_count_unit_details($from_date_tmp,$to_date_tmp){
        $from_date = date('Y-m-d', strtotime('-1 months', strtotime($from_date_tmp)));
        $to_date = date('Y-m-d', strtotime('+1 day', strtotime($to_date_tmp)));
        
        $sql = "SELECT booking_unit_details.partner_id, source, COUNT( booking_unit_details.id ) AS total_unit
                FROM  `booking_unit_details` , bookings_sources
                WHERE booking_status =  'Completed'
                AND ud_closed_date >=  '$from_date'
                AND ud_closed_date <  '$to_date'
                AND partner_invoice_id IS NULL 
                AND partner_net_payable > 0
                AND booking_unit_details.partner_id = bookings_sources.partner_id
                GROUP BY booking_unit_details.partner_id
                ";
        
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    /**
     * @desc  get count by appliance and price tag
     * @param String $partner_id
     */
    function get_count_services($partner_id, $from_date_tmp, $to_date_tmp){
        $from_date = date('Y-m-d', strtotime('-1 months', strtotime($from_date_tmp)));
        $to_date = date('Y-m-d', strtotime('+1 day', strtotime($to_date_tmp)));
        $sql = "SELECT ud.service_id, COUNT( ud.id ) AS total_unit,
                CASE 
               
                    WHEN MIN( ud.`appliance_capacity` ) = '' AND MAX( ud.`appliance_capacity` ) = '' THEN
                    concat(services,' ', price_tags )

                    WHEN MIN( ud.`appliance_capacity` ) = '' AND MAX( ud.`appliance_capacity` ) != '' THEN 
                    concat(services,' ', price_tags,' (', 
                    MAX( ud.`appliance_capacity` ),') ' )

                    WHEN MIN( ud.`appliance_capacity` ) = MAX( ud.`appliance_capacity` ) THEN 
                    concat(services,' ', price_tags,' (', 
                    MAX( ud.`appliance_capacity` ),') ' )


                    WHEN MIN( ud.`appliance_capacity` ) != '' AND MAX( ud.`appliance_capacity` ) = '' THEN
                     concat(services,' ', price_tags,' (', 
                    MIN( ud.`appliance_capacity` ),') ' )
                
                ELSE 
                    concat(services,' ', price_tags,' (', MIN( ud.`appliance_capacity` ),
                '-',MAX( ud.`appliance_capacity` ),') ' )
                
                
                END AS services
                FROM  `booking_unit_details` as ud , services
                WHERE booking_status =  'Completed'
                AND ud_closed_date >=  '$from_date'
                AND ud_closed_date <  '$to_date'
                AND partner_invoice_id IS NULL 
                AND partner_id = '$partner_id'
                AND partner_net_payable > 0
                AND ud.service_id = services.id
               GROUP BY  `partner_net_payable`,ud.service_id,price_tags  ";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    /**
     * @desc: Duplicate entry in unit details
     * @param String $partner_id
     */
    function check_duplicate_completed_booking($partner_id,$from_date_tmp, $to_date_tmp){
        $from_date = date('Y-m-d', strtotime('-1 months', strtotime($from_date_tmp)));
        $to_date = date('Y-m-d', strtotime('+1 day', strtotime($to_date_tmp)));
        $sql ="SELECT DISTINCT (
                b1.`booking_id`
                ), b1.`price_tags` 
                FROM  `booking_unit_details` AS b1,  `booking_unit_details` AS b2
                WHERE b1.`booking_id` = b2.`booking_id` 
                AND b1.`price_tags` = b2.`price_tags` 
                AND b1.id != b2.id
                AND b1.partner_id = '$partner_id'
                AND b2.partner_id = '$partner_id'
                AND b1.ud_closed_date >=  '$from_date'
                AND b1.ud_closed_date <  '$to_date'
                AND (
                b1.booking_status !=  'Cancelled'
                OR b2.booking_status !=  'Cancelled'
                )
                AND (
                b2.booking_status
                IN (
                 'Completed'
                )
                OR b1.booking_status
                IN (
                 'Completed'
                )
            )";
        
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    /**
     * @desc: Wall Mount stand added but installation not added
     * @param String $partner_id
     */
    function installation_not_added($partner_id,$from_date_tmp, $to_date_tmp){
        $from_date = date('Y-m-d', strtotime('-1 months', strtotime($from_date_tmp)));
        $to_date = date('Y-m-d', strtotime('+1 day', strtotime($to_date_tmp)));
        $sql = "SELECT booking_id FROM `booking_unit_details`"
                . " WHERE booking_status = 'Completed' "
                . " AND ud_closed_date >= '$from_date' AND price_tags = 'Wall Mount Stand' "
                . " AND ud_closed_date < '$to_date' "
                . " AND service_id ='46' "
                . " AND booking_id NOT IN (SELECT booking_id FROM `booking_unit_details` "
                . " WHERE booking_status = 'Completed' "
                . " AND ud_closed_date >= '$from_date' "
                . " AND ud_closed_date < '$to_date' "
                . " AND partner_id = '$partner_id' "
                . " AND price_tags = 'Installation & Demo' "
                . " AND service_id ='46')";
        
        $query = $this->db->query($sql);
        return $query->result_array();
    }
}