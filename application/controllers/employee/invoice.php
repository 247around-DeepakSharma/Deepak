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
        $this->load->library('email');

    }

    /**
     * Load invoicing form
     */
    public function index() {
        $this->checkUserSession();
        $invoicingSummary = $this->invoices_model->getsummary_of_invoice("vendor",array('active' => 1, 'is_sf' => 1), date('Y-m-d'));
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
        $partner_source_type = $this->input->post('partner_source_type');
        $sf_cp = json_decode($this->input->post('sf_cp'), true);
        $due_date = $this->input->post('due_date');
        if($vendor_type != ""){
            $sf_cp['active'] = $vendor_type;
        }
          $invoicingSummary= $this->invoices_model->getsummary_of_invoice($vendor_partner,$sf_cp, $due_date, $partner_source_type);
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
        $invoice_type = $this->input->post('invoice_type');
        $data = array('vendor_partner' => $this->input->post('source'),
                      'vendor_partner_id' => $this->input->post('vendor_partner_id'));
        
        $settle_amount = 1;
        if($this->input->post('settle_invoice')){
          
           if($this->input->post('settle_invoice') == 1){
               $settle_amount = 0;
           }
        }
        
        if($invoice_period === 'all'){
            $where = array('vendor_partner' => $this->input->post('source'),
                      'vendor_partner_id' => $this->input->post('vendor_partner_id'));
            if($settle_amount == 0){
                $where['settle_amount'] = 0;
            }
            
            if($invoice_type){
               $types = implode('","', $invoice_type); 
               if($types){
                    $where['type IN ("'.$types.'")'] = NULL;
               }
            }
         
        }else if($invoice_period === 'cur_fin_year'){
            $where = "vendor_partner = '".$this->input->post('source')."' AND vendor_partner_id = '".$this->input->post('vendor_partner_id')
                    ."' AND case WHEN month(CURDATE()) IN ('1','2','3') THEN from_date >= CONCAT(YEAR(CURDATE())-1,'-04-01') "
                    . "and from_date <= CONCAT(YEAR(CURDATE()),'-03-31') WHEN month(from_date) NOT IN ('1','2','3') "
                    . "THEN from_date >= CONCAT(YEAR(CURDATE()),'-04-01') and from_date <= CONCAT(YEAR(CURDATE())+1,'-03-31') END";
            if($settle_amount == 0){
                $where .= " AND settle_amount = 0 ";
            }
            
             if($this->input->post('invoice_type')){
               $types = implode('","', $invoice_type); 
                if($types){
                    $where .= ' AND type IN ("'.$types.'") ';
                }
            }
        }
        
        if(!empty($this->input->post('vertical'))){
            $where['vertical'] = $this->input->post('vertical');
        }
        if(!empty($this->input->post('category'))){
            $where['category'] = $this->input->post('category');
        }
        if(!empty($this->input->post('sub_category'))){
            $where['sub_category'] = $this->input->post('sub_category');
        }
        
        $invoice['invoice_array'] = $this->invoices_model->getInvoicingData($where, false);
        $invoice['invoicing_summary'] = $this->invoices_model->getsummary_of_invoice($data['vendor_partner'],array('id' => $data['vendor_partner_id']))[0];
            
        //TODO: Fix the reversed names here & everywhere else as well
        $data2['partner_vendor'] = $this->input->post('source');
        $data2['partner_vendor_id'] = $this->input->post('vendor_partner_id');
       // $invoice['bank_statement'] = $this->invoices_model->get_bank_transactions_details('*',$data2);
        if ($data['vendor_partner'] == "vendor") {
    
            $invoice['unbilled_amount'] = $this->invoices_model->get_unbilled_amount($data['vendor_partner_id']);
        }
        $invoice['vendor_partner'] = $this->input->post('source');
        $invoice['vendor_partner_id'] = $this->input->post('vendor_partner_id');
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
            if(!empty($data[0]['invoice_detailed_excel'])){
                $detailed_invoice = S3_WEBSITE_URL."invoices-excel/".$data[0]['invoice_detailed_excel'];
            }
            $main_invoice = S3_WEBSITE_URL."invoices-excel/".$data[0]['invoice_file_main'];
            //get email template
            if(($data[0]['category'] == INSTALLATION_AND_REPAIR || $data[0]['category'] == RECURRING_CHARGES) && ($data[0]['sub_category'] == CREDIT_NOTE || $data[0]['sub_category'] == GST_CREDIT_NOTE || $data[0]['sub_category'] == DEBIT_NOTE || $data[0]['sub_category'] == GST_DEBIT_NOTE)){
                $email_template = $this->booking_model->get_booking_email_template("resend_dn_cn_invoice"); 
                $email_template_name = "resend_dn_cn_invoice";
                $subject = vsprintf($email_template[4], array($data[0]['sub_category']));
                $message = vsprintf($email_template[0], array($data[0]['sub_category'], $data[0]['total_amount_collected'], $data[0]['reference_invoice_id']));
            }
            else{
                $email_template = $this->booking_model->get_booking_email_template("resend_invoice"); 
                $email_template_name = "resend_invoice";
                $subject = vsprintf($email_template[4], array(date("jS M, Y", strtotime($start_date)), date("jS M, Y", strtotime($end_date))));
                $message = vsprintf($email_template[0], array(date("jS M, Y", strtotime($start_date)), date("jS M, Y", strtotime($end_date))));
            }
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
            $email_from = $email_template[2];
            $sent = $this->send_email_with_invoice($email_from, $to, $cc, $message, $subject, $detailed_invoice, $main_invoice, $email_template_name);
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
        $data['partnerType'] = array(OEM, EXTWARRANTYPROVIDERTYPE, ECOMMERCETYPE);
        $data['partner'] = $this->partner_model->getpartner("", false);
        $invoicing_summary = $this->invoices_model->getsummary_of_invoice("partner", array('active' => '1'), false, $data['partnerType']);
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
     * @desc: This is used to load update form of bank transaction details
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
        $invoice_id = $this->input->post('invoice');
        $invoice_id_array['partner_vendor'] = $this->input->post('partner_vendor');
        $invoice_id_array['partner_vendor_id'] = $this->input->post('partner_vendor_id');
        $invoice_id_array['bankname'] = $this->input->post('bankname');
        $invoice_id_array['transaction_mode'] = $this->input->post('transaction_mode');
        $invoice_id_array['agent_id'] = $this->input->post('agent_id');
        $invoice_id_array['tdate'] = $this->input->post('tdate');
        $invoice_id_array['description'] = $this->input->post('description');
        $invoice_id_array['transaction_id'] = $this->input->post('transaction_id');
        $invoice_id_array['bank_txn_id'] = $this->input->post('bank_txn_id');
        $invoice_id_array['invoice_id'] = $invoice_id;

        $this->invoice_lib->process_add_new_transaction($invoice_id_array);
        
        redirect(base_url() . 'employee/invoice/invoice_summary/' . $invoice_id_array['partner_vendor'] . "/" . $invoice_id_array['partner_vendor_id']);
        
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
            $where = array();
            if($this->input->post('type')){
                $type = $this->input->post('type');
                
                if($type == BUYBACKTYPE){
                    $where['is_cp'] = 1;
                } else if($type == MICRO_WAREHOUSE_CHARGES_TYPE){
                     $where['is_micro_wh'] = 1;
                } else if($type ==SECURITY){
                     $where['is_sf'] = 1;
                }
            }
            $select = "service_centres.name, service_centres.id";
            $all_vendors = $this->vendor_model->getVendorDetails($select, $where);
            foreach ($all_vendors as $v_name) {
                $option = "<option value='" . $v_name['id'] . "'";
                if ($vendor_partner_id == $v_name['id']) {

                    $option .= "selected ";
                }
                
                $option .=" > ";
                $option .= $v_name['name'] . "</option>";

                echo $option;
            }
            
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
        $data = $misc_data['annexure'];
        $files = array();
        $template = 'Partner_invoice_detail_template-v3.xlsx';

        $meta = $misc_data['meta'];
        $total_misc_charge = 0;
        $total_penalty_discount = 0;
        $penalty_booking_count = 0;
        if(!empty($misc_data['misc'])){
            $total_misc_charge = (array_sum(array_column($misc_data['misc'], 'partner_charge')));
        }
        
        $meta['total_courier_charge'] = (array_sum(array_column($misc_data['final_courier'], 'courier_charges_by_sf')));
        $meta['total_upcountry_price'] = 0;
        $total_upcountry_distance = $total_upcountry_booking = 0;
        

        $output_file_excel = TMP_FOLDER . $meta['invoice_id'] . "-detailed.xlsx";

        $this->invoice_lib->generate_invoice_excel($template, $meta, $data, $output_file_excel);

        // Generate Upcountry Excel
        if (!empty($misc_data['upcountry'])) {
            $meta['total_upcountry_price'] = $misc_data['upcountry'][0]['total_upcountry_price'];
            $total_upcountry_booking = $misc_data['upcountry'][0]['total_booking'];
            $total_upcountry_distance = $misc_data['upcountry'][0]['total_distance'];
            $u_files_name = $this->generate_partner_upcountry_excel($partner_id, $misc_data['upcountry'], $meta);
            array_push($files, $u_files_name);

            log_message('info', __METHOD__ . "=> File created " . $u_files_name);
        }

        if (!empty($misc_data['final_courier'])) {
            $c_files_name = $this->generate_partner_courier_excel($misc_data['final_courier'], $meta);
            array_push($files, $c_files_name);
            log_message('info', __METHOD__ . "=> File created " . $c_files_name);
        }

        if(!empty($misc_data['misc'])){
            $meta['total_misc_charge'] = (array_sum(array_column($misc_data['misc'], 'partner_charge')));
            $c_files_name = $this->generate_partner_misc_excel($misc_data['misc'], $meta);
            array_push($files, $c_files_name);
            log_message('info', __METHOD__ . "=> File created " . $c_files_name);
        }
        
        if(!empty($misc_data['penalty_discount'])){
            $total_penalty_discount = (array_sum(array_column($misc_data['penalty_discount'], 'penalty_amount')));
            $penalty_booking_count = (array_sum(array_column($misc_data['penalty_discount'], 'booking_failed')));
            $c_files_name = $this->generate_partner_penalty_excel($misc_data['penalty_discount'], $misc_data['penalty_tat_count'], $meta['invoice_id']);
            array_push($files, $c_files_name);
            
            $c_files_name1 = $this->generate_partner_penalty__tat_breakup_excel($misc_data['penalty_booking_data'], $meta['invoice_id']);
            array_push($files, $c_files_name1);
        }

        $this->combined_partner_invoice_sheet($output_file_excel, $files);
        array_push($files, $output_file_excel);
        //$convert = $this->invoice_lib->send_request_to_convert_excel_to_pdf($meta['invoice_id'], $invoice_type);
        $convert = $this->invoice_lib->convert_invoice_file_into_pdf($misc_data, $invoice_type);
        
        $output_pdf_file_name = $convert['main_pdf_file_name'];

        array_push($files, TMP_FOLDER . $convert['excel_file']);

        if ($invoice_type == "final") {
            log_message('info', __METHOD__ . "=> Final");
            
            if(isset($data[0]['invoice_email_to'])){
               $invoice_email_to = $data[0]['invoice_email_to'];
               $invoice_email_cc = $data[0]['invoice_email_cc'];
            } else {
                $partner_details = $this->partner_model->getpartner_details('partner_id,invoice_email_to,invoice_email_cc', array('partners.id' =>$partner_id) );
                $invoice_email_to = $partner_details[0]['invoice_email_to'];
                $invoice_email_cc = $partner_details[0]['invoice_email_cc'];
            }
            
            //get email template from database
            $email_template = $this->booking_model->get_booking_email_template(PARTNER_INVOICE_DETAILED_EMAIL_TAG);
            $subject = vsprintf($email_template[4], array($meta['company_name'], $f_date, $t_date));
            $message = $email_template[0];
            $email_from = $email_template[2];

            $to = $invoice_email_to;
            $cc = $invoice_email_cc.", " .ACCOUNTANT_EMAILID;
            $this->upload_invoice_to_S3($meta['invoice_id']);
            $pdf_attachement_url = 'https://s3.amazonaws.com/' . BITBUCKET_DIRECTORY . '/invoices-excel/' . $output_pdf_file_name;

            $this->send_email_with_invoice($email_from, $to, $cc, $message, $subject, $output_file_excel, $pdf_attachement_url,PARTNER_INVOICE_DETAILED_EMAIL_TAG);
           

            $invoice_details = array(
                'invoice_id' => $meta['invoice_id'],
                'type_code' => 'A',
                'type' => 'Cash',
                'vendor_partner' => 'partner',
                'vendor_partner_id' => $partner_id,
                'invoice_file_main' => $output_pdf_file_name,
                'invoice_file_excel' => $meta['invoice_id'] . ".xlsx",
                'invoice_detailed_excel' => $meta['invoice_id'] . '-detailed.xlsx',
                'from_date' => date("Y-m-d", strtotime($f_date)), //??? Check this next time, format should be YYYY-MM-DD
                'to_date' => date("Y-m-d", strtotime($t_date)),
                'num_bookings' => $meta['service_count'],
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
                "hsn_code" => $hsn_code,
                'packaging_quantity' => $misc_data['packaging_quantity'],
                'packaging_rate' => $misc_data['packaging_rate'],
                'miscellaneous_charges' => $total_misc_charge,
                'warehouse_storage_charges' => $misc_data['warehouse_storage_charge'],
                'penalty_amount'=> $total_penalty_discount,
                'penalty_bookings_count' => $penalty_booking_count,
                'vertical' => SERVICE,
                'category' => INSTALLATION_AND_REPAIR,
                'sub_category' => CASH,
                'accounting' => 1,
            );

            $this->invoices_model->insert_new_invoice($invoice_details);
            log_message('info', __METHOD__ . "=> Insert Invoices in partner invoice table");
            //Insert invoice Breakup
            $this->insert_invoice_breakup($misc_data);

            foreach ($data as $value1) {

                log_message('info', __METHOD__ . "=> Invoice update in booking unit details unit id" . $value1['unit_id'] . " Invoice Id" . $meta['invoice_id']);
                $this->booking_model->update_booking_unit_details_by_any(array('id' => $value1['unit_id']), array('partner_invoice_id' => $meta['invoice_id']));
            }
            

            if (!empty($misc_data['upcountry'])) {
                foreach ($misc_data['upcountry'] as $up_booking_details) {
                    $up_b = explode(",", $up_booking_details['booking_id']);
                    for($i=0; $i < count($up_b); $i++){
                        
                        $this->booking_model->update_booking(trim($up_b[$i]), array('upcountry_partner_invoice_id' => $meta['invoice_id']));
                    }

                }
            }
            
            if(!empty($misc_data['warehouse_courier'])){
                foreach ($misc_data['warehouse_courier'] as $spare_courier) {
                    $sp_id = explode(",", $spare_courier['sp_id']);
                    foreach($sp_id as $sid){
                        $this->service_centers_model->update_spare_parts(array('id' => $sid), array('partner_warehouse_courier_invoice_id' =>$meta['invoice_id']));
                    }
                }
            }
            
            if(!empty($misc_data['defective_part_by_wh'])){
                foreach ($misc_data['defective_part_by_wh'] as $defective_id) {
                    $c_id = explode(",", $defective_id['c_id']);
                    foreach($c_id as $cid){
                       $this->inventory_model->update_courier_detail(array('id' => $cid), array('partner_invoice_id' => $meta['invoice_id']));
                    }
                }
            }
            
            if(!empty($misc_data['courier'])){
                foreach ($misc_data['courier'] as $spare_array) {
                    $s_id = explode(",", $spare_array['sp_id']);
                    foreach($s_id as $spare_id){
                        $this->service_centers_model->update_spare_parts(array('id' => $spare_id), array('partner_courier_invoice_id' => $meta['invoice_id']));
                    }
                }
            }
            
            if(!empty($misc_data['pickup_courier'])){
                foreach ($misc_data['pickup_courier'] as $pickup) {
                    $s_id = explode(",", $pickup['sp_id']);
                    foreach($s_id as $spare_id){
                        $this->service_centers_model->update_spare_parts(array('id' => $spare_id), array('partner_warehouse_courier_invoice_id' => $meta['invoice_id']));
                    }
                }
            }
            
            if(!empty($misc_data['pickup_courier'])){
                foreach ($misc_data['pickup_courier'] as $pickup) {
                    $this->inventory_model->update_courier_company_invoice_details(array('awb_number' => $pickup['awb']), 
                            array('partner_id' =>$partner_id, "booking_id" => $pickup['booking_id'], "partner_invoice_id" => $meta['invoice_id'],
                                'basic_billed_charge_to_partner' => $pickup['courier_charges_by_sf']));
                }
            }
            
            if(!empty($misc_data['courier'])){
                foreach ($misc_data['courier'] as $spare_array) {
                   
                    $this->inventory_model->update_courier_company_invoice_details(array('awb_number' => $spare_array['awb']), 
                            array('partner_id' =>$partner_id, "booking_id" => $spare_array['booking_id'], "partner_invoice_id" => $meta['invoice_id'],
                                'basic_billed_charge_to_partner' => $spare_array['courier_charges_by_sf']));
                }
            }

            if(!empty($misc_data['misc'])){
                foreach ($misc_data['misc'] as $value) {
                    $this->booking_model->update_misc_charges(array('id' => $value['id']), array('partner_invoice_id' => $meta['invoice_id']));
                }
                exec("rm -rf " . escapeshellarg(TMP_FOLDER . $meta['invoice_id'] . "-miscellaneous-detailed.xlsx"));
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
       ob_start();
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
            ob_end_flush();
            $res1 = 0;
            system(" chmod 777 " . TMP_FOLDER . $invoice_id . '.zip ', $res1);
            readfile(TMP_FOLDER . $invoice_id. '.zip');
            exec("rm -rf " . escapeshellarg(TMP_FOLDER . $invoice_id . '.zip'));
            exec("rm -rf " . escapeshellarg(TMP_FOLDER . "copy_" . $invoice_id . "-draft.xlsx"));
            exec("rm -rf " . escapeshellarg(TMP_FOLDER . "copy_" . $invoice_id . "-draft.pdf"));
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
    
    function generate_partner_misc_excel($data, $meta){
        $template = 'Partner_invoice_detail_template-v2-misc.xlsx';
        $output_file_excel = TMP_FOLDER . $meta['invoice_id'] . "-miscellaneous-detailed.xlsx";
        $this->invoice_lib->generate_invoice_excel($template, $meta, $data, $output_file_excel);
        return $output_file_excel;
    }
    /**
     * @desc This function is used to generate penalty annexure file
     * Partner apply penalty on Around
     * @param Array $tat_data
     * @param Array $tat_count
     * @param int $invoice_id
     * @return string
     */
    function generate_partner_penalty_excel($tat_data, $tat_count, $invoice_id){
        $template = 'partner_penalty_discount.xlsx';
        $output_file_excel = TMP_FOLDER . $invoice_id . "-penalty-detailed.xlsx";
        $this->invoice_lib->generate_invoice_excel($template, $tat_count, $tat_data, $output_file_excel, true);
        return $output_file_excel;
    }
    
    function generate_partner_penalty__tat_breakup_excel($tat_breakup, $invoice_id){
        $template = 'Penatly_tat_breakup-v1.xlsx';
        $output_file_excel = TMP_FOLDER . $invoice_id . "-penalty-tat-breakup-detailed.xlsx";
        $this->invoice_lib->generate_invoice_excel($template, array(), $tat_breakup, $output_file_excel, true);
        return $output_file_excel;
    }

    /**
     * It generates cash invoices for vendor
     * @param Array $invoices
     * @param Array $details
     * @return type
     */
    function generate_cash_details_invoices_for_vendors($vendor_id, $data, $invoices_d,$invoice_type, $agent_id, $from_date, $to_date) {
        log_message('info', __FUNCTION__ . '=> Entering... for invoices:'. $invoices_d['meta']['invoice_id']);
        $meta  = $invoices_d['meta'];
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
                    '. Total transaction value for the bookings was Rs. ' . sprintf("%.2f",$meta['total_amount_paid']) .
                    '. Around royalty for this invoice is Rs. ' . sprintf("%.2f",$meta['sub_total_amount']) .
                    '. Your rating for completed bookings is ' . $meta['t_rating'] .
                    '. We look forward to your continued support in future. As next step, please deposit ' .
                    '247around royalty per the below details.';
        
        $output_file_excel = TMP_FOLDER . $meta['invoice_id'] . "-detailed.xlsx";
        $this->invoice_lib->generate_invoice_excel($template, $meta, $data, $output_file_excel);
        array_push($files, $output_file_excel);
        
//      $convert = $this->invoice_lib->send_request_to_convert_excel_to_pdf($meta['invoice_id'], $invoice_type); 
        $convert = $this->invoice_lib->convert_invoice_file_into_pdf($invoices_d, $invoice_type);
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
                'total_amount_collected' => $meta['sub_total_amount'],
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
                "invoice_file_pdf" => $convert['copy_file'],
                "vertical" => SERVICE,
                "category" => INSTALLATION_AND_REPAIR,
                "sub_category" => COMMISSION,
                "accounting" => 1,
            );

            $this->invoices_model->action_partner_invoice($invoice_details);
            
            //Insert invoice Breakup
            $this->insert_invoice_breakup($invoices_d);
         
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
                 if(isset($value['detailed_excel'])){
                     $this->email->attach($value['detailed_excel'], 'attachment');
                 }
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
                        if($value['unit_id'] != "Misc"){
                            log_message('info', __METHOD__ . ': update invoice id in booking unit details ' . $value['unit_id'] . " invoice id " . $invoice_id);
                            $this->booking_model->update_booking_unit_details_by_any(array('id' => $value['unit_id']), array($unit_column => $invoice_id));
                        }
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
               
                $invoice_details[$j]['amount_paid'] = sprintf("%.2f",($invoice_details[$j]['vendor_installation_charge'] + 
                        $invoice_details[$j]['vendor_stand']));
                
                if(!empty($invoice_details[$j]['rating_stars'])){
                    $rating += $invoice_details[$j]['rating_stars'];
                    $rating_count++;
                } else {
                    $rating += 1;
                }
            }
            if($rating_count == 0){
                $rating_count = 1;
            }
            
            $total_misc_charges = 0;
            if($invoice_data['misc']){
                $total_misc_charges = (array_sum(array_column($invoice_data['misc'], 'total_booking_charge')));
                
                $invoice_details = array_merge($invoice_details, $invoice_data['misc']);
            }

            $t_total = $invoice_data['meta']['total_sc_charge'] + $total_stand_charge;
            
            $tds = $this->check_tds_sc($invoice_data['booking'][0], $invoice_data['meta']['total_sc_charge']);

            // count unique booking id
            $invoice_data['meta']['count'] = $invoice_data['meta']['service_count'];
            $invoice_data['meta']['tds'] = $tds['tds'];
            $invoice_data['meta']['tds_rate'] = $tds['tds_rate'];
            $invoice_data['meta']['warehouse_storage_charge'] = $invoice_data['warehouse_storage_charge'];
            $invoice_data['meta']['tds_tax_rate'] = $tds['tds_per_rate'];
            $invoice_data['meta']['t_ic'] = sprintf("%.2f",($total_inst_charge));
            $invoice_data['meta']['t_stand'] = sprintf("%.2f",$total_stand_charge);
            $invoice_data['meta']['t_total'] =  sprintf("%.2f",$t_total);
            $invoice_data['meta']['total_gst_amount'] =  sprintf("%.2f",$invoice_data['meta']["cgst_total_tax_amount"] + $invoice_data['meta']["sgst_total_tax_amount"] +
                    $invoice_data['meta']["igst_total_tax_amount"]);
            $invoice_data['meta']['t_rating'] = $rating/$rating_count;
            $invoice_data['meta']['cr_total_penalty_amount'] = sprintf("%.2f",(array_sum(array_column($invoice_data['c_penalty'], 'p_amount'))));
            $invoice_data['meta']['total_penalty_amount'] = -sprintf("%.2f",(array_sum(array_column($invoice_data['d_penalty'], 'p_amount'))));
            $invoice_data['meta']['total_upcountry_price'] = sprintf("%.2f",$total_upcountry_price);
            $invoice_data['meta']['total_courier_charges'] = sprintf("%.2f",(array_sum(array_column($invoice_data['courier'], 'courier_charges_by_sf'))));;
            
            $invoice_data['meta']['t_vp_w_tds'] = sprintf("%.2f", ($invoice_data['meta']['sub_total_amount'] - $invoice_data['meta']['tds']));
            
            $invoice_data['meta']['msg'] = 'Thanks 247around Partner for your support, we completed ' .  $invoice_data['meta']['count'] .
                    ' bookings with you from ' .  $invoice_data['meta']['sd'] . ' to ' .  $invoice_data['meta']['ed'] .
                    '. Total transaction value for the bookings was Rs. ' .  $invoice_data['meta']['t_vp_w_tds'] .
                    '. Your rating for completed bookings is ' . sprintf("%.2f", $invoice_data['meta']['t_rating']) .
                    '. We look forward to your continued support in future. As next step, 247around will pay you remaining amount as per our agreement.';

           
            log_message('info', 'Excel data: ' . print_r( $invoice_data['meta'], true));

          
            //Get populated XLS with data
            $output_file_excel = TMP_FOLDER . $invoice_data['meta']['invoice_id'] . "-detailed.xlsx";
           
            $this->generate_foc_detailed_invoice_excel($template, $invoice_data, $invoice_details, $output_file_excel);
            log_message('info', __FUNCTION__ . " Excel File Created " . $output_file_excel);
            
            array_push($files, $output_file_excel);
            $convert = $this->invoice_lib->convert_invoice_file_into_pdf($invoice_data, $invoice_type, true, FALSE);
            //$convert = $this->invoice_lib->send_request_to_convert_excel_to_pdf($invoice_data['meta']['invoice_id'], $invoice_type,true); 
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
                    "hsn_code" => $invoice_data['booking'][0]['hsn_code'],
                    "miscellaneous_charges" => $total_misc_charges,
                    "warehouse_storage_charges" => $invoice_data['warehouse_storage_charge'],
                    "packaging_rate" => $invoice_data['packaging_rate'],
                    "packaging_quantity" => $invoice_data['packaging_quantity'],
                    "vertical" => SERVICE,
                    "category" => INSTALLATION_AND_REPAIR,
                    "sub_category" => FOC,
                    "accounting" => 1,
                );
                
                // insert invoice details into vendor partner invoices table
                $this->invoices_model->action_partner_invoice($invoice_details_insert);
                log_message("info", __METHOD__. " Main Invoice inserted ". $invoice_data['meta']['invoice_id']);
                $gst_amount =  ($invoice_data['meta']['igst_total_tax_amount'] +  $invoice_data['meta']["cgst_total_tax_amount"] +  $invoice_data['meta']["sgst_total_tax_amount"]);
                if($gst_amount > 0){
                  
                    $debit_invoice_details = array(
                        'invoice_id' => "Around-GST-DN-".$invoice_data['meta']['invoice_id'],
                        "reference_invoice_id" => $invoice_data['meta']['invoice_id'],
                        'type' => 'DebitNote',
                        'credit_generated' => 0,
                        'vendor_partner_id' => $vendor_id,
                        'type_code' => 'A',
                        'vendor_partner' => 'vendor',
                        'invoice_date' => date("Y-m-d"),
                        'from_date' => date("Y-m-d", strtotime($from_date)),
                        'to_date' => date("Y-m-d", strtotime($to_date)),
                        'total_service_charge' => ( $invoice_data['meta']["cgst_total_tax_amount"] +  $invoice_data['meta']["sgst_total_tax_amount"] +  $invoice_data['meta']["igst_total_tax_amount"]),
                        'total_amount_collected' => ( $invoice_data['meta']["cgst_total_tax_amount"] +  $invoice_data['meta']["sgst_total_tax_amount"] +  $invoice_data['meta']["igst_total_tax_amount"]),
                        //Amount needs to be Paid to Vendor
                        'amount_collected_paid' => $invoice_data['meta']["cgst_total_tax_amount"] +  $invoice_data['meta']["sgst_total_tax_amount"] +  $invoice_data['meta']["igst_total_tax_amount"],
                        //Add 1 month to end date to calculate due date
                        'due_date' => date("Y-m-d", strtotime($to_date . "+1 month")),
                        //add agent id
                        'agent_id' => $agent_id,
                        'vertical' => SERVICE,
                        'category' => INSTALLATION_AND_REPAIR,
                        'sub_category' => GST_DEBIT_NOTE,
                        'accounting' => 1,
                    );
            
                    $this->invoices_model->action_partner_invoice($debit_invoice_details);
                    log_message("info", __METHOD__. " GST Invoice inserted Around-GST-CN-".$invoice_data['meta']['invoice_id']);
                }
                
                
                //Insert invoice Breakup
                $this->insert_invoice_breakup($invoice_data);
                
                //Update Penalty Amount
                foreach ($invoice_data['d_penalty'] as $value) {
                    $explode = explode(",", $value['p_id']);
                    if(!empty($explode)){
                        foreach ($explode as $p_id) {
                            $this->penalty_model->update_penalty_any(array('id' => $p_id), array('foc_invoice_id' => $invoice_data['meta']['invoice_id']));
                        }
                    }
                    
                }
                
                foreach ($invoice_data['c_penalty'] as $value) {
                    $explode = explode(",", $value['c_id']);
                    if(!empty($explode)){
                        foreach ($explode as $p_id) {
                            $this->penalty_model->update_penalty_any(array('id' => $p_id), array('removed_penalty_invoice_id' => $invoice_data['meta']['invoice_id']));
                        }
                    }
                    
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
                
                if(!empty($invoice_data['misc'])){
                    foreach ($invoice_data['misc'] as $misc) {
                        $this->booking_model->update_misc_charges(array('id' => $misc['misc_id']), array('vendor_invoice_id' => $invoice_data['meta']['invoice_id']));
                    }
                }
                
               if(!empty($invoice_data['warehouse_courier'])){
                foreach ($invoice_data['warehouse_courier'] as $spare_courier) {
                    $sp_id = explode(",", $spare_courier['sp_id']);
                    foreach($sp_id as $sid){
                        $this->service_centers_model->update_spare_parts(array('id' => $sid), array('warehouse_courier_invoice_id' =>$invoice_data['meta']['invoice_id']));
                    }
                }
            }
            
//            if(!empty($invoice_data['defective_return_to_partner'])){
//                foreach ($invoice_data['defective_return_to_partner'] as $defective_id) {
//                    $c_id = explode(",", $defective_id['c_id']);
//                    foreach($c_id as $cid){
//                        $this->inventory_model->update_courier_detail(array('id' => $cid), array('sender_invoice_id' => $invoice_data['meta']['invoice_id']));
//                    }
//                }
//            }
            
            if(!empty($invoice_data['courier'])){
                foreach ($invoice_data['courier'] as $spare_array) {
                    $s_id = explode(",", $spare_array['sp_id']);
                    foreach($s_id as $spare_id){
                        $this->service_centers_model->update_spare_parts(array('id' => $spare_id), array('vendor_courier_invoice_id' =>$invoice_data['meta']['invoice_id']));
                        
                    }
                }
            }

            /*
             * Update booking-invoice table to capture this new invoice against these bookings.
             * Since this is a type B invoice, it would be stored as a vendor-credit invoice.
             */
            if(!empty($invoice_details)){
                $this->update_invoice_id_in_unit_details($invoice_details, $invoice_data['meta']['invoice_id'], $invoice_type, "vendor_foc_invoice_id");
            }
            
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
        if(ob_get_length() > 0) {
            ob_end_clean();
        }
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
                    'data' =>  $invoice_data['final_courier_data']
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
     * This method is used to get data from Form.
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
        } else if($this->session->userdata('error')){
            log_message('info', __METHOD__. "Invoice error already set");
        } else {
            $output = "Invoice is not generating. Data is not Found";
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

            $s = $this->generate_vendor_invoices($details, 0);
        } else if ($details['vendor_partner'] === "partner") {
            log_message('info', "Invoice generate - partner id: " . print_r($details['vendor_partner_id'], true) . ", Date Range" .
                    print_r($details['date_range'], true) . ", Invoice status" . print_r($details['invoice_type'], true));

            $s = $this->generate_partner_invoices($details['vendor_partner_id'], $details['date_range'], $details['invoice_type'],$details['agent_id']);
        }
        
        if($s && $details['vendor_partner_id'] = "All"){
            $email_template = $this->booking_model->get_booking_email_template(ALL_INVOICE_SUCCESS_MESSAGE);
            $subject = vsprintf($email_template[4], array($details['vendor_invoice_type'],$details['date_range'] ));
            $message = vsprintf($email_template[0], array($details['vendor_invoice_type']));
            $email_from = $email_template[2];
            $to = $email_template[1];

            $this->notify->sendEmail($email_from, $to, '', '', $subject, $message, '',ALL_INVOICE_SUCCESS_MESSAGE);

        }
        
        return $s;

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
        $sms['smsData']['amount'] = sprintf("%.2f",$total);
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
               
                
                //$convert = $this->invoice_lib->send_request_to_convert_excel_to_pdf($invoice['meta']['invoice_id'], $invoice_type);
                $convert = $this->invoice_lib->convert_invoice_file_into_pdf($invoice, $invoice_type);
                unset($invoice['booking']);
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
                        "invoice_file_pdf" => $convert['copy_file'],
                        "vertical" => SERVICE,
                        "category" => SPARES,
                        "sub_category" => BRACKETS,
                        "accounting" => 1
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
               
                //unset($invoices['booking']);
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

                $data = $this->invoices_model->get_vendor_cash_detailed($vendor_id, $from_date, $to_date, $is_regenerate);
                $invoices_details_data = array_merge($data, $invoices['upcountry']);
                $invoices['meta']['r_sc'] = $invoices['meta']['r_asc'] = $invoices['meta']['r_pc'] = $rating = $total_amount_paid = 0;
                $i = 0;
                $parts_count = 0;
                foreach ($invoices_details_data as $value) {
                    $invoices['meta']['r_sc'] += $value['service_charges'];
                    $invoices['meta']['r_asc'] += $value['additional_charges'];
                    $invoices['meta']['r_pc'] += $value['parts_cost'];
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
                $invoices['meta']['total_amount_paid'] = sprintf("%.2f",$total_amount_paid);
                $invoices['meta']['t_rating'] = sprintf("%.2f",$rating / $i);
                $this->generate_cash_details_invoices_for_vendors($vendor_id, $invoices_details_data, $invoices, $invoice_type, $agent_id, $from_date, $to_date);
                unset($invoices_details_data);
                unset($invoices);

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
                    $res = $this->generate_buyback_detailed_invoices($vendor_id, $invoices, $invoice_type, $details['agent_id'], $from_date, $to_date);
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
                
                //get email template from database
                $email_template = $this->booking_model->get_booking_email_template(BUYBACK_DETAILS_INVOICE_FOR_VENDORS_EMAIL_TAG);
                $subject = vsprintf($email_template[4], array($company_name,$sd,$ed));
                $message = $email_template[0];
                $email_from = $email_template[2];
                $cc = $rem_email_id.",".$email_template[3];
             
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

    function generate_buyback_detailed_invoices($vendor_id, $invoices, $invoice_type, $agent_id,$from_date, $to_date){
        log_message('info', __FUNCTION__ . " Entering...." );
        $files = array();
        $data = $invoices['annexure_data'];
        $meta =  $invoices['meta'];
        $template = 'Buyback-Annexure-v1.xlsx';
        $output_file_excel = TMP_FOLDER . $meta['invoice_id'] . "-detailed.xlsx";
        $this->invoice_lib->generate_invoice_excel($template, $meta, $data, $output_file_excel);
        array_push($files, $output_file_excel);
        
        //$convert = $this->invoice_lib->send_request_to_convert_excel_to_pdf($meta['invoice_id'], $invoice_type); 
        $convert = $this->invoice_lib->convert_invoice_file_into_pdf($invoices, $invoice_type);
        $output_file_main = $convert['main_pdf_file_name'];
        array_push($files, TMP_FOLDER.$convert['excel_file']);

        log_message('info', 'Excel data: ' . print_r($meta, true));
         if ($invoice_type === "final") {
            $out['pdf'] = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/invoices-excel/".$output_file_main;
            $out['excel'] = $output_file_excel;
            $out['invoice_id'] = $meta['invoice_id'];
            $out['detailed_excel'] = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/invoices-excel/".$meta['invoice_id'] . '-detailed.xlsx';
            
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
                "invoice_file_pdf" => $convert['copy_file'],
                "cgst_tax_rate" => $meta['cgst_tax_rate'],
                "sgst_tax_rate" => $meta['sgst_tax_rate'],
                "igst_tax_rate" => $meta['igst_tax_rate'],
                "igst_tax_amount" => $meta["igst_total_tax_amount"],
                "sgst_tax_amount" => $meta["sgst_total_tax_amount"],
                "cgst_tax_amount" => $meta["cgst_total_tax_amount"],
                "vertical" => BUYBACK_TYPE,
                "category" => EXCHANGE,
                "sub_category" => SALE,
                "accounting" => 1
            );

            $this->invoices_model->action_partner_invoice($invoice_details);
            exec("rm -rf " . escapeshellarg(TMP_FOLDER . "copy_" . $meta['invoice_id'] . ".xlsx"));
            log_message('info', __METHOD__ . ': Invoice ' . $meta['invoice_id'] . ' details  entered into invoices table');

            //Insert invoice Breakup
            $this->insert_invoice_breakup($invoices);


            $this->update_invoice_id_in_buyback($data, $meta['invoice_id'], $invoice_type, "cp_invoice_id");
            
        } else {
            $out['invoice_id'] = $meta['invoice_id'];
            $out['excel'] = $output_file_excel;
            $out['pdf'] = $output_file_main;
            $this->download_invoice_files($meta['invoice_id'], $output_file_excel, $output_file_main);
        }
        $out['files'] = $files;
        //Do not Delete XLS files now
        foreach ($files as $file_name) {
            exec("rm -rf " . escapeshellarg($file_name));
        }
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
            if($invoices['booking'][0]['minimum_guarantee_charge'] > 0 && $invoice_type == "final"){

                if($invoices['booking'][0]['minimum_guarantee_charge'] > $invoices['meta']['sub_total_amount']){
                    $this->send_guarantee_exist_mail(array('minimum_guarantee_charge' => $invoices['booking'][0]['minimum_guarantee_charge'],
                      'invoice_amount' => $invoices['meta']['sub_total_amount'], "company_name" => $invoices['booking'][0]['company_name'], 
                        'from_date' =>  date('M', strtotime($from_date)), 'to_date' => $to_date, 'vendor_id' => $vendor_id));
                }
            }   
                
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
                    $this->session->set_userdata(array('error' => "Invoice File did not create"));
                    log_message('info', __FUNCTION__ . ' Invoice File did not create. invoice id' . $invoices['meta']['invoice_id']);
                    return FALSE;
                }
            } else {
                //Negative Amount Invoice
                $this->session->set_userdata(array('error' => "Vendor has negative invoice amount"));
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
            if($invoice_type == "final"){
                $select = 'company_name, minimum_guarantee_charge ';
                $vendor_details = $this->vendor_model->getVendorDetails($select, array('id' => $vendor_id));
            
                if(!empty($vendor_details) && $vendor_details[0]['minimum_guarantee_charge'] > 0){
                    $basic_min_guarantee_charge =  ($vendor_details[0]['minimum_guarantee_charge'] * SERVICE_TAX_RATE)/(1 + SERVICE_TAX_RATE);


                    $this->send_guarantee_exist_mail(array('minimum_guarantee_charge' => $vendor_details[0]['minimum_guarantee_charge'],
                      'invoice_amount' => 0, "company_name" => $vendor_details[0]['company_name'], 
                        'from_date' => date('M', strtotime($from_date)), 'vendor_id' => $vendor_id));

                }
            }
        
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
            $invoice_details['invoice_breakup'] = $this->invoices_model->get_breakup_invoice_details("*", array('invoice_id' => $invoice_id));
        }
        $invoice_details['vendor_partner'] = $vendor_partner;
        if(isset($invoice_details['invoice_breakup']) && !empty($invoice_details['invoice_breakup'])){
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/update_invoices_with_breakup', $invoice_details);
        } else {
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/insert_update_invoice', $invoice_details);
        }
        
    }
    /*
     * This function is use to process detailed file of buyback reimburshment 
     * It update reimburshment invoice id and discount amount against orders in unit detail file
     */
    function process_buyback_reimburshment_detailed_file(){
        log_message('info', __FUNCTION__ . " Entering...." );
        $temp['file'] = $_FILES['invoice_detailed_excel'];
        $data = $this->miscelleneous->excel_to_Array_converter($temp,NULL,1);
        log_message('info', __FUNCTION__ . " File Data".print_r($data,true) );
        $count = count($data);
        for($i= 0 ;$i<$count-1;$i++){
            if($data[$i]['OrderID']){
                $where['partner_order_id'] =  $data[$i]['OrderID'];
                $updateDataArray['partner_discount'] = $data[$i]['ReimbursementValue'];
                $updateDataArray['partner_reimbursement_invoice'] = $this->input->post('invoice_id');
                 log_message('info', __FUNCTION__ . " File where".print_r($where,true). "Update Data".print_r($updateDataArray,true));
                $this->bb_model->update_bb_unit_details($where,$updateDataArray);
            }
        }
        log_message('info', __FUNCTION__ . " End" );
    }

    function process_buyback_cp_credit_note_detailed_file(){
        log_message('info', __FUNCTION__ . " Entering...." );
        $temp['file'] = $_FILES['invoice_detailed_excel'];
        $data = $this->miscelleneous->excel_to_Array_converter($temp,NULL,0);
        log_message('info', __FUNCTION__ . " File Data".print_r($data,true) );
        $count = count($data);
        for($i= 0 ;$i<$count-1;$i++){
            if($data[$i]['Orderid']){
                $where['partner_order_id'] =  $data[$i]['Orderid'];
                $updateDataArray['cp_discount'] = $data[$i]['AmazonPrice'];
                $updateDataArray['cp_credit_note_invoice'] = $this->input->post('invoice_id');
                 log_message('info', __FUNCTION__ . " File where".print_r($where,true). "Update Data".print_r($updateDataArray,true));
                $this->bb_model->update_bb_unit_details($where,$updateDataArray);
            }
        }
        log_message('info', __FUNCTION__ . " End" );
    }
    /**
     * @desc: Update/ Insert Partner Invoice Details from panel
     * @param String $vendor_partner
     */
    function process_insert_update_invoice($vendor_partner) {
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . " Entering...." . $vendor_partner);
        $this->form_validation->set_rules('vendor_partner_id', 'Vendor Partner', 'required|trim');
        $this->form_validation->set_rules('invoice_id', 'Invoice ID', 'required|trim');
        $this->form_validation->set_rules('around_type', 'Around Type', 'required|trim');
        $this->form_validation->set_rules('gst_rate', 'GST Rate', 'required|trim');
        $this->form_validation->set_rules('from_date', 'Invoice Period', 'required|trim');
        $this->form_validation->set_rules('type', 'Type', 'required|trim');
        if ($this->form_validation->run()) {
            $flag = true;
            $data = $this->get_create_update_invoice_input($vendor_partner);
            $in_data = $this->invoices_model->get_invoices_details(array('invoice_id' => $data['invoice_id']),'*');
            if (!empty($in_data)) {
                if ($data['vendor_partner_id'] != $in_data[0]['vendor_partner_id']) {
                    $flag = false;
                }
            }

            if ($flag) {
                $total_amount_collected = ($data['total_service_charge'] +
                        $data['total_additional_service_charge'] +
                        ($data['packaging_rate']  * $data['packaging_quantity'])+
                        $data['parts_cost'] + $data['courier_charges'] + 
                        $data['miscellaneous_charges'] + $data['warehouse_storage_charges'] + 
                        $data['upcountry_price'] + $data['credit_penalty_amount'] - $data['penalty_amount']);
                
                $tds_sc_charge = $total_amount_collected - $data['parts_cost'];

                $entity_details = array();
                $gst_number = "";

                if ($data['vendor_partner'] == "vendor") {
                    $entity_details = $this->vendor_model->viewvendor($data['vendor_partner_id']);
                    
                    $gst_number = $entity_details[0]['gst_no'];
                    if($data['type_code'] == "A" && empty($gst_number)){
                        $gst_number = TRUE;
                    }
                    
                } else {

                    $entity_details = $this->partner_model->getpartner_details("gst_number, state", array('partners.id' => $data['vendor_partner_id']));
                    $gst_number = $entity_details[0]['gst_number'];
                    if (empty($gst_number)) {

                        $gst_number = TRUE;
                    }
                }

                if (empty($gst_number)) {
                    $gst_rate = 0;
                } else {
                    $gst_rate = $this->input->post('gst_rate');
                }


                $gst_amount = $total_amount_collected * ($gst_rate / 100);
                $data['total_amount_collected'] = sprintf("%.2f",($total_amount_collected + $gst_amount));

                $data['rcm'] = 0;

                $c_s_gst = $this->invoices_model->check_gst_tax_type($entity_details[0]['state']);
                if ($c_s_gst) {

                    $data['cgst_tax_amount'] = $data['sgst_tax_amount'] = $gst_amount / 2;
                    $data['cgst_tax_rate'] = $data['sgst_tax_rate'] = $gst_rate / 2;
                    $data['igst_tax_rate'] = 0;
                } else {

                    $data['igst_tax_amount'] = $gst_amount;
                    $data['igst_tax_rate'] = $gst_rate;
                    $data['cgst_tax_rate'] = $data['sgst_tax_rate'] = 0;
                }


                switch ($data['type_code']) {
                    case 'A':
                        log_message('info', __FUNCTION__ . " .. type code:- " . $data['type']);
                        $data['around_royalty'] = sprintf("%.2f",$data['total_amount_collected']);
                        $data['amount_collected_paid'] = sprintf("%.2f",$data['total_amount_collected']);

                        break;
                    case 'B':
                        log_message('info', __FUNCTION__ . " .. type code:- " . $data['type']);

                        $tds['tds'] = 0;
                        $tds['tds_rate'] = 0;
                        if ($data['type'] == 'FOC') {

                            if ($vendor_partner == "vendor") {
                                $tds = $this->check_tds_sc($entity_details[0], ($tds_sc_charge));
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
                        } else if ($data['type'] == 'CreditNote' || $data['type'] == 'Buyback' || $data['type'] == 'Stand' || $data['type'] == "Parts" || $data['type'] == LIQUIDATION) {

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
                $data['vertical'] = $this->input->post("vertical");
                $data['category'] = $this->input->post("category");
                $data['sub_category'] = $this->input->post("sub_category");
                $data['accounting'] = $this->input->post("accounting_input");
                
                $status = $this->invoices_model->action_partner_invoice($data);

                if ($status) {
                    //Process Detailed File For buyback Reimburshment
//                    if(($this->input->post('vertical') == BUYBACK_TYPE) && ($this->input->post('sub_category') == BUYBACK_INVOICE_SUBCAT_REIMBURSEMENT) 
//                            && ($this->input->post('vendor_partner_id') == AMAZON_SELLER_ID) && $_FILES['invoice_detailed_excel']['tmp_name'] ){
//                        $this->process_buyback_reimburshment_detailed_file();
//                    }
                    //Process Detailed File For buyback CP Credit note
//                    if(($this->input->post('vertical') == BUYBACK_TYPE) && ($this->input->post('sub_category') == BUYBACK_CP_CREDIT_NOTE_SUBCAT) && 
//                            ($this->input->post('around_type') == 'B') && $_FILES['invoice_detailed_excel']['tmp_name']){
//                        $this->process_buyback_cp_credit_note_detailed_file();
//                    }
                    log_message('info', __METHOD__ . ' Invoice details inserted ' . $data['invoice_id']);
                } else {

                    log_message('info', __METHOD__ . ' Invoice details not inserted ' . $data['invoice_id']);
                }

                redirect(base_url() . 'employee/invoice/invoice_summary/' . $data['vendor_partner'] . "/" . $data['vendor_partner_id']);
            } else {
               
                $userSession = array('error' => "Invoice already mapped to another partner");
                $this->session->set_userdata($userSession);
                redirect(base_url() . 'employee/invoice/insert_update_invoice/' . $vendor_partner);
            }
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
        $data['reference_invoice_id'] = $this->input->post('reference_invoice_id');
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
        $data['packaging_quantity'] = $this->input->post('packaging_quantity');
        $data['packaging_rate'] = $this->input->post('packaging_rate');
        $data['miscellaneous_charges'] = $this->input->post('miscellaneous_charges');
        $data['warehouse_storage_charges'] = $this->input->post('warehouse_storage_charges');
        $data['type_code'] = $this->input->post('around_type');
        
       
        return $data;
    }

    /**
     * @desc: Calculate TDS Amount 
     * @param Array $sc_details
     * @param String $total_sc_charge
     * @return Array
     */
    function check_tds_sc($sc_details, $total_sc_charge) {
        log_message('info', __FUNCTION__ . " Entering....". $total_sc_charge );
        $tds = 0;
        $tds_per_rate = 0;
        if (empty($sc_details['pan_no'])) {
            $tds = ($total_sc_charge) * .20;
            $tds_tax_rate = 20;
            $tds_per_rate = "20%";
        } else {
            switch ($sc_details['company_type']) {
                case 'Proprietorship Firm':
                        $_4th_char = substr($sc_details['pan_no'], 3, 1);
                        if (strcasecmp($_4th_char, "F") == 0) {
                            $tds = ($total_sc_charge) * .02;
                            $tds_tax_rate = 2;
                            $tds_per_rate = "2%";
                        } else {
                            $tds = ($total_sc_charge) * .01;
                            $tds_tax_rate = 1;
                            $tds_per_rate = "1%";
                        }
                    
                    break;
                case "Individual":
                    $tds = ($total_sc_charge) * .01;
                    $tds_tax_rate = 1;
                    $tds_per_rate = "1%";
                    break;

                case "Partnership Firm":
                case "Company (Pvt Ltd)":
                case "Private Ltd Company":
                    $tds = ($total_sc_charge) * .02;
                    $tds_tax_rate = 2;
                    $tds_per_rate = "2%";
                    break;
                default :
                    $tds = ($total_sc_charge) * .02;
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
        //log_message('info', __FUNCTION__ . " Entering....". json_encode($_POST)); 
        $data = $this->input->post('amount_service_center');
        $due_date = $this->input->post('dowmload_excel_due_date');
        $payment_data = array();
                
        if (!empty($data)) {
            
            $sc_details = $this->get_payment_summary_csv_header();
            array_push($payment_data, $sc_details);
            $invoice_xl = array();
            foreach ($data as $key => $jdata) {
                
                
                $d = json_decode($jdata, true);
                $amount = $d['amount'];
                
                $parts_name = $d['parts_name'];
                $challan_value = $d['challan_value'];
                $explode = explode("_", $key);
                $service_center_id = $explode[0];
                $defective_parts =$explode[1];
                $defective_parts_max_age = $explode[2];
                $sc = $this->vendor_model->viewvendor($service_center_id)[0];
                $sc_details['debit_acc_no'] = '102405500277';
                $sc_details['bank_account'] = trim($sc['bank_account']);
                $sc_details['beneficiary_name'] = trim($sc['beneficiary_name']);

                $sc_details['final_amount'] = abs(sprintf("%.2f",$amount));
                if (trim($sc['bank_name']) === ICICI_BANK_NAME) {
                    $sc_details['payment_mode'] = "I";
                } else {
                    $sc_details['payment_mode'] = "N";
                }

                $sc_details['payment_date'] = date("d-M-Y");
                $sc_details['ifsc_code'] = trim($sc['ifsc_code']);
                $sc_details['payable_location_name'] = "";$sc_details['print_location'] = ""; $sc_details['bene_mobile_no'] = "";
                $sc_details['bene_email_id'] = ""; $sc_details['ben_add_1'] = "";$sc_details['ben_add_2'] = ""; $sc_details['ben_add_3'] = "";
                $sc_details['ben_add_4'] = ""; $sc_details['add_details_1'] = ""; $sc_details['add_details_2'] = "";
                $sc_details['add_details_3'] = ""; $sc_details['add_details_4'] = ""; $sc_details['add_details_5'] = "";
                
                $rm = $this->vendor_model->get_rm_sf_relation_by_sf_id($service_center_id);

                $sc_details['rm_name'] = (!empty($rm))? $rm[0]['full_name']:"";
                $sc_details['remarks'] = preg_replace("/[^A-Za-z0-9]/", "", $sc['name']);
                $sc_details['gst_no'] = $sc['gst_no'];
                $sc_details['is_signature'] = !empty($sc['signature_file']) ?"Yes":"NO";
                
                $sc_details['defective_parts'] = $defective_parts;
                $sc_details['defective_parts_max_age'] = $defective_parts_max_age;
                $sc_details['shipped_parts_name'] = $parts_name;
                $sc_details['challan_value'] = $challan_value;
                
                $oot_shipped = $this->invoices_model->get_oot_shipped_defective_parts($service_center_id);
                
                $sc_details['oot_defective_parts_shipped'] = (!empty($oot_shipped))? $oot_shipped[0]['count']:"0";
                $sc_details['oot_defective_parts_max_age'] = (!empty($oot_shipped))? $oot_shipped[0]['max_sp_age']:"0";
                $sc_details['oot_part_type'] = (!empty($oot_shipped))? $oot_shipped[0]['parts']:"";
                $sc_details['oot_challan_value'] = (!empty($oot_shipped))? $oot_shipped[0]['challan_value']:"0";
                
                $shipped_parts = $this->invoices_model->get_intransit_defective_parts($service_center_id);
                
                $sc_details['defective_parts_shipped'] = (!empty($shipped_parts))? $shipped_parts[0]['count']:"0";
                $sc_details['defective_parts_shipped_max_age'] = (!empty($shipped_parts))? $shipped_parts[0]['max_sp_age']:"0";
                $sc_details['defective_shipped_part_type'] = (!empty($shipped_parts))? $shipped_parts[0]['parts']:"0";
                $sc_details['shipped_challan_value'] = (!empty($shipped_parts))? $shipped_parts[0]['challan_value']:"0";

                $sc_details['is_verified'] = ($sc['is_verified'] ==0) ? "Not Verified" : "Verified";
                $sc_details['amount_type'] = ($amount > 0)? "CR":"DR";
                $sc_details['sf_id'] = $service_center_id;
                $sc_details['is_sf'] = $sc['is_sf'];
                $sc_details['is_cp'] = $sc['is_cp'];
                $sc_details['check_file'] = !empty($sc['cancelled_cheque_file']) ? S3_WEBSITE_URL."vendor-partner-docs/".$sc['cancelled_cheque_file'] : "";
                array_push($payment_data, $sc_details);
                
                $invoice_data = $this->get_paymnet_summary_invoice_data($service_center_id, $due_date);
                if(!empty($invoice_data)){
                    array_push($invoice_xl, json_decode(json_encode($invoice_data, true), true));
                }
            }
            $this->_download_payment_invoice_summary($invoice_xl, $payment_data);
        }
    }
    /**
     * @desc Used to download payment summary csv file and its break up
     * @param Array $invoice_xl
     * @param Array $payment_data
     */
    function _download_payment_invoice_summary($invoice_xl, $payment_data) {
        $main_invoice = array();
        foreach ($invoice_xl as $invoice) {
            foreach ($invoice as $val) {
                $main_invoice[] = $val;
            }
        }

        $template = 'payment_invoice_summary_details.xlsx';
        // directory
        $templateDir = __DIR__ . "/../excel-templates/";

        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );

        //load template
        if(ob_get_length() > 0) {
            ob_end_clean();
        }
        $R = new PHPReport($config);

        $R->load(array(
            array(
                'id' => 'invoice',
                'repeat' => true,
                'data' => $main_invoice,
            ),
                )
        );

        $output_file_excel = TMP_FOLDER . "invoice_payment_file_" . date('Ymd') . ".xlsx";

        $res1 = 0;
        if (file_exists($output_file_excel)) {

            system(" chmod 777 " . $output_file_excel, $res1);
            unlink($output_file_excel);
        }
        //Create Excel FIle
        $R->render('excel', $output_file_excel);
        system(" chmod 777 " . $output_file_excel, $res1);

        // create a file pointer connected to the output stream
        $output = fopen(TMP_FOLDER."payment_upload_summary.csv", 'w');
        //Write CSV file
        foreach ($payment_data as $line) {
            fputcsv($output, $line);
        }
        //Download zip file
        system('zip ' . TMP_FOLDER . "payment_upload_summary" . '.zip ' . $output_file_excel . ' ' . TMP_FOLDER."payment_upload_summary.csv");
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment; filename=\"payment_upload_summary.zip\"");

        $res2 = 0;
        system(" chmod 777 " . TMP_FOLDER . 'payment_upload_summary.zip ', $res2);
        readfile(TMP_FOLDER .  'payment_upload_summary.zip');
        
        //Delete All file
        exec("rm -rf " . escapeshellarg(TMP_FOLDER."payment_upload_summary.csv"));
        exec("rm -rf " . escapeshellarg($output_file_excel));
        exec("rm -rf " . escapeshellarg(TMP_FOLDER . 'payment_upload_summary.zip'));
    }
    /**
     * @desc Used to get un-settle invoice id
     * @param int $service_center_id
     * @return Object
     */
    function get_paymnet_summary_invoice_data($service_center_id, $due_date=false) {
        $select_invoice = "vendor_partner_invoices.id, vendor_partner_id, name, invoice_id, from_date, to_date, "
                . "invoice_date, CASE WHEN (amount_collected_paid > 0) THEN (amount_collected_paid - amount_paid) ELSE (amount_collected_paid + amount_paid) END as amount_due";
       
        if($due_date){
            $where_invoice['where'] = array('vendor_partner_id' => $service_center_id,
            "vendor_partner" => "vendor", "due_date <= '".$due_date."' " => NULL,
            "settle_amount" => 0);
        }
        else{
            $where_invoice['where'] = array('vendor_partner_id' => $service_center_id,
            "vendor_partner" => "vendor", "due_date <= CURRENT_DATE() " => NULL,
            "settle_amount" => 0); 
        }
        $where_invoice['length'] = -1;
        return $this->invoices_model->searchInvoicesdata($select_invoice, $where_invoice);
    }
    /**
     * @desc Used to get header of payment csv file
     * @return Array
     */
    function get_payment_summary_csv_header() {
        log_message("info", __METHOD__);
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
        $sc_details['rm_name'] = "RM Name";
        $sc_details['remarks'] = "Remarks";
        $sc_details['gst_no'] = "GST Number";
        $sc_details['is_signature'] = "Signature Exist";
        
        $sc_details['defective_parts'] = "No Of Defective Parts";
        $sc_details['defective_parts_max_age'] = "Max Age of Spare Pending";
        $sc_details['shipped_parts_name'] = "Shipped Parts Type";
        $sc_details['challan_value'] = "Defective Challan Approx Value";
        
        $sc_details['oot_defective_parts_shipped'] = "No Of OOT Shipped Part";
        $sc_details['oot_defective_parts_max_age'] = "Max Age of OOT Spare Pending";
        $sc_details['oot_part_type'] = "OOT Shipped Parts Type";
        $sc_details['oot_challan_value'] = "OOT Challan Approx Value";
        
        $sc_details['defective_parts_shipped'] = "No Of Defective Parts shipped";
        $sc_details['defective_parts_shipped_max_age'] = "Max Age of Shipped Part";
        $sc_details['defective_shipped_part_type'] = "Shipped Parts Type";
        $sc_details['shipped_challan_value'] = "Shipped Challan Approx Value";

        $sc_details['is_verified'] = "Bank Account Verified";
        $sc_details['amount_type'] = "Type";
        $sc_details['sf_id'] = "SF/CP Id";
        $sc_details['is_sf'] = "SF";
        $sc_details['is_cp'] = "CP";
        $sc_details['check_file'] = "Check File";

        return $sc_details;
    }

    /**
     * @desc: This method is used to fetch invoice id. It called by Ajax while 
     * invoice invoice details.
     * @param int $vendor_partner_id
     * @param String $vendor_partner_type
     * @param String $type_code
     * @param String $type
     */
    function fetch_invoice_id($vendor_partner_id, $vendor_partner_type, $type_code, $type) {
        $entity_details = array();

        if (!empty($vendor_partner_id) && !empty($type_code) && !empty($type)) {
            switch ($type_code) {

                case 'A':
                    if($type == "DebitNote"){
                        echo $this->create_invoice_id_to_insert("ARD-DN");
                    } else {
                        echo $this->create_invoice_id_to_insert("Around");
                    }

                    break;

                case 'B':
                    if($type == "CreditNote"){
                        echo $this->create_invoice_id_to_insert("ARD-CN");
                    }
                    else if ($vendor_partner_type == "vendor") {
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
    function generate_crm_setup($isCron = false) { 
        if($isCron == true){
            
        }
        else{
            $this->checkUserSession();
        }
        log_message('info', __FUNCTION__ . " Entering....");
        $this->form_validation->set_rules('partner_name', 'Partner Name', 'trim');
        $this->form_validation->set_rules('partner_id', 'Partner ID', 'required|trim');
        $this->form_validation->set_rules('daterange', 'Start Date', 'required|trim');
        $this->form_validation->set_rules('invoice_type', 'Invoice Type', 'required|trim');
        $this->form_validation->set_rules('service_charge', 'Service Charge', 'required|trim');
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
            $invoice_tag = ANNUAL_CHARGE_INVOICE_TAGGING;
           
            if($description == QC_INVOICE_DESCRIPTION){
                $hsn_code = QC_HSN_CODE;
                $type = "Buyback";
                $email_tag = SWEETENER_INVOCIE_EMAIL_TAG;
                $invoice_tag = PARTNER_SWEETENER_CHARGE_INVOICE_TAGGING;
  
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
                'invoice_tagged' => $invoice_tag,
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
                "invoice_file_pdf" => $response['meta']['copy_file'],
                "vertical" => SERVICE,
                "category" => RECURRING_CHARGES,
                "sub_category" => CRM,
                "accounting" => 1
            );
            
             $this->invoices_model->insert_new_invoice($invoice_details);
             log_message('info', __METHOD__ . ": Invoice ID inserted");
             $this->session->set_flashdata('file_error', $description.' Invoice Generated');
             redirect(base_url() . "employee/invoice/invoice_partner_view");

        } else {
            log_message('info', __METHOD__ . ": Invoice ID inserted");
            $this->session->set_flashdata('file_error', $description. ' Not Generated');
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
        $status = $this->_process_advance_payment($agent_id, null);
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
        $status = $this->_process_advance_payment($agent_id, PAYTM_GATEWAY);
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
    
    function _process_advance_payment($agent_id, $flag = null){
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
                    $amount, $data['tds_amount'], "Credit", $agent_id, $flag);
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
        if($this->input->post('payment_txn_id')){
            $data['payment_txn_id'] = $this->input->post('payment_txn_id');
        }
        
        return $this->invoices_model->bankAccountTransaction($data);
    }
    
    function advance_invoice_insert($vendor_partner, $vendor_partner_id, $date, $amount, $tds, $txntype, $agent_id, $flag=null) { 

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
                $advance_type = $this->input->post('advance_type');
                switch ($advance_type){
                    case BUYBACKTYPE:
                        $data['type'] = BUYBACK_VOUCHER;
                        $data['vertical'] = BUYBACK_VERTICAL;
                        $data['category'] = EXCHANGE;
                        $data['sub_category'] = ADVANCE;
                        $data['accounting'] = 0;
                        break;
                    
                    case MICRO_WAREHOUSE_CHARGES_TYPE:
                        $data['type'] = VENDOR_VOUCHER;
                        $data['vertical'] =SERVICE;
                        $data['third_party_entity'] = _247AROUND_PARTNER_STRING;
                        $data['third_party_entity_id'] = $this->input->post('third_party');
                        $data['category'] = MICROWAREHOUSE;
                        $data['sub_category'] = SECURITY;
                        $data['accounting'] = 0;
                        break;
                    
                    case SECURITY:
                        $data['type'] = VENDOR_VOUCHER;
                        $data['vertical'] =SERVICE;
                        $data['category'] = ADVANCE;
                        $data['sub_category'] = SECURITY;
                        $data['accounting'] = 0;
                        break;
                    default :
                        $data['type'] = VENDOR_VOUCHER;
                        $data['vertical'] =SERVICE;
                        $data['category'] = ADVANCE;
                        $data['sub_category'] = SECURITY;
                        $data['accounting'] = 0;
                        break;
                }
                if($txntype == "Credit"){
                    $data['invoice_id'] = $this->create_invoice_id_to_insert("ARD-RV");
                    $basic_price = $amount;
                    $data['parts_cost'] = $basic_price;
                    $amount_collected_paid = $amount;
                    $data['type_code'] = "B";
                    $data['amount_collected_paid'] = -$amount_collected_paid;
                } else {
                    $data['invoice_id'] = $this->create_invoice_id_to_insert($entity[0]['sc_code']."-RV");
                    $basic_price = $amount;
                    $amount_collected_paid = $amount;
                    $data['total_service_charge'] = $basic_price;
                    $data['type_code'] = "A";
                    $data['amount_collected_paid'] = $amount_collected_paid;
                    $data['vertical'] =SERVICE;
                    $data['category'] = ADVANCE;
                    $data['sub_category'] = CASH;
                    $data['accounting'] = 0;
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
                
                if($flag && $flag == PAYTM_GATEWAY){
                    $data['vertical'] =SERVICE;
                    $data['category'] = ADVANCE;
                    $data['sub_category'] = PRE_PAID_PAYMENT_GATEWAY;
                    $data['accounting'] = 0;
                }
                else{
                    $data['vertical'] =SERVICE;
                    $data['category'] = ADVANCE;
                    $data['sub_category'] = PREPAID;
                    $data['accounting'] = 0;
                }
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
        $tax_charge = $this->booking_model->get_calculated_tax_charge($amount, $gst_rate);
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
        $data[0]['gst_rate'] = $gst_rate;
        
        $response = $this->invoices_model->_set_partner_excel_invoice_data($data, $sd, $ed, $invoice_type,$invoice_date);
        log_message("info", __METHOD__." Partner Advance Excel Data generated ".$invoice_id);
        $response['meta']['invoice_id'] = $invoice_id;
        if($invoice_type == "Receipt Voucher"){
            $response['meta']['sd'] = "";
            $response['meta']['ed'] = "";
        }
       
        $status = $this->invoice_lib->send_request_to_create_main_excel($response, "final");
        if($status){
            log_message("info", __METHOD__." Partner Advance Excel generated ".$invoice_id);
            
            //$convert = $this->invoice_lib->send_request_to_convert_excel_to_pdf($invoice_id, "final");
            $convert = $this->invoice_lib->convert_invoice_file_into_pdf($response, "final");
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
                if($email_tag == CRM_SETUP_INVOICE_EMAIL_TAG || $email_tag == CRM_SETUP_PROFORMA_INVOICE_EMAIL_TAG){
                    $to = $partner_data['invoice_email_to'].",".$email_template[1];
                    $cc = $partner_data['invoice_email_cc'].",".$email_template[3];
                }
                else{
                    $to = $email_template[1];
                    $cc = $email_template[3];
                }
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
        if(ob_get_length() > 0) {
            ob_end_clean();
        }
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
        $req['select'] = "spare_parts_details.requested_inventory_id, spare_parts_details.shipped_inventory_id, spare_parts_details.parts_requested_type,spare_parts_details.shipped_parts_type, spare_parts_details.purchase_price, spare_parts_details.sell_invoice_id, parts_requested,invoice_gst_rate, spare_parts_details.service_center_id, spare_parts_details.booking_id, booking_details.service_id";
        $sp_data = $this->inventory_model->get_spare_parts_query($req);
        if (!empty($sp_data) && empty($sp_data[0]->sell_invoice_id) && ($sp_data[0]->purchase_price > 0)) {
            $vendor_details = $this->vendor_model->getVendorDetails("gst_no, "
                    . "company_name,address as company_address,district,"
                    . "state, pincode, owner_email, primary_contact_email", array('id' => $sp_data[0]->service_center_id));
            
            $ptype = !(empty($sp_data[0]->shipped_parts_type))?$sp_data[0]->shipped_parts_type:$sp_data[0]->parts_requested_type;
            
            $inventory_id = "";
            if(!empty($sp_data[0]->shipped_inventory_id)){
                
                $inventory_id = $sp_data[0]->shipped_inventory_id;
                
            } else if($sp_data[0]->requested_inventory_id){
                
                $inventory_id = $sp_data[0]->requested_inventory_id;
            }
            
            $margin = $this->inventory_model->get_oow_margin($inventory_id, array('part_type' => $ptype,
                    'service_id' => $sp_data[0]->service_id));
               
            $repair_around_oow_percentage = $margin['oow_vendor_margin']/100;
            
            $data = array();
            $data[0]['description'] = ucwords($sp_data[0]->parts_requested) . " (" . $sp_data[0]->booking_id . ") ";
            $amount = $sp_data[0]->purchase_price + $sp_data[0]->purchase_price * $repair_around_oow_percentage;
            $tax_charge = $this->booking_model->get_calculated_tax_charge($amount, $sp_data[0]->invoice_gst_rate);
            $data[0]['taxable_value'] = sprintf("%.2f", ($amount - $tax_charge));
            $data[0]['product_or_services'] = "Product";
            if(!empty($vendor_details[0]['gst_no'])){
                $data[0]['gst_number'] = $vendor_details[0]['gst_no'];
            } else {
                $data[0]['gst_number'] = 1;
            }
            
            $data[0]['company_name'] = $vendor_details[0]['company_name'];
            $data[0]['company_address'] = $vendor_details[0]['company_address'];
            $data[0]['district'] = $vendor_details[0]['district'];
            $data[0]['pincode'] = $vendor_details[0]['pincode'];
            $data[0]['state'] = $vendor_details[0]['state'];
            $data[0]['rate'] = "0";
            $data[0]['qty'] = 1;
            $data[0]['hsn_code'] = SPARE_HSN_CODE;
            $sd = $ed = $invoice_date = date("Y-m-d");
            $gst_rate = $sp_data[0]->invoice_gst_rate;
            $data[0]['gst_rate'] = $gst_rate;

            $response = $this->invoices_model->_set_partner_excel_invoice_data($data, $sd, $ed, "Tax Invoice",$invoice_date);
            $response['meta']['invoice_id'] = $this->create_invoice_id_to_insert("Around");
            $status = $this->invoice_lib->send_request_to_create_main_excel($response, "final");
            if ($status) {
                log_message("info", __METHOD__ . " Vendor Spare Invoice SF ID" . $sp_data[0]->service_center_id . " Spare Id " . $spare_id);

                $convert = $this->invoice_lib->convert_invoice_file_into_pdf($response, "final");
                $output_pdf_file_name = $convert['main_pdf_file_name'];
                $response['meta']['invoice_file_main'] = $output_pdf_file_name;
                $response['meta']['copy_file'] = $convert['copy_file'];

                $email_template = $this->booking_model->get_booking_email_template(SPARE_INVOICE_EMAIL_TAG);
                $subject = vsprintf($email_template[4], array($vendor_details[0]['company_name'], $sp_data[0]->booking_id));
                $message = $email_template[0];
                $email_from = $email_template[2];

                $rm_details = $this->vendor_model->get_rm_sf_relation_by_sf_id($sp_data[0]->service_center_id);
                $rem_email_id = "";
                if (!empty($rm_details)) {
                    $rem_email_id = ", " . $rm_details[0]['official_email'];
                }
                $to = $vendor_details[0]['owner_email'] . ", " . $vendor_details[0]['primary_contact_email'];
//                $to = $email_template[3];
                $cc = $email_template[3];;

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
                    'type' => "Parts",
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
                    "remarks" => $data[0]['description'],
                    "vertical" => SERVICE,
                    "category" => SPARES,
                    "sub_category" => OUT_OF_WARRANTY,
                    "accounting" => 1
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
    /**
     * @desc This function is used to generate reverse invoice for out of warranty booking
     * It will generate for both party(SF/Partner)
     * @param String $spare_id
     */
    function generate_reverse_oow_invoice($spare_id){
        log_message('info', __METHOD__. " Spare ID ".$spare_id);
        $oow_data = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id, booking_unit_details_id, purchase_price, sell_price, sell_invoice_id, purchase_invoice_id, "
                . "spare_parts_details.purchase_price, parts_requested,invoice_gst_rate, spare_parts_details.service_center_id, spare_parts_details.booking_id,"
                . "reverse_sale_invoice_id, reverse_purchase_invoice_id, booking_details.partner_id as booking_partner_id, invoice_gst_rate", 
                    array('spare_parts_details.id' => $spare_id, 
                        'booking_unit_details_id IS NOT NULL' => NULL,
                        'sell_price > 0 ' => NULL,
                        'sell_invoice_id IS NOT NULL' => NULL,
                        'estimate_cost_given_date IS NOT NULL' => NULL,
                        'spare_parts_details.part_warranty_status' => 2,
                        'defective_part_required' => 1,
                        'approved_defective_parts_by_partner' => 1,
                        'status' => DEFECTIVE_PARTS_RECEIVED,
                        '(reverse_sale_invoice_id IS NULL OR reverse_purchase_invoice_id IS NULL)' => NULL),
                    true);

        if(!empty($oow_data)){
            foreach ($oow_data as $value) {
                if(!empty($value['sell_invoice_id']) && empty($value['reverse_sale_invoice_id'])){
                   $invoice_details = $this->invoices_model->get_invoices_details(array('invoice_id' => $value['sell_invoice_id']), $select = "*");
                   if(!empty($invoice_details)){
                       $this->generate_reverse_sale_invoice($invoice_details, $value);
                   }
                }

                if(!empty($value['purchase_invoice_id']) && empty($value['reverse_purchase_invoice_id'])){
                    $this->generate_reverse_purchase_invoice($value);
                }
            }
        }
    }
    /**
     * @desc This function is used to generate reverse sale invoice means purchase invoice from SF
     * @param Array $invoice_details
     * @param Array $spare_data
     */
    function generate_reverse_sale_invoice($invoice_details, $spare_data){
        log_message('info', __METHOD__. " invoice data ". print_r($invoice_details, true). " Spare Data ". print_r($spare_data, TRUE));
        $vendor_details = $this->vendor_model->getVendorDetails("gst_no, "
                    . "company_name,address as company_address,district,"
                    . "state, pincode, owner_email, primary_contact_email, sc_code, owner_phone_1", array('id' => $invoice_details[0]['vendor_partner_id']));
        $data = array();
        $data[0]['description'] = ucwords($spare_data['parts_requested']) . " (" . $spare_data['booking_id'] . ") ";
        $data[0]['taxable_value'] = $invoice_details[0]['parts_cost'];
        $data[0]['product_or_services'] = "Product";
        if(!empty($vendor_details[0]['gst_no'])){
            $data[0]['gst_number'] = $vendor_details[0]['gst_no'];
        } else {
            $data[0]['gst_number'] = 1;
        }
        
        $data[0]['company_name'] = $vendor_details[0]['company_name'];
        $data[0]['company_address'] = $vendor_details[0]['company_address'];
        $data[0]['district'] = $vendor_details[0]['district'];
        $data[0]['pincode'] = $vendor_details[0]['pincode'];
        $data[0]['state'] = $vendor_details[0]['state'];
        $data[0]['owner_phone_1'] = $vendor_details[0]['owner_phone_1'];
        $data[0]['rate'] = "0";
        $data[0]['qty'] = 1;
        $data[0]['hsn_code'] = SPARE_HSN_CODE;
        $sd = $ed = $invoice_date = date("Y-m-d");
        $gst_rate = ($invoice_details[0]['cgst_tax_rate'] + $invoice_details[0]['sgst_tax_rate'] + $invoice_details[0]['igst_tax_rate']);
        $data[0]['gst_rate'] = $gst_rate;
        $vendor_details[0]['service_center_id'] = $invoice_details[0]['vendor_partner_id'];
        $vendor_details[0]['booking_id'] = $invoice_details[0]['booking_id'];
        
        $array = array();
        $array[0]['service_center_id'] = $invoice_details[0]['vendor_partner_id'];
        $array[0]['id'] = $spare_data['id'];
        $array[0]['company_name'] = $vendor_details[0]['company_name'];
        
        $invoice_id = $this->create_invoice_id_to_insert($vendor_details[0]['sc_code']);
        $a = $this->_reverse_sale_invoice($invoice_id, $data, $sd, $ed, $invoice_date, $array);
        if($a){
             return true;
        } else {
            
            log_message('info', __METHOD__ . " File is not generated " );
            return false;
        }
    }
    /**
     * @desc This function is used to generate Micro Spare purchase invoice  
     * @param int $spare_id
     */
    function generate_micro_reverse_sale_invoice($spare_id) {
        log_message('info', __METHOD__ . " Spare ID " . $spare_id);

        if (!empty($spare_id)) {
            $spare = $this->partner_model->get_spare_parts_by_any("spare_parts_details.*, booking_details.partner_id as booking_partner_id, service_centres.gst_no as gst_number,service_centres.sc_code,"
                    . "service_centres.state,service_centres.address as company_address,service_centres.company_name,"
                    . "service_centres.district, service_centres.pincode, service_centres.is_wh, spare_parts_details.is_micro_wh,owner_phone_1 ", array('spare_parts_details.id' => $spare_id), TRUE, TRUE);
            if (!empty($spare)) {
                if ($spare[0]['is_micro_wh'] == 1) {
                        if (!empty($spare[0]['shipped_inventory_id'])) {
                            if (empty($spare[0]['gst_number'])) {
                                $spare[0]['gst_number'] = TRUE;
                            }
                            $invoice_id = $invoice_id = $this->invoice_lib->create_invoice_id($spare[0]['sc_code']);
                            $spare[0]['spare_id'] = $spare_id;
                            $spare[0]['inventory_id'] = $spare[0]['shipped_inventory_id'];
                            $spare[0]['booking_partner_id'] = $spare[0]['booking_partner_id'];
                            $unsettle = $this->invoice_lib->settle_inventory_invoice_annexure($spare, $invoice_id);
                            if (!empty($unsettle['processData'])) {
                                $data = array();
                                $data[0]['description'] = ucwords($unsettle['processData'][0]['part_name']) . " (" . $spare[0]['booking_id'] . ") ";
                                $data[0]['taxable_value'] = $unsettle['processData'][0]['rate'];
                                $data[0]['product_or_services'] = "Product";
                                $data[0]['gst_number'] = $spare[0]['gst_number'];
                                $data[0]['invoice_id'] = $invoice_id;
                                $data[0]['spare_id'] = $spare_id;
                                $data[0]['inventory_id'] = $spare[0]['inventory_id'];
                                $data[0]['company_name'] = $spare[0]['company_name'];
                                $data[0]['owner_phone_1'] = $spare[0]['owner_phone_1'];
                                $data[0]['company_address'] = $spare[0]['company_address'];
                                $data[0]['district'] = $spare[0]['district'];
                                $data[0]['pincode'] = $spare[0]['pincode'];
                                $data[0]['state'] = $spare[0]['state'];
                                $data[0]['rate'] = $unsettle['processData'][0]['rate'];
                                $data[0]['qty'] = 1;
                                $data[0]['hsn_code'] = $unsettle['processData'][0]['rate'];
                                $sd = $ed = $invoice_date = date("Y-m-d");
                                $data[0]['gst_rate'] = $unsettle['processData'][0]['gst_rate'];

                                $a = $this->_reverse_sale_invoice($invoice_id, $data, $sd, $ed, $invoice_date, $spare);
                                if ($a) {
                                    
                                } else {
                                    log_message('info', __METHOD__ . " File is not genereated " . $spare_id);
                                }
                            }
                        } else {
                            log_message('info', __METHOD__ . " Shipped inventory Id is empty " . $spare_id);
                        }
                    
                } else {
                    log_message('info', __METHOD__ . " Partner ID ans sf id is not same dor spare id " . $spare_id);
                }
            } else {
                log_message('info', __METHOD__ . " Spare ID is not exit " . $spare_id);
            }
        } else {
            log_message('info', __METHOD__ . " Empty Spare ");
        }
    }
    /**
     * @desc This function is used to insert sale invoice and mail with invoice file
     * @param String $invoice_id
     * @param Array $data
     * @param date $sd
     * @param date $ed
     * @param date $invoice_date
     * @param Array $spare
     * @return boolean
     */           
    function _reverse_sale_invoice($invoice_id, $data, $sd, $ed, $invoice_date, $spare){
        $response = $this->invoices_model->_set_partner_excel_invoice_data($data, $sd, $ed, "Tax Invoice", $invoice_date);
        $response['meta']['invoice_id'] = $invoice_id;
        $c_s_gst = $this->invoices_model->check_gst_tax_type($spare[0]['state']);
        if ($c_s_gst) {
            $response['meta']['invoice_template'] = "SF_FOC_Tax_Invoice-Intra_State-v1.xlsx";
        } else {
            $response['meta']['invoice_template'] = "SF_FOC_Tax_Invoice_Inter_State_v1.xlsx";
        }
        $status = $this->invoice_lib->send_request_to_create_main_excel($response, "final");
        if ($status) {
            log_message("info", __METHOD__ . " Vendor Spare Invoice SF ID" . $spare[0]['service_center_id']);

            $convert = $this->invoice_lib->convert_invoice_file_into_pdf($response, "final");
            $output_pdf_file_name = $convert['main_pdf_file_name'];
            $response['meta']['invoice_file_main'] = $output_pdf_file_name;
            $response['meta']['copy_file'] = $convert['copy_file'];

            $email_template = $this->booking_model->get_booking_email_template(SPARE_INVOICE_EMAIL_TAG);
            $subject = vsprintf($email_template[4], array($spare[0]['company_name'], $spare[0]['booking_id']));
            $message = $email_template[0];
            $email_from = $email_template[2];

            $to = $email_template[3];
            $cc = "";

            $this->upload_invoice_to_S3($response['meta']['invoice_id'], false);

            $cmd = "curl https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/invoices-excel/" . $output_pdf_file_name . " -o " . TMP_FOLDER . $output_pdf_file_name;
            exec($cmd);
            $this->send_email_with_invoice($email_from, $to, $cc, $message, $subject, TMP_FOLDER . $output_pdf_file_name, "", SPARE_INVOICE_EMAIL_TAG);

            unlink(TMP_FOLDER . $response['meta']['invoice_id'] . ".xlsx");
            unlink(TMP_FOLDER . $output_pdf_file_name);
            unlink(TMP_FOLDER . "copy_" . $response['meta']['invoice_id'] . ".xlsx");

            $invoice_details = array(
                'invoice_id' => $response['meta']['invoice_id'],
                'type_code' => 'B',
                'type' => "Parts",
                'vendor_partner' => 'vendor',
                'vendor_partner_id' => $spare[0]['service_center_id'],
                'invoice_file_main' => $response['meta']['invoice_file_main'],
                'invoice_file_excel' => $response['meta']['invoice_id'] . ".xlsx",
                'from_date' => date("Y-m-d", strtotime($sd)), //??? Check this next time, format should be YYYY-MM-DD
                'to_date' => date("Y-m-d", strtotime($ed)),
                'parts_cost' => $response['meta']['total_taxable_value'],
                'parts_count' => 1,
                'total_amount_collected' => $response['meta']['sub_total_amount'],
                'invoice_date' => date("Y-m-d"),
                'around_royalty' => 0,
                'due_date' => date("Y-m-d"),
                //Amount needs to be collected from Vendor
                'amount_collected_paid' => -$response['meta']['sub_total_amount'],
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
                "remarks" => $data[0]['description'],
                "vertical" => SERVICE,
                "category" => SPARES,
                "sub_category" => DEFECTIVE_RETURN,
                "accounting" => 1
            );

            $this->invoices_model->insert_new_invoice($invoice_details);
            log_message('info', __METHOD__ . ": Invoice ID inserted");

            $this->service_centers_model->update_spare_parts(array('id' => $spare[0]['id']), array("reverse_sale_invoice_id" => $response['meta']['invoice_id']));
            log_message('info', __METHOD__ . ": Invoice Updated in Spare Parts " . $response['meta']['invoice_id']);

            $this->invoice_lib->insert_def_invoice_breakup($response, 1);
        } else {
            return false;
        }
    }
    /**
     * @desc This function is used create Micro invoice, sale to Partner 
     * @param String $spare_id
     */
    function generate_reverse_micro_purchase_invoice($spare_id){
        log_message('info', __METHOD__ . " Spare ID " . $spare_id);
        //$array = $this->input->post('spare_id');
       // foreach ($array as $value) {

            $spare = $this->partner_model->get_spare_parts_by_any("booking_details.partner_id AS booking_partner_id, "
                    . "spare_parts_details.partner_id,spare_parts_details.shipped_inventory_id, "
                    . "spare_parts_details.shipped_inventory_id as inventory_id, service_center_id,"
                    . "spare_parts_details.is_micro_wh, spare_parts_details.booking_id,"
                    . "spare_parts_details.id", array('spare_parts_details.id' => $spare_id ), TRUE, FALSE);
            
           
            if(!empty($spare)){
                $partner_details = $this->partner_model->getpartner($spare[0]['booking_partner_id']);
                if(!empty($partner_details)){
                    if ($spare[0]['is_micro_wh'] == 1 && empty($spare[0]['reverse_purchase_invoice_id'])) { 
                        if (!empty($spare[0]['shipped_inventory_id'])) {
                            if (empty($spare[0]['gst_number'])) {
                                $spare[0]['gst_number'] = TRUE;
                            }
                            $invoice_id = $invoice_id = $this->invoice_lib->create_invoice_id("Around");
                            $spare[0]['spare_id'] = $spare_id;
                            $spare[0]['inventory_id'] = $spare[0]['shipped_inventory_id'];
                            $spare[0]['booking_partner_id'] = $spare[0]['partner_id'];
                            $unsettle = $this->invoice_lib->settle_inventory_invoice_annexure($spare, $invoice_id);
                            if (!empty($unsettle['processData'])) {
                                $data = array();
                                $data[0]['description'] = ucwords($unsettle['processData'][0]['part_name']) . " (" . $spare[0]['booking_id'] . ") ";
                                $data[0]['taxable_value'] = $unsettle['processData'][0]['rate'];
                                $data[0]['product_or_services'] = "Product";
                                $data[0]['gst_number'] = $partner_details[0]['gst_number'];
                                $data[0]['invoice_id'] = $invoice_id;
                                $data[0]['spare_id'] = $spare_id;
                                $data[0]['inventory_id'] = $spare[0]['shipped_inventory_id'];
                                $data[0]['company_name'] = $partner_details[0]['company_name'];
                                $data[0]['company_address'] = $partner_details[0]['address'];
                                $data[0]['district'] = $partner_details[0]['district'];
                                $data[0]['pincode'] = $partner_details[0]['pincode'];
                                $data[0]['state'] = $partner_details[0]['state'];
                                $data[0]['rate'] = $unsettle['processData'][0]['rate'];
                                $data[0]['qty'] = 1;
                                $data[0]['hsn_code'] = $unsettle['processData'][0]['hsn_code'];
                                $sd = $ed = $invoice_date = date("Y-m-d");
                                $data[0]['gst_rate'] = $unsettle['processData'][0]['gst_rate'];

                                $a = $this->_reverse_purchase_invoice($invoice_id, $data, $sd, $ed, $invoice_date, $partner_details, $spare[0]);
                                if ($a) {
                                    
                                } else {
                                    log_message('info', __METHOD__ . " File is not genereated " . $spare_id);
                                }
                            }
                        }
                    }
                }
            }
        
    }

    /**
     * @desc This function is used to generate reverse purchase invoice means sale invoice to Partner 
     * @param Array $spare_data
     */
    function generate_reverse_purchase_invoice($spare_data){
        log_message('info', __METHOD__. " Spare Data ". print_r($spare_data, true));
        
        $invoice_breakup_details = $this->invoices_model->get_breakup_invoice_details("*", array('spare_id' => $spare_data['id'], "invoice_id" => $spare_data['purchase_invoice_id']));
        
        if(!empty($invoice_breakup_details)){
            $data = array();
            $data[0]['description'] = $invoice_breakup_details[0]['description'];
            $data[0]['vendor_basic_charges'] = 0;
            $data[0]['vendor_tax'] = 0;
            $data[0]['product_or_services'] = "Product";
            $gst_rate = $invoice_breakup_details[0]['cgst_tax_rate'] + $invoice_breakup_details[0]['sgst_tax_rate'] + $invoice_breakup_details[0]['igst_tax_rate'];
            $data[0]['tax_rate'] = $gst_rate;
            $data[0]['partner_charge'] = $invoice_breakup_details[0]['taxable_value'];
            $data[0]['booking_id'] = $spare_data['booking_id'];
            $data[0]['approval_file'] = '';
            $data[0]['create_date'] = date('Y-m-d H:i:s');
            
            $a = $this->booking_model->insert_misc_charges_in_batch($data);

            if(!empty($a)){
                log_message('info', __METHOD__. " Misc Charges Added". $spare_data['booking_id'] );
                
            } else {
                log_message('info', __METHOD__. " Misc Charges Not Added ". $spare_data['booking_id']);
            }
            
        }

    }
    
    function _reverse_purchase_invoice($invoice_id, $data, $sd, $ed, $invoice_date, $partner_details, $spare_data){
        log_message('info', __METHOD__);
        $response = $this->invoices_model->_set_partner_excel_invoice_data($data, $sd, $ed, "Tax Invoice",$invoice_date);
        
            $response['meta']['invoice_id'] = $invoice_id;
            
            $status = $this->invoice_lib->send_request_to_create_main_excel($response, "final");
            
            if ($status) {
                log_message("info", __METHOD__ . " Vendor Spare Invoice SF ID" . $spare_data['service_center_id'] . " Spare Id " . $spare_data['id']);

                $convert = $this->invoice_lib->convert_invoice_file_into_pdf($response, "final");
                $output_pdf_file_name = $convert['main_pdf_file_name'];
                $response['meta']['invoice_file_main'] = $output_pdf_file_name;
                $response['meta']['copy_file'] = $convert['copy_file'];

                $email_template = $this->booking_model->get_booking_email_template(SPARE_INVOICE_EMAIL_TAG);
                $subject = vsprintf($email_template[4], array($partner_details[0]['company_name'], $spare_data['booking_id']));
                $message = $email_template[0];
                $email_from = $email_template[2];

                $to = $partner_details[0]['invoice_email_to'];
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
                    'type' => "Parts",
                    'vendor_partner' => 'partner',
                    'vendor_partner_id' => $partner_details[0]['id'],
                    'invoice_file_main' => $response['meta']['invoice_file_main'],
                    'invoice_file_excel' => $response['meta']['invoice_id'] . ".xlsx",
                    'from_date' => date("Y-m-d", strtotime($sd)), //??? Check this next time, format should be YYYY-MM-DD
                    'to_date' => date("Y-m-d", strtotime($ed)),
                    'parts_cost' => $response['meta']['total_taxable_value'],
                    'parts_count' => 1,
                    'total_amount_collected' => $response['meta']['sub_total_amount'],
                    'invoice_date' => date("Y-m-d"),
                    'around_royalty' => 0,
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
                    "remarks" => $data[0]['description'],
                    "vertical" => SERVICE,
                    "category" => SPARES,
                    "sub_category" => DEFECTIVE_RETURN,
                    "accounting" => 1
                );

                $this->invoices_model->insert_new_invoice($invoice_details);
                log_message('info', __METHOD__ . ": Invoice ID inserted");
                
                $this->invoice_lib->insert_def_invoice_breakup($response, 1);

                $this->service_centers_model->update_spare_parts(array('id' => $spare_data['id']), array("reverse_purchase_invoice_id" => $response['meta']['invoice_id']));
                log_message('info', __METHOD__ . ": Invoice Updated in Spare Parts " . $response['meta']['invoice_id']);
                
                log_message('info', __METHOD__ . ": ...Exit" . $response['meta']['invoice_id']);
                
                return true;
            } else {
                return false;
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
        log_message("info", __METHOD__ . " Post " . print_r(json_encode($this->input->post(), true), true));

        $this->form_validation->set_rules('part', 'part', 'required');
        $this->form_validation->set_rules('invoice_date', 'Invoice Date', 'required');
        $this->form_validation->set_rules('remarks', 'Remarks', 'required');
        $this->form_validation->set_rules('invoice_id', 'Invoice ID', 'required');
        $validation = $this->form_validation->run();
        if ($validation) {
            $part_data = $this->input->post("part");
            $is_validate = $this->validate_spare_purchase_data($part_data);
            if($is_validate['status']){
                $w['length'] = -1;
                $w['where_in'] = array("spare_parts_details.id" => $is_validate['data']);
                $w['select'] = "spare_parts_details.id, spare_parts_details.booking_id, purchase_price, public_name, booking_details.partner_id, "
                        . "purchase_invoice_id,sell_invoice_id, sell_price, incoming_invoice_pdf, partners.state, parts_shipped";
                $data = $this->inventory_model->get_spare_parts_query($w);
                
                $unique_partner = array_unique(array_map(function ($k) {
                        return $k->partner_id;
                    }, $data));
                    
                if (count($unique_partner) == 1) {
                    $invoice_pdf = "";
                    foreach ($data as $sp) {
                        if (!empty($sp->incoming_invoice_pdf)) {
                            $invoice_pdf = $sp->incoming_invoice_pdf;
                        }
                    }
                    if (!empty($invoice_pdf)) {
                        $tmp_invoice_id = $this->input->post('invoice_id');
                        $invoice_id = str_replace("/","-",$tmp_invoice_id);
                        $partner_id = $this->input->post('partner_id');
                        $invoice_date = $this->input->post('invoice_date');
                        $invoice_breakup = array();
                        
                        $c_s_gst = $this->invoices_model->check_gst_tax_type($data[0]->state);
                        $uni_booking_id = array_unique(array_map(function ($k) {
                            return $k->booking_id;
                        }, $data));
                            
                        $invoice = array();
                        $booking_id_array = array();
                        $total_amount_collected = 0;
                        $total_part_basic = 0;
                        $total_cgst_amount = $total_igst_ampount =0;
                        
                        foreach ($data as $value) {
                            array_push($booking_id_array, $value->booking_id);
                            $igst_rate = $cgst_rate = $sgst_rate = 0;
                            $igst_amount = $cgst_amount = $sgst_amount = 0;
                            if($c_s_gst){
                                $cgst_rate = $sgst_rate = $part_data[$value->id]['gst_rate']/2;
                                $cgst_amount = $sgst_amount = (($part_data[$value->id]['basic_amount'] * $part_data[$value->id]['gst_rate'])/100)/2;
                                $total_cgst_amount += $cgst_amount;
                            } else {
                                $igst_rate = $part_data[$value->id]['gst_rate'];
                                $igst_amount = (($part_data[$value->id]['basic_amount'] * $part_data[$value->id]['gst_rate'])/100);
                                $total_igst_ampount += $igst_amount;
                            }
                            $total_amount = $part_data[$value->id]['basic_amount'] + $igst_amount +$cgst_amount + $sgst_amount;
                            $total_amount_collected += $total_amount;
                            $total_part_basic += $part_data[$value->id]['basic_amount'];
                            $invoice_details = array(
                                "invoice_id" => $invoice_id,
                                "description" => $value->parts_shipped,
                                "qty" => 1,
                                "product_or_services" => "Parts",
                                "rate" => $part_data[$value->id]['basic_amount'],
                                "taxable_value" => $part_data[$value->id]['basic_amount'],
                                "cgst_tax_rate" => $cgst_rate,
                                "sgst_tax_rate" => $sgst_rate,
                                "igst_tax_rate" => $igst_rate,
                                "cgst_tax_amount" => $cgst_amount,
                                "sgst_tax_amount" => $sgst_amount,
                                "spare_id" => $value->id,
                                "igst_tax_amount" => $igst_amount,
                                "hsn_code" => $part_data[$value->id]['hsn_code'],
                                "total_amount" => $total_amount,
                                "create_date" => date('Y-m-d H:i:s')

                            );
            
                            array_push($invoice_breakup, $invoice_details);
                        }
                        
                        $invoice['invoice_id'] = $invoice_id;
                        $invoice['vendor_partner'] = "partner";
                        $invoice['remarks'] = trim($this->input->post("remarks")) . " for Booking id " . implode(", ", $uni_booking_id);
                        $invoice['parts_count'] = count($part_data);
                        $invoice['hsn_code'] = '';
                        $invoice['total_amount_collected'] = $total_amount_collected;

                        $invoice['type'] = "Parts";
                        $invoice['invoice_date'] = $invoice['due_date'] = date("Y-m-d", strtotime($invoice_date));
                        $invoice['from_date'] = $invoice['to_date'] = date("Y-m-d", strtotime($invoice_date));
                        $invoice['type_code'] = "B";
                        $invoice['agent_id'] = $this->session->userdata('id');
                        $invoice['vendor_partner_id'] =$partner_id;
                        $invoice['parts_cost'] = $total_part_basic;
                        $invoice['cgst_tax_amount'] = $invoice['sgst_tax_amount'] =  $total_cgst_amount;
                        $invoice['igst_tax_amount'] = $total_igst_ampount;
                    
                        $invoice['invoice_file_main'] = $invoice_pdf;
                        $invoice['amount_collected_paid'] = -$total_amount_collected;
                        $invoice['vertical'] = SERVICE;
                        $invoice['category'] = SPARES;
                        $invoice['sub_category'] = OUT_OF_WARRANTY;
                        $invoice['accounting'] = 1;

                        $this->invoices_model->action_partner_invoice($invoice);
                        $this->invoices_model->insert_invoice_breakup($invoice_breakup);
                        foreach ($data as $value ) {

                            $this->service_centers_model->update_spare_parts(array('id' => $value->id), array("purchase_invoice_id" => $invoice['invoice_id'],
                                "status" => SPARE_SHIPPED_BY_PARTNER, 'invoice_gst_rate' => $part_data[$value->id]['gst_rate']));
                            
                            $this->vendor_model->update_service_center_action($value->booking_id, array('current_status' => "InProcess", 'internal_status' => SPARE_PARTS_SHIPPED));
                            
                            $booking['internal_status'] = SPARE_PARTS_SHIPPED;
                            $actor = $next_action = 'not_define';
                            $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, $booking['internal_status'], $partner_id, $value->booking_id);
                            if (!empty($partner_status)) {
                                $booking['partner_current_status'] = $partner_status[0];
                                $booking['partner_internal_status'] = $partner_status[1];
                                $actor = $booking['actor'] = $partner_status[2];
                                $next_action = $booking['next_action'] = $partner_status[3];
                            }
                            $this->booking_model->update_booking($value->booking_id, $booking);
                            
                            $this->notify->insert_state_change($value->booking_id, "Invoice Approved", "", "Admin Approve Partner OOW Invoice ", $this->session->userdata('id'), $this->session->userdata('employee_id'),
                    $actor,$next_action,_247AROUND);
                            
                            // Send OOW invoice to Inventory Manager
                            /* changed by kalyani for removing duplicate invoice id error
                            //$url = base_url() . "employee/invoice/generate_oow_parts_invoice/" . $value->id;
                            //$async_data['booking_id'] = $value->booking_id;
                            //$this->asynchronous_lib->do_background_process($url, $async_data);
                            */
                            $this->generate_oow_parts_invoice($value->id);
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
        } else {
            echo "Please Enter All Field";
        }
            
    }
    /**
     * 
     * @param Array $part_data
     * @return Array
     */
    function validate_spare_purchase_data($part_data){
        $invalid_data = "";
        $spare_id =array();
        foreach ($part_data as $id =>$value) {
            if(empty($value['booking_id'])){
                $invalid_data = "Booking ID should not be empty";
                break;
            } else if(empty($value['hsn_code'])){
                
                $invalid_data = "HSN Code should not be empty";
                
            } else if(empty($value['gst_rate'])){
                
                $invalid_data = "GST Rate should not be empty";
                
            }else if(empty($value['basic_amount'])){
                
                 $invalid_data = "Invoice Amount should not be empty";
            } else {
                array_push($spare_id, $id);
            }
        }
        
        if(!empty($invalid_data)){
            return array('status' => false, "message" => $invalid_data);
        } else {
            return array('status' => true, "data" => $spare_id);
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
        $this->form_validation->set_rules('vendor_partner_id', 'Vendor Partner ID', 'required|trim');
        $this->form_validation->set_rules('vendor_partner', 'Vendor_partner', 'required|trim');
        $this->form_validation->set_rules('invoice_type', 'Invoice Type', 'required|trim');
        $this->form_validation->set_rules('invoice_date', 'Invoice Period', 'required|trim');
        $this->form_validation->set_rules('remarks', 'Remarks', 'required|trim');

        if ($this->input->post('service_rate') > 0) {
            $this->form_validation->set_rules('service_count', 'Service QTY', 'required|trim|greater_than[0]');
            $this->form_validation->set_rules('service_description', 'Description', 'required|trim');
            $this->form_validation->set_rules('service_gst_rate', 'GST Rate', 'required|trim');
        }

        if ($this->input->post('part_rate') > 0) {
            $this->form_validation->set_rules('part_count', 'Part QTY', 'required|trim|greater_than[0]');
            $this->form_validation->set_rules('part_description', 'Description', 'required|trim');
            $this->form_validation->set_rules('part_gst_rate', 'GST Rate', 'required|trim');
        }


        if ($this->form_validation->run()) {
            $data['vendor_partner'] = $this->input->post('vendor_partner');
            $data['vendor_partner_id'] = $this->input->post('vendor_partner_id');
            $data['type'] = $this->input->post('invoice_type');
            $service_rate = $this->input->post('service_rate');
            $parts_rate = $this->input->post('part_rate');
            $data['num_bookings'] = $this->input->post('service_count');
            $data['parts_count'] = $this->input->post('part_count');
            $service_gst_rate = trim($this->input->post('service_gst_rate'));
            $part_gst_rate = trim($this->input->post('part_gst_rate'));
            $service_hsn_code = $this->input->post('service_hsn_code');
            $part_hsn_code = $this->input->post('part_hsn_code');
            $data['remarks'] = $this->input->post('remarks');
            $service_description = $this->input->post('service_description');
            $part_description = $this->input->post('part_description');
            $reference_number = $this->input->post('reference_numner');

            $custom_date = explode("-", $this->input->post('invoice_date'));
            $sd = $custom_date[0];
            $ed = $custom_date[1];

            $invoice_date = date('Y-m-d');
            $hsn_code = "";

            if ($data['vendor_partner'] == "vendor") {

                $entity_details = $this->vendor_model->getVendorDetails("gst_no as gst_number, sc_code,"
                        . "state,address as company_address,company_name,district, pincode", array("id" => $data['vendor_partner_id']));
                
                if(!empty($entity_details[0]['gst_number'])){
                    
                    $c_gst = $this->invoice_lib->check_gst_number_valid($data['vendor_partner_id'], $entity_details[0]['gst_number']);
                    
                } else {
                    $c_gst = TRUE;
                }

            } else {

                $entity_details = $this->partner_model->getpartner_details("gst_number,"
                        . "company_name, state, address as company_address, district, pincode, "
                        . "invoice_email_to,invoice_email_cc", array('partners.id' => $data['vendor_partner_id']));
                $c_gst = true;
            }

            if (!empty($c_gst)) {

                if ($data['type'] == "CreditNote") {

                    $invoice_id = $this->invoice_lib->create_invoice_id("ARD-CN");
                    $type = "Credit Note";
                    $data['type_code'] = "B";
                    $data['vertical'] = SERVICE;
                    $data['category'] = INSTALLATION_AND_REPAIR;
                    $data['sub_category'] = CREDIT_NOTE;
                    $data['accounting'] = 1;
                } else {

                    $invoice_id = $this->invoice_lib->create_invoice_id("ARD-DN");
                    $type = "Debit Note";
                    $data['type_code'] = "A";
                    $data['vertical'] = SERVICE;
                    $data['category'] = INSTALLATION_AND_REPAIR;
                    $data['sub_category'] = DEBIT_NOTE;
                    $data['accounting'] = 1;
                }
                $invoice = array();
                if ($service_rate > 0) {
                    $s = $this->get_credit_debit_note_array_data(0, $service_description, $service_rate, $data['num_bookings'], "Service", $service_gst_rate, $service_hsn_code, $entity_details[0], true);
                    array_push($invoice, $s[0]);
                    $hsn_code = $service_hsn_code;
                }

                if ($parts_rate > 0) {
                    $p = $this->get_credit_debit_note_array_data(0, $part_description, $parts_rate, $data['parts_count'], "Product", $part_gst_rate, $part_hsn_code, $entity_details[0], true);
                    array_push($invoice, $p[0]);
                    $hsn_code = $part_hsn_code;
                }
                if (!empty($invoice)) {
                    $response = $this->invoices_model->_set_partner_excel_invoice_data($invoice, $sd, $ed, $type, $invoice_date);
                    $response['meta']['invoice_id'] = $invoice_id;
                    $response['meta']['reference_invoice_id'] = $reference_number;
                    $status = $this->invoice_lib->send_request_to_create_main_excel($response, "final");
                    if (!empty($status)) {
//                    $convert = $this->invoice_lib->send_request_to_convert_excel_to_pdf($invoice_id, "final");
                        $convert = $this->invoice_lib->convert_invoice_file_into_pdf($response, "final");
                        $output_pdf_file_name = $convert['main_pdf_file_name'];
                        $response['meta']['invoice_file_main'] = $output_pdf_file_name;
                        $response['meta']['copy_file'] = $convert['copy_file'];
                        $response['meta']['invoice_file_excel'] = $invoice_id . ".xlsx";

                        $this->upload_invoice_to_S3($invoice_id, false);
                        $file = $this->upload_create_update_invoice_to_s3($invoice_id);
                        if (isset($file['invoice_detailed_excel'])) {
                            $data['invoice_detailed_excel'] = $file['invoice_detailed_excel'];
                        }


                        $data['invoice_id'] = $invoice_id;
                        $data['total_service_charge'] = $response['meta']['total_ins_charge'];
                        $data['parts_cost'] = $response['meta']['total_parts_charge'];
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
                        $data['hsn_code'] = $part_hsn_code;
                        $data['reference_invoice_id'] = $response['meta']['reference_invoice_id'];

                        $status = $this->invoices_model->insert_new_invoice($data);
                        if (!empty($status)) {
                            log_message("info", __METHOD__ . " Invoice Inserted ");
                            echo "Success";
                        } else {
                            log_message("info", __METHOD__ . " Invoice not Inserted ");
                            echo "Error";
                        }

                        unlink(TMP_FOLDER . $invoice_id . ".xlsx");
                        unlink(TMP_FOLDER . "copy_" . $invoice_id . ".xlsx");
                    } else {
                        echo "Invoice is not generating";
                    }
                } else {
                    log_message("info", __METHOD__ . " Invoice is not generating");
                    echo "Invoice is not generating";
                }
            } else {
                log_message("info", __METHOD__ . " Invalid GST Number");
                echo "Invoice is not generating. Please check GST Number";
            }
        } else {
            log_message("info", __METHOD__ . " validation failed");
            echo validation_errors();
        }
    }

    function get_credit_debit_note_array_data($key, $description, $rate, $qty, $product_or_services, 
            $gst_rate, $hsn_code,$partner_data,$is_gst_required){
        $data = array();
        $data[$key]['description'] =  $description;
        $data[$key]['rate'] = $rate;
        $data[$key]['qty'] = $qty;
        $data[$key]['taxable_value'] = ($rate * $qty);
        $data[$key]['product_or_services'] = $product_or_services;
        if(!empty($partner_data['gst_number'])){
             $data[$key]['gst_number'] = $partner_data['gst_number'];
             
        } else if($is_gst_required){
             $data[$key]['gst_number'] = TRUE;
             
        } else {
            $data[$key]['gst_number'] = "";
        }
        
        $data[$key]['company_name'] = $partner_data['company_name'];
        $data[$key]['company_address'] = $partner_data['company_address'];
        $data[$key]['district'] = $partner_data['district'];
        $data[$key]['pincode'] = $partner_data['pincode'];
        $data[$key]['state'] = $partner_data['state'];
        $data[$key]['hsn_code'] = $hsn_code;
       
        $data[$key]['gst_rate'] = $gst_rate;
        
        return $data;
    }
    
    /**
     * @desc: This function is used to get partners annual charges consolidated table view
     * @params: void
     * @return: view
     * 
     */
    
    public function partners_annual_charges() {  
         $this->miscelleneous->load_nav_header();
         $data['annual_charges_data'] =$this->invoices_model->get_partners_annual_charges("public_name, invoice_id, vendor_partner_id, "
                 . "from_date, to_date,amount_collected_paid, invoice_file_main");  
         $this->load->view('employee/partners_annual_charges_view', $data);  
    }
    
    /**
     * @desc This function is used to generate GST Credit note 
     * @param String $invoice_id
     */
    function generate_gst_creditnote($dn_invoice_id) {
        log_message("info", __METHOD__ . " Invoice ID " . $dn_invoice_id);
        $invoice_details = $this->invoices_model->get_invoices_details(array("invoice_id" => $dn_invoice_id));
        if (!empty($invoice_details)) {
            if ($invoice_details[0]['credit_generated'] == 0) {
                $invoice_id = $invoice_details[0]['reference_invoice_id'];
                if (empty($invoice_id)) {
                    $tmp_invoice_id = explode("Around-GST-DN-", $dn_invoice_id);
                    $invoice_id = $tmp_invoice_id[1];
                }
                $credit_invoice_details = array(
                    'invoice_id' => "Around-GST-CN-" . $invoice_id,
                    "reference_invoice_id" => $invoice_details[0]['reference_invoice_id'],
                    'type' => 'CreditNote',
                    'vendor_partner_id' => $invoice_details[0]['vendor_partner_id'],
                    'type_code' => 'B',
                    'vendor_partner' => $invoice_details[0]['vendor_partner'],
                    'invoice_date' => date("Y-m-d"),
                    'from_date' => date("Y-m-d", strtotime($invoice_details[0]['from_date'])),
                    'to_date' => date("Y-m-d", strtotime($invoice_details[0]['to_date'])),
                    'total_service_charge' => $invoice_details[0]['total_service_charge'],
                    'total_amount_collected' => $invoice_details[0]['total_amount_collected'],
                    //Amount needs to be Paid to Vendor
                    'amount_collected_paid' => -$invoice_details[0]['amount_collected_paid'],
                    'settle_amount' => 0,
                    //Add 1 month to end date to calculate due date
                    'due_date' => date("Y-m-d"),
                    //add agent id
                    'agent_id' => $this->session->userdata('id'),
                    'vertical' => SERVICE,
                    'category' => INSTALLATION_AND_REPAIR,
                    'sub_category' => GST_CREDIT_NOTE,
                    'accounting' => 0,
                );
               // print_r($credit_invoice_details); die();
                $this->invoices_model->action_partner_invoice($credit_invoice_details);
                $this->invoices_model->update_partner_invoices(array('invoice_id' => $dn_invoice_id), array('credit_generated' => 1));
                
                $email_template = $this->booking_model->get_booking_email_template(CN_AGAINST_GST_DN);
                if(!empty($email_template)){
                    $subject = vsprintf($email_template[4], array($invoice_id));
                    $message = vsprintf($email_template[0], array($invoice_details[0]['total_amount_collected'], $invoice_id)); 
                    $email_from = $email_template[2];
                    $get_rm_email =$this->vendor_model->get_rm_sf_relation_by_sf_id($invoice_details[0]['vendor_partner_id']); 
                    $get_owner_email = $this->vendor_model->getVendorDetails("owner_email", array('id' =>$invoice_details[0]['vendor_partner_id']));
                    $to = $get_owner_email[0]['owner_email'];
                    $cc = $email_template[3].", ".$get_rm_email[0]['official_email'];
                    $this->notify->sendEmail($email_from, $to, $cc, '', $subject, $message, '', CN_AGAINST_GST_DN);
                }
                
                
                redirect(base_url() . 'employee/invoice/invoice_summary/' . $invoice_details[0]['vendor_partner'] . "/" . $invoice_details[0]['vendor_partner_id']);
                
            } else {
                echo "Already CreditNote Generated";
            }
        } else {
            echo "Invoice Not Found";
        }
    }
    
    function get_pending_defective_parts_list($vendor_id){
        $select = "spare_parts_details.booking_id, shipped_parts_type, DATEDIFF(CURRENT_TIMESTAMP, service_center_closed_date) as pending_age, challan_approx_value";
        $where = array(
            "spare_parts_details.defective_part_required"=>1,
            "spare_parts_details.service_center_id" => $vendor_id,
            "status IN ('".DEFECTIVE_PARTS_PENDING."', '".DEFECTIVE_PARTS_REJECTED."')  " => NULL,
            "DATEDIFF(CURRENT_TIMESTAMP, service_center_closed_date) > 15 " => NULL
            
        );
       
        $data = $this->service_centers_model->get_spare_parts_booking($where, $select);
        if(!empty($data)){
            $html = "";
            foreach ($data as $key => $value) {
                $html .= "<tr>";
                $html .= "<td>".($key +1)."</td>";
                $html .= "<td>".$value['booking_id']."</td>";
                $html .= "<td>".$value['shipped_parts_type']."</td>";
                $html .= "<td>".$value['pending_age']." Days </td>";
                $html .= "<td> Rs.".$value['challan_approx_value']."</td>";
                $html .= "</tr>";
                
            }
            
            echo $html;
        } else {
            echo "DATA NOT FOUND";
        }
        
    }
    /**
     * @desc insert Invoice details(Break up if invoice)
     * @param Arary $invoice
     */
    function insert_invoice_breakup($invoice){
        $invoice_breakup = array();
        foreach($invoice['booking'] as $value){
            $invoice_details = array(
                "invoice_id" => $invoice['meta']['invoice_id'],
                "description" => $value['description'],
                "qty" => $value['qty'],
                "product_or_services" => $value['product_or_services'],
                "rate" => $value['rate'],
                "taxable_value" => $value['taxable_value'],
                "cgst_tax_rate" => (isset($value['cgst_rate']) ? $value['cgst_rate'] : 0),
                "sgst_tax_rate" => (isset($value['sgst_rate']) ? $value['sgst_rate'] : 0),
                "igst_tax_rate" => (isset($value['igst_rate']) ? $value['igst_rate'] : 0),
                "cgst_tax_amount" => (isset($value['cgst_tax_amount']) ? $value['cgst_tax_amount'] : 0),
                "sgst_tax_amount" => (isset($value['sgst_tax_amount']) ? $value['sgst_tax_amount'] : 0),
                "igst_tax_amount" => (isset($value['igst_tax_amount']) ? $value['igst_tax_amount'] : 0),
                "hsn_code" => $value['hsn_code'],
                "total_amount" => $value['total_amount'],
                "create_date" => date('Y-m-d H:i:s')
                
            );
            
            array_push($invoice_breakup, $invoice_details);
        }
         $this->invoices_model->insert_invoice_breakup($invoice_breakup);
    }
    /**
     * @desc This function is used to process to update invoices with invoice breakup/Description
     * @param String $vendor_partner
     * @param int $vendor_partner_id
     * @param String $invoice_id
     */
    function update_invoice_with_breakup($vendor_partner, $vendor_partner_id, $invoice_id) {
        log_message('info', __METHOD__);
        if (!empty($invoice_id) && !empty($vendor_partner) && !empty($vendor_partner_id)) {

            $this->form_validation->set_rules('invoice_id', 'Invoice ID', 'required|trim');
            $this->form_validation->set_rules('around_type', 'Around Type', 'required|trim');
            $this->form_validation->set_rules('from_date', 'Invoice Period', 'required|trim');
            $this->form_validation->set_rules('type', 'Type', 'required|trim');
            if ($this->form_validation->run()) {
                $invoice = $this->input->post('invoice');
                $main['type_code'] = $this->input->post('around_type');
                $main['invoice_date'] = date('Y-m-d', strtotime($this->input->post('invoice_date')));
                $main['due_date'] = date('Y-m-d', strtotime($this->input->post('due_date')));
                $main['type'] = $this->input->post('type');
                $date_range = $this->input->post('from_date');
                $date_explode = explode("-", $date_range);
                $main['from_date'] = trim($date_explode[0]);
                $main['to_date'] = trim($date_explode[1]);
                $main['remarks'] = $this->input->post("remarks");
                $main['vertical'] = $this->input->post("vertical");
                $main['category'] = $this->input->post("category");
                $main['sub_category'] = $this->input->post("sub_category");
                $main['accounting'] = $this->input->post("accounting");
                $main['rcm'] = 0;

                $gst_amount = 0;
                $service_charge = 0;
                $tds_sc_charge = 0;
                $parts_charge = 0;
                $parts_qty = 0;
                $service_qty = 0;
                $is_igst = false;
                $total_amount_collected_amount = 0;
                foreach ($invoice as $id => $value) {
                    $data = array();
                    $data['description'] = $value['description'];
                    $data['product_or_services'] = $value['product_or_services'];
                    $data['hsn_code'] = $value['hsn_code'];
                    $data['qty'] = $value['qty'];
                    $data['rate'] = $value['rate'];
                    $data['taxable_value'] = $value['taxable_value'];
                    $data['sgst_tax_rate'] = (isset($value['sgst_tax_rate']) ? $value['sgst_tax_rate'] : 0);
                    $data['cgst_tax_rate'] = (isset($value['cgst_tax_rate']) ? $value['cgst_tax_rate'] : 0);
                    $data['igst_tax_rate'] = (isset($value['igst_tax_rate']) ? $value['igst_tax_rate'] : 0);
                    $data['cgst_tax_amount'] = (isset($value['cgst_tax_amount']) ? $value['cgst_tax_amount'] : 0);
                    $data['igst_tax_amount'] = (isset($value['igst_tax_amount']) ? $value['igst_tax_amount'] : 0);
                    $data['sgst_tax_amount'] = (isset($value['sgst_tax_amount']) ? $value['sgst_tax_amount'] : 0);
                    $data['total_amount'] = $value['total_amount'];

                    $this->invoices_model->update_invoice_breakup(array('id' => $id), $data);

                    if (isset($value['igst_tax_amount']) && $value['igst_tax_amount'] > 0) {
                        $is_igst = TRUE;
                    }
                    $gst_amount += ($data['cgst_tax_amount'] + $data['igst_tax_amount'] + $data['sgst_tax_amount']);
                    $total_amount_collected_amount += $data['total_amount'];
                    if ($data['product_or_services'] == "Product" || $data['product_or_services'] == "Parts" || $data['product_or_services'] == "Part") {
                        $parts_charge += $data['taxable_value'];
                        $parts_qty += $data['qty'];
                    } else {
                        if ($data['product_or_services'] == "Service") {
                            $service_charge += $data['taxable_value'];
                            $service_qty += $data['qty'];
                        }
                        $tds_sc_charge += $data['taxable_value'];
                    }
                }
                $main['total_service_charge'] = $service_charge;
                $main['num_bookings'] = $service_qty;
                $main['parts_cost'] = $parts_charge;
                $main['parts_count'] = $parts_qty;
                if ($vendor_partner == "vendor") {
                    $entity_details = $this->vendor_model->viewvendor($vendor_partner_id);
                } else {

                    $entity_details = $this->partner_model->getpartner_details("gst_number, state", array('partners.id' => $vendor_partner_id));
                }
                if ($gst_amount == 0) {

                    $main['cgst_tax_amount'] = $main['sgst_tax_amount'] = $main['sgst_tax_rate'] = $main['cgst_tax_rate'] = 0;
                    $main['igst_tax_amount'] = 0;
                    $main['igst_tax_rate'] = 0;
                } else if ($gst_amount > 0) {
                    if ($is_igst) {
                        $main['igst_tax_amount'] = $gst_amount;
                    } else {
                        $main['cgst_tax_amount'] = $main['sgst_tax_amount'] = $gst_amount / 2;
                    }
                }
                switch ($main['type_code']) {
                    case 'A':
                        log_message('info', __FUNCTION__ . " .. type code:- " . $main['type']);
                        $main['total_amount_collected'] = $total_amount_collected_amount;
                        $main['around_royalty'] = sprintf("%.2f", $main['total_amount_collected']);
                        $main['amount_collected_paid'] = sprintf("%.2f", $main['total_amount_collected']);

                        break;
                    case 'B':
                        log_message('info', __FUNCTION__ . " .. type code:- " . $main['type']);
                        $tds['tds'] = 0;
                        $tds['tds_rate'] = 0;
                        if ($main['type'] == 'FOC') {

                            if ($vendor_partner == "vendor") {
                                $tds = $this->check_tds_sc($entity_details[0], ($tds_sc_charge));
                            } else {
                                $tds['tds'] = 0;
                                $tds['tds_rate'] = 0;
                            }
                        } else if ($main['type'] == 'CreditNote' || $main['type'] == 'Buyback' || $main['type'] == 'Stand' || $main['type'] == "Parts" || $main['type'] == "Liquidation") {

                            $tds['tds'] = 0;
                            $tds['tds_rate'] = 0;
                        }

                        $main['around_royalty'] = 0;
                        $main['total_amount_collected'] = $total_amount_collected_amount;
                        $main['amount_collected_paid'] = -($main['total_amount_collected'] - $tds['tds'] - $main['rcm']);
                        $main['tds_amount'] = $tds['tds'];
                        $main['tds_rate'] = $tds['tds_rate'];
                        break;
                }
                $main['invoice_id'] = $invoice_id;
                $file = $this->upload_create_update_invoice_to_s3($invoice_id);
                if (isset($file['invoice_file_main'])) {
                    $main['invoice_file_main'] = $file['invoice_file_main'];
                }
                if (isset($file['invoice_detailed_excel'])) {
                    $main['invoice_detailed_excel'] = $file['invoice_detailed_excel'];
                }
                if (isset($file['invoice_file_excel'])) {
                    $main['invoice_file_excel'] = $file['invoice_file_excel'];
                }
                $main['agent_id'] = $this->session->userdata("id");
                $status = $this->invoices_model->action_partner_invoice($main);

                if ($status) {
                    log_message('info', __METHOD__ . ' Invoice details inserted ' . $invoice_id);
                } else {

                    log_message('info', __METHOD__ . ' Invoice details not inserted ' . $invoice_id);
                }

                redirect(base_url() . 'employee/invoice/invoice_summary/' . $vendor_partner . "/" . $vendor_partner_id);
            } else {
                $userSession = array('error' => "Invoice is not update. Please try again");
                $this->session->set_userdata($userSession);
                redirect(base_url() . 'employee/invoice/invoice_summary/' . $vendor_partner . "/" . $vendor_partner_id);
            }
        } else {
            $userSession = array('error' => "Invoice is not update. Please try again");
            $this->session->set_userdata($userSession);
            redirect(base_url() . 'employee/invoice/insert_update_invoice/' . $vendor_partner);
        }
    }

   /**
    * This function loads the qvc email form view.
    */
    
    function QC_transaction_details(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/QC_transaction_form');
    }
    
    /**
     * @desc This function helps in sending email.
     * @param This also loads the template view.
     */
    public function process_to_send_QC_transction(){ 
        $agent_id = $this->session->userdata('id');
        $status = $this->_process_advance_payment($agent_id, null);
        if ($status) {
            $data = array(
                'transction_date' => $this->input->post('tdate'),
                'transction_amount' => $this->input->post('amount'),
                'review' => $this->input->post('description')
            );
            $email_template = $this->booking_model->get_booking_email_template(QWIKCILVER_TRANSACTION_DETAIL);
            $partner_detail = $this->partner_model->getpartner(QWIKCILVER_PARTNER_ID, FALSE); //owner_email, invoice_email_to
            if(!empty($email_template)){
                $fromemail = $email_template[2];
                $cc = $partner_detail[0]['invoice_email_cc'].", ".$email_template[3].", ".$this->session->userdata('official_email');
                $toemail = $partner_detail[0]['owner_email']." ,".$partner_detail[0]['invoice_email_to'];
                $subject = vsprintf($email_template[4], array($partner_detail[0]['company_name']));
                $mesg = $this->load->view('templates/QC_email_template.php',$data,true);
                $this->notify->sendEmail($fromemail, $toemail,$cc, '', $subject, $mesg, '', QWIKCILVER_TRANSACTION_DETAIL);
            }
            $userSession = array('success' => "Bank Transaction Added");
            $this->session->set_userdata($userSession);
            redirect(base_url() . "employee/invoice/QC_transaction_details");
        } else {
            $userSession = array('error' => "Bank Transaction Not Added");
            $this->session->set_userdata($userSession);
            redirect(base_url() . "employee/invoice/QC_transaction_details");
        }
    }

    function get_all_invoice_vertical(){ 
        $vertical_input = $this->input->post('vertical_input');
        $html = "<option value='' selected disabled>Select Vertical</option>";
        $select = 'distinct(vertical)';
        $vertical = $this->invoices_model->get_invoice_tag($select);
        foreach ($vertical as $vertical) {
            $html .= '<option value="'.$vertical['vertical'].'"';
            if($vertical['vertical'] === $vertical_input){
               $html .= 'selected'; 
            }
            $html .= '>'.$vertical['vertical'].'</option>';
        }
        echo $html;
    }
    
    function get_invoice_category(){
        $vertical = $this->input->post('vertical');
        $category_input = $this->input->post('category_input');
        $html = "<option value='' selected disabled>Select Category</option>";
        $select = 'distinct(category)';
        $where = array('vertical'=>$vertical);
        $category = $this->invoices_model->get_invoice_tag($select, $where);
        foreach ($category as $category) {
            $html .= '<option value="'.$category['category'].'"';
            if($category['category'] === $category_input){
               $html .= 'selected'; 
            }
            $html .= '>'.$category['category'].'</option>';
        }
        echo $html;
    }
    
    function get_invoice_sub_category(){
        $vertical = $this->input->post('vertical');
        $category = $this->input->post('category');
        $sub_category_input = $this->input->post('sub_category_input');
        $html = "<option value='' selected disabled>Select Category</option>";
        $select = 'distinct(sub_category), accounting';
        $where = array('vertical'=>$vertical, 'category'=>$category);
        $sub_category = $this->invoices_model->get_invoice_tag($select, $where);
        foreach ($sub_category as $sub_category) {
            $html .= '<option data-id="'.$sub_category['accounting'].'" value="'.$sub_category['sub_category'].'"';
            if($sub_category['sub_category'] === $sub_category_input){
               $html .= 'selected'; 
            }
            $html .= '>'.$sub_category['sub_category'].'</option>';
        }
        echo $html;
    }
    /**
     *  @desc : This function add new HSN Code view page.
     * @param : void 
     */
    function get_add_new_hsn_code() {
        $this->checkUserSession();
        $data['hsn_code_list'] = $this->invoices_model->get_hsncode_details('*', array());
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/addnewhsncode', $data);
    }
    /**
     *  @desc : This is used to add data in hsn_code_details table
     *  @param : void
     *  @return : Json
     */
    function post_add_new_hsncode() {
        $this->checkUserSession();
        $hsn_code = $this->input->post('hsn_code');
        if (!empty($hsn_code)) {
            $hsn = array('hsn_code' => $hsn_code, 'gst_rate' => $this->input->post('gst_rate'), 'agent_id' => $this->input->post('agent_id'));
            $last_inserted_id = $this->inventory_model->insert_query('hsn_code_details', $hsn);
            if(!empty($last_inserted_id)){
                echo json_encode(array('status'=>'success'));
            } else {
            echo json_encode(array('status'=>'failed'));                
            }
        }
    }
      /**
     * @desc This is used to update hsn code details related field. Just pass field name, value and table primary key id
     */
    function update_hsn_code_details_column(){
        $this->form_validation->set_rules('data', 'Data', 'required');
        $this->form_validation->set_rules('id', 'id', 'required');
        $this->form_validation->set_rules('column', 'column', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post('data');
            $id = $this->input->post('id');
            $column = $this->input->post('column');            
            $this->invoices_model->update_hsn_code_details(array('id' => $id), array($column => $data));
            echo "Success";
        } else {
            echo "Error";
        }
    }
    
    /**
     * @desc @desc This function is used to send mail to accountant when Min
     * @param Array $data
     */
    function send_guarantee_exist_mail($data){
        $email_template = $this->booking_model->get_booking_email_template(MINIMUM_GUARANTEE_MAIL_TEMPLATE);
       
        if (!empty($email_template)) {
            $employee_rm_relation = $this->vendor_model->get_rm_sf_relation_by_sf_id($data['vendor_id']);
          //  print_r($employee_rm_relation); exit();
            $rm_email = "";
            if(!empty($employee_rm_relation)){
                $rm_email = ", ".$employee_rm_relation[0]['official_email'];
            }

            $to = $email_template[1].$rm_email;
            $cc = $email_template[3];
            $bcc = $email_template[5];
            $subject = $email_template[4];
            $emailBody = vsprintf($email_template[0], array($data['company_name'], $data['minimum_guarantee_charge'], $data['invoice_amount'], $data['from_date']));
            $this->notify->sendEmail($email_template[2], $to, $cc, $bcc, $subject, $emailBody, "", MINIMUM_GUARANTEE_MAIL_TEMPLATE);
        }
    }
    /**
     * @desc: This is used to show all details of invoice
     * @param Sting $vendor_partner
     * @param String $invoice_id
     */
 function view_invoice($vendor_partner, $invoice_id) {
        if ($invoice_id) {
            $where = array('invoice_id' => $invoice_id);
            //Get Invocie details from Vendor Partner Invoice Table
            $invoice_details['invoice_details'] = $this->invoices_model->getInvoicingData($where, TRUE);
            $agent_array = $this->employee_model->getemployeefromid($invoice_details['invoice_details'][0]['agent_id']);
            if(!empty($agent_array)){
                $invoice_details['agent_name'] = $agent_array[0]['full_name'];
            }
            $invoice_details['invoice_breakup'] = $this->invoices_model->get_breakup_invoice_details("*", array('invoice_id' => $invoice_id));
        }
        $invoice_details['vendor_partner'] = $vendor_partner;
        
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/view_invoice', $invoice_details);
    } 
    
    /**
     * @desc: This function is used to update bank transaction table
     * @param $data $where
     * @return boolean
     */
    function update_bank_transaction_description() {
        $data = array("description" => $this->input->post("description"));
        $where = array("id" => $this->input->post("id"));
        $this->invoices_model->update_bank_transactions($where, $data);
        echo true;
    }      
    
    /**
     * @desc: This function is used to create ProForma invoices
     */
    function generate_partner_proforma_invoice() { 
        $this->checkUserSession();
        log_message('info', __FUNCTION__ . " Entering....");
        $this->form_validation->set_rules('partner_name', 'Partner Name', 'trim');
        $this->form_validation->set_rules('partner_id', 'Partner ID', 'required|trim');
        $this->form_validation->set_rules('daterange', 'Start Date', 'required|trim');
        $this->form_validation->set_rules('invoice_type', 'Invoice Type', 'required|trim');
        $this->form_validation->set_rules('service_charge', 'Service Charge', 'required|trim');
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
            $email_tag = CRM_SETUP_PROFORMA_INVOICE_EMAIL_TAG;
            
            $invoice_date = date("Y-m-d");
            $invoice_id = $this->invoice_lib->create_proforma_invoice_id("Around-PI");
            
            $response = $this->generate_partner_additional_invoice($partner_data[0], $description,
            $amount, $invoice_id, $sd, $ed, $invoice_date, $hsn_code, "Proforma Invoice", $email_tag, 1, DEFAULT_TAX_RATE);
            
            $basic_sc_charge = $response['meta']['total_taxable_value'];
            $invoice_details = array(
                'invoice_id' => $invoice_id,
                'type_code' => 'A',
                'type' => $type,
                'vendor_partner' => 'partner',
                'vendor_partner_id' => $partner_id,
                'invoice_file_main' => $response['meta']['invoice_file_main'],
                'invoice_file_excel' => $response['meta']['invoice_id'] . ".xlsx",
                "invoice_file_pdf" => $response['meta']['copy_file'],
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
                "vertical" => SERVICE,
                "category" => RECURRING_CHARGES,
                "sub_category" => CRM_PERFORMA,
                "accounting" => 0
            );
            
            $this->invoices_model->insert_performa_invoice($invoice_details);
            log_message('info', __METHOD__ . ":Performa Invoice ID inserted");
            $this->session->set_flashdata('file_error', 'CRM Setup Proforma Invoice Generated');
            redirect(base_url() . "employee/invoice/invoice_partner_view");

        } else {
            $this->session->set_flashdata('file_error','CRM Setup Proforma invoices Not Generated');
            log_message('info', __METHOD__ . ": Validation Failed");
            $this->invoice_partner_view();
        }
    }
}
