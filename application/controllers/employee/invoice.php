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
        $this->load->library("notify");
        $this->load->library('PHPReport');
        $this->load->library('form_validation');
        $this->load->library("session");
        $this->load->library('s3');
        $this->load->library('table');

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
        
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
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

        if ($data['vendor_partner'] == "vendor") {
            $invoice['vendor_details'] = $this->vendor_model->getVendorContact($data['vendor_partner_id']);
            $where = "service_center_id = '" . $data['vendor_partner_id'] . "' AND approved_defective_parts_by_partner = '0' "
                    . " AND parts_shipped IS NOT NULL ";

            $invoice['count_spare_parts'] = count($this->partner_model->get_spare_parts_booking($where));
            if (!empty($invoice['invoice_array'])) {
                $to_date = $invoice['invoice_array'][count($invoice['invoice_array']) - 1]['to_date'];
            } else {
                $to_date = "";
            }

            $invoice['unbilled_amount'] = $this->invoices_model->get_unbilled_amount($data['vendor_partner_id'], $to_date);
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

        $cc = "billing@247around.com, " . NITS_ANUJ_EMAIL_ID;
        $subject = "247around - Invoice for period: " . $start_date . " To " . $end_date;
        $attachment = 'https://s3.amazonaws.com/' . BITBUCKET_DIRECTORY . '/invoices-pdf/' . $invoiceId . '.pdf';

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

        $this->load->view('employee/header/' . $this->session->userdata('user_group'));
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
        $data['invoice_id_array'] = $this->input->post('invoice_id');
        $data['selected_amount_collected'] = $this->input->post('selected_amount_collected');
        $data['selected_tds'] = $this->input->post('selected_tds');
        $data['tds_amount'] = $this->input->post('tds_amount');
        $data['amount_collected'] = $this->input->post('amount_collected');
        $data['invoice_id_list'] = array();
        if (empty($vendor_partner)) {
            $where = " amount_paid = 0 ";
            $data['invoice_id_list'] = $this->invoices_model->get_invoices_details($where);
        }

        $this->load->view('employee/header/' . $this->session->userdata('user_group'));
        $this->load->view('employee/addnewtransaction', $data);
    }

    /**
     * @desc: This is used to load upadate form of bank transaction details
     * @param String $id (Bank transaction id)
     */
    function update_banktransaction($id) {

        $details = $this->invoices_model->get_bank_transactions_details(array('id' => $id));
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


            $bank_payment_history = $this->invoices_model->get_payment_history(array('bank_transaction_id' => $id));
            foreach ($bank_payment_history as $value) {
                if ($value == "Debit") {
                    $amount = -$value['credit_debit_amount'];
                } else {
                    $amount = $value['credit_debit_amount'];
                }
                $amount_collected[$value['invoice_id']] = $amount;
                $tds[$value['invoice_id']] = $value['tds_amount'];
            }

            $data['tds_amount'] = $tds;
            $data['amount_collected'] = $amount_collected;

            $this->load->view('employee/header/' . $this->session->userdata('user_group'));
            $this->load->view('employee/addnewtransaction', $data);
        }
    }

    /**
     *  @desc : This is used to insert and update bank transaction table. It gets bank transaction id while update other wise empty
     *  @param : void
     *  @return : void
     */
    function post_add_new_transaction() {
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
                $where = " invoice_id = '" . $invoice_id . "' ";
                $data = $this->invoices_model->get_invoices_details($where);
                $credit_debit = $credit_debit_array[$key];
                $p_history['invoice_id'] = $invoice_id;
                $p_history['credit_debit'] = $credit_debit;
                $p_history['credit_debit_amount'] = round($credit_debit_amount[$key],0);
                $p_history['agent_id'] = $this->session->userdata('id');
                $p_history['tds_amount'] = $tds_amount_array[$key];
                $p_history['create_date'] = date("Y-m-d H:i:s");
                array_push($payment_history, $p_history);

                if ($credit_debit == 'Credit') {

                    $paid_amount += round($credit_debit_amount[$key], 0);
                } else if ($credit_debit == 'Debit') {

                    $paid_amount += (-round($credit_debit_amount[$key], 0));
                }
                $tds += $tds_amount_array[$key];
                $amount_collected = abs(round(($data[0]['amount_collected_paid'] + $data[0]['amount_paid']), 0));

                if ($amount_collected == round($credit_debit_amount[$key], 0)) {

                    $vp_details['settle_amount'] = 1;
                    $vp_details['amount_paid'] = $credit_debit_amount[$key] + $data[0]['amount_paid'];
                } else {
                    //partner Pay to 247Around
                    if ($account_statement['partner_vendor'] == "partner" && $credit_debit == 'Credit') {
                        $per_tds = ($tds_amount_array[$key] * 100) / $data[0]['amount_collected_paid'];
                        $vp_details['tds_amount'] = $tds_amount_array[$key];
                        $vp_details['tds_rate'] = $per_tds;
                        $amount_collected = $data[0]['total_amount_collected'] - $vp_details['tds_amount'];
                        $vp_details['around_royalty'] = $vp_details['amount_collected_paid'] = $amount_collected;

                        if (round($amount_collected, 0) == round($credit_debit_amount[$key], 0)) {
                            $vp_details['settle_amount'] = 1;
                        } else {
                            $vp_details['settle_amount'] = 0;
                        }
                        $vp_details['amount_paid'] = $credit_debit_amount[$key];
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
        
        foreach($payment_history as $key => $value){
            $payment_history[$key]['bank_transaction_id '] = $bank_txn_id;
        }
        $this->accounting_model->insert_batch_payment_history($payment_history);

        //Send SMS to vendors about payment
        if ($account_statement['partner_vendor'] == 'vendor') {

             $this->send_payment_sms_to_vendor($account_statement);
        }

          redirect(base_url() . 'employee/invoice/invoice_summary/' . $account_statement['partner_vendor'] . "/" . $account_statement['partner_vendor_id']);
    }

    function send_payment_sms_to_vendor($account_statement) {
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

        $this->load->view('employee/header/' . $this->session->userdata('user_group'));
        $this->load->view('employee/view_transactions', $invoice);
    }

    /**
     * @desc: generate details partner Detailed invoices
     */
    function create_partner_invoices_detailed($partner_id, $f_date, $t_date, $invoice_type, $invoice_id,$agent_id) {
        log_message('info', __METHOD__ . "=> " . $invoice_type . " Partner Id " . $partner_id);
        $data1 = $this->invoices_model->getpartner_invoices($partner_id, $f_date, $t_date);
        $data = $data1['main_invoice'];
        $upcountry_invoice = $data1['upcountry_invoice'];

        $file_names = array();
        $template = 'Partner_invoice_detail_template-v2.xlsx';
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

            $start_date = date("jS M, Y", strtotime($f_date));
            $end_date = date("jS M, Y", strtotime($t_date));

            foreach ($data as $key => $value) {
                /*
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
                 * 
                 */

                $data[$key]['remarks'] = $value['price_tags'];
                $data[$key]['closed_date'] = date("jS M, Y", strtotime($value['closed_date']));
                $data[$key]['reference_date'] = date("jS M, Y", strtotime($value['reference_date']));

                $total_installation_charge += round($value['installation_charge'], 2);
                $total_service_tax += round($value['st'], 2);
                $total_stand_charge += round($value['stand'], 2);
                $total_vat_charge += round($value['vat'], 2);
                $total_charges = round(($total_installation_charge + $total_service_tax + $total_stand_charge + $total_vat_charge), 0);
            }

            $excel_data['invoice_id'] = $invoice_id;
            $excel_data['today'] = date("jS M, Y");
            $excel_data['company_name'] = $data[0]['company_name'];
            $excel_data['company_address'] = $data[0]['company_address'] . ", " .
                    $data[0]['district'] . ", Pincode - " . $data[0]['pincode'] . ", " . $data[0]['state'];
            $excel_data['total_installation_charge'] = $total_installation_charge;
            $excel_data['total_service_tax'] = $total_service_tax;
            $excel_data['total_stand_charge'] = $total_stand_charge;
            $excel_data['total_vat_charge'] = $total_vat_charge;
            $excel_data['total_charges'] = $total_charges;
            $excel_data['period'] = $start_date . " To " . $end_date;
            if (!empty($data[0]['seller_code'])) {
                $excel_data['seller_code'] = "Seller Code: " . $data[0]['seller_code'];
            } else {
                $excel_data['seller_code'] = '';
            }

            log_message('info', 'Excel data: ' . print_r($excel_data, true));

            $files_name = $this->generate_pdf_with_data($excel_data, $data, $R);

            $output_file_excel = "";
            $total_upcountry_booking = 0;
            $total_upcountry_distance = 0;
            $excel_data['total_upcountry_price'] = 0;
            if (!empty($upcountry_invoice)) {
                $template1 = 'Partner_invoice_detail_template-v2-upcountry.xlsx';


                //set config for report
                $config1 = array(
                    'template' => $template1,
                    'templateDir' => $templateDir
                );


                //load template
                $R1 = new PHPReport($config1);

                $excel_data['total_upcountry_price'] = $upcountry_invoice[0]['total_upcountry_price'];
                $total_upcountry_booking = $upcountry_invoice[0]['total_booking'];
                $total_upcountry_distance = $upcountry_invoice[0]['total_distance'];



                $R1->load(array(
                    array(
                        'id' => 'meta',
                        'data' => $excel_data,
                        'format' => array(
                            'date' => array('datetime' => 'd/M/Y')
                        )
                    ),
                    array(
                        'id' => 'upcountry',
                        'repeat' => true,
                        'data' => $upcountry_invoice,
                    ),
                        )
                );

                //Get populated XLS with data
                $output_file_dir = TMP_FOLDER;
                $output_file = $excel_data['invoice_id'] . "-upcountry-detailed";
                $output_file_excel = $output_file_dir . $output_file . ".xlsx";
                $res1 = 0;
                if (file_exists($output_file_excel)) {

                    system(" chmod 777 " . $output_file_excel, $res1);
                    unlink($output_file_excel);
                }
                //for xlsx: excel, for xls: excel2003
                $R1->render('excel', $output_file_excel);
                system(" chmod 777 " . $output_file_excel, $res1);
                array_push($file_names, $output_file_excel);
                $files_name = $this->combined_partner_invoice_sheet($files_name, $output_file_excel);
            }
            system(" chmod 777 " . $files_name . ".xlsx", $res1);

            log_message('info', __METHOD__ . "=> File created " . $files_name);
            //Send report via email
            $this->email->clear(TRUE);
            $this->email->from('billing@247around.com', '247around Team');
            $cc = "";
            if ($invoice_type == "final") {
                $to = $data[0]['invoice_email_to'];
                $subject = "247around - " . $data[0]['company_name'] .
                        " Invoice for period: " . $f_date . " to " . $t_date;

                $cc = $data[0]['invoice_email_cc'];
            } else {
                $to = ANUJ_EMAIL_ID;
                $cc = "";
                $subject = "DRAFT Partner INVOICE Detailed- 247around - " . $data[0]['company_name'] .
                        " Invoice for period: " . $f_date . " to " . $t_date;
            }

            $this->email->to($to);
            $this->email->cc($cc);
            $this->email->subject($subject);
            $this->email->attach($files_name . ".xlsx", 'attachment');
//            if($output_file_excel !=""){
//                $this->email->attach($output_file_excel, 'attachment');
//            }

           // $this->email->attach(TMP_FOLDER .$invoice_id. ".xlsx", 'attachment');
            if ($invoice_type == "draft") {

                $this->email->attach(TMP_FOLDER .$invoice_id. ".xlsx", 'attachment');

            }
            $this->email->attach(TMP_FOLDER .$invoice_id. ".pdf", 'attachment');
            

            $mail_ret = $this->email->send();

            if ($mail_ret) {
                log_message('info', __METHOD__ . ": Mail sent successfully");
                echo "Mail sent successfully..............." . PHP_EOL;
            } else {
                log_message('info', __METHOD__ . ": Mail could not be sent");
                echo "Mail could not be sent..............." . PHP_EOL;
            }

            array_push($file_names, $files_name . ".xlsx");
            array_push($file_names, TMP_FOLDER . $invoice_id . ".xlsx");
            array_push($file_names, TMP_FOLDER . $invoice_id . ".pdf");

            if ($invoice_type == "final") {
                log_message('info', __METHOD__ . "=> Final");
                $bucket = BITBUCKET_DIRECTORY;

                $directory_xls = "invoices-excel/" . $invoice_id . ".xlsx";
                $directory_pdf = "invoices-excel/" . $invoice_id . ".pdf";
                $directory_detailed = "invoices-excel/" . $invoice_id . "-detailed.xlsx";

                $this->s3->putObjectFile(TMP_FOLDER . $invoice_id . "-detailed.xlsx", $bucket, $directory_detailed, S3::ACL_PUBLIC_READ);
                $this->s3->putObjectFile(TMP_FOLDER . $invoice_id . ".xlsx", $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                $this->s3->putObjectFile(TMP_FOLDER . $invoice_id . ".pdf", $bucket, $directory_pdf, S3::ACL_PUBLIC_READ);
                if ($output_file_excel != "") {
                    $directory_upcountry_xls = "invoices-excel/" . $invoice_id . "-upcountry-detailed.xlsx";
                    $this->s3->putObjectFile($output_file_excel, $bucket, $directory_upcountry_xls, S3::ACL_PUBLIC_READ);
                }
                $tds = 0;

                $invoice_details = array(
                    'invoice_id' => $invoice_id,
                    'type_code' => 'A',
                    'type' => 'Cash',
                    'vendor_partner' => 'partner',
                    'vendor_partner_id' => $data[0]['partner_id'],
                    'invoice_file_excel' => $invoice_id . '.pdf',
                    'invoice_detailed_excel' => $invoice_id . '-detailed.xlsx',
                    'from_date' => date("Y-m-d", strtotime($f_date)), //??? Check this next time, format should be YYYY-MM-DD
                    'to_date' => date("Y-m-d", strtotime($t_date)),
                    'num_bookings' => $count,
                    'total_service_charge' => ($excel_data['total_installation_charge'] - $tds),
                    'total_additional_service_charge' => 0.00,
                    'service_tax' => $excel_data['total_service_tax'],
                    'parts_cost' => $excel_data['total_stand_charge'],
                    'vat' => $excel_data['total_vat_charge'],
                    'total_amount_collected' => ($excel_data['total_charges'] - $tds + $excel_data['total_upcountry_price']),
                    'tds_amount' => $tds,
                    'tds_rate' => '0',
                    'upcountry_booking' => $total_upcountry_booking,
                    'upcountry_distance' => $total_upcountry_distance,
                    'upcountry_price' => $excel_data['total_upcountry_price'],
                    'rating' => 5,
                    'invoice_date' => date('Y-m-d'),
                    'around_royalty' => $excel_data['total_charges'] + $excel_data['total_upcountry_price'],
                    'due_date' => date("Y-m-d", strtotime($t_date . "+1 month")),
                    //Amount needs to be collected from Vendor
                    'amount_collected_paid' => ($excel_data['total_charges'] + $excel_data['total_upcountry_price'] - $tds),
                    //add agent_id
                    'agent_id' => $agent_id
                );

                $this->invoices_model->insert_new_invoice($invoice_details);
                log_message('info', __METHOD__ . "=> Insert Invoices in partner invoice table");

                foreach ($data as $key => $value1) {

                    log_message('info', __METHOD__ . "=> Invoice update in booking unit details unit id" . $value1['unit_id'] . " Invoice Id" . $invoice_id);
                    $this->booking_model->update_booking_unit_details_by_any(array('id' => $value1['unit_id']), array('partner_invoice_id' => $invoice_id));
                }

                if (!empty($upcountry_invoice)) {
                    foreach ($upcountry_invoice as $up_booking_details) {
                        $this->booking_model->update_booking($up_booking_details['booking_id'], array('upcountry_partner_invoice_id' => $invoice_id));
                    }
                }
            }

            //Delete XLS files now
            foreach ($file_names as $file_name) {
                exec("rm -rf " . escapeshellarg($file_name));
            }
            return true;
        } else {

            log_message('info', __METHOD__ . "=> Data Not found" . $invoice_type . " Partner Id " . $partner_id);
            return FALSE;
        }
    }

    /**
     * @desc: Generate Excel and Pdf File with invoices data and return file names
     * @param: Array(Excel data), Array(Invoices data), Initiallized PHP report library and files name
     * @return : File name
     */
    function generate_pdf_with_data($excel_data, $data, $R) {
        log_message('info', __METHOD__);

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
        $output_file_dir = TMP_FOLDER;
        $output_file = $excel_data['invoice_id'] . "-detailed";
        $output_file_excel = $output_file_dir . $output_file . ".xlsx";
        $res1 = 0;
        if (file_exists($output_file_excel)) {

            system(" chmod 777 " . $output_file_excel, $res1);
            unlink($output_file_excel);
        }
        //for xlsx: excel, for xls: excel2003
        $R->render('excel', $output_file_excel);

        system(" chmod 777 " . $output_file_excel, $res1);
        //convert excel to pdf
        //$output_file_pdf = $output_file_dir . $output_file . ".pdf";
        //$cmd = "curl -F file=@" . $output_file_excel . " http://do.convertapi.com/Excel2Pdf?apikey=" . CONVERTAPI_KEY . " -o " . $output_file_pdf;
        // putenv('PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/opt/node/bin');
        $tmp_path = TMP_FOLDER;
        //  $tmp_output_file = TMP_FOLDER.'output_' . __FUNCTION__ . '.txt';
        //  $cmd = 'echo ' . $tmp_path . ' & echo $PATH & UNO_PATH=/usr/lib/libreoffice & ' .
        //          '/usr/bin/unoconv --format pdf --output ' . $output_file_pdf . ' ' .
        //         $output_file_excel . ' 2> ' . $tmp_output_file;
        // $output = '';
        // $result_var = '';
        // exec($cmd, $output, $result_var);
        // Dump data in a file as a Json
        $file = fopen(TMP_FOLDER . $output_file . ".txt", "w") or die("Unable to open file!");
        $res = 0;
        system(" chmod 777 " . TMP_FOLDER . $output_file . ".txt", $res);
        $json_data['excel_data'] = $excel_data;
        $json_data['invoice_data'] = $data;
        $contents = " Patner Invoice Json Data:\n";
        fwrite($file, $contents);
        fwrite($file, print_r(json_encode($json_data), TRUE));
        fclose($file);
        log_message('info', __METHOD__ . ": Json File Created");

        $bucket = BITBUCKET_DIRECTORY;
        $directory_xls = "invoices-json/" . $output_file . ".txt";
        $json = $this->s3->putObjectFile(TMP_FOLDER . $output_file . ".txt", $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
        if ($json) {
            log_message('info', __METHOD__ . ": Json File Uploded to S3");
        } else {
            log_message('info', __METHOD__ . ": Json File Not Uploded to S3");
        }


        //Delete JSON files now
        exec("rm -rf " . escapeshellarg(TMP_FOLDER . $output_file . ".txt"));

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
            log_message('info', __FUNCTION__ . "=> Data Found for Cash Detailed Invoice");
            echo "Data Found for Cash Detailed Invoice" . PHP_EOL;

            // it stores all unique booking id which is completed by particular vendor id
            $unique_booking = array_unique(array_map(function ($k) {
                        return $k['booking_id'];
                    }, $invoices['booking']));

            // Count unique booking id
            $count = count($unique_booking);

            // push unique booking array into another array
            array_push($unique_booking_cash, $unique_booking);

            log_message('info', __FUNCTION__ . '=> Start Date: ' .
                    $from_date . ', End Date: ' . $to_date . ', Service Centre: ' . $details['vendor_partner_id'] . ', Count: ' . $count);

            // set date format like 1st July 2016
            $start_date = date("jS M, Y", strtotime($from_date));
            $end_date = date("jS M, Y", strtotime($to_date));

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
                if ((strcasecmp($invoices['booking'][0]['state'], "DELHI") == 0) ||
                        (strcasecmp($invoices['booking'][0]['state'], "New Delhi") == 0)) {
                    //If matched return 0;
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
                $invoice_no_temp = $this->invoices_model->get_invoices_details($where);
                $invoice_no = 1;
                if (!empty($invoice_no_temp)) {
                    $explode = explode($invoice_id_tmp . "-", $invoice_no_temp[0]['invoice_id']);
                    $invoice_no = $explode[1] + 1;
                }

                $invoice_id = $invoice_id_tmp . "-" . $invoice_no;
            }

            $excel_data = $invoices['meta'];
            $excel_data['invoice_id'] = $invoice_id;
            $excel_data['vendor_name'] = $invoices['booking'][0]['company_name'];
            $excel_data['vendor_address'] = $invoices['booking'][0]['address'];
            $excel_data['sd'] = $start_date;
            $excel_data['ed'] = $end_date;
            $excel_data['invoice_date'] = date("jS M, Y");
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
            $output_file_dir = TMP_FOLDER;
            $output_file = $invoice_id;
            $output_file_excel = $output_file_dir . $output_file . "-detailed.xlsx";
            if (file_exists($output_file_excel)) {
                $res1 = 0;

                log_message('info', __FUNCTION__ . " File exists, deleting it now: " . $output_file_excel);
                echo " File exists, deleting it now: " . $output_file_excel . PHP_EOL;

                system(" chmod 777 " . $output_file_excel, $res1);

                $f_del = unlink($output_file_excel);
                log_message('info', __FUNCTION__ . " File deleted: " . $f_del);
                echo " File deleted: " . $f_del . PHP_EOL;
            }

            //for xlsx: excel, for xls: excel2003
            $R->render('excel', $output_file_excel);

            system(" chmod 777 " . $output_file_excel, $res1);

            if ($details['invoice_type'] === "final") {

                $rm_details = $this->vendor_model->get_rm_sf_relation_by_sf_id($invoices['booking'][0]['id']);
                $rem_email_id = "";
                if (!empty($rm_details)) {
                    $rem_email_id = ", " . $rm_details[0]['official_email'];
                }
                $to = $invoices['booking'][0]['owner_email'] . ", " . $invoices['booking'][0]['primary_contact_email'];
                $subject = "247around - " . $invoices['booking'][0]['company_name'] .
                        " - Cash Invoice for period: " . $start_date . " to " . $end_date;
                $cc = NITS_ANUJ_EMAIL_ID . $rem_email_id;


                $this->email->clear(TRUE);
                $this->email->from('billing@247around.com', '247around Team');
                $this->email->to($to);
                $this->email->cc($cc);
                //attach detailed invoice
                $this->email->attach($output_file_excel, 'attachment');
                //attach mail invoice
                $this->email->attach($output_file_dir . $invoice_id . ".pdf", 'attachment');
                $message = "Dear Partner," . "<br/><br/>Please find attached CASH invoice. Please do <strong>Reply All</strong> for raising any query or concern regarding the invoice.";
                $message .= "<br/><br/>Thanks,<br/>247around Team";
                $this->email->message($message);
                $this->email->subject($subject);
                $mail_ret = $this->email->send();

                if ($mail_ret) {
                    log_message('info', __METHOD__ . ": Mail sent successfully");
                    echo "Mail sent successfully..............." . PHP_EOL;
                } else {
                    log_message('info', __METHOD__ . ": Mail could not be sent");
                    echo "Mail could not be sent..............." . PHP_EOL;
                }

                //Add RM email id in CC as well
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

                $this->notify->send_sms_msg91($sms);

                //Upload Excel files to AWS
                $bucket = BITBUCKET_DIRECTORY;
                $directory_xls = "invoices-excel/" . $invoice_id . "-detailed.xlsx";
                $invoice_upload = $this->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                if ($invoice_upload) {
                    log_message('info', __METHOD__ . ": Cash Detailed Invoices uploaded to S3");
                    echo " Cash Detailed Invoices uploaded to S3";
                } else {
                    $invoice_upload = $this->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                    if ($invoice_upload) {
                        log_message('info', __METHOD__ . ": Cash Detailed Invoices uploaded to S3");
                        echo " Cash Detailed Invoices uploaded to S3";
                    } else {
                        log_message('info', __METHOD__ . ": Cash Detailed Invoices is not uploaded to S3");
                        echo " Cash Detailed Invoices is not uploaded to S3";
                    }
                }
                $up_charges = 0;
                $up_distance = 0;
                $up_total_booking = 0;
                if (isset($invoices['upcountry'])) {

                    $up_charges = $invoices['upcountry']['total_upcountry_price'];
                    $up_distance = $invoices['upcountry']['total_distance'];
                    $up_total_booking = $invoices['upcountry']['total_booking'];
                }

                //Save this invoice info in table
                $invoice_details = array(
                    'invoice_id' => $invoice_id,
                    'type' => 'Cash',
                    'type_code' => 'A',
                    'vendor_partner' => 'vendor',
                    'vendor_partner_id' => $invoices['booking'][0]['id'],
                    'invoice_file_excel' => $invoice_id . '.pdf',
                    'invoice_detailed_excel' => $invoice_id . '-detailed.xlsx',
                    'invoice_date' => date("Y-m-d"),
                    'from_date' => date("Y-m-d", strtotime($from_date)),
                    'to_date' => date("Y-m-d", strtotime($to_date)),
                    'num_bookings' => $count,
                    'total_service_charge' => $excel_data['r_sc'] - $up_charges,
                    'total_additional_service_charge' => $excel_data['r_asc'],
                    'parts_cost' => $excel_data['r_pc'],
                    'vat' => 0, //No VAT here in Cash invoice
                    'total_amount_collected' => $excel_data['r_total'],
                    'rating' => $excel_data['t_rating'],
                    'around_royalty' => $excel_data['r_total'],
                    'upcountry_price' => $up_charges,
                    'upcountry_distance' => $up_distance,
                    'upcountry_booking' => $up_total_booking,
                    //Service tax which needs to be paid
                    'service_tax' => $excel_data['r_st'],
                    'invoice_date' => date('Y-m-d'),
                    //Amount needs to be collected from Vendor
                    'amount_collected_paid' => $excel_data['r_total'],
                    //Mail has not 
                    'mail_sent' => $mail_ret,
                    //SMS has been sent or not
                    'sms_sent' => 1,
                    //Add 1 month to end date to calculate due date
                    'due_date' => date("Y-m-d", strtotime($to_date . "+1 month")),
                    //add agent_id
                    'agent_id' => $details['agent_id']
                );

                $this->invoices_model->action_partner_invoice($invoice_details);

                log_message('info', __METHOD__ . ': Invoice ' . $invoice_id . ' details  entered into invoices table');

                /*
                 * Update booking-invoice table to capture this new invoice against these bookings.
                 * Since this is a type 'Cash' invoice, it would be stored as a vendor-debit invoice.
                 */

                $this->update_invoice_id_in_unit_details($invoices['booking'], $invoice_id, $details['invoice_type'], "vendor_cash_invoice_id");
            }

            // Store Cash Invoices details
            $invoice_sc_details[$invoices['booking'][0]['id']]['cash_file_name'] = $output_file_excel;
            $invoice_sc_details[$invoices['booking'][0]['id']]['cash_amount'] = $excel_data['r_total'];
            $invoice_sc_details[$invoices['booking'][0]['id']]['cash_invoice_id'] = $invoice_id;
            $invoice_sc_details[$invoices['booking'][0]['id']]['start_date'] = $start_date;
            $invoice_sc_details[$invoices['booking'][0]['id']]['end_date'] = $end_date;

            if ($details['invoice_type'] !== "final") {
                if (file_exists($output_file_excel)) {
                    system('zip ' . $output_file_dir . $invoice_id . '.zip ' . $output_file_excel . ' ' . $output_file_dir . $output_file . ".xlsx "
                            . $output_file_dir . $output_file . ".pdf");

                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header("Content-Disposition: attachment; filename=\"$invoice_id.zip\"");
                    readfile($output_file_dir . $invoice_id . '.zip');
                }
            }

            //Dont delete them for some time
            exec("rm -rf " . escapeshellarg($output_file_excel));
            exec("rm -rf " . escapeshellarg($output_file_dir . $invoice_id . ".xlsx"));
            log_message('info', __METHOD__ . ' Exit ');

            unset($excel_data);
        } else {
            log_message('info', __FUNCTION__ . "=> Data Not Found for Cash Detailed Invoice" . print_r($details));

            echo "Data Not Found for Cash Detailed Invoice" . PHP_EOL;
        }

        return $invoice_sc_details;
    }

    /**
     * @desc: This Method used to update invoice id in unit_details
     * @param $invoices_data Array Misc data about Invoice
     * @param $invoice_id String Invoice ID
     * @param $invoice_type String Invoice Type (draft/final)
     */
    function update_invoice_id_in_unit_details($invoices_data, $invoice_id, $invoice_type, $unit_column) {
        log_message('info', __METHOD__ . ': Reset Invoice id ' . " invoice id " . $invoice_id);

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

    /**
     * @desc: This is used to generates foc type invoices for vendor
     * @param: Array()
     * @return: Array (booking id)
     */
    function generate_foc_details_invoices_for_vendors($invoices_data, $details, $is_regenerate) {
        log_message('info', __FUNCTION__ . '=> Entering...');
        $custom_date = explode("-", $details['date_range']);
        $from_date = $custom_date[0];
        $to_date = $custom_date[1];
        $unique_booking_foc = array();
        $invoice_sc_details = array();
        $is_upcountry = FALSE;
        $total_upcountry_booking = 0;
        $upcountry_rate = 0;
        $upcountry_distance = 0;
        $template = 'Vendor_Settlement_Template-FoC-v4.xlsx';
        if (isset($invoices_data['upcountry_details'])) {
            $template = 'Vendor_Settlement_Template-FoC-upcountry-v4.xlsx';
            $total_upcountry_booking = $invoices_data['upcountry_details'][0]['total_booking'];
            $upcountry_rate = $invoices_data['upcountry_details'][0]['sf_upcountry_rate'];
            $upcountry_distance = $invoices_data['upcountry_details'][0]['total_distance'];
            $is_upcountry = TRUE;
        }
        $to_date1 = date('Y-m-d', strtotime('+1 day', strtotime($to_date)));
        $penalty_data = $this->penalty_model->add_penalty_in_invoice($details['vendor_partner_id'], $from_date, $to_date1, "", $is_regenerate);
        $credit_penalty = $this->penalty_model->get_removed_penalty($details['vendor_partner_id'], $from_date, "");
        $courier_charges = $this->invoices_model->get_sf_courier_charges($details['vendor_partner_id'], $from_date, $to_date1);
        // directory
        $templateDir = __DIR__ . "/../excel-templates/";
        $invoices = $invoices_data['invoice_details'];

        if (!empty($invoices)) {
            $total_inst_charge = 0;
            $total_st_charge = 0;
            $total_stand_charge = 0;
            $total_vat_charge = 0;

            if (isset($details['invoice_id'])) {
                log_message('info', __FUNCTION__ . " Re-Generate Invoice id " . $details['invoice_id']);
                $invoice_id = $details['invoice_id'];
            } else {
                if ((strcasecmp($invoices[0]['state'], "DELHI") == 0) ||
                        (strcasecmp($invoices[0]['state'], "New Delhi") == 0)) {
                    //If matched return 0;
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
                $invoice_no_temp = $this->invoices_model->get_invoices_details($where);
                $invoice_no = 1;
                if (!empty($invoice_no_temp)) {
                    $explode = explode($invoice_id_tmp . "-", $invoice_no_temp[0]['invoice_id']);
                    $invoice_no = $explode[1] + 1;
                }

                $invoice_id = $invoice_id_tmp . "-" . $invoice_no;
                log_message('info', __FUNCTION__ . " Generate Invoice id " . $invoice_id);
            }

            // Calculate charges
            for ($j = 0; $j < count($invoices); $j++) {
                $total_inst_charge += $invoices[$j]['vendor_installation_charge'];
                $total_st_charge += $invoices[$j]['vendor_st'];
                $total_stand_charge += $invoices[$j]['vendor_stand'];
                $total_vat_charge += $invoices[$j]['vendor_vat'];
                $invoices[$j]['amount_paid'] = round(($invoices[$j]['vendor_installation_charge'] + $invoices[$j]['vendor_st'] + $invoices[$j]['vendor_stand'] + $invoices[$j]['vendor_vat']), 0);
            }
            $total_courier_charges = 0;
            if (!empty($courier_charges)) {
                $total_courier_charges = (array_sum(array_column($courier_charges, 'courier_charges_by_sf')));
            }

            $t_total = $total_inst_charge + $total_stand_charge + $total_st_charge + $total_vat_charge;

            $tds_array = $this->check_tds_sc($invoices[0], $total_inst_charge + $total_st_charge);
            $tds = $tds_array['tds'];
            $tds_tax_rate = $tds_array['tds_rate'];
            $tds_per_rate = $tds_array['tds_per_rate'];

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
            $end_date = date("jS M, Y", strtotime($to_date));

            log_message('info', 'Service Centre: ' . $invoices[0]['id'] . ', Count: ' . $count);

            //set config for report
            $config = array(
                'template' => $template,
                'templateDir' => $templateDir
            );

            //load template
            $R = new PHPReport($config);

            // stores charges
            $excel_data = array(
                't_ic' => $total_inst_charge,
                't_st' => $total_st_charge,
                't_stand' => $total_stand_charge,
                't_vat' => $total_vat_charge,
                't_total' => $t_total,
                't_rating' => $invoices[0]['avg_rating'],
                'tds' => round($tds, 2),
                'tds_tax_rate' => $tds_tax_rate,
                't_vp_w_tds' => round($t_total - $tds, 0) // vendor payment with TDS
            );

            $total_upcountry_price = 0;
            if ($is_upcountry) {

                $total_upcountry_price = $invoices_data['upcountry_details'][0]['total_upcountry_price'];
            } else {
                $invoices_data['upcountry_details'] = array();
            }


            $penalty_amount = (array_sum(array_column($penalty_data, 'p_amount')));
            $cr_penalty_amount = (array_sum(array_column($credit_penalty, 'p_amount')));
            $excel_data['total_penalty_amount'] = -$penalty_amount;
            $excel_data['cr_total_penalty_amount'] = $cr_penalty_amount;
            $excel_data['total_upcountry_price'] = round($total_upcountry_price, 2);
            $excel_data['total_courier_charges'] = round($total_courier_charges, 2);
            $t_vp_w_tds = $excel_data['t_vp_w_tds'] + $excel_data['total_upcountry_price'] + $excel_data['cr_total_penalty_amount'] + $excel_data['total_courier_charges'] - $penalty_amount;
            if ($t_vp_w_tds >= 0) {
                $excel_data['t_vp_w_tds'] = $t_vp_w_tds;
            } else if ($t_vp_w_tds < 0) {
                $excel_data['t_vp_w_tds'] = abs($t_vp_w_tds) . "(DR)";
            }


            $excel_data['invoice_id'] = $invoice_id;
            $excel_data['vendor_name'] = $invoices[0]['company_name'];
            $excel_data['vendor_address'] = $invoices[0]['address'];
            $excel_data['sd'] = $start_date;
            $excel_data['ed'] = $end_date;
            $excel_data['invoice_date'] = date("jS M, Y");
            $excel_data['count'] = $count;
            $excel_data['tin'] = $invoices[0]['tin'];
            $excel_data['service_tax_no'] = $invoices[0]['service_tax_no'];

            $excel_data['msg'] = 'Thanks 247around Partner for your support, we completed ' . $count .
                    ' bookings with you from ' . $start_date . ' to ' . $end_date .
                    '. Total transaction value for the bookings was Rs. ' . round($excel_data['t_total'], 0) .
                    '. Your rating for completed bookings is ' . round($excel_data['t_rating'], 0) .
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
                array(
                    'id' => 'upcountry',
                    'repeat' => true,
                    'data' => $invoices_data['upcountry_details']
                ),
                array(
                    'id' => 'penalty',
                    'repeat' => true,
                    'data' => $penalty_data
                ),
                array(
                    'id' => 'cr_penalty',
                    'repeat' => true,
                    'data' => $credit_penalty
                ),
                array(
                    'id' => 'courier',
                    'repeat' => true,
                    'data' => $courier_charges
                ),
                    )
            );

            //Get populated XLS with data
            $output_file_dir = TMP_FOLDER;
            $output_file = $invoice_id;
            $output_file_excel = $output_file_dir . $output_file . "-detailed.xlsx";
            $res1 = 0;
            if (file_exists($output_file_excel)) {

                system(" chmod 777 " . $output_file_excel, $res1);
                unlink($output_file_excel);
            }

            //for xlsx: excel, for xls: excel2003
            $R->render('excel', $output_file_excel);
            $res2 = 0;
            system(" chmod 777 " . $output_file_excel, $res2);
            log_message('info', __FUNCTION__ . " Excel File Created " . $output_file_excel);

            if ($details['invoice_type'] === "final") {
                $rm_details = $this->vendor_model->get_rm_sf_relation_by_sf_id($invoices[0]['id']);
                $rem_email_id = "";
                if (!empty($rm_details)) {
                    $rem_email_id = ", " . $rm_details[0]['official_email'];
                }

                $from = 'billing@247around.com';
                $to = $invoices[0]['owner_email'] . ", " . $invoices[0]['primary_contact_email'];
                $subject = "247around - " . $invoices[0]['company_name'] . " - FOC Invoice for period: " . $start_date . " to " . $end_date;
                $cc = NITS_ANUJ_EMAIL_ID . $rem_email_id;
                $this->email->from($from);
                $this->email->to($to);
                $this->email->cc($cc);
                $this->email->subject($subject);
                $this->email->attach($output_file_excel, 'attachment');
                $this->email->attach($output_file_dir . $output_file . ".pdf", 'attachment');
                $message = "Dear Partner," . "<br/><br/>Please find attached FOC invoice. Please do <strong>Reply All</strong> for raising any query or concern regarding the invoice.";
                $message .= "<br/><br/>Thanks,<br/>247around Team";
                $this->email->message($message);
                $mail_ret = $this->email->send();

                if ($mail_ret) {
                    log_message('info', __METHOD__ . ": Mail sent successfully");
                    echo "Mail sent successfully..............." . PHP_EOL;
                } else {
                    log_message('info', __METHOD__ . ": Mail could not be sent");
                    echo "Mail could not be sent..............." . PHP_EOL;
                }
            }

            if ($details['invoice_type'] === "final") {
                log_message('info', __FUNCTION__ . " Final");

                //Send SMS to PoC/Owner
                $sms['tag'] = "vendor_invoice_mailed";
                $sms['smsData']['type'] = 'FOC';
                $sms['smsData']['month'] = date('M Y', strtotime($start_date));
                $sms['smsData']['amount'] = round($excel_data['t_total'], 0);
                $sms['phone_no'] = $invoices[0]['owner_phone_1'];
                $sms['booking_id'] = "";
                $sms['type'] = "vendor";
                $sms['type_id'] = $invoices[0]['id'];

                $this->notify->send_sms_msg91($sms);
                log_message('info', __FUNCTION__ . " SMS Sent");
                //Upload Excel files to AWS
                $bucket = BITBUCKET_DIRECTORY;
                $directory_xls = "invoices-excel/" . $invoice_id . "-detailed.xlsx";

                $foc_detailed = $this->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                if ($foc_detailed) {

                    log_message('info', __METHOD__ . ": Invoices uploaded to S3 " . $invoice_id . "-detailed.xlsx");
                    echo ": Invoices uploaded to S3 " . $invoice_id . "-detailed.xlsx";
                } else {

                    $foc_detailed = $this->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                    if ($foc_detailed) {

                        log_message('info', __METHOD__ . ": Invoices uploaded to S3 " . $invoice_id . "-detailed.xlsx");
                        echo ": Invoices uploaded to S3 " . $invoice_id . "-detailed.xlsx";
                    } else {

                        log_message('info', __METHOD__ . ": Invoices Not uploaded to S3 " . $invoice_id . "-detailed.xlsx");
                        echo ": Invoices not uploaded to S3 " . $invoice_id . "-detailed.xlsx";
                    }
                }
                //Save this invoice info in table
                $invoice_details = array(
                    'invoice_id' => $invoice_id,
                    'type' => 'FOC',
                    'type_code' => 'B',
                    'vendor_partner' => 'vendor',
                    'vendor_partner_id' => $invoices[0]['id'],
                    'invoice_file_excel' => $invoice_id . '.pdf',
                    'invoice_detailed_excel' => $invoice_id . '-detailed.xlsx',
                    //'invoice_file_pdf' => $output_file . '.pdf',
                    'invoice_date' => date("Y-m-d"),
                    'from_date' => date("Y-m-d", strtotime($from_date)),
                    'to_date' => date("Y-m-d", strtotime($to_date)),
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
                    'amount_collected_paid' => (0 - $t_vp_w_tds),
                    //Mail has not sent
                    'mail_sent' => $mail_ret,
                    'tds_rate' => $tds_per_rate,
                    //SMS has been sent or not
                    'sms_sent' => 1,
                    'upcountry_booking' => $total_upcountry_booking,
                    'upcountry_rate' => $upcountry_rate,
                    'upcountry_price' => $excel_data['total_upcountry_price'],
                    'upcountry_distance' => $upcountry_distance,
                    'penalty_amount' => $penalty_amount,
                    'penalty_bookings_count' => array_sum(array_column($penalty_data, 'penalty_times')),
                    'credit_penalty_amount' => $cr_penalty_amount,
                    'credit_penalty_bookings_count' => array_sum(array_column($credit_penalty, 'penalty_times')),
                    'courier_charges' => $total_courier_charges,
                    'invoice_date' => date('Y-m-d'),
                    //Add 1 month to end date to calculate due date
                    'due_date' => date("Y-m-d", strtotime($to_date . "+1 month")),
                    //add agent id
                    'agent_id' => $details['agent_id']
                );

                // insert invoice details into vendor partner invoices table
                $this->invoices_model->action_partner_invoice($invoice_details);
                //Update Penalty Amount
                foreach ($penalty_data as $value) {
                    $this->penalty_model->update_penalty_any(array('booking_id' => $value['booking_id']), array('foc_invoice_id' => $invoice_id));
                }

                log_message('info', __METHOD__ . ': Invoice ' . $invoice_id . ' details  entered into invoices table');

                /*
                 * Update booking-invoice table to capture this new invoice against these bookings.
                 * Since this is a type B invoice, it would be stored as a vendor-credit invoice.
                 */

                $this->update_invoice_id_in_unit_details($invoices, $invoice_id, $details['invoice_type'], "vendor_foc_invoice_id");
            }

            // Store foc invoices
            $invoice_sc_details[$invoices[0]['id']]['foc_invoice_file_name'] = $output_file_excel;
            $invoice_sc_details[$invoices[0]['id']]['foc_amount'] = $t_total;
            $invoice_sc_details[$invoices[0]['id']]['foc_invoice_id'] = $invoice_id;
            $invoice_sc_details[$invoices[0]['id']]['start_date'] = $start_date;
            $invoice_sc_details[$invoices[0]['id']]['end_date'] = $end_date;

            if ($details['invoice_type'] !== "final") {
                if (file_exists($output_file_excel)) {
                    system('zip ' . $output_file_dir . $invoice_id . '.zip ' . $output_file_excel . ' ' . $output_file_dir . $output_file . ".xlsx " .
                            $output_file_dir . $output_file . ".pdf");

                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header("Content-Disposition: attachment; filename=\"$invoice_id.zip\"");
                    readfile($output_file_dir . $invoice_id . '.zip');
                }
                exec("rm -rf " . escapeshellarg($output_file_dir . $invoice_id . '.zip'));
            }
            unset($excel_data);

            exec("rm -rf " . escapeshellarg($output_file_excel));
            exec("rm -rf " . escapeshellarg($output_file_dir . $output_file . ".xlsx"));
        } else {
            log_message('info', __FUNCTION__ . "Exit data not found " . print_r($details, TRUE));
        }


        return $invoice_sc_details;
    }

    /**
     * @desc: This Method loads invoice form
     */
    function get_invoices_form() {
        $data['vendor_partner'] = "vendor";
        $data['id'] = "";
        $this->load->view('employee/header/' . $this->session->userdata('user_group'));
        $this->load->view('employee/get_invoices_form', $data);
    }

    /**
     * @desc: This method is used to generate invoices for partner or vendor.
     * This methd is used to get data from Form.
     */
    function process_invoices_form() {
        log_message('info', __FUNCTION__ . " Entering......");
        $details['vendor_partner'] = $this->input->post('partner_vendor');
        $details['invoice_type'] = $this->input->post('invoice_version');
        $details['vendor_partner_id'] = $this->input->post('partner_vendor_id');
        $details['date_range'] = $this->input->post('daterange');
        $details['vendor_invoice_type'] = $this->input->post('vendor_invoice_type');
        $details['agent_id'] = $this->session->userdata('id');

        $status = $this->generate_vendor_partner_invoices($details);
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
        log_message('info', __FUNCTION__ . " Entering......");
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
        log_message('info', __FUNCTION__ . " Entering......");


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
        $where = " `invoice_id` = '$invoice_id'";
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
        log_message('info', __FUNCTION__ . '=> Entering... Partner Id' . $partner_id . " date range " . $date_range . " invoice type " . $invoice_type);
        $custom_date = explode("-", $date_range);
        $from_date = $custom_date[0];
        $to_date = $custom_date[1];

        if ($partner_id == "All") {
            $partner = $this->partner_model->get_all_partner_source();
            foreach ($partner as $value) {
                log_message('info', __FUNCTION__ . '=> Partner Id ' . $value['partner_id']);
                $invoice_id = $this->create_partner_invoice($value['partner_id'], $from_date, $to_date, $invoice_type);
                if ($invoice_id) {
                    $this->create_partner_invoices_detailed($value['partner_id'], $from_date, $to_date, $invoice_type, $invoice_id,$agent_id);
                }
            }
        } else {
            log_message('info', __FUNCTION__ . '=> Partner Id ' . $partner_id);
            $invoice_id = $this->create_partner_invoice($partner_id, $from_date, $to_date, $invoice_type);
            if ($invoice_id) {
                return $this->create_partner_invoices_detailed($partner_id, $from_date, $to_date, $invoice_type, $invoice_id,$agent_id);
            } else {
                return False;
            }
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

                    $vendor_details = $this->vendor_model->getActiveVendor('', 0);
                    foreach ($vendor_details as $value) {
                        $details['vendor_partner_id'] = $value['id'];

                        log_message('info', __FUNCTION__ . " Preparing CASH Invoice for Vendor: " . $details['vendor_partner_id']);
                        echo " Preparing CASH Invoice for Vendor: " . $details['vendor_partner_id'] . PHP_EOL;

                        //Prepare main invoice first
                        $details['invoice_id'] = $this->generate_vendor_cash_invoice($details, $is_regenerate);

                        //Invoice made successfully
                        if ($details['invoice_id']) {
                            log_message('info', 'Invoice made successfully, generating detailed annexure now');
                            echo 'Invoice made successfully' . PHP_EOL;

                            //Generate detailed annexure now
                            $data = $this->invoices_model->get_vendor_cash_detailed($details['vendor_partner_id'], $details['date_range'], $is_regenerate);
                            $this->generate_cash_details_invoices_for_vendors($data, $details);
                        } else {

                            echo " Data Not found for vendor: " . $details['vendor_partner_id'];

                            log_message('info', __FUNCTION__ . " Data Not found for vendor: " . $details['vendor_partner_id']);
                        }
                    }
                } else {
                    echo " Preparing CASH Invoice  Vendor: " . $details['vendor_partner_id'];

                    log_message('info', __FUNCTION__ . ": Preparing CASH Invoice Vendor Id: " . $details['vendor_partner_id']);

                    //Prepare main invoice first
                    $details['invoice_id'] = $this->generate_vendor_cash_invoice($details, $is_regenerate);
                    if ($details['invoice_id']) {
                        //Generate detailed annexure now
                        $data = $this->invoices_model->get_vendor_cash_detailed($details['vendor_partner_id'], $details['date_range'], $is_regenerate);
                        $this->generate_cash_details_invoices_for_vendors($data, $details);
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

                    $vendor_details = $this->vendor_model->getActiveVendor('', 0);
                    echo " Preparing FOC Invoice  Vendor: " . $details['vendor_partner_id'];
                    foreach ($vendor_details as $value) {
                        $details['vendor_partner_id'] = $value['id'];
                        log_message('info', __FUNCTION__ . ": Preparing FOC Invoice Vendor Id: " . $details['vendor_partner_id']);
                        //Prepare main invoice first
                        $details['invoice_id'] = $this->generate_vendor_foc_invoice($details, $is_regenerate);

                        if ($details['invoice_id']) {
                            //Generate detailed annexure now                
                            $data = $this->invoices_model->generate_vendor_foc_detailed_invoices($details['vendor_partner_id'], $details['date_range'], $is_regenerate);
                            $this->generate_foc_details_invoices_for_vendors($data, $details, $is_regenerate);
                        } else {
                            echo "<script>alert('Data Not Found');</script>";
                            echo " Data Not found for vendor: " . $details['vendor_partner_id'];
                            log_message('info', __FUNCTION__ . " Data Not found for vendor: " . $details['vendor_partner_id']);
                        }
                    }
                } else {
                    //Prepare main invoice first
                    $details['invoice_id'] = $this->generate_vendor_foc_invoice($details, $is_regenerate);
                    log_message('info', __FUNCTION__ . ": Preparing FOC Invoice Vendor Id: " . $details['vendor_partner_id']);
                    echo " Preparing FOC Invoice  Vendor: " . $details['vendor_partner_id'];
                    if ($details['invoice_id']) {
                        //Generate detailed annexure now                
                        $data = $this->invoices_model->generate_vendor_foc_detailed_invoices($details['vendor_partner_id'], $details['date_range'], $is_regenerate);
                        $this->generate_foc_details_invoices_for_vendors($data, $details, $is_regenerate);
                        return TRUE;
                    } else {
                        echo "<script>alert('Data Not Found');</script>";
                        echo " Data Not found for vendor: " . $details['vendor_partner_id'];
                        log_message('info', __FUNCTION__ . " Data Not found for vendor: " . $details['vendor_partner_id']);
                        return FALSE;
                    }
                }
                break;

            case "brackets":
                log_message('info', __FUNCTION__ . " Brackets");
                //This constant is used to track all vendors selected to avoid sending mail when all vendor +draft is selected
                $vendor_all_flag = 0;
                if ($details['vendor_partner_id'] === 'All') {
                    $vendor_all_flag = 1;
                    $vendor = $this->vendor_model->getActiveVendor('', 0);

                    foreach ($vendor as $value) {
                        log_message('info', __FUNCTION__ . " Brackets Vendor Id: " . $value['id']);
                        $details['vendor_partner_id'] = $value['id'];
                        //Generating and sending invoice to vendors
                        $this->send_brackets_invoice_to_vendors($details, $vendor_all_flag);
                    }
                } else {
                    log_message('info', __FUNCTION__ . " Brackets Vendor Id: " . $details['vendor_partner_id']);
                    //Generating and sending invoice to vendors
                    return $this->send_brackets_invoice_to_vendors($details, $vendor_all_flag);
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
        if ($vendor_partner == 'vendor') {
            $data['service_center'] = $this->vendor_model->getActiveVendor("", 0);
        } else {
            $data['partner'] = $this->partner_model->getpartner();
        }

        $data['vendor_partner_id'] = $vendor_partner_id;
        $data['vendor_partner'] = $vendor_partner;
        $this->load->view('employee/header/' . $this->session->userdata('user_group'));
        $this->load->view('employee/invoices_details', $data);
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
        $to_date1 = date('Y-m-d', strtotime('+1 day', strtotime($to_date)));
        //Making invoice array
        $invoice = $this->inventory_model->get_vendor_bracket_invoices($vendor_id, $from_date, $to_date1);

        if (!empty($invoice)) {
            $invoice[0]['period'] = date("jS M, Y", strtotime($from_date)) . " To " . date('jS M, Y', strtotime($to_date));
            $invoice[0]['today'] = date("jS M, Y");
            if (isset($details['invoice_id'])) {
                log_message('info', __METHOD__ . ": Invoice Id re- geneterated " . $details['invoice_id']);
                $invoice[0]['invoice_number'] = $details['invoice_id'];
            } else {
                if ((strcasecmp($invoice[0]['state'], "DELHI") == 0) ||
                        (strcasecmp($invoice[0]['state'], "New Delhi") == 0)) {
                    //If matched return 0;
                    $type = "T";
                    $invoice[0]['invoice_type'] = "TAX INVOICE";
                } else {
                    $type = "R";
                    $invoice[0]['invoice_type'] = "RETAIL INVOICE";
                }

                $current_month = date('m');
                // 3 means March Month
                if ($current_month > 3) {
                    $financial = date('Y') . "-" . (date('y') + 1);
                } else {
                    $financial = (date('Y') - 1) . "-" . date('y');
                }


                //Make sure it is unique
                $invoice_id_tmp = "Around-" . $type . "-" . $financial . "-" . date("M", strtotime(date($from_date)));
                $where = " `invoice_id` LIKE '%$invoice_id_tmp%'";
                $invoice_no_temp = $this->invoices_model->get_invoices_details($where);
                $invoice_no = 1;
                if (!empty($invoice_no_temp)) {
                    $explode = explode($invoice_id_tmp . "-", $invoice_no_temp[0]['invoice_id']);
                    $invoice_no = $explode[1] + 1;
                }

                $invoice[0]['invoice_number'] = $invoice_id_tmp . "-" . $invoice_no;
            }

            log_message('info', __FUNCTION__ . " Entering......... Invoice Id " . $invoice[0]['invoice_number']);
            $invoice[0]['invoice_date'] = date("jS M, Y");
            $invoice[0]['tax_rate'] = 5.00;
            $invoice[0]['19_24_tax_total'] = $this->booking_model->get_calculated_tax_charge(_247AROUND_BRACKETS_19_24_UNIT_PRICE, $invoice[0]['tax_rate']);
            $invoice[0]['26_32_tax_total'] = $this->booking_model->get_calculated_tax_charge(_247AROUND_BRACKETS_26_32_UNIT_PRICE, $invoice[0]['tax_rate']);
            $invoice[0]['36_42_tax_total'] = $this->booking_model->get_calculated_tax_charge(_247AROUND_BRACKETS_36_42_UNIT_PRICE, $invoice[0]['tax_rate']);
            $invoice[0]['43_tax_total'] = $this->booking_model->get_calculated_tax_charge(_247AROUND_BRACKETS_43_UNIT_PRICE, $invoice[0]['tax_rate']);
            $invoice[0]['19_24_unit_price'] = _247AROUND_BRACKETS_19_24_UNIT_PRICE - $invoice[0]['19_24_tax_total'];
            $invoice[0]['26_32_unit_price'] = _247AROUND_BRACKETS_26_32_UNIT_PRICE - $invoice[0]['26_32_tax_total'];
            $invoice[0]['36_42_unit_price'] = _247AROUND_BRACKETS_36_42_UNIT_PRICE - $invoice[0]['36_42_tax_total'];
            $invoice[0]['43_unit_price'] = _247AROUND_BRACKETS_43_UNIT_PRICE - $invoice[0]['43_tax_total'];

            $invoice[0]['total_brackets'] = $invoice[0]['_19_24_total'] + $invoice[0]['_26_32_total'] + $invoice[0]['_36_42_total'] + $invoice[0]['_43_total'];
            $invoice[0]['t_19_24_unit_price'] = $invoice[0]['_19_24_total'] * $invoice[0]['19_24_unit_price'];
            $invoice[0]['t_26_32_unit_price'] = $invoice[0]['_26_32_total'] * $invoice[0]['26_32_unit_price'];
            $invoice[0]['t_36_42_unit_price'] = $invoice[0]['_36_42_total'] * $invoice[0]['36_42_unit_price'];
            $invoice[0]['t_43_unit_price'] = $invoice[0]['_43_total'] * $invoice[0]['43_unit_price'];
            $invoice[0]['total_part_cost'] = ($invoice[0]['t_19_24_unit_price'] + $invoice[0]['t_26_32_unit_price'] + $invoice[0]['t_36_42_unit_price'] + $invoice[0]['t_43_unit_price']);
            $invoice[0]['part_cost_vat'] = round($invoice[0]['total_part_cost'] * $invoice[0]['tax_rate'] / 100, 2);
            $invoice[0]['sub_total'] = round(($invoice[0]['part_cost_vat'] + $invoice[0]['total_part_cost']), 2);
            $invoice[0]['total'] = round(($invoice[0]['part_cost_vat'] + $invoice[0]['total_part_cost']), 0);
            $invoice[0]['price_inword'] = convert_number_to_words($invoice[0]['total']);

            //Creating excel report
            $output_file = $this->create_vendor_brackets_invoice($invoice[0]);


            if (isset($output_file)) {

                // Sending SMS  to Vendor , adding value in vednor_partner_invoice table when invoice type is FINAL
                if ($invoice_type == 'final') {
                    log_message('info', __FUNCTION__ . " Final");
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

                    $this->notify->send_sms_msg91($sms);
                    log_message('info', __FUNCTION__ . " SMS Sent.....");
                    //Upload Excel files to AWS
                    $bucket = BITBUCKET_DIRECTORY;
                    $directory_xls = "invoices-excel/" . $invoice[0]['invoice_number'] . '.xlsx';
                    $directory_pdf = "invoices-excel/" . $invoice[0]['invoice_number'] . '.pdf';

                    $this->s3->putObjectFile(TMP_FOLDER . $invoice[0]['invoice_number'] . '.xlsx', $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                    $this->s3->putObjectFile(TMP_FOLDER . $invoice[0]['invoice_number'] . '.pdf', $bucket, $directory_pdf, S3::ACL_PUBLIC_READ);

                    //Save this invoice info in table
                    $invoice_details = array(
                        'invoice_id' => $invoice[0]['invoice_number'],
                        'type' => 'Stand',
                        'type_code' => 'A',
                        'vendor_partner' => 'vendor',
                        'vendor_partner_id' => $invoice[0]['vendor_id'],
                        'invoice_file_excel' => $invoice[0]['invoice_number'] . '.xlsx',
                        'invoice_file_pdf' => $invoice[0]['invoice_number'] . '.pdf',
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
                        'invoice_date' => date('Y-m-d'),
                        'tds_amount' => 0.0,
                        'amount_paid' => 0.0,
                        'settle_amount' => 0,
                        'amount_paid' => 0.0,
                        'mail_sent' => 1,
                        'sms_sent' => 1,
                        //Add 1 month to end date to calculate due date
                        'due_date' => date("Y-m-d", strtotime($to_date . "+1 month")),
                        'agent_id' => $details['agent_id']
                    );
                    $this->invoices_model->action_partner_invoice($invoice_details);
                    log_message('info', __FUNCTION__ . " Reset Invoice Id " . $invoice[0]['invoice_number']);
                    $this->inventory_model->update_brackets(array('invoice_id' => NULL), array('invoice_id' => $invoice[0]['invoice_number']));
                    if (strpos($invoice[0]['order_id'], ',') !== FALSE) {
                        $var = explode(",", $invoice[0]['order_id']);
                        foreach ($var as $value) {
                            log_message('info', __FUNCTION__ . " Update invoice id for bracket invoice id " . $invoice[0]['invoice_number'] . " Order Id " . $value);
                            $this->inventory_model->update_brackets(array('invoice_id' => $invoice[0]['invoice_number']), array('order_id' => $value));
                        }
                    } else {
                        log_message('info', __FUNCTION__ . " Update invoice id for bracket invoice id " . $invoice[0]['invoice_number'] . " Order Id " . $invoice[0]['order_id']);
                        $this->inventory_model->update_brackets(array('invoice_id' => $invoice[0]['invoice_number']), array('order_id' => $invoice[0]['order_id']));
                    }
                }

                //Logging success
                log_message('info', __FUNCTION__ . ' Brackets Report invoice has been generated .' . print_r($invoice, TRUE));
                return $output_file;
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
    function create_vendor_brackets_invoice($data) {
        log_message('info', __FUNCTION__ . " Entering......... ");
        $output_file_dir = TMP_FOLDER;
        $output_file = $data['invoice_number'];
        $output_file_name = $output_file . ".xlsx";
        $output_pdf_file_name = $output_file.".pdf";
        $output_file_excel = $output_file_dir . $output_file_name;
        $output_file_pdf = $output_file_dir . $output_pdf_file_name;
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
        if (file_exists($output_file_excel)) {
            $res1 = 0;
            system(" chmod 777 " . $output_file_excel, $res1);
            unlink($output_file_excel);
        }
        $R->load(array(
            'id' => 'invoice',
            'data' => $data
        ));


        $res1 = 0;
        if (file_exists($output_file_excel)) {

            system(" chmod 777 " . $output_file_excel, $res1);
            unlink($output_file_excel);
        }
        $response = $R->render('excel', $output_file_excel);
        system(" chmod 777 " . $output_file_excel, $res1);
        
        //convert excel to pdf
        putenv('PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/opt/node/bin');
        $tmp_path = TMP_FOLDER;
        $tmp_output_file = TMP_FOLDER . 'output_' . __FUNCTION__ . '.txt';
        $cmd = 'echo ' . $tmp_path . ' & echo $PATH & UNO_PATH=/usr/lib/libreoffice & ' .
                '/usr/bin/unoconv --format pdf --output ' . $output_file_pdf . ' ' .
                $output_file_excel . ' 2> ' . $tmp_output_file;
        $output = '';
        $result_var = '';
        exec($cmd, $output, $result_var); 
        
        if ($response == NULL) {
            log_message('info', __FUNCTION__ . " Excel file created " . $output_file_excel);
            return $output_file;
        } else {
            log_message('info', __FUNCTION__ . " Excel file not created ");
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
        $cc = NITS_ANUJ_EMAIL_ID;
        if (!empty($rm_details)) {
            $cc = NITS_ANUJ_EMAIL_ID . ", " . $rm_details[0]['official_email'];
        }

        $to = $vendor_data[0]['primary_contact_email'] . ',' . $vendor_data[0]['owner_email'];
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

        $send_mail = $this->notify->sendEmail($from, $to, $cc, '', 'Brackets Invoice - ' . $vendor_data[0]['name'], $message, $output_file_excel);

        if ($send_mail) {
            log_message('info', __FUNCTION__ . "Bracket invoice sent...");
            return TRUE;
        } else {
            log_message('info', __FUNCTION__ . "Bracket invoice not sent...");
            return FALSE;
        }
    }

    /**
     * @Desc: This function is used to send draft mail of vendor brackets invoice 
     * @parmas: Vendor id, bracket_invoicefile path
     * @return: boolean
     */
    function send_brackets_invoice_draft_mail($vendor_id, $output_file_excel, $from_date) {

        $invoice_month = date('F', strtotime($from_date));

        $vendor_details = $this->vendor_model->getVendorContact($vendor_id);

        $to = ANUJ_EMAIL_ID;
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

        $send_mail = $this->notify->sendEmail($from, $to, '', '', 'DRAFT - Brackets Invoice - ' . $vendor_details[0]['name'], $message, $output_file_excel);
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
    function send_brackets_invoice_to_vendors($details, $vendor_all_flag) {
        log_message('info', __FUNCTION__ . " Entering.........");
        $vendor_id = $details['vendor_partner_id'];
        $date_range = $details['date_range'];
        $invoice_type = $details['invoice_type'];

        $custom_date = explode("-", $date_range);
        $from_date = $custom_date[0];
        // $to_date = $custom_date[1];
        // Call generate_brackets_invoices method to generates Brackets Invoice
        $output_file = $this->generate_brackets_invoices($details);
        //Sending invoice copy to vendors in mail if invocie is being genetared
        if ($output_file) {
            
            $output_file_excel = TMP_FOLDER.$output_file."xlsx";
            $output_file_pdf = TMP_FOLDER.$output_file.".pdf";
            
            log_message('info', __FUNCTION__ . " Excel file return " . $output_file_excel);
            // Not sending mail when vendor_id is all + draft
            if ($vendor_all_flag != 1 && $invoice_type == 'draft') {
                //Sending mail to Anuj along with invoice copy as attachment
                $send_mail = $this->send_brackets_invoice_draft_mail($vendor_id, $output_file_pdf, $from_date);
                if ($send_mail) {
                    //Loggin Success
                    log_message('info', __FUNCTION__ . ' DRAFT INVOICE - Brackets invoice has been sent for the month of ' . $from_date);
                } else {
                    //Loggin Error
                    log_message('info', __FUNCTION__ . ' DRAFT INVOICE - Error in sending Brackets invoice for the month of ' . $from_date);
                }
            }

            //Handling case when invoice type is Final

            if ($invoice_type == 'final') {

                // Sending mail to all vendors POC + OWNER
                $send_mail = $this->send_brackets_invoice_mail($vendor_id, $output_file_pdf, $from_date);
                if ($send_mail) {
                    //Loggin Success
                    log_message('info', __FUNCTION__ . ' Brackets invoice has been sent to the following Vendor ID ' . $vendor_id . ' for the month of ' . $from_date);
                } else {
                    //Loggin Error
                    log_message('info', __FUNCTION__ . ' Error in sending Brackets invoice to the following Vendor ID ' . $vendor_id . ' for the month of ' . $from_date);
                }
            }
            exec("rm -rf " . escapeshellarg($output_file_excel));
            exec("rm -rf " . escapeshellarg($output_file_pdf));
            return true;
        } else {

            log_message('info', __FUNCTION__ . " Excel file Not exist ");
            return false;
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
    function create_partner_invoice($partner_id, $from_date, $to_date, $invoice_type) {
        log_message('info', __FUNCTION__ . ' Entering.......');
        $invoices = $this->invoices_model->generate_partner_invoice($partner_id, $from_date, $to_date);
        if (!empty($invoices)) {

            $template = 'partner_invoice_Main_v3.xlsx';
            // directory
            $templateDir = __DIR__ . "/../excel-templates/";

            $config = array(
                'template' => $template,
                'templateDir' => $templateDir
            );
            $invoices['meta']['sd'] = date("jS M, Y", strtotime($from_date));
            $invoices['meta']['ed'] = date('jS M, Y', strtotime($to_date));
            $invoices['meta']['invoice_date'] = date("jS M, Y");
            if ((strcasecmp($invoices['booking'][0]['state'], "DELHI") == 0) ||
                    (strcasecmp($invoices['booking'][0]['state'], "New Delhi") == 0)) {
                //If matched return 0;
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
            $invoice_no_temp = $this->invoices_model->get_invoices_details($where);
            $invoice_no = 1;
            if (!empty($invoice_no_temp)) {
                $explode = explode($invoice_id_tmp . "-", $invoice_no_temp[0]['invoice_id']);
                $invoice_no = $explode[1] + 1;
            }

            $invoices['meta']['invoice_id'] = $invoice_id_tmp . "-" . $invoice_no;

            log_message('info', __FUNCTION__ . ' Invoice id ' . $invoices['meta']['invoice_id']);
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

            $output_file_excel = TMP_FOLDER . $invoices['meta']['invoice_id'] . ".xlsx";
            $res1 = 0;
            if (file_exists($output_file_excel)) {

                system(" chmod 777 " . $output_file_excel, $res1);
                unlink($output_file_excel);
            }
            $R->render('excel', $output_file_excel);
            log_message('info', __FUNCTION__ . ' File created ' . $output_file_excel);
            system(" chmod 777 " . $output_file_excel, $res1);
            //convert excel to pdf
            $output_file_pdf = TMP_FOLDER . $invoices['meta']['invoice_id'] . ".pdf";

            putenv('PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/opt/node/bin');
            $tmp_path = TMP_FOLDER;
            $tmp_output_file = TMP_FOLDER . 'output_' . __FUNCTION__ . '.txt';
            $cmd = 'echo ' . $tmp_path . ' & echo $PATH & UNO_PATH=/usr/lib/libreoffice & ' .
                    '/usr/bin/unoconv --format pdf --output ' . $output_file_pdf . ' ' .
                    $output_file_excel . ' 2> ' . $tmp_output_file;
            $output = '';
            $result_var = '';
            exec($cmd, $output, $result_var);

//            $this->email->clear(TRUE);
//            $this->email->from('billing@247around.com', '247around Team');
//            $to = ANUJ_EMAIL_ID;
//            $subject = "DRAFT INVOICE (SUMMARY) - 247around - " . $invoices['meta']['company_name'];
//
//            $this->email->to($to);
//            $this->email->subject($subject);
//            $this->email->attach($output_file_excel, 'attachment');
//
//            $mail_ret = $this->email->send();
//
//            if ($mail_ret) {
//                log_message('info', __METHOD__ . ": Mail sent successfully");
//                echo "Mail sent successfully..............." . PHP_EOL;
//            } else {
//                log_message('info', __METHOD__ . ": Mail could not be sent");
//                echo "Mail could not be sent..............." . PHP_EOL;
//            }


            log_message('info', __FUNCTION__ . ' return with invoice id' . $invoices['meta']['invoice_id']);
            return $invoices['meta']['invoice_id'];
        } else {
            log_message('info', __FUNCTION__ . ' Data Not Found');
            echo "Data Not found";
            return FALSE;
        }
    }

    /**
     * @desc: This method is used to generate vendor Foc invoice and return invoice id
     * @param Array $details
     */
    function generate_vendor_foc_invoice($details, $is_regenerate) {
        log_message('info', __FUNCTION__ . "Entering...");
        $vendor_id = $details['vendor_partner_id'];
        $custom_date = explode("-", $details['date_range']);
        $from_date = $custom_date[0];
        $to_date = $custom_date[1];
        $invoice_type = $details['invoice_type'];
        $invoices = $this->invoices_model->get_vendor_foc_invoice($vendor_id, $from_date, $to_date, $is_regenerate);

        if (!empty($invoices)) {

            $template = 'Vendor_Settlement_Template-FoC-v5.xlsx';
            // directory
            $templateDir = __DIR__ . "/../excel-templates/";

            $config = array(
                'template' => $template,
                'templateDir' => $templateDir
            );

            $invoices['meta']['sd'] = date("jS M, Y", strtotime($from_date));
            $invoices['meta']['ed'] = date('jS M, Y', strtotime($to_date));
            $invoices['meta']['invoice_date'] = date("jS M, Y");
            if (isset($details['invoice_id'])) {
                log_message('info', __METHOD__ . ": Invoice Id re- geneterated " . $details['invoice_id']);
                $invoices['meta']['invoice_id'] = $details['invoice_id'];
                if ((strcasecmp($invoices['booking'][0]['state'], "DELHI") == 0) ||
                        (strcasecmp($invoices['booking'][0]['state'], "New Delhi") == 0)) {
                    //If matched return 0;
                    $invoice_version = "T";
                    $invoices['meta']['invoice_type'] = "TAX INVOICE";
                } else {
                    $invoice_version = "R";
                    $invoices['meta']['invoice_type'] = "RETAIL INVOICE";
                }
            } else {
                if ((strcasecmp($invoices['booking'][0]['state'], "DELHI") == 0) ||
                        (strcasecmp($invoices['booking'][0]['state'], "New Delhi") == 0)) {
                    //If matched return 0;
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
                $invoice_no_temp = $this->invoices_model->get_invoices_details($where);
                $invoice_no = 1;
                if (!empty($invoice_no_temp)) {
                    $explode = explode($invoice_id_tmp . "-", $invoice_no_temp[0]['invoice_id']);
                    $invoice_no = $explode[1] + 1;
                }

                $invoices['meta']['invoice_id'] = $invoice_id_tmp . "-" . $invoice_no;
                log_message('info', __METHOD__ . ": Invoice Id geneterated "
                        . $invoices['meta']['invoice_id']);
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

            $output_file_excel = TMP_FOLDER . $invoices['meta']['invoice_id'] . ".xlsx";

            $res1 = 0;
            if (file_exists($output_file_excel)) {

                system(" chmod 777 " . $output_file_excel, $res1);
                unlink($output_file_excel);
            }

            $R->render('excel', $output_file_excel);

            log_message('info', __METHOD__ . ": Excel FIle generated " . $output_file_excel);
            $res2 = 0;
            system(" chmod 777 " . $output_file_excel, $res2);

            //convert excel to pdf
            $output_file_pdf = TMP_FOLDER . $invoices['meta']['invoice_id'] . ".pdf";

            putenv('PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/opt/node/bin');
            $tmp_path = TMP_FOLDER;
            $tmp_output_file = TMP_FOLDER . 'output_' . __FUNCTION__ . '.txt';
            $cmd = 'echo ' . $tmp_path . ' & echo $PATH & UNO_PATH=/usr/lib/libreoffice & ' .
                    '/usr/bin/unoconv --format pdf --output ' . $output_file_pdf . ' ' .
                    $output_file_excel . ' 2> ' . $tmp_output_file;
            $output = '';
            $result_var = '';
            exec($cmd, $output, $result_var);


            if ($invoice_type == "final") {
                log_message('info', __METHOD__ . ": Invoice type Final");

                $bucket = BITBUCKET_DIRECTORY;
                $directory_xls = "invoices-excel/" . $invoices['meta']['invoice_id'] . ".xlsx";
                $directory_pdf = "invoices-excel/" . $invoices['meta']['invoice_id'] . ".pdf";

                $foc_upload = $this->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                $foc_upload_pdf = $this->s3->putObjectFile($output_file_pdf, $bucket, $directory_pdf, S3::ACL_PUBLIC_READ);
                if ($foc_upload_pdf) {
                    log_message('info', __METHOD__ . ": Main FOC PDF Invoice File uploaded to s3");
                    echo "Main FOC PDF Invoice File uploaded to s3";
                } else {
                    $foc_upload_pdf = $this->s3->putObjectFile($output_file_pdf, $bucket, $directory_pdf, S3::ACL_PUBLIC_READ);
                    if ($foc_upload_pdf) {
                        log_message('info', __METHOD__ . ": Main FOC PDF Invoice File uploaded to s3");
                        echo "Main FOC PDF Invoice File uploaded to s3";
                    } else {
                        log_message('info', __METHOD__ . ": Main FOC PDF Invoice File uploaded to s3 " . $invoices['meta']['invoice_id'] . ".pdf");
                        echo "Main FOC PDF Invoice File uploaded to s3 " . $invoices['meta']['invoice_id'] . ".pdf";
                    }
                }


                if ($foc_upload) {
                    log_message('info', __METHOD__ . ": Main FOC Invoice File uploaded to s3");
                    echo "Main FOC Invoice File uploaded to s3";
                } else {
                    $foc_upload = $this->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                    if ($foc_upload) {
                        log_message('info', __METHOD__ . ": Main FOC Invoice File uploaded to s3");
                        echo "Main FOC Invoice File uploaded to s3";
                    } else {
                        log_message('info', __METHOD__ . ": Main FOC Invoice File uploaded to s3 " . $invoices['meta']['invoice_id'] . ".xlsx");
                        echo "Main FOC Invoice File uploaded to s3 " . $invoices['meta']['invoice_id'] . ".xlsx";
                    }
                }

                // Dump data in a file as a Json
                $file = fopen(TMP_FOLDER . $invoices['meta']['invoice_id'] . ".txt", "w") or die("Unable to open file!");
                $res = 0;
                system(" chmod 777 " . TMP_FOLDER . $invoices['meta']['invoice_id'] . ".txt", $res);
                $json_data['invoice_data'] = $invoices;

                $contents = " Vendor FOC Invoice Json Data:\n";
                fwrite($file, $contents);
                fwrite($file, print_r(json_encode($json_data), TRUE));
                fclose($file);
                log_message('info', __METHOD__ . ": Json File Created");

                $directory_xls = "invoices-json/" . $invoices['meta']['invoice_id'] . ".txt";
                $json = $this->s3->putObjectFile(TMP_FOLDER . $invoices['meta']['invoice_id'] . ".txt", $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                if ($json) {

                    log_message('info', __METHOD__ . ": Json TXTInvoice File uploaded to s3");
                    echo "Main FOC Invoice File uploaded to s3";
                } else {

                    $json = $this->s3->putObjectFile(TMP_FOLDER . $invoices['meta']['invoice_id'] . ".txt", $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                    if ($json) {

                        log_message('info', __METHOD__ . ": Json TXT File uploaded to s3");
                        echo "Main FOC Invoice File uploaded to s3";
                    } else {

                        log_message('info', __METHOD__ . ": Json TXT File uploaded to s3 " . $invoices['meta']['invoice_id'] . ".txt");
                        echo "Main FOC Invoice File uploaded to s3 " . $invoices['meta']['invoice_id'] . ".txt";
                    }
                }


                //Delete JSON files now
                exec("rm -rf " . escapeshellarg(TMP_FOLDER . $invoices['meta']['invoice_id'] . ".txt"));
            }

            log_message('info', __FUNCTION__ . " Exit Invoice Id: " . $invoices['meta']['invoice_id']);
            return $invoices['meta']['invoice_id'];
        } else {
            echo "Data Not Found";
            log_message('info', __FUNCTION__ . " Data Not Found.");
            return false;
        }
    }

    /**
     * @desc: This method is used to generate Main Cash Invoice
     * @param type $details
     * @return string
     */
    function generate_vendor_cash_invoice($details, $is_regenerate) {
        log_message('info', __FUNCTION__ . " Entering...." . print_r($details, true));

        $vendor_id = $details['vendor_partner_id'];
        $custom_date = explode("-", $details['date_range']);
        $from_date = $custom_date[0];
        $to_date = $custom_date[1];
        $invoice_type = $details['invoice_type'];
        $invoices = $this->invoices_model->get_vendor_cash_invoice($vendor_id, $from_date, $to_date, $is_regenerate);

        if (!empty($invoices)) {
            log_message('info', __FUNCTION__ . "=> Data Found for Cash Invoice");
            echo "Data Found for Cash Invoice" . PHP_EOL;

            $template = 'Vendor_Settlement_Template-CashMain-v4.xlsx';
            // directory
            $templateDir = __DIR__ . "/../excel-templates/";

            $config = array(
                'template' => $template,
                'templateDir' => $templateDir
            );

            $invoices['meta']['sd'] = date("jS M, Y", strtotime($from_date));
            $invoices['meta']['ed'] = date('jS M, Y', strtotime($to_date));
            $invoices['meta']['invoice_date'] = date("jS M, Y");

            if (isset($details['invoice_id'])) {
                log_message('info', __FUNCTION__ . " Re-Generate Cash Invoice ID: " . $details['invoice_id']);
                echo "Re-Generate Cash Invoice ID: " . $details['invoice_id'] . PHP_EOL;

                $invoices['meta']['invoice_id'] = $details['invoice_id'];

                if ((strcasecmp($invoices['product'][0]['state'], "DELHI") == 0) ||
                        (strcasecmp($invoices['product'][0]['state'], "New Delhi") == 0)) {
                    //If matched return 0;
                    $invoice_version = "T";
                    $invoices['meta']['invoice_type'] = "TAX INVOICE";
                } else {
                    $invoice_version = "R";
                    $invoices['meta']['invoice_type'] = "RETAIL INVOICE";
                }
            } else {
                if ((strcasecmp($invoices['product'][0]['state'], "DELHI") == 0) ||
                        (strcasecmp($invoices['product'][0]['state'], "New Delhi") == 0)) {
                    //If matched return 0;
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
                $invoice_no_temp = $this->invoices_model->get_invoices_details($where);
                $invoice_no = 1;
                if (!empty($invoice_no_temp)) {
                    $explode = explode($invoice_id_tmp . "-", $invoice_no_temp[0]['invoice_id']);
                    $invoice_no = $explode[1] + 1;
                }

                $invoices['meta']['invoice_id'] = $invoice_id_tmp . "-" . $invoice_no;

                log_message('info', __FUNCTION__ . " New Invoice ID Generated: " . $invoices['meta']['invoice_id']);
                echo " New Invoice ID Generated: " . $invoices['meta']['invoice_id'] . PHP_EOL;
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

            $output_file_excel = TMP_FOLDER . $invoices['meta']['invoice_id'] . ".xlsx";
            if (file_exists($output_file_excel)) {
                $res1 = 0;

                log_message('info', __FUNCTION__ . " File exists, deleting it now: " . $output_file_excel);
                echo " File exists, deleting it now: " . $output_file_excel . PHP_EOL;

                system(" chmod 777 " . $output_file_excel, $res1);

                $f_del = unlink($output_file_excel);
                log_message('info', __FUNCTION__ . " File deleted: " . $f_del);
                echo " File deleted: " . $f_del . PHP_EOL;
            }

            $R->render('excel', $output_file_excel);
            log_message('info', __FUNCTION__ . " Excel Created " . $output_file_excel);
            $res2 = 0;
            system(" chmod 777 " . $output_file_excel, $res2);

            //convert excel to pdf
            $output_file_pdf = TMP_FOLDER . $invoices['meta']['invoice_id'] . ".pdf";

            putenv('PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/opt/node/bin');
            $tmp_path = TMP_FOLDER;
            $tmp_output_file = TMP_FOLDER . 'output_' . __FUNCTION__ . '.txt';
            $cmd = 'echo ' . $tmp_path . ' & echo $PATH & UNO_PATH=/usr/lib/libreoffice & ' .
                    '/usr/bin/unoconv --format pdf --output ' . $output_file_pdf . ' ' .
                    $output_file_excel . ' 2> ' . $tmp_output_file;
            $output = '';
            $result_var = '';
            exec($cmd, $output, $result_var);

            if ($invoice_type == "final") {
                log_message('info', __FUNCTION__ . " Generate Final Cash Invoice ");

                $bucket = BITBUCKET_DIRECTORY;
                $directory_xls = "invoices-excel/" . $invoices['meta']['invoice_id'] . ".xlsx";
                $directory_pdf = "invoices-excel/" . $invoices['meta']['invoice_id'] . ".pdf";

                $invoice_uploaded = $this->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                $invoice_uploaded_pdf = $this->s3->putObjectFile($output_file_pdf, $bucket, $directory_pdf, S3::ACL_PUBLIC_READ);
                if ($invoice_uploaded_pdf) {
                    echo 'Main Cash PDF Invoice Uploaded' . PHP_EOL;
                    log_message('info', __FUNCTION__ . " Main Cash PDF Invoice is uploaded to S3: " . $invoices['meta']['invoice_id'] . ".pdf");
                } else {
                    $invoice_uploaded_pdf = $this->s3->putObjectFile($output_file_pdf, $bucket, $directory_pdf, S3::ACL_PUBLIC_READ);
                    if ($invoice_uploaded_pdf) {
                        echo 'Main Cash PDF Invoice Uploaded' . PHP_EOL;
                        log_message('info', __FUNCTION__ . " Main Cash PDF Invoice is uploaded to S3: " . $invoices['meta']['invoice_id'] . ".pdf");
                    } else {
                        echo 'Main Cash PDF Invoice NOT Uploaded' . PHP_EOL;
                        log_message('info', __FUNCTION__ . " Main Cash PDF Invoice is NOT uploaded to S3: " . $invoices['meta']['invoice_id'] . ".pdf");
                    }
                }
                if ($invoice_uploaded) {
                    echo 'Main Cash Invoice Uploaded' . PHP_EOL;
                    log_message('info', __FUNCTION__ . " Main Cash Invoice is uploaded to S3: " . $invoices['meta']['invoice_id'] . ".xlsx");
                } else {
                    $invoice_uploaded = $this->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                    if ($invoice_uploaded) {
                        echo 'Main Cash Invoice Uploaded' . PHP_EOL;
                        log_message('info', __FUNCTION__ . " Main Cash Invoice is uploaded to S3: " . $invoices['meta']['invoice_id'] . ".xlsx");
                    } else {
                        echo 'Main Cash Invoice NOT Uploaded' . PHP_EOL;
                        log_message('info', __FUNCTION__ . " Main Cash Invoice is NOT uploaded to S3: " . $invoices['meta']['invoice_id'] . ".xlsx");
                    }
                }

                // Dump data in a file as a Json
                $file = fopen(TMP_FOLDER . $invoices['meta']['invoice_id'] . ".txt", "w");
                if ($file === FALSE) {
                    echo "Unable to create JSON file......." . PHP_EOL;
                    log_message('info', __FUNCTION__ . "Unable to create JSON file.......");
                } else {
                    $res = 0;
                    system(" chmod 777 " . TMP_FOLDER . $invoices['meta']['invoice_id'] . ".txt", $res);
                    log_message('info', __FUNCTION__ . " Chmod result: " . print_r($res, TRUE));

                    $json_data['invoice_data'] = $invoices;

                    $contents = " Vendor Cash Invoice Json Data:\n";
                    fwrite($file, $contents);
                    fwrite($file, print_r(json_encode($json_data), TRUE));
                    fclose($file);

                    log_message('info', __METHOD__ . ": Json File Created");

                    $directory_xls = "invoices-json/" . $invoices['meta']['invoice_id'] . ".txt";
                    $json_upload = $this->s3->putObjectFile(TMP_FOLDER . $invoices['meta']['invoice_id'] . ".txt", $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                    if ($json_upload) {
                        echo 'Main Invoice JOSN File Uploaded' . PHP_EOL;
                        log_message('info', __FUNCTION__ . " Main Invoice JOSN FIle Uploaded to S3" . $invoices['meta']['invoice_id'] . ".txt");
                    } else {
                        $json_upload = $this->s3->putObjectFile(TMP_FOLDER . $invoices['meta']['invoice_id'] . ".txt", $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                        if ($json_upload) {
                            echo 'Main Invoice JOSN File Uploaded' . PHP_EOL;
                            log_message('info', __FUNCTION__ . " Main Invoice JOSN FIle Uploaded to S3" . $invoices['meta']['invoice_id'] . ".txt");
                        } else {
                            echo 'Main Invoice JOSN File not Uploaded' . PHP_EOL;
                            log_message('info', __FUNCTION__ . " Main Invoice is not uploaded to S3" . $invoices['meta']['invoice_id'] . ".txt");
                        }
                    }

                    //Delete JSON files now
                    exec("rm -rf " . escapeshellarg(TMP_FOLDER . $invoices['meta']['invoice_id'] . ".txt"));
                }
            }

            log_message('info', __FUNCTION__ . " Exiting with Invoice Id: " . $invoices['meta']['invoice_id']);
            echo " Exiting with Invoice Id: " . $invoices['meta']['invoice_id'] . PHP_EOL;

            return $invoices['meta']['invoice_id'];
        } else {
            log_message('info', __FUNCTION__ . "=> Data Not Found for Cash Invoice" . print_r($details));

            echo "Data Not Found for Cash Invoice" . PHP_EOL;

            return FALSE;
        }
    }

    /**
     * @desc: This is used to load invoice insert/update form
     * @param Sting $vendor_partner
     * @param String $invoice_id
     */
    function insert_update_invoice($vendor_partner, $invoice_id = FALSE) {
        log_message('info', __FUNCTION__ . " Entering...." . $invoice_id);
        if ($invoice_id) {
            $where = " `invoice_id` = '$invoice_id'";
            //Get Invocie details from Vendor Partner Invoice Table
            $invoice_details['invoice_details'] = $this->invoices_model->get_invoices_details($where);
        }
        $invoice_details['vendor_partner'] = $vendor_partner;
        $this->load->view('employee/header/' . $this->session->userdata('user_group'));
        $this->load->view('employee/insert_update_invoice', $invoice_details);
    }

    /**
     * @desc: Update/ Insert Partner Invoice Details from panel
     * @param String $vendor_partner
     */
    function process_insert_update_invoice($vendor_partner) {
        log_message('info', __FUNCTION__ . " Entering...." . $vendor_partner);
        $this->form_validation->set_rules('vendor_partner_id', 'Vendor Partner', 'required|trim|xss_clean');
        $this->form_validation->set_rules('invoice_id', 'Invoice ID', 'required|trim|xss_clean');
        $this->form_validation->set_rules('around_type', 'Around Type', 'required|trim|xss_clean');
        if ($this->form_validation->run()) {

            $sms_sent = $this->input->post('sms_sent');
            $mail_sent = $this->input->post('mail_sent');

            $data['type'] = $this->input->post('type');
            $data['vendor_partner'] = $vendor_partner;
            $data['vendor_partner_id'] = $this->input->post('vendor_partner_id');
            $data['from_date'] = $this->input->post('from_date');
            $data['to_date'] = $this->input->post('to_date');
            $data['num_bookings'] = $this->input->post('num_bookings');
            $invoice_id = $this->input->post('invoice_id');
            $data['total_service_charge'] = $this->input->post('total_service_charge');
            $data['total_additional_service_charge'] = $this->input->post('total_additional_service_charge');
            $data['service_tax'] = $this->input->post('service_tax');
            $data['parts_cost'] = $this->input->post('parts_cost');
            $data['vat'] = $this->input->post('vat');
            $data['penalty_amount'] = $this->input->post("penalty_amount");
            $data['upcountry_booking'] = $this->input->post("upcountry_booking");
            $data['upcountry_distance'] = $this->input->post("upcountry_distance");
            $data['courier_charges'] = $this->input->post("courier_charges");
            $data['upcountry_price'] = $this->input->post("upcountry_price");
            $data['remarks'] = $this->input->post("remarks");
            $data['penalty_bookings_count'] = $this->input->post("penalty_bookings_count");
            $data['total_amount_collected'] = round(($data['total_service_charge'] +
                    $data['total_additional_service_charge'] +
                    $data['parts_cost'] + $data['vat'] +
                    $data['service_tax'] + $data['courier_charges'] +$data['upcountry_price'] - $data['penalty_amount']), 0);

            $data['due_date'] = date("Y-m-d", strtotime($data['to_date'] . "+1 month"));
            $invoice_date = $this->input->post('invoice_date');
            $data['invoice_date'] = date('Y-m-d', strtotime($invoice_date));
            $data['type_code'] = $this->input->post('around_type');

            $entity_details = array();
            $main_invoice_file = "";
            $detailed_invoice_file = "";
            $sms = array();
            if ($data['vendor_partner'] == "vendor") {
                $entity_details = $this->vendor_model->viewvendor($data['vendor_partner_id']);
            }
            if (!empty($invoice_id)) {
                $data['invoice_id'] = $invoice_id;
            }

            switch ($data['type_code']) {
                case 'A':
                    log_message('info', __FUNCTION__ . " .. type code:- " . $data['type']);
                    $data['total_amount_collected'] = ($data['total_amount_collected']);
                    $data['around_royalty'] = round($data['total_amount_collected'], 0);
                    $data['amount_collected_paid'] = round($data['total_amount_collected'], 0);

                    break;
                case 'B':
                    log_message('info', __FUNCTION__ . " .. type code:- " . $data['type']);
                    $data['total_amount_collected'] = ($data['total_amount_collected'] );
                    $tds['tds'] = 0;
                    $tds['tds_rate'] = 0;
                    if ($data['type'] == 'FOC') {

                        if ($vendor_partner == "vendor") {
                            $tds = $this->check_tds_sc($entity_details[0], $data['total_service_charge'] + $data['service_tax']);
                        } else {
                            $tds['tds'] = 0;
                            $tds['tds_rate'] = 0;
                        }
                    } else if ($data['type'] == 'CreditNote' || $data['type'] == 'Buyback' || $data['type'] == 'Stand') {

                        $tds['tds'] = 0;
                        $tds['tds_rate'] = 0;
                    }

                    $data['around_royalty'] = 0;
                    $data['amount_collected_paid'] = -($data['total_amount_collected'] - $tds['tds']);
                    $data['tds_amount'] = $tds['tds'];
                    $data['tds_rate'] = $tds['tds_rate'];
                    break;
            }

            if (!empty($_FILES["invoice_file_excel"]['tmp_name'])) {
                $temp = explode(".", $_FILES["invoice_file_excel"]["name"]);
                $extension = end($temp);
                // Uploading to S3
                $bucket = BITBUCKET_DIRECTORY;
                $directory = "invoices-excel/" . $data['invoice_id'] . "." . $extension;
                $is_s3 = $this->s3->putObjectFile($_FILES["invoice_file_excel"]["tmp_name"], $bucket, $directory, S3::ACL_PUBLIC_READ);
                echo $is_s3;
                if ($is_s3) {
                    log_message('info', __FUNCTION__ . " Main Invoice upload");
                    $data['invoice_file_excel'] = $data['invoice_id'] . "." . $extension;
                    $main_invoice_file = $data['invoice_file_excel'];
                } else {
                    log_message('info', __FUNCTION__ . " Main Invoice upload failed");
                }
            }
            if (!empty($_FILES["invoice_detailed_excel"]['tmp_name'])) {
                $temp1 = explode(".", $_FILES["invoice_detailed_excel"]["name"]);
                $extension1 = end($temp1);
                // Uploading to S3
                $bucket = BITBUCKET_DIRECTORY;
                $directory = "invoices-excel/" . $data['invoice_id'] . "-detailed." . $extension1;
                $is_s3 = $this->s3->putObjectFile($_FILES["invoice_detailed_excel"]["tmp_name"], $bucket, $directory, S3::ACL_PUBLIC_READ);

                if ($is_s3) {
                    log_message('info', __FUNCTION__ . " Main Invoice upload");
                    $data['invoice_detailed_excel'] = $data['invoice_id'] . "-detailed." . $extension1;
                    $detailed_invoice_file = $data['invoice_detailed_excel'];
                } else {
                    log_message('info', __FUNCTION__ . " Main Invoice upload failed");
                }
            }

            $status = $this->invoices_model->action_partner_invoice($data);

            if ($status) {
                log_message('info', __METHOD__ . ' Invoice details inserted ' . $data['invoice_id']);
                if ($sms_sent && $data['vendor_partner'] === 'vendor') {

                    $sms['tag'] = "vendor_invoice_mailed";
                    $sms['smsData']['type'] = $data['type'];
                    $sms['smsData']['month'] = date('M Y', strtotime($data['from_date']));
                    $sms['smsData']['amount'] = $data['amount_collected_paid'];
                    $sms['phone_no'] = $entity_details[0]['owner_phone_1'];
                    $sms['booking_id'] = "";
                    $sms['type'] = "vendor";
                    $sms['type_id'] = $data['vendor_partner_id'];


                    $this->notify->send_sms_msg91($sms);
                    log_message('info', __METHOD__ . ' SMS Sent ' . $data['invoice_id']);
                }

                if ($mail_sent) {

                    if ($main_invoice_file != "") {
                        $this->send_attach_email_to_sf($entity_details, $data['type'], $data['from_date'], $data['to_date'], $main_invoice_file, $detailed_invoice_file);
                    }
                }
            } else {

                log_message('info', __METHOD__ . ' Invoice details not inserted ' . $data['invoice_id']);
            }

            redirect(base_url() . 'employee/invoice/invoice_summary/' . $data['vendor_partner'] . "/" . $data['vendor_partner_id']);
        } else {
            $this->insert_update_invoice($vendor_partner);
        }
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
        } else if (empty($sc_details['contract_file'])) {

            $tds = ($total_sc_details) * .05;
            $tds_tax_rate = 5;
            $tds_per_rate = "5%";
        } else {
            switch ($sc_details['company_type']) {
                case 'Proprietorship Firm':
                    if (!empty($sc_details['pan_no'])) {
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
    function create_invoice_id_to_insert($entity_details, $from_date, $start_name) {
        log_message('info', __FUNCTION__ . " Entering....");

        if ((strcasecmp($entity_details[0]['state'], "DELHI") == 0) ||
                (strcasecmp($entity_details[0]['state'], "New Delhi") == 0)) {

            $invoice_version = "T";
            $invoices['invoice_type'] = "TAX INVOICE";
        } else {
            $invoice_version = "R";
            $invoices['invoice_type'] = "RETAIL INVOICE";
        }

        $current_month = date('m');
        // 3 means March Month
        if ($current_month > 3) {
            $financial = date('Y') . "-" . (date('y') + 1);
        } else {
            $financial = (date('Y') - 1) . "-" . date('y');
        }

        //Make sure it is unique
        $invoice_id_tmp = $start_name . "-" . $invoice_version . "-" . $financial . "-" . date("M", strtotime($from_date));
        $where = " `invoice_id` LIKE '%$invoice_id_tmp%'";
        $invoice_no_temp = $this->invoices_model->get_invoices_details($where);
        $invoice_no = 1;
        if (!empty($invoice_no_temp)) {
            $explode = explode($invoice_id_tmp . "-", $invoice_no_temp[0]['invoice_id']);
            $invoice_no = $explode[1] + 1;
        }
        log_message('info', __FUNCTION__ . " Exit....");
        $invoices['invoice_id'] = $invoice_id_tmp . "-" . $invoice_no;
        return $invoices;
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
        $cc = NITS_ANUJ_EMAIL_ID;

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
        $payment_data = array();

        if (!empty($data)) {
            foreach ($data as $service_center_id => $amount) {
                $sc = $this->vendor_model->viewvendor($service_center_id)[0];

                $sc_details['debit_acc_no'] = '102405500277';
                $sc_details['bank_account'] = trim($sc['bank_account']);
                $sc_details['beneficiary_name'] = trim($sc['beneficiary_name']);

                $sc_details['final_amount'] = abs(round($amount, 0));
                if ($amount > 0) {
                    $sc_details['amount_type'] = "CR";
                } else {
                    $sc_details['amount_type'] = "DR";
                }

                if (trim($sc_details['bank_name']) === ICICI_BANK_NAME) {
                    $sc_details['payment_mode'] = "I";
                } else {
                    $sc_details['payment_mode'] = "N";
                }

                $sc_details['payment_date'] = date("d-M-Y");
                $sc_details['ifsc_code'] = trim($sc['ifsc_code']);
                $sc_details['remarks'] = preg_replace("/[^A-Za-z0-9]/", "", $sc['name']);

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
    function fetch_invoice_id($vendor_partner_id, $vendor_partner_type, $from_date, $type_code) {
        $entity_details = array();
        if ($vendor_partner_type == "vendor") {

            $entity_details = $this->vendor_model->viewvendor($vendor_partner_id);
        } else {
            $entity_details = $this->partner_model->getpartner($vendor_partner_id);
        }

        if (!empty($entity_details)) {
            switch ($type_code) {

                case 'A':

                    $invoice_id = $this->create_invoice_id_to_insert($entity_details, $from_date, "Around");
                    echo $invoice_id['invoice_id'];
                    break;

                case 'B':
                    
                    if ($vendor_partner_type == "vendor") {
                        $invoice_id = $this->create_invoice_id_to_insert($entity_details, $from_date, $entity_details[0]['sc_code']);
                        echo $invoice_id['invoice_id'];
                    } else {
                        $invoice_id = $this->create_invoice_id_to_insert($entity_details, $from_date, "Around");
                        echo $invoice_id['invoice_id'];
                    }
                
                    
                    break;
            }
        } else {
            echo "DATA NOT FOUND";
        }
    }
    /**
     * @desc: This is used to Insert CRM SETUP invoice
     */
    function generate_crm_setup() {
        log_message('info', __FUNCTION__ . " Entering....");
        $this->form_validation->set_rules('partner_name', 'Partner Name', 'required|trim|xss_clean');
        $this->form_validation->set_rules('partner_id', 'Partner ID', 'required|trim|xss_clean');
        $this->form_validation->set_rules('partner_address', 'Partner Address', 'required|trim|xss_clean');
        $this->form_validation->set_rules('from_date', 'Start Date', 'required|trim|xss_clean');
        $this->form_validation->set_rules('partner_state', 'State', 'required|trim|xss_clean');
        $this->form_validation->set_rules('service_charge', 'Service Charge', 'required|trim|xss_clean');
        $this->form_validation->set_rules('email_to', 'Partner Email To', 'required|trim|xss_clean');
        $this->form_validation->set_rules('email_cc', 'Partner_Email CC', 'trim|xss_clean');
        if ($this->form_validation->run() == TRUE) {
            $entity_details = array();
            $booking = array();

            $state = $this->input->post('partner_state');
            $email_to = $this->input->post('email_to');
            $email_cc = $this->input->post('email_cc');
            $seller_code = $this->input->post('seller_code');
            $from_date = $this->input->post('from_date');
            $meta['grand_part'] = $this->input->post('service_charge');
            $service_tax = $this->booking_model->get_calculated_tax_charge($meta['grand_part'], 15);

            $sub_service_cost = $meta['grand_part'] - $service_tax;

            $setup_insert['vendor_partner'] = "partner";
            $setup_insert['vendor_partner_id'] = $partner_id = $this->input->post('partner_id');

            $meta['company_address'] = $this->input->post('partner_address');
            $meta['company_name'] = $this->input->post('partner_name');
            $meta['total_service_cost_14'] = $sub_service_cost * .14;
            $meta['total_service_cost_5'] = $sub_service_cost * 0.005;

            $meta['invoice_date'] = date('jS M, Y');
            $meta['sd'] = date('jS M, Y', strtotime($from_date));
            $meta['ed'] = date('jS M, Y', strtotime('+1 year', strtotime($from_date)));
            $meta['seller_code'] = "Seller Code - " . $seller_code;
            $meta['total_part_cost'] = $meta['part_cost_vat'] = $meta['sub_part'] = $meta['total_upcountry_charges'] = '';
            $meta['price_inword'] = convert_number_to_words($meta['grand_part']);

            $setup_insert['service_tax'] = $service_tax;
            $setup_insert['total_service_charge'] = $meta['total_service_cost'] = $sub_service_cost;
            $meta['sub_service_cost'] = $meta['grand_part'];
            $setup_insert['total_amount_collected'] = $setup_insert['amount_collected_paid'] = $setup_insert['amount_paid'] = $setup_insert['around_royalty'] = $meta['grand_part'];
            $setup_insert['settle_amount'] = 1;
            $setup_insert['from_date'] = date("Y-m-d", strtotime($from_date));
            $setup_insert['invoice_date'] = date('Y-m-d');
            $setup_insert['due_date'] = $setup_insert['to_date'] = date('Y-m-d', strtotime('+1 year', strtotime($from_date)));
            $setup_insert['type'] = "Cash";
            $setup_insert['type_code'] = "A";

            $entity_details[0]['state'] = $state;
            $invoice_details = $this->create_invoice_id_to_insert($entity_details, $from_date, "Around");
            $setup_insert['invoice_id'] = $meta['invoice_id'] = $invoice_details['invoice_id'];
            $meta['invoice_type'] = $invoice_details['invoice_type'];

            $booking[0]['description'] = "Annual Setup Charges";
            $booking[0]['p_tax_rate'] = $booking[0]['p_rate'] = $booking[0]['s_service_charge'] = $booking[0]['upcountry_charges'] = '';
            $booking[0]['s_total_service_charge'] = $meta['total_service_cost'];
            $booking[0]['p_part_cost'] = 0;
            $booking[0]['qty'] = 1;

            $template = 'partner_invoice_Main_v3.xlsx';
            // directory
            $templateDir = __DIR__ . "/../excel-templates/";

            $config = array(
                'template' => $template,
                'templateDir' => $templateDir
            );

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

            $output_file_excel = TMP_FOLDER . $meta['invoice_id'] . "-invoice-setup.xlsx";
            $res1 = 0;
            if (file_exists($output_file_excel)) {

                system(" chmod 777 " . $output_file_excel, $res1);
                unlink($output_file_excel);
            }
            //for xlsx: excel, for xls: excel2003
            $R->render('excel', $output_file_excel);
            $output_file_pdf = TMP_FOLDER . $meta['invoice_id'] . "-invoice-setup.pdf";

            putenv('PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/opt/node/bin');
            $tmp_path = TMP_FOLDER;
            $tmp_output_file = TMP_FOLDER . 'output_' . __FUNCTION__ . '.txt';
            $cmd = 'echo ' . $tmp_path . ' & echo $PATH & UNO_PATH=/usr/lib/libreoffice & ' .
                    '/usr/bin/unoconv --format pdf --output ' . $output_file_pdf . ' ' .
                    $output_file_excel . ' 2> ' . $tmp_output_file;
            $output = '';
            $result_var = '';
            exec($cmd, $output, $result_var);

            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "invoices-excel/" . $meta['invoice_id'] . "-invoice-setup.xlsx";
            $directory_pdf = "invoices-excel/" . $meta['invoice_id'] . "-invoice-setup.pdf";

            $this->s3->putObjectFile(TMP_FOLDER . $meta['invoice_id'] . "-invoice-setup.xlsx", $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            $this->s3->putObjectFile(TMP_FOLDER . $meta['invoice_id'] . "-invoice-setup.pdf", $bucket, $directory_pdf, S3::ACL_PUBLIC_READ);
            $setup_insert['invoice_file_excel'] = $meta['invoice_id'] . "-invoice-setup.pdf";

            $status = $this->invoices_model->insert_new_invoice($setup_insert);
            if ($status) {
                //Send report via email
                $this->email->clear(TRUE);
                $this->email->from('billing@247around.com', '247around Team');
                $to = $email_to;
                $subject = "PARTNER CRM SETUP INVOICE- 247around - " . $meta['company_name'] .
                        " Invoice for period: " . $meta['sd'] . " to " . $meta['ed'];
                $cc = $email_cc . ", " . NITS_ANUJ_EMAIL_ID;

                $this->email->to($to);
                $this->email->cc($cc);

                $this->email->subject($subject);
                $this->email->attach(TMP_FOLDER . $meta['invoice_id'] . "-invoice-setup.pdf", 'attachment');

                $mail_ret = $this->email->send();

                if ($mail_ret) {
                    log_message('info', __METHOD__ . ": Mail sent successfully");
                    echo "Mail sent successfully..............." . PHP_EOL;
                } else {
                    log_message('info', __METHOD__ . ": Mail could not be sent");
                    echo "Mail could not be sent..............." . PHP_EOL;
                }
                exec("rm -rf " . escapeshellarg(TMP_FOLDER . $meta['invoice_id'] . "-invoice-setup.pdf"));
                exec("rm -rf " . escapeshellarg(TMP_FOLDER . $meta['invoice_id'] . "-invoice-setup.xlsx"));
                $this->session->set_flashdata('file_error', 'CRM SETUP INVOICE- GENERATED');
                redirect(base_url() . "employee/invoice/invoice_partner_view");
            } else {
                log_message('info', __METHOD__ . ": Invoice ID is not inserted");
                $this->session->set_flashdata('file_error', 'CRM SETUP INVOICE NOT INSERTED');
                redirect(base_url() . "employee/invoice/invoice_partner_view");
            }
        } else {
            log_message('info', __METHOD__ . ": Validation Failed");
            $this->invoice_partner_view();
        }
    }

    /**
     * @desc Combined detailed and upcountry excell sheet in a Single sheet
     * @param String $details_excel
     * @param String $upcountry_excel
     * @return String 
     */
    function combined_partner_invoice_sheet($details_excel, $upcountry_excel) {

        // Files are loaded to PHPExcel using the IOFactory load() method
        $objPHPExcel1 = PHPExcel_IOFactory::load($details_excel . ".xlsx");
        $objPHPExcel2 = PHPExcel_IOFactory::load($upcountry_excel);

        // Copy worksheets from $objPHPExcel2 to $objPHPExcel1
        foreach ($objPHPExcel2->getAllSheets() as $sheet) {
            $objPHPExcel1->addExternalSheet($sheet);
        }

        // Save $objPHPExcel1 to browser as an .xls file
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel1, "Excel2007");

        $objWriter->save($details_excel . ".xlsx");

        return $details_excel;
    }

    /**
     * @desc This function adds new advance bank transactions between vendor/partner and 247around
     * @param String $vendor_partner
     * @param int $id
     */
    function get_advance_bank_transaction($vendor_partner = "", $id = "") {
        $data['vendor_partner'] = $vendor_partner;
        $data['id'] = $id;


        $this->load->view('employee/header/' . $this->session->userdata('user_group'));
        $this->load->view('employee/advance_bank_transaction', $data);
    }

    /**
     * @desc Add new bank transaction
     */
    function process_advance_payment() {
        $data['partner_vendor'] = $this->input->post("partner_vendor");
        $data['partner_vendor_id'] = $this->input->post('partner_vendor_id');
        $data['credit_debit'] = $this->input->post("credit_debit");
        $data['bankname'] = $this->input->post("bankname");
        $amount = $this->input->post("amount");
        if ($data['credit_debit'] == "Credit") {
            $data['credit_amount'] = $amount;
        } else if ($data['credit_debit'] == "Debit") {
            $data['debit_amount'] = $amount;
        }

        $data['tds_amount'] = $this->input->post('tds_amount');
        $data['transaction_mode'] = $this->input->post('transaction_mode');
        $data['transaction_date'] = date("Y-m-d", strtotime($this->input->post("tdate")));
        $data['description'] = $this->input->post("description");
        $data['agent_id'] = $this->session->userdata('id');
        $data['create_date'] = date("Y-m-d H:i:s");
        $status = $this->invoices_model->bankAccountTransaction($data);
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

    /**
     * @desc show form for new credit note for brackets
     * @param void
     * @return void 
     */
    function show_purchase_brackets_credit_note_form() {
        $this->load->view('employee/header/' . $this->session->userdata('user_group'));
        $this->load->view('employee/purchase_brackets_credit_note_form');
    }

    /**
     * @desc process credit note form to update vendor_partner_invoices table and brackets table
     * @param void
     * @return void 
     */
    function process_purchase_bracket_credit_note() {
        //validate input post variable
        $this->form_validation->set_rules('order_id', 'Order Id', 'required|xss_clean|callback_validate_order_id');
        $this->form_validation->set_rules('courier_charges', 'Courier Charges', 'required|xss_clean');
        if (empty($_FILES['courier_charges_file']['name'])) {
            $this->form_validation->set_rules('courier_charges_file', 'File', 'required|xss_clean');
        }
        if ($this->form_validation->run() == false) {
            $this->load->view('employee/header/' . $this->session->userdata('user_group'));
            $this->load->view('employee/purchase_brackets_credit_note_form');
        } else {
            //save courier charges file to s3
            if (($_FILES['courier_charges_file']['error'] != 4) && !empty($_FILES['courier_charges_file']['tmp_name'])) {
                $tmpFile = $_FILES['courier_charges_file']['tmp_name'];
                $courier_charges_file_name = $this->input->post('order_id') . 'courier_charges_file' . substr(md5(uniqid(rand(0, 9))), 0, 15) . "." . explode(".", $_FILES['courier_charges_file']['name'])[1];
                //Upload files to AWS
                $bucket = BITBUCKET_DIRECTORY;
                $directory_xls = "vendor-partner-docs/" . $courier_charges_file_name;
                //$this->s3->putObjectFile($tmpFile, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                //Logging success for file uppload
                log_message('info', __METHOD__ . 'Courier charges file is being uploaded sucessfully.');
            }

            $order_id = trim($this->input->post('order_id'));
            $courier_charges = $this->input->post('courier_charges');
            $order_id_data = $this->inventory_model->get_new_credit_note_brackets_data($order_id);


            $result = array();
            $_19_24_shipped_brackets_data = array();
            $_26_32_shipped_brackets_data = array();
            $_36_42_shipped_brackets_data = array();
            $_43_shipped_brackets_data = array();


            //prepare data to make credit note file
            if (!empty($order_id_data[0]['19_24_shipped'])) {
                $_19_24_shipped_brackets_data[0]['description'] = 'Bracket  Charges Refund (19-24 Inch)';
                $_19_24_shipped_brackets_data[0]['p_tax_rate'] = '';
                $_19_24_shipped_brackets_data[0]['qty'] = $order_id_data[0]['19_24_shipped'];
                $_19_24_shipped_brackets_data[0]['p_rate'] = '';
                $_19_24_shipped_brackets_data[0]['p_part_cost'] = '';
                $_19_24_shipped_brackets_data[0]['s_service_charge'] = '';
                $_19_24_shipped_brackets_data[0]['misc_price'] = $order_id_data[0]['19_24_shipped'] * _247AROUND_BRACKETS_19_24_UNIT_PRICE;
                $_19_24_shipped_brackets_data[0]['s_total_service_charge'] = '';

                $result = array_merge($result, $_19_24_shipped_brackets_data);
            }

            if (!empty($order_id_data[0]['26_32_shipped'])) {
                $_26_32_shipped_brackets_data[0]['description'] = 'Bracket  Charges Refund (26-32 Inch)';
                $_26_32_shipped_brackets_data[0]['p_tax_rate'] = '';
                $_26_32_shipped_brackets_data[0]['qty'] = $order_id_data[0]['26_32_shipped'];
                $_26_32_shipped_brackets_data[0]['p_rate'] = '';
                $_26_32_shipped_brackets_data[0]['p_part_cost'] = '';
                $_26_32_shipped_brackets_data[0]['s_service_charge'] = '';
                $_26_32_shipped_brackets_data[0]['misc_price'] = $order_id_data[0]['26_32_shipped'] * _247AROUND_BRACKETS_26_32_UNIT_PRICE;
                $_26_32_shipped_brackets_data[0]['s_total_service_charge'] = '';

                $result = array_merge($result, $_26_32_shipped_brackets_data);
            }

            if (!empty($order_id_data[0]['36_42_shipped'])) {
                $_36_42_shipped_brackets_data[0]['description'] = 'Bracket  Charges Refund (36_42 Inch)';
                $_36_42_shipped_brackets_data[0]['p_tax_rate'] = '';
                $_36_42_shipped_brackets_data[0]['qty'] = $order_id_data[0]['36_42_shipped'];
                $_36_42_shipped_brackets_data[0]['p_rate'] = '';
                $_36_42_shipped_brackets_data[0]['p_part_cost'] = '';
                $_36_42_shipped_brackets_data[0]['s_service_charge'] = '';
                $_36_42_shipped_brackets_data[0]['misc_price'] = $order_id_data[0]['36_42_shipped'] * _247AROUND_BRACKETS_36_42_UNIT_PRICE;
                $_36_42_shipped_brackets_data[0]['s_total_service_charge'] = '';

                $result = array_merge($result, $_36_42_shipped_brackets_data);
            }

            if (!empty($order_id_data[0]['43_shipped'])) {
                $_43_shipped_brackets_data[0]['description'] = 'Bracket  Charges Refund (Greater Than 43 Inch)';
                $_43_shipped_brackets_data[0]['p_tax_rate'] = '';
                $_43_shipped_brackets_data[0]['qty'] = $order_id_data[0]['43_shipped'];
                $_43_shipped_brackets_data[0]['p_rate'] = '';
                $_43_shipped_brackets_data[0]['p_part_cost'] = '';
                $_43_shipped_brackets_data[0]['s_service_charge'] = '';
                $_43_shipped_brackets_data[0]['misc_price'] = $order_id_data[0]['43_shipped'] * _247AROUND_BRACKETS_43_UNIT_PRICE;
                $_43_shipped_brackets_data[0]['s_total_service_charge'] = '';

                $result = array_merge($result, $_43_shipped_brackets_data);
            }
            
            //if there is no data for brackets then did not process the credit note and rdirect to form
            if (!empty($result)) {
                $courier_charges_data[0]['description'] = 'Courier Charges';
                $courier_charges_data[0]['p_tax_rate'] = '';
                $courier_charges_data[0]['qty'] = '';
                $courier_charges_data[0]['p_rate'] = '';
                $courier_charges_data[0]['p_part_cost'] = '';
                $courier_charges_data[0]['s_service_charge'] = '';
                $courier_charges_data[0]['misc_price'] = $courier_charges;
                $courier_charges_data[0]['s_total_service_charge'] = '';

                $result = array_merge($result, $courier_charges_data);


                $total_brackets = $order_id_data[0]['19_24_shipped'] + $order_id_data[0]['26_32_shipped'] + $order_id_data[0]['36_42_shipped'] + $order_id_data[0]['43_shipped'];

                $t_19_24_shipped_price = $order_id_data[0]['19_24_shipped'] * _247AROUND_BRACKETS_19_24_UNIT_PRICE;
                $t_26_32_shipped_price = $order_id_data[0]['26_32_shipped'] * _247AROUND_BRACKETS_26_32_UNIT_PRICE;
                $t_36_42_shipped_price = $order_id_data[0]['36_42_shipped'] * _247AROUND_BRACKETS_36_42_UNIT_PRICE;
                $t_43_shipped_price = $order_id_data[0]['43_shipped'] * _247AROUND_BRACKETS_43_UNIT_PRICE;
                $total_brackets_price = $t_19_24_shipped_price + $t_26_32_shipped_price + $t_36_42_shipped_price + $t_43_shipped_price;

                if ((strcasecmp($order_id_data[0]['state'], "DELHI") == 0) ||
                        (strcasecmp($order_id_data[0]['state'], "New Delhi") == 0)) {
                    //If matched return 0;
                    $invoice_version = "T";
                    $meta['invoice_type'] = "TAX INVOICE";
                } else {
                    $invoice_version = "R";
                    $meta['invoice_type'] = "RETAIL INVOICE";
                }

                $current_month = date('m');
                // 3 means March Month
                if ($current_month > 3) {
                    $financial = date('Y') . "-" . (date('y') + 1);
                } else {
                    $financial = (date('Y') - 1) . "-" . date('y');
                }

                //Make sure it is unique
                $invoice_id_tmp = $order_id_data[0]['sc_code'] . $invoice_version . "-" . $financial . "-" . date("M", strtotime($order_id_data[0]['shipment_date']));
                $where = " `invoice_id` LIKE '%$invoice_id_tmp%'";
                $invoice_no_temp = $this->invoices_model->get_invoices_details($where);
                $invoice_no = 1;
                if (!empty($invoice_no_temp)) {
                    $explode = explode($invoice_id_tmp . "-", $invoice_no_temp[0]['invoice_id']);
                    $invoice_no = $explode[1] + 1;
                }

                $meta['invoice_id'] = $invoice_id_tmp . "-" . $invoice_no;
                log_message('info', __METHOD__ . ": Invoice Id geneterated "
                        . $meta['invoice_id']);

                $total_charges = round($total_brackets_price + $courier_charges);

                $meta['total_service_cost_14'] = '';
                $meta['total_service_cost_5'] = '';
                $meta['total_service_cost_5'] = '';
                $meta['sub_service_cost'] = '';
                $meta['sub_part'] = '';
                $meta['part_cost_vat'] = '';
                $meta['vat_tax'] = '';
                $meta['total_part_cost'] = '';
                $meta['total_service_cost'] = '';
                $meta['total_misc_price'] = $total_charges;
                $meta['grand_total_price'] = $total_charges;
                $meta['price_inword'] = convert_number_to_words($total_charges);
                $meta['vendor_name'] = $order_id_data[0]['company_name'];
                $meta['vendor_address'] = $order_id_data[0]['address'];
                $meta['service_tax_no'] = $order_id_data[0]['service_tax_no'];
                $meta['tin'] = $order_id_data[0]['tin_no'];
                $meta['invoice_date'] = date("jS M, Y");
                $meta['sd'] = '';
                $meta['ed'] = '';

                $result_excel = $this->generate_new_credit_note_brackets($result, $meta);

                if ($result_excel) {

                    //Save this invoice info in table
                    $invoice_details = array(
                        'invoice_id' => $meta['invoice_id'],
                        'type' => 'Stand',
                        'type_code' => 'B',
                        'vendor_partner' => 'vendor',
                        'vendor_partner_id' => $order_id_data[0]['order_received_from'],
                        'invoice_file_excel' => $meta['invoice_id'] . '.xlsx',
                        'invoice_file_pdf' => $meta['invoice_id'] . '.pdf',
                        'from_date' => $order_id_data[0]['shipment_date'],
                        'to_date' => $order_id_data[0]['shipment_date'],
                        'num_bookings' => $total_brackets,
                        'total_service_charge' => 0,
                        'total_additional_service_charge' => 0,
                        'service_tax' => 0,
                        'parts_cost' => $meta['total_misc_price'],
                        'vat' => '0',
                        'total_amount_collected' => $meta['grand_total_price'],
                        'rating' => 0,
                        'around_royalty' => 0,
                        'amount_collected_paid' => '-' . $meta['grand_total_price'],
                        'invoice_date' => date('Y-m-d'),
                        'tds_amount' => 0.0,
                        'amount_paid' => 0.0,
                        'settle_amount' => 0,
                        'amount_paid' => 0.0,
                        'mail_sent' => 1,
                        'sms_sent' => 1,
                        'courier_charges' => $courier_charges,
                        //Add 1 month to end date to calculate due date
                        'due_date' => date("Y-m-d", strtotime($order_id_data[0]['shipment_date'] . "+1 month")),
                        'agent_id' => $this->session->userdata('id')
                    );

                    $invoice_update_msg = $this->invoices_model->action_partner_invoice($invoice_details);

                    if (!empty($invoice_update_msg)) {

                        //save the brackets purchase invoice id into the table
                        $purchase_brackets_invoice_id = $this->inventory_model->update_brackets(array('purchase_invoice_id' => $meta['invoice_id']), array('order_id' => $order_id));
                        if ($purchase_brackets_invoice_id) {
                            $send_mail = $this->send_brackets_credit_note_mail_sms($order_id_data, $meta['invoice_id'], $meta['grand_total_price']);

                            if ($send_mail) {
                                //Loggin Success
                                log_message('info', __FUNCTION__ . ' Credit Note - Brackets credit note has been sent for the month of ' . $meta['invoice_date']);
                                $success_msg = "Credit Note Created Succesfully";
                                $this->session->set_flashdata('success_msg', $success_msg);
                                redirect(base_url() . 'employee/invoice/show_purchase_brackets_credit_note_form');
                            } else {
                                //Loggin Error
                                log_message('info', __FUNCTION__ . ' Credit Note - Error in sending Brackets credit note for the month of ' . $meta['invoice_date']);
                                $error_msg = "Error in Sending Mail to sf";
                                $this->session->set_flashdata('error_msg', $error_msg);
                                redirect(base_url() . 'employee/invoice/show_purchase_brackets_credit_note_form');
                            }
                        } else {
                            log_message('info', __FUNCTION__ . ' Credit Note - Error in Inserting Brackets credit note data in brackets table for the month of ' . $meta['invoice_date'] . 'and data is ' . print_r($invoice_details));
                            $error_msg = "Error in generating credit note!!! Please Try Again";
                            $this->session->set_flashdata('error_msg', $error_msg);
                            redirect(base_url() . 'employee/invoice/show_purchase_brackets_credit_note_form');
                        }
                    } else {
                        log_message('info', __FUNCTION__ . ' Credit Note - Error in Inserting Brackets credit note data in the vendor_partner_invoice table for the month of ' . $meta['invoice_date'] . 'and data is ' . print_r($invoice_details));
                        log_message('info', __FUNCTION__ . ' Error in generating credit note');
                        $error_msg = "Error in generating credit note!!! Please Try Again";
                        $this->session->set_flashdata('error_msg', $error_msg);
                        redirect(base_url() . 'employee/invoice/show_purchase_brackets_credit_note_form');
                    }
                } else {
                    log_message('info', __FUNCTION__ . ' Error in generating credit note');
                    $error_msg = "Error in generating credit note!!! Please Try Again";
                    $this->session->set_flashdata('error_msg', $error_msg);
                    redirect(base_url() . 'employee/invoice/show_purchase_brackets_credit_note_form');
                }
            } else {
                log_message('info', __FUNCTION__ . 'No shipment data found for this order id');
                $error_msg = "No shipment data found for this order id";
                $this->session->set_flashdata('error_msg', $error_msg);
                redirect(base_url() . 'employee/invoice/show_purchase_brackets_credit_note_form');
            }
        }
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
                    $this->form_validation->set_message('validate_order_id', 'Credit Note for this order id is already exist');
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
        $template = 'Vendor_Settlement_Template-FoC-v5.xlsx';
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

        //convert excel to pdf
        $output_file_pdf = TMP_FOLDER . $meta['invoice_id'] . ".pdf";

        putenv('PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/opt/node/bin');
        $tmp_path = TMP_FOLDER;
        $tmp_output_file = TMP_FOLDER . 'output_' . __FUNCTION__ . '.txt';
        $cmd = 'echo ' . $tmp_path . ' & echo $PATH & UNO_PATH=/usr/lib/libreoffice & ' .
                '/usr/bin/unoconv --format pdf --output ' . $output_file_pdf . ' ' .
                $output_file_excel . ' 2> ' . $tmp_output_file;
        $output = '';
        $result_var = '';
        exec($cmd, $output, $result_var);

        //upload file to s3
        $bucket = BITBUCKET_DIRECTORY;
        $directory_xls = "invoices-excel/" . $meta['invoice_id'] . ".xlsx";
        $directory_pdf = "invoices-excel/" . $meta['invoice_id'] . ".pdf";

        $foc_upload = $this->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
        $foc_upload_pdf = $this->s3->putObjectFile($output_file_pdf, $bucket, $directory_pdf, S3::ACL_PUBLIC_READ);

        if ($foc_upload_pdf) {
            log_message('info', __METHOD__ . ": New Credit Note For brackets File uploaded to s3");
        } else {
            log_message('info', __METHOD__ . ": Error in Uploading New Credit Note For brackets File to s3" . $meta['invoice_id'] . "pdf");
        }

        if ($foc_upload) {
            log_message('info', __METHOD__ . ": New Credit Note For brackets File uploaded to s3");
        } else {
            log_message('info', __METHOD__ . ": Error in Uploading New Credit Note For brackets File to s3 " . $meta['invoice_id'] . ".xlsx");
        }

        if (file_exists($output_file_excel) && file_exists($output_file_pdf)) {
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
    function send_brackets_credit_note_mail_sms($vendor_details, $invoice_id, $amount) {


        //send sms
        $sms['tag'] = "vendor_invoice_mailed";
        $sms['smsData']['type'] = 'stand';
        $sms['smsData']['month'] = date('M Y', strtotime($vendor_details[0]['shipment_date']));
        $sms['smsData']['amount'] = $amount;
        $sms['phone_no'] = $vendor_details[0]['owner_phone_1'];
        $sms['booking_id'] = "";
        $sms['type'] = "vendor";
        $sms['type_id'] = $vendor_details[0]['order_received_from'];
        $this->notify->send_sms_msg91($sms);
        log_message('info', __METHOD__ . ' SMS Sent ' . $invoice_id);


        //send email
        $to = $vendor_details[0]['owner_email'].",".ANUJ_EMAIL_ID;
        $from = 'billing@247around.com';

        $message = "Dear Partner,<br/><br/>";
        $message .= "Please find attached invoice for Brackets delivered";
        $message .= "Hope to have a long lasting working relationship with you.";
        $message .= "<br><br>With Regards,
                        <br>247around Team<br>
                        <br>247around is part of Businessworld Startup Accelerator & Google Bootcamp 2015
                        <br>Follow us on Facebook: www.facebook.com/247around
                        <br>Website: www.247around.com
                        <br>Playstore - 247around -
                        <br>https://play.google.com/store/apps/details?id=com.handymanapp";

        $output_file_excel = TMP_FOLDER . $invoice_id . ".pdf";
        $send_mail = $this->notify->sendEmail($from, $to, '', '', 'Credit Note - Brackets Invoice - ' . $vendor_details[0]['company_name'], $message, $output_file_excel);
        if ($send_mail) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
