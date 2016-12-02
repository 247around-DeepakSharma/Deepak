<?php
class Employee_model extends CI_Model{
  /**
  * @desc load both db
  */
  function __construct(){
  parent::__Construct();
  
  $this->db = $this->load->database('default', TRUE,TRUE);
  }

  /* @desc : this function for create employee
   * @param : array(employee detail)
   * @return : employee id
   */

  function insertData($insert){
    $this->db->insert('employee',$insert);
    return   $this->db->insert_id();

  }
  /* @desc : this function for count total employee
   * @param : void
   * @return : no. of employee
   */

  function total_employee(){
  return $this->db->count_all_results("employee");
  }

  /**
   * @desc : This funtion get employee
   * @param : limit (between 10)
   * @return : if employee exist return employee other wise false
   */
    public function get_employee($limit = '', $start = '') {
      if($limit != '' && $start != ''){
      $this->db->limit($limit, $start);
      }else{
          $this->db->select('*');
      }
      
      $query = $this->db->get('employee');
      if ($query->num_rows() > 0) {
        return $query->result_array();
      }
      return false;
   }
  /**
   * @desc : This funtion for update
   * @param : employe id and data
   * @return : void
   */

   function update($id,$data){
   	$this->db->where('id',$id);
   	$this->db->update('employee',$data);

   }
  /**
   * @desc : This funtion get employee
   * @param : employe id
   * @return : array(employe detail)
   */


   function getemployeefromid($id){
   	$this->db->where('id',$id);
   	$query = $this->db->get('employee');
   	return $query->result_array();
   }

  /**
   * @desc : This funtion add vrification list
   * @param :  user id and user 
   * @return : void
   */

   function Add_verificationlist($userid,$insert){
    $this->db->where('user_id',$userid);
    $this->db->update('users',$insert);

   }

  /**
   * @desc : This funtion get approve handyman list last14 days
   * @param : day
   * @return :array(approve handyman)
   */


   function last14daysapproved($date){
    $sql ="SELECT * from `handyman` where `approve_date` >= DATE_SUB(CURDATE(), INTERVAL $date DAY) ";
    $data = $this->db->query($sql);
    $result = $data->result_array();
    return  $result;
   }

 /**
   * @desc : This funtion get verify handyman list last14 days
   * @param : day
   * @return :array(verify handyman)
   */


   function last14daysverify($date){
    $sql ="SELECT * from `handyman` where `verify_date` >= DATE_SUB(CURDATE(), INTERVAL $date DAY) ";
    $data = $this->db->query($sql);
    $result = $data->result_array();
    return  $result;

   }

  /**
   * @desc : This funtion get approve handyman list of particular id
   * @param : employee id ,day
   * @return :array(approvehandyman)
   */


   function approvehandymanlist($employee_id,$date){
   
    $sql ="SELECT * from `handyman` where  `approve_by` = '$employee_id' and `approve_date` >= DATE_SUB(CURDATE(), INTERVAL $date DAY)  ";
    $data = $this->db->query($sql);
    $result = $data->result_array();
    return $result;
   }


  /**
   * @desc : This funtion get verify handyman list of particular id
   * @param : employee id ,day
   * @return :array(verifyhandyman)
   */

  function verifylist($employee_id,$date){
     $sql ="SELECT * from handyman  JOIN services on services.id = handyman.service_id  where  verify_by = '$employee_id' and verify_date >= DATE_SUB(CURDATE(), INTERVAL $date DAY)";
     $query = $this->db->query($sql);
    if ($query->num_rows() > 0) {
        return   $query->result_array();
   
      }
      return false;
   }
  /**
   * @desc : This funtion  delete handyman when error occured
   * @param :  handyman id
   * @return :void
   */

    function delete($id){
        $this->db->where('id',$id);
        $this->db->delete('handyman');
        $client = new Elasticsearch\Client();
        $indexParams['index']  = "boloaaka";
        $indexParams['type']   = "handyman";
        $indexParams['id'] = $id;
        $retDelete = $client->delete($indexParams);
    }

  /**
   * @desc : This funtion  delete employee
   * @param :  handyman id
   * @return :void
   */

    function deleteemployee($id){
        $this->db->where('id',$id);
        $this->db->delete('employee');
    }

    /**
     * @desc: This function is used to add login details of the logged employee
     * params: Array
     * return: Int
     */
    function add_login_logout_details($data){
        $this->db->insert('login_logout_details', $data);
        return $this->db->insert_id();
    }
    
      /**
       * @Desc: This function is used to get RM's from employee table
       * @params: void
       * @return: Array
       * 
       */
      function get_rm_details(){
          $this->db->select('*');
          $this->db->where('groups','regionalmanager');
          $query = $this->db->get('employee');
          return $query->result_array();
      }

}
