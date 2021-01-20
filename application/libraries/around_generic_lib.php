<?php
/*
 * This class use to check authentication using header for coming request 
 */

class around_generic_lib {
   
    var $My_CI;

    function __Construct() {
    $this->My_CI = & get_instance();

    $this->My_CI->load->library('PHPReport');
    $this->My_CI->load->library('email');
    $this->My_CI->load->library('s3');
    $this->My_CI->load->library('form_validation');
    $this->My_CI->load->library("miscelleneous");
    $this->My_CI->load->library("booking_creation_lib");
    $this->My_CI->load->library("notify");
    $this->My_CI->load->helper('download');
    $this->My_CI->load->helper(array('form', 'url'));
    $this->My_CI->load->model('employee_model');
    $this->My_CI->load->model('booking_model');
    $this->My_CI->load->model('reporting_utils');
    $this->My_CI->load->model('booking_request_model');
    $this->My_CI->load->model('warranty_model');
    $this->My_CI->load->model('vendor_model');
    $this->My_CI->load->model('dealer_model');
    $this->My_CI->load->model('partner_model');
    $this->My_CI->load->model('indiapincode_model');
    $this->My_CI->load->library('paytm_payment_lib');
    $this->My_CI->load->library('trackingmore_api');
    }



    /**
     *  @desc : This function is to get all states.
     *
     *  All the distinct states of India in Ascending order From Table state_code
     *
     *  @param : void
     *  @return : array of states
     *  @author : Abhishek Awasthi
     */


    function  getAllStates(){
        $result = array();
        $response  = $this->My_CI->vendor_model->get_allstates();
        if(!empty($response)){
            $result['data'] = $response;
            $result['message'] = STATES_FOUND_MSG; 
            $result['code'] = STATES_FOUND_MSG_CODE;
        }else{
            $result['data'] = '';
            $result['message'] = STATES_FOUND_MSG_ERR;
            $result['code'] = STATES_FOUND_MSG_ERR_CODE; 
        }
        return $result;

    }




    /**
     *  @desc : This function is to get all cities of state.
     *
     *  
     *
     *  @param : void
     *  @return : array of cities
     *  @author : Abhishek Awasthi
     */


    function getStateCities($state_code){
        $result = array();
        $response  = $this->My_CI->indiapincode_model->getStateCities($state_code);
        if(!empty($response)){
            $result['data'] = $response;
            $result['message'] = CITIES_FOUND_MSG; 
            $result['code'] = API_SUCCESS_CODE;
        }else{
            $result['data'] = array();
            $result['message'] = CITIES_FOUND_MSG_ERR;
            $result['code'] = CITIES_FOUND_MSG_ERR_CODE; 
        }
        return $result;

    }



          /*
     * @Desc - This function used to get spares of booking     
     * @param - $booking_id
     * @response - Array
     * @Author - Abhishek Awasthi
     */

    function getSpareDetailsOfBooking($booking_id){
/*  If Shipped is empty or NULL Show Requested Data */
        $sp_details = $this->My_CI->partner_model->get_spare_parts_by_any("spare_parts_details.part_warranty_status,IFNULL(spare_parts_details.parts_shipped,spare_parts_details.parts_requested) as parts_shipped ,IF(spare_parts_details.shipped_parts_type=' ',spare_parts_details.parts_requested_type,spare_parts_details.shipped_parts_type) as shipped_parts_type,spare_parts_details.shipped_quantity,spare_parts_details.quantity,spare_parts_details.awb_by_partner,spare_parts_details.courier_name_by_partner,spare_parts_details.remarks_by_partner,spare_parts_details.awb_by_sf,spare_parts_details.courier_name_by_sf,spare_parts_details.challan_approx_value,spare_parts_details.awb_by_wh,spare_parts_details.courier_name_by_wh,spare_parts_details.status,spare_parts_details.remarks_by_sc,model_number,serial_number,date_of_request,spare_cancellation_reason", array('booking_id' => $booking_id));

        return $sp_details;
    }


     /*
     * @Desc - This function used to get booking data like spares , units ,etc
     * @param - $booking_id
     * @response - Array
     * @Author - Abhishek Awasthi
     */

    function getBookingDetails($booking_id="",$appliance_id,$is_repeat,$show_all_capacity=FALSE){
        $booking = array();
        if(!empty($booking_id)){
          $booking_history = $this->My_CI->booking_creation_lib->get_edit_booking_form_helper_data($booking_id,$appliance_id,$is_repeat, $show_all_capacity); 
          $booking['booking_history'] =  $booking_history; 
          return $booking; 
        }else{
            return $booking;
        }
        

    }


     /*
     * @Desc - This function used to get tracking Data 
     * @param - $carrier_code,$awb_number
     * @response - Array
     * @Author - Abhishek Awasthi
     */

    function getTrackingData($carrier_code,$awb_number){
         $track_api_data = $this->My_CI->trackingmore_api->getRealtimeTrackingResults($carrier_code,$awb_number);
         return  $track_api_data;
    }



    /*
     * @Desc - This function used to get dealer state mapping Data
     * @param - $dealer
     * @response - Array
     * @Author - Abhishek Awasthi
     */

    function getDealerStateMapped($dealer){
         $states = $this->dealer_model->getDealerStates($dealer);
         return  $states;
    }

    /*
     * @Desc - This function used to get dealer state cities mapping Data
     * @param - $entity, $state_code
     * @response - Array
     * @Author - Abhishek Awasthi
     */

    function getDealerStateCitiesMapped($entity, $state_code){

         $states = $this->dealer_model->getDealerStatesCities($entity, $state_code);
         return  $states;

    }


    /*
     * @Desc - This function used to get spare tracking history
     * @param - $spare_id
     * @response - Array
     * @Author - Abhishek Awasthi
     */
    function getSpareTrackingHistory($spare_id){

        $data = array();
        if (!empty($spare_id)) {
            $data['spare_history'] = $this->My_CI->partner_model->get_spare_state_change_tracking("spare_state_change_tracker.id,spare_state_change_tracker.spare_id,spare_state_change_tracker.action,spare_state_change_tracker.remarks,spare_state_change_tracker.agent_id,spare_state_change_tracker.entity_id,spare_state_change_tracker.entity_type, spare_state_change_tracker.create_date", array('spare_state_change_tracker.spare_id' => $spare_id), false);
        }else{
            $data['spare_history'] = array();
        }

        return $data;
    }



  /*
     * @Desc - This function is used to get escalation reasons 
     * @param - 
     * @response - json
     * @Author  - Abhishek Awasthi
  */


function getEscalationReason($entity_type){

$data = array();
$data['escalation_reason'] = $this->My_CI->vendor_model->getEscalationReason(array('entity'=>$entity_type,'active'=> '1','process_type'=>'escalation'));

return $data;

}


}
