<?php

class accounting_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }
    
    /**
     * @desc: This Function is used to insert the challan details into database
     * @param: array
     * @return : string
     */
    function insert_challan_details($data) {
        $this->db->insert('challan_details', $data);
        return $this->db->insert_id();
    }

    /**
     * @desc: This Function is used to edit the challan details
     * @param: array
     * @return : string
     */
    function edit_challan_details($data, $id) {
        $this->db->where('id', $id);
        $this->db->update('challan_details', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @desc: This Function is used to get the challan details from database
     * @param: string
     * @return : array
     */
    function fetch_challan_details($challan_type = "", $challan_id = "") {
        if ($challan_type != 'ALL' && $challan_id == "") {
            $this->db->where('type', $challan_type);
        } else if ($challan_type == "" && $challan_id != "") {
            $this->db->where('id', $challan_id);
        }
        $this->db->select('*');
        $this->db->from('challan_details');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * @desc: This Function is used to insert the challan and invoice id into database
     * @param: array
     * @return : string
     */
    function insert_invoice_challan_id_mapping_data($data) {
        $this->db->insert_batch('invoice_challan_id_mapping', $data);
        return $this->db->insert_id();
    }

    /**
     * @desc: This Function is used to get the payment report based on payment type
     * @param: string
     * @return : array
     */
    function get_account_report_data($payment_type, $from_date, $to_date, $partner_vendor,$is_challan_data,$invoice_data_by,$report_type="") {
        $return_data = array();
        switch ($payment_type) {
            case 'A':
            case 'B': 
                $return_data = $this->get_accounting_s_p_report($from_date, $to_date, $partner_vendor,$is_challan_data,$invoice_data_by, $payment_type);
                break;

            case 'tds' :
               
                $return_data = $this->get_tds_accounting_report($from_date, $to_date,$report_type);
                break;
        }

        return $return_data;
    }

    /**
     * @desc: This Function is used to get the purchase PAYMENT REPORT
     * @param: string
     * @return : array
     */
    function get_accounting_s_p_report($from_date, $to_date, $partner_vendor, $is_challan_data, $invoice_data_by, $payment_type) {
        if ($is_challan_data === '1') {
            $where = " AND vpi.invoice_id NOT IN (SELECT invoice_id FROM invoice_challan_id_mapping)";
        } else if ($is_challan_data === '2') {
            $where = "";
        }

        if ($invoice_data_by === 'invoice_date') {
            $where .= " AND vpi.`invoice_date`>='$from_date'  AND vpi.`invoice_date` <'$to_date'";
        } else if ($invoice_data_by === 'period') {
            $where .= " AND vpi.`from_date`>='$from_date'  AND vpi.`to_date` <'$to_date'";
        }

        $sql = "SELECT invoice_id, vendor_partner, IFNULL(sc.company_name,partners.company_name ) as company_name, "
                . " IFNULL(sc.state,partners.state ) as state, "
                . " IFNULL(sc.gst_no,partners.gst_number ) as gst_number, "
                . " invoice_date, from_date,"
                . " IFNULL(sc.service_tax_no, partners.service_tax) as service_tax_no, "
                . " IFNULL(sc.tin_no, partners.tin) as tin_no, "
                . " to_date,total_service_charge,"
                . " `total_additional_service_charge`,vpi.`service_tax`,"
                . " `parts_cost`,`vat`,`total_amount_collected`,"
                . " `around_royalty`,`amount_collected_paid`,"
                . " `hsn_code`,`cgst_tax_amount`,`igst_tax_amount`,"
                . "`sgst_tax_amount`,`cgst_tax_rate`,"
                . "`igst_tax_rate`,`sgst_tax_rate`,`tds_rate`,"
                . "`tds_amount`,`upcountry_price`,`penalty_amount`,"
                . "`credit_penalty_amount`,`courier_charges`,`num_bookings` "
                . " FROM vendor_partner_invoices as vpi LEFT JOIN service_centres as sc ON vendor_partner = 'vendor' "
                . " AND sc.id = vpi.vendor_partner_id LEFT JOIN partners ON vendor_partner = 'partner' "
                . " AND partners.id = vpi.vendor_partner_id WHERE "
                . " type_code = '$payment_type' AND vpi.type NOT IN ('".BUYBACK_VOUCHER."', '".PARTNER_VOUCHER."') AND  vendor_partner = '$partner_vendor' $where";
        
        $query = $this->db->query($sql);
        $data = $query->result_array();

        return $data;
    }

    /**
     * @desc: This Function is used to get the final tds PAYMENT REPORT
     * @param: $from_date string
     * @param: $to_date string
     * @param: $partner_vendor string
     * @return : array
     */
    function get_tds_accounting_report($from_date, $to_date, $report_type) {
        $group_by = "";
        if($report_type == "draft"){
            $select = "company_name, company_type, ph.invoice_id, invoice_date,name_on_pan,
                    pan_no, owner_name, vpi.total_service_charge, 
                    vpi.total_additional_service_charge, vpi.service_tax,
                    vpi.total_amount_collected,(total_amount_collected - ph.tds_amount) as net_amount,
                    ph.tds_amount, tds_rate ,abs(vpi.amount_collected_paid) as amount_collected_paid ";
        } else {
            $select = "company_name,company_type,name_on_pan,pan_no,SUM(ph.tds_amount) as tds_amount,
                    tds_rate";
            $group_by = " GROUP BY sc.id";
        }
        $sql ="SELECT $select
                    FROM payment_history as ph, vendor_partner_invoices as vpi, service_centres as sc 
                    WHERE ph.create_date >= '$from_date' AND ph.create_date < '$to_date'
                    AND ph.tds_amount > 0 AND type_code = 'B' AND sc.id = vendor_partner_id AND ph.invoice_id = vpi.invoice_id AND vendor_partner ='vendor' $group_by";

        $query1 = $this->db->query($sql);
      
        $data = $query1->result_array();
       
        return $data;
    }
    
   
    function insert_batch_payment_history($data) {
        $this->db->insert_batch('payment_history', $data);
        return $this->db->insert_id();
    }

    /**
     * @desc: This Function is used untag invoice id from challan id
     * @param: string
     * @return : array
     */
    function untag_challan_invoice_id($challan_id, $invoice_id) {
        $set = array('active' => 0);
        $this->db->where('challan_id', $challan_id);
        $this->db->where('invoice_id', $invoice_id);
        $this->db->update('invoice_challan_id_mapping', $set);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * @desc: This Function is used search the challan id
     * @param: array $where
     * @return : array
     */
    function get_challan_details($where) {
        $this->db->select('*');
        $this->db->where($where);
        $query = $this->db->get('challan_details');
        return $query->result_array();
    }
    
    /**
     * @desc: This Function is used get invoices mapped with challan id
     * @param: $challan_id string
     * @return : void()
     */
    function get_tagged_invoice_challan_data($challan_id){
        $this->db->select('invoice_id,challan_tender_date');
        $this->db->from('challan_details');
        $this->db->join('invoice_challan_id_mapping','challan_details.id=invoice_challan_id_mapping.challan_id');
        $this->db->where('invoice_challan_id_mapping.challan_id',$challan_id);
        $query = $this->db->get();
        return $query->result_array();
    }

}