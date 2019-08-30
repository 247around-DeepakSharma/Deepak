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
  
  
  /* @desc : this function for create employee relation
   * @param : array(employee relation detail)
   * @return :  id
   */

  function insertEmployeeRelation($insert){
    $this->db->insert('employee_relation',$insert);
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
       * @Desc: This function is used to get RM's from employee table
       * @params: void
       * @return: Array
       * 
       */
      function get_rm_details_by_id($id){
          $this->db->select('*');
          $this->db->where('groups','regionalmanager');
          $this->db->where('active','1');
          $this->db->where('id',$id);
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
   
    /**
    * 
    * @param type blank
    * @return states
    */
   function get_states() {
        $sql = "SELECT * FROM state_code";
       return $this->db->query($sql)->result_array();
   }
   
   /**
    * @desc : This function is used to check existence of rm id in employee_relation
    * @param type $id
    * @return type
    */
   function chk_entry_in_employee_relation($id) {
       $data=$this->db->query("select * from `employee_relation` where agent_id =".$id)->result_array();
       if(count( $data) == 0 ){
            $emp_rel["agent_id"] = $id;
            $emp_rel["active"] = 1;
            $emp_rel["create_date"] = date('Y-m-d H:i:s');
           $this->insertEmployeeRelation($emp_rel);
       }
   }
   /**
    * @Desc: This function is used to get assigned states 
    * @params: void
    * @return: Array
    * 
    */
    function get_rm_mapped_state($rmid){
        $sql= "select `state_code`.`state` from state_code where `state_code`.state_code in (
            SELECT
  DISTINCT SUBSTRING_INDEX(SUBSTRING_INDEX(vals, ',', n.digit+1), ',', -1) val
FROM
  (select state_code as vals from employee_relation where agent_id=".$rmid.") tt1
  INNER JOIN
  (SELECT 0 digit UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3  UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10) n
  ON LENGTH(REPLACE(vals, ',' , '')) <= LENGTH(vals)-n.digit)";
        return $this->db->query($sql)->result_array();
 }
    
   /**
    * @desc : This function is used to update state mapping. New User added is RM and any ASM details are propagated to this RM 
    * @param type $id,$state
    * @return type
    */
   function update_new_rm_mapping($id) {
       $sql="update `employee_relation`
        left join 
        (SELECT `manager_id` as `id`,  GROUP_CONCAT(`individual_service_centres_id` SEPARATOR ',')  as `center_id`, GROUP_CONCAT(`state_code` SEPARATOR ',') as `state_id` FROM `employee_relation` 
        left JOIN `employee_hierarchy_mapping` on `employee_relation`.`agent_id`=`employee_hierarchy_mapping`.`employee_id`
        where `employee_hierarchy_mapping`.`manager_id` = ".$id.") as a on (a.id=employee_relation.agent_id)
        set `service_centres_id`=a.`center_id` , `state_code`=a.`state_id`
        where `agent_id`=a.id";
       return $this->db->query($sql);
   }
   
   
   /**
    * @desc : This procedure is to update its manager mapping if he is a regionalmanager 
    * @param type $id
    * @return type
    */
   function update_asm_manager_mapping($id) {
       $sql="update `employee_relation` "
               . "Left Join ( select e1.id,`employee_relation`.`individual_service_centres_id`, `employee_relation`.`state_code` "
               . "from `employee_hierarchy_mapping` join `employee` e1 on e1.`id` =`employee_hierarchy_mapping`.`manager_id` "
               . "join `employee_relation` on `employee_relation`.`agent_id`=`employee_hierarchy_mapping`.`employee_id` "
               . "where e1.`groups`='regionalmanager' and `employee_hierarchy_mapping`.`employee_id`=".$id.") a "
               . "on a.id=`employee_relation`.agent_id "
               . "set `employee_relation`.`state_code`= if((`employee_relation`.`state_code` is null or `employee_relation`.`state_code` =''),a.`state_code`,concat(`employee_relation`.`state_code`, concat(',',a.`state_code`) )), "
               . "`employee_relation`.`service_centres_id`=if((`employee_relation`.`service_centres_id` is null or `employee_relation`.`service_centres_id` =''),a.`individual_service_centres_id`,concat(`employee_relation`.`service_centres_id`,concat(',',a.`individual_service_centres_id`)) ) "
               . "where `employee_relation`.`agent_id`=a.id";
       print_r($sql);
       return $this->db->query($sql);
   }
   
   /**
    * @desc : This function is used to update rm/asm state mapping
    * @param type $id,$state
    * @return type
    */
   function update_rm_state_mapping($id,$state) {
       //$data=$this->db->query("SELECT GROUP_CONCAT(id SEPARATOR ',') as 'service_center' FROM `service_centres` WHERE `state`= '".$state."' GROUP BY NULL")->result_array();
       $query="UPDATE `employee_relation` SET "
                         ."   `service_centres_id`= if ((`service_centres_id` IS NULL OR  `service_centres_id` = ''), "
                        ."(SELECT GROUP_CONCAT(id ORDER BY id SEPARATOR ',')  FROM `service_centres` WHERE `state`= '".$state."' GROUP BY NULL),"
                        ."concat(`service_centres_id`,concat(',',(SELECT GROUP_CONCAT(`id` ORDER BY id  SEPARATOR ',')  FROM `service_centres` WHERE `state`= '".$state."' GROUP BY NULL)))),"
 
                        ."`individual_service_centres_id` = if((`individual_service_centres_id` IS NULL OR  `individual_service_centres_id` = ''), " 
                        ." (SELECT GROUP_CONCAT(id ORDER BY id  SEPARATOR ',')  FROM `service_centres` WHERE `state`= '".$state."' GROUP BY NULL), "
                        ."concat(`individual_service_centres_id`,concat( ',',(SELECT GROUP_CONCAT(id ORDER BY id  SEPARATOR ',')  FROM `service_centres` WHERE `state`= '".$state."' GROUP BY NULL)))),"

                        ."`state_code`= if( (`state_code` IS NULL OR  `state_code` = ''), (select `state_code` from `state_code` WHERE `state`='".$state."' LIMIT 1),"
                        ." concat(`state_code`,(select concat(',',`state_code`) from `state_code` WHERE `state`='".$state."' LIMIT 1)))"
                        ."   WHERE `agent_id`=".$id;
      // print_r($query);
       return $this->db->query($query);
   }
   
   /**
    * @desc : This function is used to remove all mapping of state in database
    * @param type $state
    * @return type
    */
   function remove_all_rm_state_map($state) {
        $sql_individual_service_centres_id = "UPDATE `employee_relation` as a "
                ."LEFT JOIN `state_code` ON FIND_IN_SET(`state_code`.`state_code` , a.`state_code`) "
                ."LEFT JOIN `service_centres` on (`state_code`.`state` = `service_centres`.`state`) "
                ."SET a.`individual_service_centres_id` = ( "
                ."select TRIM(BOTH ',' FROM REPLACE(CONCAT(',',b.`individual_service_centres_id`, ','), "
                ."CONCAT(\",\",GROUP_CONCAT(`service_centres`.`id` ORDER BY `service_centres`.`id` SEPARATOR ','),\",\"), ',')) from `employee_relation` b "
                ."LEFT JOIN `state_code` ON FIND_IN_SET(`state_code`.`state_code` ,b.`state_code`) "
                ."LEFT JOIN `service_centres` on (`state_code`.`state` = `service_centres`.`state`) "
                ."WHERE `state_code`.`state` = '".trim($state)."' and   b.`agent_id` = a.agent_id) " 
                ."WHERE `state_code`.`state` = '".trim($state)."'";
        
        $sql_service_centres_id = "UPDATE `employee_relation` as a "
                ."LEFT JOIN `state_code` ON FIND_IN_SET(`state_code`.`state_code` , a.`state_code`) "
                ."LEFT JOIN `service_centres` on (`state_code`.`state` = `service_centres`.`state`) "
                ."SET a.`service_centres_id` = ( "
                ."select TRIM(BOTH ',' FROM REPLACE(CONCAT(',',b.`service_centres_id`, ','), "
                ."CONCAT(\",\",GROUP_CONCAT(`service_centres`.`id` ORDER BY `service_centres`.`id` SEPARATOR ','),\",\"), ',')) from `employee_relation` b "
                ."LEFT JOIN `state_code` ON FIND_IN_SET(`state_code`.`state_code` ,b.`state_code`) "
                ."LEFT JOIN `service_centres` on (`state_code`.`state` = `service_centres`.`state`) "
                ."WHERE `state_code`.`state` = '".trim($state)."' and   b.`agent_id` = a.agent_id) "
                ."WHERE `state_code`.`state` = '".trim($state)."'";
        
        $sql_state_code = "UPDATE `employee_relation` 
                LEFT JOIN `state_code` ON FIND_IN_SET(`state_code`.`state_code` , `employee_relation`.`state_code`)
                SET `employee_relation`.`state_code` =TRIM(BOTH ',' FROM REPLACE(CONCAT(',', `employee_relation`.`state_code`, ','), CONCAT(',',`state_code`.`id`,','), ','))
                WHERE `state_code`.`state` = '".trim($state)."'";
       
        $res=$this->db->query($sql_individual_service_centres_id);
        $res=$this->db->query($sql_service_centres_id);
        $res= $this->db->query($sql_state_code);
        return '';
   }
   
   /**
    * 
    * @param type $state
    * @return type
    */
   function get_state_wise_rm($state) {
        $sql = "SELECT
                    employee.id,
                    employee.full_name
                FROM
                    employee_relation
                    LEFT JOIN employee ON (employee_relation.agent_id = employee.id)
                    LEFT JOIN state_code ON FIND_IN_SET(state_code.state_code , employee_relation.state_code)
                WHERE 
                    state_code.state = '".trim($state)."'";
       return $this->db->query($sql)->result_array();
   }
}
