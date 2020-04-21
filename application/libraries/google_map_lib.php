<?php

class google_map_lib {

    public function __construct() {
        $this->My_CI = & get_instance();
    }
    function google_map_address_api($pincode){
//        $request = "https://maps.google.com/maps/api/geocode/json?address=".$pincode."&sensor=false&region=India&key=AIzaSyB4pxS4j-_NBuxwcSwSFJ2ZFU-7uep1hKc";
//        $ch = curl_init();
//        curl_setopt_array(
//        $ch, array(
//        CURLOPT_URL =>$request,
//        CURLOPT_RETURNTRANSFER => true
//        ));
//        $output = curl_exec($ch);
        $output = '{ "results" : [ { "address_components" : [ { "long_name" : "110051", "short_name" : "110051", "types" : [ "postal_code" ] }, { "long_name" : "New Delhi", "short_name" : "New Delhi", "types" : [ "locality", "political" ] }, { "long_name" : "Delhi", "short_name" : "DL", "types" : [ "administrative_area_level_1", "political" ] }, { "long_name" : "India", "short_name" : "IN", "types" : [ "country", "political" ] } ], "formatted_address" : "New Delhi, Delhi 110051, India", "geometry" : { "bounds" : { "northeast" : { "lat" : 28.66559119999999, "lng" : 77.29854069999999 }, "southwest" : { "lat" : 28.6433122, "lng" : 77.2725126 } }, "location" : { "lat" : 28.6569035, "lng" : 77.28229229999999 }, "location_type" : "APPROXIMATE", "viewport" : { "northeast" : { "lat" : 28.66559119999999, "lng" : 77.29854069999999 }, "southwest" : { "lat" : 28.6433122, "lng" : 77.2725126 } } }, "place_id" : "ChIJ85SOHWD7DDkRI-0i7DDZy-M", "types" : [ "postal_code" ] } ], "status" : "OK" }';
        return $output;
    }
}
