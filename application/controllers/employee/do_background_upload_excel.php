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
    
    var $ColumnFailed = "";
    var $finalArray = array();
    var $email_message_id = "";
    var $is_send_file_back = "";
    var $file_read_column = "";
    var $file_write_column = "";
    var $revert_file_email = "";
    var $send_file_back_data = array();

    /**
     * load list modal and helpers
     */
    function __Construct() {
	parent::__Construct();

	$this->load->helper(array('form', 'url'));

        $this->load->library('asynchronous_lib');
        $this->load->library('miscelleneous');
        $this->load->library('notify');
        $this->load->helper(array('form', 'url'));
        $this->load->helper('download');

        $this->load->library('form_validation');
        $this->load->library('s3');
        $this->load->library('PHPReport');
        $this->load->library('partner_utilities');
        $this->load->library('booking_utilities');

        $this->load->model('user_model');
        $this->load->model('upcountry_model');
        $this->load->model('booking_model');
        $this->load->model('partner_model');
        $this->load->model('vendor_model');
        $this->load->model('reporting_utils');
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
    function upload_snapdeal_file($file_type = "") {
	log_message('info', __FUNCTION__ . "=> File type: " . $file_type . ", Beginning processing...");

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
        //Email Message ID - Unique for every email
        $this->email_message_id = !($this->input->post('email_message_id') === NULL)?$this->input->post('email_message_id'):'';
        if(!empty($this->input->post('email_send_to'))){
            $this->email_send_to = $this->input->post('email_send_to');
        }
	//  Get worksheet dimensions
	$sheet = $objPHPExcel->getSheet(0);
	$highestRow = $sheet->getHighestRow();
	$highestColumn = $sheet->getHighestDataColumn();
        
        //Validation for Empty File
        if($highestRow <=1){
            //Logging
            log_message('info',__FUNCTION__.' Empty File Uploaded for Snapdeal File Upload - Type :'.$file_type);
            $this->session->set_flashdata('file_error','Empty file has been uploaded');
            if(!empty($file_type)){
                if($file_type == 'delivered'){
                    redirect(base_url() . "employee/booking_excel");
                }else{
                    redirect(base_url() . "employee/booking_excel/upload_shipped_products_excel");
                }
            }
        }
        
	$headings = $sheet->rangeToArray('A1:' . $highestColumn . 1, NULL, TRUE, FALSE);
	$headings_new = array();
	$data = array();
        $shipped_data = array();
        $delivered_data = array();

        foreach ($headings as $heading) {
	    $heading = str_replace(array("/", "(", ")", "."), "", $heading);
	    array_push($headings_new, array_map("strtolower", str_replace(array(" "), "_", $heading)));
	}
        
	for ($row = 2, $i = 0; $row <= $highestRow; $row++, $i++) {
	    //  Read a row of data into an array
	    $rowData_array = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            if(!empty($rowData_array[0][11])){
               
	        $rowData = array_combine($headings_new[0], $rowData_array[0]);
                //Check isset type of data column
                if(isset($rowData['type_of_data'])){
                    
                    if($rowData['type_of_data'] == 'Shipped'){
                       if(isset($rowData['delivery_end_date'])){
                            //pushed Shipped data into varible $shipped_data
                            array_push($shipped_data, $rowData);
                       } else{
                           $subject = "Delivery END Date Column is not exist. SD Uploading Failed.";
                           $message  = $file_name. " is not uploaded";
                           $this->send_mail_column($subject, $message, false,_247AROUND_SNAPDEAL_SHIPPED,SNAPDEAL_ID,SNAPDEAL_FAILED_FILE_UPLOAD_SHIPPED);
                       }
                        
                    } else if($rowData['type_of_data'] == 'Delivered'){
                        if(isset($rowData['delivery_date'])){
                            //pushed Shipped data into varible $delivery_data
                            $rowData['partner_source'] = "Snapdeal-delivered-excel";
                            array_push($delivered_data, $rowData);
                            
                        } else {
                             $subject = "Delivery Date Column is not exist. SD Uploading Failed.";
                             $message  = $file_name. " is not uploaded";
                             $this->send_mail_column($subject, $message, false,_247AROUND_SNAPDEAL_DELIVERED,SNAPDEAL_ID,SNAPDEAL_FAILED_FILE_UPLOAD_DELIVERED);
                        }
                    }

                } else if(isset($rowData['delivery_date'])){
                    if($file_type == 'delivered'){
                        $rowData['partner_source'] = "Snapdeal-delivered-excel";     
                    }
                    array_push($data, $rowData);
                } else {
                    $subject = "Delivery Date Column is not exist. SD Uploading Failed.";
                    $agent_name = !empty($this->session->userdata('emp_name'))?$this->session->userdata('emp_name'):_247AROUND_DEFAULT_AGENT_NAME;
                    $message  = $file_name. " is not uploaded Agent Name: ".  $agent_name;
                    $this->send_mail_column($subject, $message, false,_247AROUND_SNAPDEAL_DELIVERED,SNAPDEAL_ID,SNAPDEAL_FAILED_FILE_UPLOAD_DELIVERED);
                }
            } 
	}
        
        // For shipped data
        if(!empty($shipped_data)){
            $this->process_upload_sd_file($shipped_data,"shipped", $file_name,SNAPDEAL_ID);
            $this->miscelleneous->update_file_uploads($_FILES["file"]["name"],$_FILES["file"]["tmp_name"],_247AROUND_SNAPDEAL_SHIPPED,FILE_UPLOAD_SUCCESS_STATUS,$this->email_message_id);
        }
        //For delivered data
        if(!empty($delivered_data)){
            $this->process_upload_sd_file($delivered_data,"delivered", $file_name,SNAPDEAL_ID);
            $this->miscelleneous->update_file_uploads($_FILES["file"]["name"],$_FILES["file"]["tmp_name"],_247AROUND_SNAPDEAL_DELIVERED,FILE_UPLOAD_SUCCESS_STATUS,$this->email_message_id);
        }
        // for both type of file
        if(!empty($data)){
            $this->process_upload_sd_file($data,$file_type, $file_name,SNAPDEAL_ID);
            if($file_type === 'delivered'){
                $type = _247AROUND_SNAPDEAL_DELIVERED;
            }else{
                $type = _247AROUND_SNAPDEAL_SHIPPED;
            }
            log_message("info","both");
            $this->miscelleneous->update_file_uploads($_FILES["file"]["name"],$_FILES["file"]["tmp_name"],$type,FILE_UPLOAD_SUCCESS_STATUS,$this->email_message_id);
        }
        
    }
    /**
     * @desc: this is used to send mail while validation pass and column is not exist
     * @param String $subject
     * @param String $message
     * @param boolean $validation
     */
    function send_mail_column($subject, $message, $validation,$file_type,$partner_id,$emailTag){
        if(empty($this->email_send_to)){
            if(empty($this->session->userdata('official_email'))){
                $get_partner_am_id = $this->partner_model->getpartner_details('account_manager_id', array('partners.id' => $partner_id));
                if (!empty($get_partner_am_id[0]['account_manager_id'])) {
                    $file_upload_agent_email = $this->employee_model->getemployeefromid($get_partner_am_id[0]['account_manager_id'])[0]['official_email'];
                }else{
                    $file_upload_agent_email = _247AROUND_SALES_EMAIL;
                }
            }else{
                $file_upload_agent_email = $this->session->userdata('official_email');
            }
            
            $this->email_send_to = $file_upload_agent_email;
        }else{
            $file_upload_agent_email = $this->email_send_to;
        }
        
        
        $to = NITS_ANUJ_EMAIL_ID.",".$file_upload_agent_email;
        $from = "noreply@247around.com";
        $cc = "";
        $bcc = "";
        $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, "",$emailTag);
        log_message('info', __FUNCTION__ . "=> Validation ". $validation."  ".$message);
        if ($validation == false) {
            if ($partner_id == SNAPDEAL_ID) {
                if ($file_type === 'delivered') {
                    $type = _247AROUND_SNAPDEAL_DELIVERED;
                } else {
                    $type = _247AROUND_SNAPDEAL_SHIPPED;
                }
                $this->miscelleneous->update_file_uploads($_FILES["file"]["name"], $_FILES["file"]["tmp_name"], $type, FILE_UPLOAD_FAILED_STATUS, $this->email_message_id);
            }

            exit();
        }
    }
    
    function process_upload_sd_file($data,$file_type, $file_name,$default_partner){
        // Warning: Do not Change Validation Orderddd
	$validate_data = $this->validate_phone_number($data, $file_type, $file_name,$default_partner);
	$row_data1 = $this->validate_product($validate_data, $file_type, $file_name,$default_partner);
	$row_data2 = $this->validate_delivery_date($row_data1, $file_type, $file_name,$default_partner);
	$row_data3 = $this->validate_pincode($row_data2, $file_type, $file_name,$default_partner);
	$row_data4 = $this->validate_order_id($row_data3);
	$row_data5 = $this->validate_product_type($row_data4);
	$row_data = $this->validate_order_id_same_as_phone($row_data5, $file_type,$file_name,$default_partner);
        
        $subject = $file_type ." data validated. File is under process";
        $message  = $file_name. " validation Pass. File is under process";
        $this->send_mail_column($subject, $message, TRUE,$file_type,$default_partner,SNAPDEAL_VALIDATION_PASS);

	$count_total_leads_came_today = count($data);
	log_message('info', __FUNCTION__ . "=> File type: " . $file_type . 
                ", Count_total_leads_came_today: " . $count_total_leads_came_today);
	$count_booking_inserted = 0;
	$count_booking_updated = 0;
	$count_booking_not_updated = 0;
        
	foreach ($row_data['valid_data'] as $key => $value) {
            $phone = explode('/', $value['phone']);
            $value['pincode'] = trim(str_replace(" ", "", trim($value['pincode'])));
	    //echo print_r($rowData[0], true), EOL;
	    if ($value['phone'] == "") {
		//echo print_r("Phone number null, break from this loop", true), EOL;
		break;
	    }
            
            //Sanitizing Brand Name
            if(!empty($value['brand'])){
                $value['brand'] = preg_replace('/[^A-Za-z0-9 ]/', '', $value['brand']);
            }
            
	    //Insert user if phone number doesn't exist
            $output = $this->user_model->get_users_by_any(array("users.phone_number" => trim($phone[0])));
	    
            $distict_details = $this->vendor_model->get_distict_details_from_india_pincode(trim($value['pincode']));
            
	    if (empty($output)) {
		//User doesn't exist
                $user = array();
                $user['user_email'] = (isset($value['email_id']) ? $value['email_id'] : "");
                $user['name'] = $this->miscelleneous->is_user_name_empty(trim($value['customer_name']), $user['user_email'], $phone[0]);
		$user['phone_number'] = $phone[0];
                if(isset($phone[1])){
                    $user['alternate_phone_number'] = $phone[1];
                }
		$user['home_address'] = $value['customer_address'];
		$user['pincode'] = trim($value['pincode']);
		$user['city'] = !empty($value['city'])?$value['city']:$distict_details['district'];
		$user['state'] = $distict_details['state'];

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
		$user['user_email'] = (isset($value['email_id']) ? $value['email_id'] : "");
		$user['name'] = $value['customer_name'];
	    }

	    
	    //Assigning Booking Source and Partner ID for Brand Requested
            // First we send Service id and Brand and get Partner_id from it
            // Now we send state, partner_id and service_id 
            $value['brand'] = isset($value['service_appliance_data']['brand'])?$value['service_appliance_data']['brand'] :$value['brand'];
            $value['brand'] = trim(str_replace("'", "", $value['brand']));
            $data = $this->miscelleneous->_allot_source_partner_id_for_pincode($value['service_id'], $distict_details['state'], $value['brand'],$default_partner);
            if ($data) {
                $booking['partner_id'] = $data['partner_id'];
                $booking['source'] = $data['source'];
                
                $check_partner_booking = $this->partner_model->get_order_id_for_partner($booking['partner_id'], $value['sub_order_id']);
                
                if(!is_null($check_partner_booking)){
                    $partner_booking = $check_partner_booking;
                }else{
                    if (isset($value['order_item_id']) && !empty($value['order_item_id'])) {
                        $booking['order_id'] = $value['sub_order_id'] . "-" . $value['order_item_id'];
                    } else if (isset($value['item_id']) && !empty($value['item_id'])) {
                        $booking['order_id'] = $value['sub_order_id'] . "-" . $value['item_id'];
                    } else {
                        $booking['order_id'] = $value['sub_order_id'];
                    }
                    
                    $partner_booking = $this->partner_model->get_order_id_for_partner($booking['partner_id'], $booking['order_id']);
                }
                
                
                //log_message('info', print_r($partner_booking, TRUE));
                //Check whether order id exists or not
                if (is_null($partner_booking)) {
                    log_message('info', __FUNCTION__ . "=> File type: " . $file_type .
                            ", Order ID NOT found: " . $value['sub_order_id']);
                    //order id not found
                    $appliance_details['user_id'] = $booking['user_id'] = $user_id;
                    $appliance_details['service_id'] = $unit_details['service_id'] = $booking['service_id'] = $value['service_id'];
                    $booking['booking_pincode'] = trim($value['pincode']);
                    $where = array('service_id' => $value['service_id'], 'brand_name' => trim($value['brand']));
                    $brand_id_array = $this->booking_model->get_brand($where);
                    // If brand not exist then insert into table
                    if (empty($brand_id_array)) {

                        $inserted_brand_id = $this->booking_model->addNewApplianceBrand($value['service_id'], trim($value['brand']));
                        if (!empty($inserted_brand_id)) {
                            log_message('info', __FUNCTION__ . ' Brand added successfully in Appliance Brands Table ' . $value['brand']);
                        } else {
                            log_message('info', __FUNCTION__ . ' Error in adding brands in Appliance Brands ' . $value['brand']);
                        }
                    }
                    $appliance_details['brand'] = $unit_details['appliance_brand'] = trim($value['brand']);

                    switch ($file_type) {
                        case 'delivered':
                            if (isset($value['fso_delivery_date'])) {
                                $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($value['fso_delivery_date']);
                            } else if(isset($value['delivery_date'])){
                                $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($value['delivery_date']);
                            } else {
                                $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject();
                            }
                            //For delivered file, set booking date empty so that the queries come on top of the page
                            $yy = date("y");
                            $mm = date("m");
                            $dd = date("d");
                            $booking['partner_source'] = $value['partner_source'];
                            $booking['booking_date'] = '';

                            //Set delivered date only
                            $booking['delivery_date'] = $dateObj2->format('Y-m-d H:i:s');
                            //$booking['estimated_delivery_date'] = '';
                            $booking['backup_delivery_date'] = isset($value['delivery_date'])?$value['delivery_date']:'';
                            //$booking['backup_estimated_delivery_date'] = '';

                            $booking['internal_status'] = "Missed_call_not_confirmed";
                            $booking['query_remarks'] = 'Product Delivered, Call Customer For Booking';
                            $booking['booking_remarks'] = 'Installation and Demo';
                            $booking['booking_timeslot'] = '4PM-7PM';
                            break;
                            
                        default :
                            if (isset($value['delivery_end_date'])) {
                                $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($value['delivery_end_date']);
                            } else {
                                $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($value['delivery_date']);
                            }

                            if ($dateObj2->format('d') == date('d')) {
                                //If date is NULL, add 3 days from today in EDD.
                                $dateObj2 = date_create('+3days');
                            }
                            $yy = $dateObj2->format('y');
                            $mm = $dateObj2->format('m');
                            $dd = $dateObj2->format('d');
                            $booking['partner_source'] = "Snapdeal-shipped-excel";
                            $booking['booking_date'] = $dateObj2->format('d-m-Y');

                            // Set EDD only
                            $booking['estimated_delivery_date'] = $dateObj2->format('Y-m-d H:i:s');
                            $booking['delivery_date'] = '';
                            $booking['backup_estimated_delivery_date'] = $value['delivery_date'];
                            $booking['backup_delivery_date'] = '';

                            //Tag internal status for missed call
                            $booking['internal_status'] = "Missed_call_not_confirmed";
                            $booking['query_remarks'] = 'Product Shipped';
                            $booking['booking_remarks'] = 'Installation and Demo';
                            $booking['booking_timeslot'] = '4PM-7PM';

                            break;
                    }

                    //log_message('info', print_r($dateObj2, true));

                    $booking['booking_id'] = str_pad($booking['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
                    $booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($booking['user_id'])) + 1);
                    $booking['booking_id'] = "Q-" . $booking['source'] . "-" . $booking['booking_id'];
                    $unit_details['booking_id'] = $booking['booking_id'];
                    $unit_details['partner_id'] = $booking['partner_id'];

                    //Use this to remove special chars:
                    //preg_replace('/[^(\x20-\x7F)]*/','', $string);
                    $appliance_details['description'] = $unit_details['appliance_description'] = preg_replace('/[^(\x20-\x7F)]*/', '', $value['product_type']);

                    $appliance_details['category'] = $unit_details['appliance_category'] = isset($value['service_appliance_data']['category']) ? $value['service_appliance_data']['category'] : '';

                    $appliance_details['capacity'] = $unit_details['appliance_capacity'] = isset($value['service_appliance_data']['capacity']) ? $value['service_appliance_data']['capacity'] : '';
                    $appliance_details['model_number'] = $unit_details['model_number'] = $value['model'];
                    $appliance_details['tag'] = $value['brand'] . " " . $value['product'];
                    $booking['booking_remarks'] = '';
                    $booking['booking_alternate_contact_no'] = '';
                    $appliance_details['purchase_date'] = $unit_details['purchase_date'] = date('Y-m-d');
                    $appliance_details['last_service_date'] = date('d-m-Y');
                    //get partner data to check the price

                    $this->initialized_variable->fetch_partner_data($booking['partner_id']);

                    if ($this->initialized_variable->get_partner_data()[0]['partner_type'] == OEM) {
                        //if partner type is OEM then sent appliance brand in argument
                        $prices = $this->partner_model->getPrices($booking['service_id'], $unit_details['appliance_category'], $unit_details['appliance_capacity'], $booking['partner_id'], 'Installation & Demo', $unit_details['appliance_brand'], false);
                    } else {
                        //if partner type is not OEM then dose not sent appliance brand in argument
                        $prices = $this->partner_model->getPrices($booking['service_id'], $unit_details['appliance_category'], $unit_details['appliance_capacity'], $booking['partner_id'], 'Installation & Demo', "", false);
                    }
                    $booking['amount_due'] = '0';
                    $is_price = array();
                    if (!empty($prices) && count($prices) == 1) {
                        $unit_details['id'] = $prices[0]['id'];
                        $unit_details['price_tags'] = $prices[0]['service_category'];
                        $unit_details['around_paid_basic_charges'] = $unit_details['around_net_payable'] = "0.00";
                        $unit_details['partner_paid_basic_charges'] = $prices[0]['partner_net_payable'];
                        $unit_details['partner_net_payable'] = $prices[0]['partner_net_payable'];
                        $booking['amount_due'] = $prices[0]['customer_net_payable'];
                        $is_price['customer_net_payable'] = $prices[0]['customer_net_payable'];
                        $is_price['is_upcountry'] = $prices[0]['is_upcountry'];
                    }
                    
                    $unit_details['booking_status'] = _247AROUND_FOLLOWUP;

                    $ref_date = !empty($value['referred_date_and_time'])?PHPExcel_Shared_Date::ExcelToPHPObject($value['referred_date_and_time']):PHPExcel_Shared_Date::ExcelToPHPObject();
                    $booking['reference_date'] = $ref_date->format('Y-m-d H:i:s');
                    $booking['booking_timeslot'] = '';
                    $booking['request_type'] = 'Installation & Demo';
                    $booking['booking_primary_contact_no'] = $phone[0];
                    $booking['create_date'] = date('Y-m-d H:i:s');
                    $booking['current_status'] = _247AROUND_FOLLOWUP;
                    $booking['type'] = "Query";
                    $booking['booking_address'] = $value['customer_address'];
                    $booking['city'] = !empty($value['city'])?$value['city']:$distict_details['district'];
                    $booking['state'] = $distict_details['state'];
                    $booking['district'] = $distict_details['district'];
                    $booking['taluk'] = $distict_details['taluk'];
                    $booking['quantity'] = '1';
                    
                    if (isset($value['order_item_id'])) {
                        $unit_details['sub_order_id'] = $value['order_item_id'];
                    } else if (isset($value['item_id'])) {
                        $unit_details['sub_order_id'] = $value['item_id'];
                    }

                    
                    //capture service_promise_date if it exist
                    if (isset($value['service_promise_date']) && !empty($value['service_promise_date'])) {


                        $spd = str_replace('/', '-', $value['service_promise_date']);

                        $booking['service_promise_date'] = date("Y-m-d H:i:s", strtotime($spd));
                    }
                    
                    //check partner status from partner_booking_status_mapping table  
                    $actor = $next_action = 'NULL';
                    $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'], $booking['partner_id'], $booking['booking_id']);
                    if (!empty($partner_status)) {
                        $booking['partner_current_status'] = $partner_status[0];
                        $booking['partner_internal_status'] = $partner_status[1];
                        $actor = $booking['actor'] = $partner_status[2];
                        $next_action = $booking['next_action'] = $partner_status[3];
                    }

                    //Send SMS to customers regarding delivery confirmation through missed call for delivered file only
                    //Check whether vendor is available or not
                    if($booking['partner_id'] == GOOGLE_FLIPKART_PARTNER_ID){
                        $booking['sms_count'] = 1;
                        $booking['internal_status'] = _247AROUND_FOLLOWUP;
                        
                        //send sms to google customer. right now it is hard coded change this in future
                        $sms['tag'] = "flipkart_google_sms";
                        $sms['smsData'] = array();
                        $sms['phone_no'] = $booking['booking_primary_contact_no'];
                        $sms['booking_id'] = $booking['booking_id'];
                        $sms['type'] = "user";
                        $sms['type_id'] = $user_id;
                        $this->notify->send_sms_msg91($sms);
                    }else{
                        $is_sms = $this->miscelleneous->check_upcountry($booking, $value['appliance'], $is_price, $file_type);
                        if (!$is_sms) {
                            $booking['internal_status'] = SF_UNAVAILABLE_SMS_NOT_SENT;
                        } else {
                            $booking['sms_count'] = 1;
                        }
                    }
                    $booking_details_id = $this->booking_model->addbooking($booking);

                    if ($booking_details_id) {
                        log_message('info', __FUNCTION__ . ' =>  Booking is inserted in booking details: ' . $booking['booking_id']);
                        $unit_details['appliance_id'] = $this->booking_model->addappliance($appliance_details);

                        if ($unit_details['appliance_id']) {
                            log_message('info', __METHOD__ . "=> Appliance added: " . $unit_details['appliance_id']);
                            if (!empty($prices)) {
                                $unit_id = $this->booking_model->insert_data_in_booking_unit_details($unit_details, $booking['state'], 0);
                            } else {
                                $unit_id = $this->booking_model->addunitdetails($unit_details);
                            }
                            if ($unit_id) {
                                log_message('info', __METHOD__ . "=> Unit details added: " . print_r($unit_id, true));
                                $tmp['order_id'] = $booking['order_id'];
                                $tmp['booking_id'] = $booking['booking_id'];
                                array_push($this->send_file_back_data, $tmp);
                                $count_booking_inserted++;

                                if(empty($this->session->userdata('id'))){
                                    $this->notify->insert_state_change($booking['booking_id'], _247AROUND_FOLLOWUP, _247AROUND_NEW_QUERY, $booking['query_remarks'], _247AROUND_DEFAULT_AGENT, 
                                            _247AROUND_DEFAULT_AGENT_NAME,$actor,$next_action, _247AROUND);
                                }else{
                                    $this->notify->insert_state_change($booking['booking_id'], _247AROUND_FOLLOWUP, _247AROUND_NEW_QUERY, '', $this->session->userdata('id'), 
                                            $this->session->userdata('employee_id'),$actor,$next_action, _247AROUND);
                                }
                            } else {
                                log_message('info', __FUNCTION__ . ' => ERROR: Booking is not inserted in booking details: '
                                        . print_r($value, true));

                                $row_data['error'][$key]['appliance'] = "Appliance is not inserted";
                                $row_data['error'][$key]['invalid_data'] = $value;
                            }
                        } else {
                            log_message('info', __FUNCTION__ . ' => ERROR: UNIT is not inserted: ' .
                                    print_r($value, true));

                            $row_data['error'][$key]['unit_details'] = " Booking Unit Id is not inserted";
                            $row_data['error'][$key]['invalid_data'] = $value;
                        }
                    } else {
                        log_message('info', __FUNCTION__ . ' => ERROR: Booking is not inserted in booking details: '
                                . print_r($value, true));

                        $row_data['error'][$key]['booking_details'] = " Booking Unit Id is not inserted";
                        $row_data['error'][$key]['invalid_data'] = $value;
                    }

                    if (empty($booking['state'])) {
                        log_message('info', __FUNCTION__ . " => Pincode is not found for booking id: " .
                                $booking['booking_id']);
                        /*
                          $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
                          $send['booking_id'] = $booking['booking_id'];
                          $send['state'] = "Pincode_not_found";
                          $this->asynchronous_lib->do_background_process($url, $send);
                         *
                         */
                    }

                    $this->insert_booking_in_partner_leads($booking, $unit_details, $user, $value['product']);

                    //Reset
                    unset($appliance_details);
                    unset($booking);
                    unset($unit_details);
                } else {
                    //Order ID found
                    log_message('info', __FUNCTION__ . "=> File type: " . $file_type .
                            ", Order ID found: " . $value['sub_order_id']);
                    $status = $partner_booking['current_status'];
                    $int_status = $partner_booking['internal_status'];

                    switch ($file_type) {
                        case 'delivered':
                            //If state is followup and booking date not empty, reset the date
                            if ($status == "FollowUp" && $partner_booking['booking_date'] != '' &&
                                    $int_status == 'Missed_call_not_confirmed') {
                                if (isset($value['fso_delivery_date'])) {
                                    $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($value['fso_delivery_date']);
                                } else if(isset($value['Delivery_Date'])){
                                    $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($value['Delivery_Date']);
                                } else {
                                    $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject();
                                }
                                
                                $update_data['delivery_date'] = $dateObj2->format('Y-m-d H:i:s');
                                $update_data['backup_delivery_date'] = isset($value['delivery_date'])?$value['delivery_date']:'';
                                $update_data['booking_date'] = '';
                                $update_data['booking_timeslot'] = '';
                                $update_data['update_date'] = date("Y-m-d H:i:s");

                                $sms_count = 0;

                                $category = isset($value['service_appliance_data']['category']) ? $value['service_appliance_data']['category'] : '';
                                $capacity = isset($value['service_appliance_data']['capacity']) ? $value['service_appliance_data']['capacity'] : '';
                                $brand = isset($value['service_appliance_data']['brand']) ? $value['service_appliance_data']['brand'] : $value['brand'];

                                $this->initialized_variable->fetch_partner_data($partner_booking['partner_id']);

                                if ($this->initialized_variable->get_partner_data()[0]['partner_type'] == OEM) {
                                    $prices = $this->partner_model->getPrices($partner_booking['service_id'], $category, $capacity, $partner_booking['partner_id'], 'Installation & Demo', $brand, false);
                                } else {
                                    $prices = $this->partner_model->getPrices($partner_booking['service_id'], $category, $capacity, $partner_booking['partner_id'], 'Installation & Demo', "", false);
                                }

                                $is_price = array();
                                if (!empty($prices)) {

                                    $is_price['customer_net_payable'] = $prices[0]['customer_net_payable'];
                                    $is_price['is_upcountry'] = $prices[0]['is_upcountry'];
                                }

                                $is_sms = $this->miscelleneous->check_upcountry($partner_booking, $value['appliance'], $is_price, $file_type);
                                if ($is_sms) {
                                    $sms_count = 1;
                                } else {
                                    $update_data['internal_status'] = SF_UNAVAILABLE_SMS_NOT_SENT;

                                    log_message('info', __FUNCTION__ . ' =>  SMS not sent because of Vendor Unavailability for Booking ID: ' . $partner_booking['booking_id']);
                                }

                                $update_data['sms_count'] = $sms_count;

                                $this->booking_model->update_booking($partner_booking['booking_id'], $update_data);
                                $count_booking_updated++;
                                $tmp['order_id'] = $value['sub_order_id'];
                                $tmp['booking_id'] = $partner_booking['booking_id'];
                                array_push($this->send_file_back_data, $tmp);

                                log_message('info', __FUNCTION__ . ' => Updated Partner Lead: ' . $partner_booking['booking_id']);

                                unset($update_data);
                            } else {
                                log_message('info', __FUNCTION__ . ' => Booking Already scheduled, no update required');

                                $count_booking_not_updated++;
                            }

                            break;

                        default :
                            if (isset($value['delivery_end_date'])) {
                                $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($value['delivery_end_date']);
                            } else {
                                $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($value['delivery_date']);
                            }

                            //$dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($value['Expected_Delivery_Date']);
                            $new_estimated_delivery_date = $dateObj2->format('Y-m-d H:i:s');

                            // if ($new_estimated_delivery_date !=  $partner_booking['estimated_delivery_date']) {
                            if (1) {
                                $update_data['estimated_delivery_date'] = $new_estimated_delivery_date;
                                $update_data['backup_estimated_delivery_date'] = $value['delivery_date'];

                                $update_data['update_date'] = date("Y-m-d H:i:s");
                                $this->booking_model->update_booking($partner_booking['booking_id'], $update_data);
                                
                                $count_booking_updated++;
                                $tmp['order_id'] = $value['sub_order_id'];
                                $tmp['booking_id'] = $partner_booking['booking_id'];
                                array_push($this->send_file_back_data, $tmp);

                                unset($update_data);

                                log_message('info', __FUNCTION__ . ' => Updated Partner Lead: ' . $partner_booking['booking_id']);
                            } else {
                                log_message('info', __FUNCTION__ . ' => EDD update for shipped booking NOT required');

                                $count_booking_not_updated++;
                            }
                            break;
                    }
                }
            }
        }

        log_message('info', __FUNCTION__ . " => Exiting the BIG for-each, some IMP counts: " . 
                print_r(array($count_total_leads_came_today, $count_booking_inserted, 
                    $count_booking_updated, $count_booking_not_updated), true));
       

	$row_data['error']['total_booking_inserted'] = $count_booking_inserted;
	$row_data['error']['total_booking_came_today'] = $count_total_leads_came_today;
        $row_data['error']['count_booking_updated'] = $count_booking_updated;
        $row_data['error']['count_booking_not_updated'] = $count_booking_not_updated;
        
	if (isset($row_data['error'])) {
		log_message('info', __FUNCTION__ . "=> File type: " . $file_type . " => Errors found, sending mail now");
	    $this->get_invalid_data($row_data['error'], $file_type, $file_name ,$default_partner,FALSE);
	} else {
            log_message('info', __FUNCTION__ . "=> File type: " . $file_type . " => Wow, no errors found !!!");
        }
 
    log_message('info', __FUNCTION__ . "=> File type: " . $file_type . " => Exiting now...");
    }

    /**
     * @desc: This method is used to validate Phone number while upload excel file
     * We will count of invalid data, If count is greater or equal to five.
     * It will send Invalidate data to mail and exit from function
     * Otherwise return data with inavlidate data
     * @param: Array
     * @param: Array
     */
    function validate_phone_number($data, $filetype, $file_name,$partner_id) {
        log_message('info', __FUNCTION__ . "=> Entering validation routine...");
	$invalid_data = array();
	$valid_data = array();
        $status = array();
	foreach ($data as $key => $value) {

	    if (count($invalid_data) > 4) {
		$status['invalid_phone'] = $invalid_data;
		$this->get_invalid_data($status, $filetype, $file_name,$partner_id);
                log_message('info', __FUNCTION__ . "=> Exiting validation routine: Limit Crossed");

		exit();
	    }
            if(!empty($value['phone'])){
	        // check mobile number validation
                $phone = explode('/', $value['phone']);
	        if (!preg_match('/^\d{10}$/', trim($phone[0]))) {
		    unset($data[$key]);
		    array_push($invalid_data, $value);
	        }
            } else {
                // If Phone is Empty then unset Key
                unset($data[$key]);
            }

	}
        $valid_data['valid_data'] = $data;
	// append invalid data. size of invalid data is less than 5
	if (!empty($invalid_data)) {
	    log_message('info', __FUNCTION__ . ' =>  Phone Number is not valid in Excel data: ' .
		print_r($invalid_data, true));

	    $valid_data['error']['invalid_phone'] = $invalid_data;
	}
        
        log_message('info', __FUNCTION__ . "=> Exiting validation routine: Under Control");

	return $valid_data;
    }

    /**
     * @desc: This method is used to validate Product number while upload excel file
     * We will count of invalid data, If count is greater or equal to five.
     * It will send Invalid data to mail and exit from function
     * Otherwise return data with inavlidate data
     * In Case valid row, we will append service id in the data row
     * @param: Array $data
     * @param: String $filetype
     */
    function validate_product($data, $filetype, $file_name,$partner_id) {
        log_message('info', __FUNCTION__ . "=> Entering validation routine...");
	$invalid_data = array();
        $status = array();
	foreach ($data['valid_data'] as $key => $value) {
	    $flag = 0;
	    if (count($invalid_data) > 4) {
		$status['invalid'] = $invalid_data;

		// Add Only user
		$this->add_user_for_invalid($invalid_data);
		$this->get_invalid_data($status, $filetype, $file_name,$partner_id);
                
                log_message('info', __FUNCTION__ . "=> Exiting validation routine: Limit Crossed");

                exit();
	    }

	    $prod = trim($value['product']);
            
            if(!empty($value['brand'])){
                $where = array('product_description' => trim(preg_replace('/[^(\x20-\x7F)]*/','', $value['product_type'])),
                                'brand' => $value['brand']);
            }else{
                $where = array('product_description' => trim(preg_replace('/[^(\x20-\x7F)]*/','', $value['product_type'])));
            }
            
            //check if service_id already exist or not by using product description
            $service_appliance_data = $this->booking_model->get_service_id_by_appliance_details($where);
            
            if(!empty($service_appliance_data)){
                log_message('info', __FUNCTION__ . "=> Dsecription found");
                $data['valid_data'][$key]['service_id'] = $service_appliance_data[0]['service_id'];
                $data['valid_data'][$key]['service_appliance_data'] = $service_appliance_data[0];
                $data['valid_data'][$key]['appliance'] = $service_appliance_data[0]['services'];
            }
            else{
                  log_message('info', __FUNCTION__ . "=> Dsecription not found");
                if (stristr($prod, "Washing Machine") || stristr($prod, "WashingMachine") || stristr($prod, "Dryer")) {
		$data['valid_data'][$key]['appliance'] = 'Washing Machine';
                }
                if (stristr($prod, "Television") || stristr($prod, "TV") ||  stristr($prod, "Tv") ||  stristr($prod, "LED")) {
                    $data['valid_data'][$key]['appliance'] = 'Television';
                }
                //remove AC beacuse when description contain active then it mapped other appliance booking into ac
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

	    
	}

	if (!empty($invalid_data)) {
	    log_message('info', __FUNCTION__ . ' =>  Product is not valid in Excel data: ' .
		print_r($invalid_data, true));

	    $data['error']['invalid_product'] = $invalid_data;

	    // Add Only user
	    $this->add_user_for_invalid($invalid_data);
	}

        log_message('info', __FUNCTION__ . "=> Exiting validation routine: Under Control");

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
        log_message('info', __FUNCTION__ . "=> Entering validation routine...");
	$invalid_data = array();
	// get unproductive description array
	$unproductive_description = $this->unproductive_product();
	foreach ($data['valid_data'] as $key => $value) {

	    $prod = trim(preg_replace('/[^(\x20-\x7F)]*/','', $value['product_type']));

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
        log_message('info', __FUNCTION__ . "=> Exiting validation routine: Under Control");

	return $data;
    }

    /**
     * @desc: This is used to validate pincode. pincode must be 6 digit integer.
     * If count of invalid pincode is greater than 4 then it trigger a mail and exit function.
     * If count of invalid pincode is less than 4 then we will append invalid array into error index
     * @param: Array
     * @return: Array
     */
    function validate_pincode($data, $filetype, $file_name,$partner_id) {
        log_message('info', __FUNCTION__ . "=> Entering validation routine...");
        $status = array();
	$invalid_data = array();
	foreach ($data['valid_data'] as $key => $value) {
	    if (count($invalid_data) > 4) {
		$status['invalid_pincode'] = $invalid_data;

                // Add Only user
		$this->add_user_for_invalid($invalid_data);
		$this->get_invalid_data($status, $filetype, $file_name,$partner_id);
                log_message('info', __FUNCTION__ . "=> Exiting validation routine: Limit Crossed");

		exit();
	    }
	    // check pincode is 6 digit
	    if (!preg_match('/^\d{6}$/', str_replace(' ', "", trim($value['pincode'])))) {

		unset($data['valid_data'][$key]);
		array_push($invalid_data, $value);
	    }
	}
	// append invalid data. size of invalid data is less than 5
	if (!empty($invalid_data)) {
	    log_message('info', __FUNCTION__ . ' =>  Pincode is not valid in Excel data: ' .
		print_r($invalid_data, true));

	    $data['error']['invalid_pincode'] = $invalid_data;

	    // Add Only user
	    $this->add_user_for_invalid($invalid_data);
	}
        
        log_message('info', __FUNCTION__ . "=> Exiting validation routine: Under Control");
                    
        return $data;
    }

    /**
     * @desc: This is used to validate delivery date for both type of files.
     * if delivery file is uploaded then it unset future date and
     * if count is greater than 5, it exit and trigger mail.
     * If shipped file is uploded then return count future and past date
     */
    function validate_delivery_date($data, $file_type, $file_name,$partner_id) {
        log_message('info', __FUNCTION__ . "=> Entering validation routine...");
        $status = array();
	$invalid_data = array();
	foreach ($data['valid_data'] as $key => $value) {
            if(isset($value['fso_delivery_date'])){
                $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($value['fso_delivery_date']);
                
            } else if(isset($value['delivery_date'])){
                $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($value['delivery_date']);
            } else {
                $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject();
            }

	    if (count($invalid_data) > 4) {
            	$status['invalid_date'] = $invalid_data;

		// Add Only user
		$this->add_user_for_invalid($invalid_data);
		$this->get_invalid_data($status, $file_type, $file_name,$partner_id);
                log_message('info', __FUNCTION__ . "=> Exiting validation routine: Limit Crossed");
		exit();
	    }
	    if ($file_type == "delivered") {
		if (date('Y-m-d') < $dateObj2->format('Y-m-d')) {
                    //Disabling this check as we always get future dates in delivery file
		    //Future Date
		    //unset($data['valid_data'][$key]);
		    //array_push($invalid_data, $value);
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
//	if ($file_type == "shipped") {
//	    $data['error']['count_past_delivery_date'] = $past_date;
//	    $data['error']['count_future_delivery_date'] = $future_date;
//	}
         log_message('info', __FUNCTION__ . "=> Exiting validation routine: Under Control");            
	return $data;
    }

    /**
     * @desc: This method is used to check order id. Order Id should not be null or order id
     * If order id is null or empty then it unset row and continue.
     * @param array $data
     * @return array
     */
    function validate_order_id($data){
        log_message('info', __FUNCTION__ . "=> Entering validation routine...");
        $invalid_data = array();
        foreach ($data['valid_data'] as $key => $value) {

	    if (is_null($value['sub_order_id']) || $value['sub_order_id'] = "") {

		unset($data['valid_data'][$key]);
                array_push($invalid_data, $value);
	    }
	}

        if(!empty($invalid_data)){
            $data['error']['invalid_order_id'] = $invalid_data;

	    // Add Only user
	    $this->add_user_for_invalid($invalid_data);
	}
        log_message('info', __FUNCTION__ . "=> Exiting validation routine: Under Control");            
        return $data;
    }

    /**
     * @desc: This method checks, Order should not be equal to Phone number
     * @param Array $data
     * @param String $filetype
     * @return Array
     */
    function validate_order_id_same_as_phone($data, $filetype,$file_name,$partner_id) {
        log_message('info', __FUNCTION__ . "=> Entering validation routine...");
	$invalid_data = array();
	foreach ($data['valid_data'] as $key => $value) {
            $phone = explode('/', $value['phone']);
	    if (count($invalid_data) > 4) {


		$status['invalid_same_order_id_phone'] = $invalid_data;
		// Add Only user
		$this->add_user_for_invalid($invalid_data);
		$this->get_invalid_data($status, $filetype, $file_name,$partner_id);
                 log_message('info', __FUNCTION__ . "=> Exiting validation routine: Limit Crossed");
		exit();
	    }
	    if ($value['sub_order_id'] == $phone[0]) {
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
        log_message('info', __FUNCTION__ . "=> Exiting validation routine: Under Control"); 
	return $data;
    }

    /**
     * @desc: This is used to send Json invalid data to mail
     * @param Array $invalid_data_with_reason
     * @param string $filetype
     */
    function get_invalid_data($invalid_data_with_reason, $filetype, $file_name,$partner_id,$file_upload = true) {
        
        if(empty($this->email_send_to)){
            if(empty($this->session->userdata('official_email'))){
                $get_partner_am_id = $this->partner_model->getpartner_details('account_manager_id', array('partners.id' => $partner_id));
                if (!empty($get_partner_am_id[0]['account_manager_id'])) {
                    $file_upload_agent_email = $this->employee_model->getemployeefromid($get_partner_am_id[0]['account_manager_id'])[0]['official_email'];
                }else{
                    $file_upload_agent_email = _247AROUND_SALES_EMAIL;
                }
            }else{
                $file_upload_agent_email = $this->session->userdata('official_email');
            }
            
            $this->email_send_to = $file_upload_agent_email;
        }else{
            $file_upload_agent_email = $this->email_send_to;
        }
        
	$to = NITS_ANUJ_EMAIL_ID.",".$file_upload_agent_email;
        $from = "noreply@247around.com";
	$cc = "";
	$bcc = "";
	$subject = "";
           
	if ($filetype == "delivered") {
	    $subject = "Delivered File is uploaded";
	    $message = " Please check shipped file data:<br/>". " Agent Name ". $this->session->userdata('employee_id');
	} else {
	    $subject = "Shipped File is uploaded";
	    $message = " Please check delivered file data:<br/>". " Agent Name ". $this->session->userdata('employee_id');
	}
        $invalid_data_with_reason['file_name']= $file_name;

        $html = $this->load->view('employee/invalid_data',$invalid_data_with_reason, TRUE);
        // echo $html = $this->load->view('employee/invalid_data',$invalid_data_with_reason);
	$this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $html, "",DELIVERED_FILE_UPLOADED);
        if($file_upload && $partner_id == SNAPDEAL_ID){
            $this->miscelleneous->update_file_uploads($_FILES["file"]["name"],$_FILES["file"]["tmp_name"],$filetype,FILE_UPLOAD_FAILED_STATUS,$this->email_message_id);
        }
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
        if(isset($booking['service_promise_date'])){
            $partner_booking['spd_date']= $booking['service_promise_date'];
        }

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
            '(PNG /LPG) Geyser',
            'Gas Geyser',
            'Set of 2',
            'Drinking Water Pump',
            'Set of 24 pcs',
            'Casseroles',
            'Spun Filter Cartridge',
            'Oil Filled Radiator',
            'Immersion Water Heater Rod',
            '10" Filter Housing Transparent',
            'Blow Hot Element Heater',
            'Bajaj Fan Heater',
            'Gas Geyser',
            'Ro Body Cover',
            'Pack Of 24 Pcs',
            'Mineral Water Pot Offline Non Electric Water Purifer Filter',
            'Membrane Ro Water Purifier',
            '15 Filter',
            'Hevy Duty 5000 Cartridge',
            'Cleanwell Filter',
            'CSM MEMBRANE 80 GPD',
            'Spun Filter pack of ',
            'Zero B Filter',
            'Tower Heater',
            'Oil Filled Heater'
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
            $phone = explode('/', trim($value['phone']));
            $output = $this->user_model->get_users_by_any(array("users.phone_number" => $phone[0]));
            $distict_details = $this->vendor_model->get_distict_details_from_india_pincode(trim(str_replace(" ", "", trim($value['pincode']))));

            if (empty($output)) {
                //User doesn't exist
                if (isset($value['customer_name']) || isset($value['phone']) || isset($value['customer_address']) || isset($value['pincode'])) {
                    $user['user_email'] = (isset($value['email_id']) ? $value['email_id'] : "");
                    $user['name'] = $this->miscelleneous->is_user_name_empty(trim($value['customer_name']), $user['user_email'], $phone[0]);
                    $user['phone_number'] = $phone[0];
                    if(isset($phone[1])){
                        $user['alternate_phone_number'] = $phone[1];
                    }
                    $user['home_address'] = $value['customer_address'];
                    $user['pincode'] = trim(str_replace(" ", "", trim($value['pincode'])));
                    $user['city'] = !empty($value['city'])?$value['city']:$distict_details['district'];;
                    $user['state'] = $distict_details['state'];

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
    
    /**
     * @desc: This function is used to process upload booking file for all partner
     * @param void
     * @param void
     */
    function process_upload_file() {
        log_message("info", __METHOD__ . " File Upload: Beginning processing...");
        
        //check file type
        $upload_file_type = $this->input->post('file_type');
        $redirect_to = $this->input->post('redirect_to');
        $partner_id = $this->input->post('partner_id');
        $this->is_send_file_back = $this->input->post('is_file_send_back');
        $this->file_read_column = $this->input->post('file_read_column');
        $this->file_write_column = $this->input->post('file_write_column');
        $this->revert_file_email = $this->input->post('revert_file_email');
        
        if(!empty($this->input->post('email_send_to'))){
            $this->email_send_to = $this->input->post('email_send_to');
        }
        
        //get email msg id in the case of automatic file upload
        $this->email_message_id = !($this->input->post('email_message_id') === NULL) ? $this->input->post('email_message_id') : '';
        
        //get file extension and file tmp name
        $file_status = $this->get_upload_file_type();
        
        //if file type is valid then validate header for processing
        if ($file_status['status']) {
            //get file header
            $header_data = $this->read_upload_file_header($file_status);
            $this->ColumnFailed = "";
            //check all required header and file type 
            if ($header_data['status']) {
                $header_data = array_merge($header_data,$file_status);
                $header_data['file_type'] = $upload_file_type;
                $response = $this->process_file_upload($header_data);
                
                //if file uploaded successfully then log else send email 
                if ($response['status']) {
                    log_message("info", "File Uploaded successfully");
                    //now send back file with updated booking id to partner
                    if(!empty($this->is_send_file_back) && !empty($this->send_file_back_data) && $this->is_send_file_back !== "null"){
                        $this->revert_file_to_partner($header_data);
                    }else{
                        log_message("info", "unable to send file back to partner");
                    }
                    
                    //save file and upload on s3
                    $this->miscelleneous->update_file_uploads($header_data['file_name'],TMP_FOLDER.$header_data['file_name'], $upload_file_type,FILE_UPLOAD_SUCCESS_STATUS,$this->email_message_id);
                } else {
                    
                    //save file and upload on s3
                    $this->miscelleneous->update_file_uploads($header_data['file_name'], TMP_FOLDER.$header_data['file_name'], $upload_file_type, FILE_UPLOAD_FAILED_STATUS, $this->email_message_id);
                    
                    //get email details 
                    if(empty($this->email_send_to)){
                        if(empty($this->session->userdata('official_email'))){
                            $get_partner_am_id = $this->partner_model->getpartner_details('account_manager_id', array('partners.id' => $partner_id));
                            if (!empty($get_partner_am_id[0]['account_manager_id'])) {
                                $to = $this->employee_model->getemployeefromid($get_partner_am_id[0]['account_manager_id'])[0]['official_email'];
                            }else{
                                $to = _247AROUND_SALES_EMAIL;
                            }
                        }else{
                            $to = $this->session->userdata('official_email');
                        }
                    }else{
                        $to = $this->email_send_to;
                    }
                    
                    $cc = NITS_ANUJ_EMAIL_ID;
                    $agent_name = !empty($this->session->userdata('emp_name')) ? $this->session->userdata('emp_name') : _247AROUND_DEFAULT_AGENT_NAME;
                    $subject = "Failed! $upload_file_type File uploaded by " . $agent_name;
                    $body = $response['msg'];
                    $body .= "<br> <b>File Name</b> ". $header_data['file_name']; 
                    $attachment = TMP_FOLDER.$header_data['file_name'];
                    $this->notify->sendEmail("noreply@247around.com", $to, $cc, "", $subject, $body, $attachment,FAILED_UPLOAD_FILE);

                    log_message('info', __FUNCTION__ . " " . $this->ColumnFailed);
                    $this->session->set_flashdata('file_error', $this->ColumnFailed);
                    redirect(base_url() . "employee/booking_excel/$redirect_to");
                }
                
                $res1 = 0;
                system("chmod 777" . TMP_FOLDER . $header_data['file_name'], $res1);
                unlink(TMP_FOLDER.$header_data['file_name']);
            } else {
                $this->session->set_flashdata('file_error', 'Empty file has been uploaded');
                redirect(base_url() . "employee/booking_excel/$redirect_to");
            }
        } else {
            $this->session->set_flashdata('file_error', 'Empty file has been uploaded');
            redirect(base_url() . "employee/booking_excel/$redirect_to");
        }
    }
    
    
    /**
     * @desc: This function is used to get the file type
     * @param void
     * @param $response array
     */
    private function get_upload_file_type(){
        if (!empty($_FILES['file']['name']) && $_FILES['file']['size'] > 0) {
            $pathinfo = pathinfo($_FILES["file"]["name"]);

            switch ($pathinfo['extension']) {
                case 'xlsx':
                    $response['file_tmp_name'] = $_FILES['file']['tmp_name'];
                    $response['file_ext'] = 'Excel2007';
                    break;
                case 'xls':
                    $response['file_tmp_name'] = $_FILES['file']['tmp_name'];
                    $response['file_ext'] = 'Excel5';
                    break;
            }
            
            $response['status'] =  True;
        } else {
            log_message('info', __FUNCTION__ . ' Empty File Uploaded');
            $response['status'] =  False;
        }
        
        return $response;
        
    }
    
    /**
     * @desc: This function is used to get the file header
     * @param $file array
     * @param $response array
     */
    private function read_upload_file_header($file){
        
        try {
            $objReader = PHPExcel_IOFactory::createReader($file['file_ext']);
            $objPHPExcel = $objReader->load($file['file_tmp_name']);
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($file['file_tmp_name'], PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }

        $file_name = $_FILES["file"]["name"];
        move_uploaded_file($file['file_tmp_name'],TMP_FOLDER.$file_name);
        $res1 = 0;
        system("chmod 777" . TMP_FOLDER . $file_name, $res1);

        //  Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestDataRow();
        $highestColumn = $sheet->getHighestDataColumn();
        $response['status'] =  TRUE;
        //Validation for Empty File
        if ($highestRow <= 1) {
            log_message('info', __FUNCTION__ . ' Empty File Uploaded');
            $response['status'] =  False;
        }

        $headings = $sheet->rangeToArray('A1:' . $highestColumn . 1, NULL, TRUE, FALSE);
        $headings_new = array();
        foreach ($headings as $heading) {
            $heading = str_replace(array("/", "(", ")", "."), "", $heading);
            array_push($headings_new, str_replace(array(" "), "_", $heading));
        }
        
        $headings_new1 = array_map('strtolower', $headings_new[0]);
        
        $response['file_name'] =  $file_name;
        $response['header_data'] =  $headings_new1;
        $response['sheet'] =  $sheet;
        $response['highest_row'] =  $highestRow;
        $response['highest_column'] =  $highestColumn;
        
        return $response;
    }
    
    /**
     * @desc: This function is used to get the sub array from the array
     * @param $parentArray array
     * @param $subsetArrayToGet array
     * @param array
     */
    function get_sub_array(array $parentArray, array $subsetArrayToGet)
    {
        return array_intersect_key($parentArray, array_flip($subsetArrayToGet));
    }
    
    /**
     * @desc: This function is used to make data for uploaded file
     * @param $data array
     * @param void 
     */
    function get_final_file_data($data,$header_data){
        $tmpArr['unique_id'] = 'Around';
        $tmpArr['referred_date_and_time'] = '';
        $tmpArr['sub_order_id'] = $data[$header_data['sub_order_id']];
        $tmpArr['product_type'] = $data[$header_data['product_type']];
        $tmpArr['customer_name'] = $data[$header_data['customer_name']];
        $tmpArr['customer_address'] = $data[$header_data['customer_address']];
        $tmpArr['pincode'] = $data[$header_data['pincode']];
        
        if(isset($data[$header_data['brand']]) && !empty($data[$header_data['brand']])){
            $tmpArr['brand'] = $data[$header_data['brand']];
        }else{
            $tmpArr['brand'] = '';
        }
        
        if(isset($data[$header_data['model']]) && !empty($data[$header_data['model']])){
            $tmpArr['model'] = $data[$header_data['model']];
        }else{
            $tmpArr['model'] = '';
        }
        
        if(isset($data[$header_data['product']]) && !empty($data[$header_data['product']])){
            $tmpArr['product'] = $data[$header_data['product']];
        }else{
            $tmpArr['product'] = $tmpArr['product_type'];
        }
        
        if(isset($data[$header_data['city']]) && !empty($data[$header_data['city']])){
            $tmpArr['city'] = $data[$header_data['city']];
        }else{
            $tmpArr['city'] = '';
        }
        $tmpArr['phone'] = $data[$header_data['phone']];
        if(isset($data[$header_data['alternate_phone']]) && !empty($data[trim($header_data['alternate_phone'])])){
            $tmpArr['phone'] = $data[$header_data['phone']]."/".$data[$header_data['alternate_phone']];
        }else{
            $tmpArr['phone'] = $data[$header_data['phone']];
        }
        if(isset($data[$header_data['email_id']]) && !empty($data[$header_data['email_id']])){
            $tmpArr['email_id'] = $data[$header_data['email_id']];
        }else{
            $tmpArr['email_id'] = '';
        }
        
        if(isset($data['item_id'])){
            $tmpArr['order_item_id'] = $data['item_id'];
        }else if(isset($data['order_item_id'])){
            $tmpArr['order_item_id'] = $data['order_item_id'];
        }
        
        $tmpArr['call_type_installation_table_top_installationdemo_service'] = '';
        $tmpArr['partner_source'] = $data['partner_source'];
        
        
        if(isset($data[$header_data['spd']]) && !empty($data[trim($header_data['spd'])])){
            $tmpArr['service_promise_date'] = $data['promise_before_date'];
        }else{
            $tmpArr['service_promise_date'] = '';
        }
        
        array_push($this->finalArray, $tmpArr);
    }
    
    /**
     * @desc: This function is used to validate upload file header
     * @param $data array
     * @param $return_response array
     */
    function check_column_exist($data){
        foreach($data['actual_header_data'] as $key => $value){
            //check all header in the file are as per our database
            $subArray = $this->get_sub_array($value,array('sub_order_id','product','product_type','customer_name','customer_address','pincode','phone'));
            $is_all_header_present = array_diff(array_values($subArray),$data['header_data']);
            if(empty($is_all_header_present)){
                $return_response['status'] = TRUE;
                $return_response['msg'] = '';
                $return_response['key'] = $key;
                break;
            }else{
                $this->Columfailed = "<b>".implode($is_all_header_present, ',')." </b> column does not exist.Please correct these and upload again. <br><br><b> For reference,Please use previous successfully upload file from CRM</b>";
                $return_response['status'] = FALSE;
                $return_response['msg'] = $this->Columfailed;
            }
        }
        return $return_response;
    }
    
    function process_file_upload($data){
        log_message('info', __FUNCTION__ . "=> File Upload: Beginning processing...");
        $select = '*';
        $data['actual_header_data'] = $this->reusable_model->get_search_query('partner_file_upload_header_mapping',$select,array('partner_id'=>$this->input->post('partner_id')),NULL,NULL,NULL,NULL,NULL)->result_array();
        $response = $this->check_column_exist($data);
        if ($response['status']) {          
            for ($row = 2, $i = 0; $row <= $data['highest_row']; $row++, $i++) {
                $rowData_array = $data['sheet']->rangeToArray('A' . $row . ':' . $data['highest_column'] . $row, NULL, TRUE, FALSE);
                $rowData = array_combine($data['header_data'], $rowData_array[0]);
                $rowData['partner_source'] = $this->input->post('partner_source');
                $rowData['partner_id'] = $this->input->post('partner_id');;
                $this->get_final_file_data($rowData,$data['actual_header_data'][$response['key']]);
            }
            
            if(!empty($this->finalArray)){
                //process file to insert bookings
                $this->process_upload_sd_file($this->finalArray,'delivered', $data['file_name'],$this->input->post('partner_id'));
            }
            
            $response['status'] = TRUE;
        }
        
        return $response;
    }
    
    function upload_partner_booking_file(){
        $this->miscelleneous->load_nav_header();
	$this->load->view('employee/upload_partner_booking_file');
    }
    
    
    /**
     * @desc: This function is used to write the booking id on uploaded file
     * @params: $data array
     * @return: boolean response
     */
    function revert_file_to_partner($data) {
        log_message("info", __METHOD__ . " Generating File");
        //start adding new cell value on actual price sheet
        if(file_exists(TMP_FOLDER.$data['file_name'])) {
            if (pathinfo(TMP_FOLDER . $data['file_name'])['extension'] == 'xlsx') {
                $inputFileName1 = TMP_FOLDER . $data['file_name'];
                $inputFileExtn1 = 'Excel2007';
            } else {
                $inputFileName1 = TMP_FOLDER . $data['file_name'];
                $inputFileExtn1 = 'Excel5';
            }
            $objReader1 = PHPExcel_IOFactory::createReader($inputFileExtn1);
            $objPHPExcel1 = $objReader1->load($inputFileName1);

            //get first sheet 
            $sheet = $objPHPExcel1->getSheet(0);
            //get total number of rows
            $highestRow = $sheet->getHighestDataRow();

            for ($row = 2, $i = 0; $row <= $highestRow; $row++, $i++) {
                $order_id = $sheet->getCell($this->file_read_column . $row)->getValue();
                $key = array_search($order_id, array_column($this->send_file_back_data, 'order_id'));
                if ($key !== FALSE) {
                    $sheet->setCellValue($this->file_write_column . $row, $this->send_file_back_data[$key]['booking_id']);
                }
            }

            // Write the file
            $file_name = TMP_FOLDER . "Updated_file_with_booking_id_" .pathinfo($data['file_name'], PATHINFO_FILENAME). ".xls";
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel1, 'Excel5');
            $objWriter->save($file_name);

            if (file_exists($file_name)) {
                //send mail 
                $template = $this->booking_model->get_booking_email_template("revert_upload_file_to_partner");
                $body = $template[0];
                $to = $this->revert_file_email;
                $from = $template[2];
                $cc = $template[3] . "," . $this->email_send_to;
                $subject = $template[4];
                $attachment = $file_name;
                $sendmail = $this->notify->sendEmail($from, $to, $cc, "", $subject, $body, $attachment,'revert_upload_file_to_partner');
                if ($sendmail) {
                    $response = TRUE;
                    log_message("info", "file send to partner with updated booking id's");
                } else {
                    $response = FALSE;
                    log_message("info", "Error in sending file back to partner with updated booking id's");
                }
                $res1 = 0;
                system("chmod 777" . $file_name, $res1);
                unlink($file_name);
            } else {
                $response = FALSE;
                $agent_name = !empty($this->session->userdata('emp_name')) ? $this->session->userdata('emp_name') : _247AROUND_DEFAULT_AGENT_NAME;
                $subject = "Booking Id didn't update for upload File " . $data['file_name'];
                $body = "Error In writng booking Id to upload file " . $data['file_name'];
                $body .= "Agent Name: " . $agent_name;
                $sendmail = $this->notify->sendEmail(NOREPLY_EMAIL_ID, DEVELOPER_EMAIL, "", "", $subject, $body, "",BOOKING_ID_NOT_UPDATED_FOR_UPLOADED_FILE);
            }
            
        } else {
            log_message("info", "file not found to revert back");
            $response = FALSE;
        }

        return $response;
    }

}