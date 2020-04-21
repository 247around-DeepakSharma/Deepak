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
    function get_without_cashback_transactions(){
        $this->db->select("paytm_transaction_callback.*");
        $this->db->where("paytm_cashback_details.transaction_id IS NULL AND paytm_transaction_callback.discount_flag=0");
        $this->db->join('paytm_cashback_details', 'paytm_cashback_details.transaction_id = paytm_transaction_callback.txn_id', 'left');
        $query = $this->db->get("paytm_transaction_callback");
        return $query->result_array();
    }
    function get_all_transactions_with_cashback_query($data,$start,$length){
        $where=array();
        if($data['transaction_start_date']){
            $where['date(paytm_transaction_callback.create_date)>="'.date( 'y-m-d', strtotime($data['transaction_start_date']) ).'"'] =NULL;
            $where['date(paytm_transaction_callback.create_date)<="'.date( 'y-m-d', strtotime($data['transaction_end_date']) ).'"'] =NULL; 
        }
         if($data['booking_id']){
            $where['paytm_transaction_callback.booking_id']=trim($data['booking_id']);
        }
        $this->db->select("paytm_transaction_callback.booking_id,SUM(paytm_transaction_callback.paid_amount ) as paid_amount,"
                . ",(SELECT SUM(cashback_amount) as cashback_amount FROM paytm_cashback_details WHERE paytm_transaction_callback.booking_id=booking_id) as cashback_amount");
        if($where){
            $this->db->where($where);
        }
        if($start && $length){
            $this->db->limit($length, $start);
        }
        $this->db->group_by("paytm_transaction_callback.booking_id");
        $query = $this->db->get("paytm_transaction_callback");
        return $query;
    }
    function get_all_transactions_with_cashback($data,$start=NULL,$length=NULL){
        $query = $this->get_all_transactions_with_cashback_query($data,$start,$length);
        return $query->result_array();
    }
    function get_all_transactions_with_cashback_count($data,$start=NULL,$length=NULL){
        $query = $this->get_all_transactions_with_cashback_query($data,$start,$length);
        return $query->num_rows();
    }
    function get_paytm_transaction_and_cashback($bookingID){
        $where['paytm_transaction_callback.booking_id'] = $bookingID;
        $this->db->select("paytm_transaction_callback.booking_id,paytm_transaction_callback.vendor_invoice_id,paytm_transaction_callback.order_id,paytm_transaction_callback.txn_id,paytm_transaction_callback.paid_amount,"
                . "paytm_transaction_callback.create_date,GROUP_CONCAT(paytm_cashback_details.cashback_reason) as cashback_reason,GROUP_CONCAT(paytm_cashback_details.cashback_amount)"
                . "as cashback_amount,GROUP_CONCAT(paytm_cashback_details.cashback_from) as cashback_from,GROUP_CONCAT(paytm_cashback_details.date) as cashback_date");
        $this->db->join('paytm_cashback_details','paytm_cashback_details.transaction_id=paytm_transaction_callback.txn_id','left');
        $this->db->where($where);
         $this->db->group_by("paytm_cashback_details.transaction_id");
        $query = $this->db->get("paytm_transaction_callback");
        return $query->result_array();
    }
    
    function get_paytm_transactions($booking_id){
        $this->db->select("*");
        $this->db->where("booking_id", $booking_id);
        $this->db->order_by("id", "DESC");
        $query = $this->db->get("paytm_transaction_callback");
        return $query->result_array();
    }
}