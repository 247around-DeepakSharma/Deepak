<?php
class Popularsearch_model extends CI_Model{
/**
* @desc load both db
*/
function __construct(){
parent::__Construct();
$this->db_location = $this->load->database('default1', TRUE,TRUE);
$this->db = $this->load->database('default', TRUE,TRUE);
}

   /** @description* This funtion for add popular search keyword
    *  @param : search keyword input
    *  @return : insert
    */

      function AddPopularSearchKeyword($insert){
         $this->db->insert('popularSearch',$insert);
         $id =  $this->db->insert_id();
         $client = new Elasticsearch\Client();
         $indexParams = $this->elasticsearch();
         $indexParams['id']     = $id;
         $indexParams['body']   = $insert;
         $sucess = $client->create($indexParams);
         return $id;
        }

        function elasticsearch(){
         $indexParams['index']  = "boloaaka";
         $indexParams['type']   = "popularSearch";
         return $indexParams;

        }

  /**
   * @desc : This funtion count total no of search keyword
   * @param : void
   * @return : total no search keyword
   */

      function total_countsearchkeyword(){
         return $this->db->count_all_results("popularSearch");
      }

   /** @description* This funtion get all search keyword
    *  @param : limit (between 10)
    *  @return :  array (search keyword) 
    */

      function get_popularsearchkeyword($limit, $start){
        $this->db->limit($limit, $start);
        $query = $this->db->get('popularSearch');
        if ($query->num_rows() > 0) {
          return $query->result_array();
        }
        return false;

      }
  /**
   * @desc : This funtion for delete search keyword and get name of search keyword
   * @param :id
   * @return : total name of search keyword
   */

      function DeleteSearchkeyword($id){
      	 $name  = $this->getsearchkeywordname($id);
      	 $this->db->where('id', $id);
         $this->db->delete('popularSearch');
         $client = new Elasticsearch\Client();
         $indexParams = $this->elasticsearch();
         $indexParams['id']     = $id;
         $sucess = $client->delete($indexParams);
         return $name[0]['searchkeyword'];
         
          }
      

  /**
   * @desc : This funtion for get name of delete search keyword
   * @param :search keyword id
   * @return : array(searchkeyword)
   */

      function getsearchkeywordname($id){
      	$this->db->select('searchkeyword');
        $this->db->where('id',$id);
        $query =  $this->db->get('popularSearch');
        return $query->result_array();
      }

  /**
   * @desc : This funtion for update search keyword 
   * @param :id and array input search keyword
   * @return : void
   */

      function UpdatePopularSearchKeyword($id,$update){
      	$this->db->where('id',$id);
      	$this->db->update('popularSearch',$update);
        $client = new Elasticsearch\Client();
        $indexParams = $this->elasticsearch();
        $indexParams['id']     = $id;
        $indexParams['body']['doc']   = $update;
        $sucess = $client->update($indexParams);
       // print_r($sucess);

      }
  /**
   * @desc : This funtion for get all information of given id
   * @param :search keyword id 
   * @return : array(search keyword)
   */

      function getsearchkeyword($id){
         $this->db->select('*');
         $this->db->where('id',$id);
         $query =  $this->db->get('popularSearch');
         return $query->result_array();
      }
}
