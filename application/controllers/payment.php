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
    function test_cashback($bookingID,$amount){
        echo $this->paytm_payment_lib->paytm_cashback($bookingID,$amount);
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
        $tempHtml = "<table class='table'>";
        $tempHtml .= "<th>S.N</th>";
         $tempHtml .= "<th>Amount</th>";
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
}
