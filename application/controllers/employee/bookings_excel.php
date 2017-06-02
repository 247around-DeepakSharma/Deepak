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
        $this->load->library('booking_utilities');

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
	$this->load->view('employee/header/'.$this->session->userdata('user_group'));
	$this->load->view('employee/upload_bookings_excel');
    }

    /*
     * @desc: This function is to upload the products that have been shipped from our partners
     * @param: void
     * @return: void
     */

    public function upload_shipped_products_excel() {
	$this->load->view('employee/header/'.$this->session->userdata('user_group'));
	$this->load->view('employee/upload_shippings_excel');
    }

    /*
     * @desc: This function is to upload the products that have been delevered from our partners
     * @param: void
     * @return: void
     */

    public function upload_delivered_products_for_paytm_excel() {
	$this->load->view('employee/header/'.$this->session->userdata('user_group'));
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
        
        //Updating File Uploads table and upload file to s3
        $this->_update_paytm_file_uploads($_FILES["file"]["tmp_name"]);

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
            
            //Sanitizing Brand Name
            if(!empty($rowData[0]['Brand'])){
                $rowData[0]['Brand'] = preg_replace('/[^A-Za-z0-9 ]/', '', $rowData[0]['Brand']);
            }

	    //Insert user if phone number doesn't exist
	    $output = $this->user_model->search_user(trim($rowData[0]['CustomerContactNo']));
            $distict_details = $this->vendor_model->get_distict_details_from_india_pincode(trim($rowData[0]['CustomerPincode']));

	    if (empty($output)) {
		//User doesn't exist
                $user_name = $this->is_user_name_empty($rowData[0]['CustomerName'],$rowData[0]['customer_email'],$rowData[0]['CustomerContactNo']);
		$user['name'] = $user_name;
		$user['phone_number'] = $rowData[0]['CustomerContactNo'];
		$user['user_email'] = $rowData[0]['customer_email'];
		$user['home_address'] = $rowData[0]['CustomerAddress1'] . " ," . $rowData[0]['CustomerAddress2'];
		$user['pincode'] = $rowData[0]['CustomerPincode'];
		$user['city'] = $rowData[0]['CustomerCity'];
		$user['state'] = $distict_details['state'];
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
            $partner_booking = $this->partner_model->get_order_id_for_partner("3", $rowData[0]['OrderID']);
	    if (is_null($partner_booking)) {
		$booking['order_id'] = $rowData[0]['OrderID'];

		//$lead_details['Unique_id'] = $rowData[0]['Item ID'];
		//$dateObj1 = PHPExcel_Shared_Date::ExcelToPHPObject($rowData[0]['Order Date']);
		//$lead_details['DeliveryDate'] = $dateObj1->format('d/m/Y');
		$appliance_details['brand'] = $unit_details['appliance_brand'] = $rowData[0]['Brand'];
		$appliance_details['model_number'] = $unit_details['model_number'] = "";

		$prod = trim($rowData[0]['ProductCategoryL3']);
                
                //check if service_id already exist or not by using product description
                $service_appliance_data = $this->booking_model->get_service_id_by_appliance_details(trim($rowData[0]['ProductName']));

                if(!empty($service_appliance_data)){
                    log_message('info', __FUNCTION__ . "=> Appliance Dsecription found in table");
                    $lead_details['service_id'] = $service_appliance_data[0]['service_id'];
                    $lead_details['service_appliance_data'] = $service_appliance_data[0];
                    $lead_details['Product'] = $service_appliance_data[0]['services'];
                }
                else{
                    log_message('info', __FUNCTION__ . "=> Appliance Dsecription does not found in table");
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
                }

		

		$appliance_details['description'] = $unit_details['appliance_description'] = trim($rowData[0]['ProductName']);

		$booking['booking_address'] = $rowData[0]['CustomerAddress1'] . " ," . $rowData[0]['CustomerAddress2'];
		$booking['booking_pincode'] = $rowData[0]['CustomerPincode'];
		$booking['city'] = $rowData[0]['CustomerCity'];
                $booking['state'] = $distict_details['state'];
                $booking['district'] = $distict_details['district'];
                $booking['taluk'] = $distict_details['taluk'];

		

		$booking['booking_primary_contact_no'] = $rowData[0]['CustomerContactNo'];

		$dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($rowData[0]['shippedDate']);
		$booking['shipped_date'] = date('Y-m-d H:i:s', strtotime($dateObj2->format('d-m-Y')));

		$booking['current_status'] = "FollowUp";
		$booking['internal_status'] = "Missed_call_not_confirmed";
                $booking['create_date'] = date("Y-m-d H:i:s");

		//Add this as a Query now
		$booking['booking_id'] = '';
		$appliance_details['user_id'] = $booking['user_id'] = $user_id;
		$appliance_details['service_id'] = $unit_details['service_id'] = $booking['service_id'] = isset($lead_details['service_id'])?$lead_details['service_id']:$this->booking_model->getServiceId($lead_details['Product']);
		log_message('info', __FUNCTION__ . "=> Service ID: " . $booking['service_id']);

		$booking['booking_alternate_contact_no'] = '';

		$yy = date("y");
		$mm = date("m");
		$dd = date("d");
		$booking['booking_id'] = str_pad($booking['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
		$booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($booking['user_id'])) + 1);

		//Assigning Booking Source and Partner ID for Brand Requested
                // First we send Service id and Brand and get Partner_id from it
                // Now we send state, partner_id and service_id 
                $data = $this->_allot_source_partner_id_for_pincode($booking['service_id'], $booking['state'], $appliance_details['brand'], "SP");

                $booking['partner_id'] = $data['partner_id'];
                $booking['source'] = $data['source'];

                $unit_details['booking_id'] = $booking['booking_id'] = "Q-" . $booking['source'] . "-" . $booking['booking_id'];

		$unit_details['partner_id'] = $booking['partner_id'];
		$booking['quantity'] = '1';
		$appliance_details['category'] = $unit_details['appliance_category'] = isset( $lead_details['service_appliance_data']['category'])? $lead_details['service_appliance_data']['category']:'';
		$appliance_details['capacity'] = $unit_details['appliance_capacity'] = isset( $lead_details['service_appliance_data']['capacity'])? $lead_details['service_appliance_data']['capacity']:'';
		$appliance_details['tag'] = $unit_details['appliance_brand'] . " " . $unit_details['appliance_description'];
		$appliance_details['purchase_month'] = $unit_details['purchase_month'] = date('m');
		$appliance_details['purchase_year'] = $unit_details['purchase_year'] = date('Y');
		$booking['partner_source'] = "Paytm-delivered-excel";
                
                //get partner data to check the price
                $partner_data = $this->partner_model->get_partner_code($booking['partner_id']);
                $partner_mapping_id = $booking['partner_id'];
                if($partner_data[0]['partner_type'] == OEM){
                    //if partner type is OEM then sent appliance brand in argument
                    $prices = $this->partner_model->getPrices($booking['service_id'], $unit_details['appliance_category'], $unit_details['appliance_capacity'], $partner_mapping_id,'Installation & Demo', $unit_details['appliance_brand']);
                } else {
                    //if partner type is not OEM then dose not sent appliance brand in argument
                    $prices = $this->partner_model->getPrices($booking['service_id'], $unit_details['appliance_category'], $unit_details['appliance_capacity'], $partner_mapping_id,'Installation & Demo',"");
                }
                $booking['amount_due'] = '0';
                $is_price = array();
                $flag = array();
                if(!empty($prices)){
                    $unit_details['id'] =  $prices[0]['id'];
                    $unit_details['price_tags'] =  "Installation & Demo";
                    $unit_details['around_paid_basic_charges'] =  $unit_details['around_net_payable'] = "0.00";
                    $unit_details['partner_paid_basic_charges'] = $prices[0]['partner_net_payable'];
                    $unit_details['partner_net_payable'] = $prices[0]['partner_net_payable'];
                    $booking['amount_due'] = $prices[0]['customer_net_payable'];
                    $is_price['customer_net_payable'] = $prices[0]['customer_net_payable'];
                    $is_price['is_upcountry'] = $prices[0]['is_upcountry'];
                    $flag = array('1');
                }


		$booking['potential_value'] = '';
		$appliance_details['last_service_date'] = date('d-m-Y');


		$booking['type'] = "Query";
                
		$booking['booking_date'] = date('d-m-Y', strtotime("+3 days",strtotime($booking['shipped_date'])));
		$booking['booking_timeslot'] = '';
		$booking['amount_due'] = '';
		$booking['booking_remarks'] = 'Installation & Demo';
		$booking['query_remarks'] = 'Installation & Demo';
		$booking['request_type'] = 'Installation & Demo';

		//Insert query
		//echo print_r($booking, true) . "<br><br>";
                //check partner status from partner_booking_status_mapping table  
                $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'], $booking['partner_id'], $booking['booking_id']);
                if (!empty($partner_status)) {
                    $booking['partner_current_status'] = $partner_status[0];
                    $booking['partner_internal_status'] = $partner_status[1];
                }
                
                $vendors = $this->vendor_model->check_vendor_availability($booking['booking_pincode'], $booking['service_id']);
                $vendors_count = count($vendors);

                if ($vendors_count > 0) {
                    $this->send_sms_to_customer($lead_details['Product'],
                            $booking['booking_primary_contact_no'], $user_id,
                            $booking['booking_id'], $unit_details['appliance_category'],$booking['amount_due'], $flag);
                } else { //if ($vendors_count > 0) {
                    //update booking
                    $booking['internal_status'] = SF_UNAVAILABLE_SMS_NOT_SENT;

                    log_message('info', __FUNCTION__ . ' =>  SMS not sent because of Vendor Unavailability for Booking ID: ' . $booking['booking_id']);
                }
                
		$return_id = $this->booking_model->addbooking($booking);
		
                if (!$return_id) {
		    log_message('info', __FUNCTION__ . 'Error Paytm booking details not inserted: ' . print_r($booking, true));
                } else {
                    $unit_details['appliance_id'] = $this->booking_model->addappliance($appliance_details);
                    if (!empty($prices)) {
                        $unit_id = $this->booking_model->insert_data_in_booking_unit_details($unit_details, $booking['state'], 0);
                    } else {
                        $unit_id = $this->booking_model->addunitdetails($unit_details);
                    }
                    $this->insert_booking_in_partner_leads($booking, $unit_details,$user, $lead_details['Product'] );
                    $this->notify->insert_state_change($booking['booking_id'], _247AROUND_FOLLOWUP , _247AROUND_NEW_QUERY ,'', $this->session->userdata('id'), $this->session->userdata('employee_id'),_247AROUND);
                    //Reset
                    if (empty($booking['state'])) {
                        $to = NITS_ANUJ_EMAIL_ID;
                        $message = "Pincode " . $booking['booking_pincode'] . " not found for Booking ID: " . $booking['booking_id'];
                        $this->notify->sendEmail("booking@247around.com", $to, "", "", 'Pincode Not Found', $message, "");
                    }
                }

                
		unset($booking);
                
                
                
		unset($unit_details);
		unset($appliance_details);
	    }
	}

	redirect(base_url() . DEFAULT_SEARCH_PAGE);
    }
    
    function send_sms_to_customer($appliance, $phone_number, $user_id, $booking_id, $category, $amount_due, $flag) {
        $sms['tag'] = "paytm_shipped_missed_call_initial";

        //ordering of smsData is important, it should be as per the %s in the SMS
        $sms['smsData']['service'] = $appliance;
        $sms['smsData']['missed_call_number'] = SNAPDEAL_MISSED_CALLED_NUMBER;
        if(!empty($flag)){
            if($amount_due == 0){
                $sms['smsData']['message'] = "FREE";
            
            } else {
                $sms['smsData']['message'] = "Rs. ".  round($amount_due,0);
            }
            
        } else {
            $sms['smsData']['message'] = $this->notify->get_product_free_not($appliance, $category);
        }

	$sms['phone_no'] = $phone_number;
	$sms['booking_id'] = $booking_id;
	$sms['type'] = "user";
	$sms['type_id'] = $user_id;

	$this->notify->send_sms_msg91($sms);
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
    	$partner_booking['Address'] = $booking['booking_address'];
    	$partner_booking['Pincode'] = $booking['booking_pincode'];
    	$partner_booking['City'] = $booking['city'];
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
    
    /**
     * @Desc: This function is used to _allot_source_partner_id_for_pincode
     * @params: String Pincode, brnad, default partner id(SS)
     * @return : Array
     * 
     */
    private function _allot_source_partner_id_for_pincode($service_id, $state, $brand, $default_source) {
        log_message('info', __FUNCTION__ . ' ' . $service_id, $state, $brand);
        $data = [];

        $partner_array = $this->partner_model->get_active_partner_id_by_service_id_brand($brand, $service_id);
        
        if (!empty($partner_array)) {

            foreach ($partner_array as $value) {
                //Now getting details for each Partner 
                $filtered_partner_state = $this->partner_model->check_activated_partner_for_state_service($state, $value['partner_id'], $service_id);
                if ($filtered_partner_state) {
                    //Now assigning this case to Partner
                    $data['partner_id'] = $value['partner_id'];
                    $data['source'] = $this->partner_model->get_source_code_for_partner($value['partner_id']);
                } else {
                    if($default_source == "SS"){
                       //Now assigning this case to SS
                       $data['partner_id'] = SNAPDEAL_ID;
                       $data['source'] = $default_source;
                    } else if($default_source == "SP"){
                       $data['partner_id'] = PAYTM;
                       $data['source'] = $default_source;
                    }
                }
            }
        } else {
            log_message('info', ' No Active Partner has been Found in for Brand ' . $brand . ' and service_id ' . $service_id);
            //Now assigning this case to SP
            $data['partner_id'] = PAYTM;
            $data['source'] = 'SP';
        }
        
        return $data;
    }
    
    /**
     * @Desc: This function is used to upload Paytm file to s3 and update file uploads table
     * @params: String
     * @return: void
     * 
     */
    private function _update_paytm_file_uploads($tmpFile){
        //Logging
        log_message('info', __FUNCTION__ . ' Processing of Paytm Delivered Product Excel File started');

        //Adding Details in File_Uploads table as well

        $data['file_name'] = "Paytm-Delivered-" . date('Y-m-d-H-i-s') . '.xlsx';
        $data['file_type'] = _247AROUND_PAYTM_DELIVERED;
        $data['agent_id'] = $this->session->userdata('id');
        $insert_id = $this->partner_model->add_file_upload_details($data);
        if (!empty($insert_id)) {
            //Logging success
            log_message('info', __FUNCTION__ . ' Added details to File Uploads ' . print_r($data, TRUE));
        } else {
            //Loggin Error
            log_message('info', __FUNCTION__ . ' Error in adding details to File Uploads ' . print_r($data, TRUE));
        }

        //Making process for file upload
        $delivered_file = $data['file_name'];
        move_uploaded_file($tmpFile, TMP_FOLDER . $delivered_file);

        //Upload files to AWS
        $bucket = BITBUCKET_DIRECTORY;
        $directory_xls = "vendor-partner-docs/" . $delivered_file;
        $this->s3->putObjectFile(TMP_FOLDER . $delivered_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
        //Logging
        log_message('info', __FUNCTION__ . ' Paytm Delivered File has been uploaded in S3');
    }
    
    /**
     * @Desc: This function is used to check if user name is empty or not
     * if user name is not empty then return username otherwise check if email is not
     * empty.if email is empty then return mobile number as username otherwise return email as username 
     * @params: String
     * @return: void
     * 
     */
    private function is_user_name_empty($userName , $userEmail,$userContactNo){
        if(empty($userName)){
            if(empty($userEmail)){
                $user_name = $userContactNo;
            }else{
                $user_name = $userEmail;
            }
        }else{
            $user_name = $userName;
        }
        
        return $user_name;
    }

}
