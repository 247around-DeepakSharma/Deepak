<?php
class paytm_payment_model extends CI_Model {
 function __construct() {
        parent::__Construct();
    }
    function get_qr_code($select,$where=NULL){
         $this->db->select($select);
         if(!empty($where)){
            $this->db->where($where);
        }
        $query = $this->db->get("paytm_payment_qr_code"); 
        return $query->result_array();
    }
    function inactive_qr_code($where,$data){
       $this->db->where($where);
       $this->db->update("paytm_payment_qr_code",$data);
       return $this->db->affected_rows();
    }
    
    /**
     * @desc This method is used to get cashback rules detais
     * @param Array $where
     * @return Array
     */
    function get_paytm_cashback_rules($where){
        $this->db->where($where);
        $this->db->select("*");
        $query =  $this->db->get("cashback_rules");
        return $query->result_array();
    }
}