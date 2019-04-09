<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}


define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** PHPExcel_IOFactory */
//require_once dirname(__FILE__) . '/../Classes/PHPExcel/IOFactory.php';

error_reporting(E_ALL);
ini_set('memory_limit', '-1');

ini_set('display_errors', '1');
ini_set('max_execution_time', 36000); //3600 seconds = 60 minutes

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

require_once BASEPATH . 'libraries/spout-2.4.3/src/Spout/Autoloader/autoload.php';

class service_centre_charges extends CI_Controller {

    var $dataToInsert = [];
    var $currentSheetPartnerId = '';
    var $Columfailed = "";
    
    function __Construct() {
        parent::__Construct();
        $this->load->helper(array('form', 'url'));
        $this->load->helper('download');

        $this->load->library('form_validation');
        $this->load->library('s3');
        $this->load->library('PHPReport');
        $this->load->library('partner_sd_cb');
        $this->load->library('partner_utilities');
        $this->load->library('notify');
        $this->load->library("miscelleneous");
        $this->load->library('table');

        $this->load->model('user_model');
        $this->load->model('booking_model');
        $this->load->model('partner_model');
        $this->load->model('reporting_utils');
        $this->load->model('service_centre_charges_model');

        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            redirect(base_url() . "employee/login");
        }
    }

    /**
     *  @desc : This function is to get a form to upload service center charges from excel
     *  @param : void
     *  @return : void
     */
    public function index() {
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/upload_service_centre_charges_excel');
    }

    /**
     *  @desc : This function is to add service center charges from excel
     *  @param : void
     *  @return : all the charges added to view
     */
    public function add_service_centre_chrges_from_excel() {
        if (!empty($_FILES['file']['name'])) {
            $pathinfo = pathinfo($_FILES["file"]["name"]);

            if ($pathinfo['extension'] == 'xlsx') {
                if ($_FILES['file']['size'] > 0) {

                    $inputFileName = $_FILES['file']['tmp_name'];
                }
            }
        }

        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
            /**  Advise the Reader that we only want to load cell data  * */
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }

        //  Get worksheet dimensions
        $sheet = $objPHPExcel->setActiveSheetIndexbyName('Sheet1');
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

//        echo "highest row: ", $highestRow, EOL;
//        echo "highest col: ", $highestColumn, EOL;
//        echo "highest col index: ", $highestColumnIndex, EOL;
        $sheet = $objPHPExcel->getSheet(0);
        $headings = $sheet->rangeToArray('A1:' . $highestColumn . 1, NULL, TRUE, FALSE);

        $headings_new = array();
        foreach ($headings as $heading) {
            array_push($headings_new, str_replace(" ", "_", $heading));
        }

