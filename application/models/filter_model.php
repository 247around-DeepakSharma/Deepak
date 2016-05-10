<?php
class Filter_model extends CI_Model{
  /**
  * @desc load both db
  */
  function __construct(){
  parent::__Construct();
  $this->db_location = $this->load->database('default1', TRUE,TRUE);
  $this->db = $this->load->database('default', TRUE,TRUE);
  }

  /**
   *  @desc : This function get service 
   *  param : void
   *  @return : array(service)
   */

   function getserviceforfilter(){
        $this->db->distinct();
        $this->db->select('id,services');
        $this->db->where('action',1);
        $query = $this->db->get('services');
        return $query->result_array();
      }
  /**
   *  @desc : This function get handyman
   *  param : void
   *  @return : array(handyman detail)
   */

   function gethandyman_name(){
      $this->db->distinct('name,address,vendors_area_of_operation');
      $this->db->select('name,address,vendors_area_of_operation');
      $this->db->where('action',1);
      $query = $this->db->get('handyman');
      return $query->result_array();
      }
 /**
   *  @desc : This function get telecaller
   *  param : void
   *  @return : array(handyman detail)
   */

      function getagent(){
        $this->db->distinct();
        $this->db->select('employee_id');
        $query = $this->db->get('employee');
        return $query->result_array();

      }

  /**
   *  @desc : This function for filter
   *  param : data to be filter
   *  @return : array(handyman)
   */

    function filter($data){
     
       $this->db->select('handyman.id,handyman.name,handyman.service_id,handyman.profile_photo,verify_by,phone,image_processing,approved,verified,Rating_by_Agent,address,experience,age,is_paid,service_on_call,handyman.action,services.services');
  
       if(isset($data['service_id']))
       $this->db->where_in('service_id',$data['service_id']);
       if(isset($data['Rating_by_Agent']))
       $this->db->where_in('Rating_by_Agent',$data['Rating_by_Agent']);
       if(isset($data['experience'])){
        $where='';
        $count =0;
        foreach ($data['experience'] as   $value) {
            $count +=1;
         // print_r($value);
          $where .= "experience >='".$value[0]."' AND experience <='".$value[1]."'";
          if(sizeof($data['experience'])>1 && sizeof($data['experience'])>$count){
            $where .=" OR ";
          }
        }
        $this->db->where($where);
        
       }
       //$this->db->where_in('experience',$data['experience']);
       if(isset($data['Agent']))
       $this->db->where_in('Agent',$data['Agent']);
       if(isset($data['address']))
       $this->db->where_in('address',$data['address']);
       if(isset($data['address']))
       $this->db->where_in('address',$data['address']);
       if(isset($data['service_on_call']))
       $this->db->where_in('service_on_call',$data['service_on_call']);
       if(isset($data['action'])){
       $this->db->where_in('handyman.action',$data['action']);
       $this->db->where_in('approved',1);
       }
       if(isset($data['approved'])){
       $this->db->where_in('verified',1);
       $this->db->where_in('approved',0);
       }
       if(isset($data['verified']))
       $this->db->where_in('verified',$data['verified']);
       if(isset($data['verify_by']))
       $this->db->where_in('verify_by',$data['verify_by']);
       $this->db->from('handyman');
       $this->db->join('services','handyman.service_id = services.id');
       $query = $this->db->get();
 
       return $query->result_array(); 
       
  }


  

    

      //end of model
 }
