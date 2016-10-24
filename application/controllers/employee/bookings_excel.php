<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/** Error reporting */
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
//For infinite memory
ini_set('memory_limit', '-1');
//3600 seconds = 60 minutes
ini_set('max_execution_time', 36000);

define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

class bookings_excel extends CI_Controller {

    function __Construct() {
	parent::__Construct();
	$this->load->helper(array('form', 'url'));
	$this->load->helper('download');

	$this->load->library('form_validation');
	$this->load->library('s3');
	$this->load->library('PHPReport');
	$this->load->library('notify');
	$this->load->library('partner_utilities');

	$this->load->model('user_model');
	$this->load->model('booking_model');
	$this->load->model('partner_model');
	$this->load->model('vendor_model');


	if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
	    return TRUE;
	} else {
	    redirect(base_url() . "employee/login");
	}
    }

    /*
     * @desc: This function gets the page to upload booking excel
     * @param: void
     * @return: void
     */

    public function index() {
	$this->load->view('employee/header');
	$this->load->view('employee/upload_bookings_excel');
    }

    /*
     * @desc: This function is to add snapdeal leads from excel file,
     * deliveries for which have already happened.
     * This fn to be generalized later to take care all partners.
     *
     * @param: void
     * @return: list of transactions
     */

    public function add_booking_from_excel() {
	//TODO: Need to be changed
	//$partner_id = '1';
	//$partner_code = 'SS';

	if (!empty($_FILES['file']['name'])) {
	    $pathinfo = pathinfo($_FILES["file"]["name"]);

	    if ($pathinfo['extension'] == 'xlsx') {
		if ($_FILES['file']['size'] > 0) {
		    $inputFileName = $_FILES['file']['tmp_name'];
		    $inputFileExtn = 'Excel2007';
		}
	    } else {
		if ($pathinfo['extension'] == 'xls') {
		    if ($_FILES['file']['size'] > 0) {
			$inputFileName = $_FILES['file']['tmp_name'];
			$inputFileExtn = 'Excel5';
		    }
		}
	    }
	}

	try {
	    //$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
	    $objReader = PHPExcel_IOFactory::createReader($inputFileExtn);
	    $objPHPExcel = $objReader->load($inputFileName);
	} catch (Exception $e) {
	    die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
	}

	//  Get worksheet dimensions
	$sheet = $objPHPExcel->getSheet(0);
	$highestRow = $sheet->getHighestRow();
	$highestColumn = $sheet->getHighestColumn();
	//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
	//echo "highest row: ", $highestRow, EOL;
	//echo "highest col: ", $highestColumn, EOL;
	//echo "highest col index: ", $highestColumnIndex, EOL;

	$headings = $sheet->rangeToArray('A1:' . $highestColumn . 1, NULL, TRUE, FALSE);

	$headings_new = array();
	foreach ($headings as $heading) {
	    $heading = str_replace(array("/", "(", ")", "."), "", $heading);
	    array_push($headings_new, str_replace(array(" "), "_", $heading));
	}

	for ($row = 2, $i = 0; $row <= $highestRow; $row++, $i++) {
	    //  Read a row of data into an array
	    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
	    $rowData[0] = array_combine($headings_new[0], $rowData[0]);

	    //echo print_r($rowData[0], true), EOL;
	    if ($rowData[0]['Phone'] == "") {
		//echo print_r("Phone number null, break from this loop", true), EOL;
		break;
	    }

	    //Insert user if phone number doesn't exist
	    $output = $this->user_model->search_user(trim($rowData[0]['Phone']));
	    $state = "";

	    if (empty($output)) {
		//User doesn't exist
		$user['name'] = $rowData[0]['Customer_Name'];
		$user['phone_number'] = $rowData[0]['Phone'];
		$user['user_email'] = (isset($rowData[0]['Email_ID']) ? $rowData[0]['Email_ID'] : "");
		$user['home_address'] = $rowData[0]['Customer_Address'];
		$user['pincode'] = $rowData[0]['Pincode'];
		$user['city'] = $rowData[0]['CITY'];

		$state = $this->vendor_model->get_state_from_pincode($rowData[0]['Pincode']);

		$user['state'] = $state['state'];

		$user_id = $this->user_model->add_user($user);

		//echo print_r($user, true), EOL;
		//Add sample appliances for this user
		$count = $this->booking_model->getApplianceCountByUser($user_id);
		//Add sample appliances if user has < 5 appliances in wallet
		if ($count < 5) {
		    $this->booking_model->addSampleAppliances($user_id, 5 - intval($count));
		}
	    } else {
		//User exists
		$user_id = $output[0]['user_id'];
		$user['user_email'] = (isset($rowData[0]['Email_ID']) ? $rowData[0]['Email_ID'] : "");
		$user['name'] = $rowData[0]['Customer_Name'];
	    }

	    //Wybor brand should be tagged to Partner Wybor only if the
	    //States are:
            // - Tamilnadu (pincode starts from 6).
            // - AP / Telangana (pincode starts from 5)
            // (Karnataka also starts from 5, we will leave that as of now)
	    //Ray brand should be tagged to Ray.
	    //All other brands would go to Snapdeal.
	    switch ($rowData[0]['Brand']) {
		case 'Wybor':
		    if ((substr($rowData[0]['Pincode'], 0, 1) == "5") || 
                            (substr($rowData[0]['Pincode'], 0, 1) == "6")) {
			$booking['partner_id'] = '247010';
			$booking['source'] = "SY";
		    } else {
			$booking['partner_id'] = '1';
			$booking['source'] = "SS";
		    }

		    break;

		case 'Ray':
		    $booking['partner_id'] = '247011';
		    $booking['source'] = "SR";
		    break;

		default:
		    $booking['partner_id'] = '1';
		    $booking['source'] = "SS";
		    break;
	    }

	    //Add this lead into the leads table
	    //Check whether this is a new Lead or Not
	    $partner_booking = $this->partner_model->get_order_id_for_partner($booking['partner_id'], $rowData[0]['Sub_Order_ID']);
	    if (is_null($partner_booking)) {

		$prod = trim($rowData[0]['Product']);

		if (stristr($prod, "Washing Machine") || stristr($prod, "WashingMachine") || stristr($prod, "Dryer")) {
		    $prod = 'Washing Machine';
		}
		if (stristr($prod, "Television")) {
		    $prod = 'Television';
		}
		if (stristr($prod, "Airconditioner") || stristr($prod, "Air Conditioner")) {
		    $prod = 'Air Conditioner';
		}
		if (stristr($prod, "Refrigerator")) {
		    $prod = 'Refrigerator';
		}
		if (stristr($prod, "Microwave")) {
		    $prod = 'Microwave';
		}
		if (stristr($prod, "Purifier")) {
		    $prod = 'Water Purifier';
		}
		if (stristr($prod, "Chimney")) {
		    $prod = 'Chimney';
		}
		if (stristr($prod, "Geyser")) {
		    $prod = 'Geyser';
		}

		$appliance_details['user_id'] = $booking['user_id'] = $user_id;
		$appliance_details['service_id'] = $unit_details['service_id'] = $booking['service_id'] = $this->booking_model->getServiceId($prod);
		//echo "Service ID: " . $booking['service_id'] . PHP_EOL;

		$booking['booking_pincode'] = $rowData[0]['Pincode'];
		$appliance_details['brand'] = $unit_details['appliance_brand'] = $rowData[0]['Brand'];

		$yy = date("y");
		$mm = date("m");
		$dd = date("d");
		$booking['booking_id'] = str_pad($booking['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
		$booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($booking['user_id'])) + 1);
		$booking['booking_id'] = "Q-" . $booking['source'] . "-" . $booking['booking_id'];
		$unit_details['booking_id'] = $booking['booking_id'];

		$unit_details['partner_id'] = $booking['partner_id'];
		$appliance_details['category'] = $unit_details['appliance_category'] = '';
		$appliance_details['capacity'] = $unit_details['appliance_capacity'] = '';
		$appliance_details['description'] = $unit_details['appliance_description'] = $rowData[0]['Product_Type'];
		$appliance_details['model_number'] = $unit_details['model_number'] = $rowData[0]['Model'];
		$appliance_details['serial_number'] = $unit_details['serial_number'] = '';
		$appliance_details['tag'] = $unit_details['appliance_tag'] = $rowData[0]['Brand'] . " " . $prod;
		$appliance_details['purchase_month'] = $unit_details['purchase_month'] = date('m');
		$appliance_details['purchase_year'] = $unit_details['purchase_year'] = date('Y');
		$appliance_details['last_service_date'] = date('d-m-Y');

		$unit_details['appliance_id'] = $this->booking_model->addappliance($appliance_details);
		$unit_details['appliance_size'] = '';
		$unit_details['price_tags'] = '';
		$this->booking_model->addunitdetails($unit_details);

		$booking['order_id'] = $rowData[0]['Sub_Order_ID'];
		$ref_date = PHPExcel_Shared_Date::ExcelToPHPObject($rowData[0]['Referred_Date_and_Time']);
		$booking['reference_date'] = $ref_date->format('Y-m-d H:i:s');
		$booking['booking_date'] = '';
		$booking['booking_timeslot'] = '';
		$booking['request_type'] = 'Installation & Demo';
		$booking['partner_source'] = "Snapdeal-delivered-excel";
		$del_date = PHPExcel_Shared_Date::ExcelToPHPObject($rowData[0]['Delivery_Date']);
		$booking['delivery_date'] = $del_date->format('Y-m-d H:i:s');
		//since product is already delivered
		$booking['estimated_delivery_date'] = $del_date->format('Y-m-d H:i:s');

		$booking['booking_primary_contact_no'] = $rowData[0]['Phone'];
		$booking['booking_alternate_contact_no'] = '';
		$booking['current_status'] = "FollowUp";
		$booking['internal_status'] = "FollowUp";
		$booking['type'] = "Query";

		$booking['booking_address'] = $rowData[0]['Customer_Address'];
		$booking['amount_due'] = '';
		$booking['booking_remarks'] = '';
		$booking['query_remarks'] = 'Product Shipped, Call Customer For Booking';
		$booking['city'] = $rowData[0]['CITY'];

		$state = $this->vendor_model->get_state_from_pincode($rowData[0]['Pincode']);
		$booking['state'] = $state['state'];
		if (empty($booking['state'])) {
		    $to = "anuj@247around.com, nits@247around.com";
		    $message = "Pincode " . $booking['booking_pincode'] . " not found for Booking ID: " . $booking['booking_id'];
		    $this->notify->sendEmail("booking@247around.com", $to, "", "", 'Pincode Not Found', $message, "");
		}

		$booking['quantity'] = '1';
		$booking['potential_value'] = '';

		//Insert query
		//echo print_r($booking, true) . "<br><br>";
		$this->booking_model->addbooking($booking);
		$this->insert_booking_in_partner_leads($booking, $unit_details,$user, $prod );

		$this->notify->insert_state_change($booking['booking_id'], _247AROUND_FOLLOWUP , _247AROUND_NEW_QUERY , $this->session->userdata('id'), $this->session->userdata('employee_id'),_247AROUND);
		//Reset
		unset($appliance_details);
		unset($booking);
		//unset($lead_details);
	    } else {


		$status = $partner_booking['current_status'];
		$int_status = $partner_booking['internal_status'];

		//Clear the booking date so that it starts reflecting on our panel & update booking.
		//This should be done only if the booking has not been updated in the meanwhile.
		//If the booking has already been scheduled or cancelled, leave this as it is.
		//If the booking query remarks or internal status has been changed, then also leave it.
		//Update delivery date in both the cases
		$dateObj = PHPExcel_Shared_Date::ExcelToPHPObject($rowData[0]['Delivery_Date']);
		$data['delivery_date'] = $dateObj->format('Y-m-d H:i:s');

		if ($status == 'FollowUp' && $int_status == 'FollowUp') {
		    $data['booking_date'] = '';
		    $data['booking_timeslot'] = '';
		}

		log_message('info', __FUNCTION__ . 'Update Partned Lead (Delivered): ' .
		    print_r(array($partner_booking['booking_id'], $data), true));

		$this->booking_model->update_booking($partner_booking['booking_id'], $data);

		unset($data);
	    }
	}

	redirect(base_url() . DEFAULT_SEARCH_PAGE);
    }

    /*
     * @desc: This function is to upload the products that have been shipped from our partners
     * @param: void
     * @return: void
     */

    public function upload_shipped_products_excel() {
	$this->load->view('employee/header');
	$this->load->view('employee/upload_shippings_excel');
    }

    /*
     * @desc: This function is to add the uploaded products that have been shipped from our partners
     * @param: void
     * @return: void
     */

    public function add_snapdeal_shipped_products_from_excel() {
	log_message('info', __FUNCTION__);

	if (!empty($_FILES['file']['name'])) {
	    $pathinfo = pathinfo($_FILES["file"]["name"]);

	    if ($pathinfo['extension'] == 'xlsx') {
		if ($_FILES['file']['size'] > 0) {
		    $inputFileName = $_FILES['file']['tmp_name'];
		    $inputFileExtn = 'Excel2007';
		}
	    } else {
		if ($pathinfo['extension'] == 'xls') {
		    if ($_FILES['file']['size'] > 0) {
			$inputFileName = $_FILES['file']['tmp_name'];
			$inputFileExtn = 'Excel5';
		    }
		}
	    }
	}

	try {
	    //$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
	    $objReader = PHPExcel_IOFactory::createReader($inputFileExtn);
	    $objPHPExcel = $objReader->load($inputFileName);
	} catch (Exception $e) {
	    die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
	}

	//  Get worksheet dimensions
	$sheet = $objPHPExcel->getSheet(0);
	$highestRow = $sheet->getHighestRow();
	$highestColumn = $sheet->getHighestColumn();
	//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

	$headings = $sheet->rangeToArray('A1:' . $highestColumn . 1, NULL, TRUE, FALSE);

	$headings_new = array();
	foreach ($headings as $heading) {
	    $heading = str_replace(array("/", "(", ")", "."), "", $heading);
	    array_push($headings_new, str_replace(array(" "), "_", $heading));
	}

	for ($row = 2, $i = 0; $row <= $highestRow; $row++, $i++) {
	    //  Read a row of data into an array
	    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
	    $rowData[0] = array_combine($headings_new[0], $rowData[0]);

	    //echo print_r($rowData[0], true), EOL;
	    if ($rowData[0]['Phone'] == "") {
		//echo print_r("Phone number null, break from this loop", true), EOL;
		break;
	    }

	    //Insert user if phone number doesn't exist
	    $output = $this->user_model->search_user(trim($rowData[0]['Phone']));
	    $state = "";

	    if (empty($output)) {
		//User doesn't exist
		$user['name'] = $rowData[0]['Customer_Name'];
		$user['phone_number'] = $rowData[0]['Phone'];
		$user['user_email'] = (isset($rowData[0]['Email_ID']) ? $rowData[0]['Email_ID'] : "");
		$user['home_address'] = $rowData[0]['Customer_Address'];
		$user['pincode'] = $rowData[0]['Pincode'];
		$user['city'] = $rowData[0]['CITY'];

		$state = $this->vendor_model->get_state_from_pincode($rowData[0]['Pincode']);
		$user['state'] = $state['state'];

		$user_id = $this->user_model->add_user($user);

		//echo print_r($user, true), EOL;
		//Add sample appliances for this user
		$count = $this->booking_model->getApplianceCountByUser($user_id);
		//Add sample appliances if user has < 5 appliances in wallet
		if ($count < 5) {
		    $this->booking_model->addSampleAppliances($user_id, 5 - intval($count));
		}
	    } else {
		//User exists
		$user_id = $output[0]['user_id'];
		$user['name'] = $rowData[0]['Customer_Name'];
		$user['user_email'] = (isset($rowData[0]['Email_ID']) ? $rowData[0]['Email_ID'] : "");
	    }

	    //Wybor brand should be tagged to Partner Wybor only if the
	    //state is Tamilnadu (pincode starts from 6). Else it would be
	    //tagged to Snapdeal.
	    //Ray brand should be tagged to Ray.
	    //All other brands would go to Snapdeal.
	    switch ($rowData[0]['Brand']) {
		case 'Wybor':
		    if ((substr($rowData[0]['Pincode'], 0, 1) == "5") || 
                            (substr($rowData[0]['Pincode'], 0, 1) == "6")) {
			$booking['partner_id'] = '247010';
			$booking['source'] = "SY";
		    } else {
			$booking['partner_id'] = '1';
			$booking['source'] = "SS";
		    }

		    break;

		case 'Ray':
		    $booking['partner_id'] = '247011';
		    $booking['source'] = "SR";
		    break;

		default:
		    $booking['partner_id'] = '1';
		    $booking['source'] = "SS";
		    break;
	    }

	    //Add this lead into the leads table
	    //Check whether this is a new Lead or Not
	    $partner_booking = $this->partner_model->get_order_id_for_partner($booking['partner_id'], $rowData[0]['Sub_Order_ID']);
	    if (is_null($partner_booking)) {
		$booking['order_id'] = $rowData[0]['Sub_Order_ID'];
		$booking['booking_pincode'] = $rowData[0]['Pincode'];

		$dateObj1 = PHPExcel_Shared_Date::ExcelToPHPObject($rowData[0]['Referred_Date_and_Time']);
		$booking['reference_date'] = $dateObj1->format('d/m/Y');

		$unit_details['appliance_brand'] = $appliance_details['brand'] = $rowData[0]['Brand'];
		$unit_details['model_number'] = $appliance_details['model_number'] = $rowData[0]['Model'];
		//$unit_details['Product'] = '';

		$prod = trim($rowData[0]['Product']);

		//Initialize Air Conditioner type as Split
		$ac_type = 'Split';

		if (stristr($prod, "Washing Machine") || stristr($prod, "WashingMachine") || stristr($prod, "Dryer")) {
		    $lead_details['Product'] = 'Washing Machine';
		}
		if (stristr($prod, "Television")) {
		    $lead_details['Product'] = 'Television';
		}
		if (stristr($prod, "Airconditioner") || stristr($prod, "Air Conditioner")) {
		    $lead_details['Product'] = 'Air Conditioner';

		    if (stristr($rowData[0]['Product_Type'], "Window")) {
			$ac_type = 'Window';
		    }
		}
		if (stristr($prod, "Refrigerator")) {
		    $lead_details['Product'] = 'Refrigerator';
		}
		if (stristr($prod, "Microwave")) {
		    $lead_details['Product'] = 'Microwave';
		}
		if (stristr($prod, "Purifier")) {
		    $lead_details['Product'] = 'Water Purifier';
		}
		if (stristr($prod, "Chimney")) {
		    $lead_details['Product'] = 'Chimney';
		}

		if (stristr($prod, "Geyser")) {
		    $lead_details['Product'] = 'Geyser';
		}

		$unit_details['appliance_description'] = $appliance_details['description'] = $rowData[0]['Product_Type'];

		if (isset($rowData[0]['Expected_Delivery_Date']))
		    $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($rowData[0]['Expected_Delivery_Date']);
		else
		    $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($rowData[0]['Delivery_Date']);

		if ($dateObj2->format('d') == date('d')) {
		    //If date is NULL, add 3 days from today in EDD.
		    $dateObj2 = date_create('+3days');
		} else {
		    //If date is NOT NULL, leave EDD as it is.
		    //$dateObj2 = date_add($dateObj2, date_interval_create_from_date_string('2 days'));
		}
		log_message('info', print_r($dateObj2, true));

		$booking['estimated_delivery_date'] = $dateObj2->format('d/m/Y');

		//Add this as a Query now
		$booking['booking_id'] = '';
		$appliance_details['user_id'] = $booking['user_id'] = $user_id;
		$unit_details['service_id'] = $appliance_details['service_id'] = $booking['service_id'] = $this->booking_model->getServiceId($lead_details['Product']);
		log_message('info', __FUNCTION__ . "=> Service ID: " . $booking['service_id']);

		$booking['booking_alternate_contact_no'] = '';

		$yy = $dateObj2->format('y');
		$mm = $dateObj2->format('m');
		$dd = $dateObj2->format('d');
		$booking['booking_id'] = str_pad($booking['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
		$booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($booking['user_id'])) + 1);

		$unit_details['booking_id'] = $booking['booking_id'] = "Q-" . $booking['source'] . "-" . $booking['booking_id'];

		$booking['quantity'] = '1';
		$unit_details['partner_id'] = $booking['partner_id'];

		$appliance_details['category'] = $unit_details['appliance_category'] = '';
		$appliance_details['capacity'] = $unit_details['appliance_capacity'] = '';
		$appliance_details['tag'] = $unit_details['appliance_tag'] = $unit_details['appliance_brand'] . " " . $lead_details['Product'];
		$appliance_details['purchase_month'] = $unit_details['purchase_month'] = date('m');
		$appliance_details['purchase_year'] = $unit_details['purchase_year'] = date('Y');

		$booking['potential_value'] = '';
		$appliance_details['last_service_date'] = date('d-m-Y');

		$unit_details['appliance_id'] = $appliance_id = $this->booking_model->addappliance($appliance_details);
		$this->booking_model->addunitdetails($unit_details);


		$booking['query_remarks'] = "FollowUp";
		$booking['current_status'] = "FollowUp";
		$booking['type'] = "Query";
		$booking['booking_date'] = $dateObj2->format('d-m-Y');
		$booking['booking_timeslot'] = '10AM-1PM';
		$booking['booking_address'] = $rowData[0]['Customer_Address'];
		$booking['city'] = $rowData[0]['CITY'];
		$booking['booking_primary_contact_no'] = $rowData[0]['Phone'];
		$booking['partner_source'] = "Snapdeal-shipped-excel";
		$booking['request_type'] = "";
		$booking['amount_due'] = '';
		$booking['booking_remarks'] = '';
		$booking['query_remarks'] = 'Product Shipped, Call Customer For Booking';

		$state = $this->vendor_model->get_state_from_pincode($rowData[0]['Pincode']);
		$booking['state'] = $state['state'];
		if (empty($booking['state'])) {
		    $to = "anuj@247around.com, nits@247around.com";
		    $message = "Pincode " . $booking['booking_pincode'] . " not found for Booking ID: " . $booking['booking_id'];
		    $this->notify->sendEmail("booking@247around.com", $to, "", "", 'Pincode Not Found', $message, "");
		}
		$booking['create_date'] = date('Y-m-d H:i:s');


		//Insert query
		//echo print_r($booking, true) . "<br><br>";
		$this->booking_model->addbooking($booking);

		//Send SMS to customer about free installation
		switch ($lead_details['Product']) {
		    case 'Washing Machine':
			$sms['tag'] = "sd_shipped_free";
			$sms['smsData']['service'] = 'Washing Machine';
			$sms['smsData']['message'] = 'free installation';
			break;

		    case 'Refrigerator':
			$sms['tag'] = "sd_shipped_free";
			$sms['smsData']['service'] = 'Refrigerator';
			$sms['smsData']['message'] = 'free installation';
			break;

		    case 'Microwave':
			$sms['tag'] = "sd_shipped_free";
			$sms['smsData']['service'] = 'Microwave';
			$sms['smsData']['message'] = 'free installation';
			break;

		    case 'Television':
			$sms['tag'] = "sd_shipped_free";
			$sms['smsData']['service'] = 'TV';
			$sms['smsData']['message'] = 'free installation and wall-mounted stand';
			break;

		    case 'Water Purifier':
			$sms['tag'] = "sd_shipped_free";
			$sms['smsData']['service'] = 'Water Purifier';
			$sms['smsData']['message'] = 'free installation';
			break;

		    case 'Air Conditioner':
			$sms['tag'] = "sd_shipped_ac";

			if ($ac_type == 'Window') {
			    $sms['smsData']['message'] = 'Rs550';
			} else {
			    $sms['smsData']['message'] = 'Rs1400';
			}

			break;

		    default:
			break;
		}

		$this->insert_booking_in_partner_leads($booking, $unit_details,$user, $lead_details['Product'] );
		$this->notify->insert_state_change($booking['booking_id'], _247AROUND_FOLLOWUP , _247AROUND_NEW_QUERY , $this->session->userdata('id'), $this->session->userdata('employee_id'),_247AROUND);

		//$sms['phone_no'] = $lead_details['Phone'];
		//$sms['booking_id'] = $booking['booking_id'];
		// $this->notify->send_sms_acl($sms);
		//Reset
		unset($booking);
		unset($lead_details);
		unset($appliance_details);
		unset($unit_details);
	    } else {
		//Skip this request as it already exists
	    }
	}

	redirect(base_url() . DEFAULT_SEARCH_PAGE);
    }

    /*
     * @desc: This function is to upload the products that have been delevered from our partners
     * @param: void
     * @return: void
     */

    public function upload_delivered_products_for_paytm_excel() {
	$this->load->view('employee/header');
	$this->load->view('employee/upload_delivered_excel');
    }

    /*
     * @desc: This function is to add bookings from excel
     * @param: void
     * @return: list of transactions
     */

    public function upload_booking_for_paytm() {
	log_message('info', __FUNCTION__);

	if (!empty($_FILES['file']['name'])) {
	    $pathinfo = pathinfo($_FILES["file"]["name"]);

	    if ($pathinfo['extension'] == 'xlsx') {
		if ($_FILES['file']['size'] > 0) {
		    $inputFileName = $_FILES['file']['tmp_name'];
		    $inputFileExtn = 'Excel2007';
		}
	    } else if ($pathinfo['extension'] == 'xls') {
		    if ($_FILES['file']['size'] > 0) {
			$inputFileName = $_FILES['file']['tmp_name'];
			$inputFileExtn = 'Excel5';
		    }
		} else {

	    	$output = "File format is not correct. Only XLS or XLSX files are allowed.";
            $userSession = array('error' =>$output);
            $this->session->set_userdata($userSession);
	    	redirect(base_url()."employee/bookings_excel/upload_delivered_products_for_paytm_excel");

		}

	}

	try {
	    //$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
	    $objReader = PHPExcel_IOFactory::createReader($inputFileExtn);
	    $objPHPExcel = $objReader->load($inputFileName);
	} catch (Exception $e) {
	    die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
	}

	//  Get worksheet dimensions
	//$sheet = $objPHPExcel->setActiveSheetIndexbyName('Sheet1');
	$sheet = $objPHPExcel->getSheet(0);
	$highestRow = $sheet->getHighestRow();
	$highestColumn = $sheet->getHighestColumn();
	//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
	//echo "highest row: ", $highestRow, EOL;
	//echo "highest col: ", $highestColumn, EOL;
	//echo "highest col index: ", $highestColumnIndex, EOL;

	$headings = $sheet->rangeToArray('A1:' . $highestColumn . 1, NULL, TRUE, FALSE);

	$headings_new = array();
	foreach ($headings as $heading) {
	    $heading = str_replace(array("/", "(", ")", " ", "."), "", $heading);
	    array_push($headings_new, str_replace(array(" "), "_", $heading));
	}

	for ($row = 2, $i = 0; $row <= $highestRow; $row++, $i++) {
	    //  Read a row of data into an array
	    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
	    $rowData[0] = array_combine($headings_new[0], $rowData[0]);

	    //echo print_r($rowData[0], true), EOL;
	    if ($rowData[0]['CustomerContactNo'] == "") {
		//echo print_r("Phone number null, break from this loop", true), EOL;
		break;
	    }

	    //Insert user if phone number doesn't exist
	    $output = $this->user_model->search_user(trim($rowData[0]['CustomerContactNo']));

	    if (empty($output)) {
		//User doesn't exist
		$user['name'] = $rowData[0]['CustomerName'];
		$user['phone_number'] = $rowData[0]['CustomerContactNo'];
		$user['user_email'] = $rowData[0]['customer_email'];
		$user['home_address'] = $rowData[0]['CustomerAddress1'] . " ," . $rowData[0]['CustomerAddress2'];
		$user['pincode'] = $rowData[0]['CustomerPincode'];
		$user['city'] = $rowData[0]['CustomerCity'];
		$user['state'] = $rowData[0]['CustomerState'];
		$user['is_verified'] = 1;

		$user_id = $this->user_model->add_user($user);

		//echo print_r($user, true), EOL;
		//Add sample appliances for this user
		$count = $this->booking_model->getApplianceCountByUser($user_id);
		//Add sample appliances if user has < 5 appliances in wallet
		if ($count < 5) {
		    $this->booking_model->addSampleAppliances($user_id, 5 - intval($count));
		}
	    } else {
		//User exists
	    $user['name'] = $rowData[0]['CustomerName'];
	    $user['user_email'] = $rowData[0]['customer_email'];
		$user_id = $output[0]['user_id'];
	    }

	    //Add this lead into the leads table
	    //Check whether this is a new Lead or Not
	    //Pass order id and partner source
	    if ($this->booking_model->check_booking_exists_by_order_id($rowData[0]['OrderID'], "Paytm-delivered-excel") == FALSE) {
		$booking['order_id'] = $rowData[0]['OrderID'];

		//$lead_details['Unique_id'] = $rowData[0]['Item ID'];
		//$dateObj1 = PHPExcel_Shared_Date::ExcelToPHPObject($rowData[0]['Order Date']);
		//$lead_details['DeliveryDate'] = $dateObj1->format('d/m/Y');
		$appliance_details['brand'] = $unit_details['appliance_brand'] = $rowData[0]['Brand'];
		$appliance_details['model_number'] = $unit_details['model_number'] = "";

		$prod = trim($rowData[0]['ProductCategoryL3']);

		if (stristr($prod, "Washing Machine") || stristr($prod, "WashingMachine") || stristr($prod, "Dryer")) {
		    $lead_details['Product'] = 'Washing Machine';
		}
		if (stristr($prod, "Television")) {
		    $lead_details['Product'] = 'Television';
		}
		if (stristr($prod, "Airconditioner") || stristr($prod, "Air Conditioner")) {
		    $lead_details['Product'] = 'Air Conditioner';
		}
		if (stristr($prod, "Refrigerator")) {
		    $lead_details['Product'] = 'Refrigerator';
		}
		if (stristr($prod, "Microwave")) {
		    $lead_details['Product'] = 'Microwave';
		}
		if (stristr($prod, "Purifier")) {
		    $lead_details['Product'] = 'Water Purifier';
		}
		if (stristr($prod, "Chimney")) {
		    $lead_details['Product'] = 'Chimney';
		}
		if (stristr($prod, "Geyser")) {
		    $lead_details['Product'] = 'Geyser';
		}

		$appliance_details['description'] = $unit_details['appliance_description'] = $rowData[0]['ProductName'];

		$booking['booking_address'] = $rowData[0]['CustomerAddress1'] . " ," . $rowData[0]['CustomerAddress2'];
		$booking['booking_pincode'] = $rowData[0]['CustomerPincode'];
		$booking['city'] = $rowData[0]['CustomerCity'];
		$booking['state'] = $rowData[0]['CustomerState'];

		if (empty($booking['state'])) {
		    $to = "anuj@247around.com, nits@247around.com";
		    $message = "Pincode " . $booking['booking_pincode'] . " not found for Booking ID: " . $booking['booking_id'];
		    $this->notify->sendEmail("booking@247around.com", $to, "", "", 'Pincode Not Found', $message, "");
		}

		$booking['booking_primary_contact_no'] = $rowData[0]['CustomerContactNo'];

		$dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($rowData[0]['shippedDate']);
		$booking['shipped_date'] = date('Y-m-d H:i:s', strtotime($dateObj2->format('d-m-Y')));

		$booking['current_status'] = "FollowUp";
		$booking['internal_status'] = "FollowUp";

		//Add this as a Query now
		$booking['booking_id'] = '';
		$appliance_details['user_id'] = $booking['user_id'] = $user_id;
		$appliance_details['service_id'] = $unit_details['service_id'] = $booking['service_id'] = $this->booking_model->getServiceId($lead_details['Product']);
		log_message('info', __FUNCTION__ . "=> Service ID: " . $booking['service_id']);

		$booking['booking_alternate_contact_no'] = '';

		$yy = date("y");
		$mm = date("m");
		$dd = date("d");
		$booking['booking_id'] = str_pad($booking['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
		$booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($booking['user_id'])) + 1);

		//Wybor brand should be tagged to Partner Wybor only if the
		//state is Tamilnadu (pincode starts from 6). Else it would be
		//tagged to Paytm.
		//Ray brand should be tagged to Ray.
		//All other brands would go to Paytm.
		switch ($rowData[0]['Brand']) {
		    case 'Wybor':
			if ((substr($rowData[0]['Pincode'], 0, 1) == "5") ||
                                (substr($rowData[0]['Pincode'], 0, 1) == "6")) {
			    $booking['partner_id'] = '247010';
			    $booking['source'] = "SY";
			} else {
			    $booking['partner_id'] = '3';
			    $booking['source'] = "SP";
			}

			break;

		    case 'Ray':
			$booking['partner_id'] = '247011';
			$booking['source'] = "SR";
			break;

		    default:
			$booking['partner_id'] = '3';
			$booking['source'] = "SP";
			break;
		}

		$unit_details['booking_id'] = $booking['booking_id'] = "Q-" . $booking['source'] . "-" . $booking['booking_id'];

		$unit_details['partner_id'] = $booking['partner_id'];
		$booking['quantity'] = '1';
		$appliance_details['category'] = $unit_details['appliance_category'] = '';
		$appliance_details['capacity'] = $unit_details['appliance_capacity'] = '';
		$appliance_details['tag'] = $unit_details['appliance_tag'] = $unit_details['brand'] . " " . $unit_details['appliance_description'];
		$appliance_details['purchase_month'] = $unit_details['purchase_month'] = date('m');
		$appliance_details['purchase_year'] = $unit_details['purchase_year'] = date('Y');
		$booking['partner_source'] = "Paytm-delivered-excel";

		$booking['potential_value'] = '';
		$appliance_details['last_service_date'] = date('d-m-Y');

		$unit_details['appliance_id'] = $this->booking_model->addappliance($appliance_details);

		$return_unit_id = $this->booking_model->addunitdetails($unit_details);
		if (!$return_unit_id) {
		    log_message('info', __FUNCTION__ . 'Error Paytm booking unit not inserted: ' . print_r($unit_details, true));
		}

		$booking['type'] = "Query";
		$booking['booking_date'] = '';
		$booking['booking_timeslot'] = '';
		$booking['amount_due'] = '';
		$booking['booking_remarks'] = '';
		$booking['query_remarks'] = '';

		//Insert query
		//echo print_r($booking, true) . "<br><br>";
		$return_id = $this->booking_model->addbooking($booking);
		if (!$return_id) {
		    log_message('info', __FUNCTION__ . 'Error Paytm booking details not inserted: ' . print_r($booking, true));
		}

		$this->insert_booking_in_partner_leads($booking, $unit_details,$user, $lead_details['Product'] );
		$this->notify->insert_state_change($booking['booking_id'], _247AROUND_FOLLOWUP , _247AROUND_NEW_QUERY , $this->session->userdata('id'), $this->session->userdata('employee_id'),_247AROUND);
		//Reset
		unset($booking);
		unset($unit_details);
		unset($appliance_details);
	    }
	}

	redirect(base_url() . DEFAULT_SEARCH_PAGE);
    }

     /**
     * @desc: This method used to insert data into partner leads table.
     * @param: Array Booking details
     * @param: Array Unit details
     * @param: Array User details
     * @param: String Service Name
     */
    function insert_booking_in_partner_leads($booking, $unit_details, $user_details, $product){
    	$partner_booking['PartnerID'] = $booking['partner_id'];
    	$partner_booking['OrderID'] = $booking['order_id'];
    	$partner_booking['247aroundBookingID'] = $booking['booking_id'];
    	$partner_booking['Product'] = $product;
    	$partner_booking['Brand'] = $unit_details['appliance_brand'];
    	$partner_booking['Model'] = $unit_details['model_number'];
    	$partner_booking['ProductType'] = $unit_details['appliance_description'];
    	$partner_booking['Category'] = $unit_details['appliance_category'];
    	$partner_booking['Name'] = $user_details['name'];
    	$partner_booking['Mobile'] =  $booking['booking_primary_contact_no'];
    	$partner_booking['AlternatePhone'] =  $booking['booking_alternate_contact_no'];
    	$partner_booking['Email'] = $user_details['user_email'];
    	$partner_booking['Landmark'] = $booking['booking_landmark'];
    	$partner_booking['Address'] = $booking['booking_address'];
    	$partner_booking['Pincode'] = $booking['booking_pincode'];
    	$partner_booking['City'] = $booking['city'];
    	$partner_booking['DeliveryDate'] = $booking['delivery_date'];
    	$partner_booking['RequestType'] = $booking['request_type'];
    	$partner_booking['ScheduledAppointmentDate'] = $booking['booking_date'];
    	$partner_booking['ScheduledAppointmentTime'] = $booking['booking_timeslot'];
    	$partner_booking['Remarks'] = $booking['booking_remarks'];
    	$partner_booking['PartnerRequestStatus'] = "";
    	$partner_booking['247aroundBookingStatus'] = "FollowUp";
    	$partner_booking['247aroundBookingRemarks'] = "FollowUp";
    	$partner_booking['create_date'] = date('Y-m-d H:i:s');

    	$partner_leads_id = $this->partner_model->insert_partner_lead($partner_booking);
    	if($partner_leads_id){
    		return true;
    	} else {
           log_message('info', __FUNCTION__." Booking is not inserted into Partner Leads table:". print_r($partner_booking, true));
    	}

    }

}
