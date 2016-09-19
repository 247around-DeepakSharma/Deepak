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

    function upload_snapdeal_file($file_type){
        //$objPHPExcel, file_type
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

        $headings = $sheet->rangeToArray('A1:' . $highestColumn . 1, NULL, TRUE, FALSE);
        $headings_new = array();
        $data = array();
        foreach ($headings as $heading) {
            $heading = str_replace(array("/", "(", ")", "."), "", $heading);
            array_push($headings_new, str_replace(array(" "), "_", $heading));
        }

        for ($row = 2, $i = 0; $row <= $highestRow; $row++, $i++) {
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowData = array_combine($headings_new[0], $rowData[0]);
            
            array_push($data, $rowData);
        }
       // print_r($data);

       $validate_data =  $this->validate_phone_number($data);
       $row_data1 =  $this->validate_product($validate_data);
       $row_data2 = $this->validate_delivery_date($row_data1, $file_type);
       $row_data =  $this->validate_pincode($row_data2);

       foreach ($row_data as $key => $value) {
           //echo print_r($rowData[0], true), EOL;
            if ($value['Phone'] == "") {
            //echo print_r("Phone number null, break from this loop", true), EOL;
            break;
            }

            //Insert user if phone number doesn't exist
           $output = $this->user_model->search_user(trim($row['Phone']));
           $state = $this->vendor_model->get_state_from_pincode($row['Pincode']);

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
            //state is Tamilnadu (pincode starts from 6). Else it would be
            //tagged to Snapdeal.
            //Ray brand should be tagged to Ray.
            //All other brands would go to Snapdeal.
            switch ($value['Brand']) {
                case 'Wybor':
                    if (substr($value['Pincode'], 0, 1) == "6") {
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
            if (is_null($partner_booking)) {

                $appliance_details['user_id'] = $booking['user_id'] = $user_id;
                $appliance_details['service_id'] = $unit_details['service_id'] = $booking['service_id'] =$value['service_id'];
                $booking['booking_pincode'] = $value['Pincode'];
                $appliance_details['brand'] = $unit_details['appliance_brand'] = $value['Brand'];

                if (isset($value['Expected_Delivery_Date'])){
                    $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($value['Expected_Delivery_Date']);

                } else{
                    $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($value['Delivery_Date']);
                }
                        
                if($file_type == "shipped"){
                    if ($dateObj2->format('d') == date('d')) {
                        //If date is NULL, add 3 days from today in EDD.
                        $dateObj2 = date_create('+3days');
                    }

                    $yy = $dateObj2->format('y');
                    $mm = $dateObj2->format('m');
                    $dd = $dateObj2->format('d'); 
                    $booking['partner_source'] = "Snapdeal-shipped-excel";
                    $booking['booking_date'] = $dateObj2->format('d-m-Y');

                    
                } else if($file_type == "delivered") {
  
                    $yy = date("y");
                    $mm = date("m");
                    $dd = date("d");
                    $booking['booking_date'] = '';
                    $booking['partner_source'] = "Snapdeal-delivered-excel";
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
                $appliance_details['tag'] = $unit_details['appliance_tag'] = $value['Brand'] . " " .$value['Product'];
                $booking['booking_remarks'] = '';
                $booking['booking_alternate_contact_no'] = '';
                $appliance_details['purchase_month'] = $unit_details['purchase_month'] = date('m');
                $appliance_details['purchase_year'] = $unit_details['purchase_year'] = date('Y');
                $appliance_details['last_service_date'] = date('d-m-Y');

                $unit_details['appliance_id'] = $this->booking_model->addappliance($appliance_details);
                if($unit_details['appliance_id']){
                    $unit_id = $this->booking_model->addunitdetails($unit_details);
                    if($unit_id){
                        $booking['order_id'] = $value['Sub_Order_ID'];
                        $ref_date = PHPExcel_Shared_Date::ExcelToPHPObject($value['Referred_Date_and_Time']);
                        $booking['reference_date'] = $ref_date->format('Y-m-d H:i:s');
                        $booking['booking_timeslot'] = '';
                        $booking['request_type'] = 'Installation & Demo';
                        $booking['delivery_date'] = $dateObj2->format('Y-m-d H:i:s');
                        $booking['estimated_delivery_date'] = $dateObj2->format('d/m/Y');
                        $booking['booking_primary_contact_no'] = $value['Phone'];
                        $booking['current_status'] = "FollowUp";
                        $booking['internal_status'] = "FollowUp";
                        $booking['type'] = "Query";
                        $booking['booking_address'] = $value['Customer_Address'];
                        $booking['query_remarks'] = 'Product Shipped, Call Customer For Booking';
                        $booking['city'] = $value['CITY'];
                        $booking['state'] = $state['state'];
                        $booking['quantity'] = '1';
                        $booking_details_id = $this->booking_model->addbooking($booking);
                        if($booking_details_id){} else {

                             log_message('info', __FUNCTION__ . ' =>  Booking is not inserted in booking details: ' . print_r($value, true));

                            $row_data['error'][$key]['booking_details'] = " Booking Unit Id is not inserted";
                            $row_data['error'][$key]['invalide_data'] = $value;
                        }
                        if (empty($booking['state'])) {
                            log_message('info', __FUNCTION__ . " Pincode is not found booking id: " . print_r($booking['booking_id'], true));
                            $url = base_url() . "employee/do_background_process/send_sms_email_for_booking";
                            $send['booking_id'] = $booking['booking_id'];
                            $send['state'] = "Pincode_not_found";
                            $this->asynchronous_lib->do_background_process($url, $send);
                        }

                        $this->insert_booking_in_partner_leads($booking, $unit_details,$user, $value['Product'] );

                        $this->notify->insert_state_change($booking['booking_id'], "FollowUp", "New_Query", $this->session->userdata('id'), $this->session->userdata('employee_id'));

                        // $userSession = array('success' => 'Please enter correct user name and password');
                        // $this->session->set_userdata($userSession);

                        //Reset
                        unset($appliance_details);
                        unset($booking);
                        unset($unit_details);

                    } else {

                        log_message('info', __FUNCTION__ . ' =>  Appliance is not inserted: ' .
                          print_r($value, true));

                        $row_data['error'][$key]['unit_details'] = " Booking Unit Id is not inserted";
                        $row_data['error'][$key]['invalide_data'] = $value;
                    }
                    
                } else {
                    log_message('info', __FUNCTION__ . ' =>  Appliance is not inserted: ' .
                     print_r($value, true));

                    $row_data['error'][$key]['appliance'] = "Appliance is not inserted";
                    $row_data['error'][$key]['invalide_data'] = $value;

                }
               

            } else if($file_type == "delivered"){
                $status = $partner_booking['current_status'];
                $int_status = $partner_booking['internal_status'];

                //Clear the booking date so that it starts reflecting on our panel & update booking.
                //This should be done only if the booking has not been updated in the meanwhile.
                //If the booking has already been scheduled or cancelled, leave this as it is.
                //If the booking query remarks or internal status has been changed, then also leave it.
                //Update delivery date in both the cases
                $dateObj = PHPExcel_Shared_Date::ExcelToPHPObject($value['Delivery_Date']);
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
        if(isset($row_data['error'])){
            $this->get_invalid_data($row_data['error']);
        }
        
       

    }
    
    /**
     * @desc: This method is used to validate Phone number while upload excel file
     * We will count of invalidate data, If count is greater or equal to five. 
     * It will send Invalidate data to mail and exit from function 
     * Otherwise return data with inavlidate data
     * @param: Array
     * @param: Array
     */
    function validate_phone_number($data){
        $invalid_data = array();
        foreach ($data as $key => $value) {
            
            if(count($invalid_data) > 4){

                $status['reason'] = "Phone Number is not valid";
                $status['validate'] =  $invalid_data;
                $this->get_invalid_data($status);
                exit();
            }
            // check mobile number validation
            if(!preg_match('/^\d{10}$/',$value['Phone'])) {
                unset($data[$key]);
                array_push($invalid_data, $value);
            } 
        }
        // append invalidate data. size of invalidate data is less than 5
        if(!empty($valid_data)){
            log_message('info', __FUNCTION__ . ' =>  Phone Number is not valid Excel data: ' .
            print_r($invalid_data, true));

            $data['error']['reason_phone'] = "Phone Number is not valid";
            $data['error']['validate_phone'] =  $invalid_data;
        }

        return $data;
    }

    function validate_product($data){
        $invalid_data = array();
        foreach($data as $key => $value){
            $flag = 0;
            if(count($invalid_data) > 4){

                $status['reason_product'] = "Product is not valid";
                $status['validate_phone'] =  $invalid_data;
                $this->get_invalid_data($status);
                exit();
            }

            $prod = trim($value['Product']);
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

            if (stristr($prod, "microwave cooking")) {
                $flag = 1;
                unset($data[$key]);
                array_push($invalid_data, $value);
            }

            if (stristr($prod, "Tds Meter")) {
                $flag = 1;
                unset($data[$key]);
                array_push($invalid_data, $value);
            }

            if (stristr($prod, "Accessories")) {
                $flag = 1;
                unset($data[$key]);
                array_push($invalid_data, $value);
            }

            if($flag == 0){
                $service_id =  $this->booking_model->getServiceId($prod);
                if($service_id){

                    $data[$key]['service_id'] = $service_id;

                } else {
                    unset($data[$key]);
                    array_push($invalid_data, $value);
                }
            } 
        }

        if(!empty($valid_data)){
            log_message('info', __FUNCTION__ . ' =>  Product is not valid in Excel data: ' .
            print_r($invalid_data, true));
            $data['error']['reason_product'] = "Product is not valid";
            $data['error']['validate_product'] =  $invalid_data;
        }

        return $data;
    }
    
    /**
     * @desc: This is used to validate pincode. pincode must be 6 digit integer
     */
    function validate_pincode($data){
        $invalid_data = array();
        foreach ($data as $key => $value) {
            if(count($invalid_data) > 4){

                $status['reason'] = " Pincode is not valid in File";
                $status['validate'] =  $invalid_data;
                $this->get_invalid_data($status);
                exit();
            }
            // check pincode is 6 digit
            if(!preg_match('/^\d{6}$/',$value['Pincode'])) {

                unset($data[$key]);
                array_push($invalid_data, $value);
            } 
        }
        // append invalidate data. size of invalidate data is less than 5
        if(!empty($valid_data)){
            log_message('info', __FUNCTION__ . ' =>  Pincode is not valid in Excel data: ' .
            print_r($invalid_data, true));

            $data['error']['reason_pincode'] = "Pincode is not valid";
            $data['error']['validate_pincode'] =  $invalid_data;
        }
       // print_r($data);
        return $data;
    }
    /**
     * @desc: This is used to validate delivery date.
     * if delivery file is uploaded then it unset future date and 
     * if count is greater than 5, it exit and trigger mail.
     * If shipped file is uploded then return count future and past date
     */
    function validate_delivery_date($data, $file_type){
        $invalid_data = array();
        $future_date = 0;
        $past_date = 0;
        foreach ($data as $key => $value) {
            $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($value['Delivery_Date']);
            if(count($invalid_data) > 4){

                $status['reason'] = " Delivery is not valid in Excel data";
                $status['validate'] =  $invalid_data;
                $this->get_invalid_data($status);
                exit();
            }
            if($file_type == "delivered"){
                 if(date('Y-m-d') < $dateObj2->format('Y-m-d')) {
                    //Future Date
                    unset($data[$key]);
                    array_push($invalid_data, $value);
                 } 

            } else if($file_type == "shipped"){
                if(date('Y-m-d') < $dateObj2->format('Y-m-d')) {
                    //Future Date
                    $future_date++;
                
                } else {
                    $past_date++;
                }
            }
        }

        if(!empty($valid_data)){
            log_message('info', __FUNCTION__ . ' =>  Product is not valid in Excel data: ' .
            print_r($invalid_data, true));
            $data['error']['reason_delivery_date'] = "Product is not valid";
            $data['error']['validate_delivery_date'] =  $invalid_data;
        }
        if($file_type == "shipped"){
            $data['error']['count_past_delivery_date'] = $past_date;
            $data['error']['count_future_delivery_date'] = $future_date;
        }
        return $data;
    }
    
    function get_invalid_data($invalid_data_with_reason){
        
        $to = "anuj@247around.com";
        //$to = "abhaya@247around.com";
        $from = "booking@247around.com";
        $cc = "";
        $bcc = "";
        $subject = 'Invalidate Data in Excel File';
        $message = json_encode($invalid_data_with_reason);
        $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, "");
    }

    function send_sms($product){
        //Send SMS to customer about free installation
        switch ($product) {
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
        if($partner_leads_id){
            return true;
        } else {
           log_message('info', __FUNCTION__." Booking is not inserted into Partner Leads table:". print_r($partner_booking, true));
        }

    }
}