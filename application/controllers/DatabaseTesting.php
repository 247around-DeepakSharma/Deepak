<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This Controller checks database
 *
 * @author Abhay Anand
 */
class DatabaseTesting extends CI_Controller {

    function __Construct() {
	parent::__Construct();

	$this->load->model('database_testing_model');

	$this->load->library('notify');
	$this->load->library('email');
    }

    function index() {
	  $data = $this->database_testing_model->check_unit_details();
	if ($data) {
	  log_message('info', " Unit details have some inconsistent data: " . print_r($data, true));
	  }

	  $is_pricetags = $this->database_testing_model->check_price_tags();
	  if ($is_pricetags) {
	  log_message('info', " Unit details have inconsistent data( price tags): " . print_r($is_pricetags, true));
	  }

	  $is_tax_rate = $this->database_testing_model->check_tax_rate();
	  if ($is_tax_rate) {
	  log_message('info', " Unit details have some inconsistent data(tax rate): " . print_r($is_tax_rate, true));
	  }
	  $is_unit_status = $this->database_testing_model->check_booking_unit_details_status();
	  if ($is_unit_status) {

	  log_message('info', " Unit details have some inconsistent data(unit details): " . print_r($is_unit_status, true));
	  }

	  $is_booking_details = $this->database_testing_model->check_booking_details();
	  if ($is_booking_details) {

	  log_message('info', " Unit details have some inconsistent data(booking details): " . print_r($is_booking_details, true));
	  }

	$is_booking_id = $this->database_testing_model->check_booking_exist_in_unit_details();
	if ($is_booking_id) {
	    log_message('info', " Unit details have some inconsistent data(booking id): " . print_r($is_booking_id, true));
	}
	$is_service_center = $this->database_testing_model->check_booking_exist_in_service_center();
	if ($is_service_center) {
	   
	    log_message('info', " Unit details have some inconsistent data( service_center id): " . print_r($is_service_center, true));
	}
    }

}
