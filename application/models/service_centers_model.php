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
    function service_center_login($data){
       $this->db->select('service_center_id');
       $this->db->where('user_name',$data['user_name']);
       $this->db->where('password',$data['password']);
       $this->db->where('active',1);
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
     function getPending_booking($limit="", $start="", $service_center_id ){
       
        //$where .= " AND DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= -1";
        $this->db->distinct();
        if($limit !="count"){
            $this->db->limit($limit, $start);
        }
        
        $this->db->select('booking_id, DATEDIFF(CURRENT_TIMESTAMP , create_date ) as age_of_booking,admin_remarks');
        $this->db->where('service_center_id', $service_center_id);
        $this->db->where('current_status', "Pending");
        $this->db->order_by('create_date  ASC'); 
        $query = $this->db->get('service_center_booking_action');
        $pending_booking = $query->result_array();
        
        if($limit !="count"){
            $data = array();
            
            foreach ($pending_booking as $key => $value) {
                $sql = "Select  services.services,
                           users.name as customername, 
                           users.phone_number,
                           booking_details.booking_id,
                           booking_details.booking_date,
                           booking_details. booking_primary_contact_no,
                           booking_details.booking_jobcard_filename,
                           booking_details.booking_timeslot
                           From  booking_details 
                           JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
                           JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
                           WHERE booking_details.booking_id = '$value[booking_id]' AND (booking_details.current_status='Pending' OR booking_details.current_status='Rescheduled') ";

                $query1 = $this->db->query($sql);
                $result = $query1->result();
                if($query1->num_rows > 0){
                  $result[0]->age_of_booking = $value['age_of_booking'];
                  $result[0]->admin_remarks = $value['admin_remarks'];
                  array_push($data, $result[0]);
                }

               
            
            }
          
        }

        if($limit =="count"){
           
            return count($pending_booking);

        } else {
          
           
            return $data;
        }
        

     }
     /**
      * @desc: this method return completed and cancelled booking according to request status
      * @param: End limit
      * @param: Start limit
      * @param: service center id
      * @param: Status+(Cancelled or Completed)
      */
     function getcompleted_or_cancelled_booking($limit="", $start="", $service_center_id, $status){
        if($limit!="count"){
            $this->db->limit($limit, $start);
        }
        
        $this->db->select('booking_details.booking_id, users.name as customername, booking_details.booking_primary_contact_no, services.services, booking_details.booking_date, booking_details.closing_remarks, booking_details.booking_timeslot');
        $this->db->from('booking_details');
        $this->db->join('services','services.id = booking_details.service_id');
        $this->db->join('users','users.user_id = booking_details.user_id');
        $this->db->where('booking_details.current_status', $status);
        $this->db->where('assigned_vendor_id',$service_center_id);
        $query = $this->db->get();
        
        $result = $query->result_array();
        if($limit == "count"){
        
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
    function getcharges_filled_by_service_center($booking_id, $status){

        $this->db->distinct();
        $this->db->select('booking_id, amount_paid, admin_remarks, service_center_remarks, cancellation_reason');
        if ($booking_id != "") {
            $this->db->where('booking_id', $booking_id);
        }

         //Status should NOT be Completed or Cancelled
        if($status !="")
        $this->db->where_not_in('current_status', $status);

        $this->db->where_not_in('internal_status', "Reschedule");
        $query = $this->db->get('service_center_booking_action');
        $booking = $query->result_array();

         foreach ($booking as $key => $value) {
            // get data from booking unit details table on the basis of appliance id
            $this->db->select('unit_details_id, service_charge, additional_service_charge,  parts_cost, amount_paid, price_tags, appliance_category,appliance_capacity, service_center_booking_action.internal_status, service_center_booking_action.serial_number');
            $this->db->where('service_center_booking_action.booking_id', $value['booking_id']);
            $this->db->from('service_center_booking_action');
            $this->db->join('booking_unit_details','booking_unit_details.id = service_center_booking_action.unit_details_id');
            $query2 = $this->db->get();

            $result = $query2->result_array();
            $booking[$key]['unit_details'] = $result; 
        }
        return $booking;
    }
    /**
     * @desc: this method update service center action table
     */
    function update_service_centers_action_table($booking_id, $data){
      $this->db->where('booking_id', $booking_id);
      $this->db->update('service_center_booking_action', $data);

    }

    function delete_booking_id($booking_id){
      $this->db->where('booking_id', $booking_id);
      $this->db->delete('service_center_booking_action');
      return TRUE;
    }
}