<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

require_once BASEPATH . 'libraries/spout-2.4.3/src/Spout/Autoloader/autoload.php';

class Do_background_process extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
	parent::__Construct();

	$this->load->helper(array('form', 'url'));
	$this->load->model('booking_model');
	$this->load->library('booking_utilities');
	$this->load->library('asynchronous_lib');
	$this->load->model('vendor_model');
	$this->load->library('s3');
	$this->load->library('email');
    }

    /**
     *  @desc : Function to assign vendors for pending bookings,
     *  @param : service center
     *  @return : void
     */
    function assign_booking() {
	log_message('info', "Entering: " . __METHOD__);

	$service_center = $this->input->post('service_center');
	foreach ($service_center as $booking_id => $service) {
	    if ($service != "Select") {
		log_message('info', "Booking ID: " . $booking_id . ", Service centre: " . $service);

		//Assign service centre
		$this->booking_model->assign_booking($booking_id, $service);

		//Prepare job card
		$this->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);

		//Send mail to vendor, no Note to vendor as of now
		$message = "";
		$this->booking_utilities->lib_send_mail_to_vendor($booking_id, $message);
	    }
	}
    }
    
    /**
     * @desc: this is used to upload asynchronouly data from current uploaded excel file. 
     */
    function upload_pincode_file(){
    	$mapping_file['pincode_mapping_file'] = $this->vendor_model->getLatestVendorPincodeMappingFile();
    	
    	$reader = ReaderFactory::create(Type::XLSX);
    	$reader->open("/tmp/" . $mapping_file['pincode_mapping_file'][0]['file_name']);
    	$count = 1;
	    $pincodes_inserted = 0;
	    $err_count = 0;
	    $header_row = FALSE;

	    $rows = array();
	    foreach ($reader->getSheetIterator() as $sheet) {
	        foreach ($sheet->getRowIterator() as $row) {
		        if ($count > 0) {
		            if ($count % 1000 == 0) {
			            if (!$header_row) {
			            //header row to be removed for the first iteration
			            array_shift($rows);

			            $header_row = TRUE;
		        	}

			        //call insert_batch function for $rows..
			        $this->vendor_model->insert_vendor_pincode_mapping_temp($rows);
		        	$pincodes_inserted += count($rows);
			        //echo date("Y-m-d H:i:s") . "=> " . $pincodes_inserted . " pincodes added\n";
			        unset($rows);
			        $rows = array();

			        //reset count
			        $count = 0;
		        }

		        $data['Vendor_Name'] = $row[0];
		        $data['Vendor_ID'] = $row[1];
		        $data['Appliance'] = $row[2];
		        $data['Appliance_ID'] = $row[3];
		        $data['Brand'] = $row[4];
		        $data['Area'] = $row[5];
		        $data['Pincode'] = $row[6];
		        $data['Region'] = $row[7];
		        $data['City'] = $row[8];
		        $data['State'] = $row[9];

		        array_push($rows, $data);
		    }
		    $count++;
	    }

	    //insert remaining rows
	    $this->vendor_model->insert_vendor_pincode_mapping_temp($rows);
	    //echo date("Y-m-d H:i:s") . "=> " . ($count - 1) . " records added\n";
	    $pincodes_inserted += count($rows);
	}

	$reader->close();

	if ($err_count === 0) {
	    //Drop the original pincode mapping table and rename the temp table with new pincodes mapping
	    $result = $this->vendor_model->switch_temp_pincode_table();

	    if ($result)
		$data['table_switched'] = TRUE;
	}
    	
    }

}
