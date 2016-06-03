<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//error_reporting(E_ERROR);
//ini_set('display_errors', '1');

/**
 * REST APIs for Partners like Snapdeal to insert / update orders in our CRM
 *
 * @author anujaggarwal
 */
define('SUCCESS_CODE', 247);
define('SUCCESS_MSG', 'Success');

define('ERR_GENERIC_ERROR_CODE', -1000);
define('ERR_INVALID_AUTH_TOKEN_CODE', -1001);
define('ERR_MOBILE_NUM_MISSING_CODE', -1002);
define('ERR_ORDER_ID_EXISTS_CODE', -1003);
define('ERR_MANDATORY_PARAMETER_MISSING_CODE', -1004);
define('ERR_INVALID_PRODUCT_CODE', -1005);
define('ERR_INVALID_REQUEST_TYPE_CODE', -1006);
define('ERR_ORDER_ID_NOT_FOUND_CODE', -1007);
define('ERR_INVALID_BOOKING_ID_CODE', -1008);
define('ERR_REQUEST_ALREADY_COMPLETED_CODE', -1009);
define('ERR_REQUEST_ALREADY_CANCELLED_CODE', -1010);
define('ERR_REQUEST_BEYOND_CUTOFF_TIME_CODE', -1011);
define('ERR_INVALID_DATE_FORMAT_CODE', -1012);
define('ERR_INVALID_TIMESLOT_FORMAT_CODE', -1013);
define('ERR_INVALID_INSTALLATION_TIMESLOT_CODE', -1014);
define('ERR_INVALID_PARTNER_NAME_CODE', -1015);
define('ERR_INVALID_JSON_INPUT_CODE', -1016);


define('ERR_GENERIC_ERROR_MSG', 'Unknown Error');
define('ERR_INVALID_AUTH_TOKEN_MSG', 'Invalid Auth Token');
define('ERR_MOBILE_NUM_MISSING_MSG', 'Mobile Number Missing');
define('ERR_ORDER_ID_EXISTS_MSG', 'Order ID Exists');
define('ERR_MANDATORY_PARAMETER_MISSING_MSG', 'Mandatory Parameter is Missing');
define('ERR_INVALID_PRODUCT_MSG', 'Invalid Product');
define('ERR_INVALID_REQUEST_TYPE_MSG', 'Invalid Request Type');
define('ERR_ORDER_ID_NOT_FOUND_MSG', 'Order ID Not Found');
define('ERR_INVALID_BOOKING_ID_MSG', 'Invalid Booking ID');
define('ERR_REQUEST_ALREADY_COMPLETED_MSG', 'Request is Already Completed');
define('ERR_REQUEST_ALREADY_CANCELLED_MSG', 'Request is Already Cancelled');
define('ERR_REQUEST_BEYOND_CUTOFF_TIME_MSG', 'Request Beyond Cutoff Time');
define('ERR_INVALID_DATE_FORMAT_MSG', 'Invalid Date Format');
define('ERR_INVALID_TIMESLOT_FORMAT_MSG', 'Invalid Timeslot Format');
define('ERR_INVALID_INSTALLATION_TIMESLOT_MSG', 'Invalid Installation Timeslot');
define('ERR_INVALID_PARTNER_NAME_MSG', 'Invalid Partner Name');
define('ERR_INVALID_JSON_INPUT_MSG', 'Invalid JSON Input');

class Partner extends CI_Controller {

    private $token = null;
    private $header = null;
    private $partner = null;
    private $requestUrl = '';
    private $jsonRequestData = null;
    private $jsonResponseString;
    //private $debug;

    function __Construct() {
	parent::__Construct();

	$this->load->model('partner_model');
	$this->load->model('user_model');
	$this->load->model('booking_model');

	$this->load->library('email');
	$this->load->helper(array('form', 'url'));
    }

    public function index() {
	log_message('info', "Entering: " . __METHOD__);

	$activity = array('activity' => __METHOD__);
	$this->partner_model->log_partner_activity($activity);

	echo "Success";
    }

