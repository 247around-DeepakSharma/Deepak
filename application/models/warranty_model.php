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

    function get_warranty_data($arrOrWhere) {
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
        
        $this->db->select($strSelect);
        $this->db->or_where($arrOrWhere);
        $this->db->from('warranty_plans');
        $this->db->join('warranty_plan_model_mapping', ' warranty_plans.plan_id = warranty_plan_model_mapping.plan_id', 'left');
        $this->db->join('appliance_model_details', 'warranty_plan_model_mapping.model_id = appliance_model_details.id', 'left');
        
        $this->db->group_by('appliance_model_details.id,appliance_model_details.model_number,warranty_plans.period_start, warranty_plans.period_end');
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
        $this->db->join('spare_parts_details', 'booking_details.booking_id = spare_parts_details.booking_id', 'Left');
        $this->db->join('booking_unit_details', 'booking_details.booking_id = booking_unit_details.booking_id', 'Left');
        $this->db->join('service_center_booking_action', 'booking_details.booking_id = service_center_booking_action.booking_id', 'Left');
        $this->db->where_in('booking_details.booking_id',$arrBookingIds);
        $this->db->group_by('booking_details.booking_id');
        $query = $this->db->get();
        return $query->result_array();
    }
}
