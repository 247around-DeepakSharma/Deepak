<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

require_once BASEPATH . 'libraries/spout-2.4.3/src/Spout/Autoloader/autoload.php';

/**
 * Partner Utilities library
 *
 * @author anujaggarwal
 */
class partner_utilities {

    function __Construct() {
	$this->My_CI = & get_instance();

	$this->My_CI->load->model('partner_model');
    }

    /**
     *  @desc  : Validate Excel file
     *  @param : void
     *  @return : void
     */
    function validate_file($file) {
	if (!empty($file['file']['name'])) {
	    $pathinfo = pathinfo($file["file"]["name"]);
	    if (($pathinfo['extension'] == 'xlsx' || $pathinfo['extension'] == 'xls') && $file['file']['size'] > 0) {
		return true;
	    } else {
		$data['error'] = "Please Select Valid Excel File";
		return $data;
	    }
	} else {
	    $data['error'] = "Please Select Excel File";
	    return $data;
	}
    }

}
