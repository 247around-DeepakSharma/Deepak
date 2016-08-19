<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

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

	$booking_id = $this->input->post('booking_id');
	$service_center_id = $this->input->post('service_center_id');

	log_message('info', "Booking ID: " . $booking_id . ", Service centre: " . $service_center_id);

	//Assign service centre
	$this->booking_model->assign_booking($booking_id, $service_center_id);

	$data['current_status'] = "Pending";
	$data['internal_status'] = "Pending";
	$data['service_center_id'] = $service_center_id;
	$data['booking_id'] = $booking_id;
	$data['create_date'] = date('Y-m-d H:i:s');
	$this->vendor_model->insert_service_center_action($data);

	//Send SMS to customer
	$query1 = $this->booking_model->booking_history_by_booking_id($booking_id);
	$sms['tag'] = "service_centre_assigned";
	$sms['phone_no'] = $query1[0]['phone_number'];
	$sms['smsData'] = "";

	$sms_sent = $this->notify->send_sms($sms);
	if ($sms_sent === FALSE) {
	    log_message('info', "SMS not sent to user while assigning vendor. User's Phone: " .
		$query1[0]['phone_number']);
	}

	//Prepare job card
	$this->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);

	//Send mail to vendor, no Note to vendor as of now
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
	    $bat_res = $this->vendor_model->insert_vendor_pincode_mapping_temp($rows);
	    if ($bat_res === FALSE) {
		log_message('info', 'Error in batch insertion');
		$err_count++;
	    }

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

    /**
     * @desc: Approve booking status change suggested by Service Center
     *
     * SC cancels or completes a booking and we review this change. If we are reviewing
     * bulk bookings by selecting check-boxes on "Review Bookings - Complete / Cancel"
     * page, this function gets called. Once we approve, bookings gets cancelled /
     * completed in the system.
     *
     * This fn gets called from complete_booking() in new_booking controller.
     *
     * @param : void
     * @return; void
     */
    function complete_booking() {
	$booking_id = $this->input->post('booking_id');

	log_message('info', __METHOD__ . "=> Booking Id " . print_r($booking_id, TRUE));

	$data = $this->booking_model->getbooking_charges($booking_id);
	log_message('info', ": " . "data " . print_r($data, TRUE));

	//Bookings can be completed or cancelled. Take appropriate actions for both types.
	if ($data[0]['internal_status'] == "Cancelled") {
	    $this->process_review_cancel_booking($data);
	} else {
	    $this->process_review_complete_booking($data);
	}

	redirect(base_url() . 'employee/new_booking/review_bookings', 'refresh');
    }

    //Completes booking after Reviewing SC Remarks and Charges
    function process_review_complete_booking($data) {
	$current_status = "Completed";
	$closed_date = date('Y-m-d H:i:s');

	$data[0]['current_status'] = $current_status;
	$this->vendor_model->update_service_center_action($data[0]);

//	$data[0]['closing_remarks'] = "Service Center Remarks:- " . $data[0]['service_center_remarks'] .
//	    " <br/> Admin:-  " . $data[0]['admin_remarks'];
	//Unset fields which are not required
//	unset($data[0]['id']);
//	unset($data[0]['service_center_id']);
//	unset($data[0]['service_center_remarks']);
//	unset($data[0]['cancellation_reason']);
//	unset($data[0]['reschedule_reason']);
//	unset($data[0]['admin_remarks']);
//	unset($data[0]['create_date']);
//	//TODO: Why do we have these fields here? These need to be removed.
//	unset($data[0]['booking_date']);
//	unset($data[0]['booking_timeslot']);
//	$data[0]['closed_date'] = date('Y-m-d H:i:s');
//
	//update booking_details table
	$booking_data['service_charge'] = $data[0]['service_charge'];
	$booking_data['additional_service_charge'] = $data[0]['additional_service_charge'];
	$booking_data['parts_cost'] = $data[0]['parts_cost'];
	$booking_data['amount_paid'] = $data[0]['amount_paid'];
	$booking_data['current_status'] = $data[0]['current_status'];
	$booking_data['internal_status'] = $data[0]['internal_status'];
	$booking_data['update_date'] = $closed_date;
	$booking_data['closed_date'] = $closed_date;
	$booking_data['closing_remarks'] = "Service Center Remarks:- " . $data[0]['service_center_remarks'] .
	    " <br/> Admin:-  " . $data[0]['admin_remarks'];

	log_message('info', "Update Booking Details (Complete): " . print_r($booking_data, true));
	$this->booking_model->update_booking($data[0]['booking_id'], $booking_data);

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
		    "Status_by_247around" => $data[0]['current_status'],
		    "Remarks_by_247around" => $data[0]['internal_status'],
		    "Rating_Stars" => "",
		    "update_date" => $closed_date
		);

		log_message('info', "update sd lead for booking id: " . $data[0]['booking_id']);
		$this->booking_model->update_sd_lead($sd_where, $sd_data);
	    } else {
		//Update Partner leads table
		if (Partner_Integ_Complete) {
		    $partner_where = array("247aroundBookingID" => $data[0]['booking_id']);
		    $partner_data = array(
			"247aroundBookingStatus" => $data[0]['current_status'],
			"247aroundBookingRemarks" => $data[0]['internal_status'],
			"update_date" => $closed_date
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

	//Log this state change as well for this booking
	$state_change['booking_id'] = $data[0]['booking_id'];
	$state_change['old_state'] = 'Pending';
	$state_change['new_state'] = 'Completed';
	$state_change['agent_id'] = $this->session->userdata('id');
	$this->booking_model->insert_booking_state_change($state_change);
	log_message('info', 'Booking Status Change - Booking id: ' . $data[0]['booking_id'] .
	    " Completed By " . $this->session->userdata('employee_id'));

	$query1 = $this->booking_model->booking_history_by_booking_id($data[0]['booking_id'], "join");

	$email['name'] = $query1[0]['name'];
	$email['phone_no'] = $query1[0]['phone_number'];
	$email['user_email'] = $query1[0]['user_email'];
	$email['booking_id'] = $query1[0]['booking_id'];
	$email['service'] = $query1[0]['services'];
	$email['booking_date'] = $query1[0]['booking_date'];
	$email['closed_date'] = $closed_date;
	$email['amount_paid'] = $data[0]['amount_paid'];
	$email['closing_remarks'] = $booking_data['closing_remarks'];
	$email['vendor_name'] = $query1[0]['vendor_name'];
	$email['district'] = $query1[0]['district'];
	$email['tag'] = "complete_booking";
	$email['subject'] = "Booking Completion-AROUND";

	$this->notify->send_email($email);

	if ($is_sd == FALSE) {
	    $sms['tag'] = "complete_booking";
	    $sms['smsData']['service'] = $query1[0]['services'];
	    $sms['phone_no'] = $query1[0]['phone_number'];
	    $sms['booking_id'] = $query1[0]['booking_id'];

	    $this->notify->send_sms($sms);
	} else {
	    $sms['tag'] = "complete_booking_snapdeal";
	    $sms['smsData']['service'] = $query1[0]['services'];
	    $sms['phone_no'] = $query1[0]['phone_number'];
	    $sms['booking_id'] = $query1[0]['booking_id'];

	    $this->notify->send_sms($sms);
	}

	return 1;
    }

    //Cancels booking after Reviewing SC Remarks and Charges
    function process_review_cancel_booking($data) {
	$current_status = "Cancelled";
	$closed_date = date('Y-m-d H:i:s');

	$data[0]['current_status'] = $current_status;
	//Internal status is there in $data[0]['internal_status']
	$this->vendor_model->update_service_center_action($data[0]);

	$booking_data['cancellation_reason'] = $data[0]['cancellation_reason'];
	$booking_data['closing_remarks'] = "Service Center Remarks:- " . $data[0]['service_center_remarks'] .
	    " <br/> Admin:-  " . $data[0]['admin_remarks'];
	$booking_data['update_date'] = $closed_date;
	$booking_data['closed_date'] = $closed_date;

	$booking_data['current_status'] = "Cancelled";
	$booking_data['internal_status'] = "Cancelled";

	$booking_data['service_charge'] = 0;
	$booking_data['service_charge_collected_by'] = 0;
	$booking_data['additional_service_charge'] = 0;
	$booking_data['additional_service_charge_collected_by'] = 0;
	$booking_data['parts_cost'] = 0;
	$booking_data['parts_cost_collected_by'] = 0;
	$booking_data['amount_paid'] = 0;

	//update booking_details table
	log_message('info', "Update Booking Details (Cancel): " . print_r($booking_data, true));
	$this->booking_model->update_booking($data[0]['booking_id'], $booking_data);

	//Is this SD booking?
	if (strpos($data[0]['booking_id'], "SS") !== FALSE) {
	    $is_sd = TRUE;
	} else {
	    $is_sd = FALSE;
	}

	//Update SD leads table if required
	if ($is_sd) {
	    if ($this->booking_model->check_sd_lead_exists_by_booking_id($data[0]['booking_id']) === TRUE) {
		$sd_where = array("CRM_Remarks_SR_No" => $data[0]['booking_id']);
		$sd_data = array(
		    "Status_by_247around" => $booking_data['current_status'],
		    "Remarks_by_247around" => $booking_data['internal_status'],
		    "update_date" => $booking_data['update_date']
		);
		$this->booking_model->update_sd_lead($sd_where, $sd_data);
	    } else {
		if (Partner_Integ_Complete) {
		    //Update Partner leads table
		    $partner_where = array("247aroundBookingID" => $data[0]['booking_id']);
		    $partner_data = array(
			"247aroundBookingStatus" => $booking_data['current_status'],
			"247aroundBookingRemarks" => $booking_data['internal_status'],
			"update_date" => $booking_data['update_date']
		    );
		    $this->partner_model->update_partner_lead($partner_where, $partner_data);

		    //Call relevant partner API
		    //TODO: make it dynamic, use service object model (interfaces)
		    $partner_cb_data = array_merge($partner_where, $partner_data);
		    $this->partner_sd_cb->update_status_cancel_booking($partner_cb_data);
		}
	    }
	}

	//Log this state change as well for this booking
	$state_change['booking_id'] = $data[0]['booking_id'];
	$state_change['old_state'] = 'Pending';
	$state_change['new_state'] = 'Cancelled';
	$state_change['agent_id'] = $this->session->userdata('id');
	$this->booking_model->insert_booking_state_change($state_change);
	log_message('info', 'Booking Status Change - Pending Booking ID: ' .
	    $data[0]['booking_id'] . " Cancelled By " . $this->session->userdata('employee_id'));

	$query1 = $this->booking_model->booking_history_by_booking_id($data[0]['booking_id'], "join");

	$email['name'] = $query1[0]['name'];
	$email['phone_no'] = $query1[0]['phone_number'];
	$email['user_email'] = $query1[0]['user_email'];
	$email['booking_id'] = $query1[0]['booking_id'];
	$email['service'] = $query1[0]['services'];
	$email['booking_date'] = $query1[0]['booking_date'];
	$email['booking_timeslot'] = $query1[0]['booking_timeslot'];
	$email['update_date'] = $booking_data['update_date'];
	$email['cancellation_reason'] = $booking_data['cancellation_reason'];
	$email['vendor_name'] = $query1[0]['vendor_name'];
	$email['district'] = $query1[0]['district'];

	$email['tag'] = "cancel_booking";
	$email['subject'] = "Pending Booking Cancellation - AROUND";

	$this->notify->send_email($email);

	if ($is_sd == FALSE) {
	    $sms['tag'] = "cancel_booking";
	    $sms['smsData']['service'] = $query1[0]['services'];
	    $sms['phone_no'] = $query1[0]['phone_number'];
	    $sms['booking_id'] = $query1[0]['booking_id'];

	    $this->notify->send_sms($sms);
	}

	return 1;
    }

    /* end controller */
}
