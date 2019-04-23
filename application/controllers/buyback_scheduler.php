<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 360000);

class buyback_scheduler extends CI_Controller {
    function __Construct() {
        parent::__Construct();
        $this->load->library("buyback");
        $this->load->library("notify");
    }
    // This Function is used to send Buyback Disputed amount Summary 
    // It Contains Following Orders
    // 1) Without Invoiced Without Reimbursement 
    // 2) Invoiced to CP on claimed Price, but did not get Reimbursement
    // 3) Orders On review Page
    
    function send_buyback_disputed_amount_summary(){
       $data['without_invoiced']['data']= json_decode($this->buyback->get_orders_without_invoices_and_without_reimbursement());
       $data['with_claim']['data'] =  json_decode($this->buyback->get_orders_with_cp_invoice_and_without_reimbursement());
       $data['review_page']['data'] =  json_decode($this->buyback->get_review_page_orders());
       $data['without_invoiced']['Title'] = 'Without Invoiced Without Reimbursement';
       $data['with_claim']['Title'] = 'Invoiced to CP on Claimed Prices And Without Reimbursement';
       $data['review_page']['Title'] = 'Review Page Orders';
       $tempMessage = $this->load->view('buyback/buyback_disputed_summary_email_template',array('data'=>$data),true);
       $email_template = $this->booking_model->get_booking_email_template(BUYBACK_DISPUTED_ORDERS_SUMMARY);
                if(!empty($email_template)){
                    $subject = $email_template[4];
                    $message = vsprintf($email_template[0], array($tempMessage)); 
                    $email_from = $email_template[2];
                    $to = $email_template[1];
                    $cc = $email_template[3];
                    $bcc = $email_template[5];
                    $this->notify->sendEmail($email_from, $to, $cc, $bcc, $subject, $message, '', BUYBACK_DISPUTED_ORDERS_SUMMARY);
                }
    }
}
