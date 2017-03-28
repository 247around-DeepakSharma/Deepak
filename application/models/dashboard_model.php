<?php

class dashboard_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }

    function get_paid_foc_booking_count($startDate = "", $endDate = "" , $current_status = "",$partner_id="") {
        $where = "";
        if($current_status == ''){
            $current_status = 'Completed';
        }
        if($current_status == 'Completed' || $current_status == 'Cancelled'){
            $where = "AND booking_unit_details.ud_closed_date >=". "'$startDate'" . " AND booking_unit_details.ud_closed_date <=" ."'$endDate'";
        }else if ($current_status == 'FollowUp' || $current_status == 'Pending' || $current_status == 'Rescheduled'){
            $where = "AND booking_unit_details.create_date >=". "'$startDate'" . " AND booking_unit_details.create_date <=" ."'$endDate'";
        }
        if($partner_id != ''){
            $where .= "AND partner_id = '$partner_id'";
        }
        $sql = "SELECT 
                SUM(IF(partner_net_payable > 0, 1, 0)) AS FOC,
                SUM(IF(partner_net_payable = 0 , 1, 0)) AS Paid
                FROM 
                booking_unit_details WHERE SUBSTR(booking_unit_details.booking_id ,1,2) <> 'Q-' AND booking_status = '$current_status' $where";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    function get_total_foc_or_paid_booking($startDate, $endDate , $type = "",$current_status = ""){
        $where = "";
        if($current_status == 'Completed' || $current_status == 'Cancelled'){
            $where = "AND booking_unit_details.ud_closed_date >=". "'$startDate'" . " AND booking_unit_details.ud_closed_date <=" ."'$endDate'";
        }else if ($current_status == 'FollowUp' || $current_status == 'Pending' || $current_status == 'Rescheduled'){
            $where = "AND booking_unit_details.create_date >=". "'$startDate'" . " AND booking_unit_details.create_date <=" ."'$endDate'";
        }
        if($type == 'FOC'){
            $where .= 'AND partner_net_payable > 0';
        }else if($type == 'PAID'){
            $where .= 'AND partner_net_payable = 0';
        }
        $sql = "SELECT public_name, COUNT(*) as count
                FROM booking_unit_details LEFT JOIN partners 
                ON booking_unit_details.partner_id = partners.id 
                WHERE SUBSTR(booking_unit_details.booking_id ,1,2) <> 'Q-' AND booking_status = '$current_status' $where
                GROUP BY partner_id ";
        $query = $this->db->query($sql);
        return $query->result_array();
        
    }
    
    function get_partner_booking_based_on_services($startDate, $endDate , $current_status,$partner_id){
        $where="";
        if($current_status == 'Completed' || $current_status == 'Cancelled'){
            $where .= "booking_details.closed_date >=". "'$startDate'" . " AND booking_details.closed_date <=" ."'$endDate'";
        }else if ($current_status == 'FollowUp' || $current_status == 'Pending' || $current_status == 'Rescheduled'){
            $where .= "booking_details.create_date >=". "'$startDate'" . " AND booking_details.create_date <=" ."'$endDate'";
        }
        
        $sql = "SELECT service_id, services,count(*) as total
                FROM booking_details LEFT JOIN services 
                ON booking_details.service_id = services.id 
                WHERE partner_id = '$partner_id' AND current_status = '$current_status' AND $where
                GROUP BY service_id";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    function get_partner_bookings_data($startDate, $endDate ,$partner_id){
        $sql = "SELECT 
                        SUM(IF(current_status ='Completed' && closed_date >= '$startDate' && closed_date <= '$endDate' , 1, 0)) AS Completed,
                        SUM(IF(current_status ='Cancelled' && closed_date >= '$startDate' && closed_date <= '$endDate' , 1, 0)) AS Cancelled,
                        SUM(IF(current_status ='Pending' && create_date >= '$startDate' && create_date <= '$endDate', 1, 0)) AS Pending,
                        SUM(IF(current_status ='Rescheduled' && create_date >= '$startDate' && create_date <= '$endDate' , 1, 0)) AS Rescheduled,
                        SUM(IF(current_status ='FollowUp' && create_date >= '$startDate' && create_date <= '$endDate' , 1, 0)) AS FollowUp
                        FROM booking_details WHERE partner_id = '$partner_id'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    function  get_paid_or_foc_booking_groupby_services($startDate = "", $endDate = "" , $type = "", $current_status = "",$partner_id=""){
        $where = "";
        if($current_status == ''){
            $current_status = 'Completed';
        }
        if($current_status == 'Completed' || $current_status == 'Cancelled'){
            $where = "AND booking_unit_details.ud_closed_date >=". "'$startDate'" . " AND booking_unit_details.ud_closed_date <=" ."'$endDate'";
        }else if ($current_status == 'FollowUp' || $current_status == 'Pending' || $current_status == 'Rescheduled'){
            $where = "AND booking_unit_details.create_date >=". "'$startDate'" . " AND booking_unit_details.create_date <=" ."'$endDate'";
        }
        if($partner_id != ''){
            $where .= "AND partner_id = '$partner_id'";
        }
        if($type == 'FOC'){
            $where .= 'AND partner_net_payable > 0';
        }else if($type == 'PAID'){
            $where .= 'AND partner_net_payable = 0';
        }
        $sql = "SELECT services,COUNT(*) as total
                FROM booking_unit_details 
                JOIN services ON booking_unit_details.service_id = services.id 
                WHERE SUBSTR(booking_unit_details.booking_id ,1,2) <> 'Q-' 
                AND booking_status = '$current_status' $where
                GROUP BY services";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

}
