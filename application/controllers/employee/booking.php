<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

define('Partner_Integ_Complete', TRUE);

class Booking extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
	parent::__Construct();
	$this->load->model('employee_model');
	$this->load->model('booking_model');
	$this->load->model('user_model');
	$this->load->model('vendor_model');
	$this->load->model('filter_model');
	$this->load->model('partner_model');
	$this->load->model('invoices_model');

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
     * @desc : This function will load booking and Add booking
     * @param: void
     * @return : print Booking on Booking Page
     */
    function addbooking() {
	//$results['service'] = $this->filter_model->getserviceforfilter();
	//$results['agent'] = $this->filter_model->getagent();

	//$employee_id = $this->session->userdata('employee_id');

	//$results['one'] = $this->employee_model->verifylist($employee_id, '0');
	//$results['three'] = $this->employee_model->verifylist($employee_id, '2');
	//$results['forteen'] = $this->employee_model->verifylist($employee_id, '14');

	$results['user_id'] = $this->input->post('user_id');
	$results['home_address'] = $this->input->post('home_address');
	$results['user_email'] = $this->input->post('user_email');
	$results['city'] = $this->input->post('city');
	$results['state'] = $this->input->post('state');
	$results['phone_number'] = $this->input->post('phone_number');
	$results['alternate_phone_number'] = $this->input->post('alternate_phone_number');
	$results['pincode'] = $this->input->post('pincode');
	$results['name'] = $this->input->post('name');

	$results['reason'] = $this->booking_model->cancelreason();
	$results['services'] = $this->booking_model->selectservice();
	$results['sources'] = $this->booking_model->select_booking_source();

	$this->load->view('employee/header', $results);
	$this->load->view('employee/addbooking');
    }

    public function index() {
	$validation = true; // $this->checkValidation();

	if ($validation) {
	    $booking['type'] = $this->input->post('type');
	    $booking['source'] = $this->input->post('source');
	    $booking['city'] = $this->input->post('city');
	    $booking['state'] = $this->input->post('state');
	    $booking['quantity'] = $this->input->post('quantity');
	    $booking['appliance_brand1'] = $this->input->post('appliance_brand1');
	    $booking['appliance_category1'] = $this->input->post('appliance_category1');
	    $booking['appliance_capacity1'] = $this->input->post('appliance_capacity1');
	    $booking['items_selected1'] = $this->input->post('items_selected1');
	    $booking['total_price1'] = $this->input->post('total_price1');
	    $booking['model_number1'] = $this->input->post('model_number1');
	    $booking['appliance_tags1'] = $this->input->post('appliance_tags1');
	    $booking['purchase_year1'] = $this->input->post('purchase_year1');
	    $booking['potential_value'] = $this->input->post('potential_value');
	    $booking['appliance_brand2'] = $this->input->post('appliance_brand2');
	    $booking['appliance_category2'] = $this->input->post('appliance_category2');
	    $booking['appliance_capacity2'] = $this->input->post('appliance_capacity2');
	    $booking['items_selected2'] = $this->input->post('items_selected2');
	    $booking['total_price2'] = $this->input->post('total_price2');
	    $booking['model_number2'] = $this->input->post('model_number2');
	    $booking['appliance_tags2'] = $this->input->post('appliance_tags2');
	    $booking['purchase_year2'] = $this->input->post('purchase_year2');
	    if ($booking['total_price2'] == '') {
		$booking['appliance_brand2'] = " ";
		$booking['appliance_category2'] = " ";
		$booking['appliance_capacity2'] = " ";
	    }
	    $booking['appliance_brand3'] = $this->input->post('appliance_brand3');
	    $booking['appliance_category3'] = $this->input->post('appliance_category3');
	    $booking['appliance_capacity3'] = $this->input->post('appliance_capacity3');
	    $booking['items_selected3'] = $this->input->post('items_selected3');
	    $booking['total_price3'] = $this->input->post('total_price3');
	    $booking['model_number3'] = $this->input->post('model_number3');
	    $booking['appliance_tags3'] = $this->input->post('appliance_tags3');
	    $booking['purchase_year3'] = $this->input->post('purchase_year3');
	    if ($booking['total_price3'] == '') {
		$booking['appliance_brand3'] = " ";
		$booking['appliance_category3'] = " ";
		$booking['appliance_capacity3'] = " ";
	    }
	    $booking['appliance_brand4'] = $this->input->post('appliance_brand4');
	    $booking['appliance_category4'] = $this->input->post('appliance_category4');
	    $booking['appliance_capacity4'] = $this->input->post('appliance_capacity4');
	    $booking['items_selected4'] = $this->input->post('items_selected4');
	    $booking['total_price4'] = $this->input->post('total_price4');
	    $booking['model_number4'] = $this->input->post('model_number4');
	    $booking['appliance_tags4'] = $this->input->post('appliance_tags4');
	    $booking['purchase_year4'] = $this->input->post('purchase_year4');
	    if ($booking['total_price4'] == '') {
		$booking['appliance_brand4'] = " ";
		$booking['appliance_category4'] = " ";
		$booking['appliance_capacity4'] = " ";
	    }
	    $booking['user_id'] = $this->input->post('user_id');
	    $foremail['phone_number'] = $this->input->post('booking_primary_contact_no');
	    $foremail['user_email'] = $this->input->post('user_email');
	    $foremail['name'] = $this->input->post('name');

	    $booking['booking_primary_contact_no'] = $this->input->post('booking_primary_contact_no');
	    $booking['booking_alternate_contact_no'] = $this->input->post('booking_alternate_contact_no');
	    $booking['service_id'] = $this->input->post('service_id');
	    $booking['booking_date'] = $this->input->post('booking_date');
	    $booking['booking_timeslot'] = $this->input->post('booking_timeslot');
	    $booking['booking_remarks'] = $this->input->post('booking_remarks');
	    $booking['query_remarks'] = $this->input->post('query_remarks');
	    $booking['booking_address'] = $this->input->post('booking_address');

	    $booking['booking_pincode'] = $this->input->post('booking_pincode');
	    $booking['amount_due'] = $booking['total_price1'] + $booking['total_price2'] + $booking['total_price3'] + $booking['total_price4'];
	    $booking['create_date'] = date("Y-m-d h:i:s");
	    $booking['booking_date'] = date('d-m-Y', strtotime($booking['booking_date']));
	    $yy = date("y", strtotime($booking['booking_date']));
	    $mm = date("m", strtotime($booking['booking_date']));
	    $dd = date("d", strtotime($booking['booking_date']));
	    if ($booking['type'] == "Query") {
		$booking['booking_id'] = str_pad($booking['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
		$booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($booking['user_id'])) + 1);
		//Add source
		$booking['booking_id'] = $booking['source'] . "-" . $booking['booking_id'];
		$add = "Q-";
		$booking['booking_id'] = $add . $booking['booking_id'];
		$booking['current_status'] = "FollowUp";
		$booking['internal_status'] = "FollowUp";
		$booking['type'] = "Query";
		$booking['total_price1'] = 0;
		$booking['total_price2'] = 0;
		$booking['total_price3'] = 0;
		$booking['total_price4'] = 0;
	    } else {
		$booking['booking_id'] = str_pad($booking['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
		$booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($booking['user_id'])) + 1);
		//Add source
		$booking['booking_id'] = $booking['source'] . "-" . $booking['booking_id'];

		$booking['type'] = "Booking";
		$booking['current_status'] = "Pending";
		$booking['internal_status'] = "Scheduled";
	    }
	    $appliance_id = $this->booking_model->addappliancedetails($booking);
	    $this->booking_model->addunitdetails($booking);

	    $output = $this->booking_model->addbooking($booking, $appliance_id[0]['id'], $booking['city'], $booking['state']);

	    $query1 = $this->booking_model->booking_history_by_booking_id($booking['booking_id']);
	    $query2 = $this->booking_model->get_unit_details($booking['booking_id']);

	    $months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

	    $mm = $months[$mm - 1];

	    $booking['booking_date'] = $dd . $mm;

	    if ($booking['booking_timeslot'] == "10AM-1PM") {
		$booking['booking_timeslot'] = "1PM";
	    } elseif ($booking['booking_timeslot'] == "1PM-4PM") {
		$booking['booking_timeslot'] = "4PM";
	    } elseif ($booking['booking_timeslot'] == "4PM-7PM") {
		$booking['booking_timeslot'] = "7PM";
	    }

	    //-------Sending Email On Booking--------//
	    if ($booking['current_status'] != "FollowUp") {
		$message = "Congratulations You have received new booking, details are mentioned below:
      <br>Customer Name: " . $query1[0]['name'] . "<br>Customer Phone Number: " . $query1[0]['phone_number'] .
		    "<br>Customer email address: " . $query1[0]['user_email'] . "<br>Booking Id: " .
		    $booking['booking_id'] . "<br>Service name:" . $query1[0]['services'] .
		    "<br>Number of appliance: " . $booking['quantity'] . "<br>Booking Date: " .
		    $booking['booking_date'] . "<br>Booking Timeslot: " . $booking['booking_timeslot'] .
		    "<br>Amount Due: " . $booking['amount_due'] . "<br>Your Booking Remark is: " .
		    $booking['booking_remarks'] . "<br>Booking address: " . $booking['booking_address'] .
		    "<br>Booking city: " . $booking['city'] .
		    "<br>Booking pincode: " . $booking['booking_pincode'] . "<br><br>
        Appliance Details:<br>";

		$appliance = "";
		for ($i = 0; $i < $booking['quantity']; $i++) {

		    $appliance = "<br>Brand : " . $query2[$i]['appliance_brand'] . "<br>Category : " .
			$query2[$i]['appliance_category'] . "<br>Capacity : " . $query2[$i]['appliance_capacity'] .
			"<br>Selected service/s is/are: " . $query2[$i]['price_tags'] . "<br>Total price is: " .
			$query2[$i]['total_price'] . "<br>";
		    $message = $message . $appliance;
		}
		$message = $message . "<br> Thanks!!";

		$from = "booking@247around.com";
		$to = "anuj@247around.com, nits@247around.com";
		$cc = "";
		$bcc = "";
		$subject = 'Booking Confirmation-AROUND';
		$attachment ="";
//		$this->sendMail($subject, $message, $to, $cc, $bcc);
		$this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment);
		//-------Sending SMS on booking--------//

		$smsBody = "Got it! Request for " . $query1[0]['services'] . " Repair is confirmed for " .
		    $booking['booking_date'] . ", " . $booking['booking_timeslot'] .
		    ". 247Around Indias 1st Multibrand Appliance repair App goo.gl/m0iAcS. 011-39595200";

		    $this->notify->sendTransactionalSms($query1[0]['phone_number'], $smsBody);
	    }
	    //------End of sending SMS--------//

	    redirect(base_url() . 'employee/booking/view', 'refresh');
	}
    }

    /**
     *  @desc : This function is for booking confirmation
     *  @param : void
     *  @return : all the booking details
     */
    function bookingconfirmation() {
	$booking['user_id'] = $this->input->post('user_id');
	$foremail['phone_number'] = $this->input->post('booking_primary_contact_no');
	$foremail['user_email'] = $this->input->post('user_email');
	$foremail['name'] = $this->input->post('name');
	$booking['city'] = $this->input->post('booking_city');
    $booking['state'] = $this->input->post('booking_state');

	$booking['newbrand1'] = $this->input->post('newbrand1');
	$booking['newbrand2'] = $this->input->post('newbrand2');
	$booking['newbrand3'] = $this->input->post('newbrand3');
	$booking['newbrand4'] = $this->input->post('newbrand4');
	//For future use, i.e. for multiple appliances
	//$booking['potential_value2'] = $this->input->post('potential_value2');
	//$booking['potential_value3'] = $this->input->post('potential_value3');
	//$booking['potential_value4'] = $this->input->post('potential_value4');
	$booking['booking_primary_contact_no'] = $this->input->post('booking_primary_contact_no');
	$booking['booking_alternate_contact_no'] = $this->input->post('booking_alternate_contact_no');
	$booking['service_id'] = $this->input->post('service_id');
	$booking['booking_date'] = $this->input->post('booking_date');
	$booking['type'] = $this->input->post('type');
	$booking['source'] = $this->input->post('source_code');
	$booking['query_remarks'] = $this->input->post('query_remarks');
	$yy = date("y", strtotime($booking['booking_date']));
	$mm = date("m", strtotime($booking['booking_date']));
	$dd = date("d", strtotime($booking['booking_date']));
	if ($booking['type'] == "Query") {    //For Query
	    $booking['booking_id'] = "";
	    $booking['current_status'] = "FollowUp";
	    $booking['type'] = "Query";
	    $booking['booking_timeslot'] = $this->input->post('booking_timeslot');
	    $booking['quantity'] = $this->input->post('quantity');
	    $booking['booking_remarks'] = $this->input->post('booking_remarks');
	    $booking['booking_address'] = $this->input->post('booking_address');
	    $booking['booking_pincode'] = $this->input->post('booking_pincode');
	    $booking['potential_value'] = $this->input->post('potential_value1');
	    if ($booking['newbrand1'] != "") {
		$booking['appliance_brand1'] = $booking['newbrand1'];

		$this->booking_model->addNewApplianceBrand($booking['service_id'], $booking['newbrand1']);
	    } else {
		$booking['appliance_brand1'] = $this->input->post('appliance_brand1');
	    }
	    $booking['appliance_category1'] = $this->input->post('appliance_category1');
	    $booking['appliance_capacity1'] = $this->input->post('appliance_capacity1');
	    $booking['items_selected1'] = $this->input->post('items_selected1');
	    $booking['model_number1'] = $this->input->post('model_number1');
	    $booking['appliance_tags1'] = $this->input->post('appliance_tags1');
	    $booking['purchase_year1'] = $this->input->post('purchase_year1');
	    if ($booking['newbrand2'] != "") {
		$booking['appliance_brand2'] = $booking['newbrand2'];
		$this->booking_model->addNewApplianceBrand($booking['service_id'], $booking['newbrand2']);
	    } else {
		$booking['appliance_brand2'] = $this->input->post('appliance_brand2');
	    }
	    $booking['appliance_category2'] = $this->input->post('appliance_category2');
	    $booking['appliance_capacity2'] = $this->input->post('appliance_capacity2');
	    $booking['items_selected2'] = $this->input->post('items_selected2');
	    $booking['model_number2'] = $this->input->post('model_number2');
	    $booking['appliance_tags2'] = $this->input->post('appliance_tags2');
	    $booking['purchase_year2'] = $this->input->post('purchase_year2');
	    if ($booking['quantity'] <= 1) {
		$booking['appliance_brand2'] = " ";
		$booking['appliance_category2'] = " ";
		$booking['appliance_capacity2'] = " ";
	    }
	    if ($booking['newbrand3'] != "") {
		$booking['appliance_brand3'] = $booking['newbrand3'];
		$this->booking_model->addNewApplianceBrand($booking['service_id'], $booking['newbrand3']);
	    } else {
		$booking['appliance_brand3'] = $this->input->post('appliance_brand3');
	    }
	    $booking['appliance_category3'] = $this->input->post('appliance_category3');
	    $booking['appliance_capacity3'] = $this->input->post('appliance_capacity3');
	    $booking['items_selected3'] = $this->input->post('items_selected3');
	    $booking['model_number3'] = $this->input->post('model_number3');
	    $booking['appliance_tags3'] = $this->input->post('appliance_tags3');
	    $booking['purchase_year3'] = $this->input->post('purchase_year3');
	    if ($booking['quantity'] <= 2) {
		$booking['appliance_brand3'] = " ";
		$booking['appliance_category3'] = " ";
		$booking['appliance_capacity3'] = " ";
	    }
	    if ($booking['newbrand4'] != "") {
		$booking['appliance_brand4'] = $booking['newbrand4'];
		$this->booking_model->addNewApplianceBrand($booking['service_id'], $booking['newbrand4']);
	    } else {
		$booking['appliance_brand4'] = $this->input->post('appliance_brand4');
	    }
	    $booking['appliance_category4'] = $this->input->post('appliance_category4');
	    $booking['appliance_capacity4'] = $this->input->post('appliance_capacity4');
	    $booking['items_selected4'] = $this->input->post('items_selected4');
	    $booking['model_number4'] = $this->input->post('model_number4');
	    $booking['appliance_tags4'] = $this->input->post('appliance_tags4');
	    $booking['purchase_year4'] = $this->input->post('purchase_year4');
	    if ($booking['quantity'] <= 3) {
		$booking['appliance_brand4'] = " ";
		$booking['appliance_category4'] = " ";
		$booking['appliance_capacity4'] = " ";
	    }
	    $booking['total_price1'] = 0;
	    $booking['total_price2'] = 0;
	    $booking['total_price3'] = 0;
	    $booking['total_price4'] = 0;
	} else { //For Booking
	    $booking['booking_id'] = str_pad($booking['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
	    $booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($booking['user_id'])) + 1);

	    $booking['type'] = "Pending";
	    $booking['current_status'] = "Pending";
	    $booking['potential_value'] = 0;
	    $booking['booking_timeslot'] = $this->input->post('booking_timeslot');
	    $booking['quantity'] = $this->input->post('quantity');
	    $booking['booking_remarks'] = $this->input->post('booking_remarks');
	    $booking['booking_address'] = $this->input->post('booking_address');
	    $booking['booking_pincode'] = $this->input->post('booking_pincode');
	    $booking['appliance_brand1'] = $this->input->post('appliance_brand1');
	    if ($booking['newbrand1'] != "") {
		$booking['appliance_brand1'] = $booking['newbrand1'];
		$this->booking_model->addNewApplianceBrand($booking['service_id'], $booking['newbrand1']);
	    }
	    $booking['appliance_category1'] = $this->input->post('appliance_category1');
	    $booking['appliance_capacity1'] = $this->input->post('appliance_capacity1');
	    $booking['items_selected1'] = $this->input->post('items_selected1');
	    $booking['total_price1'] = $this->input->post('total_price1');
	    $booking['model_number1'] = $this->input->post('model_number1');
	    $booking['appliance_tags1'] = $this->input->post('appliance_tags1');
	    $booking['purchase_year1'] = $this->input->post('purchase_year1');
	    $booking['appliance_brand2'] = $this->input->post('appliance_brand2');
	    $booking['appliance_category2'] = $this->input->post('appliance_category2');
	    $booking['appliance_capacity2'] = $this->input->post('appliance_capacity2');
	    $booking['items_selected2'] = $this->input->post('items_selected2');
	    $booking['total_price2'] = $this->input->post('total_price2');
	    $booking['model_number2'] = $this->input->post('model_number2');
	    $booking['appliance_tags2'] = $this->input->post('appliance_tags2');
	    $booking['purchase_year2'] = $this->input->post('purchase_year2');
	    if ($booking['total_price2'] == '') {
		$booking['appliance_brand2'] = " ";
		$booking['appliance_category2'] = " ";
		$booking['appliance_capacity2'] = " ";
	    } elseif ($booking['newbrand2'] != "") {
		$booking['appliance_brand2'] = $booking['newbrand2'];
		$this->booking_model->addNewApplianceBrand($booking['service_id'], $booking['newbrand2']);
	    }
	    $booking['appliance_brand3'] = $this->input->post('appliance_brand3');
	    $booking['appliance_category3'] = $this->input->post('appliance_category3');
	    $booking['appliance_capacity3'] = $this->input->post('appliance_capacity3');
	    $booking['items_selected3'] = $this->input->post('items_selected3');
	    $booking['total_price3'] = $this->input->post('total_price3');
	    $booking['model_number3'] = $this->input->post('model_number3');
	    $booking['appliance_tags3'] = $this->input->post('appliance_tags3');
	    $booking['purchase_year3'] = $this->input->post('purchase_year3');
	    if ($booking['total_price3'] == '') {
		$booking['appliance_brand3'] = " ";
		$booking['appliance_category3'] = " ";
		$booking['appliance_capacity3'] = " ";
	    } elseif ($booking['newbrand3'] != "") {
		$booking['appliance_brand3'] = $booking['newbrand3'];
		$this->booking_model->addNewApplianceBrand($booking['service_id'], $booking['newbrand3']);
	    }
	    $booking['appliance_brand4'] = $this->input->post('appliance_brand4');
	    $booking['appliance_category4'] = $this->input->post('appliance_category4');
	    $booking['appliance_capacity4'] = $this->input->post('appliance_capacity4');
	    $booking['items_selected4'] = $this->input->post('items_selected4');
	    $booking['total_price4'] = $this->input->post('total_price4');
	    $booking['model_number4'] = $this->input->post('model_number4');
	    $booking['appliance_tags4'] = $this->input->post('appliance_tags4');
	    $booking['purchase_year4'] = $this->input->post('purchase_year4');
	    if ($booking['total_price4'] == '') {
		$booking['appliance_brand4'] = " ";
		$booking['appliance_category4'] = " ";
		$booking['appliance_capacity4'] = " ";
	    } elseif ($booking['newbrand4'] != "") {
		$booking['appliance_brand4'] = $booking['newbrand4'];
		$this->booking_model->addNewApplianceBrand($booking['service_id'], $booking['newbrand4']);
	    }
	}
	$booking['amount_due'] = $booking['total_price1'] + $booking['total_price2'] + $booking['total_price3'] + $booking['total_price4'];
	$booking['create_date'] = date("Y-m-d h:i:s");

	$result = $this->booking_model->service_name($booking['service_id']);

	$booking_source = $this->booking_model->get_booking_source($booking['source']);

	$this->load->view('employee/header');
	$this->load->view('employee/bookingconfirmation', array('booking' => $booking, 'result' => $result,
	    'booking_source' => $booking_source[0]));
    }

    function loadViews($output) {
	$data['sucess'] = $output;
	$results['service'] = $this->filter_model->getserviceforfilter();
	$results['agent'] = $this->filter_model->getagent();
	$employee_id = $this->session->userdata('employee_id');
	$results['one'] = $this->employee_model->verifylist($employee_id, '0');
	$results['three'] = $this->employee_model->verifylist($employee_id, '2');
	$results['forteen'] = $this->employee_model->verifylist($employee_id, '14');
	$this->load->view('employee/header', $results);
	//$this->load->view('employee/addbooking',$data);
	$this->load->view('employee/bookinghistory');
    }

    /**
     *  @desc : This function for check validation
     *  @param : void
     *  @return : tue if validation true otherwise FALSE
     */
    public function checkValidation() {
	$this->form_validation->set_rules('user_id', 'user_id', 'required');

	$this->form_validation->set_rules('service_id', 'service_id', 'required');
	$this->form_validation->set_rules('booking_date', 'booking_date', 'required');
	$this->form_validation->set_rules('booking_timeslot', 'booking_timeslot', 'required');
	$this->form_validation->set_rules('appliance_brand', 'appliance_brand', 'required');
	$this->form_validation->set_rules('appliance_category', 'appliance_category', 'required');
	$this->form_validation->set_rules('appliance_capacity', 'appliance_capacity', 'required');
	$this->form_validation->set_rules('quantity', 'quantity', 'required');

	$this->form_validation->set_error_delimiters('<div class="error" role="alert">', '</div>');
	if ($this->form_validation->run() == FALSE) {
	    return FALSE;
	} else {
	    return true;
	}
    }

    /**
     *  @desc : This function displays list of bookings
     *  @param : void
     *  @return : all the bookings to view
     */
    function viewbooking($offset = 0, $page = 0) {
	$query = $this->booking_model->viewbooking();

	$data['Bookings'] = null;

	if ($query) {
	    $data['Bookings'] = $query;
	}

	$this->load->view('employee/header');

	$this->load->view('employee/booking', $data);
    }

    //Function to view all pending bookings when you select All from pagination
    function view_all_pending_booking() {
	$query = $this->booking_model->viewallpendingbooking();

	$data['Bookings'] = null;

	if ($query) {
	    $data['Bookings'] = $query;
	}

	$this->load->view('employee/header');

	$this->load->view('employee/booking', $data);
    }

    /**
     *  @desc : This function displays list of pending bookings according to pagination
     *  @param : Starting page & number of results per page
     *  @return : pending bookings according to pagination
     */
    function view($offset = 0, $page = 0, $booking_id ="") {
	if ($page == 0) {
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

    //Function to view all completed bookings when you select All from pagination
    function viewallcompletedbooking() {
	$query = $this->booking_model->view_all_completed_booking();

	$data['Bookings'] = null;

	if ($query) {
	    $data['Bookings'] = $query;
	}

	$this->load->view('employee/header');

	$this->load->view('employee/viewcompletedbooking', $data);
    }


    //Function to view all cancelled bookings when you select All from pagination
    function viewallcancelledbooking() {
	$query = $this->booking_model->view_all_cancelled_booking();

	$data['Bookings'] = null;

	if ($query) {
	    $data['Bookings'] = $query;
	}

	$this->load->view('employee/header');

	$this->load->view('employee/viewcancelledbooking', $data);
    }

    /**
     *  @desc : This function displays list of completed bookings according to pagination
     *  @param : Starting page & number of results per page
     *  @return : completed bookings according to pagination
     */
    function viewcompletedbooking($offset = 0, $page = 0) {
	if ($page == 0) {
	    $page = 50;
	}

	$offset = ($this->uri->segment(4) != '' ? $this->uri->segment(4) : 0);
	$config['base_url'] = base_url() . 'employee/booking/viewcompletedbooking';
	$config['total_rows'] = $this->booking_model->total_completed_booking();
	$config['per_page'] = $page;
	$config['uri_segment'] = 4;
	$config['first_link'] = 'First';
	$config['last_link'] = 'Last';

	$this->pagination->initialize($config);
	$data['links'] = $this->pagination->create_links();

	$data['Bookings'] = $this->booking_model->view_completed_booking($config['per_page'], $offset);
	$this->load->view('employee/header');

	$this->load->view('employee/viewcompletedbooking', $data);
    }


    /**
     *  @desc : This function displays list of cancelled bookings according to pagination
     *  @param : Starting page & number of results per page
     *  @return : cancelled bookings according to pagination
     */
    function viewcancelledbooking($offset = 0, $page = 0) {
	if ($page == 0) {
	    $page = 50;
	}

	$offset = ($this->uri->segment(4) != '' ? $this->uri->segment(4) : 0);
	$config['base_url'] = base_url() . 'employee/booking/viewcancelledbooking';
	$config['total_rows'] = $this->booking_model->total_completed_booking();
	$config['per_page'] = $page;
	$config['uri_segment'] = 4;
	$config['first_link'] = 'First';
	$config['last_link'] = 'Last';

	$this->pagination->initialize($config);
	$data['links'] = $this->pagination->create_links();

	$data['Bookings'] = $this->booking_model->view_cancelled_booking($config['per_page'], $offset);
	$this->load->view('employee/header');

	$this->load->view('employee/viewcancelledbooking', $data);
    }

    /**
     *  @desc : This function selects all the services
     *  @param : void
     *  @return : all the services to view
     */
    function ServiceSelect() {
	$query = $this->booking_model->selectservice();

	$data['Services'] = null;

	if ($query) {
	    $data['Services'] = $query;
	}

	$this->load->view('employee/addbooking', $data);
    }

    /**
     *  @desc : This function selects the brands present in applience_brand table
     *  @param : void
     *  @return : all the brands to view
     */
    function brandselect() {
	$query = $this->booking_model->selectbrand();

	$data['brands'] = null;

	if ($query) {
	    $data['brands'] = $query;
	}

	$this->load->view('employee/addbooking', $data);
    }

    /**
     *  @desc : This function displays all the categoties present
     *  @param : void
     *  @return : all the categories to view
     */
    function categoryselect() {
	$query = $this->booking_model->selectcategory();

	$data['category'] = null;

	if ($query) {
	    $data['category'] = $query;
	}

	$this->load->view('employee/addbooking', $data);
    }

    /**
     *  @desc : This function displays user details
     *  @param : phone number
     *  @return : the details of particular user
     */
    function finduser($phone) {
	$query = $this->booking_model->finduser($phone);
    }

    /**
     *  @desc : This function the cancelation reason for booking
     *  @param : void
     *  @return : all the cancilation reasons present in the database
     */
    function cancelreason() {


	$query = $this->booking_model->cancelreason();

	$data['reason'] = null;

	if ($query) {
	    $data['reason'] = $query;
	}

	$this->load->view('employee/cancelbooking', $data);
    }

    /**
     *  @desc : This function is to select to booking to be completed
     *  @param : booking id
     *  @return : user details and booking history to view
     */
    function get_complete_booking_form($booking_id) {

	$getbooking = $this->booking_model->getbooking($booking_id);
	$query2 = $this->booking_model->get_unit_details($booking_id);
	if ($getbooking) {

	    $data['booking_id'] = $getbooking;

	    $query = $this->booking_model->booking_history_by_booking_id($booking_id);
	    $page = "Complete";
	    $internal_status = $this->booking_model->get_internal_status($page);
	    $vendor_details = $this->booking_model->get_booking_vendor_details($getbooking[0]['assigned_vendor_id']);
	    $data1['booking_id'] = $query;	    

	    $this->load->view('employee/header');
	    $this->load->view('employee/completebooking', array('data' => $data,
		'data1' => $data1,
		'internal_status' => $internal_status,
		'query2' => $query2,
		'vendor_details' => $vendor_details));
	} else {
	    echo "This Id doesn't Available";
	}
    }

    /**
     *  @desc : This function is to complete the booking
     *  @param : booking id
     *  @return : completes the booking and load view
     */
    function process_complete_booking_form($booking_id) {
	$data['closing_remarks'] = $this->input->post('closing_remarks');
	$data['service_charge'] = $this->input->post('service_charge');
	$data['service_charge_collected_by'] = $this->input->post('service_charge_collected_by');
	$data['additional_service_charge'] = $this->input->post('additional_service_charge');
	$data['additional_service_charge_collected_by'] = $this->input->post('additional_service_charge_collected_by');
	$data['parts_cost'] = $this->input->post('parts_cost');
	$data['parts_cost_collected_by'] = $this->input->post('parts_cost_collected_by');
	$data['amount_paid'] = $data['service_charge'] + $data['parts_cost'] + $data['additional_service_charge'];
	$data['internal_status'] = $this->input->post('internal_status');
	$data['rating_star'] = $this->input->post('rating_star');
	$data['rating_comments'] = $this->input->post('rating_comments');
	$data['vendor_name'] = $this->input->post('vendor_name');
	$data['vendor_city'] = $this->input->post('vendor_city');
	$data['vendor_rating_stars'] = $this->input->post('vendor_rating_star');
	$data['vendor_rating_comments'] = $this->input->post('vendor_rating_comments');
	
	if ($data['rating_star'] === "Select" && $data['rating_comments'] == '') {
	    $data['rating_star'] = "";
	    $data['rating_comments'] = "";
	}
	$data['closed_date'] = date("Y-m-d h:i:s");

	$this->booking_model->complete_booking($booking_id, $data);

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
		$sd_where = array("CRM_Remarks_SR_No" => $booking_id);
		$sd_data = array(
		    "Status_by_247around" => "Completed",
		    "Remarks_by_247around" => $data['internal_status'],
		    "Rating_Stars" => $data['rating_star'],
		    "update_date" => $data['closed_date']
		);
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
	
	$from = "booking@247around.com";
	$to = "anuj@247around.com, nits@247around.com";
	$cc = "";
	$bcc = "";
	$subject = 'Booking Completion-AROUND';
	$message = "Booking Completion.<br>Customer name: " . $query1[0]['name'] . "<br>Customer phone number: " . $query1[0]['phone_number'] . "<br>Customer email: " . $query1[0]['user_email'] . "<br>Booking Id is: " . $query1[0]['booking_id'] . "<br>Your service name is:" . $query1[0]['services'] . "<br>Booking date: " . $query1[0]['booking_date'] . "<br>Booking completion date: " . $data['closed_date'] . "<br>Amount paid for the booking: " . $data['amount_paid'] . "<br>Your booking completion remark is: " . $data['closing_remarks'] . "<br>Vendor name:" . $data['vendor_name'] . "<br>Vendor city:" . $data['vendor_city'] . "<br>Thanks!!";
	$attachment ="";	
//	$this->sendMail($subject, $message, $to, $cc, $bcc);
	$this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment);
	//------End of sending email--------//
	//------Send SMS on Completion of booking-----//
	if ($is_sd == FALSE) {
	    $smsBody = "Your request for " . $query1[0]['services'] . " Repair completed. Like us on Facebook goo.gl/Y4L6Hj For discounts download app goo.gl/m0iAcS. For feedback call 011-39595200.";
	    $this->notify->sendTransactionalSms($query1[0]['phone_number'], $smsBody);

	}

	//-------End of send SMS-----------//


	redirect(base_url() . 'employee/booking/view', 'refresh');
    }

    /**
     *  @desc : This function is to select to booking to be canceled
     *  @param : booking id
     *  @return : user details and booking history to view
     */
    function get_cancel_booking_form($booking_id) {	    
	    $data['user_and_booking_details'] = $this->booking_model->booking_history_by_booking_id($booking_id);

	    $data['reason'] = $this->booking_model->cancelreason();
	    $this->load->view('employee/header');
	    $this->load->view('employee/cancelbooking', $data);	
    }

    /**
     *  @desc : This function is to cancels the booking
     *  @param : booking id
     *  @return : cancels the booking and load view
     */
    function process_cancel_booking_form($booking_id) {
	$data['cancellation_reason'] = $this->input->post('cancellation_reason');

	$data['update_date'] = date("Y-m-d h:i:s");
	$data['closed_date'] = date("Y-m-d h:i:s");

	if ($data['cancellation_reason'] === 'Other') {
	    $data['cancellation_reason'] = "Other : " . $this->input->post("cancellation_reason_text");
	}
	$data['current_status'] = "Cancelled";
	$data['internal_status'] = "Cancelled";
	
	$insertData = $this->booking_model->cancel_booking($booking_id, $data);

	 //Update SD leads table if required
//$this->booking_model->update_sd_lead_status($booking_id, 'Cancelled');
        //Is this SD booking?
        if (strpos($booking_id, "SS") !== FALSE) {
            $is_sd = TRUE;
        } else {
            $is_sd = FALSE;
        }

        if ($is_sd) {
            if ($this->booking_model->check_sd_lead_exists_by_booking_id($booking_id) === TRUE) {
                $sd_where = array("CRM_Remarks_SR_No" => $booking_id);
                $sd_data = array(
                    "Status_by_247around" => $data['current_status'],
                    "Remarks_by_247around" => $data['internal_status'],
                    "update_date" => $data['update_date']
                );
                $this->booking_model->update_sd_lead($sd_where, $sd_data);
            } else {
                if (Partner_Integ_Complete) {
                    //Update Partner leads table
                    $partner_where = array("247aroundBookingID" => $booking_id);
                    $partner_data = array(
                        "247aroundBookingStatus" => $data['current_status'],
                        "247aroundBookingRemarks" => $data['internal_status'],
                        "update_date" => $data['update_date']
                    );
                $this->partner_model->update_partner_lead($partner_where, $partner_data);

                    //Call relevant partner API
                    //TODO: make it dynamic, use service object model (interfaces)
                    $partner_cb_data = array_merge($partner_where, $partner_data);
                $this->partner_sd_cb->update_status_cancel_booking($partner_cb_data);
                }
            }
        }

        $query1 = $this->booking_model->booking_history_by_booking_id($booking_id, "join");

		//------------Sending Email----------//	
	
	$from = "booking@247around.com";
	$to = "anuj@247around.com, nits@247around.com";
	$cc = "";
	$bcc = "";
	$subject = 'Booking Cancellation-AROUND';
	$message = "Booking Cancellation:<br>Customer name: " . $query1[0]['name'] . "<br>Customer phone number: " .
	    $query1[0]['phone_number'] . "<br>Customer email: " . $query1[0]['user_email'] . "<br>Booking Id: " .
	    $query1[0]['booking_id'] . "<br>Service name is:" . $query1[0]['services'] . "<br>Booking date was: " .
	    $query1[0]['booking_date'] . "<br>Booking timeslot was: " . $query1[0]['booking_timeslot'] .
	    "<br>Booking cancellation date is: " . $data['update_date'] . "<br>Booking cancellation reason: " .
	    $data['cancellation_reason'] . "<br>Vendor name:" . $query1[0]['vendor_name'] . "<br>Vendor city:" .
	    $query1[0]['city'] ."<br> Thanks!!";
	$attachment ="";
//	$this->sendMail($subject, $message, $to, $cc, $bcc);
	$this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment);
	//------End of sending email--------//
	//------------Send SMS for cancellation---------//
	if ($is_sd == FALSE) {
	    $smsBody = "Your request for " . $query1[0]['services'] . " Repair is cancelled. For discounts download app 247Around goo.gl/m0iAcS. Like us on Facebook goo.gl/Y4L6Hj. 011-39595200";
	    $this->notify->sendTransactionalSms($query1[0]['phone_number'], $smsBody);
	}

	log_message('info','Booking Status Change- Booking id: '. $booking_id. " Cancelled By ". $this->session->userdata('employee_id'));


	//---------End of sending SMS----------//
	redirect(base_url() . 'employee/booking/view');
    }

    /**
     *  @desc : This function is to select to booking to be rescheduled
     *  @param : booking id
     *  @return : user details and booking history to view
     */
    function get_reschedule_booking_form($booking_id) {
	$getbooking = $this->booking_model->getbooking($booking_id);

	if ($getbooking) {
	    $employee_id = $this->session->userdata('employee_id');

	    $data['booking_id'] = $getbooking;

	    $query = $this->booking_model->booking_history_by_booking_id($booking_id);

	    $data1['booking_id'] = $query;

	    $this->load->view('employee/header');
	    $this->load->view('employee/reschedulebooking', array('data' => $data, 'data1' => $data1));
	} else {
	    echo "This Id doesn't Exists";
	}
    }

    /**
     *  @desc : This function is to reschedule the booking
     *  @param : booking id
     *  @return : reschedules the booking and load view
     */
    function process_reschedule_booking_form($booking_id) {
	//$data = $this->booking_model->getbooking($booking_id);
	//$data['user_id'] = $this->input->post('user_id');
	//$data['booking_date'] = $this->input->post('booking_date');
	$data['booking_date'] = date('d-m-Y', strtotime($this->input->post('booking_date')));

	$yy = date("y", strtotime($data['booking_date']));
	$mm = date("m", strtotime($data['booking_date']));
	$dd = date("d", strtotime($data['booking_date']));

	$data['booking_timeslot'] = $this->input->post('booking_timeslot');
	$data['current_status'] = 'Rescheduled';
	$data['internal_status'] = 'Rescheduled';
	$data['update_date'] = date("Y-m-d h:i:s");

	//Is this SD booking?
	if (strpos($booking_id, "SS") !== FALSE) {
	    $is_sd = TRUE;
	} else {
	    $is_sd = FALSE;
	}

	if ($data['booking_timeslot'] == "Select") {
	    echo "Please Select Booking Timeslot.";
	} else {
	    //$insertData = $this->booking_model->reschedule_booking($booking_id, $data);
	    $this->booking_model->reschedule_booking($booking_id, $data);

	    //Update SD leads table if required
	    if ($is_sd) {
		if ($this->booking_model->check_sd_lead_exists_by_booking_id($booking_id) === TRUE) {
		    $sd_where = array("CRM_Remarks_SR_No" => $booking_id);
		    $sd_data = array(
			"Status_by_247around" => $data['current_status'],
			"Remarks_by_247around" => $data['internal_status'],
			"Scheduled_Appointment_DateDDMMYYYY" => $data['booking_date'],
			"Scheduled_Appointment_Time" => $data['booking_timeslot'],
			"update_date" => $data['update_date']
		    );
		    $this->booking_model->update_sd_lead($sd_where, $sd_data);
		} else {
		    if (Partner_Integ_Complete) {
			//Update Partner leads table
			$sch_date = date_format(date_create($yy . "-" . $mm . "-" . $dd), "Y-m-d H:i:s");
			$partner_where = array("247aroundBookingID" => $booking_id);
			$partner_data = array(
			    "247aroundBookingStatus" => $data['current_status'],
			    "247aroundBookingRemarks" => $data['internal_status'],
			    "ScheduledAppointmentDate" => $sch_date,
			    "ScheduledAppointmentTime" => $data['booking_timeslot'],
			    "update_date" => $data['update_date']
			);
			$this->partner_model->update_partner_lead($partner_where, $partner_data);

			//Call relevant partner API
			//TODO: make it dynamic, use service object model (interfaces)
			$partner_cb_data = array_merge($partner_where, $partner_data);
			$this->partner_sd_cb->update_status_reschedule_booking($partner_cb_data);
		    }
		}
	    }

	    $query1 = $this->booking_model->booking_history_by_booking_id($booking_id);	    
	    
	    $from = 'booking@247around.com';
	    $to = "anuj@247around.com, nits@247around.com";
	    $cc = "";
	    $bcc = "";
	    $subject = 'Booking Rescheduled-AROUND';
	    $message = "Booking Rescheduled:<br>Customer name: " . $query1[0]['name'] .
		"<br>Customer phone number: " . $query1[0]['phone_number'] .
		"<br>Customer email address: " . $query1[0]['user_email'] .
		"<br>Booking Id is: " . $query1[0]['booking_id'] .
		"<br>Service name is:" . $query1[0]['services'] .
		"<br>New booking Date is: " . $data['booking_date'] .
		"<br>New booking timeslot is: " . $data['booking_timeslot'] .
		"<br>Booking updation date is: " . $data['update_date'] .
		"<br>Booking address is: " . $query1[0]['booking_address'] .
		"<br> Thanks!!";
		$attachment = "";
	    //$this->sendMail('Booking Rescheduled-AROUND', $message, $to, '', '');
	    $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment);

	    $months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
	    $mm = $months[$mm - 1];
	    $data['booking_date'] = $dd . $mm;
	    if ($data['booking_timeslot'] == "10AM-1PM") {
		$data['booking_timeslot'] = "1PM";
	    } elseif ($data['booking_timeslot'] == "1PM-4PM") {
		$data['booking_timeslot'] = "4PM";
	    } elseif ($data['booking_timeslot'] == "4PM-7PM") {
		$data['booking_timeslot'] = "7PM";
	    }

	    if ($is_sd == FALSE) {
		$smsBody = "Your request for " . $query1[0]['services'] . " Repair is rescheduled to " . $data['booking_date'] . ", " . $data['booking_timeslot'] . ". To avail discounts book on App 247Around goo.gl/m0iAcS. 011-39595200";
		$this->notify->sendTransactionalSms($query1[0]['phone_number'], $smsBody);
	    }

	    //Setting mail to vendor flag to 0, once booking is rescheduled
	    $this->booking_model->set_mail_to_vendor_flag_to_zero($booking_id);

	    log_message('info', 'Rescheduled- Booking id: ' . $booking_id. " Rescheduled By ". $this->session->userdata('employee_id'). " data ".print_r($data));

	    redirect(base_url() . 'employee/booking/view', 'refresh');
	}
    }

    //-------Send email function--------------//

 //    function sendMail($subject, $message, $to, $cc, $bcc) {

	// $this->load->library('email');
	// $this->email->initialize(array(
	//     'useragent' => 'CodeIgniter',
	//     'protocol' => 'smtp',
	//     'smtp_host' => 'smtp.sendgrid.net',
	//     'smtp_port' => '587',
	//     'smtp_user' => 'nitinmalhotra',
	//     'smtp_pass' => 'mandatory16',
	//     'mailtype' => 'html',
	//     'charset' => 'iso-8859-1',
	//     'crlf' => "\r\n",
	//     'newline' => "\r\n",
	//     'wordwrap' => TRUE
	//     )
	// );

	// $this->email->from('booking@247around.com', '247around Team');
	// $this->email->to($to);
	// $this->email->cc($cc);
	// //$this->email->bcc('anuj@247around.com, nits@247around.com');
	// //$this->email->bcc('anuj.aggarwal@gmail.com');
	// $this->email->subject($subject);
	// $this->email->message($message);
	// $this->email->send();
 //    }

    function getBrandForService($service_id) {

	$result = $this->booking_model->getBrandForService($service_id);
	foreach ($result as $brand) {
	    echo "<option>$brand[brand_name]</option>";
	}
	//echo $service_id;
    }

    /**
     * @desc : This function will load category with help of service_id on ajax call
     * @param: service_id of booking
     * @return : displays category
     */
    function getCategoryForService($service_id) {
	//echo $service_id;

	$result = $this->booking_model->getCategoryForService($service_id);

	foreach ($result as $category) {
	    echo "<option>$category[category]</option>";
	}
    }

    /**
     * @desc : This function will load capacity with help of Category and service_id on ajax call
     * @param: Category and service_id of booking
     * @return : displays capacity
     */
    public function getCapacityForCategory($service_id, $category) {
	//Return column "capacity", only unique results, as per the
	//$category=str_replace('%20',' ',$category);
	$category = urldecode($category);

	$result = $this->booking_model->getCapacityForCategory($service_id, $category);

	foreach ($result as $capacity) {
	    echo "<option>$capacity[capacity]</option>";
	}
    }

    /**
     * @desc : This function will show the price and services for ajax call
     * @param: service_id,category and capacity of the booking
     * @return : services name and there prices
     */
    public function getPricesForCategoryCapacity($service_id, $category, $capacity) {
	//Return columns "service_category" and "total_charges",
	if ($capacity != "NULL") {
	    $capacity = urldecode($capacity);
	} else {
	    $capacity = "";
	}
	$category = urldecode($category);

	$result = $this->booking_model->getPricesForCategoryCapacity($service_id, $category, $capacity);

	echo "<tr><th>Service Category</th><th>Total Charges</th><th>Selected Services</th></tr>";
	foreach ($result as $prices) {
	    echo "<tr><td>" . $prices['service_category'] . "</td><td>" .
	    $prices['total_charges'] .
	    "</td><td><input id='Checkbox1' class='Checkbox1' type='checkbox' " .
	    "name='" . str_replace(" ", "", $prices['service_category']) . "'" .
	    "value=" . $prices['total_charges'] . "></td><tr>";
	}
    }

    /**
     * @desc : This function sends SMS to users
     * @param: user phone number and smsBody
     * @return : sends the msg to the user
     */
 //    function sendTransactionalSms($phone_number, $body) {

	// //log_message ('info', "Entering: " . __METHOD__ . ": Phone num: " . $phone_number);

	// $post_data = array(
	//     // 'From' doesn't matter; For transactional, this will be replaced with your SenderId;
	//     // For promotional, this will be ignored by the SMS gateway
	//     'From' => '01130017601',
	//     'To' => $phone_number,
	//     'Body' => $body,
	// );

	// $exotel_sid = "aroundhomz";
	// $exotel_token = "a041058fa6b179ecdb9846ccf0e4fd8e09104612";

	// $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";

	// $ch = curl_init();

	// curl_setopt($ch, CURLOPT_VERBOSE, 1);
	// curl_setopt($ch, CURLOPT_URL, $url);
	// curl_setopt($ch, CURLOPT_POST, 1);
	// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	// curl_setopt($ch, CURLOPT_FAILONERROR, 0);
	// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	// curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));

	// $http_result = curl_exec($ch);
	// $error = curl_error($ch);
	// $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	// //print_r($ch);
	// //echo exit();
	// curl_close($ch);
 //    }

    /**
     *  @desc : This function is to select all pending bookings to assign vendor(if not already assigned)
     *  @param : void
     *  @return : booking details and vendor details to view
     */
    function get_assign_booking_form() {
	$results = array();
	$bookings = $this->booking_model->pendingbookings();
	//print_r($bookings);

	foreach ($bookings as $booking) {
	   array_push($results, $this->booking_model->find_sc_by_pincode_and_appliance($booking['service_id'], $booking['booking_pincode']));
	}

	$this->load->view('employee/header');
	$this->load->view('employee/assignbooking', array('data' => $bookings, 'results' => $results));
    }

    /**
     *  @desc : Function to assign vendors for pending bookings in background process,
     *  it send a Post server request.
     *  @param : service center
     *  @return : load pending booking view
     */
    function process_assign_booking_form() {
	$service_center['service_center'] = $this->input->post('service_center');
	$url = base_url() . "employee/do_background_process/assign_booking";
	$this->asynchronous_lib->do_background_process($url, $service_center);

	//$this->view_pending_queries();
	redirect(base_url() . 'employee/booking/view_pending_queries');
    }

    /**
     *  @desc : Ajax call(This function is to get non working days for particular vendor)
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
    function get_rating_form($booking_id) {
	$getbooking = $this->booking_model->getbooking($booking_id);
	if ($getbooking) {
	    $employee_id = $this->session->userdata('employee_id');
	    $data = $getbooking;
	    $this->load->view('employee/header');
	    $this->load->view('employee/rating', array('data' => $data));
	} else {
	    echo "Id doesn't exist";
	}
    }

    /**
     *  @desc : This function is to save ratings for booking and for vendors
     *  @param : booking id
     *  @return : rate for booking and load view
     */
    function process_rating_form($booking_id) {
	//$data['user_id'] = $this->input->post('user_id');

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

	$this->booking_model->rate($booking_id, $data);

	//Is this SD booking?
	if (strpos($booking_id, "SS") !== FALSE) {
	    $is_sd = TRUE;
	} else {
	    $is_sd = FALSE;
	}

	//Update SD bookings if required
	if ($is_sd) {
	    $sd_where = array("CRM_Remarks_SR_No" => $booking_id);
	    $sd_data = array(
		"Rating_Stars" => $data['rating_stars'],
		"update_date" => $data['closed_date']
	    );
	    $this->booking_model->update_sd_lead($sd_where, $sd_data);
	}

	redirect(base_url() . 'employee/booking/viewcompletedbooking', 'refresh');
    }

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
     *  @desc : This function is to select queries for confirmation
     *  @param : booking id
     *  @return : user, booking details, appliance id, category and service to view
     */
    function get_update_query_form($booking_id) {
	//get booking details
	$query1 = $this->booking_model->getbooking($booking_id);
	//get uit details
	$query2 = $this->booking_model->get_unit_details($booking_id);
	$page = "FollowUp";
	$internal_status = $this->booking_model->get_internal_status($page);

	if ($query1) {
	    //get user and other details
	    $query3 = $this->booking_model->booking_history_by_booking_id($booking_id);
	    //echo print_r($query3, true);
	}

	$service_id = $query1[0]['service_id'];
	//echo print_r($service_id, true);

	$appliance_id = $query1[0]['appliance_id'];
	$all_brands = $this->booking_model->getBrandForService($service_id);
	$all_categories = $this->booking_model->getCategoryForService($service_id);
	//echo print_r($all_categories, true);
	$all_capacities = $this->booking_model->getCapacityForAppliance($service_id);
	//echo print_r($all_capacities, true);

	if (count($query2) > 0) {
	    $unit_id = $query2[0]['id'];
	    $brand = $query2[0]['appliance_brand'];

	    //rearrange brands array so that $brand comes on top
	    $brands = array(0 => array("brand_name" => $brand));
	    foreach ($all_brands as $value) {
		if ($brand != $value['brand_name']) {
		    array_push($brands, $value);
		}
	    }

	    $category = $query2[0]['appliance_category'];

	    //rearrange categories array so that $category comes on top
	    $categories = array(0 => array("category" => $category));
	    foreach ($all_categories as $value) {
		if ($category != $value['category']) {
		    array_push($categories, $value);
		}
	    }

	    $all_capacities = $this->booking_model->getCapacityForCategory($service_id, $category);
	    $capacity = $query2[0]['appliance_capacity'];

	    //rearrange capacities array so that $capacity comes on top
	    $capacities = array(0 => array("capacity" => $capacity));
	    foreach ($all_capacities as $value) {
		if ($capacity != $value['capacity']) {
		    array_push($capacities, $value);
		}
	    }
	} else {
	    $unit_id = '';
	    $brands = $all_brands;
	    $categories = $all_categories;
	    $capacities = $all_capacities;
	}

	$this->load->view('employee/header');
	$this->load->view('employee/followup', array(
	    'query1' => $query1,
	    'unit_details' => $query2[0],
	    'internal_status' => $internal_status,
	    'query3' => $query3,
	    'unit_id' => $unit_id,
	    'appliance_id' => $appliance_id,
	    'brands' => $brands,
	    'categories' => $categories,
	    'capacities' => $capacities));
    }

    /**
     *  @desc : This function is to process the followup
     *  @param : booking id
     *  @return : confirms as booking/query and load view
     */
    function process_update_query_form($booking_id) {
	$booking['user_id'] = $this->input->post('user_id');
	$booking['service_id'] = $this->input->post('service_id');

	//Appliance details
	$booking['appliance_brand'] = $this->input->post('appliance_brand');
	$booking['appliance_category'] = $this->input->post('appliance_category');
	$booking['appliance_capacity'] = $this->input->post('appliance_capacity');
	$booking['purchase_year'] = $this->input->post('purchase_year');
	$booking['appliance_tag'] = $this->input->post('appliance_tag');
	$booking['model_number'] = $this->input->post('model_number');

	$booking['booking_primary_contact_no'] = $this->input->post('booking_primary_contact_no');
	$booking['booking_alternate_contact_no'] = $this->input->post('booking_alternate_contact_no');
	//echo print_r($this->input->post('booking_alternate_contact_no'), true);
	$booking['total_price'] = $this->input->post('total_price');
	$booking['potential_value'] = $this->input->post('potential_value');
	$booking['items_selected'] = $this->input->post('items_selected');
	$booking['booking_date'] = $this->input->post('booking_date');
	$booking['query_remarks'] = $this->input->post('query_remarks');
	$booking['booking_remarks'] = $this->input->post('booking_remarks');
	$booking['booking_timeslot'] = $this->input->post('booking_timeslot');
	$booking['booking_address'] = $this->input->post('booking_address');
	$booking['booking_pincode'] = $this->input->post('booking_pincode');
	$booking['quantity'] = $this->input->post('quantity');

	//internal_status would be empty if booking is confirmed
	if ($this->input->post('internal_status') != "")
	    $booking['internal_status'] = $this->input->post('internal_status');
	else
	    $booking['internal_status'] = "FollowUp";

	$booking['type'] = "Query";
	$booking['amount_due'] = $booking['total_price'];
	$booking['booking_date'] = date('d-m-Y', strtotime($booking['booking_date']));
	$yy = date("y", strtotime($booking['booking_date']));
	$mm = date("m", strtotime($booking['booking_date']));
	$dd = date("d", strtotime($booking['booking_date']));

	$unit_id = $this->input->post('unit_id');
	$appliance_id = $this->input->post('appliance_id');

	//Insert appliance if required
	if (!$appliance_id) {
	    $booking['appliance_id'] = $this->booking_model->addsingleappliance($booking);
	} else {
	    $booking['appliance_id'] = $appliance_id;
	    $this->booking_model->update_appliance_details($booking);
	}

	//Is this SD booking?
	//TODO: Check whether this is a Partner booking
	if (strpos($booking_id, "SS") !== FALSE) {
	    $is_sd = TRUE;
	} else {
	    $is_sd = FALSE;
	}
	//Check to find which button is clicked
	//echo print_r($this->input->post('sbm'), true);
	if ($this->input->post('sbm') == "Confirm Booking") {
	    //Remove "Q-" from booking ID
	    $booking['booking_id'] = substr($booking_id, 2);
	    $booking['current_status'] = "Pending";
	    $booking['internal_status'] = "Scheduled";
	    //$booking['potential_value'] = 0;

	    if (!$unit_id) {
		//Insert unit appliance
		$this->booking_model->add_single_unit_details($booking);
	    } else {
		//Update unit appliance
		$this->booking_model->update_booking_unit_details($booking_id, $booking);
	    }

	    //Updating booking details
	    if ($this->booking_model->update_booking_details($booking_id, $booking)) {
		//Update SD leads table if required
		if ($is_sd) {
		    if ($this->booking_model->check_sd_lead_exists_by_booking_id($booking_id) === TRUE) {
			//Booking came through old method of excel file sharing
			//Update it in snapdeal_leads table
			$sd_where = array("CRM_Remarks_SR_No" => $booking_id);
			$sd_data = array(
			    "CRM_Remarks_SR_No" => $booking['booking_id'],
			    "Status_by_247around" => $booking['current_status'],
			    "Remarks_by_247around" => $booking['internal_status'],
			    "Scheduled_Appointment_DateDDMMYYYY" => $booking['booking_date'],
			    "Scheduled_Appointment_Time" => $booking['booking_timeslot'],
			    "update_date" => date("Y-m-d h:i:s")
			);
			$this->booking_model->update_sd_lead($sd_where, $sd_data);
		    } else {
			//Update Partner leads table
			if (Partner_Integ_Complete) {
			    $sch_date = date_format(date_create($yy . "-" . $mm . "-" . $dd), "Y-m-d H:i:s");
			    $partner_where = array("247aroundBookingID" => $booking_id);
			    $partner_data = array(
				"247aroundBookingStatus" => $booking['current_status'],
				"247aroundBookingRemarks" => $booking['internal_status'],
				"ScheduledAppointmentDate" => $sch_date,
				"ScheduledAppointmentTime" => $booking['booking_timeslot'],
				"update_date" => date("Y-m-d h:i:s")
			    );
			    $this->partner_model->update_partner_lead($partner_where, $partner_data);

			    //Call relevant partner API
			    //TODO: make it dynamic, use service object model (interfaces)
			    $partner_cb_data = array_merge($partner_where, $partner_data);
			    $this->partner_sd_cb->update_status_schedule_booking($partner_cb_data);
			}
		    }
		}

		$query1 = $this->booking_model->booking_history_by_booking_id($booking['booking_id']);
		//echo print_r($query1, true);
		//$query2 = $this->booking_model->get_unit_details($booking['booking_id']);
		//echo print_r($query2, true);

		$months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		$mm = $months[$mm - 1];
		$booking['booking_date'] = $dd . $mm;

		if ($booking['booking_timeslot'] == "10AM-1PM") {
		    $booking['booking_timeslot'] = "1PM";
		} elseif ($booking['booking_timeslot'] == "1PM-4PM") {
		    $booking['booking_timeslot'] = "4PM";
		} elseif ($booking['booking_timeslot'] == "4PM-7PM") {
		    $booking['booking_timeslot'] = "7PM";
		}

		//-------Sending Email On Booking--------//
		$message = "Congratulations! Query has been converted to Booking, details are mentioned below:
                            <br>Customer Name: " . $query1[0]['name'] . "<br>Customer Phone Number: " .
		    $query1[0]['phone_number'] . "<br>Customer email address: " . $query1[0]['user_email'] .
		    "<br>Booking Id: " . $booking['booking_id'] . "<br>Service name:" . $query1[0]['services'] .
		    "<br>Number of appliance: " . $query1[0]['quantity'] . "<br>Booking Date: " .
		    $booking['booking_date'] . "<br>Booking Timeslot: " . $booking['booking_timeslot'] .
		    "<br>Amount Due: " . $query1[0]['amount_due'] . "<br>Your Booking Remark is: " .
		    $booking['booking_remarks'] . "<br>Booking address: " . $booking['booking_address'] .
		    "<br>Booking city: " . $query1[0]['city'] .
		    "<br>Booking pincode: " . $query1[0]['booking_pincode'] . "<br><br>
                              Appliance Details:<br>";

		$appliance = "";

		$appliance = "<br>Brand : " . $booking['appliance_brand'] . "<br>Category : " . $booking['appliance_category'] . "<br>Capacity : " . $booking['appliance_capacity'] .
		    "<br>Selected service/s is/are: " . $booking['items_selected'] . "<br>Total price is: " . $booking['total_price'] . "<br>";
		$message = $message . $appliance;

		$message = $message . "<br> Thanks!!";

		$from = 'booking@247around.com';
		$to = "anuj@247around.com, nits@247around.com";
		$cc = "";
		$bcc = "";
		$subject = 'Booking Confirmation-AROUND';
		$attachment = "";
//		$this->sendMail($subject, $message, $to, $cc, $bcc);
		$this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment);

		//TODO: Make it generic
		if ($is_sd == FALSE) {
		    $smsBody = "Got it! Request for " . $query1[0]['services'] . " Repair is confirmed for " . $booking['booking_date'] . ", " . $booking['booking_timeslot'] . ". 247Around Indias 1st Multibrand Appliance repair App goo.gl/m0iAcS. 011-39595200";
		    $this->notify->sendTransactionalSms($query1[0]['phone_number'], $smsBody);
		}

		//------End of sending SMS--------//
		//redirect(base_url() . 'employee/booking/view', 'refresh');
		redirect(base_url() . 'employee/booking/view_pending_queries', 'refresh');
	    } else {
		echo "Booking not inserted";
	    }
	}
	//booking not confirmed
	else {
	    $booking['current_status'] = "FollowUp";
	    $booking['potential_value'] = $this->input->post('potential_value');
	    $booking['booking_id'] = $booking_id;

	    if (!$unit_id) {
		//Insert unit appliance
		$this->booking_model->add_single_unit_details($booking);
	    } else {
		//Update unit appliance
		$this->booking_model->update_booking_unit_details($booking_id, $booking);
	    }

	    //Updating booking details
	    $result = $this->booking_model->update_booking_details($booking_id, $booking);
	    if ($result) {
		//Update SD leads table if required
		if ($is_sd) {
		    if ($this->booking_model->check_sd_lead_exists_by_booking_id($booking_id) === TRUE) {
			$sd_where = array("CRM_Remarks_SR_No" => $booking_id);
			$sd_data = array(
			    "Status_by_247around" => $booking['current_status'],
			    "Remarks_by_247around" => $booking['internal_status'],
			    "update_date" => date("Y-m-d h:i:s")
			);
			$this->booking_model->update_sd_lead($sd_where, $sd_data);
		    } else {
			if (Partner_Integ_Complete) {
			    //Update Partner leads table
			    $partner_where = array("247aroundBookingID" => $booking_id);
			    $partner_data = array(
				"247aroundBookingStatus" => $booking['current_status'],
				"247aroundBookingRemarks" => $booking['internal_status'],
				"update_date" => date("Y-m-d h:i:s")
			    );
			    $this->partner_model->update_partner_lead($partner_where, $partner_data);

			    //Call relevant partner API
			    //TODO: make it dynamic, use service object model (interfaces)
			    $partner_cb_data = array_merge($partner_where, $partner_data);
			    $this->partner_sd_cb->update_status_schedule_booking($partner_cb_data);
			}
		    }
		}

		redirect(base_url() . 'employee/booking/view_pending_queries', 'refresh');
	    } else {
		echo "Query is not saved";
	    }
	}
    }

    /**
     *  @desc : This function is to select queries for cancellation
     *  @param : booking id
     *  @return : users, booking details and cancellation reason to view
     */
    function get_cancel_followup_form($booking_id) {
	$query = $this->booking_model->getbooking($booking_id);
	$reasons = $this->booking_model->cancelreason();
	$page = "Cancel";
	$internal_status = $this->booking_model->get_internal_status($page);
	$this->load->view('employee/header');
	$this->load->view('employee/cancelfollowup', array('query' => $query[0],
	    'reasons' => $reasons,
	    'internal_status' => $internal_status));
    }

    /**
     *  @desc : This function is to cancel the followup
     *  @param : booking id
     *  @return : cancel the query and load view
     */
    function process_cancel_followup_form($booking_id) {
	//$booking['booking_id'] = $this->input->post('booking_id');
	$booking['closing_remarks'] = $this->input->post('closing_remarks');
	$booking['current_status'] = "Cancelled";
	$booking['internal_status'] = $this->input->post('internal_status');
	$booking['cancellation_reason'] = $this->input->post('cancellation_reason');
	$booking['closed_date'] = date("Y-m-d h:i:s");
	$booking['update_date'] = $booking['closed_date'];

	//Is this SD booking?
	if (strpos($booking_id, "SS") !== FALSE) {
	    $is_sd = TRUE;
	} else {
	    $is_sd = FALSE;
	}

	if ($booking['cancellation_reason'] == 'Other') {
	    if ($is_sd) {
		//For SD bookings, save internal status as cancellation reason
		$booking['cancellation_reason'] = "Other : " . $booking['internal_status'];
	    } else {
		//For other bookings, save other reason text
		$booking['cancellation_reason'] = "Other : " . $this->input->post("cancellation_reason_text");
	    }
	}

	$this->booking_model->cancel_followup($booking_id, $booking);

	//Update SD bookings if required
	if ($is_sd) {
	    if ($this->booking_model->check_sd_lead_exists_by_booking_id($booking_id) === TRUE) {
		$sd_where = array("CRM_Remarks_SR_No" => $booking_id);
		$sd_data = array(
		    "Status_by_247around" => "Cancelled",
		    "Remarks_by_247around" => $booking['internal_status'],
		    "update_date" => $booking['closed_date']
		);
		$this->booking_model->update_sd_lead($sd_where, $sd_data);
	    } else {
		if (Partner_Integ_Complete) {
		    //Update Partner leads table
		    $partner_where = array("247aroundBookingID" => $booking_id);
		    $partner_data = array(
			"247aroundBookingStatus" => "Cancelled",
			"247aroundBookingRemarks" => $booking['internal_status'],
			"update_date" => $booking['closed_date']
		    );
		    $this->partner_model->update_partner_lead($partner_where, $partner_data);

		    //Call relevant partner API
		    //TODO: make it dynamic, use service object model (interfaces)
		    $partner_cb_data = array_merge($partner_where, $partner_data);
		    $this->partner_sd_cb->update_status_cancel_booking($partner_cb_data);
		}
	    }
	}

	$query1 = $this->booking_model->booking_history_by_booking_id($booking_id);

	log_message('info', 'Booking Status Change- Booking id: ' . $booking_id. " Cancelled By ". $this->session->userdata('employee_id'));

	//------------Sending Email----------//

	$message = "Booking Cancellation:<br>Customer name: " . $query1[0]['name'] . "<br>Customer phone number: " .
	    $query1[0]['phone_number'] . "<br>Customer email: " . $query1[0]['user_email'] . "<br>Booking Id: " .
	    $query1[0]['booking_id'] . "<br>Service name is:" . $query1[0]['services'] . "<br>Booking date was: " .
	    $query1[0]['booking_date'] . "<br>Booking timeslot was: " . $query1[0]['booking_timeslot'] .
	    "<br>Booking cancellation date is: " . $booking['update_date'] . "<br>Booking cancellation reason: " .
	    $booking['cancellation_reason'] . "<br> Thanks!!";
	$from = 'booking@247around.com';	    
	$to = "anuj@247around.com, nits@247around.com";
	$cc = "";
	$bcc = "";
	$subject = "Booking Cancellation-AROUND";
	$attachment = "";
//	$this->sendMail('Booking Cancellation-AROUND', $message, $to, '', '');
	$this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment);

	redirect(base_url() . 'employee/booking/view_pending_queries', 'refresh');
    }


    function jobcard($booking_id) {
	$query1 = $this->booking_model->booking_history_by_booking_id($booking_id);
	$query2 = $this->booking_model->get_unit_details($booking_id);

	$this->load->view('employee/header');
	$this->load->view('employee/unassignedjobcard', array('query1' => $query1, 'query2' => $query2));
    }

    function viewdetails($booking_id) {
	$data['query1'] = $this->booking_model->booking_history_by_booking_id($booking_id);
	$data['query2'] = $this->booking_model->get_unit_details($booking_id);
	$data['query4'] = $this->booking_model->getdescription_about_booking($booking_id);

	$data['query3'] = $this->booking_model->selectservicecentre($booking_id);
	if (count($data['query3']) == 0) {
	    //Service centre not assigned yet
	    $data['query3'][0]['service_centre_name'] = 'NA';
	    $data['query3'][0]['primary_contact_name'] = 'NA';
	    $data['query3'][0]['primary_contact_phone_1'] = 'NA';
	}

	$this->load->view('employee/header');
	$this->load->view('employee/viewdetails', $data);
    }

