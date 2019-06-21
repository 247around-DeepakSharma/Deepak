<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 36000);

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

require_once BASEPATH . 'libraries/spout-2.4.3/src/Spout/Autoloader/autoload.php';

class Do_background_process extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();

        $this->load->helper(array('form', 'url'));
        $this->load->model('booking_model');
        $this->load->model('service_centers_model');
        $this->load->model('vendor_model');
        $this->load->model('invoices_model');
        $this->load->model('upcountry_model');
        $this->load->model('partner_model');
        $this->load->model('database_testing_model');
        $this->load->library("miscelleneous");
        $this->load->library('booking_utilities');
        $this->load->library('partner_sd_cb');
        $this->load->library('partner_cb');
        $this->load->library('asynchronous_lib');
        $this->load->library('notify');
        $this->load->library('s3');
        $this->load->library('email');
        $this->load->library('push_notification_lib');
        $this->load->dbutil();
    }

    /**
     *  @desc : Function to assign vendors for pending bookings,
     *  @param : service center
     *  @return : void
     */
    function assign_booking() {
        log_message('info', __METHOD__ . " => Entering");

        $data = $this->input->post('booking_id');
        $agent_id = $this->input->post('agent_id');
        $agent_name = $this->input->post('agent_name');

        foreach ($data as $booking_id => $service_center_id) {
            if (!empty($booking_id) || $booking_id != '0') {
                if ($service_center_id != "") {

                    log_message('info', "Async Process to Assign booking - Booking ID: " .
                            $booking_id . ", SF ID: " . $service_center_id);

                    $upcountry_status = $this->miscelleneous->assign_upcountry_booking($booking_id, $agent_id, $agent_name);
                    if ($upcountry_status) {
                        
                        // Send New Booking SMS
                        $this->notify->send_sms_email_for_booking($booking_id, "Newbooking" );
            
                        //Send Push Notification
                        //Send To Vendor
                        $receiverArrayVendor['vendor'] = array($service_center_id);
                        $notificationTextArrayVendor['msg'] = array($booking_id);
                        $this->push_notification_lib->create_and_send_push_notiifcation(BOOKING_ASSIGN_TO_VENDOR,$receiverArrayVendor,$notificationTextArrayVendor);
                        //End Push Notification
                        log_message('info', __FUNCTION__ . " => Continue Process" . $booking_id);
                        $this->miscelleneous->send_sms_create_job_card($upcountry_status);
                        //$this->partner_cb->partner_callback($booking_id);
                    }

                    log_message('info', "Async Process Exiting for Booking ID: " . $booking_id);
                }
            }
        }

        //Checking again for Pending Job cards
        //Disabling this feature temporarily since we are re-generating missed job cards through CRON as well
//        $pending_booking_job_card = $this->database_testing_model->count_pending_bookings_without_job_card();
//        if (!empty($pending_booking_job_card)) {
//            //Creating Job cards for Bookings 
//            foreach ($pending_booking_job_card as $value) {
//                //Prepare job card
//                $this->booking_utilities->lib_prepare_job_card_using_booking_id($value['booking_id']);
//            }
//        }


        log_message('info', __METHOD__ . " => Exiting");
    }

    /**
     * @desc: this is used to upload asynchronouly data from current uploaded excel file.
     */
    function upload_pincode_file() {
        log_message('info', __METHOD__);
        $mapping_file['pincode_mapping_file'] = $this->vendor_model->getLatestVendorPincodeMappingFile();

        $reader = ReaderFactory::create(Type::XLSX);
        $reader->open(TMP_FOLDER . $mapping_file['pincode_mapping_file'][0]['file_name']);
        $count = 1;
        $pincodes_inserted = 0;
        $err_count = 0;
        $header_row = FALSE;

        $rows = array();
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                if ($count > 0) {
                    if ($count % 1000 == 0) {
                        if (!$header_row) {
                            //header row to be removed for the first iteration
                            array_shift($rows);

                            $header_row = TRUE;
                        }

                        //call insert_batch function for $rows..
                        $bat_res = $this->vendor_model->insert_vendor_pincode_mapping_temp($rows);
                        if ($bat_res === FALSE) {
                            log_message('info', 'Error in batch insertion');
                            $err_count++;
                        }
                        $pincodes_inserted += count($rows);
                        //echo date("Y-m-d H:i:s") . "=> " . $pincodes_inserted . " pincodes added\n";
                        unset($rows);
                        $rows = array();

                        //reset count
                        $count = 0;
                    }

                    $data['Vendor_Name'] = $row[0];
                    $data['Vendor_ID'] = $row[1];
                    $data['Appliance'] = $row[2];
                    $data['Appliance_ID'] = $row[3];
                    $data['Brand'] = $row[4];
                    $data['Area'] = $row[5];
                    $data['Pincode'] = $row[6];
                    $data['Region'] = $row[7];
                    $data['City'] = $row[8];
                    $data['State'] = $row[9];

                    array_push($rows, $data);
                }
                $count++;
            }

            //insert remaining rows
            $this->vendor_model->insert_vendor_pincode_mapping_temp($rows);
            //echo date("Y-m-d H:i:s") . "=> " . ($count - 1) . " records added\n";
            $pincodes_inserted += count($rows);
        }

        $reader->close();

        if ($err_count === 0) {
            //Drop the original pincode mapping table and rename the temp table with new pincodes mapping
            $result = $this->vendor_model->switch_temp_pincode_table();

            if ($result)
                $data['table_switched'] = TRUE;
        } else {
            log_message('info', 'Tables not switched, ' . $err_count . ' errors.');
        }
    }

    function complete_booking() {
        log_message('info', "Entering: " . __METHOD__ . " " . json_encode($this->input->post()));
       
        $approvalBooking_id = $this->input->post('booking_id');
        $agent_id = $this->input->post('agent_id');
        $agent_name = $this->input->post('agent_name');
        $partner_id_array = $this->input->post('partner_id');
        $approved_by = $this->input->post('approved_by');
        
        foreach ($approvalBooking_id as $booking_id) {
            
            $partner_id = $partner_id_array[$booking_id];
            
            log_message('info', "Booking Id " . print_r($booking_id, TRUE));
            
            $data = $this->booking_model->getbooking_charges($booking_id);
            $len = count($data);
            $shouldProcess = TRUE;
            for($c=0; $c<$len;$c++){
                $expectredValuesArray = array(_247AROUND_CANCELLED,_247AROUND_COMPLETED);
                echo $data[$c]['internal_status'];
                if(!in_array($data[$c]['internal_status'], $expectredValuesArray)){
                    $shouldProcess = FALSE;
                }
            }
            if($shouldProcess){
                    $current_status = _247AROUND_CANCELLED;

                    $upcountry_charges = 0;
                    log_message('info', ": " . " service center data " . print_r($data, TRUE));

                    // insert in booking files.
                    if(!empty($data[0]['sf_purchase_invoice'])) {
                        $booking_file = [];
                        $booking_file['booking_id'] = $booking_id;
                        $booking_file['file_description_id'] = SF_PURCHASE_INVOICE_FILE_TYPE;
                        $booking_file['file_name'] = $data[0]['sf_purchase_invoice'];
                        $booking_file['file_type'] = 'image/'.pathinfo("https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$value['sf_purchase_invoice'], PATHINFO_EXTENSION);
                        //$booking_file['size'] = filesize("https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$purchase_invoice_file_name);
                        $booking_file['create_date'] = date("Y-m-d H:i:s");
                        $this->booking_model->insert_booking_file($booking_file);
                    }
                    
                    foreach ($data as $key => $value) {
                        $current_status1 = _247AROUND_CANCELLED;
                        if ($value['internal_status'] == _247AROUND_COMPLETED) {
                            $current_status1 = _247AROUND_COMPLETED;
                            $current_status = _247AROUND_COMPLETED;
                        }

                        if ($key == 0) {
                            $upcountry_charges = $value['upcountry_charges'];
                        }

                        if (!empty($value['admin_remarks']) && !empty($value['service_center_remarks'])) {
                            $service_center['closing_remarks'] = "Service Center Remarks:- " . $value['service_center_remarks'] .
                                    "   Admin:-  " . $value['admin_remarks'];
                        } else if (!empty($value['service_center_remarks']) && empty($value['admin_remarks'])) {
                            $service_center['closing_remarks'] = "Service Center Remarks:- " . $value['service_center_remarks'];
                        } else if (empty($value['service_center_remarks']) && !empty($value['admin_remarks'])) {
                            $service_center['closing_remarks'] = "Admin:-  " . $value['admin_remarks'];
                        } else {
                            $service_center['closing_remarks'] = "";
                        }

                        $service_center['current_status'] = $current_status1;
                        $unit_details['booking_status'] = $service_center['internal_status'] = $value['internal_status'];
                        $unit_details['id'] = $service_center['unit_details_id'] = $value['unit_details_id'];
                        $unit_details['ud_closed_date'] = $service_center['closed_date'] = date("Y-m-d H:i:s");
                        $unit_details['sf_purchase_date'] = $value['sf_purchase_date'];
                        $unit_details['sf_purchase_invoice'] = $value['sf_purchase_invoice'];

        //            if (is_null($value['closed_date'])) {
        //                $unit_details['ud_closed_date'] = $service_center['closed_date'] = date("Y-m-d H:i:s");
        //            } else {
        //                $unit_details['ud_closed_date'] = $value['closed_date'];
        //            }

                        log_message('info', ": " . " update Service center data " . print_r($service_center, TRUE));

                        $this->vendor_model->update_service_center_action($booking_id, $service_center);
                        $unit_details['serial_number'] = $value['serial_number'];
                        $unit_details['customer_paid_basic_charges'] = $value['service_charge'];
                        $unit_details['customer_paid_extra_charges'] = $value['additional_service_charge'];
                        $unit_details['customer_paid_parts'] = $value['parts_cost'];
                        $unit_details['is_broken'] = $value['is_broken'];
                        $unit_details['serial_number_pic'] = $value['serial_number_pic'];
                        $unit_details['sf_model_number'] = $value['model_number'];

                        if(!empty($value['serial_number_pic'])){

                           $is_inserted =$this->partner_model->insert_partner_serial_number(array('partner_id' =>$partner_id, 
                               "serial_number" => $value['serial_number'], "active" =>1, "added_by" => "vendor" ));
                            $serialNumberMandatoryPartners = explode(',',SERIAL_NUMBER_MENDATORY);
                           if(!empty($is_inserted) && in_array($partner_id, $serialNumberMandatoryPartners)){
                               //$this->miscelleneous->inform_partner_for_serial_no($booking_id, $value['service_center_id'], $partner_id, $value['serial_number'], $value['serial_number_pic']);
                           } 
                        }

                        log_message('info', ": " . " update booking unit details data " . print_r($unit_details, TRUE));
                        // update price in the booking unit details page
                        $this->booking_model->update_unit_details($unit_details);
                        $this->miscelleneous->update_appliance_details($unit_details['id']);
                    }

                    $booking['closed_date'] = date("Y-m-d H:i:s");
        //        if (is_null($value['closed_date'])) {
        //            $booking['closed_date'] = date("Y-m-d H:i:s");
        //        } else {
        //            $booking['closed_date'] = $value['closed_date'];
        //        }

                    $booking['current_status'] = $current_status;
                    $booking['internal_status'] = $current_status;
                    $booking['amount_paid'] = $data[0]['amount_paid'];
                    $booking['closing_remarks'] = $service_center['closing_remarks'];
                    $booking['cancellation_reason'] = NULL;
                    $booking['customer_paid_upcountry_charges'] = $upcountry_charges;
                    $booking['update_date'] = date('Y-m-d H:i:s');
                    $booking['approved_by'] = $approved_by;

                    $booking['completion_symptom'] = $data[0]['technical_problem'];
                    $booking['technical_solution'] = $data[0]['technical_solution'];
                    //update booking_details table
                    log_message('info', ": " . " update booking details data (" . $current_status . ")" . print_r($booking, TRUE));

                    if ($current_status == _247AROUND_CANCELLED) {
                        $booking['cancellation_reason'] = $data[0]['cancellation_reason'];
                        $booking['internal_status'] = $booking['cancellation_reason'];
                        $booking['api_call_status_updated_on_completed'] = DEPENDENCY_ON_CUSTOMER;
                    }

                    //check partner status from partner_booking_status_mapping table  
                    $actor = $next_action = 'NULL';
                    $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'], $partner_id, $booking_id);
                    if (!empty($partner_status)) {
                        $booking['partner_current_status'] = $partner_status[0];
                        $booking['partner_internal_status'] = $partner_status[1];
                        $actor = $booking['actor'] = $partner_status[2];
                        $next_action = $booking['next_action'] = $partner_status[3];
                    }
                    $booking['is_in_process'] = 0;
                    $this->booking_model->update_booking($booking_id, $booking);
                    $this->miscelleneous->process_booking_tat_on_completion($booking_id);
                    //Update Spare parts details table
                    $spare = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id, spare_parts_details.status", array('booking_id' => $booking_id, 'status NOT IN ("Completed","Cancelled")' => NULL), false);
                    foreach ($spare as $sp) {
                        //Update Spare parts details table
                        $this->service_centers_model->update_spare_parts(array('id' => $sp['id']), array('old_status' => $sp['status'], 'status' => $current_status));
                    }

                    //Log this state change as well for this booking
                   $this->notify->insert_state_change($booking_id, $current_status, _247AROUND_PENDING, $booking['closing_remarks'], $agent_id, $agent_name, $actor,$next_action,$approved_by);

                    $this->notify->send_sms_email_for_booking($booking_id, $current_status);

                    $this->partner_cb->partner_callback($booking_id);
                    //Generate Customer payment Invoice
                    if ($data[0]['amount_paid'] > MAKE_CUTOMER_PAYMENT_INVOICE_GREATER_THAN && $current_status == _247AROUND_COMPLETED) {
                        $invoice_url = base_url() . "employee/user_invoice/payment_invoice_for_customer/" . $booking_id . "/" . $agent_id;

                        $ch = curl_init($invoice_url);
                        curl_setopt_array($ch, array(
                            CURLOPT_POST => TRUE,
                            CURLOPT_RETURNTRANSFER => TRUE,

                        ));

                        // Send the request
                        $response = curl_exec($ch);

                        log_message("info", __METHOD__. " User Invoice Response for booking ID ". $booking_id. " User Invoice ". print_r($response, true) );

                    } else {
                        log_message("info", __METHOD__." Amount Paid less then 5  for booking ID " . $booking_id . " Amount Paid " . $data[0]['amount_paid']);
                    }
            }
            else{
                log_message("info", __METHOD__." Booking ID ( " . $booking_id . ") Internal Status was not in completed or cancelled" . print_r($data,true));
            }
        }
    }

    /**
     * @desc : this method send request to send sms and email for completed, cancelled, Rescheduled, open completed/cancelled booking
     */
    function send_sms_email_for_booking() {
        log_message('info', __FUNCTION__);
        $booking_id = $this->input->post('booking_id');
        $state = $this->input->post('state');

        log_message('info', __FUNCTION__ . " Booking ID :" . print_r($booking_id, true) . " Sms OR EMAIL tag: " . print_r($state, true));

        $this->notify->send_sms_email_for_booking($booking_id, $state);
        log_message('info', ":  Send sms and email request for booking_id" . print_r($booking_id, TRUE) . " and state " . print_r($state, TRUE));
    }
    /*
     * this function is used to send push notifiction asynchronously 
     */
    function send_asyn_push_notification(){
        $title = $msg = $url = $notification_type = $subscriberArray = NULL;
         $auto_hide =0;
         if($this->input->post('title')){
            $title = $this->input->post('title');
        }
        if($this->input->post('msg')){
            $msg = $this->input->post('msg');
        }
        if($this->input->post('url')){
            $url= $this->input->post('url');
        }
        if($this->input->post('notification_type')){
            $notification_type = $this->input->post('notification_type');
        }
        if($this->input->post('subscriberArray')){
            $subscriberArray = $this->input->post('subscriberArray');
        }
        if($this->input->post('auto_hide')){
            $auto_hide = $this->input->post('auto_hide');
        }
        if($this->input->post('notification_tag')){
            $notification_tag = $this->input->post('notification_tag');
        }
        $this->push_notification_lib->send_push_notification($title,$msg,$url,$notification_type,$subscriberArray,$notification_tag,$auto_hide);
    }
    function send_email_to_sf_on_partner_brand_updation(){
        $partnerID= $this->input->post('partner_id');
        $data= $this->input->post('data');
        $services= $this->input->post('services');
        $new_brand_data=$this->input->post('newappliancebrand');
        if(!empty($new_brand_data))
        {
        log_message('info', __FUNCTION__ . ' Function Start');
        $partnerArray = $this->reusable_model->get_search_result_data("bookings_sources","source,code",array("partner_id"=>$partnerID),NULL,NULL,NULL,NULL,NULL,array());
        foreach($services as $serviceDetails){
            $serviceArray[$serviceDetails['id']] = $serviceDetails['services'];
        }
        $tableString = '<table class="table table-bordered" style="border: 1px solid;border-collapse: collapse;">
        <thead>
        <tr style="border-bottom: 1px solid #000;">
        <td style="font-family: Century Gothic;">S.N</td>
        <td style="font-family: Century Gothic;padding-left: 20px;">Service</td>
        <td style="font-family: Century Gothic;padding-left: 20px;">Brands</td>
        </tr>
        </thead>';
        $sn = 1;
//        foreach($data['brand'] as $appliance=>$brandArray){
//            $tableString = $tableString.'<tr style="border-bottom: 1px solid #000;">';
//            $tableString = $tableString.'<td style="font-family: Century Gothic;">'.$sn.'</td>';
//            $tableString = $tableString.'<td style="font-family: Century Gothic;padding-left: 20px;">'.$serviceArray[$appliance].'</td>';
//            $tableString = $tableString.'<td style="font-family: Century Gothic;padding-left: 20px;">'.implode(",",array_values($brandArray)).'</td>';
//            $tableString = $tableString.'</tr>';
//            $sn++;
//        }
         foreach($new_brand_data as $newbranddata){
             $service_id=$newbranddata['service_id'];
            $tableString = $tableString.'<tr style="border-bottom: 1px solid #000;">';
            $tableString = $tableString.'<td style="font-family: Century Gothic;">'.$sn.'</td>';
            $tableString = $tableString.'<td style="font-family: Century Gothic;padding-left: 20px;">'.$serviceArray[$service_id].'</td>';
            $tableString = $tableString.'<td style="font-family: Century Gothic;padding-left: 20px;">'.$newbranddata['brand'].'</td>';
            $tableString = $tableString.'</tr>';
            $sn++;
        }
        $tableString = $tableString."</table>";
        $service_centers = $this->vendor_model->select_active_service_center_email();
        foreach($service_centers as $serviceCentersEmail){
            $tempArray = array_unique($serviceCentersEmail);
            $bccTempArray[] = implode(",",$tempArray);
        }
       $template = $this->booking_model->get_booking_email_template("partner_information_to_sf");
       $body = vsprintf($template[0],array($partnerArray[0]['source'],$partnerArray[0]['code'],$tableString));
       $to = $template[1];
       $from = $template[2];
       $cc = $template[3];
       $subject = $template[4];
       $bcc = implode(",",$bccTempArray);
       log_message('info', __FUNCTION__ . 'BCC '.print_r($bcc,true));
       log_message('info', __FUNCTION__ . 'Body '.print_r($body,true));
       $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $body, "",'partner_information_to_sf');
       log_message('info', __FUNCTION__ . ' Function End');
        }
}
    function create_and_send_partner_requested_report(){
            $is_email = FALSE;
            log_message('info', __FUNCTION__ . 'Function Start '.print_r( $this->input->post(),true));
            $start = $this->input->post('start');
            $end = $this->input->post('end');
            $status = $this->input->post('status');
            $state = $this->input->post('state');
            $agentID = $this->input->post('agentID');
            $partnerID = $this->input->post('partnerID');
            $newCSVFileName = "Booking_summary_" . $start ."_".$end.".csv";
            $csv = TMP_FOLDER . $newCSVFileName;
            $where[] = "(date(booking_details.create_date)>='".$start."' AND date(booking_details.create_date)<='".$end."')";
            if($status != 'all'){
                if($status == _247AROUND_PENDING){
                    $where[] = "booking_details.current_status NOT IN ('Cancelled','Completed')";
              }
                    else{
                        $where[] = "booking_details.current_status IN('".$status."')";
                    }
                }
                if(!in_array('all',$state)){
                    $where[] = "booking_details.state IN ('".implode("','",$state)."')";
                }
               log_message('info', __FUNCTION__ . "Where ".print_r($where,true));
               $report =  $this->partner_model->get_partner_leads_csv_for_summary_email($partnerID,0,implode(' AND ',$where));
               $delimiter = ",";
                $newline = "\r\n";
                $new_report = $this->dbutil->csv_from_result($report, $delimiter, $newline);
                $file = fopen($csv,"w");
                fwrite($file,$new_report);
                fclose($file);
                //write_file($csv, $new_report,'r+');
                $partnerDetails = $this->reusable_model->get_search_result_data("entity_login_table","email",array("entity"=>"partner","entity_id"=>$partnerID,"agent_id"=>$agentID),
                        NULL,NULL,NULL,NULL,NULL,array());
                log_message('info', __FUNCTION__ . "Partner Details ".print_r($partnerDetails,true));
                $subject = 'Booking Report From 247Around';
                $message = 'Hi , <br><br>Your Requested Report is ready, Please find attachment<br><br>Thanks!<br><br> 247Around';
                $is_email = $this->notify->sendEmail(NOREPLY_EMAIL_ID, $partnerDetails[0]['email'], "", "", $subject, $message, $csv,"Partner_Report");
                if($is_email){
                    log_message('info', __FUNCTION__ . "Function End ".$partnerDetails[0]['email']);
                }
                else{
                   log_message('error', __FUNCTION__ . "Function End With Error Sending Email To Partner".$partnerDetails[0]['email']);
                   $subject = 'Error In Sending Requested Partner Report';
                   $message = print_r($this->input->post(),true);
                   $this->notify->sendEmail(NOREPLY_EMAIL_ID, "chhavid@247around.com", "", "", $subject, $message, $csv,"Requested PartnerReport Not Send");
                }
                unlink($csv);
    }
    
    function send_request_for_partner_cb($booking_id){
        $this->partner_cb->partner_callback($booking_id);
    }
      function sendWelcomeSms($phone_number, $vendor_name,$id) {
        $template = $this->vendor_model->getVendorSmsTemplate("new_vendor_creation");
        $smsBody = sprintf($template, $vendor_name);

        $this->notify->sendTransactionalSmsAcl($phone_number, $smsBody);
        //For saving SMS to the database on sucess
    
        $this->notify->add_sms_sent_details($id, 'vendor' , $phone_number,
                   $smsBody, '','new_vendor_creation' );
    

    }
