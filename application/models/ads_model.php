<?php

class Ads_model extends CI_Model{
  
/**
* @desc load both db
*/
function __construct(){
parent::__Construct();

$this->db = $this->load->database('default', TRUE,TRUE);
}

 /** @description : this function for add ads
  *  @param :array(ads,url,image name)
  *  @return : void
  */

   function addads($insert){
	$this->db->insert('advertise',$insert);
	$query = $this->db->insert_id();
   }

 /** @description : this function get ads
  *  @param : void
  *  @return : :array(ads,url,image name)
  */

   function getads(){
	$query = $this->db->get('advertise');
        return  $query->result_array();
   }

//end model
}
