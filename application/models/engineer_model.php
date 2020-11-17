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
    
    function delete_engineer_sign($where){
        if(!empty($where)){
            $this->db->where($where);
            $this->db->delete('engineer_table_sign');
        }
        log_message('info', __FUNCTION__ . '=> Delete engineer_table_sign : ' .$this->db->last_query());
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

    function count_all_review_engineer_action($post) {
        $this->_get_engineer_action_table_list($post, 'count( DISTINCT engineer_booking_action.id) as numrows');
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
    }



    function count_filtered_review_engineer_action($post) {
        $sfIDArray = array();
        $this->_get_engineer_action_table_list($post, 'count( DISTINCT engineer_booking_action.id) as numrows');
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
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
    
    function get_service_based_engineer($where, $select = "*"){
        $this->db->select($select);
        $this->db->join('engineer_appliance_mapping', 'engineer_appliance_mapping.engineer_id = engineer_details.id');
        if($where){
           $this->db->where($where);  
        }
        $query = $this->db->get("engineer_details");
        return $query->result_array();
    }
    
    function get_engineer_booking_details($select="*", $where = array(), $is_user = false, $is_service = false, $is_unit = false, $is_partner = false, $is_vendor = false, $is_sign = false, $is_symptom = false){
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
        if($is_sign){
            $this->db->join('engineer_table_sign', 'engineer_table_sign.booking_id = engineer_booking_action.booking_id'); 
        }
        if($is_symptom){
            $this->db->join('booking_symptom_defect_details', 'booking_symptom_defect_details.booking_id = engineer_booking_action.booking_id', 'left');
            $this->db->join('symptom', 'symptom.id = booking_symptom_defect_details.symptom_id_booking_creation_time', 'left');
        }
        $this->db->order_by("engineer_booking_action.closed_date", "DESC");
        $query = $this->db->get();
        //echo $this->db->last_query(); die();
        return $query->result_array();
    }
    
    function engineer_profile_data($enginner_id){
        $sql = "SELECT engineer_details.*, GROUP_CONCAT(services SEPARATOR ', ') as appliances, entity_identity_proof.identity_proof_type, entity_identity_proof.identity_proof_number, service_centres.name as company_name, district 
                FROM engineer_details 
                JOIN entity_identity_proof on entity_identity_proof.entity_id = engineer_details.id AND entity_identity_proof.entity_type = 'engineer' 
                JOIN engineer_appliance_mapping on engineer_appliance_mapping.engineer_id = engineer_details.id
                JOIN services on services.id = engineer_appliance_mapping.service_id
                JOIN service_centres on service_centres.id = engineer_details.service_center_id
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
            $where .= " and engineer_booking_action.current_status = '".trim($post_data['status'])."'";
        } 

        $sql = "SELECT 
                    booking_details.booking_id, booking_details.booking_address, booking_details.request_type, booking_details.booking_date, booking_details.count_escalation,
                    booking_details.booking_primary_contact_no, engineer_booking_action.internal_status,
                    users.name as username,
                    partners.public_name as partner_name,
                    services.services,
                    DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(booking_details.initial_booking_date, '%Y-%m-%d')) as age_of_booking,
                    (SELECT GROUP_CONCAT(DISTINCT brand.appliance_brand) FROM booking_unit_details brand WHERE brand.booking_id = booking_details.booking_id GROUP BY brand.booking_id ) as appliance_brand
                FROM 
                    booking_details
                    JOIN engineer_booking_action ON (engineer_booking_action.booking_id = booking_details.booking_id)
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
        $sql = "SELECT (select count(id) FROM booking_details WHERE DATEDIFF(STR_TO_DATE(initial_booking_date,'%Y-%m-%d'), CAST(service_center_closed_date AS date)) = 0 AND assigned_vendor_id=$sf_id AND assigned_engineer_id=$engineer_id AND current_status='Completed') as same_day_closure,"
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
            eb.`current_status`, eb.`internal_status`, partners.public_name as partner_name, bud.appliance_brand, eb.`cancellation_reason`, eb.`cancellation_remark`,
            eb.`closing_remark`, Date_format(str_to_date(bd.initial_booking_date,"%Y-%m-%d"),"%d-%b-%Y") , DATE_FORMAT(eb.closed_date,"%d-%b-%Y") , if(et.mismatch_pincode = 1, "No", "Yes") as pincode_matched
            FROM `engineer_booking_action` as eb JOIN service_centres as s on s.id = eb.`service_center_id`
            JOIN engineer_details as e on e.id = eb.`engineer_id` LEFT JOIN engineer_table_sign as et on et.booking_id = eb.booking_id
            JOIN booking_details as bd on bd.booking_id = eb.booking_id JOIN partners on partners.id = bd.partner_id
            JOIN booking_unit_details as bud on bud.booking_id = bd.booking_id
            WHERE eb.closed_date IS NOT NULL AND eb.closed_date >= "'.$start_date.'" AND eb.closed_date <= "'.$end_date.'" ORDER BY `eb`.`closed_date` DESC';
       
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    /*
     *@Desc - This function is used to get all booking details for viewing booking details completed by engineer from App
     *@param - $service_center_id, @engineer_id
     *@return - resultant array
     */ 
    function engineer_completed_bookings_details($booking_id){
        $sql = "SELECT engineer_booking_action.*, booking_details.booking_date, booking_details.booking_address, booking_details.state, booking_unit_details.appliance_brand, 
                services.services, booking_details.request_type, booking_pincode, booking_primary_contact_no, booking_details.booking_timeslot, booking_unit_details.appliance_category, 
                booking_unit_details.appliance_category, booking_unit_details.appliance_capacity, symptom.symptom, defect.defect, symptom_completion_solution.technical_solution
                FROM `engineer_booking_action` 
                JOIN `booking_details` ON `booking_details`.`booking_id` = `engineer_booking_action`.`booking_id`
                JOIN booking_unit_details ON booking_unit_details.booking_id = `engineer_booking_action`.`booking_id`
                JOIN services on services.id = booking_details.service_id
                LEFT JOIN symptom on symptom.id = engineer_booking_action.symptom
                LEFT JOIN defect on defect.id = engineer_booking_action.defect
                LEFT JOIN symptom_completion_solution ON symptom_completion_solution.id = engineer_booking_action.solution
                WHERE `engineer_booking_action`.`booking_id` = '".$booking_id."'";
        
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
     /*
     *@Desc - This function is used to get all bookings respected to the user data
     *@param - $service_center_id, @engineer_id
     *@return - resultant array
     */
    function engineer_bookings_on_user($phone_number, $engineer_id, $service_center_id){
        $sql = "SELECT DISTINCT services.services, users.phone_number, users.name as name, users.phone_number, booking_details.* "
             . "FROM (`users`) JOIN `booking_details` ON `booking_details`.`user_id` = `users`.`user_id` AND `booking_details`.`assigned_engineer_id` = '".$engineer_id."' AND `booking_details`.`assigned_vendor_id` = '".$service_center_id."' "
             . "JOIN `services` ON `services`.`id` = `booking_details`.`service_id`"
             . " WHERE `users`.`phone_number` = '".$phone_number."' OR booking_details.booking_primary_contact_no = '".$phone_number."' OR booking_details.booking_alternate_contact_no = '".$phone_number."'"
             . " ORDER BY `booking_details`.`create_date` DESC";
        
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
     /*
     *@Desc - This function is used to get engineer earning
     *@param - $service_center_id, @engineer_id
     *@return - resultant array
     */
    function get_en_incentive_details($select, $where){
        $this->db->select($select, false);
        $this->db->from("engineer_incentive_details");
        $this->db->where($where);
        $this->db->join("booking_details", "booking_details.id = engineer_incentive_details.booking_details_id");
        $this->db->join("services", "services.id = booking_details.service_id");
        $this->db->order_by("engineer_incentive_details.id", "DESC");
        $query = $this->db->get();
        //echo $this->db->last_query(); die();
        return $query->result_array();
    }
    
    /*
     *@Desc - This function is used to get booking details and engineer incentive details
     *@param - $select, @where
     *@return - resultant array
     */
    function get_booking_with_eng_incentive($select, $where){
        $this->db->select($select, false);
        $this->db->from("booking_details");
        $this->db->where($where);
        $this->db->join("engineer_incentive_details", "booking_details.id = engineer_incentive_details.booking_details_id", "left");
        $this->db->order_by("engineer_incentive_details.id", "DESC");
        $query = $this->db->get();
        //echo $this->db->last_query(); die();
        return $query->result_array();
    }
    
    /*@author Abhishek Awasthi
     *@Desc - This function is used to insert engineer incentive details
     *@param - $data
     *@return - resultant last insert id
     */
    function insert_eng_incentive_details($data){
        $this->db->insert("engineer_incentive_details", $data);
        return $this->db->insert_id();
    }
    
    /* @author Abhishek Awasthi
     *@Desc - This function is used to update engineer incentive details
     *@param - $data, $where
     *@return - 
     */
    function update_eng_incentive_details($data, $where=array(), $where_in=array()){
        if(!empty($where)){
            $this->db->where($where);
        }
        if(!empty($where_in)){ 
            $this->db->where_in("booking_details_id", $where_in);
        }
        else{
            
        }
        $this->db->update("engineer_incentive_details", $data);
    }


    /* @author Abhishek Awasthi
     *@Desc - This function is used to insert engineer notification details
     *@param - $data
     *@return - 
     */

    function insert_engg_notification_data($data){
        $this->db->insert("engineer_notification_detail", $data);
        return $this->db->insert_id();

    }

  /* @author Abhishek Awasthi
     *@Desc - This function is used to update engineer notification details
     *@param - $data, $id
     *@return - 
     */
    function update_engg_notification_data($data,$id){
        $this->db->where('id',$id);
        $this->db->update("engineer_notification_detail", $data);
        return $this->db->insert_id();

    }



  /* @author Abhishek Awasthi
     *@Desc - This function is used to get spare data 
     *@param - $spare id
     *@return - Row
     */
   function get_spare_details($select,$where){
         $this->db->select($select); 
         $this->db->where($where);
         $this->db->from('spare_parts_details');
         $query = $this->db->get();
         return $query->result_array();

   }


  /*  @author Abhishek Awasthi
     *@Desc - This function is used to insert engineer notification details
     *@param - $select, $where
     *@return - 
     */

    function get_engg_notification_data($select,$where){

         $this->db->select($select); 
         $this->db->where($where);
         $this->db->order_by("id", "desc"); /// Order in decresing order in notifications //
         $this->db->from('engineer_notification_detail');
         $query = $this->db->get();
         return $query->result_array();

    }

    /* @author Abhishek Awasthi
     *@Desc - This function is used to get partners
     *@param - $select, $where
     *@return - 
     */


    function get_partnersList_data($select,$where){

         $this->db->select($select); 
         $this->db->where($where);
         $this->db->from('partners');
         $query = $this->db->get();
         return $query->result_array();

    }


    /* @author Abhishek Awasthi
     *@Desc - This function is used to get partners appliances
     *@param - $select, $where
     *@return - 
     */


    function get_partner_appliances($select,$where){

         $this->db->distinct();
         $this->db->select($select); 
         $this->db->where($where);
         $this->db->from('services');
         $this->db->join('partner_appliance_details', 'partner_appliance_details.service_id = services.id');
         $query = $this->db->get();
         return $query->result_array();

    }


    /* @author Abhishek Awasthi
     *@Desc - This function is used to get partners app models
     *@param - $select, $where
     *@return - 
     */


   function  getPartner_appliancesModels($select,$where){

         $this->db->distinct();
         $this->db->select($select); 
         $this->db->where($where);
         $this->db->from('appliance_model_details');
         $query = $this->db->get();
         return $query->result_array();

   }


    /* @author Abhishek Awasthi
     *@Desc - This function is used to spare with spare id
     *@param - $spare id
     *@return - Row
     */


   function check_cancell_allowed($spare){

         $this->db->select("*"); 
         $this->db->where('id',$spare);
         $this->db->from('spare_parts_details');
         $query = $this->db->get();
         return $query->result_array();

   }


    /* @author Abhishek Awasthi
     *@Desc - This function is used to get booking data
     *@param - $spare id
     *@return - Row
     */


   function get_booking_details($select,$where){

         $this->db->select($select); 
         $this->db->where($where);
         $this->db->from('booking_details');
         $query = $this->db->get();
         return $query->result_array();

   }


    /* @author Abhishek Awasthi
     *@Desc - This function is used to get parts data
     *@param - $select ,$where
     *@return - Array
     */


   function getPartner_appliancesInventoryData($select,$where){
        $this->db->_protect_identifiers = FALSE; /// Apply protected identifiers //
         $this->db->distinct();
         $this->db->select($select); 
         $this->db->where($where);
         $this->db->from('inventory_master_list');
         $query = $this->db->get();
         return $query->result_array();

   }


    /* @author Abhishek Awasthi
     *@Desc - This function is used to get config data
     *@param - $config_type
     *@return - Row
     */

 function get_engineer_config($config_type){

         $this->db->select("*"); 
         $this->db->where('configuration_type',$config_type);
         $this->db->from('engineer_configs');
         $query = $this->db->get();
         return $query->result();


 }


    /* @author Abhishek Awasthi
     *@Desc - This function is used to update config
     *@param -  
     *@return - json
     */


   function update_config_data($data,$where){

    $this->db->where($where);
    $this->db->update('engineer_configs',$data);
    if($this->db->affected_rows() > 0){
        return TRUE;
    }else{
        return FALSE;
    }


   }




/* @author Abhishek Awasthi
     *@Desc - This function is used to get all incentives of a engineer
     *@param -  
     *@return - Array
*/

   function get_engineer_incentives($select,$where){

    $this->db->select($select);
    $this->db->where($where);
    $this->db->from('booking_details');
    $this->db->join('engineer_incentive_details','booking_details.id=engineer_incentive_details.booking_details_id');
    $this->db->order_by('engineer_incentive_details.create_date','DESC');
    $query = $this->db->get();
    return $query->result();


   }


   /* @author Abhishek Awasthi
     *@Desc - This function is used to get all  engineers which are active
     *@param -  
     *@return - Array
*/

   function get_active_engineers($select,$where){

    $this->db->select($select);
    $this->db->where($where);
    $this->db->from('engineer_details');
    $query = $this->db->get();
    return $query->result();


   }

/* @author Abhishek Awasthi
     *@Desc - This function is used to get all  app installed engineers
     *@param -  
     *@return - Array
*/
   function getinstalls($where){

    $this->db->select("*");
    $this->db->where($where);
    $this->db->from('engineer_details');
    $query = $this->db->get();
    return $query->result();

   }

/* @author Abhishek Awasthi
     *@Desc - This function is used to get consumption status 
     *@param -  
     *@return - Array
*/
   function get_consumption_status_spare($where){

    $this->db->select("*");
    $this->db->where($where);
    $this->db->from('spare_consumption_status');
    $query = $this->db->get();
    return $query->result();

   }


       /**
     *  @desc : This function is used to get engineer Booking History
     *  @param : $post string
     *  @param : $select string
     *  @param : 
     *  @return: Array()
     */
    function get_engineer_history_list($post, $select = "") {
        $this->_get_engineer_history_list($post, $select);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        $query = $this->db->get();

        $result = $query->result_array();


        return $result;
    }


        /**
     * @Desc: This function is used to get  engineer Booking History
     * @params: $post array
     * @params: $select string
     * @Author : Abhishek Awasthi
     * @return: void
     * 
     */
    function _get_engineer_history_list($post, $select) {
        $this->db->distinct();
        $this->db->select($select, FALSE);
        $this->db->join('service_centres','booking_details.assigned_vendor_id = service_centres.id');
        $this->db->join('engineer_details','booking_details.assigned_engineer_id = engineer_details.id');
 
        $this->db->from('booking_details');
        if (!empty($post['where'])) {
            $this->db->where($post['where']);
        }

        if (!empty($post['search_value'])) {
            $like = "";
            foreach ($post['column_search'] as $key => $item) { // loop column 
                // if datatable send POST for search
                if ($key === 0) { // first loop
                    $like .= "( " . $item . " LIKE '%" . $post['search_value'] . "%' ";
                } else {
                    $like .= " OR " . $item . " LIKE '%" . $post['search_value'] . "%' ";
                }
            }
            $like .= ") ";

            $this->db->where($like, null, false);
        }

        if (!empty($post['order'])) {
            $this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
        } else {
            $this->db->order_by('booking_details.id', 'desc');
        }

        if (!empty($post['group_by'])) {
            $this->db->group_by($post['group_by']);
        }
        if (isset($post['having']) && !empty($post['having'])) {
            $this->db->having($post['having'], FALSE);
        }
    }

    /**
     *  @desc : This function is used to get total bookings count
     *  @param : $post string
     *  @return: Array()
     */
    public function count_all_engineer_history($post) {
        $this->_get_engineer_history_list($post, 'count( DISTINCT booking_details.id) as numrows');
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
    }

    /**
     *  @desc : This function is used to get total filtered bookings
     *  @param : $post string
     *  @return: Array()
     */
    function count_filtered_engineer_history($post) {
        $sfIDArray = array();
        $this->_get_engineer_history_list($post, 'count( DISTINCT booking_details.id) as numrows');
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
    }





}
