<?php

class push_notification_model extends CI_Model {
 function __construct() {
        parent::__Construct();
    }
    function get_push_notification_subscribers_by_entity($entityType,$entityID=NULL){
        $finalArray = array();
        $this->db->select('entity_id,COUNT(subscriber_id) as subscription_count, (CASE WHEN subscriber_id = -1 THEN "Blocked" ELSE "subscribe" END) as "blocked_status",'
                . '(CASE WHEN unsubscription_flag = 1 THEN "unsubscribe" ELSE "subscribe" END) as "subscription_status"');
        $this->db->where(array("entity_type"=>$entityType));
        $this->db->group_by("entity_id,subscription_status,blocked_status");
         if(!empty($entityID)){
            $this->db->where(array("entity_id"=>$entityID));
        }
        $query = $this->db->get("push_notification_subscribers");
        $data = $query->result_array();
        foreach($data as $subscriberData){
            if($subscriberData['blocked_status'] == 'subscribe' && $subscriberData['subscription_status'] == 'subscribe'){
                $finalArray[$subscriberData['entity_id']]['subscription_count'] = $subscriberData['subscription_count'];
            }
            else{
                if($subscriberData['blocked_status'] == 'Blocked'){
                    $finalArray[$subscriberData['entity_id']]['blocked_count'] = $subscriberData['subscription_count'];
                }
                else{
                    $finalArray[$subscriberData['entity_id']]['unsubscription_count'] = $subscriberData['subscription_count'];
                } 
            }
        }
        return $finalArray;
    }
    /*
     * This function is used to get subscriber id by entity_type
     */
    function get_subscriber_by_entity_types($entityTypesArray){
        $where['unsubscription_flag'] = 0;
        $where['is_valid'] = 1;
        $this->db->select('DISTINCT(subscriber_id)');
        $this->db->where_in("entity_type",$entityTypesArray);
        $this->db->where_not_in('subscriber_id', -1);
        $this->db->where($where);
        $query = $this->db->get("push_notification_subscribers");
        $data = $query->result_array();
        return $data;
    }
    /*
     * This function is used to get notification template by template ID
     */
    function get_push_notification_template($templateTag){
        $this->db->select('*');
        $this->db->where(array("notification_tag"=>$templateTag,"active"=>1));
        $query = $this->db->get("push_notification_templates");
        return $query->result_array();
    }
    /*
     * This Function is used to get subscriber id on combination of entity_type and entity_id
     * @input $entityIDTypeArray = array('employee'=>array('1','2'),'vendor'=>array('1','2','11'))
     */
    function get_subscriberID_by_entity_type_and_entity_id($entityIDTypeArray){
        $tempArray = array();
        $strWhere = "";
        foreach($entityIDTypeArray as $entity_type=>$entity_ID_array){
            if(!empty($entity_ID_array))
            {
                $tempArray[] = "(entity_type='".$entity_type."' AND entity_id IN (".implode(",",$entity_ID_array)."))";
            }
        }
        if(!empty($tempArray)){
            $strWhere = " AND ".implode(" OR ",$tempArray);
        }
        
        $sql = "SELECT DISTINCT(subscriber_id) FROM push_notification_subscribers WHERE 1 ".$strWhere." AND subscriber_id !=-1 AND unsubscription_flag =0 AND is_valid=1";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    function get_booking_data($bookingIDArray){
         $this->db->select('booking_details.booking_id,booking_details.partner_id,service_center_booking_action.internal_status');
         $this->db->join('service_center_booking_action', 'service_center_booking_action.booking_id = booking_details.booking_id');
        $this->db->where_in("booking_details.booking_id",$bookingIDArray);
        $query = $this->db->get("booking_details");
        return $query->result_array();
    }
    function update_push_notification_unsubscription($subscriber_id,$where){
            $this->db->set('unsubscription_flag', '1');
            $this->db->set('unsubscription_date', date('Y-m-d h:i:s'));
            $this->db->where($where);
            $this->db->update('push_notification_subscribers');
    }
}