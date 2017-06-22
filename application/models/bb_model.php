<?php

class Bb_model extends CI_Model {

    var $column_order = array('bb_unit_details.partner_order_id', 'services', 'city',
        'order_date', 'delivery_date', 'current_status'); //set column field database for datatable orderable
    var $column_search = array('bb_unit_details.partner_order_id', 'services', 'city',
        'order_date', 'delivery_date', 'current_status'); //set column field database for datatable searchable 
    var $order = array('bb_order_details.id' => 'asc'); // default order 
    var $status = array('0' => array('Delivered'),
        '1' => array('Rejected', 'Cancelled', 'Lost', 'Unknown'),
        '2' => array('In-Transit', 'New Item In-transit', 'Attempted'),
        '3' => ''
        );

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }

    /**
     * @desc get order details
     * @param Array $where
     * @param String $select
     * @return Array
     */
    function get_bb_order_details($where, $select) {
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get("bb_order_details");

        return $query->result_array();
    }

    /**
     * @desc Get data from shop address
     * @param Array $where
     * @param String $select
     * @return Arary
     */
    function get_cp_shop_address_details($where, $select) {
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get("bb_shop_address");
        return $query->result_array();
    }

    function insert_bb_order_details($data) {
        $this->db->insert('bb_order_details', $data);
        return $this->db->insert_id();
    }

    function insert_bb_unit_details($data) {
        $this->db->insert('bb_unit_details', $data);
        return $this->db->insert_id();
    }

    function get_bb_state_change($where) {
        $this->db->select('*');
        $this->db->where($where);
        $query = $this->db->get("bb_state_change");
        return $query->result_array();
    }

    function insert_bb_state_change($details) {
        $this->db->insert('bb_state_change', $details);
        return $this->db->insert_id();
    }

    function update_bb_order_details($where, $data) {
        $this->db->where($where);
        return $this->db->update('bb_order_details', $data);
    }

    function update_bb_unit_details($where, $data) {
        $this->db->where($where);
        return $this->db->update('bb_unit_details', $data);
    }
    /**
     * @desc 
     * @param type $search_value
     * @param type $order
     * @param type $status_flag
     */
    private function _get_bb_order_list_query($search_value, $order, $status_flag) {
         $this->db->from('bb_order_details');

        $this->db->join('bb_unit_details', 'bb_order_details.partner_order_id = bb_unit_details.partner_order_id '
                . ' AND bb_order_details.partner_id = bb_unit_details.partner_id ');
        if($status_flag  == 3){
              $this->db->select('bb_unit_details.partner_order_id, city,order_date, '
                . 'delivery_date, current_status, partner_basic_charge, cp_basic_charge,cp_tax_charge');
              $this->db->where('assigned_cp_id IS NULL', NULL, FALSE);
              
        } else {
            $this->db->select('bb_unit_details.partner_order_id, services,city, order_date, '
                . 'delivery_date, current_status, partner_basic_charge, cp_basic_charge,cp_tax_charge');
            $this->db->where('assigned_cp_id IS NOT NULL', NULL, FALSE);
            $this->db->where_in('current_status', $this->status[$status_flag]);
            $this->db->join('services', 'services.id = bb_unit_details.service_id');
        }

        foreach ($this->column_search as $key => $item) { // loop column 
            if (!empty($search_value)) { // if datatable send POST for search
                if ($key === 0) { // first loop
                    $this->db->like($item, $search_value);
                } else {
                    if($status_flag == 3 && $key ==1){
                        //Unassigned booking need not to search services
                    } else {
                        $this->db->or_like($item, $search_value);
                    }
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
    /**
     * 
     * @param type $length
     * @param type $start
     * @param type $search_value
     * @param type $order
     * @param type $status_flag
     * @return Object
     */
    function get_bb_order_list($length, $start, $search_value, $order, $status_flag) {
        $this->_get_bb_order_list_query($search_value, $order, $status_flag);
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
    function count_filtered($search_value, $order, $status_flag) {
        $this->_get_bb_order_list_query($search_value, $order, $status_flag);
        $query = $this->db->get();

        return $query->num_rows();
    }
    /**
     * @desc Used to return count of data as requested status
     * @param Int $status_flag
     * @return Count
     */
    public function count_all($status_flag) {
        $this->db->from('bb_order_details');
        $this->db->where_in('current_status', $this->status[$status_flag]);
        return $this->db->count_all_results();
    }    
    /**
     * @desc This function is used to insert charges list excel data
     * @para, $charges_data array
     * @return boolean
     */
    function insert_charges_data_in_batch($charges_data){
        $this->db->truncate("bb_charges");
        return $this->db->insert_batch("bb_charges", $charges_data);
    }
    
    
    /**
     * @desc 
     * @param type $search_value
     * @param type $order
     * @param type $status_flag
     */
    private function _get_bb__review_order_list_query($search_value, $order) {
         $this->db->from('bb_cp_order_action as cp_action');

        $this->db->join('service_centres as cp', 'cp_action.cp_id = cp.id');
        $this->db->select('cp_action.id,cp_action.partner_order_id,cp_action.cp_id,cp_action.category,cp_action.brand,cp_action.physical_condition,
            cp_action.working_condition,cp_action.status,cp_action.remarks,cp_action.current_status, cp.name');
        $this->db->where('current_status','In_process');
        foreach ($this->cp_action_column_search as $key => $item) { // loop column 
            if (!empty($search_value)) { // if datatable send POST for search
                if ($key === 0) { // first loop
                    $this->db->like($item, $search_value);
                } else {
                   $this->db->or_like($item, $search_value);
                }
            }
           
        }

        if (!empty($order)) { // here order processing
            $this->db->order_by($this->cp_action_column_order[$order[0]['column'] - 1], $order[0]['dir']);
        } else if (isset($this->cp_action_column_default_order)) {
            $order = $this->cp_action_column_default_order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
    /**
     * 
     * @param type $length
     * @param type $start
     * @param type $search_value
     * @param type $order
     * @param type $status_flag
     * @return Object
     */
    function get_bb_review_order_list($length, $start, $search_value, $order) {
        $this->_get_bb__review_order_list_query($search_value, $order);
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
    function count_filtered_review_order($search_value, $order) {
        $this->_get_bb__review_order_list_query($search_value, $order);
        $query = $this->db->get();

        return $query->num_rows();
    }
    /**
     * @desc Used to return count of data as requested status
     * @param Int $status_flag
     * @return Count
     */
    public function count_all_review_order() {
        $this->db->from('bb_cp_order_action');
        $this->db->where('current_status','In_process');
        return $this->db->count_all_results();
    }
    
    /**
     * @desc Used to get the  buyback image link
     * @param $where array
     * @param $select array
     * @param $is_distinct default false
     * @return array
     */
    function get_bb_order_images($where, $select,$is_distinct=False){
        if($is_distinct){
            $this->db->distinct();
        }
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get("bb_order_image_mapping");
       
        return $query->result_array();
    }
    
    
    /**
     * @desc Used to approve buyback order in bulk
     * @param $data array
     * @return boolean
     */
    function approved_bb_orders($data){
        foreach ($data as $value) {
            $this->db->where('id', $value);
            $this->db->update('bb_cp_order_action', array('current_status' => 'Completed'));
        }

        return TRUE;
    }
    
    function get_bb_order_history($order_id){
        $sql = "SELECT bb_sc.old_state, bb_sc.new_state,bb_sc.remarks,e.full_name as agent_name,
                cp.name as cp_name, p.public_name as partner_name 
                FROM bb_state_change as bb_sc 
                LEFT JOIN employee as e ON bb_sc.agent_id = e.id 
                LEFT JOIN service_centres as cp ON bb_sc.service_center_id = cp.id 
                LEFT JOIN partners as p ON bb_sc.partner_id = p.id 
                WHERE bb_sc.order_id = '$order_id'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    function get_bb_order_appliance_details($partner_order_id){
        $sql = "SELECT bb_unit.* , s.services as service_name
                FROM bb_unit_details as bb_unit 
                JOIN services as s ON bb_unit.service_id = s.id 
                WHERE bb_unit.partner_order_id = '$partner_order_id'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    function get_bb_order_detailed_data($order_id){
        $sql = "SELECT bb_od.*,cp.name as cp_name, p.public_name as partner_name
                FROM bb_order_details as bb_od 
                LEFT JOIN service_centres as cp ON bb_od.assigned_cp_id = cp.id 
                LEFT JOIN partners as p ON bb_od.partner_id = p.id 
                WHERE bb_od.partner_order_id = '$order_id'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    
}

