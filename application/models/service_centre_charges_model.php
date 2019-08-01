<?php

class service_centre_charges_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }

    /**
   *  @desc : This function is to insert service centre charges from excel
   *  @param : all the service centre charges
   *  @return : void
   */
    function insert_service_centre_charges($details) {
        $this->db->insert('service_centre_charges', $details);
    }

     /**
   *  @desc : This function is to select service centre charges 
   *  @param : service id
   *  @return : all the service centre charges for particular service
   */
    public function get_prices_for_particular_appliance($service_id)
    {
		$this->db->select('*');
		$this->db->from('service_centre_charges');
		$this->db->where(array('service_id' => $service_id));
		$this->db->order_by('category ASC, capacity ASC');
		$query = $this->db->get();

		return $query->result();
    }

    function insert_data_in_temp($table_name, $rows){
      return $this->db->insert_batch($table_name, $rows);
    }

    function switch_temp_table($table_name){
      $this->load->dbforge();

      //rename original to temp2 since there is no efficient copy table command
      $this->dbforge->rename_table($table_name, $table_name."2");

      //rename temp to original
      $this->dbforge->rename_table($table_name."_tmp", $table_name);

      //rename temp2 to temp
      $this->dbforge->rename_table($table_name."2", $table_name."_tmp");

      //truncate temp
      $this->db->empty_table($table_name."_tmp");

    }

    function getPartnerID($source){

      $this->db->select('id');
      $this->db->where('source', $source);
      $query = $this->db->get('bookings_sources');
      $data = $query->result_array();
      if ($query->num_rows() > 0) {
          return $data[0]['id'];
      } else {

        return '';
      }

    }
    
    function get_service_city_source_all_appliances_details(){
        $query = $this->db->query("Select id,services from services where isBookingActive='1'");
        $query1['services'] = $query->result_array();
        
        $this->db->distinct();
        $this->db->select('City');
        $sql2 = $this->db->get('vendor_pincode_mapping');

        $query2['city'] = $sql2->result_array();
        
        $query3['source'] = $this->partner_model->get_all_partner_source();

        $query4['categories'] = $this->get_service_caharges_data("category", array(), "category");
        
        $query5['capacities'] = $this->get_service_caharges_data("capacity", array(), "category");

        $query6['appliances'] = $this->get_service_caharges_data("service_category", array(), "service_category");
        
        return array_merge($query1, $query2, $query3, $query4, $query5, $query6);
        
    }
    
    function get_service_caharges_data($select, $where = array(), $order_by ="", $where_in = array()){
        $this->db->distinct();
        $this->db->select($select);
        if(!empty($where_in)){
            foreach($where_in as $index => $value){
                $this->db->where_in($index, $value);
            } 
        }
        if(!empty($where)){
            $this->db->where($where);
        }
        $this->db->from('service_centre_charges');
        if(!empty($order_by)){
            $this->db->order_by($order_by, "asc");
        }
        
        $this->db->join("services", "services.id = service_centre_charges.service_id");
        $query = $this->db->get();
        
    	return $query->result_array();
        
    }

    function editPriceTable($data){
      $this->db->where('id', $data['id']);
      $this->db->update('service_centre_charges', $data); 

    }
    
    /*
     * @Desc: This function is used to get service center charges table values
     * @params: void
     * @return: Array
     * 
     */
    function get_service_centre_charges($state){
        $this->db->select('service_centre_charges.*, services.services as product');
        $this->db->join('services','services.id = service_centre_charges.service_id');
        $this->db->where_not_in('service_centre_charges.service_category', array('Spare Parts'));
        $query = $this->db->get('service_centre_charges');
        return  $query->result_array();
    }
    
    /**
     * @Desc:This function is used to get unique states from Tax Rates for SC Charges List
     * @params: void
     * @return: void
     * 
     */
    function get_unique_states_from_tax_rates(){
        $sql = 'SELECT DISTINCT state FROM tax_rates Order By State Asc';
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    /**
     * @Desc:This function is used to get partner price data
     * @params: $data
     * @return: array
     * 
     */
    function get_partner_price_data($where, $where_in = array()){
        
        $this->db->select('category , capacity , service_category , customer_total , partner_payable_basic , customer_net_payable,vendor_total,pod,is_upcountry,vendor_basic_percentage,brand');
        $this->db->from('service_centre_charges');
        $this->db->where($where);
        $this->db->where('active', 1);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * @Desc:This function is used to get appliance based on partner id
     * @params: $price_mapping_id
     * @return: array
     * 
     */


        function get_service_brands_for_partner($partner_id){
        $sql = "Select Distinct partner_appliance_details.brand, services.services,services.id  "
                . "From partner_appliance_details, services "
                . "where partner_appliance_details.service_id = services.id "
                . "AND partner_appliance_details.partner_id = '".$partner_id."'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }



    
    
    function get_appliance_from_partner($partner_id){
        $this->db->distinct();
        $this->db->select('services.id,services');
        $this->db->from('services');
        $this->db->join('service_centre_charges','services.id = service_centre_charges.service_id');
        $this->db->where('service_centre_charges.partner_id ' , $partner_id);
        $this->db->order_by('services');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * @Desc:This function is used to get service category based on appliance
     * @params: $service_id
     * @return: array
     * 
     */
    
    function get_service_category_from_service_id($service_id,$partner_id=""){
        if($partner_id != ""){
            $where = array('service_id'=>$service_id,'partner_id'=>$partner_id);
        }else{
            $where = array('service_id'=>$service_id);
        }
        $this->db->distinct();
        $this->db->select('service_category');
        $this->db->from('service_centre_charges');
        $this->db->where($where);
        $this->db->order_by('service_category');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * @desc Used to get the buyback charges from bb_charges table
     * @param $where array
     * @param $select array
     * @param $is_distinct default false
     * @return array
     */
    function get_bb_charges($where='', $select,$is_distinct=False, $join = FALSE,$start = null,$limit = null,$is_download = false,$where_in = ''){
        if($is_distinct){
            $this->db->distinct();
        }
        $this->db->select($select, false);
        if($where != ''){
            $this->db->where($where);
        }
        if($where_in != ''){
            foreach($where_in as $key => $value){
                $this->db->where_in($key,$value);
            }
        }
        if($join){
            $this->db->join('bb_shop_address', 'bb_shop_address.cp_id = bb_charges.cp_id AND bb_shop_address.shop_address_city = bb_charges.city');
        }
        
        if($start !== "" && !empty($limit)){
            $this->db->limit($limit,$start);
        }
        
        $query = $this->db->get("bb_charges");
        
        if($is_download){
            return $query;
        }else{
            return $query->result_array();
        }
        
    }
    
    function get_service_charge_details($where, $select, $order_by){
        $this->db->distinct();
        $this->db->select($select);
        $this->db->where($where);
        $this->db->order_by($order_by);
        $query = $this->db->get('service_centre_charges');
        return $query->result_array();
    }
    
    /**
     *  @desc : This function is used to get sc charges by any condition
     *  @param : $post string
     *  @param : $select string
     *  @return : $output Array()
     */
    function _get_service_centre_charges($post, $select = "") {
        $this->db->from('service_centre_charges');
        if (empty($select)) {
            $select = '*';
        }
        $this->db->select($select,FALSE);
        // $this->db->join('tax_rates','tax_rates.tax_code = service_centre_charges.tax_code'
        //        . ' AND tax_rates.product_type = service_centre_charges.product_type ');
        $this->db->join('services','services.id = service_centre_charges.service_id');
        
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
            $this->db->order_by('service_id','DESC');
        }
    }
    
    /**
     *  @desc : This function is used to get charges by any condition
     *  @param : $post string
     *  @param : $select string
     *  @return: Array()
     */
    function get_service_centre_charges_by_any($post, $select = "") {
        $this->_get_service_centre_charges($post, $select);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     *  @desc : This function is used to get total count of charges
     *  @param : $post string
     *  @return: Array()
     */
    public function count_all_charges($post) {
        $this->_get_service_centre_charges($post, 'count(category) as numrows');
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
    }  
    
    /**
     *  @desc : This function is used to get total filtered count of charges
     *  @param : $post string
     *  @return: Array()
     */
    function count_filtered_charges($post){
        $this->_get_service_centre_charges($post,'count(category) as numrows');
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
    }
    
    function get_service_request_type($where, $select){
        $this->db->distinct();
        $this->db->select($select);
        $this->db->where($where);
        $this->db->order_by("service_category","ASC");
        $query = $this->db->get('request_type');
        
    	return $query->result_array();
    }
    
//    ----------------------------- NOT IN USE ----------------------------------------------------------
//    ---------------------------- Commented by Prity Sharma on 01-08-2019 ------------------------------
    
//    function getServiceCategoryMapping($where, $select, $order_by, $where_in = array()){
//        $this->db->distinct();
//        $this->db->select($select);
//        $this->db->join('category', 'service_category_mapping.category_id = category.id');
//        $this->db->join('capacity', 'service_category_mapping.capacity_id = capacity.id', 'left');
//        if(!empty($where_in)){
//            foreach($where_in as $index => $value){
//                $this->db->where_in($index, $value);
//            } 
//        }
//        $this->db->where($where);
//        $this->db->order_by($order_by);
//        $query = $this->db->get('service_category_mapping');
//    	return $query->result_array();
//    }
    
    function delete_service_charges($where_in){
       if(!empty($where_in)){
           $this->db->where_in('id', $where_in);
           return $this->db->delete("service_centre_charges");
       } 
    }
    /**
     * @desc This is used to insert service charges data before delete in trigger_service_charges table
     * @param int $agent_id
     * @param int $charges_id
     * @return type
     */
    function insert_deleted_s_charge_in_trigger($agent_id, $charges_id){
        $id = implode(",", $charges_id);
        if(!empty($id)){
             $sql = "INSERT INTO trigger_service_charges (SELECT service_centre_charges.*, CURRENT_TIMESTAMP AS current_updated_date, "
                . "'$agent_id' AS deleted_by FROM service_centre_charges WHERE service_centre_charges.id IN ($id))";
            $this->db->query($sql);
            
        }
       
    }
    
        /**
     * @desc: This function is used to insert service category capacity data into service_category_mapping table
     * @params: Array $app_data
     * @return: true if inserted
     * 
     */
     function insert_appliance_detail($app_data){
       
        $this->db->select('*');
        $this->db->from('service_category_mapping');
        $this->db->where($app_data);
        $query = $this->db->get();
        if ($query->num_rows() == 0) {

        $this->db->insert('service_category_mapping', $app_data);
        return $this->db->insert_id();
        } else {
            return false;
        }
        }


    /**
     * @desc: This function is used to get data from service_category_mapping table
     * @params: Array $where
     * @return: string
     * 
     */
//             function get_appliance_data($where = array()){  
//             $this->db->select('service_category_mapping.*, services.services, category.name as category, capacity.name as capacity');
//             $this->db->from('service_category_mapping');
//             $this->db->join('services', 'services.id =  service_category_mapping.service_id');
//             $this->db->join('category', 'category.id =  service_category_mapping.category_id');
//             $this->db->join('capacity', 'capacity.id =  service_category_mapping.capacity_id', 'left');
//             
//             if(!empty($where)){
//                 $this->db->where($where);
//             }
//             $this->db->order_by('services.services, category.name, capacity.name');
//             $query = $this->db->get();
//            return $query->result();  
//        } 
        
        /**
     * @desc: This function is used to update service category capacity data in service_category_mapping table
     * @params: Array $data
     * @params: $id
     * @return: true if updated
     * 
     */
     function update_appliance_detail($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('service_category_mapping', $data);
        
        if ($this->db->affected_rows() > 0) {
                return true;
            
        } else {
            return false;
        }
    }
    
    /**
     * @desc: This function is used to get data from request_type table
     * @params: Array $where
     * @return: string
     * 
     */
            function get_service_category_data($where = array()){  
             $this->db->select('request_type.*, services.services');
             $this->db->from('request_type');
             $this->db->join('services', 'services.id =  request_type.service_id');
             if(!empty($where)){
                 $this->db->where($where);
             }
             $query = $this->db->get();
            return $query->result();  
        } 
        
        /**
     * @desc: This function is used to update data in request_type table
     * @params: Array $data
     * @params: $id 
     * @return: true if inserted
     * 
     */
     function update_service_category_detail($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('request_type', $data);
     
        
        if ($this->db->affected_rows() > 0) {
                return true;
            
        } else {
            return false;
        }
    }
 

      /**
     * @desc: This function is used to insert new appliance name into services table
     * @params: Array $data
     * @return: true if inserted
     * 
     */
        function insert_appliance_name($data){
        
        $this->db->select('*');
        $this->db->from('services');
        $this->db->where($data);
        $query = $this->db->get();
        if ($query->num_rows() == 0) {

        $this->db->insert('services', $data);
        return $this->db->insert_id();

        }else {
            return false;
        }
        }
      
      /**
     * @desc: This function is used to update appliance name in services table
     * @params: Array $data
     * @params: $id
     * @return: true if updated
     * 
     */
        function update_appliance_name($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('services', $data);
     
        
        if ($this->db->affected_rows() > 0) {
                return true;
            
        } else {
            return false;
        }    
        
    }
     /**
     * @desc: This function is used to insert service category data into request_type table
     * @params: Array $data
     * @return: true if inserted
     * 
     */
    function insert_service_category($data) {

        $this->db->select('*');
        $this->db->from('request_type');
        $this->db->where($data);
        $query = $this->db->get();
        if ($query->num_rows() == 0) {

            $this->db->insert('request_type', $data);
            return $this->db->insert_id();
            
        } else {
            return false;
        }
    }
    
    
        /**
     * @desc: This function is used to get entity role data from entity_role table
     * @params: Array $where
     * @return: string
     * 
     */
         function get_entity_role_data($where = array()){  
             $this->db->select('*');
             if(!empty($where)){
                 $this->db->where($where);
             }
             $query = $this->db->get('entity_role');
            return $query->result();  
        } 

      /**
     * @desc: This function is used to insert entity role data into entity_role table
     * @params: Array $data
     * @return: true if inserted
     * 
     */
        function insert_entity_role_detail($data){
        
        $this->db->select('*');
        $this->db->from('entity_role');
        $this->db->where($data);
        $query = $this->db->get();
        if ($query->num_rows() == 0) {

        $this->db->insert('entity_role', $data);
        return $this->db->insert_id();

        } else {
            return false;
        }
        }
      
      /**
     * @desc: This function is used to update data in entity_role table
     * @params: Array $data
     * @params: $id
     * @return: true if updated
     * 
     */
    function update_entity_role_detail($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('entity_role', $data);
     
        
        if ($this->db->affected_rows() > 0) {
                return true;
            
        } else {
            return false;
        }
    }
    
    
    /**
     * @desc: This function is used to insert bank detail
     * @params: $data
     * @return: last insert id
    */
    function insert_bank_detail($data) {

        $this->db->select('id');
        $this->db->from('bank_details');
        $this->db->where($data);
        $query = $this->db->get();
        if ($query->num_rows() == 0) {
            $this->db->insert('bank_details', $data);
            return $this->db->insert_id();
            
        } else {
            return false;
        }
    }
    
    /**
     * @desc: This function is used to get bank detail
     * @params: $select, $post
     * @return: array
    */
    function get_bank_detail($select='*', $post){
        $this->_getBankDetail($select, $post);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }
    
    function _getBankDetail($select, $post){
        $this->db->from('bank_details');
        $this->db->select($select, FALSE);

        if (!empty($post['where'])) {
            $this->db->where($post['where'], FALSE);
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

        if(isset($post['column_order']) && !empty($post['column_order'])){
            foreach ($post['column_order'] as $key => $item) { 
                  $this->db->order_by($key, $item);
            }
           
        }
        
        if(isset($post['group_by']) && !empty($post['group_by'])){
            $this->db->group_by($post['group_by']);
        }
    }
    
    /**
     * @desc: This function is used to get count of resultant row from filtered data table
     * @params: $select, $post
     * @return: number
    */
    function count_filtered_bank_detail($select, $post) {
        $this->_getBankDetail($select, $post);

        $query = $this->db->get();
        return $query->num_rows();
    }
    
    /**
     * @desc: This function is used to update bank detail
     * @params: $where, $data
     * @return: boolean
    */
    function update_bank_details($where, $data){
        $this->db->where($where);
        $this->db->update('bank_details', $data);
     
        if ($this->db->affected_rows() > 0) {
                return true;
            
        } else {
            return false;
        }
    }
    
    /**
     This function return Categories mapped with a partner for a given service
     * @author Prity Sharma
     * @date 18-07-2019
     * @return array 
     */
    function getServiceCategoryMapping($where, $select, $order_by, $where_in = array(), $join_array = array(), $join_type_array = array(), $result_array = true){        
        $this->db->distinct();
        $this->db->select($select);        
        $this->db->join('category', 'service_category_mapping.category_id = category.id');   
        if(!empty($join_array)){
            foreach ($join_array as $tableName => $joinCondition){
                if(array_key_exists($tableName, $join_type_array)){
                    $this->db->join($tableName,$joinCondition,$join_type_array[$tableName]);
                }
                else{
                    $this->db->join($tableName,$joinCondition);
                }
            }
        }
        
        if(!empty($where))
        {
            $this->db->where($where);
        }
        
        if(!empty($where_in))
        {
            foreach($where_in as $key => $values){
                $this->db->where_in($key, $values);
            }
        }
        $this->db->order_by($order_by);
        $query = $this->db->get('service_category_mapping');
        if(!$result_array)
        {
            return $query->result();
        }
    	return $query->result_array();
    }    
}
