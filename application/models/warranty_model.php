<?php

class Warranty_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
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
                        group_concat(state_code.state) as states,
                        group_concat(inventory_parts_type.part_type) as part_types';

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

}
