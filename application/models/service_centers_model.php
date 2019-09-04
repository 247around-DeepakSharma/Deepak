<?php

class Service_centers_model extends CI_Model {
    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }

    /**
     * @desc: check service center login and return pending booking
     * @param: Array(username, password)
     * @return : Array(Pending booking)  
     */
    function service_center_login($data) {
        $this->db->select('*');
        $this->db->where('user_name', $data['user_name']);
        $this->db->where('password', $data['password']);
        $this->db->where('active', 1);
        $query = $this->db->get('service_centers_login');
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0];
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
            $booking = " AND bd.booking_id IN ('".$booking_id."') ";
        }
        $status = "";
        for($i =1; $i <= 4;$i++ ){
            if($booking_id !=""){
                if($i==2){
                //Future Booking
                    $day  = " AND (DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(bd.booking_date, '%d-%m-%Y')) <=- -1) ";
                    $booking = " ";
                    $status = " AND (bd.current_status='Pending' OR bd.current_status='Rescheduled') AND sc.current_status = 'Pending'";
                } else if($i == 3){
                    // Rescheduled Booking
                    $day  = " AND (DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(bd.booking_date, '%d-%m-%Y')) < -1) ";
                    $status = " AND (bd.current_status='Rescheduled' AND sc.current_status = 'Pending')  ";
                } 
                
            } else {
                if($i ==1){
                // Today Day
                $day  = " AND (DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(bd.booking_date, '%d-%m-%Y')) >= 0) ";
                $status = " AND (bd.current_status='Pending' OR bd.current_status='Rescheduled') AND sc.current_status = 'Pending'";
                
                } else if($i==2) {
                //Tomorrow Booking
                $day  = " AND (DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(bd.booking_date, '%d-%m-%Y')) = -1) ";
                $status = " AND (bd.current_status='Pending' OR bd.current_status='Rescheduled') AND sc.current_status = 'Pending'";
                } else if($i == 3){
                    // Rescheduled Booking
                    $day  = " AND (DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(bd.booking_date, '%d-%m-%Y')) < -1) ";
                    $status = " AND (bd.current_status='Rescheduled' AND sc.current_status = 'Pending')  ";
                } else if ($i== 4) {
                    $day = " ";
                    $status = " AND sc.current_status='InProcess' AND sc.internal_status IN (".$this->stored_internal_status().")";
                }
                
            }
            
            
            $sql = " SELECT DISTINCT (sc.`booking_id`), `sc`.admin_remarks, "
                . " bd.booking_primary_contact_no, "
                . " users.name as customername,  "
                . " bd.booking_date,"
                . " bd.partner_id,"
                . " bd.booking_jobcard_filename,"
                . " bd.assigned_engineer_id,"
                . " bd.booking_timeslot, "
                . " bd.current_status, "
                . " bd.amount_due, "
                . " bd.flat_upcountry, "
                . " bd.request_type, "
                . " bd.count_escalation, "
                . " bd.is_upcountry, "
                . " bd.count_reschedule, "
                . " bd.upcountry_paid_by_customer, "
                . " bd.is_penalty, "
                . " bd.booking_address, "
                . " bd.booking_pincode, "
                . " bd.create_date, "
                . " bd.order_id, "
                . " bd.service_center_closed_date, "
                . " bd.booking_address, "
                . " bd.booking_alternate_contact_no, "
                . " bd.request_type, "
                . " bd.internal_status, "
                . " bd.booking_remarks, bd.service_id,"
                . " services, booking_files.file_name as booking_files_purchase_invoice, "
                . " (SELECT GROUP_CONCAT(DISTINCT brand.appliance_brand) FROM booking_unit_details brand WHERE brand.booking_id = bd.booking_id GROUP BY brand.booking_id ) as appliance_brand,"
                . " (SELECT GROUP_CONCAT(model_number) FROM booking_unit_details brand WHERE booking_id = bd.booking_id) as model_numbers,"
                 . "CASE WHEN (SELECT Distinct 1 FROM booking_unit_details as bu1 WHERE bu1.booking_id = bd.booking_id "
                    . "AND price_tags = 'Wall Mount Stand' AND bu1.service_id = 46 ) THEN (1) ELSE 0 END as is_bracket, " 
                    
                 . " CASE WHEN (bd.is_upcountry = 1 AND upcountry_paid_by_customer =0 AND bd.sub_vendor_id IS NOT NULL)  "
                 . " THEN (SELECT  ( round((bd.upcountry_distance * bd.sf_upcountry_rate)/(count(b.id)),2)) "
                 . " FROM booking_details AS b WHERE b.booking_pincode = bd.booking_pincode "
                 . " AND b.booking_date = bd.booking_date AND is_upcountry =1 "
                 . " AND b.sub_vendor_id IS NOT NULL "
                 . " AND b.upcountry_paid_by_customer = 0 "
                 . " AND b.sf_upcountry_rate = bd.sf_upcountry_rate"
                 . " AND bd.current_status IN ('Pending','Rescheduled', 'Completed')  "
                 . " AND b.assigned_vendor_id = '$service_center_id' ) "
                 . " WHEN (bd.is_upcountry = 1 AND upcountry_paid_by_customer = 1 AND bd.sub_vendor_id IS NOT NULL ) "
                 . " THEN (bd.upcountry_distance * bd.sf_upcountry_rate) "
                 . " ELSE 0 END AS upcountry_price, "
                    
                . " (SELECT SUM(vendor_basic_charges + vendor_st_or_vat_basic_charges)
                        FROM booking_unit_details AS u
                        WHERE u.booking_id = bd.booking_id AND pay_to_sf = '1') AS earn_sc,
