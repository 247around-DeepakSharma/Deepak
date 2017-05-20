<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Misc_controller extends CI_Controller {

    function __Construct() {
        parent::__Construct();
        
        $this->load->model('upcountry_model');
        $this->load->model('vendor_model');


    }
    
    function get_distance_between_pincode_sf_hq_level(){
        echo "Entering".PHP_EOL;
        $data = $this->upcountry_model->get_sub_service_center_details(array());
         echo "GET SUB Office details".PHP_EOL;
        foreach($data as $key1 => $value){
            echo " Foreach data".PHP_EOL;
            $pincode_list = $this->vendor_model->getPincode_from_india_pincode($value['district']);
            echo "GET PIncode List".PHP_EOL;
            foreach ($pincode_list as $key2 => $pincode) {
                $is_distance = $this->upcountry_model->calculate_distance_between_pincode($value['pincode'], $value['district'],$pincode['pincode'], $value['district']);
                if($is_distance){
                    $distance1 = (round($is_distance['distance']['value'] / 1000, 2));
                    $service_id_list = $this->vendor_model->get_distinct_vendor_service_details($value['service_center_id'], $value['pincode']);
                    foreach ($service_id_list as $key3 => $service_id) {
                        $array = array(
                            "service_center_id" => $value['service_center_id'],
                            "sf_sub_office_id" => $value['id'],
                            "district" => $value['district'],
                            "district_pincode" =>$value['pincode'],
                            "pincode" => $pincode['pincode'],
                            "distance" => $distance1,
                            "rate" => $value['upcountry_rate'],
                            "service_id" => $service_id["Appliance_ID"]
                        );
                        
                        $status =$this->upcountry_model->insert_sf_hq_level_distance_details($array);
                        if($status){
                            echo "Inserted";
                            log_message("info", __METHOD__." Inserted ".$value['pincode']. " Pincode 2 ".$pincode['pincode']);
                        } else {
                            echo "Distance not calculated ".$value['pincode']. " Pincode 2 ".$pincode['pincode']." SF ID "
                                    . $value['service_center_id']." service_id ". $service_id['Appliance_ID'];
                            log_message("info", __METHOD__." vendor_id ".$value['service_center_id']."  pincode".$value['pincode']. " Pincode 2 ".$pincode['pincode']);
                        }
                        
                        unset($service_id_list[$key3]);
                    }
                    
                } else {
                    echo "Distance not calculated ".$value['pincode']. " Pincode 2 ".$pincode['pincode'];
                    log_message("info", __METHOD__." Distance can not calcuate Pincode 1".$value['pincode']. " Pincode 2 ".$pincode['pincode']);
                }
                unset($pincode_list[$key2]);
            }
            unset($data[$key1]);
        }
        
    }
    
    
}