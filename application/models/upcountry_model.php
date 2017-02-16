<?php
/**
 * 247Around provides extra transportation charges to SF for those booking 
 * whose distance is greater than 50 Km(Up & Down) between Customer and Vendor region.
 * 
 * We will not provide upcountry charges when distance is greater than 150KM(Up & Down).
 * 
 * ............................. For Free Booking to SF......................................
 * 
 * To display upcountry booking in SF or Admin Panel, We will combine those upcountry 
 * booking whose booking date, pincode and SF Id is same and current_status of 
 * these bookings must be Pending, Rescheduled or Completed.
 * 
 * While create SF invoice, We will combine those bookings whose booking date 
 * pincode and SF id is same and current status is completed.
 * 
 * ........................For Free Booking to Partner............................
 * 
 * While create a new Invoice to get upcountry booking details, we will combine 
 * those booking whose  booking date, pincode and Appliance Id Is same.
 * 
 * ..............For Cash Booking to SF OR Partner............................
 * We will not combined upcountry booking for Cash Booking 
 * 
 * To calculate upcountry distance, we created a action_upcountry_booking() method.
 * In this method we need to pass booking Id.
 * 
 * To assign upcountry booking, first we need to check value of 
 * is_upcountry field of partners table must be 1. 
 * 
 * then we will get city of booking pincode from vendor pincode mapping table 
 * And search SF header quater pincode of that city from sub_service_center_table
 * 
 * Now, we will calculate distance between booking pincode and SF district head quater pincode.
 * If total up & down distance is less than 50 KM then we will not going to mark upcountry for this booking.
 * 
 * If total up & down distance is greater than 150 KM then we will mark to upcountry but not update distance.
 * We will update distance and mark distric office manualy from Form.
 * 
 * Google provides free API to calculate distance between to region.
 * We will use calculate_distance_between_pincode() method to call Google API. 
 * 
 * In this method, we will pass booking pincode, booking city, SF district pincode, SF booking city as parameter 
 * before call Google API, we need to remove space with %20 in the booking city 
 * because if we will call api with space then it return Bad url request.
 * 
 * Its compulsary to call Google API with city, pincode, India 
 * because some india's pincode is same as singapore Or U.S pincode
 * 
 * While Assign booking to SF, if we can not calculate distance (means failed to calculate distance)
 * then send email to Anuj@247around.com Or nits@247around.com with booking Pincode and SF id
 * 
 * When failed to calculate distance then we will update is_upcountry as 1
 * but not assign sub vendor is ( means its must be NULL).
 * 
 * We will shown failed upcountry booking in the Editable table. 
 * We get failed booking when is_upcountry is 1 and sub_vendor_id is NULL.  
 *    
 */
