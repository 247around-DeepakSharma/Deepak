<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
ini_set('memory_limit', '-1');
//3600 seconds = 60 minutes
ini_set('max_execution_time', 360000);

class Push_Notification extends CI_Controller {
    function __Construct() {
        parent::__Construct();
        $this->load->model('reusable_model');
        $this->load->library('miscelleneous');
    }
     function send_pushcrew_notification(){
        log_message('info', __FUNCTION__ . " Function Start");
        $title = $this->input->post('title');
        $msg = $this->input->post('msg');
        $url = $this->input->post('url');
        $subscriberArray = $this->input->post('subscriberArray');
        $subscriberListArray = Array();
        $subscriberListArray['subscriber_list'] = $subscriberArray;
        $subscriberListJsonString = json_encode($subscriberListArray);
        $apiToken = PUSH_NOTIFICATION_API_KEY;
        $curlUrl = PUSH_NOTIFICATION_SUBSCRIBER_LIST_SEND_NOTIFICATION_URL;
        $fields = array('title' => $title,'message' => $msg,'url' => $url,'subscriber_list' => $subscriberListJsonString);
        $httpHeadersArray = Array();
        $httpHeadersArray[] = 'Authorization: key='.$apiToken;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $curlUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeadersArray);
        $result = curl_exec($ch);
        $resultArray = json_decode($result, true);
        if($resultArray['status'] == 'success'){
                $data['title'] = $title;
                $data['msg'] = $msg;
                $data['url'] = $url;
                $data['subscriber_ids'] = implode(",",$subscriberArray);
                $data['request_id'] =$resultArray['request_id'];
                $this->reusable_model->insert_into_table("push_notification_logs",$data);
                log_message('info', __FUNCTION__ . " Function End Notification has been send Successfully");
          }
          else{
              log_message('info', __FUNCTION__ . " Function End Notification has Not been send, status is failure");
          }
    }
}

