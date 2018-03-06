<?php
/*
 * This library handles payment communication with Paytm 
 * In this library we are using Version 2.0 of paytm API
 * Reffrence_document - 
 */
class paytm_payment_lib {
    public function __construct() {
        $this->P_P = & get_instance();
        $this->P_P->load->model('paytm_payment_model');
        $this->P_P->load->helper('array');
        $this->P_P->load->library('paytm_inbuilt_function_lib');
        $this->P_P->load->library('miscelleneous');
        $this->P_P->load->model('reusable_model');
    }
    private function send_curl_request($data_string,$headers,$url,$activity){
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL,$url); curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
        $output = curl_exec ($ch); // execute echo $output;
        $this->save_api_response_in_log_table($activity,$output,$data_string,_247AROUND,json_encode($headers));
        return $output;
    }
    function save_api_response_in_log_table($activity,$response=NULL,$request=NULL,$partner_id=NULL,$header=NULL){
        $logData['activity'] = $activity;
        if($response != NULL){
            $logData['json_response_string'] = $response;
        }
        if($partner_id != NULL){
            $logData['partner_id'] = $partner_id;
        }
        if($request != NULL){
            $logData['json_request_data'] = $request;
        }
        if($header != NULL){
            $logData['header'] = $header;
        }
        $this->P_P->reusable_model->insert_into_table("log_partner_table",$logData);
    }
    /*
     * This is a helper function for generate_qr_code_function
     * It use to create response for qr code generation process
     * @input - 1) $status - SUCCESS or FAILURE
     *                  2) $status Msg - A msg to explain failure or success
     *                  3) $dataArray  - Response Parameters array in case of success (Optional for Failure case)
     * @output - Response of generate_qr_code function in json format 
     *                  1) Status - Success Failure
     *                  2) Status Msg - Msg to explain success or failure
     *                  3) qr_id - Table id of newly generated QR Code or Already Exisiting Code
     *                  4) qr_image - Image name of generated QR
     *                  5) qr_path - URL of generated Image
     */
    private function QR_create_qr_code_response($status,$statusMsg,$dataArray=NULL){
        log_message('info', __FUNCTION__ . " Function Start");
        $responseArray['status']  = $status;
        $responseArray['status_msg']  = $statusMsg;
        if(!empty($dataArray)){
            $responseArray['qr_id'] = $dataArray[0]['id'];
            $responseArray['qr_image'] = $dataArray[0]['qr_image_name'];
            $responseArray['qr_url'] = $dataArray[0]['qr_image_url'];
        }
        log_message('info', __FUNCTION__ . " Function End With Response".print_r($responseArray,true));
        return json_encode($responseArray);
    }
    /*
     * This function is used to save success response parameters in qr table
     */
    private function QR_save_qr_record_in_db($bookingID,$amount,$parameterList,$success_response,$imgPath,$imgName){
        log_message('info', __FUNCTION__ . " Function Start");
        $data['booking_id'] = $bookingID;
        if($amount != 0){
          $data['amount'] = $amount;  
        }
        $data['order_id'] = $parameterList['request']['orderId'];
        $data['channel'] = $parameterList['request']['posId'];
        $data['txn_response_contact'] = $parameterList['request']['merchantContactNO'];
        $data['qr_data'] = $success_response['response']['qrData'];
        $data['qr_path'] = $success_response['response']['path'];
        $data['qr_image_url'] = $imgPath;
        $data['qr_image_name'] = $imgName;
        $data['create_date'] = date('Y_m-d h:i:s');
        $data['create_date'] = date('Y_m-d h:i:s');
        $db_id = $this->P_P->reusable_model->insert_into_table("paytm_payment_qr_code",$data);
        log_message('info', __FUNCTION__ . " Function End".print_r($data,true)." database id".$db_id);
        return array('data'=>$data,'db_id'=>$db_id);
    }
    /*
     * This Function is used to create QR Generate API Parameters For Paytm
     */
    private function QR_create_qr_parameters($bookingID,$channel,$amount,$contact){
        log_message('info', __FUNCTION__ . " Function Start");
        $paramlist['request']['requestType'] = QR_CODE_REQUEST_TYPE;
        $paramlist['request']['merchantContactNO'] = $contact;
        $paramlist['request']['posId'] = $channel;
        if($amount != 0){
            $paramlist['request']['amount'] = $amount; 
        }
        $paramlist['request']['currency'] = "INR";
        $paramlist['request']['merchantGuid'] = MERCHANT_GUID;
        $paramlist['request']['orderId'] = $bookingID."_".$channel."_".rand(100,1000);
        $paramlist['request']['Validity'] = "30";
        $paramlist['request']['industryType'] = "RETAIL";
        $paramlist['request']['orderDetails'] = $bookingID;
        $paramlist['platformName'] = 'PayTM'; 
        $paramlist['operationType'] = 'QR_CODE';
        log_message('info', __FUNCTION__ . " Function End");
        return $paramlist;
    }
    /*
     * This function is used to handle successful creation of qr code
     */
    private function QR_generation_success_handler($outputArray,$bookingID,$amount,$paramlist){
        log_message('info', __FUNCTION__ . "Function Start");
        //Img name is booking + random number
        $imgName = "QR_".$bookingID."_".rand(100,1000).".png";
        //Generate Img from QR path
        $imgPath = $this->P_P->miscelleneous->generate_image($outputArray['response']['path'],$imgName,QR_CODE_S3_FOLDER);
        //Save Records into qr table
        $databaseArray = $this->QR_save_qr_record_in_db($bookingID,$amount,$paramlist,$outputArray,$imgPath,$imgName);
        //If saved then
        if($databaseArray['db_id'] >0){
            log_message('info', __FUNCTION__ . "Function End Data Saved into Qr table".print_r($databaseArray,true));
            $databaseArray['data']['id'] = $databaseArray['db_id'];
            return array('is_success'=>1,'msg'=>QR_CREATED_SUCCESSFULLY_MSG,'data'=>$databaseArray['data']);
        }
        else{
            log_message('info', __FUNCTION__ . "Function End Data Not Saved into Qr table".print_r($databaseArray,true));
            return array('is_success'=>0,'msg'=>QR_CODE_DATABASE_ERROR,'data'=>array());
        }
    }
    /*
     * This is a helper function For generate_qr_code function
     * It is use to communicate with Paytm to generate QR code
     * @input - $booking_id,$channel(Channel for sales eg -"job_card,app,user_download" etc),$amount
     * It Create request json with all required parameters for Paytm API
     * @output - msg-"Is QR Generated Or Not",success or failure msg,Paytm ApI Response Data
     */
    private function QR_process_generate_qr_code($bookingID,$channel,$amount,$contact){
        log_message('info', __FUNCTION__ . "Function Start");
        //Create Parameters List
        $paramlist = $this->QR_create_qr_parameters($bookingID,$channel,$amount,$contact);
        log_message('info', __FUNCTION__ . "Parameters List".print_r($paramlist,true));
        $data_string = json_encode($paramlist);
        //Create Checksum for requested Body
        $checkSum = $this->P_P->paytm_inbuilt_function_lib->getChecksumFromString($data_string ,PAYTM_MERCHANT_KEY); 
        //Header Array
        $headers = array('Content-Type:application/json','merchantGuid: '.MERCHANT_GUID,'mid: '.MID,'checksumhash:'.$checkSum); 
        //Send Curl request to paytm API
        $output = $this->send_curl_request($data_string,$headers,QR_CODE_URL,"QR_Code_generation");
        $outputArray = json_decode($output,true);
        // QR_001 -> SUCCESS, QR-1020 -> IF QR already exist for same input
        //In both case save into databse 
        if($outputArray['statusCode'] == SUCCESS_PAYTM_QR_RESPONSE || $outputArray['statusCode'] == ALREADY_GENERATED_PAYTM_QR_RESPONSE){
            log_message('info', __FUNCTION__ . "Function End with Success");
            return $this->QR_generation_success_handler($outputArray,$bookingID,$amount,$paramlist);
        }
        else{
            log_message('info', __FUNCTION__ . "Function End With Failure");
              return array('is_success'=>0,'msg'=>QR_CODE_FAILURE,'data'=>array());
        }
    }
    /*
     * This is a helper function for generate_qr_code function
     * It check is Qr code Already Exist For Booking iD, Amount,Channel and Contact
     * @input - $booking_id, $channel(Channel of Sale eg - Job_card,App,user_download etc) $amount,$contact(Contact for transaction notification)
     * @output - array which contains flag (is_exist) and 2nd is Existing Data
     */
    private function QR_is_qr_code_already_exists_for_input($bookingID,$channel,$amount,$contact){
        log_message('info', __FUNCTION__ . "booking_id".$bookingID.", Amount ".$amount.", Channel ".$channel);
        // Check if qr code already there for same bookingid and amount 
        $where['booking_id'] = $bookingID;
        $where['transaction_id'] = NULL;
        $where['channel'] = $channel;
        $where['amount'] = NULL;
        $where['txn_response_contact'] = $contact;
        if($amount != 0){
            $where['amount'] = $amount;
         }
         $existingBookingData = $this->P_P->paytm_payment_model->get_qr_code("*",$where);
         //If data Exists
         if(!empty($existingBookingData)){
             log_message('info', __FUNCTION__ . "Function End With QR Code Exists".print_r($existingBookingData,true));
             return array('is_exist'=>1,'data'=>$existingBookingData);
         }
         //If not
         log_message('info', __FUNCTION__ . "Function End With QR Code Not Exists");
         return array('is_exist'=>0,'data'=>array());
    }
    /*
     * This function is used to save paytm call back response in callback table 
     */
    function CALLBACK_save_call_back_data_in_callback_table($jsonArray){
        $orderID = $jsonArray['response']['merchantOrderId'];
        $booking_id = explode("_",$orderID)[0];
        $data['booking_id'] = $booking_id;
        // Unique id genereted by paytm for each order
        $data['paytm_order_id'] = $jsonArray['orderId'];
        // Order id provided by merchant (247Around) at the time of qr generation (Unique for each qr)
        $data['order_id'] = $orderID;
        // transaction id provided by paytm (Unique for each transaction)
        $data['txn_id'] = $jsonArray['response']['walletSystemTxnId'];
        // How much amount has been received by current transaction
        $data['paid_amount'] = $jsonArray['response']['txnAmount'];
        //Guid For user
        $data['user_guid'] = $jsonArray['response']['userGuid'];
       // Does paytm generrate any cashback for current transaction
        $data['cash_back_status'] = $jsonArray['response']['cashBackStatus'];
        // Details of cashback
        $data['cash_back_message'] = $jsonArray['response']['cashBackMessage'];
        $data['create_date'] = date("Y-m-d h:i:s");
        //response_api (From which api we are getting response,check status or callback)
        $data['response_api'] = TRANSACTION_RESPONSE_FROM_CALLBACK;
        $insertID = $this->P_P->reusable_model->insert_into_table("paytm_transaction_callback",$data);
         //Send Email 
        $to = TRANSACTION_SUCCESS_TO; 
        $cc = TRANSACTION_SUCCESS_CC;
        $subject = "New Transaction From Paytm - ".$data['txn_id'];
        $message = "Hi,<br/> We got a new transaction from paytm for below:<br/>  BookingID - " .$booking_id.", OrderID - ".$data['order_id'];
        $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $message, "");
        return $insertID;
    }
    /*
     * This Function is used to update transaction table id in qr table
     */
    function CALLBACK_update_transaction_id_in_qr_table($order_id,$insertID){
        $updateData['transaction_id'] = $insertID;
        $updateData['payment_date'] = date("Y-m-d h:i:s");
        $where['order_id'] = $order_id;
        $affected_rows  = $this->P_P->reusable_model->update_table("paytm_payment_qr_code",$updateData,$where);
        if($affected_rows>0){
            return SUCCESS_STATUS;
        }
        return FAILURE_STATUS;
    }
    /*
     * This function is used to update payment Channel against booking_id
     */
    function CALLBACK_update_payment_method_in_booking_details($order_id,$booking_id){
        $data = $this->P_P->reusable_model->get_search_result_data("paytm_payment_qr_code",'channel',array('order_id'=>$order_id),NULL,NULL,NULL,NULL,NULL,array());
        if(!empty($data)){
            $this->P_P->reusable_model->update_table("booking_details",array('payment_method'=>$data[0]['channel']),array('booking_id'=>$booking_id));
        }
    }
    function CASHBACK_create_cashback_response($status,$msg){
        log_message('info', __FUNCTION__ . " Function Start");
        $responseArray['status']  = $status;
        $responseArray['status_msg']  = $msg;
        log_message('info', __FUNCTION__ . " Function End With Response".print_r($responseArray,true));
        return json_encode($responseArray);
    }
    /*
     * This function is used to create parameters(need to send to paytm api) array
     * @input - 1) $bookingPaymentDetails - Transaction Payment details Array
     *                  2) $amount - Amount needs to cashback
     */
    private function CASHBACK_create_cashback_parameters($bookingPaymentDetails,$amount){
        log_message('info', __FUNCTION__ . " Function Start");
        //amount need to refund
        $paramlist['request']['amount'] = $amount;
        //Order_id (Created at the time of qr generation)
        $paramlist['request']['merchantOrderId'] = $bookingPaymentDetails[0]['order_id'];
        //Merchant GUID
        $paramlist['request']['merchantGuid'] = MERCHANT_GUID;
        //Transaction_id (Return by paytm at the time of payment)
        $paramlist['request']['txnGuid'] = $bookingPaymentDetails[0]['txn_id'];
        //Currency
        $paramlist['request']['currencyCode'] = "INR";
        //Refund id (It must be unique for each cashback)
        $paramlist['request']['refundRefId'] = "R_".$bookingPaymentDetails[0]['booking_id']."_".rand(100,1000);
        $paramlist['platformName'] = "PayTM";
        $paramlist['operationType'] = "REFUND_MONEY";
        $paramlist['version'] = CASHBACK_API_version; 
        log_message('info', __FUNCTION__ . " Function End");
        return $paramlist;
    }
    /*
     * This function is used to handle successfully processing of cashback
     * @input - 1) @transaction_id - Transaction ID(Return by paytm at the time of qr generation)
     *                  2) @amount - Amount needs to transafer
     *                  3) $paramlist = Parameter Array(Requestr array for paytm API)
     *                  4) @outputArray - Response By Paytm
     */
    function CASHBACK_generation_success_handler($transaction_id,$amount,$paramlist,$outputArray){
       //Create where array (Where we have to update refund in transaction table)
       $where['txn_id'] =  $transaction_id;
       $where['order_id'] =  $paramlist['request']['merchantOrderId'];
       //Cashback Amount
       $cashBackData['cashback_amount'] = $amount;
       //Cashback Transaction ID (Provided by 247Around at the time of request)
       $cashBackData['cashback_txn_id'] = $paramlist['request']['refundRefId'];
       //Refund Transaction ID (Provided BY paytm on successfull processing of cashback)
       $cashBackData['cashback_txn_id_paytm'] = $outputArray['response']['refundTxnGuid'];
       //Cashback initiated by (Default is _247AROUND)
       $cashBackData['cashback_from'] = _247AROUND; 
       $cashBackData['cash_back_status'] = "SUCCESS";
       $cashBackData['cashback_date'] = date("Y-m-d h:i:s");
       $db_id = $this->P_P->reusable_model->update_table("paytm_transaction_callback",$cashBackData,$where);
       if($db_id>0){
           return array('is_success'=>1,'msg'=>SUCCESS_STATUS);
       }
       return array('is_success'=>0,'msg'=>CASHBACK_DATABASE_ERROR);
    }
    /*
     * This function is used to process cashback using paytm API
     * @input - 1) $bookingPaymentDetails - transaction Payment details array
     *                  2) $amount - (Amount need to transfer)
     *                  3) $transaction_id - Transaction ID 
     */
    function CASHBACK_process_cashback($bookingPaymentDetails,$amount,$transaction_id){
        //Check is Refund amount less then transaction amount?
        //IF yes
        if($bookingPaymentDetails[0]['paid_amount']>$amount){
            //Create API request Array
            $paramlist = $this->CASHBACK_create_cashback_parameters($bookingPaymentDetails,$amount);
            $data_string = json_encode($paramlist);
            //Create Checksum for requested Body
            $checkSum = $this->P_P->paytm_inbuilt_function_lib->getChecksumFromString($data_string ,PAYTM_MERCHANT_KEY); 
            //Header Array
            $headers = array('Content-Type:application/json','mid: '.MERCHANT_GUID,'checksumhash:'.$checkSum); 
            //Send Curl request to paytm api
            $output = $this->send_curl_request($data_string,$headers,CASHBACK_URL,"Process_Cashback");
            //$output = '{"type": null,"requestGuid": null,"orderId": "TEST_123469933335ff991099","status": "SUCCESS","statusCode": "SUCCESS","statusMessage":"SUCCESS","response": {"refundTxnGuid": "14271439146","refundTxnStatus": "SUCCESS"},"metadata": "Test"}';
            $outputArray = json_decode($output,true);
            //IF success
            if($outputArray['status'] == 'SUCCESS'){
                log_message('info', __FUNCTION__ . "Function End with Success");
                return $this->CASHBACK_generation_success_handler($transaction_id,$amount,$paramlist,$outputArray);
            }
            else{
                log_message('info', __FUNCTION__ . "Function End With Failure");
                  return array('is_success'=>0,'msg'=>QR_CODE_FAILURE);
            }
        }
        else{
            return array('is_success'=>0,'msg'=>REFUND_AMOUNT_GRETER_THEN_TRANSACTION_AMOUNT);
        }
    }
    /*
     * This Function is used to process paytm cashback against A Transaction
     * @input - 1) @transaction_id - Transaction ID(Transaction ID provided by paytm)
     *                  2) $amount - How much Amount we have to refund
     */
    function paytm_cashback($transaction_id,$amount){
        //Check is transaction id exists
        //Select * FROM paytm_transaction_callback WHERE txn_id=$transaction_id;
        $bookingPaymentDetails = $this->P_P->reusable_model->get_search_result_data("paytm_transaction_callback","*",array('txn_id'=>$transaction_id),NULL,NULL,NULL,NULL,NULL,array());
        //If transaction id does'nt exists
        if(empty($bookingPaymentDetails)){
            //exit function with response not found transaction
            return $this->CASHBACK_create_cashback_response(FAILURE_STATUS,CASHBACK_TRANSACTION_NOT_FOUND_MSG);
        }
        else{
            //Check if cashback already processed against this transaction
            //IF yes
            if($bookingPaymentDetails[0]['cashback_txn_id'] != NULL){
                //exit function with response cashback already processed
                return $this->CASHBACK_create_cashback_response(FAILURE_STATUS,CASHBACK_ALREADY_DONE_FOR_THIS_TRANSACTION_ID);
            }
            //IF not
            else{
                $resultArray = $this->CASHBACK_process_cashback($bookingPaymentDetails,$amount,$transaction_id);
                 if($resultArray['is_success'] == 1){
                         return $this->CASHBACK_create_cashback_response(SUCCESS_STATUS,$resultArray['msg']);
                     }
                     else{
                         return $this->CASHBACK_create_cashback_response(FAILURE_STATUS,$resultArray['msg']);
                     }
            }
        }
    }
      /*
     * This function is used to genrate qr code using paytm API
     * @input parameter - booking_id,amount,Channel(Through which channel payment happens eg - "job_card","app","user_download" etc),contact(transaction notification)
     * @output paramenter - 1) Status - (Success or Failure)
                                                   2) status_message (reason of failure in case of failure or Success msg)
                                                   3) QR Image Url
                                                   4) QR Image name
                                                   5) Database Record ID (after saving QR code in database, it will return databse id)
                                                     
     */
    function generate_qr_code($bookingID,$channel,$amount,$contact){
            if($amount !=0){
                $amount = number_format((float)$amount, 2, '.', '');
            }
            log_message('info', __FUNCTION__ . "booking_id".$bookingID.", Amount ".$amount.", Channel ".$channel.", Contact no ".$contact);
            //Check if any qr code already there with same booking and amount
            $existBooking = $this->QR_is_qr_code_already_exists_for_input($bookingID,$channel,$amount,$contact);
            // if yes then generate response with existing data
            if($existBooking['is_exist'] == 1){
                return $this->QR_create_qr_code_response(SUCCESS_STATUS,QR_ALREADY_EXISTS_MSG,$existBooking['data']);
            }
            //if not then
            else{
                //Generate qr code
                $resultArray = $this->QR_process_generate_qr_code($bookingID,$channel,$amount,$contact);
                // IF QR code Generated Successfully
                if($resultArray['is_success'] == 1){
                    return $this->QR_create_qr_code_response(SUCCESS_STATUS,$resultArray['msg'], array($resultArray['data']));
                }
                //If not able to Generate QR Code
                else{
                    return $this->QR_create_qr_code_response(FAILURE_STATUS,$resultArray['msg']);
                }
            }
    }
    /*
     * This is a helper function for check_status_from_order_id
     * This function is used to create response of check_status_from_order_id function 
     * @ input - 1) checkstatus Status
     * 2) StatusMsg - Detailed description of status
     * 3) Transaction Data - if checkstatus API response is success
     */
    function CHECKSTATUS_create_check_status_response($status,$statusMsg,$dataArray=array()){
        log_message('info', __FUNCTION__ . " Function Start");
        $responseArray['status']  = $status;
        $responseArray['status_msg']  = $statusMsg;
        if(!empty($dataArray)){
            $responseArray['data'] = $dataArray;
        }
        log_message('info', __FUNCTION__ . " Function End With Response".print_r($responseArray,true));
        return json_encode($responseArray);
    }
    /*
     * This is a helper function for check_status_from_order_id
     * This function is use to handle success response from paytm checkstatus API,
     * Save transaction into database
     * @input - Paytm Response Array
     * @output - structured response Array
     */
    function CHECKSTATUS_checkstatus_success_handler($responseArray){
        log_message('info', __FUNCTION__ . " Function Start With  ".print_r($responseArray,true));
        $transactionsArray = $responseArray['response']['txnList'];
        foreach ($transactionsArray as $transaction){
            $orderID = $transaction['merchantOrderId'];
            $data['booking_id'] = explode("_",$orderID)[0];
            $data['paytm_order_id'] = $orderID;
            $data['order_id'] = $orderID;
            $data['txn_id'] = $transaction['txnGuid'];
            $data['paid_amount'] = $transaction['txnAmount'];
            $data['user_guid'] = $transaction['ssoId'];
            $data['paid_amount'] = $transaction['txnAmount'];
            $data['cashback_amount'] = "";
            $data['cashback_txn_id'] = $transaction['cashbackTxnId'];
            $data['create_date'] = date('Y-m-d h:i:s');
            //From Which API We are Getting Response
            $data['response_api'] = TRANSACTION_RESPONSE_FROM_CHECK_STATUS;
            // Save data into transaction table
            $insertID = $this->P_P->reusable_model-> insert_into_table("paytm_transaction_callback",$data);
            if($affectedRows>0){
                //Send Email 
                $to = TRANSACTION_SUCCESS_TO; 
                $cc = TRANSACTION_SUCCESS_CC;
                $subject = "New Transaction From Paytm - ".$data['txn_id'];
                $message = "Hi,<br/> We got a new transaction from paytm for below:<br/>  BookingID - " .$booking_id.", OrderID - ".$data['order_id'];
                $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $message, "");
            }
        }
        log_message('info', __FUNCTION__ . " Function End With  ".print_r($data,true));
        return $data;
    }
    /*
     * This is a helper function for check_status_from_order_id
     * this function use to create request for checkstatus , send to paytm 
     * @input - OrderID
     * @output - Paytm Response Array
     */ 
    function CHECKSTATUS_send_check_status_request_from_order_id($order_id){
        log_message('info', __FUNCTION__ . " Function Start ".$order_id);
        //This is default requestType to check transaction status from orderID
        $data['request']['requestType'] = 'merchanttxnid';
        //This is default txnType to check transaction status from orderID
        $data['request']['txnType'] = 'withdraw';
        //Merchant Guid
        $data['request']['merchantGuid'] = MERCHANT_GUID;
        //Order ID which we create at the time of qr generation
        $data['request']['txnId'] = $order_id;
        //Default Value
        $data['platformName'] = "PayTM";
        //Default Value
        $data['operationType'] = "CHECK_TXN_STATUS"; 
        //Create json string of request
        $data_string  = json_encode($data);
        //Create Checksum for requested Body
        $checkSum = $this->P_P->paytm_inbuilt_function_lib->getChecksumFromString($data_string ,PAYTM_MERCHANT_KEY);
        //Pass Merchant guid in Mid Fields in header
        $headers = array('Content-Type:application/json','mid: '.MERCHANT_GUID,'checksumhash:'.$checkSum); 
        //Send Curl request to paytm api
        $output = $this->send_curl_request($data_string,$headers,CHECK_STATUS_URL,"Process_Check_Status");
        log_message('info', __FUNCTION__ . " Function End With Data:  ".print_r($output,true));
        return $outputArray = json_decode($output,true);
    }
    /*
     * This function is used to check transaction status against a order_id
     * @input - order id which we define at the time of qr generation
     * @output - Response Contains
     * 1) Status - Success,Failure Or No Transaction
     * 2) StatusMsg - Detailed Info About Status
     * 3) transaction Data(Optional) - Data of transaction , if transaction happens against requested order_id
     */
    function check_status_from_order_id($order_id){
        log_message('info', __FUNCTION__ . " Function Start ".$order_id);
        //Send Check Status request to paytm
       $responseArray =  $this->CHECKSTATUS_send_check_status_request_from_order_id($order_id);
       // If success
        if($responseArray['response']['txnList'][0]['status'] == CHECK_STATUS_SUCCESS_CODE){
            $data = $this->CHECKSTATUS_checkstatus_success_handler($responseArray);
            log_message('info', __FUNCTION__ . " Function End With Success");
            return $this->CHECKSTATUS_create_check_status_response(CHECK_STATUS_SUCCESS,CHECK_STATUS_SUCCESS_MSG,$data);
        }
        // If transaction not happens against this order id
        else if($responseArray['statusCode'] == CHECK_STATUS_INVALID_ORDER_ID){
            log_message('info', __FUNCTION__ . " Function End With No Transaction against Order ID");
           return  $this->CHECKSTATUS_create_check_status_response(TRANSACTION_NOT_HAPPENS_YET,TRANSACTION_NOT_HAPPENS_YET_MSG);
        }
        // Failure
        else{
            log_message('info', __FUNCTION__ . " Function End With Failure");
            return $this->CHECKSTATUS_create_check_status_response(CHECK_STATUS_FAILURE,CHECK_STATUS_FAILURE_MSG);
        }
    }
    function booking_paytm_payment_data($booking_id){
        $finalAmount = 0;
        //Select * From paytm_transaction_callback where booking_id=$booking_id
        $data = $this->P_P->reusable_model->get_search_result_data("paytm_transaction_callback","*",array("booking_id"=>$booking_id),NULL,NULL,NULL,NULL,NULL,array());
        if(!empty($data)){
            foreach($data as $transaction){
                $finalAmount = $finalAmount+$transaction['paid_amount'];
                $channel[] = explode("_",$transaction['order_id'])[1];
            }
            return array('status'=>true,'data'=>$data,'total_amount'=>$finalAmount,'channels'=>array_values(array_unique($channel)));
        }
        return array('status'=>false);
    }
}
