<?php

class dashboard_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }
    
    /**
     * @desc This function is used to get total count for foc and paid 
     * @param: string
     * @return array this function will return array containing data of total foc or paid bookings
     */
    function get_paid_foc_booking_count($startDate = "", $endDate = "" , $current_status = "",$partner_id="") {
        $where = "";
        if($current_status == ''){
            $current_status = 'Completed';
        }
        if($current_status == 'Completed' || $current_status == 'Cancelled'){
            $where = "AND booking_unit_details.ud_closed_date >=". "'$startDate'" . " AND booking_unit_details.ud_closed_date <=" ."'$endDate'";
        }
//        else if ($current_status == 'FollowUp' || $current_status == 'Pending' || $current_status == 'Rescheduled'){
//            $where = "AND booking_unit_details.create_date >=". "'$startDate'" . " AND booking_unit_details.create_date <=" ."'$endDate'";
//        }
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
    
    /**
     * @desc This function is used to get total count for foc Or paid 
     * @param: string
     * @return array this function will return array containing data of total foc OR paid bookings
     */
    function get_total_foc_or_paid_booking($startDate, $endDate , $type = "",$current_status = ""){
        $where = "";
        if($current_status == 'Completed' || $current_status == 'Cancelled'){
            $where = "AND booking_unit_details.ud_closed_date >=". "'$startDate'" . " AND booking_unit_details.ud_closed_date <=" ."'$endDate'";
        }
//        else if ($current_status == 'FollowUp' || $current_status == 'Pending' || $current_status == 'Rescheduled'){
//            $where = "AND booking_unit_details.create_date >=". "'$startDate'" . " AND booking_unit_details.create_date <=" ."'$endDate'";
//        }
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
    
    
    /**
     * @desc This function is used to get partner booking data group by appliance
     * @param: string
     * @return array 
     */
    function get_partner_booking_based_on_services($startDate, $endDate , $current_status,$partner_id){
        $where="";
        if($current_status == 'Completed' || $current_status == 'Cancelled'){
            $where .= "booking_details.closed_date >=". "'$startDate'" . " AND booking_details.closed_date <=" ."'$endDate'";
        }else if ($current_status == 'FollowUp' || $current_status == 'Pending' || $current_status == 'Rescheduled'){
            $where .= "booking_details.create_date >=". "'$startDate'" . " AND booking_details.create_date <=" ."'$endDate'";
        }
        
        $sql = "SELECT service_id, services,count(booking_details.id) as total
                FROM booking_details LEFT JOIN services 
                ON booking_details.service_id = services.id 
                WHERE partner_id = '$partner_id' AND current_status = '$current_status' AND $where
                GROUP BY service_id";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    
    /**
     * @desc This function is used to get booking data for perticular partner_id based on current status
     * @param: string
     * @return array 
     */
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
    
     /**
     * @desc This function is used to get booking data for perticular partner_id based on it is foc or paid
     * case 1: when partner_id is null then get data for all partner
     * case 2: when partner is not null then get data according to partner_id
     * @param: string
     * @return array 
     */
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
    /**
     * @desc: This function is used to get the booking data grou by request type on  ajax call
     * @param string
     * @return array
     */
    function get_data_onScroll($startDate = "", $endDate = "",$partner_id=""){
        $where = "booking_details.create_date >=". "'$startDate'" . " AND booking_details.create_date <=" ."'$endDate'";
        if($partner_id != ""){
            $where .= "AND partner_id = '$partner_id'";
        }
        $this->db->select('request_type,count(*) as total');
        $this->db->from('booking_details');
        $this->db->where($where);
        $this->db->group_by('request_type');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    
    /**
     * @desc: This function is used to get booking data based on request_type and 
     * group by current_status
     * case 1: when partner_id is null then get data for all partner
     * case 2: when partner is not null then get data according to partner_id
     * @param string
     * @return array
     */
    function get_bookings_basedon_request_type_status($startDate = "", $endDate = "",$request_type = "",$partner_id=""){
        $where = "request_type = '$request_type' AND booking_details.create_date >=". "'$startDate'" . " AND booking_details.create_date <=" ."'$endDate'";
        if($partner_id != ""){
            $where .= "AND partner_id = '$partner_id'";
        }
        $this->db->select('current_status,count(*) as total');
        $this->db->from('booking_details');
        $this->db->where($where);
        $this->db->group_by('current_status');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * @desc: This function is used to get booking based on RM 
     * @param string
     * @return array
     */
    function get_booking_data_by_rm_region($startDate = "", $endDate = "",$service_centers_id,$partner_id=""){
        $where = "Where bd1.assigned_vendor_id IN($service_centers_id)";
        if($partner_id != ""){
            $where .= "AND bd1.partner_id = '$partner_id'";
        }
        $sql = "SELECT SUM(Completed)+SUM(Cancelled) + SUM(Pending) as Total , Completed as Completed,Cancelled as Cancelled , Pending as Pending FROM 
                        (SELECT 
                            SUM(IF(current_status ='Completed' && closed_date >= '$startDate' && closed_date <= '$endDate' , 1, 0)) AS Completed,
                            SUM(IF(current_status ='Cancelled' && closed_date >= '$startDate' && closed_date <= '$endDate' , 1, 0)) AS Cancelled,
                            SUM(IF(current_status IN ('Pending','Rescheduled') && create_date >= '$startDate' && create_date <= '$endDate', 1, 0)) AS Pending
                            FROM booking_details as bd1 $where) as bd2";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    /**
     * @desc: This function is used to get booking entered and scheduled data
     * @param string
     * @return array
     */
    function get_partners_inflow_data($startDate, $endDate){
        $final_data = [];
        
        //get partner id to start loop
        $this->db->select('id,public_name');
        $this->db->from('partners');
        $this->db->where('is_active','1');
        $this->db->order_by('public_name');
        $query = $this->db->get();
        $partner_id = $query->result_array();
        foreach($partner_id as $value){
            //query for getting entered booking data
            $booking_entered_sql = "Select count(*) as booking_entered
                                    FROM booking_details 
                                    WHERE booking_details.partner_id= '" . $value['id'] . "'
                                    AND booking_details.create_date >='$startDate' AND booking_details.create_date<='$endDate'";
            
            $query = $this->db->query($booking_entered_sql);
            $data['booking_entered'] = $query->result_array()[0]['booking_entered'];
            //query for getting booking scheduled data case assume here : 
            //old_state = new_booking OR followup
            //new_state = pending
            $booking_pending_sql = "SELECT COUNT(*) as booking_pending
                                    FROM booking_state_change 
                                    JOIN booking_details
                                    ON booking_state_change.booking_id = booking_details.booking_id
                                    WHERE booking_details.partner_id= '" . $value['id'] . "' 
                                    AND old_state IN('New_Booking','FollowUp') AND new_state = 'Pending' 
                                    AND booking_state_change.create_date >='$startDate' AND booking_state_change.create_date<='$endDate'";
            $query1 = $this->db->query($booking_pending_sql);
            $data['booking_pending'] = $query1->result_array()[0]['booking_pending'];
            $data['partner_name'] = $value['public_name'];
            //store all data into array $data
            array_push($final_data, $data);
        }
        return $final_data;
    }
    
    /**
     * @desc: This function is used to get partner completed booking data 
     * based on month
     * @param string
     * @return array
     */
    function get_partner_monthly_bookings($partner_id){
        $status = 'Completed';
        $sql = "SELECT DATE_FORMAT(closed_date, '%b') AS month,DATE_FORMAT(closed_date, '%Y') AS year, COUNT(*) as completed_booking
                FROM booking_details
                WHERE current_status = '$status' AND partner_id = '$partner_id'
                AND closed_date >= (NOW() - INTERVAL 11 MONTH)
                GROUP BY DATE_FORMAT(closed_date, '%m-%Y') 
                ORDER BY YEAR(closed_date),MONTH(closed_date)";
        $query = $this->db->query($sql);
        $completed_booking = $query->result_array();
        return $completed_booking;
    }
}
