<?php

class Signup_message_model extends CI_Model{
  
/**
* @desc load both db
*/
function __construct(){
parent::__Construct();


$this->db = $this->load->database('default', TRUE,TRUE);
}

 /**
   * @desc : This funtion for get signup Message
   * @param : void
   * @return : signup message
   */
  public function getsignup_message(){
  	$query = $this->db->get('signup_message');
    $result = $query->result_array();
    return $result[0]['signup_message'];
    
  }

  /**
   * @desc : This funtion for update signup Message
   * @param : signup message text
   * @return : void
   */
  public function updatesignup_message($data){
  	$this->db->where('id',1);
  	$this->db->update('signup_message',$data);
  }

//end of model

}
