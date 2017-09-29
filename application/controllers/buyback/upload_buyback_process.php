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

        $this->load->model("partner_model");
        $this->load->model('reporting_utils');

        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            echo PHP_EOL . 'Terminal Access Not Allowed' . PHP_EOL;
            redirect(base_url() . "employee/login");
        }
    }

    function index() {

        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('buyback/order_details_file_upload');
        $this->load->view('dashboard/dashboard_footer');
    }
    
     function price_sheet_upload() {

        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('buyback/price_sheet_upload');
        $this->load->view('dashboard/dashboard_footer');
    }
    
    function buyback_file_processing(){
        $pathinfo = pathinfo($_FILES["file"]["name"]);
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
        $insert_id = $this->bb_model->insert_bb_sheet_data($this->upload_sheet_data);
        if ($insert_id) {
            log_message('info', "Buyback Sheet Data Inserted");
        } else {
           log_message('info', "Error In Inserting Buyback Sheet Data");
        }
        
        if ($response['code'] == -247) {
            echo $response;
        } else {
           
            $order_file = $_FILES["file"]["name"];
            $to = NITS_ANUJ_EMAIL_ID . "," . ADIL_EMAIL_ID;
            $cc = "abhaya@247around.com";

            $message = "";
            $subject = "Buyback Order is uploaded by " . $this->session->userdata('employee_id');

            $message .= "Order File Name ---->" . $order_file . "<br/><br/>";
            $message .= "Total lead  ---->" . $response['msg'] . "<br/><br/>";

            $message .= "Total Delivered ---->" . ($this->initialized_variable->delivered_count() == -1)? 0: $this->initialized_variable->delivered_count() . "<br/><br/>";
            $message .= "Total Inserted ---->" . ($this->initialized_variable->total_inserted()) . "<br/><br/>";
            $message .= "Total Updated ---->" . ($this->initialized_variable->total_updated() == -1)? 0: $this->initialized_variable->total_updated() . "<br/><br/>";
            $message .= "Total Not Assigned ---->" . ($this->initialized_variable->not_assigned_order()) . "<br/><br/>";
            $message .= "Please check below orders, these were neither inserted nor updated: <br/><br/><br/>";
            $message .= $this->table->generate();

            $this->notify->sendEmail("buyback@247around.com", $to, $cc, "", $subject, $message, "");

            $this->upload_file_to_S3($order_file, _247AROUND_BB_ORDER_LIST, $_FILES['file']['tmp_name']);
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

                    $rowData1['partner_id'] = 247024;
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

                        $this->table->add_row($rowData1['partner_order_id']);
                    }
                } else {
                   $response = array("code" => -247, "msg" => $this->Columfailed);
                   return $response;
                }
            }
            $response = array("code" => 247, "msg" => $i);
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
        
        $response = array("code" => 247, "msg" => $escapeCounter);
        return $response;
    }

    function get_bb_csv_data($data){
        $this->initialized_variable->set_post_buyback_order_details(array());
        
        $rowData['partner_id'] = 247024;
        $rowData['partner_name'] = "Amazon";
        $rowData['subcat'] = $data[1];
        $rowData['city'] = $data[2];
        $rowData['order_date'] = date('Y-m-d', strtotime($data[3]));
        $rowData['partner_order_id'] = $data[4];
        $rowData['order_key'] = $data[5];
        $rowData['buyback_details'] = $data[5];
        $rowData['partner_charge'] = $data[6];
        $rowData['partner_basic_charge'] = $data[6];
        $rowData['partner_sweetner_charges'] = $data[7];
        $rowData['current_status'] = $data[9];
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

            $this->table->add_row($rowData['partner_order_id']);
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
        
        if ($data[2] !== 'City') {
            $message .= " City Column does not exist at position Three.<br/><br/>";
            $this->Columfailed .= " City, ";
            $error = true;
        }
        
        if ($data[3] !== 'Order Date') {
            $message .= " Order Date Column does not exist at position Four.<br/><br/>";
            $this->Columfailed .= " Order Date, ";
            $error = true;
        }
        
        if ($data[4] !== 'Order Id') {
            $message .= " Order Id Column does not exist at position Fifth.<br/><br/>";
            $this->Columfailed .= " Order Id, ";
            $error = true;
        }
        
        if ($data[5] !== 'Used Item Info') {
            $message .= " Used Item Info Column does not exist at position Sixth.<br/><br/>";
            $this->Columfailed .= " Used Item Info, ";
            $error = true;
        }
        if ($data[6] !== 'Exchange Offer Value') {
            $message .= " Exchange Offer Value Column does not exist at position Seventh.<br/><br/>";
            $this->Columfailed .= " Exchange Offer Value, ";
            $error = true;
        }
        if ($data[7] !== 'Sponsored Value') {
            $message .= " Sponsored Value Column does not exist at position Eight.<br/><br/>";
            $this->Columfailed .= " Sponsored Value, ";
            $error = true;
        }
        
        if ($data[9] !== 'Order Status') {
            $message .= " Order Status Column does not exist at position Tenth.<br/><br/>";
            $this->Columfailed .= " Order Status, ";
            $error = true;
        }
        
        return $this->send_column_not_exit_mail($error);
    }
    
    private function send_column_not_exit_mail($error) {
        if ($error) {
            $message .= " Please check and upload again.";
            $this->Columfailed .= " column does not exist.";
            $to = NITS_ANUJ_EMAIL_ID . "," . ADIL_EMAIL_ID;
            $cc = "abhaya@247around.com";
            $subject = "Failure! Buyback Order is uploaded by " . $this->session->userdata('employee_id');
            $this->notify->sendEmail("buyback@247around.com", $to, $cc, "", $subject, $message, "");
            return false;
        } else {
            return true;
        }
    }
    
    function upload_file_to_S3($file_name, $file_type, $file_path){
        $data['file_name'] = $file_name;
        $data['file_type'] = $file_type;
        $data['agent_id'] = $this->session->userdata('id');
        $insert_id = $this->partner_model->add_file_upload_details($data);
        if ($insert_id) {
            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "vendor-partner-docs/" . $file_name;
            $this->s3->putObjectFile($file_path, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
        }
        return true;
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

                for ($row = 2, $i = 0; $row <= $highestRow; $row++, $i++) {
                    //  Read a row of data into an array
                    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE, FALSE);
                    $newRowData = array_combine($headings_new1, $rowData[0]);
                    $dataToInsert = $this->set_charges_rows_data($newRowData);
                    array_push($sheetData, $dataToInsert);
                }
                //insert data in batch
                $is_insert = $this->bb_model->insert_charges_data_in_batch($sheetData);
                if ($is_insert) {

                    //Adding Details in File_Uploads table as well
                    $data['file_name'] = $_FILES['file']['name'];
                    $data['file_type'] = _247AROUND_BB_PRICE_LIST;
                    $data['agent_id'] = $this->session->userdata('id');
                    $insert_id = $this->partner_model->add_file_upload_details($data);
                    if ($insert_id) {
                        //Upload files to AWS
                        $bucket = BITBUCKET_DIRECTORY;
                        $directory_xls = "vendor-partner-docs/" . $_FILES['file']['name'];
                        $this->s3->putObjectFile($inputFileName, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                    }

                    //Return success Message
                    $msg = "File Uploaded Successfully.";
                    $response = array("code" => '247', "msg" => $msg);
                    echo json_encode($response);
                } else {
                    $msg = "Error!!! Please Try Again...";
                    $response = array("code" => '-247', "msg" => $msg);
                    echo json_encode($response);
                }
            } else {
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
            $subject = "Failed!!! Buyabck Price Sheet File uploaded by " . $this->session->userdata('employee_id');
            $this->notify->sendEmail("noreply@247around.com", $to, $cc, "", $subject, $message, "");
            return false;
        } else {
            return true;
        }
    }
    
}
