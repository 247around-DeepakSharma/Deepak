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
}