<?php

class Whatsapp_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }


    /**
     *  @desc : This function is used to get whatsapp log list
     *  @param : $post string
     *  @param : $select string
     *  @param : $sfIDArray array
     *  @return: Array()
     */
    function get_whatsapp_log_list($post, $select = "") {
        $this->_get_whatsapp_log_list($post, $select);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        $query = $this->db->get();

        $result = $query->result_array();


        return $result;
    }

    /**
     * @Desc: This function is used to get data from the whatsapp_log table
     * @params: $post array
     * @params: $select string
     * @return: void
     * 
     */
    function _get_whatsapp_log_list($post, $select) {

        if (empty($select)) {
            $select = '*';
        }
        $this->db->distinct();
        $this->db->select($select, FALSE);
        $this->db->from('whatsapp_logs');
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
            $this->db->order_by('id', 'desc');
        }

        if (!empty($post['group_by'])) {
            $this->db->group_by($post['group_by']);
        }
        if (isset($post['having']) && !empty($post['having'])) {
            $this->db->having($post['having'], FALSE);
        }
    }

    /**
     *  @desc : This function is used to get total inventory stocks
     *  @param : $post string
     *  @return: Array()
     */
    public function count_all_whatsapp_log($post) {
        $this->_get_whatsapp_log_list($post, 'count( DISTINCT id) as numrows');
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
    }

    /**
     *  @desc : This function is used to get total filtered inventory stocks
     *  @param : $post string
     *  @return: Array()
     */
    function count_filtered_whatsapp_log($post) {
        $sfIDArray = array();
        $this->_get_whatsapp_log_list($post, 'count( DISTINCT id) as numrows');
        $query = $this->db->get();
        return $query->result_array()[0]['numrows'];
    }

    
      /**
     *  @desc : This function is used to get chat of a number
     *  @param : $number string
     *  @return: Array()
     */  
    function getChatByNumber($number) {
        $sql = "select * from whatsapp_logs where source='" . $number . "' or destination='" . $number . "' order by created_on asc";
        $query = $this->db->query($sql);
        return $query->result_array();
    }


     /**
     *  @desc : This function is used to get last message details sent to whatsapp 
     *  @param : $phone string
     *  @return: Array()
     *  Abhishek Awasthi
     */     
    function get_last_whatsapp_message_send_tag($phone){
        $sql = "select * from whatsapp_logs where destination='" . $phone . "'  order by created_on desc";
        $query = $this->db->query($sql); 
        return $query->result_array();
    }
     /**
     *  @desc : This function is used to get whatsapp template
     *  @param : $select $where
     *  @return: result Array()
     *  Ghanshyam
     */
    
    function get_whatsapp_template($select,$where){
        if(!empty($select) && !empty($where)){
            $this->db->where($where);
            $this->db->select($select);
            $query = $this->db->get('whatsapp_template');
            return $query->result_array();
        }
    }
     /**
     *  @desc : This function is used to get whatsapp option by template ID and reply message
     *  @param : $phone string
     *  @return: Array()
     *  Ghanshyam
     */
    function get_last_whatsapp_message_send_tag_options($select,$where){
        if(!empty($select) && !empty($where)){
            $this->db->where($where);
            $this->db->select($select);
            $query = $this->db->get('whatsapp_options');
            return $query->result_array();
        }
    }

    /**
     *  @desc : This function is used to update whatsapp log
     *  @param : $phone string
     *  @return: Array()
     *  Ghanshyam
     */
    function update_whatsapp_log($where,$data){
      if(!empty($where) && !empty($data)){
         $this->db->where($where); 
         $this->db->update('whatsapp_logs',$data);
      }
    }
     


}
