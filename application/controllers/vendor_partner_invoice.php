<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

error_reporting(E_ALL);
ini_set('display_errors', '1');

//ini_set('include_path', '/Applications/MAMP/htdocs/aroundlocalhost/system/libraries');
ini_set('include_path', '/var/www/aroundhomzapp.com/public_html/system/libraries');

require_once('simple_html_dom.php');

//define('CONVERTAPI_KEY', '110761630');     //test account
define('CONVERTAPI_KEY', '278325305');    //247around account

/**
 * Description of vendor_partner_invoice
 *
 * @author anujaggarwal
 */
class vendor_partner_invoice extends CI_Controller {

    function __Construct() {
        parent::__Construct();

	$this->load->model('reporting_utils');
	$this->load->model('invoices_model');

	$this->load->library('PHPReport');
	$this->load->library('email');
	$this->load->library('s3');
    }

    /**
     * @input: void
     * @description:
     * @output: void
     */
    public function index() {
	echo PHP_EOL . __METHOD__ . "=> Success" . PHP_EOL;
    }

    /*
     * Two types of invoices are generated for vendors.
     * One is for the jobs where they collected money.
     * Second is for the partner-provided jobs where partner will pay directly
     * to 247around and vendor did the job for free.
     * This function is for the 1st type of invoices.
     *
     * Start date format = DD-MM-YYYY
     * End date format = DD-MM-YYYY
     */
    public function generate_non_partner_invoices_for_vendors($start_date, $end_date) {
	//log_message('info', __FUNCTION__);
	//echo $start_date, $end_date;

	$file_names = array();

	//Type A invoices
	$template = 'Vendor_Settlement_Template-A-v1.xlsx';
	//set absolute path to directory with template files
	$templateDir = __DIR__ . "/";

	//set config for report
	$config = array(
	    'template' => $template,
	    'templateDir' => $templateDir
	);

	$s_date = date("Y-m-d H:i:s", strtotime($start_date));
	$e_date = date("Y-m-d H:i:s", strtotime($end_date));

	//fetch all vendors (include inactive as well)
	$service_centers = $this->reporting_utils->find_all_service_centers();
	//echo print_r($service_centers, true);

	foreach ($service_centers as $sc) {
	    //log_message('info', "fetch pending bookings for service center id: " . $sc['id']);
	    $bookings_completed = $this->reporting_utils->get_completed_bookings_by_sc($sc['id'], $s_date, $e_date);
	    $count = count($bookings_completed);
	    //log_message('info', "Count: " . $count);

	    if ($count > 0) {
		//Find total charges for these bookings
		$tot_ch_rat = $this->get_total_charges_rating_for_non_partner_bookings($bookings_completed);

		//load template
		$R = new PHPReport($config);
		//A means it is for the 1st type of invoice as explained above
		//Make sure it is unique
		$invoice_id = $sc['sc_code'] . "-" . date("dMY") . "-A-" . rand(100, 999);

		$R->load(array(
		    array(
			'id' => 'meta',
			'data' => array('invoice_id' => $invoice_id,
			    'vendor_name' => $sc['name'], 'vendor_address' => $sc['address'],
			    'sd' => $start_date, 'ed' => $end_date, 'today' => date("d-M-Y"),
			    'count' => $count, 't_sc' => $tot_ch_rat['t_sc'],
			    't_asc' => $tot_ch_rat['t_asc'], 't_pc' => $tot_ch_rat['t_pc'],
			    't_rating' => $tot_ch_rat['t_avg_rating']
			),
			'format' => array(
			    'date' => array('datetime' => 'd/M/Y')
			)
		    ),
		    array(
			'id' => 'booking',
			'repeat' => true,
			'data' => $bookings_completed,
			//'minRows' => 2,
			'format' => array(
			    'create_date' => array('datetime' => 'd/M/Y'),
			    'total_price' => array('number' => array('prefix' => 'Rs. ')),
			)
		    ),
		    )
		);

		//Get populated XLS with data
		$output_file_dir = "/tmp/";
		//$output_file = "BookingsClosed-Repairs-" . $sc['sc_code'] . "-" . date('d-M-Y');
		$output_file = $invoice_id;
		$output_file_excel = $output_file_dir . $output_file . ".xlsx";
		//for xlsx: excel, for xls: excel2003
		$R->render('excel', $output_file_excel);

		//convert excel to pdf
		$output_file_pdf = $output_file_dir . $output_file . ".pdf";
		$cmd = "curl -F file=@" . $output_file_excel .
		    " http://do.convertapi.com/Excel2Pdf?apikey=" . CONVERTAPI_KEY . " -o " . $output_file_pdf;
		exec($cmd);

		//log_message('info', "Report generated with $count records");
		echo PHP_EOL . "Report generated with $count records" . PHP_EOL;
		//Send report via email
		$this->email->clear(TRUE);
		$this->email->from('booking@247around.com', '247around Team');
		//$this->email->to('anuj.aggarwal@gmail.com');
		$this->email->to($sc['owner_email']);
		$cc = "sales@247around.com, nits@247around.com, anuj@247around.com";
		$this->email->cc($cc);
		$this->email->bcc("anuj.aggarwal@gmail.com");

		$subject = "247around - " . $sc['name'] . " - Invoice for period: " . $start_date . " To " . $end_date;
		$this->email->subject($subject);

		$message = "Dear Partner,<br/><br/>";
		$message .= "Please find attached invoice for jobs completed between " . $start_date . " and " . $end_date . ". ";
		$message .= "Details with breakup by job, service category is attached. Also the service rating as given by customers is shown.<br/><br/>";
		$message .= "This invoice is for the jobs where payment was collected by " . $sc['name'] . ". Previous invoice sent on 5th April was for the jobs where 247around collected the payment.<br><br>";
		$message .= "Please review all the invoices and let us know if there are any discrepancies. Do verify your bank details mentioned in the earlier invoice as well.<br><br>";
		$message .= "Once we receive your confirmation, payments for jobs done till 29th Feb 2016 will be credited by 15th April 2016 by deducting 247around royalty charges towards you. ";
		$message .= "Settlement for jobs done from 1st to 31st March 2016 will be done by 30th April 2016.<br><br>";
		$message .= "Hope to have a long lasting working relationship with you.";
		$message .= "<br><br>With Regards,
                        <br>247around Team<br>
                        <br>247around is part of Businessworld Startup Accelerator & Google Bootcamp 2015
                        <br>Follow us on Facebook: www.facebook.com/247around
                        <br>Website: www.247around.com
                        <br>Playstore - 247around -
                        <br>https://play.google.com/store/apps/details?id=com.handymanapp";

		$this->email->message($message);
		$this->email->attach($output_file_pdf, 'attachment');

		//Save filenames to delete later on
		array_push($file_names, $output_file_excel);
		array_push($file_names, $output_file_pdf);

		$mail_ret = $this->email->send();
		if ($mail_ret) {
		    //log_message('info', __METHOD__ . ": Mail sent successfully");
		    echo "Mail sent successfully...............\n\n";
		} else {
		    log_message('error', __METHOD__ . ": Mail could not be sent");
		    echo "Mail could not be sent" . PHP_EOL;
		}

		//Upload Excel files to AWS
		//$bucket = 'bookings-collateral-test';
		$bucket = 'bookings-collateral';
		$directory_xls = "invoices-excel/" . $output_file . ".xlsx";
		$directory_pdf = "invoices-pdf/" . $output_file . ".pdf";

		$this->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PRIVATE);
		$this->s3->putObjectFile($output_file_pdf, $bucket, $directory_pdf, S3::ACL_PRIVATE);
		//Save this invoice info in table
		//TODO: Add other info in the table like period, amount, num of jobs etc
		$invoice_details = array(
		    'invoice_id' => $invoice_id,
		    'invoice_file_excel' => $output_file . '.xlsx',
		    'invoice_file_pdf' => $output_file . '.pdf',
		    'type' => 'A',
		    'vendor_partner' => 'vendor',
		    'vendor_partner_id' => $sc['id']
		);
		$this->invoices_model->insert_new_invoice($invoice_details);

		/*
		 * Update booking-invoice table to capture this new invoice against these bookings.
		 * Since this is a type A invoice, it would be stored as a vendor-debit invoice.
		 */
		$this->update_booking_invoice_mappings_repairs($bookings_completed, $invoice_id);
	    }

	    //break;
	}

