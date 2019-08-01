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
	$this->load->model('reporting_utils');
        $this->load->model('partner_model');

	$this->load->library('notify');
	$this->load->library('email');
	$this->load->library('booking_utilities');
        $this->load->library('invoice_lib');
        
    }

    function index() {
        $file = fopen(TMP_FOLDER. date('Y-m-d') . ".txt", "w") or die("Unable to open file!");
        $res = 0;
        system(" chmod 777 ".TMP_FOLDER . date('Y-m-d') . ".txt", $res);
        $data = $this->database_testing_model->check_unit_details();

        if (!empty($data)) {
            echo "..1".PHP_EOL;
            $contents = " Appliance ID, Partner ID, Service ID field has 0 in booking unit details:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($data, TRUE));

            //log_message('info', " Unit details have some inconsistent data: " . print_r($data, true));
        }

        $is_pricetags = $this->database_testing_model->check_price_tags();
        if (!empty($is_pricetags)) {
            echo "..2".PHP_EOL;
            $contents = " Price tag is empty:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_pricetags, TRUE));
            //log_message('info', " Unit details have inconsistent data( price tags): " . print_r($is_pricetags, true));
        }

        $is_tax_rate = $this->database_testing_model->check_tax_rate();
        if (!empty($is_tax_rate)) {
            echo "..3".PHP_EOL;
            $contents = " Tax Rate is 0:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_tax_rate, TRUE));
            //log_message('info', " Unit details have some inconsistent data(tax rate): " . print_r($is_tax_rate, true));
        }
        $is_unit_status = $this->database_testing_model->check_booking_unit_details_status();
        if (!empty($is_unit_status)) {
            echo "..4".PHP_EOL;
            $contents = " Booking Status has empty:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_unit_status, TRUE));

            // log_message('info', " Unit details have some inconsistent data(unit details): " . print_r($is_unit_status, true));
        }

        // $is_booking_details = $this->database_testing_model->check_booking_details();
        // if ($is_booking_details) {
        //     log_message('info', " Unit details have some inconsistent data(booking details): " . print_r($is_booking_details, true));
        // }
	
        $is_service_center = $this->database_testing_model->check_booking_exist_in_service_center();
        if (!empty($is_service_center)) {
            echo "..5".PHP_EOL;
            $contents = " Number of line item in the unit details should be same in service  center Table:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_service_center, TRUE));
            //log_message('info', " Unit details have some inconsistent data( service_center id): " . print_r($is_service_center, true));
        }

        $is_action_table = $this->database_testing_model->check_service_center_action();
        if (!empty($is_action_table)) {
            echo "..6".PHP_EOL;
            $contents = " Unit details id is zero in SC action table:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_action_table, TRUE));
            //log_message('info', " Unit details have some inconsistent data( service_center action table): " . print_r($is_action_table, true));
        }

        $is_pending = $this->database_testing_model->check_pending_booking_in_action_table();
        if (!empty($is_pending)) {
            echo "..7".PHP_EOL;
            $contents = " check if booking is pending or rescheduled in booking details  it must be pending or Inprocess in service center action table:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_pending, TRUE));
            //log_message('info', " Unit details have some inconsistent data( Pending Booking not show in service center panel): " . print_r($is_pending, true));
        }

        $is_service_center_action = $this->database_testing_model->check_booking_exist_in_service_center_action_table();

        if (!empty($is_service_center_action)) {
            echo "..8".PHP_EOL;
            $contents = " Bookings not visible to Vendors but Pending in our system:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_service_center_action, TRUE));
            //log_message('info', " Assigned Booking is not exist in the service center action table: " . print_r($is_service_center_action, true));
        }

        $is_booking_status = $this->database_testing_model->check_in_closed_booking_booking_status_notempty();
        if (!empty($is_booking_status)) {
            echo "..9".PHP_EOL;
            $contents = " Booking status has not empty for cloased booking:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_booking_status, TRUE));
            //log_message('info', " Booking Status is empty: " . print_r($is_booking_status, true));
        }

        $is_product_or_services = $this->database_testing_model->check_product_or_services();
        if (!empty($is_product_or_services)) {
            echo "..10".PHP_EOL;
            $contents = " Product OR servuces Field has Empty:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_product_or_services, TRUE));
            //log_message('info', " Product_or_services is empty for completed booking: " . print_r($is_product_or_services, true));
        }
        $is_prices_negative = $this->database_testing_model->check_prices_should_not_be_negative();
        if (!empty($is_prices_negative)) {
            echo "..11".PHP_EOL;
            $contents = " Prices has negative  value:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_prices_negative, TRUE));
            // log_message('info', " Prices becomes negative " . print_r($is_prices_negative, true));
        }

//        $is_emial_flag = $this->database_testing_model->check_assigned_vendor_email_flag();
//        if (!empty($is_emial_flag)) {
//            echo "..12".PHP_EOL;
//            $contents = " Mail Flag Has 0 when vendor Assigned:\n";
//            fwrite($file, $contents);
//            fwrite($file, print_r($is_emial_flag, TRUE));
//            //log_message('info', " Assigned booking has mail flag 0 " . print_r($is_emial_flag, true));
//        }
        
        $is_check_partner_paid_basic_charge = $this->database_testing_model->check_partner_paid_basic_charge();
        if (!empty($is_check_partner_paid_basic_charge)) {
            echo "..13".PHP_EOL;
            $contents = " Check Partner Paid Basic Charge should satisfy formula:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_check_partner_paid_basic_charge, TRUE));
            //log_message('info', " Assigned booking has mail flag 0 " . print_r($is_emial_flag, true));
        }
        
        $is_ac_service = $this->database_testing_model->check_customer_paid_basic_charge();
        if (!empty($is_ac_service)) {
            echo "..14".PHP_EOL;
            $contents = " Service which was closed at 0 prices:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_ac_service, TRUE));
            //log_message('info', " Assigned booking has mail flag 0 " . print_r($is_emial_flag, true));
        }
        $is_stand = $this->database_testing_model->check_stand();
        if (!empty($is_stand)) {
            echo "..15".PHP_EOL;
            $contents = " Stand is not added in the Unit details:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_stand, TRUE));
            //log_message('info', " Assigned booking has mail flag 0 " . print_r($is_emial_flag, true));
        }
        
        $is_duplicate_entry = $this->database_testing_model->check_duplicate_entry();
        if (!empty($is_duplicate_entry)) {
            echo "..16".PHP_EOL;
            $contents = " Duplicate booking in Unit details:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_duplicate_entry, TRUE));
            //log_message('info', " Assigned booking has mail flag 0 " . print_r($is_emial_flag, true));
        }
        
        $is_booking_id = $this->database_testing_model->check_booking_exist_in_unit_details();
	 if (!empty($is_booking_id)) {
            echo "..17".PHP_EOL;
            $contents = " Booking Id not exist in unit details:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($is_booking_id, TRUE));
            //log_message('info', " Unit details have some inconsistent data( service_center id): " . print_r($is_service_center, true));
        }
        
         $customer_total = $this->database_testing_model->check_customer_total();
	 if (!empty($customer_total)) {
            echo "..18".PHP_EOL;
            $contents = "Customer Total is zero:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($customer_total, TRUE));
            //log_message('info', " Unit details have some inconsistent data( service_center id): " . print_r($is_service_center, true));
        }
        
        $pending_booking_job_card = $this->database_testing_model->count_pending_bookings_without_job_card();
	 if (!empty($pending_booking_job_card)) {
            echo "..19".PHP_EOL;
//            //Creating Job cards for Bookings and sending mail to vendors 
//            foreach($pending_booking_job_card as $value){
//                //Prepare job card
//                $this->booking_utilities->lib_prepare_job_card_using_booking_id($value['booking_id']);
//
//                //Send mail to vendor, no Note to vendor as of now
//               $this->booking_utilities->lib_send_mail_to_vendor($value['booking_id'], "");
//            }
             
            $contents = "Pending Bookings without Job cards:\n";
            fwrite($file, $contents);
            fwrite($file, print_r($pending_booking_job_card, TRUE));
            //log_message('info', " Unit details have some inconsistent data( service_center id): " . print_r($is_service_center, true));
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
                  line-height: 1.42857143;    "> Partner paid basic charge is not correct </th>
                   <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;   ">' . Count($is_check_partner_paid_basic_charge) . '</td>
                  </tr>
                  
                 <tr>
                  <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                  vertical-align: bottom;padding: 8px;
                  line-height: 1.42857143;    "> Service which was closed at 0 prices </th>
                   <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;   ">' . Count($is_ac_service) . '</td>
                  </tr>
                   <tr>
                  <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                  vertical-align: bottom;padding: 8px;
                  line-height: 1.42857143;    "> Stand is not added in the unit details </th>
                   <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;   ">' . Count($is_stand) . '</td>
                  </tr>
                   <tr>
                  <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                  vertical-align: bottom;padding: 8px;
                  line-height: 1.42857143;    "> Duplicate_Entry in unit details </th>
                   <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;   ">' . Count($is_duplicate_entry) . '</td>
                  </tr>
                  
                   <tr>
                  <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                  vertical-align: bottom;padding: 8px;
                  line-height: 1.42857143;    "> Booking Id is not exist in unit details</th>
                   <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;   ">' . Count($is_booking_id) . '</td>
                  </tr>
                   <tr>
                  <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                  vertical-align: bottom;padding: 8px;
                  line-height: 1.42857143;    "> Customer Total Is zero</th>
                   <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;   ">' . Count($customer_total) . '</td>
                  </tr>
                   <tr>
                  <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                  vertical-align: bottom;padding: 8px;
                  line-height: 1.42857143;    "> Pending Bookings without Job Cards</th>
                   <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;   ">' . Count($pending_booking_job_card) . '</td>
                  </tr>
                  
            </tr>
         </thead>
         <tbody>
   
            </tbody></table>';
       // echo $table;
        $from = NOREPLY_EMAIL_ID;
        $to= "anuj@247around.com";
        $bcc= "";
        $cc = DEVELOPER_EMAIL;
        $subject = "Inconsistent Data";
        $message = $table;
        $attachment = TMP_FOLDER. date('Y-m-d') . ".txt";
        $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment,INCONSISTENT_DATA_TO_DEVELOPER);
        $out="";
        $return ="";
        exec("rm -rf " . escapeshellarg(TMP_FOLDER . date('Y-m-d') . ".txt"), $out, $return);
        // Return will return non-zero upon an error

        if (!$return) {
            // exec() has been executed sucessfully
            // Inserting values in scheduler tasks log
            $this->reporting_utils->insert_scheduler_tasks_log(__FUNCTION__);
            //Logging
            log_message('info', __FUNCTION__ . ' Executed Sucessfully ' . TMP_FOLDER . date('Y-m-d') . ".txt");
        }
    }
    
    /**
     * @desc: This is method is used to send Error file 
     */
    function send_error_file(){
        $attachment = FCPATH."application/logs/error_" . date('Y-m-d') . ".txt";
        if(file_exists($attachment)){
            $from = NOREPLY_EMAIL_ID;
            $to= DEVELOPER_EMAIL;
            $bcc= "";
            $cc = "";
            $subject = "Error File";
            $message = "Find Attachment";
        
            $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment,SEND_ERROR_FILE);
            
            // Inserting values in scheduler tasks log
            $this->reporting_utils->insert_scheduler_tasks_log(__FUNCTION__); 
        }
    }
    /**
     * @esc: This is used to send requested log file
     * @param String $file_name
     */
    function get_log_file($file_name){
        $attachment = FCPATH."application/logs/".$file_name.".php";
        if(file_exists($attachment)){
            $attach_zip = FCPATH."application/logs/".$file_name.'.zip';
            system('zip '. $attach_zip."  ". $attachment );
            system("chmod 777 ".$attach_zip);
            
            $from = NOREPLY_EMAIL_ID;
            $to= DEVELOPER_EMAIL;
            $bcc= "";
            $cc = "";
            $subject = "Log file ". $file_name.".php";
            $message = "Find Attachment";
            
            $is_mail =$this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message,  $attach_zip,SEND_LOG_FILE); 
            if($is_mail){
                exec("rm -rf " . escapeshellarg($attach_zip));
                echo "Mail Sent....". $file_name.".php";
               
            } else {
                echo "Mail Not Sent.....".$file_name.".php";
            }
        } else {
            echo "File Not Found";
        }
    }
    /**
     * @desc This is used to convert jobcard excel to pdf
     * @param Date $date(Format - 2018-03-14)
     */
    function create_jobcards($date) {
        echo __FUNCTION__ . PHP_EOL;
        
        $booking_id = $this->database_testing_model->get_booking_id_without_pdf_jobcards($date);
        
        if (!empty($booking_id)) {
            foreach ($booking_id as $value) {
                echo $value['booking_id'] . PHP_EOL;
                
                $url = base_url() .
                        "employee/bookingjobcard/prepare_job_card_using_booking_id/" .
                        $value['booking_id'];
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

                curl_exec($ch);

                // get HTTP response code
                curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
            }
        }
    }
    
    function testDefective(){
        $where =  array("status" => DEFECTIVE_PARTS_PENDING, 'defective_part_required' => 1, 'sf_challan_number IS NULL ' => NULL);
               $data  = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id, service_center_id, service_center_closed_date",
                        $where, true, false, "spare_parts_details.id");
            echo count($data);echo PHP_EOL;       
         foreach ($data as $key => $value) {
            echo $key.PHP_EOL;
                        $this->invoice_lib->generate_challan_file($value['id'], $value['service_center_id'], $value['service_center_closed_date']);
            print_r($value); 
         }


    }



   function force_generate_challan($id,$s){
        $this->invoice_lib->force_generate_challan_file($id, $s);

    }

    function insert_partner_code(){ 
        $data =array();
       
        foreach (range('A', 'Z') as $char1){
            $i = 0;
            foreach (range('A', 'Z') as $char) {
                $code = $char1 . $char;
                $data[$i]['code'] = $code;
                $data[$i]['series'] = $char1;
                $i++;
            }
            $this->partner_model->insert_data_in_batch("partner_code", $data, "gk");
        }
    }
    
    function all_partner_gst_checking_by_api(){
        $partners = $this->partner_model->getpartner('', false);
        foreach ($partners as $partner){
            if($partner['gst_number']){
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.taxprogsp.co.in/commonapi/v1.1/search?aspid=1606680918&password=priya@b30&Action=TP&Gstin=".$partner['gst_number'],  
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                ));
                $api_response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                if ($err) {
                    echo "cURL Error :" . $err ."</br>"; 
                    $emailBody =  "cURL Error :" . $err ."</br>"; 
                    $this->notify->sendEmail('kalyanit@247around.com', 'kalyanit@247around.com', '', '', 'partner gst curl fail', $emailBody, "",'sf_permanent_on_off');
                } else {
                    $api_response = json_decode($api_response, TRUE);
                    if(isset($api_response['error'])){
                      $emailBody =json_encode($api_response, TRUE);
                      $this->notify->sendEmail('kalyanit@247around.com', 'kalyanit@247around.com', '', '', 'partner gst error', $emailBody, "",'sf_permanent_on_off');
                    }
                    else{
                        if(isset($api_response['dty'])){
                            $data['gst_type'] = $api_response['dty'];
                        }
                        if(isset($api_response['sts'])){
                            $data['gst_status'] = $api_response['sts'];
                        }
                       
                        $this->partner_model->edit_partner($data, $partner['id']);
                    }
                }
            }
        }
    }
    
    function engineer_old_date_appliances_mapping(){
        $engineers = $this->engineer_model->get_engineers_details(array(), '*');
        foreach ($engineers as $key => $value) {
            $appliances = json_decode($value['appliance_id']);
            if(!empty($appliances)){
                foreach ($appliances as $akey => $avalue) {
                    echo "insert into engineer_appliance_mapping (`engineer_id`, `service_id`, `is_active`) values ('".$value['id']."', '".$avalue->service_id."', '1');"."</br>";
                }
            }
        }
    }
}
