<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

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

        $service_center = $this->input->post('service_center');
        foreach ($service_center as $booking_id => $service) {
            if ($service != "Select") {
                log_message('info', "Booking ID: " . $booking_id . ", Service centre: " . $service);

                //Assign service centre
                $this->booking_model->assign_booking($booking_id, $service);
                $query1 = $this->booking_model->booking_history_by_booking_id($booking_id);
                $sms['tag'] = "service_center_assigned";
                $sms['phone_no'] = $query1[0]['phone_number'];
                $sms['smsData'] = "";
                //$sms['smsData']['service'] = $query1[0]['services'];                
                $sms_sent = $this->notify->send_sms($sms);
                if ($sms_sent === FALSE) {
                    log_message('info', "SMS not sent to user while assign vendor. User's Phone: " . $query1[0]['phone_number']);
                }

                //Prepare job card
                $this->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);

                //Send mail to vendor, no Note to vendor as of now
                $message = "";
                $this->booking_utilities->lib_send_mail_to_vendor($booking_id, $message);
            }
        }
    }

    /**
     * @desc: this is used to upload asynchronouly data from current uploaded excel file.
     */
    function upload_pincode_file() {
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
                        $this->vendor_model->insert_vendor_pincode_mapping_temp($rows);
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
        }
    }

    function complete_booking() {
        log_message('info', "Entering: " . __METHOD__);

        $booking_id = $this->input->post('approve');
        log_message('info', "Booking Id " . print_r($booking_id, TRUE));

        foreach ($booking_id as $key => $value) {
            $data = $this->booking_model->getbooking_charges($value);
            log_message('info', ": " . "data " . print_r($data, TRUE));

            //unset id of service center action table
            $data[0]['closed_date'] = date('Y-m-d h:i:s');
            unset($data[0]['id']);
            unset($data[0]['service_center_id']);
            $data[0]['closing_remarks'] = "Service Center Remarks:- " . $data[0]['service_center_remarks'] .
                    " <br/> Admin:-  " . $data[0]['admin_remarks'];

            unset($data[0]['admin_remarks']);
            unset($data[0]['create_date']);
            $data[0]['current_status'] = "Completed";
            $data[0]['booking_id'] = $value;

            $this->vendor_model->update_service_center_action($data[0]);

            unset($data[0]['service_center_remarks']);

            //update booking_details table
            log_message('info', "Update data " . print_r($data, true));
            $this->booking_model->update_booking($data[0]['booking_id'], $data[0]);

            //Save this booking id in booking_invoices_mapping table as well now
            $this->invoices_model->insert_booking_invoice_mapping(array('booking_id' => $data[0]['booking_id']));

            //Is this SD booking?
            if (strpos($data[0]['booking_id'], "SS") !== FALSE) {
                $is_sd = TRUE;
            } else {
                $is_sd = FALSE;
            }

            //Update SD bookings if required
            if ($is_sd) {
                if ($this->booking_model->check_sd_lead_exists_by_booking_id($data[0]['booking_id']) === TRUE) {
                    $sd_where = array("CRM_Remarks_SR_No" => $data[0]['booking_id']);
                    $sd_data = array(
                        "Status_by_247around" => "Completed",
                        "Remarks_by_247around" => $data[0]['internal_status'],
                        "Rating_Stars" => "",
                        "update_date" => $data[0]['closed_date']
                    );

                    log_message('info', "update sd lead");
                    $this->booking_model->update_sd_lead($sd_where, $sd_data);
                } else {
                    //Update Partner leads table
                    if (Partner_Integ_Complete) {
                        $partner_where = array("247aroundBookingID" => $data[0]['booking_id']);
                        $partner_data = array(
                            "247aroundBookingStatus" => "Completed",
                            "247aroundBookingRemarks" => $data[0]['internal_status'],
                            "update_date" => $data[0]['closed_date']
                        );

                        log_message('info', "update partner lead");
                        $this->partner_model->update_partner_lead($partner_where, $partner_data);

                        //Call relevant partner API
                        //TODO: make it dynamic, use service object model (interfaces)
                        $partner_cb_data = array_merge($partner_where, $partner_data);
                        $this->partner_sd_cb->update_status_complete_booking($partner_cb_data);
                    }
                }
            }

            $query1 = $this->booking_model->booking_history_by_booking_id($data[0]['booking_id']);

            log_message('info', 'Booking Status Change - Booking id: ' . $data[0]['booking_id'] . " Completed By " . $this->session->userdata('employee_id'));

            $message = "Booking Completion.<br>Customer name: " . $query1[0]['name'] . "<br>Customer phone number: " .
                    $query1[0]['phone_number'] . "<br>Customer email: " . $query1[0]['user_email'] . "<br>Booking Id is: " .
                    $query1[0]['booking_id'] . "<br>Your service name is:" . $query1[0]['services'] . "<br>Booking date: " .
                    $query1[0]['booking_date'] . "<br>Booking completion date: " . $data[0]['closed_date'] .
                    "<br>Amount paid for the booking: " . $data[0]['amount_paid'] . "<br>Your booking completion remark is: " .
                    $data[0]['closing_remarks'] . "<br>Vendor name:" . $query1[0]['vendor_name'] . "<br>Vendor city:" .
                    $query1[0]['district'] . "<br> Thanks!!";

            $to = "anuj@247around.com, nits@247around.com";
            $subject = 'Booking Completion - 247around';
            $cc = "";
            $bcc = "";
            $this->notify->sendEmail("booking@247around.com", $to, $cc, $bcc, $subject, $message, "");

            if ($is_sd == FALSE) {
                $smsBody = "Your request for " . $query1[0]['services'] . " Repair completed. Like us on Facebook goo.gl/Y4L6Hj For discounts download app goo.gl/m0iAcS. For feedback call 011-39595200.";
                $this->notify->sendTransactionalSms($query1[0]['phone_number'], $smsBody);
            }
        }
    }

    function save_completed_booking() {
        
    }

    function save_cancelled_booking() {
        
    }

}