	//Delete XLS files now
	foreach ($file_names as $file_name)
	    exec("rm -rf " . escapeshellarg($file_name));

	exit(0);
    }

    /*
     * Two types of invoices are generated for vendors.
     * One is for the jobs where they collected money.
     * Second is for the partner-provided jobs where partner will pay directly
     * to 247around and vendor did the job for free.
     * This function is for the 2nd type of invoices.
     *
     * Start date format = DD-MM-YYYY
     * End date format = DD-MM-YYYY
     */

    public function generate_partner_invoices_for_vendors($start_date, $end_date) {
	//log_message('info', __FUNCTION__);
	//echo $start_date, $end_date;

	$file_names = array();

	//Type B invoices
	$template = 'Vendor_Settlement_Template-B-v1.xlsx';
	//set absolute path to directory with template files
	$templateDir = __DIR__ . "/";

	//set config for report
	$config = array(
	    'template' => $template,
	    'templateDir' => $templateDir
	);

	$s_date = date("Y-m-d H:i:s", strtotime($start_date));
	$e_date = date("Y-m-d H:i:s", strtotime($end_date));

	//fetch all vendors (include inactive as well)
	$service_centers = $this->reporting_utils->find_all_service_centers();
	//echo print_r($service_centers, true);

	foreach ($service_centers as $sc) {
	    //log_message('info', "fetch pending bookings for service center id: " . $sc['id']);
	    $bookings_completed = $this->reporting_utils->get_completed_sd_bookings_by_sc($sc['id'], $s_date, $e_date);
	    $count = count($bookings_completed);
	    //log_message('info', "Count: " . $count);

	    if (($count > 0) && ($sc['id'] > 31)) {
		//Find total charges for these bookings
		$tot_ch_rat = $this->get_total_charges_rating_for_partner_bookings($bookings_completed);

		//load template
		$R = new PHPReport($config);
		//B means it is for the 1st type of invoice as explained above
		//Make sure it is unique
		$invoice_id = $sc['sc_code'] . "-" . date("dMY") . "-B-" . rand(100, 999);

		$R->load(array(
		    array(
			'id' => 'meta',
			'data' => array('invoice_id' => $invoice_id,
			    'vendor_name' => $sc['name'], 'vendor_address' => $sc['address'],
			    'sd' => $start_date, 'ed' => $end_date, 'today' => date("d-M-Y"),
			    'count' => $count, 'total' => $tot_ch_rat['total'],
			    't_rating' => $tot_ch_rat['t_avg_rating'],
			    'beneficiary_name' => $sc['beneficiary_name'],
			    'bank_account' => $sc['bank_account'],
			    'bank_name' => $sc['bank_name'], 'ifsc_code' => $sc['ifsc_code']
			),
			'format' => array(
			    'date' => array('datetime' => 'd/M/Y')
			)
		    ),
		    array(
			'id' => 'booking',
			'repeat' => true,
			'data' => $bookings_completed,
			//'minRows' => 2,
			'format' => array(
			    'create_date' => array('datetime' => 'd/M/Y'),
			    'total_price' => array('number' => array('prefix' => 'Rs. ')),
			)
		    ),
		    )
		);

		//Get populated XLS with data
		$output_file_dir = "/tmp/";
		//$output_file = "BookingsClosed-Installations-" . $sc['sc_code'] . "-" . date('d-M-Y');
		$output_file = $invoice_id;
		$output_file_excel = $output_file_dir . $output_file . ".xlsx";
		//for xlsx: excel, for xls: excel2003
		$R->render('excel', $output_file_excel);

		//convert excel to pdf
		$output_file_pdf = $output_file_dir . $output_file . ".pdf";
		$cmd = "curl -F file=@" . $output_file_excel .
		    " http://do.convertapi.com/Excel2Pdf?apikey=" . CONVERTAPI_KEY . " -o " . $output_file_pdf;
		exec($cmd);

		//log_message('info', "Report generated with $count records");
		echo PHP_EOL . "Report generated with $count records" . PHP_EOL;
		//Send report via email
		$this->email->clear(TRUE);
		$this->email->from('booking@247around.com', '247around Team');
		//$this->email->to('anuj.aggarwal@gmail.com');
		$this->email->to($sc['owner_email']);
		$cc = "sales@247around.com, nits@247around.com, anuj@247around.com";
		$this->email->cc($cc);
		//$this->email->bcc("anuj.aggarwal@gmail.com");

		$subject = "247around - " . $sc['name'] . " - Invoice for period: " . $start_date . " To " . $end_date;
		$this->email->subject($subject);

		//$message = "Dear " . $sc['owner_name'] . " Ji,<br/><br/>";
		$message = "Dear Partner,<br/><br/>";
		$message .= "Please find attached invoice for installations done between " . $start_date . " and " . $end_date . ".<br/><br/>";
		$message .= "Details with breakup by job, service category is attached. Also the service rating as given by customers is attached.<br/><br/>";
		$message .= "We shall remit the payment to your bank account mentioned in the attachment as per our agreement.<br/><br/>";
		$message .= "Hope to have a long lasting working relationship with you.";
		$message .= "<br><br>With Regards,
                        <br>247around Team<br>
                        <br>247around is part of Businessworld Startup Accelerator & Google Bootcamp 2015
                        <br>Follow us on Facebook: www.facebook.com/247around
                        <br>Website: www.247around.com
                        <br>Playstore - 247around -
                        <br>https://play.google.com/store/apps/details?id=com.handymanapp";

		$this->email->message($message);
		$this->email->attach($output_file_pdf, 'attachment');

		//Save filenames to delete later on
		array_push($file_names, $output_file_excel);
		array_push($file_names, $output_file_pdf);

		$mail_ret = $this->email->send();
		if ($mail_ret) {
		    //log_message('info', __METHOD__ . ": Mail sent successfully");
		    echo "Mail sent successfully...............\n\n";
		} else {
		    log_message('error', __METHOD__ . ": Mail could not be sent");
		    echo "Mail could not be sent" . PHP_EOL;
		}

		//Upload Excel files to AWS
		//$bucket = 'bookings-collateral-test';
		$bucket = 'bookings-collateral';
		$directory_xls = "invoices-excel/" . $output_file . ".xlsx";
		$directory_pdf = "invoices-pdf/" . $output_file . ".pdf";

		$this->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PRIVATE);
		$this->s3->putObjectFile($output_file_pdf, $bucket, $directory_pdf, S3::ACL_PRIVATE);

		//Save this invoice info in table
		$invoice_details = array(
		    'invoice_id' => $invoice_id,
		    'invoice_file_excel' => $output_file . '.xlsx',
		    'invoice_file_pdf' => $output_file . '.pdf',
		    'type' => 'B',
		    'vendor_partner' => 'vendor',
		    'vendor_partner_id' => $sc['id']
		);
		$this->invoices_model->insert_new_invoice($invoice_details);

		/*
		 * Update booking-invoice table to capture this new invoice against these bookings.
		 * Since this is a type A invoice, it would be stored as a vendor-debit invoice.
		 */
		$this->update_booking_invoice_mappings_installations($bookings_completed, $invoice_id);
	    }

	    //For testing, break after 1st vendor
	    //break;
	}

	//Delete XLS files now
	foreach ($file_names as $file_name)
	    exec("rm -rf " . escapeshellarg($file_name));

	exit(0);
    }

    function get_total_charges_rating_for_non_partner_bookings($bookings_completed) {
	$t_sc = 0;
	$t_asc = 0;
	$t_pc = 0;
	$rating_count = 0;
	$t_rating = 0;

	foreach ($bookings_completed as $booking) {
	    $t_sc += intval($booking['service_charge']);
	    $t_asc += intval($booking['additional_service_charge']);
	    $t_pc += intval($booking['parts_cost']);

	    if (intval($booking['rating']) > 0) {
		$rating_count++;
		$t_rating += intval($booking['rating']);
	    }
	}

	return array(
	    't_sc' => $t_sc, 't_asc' => $t_asc,
	    't_pc' => $t_pc, 't_avg_rating' => (round($t_rating / $rating_count, 1)));
    }

    function get_total_charges_rating_for_partner_bookings($bookings_completed) {
	$total = 0;
	$rating_count = 0;
	$t_rating = 0;

	foreach ($bookings_completed as $booking) {
	    $total += $booking['total_ic'];

	    if (intval($booking['rating']) > 0) {
		$rating_count++;
		$t_rating += intval($booking['rating']);
	    }
	}

	return array(
	    'total' => $total, 't_avg_rating' => (round($t_rating / $rating_count, 1)));
    }

    /*
     * Update booking-invoice table to capture this new invoice against these bookings.
     * Since this is a type A invoice, it would be stored as a vendor-debit invoice.
     */

    function update_booking_invoice_mappings_repairs($bookings_completed, $invoice_id) {
	foreach ($bookings_completed as $booking) {
	    $details = array('vendor_debit_invoice_id' => $invoice_id);
	    $this->invoices_model->update_booking_invoice_mapping($booking['booking_id'], $details);
	}
    }

    /*
     * Update booking-invoice table to capture this new invoice against these bookings.
     * Since this is a type A invoice, it would be stored as a vendor-debit invoice.
     */

    function update_booking_invoice_mappings_installations($bookings_completed, $invoice_id) {
	foreach ($bookings_completed as $booking) {
	    $details = array('vendor_credit_invoice_id' => $invoice_id);
	    $this->invoices_model->update_booking_invoice_mapping($booking['booking_id'], $details);
	}
    }

}
