<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class reusable_model extends CI_Model {
 function __construct() {
        parent::__Construct();
    }
    /*
     * This Function use to execute custom select query
     * @input - Custom query 
     * @output - result array for executed query
     */
    function execute_custom_select_query($sql){
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    /*
     * This Function use to execute custom insert,update,delete query
     * @input - Custom query
     * @output - number of affected rows by executed query
     */
    function execute_custom_insert_update_delete_query($sql){
       $this->db->query($sql);
       return $this->db->affected_rows();
    }
     /*
     * This function use to get query on the basis of input 
     * @input string(table),string(required field(comma seprated), array[OPTIONAL](key value pair array with where ),array[OPTIONAL](collection of fileds on which we want to apply group by))
     * @output array(result of query satisfied the where condition)
     */
    
    function get_search_query($table,$select,$where,$join,$limitArray,$orderBYArray,$whereIN,$JoinTypeTableArray,$groupBY=array()){
        $this->db->select($select,FALSE);
        if(!empty($where)){
            $this->db->where($where);
        }
        if(!empty($join)){
            if(!$JoinTypeTableArray){
                $JoinTypeTableArray = array();
            }
            foreach ($join as $tableName=>$joinCondition){
                if(array_key_exists($tableName, $JoinTypeTableArray)){
                    $this->db->join($tableName,$joinCondition,$JoinTypeTableArray[$tableName]);
                }
                else{
                    $this->db->join($tableName,$joinCondition);
                }
            }
        }
        if(!empty($limitArray)){
            if($limitArray['length'] != -1){
                 $this->db->limit($limitArray['length'], $limitArray['start']);
            }
        }
        if(!empty($orderBYArray)){
            foreach ($orderBYArray as $fieldName=>$sortingOrder){
                $this->db->order_by($fieldName, $sortingOrder);
            }
        }
        if(!empty($whereIN)){
            foreach ($whereIN as $fieldName=>$conditionArray){
                    $this->db->where_in($fieldName, $conditionArray);
            }
        }
        if(!empty($groupBY)){
            $this->db->group_by($groupBY);
        }
       return $query = $this->db->get($table);   
    }
    /*
     * This function used to update a tables field on given condition 
     * @input - TableName, data arary (field name , value in key_value pair),where array(field name,condition as key value pair) 
     */
    function update_table($table,$data,$where=NULL){
        if($where){
            $this->db->where($where);
        }
       $this->db->update($table,$data);
       return $this->db->affected_rows();
    }
    
    function insert_into_table($table,$data){
        $this->db->insert_ignore($table, $data);
        return $this->db->insert_id();
    }
    
    function insert_batch($table,$data){
        $this->db->insert_batch($table, $data); 
        return $this->db->affected_rows();
    }
    
     function get_search_result_data($table,$select,$where,$join,$limitArray,$orderBYArray,$whereIN,$JoinTypeTableArray,$groupBY=array()){
       $this->db->_reserved_identifiers = array('*','CASE',')','FIND_IN_SET','STR_TO_DATE','%d-%m-%Y,"")');
       $this->db->_protect_identifiers = FALSE;
       $query = $this->get_search_query($table,$select,$where,$join,$limitArray,$orderBYArray,$whereIN,$JoinTypeTableArray,$groupBY);
       return $query->result_array();
       //echo$this->db->last_query();
     }
    function get_search_result_count($table,$select,$where,$join,$limitArray,$orderBYArray,$whereIN,$JoinTypeTableArray){
       $this->db->_reserved_identifiers = array('*','CASE','FIND_IN_SET');
       $this->get_search_query($table,$select,$where,$join,$limitArray,$orderBYArray,$whereIN,$JoinTypeTableArray);
       return $this->db->affected_rows();
    }
    
    function get_rm_for_pincode($pincode){
        $sql = "SELECT india_pincode.pincode,employee_relation.agent_id as rm_id,india_pincode.state FROM india_pincode INNER JOIN state_code ON state_code.state=india_pincode.state LEFT JOIN employee_relation ON 
FIND_IN_SET(state_code.state_code,employee_relation.state_code) WHERE india_pincode.pincode IN ('" . $pincode . "') GROUP BY india_pincode.pincode";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    function get_state_for_rm($rmID){
       $sql = "SELECT state_code.state FROM employee_relation  LEFT JOIN state_code ON 
FIND_IN_SET(state_code.state_code,employee_relation.state_code) WHERE employee_relation.agent_id = '" . $rmID . "'";
         $query = $this->db->query($sql);
        return $query->result_array();
    }
    function delete_from_table($table,$where){
        $this->db->where($where);
        $this->db->delete($table);
    }
    
     /**
     * @desc This is used to get count of all data from given table
     * @param $table, $where
     * @return array
     */  
    function count_all_result($table, $where=array()){
        $this->db->from($table);
        if(isset($where)){
            $this->db->where($where);
        }
        $query = $this->db->count_all_results();
        return $query;
    }
}