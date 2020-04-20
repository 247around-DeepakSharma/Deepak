<?php

class Assets_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }

    function insert_new_assets($details) {
        $this->db->insert('assets_list', $details);
        return $this->db->insert_id();
    }
    function assigned_assets($insert){
        $this->db->insert('assets_assigned',$insert);
        return $this->db->insert_id();   
    }

    public function get_assets($data = array()) {

        $this->db->select('assets_list.*,employee.full_name');
        if(!empty($data)){
            $this->db->where($data);
        }
        $this->db->from('assets_list');
        $this->db->join('employee', 'assets_list.employee_id = employee.id', 'left');
        $query = $this->db->get();
        return $query->result_array();
    }

    function update_assets($id, $data) {
        $this->db->where("id", $id);
        $this->db->update("assets_list", $data);
        return true;
    }
    public function get_assigned_history($data=array()){
        $this->db->select('assets_assigned.*,employee.full_name, e.full_name as agent_name');
        if(!empty($data)){
            $this->db->where($data);
        }
        $this->db->from('assets_assigned');
        $this->db->join('employee', 'assets_assigned.employee_id = employee.id', 'left');
        $this->db->join('employee as e', 'assets_assigned.agent_id = e.id', 'left');
        $query = $this->db->get();
        return $query->result_array();
    }

}

?>