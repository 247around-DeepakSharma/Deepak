<?php
class Employee_model extends CI_Model{
  /**
  * @desc load both db
  */
  function __construct(){
  parent::__Construct();
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
      $this->db->where('active',1);
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
          $this->db->where('active','1');
          $query = $this->db->get('employee');
          return $query->result_array();
      }
      
      /**
       * @Desc: This function is used to get Holiday List
       * @params: Active
       * @return : Array
       * 
       */
      function get_holiday_list(){
          $this->db->select('*');
          $this->db->where('active',1);
          $this->db->order_by('event_date','asc');
          $query = $this->db->get('holiday_list');
          return $query->result_array();
      }
      
      /**
       * @Desc: This function is used to get employee for particular group
       * @params: String user_group
       * @return: Array
       * 
       */
      function get_employee_by_group($where){
          $this->db->select('*');
          $this->db->where($where);
          $this->db->order_by('full_name');
          $query = $this->db->get('employee');
          return $query->result_array();
      }
      
      /**
       * @Desc: This function is used to get admin and RM for CRON function mails
       * @params: void
       * @return: Array
       * 
       */
      function get_employee_for_cron_mail(){
          $this->db->select('*');
          $this->db->where('groups',_247AROUND_ADMIN);
          $this->db->or_where('groups',_247AROUND_RM);
          $query = $this->db->get('employee');
          return $query->result_array();
      }
      
      /**
       * @Desc: This function is used to get Employee details from Full Name
       * @params: Full Name
       * @return: Array
       * 
       */
      function get_employee_by_full_name($fullname){
          $this->db->where('full_name',$fullname);
          $query = $this->db->get('employee');
          return $query->result_array();
      }
      
      function get_employee_email_by_group($groups){
          $this->db->select('official_email');
          $this->db->where('groups',$groups);
          $query = $this->db->get('employee');
          return $query->result_array();
      }

}
