<?php

class Cp_model extends CI_Model {

   var $column_order = array(NULL,'name', 'contact_person',NULL,NULL, 'shop_address_line1',NULL,'shop_address_region'); //set column field database for datatable orderable
   
    var $column_search = array('name', 'contact_person',
        'shop_address_region', 'primary_contact_number', 'shop_address_line1'); //set column field database for datatable searchable 
    var $order = array('name,bb_shop_address.shop_address_region ' => 'asc'); // default order 
    
    var $bb_select = 'bb_unit_details.partner_order_id,bb_order_details.partner_id, services,city, order_date, bb_cp_order_action.internal_status, delivery_date, bb_cp_order_action.current_status, partner_basic_charge, cp_basic_charge,cp_tax_charge,bb_unit_details.order_key, bb_unit_details.service_id, bb_order_details.assigned_cp_id,bb_cp_order_action.admin_remarks, bb_unit_details.category,bb_order_details.partner_tracking_id';
    
    /**
     * @desc load both db
     */

    function __construct() {
        parent::__Construct();
    }

    function get_cp_shop_address_list($post) {
        $this->_get_cp_shop_address_list_query($post);
        if (isset($post['length']) && $post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        
        $query = $this->db->get();
        
        return $query->result();
    }

    function _get_cp_shop_address_list_query($post) {
        $this->db->select('bb_shop_address.id, name, contact_person, '
                . 'shop_address_region,shop_address_city, primary_contact_number, alternate_conatct_number, shop_address_line1,'
                . 'shop_address_line2, shop_address_pincode, bb_shop_address.active,'
                . ' bb_shop_address.contact_email, tin_number, alternate_conatct_number2, shop_address_state,cp_capacity');

        $this->db->from('bb_shop_address');
        $this->db->join('service_centres', 'service_centres.id = bb_shop_address.cp_id ');
        if(isset($post['where']) && !empty($post['where'])){
            $this->db->where($post['where']);
        }
        //$this->db->join('partners', 'partners.id = bb_shop_address.partner_id ');
        if (!empty($post['search_value'])) { // if datatable send POST for search
        $like = "";
        foreach ($this->column_search as $key => $item) { // loop column 
           
                if ($key === 0) { // first loop
                    $like .= "( ".$item." LIKE '%".$post['search_value']."%' ";
                    //$this->db->like($item, $search_value);
                } else {
                    $like .= " OR ".$item." LIKE '%".$post['search_value']."%' ";
                    //$this->db->or_like($item, $search_value);
                }
            }
            $like .= ") ";

            $this->db->where($like, null, false);
        }
        if (!empty($post['order'])) { // here order processing
            $order = $post['order'];

            $this->db->order_by($this->column_order[$order[0]['column']], $order[0]['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function count_all_shop_address() {
        $this->db->from('bb_shop_address');
        return $this->db->count_all_results();
    }

    function count_filtered_shop_address($post) {
        $this->_get_cp_shop_address_list_query($post);
        $query = $this->db->get();

        return $query->num_rows();
    }
    
    function update_cp_shop_address($where, $data){
        $this->db->where($where);
        return $this->db->update('bb_shop_address', $data);
    }
    
    function insert_bb_cp_order_action($data){
        $this->db->insert('bb_cp_order_action',$data);
        return $this->db->insert_id();
    }

    /**
     * @desc this is used to get the buyback order data 
     * @param type $length
     * @param type $start
     * @param type $search_value
     * @param type $order
     * @param type $status_flag
     * @return Object
     */
    function get_bb_cp_order_list($post) {
       
        $this->bb_model->_get_bb_order_list_query($post,$this->bb_select);
        $this->db->join('bb_cp_order_action', 'bb_cp_order_action.partner_order_id = bb_unit_details.partner_order_id '
                . 'AND bb_order_details.assigned_cp_id = bb_cp_order_action.cp_id');

        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }
    /**
     * @desc This is used to get order data as requested
     * @param String $search_value
     * @param String $order
     * @param Int $status_flag
     * @return Number of rows
     */
    function cp_order_list_count_filtered($post) {
        $this->bb_model->_get_bb_order_list_query($post,$this->bb_select);
        $this->db->join('bb_cp_order_action', 'bb_cp_order_action.partner_order_id = bb_unit_details.partner_order_id '
                . 'AND bb_order_details.assigned_cp_id = bb_cp_order_action.cp_id');

        
        $query = $this->db->get();
        return $query->num_rows();
    }
    /**
     * @desc Used to return count of data as requested status
     * @param Array $post
     * @return Count
     */
    public function cp_order_list_count_all($post) {
        $this->bb_model->_count_all_bb_order($post);
        $this->db->join('bb_cp_order_action', 'bb_cp_order_action.partner_order_id = bb_unit_details.partner_order_id '
                . 'AND bb_order_details.assigned_cp_id = bb_cp_order_action.cp_id');

        $query = $this->db->count_all_results();
       
        return $query;
    }
    
    
    /**
     * @desc Used to check buyback order key 
     * @param $where array
     * @param $select array
     * @param $is_distinct default false
     * @return array
     */
    function check_order_key_exist($where, $select,$is_distinct=False){
        if($is_distinct){
            $this->db->distinct();
        }
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get("bb_unit_details");
        return $query->result_array();
    }
    
    
    /**
     * @desc Used to insert  the  buyback images mapped with order id
     * @param $data array
     * @return $inser_id string
     */
    function insert_bb_order_image($data){
        $insert_id = $this->db->insert('bb_order_image_mapping',$data);
        return $insert_id;
    }
    
    function update_bb_cp_order_action($where,$data){
        $this->db->where($where);
        return $this->db->update('bb_cp_order_action', $data);

    }
    
    function action_bb_cp_order_action($where,$data){
        $is_exist = $this->get_cp_order_action($where,"*");
        if(!empty($is_exist)){
            $this->update_bb_cp_order_action($where,array('cp_id' =>$data['cp_id']));
        } else {
            $this->insert_bb_cp_order_action($data);
        }
    }
    
    function insert_cp_shop_address($data){
        $this->db->insert('bb_shop_address',$data);
        return $this->db->insert_id();
    }
    
    function get_cp_order_action($where, $select){
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get('bb_cp_order_action');
        return $query->result_array();
    }
    
    
    /**
     * @desc Used to get cp histroy from log_entity_action table
     * @param $select string
     * @param $where array();
     * @return $query array();
     */
    function get_cp_history($select,$where){
        
        $this->db->select($select);
        $this->db->from('log_entity_action');
        $this->db->where($where);
        $this->db->join('employee', 'log_entity_action.agent_id = employee.id','left');
        $query = $this->db->get();
        return $query->result_array();
    }
}
