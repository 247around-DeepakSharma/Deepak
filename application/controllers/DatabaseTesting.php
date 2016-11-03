<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}


error_reporting(E_ALL);
ini_set('display_errors', '1');

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 360000);

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This Controller checks database consistency
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
        $file = fopen("/tmp/" . date('Y-m-d') . ".txt", "w") or die("Unable to open file!");
        ;
        $data = $this->database_testing_model->check_unit_details();

        if ($data) {
            $contents = " Appliance ID, Partner ID, Service ID field has 0 in booking unit details:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($data, TRUE));

            //log_message('info', " Unit details have some inconsistent data: " . print_r($data, true));
        }

        $is_pricetags = $this->database_testing_model->check_price_tags();
        if ($is_pricetags) {
            $contents = " price tag is empty:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_pricetags, TRUE));
            //log_message('info', " Unit details have inconsistent data( price tags): " . print_r($is_pricetags, true));
        }

        $is_tax_rate = $this->database_testing_model->check_tax_rate();
        if ($is_tax_rate) {
            $contents = " Tax Rate is 0:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_tax_rate, TRUE));
            //log_message('info', " Unit details have some inconsistent data(tax rate): " . print_r($is_tax_rate, true));
        }
        $is_unit_status = $this->database_testing_model->check_booking_unit_details_status();
        if ($is_unit_status) {
            $contents = " Booking Status has empty:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_unit_status, TRUE));

            // log_message('info', " Unit details have some inconsistent data(unit details): " . print_r($is_unit_status, true));
        }

        // $is_booking_details = $this->database_testing_model->check_booking_details();
        // if ($is_booking_details) {
        //     log_message('info', " Unit details have some inconsistent data(booking details): " . print_r($is_booking_details, true));
        // }
//	$is_booking_id = $this->database_testing_model->check_booking_exist_in_unit_details();
//	if ($is_booking_id) {
//	    log_message('info', " Booking Id is not exist in the unit details" . print_r($is_booking_id, true));
//	}
        $is_service_center = $this->database_testing_model->check_booking_exist_in_service_center();
        if ($is_service_center) {
            $contents = " Number of line item in the unit details should be same in service  center Table:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_service_center, TRUE));
            //log_message('info', " Unit details have some inconsistent data( service_center id): " . print_r($is_service_center, true));
        }

        $is_action_table = $this->database_testing_model->check_service_center_action();
        if ($is_action_table) {
            $contents = " Unit details id is zero in SC action table:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_action_table, TRUE));
            //log_message('info', " Unit details have some inconsistent data( service_center action table): " . print_r($is_action_table, true));
        }

        $is_pending = $this->database_testing_model->check_pending_booking_in_action_table();
        if ($is_pending) {
            $contents = " check if booking is pending or rescheduled in booking details  it must be pending or Inprocess in service center action table:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_pending, TRUE));
            //log_message('info', " Unit details have some inconsistent data( Pending Booking not show in service center panel): " . print_r($is_pending, true));
        }

        $is_service_center_action = $this->database_testing_model->check_booking_exist_in_service_center_action_table();

        if ($is_service_center_action) {
            $contents = " Bookings not visible to Vendors but Pending in our system:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_service_center_action, TRUE));
            //log_message('info', " Assigned Booking is not exist in the service center action table: " . print_r($is_service_center_action, true));
        }

        $is_booking_status = $this->database_testing_model->check_in_closed_booking_booking_status_notempty();
        if ($is_booking_status) {
            $contents = " Booking status has not empty for cloased booking:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_booking_status, TRUE));
            //log_message('info', " Booking Status is empty: " . print_r($is_booking_status, true));
        }

        $is_product_or_services = $this->database_testing_model->check_product_or_services();
        if ($is_product_or_services) {
            $contents = " Product OR servuces Field has Empty:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_product_or_services, TRUE));
            //log_message('info', " Product_or_services is empty for completed booking: " . print_r($is_product_or_services, true));
        }
        $is_prices_negative = $this->database_testing_model->check_prices_should_not_be_negative();
        if ($is_prices_negative) {
            $contents = " Prices has negative  value:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_prices_negative, TRUE));
            // log_message('info', " Prices becomes negative " . print_r($is_prices_negative, true));
        }

        $is_emial_flag = $this->database_testing_model->check_assigned_vendor_email_flag();
        if ($is_emial_flag) {
            $contents = " Mail Flag Has 0 when vendor Assigned:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_emial_flag, TRUE));
            //log_message('info', " Assigned booking has mail flag 0 " . print_r($is_emial_flag, true));
        }

        fclose($file);

        $table = '<div style="position: inherit;padding: 0 30px;border-left: 1px solid #e7e7e7">
