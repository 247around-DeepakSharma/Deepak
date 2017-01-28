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
        $this->load->model('reporting_utils');
        $this->load->model('pc_distance_model');
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
    
    function is_vendor_available ($booking_id) {
        
    }
    
}

