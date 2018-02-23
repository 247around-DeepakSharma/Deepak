<?php
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
    private function QR_save_qr_record_in_db($bookingID,$amount,$orderID,$success_response,$imgPath,$imgName){
        log_message('info', __FUNCTION__ . " Function Start");
        $data['booking_id'] = $bookingID;
        if($amount != 0){
          $data['amount'] = $amount;  
        }
        $data['order_id'] = $orderID;
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
    private function QR_create_qr_parameters($bookingID,$amount){
        log_message('info', __FUNCTION__ . " Function Start");
        $paramlist['request']['requestType'] = QR_CODE_REQUEST_TYPE;
        $paramlist['request']['merchantContactNO'] = MERCHANT_CONTACT;
        $paramlist['request']['posId'] = $bookingID;
        if($amount != 0){
            $paramlist['request']['amount'] = $amount; 
        }
        $paramlist['request']['currency'] = "INR";
        $paramlist['request']['merchantGuid'] = MERCHANT_GUID;
        if($amount == 0){
           $paramlist['request']['orderId'] = $bookingID."_".rand();
        }
        else{
            $paramlist['request']['orderId'] = $bookingID."_".$amount;
        }
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
        $imgName = "QR_".$bookingID."_".rand().".png";
        //Generate Img from QR path
        $imgPath = $this->P_P->miscelleneous->generate_image($outputArray['response']['path'],$imgName,QR_CODE_S3_FOLDER);
        //Save Records into qr table
        $databaseArray = $this->QR_save_qr_record_in_db($bookingID,$amount,$paramlist['request']['orderId'],$outputArray,$imgPath,$imgName);
        //If saved then
        if($databaseArray['db_id'] >0){
            log_message('info', __FUNCTION__ . "Data Saved into Qr table".print_r($databaseArray,true));
            //update qr_id in booking_details table
            $this->P_P->reusable_model->update_table("booking_details",array('qr_code_id'=>$databaseArray['db_id']),array('booking_id'=>$bookingID));
            $databaseArray['data']['id'] = $databaseArray['db_id'];
            log_message('info', __FUNCTION__ . "Function End");
            return array('is_success'=>1,'msg'=>QR_CREATED_SUCCESSFULLY_MSG,'data'=>$databaseArray['data']);
        }
        else{
            log_message('info', __FUNCTION__ . "Data Not Saved into Qr table".print_r($databaseArray,true));
            log_message('info', __FUNCTION__ . "Function End");
            return array('is_success'=>0,'msg'=>QR_CODE_DATABASE_ERROR,'data'=>array());
        }
    }
    /*
     * This is a helper function to generate_qr_code function
     * It is use to communicate with Paytm to generate QR code
     * @input - $booking_id,$amount
     * It Create request json with all required parameters for Paytm API
     * @output - msg-"Is QR Generated Or Not",success or failure msg,Paytm ApI Response Data
     */
    private function QR_process_generate_qr_code($bookingID,$amount){
        log_message('info', __FUNCTION__ . "Function Start");
        //Create Parameters List
        $paramlist = $this->QR_create_qr_parameters($bookingID,$amount);
        log_message('info', __FUNCTION__ . "Parameters List".print_r($paramlist,true));
        $data_string = json_encode($paramlist);
        //Create Checksum for requested Body
        $checkSum = $this->P_P->paytm_inbuilt_function_lib->getChecksumFromString($data_string ,PAYTM_MERCHANT_KEY); 
        //Header Array
        $headers = array('Content-Type:application/json','merchantGuid: '.MERCHANT_GUID,'mid: '.MID,'checksumhash:'.$checkSum); 
        //Send Curl request to paytm api
        $output = $this->send_curl_request($data_string,$headers,QR_CODE_URL,"QR_Code_generation");
        //$output= '{"requestGuid":null,"orderId":null,"status":"SUCCESS","statusCode":"QR_0001","statusMessage":"SUCCESS","response":{"path":"iVBORw0KGgoAAAANSUhEUgAAAeAAAAHgAQAAAABVr4M4AAABXklEQVR42u3cO3aDMBAAQFH5GDmqOaqP4QrF4RmsX3ASuuyowlijcp+kXTblEyPBQfCcuvGRH2+n9f/b1+/H472fNcEwDMfGUxVP7yvOeUnpsuJr+XYfMwzDMLxH1mmbNr+icLHkXMdmGIZhuMPdxhWGYRj+GR48wjAMw8fH/rwt+Zc7AxiG4QD425RTHXp/ma+CYRj+31iJFwzD8Ml6z2uZXJra3H0eHvthGIaD43baVB37c1v5BMMwHBwXZaGpEV0iqlkdhmEY3vell4Oq+/SccHtdmsIwDIfFeZBcKnarqbwXWMorAhiG4ei4G+MPlfITF8d+GIbhoHhc7lRsZ5sK0SI2wzAMh8ZHLUZy9cVnU3UPwzAcHY+TS6n/UKlq2wTDMAyPGjRdhjcAGYZhGH7XYmTLM23xdinXgWEYjo67Y3+LU9tiBIZhODx+02KkydI3G1cYhuGg+MSAY+BPn84D/CQLePgAAAAASUVORK5CYII=","encryptedData":"281005040101OAFUY7DFX3F8","qrData":"281005040101OAFUY7DFX3F8"}}';
        $outputArray = json_decode($output,true);
        // QR_001 -> SUCCESS, QR-1020 -> IF QR already exist for same input
        //In both case save into databse 
        if($outputArray['statusCode'] == 'QR_0001' || $outputArray['statusCode'] == 'QR_1020'){
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
     * It check is Qr code Already Exist For Booking iD and Amount
     * @input - $booking_id and $amount
     * @output - array which contains flag (is_exist) and 2nd is Existing Data
     */
    private function QR_is_qr_code_already_exists_for_input($bookingID,$amount){
        log_message('info', __FUNCTION__ . "booking_id".$bookingID.", Amount ".$amount);
        // Check if qr code already there for same bookingid and amount 
        $where['booking_id'] = $bookingID;
        $where['transaction_id'] = NULL;
        $where['is_active'] = 1;
        if($amount != 0){
            $where['amount'] = $amount;
         }
         else{
            $where['amount'] = NULL;
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
    function generate_qr_code($bookingID,$amount=0){
            log_message('info', __FUNCTION__ . "booking_id".$bookingID.", Amount ".$amount);
            //Check if any qr code already there with same booking and amount
            $existBooking = $this->QR_is_qr_code_already_exists_for_input($bookingID,$amount);
            // if yes then generate response with existing data
            if($existBooking['is_exist'] == 1){
                return $this->QR_create_qr_code_response(SUCCESS_STATUS,QR_ALREADY_EXISTS_MSG,$existBooking['data']);
            }
            //if not then
            else{
                //Inactive all active qr code with same booking_id and diff amount where transaction_id is null
                $this->P_P->paytm_payment_model->inactive_qr_code(array('booking_id'=>$bookingID,'transaction_id'=>NULL),array('is_active'=>0));
                //Generate qr code
                $resultArray = $this->QR_process_generate_qr_code($bookingID,$amount);
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
     * This function is used to save paytm call back response in callback table 
     */
    function CALLBACK_save_call_back_data_in_callback_table($jsonArray){
        $orderID = $jsonArray['response']['merchantOrderId'];
        $booking_id = explode("_",$orderID)[0];
        $data['booking_id'] = $booking_id;
        $data['paytm_order_id'] = $jsonArray['orderId'];
        $data['order_id'] = $orderID;
        $data['txn_id'] = $jsonArray['response']['walletSystemTxnId'];
        $data['paid_amount'] = $jsonArray['response']['txnAmount'];
        $data['user_guid'] = $jsonArray['response']['userGuid'];
        $data['cash_back_status'] = $jsonArray['response']['cashBackStatus'];
        $data['cash_back_message'] = $jsonArray['response']['cashBackMessage'];
        $data['create_date'] = date("Y-m-d h:i:s");
        $insertID = $this->P_P->reusable_model->insert_into_table("paytm_transaction_callback",$data);
        return $insertID;
    }
    /*
     * This Function is used to update transaction table id in qr table
     * If any qr is not active and we found a transaction against that qr code then update its is_active flag from 0 to 1
     */
    function CALLBACK_update_transaction_id_in_qr_table($order_id,$insertID){
        $updateData['transaction_id'] = $insertID;
        $updateData['is_active'] = 1;
        $updateData['payment_date'] = date("Y-m-d h:i:s");
        $where['order_id'] = $order_id;
        $this->P_P->reusable_model->update_table("paytm_payment_qr_code",$updateData,$where);
    }
    /*
     * This Function is used to process paytm cashback
     */
    function paytm_cashback($booking_id,$amount){
        
    }
}
