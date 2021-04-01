<?php

class Warranty_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
        $this->load->helper('custom_functions_helper');
    }

    /**
     * @desc: This is used to get warranty data of a model
     * @param Array $data
     * @return boolean
     */
    function check_warranty($data) {
        if (empty($data['partner']) || empty($data['service_id']) || empty($data['brand']) || empty($data['model']) || empty($data['purchase_date'])):
            echo "Insufficient data";
            return;
        endif;

        $strSelect = 'warranty_plans.*,
                        group_concat(distinct(state_code.state)) as states,
                        group_concat(distinct(inventory_parts_type.part_type)) as part_types';

        $arrWhere = [
            'warranty_plans.partner_id' => $data['partner'],
            'warranty_plan_model_mapping.service_id' => $data['service_id'],
            'warranty_plan_model_mapping.model_id' => $data['model'],
            'warranty_plans.period_start <= ' => date('Y-m-d', strtotime($data['purchase_date'])),
            'warranty_plans.period_end >= ' => date('Y-m-d', strtotime($data['purchase_date'])),
            'warranty_plans.is_active' => 1
        ];

        $this->db->select($strSelect);
        $this->db->from('warranty_plans');
        $this->db->join('warranty_plan_model_mapping', 'warranty_plan_model_mapping.plan_id = warranty_plans.plan_id and warranty_plan_model_mapping.is_active = 1', 'Left');
        $this->db->join('warranty_plan_state_mapping', 'warranty_plan_state_mapping.plan_id = warranty_plans.plan_id and warranty_plan_state_mapping.is_active = 1', 'Left');
        $this->db->join('state_code', 'warranty_plan_state_mapping.state_code = state_code.state_code', 'Left');
        $this->db->join('warranty_plan_part_type_mapping', 'warranty_plan_part_type_mapping.plan_id = warranty_plans.plan_id and warranty_plan_part_type_mapping.is_active', 'Left');
        $this->db->join('inventory_parts_type', 'warranty_plan_part_type_mapping.part_type_id = inventory_parts_type.id', 'Left');
        $this->db->where($arrWhere);

        $column_search = array('warranty_plans.plan_name', 'warranty_plans.period_start', 'warranty_plans.period_end', 'state_code.state');
        if (!empty($data['search']['value'])) {
            $like = "";
            foreach ($column_search as $key => $item) { // loop column 
                // if datatable send POST for search
                if ($key === 0) { // first loop
                    $like .= "( " . $item . " LIKE '%" . $data['search']['value'] . "%' ";
                } else {
                    $like .= " OR " . $item . " LIKE '%" . $data['search']['value'] . "%' ";
                }
            }
            $like .= ") ";

            $this->db->where($like, null, false);
        }

        $this->db->group_by('warranty_plans.plan_id');
        $this->db->order_by('warranty_plans.warranty_type,warranty_plans.period_start');
        $query = $this->db->get();

        return $query->result_array();
    }

    function check_warranty_by_booking_ids($arrBookings) {
        $this->db->_protect_identifiers = FALSE;
        $strBookings = '"'.implode('","', $arrBookings).'"';
        $strSelect =    'booking_details.booking_id,
                        booking_details.service_id,
                        booking_details.partner_id,
                        booking_details.create_date,
                        appliance_model_details.id AS model_id,
                        IFNULL(spare_parts_details.model_number,
                                        IFNULL(booking_unit_details.sf_model_number,
                                                        IFNULL(service_center_booking_action.model_number,
                                                                        booking_unit_details.model_number))) AS model_number,
                        IFNULL(spare_parts_details.date_of_purchase,
                                        IFNULL(booking_unit_details.sf_purchase_date,
                                                        IFNULL(service_center_booking_action.sf_purchase_date,
                                                                        booking_unit_details.purchase_date))) AS date_of_purchase,
                        warranty_plans.plan_id,
                        ifnull(warranty_plans.warranty_type, ".IN_WARRANTY_STATUS.") as warranty_type,
                        ifnull(warranty_plans.warranty_period, 12) as warranty_period,
                        (CASE WHEN ifnull(warranty_plans.warranty_type, ".IN_WARRANTY_STATUS.") = ".IN_WARRANTY_STATUS." THEN ifnull(warranty_plans.warranty_period, 12) ELSE 0 END) as in_warranty_period,
                        (CASE WHEN ifnull(warranty_plans.warranty_type, ".IN_WARRANTY_STATUS.") <> ".IN_WARRANTY_STATUS." THEN ifnull(warranty_plans.warranty_period, 12) ELSE 0 END) as extended_warranty_period';
        
        $arrWhere = [
            'booking_details.booking_id IN ' => "(".$strBookings.")"           
        ];
        
        $this->db->select($strSelect);
        $this->db->from('booking_details');
        $this->db->join('spare_parts_details', 'booking_details.booking_id = spare_parts_details.booking_id', 'Left');
        $this->db->join('booking_unit_details', 'booking_details.booking_id = booking_unit_details.booking_id', 'Left');
        $this->db->join('service_center_booking_action', 'booking_details.booking_id = service_center_booking_action.booking_id', 'Left');
        $this->db->join('appliance_model_details', 'IFNULL(spare_parts_details.model_number,
                                        IFNULL(booking_unit_details.sf_model_number,
                                                        IFNULL(service_center_booking_action.model_number,
                                                                        booking_unit_details.model_number))) = appliance_model_details.model_number');
        $this->db->join('warranty_plan_model_mapping', 'appliance_model_details.id = warranty_plan_model_mapping.model_id', 'Left');
        $this->db->join('warranty_plans', 'warranty_plan_model_mapping.plan_id = warranty_plans.plan_id
                                AND DATE(warranty_plans.period_start) <= IFNULL(spare_parts_details.date_of_purchase,
                                        IFNULL(booking_unit_details.sf_purchase_date,
                                                        IFNULL(service_center_booking_action.sf_purchase_date,
                                                                        booking_unit_details.purchase_date)))
                                AND DATE(warranty_plans.period_end) >= IFNULL(spare_parts_details.date_of_purchase,
                                        IFNULL(booking_unit_details.sf_purchase_date,
                                                        IFNULL(service_center_booking_action.sf_purchase_date,
                                                                        booking_unit_details.purchase_date)))
                                AND warranty_plans.is_active = 1
                                AND warranty_plans.partner_id = booking_details.partner_id', 'Left');
        $this->db->where($arrWhere);
        $this->db->group_by('booking_details.booking_id, warranty_plans.plan_id');
        $query = $this->db->get();        
        return $query->result_array();
    }

    function get_warranty_data($arrOrWhere, $data = array()) {
        $this->db->_protect_identifiers = FALSE;
        $strSelect = "IFNULL(appliance_model_details.id, concat('PRODUCT',warranty_plans.service_id)) AS model_id,
                    ifnull(appliance_model_details.model_number, concat('ALL',warranty_plans.service_id)) AS model_number,
                    warranty_plans.plan_id,
                    warranty_plans.period_start as plan_start_date,
                    warranty_plans.period_end as plan_end_date,
                    warranty_plans.plan_depends_on,
                    ifnull(warranty_plans.warranty_type, ".IN_WARRANTY_STATUS.") as warranty_type,
                    ifnull(warranty_plans.warranty_period, ".DEFAULT_IN_WARRANTY_PERIOD.") as warranty_period,
                    MAX(CASE WHEN ifnull(warranty_plans.warranty_type, ".IN_WARRANTY_STATUS.") = ".IN_WARRANTY_STATUS." THEN ifnull(warranty_plans.warranty_period, 12) ELSE 0 END) as in_warranty_period,
                    MAX(CASE WHEN ifnull(warranty_plans.warranty_type, ".IN_WARRANTY_STATUS.") <> ".IN_WARRANTY_STATUS." THEN ifnull(warranty_plans.warranty_period, 0) ELSE 0 END) as extended_warranty_period";
        if(!empty($data['select'])){
            $strSelect .= ",".$data['select'];
        }
        
        $this->db->select($strSelect);
        $this->db->or_where($arrOrWhere);
        $this->db->where(['warranty_plans.is_active' => 1]);
        $this->db->from('warranty_plans');
        $this->db->join('warranty_plan_model_mapping', ' warranty_plans.plan_id = warranty_plan_model_mapping.plan_id AND warranty_plan_model_mapping.is_active = 1', 'left');
        $this->db->join('appliance_model_details', 'warranty_plan_model_mapping.model_id = appliance_model_details.id', 'left');
        if(!empty($data['join'])){
            foreach ($data['join'] as $tableName=>$joinCondition){
                $this->db->join($tableName,$joinCondition);
            }
        }
        
        if(!empty($data['group_by'])){
            $this->db->group_by($data['group_by']);
        }
        else
        {
            $this->db->group_by('appliance_model_details.id,appliance_model_details.model_number,warranty_plans.period_start, warranty_plans.period_end');
        }
        
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_warranty_specific_data_of_bookings($arrBookingIds) {
        $this->db->_protect_identifiers = FALSE;
        $strSelect =    'booking_details.booking_id,
                        date(booking_details.create_date) as booking_create_date,
                        booking_details.partner_id,
                        booking_details.service_id,
                        booking_unit_details.appliance_brand,
                        IFNULL(spare_parts_details.model_number,
                                        IFNULL(booking_unit_details.sf_model_number,
                                                        IFNULL(service_center_booking_action.model_number,
                                                                        booking_unit_details.model_number))) AS model_number,
                        IFNULL(date(spare_parts_details.date_of_purchase),
                                        IFNULL(date(booking_unit_details.sf_purchase_date),
                                                        IFNULL(date(service_center_booking_action.sf_purchase_date),
                                                                        date(booking_unit_details.purchase_date)))) AS purchase_date';


        $this->db->select($strSelect);
        $this->db->from('booking_details');
        $this->db->join('spare_parts_details', 'booking_details.booking_id = spare_parts_details.booking_id AND spare_parts_details.status <> "'._247AROUND_CANCELLED.'"', 'Left');
        $this->db->join('booking_unit_details', 'booking_details.booking_id = booking_unit_details.booking_id AND booking_unit_details.booking_status <> "'._247AROUND_CANCELLED.'"', 'Left');
        $this->db->join('service_center_booking_action', 'booking_details.booking_id = service_center_booking_action.booking_id AND service_center_booking_action.current_status <> "'._247AROUND_CANCELLED.'"', 'Left');
        $this->db->where_in('booking_details.booking_id',$arrBookingIds);
        $this->db->group_by('booking_details.booking_id');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     This function returns mpodels mapped to plans
     * @author Prity Sharma
     * @date 31-10-2019
     * @return array 
     */
    function getPlanWiseModels($where, $select, $order_by, $where_in = array(), $join_array = array(), $join_type_array = array(), $result_array = true, $group_by = "",$like="",$start='',$length=-1){
        $this->db->distinct();
        $this->db->select($select);        
        if(!empty($join_array)){
            foreach ($join_array as $tableName => $joinCondition){
                if(!empty($join_type_array) && array_key_exists($tableName, $join_type_array)){
                    $this->db->join($tableName,$joinCondition,$join_type_array[$tableName]);
                }
                else{
                    $this->db->join($tableName,$joinCondition);
                }
            }
        }
       if($length!=-1){
            $this->db->limit($length, $start);
       }
       if(!empty($like)){
            $this->db->where($like, null, false);
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
        if(!empty($group_by)){
            $this->db->group_by($group_by);
        }
        
        $this->db->order_by($order_by);
        $query = $this->db->get('warranty_plans');
        if(!$result_array)
        {
            return $query->result();
        }
    	return $query->result_array();
    }  
    
    function selectPlans($format = null) {
        $query = $this->db->query("Select plan_id,plan_name,is_active from warranty_plans where is_active = 1 and plan_name <> '' order by plan_name");
        return $query->result();
    }
    
    function selectModels($format = null) {
        $query = $this->db->query("Select id,model_number,service_id,active from appliance_model_details where active = 1 and model_number <> '' order by model_number");
        return $query->result();
    }
    
    function map_model_to_plan($arrModels)
    {
        log_message ('info', __METHOD__);
        $this->db->insert_ignore('warranty_plan_model_mapping', $arrModels);
        return $this->db->insert_id();
    }
    
    function remove_model_from_plan($mapping_id)
    {
        log_message ('info', __METHOD__);
        $this->db->where('id', $mapping_id);
        $this->db->update('warranty_plan_model_mapping', ['is_active' => 0]);
    }
    
    function activate_model_to_plan($mapping_id)
    {
        log_message ('info', __METHOD__);
        $this->db->where('id', $mapping_id);
        $this->db->update('warranty_plan_model_mapping', ['is_active' => 1]);
    }
    
    function activate_plan($plan_id)
    {
        log_message ('info', __METHOD__);
        $this->db->where('plan_id', $plan_id);
        $this->db->update('warranty_plans', ['is_active' => 1]);
    }
    
    function deactivate_plan($plan_id)
    {
        log_message ('info', __METHOD__);
        $this->db->where('plan_id', $plan_id);
        $this->db->update('warranty_plans', ['is_active' => 0]);
    }
    
    function get_partner_list()
    {
        $this->db->select('id, public_name as name');
        $this->db->order_by('name');
        $query = $this->db->get("partners");
        $results = $query->result_array();
        return $results;
    }
    
    function get_partner_service_list($partner_id)
    {
        $params = array($partner_id);
        $query = "SELECT pad.service_id, s.services FROM partner_appliance_details as pad, services as s where pad.service_id = s.id and partner_id=? group by pad.partner_id, pad.service_id order by services";
//        $response = $this->db->query($query, $params);
//        $results = $response->result_array();
        $results = execute_paramaterised_query($query, $params);
        return $results;
    }
    
    
    function get_state_list()
    {
        $this->db->select('state_code as id, state as name');
        $this->db->order_by('name');
        $query = $this->db->get("state_code");
        $results = $query->result_array();
        return $results;
    }
    
    
     /**
     *  @desc : This function is used to get warranty plan data
     *  @param : $post string
     *  @param : $select string
     *  @return: Array()
     */
    function get_warranty_plan_list($post, $select = "",$is_array = false) {
        $this->_get_warranty_plan_list_list($post, $select);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        
        $query = $this->db->get();
     //   echo $this->db->last_query();
        log_message('info', __METHOD__. " ".$this->db->last_query());
        if($is_array){
            return $query->result_array();
        }else{
            return $query->result();
        }
    }
    
    
     /**
     * @Desc: This function is used to get data from the warranty_plans table
     * @params: $post array
     * @params: $select string
     * @return: void
     * 
     */
    function _get_warranty_plan_list_list($post,$select){
        
        if (empty($select)) {
            $select = '*';
        }
        $this->db->distinct();
        $this->db->select($select,FALSE);
        $this->db->from('warranty_plans as wp');
        $this->db->join('services as s', 'wp.service_id = s.id');
        $this->db->join('partners as p', 'wp.partner_id = p.id ');

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
            $this->db->order_by('wp.plan_name','ASC');
        }
    }
    
    
     /**
     *  @desc : This function is used to get total warranty_plans data
     *  @param : $post string
     *  @return: Array()
     */
    public function count_all_warranty_plan_list($post) {
        $this->_get_warranty_plan_list_list($post, 'count(distinct(wp.plan_id)) as numrows');
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
    }
    
      /**
     *  @desc : This function is used to get total filtered warranty_plans data
     *  @param : $post string
     *  @return: Array()
     */
    function count_filtered_warranty_plan_list($post){
        $this->_get_warranty_plan_list_list($post, 'count(distinct(wp.plan_id)) as numrows');
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
    }
    
    
    function get_warranty_plan_details($plan_id)
    {
        $this->db->select('wp.plan_id, wp.plan_name, wp.plan_description, wp.period_start, wp.period_end, wp.warranty_period, wp.is_active, s.id as service_id, p.id as partner_id, wp.warranty_type, wp.inclusive_svc_charge, wp.inclusive_gas_charge, wp.inclusive_transport_charge, wp.plan_depends_on, wp.warranty_grace_period');
        $this->db->from('warranty_plans as wp');
        $this->db->join('services as s', 'wp.service_id = s.id');
        $this->db->join('partners as p', 'wp.partner_id = p.id ');
        $this->db->where('plan_id', $plan_id);
        $query = $this->db->get();
        $num_rows = $query->num_rows();
        if($num_rows == 1)
        {
            $results = $query->result_array();
            return $results;
        }
        else 
        {
            return false;
        }
        
    }
    
    
    function get_warranty_plan_state_list($plan_id)
    {
        $params = array($plan_id);
        $query = "select distinct(state_code) from warranty_plan_state_mapping where plan_id = ?";
        $results = execute_paramaterised_query($query, $params);
        return $results;
    }
    
    
}
