<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Payment extends CI_Controller {
    function __Construct() {
        parent::__Construct();
        $this->load->library('paytm_payment_lib');
        $this->load->model('reusable_model');
        $this->load->library('authentication_lib');
        $this->load->library('booking_utilities');
        $this->load->model('booking_model');
    }
    /*
     * This function is used to handle paytm callback after transaction against any qr code
     * @input - Paytm response in json
     * Firstly we check header for authentication ,if fais then return with fail
     * If success then save callback data in table
     * After this Save callback table id in Qr table
     * @Output - Success or failure
     */
    function paytm_payment_callback(){
        log_message('info', __FUNCTION__ . "Function Start");
        $authArray = $this->authentication_lib->checkAPIAuthentication();
       $json = file_get_contents('php://input');
        $this->paytm_payment_lib->save_api_response_in_log_table("paytm_transaction_callback",$json,NULL,NULL,json_encode($authArray[1]));
        if($authArray[0] == true){
            //$json = '{"type": null,"requestGuid": null,"orderId": "PG-1672651712311_1743613161","status": null,"statusCode": "SUCCESS","statusMessage": "SUCCESS","response": {"userGuid":"247939278","pgTxnId":"6934721772","timestamp":1492662625972,"cashBackStatus":null,"cashBackMessage":null,"state":null,"heading":null,"walletSysTransactionId":"qwewdjskcnjk","walletSystemTxnId":"XXXXXXXXXXXX","comment":null,"posId":null,"txnAmount":400,"merchantOrderId":"SP-1664331712271_user_download_118832829","uniqueReferenceLabel":null,"uniqueReferenceValue":null,"pccCode":null},"metadata": null}';
            //Save Paytm Response in log table
            $jsonArray = json_decode($json,true);
            //If Payment is done successfully 
            if($jsonArray['statusCode'] == 'SUCCESS'){
                //Save Callback Data In Callback Table
                $insertID = $this->paytm_payment_lib->CALLBACK_save_call_back_data_in_callback_table($jsonArray);
                //Get Booking id from orderid
                $booking_id = explode("_",$jsonArray['response']['merchantOrderId'])[0];
                //Update Transaction table Id Against QR Code in Qr Table
                $this->paytm_payment_lib->CALLBACK_update_transaction_id_in_qr_table($jsonArray['response']['merchantOrderId'],$insertID);
                //Update Transaction table Id Against Booking id in booking details
                $this->paytm_payment_lib->CALLBACK_update_payment_method_in_booking_details($jsonArray['response']['merchantOrderId'],$booking_id);
                echo "SUCCESS";
                log_message('error', __FUNCTION__ . "Function End With Success :");
            }
        }
        else{
            echo  $authArray[3];
            log_message('error', __FUNCTION__ . "Function End With Error :".$authArray[3]);
        }
    }
    function test_cashback($transaction_id,$order_id,$amount,$cashback_reason,$cashback_medium){
        echo $this->paytm_payment_lib->paytm_cashback($transaction_id,$order_id,$amount,$cashback_reason,$cashback_medium);
    }
    function test_QR($bookingID,$qr_for,$amount,$contact){
        echo $this->paytm_payment_lib->generate_qr_code($bookingID,$qr_for,$amount,$contact);
    }
     function check_status($order_id){
        echo $this->paytm_payment_lib->check_status_from_order_id($order_id);
    }
    /*
     * This function use to create view for transactions releated to a booking ID
     */
    function booking_paytm_payment_view($booking_id){
        $data = $this->paytm_payment_lib->get_paytm_transaction_data($booking_id);
        $data['booking_id'] = $booking_id;
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/paytm_transaction_against_booking', $data);
    }
    /*
     * This function use to check status of transaction (USING CHECKSTATUS API) for a booking id
     * First it gets order_id(Merchaint order id for Qr) related to a booking , which does'nt have any transaction yet 
     * Then it send a request to check status API to checkis there any transaction releated to this order_id , IF create failure response and return
     * IF yes then save that transaction in callback table by mention we got this transaction data via checkstatus API
     * After that create a table for response and return
     */
    function get_booking_transaction_status_by_check_status_api($booking_id){
        $resultArray = array();
        $transactionArray = $this->paytm_payment_lib->get_paytm_transaction_data($booking_id);
        if($transactionArray['status']){
            foreach ($transactionArray['data'] as $existTransactions){
                $finalArray['status'] = 'SUCCESS';
                $finalArray['data'] = $existTransactions;
                $resultArray[] = $finalArray    ;
            }
        }
        $tempHtml = "<table class='table'>";
        $tempHtml .= "<th>S.N</th>";
         $tempHtml .= "<th>Amount</th>";
         $tempHtml .= "<th>DateTime</th>";
         $tempHtml .= "<th>Channel</th>";
        $data = $this->paytm_payment_model->get_order_id_without_transaction_for_booking_id($booking_id);
        foreach($data as $order_id){
            $resultArray[] = json_decode($this->paytm_payment_lib->check_status_from_order_id($order_id['order_id']),true);
        }
        if(empty($resultArray)){
            $html = "<p>There is not any new transaction</p>";
        }
        else {
            $t =0;
            foreach($resultArray as $transactionResponse){
                if($transactionResponse['status'] == 'SUCCESS'){
                     $t++; 
                    $tempHtml .= "<tr>";
                    $tempHtml .= "<td>".$t."</td>";
                    $tempHtml .= "<td>".$transactionResponse['data']['paid_amount']."</td>";
                    $tempHtml .= "<td>".$transactionResponse['data']['create_date']."</td>";
                    $tempHtml .= "<td>".explode("_",$transactionResponse['data']['order_id'])[1]."</td>";
                    $tempHtml .= "</tr>";
                }
                else{
                       $html = "<p>There is not any new transaction</p>";
                }
                if($t != 0){
                    $html = $tempHtml;
                }
            }  
        }
        echo $html;
    }
    function paytm_transaction_data(){
        $data['cashback_start_date']  = $data['cashback_end_date'] =  $data['transaction_start_date'] = $data['transaction_end_date'] ='';
        if($this->input->post('cashback_date')){
             $cashback_date = explode("-",$this->input->post('cashback_date'));  
             $data['cashback_start_date'] = $cashback_date[0];
             $data['cashback_end_date'] = $cashback_date[1];
        }
        if($this->input->post('transaction_date')){
                $transaction_date = explode("-",$this->input->post('transaction_date'));
                $data['transaction_start_date'] = $transaction_date[0];
                $data['transaction_end_date'] = $transaction_date[1];
        }
        $data['booking_id'] = $this->input->post('booking_id');
        $data['is_cashback'] = $this->input->post('is_cashback');
        $finalArray = array();
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
        $result = $this->paytm_payment_model->get_all_transactions_with_cashback($data,$post['start'],$post['length']);
        for($i=0;$i<count($result);$i++){
            $tempArray = array();
            $index = $post['start']+($i+1);
            $tempArray[]= $index;
            $tempArray[] = $result[$i]['booking_id'];
            $tempArray[] = $result[$i]['paid_amount'];
            $tempArray[] = $result[$i]['cashback_amount'];
            $tempArray[] = '<button type="button" style="background-color: #2C9D9C;border-color: #2C9D9C;" class="btn btn-sm btn-color" data-toggle="modal" '
                    . 'data-target="#transactionDetails" onclick="showAllTransactions('."'".$result[$i]['booking_id']."'".')"><i class="fa fa-eye" aria-hidden="true"></i></button>';
            $tempArray[] = '<button type="button" style="background-color: #2C9D9C;border-color: #2C9D9C;" class="btn btn-sm btn-color" data-toggle="modal" '
                    . 'data-target="#processCashback" onclick="processCashback('."'".$result[$i]['booking_id']."'".')"><i class="fa fa-money" aria-hidden="true"></i></button>';
            $finalArray[] = $tempArray;
        }
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->paytm_payment_model->get_all_transactions_with_cashback_count($data,$post['start'],$post['length']),
            "recordsFiltered" => $this->paytm_payment_model->get_all_transactions_with_cashback_count($data,NULL,NULL),
            "data" => $finalArray,
        );
        
        echo json_encode($output);
    }
    function paytm_transaction_view(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/paytm_transactions_details');
    }
    function get_all_transaction_cashback_for_booking($bookingID){
        $data = $this->paytm_payment_model->get_paytm_transaction_and_cashback($bookingID);
        echo json_encode($data);
    }
    function process_cashback_by_form(){
        $response = $this->paytm_payment_lib->paytm_cashback($this->input->post('transaction_id'),$this->input->post('order_id'),$this->input->post('cashback_amount'),
        $this->input->post('cashback_reason'),CASHBACK_FORM);
        $responseArray = json_decode($response,true);
        if($responseArray['status'] == SUCCESS_STATUS){
            $this->reusable_model->update_table("paytm_transaction_callback", array("discount_flag" => 1), array('txn_id' => $this->input->post('transaction_id')));
        }
        echo "<p style='text-align:center'>".$responseArray['status']."</p>";
        echo "<p style='text-align:center'>".$responseArray['status_msg']."<p>";
    }
    function resend_QR_code($booking_id,$regenrate_flag,$partner_id){
        $msg = "SMS Sending Failed";
        $booking_details = $this->booking_model->getbooking_history($booking_id, "join");
        if($booking_details[0]['amount_due']>0){
            $is_sms = $this->booking_utilities->send_qr_code_sms($booking_details[0]['booking_id'], 
            $booking_details[0]['primary_contact_phone_1'], $booking_details[0]['user_id'], 
            $booking_details[0]['booking_primary_contact_no'], $booking_details[0]['services'],$regenrate_flag, $booking_details[0]['partner_id']);
            if($is_sms){
                $msg = "SMS Send Successfully";
            }
        }
        else{
            $msg = "Amount paid by Customer is 0, No need to send QR Code";
        }
        echo "<p style='text-align:center;background: #2c9d9c;padding:10px;color:fff;font:20px Century Gothic'>".$msg."</p>";
         echo '<script>setTimeout(function(){ window.close(); }, 1500);</script>';
    }
    function test(){
        $sms['type'] = "user";
        $sms['type_id'] = 1111;
        $sms['tag'] = "customer_qr_download";
        $sms['smsData']['services'] = "Television";
        $sms['smsData']['url'] = "dff";
        $sms['phone_no'] = "8826186751";
        $sms['booking_id'] = "dfdghfthg";
        $this->notify->send_sms_msg91($sms);
    }
}
