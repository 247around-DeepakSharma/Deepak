<?php

class Cp_model extends CI_Model {

    var $column_order = array('public_name', 'name', 'contact_person', 'shop_address_city'); //set column field database for datatable orderable
    var $column_search = array('public_name', 'name', 'contact_person',
        'shop_address_city', 'primary_contact_number', 'alternate_conatct_number', 'shop_address_line1'); //set column field database for datatable searchable 
    var $order = array('name,bb_shop_address.shop_address_city ' => 'asc'); // default order 

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

}
