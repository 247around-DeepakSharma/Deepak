<?php

class Questionnaire_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }

    public function get_questions($where = array()) {
        $this->db->_protect_identifiers = FALSE;
        $this->db->select("review_questionare.*,services.id as service_id,services.services,review_request_type_mapping.request_type_id,request_type.service_category, group_concat(review_questionare_checklist.answer) as answers");
        if (!empty($this->input->get("search"))) {
            $this->db->like('question', $this->input->get("search"));
        }
        $this->db->join("review_request_type_mapping", "review_questionare.q_id = review_request_type_mapping.q_id");
        $this->db->join("request_type", "review_request_type_mapping.request_type_id = request_type.id");
        $this->db->join("services", "request_type.service_id = services.id");
        $this->db->join("review_questionare_checklist", "review_questionare.q_id = review_questionare_checklist.q_id", "left");
        
        if(!empty($where)){
            $this->db->where($where);
        }
            
        $this->db->group_by('review_questionare.q_id');
        $query = $this->db->get("review_questionare");
        return $query->result();
    }
    
    public function save_question($data) {
        $arr_options = array();
        $rrtm_data['request_type_id'] = $data['request_type'];        
        if(!empty($data['options']))
        {
            $arr_options = explode(",", $data['options']);            
        }
        unset($data['request_type']);
        unset($data['service_id']);
        unset($data['options']);        
                
        // insert data in review_questionare 
        $data['created_by'] = $this->session->userdata('id');
        $this->db->insert('review_questionare', $data);
        $q_id = $this->db->insert_id();
                 
        // insert data in review_request_type_mapping
        $rrtm_data['q_id'] = $q_id;
        $rrtm_data['created_by'] = $this->session->userdata('id');
        $this->db->insert('review_request_type_mapping', $rrtm_data);
        
        // insert data in review_questionare_checklist
        if(!empty($arr_options)){
            foreach($arr_options as $option){
                $rqc_data['q_id'] = $q_id;
                $rqc_data['answer'] = $option;
                $rqc_data['created_by'] = $this->session->userdata('id');
                $this->db->insert('review_questionare_checklist', $rqc_data);
            }
        }
    }
    
    function update_question($where, $data) {
        $arr_options = array();
        $rrtm_data['request_type_id'] = $data['request_type'];        
        unset($data['request_type']);
        unset($data['service_id']);
        if(!empty($data['options']))
        {
            $arr_options = explode(",", $data['options']);
            unset($data['options']);
        } 
        if(empty($data['active'])){
            $data['active'] = 0;
        }
        
        // update data in review_questionare
        $this->db->where($where, FALSE);
        $this->db->update('review_questionare', $data);
        
        // update data in review_request_type_mapping
        $this->db->where($where, FALSE);
        $this->db->update('review_request_type_mapping', $rrtm_data);
                        
        // Get checklists that are already saved against Booking
        $this->db->select('group_concat(checklist_id) as checklist_ids');
        $this->db->where($where, FALSE);
        $query = $this->db->get('review_booking_checklist');
        $result = $query->result_array();
        
        // insert data in review_questionare_checklist, delete previous options and insert new ones
        $arr_checklist_where = $where;
        if(!empty($result[0]['checklist_ids'])){
            $arr_checklist_where['checklist_id NOT IN ('.$result[0]['checklist_ids'].')'] = NULL;
        }
        $this->db->where($arr_checklist_where, FALSE);
        $this->db->delete('review_questionare_checklist');
        if(!empty($arr_options)){
            foreach($arr_options as $option){
                $rqc_data['q_id'] = $data['q_id'];
                $rqc_data['answer'] = $option;
                // insert Option if not exists already
                $this->db->where($rqc_data, FALSE);
                $query = $this->db->get('review_questionare_checklist');
                $result_answers = $query->result_array();
                if(empty($result_answers)){
                    $rqc_data['created_by'] = $this->session->userdata('id');
                    $this->db->insert('review_questionare_checklist', $rqc_data);
                }
            }
        }
    }
    
    function update_question_status($where, $data) {
        // update data in review_questionare
        $this->db->where($where, FALSE);
        $this->db->update('review_questionare', $data);
        if ($this->db->affected_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }
    
}
