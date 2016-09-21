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
        $this->load->model('vendor_model');
        $this->load->model('invoices_model');
        $this->load->model('partner_model');

        $this->load->library('booking_utilities');
        $this->load->library('partner_sd_cb');
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
        log_message('info', "Entering: " . __METHOD__);

        $booking_id = $this->input->post('booking_id');
        $service_center_id = $this->input->post('service_center_id');

        log_message('info', "Async Process to assign booking - Booking ID: " . $booking_id . ", Service centre: " . $service_center_id);

        $unit_details = $this->booking_model->getunit_details($booking_id);
        foreach ($unit_details[0]['quantity'] as $value) {
            $data = array();
            $data['current_status'] = "Pending";
            $data['internal_status'] = "Pending";
            $data['service_center_id'] = $service_center_id;
            $data['booking_id'] = $booking_id;
            $data['create_date'] = date('Y-m-d H:i:s');
            $data['unit_details_id'] = $value['unit_id'];
            $this->vendor_model->insert_service_center_action($data);
        }

        //Send SMS to customer
        $query1 = $this->booking_model->getbooking_history($booking_id);
        $sms['tag'] = "service_centre_assigned";
        $sms['phone_no'] = $query1[0]['phone_number'];
        $sms['booking_id'] = $booking_id;
        $sms['smsData'] = "";

        $sms_sent = $this->notify->send_sms($sms);
        if ($sms_sent === FALSE) {
            log_message('info', "SMS not sent to user while assigning vendor. User's Phone: " .
                    $query1[0]['phone_number']);
        }

        //Prepare job card
        $this->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);

        //COMMENTING TEMPORARILY AS IT IS NOT WORKING...
//        //Send mail to vendor, no Note to vendor as of now
        $message = "";
        $this->booking_utilities->lib_send_mail_to_vendor($booking_id, $message);
    }

    /**
     * @desc: this is used to upload asynchronouly data from current uploaded excel file.
     */
    function upload_pincode_file() {
        log_message('info', __METHOD__);
        $mapping_file['pincode_mapping_file'] = $this->vendor_model->getLatestVendorPincodeMappingFile();

        $reader = ReaderFactory::create(Type::XLSX);
        $reader->open("/tmp/" . $mapping_file['pincode_mapping_file'][0]['file_name']);
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

        log_message('info', "Booking Id " . print_r($booking_id, TRUE));

        $data = $this->booking_model->getbooking_charges($booking_id);
        $current_status = "Cancelled";
        log_message('info', ": " . " service center data " . print_r($data, TRUE));

        foreach ($data as $key => $value) {
            $current_status1 = "Cancelled";
            if ($value['internal_status'] == "Completed") {
                $current_status1 = "Completed";
                $current_status = "Completed";
            }

            $service_center['booking_id'] = $booking_id;
            $service_center['closing_remarks'] = "Service Center Remarks:- " . $value['service_center_remarks'] .
                    " <br/> Admin:-  " . $value['admin_remarks'];
            $service_center['current_status'] = $current_status1;
            $unit_details['booking_status'] = $service_center['internal_status'] = $value['internal_status'];
            $unit_details['id'] = $service_center['unit_details_id'] = $value['unit_details_id'];

            $service_center['update_date'] = date('Y-m-d H:i:s');

            log_message('info', ": " . " update Service center data " . print_r($service_center, TRUE));

            $this->vendor_model->update_service_center_action($service_center);
            $unit_details['serial_number'] = $value['serial_number'];
            $unit_details['customer_paid_basic_charges'] = $value['service_charge'];
            $unit_details['customer_paid_extra_charges'] = $value['additional_service_charge'];
            $unit_details['customer_paid_parts'] = $value['parts_cost'];

            log_message('info', ": " . " update booking unit details data " . print_r($unit_details, TRUE));
            // update price in the booking unit details page
            $this->booking_model->update_unit_details($unit_details);
        }

        $booking['closed_date'] = date('Y-m-d H:i:s');
        $booking['current_status'] = $current_status;
        $booking['internal_status'] = $current_status;
        $booking['amount_paid'] = $data[0]['amount_paid'];
        $booking['closing_remarks'] = $service_center['closing_remarks'];

        //update booking_details table
        log_message('info', ": " . " update booking details data (" . $current_status . ")" . print_r($booking, TRUE));

        if ($current_status == "Cancelled") {

            $booking['cancellation_reason'] = $data[0]['cancellation_reason'];
        } else {

            //Save this booking id in booking_invoices_mapping table as well now
            $this->invoices_model->insert_booking_invoice_mapping(array('booking_id' => $data[0]['booking_id']));
        }

        $this->booking_model->update_booking($booking_id, $booking);

        //Log this state change as well for this booking
        $this->notify->insert_state_change($booking_id, $current_status, "Pending", $agent_id, $agent_name);

        $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
        $send['booking_id'] = $booking_id;
        $send['state'] = $current_status;
        $this->asynchronous_lib->do_background_process($url, $send);

        $this->partner_cb->partner_callback($booking_id);

        $this->notify->send_sms_email_for_booking($booking_id, $current_status);
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

    /**
     * @desc: This method is used to dump all vendor mapping Pincode data into excel and sen to mail. 
     * Email id provided by form
     * @param: void
     * @return: void
     */
    function download_latest_pincode_excel() {
        log_message('info', __FUNCTION__);

        $to_email = $this->input->post('email');
        $notes = $this->input->post('notes');

        $template = 'Vendor_Pincode_Mapping_Template.xlsx';
        //set absolute path to directory with template files
        $templateDir = __DIR__ . "/../excel-templates/";
        //set config for report
        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );
        //load template
        $R = new PHPReport($config);

        $vendor = $this->vendor_model->get_all_pincode_mapping();

        $R->load(array(
            'id' => 'vendor',
            'repeat' => TRUE,
            'data' => $vendor
        ));

        $output_file_dir = "/tmp/";
        $output_file = "Vendor_Pincode_Mapping-" . date('d-M-Y');
        $output_file_name = $output_file . ".xlsx";
        $output_file_excel = $output_file_dir . $output_file_name;

        $response = $R->render('excel', $output_file_excel);

        //Attach file with mail
        $cc = $bcc = "";
        $from = "booking@247around.com";
        $subject = "Vendor Pincode Mapping File - " . date('d-M-Y');
        $message = $notes;
        log_message('info', __FUNCTION__ . " => Pincode File Sent to email: " . $to_email . " File " . $output_file_excel);

        $this->notify->sendEmail($from, $to_email, $cc, $bcc, $subject, $message, $output_file_excel);
    }

    /* end controller */
}
