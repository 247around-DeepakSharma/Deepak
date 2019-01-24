<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * This class is designed for update data in bulk
 * We create new flow to add or update the data , but initialy we need to update or create new data
 * eg - we create a new table booking_tat in which we save tat data by spliting in legs 
 * 1) we create the function and add that function in flow
 * 2) but we need to update old data as well for this 
 * so this controller will keep all those which willl update the data in bulk
 */
class update_data_in_bulk extends CI_Controller {
    
    function __Construct() {
        parent::__Construct();
        $this->load->library('miscelleneous');
        $this->load->model('reusable_model');
        $this->load->model('vendor_model');
    }
    
    function get_missing_pincodes_state_city(){
       $data =  $this->reusable_model->get_search_result_data("sf_not_exist_booking_details","DISTINCT pincode as pincode",array("(state = '' OR state IS NULL)"=>NULL),NULL,NULL,NULL,NULL,NULL,$groupBY=array('pincode'));
     // $length = count($data);\
        $length = 6;
       for($i = 0; $i<$length-1;$i++){
           if($data[$i]['pincode']) {
               echo $data[$i]['pincode'];
               echo "<br>";
               $pincodeJsonData =  $this->miscelleneous->google_map_address_api($data[$i]['pincode']);
               $pincodeArray = json_decode($pincodeJsonData,true);
               if($pincodeArray['status'] == 'OK'){
                   // Insert State City in India Pincode
                    $pincode = $data[$i]['pincode'];
                    $state = $pincodeArray['results']['0']['address_components'][2]['long_name'];
                    $city = $pincodeArray['results']['0']['address_components'][1]['long_name'];
                    $this->miscelleneous->process_if_pincode_valid($pincode,$state,$city);
                   //Update State and City in sf_not_exist_booking_details
                    $resultTemp = $this->reusable_model->get_rm_for_pincode($pincode);
                    $notFoundSfArray['rm_id'] = $resultTemp[0]['rm_id'];
                    $notFoundSfArray['state'] = $resultTemp[0]['state'];
                    $notFoundSfArray['city'] = $city;
                    $this->vendor_model->update_not_found_sf_table(array("pincode"=>$pincode),$notFoundSfArray);
               }
               else if($pincodeArray['status'] == 'ZERO_RESULTS'){
                   //Delete Entry from sf_not_exist_booking_details
                    $this->vendor_model->update_not_found_sf_table(array("pincode"=>$pincode),array("is_pincode_valid" => 0));
               }
           }
       }
    }
}
