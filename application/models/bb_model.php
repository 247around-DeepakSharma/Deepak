<?php

class Bb_model extends CI_Model {

    //set column field database for datatable orderable       

    var $order = array('bb_order_details.order_date' => 'desc'); // default order 
    var $cp_action_column_search = array('bb.partner_order_id', 'name', 'cp_action.category', 'cp_action.brand', 'cp_action.physical_condition', 'cp_action.working_condition', 'bb.internal_status');
    var $cp_action_column_order = array('bb.partner_order_id', 'name', 'cp_action.category', 'cp_action.brand', 'cp_action.physical_condition', 'cp_action.working_condition', 'bb.internal_status');
    var $cp_action_column_default_order = array('cp_action.id' => 'asc'); // default order 
    var $bb_select = 'bb_unit_details.category,bb_unit_details.partner_order_id,bb_order_details.partner_id, service_id,services,city, order_date, bb_order_details.internal_status, delivery_date, bb_order_details.current_status,'
            . 'partner_basic_charge, cp_basic_charge,cp_tax_charge,bb_unit_details.order_key, bb_unit_details.service_id,bb_order_details.assigned_cp_id,bb_unit_details.physical_condition,bb_unit_details.working_condition,'
            . 'bb_order_details.partner_tracking_id as tracking_id,bb_order_details.acknowledge_date,bb_unit_details.cp_claimed_price';

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
        $this->db->from('bb_order_details');
        $this->db->join('service_centres', 'bb_order_details.assigned_cp_id = service_centres.id', 'left');
        $this->db->join('partners', 'bb_order_details.partner_id = partners.id');
        $this->db->where($where);
        $query = $this->db->get();

