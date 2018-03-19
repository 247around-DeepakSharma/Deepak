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
    function get_order_id_without_transaction_for_booking_id($booking_id){
        $query = "SELECT  order_id FROM paytm_payment_qr_code WHERE order_id NOT IN (SELECT order_id FROM paytm_transaction_callback WHERE booking_id='".$booking_id."')"
                . "AND booking_id ='".$booking_id."'";
        $query = $this->db->query($query);
        return $query->result_array();
    }
    function get_transactions_without_cashback(){
        $this->db->select("paytm_transaction_callback.*");
        $this->db->where("paytm_cashback_details.transaction_id IS NULL");
        $this->db->join('paytm_cashback_details', 'paytm_cashback_details.transaction_id = paytm_transaction_callback.txn_id', 'left');
        $query = $this->db->get("paytm_transaction_callback");
        return $query->result_array();
    }
}