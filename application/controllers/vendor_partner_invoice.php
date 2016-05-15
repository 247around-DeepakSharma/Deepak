<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

error_reporting(E_ALL);
ini_set('display_errors', '1');

//ini_set('include_path', '/Applications/MAMP/htdocs/aroundlocalhost/system/libraries');
ini_set('include_path', '/var/www/aroundhomzapp.com/public_html/system/libraries');

require_once('simple_html_dom.php');

//define('CONVERTAPI_KEY', '110761630');     //test account
//define('CONVERTAPI_KEY', '278325305');    //247around account

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
     * One is for the jobs where they collected money - Cash Jobs
     * Second is for the partner-provided jobs where partner will pay directly
     * to 247around and vendor did the job for free.
     * This function is for the 1st type of invoices.
     *
     * Start date format = DD-MM-YYYY
     * End date format = DD-MM-YYYY
     *
     * Invoice would be generated for period starting from Start_Date and ending
     * on End_date (both dates inclusive).
     */
    public function generate_cash_invoices_for_vendors($start_date, $end_date) {
	//log_message('info', __FUNCTION__);
	//echo $start_date, $end_date;

	$file_names = array();

	//Type A invoices
	$template = 'Vendor_Settlement_Template-Cash-v1.xlsx';
	//set absolute path to directory with template files
	$templateDir = __DIR__ . "/excel-templates/";

	//set config for report
	$config = array(
	    'template' => $template,
	    'templateDir' => $templateDir
	);

	//Cover entire start and end days by including time as well
	$s_date = date("Y-m-d H:i:s", strtotime($start_date . '00:00:00'));
	$e_date = date("Y-m-d H:i:s", strtotime($end_date . '23:59:59'));

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
		$tot_ch_rat = $this->get_total_charges_rating_for_cash_bookings($bookings_completed);

		//load template
		$R = new PHPReport($config);
		//A means it is for the 1st type of invoice as explained above
		//Make sure it is unique
		$invoice_id = $sc['sc_code'] . "-" . date("dMY") . "-A-" . rand(100, 999);

		$excel_data = $tot_ch_rat;
		$excel_data['invoice_id'] = $invoice_id;
		$excel_data['vendor_name'] = $sc['name'];
		$excel_data['vendor_address'] = $sc['address'];
		$excel_data['sd'] = $start_date;
		$excel_data['ed'] = $end_date;
		$excel_data['today'] = date("d-M-Y");
		$excel_data['count'] = $count;
		$excel_data['msg'] = 'Thanks 247around Partner for your support, we completed ' . $count .
		' bookings with you from ' . $start_date . ' till ' . $end_date .
		'. Total transaction value for the bookings was Rs. ' . $tot_ch_rat['t_ap'] .
		'. Around royalty for this invoice is Rs. ' . $tot_ch_rat['r_total'] .
		'. Your rating for completed bookings is ' . $tot_ch_rat['t_rating'] .
		'. We look forward to your continued support in future. As next step, please deposit 247around royalty per the below details.';

		$R->load(array(
		    array(
			'id' => 'meta',
			'data' => $excel_data,
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
		$output_file = $invoice_id;
		$output_file_excel = $output_file_dir . $output_file . ".xlsx";
		//for xlsx: excel, for xls: excel2003
		$R->render('excel', $output_file_excel);

		//convert excel to pdf
		$output_file_pdf = $output_file_dir . $output_file . ".pdf";
//		$cmd = "curl -F file=@" . $output_file_excel . " http://do.convertapi.com/Excel2Pdf?apikey=" . CONVERTAPI_KEY . " -o " . $output_file_pdf;
		putenv('PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/opt/node/bin');
		$tmp_path = '/home/around/libreoffice_tmp';
		$tmp_output_file = '/home/around/libreoffice_tmp/output_' . __FUNCTION__ . '.txt';
		$cmd = 'echo ' . $tmp_path . ' & echo $PATH & UNO_PATH=/usr/lib/libreoffice & ' .
		    '/usr/bin/unoconv --format pdf --output ' . $output_file_pdf . ' ' .
		    $output_file_excel . ' 2> ' . $tmp_output_file;

		//echo $cmd;
		$output = '';
		$result_var = '';
		exec($cmd, $output, $result_var);

		//log_message('info', "Report generated with $count records");
		echo PHP_EOL . "Report generated with $count records" . PHP_EOL;
		//Send report via email
		$this->email->clear(TRUE);
		$this->email->from('billing@247around.com', '247around Team');
		//$to = $sc['owner_email'] . ", " . $sc['primary_contact_email'];
		$to = 'anuj.aggarwal@gmail.com';
		$this->email->to($to);
		$cc = "billing@247around.com, nits@247around.com, anuj@247around.com";
		//$this->email->cc($cc);
		//$this->email->bcc("anuj.aggarwal@gmail.com");

		$subject = "247around - " . $sc['name'] . " - Invoice for period: " . $start_date . " To " . $end_date;
		$this->email->subject($subject);

		$message = "Dear Partner,<br/><br/>";
		$message .= "Please find attached invoice for jobs completed between " . $start_date . " and " . $end_date . ". ";
		$message .= "Details with breakup by job, service category is attached. Also the service rating as given by customers is shown.<br/><br/>";
		$message .= "This invoice is for the jobs where payment was collected by " . $sc['name'] . ".<br><br>";
		$message .= "Please review all the invoices and let us know if there are any discrepancies.<br><br>";
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

		//$this->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PRIVATE);
		//$this->s3->putObjectFile($output_file_pdf, $bucket, $directory_pdf, S3::ACL_PRIVATE);
		//Save this invoice info in table
//		$total_amount_collected = $tot_ch_rat['t_ap'];
//		$around_royalty = $tot_ch_rat['r_total'];
		$invoice_details = array(
		    'invoice_id' => $invoice_id,
		    'type' => 'Cash',
		    'vendor_partner' => 'vendor',
		    'vendor_partner_id' => $sc['id'],
		    'invoice_file_excel' => $output_file . '.xlsx',
		    'invoice_file_pdf' => $output_file . '.pdf',
		    'from_date' => $start_date,
		    'to_date' => $end_date,
		    'num_bookings' => $count,
		    'total_service_charge' => $tot_ch_rat['t_sc'],
		    'total_additional_service_charge' => $tot_ch_rat['t_asc'],
		    'parts_cost' => $tot_ch_rat['t_pc'],
		    'total_amount_collected' => $tot_ch_rat['t_ap'],
		    'rating' => $tot_ch_rat['t_rating'],
		    'around_royalty' => $tot_ch_rat['r_total'],
		);
		//$this->invoices_model->insert_new_invoice($invoice_details);

		/*
		 * Update booking-invoice table to capture this new invoice against these bookings.
		 * Since this is a type 'Cash' invoice, it would be stored as a vendor-debit invoice.
		 */
		//$this->update_booking_invoice_mappings_repairs($bookings_completed, $invoice_id);
	    }

	    //To test for 1 vendor, break
	    break;
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
     * These are FOC (Free Of Cost) Jobs.
     * This function is for the 2nd type of invoices.
     *
     * Start date format = DD-MM-YYYY
     * End date format = DD-MM-YYYY
     *
     * Invoice would be generated for period starting from Start_Date and ending
     * on End_date (both dates inclusive).
     */

    public function generate_foc_invoices_for_vendors($start_date, $end_date) {
	//log_message('info', __FUNCTION__);
	//echo $start_date, $end_date;

	$file_names = array();

	//Type B invoices
	$template = 'Vendor_Settlement_Template-FoC-v1.xlsx';
	//set absolute path to directory with template files
	$templateDir = __DIR__ . "/excel-templates/";

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
		$tot_ch_rat = $this->get_total_charges_rating_for_foc_bookings($bookings_completed);

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
		$this->email->from('billing@247around.com', '247around Team');
		$to = $sc['owner_email'] . ", " . $sc['primary_contact_email'];
		$this->email->to($to);
		$cc = "billing@247around.com, nits@247around.com, anuj@247around.com";
		$this->email->cc($cc);
		$this->email->bcc("anuj.aggarwal@gmail.com");

		$subject = "247around - " . $sc['name'] . " - Invoice for period: " . $start_date . " To " . $end_date;
		$this->email->subject($subject);

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

    function get_total_charges_rating_for_cash_bookings($bookings_completed) {
	$t_sc = 0; //service charges
	$t_asc = 0; //add service charges
	$t_pc = 0; //parts
	$t_ap = 0; //amount paid
	$rating_count = 0;
	$t_rating = 0;

	foreach ($bookings_completed as $booking) {
	    $t_sc += intval($booking['service_charge']);
	    $t_asc += intval($booking['additional_service_charge']);
	    $t_pc += intval($booking['parts_cost']);
	    $t_ap += intval($booking['amount_paid']);

	    if (intval($booking['rating']) > 0) {
		$rating_count++;
		$t_rating += intval($booking['rating']);
	    }
	}

	$r_sc = $t_sc * 0.3;     //around royalty for service charges
	$r_asc = $t_asc * 0.15;     //around royalty for add service charges
	$r_pc = $t_pc * 0.05;     //around royalty for parts
	$r_total = round($r_sc + $r_asc + $r_pc, 0);

	return array(
	    't_sc' => $t_sc, 't_asc' => $t_asc, 't_pc' => $t_pc,
	    't_ap' => $t_ap, 'r_sc' => $r_sc, 'r_asc' => $r_asc,
	    'r_pc' => $r_pc, 'r_total' => $r_total,
	    't_rating' => (round($t_rating / $rating_count, 1)));
    }

    function get_total_charges_rating_for_foc_bookings($bookings_completed) {
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
