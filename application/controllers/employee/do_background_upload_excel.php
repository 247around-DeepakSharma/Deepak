<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 36000);

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

require_once BASEPATH . 'libraries/spout-2.4.3/src/Spout/Autoloader/autoload.php';

class Do_background_upload_excel extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
	parent::__Construct();

	$this->load->helper(array('form', 'url'));

	$this->load->library('asynchronous_lib');
	$this->load->library('notify');
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
	$this->load->library('s3');
	$this->load->library('email');
    }

    /*
     * @desc: In case of Delivered file:
     * 
     * Case a) Order ID is NOT found
     * 
     * 1. Send SMS to customer
     * 2. Insert new booking
     * 
     * Case b) Order ID is found
     * 
     * 1. If the (current booking date) - (today date) >= 4 days, Send SMS since 
     * we have not sent SMS for this booking today.
     * For eg, we send SMS everyday for bookings where booking date is of today (T0),
     * T1, T2 or T3. If a booking has EDD as T4, we have not sent SMS to that booking.
     * Since appliance has been delivered now, inform customer by sending an SMS.
     * 
     * 2. If Current Status = FollowUp, reset the booking date as well.
     * 3. Update delivery date.
     * 
     * 
     * In case of Shipped file:
     * 
     * Case a) Order ID is NOT found
     * 
     * 1. Send SMS to customer
     * 2. Insert new booking
     * 
     * Case b) Order ID is found
     * 
     * 1. If the (current booking date) - (today date) >= 4 days, Send SMS since 
     * we have not sent SMS for this booking today.
     * For eg, we send SMS everyday for bookings where booking date is of today (T0),
     * T1, T2 or T3. If a booking has EDD as T4, we have not sent SMS to that booking.
     * Since appliance EDD has been pulled in, inform customer by sending an SMS.
     * 
     * 2. If Current Status = FollowUp, update the booking date as per the new EDD if required.
     * 3. Update EDD.
     * 
     * 
     * @param: File type - Delivered or Shipped
     */
    function upload_snapdeal_file($file_type) {
	log_message('info', __FUNCTION__ . "=> File type: " . $file_type);

	if (!empty($_FILES['file']['name']) && $_FILES['file']['size'] > 0) {
	    $pathinfo = pathinfo($_FILES["file"]["name"]);

	    switch ($pathinfo['extension']) {
		case 'xlsx':
		    $inputFileName = $_FILES['file']['tmp_name'];
		    $inputFileExtn = 'Excel2007';
		    break;
		case 'xls':
		    $inputFileName = $_FILES['file']['tmp_name'];
		    $inputFileExtn = 'Excel5';
		    break;
	    }
	}

	try {
	    //$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
	    $objReader = PHPExcel_IOFactory::createReader($inputFileExtn);
	    $objPHPExcel = $objReader->load($inputFileName);
	} catch (Exception $e) {
	    die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
	}
        
        $file_name = $_FILES["file"]["name"];
        
	//  Get worksheet dimensions
	$sheet = $objPHPExcel->getSheet(0);
	$highestRow = $sheet->getHighestRow();
	$highestColumn = $sheet->getHighestColumn();

	$headings = $sheet->rangeToArray('A1:' . $highestColumn . 1, NULL, TRUE, FALSE);
	$headings_new = array();
	$data = array();
	
        foreach ($headings as $heading) {
	    $heading = str_replace(array("/", "(", ")", "."), "", $heading);
	    array_push($headings_new, str_replace(array(" "), "_", $heading));
	}

	for ($row = 2, $i = 0; $row <= $highestRow; $row++, $i++) {
	    //  Read a row of data into an array
	    $rowData_array = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            
            //Check if phone no is blank
            //Not working hence commented
            /*
            if ($rowData_array[0][11] == "") {
                log_message('info', __FUNCTION__ . "=> Phone: " . $rowData_array[0]['Phone']);
                log_message('info', __FUNCTION__ . "=> Row data array: " . print_r($rowData_array, TRUE));
                log_message('info', __FUNCTION__ . "=> Row count: " . $row);
                
                break;
            }
             */
            
	    $rowData = array_combine($headings_new[0], $rowData_array[0]);
	    array_push($data, $rowData);
	}
        
	// Warning: Do not Change Validation Order
	$validate_data = $this->validate_phone_number($data, $file_type, $file_name);
	$row_data1 = $this->validate_product($validate_data, $file_type, $file_name);
	$row_data2 = $this->validate_delivery_date($row_data1, $file_type, $file_name);
	$row_data3 = $this->validate_pincode($row_data2, $file_type, $file_name);
	$row_data4 = $this->validate_order_id($row_data3);
	$row_data5 = $this->validate_product_type($row_data4);
	$row_data = $this->validate_order_id_same_as_phone($row_data5, $file_type,$file_name);

	$count_total_leads_came_today = count($data);
	$count_booking_inserted = 0;
       

	foreach ($row_data['valid_data'] as $key => $value) { 
           
	    //echo print_r($rowData[0], true), EOL;
	    if ($value['Phone'] == "") {
		//echo print_r("Phone number null, break from this loop", true), EOL;
		break;
	    }

	    //Insert user if phone number doesn't exist
	    $output = $this->user_model->search_user(trim($value['Phone']));
	    $state = $this->vendor_model->get_state_from_pincode($value['Pincode']);

	    if (empty($output)) {
		//User doesn't exist
		$user['name'] = $value['Customer_Name'];
		$user['phone_number'] = $value['Phone'];
		$user['user_email'] = (isset($value['Email_ID']) ? $value['Email_ID'] : "");
		$user['home_address'] = $value['Customer_Address'];
		$user['pincode'] = $value['Pincode'];
		$user['city'] = $value['CITY'];
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
		$user['user_email'] = (isset($value['Email_ID']) ? $value['Email_ID'] : "");
		$user['name'] = $value['Customer_Name'];
	    }

	    //Wybor brand should be tagged to Partner Wybor only if the
	    //States are:
            // - Tamilnadu (pincode starts from 6).
            // - AP / Telangana (pincode starts from 5)
            // (Karnataka also starts from 5, we will leave that as of now)
            // Else it would be
	    //tagged to Snapdeal.
	    //Ray brand should be tagged to Ray.
	    //All other brands would go to Snapdeal.
	    switch ($value['Brand']) {
		case 'Wybor':
		    if ((substr($value['Pincode'], 0, 1) == "5") || 
                        (substr($value['Pincode'], 0, 1) == "6")) {
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

	    $partner_booking = $this->partner_model->get_order_id_for_partner($booking['partner_id'], $value['Sub_Order_ID']);
            
            //Check whether order id exists or not
	    if (is_null($partner_booking)) {
                //order id not found
		$appliance_details['user_id'] = $booking['user_id'] = $user_id;
		$appliance_details['service_id'] = $unit_details['service_id'] = $booking['service_id'] = $value['service_id'];
		$booking['booking_pincode'] = $value['Pincode'];
                $where  = array('service_id' => $value['service_id'],'brand_name' => trim($value['Brand']));
                $brand_id_array  = $this->booking_model->get_brand($where);
                // If brand not exist then insert into table
                if(empty($brand_id_array)){
                   
                   $this->booking_model->addNewApplianceBrand($value['service_id'], trim($value['Brand']));

                } 
		$appliance_details['brand'] = $unit_details['appliance_brand'] = $value['Brand'];

		if (isset($value['Expected_Delivery_Date'])) {
		    $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($value['Expected_Delivery_Date']);
                    
		} else if(isset($value['Delivery_Start_Date'])){
                    
                    $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($value['Delivery_Start_Date']);
                } else {
                    $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($value['Delivery_Date']);
                }

		switch ($file_type) {
		    case 'shipped':
			if ($dateObj2->format('d') == date('d')) {
			    //If date is NULL, add 3 days from today in EDD.
			    $dateObj2 = date_create('+3days');
			}
			$yy = $dateObj2->format('y');
			$mm = $dateObj2->format('m');
			$dd = $dateObj2->format('d');
			$booking['partner_source'] = "Snapdeal-shipped-excel";
			$booking['booking_date'] = $dateObj2->format('d-m-Y');
			//Set EDD only
			$booking['estimated_delivery_date'] = $dateObj2->format('Y-m-d H:i:s');
			$booking['delivery_date'] = '';
			//Tag internal status for missed call
			$booking['internal_status'] = "Missed_call_not_confirmed";
			$booking['query_remarks'] = 'Product Shipped, Call Customer For Booking';

			break;

		    case 'delivered':
			//For delivered file, set booking date empty so that the queries come on top of the page
			$yy = date("y");
			$mm = date("m");
			$dd = date("d");
			$booking['partner_source'] = "Snapdeal-delivered-excel";
			$booking['booking_date'] = '';
			//Set delivered date only
			$booking['delivery_date'] = $dateObj2->format('Y-m-d H:i:s');
			$booking['estimated_delivery_date'] = '';
			$booking['internal_status'] = "FollowUp";
			$booking['query_remarks'] = 'Product Delivered, Call Customer For Booking';
		}

		log_message('info', print_r($dateObj2, true));

		$booking['booking_id'] = str_pad($booking['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
		$booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($booking['user_id'])) + 1);
		$booking['booking_id'] = "Q-" . $booking['source'] . "-" . $booking['booking_id'];
		$unit_details['booking_id'] = $booking['booking_id'];
		$unit_details['partner_id'] = $booking['partner_id'];
		$appliance_details['description'] = $unit_details['appliance_description'] = $value['Product_Type'];
		$appliance_details['category'] = $unit_details['appliance_category'] = '';
		$appliance_details['capacity'] = $unit_details['appliance_capacity'] = '';
		$appliance_details['model_number'] = $unit_details['model_number'] = $value['Model'];
		$appliance_details['tag'] = $unit_details['appliance_tag'] = $value['Brand'] . " " . $value['Product'];
		$booking['booking_remarks'] = '';
		$booking['booking_alternate_contact_no'] = '';
		$appliance_details['purchase_month'] = $unit_details['purchase_month'] = date('m');
		$appliance_details['purchase_year'] = $unit_details['purchase_year'] = date('Y');
		$appliance_details['last_service_date'] = date('d-m-Y');

		$unit_details['appliance_id'] = $this->booking_model->addappliance($appliance_details);

		if ($unit_details['appliance_id']) {
		    log_message('info', __METHOD__ . "=> Appliance added: " . $unit_details['appliance_id']);

		    $unit_id = $this->booking_model->addunitdetails($unit_details);
		    if ($unit_id) {
			log_message('info', __METHOD__ . "=> Unit details added: " . $unit_id);

			$booking['order_id'] = $value['Sub_Order_ID'];

			$ref_date = PHPExcel_Shared_Date::ExcelToPHPObject($value['Referred_Date_and_Time']);
			$booking['reference_date'] = $ref_date->format('Y-m-d H:i:s');
			$booking['booking_timeslot'] = '';
			$booking['request_type'] = 'Installation & Demo';
			$booking['booking_primary_contact_no'] = $value['Phone'];
			$booking['create_date'] = date('Y-m-d H:i:s');
			$booking['current_status'] = "FollowUp";
			$booking['type'] = "Query";
			$booking['booking_address'] = $value['Customer_Address'];
			$booking['city'] = $value['CITY'];
			$booking['state'] = $state['state'];
			$booking['quantity'] = '1';

			$booking_details_id = $this->booking_model->addbooking($booking);

			if ($booking_details_id) {
			    log_message('info', __FUNCTION__ . ' =>  Booking is inserted in booking details: ' . $booking['booking_id']);

			    $count_booking_inserted++;

			    $this->notify->insert_state_change($booking['booking_id'], _247AROUND_FOLLOWUP , _247AROUND_NEW_QUERY , $booking['query_remarks'], $this->session->userdata('id'), $this->session->userdata('employee_id'),_247AROUND);

			    //Send SMS to customers regarding delivery confirmation through missed call for delivered file only
                            //Check whether vendor is available or not
                            if ($file_type == "delivered") {
                                $vendors = $this->vendor_model->check_vendor_availability($booking['booking_pincode'], $booking['service_id']);
                                $vendors_count = count($vendors);

                                if ($vendors_count > 0) {
                                    $this->send_sms_to_snapdeal_customer($value['appliance'], 
                                            $booking['booking_primary_contact_no'], $user_id, 
                                            $booking['booking_id'], $file_type);
                                } else { //if ($vendors_count > 0) {
                                    log_message('info', __FUNCTION__ . ' =>  SMS not sent because of Vendor Unavailability for Booking ID: ' . $booking['booking_id']);
                                }
                            }   //if ($file_type == "delivered") {
			} else {
			    log_message('info', __FUNCTION__ . ' =>  Booking is not inserted in booking details: ' . print_r($value, true));

			    $row_data['error'][$key]['booking_details'] = " Booking Unit Id is not inserted";
			    $row_data['error'][$key]['invalid_data'] = $value;
			}

			if (empty($booking['state'])) {
			    log_message('info', __FUNCTION__ . " Pincode is not found booking id: " . $booking['booking_id']);
                            /*
			    $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
			    $send['booking_id'] = $booking['booking_id'];
			    $send['state'] = "Pincode_not_found";
			    $this->asynchronous_lib->do_background_process($url, $send);
                             * 
                             */
			}

			$this->insert_booking_in_partner_leads($booking, $unit_details, $user, $value['Product']);

			//Reset
			unset($appliance_details);
			unset($booking);
			unset($unit_details);
		    } else {
			log_message('info', __FUNCTION__ . ' =>  Appliance is not inserted: ' .
			    print_r($value, true));

			$row_data['error'][$key]['unit_details'] = " Booking Unit Id is not inserted";
			$row_data['error'][$key]['invalid_data'] = $value;
		    }
		} else {
		    log_message('info', __FUNCTION__ . ' =>  Appliance is not inserted: ' .
			print_r($value, true));

		    $row_data['error'][$key]['appliance'] = "Appliance is not inserted";
		    $row_data['error'][$key]['invalid_data'] = $value;
		}
	    } else {
                //Order ID found
                if ($file_type == "delivered" || $file_type == "shipped") {
                    $status = $partner_booking['current_status'];
                    $int_status = $partner_booking['internal_status'];
                    
                    //If the booking has already been scheduled or cancelled, leave this as it is.
                    if (($value['Delivery_Date'] != '(null)') && ($status == 'FollowUp')) {

                        $dateObj = PHPExcel_Shared_Date::ExcelToPHPObject($value['Delivery_Date']);
                         $current_date = date_create(date('Y-m-d'));

                        if ($file_type == "shipped" && $partner_booking['estimated_delivery_date'] !== "0000-00-00 00:00:00") {
                            $old_delivery_date = date_create(date('Y-m-d', strtotime($partner_booking['estimated_delivery_date'])));
                        } else if ($file_type == "delivered" && $partner_booking['delivery_date'] !== "0000-00-00 00:00:00") {
                            $old_delivery_date = date_create(date('Y-m-d', strtotime($partner_booking['delivery_date'])));
                        }

                        $new_delivery_date = date_create(date('Y-m-d', strtotime($dateObj->format('Y-m-d H:i:s'))));

                        $date_diff = date_diff($new_delivery_date, $old_delivery_date);
                        
                        //Check diff between new and old dates and update if diff > 0
                        if ($date_diff->days > 0) {
                            if ($file_type == "shipped") {
                                $update_data['estimated_delivery_date'] = $new_delivery_date->date;
                            } else {
                                //Clear the booking date so that it starts reflecting on our panel & update booking.
                                //This should be done only if the booking has not been updated in the meanwhile.
                                $update_data['delivery_date'] = $new_delivery_date->date;
                                
                                $update_data['booking_date'] = '';
                                $update_data['booking_timeslot'] = '';
                            }
                            
                            log_message('info', __FUNCTION__ . 'Update Partned Lead: ' .
                                    print_r(array($partner_booking['booking_id'], $update_data), true));

                            $this->booking_model->update_booking($partner_booking['booking_id'], $update_data);

                            unset($update_data);
                        }
                        
                        //Check both the differences, new date should fall in 3 days window and old date should
                        //be outside this window, only then send SMS to customer
                        $date_diff1 = date_diff($current_date, $old_delivery_date);
                        $date_diff2 = date_diff($current_date, $new_delivery_date);
                        
                        if ($date_diff1 > 4 && $date_diff2 < 4) {
                            $vendors2 = $this->vendor_model->check_vendor_availability($partner_booking['booking_pincode'], $partner_booking['service_id']);

                            //Send SMS to customer as well, check whether vendor is available or not
                            if (!empty($vendors2) && ($int_status == 'Missed_call_not_confirmed')) {
                                $this->send_sms_to_snapdeal_customer($value['appliance'], 
                                        $partner_booking['booking_primary_contact_no'], 
                                        $user_id, $partner_booking['booking_id'], $file_type);
                            } else {
                                log_message('info', __FUNCTION__ . ' =>  SMS not sent because of Vendor Unavailability for Booking ID: ' . $booking['booking_id']);
                            }
                        }
                    }
                }
            }
            
        }
        
	$row_data['error']['total_booking_inserted'] = $count_booking_inserted;
	$row_data['error']['total_booking_came_today'] = $count_total_leads_came_today;
        
	if (isset($row_data['error'])) {
	    $this->get_invalid_data($row_data['error'], $file_type, $file_name);
	}

        log_message('info', __FUNCTION__ . " => Exiting now...");

    }

    /**
     * @desc: This method is used to validate Phone number while upload excel file
     * We will count of invalidate data, If count is greater or equal to five.
     * It will send Invalidate data to mail and exit from function
     * Otherwise return data with inavlidate data
     * @param: Array
     * @param: Array
     */
    function validate_phone_number($data, $filetype, $file_name) {
	$invalid_data = array();
	$valid_data = array();
        $status = array();
	foreach ($data as $key => $value) {
	   
	    if (count($invalid_data) > 4) {
		$status['invalid_phone'] = $invalid_data;
		$this->get_invalid_data($status, $filetype, $file_name);
                
		exit();
	    }
            
	    // check mobile number validation
	    if (!preg_match('/^\d{10}$/', $value['Phone'])) {
		unset($data[$key]);
		array_push($invalid_data, $value);
	    }
            
	}
        $valid_data['valid_data'] = $data;
	// append invalid data. size of invalid data is less than 5
	if (!empty($invalid_data)) {
	    log_message('info', __FUNCTION__ . ' =>  Phone Number is not valid Excel data: ' .
		print_r($invalid_data, true));

	    $valid_data['error']['invalid_phone'] = $invalid_data;
	}
       
	return $valid_data;
    }

    /**
     * @desc: This method is used to validate Product number while upload excel file
     * We will count of invalidate data, If count is greater or equal to five.
     * It will send Invalidate data to mail and exit from function
     * Otherwise return data with inavlidate data
     * In Case valid row, we will append service id in the data row
     * @param: Array $data
     * @param: String $filetype
     */
    function validate_product($data, $filetype, $file_name) {
	$invalid_data = array();
        $status = array();
	foreach ($data['valid_data'] as $key => $value) {
	    $flag = 0;
	    if (count($invalid_data) > 4) {
		$status['invalid'] = $invalid_data;
                
		// Add Only user
		$this->add_user_for_invalid($invalid_data);
		$this->get_invalid_data($status, $filetype, $file_name);
		
                exit();
	    }
          
	    $prod = trim($value['Product']);
            
	    if (stristr($prod, "Washing Machine") || stristr($prod, "WashingMachine") || stristr($prod, "Dryer")) {
		$data['valid_data'][$key]['appliance'] = 'Washing Machine';
	    }
	    if (stristr($prod, "Television")) {
		$data['valid_data'][$key]['appliance'] = 'Television';
	    }
	    if (stristr($prod, "Airconditioner") || stristr($prod, "Air Conditioner")) {
		$data['valid_data'][$key]['appliance'] = 'Air Conditioner';
	    }
	    if (stristr($prod, "Refrigerator")) {
		$data['valid_data'][$key]['appliance'] = 'Refrigerator';
	    }
	    if (stristr($prod, "Microwave")) {
		$data['valid_data'][$key]['appliance'] = 'Microwave';
	    }
	    if (stristr($prod, "Purifier")) {
		$data['valid_data'][$key]['appliance'] = 'Water Purifier';
	    }
	    if (stristr($prod, "Chimney")) {
		$data['valid_data'][$key]['appliance'] = 'Chimney';
	    }
	    if (stristr($prod, "Geyser")) {
		$data['valid_data'][$key]['appliance'] = 'Geyser';
	    }
	    // Block Microvare cooking. If its exist in the Excel file
	    if (stristr($prod, "microwave cooking")) {
		$flag = 1;
		unset($data['valid_data'][$key]);
		array_push($invalid_data, $value);
	    }
	    // Block Tds Meter. If its exist in the Excel file
	    if (stristr($prod, "Tds Meter")) {
		$flag = 1;
		unset($data['valid_data'][$key]);
		array_push($invalid_data, $value);
	    }
	    // Block Accessories. If its exist in the Excel file
	    if (stristr($prod, "Accessories")) {
		$flag = 1;
		unset($data['valid_data'][$key]);
		array_push($invalid_data, $value);
	    }

	    if ($flag == 0) {
		$service_id = $this->booking_model->getServiceId($data['valid_data'][$key]['appliance']);
		if ($service_id) {

		    $data['valid_data'][$key]['service_id'] = $service_id;
		} else {
		    unset($data['valid_data'][$key]);
		    array_push($invalid_data, $value);
		}
	    }
	}

	if (!empty($invalid_data)) {
	    log_message('info', __FUNCTION__ . ' =>  Product is not valid in Excel data: ' .
		print_r($invalid_data, true));

	    $data['error']['invalid_product'] = $invalid_data;
            
	    // Add Only user
	    $this->add_user_for_invalid($invalid_data);
	}
        
       

	return $data;
    }

    /**
     * @desc: This is used to remove unproductive row. it validate in Product type.
     * We will store key which we have to remove data, if it exist in the file
     * @param array $data
     * @param String $file_name
     * @return array
     */
    function validate_product_type($data) {
	$invalid_data = array();
	// get unproductive description array
	$unproductive_description = $this->unproductive_product();
	foreach ($data['valid_data'] as $key => $value) {

	    $prod = trim($value['Product_Type']);

	    foreach ($unproductive_description as $un_description) {
		if (stristr($prod, $un_description)) {
		    unset($data['valid_data'][$key]);
		    array_push($invalid_data, $value);
		}
	    }
	}
	if (!empty($invalid_data)) {
	    log_message('info', __FUNCTION__ . ' =>  Product description is not valid in Excel data: ' .
		print_r($invalid_data, true));

	    $data['error']['invalid_product_type'] = $invalid_data;
           // $data['error']['invalid_title'][] =  "Product description is not valid in Excel data:";
	    // Add Only user
	    $this->add_user_for_invalid($invalid_data);
	}

	return $data;
    }

    /**
     * @desc: This is used to validate pincode. pincode must be 6 digit integer.
     * If count of invalid pincode is greater than 4 then it trigger a mail and exit function.
     * If count of invalid pincode is less than 4 then we will append invalid array into error index
     * @param: Array
     * @return: Array
     */
    function validate_pincode($data, $filetype, $file_name) {
        $status = array();
	$invalid_data = array();
	foreach ($data['valid_data'] as $key => $value) {
	    if (count($invalid_data) > 4) {
		$status['invalid_pincode'] = $invalid_data;
		
                // Add Only user
		$this->add_user_for_invalid($invalid_data);
		$this->get_invalid_data($status, $filetype, $file_name);
                
		exit();
	    }
	    // check pincode is 6 digit
	    if (!preg_match('/^\d{6}$/', $value['Pincode'])) {

		unset($data['valid_data'][$key]);
		array_push($invalid_data, $value);
	    }
	}
	// append invalidate data. size of invalidate data is less than 5
	if (!empty($invalid_data)) {
	    log_message('info', __FUNCTION__ . ' =>  Pincode is not valid in Excel data: ' .
		print_r($invalid_data, true));

	    $data['error']['invalid_pincode'] = $invalid_data;
            
	    // Add Only user
	    $this->add_user_for_invalid($invalid_data);
	}

        return $data;
    }

    /**
     * @desc: This is used to validate delivery date for both type of files.
     * if delivery file is uploaded then it unset future date and
     * if count is greater than 5, it exit and trigger mail.
     * If shipped file is uploded then return count future and past date
     */
    function validate_delivery_date($data, $file_type, $file_name) {
        $status = array();
	$invalid_data = array();
	$future_date = 0;
	$past_date = 0;
	foreach ($data['valid_data'] as $key => $value) {
            if(isset($value['Delivery_Start_Date'])){
                $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($value['Delivery_Start_Date']);
            } else {
                $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($value['Delivery_Date']);
            }
	    
	    if (count($invalid_data) > 4) {
            	$status['invalid_date'] = $invalid_data;

		// Add Only user
		$this->add_user_for_invalid($invalid_data);
		$this->get_invalid_data($status, $file_type, $file_name);
		exit();
	    }
	    if ($file_type == "delivered") {
		if (date('Y-m-d') < $dateObj2->format('Y-m-d')) {
		    //Future Date
		    unset($data['valid_data'][$key]);
		    array_push($invalid_data, $value);
		}
	    } else if ($file_type == "shipped") {
		if (date('Y-m-d') < $dateObj2->format('Y-m-d')) {
		    //Future Date
		    $future_date++;
		} else {
		    $past_date++;
		}
	    }
	}

	if (!empty($invalid_data)) {
	    log_message('info', __FUNCTION__ . ' =>  Shipped/delivered date is not valid in Excel data: ' .
		print_r($invalid_data, true));
	    
	    $data['error']['invalid_date'] = $invalid_data;
	    // Add Only user
	    $this->add_user_for_invalid($invalid_data);
	}
        
	// Past date and future date
	if ($file_type == "shipped") {
	    $data['error']['count_past_delivery_date'] = $past_date;
	    $data['error']['count_future_delivery_date'] = $future_date;
	}

	return $data;
    }

    /**
     * @desc: This method is used to check order id. Order Id should not be null or order id
     * If order id is null or empty then it unset row and continue.
     * @param array $data
     * @return array
     */
    function validate_order_id($data){
        $invalid_data = array();
        foreach ($data['valid_data'] as $key => $value) {

	    if (is_null($value['Sub_Order_ID']) || $value['Sub_Order_ID'] = "") {

		unset($data['valid_data'][$key]);
                array_push($invalid_data, $value);
	    }
	}

        if(!empty($invalid_data)){
            $data['error']['invalid_order_id'] = $invalid_data;
           
	    // Add Only user
	    $this->add_user_for_invalid($invalid_data);
	}
        
        return $data;
    }

    /**
     * @desc: This method checks, Order should not be equal to Phone number
     * @param Array $data
     * @param String $filetype
     * @return Array
     */
    function validate_order_id_same_as_phone($data, $filetype,$file_name) {
	$invalid_data = array();
	foreach ($data['valid_data'] as $key => $value) {
	    if (count($invalid_data) > 4) {

		
		$status['invalid_same_order_id_phone'] = $invalid_data;
		// Add Only user
		$this->add_user_for_invalid($invalid_data);
		$this->get_invalid_data($status, $filetype, $file_name);
		exit();
	    }
	    if ($value['Sub_Order_ID'] == $value['Phone']) {
		unset($data['valid_data'][$key]);
		array_push($invalid_data, $value);
	    }
	}

	if (!empty($invalid_data)) {
	    log_message('info', __FUNCTION__ . ' =>  Order ID is same as Phone Number: ' .
		print_r($invalid_data, true));

	    $data['error']['invalid_same_order_id_phone'] = $invalid_data;
           
	    // Add Only user
	    $this->add_user_for_invalid($invalid_data);
	}

	return $data;
    }

    /**
     * @desc: This is used to send Json invalid data to mail
     * @param Array $invalid_data_with_reason
     * @param string $filetype
     */
    function get_invalid_data($invalid_data_with_reason, $filetype, $file_name) {
	$to = "anuj@247around.com";
	$from = "booking@247around.com";
	$cc = "";
	$bcc = "";
	$subject = "";
        
	if ($filetype == "shipped") {

	    $subject = "Shipped File is uploaded";
	    $message = " Please check shipped file data:<br/>";
	} else {
	    $subject = "Delivered File is uploaded";
	    $message = " Please check delivered file data:<br/>";
	}
        $invalid_data_with_reason['file_name']= $file_name;
             
        $html = $this->load->view('employee/invalid_data',$invalid_data_with_reason, TRUE);
	$this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $html, "");
    }
    
  

    /**
     * @desc: This method is used to send sms to snapdeal shipped customer, whose edd is not tommorrow. It gets appliance free or not from notify.
     * Make sure array of smsData has index services first then message
     * @param string $appliance
     * @param string $phone_number
     * @param string $user_id
     * @param string $booking_id
     * @param string $file_type - Deliverd or Shipped
     */
    function send_sms_to_snapdeal_customer($appliance, $phone_number, $user_id, $booking_id, $file_type) {
        switch ($file_type) {
            case "shipped":
                $sms['tag'] = "sd_shipped_missed_call_initial";

                //ordering of smsData is important, it should be as per the %s in the SMS
                $sms['smsData']['message'] = $this->notify->get_product_free_not($appliance);
                $sms['smsData']['service'] = $appliance;

                break;
            
            case "delivered":
                $sms['tag'] = "sd_delivered_missed_call_initial";

                //ordering of smsData is important, it should be as per the %s in the SMS
                $sms['smsData']['message'] = $this->notify->get_product_free_not($appliance);
                $sms['smsData']['service'] = $appliance;
                
                break;

            default:
                return 0;
        }
        
	$sms['phone_no'] = $phone_number;        
	$sms['booking_id'] = $booking_id;
	$sms['type'] = "user";
	$sms['type_id'] = $user_id;

	$this->notify->send_sms($sms);
    }

    /**
     * @desc: This method ued to insert data into partner leads table.
     * @param: Array Booking details
     * @param: Array Unit details
     * @param: Array User details
     * @param: String Service Name
     */
    function insert_booking_in_partner_leads($booking, $unit_details, $user_details, $product) {
	$partner_booking['PartnerID'] = $booking['partner_id'];
	$partner_booking['OrderID'] = $booking['order_id'];
	$partner_booking['247aroundBookingID'] = $booking['booking_id'];
	$partner_booking['Product'] = $product;
	$partner_booking['Brand'] = $unit_details['appliance_brand'];
	$partner_booking['Model'] = $unit_details['model_number'];
	$partner_booking['ProductType'] = $unit_details['appliance_description'];
	$partner_booking['Category'] = $unit_details['appliance_category'];
	$partner_booking['Name'] = $user_details['name'];
	$partner_booking['Mobile'] = $booking['booking_primary_contact_no'];
	$partner_booking['AlternatePhone'] = $booking['booking_alternate_contact_no'];
	$partner_booking['Email'] = $user_details['user_email'];
	//$partner_booking['Landmark'] = $booking['booking_landmark'];
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
	if ($partner_leads_id) {
	    return true;
	} else {
	    log_message('info', __FUNCTION__ . " Booking is not inserted into Partner Leads table:" . print_r($partner_booking, true));
	}
    }

    /**
     * @desc: This is used to store key. If this key exists in the SD dile then we will remove that row.
     * @return array
     */
    function unproductive_product() {
	$unproductive_description = array(
	    'Tds Meter',
	    'Water Purifier Accessories',
	    'Room Heater',
	    'Immersion Rod',
            '(PNG /LPG) Geyser'
	);

	return $unproductive_description;
    }

    /**
     * @desc: This function is used to add user only incase invalid data
     * @param Array $row_data
     * @return boolean true
     */
    function add_user_for_invalid($row_data) {
	foreach ($row_data as $value) {

            $output = $this->user_model->search_user(trim($value['Phone']));
            $state = $this->vendor_model->get_state_from_pincode($value['Pincode']);

            if (empty($output)) {
                //User doesn't exist
                if (isset($value['Customer_Name']) || isset($value['Phone']) || isset($value['Customer_Address']) || isset($value['Pincode'])) {
                    $user['name'] = $value['Customer_Name'];
                    $user['phone_number'] = $value['Phone'];
                    $user['user_email'] = (isset($value['Email_ID']) ? $value['Email_ID'] : "");
                    $user['home_address'] = $value['Customer_Address'];
                    $user['pincode'] = $value['Pincode'];
                    $user['city'] = $value['CITY'];
                    $user['state'] = $state['state'];

                    $user_id = $this->user_model->add_user($user);
                    //echo print_r($user, true), EOL;
                    //Add sample appliances for this user
                    $count = $this->booking_model->getApplianceCountByUser($user_id);
                    //Add sample appliances if user has < 5 appliances in wallet
                    if ($count < 5) {
                        $this->booking_model->addSampleAppliances($user_id, 5 - intval($count));
                    }
                }
            }
        }
        
        return true;
    }

}