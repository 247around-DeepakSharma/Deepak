<?php

class Handyman_model extends CI_Model{
  
/**
* @desc load both db
*/
function __construct(){
parent::__Construct();

$this->db_location = $this->load->database('default1', TRUE,TRUE);
$this->db = $this->load->database('default', TRUE,TRUE);
}
     /** @description* Post request to get authentication admin
      *  @param : void
      *  @return :array(result)
      */
      function login($email,$password) {
        $sql = "SELECT * FROM admin WHERE email = '$email' AND password = '$password'"; 
        $data = $this->db->query($sql);     
        return $data->result_array();
      }

      

     /** @description* get Even service from database
      *  @param :void
      *  @return : array (service) 
      */
      function GetEvenServices(){
         $sql = $this->db->select('*');
         $this->db->where('id %2=', 0);
         $query=$this->db->get("services");
        return $query->result_array();
      }
     /** @description* get all service from database
      *  @param: void
      *  @return : array (service) 
      */

      function GetAllServices(){
         $sql = $this->db->select('*');
         $query=$this->db->get("services");
         return $query->result_array();

      }

     /** @description* This funtion get all handyman information
      *  @param :void
      *  @return : (array of handyman info).
      */
      function getAllhandymanInfo(){
        $sql = $this->db->select('*');
        $this->db->where('action', '1');
        $query = $this->db->get('handyman');
        return $query->result_array();
      }
     
  
     
     /** @description* This funtion get handyman id to update handyman detail of this id
      *  @param : handyman id and array to update handyman detail
      *  @return : update handyman
      */
      function UpdateHandyman($handyman_id,$updateData ){


        $this->db->where('id',$handyman_id);
        $this->db->update('handyman',$updateData);
        if(isset($updateData['location'])){
           $location  = json_decode($updateData['location'], true);
           $lat  = $location['lattitude'];
           $long = $location['longitude'];
           $updateData['location'] = $lat.",".$long;
         }
        $client = new Elasticsearch\Client();
        $indexParams['index']  = "boloaaka";
        $indexParams['type']   = "handyman";
        $indexParams['id']     = $handyman_id;
        $indexParams['body']['doc']   = $updateData;
        $sucess = $client->update($indexParams);
        $getname =  $this->getnamehandyman($handyman_id);

        return $getname[0]['name'];  
      }


  /* @desc : this function get handyman id name service address
   * @param : handyman id
   * @return : array handyman information
   */

  function gethandyman($handymanid){
    $this->db->select('*');
   // $this->db->where('action', '1');
    $this->db->where('id', $handymanid);
    $query = $this->db->get('handyman');
    return $query->result_array();

  }

  
 /** @description* Post request to insert data
  *  @param : array(handyman)
  *  @return :handyman id
  */
  function insertData($insert){
   
     $this->db->insert('handyman',$insert);
     $id =  $this->db->insert_id();

    for ($i=0; $i <count($insert['location']) ; $i++) { 
           $location  = json_decode($insert['location'], true);
           $lat  = $location['lattitude'];
           $long = $location['longitude'];
           $insert['location'] = $lat.",".$long;
    
    }

     $client = new Elasticsearch\Client();
     $indexParams = $this->elasticsearch();
     $indexParams['id']     = $id;
     $indexParams['body']   = $insert;
     $sucess = $client->create($indexParams);
     return $id;
    
  
  }

 /** @description*elastic search configure
  *  @param : void
  *  @return : configure
  */

   function elasticsearch(){
   
     $indexParams['index']  = "boloaaka";
     $indexParams['type']   = "handyman";
     return $indexParams;

   }

 /** @description : get handyman id 
  *  @param : 
  *  @return : configure
  */

  function getcurrenthandyman_id($insert){
    $this->db->select('id');
    $query = $this->db->get_where('handyman',$insert);
    return  $query->result_array();
  }
  
  
  /**
   * @desc : This funtion for download image
   * @param : void
   * @return : id and profile photo 
   */


   
    function downloadimage(){
      $this->db->select('id,profile_photo');
      $query = $this->db->get('handyman');
      return  $query->result_array();


    }
    function uploadurl($name,$id){
      $this->db->where('id',$id);
      $this->db->update('handyman',$name); 

    }

  /**
   * @desc : This funtion for resize image
   * @param : void
   * @return : service image
   */
    function resize(){
      $this->db->select('profile_photo');
      $query = $this->db->get('handyman');
      return  $query->result_array();

  }
  /**
   * @desc : This funtion count total no of handyman
   * @param : void
   * @return : total no handyman
   */

   public function total_count() {
       $this->db->select('*');
       $this->db->from('handyman');
       $this->db->join('services','services.id = handyman.service_id');
       $query = $this->db->get();
       $result = $query->result_array();
        return count($result);
     
       //return $this->db->count_all_results("handyman");
    }

  /**
   * @desc : This funtion get handyman detail
   * @param : limit (between 10)
   * @return : if handyman exist return handyman other wise false
   */

    

   public function get_handyman($limit,$start){
      $this->db->limit($limit, $start);
       $this->db->select('handyman.id,handyman.name,handyman.profile_photo,phone,service_id,verify_by,image_processing,Rating_by_Agent,address,verified,approved,experience,age,is_paid,service_on_call,handyman.action,services.services');
       $this->db->from('handyman');
       $this->db->join('services','services.id = handyman.service_id');
       $query = $this->db->get();
       $result = $query->result_array();
        if ($query->num_rows() > 0) {
        return $result;
         
     }
      return false;
      
   }
    /*public function get_handyman($limit, $start) {
      $this->db->limit($limit, $start);
      $query = $this->db->get('handyman');
      $i = 0;
      $result = $query->result_array();
      foreach ($result as  $value) {
       $servive = $value['service_id'];
       $this->db->select('services');
       $this->db->where('id',$servive);
       $getservice   = $this->db->get('services');
       $servicename  = $getservice->result_array();
       if($servicename)
       $result[$i]['service'] = $servicename[0]['services'];
       $i = $i+1;
      }
      if ($query->num_rows() > 0) {
          return $result;
     }
      return false;
   }*/

  
  

