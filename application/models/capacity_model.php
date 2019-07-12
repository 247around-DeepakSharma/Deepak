<?php

class Capacity_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }

    public function get_capacity() {
        if (!empty($this->input->get("search"))) {
            $this->db->like('private_key', $this->input->get("search"));
            $this->db->or_like('name', $this->input->get("search"));
        }
        $this->db->order_by('private_key');
        $query = $this->db->get("capacity");
        return $query->result();
    }

    function update_status($where, $data) {
        $data['last_updated_by'] = $this->session->userdata("employee_id");
        $this->db->where($where, FALSE);
        $this->db->update('capacity', $data);
        if ($this->db->affected_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function save_data($data) {
        if (empty($data['active'])) {
            $data['active'] = 0;
        }
        $data['private_key'] = strtoupper(preg_replace("/[^a-zA-Z0-9.-]/", "", $data['name']));     
        $data['last_updated_by'] = $this->session->userdata("employee_id");
        
        // CASE : UPDATE
        if (!empty($data['capacity_id'])) {
            $this->db->where('id', $data['capacity_id']);
            unset($data['Save'], $data['capacity_id']);
            $this->db->update('capacity', $data);
        } else {
        // CASE : CREATE
            unset($data['Save'], $data['capacity_id']);
            $this->db->insert('capacity', $data);
        }
    }
    
    function select_capacity($showAll = null) {
        $strWhere = 1;
        if(empty($showAll))
        {
            $strWhere = 'active = 1';
        }
        $query = $this->db->query("Select * from capacity where ".$strWhere." order by private_key");
        return $query->result();
    }

}
