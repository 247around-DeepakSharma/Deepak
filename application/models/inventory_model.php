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
     * @desc: This fucntion is used to insert data in brackets table
     * params: Array of data
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
     * @desc: This fucntion is used to insert data in inventory table
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
                . "DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(estimate_cost_given_date, '%Y-%m-%d')) AS age_of_est_given", FALSE);

        $this->db->join('booking_details','spare_parts_details.booking_id = booking_details.booking_id');
        $this->db->join('partners','partners.id = spare_parts_details.partner_id', "left");
        $this->db->join('users','users.user_id = booking_details.user_id');
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
            foreach ($post['column_search'] as $key => $item) { // loop column 
                // if datatable send POST for search
                if ($key === 0) { // first loop
                    $like .= "( " . $item . " LIKE '%" . $post['search']['value'] . "%' ";
                   
                } else {
                    $like .= " OR " . $item . " LIKE '%" . $post['search']['value'] . "%' ";
                   
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
    function get_inventory_master_list_data($select,$where = array()){
        $this->db->select($select);
        if(!empty($where)){
            $this->db->where($where);
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
        $this->db->join('inventory_master_list','inventory_master_list.inventory_id = inventory_stocks.inventory_id');
        $this->db->join('service_centres', 'inventory_stocks.entity_id = service_centres.id','left');
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
            $this->db->order_by('inventory_stocks.entity_id','ASC');
        }
        
        if(!empty($post['group_by'])){
            $this->db->group_by($post['group_by']);
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
                LEFT JOIN service_centres as sc1 on (sc1.id = i.`sender_entity_id` AND i.`sender_entity_type` = 'vendor') Left JOIN partners as p1 on (p1.id = i.`sender_entity_id` AND i.`sender_entity_type` = 'partner') LEFT JOIN employee as e1 ON (e1.id = i.`sender_entity_id` AND i.`sender_entity_type` = 'employee') $where $add_limit";
        
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
                    $query[$key]['agent_name'] = $employe_details[0]['full_name'];
                }else if($value['agent_type'] === _247AROUND_PARTNER_STRING){
                    $partner_details = $this->partner_model->getpartner_details('public_name',array('partners.id'=>$value['agent_id']));
                    $query[$key]['agent_name'] = $partner_details[0]['public_name'];
                }else if($value['agent_type'] === _247AROUND_SF_STRING){
                    $vendor_details = $this->vendor_model->getVendorDetails('name',array('id'=>$value['agent_id']));
                    $query[$key]['agent_name'] = $vendor_details[0]['name'];
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
        log_message("info", print_r($post,true));
        if($is_object){
            return $query->result();
        } else {
            return $query->result_array();
        }
        
    }
    
    /**
     *  @desc : This function is used to get total inventory stocks
     *  @param : $post string
     *  @return: Array()
     */
    public function count_all_inventory_stocks($post) {
        $this->_get_inventory_stocks($post, 'count(inventory_stocks.entity_id) as numrows');
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
        $this->_get_inventory_stocks($post, 'count(inventory_stocks.entity_id) as numrows');
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
    function get_warehouse_details($select,$where, $join = true) {
        $this->db->select($select,FALSE);
        $this->db->where($where,FALSE);
        $this->db->from('warehouse_person_relationship');
        $this->db->join('contact_person','warehouse_person_relationship.contact_person_id = contact_person.id');
        $this->db->join('warehouse_details','warehouse_person_relationship.warehouse_id = warehouse_details.id');
        if($join){
            $this->db->join('warehouse_state_relationship','warehouse_person_relationship.warehouse_id = warehouse_state_relationship.warehouse_id');
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
        $this->db->select($select);
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
        $this->db->join('services', 'appliance_model_details.service_id = services.id','left');
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

}