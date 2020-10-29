<?php
class User_Model extends CI_Model
{
	function saverecords($data)
	{
		$this->db->insert('indian_team',$data);
		return $this->db->insert_id();
	}
	function display_records()
	{
	    $query=$this->db->get('indian_team');
	    return $query->result();
	}
	function displayrecordsById($Team_name='')
	{
		
	      $query = $this->db->get_where('indian_team', array('Team_name' => $Team_name));
	       return $query->result();

	    
	}
	/*Update*/
	
	function update_records($data,$Team_name)
	{
		
		$this->db->where(array('Team_name' =>$Team_name));
        $this->db->update('indian_team',$data);
	}
	function delete_data($Team_name)//for delete a perticular row
	{
		$this->db->where(array('Team_Name' =>$Team_name));
        $this->db->delete('indian_team');

	}
}
?>

