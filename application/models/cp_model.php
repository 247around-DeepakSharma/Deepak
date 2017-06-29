<?php

class Cp_model extends CI_Model {

    var $column_order = array('public_name', 'name', 'contact_person', 'shop_address_city'); //set column field database for datatable orderable
    var $column_search = array('public_name', 'name', 'contact_person',
        'shop_address_city', 'primary_contact_number', 'alternate_conatct_number', 'shop_address_line1'); //set column field database for datatable searchable 
    var $order = array('name,bb_shop_address.shop_address_city ' => 'asc'); // default order 
    
    var $bb_unit_column_order = array(
        '0' =>   array('bb_unit_details.partner_order_id', 'services', 'city',
                       'physical_condition','working_condition','cp_basic_charge','current_status',
                       'delivery_date',null,null),
        '1' =>   array('bb_unit_details.partner_order_id', 'services', 'city',
                       'physical_condition','working_condition','cp_basic_charge','current_status',
                       'order_date',null,null)
         );
    var $bb_unit_column_search = array(
        '0' =>   array('bb_unit_details.partner_order_id', 'services', 'city',
        'order_date', 'delivery_date', 'current_status'),
        '1' =>   array( 'bb_unit_details.partner_order_id', 'services', 'city',
        'order_date', 'delivery_date', 'current_status')
         );
    var $bb_unit_order = array('bb_order_details.order_date' => 'desc'); // default order 
    var $bb_unit_status = array('0' => array('Delivered'),
        '1' => array('In-Transit', 'New Item In-transit', 'Attempted'));
    

    /**
     * @desc load both db
     */

    function __construct() {
        parent::__Construct();
    }

    function get_cp_shop_address_list($length, $start, $search_value, $order) {
        $this->_get_cp_shop_address_list_query($search_value, $order);
        if ($length != -1) {
            $this->db->limit($length, $start);
        }
        $query = $this->db->get();
       //  echo $this->db->last_query();"<br/>";
        return $query->result();
    }

