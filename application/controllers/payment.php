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
     */
    function paytm_payment_call_back(){
        $authArray = $this->authentication_lib->checkAPIAuthentication();
        if($authArray[0] == true){
            $json = file_get_contents('php://input');
            //$json = '{"type": null,"requestGuid": null,"orderId": "PG-1672651712311_1743613161","status": null,"statusCode": "SUCCESS","statusMessage": "SUCCESS","response": {"userGuid":"247939278","pgTxnId":"6934721772","timestamp":1492662625972,"cashBackStatus":null,"cashBackMessage":null,"state":null,"heading":null,"walletSysTransactionId":"qwewdjskcnjk","walletSystemTxnId":"XXXXXXXXXXXX","comment":null,"posId":null,"txnAmount":400,"merchantOrderId":"SP-1664331712271_user_download_118832829","uniqueReferenceLabel":null,"uniqueReferenceValue":null,"pccCode":null},"metadata": null}';
            //Save Paytm Response in log table
            $this->paytm_payment_lib->save_api_response_in_log_table("paytm_transaction_callback",$json,NULL,NULL,json_encode($authArray[1]));
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
            }
        }
    }
    function test_cashback($bookingID,$amount){
        echo $this->paytm_payment_lib->paytm_cashback($bookingID,$amount);
    }
    function test_QR($bookingID,$qr_for,$amount=0,$contact=NULL){
        echo $this->paytm_payment_lib->generate_qr_code($bookingID,$qr_for,$amount,$contact);
    }
}
