<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 3600);

define('Partner_Integ_Complete', TRUE);

class Booking extends CI_Controller {

    /**
     * load list model and helpers
     */
    function __Construct() {
	parent::__Construct();
	$this->load->model('booking_model');
	$this->load->model('booking_model');
	$this->load->model('user_model');
	$this->load->model('vendor_model');
	$this->load->model('invoices_model');
	$this->load->model('service_centers_model');
	$this->load->model('partner_model');
	$this->load->library('partner_sd_cb');
	$this->load->library('partner_cb');
	$this->load->library('notify');
	$this->load->helper(array('form', 'url'));

	$this->load->library('form_validation');
	$this->load->library("pagination");
	$this->load->library("session");
	$this->load->library('s3');
	$this->load->library('email');
	$this->load->library('notify');
	$this->load->library('booking_utilities');
	$this->load->library('partner_sd_cb');
	$this->load->library('asynchronous_lib');

	if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee') && ($this->session->userdata('add service') == '1')) {
	    return TRUE;
	} else {
	    redirect(base_url() . "employee/login");
	}
    }

    /**
     *  @desc : This method is used to add booking.
     *
     * This function will get all the booking details.
     * These booking details are the details which are inserted in booking details table while taking the actual booking.
     *
     * After insertion of booking details, if it is not a query then an email and SMS will be sent to the user for booking confirmation.

     *  @param : user id
     *
     *  @return : void
     */
    public function index($user_id) {
	$booking = $this->getAllBookingInput($user_id);

	$service = $booking['services'];
	$message = $booking['message'];
	unset($booking['message']); // unset message body from booking deatils array
	unset($booking['services']); // unset service name from booking details array

	$this->booking_model->addbooking($booking);

	if ($booking['type'] == 'Booking') {
	    $to = "anuj@247around.com, nits@247around.com";
	    //$to = "abhaya@247around.com, anuj@247Around";
	    $from = "booking@247around.com";
	    $cc = "";
	    $bcc = "";
	    $subject = 'Booking Confirmation-AROUND';
	    $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, "");
	    //-------Sending SMS on booking--------//

	    $smsBody = "Got it! Request for " . trim($service) . " Repair is confirmed for " .
		$booking['booking_date'] . ", " . $booking['booking_timeslot'] .
		". 247Around Indias 1st Multibrand Appliance repair App goo.gl/m0iAcS. 011-39595200";

	    $this->notify->sendTransactionalSms($booking['booking_primary_contact_no'], $smsBody);
	}

	redirect(base_url() . search_page);
    }

    /**
     *  @desc : This function is used to insert or update data in booking unit details and appliance details table
     *  @param : user id, booking id (optional)
     *  @return : Array(booking details)
     */
    function getAllBookingInput($user_id, $booking_id = "") {
	$user['user_id'] = $booking['user_id'] = $user_id;
	$booking['service_id'] = $this->input->post('service_id');
	$booking['source'] = $this->input->post('source_code');
	$user_name = $this->input->post('user_name');
	$booking['type'] = $this->input->post('type');
	$booking['amount_due'] = $this->input->post('grand_total_price');
	$booking['booking_address'] = $this->input->post('home_address');
	$booking['city'] = $this->input->post('city');
	$booking_date = $this->input->post('booking_date');
	$booking['partner_source'] =  $this->input->post('partner_source');
	$booking['booking_date'] = date('d-m-Y', strtotime($booking_date));

	if ($booking_id == "") {

	    $booking['booking_id'] = $this->create_booking_id($user_id, $booking['source'], $booking['type'], $booking['booking_date']);
	} else {
	    $price_tags = array();
	    if ($booking['type'] == "Booking") {

	    if (strpos($booking_id, "Q-") !== FALSE) {

              $booking_id_array = explode("Q-", $booking_id);
              $booking['booking_id'] = $booking_id_array[1];
	    } else {
            $booking['booking_id'] = $booking_id;
	    }

	    }
	}

	// select state by city
	$state = $this->vendor_model->selectSate($booking['city']);
	$booking['state'] = $state[0]['state'];
	$booking['booking_pincode'] = $this->input->post('booking_pincode');
	$service = $booking['services'] = $this->input->post('service');
	$booking['booking_primary_contact_no'] = $this->input->post('booking_primary_contact_no');
	$booking['order_id'] = $this->input->post('order_id');
	$booking['potential_value'] = $this->input->post('potential_value');
	$booking['booking_alternate_contact_no'] = $this->input->post('booking_alternate_contact_no');
	$booking['booking_timeslot'] = $this->input->post('booking_timeslot');
	$booking_remarks = $this->input->post('query_remarks');

	// All brand comming in array eg-- array([0]=> LG, [1]=> BPL)
	$appliance_brand = $this->input->post('appliance_brand');
	// All category comming in array eg-- array([0]=> TV-LCD, [1]=> TV-LED)
	$appliance_category = $this->input->post('appliance_category');
	// All capacity comming in array eg-- array([0]=> 19-30, [1]=> 31-42)
	$appliance_capacity = $this->input->post('appliance_capacity');
	// All model number comming in array eg-- array([0]=> ABC123, [1]=> CDE1478)
	$model_number = $this->input->post('model_number');
	// All price tag comming in array  eg-- array([0]=> Appliance tag1, [1]=> Appliance tag1)
	$appliance_tags = $this->input->post('appliance_tags');
	// All purchase year comming in array eg-- array([0]=> 2016, [1]=> 2002)
	$purchase_year = $this->input->post('purchase_year');
	// All purchase month comming in array eg-- array([0]=> Jan, [1]=> Feb)
	$months = $this->input->post('purchase_month');
	$booking['quantity'] = count($appliance_brand);

	$appliance_id = $this->input->post('appliance_id');

	$serial_number =  $this->input->post('serial_number');

	$partner_id = $this->partner_model->get_all_partner_source("", $booking['source']);

	$partner_net_payable = $this->input->post('partner_paid_basic_charges');
	// this case for partner
	if (!empty($partner_id)) {
	    $booking['partner_id'] = $partner_id[0]['partner_id'];
	}

	// All discount comming in array.  Array ( [BPL] => Array ( [100] => Array ( [0] => 200 ) [102] => Array ( [0] => 100 ) [103] => Array ( [0] => 0 ) ) .. Key is Appliance brand, unit id and discount value.
	$discount = $this->input->post('discount');
	// All prices comming in array with pricing table id
	/* Array(
	  [BPL] => Array
	  (
	  [0] => 100_300
	  [1] => 102_250
	  )

	  [Micromax] => Array
	  (
	  [0] => 100_300
	  )

	  ) */
	//Array ( ['brand'] => Array ( [0] => id_price ) )
	$pricesWithId = $this->input->post("prices");

	$user['user_email'] = $this->input->post('user_email');

	$message = "";

	if ($booking['type'] == 'Booking') {

	    $booking['current_status'] = 'Pending';
	    $booking['internal_status'] = 'Scheduled';
	    $booking['booking_remarks'] = $booking_remarks;


	    $message .= "Congratulations You have received new booking, details are mentioned below:
      <br>Customer Name: " . $user_name . "<br>Customer Phone Number: " . $booking['booking_primary_contact_no'] .
		"<br>Customer email address: " . $user['user_email'] . "<br>Booking Id: " .
		$booking['booking_id'] . "<br>Service name:" . $service .
		"<br>Number of appliance: " . count($appliance_brand) . "<br>Booking Date: " .
		$booking['booking_date'] . "<br>Booking Timeslot: " . $booking['booking_timeslot'] .
		"<br>Amount Due: " . $booking['amount_due'] . "<br>Your Booking Remark is: " .
		$booking['booking_remarks'] . "<br> Booking address: " . $booking['booking_address'] .
		" " . $booking['city'] . ", " . $booking['state'] . ", " .
		"<br>Booking pincode: " . $booking['booking_pincode'] . "<br><br>
        Appliance Details:<br>";
	} else if ($booking['type'] == 'Query') {

	    $booking['current_status'] = "FollowUp";
	    $booking['internal_status'] = "FollowUp";
	    $booking['query_remarks'] = $booking_remarks;
	    if($booking_id !=""){
	    	$booking['booking_id'] = "Q-".$booking_id;
	    	$this->service_centers_model->delete_booking_id($booking_id);
	    }
	    
	}

	foreach ($appliance_brand as $key => $value) {

	    $services_details = "";
	    $appliances_details = "";
	    $appliances_details['user_id'] = $user_id;
	    $appliances_details['brand'] = $services_details['appliance_brand'] = $value; // brand
	    // get category from appiance category array for only specific key.
	    $appliances_details['category'] = $services_details['appliance_category'] = $appliance_category[$key];
	    // get appliance_capacity from appliance_capacity array for only specific key.
	    $appliances_details['capacity'] = $services_details['appliance_capacity'] = $appliance_capacity[$key];
	    // get model_number from appliance_capacity array for only specific key such as $model_number[0].
	    $appliances_details['model_number'] = $services_details['model_number'] = $model_number[$key];
	    // get appliance tag from appliance_tag array for only specific key such as $appliance_tag[0].
	    $appliances_details['tag'] = $services_details['appliance_tag'] = $appliance_tags[$key];
	    // get purchase year from purchase year array for only specific key such as $purchase_year[0].
	    $appliances_details['purchase_year'] = $services_details['purchase_year'] = $purchase_year[$key];
	    $services_details['booking_id'] = $booking['booking_id'];
	    $appliances_details['serial_number'] = $services_details['serial_number'] = $serial_number[$key];
	    // get purchase months from months array for only specific key such as $months[0].
	    $appliances_details['purchase_month'] = $services_details['purchase_month'] = $months[$key];
	    $appliances_details['service_id'] = $services_details['service_id'] = $booking['service_id'];
	    $appliances_details['last_service_date'] = date('Y-m-d H:i:s');


	    if (!empty($partner_id)) {

		$services_details['partner_id'] = $booking['partner_id'];
	    }

	    /* if appliance id exist the initialize appliance id in array and update appliance details other wise it insert appliance details and return appliance id
	     * */

	    if (isset($appliance_id[$key])) {

		$services_details['appliance_id'] = $appliance_id[$key];
		$this->booking_model->update_appliances($services_details['appliance_id'], $appliances_details);
	    } else {

		$services_details['appliance_id'] = $this->booking_model->addappliance($appliances_details);
	    }

	    // log_message ('info', __METHOD__ . "Appliance details data". print_r($appliances_details));
	    //Array ( ['brand'] => Array ( [0] => id_price ) )
	    foreach ($pricesWithId[$value] as $keys => $values) {

		$prices = explode("_", $values);  // split string..
		$services_details['id'] = $prices[0]; // This is id of service_centre_charges table.
		// discount for appliances. Array ( [BPL] => Array ( [100] => Array ( [0] => 200 ) [102] => Array ( [0] => 100 ) [103] => Array ( [0] => 0 ) )

		$services_details['around_paid_basic_charges'] = $discount[$value][$services_details['id']][0];
		$services_details['partner_paid_basic_charges'] = $partner_net_payable[$value][$services_details['id']][0];

		if ($booking_id == "") {

		    $result = $this->booking_model->insert_data_in_booking_unit_details($services_details, $booking['state']);

		    if ($booking['current_status'] != 'FollowUp') {
			$message .= "<br>Brand : " . $result['appliance_brand'] . "<br>Category : " .
			    $result['appliance_category'] . "<br>Capacity : " . $result['appliance_capacity'] .
			    "<br>Selected service is: " . $result['price_tags'] . "<br>Total price is: " .
			    $result['customer_net_payable'] . "<br>";

			$message .= "<br/>";

			//Log this state change as well for this booking
			//param:-- booking id, new state, old state, employee id, employee name
			$this->notify->insert_state_change($booking['booking_id'], $booking['current_status'], "New", $this->session->userdata('id'), $this->session->userdata('employee_id'));
		    }
		} else {
            $services_details['booking_status'] = "";
		    $price_tag = $this->booking_model->update_booking_in_booking_details($services_details, $booking_id, $booking['state']);

		    array_push($price_tags, $price_tag);

		    //Log this state change as well for this booking
		    $this->notify->insert_state_change($booking_id, "Updated", $booking['current_status'], $this->session->userdata('id'), $this->session->userdata('employee_id'));
		}
	    }
	}

	if (!empty($price_tags)) {

	    $this->booking_model->check_price_tags_status($booking['booking_id'], $price_tags);
	}

	if ($booking['type'] == 'Query') {

	    $booking['message'] .= "";
	} else {

	    $booking['message'] = $message;
	}
	$this->user_model->edit_user($user);

	return $booking;
    }

    /**
     * @desc: this method generates booking id. booking id is the combination of booking source, 4 digit random number, date and month
     * @param: user id, booking source, booking type
     * @return: booking id
     */
    function create_booking_id($user_id, $source, $type, $booking_date) {
	$booking['booking_id'] = '';

	$yy = date("y", strtotime($booking_date));
	$mm = date("m", strtotime($booking_date));
	$dd = date("d", strtotime($booking_date));

	$booking['booking_id'] = str_pad($user_id, 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
	$booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($user_id)) + 1);


	//Add source
	$booking['source'] = $source;
	if ($type == "Booking") {
	    $booking['booking_id'] = $booking['source'] . "-" . $booking['booking_id'];
	} else {
	    $booking['booking_id'] = "Q-" . $booking['source'] . "-" . $booking['booking_id'];
	}

	return $booking['booking_id'];
    }

    /**
     * @desc : This function loads add booking form with city, booking source, service and user details
     * @param: String(Phone Number)
     * @return : void
     */
    function addbooking($phone_number) {
	$data = $this->booking_model->get_city_booking_source_services($phone_number);
	$this->load->view('employee/header');
	$this->load->view('employee/addbookingmodel');
	$this->load->view('employee/addbooking', $data);
    }

    /**
     *  @desc : This function displays list of pending bookings according to pagination and also show all booking if $page is All.
     *  @param : Starting page & number of results per page
     *  @return : pending bookings according to pagination
     */
    function view($offset = 0, $page = 0, $booking_id = "") {

	if ($page == '0') {
	    $page = 50;
	}

	$offset = ($this->uri->segment(4) != '' ? $this->uri->segment(4) : 0);
	$config['base_url'] = base_url() . 'employee/booking/view';
	$config['total_rows'] = $this->booking_model->total_pending_booking($booking_id);

	$config['per_page'] = $page;
	$config['uri_segment'] = 4;
	$config['first_link'] = 'First';
	$config['last_link'] = 'Last';

	$this->pagination->initialize($config);
	$data['links'] = $this->pagination->create_links();

	$data['Count'] = $config['total_rows'];
	$data['Bookings'] = $this->booking_model->date_sorted_booking($config['per_page'], $offset, $booking_id);
	if ($this->session->flashdata('result') != '')
	    $data['success'] = $this->session->flashdata('result');

	$this->load->view('employee/header');
	$this->load->view('employee/booking', $data);
    }

    /**
     *  @desc : This function displays list of completed and Cancelled bookings according to pagination. If $status is Completed, it gets Completed booking and if $status is Cancelled, it gets Cancelled booking
     *
     * This method will show only that number of bookings which are being selected from the pagination section(50/100/200/All).
     *
     *  @param : Starting page & number of results per page
     *  @return : completed bookings according to pagination
     */
    function viewclosedbooking($status, $offset = 0, $page = 0, $booking_id = "") {
	if ($page == '0') {
	    $page = 50;
	}

	$offset = ($this->uri->segment(5) != '' ? $this->uri->segment(5) : 0);
	$config['base_url'] = base_url() . 'employee/booking/viewclosedbooking/' . $status;
	$config['total_rows'] = $this->booking_model->total_closed_booking($status, $booking_id);
	$config['per_page'] = $page;
	$config['uri_segment'] = 4;
	$config['first_link'] = 'First';
	$config['last_link'] = 'Last';

	$this->pagination->initialize($config);
	$data['links'] = $this->pagination->create_links();
	$data['Bookings'] = $this->booking_model->view_completed_or_cancelled_booking($config['per_page'], $offset, $status, $booking_id);
	$this->load->view('employee/header');

	$this->load->view('employee/viewcompletedbooking', $data);
    }

    /**
     *  @desc : This function returns the cancelation reason for booking
     *  @param : void
     *  @return : all the cancelation reasons present in the database
     */
    function cancelreason() {
	$query = $this->booking_model->cancelreason("247around");
	$data['reason'] = null;
	if ($query) {

	    $data['reason'] = $query;
	}

	$this->load->view('employee/cancelbooking', $data);
    }

    /**
     *  @desc : This function is to select booking to be completed
     *
     * Opens a form with basic booking details and feilds to be filled before completing the booking like amount collected, amount collected by, etc.
     *
     *  @param : booking id
     *  @return : user details and booking history to view
     */
    function get_complete_booking_form($booking_id) {
	$data['booking_id'] = $booking_id;

	$data['booking_history'] = $this->booking_model->getbooking_history($booking_id);
	$data['booking_unit_details'] = $this->booking_model->getunit_details($booking_id);
	$source = $this->partner_model->get_all_partner_source("0", $data['booking_history'][0]['source']);
	$data['booking_history'][0]['source_name'] = $source[0]['source'];

	$partner_id = $this->booking_model->get_price_mapping_partner_code($data['booking_history'][0]['source']);
	$data['prices'] = array();

	foreach ($data['booking_unit_details'] as $key => $value) {

	    $prices = $this->booking_model->getPricesForCategoryCapacity($data['booking_history'][0]['service_id'], $data['booking_unit_details'][$key]['category'], $data['booking_unit_details'][$key]['capacity'], $partner_id, $data['booking_history'][0]['state']);

	    foreach ($value['quantity'] as $index => $price_tag) {
		// Searched already inserted price tag exist in the price array (get all service category)
		$id = $this->search_for_key($price_tag['price_tags'], $prices);
		// remove array key, if price tag exist into price array
		unset($prices[$id]);
	    }

	    array_push($data['prices'], $prices);
	}


	$this->load->view('employee/header');
	$this->load->view('employee/completebooking', $data);
    }

    /**
     * @desc: This is method return index key, if service caregory matches with given price tags
     * @param: Price tag and Array
     * @return: key
     */
    function search_for_key($price_tag, $array) {
	foreach ($array as $key => $val) {
	    if ($val['service_category'] === $price_tag) {
		return $key;
	    }
	}
	return null;
    }

    /**
     *  @desc : This function is to select booking/Query to be canceled.
     *
     * If $status is followup means it Query and its load internal status
     *
     * Opens a form with user's name and option to be choosen to cancel the booking.
     *
     * Atleast one booking/Query cancellation reason must be selected.
     *
     * If others option is choosen, then the cancellation reason must be entered in the textarea.
     *
     *  @param : booking id
     *  @return : user details and booking history to view
     */
    function get_cancel_form($booking_id, $status = "") {
	$data['user_and_booking_details'] = $this->booking_model->getbooking_history($booking_id);
	$data['reason'] = $this->booking_model->cancelreason("247around");
	if ($status == "followup") {

	    $data['internal_status'] = $this->booking_model->get_internal_status("Cancel");
	}

	$this->load->view('employee/header');
	$this->load->view('employee/cancelbooking', $data);
    }

    /**
     *  @desc : This function is to cancels the booking/Query
     *
     * Accepts the cancellation reason provided in cancel booking/Query form and then cancels booking with the reason provided.
     *
     *  @param : booking id
     *  @return : cancels the booking and load view
     */
    function process_cancel_form($booking_id) {
	$data['cancellation_reason'] = $this->input->post('cancellation_reason');
	$data['closed_date'] = $data['update_date'] = date("Y-m-d H:i:s");

	if ($data['cancellation_reason'] == 'Other') {
	    $data['cancellation_reason'] = "Other : " . $this->input->post("cancellation_reason_text");
	}
	$data['current_status'] = $data['internal_status'] = "Cancelled";
	$data_vendor['cancellation_reason'] = $data['cancellation_reason'];

	$this->booking_model->update_booking($booking_id, $data);

	//Update this booking in vendor action table
	$data_vendor['update_date'] = date("Y-m-d H:i:s");
	$data_vendor['current_status'] = $data_vendor['internal_status'] = "Cancelled";

	$data_vendor['booking_id'] = $booking_id;

	$this->vendor_model->update_service_center_action($data_vendor);

	$this->update_price_while_cancel_booking($booking_id);

	//Log this state change as well for this booking
	//param:-- booking id, new state, old state, employee id, employee name
	$this->notify->insert_state_change($booking_id, $data['current_status'], "Pending", $this->session->userdata('id'), $this->session->userdata('employee_id'));

	// this is used to send email or sms while booking cancelled
	$url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
	$send['booking_id'] = $booking_id;
	$send['state'] = $data['current_status'];
	$this->asynchronous_lib->do_background_process($url, $send);
	// call partner callback
	$this->partner_cb->partner_callback($booking_id);

	redirect(base_url() . search_page);
    }

    function update_price_while_cancel_booking($booking_id) {
	$unit_details['booking_status'] = "Cancelled";
	$unit_details['tax_rate'] = $unit_details['customer_total'] = 0;
	$unit_details['customer_paid_basic_charges'] = $unit_details['partner_paid_basic_charges'] = 0;
	$unit_details['around_paid_basic_charges'] = $unit_details['around_comm_basic_charges'] = 0;
	$unit_details['vendor_to_around'] = $unit_details['around_to_vendor'] = 0;

	$this->booking_model->update_booking_unit_details($booking_id, $unit_details);
    }

    /**
     *  @desc : This function is to select booking to be rescheduled
     *
     * Opens a form with user's name and current date and timeslot.
     *
     * Select the new date and timeslot for current booking.
     *
     *  @param : booking id
     *  @return : user details and booking history to view
     */
    function get_reschedule_booking_form($booking_id) {
	$getbooking = $this->booking_model->getbooking($booking_id);

	if ($getbooking) {

	    $this->session->userdata('employee_id');
	    $data['booking_id'] = $getbooking;

	    $query = $this->booking_model->getbooking_history($booking_id);

	    $data1['booking_id'] = $query;

	    $this->load->view('employee/header');
	    $this->load->view('employee/reschedulebooking', array('data' => $data, 'data1' => $data1));
	} else {
	    echo "This Id doesn't Exists";
	}
    }

    /**
     *  @desc : This function is to reschedule the booking.
     *
     * Accepts the new booking date and timeslot provided in form and then reschedules booking
     * accordingly.
     *
     *  @param : booking id
     *  @return : reschedules the booking and load view
     */
    function process_reschedule_booking_form($booking_id) {

	$data['booking_date'] = date('d-m-Y', strtotime($this->input->post('booking_date')));
	$data['booking_timeslot'] = $this->input->post('booking_timeslot');
	$data['current_status'] = 'Rescheduled';
	$data['internal_status'] = 'Rescheduled';
	$data['update_date'] = date("Y-m-d H:i:s");


	if ($data['booking_timeslot'] == "Select") {
	    echo "Please Select Booking Timeslot.";
	} else {

	    $this->booking_model->update_booking($booking_id, $data);

	    $service_center_data['booking_id'] = $booking_id;
	    $service_center_data['internal_status'] = "Pending";
	    $service_center_data['current_status'] = "Pending";
	    $service_center_data['update_date'] = date("Y-m-d H:i:s");
	    $this->vendor_model->update_service_center_action($service_center_data);

	    $send_data['booking_id'] = $booking_id;
	    $send_data['current_status'] = "Rescheduled";

	    $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
	    $this->asynchronous_lib->do_background_process($url, $send_data);

	    //Setting mail to vendor flag to 0, once booking is rescheduled
	    $this->booking_model->set_mail_to_vendor_flag_to_zero($booking_id);

	    //Prepare job card
	    $this->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);
	    // Partner Call back
	    $this->partner_cb->partner_callback($booking_id);

	    log_message('info', 'Rescheduled- Booking id: ' . $booking_id . " Rescheduled By " . $this->session->userdata('employee_id') . " data " . print_r($data, true));

	    redirect(base_url() . search_page);
	}
    }

    /**
     * @desc : This function will get all the brands for that particular service with help of service_id on ajax call
     * @param: service_id of booking
     * @return : all present brands
     */
    function getBrandForService() {
	$service_id = $this->input->post('service_id');

	$result = $this->booking_model->getBrandForService($service_id);
	echo "<option selected disabled> Select Brand</option>";
	foreach ($result as $brand) {
	    echo "<option>$brand[brand_name]</option>";
	}
    }

    /**
     * @desc : This function will load category with help of service_id on ajax call
     * this method get get category on the basis of service id, state, price mapping id
     * @input: service id, city, partner_code
     * @return : displays category
     */
    function getCategoryForService() {

	$service_id = $this->input->post('service_id');
	$city = $this->input->post('city');
	$partner = $this->input->post('partner_code');

	$partner_id = $this->booking_model->get_price_mapping_partner_code($partner);

	$state = $this->vendor_model->selectSate($city);
	$result = $this->booking_model->getCategoryForService($service_id, $state[0]['state'], $partner_id);
	echo "<option selected disabled>Select Appliance Category</option>";
	foreach ($result as $category) {
	    echo "<option>$category[category]</option>";
	}
    }

    /**
     * @desc : This function will load capacity with help of Category and service_id on ajax call
     * @param: Category and service_id of booking
     * @return : displays capacity
     */
    function getCapacityForCategory() {
	$service_id = $this->input->post('service_id');
	$category = $this->input->post('category');
	$city = $this->input->post('city');
	$parter_code = $this->input->post('partner_code');

	$partner_id = $this->booking_model->get_price_mapping_partner_code($parter_code);

	$state = $this->vendor_model->selectSate($city);

	$result = $this->booking_model->getCapacityForCategory($service_id, $category, $state[0]['state'], $partner_id);

	foreach ($result as $capacity) {
	    echo "<option>$capacity[capacity]</option>";
	}
    }

    /**
     * @desc : This function will show the price and services for ajax call
     * this method returns price list on the basis of service id, category, capacity, price mapping id, state
     * @input: service_id,category, capacity, brand, partner code, city, clone number
     * @return : services name and there prices
     */
    function getPricesForCategoryCapacity() {

	$service_id = $this->input->post('service_id');
	$category = $this->input->post('category');
	$capacity = $this->input->post('capacity');
	$brand = $this->input->post('brand');
	$parter_code = $this->input->post('partner_code');
	$city = $this->input->post('city');
	$clone_number = $this->input->post('clone_number');
	$state = $this->vendor_model->selectSate($city);

	$partner_id = $this->booking_model->get_price_mapping_partner_code($parter_code);

	$result = $this->booking_model->getPricesForCategoryCapacity($service_id, $category, $capacity, $partner_id, $state[0]['state']);
	if (!empty($result)) {

	    echo "<tr><th>Service Category</th><th>Std. Charges</th><th>Partner Discount</th><th>Final Charges</th><th>247around Discount</th><th>Selected Services</th></tr>";
	    $html = "";

	    $i = 0;

	    foreach ($result as $prices) {
		$service_category = $prices['service_category'];

		$html .="<tr><td>" . $prices['service_category'] . "</td>";
		$html .= "<td>" . $prices['customer_total'] . "</td>";
		$html .= "<td><input  type='text' class='form-control partner_discount' name= 'partner_paid_basic_charges[$brand][" . $prices['id'] . "][]'  id='partner_paid_basic_charges_" . $i . "_" . $clone_number . "' value = '" . $prices['partner_net_payable'] . "' placeholder='Enter discount' readonly/></td>";
		$html .= "<td>" . $prices['customer_net_payable'] . "</td>";
		$html .= "<td><input  type='text' class='form-control discount' name= 'discount[$brand][" . $prices['id'] . "][]'  id='discount_" . $i . "_" . $clone_number . "' value = '0' placeholder='Enter discount' readonly></td>";
		$html .= "<td><input class='price_checkbox'";
		if ($prices['service_category'] == 'Repair') {
		    $html .= "checked";
		}

		$html .=" type='checkbox' id='checkbox_" . $i . "_" . $clone_number . "'";
		$html .= "name='prices[$brand][]'";
		$html .= "  onclick='final_price(), enable_discount(this.id)'" .
		    "value=" . $prices['id'] . "_" . intval($prices['customer_net_payable']) . " ></td><tr>";

		$i++;
	    }
	    echo $html;
	} else {
	    echo "Price Table Not Found";
	}
    }

    /**
     *  @desc : Ajax call(This function is to get non working days for particular vendor)
     *
     *  To know the non working days for the selected vendor.
     *
     *  @param : vendor's id(service centre id)
     *  @return : Non working days for particular vendor
     */
    function get_non_working_days_for_vendor($service_centre_id) {
	$result = $this->vendor_model->get_non_working_days_for_vendor($service_centre_id);
	if (empty($result)) {
	    echo "No non working days found";
	}
	$non_working_days = $result[0]['non_working_days'];
	echo $non_working_days;
    }

    /**
     *  @desc : This function is to select completed booking to be rated
     *  @param : booking id
     *  @return : user details to view
     */
    function get_rating_form($booking_id, $status) {
	$getbooking = $this->booking_model->getbooking($booking_id);
	if ($getbooking) {

	    $this->session->userdata('employee_id');
	    $data = $getbooking;
	    $this->load->view('employee/header');
	    $this->load->view('employee/rating', array('data' => $data, 'status' =>  $status));
	} else {
	    echo "Id doesn't exist";
	}
    }

    /**
     *  @desc : This function is to save ratings for booking and for vendors
     *
     * With the help of this form you can rate the booking as per user experience and for vendors for the quality of service provided by the vendor.
     *
     *  @param : booking id
     *  @return : rate for booking and load view
     */
    function process_rating_form($booking_id, $status) {

	if ($this->input->post('rating_star') != "Select") {
	    $data['rating_stars'] = $this->input->post('rating_star');
	    $data['rating_comments'] = $this->input->post('rating_comments');
	} else {
	    $data['rating_stars'] = '';
	    $data['rating_comments'] = '';
	}

	if ($this->input->post('vendor_rating_star') != "Select") {
	    $data['vendor_rating_stars'] = $this->input->post('vendor_rating_star');
	    $data['vendor_rating_comments'] = $this->input->post('vendor_rating_comments');
	} else {
	    $data['vendor_rating_stars'] = '';
	    $data['vendor_rating_comments'] = '';
	}

	$this->booking_model->update_booking($booking_id, $data);

	redirect(base_url() . 'employee/booking/viewclosedbooking/'.$status);
    }

    /**
     *  @desc : This function is to save ratings for vendors
     *
     * With the help of this form you can rate for vendors for the quality of service provided by the vendor.
     *
     *  @param : booking id
     *  @return : rate for booking and load view
     */
    function vendor_rating($booking_id) {
	$this->booking_model->vendor_rating($booking_id, $data);
	$query = $this->booking_model->viewbooking();
	$data['Bookings'] = null;
	if ($query) {
	    $data['Bookings'] = $query;
	}
	$this->load->view('employee/header');
	$this->load->view('employee/booking', $data);
    }

    /**
     *  @desc : This function is to view deatils of any particular booking.
     *
     * 	We get all the details like User's details, booking details, and also the appliance's unit details.
     *
     *  @param : booking id
     *  @return : booking details and load view
     */
    function viewdetails($booking_id) {
	$data['booking_history'] = $this->booking_model->getbooking_history($booking_id);
	$data['unit_details'] = $this->booking_model->get_unit_details($booking_id);

	$data['service_center'] = $this->booking_model->selectservicecentre($booking_id);

	$this->load->view('employee/header');
	$this->load->view('employee/viewdetails', $data);
    }

