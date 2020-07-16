<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 36000);

define('Partner_Integ_Complete', TRUE);

class Test extends CI_Controller {

    /**
     * @desc This is used to get Repair- OOW Booking
     */
    function get_oow_booking_test(){
        
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/get_oow_booking_test';
    }
}