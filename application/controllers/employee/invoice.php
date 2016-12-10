<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 36000000);

class Invoice extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
	parent::__Construct();

	$this->load->helper(array('form', 'url'));

	$this->load->model("invoices_model");
	$this->load->model("vendor_model");
        $this->load->model("inventory_model");
	$this->load->model("booking_model");
	$this->load->model("partner_model");
	$this->load->library("notify");
	$this->load->library('PHPReport');
	$this->load->library('form_validation');
	$this->load->library("session");
	$this->load->library('s3');

	if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
	    return TRUE;
	} else {
            echo PHP_EOL . 'Terminal Access Not Allowed' . PHP_EOL;
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
       
        if($data['vendor_partner'] == "vendor"){
            $invoice['vendor_details'] = $this->vendor_model->getVendorContact($data['vendor_partner_id']);
        }

	echo $this->load->view('employee/invoicing_table', $invoice);
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

	log_message('info', "To- EmailId" . print_r($to, true));

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
	$data['invoice_id'] = $this->input->post('invoice_id');
	$data['selected_amount_collected'] = $this->input->post('selected_amount_collected');
	$data['selected_tds'] = $this->input->post('selected_tds');
        
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
	$paid_amount = 0;
	if ($account_statement['credit_debit'] == 'Credit') {
	    $account_statement['debit_amount'] = '0';
	    $account_statement['credit_amount'] = $amount;
	    $paid_amount = -$amount;
	} else if ($account_statement['credit_debit'] == 'Debit') {
	    $account_statement['credit_amount'] = '0';
	    $account_statement['debit_amount'] = $amount;
	    $paid_amount = $amount;
	}

	$transaction_date = $this->input->post('tdate');
	$account_statement['transaction_date'] = date("Y-m-d", strtotime($transaction_date));
	$account_statement['description'] = $this->input->post('description');

	$invoice_id = explode(',', $account_statement['invoice_id']);

	$this->invoices_model->update_settle_invoices($invoice_id, $paid_amount, $account_statement['partner_vendor'], $account_statement['partner_vendor_id']);

	$this->invoices_model->bankAccountTransaction($account_statement);

	// $output = "Transaction added successfully.";
	// $userSession = array('success' => $output);
	// $this->session->set_userdata($userSession);
	//Send SMS to vendors about payment
	if ($account_statement['partner_vendor'] == 'vendor') {
	    $vendor_arr = $this->vendor_model->getVendorContact($account_statement['partner_vendor_id']);
	    $v = $vendor_arr[0];

	    $sms['tag'] = "payment_made_to_vendor";
	    $sms['phone_no'] = $v['owner_phone_1'];
	    $sms['smsData'] = "previous month";
	    $sms['booking_id'] = "";
	    $sms['type'] = $account_statement['partner_vendor'];
	    $sms['type_id'] = $account_statement['partner_vendor_id'];

	    $this->notify->send_sms_acl($sms);
	}

	redirect(base_url() . 'employee/invoice/invoice_summary/' . $account_statement['partner_vendor'] . "/" . $account_statement['partner_vendor_id']);
    }

    /**
     *  @desc : AJAX CALL. This function is to get the partner or vendor details.
     *  @param : $par_ven - Vendor or partner name(specification)
     *  @return : void
     */
    function getPartnerOrVendor($par_ven) {
	$vendor_partner_id = $this->input->post('vendor_partner_id');
	$flag = $this->input->post('invoice_flag');

	if ($par_ven == 'partner') {
	    if ($flag == 1) {
		echo "<option value='All'>All</option>";
	    }
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
	    if ($flag == 1) {
		echo "<option value='All'>All</option>";
	    }

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
    function delete_banktransaction($transaction_id, $vendor_partner, $vendor_partner_id) {
	$this->invoices_model->delete_banktransaction($transaction_id);
	redirect(base_url() . 'employee/invoice/invoice_summary/' . $vendor_partner . "/" . $vendor_partner_id);
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
	if (!in_array($type, $possible_type)) {
	    $type = 'vendor';
        }

	$invoice['bank_statement'] = $this->invoices_model->get_all_bank_transactions($type);

	$this->load->view('employee/header');
	$this->load->view('employee/view_transactions', $invoice);
    }

    /**
     * @desc: generate details partner Detailed invoices
     */
    function create_partner_invoices_detailed($partner_id,$f_date, $t_date, $invoice_type, $invoice_id) {
	log_message('info', __METHOD__ . "=> " . $invoice_type. " Partner Id ". $partner_id);
        $data = $this->invoices_model->getpartner_invoices($partner_id, $f_date, $t_date);              
	$file_names = array();
	$template = 'Partner_invoice_detail_template-v1.xlsx';
	//set absolute path to directory with template files
	$templateDir = __DIR__ . "/../excel-templates/";
                    
	
        if (!empty($data)) {
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

            $unique_booking = array_unique(array_map(function ($k) {
                    return $k['booking_id'];
                }, $data));

            $count = count($unique_booking);

            log_message('info', __FUNCTION__ . '=> Start Date: ' . $data[0]['start_date'] . ', End Date: ' . $data[0]['end_date']);

            $start_date = date("jS M, Y", strtotime($data[0]['start_date']));
            $end_date = date("jS M, Y", strtotime($data[0]['end_date']));
          
            foreach ($data as $key => $value) {
                switch ($value['price_tags']) {
                    case 'Wall Mount Stand':
                        $data[$key]['remarks'] = "Stand Delivered";
                        break;

                    case 'Repair':
                        $data[$key]['remarks'] = "Repair";
                        break;

                    case 'Repair - In Warranty':
                        $data[$key]['remarks'] = "Repair - In Warranty";
                        break;

                    case 'Repair - Out Of Warranty':
                        $data[$key]['remarks'] = "Repair - Out Of Warranty";
                        break;

                    default:
                        $data[$key]['remarks'] = "Installation Completed";
                        break;
                }

                $data[$key]['closed_date'] = date("jS M, Y", strtotime($value['closed_date']));
                $data[$key]['reference_date'] = date("jS M, Y", strtotime($value['reference_date']));

                $total_installation_charge += round($value['installation_charge'], 2);
                $total_service_tax += round($value['st'], 2);
                $total_stand_charge += round($value['stand'], 2);
                $total_vat_charge += round($value['vat'], 2);
                $total_charges = round(($total_installation_charge + $total_service_tax + $total_stand_charge + $total_vat_charge), 0);
                if ($invoice_type == "final") {
                    log_message('info', __METHOD__ . "=> Invoice update in booking unit details unit id" . $value['unit_id']. " Invoice Id". $invoice_id);
                    $this->booking_model->update_booking_unit_details_by_any(array('id'=>$value['unit_id']), array('partner_invoice_id'=> $invoice_id));   
                }
            }

            $excel_data['invoice_id'] = $invoice_id;
            $excel_data['today'] = date('d-M-Y');
            $excel_data['company_name'] = $data[0]['company_name'];
            $excel_data['company_address'] = $data[0]['company_address'];
            $excel_data['total_installation_charge'] = $total_installation_charge;
            $excel_data['total_service_tax'] = $total_service_tax;
            $excel_data['total_stand_charge'] = $total_stand_charge;
            $excel_data['total_vat_charge'] = $total_vat_charge;
            $excel_data['total_charges'] = $total_charges;
            $excel_data['period'] = $start_date ." To". $end_date;
            $excel_data['vendor_num'] = 'Vendor Number: 252752';

            log_message('info', 'Excel data: ' . print_r($excel_data, true));

            $files_name = $this->generate_pdf_with_data($excel_data, $data, $R);
             log_message('info', __METHOD__ . "=> File created ".$files_name );
            //Send report via email
            $this->email->clear(TRUE);
            $this->email->from('billing@247around.com', '247around Team');
            $to = "anuj@247around.com";
            $subject = "DRAFT Partner INVOICE Detailed- 247around - " . $data[0]['company_name'] .
                " Invoice for period: " . $start_date . " to " . $end_date;

            $this->email->to($to);
            $this->email->subject($subject);
            $this->email->attach($files_name . ".xlsx", 'attachment');
            $this->email->attach($files_name . ".pdf", 'attachment');

            $mail_ret = $this->email->send();

            if ($mail_ret) {
                log_message('info', __METHOD__ . ": Mail sent successfully");
                echo "Mail sent successfully..............." . PHP_EOL;
            } else {
                log_message('info', __METHOD__ . ": Mail could not be sent");
                echo "Mail could not be sent..............." . PHP_EOL;
            }

            array_push($file_names, $files_name . ".xlsx");
            array_push($file_names, $files_name . ".pdf");

            if ($invoice_type == "final") {
                log_message('info', __METHOD__ . "=> Final" );
                $bucket = BITBUCKET_DIRECTORY;

                $directory_xls = "invoices-excel/" . $files_name . ".xlsx";
                $directory_pdf = "invoices-pdf/" . $files_name . ".pdf";

                $this->s3->putObjectFile($files_name . ".xlsx", $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                $this->s3->putObjectFile($files_name . ".pdf", $bucket, $directory_pdf, S3::ACL_PUBLIC_READ);

                $invoice_details = array(
                    'invoice_id' => $invoice_id,
                    'type_code' => 'A',
                    'type' => 'Cash',
                    'vendor_partner' => 'partner',
                    'vendor_partner_id' => $data[0]['partner_id'],
                    'invoice_file_excel' => $files_name . '.xlsx',
                    'invoice_file_pdf' => $files_name . '.pdf',
                    'from_date' => date("Y-m-d", strtotime($start_date)), //??? Check this next time, format should be YYYY-MM-DD
                    'to_date' => date("Y-m-d", strtotime($end_date)),
                    'num_bookings' => $count,
                    'total_service_charge' => $excel_data['total_installation_charge'],
                    'total_additional_service_charge' => 0.00,
                    'service_tax' => $excel_data['total_service_tax'],
                    'parts_cost' => $excel_data['total_stand_charge'],
                    'vat' => $excel_data['total_charges'],
                    'total_amount_collected' => $excel_data['total_charges'],
                    'rating' => 5,
                    'around_royalty' => $excel_data['total_charges'],
                    //Amount needs to be collected from Vendor
                    'amount_collected_paid' => $excel_data['total_charges'],
                );

                $this->invoices_model->insert_new_invoice($invoice_details);
                log_message('info', __METHOD__ . "=> Insert Invoices in partner invoice table"  );

            }
            //Delete XLS files now
            foreach ($file_names as $file_name) {
                exec("rm -rf " . escapeshellarg($file_name));
            }
        } else {
            log_message('info', __METHOD__ . "=> Data Not found" . $invoice_type. " Partner Id ". $partner_id);
        }
	
    }

    /**
     * @desc: Generate Excel and Pdf File with invoices data and return file names
     * @param: Array(Excel data), Array(Invoices data), Initiallized PHP report library and files name
     * @return : File name
     */
    function generate_pdf_with_data($excel_data, $data, $R) {
        log_message('info', __METHOD__ );
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
	$output_file = $excel_data['invoice_id']."-detailed";
	$output_file_excel = $output_file_dir . $output_file . ".xlsx";
        $res1 = 0;
        if(file_exists($output_file_excel)){

            system(" chmod 777 ".$output_file_excel, $res1);
            unlink($output_file_excel);
        }
	//for xlsx: excel, for xls: excel2003
	$R->render('excel', $output_file_excel);
        system(" chmod 777 ".$output_file_excel, $res1);
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
        // Dump data in a file as a Json
        $file = fopen("/tmp/" . $output_file . ".txt", "w") or die("Unable to open file!");
        $res = 0;
        system(" chmod 777 /tmp/" . $output_file . ".txt", $res);
        $json_data['excel_data'] = $excel_data;
        $json_data['invoice_data'] =  $data;
        $contents = " Patner Invoice Json Data:\n";
        fwrite($file, $contents);
        fwrite($file, print_r(json_encode($json_data), TRUE));
        fclose($file);
        log_message('info', __METHOD__ . ": Json File Created");
   
        $bucket = BITBUCKET_DIRECTORY;
        $directory_xls = "invoices-json/" . $output_file . ".txt";
        $this->s3->putObjectFile("/tmp/".$output_file.".txt", $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
        log_message('info', __METHOD__ . ": Json File Uploded to S3");
        
        //Delete JSON files now
	exec("rm -rf " . escapeshellarg("/tmp/".$output_file.".txt"));

	return $output_file_dir . $output_file;
    }

    /**
     * It generates cash invoices for vendor
     * @param Array $invoices
     * @param Array $details
     * @return type
     */
    function generate_cash_details_invoices_for_vendors($invoices, $details) {
        log_message('info', __FUNCTION__ . '=> Entering...');
        
        $custom_date = explode("-", $details['date_range']);
        $from_date = $custom_date[0];
        $to_date = $custom_date[1];

        $unique_booking_cash = array();
        $invoice_sc_details = array();

        $template = 'Vendor_Settlement_Template-CashDetailed-v3.xlsx';
        // xls file directory
        $templateDir = __DIR__ . "/../excel-templates/";

        if (!empty($invoices)) {
            // it stores all unique booking id which is completed by particular vendor id
            $unique_booking = array_unique(array_map(function ($k) {
                        return $k['booking_id'];
                    }, $invoices['booking']));

            // Count unique booking id
            $count = count($unique_booking);

            // push unique booking array into another array
            array_push($unique_booking_cash, $unique_booking);

            log_message('info', __FUNCTION__ . '=> Start Date: ' .
                    $from_date . ', End Date: ' . $to_date);

            // set date format like 1st July 2016
            $start_date = date("jS M, Y", strtotime($from_date));
            $end_date = date("jS M, Y", strtotime('-1 day', strtotime($to_date)));

            log_message('info', 'Service Centre: ' . $details['vendor_partner_id'] . ', Count: ' . $count);

            //set config for report
            $config = array(
                'template' => $template,
                'templateDir' => $templateDir
            );

            //load template
            $R = new PHPReport($config);

            //A means it is for the 1st type of invoice as explained above
            //Make sure it is unique
            if (isset($details['invoice_id'])) {
                $invoice_id = $details['invoice_id'];
            } else {
                if ($invoices['booking'][0]['state'] == "DELHI") {

                    $invoice_version = "T";
                } else {
                    $invoice_version = "R";
                }


                $current_month = date('m');
                // 3 means March Month
                if ($current_month > 3) {
                    $financial = date('Y') . "-" . (date('Y') + 1);
                } else {
                    $financial = (date('Y') - 1) . "-" . date('Y');
                }

                //Make sure it is unique
                $invoice_id_tmp = "Around-" . $invoice_version . "-" . $financial . "-" . date("M", strtotime($from_date));
                $where = " `invoice_id` LIKE '%$invoice_id_tmp%'";
                $invoice_no = $this->invoices_model->get_invoices_details($where);

                $invoice_id = $invoice_id_tmp . "-" . (count($invoice_no) + 1);
            }

            $excel_data = $invoices['meta'];
            $excel_data['invoice_id'] = $invoice_id;
            $excel_data['vendor_name'] = $invoices['booking'][0]['company_name'];
            $excel_data['vendor_address'] = $invoices['booking'][0]['address'];
            $excel_data['sd'] = $start_date;
            $excel_data['ed'] = $end_date;
            $excel_data['invoice_date'] = date("jS M, Y", strtotime($end_date));
            $excel_data['count'] = $count;
            $excel_data['tin'] = $invoices['booking'][0]['tin'];
            $excel_data['service_tax_no'] = $invoices['booking'][0]['service_tax_no'];
            //set message to be displayed in excel sheet
            $excel_data['msg'] = 'Thanks 247around Partner for your support, we completed ' . $count .
                    ' bookings with you from ' . $start_date . ' to ' . $end_date .
                    '. Total transaction value for the bookings was Rs. ' . round($invoices['meta']['total_amount_paid'], 0) .
                    '. Around royalty for this invoice is Rs. ' . round($excel_data['r_total'], 0) .
                    '. Your rating for completed bookings is ' . $excel_data['t_rating'] .
                    '. We look forward to your continued support in future. As next step, please deposit ' .
                    '247around royalty per the below details.';

            $excel_data['beneficiary_name'] = $invoices['booking'][0]['beneficiary_name'];
            $excel_data['bank_account'] = $invoices['booking'][0]['bank_account'];
            $excel_data['bank_name'] = $invoices['booking'][0]['bank_name'];
            $excel_data['ifsc_code'] = $invoices['booking'][0]['ifsc_code'];

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
                    'data' => $invoices['booking'],
                    //'minRows' => 2,
                    'format' => array(
                        'create_date' => array('datetime' => 'd/M/Y'),
                        'total_price' => array('number' => array('prefix' => 'Rs. ')),
                    )
                ),
                    )
            );

            //Get populated XLS with data
            $output_file_dir = "/247around_tmp/";
            $output_file = $invoice_id;
            $output_file_excel = $output_file_dir . $output_file . "-detailed.xlsx";
            $res1 = 0;
            if (file_exists($output_file_excel)) {

                system(" chmod 777 " . $output_file_excel, $res1);
                unlink($output_file_excel);
            }

            //for xlsx: excel, for xls: excel2003
            $R->render('excel', $output_file_excel);
            system(" chmod 777 " . $output_file_excel, $res1);
            $this->email->clear(TRUE);
            $this->email->from('billing@247around.com', '247around Team');
            if ($details['invoice_type'] === "final") {
                $to = $invoices['booking'][0]['owner_email'] . ", " . $invoices['booking'][0]['primary_contact_email'];
                $subject = "247around - " . $invoices['booking'][0]['company_name'] .
                        " - Cash Invoice for period: " . $start_date . " to " . $end_date;
                $cc = "anuj@247around.com, nits@247around.com";
                 
             } else {
                $to = "anuj@247around.com";
                $cc = "";
                $subject = "Draft - Cash Invoices - 247around - " . $invoices['booking'][0]['company_name'];
             }
             
            $this->email->to($to);
            $this->email->attach($output_file_excel, 'attachment');
            $this->email->attach($output_file_dir.$invoice_id.".xlsx", 'attachment');

            $this->email->subject($subject);
            $mail_ret = $this->email->send();

            if ($mail_ret) {
                log_message('info', __METHOD__ . ": Mail sent successfully");
                echo "Mail sent successfully..............." . PHP_EOL;
            } else {
                log_message('info', __METHOD__ . ": Mail could not be sent");
                echo "Mail could not be sent..............." . PHP_EOL;
            }

            if ($details['invoice_type'] === "final") {
                
                //Send SMS to PoC/Owner
                $sms['tag'] = "vendor_invoice_mailed";
                $sms['smsData']['type'] = 'Cash';
                $sms['smsData']['month'] = date('M Y', strtotime($start_date));
                $sms['smsData']['amount'] = $invoices['meta']['total_amount_paid'];
                $sms['phone_no'] = $invoices['booking'][0]['owner_phone_1'];
                $sms['booking_id'] = "";
                $sms['type'] = "vendor";
                $sms['type_id'] = $invoices['booking'][0]['id'];

                $this->notify->send_sms_acl($sms);
                //Upload Excel files to AWS
                $bucket = BITBUCKET_DIRECTORY;
                $directory_xls = "invoices-excel/" . $invoice_id . "-detailed.xlsx";
                $invoice_upload = $this->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                if($invoice_upload){
                    log_message('info', __METHOD__ . ": Cash Detailed Invoices uploaded to S3");
                    echo " Cash Detailed Invoices uploaded to S3";

                } else {
                    log_message('info', __METHOD__ . ": Cash Detailed Invoices is not uploaded to S3");
                    echo " Cash Detailed Invoices is not uploaded to S3";
                }

                //Save this invoice info in table
                $invoice_details = array(
                    'invoice_id' => $invoice_id,
                    'type' => 'Cash',
                    'type_code' => 'A',
                    'vendor_partner' => 'vendor',
                    'vendor_partner_id' => $invoices['booking'][0]['id'],
                    'invoice_file_excel' => $invoice_id . '.xlsx',
                    //'invoice_file_pdf' => $output_file . '.pdf',
                    'from_date' => date("Y-m-d", strtotime($start_date)),
                    'to_date' => date("Y-m-d", strtotime($end_date)),
                    'num_bookings' => $count,
                    'total_service_charge' => $excel_data['r_sc'],
                    'total_additional_service_charge' => $excel_data['r_asc'],
                    'parts_cost' => $excel_data['r_pc'],
                    'vat' => 0, //No VAT here in Cash invoice
                    'total_amount_collected' => $excel_data['r_total'],
                    'rating' => $excel_data['t_rating'],
                    'around_royalty' => $excel_data['r_total'],
                    //Service tax which needs to be paid
                    'service_tax' => $excel_data['r_st'],
                    //Amount needs to be collected from Vendor
                    'amount_collected_paid' => $excel_data['r_total'],
                    //Mail has not 
                    'mail_sent' => $mail_ret,
                    //SMS has been sent or not
                    'sms_sent' => 1,
                    //Add 1 month to end date to calculate due date
                    'due_date' => date("Y-m-d", strtotime($end_date . "+1 month"))
                );

                $this->invoices_model->action_partner_invoice($invoice_details);

                log_message('info', __METHOD__ . ': Invoice ' . $invoice_id . ' details  entered into invoices table');

                /*
                 * Update booking-invoice table to capture this new invoice against these bookings.
                 * Since this is a type 'Cash' invoice, it would be stored as a vendor-debit invoice.
                 */
                $this->update_booking_invoice_mappings_repairs($invoices['booking'], $invoice_id);
            }


            // insert data into vendor invoices snapshot or draft table as per the invoice type
            $this->insert_cash_invoices_snapshot($invoices, $invoice_id, $details['invoice_type']);
            
            // Store Cash Invoices details
            $invoice_sc_details[$invoices['booking'][0]['id']]['cash_file_name'] = $output_file_excel;
            $invoice_sc_details[$invoices['booking'][0]['id']]['cash_amount'] = $excel_data['r_total'];
            $invoice_sc_details[$invoices['booking'][0]['id']]['cash_invoice_id'] = $invoice_id;
            $invoice_sc_details[$invoices['booking'][0]['id']]['start_date'] = $start_date;
            $invoice_sc_details[$invoices['booking'][0]['id']]['end_date'] = $end_date;

            exec("rm -rf " . escapeshellarg($output_file_excel));
            exec("rm -rf " . escapeshellarg($output_file_dir.$invoice_id.".xlsx"));
            log_message('info', __METHOD__ . ' Exit ');
            unset($excel_data);
        } else {
            //Enter log and echo here
            
        }


        return $invoice_sc_details;
    }

    /**
     * @desc: This Method used to insert foc invoice snapshot into vendor invoices snapshot table
     * @param $invoices_data Array Misc data about Invoice
     * @param $invoice_id String Invoice ID
     * @param $invoice_type String Invoice Type (draft/final)
     */
    function insert_foc_invoices_snapshot($invoices_data, $invoice_id, $invoice_type) {
        log_message('info', __METHOD__ . ': Reset Invoice id '. " invoice id ". $invoice_id);
        $this->booking_model->update_booking_unit_details_by_any(array('vendor_foc_invoice_id'=> $invoice_id), array('vendor_foc_invoice_id'=> NULL));
	$data = array();
	foreach ($invoices_data as $value) {
            if ($invoice_type == "final") {
                
                log_message('info', __METHOD__ . ': update invoice id in booking unit details '. $value['unit_id']. " invoice id ". $invoice_id);
                $this->booking_model->update_booking_unit_details_by_any(array('id' => $value['unit_id']), array('vendor_foc_invoice_id'=> $invoice_id));   
             }
	    $data['booking_id'] = $value['booking_id'];
	    $data['invoice_id'] = $invoice_id;
	    $data['vendor_id'] = $invoices_data[0]['id'];
	    $data['type_code'] = "B";
	    $data['city'] = $value['city'];
	    $data['appliance'] = $value['services'];
	    $data['appliance_category'] = $value['appliance_category'];
	    $data['appliance_capacity'] = $value['appliance_capacity'];
	    $data['closed_date'] = $value['closed_booking_date'];
	    $data['service_category'] = $value['price_tags'];
	    $data['service_charge'] = $value['vendor_installation_charge'];
	    $data['service_tax'] = $value['vendor_st'];
	    $data['stand'] = $value['vendor_stand'];
	    $data['vat'] = $value['vendor_vat'];
	    $data['amount_paid'] = $value['amount_paid'];
	    $data['rating'] = $value['rating_stars'];

	    $this->invoices_model->insert_invoice_row($data, $invoice_type);
	}
    }

    /**
     * @desc: This Method used to insert Cash invoice snapshot into vendor invoices snapshot table
     * @param $invoices_data Array Misc data about Invoice
     * @param $invoice_id String Invoice ID
     * @param $invoice_type String Invoice Type (draft/final)
     */
    function insert_cash_invoices_snapshot($invoices_data, $invoice_id, $invoice_type) {
	$data = array();
         log_message('info', __METHOD__ . ': Reset Invoice id '. " invoice id ". $invoice_id);
        //Reset Vendor Cash invoice id
        $this->booking_model->update_booking_unit_details_by_any(array('vendor_cash_invoice_id'=>$invoice_id), array('vendor_cash_invoice_id' => NULL));
	foreach ($invoices_data['booking'] as $value) {
            if ($invoice_type == "final") {
                log_message('info', __METHOD__ . ': update invoice id in booking unit details '. $value['unit_id']. " invoice id ". $invoice_id);

                    $this->booking_model->update_booking_unit_details_by_any(array('id'=>$value['unit_id']), array('vendor_cash_invoice_id'=> $invoice_id));     
            }
	    $data['booking_id'] = $value['booking_id'];
	    $data['invoice_id'] = $invoice_id;
	    $data['vendor_id'] = $value['id'];
	    $data['type_code'] = "A";
	    $data['city'] = $value['city'];
	    $data['appliance'] = $value['services'];
	    $data['appliance_category'] = $value['appliance_category'];
	    $data['appliance_capacity'] = $value['appliance_capacity'];
	    $data['closed_date'] = $value['closed_booking_date'];
	    $data['service_category'] = $value['price_tags'];
	    $data['service_charge'] = $value['service_charges'];
	    $data['around_discount'] = $value['around_net_payable'];
	    $data['addtional_service_charge'] = $value['additional_charges'];
	    $data['parts_cost'] = $value['parts_cost'];
	    $data['amount_paid'] = $value['amount_paid'];
	    $data['rating'] = $value['rating_stars'];

	    $this->invoices_model->insert_invoice_row($data, $invoice_type);
	}
    }

    /**
     * @desc: This is used to generates foc type invoices for vendor
     * @param: Array()
     * @return: Array (booking id)
     */
    function generate_foc_details_invoices_for_vendors($invoices, $details) {
        log_message('info', __FUNCTION__ . '=> Entering...');
        $custom_date = explode("-", $details['date_range']);
        $from_date = $custom_date[0];
        $to_date = $custom_date[1];
        $unique_booking_foc = array();
        $invoice_sc_details = array();

        $template = 'Vendor_Settlement_Template-FoC-v4.xlsx';
        // directory
        $templateDir = __DIR__ . "/../excel-templates/";
                    
        if (!empty($invoices)) {
            $total_inst_charge = 0;
            $total_st_charge = 0;
            $total_stand_charge = 0;
            $total_vat_charge = 0;
            
            if (isset($details['invoice_id'])) {
                log_message('info', __FUNCTION__. " Re-Generate Invoice id ". $details['invoice_id']);
                $invoice_id = $details['invoice_id'];
            } else {
                if ($invoices[0]['state'] == "DELHI") {

                    $invoice_version = "T";
                } else {
                    $invoice_version = "R";
                }

                $current_month = date('m');
                // 3 means March Month
                if ($current_month > 3) {
                    $financial = date('Y') . "-" . (date('Y') + 1);
                } else {
                    $financial = (date('Y') - 1) . "-" . date('Y');
                }

                //Make sure it is unique
                $invoice_id_tmp = $invoices[0]['sc_code'] . "-" . $invoice_version . "-" . $financial . "-" . date("M", strtotime($from_date));
                $where = " `invoice_id` LIKE '%$invoice_id_tmp%'";
                $invoice_no = $this->invoices_model->get_invoices_details($where);

                $invoice_id = $invoice_id_tmp . "-" . (count($invoice_no) + 1);
                log_message('info', __FUNCTION__. " Generate Invoice id ". $invoice_id);
            }

            // Calculate charges
            for ($j = 0; $j < count($invoices); $j++) {
                $total_inst_charge += $invoices[$j]['vendor_installation_charge'];
                $total_st_charge += $invoices[$j]['vendor_st'];
                $total_stand_charge += $invoices[$j]['vendor_stand'];
                $total_vat_charge += $invoices[$j]['vendor_vat'];
                $invoices[$j]['amount_paid'] = round(($invoices[$j]['vendor_installation_charge'] + $invoices[$j]['vendor_st'] + $invoices[$j]['vendor_stand'] + $invoices[$j]['vendor_vat']),0);

            }

            $t_total = $total_inst_charge + $total_stand_charge + $total_st_charge + $total_vat_charge;

            //this array stores unique booking id
            $unique_booking = array_unique(array_map(function ($k) {
                        return $k['booking_id'];
                    }, $invoices));

            // count unique booking id
            $count = count($unique_booking);

            // push unique booking id into another array
            array_push($unique_booking_foc, $unique_booking);

            log_message('info', __FUNCTION__ . '=> Start Date: ' . $from_date . ', End Date: ' . $to_date);

            //set date format like 1st june 2016
            $start_date = date("jS M, Y", strtotime($from_date));
            $end_date = date("jS M, Y", strtotime('-1 day', strtotime($to_date)));

            log_message('info', 'Service Centre: ' . $invoices[0]['id'] . ', Count: ' . $count);

            //set config for report
            $config = array(
                'template' => $template,
                'templateDir' => $templateDir
            );

            //load template
            $R = new PHPReport($config);

            $t_w_total = $t_total;
            $tds = 0;

            // stores charges
            $excel_data = array(
                't_ic' => $total_inst_charge,
                't_st' => $total_st_charge,
                't_stand' => $total_stand_charge,
                't_vat' => $total_vat_charge,
                't_total' => $t_total,
                't_rating' => $invoices[0]['avg_rating'],
                'tds' => $tds,
                't_vp_w_tds' => round($t_w_total, 0) // vendor payment without TDS
            );

            $excel_data['invoice_id'] = $invoice_id;
            $excel_data['vendor_name'] = $invoices[0]['company_name'];
            $excel_data['vendor_address'] = $invoices[0]['address'];
            $excel_data['sd'] = $start_date;
            $excel_data['ed'] = $end_date;
            $excel_data['invoice_date'] = date("jS M, Y", strtotime($end_date));
            $excel_data['count'] = $count;
            $excel_data['tin'] = $invoices[0]['tin'];
            $excel_data['service_tax_no'] = $invoices[0]['service_tax_no'];

            $excel_data['msg'] = 'Thanks 247around Partner for your support, we completed ' . $count .
                    ' bookings with you from ' . $start_date . ' to ' . $end_date .
                    '. Total transaction value for the bookings was Rs. ' . round($excel_data['t_total'],0) .
                    '. Your rating for completed bookings is ' . round($excel_data['t_rating'],0) .
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
            $output_file_excel = $output_file_dir . $output_file . "-detailed.xlsx";
            $res1 = 0;
            if(file_exists($output_file_excel)){

                system(" chmod 777 ".$output_file_excel, $res1);
                unlink($output_file_excel);
            }

            //for xlsx: excel, for xls: excel2003
            $R->render('excel', $output_file_excel);
            $res2 = 0;
            system(" chmod 777 ".$output_file_excel, $res2);
            log_message('info', __FUNCTION__. " Excel File Created ". $output_file_excel);
            
             if ($details['invoice_type'] === "final") {
                $to = $invoices[0]['owner_email'] . ", " .$invoices[0]['primary_contact_email'];
                $subject = "247around - " . $invoices[0]['company_name'] . " - FOC Invoice for period: " .  $start_date . " to " .  $end_date;
                $cc = "anuj@247around.com, nits@247around.com";
                 
             } else {
                $this->email->clear(TRUE);
                $this->email->from('billing@247around.com', '247around Team');
                $to = "anuj@247around.com";
                $subject = "Draft - FOC INVOICE (Detailed) - 247around - " . $invoices[0]['company_name'];
                $cc = "";
                 
             }
             
            $this->email->to($to);
            $this->email->cc($cc);
            $this->email->subject($subject);
            $this->email->attach($output_file_excel, 'attachment');
            $this->email->attach($output_file_dir . $output_file.".xlsx", 'attachment');
            $mail_ret = $this->email->send();

            if ($mail_ret) {
                log_message('info', __METHOD__ . ": Mail sent successfully");
                echo "Mail sent successfully..............." . PHP_EOL;
            } else {
                log_message('info', __METHOD__ . ": Mail could not be sent");
                echo "Mail could not be sent..............." . PHP_EOL;
            }


            if ($details['invoice_type'] === "final") {
                log_message('info', __FUNCTION__. " Final" );
               
                //Send SMS to PoC/Owner
                $sms['tag'] = "vendor_invoice_mailed";
                $sms['smsData']['type'] = 'FOC';
                $sms['smsData']['month'] = date('M Y', strtotime($start_date));
                $sms['smsData']['amount'] = $excel_data['t_total'];
                $sms['phone_no'] = $invoices[0]['owner_phone_1'];
                $sms['booking_id'] = "";
                $sms['type'] = "vendor";
                $sms['type_id'] = $invoices[0]['id'];

                $this->notify->send_sms_acl($sms);
                log_message('info', __FUNCTION__. " SMS Sent" );
                //Upload Excel files to AWS
                $bucket = BITBUCKET_DIRECTORY;
                $directory_xls = "invoices-excel/" . $invoice_id . "-detailed.xlsx";
                
                $foc_detailed = $this->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                if($foc_detailed){
                    
                    log_message('info', __METHOD__ . ": Invoices uploaded to S3 ".$invoice_id . "-detailed.xlsx");
                    echo ": Invoices uploaded to S3 ".$invoice_id . "-detailed.xlsx";
                
                } else {
                    
                    log_message('info', __METHOD__ . ": Invoices Not uploaded to S3 ".$invoice_id . "-detailed.xlsx");
                    echo ": Invoices not uploaded to S3 ".$invoice_id . "-detailed.xlsx";
                }
                //Save this invoice info in table
                $invoice_details = array(
                    'invoice_id' => $invoice_id,
                    'type' => 'FOC',
                    'type_code' => 'B',
                    'vendor_partner' => 'vendor',
                    'vendor_partner_id' => $invoices[0]['id'],
                    'invoice_file_excel' => $invoice_id . '.xlsx',
                    //'invoice_file_pdf' => $output_file . '.pdf',
                    'from_date' => date("Y-m-d", strtotime($start_date)),
                    'to_date' => date("Y-m-d", strtotime($end_date)),
                    'num_bookings' => $count,
                    'total_service_charge' => $total_inst_charge,
                    'total_additional_service_charge' => 0,
                    'service_tax' => $total_st_charge,
                    'parts_cost' => $total_stand_charge,
                    'vat' => $total_vat_charge,
                    'total_amount_collected' => $t_total,
                    'tds_amount' => $excel_data['tds'],
                    'rating' => $excel_data['t_rating'],
                    'around_royalty' => 0,
                    //Amount needs to be Paid to Vendor
                    'amount_collected_paid' => (0 - $excel_data['t_vp_w_tds']),
                    //Mail has not sent
                    'mail_sent' => $mail_ret,
                    //SMS has been sent or not
                    'sms_sent' => 1,
                    //Add 1 month to end date to calculate due date
                    'due_date' => date("Y-m-d", strtotime($end_date . "+1 month"))
                );

                // insert invoice details into vendor partner invoices table
                $this->invoices_model->action_partner_invoice($invoice_details);

                log_message('info', __METHOD__ . ': Invoice ' . $invoice_id . ' details  entered into invoices table');

                /*
                 * Update booking-invoice table to capture this new invoice against these bookings.
                 * Since this is a type B invoice, it would be stored as a vendor-credit invoice.
                 */
                $this->update_booking_invoice_mappings_installations($invoices, $invoice_id);
            }

            // insert data into vendor invoices snapshot or draft table as per the invoice type
            $this->insert_foc_invoices_snapshot($invoices, $invoice_id, $details['invoice_type']);

            // Store foc invoices
            $invoice_sc_details[$invoices[0]['id']]['foc_invoice_file_name'] = $output_file_excel;
            $invoice_sc_details[$invoices[0]['id']]['foc_amount'] = $t_total;
            $invoice_sc_details[$invoices[0]['id']]['foc_invoice_id'] = $invoice_id;
            $invoice_sc_details[$invoices[0]['id']]['start_date'] = $start_date;
            $invoice_sc_details[$invoices[0]['id']]['end_date'] = $end_date;

            unset($excel_data);
              exec("rm -rf " . escapeshellarg($output_file_excel));
              exec("rm -rf " . escapeshellarg($output_file_dir . $output_file.".xlsx"));

        } else {
            log_message('info', __FUNCTION__. "Exit data not found ". print_r($details, TRUE));
        }


        return $invoice_sc_details;
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

    /**
     * @desc: This Method loads invoice form
     */
    function get_invoices_form() {
	$data['vendor_partner'] = "vendor";
	$data['id'] = "";
	$this->load->view('employee/header');
	$this->load->view('employee/get_invoices_form', $data);
    }
    /**
     * @desc: This method is used to generate invoices for partner or vendor.
     * This methd is used to get data from Form.
     */
    function process_invoices_form() {
        log_message('info', __FUNCTION__. " Entering......");
	$details['vendor_partner'] = $this->input->post('partner_vendor');
	$details['invoice_type'] = $this->input->post('invoice_version');
	$details['vendor_partner_id'] = $this->input->post('partner_vendor_id');
	$details['invoice_month'] = $this->input->post('invoice_month');
	$details['vendor_invoice_type'] = $this->input->post('vendor_invoice_type');
        
        $this->generate_vendor_partner_invoices($details);
        redirect(base_url() . "employee/invoice/get_invoices_form");

	
    }
    /**
     * @desc This is used to generate invoice from terminal.
     * Use param like this - 'vendor','final','1','04','foc'
     * @param type $vendor_partner
     * @param type $invoice_type
     * @param type $vendor_partner_id
     * @param type $invoice_month
     * @param type $vendor_invoice_type
     */
    function process_invoices_from_terminal($vendor_partner,$invoice_type,$vendor_partner_id,$invoice_month,$vendor_invoice_type){
        log_message('info', __FUNCTION__. " Entering......");
	$details['vendor_partner'] = $vendor_partner;
	$details['invoice_type'] =$invoice_type;
	$details['vendor_partner_id'] = $vendor_partner_id;
	$details['invoice_month'] = $invoice_month;
	$details['vendor_invoice_type'] =$vendor_invoice_type;
        
        $this->generate_vendor_partner_invoices($details);
    }
    /**
     * @desc: 
     * @param array $details
     */
    function generate_vendor_partner_invoices($details){
        log_message('info', __FUNCTION__. " Entering......");
        $next_month = "";
	$year = "";
           
	if ($details['invoice_month'] == 12) {
	    $next_month = 01;
	    $year = date('Y') + 1;
	} else {
	    $next_month = $details['invoice_month'] + 1;
	    $year = date('Y');
	}

	$details['date_range'] = date('Y') . "/" . $details['invoice_month'] . "/01-" . $year . "/" . $next_month . "/01";
        print_r($details);
        
        if ($details['vendor_partner'] === "vendor") {
            echo "Invoice Generating..";
	    log_message('info', "Invoice generate - vendor id: " . print_r($details['vendor_partner_id'], true) . ", Date Range" .
		print_r($details['date_range'], true) . ", Invoice version" . print_r($details['invoice_type'], true) . ", Invoice type" .
		print_r($details['vendor_invoice_type'], true));

            $this->generate_vendor_invoices($details);
            
	} else if ($details['vendor_partner'] === "partner") {
	    log_message('info', "Invoice generate - partner id: " . print_r($details['vendor_partner_id'], true) . ", Date Range" .
		print_r($details['date_range'], true) . ", Invoice status" . print_r($details['invoice_type'], true));

	    $this->generate_partner_invoices($details['vendor_partner_id'], $details['date_range'], $details['invoice_type']);
	}
        
        log_message('info', __FUNCTION__. " Exit......");
    }
    /**
     * @desc: This method is used to Re-Generate Invoice id. 
     * @param String $invoice_id
     * @param String $invoice_type
     */
    function regenerate_invoice($invoice_id, $invoice_type){
        $where = " `invoice_id` = '$invoice_id'";
        //Get Invocie details from Vendor Partner Invoice Table
        $invoice_details = $this->invoices_model->get_invoices_details($where);
        if (!empty($invoice_details)) {
           
            $details['vendor_partner'] = $invoice_details[0]['vendor_partner'];
            $details['invoice_type'] = $invoice_type;
            $details['invoice_id'] = $invoice_id;
            $details['vendor_partner_id'] = $invoice_details[0]['vendor_partner_id'];
            $details['date_range'] = str_replace("-", "/", $invoice_details[0]['from_date']) . "-" . str_replace("-", "/", date('Y-m-d', strtotime('+1 day', strtotime($invoice_details[0]['to_date']))));

            if ($invoice_details[0]['vendor_partner'] == 'partner') {
//            if($invoice_type == 'final'){
//                $where_unit = array('partner_invoice_id'=>$invoice_id);
//                $data['partner_invoice_id'] = NULL;
//                //Reset Partner invoice id
//                $this->booking_model->update_booking_unit_details_by_any($where_unit, $data);
//                log_message('info', "Invoice generate - partner id: " . print_r($details['vendor_partner_id'], true) . ", Date Range" .
//                    print_r($details['date_range'], true) . ", Invoice status" . print_r($details['invoice_type'], true));
//            }
//            //Generate Invoice Id     
//	    $this->generate_partner_invoices($details['vendor_partner_id'], $details['date_range'], $details['invoice_type']);
            } else if ($invoice_details[0]['vendor_partner'] == 'vendor' && $invoice_details[0]['type'] != "Stand") {
                if ($invoice_details[0]['type'] == "FOC") {
                    $where_unit = array('vendor_foc_invoice_id' => $invoice_id);
                } else if ($invoice_details[0]['type'] == "Cash") {
                    $where_unit = array('vendor_cash_invoice_id' => $invoice_id);
                } 

                $unit_details = $this->booking_model->get_unit_details($where_unit);
                // Check is null vendor foc invoice id
                if (!is_null($unit_details[0]['vendor_cash_invoice_id'])) {

                    $details['vendor_invoice_type'] = "cash";
                    log_message('info', "Invoice generate - vendor id: " . print_r($details['vendor_partner_id'], true) . ", Date Range" .
                            print_r($details['date_range'], true) . ", Invoice version" . print_r($details['invoice_type'], true) . ", Invoice type" .
                            print_r($details['vendor_invoice_type'], true));

                    $this->generate_vendor_invoices($details);
                }
                // Check is null vendor foc invoice id
                if (!is_null($unit_details[0]['vendor_foc_invoice_id'])) {

                    $details['vendor_invoice_type'] = "foc";
                    log_message('info', "Invoice generate - vendor id: " . print_r($details['vendor_partner_id'], true) . ", Date Range" .
                            print_r($details['date_range'], true) . ", Invoice version" . print_r($details['invoice_type'], true) . ", Invoice type" .
                            print_r($details['vendor_invoice_type'], true));

                    $this->generate_vendor_invoices($details);
                }
            } else if($invoice_details[0]['type'] == "Stand"){
                $details['vendor_invoice_type'] = "brackets";
                log_message('info', "Invoice generate - vendor id: " . print_r($details['vendor_partner_id'], true) . ", Date Range" .
                            print_r($details['date_range'], true) . ", Invoice version" . print_r($details['invoice_type'], true) . ", Invoice type" .
                            print_r($details['vendor_invoice_type'], true));
                $this->generate_vendor_invoices($details);
            }
        }

        redirect(base_url() . "invoice/invoice_summary/".$details['vendor_partner']."/".$details['vendor_partner_id']);
        
    }

    /**
     * @desc: this method is used to generate both type invoices (invoices details and summary)
     * @param type $partner_id
     * @param type $date_range
     * @param type $invoice_type
     */
    function generate_partner_invoices($partner_id, $date_range, $invoice_type) {
        log_message('info', __FUNCTION__ . '=> Entering... Partner Id'.$partner_id. " date range ". $date_range. " invoice type ". $invoice_type );
        $custom_date = explode("-", $date_range);
        $from_date = $custom_date[0];
        $to_date = $custom_date[1];
        
        if ($partner_id == "All") {
            $partner = $this->partner_model->get_all_partner_source();
            foreach($partner as $value){
                log_message('info', __FUNCTION__ . '=> Partner Id '. $value['partner_id']);
                $invoice_id = $this->create_partner_invoice($value['partner_id'], $from_date,$to_date,$invoice_type);
                $this->create_partner_invoices_detailed($value['partner_id'], $from_date,$to_date, $invoice_type, $invoice_id);
            }
            
        } else{
            log_message('info', __FUNCTION__ . '=> Partner Id '. $partner_id);
            $invoice_id = $this->create_partner_invoice($partner_id, $from_date,$to_date, $invoice_type);
            $this->create_partner_invoices_detailed($partner_id, $from_date,$to_date, $invoice_type, $invoice_id);
        }
        
        log_message('info', __FUNCTION__ . '=> Exiting...');        
    }

    /**
     * This method used to generates previous invoice for vendor
     * @param Array $details
     */
    function generate_vendor_invoices($details) {
         log_message('info', __FUNCTION__. " Entering......". " Details: ". print_r($details, true));

	switch ($details['vendor_invoice_type']) {
	    case "cash":

                 if($details['vendor_partner_id'] == 'All'){
                    
                    $vendor_details = $this->vendor_model->getActiveVendor('',0);
                    foreach ($vendor_details as $value) {
                        $details['vendor_partner_id'] = $value['id'];
                         log_message('info', __FUNCTION__. " Preparing CASH Invoice  Vendor: ". $details['vendor_partner_id'] );
                         echo " Preparing CASH Invoice  Vendor: ". $details['vendor_partner_id'] ;
                        //Prepare main invoice first
                        $details['invoice_id'] = $this->generate_vendor_cash_invoice($details);
                        if($details['invoice_id']){
                            //Generate detailed annexure now
                            $data = $this->invoices_model->get_vendor_cash_detailed($details['vendor_partner_id'], 
                                    $details['date_range']);
                            $this->generate_cash_details_invoices_for_vendors($data, $details);

                        } else {
                            echo  " Data Not found for vendor: ". $details['vendor_partner_id'] ;
                            log_message('info', __FUNCTION__. " Data Not found for vendor: ". $details['vendor_partner_id'] );
                        }
                    }
                    
                } else {
                    echo  " Preparing CASH Invoice  Vendor: ". $details['vendor_partner_id'];
                   
                    log_message('info', __FUNCTION__. ": Preparing CASH Invoice Vendor Id: ".$details['vendor_partner_id']);
                     
                    //Prepare main invoice first
                    $details['invoice_id'] = $this->generate_vendor_cash_invoice($details);
                    if($details['invoice_id']){
                        //Generate detailed annexure now
                        $data = $this->invoices_model->get_vendor_cash_detailed($details['vendor_partner_id'], 
                                $details['date_range']);
                        $this->generate_cash_details_invoices_for_vendors($data, $details);
                        
                    } else {
                        echo  " Data Not found for vendor: ". $details['vendor_partner_id'] ;
                        log_message('info', __FUNCTION__. " Data Not found for vendor: ". $details['vendor_partner_id'] );
                    }
                }
		break;

	    case "foc":
                log_message('info', __FUNCTION__. " Preparing FOC Invoice");
                
                if($details['vendor_partner_id'] == 'All'){
                    
                    $vendor_details = $this->vendor_model->getActiveVendor('',0);
                    echo " Preparing CASH Invoice  Vendor: ". $details['vendor_partner_id'] ;
                    foreach ($vendor_details as $value) {
                        $details['vendor_partner_id'] = $value['id'];
                        log_message('info', __FUNCTION__. ": Preparing FOC Invoice Vendor Id: ".$details['vendor_partner_id']);
                        //Prepare main invoice first
                        $details['invoice_id'] = $this->generate_vendor_foc_invoice($details);
                        
                        if( $details['invoice_id']){
                            //Generate detailed annexure now                
                            $data = $this->invoices_model->generate_vendor_foc_detailed_invoices($details['vendor_partner_id'], 
                                    $details['date_range']);
                            $this->generate_foc_details_invoices_for_vendors($data, $details);
                             
                         } else {
                            echo  " Data Not found for vendor: ". $details['vendor_partner_id'] ;
                            log_message('info', __FUNCTION__. " Data Not found for vendor: ". $details['vendor_partner_id'] );
                        }
                    }
                   
                } else {
                        //Prepare main invoice first
                        $details['invoice_id'] = $this->generate_vendor_foc_invoice($details);
                        log_message('info', __FUNCTION__. ": Preparing FOC Invoice Vendor Id: ".$details['vendor_partner_id']);
                        echo " Preparing CASH Invoice  Vendor: ". $details['vendor_partner_id'] ;
                        if( $details['invoice_id']){
                            //Generate detailed annexure now                
                            $data = $this->invoices_model->generate_vendor_foc_detailed_invoices($details['vendor_partner_id'], 
                                    $details['date_range']);
                            $this->generate_foc_details_invoices_for_vendors($data, $details);
                            
                        } else {
                            echo  " Data Not found for vendor: ". $details['vendor_partner_id'] ;
                            log_message('info', __FUNCTION__. " Data Not found for vendor: ". $details['vendor_partner_id'] );
                        }
                }
                break;
            
            case "brackets":
                log_message('info', __FUNCTION__. " Brackets");
                //This constant is used to track all vendors selected to avoid sending mail when all vendor +draft is selected
                $vendor_all_flag = 0;
                if($details['vendor_partner_id'] === 'All'){
                    $vendor_all_flag = 1;
                    $vendor = $this->vendor_model->getActiveVendor('',0);
                    
                    foreach ($vendor as $value) {
                        log_message('info', __FUNCTION__. " Brackets Vendor Id: ".$value['id'] );
                        $details['vendor_partner_id'] = $value['id'];
                        //Generating and sending invoice to vendors
                        $this->send_brackets_invoice_to_vendors($details,$vendor_all_flag);
		}

                }else{
                     log_message('info', __FUNCTION__. " Brackets Vendor Id: ".$details['vendor_partner_id'] );
                    //Generating and sending invoice to vendors
                    $this->send_brackets_invoice_to_vendors($details, $vendor_all_flag);
                    
                }
		break;
	   
	}
    }

    /**
     * @desc: This Method is used to load Invoice details for particular Vendor
     * @param: Vendor Partner
     * @param: Vendor id
     */
    function invoice_summary($vendor_partner, $vendor_partner_id) {
        if($vendor_partner ==  'vendor'){
            $data['service_center'] = $this->vendor_model->getActiveVendor("", 0);
        } else {
            $data['partner'] =$this->partner_model->getpartner();
        }
	
	$data['vendor_partner_id'] = $vendor_partner_id;
	$data['vendor_partner'] = $vendor_partner;
	$this->load->view('employee/header');
	$this->load->view('employee/invoices_details', $data);
    }

    
    /**
     * @Desc: This function is used to generate brackets invoices for vendors
     * @params: vendor id, invoice_month, $invoice_type
     * @return : Mix
     */
    function generate_brackets_invoices($details){
       log_message('info', __FUNCTION__. " Entering......... ". print_r($details, true) );
        $vendor_id = $details['vendor_partner_id'];
        $date_range = $details['date_range'];
        $invoice_type = $details['invoice_type'];
        
        $custom_date = explode("-", $date_range);
        $from_date = $custom_date[0];
        $to_date = $custom_date[1];
        //Making invoice array
        $invoice = $this->inventory_model->get_vendor_bracket_invoices($vendor_id, $from_date,$to_date);

        if (!empty($invoice)) {
           
            $invoice[0]['period'] = date("jS M, Y", strtotime($from_date))." To ".date('jS M, Y', strtotime('-1 day', strtotime($to_date)));
            $invoice[0]['today'] = date("jS M, Y", strtotime($to_date));
       if (isset($details['invoice_id'])) {
             log_message('info', __METHOD__ . ": Invoice Id re- geneterated ".  $details['invoice_id'] );
           $invoice[0]['invoice_number'] = $details['invoice_id'];
        
        
        } else {
           if($invoice[0]['state'] == "DELHI"){
                    
                $type = "T";
                $invoice[0]['invoice_type'] = "TAX INVOICE";
                    
            } else {
                    $type = "R";
                    $invoice[0]['invoice_type'] = "RETAIL INVOICE";
            }
                
            $current_month = date('m');
            // 3 means March Month
            if($current_month >3){
                $financial =  date('Y')."-".(date('y') +1);
            } else {
                $financial =  (date('Y') -1)."-".date('y') ;
            }
                
		
           //Make sure it is unique
            $invoice_id_tmp =  "Around-" .$type . "-" . $financial."-".date("M", strtotime(date($from_date)));
            $where = " `invoice_id` LIKE '%$invoice_id_tmp%'";
            $invoice_no = $this->invoices_model->get_invoices_details($where);
            
            $invoice[0]['invoice_number'] = $invoice_id_tmp."-".(count($invoice_no) + 1);
        }
            
            log_message('info', __FUNCTION__. " Entering......... Invoice Id ". $invoice[0]['invoice_number']);
            $invoice[0]['invoice_date']  = date("jS M, Y", strtotime($to_date));
            $invoice[0]['tax_rate'] = 5.00;
            $invoice[0]['19_24_tax_total'] = $this->booking_model->get_calculated_tax_charge(_247AROUND_BRACKETS_19_24_UNIT_PRICE, $invoice[0]['tax_rate']);
            $invoice[0]['26_32_tax_total'] = $this->booking_model->get_calculated_tax_charge(_247AROUND_BRACKETS_26_32_UNIT_PRICE, $invoice[0]['tax_rate']);
            $invoice[0]['36_42_tax_total'] = $this->booking_model->get_calculated_tax_charge(_247AROUND_BRACKETS_36_42_UNIT_PRICE, $invoice[0]['tax_rate']);
            $invoice[0]['19_24_unit_price'] = _247AROUND_BRACKETS_19_24_UNIT_PRICE - $invoice[0]['19_24_tax_total'];
            $invoice[0]['26_32_unit_price'] = _247AROUND_BRACKETS_26_32_UNIT_PRICE - $invoice[0]['26_32_tax_total'];
            $invoice[0]['36_42_unit_price'] = _247AROUND_BRACKETS_36_42_UNIT_PRICE - $invoice[0]['36_42_tax_total'];
            
            $invoice[0]['total_brackets'] = $invoice[0]['_19_24_total'] + $invoice[0]['_26_32_total'] + $invoice[0]['_36_42_total'];
            $invoice[0]['t_19_24_unit_price'] = $invoice[0]['_19_24_total'] * $invoice[0]['19_24_unit_price'];
            $invoice[0]['t_26_32_unit_price'] = $invoice[0]['_26_32_total'] * $invoice[0]['26_32_unit_price'];
            $invoice[0]['t_36_42_unit_price'] = $invoice[0]['_36_42_total'] * $invoice[0]['36_42_unit_price'];
            $invoice[0]['total_part_cost'] = ($invoice[0]['t_19_24_unit_price'] + $invoice[0]['t_26_32_unit_price'] + $invoice[0]['t_36_42_unit_price']);
            $invoice[0]['part_cost_vat'] = round($invoice[0]['total_part_cost'] * $invoice[0]['tax_rate']/100,2);
            $invoice[0]['sub_total'] = round(($invoice[0]['part_cost_vat'] + $invoice[0]['total_part_cost'] ),2);
            $invoice[0]['total'] = round(($invoice[0]['part_cost_vat'] + $invoice[0]['total_part_cost'] ),0);
            $invoice[0]['price_inword']  = convert_number_to_words($invoice[0]['total']);
            
            //Creating excel report
            $output_file_excel = $this->create_vendor_brackets_invoice($invoice[0]);
           
        
        if (isset($output_file_excel)) {
           
            // Sending SMS  to Vendor , adding value in vednor_partner_invoice table when invoice type is FINAL
            if ($invoice_type == 'final') {
                 log_message('info', __FUNCTION__. " Final");
//                //Inserting invoice id in Brackets Table against order id
//                $update_brackets_array['invoice_id'] = $invoice[0]['invoice_number'];
//                $update_brackets = $this->inventory_model->update_brackets($update_brackets_array, array('order_id' => $order_id));
                
                //Send SMS to PoC/Owner
                $sms['tag'] = "vendor_invoice_mailed";
                $sms['smsData']['type'] = 'Stand';
                $sms['smsData']['month'] = date('M Y', strtotime($from_date));
                $sms['smsData']['amount'] = $invoice[0]['total'];
                $sms['phone_no'] = $invoice[0]['owner_phone_1'];
                $sms['booking_id'] = "";
                $sms['type'] = "vendor";
                $sms['type_id'] = $invoice[0]['vendor_id'];

                $this->notify->send_sms($sms);
                log_message('info', __FUNCTION__. " SMS Sent.....");
                //Upload Excel files to AWS
                $bucket = BITBUCKET_DIRECTORY;
                $directory_xls = "invoices-excel/" . $invoice[0]['invoice_number'].'.xlsx';

                $this->s3->putObjectFile("/tmp/".$invoice[0]['invoice_number'].'.xlsx', $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                
                //Save this invoice info in table
                $invoice_details = array(
                    'invoice_id' => $invoice[0]['invoice_number'],
                    'type' => 'Stand',
                    'type_code' => 'D',
                    'vendor_partner' => 'vendor',
                    'vendor_partner_id' => $invoice[0]['vendor_id'],
                    'invoice_file_excel' => $invoice[0]['invoice_number'].'.xlsx' ,
                    'invoice_file_pdf' => '',
                    'from_date' => date("Y-m-d", strtotime($from_date)),
                    'to_date' => date('Y-m-d', strtotime('-1 day', strtotime($to_date))),
                    'num_bookings' => $invoice[0]['total_brackets'],
                    'total_service_charge' => 0,
                    'total_additional_service_charge' => 0,
                    'service_tax' => 0,
                    'parts_cost' => ($invoice[0]['total_part_cost'] ),
                    'vat' => ($invoice[0]['part_cost_vat'] ),
                    'total_amount_collected' => $invoice[0]['total'],
                    'rating' => 0,
                    'around_royalty' => $invoice[0]['total'],
                    'amount_collected_paid' => $invoice[0]['total'],
                    'tds_amount' => 0.0,
                    'amount_paid' => 0.0,
                    'settle_amount' => 0,
                    'amount_paid' => 0.0,
                    'mail_sent' => 1,
                    'sms_sent' => 1,
                    //Add 1 month to end date to calculate due date
                    'due_date' => date("Y-m-d", strtotime($to_date . "+1 month"))
                );
                $this->invoices_model->action_partner_invoice($invoice_details);
                log_message('info', __FUNCTION__. " Reset Invoice Id ".$invoice[0]['invoice_number']);
                $this->inventory_model->update_brackets(array('invoice_id'=> NULL), array('invoice_id'=> $invoice[0]['invoice_number']));
                if (strpos($invoice[0]['order_id'], ',') !== FALSE){
                    $var = explode(",", $invoice[0]['order_id']);
                    foreach ($var as $value) {
                        log_message('info', __FUNCTION__. " Update invoice id for bracket invoice id ".$invoice[0]['invoice_number'] . " Order Id ".$value );
                        $this->inventory_model->update_brackets(array('invoice_id'=> $invoice[0]['invoice_number']), array('order_id'=>$value));
                    }
                        
                } else { 
                    log_message('info', __FUNCTION__. " Update invoice id for bracket invoice id ".$invoice[0]['invoice_number'] . " Order Id ".$invoice[0]['order_id'] );
                    $this->inventory_model->update_brackets(array('invoice_id'=> $invoice[0]['invoice_number']), array('order_id'=>$invoice[0]['order_id']));
                    
                }
            }

            //Logging success
            log_message('info', __FUNCTION__ . ' Brackets Report invoice has been generated .' . print_r($invoice, TRUE));
            return $output_file_excel;
        } else {
            //Logging failure
            log_message('info', __FUNCTION__ . ' Error in generating Brackets Report invoice for Vendor ID. ' . $vendor_id);
            return FALSE;
        }
        } else {
             log_message('info', __FUNCTION__ . ' Data Not Found');
            return FALSE;
        }
    }

    /**
     * @Desc: This function is used to create invoices for vendor 
     * @params: Array
     * @return: Mix
     */
    function create_vendor_brackets_invoice($data){
        log_message('info', __FUNCTION__. " Entering......... ");
        $template = 'Bracket_Invoice.xlsx';
        //set absolute path to directory with template files
        $templateDir = __DIR__ . "/../excel-templates/";
        //set config for report
        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );
        //load template
        $R = new PHPReport($config);
        if(file_exists($output_file_excel)){
           
            system(" chmod 777 ".$output_file_excel, $res1);
            unlink($output_file_excel);
        }
        $R->load(array(
                 'id' => 'invoice',
                'data' => $data
            ));
        
        $output_file_dir = "/tmp/";
        $output_file = $data['invoice_number'];
        $output_file_name = $output_file . ".xlsx";
        $output_file_excel = $output_file_dir . $output_file_name;
        $res1 = 0;
        if(file_exists($output_file_excel)){
           
            system(" chmod 777 ".$output_file_excel, $res1);
            unlink($output_file_excel);
        }
        $response = $R->render('excel', $output_file_excel);
        system(" chmod 777 ".$output_file_excel, $res1);
        if($response == NULL){
            log_message('info', __FUNCTION__. " Excel file created ". $output_file_excel);
            return $output_file_excel;
        } else {
             log_message('info', __FUNCTION__. " Excel file not created ");
            return FALSE;
        }
        
        
        
    }
    
     /**
     * @Desc: This function is used to send mail to vendor brackets invoice 
     * @parmas: Vendor id, bracket_invoice file path
     * @return: boolean
     */
    function send_brackets_invoice_mail($vendor_id,$output_file_excel,$get_invoice_month){
        log_message('info', __FUNCTION__. " Entering...." );
        $invoice_month = date('F',strtotime($get_invoice_month));
        
        $vendor_data = $this->vendor_model->getVendorContact($vendor_id);
        
        $to = $vendor_data[0]['primary_contact_email'].','.$vendor_data[0]['owner_email'];
        $from = 'billing@247around.com';
        $cc = 'anuj@247around.com, nits@247around.com';

        $message = "Dear Partner,<br/><br/>";
        $message .= "Please find attached invoice for Brackets delivered in " . $invoice_month . ". ";
        $message .= "Hope to have a long lasting working relationship with you.";
        $message .= "<br><br>With Regards,
                        <br>247around Team<br>
                        <br>247around is part of Businessworld Startup Accelerator & Google Bootcamp 2015
                        <br>Follow us on Facebook: www.facebook.com/247around
                        <br>Website: www.247around.com
                        <br>Playstore - 247around -
                        <br>https://play.google.com/store/apps/details?id=com.handymanapp";

        $send_mail = $this->notify->sendEmail($from, $to, $cc, '', 'Brackets Invoice - '.$vendor_data[0]['name'] , $message, $output_file_excel);
        
        if ($send_mail) {
            log_message('info', __FUNCTION__. "Bracket invoice sent..." );
            return TRUE;
        } else {
             log_message('info', __FUNCTION__. "Bracket invoice not sent..." );
            return FALSE;
        }
    }
    
     /**
     * @Desc: This function is used to send draft mail of vendor brackets invoice 
     * @parmas: Vendor id, bracket_invoicefile path
     * @return: boolean
     */
    function send_brackets_invoice_draft_mail($vendor_id,$output_file_excel,$from_date){
      
        $invoice_month = date('F',strtotime($from_date));
        
        $vendor_details = $this->vendor_model->getVendorContact($vendor_id);

        $to = 'anuj@247around.com';
        $from = 'billing@247around.com';

        $message = "Dear Partner,<br/><br/>";
        $message .= "Please find attached invoice for Brackets delivered in " . $invoice_month . ". ";
        $message .= "Hope to have a long lasting working relationship with you.";
        $message .= "<br><br>With Regards,
                        <br>247around Team<br>
                        <br>247around is part of Businessworld Startup Accelerator & Google Bootcamp 2015
                        <br>Follow us on Facebook: www.facebook.com/247around
                        <br>Website: www.247around.com
                        <br>Playstore - 247around -
                        <br>https://play.google.com/store/apps/details?id=com.handymanapp";

        $send_mail = $this->notify->sendEmail($from, $to, '', '', 'DRAFT - Brackets Invoice - '.$vendor_details[0]['name'], $message, $output_file_excel);
        if ($send_mail) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    
    /**
     * @Desc: This function is used to send mails of Brackets invoices created for vendors
     * @params: vendor ID, invoice month,invoice type,vendor_all_flag
     * @return: void
     */
    function send_brackets_invoice_to_vendors($details,$vendor_all_flag) {
        log_message('info', __FUNCTION__. " Entering........." );
        $vendor_id = $details['vendor_partner_id'];
        $date_range = $details['date_range'];
        $invoice_type = $details['invoice_type'];
        
        $custom_date = explode("-", $date_range);
        $from_date = $custom_date[0];
       // $to_date = $custom_date[1];
        // Call generate_brackets_invoices method to generates Brackets Invoice
        $output_file_excel = $this->generate_brackets_invoices($details);
        //Sending invoice copy to vendors in mail if invocie is being genetared
        if ($output_file_excel) {
            log_message('info', __FUNCTION__. " Excel file return ". $output_file_excel );
            // Not sending mail when vendor_id is all + draft
            if($vendor_all_flag != 1 && $invoice_type == 'draft'){
                //Sending mail to Anuj along with invoice copy as attachment
                $send_mail = $this->send_brackets_invoice_draft_mail($vendor_id, $output_file_excel, $from_date);
                if ($send_mail) {
                    //Loggin Success
                    log_message('info', __FUNCTION__ . ' DRAFT INVOICE - Brackets invoice has been sent for the month of ' . $from_date);
                } else {
                    //Loggin Error
                    log_message('info', __FUNCTION__ . ' DRAFT INVOICE - Error in sending Brackets invoice for the month of ' . $from_date);
                }
            }
            
            //Handling case when invoice type is Final
            
            if($invoice_type == 'final'){
                
                // Sending mail to all vendors POC + OWNER
                $send_mail = $this->send_brackets_invoice_mail($vendor_id, $output_file_excel, $from_date);
                if ($send_mail) {
                    //Loggin Success
                    log_message('info', __FUNCTION__ . ' Brackets invoice has been sent to the following Vendor ID ' . $vendor_id . ' for the month of ' . $from_date);
                } else {
                    //Loggin Error
                    log_message('info', __FUNCTION__ . ' Error in sending Brackets invoice to the following Vendor ID ' . $vendor_id . ' for the month of ' . $from_date);
                }
            }
            exec("rm -rf " . escapeshellarg($output_file_excel));
        } else{
            log_message('info', __FUNCTION__. " Excel file Not exist " );
        }
    }
    /**
     * 
     * @param type $partner_id
     * @param type $from_date
     * @param type $to_date
     * @param type $invoice_type
     * @return string Invoice Id
     */
    function create_partner_invoice($partner_id, $from_date,$to_date, $invoice_type){
      log_message('info', __FUNCTION__ . ' Entering.......');
        $invoices = $this->invoices_model->generate_partner_invoice($partner_id, $from_date,$to_date);
        if(!empty($invoices)){
        
        $template = 'partner_invoice_Main_v2.xlsx';
        // directory
        $templateDir = __DIR__ . "/../excel-templates/";
        
        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );
        $invoices['meta']['sd'] = date("jS M, Y", strtotime($from_date));
        $invoices['meta']['ed']  = date('jS M, Y', strtotime('-1 day', strtotime($to_date)));
        $invoices['meta']['invoice_date'] = date("jS M, Y", strtotime($to_date));

        if ($invoices['booking'][0]['state'] == "DELHI") {

            $invoice_version = "T";
            $invoices['meta']['invoice_type'] = "TAX INVOICE";
        } else {
            $invoice_version = "R";
            $invoices['meta']['invoice_type'] = "RETAIL INVOICE";
        }

        $current_month = date('m');
        // 3 means March Month
        if ($current_month > 3) {
            $financial = date('Y') . "-" . (date('y') + 1);
        } else {
            $financial = (date('Y') - 1) . "-" . date('y');
        }

        //Make sure it is unique
        $invoice_id_tmp = "Around" . "-" . $invoice_version . "-" . $financial . "-" . date("M", strtotime($from_date));
        $where = " `invoice_id` LIKE '%$invoice_id_tmp%'";
        $invoice_no = $this->invoices_model->get_invoices_details($where);

        $invoices['meta']['invoice_id'] = $invoice_id_tmp . "-" . (count($invoice_no) + 1);

         log_message('info', __FUNCTION__ . ' Invoice id '. $invoices['meta']['invoice_id']);
        //load template
        $R = new PHPReport($config);

        $R->load(array(
                    array(
                        'id' => 'meta',
                        'repeat' => false,
                        'data' => $invoices['meta'],
                        'format' => array(
                            'date' => array('datetime' => 'd/M/Y')
                        )
                    ),
                    array(
                        'id' => 'booking',
                        'repeat' => true,
                        'data' => $invoices['booking'],
                    ),

                        )
                );
        
        $output_file_excel = "/tmp/".$invoices['meta']['invoice_id'].".xlsx";
        $res1 = 0;
         if(file_exists($output_file_excel)){
            
            system(" chmod 777 ".$output_file_excel, $res1);
            unlink($output_file_excel);
        }
        $R->render('excel', $output_file_excel);
        log_message('info', __FUNCTION__ . ' File created '.$output_file_excel );
        system(" chmod 777 ".$output_file_excel, $res1);
        $this->email->clear(TRUE);
        $this->email->from('billing@247around.com', '247around Team');
        $to = "anuj@247around.com";
        $subject = "DRAFT INVOICE (SUMMARY) - 247around - " .$invoices['meta']['company_name'];
//		    . " Invoice for period: " . $start_date . " to " . $end_date;

        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->attach($output_file_excel, 'attachment');

        $mail_ret = $this->email->send();

        if ($mail_ret) {
            log_message('info', __METHOD__ . ": Mail sent successfully");
            echo "Mail sent successfully..............." . PHP_EOL;
        } else {
            log_message('info', __METHOD__ . ": Mail could not be sent");
            echo "Mail could not be sent..............." . PHP_EOL;
        }


        if ($invoice_type == "final") {
            log_message('info', __FUNCTION__ . ' Final' );
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "invoices-excel/" . $output_file_excel;
            
            $this->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
          
            log_message('info', __FUNCTION__ . ' File Uploaded to S3' );
            
            // Dump data in a file as a Json
            $file = fopen("/tmp/".$invoices['meta']['invoice_id'] . ".txt", "w") or die("Unable to open file!");
            $res = 0; 
            system(" chmod 777 /tmp/" . $invoices['meta']['invoice_id'] . ".txt", $res);
            $json_data['invoice_data'] = $invoices;

            $contents = " Patner Invoice Json Data:\n";
            fwrite($file, $contents);
            fwrite($file, print_r(json_encode($json_data), TRUE));
            fclose($file);
            log_message('info', __METHOD__ . ": Json File Created");

            $directory_xls = "invoices-json/" . $invoices['meta']['invoice_id'] . ".txt";
            $this->s3->putObjectFile("/tmp/".$invoices['meta']['invoice_id'].".txt", $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            log_message('info', __METHOD__ . ": Json File Uploded to S3");

            //Delete JSON files now
           exec("rm -rf " . escapeshellarg("/tmp/".$invoices['meta']['invoice_id'].".txt"));
                    
        }
        
         exec("rm -rf " . escapeshellarg($output_file_excel));
       
        log_message('info', __FUNCTION__ . ' return with invoice id'.  $invoices['meta']['invoice_id'] );
        return $invoices['meta']['invoice_id'];
        } else {
            log_message('info', __FUNCTION__ . ' Data Not Found' );
            echo "Data Not found";
        }
    }
    /**
     * @desc: This method is used to generate vendor Foc invoice and return invoice id
     * @param Array $details
     */
    function generate_vendor_foc_invoice($details){
        log_message('info',__FUNCTION__. "Entering...");
        $vendor_id = $details['vendor_partner_id'];
        $custom_date = explode("-", $details['date_range']);
        $from_date = $custom_date[0];
        $to_date = $custom_date[1];
        $invoice_type = $details['invoice_type'];
        $invoices = $this->invoices_model->get_vendor_foc_invoice($vendor_id,$from_date,$to_date);
        if(!empty($invoices)){
        
        $template = 'Vendor_Settlement_Template-FoC-v5.xlsx';
        // directory
        $templateDir = __DIR__ . "/../excel-templates/";
        
        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );
        
        $invoices['meta']['sd'] = date("jS M, Y", strtotime($from_date));
        $invoices['meta']['ed']  = date('jS M, Y', strtotime('-1 day', strtotime($to_date)));
        $invoices['meta']['invoice_date'] = date("jS M, Y", strtotime($to_date));
        if (isset($details['invoice_id'])) {
             log_message('info', __METHOD__ . ": Invoice Id re- geneterated ".  $details['invoice_id'] );
            $invoices['meta']['invoice_id'] = $details['invoice_id'];
            if ($invoices['booking'][0]['state'] == "DELHI") {

            $invoice_version = "T";
            $invoices['meta']['invoice_type'] = "TAX INVOICE";
            } else {
                $invoice_version = "R";
                $invoices['meta']['invoice_type'] = "RETAIL INVOICE";
            }
        
        } else {
            if ($invoices['booking'][0]['state'] == "DELHI") {

            $invoice_version = "T";
            $invoices['meta']['invoice_type'] = "TAX INVOICE";
            } else {
                $invoice_version = "R";
                $invoices['meta']['invoice_type'] = "RETAIL INVOICE";
            }

            $current_month = date('m');
            // 3 means March Month
            if ($current_month > 3) {
                $financial = date('Y') . "-" . (date('y') + 1);
            } else {
                $financial = (date('Y') - 1) . "-" . date('y');
            }

            //Make sure it is unique
            $invoice_id_tmp = $invoices['meta']['sc_code'] . "-" . $invoice_version 
                    . "-" . $financial . "-" . date("M", strtotime($from_date));
            $where = " `invoice_id` LIKE '%$invoice_id_tmp%'";
            $invoice_no = $this->invoices_model->get_invoices_details($where);

            $invoices['meta']['invoice_id'] = $invoice_id_tmp . "-" . (count($invoice_no) + 1);
            log_message('info', __METHOD__ . ": Invoice Id geneterated "
                    .  $invoices['meta']['invoice_id'] );
        }
        
        //load template
        $R = new PHPReport($config);

        $R->load(array(
                    array(
                        'id' => 'meta',
                        'repeat' => false,
                        'data' => $invoices['meta'],
                        'format' => array(
                            'date' => array('datetime' => 'd/M/Y')
                        )
                    ),
                    array(
                        'id' => 'booking',
                        'repeat' => true,
                        'data' => $invoices['booking'],
                    ),
                        )
                );
        
        $output_file_excel = "/tmp/".$invoices['meta']['invoice_id'].".xlsx";
        $res1 = 0;
        if(file_exists($output_file_excel)){
           
            system(" chmod 777 ".$output_file_excel, $res1);
            unlink($output_file_excel);
        }
        $R->render('excel', $output_file_excel);
        log_message('info', __METHOD__ . ": Excel FIle generated ".$output_file_excel );
        $res2 = 0;
        system(" chmod 777 ".$output_file_excel, $res2);
       
           
        if ($invoice_type == "final") {
            log_message('info', __METHOD__ . ": Invoice type Final");
           
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "invoices-excel/" . $invoices['meta']['invoice_id'].".xlsx";
          
            $foc_upload = $this->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            if($foc_upload){
                log_message('info', __METHOD__ . ": Main FOC Invoice File uploaded to s3");
                echo "Main FOC Invoice File uploaded to s3";
                
            } else{
                log_message('info', __METHOD__ . ": Main FOC Invoice File uploaded to s3 ".$invoices['meta']['invoice_id'].".xlsx");
                echo "Main FOC Invoice File uploaded to s3 ".$invoices['meta']['invoice_id'].".xlsx";
            }
          
            // Dump data in a file as a Json
            $file = fopen("/tmp/".$invoices['meta']['invoice_id'] . ".txt", "w") or die("Unable to open file!");
            $res = 0;
            system(" chmod 777 /tmp/" . $invoices['meta']['invoice_id'] . ".txt", $res);
            $json_data['invoice_data'] = $invoices;

            $contents = " Vendor FOC Invoice Json Data:\n";
            fwrite($file, $contents);
            fwrite($file, print_r(json_encode($json_data), TRUE));
            fclose($file);
            log_message('info', __METHOD__ . ": Json File Created");

            $directory_xls = "invoices-json/" . $invoices['meta']['invoice_id'] . ".txt";
            $json = $this->s3->putObjectFile("/tmp/".$invoices['meta']['invoice_id'].".txt", $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            if($json){
                
                 log_message('info', __METHOD__ . ": Main FOC Invoice File uploaded to s3");
                echo "Main FOC Invoice File uploaded to s3";
                
            } else {
                
                log_message('info', __METHOD__ . ": Main FOC Invoice File uploaded to s3 ".$invoices['meta']['invoice_id'].".txt");
                echo "Main FOC Invoice File uploaded to s3 ".$invoices['meta']['invoice_id'].".txt";
            }
            log_message('info', __METHOD__ . ": Json File Uploded to S3");

            //Delete JSON files now
          exec("rm -rf " . escapeshellarg("/tmp/".$invoices['meta']['invoice_id'].".txt"));
                    
        }
       
        log_message('info',__FUNCTION__. " Exit Invoice Id: ". $invoices['meta']['invoice_id']);
        return $invoices['meta']['invoice_id'];
        
        } else {
            echo "Data Not Found";
            log_message('info',__FUNCTION__. " Data Not Found.");
            return false;
        }
    }
    
    /**
     * @desc: This method is used to generate Main Cash Invoice
     * @param type $details
     * @return string
     */
    function generate_vendor_cash_invoice($details) {
        log_message('info', __FUNCTION__ . " Entering...." . print_r($details, true));

        $vendor_id = $details['vendor_partner_id'];
        $custom_date = explode("-", $details['date_range']);
        $from_date = $custom_date[0];
        $to_date = $custom_date[1];
        $invoice_type = $details['invoice_type'];

        $invoices = $this->invoices_model->get_vendor_cash_invoice($vendor_id, $from_date, $to_date);
        
        if (!empty($invoices)) {
            log_message('info', __FUNCTION__ . "=> Data Found");

            $template = 'Vendor_Settlement_Template-CashMain-v4.xlsx';
            // directory
            $templateDir = __DIR__ . "/../excel-templates/";

            $config = array(
                'template' => $template,
                'templateDir' => $templateDir
            );

            $invoices['meta']['sd'] = date("jS M, Y", strtotime($from_date));
            $invoices['meta']['ed'] = date('jS M, Y', strtotime('-1 day', strtotime($to_date)));
            $invoices['meta']['invoice_date'] = date("jS M, Y", strtotime($to_date));

            if (isset($details['invoice_id'])) {
                log_message('info', __FUNCTION__ . " Re-Generate Invoice ID: " . $details['invoice_id']);
                $invoices['meta']['invoice_id'] = $details['invoice_id'];
                if ($invoices['product'][0]['state'] == "DELHI") {

                    $invoice_version = "T";
                    $invoices['meta']['invoice_type'] = "TAX INVOICE";
                } else {
                    $invoice_version = "R";
                    $invoices['meta']['invoice_type'] = "RETAIL INVOICE";
                }
            } else {
                if ($invoices['product'][0]['state'] == "DELHI") {

                    $invoice_version = "T";
                    $invoices['meta']['invoice_type'] = "TAX INVOICE";
                } else {
                    $invoice_version = "R";
                    $invoices['meta']['invoice_type'] = "RETAIL INVOICE";
                }

                $current_month = date('m');
                // 3 means March Month
                if ($current_month > 3) {
                    $financial = date('Y') . "-" . (date('y') + 1);
                } else {
                    $financial = (date('Y') - 1) . "-" . date('y');
                }

                //Make sure it is unique
                $invoice_id_tmp = "Around-" . $invoice_version . "-" . $financial . "-" . date("M", strtotime($from_date));
                $where = " `invoice_id` LIKE '%$invoice_id_tmp%'";
                $invoice_no = $this->invoices_model->get_invoices_details($where);

                $invoices['meta']['invoice_id'] = $invoice_id_tmp . "-" . (count($invoice_no) + 1);
                log_message('info', __FUNCTION__ . " Generate Invoice ID: " . $invoices['meta']['invoice_id']);
            }

            //load template
            $R = new PHPReport($config);
            $R->load(array(
                array(
                    'id' => 'meta',
                    'repeat' => false,
                    'data' => $invoices['meta'],
                    'format' => array(
                        'date' => array('datetime' => 'd/M/Y')
                    )
                ),
                array(
                    'id' => 'invoice',
                    'repeat' => true,
                    'data' => $invoices['product'],
                ),
                    )
            );


            $output_file_excel = "/247around_tmp/" . $invoices['meta']['invoice_id'] . ".xlsx";
            $res1 = 0;
            if (file_exists($output_file_excel)) {

                system(" chmod 777 " . $output_file_excel, $res1);
               
                unlink($output_file_excel);
            }

            $R->render('excel', $output_file_excel);
            log_message('info', __FUNCTION__ . " Excel Created " . $output_file_excel);
            $res2 = 0;
            system(" chmod 777 " . $output_file_excel, $res2);

           
            if ($invoice_type == "final") {
                log_message('info', __FUNCTION__ . " Generate Final Invoice ");

                $bucket = 'bookings-collateral-test';
                $directory_xls = "invoices-excel/" . $invoices['meta']['invoice_id'] . ".xlsx";

                $invoice_uploaded = $this->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                if($invoice_uploaded){
                    echo 'Main Invoice Uploaded'.PHP_EOL;
                    log_message('info', __FUNCTION__ . " Main Invoice is uploaded to S3".$invoices['meta']['invoice_id'] . ".xlsx");
                    
                } else {
                     echo 'Main Invoice not Uploaded'.PHP_EOL;
                    log_message('info', __FUNCTION__ . " Main Invoice is not uploaded to S3".$invoices['meta']['invoice_id'] . ".xlsx"); 
                }
 
                // Dump data in a file as a Json
                $file = fopen("/247around_tmp/" . $invoices['meta']['invoice_id'] . ".txt", "w") or die("Unable to open file!");
                $res = 0;
                system(" chmod 777 /247around_tmp/" . $invoices['meta']['invoice_id'] . ".txt", $res);
                log_message('info', __FUNCTION__ . " Chmod result: " . print_r($res, TRUE));

                $json_data['invoice_data'] = $invoices;

                $contents = " Vendor FOC Invoice Json Data:\n";
                fwrite($file, $contents);
                fwrite($file, print_r(json_encode($json_data), TRUE));
                fclose($file);
                
                log_message('info', __METHOD__ . ": Json File Created");

                $directory_xls = "invoices-json/" . $invoices['meta']['invoice_id'] . ".txt";
                $json_upload = $this->s3->putObjectFile("/247around_tmp/" . $invoices['meta']['invoice_id'] . ".txt", $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                if($json_upload){
                    echo 'Main Invoice JOSN File Uploaded'.PHP_EOL;; 
                    log_message('info', __FUNCTION__ . " Main Invoice JOSN FIle Uploaded to S3".$invoices['meta']['invoice_id'] . ".txt");
                    
                } else {
                     echo 'Main Invoice JOSN File not Uploaded'.PHP_EOL;
                    log_message('info', __FUNCTION__ . " Main Invoice is not uploaded to S3".$invoices['meta']['invoice_id'] . ".txt"); 
                }
                log_message('info', __METHOD__ . ": Json File Uploded to S3");

                //Delete JSON files now
                //Do not delete XLSX now, it is being used later for email
                exec("rm -rf " . escapeshellarg("/247around_tmp/" . $invoices['meta']['invoice_id'] . ".txt"));
            }

            log_message('info', __FUNCTION__ . " Exit Invoice Id: " . $invoices['meta']['invoice_id']);
            
            return $invoices['meta']['invoice_id'];
        } else {
            log_message('info', __FUNCTION__ . " Exit Dta Not Found " . print_r($details));
            
            echo "Data Does Not Exist" . PHP_EOL;
            return FALSE;
        }
    }

}
