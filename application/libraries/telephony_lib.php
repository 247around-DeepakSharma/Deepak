<?php

class Telephony_lib {

    public function __construct() {
        $this->Tp_CI = & get_instance();
        $this->Tp_CI->load->model('reusable_model');
        $this->Tp_CI->load->model('apis');
        $this->Tp_CI->load->model('partner_model');
        $this->Tp_CI->load->model('user_model');
        $this->Tp_CI->load->library('notify');
        $this->Tp_CI->load->library('asynchronous_lib');
    }
    function process_outgoing_calls_data($incomingData){
        foreach($incomingData['objects'] as $data){
            $agentData = array();
            $to = substr($data['customer_number'], '-10');
            $from = substr($data['agent_number'], '-10');
            $updateArray['call_status'] = $data['call_status'];
            $updateArray['call_duration'] = $data['call_duration'];
            $where['customer_phone'] = $to;
            $where['call_duration IS NULL'] = NULL;
            $where['call_status IS NULL'] = NULL;
            $where['date(create_date)'] = date("Y-m-d");
            if($from){
                $agentData = $this->Tp_CI->reusable_model->get_search_result_data("employee","id",array("phone"=>$from),NULL,NULL,NULL,NULL,NULL,array());
            }
        }
        if($to && !empty($agentData)){
            $where['agent_id'] = $agentData[0]['id'];
            $this->Tp_CI->reusable_model->update_table("agent_outbound_call_log",$updateArray,$where);
        }
    }
    function microtime_float() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }
    function process_missed_calls_response($data){
        $callDate = $data["call_date"] ;
        $callTime = $data["call_time"] ;
        $callDetails['callSid'] = $data['call_uuid'];
        $callDetails['from_number'] = $data['from'];
        $to = $callDetails['To'] = $data['to'];
        $callDetails['Direction'] = "Incoming";
        $callDetails['DialCallDuration'] = $data['conversation_duration'];
        $callDetails['StartTime'] = date('Y-m-d H:i:s', strtotime("$callDate $callTime"));
        $callDetails['EndTime'] = NULL;
        $callDetails['CallType'] = $data["call_status"];
        $callDetails['DialWhomNumber'] = NULL;
        $callDetails['digits'] = NULL;
        if($to == AC_SERVICE_MISSED_CALLED_NUMBER_KNOWLARITY){
            $url = base_url() . "api/pass_through_ac_service";
        }
        else if($to == ANDROID_APP_MISSED_CALLED_NUMBER_KNOWLARITY){
            $url = base_url() . "api/pass_through_android_app";
        }
        else if($to == GOOD_MISSED_CALL_RATING_NUMBER_KNOWLARITY || $to == POOR_MISSED_CALL_RATING_NUMBER_KNOWLARITY){
            $url = base_url() . "api/pass_through_rating_missed_call";
        }
        else if($to == FAKE_RESCHEDULED_MISSED_CALL_NUMBER_KNOWLARITY){
            $url = base_url() . "api/pass_through_fake_reschedule_call";
        }
        else if($to == PARTNERS_MISSED_CALLED_NUMBER_KNOWLARITY || $to == SNAPDEAL_MISSED_CALLED_NUMBER_KNOWLARITY){
           $url = base_url() . "api/pass_through";
        }
            $this->Tp_CI->asynchronous_lib->do_background_process($url, $callDetails);
    }
    function process_incoming_calls_response($data){
        log_message('info', __METHOD__);
        $agentNumber = substr($data['agent_number'], '-10');
        $customerNumber = substr($data['from'], '-10');
        $callDate = $data["call_date"] ;
        $callTime = $data["call_time"] ;
        $agent_id = $customer_id = NULL;
        $agentData = $this->Tp_CI->reusable_model->get_search_result_data("employee","id",array("phone"=>$agentNumber),NULL,NULL,NULL,NULL,NULL,array());
        if(!empty($agentData)){
            $agent_id = $agentData[0]['id'];
        }
        $user = $this->Tp_CI->user_model->get_users_by_any(array("users.phone_number" => $customerNumber));
        if ($user) {
            $customer_id = $user[0]['user_id'];
        }
        $incomingData['virtual_number'] = $data['to'];
        $incomingData['agent_id'] = $agent_id;
        $incomingData['customer_id'] = $customer_id;
        $incomingData['customer_phone'] = $customerNumber;
        $incomingData['create_date'] = date('Y-m-d H:i:s', strtotime("$callDate $callTime"));
        $incomingData['call_duration'] = $data['conversation_duration'];
        $incomingData['call_status'] = $data['call_status'];
        return $this->Tp_CI->reusable_model->insert_into_table("incoming_call_logs",$incomingData);
    }
}
