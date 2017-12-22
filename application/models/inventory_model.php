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
                . 'b.old_state,b.new_state,b.partner_id,b.service_center_id,b.agent_id');
        $this->db->where('order_id',$order_id);
        $this->db->join('booking_state_change b','b.booking_id = brackets.order_id');
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

}