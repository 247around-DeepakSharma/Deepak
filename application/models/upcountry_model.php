<?php

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
     * @desc:  Calculate diatnce between two Pincodes
     * @param String $postcode1
     * @param String $postcode2
     * @return boolean
     */
    function calculate_distance_between_pincode($postcode1, $postcode2){
        log_message('info', __FUNCTION__.' Calculate Pincdode1 '. $postcode1." Pincode 2". $postcode2 );
        $url = "http://maps.googleapis.com/maps/api/distancematrix/json?origins=$postcode2,India&destinations=$postcode1,India&mode=driving&language=en-EN&sensor=false";

        $data = file_get_contents($url);
        $result = json_decode($data, true);
        foreach($result['rows'] as $distance) {
            
            if($distance['elements'][0]['status'] == "OK"){
                log_message('info', __FUNCTION__.' Distance Found');
               
              return $distance['elements'][0];
              
            } else {
                log_message('info', __FUNCTION__.' Distance Not Found pincode1 '. $postcode1. " ". $postcode2);
               
                return FALSE;
            }
        } 
    }
    /**
     * @desc: Upadte booking for Upcountry
     * We need to check, is partner provide upcountry charges and also check is service center sub offices
     * @param String $partner_id
     * @param String $service_center_id
     * @param String $booking_pincode
     * @param String $booking_id
     * @return boolean|array
     */
    function action_upcountry_booking($partner_id, $service_center_id, $booking_pincode, $booking_id){
        log_message('info', __FUNCTION__.' Partner Id '. $partner_id." Service_center_id".
                $service_center_id. " Booking_pincode ". $booking_pincode. " Booking_id". $booking_id );
        $res_partner = $this->get_partner_provide_upcountry($partner_id);
        $failed_data = array();
        $distance_data = array();
        if($res_partner){
            $where1 = array('service_center_id' => $service_center_id);
            $res_sb = $this->get_sub_service_center_details($where1);
            if($res_sb){
                foreach ($res_sb as $value) {
                   
                    $is_distance = $this->calculate_distance_between_pincode($booking_pincode, $value['pincode']);
                    if($is_distance){
                        $distance = round($is_distance['distance']['value']/1000,0);
                       
                        array_push($distance_data, array('upcountry_pincode'=> $value['pincode'],
                            'upcountry_distance'=> $distance, 
                            'upcountry_rate'=> $value['upcountry_rate'],
                            'sub_vendor_id'=> $value['id'],
                            'upcountry_price' => $value['upcountry_rate'] * $distance));
                    } else {
                        $failed = array("booking_id"=>$booking_id, "booking_pincode"=> $booking_pincode, " service center pincode"=>$value['pincode']);
                        $this->booking_model->update_booking($booking_id, array('is_upcountry'=> '1'));
                        array_push($failed_data, $failed);
                    }
                }
                $min_price_data = $this->get_minimum_upcountry_price($distance_data);
                
                if($min_price_data['upcountry_distance'] < UPCOUNTRY_DISTANCE_THRESHOLD){
                    $min_price_data['all_upcountry_pincode_details'] = json_encode($distance_data, true);
                    $this->booking_model->update_booking($booking_id,$min_price_data);
                    
                } else {
                    log_message('info', __FUNCTION__.' Minimum Price Greater Than Thresold');
                    $failed = array("booking_id"=>$booking_id, "booking_pincode"=> $booking_pincode, " service center pincode"=>$min_price_data['upcountry_pincode']);
                    $this->booking_model->update_booking($booking_id, array('is_upcountry'=> '1', 
                        'all_upcountry_pincode_details'=>json_encode($distance_data, true)));
                    array_push($failed_data, $failed);
                }
               
                if(!empty($failed_data)){
                    log_message('info', __FUNCTION__.' Upcountry Failed for booking id '. print_r($failed_data, true));
                    return $failed_data;
                    
                } else {
                    log_message('info', __FUNCTION__.' Upcountry Successfully Assign for booking id '. $booking_id);
                    return "Success";
                }
                
            } else{
               log_message('info', __FUNCTION__.' Service Center doest not work upcountry service center id '. $service_center_id);
               return false;
            }
        } else {
            log_message('info', __FUNCTION__.' Partner doest not work upcountry Partner id '. $partner_id);
            return  false;
        }
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
      
        $this->db->select('*');
        $this->db->where($where);
        $query = $this->db->get('sub_service_center_details');
        return $query->result_array();
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
                
                $min_price['upcountry_price'] = $value['upcountry_price'];
                $min_price['upcountry_pincode'] = $value['upcountry_pincode'];
                $min_price['upcountry_rate'] = $value['upcountry_rate'];
                $min_price['sub_vendor_id'] = $value['sub_vendor_id'];
                $min_price['upcountry_distance'] = $value['upcountry_distance'];
                $min_price['is_upcountry'] = 1;
            }
        }
       
        return $min_price;
    }
    /**
     * @desc: This is used to return failed upcoutry booking details
     * @return boolean
     */
    function get_upcountry_failed_details(){
        $this->db->select("booking_id, is_upcountry,upcountry_pincode,sub_vendor_id,"
                . "upcountry_rate,upcountry_distance,upcountry_price, assigned_vendor_id");
        $this->db->where("is_upcountry", '1');
        $this->db->where("sub_vendor_id IS NULL", NULL, false);
        $query = $this->db->get("booking_details");
        return $query->result_array();
    }
    /**
     * @desc: This is used to combined booking id on the basis of booking date and booking Pincode while create FOC invoice
     * @param String $vendor_id
     * @param String $from_date
     * @param String $to_date
     */
    function upcountry_in_invoice($vendor_id, $from_date, $to_date){
        $sql = "SELECT CONCAT( '', GROUP_CONCAT( DISTINCT ( bd.booking_id ) ) , '' ) AS booking,"
                . " upcountry_distance, assigned_vendor_id, round((upcountry_rate * upcountry_distance )/1.15,2) AS upcountry_price,"
                . " COUNT(DISTINCT(bd.booking_id)) AS count_booking, round((upcountry_rate)/1.15,2) AS upcountry_rate "
                . " FROM `booking_details` AS bd, booking_unit_details AS ud "
                . " WHERE ud.booking_id = bd.booking_id "
                . " AND bd.assigned_vendor_id = '$vendor_id' "
                . " AND ud.ud_closed_date >= '$from_date' "
                . " AND ud.ud_closed_date < '$to_date' "
                . " AND ud.around_to_vendor >0 "
                . " AND ud.vendor_to_around =0 "
                . " AND pay_to_sf = '1' "
                . " AND sub_vendor_id IS NOT NULL "
                . " AND is_upcountry = '1' "
                . " GROUP BY bd.booking_date ";
        
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
            $result[0]['total_distance'] = $total_distance;
            
            return $result;
            
        } else {
            return FALSE;
        }
    }
    
}