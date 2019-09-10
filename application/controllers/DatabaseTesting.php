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

        $from = SYS_HEALTH_EMAIL;

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

            $from = SYS_HEALTH_EMAIL;

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
     * @desc This is used to convert jobcard excel to pdf when PDF conversion
     *      has failed.
     * 
     * @param Date $date(Format - 2018-03-14)
     */
    function create_jobcards_without_pdf($date) {
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
    
    /**
     * @desc This is used to recreate jobcards which got missed and not created
     *        at all i.e. both PDF and XLSX files are missing.
     * 
     * @param Date $date(Format - 2018-03-14)
     */
    function recreate_jobcards() {
        echo __FUNCTION__ . PHP_EOL;
        
        $pending_booking_job_card = $this->database_testing_model->count_pending_bookings_without_job_card();
        
        if (!empty($pending_booking_job_card)) {
            echo 'Pending Job Cards: ' . count($pending_booking_job_card) . PHP_EOL;
            
            //Creating Job cards for Bookings 
            foreach ($pending_booking_job_card as $value) {
                echo $value['booking_id'] . PHP_EOL;
                
                //Prepare job card
                $this->booking_utilities->lib_prepare_job_card_using_booking_id($value['booking_id']);
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



    function do_partner_nrn_approval($booking_id){

        //$booking_id = trim($this->input->post('booking_id'));
        $partner_id = 247130;
        $email="Not Given";
        $remarks = 'Approved By Sudhir';
        if (isset($_POST['email']) && !empty($_POST['email'])) {
           $email = $this->input->post('email');
        }else{
          $email='';
        }
      //  $allowedExts = array("PDF", "pdf",'jpg','jpeg','png','PNG',);
        $allowedExts = array("PDF", "pdf",'jpg','jpeg','png','PNG','docx','DOCX','doc','DOC');
        $approval_file_name = "Not Uploaded";
        if(isset($_FILES["approval_file"]) && !empty($_FILES["approval_file"])){
           $approval_file_name = $this->miscelleneous->upload_file_to_s3($_FILES["approval_file"], "nrn_approval", $allowedExts, $booking_id, "nrn_approvals_files", "incoming_approve_nrn");
        }

        $data_nrn = array(
            'booking_id'=>$booking_id,
            'email_to'=>trim($email),
            'approval_file'=>$approval_file_name,
            'remark'=>trim($remarks)
        );

 $where_shipped = array('booking_id' => trim($booking_id),'shipped_date IS NOT NULL'=>NULL);
 $check_shipped_status = $this->partner_model->get_spare_parts_by_any("*",$where_shipped);
        $response = $this->partner_model->insert_nrn_approval($data_nrn);
        if ($response  && empty($check_shipped_status)) {

            $select_invemtory = "partner_id,requested_inventory_id,quantity,booking_id,status,entity_type";
            $where_inventory = array('booking_id' => trim($booking_id),'entity_type'=>_247AROUND_SF_STRING,'status'=>SPARE_PARTS_REQUESTED);
            $spare_inventory_update = $this->partner_model->get_spare_parts_by_any($select_invemtory,$where_inventory);

            foreach ($spare_inventory_update as  $update_pending) {
                 
                $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $update_pending['partner_id'], $update_pending['requested_inventory_id'], -$update_pending['quantity']);
            }

        
                $where = array('booking_id' => trim($booking_id));
                $data = array(
                    'status'=>NRN_APPROVED_BY_PARTNER,
                    'nrn_approv_by_partner'=>1
                );
                $response = $this->service_centers_model->update_spare_parts($where, $data);
                echo "Spare Updated--";
                    $booking['internal_status'] =NRN_APPROVED_BY_PARTNER;
                    $booking['current_status'] = _247AROUND_PENDING;
                    $actor="";
                    $next_action="";
                    $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING,NRN_APPROVED_BY_PARTNER, $partner_id, $booking_id);
                
                if (!empty($partner_status)) {
                    $booking['partner_current_status'] = $partner_status[0];
                    $booking['partner_internal_status'] = $partner_status[1];
                    $actor = $booking['actor'] = $partner_status[2];
                    $next_action = $booking['next_action'] = $partner_status[3];
                }
                $this->booking_model->update_booking($booking_id, $booking);
                echo "Booking Updated--";
               $data_service_center=array(
                        'current_status'=>'InProcess',
                        'internal_status'=>NRN_APPROVED_BY_PARTNER
                );
               $this->vendor_model->update_service_center_action($booking_id, $data_service_center);
               echo "Service Center Action Updated--";
                $new_state=NRN_APPROVED_BY_PARTNER;
                    $this->notify->insert_state_change($booking_id, $new_state,SPARE_PART_ON_APPROVAL, NRN_TO_BE_SHIPPED_BY_PARTNER." - ".$remarks, _247AROUND_DEFAULT_AGENT, 'Videocon', $actor,$next_action, NRN_TO_BE_APPROVED_BY_PARTNER,_247AROUND);
                echo "1";   
                log_message('info', __FUNCTION__ . " New State: " . NRN_APPROVED_BY_PARTNER . " Booking id: " . $booking_id);
        }else{
           echo "0";
        }
       
    }



    function nrn_bulk(){

    //  print_r($this->session->all_userdata());
      $bookings = array(
  array('booking_id' => 'LP-1269471905183'),
  array('booking_id' => 'LP-1555031906283'),
  array('booking_id' => 'LP-1927881907222'),
  array('booking_id' => 'LP-2018091905047'),
  array('booking_id' => 'LP-2266961906073'),
  array('booking_id' => 'LP-23216519061113'),
  array('booking_id' => 'LP-2761711905034'),
  array('booking_id' => 'LP-276911906122'),
  array('booking_id' => 'LP-2857801907109'),
  array('booking_id' => 'LP-2866901906222'),
  array('booking_id' => 'LP-3463361905102'),
  array('booking_id' => 'LP-3524381907152'),
  array('booking_id' => 'LP-3593291905168'),
  array('booking_id' => 'LP-3593951903101'),
  array('booking_id' => 'LP-3608561903131'),
  array('booking_id' => 'LP-3612661903192'),
  array('booking_id' => 'LP-3615541903232'),
  array('booking_id' => 'LP-361901905062'),
  array('booking_id' => 'LP-3623251903161'),
  array('booking_id' => 'LP-3664641904172'),
  array('booking_id' => 'LP-3690301904021'),
  array('booking_id' => 'LP-3746711905142'),
  array('booking_id' => 'LP-3753461904161'),
  array('booking_id' => 'LP-3776671904201'),
  array('booking_id' => 'LP-3835321905011'),
  array('booking_id' => 'LP-3837711905011'),
  array('booking_id' => 'LP-3840751905021'),
  array('booking_id' => 'LP-3843431905021'),
  array('booking_id' => 'LP-3843601905021'),
  array('booking_id' => 'LP-3844351905021'),
  array('booking_id' => 'LP-3844761905173'),
  array('booking_id' => 'LP-3850121906243'),
  array('booking_id' => 'LP-3851891905031'),
  array('booking_id' => 'LP-3852841905031'),
  array('booking_id' => 'LP-3854881905031'),
  array('booking_id' => 'LP-3855531905031'),
  array('booking_id' => 'LP-3855641906122'),
  array('booking_id' => 'LP-3856081905031'),
  array('booking_id' => 'LP-3857931905112'),
  array('booking_id' => 'LP-3858061905312'),
  array('booking_id' => 'LP-3861261905223'),
  array('booking_id' => 'LP-3862691905041'),
  array('booking_id' => 'LP-3862831905041'),
  array('booking_id' => 'LP-3864301905041'),
  array('booking_id' => 'LP-3864561905293'),
  array('booking_id' => 'LP-3865011905041'),
  array('booking_id' => 'LP-3865741905041'),
  array('booking_id' => 'LP-3867601905041'),
  array('booking_id' => 'LP-3870561905061'),
  array('booking_id' => 'LP-3870721907173'),
  array('booking_id' => 'LP-3872591905222'),
  array('booking_id' => 'LP-3874911905051'),
  array('booking_id' => 'LP-3876681905282'),
  array('booking_id' => 'LP-3877641907083'),
  array('booking_id' => 'LP-3878381906063'),
  array('booking_id' => 'LP-3879621906182'),
  array('booking_id' => 'LP-3879721905051'),
  array('booking_id' => 'LP-3882821906212'),
  array('booking_id' => 'LP-3885921905092'),
  array('booking_id' => 'LP-3892401905061'),
  array('booking_id' => 'LP-3892671905061'),
  array('booking_id' => 'LP-3893131905061'),
  array('booking_id' => 'LP-3893291905061'),
  array('booking_id' => 'LP-3894721905061'),
  array('booking_id' => 'LP-3894911907065'),
  array('booking_id' => 'LP-3895241905061'),
  array('booking_id' => 'LP-3895381907152'),
  array('booking_id' => 'LP-3896221905061'),
  array('booking_id' => 'LP-3897031905071'),
  array('booking_id' => 'LP-3907691905112'),
  array('booking_id' => 'LP-3907731905081'),
  array('booking_id' => 'LP-3908271905081'),
  array('booking_id' => 'LP-3908421905081'),
  array('booking_id' => 'LP-3909151905182'),
  array('booking_id' => 'LP-3909801905081'),
  array('booking_id' => 'LP-3910181905081'),
  array('booking_id' => 'LP-3913591905081'),
  array('booking_id' => 'LP-3913781905081'),
  array('booking_id' => 'LP-3914601905132'),
  array('booking_id' => 'LP-3915851907053'),
  array('booking_id' => 'LP-3915951905081'),
  array('booking_id' => 'LP-3917411905081'),
  array('booking_id' => 'LP-3918041905081'),
  array('booking_id' => 'LP-3920701905081'),
  array('booking_id' => 'LP-3921351905081'),
  array('booking_id' => 'LP-3921411905202'),
  array('booking_id' => 'LP-3923901905081'),
  array('booking_id' => 'LP-3924921905222'),
  array('booking_id' => 'LP-3927931905091'),
  array('booking_id' => 'LP-3929211907207'),
  array('booking_id' => 'LP-3929211907208'),
  array('booking_id' => 'LP-3930711905091'),
  array('booking_id' => 'LP-3932481906092'),
  array('booking_id' => 'LP-3934301905091'),
  array('booking_id' => 'LP-3937461905172'),
  array('booking_id' => 'LP-3937711905091'),
  array('booking_id' => 'LP-3938731905091'),
  array('booking_id' => 'LP-3941781905091'),
  array('booking_id' => 'LP-3941841905091'),
  array('booking_id' => 'LP-3945941905101'),
  array('booking_id' => 'LP-3946261905101'),
  array('booking_id' => 'LP-3948051905101'),
  array('booking_id' => 'LP-3948111905101'),
  array('booking_id' => 'LP-3949121905101'),
  array('booking_id' => 'LP-3950551905101'),
  array('booking_id' => 'LP-3953781905101'),
  array('booking_id' => 'LP-3954941905162'),
  array('booking_id' => 'LP-3955821905111'),
  array('booking_id' => 'LP-3956621907136'),
  array('booking_id' => 'LP-3958151905101'),
  array('booking_id' => 'LP-3959081905111'),
  array('booking_id' => 'LP-3960091905111'),
  array('booking_id' => 'LP-3960671905192'),
  array('booking_id' => 'LP-3963611905131'),
  array('booking_id' => 'LP-3963731905111'),
  array('booking_id' => 'LP-3964661905111'),
  array('booking_id' => 'LP-3964911905111'),
  array('booking_id' => 'LP-3965021906116'),
  array('booking_id' => 'LP-3968531905111'),
  array('booking_id' => 'LP-3969331905131'),
  array('booking_id' => 'LP-3970371905131'),
  array('booking_id' => 'LP-3970791905111'),
  array('booking_id' => 'LP-3971391905111'),
  array('booking_id' => 'LP-3973821905312'),
  array('booking_id' => 'LP-3974051905111'),
  array('booking_id' => 'LP-3974631905172'),
  array('booking_id' => 'LP-3975611905121'),
  array('booking_id' => 'LP-3975841905121'),
  array('booking_id' => 'LP-3976061905121'),
  array('booking_id' => 'LP-3976421905121'),
  array('booking_id' => 'LP-3978111905121'),
  array('booking_id' => 'LP-3978281905121'),
  array('booking_id' => 'LP-3979071905131'),
  array('booking_id' => 'LP-3979201905131'),
  array('booking_id' => 'LP-3979501905131'),
  array('booking_id' => 'LP-3979521905121'),
  array('booking_id' => 'LP-3979961905121'),
  array('booking_id' => 'LP-3981341905131'),
  array('booking_id' => 'LP-3983631905131'),
  array('booking_id' => 'LP-3983671905131'),
  array('booking_id' => 'LP-3983951905253'),
  array('booking_id' => 'LP-3987841907022'),
  array('booking_id' => 'LP-3988611905141'),
  array('booking_id' => 'LP-3988791905131'),
  array('booking_id' => 'LP-3990171905131'),
  array('booking_id' => 'LP-3990481905312'),
  array('booking_id' => 'LP-3990681905131'),
  array('booking_id' => 'LP-3992041905131'),
  array('booking_id' => 'LP-3992871905131'),
  array('booking_id' => 'LP-3992921905131'),
  array('booking_id' => 'LP-3993491905141'),
  array('booking_id' => 'LP-3994001905141'),
  array('booking_id' => 'LP-3994061905131'),
  array('booking_id' => 'LP-3994301905182'),
  array('booking_id' => 'LP-3995381905131'),
  array('booking_id' => 'LP-3995471905131'),
  array('booking_id' => 'LP-3997261905131'),
  array('booking_id' => 'LP-3998131907262'),
  array('booking_id' => 'LP-3999391905141'),
  array('booking_id' => 'LP-3999401905141'),
  array('booking_id' => 'LP-4002521905141'),
  array('booking_id' => 'LP-4002521906204'),
  array('booking_id' => 'LP-4003341905141'),
  array('booking_id' => 'LP-4004271905212'),
  array('booking_id' => 'LP-4004411905141'),
  array('booking_id' => 'LP-4005121905141'),
  array('booking_id' => 'LP-4005171905141'),
  array('booking_id' => 'LP-4006591905141'),
  array('booking_id' => 'LP-4007691905141'),
  array('booking_id' => 'LP-4009161906072'),
  array('booking_id' => 'LP-4010091906112'),
  array('booking_id' => 'LP-4011011905282'),
  array('booking_id' => 'LP-4011541906033'),
  array('booking_id' => 'LP-4011961905141'),
  array('booking_id' => 'LP-4012841906082'),
  array('booking_id' => 'LP-4015881905182'),
  array('booking_id' => 'LP-4017171905151'),
  array('booking_id' => 'LP-4017301905151'),
  array('booking_id' => 'LP-4017341905151'),
  array('booking_id' => 'LP-4017401905151'),
  array('booking_id' => 'LP-4018401905151'),
  array('booking_id' => 'LP-4018551905151'),
  array('booking_id' => 'LP-4018951905151'),
  array('booking_id' => 'LP-4020371905202'),
  array('booking_id' => 'LP-4021471905151'),
  array('booking_id' => 'LP-4021511905151'),
  array('booking_id' => 'LP-4021851905151'),
  array('booking_id' => 'LP-4022241905151'),
  array('booking_id' => 'LP-4022451905151'),
  array('booking_id' => 'LP-4022861905151'),
  array('booking_id' => 'LP-4024261905161'),
  array('booking_id' => 'LP-4025111905161'),
  array('booking_id' => 'LP-4026251905172'),
  array('booking_id' => 'LP-4027721905161'),
  array('booking_id' => 'LP-4029331905192'),
  array('booking_id' => 'LP-4029381905171'),
  array('booking_id' => 'LP-4029441905161'),
  array('booking_id' => 'LP-4029671905161'),
  array('booking_id' => 'LP-4030201907013'),
  array('booking_id' => 'LP-4030431905161'),
  array('booking_id' => 'LP-4031381905161'),
  array('booking_id' => 'LP-4031771905161'),
  array('booking_id' => 'LP-4032711905161'),
  array('booking_id' => 'LP-4034601905171'),
  array('booking_id' => 'LP-4035331905161'),
  array('booking_id' => 'LP-4035961905161'),
  array('booking_id' => 'LP-4036101907222'),
  array('booking_id' => 'LP-4038341905161'),
  array('booking_id' => 'LP-4040201905161'),
  array('booking_id' => 'LP-4040271906032'),
  array('booking_id' => 'LP-4040361905161'),
  array('booking_id' => 'LP-4043411905171'),
  array('booking_id' => 'LP-4043641906072'),
  array('booking_id' => 'LP-4043651905171'),
  array('booking_id' => 'LP-4044831905171'),
  array('booking_id' => 'LP-4045011905171'),
  array('booking_id' => 'LP-4045441905171'),
  array('booking_id' => 'LP-4046691905171'),
  array('booking_id' => 'LP-4046981905171'),
  array('booking_id' => 'LP-4048031905171'),
  array('booking_id' => 'LP-4048071905171'),
  array('booking_id' => 'LP-4048131905171'),
  array('booking_id' => 'LP-4049121905171'),
  array('booking_id' => 'LP-4049361905171'),
  array('booking_id' => 'LP-4049421906082'),
  array('booking_id' => 'LP-4049841905171'),
  array('booking_id' => 'LP-4051081905171'),
  array('booking_id' => 'LP-4051101905171'),
  array('booking_id' => 'LP-4052471905171'),
  array('booking_id' => 'LP-4053901905242'),
  array('booking_id' => 'LP-4055081905171'),
  array('booking_id' => 'LP-4055411905171'),
  array('booking_id' => 'LP-4057481907026'),
  array('booking_id' => 'LP-4059001905181'),
  array('booking_id' => 'LP-4059611905181'),
  array('booking_id' => 'LP-4061331905181'),
  array('booking_id' => 'LP-4061391905181'),
  array('booking_id' => 'LP-4062231906223'),
  array('booking_id' => 'LP-4062961905181'),
  array('booking_id' => 'LP-4064061905181'),
  array('booking_id' => 'LP-4064851906276'),
  array('booking_id' => 'LP-4065301905191'),
  array('booking_id' => 'LP-4065921905181'),
  array('booking_id' => 'LP-4068201905181'),
  array('booking_id' => 'LP-4068711905202'),
  array('booking_id' => 'LP-4069071905181'),
  array('booking_id' => 'LP-4069501905181'),
  array('booking_id' => 'LP-4070181905181'),
  array('booking_id' => 'LP-4070221905181'),
  array('booking_id' => 'LP-4072471905212'),
  array('booking_id' => 'LP-4077341905191'),
  array('booking_id' => 'LP-4078551905201'),
  array('booking_id' => 'LP-4078661905201'),
  array('booking_id' => 'LP-4080171905201'),
  array('booking_id' => 'LP-4080281908033'),
  array('booking_id' => 'LP-4080331905201'),
  array('booking_id' => 'LP-4081131905201'),
  array('booking_id' => 'LP-4081181905201'),
  array('booking_id' => 'LP-4082641905201'),
  array('booking_id' => 'LP-4084001905201'),
  array('booking_id' => 'LP-4085221906255'),
  array('booking_id' => 'LP-4085721905201'),
  array('booking_id' => 'LP-4087431906212'),
  array('booking_id' => 'LP-4087751905201'),
  array('booking_id' => 'LP-4087831905201'),
  array('booking_id' => 'LP-4087961905201'),
  array('booking_id' => 'LP-4088161905201'),
  array('booking_id' => 'LP-4088941905201'),
  array('booking_id' => 'LP-4089321905201'),
  array('booking_id' => 'LP-4089341905201'),
  array('booking_id' => 'LP-4089581906082'),
  array('booking_id' => 'LP-4090081905232'),
  array('booking_id' => 'LP-4090321905201'),
  array('booking_id' => 'LP-4090651905201'),
  array('booking_id' => 'LP-4091081905211'),
  array('booking_id' => 'LP-4091511905211'),
  array('booking_id' => 'LP-4091571906012'),
  array('booking_id' => 'LP-4094561905201'),
  array('booking_id' => 'LP-4094781905272'),
  array('booking_id' => 'LP-4095071905201'),
  array('booking_id' => 'LP-4095271905201'),
  array('booking_id' => 'LP-4095601905252'),
  array('booking_id' => 'LP-4096121907225'),
  array('booking_id' => 'LP-4096781905201'),
  array('booking_id' => 'LP-4097211905201'),
  array('booking_id' => 'LP-4097241905201'),
  array('booking_id' => 'LP-4097921905211'),
  array('booking_id' => 'LP-4098231905211'),
  array('booking_id' => 'LP-4099471905211'),
  array('booking_id' => 'LP-4103121907172'),
  array('booking_id' => 'LP-4103441905211'),
  array('booking_id' => 'LP-4103501905211'),
  array('booking_id' => 'LP-4103611905211'),
  array('booking_id' => 'LP-4103691906112'),
  array('booking_id' => 'LP-4104571905211'),
  array('booking_id' => 'LP-4104911905211'),
  array('booking_id' => 'LP-4105831905211'),
  array('booking_id' => 'LP-4106181906042'),
  array('booking_id' => 'LP-4106281905211'),
  array('booking_id' => 'LP-4106921905302'),
  array('booking_id' => 'LP-4107021905211'),
  array('booking_id' => 'LP-4108001905211'),
  array('booking_id' => 'LP-4108011905211'),
  array('booking_id' => 'LP-4111291905211'),
  array('booking_id' => 'LP-4111581905211'),
  array('booking_id' => 'LP-4112141905221'),
  array('booking_id' => 'LP-4112291905211'),
  array('booking_id' => 'LP-4112841905211'),
  array('booking_id' => 'LP-4114381905221'),
  array('booking_id' => 'LP-4114961905221'),
  array('booking_id' => 'LP-4116281905221'),
  array('booking_id' => 'LP-4118791905221'),
  array('booking_id' => 'LP-4119451906042'),
  array('booking_id' => 'LP-4119871905221'),
  array('booking_id' => 'LP-4120571905221'),
  array('booking_id' => 'LP-4121111905221'),
  array('booking_id' => 'LP-4121551905221'),
  array('booking_id' => 'LP-4123071906202'),
  array('booking_id' => 'LP-4123421905221'),
  array('booking_id' => 'LP-4123701905221'),
  array('booking_id' => 'LP-4123921905221'),
  array('booking_id' => 'LP-4124861905231'),
  array('booking_id' => 'LP-4125161905231'),
  array('booking_id' => 'LP-4125301905231'),
  array('booking_id' => 'LP-4125481905231'),
  array('booking_id' => 'LP-4125581905231'),
  array('booking_id' => 'LP-4125771905231'),
  array('booking_id' => 'LP-4125941905231'),
  array('booking_id' => 'LP-4126261905231'),
  array('booking_id' => 'LP-4126511906132'),
  array('booking_id' => 'LP-4126991905231'),
  array('booking_id' => 'LP-4127341906122'),
  array('booking_id' => 'LP-4129871905231'),
  array('booking_id' => 'LP-4130841905231'),
  array('booking_id' => 'LP-4130871905231'),
  array('booking_id' => 'LP-4131011907163'),
  array('booking_id' => 'LP-4131601905231'),
  array('booking_id' => 'LP-4131681905231'),
  array('booking_id' => 'LP-4131711905231'),
  array('booking_id' => 'LP-4132781905231'),
  array('booking_id' => 'LP-4132961905231'),
  array('booking_id' => 'LP-4133271905231'),
  array('booking_id' => 'LP-4133301907132'),
  array('booking_id' => 'LP-4133481905231'),
  array('booking_id' => 'LP-4134051905231'),
  array('booking_id' => 'LP-4134511905231'),
  array('booking_id' => 'LP-4134771905231'),
  array('booking_id' => 'LP-4136701905231'),
  array('booking_id' => 'LP-4137271905231'),
  array('booking_id' => 'LP-4137661905231'),
  array('booking_id' => 'LP-4138111905252'),
  array('booking_id' => 'LP-4139051905231'),
  array('booking_id' => 'LP-4139061905231'),
  array('booking_id' => 'LP-4139671905231'),
  array('booking_id' => 'LP-4142701905241'),
  array('booking_id' => 'LP-4143601905241'),
  array('booking_id' => 'LP-4143611905241'),
  array('booking_id' => 'LP-4143981905241'),
  array('booking_id' => 'LP-4144341905241'),
  array('booking_id' => 'LP-4144591905241'),
  array('booking_id' => 'LP-4144621905241'),
  array('booking_id' => 'LP-4144661905241'),
  array('booking_id' => 'LP-4145541905241'),
  array('booking_id' => 'LP-4146061905241'),
  array('booking_id' => 'LP-4146171905241'),
  array('booking_id' => 'LP-4146971905241'),
  array('booking_id' => 'LP-4147181905241'),
  array('booking_id' => 'LP-4147441905241'),
  array('booking_id' => 'LP-4147851905241'),
  array('booking_id' => 'LP-4149311905241'),
  array('booking_id' => 'LP-4150281905241'),
  array('booking_id' => 'LP-4150851905241'),
  array('booking_id' => 'LP-4151801905241'),
  array('booking_id' => 'LP-4152191905241'),
  array('booking_id' => 'LP-4152731905241'),
  array('booking_id' => 'LP-4157701905261'),
  array('booking_id' => 'LP-4158571905251'),
  array('booking_id' => 'LP-4159321905251'),
  array('booking_id' => 'LP-4161111905251'),
  array('booking_id' => 'LP-4166311905251'),
  array('booking_id' => 'LP-4166401905271'),
  array('booking_id' => 'LP-4167691905262'),
  array('booking_id' => 'LP-4167991905251'),
  array('booking_id' => 'LP-4168241905251'),
  array('booking_id' => 'LP-4168901905251'),
  array('booking_id' => 'LP-4168921905251'),
  array('booking_id' => 'LP-4168991905251'),
  array('booking_id' => 'LP-4169101905302'),
  array('booking_id' => 'LP-4169341905302'),
  array('booking_id' => 'LP-4170481905251'),
  array('booking_id' => 'LP-4170501907042'),
  array('booking_id' => 'LP-4172161905261'),
  array('booking_id' => 'LP-4175141905261'),
  array('booking_id' => 'LP-4176891905261'),
  array('booking_id' => 'LP-4177701905303'),
  array('booking_id' => 'LP-4178611905271'),
  array('booking_id' => 'LP-4179341905292'),
  array('booking_id' => 'LP-4179631906102'),
  array('booking_id' => 'LP-4180511905271'),
  array('booking_id' => 'LP-4181381905271'),
  array('booking_id' => 'LP-4181451905271'),
  array('booking_id' => 'LP-4181651905271'),
  array('booking_id' => 'LP-4181751905271'),
  array('booking_id' => 'LP-4182871905271'),
  array('booking_id' => 'LP-4183651905271'),
  array('booking_id' => 'LP-4183721905271'),
  array('booking_id' => 'LP-4184151906202'),
  array('booking_id' => 'LP-4184211905271'),
  array('booking_id' => 'LP-4184581905271'),
  array('booking_id' => 'LP-4184671905271'),
  array('booking_id' => 'LP-4185321905271'),
  array('booking_id' => 'LP-4185481905271'),
  array('booking_id' => 'LP-4185541905271'),
  array('booking_id' => 'LP-4186491905271'),
  array('booking_id' => 'LP-4186551905271'),
  array('booking_id' => 'LP-4186701905271'),
  array('booking_id' => 'LP-4186761905271'),
  array('booking_id' => 'LP-4186791905271'),
  array('booking_id' => 'LP-4186881907242'),
  array('booking_id' => 'LP-4186951906282'),
  array('booking_id' => 'LP-4187641905271'),
  array('booking_id' => 'LP-4187831905271'),
  array('booking_id' => 'LP-4188851906212'),
  array('booking_id' => 'LP-4189161905271'),
  array('booking_id' => 'LP-4189281905271'),
  array('booking_id' => 'LP-4191161905281'),
  array('booking_id' => 'LP-4191561905281'),
  array('booking_id' => 'LP-4193751905281'),
  array('booking_id' => 'LP-4194611906192'),
  array('booking_id' => 'LP-4195891905281'),
  array('booking_id' => 'LP-4199651906173'),
  array('booking_id' => 'LP-4200241905281'),
  array('booking_id' => 'LP-4200901907023'),
  array('booking_id' => 'LP-4201111905281'),
  array('booking_id' => 'LP-4202001905281'),
  array('booking_id' => 'LP-4203131905281'),
  array('booking_id' => 'LP-4203781905281'),
  array('booking_id' => 'LP-4205121905281'),
  array('booking_id' => 'LP-4205801905281'),
  array('booking_id' => 'LP-4205821905281'),
  array('booking_id' => 'LP-4206301905291'),
  array('booking_id' => 'LP-4206341905291'),
  array('booking_id' => 'LP-4206441905291'),
  array('booking_id' => 'LP-4206671905291'),
  array('booking_id' => 'LP-4206851905291'),
  array('booking_id' => 'LP-4206891905291'),
  array('booking_id' => 'LP-4207011905291'),
  array('booking_id' => 'LP-4207201905291'),
  array('booking_id' => 'LP-4207741905291'),
  array('booking_id' => 'LP-4209051905291'),
  array('booking_id' => 'LP-4209451906212'),
  array('booking_id' => 'LP-4210691907124'),
  array('booking_id' => 'LP-4211181905291'),
  array('booking_id' => 'LP-4213651906162'),
  array('booking_id' => 'LP-4213891905291'),
  array('booking_id' => 'LP-4214211907253'),
  array('booking_id' => 'LP-4214341906162'),
  array('booking_id' => 'LP-4215401905291'),
  array('booking_id' => 'LP-4215411906162'),
  array('booking_id' => 'LP-4215491905291'),
  array('booking_id' => 'LP-4215891905291'),
  array('booking_id' => 'LP-4215941905291'),
  array('booking_id' => 'LP-4215991905291'),
  array('booking_id' => 'LP-4216311905291'),
  array('booking_id' => 'LP-4216531906172'),
  array('booking_id' => 'LP-4216641905291'),
  array('booking_id' => 'LP-4216951905291'),
  array('booking_id' => 'LP-4217471905291'),
  array('booking_id' => 'LP-4218521905291'),
  array('booking_id' => 'LP-4220151905291'),
  array('booking_id' => 'LP-4221851905291'),
  array('booking_id' => 'LP-4224061905301'),
  array('booking_id' => 'LP-4225571907204'),
  array('booking_id' => 'LP-4225601905301'),
  array('booking_id' => 'LP-4227271905301'),
  array('booking_id' => 'LP-4227271905302'),
  array('booking_id' => 'LP-4227521905301'),
  array('booking_id' => 'LP-4227561905301'),
  array('booking_id' => 'LP-4228571905301'),
  array('booking_id' => 'LP-4230071905301'),
  array('booking_id' => 'LP-4230311905301'),
  array('booking_id' => 'LP-4231521905301'),
  array('booking_id' => 'LP-4231781905301'),
  array('booking_id' => 'LP-4232021905301'),
  array('booking_id' => 'LP-4233071905301'),
  array('booking_id' => 'LP-4233561905301'),
  array('booking_id' => 'LP-4233741905301'),
  array('booking_id' => 'LP-4234391906222'),
  array('booking_id' => 'LP-4236441905301'),
  array('booking_id' => 'LP-4238641907192'),
  array('booking_id' => 'LP-4238661905301'),
  array('booking_id' => 'LP-4238901905301'),
  array('booking_id' => 'LP-4239751905311'),
  array('booking_id' => 'LP-4240011906102'),
  array('booking_id' => 'LP-4241701905311'),
  array('booking_id' => 'LP-4241861905311'),
  array('booking_id' => 'LP-4246681905311'),
  array('booking_id' => 'LP-4247541905311'),
  array('booking_id' => 'LP-4249101905311'),
  array('booking_id' => 'LP-4249601905311'),
  array('booking_id' => 'LP-4250531905311'),
  array('booking_id' => 'LP-4252461905311'),
  array('booking_id' => 'LP-4252701905311'),
  array('booking_id' => 'LP-4253591905311'),
  array('booking_id' => 'LP-4253671905311'),
  array('booking_id' => 'LP-4254011905311'),
  array('booking_id' => 'LP-4256591906011'),
  array('booking_id' => 'LP-4256901906011'),
  array('booking_id' => 'LP-4258381906013'),
  array('booking_id' => 'LP-4258731906011'),
  array('booking_id' => 'LP-4258971906011'),
  array('booking_id' => 'LP-4260271906011'),
  array('booking_id' => 'LP-4260651906011'),
  array('booking_id' => 'LP-4260901906011'),
  array('booking_id' => 'LP-4261921907093'),
  array('booking_id' => 'LP-4263351907154'),
  array('booking_id' => 'LP-4263791906273'),
  array('booking_id' => 'LP-4263961906011'),
  array('booking_id' => 'LP-4264631906042'),
  array('booking_id' => 'LP-4265961906011'),
  array('booking_id' => 'LP-4270551906011'),
  array('booking_id' => 'LP-4271751906072'),
  array('booking_id' => 'LP-4273741906022'),
  array('booking_id' => 'LP-4275101906021'),
  array('booking_id' => 'LP-4275301906021'),
  array('booking_id' => 'LP-4275871906284'),
  array('booking_id' => 'LP-4275931906102'),
  array('booking_id' => 'LP-4276951906021'),
  array('booking_id' => 'LP-4277321906021'),
  array('booking_id' => 'LP-4285401906031'),
  array('booking_id' => 'LP-4287941906031'),
  array('booking_id' => 'LP-4288151906031'),
  array('booking_id' => 'LP-4289291906031'),
  array('booking_id' => 'LP-4289981906031'),
  array('booking_id' => 'LP-4290831906031'),
  array('booking_id' => 'LP-4291091906186'),
  array('booking_id' => 'LP-4292231906031'),
  array('booking_id' => 'LP-4295051906031'),
  array('booking_id' => 'LP-4296591906072'),
  array('booking_id' => 'LP-4304211906041'),
  array('booking_id' => 'LP-4304581906041'),
  array('booking_id' => 'LP-4304591906041'),
  array('booking_id' => 'LP-4310991906262'),
  array('booking_id' => 'LP-4311461906041'),
  array('booking_id' => 'LP-4312691906041'),
  array('booking_id' => 'LP-4314231906041'),
  array('booking_id' => 'LP-4317251906051'),
  array('booking_id' => 'LP-4318841906051'),
  array('booking_id' => 'LP-4321491906051'),
  array('booking_id' => 'LP-4325011907045'),
  array('booking_id' => 'LP-4327001906051'),
  array('booking_id' => 'LP-4328661906051'),
  array('booking_id' => 'LP-4329241906061'),
  array('booking_id' => 'LP-4330271906061'),
  array('booking_id' => 'LP-4330601906282'),
  array('booking_id' => 'LP-4333131906061'),
  array('booking_id' => 'LP-4336071906061'),
  array('booking_id' => 'LP-4342021906061'),
  array('booking_id' => 'LP-4342031906242'),
  array('booking_id' => 'LP-4342351906212'),
  array('booking_id' => 'LP-4344461906061'),
  array('booking_id' => 'LP-4345251906061'),
  array('booking_id' => 'LP-4348401906082'),
  array('booking_id' => 'LP-4348551906071'),
  array('booking_id' => 'LP-4348661906071'),
  array('booking_id' => 'LP-4350091906071'),
  array('booking_id' => 'LP-4350211906071'),
  array('booking_id' => 'LP-4351391906071'),
  array('booking_id' => 'LP-4351461906071'),
  array('booking_id' => 'LP-4351681906071'),
  array('booking_id' => 'LP-4353131906071'),
  array('booking_id' => 'LP-4353251906071'),
  array('booking_id' => 'LP-4355191906071'),
  array('booking_id' => 'LP-4357411906081'),
  array('booking_id' => 'LP-4357731907022'),
  array('booking_id' => 'LP-4358941906071'),
  array('booking_id' => 'LP-4359411906071'),
  array('booking_id' => 'LP-4359831906255'),
  array('booking_id' => 'LP-4360651906071'),
  array('booking_id' => 'LP-4363101906081'),
  array('booking_id' => 'LP-4365761906081'),
  array('booking_id' => 'LP-4367221906081'),
  array('booking_id' => 'LP-4367651906259'),
  array('booking_id' => 'LP-4368451906082'),
  array('booking_id' => 'LP-4369111906081'),
  array('booking_id' => 'LP-4371171906081'),
  array('booking_id' => 'LP-4374701906081'),
  array('booking_id' => 'LP-4377931906091'),
  array('booking_id' => 'LP-4380241906091'),
  array('booking_id' => 'LP-4385211906101'),
  array('booking_id' => 'LP-4386461906101'),
  array('booking_id' => 'LP-4387231906101'),
  array('booking_id' => 'LP-4388241906152'),
  array('booking_id' => 'LP-4388981906101'),
  array('booking_id' => 'LP-4389681906101'),
  array('booking_id' => 'LP-4390911906101'),
  array('booking_id' => 'LP-4391571906101'),
  array('booking_id' => 'LP-4391971906182'),
  array('booking_id' => 'LP-4394181906101'),
  array('booking_id' => 'LP-4398071906101'),
  array('booking_id' => 'LP-4402921906111'),
  array('booking_id' => 'LP-4403801906111'),
  array('booking_id' => 'LP-4406191906111'),
  array('booking_id' => 'LP-4407191906111'),
  array('booking_id' => 'LP-4409231906111'),
  array('booking_id' => 'LP-4410061906162'),
  array('booking_id' => 'LP-4410281906111'),
  array('booking_id' => 'LP-4413461906222'),
  array('booking_id' => 'LP-4413851906111'),
  array('booking_id' => 'LP-4415151906111'),
  array('booking_id' => 'LP-4415531906111'),
  array('booking_id' => 'LP-4415951906111'),
  array('booking_id' => 'LP-4418041906121'),
  array('booking_id' => 'LP-4419791906121'),
  array('booking_id' => 'LP-4420291906121'),
  array('booking_id' => 'LP-4420411906121'),
  array('booking_id' => 'LP-4420571906121'),
  array('booking_id' => 'LP-4420981906121'),
  array('booking_id' => 'LP-4421171906121'),
  array('booking_id' => 'LP-4421491906121'),
  array('booking_id' => 'LP-4421711906121'),
  array('booking_id' => 'LP-4425001906121'),
  array('booking_id' => 'LP-4425051906121'),
  array('booking_id' => 'LP-4425241906121'),
  array('booking_id' => 'LP-4425481906121'),
  array('booking_id' => 'LP-4426101906121'),
  array('booking_id' => 'LP-4426351906122'),
  array('booking_id' => 'LP-4426691906121'),
  array('booking_id' => 'LP-4429321906232'),
  array('booking_id' => 'LP-4429931906272'),
  array('booking_id' => 'LP-4431611906131'),
  array('booking_id' => 'LP-4431881906131'),
  array('booking_id' => 'LP-4432051906131'),
  array('booking_id' => 'LP-4433311906131'),
  array('booking_id' => 'LP-4434621906131'),
  array('booking_id' => 'LP-4435141906202'),
  array('booking_id' => 'LP-4435651906183'),
  array('booking_id' => 'LP-4436591906131'),
  array('booking_id' => 'LP-4437401906131'),
  array('booking_id' => 'LP-4438791906131'),
  array('booking_id' => 'LP-4440151907092'),
  array('booking_id' => 'LP-44409519061310'),
  array('booking_id' => 'LP-44409519061311'),
  array('booking_id' => 'LP-4440951906133'),
  array('booking_id' => 'LP-4440951906135'),
  array('booking_id' => 'LP-4440951906139'),
  array('booking_id' => 'LP-4440991906131'),
  array('booking_id' => 'LP-4441271906131'),
  array('booking_id' => 'LP-4441351906131'),
  array('booking_id' => 'LP-4442871906131'),
  array('booking_id' => 'LP-4445011906141'),
  array('booking_id' => 'LP-4446941906141'),
  array('booking_id' => 'LP-4447631906141'),
  array('booking_id' => 'LP-4448471906141'),
  array('booking_id' => 'LP-4451901906141'),
  array('booking_id' => 'LP-4452641906141'),
  array('booking_id' => 'LP-4453711906141'),
  array('booking_id' => 'LP-4456431906212'),
  array('booking_id' => 'LP-4456841906151'),
  array('booking_id' => 'LP-4457081906213'),
  array('booking_id' => 'LP-4457461906151'),
  array('booking_id' => 'LP-4460021906151'),
  array('booking_id' => 'LP-4464041906151'),
  array('booking_id' => 'LP-4465801906282'),
  array('booking_id' => 'LP-4466631906262'),
  array('booking_id' => 'LP-4468351906151'),
  array('booking_id' => 'LP-4470911906192'),
  array('booking_id' => 'LP-4471541906151'),
  array('booking_id' => 'LP-4472851906161'),
  array('booking_id' => 'LP-4473811906161'),
  array('booking_id' => 'LP-4475341906161'),
  array('booking_id' => 'LP-4475691906161'),
  array('booking_id' => 'LP-4477631906161'),
  array('booking_id' => 'LP-4479781906171'),
  array('booking_id' => 'LP-4479831906171'),
  array('booking_id' => 'LP-4482541906171'),
  array('booking_id' => 'LP-4484191907024'),
  array('booking_id' => 'LP-4487291906171'),
  array('booking_id' => 'LP-4489071906171'),
  array('booking_id' => 'LP-4490121906172'),
  array('booking_id' => 'LP-4491541907162'),
  array('booking_id' => 'LP-4491901906171'),
  array('booking_id' => 'LP-4492291906171'),
  array('booking_id' => 'LP-4493041906214'),
  array('booking_id' => 'LP-4493041907025'),
  array('booking_id' => 'LP-4495531906181'),
  array('booking_id' => 'LP-4495991906181'),
  array('booking_id' => 'LP-4496331906181'),
  array('booking_id' => 'LP-4498171906181'),
  array('booking_id' => 'LP-4499231906181'),
  array('booking_id' => 'LP-4501071906181'),
  array('booking_id' => 'LP-4502281906181'),
  array('booking_id' => 'LP-4502341906181'),
  array('booking_id' => 'LP-4503711906181'),
  array('booking_id' => 'LP-4503841906181'),
  array('booking_id' => 'LP-4503931906181'),
  array('booking_id' => 'LP-4504471906181'),
  array('booking_id' => 'LP-4504801906181'),
  array('booking_id' => 'LP-4506121906222'),
  array('booking_id' => 'LP-4506581906181'),
  array('booking_id' => 'LP-4506831906181'),
  array('booking_id' => 'LP-4507111906181'),
  array('booking_id' => 'LP-4507291907112'),
  array('booking_id' => 'LP-4508451906181'),
  array('booking_id' => 'LP-4510111906191'),
  array('booking_id' => 'LP-4511431906191'),
  array('booking_id' => 'LP-4511741906191'),
  array('booking_id' => 'LP-4513291906191'),
  array('booking_id' => 'LP-4515201906191'),
  array('booking_id' => 'LP-4520671906191'),
  array('booking_id' => 'LP-4521201906191'),
  array('booking_id' => 'LP-4521561906191'),
  array('booking_id' => 'LP-4521641906191'),
  array('booking_id' => 'LP-4524521906201'),
  array('booking_id' => 'LP-4525751906201'),
  array('booking_id' => 'LP-4525811907222'),
  array('booking_id' => 'LP-4526571906201'),
  array('booking_id' => 'LP-4527851906201'),
  array('booking_id' => 'LP-4528691906201'),
  array('booking_id' => 'LP-4535291906201'),
  array('booking_id' => 'LP-4535691906201'),
  array('booking_id' => 'LP-4536661906211'),
  array('booking_id' => 'LP-4536861906211'),
  array('booking_id' => 'LP-4537441906211'),
  array('booking_id' => 'LP-4540971906211'),
  array('booking_id' => 'LP-4541611906211'),
  array('booking_id' => 'LP-4541931908063'),
  array('booking_id' => 'LP-4542011906211'),
  array('booking_id' => 'LP-4543031907062'),
  array('booking_id' => 'LP-4543461906211'),
  array('booking_id' => 'LP-4544331906211'),
  array('booking_id' => 'LP-4545401906211'),
  array('booking_id' => 'LP-4546891906211'),
  array('booking_id' => 'LP-4547351906211'),
  array('booking_id' => 'LP-4547581906211'),
  array('booking_id' => 'LP-4547871906211'),
  array('booking_id' => 'LP-4548971906221'),
  array('booking_id' => 'LP-4549021906221'),
  array('booking_id' => 'LP-4549031906221'),
  array('booking_id' => 'LP-4549121906221'),
  array('booking_id' => 'LP-4549151906221'),
  array('booking_id' => 'LP-4549181906221'),
  array('booking_id' => 'LP-4549211906221'),
  array('booking_id' => 'LP-4549371906221'),
  array('booking_id' => 'LP-4550041906221'),
  array('booking_id' => 'LP-4550391906221'),
  array('booking_id' => 'LP-4550411907162'),
  array('booking_id' => 'LP-4550441906221'),
  array('booking_id' => 'LP-4550481906221'),
  array('booking_id' => 'LP-4550641907222'),
  array('booking_id' => 'LP-4550721906221'),
  array('booking_id' => 'LP-4550781906221'),
  array('booking_id' => 'LP-4550881906221'),
  array('booking_id' => 'LP-4551081906221'),
  array('booking_id' => 'LP-4551131906221'),
  array('booking_id' => 'LP-4551231906221'),
  array('booking_id' => 'LP-4552261906221'),
  array('booking_id' => 'LP-4552451906221'),
  array('booking_id' => 'LP-4552691906221'),
  array('booking_id' => 'LP-4552841906221'),
  array('booking_id' => 'LP-4553001906221'),
  array('booking_id' => 'LP-4553251906221'),
  array('booking_id' => 'LP-4553441906221'),
  array('booking_id' => 'LP-4554251906221'),
  array('booking_id' => 'LP-4554291906221'),
  array('booking_id' => 'LP-4554601906221'),
  array('booking_id' => 'LP-4554711906221'),
  array('booking_id' => 'LP-4554821906221'),
  array('booking_id' => 'LP-4555471906221'),
  array('booking_id' => 'LP-4555561906221'),
  array('booking_id' => 'LP-4555591906221'),
  array('booking_id' => 'LP-4555751906221'),
  array('booking_id' => 'LP-4555841907192'),
  array('booking_id' => 'LP-4557611906221'),
  array('booking_id' => 'LP-4557861906221'),
  array('booking_id' => 'LP-4559631906221'),
  array('booking_id' => 'LP-4560241906221'),
  array('booking_id' => 'LP-4560841906221'),
  array('booking_id' => 'LP-4563821906221'),
  array('booking_id' => 'LP-4564451906252'),
  array('booking_id' => 'LP-4565771906231'),
  array('booking_id' => 'LP-4566781906231'),
  array('booking_id' => 'LP-4568091906231'),
  array('booking_id' => 'LP-4568491906231'),
  array('booking_id' => 'LP-4568701906231'),
  array('booking_id' => 'LP-4568951906231'),
  array('booking_id' => 'LP-4575611906241'),
  array('booking_id' => 'LP-4579321906241'),
  array('booking_id' => 'LP-4579871906241'),
  array('booking_id' => 'LP-4580421906241'),
  array('booking_id' => 'LP-4581241907012'),
  array('booking_id' => 'LP-4581621906241'),
  array('booking_id' => 'LP-4584501906251'),
  array('booking_id' => 'LP-4588411906251'),
  array('booking_id' => 'LP-4588861906251'),
  array('booking_id' => 'LP-4590751906251'),
  array('booking_id' => 'LP-4591031906251'),
  array('booking_id' => 'LP-4591511906251'),
  array('booking_id' => 'LP-4593651907022'),
  array('booking_id' => 'LP-4593701906251'),
  array('booking_id' => 'LP-4596541906261'),
  array('booking_id' => 'LP-4597561906261'),
  array('booking_id' => 'LP-4598141906261'),
  array('booking_id' => 'LP-4598181906261'),
  array('booking_id' => 'LP-4599271906261'),
  array('booking_id' => 'LP-4599891906261'),
  array('booking_id' => 'LP-4601181907082'),
  array('booking_id' => 'LP-4605291906271'),
  array('booking_id' => 'LP-4606031906271'),
  array('booking_id' => 'LP-4608461906271'),
  array('booking_id' => 'LP-4610101906271'),
  array('booking_id' => 'LP-4610571906271'),
  array('booking_id' => 'LP-4611971907012'),
  array('booking_id' => 'LP-4612331906271'),
  array('booking_id' => 'LP-4613331906271'),
  array('booking_id' => 'LP-4613601907122'),
  array('booking_id' => 'LP-4615051906271'),
  array('booking_id' => 'LP-4616741906281'),
  array('booking_id' => 'LP-4623271907042'),
  array('booking_id' => 'LP-4625191906281'),
  array('booking_id' => 'LP-4625761907042'),
  array('booking_id' => 'LP-4629311906291'),
  array('booking_id' => 'LP-4629411906291'),
  array('booking_id' => 'LP-4629811906291'),
  array('booking_id' => 'LP-4630921906291'),
  array('booking_id' => 'LP-4634621906291'),
  array('booking_id' => 'LP-4637611906301'),
  array('booking_id' => 'LP-4640711907011'),
  array('booking_id' => 'LP-4640841907011'),
  array('booking_id' => 'LP-4641091907011'),
  array('booking_id' => 'LP-4642011907011'),
  array('booking_id' => 'LP-4643091907011'),
  array('booking_id' => 'LP-4644781907011'),
  array('booking_id' => 'LP-4649511907033'),
  array('booking_id' => 'LP-4650491907011'),
  array('booking_id' => 'LP-4653551907102'),
  array('booking_id' => 'LP-4653561907062'),
  array('booking_id' => 'LP-4654671907232'),
  array('booking_id' => 'LP-4654691907063'),
  array('booking_id' => 'LP-4654891907021'),
  array('booking_id' => 'LP-4656701907021'),
  array('booking_id' => 'LP-4656811907021'),
  array('booking_id' => 'LP-4656841907021'),
  array('booking_id' => 'LP-4658041907021'),
  array('booking_id' => 'LP-4658121907021'),
  array('booking_id' => 'LP-4658261907021'),
  array('booking_id' => 'LP-4659121907021'),
  array('booking_id' => 'LP-4659131907021'),
  array('booking_id' => 'LP-4660811907021'),
  array('booking_id' => 'LP-4662221907052'),
  array('booking_id' => 'LP-4662491907021'),
  array('booking_id' => 'LP-4665331907031'),
  array('booking_id' => 'LP-4668551907031'),
  array('booking_id' => 'LP-4669291907031'),
  array('booking_id' => 'LP-4669531907031'),
  array('booking_id' => 'LP-4669551907031'),
  array('booking_id' => 'LP-4669881907031'),
  array('booking_id' => 'LP-4673201907031'),
  array('booking_id' => 'LP-4676411907031'),
  array('booking_id' => 'LP-4685341907041'),
  array('booking_id' => 'LP-4689431907051'),
  array('booking_id' => 'LP-4695811907051'),
  array('booking_id' => 'LP-4697201907051'),
  array('booking_id' => 'LP-4697821907051'),
  array('booking_id' => 'LP-4698721907172'),
  array('booking_id' => 'LP-4699111907252'),
  array('booking_id' => 'LP-4699221907082'),
  array('booking_id' => 'LP-4699271907061'),
  array('booking_id' => 'LP-4704221907061'),
  array('booking_id' => 'LP-4705141907061'),
  array('booking_id' => 'LP-4705561907061'),
  array('booking_id' => 'LP-4705781907061'),
  array('booking_id' => 'LP-4707731907061'),
  array('booking_id' => 'LP-4708451907061'),
  array('booking_id' => 'LP-4708501907061'),
  array('booking_id' => 'LP-4711841907071'),
  array('booking_id' => 'LP-4712151907071'),
  array('booking_id' => 'LP-4713891907081'),
  array('booking_id' => 'LP-4714321907081'),
  array('booking_id' => 'LP-4714661907081'),
  array('booking_id' => 'LP-4715371907203'),
  array('booking_id' => 'LP-4716911907081'),
  array('booking_id' => 'LP-4716971907092'),
  array('booking_id' => 'LP-4718191907102'),
  array('booking_id' => 'LP-4718741907081'),
  array('booking_id' => 'LP-4719281907081'),
  array('booking_id' => 'LP-4720311907081'),
  array('booking_id' => 'LP-4722601907081'),
  array('booking_id' => 'LP-4723731907081'),
  array('booking_id' => 'LP-4727641907091'),
  array('booking_id' => 'LP-4729411907091'),
  array('booking_id' => 'LP-4731881907091'),
  array('booking_id' => 'LP-4734731907091'),
  array('booking_id' => 'LP-4735911907091'),
  array('booking_id' => 'LP-4736791907091'),
  array('booking_id' => 'LP-4737201907091'),
  array('booking_id' => 'LP-4737291907091'),
  array('booking_id' => 'LP-4737351907091'),
  array('booking_id' => 'LP-4742701907101'),
  array('booking_id' => 'LP-4744791907101'),
  array('booking_id' => 'LP-4747381907101'),
  array('booking_id' => 'LP-4747831907101'),
  array('booking_id' => 'LP-4751101907242'),
  array('booking_id' => 'LP-4752571907111'),
  array('booking_id' => 'LP-4755291907111'),
  array('booking_id' => 'LP-4755871907111'),
  array('booking_id' => 'LP-4761441908163'),
  array('booking_id' => 'LP-4763191907192'),
  array('booking_id' => 'LP-4764141907121'),
  array('booking_id' => 'LP-4767921907121'),
  array('booking_id' => 'LP-4768861907131'),
  array('booking_id' => 'LP-4770881907131'),
  array('booking_id' => 'LP-4772011907131'),
  array('booking_id' => 'LP-4772511907162'),
  array('booking_id' => 'LP-4772811907131'),
  array('booking_id' => 'LP-4773631907131'),
  array('booking_id' => 'LP-4774691907131'),
  array('booking_id' => 'LP-4775991907131'),
  array('booking_id' => 'LP-4777361907131'),
  array('booking_id' => 'LP-4777421907252'),
  array('booking_id' => 'LP-4779621907141'),
  array('booking_id' => 'LP-4780201907141'),
  array('booking_id' => 'LP-4780261907141'),
  array('booking_id' => 'LP-4781451908172'),
  array('booking_id' => 'LP-4788051907242'),
  array('booking_id' => 'LP-4790241907151'),
  array('booking_id' => 'LP-4790541907151'),
  array('booking_id' => 'LP-4791841907151'),
  array('booking_id' => 'LP-4791921907151'),
  array('booking_id' => 'LP-4791921907182'),
  array('booking_id' => 'LP-4794121907161'),
  array('booking_id' => 'LP-4795451907161'),
  array('booking_id' => 'LP-4801091907161'),
  array('booking_id' => 'LP-4803141907161'),
  array('booking_id' => 'LP-4803541907161'),
  array('booking_id' => 'LP-4803891907161'),
  array('booking_id' => 'LP-4804731907171'),
  array('booking_id' => 'LP-4806421907171'),
  array('booking_id' => 'LP-4807051907171'),
  array('booking_id' => 'LP-4807391907171'),
  array('booking_id' => 'LP-4812011907171'),
  array('booking_id' => 'LP-4812281907171'),
  array('booking_id' => 'LP-4812341907171'),
  array('booking_id' => 'LP-4813101907171'),
  array('booking_id' => 'LP-4823561907181'),
  array('booking_id' => 'LP-4829901907191'),
  array('booking_id' => 'LP-4830851907191'),
  array('booking_id' => 'LP-4831611907191'),
  array('booking_id' => 'LP-4833171907191'),
  array('booking_id' => 'LP-4838111907201'),
  array('booking_id' => 'LP-4838391907201'),
  array('booking_id' => 'LP-4839561907201'),
  array('booking_id' => 'LP-4840651907212'),
  array('booking_id' => 'LP-4845431907201'),
  array('booking_id' => 'LP-4845471907201'),
  array('booking_id' => 'LP-4845991907201'),
  array('booking_id' => 'LP-4848081907211'),
  array('booking_id' => 'LP-4848101907211'),
  array('booking_id' => 'LP-4855311907221'),
  array('booking_id' => 'LP-4856021907221'),
  array('booking_id' => 'LP-4856971907221'),
  array('booking_id' => 'LP-4862811907221'),
  array('booking_id' => 'LP-4863061907231'),
  array('booking_id' => 'LP-4863691907231'),
  array('booking_id' => 'LP-4867741907231'),
  array('booking_id' => 'LP-4868271907231'),
  array('booking_id' => 'LP-4869871907231'),
  array('booking_id' => 'LP-4871341907231'),
  array('booking_id' => 'LP-4871991907231'),
  array('booking_id' => 'LP-4872071907231'),
  array('booking_id' => 'LP-4873711907241'),
  array('booking_id' => 'LP-4878301907241'),
  array('booking_id' => 'LP-4878641907241'),
  array('booking_id' => 'LP-4878871907241'),
  array('booking_id' => 'LP-4881431907241'),
  array('booking_id' => 'LP-4884401907251'),
  array('booking_id' => 'LP-4887131907251'),
  array('booking_id' => 'LP-4888101907251'),
  array('booking_id' => 'LP-4894011907251'),
  array('booking_id' => 'LP-4894291907251'),
  array('booking_id' => 'LP-4895351907292'),
  array('booking_id' => 'LP-4898311907261'),
  array('booking_id' => 'LP-4901051907261'),
  array('booking_id' => 'LP-4902041907261'),
  array('booking_id' => 'LP-4906171907271'),
  array('booking_id' => 'LP-4911501907271'),
  array('booking_id' => 'LP-4912581907271'),
  array('booking_id' => 'LP-4915081907271'),
  array('booking_id' => 'LP-4921911908032'),
  array('booking_id' => 'LP-4923131908062'),
  array('booking_id' => 'LP-4924321907291'),
  array('booking_id' => 'LP-4929471907291'),
  array('booking_id' => 'LP-4930901907291'),
  array('booking_id' => 'LP-4934051907301'),
  array('booking_id' => 'LP-4942811908072'),
  array('booking_id' => 'LP-4945201907311'),
  array('booking_id' => 'LP-4947801907311'),
  array('booking_id' => 'LP-4947801907312'),
  array('booking_id' => 'LP-4950321907311'),
  array('booking_id' => 'LP-4959281908011'),
  array('booking_id' => 'LP-4965341908021'),
  array('booking_id' => 'LP-4967331908021'),
  array('booking_id' => 'LP-4970831908021'),
  array('booking_id' => 'LP-4971781908031'),
  array('booking_id' => 'LP-4990151908222'),
  array('booking_id' => 'LP-5005141908061'),
  array('booking_id' => 'LP-5009311908071'),
  array('booking_id' => 'LP-5028491908081'),
  array('booking_id' => 'LP-5034541908132'),
  array('booking_id' => 'LP-5038491908091'),
  array('booking_id' => 'LP-5058391908121'),
  array('booking_id' => 'LP-5069821908131'),
  array('booking_id' => 'LP-5072351908141'),
  array('booking_id' => 'LP-5081401908141'),
  array('booking_id' => 'LP-5111251908171'),
  array('booking_id' => 'LP-5129661908191')
);




      foreach ($bookings as   $booking) {
        echo $booking['booking_id']; 
          $this->do_partner_nrn_approval($booking['booking_id']);
                         // $url = base_url() . "employee/partner/do_partner_nrn_approval";
                         // $async_data['remarks'] = 'Appproved By Sudhir';
                         // $async_data['partner_id'] = 247130;
                         // $async_data['booking_id'] =trim($booking);
                         // $this->asynchronous_lib->do_background_process($url, $async_data);


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
