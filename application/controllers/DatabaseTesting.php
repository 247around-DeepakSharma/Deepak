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
       // $where =  array("status" => DEFECTIVE_PARTS_PENDING, 'defective_part_required' => 1, 'sf_challan_number IS NULL ' => NULL);

      //  $where =array()


       $where =  array("spare_parts_details.id IN (85882,87452,90144,91752,93614,95453,95891,98865,98936,100039,101899,106158,106323,106788,106789,106985,111056,111181,111704,112500,112564,112742,113111,113124,113717,114103,114986,116520,116937,116991,118196,118880,12132,12848,15007,17219,17490,19411,19970,20327,22890,23128,24366,24616,26520,27088,29653,29826,30967,31185,35336,39405,40452,49353,50303,50888,52575,59612,61959,63602,63925,63926,64213,65932,66098,66099,66665,68114,68336,68614,71638,71721,73191,73227,73260,73540,74724,76297,76713,78049,78483,78879,79801,79863,81687,81689,82499,82663,82705,83456,83457,83641,83647,84914,84973,85047,85791,86346,86882,86932,87336,88091,88223,88818,89558,89720,90165,90172,91011,91197,91503,92187,92282,93242,93538,93550,94945,95322,95482,95695,96279,96280,96530,96540,96923,97170,97274,97651,97668,97714,97718,97725,98703,98704,98785,99103,99135,99336,99360,99361,99362,99928,100144,100226,100405,100465,100914,102004,102744,102897,103803,104328,104506,104986,105305,105716,105717,106268,106696,106743,106744,107001,107223,107398,107474,107772,107805,107806,107917,107918,108208,109528,109619,109627,110165,110215,111659,112175,112528,113410,113411,114005,114564,116198,2384,2536,2611,2786,3301,3327,3766,3790,4006,4078,4169,4182,4226,4231,4476,4734,4803,5039,5337,5392,5573,5620,5643,5706,5711,5835,5966,6256,6411,6484,6506,6542,6582,6670,6848,6862,6959,7132,7284,7352,7360,7389,7558,7603,7692,7712,7972,7974,7976,8068,8110,8325,8344,8349,8446,8463,8577,8615,8616,8693,8708,8751,8760,8776,8911,8936,8940,9165,9243,9829,9898,10046,10047,10122,10217,10272,10275,10300,10377,10412,10490,10580,10581,10776,10792,10836,10851,11081,11214,11267,11292,11308,11316,11371,11384,11457,11469,11821,11949,11953,12051,12056,12221,12243,12284,12361,12504,12505,12526,12659,12737,12881,13104,13136,13416,13429,13487,13542,13598,13608,13636,13670,13696,13914,14003,14016,14108,14109,14130,14229,14247,14248,14296,14339,14344,14442,14443,14481,14501,14585,14611,14612,14633,14653,14730,15323,15367,15410,15520,15530,15624,15628,15630,15632,15702,15791,15901,15918,15989,16019,16020,16134,16178,16279,16443,16463,16507,16545,16579,16670,16704,16709,16863,17306,17327,17332,17343,17557,17570,17573,17649,17667,18031,18069,18157,18163,18191,18262,18264,18312,18576,18588,18666,18695,18721,18819,18920,18921,18988,19086,19091,19096,19186,19318,19383,19606,19761,19892,20055,20336,20489,20689,20873,20875,20969,21023,21306,21355,21527,21568,21745,21792,21842,21883,21921,22071,22072,22167,22220,22294,22298,22373,22404,22406,22581,22646,22755,22830,22832,22866,22869,22952,23050,23069,23076,23122,23176,23284,23335,23490,23613,23754,23845,23849,23855,23856,23981,23983,24014,24058,24131,24183,24184,24199,24238,24239,24250,24315,24349,24641,24659,24660,24737,24890,24937,24961,25004,25068,25078,25153,25188,25220,25275,25299,25315,25402,25461,25464,25471,25495,25524,25525,25611,25915,25933,25948,25988,26034,26114,26152,26245,26339,26444,26482,26506,26507,26618,26754,26782,26932,27027,27060,27236,27255,27268,27286,27402,27416,27426,27472,27473,27487,27488,27489,27490,27572,27748,27750,27786,27788,27790,27853,27910,27911,27962,28031,28064,28068,28229,28260,28466,28476,28510,28536,28537,28780,29077,29190,29216,29282,29320,29340,29365,29366,29408,29423,29439,29502,29656,29685,29754,29761,29774,29786,29787,29831,29858,29871,29958,29989,30080,30122,30128,30129,30130,30134,30157,30256,30264,30275,30286,30291,30297,30330,30380,30381,30382,30390,30411,30417,30458,30519,30520,30521,30538,30539,30540,30569,30604,30650,30691,30728,30749,30750,30757,30758,30759,30766,30802,30805,30829,30983,30996,31020,31045,31054,31066,31093,31095,31099,31109,31110,31161,31173,31180,31224,31228,31312,31313,31326,31345,31408,31477,31494,31558,31568,31578,31607,31624,31719,31810,31833,31840,31883,31920,31936,31945,32003,32028,32070,32078,32203,32210,32264,32313,32315,32379,32422,32476,32492,32516,32533,32633,32702,32788,32807,32861,32911,32962,33115,33125,33294,33476,33705,33710,33745,33882,34010,34069,34073,34514,34657,34699,34720,34733,34928,34953,35096,35153,35231,35234,35243,35253,35279,35395,35423,35466,36002,36011,36047,36051,36541,36575,36632,37134,37175,37245,37452,37559,37780,38010,38083,38181,38245,38620,38671,38728,38748,38789,38808,38887,38905,39031,39040,39046,39189,39206,39400,39401,39477,39526,39699,39741,39792,39896,40027,40071,40100,40153,40297,40498,40803,40810,40826,40869,40977,41071,41367,41417,41566,41683,41701,41738,41770,41830,41833,41836,41840,41842,41850,41907,41947,42067,42224,42276,42277,42278,42387,42423,42542,42544,42557,42593,42620,42775,42787,43009,43082,43119,43163,43371,43409,43524,43654,43655,43752,43753,43823,43985,44043,44195,44206,44269,44270,44271,44279,44476,44670,44731,44850,44877,44942,44991,45018,45101,45228,45354,45552,45666,45671,45694,46100,46132,46144,46154,46155,46370,46401,46436,46525,46559,46644,46876,47378,47393,47426,47446,47685,47722,47734,47767,48019,48095,48230,48286,48437,48476,48495,48534,48661,48731,48791,48865,48916,48919,48947,49143,49173,49264,49391,49429,49459,49474,49504,49556,49711,49728,49769,50037,50117,50191,50435,50503,50571,50572,50580,50591,50607,50678,50822,50870,50974,50989,51146,51513,51650,51660,51759,51762,51925,51926,52085,52091,52117,52130,52178,52347,52430,52532,52581,52705,52876,52895,52991,53026,53048,53116,53124,53243,53385,53493,53529,53534,53556,53557,53592,53870,53918,53919,54061,54085,54131,54137,54146,54352,54413,54441,54467,54633,54707,54727,54857,54906,54956,54957,54977,55034,55051,55159,55225,55375,55430,55442,55498,55513,55606,55710,55777,55793,55849,55980,56047,56118,56168,56221,56276,56346,56522,56580,56632,56693,56905,56989,57167,57239,57264,57296,57386,57430,57488,57491,57630,57660,57700,57742,57780,57781,57789,57799,57800,57801,57907,58039,58052,58070,58078,58188,58197,58355,58422,58542,58566,58599,58600,58674,58699,58816,58849,58870,58914,58932,58935,59122,59169,59194,59206,59242,59267,59291,59292,59348,59387,59388,59424,59467,59471,59477,59481,59565,59582,59620,59628,59715,59798,59863,59958,60039,60153,60299,60314,60447,60514,60557,60694,60699,60713,60766,60822,60828,60852,60860,60888,60962,61091,61095,61141,61249,61266,61279,61281,61282,61288,61291,61349,61354,61359,61373,61452,61494,61529,61544,61573,61706,61707,61773,61849,61879,62003,62104,62245,62246,62286,62309,62312,62478,62489,62782,62821,62842,63063,63064,63091,63170,63176,63217,63259,63271,63272,63274,63289,63291,63294,63297,63298,63299,63306,63422,63433,63443,63460,63512,63592,63654,63764,63842,63937,64068,64119,64175,64297,64439,64442,64456,64565,64612,64626,64671,64679,64680,64714,64757,64764,64766,64841,64913,65010,65061,65094,65144,65185,65186,65187,65241,65342,65387,65398,65431,65432,65448,65521,65648,65756,65774,65863,65952,65995,66001,66109,66117,66127,66143,66183,66184,66185,66192,66205,66236,66307,66508,66613,66623,66624,66626,66630,66652,66691,66699,66767,66769,66777,66803,66804,66809,66829,66830,66851,66872,66873,66874,67054,67059,67060,67136,67147,67168,67343,67344,67390,67529,67555,67556,67593,67594,67595,67600,67606,67739,67761,67766,67795,67827,67858,67875,67897,67951,67993,67998,68052,68061,68102,68123,68167,68176,68241,68242,68456,68460,68529,68652,68744,68950,68981,69017,69036,69041,69123,69221,69284,69321,69326,69346,69488,69504,69674,69709,69716,69734,69740,69762,69764,69813,69861,69862,69883,69886,69927,69994,70030,70069,70132,70286,70309,70312,70344,70406,70441,70445,70449,70478,70482,70504,70588,70598,70631,70651,70681,70682,70683,70699,70700,70735,70736,70738,70745,70800,70826,71017,71018,71118,71156,71174,71212,71221,71242,71300,71304,71429,71433,71464,71476,71486,71487,71488,71505,71516,71574,71578,71729,71757,71771,71797,71812,71894,71899,71915,71916,71920,71928,71963,71998,72196,72201,72221,72244,72249,72273,72312,72328,72363,72387,72411,72412,72420,72422,72480,72482,72490,72570,72750,72760,72770,72779,72780,72950,72965,72966,72970,72974,72990,73008,73059,73092,73164,73182,73192,73212,73282,73303,73319,73338,73350,73362,73394,73536,73537,73556,73567,73682,73685,73698,73706,73709,73720,73723,73771,73781,73908,73913,74086,74156,74167,74170,74223,74237,74257,74286,74325,74366,74367,74559,74631,74633,74650,74651,74655,74681,74722,74733,74734,74741,74747,74757,74758,74761,74779,74780,74820,74821,74897,74913,74915,75006,75049,75050,75051,75083,75115,75144,75153,75188,75243,75328,75330,75336,75343,75352,75361,75377,75490,75519,75666,75703,75781,75794,75796,75804,75845,75990,75991,76095,76114,76172,76214,76229,76254,76281,76361,76409,76429,76560,76596,76637,76639,76646,76647,76651,76653,76654,76675,76690,76738,76747,76752,76767,76804,76811,76894,76897,76929,76937,77006,77026,77046,77124,77125,77141,77182,77183,77236,77253,77287,77362,77429,77484,77510,77722,77733,77789,77804,77807,77808,77811,77813,77825,77851,77892,77979,78027,78040,78042,78059,78071,78124,78137,78153,78159,78183,78184,78198,78199,78210,78211,78212,78214,78215,78224,78225,78226,78339,78384,78399,78439,78490,78491,78493,78495,78510,78582,78591,78601,78604,78608,78684,78717,78801,78859,78907,78913,79039,79041,79155,79164,79208,79233,79242,79291,79309,79402,79428,79441,79442,79443,79467,79482,79491,79573,79593,79642,79692,79699,79704,79734,79743,79787,79802,79858,79883,79900,79930,79932,79934,79949,79979,80006,80061,80098,80108,80113,80192,80220,80322,80332,80389,80395,80399,80417,80447,80459,80635,80718,80732,80769,80783,80805,80859,80860,80869,80875,80902,80960,80963,81053,81061,81063,81064,81074,81075,81084,81091,81092,81096,81113,81139,81149,81169,81170,81177,81197,81218,81224,81225,81282,81331,81352,81356,81361,81425,81433,81445,81455,81671,81703,81726,81728,81834,81840,81966,82023,82024,82074,82119,82129,82170,82171,82190,82209,82269,82329,82347,82348,82349,82355,82441,82446,82516,82521,82526,82527,82586,82613,82629,82683,82794,82830,82866,82867,82868,82920,82921,83100,83111,83205,83231,83258,83335,83339,83352,83367,83368,83428,83429,83479,83606,83636,83664,83747,83777,83783,83817,83819,83842,83932,83933,83940,83976,83997,84068,84072,84130,84138,84149,84160,84172,84187,84191,84227,84260,84264,84265,84267,84270,84347,84399,84407,84409,84450,84494,84503,84504,84506,84535,84536,84550,84554,84606,84669,84796,84799,84807,84815,84830,84850,84856,84876,84877,84881,84890,84918,84949,85024,85147,85151,85153,85155,85186,85206,85303,85304,85350,85351,85352,85387,85456,85486,85502,85557,85654,85667,85697,85727,85790,85808,85809,85888,85920,85943,85953,85970,85971,85972,85981,86032,86071,86109,86117,86118,86146,86161,86241,86290,86317,86324,86337,86350,86442,86453,86486,86487,86494,86509,86516,86531,86592,86593,86594,86627,86691,86694,86695,86714,86785,86842,86931,86938,86972,86995,87021,87044,87046,87047,87067,87076,87081,87082,87152,87191,87207,87232,87272,87305,87306,87311,87337,87350,87397,87398,87401,87416,87426,87440,87441,87449,87497,87525,87529,87551,87555,87556,87580,87585,87593,87607,87643,87646,87647,87672,87699,87766,87783,87802,87825,87827,87842,87869,87871,87880,87881,87919,87922,87957,87958,87970,87981,88036,88096,88105,88146,88167,88174,88182,88185,88186,88192,88203,88255,88295,88296,88364,88432,88469,88535,88579,88626,88643,88681,88711,88713,88720,88721,88753,88756,88776,88777,88778,88782,88783,88836,88838,88857,88862,88863,88881,88938,88940,88952,88953,88973,88974,88998,89007,89010,89014,89015,89016,89075,89077,89126,89127,89139,89140,89256,89260,89261,89288,89296,89330,89347,89348,89351,89353,89368,89388,89392,89580,89581,89597,89611,89617,89625,89704,89719,89721,89744,89842,89843,89848,89876,89912,89918,89922,89939,89944,89948,89967,90035,90044,90057,90082,90206,90272,90273,90275,90283,90284,90303,90314,90315,90340,90354,90355,90376,90378,90379,90380,90439,90451,90475,90480,90494,90498,90499,90611,90612,90614,90649,90655,90663,90665,90680,90689,90690,90802,90803,90822,90853,90861,90880,90909,90911,90964,90992,91002,91007,91008,91009,91057,91077,91159,91167,91203,91206,91275,91291,91303,91376,91388,91440,91451,91464,91489,91490,91491,91506,91528,91580,91632,91654,91668,91669,91685,91696,91736,91758,91770,91780,91811,91858,91956,91973,91980,91996,92065,92068,92082,92097,92138,92157,92158,92160,92161,92162,92172,92256,92257,92258,92315,92316,92317,92325,92326,92327,92400,92410,92417,92422,92492,92554,92567,92583,92613,92662,92691,92699,92771,92827,92829,92833,92847,92884,92885,92886,92916,92974,92975,92984,93039,93146,93177,93178,93206,93214,93220,93221,93225,93229,93243,93290,93341,93368,93369,93371,93373,93374,93379,93383,93412,93413,93447,93448,93454,93460,93483,93484,93521,93602,93615,93629,93630,93657,93689,93736,93769,93780,93786,93792,93793,93823,93829,93835,93891,93927,93948,93949,94018,94025,94026,94066,94070,94071,94097,94185,94186,94222,94228,94238,94244,94245,94246,94255,94256,94257,94258,94262,94266,94275,94285,94288,94289,94296,94303,94304,94326,94345,94346,94358,94367,94371,94373,94375,94402,94404,94405,94481,94484,94545,94546,94572,94596,94597,94682,94686,94689,94705,94712,94713,94741,94745,94748,94777,94869,94870,94905,94922,94953,94997,94999,95018,95075,95136,95218,95233,95234,95308,95320,95337,95341,95342,95346,95386,95392,95402,95417,95452,95454,95552,95553,95554,95570,95609,95617,95653,95688,95689,95719,95727,95728,95735,95764,95766,95855,95856,95857,95872,95922,95932,95944,95951,95952,95960,96005,96009,96034,96077,96078,96083,96098,96146,96156,96157,96176,96178,96183,96187,96197,96206,96224,96233,96236,96242,96297,96301,96308,96315,96320,96346,96372,96373,96374,96392,96429,96431,96478,96494,96510,96512,96544,96571,96572,96576,96589,96606,96623,96624,96630,96655,96666,96671,96684,96685,96705,96719,96732,96751,96752,96767,96768,96778,96787,96812,96852,96866,96876,96884,96894,96898,96926,96943,96949,96955,96956,96979,96991,96993,96994,97012,97020,97021,97074,97093,97114,97115,97118,97119,97125,97149,97150,97179,97240,97308,97311,97344,97361,97362,97363,97376,97421,97432,97438,97440,97449,97468,97492,97525,97526,97527,97539,97551,97552,97571,97576,97624,97625,97638,97653,97654,97655,97656,97657,97670,97671,97676,97681,97745,97749,97869,97870,97882,97891,97977,97978,97983,98004,98005,98008,98020,98028,98048,98059,98060,98119,98126,98147,98162,98194,98218,98247,98262,98285,98297,98302,98317,98367,98377,98378,98407,98408,98416,98421,98455,98489,98490,98491,98505,98506,98539,98565,98566,98569,98573,98574,98579,98600,98602,98603,98605,98611,98698,98699,98722,98743,98748,98750,98767,98776,98816,98864,98867,98868,98893,98901,98905,98906,98915,98916,98922,98923,98927,98931,98943,98944,98945,98954,98958,98963,98966,98967,98968,98975,98980,98990,98998,98999,99025,99034,99035,99054,99060,99084,99092,99106,99112,99113,99115,99123,99125,99127,99153,99184,99205,99224,99269,99297,99314,99323,99335,99350,99386,99392,99400,99401,99431,99444,99450,99455,99462,99481,99494,99502,99520,99523,99624,99627,99638,99647,99667,99668,99682,99685,99697,99698,99702,99703,99705,99709,99710,99712,99729,99740,99749,99768,99772,99808,99811,99820,99823,99850,99857,99882,99948,99974,100012,100031,100040,100056,100068,100071,100096,100122,100127,100132,100147,100196,100197,100214,100216,100227,100246,100250,100254,100274,100297,100299,100325,100353,100354,100360,100376,100379,100383,100384,100391,100394,100416,100429,100430,100442,100448,100454,100455,100464,100467,100468,100474,100516,100539,100576,100633,100634,100635,100646,100680,100688,100713,100731,100736,100742,100750,100771,100772,100779,100805,100848,100864,100865,100878,100881,100891,100904,100912,100935,100961,100967,100970,100978,100981,100990,101020,101023,101025,101061,101070,101086,101103,101107,101117,101131,101152,101167,101192,101194,101206,101208,101211,101212,101213,101225,101228,101235,101265,101283,101290,101367,101397,101413,101444,101445,101447,101449,101457,101458,101470,101471,101474,101475,101477,101478,101499,101500,101508,101542,101543,101567,101571,101620,101621,101635,101651,101655,101659,101667,101703,101740,101772,101796,101830,101833,101834,101842,101845,101846,101854,101863,101896,101912,101922,101924,101937,101944,101949,101950,101952,101953,101971,101988,101989,101997,102006,102022,102044,102066,102071,102097,102138,102160,102161,102162,102164,102165,102172,102197,102211,102244,102247,102263,102283,102312,102317,102320,102339,102356,102358,102373,102386,102387,102454,102459,102461,102482,102496,102497,102501,102502,102569,102571,102572,102587,102593,102613,102615,102617,102703,102708,102715,102779,102783,102784,102824,102843,102887,102888,102899,102929,102930,102934,102942,102948,102949,102980,102992,103025,103028,103037,103045,103046,103047,103056,103102,103107,103135,103148,103167,103170,103203,103225,103234,103260,103265,103266,103281,103287,103305,103327,103360,103372,103462,103557,103566,103574,103588,103589,103590,103591,103618,103620,103712,103714,103790,103800,103861,103954,103969,103970,104011,104017,104051,104102,104146,104148,104169,104170,104171,104181,104182,104204,104207,104218,104219,104222,104228,104240,104280,104283,104293,104299,104313,104321,104332,104333,104336,104343,104354,104363,104364,104387,104407,104434,104466,104512,104523,104525,104596,104607,104608,104609,104643,104655,104666,104694,104712,104729,104749,104756,104785,104793,104800,104805,104806,104807,104821,104882,104914,104928,104931,104940,104987,105006,105008,105014,105015,105021,105022,105090,105091,105125,105132,105139,105140,105142,105160,105234,105235,105245,105255,105260,105261,105263,105270,105278,105280,105297,105304,105325,105332,105380,105381,105411,105431,105465,105500,105514,105516,105551,105560,105567,105568,105574,105584,105585,105626,105628,105639,105646,105665,105729,105734,105759,105763,105774,105793,105821,105822,105846,105904,105945,105958,105959,105983,105987,105990,106000,106004,106010,106058,106059,106082,106121,106138,106167,106175,106189,106213,106214,106216,106230,106245,106246,106276,106282,106283,106284,106303,106317,106330,106332,106376,106402,106415,106416,106424,106425,106426,106433,106445,106468,106504,106566,106614,106626,106661,106662,106672,106675,106678,106723,106724,106737,106769,106787,106797,106799,106800,106816,106834,106835,106836,106859,106875,106889,106894,106905,106926,106943,106958,106976,106982,106998,107003,107004,107015,107039,107044,107052,107054,107058,107077,107085,107092,107109,107112,107114,107136,107137,107138,107139,107140,107153,107155,107156,107210,107215,107216,107301,107323,107329,107330,107343,107344,107346,107349,107355,107433,107439,107441,107442,107479,107504,107505,107506,107507,107508,107509,107521,107522,107523,107527,107533,107539,107579,107580,107635,107639,107658,107683,107713,107719,107729,107751,107763,107811,107887,107893,107944,107955,107956,107991,108000,108012,108063,108069,108078,108081,108086,108097,108098,108105,108139,108143,108178,108206,108211,108212,108272,108276,108285,108288,108303,108323,108324,108325,108326,108329,108359,108376,108411,108426,108431,108432,108457,108461,108467,108475,108478,108479,108489,108490,108493,108504,108524,108550,108551,108565,108600,108620,108623,108625,108630,108659,108662,108670,108677,108688,108698,108699,108708,108710,108717,108724,108726,108727,108732,108737,108744,108776,108817,108818,108822,108885,108889,108895,108910,108913,108922,108925,108926,108930,108932,108938,109017,109033,109037,109038,109039,109051,109053,109062,109063,109068,109069,109080,109086,109087,109091,109092,109104,109105,109107,109119,109129,109131,109135,109136,109141,109150,109162,109164,109168,109169,109170,109171,109172,109177,109211,109214,109215,109236,109274,109275,109293,109294,109302,109303,109330,109360,109363,109368,109373,109398,109415,109441,109442,109461,109481,109482,109483,109492,109499,109502,109523,109535,109556,109560,109588,109592,109597,109600,109601,109602,109605,109621,109632,109644,109645,109651,109663,109667,109670,109671,109692,109710,109739,109740,109751,109753,109759,109767,109768,109780,109819,109825,109846,109847,109848,109852,109861,109864,109872,109886,109895,109902,109918,109943,109953,109967,109985,109997,109999,110000,110021,110026,110030,110032,110033,110036,110072,110077,110082,110083,110105,110115,110116,110119,110122,110148,110155,110169,110174,110178,110180,110237,110271,110276,110277,110278,110280,110281,110302,110303,110305,110322,110342,110350,110351,110352,110362,110363,110377,110381,110390,110411,110415,110421,110444,110468,110492,110498,110499,110518,110520,110533,110558,110572,110574,110578,110583,110584,110588,110592,110621,110625,110644,110658,110666,110772,110784,110816,110876,110931,110937,110954,110955,110957,110962,110963,110976,111017,111018,111024,111025,111053,111057,111059,111064,111065,111066,111072,111083,111088,111089,111090,111094,111096,111106,111114,111115,111116,111117,111118,111140,111147,111148,111152,111153,111159,111162,111174,111177,111178,111180,111183,111187,111216,111218,111225,111237,111238,111242,111246,111247,111254,111255,111257,111260,111269,111270,111273,111276,111286,111287,111288,111292,111310,111344,111391,111392,111406,111414,111426,111472,111519,111532,111538,111552,111566,111567,111568,111572,111581,111598,111604,111617,111620,111625,111626,111638,111658,111671,111677,111688,111706,111710,111712,111714,111715,111723,111740,111741,111742,111746,111747,111766,111767,111787,111790,111802,111818,111825,111870,111887,111894,111907,111908,111980,112016,112022,112027,112034,112059,112074,112075,112096,112097,112099,112101,112109,112189,112216,112229,112232,112249,112258,112270,112319,112338,112367,112370,112385,112387,112394,112399,112400,112409,112415,112419,112420,112430,112454,112467,112481,112487,112492,112493,112525,112526,112530,112537,112542,112545,112550,112584,112597,112598,112611,112614,112660,112665,112678,112692,112696,112704,112713,112720,112721,112722,112729,112731,112733,112734,112738,112739,112746,112747,112753,112756,112757,112763,112785,112786,112787,112788,112789,112790,112806,112807,112814,112815,112816,112817,112826,112827,112828,112832,112833,112834,112835,112839,112840,112849,112850,112858,112861,112862,112865,112867,112871,112872,112877,112890,112903,112910,112921,112926,112970,112979,113039,113052,113055,113066,113093,113110,113114,113143,113145,113151,113152,113154,113155,113162,113167,113174,113178,113180,113181,113182,113183,113184,113185,113186,113187,113201,113204,113208,113245,113247,113251,113256,113259,113261,113262,113273,113288,113289,113294,113308,113365,113407,113444,113453,113457,113465,113468,113469,113490,113494,113497,113505,113517,113528,113529,113540,113597,113598,113599,113625,113628,113633,113634,113663,113685,113688,113695,113719,113782,113803,113839,113840,113846,113847,113848,113862,113889,113892,113919,113926,113931,113934,113956,113959,113960,113976,113980,113983,113995,113996,114085,114091,114094,114114,114115,114130,114131,114132,114153,114161,114169,114180,114189,114191,114214,114244,114256,114281,114282,114287,114288,114289,114290,114309,114310,114311,114312,114337,114348,114351,114356,114378,114379,114391,114400,114435,114438,114461,114463,114499,114515,114516,114522,114532,114533,114536,114537,114546,114555,114592,114627,114643,114667,114668,114669,114675,114690,114703,114717,114726,114728,114733,114736,114739,114766,114767,114768,114770,114775,114776,114778,114780,114813,114817,114821,114837,114860,114861,114863,114866,114872,114886,114905,114908,114909,114911,114913,114914,114918,114941,114942,114943,114944,114964,114972,114973,114998,115000,115003,115014,115018,115019,115034,115035,115036,115060,115067,115102,115139,115210,115220,115242,115247,115251,115262,115267,115277,115318,115327,115328,115337,115339,115345,115346,115350,115351,115353,115373,115381,115392,115397,115398,115401,115409,115422,115445,115448,115451,115452,115453,115468,115470,115475,115487,115488,115495,115497,115502,115503,115504,115505,115531,115555,115576,115609,115637,115667,115682,115691,115707,115708,115725,115730,115748,115756,115763,115784,115787,115846,115847,115864,115865,115868,115875,115876,115886,115896,115897,115905,115920,115937,116004,116047,116069,116071,116072,116098,116106,116107,116119,116131,116154,116174,116180,116190,116208,116217,116227,116229,116258,116259,116262,116264,116272,116304,116305,116306,116307,116341,116360,116361,116366,116374,116376,116377,116396,116400,116406,116407,116408,116428,116430,116436,116481,116508,116533,116565,116567,116575,116576,116577,116588,116623,116628,116653,116661,116663,116664,116677,116683,116723,116735,116737,116746,116747,116748,116824,116827,116842,116847,116853,116854,116866,116887,116897,116908,116918,116920,116933,116955,116961,116966,116975,117000,117006,117025,117029,117033,117043,117053,117054,117063,117074,117077,117102,117104,117115,117116,117117,117118,117141,117146,117158,117159,117187,117188,117203,117206,117210,117213,117224,117239,117299,117300,117305,117313,117407,117409,117428,117445,117453,117462,117476,117482,117491,117498,117517,117527,117544,117545,117547,117548,117549,117552,117561,117562,117566,117567,117568,117569,117570,117585,117598,117607,117636,117637,117648,117649,117650,117663,117674,117701,117771,117778,117781,117810,117818,117832,117833,117834,117850,117857,117862,117866,117885,117918,117919,117920,118014,118015,118016,118025,118030,118031,118032,118051,118054,118055,118072,118073,118074,118083,118084,118101,118121,118127,118155,118160,118170,118178,118210,118215,118217,118221,118231,118268,118271,118272,118308,118316,118320,118324,118327,118333,118352,118405,118413,118415,118452,118464,118469,118489,118506,118509,118572,118596,118638,118649,118735,118736,118777,118783,118810,118832,118833,118836,118839,118849,118871,118920,118922,118923,118924,118925,118929,118982,118998,119036,119037,119041,119042,119051,119052,119085,119108,119116,119124,119161,119170,119172,119173,119200,119203,119215,119216,119224,119229,119230,119234,119247,119249,119265,119278,119320,119328,119329,119355,119407,119408,119458,119459,119460,119473,119474,119485,119507,119508,119511,119552,119556,119606,119607,119642,119652,119730,119764,119768,119815,119833,119897,119972,120071,120078,120111,120152,120270,120301,120302,120341,120355,120356,120368,120369,120386,120587,121979,121980,56169,99377,100605,102494,102973,103793,109903,112056,113005,113674,115987,116319)" => NULL);

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
