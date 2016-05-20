<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

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

	$this->load->library('form_validation');
	$this->load->library("session");

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
	$data['service_center'] = $this->invoices_model->getServiceCenter();

	$this->load->view('employee/header');
	$this->load->view('employee/invoice_list', $data);
    }

    /**
     * This is used to get vendor, partner invoicing data by service center id or partner id 
     * and load data in a table.
     */
    function getInvoicingData() {
        $data['source'] = $this->input->post('source');
        $data['vendor_partner_id'] = $this->input->post('vendor_partner_id');
    
        $invoice['invoice_array'] = $this->invoices_model->getInvoicingData($data);
        $invoice['bank_statement'] =$this->invoices_model->bank_statement_details($data);
        $this->load->view('employee/invoicing_table', $invoice);
        
    }

    // Send Invoice pdf file to vendor
    function sendInvoiceMail($invoiceId, $vendor_partnerId, $start_date, $end_date, $vendor_partner) {
	log_message ('info', "Entering: " . __METHOD__);
        $email = $this->input->post('email');
	// download invoice pdf file to local machine
        if($vendor_partner == "vendor"){

          $to = $this->get_to_emailId_for_vendor($vendor_partnerId, $email);

        } else {
            $to = $this->get_to_emailId_for_partner($vendor_partnerId, $email);
        }
        
        log_message ('info', "vendor partner type". print_r($vendor_partner));
        log_message ('info', "vendor partner id". print_r($vendor_partnerId));

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
        
        log_message ('info', "To- EmailId". print_r($to));

        $this->notify->sendEmail("billing@247around.com", $to, $cc, '', $subject, $message, $attachment);
        if($vendor_partner == "vendor"){
            redirect(base_url() . 'employee/invoice', 'refresh');
        } else {
            redirect(base_url() . 'employee/invoice/invoice_partner_view', 'refresh');
        }
    }
    
    /**
     * @desc Load view to select patner to display invoices
     */
    function invoice_partner_view(){
        $data['partner'] = $this->partner_model->get_all_partner_source();
        
        $this->load->view('employee/header');
	$this->load->view('employee/invoice_list', $data);
        
        
    }
    
    /**
     * @desc Get vendor email id from table to send invoice.
     * @param type vendorId
     * @param type email
     * @return string
     */
    function get_to_emailId_for_vendor($vendorId, $email){
        $getEmail = $this->invoices_model->getEmailIdForVendor($vendorId);
        $to ="";
	if (!empty($email)) {
	    $to = $getEmail[0]['primary_contact_email'] . ',' . $getEmail[0]['owner_email'] . ',' . $email;
	} else {
            $to = $getEmail[0]['primary_contact_email'] . ',' . $getEmail[0]['owner_email'];
	}
        
        return $to;
    }
    
    /**
     * Get partner email id from table to send invoice.
     * @param type $partnerId
     * @param type $email
     * @return string
     */
    function get_to_emailId_for_partner($partnerId, $email){
        $getEmail = $this->invoices_model->getEmailIdForPartner($partnerId);
        $to ="";
	if (!empty($email)) {
	    $to = $getEmail[0]['partner_email_for_to'] . ',' . $email;
	} else {
            $to = $getEmail[0]['partner_email_for_to'];
	}
        
        return $to;
    }


     /**
     *  @desc : This function gets bank accounts/statements details
     *  @param : Starting page & number of results per page
     *  @return : pending bookings according to pagination
     */
    function get_add_new_transaction(){
     $this->load->view('employee/header');
     $this->load->view('employee/addnewtransaction');
    }
    
    /**
     *  @desc : This function inserts bank accounts/statements details
     *  @param : Starting page & number of results per page
     *  @return : pending bookings according to pagination
     */
    function post_add_new_transaction(){
        $account_statement['partner_vendor'] = $this->input->post('partner_vendor');
        $account_statement['name'] = $this->input->post('name');
        $account_statement['invoice_id'] = $this->input->post('invoice_id');
        $account_statement['bankname'] = $this->input->post('bankname');
        $account_statement['credit_debit'] = $this->input->post('credit_debit');                        
        $account_statement['transaction_mode'] = $this->input->post('transaction_mode');
        $amount = $this->input->post('amount');
        if($account_statement['credit_debit']=='Credit')
        {
            $account_statement['debit_of'] ='0';
            $account_statement['credit_of'] = $amount;
        }
        else if($account_statement['credit_debit'] =='Debit')
        {
            $account_statement['credit_of'] ='0';
            $account_statement['debit_of'] = $amount;
        }
        $account_statement['transaction_date'] = $this->input->post('tdate');
        $account_statement['description'] = $this->input->post('description');
        //$account_statement['file'] = $this->input->post('file');
        
        $this->invoices_model->bankAccountStatement($account_statement);
        //redirect(base_url() . 'employee/invoice/vendor_details', 'refresh');
        redirect(base_url() . 'employee/invoice/get_bank_account_statement');
    }
    
    function getPartnerOrVendor($par_ven){
        if($par_ven=='Partner'){
            $all_partners= $this->partner_model->get_all_partner_source("null");
            foreach ($all_partners as $p_name) {
            echo "<option>".$p_name['source']."</option>";
            }
        }
        else{
            $all_vendors= $this->vendor_model->getActiveVendor();
            foreach ($all_vendors as $v_name) {
            echo "<option>".$v_name['name']."</option>";
            }
        }
    }
    

}
