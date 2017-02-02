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
            if ($service_center_id != "") {

                log_message('info', "Async Process to Assign booking - Booking ID: " .
                        $booking_id . ", SF ID: " . $service_center_id);

                $upcountry_status = $this->miscelleneous->assign_upcountry_booking($booking_id, $agent_id, $agent_name);
                if ($upcountry_status) {
                    log_message('info', __FUNCTION__ . " => Continue Process".$booking_id);
                    //Send SMS to customer
                    $sms['tag'] = "service_centre_assigned";
                    $sms['phone_no'] = $upcountry_status[0]['booking_primary_contact_no'];
                    $sms['booking_id'] = $booking_id;
                    $sms['type'] = "user";
                    $sms['type_id'] = $upcountry_status[0]['user_id'];
                    $sms['smsData'] = "";

                    $this->notify->send_sms_acl($sms);
                    log_message('info', "Send SMS to customer: " . $booking_id);

                    //Prepare job card
                    $this->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);
                    log_message('info', "Async Process to create Job card: " . $booking_id);

                    //Send mail to vendor, no Note to vendor as of now
                    $message = "";
                    $this->booking_utilities->lib_send_mail_to_vendor($booking_id, $message);
                }

                log_message('info', "Async Process Exiting for Booking ID: " . $booking_id);
            }
        }

        //Checking again for Pending Job cards
        $pending_booking_job_card = $this->database_testing_model->count_pending_bookings_without_job_card();
        if (!empty($pending_booking_job_card)) {
            //Creating Job cards for Bookings 
            foreach ($pending_booking_job_card as $value) {
                //Prepare job card
                $this->booking_utilities->lib_prepare_job_card_using_booking_id($value['booking_id']);
            }
        }


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
        log_message('info', "Entering: " . __METHOD__);

        $booking_id = $this->input->post('booking_id');
        $agent_id = $this->input->post('agent_id');
        $agent_name = $this->input->post('agent_name');
        $partner_id = $this->input->post('partner_id');
        //$remarks = $this->input->post('admin_remarks');
        log_message('info', "Booking Id " . print_r($booking_id, TRUE));

        $data = $this->booking_model->getbooking_charges($booking_id);
        $current_status = _247AROUND_CANCELLED ;

        $upcountry_charges = 0;
        log_message('info', ": " . " service center data " . print_r($data, TRUE));
       
        foreach ($data as $key => $value) {
            $current_status1 = _247AROUND_CANCELLED ;
            if($value['internal_status'] == _247AROUND_COMPLETED){
                $current_status1 = _247AROUND_COMPLETED;
                $current_status = _247AROUND_COMPLETED;
            }
            
            if($key ==0){
                $upcountry_charges = $value['upcountry_charges'];
            }
            if(!empty($value['admin_remarks']) && !empty($value['service_center_remarks'])){
                $service_center['closing_remarks'] = "Service Center Remarks:- " . $value['service_center_remarks'] .
                        "   Admin:-  " . $value['admin_remarks'];
                
            } else if(!empty ($value['service_center_remarks']) && empty ($value['admin_remarks'])) {
                $service_center['closing_remarks'] = "Service Center Remarks:- " . $value['service_center_remarks'];
                
            } else if(empty ($value['service_center_remarks']) && !empty ($value['admin_remarks'])){
                $service_center['closing_remarks'] = "Admin:-  " . $value['admin_remarks'];
            } else{
                $service_center['closing_remarks'] = "";
            }
            $service_center['current_status'] = $current_status1;
            $unit_details['booking_status'] = $service_center['internal_status'] = $value['internal_status'];
            $unit_details['id'] = $service_center['unit_details_id'] = $value['unit_details_id'];

            $service_center['update_date'] =  date('Y-m-d H:i:s');
            if(is_null($value['closed_date'])){
                $unit_details['ud_closed_date'] = $service_center['closed_date'] = date("Y-m-d H:i:s");
            } else {
                $unit_details['ud_closed_date'] = $value['closed_date'];
            }

            log_message('info', ": " . " update Service center data " . print_r($service_center, TRUE));

            $this->vendor_model->update_service_center_action($booking_id, $service_center);
            $unit_details['serial_number'] = $value['serial_number'];
            $unit_details['customer_paid_basic_charges'] = $value['service_charge'];
            $unit_details['customer_paid_extra_charges'] = $value['additional_service_charge'];
            $unit_details['customer_paid_parts'] = $value['parts_cost'];
            
            

            log_message('info', ": " . " update booking unit details data " . print_r($unit_details, TRUE));
             // update price in the booking unit details page
            $this->booking_model->update_unit_details($unit_details);
        }
        if(is_null($value['closed_date'])){
            $booking['closed_date'] = date("Y-m-d H:i:s");
            
        } else {
            $booking['closed_date'] = $value['closed_date'];
        }
        
        $booking['current_status'] = $current_status;
        $booking['internal_status'] = $current_status;
        $booking['amount_paid'] = $data[0]['amount_paid'];
        $booking['closing_remarks'] = $service_center['closing_remarks'];
        $booking['customer_paid_upcountry_charges'] = $upcountry_charges;
        
        

        //update booking_details table
        log_message('info', ": " . " update booking details data (" .$current_status .")".print_r($booking, TRUE));

        if($current_status == _247AROUND_CANCELLED){

            $booking['cancellation_reason'] = $data[0]['cancellation_reason'];
            $booking['internal_status'] =  $booking['cancellation_reason'];

        } else {

            //Save this booking id in booking_invoices_mapping table as well now
            $this->invoices_model->insert_booking_invoice_mapping(array('booking_id' => $data[0]['booking_id']));
        }
        //check partner status from partner_booking_status_mapping table  
            $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'],$partner_id, $booking_id);
            if(!empty($partner_status)){
                $booking['partner_current_status'] = $partner_status[0];
                $booking['partner_internal_status'] = $partner_status[1];
            }

        $this->booking_model->update_booking($booking_id, $booking);
        //Update Spare parts details table
        $this->service_centers_model->update_spare_parts(array('booking_id'=> $booking_id), 
                 array('status'=> $current_status));

        //Log this state change as well for this booking
        $this->notify->insert_state_change($booking_id, $current_status, _247AROUND_PENDING, $booking['closing_remarks'],
                $agent_id, $agent_name, _247AROUND);

        $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
        $send['booking_id']  = $booking_id;
        $send['state'] = $current_status;
        $this->asynchronous_lib->do_background_process($url, $send);

        $this->partner_cb->partner_callback($booking_id);
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
        log_message('info', ":  Send sms and email request for booking_id" .print_r($booking_id, TRUE). " and state ". print_r($state, TRUE));

    }
    
    
  
    /* end controller */

}
