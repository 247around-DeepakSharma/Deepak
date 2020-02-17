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
	public function show_accessories_list()
	{
		$this->db->select('accessories_product_description.*,services.services,hsn_code_details.hsn_code as text_hsn_code');
        $this->db->from('accessories_product_description');
        $this->db->join('services', 'accessories_product_description.appliance=services.id');
		$this->db->join('hsn_code_details', 'accessories_product_description.hsn_code=hsn_code_details.id');
		$where=array('accessories_product_description.status'=>1);
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result_array();
	}
   
}
