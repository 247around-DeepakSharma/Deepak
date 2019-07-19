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
                $return_data = $this->get_tds_accounting_report($from_date, $to_date,$report_type,$invoice_data_by);
                break;
            case 'buyback' :
                $return_data = $this->get_buyback_accounting_report($from_date, $to_date, $partner_vendor,$is_challan_data,$invoice_data_by, $payment_type);
                break;
            case 'paytm' :
                $return_data = $this->get_paytm_accounting_report($from_date, $to_date, $partner_vendor,$is_challan_data,$invoice_data_by, $payment_type);
                break;
            case 'advance_voucher' :
                $return_data = $this->get_advance_voucher_accounting_report($from_date, $to_date, $invoice_data_by, $payment_type);
                break;
        }

        return $return_data;
    }
    
    
     /**
     * @desc: This Function is used to get the PAYTM PAYMENT REPORT for vendor and partner
     * @param: string
     * @return : array
     */
    function get_paytm_accounting_report($from_date, $to_date, $partner_vendor, $is_challan_data, $invoice_data_by, $payment_type){
        
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
            if($partner_vendor == 'partner'){
                $where .= " AND vpi.vertical = '".SERVICE."' AND vpi.category = '".ADVANCE."' AND  vpi.sub_category = '".PRE_PAID_PAYMENT_GATEWAY."'";
            }
            else if($partner_vendor == 'vendor'){
                $where .= " AND vpi.vertical = '".SERVICE."' AND vpi.category = '".CREDIT_NOTE."' AND  vpi.sub_category = '".CUSTOMER_PAYMENT."'";
            }
            $sql = "SELECT invoice_id, vendor_partner, IFNULL(sc.name,partners.company_name ) as company_name, "
                . " IFNULL(sc.address,partners.address ) as address, "
                . " IFNULL(sc.state,partners.state ) as state, invoice_date,`reference_invoice_id`, "
                . " IFNULL(sc.gst_no,partners.gst_number ) as gst_number, IFNULL(sc.gst_taxpayer_type, '') as gst_reg_type,"
                . " from_date, to_date,"
                . " IFNULL(sc.service_tax_no, partners.service_tax) as service_tax_no, "
                . " IFNULL(sc.tin_no, partners.tin) as tin_no, total_service_charge, "
                . " `total_additional_service_charge`,vpi.`service_tax`,"
                . " `parts_cost`,`parts_count`, `vat`,`total_amount_collected`,"
                . " `around_royalty`,`total_service_charge`,abs(`amount_collected_paid`) as amount_collected_paid,"
                . " `hsn_code`,`cgst_tax_amount`,`igst_tax_amount`,"
                . "`sgst_tax_amount`,`cgst_tax_rate`,"
                . "`igst_tax_rate`,`sgst_tax_rate`,`tds_rate`,"
                . "`tds_amount`,`upcountry_price`,`penalty_amount`,"
                . "`credit_penalty_amount`,`courier_charges`,`num_bookings`,vpi.type,vpi.type_code,  vertical, category, sub_category "
                . " FROM vendor_partner_invoices as vpi LEFT JOIN service_centres as sc ON vendor_partner = 'vendor' "
                . " AND sc.id = vpi.vendor_partner_id LEFT JOIN partners ON vendor_partner = 'partner' "
                . " AND partners.id = vpi.vendor_partner_id WHERE "
                . " vendor_partner = '$partner_vendor' $where";
        
        $query = $this->db->query($sql);
        $data = $query->result_array();
        return $data;    
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

        $sql = "SELECT invoice_id, vendor_partner, IFNULL(sc.name,partners.company_name ) as company_name, "
                . " IFNULL(sc.address,partners.address ) as address, "
                . " IFNULL(sc.state,partners.state ) as state, "
                . " IFNULL(sc.gst_no,partners.gst_number ) as gst_number, IFNULL(sc.gst_taxpayer_type, '') as gst_reg_type,"
                . " invoice_date, from_date,"
                . " IFNULL(sc.service_tax_no, partners.service_tax) as service_tax_no, "
                . " IFNULL(sc.tin_no, partners.tin) as tin_no, "
                . " to_date,total_service_charge,"
                . " `total_additional_service_charge`,vpi.`service_tax`, vpi.warehouse_storage_charges, vpi.miscellaneous_charges,"
                . " `parts_cost`,`parts_count`, `parts_cost`, `vat`,`total_amount_collected`,"
                . " `around_royalty`,`total_service_charge`,`parts_count`,`reference_invoice_id`,abs(`amount_collected_paid`) as amount_collected_paid,"
                . " `hsn_code`,`cgst_tax_amount`,`igst_tax_amount`,"
                . "`sgst_tax_amount`,`cgst_tax_rate`,"
                . "`igst_tax_rate`,`sgst_tax_rate`,`tds_rate`,"
                . "`tds_amount`,`upcountry_price`,`penalty_amount`,"
                . "`credit_penalty_amount`,`courier_charges`,`num_bookings`,vpi.type,vpi.type_code,"
                . "vertical, category, sub_category"
                . " FROM vendor_partner_invoices as vpi LEFT JOIN service_centres as sc ON vendor_partner = 'vendor' "
                . " AND sc.id = vpi.vendor_partner_id LEFT JOIN partners ON vendor_partner = 'partner' "
                . " AND partners.id = vpi.vendor_partner_id WHERE "
                . " type_code = '$payment_type' AND vpi.type NOT IN ('".BUYBACK_VOUCHER."', '".BUYBACK_TYPE."') AND  vendor_partner = '$partner_vendor' $where";
        
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
    function get_tds_accounting_report($from_date, $to_date, $report_type,$invoice_data_by) {
        $group_by = "";
        if($report_type == "draft"){ 
            $select = "name,company_name, company_type, vpi.invoice_id, vpi.invoice_date,name_on_pan, address, state, gst_taxpayer_type,
                    pan_no, owner_name, vpi.total_service_charge, vpi.type, vpi.reference_invoice_id, vpi.type_code,
                    vpi.total_additional_service_charge, vpi.service_tax, vpi.parts_count, vpi.parts_cost,
                    vpi.total_amount_collected,(total_amount_collected - vpi.tds_amount) as net_amount,
                    vpi.tds_amount, tds_rate ,abs(vpi.amount_collected_paid) as amount_collected_paid,sc.gst_no, vertical, category, sub_category,cgst_tax_amount,sgst_tax_amount,igst_tax_amount,cgst_tax_rate,sgst_tax_rate,igst_tax_rate,`num_bookings`";
        } else {
            $select = "name,company_name, company_type, vpi.invoice_id, vpi.invoice_date,name_on_pan, address, state, gst_taxpayer_type,
                    pan_no, owner_name, SUM(vpi.total_service_charge) as total_service_charge, vpi.type, vpi.reference_invoice_id, vpi.type_code,
                    SUM(vpi.total_additional_service_charge) as total_additional_service_charge, SUM(vpi.service_tax) as service_tax, SUM(vpi.parts_count) as parts_count, SUM(vpi.parts_cost) as parts_cost,
                    SUM(vpi.total_amount_collected) as total_amount_collected,SUM(total_amount_collected - vpi.tds_amount) as net_amount,
                    SUM(vpi.tds_amount) as tds_amount, tds_rate ,SUM(abs(vpi.amount_collected_paid)) as amount_collected_paid,sc.gst_no, vertical, category, sub_category,SUM(cgst_tax_amount) as cgst_tax_amount,SUM(sgst_tax_amount) as sgst_tax_amount,SUM(igst_tax_amount) as igst_tax_amount,cgst_tax_rate,sgst_tax_rate,igst_tax_rate,SUM(`num_bookings`) as num_bookings";
//            $select = "name,company_name,company_type,name_on_pan,pan_no,SUM(tds_amount) as tds_amount, vertical, category, sub_category,
//                      tds_rate, (SUM(total_service_charge)+ SUM(courier_charges) + SUM(warehouse_storage_charges) + SUM(miscellaneous_charges) + SUM(upcountry_price) + SUM(credit_penalty_amount) + SUM(total_additional_service_charge) - SUM(penalty_amount)) as tds_taxable_amount";
            $group_by = " GROUP BY sc.id, tds_rate";
        }
        $where = "";
        if ($invoice_data_by === 'invoice_date') {
            $where .= " AND vpi.`invoice_date`>='$from_date'  AND vpi.`invoice_date` <'$to_date'";
        } else if ($invoice_data_by === 'period') {
            $where .= " AND vpi.`from_date`>='$from_date'  AND vpi.`to_date` <'$to_date'";
        }
        $sql ="SELECT $select
                    FROM vendor_partner_invoices as vpi, service_centres as sc 
                    WHERE vpi.tds_amount > 0 AND type_code = 'B' AND sc.id = vpi.vendor_partner_id  AND vendor_partner ='vendor' $where $group_by";

        $query1 = $this->db->query($sql);
      
        $data = $query1->result_array();
       
        return $data;
    }
    
     /**
     * @desc: This Function is used to get the final tds PAYMENT REPORT
     * @param: $from_date string
     * @param: $to_date string
     * @param: $partner_vendor string
     * @return : array
     */
    function get_advance_voucher_accounting_report($from_date, $to_date,$invoice_data_by, $payment_type) {
        $where = "";
        if ($invoice_data_by === 'invoice_date') {
            $where .= " AND vpi.`invoice_date`>='$from_date'  AND vpi.`invoice_date` <'$to_date'";
        } else if ($invoice_data_by === 'period') {
            $where .= " AND vpi.`from_date`>='$from_date'  AND vpi.`to_date` <'$to_date'";
        }
        
        $sql ="SELECT vpi.invoice_id as advance_voucher, p.public_name as partner_name,p.address,p.state, total_service_charge, total_additional_service_charge,
                cgst_tax_amount, cgst_tax_rate, sgst_tax_amount, sgst_tax_rate, igst_tax_amount, igst_tax_rate, total_amount_collected, invoice_date,gst_number, from_date,
                to_date, vpi.type, vpi.type_code, vertical, category, sub_category, credit_debit, bt.invoice_id, abs(amount_collected_paid) as amount_collected_paid, vpi.tds_rate, vpi.tds_amount, reference_invoice_id, vpi.parts_count, `num_bookings`
                FROM vendor_partner_invoices as vpi, bank_transactions as bt, partners as p
                WHERE vpi.vendor_partner = 'partner' AND vpi.type = 'Partner_Voucher' AND type_code = 'B' AND bt.is_advance = '1' AND p.id = vpi.vendor_partner_id AND vendor_partner ='partner' $where";

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
    
    function get_buyback_accounting_report($from_date, $to_date, $partner_vendor, $is_challan_data, $invoice_data_by, $payment_type){
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

        $sql = "SELECT invoice_id, vendor_partner, IFNULL(sc.name,partners.company_name ) as company_name, "
                . " IFNULL(sc.address,partners.address ) as address, "
                . " IFNULL(sc.state,partners.state ) as state, "
                . " IFNULL(sc.gst_no,partners.gst_number ) as gst_number, "
                . " IFNULL(sc.gst_taxpayer_type, '') as gst_registration_type, "
                . " invoice_date, from_date,"
                . " IFNULL(sc.service_tax_no, partners.service_tax) as service_tax_no, "
                . " IFNULL(sc.tin_no, partners.tin) as tin_no, "
                . " to_date,total_service_charge,"
                . " `total_additional_service_charge`,vpi.`service_tax`,"
                . " `parts_cost`,`parts_count`,`vat`,`total_amount_collected`, vpi.type, vpi.type_code,"
                . " `around_royalty`,`amount_collected_paid`,"
                . " `hsn_code`,"
                . " case when((`cgst_tax_amount`+`igst_tax_amount`+`sgst_tax_amount`) = 0) Then buyback_tax_amount ELSE (`cgst_tax_amount`+`igst_tax_amount`+`sgst_tax_amount`) END as tax,"
                . "`cgst_tax_rate`,`igst_tax_rate`,`sgst_tax_rate`,`tds_rate`,"
                . "`tds_amount`,`upcountry_price`,`penalty_amount`,"
                . "`credit_penalty_amount`,`courier_charges`,`num_bookings`, vertical, category, sub_category, reference_invoice_id"
                . " FROM vendor_partner_invoices as vpi LEFT JOIN service_centres as sc ON vendor_partner = 'vendor' "
                . " AND sc.id = vpi.vendor_partner_id LEFT JOIN partners ON vendor_partner = 'partner' "
                . " AND partners.id = vpi.vendor_partner_id WHERE "
                . " vpi.type IN ('".$payment_type."') AND  vendor_partner = '$partner_vendor' $where";
        
        $query = $this->db->query($sql);
        log_message("info", $this->db->last_query());
        $data = $query->result_array();

        return $data;
    }
    function get_courier_documents($id=NULL){
        $where = "WHERE courier_details.is_active = 1";
        if($id){
            $where = " WHERE courier_details.id = ".$id;
        }
        $query = $this->db->query("SELECT courier_details.*,
        (CASE
            WHEN sender_entity_type  = 'partner' THEN partners.public_name
            WHEN sender_entity_type  = 'vendor' THEN service_centres.name
            ELSE employee.full_name
        END) as sender_entity_name,
        (CASE
            WHEN receiver_entity_type  = 'partner' THEN p.public_name
            WHEN receiver_entity_type  = 'vendor' THEN s.name
            ELSE e.full_name
        END) as receiver_entity_name,
        contact_person.name as contact_person_name
        FROM courier_details LEFT JOIN `employee` ON `courier_details`.`sender_entity_id` = `employee`.`id` LEFT JOIN `partners` ON `courier_details`.`sender_entity_id`= `partners`.`id` 
        LEFT JOIN `service_centres` ON `courier_details`.`sender_entity_id`= `service_centres`.`id` LEFT JOIN `partners` as p ON `courier_details`.`receiver_entity_id`= `p`.`id` 
        LEFT JOIN `service_centres` as s ON `courier_details`.`receiver_entity_id`= `s`.`id` LEFT JOIN `employee` as e ON `courier_details`.`sender_entity_id` = e.`id` 
        LEFT JOIN `contact_person` ON `courier_details`.`contact_person_id`= `contact_person`.`id` ".$where." ORDER BY `courier_details`.`id` asc ;");
        return $query->result();
    }
     /**
     * @desc: This Function is used to insert gstr2a data
     * @param: array $data
     * @return : insert_id
     */
    function insert_taxpro_gstr2a_data($data) {
        $this->db->insert_ignore_duplicate_batch('taxpro_gstr2a_data', $data);
        return $this->db->insert_id();
    }
    
     /**
     * @desc: This Function is used to get gstr2a data
     * @param: array $select, $where
     * @return : array
     */
    function get_taxpro_gstr2a_data($select, $where = array()){
        $this->db->select($select);
        if(!empty($where)){
            $this->db->where($where);
        }
        $this->db->from('taxpro_gstr2a_data');

        $query = $this->db->get();
        return $query->result_array(); 
    }
    
    function get_gstr2a_mapping_details($condition, $select){
        $this->_get_gstr2a_mapping_details($condition, $select);
        if(isset($condition['length'])){
            if ($condition['length'] != -1) {
                $this->db->limit($condition['length'], $condition['start']);
            }
        }
        $query = $this->db->get();
         log_message("info", $this->db->last_query());
        return $query->result_array();
    }
    
     /**
     * @desc: This Function is used to get gstr2a data for gstra2a report
     * @param: array $select, $where
     * @return : array
     */
    function _get_gstr2a_mapping_details($condition, $select){
        
        $this->db->select($select);
        $this->db->from('taxpro_gstr2a_data');
        
        if($condition['entity_type'] == 'vendor'){
            $this->db->join('service_centres', 'service_centres.gst_no = taxpro_gstr2a_data.gst_no');
        }
        else if($condition['entity_type'] == 'partner'){
            $this->db->join('partners', 'partners.gst_number = taxpro_gstr2a_data.gst_no');
        }
        else if($condition['entity_type'] == 'other'){
            $this->db->join('service_centres', 'service_centres.gst_no = taxpro_gstr2a_data.gst_no', 'left');
            $this->db->join('partners', 'partners.gst_number = taxpro_gstr2a_data.gst_no', 'left');
            $this->db->join('gstin_detail', 'gstin_detail.gst_number = taxpro_gstr2a_data.gst_no', 'left');
        }
        
        if(!empty($condition['where'])){
            $this->db->where($condition['where']);
        }
         
        if (!empty($condition['search'])) {
            $key = 0;
            $like = "";
            foreach ($condition['search'] as $index => $item) {
                if ($key === 0) { // first loop
                   // $this->db->like($index, $item);
                    $like .= "( ".$index." LIKE '%".$item."%' ";
                } else {
                    $like .= " OR ".$index." LIKE '%".$item."%' ";
                }
                $key++;
            }
            $like .= ") ";

            $this->db->where($like, null, false);
        }
        
        if (!empty($condition['search_value'])) {
            $like = "";
            foreach ($condition['column_search'] as $key => $item) { // loop column 
                // if datatable send POST for search
                if ($key === 0) { // first loop
                    $like .= "( " . $item . " LIKE '%" . $condition['search_value'] . "%' ";
                } else {
                    $like .= " OR " . $item . " LIKE '%" . $condition['search_value'] . "%' ";
                }
            }
            $like .= ") ";

            $this->db->where($like, null, false);
        }
        
        
        if(!empty($condition['order_by'])){
            $this->db->order_by($condition['order_by']);
        }else if(!empty ($condition['order'])){
            $this->db->order_by($condition['column_order'][$condition['order'][0]['column']], $condition['order'][0]['dir']);
        }else{
            $this->db->order_by('taxpro_gstr2a_data.invoice_number', "asc");
            $this->db->order_by($condition['column_order'], "asc");
        }
    }
    
    function update_taxpro_gstr2a_data($id, $data){
        $this->db->where('id', $id);
        $this->db->update('taxpro_gstr2a_data', $data);
        if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }
    
    function count_all_taxpro_gstr2a_data($post){
        $this->_count_all_taxpro_gstr2a_data($post);
        $query = $this->db->count_all_results();
        return $query;
    }
    
    function _count_all_taxpro_gstr2a_data($post){
        $this->db->from('taxpro_gstr2a_data');
        if($post['entity_type'] == 'vendor'){
            $this->db->join('service_centres', 'service_centres.gst_no = taxpro_gstr2a_data.gst_no');
        }
        else if($post['entity_type'] == 'partner'){
            $this->db->join('partners', 'partners.gst_number = taxpro_gstr2a_data.gst_no');
        }
        else if($post['entity_type'] == 'other'){
            $this->db->join('service_centres', 'service_centres.gst_no = taxpro_gstr2a_data.gst_no', 'left');
            $this->db->join('partners', 'partners.gst_number = taxpro_gstr2a_data.gst_no', 'left');
        }
        $this->db->where($post['where']);
    }
    
    function count_filtered_taxpro_gstr2a_data($condition, $select){
        $this->_get_gstr2a_mapping_details($condition, $select);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    function get_variable_charge($select='*', $where=array()){
        $this->db->select($select);
        if(!empty($where)){
            $this->db->where($where);
        }
        $this->db->from('variable_charges_type');

        $query = $this->db->get();
        return $query->result_array(); 
    }
    
    /**
     * @desc This function is used to insert variable charges type
     * @param String $data
     * @return insert_id
     */
    function insert_into_variable_charge($data){
        $this->db->insert('variable_charges_type', $data);
         log_message("info", $this->db->last_query());
        return $this->db->insert_id();
    }
    
     /**
     * @desc This function is used to get variable charges type
     * @param String $select
     * @param Array $where 
     * @return Array
     */
    function get_vendor_partner_variable_charges($select, $where=array(), $join=false){
        $this->db->select($select);
        if(!empty($where)){
            $this->db->where($where);
        }
        if($join == true){
            $this->db->join('variable_charges_type', 'variable_charges_type.id = vendor_partner_variable_charges.charges_type');
        }
        $this->db->from('vendor_partner_variable_charges');

        $query = $this->db->get();
        return $query->result_array(); 
    }
}
