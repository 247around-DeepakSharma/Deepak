<?php

class Inventory_model extends CI_Model {

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
                . 'b.old_state,b.new_state,employee.employee_id as agent_name,bookings_sources.source as partner_name');
        $this->db->where('order_id',$order_id);
        $this->db->join('booking_state_change b','b.booking_id = brackets.order_id');
        $this->db->join('employee', 'employee.id = b.agent_id');
        $this->db->join('bookings_sources', 'bookings_sources.partner_id = b.partner_id');
        $this->db->group_by('b.new_state');
        $this->db->order_by('brackets.create_date','asc');
        $query = $this->db->get('brackets');
        return $query->result_array();
        
    }
    
    /**
     * @Desc: This function is used to get brackets details of vendor
     *          whose brackets have been received successfully of particular month
     * @params: $vendor_id
     * @return: Array
     * 
     */
    function get_vendor_bracket_invoices($vendor_id,$from_date,$to_date){
       //Getting date range
     
        $sql = 'SELECT SUM(brackets.19_24_received) as _19_24_total, SUM(brackets.26_32_received) as _26_32_total,
                    SUM(brackets.36_42_received) as _36_42_total,SUM(brackets.43_received) as _43_total,
                    SUM(brackets.total_received) as total_received,
                    sc.name as vendor_name,sc.state,sc.sc_code,
                    CONCAT(  "", GROUP_CONCAT( DISTINCT (brackets.order_id) ) ,  "" ) AS order_id,
                    brackets.order_received_from as vendor_id,
                    sc.address as vendor_address, sc.owner_phone_1 as owner_phone_1,
                    sc.state
                    
                    FROM brackets,service_centres as sc WHERE brackets.received_date >= "'.$from_date.'" 
                    AND brackets.received_date <= "'.$to_date.'" AND brackets.is_received= "1" 
                    AND brackets.order_received_from = "'.$vendor_id.'" 
                    AND sc.id = brackets.order_received_from GROUP BY brackets.order_received_from';
        $query = $this->db->query($sql);
        return $query->result_array();
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
        $sql = "SELECT sc.company_name ,sc.address,sc.tin_no,sc.service_tax_no,sc.state,sc.sc_code,sc.owner_email,sc.owner_phone_1,b.19_24_shipped,
                b.26_32_shipped,b.36_42_shipped,b.43_shipped,b.total_shipped,b.order_received_from,b.shipment_date
                FROM brackets as b 
                JOIN service_centres as sc ON b.order_received_from = sc.id 
                WHERE b.order_id = '$order_id'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

}