<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** PHPExcel_IOFactory */
//require_once dirname(__FILE__) . '/../Classes/PHPExcel/IOFactory.php';

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('max_execution_time', 3600); //3600 seconds = 60 minutes

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
	$this->load->model('service_centre_charges_model');

	if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
	    return TRUE;
	} else {
	    redirect(base_url() . "employee/login");
	}
    }

    public function index() {
	$this->load->view('employee/header');
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

	$this->load->view('employee/header');
	$this->load->view('employee/service_centre_charges_summary', $data);
    }

    /**
     *  @desc : This function is to display service center charges
     *  @param : void
     *  @return : all the services to view
     */
    public function display_service_centre_charges() {
	$services = $this->booking_model->selectservice();

	$this->load->view('employee/header');
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

	$this->load->view('employee/header');
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
	    $inputFileName = $_FILES['file']['tmp_name'];
	    $this->upload_excel($inputFileName, "price");
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
			array_push($rows, $data);
		    }
		} else if ($type == "tax") {
		    // Get Data from top 2 rows in excel file
		    if ($count > 2) {
			$data = $this->set_tax_rows_data($row);
			array_push($rows, $data);
		    }
		}
		$count++;
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
	    $return = $return = $this->partner_model->insert_data_in_batch($table_name, $rows);
	}
	if ($return == 1) {
	    $this->redirect_upload_form();
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
	$data['partner_id'] = $row[1];
	$data['state'] = $row[2];
	$data['service_id'] = $row[4];
	$data['category'] = $row[5];
	$data['capacity'] = $row[6];
	$data['service_category'] = $row[7];
	$data['product_or_services'] = $row[8];
	$data['product_type'] = $row[9];
	$data['tax_code'] = $row[10];
	$data['active'] = $row[12];
	$data['check_box'] = $row[13];
	$data['vendor_basic_charges'] = $row[14];
	$data['vendor_tax_basic_charges'] = $row[15];
	$data['vendor_total'] = $row[16];
	$data['around_basic_charge'] = $row[17];
	$data['around_tax_basic_charges'] = $row[18];
	$data['around_total'] = $row[19];
	$data['customer_total'] = $row[20];
	$data['partner_payable_basic'] = $row[21];
	$data['partner_payable_tax'] = $row[22];
	$data['partner_net_payable'] = $row[23];
	$data['customer_net_payable'] = $row[24];

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
       
        $this->load->view('employee/header');
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
}
