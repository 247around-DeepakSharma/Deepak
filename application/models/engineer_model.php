<?php

class Engineer_model extends CI_Model {
    
    var $order = array('booking_unit_details.product_or_services' => 'desc'); // default order 

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }
    
         
     function insert_engineer_action($data){
         $this->db->insert("engineer_booking_action", $data);
          return $this->db->insert_id();
     }
     
    /**
     * @desc This is used to delete booking id from Engineer Action table
     * @param type $booking_id
     */
    function delete_booking_from_engineer_table($booking_id) {
        if (!empty($booking_id) || $booking_id != "0") {
            $result = $this->getengineer_action_data("booking_id",array("booking_id" => $booking_id));
           
            if (!empty($result)) {
                log_message('info', __METHOD__ . "=> Booking ID: " . $booking_id . "=> Old vendor data " .
                        print_r($result, TRUE));

                $this->db->where('booking_id', $booking_id);
                $this->db->delete("engineer_booking_action");
                log_message('info', __METHOD__ . "=> Delete SQL: " . $this->db->last_query());
            }
        }
    }
    
    function delete_engineer_table($where){
        if(!empty($where)){
            $this->db->where($where);
            $this->db->delete('engineer_booking_action');
        }
        log_message('info', __FUNCTION__ . '=> Delete sc unit details: ' .$this->db->last_query());
    }
    /**
     * @desc This is used to select engineer action data 
     * @param String $select
     * @param Array $where
     * @return Array
     */
    function getengineer_action_data($select, $where){
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get("engineer_booking_action");
        return $query->result_array();
    }
    
    function getengineer_sign_table_data($select, $where){
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get("engineer_table_sign");
        return $query->result_array();
    }
    
    function update_engineer_table($data, $where){
        $this->db->where($where);
        $this->db->update("engineer_booking_action", $data);
    }
    
    function update_engineer_action_sig($data, $where){
        $this->db->where($where);
        $this->db->update("engineer_table_sign", $data);
    }
    
    function insert_engineer_action_sign($data){
        $this->db->insert("engineer_table_sign", $data);
        return $this->db->insert_id();
    }
    
    function get_engineer_sign($select, $where){
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get("engineer_table_sign");
        return $query->result_array();
        
    }
     
     function get_engineer_action_table_list($post, $select) {
        $this->_get_engineer_action_table_list($post, $select);
        if(isset($post['length'])){
            if ($post['length'] != -1) {
                $this->db->limit($post['length'], $post['start']);
            }
        }
        $query = $this->db->get();

        return $query->result();
    }
    
   
    public function _get_engineer_action_table_list($post, $select) {
        $this->db->from('engineer_booking_action');
        $this->db->distinct();
        $this->db->select($select);
        $this->db->join('booking_details', 'booking_details.booking_id = engineer_booking_action.booking_id '
                . ' AND booking_details.assigned_vendor_id = engineer_booking_action.service_center_id ');
       
        $this->db->join('service_center_booking_action', 'engineer_booking_action.booking_id = service_center_booking_action.booking_id '
                . ' AND service_center_booking_action.service_center_id = engineer_booking_action.service_center_id '
                . ' AND service_center_booking_action.unit_details_id = engineer_booking_action.unit_details_id');
        
         $this->db->join('booking_unit_details', 'booking_unit_details.booking_id = service_center_booking_action.booking_id '
                . ' AND booking_unit_details.id = service_center_booking_action.unit_details_id ');
        
        $this->db->join('engineer_table_sign', 'booking_details.booking_id = engineer_table_sign.booking_id '
                . ' AND booking_details.assigned_vendor_id = engineer_table_sign.service_center_id ', 'left');
       
        $this->db->join('users', 'users.user_id = booking_details.user_id', 'left');

        $this->db->join('services', 'services.id = booking_details.service_id');
        if (!empty($post['where'])) {
            $this->db->where($post['where'], FALSE);
        }
        if (isset($post['where_in'])) {
            foreach ($post['where_in'] as $index => $value) {

                $this->db->where_in($index, $value);
            }
        }

        if (!empty($post['search_value'])) {
            $like = "";
            foreach ($post['column_search'] as $key => $item) { // loop column 
                // if datatable send POST for search
                if ($key === 0) { // first loop
                    $like .= "( " . $item . " LIKE '%" . $post['search_value'] . "%' ";
                    // $this->db->like($item, $post['search_value']);
                } else {
                    $like .= " OR " . $item . " LIKE '%" . $post['search_value'] . "%' ";
                    //$this->db->or_like($item, $post['search_value']);
                }
            }
            $like .= ") ";

            $this->db->where($like, null, false);
        }

        if (!empty($post['order'])) { // here order processing
            $this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
    
    function get_engineers_details($where, $select){
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get("engineer_details");
        return $query->result_array();
        
    }
    
    /* 
     * @Desc - This function is used to insert data into engineer_appliance_mapping table      
     */
    function insert_engineer_appliance_mapping($data){
        $this->db->insert_batch("engineer_appliance_mapping", $data);
        return $this->db->insert_id();
    }
    
     /* 
     * @Desc - This function is used to get data from engineer_appliance_mapping table      
     */
    function get_engineer_appliance($where, $select='*'){
        $this->db->select($select);
        if(!empty($where)){
           $this->db->where($where); 
        }
        $query = $this->db->get("engineer_appliance_mapping");
        return $query->result_array();
    }
    
     /* 
     * @Desc - This function is used to update data from engineer_appliance_mapping table      
     */
    function update_engineer_appliance($where, $data){
        $this->db->where($where);
        $this->db->update("engineer_appliance_mapping", $data);
    }
    
    function update_engineer_appliance_mapping($engineer_id, $services){
        $this->update_engineer_appliance(array("engineer_id"=>$engineer_id), array("is_active"=>0));
        foreach ($services as $key => $value) {
            $data = array();
            $where = array(
                "engineer_id" => $engineer_id,
                "service_id" => $value,
            );
            
            $check_service = $this->get_engineer_appliance($where, "id");
            if(empty($check_service)){
                array_push($data, $where);
                $this->insert_engineer_appliance_mapping($data);
            }
            else{
                $this->update_engineer_appliance(array("id"=>$check_service[0]['id']), array("is_active"=>1));
            }
        }
    }
    
    function get_service_based_engineer($where, $select = "*"){
        $this->db->select($select);
        $this->db->join('engineer_appliance_mapping', 'engineer_appliance_mapping.engineer_id = engineer_details.id');
        if($where){
           $this->db->where($where);  
        }
        $query = $this->db->get("engineer_details");
        return $query->result_array();
    }
    
    function get_engineer_booking_details($select="*", $where = array(), $is_user = false, $is_service = false, $is_unit = false, $is_partner = false, $is_vendor = false){
        $this->db->distinct();
        $this->db->select($select, false);
        $this->db->from('engineer_booking_action');
        $this->db->where($where);
        $this->db->join("booking_details", "booking_details.booking_id = engineer_booking_action.booking_id");
        $this->db->join("service_center_booking_action", "service_center_booking_action.booking_id = engineer_booking_action.booking_id");
        if($is_service){
            $this->db->join("services", "services.id = booking_details.service_id");
        }
        if($is_user){
            $this->db->join('users',' users.user_id = booking_details.user_id');
        }
        if($is_unit){
            $this->db->join('booking_unit_details', 'booking_unit_details.booking_id = booking_details.booking_id');
        }
        if($is_partner){
            $this->db->join('partners', 'booking_details.partner_id = partners.id'); 
        }
        if($is_vendor){
            $this->db->join('service_centres', 'booking_details.assigned_vendor_id = service_centres.id'); 
        }
        $query = $this->db->get();
        //echo $this->db->last_query(); die();
        return $query->result_array();
    }
    
    function engineer_profile_data($enginner_id){
        $sql = "SELECT engineer_details.*, GROUP_CONCAT(services SEPARATOR ', ') as appliances, entity_identity_proof.identity_proof_type, entity_identity_proof.identity_proof_number 
                FROM engineer_details 
                JOIN entity_identity_proof on entity_identity_proof.entity_id = engineer_details.id AND entity_identity_proof.entity_type = 'engineer' 
                JOIN engineer_appliance_mapping on engineer_appliance_mapping.engineer_id = engineer_details.id
                JOIN services on services.id = engineer_appliance_mapping.service_id
                WHERE engineer_details.id = '".$enginner_id."' GROUP BY engineer_appliance_mapping.engineer_id";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
        
    function get_engineer_vise_call_list($post_data) {
       
        $where = '';
        
        if ($post_data['length'] != -1) {
            $this->db->limit($post_data['length'], $post_data['start']);
        }
        
        if(!empty($post_data['engineer_id'])) {
            $where .= " and assigned_engineer_id = ".trim($post_data['engineer_id']);
            //$where .= " and assigned_engineer_id = 24700001";
            
        } 
        if(!empty($post_data['status'])) {
            $where .= " and current_status = '".trim($post_data['status'])."'";
        } 
        
        $sql = "SELECT 
                    booking_details.*,
                    users.name as username,
                    partners.public_name as partner_name,
                    services.services,
                    DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(booking_details.initial_booking_date, '%d-%m-%Y')) as age_of_booking,
                    (SELECT GROUP_CONCAT(DISTINCT brand.appliance_brand) FROM booking_unit_details brand WHERE brand.booking_id = booking_details.booking_id GROUP BY brand.booking_id ) as appliance_brand
                FROM 
                    booking_details 
                    LEFT JOIN partners ON (booking_details.partner_id = partners.id)
                    LEFT JOIN users ON (booking_details.user_id = users.user_id)
                    LEFT JOIN services ON (booking_details.service_id = services.id)
                WHERE 
                    1 {$where}";
                     
        $query = $this->db->query($sql);
        return $query->result_array();
        
    }  
    
    function get_engineer_rating($engineer_id, $sf_id){
        $sql = "SELECT ROUND(AVG(rating_stars)) as rating
                FROM booking_details WHERE assigned_vendor_id = '$sf_id'
                AND assigned_engineer_id = '$engineer_id'
                AND rating_stars IS NOT NULL AND current_status = '"._247AROUND_COMPLETED."'";    
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    function get_engineer_D0_closure($engineer_id, $sf_id){
        $sql = "SELECT (select count(id) FROM booking_details WHERE DATEDIFF(STR_TO_DATE(initial_booking_date,'%d-%m-%Y'), CAST(service_center_closed_date AS date)) = 0 AND assigned_vendor_id=$sf_id AND assigned_engineer_id=$engineer_id AND current_status='Completed') as same_day_closure,"
                . " (Select Count(id) From booking_details WHERE assigned_vendor_id=$sf_id AND assigned_engineer_id=$engineer_id AND current_status='Completed') as total_closure from booking_details limit 1";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
     /*
    *@Desc - This function is used to download booking details closed by engineer
    */
    function get_engineer_closed_bookings($vendors, $start_date, $end_date){
        $vendor_ids = "";
        if(!in_array("All", $vendors)){
            $vendor_ids = "service_centres.id IN (";
            foreach ($vendors as $value) {
                $vendor_ids .= "'".$value."',";
            }
            $vendor_ids = rtrim($vendor_ids, ",");
            $vendor_ids .= ") AND";
        }
        
        $sql = 'SELECT DISTINCT(eb.booking_id), s.name as service_center_name, e.name as engineer_name, 
            eb.`current_status`, eb.`internal_status`, eb.`cancellation_reason`, eb.`cancellation_remark`,
            eb.`closing_remark`, eb.closed_date, if(et.mismatch_pincode = 1, "No", "Yes") as pincode_matched
            FROM `engineer_booking_action` as eb JOIN service_centres as s on s.id = eb.`service_center_id`
            JOIN engineer_details as e on e.id = eb.`engineer_id` LEFT JOIN engineer_table_sign as et on et.booking_id = eb.booking_id
            WHERE eb.closed_date IS NOT NULL AND eb.closed_date >= "'.$start_date.'" AND eb.closed_date <= "'.$end_date.'" ORDER BY `eb`.`closed_date` DESC';
       
        $query = $this->db->query($sql);
        return $query->result_array();
    }
}