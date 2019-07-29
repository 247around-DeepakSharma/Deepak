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

class Accounting extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();

        $this->load->helper(array('form', 'url'));

        $this->load->model("invoices_model");
        $this->load->model("accounting_model");
        $this->load->model("vendor_model");
        $this->load->model("partner_model");
        $this->load->model('bb_model');
        $this->load->model('booking_model');
        $this->load->library('miscelleneous');
        $this->load->library("notify");
        $this->load->library('PHPReport');
        $this->load->library('form_validation');
        $this->load->library("session");
        $this->load->library('s3');
        $this->load->library('invoice_lib');
        //  $this->load->library('email');

//    if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
//            return TRUE;
//        } else {
//           echo PHP_EOL . 'Terminal Access Not Allowed' . PHP_EOL;
//           redirect(base_url() . "employee/login");
//        }
    }

    /**
     * @desc: This Function is used to show the challan upload form
     * @param: void
     * @return : void
     */
    function get_challan_upload_form() {
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/upload_challan_details');
    }

    /**
     * @desc: This Function is used to show the challan EDIT form
     * @param: string
     * @return : void
     */
    function get_challan_edit_form($challan_id = "") {
        $data['challan_data'] = $this->accounting_model->fetch_challan_details('', $challan_id);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/upload_challan_details', $data);
    }

    /**
     * @desc: This Function is used to insert the challan details into database
     * @param: void
     * @return : void
     */
    function process_challan_upload_form() {
        $checkvalidation = $this->checkChallanValidationForm();
        if ($checkvalidation) {
            //Start Processing Challan File Upload
            if (($_FILES['challan_file']['error'] != 4) && !empty($_FILES['challan_file']['tmp_name'])) {

                $tmpFile = $_FILES['challan_file']['tmp_name'];
                $challan_file = trim($this->input->post('cin_no')) . '_challanfile_' . substr(md5(uniqid(rand(0, 9))), 0, 15) . "." . explode(".", $_FILES['challan_file']['name'])[1];
                $_POST['challan_file_name'] = $challan_file;
                //Upload files to AWS
                $bucket = BITBUCKET_DIRECTORY;
                $directory_xls = "vendor-partner-docs/" . $challan_file;
                $this->s3->putObjectFile($tmpFile, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                //Logging success for file uppload
                log_message('info', __CLASS__ . 'Challan FILE is being uploaded sucessfully.');
            } else {
                //Redirect back to Form
                if ($this->input->post('id')) {
                    $is_image_exist = true;
                } else {
                    $error_msg = "Please Upload Valid Challan File";
                    $this->session->set_flashdata('error_msg', $error_msg);
                    redirect(base_url() . 'employee/accounting/get_challan_upload_form');
                }
            }

            //Start Processing annexure file upload if it is present
            if (($_FILES['annexure_file']['error'] != 4) && !empty($_FILES['annexure_file']['tmp_name'])) {

                $tmpFile = $_FILES['annexure_file']['tmp_name'];
                $annexure_file = trim($this->input->post('cin_no')) . '_annexure_file_' . substr(md5(uniqid(rand(0, 9))), 0, 15) . "." . explode(".", $_FILES['annexure_file']['name'])[1];
                $_POST['annexure_file_name'] = $annexure_file;
                //Upload files to AWS
                $bucket = BITBUCKET_DIRECTORY;
                $directory_xls = "vendor-partner-docs/" . $annexure_file;
                $this->s3->putObjectFile($tmpFile, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                //Logging success for file uppload
                log_message('info', __CLASS__ . 'Annexure FILE is being uploaded sucessfully.');
            }

            $daterange = explode('-', $this->input->post('daterange'));
            $from_date = $daterange[0];
            $to_date = $daterange[1];
            //get all the data from the form
            $data = array('serial_no' => trim($this->input->post('serial_no')),
                'cin_no' => trim($this->input->post('cin_no')),
                'type' => trim($this->input->post('challan_type')),
                'amount' => $this->input->post('amount'),
                'bank_name' => trim($this->input->post('bank_name')),
                'paid_by' => trim($this->input->post('paid_by')),
                'challan_tender_date' => $this->input->post('tender_date'),
                'remarks' => trim($this->input->post('remarks')),
                'from_date' => $from_date,
                'to_date' => $to_date
            );

            //if challan file exist then get the file name
            if ($this->input->post('challan_file_name')) {
                $data['challan_file'] = trim($this->input->post('challan_file_name'));
            }

            //if annexure file exist then get the file name
            if ($this->input->post('annexure_file_name')) {
                $data['annexure_file'] = trim($this->input->post('annexure_file_name'));
            }

            //if challan id exist then edit the details else add the details
            if ($this->input->post('id')) {
                $insert_id = $this->accounting_model->edit_challan_details($data, $this->input->post('id'));
            } else {
                $insert_id = $this->accounting_model->insert_challan_details($data);
            }

            //show message on the basis of previous action on database
            if ($insert_id) {
                $success_msg = "Challan Details uploaded successfully";
                $this->session->set_flashdata('success_msg', $success_msg);
                redirect(base_url() . 'employee/accounting/get_challan_upload_form');
            } else {
                $error_msg = "Error!!! Please Try Again";
                $this->session->set_flashdata('error_msg', $error_msg);
                redirect(base_url() . 'employee/accounting/get_challan_upload_form');
            }
        } else {
            if ($this->input->post('id')) {
                $this->get_challan_edit_form($this->input->post('id'));
            } else {
                $this->get_challan_upload_form();
            }
        }
    }

    /**
     * @desc: This Function is validate the challan upload form
     * @param: void
     * @return : string
     */
    function checkChallanValidationForm() {
        $this->form_validation->set_rules('challan_type', 'Challan Type', 'required|trim');
        $this->form_validation->set_rules('serial_no', 'Serial Number', 'required|trim');
        $this->form_validation->set_rules('cin_no', 'CIN Number', 'required|trim');
        $this->form_validation->set_rules('amount', 'Amount', 'required|trim');
        $this->form_validation->set_rules('bank_name', 'Bank Name', 'required|trim');
        $this->form_validation->set_rules('paid_by', 'Paid By', 'required|trim');
        $this->form_validation->set_rules('daterange', 'DateRange', 'required|trim');
        if ($this->form_validation->run() == FALSE) {
            $this->get_challan_upload_form();
        } else {
            return true;
        }
    }

    /**
     * @desc: This Function is get the challan history view
     * @param: void
     * @return : string
     */
    function get_challan_details() {
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/challan_details');
    }

    /**
     * @desc: This Function is use to get the challan history view on ajax call
     * @param: void
     * @return : string
     */
    function fetch_challan_details($is_tag = "") {
        if ($is_tag != "") {
            $data['is_tag'] = false;
        } else {
            $data['is_tag'] = true;
        }
        $challan_type = $this->input->post('challan_type');
        $data['challan_details'] = $this->accounting_model->fetch_challan_details($challan_type);
        $this->load->view('employee/tag_untag_challan_id_table_view', $data);
    }

    /**
     * @desc: This Function is use to do mapping between invoice id and challan id
     * @param: void
     * @return : void
     */
    function mapping_challanId_to_InvoiceId() {

        $challan_id = $this->input->post('challan_id');
        $invoice_id = $this->input->post('invoice_id');

        //getting invoice id corresponding to challan id
        if (!empty($challan_id)) {

            //getting existing invoice id from vendor_partner_invoices_table 
            $existing_invoice_id = $this->invoices_model->get_invoices_details(array());
            $existing_invoice_id_arr = [];
            $non_existing_invoices_data = '';
            foreach ($existing_invoice_id as $key => $value) {
                array_push($existing_invoice_id_arr, $value['invoice_id']);
            }

            //getting invoice id for each challan id from the submitted form
            foreach ($challan_id as $challan_id_key => $challan_id_value) {
                $invoice_id_array = explode(PHP_EOL, $invoice_id[$challan_id_key]);

                //prepare invoice id data to insert into table if invoice id exist in the vendor_partner_invoices table
                $invoice_data = $this->prepare_invoice_data_to_mapped_challan_id($challan_id_value, $invoice_id_array, $existing_invoice_id_arr);

                if (!empty($invoice_data['exist_invoices_data'])) {
                    //insert data into database in batch
                    $insert_id = $this->accounting_model->insert_invoice_challan_id_mapping_data($invoice_data['exist_invoices_data']);
                    if ($insert_id) {
                        log_message('info', __METHOD__ . " : Invoice ID corresponding to challan ID = $challan_id_value inserted successfully");
                    } else {
                        log_message('info', __METHOD__ . " : Error in inserting Invoice ID corresponding to challan ID = $challan_id_value");
                    }
                }

                if (!empty($invoice_data['non_exist_invoices_data'])) {
                    $str = implode(',', $invoice_data['non_exist_invoices_data']);
                    $non_existing_invoices_data .= $str . ',';
                }
            }
            $msg = "Mapping has been done successfully ";

            if (!empty($non_existing_invoices_data)) {
                $msg .= "and these Invoices are not exist in the table : " . rtrim($non_existing_invoices_data,',');
            }
            $this->session->set_flashdata('success_msg', $msg);
            redirect(base_url() . 'employee/accounting/get_challan_details');
        } else {
            $error_msg = "Empty field could't be inserted";
            $this->session->set_flashdata('error_msg', $error_msg);
            redirect(base_url() . 'employee/accounting/get_challan_details');
        }
    }

    /**
     * @desc: This Function is use to prepare invoice id to mapped with challan id
     * @param: string $challan_id_value, $invoice_id_array, $existing_invoice_id_arr
     * @return : array $data
     */
    function prepare_invoice_data_to_mapped_challan_id($challan_id_value, $invoice_id_array, $existing_invoice_id_arr) {
        $data['exist_invoices_data'] = [];
        $data['non_exist_invoices_data'] = [];
        foreach ($invoice_id_array as $invoice_id_value) {

            //check if invoice id is exist in vendor_partner_invoices table
            $checked_data = $this->check_invoiceId_exist_in_table($invoice_id_value, $existing_invoice_id_arr);
            if (!empty($checked_data['exist_invoices'])) {
                $arr = array('challan_id' => trim($challan_id_value),
                    'invoice_id' => trim($checked_data['exist_invoices']),
                    'create_date' => date('Y-m-d H:i:s')
                );
                array_push($data['exist_invoices_data'], $arr);
            }

            if (!empty($checked_data['non_exist_invoices'])) {
                array_push($data['non_exist_invoices_data'], $checked_data['non_exist_invoices']);
            }
        }
        return $data;
    }

    /**
     * @desc: This Function is use check if invoice id is exist in the vendor_partner_invoices
     * @param: string $invoice_id_value, $existing_invoice_id_arr
     * @return : array $data
     */
    function check_invoiceId_exist_in_table($invoice_id_value, $existing_invoice_id_arr) {

        if (in_array($invoice_id_value, $existing_invoice_id_arr)) {
            $data['exist_invoices'] = $invoice_id_value;
        } else {
            $data['non_exist_invoices'] = $invoice_id_value;
        }
        return $data;
    }

    /**
     * @desc: This Function is used to show the payment report
     * @param: void
     * @return : void
     */
    function accounting_report() {
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/payment_history_report_view');
    }

    /**
     * @desc: This Function is used to show the payment report by ajax call
     * @param: void
     * @return : view
     */
    function show_accounting_report() {
        $payment_type = $this->input->post('type');
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $new_to_date = date('Y/m/d', strtotime($to_date . "+1 days"));
        $partner_vendor = $this->input->post('partner_vendor');
        $report_type = $this->input->post('report_type');
        $is_challan_data = $this->input->post('is_challan_data');
        $invoice_data_by = $this->input->post('invoice_by');


        $data['invoice_data'] = $this->accounting_model->get_account_report_data($payment_type, $from_date, $new_to_date, $partner_vendor, $is_challan_data, $invoice_data_by, $report_type);

        if (!empty($data['invoice_data'])) {
            $data['partner_vendor'] = $partner_vendor;
            $data['payment_type'] = $payment_type;
            $data['report_type'] = $report_type;
            foreach($data['invoice_data'] as $key => $value) {
                $data['invoice_details_data'][$key] = $this->invoices_model->get_breakup_invoice_details("invoice_id, product_or_services, rate, qty, taxable_value, cgst_tax_amount, sgst_tax_amount, igst_tax_amount, total_amount", array("invoice_id" => $value['invoice_id']));
            }
            echo $this->load->view('employee/paymnet_history_table_view', $data);
        } else {
            echo "error";
        }
    }

    /**
     * @desc: This Function is used to show the view for search the invoice Id 
     * @param: void
     * @return : void
     */
    function show_search_invoice_view() {
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/search_invoice_data');
    }

    /**
     * @desc: This Function is used to search the invoice Id 
     * @param: void
     * @return : view
     */
    function search_invoice_id() {
        $invoice_id = trim($this->input->post('invoice_id'));
        $invoice_remarks = trim($this->input->post('invoice_remarks'));
        $request_data = array();
        if(!empty($invoice_id)){
            $request_data['invoice_id'] = $invoice_id;
        }
        
        if(!empty($invoice_remarks)){
            $request_data["remarks like '%$invoice_remarks%' "] = NULL;
        }
        
        if(!empty($request_data)){
            $data['invoiceid_data'] = $this->invoices_model->getInvoicingData($request_data, true);
            if (!empty($data['invoiceid_data'])) {

                echo $this->load->view('employee/invoiceid_details_data_table', $data);
            } else {
                echo "<div class='text-danger text-center'> <b>No Data Found <b></div>";
            }
        }else{
            echo "<div class='text-danger text-center'> <b>No Data Found <b></div>";
        }
        
    }

    /**
     * @desc: This Function is used untag those 
     * invoice id which mapped with incorrect challan id
     * @param: void
     * @return : void
     */
    function untag_challan_invoice_id() {
        $challan_id = $this->input->post('challan_id');
        $invoice_id = $this->input->post('invoice_id');

        //check if challan id is empty
        if (!empty($challan_id)) {

            $non_updated_invoices = '';

            //getting invoice id corresponding to challan id
            foreach ($challan_id as $challan_id_key => $challan_id_value) {
                $invoice_id_array = explode(PHP_EOL, $invoice_id[$challan_id_key]);

                //update invoice id with corresponding challan id
                foreach ($invoice_id_array as $value) {
                    if ($value !== 0 && $value !== '') {
                        $update = $this->accounting_model->untag_challan_invoice_id($challan_id_value, $value);
                        if ($update) {
                            log_message('info', __METHOD__ . " : Invoice ID corresponding to challan ID = $challan_id_value updated successfully");
                        } else {
                            $non_updated_invoices .= $value . ',';
                            log_message('info', __METHOD__ . " : Error in updating Invoice ID corresponding to challan ID = $challan_id_value");
                        }
                    }
                }
            }

            if (!empty($non_updated_invoices)) {
                $msg = "Some invoice id successfully untag from challan id";
                $msg .= " and these invoices are not updated : " . $non_updated_invoices;

                $this->session->set_flashdata('error_msg', $msg);
                redirect(base_url() . 'employee/accounting/get_challan_details');
            } else {
                $msg = "Invoices Id successfully untag from challan id";

                $this->session->set_flashdata('success_msg', $msg);
                redirect(base_url() . 'employee/accounting/get_challan_details');
            }
        } else {
            $error_msg = "Empty field could't be updated";
            $this->session->set_flashdata('error_msg', $error_msg);
            redirect(base_url() . 'employee/accounting/get_challan_details');
        }
    }

    /**
     * @desc: This Function is used to show the view for search the challan Id 
     * @param: void
     * @return : void
     */
    function show_search_challan_id_view() {
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/search_challan_id');
    }

    /**
     * @desc: This Function is used to search the challan Id 
     * @param: void
     * @return : view
     */
    function search_challan_id() {
        $cin_no = trim($this->input->post('cin_no'));
        $where = array('cin_no' => $cin_no);
        $data['challan_details'] = $this->accounting_model->get_challan_details($where);
        //print_r($data);exit();
        if (!empty($data['challan_details'])) {
            echo $this->load->view('employee/challanid_details_data_table', $data);
        } else {
            echo "<div class='text-danger text-center'> <b>No Data Found <b></div>";
        }
    }

    /**
     * @desc: This Function is used to show invoices mapped with challan id
     * @param: $challan_id string
     * @return : void()
     */
    function get_tagged_incoice_challan_data($challan_id) {
        $data['tagged_invoice_data'] = $this->accounting_model->get_tagged_invoice_challan_data($challan_id);
        if (!empty($data['tagged_invoice_data'])) {
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/show_tagged_invoices_challan_data', $data);
        } else {
            "No Invoices tagged with this data";
        }
    }

    /**
     * @desc: This Function is used to search the transaction details from
     * bank transaction table 
     * @param: void()
     * @return : void()
     */
    function search_bank_transaction() {
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/search_bank_transaction');
    }

    function process_search_bank_transaction() {
        $transaction_type = $this->input->post('transaction_type');
        $transaction_date = $this->input->post('transaction_date');
        $transaction_amount = trim($this->input->post('transaction_amount'));
        $transaction_description = trim($this->input->post('transaction_description'));
        
        $where = array();
        if(!empty($transaction_type))
        {
            $where['credit_debit'] = $transaction_type;
        }
        if(!empty($transaction_date))
        {
            $modified_transaction_date = date('Y-m-d', strtotime($transaction_date));
            $where['transaction_date'] = $modified_transaction_date;
        }
        if(!empty($transaction_amount) && is_numeric($transaction_amount))
        {
            $min_transaction_amount = $transaction_amount - 5;
            $max_transaction_amount = $transaction_amount + 5;
            
            if(!empty($transaction_type) && $transaction_type === 'Credit')
            {
                $where["credit_amount >="] = $min_transaction_amount;
                $where["credit_amount <="] = $max_transaction_amount;
            }
            else if(!empty($transaction_type) && $transaction_type === 'Debit')
            {
                $where["debit_amount >="] = $min_transaction_amount;
                $where["debit_amount <="] = $max_transaction_amount;
            }else{
                $where["(credit_amount >= $min_transaction_amount AND credit_amount <= $max_transaction_amount) OR (debit_amount >= $min_transaction_amount AND debit_amount <= $max_transaction_amount)"] = NULL;
            }    
                
        }
        
        if(!empty($transaction_description))
        {
            $where["description like '%$transaction_description%'"] = NULL;
        }
        
        if(!empty($where))
        {
            $select = 'bank_transactions.* , employee.full_name as agent_name';
            $data['transaction_details'] = $this->invoices_model->get_bank_transactions_details($select,$where,true);
            if(!empty($data['transaction_details'])){
                echo $this->load->view('employee/show_bank_transaction_details',$data);
            }else{
                echo "<div class='text-danger text-center'> <b> No Data Found <b></div>";
            }
        }else{
            echo "<div class='text-danger text-center'> <b>Please Enter Correct Details <b></div>";
        }
    }
    
    //Function to load add ducuments page
   function shipped_documents(){
        //$courier_details = $this->accounting_model->get_courier_documents();
        $partner_id = $this->reusable_model->get_search_result_data("partners","*",array("is_active"=>1),NULL,NULL,NULL,NULL,NULL,array());
        $sf_id = $this->reusable_model->get_search_result_data("service_centres","*",array("active"=>1),NULL,NULL,NULL,NULL,NULL,array());
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/shipped_documents',array('partner_id'=>$partner_id,'sf_id'=>$sf_id, 'courier_details'=>null));
    }
    
    //for displaying contact in view->shipped_documents.php on the basis of partner id
    
    function get_contact(){
        $data = $this->reusable_model->get_search_result_data("contact_person","*",array("entity_id"=>$this->input->post('id'),"entity_type"=>$this->input->post('entity')),NULL,NULL,NULL,NULL,NULL,array());
        echo json_encode($data);
    }
    
    //checks if the invoice ID entered by the user exists in the database
    function check_invoice_id($id,$entityType){
        $invoiceData = $this->reusable_model->get_search_result_data("vendor_partner_invoices","vendor_partner_id", array("invoice_id"=>$id,'vendor_partner'=>$entityType),NULL,
                NULL, NULL, NULL, NULL,array());
        if(!empty($invoiceData))
            echo $invoiceData[0]['vendor_partner_id'];
        else{
            echo "false";
        }
    }
    
    //save all the added document
    function save_documents(){
        $this->checkUserSession();
        $tmp_courier_name = $_FILES['courier']['tmp_name'];
        $courier_name = implode("",explode(" ",$this->input->post('courier'))).'_courier_'.substr(md5(uniqid(rand(0,9))), 0, 15).".".explode(".",$_FILES['courier']['name'])[1];
        move_uploaded_file($tmp_courier_name, TMP_FOLDER.$courier_name);
        $bucket = BITBUCKET_DIRECTORY;
        $directory_xls = "vendor-partner-docs/" . $courier_name;
        $this->s3->putObjectFile(TMP_FOLDER.$courier_name, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
        $_POST['courier'] = $courier_name;
        $attachment_courier = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $courier_name;

        //Logging success for file uppload
        log_message('info', __CLASS__ . ' Courier FILE is being uploaded sucessfully.');
        
        $shipped_documents['sender_entity_id'] = $this->session->userdata('id');
        $shipped_documents['sender_entity_type'] = $this->session->userdata('userType');
        if($this->session->userdata('userType') == 'employee'){
            $shipped_documents['sender_entity_type'] = '247around';
        }
        $shipped_documents['courier_file'] = $this->input->post('courier');
        $shipped_documents['courier_name'] = $this->input->post('courier_name');
        $shipped_documents['receiver_entity_type'] = $this->input->post('entity_type');
        $shipped_documents['document_type']= $this->input->post('doc_type');
        $shipped_documents['notification_email']= $this->input->post('email_input');
        if($this->input->post('contact')){
            $shipped_documents['contact_person_id'] = $this->input->post('contact');
        }
        else if($this->input->post('contact_input')){
            $shipped_documents['contact_person_id'] = $this->input->post('contact_input');
        }
        $shipped_documents['create_date'] = date('Y-m-d H:i:s');
        $shipped_documents['AWB_no'] = $this->input->post('awb_no');
        $shipped_documents['shipment_date'] = $this->input->post('shipment_date');
        if($this->input->post('remarks')){
            $shipped_documents['remarks'] = $this->input->post('remarks');
        }
        if($this->input->post('invoice_id')){
            $shipped_documents['partner_invoice_id'] = $this->input->post('invoice_id');
        }
        $entity_type = $this->input->post('entity_type');
        $doc_type= $this->input->post('doc_type');
        switch($doc_type){
            case "invoice":
                $id = $this->input->post('vendor_partner_id');
                break;
            case "contract":
                switch($entity_type){
                    case "247around":
                        $id = _247AROUND;
                        break;
                    case "vendor":
                        $id = $this->input->post('sfid');
                        break;
                    case "partner":
                        if($this->input->post('partnerid'))
                            $id = $this->input->post('partnerid');
                        else
                            $id = $this->input->post('partnerbox');
                        break;
                }
                break;
        }
        $shipped_documents['receiver_entity_id'] = $id;
        if($this->input->post('add_edit') == 'add'){ 
            $add_documents = $this->reusable_model->insert_into_table("courier_details",$shipped_documents);
            /***  Send email for courier detail to given email id ****/
            $email_template = $this->booking_model->get_booking_email_template(COURIER_DOCUMENT);
            $from = $email_template[2];
            $to = $this->input->post('email_input');
            $subject = $email_template[4];
            $message = vsprintf($email_template[0], array($this->input->post('doc_type'), $this->input->post('courier_name'), $this->input->post('awb_no'), $this->input->post('shipment_date'), $this->input->post('remarks')));
            $attachment = TMP_FOLDER.$courier_name;
            $this->notify->sendEmail($from, $to, "", "", $subject, $message, $attachment, "Courier Detail");
            /**** End ****/
        }
        else{ 
            $add_documents = $this->reusable_model->update_table("courier_details",$shipped_documents,array('id'=>$this->input->post('add_edit')));
        }
        if(!($add_documents)){
             echo '<script language="javascript">'; echo 'alert("Error in saving document!!")'; echo '</script>';
            exit();
        }
         redirect(base_url() . "employee/accounting/view_shipped_documents");
    }
    
    /**
     * @desc: This funtion will check Session
     * @param: void
     * @return: true if details matches else session is distroyed.
     */
    function checkUserSession() {
         if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            log_message('info', __FUNCTION__. " Session Expire for Service Center");
            $this->session->sess_destroy();
            redirect(base_url() . "employee/login");
        }
    }
    //to view all the documents
    function view_shipped_documents(){
        $courier_details = $this->accounting_model->get_courier_documents();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/view_shipped_documents',array('courier_details'=>$courier_details));
    
    }
    
    //to update a single doc
    function update_shipped_document($id){
        $courier_details = $this->accounting_model->get_courier_documents($id);
        $partner_id = $this->reusable_model->get_search_result_data("partners","*",array("is_active"=>1),NULL,NULL,NULL,NULL,NULL,array());
        $sf_id = $this->reusable_model->get_search_result_data("service_centres","*",array("active"=>1),NULL,NULL,NULL,NULL,NULL,array());        
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/shipped_documents',array('courier_details'=>$courier_details,'partner_id'=>$partner_id,'sf_id'=>$sf_id));   
    }
    //deletes doc and redirects to same page
    function delete_shipped_document($id){
        if(!($this->reusable_model->update_table('courier_details', array('is_active'=>0), array('id'=>$id)))){
            echo '<script language="javascript">'; 
            echo 'alert("Record not deleted")'; 
            echo '</script>';
        }
        else{
            echo '<script language="javascript">'; 
            echo 'alert("Record deleted")';
            echo '</script>';
            
        } 
        redirect(base_url() . "employee/accounting/view_shipped_documents");
    }
    /**
     * @desc This is used to get all unique invoice type 
     */
    function get_invoice_type(){
        $data = $this->invoices_model->get_invoices_details(array(), "Distinct type");
        $html = "<option value=''>Select Invoice Type</option>";
        foreach ($data as $value) {
            $html .= "<option value='".$value['type']."' >".$value['type']."</option>";
        }
        
        echo $html;
    }
    
    /**
     * @desc This function is generalize used to get the data for invoice datatable
     * @param request_type
     */
    function get_invoice_searched_data(){
        log_message("info", __METHOD__);
        $post = $this->getInvoiceDataTablePost();
        $post['column_order'] = array(NULL, 'vendor_partner_invoices.id');
        $post['column_search'] = array('invoice_id');
        $data = array();
        
        switch ($this->input->post('request_type')){
            case 'sf_invoice_summary':
                $data = $this->getServiceCenterInvoicingData($post);
                break;
            case 'partner_invoice_summary':
                $data = $this->getPartnerInvoicingData($post);
                break;
            case 'admin_search':
                $data = $this->getSearchedInvoicingData($post);
                break;
            default :
               break; 
        }
        
       
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->invoices_model->count_all_invoices($post),
            "recordsFiltered" =>  $this->invoices_model->count_filtered_invoice('*', $post),
            "data" => $data,
        );
        
        echo json_encode($output);
        
    }
        
    /**
     * @desc Filter invoice data from Invoice Search page from admin
     * @param type $post
     * @return type
     */
     
    function getSearchedInvoicingData($post){
        $select = "IFNULL(service_centres.name, partners.public_name) as party_name, vendor_partner_invoices.*";
        $list = $this->invoices_model->searchInvoicesdata($select, $post);
        $no = $post['start'];
        $data = array();
        foreach ($list as $invoice_list) {
            $no++;
            $row =  $this->invoice_datatable($invoice_list, $no);
            $data[] = $row;
        }
        return $data;
    }
            
     /**
     * @desc This function is used to get service center invoice data
     * @param type $post
     * @return type
     */
    function getServiceCenterInvoicingData($post){
        $select = "IFNULL(service_centres.name, partners.public_name) as party_name, vendor_partner_invoices.*";
        $list = $this->invoices_model->searchInvoicesdata($select, $post);
        $no = $post['start'];
        $data = array();
        foreach ($list as $invoice_list) {
            $no++;
            $row =  $this->invoice_sf_datatable($invoice_list, $no);
            $data[] = $row;
        }
        return $data;
    }
    
     /**
     * @desc This function is used to get partner invoice data
     * @param type $post
     * @return type
     */
    function getPartnerInvoicingData($post){
        $select = "vendor_partner_invoices.*";
        $list = $this->invoices_model->searchInvoicesdata($select, $post);
        $no = $post['start'];
        $data = array();
        foreach ($list as $invoice_list) {
            $no++;
            $row =  $this->invoice_partner_datatable($invoice_list, $no);
            $data[] = $row;
        }
        return $data;
    }
    
     /**
     * @desc This function is used to get service center invoice bank transactions
     * @param type $post
     * @return type
     */
    function getServiceCenterBankTransactionData($post){
        $list = $this->invoices_model->searchPaymentSummaryData('*', $post);
        $no = $post['start'];
        $data = array();
        foreach ($list as $transaction_list) {
            $no++;
            $row =  $this->sf_bank_transaction_datatable($transaction_list, $no);
            $data[] = $row;
        }
        return $data;
    }
    
    /**
     * @desc This function is used to get partner bank transactions
     * @param type $post
     * @return type
     */
    function getPartnerBankTransactionData($post){
        $list = $this->invoices_model->searchPaymentSummaryData('*', $post);
        $no = $post['start'];
        $data = array();
        foreach ($list as $transaction_list) {
            $no++;
            $row =  $this->partner_transaction_datatable($transaction_list, $no);
            $data[] = $row;
        }
        return $data;
    }
    
    /**
     * @desc This is used to generate Data table row
     * @param Array $invoice_list
     * @param int $no
     * @return Array
     */
    function invoice_datatable($invoice_list, $no){
        $row = array();
        $invoice_links = "";
        if($invoice_list->settle_amount == 1){
            $row[] = '<span class="satteled_row">'.$no.'</span>';
        }
        else{
            $row[] = $no;
        }
        
        $invoice_links .= '<p style="margin-top:15px;"><a  href="https://s3.amazonaws.com/'.BITBUCKET_DIRECTORY.'/invoices-excel/'.$invoice_list->invoice_file_main.'">Main</a></p>';
        $invoice_links .= '<p style="margin-top:15px;"><a  href="https://s3.amazonaws.com/'.BITBUCKET_DIRECTORY.'/invoices-excel/'.$invoice_list->invoice_detailed_excel.'">Detail</a>';
        $invoice_links .= '</p><p style="margin-top:15px;"><a  href="javascript:void(0);" onclick="get_invoice_payment_history(this)" data-id="'.$invoice_list->invoice_id.'">History</a></p>';
        
        $row[] = "<a href='". base_url()."employee/invoice/invoice_summary/".$invoice_list->vendor_partner."/".$invoice_list->vendor_partner_id."' target='_blank'>".$invoice_list->party_name."</a>";
        $row[] = $invoice_list->invoice_id.$invoice_links;
        $row[] = $invoice_list->type;
        $row[] = $invoice_list->num_bookings."/".$invoice_list->parts_count;
        $row[] = date("jS M, Y", strtotime($invoice_list->invoice_date))." <br/><br/> ".date("jS M, Y", strtotime($invoice_list->from_date)). " to ". date("jS M, Y", strtotime($invoice_list->to_date));
        $row[] = $invoice_list->total_amount_collected;
        $row[] = sprintf("%.2f",($invoice_list->total_service_charge + $invoice_list->service_tax));
        $row[] = sprintf("%.2f", $invoice_list->total_additional_service_charge );
        $row[] = sprintf("%.2f", ($invoice_list->parts_cost + $invoice_list->vat));
        $row[] = sprintf("%.2f", $invoice_list->tds_amount);
        $row[] = sprintf("%.2f", $invoice_list->penalty_amount);
        $row[] = sprintf("%.2f",$invoice_list->igst_tax_amount + $invoice_list->cgst_tax_amount + $invoice_list->sgst_tax_amount);
        $row[] = ($invoice_list->amount_collected_paid < 0)?  sprintf("%.2f",$invoice_list->amount_collected_paid) : 0;
        $row[] = ($invoice_list->amount_collected_paid > 0)?  sprintf("%.2f",$invoice_list->amount_collected_paid) : 0;
        $row[] = $invoice_list->amount_paid;
        $row[] = $invoice_list->remarks;
        $row[] = $invoice_list->vertical;
        $row[] = $invoice_list->category;
        $row[] = $invoice_list->sub_category;
        $a_update = '<a href="'.base_url().'employee/invoice/insert_update_invoice/'.$invoice_list->vendor_partner.'/'.$invoice_list->invoice_id.'"';
        if($invoice_list->amount_paid > 0){
            $a_update .= " disabled ";
        }
        $a_update .= ' class="btn btn-sm btn-info">update</a> ';
        $row[] = $a_update;
        
        $resend_invoice = '<a href="'.base_url().'employee/invoice/sendInvoiceMail/'.$invoice_list->invoice_id.'" class="btn btn-sm btn-primary">Resend Invoice</a>';
        $row[] = $resend_invoice;
//        if(($invoice_list->type == "DebitNote") && $invoice_list->credit_generated == 0){
//            $row[] = '<a target="_blank" href="'.base_url().'employee/invoice/generate_gst_creditnote/'.$invoice_list->invoice_id.'" class="btn btn-sm btn-success"> Generate</a>';
//        } else {
//            $row[] = "";
//        }
        return $row;
    }
    /**
     * @desc Get POST data from DataTable
     * @return Array
     */
    function getInvoiceDataTablePost(){
        
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        if(!empty($search['value'])){
           $post['search_value'] = trim($search['value']); 
        }
                
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
        
        $vendor_partner = $this->input->post("vendor_partner");
        $vendor_partner_id = $this->input->post("vendor_partner_id");
        $invoice_date = $this->input->post("invoice_date");
        $invoice_period = $this->input->post("invoice_period_date");
        $settle_amount = $this->input->post("settle");
        $remarks = trim($this->input->post("invoice_remarks"));
        $invoice_type = trim($this->input->post("invoice_type"));
        $invoice_id = $this->input->post("invoice_id");
        
        if(!empty($vendor_partner)){
            $post['where']['vendor_partner'] = $vendor_partner;
        }
        if(!empty($vendor_partner_id)){
            if($vendor_partner_id != "All"){
                $post['where']['vendor_partner_id'] = $vendor_partner_id;
            } 
        }
        
        if(!empty($invoice_date)){
           $in = explode("/", $invoice_date);
           $post['where']['invoice_date >="'.$in[0].'"'] = NULL;
           $post['where']['invoice_date <= "'.$in[1].'"'] = NULL;
           
        }
        
        if(!empty($invoice_period)){
            $period = explode("/", $invoice_period);
            $post['where']['invoice_date >="'.$period[0].'"'] = NULL;
            $post['where']['invoice_date <= "'.$period[1].'"'] = NULL;
        }

        if($settle_amount != 2){
            $post['where']['settle_amount'] = $settle_amount;
        }
        
        if(!empty($remarks)){
            $post['where']['vendor_partner_invoices.remarks LIKE "%'.$remarks.'%" '] = NULL;
        }
        
        if(!empty($invoice_type)){
            $post['where']['vendor_partner_invoices.type'] = $invoice_type;
        }
        
        if(!empty($invoice_id)){
            $post['where']['vendor_partner_invoices.invoice_id LIKE "%'.$invoice_id.'%"'] = NULL;
        }
        
        if(!empty($this->input->post("vertical"))){  
            $post['where']['vendor_partner_invoices.vertical'] = $this->input->post("vertical");
        }
        
        if(!empty($this->input->post("category"))){
            $post['where']['vendor_partner_invoices.category'] = $this->input->post("category");
        }
        
        if(!empty($this->input->post("sub_category"))){
            $post['where']['vendor_partner_invoices.sub_category'] = $this->input->post("sub_category");
        }
        
        if($this->input->post("is_msl") != NULL) {
            if($this->input->post("is_msl")) {
                $post['where_in']['vendor_partner_invoices.sub_category'] = array(DEFECTIVE_RETURN, IN_WARRANTY, MSL, MSL_SECURITY_AMOUNT, NEW_PART_RETURN );
            }
            else {
                $post['where_not_in']['vendor_partner_invoices.sub_category'] = array(DEFECTIVE_RETURN, IN_WARRANTY, MSL, MSL_SECURITY_AMOUNT, NEW_PART_RETURN );
            }
        }
        if(!empty($this->input->post("group_by"))){
            $post['group_by'] = $this->input->post("group_by");
        }
        return $post;
    }
    
    /**
     * @desc: This Function is used to show the view for search the invoice Id 
     * @param: void
     * @return : void
     */
    function bank_transactions() {
       $this->miscelleneous->load_nav_header();
       $this->load->view('employee/payment_summary');
    }
    
    /**
     * @desc This function is generalize used to get the data for invoice datatable
     * @param request_type
     */
    function get_payment_summary_searched_data(){
        $post = $this->getPaymentSummaryDataTablePost();
        $post['column_order'] = array( NULL, 'bank_transactions.id');
        $post['column_search'] = array('description', 'credit_amount', 'debit_amount', 'invoice_id', 'transaction_date', 'transaction_id');
        $data = array();
        
        switch ($this->input->post('request_type')){
            case 'sf_bank_transaction':
                $data = $this->getServiceCenterBankTransactionData($post);
                break;
            case 'partner_bank_transaction':
                $data = $this->getPartnerBankTransactionData($post);
                break;
            case 'admin_search':
                $data = $this->getAdminBankTransactionData($post);
                break;
            case 'invoice_summary_bank_transaction':
                $post['order_by'] = array("bank_transactions.id" => "desc");
                $data = $this->getInvoiceSummaryBankTransactionData($post);
                break;
            default :
               break; 
        }
        
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->invoices_model->count_all_transactions($post),
            "recordsFiltered" =>  $this->invoices_model->count_filtered_bank_transaction('*', $post),
            "data" => $data,
        );
        log_message("info", __METHOD__." final outpoot ". json_encode($output, TRUE)); 
        echo json_encode($output);
    }
     /**
     * @desc This function is used to filter bank transaction data from payment summary page
     * @return json for datatable
     */
    function getInvoiceSummaryBankTransactionData($post){
        $select = "bank_transactions.*";
        $list = $this->invoices_model->searchPaymentSummaryData($select, $post);
        $no = $post['start'];
        $data = array();
        foreach ($list as $transaction_list) {
            $no++;
            $row =  $this->invoice_summary_bank_transaction_datatable($transaction_list, $no);
            $data[] = $row;
        }
        return $data;
    }
    
    
     /**
     * @desc This function is used to filter bank transaction data from payment summary page
     * @return json for datatable
     */
    function getAdminBankTransactionData($post){
        $select = "bank_transactions.*, employee.full_name";
        if($this->input->post("vendor_partner") == "vendor"){
            $select .= ", service_centres.name as name";
        }
        else if($this->input->post("vendor_partner") == "partner"){
            $select .= ", partners.public_name as name";
        }
        else{
            $select .=  ', CASE WHEN partner_vendor = "vendor" THEN service_centres.name ELSE partners.company_name END as "name"';
        }
        
        $list = $this->invoices_model->searchPaymentSummaryData($select, $post);
        $no = $post['start'];
        $data = array();
        foreach ($list as $transaction_list) {
            $no++;
            $row =  $this->bank_transaction_datatable($transaction_list, $no);
            $data[] = $row;
        }
        return $data;
    }
    
    /**
     * @desc Get POST data from DataTable
     * @return Array
     */
    function getPaymentSummaryDataTablePost(){
        
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = preg_replace("([\",'])", "", trim($search['value']));
        
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
        
        $vendor_partner = $this->input->post("vendor_partner");
        $vendor_partner_id = $this->input->post("vendor_partner_id");
        $transaction_date = $this->input->post("transaction_date");
        $transaction_period = $this->input->post("transaction_period_date");
        $transaction_id = $this->input->post("transaction_id");
       
        if(!empty($vendor_partner)){
            $post['where']['partner_vendor'] = $vendor_partner;
        }
        if(!empty($vendor_partner_id)){
            if($vendor_partner_id != "All"){
                $post['where']['partner_vendor_id'] = $vendor_partner_id;
            } 
        }
        
        if(!empty($transaction_id)){
            $post['where']['transaction_id'] = $transaction_id;
        }
        
        if(!empty($transaction_date)){
           $in = explode("/", $transaction_date);
           $post['where']['bank_transactions.transaction_date >="'.$in[0].'"'] = NULL;
           $post['where']['bank_transactions.transaction_date <= "'.$in[1].'"'] = NULL;
           
        }
        
        if(!empty($transaction_period)){
            $period = explode("/", $transaction_period);
            $post['where']['bank_transactions.create_date >="'.$period[0].'"'] = NULL;
            $post['where']['bank_transactions.create_date <= "'.$period[1].'"'] = NULL;
        }
        
        return $post;
    }
    
    /**
     * @desc This is used to generate Data table row for payment summary
     * @param Array $transaction_list
     * @param int $no
     * @return Array
     */
    function invoice_summary_bank_transaction_datatable($transaction_list, $no){
        $row = array();
        if($transaction_list->is_advance ==1){ 
            $row[] = $no.'<p id="advance_text"> Advance</p>';
        }
        else{
            $row[] = $no;
        }
        $row[] = $transaction_list->transaction_date;
        $row[] = '<span class="text">'.$transaction_list->description.'</span><span class="edit" onclick="bd_update(this, '.$transaction_list->id.')"><i class="fa fa-pencil fa-lg" style="margin-left:5px;"></i></span>';
        $row[] = sprintf("%.2f",$transaction_list->credit_amount); 
        $row[] = sprintf("%.2f",$transaction_list->debit_amount); 
        $row[] = sprintf("%.2f",$transaction_list->tds_amount);
        $row[] = $transaction_list->invoice_id;
        $row[] = $transaction_list->bankname."/".$transaction_list->transaction_mode;
        $row[] = $transaction_list->transaction_id;
        return $row;
    }
    
    /**
     * @desc This is used to generate Data table row for payment summary
     * @param Array $transaction_list
     * @param int $no
     * @return Array
     */
    function bank_transaction_datatable($transaction_list, $no){
        $row = array();
        $row[] = $no;
        $row[] = $transaction_list->name;
        $row[] = $transaction_list->transaction_date;
        $row[] = $transaction_list->description;
        $row[] = sprintf("%.2f",$transaction_list->credit_amount); 
        $row[] = sprintf("%.2f",$transaction_list->debit_amount); 
        $row[] = sprintf("%.2f",$transaction_list->tds_amount);
        $row[] = $transaction_list->invoice_id;
        $row[] = $transaction_list->bankname."/".$transaction_list->transaction_mode;
        $row[] = $transaction_list->transaction_id;
        $row[] = $transaction_list->full_name;
        return $row;
    }
    
    
    /**
     * @desc This is used to generate Data table row for SF invoice table
     * @param Array $order_list
     * @param int $no
     * @return Array  
     */
    function invoice_sf_datatable($order_list, $no){
        //log_message("info",__METHOD__);
        $row = array();
        if($order_list->settle_amount == 1){
            $row[] = '<span class="satteled_row">'.$no.'</span>';
        }
        else{
            $row[] = $no;
        }
        $row[] = $order_list->invoice_id;
        $row[] = date("jS M, Y", strtotime($order_list->from_date)). " to ". date("jS M, Y", strtotime($order_list->to_date));
        $row[] = $order_list->type;
        $row[] = $order_list->sub_category;
        $row[] = '<a href="https://s3.amazonaws.com/bookings-collateral/invoices-excel/'.$order_list->invoice_file_main.'">'.$order_list->invoice_file_main.'</a>';
        $row[] = '<a href="https://s3.amazonaws.com/bookings-collateral/invoices-excel/'.$order_list->invoice_detailed_excel.'">'.$order_list->invoice_detailed_excel.'</a>';
       
        $row[] = $order_list->num_bookings;
       
        $row[] = $order_list->tds_amount;
        if($order_list->amount_collected_paid < 0){ 
            $row[] = abs($order_list->amount_collected_paid); 
            
        } else {
            $row[] = "0.00"; 
            
        } 
        if($order_list->amount_collected_paid > 0){ 
            $row[] = round($order_list->amount_collected_paid,0); 
        } else {
            $row[] = "0.00";
            
        }
        return $row;
    }
    
    /**
     * @desc This is used to generate Data table row for partner invoice table
     * @param Array $order_list
     * @param int $no
     * @return Array  
     */
    function invoice_partner_datatable($order_list, $no){
        log_message("info",__METHOD__);
        $row = array();
        if($order_list->settle_amount == 1){
            $row[] = '<span class="satteled_row">'.$no.'</span>';
        }
        else{
            $row[] = $no;
        }
        $row[] = $order_list->invoice_id;
        if($order_list->amount_collected_paid > 0){
            $row[] = "247Around";
        }
        else{
            $row[] = "Partner";
        }
        $row[] = date("jS M, Y", strtotime($order_list->invoice_date));
        $row[] = date("jS M, Y", strtotime($order_list->from_date)) . " to " . date("jS M, Y", strtotime($order_list->to_date));
        $row[] = $order_list->sub_category;
        $row[] = $order_list->num_bookings . "/" . $order_list->parts_count;
        $row[] = $order_list->tds_amount;
        $row[] = $order_list->total_amount_collected;
        $row[] = $order_list->amount_paid;
        $row[] = sprintf("%.2f", $order_list->total_amount_collected-$order_list->amount_paid);
        $html = '<ul style=" list-style-type: none;">';
        if(!empty($order_list->invoice_file_main)) { 
          $html .=  '<li style="display: inline; font-size: 30px;"><a title="Main File"  href="https://s3.amazonaws.com/'.BITBUCKET_DIRECTORY.'/invoices-excel/'.$order_list->invoice_file_main.'"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a></li>';
        } 
        if(!empty($order_list->invoice_detailed_excel)) { 
           $html .=  '<li style="display: inline;font-size: 30px; margin-left: 10px;"><a  href="https://s3.amazonaws.com/'.BITBUCKET_DIRECTORY.'/invoices-excel/'.$order_list->invoice_detailed_excel.'"><i class="fa fa-file-excel-o" aria-hidden="true"></i></a></li>';
        } 
        $html .=  '</ul>';
        $row[] = $html;
        return $row;
    }
    
    /**
     * @desc This is used to generate Data table row for service center payment summary
     * @param Array $transaction_list
     * @param int $no
     * @return Array
     */
    function sf_bank_transaction_datatable($transaction_list, $no){
        $row = array();
        if($transaction_list->is_advance ==1){ 
            $row[] = $no.'<p id="advance_text"> Advance</p>';
        }
        else{
            $row[] = $no;
        }
        $row[] = $transaction_list->transaction_date;
        $row[] = $transaction_list->description;
        $row[] = round($transaction_list->credit_amount,0);
        $row[] = round($transaction_list->debit_amount,0);
        $row[] = round($transaction_list->tds_amount,0);
        $row[] = $transaction_list->invoice_id;
        $row[] = $transaction_list->bankname."/".$transaction_list->transaction_mode;
        return $row;
    }
    
     /**
     * @desc This is used to generate Data table row partner bank transaction
     * @param Array $transaction_list
     * @param int $no
     * @return Array
     */
    function partner_transaction_datatable($transaction_list, $no){
        $row = array();
       
        $row[] = $no;
        $row[] = date("jS M, Y", strtotime($transaction_list->transaction_date));
        $row[] = round($transaction_list->credit_amount,0);
        $row[] = $transaction_list->invoice_id;
        $row[] = $transaction_list->description;
        return $row;
    }
    
    
     /**
     * @desc This is used to show vendor GST Report
     * @param void
     * @return view
     */
    function show_gst_report(){
        $current_month = date('m');
        if ($current_month > 3) {
            $financial_year = date('Y');
        } else {
            $financial_year = (date('Y') - 1);
        }
        $data = array();
        $select = 'vendor_partner_id, service_centres.name as "name", service_centres.owner_name, service_centres.primary_contact_name, service_centres.owner_phone_1, service_centres.primary_contact_phone_1, CASE WHEN service_centres.active = "1" THEN "Active" ELSE "Inactive" END as "status", SUM(CASE WHEN to_date <= "'.$financial_year.'-03-31"'
                . 'THEN amount_collected_paid ELSE 0 END) as fy_amount, SUM(amount_collected_paid) as total_amount';
        $post['group_by'] = 'vendor_partner_id';
        $post['where'] = array('vendor_partner'=>'vendor', 'invoice_id like "%Around-GST-CN%" OR invoice_id like "%Around-GST-DN%"'=>NULL);
        $post['length'] = -1;
        $post['order_by'] = array('fy_amount'=>'desc');
        $data['data'] = $this->invoices_model->searchInvoicesdata($select, $post);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/gst_report', $data); 
    }
    
     /**
     * @desc This is used show fixed variable charges for vendor partner
     * @param void
     * @return view
     */
    function add_variable_charges(){ 
        $select = "IFNULL( service_centres.name, partners.public_name ) as name, vendor_partner_variable_charges.*";
        $variable_charges['charges'] = $this->invoices_model->get_variable_charge($select, array(), true);
        $variable_charges['charges_type'] = $this->accounting_model->get_variable_charge("id, name");
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/add_variable_charges', $variable_charges);  
    }
    
    /**
     * @desc This function form select box of charges type for variable charges
     * @param void
     * @return html
     */
    function getVendorPartnerVariableChargesType(){
        $variable_charge_detail = $this->accounting_model->get_variable_charge("*");
        $html = '<option disabled>Select charge type</option>';
        foreach($variable_charge_detail as $charges){
            $selected = "";
            if($charges['type'] == trim($this->input->post('type'))){
                $selected = "selected";
            }
            $html .= '<option data-charge-type="'.$charges['type'].'" value="'.$charges['id'].'" '.$selected.'>'.$charges['description'].'</option>';
        }
        echo $html;
    }
    
     /**
     * @desc This is used to add and update fixed variable charges for vendor partner
     * @param void
     * @return view
     */
    function process_variable_charges(){ 
        $data = array();
        $data['entity_type'] = $this->input->post('vendor_partner');
        $data['entity_id'] = $this->input->post('vendor_partner_id');
        $data['fixed_charges'] = $this->input->post('fixed_charges');
        $variable_charge_detail = $this->accounting_model->get_variable_charge("*", array('id'=>$this->input->post('charges_type')));
        $data['charges_type'] = $variable_charge_detail[0]['type'];
        $data['description'] = $variable_charge_detail[0]['description'];
        $data['hsn_code'] = $variable_charge_detail[0]['hsn_code'];
        $data['gst_rate'] = $variable_charge_detail[0]['gst_rate'];

        if(!empty($this->input->post('variable_charges_id')) && $this->input->post('variable_charges_id') > 0){
           $data['update_date'] = date("Y-m-d H:i:s");
           $result = $this->invoices_model->update_into_variable_charge(array('id'=>$this->input->post('variable_charges_id')), $data); 
           $this->session->set_userdata('success', 'Data Updated Successfully');
        }else{
           $data['create_date'] = date("Y-m-d H:i:s");
           $result = $this->invoices_model->insert_into_variable_charge($data);
           $this->session->set_userdata('success', 'Data Entered Successfully');
        }
        if(!$result){
            $this->session->set_userdata('failed', 'Data can not be inserted. Please Try Again...');
        }
        redirect(base_url() . 'employee/accounting/add_variable_charges'); 
    }
    
    /**
     * @desc This is used to fetch gstr2a data from taxpro api
     * @param void
     * @return view
     */
    function generate_gstr2a_report(){ 
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/generate_taxpro_GSTR2a_Data');
    }
    
    
     /**
     * @desc This function is used to generate otp for getting authentication token from taxpro
     * @param void
     * @return api response
     */
    function generate_taxpro_otp(){
       
        $url = TAXPRO_OTP_REQUEST_URL;
        //$url = "http://testapi.taxprogsp.co.in/taxpayerapi/dec/v0.2/authenticate?action=OTPREQUEST&aspid=".ASP_ID."&password=".ASP_PASSWORD."&gstin=27GSPMH0041G1ZZ&username=Chartered.MH.1";
        $activity = array(
            'entity_type' => _247AROUND_PARTNER_STRING,
            'partner_id' => _247AROUND,
            'activity' => __METHOD__,
            'header' => "",
            'json_request_data' => $url,
        );
        $api_response = $this->invoice_lib->taxpro_api_curl_call($url);
        $activity['json_response_string'] = $api_response;
        $this->partner_model->log_partner_activity($activity);
        echo $api_response;
    }
    
     /**
     * @desc This function is used to generate authentication token from taxpro
     * @param otp
     * @return boolean message
     */
    function generate_taxpro_auth_token(){
        $otp = $this->input->post("otp");
        $url = TAXPRO_AUTH_TOKEN_REQUEST_URL.$otp;
        //$url = "http://testapi.taxprogsp.co.in/taxpayerapi/dec/v0.2/authenticate?action=AUTHTOKEN&aspid=".ASP_ID."&password=".ASP_PASSWORD."&gstin=27GSPMH0041G1ZZ&username=Chartered.MH.1&OTP=575757";
        $activity = array(
            'entity_type' => _247AROUND_PARTNER_STRING,
            'partner_id' => _247AROUND,
            'activity' => __METHOD__,
            'header' => "",
            'json_request_data' => $url,
        );
        $api_response = $this->invoice_lib->taxpro_api_curl_call($url);
        $activity['json_response_string'] = $api_response;
        $this->partner_model->log_partner_activity($activity);
        $response = json_decode($api_response);
        if($response->status_cd == '1'){
           $this->fetch_taxpro_gstr2a_data($response->auth_token);
           echo "success";
        }
        else{
           echo "error";
        }
    }
    
    /**
     * @desc This function is used to fetch gstr2a data and save required data into database from taxpro
     * @param authtoken
     * @return void
     */
    function fetch_taxpro_gstr2a_data($autnToken){ 
        $to_date = date("Y-m");
        $from_date = date("2017-07");
        while($from_date < $to_date){
            $year = date('Y', strtotime($from_date));
            $month = date('m', strtotime($from_date));
            $ret_period = $month.$year;
            $url = TAXPRO__FEATCH_GSTR2A_URL.$autnToken.'&ret_period='.$ret_period;
            $activity = array(
                'entity_type' => _247AROUND_PARTNER_STRING,
                'partner_id' => _247AROUND,
                'activity' => __METHOD__,
                'header' => "",
                'json_request_data' => $url,
            );
            $api_response = $this->invoice_lib->taxpro_api_curl_call($url);
            
            $response = json_decode($api_response, TRUE);
            $data_on_gstin_array = $response['b2b'];
            $row_batch = array();
            foreach ($data_on_gstin_array as $data_on_gstin) {
                $gst_no = $data_on_gstin['ctin'];
                $data_on_invoice_array = $data_on_gstin['inv'];
                foreach ($data_on_invoice_array as $data_on_invoice) {
                    $checksum = $data_on_invoice['chksum'];
                        $date =  date("Y-m-d", strtotime($data_on_invoice['idt']));
                        $invoice_val = $data_on_invoice['val'];
                        $invoice_number = $data_on_invoice['inum'];
                        $data_on_invoice_items_array = $data_on_invoice['itms'];
                        foreach ($data_on_invoice_items_array as $data_on_invoice_items) { 
                            $data_on_tax = $data_on_invoice_items['itm_det'];
                            $gst_rate = $data_on_tax['rt'];
                            $taxable_val = $data_on_tax['txval'];
                            if (isset($data_on_tax['iamt'])){
                                $cgst_val = 0;
                                $sgst_val = 0;
                                $igst_val = $data_on_tax['iamt'];
                            }
                            else{
                                $cgst_val = $data_on_tax['camt'];
                                $sgst_val = $data_on_tax['samt'];
                                $igst_val = 0;
                            }
                            $row = array(
                                'gst_no' => $gst_no,
                                'invoice_number' => $invoice_number,
                                'invoice_amount' => $invoice_val,
                                'gst_rate' => $gst_rate,
                                'taxable_value' => $taxable_val,
                                'igst_amount' => $igst_val,
                                'cgst_amount' => $cgst_val,
                                'sgst_amount' => $sgst_val,
                                'invoice_date' => $date,
                                'checksum' => $checksum,
                                'gstr2a_period' => $ret_period,
                                'create_date' => date('Y-m-d H:i:s')
                            );
                        $check_checksum = $this->accounting_model->get_taxpro_gstr2a_data('id', array('checksum' => $checksum));
                        if(empty($check_checksum)){
                            array_push($row_batch, $row);
                        }
                    }
                }
            }
            if(!empty($row_batch)){
                $this->accounting_model->insert_taxpro_gstr2a_data($row_batch);
            }
            
            $activity['json_response_string'] = $api_response;
            $this->partner_model->log_partner_activity($activity);
            
            $from_date = date('Y-m', strtotime('+1 months', strtotime($from_date)));
        }
    }
    
    /**
     * @desc This function is used to show the gstr2a data from taxpro
     * @param voide
     * @return view
     */
    function show_gstr2a_report(){
        $data = array();
        $data['last_updated_data'] = $this->reusable_model->execute_custom_select_query('SELECT `create_date` FROM `taxpro_gstr2a_data` ORDER BY create_date desc LIMIT 1')[0]['create_date'];
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/show_taxpro_GSTR2a_Data', $data);
    }
    
    /**
     * @desc This function is used to get data for gstr2a report
     * @param datatable parameters
     * @return boolean message
     */
    function get_gst2ra_mapped_data(){
        $color_array = array();
        $inv_where = array();
        $post = $this->get_gst2ra_post_data();
        $post['where']['taxpro_gstr2a_data.is_rejected'] =  0;
        $post['where']['taxpro_gstr2a_data.is_mapped'] =  0;
        //$post['where']['NOT EXISTS(select taxpro_checksum from vendor_partner_invoices where vendor_partner_invoices.taxpro_checksum = taxpro_gstr2a_data.checksum)'] =  NULL;
        $post['entity_type'] = $this->input->post("entity");
        if($post['entity_type'] == 'vendor'){
            $inv_where['vendor_partner'] = 'vendor';
            $inv_where['credit_generated'] = 0;
            $inv_where['invoice_id like "%Around-GST-DN%"'] = NULL;
            $post['column_search'] = array('service_centres.name', 'taxpro_gstr2a_data.gst_no', 'taxpro_gstr2a_data.invoice_number');
            $post['column_order'] = 'service_centres.name';
            $select = "taxpro_gstr2a_data.*, service_centres.company_name, service_centres.name, service_centres.id as vendor_id";
        }
        else if($post['entity_type'] == 'partner'){
            $inv_where['vendor_partner'] = 'partner';
            $post['column_search'] = array('partners.public_name', 'taxpro_gstr2a_data.gst_no', 'taxpro_gstr2a_data.invoice_number');
            $post['column_order'] = 'partners.public_name';
            $select = "taxpro_gstr2a_data.*, partners.company_name, partners.public_name as name, partners.id as vendor_id";
        }
        else if($post['entity_type'] == 'other'){
            $post['column_search'] = array('gstin_detail.company_name', 'taxpro_gstr2a_data.gst_no', 'taxpro_gstr2a_data.invoice_number');
            $post['column_order'] = 'taxpro_gstr2a_data.invoice_number';
            $select = "taxpro_gstr2a_data.*, gstin_detail.company_name, gstin_detail.company_name as name, gstin_detail.id as vendor_id";
            $post['where']['service_centres.id'] =  null;
            $post['where']['partners.id'] =  null;
        }
        
        $list = $this->accounting_model->get_gstr2a_mapping_details($post, $select);
        $data = array();
        $no = $post['start'];
        foreach ($list as $data_list) {
            $no++;
            $array_val = $data_list['checksum'];
            if(in_array($array_val, $color_array)){
                $data_list['duplicate_entry'] = 1;
            }
            else{
                array_push($color_array, $array_val);
                $data_list['duplicate_entry'] = 0;
            }
            
            if($post['entity_type'] == 'vendor'){
               $inv_where['vendor_partner_id'] = $data_list['vendor_id'];
               $data_list['vendor_invoices'] = $this->invoices_model->getInvoicingData($inv_where, false);
               $data_list['entity_type'] = 'vendor';
            }
            else if($post['entity_type'] == 'partner'){
               $inv_where['vendor_partner_id'] = $data_list['vendor_id'];
               $inv_where['invoice_id'] = $data_list['invoice_number'];
               $data_list['vendor_invoices'] = $this->invoices_model->getInvoicingData($inv_where, false);
               $data_list['entity_type'] = 'partner';
            }
            else{
                $data_list['vendor_invoices'] = array();
                $data_list['entity_type'] = 'other';
            }
            
            $row =  $this->gstr2a_table_data($data_list, $no);
            $data[] = $row;
        }
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->accounting_model->count_all_taxpro_gstr2a_data($post),
            "recordsFiltered" => $this->accounting_model->count_filtered_taxpro_gstr2a_data($post, 'taxpro_gstr2a_data.id'),
            "data" => $data,
        );
        echo json_encode($output); 
    }
    
    function get_gst2ra_post_data(){
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
        return $post;
    }
    
    function gstr2a_table_data($data_list, $no){
        $row = array();
        $partner_inv_not_found = null;
        if($data_list['entity_type'] == 'vendor'){  
            $inv_href = base_url()."employee/invoice/invoice_summary/vendor/".$data_list['vendor_id']; 
        }
        else if($data_list['entity_type'] == 'partner'){ 
            $inv_href = base_url()."employee/invoice/invoice_summary/partner/".$data_list['vendor_id']; 
            if(empty($data_list['vendor_invoices'])){
                $partner_inv_not_found = 'inv_not_found';
            }
        }
        else{ 
            $inv_href = "#"; 
        }
        
        $row[] = $no;
        $row[] = $data_list['invoice_number'];
        if($data_list['duplicate_entry'] == 1){
            $row[] = "<a class='duplicate_row ".$partner_inv_not_found."' href='".$inv_href."' target='_blank'>".$data_list['name']."</a>";
        }
        else{
            $row[] = "<a class='".$partner_inv_not_found."' href='".$inv_href."' target='_blank'>".$data_list['name']."</a>";
        }
        $row[] = $data_list['gst_no'];
        $row[] = $data_list['invoice_date'];
        $row[] = $data_list['igst_amount'];
        $row[] = $data_list['cgst_amount'];
        $row[] = $data_list['sgst_amount'];
        $total_tax = $data_list['igst_amount']+$data_list['cgst_amount']+$data_list['sgst_amount'];
        $row[] = $total_tax;
        $row[] = $data_list['taxable_value'];
        $row[] = $data_list['invoice_amount'];
        $html = "<select class='invoice_select select2-multiple2' id='selected_invoice_".$no."' onchange='check_tax_amount(".$total_tax.", this)' multiple>";
        foreach($data_list['vendor_invoices'] as $key => $value){
           $html .= "<option data-parent-inv='".$value['reference_invoice_id']."' data-tax='".$value['total_amount_collected']."' value='".$value['invoice_id']."'>".$value['invoice_id']." (".$value['total_amount_collected'].")</option>"; 
        }
        $html .= "<select>";
        $cn_btn = '<button class="btn btn-primary btn-sm" style="margin-right:5px" data-id="'.$data_list['id'].'" data-checksum="'.$data_list['checksum'].'" onclick="generate_credit_note('.$no.', this)" disabled>Generate CN</button>';
        $row[] = $html;
        $row[] = $cn_btn;
        $row[] = "<a class='btn btn-warning btn-sm' onclick='reject(".$data_list['id'].")'  data-toggle='modal' data-target='#myModal'>Remark</a>";
        return $row;
    }
    
    /**
     * @desc This function is used to reject data who is invalid for us
     * @param id
     * @return boolean message
     */
    function reject_taxpro_gstr2a(){ 
        $id = $this->input->post('id');
        $remarks = $this->input->post('remarks');
        echo $this->accounting_model->update_taxpro_gstr2a_data($id, array('is_rejected'=>1, 'reject_remarks'=>$remarks));
    }
    /**
     * @desc This function is used to update flag for credit note generated against thie debit note
     * @param otp
     * @return boolean message
     */
    function update_cn_by_taxpro_gstr2a(){
        $invoices = $this->input->post('parent_inv');
        $checksum = $this->input->post('checksum');
        $cn_remark = $this->input->post('cn_remark');
        $id = $this->input->post('id');
        foreach ($invoices as $invoice_id) {
            $this->invoices_model->update_partner_invoices(array('invoice_id'=>$invoice_id), array('taxpro_checksum'=>$checksum, 'gst_credit_note_remark'=>$cn_remark));
        }
        echo $this->accounting_model->update_taxpro_gstr2a_data($id, array('is_mapped'=>1));
    }
    
    function add_charges_type(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/add_variable_charges_type_form');  
    }
    
    /**
     * @desc This function is used to add charges type
     * @param form
     * @return redirect on same page
     */
    function process_charges_type(){
        $data = array();
        $charge_type = $this->input->post('charges_type');
        $charges_type = $this->accounting_model->get_variable_charge('id', array('type'=>$charge_type));
        if(empty($charges_type)){
            $data['type'] =  $charge_type;
            $data['description'] = $this->input->post('description');
            $data['hsn_code'] = $this->input->post('hsn_code');
            $data['gst_rate'] = $this->input->post('gst_rate');
            $data['created_date'] = date("Y-m-d H:i:s");
            $result = $this->accounting_model->insert_into_variable_charge($data);
            if(!empty($result)){
                $this->session->set_userdata('success', 'Data Entered Successfully');
                redirect(base_url() . 'employee/accounting/add_charges_type'); 
            }
            else{
                $this->session->set_userdata('error', 'Data Not Saved Try Again!');
                redirect(base_url() . 'employee/accounting/add_charges_type'); 
            }
        }
        else{
            $this->session->set_userdata('error', 'Charge Type Already Exist!');
            redirect(base_url() . 'employee/accounting/add_charges_type'); 
        }
        
    }
    
    /*
     * @desc - This function is used to add and update partner variable charges.
     * @param -  get form
     * @render on same page
     */ 
    function process_partner_variable_charges(){
            $data = array();
            $data['entity_type'] = _247AROUND_PARTNER_STRING;
            $data['entity_id'] = $this->input->post('partner_id');
            $data['fixed_charges'] = $this->input->post('fixed_charges');
            $data['charges_type'] = $this->input->post('charges_type');
            $data['validity_in_month'] = $this->input->post('validity');
            $variable_charge_detail = $this->accounting_model->get_vendor_partner_variable_charges("id", array('charges_type'=>$this->input->post('charges_type'), 'entity_type'=>_247AROUND_PARTNER_STRING, 'entity_id'=>$this->input->post('partner_id')));
            if((!empty($variable_charge_detail && $variable_charge_detail[0]['id'] == $this->input->post('variable_charges_id'))) || empty($variable_charge_detail)){
                if(!empty($this->input->post('variable_charges_id')) && $this->input->post('variable_charges_id') > 0){
                   $data['update_date'] = date("Y-m-d H:i:s");
                   $result = $this->invoices_model->update_into_variable_charge(array('id'=>$this->input->post('variable_charges_id')), $data); 
                   $this->session->set_userdata('success', 'Data Updated Successfully');
                }else{
                   $data['create_date'] = date("Y-m-d H:i:s");
                   $result = $this->invoices_model->insert_into_variable_charge($data);
                   $this->session->set_userdata('success', 'Data Entered Successfully');
                }
                if($result){
                    $this->session->set_userdata('success', 'Data Saved Successfully');
                    redirect(base_url() . 'employee/partner/editpartner/' . $this->input->post('partner_id'));
                } else {
                    $this->session->set_userdata('error', 'Data can not be inserted. Please Try Again...');
                    redirect(base_url() . 'employee/partner/editpartner/' . $this->input->post('partner_id'));
                }
            }
            else{
                $this->session->set_userdata('error', 'Charge Type Already Exist.');
                redirect(base_url() . 'employee/partner/editpartner/' . $this->input->post('partner_id'));
            }
    }
    
    /*
    * @desc - This function is used to update partner's variable charges
    * @param - $partner_id, $fixed_charges, $charges_type, $validity     
    * @return - boolean
    */
    function edit_partner_variable_charges(){
        $data = array();
        $data['entity_type'] = _247AROUND_PARTNER_STRING;
        $data['entity_id'] = $this->input->post('partner_id');
        $data['fixed_charges'] = $this->input->post('fixed_charges');
        $data['charges_type'] = $this->input->post('charges_type');
        $data['validity_in_month'] = $this->input->post('validity');
        $data['update_date'] = date("Y-m-d H:i:s");
        if(!empty($this->input->post('variable_charges_id')) && $this->input->post('variable_charges_id') > 0){
            $result = $this->invoices_model->update_into_variable_charge(array('id'=>$this->input->post('variable_charges_id')), $data); 
        }else{
            echo false;
        }
        if($result){
            echo true;
        } else {
            echo false;
        }
    }
    
    /*
     * @desc - This function is used to download buyback summary report
     * $daterange
     * CSV
     */
    function download_buyback_summary_report(){
        ob_start();
        $daterange = explode("-", $this->input->post("buyback_daterange"));
        $start_date = date("Y-m-d", strtotime($daterange[0]));
        $end_date = date("Y-m-d", strtotime($daterange[1]));
        $where = array(
            "bb_unit_details.cp_invoice_id IS NOT NULL" => NULL,
            "vendor_partner_invoices.invoice_date  >=  '".$start_date."'" => NULL,    
            "vendor_partner_invoices.invoice_date  <=  '".$end_date."'" => NULL, 
        );
        $join = array(
            "bb_order_details" => "bb_order_details.partner_order_id = bb_unit_details.partner_order_id",
            "vendor_partner_invoices" => "bb_unit_details.cp_invoice_id = vendor_partner_invoices.invoice_id",
            "services" => "bb_unit_details.service_id = services.id",
        );
        $select = "bb_unit_details.partner_id, bb_unit_details.partner_order_id, services.services,"
                . "bb_unit_details.partner_basic_charge, bb_unit_details.partner_tax_charge, bb_unit_details.cp_basic_charge,"
                . "bb_unit_details.cp_tax_charge, bb_unit_details.cp_claimed_price, bb_unit_details.cp_invoice_id,"
                . "vendor_partner_invoices.invoice_date, vendor_partner_invoices.from_date, vendor_partner_invoices.to_date";
        $data =  $this->bb_model->get_bb_detail($select, $where, $join);
        $headings = array("partner_id", "partner_order_id", "service", "partner_basic_charge", "partner_tax_charge", "cp_basic_charge", "cp_tax_charge", "cp_claimed_price", "cp_invoice_id", "invoice_date", "from_date", "to_date");
        $this->miscelleneous->downloadCSV($data,$headings,"buyback_summary_report");  
    }
    
    /*
    * @desc - This function is used to activate and deactivate variable charges
    * @param - $status, $variable_charge_id     
    * @return - json
    */
    function active_deactive_variable_charges(){
        $return = array();
        $data['status'] = $this->input->post("status");
        $data['update_date'] = date("Y-m-d H:i:s");
        $result = $this->invoices_model->update_into_variable_charge(array('id'=>$this->input->post('variable_charge_id')), $data); 
        if($result){
            $return["status"] = true;
            $return["message"] = "Status Changed Successfully";
        }
        else{
            $return["status"] = false;
            $return["message"] = "Error Occured while Updating Status";
        }
        echo json_encode($return);
    }
}
