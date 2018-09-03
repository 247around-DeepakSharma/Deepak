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
        $this->load->library('user_agent');
        $this->load->helper('text');
    }
    // This function is used to get notification center data and create a view for notifictaion center
     function send_pushcrew_notification(){
        log_message('info', __FUNCTION__ . " Function Start");
        $title = $msg = $url = $notification_type = $subscriberArray = NULL;
        if($this->input->post('title')){
            $title = $this->input->post('title');
        }
        if($this->input->post('msg')){
            $msg = $this->input->post('msg');
        }
        if($this->input->post('url')){
            $url= $this->input->post('url');
        }
        if($this->input->post('notification_type')){
            $notification_type = $this->input->post('notification_type');
        }
        if($this->input->post('subscriberArray')){
            $subscriberArray = $this->input->post('subscriberArray');
        }
        if($title && $msg && $url && $notification_type && $subscriberArray){
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
                    $data['notification_type'] =$notification_type;
                    $this->reusable_model->insert_into_table("push_notification_logs",$data);
                    log_message('info', __FUNCTION__ . " Function End Notification has been send Successfully");
              }

              else{
                  log_message('info', __FUNCTION__ . " Function End Notification has Not been send, status is failure");
              }
        }
    }
     function get_notifications(){
         $topNavColor = '#2c9d9c';
         $entity_id = $this->input->post('entity_id');
         $entity_type = $this->input->post('entity_type');
         if($entity_type == 'partner'){
             $topNavColor = '#2a3f54';
         }
         $notificationString = '<li  style="text-align:center;font:bold 20px Century Gothic;background: '.$topNavColor.'; padding: 7px;color: #fff;margin-bottom: 10px;">Notifications</li>';
         $data = $this->reusable_model->get_search_result_data("push_notification_subscribers p","push_notification_logs.*",array("p.entity_type"=>$entity_type,"p.entity_id"=>$entity_id),
                 array("push_notification_logs"=>"FIND_IN_SET(p.subscriber_id,push_notification_logs.subscriber_ids)"),NULL,NULL,NULL,NULL,array("push_notification_logs.id"));
         if($data){
            foreach($data as $notificationArray){
                $notificationString = $notificationString.'<li class="navigation_li '.$notificationArray['notification_type'].'"><a href='.$notificationArray['url'].'><strong>'.$notificationArray['title'].'</strong></a></li>';
                $notificationString = $notificationString.'<div class="clear"></div>';
                $notificationString = $notificationString.'<li class="divider"></li>';
            }
         }
         else{
             $notificationString = $notificationString.'<li class="no_new_notification">No new notification </li>';
         }
         echo $notificationString;
   }
       function save_push_notification_subscribers(){
        log_message('info', __FUNCTION__ . " Function Start");
        $data['subscriber_id'] = $this->input->post('subscriberID');
        $data['entity_id'] = $this->session->userdata('id');
        $data['entity_type'] = $this->session->all_userdata()['userType'];
        $data['browser'] = $this->agent->browser();
        if($data['entity_type']=='service_center'){
            $data['entity_id'] = $this->session->userdata('service_center_id');
            $data['entity_type'] = 'vendor';
        }
        else if($data['entity_type']=='partner'){
            $data['entity_id'] = $this->session->userdata('partner_id');
            $data['entity_type'] = 'partner';
        }
        $data['device'] = "Desktop";
        if($data['subscriber_id'] == -1){
            $data['unsubscription_flag'] = 1;
            $data['unsubscription_date'] = date('Y-m-d h:i:s');
        }
        $is_mobile = $this->agent->is_mobile();
        if($is_mobile){
            $data['device'] = "Mobile";
        }
       $this->reusable_model->insert_into_table("push_notification_subscribers",$data);
       log_message('info', __FUNCTION__ . " Function End");
    }
    
}

