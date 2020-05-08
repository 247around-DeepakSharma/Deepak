<?php

class Employeelogin extends CI_Model{
  
 /**
  * @desc load both db
  */
  function __construct(){
  parent::__Construct();

  
  $this->db = $this->load->database('default', TRUE,TRUE);
  }
     /** @description* Post request to get authentication admin
      *  @param : employee id and password
      *  @retun: array(result)
      */
      function login($employee_id,$employee_password) {
        $sql = "SELECT * FROM employee WHERE employee_id = ".$this->db->escape($employee_id)." AND employee_password = ".$this->db->escape($employee_password)." AND active = 1"; 
        $data = $this->db->query($sql)->result_array(); 
        /**
         * If warehouse person logged in then get wh name.
         */
        if(!empty($data[0]) && !empty($data[0]['warehouse_id'])) {
            $sql = "SELECT district, state FROM service_centres WHERE id = ".$data[0]['warehouse_id']; 
            $sc_data = $this->db->query($sql)->result_array(); 
            $data[0]['wh_name'] = _247AROUND_EMPLOYEE_STRING." ".$sc_data[0]['district'] ." (". $sc_data[0]['state'].")";
        }
       
        return $data;
      }
      
      /**
       * @desc: This function is used to authenticate 247access user
       * @params: String username, password
       * @return: Array
       */
      function _247access_login($username, $password){
          $this->db->select('id');
          $this->db->where('employee_id', $username);
          $this->db->where('employee_password', md5($password));
          $query = $this->db->get('employee');
          if($query->num_rows() > 0){
              $result = $query->result_array();
              return $result[0]['id'];
          }else{
              return FALSE;
          }
          
      }
      
      /**
       * @Desc: This function is used to get group name of particular employee
       *@params: employee_id(name)
       * @return: Mix
       */
      function get_employee_group_name($id){
          $this->db->select('groups');
          $this->db->where('id', $id);
          $query = $this->db->get('employee');
          if($query->num_rows() > 0){
              $result =  $query->result_array();
              return $result[0]['groups'];
          }else{
              return FALSE;
          }
      }
      
// end of model
}