<div style="padding-right: 15px;padding-left: 15px;margin-right: auto;margin-left: auto;">
<div class="row" >
<di style="width:100%" >
<h2 style="padding-bottom: 9px;margin: 40px 0 20px; border-bottom: 1px solid #eee;">Inconsistent Data</h2>
<div  style="     margin-bottom: 20px;
   background-color: #fff;
   border: 1px solid transparent;
   border-radius: 4px;
   box-shadow: 0 1px 1px rgba(0,0,0,.05);
   border-color: #ddd;">
   <div style="    padding: 15px;" >
      <table class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" 
         style="margin-top:10px;     width: 100%;
         max-width: 100%;
         margin-bottom: 20px;    background-color: transparent;    display: table;border: 1px solid #ddd;">
         <thead >
            <tr>
              
               <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                  vertical-align: bottom;padding: 8px;
                  line-height: 1.42857143;    ">Appliance ID, Partner ID, Service ID field has 0 in booking unit details</th>
                    <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;   ">' . Count($data) . '</td>
           </tr>
           <tr>
               <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                  vertical-align: bottom;padding: 8px;
                  line-height: 1.42857143;    ">price tag is empty </th>
                   <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;   ">' . Count($is_pricetags) . '</td>
                  </tr>
                  <tr>
               <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                  vertical-align: bottom;padding: 8px;
                  line-height: 1.42857143;    ">Tax Rate is 0 </th>
                   <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;   ">' . Count($is_tax_rate) . '</td>
                  </tr>
                  <tr>
               <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                  vertical-align: bottom;padding: 8px;
                  line-height: 1.42857143;    ">Booking Status is empty</th>
                   <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;   ">' . Count($is_unit_status) . '</td>
                      </tr>
                      <tr>
                <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                  vertical-align: bottom;padding: 8px;
                  line-height: 1.42857143;    ">Number of line item in the unit details should be same in service  center Table </th>
                    <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;   ">' . Count($is_service_center) . '</td>
                      </tr>
                      <tr>
                  <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                  vertical-align: bottom;padding: 8px;
                  line-height: 1.42857143;    "> Unit details id is zero in SC action table </th>
                     <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;   ">' . Count($is_action_table) . '</td>
                      </tr>
                      <tr>
                  <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                  vertical-align: bottom;padding: 8px;
                  line-height: 1.42857143;    ">  check if booking is pending or rescheduled in booking details  it must be pending or Inprocess in service center action table </th>
                   <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;   ">' . Count($is_pending) . '</td>
                     </tr>
                     <tr>
                  <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                  vertical-align: bottom;padding: 8px;
                  line-height: 1.42857143;    "> Bookings not visible to Vendors but Pending in our system </th>
                  <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;   ">' . Count($is_service_center_action) . '</td>
                  </tr>
                  <tr>
                  <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                  vertical-align: bottom;padding: 8px;
                  line-height: 1.42857143;    "> Booking status has not empty for cloased booking </th>
                   <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;   ">' . Count($is_booking_status) . '</td>
                  </tr>
                  <tr>
                  <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                  vertical-align: bottom;padding: 8px;
                  line-height: 1.42857143;    "> Product OR Services Field has Empty </th>
                   <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;   ">' . Count($is_product_or_services) . '</td>
                  </tr>
                  <tr>
                  <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                  vertical-align: bottom;padding: 8px;
                  line-height: 1.42857143;    "> Prices has negative  value </th>
                   <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;   ">' . Count($is_prices_negative) . '</td>
                  </tr>
                  <tr>
                  <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                  vertical-align: bottom;padding: 8px;
                  line-height: 1.42857143;    "> Mail Flag Has 0 when vendor Assigned </th>
                   <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;   ">' . Count($is_emial_flag) . '</td>
                  </tr>
                  
            </tr>
         </thead>
         <tbody>
   
            </tbody></table>';
       // echo $table;
        $from = "booking@247around.com";
        $to= "anuj@247around.com";
        $bcc= "";
        $cc = "abhaya@247around.com";
        $subject = "Inconsistent Data";
        $message = $table;
        $attachment = $file;
        $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment);
        
        exec("rm -rf " . escapeshellarg($file));
    }
    

}
