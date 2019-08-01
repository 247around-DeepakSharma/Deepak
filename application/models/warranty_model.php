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
    
    /**
     * @desc: This is used to get warranty data of a model
     * This function returns the maximum warranty period for a given plan.
     * @param Array $data
     * @return boolean
     */
    function check_warranty_for_bulk_data($data) {
        if (empty($data['partner']) || empty($data['service_id']) || empty($data['brand']) || empty($data['model']) || empty($data['purchase_date'])):
            echo "Insufficient data";
            return;
        endif;

        $strSelect =    'warranty_plans.plan_id,
                        warranty_plans.plan_name,
                        warranty_plans.plan_description,
                        warranty_plans.period_start,
                        warranty_plans.period_end,
                        warranty_plans.warranty_type,
                        warranty_plans.partner_id,
                        warranty_plans.inclusive_svc_charge,
                        warranty_plans.inclusive_gas_charge,
                        warranty_plans.inclusive_transport_charge,
                        max(warranty_plans.warranty_period) as warranty_period,
                        warranty_plans.warranty_grace_period,
                        warranty_plans.is_active,
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

        $this->db->group_by('warranty_plan_model_mapping.model_id');
        $this->db->order_by('warranty_plans.warranty_type,warranty_plans.period_start');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function check_warranty_by_booking_ids($arrBookings) {
        $strSelect = "booking_details.booking_id,booking_details.service_id,booking_details.partner_id,"
                . "booking_details.create_date,appliance_model_details.id as model_id,"
                . "ifnull(spare_parts_details.model_number, ifnull(booking_unit_details.sf_model_number,booking_unit_details.model_number)) as model_number,"
                . "ifnull(spare_parts_details.date_of_purchase, ifnull(booking_unit_details.sf_purchase_date,booking_unit_details.purchase_date)) as date_of_purchase,"
                . "warranty_plan_model_mapping.plan_id,"
                . "ifnull(MAX(warranty_plans.warranty_period), 12) as warranty_period,"
                . "CASE WHEN warranty_plans.warranty_type = ".EXTENDED_WARRANTY_STATUS." THEN 'EW' else 'IW' end as warranty_type";
                
        $this->db->select($strSelect);
        $this->db->from('booking_details');
        $this->db->join('spare_parts_details', 'booking_details.booking_id = spare_parts_details.booking_id','left');
        $this->db->join('booking_unit_details', 'booking_details.booking_id = booking_unit_details.booking_id','left');
        $this->db->join('appliance_model_details', 'ifnull(spare_parts_details.model_number, ifnull(booking_unit_details.sf_model_number,booking_unit_details.model_number)) = appliance_model_details.model_number');
        $this->db->join('warranty_plan_model_mapping', 'appliance_model_details.id = warranty_plan_model_mapping.model_id', 'Left');
        $this->db->join('warranty_plans', 'warranty_plan_model_mapping.plan_id = warranty_plans.plan_id and date(warranty_plans.period_start) <= ifnull(spare_parts_details.date_of_purchase, ifnull(booking_unit_details.sf_purchase_date,booking_unit_details.purchase_date)) and date(warranty_plans.period_end) >= ifnull(spare_parts_details.date_of_purchase, ifnull(booking_unit_details.sf_purchase_date,booking_unit_details.purchase_date)) and warranty_plans.is_active = 1', 'Left');
//        Extra join condition of Partner Id
//        $this->db->join('warranty_plans', 'warranty_plan_model_mapping.plan_id = warranty_plans.plan_id and date(warranty_plans.period_start) <= spare_parts_details.date_of_purchase and date(warranty_plans.period_end) >= spare_parts_details.date_of_purchase and warranty_plans.is_active = 1 and warranty_plans.partner_id = booking_details.partner_id');
        $this->db->where_in('booking_details.booking_id', $arrBookings);
        $this->db->group_by('booking_details.booking_id, warranty_plan_model_mapping.model_id');
        $query = $this->db->get();
        return $query->result_array();
    }

}
