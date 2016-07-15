<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

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
	$data['service_center'] = $this->vendor_model->getActiveVendor("",0);
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
    function getpartner_invoices($partner_id =""){
    	$data = $this->invoices_model->getpartner_invoices($partner_id);
    	$this->create_partner_invoices_details($data['invoice1']);
    	$this->generate_partner_summary_invoices($data['invoice2']); 	  
    }
    
    /**
     * @desc: generate details partner invoices
     */
    function create_partner_invoices_details($data){
    	$file_names = array();
        $template = 'partner_invoices.xlsx';
	        //set absolute path to directory with template files
	    $templateDir = __DIR__ . "/../";

    	for($i=0; $i< count($data); $i++){

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
    		$invoice_id = $getsource.date('dmY');

    		$unique_booking = array_unique(array_map(function ($k) { return $k['booking_id']; }, $data[$i]));
            
    		$count = count($unique_booking);

    		log_message('info', __FUNCTION__ . '=> Start Date: ' . $data[$i][0]['start_date'] . ', End Date: ' . $data[$i][0]['end_date']);
	         //echo $start_date, $end_date; 

    		$start_date = date("jS F, Y", strtotime($data[$i][0]['start_date']));
    		$end_date =  date("jS F, Y", strtotime($data[$i][0]['end_date']));

    		foreach ($data[$i] as $key => $value) {

    		    
    		    if($value['price_tags'] == "Wall Mount Stand"){

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

    	    array_push($file_names, $files_name.".xlsx");
		    array_push($file_names, $files_name.".pdf");

    	    $bucket = 'bookings-collateral-test';
		    $bucket = 'bookings-collateral';
		    $directory_xls = "invoices-excel/" . $files_name . ".xlsx";
		    $directory_pdf = "invoices-pdf/" . $files_name . ".pdf";

		    $this->s3->putObjectFile($files_name . ".xlsx", $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
		    $this->s3->putObjectFile($files_name . ".pdf", $bucket, $directory_pdf, S3;


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
		    'total_service_charge' =>  $excel_data['total_installation_charge'],
		    'service_tax' => $excel_data['total_service_tax'],
		    'parts_cost' =>  $excel_data['total_stand_charge'] ,
		    'vat' =>  $excel_data['total_charges'] ,
		    'around_royalty' =>  $excel_data['total_charges'],
		);
		$this->invoices_model->insert_new_invoice($invoice_details);

    	}

    	//Delete XLS files now
	    foreach ($file_names as $file_name)
	    exec("rm -rf " . escapeshellarg($file_name));

	    exit(0);

    }

    function generate_partner_summary_invoices($data){
    	$file_names = array();


        $template = 'partner_invoice_summary.xlsx';
	        //set absolute path to directory with template files
	    $templateDir = __DIR__ . "/../";

    	for($i=0; $i< count($data); $i++){


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
    		$invoice_id = $getsource.date('dMY');

    		foreach ($data[$i] as $key => $value) {
    			
    			$total_installation_charge += $value['total_installation_charge'];
    		    $total_service_tax += $value['total_st'];
    		    $total_stand_charge += $value['total_stand_charge'];
    		    $total_vat_charge += $value['total_vat_charge'];
    		    $total_unit += $value['count_booking'];

    		    $data[$i][$key]['partner_paid_basic_charges'] =$value['total_installation_charge'] + $value['total_st'] + $value['total_stand_charge'] +  $value['total_vat_charge'];

    		    
    		    echo "<br/><br/>";

    		    if($value['price_tags'] == "Wall Mount Stand"){

    		    	$data[$i][$key]['remarks'] = "TV With Stand";

    		    } else if($value['services'] == "Television"){

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



    	    array_push($file_names, $files_name.".xlsx");
		    array_push($file_names, $files_name.".pdf");



    	     //$bucket = 'bookings-collateral-test';
		    $bucket = 'bookings-collateral';
		    $directory_xls = "invoices-excel/" . $files_name . ".xlsx";
		    $directory_pdf = "invoices-pdf/" . $files_name . ".pdf";

		    //$this->s3->putObjectFile($files_name . ".xlsx", $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
		    //$this->s3->putObjectFile($files_name . ".pdf", $bucket, $directory_pdf, S3;

        }

        //Delete XLS files now
	    /*foreach ($file_names as $file_name)
	    exec("rm -rf " . escapeshellarg($file_name));

	    exit(0);*/

    }
    
    /**
     * @desc: Generate Excel and Pdf File with invoices data and return file names
     * @param: Array(Excel data), Array(Invoices data), Initiallized PHP report library and files name
     * @return : File name
     */
    function generate_pdf_with_data($excel_data, $data, $R, $file_names){


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
			'data' =>  $data,
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

}
