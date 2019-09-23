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



  function nrn_bulk(){

      print_r($this->session->all_userdata());
      $bookings =array('LP-1232031906223','LP-1583501907034','LP-1872171905263','LP-1927881907222','LP-20165219071423','LP-2761711905034','LP-3360951906142','LP-3570921903051','LP-3596911904022','LP-3610941903131','LP-3628231904102','LP-3759351904171','LP-3792841905072','LP-3811711904271','LP-3822611904291','LP-3823171904291','LP-3835171908076','LP-3835321905011','LP-3839831907283','LP-3844051905021','LP-3853581905031','LP-3861261905223','LP-3865011905041','LP-3869111905182','LP-3875231905292','LP-3878151905051','LP-3879441907283','LP-3879621906182','LP-3880781905061','LP-3881311905061','LP-3887391906122','LP-3888801905061','LP-3888981905061','LP-3890141907152','LP-3891391905212','LP-3891481905061','LP-3891531907113','LP-3892401905061','LP-3892671905061','LP-38931019080223','LP-3895241905061','LP-3895381907152','LP-3897031905071','LP-3898771905071','LP-3900381905172','LP-3903681905071','LP-3904481905071','LP-3907421906102','LP-3912441906173','LP-3912441906173','LP-3915801905082','LP-3915801906244','LP-3915951905081','LP-3921351905081','LP-3924851907033','LP-3925591907122','LP-3927151906212','LP-3932421905091','LP-3934411905091','LP-3938731905091','LP-3946261905101','LP-3946731905101','LP-3949121905101','LP-3952971905302','LP-3960081905111','LP-3966151905111','LP-3971411905111','LP-3976041905121','LP-3977311905131','LP-3977401905121','LP-3978231905121','LP-3980201905121','LP-3980691905121','LP-3983591905202','LP-3983631905131','LP-3985551905131','LP-3987301907033','LP-3987381905131','LP-3990171905131','LP-3992301905131','LP-3992881905141','LP-3994001905141','LP-3994101907012','LP-3994241905131','LP-3996421905131','LP-3997261905131','LP-3999881905141','LP-4003881905141','LP-4007101905141','LP-4007691905141','LP-4008271905141','LP-4009871905141','LP-4010071905151','LP-4011961905141','LP-4012631905141','LP-4013771905151','LP-4013771906152','LP-4014661905302','LP-4017131906134','LP-4017401905151','LP-4021471905151','LP-4021701905151','LP-4021851905151','LP-4024261905161','LP-4026761905161','LP-4026761905287','LP-4027541905161','LP-4027741905161','LP-4028371905161','LP-4028821905161','LP-4029441905161','LP-4029671905161','LP-4035961905161','LP-4037031908072','LP-4039461905161','LP-4039901905161','LP-4040631905161','LP-4041011905161','LP-4045491906012','LP-4046161905171','LP-4048251905252','LP-4049921906203','LP-4051081905171','LP-4051341905171','LP-4052471905171','LP-4054011906126','LP-4055111905171','LP-4057081905181','LP-4057141905181','LP-4057321905181','LP-4057331905303','LP-4057751906184','LP-4058071905181','LP-4061601905181','LP-4062101907222','LP-4062961905181','LP-4063021905181','LP-4063741905181','LP-4064811905181','LP-4065621905181','LP-4066351905181','LP-4066791906052','LP-4068211905181','LP-4070361905181','LP-4070541905181','LP-4070921905181','LP-4071021905181','LP-4071231906182','LP-4071281905181','LP-4071891905191','LP-4071891905312','LP-4075851905191','LP-4077091906142','LP-4078861905201','LP-4079311905201','LP-4080281908033','LP-4084001905201','LP-4086431905201','LP-4088591905201','LP-4089031905201','LP-4089251905201','LP-4089401905201','LP-4090221905201','LP-4090871905211','LP-4092041905201','LP-4096861905201','LP-4097241905201','LP-4097921905211','LP-4099961905283','LP-4101571908043','LP-4102571905211','LP-4103021905211','LP-4103381906082','LP-4105521905211','LP-4105831905211','LP-4106281905211','LP-4109191905221','LP-4110921905211','LP-4116281905221','LP-4119451906042','LP-4119531905221','LP-4119931906292','LP-4120201905221','LP-4121511905221','LP-4123301905221','LP-4123921905221','LP-4123981906173','LP-4125161905231','LP-4128111905231','LP-4129551905231','LP-4130001908044','LP-4131421905231','LP-4133071907022','LP-4134351905231','LP-4136701905231','LP-4137901905231','LP-4139051905231','LP-4140161905231','LP-4146461907112','LP-4146811905241','LP-4148091905241','LP-4149311905241','LP-4150771905241','LP-4151391905241','LP-4151491907123','LP-4151591905241','LP-4151821905241','LP-4151971905241','LP-4152531905241','LP-4152621905241','LP-4152731905241','LP-4153091905241','LP-4153131905241','LP-4153841905241','LP-4154091905241','LP-4156031905241','LP-4162481905251','LP-4164571905251','LP-4167621905251','LP-4168811905312','LP-4168991905251','LP-4170741905251','LP-4173861905261','LP-4174061905261','LP-4174091905261','LP-4174301906023','LP-4174561907203','LP-4175401905282','LP-4176831905261','LP-4177441905261','LP-4179081905271','LP-4184641907022','LP-4185431905281','LP-4189501905271','LP-4190481906182','LP-4191561905281','LP-4193131907012','LP-4193411905281','LP-4193681905281','LP-4194611906192','LP-4196541905281','LP-4196911905281','LP-4198991905281','LP-4200651905281','LP-4201451905281','LP-4201801905291','LP-4203781905281','LP-4208031905291','LP-4210691907124','LP-4211411906212','LP-4213931906202','LP-4214211907253','LP-4214341906162','LP-4214411905291','LP-4214651905291','LP-4216201905291','LP-42177819061010','LP-4217781906104','LP-4217781906107','LP-4217781906109','LP-4217981905291','LP-4218361905291','LP-4220151905291','LP-4225571907204','LP-4225601905301','LP-4226201905301','LP-4226201906132','LP-4227561905301','LP-4230981905301','LP-4235821906092','LP-4238641907192','LP-4241451905311','LP-4241681905311','LP-4244451905311','LP-4246681905311','LP-4247541905311','LP-4248781906113','LP-4248851906122','LP-4249101905311','LP-4253591905311','LP-4253671905311','LP-4258971906011','LP-4260901906011','LP-4265591907134','LP-4274191906142','LP-4274341908012','LP-4279491906152','LP-4279501907315','LP-4280041906162','LP-4286121906031','LP-4286151906031','LP-4287541906031','LP-4289511906031','LP-4290211906031','LP-4290231906031','LP-4290251906031','LP-4290471906031','LP-4297041908062','LP-4297581906041','LP-4301801906041','LP-4302061906041','LP-4302121906041','LP-4304581906041','LP-4311461906041','LP-4312511906041','LP-4315611906051','LP-4318521906051','LP-4321081906051','LP-4324451906051','LP-4330271906061','LP-4330451906061','LP-4333031906094','LP-4333131906061','LP-4341111906102','LP-4342731906071','LP-4347121906071','LP-4350341906071','LP-4350381906262','LP-4350861906071','LP-4351081906071','LP-4351391906071','LP-4351761906075','LP-43517619060822','LP-43517619060823','LP-4355001906071','LP-4355161907014','LP-4355161907266','LP-4357291906262','LP-4357731907022','LP-4359411906071','LP-4360911906071','LP-4360981906071','LP-4361191906071','LP-4361291906071','LP-4363101906081','LP-4364601906081','LP-4365771906102','LP-4366191906081','LP-43676619072018','LP-43676619072019','LP-43676619072020','LP-43676619072228','LP-4367731906081','LP-4368191906081','LP-4369681906081','LP-4370651906202','LP-4371171906081','LP-4374651906081','LP-4375851906081','LP-4386391906101','LP-4389681906101','LP-4391571906101','LP-4393141906242','LP-4393271906101','LP-4398071906101','LP-4403921906111','LP-4406191906111','LP-4408211906111','LP-4410821906192','LP-4411921906111','LP-4413081906111','LP-4415391906172','LP-4416731906111','LP-4420871906222','LP-4420981906121','LP-4422361906203','LP-4423181906121','LP-4426351906122','LP-4433061906131','LP-4433311906131','LP-4440151907092','LP-4440801906131','LP-4440991906131','LP-4441471907112','LP-4442871906131','LP-4445011906141','LP-4448041906141','LP-4448601906141','LP-4449651906141','LP-4453211906151','LP-4456391906141','LP-4457151906151','LP-4460551906151','LP-4460701906151','LP-4462231906151','LP-4464231906151','LP-4466991906151','LP-4469021906151','LP-4471541906151','LP-4475501906161','LP-4475551907253','LP-4478081906171','LP-4479401906171','LP-4479451906171','LP-4482401907192','LP-4490121906171','LP-4492511906171','LP-4500351906181','LP-4500351906182','LP-4502321906191','LP-4502341906181','LP-4502901906181','LP-4503711906181','LP-4503751906181','LP-4507291907112','LP-4507661906181','LP-4509661906181','LP-4512021906191','LP-4514961906212','LP-4515881906202','LP-4516961906191','LP-4517261906191','LP-4520671906191','LP-4520741906191','LP-4521201906191','LP-4522751906191','LP-4523161906191','LP-4526711907308','LP-4528181906201','LP-4535501906201','LP-4537751906211','LP-4539521906211','LP-4542491906211','LP-4543131906211','LP-4545151906211','LP-4545281906211','LP-4546831906211','LP-4547461906211','LP-4549021906221','LP-4550641907222','LP-4552941907262','LP-4554231906262','LP-4557611906221','LP-4557861906221','LP-4560231906221','LP-4566761906242','LP-4567231906231','LP-4567651906231','LP-4568091906231','LP-4568241907222','LP-4568821906231','LP-4569121906241','LP-4569621906241','LP-4571671906241','LP-4577201906241','LP-4579391906241','LP-4581241907012','LP-4582651906241','LP-4589321906251','LP-4592671906251','LP-4593651907022','LP-4597561906261','LP-4599891906261','LP-4602541906261','LP-4602671906261','LP-4606441906271','LP-4607371906271','LP-4607871906271','LP-4612331906271','LP-4613601907122','LP-4615181906271','LP-4617671906281','LP-4619131907032','LP-4619681906281','LP-4619801907152','LP-4619891906281','LP-4619991906281','LP-4621561906281','LP-4623141906281','LP-4625191906281','LP-4629411906291','LP-4630991906291','LP-4632521906291','LP-4633611906291','LP-4633971906291','LP-4634101906291','LP-4634811906291','LP-4640621907011','LP-4641331907203','LP-4641751907042','LP-4643091907011','LP-4644391907011','LP-4645161907011','LP-4647391907011','LP-4647971907012','LP-4649281907011','LP-4651161907011','LP-4652491907021','LP-4654601907032','LP-4656771907021','LP-4657071907021','LP-4658041907021','LP-4659131907021','LP-4660811907021','LP-4665331907031','LP-4667761907032','LP-4668881907031','LP-4669021908023','LP-4669531907031','LP-4670681907031','LP-46800019072515','LP-4680241907041','LP-4682611907041','LP-4684241907182','LP-4687031907041','LP-4687211907041','LP-4688691907051','LP-4691431907051','LP-4702801907061','LP-4704961907061','LP-4705561907061','LP-4706141907061','LP-4711311907223','LP-4712401907071','LP-47161319070811','LP-4718191907102','LP-4722601907081','LP-4723731907081','LP-4730431907091','LP-4731881907091','LP-4735941907091','LP-4740291907101','LP-4745381907101','LP-4745641907101','LP-4747431907101','LP-4747831907101','LP-4750881907111','LP-4759121907121','LP-4759761907121','LP-4762081907121','LP-4762341907121','LP-4762431907121','LP-4762501907121','LP-4765981907121','LP-4768861907131','LP-4770181907131','LP-4773631907131','LP-4773691907131','LP-4775701907131','LP-4777361907131','LP-4777481907131','LP-4779391907141','LP-4785441907151','LP-4788811907151','LP-4790241907151','LP-4793551907151','LP-4794121907161','LP-4798601907161','LP-4803141907161','LP-4812011907171','LP-4812761907171','LP-4813931907171','LP-4814481907171','LP-4816311907181','LP-4819701907181','LP-4830241907191','LP-4833041907191','LP-4834281907191','LP-4840651907212','LP-4841521907201','LP-4847281907211','LP-4850111907211','LP-4853271907262','LP-4860171907272','LP-4861501907272','LP-4862171908082','LP-4864091907231','LP-4869881907231','LP-4872591907231','LP-4874221907241','LP-4874641907241','LP-4874911907241','LP-4876621907241','LP-4880131907241','LP-4882061907241','LP-4882451907312','LP-4886731907251','LP-4889331907251','LP-4889671907251','LP-4891501907251','LP-4892961907251','LP-4903491908052','LP-4904091907261','LP-4914331907271','LP-4915641907271','LP-4916511908068','LP-4920171907291','LP-4922401908022','LP-4927001907291','LP-4927601907291','LP-4930621907291','LP-4930881907291','LP-4930901907291','LP-4931221907291','LP-4934721907301','LP-4934891907301','LP-4939801907301','LP-4945571907311','LP-4947801907311','LP-4947801907312','LP-4950701908011','LP-4953641908011','LP-4953911908011','LP-4954721908011','LP-4956041908011','LP-4957691908011','LP-4958111908011','LP-4958971908011','LP-4962661908062','LP-4964381908021','LP-4965341908021','LP-4966881908082','LP-4970981908021','LP-4973621908031','LP-4982341908041','LP-4984171908051','LP-4986581908052','LP-4989161908051','LP-4990251908051','LP-4990631908051','LP-4993031908051','LP-4994211908051','LP-4998411908061','LP-4999921908061','LP-5001961908061','LP-5005061908061','LP-5005541908061','LP-5009311908071','LP-5011581908071','LP-5012271908071','LP-5012291908071','LP-5012311908093','LP-5012321908071','LP-5012371908071','LP-5012381908071','LP-5012391908071','LP-5012401908071','LP-5012431908071','LP-5012441908071','LP-5012451908071','LP-5012611908071','LP-5021251908081','LP-5022331908081','LP-5029641908081','LP-5035941908091','LP-5037051908091','LP-5041611908101','LP-5043751908101','LP-5048101908111','LP-5048361908111','LP-5049481908121','LP-5049851908121','LP-890881907174');


      foreach ($bookings as   $booking) {
          
                         $url = base_url() . "employee/partner/do_partner_nrn_approval";
                         $async_data['remarks'] = 'Appproved By Sudhir';
                         $async_data['partner_id'] = 247130;
                         $async_data['booking_id'] =trim($booking);
                         $this->asynchronous_lib->do_background_process($url, $async_data);


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
