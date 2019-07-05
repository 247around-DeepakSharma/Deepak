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
          $this->db->where('active','1');
          $query = $this->db->get('employee');
          echo $this->db->last_query();
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
      
        /**
        * @Desc: This function is used to get all employee groups
        * @params: void
        * @return: Array
        * 
        */
       function get_employee_groups(){
           $this->db->select('groups');
           $this->db->distinct();
           $query = $this->db->get('employee');
           return $query->result_array();
       }

    /* @desc : this function for create employee managerial mapping
     * @param : array(employee manager hierarchy)
     * @return : void
     */
    function insertManagerData($data){
        $query = "Insert into employee_hierarchy_mapping(employee_id,manager_id) values ";
        foreach($data as $value) {
            $query .=  " (".$value['id'].",".$value['manager'].") ,"; // ,".$value['level']."
        }
        $query = trim($query," ,");
        $result=$this->db->query($query);
    }
      /**
   * @desc : This funtion for delete employee managerial mapping
   * @param : $cond (where cond)
   * @return : void
   */

   function deleteManager($cond){
   	$query =  "DELETE FROM employee_hierarchy_mapping where ".$cond." ";
        $result=$this->db->query($query);
   }
   /**
   * @desc : This funtion for update employee managerial mapping
   * @param : data
   * @return : void
   */

   function updateManager($data){
   	$query = "";
        foreach($data as $value) {
            $query .=  "Update employee_hierarchy_mapping set manager_id=".$value['manager']." where employee_id=".$value['id']."; ";
        }
        $result=$this->db->query($query);
   }
   /**
   * @desc : This funtion get employee managerial mapping
   * @param : employee id
   * @return : array(employee managerial detail)
   */


   function getemployeeManagerfromid($where=array()){
        $this->db->where($where);
   	$query = $this->db->get('employee_hierarchy_mapping');
   	return $query->result_array();
   }

    /**
    * @Desc: This function is used to get roles
    * @params: void
    * @return: Array
    * 
    */
    function get_entity_role($select, $condition = array()){
        $this->db->select($select);
        $this->db->distinct();
        
        if(!empty($condition['where'])){
            $this->db->where($condition['where']);
        }

        if(!empty($condition['where_in'])){
            foreach ($condition['where_in'] as $key => $value) {
                $this->db->where_in($key, $value);
            }
        }
        
        if(!empty($condition['order_by'])){
            $this->db->order_by($condition['order_by']);
        }
        
        $this->db->from('entity_role');
        $query = $this->db->get();
        return $query->result_array();
    }
    /**
    * @desc : This function is used to get employee Manager Details based on employee ID
    * @param : select, where condition
    * @return : array
    */
    function getemployeeManagerDetails($select,$where=array()){
        $this->db->select($select);
        $this->db->from('employee_hierarchy_mapping');
        $this->db->join('employee', 'employee.id = employee_hierarchy_mapping.manager_id');
        $this->db->where('active',1);
        if(!empty($where)){
            $this->db->where($where);
        }
   	$query = $this->db->get();
        return $query->result_array();
   }
   /**
   * @desc : This funtion is used to get employee official email id
   * @param : employes id one or more comma separated
   * @return : official email id 
   */


   function getemployeeMailFromID($id){
   	$query =  "SELECT GROUP_CONCAT(official_email) official_email FROM (`employee`) WHERE `id` IN (".$id.") ";
        $result=$this->db->query($query)->result_array();
        return $result;
   }
   
   function get_state_wise_rm($state) {
       $state_code = $this->reusable_model->get_search_result_data('state_code', 'state_code', ['state' => trim($state)], NULL, NULL, NULL, NULL, NULL)[0]['state_code'];
       $sql = "SELECT agent_id FROM `employee_relation` WHERE find_in_set('{$state_code}', state_code)";
       return $this->db->query($sql)->result_array();
   }
}
