<?php
class WorldCricket extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('User_Model');
	}

	public function display_data1()//for dispaly
	{
		$result['data']=$this->User_Model->display_records();
		return $this->load->view('display_data',$result);
	}

	public function show_teamname_forupdate($Team_name='')
	{ 
		if(!empty($Team_name))
		{
            $result['data']=$this->User_Model->displayrecordsById($Team_name);
            $this->load->view('update_records',$result);
        }
	 }
	 public function for_update($Team_name='')
	 {   
	 	if(!empty($Team_name))
		{
    	  if($this->input->post('update'))
		   {
			  $data=array
			  (
		        'Team_Captain'=>$this->input->post('captain'),
		        'Team_Country'=>$this->input->post('country'),
		        'Team_Established'=>$this->input->post('date')
		  	  );

		        $this->User_Model->update_records($data,$Team_name);
		    	$this-> display_data1();
	   
		   }
	    }
	}
    //for delete a data
	public function delete_row($Team_name='') 
	{
		$this->User_Model->delete_data($Team_name);
		$this-> display_data1();
	}

	/**
	This function is used to insert data in <table> name
	@param 
	@return
	@author Deepak
	@date 
	*/
	public function show() 
	{
		$this->load->view('world_cricket');
	}

	public function Insert_team()
	{
		if($this->input->post('save'))
		{
			$data = array
			( 
			  'Team_Name'=>$this->input->post('T_name'),
			  'Team_Captain'=>$this->input->post('Captain_Name'),
			  'Team_Country'=>$this->input->post('Country_Name'),
			  'Team_Established'=>$this->input->post('Established_Date')
		    );

			$user=$this->User_Model->saverecords($data);
				if($user > 0)
					echo "Record Not Saved Successfully";
				else
				{
					$result['data']=$this->User_Model->display_records();
		            $this->load->view('submit_view',$result);
				}
				
		}
	}
 }

?>