"
                . " DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(bd.initial_booking_date, '%d-%m-%Y')) as age_of_booking, "
                . " CASE WHEN (SELECT count(*) FROM spare_parts_details WHERE spare_parts_details.booking_id=bd.booking_id "
                . " AND bd.internal_status='Spare Parts Cancelled') THEN (SELECT GROUP_CONCAT(reason) FROM spare_parts_details "
                . " JOIN booking_cancellation_reasons ON booking_cancellation_reasons.id=spare_parts_details.spare_cancellation_reason "
                . " WHERE spare_parts_details.booking_id=bd.booking_id AND bd.internal_status='Spare Parts Cancelled') END as part_cancel_reason, "
                . " bd.partner_internal_status "   
                . " FROM service_center_booking_action as sc "
                . " JOIN booking_details as bd ON bd.booking_id =  sc.booking_id "
                . " JOIN users ON bd.user_id = users.user_id "
                . " JOIN services ON bd.service_id = services.id "
                . " JOIN service_centres AS s ON s.id = bd.assigned_vendor_id "
                . " LEFT JOIN booking_files ON booking_files.id = ( SELECT booking_files.id from booking_files WHERE booking_files.booking_id = bd.booking_id AND booking_files.file_description_id = '".BOOKING_PURCHASE_INVOICE_FILE_TYPE."' LIMIT 1 )"
                . " WHERE sc.service_center_id = '$service_center_id' "
                . " AND bd.assigned_vendor_id = '$service_center_id' "
                . $status
                . "  ".$day . $booking
                . " GROUP BY bd.booking_id ORDER BY count_escalation desc, STR_TO_DATE(`bd`.booking_date,'%d-%m-%Y') desc ";
             
            $query1 = $this->db->query($sql);
            //echo $this->db->last_query(); die();
            
            $result[$i] = $query1->result();
           
        }
        
        return $result;
        
    }




            function get_service_brands_for_partner($partner_id){
        $sql = "Select Distinct partner_appliance_details.brand, services.services,services.id  "
                . "From partner_appliance_details, services "
                . "where partner_appliance_details.service_id = services.id "
                . "AND partner_appliance_details.partner_id = '".$partner_id."'";
        $query = $this->db->query($sql);
        return $query->result_array();
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

        $this->db->select('booking_details.booking_id, booking_details.flat_upcountry, users.name as customername, booking_details.booking_primary_contact_no, '
                . 'services.services, booking_details.booking_date, booking_details.closed_date, booking_details.closing_remarks, '
                . 'booking_details.cancellation_reason, booking_details.booking_timeslot, is_upcountry, amount_due,booking_details.rating_stars, booking_details.request_type,'
                . '(CASE WHEN booking_details.assigned_engineer_id is not NULL AND booking_details.assigned_engineer_id = 24700001
                    THEN "Default Engineer" WHEN booking_details.assigned_engineer_id is not null THEN engineer_details.name ELSE "-" END) as Engineer');
        $this->db->from('booking_details');
        $this->db->join('services', 'services.id = booking_details.service_id');
        $this->db->join('users', 'users.user_id = booking_details.user_id');
        $this->db->join('engineer_details', 'booking_details.assigned_engineer_id = engineer_details.id', 'left');
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
    function get_admin_review_bookings($booking_id,$status,$whereIN,$is_partner,$offest,$perPage = -1,$where=array(),$userInfo=0,$orderBY = NULL,$select=NULL,$state=0,$join_arr=array(),$having_arr=array()){
        $limit = "";
        $where_in = "";
        $userSelect = $join = $groupBy = $having = "";
        $where_sc = "AND (partners.booking_review_for NOT LIKE '%".$status."%' OR partners.booking_review_for IS NULL OR booking_details.amount_due != 0)";
         if($is_partner){
            $where_sc = " AND (partners.booking_review_for IS NOT NULL AND booking_details.amount_due = 0)";
        }
        if($status == "Cancelled"){
            $where_sc = $where_sc." AND NOT EXISTS (SELECT 1 FROM service_center_booking_action sc_sub WHERE sc_sub.booking_id = sc.booking_id "
                    . "AND (sc_sub.internal_status ='Completed' OR sc_sub.internal_status ='Defective Part To Be Shipped By SF' OR sc_sub.internal_status ='Defective Part Received By Partner'"
                    . "OR sc_sub.internal_status ='Defective Part Shipped By SF') LIMIT 1) ";
        }
        else if($status == "Completed"){
            $where_sc = $where_sc." AND EXISTS (SELECT 1 FROM service_center_booking_action sc_sub WHERE sc_sub.booking_id = sc.booking_id AND sc_sub.internal_status ='Completed' LIMIT 1) ";
            
        } else if($status == "All"){
             $where_sc = $where_sc." AND EXISTS (SELECT 1 FROM service_center_booking_action sc_sub WHERE sc_sub.booking_id = sc.booking_id AND sc_sub.internal_status IN ('Completed', 'Cancelled') LIMIT 1) ";
        }
        
        if ($booking_id != "") {
            $where_sc =$where_sc. ' AND sc.booking_id LIKE "%'.trim($booking_id).'%"' ;
        }
        if($perPage !=-1){
            $limit = " LIMIT ".$offest.", ".$perPage;
        }
        if(!empty($whereIN)){
             foreach ($whereIN as $fieldName=>$conditionArray){
                     $where_in .= " AND ".$fieldName." IN ('".implode("','",$conditionArray)."')";
             }
         }
         if(!empty($where)){
             foreach ($where as $fieldName=>$conditionArray){
                     $where_sc =$where_sc. " AND ".$fieldName;
             }
         }
         if (!empty($join_arr)) {
            foreach($join_arr as $key=>$values){
                $join = $join." JOIN ".$key." ON ".$values;
            }
        }
        if(!empty($having_arr)){
            foreach ($having_arr as $fieldName=>$conditionArray){
                $having = $having. $fieldName." AND ";
            }
            $having = " having ".trim($having," AND ");
        }

         if($userInfo){
             $join = "JOIN users ON booking_details.user_id = users.user_id";
             $join = $join." JOIN services ON booking_details.service_id = services.id";
             $userSelect = ",users.name,services.services";
         }
        if($state == 1){
            $filter_value=1;
            $stateWhere['agent_filters.agent_id="'.$this->session->userdata('agent_id').'"'] = NULL;
            $stateWhere['agent_filters.is_active="' .$filter_value.'"']=NULL;
//            $this->db->join('agent_filters', 'agent_filters.state =  booking_details.state', "left");
//            $this->db->where($stateWhere, false);  
            if(!empty($stateWhere)){
             foreach ($stateWhere as $stateWhereKey=>$stateWhereKeyValue){
                     $where_sc =$where_sc. " AND ".$stateWhereKey;
             }
         }
         $join=$join." LEFT JOIN agent_filters ON agent_filters.state = booking_details.state";
        }
        
         if(!$select){
             $select = "sc.booking_id,sc.amount_paid,sc.admin_remarks,sc.cancellation_reason,sc.service_center_remarks,sc.sf_purchase_invoice,booking_details.request_type,booking_details.city,booking_details.state"
                . ",STR_TO_DATE(booking_details.initial_booking_date,'%d-%m-%Y') as booking_date,DATEDIFF(CURDATE(),STR_TO_DATE(booking_details.initial_booking_date,'%d-%m-%Y')) as age"
                . ",STR_TO_DATE(booking_details.create_date,'%Y-%m-%d') as booking_create_date,booking_details.booking_primary_contact_no,booking_details.is_upcountry,booking_details.partner_id,booking_details.amount_due $userSelect";
             $groupBy = "GROUP BY sc.booking_id";
         }
        $sql = "SELECT $select FROM service_center_booking_action sc "
                . " JOIN booking_details ON booking_details.booking_id = sc.booking_id  "
                . " JOIN partners ON booking_details.partner_id = partners.id "
                . "$join"
                . " WHERE sc.current_status = 'InProcess' "
                . $where_sc . $where_in
                . " AND sc.internal_status IN ('Cancelled','Completed') "
                . " AND booking_details.is_in_process = 0"
                . " $groupBy  $orderBY $having $limit";
        $query = $this->db->query($sql);
        $booking = $query->result_array();
         return $booking;
    }

    function getcharges_filled_by_service_center($booking_id,$status,$whereIN,$is_partner,$offest,$perPage,$having_arr=array()) {
        $booking = $this->get_admin_review_bookings($booking_id,$status,$whereIN,$is_partner,$offest,$perPage, [], 0, NULL, Null, 0, [],$having_arr);
        
        foreach ($booking as $key => $value) {
            // get data from booking unit details table on the basis of appliance id
            $this->db->select('booking_unit_details.partner_id,unit_details_id, service_charge, additional_service_charge,  parts_cost, upcountry_charges,'
                    . ' amount_paid, price_tags,appliance_brand, appliance_category,'
                    . ' appliance_capacity, service_center_booking_action.internal_status,'
                    . ' service_center_booking_action.serial_number, customer_net_payable, '
                    . ' service_center_booking_action.is_broken, '
                    . ' service_center_booking_action.serial_number_pic, '
                    . ' service_center_booking_action.mismatch_pincode, '
                    . ' service_center_booking_action.is_sn_correct, '
                    . ' service_center_booking_action.sf_purchase_date, '
                    . ' service_center_booking_action.sf_purchase_invoice, '
                    . ' service_center_booking_action.model_number');
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
       if((!empty($booking_id)) && ($booking_id != '0') && (!empty($data))){
        $this->db->where('booking_id', $booking_id);
        $result = $this->db->update('service_center_booking_action', $data);
        log_message('info', __FUNCTION__ . '=> Update sc table: ' .$this->db->last_query());
        return $result;
        } else {
            return FALSE;
        }
    }

    function delete_booking_id($booking_id) {
        if(!empty($booking_id) || $booking_id != '0'){
            $this->db->where('booking_id', $booking_id);
            $this->db->delete('service_center_booking_action');
        }
        log_message('info', __FUNCTION__ . '=> Delete booking id in sc table: ' .$this->db->last_query());
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


    function update_spare_parts($where, $data) {
        $this->db->where($where);
        $this->db->update('spare_parts_details', $data);
        log_message('info', __FUNCTION__ . '=> Update Spare Parts: ' . $this->db->last_query());

        if ($this->db->affected_rows() > 0) {
            $result = true;
        } else {
            $result = false;
        }
        
        return $result;
    }

    /**
     * @desc: This is used to update micro warehouse state mapping table
     * @param Array $where
     * @param Array $data
     * @return boolean
     */
    function update_micro_warehouse($where, $data) {
        $this->db->where($where);
        $this->db->update('micro_warehouse_state_mapping', $data);
        log_message('info', __FUNCTION__ . '=> Update Micor Warehouse: ' . $this->db->last_query());

        if ($this->db->affected_rows() > 0) {
            $result = true;
        } else {
            $result = false;
        }
        
        return $result;
    }
    /**
     * @desc: Insert booking details for spare parts
     * @param Array $data
     * @return boolean
     */
    function insert_data_into_spare_parts($data,$is_insert_bacth = false){
        
        if($is_insert_bacth){
            $this->db->insert_batch('spare_parts_details', $data);
        }else{
            $this->db->insert('spare_parts_details', $data);
        } 
        log_message('info', __FUNCTION__ . '=> Insert Spare Parts: ' .$this->db->last_query());
        return $this->db->insert_id();  
    }
    
    /**
     * @desc:This method ised to get all updated spare booking  by SF
     * @param String $sc_id
     * @return type Array
     */
    function get_updated_spare_parts_booking($sc_id){
        $sql = "SELECT distinct sp.*,DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(sp.date_of_request, '%Y-%m-%d')) AS age_of_request,IF( entity_type = 'vendor','Warehouse','Partner' ) as  entity_type , bd.partner_id,bd.request_type "
                . " FROM spare_parts_details as sp, service_center_booking_action as sc, booking_details as bd "
                . " WHERE  sp.booking_id = sc.booking_id  AND sp.booking_id = bd.booking_id "
                . " AND (sp.status = '".SPARE_PARTS_REQUESTED."' OR sp.status = '".SPARE_SHIPPED_BY_PARTNER."' OR sp.status = '".SPARE_PART_ON_APPROVAL."' OR sp.status = '".SPARE_OOW_SHIPPED."' OR sp.status = '".SPARE_OOW_EST_GIVEN."' OR sp.status = '".SPARE_OOW_EST_REQUESTED."' ) AND (sc.current_status = 'InProcess' OR sc.current_status = 'Pending')"
                . " AND ( sc.internal_status = '".SPARE_PARTS_REQUIRED."' OR sc.internal_status = '".SPARE_PARTS_SHIPPED."' OR sc.internal_status = '".SPARE_OOW_SHIPPED."' OR sc.internal_status = '"._247AROUND_PENDING."' OR sc.internal_status = '".SPARE_OOW_EST_GIVEN."' OR sc.internal_status = '".SPARE_OOW_EST_REQUESTED."' ) "

                . " AND sc.service_center_id = '$sc_id' ";
        $query = $this->db->query($sql);
         //log_message('info', __FUNCTION__  .$this->db->last_query());
         //echo $this->db->last_query();
        return $query->result_array();
    }
    
    function get_spare_parts_booking($where, $select, $group_by = false, $order_by = false, $offset = false, $limit = false,$state=0,$download=NULL){
        $this->_spare_parts_booking_query($where, $select,$state);
        if($group_by){
            $this->db->group_by($group_by);
        }
        
        if($order_by){
            $this->db->order_by($order_by, FALSE);
        }
        if($limit){
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        if($download){
          return $query;
        }
        else{
        return $query->result_array();
    }
    }
    
    function _spare_parts_booking_query($where, $select,$state=0){
        $this->db->select($select, false);
        $this->db->from('spare_parts_details');
        $this->db->join('booking_details','booking_details.booking_id = spare_parts_details.booking_id');
        $this->db->join('users', 'users.user_id =  booking_details.user_id');
        $this->db->join('service_centres', 'spare_parts_details.service_center_id =  service_centres.id');
        $this->db->join('inventory_master_list as i', " i.inventory_id = spare_parts_details.requested_inventory_id", "left");
        $this->db->where($where, false);  
        if($state == 1){
            $stateWhere['agent_filters.agent_id'] = $this->session->userdata('agent_id');
            $stateWhere['agent_filters.is_active'] = 1;
            $this->db->join('agent_filters', 'agent_filters.state =  booking_details.state', "left");
            $this->db->where($stateWhere, false);  
        }
    }
    
    function count_spare_parts_booking($where, $select, $group_by = false,$state=0){
        $this->db->distinct();
        $this->_spare_parts_booking_query($where, $select,$state);
        if($group_by){
            $this->db->group_by($group_by);
        }
        $query = $this->db->get();

        return $query->num_rows();

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
        return "'".ENGINEER_ON_ROUTE."',"
             . "'".CUSTOMER_NOT_REACHABLE."',"
             . "'".CUSTOMER_NOT_VISTED_TO_SERVICE_CENTER."'";
    }
    /**
     * @desc: This method is used to search booking by phone number or booking id
     * this is called by SF panel
     * @param String $searched_text_tmp
     * @param String $service_center_id
     * @return Array
     */
    function search_booking_history($searched_text_tmp,$service_center_id) {
        //Sanitizing Searched text - Getting only Numbers, Alphabets and '-'
        $searched_text = preg_replace('/[^A-Za-z0-9-]/', '', $searched_text_tmp);
        
        $where = "AND (`booking_primary_contact_no` = '$searched_text' OR `booking_alternate_contact_no` = '$searched_text' OR `booking_id` LIKE '%$searched_text%')";
       
        $sql = "SELECT `booking_id`,`booking_date`,`booking_timeslot`, users.name, services.services, current_status, assigned_engineer_id,internal_status "
                . " FROM `booking_details`,users, services "
                . " WHERE users.user_id = booking_details.user_id "
                . " AND services.id = booking_details.service_id "
                . " AND `assigned_vendor_id` = '$service_center_id' ". $where
                . " ";
        $query = $this->db->query($sql);
        
        //log_message('info', __FUNCTION__ . '=> Update Spare Parts: ' .$this->db->last_query());
        return $query->result_array();
    }
    /**
     * @desc: get count total completed booking and total earned SF
     * @param Array $service_center_id
     * @return Array
     */
    function get_sc_earned($service_center_id){
        for($i =0; $i<3; $i++){
            if($i ==0){
                $where = " AND `ud_closed_date` >=  '".date('Y-m-01')."'";
                $select = "date('Y-m-01') As month,";
            } else if($i==1) {
                $where = "  AND  ud_closed_date  >=  DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01')
			    AND ud_closed_date < DATE_FORMAT(NOW() ,'%Y-%m-01')  "; 
                 $select = "DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01') as month,";
            } else if($i ==2){
                $where = "  AND  ud_closed_date  >=  DATE_FORMAT(NOW() - INTERVAL 2 MONTH, '%Y-%m-01')
			    AND ud_closed_date < DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01')";
                 $select = "DATE_FORMAT(NOW() - INTERVAL 2 MONTH, '%Y-%m-01') as month,";
            }
        $sql = "SELECT COUNT( DISTINCT (
                bd.`id`
                ) ) AS total_booking, $select
                SUM( vendor_basic_charges + vendor_st_or_vat_basic_charges 
                 + vendor_extra_charges + vendor_st_extra_charges 
                 + vendor_parts + vendor_st_parts ) AS earned
                FROM booking_unit_details AS ud, booking_details AS bd
                WHERE bd.assigned_vendor_id = '$service_center_id'
                AND pay_to_sf =  '1'
                AND booking_status =  'Completed'
                AND bd.booking_id = ud.booking_id
                $where ";
        
        $query = $this->db->query($sql);
        $result = $query->result_array();
        
        $data[$i] = $result;
        }

        return $data;
    }
    /**
     * @desc:Get total cancel booking by SF
     * @param String $service_center_id
     * @return Array
     */
    function count_cancel_booking_sc($service_center_id){
        for($i =0; $i<3; $i++){
            if($i ==0){
                $where = " AND `ud_closed_date` >=  '".date('Y-m-01')."'";
                $select = "  DATE_FORMAT(NOW() - INTERVAL 0 MONTH, '%Y-%m-01') As month,";
            } else if($i==1) {
                $where = "  AND  ud_closed_date  >=  DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01')
			    AND ud_closed_date < DATE_FORMAT(NOW() ,'%Y-%m-01')  ";
                $select = " DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01') as month,";
                
            } else if($i==2){
                 $where = "  AND  ud_closed_date  >=  DATE_FORMAT(NOW() - INTERVAL 2 MONTH, '%Y-%m-01')
			    AND ud_closed_date < DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01')";
                 $select = "DATE_FORMAT(NOW() - INTERVAL 2 MONTH, '%Y-%m-01') as month,";
                
            }
        $sql  = " SELECT COUNT( DISTINCT (
                bd.`id`
                ) ) AS cancel_booking, $select
                SUM( vendor_basic_charges + vendor_st_or_vat_basic_charges  )  AS lose_amount
                FROM booking_unit_details AS ud, booking_details AS bd
                WHERE bd.assigned_vendor_id = '$service_center_id'
                AND booking_status =  'Cancelled'
                AND current_status = 'Cancelled'
                AND bd.booking_id = ud.booking_id
                $where";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        
        $data[$i] = $result;
        }

        return $data;
    }
    /**
     * @desc: This method returns Shipped spare part booking whose shipped date >= 7 days
     * @return Array
     */
    function get_booking_id_to_convert_pending_for_spare_parts(){
        $sql = "SELECT sp.id, sp.booking_id, scb.service_center_id, b.partner_id FROM `spare_parts_details` as sp, service_center_booking_action as scb, booking_details as b "
                . " WHERE (DATEDIFF(CURRENT_TIMESTAMP , sp.`shipped_date`) >= '".AUTO_ACKNOWLEDGE_SPARE_DELIVERED_TO_SF."') "
                . " AND sp.status = '".SPARE_SHIPPED_BY_PARTNER."' "
                . " AND scb.booking_id = sp.booking_id "
                . " AND sp.booking_id = b.booking_id ";
        $query =  $this->db->query($sql);
        
        return $query->result_array();
    }
    
    /**
     * @Desc: This function is used to get service center login details by sf id
     * @params: sf_id(INT)
     * @return: Array
     * 
     */
    function get_sc_login_details_by_id($sf_id){
        $this->db->where('service_center_id',$sf_id);
        $query = $this->db->get('service_centers_login');
        return $query->result_array();
    }
    
    function get_service_center_action_details($select, $where){
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get('service_center_booking_action');
        return $query->result_array();
    }
    
    function delete_sc_unit_details($where){
        if(!empty($where)){
            $this->db->where($where);
            $this->db->delete('service_center_booking_action');
        }
        log_message('info', __FUNCTION__ . '=> Delete sc unit details: ' .$this->db->last_query());
    }
    /**
     * @desc this is used to make the query for buyback order data
     * @param type $search_value
     * @param type $order
     * @param type $status_flag
     */
    private function _get_bb_order_list_query($search_value, $order, $status_flag) {
        $this->db->select('bb_order_details.id,bb_unit_details.partner_order_id, services,city, order_date, '
                . 'delivery_date, current_status, cp_basic_charge,cp_tax_charge,bb_unit_details.physical_condition,'
                . 'bb_unit_details.working_condition,bb_unit_details.service_id,bb_order_details.city');
        $this->db->from('bb_order_details');

        $this->db->join('bb_unit_details', 'bb_order_details.partner_order_id = bb_unit_details.partner_order_id '
                . ' AND bb_order_details.partner_id = bb_unit_details.partner_id ');
        $this->db->join('services', 'services.id = bb_unit_details.service_id');
        $this->db->where_in('current_status', $this->status[$status_flag]);
        $this->db->where('assigned_cp_id',$this->session->userdata('service_center_id'));

        
        if (!empty($search_value)) { // if datatable send POST for search
            $i = 0;
            foreach ($this->column_search as $item) { // loop column 
           
                if ($i === 0) { // first loop
                     $like .= "( ".$item." LIKE '%".$search_value."%' ";
                } else {
                    $like .= " OR ".$item." LIKE '%".$search_value."%' ";
                }
                 $i++;
            }
            $like .= ") ";

           $this->db->where($like, null, false);
        }

        if (!empty($order)) { // here order processing
            $this->db->order_by($this->column_order[$order[0]['column'] - 1], $order[0]['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
    /**
     * @desc this is used to get the buyback order data 
     * @param type $length
     * @param type $start
     * @param type $search_value
     * @param type $order
     * @param type $status_flag
     * @return Object
     */
    function get_bb_order_list($length, $start, $search_value, $order, $status_flag) {
        $this->_get_bb_order_list_query($search_value, $order, $status_flag);
        if ($length != -1) {
            $this->db->limit($length, $start);
        }
        $query = $this->db->get();

        return $query->result();
    }
    /**
     * @desc This is used to get order data as requested
     * @param String $search_value
     * @param String $order
     * @param Int $status_flag
     * @return Number of rows
     */
    function count_filtered($search_value, $order, $status_flag) {
        $this->_get_bb_order_list_query($search_value, $order, $status_flag);
        $query = $this->db->get();

        return $query->num_rows();
    }
    /**
     * @desc Used to return count of data as requested status
     * @param Int $status_flag
     * @return Count
     */
    public function count_all($status_flag) {
        $this->db->from('bb_order_details');
        $this->db->where_in('current_status', $this->status[$status_flag]);
        $this->db->where('assigned_cp_id',$this->session->userdata('service_center_id'));
        return $this->db->count_all_results();
    }
    
    
    /**
     * @desc Used to check buyback order key 
     * @param $where array
     * @param $select array
     * @param $is_distinct default false
     * @return array
     */
    function check_order_key_exist($where, $select,$is_distinct=False){
        if($is_distinct){
            $this->db->distinct();
        }
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get("bb_unit_details");
        return $query->result_array();
    }
    
    
    /**
     * @desc Used to insert  the  buyback images mapped with order id
     * @param $data array
     * @return $inser_id string
     */
    function insert_bb_order_image($data){
        $insert_id = $this->db->insert('bb_order_image_mapping',$data);
        return $insert_id;
    }
    
    
    /**
     * @desc  Thsi is used to insert gst data in the gst sc gst table. Its inserted by SC
     * @param Array $data
     */
    function insert_gst_details_data($data){
        $insert_id = $this->db->insert('sc_gst_details',$data);
        return $insert_id;
    }
    
    function get_gst_details_table_data($where){
        $this->db->where($where);
        $query = $this->db->get('sc_gst_details');
        return $query->result_array();
    }
    
    
    /**
     * @desc: this is used to get the sf rating for those bookings which rating was done
     * @param: $sf_id string
     * @return: array()
     */
    function get_vendor_rating_data($sf_id){
        $sql = "SELECT ROUND(AVG(rating_stars),1) as rating , count(booking_id) as count
                FROM booking_details WHERE assigned_vendor_id = '$sf_id'
                AND rating_stars IS NOT NULL AND current_status = '"._247AROUND_COMPLETED."'";    
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    function get_collateral_by_condition($where){
        $this->db->select('collateral.*,collateral_type.*');
        $this->db->where($where);
        $this->db->join("collateral_type","collateral_type.id=collateral.collateral_id");
        $query= $this->db->get('collateral');
        return  $query->result_array();
    }
    /*
     * This function is used to get collateral files against a booking id
    */
    function get_collateral_for_service_center_bookings($booking_id){
        $collateralData = array();
       $bookingDataSql = "SELECT booking_details.booking_id,booking_details.partner_id,booking_details.service_id,appliance_brand,appliance_category,appliance_capacity,model_number,
CASE WHEN booking_details.request_type like 'Repair%' THEN 'repair' WHEN booking_details.request_type like 'Repeat%' THEN 'repair' ELSE 'installation'END as request_type
FROM booking_unit_details JOIN booking_details ON  booking_details.booking_id = booking_unit_details.booking_id WHERE booking_details.booking_id='".$booking_id."' GROUP BY request_type";
        $query = $this->db->query($bookingDataSql);
        $data =  $query->result_array();
        if(!empty($data)){
            foreach($data as $bookingData){
                $where['entity_id'] = $bookingData['partner_id'];
                $where['appliance_id'] = $bookingData['service_id'];
                $where['request_type'] = $bookingData['request_type'];
                $where['brand'] = $bookingData['appliance_brand'];
                $where['is_valid'] = 1;
                if($bookingData['appliance_category']){
                    $where['category'] = $bookingData['appliance_category'];
                }
                if($bookingData['appliance_capacity'] && $bookingData['model_number']){
                    $where["(capacity = '".$bookingData['appliance_capacity']."' OR model = '".$bookingData['model_number']."')"] = NULL;
                }
                elseif ($bookingData['appliance_capacity']) {
                    $where['capacity'] = $bookingData['appliance_capacity'];
                }
                elseif ($bookingData['model_number']) {
                    $where['model'] = $bookingData['model_number'];
                }
            }
            $collateralData = $this->get_collateral_by_condition($where);
            if(empty($collateralData)){
                unset($where['model']);
                $collateralDataNew = $this->get_collateral_by_condition($where);
            }
            else{
             return $collateralData;   
            }
        }
        return $collateralDataNew;
    }
    
    function create_new_entry_in_spare_table($data,$id){
        $spare_details = $this->get_spare_parts_booking(array('spare_parts_details.id' => $id),'spare_parts_details.*');
        if(!empty($spare_details)){
            unset($spare_details[0]['id']);
            $spare_details[0]['status'] = 'Shipped';
            $spare_details[0]['model_number_shipped'] = $data['model_number_shipped'];
            $spare_details[0]['parts_shipped'] = $data['parts_shipped'];
            $spare_details[0]['shipped_parts_type'] = $data['shipped_parts_type'];
            $spare_details[0]['shipped_date'] = $data['shipped_date'];
            $spare_details[0]['courier_name_by_partner'] = $data['courier_name_by_partner'];
            $spare_details[0]['awb_by_partner'] = $data['awb_by_partner'];
            $spare_details[0]['remarks_by_partner'] = $data['remarks_by_partner'];
            $spare_details[0]['challan_approx_value'] = $data['challan_approx_value'];
            $spare_details[0]['partner_challan_number'] = isset($data['partner_challan_number'])?$data['partner_challan_number']:NULL;
            $spare_details[0]['partner_challan_file'] = isset($data['partner_challan_file'])?$data['partner_challan_file']:NULL;
            $spare_details[0]['incoming_invoice_pdf'] = isset($data['incoming_invoice_pdf'])?$data['incoming_invoice_pdf']:NULL;
            $spare_details[0]['shipped_inventory_id'] = isset($data['shipped_inventory_id'])?$data['shipped_inventory_id']:NULL;
            $insert_id = $this->insert_data_into_spare_parts($spare_details[0]);
        }else{
            $insert_id =  false;
        }
        
        return $insert_id;
    }
    
    function get_spare_parts_on_group($where, $select, $group_by, $sf_id = false, $start = -1, $end = -1,$count = 0,$orderBY=array(),$nrn=FALSE){
        $this->db->_reserved_identifiers = array('*','CASE',')','FIND_IN_SET','STR_TO_DATE','%d-%m-%Y,"")');
        $this->db->_protect_identifiers = FALSE;
        $this->db->select($select, false);
        $this->db->from("spare_parts_details");
        $this->db->join('booking_details', " booking_details.booking_id = spare_parts_details.booking_id");
        $this->db->join('inventory_master_list as i', " i.inventory_id = spare_parts_details.requested_inventory_id", "left");
        $this->db->join('services', " services.id = booking_details.service_id");
        if($sf_id){
            $this->db->join("inventory_stocks", "inventory_stocks.inventory_id = requested_inventory_id AND inventory_stocks.entity_id = '".$sf_id."' and inventory_stocks.entity_type = '"._247AROUND_SF_STRING."'", "left");
        }
        $this->db->join("users", "users.user_id = booking_details.user_id");
        $this->db->join("service_centres", "service_centres.id = booking_details.assigned_vendor_id");
        if ($nrn) {
        $this->db->join("spare_nrn_approval", "spare_parts_details.booking_id = spare_nrn_approval.booking_id","left");
        }
        $this->db->where($where);
        if($start > -1){
            $this->db->limit($start, $end);
        }
        if(!$count){
        $this->db->group_by($group_by);
        }
        if(!empty($orderBY)){
            $this->db->order_by($orderBY['column'], $orderBY['sorting']);
        }
        $query = $this->db->get();
        log_message('info', __METHOD__. "  ".$this->db->last_query());
        return $query->result_array();
    }
    
    function dashboard_data_count($from_count,$second_count)
    {
        $today_date=date('Y-m-d');
        $this->db->select($from_count);
        $this->db->from('sf_dashboard');
        $this->db->where('date',$today_date);
        $result=$this->db->get()->row_array();
        if(!empty($result))
        {
            $new_count=$result[$from_count]+1;
            $data=array($from_count=>$new_count);
            $this->db->where('date',$today_date);
            $this->db->update('sf_dashboard',$data);
            $afftected_Rows=$this->db->affected_rows();
            $return=$afftected_Rows;
        }
        else
        {
            $data=array(
                'date'=>$today_date,
                $from_count=>1,
                $second_count=>0
           );
            $this->db->insert('sf_dashboard',$data);
            $return_id=$this->db->insert_id();
            $return=$return_id;
        }
        return $return;
    }
    /**
     * @desc: this is used to insert awb details of spare parts
     * @param: array
     * @return: id
     */
    function insert_into_awb_details($data){
        $this->db->insert('courier_company_invoice_details',$data);
        return $this->db->insert_id();
    }
    /**
     * @desc This function is used to insert category and capacity updated by DF
     */
    function insert_update_applaince_by_sf($data){
        $this->db->insert('appliance_updated_by_sf',$data);
        return $this->db->insert_id();
    }
    
    /**
     * @desc: this is used to get pending booking specific service center id
     * @param: service center id
     * @return: Pending booking
     */
    function pending_bookings_sf_excel($service_center_id){
               
        $sql = " SELECT DISTINCT (sc.`booking_id`), `sc`.admin_remarks, "
            . " bd.booking_primary_contact_no, "
            . " users.name as customername,  "
            . " bd.booking_date,"
            . " bd.partner_id,"
            . " bd.booking_jobcard_filename,"
            . " bd.assigned_engineer_id,"
            . " bd.booking_timeslot, "
            . " bd.current_status, "
            . " bd.amount_due, "
            . " bd.flat_upcountry, "
            . " bd.request_type, "
            . " bd.count_escalation, "
            . " bd.is_upcountry, "
            . " bd.count_reschedule, "
            . " bd.upcountry_paid_by_customer, "
            . " bd.is_penalty, "
            . " bd.booking_address, "
            . " bd.booking_pincode, "
            . " bd.create_date, "
            . " bd.order_id, "
            . " bd.booking_address, "
            . " bd.booking_alternate_contact_no, "
            . " bd.request_type, "
            . " bd.internal_status, bd.current_status,"
            . " bd.booking_remarks, bd.service_id,"
            . " services, ed.name as eng_name,"
            . " (SELECT GROUP_CONCAT(DISTINCT brand.appliance_brand) FROM booking_unit_details brand WHERE brand.booking_id = bd.booking_id GROUP BY brand.booking_id ) as appliance_brand,"
            . " (SELECT GROUP_CONCAT(model_number) FROM booking_unit_details brand WHERE booking_id = bd.booking_id) as model_numbers,"
             . "CASE WHEN (SELECT Distinct 1 FROM booking_unit_details as bu1 WHERE bu1.booking_id = bd.booking_id "
                . "AND price_tags = 'Wall Mount Stand' AND bu1.service_id = 46 ) THEN (1) ELSE 0 END as is_bracket, " 

             . " CASE WHEN (bd.is_upcountry = 1 AND upcountry_paid_by_customer =0 AND bd.sub_vendor_id IS NOT NULL)  "
             . " THEN (SELECT  ( round((bd.upcountry_distance * bd.sf_upcountry_rate)/(count(b.id)),2)) "
             . " FROM booking_details AS b WHERE b.booking_pincode = bd.booking_pincode "
             . " AND b.booking_date = bd.booking_date AND is_upcountry =1 "
             . " AND b.sub_vendor_id IS NOT NULL "
             . " AND b.upcountry_paid_by_customer = 0 "
             . " AND b.sf_upcountry_rate = bd.sf_upcountry_rate"
             . " AND bd.current_status IN ('Pending','Rescheduled', 'Completed')  "
             . " AND b.assigned_vendor_id = '$service_center_id' ) "
             . " WHEN (bd.is_upcountry = 1 AND upcountry_paid_by_customer = 1 AND bd.sub_vendor_id IS NOT NULL ) "
             . " THEN (bd.upcountry_distance * bd.sf_upcountry_rate) "
             . " ELSE 0 END AS upcountry_price, "

            . " (SELECT SUM(vendor_basic_charges + vendor_st_or_vat_basic_charges)
                    FROM booking_unit_details AS u
                    WHERE u.booking_id = bd.booking_id AND pay_to_sf = '1') AS earn_sc,
"
            . " DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(bd.initial_booking_date, '%d-%m-%Y')) as age_of_booking "
            . " FROM service_center_booking_action as sc "
            . " JOIN booking_details as bd on bd.booking_id =  sc.booking_id " 
            . " JOIN users on bd.user_id = users.user_id " 
            . " JOIN services on bd.service_id = services.id " 
            . " JOIN service_centres AS s on s.id = bd.assigned_vendor_id "
            . " LEFT JOIN engineer_details As ed on ed.id = bd.assigned_engineer_id"
            . " WHERE sc.service_center_id = '$service_center_id' "
            . " AND bd.assigned_vendor_id = '$service_center_id' "
            . " AND (bd.current_status='Pending' OR bd.current_status='Rescheduled')"
            . " ORDER BY count_escalation desc, STR_TO_DATE(`bd`.booking_date,'%d-%m-%Y') desc ";

        $query1 = $this->db->query($sql);

        $result = $query1->result();

        return $result;
    }
    
    function spare_assigned_to_partner($where, $select, $group_by, $sf_id = false, $start = -1, $end = -1,$count = 0,$orderBY=array()){
        $this->db->_reserved_identifiers = array('*','CASE',')','FIND_IN_SET','STR_TO_DATE','%d-%m-%Y,"")');
        $this->db->_protect_identifiers = FALSE;
        $this->db->select($select, false);
        $this->db->from("spare_parts_details");
        $this->db->join('booking_details', " booking_details.booking_id = spare_parts_details.booking_id");
        $this->db->join('inventory_master_list as i', " i.inventory_id = spare_parts_details.requested_inventory_id", "left");
        $this->db->join('services', " services.id = booking_details.service_id");
        if($sf_id){
            $this->db->join("inventory_stocks", "inventory_stocks.inventory_id = requested_inventory_id AND inventory_stocks.entity_id = '".$sf_id."' and inventory_stocks.entity_type = '"._247AROUND_SF_STRING."'", "left");
        }
        $this->db->join("users", "users.user_id = booking_details.user_id");
        $this->db->join("service_centres", "service_centres.id = booking_details.assigned_vendor_id");
        $this->db->where($where);
        if($start > -1){
            $this->db->limit($start, $end);
        }
        if(!$count){
        $this->db->group_by($group_by);
        }
        if(!empty($orderBY)){
            $this->db->order_by($orderBY['column'], $orderBY['sorting']);
        }
        $query = $this->db->get();
        //echo $this->db->last_query();exit;
        log_message('info', __METHOD__. "  ".$this->db->last_query());
        return $query->result_array();
    }
    
    /**
     * 
     * @param type $warehouse_id
     */
    function get_warehouse_state($warehouse_id) {
        $sql = "SELECT 
                    warehouse_state_relationship.state
                FROM 
                    contact_person 
                    join warehouse_person_relationship on (contact_person.id = warehouse_person_relationship.contact_person_id)
                    join warehouse_state_relationship on (warehouse_person_relationship.warehouse_id = warehouse_state_relationship.warehouse_id)
                WHERE 
                    entity_id = {$warehouse_id} and entity_type = '"._247AROUND_SF_STRING."';";
        
        return array_column($this->db->query($sql)->result_array(), 'state');            
    }
    
    function download_service_center_completed_bookings() {
        $sql = "SELECT
                    `booking_details`.`booking_id`,
                    `users`.`name` AS customername,
                    `booking_details`.`booking_primary_contact_no`,
                    `services`.`services`,
                    `booking_details`.`request_type`,
                    date(booking_details.closed_date),
                    `booking_details`.`closing_remarks`,
                    `amount_due`,
                    `booking_details`.`rating_stars`,
                    spare_parts_details.parts_shipped,
                    (
                      CASE WHEN booking_details.assigned_engineer_id IS NOT NULL AND booking_details.assigned_engineer_id = 24700001 THEN 'Default Engineer' WHEN booking_details.assigned_engineer_id IS NOT NULL THEN engineer_details.name ELSE '-'
                    END
                  ) AS Engineer,
                    if(
                    DATEDIFF(
                      DATE(
                        booking_details.service_center_closed_date
                      ),
                      DATE_FORMAT(
                        STR_TO_DATE(
                          booking_details.initial_booking_date,
                          '%d-%m-%Y'
                        ),
                        '%Y-%c-%d'
                      )
                    ) > 0, DATEDIFF(
                      DATE(
                        booking_details.service_center_closed_date
                      ),
                      DATE_FORMAT(
                        STR_TO_DATE(
                          booking_details.initial_booking_date,
                          '%d-%m-%Y'
                        ),
                        '%Y-%c-%d'
                      )
                    )  , '0') AS tat
                    FROM
                      (`booking_details`)
                    LEFT JOIN
                      spare_parts_details ON(
                        booking_details.booking_id = spare_parts_details.booking_id AND spare_parts_details.status = 'Completed'
                      )
                    LEFT JOIN
                      engineer_details ON(
                        booking_details.assigned_engineer_id = engineer_details.id
                      )
                    JOIN
                      `services` ON `services`.`id` = `booking_details`.`service_id`
                    JOIN
                      `users` ON `users`.`user_id` = `booking_details`.`user_id`
                    WHERE
                      `booking_details`.`current_status` = 'Completed' AND `assigned_vendor_id` = '{$this->session->userdata('service_center_id')}'
                    ORDER BY
                      `closed_date` DESC";
        
        return $this->db->query($sql)->result_array();     
    }
    
        /**
     * @desc: Insert booking details for spare parts
     * @param Array $data
     * @return boolean
     */
    function insert_data_into_spare_invoice_details($data){
        
       if(!empty($data)){
         $this->db->insert('spare_invoice_details', $data);  
       }       
        log_message('info', __FUNCTION__ . '=> Insert Spare Parts: ' .$this->db->last_query());
        return $this->db->insert_id();  
    }
}
