<?php

class Service_centers_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
	  parent::__Construct();

	    $this->db_location = $this->load->database('default1', TRUE, TRUE);
	    $this->db = $this->load->database('default', TRUE, TRUE);
    }
    
    /**
     * @desc: check service center login and return pending booking
     * @param: Array(username, password)
     * @return : Array(Pending booking)  
     */
    function service_center_login($data){
       $this->db->select('service_center_id');
       $this->db->where('user_name',$data['user_name']);
       $this->db->where('password',$data['password']);
       $this->db->where('active',1);
       $query = $this->db->get('service_centers_login');
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0]['service_center_id'];
         
      } else {

      	return false;
      }

    }
}