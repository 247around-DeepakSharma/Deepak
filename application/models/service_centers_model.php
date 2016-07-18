<?php

class Service_centers_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
	  parent::__Construct();

	    $this->db_location = $this->load->database('default1', TRUE, TRUE);
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
      * @desc: this is used to get pending booking for specific service center id
      * @param: end limit, start limit, service center id
      * @return: Pending booking
      */
     function getPending_booking($limit="", $start="", $service_center_id ){
        $where = "";
        $where .= " AND assigned_vendor_id = '" . $service_center_id . "'";
        $where .= " AND DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= -1";


        $query = $this->db->query("Select services.services,
            users.name as customername, users.phone_number,
            booking_details.*, service_centres.name as service_centre_name,
            service_centres.primary_contact_name,service_centres.primary_contact_phone_1,
            DATEDIFF(CURRENT_TIMESTAMP , service_center_booking_action.create_date ) as age_of_booking, service_center_booking_action.admin_remarks
            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            JOIN `service_center_booking_action` ON `service_center_booking_action`.booking_id = `booking_details`.booking_id
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id` WHERE
        `booking_details`.booking_id NOT LIKE 'Q-%' $where AND  `service_center_booking_action`.current_status = 'Pending' AND
            (booking_details.current_status='Pending' OR booking_details.current_status='Rescheduled')"
        );

        
        if($limit =="count"){
            $temp = $query->result_array();
            return count($temp);

        } else {
            $temp = $query->result();
            usort($temp, array($this, 'date_compare_bookings'));
            return array_slice($temp, $start, $limit);
        }
        

     }

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
}