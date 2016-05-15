<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/** Error reporting */
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

class bookings_excel extends CI_Controller {

    function __Construct() {
        parent::__Construct();
        $this->load->helper(array('form', 'url'));
        $this->load->helper('download');

        $this->load->library('form_validation');
        $this->load->library('s3');
        $this->load->library('PHPReport');

        $this->load->model('user_model');
        $this->load->model('booking_model');

        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            redirect(base_url() . "employee/login");
        }
    }

    public function index() {
        $this->load->view('employee/header');
        $this->load->view('employee/upload_bookings_excel');
    }

    public function add_booking_from_excel() {
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
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileExtn);
	    $objPHPExcel = $objReader->load($inputFileName);
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }

        //  Get worksheet dimensions
        $sheet = $objPHPExcel->setActiveSheetIndexbyName('Sheet1');
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

        //echo "highest row: ", $highestRow, EOL;
        //echo "highest col: ", $highestColumn, EOL;
        //echo "highest col index: ", $highestColumnIndex, EOL;

        $sheet = $objPHPExcel->getSheet(0);
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

	    if (empty($output)) {
                //User doesn't exist
                $user['name'] = $rowData[0]['Customer_Name'];
                $user['phone_number'] = $rowData[0]['Phone'];
                $user['user_email'] = "";
                $user['home_address'] = $rowData[0]['Customer_Address'];
		$user['pincode'] = $rowData[0]['Pincode'];
		$user['city'] = $rowData[0]['CITY'];

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
            }

            //Add this lead into the leads table
	    //Check whether this is a new Lead or Not
	    if ($this->booking_model->check_sd_lead_exists_by_order_id($rowData[0]['Sub_Order_ID']) == FALSE) {
		$lead_details['Sub_Order_ID'] = $rowData[0]['Sub_Order_ID'];
		$lead_details['Unique_id'] = $rowData[0]['Unique_id'];

		$dateObj1 = PHPExcel_Shared_Date::ExcelToPHPObject($rowData[0]['Referred_Date_and_Time']);
		$lead_details['Referred_Date_and_Time'] = $dateObj1->format('d/m/Y');

		$lead_details['Brand'] = $rowData[0]['Brand'];
		$lead_details['Model'] = $rowData[0]['Model'];
		$lead_details['Product'] = '';

		$prod = trim($rowData[0]['Product']);

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


		/*
		  switch (trim($rowData[0]['Product'])) {
		  case 'Washing Machines & Dryers':
		  $lead_details['Product'] = 'Washing Machine';
		  break;

		  case 'Television':
		  $lead_details['Product'] = 'Television';
		  break;

		  case 'Televisions':
		  $lead_details['Product'] = 'Television';
		  break;

		  case 'Airconditioner':
		  $lead_details['Product'] = 'Air Conditioner';
		  break;

		  case 'Air Conditioner':
		  $lead_details['Product'] = 'Air Conditioner';
		  break;

		  case 'Air Conditioners Window AC':
		  $lead_details['Product'] = 'Air Conditioner';
		  break;

		  case 'Air Conditioners Split AC':
		  $lead_details['Product'] = 'Air Conditioner';
		  break;

		  case 'Refrigerator':
		  $lead_details['Product'] = 'Refrigerator';
		  break;

		  case 'Microwave Ovens & OTGs':
		  $lead_details['Product'] = 'Microwave';
		  break;

		  case 'Water Purifiers':
		  $lead_details['Product'] = 'Water Purifier';
		  break;

		  case 'Chimney & Hoods':
		  $lead_details['Product'] = 'Chimney';
		  break;

		  default:
		  $lead_details['Product'] = '';
		  break;
		  }
		 *
		 */

		$lead_details['Product_Type'] = $rowData[0]['Product_Type'];
		$lead_details['Customer_Name'] = $rowData[0]['Customer_Name'];
		$lead_details['Customer_Address'] = $rowData[0]['Customer_Address'];
		$lead_details['Pincode'] = $rowData[0]['Pincode'];
		$lead_details['City'] = $rowData[0]['CITY'];
		$lead_details['Phone'] = $rowData[0]['Phone'];

		$dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($rowData[0]['Delivery_Date']);
		$lead_details['Delivery_Date'] = $dateObj2->format('d/m/Y');

		//$lead_details['user_id'] = $user_id;
		$lead_details['Call_Type_Installation_Table_Top_InstallationDemo_Service'] = "";
		$lead_details['Status_by_247around'] = "FollowUp";
		$lead_details['Scheduled_Appointment_DateDDMMYYYY'] = "";
		$lead_details['Scheduled_Appointment_Time'] = "";
		$lead_details['Remarks_by_247around'] = "";
		$lead_details['Rating_Stars'] = "";
		$lead_details['Status_by_Snapdeal'] = "";
		$lead_details['Remarks_by_Snapdeal'] = "";
		$lead_details['Final_Status'] = "";

		//Add this as a Query now
		$booking['booking_id'] = '';
		$booking['user_id'] = $user_id;
		$booking['service_id'] = $this->booking_model->getServiceId($lead_details['Product']);
		//echo "Service ID: " . $booking['service_id'] . PHP_EOL;

		$booking['booking_primary_contact_no'] = $lead_details['Phone'];
		$booking['booking_alternate_contact_no'] = '';

		$yy = date("y");
		$mm = date("m");
		$dd = date("d");
		$booking['booking_id'] = str_pad($booking['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
		$booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($booking['user_id'])) + 1);

		//Add source
		$booking['source'] = "SS";
		$booking['booking_id'] = "Q-" . $booking['source'] . "-" . $booking['booking_id'];
		$lead_details['CRM_Remarks_SR_No'] = $booking['booking_id'];

		$booking['quantity'] = '1';
		$booking['appliance_brand'] = $lead_details['Brand'];
		$booking['appliance_category'] = '';
		$booking['appliance_capacity'] = '';
		$booking['description'] = $lead_details['Product_Type'];
		$booking['model_number'] = $lead_details['Model'];
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
		$booking['booking_address'] = $lead_details['Customer_Address'];
		$booking['booking_pincode'] = $lead_details['Pincode'];
		$booking['amount_due'] = '';
		$booking['booking_remarks'] = '';
		$booking['query_remarks'] = '';


		//Insert query
		//echo print_r($booking, true) . "<br><br>";
		$this->booking_model->addbooking($booking, $appliance_id, $lead_details['City']);

		//Save this in SD leads table
		$lead_details['CRM_Remarks_SR_No'] = $booking['booking_id'];
		$lead_details['Status_by_247around'] = 'FollowUp';
		//echo print_r($lead_details, true) . "<br><br>";
		$this->booking_model->insert_sd_lead($lead_details);

		//Reset
		unset($booking);
		unset($lead_details);
	    } else {
		//Skip this request as it already exists
	    }
	}

	redirect(base_url() . 'employee/booking/view_pending_queries', 'refresh');
    }

    function get_unassigned_bookings() {
        $bookings = $this->booking_model->get_sd_unassigned_bookings();

        $data['booking'] = $bookings;

        $this->load->view('employee/header');
        $this->load->view('employee/sd_booking_summary', $data);
    }

    function get_all_sd_bookings() {
        $bookings = $this->booking_model->get_all_sd_bookings();

        $data['booking'] = $bookings;

        $this->load->view('employee/header');
        $this->load->view('employee/sd_booking_summary', $data);
    }

    function get_booking_form($lead_id) {
        $lead = $this->booking_model->get_sd_lead($lead_id);
        $users = $this->user_model->search_user($lead['Phone']);
        $user = $users[0];

        $service_id = $this->booking_model->getServiceId($lead['Product']);
        $category = $this->booking_model->getCategoryForService($service_id);

        $this->load->view('employee/header');
        $this->load->view('employee/confirm_sd_lead', array(
            'lead' => $lead,
            'user' => $user,
            'service_id' => $service_id,
            'category' => $category));
    }

  function get_confirm_sd_lead_form() {
        $lead_id = $this->input->post('lead_id');
        $booking['user_id'] = $this->input->post('user_id');
        $booking['service_id'] = $this->input->post('service_id');

        $booking['user_name'] = $this->input->post('name');
        $booking['user_email'] = '';
        $booking['booking_primary_contact_no'] = $this->input->post('booking_primary_contact_no');
        $booking['booking_alternate_contact_no'] = $this->input->post('booking_alternate_contact_no');

        $appliance = $this->input->post('services');
        $booking['appliance_brand'] = $this->input->post('appliance_brand');
        $booking['appliance_category'] = $this->input->post('appliance_category');
        $booking['appliance_capacity'] = $this->input->post('appliance_capacity');
        $booking['model_number'] = $this->input->post('model_number');
	$booking['description'] = $this->input->post('description');

	$booking['items_selected'] = $this->input->post('items_selected');
        $booking['total_price'] = $this->input->post('total_price');

        $booking['booking_date'] = $this->input->post('booking_date');
        $booking['booking_timeslot'] = $this->input->post('booking_timeslot');
        $booking['booking_address'] = $this->input->post('booking_address');
        $booking['booking_pincode'] = $this->input->post('booking_pincode');
        $booking['booking_remarks'] = $this->input->post('booking_remarks');

        $booking['amount_due'] = $booking['total_price'];
        $booking['quantity'] = 1;
        $booking['type'] = 'Booking';
        $booking['query_remarks'] = '';
        $booking['current_status'] = 'Pending';
        $booking['create_date'] = date("Y-m-d h:i:s");

        $booking['source'] = 'SS';
        $booking['appliance_tags'] = '';

        $this->load->view('employee/header');

        $result[0]['services'] = $appliance;
        $this->load->view('employee/appliancebookingconf', array(
            'lead_id' => $lead_id,
            'booking' => $booking,
            'result' => $result));
    }

  function post_confirm_sd_lead_form() {
        $lead_id = $this->input->post('lead_id'); $booking['user_id'] = $this->input->post('user_id');
        $booking['service_id'] = $this->input->post('service_id');
        $booking['service_name'] = $this->input->post('services');
        $booking['user_email'] = $this->input->post('user_email');
        $booking['user_name'] = $this->input->post('user_name');
        $booking['city'] = $this->input->post('city');
        $booking['state'] = $this->input->post('state');

        $booking['booking_primary_contact_no'] = $this->input->post('booking_primary_contact_no');
        $booking['booking_alternate_contact_no'] = $this->input->post('booking_alternate_contact_no');

        $booking['quantity'] = 1;
        $booking['appliance_brand'] = $this->input->post('appliance_brand');
        $booking['appliance_category'] = $this->input->post('appliance_category');
        $booking['appliance_capacity'] = $this->input->post('appliance_capacity');
        $booking['model_number'] = $this->input->post('model_number');
	    $booking['description'] = $this->input->post('description');
	    $booking['items_selected'] = $this->input->post('items_selected');
        $booking['total_price'] = $this->input->post('total_price');
        $booking['amount_due'] = $booking['total_price'];
        $booking['potential_value'] = $booking['total_price'];

        $booking['purchase_month'] = date('m');
        $booking['purchase_year'] = date('Y');
        $booking['appliance_tags'] = $booking['appliance_brand'] .
            " " . $booking['service_name'];
        $booking['last_service_date'] = date('d-m-Y');

        $booking['booking_date'] = $this->input->post('booking_date');
        $booking['booking_timeslot'] = $this->input->post('booking_timeslot');
        $booking['booking_address'] = $this->input->post('booking_address');
        $booking['booking_pincode'] = $this->input->post('booking_pincode');
        $booking['booking_remarks'] = $this->input->post('booking_remarks');

        $booking['booking_date'] = date('d-m-Y', strtotime($booking['booking_date']));
        $yy = date("y", strtotime($booking['booking_date']));
        $mm = date("m", strtotime($booking['booking_date']));
        $dd = date("d", strtotime($booking['booking_date']));

        $booking['source'] = 'SS';
        $booking['booking_id'] = str_pad($booking['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
        $booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($booking['user_id'])) + 1);
        $booking['booking_id'] = $booking['source'] . "-" . $booking['booking_id'];

        $booking['type'] = 'Booking';
        $booking['query_remarks'] = '';
        $booking['current_status'] = 'Pending';
        $booking['create_date'] = date("Y-m-d h:i:s");

        $appliance_id = $this->booking_model->addexcelappliancedetails($booking);

        $this->booking_model->addapplianceunitdetails($booking);

        $output = $this->booking_model->addbooking($booking, $appliance_id, $booking['city'], $booking['state']);

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
                <br>Customer Name: " . $booking['user_name'] . "<br>Customer Phone Number: " .
                $booking['booking_primary_contact_no'] . "<br>Customer email address: " .
                $booking['user_email'] . "<br>Booking Id: " . $booking['booking_id'] .
                "<br>Service name:" . $booking['service_name'] . "<br>Number of appliance: " .
                $booking['quantity'] . "<br>Booking Date: " . $booking['booking_date'] .
                "<br>Booking Timeslot: " . $booking['booking_timeslot'] . "<br>Amount Due: " .
                $booking['amount_due'] . "<br>Your Booking Remark is: " . $booking['booking_remarks'] .
                "<br>Booking address: " . $booking['booking_address'] . "<br>Booking pincode: " .
                $booking['booking_pincode'] . "<br><br>
                Appliance Details:<br>";

            $appliance = "<br>Brand : " . $booking['appliance_brand'] . "<br>Category : " . $booking['appliance_category'] . "<br>Capacity : " . $booking['appliance_capacity'] .
                "<br>Selected service/s is/are: " . $booking['items_selected'] . "<br>Total price is: " . $booking['total_price'] . "<br>";
            $message = $message . $appliance . "<br> Thanks!!";

            $to = "";
            $cc = "";
            $bcc = "";
            $subject = 'Booking Confirmation - Snapdeal - AROUND';
            $this->sendMail($subject, $message, $to, $cc, $bcc); //Call to sendMail function
        }

        //Update lead table with booking ID and new status
        $this->booking_model->update_sd_lead($lead_id, $booking['booking_id'], 'Pending');

        redirect(base_url() . 'employee/bookings_excel/get_unassigned_bookings', 'refresh');
    }

    function sendMail($subject, $message, $to, $cc, $bcc) {

        $this->load->library('email');
        $this->email->initialize(array(
            'useragent' => 'CodeIgniter',
            'protocol' => 'smtp',
            'smtp_host' => 'smtp.sendgrid.net',
            'smtp_port' => '587',
            'smtp_user' => 'nitinmalhotra',
            'smtp_pass' => 'mandatory16',
            'mailtype' => 'html',
            'charset' => 'iso-8859-1',
            'crlf' => "\r\n",
            'newline' => "\r\n",
            'wordwrap' => TRUE
            )
        );

        $this->email->from('booking@247around.com', '247around Team');
	//$this->email->to($to);
        //TODO: Uncomment before releasing
        $this->email->bcc('anuj@247around.com, nits@247around.com');
        //$this->email->bcc('anuj.aggarwal@gmail.com');
        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->send();
    }

}
