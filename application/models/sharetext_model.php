<?php
class Sharetext_model extends CI_Model{
  /**
  * @desc load both db
  */
  function __construct(){
  parent::__Construct();
  $this->db_location = $this->load->database('default1', TRUE,TRUE);
  $this->db = $this->load->database('default', TRUE,TRUE);
  }

 /**
   * @desc : This funtion for get sharetext
   * @param : void
   * @return : share text
   */

  public function getsharetext(){
    $query = $this->db->get('sharetext');
    $result = $query->result_array();
    return $result[0]['sharetext'];
    
  }

 /**
   * @desc : This funtion for update sharetext
   * @param : share text
   * @return :void
   */

  public function updatesharetext($data){
  	$this->db->where('id',1);
  	$this->db->update('sharetext',$data);
  }
// Model End
}
