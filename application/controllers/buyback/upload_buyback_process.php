<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once BASEPATH . 'libraries/spout-2.4.3/src/Spout/Autoloader/autoload.php';

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

ini_set('max_execution_time', 360000); //3600 seconds = 60 minutes
ini_set('memory_limit', '-1');

class Upload_buyback_process extends CI_Controller {
    var $Columfailed = "";
    var $upload_sheet_data = array();
    var $email_send_to = "";
    var $price_quote_data = array();
    var $price_data = array();
    var $order_key_city_arr = array();

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();

        $this->load->helper(array('form', 'url'));
        $this->load->library('PHPReport');
        $this->load->library('form_validation');
        $this->load->library('buyback');
        $this->load->library("initialized_variable");
        $this->load->library('table');
        $this->load->library('partner_utilities');
        $this->load->library('s3');
        $this->load->library('notify');
        $this->load->library("miscelleneous");
        $this->load->library('invoice_lib');
        $this->load->library('booking_utilities');
        $this->load->model("partner_model");
        $this->load->model('reporting_utils');
        $this->load->model('invoices_model');

        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            return TRUE;
//            echo PHP_EOL . 'Terminal Access Not Allowed' . PHP_EOL;
//            redirect(base_url() . "employee/login");
        }
    }

    function index() {

        $data['saas_flag'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'),$data);
        $this->load->view('buyback/order_details_file_upload');
        $this->load->view('dashboard/dashboard_footer');
    }
    
     function price_sheet_upload() {

        $data['saas_flag'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'),$data);
        $this->load->view('buyback/price_sheet_upload');
        $this->load->view('dashboard/dashboard_footer');
    }
    
    function buyback_file_processing(){
        $pathinfo = pathinfo($_FILES["file"]["name"]);
        if(!empty($this->input->post('email_send_to'))){
            $this->email_send_to = $this->input->post('email_send_to');
        }
        $MB = 1048576;
        $template = array(
            'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
        );

        $this->table->set_template($template);

        $this->table->set_heading(array('Order ID'));
        
        switch ($pathinfo['extension']) {
            case 'xlsx':
            case 'xls':
                if ($_FILES['file']['size'] > 0 && $_FILES['file']['size'] < 2 * $MB) {
                    if ($pathinfo['extension'] == 'xlsx') {
                        $inputFileExtn = 'Excel2007';
                    } else {
                        $inputFileExtn = 'Excel5';
                    }

                    return $this->retrevie_buyback_xlsx_file_data($inputFileExtn);
                } else {
                    $response = array("code" => -247, "msg" => "Upload file size must be less than 2mb.");
                    return $response;
                }
                break;
            case 'csv':
                if ($_FILES['file']['size'] > 0) {
                    return $this->retrevie_buyback_csv_file_data();
                } else {
                   $response =  array("code" => -247, "msg" => "Please Upload Valid CSV File. File is empty");
                   return $response;
                }
                break;
            default :
                $response = array("code" => -247, "msg" => "File Format Is Incorrct. Please Upload Only xlsx Or xlx Or CSV file");
                return $response;
        }
    }
    
    function process_upload_order() {
        $response = $this->buyback_file_processing();
        $order_file = $_FILES["file"]["name"];
        $email_message_id = !($this->input->post('email_message_id') === NULL)?$this->input->post('email_message_id'):'';
        $insert_id = FALSE;
        if(!empty($this->upload_sheet_data)){
            $insert_id = $this->bb_model->insert_bb_sheet_data($this->upload_sheet_data);
        }
        if ($insert_id) {
            log_message('info', "Buyback Sheet Data Inserted");
        } else {
           log_message('info', "Error In Inserting Buyback Sheet Data");
        }
        
        if ($response['code'] == -247) {
            echo json_encode($response);
            $this->miscelleneous->update_file_uploads($order_file, $_FILES['file']['tmp_name'],_247AROUND_BB_ORDER_LIST,FILE_UPLOAD_FAILED_STATUS,$email_message_id, "partner", AMAZON_SELLER_ID);
        } else {
           
            $to = empty($this->email_send_to)?(empty($this->session->userdata('official_email'))?ANUJ_EMAIL_ID:$this->session->userdata('official_email') . ", " . ANUJ_EMAIL_ID):$this->email_send_to;
            $cc = NITS_EMAIL_ID;

            $message = "";
            $agent_name = !empty($this->session->userdata('emp_name'))? $this->session->userdata('emp_name'): _247AROUND_DEFAULT_AGENT_NAME;
            $subject = "Buyback Order is uploaded by " . $agent_name;

            $message .= "Order File Name ---->" . $order_file . "<br/><br/>";
            $message .= "Total lead  ---->" . $response['msg'] . "<br/><br/>";
            $message .= "Total Delivered ---->" . $this->initialized_variable->delivered_count() . "<br/><br/>";
            $message .= "Total Inserted ---->" . $this->initialized_variable->total_inserted() . "<br/><br/>";
            $message .= "Total Updated ---->" . $this->initialized_variable->total_updated() . "<br/><br/>";
            $message .= "Total Not Assigned ---->" . $this->initialized_variable->not_assigned_order() . "<br/><br/>";
            $message .= "Please check below orders, these were not assigned: <br/><br/><br/>";
            $message .= $this->table->generate();
            
            $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $message, "",BUY_BACK_ORDER_TAG);

            $this->miscelleneous->update_file_uploads($order_file, $_FILES['file']['tmp_name'],_247AROUND_BB_ORDER_LIST,FILE_UPLOAD_SUCCESS_STATUS,$email_message_id, "partner", AMAZON_SELLER_ID);
            $response = array("code" => 247, "msg" => "File sucessfully processed.");
            echo json_encode($response);
        }
    }

    function retrevie_buyback_xlsx_file_data($inputFileExtn) {
        $inputFileName = $_FILES['file']['tmp_name'];
        try {
            $objReader = PHPExcel_IOFactory::createReader($inputFileExtn);
            $objPHPExcel = $objReader->load($inputFileName);

            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestDataRow();
            $highestColumn = $sheet->getHighestDataColumn();

            $headings = $sheet->rangeToArray('A1:' . $highestColumn . 1, NULL, TRUE, FALSE);

            $headings_new = array();
            foreach ($headings as $heading) {
                $heading = str_replace(array("/", "(", ")", " ", "."), "", $heading);
                array_push($headings_new, str_replace(array(" "), "_", $heading));
            }
           
            for ($row = 2, $i = 0; $row <= $highestRow; $row++, $i++) {
                //  Read a row of data into an array
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
                $rowData11 = array_combine($headings_new[0], $rowData[0]);
                $rowData1 = array_change_key_case($rowData11);
                $this->Columfailed = "";
                $status = $this->check_column_exist($rowData1);
                if ($status) {
                    //Change index in lower case
                    $this->initialized_variable->set_post_buyback_order_details(array());

                    $rowData1['partner_id'] = AMAZON_SELLER_ID;
                    $rowData1['partner_name'] = "Amazon";
                    $rowData1['partner_charge'] = $rowData1['discount_value'];
                    $dateObj1 = PHPExcel_Shared_Date::ExcelToPHPObject($rowData1['order_day']);
                    $rowData1['order_date'] = $dateObj1->format('Y-m-d');
                    $rowData1['order_key'] = $rowData1['buyback_details'];
                    $rowData1['current_status'] = $rowData1['orderstatus'];
                    $rowData1['partner_sweetner_charges'] = isset($rowData1['sweetenervalue']) ? $rowData1['sweetenervalue'] : '';
                    $rowData1['partner_order_id'] = $rowData1['order_id'];
                    $rowData1['partner_basic_charge'] = $rowData1['discount_value'];
                    $rowData1['delivery_date'] = "";
                    $rowData1['file_received_date'] = date('Y-m-d', strtotime($this->input->post('file_received_date')));
                    if ($rowData1['city'] == '0') {
                        $rowData1['city'] = "";
                    }
                    if (!empty($rowData1['old_item_del_date'])) {
                        $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($rowData1['old_item_del_date']);
                        $rowData1['delivery_date'] = $dateObj2->format('Y-m-d');
                    }

                    $this->get_sheet_data($rowData1);
                    //set file data into global variable
                    $this->initialized_variable->set_post_buyback_order_details($rowData1);

                    //Insert/Update BB order details
                    $status1 = $this->buyback->check_action_order_details();
                    $this->initialized_variable->set_post_buyback_order_details(array());
                    if ($status1) {
                        
                    } else {

                        log_message("info", __METHOD__." Buyback Order Id neither update nor Inserted ".$rowData1['partner_order_id']);
                    }
                } else {
                   $response = array("code" => -247, "msg" => $this->Columfailed);
                   return $response;
                }
            }
            $response = array("code" => 247, "msg" => ($i));
            return $response;
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }
    }
    
     function retrevie_buyback_csv_file_data() {

        $file = $_FILES['file']['tmp_name'];

        $handle = fopen($file, "r");
        $escapeCounter = 0;

        while (($data = fgetcsv($handle, 6000, ",")) !== FALSE) {
            if ($escapeCounter > 0) {
                $this->get_bb_csv_data($data);
            } else {
               
                $s = $this->check_csv_column_exist($data);
                if (!$s) {
                   $response = array("code" => -247, "msg" => $this->Columfailed);
                   return $response;
                }
            }
            $escapeCounter = $escapeCounter + 1;
        }
        fclose($handle);
        
        $response = array("code" => 247, "msg" => $escapeCounter -1);
        return $response;
    }

    function get_bb_csv_data($data){
        $this->initialized_variable->set_post_buyback_order_details(array());
        
        $rowData['partner_id'] = 247024;
        $rowData['partner_name'] = "Amazon";
        $rowData['subcat'] = $data[1];
        $rowData['city'] = $data[3];
        $order_date = date('d-m-Y', strtotime($data[4]));
        $rowData['order_date'] = date("Y-m-d", strtotime($order_date));
        $rowData['partner_order_id'] = $data[5];
        $rowData['order_key'] = $data[6];
        $rowData['buyback_details'] = $data[6];
        $rowData['partner_charge'] = $data[7];
        $rowData['partner_basic_charge'] = $data[7];
        $rowData['partner_sweetner_charges'] = $data[8];
        $rowData['current_status'] = $data[12];
        if ($rowData['city'] == '0') {
            $rowData['city'] = "";
        }

        $rowData['tracking_id'] = "";
        $rowData['delivery_date'] = "";
        
        $rowData['file_received_date'] = date('Y-m-d', strtotime($this->input->post('file_received_date')));
        $this->get_sheet_data($rowData);
        //set file data into global variable
        $this->initialized_variable->set_post_buyback_order_details($rowData);

        //Insert/Update BB order details
        $status1 = $this->buyback->check_action_order_details();
        $this->initialized_variable->set_post_buyback_order_details(array());
        if ($status1) {

        } else {
            log_message("info", __METHOD__." Buyback Order Id neither update nor Inserted ".$rowData['partner_order_id']);
           
        }
    }
    
    function get_sheet_data($rowData) {
        $temp_arr = array();
        $temp_arr['file_name'] =  $_FILES["file"]["name"];
        $temp_arr['file_received_date'] = $rowData['file_received_date'];
        if(isset($rowData['order_day'])){
            $dateObj1 = PHPExcel_Shared_Date::ExcelToPHPObject($rowData['order_day']);
            $temp_arr['order_day'] = $dateObj1->format('Y-m-d');
        } else {
             $temp_arr['order_day'] = date("Y-m-d", strtotime($rowData['order_date']));
        }

        $temp_arr['partner_name'] = $rowData['partner_name'];
        $temp_arr['subcat'] = $rowData['subcat'];
        $temp_arr['order_id'] = $rowData['partner_order_id'];
        $temp_arr['city'] = $rowData['city'];
        $temp_arr['tracking_id'] = isset($rowData['tracking_id']) ? $rowData['tracking_id'] : '';
        $temp_arr['discount_value'] = $rowData['partner_basic_charge'];
        $temp_arr['order_status'] = $rowData['current_status'];
        $temp_arr['old_item_del_date'] = $rowData['delivery_date'];
        $temp_arr['buyback_details'] = $rowData['buyback_details'];
        $temp_arr['sweetner_value'] = isset($rowData['partner_sweetner_charges']) ? $rowData['partner_sweetner_charges'] : '';
        if($this->input->post('qc_svc')){
                $temp_arr['qc_svc'] = $this->input->post('qc_svc');
        }
        array_push($this->upload_sheet_data, $temp_arr);
        
    }

    function check_csv_column_exist($data){
        $message = "";
        $error = false;
        if ($data[1] !== 'BuyBack Category') {
            $message .= " BuyBack Category Column does not exist at position Two.<br/><br/>";
            $this->Columfailed .= " Buyback Category, ";
            $error = true;
        }
        
        if ($data[3] !== 'City') {
            $message .= " City Column does not exist at position Three.<br/><br/>";
            $this->Columfailed .= " City, ";
            $error = true;
        }
        
        if ($data[4] !== 'Order Date') {
            $message .= " Order Date Column does not exist at position Four.<br/><br/>";
            $this->Columfailed .= " Order Date, ";
            $error = true;
        }
        
        if ($data[5] !== 'Order Id') {
            $message .= " Order Id Column does not exist at position Fifth.<br/><br/>";
            $this->Columfailed .= " Order Id, ";
            $error = true;
        }
        
        if ($data[6] !== 'Used Item Info') {
            $message .= " Used Item Info Column does not exist at position Sixth.<br/><br/>";
            $this->Columfailed .= " Used Item Info, ";
            $error = true;
        }
        if ($data[7] !== 'Base Exchange Value') {
            $message .= "Base Exchange Value Column does not exist at position Seventh.<br/><br/>";
            $this->Columfailed .= " Exchange Offer Value, ";
            $error = true;
        }
        if ($data[8] !== 'Sponsored Value') {
            $message .= " Sponsored Value Column does not exist at position Eight.<br/><br/>";
            $this->Columfailed .= " Sponsored Value, ";
            $error = true;
        }
        
        if ($data[12] !== 'Order Status') {
            $message .= " Order Status Column does not exist at position Tenth.<br/><br/>";
            $this->Columfailed .= " Order Status, ";
            $error = true;
        }
        
        return $this->send_column_not_exit_mail($error);
    }
    
    private function send_column_not_exit_mail($error) {
        if ($error) {
            $message = " Please check and upload again.";
            $this->Columfailed .= " column does not exist.";
            $to =  empty($this->email_send_to)?(empty($this->session->userdata('official_email'))?ANUJ_EMAIL_ID:$this->session->userdata('official_email') . ", " . ANUJ_EMAIL_ID):$this->email_send_to;
            $cc = "";
            $agent_name = !empty($this->session->userdata('emp_name'))?$this->session->userdata('emp_name'):_247AROUND_DEFAULT_AGENT_NAME;
            $subject = "Failure! Buyback Order is uploaded by " .$agent_name ;
            $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $message, "",BUY_BACK_ORDER_FAILURE);
            return false;
        } else {
            return true;
        }
    }

    /**
     * @desc this is used to check column field exist or not 
     * @param Array $rowData1
     * @return boolean
     */
    function check_column_exist($rowData1) {
        $message = "";
        $error = false;
        
        if (!array_key_exists('buyback_details', $rowData1)) {
            $message .= " BuyBack Details Column does not exist.<br/><br/>";
            $this->Columfailed .= " Buyback, ";
            $error = true;
        }

//        if (!array_key_exists('sweetenervalue', $rowData1)) {
//            $message .= " Sweetener Value Column does not exist. <br/><br/>";
//            $this->Columfailed .= " Sweetener Value, ";
//            $error = true;
//        }
        
        if (!array_key_exists('order_id', $rowData1)) {
      
            $message .= " Order ID Column does not exist. <br/><br/>";
            $this->Columfailed .= "Order ID Column, ";
            $error = true;
        }
         
        if (!array_key_exists('discount_value', $rowData1)) {
       
            $message .= " Discount Value Column does not exist. <br/><br/>";
            $this->Columfailed .= " Discount Value Column, ";
            $error = true;
        }
        if (!array_key_exists('order_day', $rowData1)) {

            $message .= " Order day Column does not exist. <br/><br/>";
            $this->Columfailed .= " Order Day Column, ";
            $error = true;
        }
        if (!array_key_exists('city', $rowData1)) {
      
            $message .= " City Column does not exist. <br/><br/>";
            $this->Columfailed .= "City Column, ";
            $error = true;
        }
        if (!array_key_exists('orderstatus', $rowData1)) {
      
            $message .= " Order Status does not exist. <br/><br/>";
            $this->Columfailed .= "Order Status ";
            $error = true;
        }
         
        return $this->send_column_not_exit_mail($error);
    }

    /**
     * @desc This function is used to upload the charges list excel
     * @para, void
     * @return json
     */
    function proces_upload_bb_price_charges() {
        $return = $this->partner_utilities->validate_file($_FILES);
        if ($return == "true") {

            //Making process for file upload
            $pathinfo = pathinfo($_FILES["file"]["name"]);
            if ($pathinfo['extension'] == 'xlsx') {
                $inputFileName = $_FILES['file']['tmp_name'];
                $inputFileExtn = 'Excel2007';
            } else {
                $inputFileName = $_FILES['file']['tmp_name'];
                $inputFileExtn = 'Excel5';
            }

            try {
                $objReader = PHPExcel_IOFactory::createReader($inputFileExtn);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                die('Error loading file "' . $inputFileName . '": ' . $e->getMessage());
            }
            
            //get first sheet 
            $sheet = $objPHPExcel->getSheet(0);
            //get total number of rows
            $highestRow = $sheet->getHighestDataRow();
            //get total number of columns
            $highestColumn = $sheet->getHighestDataColumn();
            //get first row
            $headings = $sheet->rangeToArray('A1:' . $highestColumn . 1, NULL, TRUE, FALSE);
            
            //replace all unwanted character from headers
            $headings_new = array();
            foreach ($headings as $heading) {
                $heading = str_replace(array("/", "(", ")", " ", "."), "", $heading);
                array_push($headings_new, str_replace(array(" "), "_", $heading));
            }


            $sheetData = [];
            $this->Columfailed = "";
            $headings_new1 = array_map('strtolower', $headings_new[0]);
            //check all column exist
            $response = $this->check_bb_price_sheet_column_exist($headings_new1);
            if ($response) {
                $is_positive_margin = TRUE;
                $negative_margin_row = 0;
                for ($row = 2, $i = 0; $row <= $highestRow; $row++, $i++) {
                    //  Read a row of data into an array
                    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE, FALSE);
                    $newRowData = array_combine($headings_new1, $rowData[0]);
                    //check uploaded file has positive margin
                    $check_margin = $this->check_bb_price_margin($newRowData);
                    if($check_margin){
                        $dataToInsert = $this->set_charges_rows_data($newRowData);
                        array_push($sheetData, $dataToInsert);
                    }else{
                        $negative_margin_row = $row;
                        $is_positive_margin = FALSE;
                        break;
                    }
                    
                }
                
                if($is_positive_margin){
                    //insert data in batch
                    $is_insert = $this->bb_model->insert_charges_data_in_batch($sheetData);
                    if ($is_insert) {

                        //Adding Details in File_Uploads table as well
                        $this->miscelleneous->update_file_uploads($_FILES['file']['name'], $_FILES['file']['tmp_name'],_247AROUND_BB_PRICE_LIST,FILE_UPLOAD_SUCCESS_STATUS, "", "partner", AMAZON_SELLER_ID);
                        //Return success Message
                        $msg = "File Uploaded Successfully.";
                        $response = array("code" => '247', "msg" => $msg);
                        echo json_encode($response);
                    } else {
                        $msg = "Error!!! Please Try Again...";
                        $response = array("code" => '-247', "msg" => $msg);
                        echo json_encode($response);
                    }
                }else{
                    //Adding Details in File_Uploads table as well
                    $this->miscelleneous->update_file_uploads($_FILES['file']['name'], $_FILES['file']['tmp_name'],_247AROUND_BB_PRICE_LIST,FILE_UPLOAD_FAILED_STATUS, "", "partner", AMAZON_SELLER_ID);
                    $msg = "Error!!! Uploaded File has negative margin at row $negative_margin_row . Please correct this and upload again.";
                    $response = array("code" => '-247', "msg" => $msg);
                    echo json_encode($response);
                }
                
            } else {
                $this->miscelleneous->update_file_uploads($_FILES['file']['name'], $_FILES['file']['tmp_name'],_247AROUND_BB_PRICE_LIST,FILE_UPLOAD_FAILED_STATUS, "", "partner", AMAZON_SELLER_ID);
                $msg = "Error!!! Please Try Again...";
                $response = array("code" => '-247', "msg" => $this->Columfailed);
            }
        } else {
            $msg = $return['error'];
            $response = array("code" => '-247', "msg" => $msg);
            echo json_encode($response);
        }
    }

    /**
     * @desc This function is used to make final data from charges list excel to insert into the table
     * @para, $row array
     * @return $tmp array
     */
    private function set_charges_rows_data($row) {
        $tmp['partner_id'] = $row['partner_id'];
        $tmp['cp_id'] = $row['cp_id'];
        $tmp['service_id'] = $row['service_id'];
        $tmp['category'] = $row['categorysize'];
        $tmp['brand'] = $row['brand'];
        $tmp['physical_condition'] = $row['physicalcondition'];
        $tmp['working_condition'] = $row['workingcondition'];
        $tmp['city'] = $row['city'];
        $tmp['order_key'] = str_replace(array("_",":"), "", $row['key']);
        $tmp['partner_basic'] = $row['partner_basic'];
        $tmp['partner_tax'] = $row['partner_tax'];
        $tmp['partner_total'] = $row['partner_total'];
        $tmp['cp_basic'] = $row['cp_basic'];
        $tmp['cp_tax'] = $row['cp_tax'];
        $tmp['cp_total'] = $row['cp_total'];
        $tmp['around_basic'] = $row['around_basic'];
        $tmp['around_tax'] = $row['around_tax'];
        $tmp['around_total'] = $row['around_total'];
        $tmp['visible_to_partner'] = $row['visible_to_partner'];
        $tmp['visible_to_cp'] = $row['visible_to_cp'];
        $tmp['create_date'] = date("Y-m-d H:i:s");

        return $tmp;
    }

    public function upload_file_history($file_type) {
        $data = $this->reporting_utils->get_uploaded_file_history($file_type);
        print_r(json_encode($data, TRUE));
    }
    
    function check_bb_price_sheet_column_exist($rowData){
        $message = "";
        $error = false;
        
        if (!in_array('partner_id', $rowData)) {
            $message .= " Partner ID Column does not exist.<br/><br/>";
            $this->Columfailed .= " Partner ID, ";
            $error = true;
        }
        
        if (!in_array('cp_id', $rowData)) {
      
            $message .= " CP Id Column does not exist. <br/><br/>";
            $this->Columfailed .= "CP Id , ";
            $error = true;
        }
        
        if (!in_array('service_id', $rowData)) {

            $message .= " Service Id Column does not exist. <br/><br/>";
            $this->Columfailed .= " Service Id , ";
            $error = true;
        }
        if (!in_array('categorysize', $rowData)) {
      
            $message .= " Category Size Column does not exist. <br/><br/>";
            $this->Columfailed .= "Category Size , ";
            $error = true;
        }
        if (!in_array('brand', $rowData)) {
      
            $message .= " Brand Column does not exist. <br/><br/>";
            $this->Columfailed .= "Service Category ";
            $error = true;
        }
        if (!in_array('physicalcondition', $rowData)) {
      
            $message .= " Physical Condition Column does not exist. <br/><br/>";
            $this->Columfailed .= "Physical Condition , ";
            $error = true;
        }
        if (!in_array('workingcondition', $rowData)) {
      
            $message .= " Working Condition Column does not exist. <br/><br/>";
            $this->Columfailed .= "Working Condition,";
            $error = true;
        }
        if (!in_array('city', $rowData)) {
      
            $message .= " City Column does not exist. <br/><br/>";
            $this->Columfailed .= "City , ";
            $error = true;
        }
        if (!in_array('key', $rowData)) {
      
            $message .= " Key Column does not exist. <br/><br/>";
            $this->Columfailed .= "Key,";
            $error = true;
        }
        if (!in_array('partner_basic', $rowData)) {
      
            $message .= " Partner Basic Column does not exist. <br/><br/>";
            $this->Columfailed .= "Partner Basic , ";
            $error = true;
        }
        if (!in_array('partner_tax', $rowData)) {
      
            $message .= " Product Tax Column does not exist. <br/><br/>";
            $this->Columfailed .= "Product Tax,";
            $error = true;
        }
        if (!in_array('partner_total', $rowData)) {
      
            $message .= " Partner Total Column does not exist. <br/><br/>";
            $this->Columfailed .= "Partner Total , ";
            $error = true;
        }
        if (!in_array('cp_basic', $rowData)) {
      
            $message .= " CP Basic Column does not exist. <br/><br/>";
            $this->Columfailed .= "CP Basic,";
            $error = true;
        }
        if (!in_array('cp_tax', $rowData)) {
      
            $message .= " CP Total Column does not exist. <br/><br/>";
            $this->Columfailed .= "CP Total , ";
            $error = true;
        }
        if (!in_array('cp_total', $rowData)) {
      
            $message .= " CP Total Column does not exist. <br/><br/>";
            $this->Columfailed .= "CP Total,";
            $error = true;
        }
        if (!in_array('around_basic', $rowData)) {
      
            $message .= " Around Basic Column does not exist. <br/><br/>";
            $this->Columfailed .= "Around Basic,";
            $error = true;
        }
        if (!in_array('around_tax', $rowData)) {
      
            $message .= " Around Tax Column does not exist. <br/><br/>";
            $this->Columfailed .= "Around Tax , ";
            $error = true;
        }
        if (!in_array('around_total', $rowData)) {
      
            $message .= " Around Total Column does not exist. <br/><br/>";
            $this->Columfailed .= "Around Total,";
            $error = true;
        }
        if (!in_array('visible_to_partner', $rowData)) {
      
            $message .= " Visible To Partner Column does not exist. <br/><br/>";
            $this->Columfailed .= "Visible To Partner,";
            $error = true;
        }
        if (!in_array('visible_to_cp', $rowData)) {
      
            $message .= " Visible To CP Column does not exist. <br/><br/>";
            $this->Columfailed .= "Visible To CP , ";
            $error = true;
        }
         
        if ($error) {
            $message .= " Please check and upload again.";
            $this->Columfailed .= " column does not exist.";
            $to = $this->session->userdata('official_email');
            $cc = DEVELOPER_EMAIL;
            $subject = "Failed!!! Buyabck Price Sheet File uploaded by " . $this->session->userdata('emp_name');
            $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $message, "",BUY_BACK_PRICE_SHEET_FAILURE);
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * @desc used to show the view to upload highest quote data
     * @param void
     * @return void
     */
    function highest_quote_price_sheet_upload() {

        $data['saas_flag'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'),$data);
        $this->load->view('buyback/highest_quote_price_sheet_upload');
        $this->load->view('dashboard/dashboard_footer');
    }
    
    /**
     * @desc used to process the uploaded highest price quote sheet
     * @param void
     * @return $response JSON
     */
    function proces_upload_bb_price_quote(){
        log_message('info', __FUNCTION__);
        $return = $this->partner_utilities->validate_file($_FILES);
        if ($return == "true") {
            //Making process for file upload
            $pathinfo = pathinfo($_FILES["file"]["name"]);
            if ($pathinfo['extension'] == 'xlsx') {
                $inputFileName = $_FILES['file']['tmp_name'];
                $inputFileExtn = 'Excel2007';
            } else {
                $inputFileName = $_FILES['file']['tmp_name'];
                $inputFileExtn = 'Excel5';
            }
            
            //Processing File
            $response = $this->process_price_quote_upload_file($inputFileName, $inputFileExtn);
            
            if(!empty($response)){
                //send mail 
                $template = $this->booking_model->get_booking_email_template("buyback_price_sheet_with_quote");
                $body = $template[0];
                $to = NITS_ANUJ_EMAIL_ID.",".$this->session->userdata('official_email');
                $from = $template[2];
                $cc = $template[3];
                $subject = $template[4];
                $attachment = $response;
                
                $sendmail = $this->notify->sendEmail($from, $to, $cc, "", $subject, $body, $attachment,'buyback_price_sheet_with_quote');
                
                //check if this file is uploaded by email
                $email_message_id = !($this->input->post('email_message_id') === NULL)?$this->input->post('email_message_id'):'';
                
                if ($sendmail) {
                    log_message('info', __FUNCTION__ . ' Mail has been send successfully');
                    unlink($response);
                    $this->miscelleneous->update_file_uploads($_FILES["file"]["name"], $_FILES['file']['tmp_name'],_247AROUND_BB_PRICE_QUOTE,FILE_UPLOAD_SUCCESS_STATUS,$email_message_id, "partner", AMAZON_SELLER_ID);
                    $msg = "File Created Successfully And Mailed To Registed Email";
                    $response = array("code" => '247', "msg" => $msg);
                } else {
                    log_message('info', __FUNCTION__ . 'Error in Sending Mail');
                    $this->miscelleneous->update_file_uploads($_FILES["file"]["name"], $_FILES['file']['tmp_name'],_247AROUND_BB_PRICE_QUOTE,FILE_UPLOAD_FAILED_STATUS,$email_message_id, "partner", AMAZON_SELLER_ID);
                    $msg = "Error In sending Email";
                    $response = array("code" => '-247', "msg" => $msg);
                }
                
                //return response
                echo json_encode($response);
            } else {
                $msg = "Something went wrong!!! Please Try Again...";
                $response = array("code" => '-247', "msg" => $msg);
                echo json_encode($response);
            }
        }else{
            $msg = $return['error'];
            $response = array("code" => '-247', "msg" => $msg);
            echo json_encode($response);
        }
    }
    
    /**
     * @desc used to extract the uploaded file information
     * @param $inputFileName string
     * @param $inputFileExtn string
     * @return $new_price_sheet string
     */
    function process_price_quote_upload_file($inputFileName, $inputFileExtn){
        log_message('info', __FUNCTION__);
        try {
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
                
                //process the file data to compare 
                $this->do_action_on_file_data($sheet, $highestRow, $highestColumn, $headings_new);
            }
            
            unset($objPHPExcel,$objReader);
            
            $new_price_sheet = $this->update_price_sheet_with_new_quote();
            
            return $new_price_sheet;
            
        }catch (Exception $e) {
            die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }
    }
    
    /**
     * @desc used to check if uploaded price sheet exist in our database
     * @param $sheet object
     * @param $highestRow string
     * @param $highestColumn string
     * @param $headings_new array
     * @return void
     */
    function do_action_on_file_data($sheet, $highestRow, $highestColumn, $headings_new){
        log_message('info', __FUNCTION__);
        for ($row = 2, $i = 0; $row <= $highestRow; $row++, $i++) {
            $rowData_array = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowData = array_combine($headings_new[0], $rowData_array[0]);
            
            $order_key_need_to_check = strtolower(str_replace(array("_",":"," ","-","|","/"), "", array_shift($rowData)));
           
            //make order key and city arrray and push this data to array $this->price_quote_data for comparison
            foreach($rowData as $city => $price){
                $tmp_arr = array();
                $tmp_arr['order_key'] = $order_key_need_to_check;
                $tmp_arr['city'] = $city;
                $tmp_arr['new_price_quote'] = $price;

                array_push($this->price_quote_data, $tmp_arr);
            }
        }
    }
    
    /**
     * @desc used to compare the data with our database and update the price sheet
     * @param void
     * @return $response
     */
    function update_price_sheet_with_new_quote() {
        log_message('info', __FUNCTION__);
        $post_data = array('length' => 1,
            'start' => 0,
            'file_type' => _247AROUND_BB_PRICE_LIST,
            'result' => FILE_UPLOAD_SUCCESS_STATUS);
        //get the latest uploaded price sheet
        $latest_upload_price_sheet_file_name = $this->reporting_utils->get_uploaded_file_history($post_data)[0]->file_name;
        $s3_bucket_file = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . urlencode($latest_upload_price_sheet_file_name);
        //copy the uploaded file to our system
        if (file_exists(TMP_FOLDER . $latest_upload_price_sheet_file_name)) {
            unlink(TMP_FOLDER . $latest_upload_price_sheet_file_name);
            //get signature file from s3 and save it to server
            copy($s3_bucket_file, TMP_FOLDER . $latest_upload_price_sheet_file_name);
        } else {
            //get signature file from s3 and save it to server
            copy($s3_bucket_file, TMP_FOLDER . $latest_upload_price_sheet_file_name);
        }

        //start adding new cell value on actual price sheet
        if (pathinfo(TMP_FOLDER . $latest_upload_price_sheet_file_name)['extension'] == 'xlsx') {
            $inputFileName1 = TMP_FOLDER . $latest_upload_price_sheet_file_name;
            $inputFileExtn1 = 'Excel2007';
        } else {
            $inputFileName1 = TMP_FOLDER . $latest_upload_price_sheet_file_name;
            $inputFileExtn1 = 'Excel5';
        }
        $objReader1 = PHPExcel_IOFactory::createReader($inputFileExtn1);
        $objPHPExcel1 = $objReader1->load($inputFileName1);

        //get first sheet 
        $sheet = $objPHPExcel1->getSheet(0);
        //get total number of rows
        $highestRow = $sheet->getHighestDataRow();
        
        $order_key_city_arr = array_map(function (array $elem) { unset($elem['new_price_quote']);return $elem;},$this->price_quote_data);
        
        for ($row = 2, $i = 0; $row <= $highestRow; $row++, $i++) {
            $order_key = strtolower(str_replace(array("_",":"," ","-","|","/"), "", $sheet->getCell('K' . $row)->getValue()));
            $city = strtolower($sheet->getCell('J' . $row)->getValue());
            $search = array_keys($order_key_city_arr, array("order_key" => $order_key, "city" => $city));
            if(!empty($search)){
                $sheet->setCellValue('Y' . $row, $this->price_quote_data[$search[0]]['new_price_quote']);
            }
        }
        
        // Write the file
        $file_name = TMP_FOLDER . "updated_price_sheet_".date('Y_m_d_H_i_s').".xls";
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel1, 'Excel5');
        $objWriter->save($file_name);
        
        if(file_exists($file_name)){
            $response = $file_name;
        }else{
            $response = FALSE;
        }
        
        unlink(TMP_FOLDER . $latest_upload_price_sheet_file_name);
        
        return $response;
    }
    
    
    /**
     * @desc This function is used to check buyback price sheet margin. In the file,all rows should have positive margin
     * @param $data array()
     * @return $flag boolean
     */
    function check_bb_price_margin($data){
        $flag = true;
//        $flag = false;
//        if($data['cp_total'] > $data['partner_total'] && $data['around_total'] > 0){
//            $flag = true;
//        }
//        
        return $flag;
    }
    
    /**
     * @desc used to show the view to upload highest quote data
     * @param void
     * @return void
     */
    function upload_reimbursement_file() {
        $data['saas_flag'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'),$data);
        $this->load->view('buyback/reimbursement_file_upload_form');
        $this->load->view('dashboard/dashboard_footer');
    }
    
    /**
     * @desc used to process the reimbursement po file
     * @param void
     * @return $response JSON
     */
    function process_reimbursement_file(){
        log_message('info', __FUNCTION__);
        $return = $this->partner_utilities->validate_file($_FILES);
        if ($return == "true") {
            //Making process for file upload
            $pathinfo = pathinfo($_FILES["file"]["name"]);
            if ($pathinfo['extension'] == 'xlsx') {
                $inputFileName = $_FILES['file']['tmp_name'];
                $inputFileExtn = 'Excel2007';
            } else {
                $inputFileName = $_FILES['file']['tmp_name'];
                $inputFileExtn = 'Excel5';
            }
            
            //Processing File
            $response = $this->process_reimbursement_file_data($inputFileName, $inputFileExtn);
             log_message('info', __FUNCTION__."response".$response);
            if(!empty($response)){
                log_message('info', __FUNCTION__ . ' Mail has been send successfully');
                $this->miscelleneous->update_file_uploads($_FILES["file"]["name"], $_FILES['file']['tmp_name'],_247AROUND_BB_REIMBURSMENT_FILE, FILE_UPLOAD_SUCCESS_STATUS, "", "partner", AMAZON_SELLER_ID);
                $msg = "File Created Successfully And Mailed To Registed Email";
                $response = array("code" => '247', "msg" => $msg);
                
                //return response
                echo json_encode($response);
            } else {
                log_message('info', __FUNCTION__ . 'Something went wrong!!! Please Try Again...');
                $this->miscelleneous->update_file_uploads($_FILES["file"]["name"], $_FILES['file']['tmp_name'],_247AROUND_BB_REIMBURSMENT_FILE, FILE_UPLOAD_FAILED_STATUS, "", "partner", AMAZON_SELLER_ID);
                $msg = "Something went wrong!!! Please Try Again...";
                $response = array("code" => '-247', "msg" => $msg);
                echo json_encode($response);
            }
        }else{
            $msg = $return['error'];
            $response = array("code" => '-247', "msg" => $msg);
            echo json_encode($response);
        }
    }
    
    /**
     * @desc used to extract the uploaded file information
     * @param $inputFileName string
     * @param $inputFileExtn string
     * @return $new_price_sheet string
     */
    function process_reimbursement_file_data($inputFileName, $inputFileExtn){
        log_message('info', __FUNCTION__);
        try {
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
                
                //process reimbursement file
                $response = $this->do_action_reimbursement_file_data($sheet, $highestRow, $highestColumn, $headings_new);
            }
            
            unset($objPHPExcel,$objReader);
            
            return $response;
            
        }catch (Exception $e) {
            die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }
    }
    
    /**
     * @desc used to check if uploaded price sheet exist in our database
     * @param $sheet object
     * @param $highestRow string
     * @param $highestColumn string
     * @param $headings_new array
     * @return void
     */
    function do_action_reimbursement_file_data($sheet, $highestRow, $highestColumn, $headings_new){
        log_message('info', __FUNCTION__);
        $appData = array();
        $appService = array();
        $invoice_orders = array();
        $invalid_orders = array();
        $PO_Number = "";
        for ($row = 2, $i = 0; $row <= $highestRow; $row++, $i++) {
            $rowData_array = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowData = array_combine($headings_new[0], $rowData_array[0]);
            if(!$PO_Number){
                $PO_Number = $rowData['ponumber'];
            }
            
            $app_array = $this->bb_model->get_bb_order_appliance_details(array("partner_order_id"=> $rowData['orderid']), "services, CASE WHEN (bb_unit.service_id = '"._247AROUND_TV_SERVICE_ID."') THEN (8528) 
                WHEN (bb_unit.service_id = '"._247AROUND_AC_SERVICE_ID."') THEN (8415)
                WHEN (bb_unit.service_id = '"._247AROUND_WASHING_MACHINE_SERVICE_ID."') THEN (8450)
                WHEN (bb_unit.service_id = '"._247AROUND_REFRIGERATOR_SERVICE_ID."') THEN (8418) ELSE '' END As hsn_code");
            if(!empty($app_array)){
                $service_name = $app_array[0]['services'];
                array_push($invoice_orders, array($rowData['orderid'] => $rowData['reimbursementvalue']));
                $appData[$service_name][] = $rowData['reimbursementvalue'];
                $appService[$service_name] = $app_array[0]['hsn_code'];
            }
            else{
                array_push($invalid_orders, $rowData['orderid']);
            }
        }
        $sd = $ed = $invoice_date = date("Y-m-d");
        $invoice_id = $this->invoice_lib->create_invoice_id("Around");
        $vendor_data = $this->partner_model->getpartner_details("gst_number, "
                            . "state,address as company_address, owner_phone_1,"
                            . "company_name, pincode, "
                            . "district, invoice_email_to, invoice_email_cc", array('partners.id'=> AMAZON_SELLER_ID))[0];
        $key = 0;
        foreach ($appData as $row => $value) {
                $amount = array_sum($value);
                $gst_rate = 0;
                $data[$key]['description'] =  $row;
                //$tax_charge = $this->booking_model->get_calculated_tax_charge($amount, $gst_rate);
                $data[$key]['taxable_value'] = sprintf("%.2f", $amount);
                $data[$key]['product_or_services'] = "Product";
                $data[$key]['gst_number'] = "";
                $data[$key]['company_name'] = $vendor_data['company_name'];
                $data[$key]['company_address'] = $vendor_data['company_address']; 
                $data[$key]['booking_id'] = "";
                $data[$key]['district'] = $vendor_data['district'];
                $data[$key]['pincode'] = $vendor_data['pincode'];
                $data[$key]['state'] = $vendor_data['state'];
                $data[$key]['rate'] = sprintf("%.2f", ($amount/count($value)));
                $data[$key]['qty'] = count($value);
                $data[$key]['hsn_code'] = $appService[$row];
                $data[$key]['gst_rate'] = $gst_rate;
                $data[$key]['owner_phone_1'] = $vendor_data['owner_phone_1'];
                $key++;
        }
        if(!empty($data)){
            $invoice_type = "Tax Invoice";
            $is_customer = true;
            $response = $this->invoices_model->_set_partner_excel_invoice_data($data, $sd, $ed, $invoice_type, $invoice_date, $is_customer, $vendor_data['state']);
            $response['meta']['invoice_id'] = $invoice_id;
            $response['meta']['reference_invoice_id'] = $PO_Number;
            $response['meta']['booking_id'] = "";
            $response['meta']['customer_name'] = $vendor_data['company_name'];
            $response['meta']['customer_address'] = $vendor_data['company_address'];
            $response['meta']['customer_phone_number'] = $vendor_data['owner_phone_1'];
            $response['meta']['owner_phone_1'] = $vendor_data['owner_phone_1'];
            $response['meta']['invoice_template'] = "Buyback-v1.xlsx";
            
            $status = $this->invoice_lib->send_request_to_create_main_excel($response, "final", true);
            if($status){

                $convert = $this->invoice_lib->convert_invoice_file_into_pdf($response, "final", true, true);
                $output_pdf_file_name = $convert['main_pdf_file_name'];
                $response['meta']['invoice_file_main'] = $output_pdf_file_name;
                $response['meta']['copy_file'] = $convert['copy_file'];
                $response['meta']['invoice_file_excel'] = $invoice_id.".xlsx";

                $this->invoice_lib->upload_invoice_to_S3($invoice_id, false);
            }
            
            $response['meta']['invoice_id'] = $invoice_id;
            $response['meta']['vertical'] = BUYBACK_VERTICAL;
            $response['meta']['category'] = EXCHANGE;
            $response['meta']['sub_category'] = REIMBURSEMENT;
            $response['meta']['accounting'] = 1;
            
            $this->invoice_lib->insert_invoice_breackup($response);
            $invoice_details = $this->invoice_lib->insert_vendor_partner_main_invoice($response, "A", "Parts", _247AROUND_PARTNER_STRING, AMAZON_SELLER_ID, $convert, $this->session->userdata('id'), HSN_CODE);
            $invoice_details['reference_invoice_id'] = $PO_Number;
            $vendor_partner_invoice_id = $this->invoices_model->insert_new_invoice($invoice_details);
            if($vendor_partner_invoice_id){
                foreach ($invoice_orders as $key => $value) {
                   $this->bb_model->update_bb_unit_details(array("partner_order_id" => array_keys($value)[0]), array("partner_discount" => array_values($value)[0], "partner_reimbursement_invoice" => $invoice_id));
                }
                
                //send mail 
                $template = $this->booking_model->get_booking_email_template(BUYBACK_REIMBURESE_PO_UPLOADED);
                $body = $template[0];
                $to = $template[1].",".$this->session->userdata('official_email');
                $from = $template[2];
                $cc = $template[3];
                $subject = $template[4];
                $attachment = TMP_FOLDER.$output_pdf_file_name;
                
                $cmd = "curl " . S3_WEBSITE_URL . "invoices-excel/" . $output_pdf_file_name . " -o " . TMP_FOLDER.$output_pdf_file_name;
                exec($cmd); 

                $this->notify->sendEmail($from, $to, $cc, "", $subject, $body, $attachment, BUYBACK_REIMBURESE_PO_UPLOADED, "", "");

                unlink(TMP_FOLDER.$output_pdf_file_name);


                unlink(TMP_FOLDER.$invoice_id.".xlsx");
                unlink(TMP_FOLDER."copy_".$invoice_id.".xlsx");
                
                return true;
            }
            else{
                return false;
            }
        }
    }
}
