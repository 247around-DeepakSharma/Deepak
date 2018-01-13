<?php

class push_notification_lib {
    public function __construct() {
        $this->Pu_N = & get_instance();
        $this->Pu_N->load->model('reusable_model');
        $this->Pu_N->load->library('miscelleneous');
        $this->Pu_N->load->library('asynchronous_lib');
    }
function send_booking_completion_notification_to_closure($bookingID){
        $subscriberArray = array();
        $closerAccountSubscriberID = $this->Pu_N->reusable_model->get_search_result_data("push_notification_subscribers p","p.subscriber_id",array("employee.groups"=>"closure"),
                array("employee"=>"employee.id=p.entity_id"),NULL,NULL,NULL,NULL,array());
        foreach($closerAccountSubscriberID as $subscriberID){
            $subscriberArray[] = $subscriberID['subscriber_id'];
        }
        if(!empty($subscriberArray)){
            $notificationTemplateArray = $this->Pu_N->reusable_model->get_search_result_data("push_notification_templates","*",array("notification_tag"=>CUSTOMER_UPDATE_BOOKING_PUSH_NOTIFICATION_EMPLOYEE_TAG),NULL,NULL,NULL,NULL,NULL,array());
            if(!empty($notificationTemplateArray[0])){
                $vendorName = $this->Pu_N->reusable_model->get_search_result_data("booking_details","service_centres.name",array("booking_details.booking_id"=>$bookingID),
                        array("service_centres"=>"service_centres.id=booking_details.assigned_vendor_id"),NULL,NULL,NULL,NULL,array());
                $data['title'] = vsprintf($notificationTemplateArray[0]['title'], array($vendorName[0]['name'],$bookingID));
                $data['msg'] = vsprintf($notificationTemplateArray[0]['msg'], array($bookingID,$vendorName[0]['name']));
                $data['subscriberArray'] = $subscriberArray;
                $data['url'] = base_url().'employee/booking/review_bookings';
                $data['notification_type'] = $notificationTemplateArray[0]['notification_type'];
                $sendUrl = base_url().'push_notification/send_pushcrew_notification';
               $this->Pu_N->asynchronous_lib->do_background_process($sendUrl, $data);
            }
        }   
    }
}
