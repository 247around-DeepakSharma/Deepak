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
        $this->load->model("upcountry_model");
        $this->load->model('penalty_model');
        $this->load->model("accounting_model");
        $this->load->model("bb_model");
        $this->load->model("cp_model");
        $this->load->library("notify");
        $this->load->library("miscelleneous");
        $this->load->library('PHPReport');
        $this->load->library('form_validation');
        $this->load->library("session");
        $this->load->library('s3');
        $this->load->library('table');
        $this->load->library('push_notification_lib');
        $this->load->library("invoice_lib");

    }

    /**
     * Load invoicing form
     */
    public function index() {
        $this->checkUserSession();
        $invoicingSummary = $this->invoices_model->getsummary_of_invoice("vendor",array('active' => 1, 'is_sf' => 1), true);
        $select = "service_centres.name, service_centres.id";
        if($this->session->userdata('user_group') == 'regionalmanager'){
          $rmSpecificData = $this->get_rm_specific_service_centers_invoice_data($this->session->userdata('id'),$invoicingSummary);
          $whereIN = array("id"=>$rmSpecificData['serviceCenters']);
          $data['invoicing_summary']= $rmSpecificData["invoiceSummaryData"];
        }
        else{
            $whereIN = NULL;
            $data['invoicing_summary'] = $invoicingSummary;
        }
        $data['service_center'] = $this->reusable_model->get_search_result_data("service_centres",$select,NULL,NULL,NULL,NULL,$whereIN,NULL,array());
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/invoice_list', $data);
    }
    
    function get_rm_specific_service_centers_invoice_data($rmID,$invoicingSummary){
          $this->checkUserSession();
          $sf_list = $this->vendor_model->get_employee_relation($rmID);
          $tempArray= array();
          $serviceCenters = $sf_list[0]['service_centres_id'];
          $serviceCentersArray = explode(",",$serviceCenters);
          foreach($invoicingSummary as $invoiceSummaryData){
              if (in_array($invoiceSummaryData['id'], $serviceCentersArray)){
                  $tempArray[] = $invoiceSummaryData;
              }
          }
          return array("serviceCenters"=>$serviceCentersArray,"invoiceSummaryData"=>$tempArray);
    }
    public function invoice_listing_ajax($vendor_type = ""){
        $this->checkUserSession();
        $vendor_partner = $this->input->post('vendor_partner');
        $sf_cp = json_decode($this->input->post('sf_cp'), true);
        if($vendor_type != ""){
            $sf_cp['active'] = $vendor_type;
        }
          $invoicingSummary= $this->invoices_model->getsummary_of_invoice($vendor_partner,$sf_cp);
          if($this->session->userdata('user_group') == 'regionalmanager'){
          $rmSpecificData = $this->get_rm_specific_service_centers_invoice_data($this->session->userdata('id'),$invoicingSummary);
          $data['invoicing_summary']= $rmSpecificData["invoiceSummaryData"];
        }
        else{
            $data['invoicing_summary'] = $invoicingSummary;
        }
        if($vendor_partner === 'vendor'){ 
            $data['service_center'] = "service_center";
        }else{
            $data['partner'] = "partner";
        }
        $data['is_ajax'] = TRUE;
        echo $this->load->view('employee/invoice_list', $data);
    }

    /**
     * @desc: This is used to get vendor, partner invoicing data by service center id or partner id
     *          and load data in a table.
     *
     * @param: void
     * @return: void
     */
    function getInvoicingData() {
        $this->checkUserSession();
        $invoice_period = $this->input->post('invoice_period');
        $data = array('vendor_partner' => $this->input->post('source'),
                      'vendor_partner_id' => $this->input->post('vendor_partner_id'));
        if($invoice_period === 'all'){
            $where = array('vendor_partner' => $this->input->post('source'),
                      'vendor_partner_id' => $this->input->post('vendor_partner_id'));
        }else if($invoice_period === 'cur_fin_year'){
            $where = "vendor_partner = '".$this->input->post('source')."' AND vendor_partner_id = '".$this->input->post('vendor_partner_id')."' AND case WHEN month(CURDATE()) IN ('1','2','3') THEN from_date >= CONCAT(YEAR(CURDATE())-1,'-04-01') and from_date <= CONCAT(YEAR(CURDATE()),'-03-31') WHEN month(from_date) NOT IN ('1','2','3') THEN from_date >= CONCAT(YEAR(CURDATE()),'-04-01') and from_date <= CONCAT(YEAR(CURDATE())+1,'-03-31') END";
        }
        
        $invoice['invoice_array'] = $this->invoices_model->getInvoicingData($where);
        $invoice['invoicing_summary'] = $this->invoices_model->getsummary_of_invoice($data['vendor_partner'],array('id' => $data['vendor_partner_id']))[0];
            
        //TODO: Fix the reversed names here & everywhere else as well
        $data2['partner_vendor'] = $this->input->post('source');
        $data2['partner_vendor_id'] = $this->input->post('vendor_partner_id');
        $invoice['bank_statement'] = $this->invoices_model->get_bank_transactions_details('*',$data2);
        if ($data['vendor_partner'] == "vendor") {
    
            $invoice['unbilled_amount'] = $this->invoices_model->get_unbilled_amount($data['vendor_partner_id']);
        }

        echo $this->load->view('employee/invoicing_table', $invoice);
    }

    /**
     * @desc Send invoice to partner
     * @param String $invoiceId
     */
    function sendInvoiceMail($invoiceId) {
         $this->checkUserSession();
        log_message('info', "Entering: " . __METHOD__ . 'Invoice_id:' . $invoiceId );
        $data = $this->invoices_model->get_invoices_details(array("invoice_id" => $invoiceId));
        if (!empty($data)) {
            $vendor_partnerId = $data[0]['vendor_partner_id'];
            $start_date = $data[0]['from_date'];
            $end_date = $data[0]['to_date'];
            $vendor_partner = $data[0]['vendor_partner'];
            $email = "";
            $detailed_invoice = "";
            if(!empty($data[0]['invoice_file_main'])){
                $detailed_invoice = S3_WEBSITE_URL."invoices-excel/".$data[0]['invoice_detailed_excel'];
            }
            $main_invoice = S3_WEBSITE_URL."invoices-excel/".$data[0]['invoice_file_main'];
            $email_template = $this->booking_model->get_booking_email_template("resend_invoice");
            // download invoice pdf file to local machine
            if ($vendor_partner == "vendor") {

                $to = $this->get_to_emailId_for_vendor($vendor_partnerId, $email);
                $rm_details = $this->vendor_model->get_rm_sf_relation_by_sf_id($vendor_partnerId);
                $rem_email_id = "";
                if (!empty($rm_details)) {
                    $rem_email_id = ", " . $rm_details[0]['official_email'];
                }
                $cc = $email_template[3] . $rem_email_id. ", ". $this->session->userdata("official_email");
            } else {
                $getEmail = $this->partner_model->getpartner_details("invoice_email_to", array('partners.id' =>$vendor_partnerId));
                $to =  $to = $getEmail[0]['invoice_email_to'];
                $cc = $email_template[3]. ", ". $this->session->userdata("official_email");
            }


            $subject = vsprintf($email_template[4], array(date("jS M, Y", strtotime($start_date)), date("jS M, Y", strtotime($end_date))));
            $message = vsprintf($email_template[0], array(date("jS M, Y", strtotime($start_date)), date("jS M, Y", strtotime($end_date))));
            $email_from = $email_template[2];
            $sent = $this->send_email_with_invoice($email_from, $to, $cc, $message, $subject, $detailed_invoice, $main_invoice,'resend_invoice');
            if ($sent) {
                $userSession = array('success' => "Invoice Sent");
                $this->session->set_userdata($userSession);
            } else {
                $userSession = array('error' => "Invoice sending failed");
                $this->session->set_userdata($userSession);
            }


            log_message('info', "To- EmailId" . print_r($to, true));
            redirect(base_url() . 'employee/invoice/invoice_summary/' . $vendor_partner . "/" . $vendor_partnerId);
        }
    }

    /**
     * @desc: Load view to select patner to display invoices
     * @param: void
     * @return: void
     */
    function invoice_partner_view() {
        $this->checkUserSession();
        $data['partner'] = $this->partner_model->getpartner("", false);
        $invoicing_summary = $this->invoices_model->getsummary_of_invoice("partner", array('active' => '1'));
        foreach ($invoicing_summary as $key => $value) {
            $invoicing_summary[$key]['prepaid_data'] = $this->miscelleneous->get_partner_prepaid_amount($value["id"], FALSE);
        }
        $data['invoicing_summary'] = $invoicing_summary;
       
        $this->miscelleneous->load_nav_header();
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
     *  @desc : This function adds new transactions between vendor/partner and 247around.
     *  @param : Type $partnerId
     *  @return : void
     */
    function get_add_new_transaction($vendor_partner = "", $id = "") {
        $this->checkUserSession();
        $data['vendor_partner'] = $vendor_partner;
        $data['id'] = $id;
        $data['invoice_id_array'] = $this->input->post('invoice_id');
        $data['selected_amount_collected'] = $this->input->post('selected_amount_collected');
        $data['selected_tds'] = $this->input->post('selected_tds');
        $data['tds_amount'] = $this->input->post('tds_amount');
        $data['amount_collected'] = $this->input->post('amount_collected');
        $data['invoice_id_list'] = array();
        if (empty($vendor_partner)) {
            $where = array('amount_paid' => '0');
            $data['invoice_id_list'] = $this->invoices_model->get_invoices_details($where);
        }
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/addnewtransaction', $data);
    }

    /**
     * @desc: This is used to load upadate form of bank transaction details
     * @param String $id (Bank transaction id)
     */
    function update_banktransaction($id) {
        $this->checkUserSession();
        $details = $this->invoices_model->get_bank_transactions_details('*',array('id' => $id));
        if (!empty($details)) {
            $data['vendor_partner'] = $details[0]['partner_vendor'];
            $data['id'] = $details[0]['partner_vendor_id'];
            $data['bank_txn_details'] = $details;
            $amount = 0;
            if ($details[0]['credit_amount'] > 0) {
                $amount = $details[0]['credit_amount'];
            } else if ($details[0]['debit_amount'] > 0) {
                $amount = -$details[0]['debit_amount'];
            }
            $data['invoice_id_array'] = explode(",", $details[0]['invoice_id']);
            $data['selected_amount_collected'] = $amount;
            $data['selected_tds'] = $details[0]['tds_amount'];
            $amount_collected = array();
            $tds = array();


            $bank_payment_history = $this->invoices_model->get_payment_history('credit_debit_amount,credit_debit, invoice_id, tds_amount',array('bank_transaction_id' => $id));
            foreach ($bank_payment_history as $value) {
                if ($value['credit_debit'] == "Debit") {
                    $amount = -$value['credit_debit_amount'];
                } else {
                    $amount = $value['credit_debit_amount'];
                }
                $amount_collected[$value['invoice_id']] = $amount;
                $tds[$value['invoice_id']] = $value['tds_amount'];
            }

            $data['tds_amount'] = $tds;
            $data['amount_collected'] = $amount_collected;
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/addnewtransaction', $data);
        }
    }

    /**
     *  @desc : This is used to insert and update bank transaction table. It gets bank transaction id while update other wise empty
     *  @param : void
     *  @return : void
     */
    function post_add_new_transaction() {
        $this->checkUserSession();
        $account_statement['partner_vendor'] = $this->input->post('partner_vendor');
        $account_statement['partner_vendor_id'] = $this->input->post('partner_vendor_id');
        $account_statement['bankname'] = $this->input->post('bankname');
        $account_statement['transaction_mode'] = $this->input->post('transaction_mode');
        $invoice_id_array = $this->input->post('invoice_id');
        $credit_debit_array = $this->input->post('credit_debit');
        
        $tds_amount_array = $this->input->post('tds_amount');
        $credit_debit_amount = $this->input->post('credit_debit_amount');
        $transaction_date = $this->input->post('tdate');
        $account_statement['transaction_date'] = date("Y-m-d", strtotime($transaction_date));
        $account_statement['description'] = $this->input->post('description');
        $account_statement['transaction_id'] = $this->input->post('transaction_id');
        //Get bank txn id while update other wise empty.
        $bank_txn_id = $this->input->post("bank_txn_id");
        $account_statement['invoice_id'] = implode(",", $invoice_id_array);
        $paid_amount = 0;
        $tds = 0;
        $payment_history = array();
        foreach ($invoice_id_array as $key => $invoice_id) {
            if (!empty($invoice_id)) {
                $p_history = array();
                $vp_details = array();
                $where = array('invoice_id' => $invoice_id);
                $data = $this->invoices_model->get_invoices_details($where);
                $credit_debit = $credit_debit_array[$key];
                $p_history['invoice_id'] = $invoice_id;
                $p_history['credit_debit'] = $credit_debit;
                $p_history['credit_debit_amount'] = round($credit_debit_amount[$key], 0);
                $p_history['agent_id'] = $this->session->userdata('id');
                $p_history['tds_amount'] = $tds_amount_array[$key];
                $p_history['create_date'] = date("Y-m-d H:i:s");
                array_push($payment_history, $p_history);

                if ($credit_debit == 'Credit') {

                    $paid_amount += round($credit_debit_amount[$key], 0);
                    $amount_collected = abs(round(($data[0]['amount_collected_paid'] - $data[0]['amount_paid']), 0));
                    
                } else if ($credit_debit == 'Debit') {

                    $paid_amount += (-round($credit_debit_amount[$key], 0));
                    $amount_collected = abs(round(($data[0]['amount_collected_paid'] + $data[0]['amount_paid']), 0));
                }
              
                $tds += $tds_amount_array[$key];

                if ($amount_collected == round($credit_debit_amount[$key], 0)) {

                    $vp_details['settle_amount'] = 1;
                    $vp_details['amount_paid'] = $credit_debit_amount[$key] + $data[0]['amount_paid'];
                } else {
                    //partner Pay to 247Around
                    if ($account_statement['partner_vendor'] == "partner" && $credit_debit == 'Credit' && $data[0]['tds_amount'] == 0) {
                        $per_tds = 0;
                        $vp_details['tds_amount'] = $tds_amount_array[$key];
                        $vp_details['tds_rate'] = $per_tds;
                        $amount_collected = $data[0]['total_amount_collected'] - $vp_details['tds_amount'];
                        $vp_details['around_royalty'] = $vp_details['amount_collected_paid'] = $amount_collected;

                        if (round($amount_collected, 0) == round(($data[0]['amount_paid'] + $credit_debit_amount[$key]), 0)) {
                            $vp_details['settle_amount'] = 1;
                        } else {
                            $vp_details['settle_amount'] = 0;
                        }
                        $vp_details['amount_paid'] = $data[0]['amount_paid'] + $credit_debit_amount[$key];
                    } else {

                        $vp_details['settle_amount'] = 0;
                        $vp_details['amount_paid'] = $data[0]['amount_paid'] + $credit_debit_amount[$key];
                    }
                }

                $this->invoices_model->update_partner_invoices(array('invoice_id' => $invoice_id), $vp_details);
            }
        }

        if ($paid_amount > 0) {
            $account_statement['debit_amount'] = '0';
            $account_statement['credit_amount'] = abs($paid_amount);
            $account_statement['credit_debit'] = 'Credit';
        } else {
            $account_statement['debit_amount'] = abs($paid_amount);
            $account_statement['credit_amount'] = '0';
            $account_statement['credit_debit'] = 'Debit';
        }

        $account_statement['agent_id'] =  $this->session->userdata('id');   
        $account_statement['tds_amount'] = $tds;            
                    
        if (empty($bank_txn_id)) {
            $bank_txn_id = $this->invoices_model->bankAccountTransaction($account_statement);
        } else {
            $this->invoices_model->update_bank_transactions(array('id' => $bank_txn_id), $account_statement);
        }
        //Donot remove $value
        foreach ($payment_history as $key => $value) {
            $payment_history[$key]['bank_transaction_id'] = $bank_txn_id;
        }
        $this->accounting_model->insert_batch_payment_history($payment_history);

        //Send SMS to vendors about payment
        if ($account_statement['partner_vendor'] == 'vendor') {

             $this->send_payment_sms_to_vendor($account_statement);
        }

          redirect(base_url() . 'employee/invoice/invoice_summary/' . $account_statement['partner_vendor'] . "/" . $account_statement['partner_vendor_id']);
        
    }

    function send_payment_sms_to_vendor($account_statement) {
         $this->checkUserSession();
        $vendor_arr = $this->vendor_model->getVendorContact($account_statement['partner_vendor_id']);
        $v = $vendor_arr[0];

        $sms['tag'] = "payment_made_to_vendor";
        $sms['phone_no'] = $v['owner_phone_1'];
        $sms['smsData'] = "previous month";
        $sms['booking_id'] = "";
        $sms['type'] = $account_statement['partner_vendor'];
        $sms['type_id'] = $account_statement['partner_vendor_id'];


        $this->notify->send_sms_msg91($sms);
    }

    /**
     *  @desc : AJAX CALL. This function is to get the partner or vendor details.
     *  @param : $par_ven - Vendor or partner name(specification)
     *  @return : void
     */
    function getPartnerOrVendor($par_ven) {
         $this->checkUserSession();
        $vendor_partner_id = $this->input->post('vendor_partner_id');
        $flag = $this->input->post('invoice_flag');
        echo "<option value='' selected disabled>Select Entity</option>";
        if ($flag == 1) {
            echo "<option value='All'>All</option>";
        }
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
           
            $select = "service_centres.name, service_centres.id";
            $all_vendors = $this->vendor_model->getVendorDetails($select);
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
         $this->checkUserSession();
        log_message('info', __METHOD__ . 'for transaction_id: '.$transaction_id.' vendor_partner: '.$vendor_partner. ' vendor_partner_id: '.$vendor_partner_id);
        
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
         $this->checkUserSession();
        //Reset type to vendor if some other value is there
        $possible_type = array('vendor', 'partner', 'all');
        if (!in_array($type, $possible_type)) {
            $type = 'vendor';
        }

        $invoice['bank_statement'] = $this->invoices_model->get_all_bank_transactions($type);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/view_transactions', $invoice);
    }

    /**
     * @desc: generate details partner Detailed invoices
     */
    function create_partner_invoices_detailed($partner_id, $f_date, $t_date, $invoice_type, $misc_data, $agent_id, $hsn_code) {
        
        log_message('info', __METHOD__ . "=> " . $invoice_type . " Partner Id " . $partner_id . ' invoice_type: ' . $invoice_type . ' agent_id: ' . $agent_id);
        $data = $this->invoices_model->getpartner_invoices($partner_id, $f_date, $t_date);
        $files = array();
        $template = 'Partner_invoice_detail_template-v3.xlsx';

        $courier = $misc_data['courier'];
        $meta = $misc_data['meta'];
        $upcountry = $misc_data['upcountry'];
        unset($misc_data);
        $meta['total_courier_charge'] = (array_sum(array_column($courier, 'courier_charges_by_sf')));
        $meta['total_upcountry_price'] = 0;
        $total_upcountry_distance = $total_upcountry_booking = 0;
        $num_booking = count(array_unique(array_map(function ($k) {
                            return $k['booking_id'];
                        }, $data)));

        $output_file_excel = TMP_FOLDER . $meta['invoice_id'] . "-detailed.xlsx";

        $this->invoice_lib->generate_invoice_excel($template, $meta, $data, $output_file_excel);

        // Generate Upcountry Excel
        if (!empty($upcountry)) {
            $meta['total_upcountry_price'] = $upcountry[0]['total_upcountry_price'];
            $total_upcountry_booking = $upcountry[0]['total_booking'];
            $total_upcountry_distance = $upcountry[0]['total_distance'];
            $u_files_name = $this->generate_partner_upcountry_excel($partner_id, $upcountry, $meta);
            array_push($files, $u_files_name);

            log_message('info', __METHOD__ . "=> File created " . $u_files_name);
        }

        if (!empty($courier)) {
            $c_files_name = $this->generate_partner_courier_excel($courier, $meta);
            array_push($files, $c_files_name);
            log_message('info', __METHOD__ . "=> File created " . $c_files_name);
        }

        $this->combined_partner_invoice_sheet($output_file_excel, $files);
        array_push($files, $output_file_excel);

        $convert = $this->invoice_lib->send_request_to_convert_excel_to_pdf($meta['invoice_id'], $invoice_type);

        $output_pdf_file_name = $convert['main_pdf_file_name'];

        array_push($files, TMP_FOLDER . $convert['excel_file']);

        if ($invoice_type == "final") {
            log_message('info', __METHOD__ . "=> Final");

            //get email template from database
            $email_template = $this->booking_model->get_booking_email_template(PARTNER_INVOICE_DETAILED_EMAIL_TAG);
            $subject = vsprintf($email_template[4], array($meta['company_name'], $f_date, $t_date));
            $message = $email_template[0];
            $email_from = $email_template[2];

            $to = $data[0]['invoice_email_to'];
            $cc = $data[0]['invoice_email_cc'];
            $this->upload_invoice_to_S3($meta['invoice_id']);
            $pdf_attachement_url = 'https://s3.amazonaws.com/' . BITBUCKET_DIRECTORY . '/invoices-excel/' . $output_pdf_file_name;

            $this->send_email_with_invoice($email_from, $to, $cc, $message, $subject, $output_file_excel, $pdf_attachement_url,PARTNER_INVOICE_DETAILED_EMAIL_TAG);
           

            $invoice_details = array(
                'invoice_id' => $meta['invoice_id'],
                'type_code' => 'A',
                'type' => 'Cash',
                'vendor_partner' => 'partner',
                'vendor_partner_id' => $data[0]['partner_id'],
                'invoice_file_main' => $output_pdf_file_name,
                'invoice_file_excel' => $meta['invoice_id'] . ".xlsx",
                'invoice_detailed_excel' => $meta['invoice_id'] . '-detailed.xlsx',
                'from_date' => date("Y-m-d", strtotime($f_date)), //??? Check this next time, format should be YYYY-MM-DD
                'to_date' => date("Y-m-d", strtotime($t_date)),
                'num_bookings' => $num_booking,
                'total_service_charge' => ($meta['total_ins_charge']),
                'total_additional_service_charge' => 0.00,
                'service_tax' => 0.00,
                'parts_cost' => $meta['total_parts_charge'],
                'vat' => 0.00,
                'total_amount_collected' => $meta['sub_total_amount'],
                'tds_amount' => 0,
                'tds_rate' => 0,
                'upcountry_booking' => $total_upcountry_booking,
                'upcountry_distance' => $total_upcountry_distance,
                'courier_charges' =>  $meta['total_courier_charge'],
                'upcountry_price' => $meta['total_upcountry_price'],
                'rating' => 5,
                'invoice_date' => date('Y-m-d'),
                'around_royalty' => $meta['sub_total_amount'],
                'due_date' => date("Y-m-d", strtotime($t_date . "+1 month")),
                //Amount needs to be collected from Vendor
                'amount_collected_paid' => $meta['sub_total_amount'],
                //add agent_id
                'agent_id' => $agent_id,
                "cgst_tax_rate" => $meta['cgst_tax_rate'],
                "sgst_tax_rate" => $meta['sgst_tax_rate'],
                "igst_tax_rate" => $meta['igst_tax_rate'],
                "igst_tax_amount" => $meta["igst_total_tax_amount"],
                "sgst_tax_amount" => $meta["sgst_total_tax_amount"],
                "cgst_tax_amount" => $meta["cgst_total_tax_amount"],
                "parts_count" =>$meta['parts_count'],
                "invoice_file_pdf" => $convert['copy_file'], 
                "hsn_code" => $hsn_code
            );

            $this->invoices_model->insert_new_invoice($invoice_details);
            log_message('info', __METHOD__ . "=> Insert Invoices in partner invoice table");

            foreach ($data as $value1) {

                log_message('info', __METHOD__ . "=> Invoice update in booking unit details unit id" . $value1['unit_id'] . " Invoice Id" . $meta['invoice_id']);
                $this->booking_model->update_booking_unit_details_by_any(array('id' => $value1['unit_id']), array('partner_invoice_id' => $meta['invoice_id']));
            }

            if (!empty($upcountry)) {
                foreach ($upcountry as $up_booking_details) {
                    $up_b = explode(",", $up_booking_details['booking_id']);
                    for($i=0; $i < count($up_b); $i++){
                        
                        $this->booking_model->update_booking(trim($up_b[$i]), array('upcountry_partner_invoice_id' => $meta['invoice_id']));
                    }

                }
            }
            exec("rm -rf " . escapeshellarg(TMP_FOLDER . "copy_" . $meta['invoice_id'] . ".xlsx"));
        } else {

            $this->download_invoice_files($meta['invoice_id'], $output_file_excel, $output_pdf_file_name);
        }

        //Delete XLS files now
        foreach ($files as $file_name) {
            exec("rm -rf " . escapeshellarg($file_name));
        }

        return true;
    }

    function download_invoice_files($invoice_id, $output_file_excel, $output_pdf_file_name) {
       
        if (file_exists($output_file_excel)) {
            if (explode('.', $output_pdf_file_name)[1] === 'pdf') {
                $output_file_pdf = TMP_FOLDER . $invoice_id . '-draft.pdf';

                $cmd = "curl https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/invoices-excel/" . $output_pdf_file_name . " -o " . $output_file_pdf;
                exec($cmd);

                system('zip ' . TMP_FOLDER . $invoice_id . '.zip ' . TMP_FOLDER . $invoice_id . '-draft.xlsx' . ' ' . TMP_FOLDER . $invoice_id . '-draft.pdf'
                        . ' ' . $output_file_excel);
            } else {
                system('zip ' . TMP_FOLDER . $invoice_id . '.zip ' . TMP_FOLDER . $invoice_id . '-draft.xlsx' . ' ' . $output_file_excel);
            }

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header("Content-Disposition: attachment; filename=\"$invoice_id.zip\"");
            readfile(TMP_FOLDER . $invoice_id. '.zip');
            $res1 = 0;
            system(" chmod 777 " . TMP_FOLDER . $invoice_id . '.zip ', $res1);
            exec("rm -rf " . escapeshellarg(TMP_FOLDER . $invoice_id . '.zip'));
            exec("rm -rf " . escapeshellarg(TMP_FOLDER . "copy_" . $invoice_id . "-draft.xlsx"));
            exec("rm -rf " . escapeshellarg(TMP_FOLDER . $invoice_id . '-draft.pdf'));
            exec("rm -rf " . escapeshellarg(TMP_FOLDER . $invoice_id . '-draft.xlsx'));
        }                                              
    }

    function upload_invoice_to_S3($invoice_id, $detailed = true){

        $this->invoice_lib->upload_invoice_to_S3($invoice_id, $detailed);

    }
    
    function generate_partner_upcountry_excel($partner_id, $data, $meta) {
        if($partner_id == PAYTM){
            $template = 'Paytm_invoice_detail_template-v1-upcountry.xlsx';
        } else {
            $template = 'Partner_invoice_detail_template-v2-upcountry.xlsx';
        }
        
        $output_file_excel = TMP_FOLDER . $meta['invoice_id'] . "-upcountry-detailed.xlsx";
        $this->invoice_lib->generate_invoice_excel($template, $meta, $data, $output_file_excel);
        return $output_file_excel;

    }
    
    function generate_partner_courier_excel($data, $meta){
        
        $template = 'Partner_invoice_detail_template-v2-courier.xlsx';
        $output_file_excel = TMP_FOLDER . $meta['invoice_id'] . "-courier-detailed.xlsx";
        $this->invoice_lib->generate_invoice_excel($template, $meta, $data, $output_file_excel);
        return $output_file_excel;
    }

    /**
     * It generates cash invoices for vendor
     * @param Array $invoices
     * @param Array $details
     * @return type
     */
    function generate_cash_details_invoices_for_vendors($vendor_id, $data, $meta,$invoice_type, $agent_id, $from_date, $to_date) {
        log_message('info', __FUNCTION__ . '=> Entering... for invoices:'. $meta['invoice_id']);
        $files = array();
        // it stores all unique booking id which is completed by particular vendor id
        $unique_booking = array_unique(array_map(function ($k) {
                        return $k['booking_id'];
                    }, $data));

            // Count unique booking id
        $meta['booking_count'] = count($unique_booking);
            
        $template = 'Vendor_Settlement_Template-CashDetailed-v3.xlsx';

        //set message to be displayed in excel sheet
        $meta['msg'] = 'Thanks 247around Partner for your support, we completed ' .  $meta['booking_count'] .
                    ' bookings with you from ' .  $meta['sd'] . ' to ' .  $meta['ed'] .
                    '. Total transaction value for the bookings was Rs. ' . round($meta['total_amount_paid'], 0) .
                    '. Around royalty for this invoice is Rs. ' . round($meta['sub_total_amount'], 0) .
                    '. Your rating for completed bookings is ' . $meta['t_rating'] .
                    '. We look forward to your continued support in future. As next step, please deposit ' .
                    '247around royalty per the below details.';
        
        $output_file_excel = TMP_FOLDER . $meta['invoice_id'] . "-detailed.xlsx";
        $this->invoice_lib->generate_invoice_excel($template, $meta, $data, $output_file_excel);
        array_push($files, $output_file_excel);
        
        $convert = $this->invoice_lib->send_request_to_convert_excel_to_pdf($meta['invoice_id'], $invoice_type); 
        $output_file_main = $convert['main_pdf_file_name'];
        array_push($files, TMP_FOLDER.$convert['excel_file']);

        log_message('info', 'Excel data: ' . print_r($meta, true));

        if ($invoice_type === "final") {

            $rm_details = $this->vendor_model->get_rm_sf_relation_by_sf_id($vendor_id);
            $rem_email_id = "";
            if (!empty($rm_details)) {
                $rem_email_id = ", " . $rm_details[0]['official_email'];
            }
            $to = $meta['owner_email'] . ", " . $meta['primary_contact_email'];
            $cc = ANUJ_EMAIL_ID.", ".ACCOUNTANT_EMAILID . $rem_email_id;
            $pdf_attachement = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/invoices-excel/".$output_file_main;
                
            //get email template from database
            $email_template = $this->booking_model->get_booking_email_template(CASH_DETAILS_INVOICE_FOR_VENDORS_EMAIL_TAG);
            $subject = vsprintf($email_template[4], array($meta['company_name'],$meta['sd'],$meta['ed']));
            $message = $email_template[0];
            $email_from = $email_template[2];
                
            $mail_ret = $this->send_email_with_invoice($email_from, $to, $cc, $message, $subject, $output_file_excel, $pdf_attachement,CASH_DETAILS_INVOICE_FOR_VENDORS_EMAIL_TAG);

            //Send SMS to PoC/Owner
            $this->send_invoice_sms("Cash",  $meta['sd'], $meta['total_amount_paid'], $meta['owner_phone_1'], $vendor_id);

           //Upload Excel files to AWS
            $this->upload_invoice_to_S3($meta['invoice_id']);
            $t_s_charge =  ($meta['r_sc'] - $meta['upcountry_charge']) - $this->booking_model->get_calculated_tax_charge( ($meta['r_sc'] - $meta['upcountry_charge']), 18);
            $t_ad_charge = $meta['r_asc'] - $this->booking_model->get_calculated_tax_charge( $meta['r_asc'], 18);
            $t_part_charge = $meta['r_pc'] - $this->booking_model->get_calculated_tax_charge($meta['r_pc'], 18);
            //Save this invoice info in table
            $invoice_details = array(
                'invoice_id' => $meta['invoice_id'],
                'type' => 'Cash',
                'type_code' => 'A',
                'vendor_partner' => 'vendor',
                'vendor_partner_id' => $vendor_id,
                'invoice_file_main' => $output_file_main,
                'invoice_file_excel' => $meta['invoice_id'] . '.xlsx',
                'invoice_detailed_excel' => $meta['invoice_id'] . '-detailed.xlsx',
                'invoice_date' => date("Y-m-d"),
                'from_date' => date("Y-m-d", strtotime($from_date)),
                'to_date' => date("Y-m-d", strtotime($to_date)),
                'num_bookings' =>  $meta['booking_count'],
                "parts_count" => $meta['parts_count'],
                'total_service_charge' => $t_s_charge,
                'total_additional_service_charge' => $t_ad_charge,
                'parts_cost' => $t_part_charge,
                'vat' => 0, //No VAT here in Cash invoice
                'total_amount_collected' => $meta['total_amount_paid'],
                'rating' => $meta['t_rating'],
                'around_royalty' => $meta['sub_total_amount'],
                'upcountry_price' => $meta['upcountry_distance'],
                'upcountry_distance' => $meta['upcountry_distance'],
                'upcountry_booking' => $meta['upcountry_booking'],
                //Service tax which needs to be paid
                'service_tax' => 0,
                'invoice_date' => date('Y-m-d'),
                //Amount needs to be collected from Vendor
                'amount_collected_paid' =>$meta['sub_total_amount'],
                //Mail has not 
                'mail_sent' => $mail_ret,
                //SMS has been sent or not
                'sms_sent' => 1,
                //Add 1 month to end date to calculate due date
                'due_date' => date("Y-m-d"),
                //add agent_id
                'agent_id' => $agent_id,
                "cgst_tax_rate" => $meta['cgst_tax_rate'],
                "sgst_tax_rate" => $meta['sgst_tax_rate'],
                "igst_tax_rate" => $meta['igst_tax_rate'],
                "igst_tax_amount" => $meta["igst_total_tax_amount"],
                "sgst_tax_amount" => $meta["sgst_total_tax_amount"],
                "cgst_tax_amount" => $meta["cgst_total_tax_amount"],
                "hsn_code" => COMMISION_CHARGE_HSN_CODE,
                "invoice_file_pdf" => $convert['copy_file']
            );

            $this->invoices_model->action_partner_invoice($invoice_details);
         
            log_message('info', __METHOD__ . ': Invoice ' . $meta['invoice_id'] . ' details  entered into invoices table');

            /*
             * Update booking-invoice table to capture this new invoice against these bookings.
             * Since this is a type 'Cash' invoice, it would be stored as a vendor-debit invoice.
             */

             $this->update_invoice_id_in_unit_details($data, $meta['invoice_id'], $invoice_type, "vendor_cash_invoice_id");
        } else {
            
            $this->download_invoice_files($meta['invoice_id'], $output_file_excel, $output_file_main);
        }
        
        //Delete XLS files now
        foreach ($files as $file_name) {
            exec("rm -rf " . escapeshellarg($file_name));
        }
        unset($meta);
        unset($invoice_details);
        return true;
    }
    
    function send_email_with_invoice($email_from, $to, $cc, $message, $subject, $output_file_excel, $pdf_attachement,$invoiceTag, $multipleResponse = array()) {
        $this->email->clear(TRUE);
        $this->email->from($email_from, '247around Team');
        $this->email->to($to);
        $this->email->cc($cc);
        
        if(!empty($multipleResponse)){
            foreach ($multipleResponse as $value) {
                 $this->email->attach($value['pdf'], 'attachment');
                 $this->email->attach($value['excel'], 'attachment');
            }
        } else {
            if(!empty($output_file_excel)){
                //attach detailed invoice
                $this->email->attach($output_file_excel, 'attachment');
            }
            
            if(!empty($pdf_attachement)){
                //attach mail invoice
                $this->email->attach($pdf_attachement, 'attachment');
            }
            
        }
        $this->email->message($message);
        $this->email->subject($subject);
        $mail_ret = $this->email->send();
        if ($mail_ret) {
            $this->notify->add_email_send_details($email_from,$to,$cc,"",$subject,$message,$pdf_attachement,$invoiceTag);
            log_message('info', __METHOD__ . ": Mail sent successfully");
            echo "Mail sent successfully..............." . PHP_EOL;
            return 1;
        } else {
            log_message('info', __METHOD__ . ": Mail could not be sent");
            echo "Mail could not be sent..............." . PHP_EOL;
            return 0;
        }
    }

    /**
     * @desc: This Method used to update invoice id in unit_details
     * @param $invoices_data Array Misc data about Invoice
     * @param $invoice_id String Invoice ID
     * @param $invoice_type String Invoice Type (draft/final)
     */
    function update_invoice_id_in_unit_details($invoices_data, $invoice_id, $invoice_type, $unit_column) {
        log_message('info', __METHOD__ . ': Reset Invoice id ' . " invoice id " . $invoice_id.' and invoice_type: '.$invoice_type.' invoice_data: '.  print_r($invoices_data,true));

        if ($invoice_type == "final") {
            if ($invoices_data[0]['price_tags'] != "Upcountry Services") {
                $this->booking_model->update_booking_unit_details_by_any(array($unit_column => $invoice_id), array($unit_column => NULL));

                foreach ($invoices_data as $value) {
                    if ($value['price_tags'] != "Upcountry Services") {

                        log_message('info', __METHOD__ . ': update invoice id in booking unit details ' . $value['unit_id'] . " invoice id " . $invoice_id);
                        $this->booking_model->update_booking_unit_details_by_any(array('id' => $value['unit_id']), array($unit_column => $invoice_id));
                    }
                }
            }
        }
    }
    
    function update_invoice_id_in_buyback($invoices_data, $invoice_id, $invoice_type, $unit_column) {
        log_message('info', __METHOD__ . ': Reset Invoice id ' . " invoice id " . $invoice_id.' and invoice_type: '.$invoice_type.' invoice_data: '.  print_r($invoices_data,true));

        if ($invoice_type == "final") {
            $this->bb_model->update_bb_unit_details(array($unit_column => $invoice_id), array($unit_column => NULL));

            foreach ($invoices_data as $value) {
               
                log_message('info', __METHOD__ . ': update invoice id in booking unit details ' . $value['unit_id'] . " invoice id " . $invoice_id);
                $this->bb_model->update_bb_unit_details(array('id' => $value['unit_id']), array($unit_column => $invoice_id));
                
                $this->cp_model->update_bb_cp_order_action(array('partner_order_id' => $value['partner_order_id']),
                        array('current_status' => 'Delivered', 'internal_status' => 'Delivered'));
                
            } 
        }
    }

    /**
     * @desc: This is used to generates foc type invoices for vendor
     * @param: Array()
     * @return: Array (booking id)
     */
    function generate_foc_details_invoices_for_vendors($invoice_details, $invoice_data, $vendor_id, $invoice_type, $agent_id,$from_date,$to_date ) {
        log_message('info', __FUNCTION__ . '=> Entering...');
        
        $is_upcountry = FALSE;
        $files = array();
        $total_upcountry_booking = $upcountry_rate = $upcountry_distance = $total_inst_charge = $total_st_charge = $total_stand_charge =  $total_vat_charge = 0;
        $total_upcountry_price = 0;
       
        $template = 'Vendor_Settlement_Template-FoC-v4.xlsx';
        
        if (!empty($invoice_data['upcountry'])) {
            $template = 'Vendor_Settlement_Template-FoC-upcountry-v4.xlsx';
            $total_upcountry_booking = $invoice_data['upcountry'][0]['total_booking'];
            $upcountry_rate = $invoice_data['upcountry'][0]['sf_upcountry_rate'];
            $upcountry_distance = $invoice_data['upcountry'][0]['total_distance'];
            $total_upcountry_price = $invoice_data['upcountry'][0]['total_upcountry_price'];
            $is_upcountry = TRUE;
        }
        
        $rating = 0;

            // Calculate charges
            $rating_count = 0;
            for ($j = 0; $j < count($invoice_details); $j++) {
                $total_inst_charge += $invoice_details[$j]['vendor_installation_charge'];
               
                $total_stand_charge += $invoice_details[$j]['vendor_stand'];
               
                $invoice_details[$j]['amount_paid'] = round(($invoice_details[$j]['vendor_installation_charge'] + 
                        $invoice_details[$j]['vendor_stand']), 0);
                $rating += $invoice_details[$j]['rating_stars'];
                if(!empty($invoice_details[$j]['rating_stars'])){
                    $rating_count++;
                }
            }
            if($rating_count == 0){
                $rating_count = 1;
            }
            
            $t_total = $total_inst_charge + $total_stand_charge;
            
            $tds = $this->check_tds_sc($invoice_data['booking'][0], $invoice_data['meta']['total_sc_charge']);

            //this array stores unique booking id
            $unique_booking = array_unique(array_map(function ($k) {
                        return $k['booking_id'];
                    }, $invoice_details));

            // count unique booking id
            $invoice_data['meta']['count'] = count($unique_booking);
            $invoice_data['meta']['tds'] = $tds['tds'];
            $invoice_data['meta']['tds_rate'] = $tds['tds_rate'];
            $invoice_data['meta']['tds_tax_rate'] = $tds['tds_per_rate'];
            $invoice_data['meta']['t_ic'] =round($total_inst_charge,0);
            $invoice_data['meta']['t_stand'] = round($total_stand_charge,0);
            $invoice_data['meta']['t_total'] =  round($t_total,0);
            $invoice_data['meta']['total_gst_amount'] =  round($invoice_data['meta']["cgst_total_tax_amount"] + $invoice_data['meta']["sgst_total_tax_amount"] +
                    $invoice_data['meta']["igst_total_tax_amount"],0);
            $invoice_data['meta']['t_rating'] = $rating/$rating_count;
            $invoice_data['meta']['cr_total_penalty_amount'] = round((array_sum(array_column($invoice_data['c_penalty'], 'p_amount'))),0);
            $invoice_data['meta']['total_penalty_amount'] = -round((array_sum(array_column($invoice_data['d_penalty'], 'p_amount'))), 0);
            $invoice_data['meta']['total_upcountry_price'] = round($total_upcountry_price, 0);
            $invoice_data['meta']['total_courier_charges'] = round((array_sum(array_column($invoice_data['courier'], 'courier_charges_by_sf'))), 0);
            
            $invoice_data['meta']['t_vp_w_tds'] = round( ($invoice_data['meta']['sub_total_amount'] - $invoice_data['meta']['tds']), 0);
            
            $invoice_data['meta']['msg'] = 'Thanks 247around Partner for your support, we completed ' .  $invoice_data['meta']['count'] .
                    ' bookings with you from ' .  $invoice_data['meta']['sd'] . ' to ' .  $invoice_data['meta']['ed'] .
                    '. Total transaction value for the bookings was Rs. ' .  $invoice_data['meta']['t_vp_w_tds'] .
                    '. Your rating for completed bookings is ' . round( $invoice_data['meta']['t_rating'], 0) .
                    '. We look forward to your continued support in future. As next step, 247around will pay you remaining amount as per our agreement.';

           
            log_message('info', 'Excel data: ' . print_r( $invoice_data['meta'], true));

          
            //Get populated XLS with data
            $output_file_excel = TMP_FOLDER . $invoice_data['meta']['invoice_id'] . "-detailed.xlsx";
           
            $this->generate_foc_detailed_invoice_excel($template, $invoice_data, $invoice_details, $output_file_excel);
            log_message('info', __FUNCTION__ . " Excel File Created " . $output_file_excel);
            
            array_push($files, $output_file_excel);
        
            $convert = $this->invoice_lib->send_request_to_convert_excel_to_pdf($invoice_data['meta']['invoice_id'], $invoice_type,true); 
            $output_file_main = $convert['main_pdf_file_name'];
            array_push($files, TMP_FOLDER.$convert['excel_file']);

            if ($invoice_type === "final") {
                log_message('info', __FUNCTION__ . " Final");
                
                $rm_details = $this->vendor_model->get_rm_sf_relation_by_sf_id($vendor_id);
                $rem_email_id = "";
                if (!empty($rm_details)) {
                    $rem_email_id = ", " . $rm_details[0]['official_email'];
                }
                
                //get email template from database
                $email_template = $this->booking_model->get_booking_email_template(FOC_DETAILS_INVOICE_FOR_VENDORS_EMAIL_TAG);
                $subject = vsprintf($email_template[4], array($invoice_data['meta']['company_name'],$invoice_data['meta']['sd'],$invoice_data['meta']['ed']));
                $message = $email_template[0];
                $email_from = $email_template[2];
                $to = $invoice_data['meta']['owner_email'] . ", " . $invoice_data['meta']['primary_contact_email'];
                
                $cc = ANUJ_EMAIL_ID.", ".ACCOUNTANT_EMAILID . $rem_email_id;
                $pdf_attachement = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/invoices-excel/".$output_file_main;
                 //Upload Excel files to AWS
                $this->upload_invoice_to_S3($invoice_data['meta']['invoice_id']);
                
                $mail_ret = $this->send_email_with_invoice($email_from, $to, $cc, $message, $subject, $output_file_excel, $pdf_attachement,FOC_DETAILS_INVOICE_FOR_VENDORS_EMAIL_TAG);
                
                //Send SMS to PoC/Owner
                $this->send_invoice_sms("FOC", $invoice_data['meta']['sd'], $invoice_data['meta']['t_vp_w_tds'], $invoice_data['meta']['owner_phone_1'], $vendor_id);
               
                //Save this invoice info in table
                $invoice_details_insert = array(
                    'invoice_id' => $invoice_data['meta']['invoice_id'],
                    'type' => 'FOC',
                    'type_code' => 'B',
                    'vendor_partner' => 'vendor',
                    'vendor_partner_id' => $vendor_id,
                    'invoice_file_main' => $output_file_main,
                    'invoice_file_excel' => "copy_".$invoice_data['meta']['invoice_id'] . '.xlsx',
                    'invoice_detailed_excel' => $invoice_data['meta']['invoice_id'] . '-detailed.xlsx',
                    'invoice_date' => date("Y-m-d"),
                    'from_date' => date("Y-m-d", strtotime($from_date)),
                    'to_date' => date("Y-m-d", strtotime($to_date)),
                    'num_bookings' => $invoice_data['meta']['count'],
                    'total_service_charge' => $total_inst_charge,
                    'total_additional_service_charge' => 0,
                    'service_tax' => 0,
                    'parts_cost' => $total_stand_charge,
                    'vat' => 0.00,
                    'total_amount_collected' => ($invoice_data['meta']['sub_total_amount']),
                    'tds_amount' => $invoice_data['meta']['tds'],
                    'rating' => $invoice_data['meta']['t_rating'],
                    'around_royalty' => 0,
                    //Amount needs to be Paid to Vendor
                    'amount_collected_paid' => (0 - $invoice_data['meta']['t_vp_w_tds']),
                    //Mail has not sent
                    'mail_sent' => $mail_ret,
                    'tds_rate' => $invoice_data['meta']['tds_tax_rate'],
                    //SMS has been sent or not
                    'sms_sent' => 1,
                    'upcountry_booking' => $total_upcountry_booking,
                    'upcountry_rate' => $upcountry_rate,
                    'upcountry_price' => $invoice_data['meta']['total_upcountry_price'],
                    'upcountry_distance' => $upcountry_distance,
                    'penalty_amount' => abs($invoice_data['meta']['total_penalty_amount']),
                    'penalty_bookings_count' => array_sum(array_column($invoice_data['d_penalty'], 'penalty_times')),
                    'credit_penalty_amount' => $invoice_data['meta']['cr_total_penalty_amount'],
                    'credit_penalty_bookings_count' => array_sum(array_column($invoice_data['c_penalty'], 'penalty_times')),
                    'courier_charges' => $invoice_data['meta']['total_courier_charges'],
                    'invoice_date' => date('Y-m-d'),
                    //Add 1 month to end date to calculate due date
                    'due_date' => date("Y-m-d", strtotime($to_date . "+1 month")),
                    //add agent id
                    'agent_id' => $agent_id,
                    "cgst_tax_rate" => $invoice_data['meta']['cgst_tax_rate'],
                    "sgst_tax_rate" => $invoice_data['meta']['sgst_tax_rate'],
                    "igst_tax_rate" => $invoice_data['meta']['igst_tax_rate'],
                    "igst_tax_amount" => $invoice_data['meta']["igst_total_tax_amount"],
                    "sgst_tax_amount" => $invoice_data['meta']["sgst_total_tax_amount"],
                    "cgst_tax_amount" => $invoice_data['meta']["cgst_total_tax_amount"],
                    "parts_count" => $invoice_data['meta']["parts_count"],
                    "rcm" => $invoice_data['meta']['rcm'],
                    "invoice_file_pdf" => $convert['copy_file'],
                    "hsn_code" => $invoice_data['booking'][0]['hsn_code']
                   
                );

                // insert invoice details into vendor partner invoices table
                $this->invoices_model->action_partner_invoice($invoice_details_insert);
                //Update Penalty Amount
                foreach ($invoice_data['d_penalty'] as $value) {
                    $this->penalty_model->update_penalty_any(array('booking_id' => $value['booking_id']), array('foc_invoice_id' => $invoice_data['meta']['invoice_id']));
                }
                
                if (!empty($invoice_data['upcountry'])) {
                    foreach ($invoice_data['upcountry'] as $up_booking_details) {
                        $up_b = explode(",", $up_booking_details['booking']);
                        for($i=0; $i < count($up_b); $i++){

                            $this->booking_model->update_booking(trim($up_b[$i]), array('upcountry_vendor_invoice_id' => $invoice_data['meta']['invoice_id']));
                        }

                    }
                }

                log_message('info', __METHOD__ . ': Invoice ' . $invoice_data['meta']['invoice_id'] . ' details  entered into invoices table');

                /*
                 * Update booking-invoice table to capture this new invoice against these bookings.
                 * Since this is a type B invoice, it would be stored as a vendor-credit invoice.
                 */

                $this->update_invoice_id_in_unit_details($invoice_details, $invoice_data['meta']['invoice_id'], $invoice_type, "vendor_foc_invoice_id");
            } else {
                
                 $this->download_invoice_files($invoice_data['meta']['invoice_id'], $output_file_excel, $output_file_main);
            }
        
           
        //Delete XLS files now
        foreach ($files as $file_name) {
            exec("rm -rf " . escapeshellarg($file_name));
        }
           
        return true;

    }
    
     function generate_foc_detailed_invoice_excel($template, $invoice_data, $invoices, $output_file_excel) {
       
        // directory
        $templateDir = __DIR__ . "/../excel-templates/";
        $config = array(
                'template' => $template,
                'templateDir' => $templateDir
            );

            //load template
        $R = new PHPReport($config);
         $R->load(array(
                array(
                    'id' => 'meta',
                    'data' =>  $invoice_data['meta'],
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
                array(
                    'id' => 'upcountry',
                    'repeat' => true,
                    'data' =>  $invoice_data['upcountry']
                ),
                array(
                    'id' => 'penalty',
                    'repeat' => true,
                    'data' =>  $invoice_data['d_penalty']
                ),
                array(
                    'id' => 'cr_penalty',
                    'repeat' => true,
                    'data' =>  $invoice_data['c_penalty']
                ),
                array(
                    'id' => 'courier',
                    'repeat' => true,
                    'data' =>  $invoice_data['courier']
                ),
                    )
            );
        
        $res1 = 0;
        if (file_exists($output_file_excel)) {

            system(" chmod 777 " . $output_file_excel, $res1);
            unlink($output_file_excel);
        }

        $R->render('excel', $output_file_excel);
        
        log_message('info', __FUNCTION__ . ' File created ' . $output_file_excel);

        if (file_exists($output_file_excel)) {
            system(" chmod 777 " . $output_file_excel, $res1);
            return true;
            
        } else {
            return false;
        }
    }

    /**
     * @desc: This Method loads invoice form
     */
    function get_invoices_form() {
        $this->checkUserSession();
        $data['vendor_partner'] = "vendor";
        $data['id'] = "";
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/get_invoices_form', $data);
    }

    /**
     * @desc: This method is used to generate invoices for partner or vendor.
     * This methd is used to get data from Form.
     */
    function process_invoices_form() {
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . " Entering......");
        $details['vendor_partner'] = $this->input->post('partner_vendor');
        $details['invoice_type'] = $this->input->post('invoice_version');
        $details['vendor_partner_id'] = $this->input->post('partner_vendor_id');
        $details['date_range'] = $this->input->post('daterange');
        $details['vendor_invoice_type'] = $this->input->post('vendor_invoice_type');
        $details['agent_id'] = $this->session->userdata('id');
        
        if(count($details['vendor_partner_id']) == 1 && in_array('All', $details['vendor_partner_id'])){
            $details['vendor_partner_id'] = "All";
            $status = $this->generate_vendor_partner_invoices($details);
        }else if(count($details['vendor_partner_id']) > 1 && in_array('All', $details['vendor_partner_id'])){
            unset($details['vendor_partner_id'][array_search( 'All', $details['vendor_partner_id'] )] );
            foreach($details['vendor_partner_id'] as $value){
                $details['vendor_partner_id'] = $value;
                $status = $this->generate_vendor_partner_invoices($details);
            }
        }else if(count($details['vendor_partner_id']) > 1 && !in_array('All', $details['vendor_partner_id'])){
            foreach($details['vendor_partner_id'] as $value){
                $details['vendor_partner_id'] = $value;
                $status = $this->generate_vendor_partner_invoices($details);
            }
        }else{
            $details['vendor_partner_id'] = $details['vendor_partner_id'][0];
            $status = $this->generate_vendor_partner_invoices($details);
        }
        
        if ($status) {
            $output = "Invoice Generated...";
            $userSession = array('success' => $output);
            $this->session->set_userdata($userSession);
        } else {
            $output = "Data Not Found, No Invoice Generated !";
            $userSession = array('error' => $output);
            $this->session->set_userdata($userSession);
        }
        redirect(base_url() . "employee/invoice/get_invoices_form");
    }

    /**
     * @desc This is used to generate invoice from terminal.
     * Use param like this - 'vendor','final','1','2017-04-01', '2017-04-31','foc'
     * @param type $vendor_partner
     * @param type $invoice_type
     * @param type $vendor_partner_id
     * @param type $date_range
     * @param type $vendor_invoice_type
     */
    function process_invoices_from_terminal($vendor_partner, $invoice_type, $vendor_partner_id, $from_date_range_tmp, $to_date_range_tmp, $vendor_invoice_type) {
        log_message('info', __FUNCTION__ . " Entering...... Invoice_type: ".$invoice_type.' vendor_partner_id: '.$vendor_partner_id.' vendor_partner: '.$vendor_partner.' Vendor_Invoice_type: '.$vendor_invoice_type);
        $from_date_range = str_replace("-", "/", $from_date_range_tmp);
        $to_date_range = str_replace("-", "/", $to_date_range_tmp);
        $details['vendor_partner'] = $vendor_partner;
        $details['invoice_type'] = $invoice_type;
        $details['vendor_partner_id'] = $vendor_partner_id;
        $details['date_range'] = $from_date_range . "-" . $to_date_range;
        $details['vendor_invoice_type'] = $vendor_invoice_type;
        $details['agent_id'] = _247AROUND_DEFAULT_AGENT;

        $this->generate_vendor_partner_invoices($details);
    }

    /**
     * @desc: 
     * @param array $details
     */
    function generate_vendor_partner_invoices($details) {
        log_message('info', __FUNCTION__ . " Entering...... And Invoice_Details: ". print_r($details,true));
        
        
        if ($details['vendor_partner'] === "vendor") {
            echo "Invoice Generating..";
            log_message('info', "Invoice generate - vendor id: " . print_r($details['vendor_partner_id'], true) . ", Date Range" .
                    print_r($details['date_range'], true) . ", Invoice version" . print_r($details['invoice_type'], true) . ", Invoice type" .
                    print_r($details['vendor_invoice_type'], true));

            return $this->generate_vendor_invoices($details, 0);
        } else if ($details['vendor_partner'] === "partner") {
            log_message('info', "Invoice generate - partner id: " . print_r($details['vendor_partner_id'], true) . ", Date Range" .
                    print_r($details['date_range'], true) . ", Invoice status" . print_r($details['invoice_type'], true));

            return $this->generate_partner_invoices($details['vendor_partner_id'], $details['date_range'], $details['invoice_type'],$details['agent_id']);
        }

        log_message('info', __FUNCTION__ . " Exit......");
    }

    /**
     * @desc: This method is used to Re-Generate Invoice id. 
     * @param String $invoice_id
     * @param String $invoice_type
     */
    function regenerate_invoice($invoice_id, $invoice_type) {
         $this->checkUserSession();
        log_message('info',__FUNCTION__.'Invoice_id: ' . $invoice_id.' Invoice_type: '.$invoice_type);
        
        $where = array('invoice_id' => $invoice_id);
        //Get Invocie details from Vendor Partner Invoice Table
        $invoice_details = $this->invoices_model->get_invoices_details($where);
        if (!empty($invoice_details)) {

            $details['vendor_partner'] = $invoice_details[0]['vendor_partner'];
            $details['invoice_type'] = $invoice_type;
            $details['invoice_id'] = $invoice_id;
            $details['vendor_partner_id'] = $invoice_details[0]['vendor_partner_id'];
            $details['date_range'] = str_replace("-", "/", $invoice_details[0]['from_date']) . "-" . str_replace("-", "/", date('Y-m-d', strtotime($invoice_details[0]['to_date'])));
            $details['agent_id'] = $this->session->userdata('id');
           
            if ($invoice_details[0]['vendor_partner'] == 'vendor' && $invoice_details[0]['type'] != "Stand") {
                $exist_invoice_type = "";
                if ($invoice_details[0]['type'] == "FOC") {
                    $exist_invoice_type = "foc";
                    $where_unit = array('vendor_foc_invoice_id' => $invoice_id);
                } else if ($invoice_details[0]['type'] == "Cash") {
                    $where_unit = array('vendor_cash_invoice_id' => $invoice_id);
                    $exist_invoice_type = "cash";
                }

                $unit_details = $this->booking_model->get_unit_details($where_unit);
                if (!empty($unit_details)) {
                    // Check is null vendor foc invoice id
                    if (!is_null($unit_details[0]['vendor_cash_invoice_id'])) {

                        $details['vendor_invoice_type'] = "cash";
                        log_message('info', "Invoice generate - vendor id: " . print_r($details['vendor_partner_id'], true) . ", Date Range" .
                                print_r($details['date_range'], true) . ", Invoice version" . print_r($details['invoice_type'], true) . ", Invoice type" .
                                print_r($details['vendor_invoice_type'], true));

                        $this->generate_vendor_invoices($details, 1);
                    }
                    // Check is null vendor foc invoice id
                    if (!is_null($unit_details[0]['vendor_foc_invoice_id'])) {

                        $details['vendor_invoice_type'] = "foc";
                        log_message('info', "Invoice generate - vendor id: " . print_r($details['vendor_partner_id'], true) . ", Date Range" .
                                print_r($details['date_range'], true) . ", Invoice version" . print_r($details['invoice_type'], true) . ", Invoice type" .
                                print_r($details['vendor_invoice_type'], true));

                        $this->generate_vendor_invoices($details, 1);
                    }
                } else {
                    $details['vendor_invoice_type'] = $exist_invoice_type;

                    log_message('info', "Invoice generate - vendor id: " . print_r($details['vendor_partner_id'], true) . ", Date Range" .
                            print_r($details['date_range'], true) . ", Invoice version" . print_r($details['invoice_type'], true) . ", Invoice type" .
                            print_r($details['vendor_invoice_type'], true));
                    $this->generate_vendor_invoices($details, 1);
                }
            } else if ($invoice_details[0]['type'] == "Stand") {
                $details['vendor_invoice_type'] = "brackets";
                log_message('info', "Invoice generate - vendor id: " . print_r($details['vendor_partner_id'], true) . ", Date Range" .
                        print_r($details['date_range'], true) . ", Invoice version" . print_r($details['invoice_type'], true) . ", Invoice type" .
                        print_r($details['vendor_invoice_type'], true));
                $this->generate_vendor_invoices($details, 1);
            }
        }

        redirect(base_url() . "employee/invoice/invoice_summary/" . $details['vendor_partner'] . "/" . $details['vendor_partner_id']);
    }

    /**
     * @desc: this method is used to generate both type invoices (invoices details and summary)
     * @param type $partner_id
     * @param type $date_range
     * @param type $invoice_type
     */
    function generate_partner_invoices($partner_id, $date_range, $invoice_type,$agent_id) {
        log_message('info', __FUNCTION__ . '=> Entering... Partner Id' . $partner_id . " date range " . $date_range . " invoice type " . $invoice_type.' agent_id:'.$agent_id);
        $custom_date = explode("-", $date_range);
        $from_date = $custom_date[0];
        $to_date = $custom_date[1];

        if ($partner_id == "All") {
//            $partner = $this->partner_model->get_all_partner_source();
//            foreach ($partner as $value) {
//                log_message('info', __FUNCTION__ . '=> Partner Id ' . $value['partner_id']);
//                 $this->create_partner_invoice($value['partner_id'], $from_date, $to_date, $invoice_type, $agent_id);
//                
//            }
           return true;
        } else {
            log_message('info', __FUNCTION__ . '=> Partner Id ' . $partner_id);
            return $this->create_partner_invoice($partner_id, $from_date, $to_date, $invoice_type, $agent_id);
            
        }

        log_message('info', __FUNCTION__ . '=> Exiting...');
    }

    /**
     * This method used to generates previous invoice for vendor
     * @param Array $details
     */
    function generate_vendor_invoices($details, $is_regenerate) {
        log_message('info', __FUNCTION__ . " Entering......" . " Details: " . print_r($details, true));
        switch ($details['vendor_invoice_type']) {
            case "cash":

                if ($details['vendor_partner_id'] == 'All') {
                    $select = "service_centres.name, service_centres.id";
                    $vendor_details = $this->vendor_model->getVendorDetails($select, array('is_sf' => 1));
                    foreach ($vendor_details as $value) {
                        $details['vendor_partner_id'] = $value['id'];

                        log_message('info', __FUNCTION__ . " Preparing CASH Invoice for Vendor: " . $details['vendor_partner_id']);
                        echo " Preparing CASH Invoice for Vendor: " . $details['vendor_partner_id'] . PHP_EOL;

                        //Prepare main invoice first
                        $this->generate_vendor_cash_invoice($details, $details['agent_id'], $is_regenerate);
                        
                    }
                    return true;
                } else {
                    echo " Preparing CASH Invoice  Vendor: " . $details['vendor_partner_id'];

                    log_message('info', __FUNCTION__ . ": Preparing CASH Invoice Vendor Id: " . $details['vendor_partner_id']);

                    //Prepare main invoice first
                    $details = $this->generate_vendor_cash_invoice($details, $details['agent_id'], $is_regenerate);
                    if ($details) {
                        //Generate detailed annexure now
                       
                        return TRUE;
                    } else {
                        echo "<script>alert('Data Not Found');</script>";
                        echo " Data Not found for vendor: " . $details['vendor_partner_id'];
                        log_message('info', __FUNCTION__ . " Data Not found for vendor: " . $details['vendor_partner_id']);
                        return FALSE;
                    }
                }
                break;

            case "foc":
                log_message('info', __FUNCTION__ . " Preparing FOC Invoice");

                if ($details['vendor_partner_id'] == 'All') {
                    $select = "service_centres.name, service_centres.id";
                    $vendor_details = $this->vendor_model->getVendorDetails($select, array('is_sf' => 1));
                    echo " Preparing FOC Invoice  Vendor: " . $details['vendor_partner_id'];
                    foreach ($vendor_details as $value) {
                        $details['vendor_partner_id'] = $value['id'];
                        log_message('info', __FUNCTION__ . ": Preparing FOC Invoice Vendor Id: " . $details['vendor_partner_id']);
                        //Prepare main invoice first
                        $this->generate_vendor_foc_invoice($details, $is_regenerate);
                    }
                    return true;
                } else {
                    //Prepare main invoice first
                    return $this->generate_vendor_foc_invoice($details, $is_regenerate);

                }
                break;

            case "brackets":
                log_message('info', __FUNCTION__ . " Brackets");
                //This constant is used to track all vendors selected to avoid sending mail when all vendor +draft is selected
                if ($details['vendor_partner_id'] === 'All') {
                    $select = "service_centres.name, service_centres.id";
                    $vendor = $this->vendor_model->getVendorDetails($select, array('is_sf' => 1));

                    foreach ($vendor as $value) {
                        log_message('info', __FUNCTION__ . " Brackets Vendor Id: " . $value['id']);
                        $details['vendor_partner_id'] = $value['id'];
                        //Generating and sending invoice to vendors
                        $this->generate_brackets_invoices($details);
                    }
                    return true;
                } else {
                    log_message('info', __FUNCTION__ . " Brackets Vendor Id: " . $details['vendor_partner_id']);
                    //Generating and sending invoice to vendors
                    return $this->generate_brackets_invoices($details);
                }
                break;
                
                case "buyback":
                    log_message('info', __FUNCTION__ . " Buyback");
                                  
                    if ($details['vendor_partner_id'] === 'All') {
                        $select = "service_centres.name, service_centres.id";
                        $vendor = $this->vendor_model->getVendorDetails($select, array('is_cp' => 1));
                        foreach ($vendor as $value) {
                            log_message('info', __FUNCTION__ . " Brackets Vendor Id: " . $value['id']);
                            $details['vendor_partner_id'] = $value['id'];
                            //Generating and sending invoice to vendors
                            $this->generate_buyback_invoices($details, $is_regenerate);
                        }
                        
                    } else {
                       log_message('info', __FUNCTION__ . " Brackets Vendor Id: " . $details['vendor_partner_id']);
                       //Generating and sending invoice to vendors
                       return $this->generate_buyback_invoices($details, $is_regenerate); 
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
         $this->checkUserSession();
        if ($vendor_partner == 'vendor') {
            $select = "service_centres.name, service_centres.id";
            $data['service_center'] = $this->vendor_model->getVendorDetails($select);
        } else {
            $data['partner'] = $this->partner_model->getpartner("", false);
        }

        $data['vendor_partner_id'] = $vendor_partner_id;
        $data['vendor_partner'] = $vendor_partner;
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/invoices_details', $data);
    }
    
    function send_invoice_sms($type, $from_date, $total, $owner_phone_1, $vendor_id){
        $sms['tag'] = "vendor_invoice_mailed";
        $sms['smsData']['type'] = $type;
        $sms['smsData']['month'] = date('M Y', strtotime($from_date));
        $sms['smsData']['amount'] = round($total,0);
        $sms['phone_no'] = $owner_phone_1;
        $sms['booking_id'] = "";
        $sms['type'] = "vendor";
        $sms['type_id'] = $vendor_id;

        $this->notify->send_sms_msg91($sms);
        log_message('info', __FUNCTION__ . " SMS Sent.....");
    }

    /**
     * @Desc: This function is used to generate brackets invoices for vendors
     * @params: vendor id, invoice_month, $invoice_type
     * @return : Mix
     */
    function generate_brackets_invoices($details) {
        log_message('info', __FUNCTION__ . " Entering......... " . print_r($details, true));
        $vendor_id = $details['vendor_partner_id'];
        $date_range = $details['date_range'];
        $invoice_type = $details['invoice_type'];

        $custom_date = explode("-", $date_range);
        $from_date = $custom_date[0];
        $to_date = $custom_date[1];
       
        //Making invoice array
        $invoice = $this->invoices_model->get_vendor_bracket_invoices($vendor_id, $from_date, $to_date);

        if (!empty($invoice)) {
            $files = array();
            if (isset($details['invoice_id'])) {
                log_message('info', __METHOD__ . ": Invoice Id re- geneterated " . $details['invoice_id']);
               
                 $invoice['meta']['invoice_id'] = $details['invoice_id'];
            } else {
               
                $invoice['meta']['invoice_id'] = $this->create_invoice_id_to_insert("Around");

            }

            log_message('info', __FUNCTION__ . " Entering......... Invoice Id " . $invoice['meta']['invoice_id']);
            
            $status =$this->invoice_lib->send_request_to_create_main_excel($invoice, $invoice_type);

            if($status){
                
                log_message('info', __FUNCTION__ . ' Invoice File is created. invoice id' . $invoice['meta']['invoice_id']);
               
                unset($invoice['booking']);
                $convert = $this->invoice_lib->send_request_to_convert_excel_to_pdf($invoice['meta']['invoice_id'], $invoice_type);
                $output_pdf_file_name = $convert['main_pdf_file_name'];
                array_push($files, TMP_FOLDER . $convert['excel_file']);
                
                if ($invoice_type == 'final') {
                    log_message('info', __FUNCTION__ . " Final");
                    
                    $this->send_invoice_sms("Stand", $invoice['meta']['sd'], $invoice['meta']['sub_total_amount'], $invoice['meta']['owner_phone_1'], $vendor_id);
                    //Upload Excel files to AWS
                    $this->upload_invoice_to_S3($invoice['meta']['invoice_id'], false);
                    
                    $attachment = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/invoices-excel/" . $output_pdf_file_name;
                    $send_mail = $this->send_brackets_invoice_mail($vendor_id, $attachment, $from_date);
                    
                    $invoice_details = array(
                        'invoice_id' => $invoice['meta']['invoice_id'],
                        'type' => 'Stand',
                        'type_code' => 'A',
                        'vendor_partner' => 'vendor',
                        'vendor_partner_id' => $vendor_id,
                        'invoice_file_excel' => $invoice['meta']['invoice_id'] . '.xlsx',
                        'invoice_file_main' => $output_pdf_file_name,
                        'from_date' => date("Y-m-d", strtotime($from_date)),
                        'to_date' => date('Y-m-d', strtotime('-1 day', strtotime($to_date))),
                        'num_bookings' => 0,
                        'parts_count' => $invoice['meta']['total_qty'],
                        'total_service_charge' => 0,
                        'total_additional_service_charge' => 0,
                        'service_tax' => 0,
                        'parts_cost' => $invoice['meta']['total_taxable_value'],
                        'vat' => 0,
                        'total_amount_collected' => $invoice['meta']['sub_total_amount'],
                        'rating' => 0,
                        'around_royalty' => $invoice['meta']['sub_total_amount'],
                        'amount_collected_paid' => $invoice['meta']['sub_total_amount'],
                        'invoice_date' => date('Y-m-d'),
                        'tds_amount' => 0.0,
                        'amount_paid' => 0.0,
                        'settle_amount' => 0,
                        'mail_sent' => 1,
                        'sms_sent' => $send_mail,
                        //Add 1 month to end date to calculate due date
                        'due_date' => date("Y-m-d"),
                        'agent_id' => $details['agent_id'],
                        "cgst_tax_rate" => $invoice['meta']['cgst_tax_rate'],
                        "sgst_tax_rate" => $invoice['meta']['sgst_tax_rate'],
                        "igst_tax_rate" => $invoice['meta']['igst_tax_rate'],
                        "igst_tax_amount" => $invoice['meta']["igst_total_tax_amount"],
                        "sgst_tax_amount" => $invoice['meta']["sgst_total_tax_amount"],
                        "cgst_tax_amount" => $invoice['meta']["cgst_total_tax_amount"],
                        "invoice_file_pdf" => $convert['copy_file']
                    );
                    
                    $this->invoices_model->action_partner_invoice($invoice_details);
                    log_message('info', __FUNCTION__ . " Reset Invoice Id " . $invoice['meta']['invoice_id']);
                    $this->inventory_model->update_brackets(array('invoice_id' => NULL), array('invoice_id' => $invoice['meta']['invoice_id']));
              
                    $var = explode(",", $invoice['meta']['order_id']);
                    foreach ($var as $value) {
                        log_message('info', __FUNCTION__ . " Update invoice id for bracket invoice id " . $invoice['meta']['invoice_id'] . " Order Id " . $value);
                        $this->inventory_model->update_brackets(array('invoice_id' => $invoice['meta']['invoice_id']), array('order_id' => $value));
                    }
                    $cp_file = TMP_FOLDER."copy_".$invoice['meta']['invoice_id'].".xlsx";
                    array_push($files, $cp_file);
 
                } else {
                    $output_file_excel = TMP_FOLDER . $invoice['meta']['invoice_id'] . "-draft.xlsx";
                    array_push($files, $output_file_excel);
                    $this->download_invoice_files($invoice['meta']['invoice_id'], $output_file_excel, $output_pdf_file_name);
                   
                }
                
                //Delete XLS files now
                foreach ($files as $file_name) {
                    exec("rm -rf " . escapeshellarg($file_name));
                }

                return true;
                
            } else {
                log_message('info', __FUNCTION__ . ' Invoice File is not created. invoice id' . $invoice['meta']['invoice_id']);
                echo ' Invoice File is not created. invoice id'.PHP_EOL;
                return false;
            }

        } else {
            log_message('info', __FUNCTION__ . ' Data Not Found');
            echo "Data Not found".PHP_EOL;
            return FALSE;
        }
    }

    /**
     * @Desc: This function is used to send mail to vendor brackets invoice 
     * @parmas: Vendor id, bracket_invoice file path
     * @return: boolean
     */
    function send_brackets_invoice_mail($vendor_id, $output_file_excel, $get_invoice_month) {
        log_message('info', __FUNCTION__ . " Entering....");
        $invoice_month = date('F', strtotime($get_invoice_month));

        $vendor_data = $this->vendor_model->getVendorContact($vendor_id);
        $rm_details = $this->vendor_model->get_rm_sf_relation_by_sf_id($vendor_id);
        $cc = ANUJ_EMAIL_ID.", ".ACCOUNTANT_EMAILID;
        if (!empty($rm_details)) {
           $cc = ANUJ_EMAIL_ID.", ".ACCOUNTANT_EMAILID . ", " . $rm_details[0]['official_email'];
        }
        
        //get email template from database
        $email_template = $this->booking_model->get_booking_email_template(BRACKETS_INVOICE_EMAIL_TAG);
        $subject = vsprintf($email_template[4], array($vendor_data[0]['name']));
        $message = vsprintf($email_template[0], array($invoice_month));
        $email_from = $email_template[2];
        $to = $vendor_data[0]['primary_contact_email'] . ',' . $vendor_data[0]['owner_email'];
        
        $send_mail = $this->notify->sendEmail($email_from, $to, $cc, '', $subject, $message, $output_file_excel,BRACKETS_INVOICE_EMAIL_TAG);

        if ($send_mail) {
            log_message('info', __FUNCTION__ . "Bracket invoice sent...");
            return TRUE;
        } else {
            log_message('info', __FUNCTION__ . "Bracket invoice not sent...");
            return FALSE;
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
    function create_partner_invoice($partner_id, $from_date, $to_date, $invoice_type, $agent_id) {
        log_message('info', __FUNCTION__ . ' Entering....... Partner_id:'.$partner_id.' invoice_type:'.$invoice_type.' from_date: '.$from_date.' to_date: '.$to_date);
        $invoices = $this->invoices_model->generate_partner_invoice($partner_id, $from_date, $to_date);
        if (!empty($invoices)) {

            $invoices['meta']['invoice_id'] = $this->create_invoice_id_to_insert("Around");
            //Send Push Notification
            $receiverArray['partner'] = array($partner_id);
            $notificationTextArray['msg'] = array($invoices['meta']['invoice_id'],abs($invoices['meta']['sub_total_amount']));
            $this->push_notification_lib->create_and_send_push_notiifcation(INVOICE_CREATED_PARTNER,$receiverArray,$notificationTextArray);
            //End Push Notification
            $hsn_code = $invoices['booking'][0]['hsn_code'];
            log_message('info', __FUNCTION__ . ' Invoice id ' . $invoices['meta']['invoice_id']);
            
            $status =$this->invoice_lib->send_request_to_create_main_excel($invoices, $invoice_type);
            
            if($status){
                
                log_message('info', __FUNCTION__ . ' Invoice File is created. invoice id' . $invoices['meta']['invoice_id']);
               
                unset($invoices['booking']);
                $this->create_partner_invoices_detailed($partner_id, $from_date, $to_date, $invoice_type, $invoices,$agent_id, $hsn_code);
                return true;
                
            } else {
                log_message('info', __FUNCTION__ . ' Invoice File is not created. invoice id' . $invoices['meta']['invoice_id']);
                echo ' Invoice File is not created. invoice id'.PHP_EOL;
                return false;
            }

        } else {
            log_message('info', __FUNCTION__ . ' Data Not Found');
            echo "Data Not found".PHP_EOL;
            return FALSE;
        }
    }
    
    /**
     * @desc: This method is used to generate Main Cash Invoice
     * @param type $details
     * @return string
     */
    function generate_vendor_cash_invoice($details, $agent_id, $is_regenerate) {
        log_message('info', __FUNCTION__ . " Entering...." . print_r($details, true) . ' is_regenerate: ' . $is_regenerate);

        $vendor_id = $details['vendor_partner_id'];
        $custom_date = explode("-", $details['date_range']);
        $from_date = $custom_date[0];
        $to_date = $custom_date[1];
        $invoice_type = $details['invoice_type'];
        $invoices = $this->invoices_model->get_vendor_cash_invoice($vendor_id, $from_date, $to_date, $is_regenerate);

        if (!empty($invoices)) {

            log_message('info', __FUNCTION__ . "=> Data Found for Cash Invoice");
            echo "Data Found for Cash Invoice" . PHP_EOL;

            if (isset($details['invoice_id'])) {
                log_message('info', __FUNCTION__ . " Re-Generate Cash Invoice ID: " . $details['invoice_id']);
                echo "Re-Generate Cash Invoice ID: " . $details['invoice_id'] . PHP_EOL;

                $invoices['meta']['invoice_id'] = $details['invoice_id'];
            } else {
                $invoices['meta']['invoice_id'] = $this->create_invoice_id_to_insert("Around");

                log_message('info', __FUNCTION__ . " New Invoice ID Generated: " . $invoices['meta']['invoice_id']);
                echo " New Invoice ID Generated: " . $invoices['meta']['invoice_id'] . PHP_EOL;
            }

            $status = $this->invoice_lib->send_request_to_create_main_excel($invoices, $invoice_type);
            if ($status) {

                log_message('info', __FUNCTION__ . ' Invoice File is created. invoice id' . $invoices['meta']['invoice_id']);

                unset($invoices['booking']);
                $data = $this->invoices_model->get_vendor_cash_detailed($vendor_id, $from_date, $to_date, $is_regenerate);
                $invoices_details_data = array_merge($data, $invoices['upcountry']);
                $invoices['meta']['r_sc'] = $invoices['meta']['r_asc'] = $invoices['meta']['r_pc'] = $rating = $total_amount_paid = 0;
                $i = 0;
                $parts_count = 0;
                foreach ($invoices_details_data as $value) {
                    $invoices['meta']['r_sc'] += $value['service_charges'];
                    $invoices['meta']['r_asc'] += $value['additional_charges'];
                    $invoices['meta']['r_pc'] += $value['parts_cost'];
                    if(isset($value['product_or_services'])){
                        if($value['product_or_services'] == "Product"){
                            $parts_count++;
                        }
                    }
                   
                    $total_amount_paid += $value['amount_paid'];

                    if (!is_null($value['rating_stars']) || $value['rating_stars'] != '') {
                        $rating += $value['rating_stars'];
                        $i++;
                    }
                }

                if ($i == 0) {
                    $i = 1;
                }
                $invoices['meta']['parts_count'] = $parts_count;
                $invoices['meta']['total_amount_paid'] = round($total_amount_paid, 0);
                $invoices['meta']['t_rating'] = round($rating / $i, 0);
                $this->generate_cash_details_invoices_for_vendors($vendor_id, $invoices_details_data, $invoices['meta'], $invoice_type, $agent_id, $from_date, $to_date);
                unset($invoices_details_data);
                unset($invoices['meta']);
                return true;
            } else {
                log_message('info', __FUNCTION__ . ' Invoice File is not created. invoice id' . $invoices['meta']['invoice_id']);
                echo ' Invoice File is not created. invoice id' . PHP_EOL;
                return false;
            }
        } else {
            log_message('info', __FUNCTION__ . "=> Data Not Found for Cash Invoice" . print_r($details));

            echo "Data Not Found for Cash Invoice" . PHP_EOL;

            return FALSE;
        }
    }
    
    function generate_buyback_invoices($details, $is_regenerate) {
        log_message('info', __FUNCTION__ . " Entering...." . print_r($details, true) . ' is_regenerate: ' . $is_regenerate);
        $vendor_id = $details['vendor_partner_id'];
        $custom_date = explode("-", $details['date_range']);
        $from_date = $custom_date[0];
        $to_date = $custom_date[1];
        $invoice_type = $details['invoice_type'];
        $owner_email = "";
        $primary_contact_email = "";
        $company_name ="";
        $sd = $ed ="";
            
        $response = array();
        for ($a = 0; $a < 2; $a++) {
            //$a 1 means For profit
            //$a 0 means for Loss
            $invoices = $this->invoices_model->get_buyback_invoice_data($vendor_id, $from_date, $to_date, $is_regenerate, $a);
            if ($invoices) {
                if (isset($details['invoice_id'])) {
                    log_message('info', __FUNCTION__ . " Re-Generate Cash Invoice ID: " . $details['invoice_id']);
                    echo "Re-Generate Cash Invoice ID: " . $details['invoice_id'] . PHP_EOL;

                    $invoices['meta']['invoice_id'] = $details['invoice_id'];
                } else {
                    if($invoice_type != 'final'){
                        if(empty($response)){
                        
                        $invoices['meta']['invoice_id'] = $this->create_invoice_id_to_insert("Around");
                        
                        } else if(isset($response[0]['invoice_id'])){
                            $temp = $this->invoice_lib->_get_partial_invoice_id("Around");
                            $explode = explode($temp, $response[0]['invoice_id']);
                            $invoices['meta']['invoice_id'] = trim($temp . sprintf("%'.04d\n", ($explode[1] + 1)));
                        }
                    } else {
                       $invoices['meta']['invoice_id'] = $this->create_invoice_id_to_insert("Around");
                    }

                    log_message('info', __FUNCTION__ . " New Invoice ID Generated: " . $invoices['meta']['invoice_id']);
                    echo " New Invoice ID Generated: " . $invoices['meta']['invoice_id'] . PHP_EOL;
                }

                $status = $this->invoice_lib->send_request_to_create_main_excel($invoices, $invoice_type);
                if ($status) {

                    log_message('info', __FUNCTION__ . ' Invoice File is created. invoice id' . $invoices['meta']['invoice_id']);
                    $res = $this->generate_buyback_detailed_invoices($vendor_id, $invoices['annexure_data'], $invoices['meta'], $invoice_type, $details['agent_id'], $from_date, $to_date);
                    if(!empty($res)){
                        $owner_email = $invoices['meta']['owner_email'];
                        $primary_contact_email = $invoices['meta']['primary_contact_email'];
                        $company_name = $invoices['meta']['company_name'];
                        $sd = $invoices['meta']['sd'];
                        $ed = $invoices['meta']['ed'];
                        array_push($response, $res);
                    }
                    
                   
                } else {

                    log_message('info', __FUNCTION__ . ' Invoice File is not created. invoice id' . $invoices['meta']['invoice_id']);
                    echo ' Invoice File is not created. invoice id' . $invoices['meta']['invoice_id'] . PHP_EOL;
                   
                }
            } else {
                log_message('info', __FUNCTION__ . "=> Data Not Found for Cash Invoice" . print_r($details));

                echo "Data Not Found for Cash Invoice" . PHP_EOL;

               
            }
        }
        
        if(!empty($response)){
            if($invoice_type == "final"){
                $rm_details = $this->vendor_model->get_rm_sf_relation_by_sf_id($vendor_id);
                $rem_email_id = "";
                if (!empty($rm_details)) {
                    $rem_email_id = ", " . $rm_details[0]['official_email'];
                }
                $to = $owner_email. ", " . $primary_contact_email;
                
                $cc = ANUJ_EMAIL_ID . $rem_email_id.",".ACCOUNTANT_EMAILID;
                

                //get email template from database
                $email_template = $this->booking_model->get_booking_email_template(BUYBACK_DETAILS_INVOICE_FOR_VENDORS_EMAIL_TAG);
                $subject = vsprintf($email_template[4], array($company_name,$sd,$ed));
                $message = $email_template[0];
                $email_from = $email_template[2];

                $this->send_email_with_invoice($email_from, $to, $cc, $message, $subject, "", "", BUYBACK_DETAILS_INVOICE_FOR_VENDORS_EMAIL_TAG,$response);
                
            
            } else {
                   $buyback_invoice_id ="";
                   $str = "";
                    foreach($response as $value){
                        
                        $buyback_invoice_id .= $value['invoice_id']."-";
                            if (explode('.', $value['pdf'])[1] === 'pdf') {
                        $output_file_pdf = TMP_FOLDER . $value['invoice_id'] . '-draft.pdf';

                        $cmd = "curl https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/invoices-excel/" . $value['pdf'] . " -o " . $output_file_pdf;
                        exec($cmd);

                        $str .= ' ' . TMP_FOLDER . $value['invoice_id']  . '.zip ' . TMP_FOLDER . $value['invoice_id']  . '-draft.xlsx' . ' ' . TMP_FOLDER . $value['invoice_id']  . '-draft.pdf'
                                . ' ' . $value['excel'];
                    } else {
                        $str .= " ".TMP_FOLDER . $value['invoice_id']  . '-draft.xlsx' . ' ' .  $value['excel'];
                    }
                }
                
                    system('zip '. TMP_FOLDER.$buyback_invoice_id.".zip ". $str);
                    
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header("Content-Disposition: attachment; filename=\"$buyback_invoice_id.zip\"");
                    readfile(TMP_FOLDER . $buyback_invoice_id. '.zip');
                    $res1 = 0;
                    system(" chmod 777 " . TMP_FOLDER . $buyback_invoice_id . '.zip ', $res1);
                    exec("rm -rf " . escapeshellarg(TMP_FOLDER . $buyback_invoice_id . '.zip'));
                    foreach ($response as $value1) {
                        exec("rm -rf " . escapeshellarg(TMP_FOLDER . "copy_" . $value1['invoice_id'] . "-draft.xlsx"));
                        exec("rm -rf " . escapeshellarg(TMP_FOLDER . $value1['invoice_id'] . '-draft.pdf'));
                        exec("rm -rf " . escapeshellarg(TMP_FOLDER . $value1['invoice_id'] . '-detailed.xlsx'));
                        exec("rm -rf " . escapeshellarg(TMP_FOLDER . $value1['invoice_id'] . '-draft.xlsx'));
                    }
            }
            foreach ($response as $file) {
                foreach($file['files'] as $files){
                    exec("rm -rf " . escapeshellarg($files));
                }
            }

            return true;
        } else {
            return false;
        }
    }

    function generate_buyback_detailed_invoices($vendor_id, $data, $meta, $invoice_type, $agent_id,$from_date, $to_date){
        log_message('info', __FUNCTION__ . " Entering...." );
        $files = array();

        $template = 'Buyback-Annexure-v1.xlsx';
        $output_file_excel = TMP_FOLDER . $meta['invoice_id'] . "-detailed.xlsx";
        $this->invoice_lib->generate_invoice_excel($template, $meta, $data, $output_file_excel);
        array_push($files, $output_file_excel);
        
        $convert = $this->invoice_lib->send_request_to_convert_excel_to_pdf($meta['invoice_id'], $invoice_type); 
        $output_file_main = $convert['main_pdf_file_name'];
        array_push($files, TMP_FOLDER.$convert['excel_file']);

        log_message('info', 'Excel data: ' . print_r($meta, true));
         if ($invoice_type === "final") {
            $out['pdf'] = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/invoices-excel/".$output_file_main;
            $out['excel'] = $output_file_excel;
            $out['invoice_id'] = $meta['invoice_id'];
            
            //Send SMS to PoC/Owner
            $this->send_invoice_sms("Buyback",  $meta['sd'], $meta['sub_total_amount'], $meta['owner_phone_1'], $vendor_id);
            
           //Upload Excel files to AWS
            $this->upload_invoice_to_S3($meta['invoice_id']);
            $gst_amount = (array_sum(array_column($data, 'gst_amount')));

            //Save this invoice info in table
            $invoice_details = array(
                'invoice_id' => $meta['invoice_id'],
                'type' => 'Buyback',
                'type_code' => 'A',
                'vendor_partner' => 'vendor',
                'vendor_partner_id' => $vendor_id,
                'invoice_file_main' => $output_file_main,
                'invoice_file_excel' => $meta['invoice_id'] . '.xlsx',
                'invoice_detailed_excel' => $meta['invoice_id'] . '-detailed.xlsx',
                'invoice_date' => date("Y-m-d"),
                'from_date' => date("Y-m-d", strtotime($from_date)),
                'to_date' => date("Y-m-d", strtotime($to_date)),
                'parts_count' =>  $meta['total_qty'],
                'parts_cost' => $meta['sub_total_amount'],
                'total_amount_collected' => $meta['sub_total_amount'],
                'around_royalty' => $meta['sub_total_amount'],
                'buyback_tax_amount' => $gst_amount,
                'invoice_date' => date('Y-m-d'),
                //Amount needs to be collected from Vendor
                'amount_collected_paid' =>$meta['sub_total_amount'],
                //Mail has not 
                'mail_sent' => 1,
                //SMS has been sent or not
                'sms_sent' => 1,
                //Add 1 month to end date to calculate due date
                'due_date' => date("Y-m-d", strtotime($to_date)),
                //add agent_id
                'agent_id' => $agent_id,
                "invoice_file_pdf" => $convert['copy_file']
            );

            $this->invoices_model->action_partner_invoice($invoice_details);
            exec("rm -rf " . escapeshellarg(TMP_FOLDER . "copy_" . $meta['invoice_id'] . ".xlsx"));
            log_message('info', __METHOD__ . ': Invoice ' . $meta['invoice_id'] . ' details  entered into invoices table');


            $this->update_invoice_id_in_buyback($data, $meta['invoice_id'], $invoice_type, "cp_invoice_id");
            
        } else {
            $out['invoice_id'] = $meta['invoice_id'];
            $out['excel'] = $output_file_excel;
            $out['pdf'] = $output_file_main;
            //$this->download_invoice_files($meta['invoice_id'], $output_file_excel, $output_file_main);
        }
        $out['files'] = $files;
        //Do not Delete XLS files now
//        foreach ($files as $file_name) {
//            exec("rm -rf " . escapeshellarg($file_name));
//        }
        unset($meta);
        unset($invoice_details);
        return $out;

    }

    /**
     * @desc: This method is used to generate vendor Foc invoice and return invoice id
     * @param Array $details
     */
    function generate_vendor_foc_invoice($details, $is_regenerate) {
        log_message('info', __FUNCTION__ . "Entering..." . print_r($details, true) . ' is_regenerate: ' . $is_regenerate);
        $vendor_id = $details['vendor_partner_id'];
        $custom_date = explode("-", $details['date_range']);
        $from_date = $custom_date[0];
        $to_date = $custom_date[1];
        $invoice_type = $details['invoice_type'];
        $invoices = $this->invoices_model->get_vendor_foc_invoice($vendor_id, $from_date, $to_date, $is_regenerate);

        if (!empty($invoices['booking'])) {
            if ($invoices['meta']['sub_total_amount'] > 0) {

                if (isset($details['invoice_id'])) {
                    log_message('info', __METHOD__ . ": Invoice Id re- geneterated " . $details['invoice_id']);
                    $invoices['meta']['invoice_id'] = $details['invoice_id'];
                } else {
                    $invoices['meta']['invoice_id'] = $this->create_invoice_id_to_insert($invoices['meta']['sc_code']);

                    log_message('info', __METHOD__ . ": Invoice Id geneterated "
                            . $invoices['meta']['invoice_id']);
                }

                $status = $this->invoice_lib->send_request_to_create_main_excel($invoices, $invoice_type);
                if ($status) {
                    log_message('info', __FUNCTION__ . ' Invoice File is created. invoice id' . $invoices['meta']['invoice_id']);


                    $in_detailed = $this->invoices_model->generate_vendor_foc_detailed_invoices($vendor_id, $from_date, $to_date, $is_regenerate);
                    return $this->generate_foc_details_invoices_for_vendors($in_detailed, $invoices, $vendor_id, $invoice_type, $details['agent_id'], $from_date,$to_date );
                } else {
                    log_message('info', __FUNCTION__ . ' Invoice File did not create. invoice id' . $invoices['meta']['invoice_id']);
                    return FALSE;
                }
            } else {
                //Negative Amount Invoice
                if($invoice_type == "final"){
                    $email_template = $this->booking_model->get_booking_email_template(NEGATIVE_FOC_INVOICE_FOR_VENDORS_EMAIL_TAG);
                    $subject = vsprintf($email_template[4], array($invoices['meta']['company_name'],$invoices['meta']['sd'],$invoices['meta']['ed']));
                    $message = $email_template[0];
                    $email_from = $email_template[2];
                    $to = $invoices['meta']['owner_email'] . ", " . $invoices['meta']['primary_contact_email'];
                    $rm_details = $this->vendor_model->get_rm_sf_relation_by_sf_id($vendor_id);
                    $rem_email_id = "";
                    if (!empty($rm_details)) {
                        $rem_email_id = ", " . $rm_details[0]['official_email'];
                    }
                    $cc = ANUJ_EMAIL_ID.", ".ACCOUNTANT_EMAILID . $rem_email_id;
                    echo "Negative Invoice - ".$vendor_id. " Amount ".$invoices['meta']['sub_total_amount'].PHP_EOL;
                    log_message('info', __FUNCTION__ . "Negative Invoice - ".$vendor_id. " Amount ".$invoices['meta']['sub_total_amount']);

                    $this->send_email_with_invoice($email_from, $to, $cc, $message, $subject, "", "",NEGATIVE_FOC_INVOICE_FOR_VENDORS_EMAIL_TAG);
                }
                
              
                return false;
            }
        } else {

            echo "Data Not Found - ".$vendor_id.PHP_EOL;
            log_message('info', __FUNCTION__ . " Data Not Found -". $vendor_id);
            return false;
        }
    }

    /**
     * @desc: This is used to load invoice insert/update form
     * @param Sting $vendor_partner
     * @param String $invoice_id
     */
    function insert_update_invoice($vendor_partner, $invoice_id = FALSE) {
         $this->checkUserSession();
        log_message('info', __FUNCTION__ . " Entering.... Invoice_id: " . $invoice_id. ' vendor_partner: '.$vendor_partner);
        if ($invoice_id) {
            $where = array('invoice_id' => $invoice_id);
            //Get Invocie details from Vendor Partner Invoice Table
            $invoice_details['invoice_details'] = $this->invoices_model->get_invoices_details($where);
        }
        $invoice_details['vendor_partner'] = $vendor_partner;
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/insert_update_invoice', $invoice_details);
    }

    /**
     * @desc: Update/ Insert Partner Invoice Details from panel
     * @param String $vendor_partner
     */
    function process_insert_update_invoice($vendor_partner) {
         $this->checkUserSession();
        log_message('info', __FUNCTION__ . " Entering...." . $vendor_partner);
        $this->form_validation->set_rules('vendor_partner_id', 'Vendor Partner', 'required|trim|xss_clean');
        $this->form_validation->set_rules('invoice_id', 'Invoice ID', 'required|trim|xss_clean');
        $this->form_validation->set_rules('around_type', 'Around Type', 'required|trim|xss_clean');
        $this->form_validation->set_rules('gst_rate', 'GST Rate', 'required|trim|xss_clean');
        $this->form_validation->set_rules('from_date', 'Invoice Period', 'required|trim|xss_clean');
        $this->form_validation->set_rules('type', 'Type', 'required|trim|xss_clean');
        if ($this->form_validation->run()) {
            $data = $this->get_create_update_invoice_input($vendor_partner);
            $total_amount_collected = ($data['total_service_charge'] +
                    $data['total_additional_service_charge'] +
                    $data['parts_cost'] + $data['courier_charges'] + $data['upcountry_price'] + $data['credit_penalty_amount'] - $data['penalty_amount']);
            

            $entity_details = array();
            $gst_number = "";

            if ($data['vendor_partner'] == "vendor") {
                $entity_details = $this->vendor_model->viewvendor($data['vendor_partner_id']);
                $gst_number = $entity_details[0]['gst_no'];
            } else {

                $entity_details = $this->partner_model->getpartner_details("gst_number, state", array('partners.id' => $data['vendor_partner_id']));
                $gst_number = $entity_details[0]['gst_number'];
                if(empty($gst_number)){
                    
                    $gst_number = TRUE;
                }
            }
            
            if(empty($gst_number)){
                $gst_rate = 0;
            } else {
                $gst_rate = $this->input->post('gst_rate');
            }
            
            
            $gst_amount = $total_amount_collected * ($gst_rate / 100);
            $data['total_amount_collected'] = round(($total_amount_collected + $gst_amount), 0);

            $data['rcm'] = 0;

            $c_s_gst = $this->invoices_model->check_gst_tax_type($entity_details[0]['state']);
            if ($c_s_gst) {

                $data['cgst_tax_amount'] = $data['sgst_tax_amount'] = $gst_amount / 2;
                $data['cgst_tax_rate'] = $data['sgst_tax_rate'] = $gst_rate / 2;
            } else {

                $data['igst_tax_amount'] = $gst_amount;
                $data['igst_tax_rate'] = $gst_rate;
            }


            switch ($data['type_code']) {
                case 'A':
                    log_message('info', __FUNCTION__ . " .. type code:- " . $data['type']);
                    $data['around_royalty'] = round($data['total_amount_collected'], 0);
                    $data['amount_collected_paid'] = round($data['total_amount_collected'], 0);

                    break;
                case 'B':
                    log_message('info', __FUNCTION__ . " .. type code:- " . $data['type']);

                    $tds['tds'] = 0;
                    $tds['tds_rate'] = 0;
                    if ($data['type'] == 'FOC') {

                        if ($vendor_partner == "vendor") {
                            $tds = $this->check_tds_sc($entity_details[0], ($total_amount_collected - $data['parts_cost']));
                            if (empty($gst_number)) {

                                $data['cgst_tax_amount'] = $data['sgst_tax_amount'] = $data['sgst_tax_rate'] = $data['cgst_tax_rate'] = 0;
                                $data['igst_tax_amount'] = 0;
                                $data['igst_tax_rate'] = 0;
                               // $data['rcm'] = $total_amount_collected * ($this->input->post('gst_rate') / 100);
                            } 
                        } else {
                            $tds['tds'] = 0;
                            $tds['tds_rate'] = 0;
                        }
                    } else if ($data['type'] == 'CreditNote' || $data['type'] == 'Buyback' || $data['type'] == 'Stand' || $data['type'] == "Parts") {

                        $tds['tds'] = 0;
                        $tds['tds_rate'] = 0;
                    }

                    $data['around_royalty'] = 0;
                    $data['amount_collected_paid'] = -($data['total_amount_collected'] - $tds['tds'] - $data['rcm']);
                    $data['tds_amount'] = $tds['tds'];
                    $data['tds_rate'] = $tds['tds_rate'];
                    break;
            }

            $file = $this->upload_create_update_invoice_to_s3($data['invoice_id']);
            if (isset($file['invoice_file_main'])) {
                $data['invoice_file_main'] = $file['invoice_file_main'];
            }
            if (isset($file['invoice_detailed_excel'])) {
                $data['invoice_detailed_excel'] = $file['invoice_detailed_excel'];
            }
            if (isset($file['invoice_file_excel'])) {
                $data['invoice_file_excel'] = $file['invoice_file_excel'];
            }
            $data['agent_id'] = $this->session->userdata("id");
            $status = $this->invoices_model->action_partner_invoice($data);

            if ($status) {
                log_message('info', __METHOD__ . ' Invoice details inserted ' . $data['invoice_id']);
            } else {

                log_message('info', __METHOD__ . ' Invoice details not inserted ' . $data['invoice_id']);
            }

            redirect(base_url() . 'employee/invoice/invoice_summary/' . $data['vendor_partner'] . "/" . $data['vendor_partner_id']);
        } else {
            $this->insert_update_invoice($vendor_partner);
        }
    }

    function upload_create_update_invoice_to_s3($invoice_id_tmp) {
        $bucket = BITBUCKET_DIRECTORY;
        $invoice_id_tmp_1 = str_replace("/","",$invoice_id_tmp);
        $invoice_id = str_replace("_","",$invoice_id_tmp_1);
        $data = array();
        if (!empty($_FILES["invoice_file_main"]['tmp_name'])) {
            $temp = explode(".", $_FILES["invoice_file_main"]["name"]);
            $extension = end($temp);
            // Uploading to S3
           
            $directory = "invoices-excel/" . $invoice_id . "." . $extension;
            $is_s3 = $this->s3->putObjectFile($_FILES["invoice_file_main"]["tmp_name"], $bucket, $directory, S3::ACL_PUBLIC_READ);

            if ($is_s3) {
                log_message('info', __FUNCTION__ . " Main Invoice upload");
                $data['invoice_file_main'] = $invoice_id . "." . $extension;
               
            } else {
                log_message('info', __FUNCTION__ . " Main Invoice upload failed");
            }
        }
        if (!empty($_FILES["invoice_detailed_excel"]['tmp_name'])) {
            $temp1 = explode(".", $_FILES["invoice_detailed_excel"]["name"]);
            $extension1 = end($temp1);
            // Uploading to S3
            
            $directory = "invoices-excel/" . $invoice_id . "-detailed." . $extension1;
            $is_s3 = $this->s3->putObjectFile($_FILES["invoice_detailed_excel"]["tmp_name"], $bucket, $directory, S3::ACL_PUBLIC_READ);

            if ($is_s3) {
                log_message('info', __FUNCTION__ . " Main Invoice upload");
                $data['invoice_detailed_excel'] = $invoice_id . "-detailed." . $extension1;
            } else {
                log_message('info', __FUNCTION__ . " Main Invoice upload failed");
            }
        }
        
         if (!empty($_FILES["invoice_file_excel"]['tmp_name'])) {
            $temp1 = explode(".", $_FILES["invoice_file_excel"]["name"]);
            $extension1 = end($temp1);
            // Uploading to S3
            $directory = "invoices-excel/" . $invoice_id . "." . $extension1;
            $is_s3 = $this->s3->putObjectFile($_FILES["invoice_file_excel"]["tmp_name"], $bucket, $directory, S3::ACL_PUBLIC_READ);

            if ($is_s3) {
                log_message('info', __FUNCTION__ . " Main Excel Invoice upload");
                $data['invoice_file_excel'] = $invoice_id . "." . $extension1;
            } else {
                log_message('info', __FUNCTION__ . " Main Excel Invoice upload failed");
            }
        }
        
        return $data;
    }

    function get_create_update_invoice_input($vendor_partner) {
        $invoice_id_tmp = $this->input->post('invoice_id');
        $invoice_id_tmp_1 = str_replace("/","-",$invoice_id_tmp); 
        $invoice_id = str_replace("_","-",$invoice_id_tmp_1);
        $data['invoice_id'] = $invoice_id;
        $data['type'] = $this->input->post('type');
        $data['vendor_partner'] = $vendor_partner;
        $data['vendor_partner_id'] = $this->input->post('vendor_partner_id');
        $date_range = $this->input->post('from_date');
        $date_explode = explode("-", $date_range);
        $data['from_date'] = trim($date_explode[0]);
        $data['to_date'] = trim($date_explode[1]);
        $data['num_bookings'] = $this->input->post('num_bookings');
        $data['parts_count'] = $this->input->post('parts_count');
        $data['hsn_code'] = $this->input->post('hsn_code');
        $data['total_service_charge'] = $this->input->post('total_service_charge');
        $data['total_additional_service_charge'] = $this->input->post('total_additional_service_charge');
        $data['parts_cost'] = $this->input->post('parts_cost');
       
        $data['penalty_amount'] = $this->input->post("penalty_amount");
        $data['credit_penalty_amount'] = $this->input->post("credit_penalty_amount");
        $data['penalty_bookings_count'] = $this->input->post("penalty_bookings_count");
        $data['credit_penalty_bookings_count'] = $this->input->post("credit_penalty_bookings_count");
        $data['upcountry_booking'] = $this->input->post("upcountry_booking");
        $data['upcountry_distance'] = $this->input->post("upcountry_distance");
        $data['courier_charges'] = $this->input->post("courier_charges");
        $data['upcountry_price'] = $this->input->post("upcountry_price");
        $data['remarks'] = $this->input->post("remarks");
        $data['due_date'] = date('Y-m-d', strtotime($this->input->post('due_date')));
        $data['invoice_date'] = date('Y-m-d', strtotime($this->input->post('invoice_date')));
        $data['type_code'] = $this->input->post('around_type');
        
       
        return $data;
    }

    /**
     * @desc: Calculate TDS Amount 
     * @param Array $sc_details
     * @param String $total_sc_details
     * @return String tds amount
     */
    function check_tds_sc($sc_details, $total_sc_details) {
        log_message('info', __FUNCTION__ . " Entering....");
        $tds = 0;
        $tds_per_rate = 0;
        if (empty($sc_details['pan_no'])) {
            $tds = ($total_sc_details) * .20;
            $tds_tax_rate = 20;
            $tds_per_rate = "20%";
        } else {
            switch ($sc_details['company_type']) {
                case 'Proprietorship Firm':
                        $_4th_char = substr($sc_details['pan_no'], 3, 1);
                        if (strcasecmp($_4th_char, "F") == 0) {
                            $tds = ($total_sc_details) * .02;
                            $tds_tax_rate = 2;
                            $tds_per_rate = "2%";
                        } else {
                            $tds = ($total_sc_details) * .01;
                            $tds_tax_rate = 1;
                            $tds_per_rate = "1%";
                        }
                    
                    break;
                case "Individual":
                    $tds = ($total_sc_details) * .01;
                    $tds_tax_rate = 1;
                    $tds_per_rate = "1%";
                    break;

                case "Partnership Firm":
                case "Company (Pvt Ltd)":
                case "Private Ltd Company":
                    $tds = ($total_sc_details) * .02;
                    $tds_tax_rate = 2;
                    $tds_per_rate = "2%";
                    break;
                default :
                    $tds = ($total_sc_details) * .02;
                    $tds_tax_rate = 2;
                    $tds_per_rate = "2%";
                    break;
            }
        }
        $data['tds'] = $tds;
        $data['tds_rate'] = $tds_tax_rate;
        $data['tds_per_rate'] = $tds_per_rate;
        log_message('info', __FUNCTION__ . " Exit....");
        return $data;
    }

    /**
     * @desc: Generate Invoice ID
     * @param type $entity_details
     * @param type $from_date
     * @param type $start_name
     * @return invoice id
     */
    function create_invoice_id_to_insert($start_name) {
        log_message('info', __FUNCTION__ . " Entering....");
      
        return $this->invoice_lib->create_invoice_id($start_name);
  
    }
    

    /**
     * @desc: Send Emailt to SF with attach invoice file while create a new invoice from Form 
     * @param type $vendor_detail
     * @param type $type
     * @param type $start_date
     * @param type $end_date
     * @param type $main_invoice_file
     * @param type $detailed_invoice_file
     * @return boolean
     */
    function send_attach_email_to_sf($vendor_detail, $type, $start_date, $end_date, $main_invoice_file, $detailed_invoice_file) {
        log_message('info', __FUNCTION__ . " Entering....");
        $to = $vendor_detail[0]['owner_email'] . ", " . $vendor_detail[0]['primary_contact_email'];

        $subject = "247around - " . $vendor_detail[0]['company_name'] . " - " . $type . "  Invoice for period: " . $start_date . " to " . $end_date;
        $cc = NITS_ANUJ_EMAIL_ID.", ".ACCOUNTANT_EMAILID;

        $this->email->from('billing@247around.com', '247around Team');
        $this->email->to($to);
        $this->email->cc($cc);
        $this->email->subject($subject);
        $this->email->attach("https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/invoices-excel/" . $main_invoice_file, 'attachment');
        if ($detailed_invoice_file != "") {
            $this->email->attach("https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/invoices-excel/" . $detailed_invoice_file, 'attachment');
        }
        $mail_ret = $this->email->send();

        if ($mail_ret) {

            log_message('info', __METHOD__ . ": Mail sent successfully");
            echo "Mail sent successfully..............." . PHP_EOL;
        } else {
            log_message('info', __METHOD__ . ": Mail could not be sent");
            echo "Mail could not be sent..............." . PHP_EOL;
        }
        log_message('info', __FUNCTION__ . " EXIT....");
        return true;
    }

    /**
     * @desc: This method is used to download payment summary invoice for selected service center
     */
    function download_invoice_summary() {
        log_message('info', __FUNCTION__ . " Entering....");
        $data = $this->input->post('amount_service_center');
        $defective_parts = $this->input->post("defective_parts");
        $defective_parts_max_age = $this->input->post('defective_parts_max_age');
        $payment_data = array();
                
        if (!empty($data)) {
            $sc_details['debit_acc_no'] = "Debit Ac No";
            $sc_details['bank_account'] = "Beneficiary Ac No";
            $sc_details['beneficiary_name'] = "Beneficiary Name";
            $sc_details['final_amount'] = "Amt";
            $sc_details['payment_mode'] = "Pay Mod";
            $sc_details['payment_date'] = "Date";
            $sc_details['ifsc_code'] = "IFSC";
            $sc_details['payable_location_name'] = "Payable Location name";
            $sc_details['print_location'] = "Print Location";
            $sc_details['bene_mobile_no'] = "Bene Mobile no";
            $sc_details['bene_email_id'] = "Bene email id";
            $sc_details['ben_add_1'] = "Ben add1";
            $sc_details['ben_add_2'] = "Ben add2";
            $sc_details['ben_add_3'] = "Ben add3";
            $sc_details['ben_add_4'] = "Ben add4";
            $sc_details['add_details_1'] = "Add details 1";
            $sc_details['add_details_2'] = "Add details 2";
            $sc_details['add_details_3'] = "Add details 3";
            $sc_details['add_details_4'] = "Add details 4";
            $sc_details['add_details_5'] = "Add details 5";
            $sc_details['remarks'] = "Remarks";
            $sc_details['gst_no'] = "GST Number";
            $sc_details['is_signature'] = "Signature Exist";
            $sc_details['defective_parts'] = "No Of Defective Parts";
            $sc_details['defective_parts_max_age'] = "Max Age of Spare Pending";
            $sc_details['is_verified'] = "Bank Account Verified";
            $sc_details['amount_type'] = "Type";
            $sc_details['sf_id'] = "SF/CP Id";
            $sc_details['is_sf'] = "SF";
            $sc_details['is_cp'] = "CP";
            $sc_details['check_file'] = "Check File";
            
            array_push($payment_data, $sc_details);
            foreach ($data as $service_center_id => $amount) {
                $sc = $this->vendor_model->viewvendor($service_center_id)[0];
               
                $sc_details['debit_acc_no'] = '102405500277';
                $sc_details['bank_account'] = trim($sc['bank_account']);
                $sc_details['beneficiary_name'] = trim($sc['beneficiary_name']);

                $sc_details['final_amount'] = abs(round($amount, 0));
                if (trim($sc['bank_name']) === ICICI_BANK_NAME) {
                    $sc_details['payment_mode'] = "I";
                } else {
                    $sc_details['payment_mode'] = "N";
                }

                $sc_details['payment_date'] = date("d-M-Y");
                $sc_details['ifsc_code'] = trim($sc['ifsc_code']);
                $sc_details['payable_location_name'] = "";
                $sc_details['print_location'] = "";
                $sc_details['bene_mobile_no'] = "";
                $sc_details['bene_email_id'] = "";
                $sc_details['ben_add_1'] = "";
                $sc_details['ben_add_2'] = "";
                $sc_details['ben_add_3'] = "";
                $sc_details['ben_add_4'] = "";
                $sc_details['add_details_1'] = "";
                $sc_details['add_details_2'] = "";
                $sc_details['add_details_3'] = "";
                $sc_details['add_details_4'] = "";
                $sc_details['add_details_5'] = "";
                $sc_details['remarks'] = preg_replace("/[^A-Za-z0-9]/", "", $sc['name']);
                $sc_details['gst_no'] = $sc['gst_no'];
                if(!empty($sc['signature_file'])){
                    $sc_details['is_signature'] = "Yes";
                } else {
                    $sc_details['is_signature'] = "NO";
                }
                $sc_details['defective_parts'] = $defective_parts[$service_center_id];
                $sc_details['defective_parts_max_age'] = $defective_parts_max_age[$service_center_id];
                $sc_details['is_verified'] = ($sc['is_verified'] ==0) ? "Not Verified" : "Verified";
                if ($amount > 0) {
                    $sc_details['amount_type'] = "CR";
                } else {
                    $sc_details['amount_type'] = "DR";
                }
                $sc_details['sf_id'] = $service_center_id;
                $sc_details['is_sf'] = $sc['is_sf'];
                $sc_details['is_cp'] = $sc['is_cp'];
                $sc_details['check_file'] = !empty($sc['cancelled_cheque_file']) ? "https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/".$sc['cancelled_cheque_file'] : "";
                array_push($payment_data, $sc_details);
            }

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=payment_upload_summary.csv');

            // create a file pointer connected to the output stream
            $output = fopen('php://output', 'w');

            foreach ($payment_data as $line) {
                fputcsv($output, $line);
            }
        }
    }

    /**
     * @desc: This method is used to fetch invoice id. It called by Ajax while 
     * invoice invoice details.
     * @param String $vendor_partner_id
     * @param String $vendor_partner_type
     * @param String $from_date
     * @param String $type_code
     */
    function fetch_invoice_id($vendor_partner_id, $vendor_partner_type, $type_code) {
        $entity_details = array();

        if (!empty($vendor_partner_id) && !empty($type_code)) {
            switch ($type_code) {

                case 'A':

                    echo $this->create_invoice_id_to_insert("Around");
                   
                    break;

                case 'B':
                    
                    if ($vendor_partner_type == "vendor") {
                        $entity_details = $this->vendor_model->viewvendor($vendor_partner_id);
                        echo $this->create_invoice_id_to_insert($entity_details[0]['sc_code']);
                       
                    } else {
                        echo $this->create_invoice_id_to_insert("Around");
                    }
                
                    
                    break;
            }
        } else {
            echo "DATA NOT FOUND";
        }
    }
    /**
     * @desc: This is used to Insert CRM SETUP/QC invoice invoice
     */
    function generate_crm_setup() {
         $this->checkUserSession();
        log_message('info', __FUNCTION__ . " Entering....");
        $this->form_validation->set_rules('partner_name', 'Partner Name', 'trim|xss_clean');
        $this->form_validation->set_rules('partner_id', 'Partner ID', 'required|trim|xss_clean');
        $this->form_validation->set_rules('daterange', 'Start Date', 'required|trim|xss_clean');
        $this->form_validation->set_rules('invoice_type', 'Invoice Type', 'required|trim|xss_clean');
        $this->form_validation->set_rules('service_charge', 'Service Charge', 'required|trim|xss_clean');
        if ($this->form_validation->run() == TRUE) {
          
            $date_range = $this->input->post('daterange');
            $custom_date = explode("-", $date_range);
            $from_date = $custom_date[0];
            $to_date = $custom_date[1];
            $partner_id = $this->input->post('partner_id');
            $amount = $this->input->post('service_charge');
            $description = $this->input->post('invoice_type');
            $partner_data = $this->partner_model->getpartner_details("partners.id, gst_number, "
                    . "state,address as company_address, "
                    . "company_name, pincode, "
                    . "district, invoice_email_to,invoice_email_cc", array('partners.id' => $partner_id));
          
            $hsn_code = HSN_CODE;
            $type = "Cash"; 
            $sd = date("Y-m-d", strtotime($from_date));
            $ed = date("Y-m-d", strtotime($to_date));
            $email_tag = CRM_SETUP_INVOICE_EMAIL_TAG;
           
            if($description == QC_INVOICE_DESCRIPTION){
                $hsn_code = QC_HSN_CODE;
                $type = "Buyback";
                $email_tag = SWEETENER_INVOCIE_EMAIL_TAG;
  
            }
            $invoice_date = date("Y-m-d");
            $invoice_id = $this->create_invoice_id_to_insert("Around");
            
            $response = $this->generate_partner_additional_invoice($partner_data[0], $description,
            $amount, $invoice_id, $sd, $ed, $invoice_date, $hsn_code, "Tax Invoice", $email_tag, 1, DEFAULT_TAX_RATE);
            $basic_sc_charge = $response['meta']['total_taxable_value'];
            $invoice_details = array(
                'invoice_id' => $invoice_id,
                'type_code' => 'A',
                'type' => $type,
                'vendor_partner' => 'partner',
                'vendor_partner_id' => $partner_id,
                'invoice_file_main' => $response['meta']['invoice_file_main'],
                'invoice_file_excel' => $response['meta']['invoice_id'] . ".xlsx",
                'from_date' => date("Y-m-d", strtotime($from_date)), //??? Check this next time, format should be YYYY-MM-DD
                'to_date' => date("Y-m-d", strtotime($to_date)),
                'total_service_charge' => $basic_sc_charge,
                'total_amount_collected' => $response['meta']['sub_total_amount'],
                'invoice_date' => date("Y-m-d", strtotime($invoice_date)),
                'around_royalty' => $response['meta']['sub_total_amount'],
                'due_date' => date("Y-m-d", strtotime($to_date)),
                //Amount needs to be collected from Vendor
                'amount_collected_paid' => $response['meta']['sub_total_amount'],
                //add agent_id
                'agent_id' => $this->session->userdata('id'),
                "cgst_tax_rate" => $response['meta']['cgst_tax_rate'],
                "sgst_tax_rate" => $response['meta']['sgst_tax_rate'],
                "igst_tax_rate" => $response['meta']['igst_tax_rate'],
                "igst_tax_amount" => $response['meta']["igst_total_tax_amount"],
                "sgst_tax_amount" => $response['meta']["sgst_total_tax_amount"],
                "cgst_tax_amount" => $response['meta']["cgst_total_tax_amount"],
                "hsn_code" => $hsn_code,
                "invoice_file_pdf" => $response['meta']['copy_file']
            );
            
             $this->invoices_model->insert_new_invoice($invoice_details);
             log_message('info', __METHOD__ . ": Invoice ID inserted");
             $this->session->set_flashdata('file_error', $description.'Invoice Generated');
             redirect(base_url() . "employee/invoice/invoice_partner_view");

        } else {
            log_message('info', __METHOD__ . ": Invoice ID inserted");
            $this->session->set_flashdata('file_error', $description. 'Not Generated');
            log_message('info', __METHOD__ . ": Validation Failed");
            $this->invoice_partner_view();
        }
    }

    /**
     * @desc Combined detailed and upcountry excell sheet in a Single sheet
     * @param String $details_excel
     * @param Array $files
     * @return String 
     */
    function combined_partner_invoice_sheet($details_excel, $files) {

        // Files are loaded to PHPExcel using the IOFactory load() method
        
        $objPHPExcel1 = PHPExcel_IOFactory::load($details_excel);
        foreach($files as $file_path){
            $objPHPExcel2 = PHPExcel_IOFactory::load($file_path);

            // Copy worksheets from $objPHPExcel2 to $objPHPExcel1
            foreach ($objPHPExcel2->getAllSheets() as $sheet) {
                $objPHPExcel1->addExternalSheet($sheet);
            }
            
            
        }
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel1, "Excel2007");
        // Save $objPHPExcel1 to browser as an .xls file
        $objWriter->save($details_excel);
        $res1 = 0;
        system(" chmod 777 " . $details_excel, $res1);
        return $details_excel;
    }

    /**
     * @desc This function adds new advance bank transactions between vendor/partner and 247around
     * @param String $vendor_partner
     * @param int $id
     */
    function get_advance_bank_transaction($vendor_partner = "", $id = "") {
         $this->checkUserSession();
        $data['vendor_partner'] = $vendor_partner;
        $data['id'] = $id;
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/advance_bank_transaction', $data);
    }

    /**
     * @desc Add new bank transaction
     */
    function process_advance_payment() {
        $this->checkUserSession();
        $agent_id = $this->session->userdata('id');
        $status = $this->_process_advance_payment($agent_id);
        if ($status) {

            $userSession = array('success' => "Bank Transaction Added");
            $this->session->set_userdata($userSession);
            redirect(base_url() . "employee/invoice/get_advance_bank_transaction");
        } else {
            $userSession = array('error' => "Bank Transaction Not Added");
            $this->session->set_userdata($userSession);
            redirect(base_url() . "employee/invoice/get_advance_bank_transaction");
        }
    }
    
    function paytm_gateway_payment($agent_id){
        log_message("info",__METHOD__. "POST ". print_r($this->input->post(), true));
        $status = $this->_process_advance_payment($agent_id);
        if(!$status){
            $to = "abhaya@247around.com";
            $cc = "";
            $subject = "Payment Receipt Failed";
            $message = json_encode($this->input->post(), true);
            $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, '', $subject, $message, "", "Ad Receipt Failed");
        } else {
            log_message("info", __METHOD__ . "Receipt Inserted ".  "POST ". print_r($this->input->post(), true));
        }
    }
    
    function _process_advance_payment($agent_id){
        $data['partner_vendor'] = $this->input->post("partner_vendor");
        $data['partner_vendor_id'] = $this->input->post('partner_vendor_id');
        $data['credit_debit'] = $this->input->post("credit_debit");
        $data['bankname'] = $this->input->post("bankname");
        $data['transaction_date'] = date("Y-m-d", strtotime($this->input->post("tdate")));
        $data['tds_amount'] = $this->input->post('tds_amount');
        $amount = $this->input->post("amount");
        if ($data['credit_debit'] == "Credit") {
            $data['credit_amount'] = $amount -  $data['tds_amount'];
            
            $invoice_id = $this->advance_invoice_insert($data['partner_vendor'], 
                    $data['partner_vendor_id'], $data['transaction_date'],
                    $amount, $data['tds_amount'], "Credit", $agent_id);
            if($invoice_id){
                $data['invoice_id'] = $invoice_id;
                $data['is_advance'] = 1;
            }
            
            
        } else if ($data['credit_debit'] == "Debit") {
            $data['debit_amount'] = $amount;
            if($data['partner_vendor'] == "vendor"){
                  $invoice_id = $this->advance_invoice_insert($data['partner_vendor'], 
                    $data['partner_vendor_id'], $data['transaction_date'],
                    $amount, $data['tds_amount'], "Debit", $agent_id);
                if($invoice_id){
                    $data['invoice_id'] = $invoice_id;
                    $data['is_advance'] = 1;
                }
            }
        }

       
        $data['transaction_mode'] = $this->input->post('transaction_mode');
        $data['description'] = $this->input->post("description");
        $data['agent_id'] = $agent_id;
        $data['create_date'] = date("Y-m-d H:i:s");
        $data['transaction_id'] = $this->input->post('transaction_id');
       
        return $this->invoices_model->bankAccountTransaction($data);
    }
    
    function advance_invoice_insert($vendor_partner, $vendor_partner_id, $date, $amount, $tds, $txntype, $agent_id) {

        if ($vendor_partner == "vendor") {
            $entity = $this->vendor_model->getVendorDetails("is_cp, sc_code", array("id" => $vendor_partner_id));
        } else if ($vendor_partner == "partner") {
            $entity = $this->partner_model->getpartner_details("partners.id, gst_number, "
                    . "state,address as company_address, "
                    . "company_name, pincode, "
                    . "district, invoice_email_to,invoice_email_cc", array('partners.id' => $vendor_partner_id));
        }
        
        if (!empty($entity)) {
            if ($vendor_partner == "vendor") {
                if($txntype == "Credit"){
                    $data['invoice_id'] = $this->create_invoice_id_to_insert("ARD-RV");
                    $data['type'] = BUYBACK_VOUCHER;
                    $basic_price = $amount;

                    $data['parts_cost'] = $basic_price;
                    $amount_collected_paid = $amount;
                    $data['type_code'] = "B";
                    $data['amount_collected_paid'] = -$amount_collected_paid;
                } else {
                    $data['invoice_id'] = $this->create_invoice_id_to_insert($entity[0]['sc_code']."-RV");
                    $data['type'] = VENDOR_VOUCHER;
                    $basic_price = $amount;
                    $amount_collected_paid = $amount;
                    $data['total_service_charge'] = $basic_price;
                    $data['type_code'] = "A";
                    $data['amount_collected_paid'] = $amount_collected_paid;
                }
                
                
            } else {
                $data['invoice_id'] = $this->create_invoice_id_to_insert("ARD-PV");
                if($tds > 0){
                    $data['tds_amount'] = $tds;
                }
                $data['type'] = PARTNER_VOUCHER;
                $response = $this->generate_partner_additional_invoice($entity[0], PARTNER_ADVANCE_DESCRIPTION,
                        $amount, $data['invoice_id'], $date,  $date,  $date, HSN_CODE, "Receipt Voucher", 
                        ADVANCE_RECEIPT_EMAIL_TAG, 1, DEFAULT_TAX_RATE);
                
                $data['cgst_tax_amount'] = $data['sgst_tax_amount'] = $response['meta']['cgst_total_tax_amount'];
                $data['igst_tax_amount'] = $response['meta']['igst_total_tax_amount'];
                $data['cgst_tax_rate'] = $data['sgst_tax_rate'] = $response['meta']['sgst_tax_rate'];
                $data['igst_tax_rate'] = $response['meta']['igst_tax_rate'];

                $data['total_service_charge'] = $response['meta']['total_taxable_value'];
                $data['invoice_file_pdf'] = $response['meta']['copy_file'];
                $data['invoice_file_main'] = $response['meta']['invoice_file_main'];
                $data['invoice_file_excel'] = $response['meta']['invoice_file_excel'];
              
                $amount_collected_paid = $amount - $tds;
                $data['type_code'] = "B";
                $data['amount_collected_paid'] = -$amount_collected_paid;
            }

            
            $data['vendor_partner'] = $vendor_partner;
            $data['vendor_partner_id'] = $vendor_partner_id;
            $data['invoice_date'] = $date;
            $data['from_date'] = $date;
            $data['to_date'] = $date;
            $data['due_date'] = $date;
            
            $data['total_amount_collected'] = $amount;
            $data['around_royalty'] = 0;
            
            $data['agent_id'] = $agent_id;
            $data['create_date'] = date("Y-m-d H:i:s");
            $data['hsn_code'] = HSN_CODE;

            $this->invoices_model->action_partner_invoice($data);
            return $data['invoice_id'];
        } else {
            return false;
        }
    }
    /**
     * @desc This is used to generate Partner Buyback/CRM Setup/Advance invoice excel/PDF. 
     * It returns array which store excel data and also used to send mail
     * @param Array $partner_data
     * @param String $description
     * @param Int $amount
     * @param String $invoice_id
     * @param date $sd
     * @param date $ed
     * @param date $invoice_date
     * @param String $hsn_code
     * @return Array
     */
    function generate_partner_additional_invoice($partner_data, $description,
            $amount, $invoice_id, $sd, $ed, $invoice_date, $hsn_code, $invoice_type, $email_tag, $qty, $gst_rate){
        log_message("info", __METHOD__." Partner ID ".$invoice_id);
        $data = array();
        $data[0]['description'] =  $description;
        $tax_charge = $this->booking_model->get_calculated_tax_charge($amount, DEFAULT_TAX_RATE);
        $data[0]['taxable_value'] = ($amount  - $tax_charge);
        $data[0]['product_or_services'] = "Service";
        if(!empty($partner_data['gst_number'])){
             $data[0]['gst_number'] = $partner_data['gst_number'];
        } else {
             $data[0]['gst_number'] = TRUE;
        }
       
        $data[0]['company_name'] = $partner_data['company_name'];
        $data[0]['company_address'] = $partner_data['company_address'];
        $data[0]['district'] = $partner_data['district'];
        $data[0]['pincode'] = $partner_data['pincode'];
        $data[0]['state'] = $partner_data['state'];
        $data[0]['rate'] = 0;

        $data[0]['qty'] = $qty;

        $data[0]['hsn_code'] = $hsn_code;
                
        $response = $this->invoices_model->_set_partner_excel_invoice_data($gst_rate, $data, $sd, $ed, $invoice_type,$invoice_date);
        log_message("info", __METHOD__." Partner Advance Excel Data generated ".$invoice_id);
        $response['meta']['invoice_id'] = $invoice_id;
        if($invoice_type == "Receipt Voucher"){
            $response['meta']['sd'] = "";
            $response['meta']['ed'] = "";
        }
       
        $status = $this->invoice_lib->send_request_to_create_main_excel($response, "final");
        if($status){
            log_message("info", __METHOD__." Partner Advance Excel generated ".$invoice_id);
            
            $convert = $this->invoice_lib->send_request_to_convert_excel_to_pdf($invoice_id, "final");
            $output_pdf_file_name = $convert['main_pdf_file_name'];
            $response['meta']['invoice_file_main'] = $output_pdf_file_name;
            $response['meta']['copy_file'] = $convert['copy_file'];
            $response['meta']['invoice_file_excel'] = $invoice_id.".xlsx";
            
            $this->upload_invoice_to_S3($invoice_id, false);
            
            
            
         
            if(!empty($email_tag)){
                $email_template = $this->booking_model->get_booking_email_template($email_tag);
                $subject = vsprintf($email_template[4], array($partner_data['company_name'], $sd, $ed));
                $message = $email_template[0];
                $email_from = $email_template[2];
                $to = $email_template[1];
                $cc = $email_template[3];
                $cmd = "curl " . S3_WEBSITE_URL . "invoices-excel/" . $output_pdf_file_name . " -o " . TMP_FOLDER.$output_pdf_file_name;
                exec($cmd);    
                $this->send_email_with_invoice($email_from, $to, $cc, $message, $subject, TMP_FOLDER.$output_pdf_file_name, "",$email_tag);
                
                unlink(TMP_FOLDER.$output_pdf_file_name);
            }

            unlink(TMP_FOLDER.$invoice_id.".xlsx");
            unlink(TMP_FOLDER."copy_".$invoice_id.".xlsx");
            
        }
       
        return $response;
    }

    /**
     * @desc show form for new credit note for brackets
     * @param void
     * @return void 
     */
    function show_purchase_brackets_credit_note_form() {
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/purchase_brackets_credit_note_form');
    }

    /**
     * @desc validate order id before processing credit note form 
     * @param void
     * @return boolean 
     */
    function validate_order_id() {
        $order_id = $this->input->post('order_id');
        if (!empty($order_id)) {
            //check if order id is present in database
            $check_order_id_exist = $this->inventory_model->check_order_id_exist($order_id);
            if (!empty($check_order_id_exist)) {
                if (empty($check_order_id_exist[0]['purchase_invoice_id'])) {
                    return true;
                } else {
                    $this->form_validation->set_message('validate_order_id', 'Credit Note for this Order ID already exists');
                    return false;
                }
            } else {
                $this->form_validation->set_message('validate_order_id', 'Order Id does not exist');
                return false;
            }
        } else {
            $this->form_validation->set_message('validate_order_id', 'Please fill the Order ID');
            return false;
        }
    }

    /**
     * @desc create excel and pdf file for new credit note for brackets 
     * @param array $booking
     * @param array $meta
     * @return boolean 
     */
    function generate_new_credit_note_brackets($booking, $meta) {
        $template = 'bracket_credit_note.xlsx';
        // directory
        $templateDir = __DIR__ . "/../excel-templates/";

        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );

        //load template
        $R = new PHPReport($config);

        $R->load(array(
            array(
                'id' => 'meta',
                'repeat' => false,
                'data' => $meta,
                'format' => array(
                    'date' => array('datetime' => 'd/M/Y')
                )
            ),
            array(
                'id' => 'booking',
                'repeat' => true,
                'data' => $booking,
            ),
                )
        );

        $output_file_excel = TMP_FOLDER . $meta['invoice_id'] . ".xlsx";

        $res1 = 0;
        if (file_exists($output_file_excel)) {

            system(" chmod 777 " . $output_file_excel, $res1);
            unlink($output_file_excel);
        }

        $R->render('excel', $output_file_excel);

        log_message('info', __METHOD__ . ": Excel FIle generated " . $output_file_excel);
        $res2 = 0;
        system(" chmod 777 " . $output_file_excel, $res2);
        
        //upload file to s3
        $bucket = BITBUCKET_DIRECTORY;
        $directory_xls = "invoices-excel/" . $meta['invoice_id'] . ".xlsx";

        $foc_upload = $this->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
        
        if ($foc_upload) {
            log_message('info', __METHOD__ . ": New Credit Note For brackets Excel File uploaded to s3");
        } else {
            log_message('info', __METHOD__ . ": Error in Uploading New Credit Note For brackets Excel File to s3 " . $meta['invoice_id'] . ".xlsx");
        }

        if (file_exists($output_file_excel)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @desc send mail and sms to vendor for new credit note for brackets 
     * @param array $vendor_details
     * @param string $invoice_id
     * @param string $amount
     * @return boolean 
     */
    function send_brackets_credit_note_mail_sms($vendor_details, $invoice_id, $amount,$attachment) {


        //send sms
        $this->send_invoice_sms("Stand", $vendor_details[0]['shipment_date'], $amount, $vendor_details[0]['owner_phone_1'], $vendor_details[0]['order_given_to']);
        
        //send email
        $get_rm_email =$this->vendor_model->get_rm_sf_relation_by_sf_id($vendor_details[0]['id']); 
        $to = $vendor_details[0]['owner_email'].",".$this->session->userdata('official_email').",".$get_rm_email[0]['official_email'];
        $cc = ANUJ_EMAIL_ID.", ".ACCOUNTANT_EMAILID;
        //get email template from database
        $email_template = $this->booking_model->get_booking_email_template(BRACKETS_CREDIT_NOTE_INVOICE_EMAIL_TAG);
        $subject = vsprintf($email_template[4], array($vendor_details[0]['company_name']));
        $message = $email_template[0];
        $email_from = $email_template[2];
        
        $output_file_excel = TMP_FOLDER.$invoice_id.'.xlsx';
        $output_file_pdf = 'https://s3.amazonaws.com/'.BITBUCKET_DIRECTORY.'/invoices-excel/'.$attachment;
        
        $send_mail = $this->notify->sendEmail($email_from, $to, $cc, '', $subject, $message, $output_file_pdf,BRACKETS_CREDIT_NOTE_INVOICE_EMAIL_TAG);
        if ($send_mail) {
            exec("rm -rf " . escapeshellarg($output_file_excel));
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    function get_invoice_payment_history(){
        $invoice_id = trim($this->input->post('invoice_id'));
        $select = 'payment_history.credit_debit, payment_history.credit_debit_amount,employee.full_name, payment_history.tds_amount, bank_transactions.transaction_date';
        $data['payment_history'] = $this->invoices_model->get_payment_history($select,array('payment_history.invoice_id'=>$invoice_id),true);
        echo $this->load->view('employee/show_invoice_payment_history_list',$data);
    }
    /**
     * @desc This method is used to generate oow spare parts
     * @param int $spare_id
     */
    function generate_oow_parts_invoice($spare_id) {
        $req['where'] = array("spare_parts_details.id" => $spare_id);
        $req['length'] = -1;
        $req['select'] = "spare_parts_details.purchase_price, parts_requested, spare_parts_details.service_center_id, spare_parts_details.booking_id";
        $sp_data = $this->inventory_model->get_spare_parts_query($req);
        if (!empty($sp_data)) {
            $vendor_details = $this->vendor_model->getVendorDetails("gst_no, "
                    . "company_name,address as company_address,district,"
                    . "state, pincode, owner_email, primary_contact_email", array('id' => $sp_data[0]->service_center_id));
            $data = array();
            $data[0]['description'] = ucwords($sp_data[0]->parts_requested) . " (" . $sp_data[0]->booking_id . ") ";
            $amount = $sp_data[0]->purchase_price + $sp_data[0]->purchase_price * REPAIR_OOW_AROUND_PERCENTAGE;
            $tax_charge = $this->booking_model->get_calculated_tax_charge($amount, DEFAULT_TAX_RATE);
            $data[0]['taxable_value'] = ($amount - $tax_charge);
            $data[0]['product_or_services'] = "Product";
            $data[0]['gst_number'] = $vendor_details[0]['gst_no'];
            $data[0]['company_name'] = $vendor_details[0]['company_name'];
            $data[0]['company_address'] = $vendor_details[0]['company_address'];
            $data[0]['district'] = $vendor_details[0]['district'];
            $data[0]['pincode'] = $vendor_details[0]['pincode'];
            $data[0]['state'] = $vendor_details[0]['state'];
            $data[0]['rate'] = "0";
            $data[0]['qty'] = 1;
            $data[0]['hsn_code'] = SPARE_HSN_CODE;
            $sd = $ed = $invoice_date = date("Y-m-d");
            $gst_rate = DEFAULT_TAX_RATE;

            $response = $this->invoices_model->_set_partner_excel_invoice_data($gst_rate, $data, $sd, $ed, "Tax Invoice",$invoice_date);
            $response['meta']['invoice_id'] = $this->create_invoice_id_to_insert("Around");
            $status = $this->invoice_lib->send_request_to_create_main_excel($response, "final");
            if ($status) {
                log_message("info", __METHOD__ . " Vendor Spare Invoice SF ID" . $sp_data[0]->service_center_id . " Spare Id " . $spare_id);

                $convert = $this->invoice_lib->send_request_to_convert_excel_to_pdf($response['meta']['invoice_id'], "final");
                $output_pdf_file_name = $convert['main_pdf_file_name'];
                $response['meta']['invoice_file_main'] = $output_pdf_file_name;
                $response['meta']['copy_file'] = $convert['copy_file'];

                $email_template = $this->booking_model->get_booking_email_template(SPARE_INVOICE_EMAIL_TAG);
                $subject = vsprintf($email_template[4], array($vendor_details[0]['company_name'], $sp_data[0]->booking_id));
                $message = $email_template[0];
                $email_from = $email_template[2];

               // $rm_details = $this->vendor_model->get_rm_sf_relation_by_sf_id($sp_data[0]->service_center_id);
//                $rem_email_id = "";
//                if (!empty($rm_details)) {
//                    $rem_email_id = ", " . $rm_details[0]['official_email'];
//                }
               // $to = $vendor_details[0]['owner_email'] . ", " . $vendor_details[0]['primary_contact_email'];
                $to = $email_template[3];
                $cc ="";

                $this->upload_invoice_to_S3($response['meta']['invoice_id'], false);

                $cmd = "curl https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/invoices-excel/" . $output_pdf_file_name . " -o " . TMP_FOLDER . $output_pdf_file_name;
                exec($cmd);
                $this->send_email_with_invoice($email_from, $to, $cc, $message, $subject, TMP_FOLDER . $output_pdf_file_name, "",SPARE_INVOICE_EMAIL_TAG);

                unlink(TMP_FOLDER . $response['meta']['invoice_id'] . ".xlsx");
                unlink(TMP_FOLDER . $output_pdf_file_name);
                unlink(TMP_FOLDER . "copy_" . $response['meta']['invoice_id'] . ".xlsx");

                $invoice_details = array(
                    'invoice_id' => $response['meta']['invoice_id'],
                    'type_code' => 'A',
                    'type' => "Cash",
                    'vendor_partner' => 'vendor',
                    'vendor_partner_id' => $sp_data[0]->service_center_id,
                    'invoice_file_main' => $response['meta']['invoice_file_main'],
                    'invoice_file_excel' => $response['meta']['invoice_id'] . ".xlsx",
                    'from_date' => date("Y-m-d", strtotime($sd)), //??? Check this next time, format should be YYYY-MM-DD
                    'to_date' => date("Y-m-d", strtotime($ed)),
                    'parts_cost' => $response['meta']['total_taxable_value'],
                    'parts_count' => 1,
                    'total_amount_collected' => $response['meta']['sub_total_amount'],
                    'invoice_date' => date("Y-m-d"),
                    'around_royalty' => $response['meta']['sub_total_amount'],
                    'due_date' => date("Y-m-d"),
                    //Amount needs to be collected from Vendor
                    'amount_collected_paid' => $response['meta']['sub_total_amount'],
                    //add agent_id
                    'agent_id' => _247AROUND_DEFAULT_AGENT,
                    "cgst_tax_rate" => $response['meta']['cgst_tax_rate'],
                    "sgst_tax_rate" => $response['meta']['sgst_tax_rate'],
                    "igst_tax_rate" => $response['meta']['igst_tax_rate'],
                    "igst_tax_amount" => $response['meta']["igst_total_tax_amount"],
                    "sgst_tax_amount" => $response['meta']["sgst_total_tax_amount"],
                    "cgst_tax_amount" => $response['meta']["cgst_total_tax_amount"],
                    "hsn_code" => SPARE_HSN_CODE,
                    "invoice_file_pdf" => $response['meta']['copy_file'],
                    "remarks" => $data[0]['description']
                );

                $this->invoices_model->insert_new_invoice($invoice_details);
                log_message('info', __METHOD__ . ": Invoice ID inserted");

                $this->service_centers_model->update_spare_parts(array('id' => $spare_id), array("sell_invoice_id" => $response['meta']['invoice_id']));
                log_message('info', __METHOD__ . ": Invoice Updated in Spare Parts " . $response['meta']['invoice_id']);

                $this->booking_model->update_booking_unit_details_by_any(array("booking_id" => $sp_data[0]->booking_id, "price_tags" => "Spare Parts"), 
                        array("pay_from_sf" => 0, "vendor_cash_invoice_id" => $response['meta']['invoice_id']));
                log_message('info', __METHOD__ . ": ...Exit" . $response['meta']['invoice_id']);
            }
        }
    }
    
    function checkUserSession() {
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            echo PHP_EOL . 'Terminal Access Not Allowed' . PHP_EOL;
            redirect(base_url() . "employee/login");
        }
    }
    
    function generate_spare_purchase_invoice() {
        log_message("info", __METHOD__ . " Post " . print_r($this->input->post("spare_id"), true));

        $this->form_validation->set_rules('spare_id', 'Spare ID', 'required|xss_clean');
        $this->form_validation->set_rules('invoice_date', 'Invoice Date', 'required|xss_clean');
        $this->form_validation->set_rules('remarks', 'Remarks', 'required|xss_clean');
        $this->form_validation->set_rules('parts_count', 'Count', 'required|xss_clean');
        $this->form_validation->set_rules('invoice_id', 'Invoice ID', 'required|xss_clean');
        $this->form_validation->set_rules('hsn_code', 'HSN Code', 'required|xss_clean');
        $this->form_validation->set_rules('parts_charge', '', 'required|xss_clean');
        $validation = $this->form_validation->run();
        if ($validation) {
            $spare_id = $this->input->post("spare_id");
            $w['length'] = -1;
            $w['where_in'] = array("spare_parts_details.id" => $spare_id);
            $w['select'] = "spare_parts_details.id, spare_parts_details.booking_id, purchase_price, public_name, spare_parts_details.partner_id, "
                    . "purchase_invoice_id,sell_invoice_id, sell_price, incoming_invoice_pdf, partners.state";
            $data = $this->inventory_model->get_spare_parts_query($w);

            $unique_partner = array_unique(array_map(function ($k) {
                        return $k->partner_id;
                    }, $data));

            if (count($unique_partner) == 1) {
                $uni_booking_id = array_unique(array_map(function ($k) {
                            return $k->booking_id;
                        }, $data));
                $invoice_pdf = "";
                foreach ($data as $sp) {
                    if (!empty($sp->incoming_invoice_pdf)) {
                        $invoice_pdf = $sp->incoming_invoice_pdf;
                    }
                }

                if (!empty($invoice_pdf)) {
                    $invoice_date = $this->input->post("invoice_date");
                    $invoice['invoice_id'] = trim($this->input->post("invoice_id"));
                    $invoice['vendor_partner'] = "partner";
                    $invoice['remarks'] = trim($this->input->post("remarks")) . " for Booking id " . implode(", ", $uni_booking_id);
                    $invoice['parts_count'] = trim($this->input->post("parts_count"));
                    $invoice['hsn_code'] = trim($this->input->post("hsn_code"));
                    $invoice['total_amount_collected'] = trim($this->input->post("parts_charge"));
                   
                    $invoice['type'] = "Parts";
                    $invoice['invoice_date'] = $invoice['due_date'] = date("Y-m-d", strtotime($invoice_date));
                    $invoice['from_date'] = $invoice['to_date'] = date("Y-m-d", strtotime($invoice_date));
                    $invoice['type_code'] = "B";
                    $invoice['agent_id'] = $this->session->userdata('id');
                    $invoice['vendor_partner_id'] = $data[0]->partner_id;
                    $gst_rate = trim($this->input->post('gst_rate'));
                    $gst_amount =  $this->booking_model->get_calculated_tax_charge($invoice['total_amount_collected'], $gst_rate); 
                    $amount_collected_paid = round(($invoice['total_amount_collected'] - $gst_amount), 2);
                    $invoice['parts_cost'] = $amount_collected_paid;
                    
                    $invoice['invoice_file_main'] = $invoice_pdf;

                    $c_s_gst = $this->invoices_model->check_gst_tax_type($data[0]->state);
                    if ($c_s_gst) {

                        $invoice['cgst_tax_amount'] = $invoice['sgst_tax_amount'] = $gst_amount / 2;
                        $invoice['cgst_tax_rate'] = $invoice['sgst_tax_rate'] = $gst_rate / 2;
                    } else {
                        $invoice['igst_tax_amount'] = $gst_amount;
                        $invoice['igst_tax_rate'] = $gst_rate;
                    }
                    
                    $invoice['amount_collected_paid'] = -$amount_collected_paid;

                    $this->invoices_model->action_partner_invoice($invoice);
                    foreach ($spare_id as $id) {

                        $this->service_centers_model->update_spare_parts(array('id' => $id), array("purchase_invoice_id" => $invoice['invoice_id']));
                    }

                    echo "Success";
                } else {
                    echo "Invoice PDF Not Available";
                }
            } else {
                echo "Please Select Unique Partner Booking";
            }
        } else {
            echo "Please Enter All Field";
        }
    }
    
    function customer_invoice(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/customer_invoice_details');
    }
    /**
     * @desc This is used to insert Credit not or Debit note
     */
    function generate_credit_debit_note() {
        log_message('info', __METHOD__ . " POST DATA " . json_encode($this->input->post(), TRUE));
        $this->checkUserSession();
        $this->form_validation->set_rules('vendor_partner_id', 'Vendor Partner ID', 'required|trim|xss_clean');
        $this->form_validation->set_rules('vendor_partner', 'Vendor_partner', 'required|trim|xss_clean');
        $this->form_validation->set_rules('invoice_type', 'Invoice Type', 'required|trim|xss_clean');
        $this->form_validation->set_rules('service_count', 'Service QTY', 'required|trim|xss_clean');
        $this->form_validation->set_rules('parts_count', 'Parts QTY', 'required|trim|xss_clean');
        $this->form_validation->set_rules('parts_charge', 'Parts Charge', 'required|trim|xss_clean');
        $this->form_validation->set_rules('service_charge', 'Service Charge', 'required|trim|xss_clean');
        $this->form_validation->set_rules('gst_rate', 'GST Rate', 'required|trim|xss_clean');
        $this->form_validation->set_rules('invoice_date', 'Invoice Period', 'required|trim|xss_clean');
        $this->form_validation->set_rules('remarks', 'Remarks', 'required|trim|xss_clean');
        $this->form_validation->set_rules('description', 'Description', 'required|trim|xss_clean');

        if ($this->form_validation->run()) {
            $data['vendor_partner'] = $this->input->post('vendor_partner');
            $data['vendor_partner_id'] = $this->input->post('vendor_partner_id');
            $data['type'] = $this->input->post('invoice_type');
            $data['total_service_charge'] = $this->input->post('service_charge');
            $data['parts_cost'] = $this->input->post('parts_charge');
            $data['num_bookings'] = $this->input->post('service_count');
            $data['parts_count'] = $this->input->post('parts_count');
            $gst_rate = trim($this->input->post('gst_rate'));
            $data['hsn_code'] = $this->input->post('hsn_code');
            $data['remarks'] = $this->input->post('remarks');

            $total_qty = $data['parts_count'] + $data['num_bookings'];
            $description = $this->input->post('description');

            $invoice_date = date("Y-m-d", strtotime($this->input->post('invoice_date')));

            $sd = $ed = $invoice_date;

            $total_basic_amount = $data['total_service_charge'] + $data['parts_cost'];
            $total_amount = $total_basic_amount * (1 + $gst_rate / 100);
            if ($data['type'] == "CreditNote") {


                $invoice_id = $this->invoice_lib->create_invoice_id("ARD-CN");
                $type = "Credit Note";
                $data['type_code'] = "B";
            } else {

                $invoice_id = $this->invoice_lib->create_invoice_id("ARD-DN");
                $type = "Debit Note";
                $data['type_code'] = "A";
            }

            if ($data['vendor_partner'] == "vendor") {

                $entity_details = $this->vendor_model->getVendorDetails("gst_no as gst_number, sc_code,"
                        . "state,address as company_address,company_name,district, pincode", array("id" => $data['vendor_partner_id']));
            } else {

                $entity_details = $this->partner_model->getpartner_details("gst_number,"
                        . "company_name, state, address as company_address, district, pincode, "
                        . "invoice_email_to,invoice_email_cc", array('partners.id' => $data['vendor_partner_id']));
            }

            $response = $this->generate_partner_additional_invoice($entity_details[0], $description, $total_amount, $invoice_id, $sd, $ed, $invoice_date, $data['hsn_code'], $type, "", $total_qty, $gst_rate);
            $file = $this->upload_create_update_invoice_to_s3($invoice_id);

            if (isset($file['invoice_detailed_excel'])) {
                $data['invoice_detailed_excel'] = $file['invoice_detailed_excel'];
            }

            $data['invoice_id'] = $invoice_id;
            $data['invoice_file_main'] = $response['meta']['invoice_file_main'];
            $data['invoice_file_excel'] = $response['meta']['invoice_id'] . ".xlsx";
            $data['from_date'] = date("Y-m-d", strtotime($sd));
            $data['to_date'] = date("Y-m-d", strtotime($sd));
            $data['due_date'] = date("Y-m-d", strtotime($sd));
            $data['total_amount_collected'] = $response['meta']['sub_total_amount'];
            $data['invoice_date'] = date("Y-m-d", strtotime($sd));
            if ($data['type'] == "CreditNote") {
                $data['amount_collected_paid'] = -$response['meta']['sub_total_amount'];
            } else {
                $data['amount_collected_paid'] = $response['meta']['sub_total_amount'];
            }
            $data['agent_id'] = $this->session->userdata('id');
            $data['cgst_tax_rate'] = $response['meta']['cgst_tax_rate'];
            $data['sgst_tax_rate'] = $response['meta']['sgst_tax_rate'];
            $data['igst_tax_rate'] = $response['meta']['igst_tax_rate'];
            $data['igst_tax_amount'] = $response['meta']['igst_total_tax_amount'];
            $data['sgst_tax_amount'] = $response['meta']['sgst_total_tax_amount'];
            $data['cgst_tax_amount'] = $response['meta']['cgst_total_tax_amount'];

            $status = $this->invoices_model->insert_new_invoice($data);
            if (!empty($status)) {
                 log_message("info", __METHOD__ . " Invoice Inserted ");
                echo "Success";
            } else {
                log_message("info", __METHOD__ . " Invoice not Inserted ");
                echo "Error";
            }
        } else {
            log_message("info", __METHOD__ . " validation failed");
            echo validation_errors(); 
        }
    }

}
