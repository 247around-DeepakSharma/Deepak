<?php

class push_notification_lib {
    public function __construct() {
        $this->Pu_N = & get_instance();
        $this->Pu_N->load->model('reusable_model');
        $this->Pu_N->load->model('push_notification_model');
        $this->Pu_N->load->library('miscelleneous');
        $this->Pu_N->load->library('asynchronous_lib');
    }
    /*
     * This Function is used to send Push Notifications 
     * @input - Notification title , notification msg, notification url,notification type and an array of subscriber list
     * @output 1) This Function will send push notiifcation to browser of subscribers
     * @output 2) After Success it will save notification in notification log table
     * Note - We are Using pushcrew 3rd party to send these notifications
     */
    function send_push_notification($title,$msg,$url,$notification_type,$subscriberArray){
        log_message('info', __FUNCTION__ . " Function Start");
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
                    $this->Pu_N->reusable_model->insert_into_table("push_notification_logs",$data);
                    log_message('info', __FUNCTION__ . " Function End Notification has been send Successfully");
              }

              else{
                  log_message('info', __FUNCTION__ . " Function End Notification has Not been send, status is failure");
              }
        }
    }
    /*
     * This function is use to create and send push notification 
     * @input 1) TempatleTag - Template Tag for push notification
     * @input 2) Reciever Array , It will contain entity_type and entity_id ex - array('employee'=>array(2,3,6),'vendor'=>array(1,3,8))
     * @input 3) Notification Text, this array will contain dynamic text which we have to use in template ex - array('title'=>array("13,"Chennai"),'msg'=>array("apple","bananna"),'url'=>array("x","y"))
     * Note - If receiver array will empty then it will send notification to groups which are mention in template table with current template ID 
     * After creating the msg it will send the msg to subscribers by using send_push_notification Function 
     */
        function create_and_send_push_notiifcation($templateTag,$receiverArray=array(),$notificationTextArray=array()){
            // Get Template Data
            $templateData = $this->Pu_N->push_notification_model->get_push_notification_template($templateTag);
            if(empty($receiverArray)){
                // If receiver Array is empty then get subscriber id for group which is mentioned in template table
                $recieiverGroupTypeArray = explode(",",$templateData[0]['entity_type']);
                $subscriberArrayTemp  = $this->Pu_N->push_notification_model->get_subscriber_by_entity_types($recieiverGroupTypeArray);
            }
            else{
                // Get Subscriber ID array on combination entity_type and entity_id if receiver array is not blank
                $subscriberArrayTemp = $this->Pu_N->push_notification_model->get_subscriberID_by_entity_type_and_entity_id($receiverArray);
            }
            $subscriberArray = array();
            foreach($subscriberArrayTemp as $dataArray){
                $subscriberArray[] = $dataArray['subscriber_id'];
            }
            if(!empty($subscriberArray)){
                $data['subscriberArray'] = $subscriberArray;
                $data['notification_type'] = $templateData[0]['notification_type'];
                $data['title'] = $templateData[0]['title'];
                $data['msg'] = $templateData[0]['msg'];
                $data['url'] =    base_url().$templateData[0]['url'];
                if(array_key_exists('title', $notificationTextArray)){
                    $data['title'] = vsprintf($templateData[0]['title'], $notificationTextArray['title']);
                }
                if(array_key_exists('msg', $notificationTextArray)){
                    $data['msg'] = vsprintf($templateData[0]['msg'], $notificationTextArray['msg']);
                }  
                if(array_key_exists('url', $notificationTextArray)){
                    $data['url'] = vsprintf(base_url().$templateData[0]['url'], $notificationTextArray['url']);
                 } 
                 $sendUrl = base_url().'employee/do_background_process/send_asyn_push_notification';
                 $this->Pu_N->asynchronous_lib->do_background_process($sendUrl, $data);
                 $this->send_push_notification($data['title'],$data['msg'],$data['url'],$data['notification_type'],$data['subscriberArray']);
                 //$this->send_push_notification($data['title'],$data['msg'],$data['url'],$data['notification_type'],$subscriberArray);
            }
            else{
                log_message('info', __FUNCTION__ . " Reciever Is not available");
            }
    }
}
