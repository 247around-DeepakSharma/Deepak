<?php

class Booking_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
        $this->load->model('reusable_model');
    }

    /**
     *  @desc : This function is to add new brand to our database for a service.
     *
     *  This helps to add any new brand found(told by customer) for any
     *      service(like for Television, Refrigerator, etc)
     *
     *  @param : service id and new brand
     *  @return : void
     */
    function addNewApplianceBrand($service_id, $newbrand) {
        $data = array(
            'service_id'=>$service_id,
            'brand_name'=>$newbrand
        );
        
        $this->db->insert('appliance_brands',$data);
        return $this->db->insert_id();
    }
    
    // Update Price in unit details
    function update_price_in_unit_details($data, $unit_details){

        $data['tax_rate'] = $unit_details[0]['tax_rate'];
        $data['around_paid_basic_charges'] = $unit_details[0]['around_paid_basic_charges'];
        // calculate partner paid tax amount
        $data['partner_paid_tax'] =  ($unit_details[0]['partner_paid_basic_charges'] * $data['tax_rate'])/ 100;
       
        $vendor_total_basic_charges =  ($data['customer_paid_basic_charges'] + $unit_details[0]['partner_paid_basic_charges'] + $data['around_paid_basic_charges']) * ($unit_details[0]['vendor_basic_percentage']/100 );
        // Calculate  total partner paid charges with tax
        $data['partner_paid_basic_charges'] = $unit_details[0]['partner_paid_basic_charges'] + $data['partner_paid_tax'];
        $around_total_basic_charges = ($data['customer_paid_basic_charges'] + $data['partner_paid_basic_charges'] + $data['around_paid_basic_charges'] - $vendor_total_basic_charges);

        $data['around_st_or_vat_basic_charges'] = $this->get_calculated_tax_charge($around_total_basic_charges, $data['tax_rate'] );
        $data['vendor_st_or_vat_basic_charges'] = $this->get_calculated_tax_charge($vendor_total_basic_charges, $data['tax_rate'] );

        $data['around_comm_basic_charges'] = $around_total_basic_charges - $data['around_st_or_vat_basic_charges'];
        $data['vendor_basic_charges'] = $vendor_total_basic_charges - $data['vendor_st_or_vat_basic_charges'];

        $total_vendor_addition_charge = $data['customer_paid_extra_charges'] * addtitional_percentage;
        $total_around_additional_charge = $data['customer_paid_extra_charges'] - $total_vendor_addition_charge;

        $data['around_st_extra_charges'] = $this->get_calculated_tax_charge($total_around_additional_charge, $data['tax_rate']);
        $data['vendor_st_extra_charges'] = $this->get_calculated_tax_charge($total_vendor_addition_charge, $data['tax_rate']  );

        $data['around_comm_extra_charges'] = $total_around_additional_charge - $data['around_st_extra_charges'];
        $data['vendor_extra_charges'] = $total_vendor_addition_charge - $data['vendor_st_extra_charges'] ;

        $total_vendor_parts_charge = $data['customer_paid_parts'] * parts_percentage;
        $total_around_parts_charge =  $data['customer_paid_parts'] - $total_vendor_parts_charge;
        $data['around_st_parts'] = $this->get_calculated_tax_charge($total_around_parts_charge, $data['tax_rate'] );
        $data['vendor_st_parts'] =  $this->get_calculated_tax_charge($total_vendor_parts_charge,  $data['tax_rate']);
        $data['around_comm_parts'] =  $total_around_parts_charge - $data['around_st_parts'];
        $data['vendor_parts'] = $total_vendor_parts_charge - $data['vendor_st_parts'] ;
        // Check vendor has service tax for the service
        if($data['customer_paid_basic_charges'] == 0){
           
            $is_gst = $this->vendor_model->is_tax_for_booking($unit_details[0]['booking_id']);
            if(empty($is_gst[0]['gst_no']) ){
                $vendor_total_basic_charges =  $data['vendor_basic_charges'];
                $data['vendor_st_or_vat_basic_charges'] = 0;
            } 
        } 

        $vendor_around_charge = ($data['customer_paid_basic_charges'] + $data['customer_paid_parts'] + $data['customer_paid_extra_charges']) - ($vendor_total_basic_charges + $total_vendor_addition_charge + $total_vendor_parts_charge );

        if($vendor_around_charge > 0){

            $data['vendor_to_around'] = $vendor_around_charge;
            $data['around_to_vendor'] = 0;

        } else {
            $data['vendor_to_around'] = 0;
            $data['around_to_vendor'] = abs($vendor_around_charge);
        }
        if(isset($data['internal_status'])){
            unset($data['internal_status']);
        }
        
        if($unit_details[0]['vendor_basic_percentage'] == 0){
             $data['around_to_vendor'] = 0;
             $data['vendor_to_around'] = 0;
             $data['around_st_or_vat_basic_charges'] = 0;
             $data['around_comm_basic_charges'] = 0;
        }
        $this->db->where('id', $data['id']);
        $this->db->update('booking_unit_details',$data);
    }

    /**
     * @desc: get booking unit details(Prices) from booking id or appliance id. it gets all prices in the array of key value quantity.
     * @param: booking id, appliance id
     * @return:  Array
     */
    function getunit_details($booking_id="", $appliance_id=""){
        $where = "";

        if($booking_id !=""){
           $where = " `booking_unit_details`.booking_id = '$booking_id' ";
            $sql = "SELECT distinct(appliance_id), appliance_brand as brand,booking_unit_details.partner_id, service_id, booking_id, appliance_category as category, appliance_capacity as capacity, `booking_unit_details`.`model_number`, appliance_description as description, `booking_unit_details`.`purchase_date`,`booking_unit_details`.sub_order_id, `booking_unit_details`.`sf_purchase_date`, `booking_unit_details`.`sf_model_number`
            from booking_unit_details Where $where  ";

        } else if ($appliance_id != "") {

	    $where = " `booking_unit_details`.appliance_id = '$appliance_id' ";

            $sql = "SELECT distinct(appliance_id), brand, booking_id, category, capacity, booking_unit_details.partner_id, `appliance_details`.`model_number`,description, `appliance_details`.`purchase_date`, `appliance_details`.serial_number,`booking_unit_details`.sub_order_id, `booking_unit_details`.`sf_purchase_date`, `booking_unit_details`.`sf_model_number`
            from booking_unit_details,  appliance_details Where $where  AND `appliance_details`.`id` = `booking_unit_details`.`appliance_id`  ";

        }

        $query = $this->db->query($sql);
        $appliance =  $query->result_array();

        foreach ($appliance as $key => $value) {
            // get data from booking unit details table on the basis of appliance id
            $this->db->select('id as unit_id, pod, invoice_pod, price_tags, customer_total, serial_number_pic, around_net_payable, partner_net_payable, customer_net_payable, customer_paid_basic_charges, customer_paid_extra_charges, customer_paid_parts, booking_status, partner_paid_basic_charges,product_or_services, serial_number, around_paid_basic_charges, partner_invoice_id');
            $this->db->where('appliance_id', $value['appliance_id']);
            $this->db->where('booking_id', $value['booking_id']);
            $this->db->order_by("price_tags","asc");
            $query2 = $this->db->get('booking_unit_details');

            $result = $query2->result_array();
            $appliance[$key]['quantity'] = $result; // add booking unit details array into quantity key of previous array
        }

        return $appliance;
    }

    /**
     * @desc: update price in booking unit details
     */
    function update_unit_details($data){

        if($data['booking_status'] == _247AROUND_COMPLETED || $data['booking_status'] == _247AROUND_PENDING ){
            // get booking unit data on the basis of id
            $this->db->select('booking_id, around_net_payable,booking_status, '
                    . ' partner_net_payable as partner_paid_basic_charges, partner_net_payable, '
                    . ' tax_rate, price_tags, around_paid_basic_charges, product_or_services, vendor_basic_percentage');

            $this->db->where('id', $data['id']);
            $query = $this->db->get('booking_unit_details');
            $unit_details = $query->result_array();
            
            $this->update_price_in_unit_details($data, $unit_details);

        } else if($data['booking_status'] == "Cancelled") {
            $closed_date = date("Y-m-d H:i:s");
            if(isset($data['ud_closed_date'])){
                $closed_date = $data['ud_closed_date'];
            } 
            // Update price in unit table
            $this->db->where('id', $data['id']);
            $this->db->update('booking_unit_details', array('booking_status' => 'Cancelled',
                'ud_closed_date'=> $closed_date));
        }

    }

    /**
     *  @desc : This function is to insert booking state changes.
     *
     *  @param : Array $details Booking state change details
     *  @return :
     */
    function insert_booking_state_change($details) {
        $this->db->insert('booking_state_change', $details);

        return $this->db->insert_id();
    }
    
    /**
     * @Desc: This function is used to get the partner status from partner_status table
     * @params: $partner_id,$current_status, $internal_status 
     * @return: array
     * 
     */
    function get_partner_status($partner_id,$current_status, $internal_status){
        $this->db->select('partner_current_status, partner_internal_status,actor,next_action');
        $this->db->where(array('partner_id' => $partner_id,'247around_current_status' => $current_status, '247around_internal_status' => $internal_status));
        $this->db->or_where('partner_id',_247AROUND);
        $this->db->where(array('247around_current_status' => $current_status, '247around_internal_status' => $internal_status));
        $this->db->order_by("id", "DESC ");
        $query = $this->db->get('partner_booking_status_mapping');
        return $query->result_array();
        
    }
    /**
     * @desc TThis is used to get those upcountry bookings who have waiting to approval (Three days old booking)
     * @return type
     */
    function get_booking_to_cancel_not_approved_upcountry(){
        $sql =" SELECT booking_id,partner_id FROM booking_details where "
                . " DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) > -3 "
                . " AND current_status IN ('Pending', 'Rescheduled') AND is_upcountry = '1' AND upcountry_partner_approved = '0' ";
        $query = $this->db->query($sql);
        return $query->result_array();      
    }
    
    
    /**
     *  @desc : This function is used to show those numbers who gave missed call after sending rating sms
     *  @param : void
     *  @return : array()
     */
    function get_missed_call_rating_not_taken_booking_data(){
         $sql = "SELECT DISTINCT  u.name,rp.from_number,rp.create_date,
                 CASE rp.To WHEN '".GOOD_MISSED_CALL_RATING_NUMBER."' "
                . " THEN 'good_rating' WHEN '".POOR_MISSED_CALL_RATING_NUMBER."' "
                . " THEN 'bad_rating' ELSE NULL END as "
                . " 'rating'FROM booking_details as bd,"
                . " rating_passthru_misscall_log as rp, users as u "
                . " WHERE current_status = 'Completed' "
                . " AND bd.rating_stars IS NULL AND EXISTS "
                . " (SELECT 1 from sms_sent_details as ssd WHERE "
                . " ssd.booking_id = bd.booking_id AND ssd.sms_tag IN "
                . " ('missed_call_rating_sms', 'complete_booking','complete_booking_snapdeal')) "
                . " AND bd.closed_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01') - INTERVAL 2 MONTH "
                . " AND rp.from_number = bd.booking_primary_contact_no "
                . " AND u.user_id = bd.user_id "
                . " AND rp.create_date >= bd.closed_date "
                . "AND rating_unreachable_count < 3 "
                 . "AND rp.To IN ('".GOOD_MISSED_CALL_RATING_NUMBER."','".POOR_MISSED_CALL_RATING_NUMBER."') GROUP BY bd.user_id";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    /**
     * @Desc: This function is used to get SMS sent details for which 
     * booking id is not available in sms_sent_details table
     * @params: booking_id
     * @return: array
     * 
     */
    function get_sms_sent_details_for_empty_bookings($phone){
        $this->db->select('*');
        $this->db->where('booking_id','');
        $this->db->where('phone',$phone);
        $query = $this->db->get('sms_sent_details');
        return $query->result_array();
    }
    
    /**
     *  @desc : This function is used to get total bookings based on booking status type
     *  @param : $post string
     *  @return: Array()
     */
    public function count_all_bookings_by_status($post) {
        $this->_get_bookings_by_status($post, 'count(distinct(booking_details.booking_id)) as numrows');
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
    }  
    
    
    /**
     * @Desc: This function is used get bookings data from booking _details by using any condition
     * @params: $select string
     * @params: $where string
     * @params: $order_by string
     * @params: $group_by string
     * @return: $query array()
     * 
     */
    function get_bookings_count_by_any($select, $where, $order_by = "" , $group_by = "", $is_object = false){
        $this->db->select($select,false);
        $this->db->where($where,false);
        if(!empty($group_by)){
            $this->db->group_by($group_by);
        }
        if(!empty($order_by)){
            $this->db->order_by($order_by,false);
        }
        $this->db->from('booking_details');
        $this->db->join("services", "services.id = booking_details.service_id");
        $query = $this->db->get();
        if($is_object){
            return $query->result();
        } else {
            return $query->result_array();
        }
    }
    
    function get_advance_search_result_data($table,$select,$where=array(),$join=array(),$limitArray=array(),$orderBYArray=array(),$whereIN=array(),$JoinTypeTableArray=array()){
        $this->db->_reserved_identifiers = array('*','STR_TO_DATE');
       $query = $this->reusable_model->get_search_query($table,$select,$where,$join,$limitArray,$orderBYArray,$whereIN,$JoinTypeTableArray);
       return $query->result_array(); 
    }
    function get_advance_search_result_count($table,$select,$where=array(),$join=array(),$limitArray=array(),$orderBYArray=array(),$whereIN=array(),$JoinTypeTableArray=array()){
        $this->db->_reserved_identifiers = array('*','STR_TO_DATE');
       $this->reusable_model->get_search_query($table,$select,$where,$join,$limitArray,$orderBYArray,$whereIN,$JoinTypeTableArray);
       return $this->db->affected_rows();
    }
    function date_compare_queries($a, $b) {
        if ($a->booking_date == '' || $b->booking_date == '') {
            if (strtotime($a->create_date) == strtotime($b->create_date)) {
                $t1 = $a->id;
                $t2 = $b->id;
            } else {
                $t1 = strtotime($a->create_date);
                $t2 = strtotime($b->create_date);
            }
        } else {
            $t1 = strtotime($a->booking_date);
            $t2 = strtotime($b->booking_date);
        }

        return $t2 - $t1;
    }

    function date_compare_bookings($a, $b) {
        $t1 = strtotime($a->booking_date);
        $t2 = strtotime($b->booking_date);

        return $t2 - $t1;
    }

    function date_compare_assign_pending_bookings($a, $b) {
        $t1 = strtotime($a['booking_date']);
        $t2 = strtotime($b['booking_date']);

        return $t1 - $t2;
    }

     /**
     * @desc: this is used to add appliances
     * @param: Array(Appliances details)
     * @return: appliance id
     */
    function addappliance($appliance_detail){
        //log_message ('info', __METHOD__ . "appliance_detail data". print_r($appliance_detail, true));
        $this->db->insert('appliance_details', $appliance_detail);

        return $this->db->insert_id();
    }

    /**
     * @desc: Update appliace details
     * @param:  user id
     * @param: Array
     * @return : void
     */
    function update_appliances($appliance_id, $appliance_details){
        $this->db->where('id', $appliance_id);
        $this->db->update('appliance_details', $appliance_details);
        log_message ('info', __METHOD__ . "=> Booking  SQL ". $this->db->last_query());
    }

    /**
     *  @desc : add unit details for a booking
     *  @param : booking(appliance) details
     *  @return : none
     */

    function addunitdetails($booking){
        log_message ('info', __METHOD__);
        $this->db->insert('booking_unit_details', $booking);
        log_message ('info', __METHOD__ . "=> Booking  SQL ". $this->db->last_query());
        return $this->db->insert_id();
    }
    /**
     * @desc: Update unit details
     * @param String $booking_id
     * @param Array $data
     * @return Array
     */
    function update_booking_unit_details($booking_id, $data){
        $this->db->where('booking_id', $booking_id);
        $result = $this->db->update('booking_unit_details', $data);
        log_message ('info', __METHOD__ . "=> Booking  SQL ". $this->db->last_query());
        return $result;
    }
    
    function update_booking_unit_details_by_any($where, $data){
        $this->db->where($where);
        $result = $this->db->update('booking_unit_details', $data);
        //log_message ('info', __METHOD__ . "=> Booking  SQL ". $this->db->last_query());
        return $result;
    }


    /** @description:* add booking
     *  @param : booking
     *  @return : array (booking)
     */

    function addbooking($booking){
	$this->db->insert('booking_details', $booking);
        
        log_message ('info', __METHOD__ . "=> Booking  SQL ". $this->db->last_query());
        
        return $this->db->insert_id();
    }


    /**
     *  @desc : this function is to get a particular service.
     *
     * 	This function gets the service name with the help of its id
     *
     *  @param : service id
     *  @return : service
     */
    function selectservicebyid($service_id) {
        $query = $this->db->query("SELECT services from services where id='$service_id'");
        return $query->result_array();
    }

    /**
     *  @desc : to select the services.
     *
     *  The services we get are the once that are active from our end
     *
     *  @param : void
     *  @return : array with active services
     */
    function selectservice($format = null, $showAll = null) {
        $strWhere = 1;
        if(empty($showAll))
        {
            $strWhere = 'isBookingActive = 1';
        }
        $query = $this->db->query("Select id,services,isBookingActive from services where ".$strWhere." order by services");
        if(empty($format)){
            return $query->result();
        }else{
            return $query->result_array();
        }
    }

    /**
     *  @desc : Function to view completed bookings in Descending order according by close date.
     *
     *  Here start and limit upto which we want to see the output is given.
     *
     *  Shows users name, phone number and services name.
     *
     *  Also shows complete booking details and also assigned service centre's basic
     *  details for that particular booking.
     *
     *  @param: start and limit of result
     *  @return : array of booking, users, services and service center details in sorted
     *          format by closed date in descending order.
     */
    function view_completed_or_cancelled_booking($limit, $start, $status, $booking_id= "") {
        $add_limit = ""; $where = "";
        if($limit != "All"){
             $add_limit = " LIMIT $start, $limit ";
        }
        if($booking_id != ""){
            $where =  "  `booking_details`.booking_id = '$booking_id' AND ";
        }
        $query = $this->db->query("Select distinct services.services,users.name as customername,penalty_on_booking.active as penalty_active,
            users.phone_number, booking_details.*, service_centres.name as service_centre_name,
            service_centres.district as city, service_centres.primary_contact_name,
            service_centres.primary_contact_phone_1
            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
            LEFT JOIN `penalty_on_booking` ON `booking_details`.`booking_id` = `penalty_on_booking`.`booking_id` and penalty_on_booking.active = '1'
            WHERE type = 'Booking' AND $where
            (booking_details.current_status = '$status')
	    ORDER BY closed_date DESC $add_limit "
        );
       $temp = $query->result();
       usort($temp, array($this, 'date_compare_bookings')); 
       return $temp;
    }

    /**
     * @desc : This funtion counts total number of bookings
     * @param : void
     * @return : total number bookings
     */
    public function total_booking() {
        return $this->db->count_all_results("booking_details");
    }

    /**
     * @desc : This funtion counts total number of pending or rescheduled bookings
     *
     * Also matches users id from users and booking details table.
     *
     * @param : booking id and service center id
     * @return : total number of pending or rescheduled bookings
     */
    public function total_pending_booking($booking_id = "", $service_center_id = "",$partner_id = False) {
        $where = "";

        if ($booking_id != "") {
            $where .= "AND `booking_details`.`booking_id` = '$booking_id'";
        } else {
            $where .= "AND DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= -1";
        }

        if ($service_center_id != "") {
            $where .= " AND assigned_vendor_id = '" . $service_center_id . "'";
            $where .= "AND DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= -1";
        }
        
        if($partner_id === true){
            $where .= " AND partner_id IN ('"._247AROUND."') ";
            $where .=" AND request_type IN ('Repair','".REPAIR_IN_WARRANTY_TAG."','".REPAIR_OOW_TAG."')";
        }

        $query = $this->db->query("Select count(*) as count from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id` WHERE
            `booking_id` NOT LIKE 'Q-%' $where AND
            (booking_details.current_status='Pending' OR booking_details. current_status='Rescheduled')"
        );
        
        $count = $query->result_array();

        return $count[0]['count'];
    }

    /**
     * @desc : This funtion used to sort bookings based on their booking date
     *
     * Also matches users id from users and booking details table.
     *
     * @param : start and limit of result, booking id and service center id
     * @return : date sorted booking for pending or rescheduled bookings, booking details,
     *          basic user's and service center details.
     */
    function date_sorted_booking($limit, $start, $booking_id = "", $service_center_id = "", $partner_id = false) {
        $where = "";

        if ($booking_id != "") {
            $where .= "AND `booking_details`.`booking_id` = '$booking_id'";

        } else {
            $where .= " AND DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= -1";
        }

        if ($service_center_id != "") {
            $where .= " AND assigned_vendor_id = '" . $service_center_id . "'";
            $where .= " AND DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= -1";
        }
        
        if($partner_id === true){
            $where .= " AND partner_id IN ('"._247AROUND."') ";
            $where .=" AND request_type IN ('Repair','Repair - In Warranty','".REPAIR_OOW_TAG."')";
        }

        $add_limit = "";

        if($start !== "All"){
            $add_limit = " limit $start, $limit ";
        }


        $query = $this->db->query("Select services.services,
            users.name as customername, users.phone_number,
            booking_details.*, service_centres.name as service_centre_name,
            service_centres.primary_contact_name,service_centres.primary_contact_phone_1, service_centres.min_upcountry_distance
            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id` WHERE
    		`booking_id` NOT LIKE 'Q-%' $where AND
            (booking_details.current_status='Pending' OR booking_details.current_status='Rescheduled') order by STR_TO_DATE(`booking_details`.booking_date,'%d-%m-%Y') desc $add_limit"
        );

       // echo $this->db->last_query();
        return $query->result();

    }


    /**
     * @desc : This funtion counts total number of completed or cancelled bookings
     *
     * Also matches users id from users and booking details table.
     *
     * @param : void
     * @return : total number of completed or cancelled bookings
     */
    public function total_closed_booking($status = "", $booking_id = "") {
        $where = "";

        if($booking_id != ""){
            $where =  "  booking_id = '$booking_id' AND ";
        }

        $query = $this->db->query("Select count(*) as count from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
            WHERE `booking_id` NOT LIKE '%Q-%' AND $where
            (booking_details.current_status = '$status')"
        );

        $count = $query->result_array();
        return $count[0]['count'];
    }

    /**
     * @desc : This funtion counts total number of pending queries.
     *
     * Also matches users id from users and booking details table.
     *
     * @param : booking id
     * @return : total number of pending queries
     */
    public function total_queries($status, $booking_id = "") {
        $where = "";

	if ($booking_id != "")
	    $where .= "AND `booking_details`.`booking_id` = '$booking_id'";

	$sql = "SELECT count(*) as count from booking_details
        JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
        JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
        LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id` = `service_centres`.`id`
        WHERE `booking_id` LIKE '%Q-%' $where
        AND (DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= 0 OR
                booking_details.booking_date='') AND `booking_details`.current_status='$status'";
	$query = $this->db->query($sql);
	$count = $query->result_array();


	return $count[0]['count'];
    }


    /**
     * @desc : This funtion counts total number of bookings for a particular user
     *
     * Counts the number of bookings with same user id.
     *
     * @param : user id
     * @return : total number of bookings for particular user
     */
    public function total_user_booking($user_id) {
        $this->db->where("user_id = '$user_id'");
        $result = $this->db->count_all_results("booking_details");
        return $result;
    }

    /**
     * @desc : This funtion gives all the cancellation reasons present for partner or vendor or admin panel
     *
     * @param : void
     * @return : all the cancellation reasons present
     */
    function cancelreason($where, $order_by = "") {
        $this->db->where($where);
        if(!empty($order_by)){
            $this->db->order_by($order_by,false);
        }
        $query = $this->db->get('booking_cancellation_reasons');
        return $query->result();
    }

    /**
     * @desc : This funtion is to count number of bookings for a particular user.
     *
     * Searches and counts bookings having same user id.
     *
     * @param : user id
     * @return : count of bookings for specific user in numbers
     */
    function getBookingCountByUser($user_id) {
        $this->db->where("user_id", $user_id);
        $this->db->from("booking_details");

        //$query = $this->db->get();
        $result = $this->db->count_all_results();

        return $result;
    }

    /**
     * @desc : This funtion gives the name of the service
     *
     * Finds out the name of the service for a particular service id
     *
     * @param : service id
     * @return : service name
     */
    function service_name($service_id) {
        $sql = "Select services from services where id='$service_id'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

     /**
     * @desc: get booking history
     * @param: booking id, flag to make join with service center table
     * @return : array
     */
    function getbooking_history($booking_id, $join=""){

        $service_centre = "";
        $condition ="";
        $service_center_name ="";
        $partner = "";  
        $partner_name = "";
        if($join !=""){
            $service_center_name = ",service_centres.name as vendor_name, service_centres.min_upcountry_distance, service_centres.district as sc_district,service_centres.address, service_centres.state as sf_state, service_centres.pincode, service_centres.district as sf_district,"
		. "service_centres.primary_contact_name, service_centres.owner_email,service_centres.owner_name, gst_no, "
		. "service_centres.primary_contact_phone_1,service_centres.primary_contact_phone_2, "
                    . "service_centres.primary_contact_email,service_centres.owner_phone_1, "
                    . "service_centres.phone_1, service_centres.min_upcountry_distance as municipal_limit, isEngineerApp ";
	        $service_centre = ", service_centres ";
            $condition = " and booking_details.assigned_vendor_id =  service_centres.id";
            $partner_name = ", partners.public_name, partners.account_manager_id ";
            $partner = ", partners  ";
            $condition .= " and booking_details.partner_id =  partners.id";
        }

        $sql = " SELECT `services`.`services`, users.*, booking_details.* ".  $service_center_name. $partner_name
               . "from booking_details, users, services " . $service_centre .$partner
               . "where booking_details.booking_id='$booking_id' and "
               . "booking_details.user_id = users.user_id and "
               . "services.id = booking_details.service_id  ". $condition;

        $query = $this->db->query($sql);
        $result = $query->result_array();
        $post['is_inventory']=1;
        $post['is_original_inventory']=1;
        $post['spare_cancel_reason']=1;
        $query1 = $this->partner_model->get_spare_parts_by_any('spare_parts_details.*,inventory_master_list.part_number,inventory_master_list.part_name as final_spare_parts,im.part_number as shipped_part_number,original_im.part_name as original_parts,original_im.part_number as original_parts_number, booking_cancellation_reasons.reason as part_cancel_reason,spare_consumption_status.consumed_status, spare_consumption_status.is_consumed', array('booking_id' => $booking_id),false,false,false,$post);//, symptom_spare_request.spare_request_symptom
        if(!empty($query1)){
            $result1 = $query1;
            $result['spare_parts'] = $result1;
        }
        return $result;
    }

    function getbooking_filter_service_center($booking_id){

        $this->db->select('assigned_vendor_id');
        $this->db->where('assigned_vendor_id is NOT NULL', NULL, true);
        $this->db->where('booking_id', $booking_id);
        $query = $this->db->get('booking_details');

        if($query->num_rows > 0){
            // NOT NUll
            $data = $this->getbooking_history($booking_id, "Join");
                         
            log_message('info', __METHOD__ . $this->db->last_query());
            return $data;

        } else {
            //NUll
            $sql = " SELECT `services`.`services`, users.*, booking_details.*, partners.public_name "
               . "from booking_details, users, services ,partners "
               . "where booking_details.booking_id='$booking_id' and "
               . "booking_details.user_id = users.user_id and "
               . "services.id = booking_details.service_id  "
               . "and booking_details.partner_id =  partners.id";

        $query = $this->db->query($sql);
       // log_message('info', __METHOD__ . $this->db->last_query());

        return $query->result_array();
        }

    }


    function getbooking_history_by_appliance_id($appliance_id){

        $sql = " SELECT `services`.`services`, users.*, `booking_unit_details`.service_id, `booking_details`.* "
               . "from booking_unit_details, booking_details, users, services "
               . "where booking_unit_details.appliance_id='$appliance_id' and "
               . " booking_details.booking_id = booking_unit_details.booking_id and "
               . "booking_details.user_id = users.user_id and "
               . "services.id = booking_details.service_id  ";

        $query = $this->db->query($sql);

        return $query->result_array();
    }

    /**
     *  @desc : function to get brand's list available for a particular service
     *  @param : service_id
     *  @return : list of all brands for that service
     */
    function getBrandForService($service_id) {
        $this->db->where(array('service_id' => $service_id, 'seo' => 1));
        $this->db->select('brand_name');
	    $this->db->order_by('brand_name');
	    $query = $this->db->get('appliance_brands');
        return $query->result_array();
    }

    function getCategoryForService($service_id, $partner_id, $brand ="") {

        if($brand != ""){
          
           $this->db->where('brand',$brand);

        }
        $this->db->distinct();
        $this->db->select('category');
        $this->db->where('service_id',$service_id);
        $this->db->where('active','1');
        $this->db->where('check_box','1');
        $this->db->where('partner_id',$partner_id);
        $this->db->order_by('category', 'asc');
        $query = $this->db->get('service_centre_charges');
        
    	return $query->result_array();
    }

    function getCapacityForCategory($service_id, $category, $brand, $partner_id) {
       
        if($brand !=""){
            $this->db->where('brand', $brand);
        }
        
        $this->db->distinct();
        $this->db->select('capacity');
        $this->db->where('service_id', $service_id);
        $this->db->where('category', $category);
        $this->db->where('active', '1');
        $this->db->where('check_box', '1');
        $this->db->where('partner_id', $partner_id);
        $this->db->order_by('capacity', 'asc');
        $query = $this->db->get('service_centre_charges');
      
    	return $query->result_array();
    }

    function getCapacityForAppliance($service_id) {
	//echo $category;
	$sql = "Select distinct capacity from service_centre_charges where service_id='$service_id' and active='1' order by capacity asc";

	$query = $this->db->query($sql);

	return $query->result_array();
    }
    /*
     * @desc: This method return Price details. It filters according to service id, category, capacity, partner id
     */
    function getPricesForCategoryCapacity($service_id, $category, $capacity, $partner_id, $brand, $add_booking = NULL) {
        $this->db->distinct();
        $this->db->select('id,service_category,customer_total, partner_net_payable, customer_net_payable, pod, is_upcountry, '
                . 'vendor_basic_percentage, '
                . 'around_net_payable,product_or_services,  upcountry_customer_price, upcountry_vendor_price, upcountry_partner_price, flat_upcountry ');
        $this->db->where('service_id',$service_id);
        $this->db->where('category', $category);
        $this->db->where('active', 1);
        $this->db->where('check_box', 1);
        $this->db->where('partner_id', $partner_id);
        if($add_booking){
            $where['service_category != "'.REPEAT_BOOKING_TAG.'"'] = NULL;
            $this->db->where($where);
        }
        //if($brand !=""){
            $this->db->where('brand', $brand);
        //}

    	if (!empty($capacity)) {
    		$this->db->where('capacity', $capacity);
    	}
        
        $this->db->order_by('service_category', 'asc');

    	$query = $this->db->get('service_centre_charges');

    	return $query->result_array();
    }

    /**
     *  @desc : Function to get pending and reschedule bookings where vendor is not assigned
     *
     *  This function gives those bookings in which still no vendor is assigned to complete the booking,
     *      so that we can assigh vendor to these bookings as well.
     *
     *  @param : void
     *  @return : list of bookings with unassigned vendors
     */
    //TODO: can be removed
    function pendingbookings() {
        $sql = "Select services.services, "
                . "users.name, users.phone_number,"
                . "booking_details.* "
                . "from booking_details, users, services "
                . "where booking_details.user_id = users.user_id and "
                . "services.id = booking_details.service_id and "
                . "current_status IN ('Pending', 'Rescheduled') and "
                . "assigned_vendor_id is NULL AND upcountry_partner_approved = '1' ";
        $query = $this->db->query($sql);

        $temp = $query->result_array();

        usort($temp, array($this, 'date_compare_assign_pending_bookings'));

        //return sorted array
        return $temp;
    }

    /**
     *  @desc : Function to update booking details
     *  @param : booking id and booking details to update
     *  @return : void
     */
    
    function update_booking($booking_id, $data) {
        if(!empty($booking_id) || $booking_id != '0'){
            $this->db->where(array("booking_id" => $booking_id));
            $result =  $this->db->update("booking_details", $data);

            log_message ('info', __METHOD__ . "=> Booking  SQL ". $this->db->last_query());

            return $result;
        } else {
            return false;
        }
        
    }

    function update_booking_by_order_id($order_id, $data) {
        $this->db->where(array("order_id" => $order_id, "current_status" => "FollowUp"));
        $result =  $this->db->update("booking_details", $data);

        log_message ('info', __METHOD__ . "=> Booking SQL ". $this->db->last_query() . ", Result: " . $result);

        //return corresponding booking_id if booking gets updated else return false
        if ($this->db->affected_rows() > 0) {
            $this->db->select('booking_id');
            $this->db->where('order_id', $order_id);
            $query = $this->db->get('booking_details');
            return $query->result_array()[0]['booking_id'];
        } else {
            return FALSE;
        }
    }

    /**
     *  @desc : Function to update booking(vendor's) details
     *
     *  This function updates the rating and comments given to the vendors for the quality
     *      of service he provided.
     *
     *  @param : booking id and vendor's details to update
     *  @return : true if update done else false
     */
    function vendor_rating($booking_id, $data) {
        $sql = "UPDATE booking_details set vendor_rating_stars='$data[vendor_rating_star]',"
                . "vendor_rating_comments='$data[vendor_rating_comments]' where booking_id='$booking_id'";
        $query = $this->db->query($sql);
        return $query;
    }

    /**
     *  @desc : Function to get unit booking details
     *
     *  This function gives all the unit details of a particular booking from booking_unit_details.
     *
     *  @param : booking id
     *  @return : all the unit booking details
     */
     function get_unit_details($where, $like= FALSE, $select = "*") {
        $this->db->select($select);
        if($like == TRUE){
            $this->db->like($where);
        } else {
            $this->db->where($where);
        }
        
        $query = $this->db->get('booking_unit_details');
       // log_message('info', __METHOD__ . " SQL" . $this->db->last_query());

        return $query->result_array();
    }

    /**
     *  @desc : This function is to get appliance count of a user
     *
     *  Gives the number of appliances that are active for a particular user.
     *
     *  @param : user id
     *  @return : count of active appliances for a particular user
     */
    function getApplianceCountByUser($user_id) {
        log_message('info', __METHOD__ . "=> User ID: " . $user_id);

        $this->db->where(array('user_id' => $user_id, 'is_active' => '1'));
        $this->db->from("appliance_details");

        $result = $this->db->count_all_results();
        return $result;
    }

    /**
     *  @desc : This function is to add sample appliances for a new user
     *
     *  Sample appliances are added in user's appliance details once a user get registered with us.
     *
     *  Five(5) sample appliances are added to user's appliance details for our
     *      main 5 services.(1 for each service)
     *
     *  @param : user id and count of appliances
     *  @return : void
     */
    function addSampleAppliances($user_id, $count) {
        log_message('info', "Entering: " . __METHOD__);

        $sql1 = "SELECT * FROM sample_appliances";
        $query = $this->db->query($sql1);

        $appl = $query->result_array();
        for ($i = 0; $i < $count; $i++) {
            $appl[$i]['user_id'] = $user_id;
            //log_message('info', "Sample Appl: " . print_r($appl, TRUE));

            $sql2 = "INSERT INTO appliance_details "
                    . "(`service_id`, `brand`, `category`, `capacity`, "
                    . "`model_number`, `tag`, `purchase_date`, `rating`, `user_id`)"
                    . "VALUES (?,?,?,?,?, ?,?,?,?)";

            $this->db->query($sql2, $appl[$i]);
        }
    }
    
    /**
     *  @desc : This function is used to check brand and service id already added or not
     *
     *  @param : service id , brand name
     *  @return : array (service)
     */
    function check_brand_exists($service_id, $newbrand){
        $this->db->select('*');
        $this->db->where(array('service_id'=>$service_id,'brand_name'=>$newbrand));
        $query=$this->db->get('appliance_brands');
        //$sql = "select * from appliance_brands where service_id='".$service_id."' and brand_name = '".$newbrand."'";
        return $query->result_array();
    }

    /**
     *  @desc : This function finds all the details for the particular service name/
     *      get all service from database(mentioned earliar)
     *
     *  @param : service name
     *  @return : array (service)
     */
    function getServiceId($service_name) {
        $this->db->select('*');
        $this->db->where('services', $service_name);
        $query = $this->db->get('services');

        if($query->num_rows > 0){
            $services = $query->result_array();
            return $services[0]['id'];
        } else {
            return false;
        }

    }

    /**
     *  @desc : This function selects specific source for the given source code
     *
     *  @param : source code
     *  @return : source name of the code
     */
    function get_booking_source($source_code) {
        $this->db->select('source,partner_type,partner_id ');
        $this->db->where('code',$source_code);
        $query = $this->db->get('bookings_sources');
        return $query->result_array();
    }

    /**
     *  @desc : This function is to check whether order id exists for a specific
     * partner. Order id is unique for each partner and is tied to a unique
     * booking id.
     *
     *  @param : String	$partner_id Partner ID
     *  @param : String	$order_id Order ID
     *
     *  @return : If exists, returns booking details specific to this order id else false
     */
    function check_order_id_exists($partner_id, $order_id) {
	$this->db->where(array("partner_id" => $partner_id, "order_id" => $order_id));
	$query = $this->db->get('booking_details');

	if (count($query->result_array()) > 0) {
	    return $query->result_array()[0];
	} else {
	    return FALSE;
	}
    }

    /**
     *  @desc : Save(Insert) mail sent to vendor regarding bookings
     *  @param : Email details
     *  @return : void
     */
    function save_vendor_email($details) {
        $this->db->insert('vendor_mail_details', $details);
    }

    /**
     *  @desc : Get all mails sent to vendor regarding specific booking
     *  @param : booking_id
     *  @return : array(mail details)
     */
    function get_vendor_mails($booking_id) {
        $sql = "SELECT * from vendor_mail_details where booking_id='$booking_id'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     *  @desc : Get last mail's details sent to vendor regarding specific booking
     *  @param : booking_id
     *  @return : array(last email details)
     */
    function get_last_vendor_mail($booking_id) {
        $sql = "SELECT * FROM vendor_mail_details WHERE booking_id = '$booking_id' order by create_date DESC LIMIT 1";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * @desc : In this function, we will pass Array and search active pincode and vendor.
     * If pincode available then insert vendor name in the same key.
     * @param : Array
     * @return : Array
     */
    function searchPincodeAvailable($temp, $pv) {

        foreach ($temp as $key => $value) {

            $this->db->distinct();
            $this->db->select('count(Vendor_ID) as count');
            $this->db->where('vendor_pincode_mapping.Appliance_ID', $value->service_id);
            $this->db->where('vendor_pincode_mapping.Pincode', $value->booking_pincode);
            $this->db->from('vendor_pincode_mapping');

            $this->db->join('service_centres', 'service_centres.id = vendor_pincode_mapping.Vendor_ID');

            $this->db->where('service_centres.active', "1");
            $data = $this->db->get();

            $count = $data->result_array()[0]['count'];
            if ($count > 0) {
                if($pv == PINCODE_AVAILABLE){
                    $temp[$key]->vendor_status ="Vendor Available";
                } else if($pv == PINCODE_NOT_AVAILABLE) {
                    unset($temp[$key]);
                } else if($pv == PINCODE_ALL_AVAILABLE){
                     $temp[$key]->vendor_status = "Vendor Available";
                }

            } else {
                if($pv == PINCODE_AVAILABLE){
                    unset($temp[$key]);
                } else if($pv == PINCODE_NOT_AVAILABLE) {
                    $temp[$key]->vendor_status = "Vendor Not Available";
                } else if($pv == PINCODE_ALL_AVAILABLE){
                    $temp[$key]->vendor_status = "Vendor Not Available";
                }

            }
        }

        return $temp;
    }


    /**
     *  @desc : Function to get distinct area/pincode/city/state
     *  @param : String place type
     *  @return : Array Distinct places
     */
    function get_distinct_place($place_type) {
        $this->db->distinct($place_type);
        $this->db->group_by($place_type);
        $query = $this->db->get('vendor_pincode_mapping');

        //log_message('info', __METHOD__ . '=> Last query: ' . $this->db->last_query());

        return $query->result_array();
    }

    function search_bookings($where, $partner_id = "") {
    // Need to get brand to send to vendor pincode mapping add form, So we will use join with booking_unit_details
    $this->db->distinct();
    $this->db->select("services.services, users.name as customername,
            users.phone_number, booking_details.*,penalty_on_booking.active as penalty_active");
    $this->db->from('booking_details');
    $this->db->join('users',' users.user_id = booking_details.user_id');
    $this->db->join('services', 'services.id = booking_details.service_id');
    $this->db->join('penalty_on_booking', "booking_details.booking_id = penalty_on_booking.booking_id and penalty_on_booking.active = '1'",'left');
    if($partner_id !=""){
        $this->db->join('booking_unit_details', 'booking_unit_details.booking_id = booking_details.booking_id');
        $this->db->where('booking_details.partner_id', $partner_id);
    }
    $this->db->where($where);
    $query =  $this->db->get();
    $temp = $query->result();
    for ($i=0; $i < count($temp) ; $i++) {
       if(!empty($temp[$i]->assigned_vendor_id)){
           $this->db->select('service_centres.name as service_centre_name,
            service_centres.primary_contact_name,service_centres.primary_contact_phone_1 ');
           $this->db->where('id', $temp[$i]->assigned_vendor_id);
           $query1 = $this->db->get('service_centres');
           $result = $query1->result_array();

           $temp[$i]->service_centre_name =  $result[0]['service_centre_name'];
           $temp[$i]->primary_contact_name = $result[0]['primary_contact_name'];
           $temp[$i]->primary_contact_phone_1 = $result[0]['primary_contact_phone_1'];

    }
    }


    usort($temp, array($this, 'date_compare_queries'));
    if($query->num_rows>0){

        if (strpos($temp[0]->booking_id, "Q-") !== FALSE) {

            $data = $this->searchPincodeAvailable($temp, PINCODE_ALL_AVAILABLE);
            return $data;
        }
    }

        return  $temp;
    }

    /**
     *  @desc : This function is to get internal status from database
     *  @param : void
     *  @return : all internal status present in database
     */
    function get_internal_status($where, $is_array= false) {
        $this->db->select('*');
        $this->db->where($where);
        $query = $this->db->get('internal_status');
        
        if($query->num_rows > 0){
            if($is_array){
                return $query->result_array();
            } else {
                return $query->result();   
            }
            
        } else {
            return FALSE;
        }
    }

    /**
     *  @desc : Find potential SCs for an Appliance in a Pincode
     *  @param : appliance id and pincode
     *  @return : returns available service centers
     */
    function find_sc_by_pincode_and_appliance($appliance, $pincode) {
        $query = $this->db->query("SELECT DISTINCT(`service_centres`.`id`),'SF_EXIST' AS sf_status, service_centres.name FROM (`vendor_pincode_mapping`)
	    JOIN `service_centres` ON `service_centres`.`id` = `vendor_pincode_mapping`.`Vendor_ID`
    		WHERE `Appliance_ID` = '$appliance' AND `vendor_pincode_mapping`.`Pincode` = '$pincode'
	    AND `service_centres`.`active` = '1'
            AND `service_centres`.`on_off` = '1'");

        $service_centre_ids = $query->result_array();

       // $service_centres = array();

        if (count($service_centre_ids) > 0) {

            return $service_centre_ids;
        } else {
            $sql =  " SELECT id, name, 'SF_NOT_EXIST' AS sf_status FROM service_centres where active = 1 ";
            //No service centre found, return all SCs as of now
            $query2 = $this->db->query($sql);
            return $query2->result_array();
        }

    }

    /**
     * @desc: this function is used to get services charges to be filled by service centers
     * @param: booking id
     * @return: Array()
     */
    function getbooking_charges($booking_id = "", $status="") {

	if ($booking_id != "") {
	    $this->db->where('booking_id', $booking_id);
	}

	//Status should NOT be Completed or Cancelled
        if($status !=""){
            $this->db->where_not_in('current_status', $status);
        }

        $this->db->where_not_in('internal_status', "Reschedule");
	$query = $this->db->get('service_center_booking_action');

	log_message('info', __METHOD__ . "=> " . $this->db->last_query());

	return $query->result_array();
    }

    /**
     * @desc: this function is used to get bookings to review the details(charges) filled by service centers
     *
     * Through this the charges collected of particular booking by vendor is displayed.
     *
     * @param: void
     * @return: Array of charges
     */
    function get_booking_for_review($booking_id,$status,$whereIN,$is_partner,$offset = NULL, $perPage = NULL,$having_arr=array()) {
        $charges = $this->service_centers_model->getcharges_filled_by_service_center($booking_id,$status,$whereIN,$is_partner,$offset,$perPage,$having_arr);
        foreach ($charges as $key => $value) {
           // $charges[$key]['service_centres'] = $this->vendor_model->getVendor($value['booking_id']);
            $charges[$key]['booking'] = $this->getbooking_history($value['booking_id'], "join");
        }
        return $charges;
    }
    
    
    function get_partner_review_booking($where,$booking_id = NULL){
         $bookingSubQuery = "";
        if ($booking_id != "") {
            $bookingSubQuery = " AND service_center_booking_action.booking_id ='".$booking_id."'";
        }
        if($where){
            $whereString = implode(' OR ',$where);
        }
        $sql = "SELECT DISTINCT service_center_booking_action.booking_id "
                . "FROM service_center_booking_action JOIN booking_details ON booking_details.booking_id = service_center_booking_action.booking_id "
                . "WHERE (".$whereString.")".$bookingSubQuery. " AND service_center_booking_action.current_status = 'InProcess'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * @desc: this function is used insert the logs for outbound calls.
     *
     * Through this the customers id, name and agents name is inserted.
     *
     * @param: customer and agent details.
     * @return: void
     */
    function insert_outbound_call_log($details) {
        $this->db->insert('agent_outbound_call_log', $details);
    }

    /**
     * @desc: This function is used to get booking email tempalate.
     *
     * Through this the email tag is user to get its template from database.
     *
     * With this also the enail to and from field is also selected.
     *
     * @param: email tag
     * @return: Array of email template
     */
    function get_booking_email_template($email_tag) {
        $this->db->select("*");
        $this->db->where('tag', $email_tag);
        $this->db->where('active', 1);
        $query = $this->db->get('email_template');
        if ($query->num_rows > 0) {
            $template = $query->result_array();
            return array($template[0]['template'], $template[0]['to'], $template[0]['from'],$template[0]['cc'],$template[0]['subject'], $template[0]['bcc']);
        } else {
            return "";
        }
    }

    /**
     * @desc: this is used to display reschedule request by service center in admin panel
     * @param: void
     * @return: void
     */
    function review_reschedule_bookings_request($whereIN=array(), $where=array(), $join=array()){
        $this->db->select('distinct(service_center_booking_action.booking_id),assigned_vendor_id, amount_due, count_reschedule, initial_booking_date, booking_details.is_upcountry,'
                . 'users.name as customername, booking_details.booking_primary_contact_no, services.services, booking_details.booking_date, booking_details.booking_timeslot, '
                . 'service_center_booking_action.booking_date as reschedule_date_request,  service_center_booking_action.booking_timeslot as reschedule_timeslot_request, '
                . 'service_centres.name as service_center_name, booking_details.quantity, service_center_booking_action.reschedule_reason,service_center_booking_action.reschedule_request_date,'
                . 'booking_details.partner_id, booking_details.flat_upcountry');
        $this->db->from('service_center_booking_action');
        $this->db->join('booking_details','booking_details.booking_id = service_center_booking_action.booking_id');
        $this->db->join('services','services.id = booking_details.service_id');
        $this->db->join('users','users.user_id = booking_details.user_id');
        $this->db->join('service_centres','service_centres.id = booking_details.assigned_vendor_id');
        $this->db->where('service_center_booking_action.internal_status', "Reschedule");
        if(!empty($where)){
            $this->db->where($where);
        }
        if(!empty($whereIN)){
             foreach ($whereIN as $fieldName=>$conditionArray){
                     $this->db->where_in($fieldName, $conditionArray);
             }
        }
        if (!empty($join)) {
            foreach($join as $key=>$values){
                $this->db->join($key, $values);
            }
        }
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

     //Find order id for a partner
    function get_booking_from_order_id($partner_id, $order_id) {
        $this->db->select('*');
        $this->db->where('partner_id', $partner_id);
        $this->db->like('order_id' , $order_id);

        $query = $this->db->get("booking_details");

         $temp = $query->result();

        usort($temp, array($this, 'date_compare_queries'));

        if (strpos($temp[0]->booking_id, "Q-") !== FALSE) {

            $data = $this->searchPincodeAvailable($temp);
            return $data;
        }


    }

    function check_price_tags_status($booking_id, $unit_id_array,$inventory_details){
        
        $this->db->select('id, price_tags,appliance_capacity');
        $this->db->like('booking_id', $booking_id);
        $this->db->where_not_in('id', $unit_id_array);
        $query = $this->db->get('booking_unit_details');
        if($query->num_rows>0){
            $result = $query->result_array();
            foreach ($result as $value) {
                $this->db->where('id', $value['id']);
                $this->db->delete('booking_unit_details');
                //process inventory stock for each unit if price tag is wall mount
                if ($value['price_tags'] == _247AROUND_WALL_MOUNT__PRICE_TAG && !empty($inventory_details)) {
                    $match = array();
                    //get the size from the capacity to know the part number
                    preg_match('/[0-9]+/', $value['appliance_capacity'], $match);
                    if (!empty($match)) {
                        if ($match[0] <= 32) {
                            $data['part_number'] = LESS_THAN_32_BRACKETS_PART_NUMBER;
                        } else if ($match[0] > 32) {
                            $data['part_number'] = GREATER_THAN_32_BRACKETS_PART_NUMBER;
                        }
                        
                        $data['receiver_entity_id'] = $inventory_details['receiver_entity_id'];
                        $data['receiver_entity_type'] = $inventory_details['receiver_entity_type'];
                        $data['stock'] = $inventory_details['stock'];
                        $data['booking_id'] = $booking_id;
                        $data['agent_id'] = $inventory_details['agent_id'];
                        $data['agent_type'] = $inventory_details['agent_type'];
                        
                        $this->miscelleneous->process_inventory_stocks($data);
                    }
                }
            }
        }
       
        return;
    }

    function getpricesdetails_with_tax($service_centre_charges_id, $state){

        $sql =" SELECT service_category as price_tags,pod,invoice_pod,vendor_basic_percentage, customer_total, partner_net_payable, product_or_services  from service_centre_charges where `service_centre_charges`.id = '$service_centre_charges_id' ";

        $query = $this->db->query($sql);
        $result =  $query->result_array();

        $result[0]['tax_rate'] = DEFAULT_TAX_RATE;
        $result['DEFAULT_TAX_RATE'] = 0;
        

        return $result;


    }

    /**
     * @desc: This method get prices details and check price tag is exist in unit details or not.
     * If price tags does not exist, it inserts data in booking unit details and if price tags exist,
     * it update booking unit details.
     * @param: Array
     * @return: Price tags.
     */
    function update_booking_in_booking_details($services_details, $booking_id, $state, $update_key,$agent_details){
        
        $data = $this->getpricesdetails_with_tax($services_details['id'], $state);

        $this->db->select('id, customer_total, price_tags, vendor_basic_percentage');
        $this->db->where('appliance_id', $services_details['appliance_id']);
        $this->db->where('price_tags', $data[0]['price_tags']);
        $this->db->like('booking_id', preg_replace("/[^0-9]/","",$booking_id));
        $query = $this->db->get('booking_unit_details');
        $unit_details = $query->result_array();
        $result = array_merge($data[0], $services_details);
        if($data[0]['price_tags']  == REPAIR_OOW_PARTS_PRICE_TAGS){
            if(!empty($unit_details) && $unit_details[0]['price_tags'] == REPAIR_OOW_PARTS_PRICE_TAGS){
                $result['customer_total'] = $unit_details[0]['customer_total'];
                $result['vendor_basic_percentage'] = $unit_details[0]['vendor_basic_percentage'];
            }
        }

        unset($result['id']);  // unset service center charge  id  because there is no need to insert id in the booking unit details table
        $result['customer_net_payable'] = $result['customer_total'] - $result['partner_paid_basic_charges'] - $result['around_paid_basic_charges'];
        $result['partner_paid_tax'] = ($result['partner_paid_basic_charges'] * $result['tax_rate'])/ 100;
        

        $vendor_total_basic_charges =  ($result['customer_net_payable'] + $result['partner_paid_basic_charges'] + $result['around_paid_basic_charges'] ) * ($result['vendor_basic_percentage']/100);
        $result['partner_paid_basic_charges'] = $result['partner_paid_basic_charges'] + $result['partner_paid_tax'];
        $around_total_basic_charges = ($result['customer_net_payable'] + $result['partner_paid_basic_charges'] + $result['around_paid_basic_charges'] - $vendor_total_basic_charges);
         
        $result['around_st_or_vat_basic_charges'] = $this->get_calculated_tax_charge($around_total_basic_charges, $result['tax_rate'] );
        $result['vendor_st_or_vat_basic_charges'] = $this->get_calculated_tax_charge($vendor_total_basic_charges, $result['tax_rate'] );
        
        $result['around_comm_basic_charges'] = $around_total_basic_charges - $result['around_st_or_vat_basic_charges'];
        $result['vendor_basic_charges'] = $vendor_total_basic_charges - $result['vendor_st_or_vat_basic_charges'];
       
        log_message('info', __METHOD__ . " update booking_unit_details data " . print_r($result, true) . " Price data with tax: " . print_r($data, true));
        
        if ($query->num_rows > 0) {
            //if found, update this entry
            
            log_message('info', __METHOD__ . " update booking_unit_details ID: " . print_r($unit_details[0]['id'], true));
            $this->db->where('id', $unit_details[0]['id']);
            $this->db->update('booking_unit_details', $result);
            $u_unit_id = $unit_details[0]['id'];
            
        } else {
            //trim booking only digit
            $trimed_booking_id = preg_replace("/[^0-9]/","",$booking_id);
            $unit_where = array('booking_id' => $trimed_booking_id);
            $unit_num = $this->get_unit_details($unit_where, TRUE);
   
            log_message('info', __METHOD__ . " count previous unit: " . count($unit_num));
            log_message('info', __METHOD__ . " Price tags not found ");
            if (!empty($unit_num)) {

                if (count($unit_num) > 1) {

                    $this->db->insert('booking_unit_details', $result);
                    $u_unit_id = $this->db->insert_id();
                    log_message('info', __METHOD__ . " Insert New Unit details SQL" . $this->db->last_query());
                    //process inventory stock for each unit if price tag is wall mount
                    if($result['price_tags'] == _247AROUND_WALL_MOUNT__PRICE_TAG){
                        $this->process_inventory($result,$agent_details);
                    }
                    
                } else {
                    //$this->db->where('booking_id',  $booking_id);
                    if (empty($unit_num[0]['price_tags'])) {
                        $this->db->where('id', $unit_num[0]['id']);
                        $this->db->update('booking_unit_details', $result);
                        $u_unit_id = $unit_num[0]['id'];
                        log_message('info', __METHOD__ . " Update Unit details SQL" . $this->db->last_query());
                    } else {
                        $this->db->insert('booking_unit_details', $result);
                        $u_unit_id = $this->db->insert_id();
                        log_message('info', __METHOD__ . " Insert New Unit details SQL" . $this->db->last_query());
                        //process inventory stock for each unit if price tag is wall mount
                        if($result['price_tags'] == _247AROUND_WALL_MOUNT__PRICE_TAG){
                            $this->process_inventory($result,$agent_details);
                        }
                    }
                }
            } else {
                $this->db->insert('booking_unit_details', $result);
                $u_unit_id = $this->db->insert_id();
                log_message('info', __METHOD__ . " Insert New Unit details SQL" . $this->db->last_query());
                //process inventory stock for each unit if price tag is wall mount
                if($result['price_tags'] == _247AROUND_WALL_MOUNT__PRICE_TAG){
                    $this->process_inventory($result,$agent_details);
                }
            }
        }
        $return_details['unit_id'] = $u_unit_id;
        $return_details['DEFAULT_TAX_RATE'] = $data['DEFAULT_TAX_RATE'];
        $return_details['price_tags'] = $data[0]['price_tags'];

        return $return_details;
    }
    
    /**
     * @desc: calculate service charges and vat charges
     * @param : total charges and tax rate
     * @return calculate charges
     */
    function get_calculated_tax_charge($total_charges, $tax_rate){
          //52.50 = (402.50 / ((100 + 15)/100)) * ((15)/100)
          //52.50 =  (402.50 / 1.15) * (0.15)
        $st_vat_charge = sprintf ("%.2f", ($total_charges / ((100 + $tax_rate)/100)) * (($tax_rate)/100));
        return $st_vat_charge;
    }
    
    /**
     * @desc: this method is used to get city, services, sources details
     * @param : user phone no.
     * @return : array()
     */
    function get_city_source(){
        $query1['city'] = $this->vendor_model->getDistrict_from_india_pincode();
        $query2['sources'] = $this->partner_model->getpartner_details("bookings_sources.partner_id,source,code, partner_type", array("is_active" => 1));
       
        return $query = array_merge($query1, $query2);

    }
    

     /**
     * @desc: this is used to copy price and tax rate of custom service center id and insert into booking unit details table with
     * booking id and details.
     * @param: Array()
     * @return : Array()
     */
    function insert_data_in_booking_unit_details($services_details, $state, $update_key) {
	log_message('info', __FUNCTION__);
	$data = $this->getpricesdetails_with_tax($services_details['id'], $state);
        
        $result = array_merge($data[0], $services_details);
        unset($result['id']);  // unset service center charge  id  because there is no need to insert id in the booking unit details table
        
        return $this->_insert_data_in_booking_unit_details($result, $update_key, $data['DEFAULT_TAX_RATE']);
        
    }
    
    function _insert_data_in_booking_unit_details($result, $update_key, $default_tax_rate_flag){
        $result['customer_net_payable'] = $result['customer_total'] - $result['partner_paid_basic_charges'] - $result['around_paid_basic_charges'];
        $result['partner_paid_tax'] = ($result['partner_paid_basic_charges'] * $result['tax_rate'])/ 100;

        $vendor_total_basic_charges =  ($result['customer_net_payable'] + $result['partner_paid_basic_charges'] + $result['around_paid_basic_charges'] ) * ($result['vendor_basic_percentage']/100);
        $result['partner_paid_basic_charges'] = $result['partner_paid_basic_charges'] + $result['partner_paid_tax'];
        $around_total_basic_charges = ($result['customer_net_payable'] + $result['partner_paid_basic_charges'] + $result['around_paid_basic_charges'] - $vendor_total_basic_charges);
         
        $result['around_st_or_vat_basic_charges'] = $this->get_calculated_tax_charge($around_total_basic_charges, $result['tax_rate'] );
        $result['vendor_st_or_vat_basic_charges'] = $this->get_calculated_tax_charge($vendor_total_basic_charges, $result['tax_rate'] );
        
        $result['around_comm_basic_charges'] = $around_total_basic_charges - $result['around_st_or_vat_basic_charges'];
        $result['vendor_basic_charges'] = $vendor_total_basic_charges - $result['vendor_st_or_vat_basic_charges'];
          
     
//        log_message('info', __METHOD__ . " Insert booking_unit_details data" . print_r($result, true));
    $this->db->insert('booking_unit_details', $result);
        $result['unit_id'] = $this->db->insert_id();
         
        $result['DEFAULT_TAX_RATE'] = $default_tax_rate_flag;
        return $result;
    }
    function get_old_request_type($booking_id){
        $where['booking_id'] = $booking_id;
        $this->db->select('request_type');
        $this->db->from('booking_details');
        $this->db->where($where);
        $query = $this->db->get();
        $requesstDataArray = $query->result_array();
        $requestType = NULL;
        if(!empty($requesstDataArray)){
            $requestType =  $requesstDataArray[0]['request_type'];
        }
        return $requestType;
    }
    function update_request_type_history_table($booking_id,$oldRequestType,$oldPriceTag,$newRequest){
            log_message('info', __METHOD__ . " Booking ID " . $booking_id . "Old Price Tags " . print_r($oldPriceTag, true));
            $whereNewPrice['booking_id'] = $booking_id;
            $groupBY  = array('appliance_id');
            $newPriceTag = $this->reusable_model->get_search_result_data('booking_unit_details','appliance_id,GROUP_CONCAT(price_tags) as price_tag',$whereNewPrice,NULL,NULL,NULL,NULL,NULL,$groupBY);
            $finalOldPrice = array();
            foreach($newPriceTag as $values ){
                $finalNewPrice[$values['appliance_id']] = $values['price_tag'];
            }
            foreach($oldPriceTag as $values2 ){
                $finalOldPrice[$values2['appliance_id']] = $values2['price_tag'];
            }
            $shouldChange = false;
            foreach($finalOldPrice as $key => $values3){
                if(array_key_exists($key, $finalNewPrice)){
                    if($values3 != $finalNewPrice[$key]){
                        $shouldChange = true;
                    }
                }
               else{
                   $shouldChange = true;
                }
            }
             if($shouldChange){
                $data['booking_id'] = $booking_id;
                $data['old_request_type'] = $oldRequestType;
                $data['new_request_type'] = $newRequest;
                $data['new_price_tag'] = json_encode($finalNewPrice);
                $data['old_price_tag'] = json_encode($finalOldPrice);
                if(!empty($this->session->userdata('service_center_id'))){
                    $entity_type = "Service Center";
                    $entity_id = $this->session->userdata('service_center_id');
                    $agentID = $this->session->userdata('service_center_agent_id');
                    $entity_name = $this->session->userdata('service_center_name');
                    $agent_name = $this->session->userdata('agent_name');
                }
                else if(!empty($this->session->userdata('partner_id'))){
                    $entity_type = "Partner";
                    $entity_id = $this->session->userdata('partner_id');
                    $agentID = $this->session->userdata('agent_id');
                    $entity_name = $this->session->userdata('partner_name');
                    $agent_name = $this->session->userdata('emp_name');
                }
                else if(!empty($this->session->userdata('id'))){
                    $entity_type = "247around";
                    $entity_id = _247AROUND;
                    $agentID = $this->session->userdata('id');
                    $entity_name = "247around";
                    $agent_name = $this->session->userdata('employee_id');
                }
                else{
                    $entity_type = "247around";
                    $entity_id = _247AROUND;
                    $agentID = _247AROUND_DEFAULT_AGENT;
                    $entity_name = "247around";
                    $agent_name = "Default Agent";
                }
                $data['entity_type']  = $entity_type;
                $data['entity_id'] = $entity_id;
                $data['agent_id'] = $agentID;
                $data['entity_name'] = $entity_name;
                $data['agent_name'] = $agent_name;
                $this->reusable_model->insert_into_table('booking_request_type_state_change',$data);
                log_message('info', __METHOD__ . " Booking ID " . $booking_id . " Updated Data Array " . print_r($data, true));
            }
    }
    function update_request_type($booking_id, $price_tag,$oldPriceTag = array()) {
        log_message('info', __METHOD__ . " Booking ID " . $booking_id . " Price Tags " . print_r($price_tag, true));
        $oldRequestType =  $this->get_old_request_type($booking_id);
        if (!empty($price_tag)) {
            $results = array_filter($price_tag, function($value) {
                if ((stripos($value, 'Installation') !== false) || stripos($value, 'Repair') !== false) {
                    return $value; 
                    
                } else {

                    return false;
                }
            });

            if (!empty($results)) {
                $newRequest = array_values($results)[0];
            } else {
                $newRequest = $price_tag[0];
            }
            $this->update_booking($booking_id, array('request_type' => $newRequest));
        }
        $this->update_request_type_history_table($booking_id,$oldRequestType,$oldPriceTag,$newRequest);
    }

    /**
     * @desc: this method inser new line item while booking completion
     * @param: Unit id for copy appliance details into new line
     * @param: service charges id for cop prices nad tax
     * @param: Array, filled drom input form
     */
    function insert_new_unit_item($unit_id, $service_charge_id, $data, $state){
        $price_data = $this->getpricesdetails_with_tax($service_charge_id, $state);
        $this->db->select('booking_id,partner_id,sub_order_id, service_id,appliance_id,appliance_brand,appliance_category, appliance_capacity,appliance_size, serial_number, appliance_description, model_number, appliance_tag, purchase_date');

        $this->db->where('id', $unit_id);
        $query = $this->db->get('booking_unit_details');
        $unit_details = $query->result_array();
        if(!empty($data)){
            $result = array_merge($price_data[0], $unit_details[0]);
        } else {
            $result = $unit_details[0];
        }

        $result['booking_status'] = $data['booking_status'];
        $result['partner_paid_basic_charges'] = $result['partner_net_payable'];
        $result['around_paid_basic_charges'] = $result['around_net_payable'] = 0;

        
        // Added By Chhavi , While fixing Bug for customer net payable going 0
        $result['customer_net_payable'] = $result['customer_total'] - $result['partner_paid_basic_charges'] - $result['around_paid_basic_charges'];
        $result['partner_paid_tax'] = ($result['partner_paid_basic_charges'] * $result['tax_rate'])/ 100;
        $vendor_total_basic_charges =  ($result['customer_net_payable'] + $result['partner_paid_basic_charges'] + $result['around_paid_basic_charges'] ) * ($result['vendor_basic_percentage']/100);
        $result['partner_paid_basic_charges'] = $result['partner_paid_basic_charges'] + $result['partner_paid_tax'];
        $around_total_basic_charges = ($result['customer_net_payable'] + $result['partner_paid_basic_charges'] + $result['around_paid_basic_charges'] - $vendor_total_basic_charges);
        $result['around_st_or_vat_basic_charges'] = $this->get_calculated_tax_charge($around_total_basic_charges, $result['tax_rate'] );
        $result['vendor_st_or_vat_basic_charges'] = $this->get_calculated_tax_charge($vendor_total_basic_charges, $result['tax_rate'] );
        $result['around_comm_basic_charges'] = $around_total_basic_charges - $result['around_st_or_vat_basic_charges'];
        $result['vendor_basic_charges'] = $vendor_total_basic_charges - $result['vendor_st_or_vat_basic_charges'];
       // End 
       
        log_message('info', ": " . " insert new item in booking unit details data " . print_r($result, TRUE));

        $this->db->insert('booking_unit_details', $result);
        $new_unit_id = $this->db->insert_id();

        log_message('info', ": " . " insert new item in booking unit details returned id " . print_r($new_unit_id, TRUE));

        $data['id'] = $new_unit_id;
        if(!isset($data['ud_closed_date'])){
            $data['ud_closed_date'] = date("Y-m-d H:i:s");
        }
        
        log_message('info', ": " . " update booking unit details data " . print_r($data, TRUE));

        $this->update_unit_details($data);

        return $new_unit_id;
    }

    function get_brand($where, $select = "*"){
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get('appliance_brands');

        return $query->result_array();

    }

    /**
     * @desc: This function is used to insert Sent SMS to database
     * params: Array of data  to be inserted in sms_send_details
     * return: Int(ID) of inserted sms
     */
    function add_sms_sent_details($data){
        $this->db->insert('sms_sent_details', $data);
        return $this->db->insert_id();
    }
    /**
     * @desc: This is used to get Booking_state_change data
     * params: String Booking ID
     * return: Array of data
     *
     */
    function get_booking_state_change_by_id($booking_id){
        $bookingIDArray[] = $booking_id;
        if (strpos($booking_id, 'Q-') === false) {
            $bookingIDArray[] = "Q-".$booking_id;
        }
        $this->db->select('booking_state_change.agent_id,booking_state_change.partner_id,'
                . ' booking_state_change.service_center_id,booking_state_change.old_state,'
                . ' booking_state_change.new_state,booking_state_change.remarks,booking_state_change.create_date');
        $this->db->where_in('booking_state_change.booking_id', $bookingIDArray);
        $this->db->from('booking_state_change');
       
        $this->db->order_by('booking_state_change.id');
        $query = $this->db->get();
        $data =  $query->result_array();
        
        foreach ($data as $key => $value){
            if(!is_null($value['partner_id'])){
                // If Partner Id is 247001
                if($value['partner_id'] == _247AROUND){
                    $sql = "SELECT full_name, bookings_sources.source FROM employee, bookings_sources WHERE "
                            . " bookings_sources.partner_id = '"._247AROUND."' AND employee.id = '".$value['agent_id']."'";
                   
                    $query1 = $this->db->query($sql);
                    $data1 = $query1->result_array();
                   
                    $data[$key]['full_name'] = isset($data1[0]['full_name'])?$data1[0]['full_name']:'';
                    $data[$key]['source'] = isset($data1[0]['source'])?$data1[0]['source']:'';
                   
                    
                } else {
                    // For Partner
                    $data1 = $this->dealer_model->entity_login(array('agent_id' =>$value['agent_id']));
                   
                    $data[$key]['full_name'] = isset($data1[0]['agent_name'])?$data1[0]['agent_name']:'';
                    $data[$key]['source'] = isset($data1[0]['entity_name'])?$data1[0]['entity_name']:'';
                }
            } else if(!is_null($value['service_center_id'])){
                // For Service center
                if ($this->session->userdata('userType') == 'employee') {
                    $this->db->select("service_centers_login.full_name,service_centres.name as source ");
                 } else {
                     $this->db->select("CONCAT('Agent Id: ',service_centers_login.id ) As full_name , CONCAT('SF Id: ',service_centres.id ) As source");
                 }
                $this->db->from('service_centers_login');
                $this->db->where('service_centers_login.id', $value['agent_id']);
                $this->db->join('service_centres', 'service_centres.id = service_centers_login.service_center_id');
          
                $query1 = $this->db->get();
                $data1 = $query1->result_array();
                
                $data[$key]['full_name'] = isset($data1[0]['full_name'])?$data1[0]['full_name']:'';
                $data[$key]['source'] = isset($data1[0]['source'])?$data1[0]['source']:'';
            }
            
        }
       
        return $data;

    }
    /**
     * @desc: Get State Change  details
     * @param String $booking_id
     * @return boolean
     */
    function get_booking_state_change($booking_id){
        $trimed_booking_id = preg_replace("/[^0-9]/","",$booking_id);
        $this->db->like('booking_id',$trimed_booking_id);
        $this->db->order_by('booking_state_change.id');
        $query = $this->db->get('booking_state_change');

        if($query->num_rows){
            return $query->result_array();
        } else {
            return false;
        }
    }

    /**
     * @desc: This is used to get daily and monthly bookings completed and reports
     * params: void
     * return: array
     *
     */
    function get_completed_booking_details(){
        $where_rating = "where DATE_FORMAT(closed_date,'%m') = MONTH(CURDATE()) AND (rating_stars IS NOT NULL OR rating_stars !='')" ;
        $where_booking = "where DATE_FORMAT(closed_date,'%m') = MONTH(CURDATE()) AND current_status='"._247AROUND_COMPLETED."'";
        $where_booking_previous = "where DATE_FORMAT(closed_date,'%m') = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND current_status='"._247AROUND_COMPLETED."'";
        $sql_rating = "SELECT COUNT(id) as ratings from booking_details $where_rating";
        $sql_booking = "SELECT COUNT(id) as bookings from booking_details $where_booking";
        $sql_booking_previous = "SELECT COUNT(id) as bookings from booking_details $where_booking_previous";

        $data['ratings'] = $this->db->query($sql_rating)->row();
        $data['bookings'] = $this->db->query($sql_booking)->row();
        $data['bookings_previous'] = $this->db->query($sql_booking_previous)->row();
        return $data;
    }
    
    
    /**
     * @desc: This fuction is used to delete particular email template from 247around_email_template table
     * params: Int id of the mail template
     * return : Boolean
     * 
     */
    function delete_mail_template_by_id($id) {
        $this->db->where('id', $id);
        $this->db->delete('247around_email_template');

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @desc : This funtion is to get all booking details of particular booking based on booking_primary_contact_no.
     *
     * Finds all the booking details of particular booking of a particular user.
     *
     * @param : booking_primary_contact_no
     * @return : array of booking details
     */
    function get_spare_parts_booking($limit, $start, $vendor_id = array() , $partner_id = NULL){
        if($limit == "All"){
            $select = "count(spare_parts_details.booking_id) as count";
        } else {
            $select = "spare_parts_details.*, users.name, booking_details.booking_primary_contact_no, service_centres.name as sc_name, "
                    . "bookings_sources.source, booking_details.current_status,booking_details.request_type,"
                    . "booking_details.service_center_closed_date";
            if($limit != -1){
                $this->db->limit($limit, $start);
            }
            
        }
        if(!empty($vendor_id)){
            $this->db->where_in("assigned_vendor_id", $vendor_id);
        }
        
        if(!empty($partner_id)){
            $this->db->where('booking_details.partner_id' , $partner_id);
        }
        $this->db->select($select);
        $this->db->from('spare_parts_details'); 
        $this->db->join('booking_details', 'booking_details.booking_id = spare_parts_details.booking_id');
        $this->db->join('users', 'users.user_id = booking_details.user_id');
        $this->db->join('service_centres','service_centres.id = booking_details.assigned_vendor_id');
        $this->db->join('bookings_sources','bookings_sources.partner_id = booking_details.partner_id');
        $this->db->where_in("current_status", array("Pending","Rescheduled"));
        $this->db->order_by('spare_parts_details.awb_by_sf', 'DESC');  
        $query = $this->db->get();
        return $query->result_array();
        
    }
    
    /**
     * @desc: This method increase count in the case reschedule or escalation
     * @param String $booking_id
     * @param String $column_name
     */
    function increase_escalation_reschedule($booking_id, $column_name){
        $sql = "UPDATE booking_details SET $column_name= ($column_name+1) where booking_id = '$booking_id' ";
        return $this->db->query($sql);
    }
    
    
    /**
     * @Desc: This function is used to get SMS sent for particular booking id
     * @params: booking_id
     * @return: array
     * 
     */
    function get_sms_sent_details($booking_id){
        $bookingIDArray[] = $booking_id;
        if (strpos($booking_id, 'Q-') === false) {
            $bookingIDArray[] = "Q-".$booking_id;
        }
        $this->db->select('*');
        $this->db->where_in('booking_id', $bookingIDArray);
        $query = $this->db->get('sms_sent_details');
        return $query->result_array();
    } 
    
     /**
     * @Desc: This function is used to get email sent on escalation tag for particular booking id 
     * @params: booking_id
     * @return: array
     * 
     */
    function get_email_sent_details($booking_id, $email_tag=""){
        $trimed_booking_id = preg_replace("/[^0-9]/","",$booking_id);
        $this->db->select('*');
        if($email_tag){
            $this->db->where('email_tag',$email_tag);
        }
        $this->db->like('subject',$trimed_booking_id);
        $query = $this->db->get('email_sent');
        return $query->result_array();
    } 
    
    
    /**
     *  @desc : This function is used to insert appliance details into appliance_product_description table
     *
     *  @param : Array()
     *  @return :id
     */
    
    function insert_appliance_details($data){
        $this->db->insert('appliance_product_description', $data);

        return $this->db->insert_id();
    }
    
    /**
     *  @desc : This function is used to get appliance details data from appliance_product_description table
     *
     *  @param : Array()
     *  @return :id
     */
    
    function get_service_id_by_appliance_details($where = null){
        $this->db->select('apd.*,services');
        $this->db->from('appliance_product_description as apd');
        $this->db->join('services','apd.service_id = services.id');
        if(!empty($where)){
            $this->db->where($where);
        }
        
        $this->db->where('is_verified',1);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     *  @desc : This function is used to get brand name and service id which is not exist in appliance_brand table
     * when partner_appliance_file is uploaded
     *  @param : void()
     *  @return :array()
     */
    function get_not_exist_appliance_brand_data(){
        $sql = 'SELECT DISTINCT ap.brand as brand_name, ap.service_id
                FROM `partner_appliance_details` AS ap
                WHERE NOT 
                EXISTS (
                SELECT DISTINCT brand_name
                FROM appliance_brands
                WHERE LOWER( brand_name ) = LOWER( brand ) 
                AND ap.service_id = appliance_brands.service_id
                )';
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    /**
     *  @desc : This function is used to insert brand name and service id which is not exist in appliance_brand table
     * when partner_appliance_file is uploaded
     *  @param : array()
     *  @return :id
     */
    function insert_not_exist_appliance_brand_data($data){
        return $this->db->insert_batch('appliance_brands',$data);
    }
    
    /**
     *  @desc : This function is used to get partner brand logo
     *  @param : void()
     *  @return :array()
     */
    function get_partner_logo($select,$where){
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get('partner_brand_logo');
        return $query->result_array();
    }
    
    function get_sku_details($where){
        $this->db->select('sku_details.*, services.services, bookings_sources.code');
        $this->db->where($where);
        $this->db->join("services","services.id = sku_details.service_id");
        $this->db->join("bookings_sources", "bookings_sources.partner_id =  sku_details.partner_id");
        $query = $this->db->get('sku_details');
        
        return $query->result_array();
    }
    
    function insert_sku_transaction($data){
        $this->db->insert('ecommerce_product_transactions', $data);

        return $this->db->insert_id();
    }
    
    function get_sku_transactions($where){
        $this->db->select("*");
        $this->db->where($where);
        $query = $this->db->get("ecommerce_product_transactions");
        return $query->result_array();
    }
    
    
     /**
     * @desc: This function is used to insert rating if only one booking is 
     * exist for the given missed call number and rating column is null and current status is completed
     * @param $missed_call_number string 
     * @retun:void()
     */
    function get_missed_call_rating_booking_count($missed_call_number){
        $sql = "SELECT DISTINCT booking_id "
                . "FROM booking_details as bd,"
                . "rating_passthru_misscall_log as rp, "
                . "users as u WHERE current_status = 'Completed' "
                . "AND bd.rating_stars IS NULL AND EXISTS "
                . "(SELECT 1 from sms_sent_details as ssd WHERE "
                . "ssd.booking_id = bd.booking_id AND ssd.sms_tag IN "
                . "('missed_call_rating_sms', 'complete_booking','complete_booking_snapdeal')) "
                . "AND bd.closed_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01') "
                . "- INTERVAL 2 MONTH AND rp.from_number = bd.booking_primary_contact_no "
                . "AND u.user_id = bd.user_id "
                . " AND rp.To = '".GOOD_MISSED_CALL_RATING_NUMBER."' AND rp.from_number = '".$missed_call_number."'"
                . " AND rp.create_date >= bd.closed_date having count(DISTINCT booking_id) = 1";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    
      /**
     *  @desc : This function is used to get bookings based on booking status type
     *  @param : $post string
     *  @param : $select string
     *  @return : $output Array()
     */
  function _get_bookings_by_status($post, $select = "") {
        $this->db->_reserved_identifiers = array('*',"'%d-%m-%Y')", "SELECT");
        if (empty($select)) {
            $select = '*';
        }
        $this->db->distinct();
        $this->db->select($select,FALSE);
        $this->db->from('users');
        $this->db->join('booking_details', 'booking_details.user_id  = users.user_id', 'left');
        $this->db->join('services', 'services.id = booking_details.service_id', 'left');
        $this->db->join('service_centres', 'booking_details.assigned_vendor_id = service_centres.id','left');
        $this->db->join('penalty_on_booking', "booking_details.booking_id = penalty_on_booking.booking_id and penalty_on_booking.active = '1'",'left');
        $this->db->join('booking_files', "booking_files.id = ( SELECT booking_files.id from booking_files WHERE booking_files.booking_id = booking_details.booking_id AND booking_files.file_description_id = '".BOOKING_PURCHASE_INVOICE_FILE_TYPE."' LIMIT 1 )",'left');
        if(!isset($post['unit_not_required'])){
            $this->db->join('booking_unit_details', 'booking_details.booking_id = booking_unit_details.booking_id', 'left');
        }
        
        if (!empty($post['where'])) {
            $this->db->where($post['where']);
        }
        if (!empty($post['where_in'])) {
            foreach($post['where_in'] as $key=>$values){
                $this->db->where_in($key, $values);
            }
        }
         if (!empty($post['where_not_in'])) {
            foreach($post['where_not_in'] as $key=>$values){
                $this->db->where_not_in($key, $values);
            }
        }
        if (!empty($post['join'])) {
            foreach($post['join'] as $key=>$values){
                $this->db->join($key, $values);
            }
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

        if (!empty($post['order']) && isset($post['order_performed_on_count'])) {
            $this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
        } else {
            $this->db->order_by('closed_date','DESC');
        }
    }
    
    /**
     *  @desc : This function is used to get bookings based on booking status type
     *  @param : $post string
     *  @param : $select string
     *  @return: Array()
     */
    function get_bookings_by_status($post, $select = "",$sfIDArray = array(),$is_download=0,$is_spare=NULL) {
        $this->_get_bookings_by_status($post, $select);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        if($sfIDArray){
            $this->db->where_in('booking_details.assigned_vendor_id', $sfIDArray);
        }
        /*if($partnerIDArray){
            $this->db->where_in('booking_details.partner_id', $partnerIDArray);
            $this->db->where_not_in('booking_details.internal_status', array('InProcess_Cancelled','InProcess_Completed'));
        }*/
        if($is_download){
            if($is_download == 2){
               
            }
            else{
                $this->db->where_in('booking_details.current_status', array('Pending','Rescheduled'));
            }
        }
        if($is_spare){
            $this->db->join('spare_parts_details', 'booking_details.booking_id  = spare_parts_details.booking_id', 'left');
            $this->db->group_by('booking_details.booking_id'); 
        }
        $query = $this->db->get();
        if($is_download){
            
            return $query;
        }
        return $query->result();
    }
    
    /**
     *  @desc : This function is used to get total filtered bookings based on booking status type
     *  @param : $post string
     *  @return: Array()
     */
    function count_filtered_bookings_by_status($post,$sfIDArray = array() ,$partnerIDArray = array()){
        $this->_get_bookings_by_status($post,'count(distinct(booking_details.booking_id)) as numrows');
        if($sfIDArray){
            $this->db->where_in('booking_details.assigned_vendor_id', $sfIDArray);
        }
        if($partnerIDArray){
            $this->db->where_in('booking_details.partner_id', $partnerIDArray);
        }
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
    }
    
    
    function update_appliance_description_details($data,$where){
         $this->db->where($where,FALSE);
         $this->db->update('appliance_product_description', $data);
    }
    
    /**
     *  @desc : This function is used to get query based on booking status type
     *  @param : $post string
     *  @param : $select string
     *  @return : $output Array()
     */
    function _get_queries($post,$pincode_status, $query_status,$select = "") {
        $this->db->_reserved_identifiers = array('*','CASE','EXISTS','NOT');
        $this->db->distinct();
        $this->db->from('booking_details');
        if (empty($select)) {
            $select = '*';
        }
        
        $this->db->select($select,FALSE);
        $this->db->join('users', 'users.user_id = booking_details.user_id ');
        $this->db->join('services', 'services.id = booking_details.service_id');
        $this->db->join('booking_unit_details', 'booking_details.booking_id = booking_unit_details.booking_id');
        
        $this->db->where("booking_details.booking_id LIKE '%Q-%'",NULL);
        $this->db->where("(DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y')) >= 0 OR booking_details.booking_date = '')",NULL);
        if (!empty($post['where'])) {
            $this->db->where($post['where']);
        }
        if (!empty($post['join'])) {
            foreach($post['join'] as $key=>$values){
                $this->db->join($key, $values);
            }
        }
        
        $check_pincode = "(SELECT 1
                                FROM (vendor_pincode_mapping)
                                JOIN service_centres ON service_centres.id = vendor_pincode_mapping.Vendor_ID
                                WHERE vendor_pincode_mapping.Appliance_ID = booking_details.service_id
                                AND vendor_pincode_mapping.Pincode = booking_details.booking_pincode
                                AND service_centres.active = '1' AND service_centres.on_off = '1')";
        
        if($query_status != _247AROUND_CANCELLED){
            if($pincode_status == PINCODE_AVAILABLE ){
                $is_exist = "EXISTS $check_pincode";
                $this->db->where($is_exist,NULL);
            } else if($pincode_status == PINCODE_NOT_AVAILABLE){
                $is_exist = "NOT EXISTS $check_pincode";
                $this->db->where($is_exist,NULL);
            }
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
        
        if(isset($post['order_by']) && !empty($post['order_by'])){
            $this->db->order_by($post['order_by'],'DESC');
            //$this->db->order_by('booking_day','DESC');
        }else if(!empty($post['order'])){
            $this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
        }else{
            $this->db->order_by('booking_details.create_date','DESC');
        }
    }
    
    /**
     *  @desc : This function is used to get query based on booking status type
     *  @param : $post string
     *  @param : $select string
     *  @return: Array()
     */
    function get_queries($post, $pincode_status,$query_status,$select = "") {
        $this->_get_queries($post, $pincode_status,$query_status,$select);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        
        $query = $this->db->get();

        if( $query_status == _247AROUND_FOLLOWUP && ($pincode_status == PINCODE_ALL_AVAILABLE) && isset($post['where']['booking_details.booking_id']) && !empty($post['where']['booking_details.booking_id'])){
            $temp = $query->result();
            $data = $this->searchPincodeAvailable($temp, $pincode_status);
            $response = $data;

        }else {
            $response = $query->result();
        }
        
        return $response;
    }
    
    /**
     *  @desc : This function is used to get total bookings based on booking status type
     *  @param : $post string
     *  @return: Array()
     */
    public function count_all_queries($post,$pincode_status,$query_status) {
        $this->_get_queries($post,$pincode_status,$query_status, "count(distinct(booking_details.booking_id)) as numrows,STR_TO_DATE(booking_details.booking_date,'%d-%m-%Y') as booking_day");
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
    }  
    
    /**
     *  @desc : This function is used to get total filtered bookings based on booking status type
     *  @param : $post string
     *  @return: Array()
     */
    function count_filtered_queries($post,$pincode_status,$query_status){
        $this->_get_queries($post,$pincode_status,$query_status,"count(distinct(booking_details.booking_id)) as numrows, STR_TO_DATE(booking_details.booking_date,'%d-%m-%Y') as booking_day");
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
    }
    
    /**
     * @desc: This function is used to insert Send email to database
     * @params: $data Array()
     * @return: Integer 
     */
    function add_email_send_details($data){
        $this->db->insert('email_sent', $data);
        return $this->db->insert_id();
    }
    
    /**
     * @desc This function is used to update the inventory stock
     * @param array $result
     * @param array $agent_details
     */
    function process_inventory($result,$agent_details) {
        log_message("info",__METHOD__." ". print_r($result,TRUE));
        /* check if booking is assigned to sf
         * if booking is assigned then update the inventory stock
         * else do not perform any action on inventory
         */
        $sf_id = $this->reusable_model->get_search_query('booking_details', 'assigned_vendor_id', array('booking_id' => $result['booking_id']), NULL, NULL, NULL, NULL, NULL)->result_array();
        if (!empty($sf_id[0]['assigned_vendor_id'])) {
            $match = array();
            preg_match('/[0-9]+/', $result['appliance_capacity'], $match);
            if (!empty($match)) {
                if ($match[0] <= 32) {
                    $data['part_number'] = LESS_THAN_32_BRACKETS_PART_NUMBER;
                } else if ($match[0] > 32) {
                    $data['part_number'] = GREATER_THAN_32_BRACKETS_PART_NUMBER;
                }

                $data['receiver_entity_id'] = $sf_id[0]['assigned_vendor_id'];
                $data['receiver_entity_type'] = _247AROUND_SF_STRING;
                $data['stock'] = -1;
                $data['booking_id'] = $result['booking_id'];
                $data['agent_id'] = $agent_details['agent_id'];
                $data['agent_type'] = $agent_details['agent_type'];

                $this->miscelleneous->process_inventory_stocks($data);
            }
        }else{
            log_message("info",__METHOD__." SF Id is Empty");
        }
    }
    
    function getbooking_state_change_by_any($where){
        $this->db->where($where);
        $this->db->order_by('booking_state_change.id');
        $query = $this->db->get('booking_state_change');

        if($query->num_rows){
            return $query->result_array();
        } else {
            return false;
        }
    }
    
    /**
     * @desc: This function is used to insert those payment which was done by customer.
     * @params: array $data
     * @return: string $insert_id
     */
    function insert_payment_link_details($data){
        $this->db->insert('gateway_booking_payment_details', $data);
        return $this->db->insert_id();
    }
    
    
     /**
     * @desc: This function is used to get the details from gateway_booking_payment_details table
     * @params: string $select
     * @params: array $where
     * @return: array result_array()
     */
    function get_payment_link_details($select,$where){
        $this->db->select($select);
        $this->db->where($where);
        $this->db->from('gateway_booking_payment_details');
        $query = $this->db->get();
        
        return $query->result_array();
    }
    
    
    /**
     * @desc: This function is used to update the details from gateway_booking_payment_details table
     * @params: string $id
     * @params: array $data
     * @return: boolean $res
     */
    function update_payment_link_details($id, $data) {
        if(!empty($id)){
            $this->db->trans_start();
            $this->db->where('id',$id);
            $this->db->update('gateway_booking_payment_details', $data);
            $this->db->trans_complete();

            $res =  $this->db->trans_status();
        }else{
            $res = FALSE;
        }
        
        return $res;
    }
    
    function get_state(){
        return $this->db->query('SELECT state from state_code')->result_array();
    }
    
    function insert_state_map($data){
        return $this->db->insert_batch('warehouse_state_relationship',$data)->insert_id;
    }
    
    function partner_completed_call_status_mapping($booking_id, $data){
        $this->db->where('booking_id', $booking_id);
        $this->db->where('partner_call_status_on_completed is NULL', NULL, FALSE);
        $this->db->update('booking_details',$data);
    }
    function update_customer_not_reachable_count($bookingID){
        $query = "UPDATE booking_details SET rating_unreachable_count = rating_unreachable_count+1 WHERE booking_id='".$bookingID."'";
        $this->db->query($query);
        return $this->db->affected_rows();
    }
    
    function insert_misc_charges_in_batch($data){
        $this->db->insert_batch("miscellaneous_charges", $data);
        return $this->db->insert_id();
    }
    
    function get_misc_charges_data($select, $where){
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get('miscellaneous_charges');
        return $query->result_array();
    }
    
    function update_misc_charges($where, $data){
        $this->db->where($where);
        $this->db->update('miscellaneous_charges', $data);
    }
    
     /**
     * @Desc: This function is used to get remarks sent for particular booking id
     * @params: booking_id
     * @return: array
     * 
     */
    function get_remarks($where){
        $this->db->select('booking_comments.id, agent_id, remarks, booking_comments.create_date, employee_id,employee.full_name, booking_comments.isActive');
        $this->db->from('booking_comments');
        $this->db->join('employee','booking_comments.agent_id = employee.id');
        $this->db->where($where);
        $this->db->order_by('booking_comments.create_date');
        $query = $this->db->get();
        return $query->result_array();
    } 
    
    function add_comment($data){
        $this->db->select('*');
        $this->db->from('booking_comments');
        $this->db->where($data);
        $query = $this->db->get();
        if ($query->num_rows() == 0) {

        $this->db->insert('booking_comments', $data);
        return $this->db->insert_id();

        }else {
            return false;
        }
    }
    function update_comment($where, $data) {
         $this->db->where($where);
        $this->db->update('booking_comments',$data);
        return $this->db->affected_rows();
    }
    function get_booking_tat_required_data($booking_id){
        $sql = "SELECT booking_details.partner_id,booking_details.booking_id,booking_details.request_type,booking_details.create_date,STR_TO_DATE(booking_details.initial_booking_date,'%d-%m-%Y') as initial_booking_date,"
                . "booking_details.is_upcountry,date(booking_details.service_center_closed_date) as sf_closed_date,"
                . "date(booking_details.closed_date) as around_closed_date,spare_parts_details.id as spare_id,spare_parts_details.status as spare_status,date(spare_parts_details.date_of_request) as part_request_date,"
                . "date(spare_parts_details.acknowledge_date) as spare_receieved_date ,"
                . "date(spare_parts_details.defective_part_shipped_date) as defactive_part_shipped_date,date(spare_parts_details.spare_cancelled_date) as spare_cancelled_date,"
                . "service_centres.non_working_days "
                . "FROM booking_details LEFT JOIN spare_parts_details ON "
                . "spare_parts_details.booking_id = booking_details.booking_id LEFT JOIN service_centres ON service_centres.id = booking_details.assigned_vendor_id "
                . "WHERE booking_details.booking_id = '".$booking_id."' order by booking_details.booking_id";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    function mark_booking_in_process($bookingArray){
       $this->db->where_in("booking_id",$bookingArray);
       $this->db->update("booking_details",array("is_in_process"=>1));
       return $this->db->affected_rows();
    }
    function get_parent_child_sibling_bookings($bookingID){
        $sql = "SELECT booking_details.booking_id,booking_details.parent_booking as parent,(SELECT GROUP_CONCAT(s.booking_id) FROM booking_details s WHERE s.parent_booking = booking_details.parent_booking"
                . " AND s.booking_id != booking_details.booking_id) as siblings,(SELECT GROUP_CONCAT(c.booking_id) FROM booking_details c WHERE c.parent_booking = booking_details.booking_id) as child "
                . "FROM booking_details WHERE booking_id = '".$bookingID."'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    function get_posible_parent_booking_id($contact,$service_id,$partnerID,$dayDiff){
        $this->db->_protect_identifiers = FALSE;
        $this->db->_reserved_identifiers = array('NOT');
        $where["DATEDIFF(CURRENT_TIMESTAMP , closed_date) <= ".$dayDiff] = NULL;
        $where['booking_details.service_id'] = $service_id;
        $where['booking_details.partner_id'] = $partnerID;
        $where['booking_primary_contact_no'] = $contact;
        $where['current_status'] = _247AROUND_COMPLETED;
        $where["request_type != '".REPEAT_BOOKING_TAG."'"] = NULL;
        $where['NOT EXISTS (SELECT 1 FROM booking_details bd WHERE bd.parent_booking = booking_details.booking_id AND bd.current_status ="Pending" LIMIT 1)'] = NULL;
        $this->db->select('booking_details.booking_id,booking_details.current_status,services.services,date(booking_details.closed_date) as closed_date,'
                . 'GROUP_CONCAT(appliance_brand) as brand ,GROUP_CONCAT(appliance_capacity) as capacity');
        $this->db->from('booking_details');
        $this->db->where($where);
        $this->db->join("services","services.id = booking_details.service_id");
        $this->db->join("booking_unit_details","booking_unit_details.booking_id = booking_details.booking_id");
        $this->db->group_by('booking_unit_details.booking_id');
        $query = $this->db->get();
        return $query->result_array();
    }
    function get_parent_booking_serial_number($bookingID,$all = NULL){
      if($all){
         $sql = "SELECT booking_unit_details.serial_number as parent_sn FROM booking_unit_details JOIN booking_details ON booking_details.parent_booking = booking_unit_details.booking_id "
                . "WHERE booking_details.booking_id = '".$bookingID."' AND booking_unit_details.serial_number IS NOT NULL AND booking_unit_details.serial_number != ''";
      }
      else{
          $sql = "SELECT booking_unit_details.serial_number as parent_sn FROM booking_unit_details JOIN booking_details ON booking_details.parent_booking = booking_unit_details.booking_id "
                . "WHERE booking_details.booking_id = '".$bookingID."' GROUP BY  booking_unit_details.booking_id HAVING COUNT(booking_unit_details.booking_id) < 2 ";
      }
      $query = $this->db->query($sql);
      return $query->result_array();
    }
    function get_am_booking_data($partner_id)
    {
        $sql="select  sum(case when (request_type like '%Repeat%' or request_type like '%Repair%') and(internal_status='InProcess_Completed'"
                ."and service_center_closed_date IS NOT NULL) or (current_status='Completed') then 1 else 0 end) `repair_completed`,"
                ."sum(case when (request_type  LIKE '%Repeat%' or request_type  LIKE '%Repair%') and(current_status='Pending') then 1 else 0 end) `repair_pending`,"
                ."sum(case when (request_type like '%Repeat%' or  request_type LIKE'%Repair%') and(current_status='Cancelled') then 1 else 0 end) `repair_cancalled`,"
                ."sum(case when (request_type not like '%Repeat%' AND request_type not like '%Repair%') and(internal_status='InProcess_Completed' and service_center_closed_date IS NOT NULL)"
                ."or (current_status='Completed') then 1 else 0 end) `install_completed`, sum(case when (request_type not LIKE '%Repeat%' AND request_type not LIKE '%Repair%') and(current_status='Pending')"
                ."then 1 else 0 end) `install_pending`,"
                 ."sum(case when (request_type not like '%Repeat%' AND  request_type not LIKE'%Repair%') and(current_status='Cancelled') "
                ."then 1 else 0 end) `install_cancalled`"
                ."from booking_details where STR_TO_DATE(booking_details.initial_booking_date,'%d-%m-%Y') >=DATE_SUB(CURDATE(), INTERVAL 1 MONTH )"
                ."and partner_id IN ('".$partner_id."')";
        $query = $this->db->query($sql);
        $result =  $query->result_array();
       if(!empty($result))
        {
            $result=$result['0'];
            $result['repair_total']=$result['repair_completed']+$result['repair_pending']+$result['repair_cancalled'];
            $result['install_total']=$result['install_completed']+$result['install_pending']+$result['install_cancalled'];
        } 
        return $result;
    }
    function get_data_for_duplicate_serial_number_check($serialNumber,$booking_id){
          $sql = "SELECT * FROM booking_unit_details WHERE `serial_number` = '".$serialNumber."' AND booking_status != 'Cancelled' AND price_tags != 'Repeat Booking' AND booking_id != '".$booking_id."'"
                . " UNION "
                . "SELECT booking_unit_details.* FROM service_center_booking_action JOIN booking_unit_details ON booking_unit_details.id = service_center_booking_action.unit_details_id WHERE "
                . "service_center_booking_action.serial_number = '".$serialNumber."' AND service_center_booking_action.current_status != 'Cancelled' AND `price_tags` != 'Repeat Booking' "
                . "AND service_center_booking_action.booking_id != '".$booking_id."'";
        
         $query = $this->db->query($sql);
         return $query->result_array();
    }
    /** @description:* add symptom at the time of add booking
     *  @param : booking symptom array
     *  @return : void
     */

    function addBookingSymptom($booking_symptom){
	$this->db->insert('booking_symptom_defect_details', $booking_symptom);
        log_message ('info', __METHOD__ . "=> Booking Symptom Defect Details  SQL ". $this->db->last_query());
        
        return $this->db->insert_id();
    }
    /** @description:* Update symptom at the time of completing booking
     *  @param : booking_id,booking symptom array
     *  @return : void
     */

    function update_symptom_defect_details($booking_id, $booking_symptom) {
        $this->db->where('booking_id', $booking_id);
        $this->db->update('booking_symptom_defect_details', $booking_symptom);
        return $this->db->affected_rows();
    }
    /**
     * @Desc: This function is used to get booking symptoms 
     * @params: booking_id
     * @return: array
     * 
     */
    function getBookingSymptom($booking_id){
        $this->db->select('*');
        $this->db->from('booking_symptom_defect_details');
        $this->db->where('booking_id', $booking_id);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function get_booking_details($select="*", $where = array(), $is_user = false, $is_service = false, $is_unit = false, $is_partner = false, $is_vendor = false){
        $this->db->select($select, false);
        $this->db->from('booking_details');
        $this->db->where($where);
        if($is_service){
            $this->db->join("services", "services.id = booking_details.service_id");
        }
        if($is_user){
            $this->db->join('users',' users.user_id = booking_details.user_id');
        }
        if($is_unit){
            $this->db->join('booking_unit_details', 'booking_unit_details.booking_id = booking_details.booking_id');
        }
        if($is_partner){
            $this->db->join('partners', 'booking_details.partner_id = partners.id'); 
        }
        if($is_vendor){
            $this->db->join('service_centres', 'booking_details.assigned_vendor_id = service_centres.id'); 
        }
        $query = $this->db->get();
        return $query->result_array();
    }
    /**
     * @Desc: This function is used to get support file types for Booking
     * @params: void
     * @return: array
     * 
     */
    function get_file_type($where=array(), $showAll = false){
        $this->db->select('*');
        $this->db->distinct();
        $this->db->from('file_type');
        if(!$showAll) {
            $this->db->where('is_active', 1);
        }
        if(!empty($where)){
            $this->db->where($where);
        }
        $query = $this->db->get();
        return $query->result_array();
    }
    /**
     * @desc: This function is used to insert supporting files for booking.
     * @params: array $data
     * @return: string $insert_id
     */
    function insert_booking_file($data){
        $this->db->insert('booking_files', $data);
        log_message ('info', __METHOD__ . "=> Insert Booking Supporting File  SQL ". $this->db->last_query());
        return $this->db->insert_id();
    }
    /**
     *  @desc : Function to update supporting files for booking.
     *  @param : array $data and array $where
     *  @return : no. of affected rows
     */
    
    function update_booking_file($data, $where = array()) {
        if(!empty($where)){
            $this->db->where($where);
        }
        $result =  $this->db->update("booking_files", $data);

        log_message ('info', __METHOD__ . "=> Update Booking Supporting File  SQL ". $this->db->last_query());

        return $result;
        
    }
    /**
     *  @desc : Function to delete supporting files for booking.
     *  @param : array $where
     *  @return : no. of affected rows
     */
    
    function delete_booking_file($where = array()) {
        if(!empty($where)){
            $this->db->where($where);
        }
        $result =  $this->db->delete('booking_files');

        log_message ('info', __METHOD__ . "=> Delete Booking Supporting File  SQL ". $this->db->last_query());

        return $result;
        
    }
    /**
     * @Desc: This function is used to get support files for Booking
     * @params: array $where
     * @return: array
     * 
     */
    function get_booking_files($where = array()){
        $this->db->select('booking_files.*,file_type.file_type as file_description');
        $this->db->from('booking_files');
        $this->db->join('file_type', 'file_type.id = booking_files.file_description_id');
        if(!empty($where)){
            $this->db->where($where);
        }
        $query = $this->db->get();
        return $query->result_array();
    }
    /**
     * @Desc: This function is used to get AM detail by Booking Id
     * @params: array $booking_id
     * @return: array account manager detail
     * 
     */
    function get_am_by_booking($booking_id, $select="*"){
        $this->db->select($select);
        $this->db->from("booking_details");
        $this->db->where("booking_id", $booking_id);
        $this->db->join("agent_filters", "agent_filters.entity_type = '"._247AROUND_EMPLOYEE_STRING."' and agent_filters.entity_id = booking_details.partner_id and agent_filters.state = booking_details.state and agent_filters.is_active=1");
        $this->db->join("employee", "employee.id = agent_filters.agent_id");
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * @Desc: This function is used to get Booking cancellation reasons
     * @return: array
     */
    function get_cancellation_reasons($select="*")
    {
        $this->db->select($select);
        $this->db->from("booking_cancellation_reasons");
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function update_service_status($where, $data){
        $this->db->where($where,FALSE);
        $this->db->update('services', $data);
        if ($this->db->affected_rows() > 0) {
                return 1;            
        } else {
            return 0;
        }
    }
}
