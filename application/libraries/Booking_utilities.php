<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

/**
 * Description of Booking_utilities
 *
 * @author anujaggarwal
 */
class Booking_utilities {

    var $My_CI;

    function __Construct() {
	$this->My_CI = & get_instance();

	$this->My_CI->load->library('PHPReport');
	$this->My_CI->load->library('email');
	$this->My_CI->load->library('s3');
	$this->My_CI->load->library('form_validation');
	$this->My_CI->load->library("session");
        $this->My_CI->load->library("miscelleneous");
        $this->My_CI->load->library("notify");
	$this->My_CI->load->helper('download');
	$this->My_CI->load->helper(array('form', 'url'));
	$this->My_CI->load->model('employee_model');
	$this->My_CI->load->model('booking_model');
	$this->My_CI->load->model('reporting_utils');
        $this->My_CI->load->model('booking_request_model');
        $this->My_CI->load->model('warranty_model');
        $this->My_CI->load->library('paytm_payment_lib');
    }
    
    
     public function mpdf_failure_backup_jobcard($booking_id) {
        log_message('info', __FUNCTION__ . " => Entering, Booking ID: " . $booking_id);
        $template = 'BookingJobCard_Template-v9.xlsx';
        //set absolute path to directory with template files
        $templateDir = FCPATH . "application/controllers/excel-templates/";
        //set config for report
        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );

