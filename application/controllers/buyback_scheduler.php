<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 360000);

class buyback_scheduler extends CI_Controller {
    function __Construct() {
        parent::__Construct();
        $this->load->model('bb_model');
        $this->load->model('booking_model');
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
    
    // This Function is used to send Buyback not delivered orders mail. 
    // It Contains Following Orders
    // 1) Not delivered orders.
    // 2) Previous day from current day.
    function send_not_delivered_orders_list() {
        
        $data = $this->bb_model->get_not_delivered_orders_list();
        
        // generate data in table format.
        $table = '<table border="1" style="border-collapse:collapse">';
        $table .= '<thead><tr>
                    <th style="text-align:left;">Order ID</th>
                    <th style="text-align:left;">Tracking ID</th>
                    <th style="text-align:left;">Appliance</th>
                    <th style="text-align:left;">Category/Size</th>
                    <th style="text-align:left;">City</th>
                    <th style="text-align:left;">Order Date</th>
                    <th style="text-align:left;">Delivery Date</th>
                    <th style="text-align:left;">Current Status</th>
                    <th style="text-align:left;">Exchange Price</th>
            </tr></thead>';
        
        if(empty($data)) {
            $table .= '<tr><td colspan="9">No data found.</td></tr>';
        } else {
            foreach($data as $d) {
                $table .= '<tr>';
                $table .= '<td>'.$d['partner_order_id'].'</td>';
                $table .= '<td>'.$d['partner_tracking_id'].'</td>';
                $table .= '<td>'.$d['services'].'</td>';
                $table .= '<td>'.$d['category'].'</td>';
                $table .= '<td>'.$d['city'].'</td>';
                $table .= '<td>'.$d['order_date'].'</td>';
                $table .= '<td>'.$d['delivery_date'].'</td>';
                $table .= '<td>'.$d['current_status'].'</td>';
                $table .= '<td>'.$d['exchange_price'].'</td>';
                $table .= '</tr>';
            }
        }
        
        $table .= '</table>';
        
        $email_template = $this->booking_model->get_booking_email_template(NOT_DELIVERED_BB_ORDERS);
        
        // prepare mail
        $to = $email_template[1];
        $from = $email_template[2];
        $cc = $email_template[3];
        $subject = 'Delivered Not Received orders reporting - '.date('d-M-Y', strtotime('-1 day', strtotime(date('Y-m-d'))));
        
        $body = '<p>Hi Team,<br />
                Please find below the Delivered Not Received orders reported on '.date('d-M-Y', strtotime('-1 day', strtotime(date('Y-m-d')))).' for your immediate action:</p><br /><br />'.$table;
        
        $this->notify->sendEmail($from, $to, $cc, NULL, $subject, $body, NULL, NULL);
    }
}
