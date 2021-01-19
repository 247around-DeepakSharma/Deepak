<?php

class Indiapincode_model extends CI_Model {
    
       function __construct() {
        parent::__Construct();


        $this->db = $this->load->database('default', TRUE, TRUE);
    }

    
      public function count_all_indiapincode_master_list($post) {
        $this->_get_indiapincode_master_list($post, 'count(id) as numrows');
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
    }
    
    /**
     *  @desc : This function is used to get total filtered inventory master list
     *  @param : $post string
     *  @return: Array()
     */
    function count_filtered_indiapincode_master_list($post){
        $this->_get_indiapincode_master_list($post, 'count(id) as numrows');
        $query = $this->db->get();
      //  log_message('info', );
      // log_message('info', "Query: " . count($query->result()));
        return $query->result_array()[0]['numrows'];
    }
    
    
        function get_indiapincode_master_list($post, $select = "*",$is_array = false) {
        $this->_get_indiapincode_master_list($post, $select);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        
        $query = $this->db->get();
        if($is_array){
            return $query->result_array();
        }else{
            return $query->result();
        }
    }
    
    
    
        /**
     * @Desc: This function is used to get data from the inventory_master_list table
     * @params: $post array
     * @params: $select string
     * @return: void
     * 
     */
    function _get_indiapincode_master_list($post,$select){
        $this->db->distinct();
        $this->db->select($select,FALSE);
        $this->db->from('india_pincode');
        if (!empty($post['where'])) {
            $this->db->where($post['where']);
        }
        
        if (!empty($post['search_value'])) {
            $like = "";
            foreach ($post['column_search'] as $key => $item) { // loop column 
                // if datatable send POST for search
                if ($key === 0) { // first loop
                    $like .= "( " . $item . " LIKE '%" . $post['search_value'] . "%' ";
                } else {
                    $like .= " OR " . $item . " LIKE '%" . $post['search_value'] . "%' ";
                }
            }
            $like .= ") ";

            $this->db->where($like, null, false);
        }

        if (!empty($post['order'])) {
            $this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
        } else {
            $this->db->order_by('state','ASC');
        }
        
        if(!empty($post['group_by'])){
            $this->db->group_by($post['group_by']);
        }
    }
    
    
    function checkDuplicatePincode($data){ 
        $this->db->select("*");
        $this->db->where($data);
        $this->db->from('india_pincode');
        $query = $this->db->get();
        return $query->result();

    }
    
    function updateIndiaPincode($data,$id){
       $this->db->where('id',$id);
       $this->db->update('india_pincode',$data);
       if($this->db->affected_rows()>0){
           return TRUE;
       }else{
          return FALSE; 
       }
        
    }



    /**
     *  @desc : This function is to get all cities of state.
     *
     *  
     *
     *  @param : void state_code
     *  @return : array of cities
     *  @author : Abhishek Awasthi
     */

    function getStateCities($state_code){

        $this->db->distinct();
        $this->db->select('district');
        $this->db->where('state_code',$state_code);
        $this->db->order_by('district','asc');
        $query = $this->db->get('district_state_mapping');
        return $query->result_array();

    }

    /**
     *  @desc : This function is to get all states.
     *
     *  All the distinct states of India in Ascending order From Table state_code
     *
     *  @param : void
     *  @return : array of states
     */
    function get_allstates() {
	   $this->db->distinct();
	   $this->db->select('state,state_code');
	   $this->db->order_by('state','asc');
	   $query = $this->db->get('state_code');
	   return $query->result_array();
    }


    /*
     *  @param : void pincode
     *  @return : array of coordinates
     *  @author : Abhishek Awasthi
     */
    function getPinCoordinates($city){
        $this->db->distinct();
        $this->db->select('*');
        $this->db->where('district',$city);
        $query = $this->db->get('india_district_coordinates');
        return $query->result_array();
    }
    
    /*
     *  @param : void 
     *  @return : array of districts
     *  @author : Abhishek Awasthi
     */
    function getAllDistrict(){
        $this->db->distinct();
        $this->db->select('*');
        $query = $this->db->get('india_district_coordinates');
        return $query->result_array();   
    }

    /*
     *  @param : $data,$id
     *  @return : 
     *  @author : Abhishek Awasthi
     */
    function insertZone($data){
        $this->db->insert('india_district_coordinates',$data);

    }
    

}
