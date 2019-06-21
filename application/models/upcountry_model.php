<?php

class Upcountry_model extends CI_Model {
    var $vendor_min_up_distance = 25;
    
    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
        
    }
    /**
     * @desc: This is used to insert upcountry details as a batch
     * @param Array $data
     * @return boolean
     */
    function insert_batch_sub_sc_details($data){
        $this->db->insert_batch('sub_service_center_details', $data);
        return $this->db->insert_id();
    }
    /**
     * @desc: Calculate Distance between two region
     * @param String $postcode1
     * @param String $city1
     * @param String $postcode2
     * @param String $city2
     * @return boolean
     */
    function calculate_distance_between_pincode($postcode1, $city1,$postcode2, $city2){
        log_message('info', __FUNCTION__.' Calculate Pincdode1 '. $postcode1." Pincode 2". $postcode2 );
        
        $city_1 = str_replace(' ', '%20', $city1);
        $city_2 = str_replace(' ', '%20', $city2);
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$city_1,$postcode1,India&destinations=$city_2,$postcode2,India&mode=driving&language=en-EN&sensor=false&key=AIzaSyDYYGttub8nTWcXVZBG9iMuQwZfFaBNcbQ";
        $data = file_get_contents($url);
        $result = json_decode($data, true);
        
        if(!empty($data)){
            if ((strstr($result['destination_addresses'][0], $postcode2) === FALSE) OR (strstr($result['origin_addresses'][0], $postcode1) === FALSE)) {
                //echo $result['origin_addresses'][0] . ", " . $result['destination_addresses'][0]. PHP_EOL;
//                echo 'Pincode mismatch: ' .  $postcode1 . ", Pincode 2: ". $postcode2 . PHP_EOL;
                
                log_message('info', __FUNCTION__.' Wrong Distance Returned pincode1 '. $postcode1. ",". $postcode2);

                //Try again
                $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$postcode1,India&destinations=$postcode2,India&mode=driving&language=en-EN&sensor=false&key=AIzaSyDYYGttub8nTWcXVZBG9iMuQwZfFaBNcbQ";
                $data = file_get_contents($url);
                $result = json_decode($data, true);
                
                if(!empty($data)){
                    if ((strstr($result['destination_addresses'][0], $postcode2) === FALSE) OR (strstr($result['origin_addresses'][0], $postcode1) === FALSE)) {
//                        echo ' Distance Not Found After 2nd re-try as well: pincode1 '. $postcode1. ",". $postcode2 . PHP_EOL;
                        
                        log_message('info', __FUNCTION__.' Distance Not Found After 2nd re-try as well: pincode1 '. $postcode1. ",". $postcode2);
                        
                        return FALSE;
                    } else {
                        foreach($result['rows'] as $distance) {

                            if($distance['elements'][0]['status'] == "OK"){
                                log_message('info', __FUNCTION__.' Distance Found in 2nd pass');

                                return $distance['elements'][0];
                            } else {
                                log_message('info', __FUNCTION__.' Distance Not Found After re-try as well, Status not OK pincode1 '. $postcode1. ",". $postcode2);

                                return FALSE;
                            }
                        }   
                    }
                }
                
            } else {
                foreach($result['rows'] as $distance) {

                    if($distance['elements'][0]['status'] == "OK"){
                        log_message('info', __FUNCTION__.' Distance Found, verify pincodes returned.');

                        return $distance['elements'][0];
                    } else {
                        log_message('info', __FUNCTION__.' Distance Not Found, Status not OK pincode1 '. $postcode1. ",". $postcode2);

                        return FALSE;
                    }
                }
            }
        } else {
            log_message('info', __FUNCTION__.' Distance Not Found pincode1 '. $postcode1. ",". $postcode2);

            return FALSE;
        }
    }
    /**
     * 
     * @param type $booking_city
     * @param type $booking_pincode
     * @param type $vendor_details
     * @param type $partner_data
     * @return type
     */
    function action_upcountry_booking($booking_city,$booking_pincode, $vendor_details, $partner_data){
        log_message('info', __METHOD__  );
        $error = array();
        $same_pincode_vendor = array();
        $upcountry_vendor_details = array();
        $distance = false;
        foreach ($vendor_details as $value) {
            $where1 = array('service_center_id' => $value['vendor_id'], 
                'district'=> $value['city'], 'active' => 1);
            $res_sb = $this->get_sub_service_center_details($where1);
            $this->vendor_min_up_distance = $value['min_upcountry_distance'];
            if($res_sb){
                if($res_sb[0]['pincode'] != $booking_pincode){
                    log_message('info', __METHOD__ ." Continue process....");
                    // Calculate distance 
                    $is_distance = $this->get_distance_between_pincodes($booking_pincode,$res_sb[0]['pincode']);
                    if(empty($is_distance)){
                        log_message('info', __FUNCTION__ ." Distance not exist in table. Call to Google API");
                        $is_distance1 = $this->calculate_distance_between_pincode($booking_pincode, 
                            $booking_city, 
                            $res_sb[0]['pincode'], $res_sb[0]['district']);
                        
                        if($is_distance1){
                            $distance1 = (round($is_distance1['distance']['value'] / 1000, 2));
                            $this->insert_distance($booking_pincode, $res_sb[0]['pincode'], $distance1,_247AROUND_DEFAULT_AGENT);
                             log_message('info', __FUNCTION__ ." Insert distance & pincode " . $booking_pincode.
                                     $res_sb[0]['pincode']. $distance1);
                            $distance = $distance1 * 2;
                        }
                    } else {
                        log_message('info', __FUNCTION__ ." Distance exist in table." . $distance);
                        $distance = $is_distance[0]['distance'] *2;
                    }
                   
                    if($distance){
                        $upcountry_vendor['upcountry_pincode'] = $res_sb[0]['pincode'];
                        $upcountry_vendor['vendor_id'] = $value['vendor_id'];
                        $upcountry_vendor['sub_vendor_id'] = $res_sb[0]['id'];
                        $upcountry_vendor['upcountry_distance'] = $distance;
                        $upcountry_vendor['sf_upcountry_rate'] = $res_sb[0]['upcountry_rate'];
                       
                        array_push($upcountry_vendor_details,$upcountry_vendor);
                    } else {
                        // Distance not calculated 
                         log_message('info', __FUNCTION__ ." Distance not calculated ". $value['vendor_id']
                            ." booking_pincode ". $booking_pincode);
                        $error['message'] = UPCOUNTRY_DISTANCE_CAN_NOT_CALCULATE; 
                        $error['vendor_id'] = $value['vendor_id'];
                        $error['sub_vendor_id'] = $res_sb[0]['id'];
                        $error['upcountry_pincode'] = $res_sb[0]['pincode'];
                        $error['upcountry_distance'] = 0;
                        $error['sf_upcountry_rate'] = $res_sb[0]['upcountry_rate'];
                        $error['upcountry_remarks'] = UPCOUNTRY_DISTANCE_CAN_NOT_CALCULATE; 
                       
                    }
                } else {
                    log_message('info', __FUNCTION__ ." Not Upcountry Booking ". $value['vendor_id']
                            ." booking_pincode ". $booking_pincode);
                    $same_pincode_vendor['vendor_id'] = $value['vendor_id'];
                    $same_pincode_vendor['message'] = NOT_UPCOUNTRY_BOOKING;
                    $same_pincode_vendor['upcountry_distance'] = 0;
                    $same_pincode_vendor['upcountry_remarks'] = CUSTOMER_AND_SUB_OFFICE_HAS_SAME_PINCODE;
                    break;
                }
            } else {
                log_message('info', __FUNCTION__ ." Not Upcountry Booking ". $value['vendor_id']
                            ." booking_pincode ". $booking_pincode);
                $same_pincode_vendor['vendor_id'] = $value['vendor_id'];
                $same_pincode_vendor['message'] = NOT_UPCOUNTRY_BOOKING;
                $same_pincode_vendor['upcountry_distance'] = 0;
                if(!empty($value['city'])){
                    $same_pincode_vendor['upcountry_remarks'] = CUSTOMER_DISTRICT_NOT_EXIST_IN_SUB_OFFICE;
                } else {
                    $same_pincode_vendor['upcountry_remarks'] = CUSTOMER_PINCODE_NOT_EXIST_IN_INDIA_PINCODE;
                }
                
                break;
            }
        }
        
        if(!empty($same_pincode_vendor)){
            log_message('info', __FUNCTION__ ." Not upcountry booking" );
            return $same_pincode_vendor;
            
        } else if(!empty($upcountry_vendor_details)){
           
            if(count($upcountry_vendor_details) == 1){
                log_message('info', __FUNCTION__ ." mark Upcountry" );
               
                return $this->mark_upcountry_vendor($upcountry_vendor_details[0], $partner_data);

            } else {
                log_message('info', __FUNCTION__ ." Got to calculate min disatance" );
                $get_data = $this->get_minimum_upcountry_price($upcountry_vendor_details, $partner_data);
                return $this->mark_upcountry_vendor($get_data, $partner_data);
            }
        } else if(!empty($error)){
            log_message('info', __FUNCTION__ ." upcountry error ".print_r($error, TRUE) );
            return $error;
        }
    }
    
    function mark_upcountry_vendor($upcountry_vendor_details, $partner_data) {
         log_message('info', __FUNCTION__ );
        $up_data = array();
        
        if ($partner_data[0]['is_upcountry'] == 1) {
            
            $partner_upcountry_rate = $partner_data[0]['upcountry_rate'];
            
            $partner_upcountry_approval = $partner_data[0]['upcountry_approval'];
            
            $max_threshold_distance = ($partner_data[0]['upcountry_max_distance_threshold'] * 2 + $this->vendor_min_up_distance *2);
        } else {
            $partner_upcountry_approval = 0;
            $partner_upcountry_rate = DEFAULT_UPCOUNTRY_RATE;
//            $min_threshold_distance = UPCOUNTRY_MIN_DISTANCE;
            $max_threshold_distance = UPCOUNTRY_DISTANCE_THRESHOLD;
        }
        
        $min_threshold_distance = $this->vendor_min_up_distance *2;
        
        $upcountry_distance = $upcountry_vendor_details['upcountry_distance'];

        if ($upcountry_distance <= ($min_threshold_distance)) {
           
            log_message('info', __FUNCTION__ ." Not Upcountry Booking ". print_r($upcountry_vendor_details, true) );
            $up_data['vendor_id'] = $upcountry_vendor_details['vendor_id'];
            $up_data['message'] = NOT_UPCOUNTRY_BOOKING;
            $up_data['upcountry_distance'] = 0;
            $up_data['upcountry_remarks'] = DISTANCE_WITHIN_MUNICIPAL_LIMIT;
            $up_data['partner_provide_upcountry'] = $partner_data[0]['is_upcountry'];
            $up_data['upcountry_bill_to_partner'] = $partner_data[0]['upcountry_bill_to_partner'];

            
        } else if ($upcountry_distance > ($min_threshold_distance)
                && $upcountry_distance <= $max_threshold_distance) {

            $up_data = array('upcountry_pincode' => $upcountry_vendor_details['upcountry_pincode'],
                'upcountry_distance' => ($upcountry_vendor_details['upcountry_distance'] - $min_threshold_distance),
                'sf_upcountry_rate' => $upcountry_vendor_details['sf_upcountry_rate'],
                'sub_vendor_id' => $upcountry_vendor_details['sub_vendor_id'],
                'vendor_id' => $upcountry_vendor_details['vendor_id'],
                'partner_upcountry_rate' => $partner_upcountry_rate,
                'partner_upcountry_approval' =>$partner_upcountry_approval,
                'upcountry_approval_email' => $partner_data[0]['upcountry_approval_email'],
                'is_upcountry' => 1,
                'partner_provide_upcountry' => $partner_data[0]['is_upcountry'],
                'upcountry_bill_to_partner' => $partner_data[0]['upcountry_bill_to_partner']);

            $up_data['message'] = UPCOUNTRY_BOOKING;
            if(isset($partner_data[0]['account_manager_id'])){
                $up_data['partner_am_id'] = $partner_data[0]['account_manager_id'];
            }
            
            log_message('info', __FUNCTION__ ." Upcountry Booking ". print_r($up_data, true) );
           
        } else if($upcountry_distance > $max_threshold_distance) {
           
            $up_data = array('upcountry_pincode' => $upcountry_vendor_details['upcountry_pincode'],
                'upcountry_distance' => ($upcountry_vendor_details['upcountry_distance'] - $min_threshold_distance),
                'sf_upcountry_rate' => $upcountry_vendor_details['sf_upcountry_rate'],
                'sub_vendor_id' => $upcountry_vendor_details['sub_vendor_id'],
                'vendor_id' => $upcountry_vendor_details['vendor_id'],
                'partner_upcountry_rate' => $partner_upcountry_rate,
                'partner_upcountry_approval' =>$partner_upcountry_approval,
                'upcountry_approval_email' => $partner_data[0]['upcountry_approval_email'],
                'is_upcountry' => 1,
                'partner_provide_upcountry' => $partner_data[0]['is_upcountry'],
                'upcountry_bill_to_partner' => $partner_data[0]['upcountry_bill_to_partner']);
           $up_data['message'] = UPCOUNTRY_LIMIT_EXCEED; 
           if(isset($partner_data[0]['account_manager_id'])){
                $up_data['partner_am_id'] = $partner_data[0]['account_manager_id'];
            }
           log_message('info', __FUNCTION__ ." Upcountry Limit Exceed ". print_r($up_data, true) );
            
        }
       
        return $up_data;
    }

    /**
     * @desc: Return Minimum Upcountry Price
     * @param Array $data
     */
    function get_minimum_upcountry_price($data){
        log_message('info', __FUNCTION__);
        $min_price['upcountry_distance'] = $data[0]['upcountry_distance'];
        foreach ($data as $value) {
          
            if($min_price['upcountry_distance'] >= $value['upcountry_distance']){
                $min_price['vendor_id'] = $value['vendor_id'];
                $min_price['upcountry_pincode'] = $value['upcountry_pincode'];
                $min_price['sf_upcountry_rate'] = $value['sf_upcountry_rate'];
                $min_price['sub_vendor_id'] = $value['sub_vendor_id'];
                $min_price['upcountry_distance'] = $value['upcountry_distance'];
                $min_price['is_upcountry'] = 1;
            }
        }
        log_message('info', __FUNCTION__." Min Price ". print_r($min_price, true));
        return $min_price;
    }

    /**
     * @desc: get partner detais who has provide upcountry booking
     * @param String $partner_id
     * @return boolean
     */
    function get_partner_provide_upcountry($partner_id){
        log_message('info', __FUNCTION__.' Partner Id '.$partner_id);
        $this->db->select('id');
        $this->db->where('id', $partner_id);
        $this->db->where('is_upcountry', '1');
        $query = $this->db->get('partners');
        return $query->result_array();
    }
    /**
     * @desc: Get sub service center details in Array
     * @param Array $where
     * @return boolean
     */
    function get_sub_service_center_details($where){
        log_message('info', __FUNCTION__);
        $this->db->distinct();
        $this->db->select('*');
        $this->db->where($where);
        $query = $this->db->get('sub_service_center_details');
        
        return $query->result_array();
    }
    
    /**
     * @desc: This is used to return failed upcoutry booking details
     * @return boolean
     */
    function get_upcountry_failed_details(){
        $this->db->select("booking_id,upcountry_pincode,city, sub_vendor_id, assigned_vendor_id, partner_id, amount_due,"
                . "sf_upcountry_rate,booking_pincode");
        $this->db->where("booking_details.is_upcountry", '0');
        $this->db->where("assigned_vendor_id IS NOT NULL", NULL, false);
        $this->db->where("sub_vendor_id IS NOT NULL", NULL, false);
        $this->db->where("upcountry_pincode IS NOT NULL", NULL, false);
        $this->db->where("upcountry_distance IS NULL", NULL, FALSE);
        $this->db->where_in("current_status", array('Pending', 'Rescheduled'));
        $query = $this->db->get("booking_details");
        return $query->result_array();
    }
    /**
     * @desc: This is used to combined booking id on the basis of booking date and booking Pincode while create FOC invoice
     * And Also upcountry_paid_by_customer must be 0 means Customer did not pay upcountry charges
     * @param String $vendor_id
     * @param String $from_date
     * @param String $to_date
     * @return boolean
     */
    function upcountry_foc_invoice($vendor_id, $from_date, $to_date, $is_regenerate){
        $invoice_check = "";
        if($is_regenerate == 0){
            $invoice_check =" AND upcountry_vendor_invoice_id IS NULL ";
        }
        
        $sql = "SELECT CONCAT( '', GROUP_CONCAT( DISTINCT ( bd.booking_id ) ) , '' ) AS booking,"
                . " upcountry_distance, "
                . " assigned_vendor_id, "
                . " round(sf_upcountry_rate * upcountry_distance ) AS upcountry_price,"
                . " COUNT(DISTINCT(bd.booking_id)) AS count_booking, sf_upcountry_rate"
                . " FROM `booking_details` AS bd "
                . " WHERE "
                . "  bd.assigned_vendor_id = '$vendor_id' "
                . " AND bd.closed_date >= '$from_date' "
                . " AND bd.closed_date < '$to_date' "
                . " $invoice_check "
                . " AND sub_vendor_id IS NOT NULL "
                . " AND bd.is_upcountry = '1' "
                . " AND bd.current_status = 'Completed' "
                . " AND bd.upcountry_paid_by_customer = 0 "
                . " AND bd.upcountry_partner_approved = 1 "
                . " GROUP BY bd.booking_date, bd.booking_pincode, bd.sf_upcountry_rate ";
        
        $query = $this->db->query($sql);
        
        if($query->num_rows > 0){
            $result = $query->result_array();
            $total_price = 0;
            $total_booking = 0;
            $total_distance = 0;
            foreach ($result as $value) {
                $total_price += $value['upcountry_price'];
                $total_booking += $value['count_booking'];
                $total_distance += $value['upcountry_distance'];
            }
            $result[0]['total_upcountry_price'] = $total_price;
            $result[0]['total_booking'] = $total_booking;
            $result[0]['total_distance'] = round($total_distance, 0);

            return $result;
            
        } else {
            return FALSE;
        }
    }
    /**
     * @desc: This is used to return upcountry booking which is paid by customer. 
     * For the Cash Invoice, We do not group booking on the basis of booking date and Pincode
     * Do not remove Additional, Parts cost, price tags
     * @param String $vendor_id
     * @param String $from_date
     * @param String $to_date
     * @return Array
     */
    function upcountry_cash_invoice($vendor_id, $from_date, $to_date, $is_regenerate){
        $invoice_check = "";
        if($is_regenerate == 0){
            $invoice_check =" AND upcountry_vendor_invoice_id IS NULL ";
        }
        
        $sql = "SELECT DISTINCT ( bd.booking_id) As booking_id, 'Upcountry Services' AS product_or_services, "
                . " upcountry_distance, bd.city, services, "
                . " '0.00' AS additional_charges, "
                . " '0.00' AS parts_cost, "
                . " 'Upcountry Services' AS price_tags, "
                . " assigned_vendor_id, customer_paid_upcountry_charges AS amount_paid, "
                . " '' AS appliance_category, "
                . " '' AS appliance_capacity, "
                . " closed_date, '' AS rating_stars, "
                . " round((customer_paid_upcountry_charges * 0.30 ),2) AS service_charges, '' AS around_net_payable "
                . " FROM `booking_details` AS bd, service_centres as sc, services "
                . " WHERE "
                . " bd.assigned_vendor_id = '$vendor_id' "
                . " AND bd.closed_date >= '$from_date' "
                . " AND bd.closed_date < '$to_date' "
                . " AND sub_vendor_id IS NOT NULL "
                . " AND bd.is_upcountry = '1' "
                . " $invoice_check "
                . " AND sc.id = bd.assigned_vendor_id "
                . " AND current_status = 'Completed' "
                . " AND bd.service_id = services.id"
                . " AND bd.upcountry_paid_by_customer = 1 "
                . " AND customer_paid_upcountry_charges > 0 ";
        
        $query = $this->db->query($sql);
        if($query->num_rows > 0){
            $result = $query->result_array();
            $total_price = 0;
            $total_distance = 0;
            foreach ($result as $value) {
                $total_price += $value['service_charges'];
                $total_distance += $value['upcountry_distance'];
            }
            $result[0]['total_upcountry_price'] = $total_price;
            $result[0]['total_booking'] = count($result);
            $result[0]['total_distance'] = round($total_distance, 0);
           
            return $result;
            
        } else {
            return array();
        }
        
    }
            
    function upcountry_service_center_3_month_price($vendor_id){
         for ($i = 0; $i < 3; $i++) {
            if ($i == 0) {
                $where = " AND `ud_closed_date` >=  '" . date('Y-m-01') . "'";
                $select = " date('Y-m-01')  as month, ";
            } else if ($i == 1) {
                $where = "  AND  ud_closed_date  >=  DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01')
			    AND ud_closed_date < DATE_FORMAT(NOW() ,'%Y-%m-01')  ";
                $select =" DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01') as month, ";
            } else if ($i == 2) {
                $where = "  AND  ud_closed_date  >=  DATE_FORMAT(NOW() - INTERVAL 2 MONTH, '%Y-%m-01')
			    AND ud_closed_date < DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01')";
                $select = "DATE_FORMAT(NOW() - INTERVAL 2 MONTH, '%Y-%m-01') as month, ";
            }

            $sql = "SELECT CONCAT( '', GROUP_CONCAT( DISTINCT ( bd.booking_id ) ) , '' ) AS booking, ud_closed_date, $select "
                 
                    . " upcountry_distance,"
                    . " CASE WHEN (upcountry_paid_by_customer = 0 OR upcountry_paid_by_customer IS NULL) "
                    . " THEN (sf_upcountry_rate * upcountry_distance  ) "
                    . " ELSE (customer_paid_upcountry_charges * 0.7) END As upcountry_price,"
                    . " COUNT(DISTINCT(bd.booking_id)) AS count_booking, "
                    . " round((sf_upcountry_rate ),2) AS sf_upcountry_rate"
                    . " FROM `booking_details` AS bd, booking_unit_details AS ud "
                    . " WHERE  ud.booking_id = bd.booking_id "
                    . " AND bd.is_upcountry = '1' "
                    . " AND bd.assigned_vendor_id = '$vendor_id' "
                    . " AND sub_vendor_id IS NOT NULL "
                    . " AND bd.current_status IN ('Completed', 'Pending', 'Rescheduled') $where "
                    . " GROUP BY "
                    . " CASE WHEN (upcountry_paid_by_customer = 0 OR upcountry_paid_by_customer IS NULL) THEN ( bd.booking_date ) END, "
                    . " CASE WHEN (upcountry_paid_by_customer = 0 OR upcountry_paid_by_customer IS NULL) THEN ( bd.booking_pincode ) END, "
                    . " CASE WHEN (upcountry_paid_by_customer = 0 OR upcountry_paid_by_customer IS NULL) THEN ( bd.sf_upcountry_rate ) END, "
                    . " CASE WHEN(upcountry_paid_by_customer =1) THEN (bd.booking_id) END";

            $query = $this->db->query($sql);
           
            if ($query->num_rows > 0) {
                $result = $query->result_array();
                $total_price = 0;
                $total_booking = 0;
                
                foreach ($result as $value) {
                    $total_price += $value['upcountry_price'];
                    $total_booking += $value['count_booking'];
                   
                }
                $result[0]['total_upcountry_price'] = $total_price;
                $result[0]['total_booking'] = $total_booking;
   

                $data[$i] = $result;

            } else {
                $data[$i] = array();
            }
        }
     
        return $data;
    }
    /**
     * @desc: Get All upcountry booking details
     * @param String $service_center_id
     * @param String $booking_id
     * @return boolean
     */
    function upcountry_booking_list($service_center_id, $booking_id, $sf_upcountry_rate, $is_paid, $flat_upcountry){
        $where = "";
        $having ="";
        if(!empty($service_center_id)){
            $where = " AND bd.assigned_vendor_id = '$service_center_id' ";
        }
        
        if(!empty($booking_id)){
            $having = " HAVING booking LIKE '%$booking_id%' ";
        }
        if($sf_upcountry_rate){
            $upcountry_rate = " sf_upcountry_rate ";
        } else {
            $upcountry_rate = " partner_upcountry_rate ";
        }
        if($is_paid == 1){
            $group = "AND booking_id = '$booking_id'";
            
        } else {
            $group = "AND upcountry_paid_by_customer = 0 GROUP BY bd.booking_date, bd.booking_pincode, $upcountry_rate  $having";
        }
         $sql = "SELECT CONCAT( '', GROUP_CONCAT( DISTINCT ( bd.booking_id ) ) , '' ) AS booking, "
                 
                . " upcountry_distance, bd.flat_upcountry,  upcountry_sf_payout, partner_upcountry_charges, "
                . " CASE WHEN (flat_upcountry = 1) THEN ( upcountry_sf_payout) ELSE( (round(($upcountry_rate * upcountry_distance ),2))) END AS upcountry_price,"
                . " COUNT(DISTINCT(bd.booking_id)) AS count_booking, "
                . " $upcountry_rate"
                . " FROM `booking_details` AS bd, service_centres AS s "
                . " WHERE  bd.is_upcountry = '1' "
                . " AND current_status IN ('"._247AROUND_PENDING."', '"._247AROUND_RESCHEDULED."','"._247AROUND_COMPLETED."') "
                . " AND s.id = assigned_vendor_id  $where"
                . " AND sub_vendor_id IS NOT NULL "
                . " AND bd.flat_upcountry  = $flat_upcountry "
                . " $group ";

        $query = $this->db->query($sql);
        if($query->num_rows > 0){
            
            return $query->result_array();
        } else {
            return false;
        }
        
    }
    /**
     * @desc: This returns City accorrding to sf id, pincode and is_upcountry 1
     * @param String $sf_id
     * @param String $pincode
     * @return Array
     */
    function get_vendor_upcountry($pincode, $service_id, $sf_id = false){
        $this->db->distinct();
        $this->db->select('Vendor_ID,City,service_centres.is_upcountry, service_centres.min_upcountry_distance');
        $this->db->from('vendor_pincode_mapping');
        $this->db->order_by('vendor_pincode_mapping.Pincode');
        $this->db->where('vendor_pincode_mapping.Pincode', $pincode);
        if($sf_id){
            $this->db->where('vendor_pincode_mapping.Vendor_ID', $sf_id);
        }
        $this->db->where('vendor_pincode_mapping.Appliance_ID', $service_id);
        $this->db->join('service_centres', 'service_centres.id = vendor_pincode_mapping.Vendor_ID');
        $this->db->where('service_centres.active', "1");
        $this->db->where('service_centres.on_off', "1");
        $query = $this->db->get();
       
        return $query->result_array();
    }
    /**
     * @desc: Return service center details who work upcountry
     * @return type
     */
    function get_upcountry_service_center(){
        $this->db->distinct();
        $this->db->select('*');
        $this->db->where('is_upcountry','1');
        $query = $this->db->get('service_centres');
        return $query->result_array();
    }
    
    /**
     * @desc Get distance between pincodes from table
     * 
     * Pincode 1 should be less than Pincode 2
     * 
     * @param type $pincode1
     * @param type $pincode2
     * @return type
     */
    function get_distance_between_pincodes($pincode1,$pincode2){
        if($pincode1 < $pincode2){ $dp1 = $pincode1;$dp2 = $pincode2;} 
        else { $dp1 = $pincode2;  $dp2 = $pincode1; }
        $this->db->distinct();
        $this->db->select('distance');
        $this->db->where('pincode1', $dp1);
        $this->db->where('pincode2', $dp2);
        $query= $this->db->get('distance_between_pincode');
        return $query->result_array();
    }
    
    /**
     * @desc Insert distance between pincodes
     * 
     * Pincode 1 should be less than Pincode 2
     * 
     * @param type $pincode1
     * @param type $pincode2
     * @param type $distance
     */
    function insert_distance($pincode1, $pincode2, $distance, $agent_id){       
        if ($pincode1 < $pincode2) {
            $dp1 = $pincode1;
            $dp2 = $pincode2;
        } else {
            $dp1 = $pincode2;
            $dp2 = $pincode1;
        }
        
        $data = array('pincode1' => $dp1, 'pincode2' => $dp2,'distance' => $distance,
            'agent_id'=> $agent_id, "create_date" => date('Y-m-d H:i:s'));
        
        $this->db->insert_ignore('distance_between_pincode',$data );
        return $this->db->insert_id();
    }
    /**
     * @desc: This method is used to know that partner provides upcountry for price tags of this booking
     * @param String $booking_id
     * @return Array|boolean
     */
    function is_upcountry_booking($booking_id){
        $this->db->_reserved_identifiers = array('*','CASE','WHEN');
        $this->db->select('booking_id, sc.is_upcountry, upcountry_customer_price, upcountry_vendor_price, upcountry_partner_price, flat_upcountry');
        $this->db->from('booking_unit_details AS ud');
        $this->db->join('service_centre_charges AS sc','ud.partner_id = sc.partner_id '
                . ' AND ud.price_tags = sc.service_category '
                . ' AND ud.`appliance_category` = sc.category '
                . ' AND ud.`appliance_capacity` = sc.capacity');
        $this->db->join('bookings_sources','CASE WHEN partner_type = "'.OEM.'" '
                . 'THEN (bookings_sources.partner_id = ud.partner_id AND sc.brand = ud.appliance_brand) '
                . 'ELSE (bookings_sources.partner_id = ud.partner_id) END ');
        $this->db->join('partners', 'partners.id = ud.partner_id');
        $this->db->where('booking_id', $booking_id);
        $this->db->where('partners.is_upcountry', '1');
        $this->db->where_in('sc.is_upcountry', array('1',NOT_UPCOUNTRY_PRICE_TAG));
        $query = $this->db->get();
       
        return $query->result_array();
    }
    
    function is_customer_pay_upcountry($booking_id){
        $this->db->_reserved_identifiers = array('*','CASE','WHEN');
        $this->db->select('booking_id, sc.is_upcountry, upcountry_customer_price, upcountry_vendor_price, upcountry_partner_price, flat_upcountry');
        $this->db->from('booking_unit_details AS ud');
        $this->db->join('service_centre_charges AS sc','ud.partner_id = sc.partner_id '
                . ' AND ud.price_tags = sc.service_category '
                . ' AND ud.`appliance_category` = sc.category '
                . ' AND ud.`appliance_capacity` = sc.capacity');
        $this->db->join('bookings_sources','CASE WHEN partner_type = "'.OEM.'" '
                . 'THEN (bookings_sources.partner_id = ud.partner_id AND sc.brand = ud.appliance_brand) '
                . 'ELSE (bookings_sources.partner_id = ud.partner_id) END ');
        $this->db->where('booking_id', $booking_id);
        $this->db->where_in('sc.is_upcountry', array('0'));
        $query = $this->db->get();
       
        return $query->result_array();
    }
    /**
     * @desc
     * @param String $booking_id
     * @return ARray
     */
    function get_upcountry_service_center_id_by_booking($booking_id){
        $this->db->select('service_center_id,assigned_vendor_id, booking_primary_contact_no,user_id, booking_details.partner_id, upcountry_partner_approved');
        $this->db->from('booking_details');
        $this->db->where('booking_id',$booking_id);
        //$this->db->where('assigned_vendor_id is NULL', NULL, FALSE);
        $this->db->join('sub_service_center_details','sub_service_center_details.id = booking_details.sub_vendor_id');
        $query = $this->db->get();
        return $query->result_array();
    }
    /**
     * @desc This method is used to return those upcountry bookings which are waiting to approve upcountry charges by Partner
     * @param int $partner_id
     * @param State $state
     * @param int $count
     * @param Array $where
     * @param Array $orderBy
     * @param int $limit
     * @param int $start
     * @return Array|boolean
     */
    function get_waiting_for_approval_upcountry_charges($partner_id,$state=0,$count = 0,$where=array(),$orderBy = NULL,$limit = -1,$start =-1){
        $this->db->distinct();
        if(!$count){
        $this->db->select('bd.booking_id,request_type,name,booking_primary_contact_no,services,'
                . ' appliance_brand,appliance_category, appliance_capacity, '
                . ' booking_address,bd.city, bd.booking_pincode, bd.state, '
                . 'bd.upcountry_distance, bd.partner_upcountry_rate, '
                . 'bd.upcountry_update_date,'
                . 'sbs.district as upcountry_district, sbs.pincode as upcountry_pincode');
        }
        else{
            $this->db->select('count(bd.booking_id) as count');
        }
        if($limit != -1){
            $this->db->limit($limit, $start);
        }
        if(!empty($where)){
            foreach($where as $key => $values){
                $this->db->where($key,$values);  
            } 
        }
        if($orderBy){
            $this->db->order_by($orderBy, FALSE);
        }
        $this->db->from('booking_details As bd');
        $this->db->where_in('current_status',array(_247AROUND_PENDING,_247AROUND_RESCHEDULED));
        $this->db->where('upcountry_partner_approved','0');
        $this->db->where('upcountry_paid_by_customer','0');
        $this->db->where('sub_vendor_id IS NOT NULL',NULL, FALSE);
        $this->db->where('is_upcountry','1');
        if(!empty($partner_id)){
            $this->db->where('bd.partner_id',$partner_id);
        }
        if($state == 1){
            $stateWhere['agent_filters.agent_id'] = $this->session->userdata('agent_id');
            $stateWhere['agent_filters.is_active'] = 1;
            $this->db->join('agent_filters', 'agent_filters.state =  bd.state', "left");
            $this->db->where($stateWhere, false);  
        }
        $this->db->join('booking_unit_details','bd.booking_id = booking_unit_details.booking_id');
        $this->db->join('users','bd.user_id = users.user_id');
        $this->db->join('services','bd.service_id = services.id');
        $this->db->join('sub_service_center_details as sbs', 'sbs.id = bd.sub_vendor_id');
        $query = $this->db->get();
        
        return $query->result_array();
    }
    
    function get_booking(){
        $this->db->distinct();
        $this->db->select('bd.booking_id');
        $this->db->from('booking_details as bd');
        $this->db->join("booking_unit_details as ud ", "ud.booking_id = bd.booking_id");
        $this->db->join("service_centres as sc ", "sc.id = bd.assigned_vendor_id");
        $this->db->where('ud_closed_date >=','2016-12-01');
        $this->db->where('ud_closed_date <','2017-01-01');
        $this->db->where('booking_status >=','Completed');
        $this->db->where('sc.is_upcountry',1);
       
        $query= $this->db->get();
        return $query->result_array();
    }
    
    function upcountry_partner_invoice($partner_id, $from_date, $to_date, $spare_requested_unit_id = ""){
        $sql = "SELECT CASE WHEN (bd.partner_id = '".PAYTM_ID."' ) "
                . "THEN (CONCAT( '', GROUP_CONCAT( DISTINCT ( SUBSTRING_INDEX(bd.order_id, '-', 1) ) ) , '' )) ELSE (CONCAT( '', GROUP_CONCAT( DISTINCT ( bd.order_id ) ) , '' )) END AS order_id,"
                . " CONCAT( '', GROUP_CONCAT( DISTINCT ( bd.booking_id ) ) , '' ) AS booking_id, "
                . " CASE when(upcountry_distance >".UPCOUNTRY_DISTANCE_CAP.") THEN (".UPCOUNTRY_DISTANCE_CAP.") ELSE (upcountry_distance) END AS upcountry_distance, "
                . " CASE when(upcountry_distance >".UPCOUNTRY_DISTANCE_CAP.") THEN (partner_upcountry_rate * ".UPCOUNTRY_DISTANCE_CAP." ) ELSE  (partner_upcountry_rate *upcountry_distance ) END AS upcountry_price,"
                . " COUNT(DISTINCT(bd.booking_id)) AS count_booking, partner_upcountry_rate, upcountry_pincode, services, taluk as city, bd.district as upcountry_city, booking_pincode, sub_vendor_id"
                . " FROM `booking_details` AS bd, booking_unit_details AS ud, services "
                . " WHERE ud.booking_id = bd.booking_id "
                . " AND bd.partner_id = '$partner_id' "
                . " AND sub_vendor_id IS NOT NULL "
                . " AND bd.is_upcountry = '1' "
                . " AND bd.service_id = services.id"
                . " AND bd.upcountry_paid_by_customer = 0 "
                . " AND ud.partner_invoice_id IS NULL "
                . " AND bd.upcountry_partner_approved = 1 "
                . " AND bd.upcountry_bill_to_partner = 1 "
                . " AND bd.upcountry_partner_invoice_id IS NULL"
                
                . " AND ( ( ud.ud_closed_date >= '$from_date' "
                . " AND ud.ud_closed_date < '$to_date' "
                . " AND bd.current_status = 'Completed' "
                . " ) $spare_requested_unit_id )"
                . " GROUP BY bd.booking_date, bd.booking_pincode, bd.service_id ";
        
        $query = $this->db->query($sql);
        if($query->num_rows > 0){
            $result = $query->result_array();
            $total_price = 0;
            $total_booking = 0;
            $total_distance = 0;
            foreach ($result as $key => $value) {
                
               
                if ($partner_id == SNAPDEAL_ID){
                    // Replace non upcountry city to area from india pincode table.
                    $area = $this->is_non_upcountry_city($partner_id,$value['city'], $value['booking_pincode']);
                    if(!empty($area)){
                        $result[$key]['city'] = $area;
                    }
                }
                
//                if($partner_id == PAYTM){
//                    $m_data = $this->generate_paytm_upcountry_distance($value['sub_vendor_id'], $value["upcountry_pincode"], $value['booking_pincode']);
//                    if(!empty($m_data)){
//                         if($m_data[0]['distance'] > 0){
//                            $result[$key]['upcountry_distance'] = $m_data[0]['distance'];
//                            $result[$key]['upcountry_price'] = $m_data[0]['distance'] * $value['partner_upcountry_rate'];
//                            $result[$key]['municipal_limit'] = $m_data[0]['municipal_limit'];
//                            $result[$key]['district'] = $m_data[0]['district'];
//                         } else {
//                             unset($result[$key]);
//                         }
//                         
//                    } else {
//                        $result[$key]['municipal_limit'] = "";
//                        $result[$key]['district'] = "";
//                    }
//                }
                if(isset($result[$key])){
                    $total_price += $result[$key]['upcountry_price'];
                    $total_booking += $value['count_booking'];
                    $total_distance += $result[$key]['upcountry_distance'];
                }
                
            }
            if(!empty($result)){
                $result[0]['total_upcountry_price'] = $total_price;
                $result[0]['total_booking'] = $total_booking;
                $result[0]['total_distance'] = round($total_distance, 0);
            }
           
            return $result;
            
        } else {
            return FALSE;
        }
    }
    
    function generate_paytm_upcountry_distance($sub_vendor_id, $upcountry_pincode, $booking_pincode){
        $where = array("sub_service_center_details.id" => $sub_vendor_id);
        $data = $this->get_municipal_limit($where, "municipal_limit, sub_service_center_details.district");
        if(!empty($data)){
            $distance_data = $this->get_distance_between_pincodes($booking_pincode,$upcountry_pincode);
            $upcountry_distance = ($distance_data[0]['distance'] - $data[0]["municipal_limit"]) * 2;
            $data[0]['distance'] = $upcountry_distance;
        } else {
            $data = array();
            $data[0]["municipal_limit"] = DEFAULT_PAYTM_MUNICIPAL_LIMIT;
            $distance_data = $this->get_distance_between_pincodes($booking_pincode,$upcountry_pincode);
            $upcountry_distance = ($distance_data[0]['distance'] - $data[0]["municipal_limit"]) * 2;
            $data[0]['distance'] = $upcountry_distance;
            $data[0]['district'] = DEFAULT_PAYTM_UPCOUNTRY_DISTRICT;
        }
        
        return $data;
    }
    
    function get_municipal_limit($where, $select){
        $this->db->distinct();
        $this->db->select($select);
        $this->db->from("sub_service_center_details");
        $this->db->where($where);
        $this->db->join("municipal_limit", "municipal_limit.district = sub_service_center_details.district");
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function get_data_from_municipal_limit($where,$select){
        $this->db->distinct();
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get("municipal_limit");
        return $query->result_array();
    }
    /**
     * @desc This is used to replace city to area for snapdeal upcountry booking
     * @param int $partner_id
     * @param String $city
     * @param int $pincode
     * @return boolean
     */
    function is_non_upcountry_city($partner_id, $city, $pincode){
        if($partner_id == 1){
            $non_upcountry_city = $this->get_non_upcountry_city(array('partner_id' => $partner_id, 'city' => $city));
            if(!empty($non_upcountry_city)){
                $data = $this->vendor_model->get_distict_details_from_india_pincode($pincode);
                return $data['area'];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    /**
     * @desc This is used to get non upcountry city for snapdeal
     * @param Array $where
     * @return String
     */
    function get_non_upcountry_city($where){
        $this->db->where($where);
        $query = $this->db->get('non_upcountry_city');
        return $query->result_array();
    }
    
    
    /**
     * @desc This method is used to update sub_service_center_details table via ajax call
     * @param array(),string
     * @return boolean
     */
    function update_sub_service_center_upcountry_details($data,$id){
        
        $this->db->where('id', $id);
        $result= $this->db->update('sub_service_center_details',$data);
        if($result){
            log_message('info', __METHOD__ . "=> Sub service center Details Updated ". $this->db->last_query());
            return true;
        }else{
            log_message('info', __METHOD__ . "=> Error In Updating Sub service center Details". $this->db->last_query());
            return false;
            
        }
        
    }
    /**
     * @desc This method is used to delete sub office details in sub_service_center_details table via ajax call
     * @param void()
     * @return string
     */
//    function delete_sub_service_center_upcountry_details($id){
//        $this->db->where('id',$id);
//        $result = $this->db->delete('sub_service_center_details');
//        return $result;
//    }
    
    /**
     * @desc This method is used to update the distance for given pincode
     * @param string
     * @return string 
     */
    function update_pincode_distance($pincode1,$pincode2,$distance){
        if($pincode1 < $pincode2){ $dp1 = $pincode1;$dp2 = $pincode2;} 
        else { $dp1 = $pincode2;  $dp2 = $pincode1; }
        $set = array('distance' => $distance);
        $this->db->where('pincode1',$dp1);
        $this->db->where('pincode2',$dp2);
        $this->db->update('distance_between_pincode',$set);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    function insert_sf_hq_level_distance_details($data){
        $this->db->insert_ignore('distance_between_pincode_sf_level', $data);
        
        return $this->db->insert_id();
    }
    
    function upcountry_pincode_services_sf_level($data){
       
         $this->db->insert_ignore_duplicate_batch('upcountry_pincode_services_sf_level', $data);
        
         return $this->db->insert_id();
    }
    
    function insert_upcountry_services_sf_level($data){
        $this->db->insert_ignore('upcountry_pincode_services_sf_level', $data);
        return $this->db->insert_id();
    }
    
    function get_upcountry_non_upcountry_district(){
        $sql = "SELECT DISTINCT(sub_service_center_details.district) as District,'Upcountry' as Flag,service_centres.min_upcountry_distance as Municipal_Limit "
                . "FROM sub_service_center_details JOIN service_centres ON service_centres.id = sub_service_center_details.service_center_id WHERE service_centres.active =1 "
                . "GROUP BY sub_service_center_details.district "
                . "UNION "
                . "SELECT DISTINCT(vendor_pincode_mapping.city) as District, 'Local' as Flag, ' ' as Municipal_Limit FROM vendor_pincode_mapping WHERE NOT EXISTS (SELECT 1 FROM "
                . "sub_service_center_details JOIN service_centres ON service_centres.id = sub_service_center_details.service_center_id WHERE service_centres.active =1 "
                . "AND sub_service_center_details.district = vendor_pincode_mapping.city ) ";
        return $query = $this->db->query($sql);
    }
    
    /**
     * @desc This is used to get unpaid upcountry charges for partner
     * This is will use to calculate partner prepaid amount
     * @param int $partner_id
     * @return boolean
     */
    function getupcountry_for_partner_prepaid($partner_id){
        $sql = "SELECT "
                . " CONCAT( '', GROUP_CONCAT( DISTINCT ( bd.booking_id ) ) , '' ) AS booking_id, "
                . " CASE when(upcountry_distance >".UPCOUNTRY_DISTANCE_CAP.") THEN (".UPCOUNTRY_DISTANCE_CAP.") ELSE (upcountry_distance) END AS upcountry_distance, "
                . " CASE when(upcountry_distance >".UPCOUNTRY_DISTANCE_CAP.") THEN (partner_upcountry_rate * ".UPCOUNTRY_DISTANCE_CAP." ) ELSE  (partner_upcountry_rate *upcountry_distance ) END AS upcountry_price,"
                . " COUNT(DISTINCT(bd.booking_id)) AS count_booking, partner_upcountry_rate"
                . " FROM `booking_details` AS bd "
                . " WHERE  bd.partner_id = '$partner_id' "
                . " AND sub_vendor_id IS NOT NULL "
                . " AND bd.is_upcountry = '1' "
                . " AND bd.create_date > '2018-01-01' "
                . " AND bd.current_status IN ('Completed', 'Pending', 'Rescheduled') "
                . " AND bd.upcountry_paid_by_customer = 0 "
                . " AND bd.upcountry_partner_invoice_id IS NULL "
                . " AND bd.upcountry_partner_approved = 1 "
                . " GROUP BY bd.booking_date, bd.booking_pincode, bd.service_id ";
        $query = $this->db->query($sql);

        if($query->num_rows > 0){
            $result = $query->result_array();
            $total_price = 0;
            $total_booking = 0;
            $total_distance = 0;
            foreach ($result as $key => $value) {
                
                if(isset($result[$key])){
                    $total_price += $result[$key]['upcountry_price'];
                    $total_booking += $value['count_booking'];
                    $total_distance += $result[$key]['upcountry_distance'];
                }
                
            }
            $result[0]['total_upcountry_price'] = $total_price;
            $result[0]['total_booking'] = $total_booking;
            $result[0]['total_distance'] = round($total_distance, 0);
            
            return $result;
        } else {
            return FALSE;
        }
    }
    
    function getpincode_upcountry_local(){
        $sql = "SELECT distinct pincode as 'Pincode', district as 'District', case when is_upcountry =1 THEN ('Upcountry') ELSE 'Local' END as 'Upcountry/Local', distance as Distance FROM `upcountry_pincode_services_sf_level`";
        return $this->db->query($sql);
    }
    
    function truncate_upcountry_sf_level_table(){
        $sql = "TRUNCATE TABLE `upcountry_pincode_services_sf_level`";
        $this->db->query($sql);
    }
    
    
}