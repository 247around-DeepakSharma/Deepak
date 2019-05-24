<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Paytm_gateway extends CI_Controller {

    function __Construct() {
        parent::__Construct();
        
        //load model
        $this->load->model('partner_model');
        $this->load->model('invoices_model');
        $this->load->model('booking_model');
        
        //load library
        $this->load->library('paytmlib/encdec_paytm');
        $this->load->library('miscelleneous');
        $this->load->library('notify');
        $this->load->library('asynchronous_lib');
    }
    
    /**
     * @desc: This function is used to process the payment gateway transaction 
     * @params: void
     * @return: void
     */
    function process_paytm_transaction() {
          
        header("Pragma: no-cache");
        header("Cache-Control: no-cache");
        header("Expires: 0");

        $param_list = array();

        $ORDER_ID = $this->input->post('ORDER_ID');
        $CUST_ID = $this->input->post('CUST_ID');
        $INDUSTRY_TYPE_ID = $this->input->post('INDUSTRY_TYPE_ID');
        $CHANNEL_ID = $this->input->post('CHANNEL_ID');
        $TXN_AMOUNT = $this->input->post('TXN_AMOUNT');

        // Create an array having all required parameters for creating checksum.
        $param_list["MID"] = PAYTM_GATEWAY_MERCHANT_MID;
        $param_list["ORDER_ID"] = $ORDER_ID;
        $param_list["CUST_ID"] = $CUST_ID;
        $param_list["INDUSTRY_TYPE_ID"] = $INDUSTRY_TYPE_ID;
        $param_list["CHANNEL_ID"] = $CHANNEL_ID;
        $param_list["TXN_AMOUNT"] = $TXN_AMOUNT;
        //$param_list["TXN_AMOUNT"] = 1;
        $param_list["WEBSITE"] = PAYTM_GATEWAY_MERCHANT_WEBSITE;
        $param_list["CALLBACK_URL"] = PAYTM_GATEWAY_CALLBACK_URL;
        $param_list['ORDER_DETAILS'] = $ORDER_ID." ".$TXN_AMOUNT;

        /*
          $param_list["MSISDN"] = $MSISDN; //Mobile number of customer
          $param_list["EMAIL"] = $EMAIL; //Email ID of customer
          $param_list["VERIFIED_BY"] = "EMAIL"; //
          $param_list["IS_USER_VERIFIED"] = "YES"; //

         */
        //Here checksum string will return by getChecksumFromArray() function.
        $check_sum = $this->encdec_paytm->getChecksumFromArray($param_list, PAYTM_GATEWAY_MERCHANT_KEY);
        echo "<html>
		<head>
		<title>Merchant Check Out Page</title>
		</head>
		<body>
		    <center><h1>Please do not refresh this page...</h1></center>
			<form method='post' action='" . PAYTM_GATEWAY_TXN_URL . "' name='f1'>
		<table border='1'>
		 <tbody>";

        foreach ($param_list as $name => $value) {
            echo '<input type="hidden" name="' . $name . '" value="' . $value . '">';
        }

        echo "<input type='hidden' name='CHECKSUMHASH' value='" . $check_sum . "'>
		 </tbody>
		</table>
		<script type='text/javascript'>
		 document.f1.submit();
		</script>
		</form>
		</body>
		</html>";
    }
    
    
    /**
     * @desc: This function is used to process the payment gateway response send from the paytm
     * after completion of the transaction. 
     * @params: void
     * @return: void
     */
    function paytm_response() {
        header("Pragma: no-cache");
        header("Cache-Control: no-cache");
        header("Expires: 0");

        $param_list = $this->input->post();
        $paytm_check_sum = isset($_POST["CHECKSUMHASH"]) ? $_POST["CHECKSUMHASH"] : ""; //Sent by Paytm pg
        //Verify all parameters received from Paytm pg to your application.
        // Like MID received from paytm pg is same as your application’s MID, 
        // TXN_AMOUNT and ORDER_ID are same as what was sent by you to Paytm PG for initiating transaction etc.
        $is_valid_checksum = $this->encdec_paytm->verifychecksum_e($param_list, PAYTM_GATEWAY_MERCHANT_KEY, $paytm_check_sum); //will return TRUE or FALSE string.
        if ($is_valid_checksum == "TRUE") {
            if ($this->input->post("STATUS") == "TXN_SUCCESS") {
                //Process your transaction here as success transaction.
                //Verify amount & order id received from Payment gateway with your application's order id and amount.
                $transaction_status = $this->check_transaction_status($param_list);
                if($transaction_status['STATUS'] === $param_list['STATUS']){
                    if($transaction_status['TXNAMOUNT'] === $this->input->post('TXNAMOUNT')){
                        log_message("info"," payment details verified");
                        $transaction_status['is_txn_successfull'] =  1;
                    }else{
                        log_message("info"," payment details verified but payment amount is different");
                        $transaction_status['is_txn_successfull'] =  0;
                    }
                    
                }else{
                    log_message("info"," payment details not verified");
                    $transaction_status['is_txn_successfull'] =  0;
                }
            } else {
                $transaction_status['final_txn_status'] = $this->input->post("STATUS");
                $transaction_status['final_response_msg'] = $this->input->post("RESPMSG");
                $transaction_status['is_txn_successfull'] =  0;
            }
        } else {
            //Process transaction as suspicious.
            $transaction_status['final_txn_status'] = 'Falied';
            $transaction_status['final_response_msg'] = 'Process transaction as suspicious';
            $transaction_status['is_txn_successfull'] =  0;
        }
        
        $insert_data = array(
            'order_id' => $param_list['ORDERID'],
            'gw_txn_id' => $param_list['TXNID'],
            'txn_amount' => $param_list['TXNAMOUNT'],
            'gw_txn_status' => $param_list['STATUS'],
            'gw_response_code' => $param_list['RESPCODE'],
            'gw_response_msg' => $param_list['RESPMSG'],
            'final_txn_status' => isset($transaction_status['final_txn_status'])? $transaction_status['final_txn_status']: $transaction_status['STATUS'],
            'final_response_code' => isset($transaction_status['final_response_code'])?$transaction_status['final_response_code']:'',
            'final_response_msg' => isset($transaction_status['final_response_msg'])?$transaction_status['final_response_msg']:$transaction_status['RESPMSG'],
            'bank_txn_id' => isset($param_list['BANKTXNID'])?$param_list['BANKTXNID']:NULL,
            'payment_mode' => isset($param_list['PAYMENTMODE'])?$param_list['PAYMENTMODE']:NULL,
            'bank_name' => isset($param_list['BANKNAME'])?$param_list['BANKNAME']:'',
            'gw_name' => isset($param_list['GATEWAYNAME'])?$param_list['GATEWAYNAME']:NULL,
            'txn_date' => isset($param_list['TXNDATE'])?$param_list['TXNDATE']:NULL,
            'order_details' => isset($param_list['ORDER_DETAILS'])?$param_list['ORDER_DETAILS']:NULL,
            'contact_number' => isset($param_list['MOBILE_NO'])?$param_list['MOBILE_NO']:NULL,
            'email' => isset($param_list['EMAIL'])?$param_list['EMAIL']:NULL,
            'create_date' => date('Y-m-d-h-i-s')
        );
        
        $insert_data['is_txn_successfull'] = $transaction_status['is_txn_successfull'];
        $insert_id = $this->partner_model->insert_paytm_payment_details($insert_data);
        
        if($insert_id){
            log_message("info",__METHOD__." Payment has been completed successfully"); 
            $partner_id = $this->session->userdata('partner_id');
            if(!empty($partner_id) && $transaction_status['is_txn_successfull'] == 1){
                $this->generate_partner_payment_invoice($partner_id,$param_list, $insert_id);
            }
            $this->send_transaction_email($insert_data);
        }else{
            log_message("info",__METHOD__." Error in processing payment. ". print_r($insert_data));
        }
        
        
        $this->session->set_userdata("query",$insert_data);
        redirect(base_url().'payment/confirmation');
    }
    
    /**
     * @desc: This function is used to check the payment gateway response send from the paytm
     * after completion of the transaction. It checks that order id send from our side is same with order id send from the paytm.
     * @params: array $param_list
     * @return: array $response_param_list
     */
    function check_transaction_status($param_list) {
        header("Pragma: no-cache");
        header("Cache-Control: no-cache");
        header("Expires: 0");
        $ORDER_ID = "";
        $request_param_list = array();
        $response_param_list = array();
        if (isset($param_list["ORDERID"]) && $param_list["ORDERID"] != "") {
            // In Test Page, we are taking parameters from POST request. In actual implementation these can be collected from session or DB. 
            $ORDER_ID = $param_list["ORDERID"];
            // Create an array having all required parameters for status query.
            $request_param_list = array("MID" => PAYTM_GATEWAY_MERCHANT_MID, "ORDERID" => $ORDER_ID);

            $StatusCheckSum = $this->encdec_paytm->getChecksumFromArray($request_param_list, PAYTM_GATEWAY_MERCHANT_KEY);
            $request_param_list['CHECKSUMHASH'] = $StatusCheckSum;
            // Call the PG's getTxnStatusNew() function for verifying the transaction status.
            $response_param_list = $this->encdec_paytm->getTxnStatus($request_param_list);
        }else{
            $response_param_list['STATUS'] = 'Falied';
            $response_param_list['RESPMSG'] = "Order Id not found";
        }
        
        return $response_param_list;
    }
    
    /**
     * @desc: This function is used to get the partner current amount details and GST type
     * @params: void
     * @return: array $res
     */
    function get_partner_amount_details(){
        $partner_id = trim($this->input->post('partner_id'));
        if($partner_id){
            $partner_details = $this->partner_model->getpartner_details('state,agreement_end_date',array('partners.id' => $partner_id));
            if(!empty($partner_details[0]['state'])){
                $is_c_s_gst = $this->invoices_model->check_gst_tax_type($partner_details[0]['state']);
                $partner_amount = $this->miscelleneous->get_partner_prepaid_amount($partner_id,TRUE);
                $res['status'] = 'success';
                $res['data']['agreement_end_date'] = $partner_details[0]['agreement_end_date'];
                $res['data']['is_c_s_gst'] = $is_c_s_gst;
                $res['data']['amount_details'] = $partner_amount;
            }else{
                $res['status'] = 'error';
                $res['data']['msg'] = "Partner details not found";
            }
        }else{
            $res['status'] = 'error';
            $res['data']['msg'] = "Partner can not be empty";
        }
        
        echo json_encode($res);
    }
    
    /**
     * @desc: This function is used to show the confirmation page after the completion of the transaction
     * @params: void
     * @return: void
     */
    function show_payment_confirmation(){
        
        $data = $this->session->userdata('query');
        
        //check if page is refresh or not. If page refreshed then don't send response back to paytm.
        if(!empty($data)){
            //check if payment completed by partner or customer
            if($this->session->userdata('partner_id')){
                log_message("info", "Payment done by Partner");
                $this->miscelleneous->load_partner_nav_header();
                //$this->load->view('partner/header');
                $this->load->view('paytm_gateway/payment_confirmation_details',$data);
                $this->load->view('partner/partner_footer');
            }else if($this->session->userdata('payment_link_id')){
                log_message("info", "Payment done by customer");
                $data['payment_link_id'] = $this->session->userdata('payment_link_id');
                $this->load->view('paytm_gateway/payment_confirmation_details',$data);
            }else{
                redirect(base_url().'partner/home');
            }
        }else{
            $data['invalid_data'] = array();
            if($this->session->userdata('partner_id')){
                //$this->load->view('partner/header');
                $this->miscelleneous->load_partner_nav_header();
                $this->load->view('paytm_gateway/payment_confirmation_details',$data);
                $this->load->view('partner/partner_footer');
            }else{
                $this->load->view('paytm_gateway/payment_confirmation_details',$data);
            }
        }
    }
    
    /**
     * @desc: This function is used to send email and message after the completion of the transaction
     * @params: array $data
     * @return: void
     */
    function send_transaction_email($data) {
        
        $partner_id = $this->session->userdata('partner_id');
        $am_email = "";
        $partner_email = '';
        $payer_name = '';
        if(!empty($partner_id)){
            //$partner_details = $this->partner_model->getpartner_details('public_name,owner_email,primary_contact_email,account_manager_id',array('partners.id' => $partner_id));
            $partner_details = $this->partner_model->getpartner_data("public_name,owner_email,primary_contact_email,group_concat(distinct agent_filters.agent_id) as account_manager_id", 
                        array('partners.id' => $partner_id, 'agent_filters.entity_type' => "247around"),"",0,1,1,"partners.id");
            if (!empty($partner_details[0]['account_manager_id'])) {
                //$am_email = $this->employee_model->getemployeefromid($partner_details[0]['account_manager_id'])[0]['official_email'];
                $am_email = $this->employee_model->getemployeeMailFromID($partner_details[0]['account_manager_id'])[0]['official_email'];
            }
            
            $partner_email = $partner_details[0]['owner_email'];
            $payer_name = $partner_details[0]['public_name'];
        }else{
            $update_payment_link_details = $this->booking_model->update_payment_link_details($this->session->userdata('payment_link_id'),array('status' => 1));
            
            if($update_payment_link_details){
                log_message("info","Payment link details updated successfully");
            }else{
                log_message("info","Error in updating payment link details");
            }
        }
        
        
        if($this->session->userdata('user_contact_number')){
            $customer_phone_number = $this->session->userdata('user_contact_number');
            
            if(!empty($customer_phone_number)){
                $sms['tag'] = "gateway_payment_confirmation_sms";
                $sms['phone_no'] = $customer_phone_number;
                $sms['smsData']['link'] = $data['txn_amount'];
                $sms['booking_id'] = '';
                $sms['type'] = "user";
                $sms['type_id'] = $this->session->userdata('payment_link_id');

                $this->notify->send_sms_msg91($sms);
            }
        }
        
        if($this->session->userdata('user_email')){
            $to = $this->session->userdata('user_email');
        }else if(!empty ($partner_id)){
            $to = $partner_email;
        }else{
            $to = NITS_ANUJ_EMAIL_ID;
        }
        
        $email_template = $this->booking_model->get_booking_email_template("payment_transaction_email");
        if ($data['is_txn_successfull']) {
            switch ($data['final_txn_status']) {
                case 'TXN_SUCCESS':
                    $subject_text = "Payment Successful";
                    break;
                case 'TXN_FAILURE':
                    $subject_text = "Payment Failed";
                    break;
                case 'PENDING':
                case 'OPEN':
                    $subject_text = "Payment Pending";
                    break;
            }
        }else{
            $subject_text = "Payment Failed";
        }   
        
        $bcc = $email_template[3].','.$am_email.','.ACCOUNTANT_EMAILID;
        $subject = vsprintf($email_template[4], $subject_text);
        $data['payer_name'] = $payer_name;
        $email_body = $this->load->view('paytm_gateway/transaction_email_template',$data,TRUE);

        $sendmail = $this->notify->sendEmail($email_template[2], $to, "", $bcc, $subject, $email_body, "",'payment_transaction_email');
        
        if ($sendmail) {
            log_message('info', __FUNCTION__ . 'Payment transaction email send successfully');
        } else {
            log_message('info', __FUNCTION__ . 'Error in Sending Payment transaction email ');
        }
    }
    
    /**
     * @desc: This function is used to process the payment which was request from the custom created link
     * @params: string $hash_key
     * @return: void
     */
    function process_gateway_booking_payment($hash_key){
        
        $data = $this->booking_model->get_payment_link_details('*',array('hash_key' => md5($hash_key)));
        
        if(!empty($data)){
            if(empty($data[0]['status'])){
                $random_number = substr(mt_rand(), 1,6);
                $param_list['ORDER_ID'] = $random_number."_".date('Ymdhis');
                $param_list["CUST_ID"] = $data[0]['customer_id'];
                $param_list["INDUSTRY_TYPE_ID"] = PAYTM_GATEWAY_INDUSTRY_TYPE_ID;
                $param_list["CHANNEL_ID"] = PAYTM_GATEWAY_CHANNEL_ID;
                $param_list["TXN_AMOUNT"] = $data[0]['amount'];
                //$param_list["TXN_AMOUNT"] = 1;
                if(!empty($data[0]['phone_number'])){
                    $param_list["MSISDN"] = $data[0]['phone_number']; 
                    $this->session->set_userdata('user_contact_number',$param_list["MSISDN"]);
                }
                if(!empty($data[0]['email'])){
                    $param_list["EMAIL"] = $data[0]['email'];
                    $this->session->set_userdata('user_email',$param_list["EMAIL"]);
                }
                $this->session->set_userdata('payment_link_id',$data[0]['id']);
                echo "<html>
                    <head>
                    <title>Payment Processing</title>
                    </head>
                    <body>
                        <center><h1>Please do not refresh this page...</h1></center>
                            <form method='post' action='" . base_url().'payment/checkout' . "' name='f1'>
                    <table border='1'>
                     <tbody>";

                    foreach ($param_list as $name => $value) {
                        echo '<input type="hidden" name="' . $name . '" value="' . $value . '">';
                    }

                echo "</tbody>
                        </table>
                        <script type='text/javascript'>
                         document.f1.submit();
                        </script>
                        </form>
                        </body>
                        </html>";
            }else{
                echo "<h1>Invalid Payment</h1>";
                echo "<br> Something went wrong.";
                echo "For more information. you can contact 247around Team";
            }
        }else{
            echo "<h1>Invalid Key</h1>";
            echo "<br> The key in your link is not a valid key.";
            echo "For more information. you can contact 247around Team";
        }
    }
    
    function generate_partner_payment_invoice($partner_id, $param_list, $TXNID){
        log_message("info", __METHOD__. " Partner Id ". $partner_id, " Response ". json_encode($param_list, true));
        $postData = array(
            "partner_vendor" => "partner",
            "partner_vendor_id" => $partner_id,
            "credit_debit" => "Credit",
            "bankname" => isset($param_list['BANKNAME'])?$param_list['BANKNAME']:NULL,
            "transaction_date" => date('Y-m-d'),
            "tds_amount" => 0,
            "amount" => $param_list['TXNAMOUNT'],
            "transaction_mode" => isset($param_list['PAYMENTMODE'])?$param_list['PAYMENTMODE']:NULL,
            "description" => isset($param_list['ORDER_DETAILS'])?$param_list['ORDER_DETAILS']:'',
            'tdate' =>  isset($param_list['TXNDATE'])?$param_list['TXNDATE']:date('Y-m-d'),
            'transaction_id' => $param_list['TXNID'],
            'payment_txn_id' => $TXNID
        );

        
        $url = base_url() . "employee/invoice/paytm_gateway_payment/"._247AROUND_DEFAULT_AGENT;

        $this->asynchronous_lib->do_background_process($url, $postData);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
