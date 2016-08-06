<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

class Invoice extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
	parent::__Construct();

	$this->load->helper(array('form', 'url'));

	$this->load->model("invoices_model");
	$this->load->model("vendor_model");
	$this->load->model("booking_model");
	$this->load->model("partner_model");
	$this->load->library("notify");
	$this->load->library('PHPReport');
	$this->load->library('form_validation');
	$this->load->library("session");
	$this->load->library('s3');

	if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee') && ($this->session->userdata('add service') == '1')) {
	    return TRUE;
	} else {
	    redirect(base_url() . "employee/login");
	}
    }

    /**
     * Load invoicing form
     */
    public function index() {
	$data['service_center'] = $this->vendor_model->getActiveVendor("", 0);
	$data['invoicing_summary'] = $this->invoices_model->getsummary_of_invoice("vendor");

	$this->load->view('employee/header');
	$this->load->view('employee/invoice_list', $data);
    }

    /**
     * @desc: This is used to get vendor, partner invoicing data by service center id or partner id
     *          and load data in a table.
     *
     * @param: void
     * @return: void
     */
    function getInvoicingData() {
	$data['vendor_partner'] = $this->input->post('source');
	$data['vendor_partner_id'] = $this->input->post('vendor_partner_id');
	$invoice['invoice_array'] = $this->invoices_model->getInvoicingData($data);

	//TODO: Fix the reversed names here & everywhere else as well
	$data2['partner_vendor'] = $this->input->post('source');
	$data2['partner_vendor_id'] = $this->input->post('vendor_partner_id');
	$invoice['bank_statement'] = $this->invoices_model->get_bank_transactions_details($data2);

	$this->load->view('employee/invoicing_table', $invoice);
    }

    /**
     * @desc: Send Invoice pdf file to vendor
     *
     * @param: $invoiceId- this is the id of the invoice which we want to send.
     * @param: $vendor_partnerId- to partner's/vendor's id
     * @param: $start_date- date from which we are calculating this invoice
     * @param: $end_date- date upto which we are calculating this invoice
     * @param: $vendor_partner- tells if its vendor or partner
     * @return: void
     */
    function sendInvoiceMail($invoiceId, $vendor_partnerId, $start_date, $end_date, $vendor_partner) {
	log_message('info', "Entering: " . __METHOD__);
	$email = $this->input->post('email');
	// download invoice pdf file to local machine
	if ($vendor_partner == "vendor") {

	    $to = $this->get_to_emailId_for_vendor($vendor_partnerId, $email);
	} else {
	    $to = $this->get_to_emailId_for_partner($vendor_partnerId, $email);
	}

	log_message('info', "vendor partner type" . print_r($vendor_partner));
	log_message('info', "vendor partner id" . print_r($vendor_partnerId));

	$cc = "billing@247around.com, nits@247around.com, anuj@247around.com";
	$subject = "247around - Invoice for period: " . $start_date . " To " . $end_date;
	$attachment = 'https://s3.amazonaws.com/bookings-collateral/invoices-pdf/' . $invoiceId . '.pdf';

	$message = "Dear Partner <br/><br/>";
	$message .= "Please find attached invoice for jobs completed between " . $start_date . " and " . $end_date . ".<br/><br/>";
	$message .= "Details with breakup by job, service category is attached. Also the service rating as given by customers is shown.<br/><br/>";
	$message .= "Hope to have a long lasting working relationship with you.";
	$message .= "<br><br>With Regards,
                    <br>247around Team<br>
                    <br>247around is part of Businessworld Startup Accelerator & Google Bootcamp 2015
                    <br>Follow us on Facebook: www.facebook.com/247around
                    <br>Website: www.247around.com
                    <br>Playstore - 247around -
                    <br>https://play.google.com/store/apps/details?id=com.handymanapp";

	log_message('info', "To- EmailId" . print_r($to));

	$this->notify->sendEmail("billing@247around.com", $to, $cc, '', $subject, $message, $attachment);
	if ($vendor_partner == "vendor") {
	    redirect(base_url() . 'employee/invoice', 'refresh');
	} else {
	    redirect(base_url() . 'employee/invoice/invoice_partner_view', 'refresh');
	}
    }

    /**
     * @desc: Load view to select patner to display invoices
     * @param: void
     * @return: void
     */
    function invoice_partner_view() {

	$data['partner'] = $this->partner_model->getpartner();
	$data['invoicing_summary'] = $this->invoices_model->getsummary_of_invoice("partner");

	$this->load->view('employee/header');
	$this->load->view('employee/invoice_list', $data);
    }

    /**
     * @desc: Get vendor email id from table to send invoice.
     * @param: type vendorId
     * @param: type email
     * @return: string
     */
    function get_to_emailId_for_vendor($vendorId, $email) {
	$getEmail = $this->invoices_model->getEmailIdForVendor($vendorId);
	$to = "";
	if (!empty($email)) {
	    $to = $getEmail[0]['primary_contact_email'] . ',' . $getEmail[0]['owner_email'] . ',' . $email;
	} else {
	    $to = $getEmail[0]['primary_contact_email'] . ',' . $getEmail[0]['owner_email'];
	}

	return $to;
    }

    /**
     * @desc : Get partner email id from table to send invoice.
     * @param : type $partnerId
     * @param : type $email
     * @return : string
     */
    function get_to_emailId_for_partner($partnerId, $email) {
	$getEmail = $this->invoices_model->getEmailIdForPartner($partnerId);
	$to = "";
	if (!empty($email)) {
	    $to = $getEmail[0]['partner_email_for_to'] . ',' . $email;
	} else {
	    $to = $getEmail[0]['partner_email_for_to'];
	}

	return $to;
    }

    /**
     *  @desc : This function adds new transactions between vendor/partner and 247around.
     *  @param : Type $partnerId
     *  @return : void
     */
    function get_add_new_transaction($vendor_partner = "", $id = "") {
	$data['vendor_partner'] = $vendor_partner;
	$data['id'] = $id;
	$this->load->view('employee/header');
	$this->load->view('employee/addnewtransaction', $data);
    }

    /**
     *  @desc : This function inserts new bank transaction
     *  @param : void
     *  @return : void
     */
    function post_add_new_transaction() {
	$account_statement['partner_vendor'] = $this->input->post('partner_vendor');
	$account_statement['partner_vendor_id'] = $this->input->post('partner_vendor_id');
	$account_statement['invoice_id'] = $this->input->post('invoice_id');
	$account_statement['bankname'] = $this->input->post('bankname');
	$account_statement['credit_debit'] = $this->input->post('credit_debit');
	$account_statement['transaction_mode'] = $this->input->post('transaction_mode');
	$amount = $this->input->post('amount');

	if ($account_statement['credit_debit'] == 'Credit') {
	    $account_statement['debit_amount'] = '0';
	    $account_statement['credit_amount'] = $amount;
	} else if ($account_statement['credit_debit'] == 'Debit') {
	    $account_statement['credit_amount'] = '0';
	    $account_statement['debit_amount'] = $amount;
	}

	$transaction_date = $this->input->post('tdate');
	$account_statement['transaction_date'] = date("Y-m-d", strtotime($transaction_date));
	$account_statement['description'] = $this->input->post('description');

	$this->invoices_model->bankAccountTransaction($account_statement);

	$output = "Transaction added successfully.";
	$userSession = array('success' => $output);
	$this->session->set_userdata($userSession);

	//Send SMS to vendors about payment
	if ($account_statement['partner_vendor'] == 'vendor') {
	    $vendor_arr = $this->vendor_model->getVendorContact($account_statement['partner_vendor_id']);
	    $v = $vendor_arr[0];

	    $sms['tag'] = "payment_made_to_vendor";
	    $sms['phone_no'] = $v['owner_phone_1'];
	    $sms['smsData'] = "previous month";

	    $this->notify->send_sms($sms);
	}

	redirect(base_url() . 'employee/invoice/get_add_new_transaction');
    }

    /**
     *  @desc : AJAX CALL. This function is to get the partner or vendor details.
     *  @param : $par_ven - Vendor or partner name(specification)
     *  @return : void
     */
    function getPartnerOrVendor($par_ven) {
	$vendor_partner_id = $this->input->post('vendor_partner_id');

	if ($par_ven == 'partner') {
	    $all_partners = $this->partner_model->get_all_partner_source("0");
	    foreach ($all_partners as $p_name) {
		$option = "<option value='" . $p_name['partner_id'] . "'";
		if ($vendor_partner_id == $p_name['partner_id']) {

		    $option .= "selected";
		}
		$option .=" > ";
		$option .= $p_name['source'] . "</option>";
		echo $option;
	    }
	} else {
	    $all_vendors = $this->vendor_model->getActiveVendor("", 0);
	    foreach ($all_vendors as $v_name) {
		$option = "<option value='" . $v_name['id'] . "'";
		if ($vendor_partner_id == $v_name['id']) {

		    $option .= "selected ";
		}
		$option .=" > ";
		$option .= $v_name['name'] . "</option>";

		echo $option;
	    }
	    echo $vendor_partner_id;
	}
    }

    /**
     * @desc: This function is to delete bank transaction
     * @param: bank account transaction id, partner vender id
     * @return: void
     */
    function delete_banktransaction($transaction_id) {
	$this->invoices_model->delete_banktransaction($transaction_id);
	echo "success";
    }

    /*
     * @desc: Show all bank transactions
     * @param: party type (vendor, partner, all)
     * 'vendor' => show vendor transactios
     * 'partner' => show partner transactios
     * 'all' => show all transactios
     *
     * default: Show vendor transactions
     *
     * @return: list of transactions
     *
     */

    function show_all_transactions($type = 'vendor') {
	//Reset type to vendor if some other value is there
	$possible_type = array('vendor', 'partner', 'all');
	if (!in_array($type, $possible_type))
	    $type = 'vendor';

	$invoice['bank_statement'] = $this->invoices_model->get_all_bank_transactions($type);

	$this->load->view('employee/header');
	$this->load->view('employee/view_transactions', $invoice);
    }

    /**
     * @desc: this method is used to generate both type invoices (invoices details and summary)
     */
    function getpartner_invoices($partner_id = "") {
	$data = $this->invoices_model->getpartner_invoices($partner_id);
	$this->create_partner_invoices_details($data['invoice1']);
	$this->generate_partner_summary_invoices($data['invoice2']);
    }

    /**
     * @desc: generate details partner invoices
     */
    function create_partner_invoices_details($data) {
	$file_names = array();
	$template = 'partner_invoices.xlsx';
	//set absolute path to directory with template files
	$templateDir = __DIR__ . "/../";

	for ($i = 0; $i < count($data); $i++) {

	    //set config for report
	    $config = array(
		'template' => $template,
		'templateDir' => $templateDir
	    );

	    //load template
	    $R = new PHPReport($config);

	    $total_installation_charge = 0;
	    $total_service_tax = 0;
	    $total_stand_charge = 0;
	    $total_vat_charge = 0;
	    $total_charges = 0;

	    $getsource = substr($data[$i][0]['booking_id'], 0, 2);
	    $invoice_id = $getsource . date('dmY');

	    $unique_booking = array_unique(array_map(function ($k) {
		    return $k['booking_id'];
		}, $data[$i]));

	    $count = count($unique_booking);

	    log_message('info', __FUNCTION__ . '=> Start Date: ' . $data[$i][0]['start_date'] . ', End Date: ' . $data[$i][0]['end_date']);
	    //echo $start_date, $end_date;

	    $start_date = date("jS F, Y", strtotime($data[$i][0]['start_date']));
	    $end_date = date("jS F, Y", strtotime($data[$i][0]['end_date']));

	    foreach ($data[$i] as $key => $value) {


		if ($value['price_tags'] == "Wall Mount Stand") {

		    $data[$i][$key]['remarks'] = "Completed TV With Stand";
		} else {

		    $data[$i][$key]['remarks'] = "Completed	Installation & Demo";
		}

		$data[$i][$key]['closed_date'] = date("jS F, Y", strtotime($value['closed_date']));
		$data[$i][$key]['reference_date'] = date("jS F, Y", strtotime($value['reference_date']));

		$total_installation_charge += $value['installation_charge'];
		$total_service_tax += $value['st'];
		$total_stand_charge += $value['stand'];
		$total_vat_charge += $value['vat'];
		$total_charges = $total_installation_charge + $total_service_tax + $total_stand_charge + $total_vat_charge;
	    }

	    $excel_data['invoice_id'] = $invoice_id;
	    $excel_data['today'] = date('d-M-Y');
	    $excel_data['company_name'] = $data[$i][0]['company_name'];
	    $excel_data['company_address'] = $data[$i][0]['company_address'];
	    $excel_data['total_installation_charge'] = $total_installation_charge;
	    $excel_data['total_service_tax'] = $total_service_tax;
	    $excel_data['total_stand_charge'] = $total_stand_charge;
	    $excel_data['total_vat_charge'] = $total_vat_charge;
	    $excel_data['total_charges'] = $total_charges;

	    log_message('info', 'Excel data: ' . print_r($excel_data, true));

	    $files_name = $this->generate_pdf_with_data($excel_data, $data[$i], $R, $file_names);

	    array_push($file_names, $files_name . ".xlsx");
	    array_push($file_names, $files_name . ".pdf");

	    $bucket = 'bookings-collateral-test';
	    $bucket = 'bookings-collateral';
	    $directory_xls = "invoices-excel/" . $files_name . ".xlsx";
	    $directory_pdf = "invoices-pdf/" . $files_name . ".pdf";

	    $this->s3->putObjectFile($files_name . ".xlsx", $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
	    $this->s3->putObjectFile($files_name . ".pdf", $bucket, $directory_pdf, S3);


	    $invoice_details = array(
		'invoice_id' => $invoice_id,
		'type' => 'A',
		'vendor_partner' => 'partner',
		'vendor_partner_id' => $data[$i][0]['partner_id'],
		'invoice_file_excel' => $files_name . '.xlsx',
		'invoice_file_pdf' => $files_name . '.pdf',
		'from_date' => date("Y-m-d", strtotime($start_date)), //??? Check this next time, format should be YYYY-MM-DD
		'to_date' => date("Y-m-d", strtotime($end_date)),
		'num_bookings' => $count,
		'total_service_charge' => $excel_data['total_installation_charge'],
		'service_tax' => $excel_data['total_service_tax'],
		'parts_cost' => $excel_data['total_stand_charge'],
		'vat' => $excel_data['total_charges'],
		'around_royalty' => $excel_data['total_charges'],
	    );
	    $this->invoices_model->insert_new_invoice($invoice_details);
	}

	//Delete XLS files now
	foreach ($file_names as $file_name)
	    exec("rm -rf " . escapeshellarg($file_name));

	exit(0);
    }

    function generate_partner_summary_invoices($data) {
	$file_names = array();


	$template = 'partner_invoice_summary.xlsx';
	//set absolute path to directory with template files
	$templateDir = __DIR__ . "/../";

	for ($i = 0; $i < count($data); $i++) {


	    //set config for report
	    $config = array(
		'template' => $template,
		'templateDir' => $templateDir
	    );

	    //load template
	    $R = new PHPReport($config);


	    $total_installation_charge = 0;
	    $total_service_tax = 0;
	    $total_stand_charge = 0;
	    $total_vat_charge = 0;
	    $total_charges = 0;
	    $total_unit = 0;

	    $getsource = substr($data[$i][0]['source'], 0, 2);
	    $invoice_id = $getsource . date('dMY');

	    foreach ($data[$i] as $key => $value) {

		$total_installation_charge += $value['total_installation_charge'];
		$total_service_tax += $value['total_st'];
		$total_stand_charge += $value['total_stand_charge'];
		$total_vat_charge += $value['total_vat_charge'];
		$total_unit += $value['count_booking'];

		$data[$i][$key]['partner_paid_basic_charges'] = $value['total_installation_charge'] + $value['total_st'] + $value['total_stand_charge'] + $value['total_vat_charge'];


		echo "<br/><br/>";

		if ($value['price_tags'] == "Wall Mount Stand") {

		    $data[$i][$key]['remarks'] = "TV With Stand";
		} else if ($value['services'] == "Television") {

		    $data[$i][$key]['remarks'] = "TV Without Stand";
		} else {

		    $data[$i][$key]['remarks'] = $value['services'];
		}
	    }
	    print_r($data);

	    $excel_data['invoice_id'] = $invoice_id;
	    $excel_data['today'] = date('d-M-Y');
	    $excel_data['company_name'] = $data[$i][0]['company_name'];
	    $excel_data['company_address'] = $data[$i][0]['company_address'];
	    $excel_data['total_installation_charge'] = $total_installation_charge;
	    $excel_data['total_service_tax'] = $total_service_tax;
	    $excel_data['total_stand_charge'] = $total_stand_charge;
	    $excel_data['total_vat_charge'] = $total_vat_charge;
	    $excel_data['total_charges'] = $total_installation_charge + $total_service_tax + $total_stand_charge + $total_vat_charge;
	    $excel_data['total_unit'] = $total_unit;

	    log_message('info', 'Excel data: ' . print_r($excel_data, true));

	    $files_name = $this->generate_pdf_with_data($excel_data, $data[$i], $R, $file_names);



	    array_push($file_names, $files_name . ".xlsx");
	    array_push($file_names, $files_name . ".pdf");



	    //$bucket = 'bookings-collateral-test';
	    $bucket = 'bookings-collateral';
	    $directory_xls = "invoices-excel/" . $files_name . ".xlsx";
	    $directory_pdf = "invoices-pdf/" . $files_name . ".pdf";

	    //$this->s3->putObjectFile($files_name . ".xlsx", $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
	    //$this->s3->putObjectFile($files_name . ".pdf", $bucket, $directory_pdf, S3;
	}

	//Delete XLS files now
	/* foreach ($file_names as $file_name)
	  exec("rm -rf " . escapeshellarg($file_name));

	  exit(0); */
    }

    /**
     * @desc: Generate Excel and Pdf File with invoices data and return file names
     * @param: Array(Excel data), Array(Invoices data), Initiallized PHP report library and files name
     * @return : File name
     */
    function generate_pdf_with_data($excel_data, $data, $R, $file_names) {


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
		'data' => $data,
	    //'minRows' => 2,
	    ),
	    )
	);

	//Get populated XLS with data
	$output_file_dir = "/tmp/";
	$output_file = $excel_data['invoice_id'];
	$output_file_excel = $output_file_dir . $output_file . ".xlsx";
	//for xlsx: excel, for xls: excel2003
	$R->render('excel', $output_file_excel);

	//convert excel to pdf
	$output_file_pdf = $output_file_dir . $output_file . ".pdf";
	//$cmd = "curl -F file=@" . $output_file_excel . " http://do.convertapi.com/Excel2Pdf?apikey=" . CONVERTAPI_KEY . " -o " . $output_file_pdf;
	putenv('PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/opt/node/bin');
	$tmp_path = '/tmp/';
	$tmp_output_file = '/tmp/output_' . __FUNCTION__ . '.txt';
	$cmd = 'echo ' . $tmp_path . ' & echo $PATH & UNO_PATH=/usr/lib/libreoffice & ' .
	    '/usr/bin/unoconv --format pdf --output ' . $output_file_pdf . ' ' .
	    $output_file_excel . ' 2> ' . $tmp_output_file;
	$output = '';
	$result_var = '';
	exec($cmd, $output, $result_var);

	return $output_file_dir . $output_file;
    }

    // get all vendor invoices for both type  A and type b
    function generate_vendor_invoices($vendor_id = "", $date_ragnge = "") {
	$vendor_id = "1";
	$date_ragnge = "2016/06/01-2016/06/30";
	$data = $this->invoices_model->generate_vendor_invoices($vendor_id, $date_ragnge);

	$this->generate_cash_invoices_for_vendors($data['invoice1']);
	$this->generate_foc_invoices_for_vendors($data['invoice2']);

	//$this->load->view('employee/test', $data);
    }

    function generate_cash_invoices_for_vendors($data) {

	$file_names = array();

	$template = 'vendor_to_around_invoices.xlsx';
	//set absolute path to directory with template files
	$templateDir = __DIR__ . "/../";

	for ($i = 0; $i < count($data); $i++) {

	    $invoices = $data[$i];
	    $excel_data = array();

	    $count = count($invoices);
	    echo "<br/>";
	    print_r($invoices);
	    echo "<br/>";
	    log_message('info', __FUNCTION__ . '=> Start Date: ' . $invoices[0]['start_date'] . ', End Date: ' . $invoices[0]['end_date']);
	    //echo $start_date, $end_date;

	    $start_date = date("jS F, Y", strtotime($invoices[0]['start_date']));
	    $end_date = date("jS F, Y", strtotime($invoices[0]['end_date']));

	    log_message('info', 'Service Centre: ' . $invoices[0]['id'] . ', Count: ' . $count);

	    //set config for report
	    $config = array(
		'template' => $template,
		'templateDir' => $templateDir
	    );

	    //load template
	    $R = new PHPReport($config);
	    //A means it is for the 1st type of invoice as explained above
	    //Make sure it is unique

	    $invoice_id = $invoices[0]['sc_code'] . "-" . date("dMY") . "-A-" . rand(100, 999);
	    if (is_null($invoices[0]['avg_rating'])) {
		$invoices[0]['avg_rating'] = "";
	    }
	    $service_tax_rate = 0.145; //To be changed for June invoices onwards
	    $r_st = round($invoices[0]['amount_to_be_pay'] * $service_tax_rate, 0); //service tax calculated on royalty
	    //Find total charges for these bookings
	    $excel_data = array(
		't_ap' => $invoices[0]['total_amount_paid'], 'r_sc' => $invoices[0]['calcutated_service_tax'],
		'r_asc' => $invoices[0]['calcutated_additional_tax'], 't_sc' => $invoices[0]['sum_service_charge'],
		't_asc' => $invoices[0]['sum_addtional_charge'], 't_pc' => $invoices[0]['sum_parts_charge'],
		'r_pc' => $invoices[0]['calcutated_parts_tax'], 'r_total' => $invoices[0]['amount_to_be_pay'],
		't_rating' => $invoices[0]['avg_rating'],
		'r_st' => $r_st);


	    $excel_data['invoice_id'] = $invoice_id;
	    $excel_data['vendor_name'] = $invoices[0]['name'];
	    $excel_data['vendor_address'] = $invoices[0]['address'];
	    $excel_data['sd'] = $start_date;
	    $excel_data['ed'] = $end_date;
	    $excel_data['today'] = date("d-M-Y");
	    $excel_data['count'] = $count;

	    $excel_data['msg'] = 'Thanks 247around Partner for your support, we completed ' . $count .
		' bookings with you from ' . $start_date . ' till ' . $end_date .
		'. Total transaction value for the bookings was Rs. ' . $invoices[0]['total_amount_paid'] .
		'. Around royalty for this invoice is Rs. ' . $excel_data['r_total'] .
		'. Your rating for completed bookings is ' . $excel_data['t_rating'] .
		'. We look forward to your continued support in future. As next step, 247around will pay you remaining amount as per our agreement.';
	    $excel_data['beneficiary_name'] = $invoices[0]['beneficiary_name'];
	    $excel_data['bank_account'] = $invoices[0]['bank_account'];
	    $excel_data['bank_name'] = $invoices[0]['bank_name'];
	    $excel_data['ifsc_code'] = $invoices[0]['ifsc_code'];

	    log_message('info', 'Excel data: ' . print_r($excel_data, true));

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
		    'data' => $invoices,
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
	    //$cmd = "curl -F file=@" . $output_file_excel . " http://do.convertapi.com/Excel2Pdf?apikey=" . CONVERTAPI_KEY . " -o " . $output_file_pdf;
	    putenv('PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/opt/node/bin');
	    $tmp_path = '/tmp/';
	    $tmp_output_file = '/tmp/output_' . __FUNCTION__ . '.txt';
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
	    //$to = $invoices[0]['owner_email'] . ", " . $invoices[0]['primary_contact_email'];
	    $to = "abhaya@247around.com, anuj@247around.com";
	    //$to = 'anuj.aggarwal@gmail.com';
	    $this->email->to($to);
	    //$cc = "billing@247around.com, nits@247around.com, anuj@247around.com";
	    //$this->email->cc($cc);
	    //$this->email->bcc("anuj.aggarwal@gmail.com");
	    //$subject = "247around - " . $sc['name'] . " - Invoice for period: " . $start_date . " To " . $end_date;
	    $subject = "247around - " . $invoices[0]['name'] . " - Invoice for period: " . $start_date . " to " . $end_date;
	    $this->email->subject($subject);

	    $message = "Dear Partner,<br/><br/>";
	    $message .= "Please find attached invoice for jobs completed between " . $start_date . " and " . $end_date . ". ";
	    $message .= "Details with breakup by job, service category is attached. Also the service rating as given by customers is shown.<br/><br/>";
	    $message .= "This invoice is for the jobs where payment was collected by " . $invoices[0]['name'] . ".<br><br>";
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


	    /* $mail_ret = $this->email->send();
	      if ($mail_ret) {
	      //log_message('info', __METHOD__ . ": Mail sent successfully");
	      echo "Mail sent successfully...............\n\n";
	      } else {
	      log_message('error', __METHOD__ . ": Mail could not be sent");
	      echo "Mail could not be sent" . PHP_EOL;
	      } */
	    //Save filenames to delete later on
	    array_push($file_names, $output_file_excel);
	    array_push($file_names, $output_file_pdf);


	    //Upload Excel files to AWS
	    //$bucket = 'bookings-collateral-test';
	    $bucket = 'bookings-collateral';
	    $directory_xls = "invoices-excel/" . $output_file . ".xlsx";
	    $directory_pdf = "invoices-pdf/" . $output_file . ".pdf";

	    //$this->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
	    //$this->s3->putObjectFile($output_file_pdf, $bucket, $directory_pdf, S3::ACL_PUBLIC_READ);
	    //Save this invoice info in table
	    $invoice_details = array(
		'invoice_id' => $invoice_id,
		'type' => 'Cash',
		'type_code' => 'A',
		'vendor_partner' => 'vendor',
		'vendor_partner_id' => $invoices[0]['id'],
		'invoice_file_excel' => $output_file . '.xlsx',
		'invoice_file_pdf' => $output_file . '.pdf',
		'from_date' => date("Y-m-d", strtotime($start_date)),
		'to_date' => date("Y-m-d", strtotime($end_date)),
		'num_bookings' => $count,
		'total_service_charge' => $excel_data['t_sc'],
		'total_additional_service_charge' => $excel_data['t_asc'],
		'parts_cost' => $excel_data['t_pc'],
		'vat' => 0, //No VAT here in Cash invoice
		'total_amount_collected' => $excel_data['t_ap'],
		'rating' => $excel_data['t_rating'],
		'around_royalty' => $excel_data['r_total'],
		//Service tax which needs to be paid
		'service_tax' => $excel_data['r_st'],
		//Amount needs to be collected from Vendor
		'amount_collected_paid' => $excel_data['r_total'],
		//Add 1 month to end date to calculate due date
		'due_date' => date("Y-m-d", strtotime($end_date . "+1 month"))
	    );


	    //$this->invoices_model->insert_new_invoice($invoice_details);

	    /*
	     * Update booking-invoice table to capture this new invoice against these bookings.
	     * Since this is a type 'Cash' invoice, it would be stored as a vendor-debit invoice.
	     */
	    //$this->update_booking_invoice_mappings_repairs($invoices, $invoice_id);
	}

	//Delete XLS files now
	/*  foreach ($file_names as $file_name)
	  exec("rm -rf " . escapeshellarg($file_name));

	  exit(0); */
    }

    function generate_foc_invoices_for_vendors($data) {

	// vendor to around

	$file_names = array();


	$template = 'around_to_vendor_invoices.xlsx';
	//set absolute path to directory with template files
	$templateDir = __DIR__ . "/../";

	for ($i = 0; $i < count($data); $i++) {
	    $total_inst_charge = 0;
	    $total_st_charge = 0;
	    $total_stand_charge = 0;
	    $total_vat_charge = 0;
	    for ($j = 0; $j < count($data[$i]); $j++) {

		$total_inst_charge += $data[$i][$j]['installation_charge'];
		$total_st_charge += $data[$i][$j]['st'];
		$total_stand_charge += $data[$i][$j]['stand'];
		$total_vat_charge += $data[$i][$j]['vat'];
		$data[$i][$j]['amount_paid'] = $total_inst_charge + $total_st_charge + $total_stand_charge + $total_vat_charge;
	    }

	    $r_ic = $total_inst_charge * .30;
	    $r_st = $total_st_charge * .30;
	    $r_vat = $total_vat_charge * .30;
	    $r_stand = $total_stand_charge * .30;
	    $t_total = $total_inst_charge + $total_stand_charge + $total_st_charge + $total_vat_charge;

	    $invoices = $data[$i];

	    $excel_data = array();

	    $count = count($invoices);
	    echo "<br/>";
	    print_r($invoices);
	    echo "<br/>";

	    log_message('info', __FUNCTION__ . '=> Start Date: ' . $invoices[0]['start_date'] . ', End Date: ' . $invoices[0]['end_date']);
	    //echo $start_date, $end_date;

	    $start_date = date("jS F, Y", strtotime($invoices[0]['start_date']));
	    $end_date = date("jS F, Y", strtotime($invoices[0]['end_date']));

	    log_message('info', 'Service Centre: ' . $invoices[0]['id'] . ', Count: ' . $count);

	    //set config for report
	    $config = array(
		'template' => $template,
		'templateDir' => $templateDir
	    );

	    //load template
	    $R = new PHPReport($config);
	    //A means it is for the 1st type of invoice as explained above
	    //Make sure it is unique

	    $invoice_id = $invoices[0]['sc_code'] . "-" . date("dMY") . "-B-" . rand(100, 999);

	    if (is_null($invoices[0]['avg_rating'])) {
		$invoices[0]['avg_rating'] = "";
	    }

	    $excel_data = array(
		't_ic' => $total_inst_charge, 't_st' => $total_st_charge, 't_stand' => $total_stand_charge,
		't_vat' => $total_vat_charge, 't_total' => $t_total,
		't_rating' => $invoices[0]['avg_rating'],
		'r_ic' => $r_ic, 'r_st' => $r_st, 'r_stand' => $r_stand,
		'r_total' => round($t_total * 0.3, 0), 'r_vat' => $r_vat, 't_vp' => round($t_total * 0.7, 0)
	    );

	    $excel_data['invoice_id'] = $invoice_id;
	    $excel_data['vendor_name'] = $invoices[0]['name'];
	    $excel_data['vendor_address'] = $invoices[0]['address'];
	    $excel_data['sd'] = $start_date;
	    $excel_data['ed'] = $end_date;
	    $excel_data['today'] = date("d-M-Y");
	    $excel_data['count'] = $count;

	    $excel_data['msg'] = 'Thanks 247around Partner for your support, we completed ' . $count .
		' bookings with you from 1st April to 30th April' .
		'. Total transaction value for the bookings was Rs. ' . $excel_data['t_total'] .
		'. Around royalty for this invoice is Rs. ' . $excel_data['r_total'] .
		'. Your rating for completed bookings is ' . $excel_data['t_rating'] .
		'. We look forward to your continued support in future. As next step, 247around will pay you remaining amount as per our agreement.';

	    $excel_data['beneficiary_name'] = $invoices[0]['beneficiary_name'];
	    $excel_data['bank_account'] = $invoices[0]['bank_account'];
	    $excel_data['bank_name'] = $invoices[0]['bank_name'];
	    $excel_data['ifsc_code'] = $invoices[0]['ifsc_code'];

	    log_message('info', 'Excel data: ' . print_r($excel_data, true));

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
		    'data' => $invoices,
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
	    //$cmd = "curl -F file=@" . $output_file_excel . " http://do.convertapi.com/Excel2Pdf?apikey=" . CONVERTAPI_KEY . " -o " . $output_file_pdf;
	    putenv('PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/opt/node/bin');
	    $tmp_path = '/tmp/';
	    $tmp_output_file = '/tmp/output_' . __FUNCTION__ . '.txt';
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
	    //$to = $invoices[0]['owner_email'] . ", " . $invoices[0]['primary_contact_email'];
	    $to = "abhaya@247around.com, anuj@247around.com";
	    //$to = 'anuj.aggarwal@gmail.com';
	    $this->email->to($to);
	    // $cc = "billing@247around.com, nits@247around.com, anuj@247around.com";
	    //$this->email->cc($cc);
	    // $this->email->bcc("anuj.aggarwal@gmail.com");
	    //$subject = "247around - " . $sc['name'] . " - Invoice for period: " . $start_date . " To " . $end_date;
	    $subject = "247around - " . $invoices[0]['name'] . " - Invoice for period: " . $start_date . " to " . $end_date;
	    $this->email->subject($subject);

	    $message = "Dear Partner,<br/><br/>";
	    $message .= "Please find attached invoice for jobs completed between " . $start_date . " and " . $end_date . ". ";
	    $message .= "Details with breakup by job, service category is attached. Also the service rating as given by customers is shown.<br/><br/>";
	    $message .= "This invoice is for the jobs where payment was collected by " . $invoices[0]['name'] . ".<br><br>";
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

	    /* $mail_ret = $this->email->send();
	      if ($mail_ret) {
	      //log_message('info', __METHOD__ . ": Mail sent successfully");
	      echo "Mail sent successfully...............\n\n";
	      } else {
	      log_message('error', __METHOD__ . ": Mail could not be sent");
	      echo "Mail could not be sent" . PHP_EOL;
	      } */

	    //Save filenames to delete later on
	    array_push($file_names, $output_file_excel);
	    array_push($file_names, $output_file_pdf);


	    //Upload Excel files to AWS
	    //$bucket = 'bookings-collateral-test';
	    $bucket = 'bookings-collateral';
	    $directory_xls = "invoices-excel/" . $output_file . ".xlsx";
	    $directory_pdf = "invoices-pdf/" . $output_file . ".pdf";

	    //$this->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
	    //$this->s3->putObjectFile($output_file_pdf, $bucket, $directory_pdf, S3;
	    //Save this invoice info in table
	    $invoice_details = array(
		'invoice_id' => $invoice_id,
		'type' => 'FOC',
		'type_code' => 'B',
		'vendor_partner' => 'vendor',
		'vendor_partner_id' => $invoices[0]['id'],
		'invoice_file_excel' => $output_file . '.xlsx',
		'invoice_file_pdf' => $output_file . '.pdf',
		'from_date' => date("Y-m-d", strtotime($start_date)),
		'to_date' => date("Y-m-d", strtotime($end_date)),
		'num_bookings' => $count,
		'total_service_charge' => $excel_data['t_ic'],
		'total_additional_service_charge' => 0,
		'service_tax' => $excel_data['t_st'],
		'parts_cost' => $excel_data['t_stand'],
		'vat' => $excel_data['t_vat'],
		'total_amount_collected' => $excel_data['t_total'],
		'rating' => $excel_data['t_rating'],
		'around_royalty' => $excel_data['r_total'],
		//Amount needs to be Paid to Vendor
		'amount_collected_paid' => ( $excel_data['r_total'] - $excel_data['t_total']),
		//Add 1 month to end date to calculate due date
		'due_date' => date("Y-m-d", strtotime($end_date . "+1 month"))
	    );

	    //$this->invoices_model->insert_new_invoice($invoice_details);

	    /*
	     * Update booking-invoice table to capture this new invoice against these bookings.
	     * Since this is a type A invoice, it would be stored as a vendor-debit invoice.
	     */
	    //$this->update_booking_invoice_mappings_installations($invoices, $invoice_id);
	}



	//Delete XLS files now
	/* foreach ($file_names as $file_name)
	  exec("rm -rf " . escapeshellarg($file_name));

	  exit(0); */
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
     * Since this is a type B invoice, it would be stored as a vendor-credit invoice.
     */

    function update_booking_invoice_mappings_installations($bookings_completed, $invoice_id) {
	foreach ($bookings_completed as $booking) {
	    $details = array('vendor_credit_invoice_id' => $invoice_id);
	    $this->invoices_model->update_booking_invoice_mapping($booking['booking_id'], $details);
	}
    }

}
