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
    function getsummary_of_invoice($vendor_partner, $where, $due_date_flag = false) {
       
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
                $due_date_status = " AND `due_date` <= CURRENT_DATE() ";
            }
            
        } else if ($vendor_partner == "partner") {
            $p_where = array();
            if(isset($where['active'])){
                $p_where = array('is_active' => $where['active']);
            }
            if(isset($where['id'])){    
                $p_where['id'] = $where['id'];
            }
            if(isset($where['is_prepaid'])){    
                $p_where['is_prepaid'] = $where['is_prepaid'];
            }
            $data = $this->partner_model->get_all_partner($p_where);
            
            $due_date_status = "";
        }

        foreach ($data as $key => $value) {

            $result = $this->get_summary_invoice_amount($vendor_partner, $value['id'], $due_date_status);
           
            $data[$key]['vendor_partner'] = $vendor_partner;
            $data[$key]['final_amount'] = $result[0]['final_amount'];
            $data[$key]['amount_collected_paid'] = $result[0]['amount_collected_paid'];
            $data[$key]['is_stand'] = $result[0]['is_stand'];
            if (isset($value['name'])) {
                $sp_d = $this->get_pending_defective_parts($value['id']);
                if(!empty($sp_d)){
                    $data[$key]['count_spare_part'] = $sp_d[0]['count'];
                    $data[$key]['max_sp_age'] = $sp_d[0]['max_sp_age'];
                } else {
                    $data[$key]['count_spare_part'] = 0;
                    $data[$key]['max_sp_age'] = 0;
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
            $sql = "SELECT COALESCE(SUM(`amount_collected_paid` ),0) as amount_collected_paid, "
                    . " CASE WHEN (SELECT count(id) FROM vendor_partner_invoices "
                    . " WHERE type_code ='A' AND type = 'Stand' AND `settle_amount` = 0 AND vendor_partner_id = '$vendor_partner_id' "
                    . " AND vendor_partner = '$vendor_partner' $otherWhere ) "
                    . " THEN(1) ELSE 0 END as is_stand FROM  `vendor_partner_invoices` "
                    . " WHERE vendor_partner_id = '$vendor_partner_id' AND vendor_partner = '$vendor_partner' $otherWhere";


            $data = $this->db->query($sql);
            $result = $data->result_array();
            
            $bank_transactions = $this->getbank_transaction_summary($vendor_partner, $vendor_partner_id);
            $result[0]['final_amount'] = round($result[0]['amount_collected_paid'] - $bank_transactions[0]['credit_amount'] + $bank_transactions[0]['debit_amount'],0);
            return $result;
    }
    
    function get_pending_defective_parts($service_center_id){
        $select = "count(spare_parts_details.booking_id) as count, DATEDIFF(CURRENT_TIMESTAMP, MIN(service_center_closed_date)) as max_sp_age";
        $where = array(
            "spare_parts_details.defective_part_required"=>1,
            "spare_parts_details.service_center_id" => $service_center_id,
            "status IN ('".DEFECTIVE_PARTS_PENDING."', '".DEFECTIVE_PARTS_REJECTED."')  " => NULL
            
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
    function getpartner_invoices($partner_id, $from_date_tmp, $to_date_tmp) {
        log_message('info', __FUNCTION__);
        $from_date = date('Y-m-d', strtotime('-1 months', strtotime($from_date_tmp)));
        $to_date = date('Y-m-d', strtotime('+1 day', strtotime($to_date_tmp)));

        $sql1 = "SELECT booking_unit_details.id AS unit_id, CONCAT('''', booking_unit_details.sub_order_id) as sub_order_id, `booking_details`.booking_id as booking_id, "
                . "  invoice_email_to,invoice_email_cc, booking_details.rating_stars,  "
                . " `booking_details`.partner_id, `booking_details`.source, "
                . " CASE WHEN (serial_number_pic = '' OR serial_number_pic IS NULL) THEN ('') ELSE (CONCAT('".S3_WEBSITE_URL."engineer-uploads/', serial_number_pic)) END as serial_number_pic,"
                . "  DATE_FORMAT(STR_TO_DATE(booking_details.booking_date, '%d-%m-%Y'), '%D %b %Y') as booking_date, "
                . " `booking_details`.city, DATE_FORMAT(`booking_unit_details`.ud_closed_date, '%D %b %Y') as closed_date,price_tags, "
                . " `booking_unit_details`.appliance_capacity,`booking_unit_details`.appliance_category, "
                . "  booking_details.booking_primary_contact_no,  "
                . " `services`.services, users.name,order_id, "
                . " partner_net_payable, round((partner_net_payable * ".DEFAULT_TAX_RATE .")/100,2) as gst_amount, 
              
                    CASE WHEN(serial_number IS NULL OR serial_number = '') THEN '' ELSE (CONCAT('''', booking_unit_details.serial_number))  END AS serial_number

              From booking_details, booking_unit_details, services, partners, users
                  WHERE `booking_details`.booking_id = `booking_unit_details`.booking_id 
                  AND `services`.id = `booking_details`.service_id  
                  AND booking_status = 'Completed' 
                  AND users.user_id = booking_details.user_id
                  AND booking_details.partner_id = '" . $partner_id
                . "' AND booking_unit_details.booking_status = 'Completed' "
                . " AND booking_unit_details.partner_net_payable > 0 "
                . " AND booking_unit_details.partner_id = partners.id "
                . " AND partner_invoice_id IS NULL "
                . " AND booking_unit_details.ud_closed_date >= '$from_date'"
                . " AND booking_unit_details.ud_closed_date < '$to_date'";



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
        $this->db->where($where);
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
    
    function get_partner_invoice_data($partner_id, $from_date, $to_date) {
        $sql = "SELECT DISTINCT (`partner_net_payable`) AS rate, ".HSN_CODE." AS hsn_code, 
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
                ".DEFAULT_TAX_RATE." as gst_rate,
                COUNT( ud.`appliance_capacity` ) AS qty, 
                (partner_net_payable * COUNT( ud.`appliance_capacity` )) AS taxable_value,
                `partners`.company_name, product_or_services,
                `partners`.address as company_address, partners.pincode, partners.district,
                `partners`.state,
                `partners`.gst_number
                FROM  `booking_unit_details` AS ud, services, partners
                WHERE `partner_net_payable` >0
                AND ud.partner_id =  '$partner_id'
                AND ud.booking_status =  'Completed'
                AND ud.ud_closed_date >=  '$from_date'
                AND ud.ud_closed_date < '$to_date'
                AND ud.service_id = services.id
                AND partners.id = ud.partner_id
                AND partner_invoice_id IS NULL
                GROUP BY  `partner_net_payable`, ud.service_id,price_tags,product_or_services   ";

        $query = $this->db->query($sql);
        $result['result'] = $query->result_array();
        
        if (!empty($result['result'])) {
            $upcountry_data = $this->upcountry_model->upcountry_partner_invoice($partner_id, $from_date, $to_date);
            $courier = $this->get_partner_courier_charges($partner_id, $from_date, $to_date);
            $misc_select = 'booking_details.order_id, miscellaneous_charges.booking_id, '
                . 'miscellaneous_charges.product_or_services, miscellaneous_charges.description, vendor_basic_charges,'
                . 'miscellaneous_charges.partner_charge, miscellaneous_charges.id,'
                . 'CONCAT("'.S3_WEBSITE_URL.'misc-images/",approval_file) as file';
            
            $misc = $this->get_misc_charges_invoice_data($misc_select, "miscellaneous_charges.partner_invoice_id IS NULL", $from_date, 
                    $to_date, "booking_details.partner_id", $partner_id, "partner_charge");
            $result['upcountry'] = array();
            $result['courier'] = array();
            $result['misc'] = array();
            if (!empty($upcountry_data)) {
                $up_country = array();
                $up_country[0]['description'] = 'Upcountry Charges';
                $up_country[0]['hsn_code'] = '';
                $up_country[0]['qty'] = '';
                $up_country[0]['rate'] = '';
                $up_country[0]['gst_rate'] = DEFAULT_TAX_RATE;
                $up_country[0]['product_or_services'] = 'Upcountry';
                $up_country[0]['taxable_value'] = $upcountry_data[0]['total_upcountry_price'];
                $result['result'] = array_merge($result['result'], $up_country);
                $result['upcountry'] = $upcountry_data;
            }

            if (!empty($courier)) {
                $c_data = array();
                $c_data[0]['description'] = 'Courier Charges';
                $c_data[0]['hsn_code'] = '';
                $c_data[0]['qty'] = '';
                $c_data[0]['rate'] = '';
                $c_data[0]['gst_rate'] = DEFAULT_TAX_RATE;
                $c_data[0]['product_or_services'] = 'Courier';
                $c_data[0]['taxable_value'] = (array_sum(array_column($courier, 'courier_charges_by_sf')));
                $result['result'] = array_merge($result['result'], $c_data);
                $result['courier'] = $courier;
            }
            
            if(!empty($misc)){
                $m = array();
                $m[0]['description'] = 'Miscellaneous Charge';
                $m[0]['hsn_code'] = '';
                $m[0]['qty'] = '';
                $m[0]['rate'] = '';
                $m[0]['gst_rate'] = DEFAULT_TAX_RATE;
                $m[0]['product_or_services'] = 'Service';
                $m[0]['taxable_value'] = (array_sum(array_column($misc, 'partner_charge')));
                $result['result'] = array_merge($result['result'], $m);
                $result['misc'] = $misc;
            }
            
            return $result;
        } else {
            return false;
        }
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
        $result_data = $this->get_partner_invoice_data($partner_id, $from_date, $to_date);

        if (!empty($result_data['result'])) {
            $result =  $result_data['result'];
            $response = $this->_set_partner_excel_invoice_data($result,$from_date_tmp,$to_date_tmp, "Tax Invoice");

            $data['booking'] = $response['booking'];
            $data['meta'] = $response['meta'];
            $data['courier'] = $result_data['courier'];
            $data['upcountry'] = $result_data['upcountry'];
            $data['misc'] = $result_data['misc'];
          
            return $data;
        } else {
            return FALSE;
        }
    }
    
    function _set_partner_excel_invoice_data($result, $sd, $ed, $invoice_type, 
            $invoice_date = false, $is_customer = false, $customer_state =false){
            $c_s_gst =$this->check_gst_tax_type($result[0]['state'], $customer_state);
            
            $meta['total_qty'] = $meta['total_rate'] =  $meta['total_taxable_value'] =  
                    $meta['cgst_total_tax_amount'] = $meta['sgst_total_tax_amount'] =   $meta['igst_total_tax_amount'] =  $meta['sub_total_amount'] = 0;
            $meta['total_ins_charge'] = $meta['total_parts_charge'] =  $meta['total_parts_tax'] =  $meta['total_inst_tax'] = 0;
            $meta['igst_tax_rate'] =$meta['cgst_tax_rate'] = $meta['sgst_tax_rate'] = 0;
            $parts_count = 0;
            $meta["invoice_template"] = $this->get_invoice_tempate($result[0]['gst_number'], $is_customer, $c_s_gst);
            foreach ($result as $key => $value) {
                if($is_customer && empty($result[0]['gst_number'])){
                  
                    $meta['total_taxable_value'] = sprintf("%1\$.2f",($value['taxable_value'] + ($value['taxable_value'] * ($value['gst_rate']/100))));
                    $result[$key]['toal_amount'] = sprintf("%1\$.2f",($value['taxable_value'] + ($value['taxable_value'] * ($value['gst_rate']/100))));
                    
                    
                } else if((empty($is_customer)) && empty($result[0]['gst_number'])){
                   
                    $meta['total_taxable_value'] = sprintf("%1\$.2f",($value['taxable_value']));
                    $result[$key]['toal_amount'] = sprintf("%1\$.2f",($value['taxable_value']));
                    $result[$key]['igst_rate'] =  $result[$key]['cgst_rate'] =  $result[$key]['sgst_rate'] = 0;
                    $result[$key]['cgst_tax_amount'] =   $result[$key]['sgst_tax_amount'] = $result[$key]['igst_tax_amount'] = 0;
                    
                }else if($c_s_gst){

                    $result[$key]['cgst_rate'] =  $result[$key]['sgst_rate'] = $value['gst_rate'];
                    $result[$key]['cgst_tax_amount'] = sprintf("%1\$.2f",($value['taxable_value'] * ($value['gst_rate']/100)/2));
                    $result[$key]['sgst_tax_amount'] = sprintf("%1\$.2f",($value['taxable_value'] * ($value['gst_rate']/100)/2));
                    $meta['cgst_total_tax_amount'] +=  $result[$key]['cgst_tax_amount'];
                    $meta['sgst_total_tax_amount'] += $result[$key]['sgst_tax_amount'];
                    $meta['sgst_tax_rate'] = $meta['cgst_tax_rate'] = $value['gst_rate'];
                    $meta['total_taxable_value'] += $value['taxable_value'];
                    
                    $result[$key]['toal_amount'] = sprintf("%1\$.2f",($value['taxable_value'] + ($value['taxable_value'] * ($value['gst_rate']/100))));
                   
                } else {
                   
                    $result[$key]['igst_rate'] =  $meta['igst_tax_rate'] = $value['gst_rate'];
                    $result[$key]['igst_tax_amount'] = sprintf("%1\$.2f",($value['taxable_value'] * ($value['gst_rate']/100)));
                    $meta['igst_total_tax_amount'] +=  $result[$key]['igst_tax_amount'];
                    $meta['total_taxable_value'] += $value['taxable_value'];
                    
                    $result[$key]['toal_amount'] = sprintf("%1\$.2f",($value['taxable_value'] + ($value['taxable_value'] * ($value['gst_rate']/100))));
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
               
               
                $meta['sub_total_amount'] += $result[$key]['toal_amount'];
                if($value['product_or_services'] == "Service"){
                    
                    $meta['total_ins_charge'] += $value['taxable_value'];
                    
                } else if($value['product_or_services'] == "Product"){
                    
                    $meta['total_parts_charge'] += $value['taxable_value'];
                    $parts_count += $value['qty'];
                }
            }
            $meta['parts_count'] = $parts_count;
            $meta['total_taxable_value'] = round($meta['total_taxable_value'], 0);
            $meta['sub_total_amount'] = round($meta['sub_total_amount'], 0);
            $meta['igst_total_tax_amount'] = round( $meta['igst_total_tax_amount'], 0);
            $meta['cgst_total_tax_amount'] = round( $meta['cgst_total_tax_amount'], 0);
            $meta['sgst_total_tax_amount'] = round( $meta['sgst_total_tax_amount'], 0);
            if($result[0]['gst_number'] == 1){
                $result[0]['gst_number'] = "";
            }
            
            $meta['gst_number'] = $result[0]['gst_number'];
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

                $result[$key]['toal_amount'] = round($value['taxable_value'] + ($value['taxable_value'] * SERVICE_TAX_RATE), 2);
                $meta['total_qty'] += $value['qty'];
                $meta['total_rate'] += $value['rate'];
                $meta['total_taxable_value'] += $value['taxable_value'];
                $meta['sub_total_amount'] += $result[$key]['toal_amount'];
            }


            $meta['reverse_charge'] = 0;
            $meta['reverse_charge_type'] = 'N';
            $meta['state_code'] = $this->get_state_code(array('state'=> $meta['state']))[0]['state_code'];
            $meta['sub_total_amount'] = round( $meta['sub_total_amount'], 0); 
            $meta['total_taxable_value'] = round( $meta['total_taxable_value'], 0);
            $meta['sgst_total_tax_amount'] = round($meta['sgst_total_tax_amount'], 0);
            $meta['cgst_total_tax_amount'] = round($meta['cgst_total_tax_amount'], 0);
            $meta['igst_total_tax_amount'] = round($meta['igst_total_tax_amount'], 0);
            $meta['price_inword'] = convert_number_to_words(round($meta['sub_total_amount'], 0));
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
        $data['taxable_value'] = round($data['rate'] * $data['qty'],0);
        $data['hsn_code'] = STAND_HSN_CODE;
        
        array_push($data1, $data);
        
        $data2['description'] = "Iron Stand – Greater Than 32 Inches";
        $data2['rate'] = _247AROUND_BRACKETS_36_42_UNIT_PRICE;
        $data2['qty'] = $meta['_36_42_total'];
        $data2['taxable_value'] = round($data2['rate'] * $data2['qty'],0);
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
    
    function get_foc_invoice_data($vendor_id, $from_date_tmp, $to_date, $is_regenerate){
        $from_date = date('Y-m-d', strtotime('-1 months', strtotime($from_date_tmp)));
        $is_invoice_null = "";
        if($is_regenerate == 0){
            $is_invoice_null = " AND vendor_foc_invoice_id IS NULL ";
        }
        $sql = "SELECT DISTINCT round((`vendor_basic_charges`),2) AS rate,product_or_services,
                sc.gst_no as gst_number, ".HSN_CODE." AS hsn_code,
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
                sc.pan_no, contract_file, company_type, signature_file, sc.owner_phone_1, sc.district, sc.pincode

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
        if(!empty($result['booking'])){
            $result['upcountry'] =  $result['courier'] = $result['c_penalty'] = array();
            $result['d_penalty'] = $result['c_penalty'] = array();
            // Calculate Upcountry booking details
            $upcountry_data = $this->upcountry_model->upcountry_foc_invoice($vendor_id, $from_date, $to_date, $is_regenerate);
            $debit_penalty = $this->penalty_model->add_penalty_in_invoice($vendor_id, $from_date, $to_date, "", $is_regenerate);
            $courier = $this->get_sf_courier_charges($vendor_id, $from_date, $to_date, $is_regenerate);
            $credit_penalty = $this->penalty_model->get_removed_penalty($vendor_id, $from_date_tmp, "distinct" );
            $closed_date = "date_format(closed_date,'%d/%m/%Y') as closed_date";
            $misc_select = '"Misc" AS unit_id, "Completed" As internal_status,closed_date as closed_booking_date,"" As rating_stars,'
                    . '"0" AS customer_net_payable, partner_charge AS partner_net_payable, "" AS around_net_payable, "" AS owner_phone_1, "" AS primary_contact_phone_1, '
                    . 'booking_details.booking_id, booking_details.city, services, "" As appliance_category,'
                . '"" AS appliance_capacity, '.$closed_date.', description as price_tags, miscellaneous_charges.id AS misc_id, '
                . '(case when (product_or_services = "Service" )  THEN (round(vendor_basic_charges,2)) ELSE 0 END) as vendor_installation_charge, '
                . '(case when (product_or_services = "Product" )  THEN (round(vendor_basic_charges,2)) ELSE 0 END) as vendor_stand,'
                . 'vendor_basic_charges as total_booking_charge, product_or_services';
            $misc = $this->get_misc_charges_invoice_data($misc_select, "miscellaneous_charges.vendor_invoice_id IS NULL", $from_date, 
                    $to_date, "booking_details.assigned_vendor_id", $vendor_id, "vendor_basic_charges");
            if (!empty($upcountry_data)) {
                $up_country = array();
                $up_country[0]['description'] = 'Upcountry Charges';
                $up_country[0]['hsn_code'] = '';
                $up_country[0]['qty'] = '';
                $up_country[0]['rate'] = '';
                $up_country[0]['product_or_services'] = 'Upcountry';
                $up_country[0]['taxable_value'] = $upcountry_data[0]['total_upcountry_price'];
                $result['booking'] = array_merge($result['booking'], $up_country);
                $result['upcountry'] = $upcountry_data;
            }
            
            if(!empty($debit_penalty)){
                $d_penalty = array();
                $d_penalty[0]['description'] = 'Discount (Bookings not updated)';
                $d_penalty[0]['hsn_code'] = '';
                $d_penalty[0]['qty'] = '';
                $d_penalty[0]['rate'] = '';
                $d_penalty[0]['product_or_services'] = 'Debit Penalty';
                $d_penalty[0]['taxable_value'] = -(array_sum(array_column($debit_penalty, 'p_amount')));
                $result['booking'] = array_merge($result['booking'], $d_penalty);
                $result['d_penalty'] = $debit_penalty;
            }

            if (!empty($courier)) {
                $c_data = array();
                $c_data[0]['description'] = 'Courier Charges';
                $c_data[0]['hsn_code'] = '';
                $c_data[0]['qty'] = '';
                $c_data[0]['rate'] = '';
                $c_data[0]['product_or_services'] = 'Courier';
                $c_data[0]['taxable_value'] = (array_sum(array_column($courier, 'courier_charges_by_sf')));
                $result['booking'] = array_merge($result['booking'], $c_data);
                $result['courier'] = $courier;
            }
            
            if (!empty($credit_penalty)) {
                $cp_data = array();
                $cp_data[0]['description'] = 'Credit (Penalty Removed)';
                $cp_data[0]['hsn_code'] = '';
                $cp_data[0]['qty'] = '';
                $cp_data[0]['rate'] = '';
                $cp_data[0]['product_or_services'] = 'Credit Penalty';
                $cp_data[0]['taxable_value'] = (array_sum(array_column($credit_penalty, 'p_amount')));
                $result['booking'] = array_merge($result['booking'], $cp_data);
                $result['c_penalty'] = $credit_penalty;
            }
            
            if (!empty($misc)) {
                $m = array();
                $m[0]['description'] = 'Miscellaneous Charge';
                $m[0]['hsn_code'] = '';
                $m[0]['qty'] = '';
                $m[0]['rate'] = '';
                $m[0]['product_or_services'] = 'Misc Charge';
                $m[0]['taxable_value'] = (array_sum(array_column($misc, 'total_booking_charge')));
                $result['booking'] = array_merge($result['booking'], $m);
                $result['misc'] = $misc;
            }
        }

        return $result;
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
             foreach ($data['booking'] as $key => $value) {
                if(empty($data['booking'][0]['gst_number'])){
                    
                    $meta['invoice_template'] = "SF_FOC_Bill_of_Supply-v1.xlsx";
                    $data['booking'][$key]['toal_amount'] =sprintf("%1\$.2f",($value['taxable_value']));
                    
                } else if($c_s_gst){
                    $meta['invoice_template'] = "SF_FOC_Tax_Invoice-Intra_State-v1.xlsx";
                    
                    $data['booking'][$key]['cgst_rate'] =  $data['booking'][$key]['sgst_rate'] = 9;
                    $data['booking'][$key]['cgst_tax_amount'] = sprintf("%1\$.2f",($value['taxable_value'] * 0.09));
                    $data['booking'][$key]['sgst_tax_amount'] = sprintf("%1\$.2f",($value['taxable_value'] * 0.09));
                    $meta['cgst_total_tax_amount'] +=  $data['booking'][$key]['cgst_tax_amount'];
                    $meta['sgst_total_tax_amount'] += $data['booking'][$key]['sgst_tax_amount'];
                    $meta['sgst_tax_rate'] = $meta['cgst_tax_rate'] = 9;
                    $data['booking'][$key]['toal_amount'] = sprintf("%1\$.2f",($value['taxable_value'] + ($value['taxable_value'] * 0.18)));
                    
                } else {
                    $meta['invoice_template'] = "SF_FOC_Tax_Invoice_Inter_State_v1.xlsx";
                    
                    $data['booking'][$key]['igst_rate'] =  $meta['igst_tax_rate'] = DEFAULT_TAX_RATE;
                    $data['booking'][$key]['igst_tax_amount'] = sprintf("%1\$.2f",($value['taxable_value'] * 0.18));
                    $meta['igst_total_tax_amount'] +=  $data['booking'][$key]['igst_tax_amount'];
                    $data['booking'][$key]['toal_amount'] = sprintf("%1\$.2f",( $value['taxable_value'] + ($value['taxable_value'] * 0.18)));
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
                $meta['sub_total_amount'] +=  $data['booking'][$key]['toal_amount'];
                
                if($value['product_or_services'] == "Product"){
                    
                    $meta['total_parts_charge'] += $value['taxable_value'];
                    $parts_count += $value['qty'];
                    
                } else {
                    $meta['total_sc_charge'] += $value['taxable_value'];
                }
             }
            $meta['rcm'] = 0;
            if(empty($data['booking'][0]['gst_number'])){
                $meta['rcm'] = sprintf("%1\$.2f",( $meta['sub_total_amount'] * 0.18));
            }
            $meta['parts_count'] = $parts_count;
            $meta['reverse_charge'] = 0;
            $meta['reverse_charge_type'] = 'N';
            $meta['total_taxable_value'] = sprintf("%1\$.2f",$meta['total_taxable_value']);
            $meta['cgst_total_tax_amount'] = sprintf("%1\$.2f",$meta['cgst_total_tax_amount']);
            $meta['sgst_total_tax_amount'] = sprintf("%1\$.2f",$meta['sgst_total_tax_amount']);
            $meta['igst_total_tax_amount'] = sprintf("%1\$.2f",$meta['igst_total_tax_amount']);
            $meta['sub_total_amount'] = round($meta['sub_total_amount'], 0);
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
                        . "+ `around_comm_extra_charges` + `around_st_extra_charges` + `around_comm_parts`  + `around_st_parts`)  AS toal_amount, ";
                $where = " AND ( ( ud.vendor_to_around > 0 AND ud.around_to_vendor =0 ) OR (ud.vendor_to_around = 0 AND ud.around_to_vendor =0 ) )  ";
            } else {
                $select = "SUM(`around_comm_extra_charges` + `around_st_extra_charges` + `around_comm_parts`  + `around_st_parts`) As toal_amount,";
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
        
        if (!empty($result[0][0]['toal_amount']) && !empty($result[1][0]['toal_amount'])) {
            $data = array_merge($result[0], $result[1]);
        } else if (!empty($result[0][0]['toal_amount']) && empty($result[1][0]['toal_amount'])) {
            $data = $result[0];
        } else if (empty($result[0][0]['toal_amount']) && !empty($result[1][0]['toal_amount'])) {
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
            $commission_charge[0]['description'] = "Commission Charge";
            $total_amount_invoice = (array_sum(array_column($data, 'toal_amount')));
            if ($total_amount_invoice > 0) {
                $commission_charge[0]['toal_amount'] = $total_amount_invoice;

                $meta['upcountry_charge'] = $meta['upcountry_booking'] = $meta['upcountry_distance'] = $meta['total_sgst_tax_amount'] = $meta['total_cgst_tax_amount'] = $meta['total_igst_tax_amount'] = $meta['igst_tax_rate'] = $meta['sgst_tax_rate'] = $meta['sgst_tax_rate'] = 0;
                $from_date_tmp = date('Y-m-d', strtotime('-1 months', strtotime($from_date)));
                $upcountry_data = $this->upcountry_model->upcountry_cash_invoice($vendor_id, $from_date_tmp, $to_date, $is_regenerate);
                if (!empty($upcountry_data)) {
                    $commission_charge[0]['toal_amount'] += $upcountry_data[0]['total_upcountry_price'];
                    $meta['upcountry_charge'] = $upcountry_data[0]['total_upcountry_price'];
                    $meta['upcountry_booking'] = $upcountry_data[0]['total_booking'];
                    $meta['upcountry_distance'] = $upcountry_data[0]['total_distance'];
                }

                $tax_charge = $this->booking_model->get_calculated_tax_charge($commission_charge[0]['toal_amount'], DEFAULT_TAX_RATE);
                $commission_charge[0]['taxable_value'] = round($commission_charge[0]['toal_amount'] - $tax_charge, 0);
                $c_s_gst = $this->check_gst_tax_type($meta['state']);
                $meta['cgst_tax_rate'] = $meta['sgst_tax_rate'] = $meta['cgst_total_tax_amount'] = $meta['sgst_total_tax_amount'] = $meta['total_igst_tax_amount'] = $meta['igst_tax_rate'] = $meta['igst_total_tax_amount'] = 0;
                if ($c_s_gst) {
                    $meta['invoice_template'] = "247around_Tax_Invoice_Intra_State.xlsx";
                    $commission_charge[0]['cgst_rate'] = $commission_charge[0]['sgst_rate'] = $meta['sgst_tax_rate'] = $meta['cgst_tax_rate'] = 9;
                    $commission_charge[0]['cgst_tax_amount'] = $commission_charge[0]['sgst_tax_amount'] = $meta['cgst_total_tax_amount'] = $meta['sgst_total_tax_amount'] = round($tax_charge / 2, 0);
                } else {
                    $meta['invoice_template'] = "247around_Tax_Invoice_Inter_State.xlsx";
                    $commission_charge[0]['igst_tax_amount'] = $meta['igst_total_tax_amount'] = round($tax_charge, 0);
                    $commission_charge[0]['igst_rate'] = $meta['igst_tax_rate'] = DEFAULT_TAX_RATE;
                }

                $meta['reverse_charge_type'] = "N";
                $meta['reverse_charge'] = '';
                $meta['invoice_type'] = 'Tax Invoice';

                $meta['total_qty'] = $meta['total_rate'] = $commission_charge[0]['qty'] = $commission_charge[0]['rate'] = "";
                $commission_charge[0]['hsn_code'] = COMMISION_CHARGE_HSN_CODE;
                $meta['total_taxable_value'] = $commission_charge[0]['taxable_value'];
                $meta['sub_total_amount'] = round($commission_charge[0]['toal_amount'], 0);

                $meta['price_inword'] = convert_number_to_words($meta['sub_total_amount']);
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
                return FALSE;
            }
        } else {
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
            foreach ($commission_charge as $key => $value) {
                $commission_charge[$key]['rate'] = round($value['taxable_value']/$value['qty'], 0);
                $meta['sub_total_amount'] += $value['taxable_value'];
                $meta['total_qty'] += $value['qty'];
            }
            
            $meta['sub_total_amount'] = round($meta['sub_total_amount'], 0);
            $meta['invoice_template'] = "Buyback-v1.xlsx"; 
            
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
            
            $data1['meta'] = $meta;
            $data1['booking'] = $commission_charge;
            $data1['annexure_data'] = $annexure_data;
            
            return $data1;
            
        } else{
            return FALSE;
        }
    }
    
    function _buyback_invoice_query($vendor_id, $from_date, $to_date, $is_regenerate, $is_unit, $profitLoss){
        $is_foc_null = "";
        if ($is_regenerate == 0) {
                $is_foc_null = " AND cp_invoice_id IS NULL ";
        }
        if($profitLoss == 1){
            $profit_loss_where = ' AND CASE WHEN (cp_claimed_price > 0) THEN ((`partner_basic_charge` + `partner_tax_charge` + `partner_sweetner_charges`) <=  (cp_claimed_price)) ELSE ((`partner_basic_charge` + `partner_tax_charge` + `partner_sweetner_charges`) <=  (`cp_basic_charge` + cp_tax_charge)) END ';
        } else {
            $profit_loss_where = ' AND CASE WHEN (cp_claimed_price > 0) THEN ((`partner_basic_charge` + `partner_tax_charge` + `partner_sweetner_charges`) >  (cp_claimed_price)) ELSE ((`partner_basic_charge` + `partner_tax_charge` + `partner_sweetner_charges`) >  (`cp_basic_charge` + cp_tax_charge)) END ';
        }
        $select = " COUNT(bb_unit_details.id) as qty, SUM(CASE WHEN ( bb_unit_details.cp_claimed_price > 0) 
                THEN (round(bb_unit_details.cp_claimed_price,0)) 
                ELSE (round(bb_unit_details.cp_basic_charge + cp_tax_charge,0)) END ) AS taxable_value, concat('Used ',services) as description, 
                CASE WHEN (bb_unit_details.service_id = 46) THEN (8528) 
                WHEN (bb_unit_details.service_id = 50) THEN (8415)
                WHEN (bb_unit_details.service_id = 28) THEN (8450)
                WHEN (bb_unit_details.service_id = 37) THEN (8418) ELSE '' END As hsn_code, owner_phone_1, gst_no,
                sc.company_name, sc.address as company_address, sc.state,
                sc.owner_email, sc.primary_contact_email, sc.owner_phone_1";
        $group_by = " GROUP BY bb_unit_details.service_id ";
        if($is_unit){
            $select = " bb_unit_details.id AS unit_id,bb_unit_details.gst_amount, CASE WHEN ( bb_unit_details.cp_claimed_price > 0) 
                THEN (round(bb_unit_details.cp_claimed_price,0)) 
                ELSE (round(bb_unit_details.cp_basic_charge + cp_tax_charge,0)) END AS cp_charge,partner_tracking_id, city,order_key,
                CASE WHEN(acknowledge_date IS NOT NULL) 
                THEN (DATE_FORMAT( acknowledge_date,  '%d-%m-%Y' ) ) ELSE (DATE_FORMAT(delivery_date,  '%d-%m-%Y' )) END AS delivery_date, order_date,
                order_date, services, bb_order_details.partner_order_id";
            $group_by = "";
        }
        
        $sql = "SELECT $select
                
                
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
     * @desc This method returns booking id and curier charges for completed booking
     * @param String $vendor_id
     * @param String $from_date
     * @param String $to_date
     * @return Array
     */
    function get_sf_courier_charges($vendor_id, $from_date, $to_date, $is_regenerate) {
        $invoice_check = "";
        if($is_regenerate == 0){
            $invoice_check .= "AND vendor_foc_invoice_id IS NULL ";
        }
        $sql = " SELECT bd.booking_id, courier_charges_by_sf 
                FROM  booking_details as bd, booking_unit_details as ud,
                spare_parts_details as sp
                WHERE 
                ud.booking_status =  'Completed'
                AND bd.assigned_vendor_id = '$vendor_id'
                AND  status = 'Completed'
                AND sp.booking_id = bd.booking_id
                AND bd.booking_id = ud.booking_id
                AND ud.ud_closed_date >=  '$from_date'
                AND ud.ud_closed_date <  '$to_date'
                AND vendor_foc_invoice_id IS NULL 
                AND pay_to_sf = '1'
                AND `approved_defective_parts_by_partner` = 1
                $invoice_check
                AND courier_charges_by_sf > 0 ";

        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    function get_partner_courier_charges($partner_id, $from_date, $to_date){
      
        
        $sql = " SELECT bd.order_id, bd.booking_id,services,
                courier_charges_by_sf, bd.city,
                CASE WHEN (defective_courier_receipt IS NOT NULL) THEN 
                 (concat('".S3_WEBSITE_URL."misc-images/',defective_courier_receipt)) ELSE '' END AS courier_receipt_link
                FROM  booking_details as bd, booking_unit_details as ud,
                spare_parts_details as sp,services
                
                WHERE 
                ud.booking_status =  'Completed'
                AND bd.partner_id = '$partner_id'
                AND ud.partner_id = '$partner_id'
                AND status = 'Completed'
                AND services.id = ud.service_id
                AND sp.booking_id = bd.booking_id
                AND bd.booking_id = ud.booking_id
                AND ud.ud_closed_date >=  '$from_date'
                AND ud.ud_closed_date <  '$to_date'
                AND `approved_defective_parts_by_partner` = 1
                AND partner_invoice_id IS NULL
                AND courier_charges_by_sf > 0 ";

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
    
    function warehouse_inventory_ledger_invoice_details($warehouse_id, $from_date, $to_date, $is_regenerate){
        $this->db->_reserved_identifiers = array('*','""');
        $this->db->select('"" As awb_no, "" As courier_name, "" AS courier_receipt_link, l.inventory_id`,part_name, quantity, l.id, CAST(l.create_date AS DATE) as create_date, l.booking_id');
        $this->db->from("inventory_ledger as l");
        $this->db->join('inventory_master_list as m', 'm.inventory_id = l.inventory_id');
        $this->db->where('sender_entity_id', $warehouse_id);
        $this->db->where('sender_entity_type', "vendor");
        $this->db->where('l.create_date >= ', $from_date);
        $this->db->where('l.create_date < ', $to_date);
        $this->db->where_not_in('l.inventory_id', array(1,2));
        if(empty($is_regenerate)){
            $this->db->where('vendor_warehouse_invoice_id IS NULL', NULL);
        }

        $query = $this->db->get();
        return $query->result_array();
    }
    /**
     * @desc This is used to get warehouse invoice data(both main and annexure)
     * @param String $vendor_id
     * @param String $from_date_tmp
     * @param String $to_date
     * @param int $is_regenerate
     * @return boolean
     */
    function get_warehouse_invoice_data($vendor_id, $from_date_tmp, $to_date, $is_regenerate){
        $from_date = date('Y-m-d', strtotime('-1 months', strtotime($from_date_tmp)));
            
        // For Monthly warehouse charges
        $fm =$this->get_fixed_warehouse_charge(array('entity_type' => "vendor", "entity_id" => $vendor_id, "charges_type" => FIXED_MONTHLY_WAREHOUSE_CHARGES_TAG));
        $result['booking'] = array();
        $result['annexure'] = array();
        
        if(!empty($fm)){
            $wfm = array();
            $wfm[0]['description'] = $fm[0]['description'];
            $wfm[0]['part_name'] = "";
            $wfm[0]['booking_id'] = "";
            $wfm[0]['hsn_code'] = $fm[0]['hsn_code'];
            $wfm[0]['rate'] = 0;
            $wfm[0]['qty'] = 1;
            $wfm[0]['part_name'] = $fm[0]['description'];
            $wfm[0]['gst_rate'] = $fm[0]['gst_rate'];
            $wfm[0]['product_or_services'] = $fm[0]['description'];
            $wfm[0]['taxable_value'] = $fm[0]['fixed_charges'];
            $wfm[0]['courier_receipt_link'] = "";
            $wfm[0]['courier_name'] = "";
            $wfm[0]['awb_no'] = "";

            $result['booking'] = array_merge($result['booking'], $wfm);
            $result['annexure'] = array_merge($result['annexure'], $wfm);
           
        }
        // For the packaging
        $packaging =$this->get_fixed_warehouse_charge(array('entity_type' => "vendor", "entity_id" => $vendor_id, "charges_type" => PACKAGING_RATE_TAG));
        $ledgerData = $this->warehouse_inventory_ledger_invoice_details($vendor_id, $from_date, $to_date, $is_regenerate);
        if(!empty($ledgerData) && !empty($packaging)){
            $wp = array();
            $wp[0]['description'] = $packaging[0]['description'];
            $wp[0]['hsn_code'] = $packaging[0]['hsn_code'];
            $wp[0]['rate'] = $packaging[0]['fixed_charges'];
            $wp[0]['qty'] = (array_sum(array_column($ledgerData, 'quantity')));
            $wp[0]['gst_rate'] = $packaging[0]['gst_rate'];
            $wp[0]['product_or_services'] = 'Packaging';
            $wp[0]['taxable_value'] = $packaging[0]['fixed_charges'] * $wp[0]['qty'];
            
            $wp[0]['booking_id'] = "";
            
            foreach ($ledgerData as $key => $value) {
                $ledgerData[$key]['taxable_value'] = $value['quantity'] * $packaging[0]['fixed_charges'];
            }
            
            $result['booking'] = array_merge($result['booking'], $wp);
            $result['annexure'] = array_merge($result['annexure'], $ledgerData);
            $result['inventory_ledger'] = $ledgerData;
        }
        // Courier
        $getcourier1 = $this->get_courier_details("*, courier_charge as taxable_value, 'Courier Charge' as part_name,"
                . "AWB_no as awb_no, courier_name, concat('".S3_WEBSITE_URL."vendor-partner-docs/',courier_file) as courier_receipt_link", 
                array('sender_entity_type' => "vendor", 
            "sender_entity_id" => $vendor_id, "courier_charge > 0" => NULL,
            "create_date >= '".$from_date."' " => NULL, "create_date < '".$to_date."' " => NULL,
            "sender_invoice_id IS NULL " => NULL));
        $courier = array();
        if(!empty($getcourier1)){
            
            $courier[0]['description'] = "Courier Charge";
            $courier[0]['hsn_code'] = HSN_CODE;
            $courier[0]['rate'] = 0;
            $courier[0]['qty'] = (array_sum(array_column($getcourier1, 'quantity')));
            $courier[0]['gst_rate'] = DEFAULT_TAX_RATE;
            $courier[0]['product_or_services'] = "Courier Charge";
            $courier[0]['taxable_value'] = (array_sum(array_column($getcourier1, 'courier_charge')));
            $result['annexure'] = array_merge($result['annexure'], $getcourier1);
            $result['courier_details'] = $getcourier1;

        }

        $sp_data = $this->partner_model->get_spare_parts_by_any('id,booking_id, spare_parts_details.partner_id as sender_entity_id, '
                . 'courier_price_by_partner as taxable_value, '
                . 'courier_name_by_partner as courier_name, awb_by_partner as awb_no,'
                . '"" as courier_receipt_link,'
                . '"Courier Charge" as part_name', 
                array('partner_id' => $vendor_id, "entity_type" => "vendor", 
                    "courier_price_by_partner > 0" => NULL, 
                    "status != 'Cancelled'" => NULL,
                    "warehouse_courier_invoice_id IS NULL" => NULL,
                    "create_date >= '".$from_date."' " => NULL,
                    "create_date < '".$to_date."' " => NULL));
        
        if(!empty($sp_data)){
            $qty = count($sp_data);
            if(!empty($courier)){
                $courier[0]['qty'] = $courier[0]['qty'] + $qty;
                $courier[0]['taxable_value'] = $courier[0]['taxable_value'] + (array_sum(array_column($sp_data, 'taxable_value')));
            } else {
                $courier[0]['description'] = "Courier Details";
                $courier[0]['hsn_code'] = HSN_CODE;
                $courier[0]['rate'] = 0;
                $courier[0]['qty'] = $qty;
                $courier[0]['gst_rate'] = DEFAULT_TAX_RATE;
                $courier[0]['product_or_services'] = "Courier Charge";
                $courier[0]['taxable_value'] = (array_sum(array_column($sp_data, 'taxable_value')));
            }
            $result['annexure'] = array_merge($result['annexure'], $sp_data);
            $result['spare_courier'] = $sp_data;  
        }
        
        if(!empty($courier)){

            $result['booking'] = array_merge($result['booking'], $courier);
        }
        
        if(!empty($result['booking'] )){
            return $result;
        } else {
            return false;
        }
    }
    
    function get_courier_details($select, $where){
        
        $this->db->select($select, FALSE);
        $this->db->where($where);
        $query = $this->db->get('courier_details');
        return $query->result_array();
    }
    
    function get_fixed_warehouse_charge($where){
        $this->db->select('*');
        $this->db->where($where);
        $query = $this->db->get('vendor_partner_varialble_charges');
        return $query->result_array();
    }
    
    function get_misc_charges_invoice_data($select, $vendor_partner_invoice, 
            $from_date, $to_date, $vendor_partner,$vendor_partner_id, $sf_partner_charge){
        $this->db->select($select, false);
        $this->db->from('miscellaneous_charges');
        $this->db->join('booking_details', 'booking_details.booking_id = miscellaneous_charges.booking_id');
        $this->db->join('services', 'booking_details.service_id = services.id');
        $this->db->where('booking_details.current_status', _247AROUND_COMPLETED);
        $this->db->where($vendor_partner_invoice, NULL);
        $this->db->where("active", 1);
        $this->db->where($sf_partner_charge. " > 0", NULL);
        $this->db->where('booking_details.closed_date >= ', $from_date );
        $this->db->where('booking_details.closed_date < ', $to_date );
        $this->db->where($vendor_partner, $vendor_partner_id );
        $query = $this->db->get();
        return $query->result_array();
    }

}