//    /**
//     *  @desc : Function to sort pending bookings with current status
//     *
//     * 	This will display all the bookings present in sorted manner according to there booking status.
//     *
//     *  @param : start booking and bookings per page
//     *  @return : bookings and load view
//     */
//    function status_sorted_booking($offset = 0, $page = 0) {
//        if ($page == 0) {
//            $page = 50;
//        }
//
//        $offset = ($this->uri->segment(4) != '' ? $this->uri->segment(4) : 0);
//        $config['base_url'] = base_url() . 'employee/booking/status_sorted_booking';
//        $config['total_rows'] = $this->booking_model->total_pending_booking();
//        $config['per_page'] = $page;
//        $config['uri_segment'] = 4;
//        $config['first_link'] = 'First';
//        $config['last_link'] = 'Last';
//
//        $this->pagination->initialize($config);
//        $data['links'] = $this->pagination->create_links();
//
//        $data['Bookings'] = $this->booking_model->status_sorted_booking($config['per_page'], $offset);
//        $this->load->view('employee/header');
//
//        $this->load->view('employee/statussortedbooking', $data);
//    }
//    /**
//     *  @desc : Function to sort pending and rescheduled bookings with booking date
//     *
//     * 	This method will display all the pending and rescheduled bookings present in sorted manner according to there booking date.
//     *
//     *  @param : start booking and bookings per page
//     *  @return : sorted bookings and load view
//     */
//    function date_sorted_booking($offset = 0, $page = 0) {
//        if ($page == 0) {
//            $page = 50;
//        }
//        //$offset = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
//        $offset = ($this->uri->segment(4) != '' ? $this->uri->segment(4) : 0);
//        $config['base_url'] = base_url() . 'employee/booking/date_sorted_booking';
//        $config['total_rows'] = $this->booking_model->total_pending_booking();
//        $config['per_page'] = $page;
//        $config['uri_segment'] = 4;
//        $config['first_link'] = 'First';
//        $config['last_link'] = 'Last';
//
//        $this->pagination->initialize($config);
//        $data['links'] = $this->pagination->create_links();
//
//        $data['Bookings'] = $this->booking_model->date_sorted_booking($config['per_page'], $offset);
//        $this->load->view('employee/header');
//
//        $this->load->view('employee/datesortedbooking', $data);
//    }
//    /**
//     *  @desc : Function to sort pending and rescheduled bookings with service center's name
//     *
//     * 	This method will display all the pending and rescheduled bookings present in
//     *      sorted manner according to service centre's name assigned for the booking.
//     *
//     * 	This function is usefull to get all the bookings assigned to particular vendor together.
//     *
//     *  @param : start booking and bookings per page
//     *  @return : assigned vendor sorted bookings and load view
//     */
//    function service_center_sorted_booking($offset = 0, $page = 0) {
//	if ($page == 0) {
//	    $page = 50;
//	}
//
//	$offset = ($this->uri->segment(4) != '' ? $this->uri->segment(4) : 0);
//	$config['base_url'] = base_url() . 'employee/booking/service_center_sorted_booking';
//	$config['total_rows'] = $this->booking_model->total_pending_booking();
//	$config['per_page'] = $page;
//	$config['uri_segment'] = 4;
//	$config['first_link'] = 'First';
//	$config['last_link'] = 'Last';
//	$this->pagination->initialize($config);
//	$data['links'] = $this->pagination->create_links();
//	$data['Bookings'] = $this->booking_model->service_center_sorted_booking($config['per_page'], $offset);
//	$this->load->view('employee/header');
//	$this->load->view('employee/booking', $data);
//    }

    /**
     *  @desc : This function is to select particular appliance for booking.
     *  We have already made a function to get_edit_booking_form, this method use that function to insert booking by appliance id
     *  @param : appliance id
     *  @return : user's and appliance details to view
     */
    function get_appliance_booking_form($appliance_id) {
	$this->get_edit_booking_form("", $appliance_id);
    }

    /**
     *  @desc : This function is to get add new brand page
     *
     * 	Through this we add a new brand for selected service.
     *
     *  @param : void
     *  @return : list of active services present
     */
    function get_add_new_brand_form() {
	$services = $this->booking_model->selectservice();

	$this->load->view('employee/header');
	$this->load->view('employee/addnewbrand', array('services' => $services));
    }

    /**
     *  @desc : This function is to add new brand.
     *
     * 	Enters the new brand to our existing brand list for a particular service
     *
     *  @param : void
     *  @return : add new brand and load view
     */
    function process_add_new_brand_form() {
	$new_brand = $this->input->post('new_brand');
	$brand_name = $this->input->post('brand_name');

	foreach ($new_brand as $service_id => $service) {
	    if ($service != "Select") {
		$arr[$service] = $brand_name[$service_id];
	    }
	}
	foreach ($arr as $service_id => $brand) {
	    $this->booking_model->addNewApplianceBrand($service_id, $brand);
	}

	redirect(base_url() . 'employee/booking/get_add_new_brand_form', 'refresh');
    }

    /**
     *  @desc : This function is to view pending queries according to pagination
     *  @param : offset and per page number
     *  @return : list of pending queries according to pagination
     */
    function view_queries($status, $offset = 0, $page = 0, $booking_id = "") {
	if ($page == '0') {
	    $page = 50;
	}

	$offset = ($this->uri->segment(5) != '' ? $this->uri->segment(5) : 0);
	$config['base_url'] = base_url() . 'employee/booking/view_queries/' . $status;
	$config['total_rows'] = $this->booking_model->total_queries($status, $booking_id);

	$config['per_page'] = $page;
	$config['uri_segment'] = 4;
	$config['first_link'] = 'First';
	$config['last_link'] = 'Last';

	$this->pagination->initialize($config);
	$data['links'] = $this->pagination->create_links();

	$data['Bookings'] = $this->booking_model->get_queries($config['per_page'], $offset, $status, $booking_id);

	$this->load->view('employee/header');
	$this->load->view('employee/viewpendingqueries', $data);
    }

    /**
     * @desc: load update booking form to update booking
     * @param: booking id
     * @return : void
     */
    function get_edit_booking_form($booking_id, $appliance_id = "") {

	if ($booking_id != "") {
	    $booking_history = $this->booking_model->getbooking_history($booking_id);
	} else {
	    $booking_history = $this->booking_model->getbooking_history_by_appliance_id($appliance_id);
	}

	$booking = $this->booking_model->get_city_booking_source_services($booking_history[0]['phone_number']);
	$booking['booking_history'] = $booking_history;
	$booking['unit_details'] = $this->booking_model->getunit_details($booking_id, $appliance_id);
	$booking['brand'] = $this->booking_model->getBrandForService($booking_history[0]['service_id']);
	$partner_id = $this->booking_model->get_price_mapping_partner_code($booking_history[0]['source']);

	$booking['category'] = $this->booking_model->getCategoryForService($booking_history[0]['service_id'], $booking_history[0]['state'], $partner_id);

	$booking['capacity'] = array();
	$booking['prices'] = array();
	$booking['appliance_id'] = $appliance_id;

	foreach ($booking['unit_details'] as $key => $value) {

	    $capacity = $this->booking_model->getCapacityForCategory($booking_history[0]['service_id'], $booking['unit_details'][$key]['category'], $booking_history[0]['state'], $partner_id);

	    $prices = $this->booking_model->getPricesForCategoryCapacity($booking_history[0]['service_id'], $booking['unit_details'][$key]['category'], $booking['unit_details'][$key]['capacity'], $partner_id, $booking_history[0]['state']);

	    array_push($booking['capacity'], $capacity);
	    array_push($booking['prices'], $prices);
	}

	$this->load->view('employee/header');
	$this->load->view('employee/addbookingmodel');
	$this->load->view('employee/update_booking', $booking);
    }

    /**
     * @desc: this is used to update booking
     */
    function update_booking($user_id, $booking_id) {

	$booking = $this->getAllBookingInput($user_id, $booking_id);
	unset($booking['message']); // unset message body from booking deatils array
	unset($booking['services']); // unset service name from booking details array

	$this->booking_model->update_booking($booking_id, $booking);

	$this->partner_cb->partner_callback($booking_id);

	redirect(base_url() . search_page);
    }

    /**
     *  @desc : This function is used to rebook cancel query
     *  @param : String (Booking Id)
     *  @param : String(Phone Number)
     *  @return : refirect user controller
     */
    function cancelled_booking_re_book($booking_id, $phone) {
	$this->booking_model->change_booking_status($booking_id);
	redirect(base_url() . 'employee/user/finduser/0/0/' . $phone, 'refresh');
    }


    /**
     *  @desc : This function is used to call customer from admin panel
     *  @param : Phone Number
     *  @return : none
     */
    function call_customer($cust_phone) {
	// log_message('info', __FUNCTION__);

	$s1 = $_SERVER['HTTP_REFERER'];
	//$s2 = "https://www.aroundhomzapp.com/";
	$s2 = base_url();
	$redirect_url = substr($s1, strlen($s2));

	$this->checkUserSession();

	//Get customer id
	$cust_id = '';
	$user = $this->user_model->search_user($cust_phone);
	if ($user) {
	    $cust_id = $user[0]['user_id'];
	}

	//Find agent phone from session
	$agent_id = $this->session->userdata('id');
	$agent_phone = $this->session->userdata('phone');

	//Save call log
	$this->booking_model->insert_outbound_call_log(array(
	    'agent_id' => $agent_id, 'customer_id' => $cust_id,
	    'customer_phone' => $cust_phone
	));

	//Make call to customer now
	$this->notify->make_outbound_call($agent_phone, $cust_phone);

	//Redirect to the page from where you landed in this function, do not refresh
	redirect(base_url() . $redirect_url);
    }

    /**
     *  @desc : Callback fn called after agent finishes customer call
     *  @param : Phone Number
     *  @return : none
     */
    function call_customer_status_callback() {
	log_message('info', "Entering: " . __METHOD__);

	//http://support.exotel.in/support/solutions/articles/48259-outbound-call-to-connect-an-agent-to-a-customer-
	$callDetails['call_sid'] = (isset($_GET['CallSid'])) ? $_GET['CallSid'] : null;
	$callDetails['status'] = (isset($_GET['Status'])) ? $_GET['Status'] : null;
	$callDetails['recording_url'] = (isset($_GET['RecordingUrl'])) ? $_GET['RecordingUrl'] : null;
	$callDetails['date_updated'] = (isset($_GET['DateUpdated'])) ? $_GET['DateUpdated'] : null;

	log_message('info', print_r($callDetails, true));
//	//insert in database
//	$this->apis->insertPassthruCall($callDetails);
    }

    /**
     * @desc :This funtion will check user session for an eemplouee.
     */
    function checkUserSession() {
	if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
	    return TRUE;
	} else {
	    $this->session->sess_destroy();
	    redirect(base_url() . "employee/login");
	}
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
	    $data['internal_status'] = "Pending";
	    $data['admin_remarks'] = date("F j") . "  :-" . $admin_remarks;
	    $this->vendor_model->update_service_center_action($data);
	    echo "success";
	} else {
	    $data['current_status'] = "Pending";
	    $data['internal_status'] = "Pending";
	    // remove previous text, added in admin_remarks column.
	    $string = str_replace($charges[0]['admin_remarks'], " ", $admin_remarks);
	    // Add current and previous text in admin_remarks column
	    $data['admin_remarks'] = $charges[0]['admin_remarks'] . "   " . date("F j") . ":- " . $string;
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
    function checked_complete_review_booking() {
	$approved_booking = $this->input->post('approved_booking');
	$url = base_url() . "employee/do_background_process/complete_booking";
	$agent_id = $this->session->userdata('id');
	$agent_name = $this->session->userdata('employee_id');

	foreach ($approved_booking as $key => $booking_id) {
	    $data = array();
	    $data['booking_id'] = $booking_id;
	    $data['agent_id'] = $agent_id;
	    $data['agent_name'] = $agent_name;
	    $this->asynchronous_lib->do_background_process($url, $data);
	}

	redirect(base_url() . 'employee/booking/review_bookings');
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
     * @desc: This method is used to approve reschedule booking requests in admin panel and
     * upadte current status and internal status (Pending) of bookings in service center
     * booking action table.
     *
     */
    function process_review_reschedule_bookings() {
	$reschedule_booking_id = $this->input->post('reschedule');
	$reschedule_booking_date = $this->input->post('reschedule_booking_date');
	$reschedule_booking_timeslot = $this->input->post('reschedule_booking_timeslot');
	$reschedule_reason = $this->input->post('reschedule_reason');

	foreach ($reschedule_booking_id as $key => $booking_id) {
	    $booking['booking_date'] = date('d-m-Y', strtotime($reschedule_booking_date[$booking_id]));
	    $booking['booking_timeslot'] = $reschedule_booking_timeslot[$booking_id];
	    $send['state'] = $booking['current_status'] = 'Rescheduled';
	    $booking['internal_status'] = 'Rescheduled';
	    $booking['update_date'] = date("Y-m-d H:i:s");
	    $send['booking_id'] = $data['booking_id'] = $booking_id;
	    $booking['reschedule_reason'] = $reschedule_reason[$booking_id];

	    $this->booking_model->update_booking($booking_id, $booking);
	    $data['internal_status'] = "Pending";
	    $data['current_status'] = "Pending";

	    $this->vendor_model->update_service_center_action($data);

	    $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
	    $this->asynchronous_lib->do_background_process($url, $send);

	    //Log this state change as well for this booking
	    //param:-- booking id, new state, old state, employee id, employee name
	    $this->notify->insert_state_change($booking_id, "Rescheduled", "Pending", $this->session->userdata('id'), $this->session->userdata('employee_id'));


	    //Setting mail to vendor flag to 0, once booking is rescheduled
	    $this->booking_model->set_mail_to_vendor_flag_to_zero($booking_id);

	    $this->partner_cb->partner_callback($booking_id);

	    log_message('info', 'Rescheduled- Booking id: ' . $booking_id . " Rescheduled By " . $this->session->userdata('employee_id') . " data " . print_r($data, true));
	}

	redirect(base_url() . "employee/booking/review_bookings");
    }

    /**
     * @desc: This is used to complete booking by admin. It gets booking id and status as parameter. if status is 0 then redirect pending booking other wise redirect completed booking page
     * @param: String Array, string
     * @return :void
     */
    function process_complete_booking($booking_id, $status = "") {
	// customer paid basic charge is comming in array
	// Array ( [100] =>  500 , [102] =>  300 )
	$customer_basic_charge = $this->input->post('customer_basic_charge');
	// Additional service charge is comming in array
	$additional_charge = $this->input->post('additional_charge');
	// Parts cost is comming in array
	$parts_cost = $this->input->post('parts_cost');
	$booking_status = $this->input->post('booking_status');
	$total_amount_paid = $this->input->post('grand_total_price');
	$admin_remarks = $this->input->post('admin_remarks');
	$serial_number = $this->input->post('serial_number');
	$internal_status = "Cancelled";
	$city =  $this->input->post('city');
	$state = $this->vendor_model->selectSate($city);

	$service_center_details = $this->booking_model->getbooking_charges($booking_id);
    $i = 0;
	foreach ($customer_basic_charge as $unit_id => $value) {
	    // variable $unit_id  is existing id in booking unit details table of given booking id

	    $data = array();
	    $data['customer_paid_basic_charges'] = $value;
	    $data['customer_paid_extra_charges'] = $additional_charge[$unit_id];
	    $data['customer_paid_parts'] = $parts_cost[$unit_id];
	    $data['serial_number'] = $serial_number[$i];
	    // it checks sting new in unit_id variable
	    if (strpos($unit_id, 'new') !== false) {
		if (isset($booking_status[$unit_id])) {
		    if ($booking_status[$unit_id] == "Completed") {
			// if new line item selected then coming unit id variable is the combination of unit id & new(string) and service charges id
			// e.g- 12new103
			$remove_string_new = explode('new', $unit_id);
			$unit_id = $remove_string_new[0];
			$service_charges_id = $remove_string_new[1];
			$data['booking_id'] = $booking_id;
			$data['booking_status'] = "Completed";
			$internal_status = "Completed";
			$this->booking_model->insert_new_unit_item($unit_id, $service_charges_id, $data, $state[0]['state']);
		    }
		}
	    } else {
		$data['booking_status'] = $booking_status[$unit_id];

		if ($data['booking_status'] === "Completed") {
		    $internal_status = "Completed";
		}

		$data['id'] = $unit_id;
		
		log_message('info', ": " . " update booking unit details data " . print_r($data, TRUE));

		// update price in the booking unit details page
		$this->booking_model->update_unit_details($data);

		$service_center['booking_id'] = $booking_id;
		$service_center['closing_remarks'] = "Service Center Remarks:- " . $service_center_details[0]['service_center_remarks'] .
		    " <br/> Admin:-  " . $admin_remarks;
		$service_center['internal_status'] = $service_center['current_status'] = $data['booking_status'];

		$service_center['unit_details_id'] = $unit_id;
		$service_center['update_date'] = date('Y-m-d H:i:s');

		log_message('info', ": " . " update Service center data " . print_r($service_center, TRUE));
		$this->vendor_model->update_service_center_action($service_center);
	    }

	    $i++;
	}

	$booking['current_status'] = $internal_status;
	$booking['internal_status'] = $internal_status;
	$booking['booking_id'] = $booking_id;
	$booking['rating_stars'] = $this->input->post('rating_stars');
	$booking['vendor_rating_stars'] = $this->input->post('vendor_rating_stars');
	$booking['vendor_rating_comments'] = $this->input->post('vendor_rating_comments');
	$booking['rating_comments'] = $this->input->post('rating_comments');
	$booking['closing_remarks'] = $service_center['closing_remarks'];
	$booking['closed_date'] = date('Y-m-d H:i:s');
	$booking['amount_paid'] = $total_amount_paid;

	//update booking_details table
	log_message('info', ": " . " update booking details data (" . $booking['current_status'] . ")" . print_r($booking, TRUE));
	// this function is used to update booking details table
	$this->booking_model->update_booking($booking_id, $booking);

	//Log this state change as well for this booking
	//param:-- booking id, new state, old state, employee id, employee name
	$this->notify->insert_state_change($booking_id, $internal_status, "Pending", $this->session->userdata('id'), $this->session->userdata('employee_id'));

	$url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
	$send['booking_id'] = $booking_id;
	$send['state'] = $internal_status;
	$this->asynchronous_lib->do_background_process($url, $send);

	$this->partner_cb->partner_callback($booking_id);

	if ($status == "0") {
	    redirect(base_url() . 'employee/booking/view');
	} else {
	    redirect(base_url() . 'employee/booking/viewclosedbooking/' . $internal_status);
	}
    }

    /**
     *  @desc : This function is to present form to open completed bookings
     *
     * It converts a Completed Booking into Pending booking and schedule it to
     * a new booking date & time.
     *
     *  @param : String (Booking Id)
     *  @return :
     */
    function get_convert_booking_to_pending_form($booking_id, $status) {
	$bookings = $this->booking_model->getbooking_history($booking_id);
	$bookings[0]['status'] = $status;

	$this->load->view('employee/header');
	$this->load->view('employee/complete_to_pending', $bookings[0]);
    }

    /**
     *  @desc : This function is to process form to open completed bookings
     *
     * Accepts the new booking date and timeslot povided in form and then opens
     * a completed booking.
     *
     *  @param : booking id
     *  @return : Converts the booking to Pending stage and load view
     */
    function process_convert_booking_to_pending_form($booking_id, $status) {
	$data['booking_date'] = date('d-m-Y', strtotime($this->input->post('booking_date')));
	$data['booking_timeslot'] = $this->input->post('booking_timeslot');
	$data['current_status'] = 'Pending';
	$data['internal_status'] = 'Scheduled';
	$data['update_date'] = date("Y-m-d H:i:s");
	$data['closed_date'] = NULL;
	$data['vendor_rating_stars'] = NULL;
	$data['vendor_rating_comments'] = NULL;
	$data['amount_paid'] = NULL;
	$data['rating_stars'] = NULL;
	$data['rating_comments'] = NULL;
	$data['closing_remarks'] = NULL;
	$data['booking_jobcard_filename'] = NULL;
	$data['mail_to_vendor'] = 0;


	if ($data['booking_timeslot'] == "Select") {
	    echo "Please Select Booking Timeslot.";
	} else {

	    $this->booking_model->convert_booking_to_pending($booking_id, $data, $status);

	    //Log this state change as well for this booking
	    //param:-- booking id, new state, old state, employee id, employee name
	    $this->notify->insert_state_change($booking_id, "Pending", $status, $this->session->userdata('id'), $this->session->userdata('employee_id'));

	    $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
	    $send['booking_id'] = $booking_id;
	    $send['state'] = "OpenBooking";
	    $this->asynchronous_lib->do_background_process($url, $send);

	    log_message('info', $status . ' Booking Opened - Booking id: ' . $booking_id . " Opened By: " . $this->session->userdata('employee_id') . " => " . print_r($data, true));

	    redirect(base_url() . search_page);
	}
    }

    /**
     *  @desc : This function is to present form to open cancelled bookings
     *
     * It converts a Cancelled Booking into Pending booking and schedule it to
     * a new booking date & time.
     *
     *  @param : String (Booking Id)
     *  @return :
     */
    function get_convert_cancelled_booking_to_pending_form($booking_id) {
	$bookings = $this->booking_model->booking_history_by_booking_id($booking_id);

	$this->load->view('employee/header');
	$this->load->view('employee/cancelled_to_pending', $bookings[0]);
    }


    /**
     *  @desc : This function is used to open a cancelled query
     *  @param : String (Booking Id)
     *  @return : redirect user controller
     */
    function open_cancelled_query($booking_id) {
	$this->booking_model->change_booking_status($booking_id);

	redirect(base_url() . 'employee/booking/view_pending_queries/0/0/' . $booking_id);
    }

    function c_test1() {

	$booking_details = $this->booking_model->c_get_all_booking_id();
	//print_r($booking_details);
	foreach ($booking_details as $key => $value) {
	    $booking_id = $value['booking_id'];

	    switch ($value['price_tags']) {
		case 'Installation':
		case 'Installation,':
		case 'InstallationwithoutStand,' :
		case 'Installation,WallMountStand,':
		case 'Installation with Stand,':
		case 'InstallationwithStand,':

		    switch ($value['internal_status']) {
			case 'Completed TV Without Stand':
			case 'Completed With Demo':
			case 'Completed':
			    $data = array();
			    $data['appliance_id'] = $value['appliance_id'];
			    $data['partner_id'] = $value['partner_id'];
			    $data['service_id'] = $value['service_id'];
			    $data['price_tags'] = "Installation & Demo";

			    /* / echo "<br/>";
			      print_r($value['price_tags']);
			      echo "<br/>";
			      print_r($data);
			      echo "<br/>"; */
			    $this->booking_model->update_booking_unit_details($booking_id, $data);
			    break;

			case 'Completed TV With Stand':

			    $data = array();
			    $data['appliance_id'] = $value['appliance_id'];
			    $data['partner_id'] = $value['partner_id'];
			    $data['service_id'] = $value['service_id'];
			    $unit_details = $this->booking_model->get_unit_details($value['booking_id']);

			    $data['price_tags'] = "Installation & Demo";
			    $unit_id = $unit_details[0]['id'];


			    $this->booking_model->update_unit_details_by_id($unit_id, $data);

			    $data['booking_id'] = $value['booking_id'];
			    $data['appliance_brand'] = $value['appliance_brand'];
			    $data['appliance_category'] = $value['appliance_category'];
			    $data['appliance_capacity'] = $value['appliance_capacity'];
			    $data['model_number'] = $value['model_number'];

			    $data['appliance_tag'] = $value['appliance_tag'];
			    $data['purchase_year'] = $value['purchase_year'];
			    $data['purchase_month'] = $value['purchase_month'];
			    $data['price_tags'] = "Wall Mount Stand";

			    $this->booking_model->addunitdetails($data);
			    break;

			default:
			    echo $value['booking_id'];
			    break;
		    }

		    break;

		case 'Repair,':
		case 'Repair':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $data['price_tags'] = "Repair";


		    $this->booking_model->update_booking_unit_details($booking_id, $data);
		    break;

		case 'Visit':
		case 'Visit,':
		case 'VisitCharge,':
		case 'Visit Charge':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $data['price_tags'] = "Visit";


		    $this->booking_model->update_booking_unit_details($booking_id, $data);

		    break;
		case 'Repair,Installation,':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $unit_details = $this->booking_model->get_unit_details($value['booking_id']);

		    $data['price_tags'] = "Repair";
		    $unit_id = $unit_details[0]['id'];

		    $this->booking_model->update_unit_details_by_id($unit_id, $data);

		    $data['booking_id'] = $value['booking_id'];
		    $data['appliance_brand'] = $value['appliance_brand'];
		    $data['appliance_category'] = $value['appliance_category'];
		    $data['appliance_capacity'] = $value['appliance_capacity'];
		    $data['model_number'] = $value['model_number'];

		    $data['appliance_tag'] = $value['appliance_tag'];
		    $data['purchase_year'] = $value['purchase_year'];
		    $data['purchase_month'] = $value['purchase_month'];
		    $data['price_tags'] = "Installation & Demo";

		    $this->booking_model->addunitdetails($data);
		    break;

		case 'GasRechargewithDryer,':

		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $data['price_tags'] = "Gas Recharge with Dryer";


		    $this->booking_model->update_booking_unit_details($booking_id, $data);
		    break;

		case 'Installation,Uninstallation,':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $unit_details = $this->booking_model->get_unit_details($value['booking_id']);

		    $data['price_tags'] = "Installation & Demo";
		    $unit_id = $unit_details[0]['id'];
		    echo "<br/>";
		    $this->booking_model->update_unit_details_by_id($unit_id, $data);

		    $data['booking_id'] = $value['booking_id'];
		    $data['appliance_brand'] = $value['appliance_brand'];
		    $data['appliance_category'] = $value['appliance_category'];
		    $data['appliance_capacity'] = $value['appliance_capacity'];
		    $data['model_number'] = $value['model_number'];

		    $data['appliance_tag'] = $value['appliance_tag'];
		    $data['purchase_year'] = $value['purchase_year'];
		    $data['purchase_month'] = $value['purchase_month'];
		    $data['price_tags'] = "Uninstallation";

		    $this->booking_model->addunitdetails($data);
		    break;

		case 'GasRecharge,':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $data['price_tags'] = "Gas Recharge";

		    $this->booking_model->update_booking_unit_details($booking_id, $data);
		    break;
		case 'Uninstallation,':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $data['price_tags'] = "Uninstallation";

		    $this->booking_model->update_booking_unit_details($booking_id, $data);
		    break;
		case 'Installation,Repair,':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $unit_details = $this->booking_model->get_unit_details($value['booking_id']);

		    $data['price_tags'] = "Repair";
		    $unit_id = $unit_details[0]['id'];

		    $this->booking_model->update_unit_details_by_id($unit_id, $data);

		    $data['booking_id'] = $value['booking_id'];
		    $data['appliance_brand'] = $value['appliance_brand'];
		    $data['appliance_category'] = $value['appliance_category'];
		    $data['appliance_capacity'] = $value['appliance_capacity'];
		    $data['model_number'] = $value['model_number'];

		    $data['appliance_tag'] = $value['appliance_tag'];
		    $data['purchase_year'] = $value['purchase_year'];
		    $data['purchase_month'] = $value['purchase_month'];
		    $data['price_tags'] = "Installation & Demo";

		    $this->booking_model->addunitdetails($data);
		    break;

		default:
		    echo $value['booking_id'];
		    break;
	    }
	}

    }

    function c_test2() {
	$booking_unit_details = $this->booking_model->c_get_all_booking_unit();
	foreach ($booking_unit_details as $key => $data) {

	    $booking = $this->booking_model->return_source($data['booking_id']);

	    $partner_id = $this->booking_model->get_price_mapping_partner_code($booking[0]['source']);

	    $prices = $this->booking_model->getPrices($data['service_id'], $data['appliance_category'], $data['appliance_capacity'], $partner_id, $data['price_tags']);

	    $state = $this->vendor_model->get_state_from_pincode($data['booking_pincode']);

	    if (empty($prices)) {
		echo $data['service_id'] . "<br/>" .
		    $data['appliance_category'] . "<br/>" .
		    $data['appliance_capacity'] . "<br/>" .
		$partner_id . "<br/>" .
		$data['price_tags'] . "<br/>" .
		$data['booking_id'] . "<br/>";

		echo "<br/><br/>";
		print_r($state['state']);
		echo "<br/><br/>";
	    } else {
		unset($data['id']);
		$data['id'] = $prices[0]['id'];

		if(empty($state)){
			echo $data['booking_pincode'];
		}

		unset($data['booking_pincode']);

		$this->booking_model->update_prices($data, $data['booking_id'], $state['state']);
	    }
	}
    }

    function c_test3() {
	$booking_details = $this->booking_model->c_getbookingid();
	foreach ($booking_details as $key => $value) {

	    $data = array();
	    $data['customer_paid_basic_charges'] = $value['service_charge'];
	    $data['customer_paid_extra_charges'] = $value['additional_service_charge'];
	    $data['customer_paid_parts'] = $value['parts_cost'];
	    $data['booking_status'] = "Completed";

	    $this->booking_model->update_unit_price($value['booking_id'], $data);
	    echo "<br/>";
	    // print_r($data);
	    echo "<br/>";
	}

	//print_r($booking_details);
    }


    function p_test1() {
	$booking_details = $this->booking_model->p_get_all_booking_id();
	//print_r($booking_details);
	foreach ($booking_details as $key => $value) {
	    $booking_id = $value['booking_id'];

	    switch ($value['price_tags']) {
		case 'Installation':
		case 'Installation,':
		case 'InstallationwithoutStand,' :
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $data['price_tags'] = "Installation & Demo";

		    /* / echo "<br/>";
		      print_r($value['price_tags']);
		      echo "<br/>";
		      print_r($data);
		      echo "<br/>"; */
		    $this->booking_model->update_booking_unit_details($booking_id, $data);
		    break;

		case 'Installation,WallMountStand,':
		case 'Installation with Stand,':
		case 'InstallationwithStand,':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $unit_details = $this->booking_model->get_unit_details($value['booking_id']);

		    $data['price_tags'] = "Installation & Demo";
		    $unit_id = $unit_details[0]['id'];

		    $this->booking_model->update_unit_details_by_id($unit_id, $data);

		    $data['booking_id'] = $value['booking_id'];
		    $data['appliance_brand'] = $value['appliance_brand'];
		    $data['appliance_category'] = $value['appliance_category'];
		    $data['appliance_capacity'] = $value['appliance_capacity'];
		    $data['model_number'] = $value['model_number'];

		    $data['appliance_tag'] = $value['appliance_tag'];
		    $data['purchase_year'] = $value['purchase_year'];
		    $data['purchase_month'] = $value['purchase_month'];
		    $data['price_tags'] = "Wall Mount Stand";

		    $this->booking_model->addunitdetails($data);
		    break;

		case 'Repair,':
		case 'Repair':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $data['price_tags'] = "Repair";

		    $this->booking_model->update_booking_unit_details($booking_id, $data);
		    break;

		case 'Visit':
		case 'Visit,':
		case 'VisitCharge,':
		case 'Visit Charge':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $data['price_tags'] = "Visit";


		    $this->booking_model->update_booking_unit_details($booking_id, $data);

		    break;

		case 'Repair,Installation,':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $unit_details = $this->booking_model->get_unit_details($value['booking_id']);

		    $data['price_tags'] = "Repair";
		    $unit_id = $unit_details[0]['id'];

		    $this->booking_model->update_unit_details_by_id($unit_id, $data);

		    $data['booking_id'] = $value['booking_id'];
		    $data['appliance_brand'] = $value['appliance_brand'];
		    $data['appliance_category'] = $value['appliance_category'];
		    $data['appliance_capacity'] = $value['appliance_capacity'];
		    $data['model_number'] = $value['model_number'];

		    $data['appliance_tag'] = $value['appliance_tag'];
		    $data['purchase_year'] = $value['purchase_year'];
		    $data['purchase_month'] = $value['purchase_month'];
		    $data['price_tags'] = "Installation & Demo";

		    $this->booking_model->addunitdetails($data);
		    break;

		case 'GasRechargewithDryer,':

		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $data['price_tags'] = "Gas Recharge with Dryer";


		    $this->booking_model->update_booking_unit_details($booking_id, $data);
		    break;

		case 'Installation,Uninstallation,':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $unit_details = $this->booking_model->get_unit_details($value['booking_id']);

		    $data['price_tags'] = "Installation & Demo";
		    $unit_id = $unit_details[0]['id'];
		    echo "<br/>";
		    $this->booking_model->update_unit_details_by_id($unit_id, $data);

		    $data['booking_id'] = $value['booking_id'];
		    $data['appliance_brand'] = $value['appliance_brand'];
		    $data['appliance_category'] = $value['appliance_category'];
		    $data['appliance_capacity'] = $value['appliance_capacity'];
		    $data['model_number'] = $value['model_number'];

		    $data['appliance_tag'] = $value['appliance_tag'];
		    $data['purchase_year'] = $value['purchase_year'];
		    $data['purchase_month'] = $value['purchase_month'];
		    $data['price_tags'] = "Uninstallation";

		    $this->booking_model->addunitdetails($data);
		    break;

		case 'GasRecharge,':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $data['price_tags'] = "Gas Recharge";

		    $this->booking_model->update_booking_unit_details($booking_id, $data);
		    break;

		case 'Uninstallation,':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $data['price_tags'] = "Uninstallation";

		    $this->booking_model->update_booking_unit_details($booking_id, $data);
		    break;

		case 'Installation,Repair,':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $unit_details = $this->booking_model->get_unit_details($value['booking_id']);

		    $data['price_tags'] = "Repair";
		    $unit_id = $unit_details[0]['id'];

		    $this->booking_model->update_unit_details_by_id($unit_id, $data);

		    $data['booking_id'] = $value['booking_id'];
		    $data['appliance_brand'] = $value['appliance_brand'];
		    $data['appliance_category'] = $value['appliance_category'];
		    $data['appliance_capacity'] = $value['appliance_capacity'];
		    $data['model_number'] = $value['model_number'];

		    $data['appliance_tag'] = $value['appliance_tag'];
		    $data['purchase_year'] = $value['purchase_year'];
		    $data['purchase_month'] = $value['purchase_month'];
		    $data['price_tags'] = "Installation & Demo";

		    $this->booking_model->addunitdetails($data);
		    break;

		default:
		    echo $value['booking_id'] . "<br/>";
		    break;
	    }
	}

//	$this->p_test2();
    }

    function p_test2() {
	$booking_unit_details = $this->booking_model->p_get_all_booking_unit();
	foreach ($booking_unit_details as $key => $data) {

	    $booking = $this->booking_model->return_source($data['booking_id']);

	    $partner_id = $this->booking_model->get_price_mapping_partner_code($booking[0]['source']);

	    $prices = $this->booking_model->getPrices($data['service_id'], $data['appliance_category'], $data['appliance_capacity'], $partner_id, $data['price_tags']);

	    $state = $this->vendor_model->get_state_from_pincode($data['booking_pincode']);

	    if (empty($prices)) {
		echo $data['service_id'] . "<br/>" .
		$data['appliance_category'] . "<br/>" .
		$data['appliance_capacity'] . "<br/>" .
		$partner_id . "<br/>" .
		$data['price_tags'] . "<br/>" .
		$data['booking_id'] . "<br/>";

		echo "<br/><br/>";
		print_r($state['state']);
		echo "<br/><br/>";
	    } else {
		unset($data['id']);
		$data['id'] = $prices[0]['id'];

		if (empty($state)) {
		    echo $data['booking_pincode'];
		}

		unset($data['booking_pincode']);

		$this->booking_model->update_prices($data, $data['booking_id'], $state['state']);
	    }
	}
    }

    function test_upload(){
    	$this->booking_model->test_upload();
    }

}
