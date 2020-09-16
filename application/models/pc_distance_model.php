<?php

class pc_distance_model extends CI_Model {

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
    function insert_batch_sub_sc_details($data) {
        $this->db->insert_batch('sub_service_center_details', $data);
        return $this->db->insert_id();
    }

    function calculate_distance_between_pincodes($postcode1, $postcode2, $city) {
        //log_message('info', __FUNCTION__ . ' Calculate Pincode1 ' . $postcode1 . " Pincode 2" . $postcode2);
        
        //check whether it exists or not
        if (count($this->get_distance_bw_pincodes($postcode1, $postcode2)) <= 0) {
            $city_1 = str_replace(' ', '%20', $city);

            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins="
                    . "$city_1,$postcode1,India&destinations=$city_1,$postcode2,India"
                    . "&mode=driving&language=en-EN&sensor=false"
                    . "&key=".GOOGLE_MAPS_API_KEY;

            $data = file_get_contents($url);
            $result = json_decode($data, true);            
            //print_r($result);

            if (!empty($data)) {
                foreach ($result['rows'] as $distance) {
                    if ($distance['elements'][0]['status'] == "OK") {
                        //echo 'Distance Found' . PHP_EOL;
                        //sleep(1);
                        //usleep(10000);
                        return $distance['elements'][0];
                    } else {
                        //echo 'Distance Not Found pincode1 ' . $postcode1 . " " . $postcode2 . PHP_EOL;
                        return FALSE;
                    }
                }
            } else {
                //echo 'Distance Not Found pincode1 ' . $postcode1 . " " . $postcode2 . PHP_EOL;
                return FALSE;
            }
        } else {
            //echo 'distance already exists' . PHP_EOL;
            return 'distance already exists';
        }
    }

    function get_district_main_pincode($city, $state) {
        //log_message('info', __FUNCTION__ . ' Calculate Pincode1 ' . $postcode1 . " Pincode 2" . $postcode2);
        
        //check whether it exists or not
//        if (count($this->get_distance_bw_pincodes($postcode1, $postcode2)) <= 0) {
            $city_1 = str_replace(' ', '%20', $city);
            $state_1 = str_replace(' ', '%20', $state);

            $url1 = "https://maps.googleapis.com/maps/api/geocode/json?address="
                    . "$city_1+$state_1+INDIA"
                    . "&key=".GOOGLE_MAPS_API_KEY;

            $data1 = file_get_contents($url1);
            $result1 = json_decode($data1, true);            
            //print_r($result1);

            if (!empty($data1) && $result1['status'] == 'OK') {
                //print_r($result1['results'][0]['geometry']['location']);
                $location = $result1['results'][0]['geometry']['location'];
                $lat = $location['lat'];
                $lng = $location['lng'];
                
                $url2 = "https://maps.googleapis.com/maps/api/geocode/json?latlng="
                        . "$lat,$lng"
                        . "&key=".GOOGLE_MAPS_API_KEY;

                $data2 = file_get_contents($url2);
                $result2 = json_decode($data2, true);
                //print_r($result2);
                
                if (!empty($data2) && $result2['status'] == 'OK') {
                    //print_r($result2['results'][0]['address_components']);
                    $pin = $this->find_pincode_from_address_component($result2['results'][0]['address_components']);
                    
                    if ($pin != "000000") {
                        //pincode found
                        $this->insert_pincode_for_district($city, $pin, $state,
                                $result2['results'][0]['address_components']);
                    } else {
                        //pincode not found
                        $this->insert_pincode_for_district($city, "000000", $state,
                                $result2['results'][0]['address_components']);
                    }
                    
                    echo '.';
                }
                
            } else {
                //echo 'Distance Not Found pincode1 ' . $postcode1 . " " . $postcode2 . PHP_EOL;
                return FALSE;
            }
//        } else {
//            //echo 'distance already exists' . PHP_EOL;
//            return 'distance already exists';
//        }
    }

    function get_districts_from_states($states) {
        $this->db->distinct('district');
        $this->db->select('district, state');
        $this->db->where_in('state', $states);
        $query = $this->db->get('india_pincode');
        
        return $query->result_array();        
    }

    function get_district_offices() {
        //$this->db->where('district', $district);
        $query = $this->db->get('city_do_pincode');
        
        return $query->result_array();        
    }
    
    function get_district_offices_reverse() {
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('city_do_pincode');
        
        return $query->result_array();        
    }
    
    function get_district_pincodes($district) {
        $this->db->distinct();
        $this->db->select('pincode');
        $this->db->where('district', $district);
        $query = $this->db->get('india_pincode');
        
        return $query->result_array();        
    }
    
    function get_distance_bw_pincodes($pincode1, $pincode2) {
        if ($pincode1 < $pincode2) {
            $dp1 = $pincode1;
            $dp2 = $pincode2;
        } else {
            $dp1 = $pincode2;
            $dp2 = $pincode1;
        }
        
        $this->db->distinct();
        $this->db->select('distance');
        $this->db->where('pincode1', $dp1);
        $this->db->where('pincode2', $dp2);
        
        $query = $this->db->get('distance_between_pincode');
        
        return $query->result_array();
    }
    
    function find_pincode_from_address_component ($ac) {
        $pc = "000000";
        
        foreach ($ac as $v) {
            if ($v['types'][0] == "postal_code") {
                $pc = $v['long_name'];
                
                echo $pc . PHP_EOL;
            }
        }
        
        return $pc;
    }

    function insert_distance($pincode1, $pincode2, $distance, $city, $state,$agend_id) {
        if ($pincode1 < $pincode2) {
            $dp1 = $pincode1;
            $dp2 = $pincode2;
        } else {
            $dp1 = $pincode2;
            $dp2 = $pincode1;
        }
        
        $d = array('pincode1' => $dp1, 'pincode2' => $dp2,
                    'distance' => $distance, 'city' => $city, 'state' => $state,'agent_id'=>$agend_id);
        
//        print_r($d);
        
        $r = $this->db->insert('distance_between_pincode', $d);
        
        if ($r === FALSE)
            print_r($d);
    }

    function insert_pincode_for_district($city, $pincode, $state, $json) {
        $d = array('city' => $city, 'do-pincode' => $pincode,
                    'state' => $state, 'json' => print_r($json, TRUE), 'verified' => 0);
        
//        print_r($d);
        
        $r = $this->db->insert('city_do_pincode', $d);
        
        if ($r === FALSE)
            print_r($d);
    }

}
