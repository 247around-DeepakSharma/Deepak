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
       $length = count($data);
       for($i = 0; $i<$length-1;$i++){
           $pincode = $data[$i]['pincode'];
           if($pincode) {
               $pincodeJsonData =  $this->miscelleneous->google_map_address_api($pincode);
               $pincodeArray = json_decode($pincodeJsonData,true);
               echo $pincode;
               echo "<br>";
               if($pincodeArray['status'] == 'OK'){
                    $addressCompLength = count($pincodeArray['results'][0]['address_components']);
                    echo $country = $pincodeArray['results']['0']['address_components'][$addressCompLength-1]['long_name'];
                    if($country == 'India'){
                        echo "<br>";
                        echo $state = $pincodeArray['results']['0']['address_components'][$addressCompLength-2]['long_name'];
                        echo "<br>";
                        echo $city = $pincodeArray['results']['0']['address_components'][$addressCompLength-3]['long_name'];
                        $this->miscelleneous->process_if_pincode_valid($pincode,$state,$city);
                       //Update State and City in sf_not_exist_booking_details
                        $resultTemp = $this->reusable_model->get_rm_for_pincode($pincode);
                        $notFoundSfArray['rm_id'] = $resultTemp[0]['rm_id'];
                        $notFoundSfArray['state'] = $resultTemp[0]['state'];
                        $notFoundSfArray['city'] = $city;
                        $notFoundSfArray['is_pincode_valid'] = 1;
                        $this->vendor_model->update_not_found_sf_table(array("pincode"=>$pincode),$notFoundSfArray);
                   }
                   else{
                        $this->vendor_model->update_not_found_sf_table(array("pincode"=>$pincode),array("is_pincode_valid" => 0));
                   }
               }
               else if($pincodeArray['status'] == 'ZERO_RESULTS'){
                   //Delete Entry from sf_not_exist_booking_details
                    $this->vendor_model->update_not_found_sf_table(array("pincode"=>$pincode),array("is_pincode_valid" => 0));
               }
           }
       }
    }
}
