<?php

class Accessories_model extends CI_Model {

    /**
     * @Desc: This function is to used to calculate tax based on hsn code
     * @params: void
     * @return: NULL
     * @author Ghanshyam
     * @date : 17-02-2020
     */
    public function fetch_table_data($table, $where = '') {
        if (!empty($where)) {
            $this->db->where($where);
        }
        $result = $this->db->get($table);
        return $result->result_array();
    }

    /**
     * @Desc: This function is to used to insert data in product table
     * @params: void
     * @return: NULL
     * @author Ghanshyam
     * @date : 17-02-2020
     */
    public function insert_product_data($table, $data) {
        $this->db->insert($table, $data);
    }

    /**
     * @Desc: This function is to used to show accessories list
     * @params: void
     * @return: NULL
     * @author Ghanshyam
     * @date : 17-02-2020
     */
    public function show_accessories_list($where = array(), $whereNotIn = '') {
        $this->db->select('accessories_product_description.*,services.services');
        $this->db->from('accessories_product_description');
        $this->db->join('services', 'accessories_product_description.service_id=services.id');
        if (!empty($where)) {
            $this->db->where($where);
        }
        if (!empty($whereNotIn)) {
            $this->db->where_not_in('accessories_product_description.id',$whereNotIn);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * @Desc: This function is to used to update accessories
     * @params: void
     * @return: NULL
     * @author Ghanshyam
     * @date : 17-02-2020
     */
    public function update_accessories_data($columnUpdate, $where) {
        if (is_array($where) && !empty($where) > 0 && !empty($columnUpdate) > 0) {
            $this->db->set($columnUpdate);
            $this->db->where($where);
            $this->db->update('accessories_product_description');
            return true;
        } else {
            return false;
        }
    }

}