    function _get_cp_shop_address_list_query($search_value, $order) {
        $this->db->select('bb_shop_address.id, public_name, name, contact_person, '
                . 'shop_address_city, primary_contact_number, alternate_conatct_number, shop_address_line1,'
                . 'shop_address_line2, shop_address_pincode, bb_shop_address.active,'
                . ' bb_shop_address.contact_email, tin_number, alternate_conatct_number2, shop_address_state');

        $this->db->from('bb_shop_address');
        $this->db->join('service_centres', 'service_centres.id = bb_shop_address.cp_id ');
        $this->db->join('partners', 'partners.id = bb_shop_address.partner_id ');

        foreach ($this->column_search as $key => $item) { // loop column 
            if (!empty($search_value)) { // if datatable send POST for search
                if ($key === 0) { // first loop
                    $this->db->like($item, $search_value);
                } else {

                    $this->db->or_like($item, $search_value);
                }
            }
        }

        if (!empty($order)) { // here order processing
            $this->db->order_by($this->column_order[$order[0]['column'] - 1], $order[0]['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function count_all_shop_address() {
        $this->db->from('bb_shop_address');
        return $this->db->count_all_results();
    }

    function count_filtered_shop_address($search_value, $order) {
        $this->_get_cp_shop_address_list_query($search_value, $order);
        $query = $this->db->get();

        return $query->num_rows();
    }
    
    function update_cp_shop_address($where, $data){
        $this->db->where($where);
        return $this->db->update('bb_shop_address', $data);
    }
    
    /**
     * @desc this is used to make the query for buyback order data
     * @param type $search_value
     * @param type $order
     * @param type $status_flag
     */
    private function _get_bb_cp_order_list_query($search_value, $order, $status_flag) {
        $this->db->select('bb_order_details.id,bb_unit_details.partner_order_id, services,city, order_date, '
                . 'delivery_date, bb_order_details.current_status, cp_basic_charge,cp_tax_charge,bb_unit_details.physical_condition,'
                . 'bb_unit_details.working_condition,bb_unit_details.service_id,bb_order_details.city');
        $this->db->from('bb_order_details');

        $this->db->join('bb_unit_details', 'bb_order_details.partner_order_id = bb_unit_details.partner_order_id '
                . ' AND bb_order_details.partner_id = bb_unit_details.partner_id ');
        
        $this->db->join('bb_cp_order_action', 'bb_cp_order_action.partner_order_id = bb_unit_details.partner_order_id '
                . 'AND bb_order_details.assigned_cp_id = bb_cp_order_action.cp_id');
        
        $this->db->join('services', 'services.id = bb_unit_details.service_id');
        
        if($status_flag === '0'){
            
            $this->db->where('bb_cp_order_action.current_status','Pending');
            $this->db->where('bb_cp_order_action.internal_status','Delivered');
        } else{
            $this->db->where('bb_cp_order_action.current_status','Pending');
            $this->db->where_in('bb_cp_order_action.internal_status',$this->bb_unit_status[$status_flag]);
        }
       
        $this->db->where_in('bb_order_details.current_status', $this->bb_unit_status[$status_flag]);
        $this->db->where_in('bb_order_details.internal_status',$this->bb_unit_status[$status_flag]);
        
        $this->db->where('assigned_cp_id',$this->session->userdata('service_center_id'));
        

        $i = 0;

        foreach ($this->bb_unit_column_search[$status_flag] as $item) { // loop column 
            if (!empty($search_value)) { // if datatable send POST for search
                if ($i === 0) { // first loop
                    $this->db->like($item, $search_value);
                } else {
                    $this->db->or_like($item, $search_value);
                }
            }
            $i++;
        }

        if (!empty($order)) { // here order processing
            $this->db->order_by($this->bb_unit_column_order[$status_flag][$order[0]['column'] - 1], $order[0]['dir']);
        } else if (isset($this->bb_unit_order)) {
            $order = $this->bb_unit_order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
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
    function get_bb_cp_order_list($length, $start, $search_value, $order, $status_flag) {
        $this->_get_bb_cp_order_list_query($search_value, $order, $status_flag);
        if ($length != -1) {
            $this->db->limit($length, $start);
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
    function cp_order_list_count_filtered($search_value, $order, $status_flag) {
        $this->_get_bb_cp_order_list_query($search_value, $order, $status_flag);
        $query = $this->db->get();

        return $query->num_rows();
    }
    /**
     * @desc Used to return count of data as requested status
     * @param Int $status_flag
     * @return Count
     */
    public function cp_order_list_count_all($status_flag) {
        $this->db->from('bb_order_details');
        $this->db->join('bb_unit_details', 'bb_order_details.partner_order_id = bb_unit_details.partner_order_id '
                . ' AND bb_order_details.partner_id = bb_unit_details.partner_id ');
        $this->db->join('bb_cp_order_action', 'bb_cp_order_action.partner_order_id = bb_unit_details.partner_order_id '
                . 'AND bb_order_details.assigned_cp_id = bb_cp_order_action.cp_id');
         if($status_flag === '0'){
            
            $this->db->where('bb_cp_order_action.current_status','Pending');
            $this->db->where('bb_cp_order_action.internal_status','Delivered');
        } else{
            $this->db->where('bb_cp_order_action.current_status','Pending');
            $this->db->where_in('bb_cp_order_action.internal_status',$this->bb_unit_status[$status_flag]);
        }
        $this->db->where_in('bb_order_details.current_status', $this->bb_unit_status[$status_flag]);
        $this->db->where('assigned_cp_id',$this->session->userdata('service_center_id'));
        return $this->db->count_all_results();
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
    
    
    /**
     * @desc Used to insert  the  buyback updated data
     * @param $data array
     * @return $inser_id string
     */
    function insert_bb_order_status($data){
        $insert_id = $this->db->insert('bb_cp_order_action',$data);
        return $insert_id;
    }
    
    function update_bb_cp_order_action($where,$data){
        $this->db->where($where);
        return $this->db->update('bb_cp_order_action', $data);
    }

}