function send_vendor_creation_notification(){
     log_message('error', __FUNCTION__ . "Function Start");
     $new_vendor_mail = $this->input->post('owner_email').','.$this->input->post('primary_contact_email');
        $this->sendWelcomeSms($this->input->post('primary_contact_phone_1'), $this->input->post('name'),$this->input->post('id'));
        $this->sendWelcomeSms($this->input->post('owner_phone_1'), $this->input->post('owner_name'),$this->input->post('id'));
        //Sending Welcome Vendor Mail
        //Getting template from Database
        $template = $this->booking_model->get_booking_email_template("new_vendor_creation");
        if (!empty($template)) {
            $subject = "Welcome to 247around ".$this->input->post('company_name')." (".$this->input->post('district').")";
            $emailBody = $template[0];
            $this->notify->sendEmail($template[2], $new_vendor_mail, $template[3].",".$this->input->post('rm_email'), '', $subject, $emailBody, "",'new_vendor_creation');

            //Logging
            log_message('info', " Welcome Email Send successfully" . $emailBody);
        }else{
            //Logging Error Message
            log_message('info', " Error in Getting Email Template for New Vendor Welcome Mail");
        }
    }
    
    function reopen_booking($booking_id,$status){
        $this->miscelleneous->reopen_booking($booking_id, $status);
    }
    /* end controller */
}
