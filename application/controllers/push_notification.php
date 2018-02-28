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
     function get_notifications(){
         $topNavColor = '#2c9d9c';
         $entity_id = $this->input->post('entity_id');
         $entity_type = $this->input->post('entity_type');
         if($entity_type == 'partner'){
             $topNavColor = '#2a3f54';
         }
         $notificationString = '<li  style="text-align:center;font-size: 20px;background: '.$topNavColor.'; padding: 7px;color: #fff;margin-bottom: 10px;">Notifications</li>';
         $data = $this->reusable_model->get_search_result_data("push_notification_subscribers p","push_notification_logs.*",array("p.entity_type"=>$entity_type,"p.entity_id"=>$entity_id)
                 ,array("push_notification_logs"=>"FIND_IN_SET(p.subscriber_id,push_notification_logs.subscriber_ids)"),
                 array('length'=>NOTIFICATIONS_CENTER_LIMIT,'start'=>0),array('push_notification_logs.create_date'=>'DESC'),NULL,NULL,array("push_notification_logs.id"));
         if($data){
            foreach($data as $notificationArray){
                $title = wordwrap($notificationArray['title'],50,"<br>\n");
                $notificationString = $notificationString.'<li class="navigation_li '.$notificationArray['notification_type'].'"><a href='.$notificationArray['url'].'>'.$title.'</a></li>';
                $notificationString = $notificationString.'<div class="clear"></div>';
                $notificationString = $notificationString.'<li class="divider"></li>';
            }
         }
         else{
             $notificationString = $notificationString.'<li class="no_new_notification">No new Notification </li>';
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

