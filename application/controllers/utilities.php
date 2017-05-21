<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 36000000);

class Utilities extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();
        
	$this->load->model('user_model');
	$this->load->model('booking_model');
	$this->load->model('partner_model');
	$this->load->model('vendor_model');
        $this->load->model('upcountry_model');
        $this->load->model('reporting_utils');
        $this->load->model('pc_distance_model');
        $this->load->model('upcountry_model');
        
    }

    function test() {
        echo __METHOD__ . PHP_EOL;
    }
    
    function index() {
    }
    
    /*
     * 1. Get list of bookings using the way you want and Save this list to a file
     *   grep 'SMS not sent because of Vendor Unavailability for Booking ID' application/logs/log-2017-01-* | awk -F: '{ print $5 }' > pc_not_available_jan.txt
     * 2. Pass this file to the function below
     *   cat pc_not_available_jan.txt | xargs -I {} php index.php utilities get_current_status {} > pc_not_available_jan_status.txt
     */
    function get_current_status ($booking_id) {
        $status = $this->booking_model->get_booking_status(trim($booking_id));
        
        echo $booking_id . $status['current_status'] . ":" . $status['internal_status'] . PHP_EOL;      
    }
    
    function is_vendor_available () {
        
    }
    
    function get_distance_between_pincode_sf_hq_level() {
        echo "Entering" . PHP_EOL;
        $data = $this->upcountry_model->get_sub_service_center_details(array());
//        echo "GET SUB Office details" . PHP_EOL;
        foreach ($data as $key1 => $value) {
//            echo " Foreach data" . PHP_EOL;
            $pincode_list = $this->vendor_model->getPincode_from_india_pincode($value['district']);
//            echo "Service ID " . PHP_EOL;
            $service_id_list = $this->vendor_model->get_distinct_vendor_service_details($value['service_center_id'], $value['pincode']);
//            echo "GET PIncode List" . PHP_EOL;
            $pincode_details = array();
            foreach ($pincode_list as $key2 => $pincode) {
                if ($pincode['pincode'] != $value['pincode']) {
                    $is_distance = $this->upcountry_model->calculate_distance_between_pincode($value['pincode'], $value['district'], $pincode['pincode'], $value['district']);
                    if ($is_distance) {
                        $distance1 = (round($is_distance['distance']['value'] / 1000, 2));
                        //echo "Distance " . $distance1;
                        echo ".";
                        array_push($pincode_details, array("pincode" => $pincode['pincode'], "distance" => $distance1));
                    } else {
                        echo PHP_EOL . "Distance not calculated=> Pincode1: " . $value['pincode'] . ",Pincode 2: " . $pincode['pincode'] . PHP_EOL;
                        log_message("info", __METHOD__ . " Distance can not calcuate Pincode 1" . $value['pincode'] . " Pincode 2 " . $pincode['pincode']);
                    }
                    unset($pincode_list[$key2]);
                }
            }
//            echo "Foreach Service Id" . PHP_EOL;
            foreach ($service_id_list as $key3 => $service_id) {
                if (!empty($pincode_details)) {
                    foreach ($pincode_details as $details) {

                        $array = array(
                            "service_center_id" => $value['service_center_id'],
                            "sf_sub_office_id" => $value['id'],
                            "district" => $value['district'],
                            "district_pincode" => $value['pincode'],
                            "pincode" => $details['pincode'],
                            "distance" => $details['distance'],
                            "rate" => $value['upcountry_rate'],
                            "service_id" => $service_id["Appliance_ID"]
                        );

                        $status = $this->upcountry_model->insert_sf_hq_level_distance_details($array);
                        if ($status) {
                            echo "+";
                            log_message("info", __METHOD__ . " Inserted " . $value['pincode'] . " Pincode 2 " . $pincode['pincode']);
                        } else {
                            echo "Not Inserted " . $value['pincode'] . " Pincode 2 " . $pincode['pincode'] . " SF ID "
                            . $value['service_center_id'] . " service_id " . $service_id['Appliance_ID'];
                            log_message("info", __METHOD__ . " vendor_id " . $value['service_center_id'] . "  pincode" . $value['pincode'] . " Pincode 2 " . $pincode['pincode']);
                        }
                    }
                }

                unset($service_id_list[$key3]);
            }
            unset($data[$key1]);
        }
    }
    
}

