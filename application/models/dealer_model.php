<?php

class Dealer_model extends CI_Model {
    var $dealer_search_column = array('dealer_phone_number_1','owner_phone_number_1');
    
    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }

    /**
     * @desc: check dealer login 
     * @param: Array(userid, password)
     * @return : Array()  
     */
    function entity_login($data) {
        $this->db->select('*');
        $this->db->where($data);
        $query = $this->db->get('entity_login_table');
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        } else {

            return false;
        }
    }
    


    function get_dealer_mapping_details($condition, $select){
        $this->db->distinct();
        $this->db->select($select);
        $this->db->from('dealer_details');
        $this->db->join('dealer_brand_mapping', 'dealer_details.dealer_id = dealer_brand_mapping.dealer_id',"Left");
        $this->db->join('services', 'services.id = dealer_brand_mapping.service_id');
        $this->db->join('partners', 'partners.id = dealer_brand_mapping.partner_id');
        
        if(!empty($condition['where'])){
             $this->db->where($condition['where']);
        }
        
        if (isset($condition['where_in'])) {
            foreach ($condition['where_in'] as $index => $value) {
                $this->db->where_in($index, $value);
            }
        }
        
        if (!empty($condition['search'])) {
            $key = 0;
            $like = "";
            foreach ($condition['search'] as $index => $item) {
                if ($key === 0) { // first loop
                   // $this->db->like($index, $item);
                    $like .= "( ".$index." LIKE '%".$item."%' ";
                } else {
                    $like .= " OR ".$index." LIKE '%".$item."%' ";
                }
                $key++;
            }
            $like .= ") ";

            $this->db->where($like, null, false);
        }
        
        if (!empty($condition['search_value'])) {
            $like = "";
            foreach ($condition['column_search'] as $key => $item) { // loop column 
                // if datatable send POST for search
                if ($key === 0) { // first loop
                    $like .= "( " . $item . " LIKE '%" . $condition['search_value'] . "%' ";
                } else {
                    $like .= " OR " . $item . " LIKE '%" . $condition['search_value'] . "%' ";
                }
            }
            $like .= ") ";

            $this->db->where($like, null, false);
        }
        if(isset($condition['length'])){
            if ($condition['length'] != -1) {
                $this->db->limit($condition['length'], $condition['start']);
            }
        }
        
        if(!empty($condition['order_by'])){
            $this->db->order_by($condition['order_by']);
        }else if(!empty ($condition['order'])){
            $this->db->order_by($condition['column_order'][$condition['order'][0]['column']], $condition['order'][0]['dir']);
        }
        
        $query = $this->db->get();
        //log_message("info",__METHOD__. $this->db->last_query());
        return $query->result_array();
    }
    
    /**
     * @desc: This is used to insert the dealer details into database
     * @param Array $data
     * @return Array
     */
    function insert_dealer_details($data){
        $this->db->insert("dealer_details", $data);
        $insert_id = $this->db->insert_id();
        return  $insert_id;
    }
    
    function insert_dealer_mapping_batch($data){
        $this->db->insert_batch("dealer_brand_mapping", $data);
        $insert_id = $this->db->insert_id();
        return  $insert_id;
    }
    
    function insert_entity_login($data){
        $this->db->insert("entity_login_table", $data);
        $insert_id = $this->db->insert_id();
        return  $insert_id;
    }
    
     /**
     * @desc: This is used insert in retailer login table
     * @param $data array
     * @param $where array
     * @return boolean
     */
    function processUserRegisterRetailer($data){
         $this->db->insert("retailer_login", $data);
        $insert_id = $this->db->insert_id();
        return  $insert_id;  
        
    }
    
 
   /**
     * @desc: This is used to retailer login
     * @param $data array
     * @param $where array
     * @return boolean
    * 
     */      
    function retailer_login($data) {
        $this->db->select('*');
        $this->db->where($data);
        $query = $this->db->get('retailer_login');
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        } else {

            return false;
        }
    }
    
    
        /**
     * @desc: This is used to update the dealer details
     * @param $data array
     * @param $where array
     * @return boolean
     */
    function update_retailer($data,$where){
        $this->db->where($where);
        return $this->db->update('retailer_login', $data);
    }
    
    
    
    /**
     * @desc: This is used to get the dealer details by any 
     * @param $select string
     * @param $where array
     * @return Array
     */
    function get_dealer_details($select , $where=""){
        if($where !== ''){
            $this->db->where($where);
        }
        $this->db->select($select);
        $this->db->from('dealer_details');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * @desc: This is used to get the dealer brand mapping details by dealer id
     * @param $dealer_id string
     * @return Array
     */
    function get_dealer_brand_mapping_details($dealer_id){
        $sql = "SELECT p.id,p.public_name,s.services,dbm.brand "
                . "FROM dealer_brand_mapping AS dbm "
                . "LEFT JOIN partners AS p ON dbm.partner_id = p.id "
                . "LEFT JOIN services AS s ON dbm.service_id = s.id "
                . "WHERE dealer_id = '$dealer_id'";
        $query= $this->db->query($sql);
        return $query->result_array();
    }
    
    /**
     * @desc: This is used to update the dealer details
     * @param $data array
     * @param $where array
     * @return boolean
     */
    function update_dealer($data,$where){
        $this->db->where($where);
        return $this->db->update('dealer_details', $data);
    }
    
    /**
     * @desc: This is used to update the dealer brand mapping
     * @param $data array
     * @param $where array
     * @return boolean
     */
    function update_dealer_brand_mapping($data,$where){
        $this->db->where($where);
        $this->db->update('dealer_brand_mapping', $data);
        if($this->db->affected_rows() > 0){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    
    /**
     * @desc: This is used to update the dealer brand mapping
     * @param $data array
     * @param $where array
     * @return boolean
     */
    function delete_dealer_brand_mapping($where){
        if(!empty($where))
        {
            $this->db->where($where);
            $this->db->delete('dealer_brand_mapping');
            if($this->db->affected_rows() > 0 ){
                return TRUE;
            }else{
                return FALSE;
            }
        }
        else
             return false;
    }
    function get_entity_login_details($data){
      $this->db->select("entity_role.is_filter_applicable,entity_role.role,contact_person.id,contact_person.official_email,"
              . "entity_role.department,contact_person.entity_id,entity_login_table.agent_id,contact_person.name");
      $this->db->where($data);
      $this->db->join("contact_person","entity_login_table.contact_person_id = contact_person.id");
      $this->db->join("entity_role","entity_role.id = contact_person.role");
      $this->db->from('entity_login_table');
      $query = $this->db->get();
      return $query->result_array();
    }





    /*
     * @Desc - This function used to get dealer state mapping Data
     * @param - $dealer
     * @response - Array
     * @Author - Abhishek Awasthi
     */

    function getDealerStates($dealer){
        $state =array();
        ///  Will come when mappping is done //
        return  $states;
    }

    /*
     * @Desc - This function used to get dealer state cities mapping Data
     * @param - $entity, $state_code
     * @response - Array
     * @Author - Abhishek Awasthi
     */

    function getDealerStatesCities($entity, $state_code){
        $cities =array();
         ///  Will come when mappping is done //
         return  $cities;

    }


    /*
     * @Desc - This function used to get dealer state cities mapping Data
     * @param - $entity, $state_code
     * @response - Array
     * @Author - Abhishek Awasthi
     */
    function dealer_partner_bookings_on_user($phone_number,$enitity_id,$entity_type){

        if($entity_type == _247AROUND_DEALER_STRING){

         $sql = "SELECT DISTINCT services.services, users.phone_number, users.name as name, users.phone_number, booking_details.* "
             . "FROM (`users`) JOIN `booking_details` ON `booking_details`.`user_id` = `users`.`user_id` AND `booking_details`.`dealer_id` = '".$enitity_id."'"
             . "JOIN `services` ON `services`.`id` = `booking_details`.`service_id`"
             . " WHERE `users`.`phone_number` = '".$phone_number."' OR booking_details.booking_primary_contact_no = '".$phone_number."' OR booking_details.booking_alternate_contact_no = '".$phone_number."'"
             . " ORDER BY `booking_details`.`create_date` DESC";

        }else{

         $sql = "SELECT DISTINCT services.services, users.phone_number, users.name as name, users.phone_number, booking_details.* "
             . "FROM (`users`) JOIN `booking_details` ON `booking_details`.`user_id` = `users`.`user_id` AND `booking_details`.`partner_id` = '".$enitity_id."'"
             . "JOIN `services` ON `services`.`id` = `booking_details`.`service_id`"
             . " WHERE `users`.`phone_number` = '".$phone_number."' OR booking_details.booking_primary_contact_no = '".$phone_number."' OR booking_details.booking_alternate_contact_no = '".$phone_number."'"
             . " ORDER BY `booking_details`.`create_date` DESC";
        }

        
        $query = $this->db->query($sql);
        return $query->result_array();

    }
     /**
     * @desc: This is used to update the dealer details
     * @param $data array
     * @param $where array
     * @return boolean
     */
    function fetch_retailer_detail($select,$where=array()){
        if($where !== ''){
           $this->db->where($where);
        }
        $this->db->select($select);
        $this->db->from('retailer_login');
        $query = $this->db->get();
        return $query->result_array();
    }



}