class Upcountry_model extends CI_Model {
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
        $url = "http://maps.googleapis.com/maps/api/distancematrix/json?origins=$city_1,$postcode1,India&destinations=$city_2,$postcode2,India&mode=driving&language=en-EN&sensor=false";
        $data = file_get_contents($url);
        $result = json_decode($data, true);
        if(!empty($data)){
            foreach($result['rows'] as $distance) {

                if($distance['elements'][0]['status'] == "OK"){
                    log_message('info', __FUNCTION__.' Distance Found');

                  return $distance['elements'][0];

                } else {
                    log_message('info', __FUNCTION__.' Distance Not Found pincode1 '. $postcode1. " ". $postcode2);
                    
                    return FALSE;
                }
            }
        } else {
            log_message('info', __FUNCTION__.' Distance Not Found pincode1 '. $postcode1. " ". $postcode2);

            return FALSE;
        }
    }
    
    function action_upcountry_booking($booking_city,$booking_pincode, $vendor_details){
        log_message('info', __METHOD__ );
        $error = array();
        $same_pincode_vendor = array();
        $upcountry_vendor_details = array();
        $distance = false;
        foreach ($vendor_details as $value) {
            $where1 = array('service_center_id' => $value['vendor_id'], 
                'district'=> $value['city']);
            $res_sb = $this->get_sub_service_center_details($where1);
           
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
                            $distance = (round($is_distance1['distance']['value'] / 1000, 2));
                            $this->insert_distance($booking_pincode, $res_sb[0]['pincode'], $distance);
                             log_message('info', __FUNCTION__ ." Insert distance & pincode " . $booking_pincode.
                                     $res_sb[0]['pincode']. $distance);
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
                        $error['vendor_id'] = "";
                       
                    }
                } else {
                    log_message('info', __FUNCTION__ ." Not Upcountry Booking ". $value['vendor_id']
                            ." booking_pincode ". $booking_pincode);
                    $same_pincode_vendor['vendor_id'] = $value['vendor_id'];
                    $same_pincode_vendor['message'] = NOT_UPCOUNTRY_BOOKING;
                    break;
                }
            } else {
                log_message('info', __FUNCTION__ ." Not Upcountry Booking ". $value['vendor_id']
                            ." booking_pincode ". $booking_pincode);
                $same_pincode_vendor['vendor_id'] = $value['vendor_id'];
                $same_pincode_vendor['message'] = NOT_UPCOUNTRY_BOOKING;
                break;
            }
        }
        
        if(!empty($same_pincode_vendor)){
            log_message('info', __FUNCTION__ ." Not upcountry booking" );
            return $same_pincode_vendor;
            
        } else if(!empty($upcountry_vendor_details)){
           
            if(count($upcountry_vendor_details) == 1){
                log_message('info', __FUNCTION__ ." mark Upcountry" );
                return $this->mark_upcountry_vendor($upcountry_vendor_details[0]);

            } else {
                log_message('info', __FUNCTION__ ." Got to calculate min disatance" );
                $get_data = $this->get_minimum_upcountry_price($upcountry_vendor_details);
                return $this->mark_upcountry_vendor($get_data);
            }
        } else if(!empty($error)){
            log_message('info', __FUNCTION__ ." upcountry error ".print_r($error) );
            return $error;
        }
    }
    
    function mark_upcountry_vendor($upcountry_vendor_details) {
         log_message('info', __FUNCTION__ );
       
        $up_data = array();
        if ($upcountry_vendor_details['upcountry_distance'] < (UPCOUNTRY_MIN_DISTANCE + UPCOUNTRY_MIN_DISTANCE*0.1)) {
           
            log_message('info', __FUNCTION__ ." Not Upcountry Booking ". print_r($upcountry_vendor_details, true) );
            $up_data['vendor_id'] = $upcountry_vendor_details['vendor_id'];
            $up_data['message'] = NOT_UPCOUNTRY_BOOKING;

            
        } else if ($upcountry_vendor_details['upcountry_distance'] > (UPCOUNTRY_MIN_DISTANCE + UPCOUNTRY_MIN_DISTANCE*0.1)
                && $upcountry_vendor_details['upcountry_distance'] < UPCOUNTRY_DISTANCE_THRESHOLD) {

            $up_data = array('upcountry_pincode' => $upcountry_vendor_details['pincode'],
                'upcountry_distance' => ($upcountry_vendor_details['upcountry_distance'] - UPCOUNTRY_MIN_DISTANCE),
                'sf_upcountry_rate' => $upcountry_vendor_details['sf_upcountry_rate'],
                'sub_vendor_id' => $upcountry_vendor_details['sub_vendor_id'],
                'vendor_id' => $upcountry_vendor_details['vendor_id'],
                'is_upcountry' => 1);

            $up_data['message'] = UPCOUNTRY_BOOKING;
            log_message('info', __FUNCTION__ ." Upcountry Booking ". print_r($up_data, true) );
           
        } else if($upcountry_vendor_details['upcountry_distance'] > UPCOUNTRY_DISTANCE_THRESHOLD) {
           
            $up_data = array('upcountry_pincode' => $upcountry_vendor_details['pincode'],
                'upcountry_distance' => ($upcountry_vendor_details['upcountry_distance'] - UPCOUNTRY_MIN_DISTANCE),
                'sf_upcountry_rate' => $upcountry_vendor_details['sf_upcountry_rate'],
                'sub_vendor_id' => $upcountry_vendor_details['sub_vendor_id'],
                 'vendor_id' => $upcountry_vendor_details['vendor_id'],
                'is_upcountry' => 1);
           $up_data['message'] = UPCOUNTRY_LIMIT_EXCEED; 
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
        $this->db->select("booking_id, is_upcountry,upcountry_pincode,sub_vendor_id,"
                . "sf_upcountry_rate,upcountry_distance, assigned_vendor_id");
        $this->db->where("booking_details.is_upcountry", '1');
        $this->db->where("sub_vendor_id IS NULL", NULL, false);
        $query = $this->db->get("booking_details");
        return $query->result_array();
    }
    /**
     * @desc: This is used to combined booking id on the basis of booking date and booking Pincode while create FOC invoice
     * And Also upcountry_paid_by_customer must be 0 means Cutomer did not pay upcountry charges
     * @param String $vendor_id
     * @param String $from_date
     * @param String $to_date
     * @return boolean
     */
    function upcountry_foc_invoice($vendor_id, $from_date, $to_date){
        $sql = "SELECT CONCAT( '', GROUP_CONCAT( DISTINCT ( bd.booking_id ) ) , '' ) AS booking,"
                . " round(SUM(upcountry_distance)/COUNT(DISTINCT(bd.booking_id)),2) AS upcountry_distance, "
                . " assigned_vendor_id, "
                . " round((sf_upcountry_rate * round(SUM(upcountry_distance)/COUNT(DISTINCT(bd.booking_id)),2) ),2) AS upcountry_price,"
                . " COUNT(DISTINCT(bd.booking_id)) AS count_booking, sf_upcountry_rate"
                . " FROM `booking_details` AS bd, booking_unit_details AS ud "
                . " WHERE ud.booking_id = bd.booking_id "
                . " AND bd.assigned_vendor_id = '$vendor_id' "
                . " AND ud.ud_closed_date >= '$from_date' "
                . " AND ud.ud_closed_date < '$to_date' "
                . " AND pay_to_sf = '1' "
                . " AND sub_vendor_id IS NOT NULL "
                . " AND bd.is_upcountry = '1' "
                . " AND bd.current_status = 'Completed' "
                . " AND bd.upcountry_paid_by_customer = 0 "
                . " GROUP BY bd.booking_date, bd.booking_pincode ";
        
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
     * 
     * @param String $vendor_id
     * @param String $from_date
     * @param String $to_date
     * @return Array
     */
    function upcountry_cash_invoice($vendor_id, $from_date, $to_date){
        $sql = "SELECT DISTINCT ( bd.booking_id)"
                . " upcountry_distance, "
                . " assigned_vendor_id, "
                . " round((customer_paid_upcountry_charges * ".basic_percentage." ),2) AS upcountry_price,"
                . " COUNT(DISTINCT(bd.booking_id)) AS count_booking "
                . " FROM `booking_details` AS bd, booking_unit_details AS ud "
                . " WHERE ud.booking_id = bd.booking_id "
                . " AND bd.assigned_vendor_id = '$vendor_id' "
                . " AND ud.ud_closed_date >= '$from_date' "
                . " AND ud.ud_closed_date < '$to_date' "
                . " AND pay_to_sf = '1' "
                . " AND sub_vendor_id IS NOT NULL "
                . " AND bd.is_upcountry = '1' "
                . " AND current_status = 'Completed' "
                . " AND bd.upcountry_paid_by_customer = 1 ";
        
        $query = $this->db->query($sql);
        return $query->result_array();
        
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
                 
                    . " round(SUM(upcountry_distance)/COUNT(DISTINCT(bd.booking_id)),2) AS upcountry_distance,"
                    . " CASE WHEN (upcountry_paid_by_customer = 0 OR upcountry_paid_by_customer IS NULL) "
                    . " THEN ((round((sf_upcountry_rate * round(SUM(upcountry_distance)/COUNT(DISTINCT(bd.booking_id)),2) ),2)) ) "
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
                    . " CASE WHEN (upcountry_paid_by_customer = 0 OR upcountry_paid_by_customer IS NULL) THEN ( bd.booking_date AND bd.booking_pincode ) "
                    . " ELSE (bd.booking_id) END ";

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
    function upcountry_booking_list($service_center_id, $booking_id, $sf_upcountry_rate, $is_paid){
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
            $group = "AND upcountry_paid_by_customer = 0 GROUP BY bd.booking_date, bd.booking_pincode $having";
        }
         $sql = "SELECT CONCAT( '', GROUP_CONCAT( DISTINCT ( bd.booking_id ) ) , '' ) AS booking, "
                 
                . " round(SUM(upcountry_distance)/COUNT(DISTINCT(bd.booking_id)),2) AS upcountry_distance,"
                . " (round(($upcountry_rate * round(SUM(upcountry_distance)/COUNT(DISTINCT(bd.booking_id)),2) ),2)) AS upcountry_price,"
                . " COUNT(DISTINCT(bd.booking_id)) AS count_booking, "
                . " $upcountry_rate"
                . " FROM `booking_details` AS bd, service_centres AS s "
                . " WHERE  bd.is_upcountry = '1' "
                . " AND current_status IN ('Pending', 'Rescheduled','Completed') "
                . " AND s.id = assigned_vendor_id  $where"
                . " AND sub_vendor_id IS NOT NULL "
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
        $this->db->select('Vendor_ID,City,service_centres.is_upcountry');
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
    function insert_distance($pincode1, $pincode2, $distance){       
        if ($pincode1 < $pincode2) {
            $dp1 = $pincode1;
            $dp2 = $pincode2;
        } else {
            $dp1 = $pincode2;
            $dp2 = $pincode1;
        }
        $this->db->insert('distance_between_pincode', array('pincode1' => $dp1, 'pincode2' => $dp2,
            'distance' => $distance));
    }
    /**
     * @desc: This method is used to know that partner provides upcountry for price tags of this booking
     * @param String $booking_id
     * @return Array|boolean
     */
    function is_upcountry_booking($booking_id){
        $this->db->select('booking_id, is_upcountry');
        $this->db->from('booking_unit_details AS ud');
        $this->db->join('bookings_sources AS bs','ud.partner_id = bs.partner_id');
        $this->db->join('service_centre_charges AS sc','bs.price_mapping_id = sc.partner_id '
                . ' AND ud.price_tags = sc.service_category '
                . ' AND ud.`appliance_category` = sc.category '
                . ' AND ud.`appliance_capacity` = sc.capacity');
        $this->db->where('booking_id', $booking_id);
        $this->db->where('sc.is_upcountry', '1');
        $query = $this->db->get();
        return $query->result_array();
    }
    /**
     * @desc
     * @param String $booking_id
     * @return ARray
     */
    function get_upcountry_service_center_id_by_booking($booking_id){
        $this->db->select('service_center_id, booking_primary_contact_no,user_id');
        $this->db->from('booking_details');
        $this->db->where('booking_id',$booking_id);
        $this->db->join('sub_service_center_details','sub_service_center_details.id = booking_details.sub_vendor_id');
        $query = $this->db->get();
        return $query->result_array();
    }
    /**
     * @desc This method is used to return those upcountry bookings which are waiting to approve upcountry charges by Partner
     * @param type $partner_id
     * @return Array
     */
    function get_waiting_for_approval_upcountry_charges($partner_id){
        
        $this->db->select('bd.booking_id,request_type,name,booking_primary_contact_no,services,'
                . ' appliance_brand,appliance_category, appliance_capacity, '
                . ' booking_address,bd.city, bd.booking_pincode, bd.state, bd.upcountry_distance');
        $this->db->from('booking_details As bd');
        $this->db->where_in('current_status',array(_247AROUND_PENDING,_247AROUND_RESCHEDULED));
        $this->db->where('upcountry_partner_approved','0');
        $this->db->where('upcountry_paid_by_customer','0');
        $this->db->where('is_upcountry','1');
        if(!empty($partner_id)){
            $this->db->where('bd.partner_id',$partner_id);
        }
        $this->db->join('booking_unit_details','bd.booking_id = booking_unit_details.booking_id');
        $this->db->join('users','bd.user_id = users.user_id');
        $this->db->join('services','bd.service_id = services.id');
        $query = $this->db->get();
        
        return $query->result_array();
    }
    
    function get_booking($partner_id){
        $this->db->select('booking_id');
        $this->db->where('create_date >=','2017-01-01');
        $this->db->where('partner_id',$partner_id);
        $query= $this->db->get('booking_details');
        return $query->result_array();
    }
    
    
}