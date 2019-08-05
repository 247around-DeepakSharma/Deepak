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
    
    // This Function is used to send Buyback not delivered orders mail. 
    // It Contains Following Orders
    // 1) Not delivered orders.
    // 2) Previous day from current day.
    function send_not_delivered_orders_list() {
        
        $sql = "SELECT 
                    bb_order_details.partner_order_id,
                    bb_order_details.partner_tracking_id,
                    services.services,
                    bb_unit_details.category,
                    bb_order_details.city,
                    bb_order_details.order_date,
                    bb_order_details.delivery_date,
                    bb_cp_order_action.current_status,
                    (bb_unit_details.partner_basic_charge + bb_unit_details.partner_tax_charge) as exchange_price
                FROM
                    bb_order_details
                    LEFT JOIN bb_unit_details ON (bb_order_details.partner_order_id = bb_unit_details.partner_order_id)
                    LEFT JOIN services ON (bb_unit_details.service_id = services.id)
                    LEFT JOIN bb_cp_order_action ON (bb_order_details.partner_order_id = bb_cp_order_action.partner_order_id)
                WHERE
                    bb_cp_order_action.current_status = 'Not Delivered'	
                    AND date(bb_cp_order_action.acknowledge_date) = (CURDATE() - INTERVAL 1 DAY)";
        
        $data = $this->db->query($sql)->result_array();
        
        // generate data in table format.
        $table = '<table>';
        $table .= '<thead><tr>
                    <th>Order ID</th>
                    <th>Tracking ID</th>
                    <th>Appliance</th>
                    <th>Category/Size</th>
                    <th>City</th>
                    <th>Order Date</th>
                    <th>Delivery Date</th>
                    <th>Current Status</th>
                    <th>Exchange Price</th>
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
        
        // prepare mail
        $from = 'sunilk@247around.com';
        $to = 'kmardee@amazon.com,ybhargav@amazon.com';
        $cc = 'sunilk@247around.com';
        $subject = 'Delivered Not Received orders reporting - '.date('d-M-Y', strtotime('-1 day', strtotime(date('Y-m-d'))));
        
        $body = '<p>Hi Team,<br />
                Please find below the Delivered Not Received orders reported on '.date('d-M-Y', strtotime('-1 day', strtotime(date('Y-m-d')))).' for your immediate action:</p><br /><br />'.$table;
        
        $this->notify->sendEmail($from, $to, $cc, NULL, $subject, $body, NULL, NULL);
    }
}
