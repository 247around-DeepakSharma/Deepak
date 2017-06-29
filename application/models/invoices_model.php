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
    function getInvoicingData($data) {
        $this->db->where($data);
        $this->db->order_by('from_date');
        $query = $this->db->get('vendor_partner_invoices');

        return $query->result_array();
    }

    /**
     * Get partner Email id
     * @param type $partnerId
     */
    function getEmailIdForPartner($partnerId) {
        $this->db->select('partner_email_for_to');
        $this->db->where('partner_id', $partnerId);
        $query = $this->db->get('bookings_sources');
        return $query->result_array();
    }

    //Function to insert banks account/statement
    function bankAccountTransaction($account_statement) {
        $this->db->insert('bank_transactions', $account_statement);
        return $this->db->insert_id();
    }

    function get_bank_transactions_details($data) {
        $this->db->where($data);
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
                . "partner_vendor_id = '$partner_vendor_id' ";
        $data = $this->db->query($sql);
        return $data->result_array();
    }

    /**
     * @desc: This funtion is used to get invoicing summary for vendor or partner.
     * @param: String ( vendor or patner)
     * @return: Array()
     */
    function getsummary_of_invoice($vendor_partner, $vendor_type="") {
        $array = array();

        if ($vendor_partner == "vendor") {
            switch ($vendor_type){
                case 'active':
                    $data = $this->vendor_model->getActiveVendor("", 1);
                    break;
                case 'disabled':
                    $data = $this->vendor_model->getActiveVendor("", 'disabled');
                    break;
                case 'all':
                    $data = $this->vendor_model->getActiveVendor("", 0);
                    break;
                default:
                    $data = $this->vendor_model->getActiveVendor("", 0);
            }
            
            $due_date_status = " AND `due_date` <= CURRENT_DATE() ";
        } else if ($vendor_partner == "partner") {
            switch ($vendor_type){
                case 'active':
                    $data = $this->partner_model->getpartner();
                    break;
                case 'disabled':
                    $data = $this->partner_model->get_all_partner("",0);
                    break;
                case 'all':
                    $data = $this->partner_model->get_all_partner();
                    break;
                default:
                    $data = $this->partner_model->getpartner();
            }
            
            $due_date_status = "";
        }

        foreach ($data as $value) {


            $sql = "SELECT COALESCE(SUM(`amount_collected_paid` ),0) as amount_collected_paid, "
                    . " CASE WHEN (SELECT count(id) FROM vendor_partner_invoices "
                    . " WHERE type_code ='A' AND type = 'Stand' AND `settle_amount` = 0 AND vendor_partner_id = $value[id] "
                    . " AND vendor_partner = '$vendor_partner' $due_date_status ) "
                    . " THEN(1) ELSE 0 END as is_stand FROM  `vendor_partner_invoices` "
                    . " WHERE vendor_partner_id = $value[id] AND vendor_partner = '$vendor_partner' $due_date_status";


            $data = $this->db->query($sql);
            $result = $data->result_array();
           
            $bank_transactions = $this->getbank_transaction_summary($vendor_partner, $value['id']);

            $result[0]['vendor_partner'] = $vendor_partner;

            if (isset($value['name'])) {
                $result[0]['name'] = $value['name'];
                $result[0]['on_off'] = $value['on_off'];
                $result[0]['active'] = $value['active'];
                $result[0]['is_verified'] = $value['is_verified'];
                $where = "service_center_id = '" . $value['id'] . "' AND approved_defective_parts_by_partner = '0' "
                        . " AND parts_shipped IS NOT NULL ";

                $result[0]['count_spare_part'] = count($this->partner_model->get_spare_parts_booking($where));
            } else if (isset($value['public_name'])) {
                $result[0]['name'] = $value['public_name'];
                $result[0]['address'] = $value['address'];
                $result[0]['pincode'] = $value['pincode'];
                $result[0]['district'] = $value['district'];
                $result[0]['state'] = $value['state'];
                $result[0]['seller_code'] = $value['seller_code'];
                $result[0]['invoice_email_cc'] = $value['invoice_email_cc'];
                $result[0]['invoice_email_to'] = $value['invoice_email_to'];
                $result[0]['is_verified'] = 1;
            }

            $result[0]['id'] = $value['id'];
            $result[0]['final_amount'] = $result[0]['amount_collected_paid'] - $bank_transactions[0]['credit_amount'] + $bank_transactions[0]['debit_amount'];

            array_push($array, $result[0]);
        }

        return $array;
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
     * @desc : get all vendor invoice for previous month. it get both type A and type B invoice
     *
     * @param: void
     * @return : array
     */
    function generate_vendor_foc_detailed_invoices($vendor_id, $date_range, $is_regenerate) {

        $custom_date = explode("-", $date_range);
        $from_date = $custom_date[0];
        $to_date_tmp = $custom_date[1];
        $to_date = date('Y-m-d', strtotime('+1 day', strtotime($to_date_tmp)));
        $result1 = array();
        
        $is_invoice_null = "";
        if($is_regenerate == 0){
            $is_invoice_null =  " AND vendor_foc_invoice_id IS NULL ";
        }

        //for FOC invoice, around_to_vendor > 0 AND vendor_to_around = 0
        $where = " AND `booking_unit_details`.around_to_vendor > 0  AND `booking_unit_details`.vendor_to_around = 0 ";

        $where .= " AND pay_to_sf = '1'  $is_invoice_null  AND booking_unit_details.ud_closed_date >= '$from_date' AND booking_unit_details.ud_closed_date < '$to_date' ";
        $date = "  '$from_date' as start_date,  '" . date('Y-m-d', strtotime($to_date . " - 1 day")) . "'  as end_date,  ";
        
        $condition = "  From booking_details, booking_unit_details, services, service_centres
                          WHERE `booking_details`.booking_id = `booking_unit_details`.booking_id AND `services`.id = `booking_details`.service_id  AND `booking_details`.assigned_vendor_id = `service_centres`.id AND current_status = 'Completed' AND assigned_vendor_id = '" . $vendor_id . "' AND `booking_unit_details`.booking_status = 'Completed' $where ";

        $sql1 = "SELECT  booking_unit_details.id AS unit_id,service_centres.state, `booking_details`.booking_id, 
                    `booking_details`.city, `booking_details`.internal_status,
		     date_format(`booking_unit_details`.`ud_closed_date`,'%d/%m/%Y') as closed_date, 
                     `booking_unit_details`.ud_closed_date as closed_booking_date, 
                      rating_stars, `booking_unit_details`.price_tags,
		     `booking_unit_details`.appliance_category, 
                     `booking_unit_details`.appliance_capacity,
                     `services`.services,
		      customer_net_payable, partner_net_payable,
		     `service_centres`.company_name, `service_centres`.id, `service_centres`.sc_code, `service_centres`.address,
		     `service_centres`.beneficiary_name, `service_centres`.bank_account, `service_centres`.bank_name,
		     `service_centres`.ifsc_code,  `service_centres`.owner_email,  `service_centres`.primary_contact_email, `service_centres`.owner_phone_1,
		     `service_centres`.primary_contact_phone_1, `booking_unit_details`.  product_or_services, `booking_unit_details`.around_paid_basic_charges as around_net_payable,
		     (customer_net_payable + partner_net_payable + around_net_payable) as total_booking_charge, service_tax_no,
                     (case when (service_centres.tin_no IS NOT NULL )  THEN tin_no ELSE cst_no END) as tin, pan_no, contract_file, company_type, service_centres.pan_no

                     ,$date

                     /* get sum of vat charges if product_or_services is product else sum of vat is zero  */

                     (case when (`booking_unit_details`.product_or_services = 'Product' AND (service_centres.tin_no IS NOT NULL OR service_centres.cst_no IS NOT NULL )  )  THEN ( round(vendor_st_or_vat_basic_charges,0)) ELSE 0 END) as vendor_vat,
                    
                     /* get sum of st charges if product_or_services is Service else sum of vat is zero  */

                     (case when (`booking_unit_details`.product_or_services = 'Service'  AND `service_tax_no` IS NOT NULL )  THEN (round(vendor_st_or_vat_basic_charges,0)) ELSE 0 END) as vendor_st,
                    
                     /* get installation charge if product_or_services is Service else installation_charge is zero
                      * installation charge is the sum of around_comm_basic_charge and vendor_basic_charge
                      */
 
                     (case when (`booking_unit_details`.product_or_services = 'Service' )  THEN (round(vendor_basic_charges,0)) ELSE 0 END) as vendor_installation_charge,
                     
                      /* get stand charge if product_or_services is Product else stand charge is zero
                      * Stand charge is the sum of around_comm_basic_charge and vendor_basic_charge
                      */


                     (case when (`booking_unit_details`.product_or_services = 'Product' )  THEN (round(vendor_basic_charges,0)) ELSE 0 END) as vendor_stand,

		        (SELECT ROUND(AVG(case when rating_stars > 0  then rating_stars else null
                                end),1) $condition ) AS avg_rating

                    $condition ";

        $query1 = $this->db->query($sql1);
        $result1['invoice_details'] = $query1->result_array();
        // Calculate Upcountry booking details
        $upcountry_data = $this->upcountry_model->upcountry_foc_invoice($vendor_id, $from_date, $to_date);
        if (!empty($upcountry_data)) {
            $result1['upcountry_details'] = $upcountry_data;
        }

        return $result1;
    }

    function get_vendor_cash_detailed($vendor_id, $date_range, $is_regenerate) {
        $custom_date = explode("-", $date_range);
        $from_date = $custom_date[0];
        $to_date_tmp = $custom_date[1];
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

            $sql = "SELECT booking_unit_details.id AS unit_id, service_centres.state, "
                    . "`booking_details`.booking_id, "
                    . "`booking_details`.city,"
                    . " date_format(`booking_unit_details`.`ud_closed_date`,'%d/%m/%Y') as closed_date,"
                    . "`booking_unit_details`.ud_closed_date as closed_booking_date, "
                    . " `booking_unit_details`.price_tags, "
                    . "`booking_unit_details`.appliance_category,"
                    . "rating_stars,"
                    . " `booking_unit_details`.appliance_capacity, 
                    services,`service_centres`.company_name, 
                    `service_centres`.id, `service_centres`
                    .sc_code, `service_centres`.address,
		     `service_centres`.beneficiary_name,
                     `service_centres`.bank_account, 
                     `service_centres`.bank_name,
		     `service_centres`.ifsc_code,  
                     `service_centres`.owner_email,  
                     `service_centres`.primary_contact_email, 
                     `service_centres`.owner_phone_1,
                     `service_centres`.primary_contact_phone_1,
                     service_tax_no,(case when (service_centres.tin_no IS NOT NULL )  THEN tin_no ELSE cst_no END) as tin,
                     `booking_unit_details`.  product_or_services, '$from_date' as start_date,  '" . date('Y-m-d', strtotime($to_date . " - 1 day")) . "'  as end_date,"
                    . " around_net_payable, "
                    . " $select (around_comm_extra_charges + around_st_extra_charges) as additional_charges,"
                    . " (around_comm_parts + around_st_parts) AS parts_cost, "
                    . " (customer_paid_basic_charges + customer_paid_extra_charges + customer_paid_parts) as amount_paid  "
                    . " From booking_details, booking_unit_details, services, service_centres
                    WHERE `booking_details`.booking_id = `booking_unit_details`.booking_id AND `services`.id = `booking_details`.service_id  
                    AND `booking_details`.assigned_vendor_id = `service_centres`.id AND current_status = 'Completed' 
                    $is_invoice_null
                    AND assigned_vendor_id = '" . $vendor_id . "' "
                    . " AND `booking_unit_details`.booking_status = 'Completed' $where";


            $query = $this->db->query($sql);
            $invoice[$i] = $query->result_array();
        }
        $result = array_merge($invoice[0], $invoice[1]);
        $upcountry_data = $this->upcountry_model->upcountry_cash_invoice($vendor_id, $from_date, $to_date);

        $result1 = array_merge($result, $upcountry_data);
        if (count($result1) > 0) {

            $meta['r_sc'] = $meta['r_asc'] = $meta['r_pc'] = $meta['total_amount_paid'] = $rating = 0;
            $i = 0;
            foreach ($result1 as $value) {
                $meta['r_sc'] += $value['service_charges'];
                $meta['r_asc'] += $value['additional_charges'];
                $meta['r_pc'] += $value['parts_cost'];
                $meta['total_amount_paid'] += $value['amount_paid'];
                if (!is_null($value['rating_stars']) || $value['rating_stars'] != '') {
                    $rating += $value['rating_stars'];
                    $i++;
                }
            }
            $meta['total_amount_paid'] = round($meta['total_amount_paid'], 0);
            $meta['r_total'] = round($meta['r_sc'] + $meta['r_asc'] + $meta['r_pc'], 0);
            $meta['r_st'] = $this->booking_model->get_calculated_tax_charge($meta['r_total'], 15);
            if ($i == 0) {
                $i = 1;
            }
            $meta['t_rating'] = $rating / $i;
            $data['booking'] = $result1;
            if (!empty($upcountry_data)) {

                $data['upcountry'] = $upcountry_data[0];
            }

            $data['meta'] = $meta;

            return $data;
        } else {
            return false;
        }
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

        $sql1 = "SELECT booking_unit_details.id AS unit_id,`booking_details`.service_id, `booking_details`.booking_id, "
                . " `booking_details`.reference_date,invoice_email_to,invoice_email_cc, booking_details.rating_stars,  "
                . " `booking_details`.partner_id, `booking_details`.source,"
                . " `booking_details`.city, `booking_unit_details`.ud_closed_date as closed_date, "
                . "  price_tags, `partners`.company_name,partners.seller_code, "
                . " `partners`.address as company_address, "
                . " `booking_unit_details`.appliance_capacity, partners.state, partners.district, partners.pincode,"
                . "  booking_details.booking_primary_contact_no,  "
                . " `services`.services, users.name, "
                . " '$from_date' as start_date,  "
                . " '$to_date'  as end_date,

             (case when (`booking_unit_details`.product_or_services = 'Service' ) 
                 THEN (ROUND(partner_net_payable,2) ) 
                 ELSE 0 END) as installation_charge,
              (case when( order_id !='') THEN order_id when(booking_details.partner_id= '247010') 
              THEN (partner_serial_number) ELSE '' END ) AS order_id,

             (case when (`booking_unit_details`.product_or_services = 'Service' ) 
             THEN (ROUND(partner_net_payable * 0.15,2) ) 
             ELSE 0 END) as st,
             
            ( CASE WHEN (booking_unit_details.product_or_services = 'Service')
            THEN (ROUND(partner_net_payable,2) +  Round(partner_net_payable *0.15, 2))
            WHEN (booking_unit_details.product_or_services = 'Product') 
            THEN ( Round(partner_net_payable,2) + Round(partner_net_payable * 0.05,2))
            ELSE 0 END) AS partner_paid_basic_charges,
             
            
             (case when (`booking_unit_details`.product_or_services = 'Product' )  
             THEN (ROUND(partner_net_payable,2) ) 
             ELSE 0 END) as stand,

              (case when (`booking_unit_details`.product_or_services = 'Product' )  
               THEN (ROUND(partner_net_payable * 0.05,2) ) 
               ELSE 0 END) as vat

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
        $result1['main_invoice'] = $query1->result_array();
        $result1['upcountry_invoice'] = $this->upcountry_model->upcountry_partner_invoice($partner_id, $from_date, $to_date);

        return $result1;
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
        $bank_statement = $this->invoices_model->get_bank_transactions_details($data2);

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
     * @param String $temp_invoice_id
     * @return Array
     */
    function get_invoices_details($where) {

        $sql = "SELECT *  FROM `vendor_partner_invoices` WHERE $where ORDER BY `id` DESC";
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            return $query->result_array();
        } else {
            return array();
        }
    }

    /**
     * @desc: This is used to generate Partner Main invoice. 
     * @param String $partner_id
     * @param String $from_date_tmp
     * @param String $to_date
     * @return Array
     */
    function generate_partner_invoice($partner_id, $from_date_tmp, $to_date_tmp, $upcountry_flag = true) {
        $from_date = date('Y-m-d', strtotime('-1 months', strtotime($from_date_tmp)));
        $to_date = date('Y-m-d', strtotime('+1 day', strtotime($to_date_tmp)));
        log_message("info", $from_date . "- " . $to_date);
        // For Product
        $sql = "SELECT DISTINCT (`partner_net_payable`) AS p_rate, '' AS upcountry_charges,  '' AS s_service_charge, '' AS s_total_service_charge,
                5.00 AS p_tax_rate, 
                CASE 
               
                    WHEN MIN( ud.`appliance_capacity` ) = '' AND MAX( ud.`appliance_capacity` ) = '' THEN
                    concat(services,' ', price_tags )

                    WHEN MIN( ud.`appliance_capacity` ) = '' AND MAX( ud.`appliance_capacity` ) != '' THEN 
                    concat(services,' ', price_tags,' (', 
                    MAX( ud.`appliance_capacity` ),') ' )

                    WHEN MIN( ud.`appliance_capacity` ) = MAX( ud.`appliance_capacity` ) THEN 
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
                (partner_net_payable * COUNT( ud.`appliance_capacity` )) AS p_part_cost,
                `partners`.company_name,
                `partners`.address as company_address, partners.pincode, partners.district,
                `partners`.state,
                `partners`.seller_code
                FROM  `booking_unit_details` AS ud, services, partners
                WHERE  `product_or_services` =  'Product'
                AND  `partner_net_payable` >0
                AND ud.partner_id =  '$partner_id'
                AND ud.booking_status =  'Completed'
                AND ud.ud_closed_date >=  '$from_date'
                AND ud.ud_closed_date < '$to_date'
                AND ud.service_id = services.id
                AND partners.id = ud.partner_id
                AND partner_invoice_id IS NULL
                GROUP BY  `partner_net_payable`, ud.service_id,price_tags   ";

        $query = $this->db->query($sql);
        $product = $query->result_array();

        $sql1 = "SELECT DISTINCT (`partner_net_payable`) AS s_service_charge, '' AS upcountry_charges, '' AS p_tax_rate, '' AS p_rate, ''AS p_part_cost,
               CASE 
               
                    WHEN MIN( ud.`appliance_capacity` ) = '' AND MAX( ud.`appliance_capacity` ) = '' THEN
                    concat(services,' ', price_tags )

                    WHEN MIN( ud.`appliance_capacity` ) = '' AND MAX( ud.`appliance_capacity` ) != '' THEN 
                    concat(services,' ', price_tags,' (', 
                    MAX( ud.`appliance_capacity` ),') ' )

                    WHEN MIN( ud.`appliance_capacity` ) = MAX( ud.`appliance_capacity` ) THEN 
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
                (partner_net_payable * COUNT( ud.`appliance_capacity` )) AS  s_total_service_charge,
                `partners`.company_name,
                `partners`.address as company_address, partners.pincode, partners.district,
                `partners`.state,
                `partners`.seller_code
                FROM  `booking_unit_details` AS ud, services, partners
                WHERE  `product_or_services` =  'Service'
                AND  `partner_net_payable` >0
                AND ud.partner_id =  '$partner_id'
                AND ud.booking_status =  'Completed'
                AND ud.ud_closed_date >=  '$from_date'
                AND ud.ud_closed_date <  '$to_date'
                AND ud.service_id = services.id
                AND partners.id = ud.partner_id
                AND partner_invoice_id IS NULL
                GROUP BY  `partner_net_payable`,ud.service_id,price_tags  ";

        $query1 = $this->db->query($sql1);
        $service = $query1->result_array();
        $result = array_merge($service, $product);

        if (!empty($result)) {
            $meta['total_upcountry_charges'] = 0;
            if ($upcountry_flag) {
                $upcountry_data = $this->upcountry_model->upcountry_partner_invoice($partner_id, $from_date, $to_date);


                if (!empty($upcountry_data)) {
                    $up_country = array();
                    $up_country[0]['s_total_service_charge'] = '';
                    $up_country[0]['p_tax_rate'] = '';
                    $up_country[0]['p_part_cost'] = '';
                    $up_country[0]['s_service_charge'] = '';
                    $up_country[0]['qty'] = $upcountry_data[0]['total_booking'];
                    $up_country[0]['description'] = 'Upcountry Services';
                    $up_country[0]['p_rate'] = $upcountry_data[0]['partner_upcountry_rate'];
                    $up_country[0]['upcountry_charges'] = $upcountry_data[0]['total_upcountry_price'];
                    $meta['total_upcountry_charges'] = $upcountry_data[0]['total_upcountry_price'];

                    $result = array_merge($result, $up_country);
                }
            }
            $meta['total_part_cost'] = 0;
            $meta['total_service_cost'] = 0;
            foreach ($result as $value) {
                $meta['total_part_cost'] += $value['p_part_cost'];
                $meta['total_service_cost'] += $value['s_total_service_charge'];
            }
            $meta['total_service_cost_14'] = round($meta['total_service_cost'] * .14, 2);
            $meta['total_service_cost_5'] = round($meta['total_service_cost'] * .005, 2);
            $meta['sub_service_cost'] = $meta['total_service_cost'] + $meta['total_service_cost_14'] + $meta['total_service_cost_5'] * 2;
            $meta['part_cost_vat'] = ($meta['total_part_cost'] * 5.00) / 100;
            $meta['sub_part'] = $meta['total_part_cost'] + $meta['part_cost_vat'];
            $meta['grand_part'] = round($meta['sub_part'] + $meta['sub_service_cost'] + $meta['total_upcountry_charges'], 0);
            $meta['price_inword'] = convert_number_to_words($meta['grand_part']);


            $meta['company_name'] = $result[0]['company_name'];
            $meta['company_address'] = $result[0]['company_address'] . ", " .
                    $result[0]['district'] . ", Pincode -" . $result[0]['pincode'] . ", " . $result[0]['state'];
            if (!empty($result[0]['seller_code'])) {
                $meta['seller_code'] = "Seller Code: " . $result[0]['seller_code'];
            } else {
                $meta['seller_code'] = "";
            }


            $data['booking'] = $result;
            $data['meta'] = $meta;
            return $data;
        } else {
            return FALSE;
        }
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
        $is_invoice_null = "";
        if($is_regenerate == 0){
            $is_invoice_null = " AND vendor_foc_invoice_id IS NULL ";
        }
        $sql = "SELECT DISTINCT round((`vendor_basic_charges`),0) AS s_service_charge, sum(`courier_charges_by_sf`) AS misc_price,'' AS p_rate,'' AS p_part_cost, '' AS p_tax_rate,
               CASE 
               
                WHEN MIN( ud.`appliance_capacity` ) = '' AND MAX( ud.`appliance_capacity` ) != '' THEN 
                concat(services,' ', price_tags,' (', 
                MAX( ud.`appliance_capacity` ),') ' )
                
                WHEN MIN( ud.`appliance_capacity` ) != '' AND MAX( ud.`appliance_capacity` ) = '' THEN
                 concat(services,' ', price_tags,' (', 
                MIN( ud.`appliance_capacity` ),') ' )
                
                WHEN MIN( ud.`appliance_capacity` ) = '' AND MAX( ud.`appliance_capacity` ) = '' THEN
                concat(services,' ', price_tags )
                ELSE 
                concat(services,' ', price_tags,' (', MIN( ud.`appliance_capacity` ),
                '-',MAX( ud.`appliance_capacity` ),') ' )
                
                
                END AS description, 
                COUNT( ud.`appliance_capacity` ) AS qty, 
                round((vendor_basic_charges * COUNT( ud.`appliance_capacity` )),0) AS  s_total_service_charge,
                sc.state, sc.service_tax_no, sc.company_name,sc.address as vendor_address, sc_code,
                (case when (sc.tin_no IS NOT NULL )  THEN tin_no ELSE cst_no END) as tin, 
                sc.primary_contact_email, sc.owner_email, sc.pan_no, contract_file, company_type,
                sc.pan_no

                FROM  `booking_unit_details` AS ud 
                JOIN booking_details as bd on (bd.booking_id = ud.booking_id)
                JOIN services ON services.id = bd.service_id
                JOIN service_centres as sc ON sc.id = bd.assigned_vendor_id
                LEFT JOIN spare_parts_details as sp ON sp.booking_id = bd.booking_id
                AND sp.status = 'Completed'
                WHERE  `product_or_services` =  'Service'
                
                AND ud.booking_status =  'Completed'
                AND bd.assigned_vendor_id = '$vendor_id'
                AND ud.ud_closed_date >=  '$from_date'
                AND ud.ud_closed_date <  '$to_date'
                AND sc.id = bd.assigned_vendor_id
                AND  ud.around_to_vendor > 0  AND ud.vendor_to_around = 0
                AND pay_to_sf = '1'
                $is_invoice_null
                GROUP BY  `vendor_basic_charges`,ud.service_id, price_tags";

        $query = $this->db->query($sql);
        $service = $query->result_array();

        //FOR Parts
        $sql1 = "SELECT DISTINCT round((`vendor_basic_charges`),0) AS p_rate, '' AS misc_price, '' AS s_service_charge, '' AS s_total_service_charge,
               CASE 
               
                WHEN MIN( ud.`appliance_capacity` ) = '' AND MAX( ud.`appliance_capacity` ) != '' THEN 
                concat(services,' ', price_tags,' (', 
                MAX( ud.`appliance_capacity` ),') ' )
                
                WHEN MIN( ud.`appliance_capacity` ) != '' AND MAX( ud.`appliance_capacity` ) = '' THEN
                 concat(services,' ', price_tags,' (', 
                MIN( ud.`appliance_capacity` ),') ' )
                
                WHEN MIN( ud.`appliance_capacity` ) = '' AND MAX( ud.`appliance_capacity` ) = '' THEN
                concat(services,' ', price_tags )
                ELSE 
                concat(services,' ', price_tags,' (', MIN( ud.`appliance_capacity` ),
                '-',MAX( ud.`appliance_capacity` ),') ' )
                
                
                END AS description, 
                COUNT( ud.`appliance_capacity` ) AS qty, 
                round((vendor_basic_charges * COUNT( ud.`appliance_capacity` )),0) AS  p_part_cost,
                (case when (sc.tin_no IS NOT NULL )  THEN tin_no ELSE cst_no END) as tin, 
                sc.state, ud.tax_rate as p_tax_rate,sc.company_name,sc.address as vendor_address,sc_code,
                sc.primary_contact_email, sc.owner_email,service_tax_no, sc.pan_no, contract_file, company_type,
                sc.pan_no

                FROM  `booking_unit_details` AS ud, services, booking_details AS bd, service_centres as sc
                WHERE  `product_or_services` =  'Product'
                
                AND ud.booking_status =  'Completed'
                AND ud.booking_id = bd.booking_id
                AND bd.assigned_vendor_id = '$vendor_id'
                AND ud.ud_closed_date >=  '$from_date'
                AND ud.ud_closed_date <  '$to_date'
                AND ud.service_id = services.id
                AND sc.id = bd.assigned_vendor_id
                AND  ud.around_to_vendor > 0  AND ud.vendor_to_around = 0 
                AND pay_to_sf = '1'
                $is_invoice_null
                GROUP BY  `vendor_basic_charges`,ud.service_id,price_tags";

        $query1 = $this->db->query($sql1);
        $product = $query1->result_array();
        
        if(!empty($service) && !empty($product)){
            $data = array_merge($service,$product);
        } else if(!empty($service) && empty($product)){
            $data  = $service;
        } else if(empty($service) && !empty($product)){
            $data  = $service;
        }

        $result = array_merge($service, $product);

        if (!empty($result)) {
            // Calculate Upcountry booking details
            $upcountry_data = $this->upcountry_model->upcountry_foc_invoice($vendor_id, $from_date, $to_date);
            $meta['total_misc_price'] = 0;

            if (!empty($upcountry_data)) {
                $up_country = array();
            
                $up_country[0]['s_total_service_charge'] = '';
                $up_country[0]['p_tax_rate'] = '';
                $up_country[0]['p_part_cost'] = '';
                $up_country[0]['s_service_charge'] = '';
                $up_country[0]['qty'] = round($upcountry_data[0]['total_distance'], 0) . " KM";
                $up_country[0]['description'] = 'Upcountry Services';
                if($upcountry_data[0]['total_distance'] == 0){
                    $upcountry_data[0]['total_distance'] = 1;
                }
                $up_country[0]['p_rate'] = round(($upcountry_data[0]['total_upcountry_price'] / round($upcountry_data[0]['total_distance'], 0)), 2);
                $up_country[0]['misc_price'] = $upcountry_data[0]['total_upcountry_price'];


                $result = array_merge($result, $up_country);
            }

            $penalty_data = $this->penalty_model->add_penalty_in_invoice($vendor_id, $from_date, $to_date, "distinct", $is_regenerate);
            $credit_penalty = $this->penalty_model->get_removed_penalty($vendor_id, $from_date, "distinct" );

            $penalty_amount = 0;
            $cr_penalty_amount = 0;
            if (!empty($penalty_data)) {
                $penalty = array();
                $penalty[0]['s_total_service_charge'] = '';
                $penalty[0]['p_tax_rate'] = '';
                $penalty[0]['p_part_cost'] = '';
                $penalty[0]['s_service_charge'] = '';
                $penalty[0]['qty'] = array_sum(array_column($penalty_data, 'penalty_times'));
                $penalty[0]['description'] = "Deduction- Bookings Not Updated";
                $penalty[0]['p_rate'] = $penalty_data[0]['penalty_amount'];
                $penalty_amount = (array_sum(array_column($penalty_data, 'p_amount')));
                $penalty[0]['misc_price'] = -$penalty_amount;

                $result = array_merge($result, $penalty);
            }

            if (!empty($credit_penalty)) {
                $cr_penalty = array();
                $cr_penalty[0]['s_total_service_charge'] = '';
                $cr_penalty[0]['p_tax_rate'] = '';
                $cr_penalty[0]['p_part_cost'] = '';
                $cr_penalty[0]['s_service_charge'] = '';
                $cr_penalty[0]['qty'] = array_sum(array_column($credit_penalty, 'penalty_times'));
                $cr_penalty[0]['description'] = "Credit- Bookings Not Updated";
                $cr_penalty[0]['p_rate'] = $credit_penalty[0]['penalty_amount'];
                $cr_penalty_amount = (array_sum(array_column($credit_penalty, 'p_amount')));
                $cr_penalty[0]['misc_price'] = $cr_penalty_amount;

                $result = array_merge($result, $cr_penalty);
            }

            $meta['total_part_cost'] = 0;
            $meta['total_service_cost'] = 0;

            foreach ($result as $value) {
                $meta['total_part_cost'] += round($value['p_part_cost'],0);
                $meta['total_service_cost'] += round($value['s_total_service_charge'],0);
                $meta['total_misc_price'] += round($value['misc_price'],0);
            }

            if (is_null($result[0]['tin'])) {

                $meta['part_cost_vat'] = 0.00;
            } else {
                if (!empty($product)) {
                    $meta['part_cost_vat'] = round((($meta['total_part_cost'] * $product[0]['p_tax_rate']) / 100),0);
                } else {
                    $meta['part_cost_vat'] = 0.00;
                }
            }


            if (is_null($result[0]['service_tax_no'])) {
                $meta['total_service_cost_14'] = 0.00;
                $meta['total_service_cost_5'] = 0.00;
            } else {
                $meta['total_service_cost_14'] = $meta['total_service_cost'] * .14;
                $meta['total_service_cost_5'] = $meta['total_service_cost'] * .005;
            }

            $meta['sc_code'] = $result[0]['sc_code'];
            $meta['service_tax_no'] = $result[0]['service_tax_no'];
            $meta['sub_service_cost'] = round(($meta['total_service_cost'] + $meta['total_service_cost_14'] + $meta['total_service_cost_5'] * 2),0);
            $meta['vendor_name'] = $result[0]['company_name'];
            $meta['owner_email'] = $result[0]['owner_email'];
            $meta['vendor_address'] = $result[0]['vendor_address'];
            $meta['primary_contact_email'] = $result[0]['primary_contact_email'];
            $meta['owner_email'] = $result[0]['owner_email'];
            $meta['vat_tax'] = round($result[0]['p_tax_rate'],0);
            $meta['tin'] = $result[0]['tin'];
            $meta['sub_part'] = round(($meta['total_part_cost'] + $meta['part_cost_vat']),0);

            if (empty($result[0]['pan_no'])) {
                $meta['tds'] = round(($meta['total_service_cost'] * .20),0);
                $meta['tds_tax_rate'] = "20%";
            } else if (empty($result[0]['contract_file'])) {

                $meta['tds'] = round(($meta['total_service_cost'] * .05),0);
                $meta['tds_tax_rate'] = "5%";
            } else {
                switch ($result[0]['company_type']) {
                    case 'Proprietorship Firm':
                    if (!empty($result[0]['pan_no'])) {
                        $_4th_char = substr($result[0]['pan_no'], 3, 1);
                        if (strcasecmp($_4th_char, "F") == 0) {
                           $meta['tds'] = round((($meta['total_service_cost']) * .02),0);
                           $meta['tds_tax_rate'] =  "2%";
                        } else {
                            $meta['tds'] =  round((($meta['total_service_cost']) * .01),0);
                            $meta['tds_tax_rate'] = "1%";
                        }
                    } else {
                        $meta['tds'] =  round((($meta['total_service_cost']) * .01),0);
                        $meta['tds_tax_rate'] = "1%";
                    }
                    break;
                    case "Individual":
                        $meta['tds'] = round(($meta['total_service_cost'] * .01),0);
                        $meta['tds_tax_rate'] = "1%";
                        break;

                    case "Partnership Firm":
                    case "Company (Pvt Ltd)":
                        $meta['tds'] = round(($meta['total_service_cost'] * .02),0);
                        $meta['tds_tax_rate'] = "2%";
                        break;
                }
            }
            //$meta['grand_total_price'] = round($meta['sub_part'] + $meta['sub_service_cost'] + $meta['total_misc_price'], 0);
            //$meta['price_inword'] = convert_number_to_words($meta['grand_total_price']);
            $total_price = round($meta['sub_part'] + $meta['sub_service_cost'] + $meta['total_misc_price'], 0);
            if ($total_price >= 0) {
                $meta['grand_total_price'] = round($meta['sub_part'] + $meta['sub_service_cost'] + $meta['total_misc_price'], 0);
                $meta['price_inword'] = convert_number_to_words($meta['grand_total_price']);
            }else if ($total_price < 0){
                $meta['grand_total_price'] = abs(round($meta['sub_part'] + $meta['sub_service_cost'] + $meta['total_misc_price'], 0))."(DR)";
                $meta['price_inword'] = convert_number_to_words($meta['grand_total_price'])."(DR)";
            }

            $data['meta'] = $meta;
            $data['booking'] = $result;

            return $data;
        } else {
            return FALSE;
        }
    }

    /**
     * @desc: This method is used to get Main Cash Invoice Data
     * @param String $vendor_id
     * @param String $from_date
     * @param String $to_date_tmp
     * @return boolean
     */
    function get_vendor_cash_invoice($vendor_id, $from_date, $to_date_tmp, $is_regenerate) {
        $to_date = date('Y-m-d', strtotime('+1 day', strtotime($to_date_tmp)));
        for ($i = 0; $i < 2; $i++) {
            if ($i == 0) {
                $select = "(`around_comm_basic_charges` + `around_st_or_vat_basic_charges`) *COUNT( ud.`appliance_capacity` )  AS installation_charge, ";
                $where = " AND ( ( ud.vendor_to_around > 0 AND ud.around_to_vendor =0 ) OR (ud.vendor_to_around = 0 AND ud.around_to_vendor =0 ) )  ";
            } else {
                $select = "0.00 As installation_charge,";
                $where = " AND  around_to_vendor > 0  AND vendor_to_around = 0 AND (ud.customer_paid_extra_charges > 0 OR ud.customer_paid_parts > 0) ";
            }
            $is_foc_null ="";
            if($is_regenerate == 0){
                $is_foc_null = " AND vendor_cash_invoice_id IS NULL "; 
            }
            $sql = "SELECT  
                $select
                SUM(`around_comm_extra_charges` + `around_st_extra_charges`)   AS additional_charge, 
                SUM(`around_comm_parts`  + `around_st_parts`) AS misc_charge,
                CASE 
               
                    WHEN MIN( ud.`appliance_capacity` ) = '' AND MAX( ud.`appliance_capacity` ) = '' THEN
                    concat(services,' ', price_tags )

                    WHEN MIN( ud.`appliance_capacity` ) = '' AND MAX( ud.`appliance_capacity` ) != '' THEN 
                    concat(services,' ', price_tags,' (', 
                    MAX( ud.`appliance_capacity` ),') ' )

                    WHEN MIN( ud.`appliance_capacity` ) = MAX( ud.`appliance_capacity` ) THEN 
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
                
                sc.state, sc.service_tax_no, sc.company_name,sc.address as vendor_address, sc_code,
                sc.primary_contact_email, sc.owner_email,
                (case when (sc.tin_no IS NOT NULL )  THEN tin_no ELSE cst_no END) as tin

                FROM  `booking_unit_details` AS ud, services, booking_details AS bd, service_centres as sc
                 
                WHERE ud.booking_status =  'Completed'
                
                AND ud.booking_id = bd.booking_id
                AND bd.assigned_vendor_id = '$vendor_id'
                AND ud.ud_closed_date >=  '$from_date'
                AND ud.ud_closed_date <  '$to_date'
                AND ud.service_id = services.id
                AND sc.id = bd.assigned_vendor_id
                $is_foc_null
                $where
                GROUP BY  (`around_comm_basic_charges` + `around_st_or_vat_basic_charges`),ud.service_id,price_tags ";

            $query = $this->db->query($sql);
            $result[$i] = $query->result_array();
        }
        $data = array();
        if(!empty($result[0]) && !empty($result[1])){
            $data = array_merge($result[0], $result[1]);
        } else if(!empty($result[0]) && empty($result[1])){
            $data  = $result[0];
        } else if(empty($result[0]) && !empty($result[1])){
            $data  = $result[1];
        }
        

        $meta['total_misc_price'] = 0;
        // Calculate Upcountry booking details
        $upcountry_data = $this->upcountry_model->upcountry_cash_invoice($vendor_id, $from_date, $to_date);
       
        if (!empty($upcountry_data)) {
            $up_country = array();

            $up_country[0]['installation_charge'] = '';
            $up_country[0]['additional_charge'] = '';
            $up_country[0]['qty'] = round($upcountry_data[0]['total_distance'], 0) . " KM";
            $up_country[0]['description'] = 'Upcountry Services';
            $up_country[0]['misc_charge'] = $upcountry_data[0]['total_upcountry_price'];
            $up_country[0]['tin'] = $upcountry_data[0]['tin'];
            $up_country[0]['service_tax_no'] = $upcountry_data[0]['service_tax_no'];
            $up_country[0]['company_name'] = $upcountry_data[0]['company_name'];
            $up_country[0]['vendor_address'] = $upcountry_data[0]['vendor_address'];
            $up_country[0]['primary_contact_email'] = $upcountry_data[0]['primary_contact_email'];
            $up_country[0]['owner_email'] = $upcountry_data[0]['owner_email'];
            $up_country[0]['state'] = $upcountry_data[0]['state'];

            if (!empty($data)) {
                $data = array_merge($data, $up_country);
            } else {
                $data = $up_country;
            }
        }

        if (count($data) > 0) {
            $meta['total_charge'] = 0;
            $meta['total_installation_charge'] =0;
            $meta['additional_charges'] = 0;
            $meta['misc_charge'] = 0;
           
            foreach ($data as $value) {
                $meta['total_charge'] += ($value['installation_charge'] + $value['additional_charge'] + $value['misc_charge']);
                $meta['additional_charges'] += $value['additional_charge'];
                $meta['misc_charge'] += $value['misc_charge'];
                $meta['total_installation_charge'] += $value['installation_charge'];
               
            }
            $meta['total_charge'] = round($meta['total_charge']);
            $s_15charge = $this->booking_model->get_calculated_tax_charge($meta['total_charge'], 15);
            $s_basic_charge = $meta['total_charge'] - $s_15charge;

            $meta['s_14charge'] = round($s_basic_charge * 0.14,2);
            $meta['s_5charge'] = round($s_basic_charge * 0.005,2);
            $meta['total_charge'] = round($meta['total_charge'], 0);
            $meta['price_in_word'] = convert_number_to_words($meta['total_charge']);
            $meta['tin'] = $data[0]['tin'];
            $meta['service_tax_no'] = $data[0]['service_tax_no'];
            $meta['vendor_name'] = $data[0]['company_name'];
            $meta['vendor_address'] = $data[0]['vendor_address'];
            $meta['primary_contact_email'] = $data[0]['primary_contact_email'];
            $meta['owner_email'] = $data[0]['owner_email'];

            $data1['product'] = $data;
            $data1['meta'] = $meta;
            return $data1;
        } else {
            return FALSE;
        }
    }

    /**
     * @desc: Calculate unbilled Amount for vendor
     * @param String $vendor_id
     * @param String $to_date
     * @return Array
     */
    function get_unbilled_amount($vendor_id, $to_date) {
        $where = "";
        if (!empty($to_date)) {
            $where = "AND ud_closed_date >= '" . $to_date . "' ";
        }
        $sql = "SELECT SUM(`vendor_to_around` - `around_to_vendor`) AS unbilled_amount
                FROM booking_unit_details AS ud, booking_details AS bd
                WHERE bd.assigned_vendor_id = '$vendor_id'
                AND pay_to_sf =  '1'
                AND booking_status =  'Completed'
                AND bd.booking_id = ud.booking_id 
                $where
                AND ud_closed_date < '" . date('Y-m-d') . "'
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
    function get_sf_courier_charges($vendor_id, $from_date, $to_date) {
        $sql = " SELECT distinct bd.booking_id, courier_charges_by_sf 
                FROM  `booking_unit_details` AS ud 
                JOIN booking_details as bd on (bd.booking_id = ud.booking_id)
                LEFT JOIN spare_parts_details as sp ON sp.booking_id = ud.booking_id
                AND status = 'Completed'
                WHERE 
                ud.booking_status =  'Completed'
                AND bd.assigned_vendor_id = '$vendor_id'
                AND ud.ud_closed_date >=  '$from_date'
                AND ud.ud_closed_date <  '$to_date'
                AND pay_to_sf = '1'
                AND courier_charges_by_sf > 0 ";

        $query = $this->db->query($sql);
        return $query->result_array();
    }

    function get_payment_history($where) {
        $this->db->select('*');
        $this->db->where($where);
        $query = $this->db->get('payment_history');
        return $query->result_array();
    }
}