//        $booking = array();
        for ($row = 2, $i = 0; $row <= $highestRow; $row++, $i++) {
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowData[0] = array_combine($headings_new[0], $rowData[0]);


            //Insert service center
            $charges = $rowData[0];

            $lead_details['id'] = $this->service_centre_charges_model->insert_service_centre_charges($charges);

            //Make an array to store all the data, to display all the entered data in view
            $to_display[] = $rowData[0];
        }

        $data['booking'] = $to_display;

        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/service_centre_charges_summary', $data);
    }

    /**
     *  @desc : This function is to display service center charges
     *  @param : void
     *  @return : all the services to view
     */
    public function display_service_centre_charges() {
        $services = $this->booking_model->selectservice();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/service_centre_price_list', array('services' => $services));
    }

    /**
     *  @desc : This function called through ajax is to display service center charges for particular service
     *  @param : service id
     *  @return : all the service center charges for particular service added to view
     */
    public function display_charges_for_particular_appliance($service_id) {
        $result = $this->service_centre_charges_model->get_prices_for_particular_appliance($service_id);

        foreach ($result as $prices) {
            echo "<tr><td width='10%;'>" . $prices->category . "</td>
                 <td width='10%;'>" . $prices->capacity . "</td>
                 <td width='15%;'>" . $prices->service_category . "</td>
                 <td width='5%;'>" . $prices->total_charges . "</td>
                 <td width='5%;'>" . $prices->vendor_price . "</td>
                 <td width='5%;'>" . $prices->around_markup . "</td>
                 <td width='5%;'>" . $prices->service_charges . "</td>
                 <td width='5%;'>" . $prices->service_tax . "</td>
                 </tr>";
        }
    }

    /**
     *  @desc  : This is used to load view of excel file for service price list and tax rate
     *  @param : void
     *  @return : load view of Excel
     */
    function upload_excel_form($data = "") {
        $view['data'] = $data;
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/upload_service_price', $view);
    }

    /**
     *  @desc  : This is used to upload service price from excel
     *  @param : void
     *  @return : void
     */
    function upload_service_price_from_excel() {
        $return = $this->partner_utilities->validate_file($_FILES);
        if ($return == "true") {
            //Logging
            log_message('info', __FUNCTION__ . ' Processing of Service Price List Excel File started');
            
            $tmpFile = $_FILES['file']['tmp_name'];
            //Processing File
            $response = $this->process_upload_service_price_file($tmpFile);
            
            //Adding Details in File_Uploads table as well
            if($response['status']){
                //save file and upload on s3
                $this->miscelleneous->update_file_uploads($_FILES['file']['name'],$tmpFile, _247AROUND_SF_PRICE_LIST,FILE_UPLOAD_SUCCESS_STATUS);
                $userSession = array('success' => "File Uploaded Successfully");
                $this->session->set_userdata($userSession);
                redirect(base_url() . "employee/service_centre_charges/upload_excel_form");
            }else{
                //save file and upload on s3
                $this->miscelleneous->update_file_uploads($_FILES['file']['name'],$tmpFile, _247AROUND_SF_PRICE_LIST,FILE_UPLOAD_FAILED_STATUS);
                $userSession = array('error' => 'Error In File Uploading. '.$response['msg']);
                $this->session->set_userdata($userSession);
                redirect(base_url() . "employee/service_centre_charges/upload_excel_form");
            }
            
        } else {
            $userSession = array('error' => $return['error']);
            $this->session->set_userdata($userSession);
            redirect(base_url() . "employee/service_centre_charges/upload_excel_form");
        }
    }

    /**
     *  @desc  : Extract rows data from excel
     *  @param : input file and type(price for service price and tax for tax rate)
     *  @return : void
     */

    function upload_excel($inputFileName, $type, $flag) {
        $reader = ReaderFactory::create(Type::XLSX);
        $reader->open($inputFileName);
        $count = 1;
        $rows = array();
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                if ($type == "price") {
                    // Get Data from top 14 rows in excel file
                    if ($count > 1) {
                        $data = $this->set_price_rows_data($row);
                        array_push($rows, $data);
                    }
                } else if ($type == "tax") {
                    // Get Data from top 2 rows in excel file
                    if ($count > 2) {
                        $data = $this->set_tax_rows_data($row);
                        array_push($rows, $data);
                    }
                } else if ($type == "appliance") {
                    log_message('info', 'Inside upload excel');
                    // Get Data from top 2nd rows in excel file
                    if ($count > 1) {
                        log_message('info', 'Inside count');
                        $data = $this->set_partner_appliance_rows_data($row);

                        //Validating Data - For Array its Valid else Invalid Entry
                        if (!is_array($data)) {
                            //Logging Error
                            log_message('info', __FUNCTION__ . ' Error - Due to Empty Column values in File');
                            //Closing Excel File
                            $reader->close();
                            //Redirecting  to Upload page
                            $this->session->set_flashdata('file_error', 'Error in Uploading PARTNER APPLIANCE DETAILS File due to Empty Column values');
                            redirect(base_url() . "employee/service_centre_charges/upload_excel_form");
                            exit;
                        } else {
                            array_push($rows, $data);
                        }
                    }
                }
                $count++;
            }
            //Validation for Empty File
            if ($count == 1 || $count == 2) {
                //Logging Error
                log_message('info', __FUNCTION__ . ' Error - Empty File Uploaded');
                //Closing Excel File
                $reader->close();
                //Redirecting  to Upload page
                $this->session->set_flashdata('file_error', 'Empty File Uploaded - Please check.');
                redirect(base_url() . "employee/service_centre_charges/upload_excel_form");
                exit;
            }
            $count = 1;
            $this->insert_data_list($type, $rows, $flag);
            if ($flag) {
                unset($rows);
                $rows = array();
            }
        }
        $reader->close();
    }

    /**
     *  @desc  : This method is used to insert data into both service_price and tax_rates_by_states tables.
     *  @param : Excel file type and array(data)
     *  @return : void
     */
    function insert_data_list($type, $rows, $flag) {
        $table_name = '';
        $return = 0;
        if ($type == "price") {
            $table_name = "service_centre_charges";
            $return = $this->partner_model->insert_data_in_batch($table_name, $rows, $flag);
        } else if ($type == "tax") {
            $table_name = 'tax_rates_by_states';
            $return = $this->partner_model->insert_data_in_batch($table_name, $rows);
        } else if ($type == "appliance") {
            log_message('info', 'Inside insert data list');
            $table_name = 'partner_appliance_details';
            $return = $this->partner_model->insert_data_in_batch($table_name, $rows, $flag);
        }
        if ($return == 1) {
//	    $this->redirect_upload_form();
        } else {
            $output['error'] = "Error while uploading File";
            $this->upload_excel_form($output);
        }
        
        return $return;
    }

    /**
     *  @desc  : redirect upload excel form
     *  @param : void
     *  @return : void
     */
    function redirect_upload_form() {
        $output = "File uploaded.";
        $userSession = array('success' => $output);
        $this->session->set_userdata($userSession);
        redirect(base_url() . "employee/service_centre_charges/upload_excel_form");
    }

    /**
     *  @desc  : retrieve data from excel cell
     *  @param : array(data)
     *  @return : array
     */
    function set_price_rows_data($row) {

        $data['partner_id'] = isset($row[1]) ? $row[1] : '';
        $data['state'] = isset($row[2]) ? $row[2] : '';
        $data['brand'] = isset($row[3]) ? $row[3] : '';
        $data['service_id'] = isset($row[5]) ? $row[5] : '';
        $data['category'] = isset($row[6]) ? $row[6] : '';
        $data['capacity'] = isset($row[7]) ? $row[7] : '';
        $data['service_category'] = isset($row[8]) ? $row[8] : '';
        $data['product_or_services'] = isset($row[9]) ? $row[9] : '';
        $data['product_type'] = isset($row[10]) ? $row[10] : '';
        $data['tax_code'] = isset($row[11]) ? $row[11] : '';
        $data['active'] = isset($row[12]) ? $row[12] : '';
        $data['check_box'] = isset($row[13]) ? $row[13] : '';
        $data['vendor_basic_charges'] = isset($row[14]) ? $row[14] : '';
        $data['vendor_tax_basic_charges'] = isset($row[15]) ? $row[15] : '';
        $data['vendor_total'] = isset($row[16]) ? $row[16] : '';
        $data['around_basic_charges'] = isset($row[17]) ? $row[17] : '';
        $data['around_tax_basic_charges'] = isset($row[18]) ? $row[18] : '';
        $data['around_total'] = isset($row[19]) ? $row[19] : '';
        $data['customer_total'] = isset($row[21]) ? $row[21] : '';
        $data['partner_payable_basic'] = isset($row[22]) ? $row[22] : '';
        $data['partner_payable_tax'] = isset($row[23]) ? $row[23] : '';
        $data['partner_net_payable'] = isset($row[24]) ? $row[24] : '';
        $data['customer_net_payable'] = isset($row[25]) ? $row[25] : '';
        $data['pod'] = isset($row[26]) ? $row[26] : '';
        $data['is_upcountry'] = isset($row[27]) ? $row[27] : '';
        $data['vendor_basic_percentage'] = isset($row[28]) ? $row[28] : '';
        $data['create_date'] = date("Y-m-d H:i:s");

        return $data;
    }

    /**
     *  @desc  : upload tax rate excel file
     *  @param : void
     *  @return : file type and arrays(data)
     */
    function upload_tax_rate_from_excel() {

        $return = $this->validate_file();
        if ($return == true) {
            $inputFileName = $_FILES['file']['tmp_name'];
            $this->upload_excel($inputFileName, "tax");
        }
    }

    function set_tax_rows_data($row) {
        $data['tax'] = $row[0];
        $data['date'] = $row[1];
        $data['state'] = $row[2];
        $data['appliance'] = $row[3];
        $data['accessory'] = $row[4];
        $data['percentage_rate'] = $row[5];
        return $data;
    }

    /**
     *  @desc  : to display price table
     *  @param : void
     *  @return : void
     */
    function show_pricing_tables() {
        $data = $this->service_centre_charges_model->get_service_city_source_all_appliances_details();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/pricingtable', $data);
    }


    /**
     *  @desc  : Edit pricing table
     *  @param : 
     *  @return : void
     */
    function editPriceTable() {
        $data['id'] = $this->input->post('id');
        $data['check_box'] = $this->input->post('check_box');
        $data['active'] = $this->input->post('active');
        $data['vendor_basic_charges'] = $this->input->post('vendor_basic_charges');
        $data['vendor_tax_basic_charges'] = $this->input->post('vendor_tax_basic_charges');
        $data['around_basic_charges'] = $this->input->post('around_basic_charges');
        $data['around_tax_basic_charges'] = $this->input->post('around_tax_basic_charges');
        $data['customer_total'] = $this->input->post('customer_total');
        $data['partner_net_payable'] = $this->input->post('partner_net_payable');
        $data['customer_net_payable'] = $this->input->post('customer_net_payable');

        $this->service_centre_charges_model->editPriceTable($data);


        echo "success";
    }

    /**
     *  @desc  : This function is used to show the partner services price
     *  @param : void
     *  @return : void
     */
    function show_partner_service_price() {

        $data['partners'] = $this->partner_model->get_all_partner_source();
        $data['source_code'] = json_encode(array_column($data['partners'], 'code','partner_id'));
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/show_partner_services_price', $data);
    }

    /**
     *  @desc  : This function is used to show the partner services price based on dropdown selection
     *           through ajax call
     *  @param : void
     *  @return : array()
     */
    function show_partner_price() {
        
        $partner_id = $this->input->post('partner_id');
        $service_id = $this->input->post('service_id');
        $service_category = $this->input->post('service_category');
        $brand = $this->input->post('brand');
        $where = array();
        if(!empty($partner_id)){
            $where['partner_id'] = $partner_id;
        }
        if(!empty($service_id)){
            $where['service_id'] = $service_id;
        }
        if(!empty($service_category)){
            $where['service_category'] = $service_category;
        }
        if(!empty($brand)){
            $where['brand'] = $brand;
        }
        if(!empty($where)){
            $partner['price_data'] = $this->service_centre_charges_model->get_partner_price_data($where);
        }else{
            $partner['price_data'] = array();
        }
        
        $this->load->view('employee/show_partner_services_price', $partner);
    }

    /**
     *  @desc  : This function is used to populate the dropdown 
     *           through ajax call
     *  @param : void
     *  @return : array()
     */
    function get_partner_data() {
        if (isset($_POST['partner'])) {
            $partner_id = $this->input->post('partner');
            $services = $this->service_centre_charges_model->get_appliance_from_partner($partner_id);

            $option = '<option selected disabled>Select Appliance</option>';

            foreach ($services as $value) {
                $option .= "<option value='" . $value['id'] . "'";
                $option .=" > ";
                $option .= $value['services'] . "</option>";
            }

            echo $option;
        }

        if (isset($_POST['service_id'])) {
            $service_id = $this->input->post('service_id');
            $partner_id = $this->input->post('partner_id');
            $service_category = $this->service_centre_charges_model->get_service_category_from_service_id($service_id, $partner_id);

            $option = '<option selected disabled>Select Service Category</option>';

            if (!empty($service_category)) {
                foreach ($service_category as $value) {
                    $option .= "<option value='" . $value['service_category'] . "'";
                    $option .=" > ";
                    $option .= $value['service_category'] . "</option>";
                }
            }

            echo $option;
        }
    }

    /**
     *  @desc  : This is used to upload partner appliance details excel
     *  @param : void
     *  @return : void
     */
    function upload_partner_appliance_details_excel() {
        $return = $this->partner_utilities->validate_file($_FILES);
        if ($return == "true") {
            //Logging
            log_message('info', __FUNCTION__ . ' Processing of Partner Appliance Excel File started');
            $flag = "";
            if ($this->input->post('flag')) {
                $flag = $this->input->post('flag');
            }
            //Making process for file upload
            $tmpFile = $_FILES['file']['tmp_name'];
            $appliance_file = $_FILES['file']['name'];
            //Processing File 
            $this->upload_excel($tmpFile, "appliance", $flag);

            //Adding Details in File_Uploads table as well
            $this->miscelleneous->update_file_uploads($appliance_file,$tmpFile,_247AROUND_PARTNER_APPLIANCE_DETAILS);
            
            //check brand_name and service_id is exist in appliance_brand table or not
            $not_exist_data = $this->booking_model->get_not_exist_appliance_brand_data();
            if ($not_exist_data) {
                $this->booking_model->insert_not_exist_appliance_brand_data($not_exist_data);
                log_message('info', __FUNCTION__ . 'Not exist brand name and service id added into the table appliance_brand');
            }
            
            $this->redirect_upload_form();
        } else {
            $this->upload_excel_form($return);
        }
    }

    /**
     * @Desc:This function is used to set Rows for Partner Appliance Details
     * @params: Array
     * @return: Array
     * 
     */
    function set_partner_appliance_rows_data($row) {
        log_message('info', __FUNCTION__);
        //Flag for checking validation -- Only Model can be Empty
        $empty_flag = FALSE;
        $data['partner_id'] = isset($row[0]) && !empty($row[0]) ? $row[0] : $empty_flag = TRUE;
        $data['service_id'] = isset($row[1]) && !empty($row[1]) ? $row[1] : $empty_flag = TRUE;
        //Sanitizing Brand Name
        //$data['brand'] = isset($row[2]) && !empty($row[2])?preg_replace('/[^A-Za-z0-9 ]/', '', $row[2]):$empty_flag = TRUE;
        $data['brand'] = isset($row[2]) && !empty($row[2]) ? $row[2] : $empty_flag = TRUE;
        $data['category'] = isset($row[3]) && !empty($row[3]) ? $row[3] : $empty_flag = TRUE;
        $data['capacity'] = isset($row[4]) && !empty($row[4]) ? $row[4] : '';
        $data['model'] = isset($row[5]) && !empty($row[5]) ? $row[5] : '';
        $data['active'] = 1;

        if ($empty_flag) {
            return $empty_flag;
        } else {
            return $data;
        }
    }
    
    function process_upload_service_price_file($inputFileName) {
        try {
            $flag = FALSE;
            $msg = "";
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
            $objPHPExcel = $objReader->load($inputFileName);
            $i = 0;
            foreach ($objPHPExcel->getAllSheets() as $sheet) {
                $highestRow = $data = $sheet->getHighestDataRow();
                $highestColumn = $sheet->getHighestDataColumn();
                $headings = $sheet->rangeToArray('A1:' . $highestColumn . 1, NULL, TRUE, FALSE,FALSE);
                
                //getting sheet header
                $headings_new = array();
                foreach ($headings as $heading) {
                    $heading = str_replace(array(" ","/"), "_", preg_replace('/\s+/', ' ', $heading));
                    array_push($headings_new, $heading);
                }
                $this->Columfailed = "";
                $headings_new1 = array_map('strtolower', $headings_new[0]);
                $response = $this->check_column_exist($headings_new1);
                $sheetData = [];
                $sheetUniqueRowData = [];
                $rowNotEmpty = 0;
                if($response['status']){
                    for ($row = 2, $i = 0; $row <= $highestRow; $row++, $i++) {
                        //  Read a row of data into an array
                        $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE,FALSE);
                        $newRowData = array_combine($headings_new1, $rowData[0]);
                        if(!empty($newRowData['partner_id'])){
                            if($newRowData['service_category'] !== REPEAT_BOOKING_TAG  && $newRowData['sf_percentage'] == 0){
                                log_message('info', $sheet->getTitle().' sheet has SF Percentage 0 for non repeat booking');
                                $msg = $sheet->getTitle().' sheet has SF Percentage 0 for non repeat booking';
                                $flag = FALSE;
                                break;
                                
                            }else if($newRowData['sf_percentage'] == 100){
                                log_message('info', $sheet->getTitle().' sheet has SF Percentage 100');
                                $msg = $sheet->getTitle().' sheet has SF Percentage 100';
                                $flag = FALSE;
                                break;
                            }else{
                                $subArray = $this->get_sub_array($newRowData,array('partner_id','brand','product_id','category','capacity','service_category','customer_net_payable'));
                                array_push($sheetUniqueRowData, implode('_', str_replace(' ','_', $subArray)));
                                array_push($sheetData, $newRowData);
                                $rowNotEmpty++;
                                $this->make_final_service_price_data($newRowData);
                                $flag = TRUE;
                            }
                            
                        }
                    }
                    
                    if($flag){
                        $currentSheetPartnerIdArr = array_column($sheetData, 'partner_id');
                        $isDiffrentPartnerId = count(array_unique($currentSheetPartnerIdArr));
                        $arr_duplicates = array_diff_assoc($sheetUniqueRowData, array_unique($sheetUniqueRowData));
                        if($isDiffrentPartnerId == 1 && empty($arr_duplicates)){
                            $flag = TRUE;
                            unset($currentSheetPartnerIdArr);
                            unset($isDiffrentPartnerId);
                        }else{
                            log_message('info', $sheet->getTitle().' sheet either has different partner id or same data in any two or more row');
                            $msg = $sheet->getTitle().' sheet has either different partner id or same data';
                            $flag = FALSE;
                            break;
                        }
                    }else{
                        $flag = FALSE;
                        break;
                    }

                    
                }else{
                    log_message('info','Column Not Found');
                    $msg = $response['msg']."in the ".$sheet->getTitle()." sheet";
                    $flag = FALSE;
                    break;
                }
            }
            if($flag){
                $res = $this->insert_data_list("price", $this->dataToInsert, "");
                if($res){
                    log_message('info','Price Updated Successfully');
                }else{
                    log_message('info','Error In Updating Price');
                }
            }else{
                $to = $this->session->userdata('official_email');
                $cc = DEVELOPER_EMAIL;
                $subject = "Failed! Service Price File is uploaded by " . $this->session->userdata('employee_id');
                $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $msg, "",SERVICE_PRICE_FILE_FAILED);
            }
            
            $return_response['status'] = $flag;
            $return_response['msg'] = $msg;
            
            return $return_response;
            
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }
    }
    
    function check_column_exist($rowData){
        $message = "";
        $error = false;
        if (!in_array('partner_id', $rowData)) {
            $message .= " Partner ID Column does not exist.<br/><br/>";
            $this->Columfailed .= " Partner ID, ";
            $error = true;
        }

        if (!in_array('state', $rowData)) {
            $message .= " STATE Column does not exist. <br/><br/>";
            $this->Columfailed .= " STATE, ";
            $error = true;
        }
        
        if (!in_array('brand', $rowData)) {
      
            $message .= " Brand Column does not exist. <br/><br/>";
            $this->Columfailed .= "Brand , ";
            $error = true;
        }
         
        if (!in_array('product_id', $rowData)) {
       
            $message .= " Product Column does not exist. <br/><br/>";
            $this->Columfailed .= " Product , ";
            $error = true;
        }
        if (!in_array('category', $rowData)) {

            $message .= " Category Column does not exist. <br/><br/>";
            $this->Columfailed .= " Category , ";
            $error = true;
        }
        if (!in_array('capacity', $rowData)) {
      
            $message .= " Capacity Column does not exist. <br/><br/>";
            $this->Columfailed .= "Capacity , ";
            $error = true;
        }
        if (!in_array('service_category', $rowData)) {
      
            $message .= " Service Category Column does not exist. <br/><br/>";
            $this->Columfailed .= "Service Category ";
            $error = true;
        }
        if (!in_array('product_service', $rowData)) {
      
            $message .= " Product Service Column does not exist. <br/><br/>";
            $this->Columfailed .= "Product Service , ";
            $error = true;
        }
        if (!in_array('product_type', $rowData)) {
      
            $message .= " Product Type Column does not exist. <br/><br/>";
            $this->Columfailed .= "Product Type,";
            $error = true;
        }
        if (!in_array('tax_code', $rowData)) {
      
            $message .= " Tax Code Column does not exist. <br/><br/>";
            $this->Columfailed .= "Tax Code ,";
            $error = true;
        }
        if (!in_array('active', $rowData)) {
      
            $message .= " Active Column does not exist. <br/><br/>";
            $this->Columfailed .= "Active ";
            $error = true;
        }
        if (!in_array('check_box', $rowData)) {
      
            $message .= " Check Box Column does not exist. <br/><br/>";
            $this->Columfailed .= "Check Box ,";
            $error = true;
        }
        if (!in_array('vendor_base_svc_ch', $rowData)) {
      
            $message .= " Vendor Base Service Charge Column does not exist. <br/><br/>";
            $this->Columfailed .= "Vendor Base Service Charge ,";
            $error = true;
        }
        if (!in_array('vendor_base_svc_ch_tax', $rowData)) {
      
            $message .= " Vendor Base Service Charge Tax Column does not exist. <br/><br/>";
            $this->Columfailed .= "Vendor Base Service Charge Tax ";
            $error = true;
        }
        if (!in_array('vendor_base_svc_ch_with_tax_(rs.)', $rowData)) {
      
            $message .= " Vendor Base Service Charge With Tax Column does not exist. <br/><br/>";
            $this->Columfailed .= "Vendor Base Service Charge With Tax ";
            $error = true;
        }
        if (!in_array('around_svc_ch', $rowData)) {
      
            $message .= " Around Service Charge Column does not exist. <br/><br/>";
            $this->Columfailed .= "Around Service Charge ,";
            $error = true;
        }
        if (!in_array('around_svc_tax___vat', $rowData)) {
      
            $message .= " Around Service Charge With Tax VAT Column does not exist. <br/><br/>";
            $this->Columfailed .= "Around Service Charge With Tax VAT ,";
            $error = true;
        }
        if (!in_array('around_total_with_tax', $rowData)) {
      
            $message .= " Around Total With Tax Column does not exist. <br/><br/>";
            $this->Columfailed .= "Around Total With Tax ";
            $error = true;
        }
        if (!in_array('around_svc_ch', $rowData)) {
      
            $message .= " Around Service Charge Column does not exist. <br/><br/>";
            $this->Columfailed .= "Around Service Charge ,";
            $error = true;
        }
        if (!in_array('total_svc_tax___vat', $rowData)) {
      
            $message .= " Total Service Tax VAT Column does not exist. <br/><br/>";
            $this->Columfailed .= "Total Service Tax VAT ,";
            $error = true;
        }
        if (!in_array('customer_total_rs.', $rowData)) {
      
            $message .= " Customer Total Rs Column does not exist. <br/><br/>";
            $this->Columfailed .= "Customer Total Rs ";
            $error = true;
        }
        if (!in_array('partner_payable_basic', $rowData)) {
      
            $message .= " Partner Payable Basic Column does not exist. <br/><br/>";
            $this->Columfailed .= "Partner Payable Basic ,";
            $error = true;
        }
        if (!in_array('partner_payable_tax', $rowData)) {
      
            $message .= " Partner Payable Tax Column does not exist. <br/><br/>";
            $this->Columfailed .= "Partner Payable Tax ,";
            $error = true;
        }
        if (!in_array('partner_net_payable', $rowData)) {
      
            $message .= " Partner Net Payable Column does not exist. <br/><br/>";
            $this->Columfailed .= "Partner Net Payable ";
            $error = true;
        }
        if (!in_array('customer_net_payable', $rowData)) {
      
            $message .= " Customer Net Payable Column does not exist. <br/><br/>";
            $this->Columfailed .= "Customer Net Payable ,";
            $error = true;
        }
        if (!in_array('serial_number_mandatory', $rowData)) {
      
            $message .= " Serial Number Mandatory Column does not exist. <br/><br/>";
            $this->Columfailed .= "Serial Number Mandatory ,";
            $error = true;
        }
        if (!in_array('upcountry', $rowData)) {
      
            $message .= " Upcountry Column does not exist. <br/><br/>";
            $this->Columfailed .= "Upcountry ";
            $error = true;
        }
        if (!in_array('sf_percentage', $rowData)) {
      
            $message .= " SF Percentage Column does not exist. <br/><br/>";
            $this->Columfailed .= "SF Percentage ";
            $error = true;
        }       
        if ($error) {
            $message .= " Please check and upload again.";
            $this->Columfailed .= " column does not exist.";
            $return_response['status'] = FALSE;
            $return_response['msg'] = $message;
        } else {
            $return_response['status'] = TRUE;
            $return_response['msg'] = '';
        }
        
        return $return_response;
    }
    
    function get_sub_array(array $parentArray, array $subsetArrayToGet)
    {
        return array_intersect_key($parentArray, array_flip($subsetArrayToGet));
    }
    
    function make_final_service_price_data($newRowData){
        
        
        $final_data['partner_id'] = $newRowData['partner_id'];
        $final_data['state'] = $newRowData['state'];
        $final_data['brand'] = $newRowData['brand'];
        $final_data['service_id'] = $newRowData['product_id'];
        $final_data['category'] = $newRowData['category'];
        $final_data['capacity'] = empty($newRowData['capacity'])?'':$newRowData['capacity'];
        $final_data['service_category'] = $newRowData['service_category'];
        $final_data['product_or_services'] = $newRowData['product_service'];
        $final_data['product_type'] = $newRowData['product_type'];
        $final_data['tax_code'] = $newRowData['tax_code'];
        $final_data['active'] = $newRowData['active'];
        $final_data['check_box'] = $newRowData['check_box'];
        $final_data['vendor_basic_charges'] = $newRowData['vendor_base_svc_ch'];
        $final_data['vendor_tax_basic_charges'] = $newRowData['vendor_base_svc_ch_tax'];
        $final_data['vendor_total'] = $newRowData['vendor_base_svc_ch_with_tax_(rs.)'];
        $final_data['around_basic_charges'] = $newRowData['around_svc_ch'];
        $final_data['around_tax_basic_charges'] = $newRowData['around_svc_tax___vat'];
        $final_data['around_total'] = $newRowData['around_total_with_tax'];
        $final_data['customer_total'] = $newRowData['customer_total_rs.'];
        $final_data['partner_payable_basic'] = $newRowData['partner_payable_basic'];
        $final_data['partner_payable_tax'] = $newRowData['partner_payable_tax'];
        $final_data['partner_net_payable'] = $newRowData['partner_net_payable'];
        $final_data['customer_net_payable'] = $newRowData['customer_net_payable'];
        $final_data['pod'] = $newRowData['serial_number_mandatory'];
        $final_data['is_upcountry'] = $newRowData['upcountry'];
        $final_data['vendor_basic_percentage'] = $newRowData['sf_percentage'];
        if($newRowData['service_category'] == REPAIR_OOW_TAG){
            $this->oow_spare_parts($final_data);
        }
        array_push($this->dataToInsert, $final_data);
        
    }
    
    public function oow_spare_parts($data){
        $data['service_category'] = REPAIR_OOW_PARTS_PRICE_TAGS;
        $data['product_or_services'] = _247AROUND_PRODUCT_TAG;
        $data['product_type'] = REPAIR_OOW_PARTS_PRICE_TAGS;
        $data['tax_code'] = 'VAT';
        $data['vendor_basic_charges'] = 0;
        $data['vendor_tax_basic_charges'] = 0;
        $data['vendor_total'] = 0;
        $data['around_basic_charges'] = 0;
        $data['around_tax_basic_charges'] = 0;
        $data['around_total'] = 0;
        $data['customer_total'] = 0;
        $data['partner_payable_basic'] = 0;
        $data['partner_payable_tax'] = 0;
        $data['partner_net_payable'] = 0;
        $data['customer_net_payable'] = 0;
        $data['vendor_basic_percentage'] = 0;
        
        array_push($this->dataToInsert, $data);
    }
    /**
     * @desc This is used to load generate Price Form
     * @param type $partner_id
     */
    function generate_service_charges_view($partner_id){
        $partner_data = $this->partner_model->getpartner_details("partner_type, public_name",array("partner_id" => $partner_id));
        $partner_type = $partner_data[0]['partner_type'];
        $data['partner_type'] = $partner_type;
        $data['partner_id'] = $partner_id;
        $data['public_name'] = $partner_data[0]['public_name'];
        if ($partner_type == OEM) {

            $data['appliances'] = $this->partner_model->get_partner_specific_services($partner_id);
        } else {
            $data['appliances'] = $services = $this->booking_model->selectservice();
        }
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/generate_service_charges', $data);
    }
    /**
     * @desc it called from ajax to get Call type 
     */
    function get_service_request_type(){
        $service_id = $this->input->post("service_id");
        $price_tags = $this->input->post("price_tags");
        $data = $this->service_centre_charges_model->get_service_request_type(array("service_id" => $service_id), "*");
        $option = "";
        foreach ($data as $value) {
            $option .= "<option ";
            if ($price_tags === $value['service_category']) {
                $option .= " selected ";
            } else if (count($data) == 1) {
                $option .= " selected ";
            }
            $option .= " data-type = '".$value['product_or_services']."' value='" . $value['service_category'] . "'>" . $value['service_category'] . "</option>";
        }
        echo $option;
    }
    
    function get_service_category_request_type(){
        $service_id = $this->input->post("service_id");
        $price_tags = $this->input->post("price_tags");
        $data = $this->service_centre_charges_model->get_service_request_type(array("service_id" => $service_id), "*");
        $option = "";
        foreach ($data as $value) {
            $option .= "<option ";
            if ($price_tags === $value['service_category']) {
                $option .= " selected ";
            } else if (count($data) == 1) {
                $option .= " selected ";
            }
            $option .= " data-type = '".$value['product_or_services']."' value='" . $value['id'] . "'>" . $value['service_category'] . "</option>";
        }
        echo $option;
    }
    /**
     * @desc Generate Partner/Vendor Service Charge
     */
    function generate_service_charges(){
        $form_data = $this->input->post();
        $where = array("service_id" => $form_data['service_id'], 'partner_id' => $form_data['partner_id']);
        
        $where_in['category'] = $form_data['category'];
       
        if(isset($form_data['free']) && stristr($form_data['request_type'], "Installation") && $form_data['product_or_services'] == "Service"){
            
            $service_category = array($form_data['request_type']." (Free)", REPEAT_BOOKING_TAG);
             
        } else if(isset($form_data['paid']) && stristr($form_data['request_type'], "Installation") && $form_data['product_or_services'] == "Service"){
             
            $service_category = array($form_data['request_type']." (Paid)", REPEAT_BOOKING_TAG);
            
        } else if(stristr($form_data['request_type'], "Repair") && $form_data['product_or_services'] == "Service"){
          
            $service_category = array($form_data['request_type'], SPARE_PART_BOOKING_TAG, REPEAT_BOOKING_TAG);
            
        } else if($form_data['product_or_services'] == "Service"){
           
            $service_category = array($form_data['request_type'], REPEAT_BOOKING_TAG);
        } else {
            $service_category = array($form_data['request_type']);
        }
         
        $where_in['service_category'] = $service_category;
        
        if(isset($form_data['free'])){
            $form_data['free'] = 1;
            
        } else {
            $form_data['free'] = 0;
        }
        
        if(isset($form_data['paid'])){
            $form_data['paid'] = 1;
            
        } else {
            $form_data['paid'] = 0;
        }
        if(!isset($form_data['free_upcountry'])){
            $form_data['free_upcountry'] = 0;
        }
        if(!isset($form_data['paid_upcountry'])){
            $form_data['paid_upcountry'] = 0;
        }
        
        if(!isset($form_data['free_pod'])){
            $form_data['free_pod'] = 0;
        }
        if(!isset($form_data['paid_pod'])){
            $form_data['paid_pod'] = 0;
        }
        if(!empty($form_data['brand'])){
            $where_in['brand'] = $form_data['brand'];
        }
        if(!empty($form_data['capacity'])){
            $where_in['capacity'] = $form_data['capacity'];
        }
        
        $charges = $this->service_centre_charges_model->get_service_caharges_data("*", $where, "", $where_in);
        $key_data = array();
        foreach($charges as $value){
            
            $key = str_replace(' ', '', $value['category'].$value['brand'].$value['capacity'].$value['service_category']);
            $key_data[$key]= "";
        }
     
        $data = array();
        $existing_key = array();
        
        $data['service_category'] = $service_category;
        $existing_key[] = 'service_category';
        
        $data['product_or_services'] = array($form_data['product_or_services']);
        $existing_key[] = 'product_or_services';
       
        $data['partner_id'] = array($form_data['partner_id']);
        $existing_key[] = 'partner_id';
        
        $data['agent_id'] = array($this->session->userdata("id"));
        $existing_key[] = 'agent_id';
        
        $data['service_id'] = array($form_data['service_id']);
        $existing_key[] = 'service_id';
        
        $data['create_date'] = array(date("Y-m-d H:i:s"));
        $existing_key[] = 'create_date';
        
        if(!empty($form_data['brand'])){
            $data['brand'] = $form_data['brand'];
            $existing_key[] = 'brand';
        } else {
            $data['brand'] = array("");
            $existing_key[] = 'brand';
        }
        if(!empty($form_data['category'])){
             $data['category'] = $form_data['category'];
             $existing_key[] = 'category';
        } else {
             $data['category'] = array("");
             $existing_key[] = 'category';
        }
        if(!empty($form_data['capacity'])){
            $data['capacity'] = $form_data['capacity'];
            $existing_key[] = 'capacity';
        } else {
            $data['capacity'] = array("");
            $existing_key[] = 'capacity';
        }
        
       $combos = $this->generate_combinations($data);
       $service_data = $this->generate_service_charges_data($form_data, $combos, $existing_key, $key_data);
       if(!empty($service_data['service_charge'])){
           $this->service_centre_charges_model->insert_data_in_temp("service_centre_charges", $service_data['service_charge']);
       }
       if(!empty($service_data['duplicate'])){
           $service_data['delete'] = FALSE;
           $this->load->view("employee/duplicate_service_charge", $service_data);
       } else {
           echo "success";
       }
       
    }
    
    function generate_service_charges_data($form_data, $combos, $existing_key, $key_data) {
        $stmp = array();
        $duplicate_data = array();
        foreach ($combos as $value) {
          
            $data = array();
            foreach ($value as $key1 => $value1) {
                $data[$existing_key[$key1]] = $value1;
            }
            
            $fp = array();
            if ($form_data['free'] == 1 && $form_data['paid'] == 1) {
                $fp[] = "free";
                $fp[] = "paid";
            } else if ($form_data['free'] == 1) {
                $fp[] = "free";
            } else if ($form_data['paid'] == 1) {
                $fp[] = "paid";
            }

            foreach ($fp as $free_paid) {
                $data['tax_code'] = "VAT";
                if ($data['service_category'] == SPARE_PART_BOOKING_TAG) {
                    $data = $this->add_repeat_and_spare_Parts($data, "Product", "VAT", 0);
                    
                } else if($data['service_category'] == REPEAT_BOOKING_TAG){
                    
                    $data = $this->add_repeat_and_spare_Parts($data, "Service", "ST", 1);
                     
                } else {
                    if ($free_paid == "free") {

                        $data = $this->generate_price_for_free($data, $form_data);
                    } else if ($free_paid == "paid") {
                        $data = $this->generate_price_for_paid($data, $form_data);
                    }
                }

                $data['active'] = 1;
                $data['check_box'] = 1;
                $newkey = str_replace(' ', '', $data['category'] . $data['brand'] . $data['capacity'] . $data['service_category']);
                if (array_key_exists($newkey, $key_data)) {
                    if($data['service_category'] == SPARE_PART_BOOKING_TAG || $data['service_category'] == REPEAT_BOOKING_TAG){
                        log_message('info', __METHOD__ . " Spare OR Repeat booking ");
                    } else {
                    
                        array_push($duplicate_data, $data);
                    }
                } else {
                    array_push($stmp, $data);
                }
            }
        }
        
        return array("duplicate" => $duplicate_data,
            "service_charge" => $stmp);
    }
    
    function add_repeat_and_spare_Parts($data, $service, $tax_code, $pod){
        $data['product_or_services'] = $service;
        $data['tax_code'] = $tax_code;
        $data['pod'] = $pod;
        $data['is_upcountry'] = -1;
        $data['customer_total'] = 0;
        $data['customer_net_payable'] = 0;
        $data['partner_net_payable'] = 0;
        $data['vendor_total'] = 0;
        $data['vendor_tax_basic_charges'] = 0;
        $data['vendor_basic_charges'] = 0;
        $data['vendor_basic_percentage']  = 0;
        
        return $data;
    }

    function generate_price_for_paid($data, $form_data) {
        if ($data['product_or_services'] == "Service") {

            $data['tax_code'] = "ST";
        }
        $data['pod'] = $form_data['paid_pod'];
        $data['is_upcountry'] = $form_data['paid_upcountry'];
        $data['customer_total'] = $form_data['paid_customer_total'];
        $data['customer_net_payable'] = $form_data['paid_customer_total'];
        $data['partner_net_payable'] = 0;
        $data['vendor_total'] = $form_data['paid_vendor_total'];
        $data['vendor_tax_basic_charges'] = $this->booking_model->get_calculated_tax_charge($form_data['paid_vendor_total'], DEFAULT_TAX_RATE);
        $data['vendor_basic_charges'] = $data['vendor_total'] - $data["vendor_tax_basic_charges"];
        if ($data['customer_total'] != 0) {
            
            $data['vendor_basic_percentage'] = ($data['vendor_total'] / $data['customer_total']) * 100;
            
        } else {
            $data['vendor_basic_percentage'] = 0;
        }
        
        $data['flat_upcountry'] = $form_data['flat_upcountry'];
        if($form_data['paid_upcountry'] == 0 && $form_data['flat_upcountry'] == 1){
            
            $data['upcountry_customer_price'] = $form_data['paid_upcountry_customer_price'];
            $data['upcountry_vendor_price'] = $form_data['paid_upcountry_vendor_price'];
            $data['upcountry_partner_price'] = 0;
            
        } else {
            
            $data['upcountry_customer_price'] = 0;
            $data['upcountry_vendor_price'] = 0;
            $data['upcountry_partner_price'] = 0;
        }
        
        return $data;
    }
    
    function generate_price_for_free($data, $form_data) {
        if ($data['product_or_services'] == "Service") {

            $data['tax_code'] = "ST";
        }
        $data['pod'] = $form_data['free_pod'];
        $data['is_upcountry'] = $form_data['free_upcountry'];
        $data['customer_total'] = $form_data['free_customer_total'];
        $data['customer_net_payable'] = 0;

        $data['partner_net_payable'] = $form_data['free_customer_total'];
        $vendor_tax = $form_data['free_vendor_total'] * SERVICE_TAX_RATE;
        $vendor_total = $form_data['free_vendor_total'] + $vendor_tax;
        $data['vendor_basic_charges'] = $form_data['free_vendor_total'];
        $data['vendor_total'] = $vendor_total;
        $data['vendor_tax_basic_charges'] = $vendor_tax;
        if ($data['customer_total'] != 0) {
             $data['vendor_basic_percentage'] = ($vendor_total / $data['customer_total']) * 100;
        } else {
            $data['vendor_basic_percentage'] = 0;
        }
        
        $data['flat_upcountry'] = $form_data['flat_upcountry'];
        
        if($form_data['free_upcountry'] == 1 && $form_data['flat_upcountry'] == 1){
            
            $data['upcountry_customer_price'] = 0;
            $data['upcountry_vendor_price'] = $form_data['free_upcountry_vendor_price'];
            $data['upcountry_partner_price'] = $form_data['free_upcountry_partner_price'];
            
        } else if($form_data['free_upcountry'] == 0 && $form_data['flat_upcountry'] == 1){
            $data['upcountry_customer_price'] = $form_data['free_upcountry_customer_price'];
            $data['upcountry_vendor_price'] = $form_data['free_upcountry_vendor_price'];
            $data['upcountry_partner_price'] = 0;
        } else {
            $data['upcountry_customer_price'] = 0;
            $data['upcountry_vendor_price'] = 0;
            $data['upcountry_partner_price'] = 0;
        }
        
        return $data;
    }

    function generate_combinations($data, &$all = array(), $group = array(), $value = null, $i = 0) {
        $keys = array_keys($data);
        if (isset($value) === true) {
            array_push($group, $value);
        }

        if ($i >= count($data)) {
            array_push($all, $group);
        } else {
            $currentKey = $keys[$i];
            $currentElement = $data[$currentKey];
            foreach ($currentElement as $val) {
                $this->generate_combinations($data, $all, $group, $val, $i + 1);
            }
        }

        return $all;
    }
    
    function show_charge_list($partner_id){
        $partner_data = $this->partner_model->getpartner_details("partner_type, public_name",array("partner_id" => $partner_id));
        $partner_type = $partner_data[0]['partner_type'];
        $data['partner_type'] = $partner_type;
        $data['partner_id'] = $partner_id;
        $data['public_name'] = $partner_data[0]['public_name'];
        if ($partner_type == OEM) {

            $data['appliances'] = $this->partner_model->get_partner_specific_services($partner_id);
        } else {
            $data['appliances'] = $services = $this->booking_model->selectservice();
        }
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/show_service_price_details', $data);
    }
    
    function price_table(){
        //log_message("info", json_encode($_POST, true));
      //Do not try to un-comment
      //  $str = '{"service_id":"46","partner_id":"247010","brand":["Belco","Ego Vision","Wybor"],"category":["TV-LED"],"capacity":["16 Inch"],"request_type":"Installation & Demo","product_or_services":"","free":"1","label":"WEBUPLOAD"}';
      //  $_POST = json_decode($str, TRUE);
        $form_data = $this->input->post();
        $where = array("service_id" => $form_data['service_id'], 'partner_id' => $form_data['partner_id']);
        if(isset($form_data['paid']) && isset($form_data['free'])){
            if (stristr($form_data['request_type'], "Installation")) {
                
                $paid_service_category = $form_data['request_type']." (Free)";
                $free_service_category = $form_data['request_type']." (Paid)";
                
                $where_in['service_category'] = array($paid_service_category, $free_service_category, $form_data['request_type']);
            } else {
                $where['service_category'] = $form_data['request_type'];
            } 

        } else if(isset($form_data['paid']) && !isset($form_data['free'])){
            $where['customer_net_payable > 0'] = NULL;
            if (stristr($form_data['request_type'], "Installation")) {
                
                $where['service_category'] = $form_data['request_type']." (Paid)";
            } else {
                $where['service_category'] = $form_data['request_type'];
            }
        } else if(!isset($form_data['paid']) && isset($form_data['free'])){
            $where['partner_net_payable > 0 '] = NULL;
            
            if (stristr($form_data['request_type'], "Installation")) {
                
                $where['service_category'] = $form_data['request_type']." (Free)";
            } else {
                $where['service_category'] = $form_data['request_type'];
            }
            
        }
        
        
        
        $where_in['category'] = $form_data['category'];
        if(!empty($form_data['brand'])){
            $where_in['brand'] = $form_data['brand'];
        }
        if(!empty($form_data['capacity'])){
            $where_in['capacity'] = $form_data['capacity'];
        }
        
        $charges['duplicate'] = $this->service_centre_charges_model->get_service_caharges_data("service_centre_charges.*, services", $where, "id", $where_in);
        $charges['delete'] = true;
        $charges['public_name'] = $this->input->post("public_name");
        $this->load->view("employee/duplicate_service_charge", $charges);
    }
    
    function delete_service_charges(){
        log_message('info', __METHOD__. " ". json_encode($this->input->post('delete_charge'), true));
        //Do not try to un-comment
//        $str = '{"delete_charge":["1499","1500","1501","1663","1664","1665","1827","1828","1829","9804","9805","9806","9829","9830","9831","9854","9855","9856"],"label":"WEBUPLOAD"}';
//        $_POST = json_decode($str, true);
        $form_data = $this->input->post('delete_charge');
        if(!empty($form_data)){
            $agent_id = $this->session->userdata("id");
            $this->service_centre_charges_model->insert_deleted_s_charge_in_trigger($agent_id, $form_data);
            $status = $this->service_centre_charges_model->delete_service_charges($form_data);
            if($status){
                echo "success";
            } else {
                echo "error";
            }
        }
    }
    
    function add_miscellaneous_charges(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/get_add_miscellaneous_charges_form');
    }
    
    function process_add_misc_charges(){
        $this->form_validation->set_rules('booking_id', 'Booking ID', 'required|trim');
        $this->form_validation->set_rules('remarks', 'Remarks', 'required|trim');
        $this->form_validation->set_rules('validate_approval_misc_charges_file', 'Approval File', 'callback_validate_approval_misc_charges_file');
        if ($this->form_validation->run() == TRUE) {
         
            $booking_id = $this->input->post('booking_id');
            $booking_details = $this->booking_model->get_bookings_count_by_any("assigned_vendor_id", array('booking_id' => $booking_id));
            
            if(!empty($booking_details)){
                if(!empty($booking_details[0]['assigned_vendor_id'])){
                    
                    $remarks = $this->input->post("remarks");
                    $vendor_details = $this->vendor_model->getVendorDetails('gst_no', array('id' => $booking_details[0]['assigned_vendor_id']));
                   
                    $data = $this->input->post('misc');
                    $misc_charge_data = array();
                    $disc_not_fount = 0;
                    $template = array(
                        'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
                    );

                    $this->table->set_template($template);

                    $this->table->set_heading(array('Booking ID', 'Description', 'Vendor Charge', 'Partner Charge', 'Remarks'));
                    foreach ($data as $key => $value){

                        if(!empty($value['description'])){
                            $misc_charge_data[$key]['description'] = $value['description'];
                            $misc_charge_data[$key]['vendor_basic_charges'] = $value['vendor_charge'];
                            if(!empty($vendor_details[0]['gst_no'])){
                                $misc_charge_data[$key]['vendor_tax'] = $value['vendor_charge'] * DEFAULT_TAX_RATE/100;
                                
                            } else {
                                $misc_charge_data[$key]['vendor_tax'] = 0;
                            }
                            $misc_charge_data[$key]['product_or_services'] = $value['product_or_services'];
                            $misc_charge_data[$key]['tax_rate'] = DEFAULT_TAX_RATE;
                            $misc_charge_data[$key]['partner_charge'] = $value['partner_charge'];
                            $misc_charge_data[$key]['remarks'] = $remarks;
                            $misc_charge_data[$key]['booking_id'] = $booking_id;
                            if(!empty($this->input->post("approval_file"))){
                                $misc_charge_data[$key]['approval_file'] = $this->input->post("approval_file");
                            } 
                            
                            $misc_charge_data[$key]['create_date'] = date('Y-m-d H:i:s');
                            
                            $this->table->add_row($booking_id, $value['description'], $value['vendor_charge'], $value['partner_charge'], 
                                    $remarks);
                        } else {
                            $disc_not_fount++;
                        }
                    }
                    if(!empty($misc_charge_data)){
                        $this->booking_model->insert_misc_charges_in_batch($misc_charge_data);
                        
                        $actor = $next_action = NULL;
                        
                        $this->notify->insert_state_change($booking_id, NEW_CHARGES_ADDED,  _247AROUND_PENDING, $remarks, 
                                $this->session->userdata('id'), $this->session->userdata('employee_id'),
                    $actor, $next_action, _247AROUND);
                        
                        $email_template = $this->booking_model->get_booking_email_template(MISC_CHARGES_DETAILS_ON_EMAIL);
                        if(!empty($email_template)){
                            $to = $email_template[1]. ", ".$this->session->userdata('official_email');
                            $cc = $email_template[3];
                            $subject = vsprintf($email_template[4], array($booking_id));
                            $bcc = $email_template[5];
                            $agent_id = $this->session->userdata('emp_name');
                            $a = "<a href='". base_url()."employee/service_centre_charges/update_misc_charges/".$booking_id."'>Click Here</a>";
                            $message = vsprintf($email_template[0], array($agent_id, $this->table->generate(), $a));

                            $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, $bcc, $subject, $message, "",MISC_CHARGES_DETAILS_ON_EMAIL, "", $booking_id);
                        }
                        
                        $this->session->set_userdata(array('success' => "Charges Added Successfully"));
                        redirect(base_url()."employee/service_centre_charges/add_miscellaneous_charges");
                    } else {
                        $this->session->set_userdata(array('error' => "Please try again, Insertion Failed!"));
                        redirect(base_url()."employee/service_centre_charges/add_miscellaneous_charges");
                    }

                } else {
                    $this->session->set_userdata(array('error' => "Please Assign Booking First"));
                    redirect(base_url()."employee/service_centre_charges/add_miscellaneous_charges");
                }
            } else {
                $this->session->set_userdata(array('error' => "Booking ID is not Exist"));
                redirect(base_url()."employee/service_centre_charges/add_miscellaneous_charges");
            }
            
        } else {
            $this->session->set_userdata(array('error' => validation_errors()));
            redirect(base_url()."employee/service_centre_charges/add_miscellaneous_charges");
        }
    }
    
    function update_misc_charges($booking_id) {
        if (!empty($booking_id)) {
            log_message('info', __METHOD__ . " Booking ID " . $booking_id);
            $data['data'] = $this->booking_model->get_misc_charges_data("*", array('booking_id' => $booking_id, "active" => 1));
            if (!empty($data['data'])) {
                $data['booking_id'] = $booking_id;
                $this->miscelleneous->load_nav_header();
                $this->load->view('employee/update_misc_charges', $data);
            } else {
                $this->session->set_userdata(array('error' => "Booking ID is not exist"));
                redirect(base_url() . "employee/service_centre_charges/add_miscellaneous_charges");
            }
        } else {
            $this->session->set_userdata(array('error' => "Booking ID is not exist"));
            redirect(base_url() . "employee/service_centre_charges/add_miscellaneous_charges");
        }
    }

    function validate_approval_misc_charges_file() {
      
        if (!empty($_FILES['approval_misc_charges_file']['tmp_name'])) {
            $allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
            $booking_id = $this->input->post("booking_id");
            $defective_courier_receipt = $this->miscelleneous->upload_file_to_s3($_FILES["approval_misc_charges_file"], 
                    "approval_file", $allowedExts, $booking_id, "misc-images", "approval_file");
            if($defective_courier_receipt){
                
               return true;
            } else {
                $this->form_validation->set_message('validate_approval_misc_charges_file', 'Image, File size or '
                        . 'file type is not supported. Allowed extentions are png, jpg, jpeg and pdf. '
                        . 'Maximum file size is 5 MB.');
                return false;
            }
           
        } else {
            $is_required = $this->input->post("file_required");
            if($is_required){
                
                $this->form_validation->set_message('validate_approval_misc_charges_file', 'Approval/Email File is required ');
                return FALSE;
            } else {
                return TRUE;
            }
            
        }
    }
    
    function process_upload_misc_charges($booking_id) {
        if (!empty($booking_id)) {
            $this->form_validation->set_rules('misc', 'Miscellaneous', 'callback_validate_form_data');
            $this->form_validation->set_rules('remarks', 'Remarks', 'required|trim');
            $this->form_validation->set_rules('validate_approval_misc_charges_file', 'Approval File', 'callback_validate_approval_misc_charges_file');
            if ($this->form_validation->run() == TRUE) {
                $booking_details = $this->booking_model->get_bookings_count_by_any("assigned_vendor_id", array('booking_id' => $booking_id));
                if (!empty($booking_details)) {
                    if (!empty($booking_details[0]['assigned_vendor_id'])) {
                        $remarks = $this->input->post("remarks");
                        $vendor_details = $this->vendor_model->getVendorDetails('gst_no', array('id' => $booking_details[0]['assigned_vendor_id']));

                        $data = $this->input->post('misc');
                        
                        $template = array(
                            'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
                        );

                        $this->table->set_template($template);

                        $this->table->set_heading(array('Booking ID', 'Description', 'Vendor Charge', 'Partner Charge', 'Remarks'));

                        foreach ($data as $id => $value) {

                            if (!empty($value['description'])) {
                                $misc_charge_data = array();
                                $misc_charge_data['description'] = $value['description'];
                                $misc_charge_data['vendor_basic_charges'] = $value['vendor_charge'];
                                if (!empty($vendor_details[0]['gst_no'])) {
                                    
                                    $misc_charge_data['vendor_tax'] = $value['vendor_charge'] * DEFAULT_TAX_RATE / 100;
                                    
                                } else {
                                    
                                    $misc_charge_data['vendor_tax'] = 0;
                                }
                                $misc_charge_data['product_or_services'] = $value['product_or_services'];
                                $misc_charge_data['tax_rate'] = DEFAULT_TAX_RATE;
                                $misc_charge_data['partner_charge'] = $value['partner_charge'];
                                $misc_charge_data['remarks'] = $remarks;
                                $misc_charge_data['booking_id'] = $booking_id;
                                if(!empty($this->input->post("approval_file"))){
                                    $misc_charge_data['approval_file'] = $this->input->post("approval_file");
                                }
                                
                                $this->booking_model->update_misc_charges(array('id' => $id), $misc_charge_data);

                                $this->table->add_row($booking_id, $value['description'], $value['vendor_charge'], $value['partner_charge'], $remarks);
                            } 
                        }
                        
                        $this->session->set_userdata(array('success' => "Charges Updated Successfully"));
                        redirect(base_url()."employee/service_centre_charges/update_misc_charges/".$booking_id);
                    } else {
                        $this->session->set_userdata(array('error' => "Booking is not assigned to any Vendor"));
                        redirect(base_url()."employee/service_centre_charges/update_misc_charges/".$booking_id);
                    }
                } else {
                    $this->session->set_userdata(array('error' => "Booking ID is not exist"));
                     redirect(base_url()."employee/service_centre_charges/update_misc_charges/".$booking_id);
                }
            } else {

                $this->session->set_userdata(array('error' => validation_errors()));
                 redirect(base_url()."employee/service_centre_charges/update_misc_charges/".$booking_id);
            }
        } else {
            $this->session->set_userdata(array('error' => "Booking ID is not exist"));
            redirect(base_url() . "employee/service_centre_charges/add_miscellaneous_charges");
        }
    }

    function validate_form_data(){
        $data = $this->input->post('misc');
        $is_validate = false;
        $m = array();
        foreach ($data as $key => $value) {
           
            if(empty($value['product_or_services'])){
                array_push($m,"Product Or Sevice");
                $is_validate = true;
            }
            if(empty($value['description'])){
                $is_validate = true;
                if(empty($m)){
                     array_push($m,"Description");
                } else {
                    array_push($m,"/Description");
                }
               
                break;
            }
            
        }
      
        if($is_validate){
             $this->form_validation->set_message('validate_form_data', implode(",", $m). ", All are mandatory");
             return false;
        } else {
           return true;
        }
    }
    
    function cancel_misc_charges($id, $booking_id){
        log_message('info', __METHOD__. "id " .$id);
        if(!empty($id)){
            $this->booking_model->update_misc_charges(array('id' => $id), array('active' => 0));
            
            $actor = $next_action = NULL;
                        
            $this->notify->insert_state_change($booking_id, MISC_CHARGES_REMOVED,  _247AROUND_PENDING, "", 
                                $this->session->userdata('id'), $this->session->userdata('employee_id'),
                    $actor, $next_action, _247AROUND);
            echo "success";
        } else {
            echo "Failed";
        }
    }
    
    /**
     * @desc: This function is used to get the form to add new service category
     * @params: void
     * @return: view
     * 
     */
     public function add_service_category() {
        $services = $this->booking_model->selectservice();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/add_new_booking_request_type', array('services' => $services));
    }
    
    /**
     * @desc: This function is used to add service category data from form to database table
     * @params: void
     * @return: prints message if data entered successfully or not 
     * 
     */

    public function process_add_service_category() {


        $this->form_validation->set_rules('category', 'category', 'required');
        $this->form_validation->set_rules('service', 'service', 'required');
        $this->form_validation->set_rules('product', 'product', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = array(
                'product_or_services' => $this->input->post('product'),
                'service_id' => $this->input->post('service'),
                'service_category' => $this->input->post('category')
            );
            $status = $this->service_centre_charges_model->insert_service_category($data);
            if (!empty($status)) {
                log_message("info", __METHOD__ . " Data Entered Successfully");
                $this->session->set_userdata('success', 'Data Entered Successfully');
                redirect(base_url() . 'employee/service_centre_charges/process_add_service_category');
            } else {
                log_message("info", __METHOD__ . " Data Already Exists");
                $this->session->set_userdata('failed', 'Data Already Exists');
                redirect(base_url() . 'employee/service_centre_charges/process_add_service_category');
            }
        } else {
            $this->add_service_category();
        }
    }

    /**
     * @desc: This function is used to get the form to add new category and capacity of a service
     * @params: void
     * @return: view
     * 
     */
    public function add_new_category() {
        $services = $this->booking_model->selectservice();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/add_new_category_capacity', array('services' => $services));
    }
    
    /**
     * @desc: This function is used to add the data from category capacity form to database table
     * @params: void
     * @return: prints message if data successfully added or already exists
     * 
     */

    public function process_add_new_category() {

        $this->form_validation->set_rules('category', 'category', 'required');
        $this->form_validation->set_rules('service', 'service', 'required');

        if ($this->form_validation->run() == TRUE) {
            $app_data = array(
                'service_id' => $this->input->post('service'),
                'category' => trim($this->input->post('category')),
                'capacity' => trim($this->input->post('capacity'))
            );

            $status = $this->service_centre_charges_model->insert_appliance_detail($app_data);
            if (!empty($status)) {

                log_message("info", __METHOD__ . " Data Entered Successfully");
                $this->session->set_userdata('success', 'Data Entered Successfully');
                redirect(base_url() . 'employee/service_centre_charges/process_add_new_category');
            } else {


                log_message("info", __METHOD__ . " Data Already Exists");
                $this->session->set_userdata('failed', 'Data Already Exists');
                redirect(base_url() . 'employee/service_centre_charges/process_add_new_category');
            }
        } else {
            $this->add_new_category();
        }
    }
    
    /**
     * @desc: This function is used to get service category mapping view data in a tabular format
     * @params: void
     * @return: view
     * 
     */

    public function appliance_data_view() {
        $this->miscelleneous->load_nav_header();
        $data['appliance_data'] = $this->service_centre_charges_model->get_appliance_data();
        $this->load->view('employee/service_category_mapping_view', $data);
    }
    
    /**
     * @desc: This function is used to update service, category from service category mapping tabular view 
     * @params: void
     * @return: prints message if data is updated or already exists
     * 
     */

    public function update_appliance() {


        $this->form_validation->set_rules('service_id', 'service_id', 'required');
        $this->form_validation->set_rules('category', 'category', 'required');
        $this->form_validation->set_rules('rowid', 'rowid', 'required');


        if ($this->form_validation->run() == TRUE) {

            $id = $this->input->post('rowid');

            $data = array(
                'service_id' => $this->input->post('service_id'),
                'capacity' => ($this->input->post('capacity')),
                'category' => trim($this->input->post('category')),
                'create_date' => date('Y-m-d H:i:s')
            );

            $status = $this->service_centre_charges_model->update_appliance_detail($id, $data);
            if (!empty($status)) {

                log_message("info", __METHOD__ . " Data Updated");
                $this->session->set_userdata('success', 'Data Updated');
                redirect(base_url() . 'employee/service_centre_charges/appliance_data_view');
            } else {


                log_message("info", __METHOD__ . " Data Already Exists");
                $this->session->set_userdata('failed', 'Data Already Exists');
                redirect(base_url() . 'employee/service_centre_charges/appliance_data_view');
            }
        } else {

            $this->add_new_category();
        }
    }
    
    /**
     * @desc: This function is used to get the service category data view in a tabular format 
     * @params: void
     * @return: view
     * 
     */

    public function service_category_data_view() {
        $this->miscelleneous->load_nav_header();
        $data['service_category_data'] = $this->service_centre_charges_model->get_service_category_data();
        $this->load->view('employee/service_category_data_view', $data);
    }

    /**
     * @desc: This function is used to update service category from the service category tabular view
     * @params: void
     * @return: prints message if data is updated or already exists
     * 
     */
    public function update_service_category() {


        $this->form_validation->set_rules('service', 'service', 'required');
        $this->form_validation->set_rules('category', 'category', 'required');
        $this->form_validation->set_rules('product', 'product', 'required');
        $this->form_validation->set_rules('rowid', 'rowid', 'required');


        if ($this->form_validation->run() == TRUE) {

            $id = $this->input->post('rowid');

            $data = array(
                'service_id' => $this->input->post('service'),
                'service_category' => trim($this->input->post('category')),
                'product_or_services' => ($this->input->post('product')),
                'create_date' => date('Y-m-d H:i:s')
            );

            $status = $this->service_centre_charges_model->update_service_category_detail($id, $data);
            if (!empty($status)) {

                log_message("info", __METHOD__ . " Data Updated");
                $this->session->set_userdata('success', 'Data Updated');
                redirect(base_url() . 'employee/service_centre_charges/service_category_data_view');
            } else {


                log_message("info", __METHOD__ . " Data Already Exists");
                $this->session->set_userdata('failed', 'Data Already Exists');
                redirect(base_url() . 'employee/service_centre_charges/service_category_data_view');
            }
        } else {

            $this->service_category_data_view();
        }
    }
    
    /**
     * @desc: This function is used to get services for the drop box
     * @params: void
     * @return: services
     * 
     */

    function get_services() {

        $services = $this->booking_model->selectservice();
        $service_id = $this->input->post('service_id');

        $option = '<option selected disabled>Select Appliance</option>';

        foreach ($services as $value) {
            $option .= "<option value='" . $value->id . "'";
            if ($service_id == $value->id) {
                $option .= " selected ";
            }
            $option .= " > ";
            $option .= $value->services . "</option>";
        }

        echo $option;
    }
    
    /**
     * @desc: This function is used to get the list of all appliances in a tabular format
     * @params: void
     * @return: view
     * 
     */

    public function appliance_list() {
        $this->miscelleneous->load_nav_header(); 
        $data['appliance_name'] = $this->booking_model->selectservice();
        $this->load->view('employee/add_appliance_view', $data);
    }
    
    /**
     * @desc: This function is used to get the form to add new appliance name
     * @params: void
     * @return: view
     * 
     */

    public function add_new_appliance_name() {
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/add_appliance_name_form');
    }
    
    /**
     * @desc: This function is used to add the appliance name to the database table from the add new appliance form
     * @params: void
     * @return: prints message if the data is added or already exists
     * 
     */

    public function process_add_new_appliance_name() {


        $this->form_validation->set_rules('appliance', 'appliance', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = array(
                'services' => trim($this->input->post('appliance')),
                'isBookingActive' => 1
            );

            $status = $this->service_centre_charges_model->insert_appliance_name($data);
            if (!empty($status)) {

                log_message("info", __METHOD__ . " Data Entered Successfully");
                $this->session->set_userdata('success', 'Data Entered Successfully');
                redirect(base_url() . 'employee/service_centre_charges/appliance_list');
            } else {


                log_message("info", __METHOD__ . " Data Already Exists");
                $this->session->set_userdata('failed', 'Data Already Exists');
                redirect(base_url() . 'employee/service_centre_charges/add_new_appliance_name');
            }
        } else {
            $this->add_new_appliance_name();
        }
    }
    
    /**
     * @desc: This function is used to update appliance name in the appliance list tabular view
     * @params: void
     * @return: prints message if the data is updated or exists already
     * 
     */

    public function update_appliance_name() {


        $this->form_validation->set_rules('appliance', 'appliance', 'required');
        $this->form_validation->set_rules('rowid', 'rowid', 'required');


        if ($this->form_validation->run() == TRUE) {
            $id = $this->input->post('rowid');
            $data = array(
                'services' => trim($this->input->post('appliance')),
            );

            $status = $this->service_centre_charges_model->update_appliance_name($id, $data);
            if (!empty($status)) {

                log_message("info", __METHOD__ . " Data Updated");
                $this->session->set_userdata('success', 'Data Updated');
                redirect(base_url() . 'employee/service_centre_charges/appliance_list');
            } else {


                log_message("info", __METHOD__ . " Data Already Exists");
                $this->session->set_userdata('failed', 'Data Already Exists');
                redirect(base_url() . 'employee/service_centre_charges/appliance_list');
            }
        } else {

            $this->appliance_list();
        }
    }
    
     /**
     * @desc: This function is used to get the tabular view for all entity roles
     * @params: void
     * @return: view
     * 
     */

    public function entity_role_data() {
        $this->miscelleneous->load_nav_header();
        $data['entity_role_data'] = $this->service_centre_charges_model->get_entity_role_data();
        $this->load->view('employee/entity_role_data_view', $data);
    }
    
    /**
     * @desc: This function is used to get the form to add new entity role
     * @params: void
     * @return: view
     * 
     */

    public function add_new_entity_role() {
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/entity_role_form');
    }
    
    /**
     * @desc: This function is used to add data from entity role form to database table
     * @params: void
     * @return: prints message whether data added successfully or data exists already
     * 
     */

    public function process_add_new_entity_role() {


        $this->form_validation->set_rules('entity_type', 'entity_type', 'required');
        $this->form_validation->set_rules('department', 'department', 'required');
        $this->form_validation->set_rules('role', 'role', 'required');


        if ($this->form_validation->run() == TRUE) {
            $data = array(
                'entity_type' => $this->input->post('entity_type'),
                'department' => trim($this->input->post('department')),
                'role' => trim($this->input->post('role')),
                'is_filter_applicable' => trim($this->input->post('filter')),
                'create_date' => date('Y-m-d H:i:s')
            );

            $status = $this->service_centre_charges_model->insert_entity_role_detail($data);
            if (!empty($status)) {

                log_message("info", __METHOD__ . " Data Entered Successfully");
                $this->session->set_userdata('success', 'Data Entered Successfully');
                redirect(base_url() . 'employee/service_centre_charges/process_add_new_entity_role');
            } else {


                log_message("info", __METHOD__ . " Data Already Exists");
                $this->session->set_userdata('failed', 'Data Already Exists');
                redirect(base_url() . 'employee/service_centre_charges/process_add_new_entity_role');
            }
        } else {
            $this->add_new_entity_role();
        }
    }
    
    /**
     * @desc: This function is used to update the already existing entity role from entity role tabular view
     * @params: void
     * @return: prints message whether data is updated or exists already
     * 
     */

    public function update_entity_role() {


        $this->form_validation->set_rules('entity_type', 'entity_type', 'required');
        $this->form_validation->set_rules('department', 'department', 'required');
        $this->form_validation->set_rules('role', 'role', 'required');
        $this->form_validation->set_rules('rowid', 'rowid', 'required');


        if ($this->form_validation->run() == TRUE) {
            $id = $this->input->post('rowid');
            $data = array(
                'entity_type' => $this->input->post('entity_type'),
                'department' => trim($this->input->post('department')),
                'role' => trim($this->input->post('role')),
                'is_filter_applicable' => ($this->input->post('filter')),
                'create_date' => date('Y-m-d H:i:s')
            );

            $status = $this->service_centre_charges_model->update_entity_role_detail($id, $data);
            if (!empty($status)) {

                log_message("info", __METHOD__ . " Data Updated");
                $this->session->set_userdata('success', 'Data Updated');
                redirect(base_url() . 'employee/service_centre_charges/entity_role_data');
            } else {


                log_message("info", __METHOD__ . " Data Already Exists");
                $this->session->set_userdata('failed', 'Data Already Exists');
                redirect(base_url() . 'employee/service_centre_charges/entity_role_data');
            }
        } else {

            $this->entity_role_data();
        }
    }
    
    /**
     * @desc: This function is used to load form for add bank
     * @params: void
     * @return: view
    */
    public function add_bank(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/add_bank_form');
    }
    
    /**
     * @desc: This function is used to save bank detail
     * @params: form data
     * @return: view
    */
    public function process_add_bank(){
        $bank_name = $this->input->post("bank_name");
        $data = array('bank_name'=>$bank_name);
        $response = $this->service_centre_charges_model->insert_bank_detail($data);
        if(empty($response)){
            $this->session->set_userdata('error', 'Bank name already exist');
        }
        else{
            $this->session->set_userdata('success', 'Bank detail added successfully.');
        }
        redirect(base_url() . 'employee/service_centre_charges/add_bank');
    }
    
     /**
     * @desc: This function is used to get bank details in data table format
     * @params: void
     * @return: json
    */
    public function get_bank_details(){
        $post_data = array('length' =>$this->input->post('length'),
                           'start' =>$this->input->post('start'),
                           'order' => $this->input->post('order'),
                           'draw' => $this->input->post('draw'),
                           'search_value' => trim($this->input->post('search')['value'])
                        );
        
        $post_data['column_search'] = array('bank_name');
        $post_data['column_order'] = array('bank_name'=>'ASC');
        $list = $this->service_centre_charges_model->get_bank_detail('*', $post_data);
        
         log_message('info', __FUNCTION__ . print_r($list, true));
        
        $no = $post_data['start'];
        $data = array();
        foreach ($list as $bank_list) {
            $row = array();
            $no++;
            $row[] =  $no;
            $row[] =  $bank_list->bank_name;
            $row[] =  $bank_list->create_date;
            if($bank_list->is_active == '1'){
                $row[] =  '<a class="btn btn-xs btn-success" href="javascript:void(0);" title="Click to Deactivate Bank" onclick="update_bank_detail('.$bank_list->id.', 0)">Active</a>';
            }
            else{
                $row[] =  '<a class="btn btn-xs btn-warning" href="javascript:void(0);" title="Click to Activate Bank" onclick="update_bank_detail('.$bank_list->id.', 1)">Deactive</a>';
            }
            $data[] = $row;
        }
        
        $output = array(
            "draw" => $post_data['draw'],
            "recordsTotal" =>$this->reusable_model->count_all_result('bank_details'),
            "recordsFiltered" => $this->service_centre_charges_model->count_filtered_bank_detail('id', $post_data),
            "data" => $data,
        );
        
        echo json_encode($output);
    }
    
     /**
     * @desc: This function is used to activate and deactivate bank detail
     * @params: void
     * @return: boolean
    */
    public function update_bank_details(){
        $data = array(
            'is_active' =>$this->input->post('status')
        ); 
        $where =array(
            'id'=> $this->input->post('id')
        );
        $response = $this->service_centre_charges_model->update_bank_details($where, $data);
        echo $response;
    }
}
