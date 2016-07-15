<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

define('Partner_Integ_Complete', TRUE);

class New_booking extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
	parent::__Construct();
	$this->load->model('booking_model');
	$this->load->model('vendor_model');
	$this->load->model('invoices_model');
	$this->load->model('partner_model');
	$this->load->library('partner_sd_cb');
	$this->load->library('notify');
	$this->load->helper(array('form', 'url'));

	$this->load->library('form_validation');
	$this->load->library('asynchronous_lib');

	if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee') && ($this->session->userdata('add service') == '1')) {
	    return TRUE;
	} else {
	    redirect(base_url() . "employee/login");
	}
    }

    function index(){}

    /**
     * @desc: This is function is used to complete booking and update service center action in service_center_action table
     * @param : void
     * @return; void
     */
    function complete_review_booking() {
	log_message('info', "Entering: " . __METHOD__);


	$booking_id = $this->input->post('booking_id');
	log_message('info', "booking_id: " . $booking_id);


	$data['service_charge'] = $this->input->post('service_charge');
	$data['additional_service_charge'] = $this->input->post('additional_charge');
	$data['parts_cost'] = $this->input->post('parts_cost');
	$data['amount_paid'] = $this->input->post('amount_paid');
	
	$data['closed_date'] = date("Y-m-d h:i:s");
	$data['internal_status'] = $this->input->post('internal_status');
	if($data['internal_status'] == "Cancelled"){

		$data['current_status'] = "Cancelled";
		$data['cancellation_reason'] = $this->input->post('cancellation_reason');

	} else{
		$data['current_status'] = "Completed";
	}
	$data['service_charge'] = $this->input->post('service_charge');
	$admin_remarks = $this->input->post('admin_remarks');
	
	$service_center_remarks = $this->input->post('service_center_remarks');

	$data['closing_remarks'] = "Service Center Remarks:- " . $service_center_remarks . " <br/> Admin:-  " . $admin_remarks;

	log_message('info', "update data: " . print_r($data, TRUE));

	$this->booking_model->update_booking($booking_id, $data);

	$data['booking_id'] = $booking_id;
	$this->vendor_model->update_service_center_action($data);

	//Save this booking id in booking_invoices_mapping table as well now
	$this->invoices_model->insert_booking_invoice_mapping(array('booking_id' => $booking_id));

	//Is this SD booking?
	if (strpos($booking_id, "SS") !== FALSE) {
	    $is_sd = TRUE;
	} else {
	    $is_sd = FALSE;
	}

	//Update SD bookings if required
	if ($is_sd) {
	    if ($this->booking_model->check_sd_lead_exists_by_booking_id($booking_id) === TRUE) {
		$sd_where = array("CRM_Remarks_SR_No" => $data[0]['booking_id']);
		$sd_data = array(
		    "Status_by_247around" => "Completed",
		    "Remarks_by_247around" => $data['internal_status'],
		    "Rating_Stars" => "",
		    "update_date" => $data['closed_date']
		);

		log_message('info', "update sd lead");
		$this->booking_model->update_sd_lead($sd_where, $sd_data);
	    } else {
		//Update Partner leads table
		if (Partner_Integ_Complete) {
		    $partner_where = array("247aroundBookingID" => $booking_id);
		    $partner_data = array(
			"247aroundBookingStatus" => "Completed",
			"247aroundBookingRemarks" => $data['internal_status'],
			"update_date" => $data['closed_date']
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

	$query1 = $this->booking_model->booking_history_by_booking_id($booking_id);

	log_message('info', 'Booking Status Change- Booking id: ' . $booking_id . " Completed By " . $this->session->userdata('employee_id'));

	$message = "Booking Completion.<br>Customer name: " . $query1[0]['name'] . "<br>Customer phone number: " . $query1[0]['phone_number'] . "<br>Customer email: " . $query1[0]['user_email'] . "<br>Booking Id is: " . $query1[0]['booking_id'] . "<br>Your service name is:" . $query1[0]['services'] . "<br>Booking date: " . $query1[0]['booking_date'] . "<br>Booking completion date: " . $data['closed_date'] . "<br>Amount paid for the booking: " . $data['amount_paid'] . "<br>Your booking completion remark is: " . $data['closing_remarks'] . "<br> Thanks!!";

	$to = "anuj@247around.com, nits@247around.com";

	$subject = 'Booking Completion-AROUND';
	$cc = "";
	$bcc = "";
	$this->notify->sendEmail("booking@247around.com", $to, $cc, $bcc, $subject, $message, "");

	//------End of sending email--------//
	//------Send SMS on Completion of booking-----//
	if ($is_sd == FALSE) {
	    $smsBody = "Your request for " . $query1[0]['services'] . " Repair completed. Like us on Facebook goo.gl/Y4L6Hj For discounts download app goo.gl/m0iAcS. For feedback call 011-39595200.";
	    $this->notify->sendTransactionalSms($query1[0]['phone_number'], $smsBody);
	}

	print_r('success');
    }

    /**
     * @desc: save Admin remarks in service center action table
     * @param: void
     * @return: void
     */
    function admin_remarks() {
	$data['booking_id'] = $this->input->post('booking_id');
	$admin_remarks = $this->input->post('admin_remarks');

	$charges = $this->booking_model->getbooking_charges($data['booking_id']);

	if (empty($charges[0]['admin_remarks'])) {
        $data['current_status'] = "Pending";
	    $data['admin_remarks'] = date("F j") . "  :-" . $admin_remarks;
	    $this->vendor_model->update_service_center_action($data);
	    echo "success";
	} else {
        $data['current_status'] = "Pending";
	    // remove previous text, added in admin_remarks column.
	    $string = str_replace($charges[0]['admin_remarks'], " ", $admin_remarks);
	    // Add current and previous text in admin_remarks column
	    $data['admin_remarks'] = $charges[0]['admin_remarks'] . " <br/> " . date("F j") . ":- " . $string;
	    $this->vendor_model->update_service_center_action($data);
	    echo "success";
	}
    }

    /**
     * @desc: This funtion is used to review bookings (All selected checkbox) which are
     * completed/cancelled by our vendors.
     * It completes/cancels these bookings in the background and returns immediately.
     * @param : void
     * @return : void
     */
    function complete_booking() {
	$approve['approve'] = $this->input->post('approve');

	$url = base_url() . "employee/do_background_process/complete_booking";

	$this->asynchronous_lib->do_background_process($url, $approve);

	redirect(base_url() . 'employee/new_booking/review_bookings');
    }


    /**
     * @desc: This funtion is used to review booking which is completed/cancelled by our vendors.
     * Sends the charges filled by vendor while completing the booking to review booking page
     * It completes/cancels the particular booking in the background and returns immediately.
     * @param : $booking_id
     * @return : array of charges to view
     */
    function review_bookings($booking_id = "") {
	$data['charges'] = $this->booking_model->get_booking_for_review($booking_id);
	$data['data'] = $this->booking_model->review_reschedule_bookings_request();
	$this->load->view('employee/header');
	$this->load->view('employee/review_booking', $data);
    }


    /**
     * @desc: this method is used to approve reschedule booking request in admin panel
     */
    function process_reschedule_booking(){
    	$reschedule_booking_id = $this->input->post('reschedule');
    	$reschedule_booking_date = $this->input->post('reschedule_booking_date');
    	$reschedule_booking_timeslot = $this->input->post('reschedule_booking_timeslot');
    	$reschedule_reason = $this->input->post('reschedule_reason');

    	foreach ($reschedule_booking_id as $key => $value) {

    		$booking['booking_date'] = date('d-m-Y', strtotime($reschedule_booking_date[$value]));
    		$timeslot = $reschedule_booking_timeslot[$value];
    		$booking_timeslot = explode("-", $timeslot);
    		$booking['booking_timeslot'] = $booking_timeslot[1];
    		$booking['current_status'] = 'Rescheduled';
            $booking['internal_status'] = 'Rescheduled';
            $booking['update_date'] = date("Y-m-d h:i:s");
            $booking['reschedule_reason'] = $reschedule_reason[$value];

    		$this->booking_model->update_booking($value, $booking);
    		$data['booking_id'] = $value;
    		$data['internal_status'] =  "Pending";
    		$data['current_status'] =  "Pending";
    		$this->vendor_model->update_service_center_action($data);

    	}

    	  redirect(base_url() . "employee/new_booking/review_bookings");
    }

}
