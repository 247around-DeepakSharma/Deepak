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
        $this->load->model('booking_model');
        $this->load->library('miscelleneous');
        $this->load->library("notify");
        $this->load->library('PHPReport');
        $this->load->library('form_validation');
        $this->load->library("session");
        $this->load->library('s3');
        //  $this->load->library('upload');
        //  $this->load->library('email');

//        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
//            return TRUE;
//        } else {
//            echo PHP_EOL . 'Terminal Access Not Allowed' . PHP_EOL;
//            redirect(base_url() . "employee/login");
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
     * @desc This is used to generate Data table row
     * @param Array $invoice_list
     * @param int $no
     * @return Array
     */
    function invoice_datatable($invoice_list, $no){
        $row = array();
        $row[] = $no;
        $row[] = "<a href='". base_url()."employee/invoice/invoice_summary/".$invoice_list->vendor_partner."/".$invoice_list->vendor_partner_id."' target='_blank'>".$invoice_list->party_name."</a>";
        $row[] = $invoice_list->invoice_id;
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
        $a_update = '<a href="'.base_url().'employee/invoice/insert_update_invoice/'.$invoice_list->vendor_partner.'/'.$invoice_list->invoice_id.'"';
        if($invoice_list->amount_paid > 0){
            $a_update .= " disabled ";
        }
        $a_update .= ' class="btn btn-sm btn-info">update</a> ';
        $row[] = $a_update;
        
        $resend_invoice = '<a href="'.base_url().'employee/invoice/sendInvoiceMail/'.$invoice_list->invoice_id.'" class="btn btn-sm btn-primary">Resend Invoice</a>';
        $row[] = $resend_invoice;
        if(($invoice_list->type == "DebitNote") && $invoice_list->credit_generated == 0){
            $row[] = '<a target="_blank" href="'.base_url().'employee/invoice/generate_gst_creditnote/'.$invoice_list->invoice_id.'" class="btn btn-sm btn-success"> Generate</a>';
        } else {
            $row[] = "";
        }
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
        $post['search_value'] = trim($search['value']);
        
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
        log_message("info", __METHOD__);
        $post = $this->getPaymentSummaryDataTablePost();
        $post['column_order'] = array( NULL, 'bank_transactions.id');
        $post['column_search'] = array('description', 'credit_amount', 'debit_amount', 'invoice_id');
        $data = array();
        
        switch ($this->input->post('request_type')){
            case 'sf_bank_transaction':
                $data = $this->getServiceCenterBankTransactionData($post);
                break;
            case 'admin_search':
                $data = $this->getAdminBankTransactionData($post);
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
        $post['search_value'] = trim($search['value']);
        
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
        
        $vendor_partner = $this->input->post("vendor_partner");
        $vendor_partner_id = $this->input->post("vendor_partner_id");
        $transaction_date = $this->input->post("transaction_date");
        $transaction_period = $this->input->post("transaction_period_date");
       
        if(!empty($vendor_partner)){
            $post['where']['partner_vendor'] = $vendor_partner;
        }
        if(!empty($vendor_partner_id)){
            if($vendor_partner_id != "All"){
                $post['where']['partner_vendor_id'] = $vendor_partner_id;
            } 
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
        //log_message("info",__METHOD__);
        $row = array();
        if($order_list->settle_amount == 1){
            $row[] = '<span class="satteled_row">'.$no.'</span>';
        }
        else{
            $row[] = $no;
        }
        $row[] = $order_list->invoice_id;
        $row[] = date("jS M, Y", strtotime($order_list->invoice_date));
        $row[] = date("jS M, Y", strtotime($order_list->from_date)) . " to " . date("jS M, Y", strtotime($order_list->to_date));
        $row[] = $order_list->num_bookings . "/" . $order_list->parts_count;
        $row[] = $order_list->total_amount_collected;
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
     * @desc This is used to show vendor GST Report
     * @param void
     * @return view
     */
    function show_vendor_gst_report(){
        //SELECT company_name, SUM(CASE WHEN from_date <= "2018-03-31" THEN amount_collected_paid ELSE 0 END) as am1,(case when (from_date <= "2018-03-31" ) THEN (sum(amount_collected_paid)) ELSE 0 END) as 'amount' FROM `vendor_partner_invoices`join service_centres on service_centres.id = vendor_partner_invoices.vendor_partner_id WHERE invoice_id like "%AROUND-GST_CN%" and vendor_partner = 'vendor' group by vendor_partner_id
        $data = array();
        $select = 'service_centres.company_name as "name", SUM(CASE WHEN from_date <= "'.date("Y").'-03-31" THEN amount_collected_paid ELSE 0 END) as fy_amount, SUM(CASE WHEN from_date <= "'.date("Y-m-d").'" THEN amount_collected_paid ELSE 0 END) as total_amount';
        $post['group_by'] = 'vendor_partner_id';
        $post['where'] = array('vendor_partner'=>'vendor', 'invoice_id like "%Around-GST-CN%" OR invoice_id like "%Around-GST-DN%"'=>NULL);
        $post['length'] = -1;
        $post['order_by'] = array('total_amount'=>'desc');
        $data['data'] = $this->invoices_model->searchInvoicesdata($select, $post);
        //print_r($data['data']);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/vendor_gst_report', $data); 
    }
    
}