   /** @description* This funtion get handyman name from id
    *  @param : handyman id
    *  @return : array(name)
    */

      function getnamehandyman($id){
        $this->db->select('name');
        $this->db->where('id',$id);
        $query =  $this->db->get('handyman');
        return $query->result_array();

      }

   /** @description* This funtion get id and profile photo of handyman
    *  @param : handyman id
    *  @return : array(profile photo)
    */

      function  getexcelimage($id){
        $this->db->select('id,profile_photo');
        $query = $this->db->get_where('handyman',$id);
        return $query->result_array();
      }
   /** @description* This funtion count inactive handyman
    *  @param : void
    *  @return : array(handyman)
    */

      function total_count_inactive(){
        $this->db->where('approved',0);
        $this->db->where('handyman.verified',1);
        return $this->db->count_all_results("handyman");
      }
   /** @description* This funtion get handyman
    *  @param : limit (between 10)
    *  @return : array(handyman)
    */

      function get_handyman_approve($limit,$start){
       $this->db->limit($limit, $start);
       $this->db->select('handyman.id,handyman.name,handyman.profile_photo,phone,service_id,verified,verify_by,image_processing,Rating_by_Agent,address,verified,approved,experience,age,is_paid,service_on_call,handyman.action,services.services');
       $this->db->where('handyman.approved',0);
       $this->db->where('handyman.verified',1);
       $this->db->from('handyman');
       $this->db->join('services','handyman.service_id = services.id');
       $query = $this->db->get();
       $result = $query->result_array();
        if ($query->num_rows() > 0) {
        return  $result;
     }
      return false;

      }

   /** @description* This funtion for delete handyman
    *  @param : id 
    *  @return : array(handyman)
    */

      function delete($id){
        $this->db->where('id', $id);
        $this->db->delete('handyman');
        $client = new Elasticsearch\Client();
        $indexParams = $this->elasticsearch(); 
        $indexParams['id'] = $id;
        $retDelete = $client->delete($indexParams);
      }

   /** @description* This funtion get unverified handyman
    *  @param : void
    *  @return : array(unverified handyman)
    */

      function unverifiedhandyman(){
        $this->db->select('handyman.id,handyman.name,handyman.profile_photo,phone,address,verify_by,image_processing,verified,Rating_by_Agent,approved,experience,age,is_paid,service_on_call,handyman.action,services.services');
        $this->db->from('handyman');
        $this->db->where('verified',0);
        $this->db->join('services','services.id = handyman.service_id');
        $query = $this->db->get();
        $result = $query->result_array();
        return  $result;
     

      }

   /** @description* This funtion to download handyman table 
    *  @param : void
    *  @return : csv handyman format
    */

      function download_handyman(){
        $query = $this->db->query("SELECT * FROM handyman");
        return $this->dbutil->csv_from_result($query);

      }

   /** @description* This funtion for get handyman from apps
    *  @param : void
    *  @return : array (handyman)
    */

      function viewFromApps(){
        $this->db->select('user_handyman.id,user_handyman.handyman_id,handyman.id,handyman.name,verify_by,handyman.profile_photo,image_processing,phone,address,verified,Rating_by_Agent,approved,experience,age,is_paid,service_on_call,handyman.action,services.services');
        $this->db->from('user_handyman');
        $this->db->join('handyman','handyman.id = user_handyman.handyman_id');
        $this->db->join('services','services.id = handyman.service_id ');
        $query = $this->db->get();
        $result = $query->result_array();
        if($query->num_rows()>0){
          return $result;
        } 
        return false;
      }

   /** @description* This funtion for check duplicate  handyman on the basis of name , phone, serviceid
    *  @param : handyman name,phone,service,id
    *  @return : array (handyman)
    */


      function handymanExist($name,$serviceid,$phone){
        $sql = "SELECT id,name,phone,service_id,profile_photo FROM handyman WHERE name = '$name' AND phone = '$phone' AND service_id ='$serviceid'"; 
        $data = $this->db->query($sql);     
        return $query = $data->result_array();
      }


   /** @description* This funtion for get all verified handyman
    *  @param : void
    *  @return : void
    */

     function getallverifiedhandyman(){
        $this->db->select('handyman.id,handyman.name,handyman.profile_photo,phone,service_id,verified,verify_by,image_processing,Rating_by_Agent,address,verified,approved,experience,age,is_paid,service_on_call,handyman.action,services.services');
       $this->db->where('handyman.approved',0);
       $this->db->where('handyman.verified',1);
       $this->db->from('handyman');
       $this->db->join('services','handyman.service_id = services.id');
       $query = $this->db->get();
       $result = $query->result_array();
        if ($query->num_rows() > 0) {
        return  $result;
     }
      return false;

    }

  /** @description* This funtion for change password
    *  @param : void
    *  @return : void
    */
    function reset_password($reset){
      $this->db->where('id',1);
      $this->db->update('admin',$reset);
    }

   /** @description* This funtion check new and old password equal or not
    *  @param : void
    *  @return : void
    */

    function checkoldpassword($newpassword){
      $this->db->select('email');
      $this->db->where('password',$newpassword);
      $query = $this->db->get('admin');
      return $query->result_array();
    }


   

     

      
     
     

}
       
