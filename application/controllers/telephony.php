<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Telephony extends CI_Controller {
    function __Construct() {
        parent::__Construct();
        $this->load->model('reusable_model');
        $this->load->library('telephony_lib');
        $this->load->model('apis');
    }
    function get_pass_through_parameter_array($jsonDecodeArray){
        $data['call_date']  = $data['call_time']  =  $data['from'] =  $data['to'] = $data['call_status'] = $data['agent_number'] = $data['agent_number'] = $data['call_transfer_status'] = 
                $data["conversation_duration"] = $data['call_uuid'] = $data['recording_url'] = $data['solution_type'] = "";
         if(array_key_exists("call_date", $jsonDecodeArray)){
            $data['call_date'] = $jsonDecodeArray['call_date'];
        }
        if(array_key_exists("call_time", $jsonDecodeArray)){
            $data['call_time'] = $jsonDecodeArray['call_time'];
        }
        if(array_key_exists("caller_number", $jsonDecodeArray)){
            $data['from'] = $jsonDecodeArray['caller_number'];
        }
        if(array_key_exists("called_number", $jsonDecodeArray)){
            $data['to'] = $jsonDecodeArray['called_number'];
        }
        if(array_key_exists("call_status", $jsonDecodeArray)){
            $data['call_status'] = $jsonDecodeArray['call_status'];
        }
        if(array_key_exists("agent_number", $jsonDecodeArray)){
            $data['agent_number'] = $jsonDecodeArray['agent_number'];
        }
        if(array_key_exists("recording_url", $jsonDecodeArray)){
            $data['recording_url'] = $jsonDecodeArray['recording_url'];
        }
        if(array_key_exists("call_transfer_status", $jsonDecodeArray)){
            $data['call_transfer_status'] = $jsonDecodeArray['call_transfer_status'];
        }
        if(array_key_exists("conversation_duration", $jsonDecodeArray)){
            $data['conversation_duration'] = $jsonDecodeArray['conversation_duration'];
        }
        if(array_key_exists("call_uuid", $jsonDecodeArray)){
            $data['call_uuid'] = $jsonDecodeArray['call_uuid'];
        }
        if(array_key_exists("solution_type", $jsonDecodeArray)){
            $data['solution_type'] = $jsonDecodeArray['solution_type'];
        }
        return $data;
    }
    function pass_through(){    
//        $json = '{  
//   "call_date":"2018-01-23",
//   "call_time":"15:45:56",
//   "caller_number":" 9650461855",
//   "called_number":" 01130017603",
//   "call_status":"connected",
//   "agent_list":" 919582669508",
//   "agent_number":" 919566889988",
//   "call_transfer_status":"connected",
//   "caller_duration":"0:00:15",
//   "conversation_duration":"0:00:15",
//   "call_uuid":"29e74a9a-9b19-4ac2-adce-65ad160701b8_0",
//   "recording_url":"test.mp3",
//   "solution_type":"Missed"
//}';   
        log_message('info', __METHOD__);
        $jsonDecodeArray = $this->input->get();
        $activity = array('activity' => 'process knowlarity request', 'data' => json_encode($jsonDecodeArray), 'time' => $this->telephony_lib->microtime_float());
        $this->apis->logTable($activity);
         log_message('info', __METHOD__.print_r($jsonDecodeArray,TRUE));
        if($jsonDecodeArray){
            $data = $this->get_pass_through_parameter_array($jsonDecodeArray);
            if($data['solution_type'] == 'Incoming'){
                $this->telephony_lib->process_incoming_calls_response($data);
            }
            else{
                $this->telephony_lib->process_missed_calls_response($data);
            }
        }
        else{
            $response["status"] = "FALSE";
            $response["msg"] = "Received Blank data";
            $msg =  json_encode($response);
             log_message('error', __METHOD__.print_r($response,TRUE));
            echo $msg;
        }
    }
    function outgoing_calls(){
        $params = "";
        $getParameter['call_type'] = 1;
        $getParameter['limit'] = 100;
        //$getParameter['end_time'] = $endTime = date('Y-m-d h:i:s');
     //   $getParameter['start_time'] = date('Y-m-d h:i:s', strtotime('-15 day', strtotime($endTime)));
        $getParameter['end_time'] = $endTime = "2018-07-26 23:59:59+05:30";
        $getParameter['start_time'] = "2018-07-20 00:00:01+05:30";
        $getParameter['offset'] = 2;
        foreach($getParameter as $key=>$value){
                $params .= $key.'='.$value.'&'; 
        }
        echo $params = KNOWLARITY_CALL_LOG_URL."?".urlencode($params);
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $params,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => "",
        CURLOPT_HTTPHEADER => array(
        "authorization: ".KNOWLARITY_API_KEY,
        "cache-control: no-cache",
        "channel: Basic",
        "content-type: application/json",
        "x-api-key: ".KNOWLARITY_APPLICATION_KEY,
            "call_type: 1"
        ),
        ));
        echo $response = curl_exec($curl);
//        $data = json_decode($response,TRUE);
//        echo "<pre>";
//        print_r($data);
        exit();
        $this->telephony_lib->process_outgoing_calls_data($data);
    }
}
