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
    function get_payment_report_data($payment_type, $from_date, $to_date, $partner_vendor,$is_challan_data,$report_type="") {
        $return_data = [];
        switch ($payment_type) {
            case 'sales':
                $return_data = $this->get_sales_payment_report($from_date, $to_date, $partner_vendor,$is_challan_data);
                break;
            case 'purchase':
                $return_data = $this->get_purchase_payment_report($from_date, $to_date, $partner_vendor,$is_challan_data);
                break;
            case 'tds' :
                if($report_type === 'draft'){
                    $return_data = $this->get_draft_tds_payment_report($from_date, $to_date, $partner_vendor,$is_challan_data);
                }else if($report_type === 'final'){
                    $return_data = $this->get_final_tds_payment_report($from_date, $to_date, $partner_vendor,$is_challan_data);
                }
                
                break;
        }

        return $return_data;
    }

    /**
     * @desc: This Function is used to get the SALES PAYMENT REPORT
     * @param: string
     * @return : array
     */
    function get_sales_payment_report($from_date, $to_date, $partner_vendor,$is_challan_data) {
        if ($partner_vendor == 'partner') {
            if($is_challan_data === '1'){
                $where = "V.invoice_id NOT IN (SELECT invoice_id FROM invoice_challan_id_mapping) AND";
            }else if($is_challan_data === '2'){
                $where = "";
            }
            $sql = "Select V.invoice_id AS 'InvoiceNo',P.company_name as 'CompanyName', P.state as 'State',
                    V.invoice_date AS 'InvoiceDate',V.from_date AS 'FromDate',V.to_date AS 'ToDate', total_service_charge AS 'TotalServiceCharge' ,
                    V.total_additional_service_charge,V.service_tax AS 'ServiceTax', parts_cost as ' Parts',vat as 'VAT' ,`upcountry_price` as 'ConveyanceCharges',
                    courier_charges as 'Courier', total_amount_collected AS 'TotalAmountCollected', IFNULL(`rate`,0) as 'VAT Rate'
                    FROM  vendor_partner_invoices AS V
                    JOIN partners AS P on V.vendor_partner_id=P.id AND V.vendor_partner = 'partner'
                    JOIN tax_rates as tr  on tax_code='VAT' AND product_type='wall_bracket' AND P.state=tr.state
                    WHERE $where
                    V.type_code = 'A'
                    AND V.`invoice_date`>='$from_date'  AND V.`invoice_date` <'$to_date'";
        } else if ($partner_vendor == 'vendor') {
            if($is_challan_data === '1'){
                $where = "V.invoice_id NOT IN (SELECT invoice_id FROM invoice_challan_id_mapping) AND";
            }else if($is_challan_data === '2'){
                $where = "";
            }
            $sql = "Select V.invoice_id AS 'InvoiceNo',SC.company_name as 'CompanyName',state as 'State',
                    V.invoice_date AS 'InvoiceDate',from_date AS 'FromDate',to_date AS 'ToDate',
                    round((total_service_charge/1.15),2) AS 'AroundRoyalty' , round(((total_service_charge/1.15) * .15),2) AS 'ServiceTax',
                    total_amount_collected AS 'TotalAmountCollected'
                    FROM  vendor_partner_invoices AS V
                    JOIN service_centres AS SC on V.vendor_partner_id=SC.id
                    WHERE $where
                    V.vendor_partner =  'vendor' AND V.invoice_date >= '$from_date' AND V.invoice_date <= '$to_date'
                    AND type_code = 'A' AND type !=  'Stand' ";
        } else if ($partner_vendor == 'stand') {
            $sql = "SELECT `invoice_id` AS 'InvoiceNo', company_name AS 'CompanyName', P.state AS State, IFNULL( P.service_tax_no, '' ) AS 'ServiceTaxNo', 
                    IFNULL(tin_no, '' ) AS 'TINNo', invoice_date AS 'InvoiceDate', `parts_cost` AS Parts, `vat` AS VAT, 
                    (ABS( `amount_collected_paid` ) + tds_amount) AS 'TotalAmount', 
                    IFNULL( `rate` , 0 ) AS 'VATRate',vpi.from_date AS 'FromDate',vpi.to_date AS 'ToDate'
                    FROM `vendor_partner_invoices` AS vpi, service_centres AS P, tax_rates AS tr
                    WHERE `type_code` = 'B' AND vpi.type = 'Stand' AND vpi.`invoice_date` >= '$from_date' AND vpi.`invoice_date` < '$to_date'
                    AND `vendor_partner_id` = P.id AND tax_code = 'VAT' AND product_type = 'wall_bracket' AND P.state = tr.state";
        }

        $query = $this->db->query($sql);
        $data = $query->result_array();
        return $data;
    }

    /**
     * @desc: This Function is used to get the purchase PAYMENT REPORT
     * @param: string
     * @return : array
     */
    function get_purchase_payment_report($from_date, $to_date, $partner_vendor,$is_challan_data) {
        if ($partner_vendor == 'partner') {
            if($is_challan_data === '1'){
                $where = "AND invoice_id NOT IN (SELECT invoice_id FROM invoice_challan_id_mapping )";
            }else if($is_challan_data === '2'){
                $where = "";
            }
            $sql = "SELECT `invoice_id` as 'InvoiceNo', company_name as 'CompanyName', p.state as State, 
                    IFNULL(p.service_tax,'') as 'ServiceTaxNo', IFNULL(tin,'') as 'TINNo', 
                    invoice_date as 'InvoiceDate', vpi.`from_date` as 'FromDate', vpi.`to_date` as 'ToDate',
                    `total_service_charge` as 'ServiceCharges', vpi.total_additional_service_charge,round(vpi.service_tax,0) as 'ServiceTax', `parts_cost` as Parts, 
                    `vat` as VAT, `upcountry_price` as 'ConveyanceCharges', courier_charges as Courier,`penalty_amount` AS 'MiscDebit',
                    `credit_penalty_amount` AS 'MiscCredit', (abs(`amount_collected_paid`) + tds_amount ) as 'TotalAmount',
                    IFNULL(`rate`,0) as 'VATRate'
                    FROM `vendor_partner_invoices` as vpi, partners as p, tax_rates as tr
                    WHERE `type_code` = 'B' AND vpi.`invoice_date`>='$from_date'  AND vpi.`invoice_date`<'$to_date'
                    AND `vendor_partner` = 'partner' AND `vendor_partner_id`=p.id AND tax_code='VAT' AND product_type='wall_bracket' 
                    AND p.state=tr.state $where";
        } else if ($partner_vendor == 'vendor') {
            if($is_challan_data === '1'){
                $where = "AND invoice_id NOT IN (SELECT invoice_id FROM invoice_challan_id_mapping )";
            }else if($is_challan_data === '2'){
                $where = "";
            }
            $sql = "SELECT `invoice_id` as 'InvoiceNo', name as 'CompanyName', sc.state as State, 
                    IFNULL(service_tax_no,'') as 'ServiceTaxNo', IFNULL(tin_no,'') as 'TINNo', 
                    invoice_date as 'InvoiceDate', vpi.`from_date` as 'FromDate', vpi.`to_date` as 'ToDate',
                    `total_service_charge` as 'ServiceCharges', vpi.total_additional_service_charge,`service_tax` as 'ServiceTax', `parts_cost` as Parts, 
                    `vat` as VAT, `upcountry_price` as 'ConveyanceCharges', courier_charges as Courier,`penalty_amount` AS 'MiscDebit',
                    `credit_penalty_amount` AS 'MiscCredit', (abs(`amount_collected_paid`) + tds_amount ) as 'TotalAmount',
                    IFNULL(`rate`,0) as 'VATRate'
                    FROM `vendor_partner_invoices` as vpi, service_centres as sc, tax_rates as tr
                    WHERE `type_code` = 'B' AND vpi.type !='Stand' AND vpi.`invoice_date`>='$from_date'  AND vpi.`invoice_date`<'$to_date'
                    AND `vendor_partner` = 'vendor' AND `vendor_partner_id`=sc.id AND tax_code='VAT' AND product_type='wall_bracket' 
                    AND sc.state=tr.state $where";
        } else if ($partner_vendor == 'stand') {
            if($is_challan_data === '1'){
                $where = "AND invoice_id NOT IN (SELECT invoice_id FROM invoice_challan_id_mapping )";
            }else if($is_challan_data === '2'){
                $where = "";
            }
            $sql = "SELECT `invoice_id` as 'InvoiceNo', company_name as 'CompanyName', sc.state as State, IFNULL(sc.service_tax_no,'') as 'ServiceTaxNo',
                    IFNULL(tin_no,'') as 'TINNo', invoice_date as 'InvoiceDate', `parts_cost` as Parts, `vat` as VAT, 
                    (abs(`amount_collected_paid`) + tds_amount ) as 'TotalAmount',
                    IFNULL(`rate`,0) as 'VATRate'
                    FROM `vendor_partner_invoices` as vpi, service_centres as sc, tax_rates as tr
                    WHERE `type_code` = 'B' AND vpi.type='Stand' AND vpi.`invoice_date`>='$from_date'  AND vpi.`invoice_date`<'$to_date'
                    AND `vendor_partner_id`=sc.id AND tax_code='VAT' AND product_type='wall_bracket' AND sc.state=tr.state 
                    $where";
        }

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
    function get_draft_tds_payment_report($from_date, $to_date, $partner_vendor,$is_challan_data) {
        if ($partner_vendor == 'partner') {
            $sql = "";
            return false;
        } else if ($partner_vendor == 'vendor') {
            $sql = "SELECT company_name, company_type, payment_history.invoice_id, invoice_date, type,type_code,name_on_pan,
                    pan_no, owner_name, vendor_partner_invoices.total_service_charge, 
                    vendor_partner_invoices.total_additional_service_charge, vendor_partner_invoices.service_tax,
                    vendor_partner_invoices.total_amount_collected,(total_amount_collected - payment_history.tds_amount) as net_amount,
                    payment_history.tds_amount, tds_rate ,abs(vendor_partner_invoices.amount_collected_paid) as amount_collected_paid
                    FROM `payment_history`, vendor_partner_invoices, service_centres, tax_rates 
                    WHERE payment_history.create_date >= '$from_date' AND payment_history.create_date < '$to_date' 
                    AND payment_history.tds_amount > 0 AND vendor_partner_invoices.invoice_id = payment_history.invoice_id 
                    AND vendor_partner_invoices.vendor_partner = 'vendor' AND service_centres.id = vendor_partner_invoices.vendor_partner_id 
                    AND tax_rates.state = service_centres.state AND tax_rates.tax_code = 'ST'";
        } else if ($partner_vendor == 'stand') {
            $sql = "";
            return false;
        }

        $query = $this->db->query($sql);
        $data = $query->result_array();
        return $data;
    }
    
    /**
     * @desc: This Function is used to get the draft tds PAYMENT REPORT
     * @param: $from_date string
     * @param: $to_date string
     * @param: $partner_vendor string
     * @return : array
     */
    function get_final_tds_payment_report($from_date, $to_date, $partner_vendor,$is_challan_data) {
        if ($partner_vendor == 'partner') {
            $sql = "";
            return false;
        } else if ($partner_vendor == 'vendor') {
            $sql = "SELECT company_name,company_type,name_on_pan,pan_no,SUM(payment_history.tds_amount) as tds_amount,
                    tds_rate FROM `payment_history`, vendor_partner_invoices, service_centres, tax_rates
                    WHERE payment_history.create_date >= '$from_date' AND payment_history.create_date < '$to_date' 
                    AND payment_history.tds_amount > 0 AND vendor_partner_invoices.invoice_id = payment_history.invoice_id 
                    AND vendor_partner_invoices.vendor_partner = 'vendor' AND service_centres.id = vendor_partner_invoices.vendor_partner_id 
                    AND tax_rates.state = service_centres.state AND tax_rates.tax_code = 'ST' GROUP BY service_centres.id";
        } else if ($partner_vendor == 'stand') {
            $sql = "";
            return false;
        }

        $query = $this->db->query($sql);
        $data = $query->result_array();
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