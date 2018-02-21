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
    function send_curl_request($data_string,$headers,$url){
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL,$url); curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
        $output = curl_exec ($ch); // execute echo $output;
        return $output;
    }
    function create_qr_code_response($status,$statusMsg,$dataArray=NULL){
        $responseArray['status']  = $status;
        $responseArray['status_msg']  = $statusMsg;
        if(!empty($dataArray)){
            $responseArray['qr_id'] = $dataArray[0]['id'];
            $responseArray['qr_image'] = $dataArray[0]['qr_image_name'];
            $responseArray['qr_url'] = $dataArray[0]['qr_image_url'];
        }
        return json_encode($responseArray);
    }
    private function QR_save_qr_record_in_db($bookingID,$amount,$success_response,$imgPath,$imgName){
        $data['booking_id'] = $bookingID;
        $data['amount'] = $amount;
        $data['order_id'] = $bookingID."_".$amount;
        $data['qr_data'] = $success_response['response']['qrData'];
        $data['encrypted_data'] = $success_response['response']['encryptedData'];
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
        $paramlist['request']['merchantContactNO'] = "8826186751";
        $paramlist['request']['posId'] = "AJ 123";
        $paramlist['request']['channelId'] = "POS";
        $paramlist['request']['amount'] = $amount; 
        $paramlist['request']['currency'] = "INR";
        $paramlist['request']['merchantGuid'] = MERCHANT_GUID;
        $paramlist['request']['orderId'] = $bookingID."_".$amount;
        $paramlist['request']['Validity'] = "30";
        $paramlist['request']['industryType'] = "RETAIL";
        $paramlist['request']['orderDetails'] = $bookingID;
        $paramlist['platformName'] = 'PayTM'; 
        $paramlist['operationType'] = 'QR_CODE';
        return $paramlist;
    }
    function process_generate_qr_code($bookingID,$amount){
        $paramlist = $this->QR_create_qr_parameters($bookingID,$amount);
        $data_string = json_encode($paramlist);
        $checkSum = $this->P_P->paytm_inbuilt_function_lib->getChecksumFromString($data_string ,PAYTM_MERCHANT_KEY); 
        $headers = array('Content-Type:application/json','merchantGuid: '.MERCHANT_GUID,'mid: '.MID,'checksumhash:'.$checkSum); 
        //$output = $this->send_curl_request($data_string,$headers,QR_CODE_URL);
        $output= '{"requestGuid":null,"orderId":null,"status":"SUCCESS","statusCode":"QR_0001","statusMessage":"SUCCESS","response":{"path":"iVBORw0KGgoAAAANSUhEUgAAAeAAAAHgAQAAAABVr4M4AAABXklEQVR42u3cO3aDMBAAQFH5GDmqOaqP4QrF4RmsX3ASuuyowlijcp+kXTblEyPBQfCcuvGRH2+n9f/b1+/H472fNcEwDMfGUxVP7yvOeUnpsuJr+XYfMwzDMLxH1mmbNr+icLHkXMdmGIZhuMPdxhWGYRj+GR48wjAMw8fH/rwt+Zc7AxiG4QD425RTHXp/ma+CYRj+31iJFwzD8Ml6z2uZXJra3H0eHvthGIaD43baVB37c1v5BMMwHBwXZaGpEV0iqlkdhmEY3vell4Oq+/SccHtdmsIwDIfFeZBcKnarqbwXWMorAhiG4ei4G+MPlfITF8d+GIbhoHhc7lRsZ5sK0SI2wzAMh8ZHLUZy9cVnU3UPwzAcHY+TS6n/UKlq2wTDMAyPGjRdhjcAGYZhGH7XYmTLM23xdinXgWEYjo67Y3+LU9tiBIZhODx+02KkydI3G1cYhuGg+MSAY+BPn84D/CQLePgAAAAASUVORK5CYII=","encryptedData":"281005040101OAFUY7DFX3F8","qrData":"281005040101OAFUY7DFX3F8"}}';
        $outputArray = json_decode($output,true);
        if($outputArray['statusCode'] == 'QR_0001'){
            $imgPath = $this->P_P->miscelleneous->generate_image($outputArray['response']['path'], "QR_".$bookingID."_".$amount.".png",QR_CODE_S3_FOLDER);
            $databaseArray = $this->QR_save_qr_record_in_db($bookingID,$amount,$outputArray,$imgPath,"QR_".$bookingID."_".$amount.".png");
            if($databaseArray['db_id'] >0){
                $databaseArray['data']['id'] = $databaseArray['db_id'];
                return array('is_success'=>1,'msg'=>QR_SUCCESS_MSG,'data'=>$databaseArray['data']);
            }
            else{
                return array('is_success'=>0,'msg'=>QR_CODE_DATABASE_ERROR,'data'=>array());
            }
        }
        else{
            return array('is_success'=>0,'msg'=>QR_CODE_FAILURE,'data'=>array());
        }
    }
}
