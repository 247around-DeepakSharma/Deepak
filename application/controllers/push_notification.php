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
    // This function is used to get notification center data and create a view for notifictaion center
     function get_notifications(){
         $entity_id = $this->input->post('entity_id');
         $entity_type = $this->input->post('entity_type');
         $notificationString = '<li style="text-align:center;font:bold 20px Century Gothic;background: #2c9d9c; padding: 7px;color: #fff;margin-bottom: 10px;">Notifications</li>';
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
             $notificationString = $notificationString.'<li class="no_new_notification">No new Notification </li>';
         }
         echo $notificationString;
   }
}