        //load template
        if(ob_get_length() > 0) {
            ob_end_clean();
        }
        $R = new PHPReport($config);
        //log_message('info', "PHP report");
        $booking_details = $this->My_CI->booking_model->getbooking_history($booking_id, "join");
        if (!empty($booking_details)) {

            $qr = $this->get_qr_code_response($booking_details[0]['booking_id'], $booking_details[0]['amount_due'], 
            $booking_details[0]['primary_contact_phone_1'], $booking_details[0]['user_id'], 
            $booking_details[0]['booking_primary_contact_no'], $booking_details[0]['services'], $booking_details[0]['partner_id']);
            $unit_where = array('booking_id' => $booking_id, 'pay_to_sf' => '1', 'booking_status != "Cancelled" ' => NULL);
            
            $unit_details = $this->My_CI->booking_model->get_unit_details($unit_where);
            $meta = array();
            $meta['upcountry_charges'] = 0;
            
            if ($booking_details[0]['upcountry_paid_by_customer'] == 1) {
                $meta['upcountry_charges'] = $booking_details[0]['upcountry_distance'] * DEFAULT_UPCOUNTRY_RATE;
            }
            $meta['appliance_description'] = "";
            if(!empty($unit_details)){
                $meta['appliance_description'] = $unit_details[0]['appliance_description'];
            }
            $booking_unit_details = array();
            log_message('info', __FUNCTION__ . " => Entering, Booking ID: " . print_r($unit_details, true));
            foreach ($unit_details as $value) {
                $array = array();
                $array['appliance_category'] = $value['appliance_category'];
                $array['appliance_capacity'] = $value['appliance_capacity'];
                $array['appliance_brand'] = $value['appliance_brand'];
                $array['model_number'] = $value['model_number'];
                $array['price_tags'] = $value['price_tags'];
                $array['customer_net_payable'] = $value['customer_net_payable'];
                array_push($booking_unit_details, $array);
            }
            if($qr){
                $booking_details[0]['qr_message'] = "Get 5% Discount When You Scan QR Code & Pay Through Paytm App";
            } else {
                $booking_details[0]['qr_message'] = "";
            }
            $R->load(array(
                array(
                    'id' => 'booking',
                    //'repeat' => TRUE,
                    'data' => $booking_details[0],
                    //'minRows' => 2,
                    'format' => array(
                        'booking_date' => array('datetime' => 'd/M/Y'),
                        'amount_due' => array('number' => array('prefix' => 'Rs. ')),
                    )
                ),
                array(
                    'id' => 'unit',
                    'repeat' => TRUE,
                    'data' => $booking_unit_details,
                    //'minRows' => 2,
                    'format' => array(
                        //'create_date' => array('datetime' => 'd/M/Y'),
                        'total_price' => array('number' => array('prefix' => 'Rs. ')),
                    )
                ),
                array(
                    'id' => 'meta',
                    'repeat' => false,
                    'data' => $meta,
                ),
                    )
            );

            //Get populated XLS with data
            if ($booking_details[0]['current_status'] == "Rescheduled") {
                $output_file_suffix = "-RESC-" . $booking_details[0]['booking_date'];
            } else {
                $output_file_suffix = "";
            }
            
            $output_file_dir = TMP_FOLDER;
            $output_file = "BookingJobCard-" . $booking_id . $output_file_suffix;
            $output_file_excel = $output_file_dir . $output_file . ".xlsx";
            if (file_exists($output_file_excel)) {
                $res1 = 0;
                system(" chmod 777 " . $output_file_excel, $res1);
                unlink($output_file_excel);
            }
            if(!empty($qr)){
                log_message("info", __METHOD__. " QR is not empty");
                $R->render('excel', $output_file_excel, "F4",$qr);
            } else {
                log_message("info", __METHOD__. " QR is empty");
                $R->render('excel', $output_file_excel);
            }
            $res1 = 0;
            system(" chmod 777 " . $output_file_excel, $res1);
            $output_file_pdf = $output_file . ".pdf";
            $bucket = BITBUCKET_DIRECTORY;
            $json_result = $this->My_CI->miscelleneous->convert_excel_to_pdf($output_file_excel,$booking_id, "jobcards-pdf");
            $pdf_response = json_decode($json_result,TRUE);

            //Create html for job card
           //$html = $this->My_CI->load->view('employee/jobcard_html', array("booking_details"=>$booking_details,"booking_unit_details"=>$booking_unit_details,'meta'=>$meta,'qr'=>$qr),true);        
           //convert html into pdf
           //$json_result = $this->My_CI->miscelleneous->convert_html_to_pdf($html,$booking_details[0]['booking_id'],$output_file_pdf,"jobcards-pdf");
           //$pdf_response = json_decode($json_result,TRUE);
            if($pdf_response['response'] === 'Success'){ 
                //Update JOb Card Booking
                $this->My_CI->booking_model->update_booking($booking_id,  array('booking_jobcard_filename'=>$output_file_pdf));
                $directory_xls = "jobcards-excel/" . $output_file . ".xlsx";
                $this->My_CI->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            } else {
                
                $directory_pdf = "jobcards-pdf/" . $output_file . ".xlsx";
                $this->My_CI->s3->putObjectFile($output_file_excel, $bucket, $directory_pdf, S3::ACL_PUBLIC_READ);
                //Update JOb Card Booking
                $this->My_CI->booking_model->update_booking($booking_id,  array('booking_jobcard_filename'=>$output_file .".xlsx"));
                log_message('info', __FUNCTION__ . ' Error in Booking PDF not created '. $booking_id);
            }
            exec("rm -rf " . escapeshellarg($output_file_excel));
        }
        log_message('info', __FUNCTION__ . " => Exiting, Booking ID: " . $booking_id);
        unlink($output_file_excel);
    }

    public function lib_prepare_job_card_using_booking_id($booking_id) { 
        log_message('info', __FUNCTION__ . " => Entering, Booking ID: " . $booking_id);
        $booking_details = $this->My_CI->booking_model->getbooking_history($booking_id, "join");
        $booking_symptom = $this->My_CI->booking_model->getBookingSymptom($booking_id);
        if (!empty($booking_details)) {
            $saas_flag = $this->check_feature_enable_or_not(PARTNER_ON_SAAS);
            if($saas_flag){
               $qr = false; 
            }
            else{
                $qr = $this->get_qr_code_response($booking_details[0]['booking_id'], $booking_details[0]['amount_due'], 
                $booking_details[0]['primary_contact_phone_1'], $booking_details[0]['user_id'], 
                $booking_details[0]['booking_primary_contact_no'], $booking_details[0]['services'], $booking_details[0]['partner_id']);
            }
            $unit_where = array('booking_id' => $booking_id, 'pay_to_sf' => '1', 'booking_status != "Cancelled" ' => NULL);
            $unit_details = $this->My_CI->booking_model->get_unit_details($unit_where);
            $meta = array();
            $partner_on_saas = $this->check_feature_enable_or_not(PARTNER_ON_SAAS);
            $main_partner = $this->My_CI->partner_model->get_main_partner_invoice_detail($partner_on_saas);
            $meta['main_company_logo'] = $main_partner['main_company_logo'];
            $meta['main_company_public_name'] = $main_partner['main_company_public_name'];
            $meta['main_company_description'] = $main_partner['main_company_description'];
            $meta['main_company_name'] = $main_partner['main_company_name'];
            
            $meta['upcountry_charges'] = 0;
            if ($booking_details[0]['upcountry_paid_by_customer'] == 1) {
                if($booking_details[0]['flat_upcountry']  == 1){
                     $meta['upcountry_charges'] = $booking_details[0]['upcountry_to_be_paid_by_customer'];
                } else {
                    $meta['upcountry_charges'] = $booking_details[0]['upcountry_distance'] * DEFAULT_UPCOUNTRY_RATE;
                }
                
            }
            $meta['appliance_description'] = "";
            if(!empty($unit_details)){
                $meta['appliance_description'] = $unit_details[0]['appliance_description'];
            }
            $booking_unit_details = array();
            log_message('info', __FUNCTION__ . " => Entering, Booking ID: " . print_r($unit_details, true));
            foreach ($unit_details as $value) {
                $array = array();
                $array['appliance_category'] = $value['appliance_category'];
                $array['appliance_capacity'] = $value['appliance_capacity'];
                $array['appliance_brand'] = $value['appliance_brand'];
                $array['model_number'] = $value['model_number'];
                $array['price_tags'] = $value['price_tags'];
                $array['customer_net_payable'] = $value['customer_net_payable'];
                $array['purchase_date'] = $value['purchase_date'];
                array_push($booking_unit_details, $array);
            }
            if($qr){
                $booking_details[0]['qr_message'] = "Scan QR To Pay Through Paytm";
            } else {
                $booking_details[0]['qr_message'] = "";
            }
            //Get populated XLS with data
            if ($booking_details[0]['current_status'] == "Rescheduled") {
                $output_file_suffix = "-RESC-" . $booking_details[0]['booking_date'];
            } else {
                $output_file_suffix = "";
            }
            $output_file = "BookingJobCard-" . $booking_id . $output_file_suffix;
            $output_file_pdf = $output_file . ".pdf";
            $parant_booking_serial_number = NULL;
            if($booking_details[0]['request_type'] == REPEAT_BOOKING_TAG){
                $tempArray = $booking_details['parant_booking_serial_number'] = $this->My_CI->booking_model->get_parent_booking_serial_number($booking_id);
                if($tempArray){
                    $parant_booking_serial_number = $tempArray[0]['parent_sn'];
                }
            }
            $booking_details['parant_booking_serial_number'] = $parant_booking_serial_number;
            $symptom_id = "";
            if(count($booking_symptom)>0) {
                $symptom_id = ((!is_null($booking_symptom[0]['symptom_id_booking_completion_time'])) ? $booking_symptom[0]['symptom_id_booking_completion_time'] : $booking_symptom[0]['symptom_id_booking_creation_time']);
            }
            if($symptom_id !== ""){
                 $symptom1 = $this->My_CI->booking_request_model->get_booking_request_symptom('symptom', array('symptom.id' => $symptom_id));
                 if(!empty($symptom1)){
                     $symptom =  $symptom1[0]['symptom'];
                 } else {
                     $symptom =  "";
                 }
        
            } else {
                $symptom =  "";
            }
            //Create html for job card
           $html = $this->My_CI->load->view('employee/jobcard_html', array("booking_details"=>$booking_details,"booking_unit_details"=>$booking_unit_details,'meta'=>$meta,'qr'=>$qr, 'symptom' => $symptom),true);     
            //convert html into pdf
           $json_result = $this->My_CI->miscelleneous->convert_html_to_pdf($html,$booking_details[0]['booking_id'],$output_file_pdf,"jobcards-pdf");
            $pdf_response = json_decode($json_result,TRUE);
            
            if($pdf_response['response'] === 'Success'){ 
                //Update Job Card Booking
                $this->My_CI->booking_model->update_booking($booking_id,  array('booking_jobcard_filename'=>$output_file_pdf));
                return true;
            } else {
                log_message('info', __FUNCTION__ . ' Error in Booking PDF not created '. $booking_id);
                return false;
            }
        }
        log_message('info', __FUNCTION__ . " => Exiting, Booking ID: " . $booking_id);
    }
    function send_qr_code_sms($booking_id, $pocNumber, $user_id, $userPhone, $services,$regenrate_flag=0, $partner_id = false){
        $userDownload = $this->My_CI->paytm_payment_lib->generate_qr_code($booking_id, QR_CHANNEL_SMS, 0, $pocNumber,$regenrate_flag);
            log_message("info", __METHOD__. " Booking id ". $booking_id. " User QR Response ".print_r($userDownload, true));
            $user = json_decode($userDownload, TRUE);
            if($user['status'] == SUCCESS_STATUS){
                $tinyUrl = $this->My_CI->miscelleneous->getShortUrl(S3_WEBSITE_URL.$user['qr_url']);
                if($tinyUrl){
                    $sms['type'] = "user";
                    $sms['type_id'] = $user_id;
                    $sms['tag'] = "customer_qr_download";   
                    $sms['smsData']['services'] = $services;
                    $sms['smsData']['url'] = $tinyUrl;
                    if($partner_id){
                        if($partner_id == VIDEOCON_ID){
                            $sms['smsData']['cc_number'] = "0120-4500600";
                        }
                        else{
                           $sms['smsData']['cc_number'] = _247AROUND_CALLCENTER_NUMBER; 
                        }
                    }
                    else{
                        $sms['smsData']['cc_number'] = _247AROUND_CALLCENTER_NUMBER; 
                    }
                    $sms['phone_no'] = $userPhone;
                    $sms['booking_id'] = $booking_id;
                    $this->My_CI->notify->send_sms_msg91($sms);
                    return true;
                } else {
                    log_message("info", __METHOD__. " Booking id ". $booking_id. " Tiny url Not generated");
                }
            }
            return false;
    }
    /**
     * @desc This function is  used to genearte QR code for JOB CARD
     * @param String $booking_id
     * @param String $amount_due
     * @param String $pocNumber
     * @return boolean
     */
