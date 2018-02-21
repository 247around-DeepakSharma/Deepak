<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
 * This class is used to create Paytm Payment API
 * Eg- Create QR Code,Handle Payment Response,Refund
 */
class paytm_payment extends CI_Controller {
    function __Construct() {
        parent::__Construct();
        $this->load->library('paytm_payment_lib');
        $this->load->model('paytm_payment_model');
    }
    /*
     * This function is used to genrate qr code for (booking_id+amount) using paytm API
     * @input parameter - booking_id,amount
     * @output paramenter - 1) Status - (Success or Failure)
                                                   2) status_message (reason of failure in case of failure or Success msg)
                                                   3) QR Image Url
                                                   4) QR Image name
                                                   5) Database Record ID (after saving QR code in database, it will return databse id)
     * @conditions -1) If same booking_id and amount is already exist in db then return already exist and all data (no need to create new QR Code) 
                                  2) If booking_id is already exists and amount is different then 
                                      a) Create new QR Code
                                      b) Inactive previous code against the requested id
                                      c) Update new QR Code id in booking details
                                  3) If booking_id and amount is new 
                                       a) Genrate QR code
                                       b) Add QR code id in booking details table 
                                                     
     */
    function generate_qr_code($bookingID,$amount){
        if($amount == 0){
            $response = $this->paytm_payment_lib->create_qr_code_response(FAILURE_STATUS,AMOUNT_ZERO_ERROR);
        }
        else{
            //Check if any qr code already there with same booking and amount
            $bookingAmountData = $this->paytm_payment_model->get_qr_code("*",array('booking_id'=>$bookingID,"amount"=>$amount,'is_active'=>1));
            // if yes then generate response with existing data
            if(!empty($bookingAmountData)){
                $response = $this->paytm_payment_lib->create_qr_code_response(SUCCESS_STATUS,QR_ALREADY_EXISTS_MSG,$bookingAmountData);
            }
            //if not then
            else{
                //Inactive all qr code with same booking id diff amount
                $this->paytm_payment_model->inactive_qr_code(array('booking_id'=>$bookingID,'transaction_id'=>NULL),array('is_active'=>0));
                //Generate qr code
                $resultArray = $this->paytm_payment_lib->process_generate_qr_code($bookingID,$amount);
                if($resultArray['is_success'] == 1){
                    $response = $this->paytm_payment_lib->create_qr_code_response(SUCCESS_STATUS,QR_CREATED_SUCCESSFULLY_MSG, array($resultArray['data']));
                }
                else{
                    $response = $this->paytm_payment_lib->create_qr_code_response(FAILURE_STATUS,$resultArray['msg']);
                }
            }
        }
        echo  $response;
    }
    function payment_callback_handling(){
        
    }
    function refund_process(){
        
    }
}