    /**
     * Insert new order
     *
     * API to insert new order in the CRM
     *
     * @access	public
     * @return	Success / Error code as per the document
     */
    public function submitRequest() {
	log_message('info', "Entering: " . __METHOD__);
	$this->requestUrl = __METHOD__;

	//Default values
	$this->jsonResponseString['response'] = NULL;
	$this->jsonResponseString['code'] = ERR_GENERIC_ERROR_CODE;
	$this->jsonResponseString['result'] = ERR_GENERIC_ERROR_MSG;

	//Save header / ip address in DB
	$h = $this->getallheaders();
	if ($h === FALSE) {
	    $this->sendJsonResponse(array(ERR_GENERIC_ERROR_CODE, ERR_GENERIC_ERROR_MSG));
	} else {
	    $this->header = json_encode($h);
	    $this->token = $h['Authorization'];

	    //Validate token
	    $this->partner = $this->partner_model->validate_partner($this->token);
	    if ($this->partner !== FALSE) {
		log_message('info', __METHOD__ . ":: Token validated (Partner ID: " . $this->partner['id'] . ")");

		//Token validated
		$input_d = file_get_contents('php://input');
		$requestData = json_decode($input_d, TRUE);

		if (!(json_last_error() === JSON_ERROR_NONE)) {
		    log_message('info', __METHOD__ . ":: Invalid JSON");

		    //Invalid json
		    $this->jsonRequestData = $input_d;
		    $this->sendJsonResponse(array(ERR_INVALID_JSON_INPUT_CODE, ERR_INVALID_JSON_INPUT_MSG));
		} else {
		    $this->jsonRequestData = $input_d;
		    //Check whether the required details are present in the request
		    //And request doesn't exist in database
		    $is_valid = $this->validate_submit_request_data($requestData);
		    if ($is_valid['result'] == TRUE) {
			log_message('info', __METHOD__ . ":: Request validated");

			//Search for user
			//Insert user if phone number doesn't exist
			$output = $this->user_model->search_user($requestData['mobile']);

			if (empty($output)) {
			    log_message('info', $requestData['mobile'] . ' does not exist');

			    //User doesn't exist
			    $user['name'] = $requestData['name'];
			    $user['phone_number'] = $requestData['mobile'];
			    $user['alternate_phone_number'] = (isset($requestData['alternatePhone']) ? $requestData['alternatePhone'] : "");
			    $user['user_email'] = (isset($requestData['email']) ? $requestData['email'] : "");

			    isset($requestData['landmark']) ?
				    ($address = $requestData['address'] . ", " . $requestData['landmark']) :
				    ($address = $requestData['address']);

			    $user['home_address'] = $address;
			    $user['pincode'] = $requestData['pincode'];
			    $user['city'] = $requestData['city'];

			    $user_id = $this->user_model->add_user($user);

			    //echo print_r($user, true), EOL;
			    //Add sample appliances for this user
			    $count = $this->booking_model->getApplianceCountByUser($user_id);

			    //Add sample appliances if user has < 5 appliances in wallet
			    if ($count < 5) {
				$this->booking_model->addSampleAppliances($user_id, 5 - intval($count));
			    }
			} else {
			    log_message('info', $requestData['mobile'] . ' exists');
			    //User exists
			    $user_id = $output[0]['user_id'];
			}

			$lead_details['PartnerID'] = $this->partner['id'];
			$lead_details['orderID'] = $requestData['orderID'];
			$lead_details['Brand'] = $requestData['brand'];
			$lead_details['Model'] = (isset($requestData['model']) ? $requestData['model'] : "");

			log_message('info', 'Product type: ' . $requestData['product']);
			$prod = trim($requestData['product']);

			if (stristr($prod, "Washing Machine") || stristr($prod, "WashingMachine") || stristr($prod, "Dryer")) {
			    $lead_details['Product'] = 'Washing Machine';
			}
			if (stristr($prod, "Television") || stristr($prod, "Monitor")) {
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
			log_message('info', 'Product type matched: ' . $lead_details['Product']);

			//Product description
			$lead_details['ProductType'] = $requestData['productType'];

			//Check for all optional parameters before setting them
			$lead_details['Category'] = (isset($requestData['category']) ? $requestData['category'] : "");
			$lead_details['SubCategory'] = (isset($requestData['subCategory']) ? $requestData['subCategory'] : "");
			$lead_details['Name'] = $requestData['name'];
			$lead_details['Mobile'] = $requestData['mobile'];
			$lead_details['AlternatePhone'] = (isset($requestData['alternatePhone']) ? $requestData['alternatePhone'] : "");
			$lead_details['Email'] = (isset($requestData['email']) ? $requestData['email'] : "");
			$lead_details['Address'] = $requestData['address'];
			$lead_details['Landmark'] = (isset($requestData['landmark']) ? $requestData['landmark'] : "");
			$lead_details['Pincode'] = $requestData['pincode'];
			$lead_details['City'] = $requestData['city'];

			$lead_details['DeliveryDate'] = $this->getDateTime($requestData['deliveryDate']);

			$lead_details['RequestType'] = $requestData['requestType'];
			$lead_details['Remarks'] = (isset($requestData['remarks']) ? $requestData['remarks'] : "");

			$lead_details['PartnerRequestStatus'] = "";
			$lead_details['PartnerRequestRemarks'] = "";
			$lead_details['ScheduledAppointmentDate'] = "";
			$lead_details['ScheduledAppointmentTime'] = "";
			$lead_details['247aroundBookingStatus'] = "FollowUp";
			$lead_details['247aroundBookingRemarks'] = "";

			//Add this as a Query now
			$booking['booking_id'] = '';
			$booking['user_id'] = $user_id;
			$booking['service_id'] = $this->booking_model->getServiceId($lead_details['Product']);
			log_message('info', __METHOD__ . ":: Service ID: " . $booking['service_id']);
			//echo "Service ID: " . $booking['service_id'] . PHP_EOL;

			$booking['booking_primary_contact_no'] = $lead_details['Mobile'];
			$booking['booking_alternate_contact_no'] = $lead_details['AlternatePhone'];

			$yy = date("y");
			$mm = date("m");
			$dd = date("d");
			$booking['booking_id'] = str_pad($booking['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
			$booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($booking['user_id'])) + 1);

			//Add partner code from sources table
			//All partners should have a valid partner code in the bookings_sources table
			$booking['source'] = $this->partner_model->get_source_code_for_partner($this->partner['id']);
			$booking['partner_id'] = $this->partner['id'];
			$booking['booking_id'] = "Q-" . $booking['source'] . "-" . $booking['booking_id'];
			$lead_details['247aroundBookingID'] = $booking['booking_id'];

			$booking['quantity'] = '1';
			$booking['appliance_brand'] = $lead_details['Brand'];
			$booking['appliance_category'] = '';
			$booking['appliance_capacity'] = '';
			$booking['description'] = $lead_details['ProductType'];
			$booking['model_number'] = (isset($lead_details['Model']) ? $lead_details['Model'] : "");
			$booking['appliance_tags'] = $lead_details['Brand'] . " " . $lead_details['Product'];
			$booking['purchase_month'] = date('m');
			$booking['purchase_year'] = date('Y');

			$booking['items_selected'] = '';
			$booking['total_price'] = '';
			$booking['potential_value'] = '';
			$booking['last_service_date'] = date('d-m-Y');

			//echo print_r($booking, true) . "<br><br>";
			$appliance_id = $this->booking_model->addexcelappliancedetails($booking);
			//echo print_r($appliance_id, true) . "<br><br>";
			$this->booking_model->addapplianceunitdetails($booking);

			$booking['current_status'] = "FollowUp";
			$booking['internal_status'] = "FollowUp";
			$booking['type'] = "Query";
			$booking['booking_date'] = '';
			$booking['booking_timeslot'] = '';
			$booking['booking_address'] = $lead_details['Address'] . ", " . $lead_details['Landmark'] .
			    ", " . $lead_details['City'];
			$booking['booking_pincode'] = $lead_details['Pincode'];
			$booking['amount_due'] = '';
			$booking['booking_remarks'] = '';
			$booking['query_remarks'] = '';

			//Insert query
			//echo print_r($booking, true) . "<br><br>";
			$this->booking_model->addbooking($booking, $appliance_id, $requestData['city']);

			//Save this in SD leads table
			//echo print_r($lead_details, true) . "<br><br>";
			$this->partner_model->insert_partner_lead($lead_details);

			//Send response
			$this->jsonResponseString['response'] = array(
			    "orderID" => $lead_details['orderID'],
			    "247aroundBookingID" => $lead_details['247aroundBookingID'],
			    "247aroundBookingStatus" => $lead_details['247aroundBookingStatus']);
			$this->sendJsonResponse(array(SUCCESS_CODE, SUCCESS_MSG));
		    } else {
			log_message('info', __METHOD__ . ":: Request validation fails. " . print_r($is_valid, true));

			//Request validation fails
			//If it is because of pre-existing order id, return booking id as a part of response
			if ($is_valid['code'] == ERR_ORDER_ID_EXISTS_CODE) {
			    log_message('info', "Reason: ERR_ORDER_ID_EXISTS_CODE");

			    $lead_details = $is_valid['lead'];
			    $this->jsonResponseString['response'] = array(
				"247aroundBookingID" => $lead_details['247aroundBookingID'],
				"247aroundBookingStatus" => $lead_details['247aroundBookingStatus'],
				"247aroundBookingRemarks" => $lead_details['247aroundBookingRemarks']);

			    $this->sendJsonResponse(array($is_valid['code'], $is_valid['msg']));
			} else {
			    $this->jsonResponseString['response'] = NULL;
			    $this->sendJsonResponse(array($is_valid['code'], $is_valid['msg']));
			}
		    }
		}
	    } else {
		log_message('info', __METHOD__ . ":: Invalid token: " . $this->token);

		//invalid token
		$this->jsonResponseString['response'] = NULL;
		$this->sendJsonResponse(array(ERR_INVALID_AUTH_TOKEN_CODE, ERR_INVALID_AUTH_TOKEN_MSG));
	    }
	}
    }

    /**
     * Cancel an existing order
     *
     * API to cancel an existing order in the CRM
     *
     * @access	public
     * @return	Success / Error code as per the document
     */
    public function cancelRequest() {
	log_message('info', "Entering: " . __METHOD__);
	$this->requestUrl = __METHOD__;

	//Default values
	$this->jsonResponseString['response'] = NULL;
	$this->jsonResponseString['code'] = ERR_GENERIC_ERROR_CODE;
	$this->jsonResponseString['result'] = ERR_GENERIC_ERROR_MSG;

	//Save header / ip address in DB
	$h = $this->getallheaders();
	if ($h === FALSE) {
	    $this->sendJsonResponse(array(ERR_GENERIC_ERROR_CODE, ERR_GENERIC_ERROR_MSG));
	} else {
	    $this->header = json_encode($h);
	    $this->token = $h['Authorization'];

	    //Validate token
	    $this->partner = $this->partner_model->validate_partner($this->token);
	    if ($this->partner !== FALSE) {
		//Token validated
		$input_d = file_get_contents('php://input');
		$requestData = json_decode($input_d, TRUE);

		if (!(json_last_error() === JSON_ERROR_NONE)) {
		    log_message('info', __METHOD__ . ":: Invalid JSON");

		    //invalid json
		    $this->jsonRequestData = $input_d;
		    $this->sendJsonResponse(array(ERR_INVALID_JSON_INPUT_CODE, ERR_INVALID_JSON_INPUT_MSG));
		} else {
		    $this->jsonRequestData = $input_d;

		    //1. Validate request - check all essential parameters are there
		    //2. Check order id and booking id corresponds to this partner and
		    //both are valid
		    $is_valid = $this->validate_cancel_request_data($requestData);
		    if ($is_valid['result'] == TRUE) {
			//Cancel current request
			$lead_details = $is_valid['lead'];
			if ($this->process_cancel_request($requestData) === TRUE) {
			    //Send response
			    $this->jsonResponseString['response'] = array(
				"247aroundBookingID" => $lead_details['247aroundBookingID'],
				"247aroundBookingStatus" => "Cancelled");
			    $this->sendJsonResponse(array(SUCCESS_CODE, SUCCESS_MSG));
			} else {
			    log_message('info', __METHOD__ . ":: Cancellation fails.");

			    //Some error occured while cancelling
			    $this->sendJsonResponse(array(ERR_GENERIC_ERROR_CODE,
				ERR_GENERIC_ERROR_MSG));
			}
		    } else {
			log_message('info', __METHOD__ . ":: Request validation fails. " . print_r($is_valid, true));

			//Request validation fails
			$this->sendJsonResponse(array($is_valid['code'], $is_valid['msg']));
		    }
		}
	    } else {
		log_message('info', __METHOD__ . ":: Invalid token: " . $this->token);

		//invalid token
		$this->sendJsonResponse(array(ERR_INVALID_AUTH_TOKEN_CODE, ERR_INVALID_AUTH_TOKEN_MSG));
	    }
	}
    }


    /**
     * Schedule timeslot for an order
     *
     * Update an existing order for a new timeslot
     *
     * @access	public
     * @return	Success / Error code as per the document
     */
    public function updateRequestTimeslot() {
	log_message('info', "Entering: " . __METHOD__);
	$this->requestUrl = __METHOD__;

	//Default values
	$this->jsonResponseString['response'] = NULL;
	$this->jsonResponseString['code'] = ERR_GENERIC_ERROR_CODE;
	$this->jsonResponseString['result'] = ERR_GENERIC_ERROR_MSG;

	//Save header / ip address in DB
	$h = $this->getallheaders();
	if ($h === FALSE) {
	    $this->sendJsonResponse(array(ERR_GENERIC_ERROR_CODE, ERR_GENERIC_ERROR_MSG));
	} else {
	    $this->header = json_encode($h);
	    $this->token = $h['Authorization'];

	    //Validate token
	    $this->partner = $this->partner_model->validate_partner($this->token);
	    if ($this->partner !== FALSE) {
		//Token validated
		$input_d = file_get_contents('php://input');
		$requestData = json_decode($input_d, TRUE);

		if (!(json_last_error() === JSON_ERROR_NONE)) {
		    log_message('info', __METHOD__ . ":: Invalid JSON");

		    //invalid json
		    $this->jsonRequestData = $input_d;
		    $this->sendJsonResponse(array(ERR_INVALID_JSON_INPUT_CODE, ERR_INVALID_JSON_INPUT_MSG));
		} else {
		    $this->jsonRequestData = $input_d;

		    //1. Validate request - check all essential parameters are there
		    //2. Check order id and booking id corresponds to this partner and
		    //both are valid
		    $is_valid = $this->validate_update_timeslot_data($requestData);
		    if ($is_valid['result'] == TRUE) {
			//Schedule request
			$lead_details = $is_valid['lead'];
			$sch_req = $this->process_schedule_request($requestData);

			if ($sch_req['result'] === TRUE) {
			    //Scheduling successful, Send response
			    $this->jsonResponseString['response'] = array(
				"247aroundBookingID" => $lead_details['247aroundBookingID'],
				"installationTimeslotStart" => $sch_req['timeslotStart'],
				"installationTimeslotEnd" => $sch_req['timeslotEnd']);
			    $this->sendJsonResponse(array(SUCCESS_CODE, SUCCESS_MSG));
			} else {
			    log_message('info', __METHOD__ . ":: Scheduling fails. " . print_r($sch_req, true));

			    //Some error occured while cancelling
			    $this->sendJsonResponse(array(ERR_GENERIC_ERROR_CODE,
				ERR_GENERIC_ERROR_MSG));
			}
		    } else {
			log_message('info', __METHOD__ . ":: Request validation fails. " . print_r($is_valid, true));

			//Request validation fails
			$this->sendJsonResponse(array($is_valid['code'], $is_valid['msg']));
		    }
		}
	    } else {
		log_message('info', __METHOD__ . ":: Invalid token: " . $this->token);

		//invalid token
		$this->sendJsonResponse(array(ERR_INVALID_AUTH_TOKEN_CODE, ERR_INVALID_AUTH_TOKEN_MSG));
	    }
	}
    }

    /**
     * Get status
     *
     * API to find out the current status of an existing order
     *
     * @access	public
     * @return	Success / Error code as per the document
     */
    public function getRequestStatus() {
	log_message('info', "Entering: " . __METHOD__);
	$this->requestUrl = __METHOD__;

	//Default values
	$this->jsonResponseString['response'] = NULL;
	$this->jsonResponseString['code'] = ERR_GENERIC_ERROR_CODE;
	$this->jsonResponseString['result'] = ERR_GENERIC_ERROR_MSG;

	//Save header / ip address in DB
	$h = $this->getallheaders();
	if ($h === FALSE) {
	    $this->sendJsonResponse(array(ERR_GENERIC_ERROR_CODE, ERR_GENERIC_ERROR_MSG));
	} else {
	    $this->header = json_encode($h);
	    $this->token = $h['Authorization'];

	    //Validate token
	    $this->partner = $this->partner_model->validate_partner($this->token);
	    if ($this->partner !== FALSE) {
		//Token validated
		$input_d = file_get_contents('php://input');
		$requestData = json_decode($input_d, TRUE);

		if (!(json_last_error() === JSON_ERROR_NONE)) {
		    log_message('info', __METHOD__ . ":: Invalid JSON");

		    //invalid json
		    $this->jsonRequestData = $input_d;
		    $this->sendJsonResponse(array(ERR_INVALID_JSON_INPUT_CODE, ERR_INVALID_JSON_INPUT_MSG));
		} else {
		    $this->jsonRequestData = $input_d;

		    //1. Validate request - check all essential parameters are there
		    //2. Check order id and booking id corresponds to this partner and
		    //both are valid
		    $is_valid = $this->validate_get_request_data($requestData);
		    if ($is_valid['result'] == TRUE) {
			//Send response
			$lead_details = $is_valid['lead'];
			$this->jsonResponseString['response'] = array(
			    "247aroundBookingID" => $lead_details['247aroundBookingID'],
			    "247aroundBookingStatus" => $lead_details['247aroundBookingStatus'],
			    "247aroundBookingRemarks" => $lead_details['247aroundBookingRemarks']);
			$this->sendJsonResponse(array(SUCCESS_CODE, SUCCESS_MSG));
		    } else {
			log_message('info', __METHOD__ . ":: Request validation fails. " . print_r($is_valid, true));

			//Request validation fails
			$this->sendJsonResponse(array($is_valid['code'], $is_valid['msg']));
		    }
		}
	    } else {
		log_message('info', __METHOD__ . ":: Invalid token: " . $this->token);

		//invalid token
		$this->sendJsonResponse(array(ERR_INVALID_AUTH_TOKEN_CODE, ERR_INVALID_AUTH_TOKEN_MSG));
	    }
	}
    }

    /*
     * Dummy function to test Partner Update Status API.
     * It just returns the POST data it has received.
     */

    function updateStatus() {
	log_message('info', "Entering: " . __METHOD__);
	$this->requestUrl = __METHOD__;

	//Default values
	$this->jsonResponseString['response'] = NULL;
	$this->jsonResponseString['code'] = ERR_GENERIC_ERROR_CODE;
	$this->jsonResponseString['result'] = ERR_GENERIC_ERROR_MSG;

	//Save header / ip address in DB
	$h = $this->getallheaders();
	if ($h === FALSE) {
//	    $activity = array('activity' => __METHOD__ . '::Headers', 'data' => NULL);
//	    $this->partner_model->log_partner_activity($activity);
//	    $this->sendJsonResponse(array(ERR_GENERIC_ERROR_CODE, ERR_GENERIC_ERROR_MSG));

	    log_message('info', __METHOD__ . "=> " . ERR_GENERIC_ERROR_MSG);
	} else {
	    $this->header = json_encode($h);

//	    $activity = array('activity' => __METHOD__ . '::Headers', 'data' => json_encode($h));
//	    $this->partner_model->log_partner_activity($activity);

	    $input_d = file_get_contents('php://input');
	    $requestData = json_decode($input_d, TRUE);

	    $this->jsonRequestData = $input_d;

	    $this->jsonResponseString['response'] = array("headers" => $h, "data" => $requestData);
	    $this->sendJsonResponse(array(SUCCESS_CODE, SUCCESS_MSG));

	    log_message('info', __METHOD__ . "=> " . json_encode(array("headers" => $h, "data" => $requestData), JSON_UNESCAPED_SLASHES));
	}
    }

    //Validate new request data
    function validate_submit_request_data($request) {
	log_message('info', "Entering: " . __METHOD__);

	//Lead will store the booking entry if it exists
	$resultArr = array("result" => FALSE, "lead" => NULL, "code" => NULL, "msg" => NULL);
	$flag = TRUE;

	//Validate Partner Name
	if ($request['partnerName'] != $this->partner['public_name']) {
	    $resultArr['code'] = ERR_INVALID_PARTNER_NAME_CODE;
	    $resultArr['msg'] = ERR_INVALID_PARTNER_NAME_MSG;

	    $flag = FALSE;
	}

	//Mandatory Parameter Missing
	if (($flag === TRUE) &&
	    (($request['orderID'] == "") ||
	    ($request['product'] == "") ||
	    ($request['brand'] == "") ||
	    //($request['model'] == "") ||
	    ($request['productType'] == "") ||
	    ($request['name'] == "") ||
	    ($request['mobile'] == "") ||
	    ($request['address'] == "") ||
	    ($request['pincode'] == "") ||
	    ($request['city'] == "") ||
	    ($request['requestType'] == "")
	)) {
	    $resultArr['code'] = ERR_MANDATORY_PARAMETER_MISSING_CODE;
	    $resultArr['msg'] = ERR_MANDATORY_PARAMETER_MISSING_MSG;

	    $flag = FALSE;
	}

	//SD wants booking ID to be returned in case of order ID exists
	//So changing below implementation
	/*
	  //Order ID already exists - Return error
	  if (($flag === TRUE) && ($this->partner_model->check_partner_lead_exists_by_order_id($request['orderID']) == TRUE)) {
	  $resultArr['code'] = ERR_ORDER_ID_EXISTS_CODE;
	  $resultArr['msg'] = ERR_ORDER_ID_EXISTS_MSG;

	  $flag = FALSE;
	  }
	 *
	 */
	if ($flag === TRUE) {
	    $lead = $this->partner_model->get_partner_lead_by_order_id($this->partner['id'], $request['orderID']);
	    if (!is_null($lead)) {
		//order id exists, return booking id
		$resultArr['code'] = ERR_ORDER_ID_EXISTS_CODE;
		$resultArr['msg'] = ERR_ORDER_ID_EXISTS_MSG;
		$resultArr['lead'] = $lead;

		$flag = FALSE;
	    }
	}

	//Invalid Product
	$valid_products = array("Washing Machines & Dryers", "Televisions", "Air Conditioner",
	    "Refrigerator", "Microwave Ovens & OTGs", "Water Purifiers", "Chimney & Hoods");
	if (($flag === TRUE) &&
	    (in_array($request['product'], $valid_products) == FALSE)) {
	    //Do not return error as of now, just log this.
	    log_message('info', $request['product'] . ': Invalide product type');

	    //$resultArr['code'] = ERR_INVALID_PRODUCT_CODE;
	    //$resultArr['msg'] = ERR_INVALID_PRODUCT_MSG;
	    //$flag = FALSE;
	}

	//Check for Request type
	$valid_request_types = array("Installation", "Demo", "Installation and Demo");
	if (($flag === TRUE) &&
	    (in_array($request['requestType'], $valid_request_types) == FALSE)) {
	    $resultArr['code'] = ERR_INVALID_REQUEST_TYPE_CODE;
	    $resultArr['msg'] = ERR_INVALID_REQUEST_TYPE_MSG;

	    $flag = FALSE;
	}

	//Check timeslot format validity
	if (($flag === TRUE) &&
	    ($this->validate_timeslot_format($request['deliveryDate']) === FALSE)) {
	    $resultArr['code'] = ERR_INVALID_TIMESLOT_FORMAT_CODE;
	    $resultArr['msg'] = ERR_INVALID_TIMESLOT_FORMAT_MSG;

	    $flag = FALSE;
	}


	/*
	  if ($request['Mobile'] == "") {
	  $resultArr['code'] = ERR_MOBILE_NUM_MISSING_CODE;
	  $resultArr['msg'] = ERR_MOBILE_NUM_MISSING_MSG;
	  }
	 *
	 */

	//If code is still empty, it means data is valid.
	if ($resultArr['code'] == "") {
	    $resultArr['result'] = TRUE;
	}

	return $resultArr;
    }

    //Validate get order status API data
    function validate_get_request_data($request) {
	log_message('info', "Entering: " . __METHOD__);

	//Lead will store the booking entry if it exists
	$resultArr = array("result" => FALSE, "lead" => NULL, "code" => NULL, "msg" => NULL);
	$flag = TRUE;

	//Validate Partner Name
	if ($request['partnerName'] != $this->partner['public_name']) {
	    $resultArr['code'] = ERR_INVALID_PARTNER_NAME_CODE;
	    $resultArr['msg'] = ERR_INVALID_PARTNER_NAME_MSG;

	    $flag = FALSE;
	}

	//Mandatory Parameter Missing
	if (($flag === TRUE) &&
	    (($request['orderID'] == "") ||
	    ($request['247aroundBookingID'] == ""))
	) {
	    $resultArr['code'] = ERR_MANDATORY_PARAMETER_MISSING_CODE;
	    $resultArr['msg'] = ERR_MANDATORY_PARAMETER_MISSING_MSG;

	    $flag = FALSE;
	}

	//Order ID / Booking ID validation
	if ($flag === TRUE) {
	    $lead = $this->partner_model->get_partner_lead_by_order_id($this->partner['id'], $request['orderID']);
	    if (!is_null($lead)) {
		//order id found, check booking id
		if ($lead['247aroundBookingID'] != $request['247aroundBookingID']) {
		    $resultArr['code'] = ERR_INVALID_BOOKING_ID_CODE;
		    $resultArr['msg'] = ERR_INVALID_BOOKING_ID_MSG;

		    $flag = FALSE;
		} else {
		    //Everything fine, return lead information
		    $resultArr['lead'] = $lead;
		}
	    } else {
		//Order id not found
		$resultArr['code'] = ERR_ORDER_ID_NOT_FOUND_CODE;
		$resultArr['msg'] = ERR_ORDER_ID_NOT_FOUND_MSG;

		$flag = FALSE;
	    }
	}

	//If code is still empty, it means data is valid.
	if ($resultArr['code'] == "") {
	    $resultArr['result'] = TRUE;
	}

	return $resultArr;
    }

    //Validate cancel order API data
    function validate_cancel_request_data($request) {
	log_message('info', "Entering: " . __METHOD__);

	//Lead will store the booking entry if it exists
	$resultArr = array("result" => FALSE, "lead" => NULL, "code" => NULL, "msg" => NULL);
	$flag = TRUE;

	//Validate Partner Name
	if ($request['partnerName'] != $this->partner['public_name']) {
	    $resultArr['code'] = ERR_INVALID_PARTNER_NAME_CODE;
	    $resultArr['msg'] = ERR_INVALID_PARTNER_NAME_MSG;

	    $flag = FALSE;
	}

	//Mandatory Parameter Missing
	if (($flag === TRUE) &&
	    (($request['orderID'] == "") ||
	    ($request['247aroundBookingID'] == "") ||
	    ($request['cancellationReason'] == ""))
	) {
	    $resultArr['code'] = ERR_MANDATORY_PARAMETER_MISSING_CODE;
	    $resultArr['msg'] = ERR_MANDATORY_PARAMETER_MISSING_MSG;

	    $flag = FALSE;
	}

	//Order ID / Booking ID validation
	if ($flag === TRUE) {
	    $lead = $this->partner_model->get_partner_lead_by_order_id($this->partner['id'], $request['orderID']);
	    if (!is_null($lead)) {
		$resultArr['lead'] = $lead;

		//order id found, check booking id
		if ($lead['247aroundBookingID'] != $request['247aroundBookingID']) {
		    $resultArr['code'] = ERR_INVALID_BOOKING_ID_CODE;
		    $resultArr['msg'] = ERR_INVALID_BOOKING_ID_MSG;

		    $flag = FALSE;
		} else {
		    //Check request status
		    if ($lead['247aroundBookingStatus'] == "Cancelled") {
			$resultArr['code'] = ERR_REQUEST_ALREADY_CANCELLED_CODE;
			$resultArr['msg'] = ERR_REQUEST_ALREADY_CANCELLED_MSG;

			$flag = FALSE;
		    }

		    if (($flag === TRUE) &&
			($lead['247aroundBookingStatus'] == "Completed")) {
			$resultArr['code'] = ERR_REQUEST_ALREADY_COMPLETED_CODE;
			$resultArr['msg'] = ERR_REQUEST_ALREADY_COMPLETED_MSG;

			$flag = FALSE;
		    }

		    //Check for cut-off time
		    //if cancel request time is 2 hrs prior to cutoff time, then ok
		    //else not ok
		    //todo:
		    //Check cancellation reason
		    //todo:
		}
	    } else {
		//Order id not found
		$resultArr['code'] = ERR_ORDER_ID_NOT_FOUND_CODE;
		$resultArr['msg'] = ERR_ORDER_ID_NOT_FOUND_MSG;

		$flag = FALSE;
	    }
	}

	//Check request status
	//If code is still empty, it means data is valid.
	if ($resultArr['code'] == "") {
	    $resultArr['result'] = TRUE;
	}

	return $resultArr;
    }

    //Validate update timeslot API data
    function validate_update_timeslot_data($request) {
	log_message('info', "Entering: " . __METHOD__);

	//Lead will store the booking entry if it exists
	$resultArr = array("result" => FALSE, "lead" => NULL, "code" => NULL, "msg" => NULL);
	$flag = TRUE;

	//Validate Partner Name
	if ($request['partnerName'] != $this->partner['public_name']) {
	    $resultArr['code'] = ERR_INVALID_PARTNER_NAME_CODE;
	    $resultArr['msg'] = ERR_INVALID_PARTNER_NAME_MSG;

	    $flag = FALSE;
	}

	//Mandatory Parameter Missing
	if (($flag === TRUE) &&
	    (($request['orderID'] == "") ||
	    ($request['247aroundBookingID'] == "") ||
	    ($request['installationTimeslotStart'] == "") ||
	    ($request['installationTimeslotEnd'] == ""))
	) {
	    $resultArr['code'] = ERR_MANDATORY_PARAMETER_MISSING_CODE;
	    $resultArr['msg'] = ERR_MANDATORY_PARAMETER_MISSING_MSG;

	    $flag = FALSE;
	}

	//Order ID / Booking ID validation
	if ($flag === TRUE) {
	    $lead = $this->partner_model->get_partner_lead_by_order_id($this->partner['id'], $request['orderID']);
	    if (!is_null($lead)) {
		$resultArr['lead'] = $lead;

		//order id found, check booking id
		if ($lead['247aroundBookingID'] != $request['247aroundBookingID']) {
		    $resultArr['code'] = ERR_INVALID_BOOKING_ID_CODE;
		    $resultArr['msg'] = ERR_INVALID_BOOKING_ID_MSG;

		    $flag = FALSE;
		} else {
		    //Check request status
		    if ($lead['247aroundBookingStatus'] == "Cancelled") {
			$resultArr['code'] = ERR_REQUEST_ALREADY_CANCELLED_CODE;
			$resultArr['msg'] = ERR_REQUEST_ALREADY_CANCELLED_MSG;

			$flag = FALSE;
		    }

		    if (($flag === TRUE) &&
			($lead['247aroundBookingStatus'] == "Completed")) {
			$resultArr['code'] = ERR_REQUEST_ALREADY_COMPLETED_CODE;
			$resultArr['msg'] = ERR_REQUEST_ALREADY_COMPLETED_MSG;

			$flag = FALSE;
		    }

		    //Check timeslot format validity
		    if (
			($flag === TRUE) &&
			(($this->validate_timeslot_format($request['installationTimeslotStart']) === FALSE) ||
			($this->validate_timeslot_format($request['installationTimeslotEnd']) === FALSE))
		    ) {
			$resultArr['code'] = ERR_INVALID_TIMESLOT_FORMAT_CODE;
			$resultArr['msg'] = ERR_INVALID_TIMESLOT_FORMAT_MSG;

			$flag = FALSE;
		    }

		    //Check timeslot is valid or not
		    if ($flag === TRUE) {
			$tsStart = $request['installationTimeslotStart'];
			$tsEnd = $request['installationTimeslotEnd'];

			//Check yr, month and day - all should be same
			//Timeslot minutes == 00
			if (($tsStart['year'] != $tsEnd['year']) ||
			    ($tsStart['month'] != $tsEnd['month']) ||
			    ($tsStart['day'] != $tsEnd['day']) ||
			    ($tsStart['minute'] != "00") ||
			    ($tsEnd['minute'] != "00")
			) {
			    $resultArr['code'] = ERR_INVALID_INSTALLATION_TIMESLOT_CODE;
			    $resultArr['msg'] = ERR_INVALID_INSTALLATION_TIMESLOT_MSG;

			    $flag = FALSE;
			}

			/*
			 * Valid timeslots: 10-13, 13-16, 16-19
			 * SD asked to remove this limitation as they have different timeslots than us
			 * We are free to return our timeslot
			 *
			  if (!
			  (($tsStart['hour'] == '10' && $tsEnd['hour'] == '13') ||
			  ($tsStart['hour'] == '13' && $tsEnd['hour'] == '16') ||
			  ($tsStart['hour'] == '16' && $tsEnd['hour'] == '19'))
			  ) {
			  $resultArr['code'] = ERR_INVALID_INSTALLATION_TIMESLOT_CODE;
			  $resultArr['msg'] = ERR_INVALID_INSTALLATION_TIMESLOT_MSG;

			  $flag = FALSE;
			  }
			 *
			 */
		    }
		}
	    } else {
		//Order id not found
		$resultArr['code'] = ERR_ORDER_ID_NOT_FOUND_CODE;
		$resultArr['msg'] = ERR_ORDER_ID_NOT_FOUND_MSG;

		$flag = FALSE;
	    }
	}

	//Check request status
	//If code is still empty, it means data is valid.
	if ($resultArr['code'] == "") {
	    $resultArr['result'] = TRUE;
	}

	return $resultArr;
    }

    //Cancels an order, returns true/false if cancellation is success/failure
    function process_cancel_request($request) {
	log_message('info', "Entering: " . __METHOD__);

	$booking_id = $request['247aroundBookingID'];
	$booking['current_status'] = "Cancelled";
	$booking['internal_status'] = $request['cancellationReason'];
	$booking['cancellation_reason'] = "Other : " . $request['cancellationReason'];
	$booking['update_date'] = $booking['closed_date'] = date("Y-m-d h:i:s");

	$this->booking_model->cancel_followup($booking_id, $booking);

	//Update partner leads table
	$partner_where = array("247aroundBookingID" => $booking_id);
	$partner_data = array(
	    "247aroundBookingStatus" => "Cancelled",
	    "247aroundBookingRemarks" => $booking['internal_status'],
	    "update_date" => $booking['closed_date']
	);
	$this->partner_model->update_partner_lead($partner_where, $partner_data);

	return TRUE;

//	$query1 = $this->booking_model->booking_history_by_booking_id($booking_id);
//
//	//------------Sending Email----------//
//
//	$message = "Booking Cancellation:<br>Customer name: " . $query1[0]['name'] . "<br>Customer phone number: " .
//	    $query1[0]['phone_number'] . "<br>Customer email: " . $query1[0]['user_email'] . "<br>Booking Id: " .
//	    $query1[0]['booking_id'] . "<br>Service name is:" . $query1[0]['services'] . "<br>Booking date was: " .
//	    $query1[0]['booking_date'] . "<br>Booking timeslot was: " . $query1[0]['booking_timeslot'] .
//	    "<br>Booking cancellation date is: " . $booking['update_date'] . "<br>Booking cancellation reason: " .
//	    $booking['cancellation_reason'] . "<br> Thanks!!";
//	$to = "booking@247around.com";
//	$this->sendMail('Booking Cancellation-AROUND', $message, $to, '', '');
//
//	redirect(base_url() . 'employee/booking/view_pending_queries', 'refresh');
    }


    /*
     * Return true/false & final timeslot if rescheduling is success/failure
     * Valid timeslots from SD - 9-12, 12-3, 3-6. We remap these timeslots to our
     * timeslots currently and return them.
     */
    function process_schedule_request($request) {
	log_message('info', "Entering: " . __METHOD__);

	$booking_id = $request['247aroundBookingID'];

	$tsStart = $request['installationTimeslotStart'];
	$tsEnd = $request['installationTimeslotEnd'];

	//Make hour=0 since we don't store time info in sch_date
	$tsEnd['hour'] = "00";
	//Get DateTime
	$sch_date = $this->getDateTime($tsEnd);

	$booking['booking_date'] = date('d-m-Y', strtotime($sch_date));

	switch ($tsStart['hour']) {
	    //9-12 PM
	    case "9":
		$booking['booking_timeslot'] = "10AM-1PM";
		break;

	    //12-3 PM
	    case "12":
		$booking['booking_timeslot'] = "1PM-4PM";
		break;

	    //3-6 PM
	    case "15":
		$booking['booking_timeslot'] = "4PM-7PM";
		break;

	    default:
		//Use this in case of error
		$booking['booking_timeslot'] = "1PM-4PM";
		break;
	}

	$sch_time = $booking['booking_timeslot'];
	$booking['query_remarks'] = (isset($request['remarks']) ? $request['remarks'] : "");
	$booking['update_date'] = date("Y-m-d h:i:s");

	$this->booking_model->schedule_booking($booking_id, $booking);

	//Update Partner leads table
	$partner_where = array("247aroundBookingID" => $booking_id);
	$partner_data = array(
	    "ScheduledAppointmentDate" => $sch_date,
	    "ScheduledAppointmentTime" => $sch_time,
	    "Remarks" => $request['remarks'],
	    "update_date" => $booking['update_date']
	);
	$this->partner_model->update_partner_lead($partner_where, $partner_data);

	//Return 247around time slot
	$resultArr['timeslotStart'] = json_encode($request['installationTimeslotStart'], JSON_UNESCAPED_SLASHES);
	$resultArr['timeslotEnd'] = json_encode($request['installationTimeslotEnd'], JSON_UNESCAPED_SLASHES);
	$resultArr['result'] = TRUE;

	return $resultArr;
    }

    function validate_timeslot_format($timeslot) {
	json_decode($timeslot);

	if (!(json_last_error() === JSON_ERROR_NONE)) {
	    return FALSE;
	} else {
	    if (
		(array_key_exists('year', $timeslot)) &&
		(array_key_exists('month', $timeslot)) &&
		(array_key_exists('day', $timeslot)) &&
		(array_key_exists('hour', $timeslot)) &&
		(array_key_exists('minute', $timeslot))) {
		if (
			($timeslot['year'] === "") ||
		    ($timeslot['month'] === "") ||
		    ($timeslot['day'] === "") ||
		    ($timeslot['hour'] === "") ||
		    ($timeslot['minute'] === "")
		) {
		    //something is missing
			return FALSE;
		} else {
		    return TRUE;
		}
	    } else {
		return FALSE;
	    }

	}
    }


    /**
     * Send final JSON response to Partner
     *
     * @access	private
     * @return	Echos the output
     */
    function sendJsonResponse($code) {
	$this->jsonResponseString['code'] = $code[0];
	$this->jsonResponseString['result'] = $code[1];

	$responseData = array("data" => $this->jsonResponseString);

	//$activity = array('activity' => 'sending response', 'data' => json_encode($responseData), 'time' => $this->microtime_float());
	//$this->apis->log_partner_activity($activity);
	$activity = array(
	    'partner_id' => $this->partner['id'],
	    'activity' => $this->requestUrl,
	    'header' => $this->header,
	    'json_request_data' => $this->jsonRequestData,
	    'json_response_string' => json_encode($responseData));
	$this->partner_model->log_partner_activity($activity);

	header('Content-Type: application/json');
	//header('Content-type:application/json;charset=utf-8');
	$response = json_encode($responseData, JSON_UNESCAPED_SLASHES);

	echo $response;
    }

    function getallheaders() {
	//Use this if you are using Nginx

	$headers = '';
	foreach ($_SERVER as $name => $value) {
	    if (substr($name, 0, 5) == 'HTTP_') {
		$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
	    }
	}

	return $headers;

	//It works only with Apache
	//return getallheaders();
    }

    function getDateTime($dt) {
	$date = $dt['year'] . "-" . $dt['month'] . "-" . $dt['day'];
	$time = $dt['hour'] . ":" . $dt['minute'] . ":00";

	//TODO: check for error
	try {
	    $new_dt = date_create($date . " " . $time);
	} catch (Exception $e) {
	    log_message('info', $e->getMessage());

	    $new_dt = date_create("now");
	}

	if ($new_dt !== FALSE)
	    return $new_dt->format("Y-m-d H:i:s");
    }

}
