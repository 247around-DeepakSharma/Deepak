<?php

class push_notification_model extends CI_Model {
 function __construct() {
        parent::__Construct();
    }
    function get_push_notification_subscribers_by_entity($entityType,$entityID=NULL){
        $finalArray = array();
        $this->db->select('COUNT(subscriber_id) as subscription_count, (CASE WHEN subscriber_id = -1 THEN "Blocked" ELSE "Subscription" END) as subscription_type,entity_id');
        $this->db->where(array("entity_type"=>$entityType));
        $this->db->group_by("entity_id,subscription_type");
         if(!empty($entityID)){
            $this->db->where(array("entity_id"=>$entityID));
        }
        $query = $this->db->get("push_notification_subscribers");
        $data = $query->result_array();
        foreach($data as $subscriberData){
            if($subscriberData['subscription_type'] == 'Blocked'){
                $finalArray[$subscriberData['entity_id']]['blocked_count'] = $subscriberData['subscription_count'];
            }
            if($subscriberData['subscription_type'] == 'Subscription'){
                $finalArray[$subscriberData['entity_id']]['subscription_count'] = $subscriberData['subscription_count'];
            }
        }
        return $finalArray;
    }
    /*
     * This function is used to get subscriber id by entity_type
     */
    function get_subscriber_by_entity_types($entityTypesArray){
        $this->db->select('DISTINCT(subscriber_id)');
        $this->db->where_in("entity_type",$entityTypesArray);
        $this->db->where_not_in('subscriber_id', -1);
        $query = $this->db->get("push_notification_subscribers");
        $data = $query->result_array();
        return $data;
    }
    /*
     * This function is used to get notification template by template ID
     */
    function get_push_notification_template($templateTag){
        $this->db->select('*');
        $this->db->where(array("notification_tag"=>$templateTag));
        $query = $this->db->get("push_notification_templates");
        return $query->result_array();
    }
    /*
     * This Function is used to get subscriber id on combination of entity_type and entity_id
     * @input $entityIDTypeArray - array('employee'=>array('1','2'),'vendor'=>array('1','2','11'))
     */
    function get_subscriberID_by_entity_type_and_entity_id($entityIDTypeArray){
        $tempArray = array();
        foreach($entityIDTypeArray as $entity_type=>$entity_ID_array){
            $tempArray[] = "(entity_type='".$entity_type."' AND entity_id IN (".implode(",",$entity_ID_array)."))";
        }
      $sql = "SELECT DISTINCT(subscriber_id) FROM push_notification_subscribers WHERE ".implode(" OR ",$tempArray)." AND subscriber_id !=-1";
       $query = $this->db->query($sql);
       return $query->result_array();
    }
}