<?php
class Review_model extends CI_Model{
/**
* @desc load both db
*/
function __construct(){
parent::__Construct();

$this->db_location = $this->load->database('default1', TRUE,TRUE);
$this->db = $this->load->database('default', TRUE,TRUE);
}

/**
 * @desc : This funtion for insert review
 * @param : array(review)
 * @return : review id
 */
function insertReview($insert){
  $this->db->insert('handyman_review',$insert);
  return $this->db->insert_id();
}
/**
 * @desc : This funtion for get review  info
 * @param : void
 * @return : array(review,handyman)
 */


function getReview(){

   $this->db->select('handyman_review.id,handyman_review.status,handyman_review.handyman_id,handyman_review.user_id,handyman_review.behaviour,handyman_review.expertise,handyman_review.review,handyman.name,handyman.service_id,handyman.profile_photo,services.services,users.user_email,users.name as user_name');
   $this->db->from('handyman_review');
   $this->db->join('handyman','handyman.id = handyman_review.handyman_id');
   $this->db->join('services','services.id = handyman.service_id'); 
   $this->db->join('users','users.user_id = handyman_review.user_id');
   $query = $this->db->get();
   return $query->result_array();

}
/**
 * @desc : This funtion for update review
 * @param : review id and review
 * @return : void
 */

function toDoinactive($id,$review){
  $this->db->where('id', $id);
  $this->db->update('handyman_review',$review);

}

  /**
   * @desc : This funtion for get review message
   * @param : void
   * @return : review message
   */


  public function getreviewmessage(){
    $query = $this->db->get('reviewmessage');
    $result = $query->result_array();
    return $result[0]['reviewmessage'];
    
  }


  /**
   * @desc : This funtion for update review message
   * @param : review message
   * @return : void
   */
  public function updatereviewmessage($data){
  	$this->db->where('id',1);
  	$this->db->update('reviewmessage',$data);
  }

  /**
   * @desc : This funtion for delete review 
   * @param : review id
   * @return : void
   */

  function delete($id){
     $this->db->where('id', $id);
     $this->db->delete('handyman_review');
  }

//end of model
}
