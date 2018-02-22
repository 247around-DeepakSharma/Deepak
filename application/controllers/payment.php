<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Payment extends CI_Controller {
    function __Construct() {
        parent::__Construct();
        $this->load->library('paytm_payment_lib');
        $this->load->model('reusable_model');
    }
    /*
     * This function is used to handle paytm callback after transaction against any qr code
     * @input - Paytm response in json
     */
    function paytm_payment_call_back(){
        $json = '{"type": null,"requestGuid": null,"orderId": "PG-1672651712311_1743613161","status": null,"statusCode": "SUCCESS","statusMessage": "SUCCESS","response": {"userGuid": "c529fdf0-9af7-11e3-852b-000c292554b0","walletSystemTxnId": "63","timestamp": 1444308992384,"cashBackStatus": null,"cashBackMessage": null,"state": null,"heading": ""},"metadata": null}';
        //Save Paytm Response in log table
        $this->paytm_payment_lib->save_callback_response_in_log_table("paytm_transaction_callback",$json,NULL,NULL,NULL);
        $jsonArray = json_decode($json,true);
        //If Payment is done successfully 
        if($jsonArray['statusCode'] == 'SUCCESS'){
            //Save Callback Data In Callback Table
            $insertID = $this->paytm_payment_lib->save_call_back_data_in_callback_table($jsonArray);
            //Update Transaction Id Against QR Code in Qr Table
            $this->paytm_payment_lib->update_transaction_id_against_qr($jsonArray['orderId'],$insertID);
        }
    }
}
