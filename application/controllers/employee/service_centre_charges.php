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
        $this->load->view('employee/header/' . $this->session->userdata('user_group'));
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

        $this->load->view('employee/header/' . $this->session->userdata('user_group'));
        $this->load->view('employee/service_centre_charges_summary', $data);
    }

    /**
     *  @desc : This function is to display service center charges
     *  @param : void
     *  @return : all the services to view
     */
    public function display_service_centre_charges() {
        $services = $this->booking_model->selectservice();

        $this->load->view('employee/header/' . $this->session->userdata('user_group'));
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
        $this->load->view('employee/header/' . $this->session->userdata('user_group'));
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
                $this->miscelleneous->update_file_uploads($tmpFile, _247AROUND_SF_PRICE_LIST,FILE_UPLOAD_SUCCESS_STATUS);
                $userSession = array('success' => "File Uploaded Successfully");
                $this->session->set_userdata($userSession);
                redirect(base_url() . "employee/service_centre_charges/upload_excel_form");
            }else{
                //save file and upload on s3
                $this->miscelleneous->update_file_uploads($tmpFile, _247AROUND_SF_PRICE_LIST,FILE_UPLOAD_FAILED_STATUS);
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

        $this->load->view('employee/header/' . $this->session->userdata('user_group'));
        $this->load->view('employee/pricingtable', $data);
    }

    /**
     *  @desc  : filter pricing table
     *  @param : source, city, service id, category, capacity, appliances
     *  @return : void
     */
//    function get_pricing_details() {
//        $partner_code = $this->input->post('source');
//        $data['city'] = $this->input->post('city');
//        $data['service_id'] = $this->input->post('service_id');
//        $data['category'] = $this->input->post('category');
//        $data['capacity'] = $this->input->post('capacity');
//        $data['appliances'] = $this->input->post('appliances');
//        $data['source'] = $this->booking_model->get_price_mapping_partner_code($partner_code);
//
//        $price['price'] = $this->service_centre_charges_model->get_pricing_details($data);
//        $table = $this->load->view('employee/pricingtable', $price);
//
//        print_r($table);
//    }

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
        $this->load->view('employee/header/' . $this->session->userdata('user_group'));
        $this->load->view('employee/show_partner_services_price', $data);
    }

    /**
     *  @desc  : This function is used to show the partner services price based on dropdown selection
     *           through ajax call
     *  @param : void
     *  @return : array()
     */
    function show_partner_price() {
        $data['partner_id'] = $this->input->post('partner_id');
        $data['service_id'] = $this->input->post('service_id');
        $data['service_category'] = $this->input->post('service_category');
        $partner['price_data'] = $this->service_centre_charges_model->get_partner_price_data($data);
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
            $appliance_file = "Partner-Appliance-Details-" . date('Y-m-d-H-i-s') . '.xlsx';
            move_uploaded_file($tmpFile, TMP_FOLDER . $appliance_file);


            //Processing File 
            $this->upload_excel(TMP_FOLDER . $appliance_file, "appliance", $flag);

            //Adding Details in File_Uploads table as well

            $data['file_name'] = $appliance_file;
            $data['file_type'] = _247AROUND_PARTNER_APPLIANCE_DETAILS;
            $data['agent_id'] = $this->session->userdata('id');
            $insert_id = $this->partner_model->add_file_upload_details($data);
            if (!empty($insert_id)) {
                //Logging success
                log_message('info', __FUNCTION__ . ' Added details to File Uploads ' . print_r($data, TRUE));
            } else {
                //Loggin Error
                log_message('info', __FUNCTION__ . ' Error in adding details to File Uploads ' . print_r($data, TRUE));
            }

            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "vendor-partner-docs/" . $appliance_file;
            $this->s3->putObjectFile(TMP_FOLDER . $appliance_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            //Logging
            log_message('info', __FUNCTION__ . ' File has been uploaded in S3');

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
                            if($newRowData['service_category'] !== REPEAT_BOOKING_TAG  && $newRowData['service_category'] !== REPAIR_OOW_TAG && $newRowData['sf_percentage'] == 0){
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
                $this->notify->sendEmail("booking@247around.com", $to, $cc, "", $subject, $msg, "");
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
        $final_data['capacity'] = $newRowData['capacity'];
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

}
