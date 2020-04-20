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
        log_message('info', __METHOD__);
        $jsonDecodeArray = $this->input->get();
        $activity = array('activity' => 'process knowlarity request', 'data' => json_encode($jsonDecodeArray), 'time' => $this->telephony_lib->microtime_float());
        $this->apis->logTable($activity);
         log_message('info', __METHOD__.print_r($jsonDecodeArray,TRUE));
        if($jsonDecodeArray){
            $data = $this->get_pass_through_parameter_array($jsonDecodeArray);
            if($data['solution_type'] == 'incoming'){
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
        $getParameter['limit'] = 10000;
        $getParameter['start_time']  = date("Y-m-d");
        $getParameter['end_time'] = date('Y-m-d', strtotime('+1 day', strtotime($getParameter['start_time'])));
        foreach($getParameter as $key=>$value){
                $params .= $key.'='.$value.'&'; 
        }
        $params = KNOWLARITY_CALL_LOG_URL."?".$params;
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
        $response = curl_exec($curl);
        $data = json_decode($response,TRUE);
        $this->telephony_lib->process_outgoing_calls_data($data);
    }
}
