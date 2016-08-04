<?php
class Report_message_model extends CI_Model{
  /**
  * @desc load both db
  */
  function __construct(){
  parent::__Construct();
  
  $this->db = $this->load->database('default', TRUE,TRUE);
  }

  /**
   * @desc : This funtion for get share text
   * @param : void
   * @return : share text
   */


  public function getreport_message(){
  	$query = $this->db->get('report_message');
    $result = $query->result_array();
    return $result[0]['report_message'];
    
  }


  /**
   * @desc : This funtion for update share text
   * @param : share text
   * @return : void
   */
  public function updatereport_message($data){
  	$this->db->where('id',1);
  	$this->db->update('report_message',$data);
  }
// Model End
}
