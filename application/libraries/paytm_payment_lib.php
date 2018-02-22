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
        $logData['header'] = json_encode($headers);
        $logData['json_request_data'] = $data_string;
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL,$url); curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
        $output = curl_exec ($ch); // execute echo $output;
        $logData['json_response_string'] = $output;
        $logData['activity'] = $activity;
        $this->P_P->reusable_model->insert_into_table("log_partner_table",$logData);
        return $output;
    }
    private function create_qr_code_response($status,$statusMsg,$dataArray=NULL){
        $responseArray['status']  = $status;
        $responseArray['status_msg']  = $statusMsg;
        if(!empty($dataArray)){
            $responseArray['qr_id'] = $dataArray[0]['id'];
            $responseArray['qr_image'] = $dataArray[0]['qr_image_name'];
            $responseArray['qr_url'] = $dataArray[0]['qr_image_url'];
        }
        return json_encode($responseArray);
    }
    private function QR_save_qr_record_in_db($bookingID,$amount,$orderID,$success_response,$imgPath,$imgName){
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
        return array('data'=>$data,'db_id'=>$db_id);
    }
    private function QR_create_qr_parameters($bookingID,$amount){
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
        return $paramlist;
    }
    private function process_generate_qr_code($bookingID,$amount){
        $paramlist = $this->QR_create_qr_parameters($bookingID,$amount);
        $data_string = json_encode($paramlist);
        $checkSum = $this->P_P->paytm_inbuilt_function_lib->getChecksumFromString($data_string ,PAYTM_MERCHANT_KEY); 
        $headers = array('Content-Type:application/json','merchantGuid: '.MERCHANT_GUID,'mid: '.MID,'checksumhash:'.$checkSum); 
        $output = $this->send_curl_request($data_string,$headers,QR_CODE_URL,"QR_Code_generation");
        //$output= '{"requestGuid":null,"orderId":null,"status":"SUCCESS","statusCode":"QR_0001","statusMessage":"SUCCESS","response":{"path":"iVBORw0KGgoAAAANSUhEUgAAAeAAAAHgAQAAAABVr4M4AAABXklEQVR42u3cO3aDMBAAQFH5GDmqOaqP4QrF4RmsX3ASuuyowlijcp+kXTblEyPBQfCcuvGRH2+n9f/b1+/H472fNcEwDMfGUxVP7yvOeUnpsuJr+XYfMwzDMLxH1mmbNr+icLHkXMdmGIZhuMPdxhWGYRj+GR48wjAMw8fH/rwt+Zc7AxiG4QD425RTHXp/ma+CYRj+31iJFwzD8Ml6z2uZXJra3H0eHvthGIaD43baVB37c1v5BMMwHBwXZaGpEV0iqlkdhmEY3vell4Oq+/SccHtdmsIwDIfFeZBcKnarqbwXWMorAhiG4ei4G+MPlfITF8d+GIbhoHhc7lRsZ5sK0SI2wzAMh8ZHLUZy9cVnU3UPwzAcHY+TS6n/UKlq2wTDMAyPGjRdhjcAGYZhGH7XYmTLM23xdinXgWEYjo67Y3+LU9tiBIZhODx+02KkydI3G1cYhuGg+MSAY+BPn84D/CQLePgAAAAASUVORK5CYII=","encryptedData":"281005040101OAFUY7DFX3F8","qrData":"281005040101OAFUY7DFX3F8"}}';
        $outputArray = json_decode($output,true);
        if($outputArray['statusCode'] == 'QR_0001' || $outputArray['statusCode'] == 'QR_1020'){
            $imgName = "QR_".$bookingID."_".rand().".png";
            $imgPath = $this->P_P->miscelleneous->generate_image($outputArray['response']['path'],$imgName,QR_CODE_S3_FOLDER);
            $databaseArray = $this->QR_save_qr_record_in_db($bookingID,$amount,$paramlist['request']['orderId'],$outputArray,$imgPath,$imgName);
            if($databaseArray['db_id'] >0){
                $this->P_P->reusable_model->update_table("booking_details",array('qr_code_id'=>$databaseArray['db_id']),array('booking_id'=>$bookingID));
                $databaseArray['data']['id'] = $databaseArray['db_id'];
                return array('is_success'=>1,'msg'=>QR_CREATED_SUCCESSFULLY_MSG,'data'=>$databaseArray['data']);
            }
            else{
                return array('is_success'=>0,'msg'=>QR_CODE_DATABASE_ERROR,'data'=>array());
            }
        }
        else{
              return array('is_success'=>0,'msg'=>QR_CODE_FAILURE,'data'=>array());
        }
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
            //Check if any qr code already there with same booking and amount
            $where['booking_id'] = $bookingID;
            $where['is_payment_done'] = 0;
            $where['is_active'] = 1;
            if($amount != 0){
                $where['amount'] = $amount;
            }
            else{
                  $where['amount'] = NULL;
            }
            $bookingAmountData = $this->P_P->paytm_payment_model->get_qr_code("*",$where);
            // if yes then generate response with existing data
            if(!empty($bookingAmountData)){
                $response = $this->create_qr_code_response(SUCCESS_STATUS,QR_ALREADY_EXISTS_MSG,$bookingAmountData);
            }
            //if not then
            else{
                //Inactive all qr code with same booking id diff amount
                $this->P_P->paytm_payment_model->inactive_qr_code(array('booking_id'=>$bookingID,'is_payment_done'=>0),array('is_active'=>0));
                //Generate qr code
                $resultArray = $this->process_generate_qr_code($bookingID,$amount);
                if($resultArray['is_success'] == 1){
                    $response = $this->create_qr_code_response(SUCCESS_STATUS,$resultArray['msg'], array($resultArray['data']));
                }
                else{
                    $response = $this->create_qr_code_response(FAILURE_STATUS,$resultArray['msg']);
                }
            }
        echo  $response;
    }
}
