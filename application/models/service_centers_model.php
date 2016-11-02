<?php

class Service_centers_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();

        $this->db = $this->load->database('default', TRUE, TRUE);
    }

    /**
     * @desc: check service center login and return pending booking
     * @param: Array(username, password)
     * @return : Array(Pending booking)  
     */
    function service_center_login($data) {
        $this->db->select('service_center_id');
        $this->db->where('user_name', $data['user_name']);
        $this->db->where('password', $data['password']);
        $this->db->where('active', 1);
        $query = $this->db->get('service_centers_login');
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0]['service_center_id'];
        } else {

            return false;
        }
    }

    /**
     * @desc: this is used to get pending booking and count pending booking for specific service center id
     * @param: end limit, start limit, service center id
     * @return: Pending booking
     */
    function pending_booking($service_center_id, $booking_id){
        $booking = "";
        $day = " ";
        if($booking_id !=""){
            $booking = " AND bd.booking_id = '".$booking_id."' ";
        } 
        for($i =1; $i < 3;$i++ ){
            if($booking_id !=""){
                if($i==2){
                //Future Booking
                    $day  = " AND (DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(bd.booking_date, '%d-%m-%Y')) <=- -1) ";
                    $booking = " ";
                }
                
            } else {
                if($i ==1){
                // Today Day
                $day  = " AND (DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(bd.booking_date, '%d-%m-%Y')) >= 0) ";
                } else if($i==2) {
                //Future Booking
                $day  = " AND (DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(bd.booking_date, '%d-%m-%Y')) < 0) ";
                }
                
            }
            
            
            $sql = " SELECT DISTINCT (sc.`booking_id`), `sc`.admin_remarks, "
                . " bd.booking_primary_contact_no, " 
                . " users.name as customername,  "
                . " bd.booking_date,"
                . " bd.booking_jobcard_filename,"
                . " bd.assigned_engineer_id,"
                . " bd.booking_timeslot, "
                . " bd.count_escalation, "
                . " bd.count_reschedule, "
                . " bd.booking_address, "
                . " bd.booking_pincode, "
                . " services, CASE
                    WHEN EXISTS (SELECT *
                                 FROM   penalty_on_booking as pb
                                 WHERE  pb.booking_id = bd.booking_id AND pb.service_center_id = bd.assigned_vendor_id) 
                                 THEN (SELECT SUM(penalty_amount) as penalty_amount FROM penalty_on_booking as pob
                                 WHERE pob.booking_id = bd.booking_id AND pob.service_center_id = bd.assigned_vendor_id)
                    ELSE '0'
                  END AS penalty, "
                . " DATEDIFF(CURRENT_TIMESTAMP, STR_TO_DATE(`bd`.booking_date,'%d-%m-%Y') ) as age_of_booking "
                . " FROM service_center_booking_action as sc, booking_details as bd, users, services, engineer_details "
                . " WHERE sc.service_center_id = $service_center_id "
                . " AND sc.current_status = 'Pending' "
                . " AND bd.assigned_vendor_id = sc.service_center_id "
                . " AND bd.booking_id =  sc.booking_id "
                . " AND bd.user_id = users.user_id "
                . " AND bd.service_id = services.id "
                . " AND (bd.current_status='Pending' OR bd.current_status='Rescheduled')"
                . "  ".$day . $booking
                . " ORDER BY STR_TO_DATE(`bd`.booking_date,'%d-%m-%Y') desc ";

            $query1 = $this->db->query($sql);
            $result[$i] = $query1->result();
           
        }
        
        return $result;
        
    }

    /**
     * @desc: this method return completed and cancelled booking according to request status
     * @param: End limit
     * @param: Start limit
     * @param: service center id
     * @param: Status+(Cancelled or Completed)
     */
    function getcompleted_or_cancelled_booking($limit, $start, $service_center_id, $status, $booking_id) {
        if ($limit != "count") {
            $this->db->limit($limit, $start);
        }

        $this->db->select('booking_details.booking_id, users.name as customername, booking_details.booking_primary_contact_no, '
                . 'services.services, booking_details.booking_date, booking_details.closed_date, booking_details.closing_remarks, '
                . 'booking_details.cancellation_reason, booking_details.booking_timeslot');
        $this->db->from('booking_details');
        $this->db->join('services', 'services.id = booking_details.service_id');
        $this->db->join('users', 'users.user_id = booking_details.user_id');
        $this->db->where('booking_details.current_status', $status);
        $this->db->where('assigned_vendor_id', $service_center_id);
        if($booking_id !=""){
            $this->db->where('booking_details.booking_id', $booking_id);
        }
        
        //Sort by Closure Date for both Cancelled and Completed bookings
        $this->db->order_by('closed_date', 'desc');
        
        $query = $this->db->get();

        $result = $query->result_array();
        if ($limit == "count") {

            return count($result);
        }
        return $result;
    }

    function date_compare_bookings($a, $b) {
        $t1 = strtotime($a->booking_date);
        $t2 = strtotime($b->booking_date);

        return $t2 - $t1;
    }

    /**
     *
     */
    function getcharges_filled_by_service_center($booking_id, $status) {

        $this->db->distinct();
        $this->db->select('booking_id, amount_paid, admin_remarks, service_center_remarks, cancellation_reason');
        if ($booking_id != "") {
            $this->db->where('booking_id', $booking_id);
        }

        //Status should NOT be Completed or Cancelled
        if ($status != ""){
            $this->db->where_not_in('current_status', $status);
        }

        $this->db->where_not_in('internal_status', "Reschedule");
        $query = $this->db->get('service_center_booking_action');
        $booking = $query->result_array();

        foreach ($booking as $key => $value) {
            // get data from booking unit details table on the basis of appliance id
            $this->db->select('unit_details_id, service_charge, additional_service_charge,  parts_cost, amount_paid, price_tags, appliance_category,appliance_capacity, service_center_booking_action.internal_status, service_center_booking_action.serial_number');
            $this->db->where('service_center_booking_action.booking_id', $value['booking_id']);
            $this->db->from('service_center_booking_action');
            $this->db->join('booking_unit_details', 'booking_unit_details.id = service_center_booking_action.unit_details_id');
            $query2 = $this->db->get();

            $result = $query2->result_array();
            $booking[$key]['unit_details'] = $result;
        }
        return $booking;
    }

    /**
     * @desc: this method update service center action table
     */
    function update_service_centers_action_table($booking_id, $data) {
        $this->db->where('booking_id', $booking_id);
        $this->db->update('service_center_booking_action', $data);
    }

    function delete_booking_id($booking_id) {
        $this->db->where('booking_id', $booking_id);
        $this->db->delete('service_center_booking_action');
        return TRUE;
    }

    function get_prices_filled_by_service_center($unit_id, $booking_id) {
        $this->db->select('*');
        $this->db->where('booking_id', $booking_id);
        $this->db->where('unit_details_id', $unit_id);
        $query = $this->db->get('service_center_booking_action');
        return $query->result_array();
    }
    
    /**
     * @desc: This is use to check, If where condition is satisfy then update 
     * other wise insert details in spare_parts_details table
     * @param Array $where
     * @param Array $data
     * @return boolean
     */
    function spare_parts_action($where, $data){
        $this->db->where($where); 
        $query = $this->db->get('spare_parts_details');
        if($query->num_rows >0){
           return $this->update_spare_parts($where, $data);
            
        } else {
            return $this->insert_data_into_spare_parts($data);
        }
    }
    /**
     * @desc: This is used to update spare parts table
     * @param Array $where
     * @param Array $data
     * @return boolean
     */
    function update_spare_parts($where, $data){
        $this->db->where($where); 
        $result = $this->db->update('spare_parts_details', $data);
        log_message('info', __FUNCTION__ . '=> Update Spare Parts: ' .$this->db->last_query());
        return $result;
    }
    /**
     * @desc: Insert booking details for spare parts
     * @param Array $data
     * @return boolean
     */
    function insert_data_into_spare_parts($data){
        $this->db->insert('spare_parts_details', $data);
        log_message('info', __FUNCTION__ . '=> Insert Spare Parts: ' .$this->db->last_query());
        return $this->db->insert_id();  
    }
    
    /**
     * @desc:This method ised to get all updated spare booking  by SF
     * @param String $sc_id
     * @return type Array
     */
    function get_updated_spare_parts_booking($sc_id){
        $sql = "SELECT sp.* "
                . " FROM spare_parts_details as sp, service_center_booking_action as sc "
                . " WHERE  sp.booking_id = sc.booking_id "
                . " AND (sp.status = '".SPARE_PARTS_REQUESTED."' OR sp.status = 'Shipped') AND (sc.current_status = 'InProcess' OR sc.current_status = 'Pending')"
                . " AND ( sc.internal_status = '".SPARE_PARTS_REQUIRED."' OR sc.internal_status = '".SPARE_PARTS_SHIPPED."') "
                . " AND sc.service_center_id = '$sc_id' ";
        $query = $this->db->query($sql);
         log_message('info', __FUNCTION__  .$this->db->last_query());
         //echo $this->db->last_query();
        return $query->result_array();
    }
    
    /**
     * @desc: This method is used to get Array whose booking is updated by SF.
     * Need to update to display in SF panel next Day
     * @return type Array
     */
    function get_updated_booking_to_convert_pending(){

        $sql = " SELECT * FROM service_center_booking_action "
                . " WHERE current_status ='InProcess' AND internal_status IN (".$this->stored_internal_status().") ";
        $query = $this->db->query($sql);
        log_message('info', __FUNCTION__  .$this->db->last_query());
         
        $result =  $query->result_array();
        foreach ($result as $value) {
            $this->db->where('id', $value['id']);
            $this->db->update("service_center_booking_action", array('current_status'=>'Pending'));
        }
    }
    
    /**
     * @desc: This is used to return those internal status whose booking will be display next day after updation
     * @return String
     */
    function stored_internal_status(){
        return "'Engineer on routes',"
             . "'Customer not reachable'";
    }
    
    function search_booking_history($where,$service_center_id) {
       
        $sql = "SELECT `booking_id`,`booking_date`,`booking_timeslot`, users.name, services.services, current_status, assigned_engineer_id "
                . " FROM `booking_details`,users, services "
                . " WHERE users.user_id = booking_details.user_id "
                . " AND services.id = booking_details.service_id "
                . " AND `assigned_vendor_id` = '$service_center_id' ". $where
                . " ORDER BY booking_details.`id` DESC ";
        $query = $this->db->query($sql);
        //echo $this->db->last_query();
        return $query->result_array();
    }

    

}