function get_qr_code_response($booking_id, $amount_due, $pocNumber, $user_id, $userPhone, $services, $partner_id){
        log_message("info", __METHOD__. " Booking id ". $booking_id. " Due ".$amount_due);
        $response = $this->My_CI->paytm_payment_lib->generate_qr_code($booking_id, QR_CHANNEL_JOB_CARD, 0, $pocNumber);
        if($amount_due > 0){
            $this->send_qr_code_sms($booking_id, $pocNumber, $user_id, $userPhone, $services, 0, $partner_id);
        }
        log_message("info", __METHOD__. " Booking id ". $booking_id. " Job QR Response ".print_r($response, true));
        $result = json_decode($response, TRUE);

        if($result['status'] == SUCCESS_STATUS){
            $qrImage = $result['qr_image'];
            if(file_exists(TMP_FOLDER.$qrImage)){
                
                return TMP_FOLDER.$qrImage;
                
            } else {
                
                if(copy(S3_WEBSITE_URL.$result['qr_url'], TMP_FOLDER.$qrImage)){
                    return   TMP_FOLDER.$qrImage;
                } else {
                    log_message("info", __METHOD__. " QR Download Failed". print_r($result, true));
                    return false;
                }
            }
        } else {
            log_message("info", __METHOD__. " QR Failed ". print_r($result, true));
            return false;
        }
    }

    //This function sends email/sms to the assigned vendor
    function lib_send_mail_to_vendor($booking_id, $additional_note) {
        log_message('info', __FUNCTION__ . " => Entering, Booking Id: ". $booking_id);

        $getbooking = $this->My_CI->booking_model->getbooking_history($booking_id,"join");

        if (!empty($getbooking)) {
            $date1 = date('d-m-Y', strtotime('now'));
            $date2 = $getbooking[0]['booking_date'];
            $datetime1 = date_create($date1);
            $datetime2 = date_create($date2);

            $interval = date_diff($datetime1, $datetime2);

            $datediff = $interval->format("%a") / (60 * 60 * 24);

            $month = date("m", strtotime($getbooking[0]['booking_date']));
            $dd = date("d", strtotime($getbooking[0]['booking_date']));
            $months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
            $mm = $months[$month - 1];

            if ($datediff == 0) {
                $bookingdate = "Today";
            } elseif ($datediff == 1) {
                $bookingdate = "Tomorrow";
            } else {
                $bookingdate = $dd . " " . $mm;
            }
            $request_type=$getbooking[0]['request_type'];
            $search_string='(Service Center Visit)';
            //if request type is service center visit then customer address will not send in sms body
            if (strpos($request_type,$search_string) !== false)
            {
               $smsBody = "Booking - " . substr($getbooking[0]['name'], 0, 20) . ", " . $getbooking[0]['booking_primary_contact_no'] 
                    . ", " . $getbooking[0]['services'] . ", " . $bookingdate ."/" 
                    . $getbooking[0]['booking_timeslot']
                    . ", ". $getbooking[0]['booking_pincode'] . ". 247around"; 
            }
            else
            {
            $smsBody = "Booking - " . substr($getbooking[0]['name'], 0, 20) . ", " . $getbooking[0]['booking_primary_contact_no'] 
                    . ", " . $getbooking[0]['services'] . ", " . $bookingdate ."/" 
                    . $getbooking[0]['booking_timeslot'] .  ", " . substr($getbooking[0]['booking_address'], 0, 60)
                    . ", ". $getbooking[0]['booking_pincode'] . ". 247around";
            }
            //Send SMS to vendor
            
            $sms = array();
            $sms['status'] = "";
            $sms['tag']="booking_details_to_sf";
            $sms['phone_no'] = $getbooking[0]['primary_contact_phone_1'];
            $sms['booking_id'] = $getbooking[0]['booking_id'];
            $sms['type_id'] = $getbooking[0]['user_id'];
            $sms['type'] = "vendor";
            $sms['smsData']['booking_id'] = substr($getbooking[0]['name'], 0, 20);
            $sms['smsData']['primary_contact'] = $getbooking[0]['booking_primary_contact_no'];
            $sms['smsData']['service'] = $getbooking[0]['services'];
            $sms['smsData']['bookingdate'] = $bookingdate;
            $sms['smsData']['booking_timeslot'] = $getbooking[0]['booking_timeslot'];
            $sms['smsData']['booking_address'] = substr($getbooking[0]['booking_address'], 0, 60);
            $sms['smsData']['booking_pincode'] = $getbooking[0]['booking_pincode'];
            $this->My_CI->notify->send_sms_msg91($sms);
        } else {
            echo "Booking does not exist.";
        }
        
        log_message('info', __FUNCTION__ . " => Exiting, Booking Id: ". $booking_id);
    }
    
     /*
     * @desc: This function is used to create acc to Service center, state and city
     * 
    * params: String sf_list
    * return :void
    *
    */

   function get_booking_report_by_service_center_data($sf_list, $interval_in_days, $sf_closed_date = NULL) {

       $data = $this->My_CI->reporting_utils->get_booking_by_service_center($sf_list, $interval_in_days);
       
       foreach ($data['service_center_id'] as $key => $val) {
           
           //Setting State and City value
           if (isset($data['data'][$val]['yesterday_booked']['state'])) {
               $state = $data['data'][$val]['yesterday_booked']['state'];
               $city = $data['data'][$val]['yesterday_booked']['city'];
               $service_center_name = $data['data'][$val]['yesterday_booked']['service_center_name'];
               $service_center_id = $data['data'][$val]['yesterday_booked']['service_center_id'];
               $active = $data['data'][$val]['yesterday_booked']['active'];
               $temporary_on_off = $data['data'][$val]['yesterday_booked']['temporary_on_off'];
           }
           if (isset($data['data'][$val]['yesterday_completed']['state'])) {
               $state = $data['data'][$val]['yesterday_completed']['state'];
               $city = $data['data'][$val]['yesterday_completed']['city'];
               $service_center_name = $data['data'][$val]['yesterday_completed']['service_center_name'];
               $service_center_id = $data['data'][$val]['yesterday_completed']['service_center_id'];
               $active = $data['data'][$val]['yesterday_completed']['active'];
               $temporary_on_off = $data['data'][$val]['yesterday_completed']['temporary_on_off'];
           }
           if (isset($data['data'][$val]['yesterday_cancelled']['state'])) {
               $state = $data['data'][$val]['yesterday_cancelled']['state'];
               $city = $data['data'][$val]['yesterday_cancelled']['city'];
               $service_center_name = $data['data'][$val]['yesterday_cancelled']['service_center_name'];
               $service_center_id = $data['data'][$val]['yesterday_cancelled']['service_center_id'];
               $active = $data['data'][$val]['yesterday_cancelled']['active'];
               $temporary_on_off = $data['data'][$val]['yesterday_cancelled']['temporary_on_off'];
           }
           if (isset($data['data'][$val]['month_completed']['state'])) {
               $state = $data['data'][$val]['month_completed']['state'];
               $city = $data['data'][$val]['month_completed']['city'];
               $service_center_name = $data['data'][$val]['month_completed']['service_center_name'];
               $service_center_id = $data['data'][$val]['month_completed']['service_center_id'];
               $active = $data['data'][$val]['month_completed']['active'];
               $temporary_on_off = $data['data'][$val]['month_completed']['temporary_on_off'];
           }
           if (isset($data['data'][$val]['month_cancelled']['state'])) {
               $state = $data['data'][$val]['month_cancelled']['state'];
               $city = $data['data'][$val]['month_cancelled']['city'];
               $service_center_name = $data['data'][$val]['month_cancelled']['service_center_name'];
               $service_center_id = $data['data'][$val]['month_cancelled']['service_center_id'];
               $active = $data['data'][$val]['month_cancelled']['active'];
               $temporary_on_off = $data['data'][$val]['month_cancelled']['temporary_on_off'];
           }

           if (isset($data['data'][$val]['last_2_day']['state'])) {
               $state = $data['data'][$val]['last_2_day']['state'];
               $city = $data['data'][$val]['last_2_day']['city'];
               $service_center_name = $data['data'][$val]['last_2_day']['service_center_name'];
               $service_center_id = $data['data'][$val]['last_2_day']['service_center_id'];
               $active = $data['data'][$val]['last_2_day']['active'];
               $temporary_on_off = $data['data'][$val]['last_2_day']['temporary_on_off'];
           }
           
           if (isset($data['data'][$val]['last_3_day']['state'])) {
               $state = $data['data'][$val]['last_3_day']['state'];
               $city = $data['data'][$val]['last_3_day']['city'];
               $service_center_name = $data['data'][$val]['last_3_day']['service_center_name'];
               $service_center_id = $data['data'][$val]['last_3_day']['service_center_id'];
               $active = $data['data'][$val]['last_3_day']['active'];
               $temporary_on_off = $data['data'][$val]['last_3_day']['temporary_on_off'];
           }
           if (isset($data['data'][$val]['greater_than_5_days']['state'])) {
               $state = $data['data'][$val]['greater_than_5_days']['state'];
               $city = $data['data'][$val]['greater_than_5_days']['city'];
               $service_center_name = $data['data'][$val]['greater_than_5_days']['service_center_name'];
               $service_center_id = $data['data'][$val]['greater_than_5_days']['service_center_id'];
               $active = $data['data'][$val]['greater_than_5_days']['active'];
               $temporary_on_off = $data['data'][$val]['greater_than_5_days']['temporary_on_off'];
           }

           $state_final[] = $state;
           $way_final['state'] = $state;
           $way_final['city'] = $city;
           $way_final['active'] = $active;
           $way_final['temporary_on_off'] = $temporary_on_off;
           $way_final['service_center_name'] = $service_center_name;
           $way_final['service_center_id'] = $service_center_id;
           $way_final['yesterday_booked'] = (isset($data['data'][$val]['yesterday_booked']['booked']) ? $data['data'][$val]['yesterday_booked']['booked'] : '0');
           $way_final['yesterday_completed'] = (isset($data['data'][$val]['yesterday_completed']['completed']) ? $data['data'][$val]['yesterday_completed']['completed'] : '0');
           $way_final['yesterday_cancelled'] = (isset($data['data'][$val]['yesterday_cancelled']['cancelled']) ? $data['data'][$val]['yesterday_cancelled']['cancelled'] : '0');
           $way_final['month_completed'] = (isset($data['data'][$val]['month_completed']['completed']) ? $data['data'][$val]['month_completed']['completed'] : '0');
           $way_final['month_cancelled'] = (isset($data['data'][$val]['month_cancelled']['cancelled']) ? $data['data'][$val]['month_cancelled']['cancelled'] : '0');
           $way_final['last_2_day'] = (isset($data['data'][$val]['last_2_day']['booked']) ? $data['data'][$val]['last_2_day']['booked'] : '0');
           $way_final['last_3_day'] = (isset($data['data'][$val]['last_3_day']['booked']) ? $data['data'][$val]['last_3_day']['booked'] : '0');
           $way_final['greater_than_5_days'] = (isset($data['data'][$val]['greater_than_5_days']['booked']) ? $data['data'][$val]['greater_than_5_days']['booked'] : '0');

           $final_way[] = $way_final;
       }
       return array("final_way"=>$final_way,"state_final"=>$state_final);
}

   function booking_report_by_service_center($sf_list,$cron_flag, $interval_in_days = 1, $sf_closed_date = NULL) {
       $bookingReportData = $this->get_booking_report_by_service_center_data($sf_list, $interval_in_days, $sf_closed_date);
       //Getting States and City List
       foreach($bookingReportData['final_way'] as $value){
           $state_array[] = $value['state'];
           $city_array[] = $value['city'];
           $sf_array[] = $value['service_center_name'];
       }
       $state_array = array_unique($state_array);
       $city_array = array_unique($city_array);
       $sf_array = array_unique($sf_array);
       
       //Making State Option
       $state_option = "";
       sort($state_array);
       foreach($state_array as $value){
            $state_option .= "<option value='".$value."'>".$value.'</option>';
       }
       //Making City Option
       $city_option = "";
       sort($city_array);
       foreach($city_array as $value){
            $city_option .= "<option value='".$value."'>".$value.'</option>';
       }
       
       //Making SF Name Option
       $sf_option = "";
       sort($sf_array);
       foreach($sf_array as $value){
            $sf_option .= "<option value='".$value."'>".$value.'</option>';
       }
       
       //Getting RM List for Dropdown
       $rm_details = $this->My_CI->employee_model->get_rm_details();
       $rm_option = "";
       foreach($rm_details as $value){
           $rm_option .= "<option value='".$value['full_name']."'>".$value['full_name'].'</option>';
       }
       
       //Add Bootstrap CSS for CRON calls
       $css = '';
       if($cron_flag == 1){
           $css = '<link href="http://localhost/247around-dev/css/bootstrap.min.css" rel="stylesheet">';
       }
       
       //Generating HTML for the email
       $html = '
                   <html xmlns="http://www.w3.org/1999/xhtml">
                     <head>
                       <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                     </head><body>'.$css.'
                     <div style="margin-top: 30px;font-family:Helvetica;" class="container-fluid table-responsive">
                         <table id="count_table" style="margin-bottom: 20px;border: 1px solid #ddd; border-collapse: collapse;" class="js-dynamitable">
                           <thead>
                             <tr style="padding: 8px;line-height: 1.42857143;vertical-align: top; border-top: 1px solid #ddd">
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:9%">Regional Manager</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:10%">State</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:9%">City</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE">Name</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE">Booked<p><span class="js-sorter-desc  glyphicon glyphicon-chevron-down pull-right"></span> <span class="js-sorter-asc     glyphicon glyphicon-chevron-up pull-right"></span></th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE">Completed<p><span class="js-sorter-desc     glyphicon glyphicon-chevron-down pull-right"></span> <span class="js-sorter-asc     glyphicon glyphicon-chevron-up pull-right"></span></th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE">Cancelled<p><span class="js-sorter-desc     glyphicon glyphicon-chevron-down pull-right"></span> <span class="js-sorter-asc     glyphicon glyphicon-chevron-up pull-right"></span></th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE">' . date('M') . ' Booking Completed <p><span class="js-sorter-desc     glyphicon glyphicon-chevron-down pull-right"></span> <span class="js-sorter-asc     glyphicon glyphicon-chevron-up pull-right"></span></th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE">' . date('M') . ' Booking Cancelled <p><span class="js-sorter-desc     glyphicon glyphicon-chevron-down pull-right"></span> <span class="js-sorter-asc     glyphicon glyphicon-chevron-up pull-right"></span></th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE">0-2 Days <p><span class="js-sorter-desc     glyphicon glyphicon-chevron-down pull-right"></span> <span class="js-sorter-asc     glyphicon glyphicon-chevron-up pull-right"></span></th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE">3-5 Days <p><span class="js-sorter-desc     glyphicon glyphicon-chevron-down pull-right"></span> <span class="js-sorter-asc     glyphicon glyphicon-chevron-up pull-right"></span></th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE"> > 5 Days <p><span class="js-sorter-desc     glyphicon glyphicon-chevron-down pull-right"></span> <span class="js-sorter-asc     glyphicon glyphicon-chevron-up pull-right"></span></th>

                             </tr>
                             <tr><td>&nbsp;</td></tr>
                             <tr>
                                <th>
                                <select class="js-filter  form-control">
                                    <option value="">Filter RM</option>'.$rm_option.'
                                </select>

                                </th>
                                <th>
                                <select class="js-filter  form-control">
                                    <option value="">Filter State</option>'.$state_option.'
                                </select>
                                </th>
                                <th>
                                <select class="js-filter  form-control">
                                    <option value="">Filter City</option>'.$city_option.'
                                </select>
                                </th>
                                <th>
                                <select class="js-filter  form-control">
                                    <option value="">Filter Name</option>'.$sf_option.'
                                </select>
                                </th>
                                <th><input class="js-filter  form-control" type="text" value=""></th>
                                <th><input class="js-filter  form-control" type="text" value=""></th>
                                <th><input class="js-filter  form-control" type="text" value=""></th>
                                <th><input class="js-filter  form-control" type="text" value=""></th>
                                <th><input class="js-filter  form-control" type="text" value=""></th>
                                <th><input class="js-filter  form-control" type="text" value=""></th>
                                <th><input class="js-filter  form-control" type="text" value=""></th>
                                <th><input class="js-filter  form-control" type="text" value=""></th>
                              </tr>
                              <tr><td>&nbsp;</td></tr>
                           </thead>
                           <tbody >';
       
       $greater_than_5_days = 0;
       $overall_greater_than_5_days = 0;
       $yesterday_booked = 0;
       $overall_yesterday_booked = 0;
       $yesterday_completed = 0;
       $overall_yesterday_completed = 0;
       $yesterday_cancelled = 0;
       $overall_yesterday_cancelled = 0;
       $month_completed = 0;
       $overall_month_completed = 0;
       $month_cancelled = 0;
       $overall_month_cancelled = 0;
       $last_2_day = 0;
       $overall_last_2_day = 0;
       $last_3_day = 0;
       $overall_last_3_day = 0;
       $state_final = array_unique($bookingReportData['state_final']);
       foreach ($state_final as $val) {

           foreach ($bookingReportData['final_way'] as $key => $value) {
               
               //Getting  RM Details
                $employee_relation = $this->My_CI->vendor_model->get_rm_sf_relation_by_sf_id($value['service_center_id']);
                if (!empty($employee_relation)) {
                    $rm = $this->My_CI->employee_model->getemployeefromid($employee_relation[0]['agent_id'])[0]['full_name'];
                } else {
                    $rm = "";
                }

                $style = '';
               if($value['active'] == 0 ){
                   $style = "background:#f25788";
               }else if(is_null($value['active'])){
                   $style = "background:#f4f44b";
               }else if($value['temporary_on_off'] == 0){
                   $style = "background:#7986CB";
               }
               
               if ($value['state'] == $val) {
                       $yesterday_booked = 0;
                           $yesterday_completed = 0;
                           $yesterday_cancelled = 0;
                           $month_completed = 0;
                           $month_cancelled = 0;
                           $last_2_day = 0;
                           $last_3_day = 0;
                           $greater_than_5_days = 0;

                   $html.="<tr style='".$style."'>" .
                           "<td width='8%' style='text-align: center;border: 1px solid #001D48;padding:5px;font-size:80%' class='text-right'>" . $rm .
                           "<td width='10%' style='text-align: center;border: 1px solid #001D48;padding:5px;font-size:80%' class='text-right'>" . $value['state'] .
                           "<td width='11%' style='text-align: center;border: 1px solid #001D48;padding:5px;font-size:80%;' class='text-right'>" . $value['city'] .
                           "</td><td width='15%' style='text-align: center;border: 1px solid #001D48;font-size:80%;padding:5px;' class='text-right'>" . $value['service_center_name'] .
                           " </td><td style='text-align: center;border: 1px solid #001D48;background:#E5E0D1;padding:5px;font-size:80%' class='text-right yesterday_booked'>" . $value['yesterday_booked'] .
                           " </td><td style='text-align: center;border: 1px solid #001D48;background:#E5E0D1;padding:5px;font-size:80%' class='text-right yesterday_completed'>" . $value['yesterday_completed'] .
                           " </td><td style='text-align: center;border: 1px solid #001D48;background:#E5E0D1;padding:5px;font-size:80%' class='text-right yesterday_cancelled'>" . $value['yesterday_cancelled'] .
                           " </td><td style='text-align: center;border: 1px solid #001D48;background:#E5E0D1;padding:5px;font-size:80%' class='text-right month_completed'>" . $value['month_completed'] .
                           " </td><td style='text-align: center;border: 1px solid #001D48;background:#E5E0D1;padding:5px;font-size:80%' class='text-right month_cancelled'>" . $value['month_cancelled'] .
                           " </td><td style='text-align: center;border: 1px solid #001D48;background:#E5E0D1;padding:5px;font-size:80%' class='text-right last_2_day'>" . $value['last_2_day'] .
                           " </td><td style='text-align: center;border: 1px solid #001D48;background:#E5E0D1;padding:5px;font-size:80%' class='text-right last_3_day'>" . $value['last_3_day'] .
                           " </td><td style='text-align: center;border: 1px solid #001D48;background:#E5E0D1;padding:5px;font-size:80%' class='text-right greater_than_5_days'>" . $value['greater_than_5_days'] .
                           " </td></tr>";

                   $yesterday_booked += $value['yesterday_booked'];
                   $overall_yesterday_booked += $value['yesterday_booked'];
                   $yesterday_completed += $value['yesterday_completed'];
                   $overall_yesterday_completed += $value['yesterday_completed'];
                   $yesterday_cancelled += $value['yesterday_cancelled'];
                   $overall_yesterday_cancelled += $value['yesterday_cancelled'];
                   $month_completed += $value['month_completed'];
                   $overall_month_completed += $value['month_completed'];
                   $month_cancelled += $value['month_cancelled'];
                   $overall_month_cancelled += $value['month_cancelled'];
                   $last_2_day += $value['last_2_day'];
                   $overall_last_2_day += $value['last_2_day'];
                   $last_3_day += $value['last_3_day'];
                   $overall_last_3_day += $value['last_3_day'];
                   $greater_than_5_days += $value['greater_than_5_days'];
                   $overall_greater_than_5_days += $value['greater_than_5_days'];
               }
           }
       }

       $html .="</tbody>
                         </table>";
       $html.="<table width='100%' style='margin-bottom: 20px;border: 1px solid #ddd; border-collapse: collapse;'><tbody><tr>" .
               "</td><td style='text-align: center;border: 1px solid #001D48;background:#FF9900;width:45%'>" . 'TOTAL' .
               " </td><td style='text-align: center;border: 1px solid #001D48;background:#FF9900;width:7%'><strong class='total_yesterday_booked'>" . $overall_yesterday_booked . '<strong>' .
               " </td><td style='text-align: center;border: 1px solid #001D48;background:#FF9900;width:7%'><strong class='total_yesterday_completed'>" . $overall_yesterday_completed . '<strong>' .
               " </td><td style='text-align: center;border: 1px solid #001D48;background:#FF9900;width:7%'><strong class='total_yesterday_cancelled'>" . $overall_yesterday_cancelled . '<strong>' .
               " </td><td style='text-align: center;border: 1px solid #001D48;background:#FF9900;width:7%'><strong class='total_month_completed'>" . $overall_month_completed . '<strong>' .
               " </td><td style='text-align: center;border: 1px solid #001D48;background:#FF9900;width:7%'><strong class='total_month_cancelled'>" . $overall_month_cancelled . '<strong>' .
               " </td><td style='text-align: center;border: 1px solid #001D48;background:#FF9900;width:7%'><strong class='total_last_2_day'>" . $overall_last_2_day . '<strong>' .
               " </td><td style='text-align: center;border: 1px solid #001D48;background:#FF9900;width:7%'><strong class='total_last_3_day'>" . $overall_last_3_day . '<strong>' .
               " </td><td style='text-align: center;border: 1px solid #001D48;background:#FF9900;width:7%'><strong class='total_greater_than_5_days'>" . $overall_greater_than_5_days . '<strong>' .
               " </td></tr>";

       $html .= '</tbody>
                         </table></div>
                 <script src="'.  base_url().'js/dynamitable.jquery.min.js"></script>'
               . '<script type="text/javascript">$("select").select2();</script>';
       $html .= '<script>
                    $("#count_table").on("change", function() {
                        var yesterday_booked = 0;
                        var yesterday_completed = 0;
                        var yesterday_cancelled = 0;
                        var month_completed = 0;
                        var month_cancelled = 0;
                        var last_2_day = 0;
                        var last_3_day = 0;
                        var greater_than_5_days = 0;
                        
                        $(".yesterday_booked").parent("tr[style!=\'display: none;\']").each(function(index, value) {
                            yesterday_booked = yesterday_booked + parseInt($(this).children("td.yesterday_booked").text());
                        });
                        $(".yesterday_completed").parent("tr[style!=\'display: none;\']").each(function(index, value) {
                            yesterday_completed = yesterday_completed + parseInt($(this).children("td.yesterday_completed").text());
                        });                        
                        $(".yesterday_cancelled").parent("tr[style!=\'display: none;\']").each(function(index, value) {
                            yesterday_cancelled = yesterday_cancelled + parseInt($(this).children("td.yesterday_cancelled").text());
                        });
                        $(".month_completed").parent("tr[style!=\'display: none;\']").each(function(index, value) {
                            month_completed = month_completed + parseInt($(this).children("td.month_completed").text());
                        });
                        $(".month_cancelled").parent("tr[style!=\'display: none;\']").each(function(index, value) {
                            month_cancelled = month_cancelled + parseInt($(this).children("td.month_cancelled").text());
                        });
                        $(".last_2_day").parent("tr[style!=\'display: none;\']").each(function(index, value) {
                            last_2_day = last_2_day + parseInt($(this).children("td.last_2_day").text());
                        });
                        $(".last_3_day").parent("tr[style!=\'display: none;\']").each(function(index, value) {
                            last_3_day = last_3_day + parseInt($(this).children("td.last_3_day").text());
                        });
                        $(".greater_than_5_days").parent("tr[style!=\'display: none;\']").each(function(index, value) {
                            greater_than_5_days = greater_than_5_days + parseInt($(this).children("td.greater_than_5_days").text());
                        });
                        
                        $(".total_yesterday_booked").text(yesterday_booked);
                        $(".total_yesterday_completed").text(yesterday_completed);
                        $(".total_yesterday_cancelled").text(yesterday_cancelled);
                        $(".total_month_completed").text(month_completed);
                        $(".total_month_cancelled").text(month_cancelled);
                        $(".total_last_2_day").text(last_2_day);
                        $(".total_last_3_day").text(last_3_day);
                        $(".total_greater_than_5_days").text(greater_than_5_days);
                    });
           </script></body>
                   </html>';

       return $html;

   }
   
   /*
     * @desc: This function is used to get the partner current status and partner internal status
     * from partner_status_booking_mapping table
     * params: string
     * return :array
     *
     */
    function get_partner_status_mapping_data($current_status, $internal_status, $partner_id = "", $booking_id = "") {
        log_message('info', __METHOD__. " Current status ". $current_status. " Internal status ". $internal_status.
                " partner id ". $partner_id. " Booking id ". $booking_id);
        $partner_status = $this->My_CI->booking_model->get_partner_status($partner_id, $current_status, $internal_status);
        $booking['actor'] = "not_define";
        $booking['next_action'] = "not_define";
        if (!empty($partner_status[0]['partner_current_status']) && !empty($partner_status[0]['partner_internal_status'])) {
            $booking['partner_current_status'] = $partner_status[0]['partner_current_status'];
            $booking['partner_internal_status'] = $partner_status[0]['partner_internal_status'];
            if (!empty($partner_status[0]['actor']) && !empty($partner_status[0]['next_action'])) {
                $booking['actor'] = $partner_status[0]['actor'];
                $booking['next_action'] = $partner_status[0]['next_action'];
            }
        } else {
            $booking['partner_current_status'] = $current_status;
            $booking['partner_internal_status'] = $current_status;

            $this->send_mail_When_no_data_found($current_status, $internal_status, $booking_id, $partner_id);
        }
        $return = array($booking['partner_current_status'], $booking['partner_internal_status'],$booking['actor'],$booking['next_action']);
        log_message('info', __METHOD__. " return Msg ". print_r($return, true));
        return $return;
    }

    /*
     * @desc: This function is used to create report for service centers who is new (withins 2 months old)
     * params: String sf list
     * return :void
     *
     */

    function booking_report_for_new_service_center($sf_list) {
        $CI = get_instance();
        $CI->load->model('reporting_utils');
        $CI->load->model('vendor_model');
        $data = $CI->reporting_utils->get_booking_for_new_service_center($sf_list);
        $new_vendors = $CI->vendor_model->get_new_vendor($sf_list);

        //Generating HTML for the email
        $html = '
                   <html xmlns="http://www.w3.org/1999/xhtml">
                     <head>
                       <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                     </head>
                     <body><div style="margin-top: 30px;font-family:Helvetica;" class="container-fluid">
                         <table style="width: 95%;margin-bottom: 20px;border: 1px solid #ddd; border-collapse: collapse;">
                           <thead>
                             <tr style="padding: 8px;line-height: 1.42857143;vertical-align: top; border-top: 1px solid #ddd">
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:14%;padding-top:8px;">STATE</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:14%;padding-top:8px;">CITY</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:14%;padding-top:8px;">NAME</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:5%;padding-top:8px;">AGE</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:5%;padding-top:8px;">TODAY BOOKINGS</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:5%;padding-top:8px;">BOOKINGS MTD</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:5%;padding-top:8px;"> BOOKINGS COMPLETED</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:5%;padding-top:8px;"> BOOKINGS CANCELLED</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:5%;padding-top:8px;"> 0-2 Days</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:5%;padding-top:8px;"> 3-5 Days</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:5%;padding-top:8px;"> >5 Days</th>

                             </tr>
                           </thead>
                           <tbody>';

        foreach ($new_vendors as $value) {
            if (!empty($data['yesterday_bookings_gone'][$value['id']])) {
                $yesterday = $data['yesterday_bookings_gone'][$value['id']]['booked'];
            } else {
                $yesterday = "";
            }
            if (!empty($data['booking_gone_mtd'][$value['id']])) {
                $month_completed = $data['booking_gone_mtd'][$value['id']]['completed'];
            } else {
                $month_completed = "";
            }
            if (!empty($data['bookings_cancelled'][$value['id']])) {
                $yesterday_cancelled = $data['bookings_cancelled'][$value['id']]['cancelled'];
            } else {
                $yesterday_cancelled = "";
            }
            if (!empty($data['bookings_completed'][$value['id']])) {
                $yesterday_completed = $data['bookings_completed'][$value['id']]['completed'];
            } else {
                $yesterday_completed = "";
            }
            if (!empty($data['pending_bookings_last_2_days'][$value['id']])) {
                $pending_bookings_last_2_days = $data['pending_bookings_last_2_days'][$value['id']]['pending'];
            } else {
                $pending_bookings_last_2_days = "";
            }
            if (!empty($data['pending_bookings_last_3_5_days'][$value['id']])) {
                $pending_bookings_last_3_5_days = $data['pending_bookings_last_3_5_days'][$value['id']]['pending'];
            } else {
                $pending_bookings_last_3_5_days = "";
            }
            if (!empty($data['pending_bookings_greater_than_5_days'][$value['id']])) {
                $pending_bookings_greater_than_5_days = $data['pending_bookings_greater_than_5_days'][$value['id']]['pending'];
            } else {
                $pending_bookings_greater_than_5_days = "";
            }
            
            $style = '';
            if ($value['active'] == 0) {
                $style = "background:#f25788";
            } else if ($value['on_off'] == 0) {
                $style = "background:#7986CB";
            }

            $html.="<tr  style=".$style.">" .
                    "<td style='text-align: center;border: 1px solid #001D48;padding:10px;'>" . $value['state'] .
                    "</td><td style='text-align: center;border: 1px solid #001D48;'>" . $value['district'] .
                    "</td><td style='text-align: center;border: 1px solid #001D48;font-size:90%;'>" . $value['name'] .
                    "</td><td style='text-align: center;border: 1px solid #001D48;font-size:90%;'>" . $value['age'] .
                    " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $yesterday .
                    " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $month_completed .
                    " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $yesterday_completed .
                    " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $yesterday_cancelled .
                    " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $pending_bookings_last_2_days .
                    " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $pending_bookings_last_3_5_days .
                    " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $pending_bookings_greater_than_5_days .
                    " </td></tr>";
        }

        $html .= '</tbody>
                         </table>
                       </div>';
        $html .="<h2>Table Details:</h2><ul><li>"
                . "<b>Age:</b> No of Days from when vendor is created.</li>"
                . "<li><b>Today Bookings:</b> Bookings assigned to vendor Today.</li>"
                . "<li><b>Bookings MTD:</b>Total Bookings assigned to vendor this month. </li>"
                . "<li><b>Bookings Completed:</b> Bookings that are assigned to vendor and completed this month. </li>"
                . "<li><b>Bookings Cancelled:</b> Bookings that are assigned to vendor and cancelled this month. </li>"
                . "<li><b>0-2 Days:</b> Total count of Bookings that are Pending, Rescheduled between 0-2 Days. </li>"
                . "<li><b>3-5 Days:</b> Total count of Bookings that are Pending, Rescheduled bewteen 3-5 Days. </li>"
                . "<li><b> >5 Days:</b> Total count of Bookings that are Pending, Rescheduled greater than 5 Days. </li>"
                . "<li><b> <span style='width:20px;background:#f25788;border-radius:5px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>:</b> Permanent Deactivated SF's </li>"
                . "<li><b> <span style='width:20px;background:#7986CB;border-radius:5px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>:</b> Temporary Off SF's </li>"
                . "</ul>";
        $html .= '</body>
                   </html>';
        return $html;
    }

    /**
     * @Desc: This function is used to Send The Email When No Data found from partner_booking_status_mapping_table
     * @params: array()
     * @return: void
     * 
     */
    function send_mail_When_no_data_found($current_status,$internal_status,$booking_id,$partner_id){
        $to = DEVELOPER_EMAIL;
        $cc = "";
        $bcc = "";
        $subject = " No Data found for '".$current_status."' and '".$internal_status."' in partner_booking_status_mapping Table";
        $message = "
                    <html>
                    <head></head>
                        <body>
                            <h3> No Data Found in partner_booking_status_mapping Table For Following Data</h3>
                            <p><b>Booking ID </b> '".$booking_id."'</p>
                            <p><b>Partner ID </b> '".$partner_id."' </p>
                            <p><b>Current Status</b> '".$current_status."'</p>
                            <p><b>Internal Status</b> '".$internal_status."'</p>
                                
                        </body>
                    </html>";
        $this->My_CI->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, $bcc, $subject, $message, "",NO_DATA_FOUND_IN_STATUS_MAPPING_TABLE, "", $booking_id);
    }
    
    function convert_excel_to_pdf_paidApi($src_format, $dst_format, $files) {
        log_message("info", __METHOD__. " Src ". print_r($src_format, true), " dst ". print_r($dst_format, TRUE). " file ". print_r($files, TRUE));
        //Add Live Secret Key
        $parameter = array(
            'Secret' => 'QNUSX329fBIAICLT',
        );
        $parameters = array_change_key_case($parameter);
        $auth_param = array_key_exists('secret', $parameters) ? 'secret=' . $parameters['secret'] : 'token=' . $parameters['token'];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_URL, "https://v2.convertapi.com/{$src_format}/to/{$dst_format}?{$auth_param}");

        if (is_array($files)) {
            foreach ($files as $index => $file) {
                $parameters["files[$index]"] = file_exists($file) ? new CurlFile($file) : $file;
            }
        } else {
            $parameters['file'] = file_exists($files) ? new CurlFile($files) : $files;
        }

        curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters);
        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);
        if ($response && $httpcode >= 200 && $httpcode <= 299) {
            return json_decode($response);
            
        } else {
            throw new Exception($error . $response, $httpcode);
        }
    }
    
    function check_feature_enable_or_not($fetaure){
        $c2c_enable = TRUE;
        $permission = json_decode(PERMISSION_CONSTANT, TRUE);
        if(!empty($permission) && isset($permission[$fetaure])){
            $c2c_enable =  $permission[$fetaure]['is_on'];

        } 
        return $c2c_enable;
        
    }
    
    function get_booking_request_type($price_tag)
    {
        $newRequest = "";
        if (!empty($price_tag)) {
            $results = array_filter($price_tag, function($value) {
                if ((stripos($value, 'Installation') !== false) || (stripos($value, 'Repair') !== false) || (stripos($value, 'Extended') !== false) || (stripos($value, 'AMC') !== false)) {
                    return $value;                    
                } else {
                    return false;
                }
            });

            if (!empty($results)) {
                $newRequest = array_values($results)[0];
            } else {
                $newRequest = $price_tag[0];
            }
        }
        return $newRequest;
    }
    
    function is_spare_requested($booking){
        if(array_key_exists('spare_parts',$booking['booking_history'])){
            foreach($booking['booking_history']['spare_parts'] as $values){
                if($values['status'] != _247AROUND_CANCELLED){
                     return true;
                }
            }
        }
        return false;
    }

}
