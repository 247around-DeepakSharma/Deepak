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
        if(!empty($id))
        {
            $this->db->where('id',$id);
            $this->db->delete('handyman');
            $client = new Elasticsearch\Client();
            $indexParams['index']  = "boloaaka";
            $indexParams['type']   = "handyman";
            $indexParams['id'] = $id;
            $retDelete = $client->delete($indexParams);
        }
    }

  /**
   * @desc : This funtion  delete employee
   * @param :  handyman id
   * @return :void
   */

    function deleteemployee($id){
        if(!empty($id)){
            $this->db->where('id',$id);
            $this->db->delete('employee');
        }
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
      function get_rm_details($arr_groups = [_247AROUND_RM,_247AROUND_ASM],$rm_id=''){
          $this->db->select('employee.*, zones.zone, zones.id as zone_id');
          $this->db->join('rm_zone_mapping', 'employee.id = rm_zone_mapping.rm_id', 'left');
          $this->db->join('zones', 'rm_zone_mapping.zone_id = zones.id', 'left');
          $this->db->where_in('employee.groups', $arr_groups);
          $this->db->where('employee.active','1');
          if(!empty($rm_id)){
              $this->db->where('employee.id',$rm_id);
          }
          $query = $this->db->get('employee');          
          return $query->result_array();
      }
      
    /**
       * @Desc: This function is used to get RM's name mapped with region from employee table and rm region mapping table
       * @params: void
       * @return: Array
       * 
    */
      function get_rm_region($region=null){
        $this->db->select('employee.full_name, zones.zone');
        $this->db->join('employee', 'rm_zone_mapping.rm_id = employee.id','left');
        $this->db->join('zones', 'rm_zone_mapping.zone_id = zones.id','left');
        if(!empty($region)){
            $this->db->where_in('zones.zone',$region);            
        }
        $query = $this->db->get('rm_zone_mapping');
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
          $this->db->where_in('groups',[_247AROUND_RM,_247AROUND_ASM]);
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
          $this->db->where('groups IN ("'._247AROUND_ADMIN.'","'._247AROUND_RM.'","'._247AROUND_ASM.'")',NULL);
          $this->db->where('active','1');
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
          if(is_array($groups))
          {
              $this->db->where_in('groups',$groups);
          }
          else
          {
              $this->db->where('groups',$groups);
          }
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
       if(!empty($cond)){
   	        $query =  "DELETE FROM employee_hierarchy_mapping where ".$cond." ";
            $result=$this->db->query($query);
        }
   }
   /**
   * @desc : This funtion for update employee managerial mapping
   * @param : data
   * @return : void
   */

   function updateManager($data){
        $query = "";
        foreach($data as $value) {
             $select_query = $this->db->select('manager_id');
             $this->db->from('employee_hierarchy_mapping');
             $this->db->where(['employee_id' => $value['id'], 'manager_id' => $value['manager']]);
             $query_select = $this->db->get();
             $result = $query_select->result();
             if(empty($result)){
             $query .= "Update employee_hierarchy_mapping SET manager_id=".$value['manager']." WHERE employee_id=".$value['id'];
             }    
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
   * @desc : This funtion  checks employee is Manager
   * @param : employee id
   * @return : array(employee managerial detail)
   */


  function isRManager($empId){
    $this->db->where('manager_id',$empId);
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
    * @Desc: This function is used to get assigned states 
    * @params: void
    * @return: Array
    * 
    */
    function get_rm_mapped_state($rmid){
        $sql= "select
              distinct state_code.state from state_code            
              JOIN agent_state_mapping ON (agent_state_mapping.state_code = state_code.state_code)
            WHERE
              agent_state_mapping.agent_id = '".$rmid."'";
        return $this->db->query($sql)->result_array();
    }
    
   /**
    * @desc : This function is used to update state mapping. New User added is RM and any ASM details are propagated to this RM 
    * @param type $id,$state
    * @return type
    */
   function update_new_rm_mapping($id) {
   }
   
   /**
    * 
    * @param type $state
    * @return type
    */
   function get_state_wise_rm($state,$arr_groups = [_247AROUND_RM,_247AROUND_ASM],$rm_id='') {
       $str_groups = implode("','",$arr_groups);
       $sql = "SELECT
                    distinct(employee.id),
                    employee.full_name
                FROM
                    agent_state_mapping
                    LEFT JOIN employee ON (agent_state_mapping.agent_id = employee.id)";
       if(!empty($rm_id)){
        $sql      .="LEFT JOIN employee_hierarchy_mapping ON (employee.id = employee_hierarchy_mapping.employee_id)";
       }
        $sql      .="LEFT JOIN state_code ON (state_code.state_code = agent_state_mapping.state_code)
                WHERE 
                    state_code.state = '".trim($state)."'
                    AND employee.groups IN ('".$str_groups."')";
        if(!empty($rm_id)){
         $sql      .=" and employee_hierarchy_mapping.manager_id=$rm_id";
        }
       return $this->db->query($sql)->result_array();
   }
   
    /**
     * @Desc: This function is used to get all regions (North,South,East,West)
     * @params: void
     * @return: Array
     * @author Prity Sharma
     * @date : 22-01-2020
    */
    function get_regions(){
        $this->db->select('zones.zone as region,rm_id');
        $this->db->distinct();
        $this->db->join('zones', 'rm_zone_mapping.zone_id = zones.id');
        $query = $this->db->get('rm_zone_mapping');
        return $query->result_array();
    }
    
    /**
     * @Desc: This function maps a region with its respective RM (North,South,East,West)
     * @params: void
     * @return: NULL
     * @author Prity Sharma
     * @date : 27-01-2020
    */
    function map_region_to_rm($region, $rm_id){
        $this->db->set("rm_id",$rm_id);
        $this->db->where('zone', $region);
        $this->db->update('rm_zone_mapping JOIN zones ON rm_zone_mapping.zone_id = zones.id');
    }
    
    function insertData_agent_state_mapping($insert)
    {
            $this->db->insert('agent_state_mapping',$insert);
            return   $this->db->insert_id();
    }
    
    function get_state_of_rm_asm($state,$group,$agent)
    {
        if(!empty($state) && !empty($group) && !empty($agent))
        {
                $this->db->select('agent_state_mapping.agent_id,state_code.state');
                $this->db->from('agent_state_mapping');
                $this->db->join('state_code', 'agent_state_mapping.state_code=state_code.state_code');
                $this->db->join('employee', 'agent_state_mapping.agent_id=employee.id');
                $this->db->where_in('state_code.state', $state);
                $this->db->where('employee.groups', $group);
                $this->db->where_not_in('agent_state_mapping.agent_id', $agent);
                $query = $this->db->get();
                return $query->result_array();	
        }
    }
    
    function delete_agent_state_mapping($agentID,$state)
    {
        if(!empty($agentID) && !empty($state))
        {
                $sql="delete a from agent_state_mapping a inner join state_code s on a.state_code=s.state_code where s.state='".$state."' and a.agent_id=".$agentID."";
                $this->db->query($sql);
                return true;
        }
        else
        {
                return false;
        }
    }
    
    function insert_agent_state_mapping($agentID,$state,$created_by)
    {
        if(!empty($agentID) && !empty($state) && !empty($created_by))
        {
                $sql="insert into agent_state_mapping (agent_id,state_code,created_by) select '$agentID',state_code,'created_by' from state_code where state='$state'";
                $this->db->query($sql);
        }
        else
        {
                return false;
        }
    }
    
    function get_asm_from_rm($state,$agent)
    {
        if(!empty($state) && !empty($agent))
        {
        $this->db->select('agent_state_mapping.agent_id,state_code.state');
        $this->db->from('agent_state_mapping');
        $this->db->join('state_code', 'agent_state_mapping.state_code=state_code.state_code');
        $this->db->join('employee_hierarchy_mapping', 'agent_state_mapping.agent_id=employee_hierarchy_mapping.employee_id');
        $this->db->where_in('state_code.state', $state);
        $this->db->where('employee_hierarchy_mapping.manager_id', $agent);
        $query = $this->db->get();
        return $query->result_array();
        }
    }
    /**
     * @Desc: This function is get district of all RM / ASM except current user
     * @params: $district List, $group (areasalesmanager/regionalmanager), $currentagent
     * @return: array
     * @ Ghanshyam Ji Gupta
     */
    function get_district_of_rm_asm($district, $group, $agent) {
        if (!empty($group) && !empty($agent)) {
            $this->db->select('agent_state_mapping.agent_id,district_state_mapping.id,district_state_mapping.district');
            $this->db->from('agent_state_mapping');
            $this->db->join('district_state_mapping', 'agent_state_mapping.district_id=district_state_mapping.id');
            $this->db->join('employee', 'agent_state_mapping.agent_id=employee.id');
            if (!empty($district)) {
                $this->db->where_in('district_state_mapping.id', $district);
            }
            $this->db->where('employee.groups', $group);
            $this->db->where_not_in('agent_state_mapping.agent_id', $agent);
            $query = $this->db->get();
            //echo $this->db->last_query();
            return $query->result_array();
        }
    }
    /**
     * @Desc: This function is get mapped district of Current User
     * @params: $agentID
     * @return: array
     * @ Ghanshyam Ji Gupta
     */
    function get_rm_mapped_district($rmid) {
        if (!empty($rmid)) {
            $sql = "select
               district_state_mapping.id from district_state_mapping
               JOIN agent_state_mapping ON (agent_state_mapping.district_id = district_state_mapping.id)
               WHERE
               agent_state_mapping.agent_id = '" . $rmid . "'";
            return $this->db->query($sql)->result_array();
        }
    }
    /**
     * @Desc: This function is used to delete record from table / Un-Mapping of states
     * @params: $agentID
     * @return: boolean
     * @ Ghanshyam Ji Gupta
     */
    function delete_agent_district_mapping($agentID, $district) {
        if (!empty($agentID) && !empty($district)) {
            $sql = "delete  from agent_state_mapping where agent_id=$agentID and district_id=$district";
            $this->db->query($sql);
            return true;
        } else {
            return false;
        }
    }
    /**
     * @Desc: This function is used to insert record in table for mapping
     * @params: $agentID, $district
     * @return: boolean
     * @ Ghanshyam Ji Gupta
     */
    function insert_agent_district_mapping($agentID, $district, $created_by) {
        if (!empty($agentID) && !empty($district) && !empty($created_by)) {
            $sql = "insert into agent_state_mapping (agent_id,state_code,district_id,created_by) select '$agentID',state_code,$district,'$created_by' from district_state_mapping where id='$district'";
            $this->db->query($sql);
        } else {
            return false;
        }
    }
    /**
     * @Desc: This function is used to insert record in table for mapping
     * @params: $agentID, $district
     * @return: boolean
     * @ Ghanshyam Ji Gupta
     */
    function get_asm_from_rm_district($district, $agent) {
        if (!empty($district) && !empty($agent)) {
            $this->db->select('agent_state_mapping.agent_id,district_state_mapping.district');
            $this->db->from('agent_state_mapping');
            $this->db->join('district_state_mapping', 'agent_state_mapping.district_id=district_state_mapping.id');
            $this->db->join('employee_hierarchy_mapping', 'agent_state_mapping.agent_id=employee_hierarchy_mapping.employee_id');
            $this->db->where_in('district_state_mapping.id', $district);
            $this->db->where('employee_hierarchy_mapping.manager_id', $agent);
            $query = $this->db->get();
            return $query->result_array();
        }
    }
    /**
     * @Desc: This function is used to update service center rm and asm id based on district
     * @params: $agentID, $district
     * @return: boolean
     * @ Ghanshyam Ji Gupta
     */
    function update_asm_rm_service_center($stringtoupdate, $district) {
        if (!empty($stringtoupdate) && !empty($district)) {
            $sql = "update service_centres   join district_state_mapping on service_centres.district = district_state_mapping.district set $stringtoupdate where district_state_mapping.id = $district";
            $this->db->query($sql);
        }
    }
    /**
     * @Desc: This function is to get district based on state string array
     * @params: $agentID, $district
     * @return: boolean
     * @ Ghanshyam Ji Gupta
     */
    function get_district_from_states($state_selected_string = '') {
        if (!empty($state_selected_string)) {
            $sql = "SELECT district_state_mapping.id,district_state_mapping.district,state_code.state,state_code.zone_id FROM district_state_mapping join state_code on district_state_mapping.state_code = state_code.state_code  where state_code.state in ($state_selected_string) order by field(state_code.state,$state_selected_string),district_state_mapping.district ASC";
            $query = $this->db->query($sql);
            return $query->result_array();
        }
    }
    /**
     * @desc This function is used to insert user action
     * @param Array $data
     * @return type
     */
    function agent_action_log($data){
        $this->db->insert('agent_action_log',$data);
        return  $this->db->insert_id();
    }
    
    /**
     * @desc This function is used to store query Used by a agent in its respective Log Table
     * @param Array $data
     * @return type
     */
    function query_log($table_name, $data){
        $this->db->insert($table_name,$data);
        return  $this->db->insert_id();
    }

}
