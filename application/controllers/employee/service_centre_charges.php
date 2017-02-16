<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}


define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** PHPExcel_IOFactory */
//require_once dirname(__FILE__) . '/../Classes/PHPExcel/IOFactory.php';

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('max_execution_time', 36000); //3600 seconds = 60 minutes

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

require_once BASEPATH . 'libraries/spout-2.4.3/src/Spout/Autoloader/autoload.php';

class service_centre_charges extends CI_Controller {

    function __Construct() {
	parent::__Construct();
	$this->load->helper(array('form', 'url'));
	$this->load->helper('download');

	$this->load->library('form_validation');
	$this->load->library('s3');
	$this->load->library('PHPReport');
	$this->load->library('partner_sd_cb');
	$this->load->library('partner_utilities');

	$this->load->model('user_model');
	$this->load->model('booking_model');
        $this->load->model('partner_model');
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
	$this->load->view('employee/header/'.$this->session->userdata('user_group'));
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

	$this->load->view('employee/header/'.$this->session->userdata('user_group'));
	$this->load->view('employee/service_centre_charges_summary', $data);
    }

    /**
     *  @desc : This function is to display service center charges
     *  @param : void
     *  @return : all the services to view
     */
    public function display_service_centre_charges() {
	$services = $this->booking_model->selectservice();

	$this->load->view('employee/header/'.$this->session->userdata('user_group'));
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
    function upload_excel_form($data = '') {

	$this->load->view('employee/header/'.$this->session->userdata('user_group'));
	$this->load->view('employee/upload_service_price', $data);
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
            log_message('info',__FUNCTION__.' Processing of Service Price List Excel File started');
            
            //Making process for file upload
            $tmpFile = $_FILES['file']['tmp_name'];
            $price_file = "Service-Price-List-".date('Y-m-d-H-i-s').'.xlsx';
            move_uploaded_file($tmpFile, TMP_FOLDER . $price_file);
            
            //Processing File
	    $this->upload_excel(TMP_FOLDER . $price_file, "price");
            
            //Adding Details in File_Uploads table as well
            
            $data['file_name'] = $price_file;
            $data['file_type'] = _247AROUND_SF_PRICE_LIST;
            $data['agent_id'] = $this->session->userdata('id');
            $insert_id = $this->partner_model->add_file_upload_details($data);
            if(!empty($insert_id)){
            //Logging success
                log_message('info',__FUNCTION__.' Added details to File Uploads '.print_r($data,TRUE));
            }else{
            //Loggin Error
                log_message('info',__FUNCTION__.' Error in adding details to File Uploads '.print_r($data,TRUE));
            }
            
            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "vendor-partner-docs/" . $price_file;
            $this->s3->putObjectFile(TMP_FOLDER . $price_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            //Logging
            log_message('info',__FUNCTION__.' File has been uploaded in S3');
            
	    $this->redirect_upload_form();
	} else {
	    $this->upload_excel_form($return);
	}
    }

    /**
     *  @desc  : Extract rows data from excel
     *  @param : input file and type(price for service price and tax for tax rate)
     *  @return : void
     */
    function upload_excel($inputFileName, $type) {
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
                        print_r($data);
                            array_push($rows, $data);
                        }
		} else if ($type == "tax") {
		    // Get Data from top 2 rows in excel file
		    if ($count > 2) {
			$data = $this->set_tax_rows_data($row);
			array_push($rows, $data);
		    }
		} else if($type == "appliance"){
                    log_message('info','Inside upload excel');
                    // Get Data from top 2nd rows in excel file
		    if ($count > 1) {
                        log_message('info','Inside count');
			$data = $this->set_partner_appliance_rows_data($row);
                        
                        //Validating Data - For Array its Valid else Invalid Entry
                        if(!is_array($data)){
                            //Logging Error
                            log_message('info',__FUNCTION__.' Error - Due to Empty Column values in File');
                            //Closing Excel File
                            $reader->close();
                            //Redirecting  to Upload page
                            $this->session->set_flashdata('file_error','Error in Uploading PARTNER APPLIANCE DETAILS File due to Empty Column values');
                            redirect(base_url() . "employee/service_centre_charges/upload_excel_form");
                            exit;
                        }else{
                            array_push($rows, $data);
                        }
		    }
                    
                }
		$count++;
	    }
            //Validation for Empty File
            if($count == 1 || $count == 2) {
                //Logging Error
                log_message('info', __FUNCTION__ . ' Error - Empty File Uploaded');
                //Closing Excel File
                $reader->close();
                //Redirecting  to Upload page
                $this->session->set_flashdata('file_error', 'Empty File Uploaded - Please check.');
                redirect(base_url() . "employee/service_centre_charges/upload_excel_form");
                exit;
            }

            $this->insert_data_list($type, $rows);
	}
	$reader->close();
    }

    /**
     *  @desc  : This method is used to insert data into both service_price and tax_rates_by_states tables.
     *  @param : Excel file type and array(data)
     *  @return : void
     */
    function insert_data_list($type, $rows) {
	$table_name = '';
	$return = 0;
	if ($type == "price") {
	    $table_name = "service_centre_charges";
	    $return = $this->partner_model->insert_data_in_batch($table_name, $rows);
	} else if ($type == "tax") {
	    $table_name = 'tax_rates_by_states';
	    $return = $this->partner_model->insert_data_in_batch($table_name, $rows);
	} else if ($type == "appliance"){
            log_message('info','Inside insert data list');
            $table_name = 'partner_appliance_details';
	    $return = $this->partner_model->insert_data_in_batch($table_name, $rows);
            
        }
	if ($return == 1) {
//	    $this->redirect_upload_form();
	} else {
	    $output['error'] = "Error while uploading File";
	    $this->upload_excel_form($output);
	}
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
	$data['partner_id'] = isset($row[1])?$row[1]:'';
	$data['state'] = isset($row[2])?$row[2]:'';
	$data['service_id'] = isset($row[4])?$row[4]:'';
	$data['category'] = isset($row[5])?$row[5]:'';
	$data['capacity'] = isset($row[6])?$row[6]:'';
	$data['service_category'] = isset($row[7])?$row[7]:'';
	$data['product_or_services'] = isset($row[8])?$row[8]:'';
	$data['product_type'] = isset($row[9])?$row[9]:'';
	$data['tax_code'] = isset($row[10])?$row[10]:'';
	$data['active'] = isset($row[11])?$row[11]:'';
	$data['check_box'] = isset($row[12])?$row[12]:'';
	$data['vendor_basic_charges'] = isset($row[13])?$row[13]:'';
	$data['vendor_tax_basic_charges'] = isset($row[14])?$row[14]:'';
	$data['vendor_total'] = isset($row[15])?$row[15]:'';
	$data['around_basic_charges'] = isset($row[16])?$row[16]:'';
	$data['around_tax_basic_charges'] = isset($row[17])?$row[17]:'';
	$data['around_total'] = isset($row[18])?$row[18]:'';
	$data['customer_total'] = isset($row[20])?$row[20]:'';
	$data['partner_payable_basic'] = isset($row[21])?$row[21]:'';
	$data['partner_payable_tax'] = isset($row[22])?$row[22]:'';
	$data['partner_net_payable'] = isset($row[23])?$row[23]:'';
	$data['customer_net_payable'] = isset($row[24])?$row[24]:'';
	$data['pod'] = isset($row[25])?$row[25]:'';
        $data['vendor_basic_percentage'] = isset($row[26])?$row[26]:'';

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
    
    function show_pricing_tables(){
        $data = $this->service_centre_charges_model->get_service_city_source_all_appliances_details();
       
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/pricingtable', $data);
    }

    /**
     *  @desc  : filter pricing table
     *  @param : source, city, service id, category, capacity, appliances
     *  @return : void
     */
    
    function get_pricing_details(){
        $partner_code = $this->input->post('source');
        $data['city'] = $this->input->post('city');
        $data['service_id'] = $this->input->post('service_id');
        $data['category'] = $this->input->post('category');
        $data['capacity'] = $this->input->post('capacity');
        $data['appliances'] = $this->input->post('appliances');
        $data['source']  = $this->booking_model->get_price_mapping_partner_code($partner_code);
        
        $price['price'] = $this->service_centre_charges_model->get_pricing_details($data);
        $table = $this->load->view('employee/pricingtable', $price);

        print_r($table);
        
    }

    /**
     *  @desc  : Edit pricing table
     *  @param : 
     *  @return : void
     */

    function editPriceTable(){
    	$data['id'] = $this->input->post('id');
    	$data['check_box'] = $this->input->post('check_box');
    	$data['active'] = $this->input->post('active');
    	$data['vendor_svc_charge'] = $this->input->post('vendor_svc_charge');
    	$data['vendor_tax'] = $this->input->post('vendor_tax');
    	$data['around_svc_charge'] = $this->input->post('around_svc_charge');
    	$data['around_tax'] = $this->input->post('around_tax');
    	$data['customer_total'] = $this->input->post('customer_total');
    	$data['partner_payment'] = $this->input->post('partner_payment');
    	$data['customer_charges'] = $this->input->post('customer_charges');

    	$this->service_centre_charges_model->editPriceTable($data);

    	echo "success";
    }
    
    /**
     *  @desc  : This function is used to show the partner services price
     *  @param : void
     *  @return : void
     */
    
    function show_partner_service_price(){
        
        $data['partners'] = $this->partner_model->get_all_partner_source();
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/show_partner_services_price' , $data);
    }
    
     /**
     *  @desc  : This function is used to show the partner services price based on dropdown selection
     *           through ajax call
     *  @param : void
     *  @return : array()
     */
    
   
    function show_partner_price(){
        $data['price_mapping_id'] = $this->input->post('price_mapping_id');
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
    
    
    function get_partner_data(){
        if(isset($_POST['partner'])){
            $price_mapping_id = $this->input->post('partner');
            $services = $this->service_centre_charges_model->get_appliance_from_partner($price_mapping_id);

            $option = '<option selected disabled>Select Appliance</option>';

            foreach($services as $value)
            {
                $option .= "<option value='" . $value['id'] . "'";
                $option .=" > ";
                $option .= $value['services'] . "</option>";
            }

            echo $option;
        }
        
        if(isset($_POST['service_id'])){
            $service_id = $this->input->post('service_id');
            $service_category = $this->service_centre_charges_model->get_service_category_from_service_id($service_id);

            $option = '<option selected disabled>Select Service Category</option>';

            foreach($service_category as $value)
            {
                $option .= "<option value='" . $value['service_category'] . "'";
                $option .=" > ";
                $option .= $value['service_category'] . "</option>";
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
            log_message('info',__FUNCTION__.' Processing of Partner Appliance Excel File started');
            
            //Making process for file upload
            $tmpFile = $_FILES['file']['tmp_name'];
            $appliance_file = "Partner-Appliance-Details-".date('Y-m-d-H-i-s').'.xlsx';
            move_uploaded_file($tmpFile, TMP_FOLDER . $appliance_file);

            
            //Processing File 
	    $this->upload_excel(TMP_FOLDER . $appliance_file, "appliance");
            
            //Adding Details in File_Uploads table as well
            
            $data['file_name'] = $appliance_file;
            $data['file_type'] = _247AROUND_PARTNER_APPLIANCE_DETAILS;
            $data['agent_id'] = $this->session->userdata('id');
            $insert_id = $this->partner_model->add_file_upload_details($data);
            if(!empty($insert_id)){
            //Logging success
                log_message('info',__FUNCTION__.' Added details to File Uploads '.print_r($data,TRUE));
            }else{
            //Loggin Error
                log_message('info',__FUNCTION__.' Error in adding details to File Uploads '.print_r($data,TRUE));
            }
            
            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "vendor-partner-docs/" . $appliance_file;
            $this->s3->putObjectFile(TMP_FOLDER . $appliance_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            //Logging
            log_message('info',__FUNCTION__.' File has been uploaded in S3');
            
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
        log_message('info',__FUNCTION__);
        //Flag for checking validation -- Only Model can be Empty
        $empty_flag = FALSE;
	$data['partner_id'] = isset($row[0]) && !empty($row[0])?$row[0]:$empty_flag = TRUE;
	$data['service_id'] = isset($row[1]) && !empty($row[1])?$row[1]:$empty_flag = TRUE;
        //Sanitizing Brand Name
	$data['brand'] = isset($row[2]) && !empty($row[2])?preg_replace('/[^A-Za-z0-9 ]/', '', $row[2]):$empty_flag = TRUE;
	$data['category'] = isset($row[3]) && !empty($row[3])?$row[3]:$empty_flag = TRUE;
	$data['capacity'] = isset($row[4]) && !empty($row[4])?$row[4]:'';
	$data['model'] = isset($row[5]) && !empty($row[5])?$row[5]:'';
	$data['active'] = 1;
        
        if($empty_flag){
            return $empty_flag;
        }else{
            return $data;
        }
    }
}    