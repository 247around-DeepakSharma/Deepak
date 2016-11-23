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
        
        $this->db->insert_batch('brackets', $data);
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
    function get_brackets(){
        $this->db->select('*');
        $this->db->order_by('order_id','desc');
        $query = $this->db->get('brackets');
        return $query->result_array();
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
     * @Desc: This function is used to update inventory
     * @params: Array, String ,String
     * @return: Boolean
     * 
     */
    function update_inventory($data,$order_id,$remarks){
        $this->db->where(array(
            'order_id' => $order_id,
            'remarks' => $remarks
            ));
	$this->db->update('inventory', $data);
        if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
        
    }
    
    /**
     * @Desc: This function is used to get inventory details by vendor id
     * params: vendor_id
     * return: void
     * 
     */
    function get_vendor_inventory_details($vendor_id){
        
        $this->db->select('inventory.id,sc.id as sc_id,sc.name as sc_name,'
                . 'inventory.19_24_current_count,inventory.26_32_current_count,inventory.36_42_current_count,'
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
    function get_distict_vendor_from_inventory(){
        $sql = "SELECT DISTINCT `vendor_id` FROM (`inventory`) "
                . "Order By `vendor_id`";
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
        $this->db->select('brackets.id,brackets.order_id,brackets.invoice_id,brackets.order_received_from,brackets.order_given_to,brackets.order_date,brackets.shipment_date,'
                . 'brackets.received_date,brackets.19_24_requested,brackets.26_32_requested,brackets.36_42_requested,'
                . 'brackets.total_requested,brackets.19_24_shipped,brackets.26_32_shipped,brackets.36_42_shipped,brackets.total_shipped,'
                . 'brackets.19_24_received,brackets.26_32_received,brackets.36_42_received,brackets.total_received,brackets.is_shipped,brackets.is_received,'
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
    function get_vendor_bracket_invoices($vendor_id,$date_range){
        //Getting date range
        $custom_date = explode("-", $date_range);
        $from_date = $custom_date[0];
        $to_date = $custom_date[1];
        
        $this->db->select('SUM(brackets.19_24_received) as 19_24_total, SUM(brackets.26_32_received) as 26_32_total,'
                . 'SUM(brackets.36_42_received) as 36_42_total,SUM(brackets.total_received) as total_received,'
                . 'sc.name as vendor_name,tax_rates.rate as tax_rate,brackets.order_id,brackets.order_given_to as vendor_id,'
                . 'sc.address as vendor_address, sc.owner_phone_1 as owner_phone_1');
        $this->db->where('brackets.received_date >=', $from_date);
        $this->db->where('brackets.received_date <=', $to_date);
        $this->db->where('brackets.is_received', 1);
        $this->db->where('brackets.order_given_to', $vendor_id);
        $this->db->where('tax_rates.product_type', 'wall_bracket');
        $this->db->join('service_centres as sc','sc.id = brackets.order_given_to');
        $this->db->join('tax_rates','sc.state = tax_rates.state');
        $this->db->group_by('brackets.order_given_to');
        $query = $this->db->get('brackets');
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
    
    
}