        return $query->result_array();
    }

    /**
     * @desc To get order details without CP details. 
     * Note:- Do not try to make another join in this function.
     * @param Array $where
     * @param String $select
     * @return Array
     */
    function get_bb_order($where, $select, $order_by = "") {
        $this->db->distinct();
        $this->db->select($select);
        $this->db->from('bb_order_details');
        $this->db->join('partners', 'bb_order_details.partner_id = partners.id');
        $this->db->where($where);
        if (!empty($order_by)) {
            $this->db->order_by($order_by);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * @desc Get data from shop address
     * @param Array $where
     * @param String $select
     * @return Arary
     */
    function get_cp_shop_address_details($where, $select, $order_by = false) {
        $this->db->distinct();
        $this->db->select($select);
        $this->db->from('bb_shop_address');
        $this->db->join("service_centres", 'service_centres.id = cp_id');
        $this->db->where($where);
        if ($order_by) {
            $this->db->order_by($order_by);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    function insert_bb_order_details($data) {
        $this->db->insert_ignore('bb_order_details', $data);
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
    public function _get_bb_order_list_query($post, $select = "") {
        $this->db->from('bb_order_details');
        if (empty($select)) {
            $select = $this->bb_select;
        }
        $this->db->select($select, FALSE);

        $this->db->join('bb_unit_details', 'bb_order_details.partner_order_id = bb_unit_details.partner_order_id '
                . ' AND bb_order_details.partner_id = bb_unit_details.partner_id ');

        $this->db->join('services', 'services.id = bb_unit_details.service_id');
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

    /**
     * 
     * @param type $length
     * @param type $start
     * @param type $search_value
     * @param type $order
     * @param type $status_flag
     * @return Object
     */
    function get_bb_order_list($post, $select = "",$cp_name = 0) {
        $this->_get_bb_order_list_query($post, $select);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        if($cp_name){
           $this->db->join('service_centres', 'service_centres.id = bb_order_details.assigned_cp_id');
        }
         $query = $this->db->get();
        return $query->result();
    }

    /**
     * @desc Used to return count of data as requested status
     * @param Array $post
     * @return Count
     */
    public function count_all($post) {
        $this->_count_all_bb_order($post);
        $query = $this->db->count_all_results();

        return $query;
    }

    public function _count_all_bb_order($post) {
        $this->db->from('bb_order_details');
        $this->db->join('bb_unit_details', 'bb_order_details.partner_order_id = bb_unit_details.partner_order_id '
                . ' AND bb_order_details.partner_id = bb_unit_details.partner_id ');

        $this->db->join('services', 'services.id = bb_unit_details.service_id');
        $this->db->where($post['where']);
        foreach ($post['where_in'] as $index => $value) {
            $this->db->where_in($index, $value);
        }
    }

    function count_filtered($post) {
        $this->_get_bb_order_list_query($post);

        $query = $this->db->get();
        return $query->num_rows();
    }

    /**
     * @desc This function is used to insert charges list excel data
     * @para, $charges_data array
     * @return boolean
     */
    function insert_charges_data_in_batch($charges_data) {
        $this->db->truncate("bb_charges");
        return $this->db->insert_batch("bb_charges", $charges_data);
    }

    /**
     * @desc 
     * @param type $search_value
     * @param type $order
     * @param type $status_flag
     */
    private function _get_bb__review_order_list_query($search_value, $order,$where = NULL) {
        $this->db->from('bb_cp_order_action as cp_action');
        $this->db->join('bb_unit_details as unit', 'unit.partner_order_id = cp_action.partner_order_id');
        $this->db->join('service_centres as cp', 'cp_action.cp_id = cp.id');
        $this->db->join('bb_order_details as bb', 'cp_action.partner_order_id = bb.partner_order_id');
        $this->db->select('cp_action.id,cp_action.partner_order_id,cp_action.cp_id,cp_action.category,cp_action.brand,cp_action.physical_condition,
            cp_action.working_condition,cp_action.remarks,cp_action.internal_status,cp_action.cp_claimed_price, cp.name,bb.partner_tracking_id,(cp_basic_charge+cp_tax_charge) as cp_price
            ,(partner_basic_charge+partner_tax_charge) as partner_price');
        $this->db->where('cp_action.current_status', _247AROUND_BB_IN_PROCESS);
        if($where){
            $this->db->where($where);
        }
        if (!empty($search_value)) { // if datatable send POST for search
            $like = "";
            foreach ($this->cp_action_column_search as $key => $item) { // loop column 
                if ($key === 0) { // first loop
                    // $this->db->like($item, $search_value);
                    $like .= "( " . $item . " LIKE '%" . $search_value . "%' ";
                } else {
                    $like .= " OR " . $item . " LIKE '%" . $search_value . "%' ";
                    // $this->db->or_like($item, $search_value);
                }
            }
            $like .= ") ";

            $this->db->where($like, null, false);
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
    function get_bb_review_order_list($length, $start, $search_value, $order,$only_object = NULL,$where = NULL) {
        $this->_get_bb__review_order_list_query($search_value, $order,$where);
        if ($length != -1) {
            $this->db->limit($length, $start);
        }
        $query = $this->db->get();
        if($only_object){
            return $query;
        }
        else{
            return $query->result();
        }
    }

    /**
     * @desc This is used to get order data as requested
     * @param String $search_value
     * @param String $order
     * @param Int $status_flag
     * @return Number of rows
     */
    function count_filtered_review_order($search_value, $order,$where = NULL) {
        $this->_get_bb__review_order_list_query($search_value, $order);
        if($where){
           $this->db->where($where);
        }
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
        $this->db->where('current_status', _247AROUND_BB_IN_PROCESS);
        return $this->db->count_all_results();
    }

    /**
     * @desc Used to get the  buyback image link
     * @param $where array
     * @param $select array
     * @param $is_distinct default false
     * @return array
     */
    function get_bb_order_images($where, $select, $is_distinct = False) {
        if ($is_distinct) {
            $this->db->distinct();
        }
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get("bb_order_image_mapping");

        return $query->result_array();
    }

    function get_bb_order_history($order_id) {
        $this->db->select('bb_state_change.*,name as cp_name,public_name as partner_name');
        $this->db->where('bb_state_change.order_id', $order_id);
        $this->db->join('service_centres', 'service_centres.id = bb_state_change.service_center_id', 'left');
        $this->db->join('partners', 'partners.id = bb_state_change.partner_id', 'left');
        $this->db->from('bb_state_change');
        $this->db->order_by('bb_state_change.id');
        $query = $this->db->get();
        $data = $query->result_array();

        foreach ($data as $key => $value) {
            if (!is_null($value['partner_id'])) {
                // If Partner Id is 247001
                if ($value['partner_id'] == _247AROUND) {
                    $sql = "SELECT full_name FROM employee WHERE "
                            . " employee.id = '" . $value['agent_id'] . "'";

                    $query1 = $this->db->query($sql);
                    $data1 = $query1->result_array();

                    $data[$key]['agent_name'] = isset($data1[0]['full_name']) ? $data1[0]['full_name'] : '';
                } else {
                    // For Partner
                    $this->db->select('full_name,public_name');
                    $this->db->from('partner_login,partners');
                    $this->db->where('partner_login.id', $value['agent_id']);
                    $this->db->where('partners.id', $value['partner_id']);
                    $query1 = $this->db->get();
                    $data1 = $query1->result_array();
                    $data[$key]['agent_name'] = isset($data1[0]['full_name']) ? $data1[0]['full_name'] : '';
                }
            } else if (!is_null($value['service_center_id'])) {
                // For Service center
                $this->db->select("CONCAT('Agent Id: ',service_centers_login.id ) As full_name , CONCAT('SF Id: ',service_centres.id ) As source");
                $this->db->from('service_centers_login');
                $this->db->where('service_centers_login.id', $value['agent_id']);
                $this->db->join('service_centres', 'service_centres.id = service_centers_login.service_center_id');
                $query1 = $this->db->get();
                $data1 = $query1->result_array();
                $data[$key]['agent_name'] = isset($data1[0]['full_name']) ? $data1[0]['full_name'] : '';
            }
        }

        return $data;
    }

    function get_bb_order_appliance_details($where, $select) {
        $this->db->select($select, false);
        $this->db->from('bb_unit_details as bb_unit ');
        $this->db->join('services as s', 'bb_unit.service_id = s.id');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result_array();
    }

    function download_bb_shop_address_data() {
        $sql = "SELECT contact_person,contact_email,shop_address_line1,shop_address_line2,shop_address_city,"
                . "shop_address_state,shop_address_pincode,"
                . "concat(primary_contact_number, ',', COALESCE(alternate_conatct_number, '')) as phone_number, tin_number from bb_shop_address"
                . " WHERE bb_shop_address.active = 1";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * @desc This function is used to get any data from bb_charges table
     * @param void()
     * @return void()
     */
    function get_bb_price_data($select, $where, $is_distinct = false, $isjoin = false) {

        if ($is_distinct) {
            $this->db->distinct();
        }

        $this->db->select($select);
        $this->db->from('bb_charges');
        $this->db->where('partner_id', '247024');

        if (isset($where['cp_id']) && $where['cp_id'] != NULL) {
            $this->db->where('cp_id', $where['cp_id']);
        }
        if (isset($where['service_id']) && $where['service_id'] != NULL) {
            $this->db->where('service_id', $where['service_id']);
        }
        if (isset($where['physical_condition']) && $where['physical_condition'] != NULL) {
            $this->db->where('physical_condition', $where['physical_condition']);
        }
        if (isset($where['working_condition']) && $where['working_condition'] != NULL) {
            $this->db->where('working_condition', $where['working_condition']);
        }

        if ($isjoin) {
            $this->db->join('services as s', 'bb_charges.service_id = s.id');
        }


        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * @desc This function is used to dump all buyback order data into the table bb_delivery_order_status_report
     * @param void()
     * @return string
     */
    function insert_bb_sheet_data($data) {
        log_message("info", print_r($data,true));
        $this->db->insert_batch('bb_delivery_order_status_report', $data);
        return $this->db->insert_id();
    }
    
    /**
     * @desc: This function is used to get buyback completed orders data 
     * based on month
     * @param string $sf_id
     * @return array $acknowledge_data
     */
    function get_bb_acknowledge_data_by_month($sf_id = null){
        $where = "";
        if(!empty($sf_id)){
            $where = "AND assigned_cp_id = $sf_id";
        }
        $acknowledge_data_sql  = "SELECT DATE_FORMAT(acknowledge_date, '%b') AS month,DATE_FORMAT(acknowledge_date, '%Y') AS year, COUNT(*) as count
                                FROM bb_order_details
                                WHERE current_status = 'Completed'
                                AND acknowledge_date IS NOT NULL AND acknowledge_date >= (NOW() - INTERVAL 13 MONTH) $where
                                GROUP BY DATE_FORMAT(acknowledge_date, '%m-%Y') 
                                ORDER BY YEAR(acknowledge_date),MONTH(acknowledge_date)";
        $acknowledge_data = $this->db->query($acknowledge_data_sql)->result_array();
        
        return $acknowledge_data;
    }
    
     /**
     * @desc: This function is used to get buyback summary report
     * @param $select, $where, $join
     * @return array $result
     */
    function get_bb_detail($select='*', $where=array(), $join=array()){
        $this->db->select($select);
        if(!empty($where)){
           $this->db->where($where); 
        }
        if(!empty($join)){
            foreach ($join as $table => $condition) {
                $this->db->join($table, $condition);
            }
        }
        $this->db->from("bb_unit_details");
        $query = $this->db->get();
        return $query->result_array();
    }
    
//    function test(){
//        $sql = "SELECT * FROM `bb_charges` WHERE `service_id` IN (28,37)";
//        $query = $this->db->query($sql);
//        return $query->result_array();
//    }
//    
//    function test_update($data, $where){
//        $this->db->where($where);
//        $this->db->update("bb_charges", $data);
//    }
//    
//    function insert_buyback_order($data){
//        $this->db->insert('bb_charges', $data);
//        return $this->db->insert_id();
//    }
    
    
     function get_bb_amazon_balace_details($select,$where = array()) {
        $this->db->select($select);
        $this->db->from('bb_unit_details as bb_unit ');
        if(!empty($where)){
            $this->db->where($where);
        }
        $query = $this->db->get();
        return $query->result_array();
    }
    function get_orders_without_invoices($select,$is_groupBy = NULL,$where = NULL,$join = NULL,$cp_invoiced = true){
        $this->db->from('bb_unit_details');
        $this->db->join('bb_order_details as bb', 'bb_unit_details.partner_order_id = bb.partner_order_id');
        if($join){
            $this->db->join('bb_cp_order_action', 'bb_cp_order_action.partner_order_id = bb.partner_order_id');
        }
        $this->db->select($select);
        if($cp_invoiced){
            $this->db->where('cp_invoice_id IS NULL');
            $this->db->where('!(bb_unit_details.order_status = "Delivered" AND bb.acknowledge_date IS NOT NULL)');
        }
        else{
            $this->db->where('cp_invoice_id IS NOT NULL');
            $this->db->where('partner_discount > 0');
            $this->db->where('service_id = '._247AROUND_TV_SERVICE_ID);
        }
        $this->db->where('partner_reimbursement_invoice IS NULL');
        $this->db->where('date(delivery_date) < DATE_FORMAT( CURRENT_DATE - INTERVAL 1 MONTH, "%Y/%m/01")');
        $this->db->where('date(delivery_date) > DATE_FORMAT( CURRENT_DATE - INTERVAL 12 MONTH, "%Y/%m/31")');
        $this->db->where('bb_cp_order_action.current_status != "InProcess"');
        $this->db->where("!((bb.current_status = 'Cancelled' AND bb.internal_status = 'Cancelled') OR (bb.current_status = 'Rejected' AND bb.internal_status = 'Rejected'))");
        if($where){
            $this->db->where($where);
        }
        if($is_groupBy){
            $this->db->group_by('bb_cp_order_action.current_status');
        }
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function get_not_delivered_orders_list() {
        
        $sql = "SELECT 
            bb_order_details.partner_order_id,
            bb_order_details.partner_tracking_id,
            services.services,
            bb_unit_details.category,
            bb_order_details.city,
            bb_order_details.order_date,
            bb_order_details.delivery_date,
            bb_cp_order_action.current_status,
            (bb_unit_details.partner_basic_charge + bb_unit_details.partner_tax_charge) as exchange_price
        FROM
            bb_order_details
            INNER JOIN bb_unit_details ON (bb_order_details.partner_order_id = bb_unit_details.partner_order_id)
            INNER JOIN services ON (bb_unit_details.service_id = services.id)
            INNER JOIN bb_cp_order_action ON (bb_order_details.partner_order_id = bb_cp_order_action.partner_order_id)
        WHERE
            bb_order_details.partner_id = ".AMAZON_SELLER_ID." 
            AND bb_cp_order_action.current_status = '"._247AROUND_BB_NOT_DELIVERED."'	
            AND date(bb_cp_order_action.acknowledge_date) = (CURDATE() - INTERVAL 1 DAY)";
        
        return $this->db->query($sql)->result_array();
    }
}