//
//    /**
//     *  @desc : This function is to select queries for editing
//     *  @param : booking id
//     *  @return : user's and booking details to view
//     */
//
//  function get_edit_query_form($booking_id)
//  {
//    $getbooking = $this->booking_model->getbooking($booking_id);
//        if ($getbooking) {
//            $data= $getbooking;
//            $query = $this->booking_model->booking_history_by_booking_id($booking_id);
//            $data1 = $query;
//            $this->load->view('employee/header');
//            $this->load->view('employee/editquery', array('data' => $data, 'data1' => $data1));
//        } else {
//            echo "This Id doesn't Available";
//        }
//  }
//
//    /**
//     *  @desc : This function is to edit the queries
//     *  @param : booking id
//     *  @return : edit the query and load view
//     */
//
//  function process_edit_query_form($booking_id)
//  {
//    $data['booking_date']    = $this->input->post('booking_date');
//    $data['query_remarks']    = $this->input->post('query_remarks');
//    $data['booking_date'] = date('d-m-Y', strtotime($data['booking_date']));
//      $yy = date("y", strtotime($data['booking_date']));
//      $mm = date("m", strtotime($data['booking_date']));
//      $dd = date("d", strtotime($data['booking_date']));
//    $data['booking_timeslot'] = $this->input->post('booking_timeslot');
//    $data['update_date'] = date("Y-m-d h:i:s");
//    if($data['booking_timeslot']=="Select")
//    {
//      echo "Please Select Query Timeslot.";
//    }
//    else
//    {
//        $insertData = $this->booking_model->edit_query($booking_id, $data);
//        redirect(base_url().'employee/booking/view_pending_queries','refresh');
//
//   }
//  }
    //Function to sort pending bookings with current status
    function status_sorted_booking($offset = 0, $page = 0) {
	if ($page == 0) {
	    $page = 50;
	}

	$offset = ($this->uri->segment(4) != '' ? $this->uri->segment(4) : 0);
	$config['base_url'] = base_url() . 'employee/booking/status_sorted_booking';
	$config['total_rows'] = $this->booking_model->total_pending_booking();
	$config['per_page'] = $page;
	$config['uri_segment'] = 4;
	$config['first_link'] = 'First';
	$config['last_link'] = 'Last';

	$this->pagination->initialize($config);
	$data['links'] = $this->pagination->create_links();

	$data['Bookings'] = $this->booking_model->status_sorted_booking($config['per_page'], $offset);
	$this->load->view('employee/header');

	$this->load->view('employee/statussortedbooking', $data);
    }

    //Function to sort pending bookings with date
    function date_sorted_booking($offset = 0, $page = 0) {
	if ($page == 0) {
	    $page = 50;
	}
	//$offset = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
	$offset = ($this->uri->segment(4) != '' ? $this->uri->segment(4) : 0);
	$config['base_url'] = base_url() . 'employee/booking/date_sorted_booking';
	$config['total_rows'] = $this->booking_model->total_pending_booking();
	$config['per_page'] = $page;
	$config['uri_segment'] = 4;
	$config['first_link'] = 'First';
	$config['last_link'] = 'Last';

	$this->pagination->initialize($config);
	$data['links'] = $this->pagination->create_links();

	$data['Bookings'] = $this->booking_model->date_sorted_booking($config['per_page'], $offset);
	$this->load->view('employee/header');

	$this->load->view('employee/datesortedbooking', $data);
    }

    //Function to sort pending bookings with service center name
    function service_center_sorted_booking($offset = 0, $page = 0) {
	if ($page == 0) {
	    $page = 50;
	}

	$offset = ($this->uri->segment(4) != '' ? $this->uri->segment(4) : 0);
	$config['base_url'] = base_url() . 'employee/booking/service_center_sorted_booking';
	$config['total_rows'] = $this->booking_model->total_pending_booking();
	$config['per_page'] = $page;
	$config['uri_segment'] = 4;
	$config['first_link'] = 'First';
	$config['last_link'] = 'Last';
	$this->pagination->initialize($config);
	$data['links'] = $this->pagination->create_links();
	$data['Bookings'] = $this->booking_model->service_center_sorted_booking($config['per_page'], $offset);
	$this->load->view('employee/header');
	$this->load->view('employee/booking', $data);
    }

    /**
     *  @desc : This function is to select completed bookings for editing
     *  @param : booking id
     *  @return : user's and booking details to view
     */
    function get_edit_completed_booking_form($booking_id) {
	$getbooking = $this->booking_model->getbooking($booking_id);

	$query2 = $this->booking_model->get_unit_details($booking_id);
	if ($getbooking) {
	    $employee_id = $this->session->userdata('employee_id');

	    $data = $getbooking;

	    $query = $this->booking_model->booking_history_by_booking_id($booking_id);

	    $data1 = $query;

	    $this->load->view('employee/header');
	    $this->load->view('employee/editcompletedbooking', array('data' => $data,
		'data1' => $data1,
		'query2' => $query2));
	} else {
	    echo "This Id doesn't Available";
	}
    }

    /**
     *  @desc : This function is to edit the completed booking
     *  @param : booking id
     *  @return : edit the completed booking and load view
     */
    function process_edit_completed_booking_form($booking_id) {
	$data['service_charge'] = $this->input->post('service_charge');
	$data['service_charge_collected_by'] = $this->input->post('service_charge_collected_by');
	$data['additional_service_charge'] = $this->input->post('additional_service_charge');
	$data['additional_service_charge_collected_by'] = $this->input->post('additional_service_charge_collected_by');
	$data['parts_cost'] = $this->input->post('parts_cost');
	$data['parts_cost_collected_by'] = $this->input->post('parts_cost_collected_by');
	$data['closing_remarks'] = $this->input->post('closing_remarks');
	$data['booking_remarks'] = $this->input->post('booking_remarks');
	$data['amount_paid'] = $data['service_charge'] + $data['parts_cost'] + $data['additional_service_charge'];

	$insertData = $this->booking_model->edit_completed_booking($booking_id, $data);

	redirect(base_url() . 'employee/booking/viewcompletedbooking', 'refresh');
    }

    /**
     *  @desc : This function is to select particular appliance for booking
     *  @param : appliance id
     *  @return : user's and appliance details to view
     */
    function get_appliance_booking_form($id) {
	$sources = $this->booking_model->select_booking_source();
	$details = $this->booking_model->get_appliance_details($id);

	$price_details = $this->booking_model->getPricesForCategoryCapacity($details[0]['service_id'], $details[0]['category'], $details[0]['capacity']);

	$user_details = $this->booking_model->get_user_details($details[0]['user_id']);

	if ($details) {
	    $this->load->view('employee/header');
	    $this->load->view('employee/appliancebooking', array('sources' => $sources,
		'details' => $details,
		'price_details' => $price_details,
		'user_details' => $user_details));
	} else {
	    echo "This Appliance dosn't exists";
	}
    }

    /**
     *  @desc : This function is to get appliance booking confirmation page
     *  @param : appliance id
     *  @return : user and appliance details and load view
     */
    function appliancebookingconf($appliance_id) {
	$booking['user_id'] = $this->input->post('user_id');
	$booking['service_id'] = $this->input->post('service_id');
	$booking['user_email'] = $this->input->post('user_email');
	$booking['city'] = $this->input->post('city');
	$booking['state'] = $this->input->post('state');
	$booking['user_name'] = $this->input->post('name');
	$booking['phone_number'] = $this->input->post('phone_number'); //For pagination to user's detils page
	$booking['appliance_id'] = $appliance_id;
	$booking['appliance_brand'] = $this->input->post('appliance_brand');
	$booking['appliance_category'] = $this->input->post('appliance_category');
	$booking['model_number'] = $this->input->post('model_number');
	$booking['appliance_capacity'] = $this->input->post('appliance_capacity');
	$booking['purchase_year'] = $this->input->post('purchase_year');
	$booking['booking_primary_contact_no'] = $this->input->post('booking_primary_contact_no');
	$booking['booking_alternate_contact_no'] = $this->input->post('booking_alternate_contact_no');
	$booking['appliance_tags'] = $this->input->post('appliance_tags');
	$booking['total_price'] = $this->input->post('total_price');
	$booking['items_selected'] = $this->input->post('items_selected');
	$booking['booking_timeslot'] = $this->input->post('booking_timeslot');
	$booking['booking_address'] = $this->input->post('booking_address');
	$booking['booking_pincode'] = $this->input->post('booking_pincode');
	$booking['booking_remarks'] = $this->input->post('booking_remarks');
	$booking['booking_date'] = $this->input->post('booking_date');
	$booking['booking_date'] = date('d-m-Y', strtotime($booking['booking_date']));
	$yy = date("y", strtotime($booking['booking_date']));
	$mm = date("m", strtotime($booking['booking_date']));
	$dd = date("d", strtotime($booking['booking_date']));
	$booking['booking_id'] = str_pad($booking['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
	$booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($booking['user_id'])) + 1);
	$booking['amount_due'] = $booking['total_price'];
	$booking['quantity'] = 1;
	$booking['type'] = 'Booking';
	$booking['query_remarks'] = '';
	$booking['current_status'] = 'Pending';
	$booking['internal_status'] = 'Scheduled';
	$booking['create_date'] = date("Y-m-d h:i:s");
    $booking['source'] = $this->input->post('source_code');

	$result = $this->booking_model->service_name($booking['service_id']);

	$this->load->view('employee/header');
	$this->load->view('employee/appliancebookingconf', array('booking' => $booking, 'result' => $result));
    }

    function process_appliance_booking_form() {
	$booking['user_id'] = $this->input->post('user_id');
	$booking['service_id'] = $this->input->post('service_id');
	$booking['service_name'] = $this->input->post('services');
	$booking['user_email'] = $this->input->post('user_email');
	$booking['user_name'] = $this->input->post('user_name');
	$booking['city'] = $this->input->post('city');
	$booking['state'] = $this->input->post('state');
	$booking['phone_number'] = $this->input->post('phone_number');      //For pagination to user's detils page
	$booking['appliance_id'] = $this->input->post('appliance_id');
	$booking['appliance_brand'] = $this->input->post('appliance_brand');
	$booking['appliance_capacity'] = $this->input->post('appliance_capacity');
	$booking['appliance_category'] = $this->input->post('appliance_category');
	$booking['source'] = $this->input->post('source');
	$booking['model_number'] = $this->input->post('model_number');
	$booking['purchase_year'] = $this->input->post('purchase_year');
	$booking['booking_primary_contact_no'] = $this->input->post('booking_primary_contact_no');
	$booking['booking_alternate_contact_no'] = $this->input->post('booking_alternate_contact_no');
	$booking['appliance_tags'] = $this->input->post('appliance_tags');
	$booking['total_price'] = $this->input->post('total_price');
	$booking['items_selected'] = $this->input->post('items_selected');
	$booking['booking_timeslot'] = $this->input->post('booking_timeslot');
	$booking['booking_address'] = $this->input->post('booking_address');
	$booking['booking_pincode'] = $this->input->post('booking_pincode');
	$booking['booking_remarks'] = $this->input->post('booking_remarks');
	$booking['booking_date'] = $this->input->post('booking_date');
	$booking['booking_date'] = date('d-m-Y', strtotime($booking['booking_date']));
	$yy = date("y", strtotime($booking['booking_date']));
	$mm = date("m", strtotime($booking['booking_date']));
	$dd = date("d", strtotime($booking['booking_date']));
	$booking['booking_id'] = str_pad($booking['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
	$booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($booking['user_id'])) + 1);
	$booking['amount_due'] = $booking['total_price'];
	$booking['quantity'] = 1;
	$booking['type'] = 'Booking';
	$booking['query_remarks'] = '';
	$booking['current_status'] = 'Pending';
	$booking['internal_status'] = 'Scheduled';
	$booking['create_date'] = date("Y-m-d h:i:s");
	$booking['potential_value'] = 0;


	$this->booking_model->addapplianceunitdetails($booking);

	$output = $this->booking_model->addbooking($booking, $booking['appliance_id'], $booking['city'], $booking['state']);

	$months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
	$mm = $months[$mm - 1];
	$booking['booking_date'] = $dd . $mm;

	if ($booking['booking_timeslot'] == "10AM-1PM") {
	    $booking['booking_timeslot'] = "1PM";
	} elseif ($booking['booking_timeslot'] == "1PM-4PM") {
	    $booking['booking_timeslot'] = "4PM";
	} elseif ($booking['booking_timeslot'] == "4PM-7PM") {
	    $booking['booking_timeslot'] = "7PM";
	}

	//-------Sending Email On Booking--------//
	if ($booking['current_status'] != "FollowUp") {
	    $message = "Congratulations You have received new booking from existing appliance, details are mentioned below:
          <br>Customer Name: " . $booking['user_name'] . "<br>Customer Phone Number: " .
		$booking['booking_primary_contact_no'] . "<br>Customer email address: " .
		$booking['user_email'] . "<br>Booking Id: " . $booking['booking_id'] . "<br>Service name:" .
		$booking['service_name'] . "<br>Number of appliance: " . $booking['quantity'] .
		"<br>Booking Date: " . $booking['booking_date'] . "<br>Booking Timeslot: " .
		$booking['booking_timeslot'] . "<br>Amount Due: " . $booking['amount_due'] .
		"<br>Your Booking Remark is: " . $booking['booking_remarks'] . "<br>Booking address: " .
		$booking['booking_address'] . "<br>Booking pincode: " . $booking['booking_pincode'] .
		"<br>Booking city: " . $booking['city'] .
		"<br><br>
            Appliance Details:<br>";

	    $appliance = "";
	    for ($i = 0; $i < $booking['quantity']; $i++) {

		$appliance = "<br>Brand : " . $booking['appliance_brand'] . "<br>Category : " . $booking['appliance_category'] . "<br>Capacity : " . $booking['appliance_capacity'] .
		    "<br>Selected service/s is/are: " . $booking['items_selected'] . "<br>Total price is: " . $booking['total_price'] . "<br>";
		$message = $message . $appliance;
	    }
	    $message = $message . "<br> Thanks!!";

	    $from = 'booking@247around.com';
	    $to = "anuj@247around.com, nits@247around.com";
	    $cc = "";
	    $bcc = "";
	    $subject = 'Booking Confirmation-AROUND';
	    $attachment = "";
//	    $this->sendMail($subject, $message, $to, $cc, $bcc);
	    $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment);
	    //-------Sending SMS on booking--------//

	    if (strstr($booking['booking_id'], "SS") == FALSE) {
		$smsBody = "Got it! Request for " . $booking['service_name'] . " Repair is confirmed for " . $booking['booking_date'] . ", " . $booking['booking_timeslot'] . ". 247Around Indias 1st Multibrand Appliance repair App goo.gl/m0iAcS. 011-39595200";
		$this->notify->sendTransactionalSms($booking['booking_primary_contact_no'], $smsBody);
	    }
	    //------End of sending SMS--------//
	}

	redirect(base_url() . 'employee/booking/view', 'refresh');
    }

    /**
     *  @desc : This function is to get add new brand page
     *  @param : void
     *  @return : list of active services present
     */
    function get_add_new_brand_form() {
	$services = $this->booking_model->selectservice();

	$this->load->view('employee/header');
	$this->load->view('employee/addnewbrand', array('services' => $services));
    }

    /**
     *  @desc : This function is to add new brand
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
     *  @desc : This function is to view all pending queries
     *  @param : void
     *  @return : list of all pending queries
     */
    function view_all_pending_queries() {
	//$query = $this->booking_model->view_all_pending_queries();
	$query = $this->booking_model->get_pending_queries(-1, 0, '');

	$data['Bookings'] = null;
	if ($query) {
	    $data['Bookings'] = $query;
	}
	$this->load->view('employee/header');
	$this->load->view('employee/viewpendingqueries', $data);
    }

    /**
     *  @desc : This function is to view pending queries according to pagination
     *  @param : void
     *  @return : list of pending queries according to pagination
     */
    function view_pending_queries($offset = 0, $page = 0, $booking_id="") {
	if ($page == 0) {
	    $page = 50;
	}

	$offset = ($this->uri->segment(4) != '' ? $this->uri->segment(4) : 0);
	$config['base_url'] = base_url() . 'employee/booking/view_pending_queries';
	$config['total_rows'] = $this->booking_model->total_pending_queries($booking_id);

	$config['per_page'] = $page;
	$config['uri_segment'] = 4;
	$config['first_link'] = 'First';
	$config['last_link'] = 'Last';

	$this->pagination->initialize($config);
	$data['links'] = $this->pagination->create_links();

	$data['Bookings'] = $this->booking_model->get_pending_queries($config['per_page'], $offset, $booking_id);

	$this->load->view('employee/header');
	$this->load->view('employee/viewpendingqueries', $data);
    }

    /**
     *  @desc : This function is to view all cancelled queries
     *  @param : void
     *  @return : list of all cancelled queries
     */
    function view_all_cancelled_queries() {
	$query = $this->booking_model->view_all_cancelled_queries();

	$data['Bookings'] = null;

	if ($query) {
	    $data['Bookings'] = $query;
	}
	$this->load->view('employee/header');
	$this->load->view('employee/viewcancelledqueries', $data);
    }

    /**
     *  @desc : This function is to view cancelled queries according to pagination
     *  @param : void
     *  @return : list of cancelled queries according to pagination
     */
    function view_cancelled_queries($offset = 0, $page = 0) {
	if ($page == 0) {
	    $page = 50;
	}

	$offset = ($this->uri->segment(4) != '' ? $this->uri->segment(4) : 0);
	$config['base_url'] = base_url() . 'employee/booking/view_cancelled_queries';
	$config['total_rows'] = $this->booking_model->total_cancelled_queries();

	$config['per_page'] = $page;
	$config['uri_segment'] = 4;
	$config['first_link'] = 'First';
	$config['last_link'] = 'Last';

	$this->pagination->initialize($config);
	$data['links'] = $this->pagination->create_links();

	$data['Bookings'] = $this->booking_model->get_cancelled_queries($config['per_page'], $offset);

	if ($this->session->flashdata('result') != '')
	    $data['success'] = $this->session->flashdata('result');

	$this->load->view('employee/header');

	$this->load->view('employee/viewcancelledqueries', $data);
    }

    /**
     *  @desc : This function is to select booking for edit
     *  @param : booking id
     *  @return : user, booking details, appliance id, category and service to view
     */
    function get_edit_booking_form($booking_id) {
	//get booking details
	$query1 = $this->booking_model->getbooking($booking_id);
	//get uit details
	$query2 = $this->booking_model->get_unit_details($booking_id);
	$description = $this->booking_model->getdescription_about_booking($booking_id);

	if ($query1) {
	    //get user and other details
	    $query3 = $this->booking_model->booking_history_by_booking_id($booking_id);
	    //echo print_r($query3, true);
	}
	$service_id = $query1[0]['service_id'];
	//echo print_r($service_id, true);
	$appliance_id = $query1[0]['appliance_id'];
	$all_brands = $this->booking_model->getBrandForService($service_id);
	$all_categories = $this->booking_model->getCategoryForService($service_id);
	//echo print_r($all_categories, true);
	$all_capacities = $this->booking_model->getCapacityForAppliance($service_id);
	//echo print_r($all_capacities, true);
	if (count($query2) > 0) {
	    $unit_id = $query2[0]['id'];
	    $brand = $query2[0]['appliance_brand'];
	    //rearrange brands array so that $brand comes on top
	    $brands = array(0 => array("brand_name" => $brand));
	    foreach ($all_brands as $value) {
		if ($brand != $value['brand_name']) {
		    array_push($brands, $value);
		}
	    }
	    $category = $query2[0]['appliance_category'];
	    //rearrange categories array so that $category comes on top
	    $categories = array(0 => array("category" => $category));
	    foreach ($all_categories as $value) {
		if ($category != $value['category']) {
		    array_push($categories, $value);
		}
	    }
	    $all_capacities = $this->booking_model->getCapacityForCategory($service_id, $category);
	    $capacity = $query2[0]['appliance_capacity'];
	    //rearrange capacities array so that $capacity comes on top
	    $capacities = array(0 => array("capacity" => $capacity));
	    foreach ($all_capacities as $value) {
		if ($capacity != $value['capacity']) {
		    array_push($capacities, $value);
		}
	    }
	} else {
	    $unit_id = '';
	    $brands = $all_brands;
	    $categories = $all_categories;
	    $capacities = $all_capacities;
	}
	$this->load->view('employee/header');
	$this->load->view('employee/editbooking', array(
	    'query1' => $query1,
	    'unit_details' => $query2[0],
	    'query3' => $query3,
	    'unit_id' => $unit_id,
	    'appliance_id' => $appliance_id,
	    'brands' => $brands,
	    'description' => $description,
	    'categories' => $categories,
	    'capacities' => $capacities));
    }

    /**
     *  @desc : This function is to process the edit booking
     *  @param : booking id
     *  @return : confirms as booking/query and load view
     */
    function process_edit_booking_form($booking_id) {
	$booking['user_id'] = $this->input->post('user_id');
	$booking['service_id'] = $this->input->post('service_id');
	$booking['unit_id'] = $this->input->post('unit_id');
	//Appliance details
	$booking['appliance_id'] = $this->input->post('appliance_id');
	$booking['appliance_brand'] = $this->input->post('appliance_brand');
	$booking['appliance_category'] = $this->input->post('appliance_category');
	$booking['appliance_capacity'] = $this->input->post('appliance_capacity');
	$booking['purchase_year'] = $this->input->post('purchase_year');
	$booking['appliance_tag'] = $this->input->post('appliance_tag');
	$booking['model_number'] = $this->input->post('model_number');

	$booking['city'] = $this->input->post('booking_city');
	$booking['state'] = $this->input->post('booking_state');

	$booking['booking_alternate_contact_no'] = $this->input->post('booking_alternate_contact_no');
	$booking['booking_primary_contact_no'] = $this->input->post('booking_primary_contact_no');
	$booking['total_price'] = $this->input->post('total_price');
	$booking['items_selected'] = $this->input->post('items_selected');
	$booking['query_remarks'] = $this->input->post('query_remarks');
	$booking['booking_remarks'] = $this->input->post('booking_remarks');
	$booking['booking_date'] = $this->input->post('booking_date');
	$booking['booking_timeslot'] = $this->input->post('booking_timeslot');
	$booking['booking_address'] = $this->input->post('booking_address');
	$booking['booking_pincode'] = $this->input->post('booking_pincode');
	$booking['current_booking_date'] = $this->input->post('current_booking_date');
	$booking['current_booking_timeslot'] = $this->input->post('current_booking_timeslot');
	$booking['new_booking_date'] = $this->input->post('new_booking_date');
	$booking['new_booking_timeslot'] = $this->input->post('new_booking_timeslot');
	$data['update_date'] = date("Y-m-d h:i:s");
	$booking['booking_date'] = date('d-m-Y', strtotime($booking['booking_date']));
	$yy = date("y", strtotime($booking['booking_date']));
	$mm = date("m", strtotime($booking['booking_date']));
	$dd = date("d", strtotime($booking['booking_date']));
	$booking['amount_due'] = $booking['total_price'];
	$booking['quantity'] = 1;
	$booking['type'] = "Booking";
	$booking['booking_id'] = $booking_id;
	//Check to find which button is clicked
	if ($this->input->post('sbm') == "Edit Booking") {  //To edit an existing booking
	    $booking['booking_id'] = $booking_id;
	    $booking['potential_value'] = $this->input->post('potential_value');
	    $booking['current_status'] = "Pending";
	    //Update appliance details if required
	    $this->booking_model->update_appliance_details($booking);
	    //Update unit appliance
	    $this->booking_model->update_booking_unit_details($booking_id, $booking);

	    //Updating booking details
	    if ($this->booking_model->update_booking_details($booking_id, $booking)) {
		$query1 = $this->booking_model->booking_history_by_booking_id($booking['booking_id']);
		$months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		$mm = $months[$mm - 1];
		$booking['booking_date'] = $dd . $mm;
		if ($booking['booking_timeslot'] == "10AM-1PM") {
		    $booking['booking_timeslot'] = "1PM";
		} elseif ($booking['booking_timeslot'] == "1PM-4PM") {
		    $booking['booking_timeslot'] = "4PM";
		} elseif ($booking['booking_timeslot'] == "4PM-7PM") {
		    $booking['booking_timeslot'] = "7PM";
		}
		//-------Sending Email On Booking--------//
		$message = "Conratulations You have received an edited booking, details are mentioned below:
      <br>Customer Name: " . $query1[0]['name'] . "<br>Customer Phone Number: " . $query1[0]['phone_number'] . "<br>Customer email address: " . $query1[0]['user_email'] . "<br>Booking Id: " . $booking['booking_id'] . "<br>Service name:" . $query1[0]['services'] . "<br>Number of appliance: " . $booking['quantity'] . "<br>Booking Date: " . $booking['booking_date'] . "<br>Booking Timeslot: " . $booking['booking_timeslot'] . "<br>Amount Due: " . $query1[0]['amount_due'] . "<br>Your Booking Remark is: " . $booking['booking_remarks'] . "<br>Booking address: " . $booking['booking_address'] . "<br>Booking pincode: " . $query1[0]['booking_pincode'] . "<br><br>
        Appliance Details:<br>";
		$appliance = "";
		for ($i = 0; $i < $booking['quantity']; $i++) {
		    $appliance = "<br>Brand : " . $booking['appliance_brand'] . "<br>Category : " . $booking['appliance_category'] . "<br>Capacity : " . $booking['appliance_capacity'] .
			"<br>Selected service/s is/are: " . $booking['items_selected'] . "<br>Total price is: " . $booking['total_price'] . "<br>";
		    $message = $message . $appliance;
		}
		$message = $message . "<br> Thanks!!";

		$from = 'booking@247around.com';
		$to = "anuj@247around.com, nits@247around.com";
		$cc = "";
		$bcc = "";
		$subject = 'Booking Confirmation-AROUND';
		$attachment = "";
//		$this->sendMail($subject, $message, $to, $cc, $bcc);
		$this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment);
		redirect(base_url() . 'employee/booking/view', 'refresh');
	    } else {
		echo "Booking not inserted";
	    }
	} elseif ($this->input->post('sbm') == "Convert to Query") { //To convert booking to query
	    $booking['current_status'] = "FollowUp";
	    $booking['internal_status'] = "FollowUp";
	    $booking['potential_value'] = $this->input->post('potential_value');
	    //Add "Q-" into booking ID
	    $booking['booking_id'] = "Q-" . $booking['booking_id'];
	    //Update unit appliance, to update new booking id
	    $this->booking_model->update_booking_unit_details($booking_id, $booking);
	    if ($this->booking_model->update_booking_details($booking_id, $booking)) {
		$query1 = $this->booking_model->booking_history_by_booking_id($booking['booking_id']);
		$months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		$mm = $months[$mm - 1];
		$booking['booking_date'] = $dd . $mm;
		if ($booking['booking_timeslot'] == "10AM-1PM") {
		    $booking['booking_timeslot'] = "1PM";
		} elseif ($booking['booking_timeslot'] == "1PM-4PM") {
		    $booking['booking_timeslot'] = "4PM";
		} elseif ($booking['booking_timeslot'] == "4PM-7PM") {
		    $booking['booking_timeslot'] = "7PM";
		}
		//-------Sending Email On Booking--------//
		$message = "One booking has been converted to query, details are mentioned below:
                  <br>Customer Name: " . $query1[0]['name'] . "<br>Customer Phone Number: " . $query1[0]['phone_number'] . "<br>Customer email address: " . $query1[0]['user_email'] . "<br>Booking Id: " . $booking['booking_id'] . "<br>Service name:" . $query1[0]['services'] . "<br>Number of appliance: " . $query1[0]['quantity'] . "<br>Booking Date: " . $booking['booking_date'] . "<br>Booking Timeslot: " . $booking['booking_timeslot'] . "<br>Amount Due: " . $query1[0]['amount_due'] . "<br>Query Remark is: " . $booking['query_remarks'] . "<br>Booking address: " . $booking['booking_address'] . "<br>Booking pincode: " . $query1[0]['booking_pincode'] . "<br><br>
                Appliance Details:<br>";
		$appliance = "";
		for ($i = 0; $i < $booking['quantity']; $i++) {
		    $appliance = "<br>Brand : " . $booking['appliance_brand'] . "<br>Category : " . $booking['appliance_category'] . "<br>Capacity : " . $booking['appliance_capacity'] .
			"<br>Selected service/s is/are: " . $booking['items_selected'] . "<br>Total price is: " . $booking['total_price'] . "<br>";
		    $message = $message . $appliance;
		}
		$message = $message . "<br> Thanks!!";
		$from = 'booking@247around.com';
		$to = "anuj@247around.com, nits@247around.com";
		$cc = "";
		$bcc = "";
		$subject = 'Booking Confirmation-AROUND';
		$attachment = "";
//		$this->sendMail($subject, $message, $to, $cc, $bcc);
		$this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment);
		redirect(base_url() . 'employee/booking/view_pending_queries', 'refresh');
	    } else {
		echo "Query is not saved";
	    }
	}
    }

    /**
     *  @desc : This function is to get delete booking form
     *  @param : void
     *  @return : takes to view
     */
    function get_delete_booking_form() {
	$this->load->view('employee/header');
	$this->load->view('employee/delete_bookings');
    }

    function process_delete_booking_form() {
	$booking_id_from_textarea = $this->input->post('booking_id');
	//converting textarea string to array
	$booking_id_array = explode("\n", $booking_id_from_textarea);

	for ($i = 0; $i < count($booking_id_array); $i++) {
	    $booking_id = trim($booking_id_array[$i]);
	    $getbookingdetails = $this->booking_model->getbooking($booking_id);
	    if (empty($getbookingdetails)) {
		echo "This Booking Id does not Exist!";
	    } else {
		$appliance_id = $getbookingdetails[0]['appliance_id'];
		$this->booking_model->delete_booking_details($booking_id);
		$this->booking_model->delete_unit_booking_details($booking_id);
		$this->booking_model->delete_appliance_details($appliance_id);
	    }
	}
	redirect(base_url() . 'employee/booking/view', 'refresh');
    }

    /**
     *  @desc : This function is used to rebook cancel query
     *  @param : String (Booking Id)
     *  @param : String(Phone Number)
     *  @return : refirect user controller
     */
    function cancelled_booking_re_book($booking_id, $phone){
        $this->booking_model->change_booking_status($booking_id);
        redirect(base_url() . 'employee/user/finduser/0/0/' . $phone, 'refresh');
    }

    function get_state_by_city() {
        $city = $this->input->post('city');
        $state = $this->booking_model->selectSate($city);
        print_r($state);
    }  

}
