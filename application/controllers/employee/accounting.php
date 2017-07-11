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
     * @desc: This Function is used to show the challan upload form
     * @param: void
     * @return : void
     */
    function get_challan_upload_form() {
        $this->load->view('employee/header/' . $this->session->userdata('user_group'));
        $this->load->view('employee/upload_challan_details');
    }

    /**
     * @desc: This Function is used to show the challan EDIT form
     * @param: string
     * @return : void
     */
    function get_challan_edit_form($challan_id = "") {
        $data['challan_data'] = $this->accounting_model->fetch_challan_details('', $challan_id);
        $this->load->view('employee/header/' . $this->session->userdata('user_group'));
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
                $annexure_file = trim($this->input->post('cin_no')). '_annexure_file_' . substr(md5(uniqid(rand(0, 9))), 0, 15) . "." . explode(".", $_FILES['annexure_file']['name'])[1];
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
        $this->form_validation->set_rules('challan_type', 'Challan Type', 'required|trim|xss_clean');
        $this->form_validation->set_rules('serial_no', 'Serial Number', 'required|trim|xss_clean');
        $this->form_validation->set_rules('cin_no', 'CIN Number', 'required|trim|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount', 'required|trim|xss_clean');
        $this->form_validation->set_rules('bank_name', 'Bank Name', 'required|trim|xss_clean');
        $this->form_validation->set_rules('paid_by', 'Paid By', 'required|trim|xss_clean');
        $this->form_validation->set_rules('daterange', 'DateRange', 'required|trim|xss_clean');
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

        $this->load->view('employee/header/' . $this->session->userdata('user_group'));
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
            $existing_invoice_id = $this->invoices_model->get_invoices_details(1);
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
                
                if(!empty($invoice_data['exist_invoices_data'])) {
                    //insert data into database in batch
                    $insert_id = $this->accounting_model->insert_invoice_challan_id_mapping_data($invoice_data['exist_invoices_data']);
                    if($insert_id) {
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
                $msg .= "and these Invoices are not exist in the table : " . $non_existing_invoices_data;
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
        $this->load->view('employee/header/' . $this->session->userdata('user_group'));
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
        $data['report_data'] = $this->accounting_model->get_payment_report_data($payment_type, $from_date, $new_to_date, $partner_vendor,$is_challan_data,$report_type);
        //echo $this->db->last_query();exit();
        if($data['report_data']){
            $data['partner_vendor'] = $partner_vendor;
            $data['payment_type'] = $payment_type;
            $data['report_type'] = $report_type;
            echo $this->load->view('employee/paymnet_history_table_view.php', $data);
        }else{
            echo "error";
        }
    }

    /**
     * @desc: This Function is used to show the view for search the invoice Id 
     * @param: void
     * @return : void
     */
    function show_search_invoice_id_view() {
        $this->load->view('employee/header/' . $this->session->userdata('user_group'));
        $this->load->view('employee/search_invoice_id');
    }

    /**
     * @desc: This Function is used to search the invoice Id 
     * @param: void
     * @return : view
     */
    function search_invoice_id() {
        $invoice_id = trim($this->input->post('invoice_id'));
        $request_data = array('invoice_id' => $invoice_id);
        $data['invoiceid_data'] = $this->invoices_model->getInvoicingData($request_data);
        if (!empty($data['invoiceid_data'])) {
            if ($data['invoiceid_data'][0]['vendor_partner'] == 'vendor') {
                $data['vendor_name'] = $this->vendor_model->getVendorContact($data['invoiceid_data'][0]['vendor_partner_id']);
            } else if ($data['invoiceid_data'][0]['vendor_partner'] == 'partner') {
                $data['partner_name'] = $this->partner_model->getpartner($data['invoiceid_data'][0]['vendor_partner_id']);
            }
            echo $this->load->view('employee/invoiceid_details_data_table', $data);
        } else {
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
                        $update = $this->accounting_model->untag_challan_invoice_id($challan_id_value,$value);
                        if ($update) {
                            log_message('info', __METHOD__ . " : Invoice ID corresponding to challan ID = $challan_id_value updated successfully");
                        } else {
                            $non_updated_invoices .= $value.',';
                            log_message('info', __METHOD__ . " : Error in updating Invoice ID corresponding to challan ID = $challan_id_value");
                        }
                        
                    }
                }
            }
            
            if(!empty($non_updated_invoices)){
                $msg = "Some invoice id successfully untag from challan id";
                $msg .= " and these invoices are not updated : ". $non_updated_invoices;
                
                $this->session->set_flashdata('error_msg', $msg);
                redirect(base_url() . 'employee/accounting/get_challan_details');
            }else{
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
        $this->load->view('employee/header/' . $this->session->userdata('user_group'));
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
    function get_tagged_incoice_challan_data($challan_id){
        $data['tagged_invoice_data'] = $this->accounting_model->get_tagged_invoice_challan_data($challan_id);
        if(!empty($data['tagged_invoice_data'])){
            $this->load->view('employee/header/' . $this->session->userdata('user_group'));
            $this->load->view('employee/show_tagged_invoices_challan_data',$data);
        }else{
            "No Invoices tagged with this data";
        }
    }

}
