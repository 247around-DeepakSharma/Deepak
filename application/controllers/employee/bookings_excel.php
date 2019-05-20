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


//	if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
//	    return TRUE;
//	} else {
//	    redirect(base_url() . "employee/login");
//	}
    }

    /*
     * @desc: This function gets the page to upload booking excel
     * @param: void
     * @return: void
     */

    public function index() {
        
	$this->miscelleneous->load_nav_header();
	$this->load->view('employee/upload_bookings_excel');
    }

    /*
     * @desc: This function is to upload the products that have been shipped from our partners
     * @param: void
     * @return: void
     */

    public function upload_shipped_products_excel() {
          $this->miscelleneous->load_nav_header();
          $this->load->view('employee/upload_shippings_excel');
    }

    /*
     * @desc: This function is to upload the products that have been delevered from our partners
     * @param: void
     * @return: void
     */

    public function upload_delivered_products_for_paytm_excel() {
       
	$this->miscelleneous->load_nav_header();
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
            //Email Message ID - Unique for every email
            $email_message_id = !($this->input->post('email_message_id') === NULL)?$this->input->post('email_message_id'):'';
            if(!empty($this->input->post('email_send_to'))){
                $this->email_send_to = $this->input->post('email_send_to');
            }
            //Processing File
            $response['data'] = $this->process_upload_file($inputFileName, $inputFileExtn);
            if(!empty($response['data'])){
                $response['upload_file_name'] = $_FILES["file"]["name"];
                $html = $this->load->view('employee/email_paytm_upload_file_details',$response,TRUE);
                $to = empty($this->email_send_to)?(empty($this->session->userdata('official_email'))?_247AROUND_SALES_EMAIL:$this->session->userdata('official_email')):$this->email_send_to;
                $cc = DEVELOPER_EMAIL;
                $agent_name = !empty($this->session->userdata('emp_name'))?$this->session->userdata('emp_name'):_247AROUND_DEFAULT_AGENT_NAME;
                $subject = "Paytm File is uploaded by " . $agent_name;
                $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $html, "",PAYTM_FILE_UPLOADED);
                log_message('info', 'paytm file uploaded successfully.' . print_r($response,true));
                
                //Updating File Uploads table and upload file to s3
                $this->miscelleneous->update_file_uploads($_FILES["file"]["name"],$_FILES["file"]["tmp_name"],_247AROUND_PAYTM_DELIVERED,FILE_UPLOAD_SUCCESS_STATUS,$email_message_id, "partner", PAYTM);
            }else{
                log_message('info', "Paytm file upload failed");
                //Updating File Uploads table and upload file to s3
                $this->miscelleneous->update_file_uploads($_FILES["file"]["name"],$_FILES["file"]["tmp_name"],_247AROUND_PAYTM_DELIVERED,FILE_UPLOAD_FAILED_STATUS,$email_message_id, "partner", PAYTM);
            }
            
            //send mail to paytm if incorrect pincode found
            if (!empty($response['data'][0]['incorrct_pincode'])) {
                log_message('info',"Sending Incoorect Pincode Mail To Paytm");
                //Getting template from Database
                $template = $this->booking_model->get_booking_email_template("missing_pincode_mail");
                $subject = vsprintf($template[4], $_FILES["file"]["name"]);
                $attachement = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY.'/vendor-partner-docs/'.date('d-M-Y-H-i-s')."-".$_FILES["file"]["name"];
                $email_html = "";
                foreach ($response['data'][0]['incorrct_pincode'] as $value){
                    $email_html .= $value['order_id']."<br>";
                }
                $emailBody = vsprintf($template[0], array($_FILES["file"]["name"],$email_html));
                $this->notify->sendEmail($template[2], $template[1] ,$template[3], '', $subject , $emailBody, $attachement,'missing_pincode_mail');
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
     * @desc: This function is used to check the file upload header
     * @param: $data array()
     * @return: $return_response array()
     */
    function check_column_exist($data){
        foreach($data['actual_header_data'] as $value){
            //check all header in the file are as per our database
            $is_all_header_present = array_diff(array_values($value),$data['header_data']);
            if(empty($is_all_header_present)){
                $return_response['status'] = TRUE;
                $return_response['msg'] = '';
                break;
            }else{
                $this->Columfailed = "<b>".implode($is_all_header_present, ',')." </b> column does not exist.Please correct these and upload again. <br><br><b> For reference,Please use previous successfully upload file from CRM</b>";
                $return_response['status'] = FALSE;
                $return_response['msg'] = $this->Columfailed;
            }
        }
        
        return $return_response;
    }
    
    /*
     * @desc: This function is to upload the Satya File
     * @param: void
     * @return: void
     */

    public function upload_satya_file() {
        
	$this->miscelleneous->load_nav_header();
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
                    $to = empty($this->email_send_to)?(empty($this->session->userdata('official_email'))?_247AROUND_SALES_EMAIL:$this->session->userdata('official_email')):$this->email_send_to;
                    $cc = DEVELOPER_EMAIL;
                    $subject = "PAYTM FILE UPLOAD FAILED!!!";
                    $agent_name = !empty($this->session->userdata('emp_name'))?$this->session->userdata('emp_name'):_247AROUND_DEFAULT_AGENT_NAME;
                    $message = "File Uploaded By ". $agent_name;
                    $message .= "Sheet Name = <b>".$sheet->getTitle()."</b> <br><br>";
                    $message .= $this->Columfailed;
                    $message .= "Please Check File And Upload Again";
                    $this->notify->sendEmail(NOREPLY_EMAIL_ID,$to,$cc,"",$subject, $message,"",PAYTM_FILE_UPLOAD_FAILED);
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

            $check_header['header_data'] = array_keys($rowData[0]);
            $select = 'sub_order_id,product_type,brand,customer_name,customer_address,pincode,phone,city,delivery_date';
            $check_header['actual_header_data'] = $this->reusable_model->get_search_query('partner_file_upload_header_mapping',$select,array('partner_id'=>PAYTM),NULL,NULL,NULL,NULL,NULL)->result_array();
            $response = $this->check_column_exist($check_header);
            if (!empty($response['status'])) {
                if (empty($rowData[0]['contact_number'])) {
                    $error = true;
                    $empty_contact = true;
                    array_push($this->empty_contact_number, $rowData[0]);
                } else {
                    $error = false;
                    $empty_contact = false;
                }

                if ($empty_contact === false) {
                    //Sanitizing Brand Name
                    if (!empty($rowData[0]['brand'])) {
                        $rowData[0]['brand'] = preg_replace('/[^A-Za-z0-9 ]/', '', $rowData[0]['brand']);
                    }

                    //Insert user if phone number doesn't exist
                    $output = $this->user_model->search_user(trim($rowData[0]['contact_number']));
                    $distict_details = $this->vendor_model->get_distict_details_from_india_pincode(trim($rowData[0]['pincode']));
                    
                    if (empty($rowData[0]['pincode'])) {
                        $match = array();
                        //extract pincode from address
                        preg_match('/[0-9]{6}/', $rowData[0]['address'], $match);

                        if (!empty($match)) {
                            $rowData[0]['pincode'] = $match[0];
                        }else{
                            $rowData[0]['pincode'] = !empty($output)?$output[0]['pincode']:'';
                        }
                    }
                    
                    if (empty($output)) {
                        //User doesn't exist
                        $user_name = $this->miscelleneous->is_user_name_empty(trim($rowData[0]['customer_firstname'] ), $rowData[0]['customer_email'], $rowData[0]['contact_number']);
                        $user['name'] = $user_name;
                        $user['phone_number'] = $rowData[0]['contact_number'];
                        $user['user_email'] = $rowData[0]['customer_email'];
                        $user['home_address'] = $rowData[0]['address'];
                        $user['pincode'] = $rowData[0]['pincode'];
                        $user['city'] = !empty($rowData[0]['customer_city'])?$rowData[0]['customer_city']:$distict_details['district'];
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
                        $user['name'] = trim($rowData[0]['customer_firstname']);
                        $user['user_email'] = $rowData[0]['customer_email'];
                        $user_id = $output[0]['user_id'];
                    }
                    
                    if(empty($rowData[0]['pincode'])){
                        array_push($this->incorrect_pincode, $rowData[0]);
                    }
                    
                    $prod = trim($rowData[0]['category']);
                    $lead_details = array();

                    //check if service_id already exist or not by using product description
                    if(!empty($rowData[0]['brand'])){
                        $where = array('product_description' => trim($rowData[0]['product_name']),
                                        'brand' => $rowData[0]['brand']);
                    }else{
                        $where = array('product_description' => trim($rowData[0]['product_name']));
                    }
                    $service_appliance_data = $this->booking_model->get_service_id_by_appliance_details($where);

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
                        
                        $check_partner_booking = $this->partner_model->get_order_id_for_partner($data['partner_id'], $rowData[0]['order_id']);
                        if(!is_null($check_partner_booking)){
                            $partner_booking = $check_partner_booking;
                        }else{
                            if(isset($rowData[0]['order_item_id']) && !empty($rowData[0]['order_item_id'])){
                                $rowData[0]['order_id'] = $rowData[0]['order_id']."-".$rowData[0]['order_item_id'];
                            } else if(isset($rowData[0]['item_id']) && !empty($rowData[0]['item_id'])){
                                $rowData[0]['order_id'] = $rowData[0]['order_id']."-".$rowData[0]['item_id'];
                            }else{
                                $rowData[0]['order_id'] = $rowData[0]['order_id'];
                            }
                            
                            $partner_booking = $this->partner_model->get_order_id_for_partner($data['partner_id'], $rowData[0]['order_id']);
                        }
                        
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
                            $booking['city'] = !empty($rowData[0]['customer_city'])?$rowData[0]['customer_city']:$distict_details['district'];
                            $booking['state'] = $distict_details['state'];
                            $booking['district'] = $distict_details['district'];
                            $booking['taluk'] = $distict_details['taluk'];
                            if(isset($rowData[0]['order_item_id'])){
                                $unit_details['sub_order_id'] = $rowData[0]['order_item_id'];
                            }else if(isset($rowData[0]['item_id'])){
                                $unit_details['sub_order_id'] = $rowData[0]['item_id'];
                            }
                            

                            $booking['booking_primary_contact_no'] = $rowData[0]['contact_number'];
                            
                            if (is_string($rowData[0]['shipped_date'])) {
                                $booking['shipped_date'] = date('Y-m-d H:i:s', strtotime($rowData[0]['shipped_date']));
                            } else {
                                $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($rowData[0]['shipped_date']);
                                $booking['shipped_date'] = date('Y-m-d H:i:s', strtotime($dateObj2->format('d-m-Y')));
                            }
                            
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
                            $appliance_details['purchase_date'] = $unit_details['purchase_date'] = date('Y-m-d');
                            $booking['partner_source'] = "Paytm-delivered-excel";

                            //get partner data to check the price
                            $this->initialized_variable->fetch_partner_data($booking['partner_id']);
                            $partner_data = $this->initialized_variable->get_partner_data();
                            $partner_mapping_id = $booking['partner_id'];
                            if ($partner_data[0]['partner_type'] == OEM) {
                                //if partner type is OEM then sent appliance brand in argument
                                $prices = $this->partner_model->getPrices($booking['service_id'], $unit_details['appliance_category'], $unit_details['appliance_capacity'], $partner_mapping_id, 'Installation & Demo', $unit_details['appliance_brand'], false);
                            } else {
                                //if partner type is not OEM then dose not sent appliance brand in argument
                                $prices = $this->partner_model->getPrices($booking['service_id'], $unit_details['appliance_category'], $unit_details['appliance_capacity'], $partner_mapping_id, 'Installation & Demo', "", FALSE);
                            }
                            $booking['amount_due'] = '0';
                            $is_price = array();
                            $flag = array();
                            if (!empty($prices) && count($prices) == 1) {
                                $unit_details['id'] = $prices[0]['id'];
                                $unit_details['price_tags'] = $prices[0]['service_category'];
                                $unit_details['around_paid_basic_charges'] = $unit_details['around_net_payable'] = "0.00";
                                $unit_details['partner_paid_basic_charges'] = $prices[0]['partner_net_payable'];
                                $unit_details['partner_net_payable'] = $prices[0]['partner_net_payable'];
                                $booking['amount_due'] = $prices[0]['customer_net_payable'];
                                $is_price['customer_net_payable'] = $prices[0]['customer_net_payable'];
                                $is_price['is_upcountry'] = $prices[0]['is_upcountry'];
                                $flag = array('1');
                            }
                            
                            $unit_details['booking_status'] = _247AROUND_FOLLOWUP;


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
                            $actor = $next_action = 'NULL';
                            $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'], $booking['partner_id'], $booking['booking_id']);
                            if (!empty($partner_status)) {
                                $booking['partner_current_status'] = $partner_status[0];
                                $booking['partner_internal_status'] = $partner_status[1];
                                $actor = $booking['actor'] = $partner_status[2];
                                $next_action = $booking['next_action'] = $partner_status[3];
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
                                if(empty($this->session->userdata('id'))){
                                    $this->notify->insert_state_change($booking['booking_id'], _247AROUND_FOLLOWUP, _247AROUND_NEW_QUERY, $booking['query_remarks'], 
                                            _247AROUND_DEFAULT_AGENT, _247AROUND_DEFAULT_AGENT_NAME, $actor,$next_action,_247AROUND);
                                }else{
                                    $this->notify->insert_state_change($booking['booking_id'], _247AROUND_FOLLOWUP, _247AROUND_NEW_QUERY, '', $this->session->userdata('id'), 
                                            $this->session->userdata('employee_id'), $actor,$next_action,_247AROUND);
                                }
                                //Reset
                                if (empty($booking['state'])) {
                                    //$to = NITS_ANUJ_EMAIL_ID;
                                   // $message = "Pincode " . $booking['booking_pincode'] . " not found for Booking ID: " . $booking['booking_id'];
                                    //$this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", 'Pincode Not Found', $message, "",PINCODE_NOT_FOUND);
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
    
    /*
     * @desc: This function is to upload the Akai File
     * @param: void
     * @return: void
     */

    public function upload_akai_file() {
        
	$this->miscelleneous->load_nav_header();
	$this->load->view('employee/upload_akai_file');
    }
    
    /**
     * @desc: This function is used for mapping between partner_id and upload file header
     * @param: void
     * @return: void
     */

    public function file_upload_header_mapping() {
        
	$this->miscelleneous->load_nav_header();
	$this->load->view('employee/file_upload_header_mapping');
    }
    
    /**
     * @desc: This function is used show the data from partner_file_upload_header_mapping table
     * @param: void
     * @return: void
     */

    
    public function get_file_upload_header_mapping_data() {
        $post = $this->get_post_data();
        $select = "partner_file_upload_header_mapping.*,partners.public_name,employee.full_name,email_attachment_parser.email_host,email_attachment_parser.file_type,email_attachment_parser.send_file_back,email_attachment_parser.revert_file_to_email,email_attachment_parser.partner_id,email_attachment_parser.email_map_id";
        $list = $this->partner_model->get_file_upload_header_mapping_data($post, $select);
        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row = $this->file_upload_header_mapping_data_table($order_list, $no);
            $data[] = $row;
        }
        
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->partner_model->get_file_upload_header_mapping_data($post,'count(distinct(partner_file_upload_header_mapping.id)) as numrows')[0]->numrows,
            "recordsFiltered" =>  $this->partner_model->get_file_upload_header_mapping_data($post,'count(distinct(partner_file_upload_header_mapping.id)) as numrows')[0]->numrows,
            "data" => $data,
        );
        
        echo json_encode($output);
    }
    
    /**
     * @desc: This function is used generate table from partner_file_upload_header_mapping table
     * @param: $order_list array
     * @param: $no string
     * @return: void
     */
    private function file_upload_header_mapping_data_table($order_list, $no){
        $row = array();
        $json_data = json_encode($order_list);
        $row[] = $no;
        $row[] = $order_list->public_name;
        $row[] = $order_list->email_host;


        
        // $row[] = $order_list->referred_date_and_time;
        // $row[] = $order_list->sub_order_id;
        // $row[] = $order_list->brand;
        // $row[] = $order_list->model;
        // $row[] = $order_list->product;
        // $row[] = $order_list->product_type;
        // $row[] = $order_list->customer_name;
        // $row[] = $order_list->customer_address;
        // $row[] = $order_list->pincode;
        // $row[] = $order_list->city;
        // $row[] = $order_list->phone;
        // $row[] = $order_list->email_id;
        // $row[] = $order_list->order_item_id;
        // $row[] = $order_list->spd;
        // $row[] = $order_list->delivery_date;
        $row[] = $order_list->full_name;
        $row[] = "<a href='javascript:void(0)' class ='btn btn-primary' id='edit_mapping_details' data-id='$json_data'>Edit</a>";
        
        return $row;
    }

    /**
     *  @desc : This function is used to get the post data to show partner_file_upload_header_mapping details
     *  @param : void()
     *  @return : $post Array()
     */
    private function get_post_data(){
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');

        return $post;
    }
    
    /**
     *  @desc : This function is used to perform add/edit action on the partner_file_upload_header_mapping table
     *  @param : void()
     *  @return : $response JSON
     */


 


    function process_file_upload_header_mapping() {
        $submit_type = $this->input->post('submit_type');
        $data = array('partner_id' => $this->input->post('partner_id'),
                      'referred_date_and_time' => str_replace(" ","_",strtolower(trim($this->input->post('r_d_a_t')))),
                      'sub_order_id' => str_replace(" ","_",strtolower(trim($this->input->post('sub_order_id')))),
                      'brand' => str_replace(" ","_",strtolower(trim($this->input->post('brand')))),
                      'model' => str_replace(" ","_",strtolower(trim($this->input->post('model')))),
                      'product' => str_replace(" ","_",strtolower(trim($this->input->post('product')))),
                      'product_type' => str_replace(" ","_",strtolower(trim($this->input->post('product_type')))),
                      'customer_name' => str_replace(" ","_",strtolower(trim($this->input->post('customer_name')))),
                      'customer_address' => str_replace(" ","_",strtolower(trim($this->input->post('customer_address')))),
                      'pincode' => str_replace(" ","_",strtolower(trim($this->input->post('pincode')))),
                      'city' => str_replace(" ","_",strtolower(trim($this->input->post('city')))),
                      'phone' => str_replace(" ","_",strtolower(trim($this->input->post('phone')))),
                      'email_id' => str_replace(" ","_",strtolower(trim($this->input->post('email_id')))),
                      'delivery_date' => str_replace(" ","_",strtolower(trim($this->input->post('delivery_date')))),
                      'agent_id' =>$this->session->userdata('id'),
                      'order_item_id' => str_replace(" ","_",strtolower(trim($this->input->post('order_item_id')))),
                      'spd' => str_replace(" ","_",strtolower(trim($this->input->post('spd')))),
                      'category'=>str_replace(" ","_",strtolower(trim($this->input->post('category')))),
                      'request_type'=>str_replace(" ","_",strtolower(trim($this->input->post('request_type')))),

                      // 'email_host'=>trim($this->input->post('host')),
                      // 'file_type'=>trim($this->input->post('filetype')),
                      // 'send_file_back'=>trim($this->input->post('sendback')),
                      // 'revert_file_to_email'=>trim($this->input->post('revertemail'))
            );








        switch (strtolower(trim($submit_type))) {
            case 'add':
                $data['create_date'] = date('Y-m-d H:i:s');
                $response = $this->add_file_upload_header_mapping($data);
                break;
            case 'save':
                $response = $this->edit_file_upload_header_mapping($data);
                break;
        }
        
        echo json_encode($response);
    }
    
    /**
     *  @desc : This function is used to perform insert action on the partner_file_upload_header_mapping table
     *  @param : $data array()
     *  @return : $res array()
     */
    function add_file_upload_header_mapping($data) {


        

        $response = $this->partner_model->insert_partner_file_upload_header_mapping($data);
        // $this->partner_model->insert_partner_file_upload_header_mapping($data);

          if (!empty($response)) {


            $partner_id= $this->input->post('partner_id');
            $sqlpartner="select * from partners where id='$partner_id'";
            $partnerdata = $this->db->query($sqlpartner)->row();
            $filetypename=$this->input->post('filetype');
            $filename = $partnerdata->public_name.'-'.$filetypename;
                      $dataarr = array(
                      'partner_id'=>trim($this->input->post('partner_id')),
                      'email_host'=>trim($this->input->post('host')),
                      'file_type'=>trim($filename),
                      'send_file_back'=>trim($this->input->post('sendback')),
                      'revert_file_to_email'=>trim($this->input->post('revertemail')),
                      'email_function_name'=>'employee/do_background_upload_excel/process_upload_file',
                      'email_map_id'=>$response

            );


            $this->db->insert('email_attachment_parser',$dataarr);

            $res['response'] = 'success';
            $res['msg'] = 'partner file upload header mapping added successfully';
            log_message("info", 'partner file upload header mapping added successfully');
        } else {
            $res['response'] = 'error';
            $res['msg'] = 'error in inserting partner file upload header mapping details';
            log_message("info", 'error in inserting partner file upload header mapping details');
        }
        
        return $res;
    }
    
    /**
     *  @desc : This function is used to perform edit action on the partner_file_upload_header_mapping table
     *  @param : $data array()
     *  @return : $res array()
     */
    function edit_file_upload_header_mapping($data) {
$response = $this->partner_model->update_partner_file_upload_header_mapping(array('id' => $this->input->post('file_upload_header_mapping_id')), $data);
        if (!empty($response)) {



            $partner_id= $this->input->post('partner_id');
            $sqlpartner="select * from partners where id='$partner_id'";
            $partnerdata = $this->db->query($sqlpartner)->row();
            $filetypename=$this->input->post('filetype');
            $filename = $partnerdata->public_name.'-'.$filetypename;
                      $dataarr = array(
                      'partner_id'=>trim($this->input->post('partner_id')),
                      'email_host'=>trim($this->input->post('host')),
                      'file_type'=>trim($filename),
                      'send_file_back'=>trim($this->input->post('sendback')),
                      'revert_file_to_email'=>trim($this->input->post('revertemail')),
                      'email_function_name'=>'employee/do_background_upload_excel/process_upload_file'
                     // 'email_map_id'=>$response

            );


            $this->db->where('email_map_id',$this->input->post('email_map_id'));
            $this->db->update('email_attachment_parser',$dataarr);





            $res['response'] = 'success';
            $res['msg'] = 'Dedailts has been updated successfully';
            log_message("info", 'partner file upload header mapping updated successfully');
        } else {
            $res['response'] = 'error';
            $res['msg'] = 'error in updating mapping details';
            log_message("info", 'error in updating partner file upload header mapping details');
        }
        
        return $res;
    }
    
    /**
     * @desc: This function is to upload the Aquagrand Plus File
     * @param: void
     * @return: void
     */

    public function upload_aquagrand_plus_file() {
        
	$this->miscelleneous->load_nav_header();
	$this->load->view('employee/upload_aquagrand_plus_file');
    }

}
