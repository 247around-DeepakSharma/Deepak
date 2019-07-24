<?php

class invoices_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }

    /*
     * Save invoice information in vendor_partner_invoices table
     * Details has all invoice related info like id, type, from/to date,
     * various amounts, 247around royalty etc.
     */

    function insert_new_invoice($details) {
        //Check if invoice_id present then update row, else add new 
        $this->db->insert('vendor_partner_invoices', $details);
        return $this->db->insert_id();
    }
    
    function action_customer_invoice($details){
        $this->db->where('invoice_id', $details['invoice_id']);
        $query = $this->db->get('invoice');
        if ($query->num_rows > 0) {
            $this->update_invoice(array('invoice_id' => $details['invoice_id']));
            return true;
        } else {
            return $this->insert_invoice($details);
           
        }
    }
    function update_invoice_breakup($where, $data){
        $this->db->where($where);
        $this->db->update('invoice_details', $data);
    }
    
    function insert_invoice_breakup($invoice_details){
        return $this->db->insert_batch("invoice_details", $invoice_details);
    }
    
    function get_new_invoice_data($where, $select = "*", $group_by = false) {

        $this->db->select($select, false);
        $this->db->where($where);
        if($group_by){
            $this->db->group_by($group_by);
        }
        $query = $this->db->get("invoice");
        if ($query->num_rows > 0) {
            return $query->result_array();
        } else {
            return FALSE;
        }
    }
    
    function update_invoice($where, $data){
        $this->db->where($where);
        $this->db->update('invoice', $data);
    }
    
    function insert_invoice($details){
        $this->db->insert('invoice', $details);
        return $this->db->insert_id();
    }

    /**
     * @desc: If invoice id already exist then update this row otherwise insert invoice details
     * @param Array $details
     */
    function action_partner_invoice($details) {
        $this->db->where('invoice_id', $details['invoice_id']);
        $query = $this->db->get('vendor_partner_invoices');
        if ($query->num_rows > 0) {
            $this->db->where('invoice_id', $details['invoice_id']);
            $this->db->update('vendor_partner_invoices', $details);
            return true;
//            if($this->db->affected_rows() > 0){
//               return true;
//            }else{
//                return false;
//            }
        } else {

            $this->db->insert('vendor_partner_invoices', $details);
            return $this->db->insert_id();
        }
    }

    //TODO: This should be moved to Partner model
    //
    // Get Poc and owner email id for patticular vendor id
    function getEmailIdForVendor($vendor_id) {
        $this->db->select('primary_contact_email, owner_email');
        $this->db->where('id', $vendor_id);
        $query = $this->db->get('service_centres');
        return $query->result_array();
    }

    /**
     * Get data from vendor_partner_invoices table where vendor id
     * @param : vendor partner id
     * @return :Array
     */
    function getInvoicingData($data,$join = false) {
        $this->db->where($data);
        $this->db->order_by('invoice_date', "desc");
        $query = $this->db->get('vendor_partner_invoices');
        $return_data = $query->result_array();
        
        if($join && !empty($return_data)){
            if($return_data[0]['vendor_partner'] === 'vendor' || $return_data[0]['vendor_partner'] === 'user'){
                $details = $this->vendor_model->getVendorDetails("service_centres.company_name as vendor_partner_name",array('service_centres.id'=> $return_data[0]['vendor_partner_id']));
            }
            else if($return_data[0]['vendor_partner'] === 'partner'){
                $details = $this->partner_model->getpartner_details("public_name as vendor_partner_name",array('partners.id'=> $return_data[0]['vendor_partner_id']));
            }
            
            $return_data[0]['vendor_partner_name'] = $details[0]['vendor_partner_name'];
        }
        
        return $return_data;
    }


    //Function to insert banks account/statement
    function bankAccountTransaction($account_statement) {
        $this->db->insert('bank_transactions', $account_statement);
        return $this->db->insert_id();
    }

    function get_bank_transactions_details($select,$data, $join = '') {
        $this->db->select($select);
        $this->db->where($data);
        if($join != ''){
            $this->db->join('employee','bank_transactions.agent_id = employee.id');
        }
        $this->db->order_by('transaction_date DESC');
        $query = $this->db->get('bank_transactions');
        return $query->result_array();
    }

    /**
     * @desc: This is used to update bank transaction table
     * @param Array $where
     * @param Array $data
     */
    function update_bank_transactions($where, $data) {
        $this->db->where($where);
        $this->db->update('bank_transactions', $data);
    }

    /*
     * @desc: Show all bank transactions
     * @param: party type (vendor, partner, all)
     */

    function get_all_bank_transactions($type) {
        switch ($type) {
            case 'vendor':
                $sql = "SELECT service_centres.name, bank_transactions . *
            FROM service_centres, bank_transactions
            WHERE bank_transactions.partner_vendor =  'vendor'
            AND bank_transactions.partner_vendor_id = service_centres.id
            ORDER BY bank_transactions.transaction_date DESC";
                $query = $this->db->query($sql);
                break;

            case 'partner':
                $sql = "SELECT partners.public_name as name, bank_transactions . *
            FROM partners, bank_transactions
            WHERE bank_transactions.partner_vendor =  'partner'
            AND bank_transactions.partner_vendor_id = partners.id
            ORDER BY bank_transactions.transaction_date DESC";
                $query = $this->db->query($sql);
                break;

            case 'all':
                //TODO: This is not sorted on transaction date
                $sql = "SELECT partners.public_name, bank_transactions. *
            FROM partners, bank_transactions
            WHERE bank_transactions.partner_vendor =  'partner'
            AND bank_transactions.partner_vendor_id = partners.id

            UNION

            SELECT service_centres.name, bank_transactions. *
            FROM service_centres, bank_transactions
            WHERE bank_transactions.partner_vendor =  'vendor'
            AND bank_transactions.partner_vendor_id = service_centres.id";
                $query = $this->db->query($sql);
                break;
        }

        return $query->result_array();
    }

    /**
     * @desc: This is used to get sum of credit amount and debit amount for specific vendor or partner
     * @param: vendor_partner (vendor or partner) AND vendor or partner ID
     * @return: array()
     */
    function getbank_transaction_summary($vendor_partner, $partner_vendor_id) {
        $sql = " SELECT COALESCE(SUM(`credit_amount`),0) as credit_amount, COALESCE(SUM(`debit_amount`),0) as debit_amount  "
                . "from bank_transactions where partner_vendor = '$vendor_partner' AND "
                . "partner_vendor_id = '$partner_vendor_id' AND is_advance = 0 ";
        $data = $this->db->query($sql);
        return $data->result_array();
    }

    /**
     * @desc: This function is used to get invoicing summary for vendor or partner.
     * @param: String ( vendor or partner)
     * @return: Array()
     */
    function getsummary_of_invoice($vendor_partner, $where, $due_date_flag = false, $partner_type=array()) {
        $is_active = 'All';
        $is_prepaid = NULL;
        if ($vendor_partner == "vendor") {
            $select = "service_centres.name, service_centres.id, service_centres.on_off, service_centres.active, account_holders_bank_details.is_verified, service_centres.pan_no, service_centres.service_tax_no, service_centres.tin_no, service_centres.cst_no, service_centres.contract_file, service_centres.gst_no";
            if($where){
                foreach($where as $key=>$value){
                    $newWhere["service_centres.".$key] = $value;
                }
            }
            $data = $this->vendor_model->get_vendor_with_bank_details($select, $newWhere);
            $due_date_status = "";
            if($due_date_flag){
                $due_date_status = " AND `due_date` <= '".$due_date_flag."'"; 
            }
            
        } else if ($vendor_partner == "partner") {
            $p_where = array();
            if(isset($where['active'])){
                $p_where = array('is_active' => $where['active']);
                $is_active = $where['active'];
            }
            if(isset($where['id'])){    
                $p_where['id'] = $where['id'];
            }
            if(isset($where['is_prepaid'])){    
                $p_where['is_prepaid'] = $where['is_prepaid'];
                $is_prepaid = $where['is_prepaid'];
            }
            
            if(empty($partner_type)){
                $data = $this->partner_model->get_all_partner($p_where);
            }
            else{
                $data = $this->partner_model->get_partner_details_with_soucre_code($is_active, $partner_type, 'All', NULL, NULL, $is_prepaid);
            }
            
            $due_date_status = "";
        }

        foreach ($data as $key => $value) {

            $result = $this->get_summary_invoice_amount($vendor_partner, $value['id'], $due_date_status);
           
            $data[$key]['vendor_partner'] = $vendor_partner;
            $data[$key]['final_amount'] = $result[0]['final_amount'];
            $data[$key]['amount_collected_paid'] = $result[0]['amount_collected_paid'];
//           $data[$key]['is_stand'] = $result[0]['is_stand'];
            if (isset($value['name'])) {
                $sp_d = $this->get_pending_defective_parts($value['id']);
                if(!empty($sp_d)){
                    $data[$key]['count_spare_part'] = $sp_d[0]['count'];
                    $data[$key]['max_sp_age'] = $sp_d[0]['max_sp_age'];
                    $data[$key]['shipped_parts'] = $sp_d[0]['parts'];
                    $data[$key]['challan_value'] = $sp_d[0]['challan_value'];
                } else {
                    $data[$key]['count_spare_part'] = 0;
                    $data[$key]['max_sp_age'] = 0;
                    $data[$key]['shipped_parts'] = "";
                    $data[$key]['challan_value'] = 0;
                }
                
            } else if (isset($value['public_name'])) {
                $data[$key]['name'] = $value['public_name'];
               
                $data[$key]['is_verified'] = 1;
            }

            $result[0]['id'] = $value['id'];
        }
        
        return $data;
    }
    
    function get_summary_invoice_amount($vendor_partner, $vendor_partner_id, $otherWhere =""){
            if($vendor_partner ==  _247AROUND_SF_STRING){
                $s = "CASE WHEN (amount_collected_paid > 0) THEN COALESCE(SUM(`amount_collected_paid` - amount_paid ),0) ELSE COALESCE(SUM(`amount_collected_paid` + amount_paid ),0) END as amount_collected_paid ";
                $w = "AND settle_amount = 0 AND sub_category NOT IN ('".DEFECTIVE_RETURN."', '".IN_WARRANTY."', '".MSL."', '".MSL_SECURITY_AMOUNT."', '".NEW_PART_RETURN."' ) ";
            } else {
                $s = " COALESCE(SUM(`amount_collected_paid` ),0) as amount_collected_paid ";
                $w = "";
            }
            $sql = "SELECT $s "
                    . " FROM  `vendor_partner_invoices` "
                    . " WHERE vendor_partner_id = '$vendor_partner_id' AND vendor_partner = '$vendor_partner' $w  $otherWhere";


            $data = $this->db->query($sql);
            $result = $data->result_array();
            if($vendor_partner ==  _247AROUND_SF_STRING){
                $result[0]['final_amount'] = sprintf("%.2f",($result[0]['amount_collected_paid']));
            } else {
                 $bank_transactions = $this->getbank_transaction_summary($vendor_partner, $vendor_partner_id);
                 $result[0]['final_amount'] = sprintf("%.2f",($result[0]['amount_collected_paid'] - $bank_transactions[0]['credit_amount'] + $bank_transactions[0]['debit_amount']));
            }
            return $result;
    }
    /**
     * @desc Sf did not ship defective parts(TAT breached)
     * @param int $service_center_id
     * @return Array
     */
    function get_pending_defective_parts($service_center_id){
        $select = "count(spare_parts_details.booking_id) as count, SUM(challan_approx_value) as challan_value, GROUP_CONCAT( DISTINCT shipped_parts_type) as parts, DATEDIFF(CURRENT_TIMESTAMP, MIN(service_center_closed_date)) as max_sp_age";
        $where = array(
            "spare_parts_details.defective_part_required"=>1,
            "spare_parts_details.service_center_id" => $service_center_id,
            "status IN ('".DEFECTIVE_PARTS_PENDING."', '".DEFECTIVE_PARTS_REJECTED."')  " => NULL,
            "DATEDIFF(CURRENT_TIMESTAMP, service_center_closed_date) > '".DEFECTIVE_PART_PENDING_OOT_DAYS."' " => NULL
            
        );
        $group_by = "spare_parts_details.service_center_id";
        $data = $this->service_centers_model->get_spare_parts_booking($where, $select, $group_by);
        return $data;
        
    }
    /**
     * @desc This function is used to get those defective parts who has not shipped with in TAT
     * @param int $service_center_id
     * @return Array
     */
    function get_oot_shipped_defective_parts($service_center_id){
        $select = "count(spare_parts_details.booking_id) as count, SUM(challan_approx_value) as challan_value, GROUP_CONCAT( DISTINCT shipped_parts_type) as parts, DATEDIFF(CURRENT_TIMESTAMP, MIN(service_center_closed_date)) as max_sp_age";
        $where = array(
            "spare_parts_details.defective_part_required"=>1,
            "spare_parts_details.service_center_id" => $service_center_id,
            "status" => DEFECTIVE_PARTS_SHIPPED,
            "DATEDIFF(CURRENT_TIMESTAMP, service_center_closed_date) > '".SHIPPED_DEFECTIVE_PARTS_AFTER_TAT_BREACH."' " => NULL
            
        );
        $group_by = "spare_parts_details.service_center_id";
        $data = $this->service_centers_model->get_spare_parts_booking($where, $select, $group_by);
        return $data;
    }
    /**
     * @desc Defective Part shipped by SF but not receive by Partner
     * @param int $service_center_id
     * @return Array
     */
    function get_intransit_defective_parts($service_center_id){
        $select = "count(spare_parts_details.booking_id) as count, SUM(challan_approx_value) as challan_value, GROUP_CONCAT( DISTINCT shipped_parts_type) as parts, DATEDIFF(CURRENT_TIMESTAMP, MIN(defective_part_shipped_date)) as max_sp_age";
        $where = array(
            "spare_parts_details.defective_part_required"=>1,
            "spare_parts_details.service_center_id" => $service_center_id,
            "status" => DEFECTIVE_PARTS_SHIPPED,
            "DATEDIFF(CURRENT_TIMESTAMP, defective_part_shipped_date) > '".DEFECTIVE_PART_SHIPPED_OOT_DAYS."' " => NULL
            
        );
        $group_by = "spare_parts_details.service_center_id";
        $data = $this->service_centers_model->get_spare_parts_booking($where, $select, $group_by);
        return $data;
    }

    /**
     * @desc: Delete Bank transaction
     * @param: bank account transaction id
     * @return:
     */
    function delete_banktransaction($transaction_id) {
        $this->db->where('id', $transaction_id);
        $this->db->delete("bank_transactions");
    }

    /**
     * @desc: this method generates invoice summary and also details. when this method executes 1st for loop then  get all data  for invoices details and executes 2nd for loop then get add data for invoice summary.
     * @param: partner id and date range
     * @return: Array()
     */
    function getpartner_invoices($partner_id, $from_date, $to_date, $spare_requested_data = array() ) {
        log_message('info', __FUNCTION__);
        $s = "";
        if(!empty($spare_requested_data)){
            $u = array_column($spare_requested_data, 'id');
            $s = " OR ( booking_unit_details.id IN(". implode(",", $u).") ) ";
        }
        $sql1 = "SELECT booking_unit_details.id AS unit_id,"
                . " CASE WHEN (booking_unit_details.partner_id = '".PAYTM_ID."' ) THEN (SUBSTRING_INDEX(order_id, '-', 1)) ELSE (order_id) END AS order_id, "
                . " CONCAT('''', booking_unit_details.sub_order_id) as sub_order_id, `booking_details`.booking_id as booking_id, "
                . "  invoice_email_to,invoice_email_cc, booking_details.rating_stars,  "
                . " `booking_details`.partner_id, `booking_details`.source, "
                . " CASE WHEN (serial_number_pic = '' OR serial_number_pic IS NULL) THEN ('') ELSE (CONCAT('".S3_WEBSITE_URL.SERIAL_NUMBER_PIC_DIR."/', serial_number_pic)) END as serial_number_pic,"
                . "  DATE_FORMAT(STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y'), '%D %b %Y') as booking_date, "
                . " `booking_details`.city, DATE_FORMAT(`booking_unit_details`.ud_closed_date, '%D %b %Y') as closed_date,price_tags, "
                . " `booking_unit_details`.appliance_capacity,`booking_unit_details`.appliance_category,`booking_unit_details`.appliance_brand, "
                . "  booking_details.booking_primary_contact_no,  "
                . " `services`.services, users.name, "
                . " partner_net_payable, round((partner_net_payable * ".DEFAULT_TAX_RATE .")/100,2) as gst_amount,
                    CASE WHEN (booking_details.is_upcountry = 1) THEN ('Yes') ELSE 'NO' END As upcountry,
                    CASE WHEN (file_name = '' OR file_name IS NULL) THEN ('') ELSE (GROUP_CONCAT(CONCAT('".S3_WEBSITE_URL."misc-images/', file_name))) END as support_file,
              
                    CASE WHEN(serial_number IS NULL OR serial_number = '') THEN '' ELSE (CONCAT('''', booking_unit_details.serial_number))  END AS serial_number,
                    CASE WHEN(model_number IS NULL OR model_number = '') THEN (sf_model_number) ELSE (model_number) END AS model_number

              From booking_details LEFT JOIN booking_files on `booking_details`.booking_id = `booking_files`.booking_id , booking_unit_details, services, partners, users
                  WHERE `booking_details`.booking_id = `booking_unit_details`.booking_id 
                  AND `services`.id = `booking_details`.service_id 
                  AND booking_details.partner_id = '$partner_id'
                  AND users.user_id = booking_details.user_id
                  AND booking_unit_details.partner_net_payable > 0 
                  AND booking_unit_details.partner_id = partners.id
                  AND partner_invoice_id IS NULL 
                  
                  AND ( ( booking_status = 'Completed'
                            AND booking_unit_details.booking_status = 'Completed'
                            AND booking_unit_details.ud_closed_date >= '$from_date'
                            AND booking_unit_details.ud_closed_date < '$to_date'
                        ) $s
                    ) GROUP BY `booking_details`.booking_id
               ";



        $query1 = $this->db->query($sql1);
        return $query1->result_array();
    }

    function get_total_booking_for_check_invoices($vendor_id, $from_date, $to_date) {
        $sql = "SELECT count(booking_id) as count from booking_details where assigned_vendor_id = '$vendor_id' AND closed_date >= '$from_date' AND closed_date < '$to_date'  ";
        $query = $this->db->query($sql);

        $return = $query->result_array();
        return $return[0]['count'];
    }

    function insert_invoice_row($invoices_data, $invoice_type) {
        switch ($invoice_type) {
            case "final":
                $this->db->insert('vendor_invoices_snapshot', $invoices_data);
                break;

            case "draft":
                $this->db->insert('vendor_invoices_snapshot_draft', $invoices_data);
                break;

            default:
                break;
        }
    }

    /**
     * @desc: This method settle invoices
     * First it get amount collected for particular invoices And
     * Calculate total amount to be pay, postive amount(FOC), negative amount(CASH),
     * net collected amount(If already paid amount corresponding that invoice id
     * then we will minus in amount_collected_paind amount).
     * @param: Array Invoice ID
     * @param: Net Amount Transfered
     */
    function update_settle_invoices($invoice_id, $txn_paid_amount) {
        $total_amount_to_be_pay = 0;
        $total_positive_amount_to_be_pay = 0;
        $total_negative_amount_to_be_pay = 0;
        $net_collected_amount = 0;
        $invoices = array();
        //Get amount for invoices
        foreach ($invoice_id as $custom_invoices) {
            $this->db->select('invoice_id, amount_collected_paid, amount_paid');
            $this->db->where('invoice_id', $custom_invoices);
            $query = $this->db->get('vendor_partner_invoices');

            if ($query->num_rows > 0) {
                $result = $query->result_array();
                // Check Postive Amount
                if ($result[0]['amount_collected_paid'] >= 0) {
                    // Already paid amount but not settle
                    $net_collected_amount = ($result[0]['amount_collected_paid'] - $result[0]['amount_paid']);
                    //total positive amount to be pay
                    $total_positive_amount_to_be_pay += $net_collected_amount;
                } else {
                    // Already paid amount but not settle
                    $net_collected_amount = ($result[0]['amount_collected_paid'] + $result[0]['amount_paid']);
                    //total negative amount to be pay
                    $total_negative_amount_to_be_pay += $net_collected_amount;
                }
                //Array with index key Invoice ID
                $invoices[$result[0]['invoice_id']]['net_collected_amount'] = $net_collected_amount;
                //Array with index key Invoice ID
                $invoices[$result[0]['invoice_id']][] = $result[0]['amount_collected_paid'];
                //Final amount to be pay for these Invoice ID
                $total_amount_to_be_pay += $net_collected_amount;
            }
        }
        $paid_amount = sprintf("%.2f", $txn_paid_amount);
        //Sort Array
        asort($invoices);
        // Check if Paid amount is equal to  total amount to be pay then we settle all invoices.
        if (intval(abs($paid_amount)) == intval(abs($total_amount_to_be_pay))) {
            foreach ($invoice_id as $id_invoice) {

                $this->update_partner_invoices(array('invoice_id' => $id_invoice), array('settle_amount' => 1, 'amount_paid' => abs($invoices[$id_invoice][0])));
            }
        } else if ($total_positive_amount_to_be_pay > abs($total_negative_amount_to_be_pay)) {
            // Vendor Pays
            $settled_amount = abs($total_negative_amount_to_be_pay);
            $txn_settled_amount = $settled_amount + abs($paid_amount);

            foreach ($invoices as $key => $value) {
                //Assume:-- If the vendor will pay, then we settle all invoice id in which 247Around to be paid
                if ($value[0] < 0) {
                    $settle_data['invoice_id'] = $key;
                    $settle_data['settle_amount'] = 1;
                    $settle_data['amount_paid'] = abs($value[0]);

                    $this->update_partner_invoices(array('invoice_id' => $key), $settle_data);
                } else if ($txn_settled_amount > 0) {

                    $txn_settled_amount = $this->update_settlement($value, $key, $txn_settled_amount);
                }
            }
        } else if ($total_positive_amount_to_be_pay < abs($total_negative_amount_to_be_pay)) {
            //247Around Pay

            $settled_amount = abs($total_positive_amount_to_be_pay);
            $txn_settled_amount = $settled_amount + abs($paid_amount);

            foreach ($invoices as $key => $value) {
                //Assume:-- If the 247Around will pay, then we settle all invoice id in which Vendor to be paid
                if ($value[0] > 0) {
                    $settle_data['invoice_id'] = $key;
                    $settle_data['settle_amount'] = 1;
                    $settle_data['amount_paid'] = $value[0];


                    $this->update_partner_invoices(array('invoice_id' => $key), $settle_data);
                } else if ($txn_settled_amount > 0) {

                    $txn_settled_amount = $this->update_settlement($value, $key, $txn_settled_amount);
                }
            }
        }
    }

    /**
     * @desc: Update Invoice id
     * It checks, net collected amount is greater than or less than
     * from sum of settled amount and paid amount.
     * If sum of settled amount and paid amount is greater then we will settle these invoice id
     * AND minus net collected amount from sum of settled amount and paid amount(txn_settled_amount).
     * If sum of settled amount and paid amount is less than then we will  minus net collected amount
     * from sum of settled amount and paid amount(txn_settled_amount).
     * And Checks txn_settled amount is greater then we will settle these invoice id
     * AND txn_settled amount is less than than insert amount paid for that invoice id
     */
    function update_settlement($value, $key, $txn_settled_amount) {
        if ($txn_settled_amount >= abs($value['net_collected_amount'])) {
            $settle_data['invoice_id'] = $key;
            $settle_data['settle_amount'] = 1;
            $settle_data['amount_paid'] = abs($value[0]);

            $txn_settled_amount = $txn_settled_amount - abs($value['net_collected_amount']);

            $this->update_partner_invoices(array('invoice_id' => $key), $settle_data);
        } else if ($txn_settled_amount < abs($value['net_collected_amount'])) {

            $settle_data['invoice_id'] = $key;
            $txn_settled_amount = $txn_settled_amount - abs($value['net_collected_amount']);
            if ($txn_settled_amount >= 0) {
                $settle_data['settle_amount'] = 1;
                $settle_data['amount_paid'] = abs($value[0]);
            } else {
                $settle_data['settle_amount'] = 0;
                $settle_data['amount_paid'] = abs($value['net_collected_amount']) - abs($txn_settled_amount);
            }

            $this->update_partner_invoices(array('invoice_id' => $key), $settle_data);
        }
        return $txn_settled_amount;
    }

    /**
     * @desc: Update Vendor partner invoice table
     */
    function update_partner_invoices($where, $details) {
        $this->db->where($where);
        $this->db->update('vendor_partner_invoices', $details);
    }

    function get_unsettle_amount($vendor_partner_id, $vendor_partner) {
        $data['vendor_partner'] = $vendor_partner_id;
        $data['vendor_partner_id'] = $vendor_partner;
        $invoice_array = $this->invoices_model->getInvoicingData($data);


        $data2['partner_vendor'] = $vendor_partner_id;
        $data2['partner_vendor_id'] = $vendor_partner;
        $bank_statement = $this->invoices_model->get_bank_transactions_details('*',$data2);

        $amount_collected_paid = 0;
        $debit_amount = 0;
        $credit_amount = 0;
        foreach ($invoice_array as $key => $invoice) {
            $amount_collected_paid = ($invoice['amount_collected_paid'] + $amount_collected_paid );
        }

        foreach ($bank_statement as $key => $bs) {
            $debit_amount += intval($bs['debit_amount']);
            $credit_amount += intval($bs['credit_amount']);
        }

        return $amount_collected_paid + $debit_amount - $credit_amount;
    }

    /**
     * @desc: This method returns count of invoice id
     * @param String WHERE
     * @return Array
     */
    function get_invoices_details($where, $select = "*", $group_by = false) {

        $this->db->select($select, false);
        if(!empty($where)){
            $this->db->where($where);
        }
        
        if($group_by){
            $this->db->group_by($group_by);
        }
        $query = $this->db->get("vendor_partner_invoices");
        if ($query->num_rows > 0) {
            return $query->result_array();
        } else {
            return FALSE;
        }
    }
    
    function get_partner_invoice_data($partner_id, $from_date, $to_date, $tmp_from_date) {
        $spare_requested_data = $this->get_unit_for_requested_spare($partner_id);
        $s = "";
        if(!empty($spare_requested_data)){
            $u = array_column($spare_requested_data, 'id');
            $s = " OR ( ud.id IN(". implode(",", $u).") ) ";
        }
        
        $sql = "SELECT DISTINCT (`partner_net_payable`) AS rate, " . HSN_CODE . " AS hsn_code, 
                CASE 
                   WHEN MIN( ud.`appliance_capacity` ) = '' AND MAX( ud.`appliance_capacity` ) = '' THEN
                   concat(services,' ', price_tags )
                    
                   WHEN (MIN( ud.`appliance_capacity` ) = MAX( ud.`appliance_capacity` ) ) THEN 
                   concat(services,' ', price_tags,' (', 
                   MAX( ud.`appliance_capacity` ),') ' )

                    WHEN MIN( ud.`appliance_capacity` ) = '' AND MAX( ud.`appliance_capacity` ) != '' THEN 
                    concat(services,' ', price_tags,' (', 
                    MAX( ud.`appliance_capacity` ),') ' )

                    WHEN MIN( ud.`appliance_capacity` ) != '' AND MAX( ud.`appliance_capacity` ) = '' THEN
                     concat(services,' ', price_tags,' (', 
                    MIN( ud.`appliance_capacity` ),') ' )
                
                ELSE 
                    concat(services,' ', price_tags,' (', MIN( ud.`appliance_capacity` ),
                '-',MAX( ud.`appliance_capacity` ),') ' )
                
                
                END AS description, 
                " . DEFAULT_TAX_RATE . " as gst_rate,
                COUNT( ud.`appliance_capacity` ) AS qty, 
                (partner_net_payable * COUNT( ud.`appliance_capacity` )) AS taxable_value,
                `partners`.company_name, product_or_services,
                `partners`.address as company_address, partners.pincode, partners.district,
                `partners`.state, partners.is_wh,
                `partners`.gst_number
                FROM  `booking_unit_details` AS ud, services, partners
                WHERE `partner_net_payable` >0
                AND ud.service_id = services.id
                AND partners.id = ud.partner_id
                AND partner_invoice_id IS NULL
                AND ( ( ud.partner_id =  '$partner_id'
                        AND ud.booking_status =  'Completed'
                        AND ud.ud_closed_date >=  '$from_date'
                        AND ud.ud_closed_date < '$to_date'
                    ) $s
                  )
                GROUP BY  `partner_net_payable`, ud.service_id,price_tags,product_or_services   ";

        $query = $this->db->query($sql);
        $result['result'] = $query->result_array();

        //if (!empty($result['result'])) {
        $upcountry_data = $this->upcountry_model->upcountry_partner_invoice($partner_id, $from_date, $to_date, $s);
        $courier = $this->get_partner_courier_charges($partner_id, $from_date, $to_date);
        $pickup_courier = $this->get_pickup_arranged_by_247around_from_partner($partner_id, $from_date, $to_date);
        $warehouse_courier = $this->get_partner_invoice_warehouse_courier_data($partner_id, $from_date, $to_date);
        $packaging_charge = $this->get_partner_invoice_warehouse_packaging_courier_data($partner_id, $from_date, $to_date);
        $defective_return_to_partner = $this->get_defective_parts_courier_return_partner($partner_id, $from_date, $to_date);
        
        
        
        
        
        $misc_select = 'booking_details.order_id, miscellaneous_charges.booking_id, '
                . 'miscellaneous_charges.product_or_services, miscellaneous_charges.description, vendor_basic_charges,'
                . 'miscellaneous_charges.partner_charge, miscellaneous_charges.id,'
                . 'CONCAT("' . S3_WEBSITE_URL . 'misc-images/",approval_file) as file';

        $misc = $this->get_misc_charges_invoice_data($misc_select, "miscellaneous_charges.partner_invoice_id IS NULL", $from_date, $to_date, "booking_details.partner_id", $partner_id, "partner_charge", _247AROUND_COMPLETED);
        $result['upcountry'] = array();
        $result['pickup_courier'] = array();
        $result['courier'] = array();
        $result['misc'] = array();
        $result['warehouse_courier'] = array();
        $result['defective_part_by_wh'] = array();
        $result['final_courier'] = array();
        $result['packaging_rate'] = 0;
        $result['packaging_quantity'] = 0;
        $result['warehouse_storage_charge'] = 0;
        $result['micro_warehouse_list'] = array();
        $result['packaging_data'] = array();
        $result['spare_requested_data'] = $spare_requested_data;
        $final_courier = array_merge($courier,$pickup_courier, $warehouse_courier, $defective_return_to_partner);
        
        if (!empty($upcountry_data)) {
            if($upcountry_data[0]['total_upcountry_price'] > 0){
                $up_country = array();
                $up_country[0]['description'] = 'Upcountry Charges';
                $up_country[0]['hsn_code'] = '';
                $up_country[0]['qty'] = '';
                $up_country[0]['rate'] = '';
                $up_country[0]['gst_rate'] = DEFAULT_TAX_RATE;
                $up_country[0]['product_or_services'] = 'Upcountry';
                $up_country[0]['taxable_value'] = sprintf("%.2f", $upcountry_data[0]['total_upcountry_price']);
                $result['result'] = array_merge($result['result'], $up_country);
                $result['upcountry'] = $upcountry_data;
            }
            
        }

        if (!empty($packaging_charge)) {
            $packaging = $this->get_fixed_variable_charge(array('entity_type' => _247AROUND_PARTNER_STRING,
                "entity_id" => $partner_id, "variable_charges_type.type" => PACKAGING_RATE_TAG, 'fixed_charges > 0' => NULL));
            if (!empty($packaging)) {
                $c_data = array();
                $c_data[0]['description'] = $packaging[0]['description'];
                $c_data[0]['hsn_code'] = $packaging[0]['hsn_code'];
                $c_data[0]['qty'] = count($packaging_charge);
                $c_data[0]['rate'] = $packaging[0]['fixed_charges'];
                $c_data[0]['gst_rate'] = $packaging[0]['gst_rate'];
                $c_data[0]['product_or_services'] = $packaging[0]['description'];
                $c_data[0]['taxable_value'] = sprintf("%.2f",($c_data[0]['qty'] * $packaging[0]['fixed_charges']));
                $result['result'] = array_merge($result['result'], $c_data);
                
                $result['packaging_rate'] = $packaging[0]['fixed_charges'];
                $result['packaging_quantity'] = count($packaging_charge);
                
                $result['warehouse_courier'] = $warehouse_courier;
                $result['packaging_data'] = $packaging_charge;
            }
        }

        if (!empty($final_courier)) {
            $c_data = array();
            $courier_price = (array_sum(array_column($final_courier, 'courier_charges_by_sf')));
            if($courier_price > 0){
                $c_data[0]['description'] = 'Courier Charges';
                $c_data[0]['hsn_code'] = '';
                $c_data[0]['qty'] = '';
                $c_data[0]['rate'] = '';
                $c_data[0]['gst_rate'] = DEFAULT_TAX_RATE;
                $c_data[0]['product_or_services'] = 'Courier';
                $c_data[0]['taxable_value'] = sprintf("%.2f", $courier_price);
                $result['result'] = array_merge($result['result'], $c_data);
                
            }
            $result['courier'] = $courier;
            $result['pickup_courier'] = $pickup_courier;
            $result['final_courier'] = $final_courier;
            $result['defective_part_by_wh'] = $defective_return_to_partner;
            
        }

        if (!empty($misc)) {
            $m = array();
            $m[0]['description'] = 'Miscellaneous Charge';
            $m[0]['hsn_code'] = '';
            $m[0]['qty'] = '';
            $m[0]['rate'] = '';
            $m[0]['gst_rate'] = DEFAULT_TAX_RATE;
            $m[0]['product_or_services'] = 'Misc';
            $m[0]['taxable_value'] = sprintf("%.2f", (array_sum(array_column($misc, 'partner_charge'))));
            $result['result'] = array_merge($result['result'], $m);
            $result['misc'] = $misc;
        }

        if (!empty($result['result'])) {


            if (!isset($result['result'][0]['company_name'])) {
                $partner_details = $this->partner_model->getpartner_details('partner_id,invoice_email_to,invoice_email_cc,'
                        . '`partners`.company_name, `partners`.address as company_address, partners.pincode, partners.district,'
                        . '`partners`.state, partners.is_wh,`partners`.gst_number'
                        . '', array('partners.id' => $partner_id));

                $result['result'][0]['company_name'] = $partner_details[0]['company_name'];
                $result['result'][0]['invoice_email_to'] = $partner_details[0]['invoice_email_to'];
                $result['result'][0]['invoice_email_cc'] = $partner_details[0]['invoice_email_cc'];
                $result['result'][0]['company_address'] = $partner_details[0]['company_address'];
                $result['result'][0]['pincode'] = $partner_details[0]['pincode'];
                $result['result'][0]['district'] = $partner_details[0]['district'];
                $result['result'][0]['state'] = $partner_details[0]['state'];
                $result['result'][0]['is_wh'] = $partner_details[0]['is_wh'];
                $result['result'][0]['gst_number'] = $partner_details[0]['gst_number'];
            }

            
            $fixed_charges = $this->get_fixed_variable_charge(array('entity_type' => _247AROUND_PARTNER_STRING,
                "entity_id" => $partner_id, "variable_charges_type.is_fixed" => 1));
            if (!empty($fixed_charges)) {
                foreach ($fixed_charges as $value) {
                    $c_data = array();
                    $c_data[0]['description'] = $value['description'];
                    $c_data[0]['hsn_code'] = $value['hsn_code'];
                    $c_data[0]['qty'] = 1;
                    $c_data[0]['rate'] = $value['fixed_charges'];
                    $c_data[0]['gst_rate'] = $value['gst_rate'];
                    $c_data[0]['product_or_services'] = $value['description'];
                    $c_data[0]['taxable_value'] = $value['fixed_charges'];
                    $result['result'] = array_merge($result['result'], $c_data);
                    //$result['warehouse_storage_charge'] = $packaging1[0]['fixed_charges'];
                }
                
            }
            
            $micro_charges = $this->get_fixed_variable_charge(array('entity_type' => _247AROUND_PARTNER_STRING,
                "entity_id" => $partner_id, "variable_charges_type.type" => MICRO_WAREHOUSE_CHARGES_TYPE));
            if (!empty($micro_charges)) {
                foreach ($micro_charges as $key => $value) {
                    $micro_wh_lists = $this->invoices_model->calculate_active_microwarehouse($partner_id, $tmp_from_date, $to_date);
                    if($micro_wh_lists['count'] > 0){
                        $c_data = array();
                        $c_data[0]['description'] = $value['description'];
                        $c_data[0]['hsn_code'] = $value['hsn_code'];
                        $c_data[0]['qty'] = $micro_wh_lists['count'];
                        $c_data[0]['rate'] = $value['fixed_charges'];
                        $c_data[0]['gst_rate'] = $value['gst_rate'];
                        $c_data[0]['product_or_services'] = $value['description'];
                        $c_data[0]['taxable_value'] = $micro_wh_lists['count'] * $value['fixed_charges'];
                        $result['result'] = array_merge($result['result'], $c_data);
                       
                        $result['micro_warehouse_list'] = $micro_wh_lists['list'];
                    }
                }
                
            }
            return $result;
        } else {
            return false;
        }
    }
    /**
     * @desc This function is used to get micro warehouse invoice data for Partner
     * It will create full amount invoice when Micro warehouse created before requested from date
     * It will create partial amount invoice when Micro invoice created with in requested Date
     * @param int $partner_id
     * @param String $from_date
     * @param String $to_date
     * @return Array
     */
    function calculate_active_microwarehouse($partner_id, $from_date, $to_date){
        $micro_wh_lists = $this->inventory_model->get_micro_wh_lists_by_partner_id("micro_wh_mp.*, service_centres.company_name", array('micro_wh_mp.partner_id' => $partner_id, 
            'micro_wh_mp.create_date < "'.$from_date.'" ' => NULL, "micro_wh_mp.active" => 1));

        $count = 0;
        if(!empty($micro_wh_lists)){
            $count = count($micro_wh_lists);
        } 
        
        $micro_wh = $this->inventory_model->get_micro_wh_lists_by_partner_id("micro_wh_mp.*, service_centres.company_name", array('micro_wh_mp.partner_id' => $partner_id, 
            'micro_wh_mp.create_date >= "'.$from_date.'" ' => NULL, "micro_wh_mp.create_date <= '".$to_date."' " => NULL, "micro_wh_mp.active" => 1));

        if(!empty($micro_wh)){
            foreach ($micro_wh as $value) {
                $datetime1 = date_create(date('Y-m-d', strtotime($value['create_date']))); 
                $datetime2 = date_create(date('Y-m-d', strtotime($to_date)));
                $interval = date_diff($datetime1, $datetime2); 
                $days = $interval->days;
                if($days > 0){
                    $count += ($days/30);
                    $micro_wh_lists[] = $value;
                }
            }
        }
        
        return array('count' => sprintf("%.2f", $count), "list" => $micro_wh_lists);
    }

    /**
     * @desc: This is used to generate Partner Main invoice. 
     * @param String $partner_id
     * @param String $from_date_tmp
     * @param String $to_date_tmp
     * @return Array
     */
    function generate_partner_invoice($partner_id, $from_date_tmp, $to_date_tmp) {
        $from_date = date('Y-m-d', strtotime('-1 months', strtotime($from_date_tmp)));
        $to_date = date('Y-m-d', strtotime('+1 day', strtotime($to_date_tmp)));
        log_message("info", $from_date . "- " . $to_date);
        $result_data = $this->get_partner_invoice_data($partner_id, $from_date, $to_date, $from_date_tmp);
        $anx_data = array();
        $penalty_count = array();
        $penalty_tat = array();
        if (!empty($result_data['result'])) {
            $anx_data = $this->invoices_model->getpartner_invoices($partner_id, $from_date, $to_date, $result_data['spare_requested_data']);
            
            if(!empty($anx_data)){
                $result_data['penalty_discount'] = array();
                $penalty_data = $this->get_partner_invoice_tat_data($anx_data, $partner_id);
                if (!empty($penalty_data)) {
                    $penalty_price = (array_sum(array_column($penalty_data['tat'], 'penalty_amount')));
                    $penalty_tat = $penalty_data['tat'];
                    $penalty_count = $penalty_data['tat_count'];
                    if($penalty_price > 0){
                        $p_data = array();
                        $p_data[0]['description'] = 'Discount';
                        $p_data[0]['hsn_code'] = '';
                        $p_data[0]['qty'] = '';
                        $p_data[0]['rate'] = '';
                        $p_data[0]['gst_rate'] = DEFAULT_TAX_RATE;
                        $p_data[0]['product_or_services'] = 'Penalty Discount';
                        $p_data[0]['taxable_value'] = -sprintf("%.2f", $penalty_price);
                        $result_data['result'] = array_merge($result_data['result'], $p_data);
                    }

                }
            }
            
            $result =  $result_data['result'];
            $response = $this->_set_partner_excel_invoice_data($result,$from_date_tmp,$to_date_tmp, "Tax Invoice");

            $data['booking'] = $response['booking'];
            $data['meta'] = $response['meta'];
            $data['courier'] = $result_data['courier'];
            $data['upcountry'] = $result_data['upcountry'];
            $data['warehouse_courier'] = $result_data['warehouse_courier'];
            $data['misc'] = $result_data['misc'];
            $data['warehouse_storage_charge'] = $result_data['warehouse_storage_charge'];
            $data['final_courier'] = $result_data['final_courier'];
            $data['defective_part_by_wh'] = $result_data['defective_part_by_wh'];
            $data['packaging_rate'] = $result_data['packaging_rate'];
            $data['packaging_quantity'] = $result_data['packaging_quantity'];
            $data['annexure'] = $anx_data;
            $data['penalty_discount'] = $penalty_tat;
            $data['penalty_tat_count'] = $penalty_count;
            $data['penalty_booking_data'] = $penalty_data['penalty_booking_data'];
            $data['pickup_courier'] = $result_data['pickup_courier'];
            $data['micro_warehouse_list'] = $result_data['micro_warehouse_list'];
            $data['packaging_data'] = $result_data['packaging_data'];
          
            return $data;
        } else {
            return FALSE;
        }
    }
    
    function _set_partner_excel_invoice_data($result, $sd, $ed, $invoice_type, 
            $invoice_date = false, $is_customer = false, $customer_state =false){
        
            if(isset($result[0]['c_s_gst'])){
                $c_s_gst = $result[0]['c_s_gst'];
            } else {
                //get company detail who generated invoice
                $c_s_gst =$this->check_gst_tax_type($result[0]['state'], $customer_state);
            }
            $meta['total_qty'] = $meta['total_rate'] =  $meta['total_taxable_value'] =  
                    $meta['cgst_total_tax_amount'] = $meta['sgst_total_tax_amount'] =   $meta['igst_total_tax_amount'] =  $meta['sub_total_amount'] = 0;
            $meta['total_ins_charge'] = $meta['total_parts_charge'] =  $meta['total_parts_tax'] =  $meta['total_inst_tax'] = 0;
            $meta['igst_tax_rate'] =$meta['cgst_tax_rate'] = $meta['sgst_tax_rate'] = 0;
            $partner_on_saas = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
            $meta += $this->partner_model->get_main_partner_invoice_detail($partner_on_saas );
            
            $parts_count = 0;
            $service_count = 0;
            $meta["invoice_template"] = $this->get_invoice_tempate($result[0]['gst_number'], $is_customer, $c_s_gst);
            if($meta["invoice_template"] == "247around_Tax_Invoice_Intra_State.xlsx" || $meta["invoice_template"] == "247around_Tax_Invoice_Inter_State.xlsx"){
                $meta['main_company_logo_cell'] = _247AROUND_TAX_INVOICE_LOGO_CELL;
                $meta['main_company_seal_cell'] = _247AROUND_TAX_INVOICE_SEAL_CELL;
                $meta['main_company_sign_cell'] = _247AROUND_TAX_INVOICE_SIGN_CELL;
            }
            else{
                $meta['main_company_logo_cell'] = _247AROUND_TAX_INVOICE_LOGO_CELL;
            }
            
            foreach ($result as $key => $value) {
                if($is_customer && empty($result[0]['gst_number'])){
                  
                    $meta['total_taxable_value'] = sprintf("%1\$.2f",($value['taxable_value'] + ($value['taxable_value'] * ($value['gst_rate']/100))));
                    $result[$key]['total_amount'] = sprintf("%1\$.2f",($value['taxable_value'] + ($value['taxable_value'] * ($value['gst_rate']/100))));
                    
                    
                } else if((empty($is_customer)) && empty($result[0]['gst_number'])){
                   
                    $meta['total_taxable_value'] = sprintf("%1\$.2f",($value['taxable_value']));
                    $result[$key]['total_amount'] = sprintf("%1\$.2f",($value['taxable_value']));
                    $result[$key]['igst_rate'] =  $result[$key]['cgst_rate'] =  $result[$key]['sgst_rate'] = 0;
                    $result[$key]['cgst_tax_amount'] =   $result[$key]['sgst_tax_amount'] = $result[$key]['igst_tax_amount'] = 0;
                    
                }else if($c_s_gst){

                    $result[$key]['cgst_rate'] =  $result[$key]['sgst_rate'] = $value['gst_rate']/2;
                    $result[$key]['cgst_tax_amount'] = sprintf("%1\$.2f",($value['taxable_value'] * ($value['gst_rate']/100)/2));
                    $result[$key]['sgst_tax_amount'] = sprintf("%1\$.2f",($value['taxable_value'] * ($value['gst_rate']/100)/2));
                    $meta['cgst_total_tax_amount'] +=  $result[$key]['cgst_tax_amount'];
                    $meta['sgst_total_tax_amount'] += $result[$key]['sgst_tax_amount'];
                    $meta['sgst_tax_rate'] = $meta['cgst_tax_rate'] = $value['gst_rate']/2;
                    $meta['total_taxable_value'] += $value['taxable_value'];
                    
                    $result[$key]['total_amount'] = sprintf("%1\$.2f",($value['taxable_value'] + ($value['taxable_value'] * ($value['gst_rate']/100))));
                   
                } else {
                   
                    $result[$key]['igst_rate'] =  $meta['igst_tax_rate'] = $value['gst_rate'];
                    $result[$key]['igst_tax_amount'] = sprintf("%1\$.2f",($value['taxable_value'] * ($value['gst_rate']/100)));
                    $meta['igst_total_tax_amount'] +=  $result[$key]['igst_tax_amount'];
                    $meta['total_taxable_value'] += $value['taxable_value'];
                    
                    $result[$key]['total_amount'] = sprintf("%1\$.2f",($value['taxable_value'] + ($value['taxable_value'] * ($value['gst_rate']/100))));
                }
                
                if(empty($value['qty'])){
                    $value['qty'] = 0;
                    $result[$key]['qty'] = "";
                }

                if(empty($value['rate'])){
                    $value['rate'] = 0;
                    $result[$key]['rate'] = "";
                }
                
                $meta['total_qty'] += $value['qty'];
                $meta['total_rate'] += $value['rate'];
               
               
                $meta['sub_total_amount'] += $result[$key]['total_amount'];
                if($value['product_or_services'] == "Service"){
                    
                    $meta['total_ins_charge'] += $value['taxable_value'];
                    $service_count += $value['qty'];
                    
                } else if($value['product_or_services'] == "Product"){
                    
                    $meta['total_parts_charge'] += $value['taxable_value'];
                    $parts_count += $value['qty'];
                }
            }
            $meta['parts_count'] = $parts_count;
            $meta['service_count'] = $service_count;
            $meta['total_taxable_value'] = sprintf("%.2f",$meta['total_taxable_value']);
            $meta['sub_total_amount'] = sprintf("%.2f",$meta['sub_total_amount']);
            $meta['igst_total_tax_amount'] = sprintf("%.2f",$meta['igst_total_tax_amount']);
            $meta['cgst_total_tax_amount'] = sprintf("%.2f",$meta['cgst_total_tax_amount']);
            $meta['sgst_total_tax_amount'] = sprintf("%.2f",$meta['sgst_total_tax_amount']);
            if($result[0]['gst_number'] == 1){
                $result[0]['gst_number'] = "";
            }
            $meta['gst_number'] = $result[0]['gst_number'];
            if(!isset($result[0]['owner_phone_1'])){
                $result[0]['owner_phone_1'] = "";
            }
            $meta['owner_phone_1'] = $result[0]['owner_phone_1'];
            $meta['reverse_charge_type'] = "N";
            $meta['reference_number'] = "";
            $meta['reverse_charge'] = '';
            $meta['invoice_type'] = $invoice_type;
           
            $meta['price_inword'] = convert_number_to_words(round($meta['sub_total_amount'],0));
            if($result[0]['description'] == QC_INVOICE_DESCRIPTION){
                $meta['sd'] =  "";
                $meta['ed'] = "";
            } else {
                $meta['sd'] = date("jS M, Y", strtotime($sd));
                $meta['ed'] = date("jS M, Y", strtotime($ed));
            }
            
            if($invoice_date){
                 $meta['invoice_date'] = date("jS M, Y", strtotime($invoice_date));
            } else {
                 $meta['invoice_date'] = date("jS M, Y");
            }
           
            $meta['company_name'] = $result[0]['company_name'];
            $meta['company_address'] = $result[0]['company_address'] . ", " .
                    $result[0]['district'] . ", Pincode -" . $result[0]['pincode'] . ", " . $result[0]['state'];
            $meta['reference_invoice_id'] = "";
           
            $meta['state_code'] = $this->get_state_code(array('state' => $result[0]['state']))[0]['state_code'];
            $meta['state'] = $result[0]['state'];
            return array(
                "meta" => $meta,
                "booking" => $result
            );
    }
    
    function get_invoice_tempate($gst_number, $is_customer, $c_s_gst){
        log_message("info", __METHOD__. " Gst Number ". $gst_number. " Customer ". $is_customer. " C_S_GST ". $c_s_gst);
        if(empty($gst_number) && !empty($is_customer)){
            
            return "Customer_FOC_Bill_of_Supply.xlsx";
            
        } else if(!empty ($is_customer)){
            
            if(!empty($c_s_gst)){
                
                 return  "Customer_Tax_Invoice_Intra_State.xlsx";
                 
            } else {
                 return "Customer_Tax_Invoice_Inter_State.xlsx";  
            }
            
        }else if(!empty($c_s_gst)){
            
            return  "247around_Tax_Invoice_Intra_State.xlsx";
           
        }else {
           
             return "247around_Tax_Invoice_Inter_State.xlsx";  
        } 
            
    }
    
    /**
     * @Desc: This function is used to get brackets details of vendor
     *          whose brackets have been received successfully of particular month
     * @params: $vendor_id
     * @return: Array
     * 
     */
    function get_vendor_bracket_invoices($vendor_id, $from_date, $to_date_temp) {
        //Getting date range
        $to_date = date('Y-m-d', strtotime('+1 day', strtotime($to_date_temp)));
        $sql = 'SELECT SUM(brackets.26_32_received) as _26_32_total,
                    SUM(brackets.36_42_received) as _36_42_total,
                    SUM(brackets.total_received) as total_received,
                    sc.company_name as company_name,sc.state,sc.sc_code,
                    CONCAT(  "", GROUP_CONCAT( DISTINCT (brackets.order_id) ) ,  "" ) AS order_id,
                    brackets.order_received_from as vendor_id,
                    sc.address as company_address, sc.owner_phone_1 as owner_phone_1,
                    sc.state, gst_no as gst_number
                    
                    FROM brackets,service_centres as sc  WHERE brackets.received_date >= "' . $from_date . '" 
                    AND brackets.received_date <= "' . $to_date . '" AND brackets.is_received= "1" 
                    AND brackets.order_received_from = "' . $vendor_id . '" 
                    AND invoice_id IS NULL
                    AND sc.id = brackets.order_received_from GROUP BY brackets.order_received_from';
        $query = $this->db->query($sql);

        if ($query->num_rows > 0) {
            $result1 = $query->result_array();
            $meta = $result1[0];
            
            //get main partner detail
            $partner_on_saas = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
            $main_partner = $this->partner_model->get_main_partner_invoice_detail($partner_on_saas);
            $meta['main_company_name'] = $main_partner['main_company_name'];
            $meta['main_company_logo'] = $main_partner['main_company_logo'];
            $meta['main_company_address'] = $main_partner['main_company_address'];
            $meta['main_company_state'] = $main_partner['main_company_state'];
            $meta['main_company_pincode'] = $main_partner['main_company_pincode'];
            $meta['main_company_email'] = $main_partner['main_company_email'];
            $meta['main_company_gst_number'] = $main_partner['main_company_gst_number'];
            $meta['main_company_bank_name'] = $main_partner['main_company_bank_name'];
            $meta['main_company_bank_account'] = $main_partner['main_company_bank_account'];
            $meta['main_company_ifsc_code'] = $main_partner['main_company_ifsc_code'];
            $meta['main_company_seal'] = $main_partner['main_company_seal'];
            $meta['main_company_signature'] = $main_partner['main_company_signature'];
            
            
            $c_s_gst = $this->check_gst_tax_type($meta['state']);
            $meta['total_qty'] = $meta['total_rate'] = $meta['total_taxable_value'] = $meta['cgst_total_tax_amount'] = $meta['sgst_total_tax_amount'] = $meta['igst_total_tax_amount'] = $meta['sub_total_amount'] = 0;
            $meta['total_ins_charge'] = $meta['total_parts_charge'] = $meta['total_parts_tax'] = $meta['total_inst_tax'] = 0;
            $meta['igst_tax_rate'] = $meta['cgst_tax_rate'] = $meta['sgst_tax_rate'] = 0;


            $result = $this->get_bracket_line_item_data($meta);
            foreach ($result as $key => $value) {

                if ($c_s_gst) {
                    $meta['invoice_template'] = "247around_Tax_Invoice_Intra_State.xlsx";
                    $result[$key]['cgst_rate'] = $result[$key]['sgst_rate'] = DEFAULT_TAX_RATE/2;
                    $result[$key]['cgst_tax_amount'] = sprintf("%1\$.2f",($value['taxable_value'] * (SERVICE_TAX_RATE/2)));
                    $result[$key]['sgst_tax_amount'] = sprintf("%1\$.2f",($value['taxable_value'] * (SERVICE_TAX_RATE/2)));
                    $meta['cgst_total_tax_amount'] += $result[$key]['cgst_tax_amount'];
                    $meta['sgst_total_tax_amount'] += $result[$key]['sgst_tax_amount'];
                    $meta['sgst_tax_rate'] = $meta['cgst_tax_rate'] = DEFAULT_TAX_RATE/2;
                } else {
                    $meta['invoice_template'] = "247around_Tax_Invoice_Inter_State.xlsx";
                    $result[$key]['igst_rate'] = $meta['igst_tax_rate'] = DEFAULT_TAX_RATE;
                    $result[$key]['igst_tax_amount'] = sprintf("%1\$.2f",($value['taxable_value'] * SERVICE_TAX_RATE));
                    $meta['igst_total_tax_amount'] += $result[$key]['igst_tax_amount'];
                }

                $result[$key]['total_amount'] = sprintf("%.2f",$value['taxable_value'] + ($value['taxable_value'] * SERVICE_TAX_RATE));
                $meta['total_qty'] += $value['qty'];
                $meta['total_rate'] += $value['rate'];
                $meta['total_taxable_value'] += $value['taxable_value'];
                $meta['sub_total_amount'] += $result[$key]['total_amount'];
            }


            $meta['reverse_charge'] = 0;
            $meta['reverse_charge_type'] = 'N';
            $meta['state_code'] = $this->get_state_code(array('state'=> $meta['state']))[0]['state_code'];
            $meta['sub_total_amount'] = sprintf("%.2f",$meta['sub_total_amount']); 
            $meta['total_taxable_value'] = sprintf("%.2f",$meta['total_taxable_value']);
            $meta['sgst_total_tax_amount'] = sprintf("%.2f",$meta['sgst_total_tax_amount']);
            $meta['cgst_total_tax_amount'] = sprintf("%.2f",$meta['cgst_total_tax_amount']);
            $meta['igst_total_tax_amount'] = sprintf("%.2f",$meta['igst_total_tax_amount']);
            $meta['price_inword'] = convert_number_to_words(round($meta['sub_total_amount'],0));
            $meta['sd'] = date("jS M, Y", strtotime($from_date));
            $meta['ed'] = date("jS M, Y", strtotime($to_date_temp));
            $meta['invoice_date'] = date("jS M, Y");
            $meta['reference_invoice_id'] = "";
            $meta['invoice_type'] = "Tax Invoice";

            $data1['meta'] = $meta;
            $data1['booking'] = $result;
            return $data1;
        } else {
            return false;
        }
    }

    function get_bracket_line_item_data($meta){
        $data1 = array();
       
        $data['description'] = "Iron Stand – Less Than 32 Inches";
        $data['rate'] = _247AROUND_BRACKETS_26_32_UNIT_PRICE;
        $data['qty'] = $meta['_26_32_total'];
        $data['taxable_value'] = sprintf("%.2f",$data['rate'] * $data['qty']);
        $data['hsn_code'] = STAND_HSN_CODE;
        
        array_push($data1, $data);
        
        $data2['description'] = "Iron Stand – Greater Than 32 Inches";
        $data2['rate'] = _247AROUND_BRACKETS_36_42_UNIT_PRICE;
        $data2['qty'] = $meta['_36_42_total'];
        $data2['taxable_value'] = sprintf("%.2f",$data2['rate'] * $data2['qty']);
        $data2['hsn_code'] = STAND_HSN_CODE;
        array_push($data1, $data2);
        
        return $data1;   
    }
      
    /**
     * @desc : get all vendor invoice for previous month. it get both type A and type B invoice
     *
     * @param: void
     * @return : array
     */
    function generate_vendor_foc_detailed_invoices($vendor_id, $from_date_tmp, $to_date_tmp, $is_regenerate) {
        $from_date = date('Y-m-d', strtotime('-1 months', strtotime($from_date_tmp)));
        $to_date = date('Y-m-d', strtotime('+1 day', strtotime($to_date_tmp)));
       
        $is_invoice_null = "";
        if($is_regenerate == 0){
            $is_invoice_null =  " AND vendor_foc_invoice_id IS NULL ";
        }

        //for FOC invoice, around_to_vendor > 0 AND vendor_to_around = 0
        $where = " AND `booking_unit_details`.around_to_vendor > 0  AND `booking_unit_details`.vendor_to_around = 0 ";

        $where .= " AND pay_to_sf = '1'  $is_invoice_null  AND booking_unit_details.ud_closed_date >= '$from_date' AND booking_unit_details.ud_closed_date < '$to_date' ";
        
        $condition = "  From booking_details, booking_unit_details, services, service_centres
                          WHERE `booking_details`.booking_id = `booking_unit_details`.booking_id AND `services`.id = `booking_details`.service_id  AND `booking_details`.assigned_vendor_id = `service_centres`.id AND current_status = 'Completed' AND assigned_vendor_id = '" . $vendor_id . "' AND `booking_unit_details`.booking_status = 'Completed' $where ";

        $sql1 = "SELECT  booking_unit_details.id AS unit_id, `booking_details`.booking_id, 
                    `booking_details`.city, `booking_details`.internal_status,
		     date_format(`booking_unit_details`.`ud_closed_date`,'%d/%m/%Y') as closed_date, 
                     `booking_unit_details`.ud_closed_date as closed_booking_date, 
                      rating_stars, `booking_unit_details`.price_tags,
		     `booking_unit_details`.appliance_category, 
                     `booking_unit_details`.appliance_capacity,
                     `services`.services,
		      customer_net_payable, partner_net_payable,
		     `service_centres`.owner_phone_1,
		     `service_centres`.primary_contact_phone_1, `booking_unit_details`.  product_or_services, `booking_unit_details`.around_paid_basic_charges as around_net_payable,
		     (customer_net_payable + partner_net_payable + around_net_payable) as total_booking_charge, 
  
                     (case when (`booking_unit_details`.product_or_services = 'Service' )  THEN (round(vendor_basic_charges,2)) ELSE 0 END) as vendor_installation_charge,
                     (case when (`booking_unit_details`.product_or_services = 'Product' )  THEN (round(vendor_basic_charges,2)) ELSE 0 END) as vendor_stand

                    $condition ";

        $query1 = $this->db->query($sql1);
        return $query1->result_array();

    }
    
    function get_foc_invoice_data($vendor_id, $from_date_tmp, $to_date, $is_regenerate) {
        $from_date = date('Y-m-d', strtotime('-1 months', strtotime($from_date_tmp)));
        $is_invoice_null = "";
        if ($is_regenerate == 0) {
            $is_invoice_null = " AND vendor_foc_invoice_id IS NULL ";
        }
        $sql = "SELECT DISTINCT round((`vendor_basic_charges`),2) AS rate,product_or_services,
                sc.gst_no as gst_number, " . HSN_CODE . " AS hsn_code,
               CASE 
                WHEN MIN( ud.`appliance_capacity` ) = '' AND MAX( ud.`appliance_capacity` ) = '' THEN
                concat(services,' ', price_tags )
                
                WHEN (MIN( ud.`appliance_capacity` ) = MAX( ud.`appliance_capacity` ) )  THEN 
                concat(services,' ', price_tags,' (', 
                MAX( ud.`appliance_capacity` ),') ' )
                      
                WHEN MIN( ud.`appliance_capacity` ) = '' AND MAX( ud.`appliance_capacity` ) != '' THEN 
                concat(services,' ', price_tags,' (', 
                MAX( ud.`appliance_capacity` ),') ' )
                
                WHEN MIN( ud.`appliance_capacity` ) != '' AND MAX( ud.`appliance_capacity` ) = '' THEN
                 concat(services,' ', price_tags,' (', 
                MIN( ud.`appliance_capacity` ),') ' )
                
                ELSE 
                concat(services,' ', price_tags,' (', MIN( ud.`appliance_capacity` ),
                '-',MAX( ud.`appliance_capacity` ),') ' )
                
                
                END AS description, 
                COUNT( ud.`appliance_capacity` ) AS qty, 
                round((vendor_basic_charges * COUNT( ud.`appliance_capacity` )),2) AS  taxable_value,
                sc.state, sc.company_name,sc.address as company_address, sc_code,
                sc.primary_contact_email, sc.owner_email, sc.pan_no, contract_file, company_type,
                sc.pan_no, contract_file, company_type, signature_file, sc.owner_phone_1, sc.district, sc.pincode, is_wh,
                minimum_guarantee_charge

                FROM  `booking_unit_details` AS ud 
                JOIN booking_details as bd on (bd.booking_id = ud.booking_id)
                JOIN services ON services.id = bd.service_id
                JOIN service_centres as sc ON sc.id = bd.assigned_vendor_id
                WHERE  
                
                ud.booking_status =  'Completed'
                AND bd.assigned_vendor_id = '$vendor_id'
                AND ud.ud_closed_date >=  '$from_date'
                AND ud.ud_closed_date <  '$to_date'
                AND sc.id = bd.assigned_vendor_id
                AND  ud.around_to_vendor > 0  AND ud.vendor_to_around = 0
                AND pay_to_sf = '1'
                $is_invoice_null
                GROUP BY  `vendor_basic_charges`,ud.service_id, price_tags, product_or_services";

        $query = $this->db->query($sql);
        $result['booking'] = $query->result_array();
        //if(!empty($result['booking'])){
        $result['upcountry'] = $result['courier'] = $result['c_penalty'] = array();
        $result['d_penalty'] = $result['c_penalty'] = $result['misc'] = array();
        $result['warehouse_courier'] = $result['defective_return_to_partner'] = array();
        $result['final_courier_data'] = array();
        $result['packaging_rate'] = 0;
        $result['packaging_quantity'] = 0;
        $result['warehouse_storage_charge'] = 0;
        $result['micro_warehouse_list'] = array();
        // Calculate Upcountry booking details
        $upcountry_data = $this->upcountry_model->upcountry_foc_invoice($vendor_id, $from_date, $to_date, $is_regenerate);
        $debit_penalty = $this->penalty_model->add_penalty_in_invoice($vendor_id, $from_date, $to_date, "", $is_regenerate);
        $courier = $this->get_sf_courier_charges($vendor_id, $from_date, $to_date, $is_regenerate);
        $credit_penalty = $this->penalty_model->get_removed_penalty($vendor_id, $from_date, $to_date, "");
        $closed_date = "date_format(closed_date,'%d/%m/%Y') as closed_date";
        $misc_select = '"Misc" AS unit_id, "Completed" As internal_status,closed_date as closed_booking_date,"" As rating_stars,'
                . '"0" AS customer_net_payable, partner_charge AS partner_net_payable,"" AS around_net_payable, "" AS owner_phone_1, "" AS primary_contact_phone_1, '
                . 'booking_details.booking_id, booking_details.city, services, "" As appliance_category,'
                . '"" AS appliance_capacity, ' . $closed_date . ', description as price_tags, miscellaneous_charges.id AS misc_id, '
                . '(case when (product_or_services = "Service" )  THEN (round(vendor_basic_charges,2)) ELSE 0 END) as vendor_installation_charge, '
                . '(case when (product_or_services = "Product" )  THEN (round(vendor_basic_charges,2)) ELSE 0 END) as vendor_stand,'
                . 'vendor_basic_charges as total_booking_charge, vendor_basic_charges as amount_paid, product_or_services';
        $misc = $this->get_misc_charges_invoice_data($misc_select, "miscellaneous_charges.vendor_invoice_id IS NULL", $from_date, $to_date, "booking_details.assigned_vendor_id", $vendor_id, "vendor_basic_charges", _247AROUND_COMPLETED);

        //$warehouse_courier = $this->get_sf_invoice_warehouse_courier_data($vendor_id, $from_date, $to_date, $is_regenerate);
        //$defective_return_to_partner = $this->get_defective_parts_return_partner_sf_invoice($vendor_id, $from_date, $to_date, $is_regenerate);
        //$final_courier_data = array_merge($courier, $warehouse_courier, $defective_return_to_partner);
        $final_courier_data = $courier;
        if (!empty($upcountry_data)) {
            $up_country = array();
            $up_country[0]['description'] = 'Upcountry Charges';
            $up_country[0]['hsn_code'] = '';
            $up_country[0]['qty'] = '';
            $up_country[0]['rate'] = '';
            $up_country[0]['product_or_services'] = 'Upcountry';
            $up_country[0]['taxable_value'] = sprintf("%1\$.2f",$upcountry_data[0]['total_upcountry_price']);
            $result['booking'] = array_merge($result['booking'], $up_country);
            $result['upcountry'] = $upcountry_data;
        }

        if (!empty($final_courier_data)) {
            $c_data = array();
            $c_data[0]['description'] = 'Courier Charges';
            $c_data[0]['hsn_code'] = HSN_CODE;
            $c_data[0]['qty'] = '';
            $c_data[0]['rate'] = '';
            $c_data[0]['product_or_services'] = 'Courier';
            $c_data[0]['taxable_value'] = sprintf("%1\$.2f",(array_sum(array_column($final_courier_data, 'courier_charges_by_sf'))));
            $result['booking'] = array_merge($result['booking'], $c_data);
            //$result['defective_return_to_partner'] = $defective_return_to_partner;
            $result['courier'] = $courier;
            $result['final_courier_data'] = $final_courier_data;
        }

        if (!empty($misc)) {
            $m = array();
            $m[0]['description'] = 'Miscellaneous Charge';
            $m[0]['hsn_code'] = HSN_CODE;
            $m[0]['qty'] = '';
            $m[0]['rate'] = '';
            $m[0]['product_or_services'] = 'Misc Charge';
            $m[0]['taxable_value'] = sprintf("%1\$.2f",(array_sum(array_column($misc, 'total_booking_charge'))));
            $result['booking'] = array_merge($result['booking'], $m);
            $result['misc'] = $misc;
        }
        
//        $micro_invoice = $this->get_micro_warehoue_invoice_ledger_details($vendor_id, $from_date_tmp, $to_date);
//        if(!empty($micro_invoice) && $micro_invoice['count'] > 0){
//
//            $c_data = array();
//            $c_data[0]['description'] = MICRO_WAREHOUSE_CHARGES_DESCRIPTION;
//            $c_data[0]['hsn_code'] = HSN_CODE;
//            $c_data[0]['qty'] = $micro_invoice['count'];
//            $c_data[0]['rate'] = "";
//            $c_data[0]['gst_rate'] = DEFAULT_TAX_RATE;
//            $c_data[0]['product_or_services'] = MICRO_WAREHOUSE_CHARGES_DESCRIPTION;
//            $c_data[0]['taxable_value'] = $micro_invoice['charge'];
//            $result['booking'] = array_merge($result['booking'], $c_data);
//            $result['micro_warehouse_list'] = $micro_invoice['list'];
//                
//        }
        
//            if (!empty($warehouse_courier)) {
//                $packaging = $this->get_fixed_variable_charge(array('entity_type' => _247AROUND_SF_STRING,
//                    "entity_id" => $vendor_id, "charges_type" => PACKAGING_RATE_TAG));
//                if (!empty($packaging)) {
//                    $c_data = array();
//                    $c_data[0]['description'] = $packaging[0]['description'];
//                    $c_data[0]['hsn_code'] = $packaging[0]['hsn_code'];
//                    $c_data[0]['qty'] = count($warehouse_courier);
//                    $c_data[0]['rate'] = $packaging[0]['fixed_charges'];
//                    $c_data[0]['gst_rate'] = $packaging[0]['gst_rate'];
//                    $c_data[0]['product_or_services'] = $packaging[0]['description'];
//                    $c_data[0]['taxable_value'] = $c_data[0]['qty'] * $packaging[0]['fixed_charges'];
//                    $result['booking'] = array_merge($result['booking'], $c_data);
//                    $result['warehouse_courier'] = $warehouse_courier;
//                    $result['packaging_rate'] = $packaging[0]['fixed_charges'];
//                    $result['packaging_quantity'] = count($warehouse_courier);
//                }
//            }
        if (!empty($credit_penalty)) {
                $cp_data = array();
                $cp_data[0]['description'] = 'Credit (Penalty Removed)';
                $cp_data[0]['hsn_code'] = HSN_CODE;
                $cp_data[0]['qty'] = '';
                $cp_data[0]['rate'] = '';
                $cp_data[0]['product_or_services'] = 'Credit Penalty';
                $cp_data[0]['taxable_value'] = sprintf("%1\$.2f",(array_sum(array_column($credit_penalty, 'p_amount'))));
                $result['booking'] = array_merge($result['booking'], $cp_data);
                $result['c_penalty'] = $credit_penalty;
        }
        
        if (!empty($debit_penalty)) {
            $d_penalty = array();
            $d_penalty[0]['description'] = 'Discount (Bookings not updated)';
            $d_penalty[0]['hsn_code'] = HSN_CODE;
            $d_penalty[0]['qty'] = '';
            $d_penalty[0]['rate'] = '';
            $d_penalty[0]['product_or_services'] = 'Debit Penalty';
            $d_penalty[0]['taxable_value'] = -sprintf("%1\$.2f",(array_sum(array_column($debit_penalty, 'p_amount'))));
            $result['booking'] = array_merge($result['booking'], $d_penalty);
            $result['d_penalty'] = $debit_penalty;
        }
        
        if (!empty($result['booking'])) {
            if (!isset($result['booking'][0]['company_name'])) {
                $select = 'state,company_name,'
                        . ' address as company_address, pincode, district,'
                        . ' is_wh, owner_phone_1, sc_code, primary_contact_email,'
                        . ' owner_email, pan_no, contract_file, company_type, signature_file, gst_no as gst_number,minimum_guarantee_charge ';

                $vendor_details = $this->vendor_model->getVendorDetails($select, array('id' => $vendor_id));

                $result['booking'][0]['company_name'] = $vendor_details[0]['company_name'];
                $result['booking'][0]['company_address'] = $vendor_details[0]['company_address'];
                $result['booking'][0]['pincode'] = $vendor_details[0]['pincode'];
                $result['booking'][0]['district'] = $vendor_details[0]['district'];
                $result['booking'][0]['is_wh'] = $vendor_details[0]['is_wh'];
                $result['booking'][0]['owner_phone_1'] = $vendor_details[0]['owner_phone_1'];
                $result['booking'][0]['sc_code'] = $vendor_details[0]['sc_code'];
                $result['booking'][0]['state'] = $vendor_details[0]['state'];
                $result['booking'][0]['primary_contact_email'] = $vendor_details[0]['primary_contact_email'];
                $result['booking'][0]['owner_email'] = $vendor_details[0]['owner_email'];
                $result['booking'][0]['pan_no'] = $vendor_details[0]['pan_no'];
                $result['booking'][0]['contract_file'] = $vendor_details[0]['contract_file'];
                $result['booking'][0]['company_type'] = $vendor_details[0]['signature_file'];
                $result['booking'][0]['signature_file'] = $vendor_details[0]['company_type'];
                $result['booking'][0]['gst_number'] = $vendor_details[0]['gst_number'];
                $result['booking'][0]['minimum_guarantee_charge'] = $vendor_details[0]['minimum_guarantee_charge'];
            }
            
//            if ($result['booking'][0]['is_wh'] == 1) {
//                $packaging1 = $this->get_fixed_variable_charge(array('entity_type' => _247AROUND_SF_STRING,
//                    "entity_id" => $vendor_id, "charges_type" => FIXED_MONTHLY_WAREHOUSE_CHARGES_TAG));
//                if (!empty($packaging1)) {
//                    $c_data = array();
//                    $c_data[0]['description'] = $packaging1[0]['description'];
//                    $c_data[0]['hsn_code'] = $packaging1[0]['hsn_code'];
//                    $c_data[0]['qty'] = 0;
//                    $c_data[0]['rate'] = $packaging1[0]['fixed_charges'];
//                    $c_data[0]['gst_rate'] = $packaging1[0]['gst_rate'];
//                    $c_data[0]['product_or_services'] = $packaging1[0]['description'];
//                    $c_data[0]['taxable_value'] = $packaging1[0]['fixed_charges'];
//                    $result['booking'] = array_merge($result['booking'], $c_data);
//                    $result['warehouse_storage_charge'] = $packaging1[0]['fixed_charges'];
//                }
//            }
            // we have no gst number then we generte bill of supply.
            // IF we have GSt number then check it is valid or not. 
            // We are creating invoice only for valid GST Number.
            if (!empty($result['booking'][0]['gst_number'])) {
                $gst = $this->invoice_lib->check_gst_number_valid($vendor_id, $result['booking'][0]['gst_number']);
               
                if(!empty($gst)){
                   if($gst['status'] == true && $gst['gst_type'] == true ){
                       return $result;
                   } else if($gst['status'] == true && $gst['gst_type'] == FALSE ){
                       $result['booking'][0]['gst_number'] = "";
                       return $result;
                   } else {
                       $this->session->set_userdata(array('error' => "GST Number is Invalid"));
                       return FALSE;
                   }
                } else {
                    // IF we are getting false as response then we are not creating invoice 
                    log_message("info", __METHOD__. " GST Number Invalid for Vendor ID ". $vendor_id);
                    $this->session->set_userdata(array('error' => "GST Number is Invalid"));
                    return FALSE;
                }
            } else {
                return $result;
            }
        } else {
            log_message("info", __METHOD__. " DATA Not Found vendor ID ". $vendor_id);

            $this->session->set_userdata(array('error' => "Data Not Found"));
            return FALSE;
        }
    }
    /**
     * @desc This function is used to get micro warehouse invoice data
     * It will create full amount invoice when Micro warehouse created before requested from date
     * It will create partial amount invoice when Micro invoice created with in requested Date
     * @param int $vendor_id
     * @param String $from_date
     * @param String $to_date
     * @return Array
     */
    function get_micro_warehoue_invoice_ledger_details($vendor_id, $from_date, $to_date){
        $micro_wh_lists = $this->inventory_model->get_micro_wh_lists_by_partner_id("micro_wh_mp.*, service_centres.company_name", 
                array('micro_wh_mp.vendor_id' => $vendor_id, 'micro_wh_mp.create_date < "'.$from_date.'" ' => NULL, "micro_wh_mp.active" => 1));
        $count = 0;
        $charge = 0;
        if(!empty($micro_wh_lists)){
            $count = count($micro_wh_lists);
            $charge = (array_sum(array_column($micro_wh_lists, 'micro_warehouse_charges')));
        } 
        
        $micro_wh = $this->inventory_model->get_micro_wh_lists_by_partner_id("micro_wh_mp.*, service_centres.company_name", array('micro_wh_mp.vendor_id' => $vendor_id, 
            'micro_wh_mp.create_date >= "'.$from_date.'" ' => NULL, "micro_wh_mp.create_date <= '".$to_date."' " => NULL, "micro_wh_mp.active" => 1));

        if(!empty($micro_wh)){
            foreach ($micro_wh as $value) {
                $datetime1 = date_create(date('Y-m-d', strtotime($value['create_date']))); 
                $datetime2 = date_create(date('Y-m-d', strtotime($to_date)));
                $interval = date_diff($datetime1, $datetime2); 
                $days = $interval->days;
                if($days > 0){
                    $count += ($days/30);
                    $charge += (($days/30) * $value['micro_warehouse_charges']); 
                    $micro_wh_lists[] = $value;
                }
            }
        }
        
        return array('count' => sprintf("%.2f", $count), "list" => $micro_wh_lists, "charge" => sprintf("%.2f", $charge));
    }

    /**
     * @desc: This is used to get Main Foc invoice data
     * @param String $vendor_id
     * @param String $from_date
     * @param String $to_date
     * @return boolean
     */
    function get_vendor_foc_invoice($vendor_id, $from_date, $to_date_tmp, $is_regenerate) {

        $to_date = date('Y-m-d', strtotime('+1 day', strtotime($to_date_tmp)));
        $data = $this->get_foc_invoice_data($vendor_id, $from_date, $to_date, $is_regenerate);
        if (!empty($data['booking'])) {
            
            $meta['total_qty'] = $meta['total_rate'] =  $meta['total_taxable_value'] =  
            $meta['cgst_total_tax_amount'] = $meta['sgst_total_tax_amount'] =   $meta['igst_total_tax_amount'] =  $meta['sub_total_amount'] = 0;
            $meta['total_sc_charge'] = $meta['total_parts_charge'] =  $meta['total_parts_tax'] =  $meta['total_inst_tax'] = 0;
            $meta['igst_tax_rate'] =$meta['cgst_tax_rate'] = $meta['sgst_tax_rate'] = 0;
            
            $c_s_gst =$this->check_gst_tax_type($data['booking'][0]['state']);
            $parts_count = 0;
            $service_count = 0;
             foreach ($data['booking'] as $key => $value) {
                if(empty($data['booking'][0]['gst_number'])){
                    
                    $meta['invoice_template'] = "SF_FOC_Bill_of_Supply-v1.xlsx";
                    $data['booking'][$key]['total_amount'] =sprintf("%1\$.2f",($value['taxable_value']));
                    
                } else if($c_s_gst){
                    $meta['invoice_template'] = "SF_FOC_Tax_Invoice-Intra_State-v1.xlsx";
                    
                    $data['booking'][$key]['cgst_rate'] =  $data['booking'][$key]['sgst_rate'] = 9;
                    $data['booking'][$key]['cgst_tax_amount'] = sprintf("%1\$.2f",($value['taxable_value'] * 0.09));
                    $data['booking'][$key]['sgst_tax_amount'] = sprintf("%1\$.2f",($value['taxable_value'] * 0.09));
                    $meta['cgst_total_tax_amount'] +=  $data['booking'][$key]['cgst_tax_amount'];
                    $meta['sgst_total_tax_amount'] += $data['booking'][$key]['sgst_tax_amount'];
                    $meta['sgst_tax_rate'] = $meta['cgst_tax_rate'] = 9;
                    $data['booking'][$key]['total_amount'] = sprintf("%1\$.2f",($value['taxable_value'] + ($value['taxable_value'] * 0.18)));
                    
                } else {
                    $meta['invoice_template'] = "SF_FOC_Tax_Invoice_Inter_State_v1.xlsx";
                    
                    $data['booking'][$key]['igst_rate'] =  $meta['igst_tax_rate'] = DEFAULT_TAX_RATE;
                    $data['booking'][$key]['igst_tax_amount'] = sprintf("%1\$.2f",($value['taxable_value'] * 0.18));
                    $meta['igst_total_tax_amount'] +=  $data['booking'][$key]['igst_tax_amount'];
                    $data['booking'][$key]['total_amount'] = sprintf("%1\$.2f",( $value['taxable_value'] + ($value['taxable_value'] * 0.18)));
                }
                if(empty($value['qty'])){
                    $value['qty'] = 0;
                    $data['booking'][$key]['qty'] = "";
                }
                
                if(empty($value['rate'])){
                    $value['rate'] = 0;
                    $data['booking'][$key]['rate'] = "";
                }
                
                $meta['total_qty'] += $value['qty'];
                
                $meta['total_rate'] += $value['rate'];
                $meta['total_taxable_value'] += $value['taxable_value'];
                $meta['sub_total_amount'] +=  $data['booking'][$key]['total_amount'];
                
                if($value['product_or_services'] == "Product"){
                    
                    $meta['total_parts_charge'] += $value['taxable_value'];
                    $parts_count += $value['qty'];
                    
                } else {
                    $meta['total_sc_charge'] += $value['taxable_value'];
                    if($value['product_or_services'] == "Service"){
                        $service_count += $value['qty'];
                    }
                }
             }
            $meta['rcm'] = 0;
            if(empty($data['booking'][0]['gst_number'])){
                $meta['rcm'] = sprintf("%1\$.2f",( $meta['sub_total_amount'] * 0.18));
            }
            $meta['parts_count'] = $parts_count;
            $meta['service_count'] = $service_count;
            $meta['reverse_charge'] = 0;
            $meta['reverse_charge_type'] = 'N';
            $meta['total_taxable_value'] = sprintf("%1\$.2f",$meta['total_taxable_value']);
            $meta['cgst_total_tax_amount'] = sprintf("%1\$.2f",$meta['cgst_total_tax_amount']);
            $meta['sgst_total_tax_amount'] = sprintf("%1\$.2f",$meta['sgst_total_tax_amount']);
            $meta['igst_total_tax_amount'] = sprintf("%1\$.2f",$meta['igst_total_tax_amount']);
            $meta['sub_total_amount'] = sprintf("%.2f",$meta['sub_total_amount']);
            $meta['sd'] = date("jS M, Y", strtotime($from_date));
            $meta['ed'] = date("jS M, Y", strtotime($to_date_tmp));
            $meta['invoice_date'] = date("jS M, Y");
            $meta['company_name'] = $meta['vendor_name'] = $data['booking'][0]['company_name'];
            $meta['company_address'] = $meta['vendor_address'] = $data['booking'][0]['company_address'] . "," 
                    . $data['booking'][0]['district'] . "," . $data['booking'][0]['state'] . ", Pincode: "
                    . $data['booking'][0]['pincode'];
            $meta['reference_invoice_id'] = "";
            $meta['gst_number'] = $data['booking'][0]['gst_number'];
            $meta['sc_code'] = $data['booking'][0]['sc_code'];
            $meta['owner_email'] =  $data['booking'][0]['owner_email'];
            $meta['primary_contact_email'] =  $data['booking'][0]['primary_contact_email'];
            $bankDetails = $this->reusable_model->get_search_result_data("account_holders_bank_details","*",array('entity_id'=>$vendor_id,'entity_type'=>"SF",'is_active'=>1),NULL,NULL,NULL,NULL,NULL);
            if(empty($bankDetails[0])){
           $bankDetails[0]['beneficiary_name'] = '';
           $bankDetails[0]['bank_account'] = '';
           $bankDetails[0]['bank_name'] = '';
           $bankDetails[0]['ifsc_code'] = '';
            }
            $meta['beneficiary_name'] = $bankDetails[0]['beneficiary_name'];
            $meta['bank_account'] = $bankDetails[0]['bank_account'];
            $meta['bank_name'] = $bankDetails[0]['bank_name'];
            $meta['ifsc_code'] = $bankDetails[0]['ifsc_code'];
            $meta['owner_phone_1'] = $data['booking'][0]['owner_phone_1'];
//            if(!empty($data['booking'][0]['signature_file'])){
//                $path1  = TMP_FOLDER.$data['booking'][0]['signature_file'];
//                $cmd = "curl https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/" .$data['booking'][0]['signature_file'] . " -o " . $path1;
//                exec($cmd);
//                
//                $meta['sign_path'] = $path1;
//                $meta['cell'] = "K".(26 + count($data['booking']));
//            }
           
            if ($meta['sub_total_amount'] >= 0) {
               
                $meta['price_inword'] = convert_number_to_words(round($meta['sub_total_amount'],0));
            }
            
            $data['meta'] = $meta;

            return $data;
        } else {
            return FALSE;
        }
    }
    
   
    
    function get_vendor_cash_detailed($vendor_id, $from_date_tmp, $to_date_tmp, $is_regenerate) {
        $from_date = date('Y-m-d', strtotime('-1 months', strtotime($from_date_tmp)));
        $to_date = date('Y-m-d', strtotime('+1 day', strtotime($to_date_tmp)));
        $where = "";
        for ($i = 0; $i < 2; $i++) {
            if ($i == 0) {
                $select = "(around_comm_basic_charges + around_st_or_vat_basic_charges) as service_charges, ";
                $where = " AND ( ( `booking_unit_details`.vendor_to_around > 0 AND `booking_unit_details`.around_to_vendor =0 ) OR ( `booking_unit_details`.vendor_to_around = 0 AND `booking_unit_details`.around_to_vendor =0 ) )  ";
            } else {
                $select = "0.00 As service_charges,";
                $where = " AND  booking_unit_details.around_to_vendor > 0  AND booking_unit_details.vendor_to_around = 0 AND (booking_unit_details.customer_paid_extra_charges > 0 OR booking_unit_details.customer_paid_parts > 0) ";
            }
            $where .= " AND booking_unit_details.ud_closed_date >= '$from_date' AND booking_unit_details.ud_closed_date < '$to_date' ";
            $is_invoice_null = "";
            if($is_regenerate == 0){
                $is_invoice_null = " AND vendor_cash_invoice_id IS NULL ";
            }

            $sql = "SELECT booking_unit_details.id AS unit_id, "
                    . "`booking_details`.booking_id, "
                    . "`booking_details`.city,"
                    . " date_format(`booking_unit_details`.`ud_closed_date`,'%d/%m/%Y') as closed_date,"
                    . "`booking_unit_details`.ud_closed_date as closed_booking_date, "
                    . " `booking_unit_details`.price_tags, "
                    . "`booking_unit_details`.appliance_category,"
                    . "rating_stars, "
                    . " `booking_unit_details`.appliance_capacity, 
                     `booking_unit_details`.  product_or_services, services,"
                    . " around_net_payable, "
                    . " $select (around_comm_extra_charges + around_st_extra_charges) as additional_charges,"
                    . " (around_comm_parts + around_st_parts) AS parts_cost, "
                    . " (customer_paid_basic_charges + customer_paid_extra_charges + customer_paid_parts) as amount_paid  "
                    . " From booking_details, booking_unit_details, services, service_centres
                    WHERE `booking_details`.booking_id = `booking_unit_details`.booking_id AND `services`.id = `booking_details`.service_id  
                    AND `booking_details`.assigned_vendor_id = `service_centres`.id AND current_status = 'Completed' AND pay_from_sf = 1
                    $is_invoice_null
                    AND assigned_vendor_id = '" . $vendor_id . "' "
                    . " AND `booking_unit_details`.booking_status = 'Completed' $where";


            $query = $this->db->query($sql);
            $invoice[$i] = $query->result_array();
        }
        $result = array_merge($invoice[0], $invoice[1]);
            
       return $result;
    }
    
    function get_vendor_cash_invoice_data($vendor_id, $from_date_tmp, $to_date, $is_regenerate) {
        $from_date = date('Y-m-d', strtotime('-1 months', strtotime($from_date_tmp)));
        for ($i = 0; $i < 2; $i++) {
            if ($i == 0) {
                $select = "SUM(`around_comm_basic_charges` + `around_st_or_vat_basic_charges` "
                        . "+ `around_comm_extra_charges` + `around_st_extra_charges` + `around_comm_parts`  + `around_st_parts`)  AS total_amount, ";
                $where = " AND ( ( ud.vendor_to_around > 0 AND ud.around_to_vendor =0 ) OR (ud.vendor_to_around = 0 AND ud.around_to_vendor =0 ) )  ";
            } else {
                $select = "SUM(`around_comm_extra_charges` + `around_st_extra_charges` + `around_comm_parts`  + `around_st_parts`) As total_amount,";
                $where = " AND  around_to_vendor > 0  AND vendor_to_around = 0 AND (ud.customer_paid_extra_charges > 0 OR ud.customer_paid_parts > 0) ";
            }
            $is_foc_null = "";
            if ($is_regenerate == 0) {
                $is_foc_null = " AND vendor_cash_invoice_id IS NULL ";
            }
            $sql = "SELECT  
                $select
                sc.gst_no as gst_number, sc.state, sc.company_name,sc.address as company_address,
                sc.primary_contact_email, sc.owner_email, 
                sc.owner_phone_1, sc.primary_contact_phone_1
                FROM  `booking_unit_details` AS ud, services, booking_details AS bd, service_centres as sc
                WHERE ud.booking_status =  'Completed'
                AND ud.booking_id = bd.booking_id
                AND bd.assigned_vendor_id = '$vendor_id'
                AND ud.ud_closed_date >=  '$from_date'
                AND ud.ud_closed_date <  '$to_date'
                AND ud.service_id = services.id
                AND pay_from_sf = 1
                AND sc.id = bd.assigned_vendor_id
                $is_foc_null
                $where
                ";

            $query = $this->db->query($sql);
            $result[$i] = $query->result_array();
        }
        $data = array();
        
        if (!empty($result[0][0]['total_amount']) && !empty($result[1][0]['total_amount'])) {
            $data = array_merge($result[0], $result[1]);
        } else if (!empty($result[0][0]['total_amount']) && empty($result[1][0]['total_amount'])) {
            $data = $result[0];
        } else if (empty($result[0][0]['total_amount']) && !empty($result[1][0]['total_amount'])) {
            $data = $result[1];
        }

        return $data;
    }

    /**
     * @desc: This method is used to get Main Cash Invoice Data
     * @param String $vendor_id
     * @param String $from_date
     * @param String $to_date_tmp
     * @return boolean
     */
    function get_vendor_cash_invoice($vendor_id, $from_date, $to_date_tmp, $is_regenerate) {
        log_message("info", __METHOD__);
        $to_date = date('Y-m-d', strtotime('+1 day', strtotime($to_date_tmp)));
        $data = $this->get_vendor_cash_invoice_data($vendor_id, $from_date, $to_date, $is_regenerate);
        if (!empty($data)) {
            $commission_charge = array();
            $meta = $data[0];
            //get company detail who generated invoice
            $partner_on_saas = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
            $meta += $this->partner_model->get_main_partner_invoice_detail($partner_on_saas);
            
            $commission_charge[0]['description'] = "Commission Charge";
            $total_amount_invoice = (array_sum(array_column($data, 'total_amount')));
            if ($total_amount_invoice > 0) {
                $commission_charge[0]['total_amount'] = $total_amount_invoice;

                $meta['upcountry_charge'] = $meta['upcountry_booking'] = $meta['upcountry_distance'] = $meta['total_sgst_tax_amount'] = $meta['total_cgst_tax_amount'] = $meta['total_igst_tax_amount'] = $meta['igst_tax_rate'] = $meta['sgst_tax_rate'] = $meta['sgst_tax_rate'] = 0;
                $from_date_tmp = date('Y-m-d', strtotime('-1 months', strtotime($from_date)));
                $upcountry_data = $this->upcountry_model->upcountry_cash_invoice($vendor_id, $from_date_tmp, $to_date, $is_regenerate);
                if (!empty($upcountry_data)) {
                    $commission_charge[0]['total_amount'] += $upcountry_data[0]['total_upcountry_price'];
                    $meta['upcountry_charge'] = $upcountry_data[0]['total_upcountry_price'];
                    $meta['upcountry_booking'] = $upcountry_data[0]['total_booking'];
                    $meta['upcountry_distance'] = $upcountry_data[0]['total_distance'];
                }

                $tax_charge = $this->booking_model->get_calculated_tax_charge($commission_charge[0]['total_amount'], DEFAULT_TAX_RATE);
                $commission_charge[0]['taxable_value'] = sprintf("%.2f",$commission_charge[0]['total_amount'] - $tax_charge);
                $c_s_gst = $this->check_gst_tax_type($meta['state']);
                $meta['cgst_tax_rate'] = $meta['sgst_tax_rate'] = $meta['cgst_total_tax_amount'] = $meta['sgst_total_tax_amount'] = $meta['total_igst_tax_amount'] = $meta['igst_tax_rate'] = $meta['igst_total_tax_amount'] = 0;
                if ($c_s_gst) {
                    $meta['invoice_template'] = "247around_Tax_Invoice_Intra_State.xlsx";
                    $commission_charge[0]['cgst_rate'] = $commission_charge[0]['sgst_rate'] = $meta['sgst_tax_rate'] = $meta['cgst_tax_rate'] = 9;
                    $commission_charge[0]['cgst_tax_amount'] = $commission_charge[0]['sgst_tax_amount'] = $meta['cgst_total_tax_amount'] = $meta['sgst_total_tax_amount'] = sprintf("%.2f",$tax_charge / 2);
                } else {
                    $meta['invoice_template'] = "247around_Tax_Invoice_Inter_State.xlsx";
                    $commission_charge[0]['igst_tax_amount'] = $meta['igst_total_tax_amount'] = sprintf("%.2f",$tax_charge);
                    $commission_charge[0]['igst_rate'] = $meta['igst_tax_rate'] = DEFAULT_TAX_RATE;
                }

                $meta['reverse_charge_type'] = "N";
                $meta['reverse_charge'] = '';
                $meta['invoice_type'] = 'Tax Invoice';

                $meta['total_qty'] = $meta['total_rate'] = $commission_charge[0]['qty'] = $commission_charge[0]['rate'] = "";
                $commission_charge[0]['hsn_code'] = COMMISION_CHARGE_HSN_CODE;
                $meta['total_taxable_value'] = $commission_charge[0]['taxable_value'];
                $meta['sub_total_amount'] = sprintf("%.2f",$commission_charge[0]['total_amount']);

                $meta['price_inword'] = convert_number_to_words(round($meta['sub_total_amount'],0));
                $meta['sd'] = date("jS M, Y", strtotime($from_date));
                $meta['ed'] = date('jS M, Y', strtotime($to_date_tmp));
                $meta['invoice_date'] = date("jS M, Y");
                $meta['reference_invoice_id'] = "";
                $meta['state_code'] = $this->get_state_code(array('state' => $meta['state']))[0]['state_code'];
                $r_data['booking'] = $commission_charge;
                $r_data['meta'] = $meta;
                $r_data['upcountry'] = $upcountry_data;

                return $r_data;
            } else {
                $this->session->set_userdata(array('error' => "Invoice amount is not greater than zero"));
                return FALSE;
            }
        } else {
            $this->session->set_userdata(array('error' => "Data Not Found"));
            return FALSE;
        }
    }

    /**
     * @desc Generate Buyback Invoice
     * @param int $vendor_id SF id
     * @param String $from_date Start Date
     * @param String $to_date_tmp End date
     * @param int $is_regenerate 0 means regenerate false, 1 means regenerate true 
     * @param int $profit_loss 0 means for loss and 1 means for profit
     * @return boolean
     */
    function get_buyback_invoice_data($vendor_id, $from_date, $to_date_tmp, $is_regenerate, $profit_loss){
        log_message("info", __METHOD__);
        $to_date = date('Y-m-d', strtotime('+1 day', strtotime($to_date_tmp)));
        $annexure_data = $this->_buyback_invoice_query($vendor_id, $from_date, $to_date, $is_regenerate, true, $profit_loss);
       
        $commission_charge = $this->_buyback_invoice_query($vendor_id, $from_date, $to_date, $is_regenerate, false, $profit_loss);

        $meta['sub_total_amount'] = $meta['total_qty'] = 0;
       

        if(!empty($commission_charge)){
            $is_buyback_gst = $commission_charge[0]['is_buyback_gst_invoice'];
            $meta['total_qty'] = $meta['total_rate'] =  $meta['total_taxable_value'] =  
            $meta['cgst_total_tax_amount'] = $meta['sgst_total_tax_amount'] =   $meta['igst_total_tax_amount'] =  $meta['sub_total_amount'] = 0;
            $meta['igst_tax_rate'] =$meta['cgst_tax_rate'] = $meta['sgst_tax_rate'] = 0;
            
            $c_s_gst =$this->check_gst_tax_type($commission_charge[0]['state']);
            
            foreach ($commission_charge as $key => $value) {
                $is_bill_of_supply = TRUE;
                if(!empty($commission_charge[0]['gst_no']) && !empty($is_buyback_gst)){
                    $is_bill_of_supply = FALSE;
                }
                if($is_bill_of_supply){
                    $commission_charge[$key]['rate'] = sprintf("%.2f",$value['taxable_value']/$value['qty']);
                    $commission_charge[$key]['total_amount'] = $value['taxable_value'];
                    $meta['sub_total_amount'] += $value['taxable_value'];
                    $meta['total_qty'] += $value['qty'];
                    $meta['invoice_template'] = "Buyback-v1.xlsx"; 
                } else {
                   $commission_charge[$key]['rate'] = sprintf("%.2f",$value['taxable_value']/$value['qty']);
                   if($c_s_gst){
                        $meta['invoice_template'] = "247around_Tax_Invoice_Intra_State.xlsx";

                        $commission_charge[$key]['cgst_rate'] =  $commission_charge[$key]['sgst_rate'] = 9;
                        $commission_charge[$key]['cgst_tax_amount'] = sprintf("%1\$.2f",($value['taxable_value'] * (SERVICE_TAX_RATE/2)));
                        $commission_charge['sgst_tax_amount'] = sprintf("%1\$.2f",($value['taxable_value'] * (SERVICE_TAX_RATE/2)));
                        $meta['cgst_total_tax_amount'] +=  $commission_charge[$key]['cgst_tax_amount'];
                        $meta['sgst_total_tax_amount'] += $commission_charge[$key]['sgst_tax_amount'];
                        $meta['sgst_tax_rate'] = $meta['cgst_tax_rate'] = DEFAULT_TAX_RATE/2;
                        $commission_charge[$key]['total_amount'] = sprintf("%1\$.2f",($value['taxable_value'] + ($value['taxable_value'] *(SERVICE_TAX_RATE))));

                    } else {
                        $meta['invoice_template'] = "247around_Tax_Invoice_Inter_State.xlsx";

                        $commission_charge[$key]['igst_rate'] =  $meta['igst_tax_rate'] = DEFAULT_TAX_RATE;
                        $commission_charge[$key]['igst_tax_amount'] = sprintf("%1\$.2f",($value['taxable_value'] * SERVICE_TAX_RATE));
                        $meta['igst_total_tax_amount'] +=  $commission_charge[$key]['igst_tax_amount'];
                        $commission_charge[$key]['total_amount'] = sprintf("%1\$.2f",( $value['taxable_value'] + ($value['taxable_value'] * SERVICE_TAX_RATE)));
                    }
                    $meta['total_qty'] += $value['qty'];
                
                    $meta['total_rate'] += $commission_charge[$key]['rate'];
                    $meta['total_taxable_value'] += $value['taxable_value'];
                    $meta['sub_total_amount'] +=  $commission_charge[$key]['total_amount'];
                    $meta['invoice_type'] = "Tax Invoice";
                }
                
            }
            
            $meta['sub_total_amount'] = round(sprintf("%.2f",$meta['sub_total_amount']),0);
            
            $meta['total_taxable_value'] = sprintf("%1\$.2f",$meta['total_taxable_value']);
            $meta['cgst_total_tax_amount'] = sprintf("%1\$.2f",$meta['cgst_total_tax_amount']);
            $meta['sgst_total_tax_amount'] = sprintf("%1\$.2f",$meta['sgst_total_tax_amount']);
            $meta['igst_total_tax_amount'] = sprintf("%1\$.2f",$meta['igst_total_tax_amount']);
            
            
            $meta['sd'] = date("jS M, Y", strtotime($from_date));
            $meta['ed'] = date('jS M, Y', strtotime($to_date_tmp));
            $meta['invoice_date'] = date("jS M, Y");
            $meta['reference_invoice_id'] = "";
            $meta['price_inword'] = convert_number_to_words(round($meta['sub_total_amount'],0));
            $meta['company_name'] = $commission_charge[0]['company_name'];
            $meta['company_address'] = $commission_charge[0]['company_address'];
            $meta['state'] = $commission_charge[0]['state'];
            $meta['state_code'] = $this->get_state_code(array('state'=> $commission_charge[0]['state']))[0]['state_code'];
            $meta['gst_number'] = $commission_charge[0]['gst_no'];
            $meta['owner_email'] = $commission_charge[0]['owner_email'];
            $meta['primary_contact_email'] = $commission_charge[0]['primary_contact_email'];
            $meta['owner_phone_1'] = $commission_charge[0]['owner_phone_1'];
            
            //get main partner detail
            $partner_on_saas = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
            $main_partner = $this->partner_model->get_main_partner_invoice_detail($partner_on_saas);
            $meta['main_company_name'] = $main_partner['main_company_name'];
            $meta['main_company_logo'] = $main_partner['main_company_logo'];
            $meta['main_company_address'] = $main_partner['main_company_address'];
            $meta['main_company_state'] = $main_partner['main_company_state'];
            $meta['main_company_pincode'] = $main_partner['main_company_pincode'];
            $meta['main_company_email'] = $main_partner['main_company_email'];
            $meta['main_company_gst_number'] = $main_partner['main_company_gst_number'];
            $meta['main_company_bank_name'] = $main_partner['main_company_bank_name'];
            $meta['main_company_bank_account'] = $main_partner['main_company_bank_account'];
            $meta['main_company_ifsc_code'] = $main_partner['main_company_ifsc_code'];
            $meta['main_company_seal'] = $main_partner['main_company_seal'];
            $meta['main_company_signature'] = $main_partner['main_company_signature'];
            
            $data1['meta'] = $meta;
            $data1['booking'] = $commission_charge;
            $data1['annexure_data'] = $annexure_data;
            
            return $data1;
            
        } else{
            $this->session->set_userdata(array('error' => "Data Not Found"));
            return FALSE;
        }
    }
    
    function _buyback_invoice_query($vendor_id, $from_date_tmp, $to_date, $is_regenerate, $is_unit, $profitLoss){
        $from_date = date('Y-m-d', strtotime('-3 months', strtotime($from_date_tmp)));
        $is_foc_null = "";
        if ($is_regenerate == 0) {
                $is_foc_null = " AND cp_invoice_id IS NULL ";
        }
        if($profitLoss == 1){
            $profit_loss_where = ' AND CASE WHEN (cp_claimed_price > 0) THEN ((`partner_basic_charge` + `partner_tax_charge` - partner_discount ) <=  (cp_claimed_price)) ELSE ((`partner_basic_charge` + `partner_tax_charge` - partner_discount) <=  (`cp_basic_charge` + cp_tax_charge)) END ';
        } else {
            $profit_loss_where = ' AND CASE WHEN (cp_claimed_price > 0) THEN ((`partner_basic_charge` + `partner_tax_charge` - partner_discount ) >  (cp_claimed_price)) ELSE ((`partner_basic_charge` + `partner_tax_charge` - partner_discount) >  (`cp_basic_charge` + cp_tax_charge)) END ';
        }
        
        
        if($is_unit){
            $select = " bb_unit_details.id AS unit_id,bb_unit_details.gst_amount, CASE WHEN ( bb_unit_details.cp_claimed_price > 0) 
                THEN (round(bb_unit_details.cp_claimed_price,2)) 
                ELSE (round(bb_unit_details.cp_basic_charge + cp_tax_charge,2)) END AS cp_charge,partner_tracking_id, city,order_key,
                CASE WHEN(acknowledge_date IS NOT NULL) 
                THEN (DATE_FORMAT( acknowledge_date,  '%d-%m-%Y' ) ) ELSE (DATE_FORMAT(delivery_date,  '%d-%m-%Y' )) END AS delivery_date, order_date,
                order_date, services, bb_order_details.partner_order_id";
            $group_by = "";
            
        } else {
            
            $select = " COUNT(bb_unit_details.id) as qty, SUM(CASE WHEN ( bb_unit_details.cp_claimed_price > 0) 
                THEN (round(bb_unit_details.cp_claimed_price,2)) 
                ELSE (round(bb_unit_details.cp_basic_charge + cp_tax_charge,2)) END ) AS taxable_value, concat('Used ',services) as description, 
                CASE WHEN (bb_unit_details.service_id = '"._247AROUND_TV_SERVICE_ID."') THEN (8528) 
                WHEN (bb_unit_details.service_id = '"._247AROUND_AC_SERVICE_ID."') THEN (8415)
                WHEN (bb_unit_details.service_id = '"._247AROUND_WASHING_MACHINE_SERVICE_ID."') THEN (8450)
                WHEN (bb_unit_details.service_id = '"._247AROUND_REFRIGERATOR_SERVICE_ID."') THEN (8418) ELSE '' END As hsn_code, owner_phone_1, gst_no,
                sc.company_name, sc.address as company_address, sc.state,
                sc.owner_email, sc.primary_contact_email, sc.owner_phone_1, sc.is_buyback_gst_invoice";
            $group_by = " GROUP BY bb_unit_details.service_id ";
        }
        
        $sql = "SELECT $select, 'Product' AS 'product_or_services'
                
                
                FROM `bb_order_details`, bb_unit_details, services, service_centres as sc WHERE 
                `assigned_cp_id` = '$vendor_id' 
                AND bb_unit_details.`order_status` = 'Delivered' 
                AND acknowledge_date >= '$from_date' AND `acknowledge_date`< '$to_date'
                AND sc.id = assigned_cp_id
                AND bb_order_details.partner_order_id =  bb_unit_details.partner_order_id
                AND bb_unit_details.service_id = services.id $profit_loss_where  $is_foc_null $group_by ";
        
        $query = $this->db->query($sql);
        
        return $query->result_array();
        
    }

    /**
     * @desc: Calculate unbilled Amount for vendor
     * @param String $vendor_id
     * @param String $to_date
     * @return Array
     */
    function get_unbilled_amount($vendor_id) {
        
        $sql = "SELECT SUM(`vendor_to_around` - `around_to_vendor`) AS unbilled_amount
                FROM booking_unit_details AS ud, booking_details AS bd
                WHERE bd.assigned_vendor_id = '$vendor_id'
                AND pay_to_sf =  '1'
                AND booking_status =  'Completed'
                AND bd.booking_id = ud.booking_id 
                AND ud_closed_date >= '" . date('Y-m-d', strtotime("-3 months")) . "'
                AND `vendor_cash_invoice_id` IS NULL
                AND `vendor_foc_invoice_id` IS NULL";

        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * @desc This method returns booking id and courier charges for completed booking
     * @param String $vendor_id
     * @param String $from_date
     * @param String $to_date
     * @return Array
     */
    function get_sf_courier_charges($vendor_id, $from_date, $to_date, $is_regenerate) {
        $invoice_check = "";
        if($is_regenerate == 0){
            $invoice_check .= "AND vendor_courier_invoice_id IS NULL ";
        }
        $sql = " SELECT GROUP_CONCAT(sp.id) as sp_id, GROUP_CONCAT(bd.booking_id) as booking_id, 
                 SUM(sp.courier_charges_by_sf) as courier_charges_by_sf 
                FROM  booking_details as bd, booking_unit_details as ud,
                spare_parts_details as sp
                WHERE 
                ud.booking_status =  '"._247AROUND_COMPLETED."'
                AND bd.assigned_vendor_id = '$vendor_id'
                AND status IN( '"._247AROUND_COMPLETED."', '".DEFECTIVE_PARTS_SEND_TO_PARTNER_BY_WH."')
                AND sp.booking_id = bd.booking_id
                AND bd.booking_id = ud.booking_id
                AND ud.ud_closed_date >=  '$from_date'
                AND ud.ud_closed_date <  '$to_date'
                AND pay_to_sf = '1'
                AND `approved_defective_parts_by_partner` = 1
                AND around_pickup_from_service_center = 0
                $invoice_check
                AND courier_charges_by_sf > 0 
                AND awb_by_sf IS NOT NULL 
                GROUP BY awb_by_sf HAVING SUM(sp.courier_charges_by_sf) > 0 ";

        $query = $this->db->query($sql);
        return $query->result_array();
    }
    /**
     * @desc This function is used to return those courier data who sent to partner from service center.
     * @param String $partner_id
     * @param String $from_date
     * @param String $to_date
     * @return Array
     */
    function get_partner_courier_charges($partner_id, $from_date, $to_date){
        
        $sql = "SELECT GROUP_CONCAT(sp.id) as sp_id, GROUP_CONCAT(bd.booking_id) as booking_id, 
                awb_by_sf as awb, box_count, count(bd.booking_id) as count_of_booking,
                SUM(sp.courier_charges_by_sf) as courier_charges_by_sf, bd.city,
                CASE WHEN (billable_weight > 0 ) THEN 
                (concat(billable_weight, ' KG'))
                ELSE '' END AS billable_weight,
                CASE WHEN (defective_courier_receipt IS NOT NULL) THEN 
                (concat('".S3_WEBSITE_URL."misc-images/',defective_courier_receipt)) ELSE '' END AS courier_receipt_link
                FROM  booking_details as bd 
                JOIN booking_unit_details as ud ON bd.booking_id = ud.booking_id 
                JOIN spare_parts_details as sp ON sp.booking_id = bd.booking_id 
                LEFT JOIN courier_company_invoice_details ON awb_number = awb_by_sf 
                WHERE
                ud.booking_status =  '"._247AROUND_COMPLETED."'
                AND bd.partner_id = '$partner_id'
                AND ud.partner_id = '$partner_id'
                AND status IN( '"._247AROUND_COMPLETED."', '".DEFECTIVE_PARTS_SEND_TO_PARTNER_BY_WH."')
                AND ud.ud_closed_date >=  '$from_date'
                AND ud.ud_closed_date <  '$to_date'
                AND `approved_defective_parts_by_partner` = 1
                AND partner_courier_invoice_id IS NULL
                AND awb_by_sf IS NOT NULL
                GROUP BY awb HAVING courier_charges_by_sf > 0 
                
                ";
     
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    /**
     * @desc This function is used to return those courier data who arranged by 247around from Partner
     * @param int $partner_id
     * @param String $from_date
     * @param String $to_date
     * @return Array
     */
    function get_pickup_arranged_by_247around_from_partner($partner_id, $from_date, $to_date){
        $sql = "SELECT GROUP_CONCAT(sp.id) as sp_id, GROUP_CONCAT(bd.booking_id) as booking_id, 
                awb_by_partner as awb, 
                SUM(sp.courier_price_by_partner) as courier_charges_by_sf, bd.city,
                CASE WHEN (billable_weight > 0 ) THEN (concat(billable_weight, ' KG')) ELSE '' END AS billable_weight,
                box_count, count(bd.booking_id) as count_of_booking,
                '' AS courier_receipt_link
                FROM  booking_details as bd 
                JOIN  booking_unit_details as ud ON bd.booking_id = ud.booking_id 
                JOIN spare_parts_details as sp ON sp.booking_id = bd.booking_id 
                LEFT JOIN courier_company_invoice_details ON awb_number = awb_by_partner
                WHERE
                 ud.booking_status =  '"._247AROUND_COMPLETED."'
                     AND bd.partner_id = '$partner_id'
                AND ud.partner_id = '$partner_id'
                AND sp.partner_id = '$partner_id'
                AND status IN( '"._247AROUND_COMPLETED."', '".DEFECTIVE_PARTS_SEND_TO_PARTNER_BY_WH."')
                AND sp.booking_id = bd.booking_id
                AND bd.booking_id = ud.booking_id
                AND ud.ud_closed_date >=  '$from_date'
                AND ud.ud_closed_date <  '$to_date'
                AND `around_pickup_from_partner` = 1
                AND partner_warehouse_courier_invoice_id IS NULL
                AND awb_by_partner IS NOT NULL
                GROUP BY awb HAVING courier_charges_by_sf > 0 
                ";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
            

    function get_payment_history($select,$where,$is_join=false) {
        $this->db->select($select);
        $this->db->where($where);
        if($is_join){
            $this->db->join('employee','payment_history.agent_id = employee.id','left');
        }
        $this->db->join('bank_transactions','bank_transactions.id = payment_history.bank_transaction_id','left');
        $query = $this->db->get('payment_history');
        return $query->result_array();
    }
    
    function check_gst_tax_type($state, $customer_state = false) {
        if (!empty($customer_state)) {
            if ((strcasecmp($state, $customer_state) == 0) ||
                    (strcasecmp($state, $customer_state) == 0)) {
                //If matched return true;
                // CGST & SGST
                return TRUE;
            } else {
                //IGST
                return FALSE;
            }
        } else {
            if ((strcasecmp($state, "DELHI") == 0) ||
                    (strcasecmp($state, "New Delhi") == 0)) {
                //If matched return true;
                // CGST & SGST
                return TRUE;
            } else {
                //IGST
                return FALSE;
            }
        }
    }

    function get_state_code($where){
        $this->db->select("state_code, state");
        $this->db->where($where);
        $query = $this->db->get("state_code");
        return $query->result_array();
    }
    
    function get_new_invoice_details($post, $select) {
        $this->_get_new_invoice_details_query($post, $select);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }

    function _get_new_invoice_details_query($post, $select) {
        $this->db->from('invoice');
        $this->db->select($select, FALSE);

        if (!empty($post['where'])) {
            $this->db->where($post['where'], FALSE);
        }
        if (isset($post['where_in'])) {
            foreach ($post['where_in'] as $index => $value) {

                $this->db->where_in($index, $value);
            }
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

        if (!empty($post['order'])) { // here order processing
            $this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
        } else if (isset($this->order)) {
            $order = array('invoice.id' => 'desc');
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
    
     public function new_invoice_count_all($post) {
        $this->db->from('invoice');

        $this->db->where($post['where']);
        if(isset($post['where_in'])){
            foreach ($post['where_in'] as $index => $value) {
                $this->db->where_in($index, $value);
            }
        }
        
        $query = $this->db->count_all_results();

        return $query;
    }

    function new_invoice_count_filtered($post) {
        $this->_get_new_invoice_details_query($post, "invoice.id");

        $query = $this->db->get();
        return $query->num_rows();
    }
    
    function get_unsettle_inventory_invoice($select, $where, $order_by = array()){
        $this->db->select($select);
        $this->db->from('invoice_details');
        $this->db->join('vendor_partner_invoices', 'invoice_details.invoice_id = vendor_partner_invoices.invoice_id');
        $this->db->where($where);
        if(!empty($order_by)){
            $this->db->order_by($order_by['column_name'], $order_by['param']);
        }
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function insert_inventory_invoice($data){
        $this->db->insert('inventory_invoice_mapping', $data);
        return $this->db->insert_id();
    }
    
    function get_courier_details($select, $where){
        
        $this->db->select($select, FALSE);
        $this->db->where($where);
        $query = $this->db->get('courier_details');
        return $query->result_array();
    }
    
    function get_fixed_variable_charge($where){
        $this->db->select('vendor_partner_variable_charges.id, vendor_partner_variable_charges.entity_type, vendor_partner_variable_charges.entity_id, vendor_partner_variable_charges.fixed_charges,vendor_partner_variable_charges.percentage_charge, vendor_partner_variable_charges.validity_in_month, variable_charges_type.type as charges_type, variable_charges_type.description, variable_charges_type.hsn_code, variable_charges_type.gst_rate, variable_charges_type.is_fixed');
        $this->db->where($where);
        $this->db->join('variable_charges_type', 'variable_charges_type.id = vendor_partner_variable_charges.charges_type');
        $query = $this->db->get('vendor_partner_variable_charges');
        return $query->result_array();
    }
    
    function get_misc_charges_invoice_data($select, $vendor_partner_invoice, 
            $from_date, $to_date, $vendor_partner,$vendor_partner_id, $sf_partner_charge, $current_status = ""){
        $this->db->select($select, false);
        $this->db->from('miscellaneous_charges');
        $this->db->join('booking_details', 'booking_details.booking_id = miscellaneous_charges.booking_id');
        $this->db->join('services', 'booking_details.service_id = services.id');
        if(!empty($current_status)){
            $this->db->where('booking_details.current_status', _247AROUND_COMPLETED);
        }
        
        $this->db->where($vendor_partner_invoice, NULL);
        $this->db->where("active", 1);
        $this->db->where($sf_partner_charge. " > 0", NULL);
        if(!empty($from_date)){
            $this->db->where('booking_details.closed_date >= ', $from_date );
        }
        if(!empty($to_date)){
            $this->db->where('booking_details.closed_date < ', $to_date );
        }
        
        $this->db->where($vendor_partner, $vendor_partner_id );
        $query = $this->db->get();
        return $query->result_array();
    }

      /**
     * @desc: This function is used to get partner annual charges data from partner table 
     * @params: Array $where
     * @return: string
     * 
     */
    
     public function get_partners_annual_charges($select, $partner_id = "", $partner_active = "") {
        $wh = "";
        $partner_wh = "";
        if(!empty($partner_id)){
            $wh = " AND vendor_partner_id = '$partner_id' ";
        }
        if(!empty($partner_active)){
           $partner_wh = " AND partners.is_active = 1 ";
        }
        
        $sql = "SELECT $select FROM vendor_partner_invoices as v, partners"
                . " WHERE partners.id = v.vendor_partner_id "
                . " AND v.vendor_partner = 'partner' "
                . " AND invoice_tagged LIKE '%".ANNUAL_CHARGE_INVOICE_TAGGING."%'"
                . " AND to_date IN (SELECT MAX(to_date) FROM vendor_partner_invoices as vp"
                . " WHERE invoice_tagged LIKE '%".ANNUAL_CHARGE_INVOICE_TAGGING."%' "
                . "AND vp.vendor_partner ='partner' AND v.vendor_partner_id = vp.vendor_partner_id"
                . "  $wh $partner_wh ) "
                .  " $wh $partner_wh GROUP BY vendor_partner_id ORDER BY public_name ";
        
        $query = $this->db->query($sql);
        return $query->result();
    }
    /**
     * @desc This is used to get partner warehouse courier data
     * @param Int $partner_id
     * @param String $from_date
     * @param String $to_date
     * @return Array
     */
    function get_partner_invoice_warehouse_courier_data($partner_id, $from_date, $to_date){
        log_message('info', __METHOD__. " Enterring..");
        $sql = 'SELECT GROUP_CONCAT(sp.id) as sp_id, GROUP_CONCAT(DISTINCT sp.booking_id) as booking_id, '
                . ' awb_by_partner,'
                . ' awb_by_partner as awb, COALESCE(SUM(courier_price_by_partner),0) as courier_charges_by_sf, '
                . ' CASE WHEN (billable_weight > 0 ) THEN (concat(billable_weight, " KG")) ELSE "" END AS billable_weight,'
                . ' box_count, count(bd.booking_id) as count_of_booking,'
                . ' bd.city, CASE WHEN (courier_pic_by_partner IS NOT NULL) '
                . ' THEN (concat("'.S3_WEBSITE_URL.'vendor-partner-docs/",courier_pic_by_partner)) ELSE "" END AS courier_receipt_link '
                . ' FROM spare_parts_details as sp '
                . ' JOIN  booking_details as bd ON bd.booking_id = sp.booking_id  '
                . ' LEFT JOIN courier_company_invoice_details ON awb_number = awb_by_partner '
                . ' WHERE '
                . ' entity_type = "'._247AROUND_SF_STRING.'" '
                . ' AND bd.partner_id = "'.$partner_id.'" '
                . ' AND awb_by_partner IS NOT NULL '
                . ' AND sp.shipped_date >= "'.$from_date.'" '
                . ' AND sp.shipped_date < "'.$to_date.'" '
                . ' AND  parts_shipped IS NOT NULL '
                . ' AND partner_warehouse_courier_invoice_id IS NULL'
                . ' GROUP BY awb_by_partner HAVING courier_charges_by_sf > 0 ';
                
       
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    function get_partner_invoice_warehouse_packaging_courier_data($partner_id, $from_date, $to_date){
        log_message('info', __METHOD__. " Enterring..");
        $sql = 'SELECT sp.id as sp_id '
                . ' FROM spare_parts_details as sp '
                . ' JOIN  booking_details as bd ON bd.booking_id = sp.booking_id  '
                . ' WHERE '
                . ' entity_type = "'._247AROUND_SF_STRING.'" '
                . ' AND bd.partner_id = "'.$partner_id.'" '
                . ' AND awb_by_partner IS NOT NULL '
                . ' AND sp.shipped_date >= "'.$from_date.'" '
                . ' AND sp.shipped_date < "'.$to_date.'" '
                . ' AND  parts_shipped IS NOT NULL '
                . ' AND partner_warehouse_packaging_invoice_id IS NULL'
                . ' GROUP BY sp.id  ';
                
       
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    function get_defective_parts_courier_return_partner($partner_id, $from_date, $to_date){
        log_message('info', __METHOD__. " Enterring..");
        $sql = 'SELECT GROUP_CONCAT(courier_details.id) as c_id, "" AS billable_weight, '
                . ' GROUP_CONCAT(DISTINCT booking_id) as booking_id, AWB_no as awb,  count(booking_id) as count_of_booking, '
                . ' "" AS box_count, '
                . ' COALESCE(SUM(courier_charge),0) as courier_charges_by_sf, "" AS city, CASE WHEN (courier_file IS NOT NULL) '
                .'  THEN (concat("'.S3_WEBSITE_URL.'vendor-partner-docs/",courier_file)) ELSE "" END AS courier_receipt_link '
                . ' FROM `courier_details` '
                . ' WHERE `sender_entity_type` = "'._247AROUND_SF_STRING.'"  '
                . ' AND receiver_entity_type = "'._247AROUND_PARTNER_STRING.'" '
                . ' AND `receiver_entity_id` = "'.$partner_id.'" '
                . ' AND shipment_date >= "'.$from_date.'" '
                . ' AND shipment_date < "'.$to_date.'" '
                . ' AND partner_invoice_id IS NULL '
                . ' GROUP BY `AWB_no`  HAVING courier_charges_by_sf > 0 ';
        
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    /**
     * @desc  This is used to generate SF warehouse courier dispatched( to SF) DATA
     * @param int $vendor_id
     * @param String $from_date
     * @param String $to_date
     * @param int $is_regenerate
     * @return Array
     */
    function get_sf_invoice_warehouse_courier_data($vendor_id, $from_date, $to_date, $is_regenerate){
        log_message('info', __METHOD__. " Enterring..");
        $invoice_check = "";
        if($is_regenerate == 0){
            $invoice_check = ' AND warehouse_courier_invoice_id IS NULL';
        }
        $sql = 'SELECT GROUP_CONCAT(sp.id) as sp_id, GROUP_CONCAT(DISTINCT sp.booking_id) as booking_id, '
                . ' awb_by_partner,'
                .' COALESCE(SUM(courier_price_by_partner),0) as courier_charges_by_sf '
                . ' FROM spare_parts_details as sp '
                . ' WHERE '
                . ' entity_type = "'._247AROUND_SF_STRING.'" '
                . ' AND sp.partner_id = "'.$vendor_id.'" '
                . ' AND awb_by_partner IS NOT NULL '
                . ' AND sp.shipped_date >= "'.$from_date.'" '
                . ' AND sp.shipped_date < "'.$to_date.'" '
                . ' AND  parts_shipped IS NOT NULL '
                .  $invoice_check
                . ' GROUP BY awb_by_partner HAVING courier_charges_by_sf > 0';
        
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    function get_defective_parts_return_partner_sf_invoice($vendor_id, $from_date, $to_date, $is_regenerate){
        log_message('info', __METHOD__);
        if($is_regenerate == 0){
            $invoice_check = ' AND sender_invoice_id IS NULL';
        }
        $sql = 'SELECT GROUP_CONCAT(courier_details.id) as c_id, '
                . ' GROUP_CONCAT(DISTINCT booking_id) as booking_id, '
                . ' COALESCE(SUM(courier_charge),0) as courier_charges_by_sf '
                . ' FROM `courier_details` '
                . ' WHERE `sender_entity_type` = "'._247AROUND_SF_STRING.'"  '
                . ' AND receiver_entity_type = "'._247AROUND_PARTNER_STRING.'" '
                . ' AND `sender_entity_id` = "'.$vendor_id.'" '
                . ' AND shipment_date >= "'.$from_date.'" '
                . ' AND shipment_date < "'.$to_date.'" '
                .  $invoice_check
                . ' GROUP BY `AWB_no` HAVING courier_charges_by_sf > 0 ';
        
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    /**
     * @desc This function is used to get invoice Data
     * @param String $select
     * @param Array $post
     * @return Array
     */
    function searchInvoicesdata($select, $post){
        $this->_querySearchInvoicesdata($select, $post);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }
        
    function _querySearchInvoicesdata($select, $post){
        $this->db->from('vendor_partner_invoices');
        $this->db->select($select, FALSE);

        $this->db->join('service_centres', 'service_centres.id = vendor_partner_invoices.vendor_partner_id AND vendor_partner_invoices.vendor_partner = "vendor" ', "LEFT");
        $this->db->join('partners', 'partners.id = vendor_partner_invoices.vendor_partner_id AND vendor_partner_invoices.vendor_partner = "partner" ', "LEFT");
        if (!empty($post['where'])) {
            $this->db->where($post['where'], FALSE);
        }
        if (isset($post['where_in'])) {
            foreach ($post['where_in'] as $index => $value) {

                $this->db->where_in($index, $value);
            }
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

        if (!empty($post['order'])) { // here order processing
            $this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
        } else if (isset($post['order_by'])) {
            $order = $post['order_by'];
            $this->db->order_by(key($order), $order[key($order)]);
        }
        
        if(isset($post['group_by']) && !empty($post['group_by'])){
            $this->db->group_by($post['group_by']);
        }
    }  
     /**
     * @desc This function is used to  get count of all invoice
     * @param Array $post
     */
    public function count_all_invoices($post) {
        $this->_count_all_invoices($post);
        $query = $this->db->count_all_results();

        return $query;
    }
    /**
     * @desc This function is used to  get count of all invoice
     * @param Array $post
     */
    public function _count_all_invoices($post) {
        $this->db->from('vendor_partner_invoices');
        $this->db->join('service_centres', 'service_centres.id = vendor_partner_invoices.vendor_partner_id AND vendor_partner_invoices.vendor_partner = "vendor" ', "LEFT");
        $this->db->join('partners', 'partners.id = vendor_partner_invoices.vendor_partner_id AND vendor_partner_invoices.vendor_partner = "partner" ', "LEFT");
        if(isset($post['where'])){
            $this->db->where($post['where']);
        }
        
        if(isset($post['where_in'])){
            foreach ($post['where_in'] as $index => $value) {
                $this->db->where_in($index, $value);
            }
        }
        
    }
    /**
     * @desc This function is used to get count of filtered invoice Data
     * @param String $select
     * @param Array $post
     * @return Int
     */
    function count_filtered_invoice($select, $post) {
        $this->_querySearchInvoicesdata($select, $post);

        $query = $this->db->get();
        return $query->num_rows();
    }
    
    
    
    /**
     * @desc This function is used to get payment summary Data
     * @param String $select
     * @param Array $post
     * @return Array
     */
    function searchPaymentSummaryData($select, $post){
        $this->_querySearchPaymentSummaryData($select, $post);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        $query = $this->db->get();
        log_message("info", __METHOD__." query ".$this->db->last_query()); 
        return $query->result();
    }
    
    function _querySearchPaymentSummaryData($select, $post){
        $this->db->from('bank_transactions');
        $this->db->select($select, FALSE);
        
        $this->db->join('employee','bank_transactions.agent_id = employee.id', "LEFT");
        
        $this->db->join('service_centres', 'service_centres.id = bank_transactions.partner_vendor_id AND bank_transactions.partner_vendor = "vendor" ', "LEFT");
        $this->db->join('partners', 'partners.id = bank_transactions.partner_vendor_id AND bank_transactions.partner_vendor = "partner" ', "LEFT");
        if (!empty($post['where'])) {
            $this->db->where($post['where'], FALSE);
        }
        if (isset($post['where_in'])) {
            foreach ($post['where_in'] as $index => $value) {

                $this->db->where_in($index, $value);
            }
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

        if (!empty($post['order'])) { // here order processing
            $this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
        } else if (isset($post['order_by'])) {
            $order = $post['order_by'];
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
    
    /**
     * @desc This function is used to  get count of all bank transactions
     * @param Array $post
     */
    public function count_all_transactions($post) {
        $this->_count_all_transactions($post);
        $query = $this->db->count_all_results();

        return $query;
    }
    /**
     * @desc This function is used to  get count of all bank transactions
     * @param Array $post
     */
    public function _count_all_transactions($post) {
        $this->db->from('bank_transactions');
        $this->db->join('employee','bank_transactions.agent_id = employee.id', "LEFT");
        $this->db->join('service_centres', 'service_centres.id = bank_transactions.partner_vendor_id AND bank_transactions.partner_vendor = "vendor" ', "LEFT");
        $this->db->join('partners', 'partners.id = bank_transactions.partner_vendor_id AND bank_transactions.partner_vendor = "partner" ', "LEFT");
        if(isset($post['where'])){
            $this->db->where($post['where']);
        }
        
        if(isset($post['where_in'])){
            foreach ($post['where_in'] as $index => $value) {
                $this->db->where_in($index, $value);
            }
        }
        
    }
    
     /**
     * @desc This function is used to get count of filtered bank transactions Data
     * @param String $select
     * @param Array $post
     * @return Int
     */
    function count_filtered_bank_transaction($select, $post) {
        $this->_querySearchPaymentSummaryData($select, $post);

        $query = $this->db->get();
        return $query->num_rows();
    }
    
    /**
     * @desc This function is used to get Partner booking Tat data
     * @param Array $data
     * @param int $partner_id
     * @return boolean|Array
     */
    function get_partner_invoice_tat_data($data, $partner_id){
        log_message('info', __METHOD__);
        $booking_id = array_column($data, 'booking_id');
        $w = array('entity_id'=> $partner_id, "entity" => _247AROUND_PARTNER_STRING, "active" => 1);
        $tat_condition = $this->get_tat_invoicing_codition($w);
        $booking_tat_type = array();
        $penalty_tata_booking_details = array();
        if(!empty($tat_condition)){
            foreach ($tat_condition as $key => $value) {
                $request_type = ($value['installation_repair'] == 0)?"Installation":"Repair";
                $l_u = ($value['local_upcountry'] == 0)?"Local":"Upcountry";
                
                $b_w = array('is_upcountry' => $value['local_upcountry'],
                    'request_type' => $value['installation_repair'],
                    'leg_1 >="0" ' => NULL,
                    'leg_1 <"'.($value['tat_with_in_days']).'" ' => NULL);
                
                $b_data = $this->get_booking_tat_data("*, Case WHEN `request_type` = 0 THEN 'Installation' ELSE 'Repair' END as 'type',"
                        . " case WHEN `is_upcountry` = 0 THEN 'No' ELSE 'Yes' END as 'upcountry_status'", 
                        $b_w, array('booking_id' => $booking_id));
                
                array_push($penalty_tata_booking_details, $b_data);
                
                $tat_condition[$key]['achieved_count'] = count($b_data);

                $b_w1 = array('is_upcountry' => $value['local_upcountry'],
                    'request_type' => $value['installation_repair']);
                
                $tat_condition[$key]['total_booking'] = $this->get_booking_tat_data("COUNT(id) as count", $b_w1, array('booking_id' => $booking_id))[0]['count'];
                if($tat_condition[$key]['total_booking'] > 0){
                    $tat_condition[$key]['archieved_percentage'] = sprintf("%.2f",($tat_condition[$key]['achieved_count']/$tat_condition[$key]['total_booking'])*100);
                    if($tat_condition[$key]['archieved_percentage'] < $value['penalty_below_criteria']){
                        $tat_condition[$key]['booking_failed'] = sprintf("%.2f",(($value['penalty_below_criteria'] - $tat_condition[$key]['archieved_percentage'])/100) * $tat_condition[$key]['total_booking']);
                        $tat_condition[$key]['penalty_amount'] = sprintf("%.2f",((($tat_condition[$key]['basic_amount'] * $value['penalty_percentage'])/100) * $tat_condition[$key]['booking_failed']));
                    } else {
                        $tat_condition[$key]['booking_failed'] = 0;
                        $tat_condition[$key]['penalty_amount'] = 0;
                    }
                } else {
                    $tat_condition[$key]['archieved_percentage'] = 0;
                    $tat_condition[$key]['booking_failed'] = 0;
                    $tat_condition[$key]['penalty_amount'] = 0;
                }
                
                array_push($booking_tat_type, array('booking_type' => $request_type." ".$l_u, "booking_count" => $tat_condition[$key]['total_booking']));

            }
            $i = 0;
            $a = array();
            foreach ($penalty_tata_booking_details as $value) {
                foreach ($value as $value1) {
                    array_push($a, array('booking_id' => $value1['booking_id'], "type" => $value1['type'], 
                        "upcountry_status" => $value1['upcountry_status'],"leg_1" => $value1['leg_1'] ));
                }
            }
           
            $tat['tat_count'] = array_map("unserialize", array_unique(array_map("serialize", $booking_tat_type)));
            $tat['tat'] = $tat_condition;
            $tat['penalty_booking_data'] = $a;
            return $tat;
            
        } else {
            return false;
        }
    }
    /**
     * @desc This function is used to get penalty condition
     * Partner apply penalty on Around. We already set condition for specific partner
     * @param Array $where
     * @return boolean|Array
     */
    function get_tat_invoicing_codition($where){
        $this->db->select("*");
        $this->db->where($where);
        
        $query = $this->db->get("tat_invoice_condition");
        return $query->result_array();
    }
    /**
     * @desc This function is used to get booking tat data
     * @param String $select
     * @param Array $where
     * @param Array $where_in
     * @return boolean|Array
     */
    function get_booking_tat_data($select, $where, $where_in){
        log_message('info', __METHOD__ );
        $this->db->select($select, false);
        if(!empty($where)){
            $this->db->where($where);
        }
        if (isset($where_in)) {
            foreach ($where_in as $index => $value) {

                $this->db->where_in($index, $value);
            }
        }
        
        $query = $this->db->get("booking_tat");
        return $query->result_array();
    }
    
    /**
     * @desc This function is used to insert fixed variable charges
     * @param String $data
     * @return insert_id
     */
    function insert_into_variable_charge($data){
        $this->db->insert('vendor_partner_variable_charges', $data);
        return $this->db->insert_id();
    }
    
    /**
     * @desc This function is used to get fixed variable charges
     * @param String - $select, Array - $where, $join - boolean
     * @return Array
    */
    function get_variable_charge($select, $where=array(), $join=null){
        $this->db->select($select);
        if(!empty($where)){
          $this->db->where($where);  
        }
        if(!empty($join)){
            $this->db->join('service_centres', 'service_centres.id = vendor_partner_variable_charges.entity_id AND vendor_partner_variable_charges.entity_type = "vendor" ', "LEFT");
            $this->db->join('partners', 'partners.id =  vendor_partner_variable_charges.entity_id AND vendor_partner_variable_charges.entity_type = "partner" ', "LEFT");
        }
        $query = $this->db->get('vendor_partner_variable_charges');
        return $query->result_array();
    }

    function get_breakup_invoice_details($select, $where){
        log_message('info', __METHOD__);
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get("invoice_details");
        return $query->result_array();
    }

    
     /**
     * @desc This function is used to update fixed variable charges
     * @param Array $where and $data
     * @return boolean
     */
    function update_into_variable_charge($where, $data){
        $this->db->where($where);
        $this->db->update('vendor_partner_variable_charges', $data);
        if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }
    
     /**
     * @desc This function is used to get all invoice vertical
     * @param void
     * @return result array
     */
    function get_invoice_tag($select = '*', $where=array()){
        $this->db->select($select);
        if(!empty($where)){
             $this->db->where($where);
        }
        $query = $this->db->get("invoice_tags");
        return $query->result_array();
    }
    
     /**
     * @desc This is used to get list of HSN Code Details.     
     * @table hsn_code_details 
     * @return array
     */    
     function get_hsncode_details($select, $where) {
        $this->db->select($select);
        if(!empty($where)){
             $this->db->where($where);
        }
        $query = $this->db->get("hsn_code_details");
        return $query->result_array();
    }
       
    /**
     * @desc: This is used to update hsn code details table
     * @param Array $where
     * @param Array $data
     * @return boolean
     */
    function update_hsn_code_details($where, $data) {
        $this->db->where($where);
        $this->db->update('hsn_code_details', $data);
        log_message('info', __FUNCTION__ . '=> Update HSN Code Details: ' . $this->db->last_query());
        if ($this->db->affected_rows() > 0) {
            $result = true;
        } else {
            $result = false;
        }
        
        return $result;
    }
    
    /**
     * @desc: This is used to insert data into booking_debit_credit_details
     * @param Array $where
     * @param Array $data
     * @return boolean
     */
    function insert_into_booking_debit_credit_detils($data){
        $this->db->insert('booking_debit_credit_details', $data);
        return $this->db->insert_id();
    }
    /**
     * @desc This function is used to get Pending Bookings of Partners where part needs to be shipped by them
     * @param String $partner_id
     * @return type
     */
    function get_unit_for_requested_spare($partner_id){
        $this->db->distinct();
        $this->db->select('ud.id');
        $this->db->from('spare_parts_details as s');
        $this->db->join('booking_unit_details as ud', 'ud.booking_id = s.booking_id');
        $this->db->join('partners', 'partners.id = ud.partner_id');
        $this->db->where('ud.partner_invoice_id', NULL);
        $this->db->where('s.status', SPARE_PARTS_REQUESTED);
        $this->db->where('s.part_requested_on_approval', 1);
        $this->db->where('ud.booking_status NOT IN ("'._247AROUND_COMPLETED.'", "'._247AROUND_CANCELLED.'") ', NULL);
        $this->db->where('ud.partner_net_payable > 0', NULL);
        $this->db->where("DATEDIFF(CURRENT_TIMESTAMP, STR_TO_DATE(date_of_request, '%Y-%m-%d')) >= partners.oot_spare_to_be_shipped ", NULL);
        $this->db->where('ud.partner_id', $partner_id);
        $query = $this->db->get();
        return  $query->result_array();
        
    }
    function get_buyback_paid_reimbursement_amount(){
       $sql= "SELECT round(SUM(amount_paid)) as reimburse_amount FROM (vendor_partner_invoices) WHERE vendor_partner_invoices.invoice_id IN (SELECT DISTINCT partner_reimbursement_invoice FROM bb_unit_details)";
       $query = $this->db->query($sql);
       return  $query->result_array();
    }
    
    /**
     * @desc: This method is used to get vendor partner proforma invoices details
     * @param String WHERE
     * @return Array
     */
    function get_proforma_invoices_details($where, $select = "*", $group_by = false) {
        $this->db->select($select, false);
        if(!empty($where)){
            $this->db->where($where);
        }
        
        if($group_by){
            $this->db->group_by($group_by);
        }
        $query = $this->db->get("vendor_partner_proforma_invoices");
        if ($query->num_rows > 0) {
            return $query->result_array();
        } else {
            return FALSE;
        }
    }
    
    /*
     * @Desc - This function is used to save performa invoices
     * @param - $details
     * @return - $insert_id
     */

    function insert_performa_invoice($details) {
        $this->db->insert('vendor_partner_proforma_invoices', $details);
        return $this->db->insert_id();
    }
}
