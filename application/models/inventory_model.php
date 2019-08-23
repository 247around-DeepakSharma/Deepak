<?php

class Inventory_model extends CI_Model {
    
      var $order = array('spare_parts_details.date_of_request' => 'desc'); 

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();


        $this->db = $this->load->database('default', TRUE, TRUE);
    }

    
    /**
     * @desc: This function is used to insert data in brackets table
     * @params: Array of data
     * return : boolean
     */
    function insert_brackets($data){
        
        $this->db->insert('brackets', $data);
         if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * @desc: This function is used to get all brackets details
     * @params:void
     * @return:Array
     */
    function get_brackets($limit, $start,$sf_list = ""){
        if($sf_list != ""){
            $where = "WHERE order_received_from  IN (".$sf_list.")";
        }else{
            $where = "";
        }
        $add_limit = "";

        if($start !== "All"){
            $add_limit = " limit $start, $limit ";
        }
      $sql = "SELECT * FROM brackets "
                . $where.""
                . " ORDER BY order_id Desc $add_limit";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    
    
    /**
     * @desc: This function is used to get count of brackets. 
     * @params:void
     * @return:Array
     *     
     */    
    
    function get_total_brackets_count($sf_list = ""){
        if($sf_list != ""){
            $where = "WHERE order_received_from  IN (".$sf_list.")";
        }else{
            $where = "";
        }
        $sql = "SELECT count(*) as count FROM brackets "
                . $where.""
                . " ORDER BY order_id Desc";
        $query = $this->db->query($sql);
        $count = $query->result_array();
        return $count[0]['count'];
    }
    
    /**
     * @desc: This function is used to get only unshipped and not recieved brackets details
     * @params:void
     * @return:Array
     */
    function get_total_brackets_given($limit, $start,$sf_id){
        if($start !== "All"){
            $add_limit = " limit $start, $limit ";
        }
        $this->db->select('*');
//        $this->db->where(array('is_shipped' => 0,'is_received'=> 0, 'order_given_to'=> $sf_id, 'active'=> '1'));
        $this->db->where(array('order_given_to'=> $sf_id, 'active'=> '1', 'create_date >=' => '2017-1-1'));
        $this->db->order_by('order_id', 'desc');
        $this->db->limit($limit, $start);
        $query = $this->db->get('brackets');
        return $query->result_array();
    }
    
    function get_total_brackets_given_count($sf_id){
        $this->db->select('count(*) as count');
        $this->db->where(array('order_given_to'=> $sf_id, 'active'=> '1'));
        $this->db->order_by('order_id', 'desc');
        $query = $this->db->get('brackets');
        
        $count = $query->result_array();
        return $count[0]['count'];
    }
    
    /**
     * @Desc: Get brackets details by id
     * @params: Int id
     * @return: Array
     * 
     */
    function get_brackets_by_id($id){
        $this->db->select('*');
        $this->db->where('order_id',$id);
        $query = $this->db->get('brackets');
        return $query->result_array();
    }
    
    /**
     * @Desc: This function is used to update brackets
     * @params: Array, Int id
     * @return: Int
     * 
     */
    function update_brackets($data,$where){
        $this->db->where($where);
	$this->db->update('brackets', $data);
        if($this->db->affected_rows() > 0){
             log_message ('info', __METHOD__ . "=> Booking  SQL ". $this->db->last_query());
            return true;
        }else{
            return false;
        }
        
    }
    
    /**
     * @desc: This function is used to insert data in inventory table
     * params: Array of data
     * return : boolean
     */
    function insert_inventory($data){
        
        $this->db->insert_batch('inventory', $data);
         if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * @Desc: This function is used to check if order id for particular update is already present or not
     * @params: String
     * @return: Boolean
     * 
     */
    function check_data($vendor_id){
        $this->db->select('*');
        $this->db->where('vendor_id',$vendor_id);
         $this->db->order_by("id", "asc");
        $query = $this->db->get('inventory');
        return $query->result_array();
    }
    
    /**
     * @Desc: This function is used to get inventory details by vendor id
     * params: vendor_id
     * return: void
     * 
     */
    function get_vendor_inventory_details($vendor_id){
        
        $this->db->select('inventory.id,sc.id as sc_id,sc.name as sc_name,'
                . 'inventory.19_24_current_count,inventory.26_32_current_count,inventory.36_42_current_count,inventory.43_current_count,'
                . 'inventory.remarks,inventory.increment/decrement');
        $this->db->where('inventory.vendor_id',$vendor_id);
        $this->db->order_by("inventory.id",'asc');
        $this->db->join('service_centres sc','sc.id = inventory.vendor_id');
        $query = $this->db->get('inventory');
        return $query->result_array();
        

    }
    
    /**
     * @Desc: This function is used to get Distict vendor id from inventory table
     * @parmas:void
     * @return:void
     */
    function get_distict_vendor_from_inventory($sf_list = ""){
        if($sf_list != ""){
            $where = "JOIN brackets ON brackets.order_id = inventory.order_booking_id WHERE brackets.order_given_to IN (".$sf_list.")";
        }else{
            $where = "";
        }
        $sql = "SELECT DISTINCT `vendor_id` FROM (`inventory`) "
                . $where.''
                . " Order By `vendor_id`";
        $data = $this->db->query($sql);
        return $data->result_array();
    }
    
    /**
     * @Desc: This function is used to get brackets booking details based on order_id
     * @params: order_id
     * @return: Array
     * 
     */
    function get_brackets_by_order_id($order_id){
        $this->db->select('brackets.id,brackets.order_id,brackets.invoice_id,brackets.purchase_invoice_id,brackets.order_received_from,brackets.order_given_to,brackets.order_date,brackets.shipment_date,'
                . 'brackets.received_date,brackets.19_24_requested,brackets.26_32_requested,brackets.36_42_requested,brackets.43_requested,'
                . 'brackets.total_requested,brackets.19_24_shipped,brackets.26_32_shipped,brackets.36_42_shipped,brackets.43_shipped,brackets.total_shipped,'
                . 'brackets.19_24_received,brackets.26_32_received,brackets.36_42_received,brackets.43_received,brackets.total_received,brackets.is_shipped,brackets.is_received,'
                . 'b.old_state,b.new_state,b.partner_id,b.service_center_id,b.agent_id,vpi.invoice_file_pdf,vpi.invoice_file_excel');
        $this->db->where('order_id',$order_id);
        $this->db->join('booking_state_change b','b.booking_id = brackets.order_id');
        $this->db->join('vendor_partner_invoices vpi','brackets.invoice_id = vpi.invoice_id','left');
        $this->db->group_by('b.new_state');
        $this->db->order_by('brackets.create_date','asc');
        $query = $this->db->get('brackets');
        $data =  $query->result_array();
        
        foreach ($data as $key => $value){
            if(!is_null($value['partner_id'])){
                // If Partner Id is 247001
                if($value['partner_id'] == _247AROUND){
                    $sql = "SELECT full_name FROM employee WHERE "
                            . " employee.id = '".$value['agent_id']."'";
                   
                    $query1 = $this->db->query($sql);
                    $data1 = $query1->result_array();
                   
                    $data[$key]['agent_name'] = isset($data1[0]['full_name'])?$data1[0]['full_name']:'';
                   $data[$key]['partner_name'] = '247AROUND';
                }
//                } else {
//                    // For Partner
//                    $this->db->select('full_name,public_name');
//                    $this->db->from('partner_login,partners');
//                    $this->db->where('partner_login.id', $value['agent_id']);
//                    $this->db->where('partners.id', $value['partner_id']);
//                    $query1 = $this->db->get();
//                    $data1 = $query1->result_array();
//                    $data[$key]['agent_name'] = isset($data1[0]['full_name'])?$data1[0]['full_name']:'';
//                }
            } else if(!is_null($value['service_center_id'])){
                // For Service center
                $this->db->select('service_centres.name As public_name , full_name');
                $this->db->from('service_centers_login');
                $this->db->where('service_centers_login.id', $value['agent_id']);
                $this->db->join('service_centres', 'service_centres.id = service_centers_login.service_center_id');
                $query1 = $this->db->get();
                $data1 = $query1->result_array();
                $data[$key]['agent_name'] = isset($data1[0]['full_name'])?$data1[0]['full_name']:'';
                $data[$key]['partner_name'] = isset($data1[0]['public_name'])?$data1[0]['public_name']:'';
                
            }
            
        }
       
        return $data;
    }
    
    /**
     * @Desc: This function is used to get order id from previously entered order values
     * @params: void
     * @return: void
     * 
     */
    function get_latest_order_id(){
        $this->db->select('order_id');
        $this->db->order_by('id','desc');
        $query = $this->db->get('brackets');
        return $query->result_array();
    }
    
    
    /**
     * @Desc: This function is used to Uncancel Brackets Order for particular order ID
     * @params: Order ID
     * @return: Boolean
     * 
     */
    function uncancel_brackets($order_id,$data) {
        $this->db->where('order_id', $order_id);
        $this->db->update('brackets', $data);
        return $this->db->affected_rows();
    }
    
    /**
     * @Desc: This function is used to check if order ID exist or not in the brackets table
     * @params: string $Order_id
     * @return: array
     * 
     */
    function check_order_id_exist($order_id){
        $this->db->select('order_id ,purchase_invoice_id');
        $this->db->where('order_id',$order_id);
        $query = $this->db->get('brackets');
        return $query->result_array();
    }
    
    /**
     * @Desc: This function is used get the brackets shipped and service centre data for 
     * given order id
     * @params: string $Order_id
     * @return: array
     * 
     */
    function get_new_credit_note_brackets_data($order_id){
        $sql = "SELECT sc.id,sc.company_name ,sc.address,sc.tin_no,sc.service_tax_no,sc.state,sc.sc_code,sc.owner_email,sc.owner_phone_1,b.19_24_shipped,
                b.26_32_shipped,b.36_42_shipped,b.43_shipped,b.total_shipped,b.order_given_to,b.shipment_date
                FROM brackets as b 
                JOIN service_centres as sc ON b.order_given_to = sc.id 
                WHERE b.order_id = '$order_id'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    /**
     * @Desc: This function is used to get the filtered brackets list
     * @params: $select string
     * @params: $where string
     * @return: array
     * 
     */
    function get_filtered_brackets($select, $where){
        $this->db->select($select);
        $this->db->where($where,null,false);
        $this->db->from('brackets');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function _get_spare_parts_query($post) {
        $this->db->from('spare_parts_details');
        $this->db->select($post['select'].", DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(date_of_request, '%Y-%m-%d')) AS age_of_request,"
                . "DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(spare_parts_details.shipped_date, '%Y-%m-%d')) AS age_of_shipped_date,"
                . "spare_parts_details.quantity,"
                . "spare_parts_details.shipped_quantity,"
                . "DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(spare_parts_details.spare_cancelled_date, '%Y-%m-%d')) AS spare_cancelled_date,"
                . "DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(spare_parts_details.acknowledge_date, '%Y-%m-%d')) AS age_of_delivered_to_sf,"
                . "DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(booking_details.service_center_closed_date, '%Y-%m-%d')) AS age_part_pending_to_sf,"
                . "DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(spare_parts_details.defective_part_shipped_date, '%Y-%m-%d')) AS age_defective_part_shipped_date,"
                . "DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(estimate_cost_given_date, '%Y-%m-%d')) AS age_of_est_given", FALSE);

        $this->db->join('booking_details','spare_parts_details.booking_id = booking_details.booking_id', "left");
        $this->db->join('partners','partners.id = booking_details.partner_id', "left");
        $this->db->join('service_centres','service_centres.id = booking_details.assigned_vendor_id', "left");
        $this->db->join('users','users.user_id = booking_details.user_id', "left");
        if(isset($post['is_inventory'])){
            
            $this->db->join('inventory_master_list','inventory_master_list.inventory_id = spare_parts_details.requested_inventory_id', "left");
            $this->db->join('inventory_master_list as im','im.inventory_id = spare_parts_details.shipped_inventory_id', "left");
        }
        
        if(isset($post['spare_cancel_reason'])){
            $this->db->join('booking_cancellation_reasons','booking_cancellation_reasons.id = spare_parts_details.spare_cancellation_reason', "left");
        }
        $this->db->join('services', 'booking_details.service_id = services.id','left');
        
        if (!empty($post['where'])) {
            $this->db->where($post['where'], FALSE);
        }
        if(isset($post['where_in'])){
            foreach ($post['where_in'] as $index => $value) {

                $this->db->where_in($index, $value);
            }
        }
        
        if (!empty($post['search']['value'])) {
             $like = "";
            if(array_key_exists("column_search", $post)){
                foreach ($post['column_search'] as $key => $item) { // loop column 
                    // if datatable send POST for search
                    if ($key === 0) { // first loop
                        $like .= "( " . $item . " LIKE '%" . $post['search']['value'] . "%' ";

                    } else {
                        $like .= " OR " . $item . " LIKE '%" . $post['search']['value'] . "%' ";

                    }
                }
                $like .= ") ";
            }
            else{
                $like .= "(booking_details.booking_id LIKE '%" . $post['search']['value'] . "%')";
            }
            $this->db->where($like, null, false);
        }

        if (!empty($post['order'])) { // here order processing
            $this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
        } else if (isset($this->order)) {
//            $order = $this->order;
//            $this->db->order_by(key($order), $order[key($order)]);
            $this->db->order_by('spare_parts_details.part_requested_on_approval', 'ASC');
        
             $this->db->order_by('spare_parts_details.date_of_request', 'DESC');
        }
    }
    
    function get_spare_parts_query($post) {
        $this->_get_spare_parts_query($post);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        
        $query = $this->db->get();
        return $query->result();
    }
    
    public function count_spare_parts($post) {
        $this->db->from('spare_parts_details');
        $this->db->join('booking_details','spare_parts_details.booking_id = booking_details.booking_id');
        $this->db->join('users','users.user_id = booking_details.user_id');
        if(isset($post['where'])){
             $this->db->where($post['where']);
        }
       
        $query = $this->db->count_all_results();

        return $query;

    }

    function count_spare_filtered($post) {
        $this->_get_spare_parts_query($post);

        $query = $this->db->get();
        return $query->num_rows();
    }
    
    function insert_zopper_estimate($data){
        $this->db->insert("zopper_estimate_details", $data);
        return $this->db->insert_id();
    }
    
    function select_zopper_estimate($where){
        $this->db->where($where);
        $query = $this->db->get("zopper_estimate_details");
        return $query->result_array();
    }
    
    function update_zopper_estimate($where, $data){
        $this->db->where($where);
        $this->db->update("zopper_estimate_details", $data);
    }
    
    function insert_inventory_ledger($data){
        $this->db->insert("inventory_ledger", $data);
        return $this->db->insert_id();
    }
    
    function insert_inventory_stock($data){
        $this->db->insert("inventory_stocks", $data);
        return $this->db->insert_id();
    }
    
    function update_inventory_stock($where,$data){
        $this->db->where($where);
        $this->db->set('stock', $data, FALSE);
        $this->db->update('inventory_stocks');
        if($this->db->affected_rows() > 0){
            $response = true;
        }else{
            $response = false;
        }
        
        return $response;
    }
    
    /**
     * @Desc: This function is used to get data from the inventory_master_list table
     * @params: $select string
     * @params: $where array
     * @return: $query array
     * 
     */
    function get_inventory_master_list_data($select, $where = array(), $where_in = array()){
        $this->db->distinct();
        $this->db->select($select);
        if(!empty($where)){
            $this->db->where($where);
        }
        
        if(!empty($where_in)){
           $this->db->where_in('inventory_master_list.part_number', $where_in);
        }
                
        $query = $this->db->get('inventory_master_list');
        //log_message("info",$this->db->last_query());
        return $query->result_array();
    }
    
    /**
     * @Desc: This function is used to get data from the inventory_stocks table
     * @params: $post array
     * @params: $select string
     * @return: void
     * 
     */
    function _get_inventory_stocks($post,$select){
                
        if (empty($select)) {
            $select = '*';
        }
        $this->db->distinct();
        $this->db->select($select,FALSE);
        $this->db->from('inventory_stocks');
        $this->db->join('inventory_master_list','inventory_master_list.inventory_id = inventory_stocks.inventory_id','left');
        $this->db->join('service_centres', 'inventory_stocks.entity_id = service_centres.id','left');
        $this->db->join('services', 'inventory_master_list.service_id = services.id','left');
        
//        if(isset($post['type_join']) && $post['type_join'] == true){
//             $this->db->join('inventory_parts_type', 'inventory_master_list.type = inventory_parts_type.part_type '
//                     . 'AND inventory_parts_type.service_id = inventory_master_list.service_id','left');
//        }
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
            $this->db->order_by('inventory_stocks.entity_id','ASC');
        }
        
        if(!empty($post['group_by'])){
            $this->db->group_by($post['group_by']);
        }
        if(isset($post['having']) && !empty($post['having'])){
            $this->db->having($post['having'],FALSE);
        }
    }
    
    /**
     * @Desc: This function is used to get data from the inventory ledger table
     * @params: $limit string
     * @params: $start string
     * @params: $is_count boolean
     * @return: $query array
     * 
     */
    function get_inventory_ledger_data($limit, $start,$where = "",$is_count=false) {
        $add_limit = "";

        if ($start !== "All" && !$is_count) {
            $add_limit = " limit $start, $limit ";
        }
        $sql = "SELECT CASE WHEN(sc.name IS NOT NULL) THEN (sc.name) 
                WHEN(p.public_name IS NOT NULL) THEN (p.public_name) 
                WHEN (e.full_name IS NOT NULL) THEN (e.full_name) END as receiver, 
                CASE WHEN(sc1.name IS NOT NULL) THEN (sc1.name) 
                WHEN(p1.public_name IS NOT NULL) THEN (p1.public_name) 
                WHEN (e1.full_name IS NOT NULL) THEN (e1.full_name) END as sender,i.*
                FROM `inventory_ledger` as i LEFT JOIN service_centres as sc on (sc.id = i.`receiver_entity_id` AND i.`receiver_entity_type` = 'vendor') Left JOIN partners as p on (p.id = i.`receiver_entity_id` AND i.`receiver_entity_type` = 'partner') LEFT JOIN employee as e ON (e.id = i.`receiver_entity_id` AND i.`receiver_entity_type` = 'employee')  
                LEFT JOIN service_centres as sc1 on (sc1.id = i.`sender_entity_id` AND i.`sender_entity_type` = 'vendor') Left JOIN partners as p1 on (p1.id = i.`sender_entity_id` AND i.`sender_entity_type` = 'partner') LEFT JOIN employee as e1 ON (e1.id = i.`sender_entity_id` AND i.`sender_entity_type` = 'employee') $where ORDER BY i.create_date $add_limit";
        
        if($is_count){
            $query = count($this->db->query($sql)->result_array());
        }else{
            $query = $this->db->query($sql)->result_array();
        
            foreach ($query as $key => $value){
                //get part name from inventory_master_list 
                $inventory_details = $this->get_inventory_master_list_data('part_name,part_number',array('inventory_id' => $value['inventory_id']));
                if(!empty($inventory_details)){
                    $query[$key]['part_name'] = $inventory_details[0]['part_name'];
                    $query[$key]['part_number'] = $inventory_details[0]['part_number'];
                }else{
                    $query[$key]['part_name'] = "";
                    $query[$key]['part_number'] = "";
                }
                //get agent name 
                if($value['agent_type'] === _247AROUND_EMPLOYEE_STRING){
                    $employe_details = $this->employee_model->getemployeefromid($value['agent_id']);
                    if(!empty($employe_details[0]['full_name'])){
                      $query[$key]['agent_name'] = $employe_details[0]['full_name'];  
                    } else {
                      $query[$key]['agent_name'] = '';  
                    }                    
                }else if($value['agent_type'] === _247AROUND_PARTNER_STRING){
                    $partner_details = $this->partner_model->getpartner_details('public_name',array('partners.id'=>$value['agent_id']));
                    $query[$key]['agent_name'] = $partner_details[0]['public_name'];
                }else if($value['agent_type'] === _247AROUND_SF_STRING){
                    $vendor_details = $this->vendor_model->getVendorDetails('name',array('id'=>$value['agent_id']));
                    if(!empty($vendor_details[0]['name'])){
                        $query[$key]['agent_name'] = $vendor_details[0]['name'];
                    } else {
                       $query[$key]['agent_name'] = ''; 
                    }
                    
                }

            }
        }
        
        return $query;
    }
    
    /**
     *  @desc : This function is used to get inventory stocks
     *  @param : $post string
     *  @param : $select string
     *  @param : $sfIDArray array
     *  @return: Array()
     */
    function get_inventory_stock_list($post, $select = "",$sfIDArray=array(), $is_object =true) {
        $this->_get_inventory_stocks($post, $select);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        if($sfIDArray){
            $this->db->where_in('inventory_stocks.entity_id', $sfIDArray);
        }
        $query = $this->db->get();
        if($is_object){
            $result =  $query->result();
        } else {
            $result = $query->result_array();
        }
        
        return $result;
        
    }
      
    /**
    *  @desc : This function is used to get alternate spare parts
    *  @param : $inventory_id, 
    *  @return : $res array
    */
    
   function get_alternate_inventory_stock_list($inventory_id, $service_center_id) {

        $inventory_stock_details = array();
        if (!empty($inventory_id)) {
            $inventory_id_sets = $this->get_group_wise_inventory_id_detail('alternate_inventory_set.inventory_id', $inventory_id);
            if(!empty($inventory_id_sets)){
                $inventory_ids = implode(',', array_map(function ($entry) {
                        return $entry['inventory_id'];
                    }, $inventory_id_sets));
                    
                if (!empty($service_center_id)) {
                    $where = "inventory_stocks.entity_id = " . $service_center_id;
                } else {
                    $where = "service_centres.is_wh = 1 ";
                }
                
                $where .= " AND inventory_stocks.entity_type ='" . _247AROUND_SF_STRING . "' AND (inventory_stocks.stock - inventory_stocks.pending_request_count) > 0 ";
                if (!empty($inventory_ids)) {
                    $inventory_stock_details = $this->get_inventory_stock_details('inventory_stocks.stock as stocks,inventory_stocks.entity_id,inventory_stocks.entity_type,inventory_stocks.inventory_id, inventory_master_list.part_name', $where, $inventory_ids);
                }
            }
        }

        return $inventory_stock_details;
    }

    /**
     *  @desc : This function is used to get total inventory stocks
     *  @param : $post string
     *  @return: Array()
     */
    public function count_all_inventory_stocks($post) {
        $this->_get_inventory_stocks($post, 'count( DISTINCT inventory_stocks.id) as numrows');
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
    }
    
    /**
     *  @desc : This function is used to get total filtered inventory stocks
     *  @param : $post string
     *  @return: Array()
     */
    function count_filtered_inventory_stocks($post){
        $sfIDArray =array();
        if($this->session->userdata('user_group') == 'regionalmanager'){
            $rm_id = $this->session->userdata('id');
            $rmServiceCentersData= $this->reusable_model->get_search_result_data("employee_relation","service_centres_id",array("agent_id"=>$rm_id),NULL,NULL,NULL,NULL,NULL);
            $sfIDList = $rmServiceCentersData[0]['service_centres_id'];
            $sfIDArray = explode(",",$sfIDList);
        }
        $this->_get_inventory_stocks($post, 'count( DISTINCT inventory_stocks.id) as numrows');
        if($sfIDArray){
            $this->db->where_in('inventory_stocks.entity_id', $sfIDArray);
        }
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
    }
    
    
    /**
     * @Desc: This function is used to get data from the inventory_master_list table
     * @params: $post array
     * @params: $select string
     * @return: void
     * 
     */
    function _get_inventory_master_list($post,$select){
        
        if (empty($select)) {
            $select = '*';
        }
        $this->db->distinct();
        $this->db->select($select,FALSE);
        $this->db->from('inventory_master_list');
        $this->db->join('services', 'inventory_master_list.service_id = services.id','left');
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
            $this->db->order_by('inventory_master_list.service_id','DESC');
        }
        
        if(!empty($post['group_by'])){
            $this->db->group_by($post['group_by']);
        }
    }
    
    /**
     *  @desc : This function is used to get inventory master list
     *  @param : $post string
     *  @param : $select string
     *  @return: Array()
     */
    function get_inventory_master_list($post, $select = "",$is_array = false) {
        $this->_get_inventory_master_list($post, $select);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        
        $query = $this->db->get();
        if($is_array){
            return $query->result_array();
        }else{
            return $query->result();
        }
    }
    
    
    
     /**
     * @Desc: This function is used to get data from the alternate inventory_master_list table
     * @params: $post array
     * @params: $select string
     * @return: void
     * 
     */
    function _get_alternate_inventory_master_list($post,$select){
        
        if (empty($select)) {
            $select = '*';
        }
        $this->db->distinct();
        $this->db->select($select,FALSE);
        $this->db->from('inventory_master_list');
        $this->db->join('services', 'inventory_master_list.service_id = services.id','left');
        $this->db->join('alternate_inventory_set', 'inventory_master_list.inventory_id = alternate_inventory_set.inventory_id','left');
        $this->db->join('inventory_model_mapping', 'inventory_master_list.inventory_id = inventory_model_mapping.inventory_id','left');
        $this->db->join('appliance_model_details', 'inventory_model_mapping.model_number_id = appliance_model_details.id','left');
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
            $this->db->order_by('inventory_master_list.service_id','DESC');
        }
        
        if(!empty($post['group_by'])){
            $this->db->group_by($post['group_by']);
        }
    }
    
    /**
     *  @desc : This function is used to get alternate inventory master list
     *  @param : $post string
     *  @param : $select string
     *  @return: Array()
     */
    function get_alternate_inventory_master_list($post, $select = "",$is_array = false) {
        $this->_get_alternate_inventory_master_list($post, $select);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        
        $query = $this->db->get();
        if($is_array){
            return $query->result_array();
        }else{
            return $query->result();
        }
    }
       
     /**
     * @Desc: This function is used to get data from serviceable BOM details
     * @params: $post array
     * @params: $select string
     * @return: void
     * 
     */
    function _get_serviceable_bom_master_list($post,$select){
        
        if (empty($select)) {
            $select = '*';
        }
        $this->db->distinct();
        $this->db->select($select,FALSE);
        $this->db->from('inventory_model_mapping');
        $this->db->join('appliance_model_details','inventory_model_mapping.model_number_id = appliance_model_details.id');
        $this->db->join('inventory_master_list','inventory_model_mapping.inventory_id = inventory_master_list.inventory_id');
        $this->db->join('services','services.id = inventory_master_list.service_id');
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
            $this->db->order_by('inventory_master_list.service_id','DESC');
        }
        
        if(!empty($post['group_by'])){
            $this->db->group_by($post['group_by']);
        }
    }
    
    /**
     *  @desc : This function is used to get serviceable BOM list
     *  @param : $post string
     *  @param : $select string
     *  @return: Array()
     */
    function get_serviceable_bom_master_list($post, $select = "",$is_array = false) {
        $this->_get_serviceable_bom_master_list($post, $select);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        
        $query = $this->db->get();
        if($is_array){
            return $query->result_array();
        }else{
            return $query->result();
        }
    }
    
    
    
     /**
     *  @desc : This function is used to get Serviceable BOM list
     *  @param : $post string
     *  @return: Array()
     */
    public function count_all_serviceable_bom_list($post) {
        $this->_get_serviceable_bom_master_list($post, 'count(distinct(inventory_master_list.inventory_id)) as numrows');
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
    }
    
    
    
    /**
     *  @desc : This function is used to get total filtered alternate inventory master list
     *  @param : $post string
     *  @return: Array()
     */
    function count_filtered_serviceable_bom_list($post){
        $this->_get_serviceable_bom_master_list($post, 'count(distinct(inventory_master_list.inventory_id)) as numrows');
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
    }
    
    /**
     *  @desc : This function is used to get total alternate inventory master list
     *  @param : $post string
     *  @return: Array()
     */
    public function count_all_alternate_inventory_master_list($post) {
        $this->_get_alternate_inventory_master_list($post, 'count(distinct(inventory_master_list.inventory_id)) as numrows');
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
    }
    
    /**
     *  @desc : This function is used to get total filtered alternate inventory master list
     *  @param : $post string
     *  @return: Array()
     */
    function count_filtered_alternate_inventory_master_list($post){
        $this->_get_alternate_inventory_master_list($post, 'count(distinct(inventory_master_list.inventory_id)) as numrows');
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
    }
    
    /**
     *  @desc : This function is used to get total inventory master list
     *  @param : $post string
     *  @return: Array()
     */
    public function count_all_inventory_master_list($post) {
        $this->_get_inventory_master_list($post, 'count(distinct(inventory_master_list.inventory_id)) as numrows');
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
    }
    
    /**
     *  @desc : This function is used to get total filtered inventory master list
     *  @param : $post string
     *  @return: Array()
     */
    function count_filtered_inventory_master_list($post){
        $this->_get_inventory_master_list($post, 'count(distinct(inventory_master_list.inventory_id)) as numrows');
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
    }
    
    /**
     * @desc This is used to insert details into inventory_master_list table
     * @param Array $details
     * @return string
     */
    function insert_inventory_master_list_data($details) {
      $this->db->insert('inventory_master_list', $details);
      return $this->db->insert_id();
    }
    
    /**
     * @desc This is used to update details of inventory_master_list table
     * @param Array $where
     * @param Array $data
     * @return boolean
     */
    function update_inventory_master_list_data($where, $data){
        $this->db->where($where);
        $this->db->update('inventory_master_list',$data);
        if($this->db->affected_rows() > 0 ){
            return TRUE;
        }else{
            return FALSE;
        }
        
    }
    
    
    function get_inventory_snapshot($select,$where,$group_by = false){
        $this->db->select($select);
        $this->db->from('booking_details');
        $this->db->join('booking_unit_details', 'booking_details.booking_id = booking_unit_details.booking_id');
        $this->db->where($where);
        
        //RM Specific Bookings
        if($this->session->userdata('user_group') == 'regionalmanager'){
            $rm_id = $this->session->userdata('id');
            $rmServiceCentersData= $this->reusable_model->get_search_result_data("employee_relation","service_centres_id",array("agent_id"=>$rm_id),NULL,NULL,NULL,NULL,NULL);
            if(!empty($rmServiceCentersData)){
                $sfIDList = $rmServiceCentersData[0]['service_centres_id'];
                $sfIDArray = explode(",",$sfIDList);

                $this->db->where_in('booking_details.assigned_vendor_id', $sfIDArray);
            }
        }
        
        if($group_by){
            $this->db->group_by($group_by);
        }
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     *  @desc : This function is used to edit warehouse details
     *  @param : $select array() //consist column which we want to get
     *  @param : $where array()  
     *  @return : $res array()
     */
    function get_warehouse_details($select,$where, $join = true,$is_entity_join = false, $sf_join = false) {
        $this->db->select($select,FALSE);
        $this->db->where($where,FALSE);
        $this->db->from('warehouse_person_relationship');
        $this->db->join('contact_person','warehouse_person_relationship.contact_person_id = contact_person.id');
        $this->db->join('warehouse_details','warehouse_person_relationship.warehouse_id = warehouse_details.id');
        if($join){
            $this->db->join('warehouse_state_relationship','warehouse_person_relationship.warehouse_id = warehouse_state_relationship.warehouse_id');
        }
        
        if($is_entity_join){
             $this->db->join('entity_role','contact_person.role = entity_role.id');
        }
        
        if($sf_join){
            $this->db->join('service_centres','service_centres.id = contact_person.entity_id AND contact_person.entity_type = "'._247AROUND_SF_STRING.'"');
        }
        $query = $this->db->get();
        return $query->result_array();
    }
    /**
     * @desc This is used to insert details into inventory_master_list table in batch
     * @param Array $data
     * @return string
     */
    function insert_batch_inventory_master_list_data($data) {
      $this->db->insert_ignore_duplicate_batch('inventory_master_list', $data);
      return $this->db->insert_id();
    }
    
    /**
     * @desc This is used to insert details into spare_invoice_mapping table
     * @param Array $data
     * @return string
     */
    function insert_data_in_spare_invoice_mapping($data){
        $this->db->insert('spare_invoice_mapping', $data);
        return $this->db->insert_id();
    }
    
    function _get_spare_need_to_acknowledge($post,$select){
        if (empty($select)) {
            $select = '*';
        }
        $this->db->select($select,FALSE);
        $this->db->from('inventory_ledger as i');
        $this->db->join('inventory_master_list', 'inventory_master_list.inventory_id = i.inventory_id','left');
        $this->db->join('services', 'services.id = inventory_master_list.service_id','left');
        $this->db->join('service_centres as sc', "sc.id = i.receiver_entity_id AND i.receiver_entity_type = 'vendor'",'left');
        $this->db->join('partners as p', "p.id = i.receiver_entity_id AND i.receiver_entity_type = 'partner'",'left');
        $this->db->join('employee as e', "e.id = i.receiver_entity_id AND i.receiver_entity_type = 'employee'",'left');
        $this->db->join('service_centres as sc1', "sc1.id = i.sender_entity_id AND i.sender_entity_type = 'vendor'",'left');
        $this->db->join('partners as p1', "p1.id = i.sender_entity_id AND i.sender_entity_type = 'partner'",'left');
        $this->db->join('employee as e1', "e1.id = i.sender_entity_id AND i.sender_entity_type = 'employee'",'left');
        if(!empty($post['is_courier_details_required'])){
            $this->db->join('courier_details', 'i.courier_id = courier_details.id','left');
        }

        if ($post['is_micro_wh']) {
           $this->db->join('vendor_partner_invoices', 'vendor_partner_invoices.invoice_id = i.micro_invoice_id', 'left');
           $this->db->join('partners as pi', "pi.id = vendor_partner_invoices.third_party_entity_id AND inventory_master_list.entity_id= pi.id",'left');
        }

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
            $this->db->order_by('i.create_date','asc');
        }
        
        if(!empty($post['group_by'])){
            $this->db->group_by($post['group_by']);
        }
    }
    
    
    function get_spare_need_to_acknowledge($post, $select = "",$is_array = false){
        $this->_get_spare_need_to_acknowledge($post, $select);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        
        $query = $this->db->get();
        
        if($is_array){
            return $query->result_array();
        }else{
            return $query->result();
        }
    }
    
    /**
     *  @desc : This function is used to get total inventory stocks
     *  @param : $post string
     *  @return: Array()
     */
    public function count_spare_need_to_acknowledge($post) {
        $this->_get_spare_need_to_acknowledge($post, 'count(distinct(i.id)) as numrows');
        $query = $this->db->get();

        return $query->result_array()[0]['numrows'];
    }
    
    /**
     *  @desc : This function is used to get total filtered inventory stocks
     *  @param : $post string
     *  @return: Array()
     */
    function count_filtered_spare_need_to_acknowledge($post){
        $this->_get_spare_need_to_acknowledge($post, 'count(distinct(i.id)) as numrows');
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
    }
    
    
    /**
     *  @desc : This function is used to update inventory ledger details
     *  @param : $data array
     *  @param : $where array
     *  @return: Array()
     */
    function update_ledger_details($data,$where){
        $this->db->where($where);
        $this->db->update('inventory_ledger',$data);
        if($this->db->affected_rows() > 0){
            $response = true;
        }else{
            $response = false;
        }
        
        return $response;
    }
    
    /**
     * @desc This is used to insert details into appliance_model_details table
     * @param Array $data
     * @return string
     */
    function insert_appliance_model_details_batch($data){
        $this->db->insert_ignore_duplicate_batch('appliance_model_details', $data);
        return $this->db->insert_id();
    }
    
    /**
     * @Desc: This function is used to get data from the appliance_model_details table
     * @params: $select string
     * @params: $where array
     * @return: $query array
     * 
     */
    function get_appliance_model_details($select,$where = array()){
        $this->db->distinct();
        $this->db->select($select);
        if(!empty($where)){
            $this->db->where($where);
        }
        $this->db->order_by('model_number','ASC');
        $query = $this->db->get('appliance_model_details');
        return $query->result_array();
    }
    
    /**
     * @Desc: This function is used to get data from the inventory_model_mapping table
     * @params: $select string
     * @params: $where array
     * @return: $query array
     * 
     */
    function get_inventory_model_mapping_data($select,$where = array()){
        $this->db->distinct();
        $this->db->select($select, false);
        if(!empty($where)){
            $this->db->where($where);
        }
        $this->db->from('inventory_model_mapping');
        $this->db->join('appliance_model_details','inventory_model_mapping.model_number_id = appliance_model_details.id');
        $this->db->join('inventory_master_list','inventory_model_mapping.inventory_id = inventory_master_list.inventory_id');
        $this->db->join('services','services.id = inventory_master_list.service_id');
        
        $query = $this->db->get();
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
     * @desc This is used to insert details into inventory_model_mapping table in batch
     * @param Array $data
     * @return string
     */
    function insert_batch_inventory_model_mapping($data) {
      $this->db->insert_ignore_duplicate_batch('inventory_model_mapping', $data);
      return $this->db->insert_id();
    }
    
    
    /**
     * @desc This is used to insert courier details into courier_details table
     * @param Array $data
     * @return string
     */
    function insert_courier_details($data){
        $this->db->insert('courier_details', $data);
        return $this->db->insert_id();
    }
        
    /**
     * @desc This is used to insert ewaybill details into ewaybill_details table
     * @param Array $data
     * @return last inserted_id
     */
    function insert_ewaybill_details($data){
        $this->db->insert('ewaybill_details', $data);
        return $this->db->insert_id();
    }
    
    function insert_ewaybill_details_in_bulk($data) {
        $this->db->insert_ignore_duplicate_batch('ewaybill_details', $data);
         if($this->db->affected_rows() > 0){
            $res = TRUE;
        }else{
            $res = FALSE;
        }
        
        return $res;
    }
    
    /**
     * @Desc: This function is used to get data from the appliance_model_details table
     * @params: $post array
     * @params: $select string
     * @return: void
     * 
     */
    function _get_appliance_model_list($post,$select){
        
        if (empty($select)) {
            $select = '*';
        }
        $this->db->distinct();
        $this->db->select($select,FALSE);
        $this->db->from('appliance_model_details');
        $this->db->join('services', 'appliance_model_details.service_id = services.id');

        $this->db->join('partner_appliance_details', 'partner_appliance_details.model = appliance_model_details.id ', 'left');



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
            $this->db->order_by('appliance_model_details.model_number','ASC');
        }
        
        if(!empty($post['group_by'])){
            $this->db->group_by($post['group_by']);
        }
    }
    
    /**
     *  @desc : This function is used to get appliance_model_details data
     *  @param : $post string
     *  @param : $select string
     *  @return: Array()
     */
    function get_appliance_model_list($post, $select = "",$is_array = false) {
        $this->_get_appliance_model_list($post, $select);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        
        $query = $this->db->get();
        log_message('info', __METHOD__. " ".$this->db->last_query());
        if($is_array){
            return $query->result_array();
        }else{
            return $query->result();
        }
    }
    
    /**
     *  @desc : This function is used to get total appliance_model_details data
     *  @param : $post string
     *  @return: Array()
     */
    public function count_all_appliance_model_list($post) {
        $this->_get_appliance_model_list($post, 'count(distinct(appliance_model_details.id)) as numrows');
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
    }
    
    /**
     *  @desc : This function is used to get total filtered appliance_model_details data
     *  @param : $post string
     *  @return: Array()
     */
    function count_filtered_appliance_model_list($post){
        $this->_get_appliance_model_list($post, 'count(distinct(appliance_model_details.id)) as numrows');
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
    }
    
    /**
     * @desc This is used to insert details into appliance_model_details table
     * @param Array $details
     * @return string
     */
    function insert_appliance_model_data($details) {
      $this->db->insert('appliance_model_details', $details);
      return $this->db->insert_id();
    }
    
    /**
     * @desc This is used to update details of appliance_model_details table
     * @param Array $where
     * @param Array $data
     * @return boolean
     */
    function update_appliance_model_data($where, $data){
        $this->db->where($where);
        $this->db->update('appliance_model_details',$data);
        if($this->db->affected_rows() > 0 ){
            return TRUE;
        }else{
            return FALSE;
        }
        
    }
    
    /**
     * @desc This is used to insert details into inventory_model_mapping table
     * @param Array $data
     * @return string
     */
    function insert_inventory_model_mapping($data) {
      $this->db->insert_ignore('inventory_model_mapping', $data);
      return $this->db->insert_id();
    }
    
     /**
     * @desc This is used to Update courier details table
     * @param Array $data
     * @return boolearn
     */    
    function update_courier_detail($where, $data) {
        $this->db->where($where);
        $this->db->update('courier_details', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @desc: This function is used to update spare parts courier details in spare_parts_details table
     * @params: Array $id
     * @params: Array $data
     * @return: true if updated
     * 
     */
     function update_spare_courier_details($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('spare_parts_details', $data);
     
        
        if ($this->db->affected_rows() > 0) {
                return true;
            
        } else {
            return false;
        }
    }
    
    function insert_inventory_ledger_batch($data){
        $this->db->insert_batch("inventory_ledger", $data);
        return $this->db->insert_id();
    }
    
    function update_pending_inventory_stock_request($entity_type, $entity_id, $inventory_id, $qty){
        $sql = "Update inventory_stocks set pending_request_count = pending_request_count+ $qty WHERE "
                . "inventory_id = '".$inventory_id."' AND entity_type = '".$entity_type."' AND entity_id = '".$entity_id."' AND (pending_request_count+ $qty) > -1 ";
        $result = $this->db->query($sql);
        log_message('info', __METHOD__. " ".$this->db->last_query());
        return $result;
    }
    
    /**
     * @Desc: This function is used to get data from the inventory ledger table
     * @params: $limit string
     * @params: $start string
     * @params: $is_count boolean
     * @return: $query array
     * 
     */
    function get_tagged_spare_part_details($where = "") {
        $sql = "SELECT CASE WHEN(sc.name IS NOT NULL) THEN (sc.name) 
                WHEN(p.public_name IS NOT NULL) THEN (p.public_name) 
                WHEN (e.full_name IS NOT NULL) THEN (e.full_name) END as receiver, 
                CASE WHEN(sc1.name IS NOT NULL) THEN (sc1.name) 
                WHEN(p1.public_name IS NOT NULL) THEN (p1.public_name) 
                WHEN (e1.full_name IS NOT NULL) THEN (e1.full_name) END as sender,i.booking_id,i.invoice_id,invoice_details.description,
                invoice_details.hsn_code,invoice_details.qty,invoice_details.rate as basic_price,invoice_details.total_amount as total_amount,
                invoice_details.igst_tax_rate as gst_rate,i.create_date
                FROM `inventory_ledger` as i LEFT JOIN service_centres as sc on (sc.id = i.`receiver_entity_id` AND i.`receiver_entity_type` = 'vendor') Left JOIN partners as p on (p.id = i.`receiver_entity_id` AND i.`receiver_entity_type` = 'partner') LEFT JOIN employee as e ON (e.id = i.`receiver_entity_id` AND i.`receiver_entity_type` = 'employee')  
                LEFT JOIN service_centres as sc1 on (sc1.id = i.`sender_entity_id` AND i.`sender_entity_type` = 'vendor') 
                Left JOIN partners as p1 on (p1.id = i.`sender_entity_id` AND i.`sender_entity_type` = 'partner') 
                LEFT JOIN employee as e1 ON (e1.id = i.`sender_entity_id` AND i.`sender_entity_type` = 'employee') 
                JOIN invoice_details ON (i.invoice_id = invoice_details.invoice_id AND i.inventory_id = invoice_details.inventory_id)
                $where";

        $query = $this->db->query($sql)->result_array();

        return $query;
    }
    
    /**
     * @Desc: This function is used to get data from the courier_details table
     * @params: $select string
     * @params: $where array
     * @return: $query array
     * 
     */
    function get_spare_courier_details($select,$where){
        $this->db->select($select);
        if(!empty($where)){
            $this->db->where($where,false);
        }
        $this->db->from('inventory_ledger');
        $this->db->join('courier_details','inventory_ledger.courier_id = courier_details.id');
        $this->db->join('spare_parts_details','inventory_ledger.booking_id = spare_parts_details.booking_id');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * @desc: This function is used to insert warehouse data into warehouse_details table , warehouse_person_relationship and warehouse_state_relationship
     * using mysql transaction logic
     * @params: Array $data
     * @return: boolean
     * 
     */
    function insert_warehouse_details($wh_data,$wh_contact_person_mapping_data,$wh_state_mapping_data){
        //start mysql transaction
        $this->db->trans_begin();
        
        //insert warehouse details
        $this->db->insert('warehouse_details',$wh_data);
        $wh_id = $this->db->insert_id();
        
        $wh_contact_person_mapping_data['warehouse_id'] = $wh_id;
        //create warehouse and contact person mapping in contact person warehouse_person_relationship table
        $this->db->insert('warehouse_person_relationship',$wh_contact_person_mapping_data);
        
        //create new warehouse and state mapping
        $new_wh_state_mapping_data = array();
        foreach ($wh_state_mapping_data as $value) {
            $tmp_arr['warehouse_id'] = $wh_id;
            $tmp_arr['state'] = $value;
            $tmp_arr['create_date'] = date('Y-m-d H:i:s');
            $new_wh_state_mapping_data[] = $tmp_arr;
        }

        $this->db->insert_batch('warehouse_state_relationship', $new_wh_state_mapping_data);
        
        //complete mysql transaction
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE){
            return FALSE;
        }else{
            return TRUE;
        }
    }
    
    /**
     * @desc: This function is used to edit the warehouse details
     * @params: Array $where
     * @params: Array $data
     * @return: boolean
     */
    function edit_warehouse_details($where,$data){
        $this->db->where($where);
        $this->db->update('warehouse_details', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * @desc: This function is used to edit warehouse contatc person mapping
     * @params: Array $where
     * @params: Array $data
     * @return: boolean
     */
    function update_warehouse_contact_person_mapping($where,$data){
        $this->db->where($where);
        $this->db->update('warehouse_person_relationship', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    
   
    function get_spare_consolidated_data($select,$where,$group_by=''){
        $this->db->select($select,false);
        $this->db->from('booking_details');
        $this->db->join('spare_parts_details','booking_details.booking_id = spare_parts_details.booking_id');
        $this->db->join('partners','booking_details.partner_id = partners.id');
        $this->db->join('service_centres','booking_details.assigned_vendor_id = service_centres.id');
        $this->db->join('agent_filters',"partners.id = agent_filters.entity_id AND agent_filters.state = service_centres.state AND agent_filters.entity_type='"._247AROUND_EMPLOYEE_STRING."' ", "left"); // new query for AM
        $this->db->join('employee',"employee.id = agent_filters.agent_id", "left"); // new query for AM
        //$this->db->join('employee','partners.account_manager_id = employee.id'); // old query for AM
        $this->db->join('inventory_master_list as i', " i.inventory_id = spare_parts_details.requested_inventory_id", "left");
        $this->db->where($where,false);
        if(!empty($group_by)) {
            $this->db->group_by($group_by,false);
        }
        $query = $this->db->get();
        
        return $query;

    }
    
    /**
     * @desc: This function is used to insert the courier api data into database
     * @params: Array $data
     * @return: boolean
     */
    function  insert_courier_api_data($data){
        $this->db->insert_ignore_duplicate_batch('courier_tracking_details', $data);
         if($this->db->affected_rows() > 0){
            $res = TRUE;
        }else{
            $res = FALSE;
        }
        
        return $res;
    }
    
    
    /**
     * @desc: This function is used to get awb number details from database
     * @params: string $select
     * @params: Array $where
     * @return: Array $query
     */
    function get_awb_shippment_details($select, $where){
        $this->db->select($select,FALSE);
        $this->db->where($where,FALSE);
        $this->db->from('courier_tracking_details');
        $this->db->order_by("checkpoint_status_date", "desc");
        $query = $this->db->get();
        
        return $query->result_array();
    }   
    /**
     * @desc: This function is used to get courier services details like courier name, courier code
     * @params: string $select
     * @params: Array $where
     * @return: Array $query
     */
    function get_courier_services($select,$where = NULL){
        $this->db->select($select,FALSE);
        if(!empty($where)){
            $this->db->where($where,FALSE);
        }
        $this->db->from('courier_services');
        $this->db->order_by('courier_code','ASC');
        $query = $this->db->get();
        
        return $query->result_array();
        
    }
        
    function get_courier_details($select, $where){
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get("courier_details");
        return $query->result_array();
    }
    
    
    /**
     * @desc: This function is used to first delete old state mapping and then insert new state mapping for the warehouse
     * using mysql transaction logic
     * @params: Array $data
     * @return: boolean
     * 
     */
    function update_wh_state_mapping_data($data){
        //start mysql transaction
        $this->db->trans_begin();
        
        //delete old wh state mapping
        $this->db->where('warehouse_id', $data['wh_id']);
        $this->db->delete('warehouse_state_relationship');
        
        //create new warehouse and state mapping
        $wh_state_mapping_data = array();
        foreach ($data['new_wh_state_mapping'] as $value) {
            $tmp_arr['warehouse_id'] = $data['wh_id'];
            $tmp_arr['state'] = $value;
            $tmp_arr['create_date'] = date('Y-m-d H:i:s');
            $wh_state_mapping_data[] = $tmp_arr;
        }

        $this->db->insert_batch('warehouse_state_relationship', $wh_state_mapping_data);

        //complete mysql transaction
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE){
            return FALSE;
        }else{
            return TRUE;
        }
    }
    
     /**
     * @Desc: This function is used to get the courier company invoice details
     * @params: $select string
     * @params: $where string
     * @return: array
     * 
     */

    function get_courier_company_invoice_details($select, $where, $where_in=array()){
        $this->db->select($select);
        
        if(!empty($where)){
            $this->db->where($where);
        }
        
        if(!empty($where_in)){
            $this->db->where_in(key($where_in), $where_in[key($where_in)]);
        }

        $query = $this->db->get('courier_company_invoice_details');
        return $query->result_array();
    }
    
     /**
     * @Desc: This function is used to insert the courier company invoice details
     * @params: $select string
     * @params: $where string
     * @return: array
     * 
     */
    function insert_courier_company_invoice_details($details){
        $this->db->insert('courier_company_invoice_details', $details);
        return $this->db->insert_id();
    }
    
    /**
     * @desc: This function is used to update courier_company_invoice_details
     * @params: Array $where
     * @params: Array $data
     * @return: boolean
     */
    function update_courier_company_invoice_details($where,$data){
        $this->db->where($where);
        $this->db->update('courier_company_invoice_details', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    function update_bluk_spare_data($where_in, $data){
        log_message('info', __METHOD__. " ". print_r($where_in, true));
        if(!empty($where_in)){
            $this->db->where_in(key($where_in), $where_in[key($where_in)]);
            $this->db->update('spare_parts_details', $data);

            return TRUE;
        } else {
            return false;
        }
         
    }
    
    /**
     * @desc: This function is specifically used for upload docket number from excel to courier company invoice detail and recheck docket number
     * @params: Array $data
     * @return: Array $data
    */
    function update_docket_price($data){
        $courier_company_update_data = array();
        $returnData = array();
        $check = FALSE;
        $updateCharge = FALSE;
        if(isset($data['tid'])){
            $updateCharge = TRUE;
            $courier_company_detail[0]['id'] = $data['tid'];
        }
        else{
            $courier_company_detail = $this->get_courier_company_invoice_details('id, is_exist', array('awb_number' => $data['awb_number']));
            if(empty($courier_company_detail)){
                $courier_company_data = array(
                    'awb_number'=>$data['awb_number'],
                    'company_name'=>$data['courier_name'],
                    'courier_charge'=>$data['courier_charges'],
                    'courier_invoice_id'=>$data['invoice_id'],
                    'billable_weight'=>$data['billable_weight'],
                    'actual_weight'=>$data['actual_weight'],
                    'is_exist'=>0
                );
                $courier_company_detail[0]['id'] = $this->insert_courier_company_invoice_details($courier_company_data);
                $updateCharge = TRUE;
            }
            else{
                if($courier_company_detail[0]['is_exist'] == 0){
                    $courier_company_data_update = array(
                        'company_name'=>$data['courier_name'],
                        'courier_charge'=>$data['courier_charges'],
                        'courier_invoice_id'=>$data['invoice_id'],
                        'billable_weight'=>$data['billable_weight'],
                        'actual_weight'=>$data['actual_weight'],
                        'is_exist'=>0
                    );
                    $this->update_courier_company_invoice_details(array('id'=>$courier_company_detail[0]['id']), $courier_company_data_update);
                    $updateCharge = TRUE;
                }
                else if($courier_company_detail[0]['is_exist'] == 1){
                    $returnData['inValidData'] = $data['awb_number'];
                }
            }
        }
        if($updateCharge === TRUE){
            $data_spare_part_detail = $this->partner_model->get_spare_parts_by_any('spare_parts_details.id, awb_by_partner, awb_by_sf, booking_details.partner_id', array('awb_by_sf = "'.$data['awb_number'].'" OR awb_by_partner = "'.$data['awb_number'].'" AND status != "'._247AROUND_CANCELLED.'"'=>null), true);
            if(!empty($data_spare_part_detail)){
                $check =TRUE;
                $courier_company_update_data['partner_id'] = $data_spare_part_detail[0]['partner_id'];
                
                $courier_amount = sprintf('%0.2f', ($data['courier_charges']/count($data_spare_part_detail)));
                foreach ($data_spare_part_detail as  $value){
                    if($value['awb_by_sf'] == $data['awb_number']){
                       $courier_company_update_data['pickup_from'] = _247AROUND_SF_STRING;
                       $this->update_spare_courier_details($value['id'], array('courier_charges_by_sf'=>$courier_amount));
                    }
                    else if($value['awb_by_partner'] == $data['awb_number']){
                       $courier_company_update_data['pickup_from'] = _247AROUND_PARTNER_STRING;
                       $this->update_spare_courier_details($value['id'], array('courier_price_by_partner'=>$courier_amount)); 
                    }
                    
                }
            } else {
                $data_courier_detail = $this->get_courier_details("id, booking_id, sender_entity_type", array('AWB_no' => $data['awb_number']));
                if(!empty($data_courier_detail)){
                    $check = TRUE;
                    $courier_booking = explode(',', $data_courier_detail[0]['booking_id']);
                    $courier_company_update_data['partner_id'] = $this->reusable_model->get_search_result_data('booking_details', 'partner_id', array('booking_id'=>$courier_booking[0]), null, null, null, null, null, null)[0]['partner_id'];
                    $courier_amount = sprintf('%0.2f', ($data['courier_charges']/count($data_courier_detail)));
                    $courier_company_update_data['pickup_from'] = _247AROUND_PARTNER_STRING;
                    foreach ($data_courier_detail as  $value){
                        $courier_company_update_data['pickup_from'] = $value['sender_entity_type'];
                        $this->update_courier_detail(array('id'=>$value['id']), array('courier_charge'=>$courier_amount));
                    }
                } else {
                    $returnData['notfoundData'] = $data['awb_number'];
                }
            }
        }
        if($check === TRUE){
            $courier_company_update_data['is_exist'] = 1;
            $returnData['update_awb'] = $this->update_courier_company_invoice_details(array('id'=>$courier_company_detail[0]['id']), $courier_company_update_data);
        }
        return $returnData;
    }
    
   /**
     * @Desc: This function is used get the brackets shipped and service centre data for 
     * given order id
     * @params: string $Order_id
     * @return: array
     * 
     */
    
    function get_brackets_query($post) {        
             
        $this->_get_brackets_query($post);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        
        $query = $this->db->get();
        return $query->result();
    }
    
    
     public function _get_brackets_query($post) {
        $this->db->from('brackets');
        
        $this->db->select($post['select'], FALSE);

        $this->db->join('service_centres','brackets.order_received_from = service_centres.id', "left");
                
        if (!empty($post['where'])) {
            $this->db->where($post['where'], FALSE);
        }
        if(isset($post['where_in'])){
            foreach ($post['where_in'] as $index => $value) {

                $this->db->where_in($index, $value);
            }
        }
        
        if (!empty($post['search']['value'])) {
             $like = "";
            if(array_key_exists("column_search", $post)){
                foreach ($post['column_search'] as $key => $item) { // loop column 
                    // if datatable send POST for search
                    if ($key === 0) { // first loop
                        $like .= "( " . $item . " LIKE '%" . $post['search']['value'] . "%' ";

                    } else {
                        $like .= " OR " . $item . " LIKE '%" . $post['search']['value'] . "%' ";

                    }
                }
                $like .= ") ";
            }
            else{
                $like .= "(booking_details.booking_id LIKE '%" . $post['search']['value'] . "%')";
            }
            $this->db->where($like, null, false);
        }

//        if (!empty($post['order'])) { // here order processing
//            $this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
//        } else if (isset($this->order)) {
//            $order = $this->order;
//            $this->db->order_by(key($order), $order[key($order)]);
//        }
    }
    
    
    public function count_brackets_parts($post) {
        $this->db->from('brackets');       
        $this->db->join('service_centres','brackets.order_received_from = service_centres.id');
        if(isset($post['where'])){
             $this->db->where($post['where']);
        }
       
        $query = $this->db->count_all_results();

        return $query;

    }
    
    
    function count_brackets_filtered($post) {
        $this->_get_brackets_query($post);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    
       
    /**
     * @desc This is used to Insert Data In Table     
     * @table String $table
     * @return last inserted id
     */
    
    function insert_query($table,$data){
        if(!empty($table) && !empty($data)){
          $this->db->insert($table,$data);
          return $this->db->insert_id();   
        }        
    }
    
    /**
     * @desc This is used to get list of micro warehouse using vendor id     
     * @table micro_warehouse_state_mapping 
     * @return array
     */    
    function get_micro_wh_mapping_list($where, $select){
        $this->db->where($where);
        $this->db->select($select);
        $this->db->from('micro_warehouse_state_mapping');        
        $this->db->join('partners', 'partners.id = micro_warehouse_state_mapping.partner_id', 'RIGHT JOIN');
        $query =  $this->db->get();
        return $query->result_array();
    }
    /**
     * @desc This is used to get the micro warehouse lists by partner id     
     * @table multiple tables 
     * @return array list
     */
    
    function get_micro_wh_lists_by_partner_id($select, $where) {
        $this->db->select($select);
        $this->db->from('micro_warehouse_state_mapping AS micro_wh_mp');        
        $this->db->join('service_centres', 'service_centres.id = micro_wh_mp.vendor_id', 'RIGHT JOIN');
        $this->db->where($where);
        $query = $this->db->get();        
        $result= $query->result_array();
        return $result;
    }
    /**
     * @desc This is used to  inactive of active value in wh_on_of_status table     
     * @table only one
     * @return Json
     */
    function manage_micro_wh_from_list_by_id($id,$status) {
        $this->db->set('micro_warehouse_state_mapping.active', $status);
        $this->db->where('micro_warehouse_state_mapping.id', $id);
        $this->db->update('micro_warehouse_state_mapping');
        $afftected_row = $this->db->affected_rows();
        if(!empty($afftected_row)) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * @desc This is used to get  warehouse_on_of_status by id   
     * @table micro_warehouse_state_mapping 
     * @return array
     */    
    function get_warehouse_on_of_status_list($where, $select){
        $this->db->select($select);
        $this->db->from('micro_warehouse_state_mapping as m');            
        $this->db->join('warehouse_on_of_status as w_on_off', 'm.vendor_id = w_on_off.vendor_id');
        $this->db->group_by('m.vendor_id');     
        $this->db->where($where);        
        $query = $this->db->get();        
        return $query->result_array();
    }
     /**
     * @desc This is used to get  warehouse_on_of_status by id   
     * @table micro_warehouse_state_mapping 
     * @return array
     */   
    function get_micro_wh_state_mapping_partner_id($partner_id) {
        $this->db->select('m.*, s.name,s.district');
        $this->db->from('micro_warehouse_state_mapping as m');
        $where =array('m.active'=>1,'partner_id'=>$partner_id,'s.active'=>1,'s.on_off'=>1);      
        $this->db->join('service_centres as s', 's.id = m.vendor_id');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result_array();
    }
    /**
     * @desc This is used to get list of inventory stock count from inventory_stocks     
     * @table inventory_stocks 
     * @return array
     */    
    function get_inventory_stock_count_details($select,$where){       
        $this->db->select($select);
         $this->db->where($where);
        $query =  $this->db->get("inventory_stocks");
        return $query->result_array();
    }    
        
     /**
     * @desc This is used to get all courier invoices in data table format
     * @param $select, $post
     * @return array
     */  
    function get_searched_courier_invoices($select='*', $post){
        $this->_querySearchCourierInvoice($select, $post);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }
    
    function _querySearchCourierInvoice($select, $post){
        $this->db->from('courier_company_invoice_details');
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

        if (!empty($post['order'])) { // here order processing
            $this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
        } else if (isset($post['order_by'])) {
            $order = $post['order_by'];
            $this->db->order_by(key($order), $order[key($order)]);
        }
        
        if(isset($post['group_by']) && !empty($post['group_by'])){
            $this->db->group_by($post['group_by']);
        }
    }
    
     /**
     * @desc This is used to get count of all courier invoices in data table format
     * @param $post
     * @return array
     */  
    function count_courier_invoices($post){
        $this->db->from('courier_company_invoice_details');
        if(isset($post['where'])){
            $this->db->where($post['where']);
        }
        $query = $this->db->count_all_results();
        return $query;
    }
    
     /**
     * @desc This is used to get count of all filtered courier invoices in data table format
     * @param $select, $post
     * @return array
     */  
    function count_filtered_courier_invoices($select, $post) {
        $this->_querySearchCourierInvoice($select, $post);

        $query = $this->db->get();
        return $query->num_rows();
    }
    
       /**
     *  @desc : This function is used to get total inventory stocks
     *  @param : $post string
     *  @return: Array()
     */
    public function count_all_inventory_stocks_list($post) {
        $this->_get_inventory_stocks($post, "inventory_stocks.entity_id,inventory_stocks.entity_type, (SELECT SUM(stock) "
                . "FROM inventory_stocks as s "
                . "WHERE inventory_stocks.entity_id = s.entity_id ) as total_stocks,service_centres.name");
        $query = $this->db->get();
        log_message("info", __METHOD__."count all query ".$this->db->last_query());
        return $query->num_rows();
    }
    
    /**
     *  @desc : This function is used to get total filtered inventory stocks
     *  @param : $post string
     *  @return: Array()
     */
    function count_filtered_inventory_stocks_list($post){
        $sfIDArray =array();
        if($this->session->userdata('user_group') == 'regionalmanager'){
            $rm_id = $this->session->userdata('id');
            $rmServiceCentersData= $this->reusable_model->get_search_result_data("employee_relation","service_centres_id",array("agent_id"=>$rm_id),NULL,NULL,NULL,NULL,NULL);
            $sfIDList = $rmServiceCentersData[0]['service_centres_id'];
            $sfIDArray = explode(",",$sfIDList);
        }
        $this->_get_inventory_stocks($post, "inventory_stocks.entity_id,inventory_stocks.entity_type, (SELECT SUM(stock) "
                . "FROM inventory_stocks as s "
                . "WHERE inventory_stocks.entity_id = s.entity_id ) as total_stocks,service_centres.name");
        if($sfIDArray){
            $this->db->where_in('inventory_stocks.entity_id', $sfIDArray);
        }
        $query = $this->db->get();
        return $query->num_rows;
    }
      /**
     * @Desc: This function is used to get data from the  spare_parts_details table
     * @params: $select string
     * @params: $where array
     * @return: $query array
     * 
     */
    
    function get_spare_parts_details($select, $where=array(),$inventory_join = false,$booking_join = false){
        $this->db->select($select);
        $this->db->where($where);
        if($inventory_join){
            $this->db->join('inventory_master_list','spare_parts_details.shipped_inventory_id=inventory_master_list.inventory_id', "left");
        }
        if($booking_join){
            $this->db->join('booking_details','spare_parts_details.booking_id=booking_details.booking_id');
        }
        $query = $this->db->get("spare_parts_details");
        return $query->result_array();
    }
    
    
    /**
     * @Desc: This function is used to get data from the  spare_parts_details table
     * @params: $select string
     * @params: $where array
     * @return: $query array
     * 
     */
    
    function get_pending_spare_part_details($post, $where = array()) {
        $this->db->select($post['select'], FALSE);
        $this->db->from('spare_parts_details');
        $this->db->join('service_centres', 'spare_parts_details.service_center_id = service_centres.id');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * @Desc: This function is used to get data from the appliance_model_details table
     * @params: $select string
     * @params: $where array
     * @return: $query array
     * 
     */
    function get_inventory_parts_type_details($select,$where = array(),$is_join = false){
        $this->db->distinct();
        $this->db->select($select);
        if(!empty($where)){
            $this->db->where($where);
        }
        if($is_join){
            $this->db->join('services', 'inventory_parts_type.service_id = services.id');
            $this->db->join('hsn_code_details', 'inventory_parts_type.hsn_code_details_id= hsn_code_details.id');
        }
        $this->db->join('set_oow_part_type_margin', 'set_oow_part_type_margin.part_type_id = inventory_parts_type.id AND set_oow_part_type_margin.active = 1', 'left');
        $this->db->order_by('part_type','ASC');
        $query = $this->db->get('inventory_parts_type');
        return $query->result_array();
    }
    /**
     * @desc: This function is used to insert data in inventory_parts_type table
     * @params: Array of data
     * return : boolean
     */
    function insert_inventory_parts_type($data){
        
        $this->db->insert('inventory_parts_type', $data);
         if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }
        
    /**
     * @Desc: This function is used to update Inventory Parts Type
     * @params: Array, Int id
     * @return: Int
     * 
     */
    function update_inventory_parts_type($data,$where){
        $this->db->where($where);
	$this->db->update('inventory_parts_type', $data);
        if($this->db->affected_rows() > 0){
             log_message ('info', __METHOD__ . "=> Inventory Part Type  SQL ". $this->db->last_query());
            return true;
        }else{
            return false;
        }
        
    }
    
    
    /**
     * @Desc: This function is used to update Inventory Parts Type
     * @params: Array, Int id
     * @return: Int
     * 
     */
    function update_alternate_inventory_set($data,$where){
        $this->db->where($where);
	$this->db->update('alternate_inventory_set', $data);
        if($this->db->affected_rows() > 0){
             log_message ('info', __METHOD__ . "=> Alternate Inventory Set SQL ". $this->db->last_query());
            return true;
        }else{
            return false;
        }
        
    }
    
    /**
     * @Desc: This function is used to get inventory mapped model number
     * @params: $select string
     * @params: $where array
     * @return: $query array
     * 
     */
    function get_inventory_mapped_model_numbers($select,$where = array()){
        $this->db->distinct();
        $this->db->select($select);
        if(!empty($where)){
            $this->db->where($where);
        }
        $this->db->from('inventory_model_mapping');
        $this->db->join('appliance_model_details','inventory_model_mapping.model_number_id = appliance_model_details.id');
        $query = $this->db->get();
        return $query->result_array();
    }
    /**
     * @desc This function is used to get vendor margin & around margin for the OOW inventory
     * @param int $inventory_id
     * @param Array $part_type_array
     * @return Array
     */
    function get_oow_margin($inventory_id, $part_type_array){
        $spare_oow_est_margin = SPARE_OOW_EST_MARGIN * 100;
        $repair_oow_vendor_percentage = REPAIR_OOW_VENDOR_PERCENTAGE;
        $repair_oow_around_percentage = REPAIR_OOW_AROUND_PERCENTAGE * 100;
        $gst_rate = "";
        if(!empty($inventory_id)){
            $inventory = $this->get_inventory_master_list_data('gst_rate, oow_vendor_margin, oow_around_margin', array('inventory_id' => $inventory_id));
            $spare_oow_est_margin = $inventory[0]['oow_vendor_margin'] + $inventory[0]['oow_around_margin'];

            $repair_oow_vendor_percentage = $inventory[0]['oow_vendor_margin'];
            $repair_oow_around_percentage = $inventory[0]['oow_around_margin'];
            $gst_rate = $inventory[0]['gst_rate'];
        } else if($part_type_array){
            $part_type = $this->get_inventory_parts_type_details("oow_vendor_margin, oow_around_margin, hsn_code_details.gst_rate", $part_type_array, TRUE);

            if (!empty($part_type)) {
                if(!empty($part_type[0]['oow_around_margin']) && ($part_type[0]['oow_vendor_margin'] + $part_type[0]['oow_around_margin']) > 0){
                    $spare_oow_est_margin = ($part_type[0]['oow_vendor_margin'] + $part_type[0]['oow_around_margin']);
                    $repair_oow_vendor_percentage = $part_type[0]['oow_vendor_margin'];
                    $repair_oow_around_percentage = $part_type[0]['oow_around_margin'];
                    $gst_rate = $part_type[0]['gst_rate'];
                }
                
            }
        }
        
        return array(
            'oow_est_margin' => $spare_oow_est_margin,
            'oow_vendor_margin' => $repair_oow_vendor_percentage,
            'oow_around_margin' => $repair_oow_around_percentage,
            'gst_rate' => !(empty($gst_rate))? $gst_rate: ""
        );
        
    }
    
     /**
     * @Desc: This function is used to get data from the inventory_ledger table
     * @params: $select string
     * @params: $where array
     * @return: $query array
     * 
     */
    function get_inventory_ledger_details($select,$where){
        $this->db->select($select);
        if(!empty($where)){
            $this->db->where($where,false);
        }
        $this->db->from('inventory_ledger');        
        $query = $this->db->get();
        return $query->result_array();
    }
     /**
     * @Desc: This function is used to get data from the set_oow_part_type_margin table
     * @params: $select string
     * @params: $where array
     * @return: $query array
     * 
     */
    function get_oow_part_type_margin_details($select,$where,$where_in){
        $this->db->select($select);
        if(!empty($where)){
            $this->db->where($where,false);
        }
        if(!empty($where_in)){
            $this->db->where_in('set_oow_part_type_margin.part_type_id', $where_in);
        }
        $this->db->from('set_oow_part_type_margin');  
        $query = $this->db->get();
        return $query->result_array();
    }
    
    
        /**
     * @desc: This function is used to insert data in hsn_code_details table
     * @params: Array of data
     * return : boolean
     */
    function insert_hsn_code_details($data){        
        $this->db->insert('hsn_code_details', $data);
        return $this->db->insert_id();
    }
      
    
    /**
    * @desc: This function is used to insert data in inventory_alternate_spare_parts_mapping table
    * @params: Array of data
    * return : boolean
    */

     function insert_alternate_spare_parts($data) {
        $this->db->insert_ignore_duplicate_batch('inventory_alternate_spare_parts_mapping', $data);
        if($this->db->affected_rows() > 0){
            $res = TRUE;
        }else{
            $res = FALSE;
        }
        
        return $res;
    }

    /**
     * @Desc: This function is used to get data from the generic table
     * @params $table string 
     * @params: $select string
     * @params: $where array
     * @return: $query array
     * 
     */
     function get_generic_table_details($table, $select, $where, $where_in){
       
       $this->db->select($select);
       
        if(!empty($where)){
            $this->db->where($where);
        }
        
        if(!empty($where_in)){
           $this->db->where_in('alternate_inventory_set.inventory_id', $where_in);
        }
        
        $this->db->from($table);        
        $query = $this->db->get();         
        return $query->result_array(); 
    }
     /* @Desc: This function is used to get data from the spare_parts_details table
     * @params $table string 
     * @params: $select string
     * @params: $where array
     * @return: $query array
     * 
     */

    function getAwbCount($awb, $spid) {
        $sql = "SELECT * FROM spare_parts_details WHERE awb_by_sf='$awb'";
        $this->db->select("*");
        $count = count($this->db->query($sql)->result());
        $sql2 = "SELECT * FROM spare_parts_details WHERE awb_by_sf='$awb' and id='$spid'";
        $count2 = count($this->db->query($sql2)->result());
        if ($count2 > 0) {
            return $count;
        } else {
            return $count + 1;
        }
    }

     /* @desc: This function is used to insert data in alternate_inventory_set table
     * @params: Array of data
     * return : boolean
     */
    function insert_group_wise_inventory_id($data){
        
        $this->db->insert('alternate_inventory_set', $data);
         if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }
    
    
     /**
     * @Desc: This function is used to update alternate_inventory_set
     * @params: Array, Int id
     * @return: Int 
     */
    function update_group_wise_inventory_id($data,$where){
        $this->db->where($where);
	$this->db->update('alternate_inventory_set', $data);
        if($this->db->affected_rows() > 0){
             log_message ('info', __METHOD__ . "=> Inventory ID SQL ". $this->db->last_query());
            return true;
        }else{
            return false;
        }
        
    }
    
    /**
     * @Desc: This function is used to get alternate_inventory_set details
     * @params: Array, Int id
     * @return: Int 
     */
    function get_group_wise_inventory_id_detail($select, $inventory_id) {
        $this->db->select($select);
        $subquery = 'SELECT DISTINCT alternate_inventory_set.group_id FROM alternate_inventory_set WHERE alternate_inventory_set.inventory_id= ' . $inventory_id;
        $this->db->where('alternate_inventory_set.group_id IN (' . $subquery . ') ', NULL);
        $this->db->from('alternate_inventory_set');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    
     /**
     * @Desc: This function is used to get inventory stock details
     *  @params: $select string
     * @params: $where array
     * @return: $query array     * 
     */
    function get_inventory_stock_details($select, $where, $where_in) {
        $this->db->select($select);
        if (!empty($where)) {
            $this->db->where($where);
        }

        if (!empty($where_in)) {
            $this->db->where('inventory_stocks.inventory_id IN (' . $where_in . ') ', NULL);
        }
               
        $this->db->from('inventory_stocks');
        $this->db->join('inventory_master_list','inventory_master_list.inventory_id = inventory_stocks.inventory_id','left');
        $this->db->join('service_centres', 'inventory_stocks.entity_id = service_centres.id','left');
        $this->db->join('services', 'inventory_master_list.service_id = services.id','left');
        $this->db->order_by('inventory_stocks.stock', 'desc');
                
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * @Desc: This function is used to get data from the inventory_master_list
     * @params: $select string
     * @params: $where array
     * @return: $query array
     * 
     */
    function get_inventory_alternate_spare_parts($select, $where_in = array()) {
        $this->db->distinct();
        $this->db->select($select);

        if (!empty($where_in)) {
            $this->db->where('inventory_master_list.inventory_id IN (' . $where_in . ') ', NULL);
        }
        $this->db->from('inventory_master_list');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * @desc This function is used to return MSL data for the warehouse
     * @param String $date
     * @param int $inventory_id
     * @return Array
     */
    function get_msl_data($date, $inventory_id = ""){
       
        $this->db->select('public_name as company_name,ss.services, im.inventory_id,  part_name, part_number, '
                . 'im.type, price, im.gst_rate, count(s.id) as consumption, IFNULL(stock, 0) as stock ', FALSE);
        $this->db->from('spare_parts_details as s');
        $this->db->join('inventory_master_list as im', 's.requested_inventory_id = im.inventory_id');
        $this->db->join('partners as p', 'p.id = im.entity_id AND p.is_wh =1 ');
        $this->db->join('inventory_stocks as i', 'im.inventory_id = i.inventory_id', 'left');
        $this->db->join('service_centres as sc', 'sc.id = i.entity_id AND sc.is_wh = 1 ', 'left');
        $this->db->join('services as ss', 'ss.id = im.service_id', 'left');

        if(!empty($inventory_id)){
            $this->db->where('im.inventory_id', $inventory_id);
        }
        $this->db->where('s.status != "'._247AROUND_CANCELLED.'" ', NULL);
        $this->db->where('s.date_of_request >= "'.$date.'" ', NULL);
        $this->db->order_by('p.public_name, sc.name');
        $this->db->group_by('im.inventory_id, sc.id');
        $query = $this->db->get();
        return $query->result_array();
    }
    /**
     * @desc Used to get MicroWarehouse MSL
     * @param String $date
     * @param int $inventory_id
     * @return Array
     */
    function get_microwarehouse_msl_data($date, $inventory_id = ""){
        $this->db->select('public_name as company_name, sc.name as warehouse_name,ss.services, im.inventory_id,  part_name, part_number, '
                . 'im.type, ( (price + price *gst_rate/100)+ ((price + price *gst_rate/100) * oow_around_margin/100)) as price, im.gst_rate, count(s.id) as consumption, IFNULL(stock, 0) as stock ', FALSE);
        $this->db->from('spare_parts_details as s');
        $this->db->join('service_centres as sc', 'sc.id = s.service_center_id AND sc.is_micro_wh = 1 ');
        $this->db->join('inventory_master_list as im', 's.requested_inventory_id = im.inventory_id');
        $this->db->join('partners as p', 'p.id = im.entity_id AND p.is_micro_wh =1 ');
        $this->db->join('inventory_stocks as i', 'im.inventory_id = i.inventory_id AND sc.id = i.entity_id', 'left');
        $this->db->join('micro_warehouse_state_mapping as ms', 'ms.partner_id = p.id AND sc.id = ms.vendor_id AND ms.active = 1');
        $this->db->join('services as ss', 'ss.id = im.service_id', 'left');

        if(!empty($inventory_id)){
            $this->db->where('im.inventory_id', $inventory_id);
        }
        $this->db->where('s.status != "'._247AROUND_CANCELLED.'" ', NULL);
        $this->db->where('s.is_micro_wh','1');
        $this->db->where('s.date_of_request >= "'.$date.'" ', NULL);
        $this->db->order_by('p.public_name, sc.name, part_name');
        $this->db->group_by('im.inventory_id, sc.id');
        $query = $this->db->get();
        return $query->result_array();
    }
        /**
     * @Desc: This function is used to inser gst data data  
     * @params: $select string
     * @return: $id  
     * 
     */
    
    function  insert_entity_gst_data($data){
        $this->db->insert('entity_gst_details',$data);
        return $this->db->insert_id();

    }
    
    function get_entity_gst_data($select="entity_gst_details.*", $where){
        $this->db->select($select);
        $this->db->where($where);
        $this->db->join("state_code", "state_code.state_code = entity_gst_details.state");
        $query = $this->db->get("entity_gst_details");
        return $query->result_array();
    }
    /**
     * @Desc: This function is used to get data from the inventory_model_mapping
     * @params: $select string
     * @params: $where array
     * @return: $query array
     * 
     */
    function get_inventory_model_data($select, $where = array(), $where_in = array()) {
        $this->db->distinct();
        $this->db->select($select);
        if (!empty($where)) {
            $this->db->where($where);
        }
        
        if (!empty($where_in)) {
            foreach ($where_in as $index => $value) {
                $this->db->where_in($index, $value);
            }
        }
        $this->db->from('inventory_model_mapping');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * @Desc: This function is used to get Details of serviceable BOM
     * @params: $select string
     * @params: $where array
     * @return: $query array
     * 
     */
    function get_serviceable_bom_data($select, $where = array()) {
        $this->db->distinct();
        $this->db->select($select,false);
        $this->db->from('inventory_master_list');
        $this->db->join('inventory_model_mapping','inventory_model_mapping.inventory_id = inventory_master_list.inventory_id');
        $this->db->join('appliance_model_details','appliance_model_details.id = inventory_model_mapping.model_number_id');
        $this->db->join('services','services.id = appliance_model_details.service_id');
        if (!empty($where)) {
            $this->db->where($where,false);
        }        
        $query = $this->db->get();
        return $query;        
       
    }
   

    /**
     * @Desc: This function is used to get alternate parts
     * @params: $select string
     * @params: $where array
     * @return: $query array
     * 
     */
    function get_alternet_parts($select, $where = array()) {
        $this->db->select($select,false);
        $this->db->from('inventory_master_list');
        $this->db->join('alternate_inventory_set','alternate_inventory_set.inventory_id = inventory_master_list.inventory_id');
        $this->db->group_by("group_id");
        if (!empty($where)) {
            $this->db->where($where,false);
        }        
        $query = $this->db->get();
        return $query->result_array();        
       
    }
    
    /*
     * @Desc: This function is used to get Details of Missing serviceable BOM
     * @params: $select string
     * @params: $partner_id 
     * @params: $service_id 
     * @return: $query Object
     */
    
    function get_missing_serviceable_bom_data($select, $partner_id, $service_id) {

        $where = "";
        if (!empty($partner_id)) {
            $where = " WHERE NOT EXISTS (SELECT DISTINCT inventory_model_mapping.model_number_id FROM inventory_model_mapping WHERE inventory_model_mapping.model_number_id = appliance_model_details.id) AND services.id = service_id AND appliance_model_details.entity_id =" . $partner_id . " AND services.id=" . $service_id;
        }
        $sql = $select . " FROM appliance_model_details, services " . $where;

        $query = $this->db->query($sql);
        return $query;
    }
    
    
        
    /**
     * @Desc: This function is used to get inventory ledger details.
     * @params: $select string
     * @params: $where array
     * @return: $query array
     * 
     */
    function get_inventory_ledger_details_data($select, $where) {
        $this->db->distinct();
        $this->db->select($select, FALSE);
        $this->db->from('inventory_ledger');
        $this->db->join('vendor_partner_invoices', 'inventory_ledger.invoice_id = vendor_partner_invoices.invoice_id');
        $this->db->join('invoice_details', 'invoice_details.invoice_id = vendor_partner_invoices.invoice_id');
        $this->db->join('entity_gst_details As entt_gst_dtl', 'entt_gst_dtl.id = invoice_details.from_gst_number','left');
        $this->db->join('entity_gst_details', 'entity_gst_details.id = invoice_details.to_gst_number','left');
        $this->db->join('inventory_master_list', 'inventory_master_list.inventory_id = invoice_details.inventory_id');
        $this->db->join('courier_details', 'inventory_ledger.courier_id = courier_details.id','left');
        if (!empty($where)) {
            $this->db->where($where);
        }
        $query = $this->db->get();
        return $query;
    }

    /**
     * @Desc: This function is used to get data from the inventory_stocks table
     * @params: $post array
     * @params: $select string
     * @return: Object 
     */
    function get_warehouse_stocks($post, $select) {

        if (empty($select)) {
            $select = '*';
        }
        $this->db->distinct();
        $this->db->select($select, FALSE);
        $this->db->from('inventory_stocks');
        $this->db->join('inventory_master_list', 'inventory_master_list.inventory_id = inventory_stocks.inventory_id', 'left');
        $this->db->join('partners', 'partners.id = inventory_master_list.entity_id', 'left');
        $this->db->join('service_centres', 'inventory_stocks.entity_id = service_centres.id');

        $this->db->order_by("service_centres.name ASC, inventory_master_list.part_number ASC");
        
        if (!empty($post['where'])) {
            $this->db->where($post['where']);
        }
        $query = $this->db->get();
        return $query;
    }

}
