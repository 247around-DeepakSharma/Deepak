<?php

class Accessories_model extends CI_Model {


	public function fetch_accessories_data($table,$where='')
	{
		if(!empty($where))
		{
			$this->db->where($where);
		}
		$result=$this->db->get($table);
		return $result->result_array();
	}

	public function insert_product_data($table,$data)
	{
		$this->db->insert($table,$data);
	}
	public function show_accessories_list($where=array())
	{
		$this->db->select('accessories_product_description.*,services.services');
        $this->db->from('accessories_product_description');
        $this->db->join('services', 'accessories_product_description.appliance=services.id');
		$where['accessories_product_description.status']=1;
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result_array();
	}
   
}
