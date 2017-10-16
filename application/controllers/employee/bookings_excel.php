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

    var $Columfailed = "";
    var $total_bookings_came = 0;
    var $total_bookings_inserted = 0;
    var $empty_contact_number = array();
    var $incorrect_pincode = array();
    
    
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
        $this->load->model('upcountry_model');
        $this->load->library('asynchronous_lib');
        $this->load->library('initialized_variable');
        $this->load->library("miscelleneous");


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
        $error = false;

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

                $error = TRUE;
                $msg = "File format is not correct. Only XLS or XLSX files are allowed.";
            }
        }

        if (!$error) {
            
            //Processing File
            $response['data'] = $this->process_upload_file($inputFileName, $inputFileExtn);
            if(!empty($response['data'])){
                $response['upload_file_name'] = $_FILES["file"]["name"];
                $html = $this->load->view('employee/email_paytm_upload_file_details',$response,TRUE);
                $to = $this->session->userdata('official_email');
                $cc = NITS_EMAIL_ID.",".DEVELOPER_EMAIL;
                $subject = "Paytm File is uploaded by " . $this->session->userdata('employee_id');
                $this->notify->sendEmail("booking@247around.com", $to, $cc, "", $subject, $html, "");
                log_message('info', 'paytm file uploaded successfully.' . print_r($response,true));
                
                //Updating File Uploads table and upload file to s3
                $this->miscelleneous->update_file_uploads($_FILES["file"]["tmp_name"],_247AROUND_PAYTM_DELIVERED,FILE_UPLOAD_SUCCESS_STATUS);
            }else{
                log_message('info', "empty");
                //Updating File Uploads table and upload file to s3
                $this->miscelleneous->update_file_uploads($_FILES["file"]["tmp_name"],_247AROUND_PAYTM_DELIVERED,FILE_UPLOAD_FAILED_STATUS);
            }
        } else {
            echo $msg;
            log_message('info', $msg);
        }
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
    
    function check_column_exist($rowData){
        
        $error = false;
        
        if (!array_key_exists('order_id', $rowData)) {
            $this->Columfailed .= " Order Id Column does not exist. Please use <b>order_id</b> as column name.<br/><br/>";
            $error = true;
        }

        if (!array_key_exists('product_name', $rowData)) {
            $this->Columfailed .= " Product Name Column does not exist. Please use <b>product_name</b> as column name.<br/><br/>";
            $error = true;
        }
        
        if (!array_key_exists('category', $rowData)) {
      
            $this->Columfailed .= " Category Column does not exist. Please use <b>category</b> as column name.<br/><br/>";
            $error = true;
        }
         
        if (!array_key_exists('brand', $rowData)) {
       
            $this->Columfailed .= " Brand Column does not exist. Please use <b>brand</b> as column name.<br/><br/>";
            $error = true;
        }
        if (!array_key_exists('customer_firstname', $rowData)) {

            $this->Columfailed .= " Customer First Name Column does not exist. Please use <b>customer_firstname</b> as column name.<br/><br/>";
            $error = true;
        }
        if (!array_key_exists('customer_lastname', $rowData)) {
      
            $this->Columfailed .= " Customer Last Name does not exist. Please use <b>customer_lastname</b> as column name.<br/><br/>";
            $error = true;
        }
        if (!array_key_exists('contact_number', $rowData)) {
      
            $this->Columfailed .= " Contact Number Column does not exist. Please use <b>contact_number</b> as column name.<br/><br/>";
            $error = true;
        }
        if (!array_key_exists('address', $rowData)) {
      
            $this->Columfailed .= " Address Column does not exist. Please use <b>address</b> as column name.<br/><br/>";
            $error = true;
        }
        if (!array_key_exists('pincode', $rowData)) {
      
            $this->Columfailed .= " Pincode Column does not exist. Please use <b>pincode</b> as column name.<br/><br/>";
            $error = true;
        }
        if (!array_key_exists('customer_city', $rowData)) {
      
            $this->Columfailed .= " Customer City Column does not exist. Please use <b>customer_city</b> as column name.<br/><br/>";
            $error = true;
        }
        if (!array_key_exists('shipped_date', $rowData)) {
      
            $this->Columfailed .= " Shipped Date Column does not exist. Please use <b>shipped_date</b> as column name.<br/><br/>";
            $error = true;
        }
         
        if ($error) {
            return false;
        } else {
            return true;
        }
    }
    
    /*
     * @desc: This function is to upload the Satya File
     * @param: void
     * @return: void
     */

    public function upload_satya_file() {
        
	$this->load->view('employee/header/'.$this->session->userdata('user_group'));
	$this->load->view('employee/upload_satya_file');
    }
    
    public function process_upload_file($inputFileName, $inputFileExtn) {
        try {
            $insert_data_details = array();
            $objReader = PHPExcel_IOFactory::createReader($inputFileExtn);
            $objPHPExcel = $objReader->load($inputFileName);
            //read all sheet data
            foreach ($objPHPExcel->getAllSheets() as $sheet) {
                $highestRow = $sheet->getHighestDataRow();
                $highestColumn = $sheet->getHighestDataColumn();
                $headings = $sheet->rangeToArray('A1:' . $highestColumn . 1, NULL, TRUE, FALSE, FALSE);
                $headings_new = array();
                
                foreach ($headings as $heading) {
                    $heading = str_replace(array("/", "(", ")", " ", "."), "", $heading);
                    array_push($headings_new, array_map('strtolower', str_replace(array(" "), "_", $heading)));
                }

                $return_data = $this->do_action_on_file_data($sheet, $highestRow, $highestColumn, $headings_new);
                
                if($return_data){
                    $to = $this->session->userdata('official_email');
                    $cc = NITS_EMAIL_ID.",".DEVELOPER_EMAIL;
                    $subject = "PAYTM FILE UPLOAD FAILED!!!";
                    $message = "File Uploaded By ". $this->session->userdata('full_name');
                    $message .= "Sheet Name = <b>".$sheet->getTitle()."</b> <br><br>";
                    $message .= $this->Columfailed;
                    $message .= "Please Check File And Upload Again";
                    $this->notify->sendEmail('booking@247around.com',$to,$cc,"",$subject, $message,"");
                    $insert_data_details = array();
                    break;
                }else{
                    $data = array();
                    $data['sheet_name'] = $sheet->getTitle();
                    $data['total_bookings_came'] = $this->total_bookings_came;
                    $data['total_bookings_inserted'] = $this->total_bookings_inserted;
                    $data['contact_number_empty'] = $this->empty_contact_number;
                    $data['incorrct_pincode'] = $this->incorrect_pincode;
                    array_push($insert_data_details, $data);
                }
                
                $this->total_bookings_came = 0;
                $this->total_bookings_inserted = 0;
                $this->empty_contact_number = array();
                $this->incorrect_pincode = array();
            }
            
            return $insert_data_details;
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }
    }

    function do_action_on_file_data($sheet, $highestRow, $highestColumn, $headings_new) {
        $error = FALSE;
        for ($row = 2, $i = 0; $row <= $highestRow; $row++, $i++) {
            $this->total_bookings_came++;
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowData[0] = array_combine($headings_new[0], $rowData[0]);

            $this->Columfailed = "";
            $status = $this->check_column_exist($rowData[0]);

            if ($status) {
                if (empty($rowData[0]['contact_number'])) {
                    $error = true;
                    $empty_contact = true;
                    array_push($this->empty_contact_number, $rowData[0]);
                } else {
                    $error = false;
                    $empty_contact = false;
                }

                if (empty($rowData[0]['pincode'])) {
                    $error = true;
                    $pincode_empty = true;
                    array_push($this->incorrect_pincode, $rowData[0]);
                } else {
                    $error = false;
                    $pincode_empty = false;
                }

                if ($empty_contact === false && $pincode_empty === false) {
                    //Sanitizing Brand Name
                    if (!empty($rowData[0]['brand'])) {
                        $rowData[0]['brand'] = preg_replace('/[^A-Za-z0-9 ]/', '', $rowData[0]['brand']);
                    }

                    //Insert user if phone number doesn't exist
                    $output = $this->user_model->search_user(trim($rowData[0]['contact_number']));
                    $distict_details = $this->vendor_model->get_distict_details_from_india_pincode(trim($rowData[0]['pincode']));

                    if (empty($output)) {
                        //User doesn't exist
                        $user_name = $this->is_user_name_empty(trim($rowData[0]['customer_firstname'] . " " . $rowData[0]['customer_lastname']), $rowData[0]['customer_email'], $rowData[0]['contact_number']);
                        $user['name'] = $user_name;
                        $user['phone_number'] = $rowData[0]['contact_number'];
                        $user['user_email'] = $rowData[0]['customer_email'];
                        $user['home_address'] = $rowData[0]['address'];
                        $user['pincode'] = $rowData[0]['pincode'];
                        $user['city'] = $rowData[0]['customer_city'];
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
                        $user['name'] = trim($rowData[0]['customer_firstname'] . " " . $rowData[0]['customer_lastname']);
                        $user['user_email'] = $rowData[0]['customer_email'];
                        $user_id = $output[0]['user_id'];
                    }

                    $prod = trim($rowData[0]['category']);
                    $lead_details = array();

                    //check if service_id already exist or not by using product description
                    $service_appliance_data = $this->booking_model->get_service_id_by_appliance_details(trim($rowData[0]['product_name']));

                    if (!empty($service_appliance_data)) {
                        log_message('info', __FUNCTION__ . "=> Appliance Dsecription found in table");

                        $lead_details['service_id'] = $service_appliance_data[0]['service_id'];
                        $lead_details['service_appliance_data'] = $service_appliance_data[0];
                        $lead_details['Product'] = $service_appliance_data[0]['services'];
                    } else {
                        log_message('info', __FUNCTION__ . "=> Appliance Dsecription does not found in table");
                        if (stristr($prod, "Washing Machine") || stristr($prod, "WashingMachine") || stristr($prod, "Dryer")) {
                            $lead_details['Product'] = 'Washing Machine';
                        }
                        if (stristr($prod, "Television") || stristr($prod, "TV") || stristr($prod, "Tv")) {
                            $lead_details['Product'] = 'Television';
                        }
                        if (stristr($prod, "Airconditioner") || stristr($prod, "Air Conditioner") || strstr($prod, "AC")) {
                            $lead_details['Product'] = 'Air Conditioner';
                        }
                        if (stristr($prod, "Refrigerator")) {
                            $lead_details['Product'] = 'Refrigerator';
                        }
                        if (stristr($prod, "Microwave") || stristr($prod, "Oven")) {
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


                    $service_id = isset($lead_details['service_id']) ? $lead_details['service_id'] : $this->booking_model->getServiceId($lead_details['Product']);
                    $data = $this->miscelleneous->_allot_source_partner_id_for_pincode($service_id, $distict_details['state'], $rowData[0]['brand'], PAYTM);
                    if ($data) {
                        //Add this lead into the leads table
                        //Check whether this is a new Lead or Not
                        //Pass order id and partner source
                        $partner_booking = $this->partner_model->get_order_id_for_partner($data['partner_id'], $rowData[0]['order_id']);
                        if (is_null($partner_booking)) {
                            $booking['partner_id'] = $data['partner_id'];
                            $booking['source'] = $data['source'];
                            $booking['order_id'] = $rowData[0]['order_id'];

                            //$lead_details['Unique_id'] = $rowData[0]['Item ID'];
                            //$dateObj1 = PHPExcel_Shared_Date::ExcelToPHPObject($rowData[0]['Order Date']);
                            //$lead_details['DeliveryDate'] = $dateObj1->format('d/m/Y');
                            $appliance_details['brand'] = $unit_details['appliance_brand'] = isset($lead_details['service_appliance_data']['brand']) ? $lead_details['service_appliance_data']['brand'] : $rowData[0]['brand'];
                            $appliance_details['model_number'] = $unit_details['model_number'] = "";
                            $appliance_details['description'] = $unit_details['appliance_description'] = trim($rowData[0]['product_name']);

                            $booking['booking_address'] = $rowData[0]['address'];
                            $booking['booking_pincode'] = $rowData[0]['pincode'];
                            $booking['city'] = $rowData[0]['customer_city'];
                            $booking['state'] = $distict_details['state'];
                            $booking['district'] = $distict_details['district'];
                            $booking['taluk'] = $distict_details['taluk'];



                            $booking['booking_primary_contact_no'] = $rowData[0]['contact_number'];

                            $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($rowData[0]['shipped_date']);
                            $booking['shipped_date'] = date('Y-m-d H:i:s', strtotime($dateObj2->format('d-m-Y')));

                            $booking['current_status'] = "FollowUp";
                            $booking['internal_status'] = "Missed_call_not_confirmed";
                            $booking['create_date'] = date("Y-m-d H:i:s");

                            //Add this as a Query now
                            $booking['booking_id'] = '';
                            $appliance_details['user_id'] = $booking['user_id'] = $user_id;
                            $appliance_details['service_id'] = $unit_details['service_id'] = $booking['service_id'] = $service_id;
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


                            $unit_details['booking_id'] = $booking['booking_id'] = "Q-" . $booking['source'] . "-" . $booking['booking_id'];

                            $unit_details['partner_id'] = $booking['partner_id'];
                            $booking['quantity'] = '1';
                            $appliance_details['category'] = $unit_details['appliance_category'] = isset($lead_details['service_appliance_data']['category']) ? $lead_details['service_appliance_data']['category'] : $rowData[0]['category'];
                            $appliance_details['capacity'] = $unit_details['appliance_capacity'] = isset($lead_details['service_appliance_data']['capacity']) ? $lead_details['service_appliance_data']['capacity'] : '';
                            $appliance_details['tag'] = $unit_details['appliance_brand'] . " " . $unit_details['appliance_description'];
                            $appliance_details['purchase_month'] = $unit_details['purchase_month'] = date('m');
                            $appliance_details['purchase_year'] = $unit_details['purchase_year'] = date('Y');
                            $booking['partner_source'] = "Paytm-delivered-excel";

                            //get partner data to check the price
                            $this->initialized_variable->fetch_partner_data($booking['partner_id']);
                            $partner_data = $this->initialized_variable->get_partner_data();
                            $partner_mapping_id = $booking['partner_id'];
                            if ($partner_data[0]['partner_type'] == OEM) {
                                //if partner type is OEM then sent appliance brand in argument
                                $prices = $this->partner_model->getPrices($booking['service_id'], $unit_details['appliance_category'], $unit_details['appliance_capacity'], $partner_mapping_id, 'Installation & Demo', $unit_details['appliance_brand']);
                            } else {
                                //if partner type is not OEM then dose not sent appliance brand in argument
                                $prices = $this->partner_model->getPrices($booking['service_id'], $unit_details['appliance_category'], $unit_details['appliance_capacity'], $partner_mapping_id, 'Installation & Demo', "");
                            }
                            $booking['amount_due'] = '0';
                            $is_price = array();
                            $flag = array();
                            if (!empty($prices)) {
                                $unit_details['id'] = $prices[0]['id'];
                                $unit_details['price_tags'] = "Installation & Demo";
                                $unit_details['around_paid_basic_charges'] = $unit_details['around_net_payable'] = "0.00";
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

                            $booking['booking_date'] = date('d-m-Y', strtotime("+3 days", strtotime($booking['shipped_date'])));
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

                            $is_sms = $this->miscelleneous->check_upcountry($booking, $lead_details['Product'], $is_price, "");
                            if (!$is_sms) {
                                $booking['internal_status'] = SF_UNAVAILABLE_SMS_NOT_SENT;
                            } else {
                                $booking['sms_count'] = 1;
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
                                $this->insert_booking_in_partner_leads($booking, $unit_details, $user, $lead_details['Product']);
                                $this->notify->insert_state_change($booking['booking_id'], _247AROUND_FOLLOWUP, _247AROUND_NEW_QUERY, '', $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);
                                //Reset
                                if (empty($booking['state'])) {
                                    $to = NITS_ANUJ_EMAIL_ID;
                                    $message = "Pincode " . $booking['booking_pincode'] . " not found for Booking ID: " . $booking['booking_id'];
                                    $this->notify->sendEmail("booking@247around.com", $to, "", "", 'Pincode Not Found', $message, "");
                                }
                                $this->total_bookings_inserted++;
                            }
                            unset($booking);
                            unset($unit_details);
                            unset($appliance_details);
                        }
                    }
                }
            } else {
                $error = true;
                break;
            }
        }

        return $error;
    }

}
