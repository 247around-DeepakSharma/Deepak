<?php
class Service_model extends CI_Model {
/**
* @desc load both db
*/
function __construct(){
parent::__Construct();

$this->db_location = $this->load->database('default1', TRUE,TRUE);
$this->db = $this->load->database('default', TRUE,TRUE);
}


   /** @description* add service
    *  @param : service 
    *  @return : array (service) 
    */

    function addservice($service){
      $this->db->insert('services',$service);
      $query = $this->db->insert_id();
      $priority['priority'] = $query;
      $this->db->where('id',$query);
      $this->db->update('services',$priority);
      $client = new Elasticsearch\Client();
      $indexParams = $this->elasticsearch();
      $indexParams['id']     = $query;
      $indexParams['body']   = $service;
      $sucess = $client->create($indexParams);


    }

 /** @description*elastic search configure
  *  @param : void
  *  @return : configure
  */


    function elasticsearch(){
      
      $indexParams['index']  = "boloaaka";
      $indexParams['type']   = "services";
      return $indexParams;

    }

  /**
   * @desc : This funtion count total no of service
   * @param : void
   * @return : total no service
   */

   public function total_service(){
    return $this->db->count_all_results("services");
   }



  /**
   * @desc : This funtion get services
   * @param : limit (between 10)
   * @return : if service exist return service other wise false
   */
    public function get_service($limit, $start) {
      $this->db->limit($limit, $start);
      $this->db->order_by('priority asc');
      $query = $this->db->get('services');
      if ($query->num_rows() > 0) {
        return $query->result_array();
      }
      return false;
   }

  /**
   * @desc : This funtion update service
   * @param : service id andr array(data)
   * @return : void
   */

    function updateService($serviceid,$update){
      $this->db->where('id',$serviceid);
      $this->db->update('services',$update);
      $this->updateelasticsearch($serviceid,$update);
    
    }

  /**
   * @desc : This funtion for get service
   * @param : service id
   * @return : array(service)
   */

    function getserviceid($service){
      $this->db->select('*');
      $this->db->where('id', $service);
      $query = $this->db->get('services');
      return  $query->result_array();
    }




  /**
   * @desc : This funtion for update elasticsearch
   * @param : service id ,data
   * @return : sucess
   */
      function updateelasticsearch($id,$data){

      $client = new Elasticsearch\Client();
      $indexParams['index']  = "boloaaka";
      $indexParams['type']   = "services";
      $indexParams['id']     = $id;
      $indexParams['body']['doc']   = $data;
      $sucess = $client->update($indexParams);
      return $sucess;
      }

  /**
   * @desc : This funtion for update priority 
   * @param : array(id,priority)
   * @return : void
   */


    function servicedrag($id,$priority){
      for ($i=0; $i <count($priority) ; $i++) { 
        $this->db->where($id[$i]);
        $this->db->update('services',$priority[$i]);
        $client = new Elasticsearch\Client();
        $indexParams['index']  = "boloaaka";
        $indexParams['type']   = "services";
        $indexParams['id']     = $id[$i]['id'];
        $indexParams['body']['doc']   = $priority[$i];
        $sucess = $client->update($indexParams);
      }
       
      }


   /** @description* This funtion for delete service
    *  @param : id 
    *  @return : array(services)
    */

      function delete($id){
        $this->db->where('id', $id);
        $this->db->delete('services');
        $client = new Elasticsearch\Client();
        $indexParams = $this->elasticsearch(); 
        $indexParams['id'] = $id;
        $retDelete = $client->delete($indexParams);
      }

   /** @description* This funtion for get handyman name
    *  @param : void
    *  @return : array(handyman name)
    */

      function getHandymanName(){
        $this->db->select('id,name');
        $query = $this->db->get('handyman');
        return  $query->result_array();
      }

   /** @description* This funtion for insert data
    *  @param : array(handyman id and data)
    *  @return : void
    */

      function insertpricedata($data){
        $this->db->insert('price',$data);
        $query = $this->db->insert_id();
      }

     

}
