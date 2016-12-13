<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

ini_set('max_execution_time', 3600); //3600 seconds = 60 minutes

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

require_once '/Applications/MAMP/htdocs/aroundlocalhost/system/libraries/spout-2.4.3/src/Spout/Autoloader/autoload.php';

class converter extends CI_Controller {

    function __Construct() {
	parent::__Construct();
	$this->load->model('vendor_model');
    }

    function test() {
	echo "Controller " . __CLASS__ . " working fine..." . PHP_EOL;
    }

    function index() {

	$filePath = '/Users/anujaggarwal/Google Drive/Ideas/AroundHomz/Vendor Related/booking-related/service-centres-details/pincode mappings/';
	$fileName = 'all_india_pin_code_sanitized.xlsx';

	echo "Script started at: " . date("Y-m-d H:i:s") . PHP_EOL;

	$file_to_read = $filePath . $fileName;
	$reader = ReaderFactory::create(Type::XLSX);
	$reader->open($file_to_read);

	echo date("Y-m-d H:i:s") . "=> Inserting data from xls to db\n\n";

	$count = 1;
	$rows = array();
	foreach ($reader->getSheetIterator() as $sheet) {
	    foreach ($sheet->getRowIterator() as $row) {
		if ($count > 0) {
		    if ($count % 1000 == 0) {
			//call insert_batch function for $rows..
			$this->vendor_model->insert_india_pincode_in_batch($rows);
			echo date("Y-m-d H:i:s") . "=> " . $count . " records added\n";
			unset($rows);
			$rows = array();

			//reset count
			$count = 0;
		    }

		    $data['area'] = $row[0];
		    $data['  $datpincode'] = $row[1];
		    $data['division'] = $row[2];
		    $data['region'] = $row[3];
		    $data['taluk'] = $row[4];
		    $data['district'] = $row[5];
		    $data['state'] = $row[6];
		    array_push($rows, $data);
		}
		$count++;
	    }

	    //insert remaining rows
	    $this->vendor_model->insert_india_pincode_in_batch($rows);
	    echo date("Y-m-d H:i:s") . "=> " . ($count - 1) . " records added\n";
	}

	$reader->close();
    }

}

?>