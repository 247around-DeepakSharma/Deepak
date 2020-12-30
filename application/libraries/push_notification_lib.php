<?php

class push_notification_lib {
    public function __construct() {
        $this->Pu_N = & get_instance();
        $this->Pu_N->load->model('reusable_model');
        $this->Pu_N->load->model('push_notification_model');
        $this->Pu_N->load->library('miscelleneous');
        $this->Pu_N->load->library('asynchronous_lib');
        $this->Pu_N->load->helper('cookie');
        $this->Pu_N->load->library('notify');
    }
    /*
     * This Function is used to send Push Notifications 
     * @input - Notification title , notification msg, notification url,notification type and an array of subscriber list
     * @output 1) This Function will send push notiifcation to browser of subscribers
     * @output 2) After Success it will save notification in notification log table
     * Note - We are Using pushcrew 3rd party to send these notifications
     */
    function send_push_notification($title,$msg,$url,$notification_type,$subscriberArray,$notification_tag,$autohide_notification=0){
        if($title && $msg && $url && $notification_type && $subscriberArray){
            $subscriberListArray = Array();
            $subscriberListArray['subscriber_list'] = $subscriberArray;
            $subscriberListJsonString = json_encode($subscriberListArray);
//IF constant is not defined //
            if (defined('PUSH_NOTIFICATION_API_KEY')) {
            $apiToken = PUSH_NOTIFICATION_API_KEY;
            $curlUrl = PUSH_NOTIFICATION_SUBSCRIBER_LIST_SEND_NOTIFICATION_URL;
            }

            $fields = array('title' => $title,'message' => $msg,'url' => $url,'subscriber_list' => $subscriberListJsonString,'autohide_notification'=>$autohide_notification);
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
                    $data['notification_tag'] =$notification_tag;
                    $data['status'] =1;
                    $this->Pu_N->reusable_model->insert_into_table("push_notification_logs",$data);
              }
              else{
                   $data['title'] = $title;
                    $data['msg'] = $msg;
                    $data['url'] = $url;
                    $data['subscriber_ids'] = implode(",",$subscriberArray);
                    $data['notification_type'] =$notification_type;
                    $data['notification_tag'] =$notification_tag;
                    $data['status'] =0;
                    $data['status_msg'] = $resultArray['message'];
                    $this->Pu_N->reusable_model->insert_into_table("push_notification_logs",$data);
                    if($resultArray['message'] == INVALID_SUBSCRIBER_ID_MSG){
                        //UPDATE push_notification_subscribers SET is_valid=0,valid_date=date('Y-m-d h:i:s'),unsubscription_flag=1,unsubscription_date=date('Y-m-d h:i:s') Where subscriber_id = $subscriber_id;
                        $this->Pu_N->reusable_model->update_table("push_notification_subscribers",array("is_valid"=>0,"valid_date"=>date('Y-m-d h:i:s'),"unsubscription_flag"=>1,
                            "unsubscription_date"=>date("Y-m-d h:i:s")),array('subscriber_id'=>$subscriberArray[0]));
                    }
                    else{
                        //Send Email
//                        $to = PUSH_NOTIFICATION_ERROR_NOTIFY_EMAIL; 
//                        $cc = PUSH_NOTIFICATION_ERROR_NOTIFY_EMAIL;
//                        $subject = "Error For Push Notification";
//                        $message = "Hi,<br/> We got following error :".$resultArray['message']."For $subscriberArray[0]";
//                        $this->Pu_N->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $message, "",PUSH_NOTIFICATION_ERROR);
                    }
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
        function create_and_send_push_notiifcation($templateTag,$receiverArray=array(),$notificationTextArray=array(),$auto_hide=0){
            // Get Template Data
            $templateData = $this->Pu_N->push_notification_model->get_push_notification_template($templateTag);
            if(!empty($templateData)){
                if(empty($receiverArray)){
                    // If receiver Array is empty then get subscriber id for group which is mentioned in template table
                    $recieiverGroupTypeArray = explode(",",$templateData[0]['entity_type']);
                    $subscriberArrayTemp  = $this->Pu_N->push_notification_model->get_subscriber_by_entity_types($recieiverGroupTypeArray);
                }
                else{
                    // Get Subscriber ID array on combination entity_type and entity_id if receiver array is not blank
                    $subscriberArrayTemp = $this->Pu_N->push_notification_model->get_subscriberID_by_entity_type_and_entity_id($receiverArray);
                }
                if(!empty($subscriberArrayTemp)){
                    foreach($subscriberArrayTemp as $dataArray){
                        $subscriber_ID = array();
                        $subscriber_ID[] = $dataArray['subscriber_id'];
                        $data['subscriberArray'] = $subscriber_ID;
                        $data['notification_type'] = $templateData[0]['notification_type'];
                        $data['title'] = $templateData[0]['title'];
                        $data['msg'] = $templateData[0]['msg'];
                        $data['url'] =    base_url().$templateData[0]['url'];
                        $data['auto_hide'] =   $auto_hide;
                        $data['notification_tag'] =  $templateData[0]['notification_tag']; 
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
                }
            }
                else{
                    log_message('info', __FUNCTION__ . "Push Notification Reciever Is not available For ".print_r($receiverArray,true));
                }
            } 
            else{
                log_message('info', __FUNCTION__ . "Push Notification Template Not Found ".$templateTag);
            }
    }
    /*
     * This Function is used to send notification to partner, when we completed the booking
     * It send the Count of completed booking to Partner
     */
    function send_booking_completion_notification_to_partner($bookingIDArray){
        $partnerArray = array();
        // get internal status and partner for bookings 
        $getBookingData = $this->Pu_N->push_notification_model->get_booking_data($bookingIDArray);
        // get partner Booking's Associative Array 
        foreach($getBookingData as $data){
            if($data['internal_status'] ==  'Completed'){
                $partnerArray[$data['partner_id']]['bookings'][] = $data['booking_id'];
            }
        }
        foreach($partnerArray as $partnerID=>$bookingArray){
            $receiverArray['partner'] = array($partnerID);
            $notificationTextArray['title'] = array(count($bookingArray));
            $notificationTextArray['msg'] = array(count($bookingArray));
            $this->create_and_send_push_notiifcation(BOOKING_COMPLETED_FOR_PARTNER,$receiverArray,$notificationTextArray);
        }
    }
    /*
     * This Function is used to get unsubscribers of push notification by cookies
     * get subscription status and subscription_id
     * if subscription status is not subscribed then update unscbscription flag against that subscription ID
     */
    function get_unsubscribers_by_cookies(){
        $status = get_cookie('wingify_push_subscription_status');
        $subscriber_id = get_cookie('wingify_push_subscriber_id');
        if($status && $subscriber_id){
            if($status != "subscribed"){
                $this->Pu_N->push_notification_model->update_push_notification_unsubscription($subscriber_id,array('subscriber_id'=>$subscriber_id));
            }
        }
    }
}
