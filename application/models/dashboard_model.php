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
        $sql = "SELECT DISTINCT
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
        $sql = "SELECT DISTINCT public_name, COUNT(*) as count
                FROM booking_unit_details LEFT JOIN partners 
                ON booking_unit_details.partner_id = partners.id 
                WHERE SUBSTR(booking_unit_details.booking_id ,1,2) <> 'Q-' AND booking_status = '$current_status' $where
                GROUP BY partner_id ";
        $query = $this->db->query($sql);
        return $query->result_array();
        
    }
    
    function get_not_assigned_booking_report_data($manager_id = NULL, $employee_id = NULL, $group_by_state = false) {
        
        $where = '';
        if(!empty($employee_id) || !empty($manager_id)) {
            $where .= " and agent_state_mapping.agent_id IN (".trim($manager_id.','.$employee_id, ',').")";
        }
        
        $sql = "select 
                    employee.id,
                    employee.full_name,
                    booking_details.state,
                    group_concat(distinct state_code.state_code) as state_codes,
                    count(booking_details.id) as number_of_bookings
                from 
                    booking_details
                    left join state_code on (booking_details.state = state_code.state)
                    left join agent_state_mapping on (state_code.state_code, agent_state_mapping.state_code)
                    left join employee_hierarchy_mapping on (agent_state_mapping.agent_id = employee_hierarchy_mapping.manager_id)
                    left join employee on (agent_state_mapping.agent_id = employee.id and employee.groups IN ('"._247AROUND_RM."','"._247AROUND_ASM."'))
                where 
                    assigned_vendor_id is null AND booking_details.state <> ''	{$where}
                group by 
                    agent_state_mapping.agent_id".(!empty($group_by_state) ? ',booking_details.state' : '')." order by employee.full_name";
                    
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
        $sql = "SELECT DISTINCT services,COUNT(*) as total
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
        $service_center_array = explode(",",$service_centers_id);
        $sc_id = implode("','",$service_center_array);
        $where = "Where bd1.assigned_vendor_id IN('".$sc_id."')";
        if($partner_id != ""){
            $where .= "AND bd1.partner_id = '$partner_id'";
        }
        $sql = "SELECT SUM(Completed)+SUM(Cancelled) + SUM(Pending) as Total , Completed as Completed,Cancelled as Cancelled , Pending as Pending FROM 
                        (SELECT 
                            SUM(IF(!(current_status = 'Cancelled' OR internal_status ='InProcess_Cancelled') && service_center_closed_date >= '$startDate' && service_center_closed_date <= '$endDate' , 1, 0)) AS Completed,
                            SUM(IF((current_status = 'Cancelled' OR internal_status = 'InProcess_Cancelled') && service_center_closed_date >= '$startDate' && service_center_closed_date <= '$endDate' , 1, 0)) AS Cancelled,
                            SUM(IF(current_status IN ('Pending','Rescheduled') && (create_date >= '$startDate' && create_date <= '$endDate') && (service_center_closed_date IS NULL), 1, 0)) AS Pending
                            FROM booking_details as bd1 $where) as bd2";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    /**
     * @desc: This function is used to get graph data for review completed bookings by closure team
     * @param $startDate, $endDate
     * @return array
     */
    function get_completed_booking_graph_data($startDate, $endDate){
        $sql = "SELECT 
                    data.full_name,
                    data.id,
                    SUM(IF(new_state = 'Completed_Rejected',1,0)) AS completed_rejected,
                    SUM(IF(new_state = 'Completed_Approved',1,0)) AS completed_approved,
                    SUM(IF(new_state = 'Completed' OR new_state = 'Completed_With_Rating', 1, 0)) AS total_completed,
                    (SUM(IF(new_state = 'Completed' OR new_state = 'Completed_With_Rating', 1, 0)) - SUM(IF(new_state = 'Completed_Approved',1,0))) AS edit_completed,
                    (SUM(IF(new_state = 'Completed_Rejected',1,0)) + SUM(IF(new_state = 'Completed' OR new_state = 'Completed_With_Rating', 1, 0))) AS total_bookings
                FROM
                    (SELECT 
                        employee.full_name, employee.id, new_state, employee_id
                    FROM
                        booking_state_change AS bsc, employee
                    WHERE
                        employee.groups = 'closure'
                            AND bsc.create_date >= '$startDate'
                            AND bsc.create_date <= '$endDate'
                            AND agent_id = employee.id
                            AND partner_id = '"._247AROUND."'
                    GROUP BY employee_id , booking_id , bsc.new_state, DATE(bsc.create_date)) AS data
                GROUP BY data.employee_id";            
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    /**
     * @desc: This function is used to get excel data for review completed bookings by closure team
     * @param $startDate, $endDate
     * @return array
     */
    function get_completed_booking_excel_data($startDate, $endDate){
        $this->db->_protect_identifiers = FALSE;
        
        // set select statement
        $select = "employee.full_name AS 'Agent Name',
                    booking_details.booking_id AS 'Booking Id',
                    service_centres.name AS 'Vendor Name',
                    booking_details.request_type AS 'Request Type',
                    partners.public_name AS Partner,
                    services.services AS Product,
                    IF(new_state = 'Completed_Rejected',1,0) AS 'Rejected Bookings',
                    IF(new_state = 'Completed_Approved',1,0) AS 'Directly Approved Bookings',
                    IF((new_state = 'Completed' && old_state != 'Completed_Approved'), 1, 0) AS 'Approved With Edit Bookings',
                    IF((new_state = 'Completed_Rejected' || new_state = 'Completed_Approved' || (new_state = 'Completed' && old_state != 'Completed_Approved')),1,0) AS 'Total Bookings'";
        
        // set where condition
        $where = "employee.groups = 'closure'
                    AND bsc.create_date >= '$startDate'
                    AND bsc.create_date <= '$endDate'
                    AND bsc.partner_id = '"._247AROUND."'";
        
        $condition = "`Rejected Bookings` = '1' OR `Directly Approved Bookings` = '1' OR `Approved With Edit Bookings` = '1' OR `Total Bookings` = '1'";
        // Query here
        $this->db->select($select);
        $this->db->from('booking_state_change AS bsc');
        $this->db->join('booking_details', 'bsc.booking_id = booking_details.booking_id');
        $this->db->join('service_centres', 'booking_details.assigned_vendor_id = service_centres.id');
        $this->db->join('partners', 'booking_details.partner_id = partners.id');
        $this->db->join('services', 'booking_details.service_id = services.id');
        $this->db->join('employee', 'bsc.agent_id = employee.id');
        $this->db->where($where);
        $this->db->order_by('employee.id');
        $this->db->group_by("employee.id,bsc.booking_id, bsc.new_state, DATE(bsc.create_date)");
        $this->db->having($condition);
        // return query object
        $query = $this->db->get();
        return $query;
    }
    
    /**
     * @desc: This function is used to get graph data for review canceled bookings by closure team
     * @param $startDate, $endDate
     * @return array
     */
    function get_cancelled_booking_graph_data($startDate, $endDate){
        $sql = "SELECT employee.full_name, employee.id, SUM(IF (new_state = 'Cancelled_Rejected', 1, 0)) as completed_rejected,
                    SUM(IF (new_state = 'Cancelled_Approved', 1, 0)) as completed_approved,
                    SUM(IF (new_state = 'Cancelled', 1, 0)) as total_completed,
                    SUM(IF ((old_state = 'InProcess_Cancelled' AND new_state = 'Completed'), 1, 0)) as edit_completed,
                    (SUM(IF (new_state = 'Cancelled', 1, 0)) - SUM(IF (new_state = 'Cancelled_Approved', 1, 0))) as edit_cancelled,
                    (SUM(IF (new_state = 'Cancelled_Rejected', 1, 0)) + SUM(IF (new_state = 'Cancelled_Approved', 1, 0)) + SUM(IF ((old_state = 'InProcess_Cancelled' AND new_state = 'Completed'), 1, 0)) + (SUM(IF (new_state = 'Cancelled', 1, 0)) - SUM(IF (new_state = 'Cancelled_Approved', 1, 0)))) as total_bookings
                    FROM booking_state_change as bsc, employee WHERE employee.groups ='closure' AND bsc.create_date >= '$startDate' AND bsc.create_date <= '$endDate' AND agent_id = employee.id AND partner_id='"._247AROUND."' group by employee_id";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    /**
     * @desc: This function is used to get excel data for review cancelled bookings by closure team
     * @param $startDate, $endDate
     * @return array
     */
    function get_cancelled_booking_excel_data($startDate, $endDate){
        $this->db->_protect_identifiers = FALSE;
        
        // set select statement
        $select = "employee.full_name as 'Agent Name',
                    booking_details.booking_id as 'Booking Id',
                    service_centres.name as 'Vendor Name',
                    booking_details.request_type as 'Request Type',
                    partners.public_name as Partner,
                    services.services as Product,
                    SUM(IF (new_state = 'Cancelled_Rejected', 1, 0)) as 'Rejected Bookings',
                    SUM(IF (new_state = 'Cancelled_Approved', 1, 0)) as 'Directly Approved Bookings',
                    (SUM(IF (new_state = 'Cancelled', 1, 0)) - SUM(IF (new_state = 'Cancelled_Approved', 1, 0))) as 'Edit Cancelled Bookings',
                    SUM(IF ((old_state = 'InProcess_Cancelled' AND new_state = 'Completed'), 1, 0)) as 'Cancelled to Completed Bookings',
                    (SUM(IF (new_state = 'Cancelled_Rejected', 1, 0)) + SUM(IF (new_state = 'Cancelled_Approved', 1, 0)) + SUM(IF ((old_state = 'InProcess_Cancelled' AND new_state = 'Completed'), 1, 0)) + (SUM(IF (new_state = 'Cancelled', 1, 0)) - SUM(IF (new_state = 'Cancelled_Approved', 1, 0)))) as 'Total Bookings'";
        
        // set where condition
        $where = "employee.groups = 'closure'
                    AND bsc.create_date >= '$startDate'
                    AND bsc.create_date <= '$endDate'
                    AND bsc.partner_id = '"._247AROUND."'";
        $condition = "`Rejected Bookings` != '0' OR `Directly Approved Bookings` != '0' OR `Cancelled to Completed Bookings` != '0' OR `Edit Cancelled Bookings` != '0' OR `Total Bookings` != '0'";
        // Query here
        $this->db->select($select);
        $this->db->from('booking_state_change AS bsc');
        $this->db->join('booking_details', 'bsc.booking_id = booking_details.booking_id');
        $this->db->join('service_centres', 'booking_details.assigned_vendor_id = service_centres.id');
        $this->db->join('partners', 'booking_details.partner_id = partners.id');
        $this->db->join('services', 'booking_details.service_id = services.id');
        $this->db->join('employee', 'bsc.agent_id = employee.id');
        $this->db->where($where);
        $this->db->order_by('employee.id');
        $this->db->group_by('bsc.booking_id');
        $this->db->having($condition);
        // return query object
        $query = $this->db->get();        
        return $query;
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
    function get_bookings_data_by_month($partner_id = ""){
        
        if(!empty($partner_id)){
            $where = "!(current_status = 'Cancelled' OR internal_status ='InProcess_Cancelled') AND partner_id = '$partner_id'";
        }else{
            $where = "!(current_status = 'Cancelled' OR internal_status ='InProcess_Cancelled')";
        }
        $sql = "SELECT DATE_FORMAT(service_center_closed_date, '%b') AS month,DATE_FORMAT(service_center_closed_date, '%Y') AS year, COUNT(*) as completed_booking
                FROM booking_details
                WHERE $where
                AND service_center_closed_date >= (NOW() - INTERVAL 13 MONTH)
                GROUP BY DATE_FORMAT(service_center_closed_date, '%m-%Y') 
                ORDER BY YEAR(service_center_closed_date),MONTH(service_center_closed_date)";
        $query = $this->db->query($sql);
        $completed_booking = $query->result_array();
        return $completed_booking;
    }
    
    function get_booking_based_on_partner_source_data($startDate, $endDate,$partner_id){
        $sql = "SELECT count(booking_id) as count ,  CASE WHEN partner_source != '' THEN (partner_source) ELSE ('Partner Source Not Assigned') END as partner_source "
                . " FROM booking_details "
                . "where partner_id = '$partner_id' "
                . "AND create_date >='$startDate' AND create_date<='$endDate'"
                . "GROUP BY partner_source";
        $query = $this->db->query($sql);
        $data = $query->result_array();
        return $data;
    }
    
    
    /**
     * @desc: This function is used to get partner completed and cancelled booking data 
     * @param string
     * @return array
     */
    function get_partners_booking_data($startDate, $endDate){
        $sql = "SELECT 
                    SUM(IF(current_status ='Completed' && closed_date >= '$startDate' && closed_date <= '$endDate' , 1, 0)) AS Completed,
                    SUM(IF(current_status ='Cancelled' && closed_date >= '$startDate' && closed_date <= '$endDate' , 1, 0)) AS Cancelled,
                public_name,booking_details.partner_id
                FROM booking_details 
                JOIN partners ON booking_details.partner_id = partners.id
                WHERE partners.is_active = '1'
                GROUP BY booking_details.partner_id";
        $query = $this->db->query($sql);
        $data = $query->result_array();
        return $data;
    }
    
    /**
     * @desc: This function is used to get partner completed booking unit data
     * based on month
     * @param string
     * @return array
     */
    function get_bookings_unit_data_by_month($partner_id = ""){
        
        if(!empty($partner_id)){
            $where = "booking_status = 'Completed' AND partner_id = '$partner_id'";
        }else{
            $where = "booking_status = 'Completed'";
        }
        $sql = "SELECT DATE_FORMAT(ud_closed_date, '%b') AS month,DATE_FORMAT(ud_closed_date, '%Y') AS year, COUNT(*) as completed_booking
                FROM booking_unit_details
                WHERE $where
                AND ud_closed_date >= (NOW() - INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(ud_closed_date, '%m-%Y') 
                ORDER BY YEAR(ud_closed_date),MONTH(ud_closed_date)";
        $query = $this->db->query($sql);
        $completed_booking = $query->result_array();
        return $completed_booking;
    }
/*
 * This function get data from missing pincode table on the basis of rm id if rm id is null then it will return data group by on rm
 */    
     function get_pincode_data_for_not_found_sf($rm_id){
        $this->db->_reserved_identifiers = array('*','CASE');         
        if($rm_id){
         $where="where agent_state_mapping.agent_id= $rm_id and sf.active_flag=1 and sf.is_pincode_valid=1   and agent_state_mapping.id in(select max(id) from agent_state_mapping group by agent_id,state_code)";
        }
        else{
          $where="where agent_state_mapping.agent_id IS NULL and sf.active_flag=1 and sf.is_pincode_valid=1   and agent_state_mapping.id in(select max(id) from agent_state_mapping group by agent_id,state_code)";  
        }
       $sql='SELECT sf.pincode,sf.city,state_code.state,services.services,emp.full_name as full_name '
                .'FROM sf_not_exist_booking_details sf LEFT JOIN services ON sf.service_id=services.id LEFT JOIN state_code ON sf.state=state_code.id '
                .'LEFT JOIN partners ON partners.id = sf.partner_id INNER JOIN agent_state_mapping ON state_code.state_code=agent_state_mapping.state_code '
               . 'JOIN employee emp ON emp.id = agent_state_mapping.agent_id '
                .'LEFT JOIN '
                 .'employee ON employee.id = agent_state_mapping.agent_id '.$where;
       $query = $this->db->query($sql);
       return $query->result_array();
 }
    
     function update_query_report($where, $data){
         $this->db->where($where);
         $this->db->update("query_report", $data);
     }
     /*
      * This function is used to get Escalation On the basis of vendor,RM,Dates
      */
        function get_sf_escalation_by_rm_by_sf_by_date($startDate=NULL,$endDate=NULL,$sf_id=NULL,$rm_id=NULL,$groupBy,$partnerID=NULL){
        $escalation_select_sub = "count(vendor_escalation_log.booking_id) AS total_escalation ";
        if($partnerID){
            $escalation_where['booking_details.partner_id'] = $partnerID;
            $booking_where['booking_details.partner_id'] = $partnerID;
            $escalation_select_sub = "count(DISTINCT vendor_escalation_log.booking_id) AS total_escalation ";
        }
         //Create Blank Where Array For escalation and Booking
    $booking_orderBy["YEAR(STR_TO_DATE(booking_details.booking_date,'%Y-%m-%d'))"] = "ASC";
    $booking_orderBy["month(STR_TO_DATE(booking_details.booking_date,'%Y-%m-%d'))"] = "ASC";
    $escalation_orderBy["month(vendor_escalation_log.create_date)"] = "ASC";
    $escalation_orderBy["YEAR(vendor_escalation_log.create_date)"] = "ASC";
    $escalation_where=array();
    $booking_where=array();
    //Create Join  Array For escalation and Booking (JOIN With employee Relation to get RM)
    $escalation_join = array("service_centres"=>"vendor_escalation_log.vendor_id = service_centres.id",
        "booking_details"=>"booking_details.booking_id = vendor_escalation_log.booking_id","employee"=>"employee.id = service_centres.rm_id",
        "rm_zone_mapping"=>"service_centres.rm_id = rm_zone_mapping.rm_id",
        "zones"=>"rm_zone_mapping.zone_id = zones.id");
    $escalation_joinType = array("rm_zone_mapping" => 'left', "zones" => 'left');
    $booking_join = array("service_centres"=>"booking_details.assigned_vendor_id = service_centres.id","employee"=>"employee.id = service_centres.rm_id",
        "rm_zone_mapping"=>"service_centres.rm_id = rm_zone_mapping.rm_id",
        "zones"=>"rm_zone_mapping.zone_id = zones.id");
    $booking_joinType = array("rm_zone_mapping" => 'left', "zones" => 'left');
    //Create Select String for booking and escalation
    $booking_select = 'count(booking_id) AS total_booking,assigned_vendor_id,STR_TO_DATE(booking_details.booking_date,"%Y-%m-%d") as booking_date,service_centres.rm_id as rm_id,'
            . 'zones.zone as region,employee.full_name as rm_name,MONTH(STR_TO_DATE(booking_details.booking_date,"%Y-%m-%d")) as booking_month,'
            . 'YEAR(STR_TO_DATE(booking_details.booking_date,"%Y-%m-%d")) as booking_year';
    $escalation_select = $escalation_select_sub.',vendor_escalation_log.vendor_id,vendor_escalation_log.create_date as escalation_date,'
            . 'service_centres.rm_id as rm_id,employee.full_name as rm_name,zones.zone as region,MONTH(vendor_escalation_log.create_date) as escalation_month,YEAR(vendor_escalation_log.create_date) as escalation_year';
   // If rm id is set add rm id in where array for booking and escalation
    if($rm_id){
       $escalation_where['service_centres.rm_id'] = $rm_id;
       $booking_where['service_centres.rm_id'] = $rm_id;
    }
     // If sf id is set add sf id in where array for booking and escalation
    if($sf_id){
       $escalation_where['vendor_escalation_log.vendor_id'] = $sf_id;
       $booking_where['booking_details.assigned_vendor_id'] = $sf_id;
    }
    if($partnerID){
       $escalation_where['booking_details.partner_id'] = $partnerID;
       $booking_where['booking_details.partner_id'] = $partnerID;
    }
    // If dates are Set Then add date in where array for booking and escalation
    if(!($startDate) && !($endDate)){
            $booking_where["month(STR_TO_DATE(booking_details.booking_date,'%Y-%m-%d')) = month(now()) AND year(STR_TO_DATE(booking_details.booking_date,'%Y-%m-%d')) = year(now())"] = NULL;
            $escalation_where["month(vendor_escalation_log.create_date) = month(now()) AND year(vendor_escalation_log.create_date) = year(now())"] =NULL;
       }
       //If dates are not set set them for current Month
       else{
            $booking_where["STR_TO_DATE(booking_details.booking_date,'%Y-%m-%d') >='".$startDate."' AND STR_TO_DATE(booking_details.booking_date,'%Y-%m-%d') <'".$endDate."'"] = NULL;
            $escalation_where["date(vendor_escalation_log.create_date) >= '".$startDate."' AND date(vendor_escalation_log.create_date) < '".$endDate."'"] =  NULL;
       }
       //Get Booking data for above define where condition,select,join and requested group by
    $data['booking'] = $this->reusable_model->get_search_result_data('booking_details',$booking_select,$booking_where,$booking_join,NULL,$booking_orderBy,NULL,$booking_joinType,$groupBy['booking']);
       //Get Escalation data for above define where condition,select,join and requested group by
    $data['escalation'] = $this->reusable_model->get_search_result_data('vendor_escalation_log',$escalation_select,$escalation_where,$escalation_join,NULL,$escalation_orderBy,NULL,$escalation_joinType,$groupBy['escalation']);
    return $data;
     }
     function get_missing_pincode_query_count_by_admin(){
            $sql='SELECT COUNT(sf.pincode) as pincodeCount,employee.id,(CASE  WHEN employee.full_name IS NULL THEN "NOT FOUND RM" ELSE employee.full_name END)'
                  .'AS full_name FROM sf_not_exist_booking_details sf LEFT JOIN state_code ON sf.state=state_code.id INNER JOIN agent_state_mapping '
                    . 'ON (state_code.state_code = agent_state_mapping.state_code) LEFT JOIN '
                  .'employee ON agent_state_mapping.agent_id=employee.id where sf.active_flag=1 and sf.is_pincode_valid=1  and agent_state_mapping.id in(select max(id) from agent_state_mapping group by agent_id,state_code) group by full_name order by count(sf.pincode) DESC';
            $query = $this->db->query($sql);
            return $query->result_array();          
     }
     
    /**
     * @desc: This function is used to get inventory dashboard title data
     * @param void
     * @return array
     */
    function get_spare_parts_count_group_by_status($partner_id = ''){
        $this->db->select('count(spare_parts_details.id) as count,status,group_concat(spare_parts_details.booking_id) as booking_id');
        $this->db->from('spare_parts_details');
        $this->db->join('booking_details', 'booking_details.booking_id = spare_parts_details.booking_id');
        $this->db->group_by('status');
        
        if(!empty($partner_id)){
            $this->db->where('booking_details.partner_id',$partner_id);
        }
        $query = $this->db->get();
        return $query->result_array();
    }
    
     /**
     * @desc: This function is used to get spare part details by sf wise (oot 7 days)
     * @param void
     * @return array
     */
    function get_spare_details_count_group_by_sf($is_show_all,$partner_id){
        
        $select = "SELECT "
                . "count(spare_parts_details.booking_id) as oot_defective_parts_count,"
                . "spare_parts_details.service_center_id,"
                . "service_centres.name,"
                . "GROUP_CONCAT(DISTINCT spare_parts_details.booking_id) as booking_id";
        
        $where = "spare_parts_details.defective_part_required = 1 "
                . "AND DATEDIFF(CURRENT_DATE,booking_details.service_center_closed_date) > ".DEFECTIVE_PART_PENDING_OOT_DAYS. " AND "
                . "status IN ('".DEFECTIVE_PARTS_PENDING."', '".DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE."', '".OK_PART_TO_BE_SHIPPED."', '".DAMAGE_PART_TO_BE_SHIPPED."') ";
        
        if(!empty($partner_id)){
            $where .= " AND spare_parts_details.partner_id = $partner_id";
        }
        
        $sql = $select . " FROM spare_parts_details"
                . " JOIN service_centres ON spare_parts_details.service_center_id = service_centres.id "
                . " JOIN booking_details ON spare_parts_details.booking_id = booking_details.booking_id "
                . " WHERE $where "
                . " GROUP BY spare_parts_details.service_center_id "
                . " HAVING oot_defective_parts_count > 0 "
                . " ORDER BY oot_defective_parts_count DESC";
        
        if(empty($is_show_all)){
            $sql .= " LIMIT 0,5";
        }
        
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    /**
     * @desc: This function is used to get spare part details by partner wise
     * @param void
     * @return array
     */
    function get_oot_spare_parts_count_by_partner(){
        $sql = "SELECT COUNT(spare_parts_details.booking_id) AS 'spare_count', "
                . "IFNULL(ROUND(SUM(spare_parts_details.challan_approx_value)),0) as 'spare_amount',"
                . " booking_details.partner_id,partners.public_name "
                . " FROM spare_parts_details "
                . " JOIN booking_details ON booking_details.booking_id = spare_parts_details.booking_id"
                . " JOIN partners ON booking_details.partner_id = partners.id "
                . "WHERE booking_details.service_center_closed_date IS NOT NULL "
                . "AND DATEDIFF(CURRENT_DATE,booking_details.service_center_closed_date) > '".SF_SPARE_OOT_DAYS."'"
                . "AND spare_parts_details.defective_part_required = 1 "
                . "AND spare_parts_details.status IN ('".DEFECTIVE_PARTS_PENDING."', '".DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE."', '".OK_PART_TO_BE_SHIPPED."', '".DAMAGE_PART_TO_BE_SHIPPED."') "
                . "GROUP BY booking_details.partner_id "
                . " ORDER BY spare_count DESC";
        $query = $this->db->query($sql);
        
        return $query->result_array();
    }
    
    /**
     * @desc: This function is used to get spare part details by status wise
     * @param void
     * @return array
     */
    function get_inventory_header_count_data(){
        $sql = "SELECT spare_parts_details.status,COUNT(spare_parts_details.id) AS spare_count,"
                . "SUM(spare_parts_details.challan_approx_value) as spare_amount "
                . "FROM spare_parts_details "
                . "GROUP BY spare_parts_details.status";
        $query1 = $this->db->query($sql);
        return $query1->result_array();
    }
    
    /**
     * @desc: This function is used to get partner specific spare part snapshot
     * @param void
     * @return array
     */
    function get_partner_spare_snapshot($partner_id,$is_sf_data = 1){
        $data['total_spare_count'] = $this->get_partner_total_spare_details($partner_id);
        
        $data['oot_partner_spare_count'] = $this->get_partner_oot_spare_details_by_partner_id($partner_id);
        
        if($is_sf_data){
            $data['oot_sf_spare_count'] = $this->get_sf_oot_spare_details_by_partner_id($partner_id);
            $data['oot_sf_spare_count_by_partner_shipped_day'] = $this->get_sf_oot_spare_from_partner_shipped_details_by_partner_id($partner_id);
        }
        
        return $data;
    }
    
    function get_partner_total_spare_details($partner_id,$select = NULL) {
        if(!empty($select)){
            $select = $select;
        }else{
            $select = "SELECT count(spare_parts_details.id) as spare_count,"
                . "IFNULL(ROUND(SUM(spare_parts_details.challan_approx_value)),0) as spare_amount , 'Total' as spare_status";
        }

        $where = "spare_parts_details.status NOT IN ('" . _247AROUND_CANCELLED . "')"
                . " AND booking_details.current_status IN ('" . _247AROUND_PENDING . "','" . _247AROUND_RESCHEDULED . "') "
                . " AND spare_parts_details.parts_shipped IS NOT NULL "
                . " AND booking_details.partner_id = $partner_id";

        $sql = $select . " FROM spare_parts_details"
                . " JOIN booking_details ON spare_parts_details.booking_id = booking_details.booking_id"
                . " WHERE $where";

        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    /**
     * @desc: This function is used to get spare part details by partner_id
     * @param void
     * @return array
     */
    function get_partner_oot_spare_details_by_partner_id($partner_id,$select = NULL){
        if(!empty($select)){
            $select = $select;
        }else{
            $select = "SELECT count(spare_parts_details.id) as spare_count,"
                . "IFNULL(ROUND(SUM(spare_parts_details.challan_approx_value)),0) as spare_amount, 'Partner Out of Tat' as spare_status";
        }

        $where = "spare_parts_details.status NOT IN ('" . _247AROUND_CANCELLED . "')"
                . " AND booking_details.current_status IN ('" . _247AROUND_PENDING . "','" . _247AROUND_RESCHEDULED . "')"
                . " AND DATEDIFF(CURRENT_DATE,spare_parts_details.shipped_date) > '".PARTNER_SPARE_OOT_DAYS."'"
                . " AND spare_parts_details.parts_shipped IS NOT NULL "
                . " AND booking_details.partner_id = $partner_id";

        $sql = $select . " FROM spare_parts_details"
                . " JOIN booking_details ON spare_parts_details.booking_id = booking_details.booking_id"
                . " WHERE $where";

        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    /**
     * @desc: This function is used to get sf oot spare part details by partner_id
     * @param void
     * @return array
     */
    function get_sf_oot_spare_details_by_partner_id($partner_id,$select = NULL){
        
        if(!empty($select)){
            $select = $select;
        }else{
            $select = " SELECT count(spare_parts_details.id) as spare_count,"
                . "IFNULL(ROUND(SUM(spare_parts_details.challan_approx_value)),0) as spare_amount, 'SF Out of Tat' as spare_status";
        }
        
        $where = "spare_parts_details.defective_part_required = 1 "
                . "AND DATEDIFF(CURRENT_DATE,booking_details.service_center_closed_date) > ".SF_SPARE_OOT_DAYS. " "
                . " AND spare_parts_details.parts_shipped IS NOT NULL "
                . "AND spare_parts_details.status IN ('".DEFECTIVE_PARTS_PENDING."', '".DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE."', '".OK_PART_TO_BE_SHIPPED."', '".DAMAGE_PART_TO_BE_SHIPPED."') ";
        
        if(!empty($partner_id)){
            $where .= " AND booking_details.partner_id = $partner_id";
        }
        
        $sql = $select . " FROM spare_parts_details"
                . " JOIN booking_details ON spare_parts_details.booking_id = booking_details.booking_id "
                . " WHERE $where ";
        
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    /**
     * @desc: This function is used to get count of those sf who don't have bracket inventory
     * @param void
     * @return array
     */
    function get_sf_has_zero_stock_data(){
        $sql = "SELECT SUM(inventory_stocks.stock) AS stock,name,service_centres.id as sf_id
                FROM inventory_stocks
                JOIN service_centres ON inventory_stocks.entity_id = service_centres.id
                GROUP BY inventory_stocks.entity_id
                HAVING stock = 0";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    /**
     * @desc: This function is used to get sf oot spare part details by partner_id
     * @param void
     * @return array
     */
    function get_sf_oot_spare_from_partner_shipped_details_by_partner_id($partner_id,$select = NULL){
        
        if(!empty($select)){
            $select = $select;
        }else{
            $select = " SELECT count(spare_parts_details.id) as spare_count,"
                . "IFNULL(ROUND(SUM(spare_parts_details.challan_approx_value)),0) as spare_amount, 'SF Out of Tat By Partner Shipped Date' as spare_status";
        }
        
        $where = "spare_parts_details.defective_part_required = 1 "
                . "AND DATEDIFF(CURRENT_DATE,spare_parts_details.shipped_date) > ".PARTNER_SPARE_OOT_DAYS. " "
                . " AND spare_parts_details.parts_shipped IS NOT NULL "
                . "AND spare_parts_details.status IN ('".DEFECTIVE_PARTS_PENDING."', '".DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE."', '".OK_PART_TO_BE_SHIPPED."', '".DAMAGE_PART_TO_BE_SHIPPED."') ";
        
        if(!empty($partner_id)){
            $where .= " AND booking_details.partner_id = $partner_id";
        }
        
        $sql = $select . " FROM spare_parts_details"
                . " JOIN booking_details ON spare_parts_details.booking_id = booking_details.booking_id "
                . " WHERE $where ";
        
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    function get_missing_pincode_by_rm_id($rm_id=NULL)
    {
        if($rm_id){
         $where='where agent_state_mapping.agent_id= '.$rm_id.' and sf.active_flag=1 and sf.is_pincode_valid=1';
        }
        else {
          $where='where agent_state_mapping.agent_id IS NULL and sf.active_flag=1 and sf.is_pincode_valid=1';  
        }
        
       $sql='SELECT sf.pincode,COUNT(sf.pincode) as pincodeCount,state_code.state,sf.city,sf.service_id,services.services'
                .' FROM sf_not_exist_booking_details sf LEFT JOIN services on sf.service_id=services.id LEFT JOIN state_code on sf.state=state_code.id'
                .' INNER JOIN agent_state_mapping ON (state_code.state_code = agent_state_mapping.state_code) LEFT JOIN '
                 .'employee ON agent_state_mapping.agent_id=employee.id '. $where .'  and agent_state_mapping.id in(select max(id) from agent_state_mapping group by agent_id,state_code)  group by sf.pincode,sf.service_id order by COUNT(sf.pincode) DESC';
       $query = $this->db->query($sql);
       return $query->result_array();
     }
    
    function get_missing_pincode_data_group_by($select,$agentID=NULL,$groupby)
    {
        if($agentID)
        {
         $where='where agent_state_mapping.agent_id= '. $agentID.' and sf.active_flag=1 and sf.is_pincode_valid=1 and agent_state_mapping.id in(select max(id) from agent_state_mapping group by agent_id,state_code)';
        }
        else
        {
          $where='where agent_state_mapping.agent_id IS NULL and sf.active_flag=1 and sf.is_pincode_valid=1 and agent_state_mapping.id in(select max(id) from agent_state_mapping group by agent_id,state_code)';  
        }
       
        $sql='SELECT ' .$select
                .'  FROM sf_not_exist_booking_details sf LEFT JOIN services on sf.service_id=services.id'
                .' LEFT JOIN state_code ON sf.state=state_code.state_code'
                .' INNER JOIN agent_state_mapping ON (state_code.state_code = agent_state_mapping.state_code) LEFT JOIN '
                 .' employee ON agent_state_mapping.agent_id=employee.id '.  $where .' '.$groupby.' order by COUNT(sf.pincode) DESC';
       $query = $this->db->query($sql);
        return $query->result_array();
    }
    function get_missing_pincode_data_group_by_partner($select,$agentID=NULL,$groupby)
    {
        if($agentID)
        {
         $where="where agent_state_mapping.agent_id= ". $agentID." and sf.active_flag=1 and sf.is_pincode_valid=1  and agent_state_mapping.id in(select max(id) from agent_state_mapping group by agent_id,state_code) ";
        }
        else
        {
          $where="where agent_state_mapping.agent_id IS NULL and sf.active_flag=1 and sf.is_pincode_valid=1  and agent_state_mapping.id in(select max(id) from agent_state_mapping group by agent_id,state_code) ";  
        }
        
        $sql='SELECT '.$select
                .' FROM sf_not_exist_booking_details sf LEFT JOIN partners on sf.partner_id=partners.id'
                .' LEFT JOIN state_code ON sf.state=state_code.state_code'
                .' INNER JOIN agent_state_mapping ON (state_code.state_code = agent_state_mapping.state_code) LEFT JOIN '
                 .' employee ON agent_state_mapping.agent_id=employee.id '.$where.' ' .$groupby.'  order by COUNT(sf.pincode) DESC';
       $query = $this->db->query($sql);
       return $query->result_array(); 
    }
    
     /*
     * @desc - This function is used to isert dashboard notifications in batch
     * @param - $data
     * @return - boolean
     */
    function insert_dashboard_notification($data){
        return $this->db->insert_batch("dashboard_notifications", $data);
    }
    
     /*
     * @desc - This function is used to update dashboard notifications
     * @param - $data, $where
     * @return - void
     */
    function update_dashboard_notification($data, $where){
        $this->db->where($where);
        $this->db->update("dashboard_notifications", $data);
    }
    
     /*
     * @desc - This function is used to get dashboard notifications
     * @param - $select, $where, $orderBYArray, $limit
     * @return - array
     */
    function get_dashboard_notification($select="*", $where=array(), $orderBYArray=array(), $limit=""){
        $this->db->select($select);
        
        if(!empty($where)){
            $this->db->where($where);
        }
       
        if(!empty($orderBYArray)){
            foreach ($orderBYArray as $fieldName=>$sortingOrder){
                $this->db->order_by($fieldName, $sortingOrder);
            }
        }
        
        if($limit){
           $this->db->limit($limit);
        }
        
        $this->db->from('dashboard_notifications');
        $this->db->join('dashboard_notification_type', 'dashboard_notification_type.id = dashboard_notifications.notification_type');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function get_dashboard_notification_type($select="*", $where=array()){
        $this->db->select($select);
        
        if(!empty($where)){
            $this->db->where($where);
        }
        
        $this->db->from('dashboard_notification_type');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function insert_dashboard_notification_any($details) {
        $this->db->insert('dashboard_notifications', $details);
        return $this->db->insert_id();
    }
    /*
     * @desc - This function is used to get logged in users
     * @param - void
     * @return - array
     */
    function get_loggedin_users(){
        $query = "SELECT * FROM `login_logout_details` where date(created_on)=curdate() group by ip, agent_id order by created_on desc";
        $result = $this->db->query($query);
        return $result->result_array();
    }




        /**
     * @desc this function is used to count report rows
     * @param string $table_name
     * @param array $where
     * @return Array
     */
    function get_spare_tat_report_count_total($table_name, $where = array(), $limit = 5,$post=array()){
        $this->db->select('*');
        if(!empty($where)){
            $this->db->where($where);
        }        
        $query = $this->db->get($table_name);
        return $query->result_array();
    }



        /**
     * @desc this function is used to count filtered rows count 
     * @param string $table_name
     * @param array $where
     * @return Array
     */
    function get_spare_tat_report_count_filter($table_name, $where = array(), $limit = 5,$post=array()){
        $this->db->select('*');
        if(!empty($where)){
            $this->db->where($where);
        }


        if (!empty($post['search_value'])) {
            $like = "";
            foreach ($post['column_search'] as $key => $item) { // loop column 
                // if datatable send POST for search
                if ($key === 0) { // first loop
                    $like .= "( " . $item . " LIKE '%" . $post['search_value'] . "%' ";
                } else {
                    $like .= " OR " . $item . " LIKE '%" . $post['search_value'] . "%' ";
                }
            }
            $like .= ") ";

            $this->db->where($like, null, false);
        }

    
        $query = $this->db->get($table_name);
       // print_r($this->db->last_query());  exit;
        return $query->result_array();
    }




    /**
     * @desc this function is used to fetch view data from db
     * @param string $table_name
     * @param array $where
     * @param int $limit
     * @return Array
     */
    function get_spare_tat_report($table_name, $where = array(), $limit = 5,$post=array()){
        $this->db->select('*');
        if(!empty($where)){
            $this->db->where($where);
        }


        if (!empty($post['search_value'])) {
            $like = "";
            foreach ($post['column_search'] as $key => $item) { // loop column 
                // if datatable send POST for search
                if ($key === 0) { // first loop
                    $like .= "( " . $item . " LIKE '%" . $post['search_value'] . "%' ";
                } else {
                    $like .= " OR " . $item . " LIKE '%" . $post['search_value'] . "%' ";
                }
            }
            $like .= ") ";

            $this->db->where($like, null, false);
        }

        if (isset($post['length']) && $post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }else if(isset($post['length']) && $post['length'] == -1){
            
        }else{
            $this->db->limit($limit, 0); 
        }
        
        $query = $this->db->get($table_name);
       // print_r($this->db->last_query());  exit;
        return $query->result_array();
    }
    /**
     * @desc This funnction is used to get count of click per hour for account manager
     * @param Date $date
     * @return Array
     */
    function get_agent_action_per_hour_count($statDate, $endDate, $group =''){
        $sql = "SELECT full_name as name, agent_id, concat(extract( MONTH FROM agent_action_log.create_date ), '-', extract( DAY FROM agent_action_log.create_date ), '-', extract( HOUR FROM agent_action_log.create_date )) as combination,"
                . "extract( HOUR FROM agent_action_log.create_date ) AS theHour, "
                . " extract( Day FROM agent_action_log.create_date ) AS theDays, extract( MONTH FROM agent_action_log.create_date ) AS theMonth, "
                . " count( * ) AS data FROM agent_action_log, employee "
                . "WHERE agent_action_log.create_date >= '".$statDate."' AND agent_action_log.create_date < '".date('Y-m-d', strtotime($endDate. '+1 days'))."' "
                . "AND agent_id != 1 ";
        if (empty($group)) {
            $sql .= " AND employee.id = agent_id  AND employee.groups = 'accountmanager' ";
        } else {
            $sql .= " AND employee.id = agent_id  AND employee.groups = '" . $group . "' ";
        }
        $sql  .= " GROUP BY agent_id, extract( MONTH FROM agent_action_log.create_date ) , "
                . " extract( Day FROM agent_action_log.create_date ), "
                . " extract( HOUR FROM agent_action_log.create_date ),  "
                . "agent_id order by agent_id, extract( MONTH FROM agent_action_log.create_date ),"
                . "extract( Day FROM agent_action_log.create_date ), "
                . "extract( HOUR FROM agent_action_log.create_date ) ";
        
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    /**
     * @Desc this function is used to return total click on crm by agent id
     * @param Array $where
     * @return Array
     */
    function get_agent_total_per_score($where){
        $this->db->select('agent_id, full_name as name, count(agent_action_log.id) as data');
        $this->db->where($where);
        $this->db->from('agent_action_log');
        $this->db->order_by('data', 'desc');
        $this->db->join('employee', 'employee.id = agent_id ');
        $this->db->group_by('agent_id');
        $query = $this->db->get();
        return $query->result_array();
        
    }
    
    /**
     * @Desc this function is used to return total cancelled booking by cancellation reason wise
     * @return Array
     */
    function get_booking_cancellation_reasons($startDate, $endDate) {
        $this->db->_protect_identifiers = FALSE; 
        $this->db->select('IFNULL(booking_cancellation_reasons.reason,"Others") as cancellation_reason,count(*) as count');
        $this->db->where("(current_status = 'Cancelled' OR internal_status = 'InProcess_Cancelled') && service_center_closed_date >= '$startDate' && service_center_closed_date <= '$endDate'");
        $this->db->from('booking_details');
        $this->db->join('booking_cancellation_reasons', 'booking_details.cancellation_reason = booking_cancellation_reasons.id',' LEFT');
        $this->db->group_by('cancellation_reason');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * @desc : Method is used to insert data in booking_unit_detail_invoice_process
     * @author : Ankit Rajvanshi
     * @param type $data
     * @return boolean
     */
    function insert_into_booking_unit_detail_invoice_process($data) {
        $this->db->insert_batch('booking_unit_details_invoice_process', $data);
        if($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }

    }
    
    /**
     * @desc : Method is used to get data of bookings to be invoiced
     * @author : Ankit Rajvanshi
     * @param type $select
     * @param type $where
     * @return type
     */
    function get_unit_details_invoice_process_data($select, $where) {
        $this->db->select($select, false);
        $this->db->from('booking_details');
        $this->db->where($where);
      
        $this->db->join('booking_unit_details', 'booking_unit_details.booking_id = booking_details.booking_id');
        $this->db->join('booking_unit_details_invoice_process', 'booking_unit_details.id = booking_unit_details_invoice_process.booking_unit_details_id', 'left');

        $query = $this->db->get();
        return $query->result_array();
        echo $this->db->last_query();exit;
        
    }
    
    /**
     * @desc: This function is used to get excel data for Escalations done by Call Center / ASMs / Partners
     * @param $startDate, $endDate
     * @return array
     */
    function get_escalation_data($startDate, $endDate, $partner_id){
        $this->db->_protect_identifiers = FALSE;
        
        // set select statement
        $select =   "booking_details.booking_id as 'Booking Id',
                    services.services as 'Appliance',
                    booking_details.request_type as 'Booking Type',
                    booking_details.city as 'City',
                    booking_details.state as 'State',    
                    service_centres.company_name as 'SF Name',
                    employee_am.full_name as 'AM Name',
                    employee_asm.full_name as 'ASM Name',
                    employee_rm.full_name as 'RM Name',    
                    vendor_escalation_log.create_date as 'Escalation Date',
                    vendor_escalation_policy.entity as 'Escalation Entity',
                    vendor_escalation_policy.escalation_reason as 'Escalation Reason'";
        
        // set where condition
        $where =    "booking_details.partner_id =  '$partner_id'
                    AND date(vendor_escalation_log.create_date) >= '$startDate'
                    AND date(vendor_escalation_log.create_date) < '$endDate'";
        
        // Query here
        $this->db->select($select);
        $this->db->from('vendor_escalation_log');
        $this->db->join('service_centres', 'vendor_escalation_log.vendor_id = service_centres.id');
        $this->db->join('booking_details', 'booking_details.booking_id = vendor_escalation_log.booking_id');
        $this->db->join('employee as employee_rm', 'employee_rm.id = service_centres.rm_id');
        $this->db->join('services', 'booking_details.service_id = services.id');
        $this->db->join('vendor_escalation_policy', 'vendor_escalation_log.escalation_reason = vendor_escalation_policy.id');        
        $this->db->join('employee as employee_asm', 'employee_asm.id = service_centres.asm_id', 'left');
        $this->db->join('agent_filters', 'booking_details.partner_id = agent_filters.entity_id AND agent_filters.state = booking_details.state AND agent_filters.entity_type = "247around"', 'left');
        $this->db->join('employee as employee_am', 'employee_am.id = agent_filters.agent_id', 'left');
        $this->db->where($where);
        $this->db->order_by('vendor_escalation_log.create_date');
        $this->db->group_by('booking_details.booking_id');
        
        // return query object
        $query = $this->db->get();
        return $query;
    }
}
