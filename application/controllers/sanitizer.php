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

class Sanitizer extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();
        
        $this->load->model('pc_distance_model');
    }

    function test() {
        echo __METHOD__ . PHP_EOL;
    }
    
    function index() {
        //$email = 'anuj@247around.com';
        //$regex = '/[a-zA-Z0-9_\.\+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-\.]+/';

        echo 'Starting email cleaning....' . PHP_EOL;

        $handle = fopen("emails-full.txt", "r") or die("Couldn't get handle");
        $f1 = fopen("emails-valid.txt", "w") or die("Couldn't get handle");
        $f2 = fopen("emails-invalid.txt", "w") or die("Couldn't get handle");

        if ($handle) {
            echo 'File opened' . PHP_EOL;
            
            while (!feof($handle)) {
                $email = fgets($handle);
                if ($this->isValidEmail(trim($email)) !== FALSE) {
                    //echo '.';
                    fputs($f1, trim($email) . PHP_EOL);
                }
                else {
                    //echo PHP_EOL . 'Invalid' . PHP_EOL;
                    fputs($f2, $email);
                }
            }

            echo PHP_EOL;
            fclose($handle);
        }
    }
    
    function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) 
            && preg_match('/@.+\./', $email);
    }
    
    function find_upcountry_pincodes () {
        /*
         * 1. Read cities from city-do-pincode table. Every city has a unique District Office (DO) pincode.
         * 2. For every city, find distinct pincodes present in india-pincode table.
         * 3. Calculate distance between DO-pincode and pincode found in step 2.
         * 4. Save distance and pincodes in distance_between_pincode table.
         * 
         */
        
        $fails = 0;
        $l1 = $this->pc_distance_model->get_district_offices();
        //print_r($l1);
        
        foreach ($l1 as $do) {
            $l2 = $this->pc_distance_model->get_district_pincodes($do['city']);
//            print_r($l2);
            
            foreach ($l2 as $pc) {
                //echo $do['do-pincode'] . PHP_EOL;
                //echo $pc['pincode'] . PHP_EOL;
                
                $pc1 = $do['do-pincode'];
                $pc2 = $pc['pincode'];
                
                if ($pc1 == $pc2)
                    continue;
                
                if ($pc1 < $pc2) {
                    $distance = $this->pc_distance_model->calculate_distance_between_pincodes($pc1, $pc2, $do['city']);
                } else {
                    $distance = $this->pc_distance_model->calculate_distance_between_pincodes($pc2, $pc1, $do['city']);
                }
                
                if ($distance == 'distance already exists') {
                    echo '*';
                } else {
                    if ($distance !== FALSE && $distance['distance']['text'] != '') {
    //                    echo $distance['distance']['text'] . PHP_EOL;
                        echo '.';

                        //print_r($distance);

                        $this->pc_distance_model->insert_distance($pc1, $pc2, $distance['distance']['value'],
                                $do['city'], $do['state']);
                    } else {
                        $fails++;

                        echo PHP_EOL.'? => ';

                        //still insert it
                        $this->pc_distance_model->insert_distance($pc1, $pc2, '999999999',
                                $do['city'], $do['state']);

                        echo $pc1 . ", " . $pc2 . PHP_EOL;
                    }
                }
                
//                exit();
            }
        }
        
        echo 'Fails: ' . $fails . PHP_EOL;
    }
    
    function find_upcountry_pincodes_reverse () {
        /*
         * 1. Read cities from city-do-pincode table. Every city has a unique District Office (DO) pincode.
         * 2. For every city, find distinct pincodes present in india-pincode table.
         * 3. Calculate distance between DO-pincode and pincode found in step 2.
         * 4. Save distance and pincodes in distance_between_pincode table.
         * 
         */
        
        $fails = 0;
        $l1 = $this->pc_distance_model->get_district_offices_reverse();
        //print_r($l1);
        
        foreach ($l1 as $do) {
            $l2 = $this->pc_distance_model->get_district_pincodes($do['city']);
//            print_r($l2);
            
            foreach ($l2 as $pc) {
                //echo $do['do-pincode'] . PHP_EOL;
                //echo $pc['pincode'] . PHP_EOL;
                
                $pc1 = $do['do-pincode'];
                $pc2 = $pc['pincode'];
                
                if ($pc1 == $pc2)
                    continue;
                
                if ($pc1 < $pc2) {
                    $distance = $this->pc_distance_model->calculate_distance_between_pincodes($pc1, $pc2, $do['city']);
                } else {
                    $distance = $this->pc_distance_model->calculate_distance_between_pincodes($pc2, $pc1, $do['city']);
                }
                
                if ($distance == 'distance already exists') {
                    echo '*';
                } else {
                    if ($distance !== FALSE && $distance['distance']['text'] != '') {
    //                    echo $distance['distance']['text'] . PHP_EOL;
                        echo '.';

                        //print_r($distance);

                        $this->pc_distance_model->insert_distance($pc1, $pc2, $distance['distance']['value'],
                                $do['city'], $do['state']);
                    } else {
                        $fails++;

                        echo PHP_EOL.'? => ';

                        //still insert it
                        $this->pc_distance_model->insert_distance($pc1, $pc2, '999999999',
                                $do['city'], $do['state']);

                        echo $pc1 . ", " . $pc2 . PHP_EOL;
                    }
                }
                
//                exit();
            }
        }
        
        echo 'Fails: ' . $fails . PHP_EOL;
    }
    
    function find_city_do_pincodes() {
        $cities = $this->pc_distance_model->get_districts_from_states(array(
            'UTTAR PRADESH','MAHARASHTRA','GUJARAT','MIZORAM','RAJASTHAN','KERALA','MADHYA PRADESH',
            'UTTARAKHAND','HARYANA','PUNJAB','JAMMU & KASHMIR','ODISHA','BIHAR','KARNATAKA','WEST BENGAL',
            'ASSAM','CHATTISGARH','HIMACHAL PRADESH','MANIPUR','JHARKHAND','ARUNACHAL PRADESH','TRIPURA',
            'NAGALAND','MEGHALAYA','PONDICHERRY'
        ));
        //print_r($cities);
        
        foreach ($cities as $c) {
            $this->pc_distance_model->get_district_main_pincode($c['district'], $c['state']);
        }
        
        //$this->pc_distance_model->get_district_main_pincode('karimnagar', 'andhra pradesh');
    }

}
