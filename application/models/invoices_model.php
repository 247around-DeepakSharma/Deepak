<?php

class invoices_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
	parent::__Construct();

	$this->db_location = $this->load->database('default1', TRUE, TRUE);
	$this->db = $this->load->database('default', TRUE, TRUE);
    }

    /*
     * Insert new entry in booking invoice mapping.
     * This table is used to capture relation between booking id and invoices.
     * Any booking can go to vendor cash invoice and vendor foc invoice / partner
     * invoice. This table saves invoice IDs for bookings.
     *
     * When a booking is closed, a new entry is created here so that
     * invoices details can be updated later on at the time of invoice generation.
     */

    function insert_booking_invoice_mapping($details) {
	$this->db->insert('booking_invoices_mapping', $details);
    }

    /*
     * Update booking invoice mapping.
     * Booking ID should exist in the table. It gets created as soon as booking
     * gets completed.
     */

    function update_booking_invoice_mapping($booking_id, $details) {
	$this->db->where(array('booking_id' => $booking_id));
	$this->db->update('booking_invoices_mapping', $details);
    }

    /*
     * Save invoice information in vendor_partner_invoices table
     * Details has all invoice related info like id, type, from/to date,
     * various amounts, 247around royalty etc.
     */

    function insert_new_invoice($details) {
	$this->db->insert('vendor_partner_invoices', $details);
    }

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
        $sql = " SELECT * from vendor_partner_invoices where vendor_partner ='$data[source]' AND  vendor_partner_id = '$data[vendor_partner_id]' AND due_date <= CURRENT_DATE() Order By create_date ASC";

	   $data = $this->db->query($sql);
       return $data->result_array();
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
    }

    function bank_transactions_details($data) {
	$this->db->where('partner_vendor', $data['source']);
	$this->db->where('partner_vendor_id', $data['vendor_partner_id']);
	$this->db->order_by('transaction_date DESC');
	$query = $this->db->get('bank_transactions');
	return $query->result_array();
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
    function getbank_transaction_summary($vendor_partner, $partner_vendor_id){
        $sql = " SELECT COALESCE(SUM(`credit_amount`),0) as credit_amount, COALESCE(SUM(`debit_amount`),0) as debit_amount  from bank_transactions where partner_vendor = '$vendor_partner' AND partner_vendor_id = '$partner_vendor_id' ";
        $data = $this->db->query($sql);
        return $data->result_array();
    }

    /**
     * @desc: This funtion is used to get invoicing summary for vendor or partner.
     * @param: String ( vendor or patner)
     * @return: Array()
     */

    function getsummary_of_invoice($vendor_partner){
        $array = array();

        if($vendor_partner == "vendor"){

          $data = $this->vendor_model->getActiveVendor("", 0);

        } else if($vendor_partner == "partner") {

            $data = $this->partner_model->getpartner();
        }
        foreach ($data as $key => $value) {

            $sql = "SELECT COALESCE(SUM(`amount_collected_paid` ),0) as amount_collected_paid FROM  `vendor_partner_invoices` WHERE vendor_partner_id =  $value[id] AND vendor_partner =  '$vendor_partner'  AND `due_date` <= CURRENT_DATE()";

            $data = $this->db->query($sql);
            $result = $data->result_array();
            $bank_transactions = $this->getbank_transaction_summary($vendor_partner, $value['id']);
            $result[0]['vendor_partner'] =  $vendor_partner;
            $result[0]['name'] =  $value['name'];
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
    function delete_banktransaction($transaction_id){
       $this->db->where('id', $transaction_id);
       $this->db->delete("bank_transactions");
    }
    
    /**
     * @desc : get all vendor invoice for previous month. it get both type A and type B invoice
     * @param: void
     * @return : array
     */
    function generate_vendor_invoices($vendor_id="", $date_ragnge=""){
        $where_vendor_id  = "";
        
        if($vendor_id !=""){
           
            $where_vendor_id = " AND assigned_vendor_id = '$vendor_id'  ";
        
        } 
        if($date_ragnge !=""){
            $custom_date = explode("-", $date_ragnge);
            $from_date = $custom_date[0];
            $to_date = $custom_date[1];
            $where_vendor_id .= " AND closed_date >= '$from_date' AND closed_date < '$to_date' ";

        }  else {

            $where_vendor_id .= "  AND  booking_details.closed_date  >=  DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01')
AND booking_details.closed_date < DATE_FORMAT(NOW() ,'%Y-%m-01')  ";
        }

        $sql = "SELECT  assigned_vendor_id as vendor_id
                FROM booking_details
                WHERE current_status = 'Completed'  $where_vendor_id  Group BY assigned_vendor_id
               ";
       
        $query = $this->db->query($sql);
        $result = $query->result_array(); 
       
        $where = "";
       for($i =1; $i< 3; $i++){

            if($i ==1){
               $where = " AND `booking_unit_details`.vendor_to_around > 0 AND   `booking_unit_details`.around_to_vendor =0 "; 

            } else {
                $where = " AND `booking_unit_details`.around_to_vendor > 0  AND `booking_unit_details`.vendor_to_around = 0 "; 
            }
             $array = array();
            foreach ($result as $key => $value) {

                if( $date_ragnge != ""){
                     $where .= "  AND booking_details.closed_date >= '$from_date' AND booking_details.closed_date < '$to_date' ";
                     $date = "  '$from_date' as start_date,  '$to_date'  as end_date,  ";
        
                } else {
                     $where .=" AND  booking_details.closed_date  >=  DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01')
AND booking_details.closed_date < DATE_FORMAT(NOW() ,'%Y-%m-01') ";
                      $date = "  DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01') as start_date,  DATE_FORMAT(NOW() ,'%Y-%m-01') as end_date,  ";
                }

                $condition = "  From booking_details, booking_unit_details, services, service_centres
                          WHERE `booking_details`.booking_id = `booking_unit_details`.booking_id AND `services`.id = `booking_details`.service_id  AND `booking_details`.assigned_vendor_id = `service_centres`.id AND current_status = 'Completed' AND assigned_vendor_id = $value[vendor_id] AND `booking_unit_details`.booking_status = 'Completed' $where ";


                
                 $sql1 = "SELECT  `booking_details`.booking_id, `booking_details`.city, `booking_details`.internal_status, `booking_details`.closed_date, vendor_rating_stars, `booking_unit_details`.price_tags, `booking_unit_details`.vendor_extra_charges, `booking_unit_details`.vendor_st_extra_charges, customer_paid_extra_charges as additional_charges, (customer_paid_basic_charges + around_net_payable ) as service_charges, customer_paid_parts as parts_cost, `services`.services, vendor_to_around, customer_net_payable, partner_net_payable,around_to_vendor, (customer_paid_basic_charges + customer_paid_extra_charges + customer_paid_parts) as amount_paid ,`service_centres`.name, `service_centres`.id, `service_centres`.sc_code, `service_centres`.address, `service_centres`.beneficiary_name, `service_centres`.bank_account, `service_centres`.bank_name, `service_centres`.ifsc_code,  `service_centres`.owner_email,  `service_centres`.primary_contact_email, `booking_unit_details`.  product_or_services, `booking_unit_details`.around_net_payable,  (customer_net_payable + partner_net_payable + around_net_payable) as total_booking_charge, $date


                     (case when (`booking_unit_details`.product_or_services = 'Product' )  THEN (around_st_or_vat_basic_charges + vendor_st_or_vat_basic_charges) ELSE 0 END) as vat, 

                     (case when (`booking_unit_details`.product_or_services = 'Service' )  THEN (around_st_or_vat_basic_charges + vendor_st_or_vat_basic_charges) ELSE 0 END) as st, 

                     (case when (`booking_unit_details`.product_or_services = 'Service' )  THEN (around_comm_basic_charges +vendor_basic_charges) ELSE 0 END) as installation_charge, 

                     (case when (`booking_unit_details`.product_or_services = 'Product' )  THEN (around_comm_basic_charges +vendor_basic_charges) ELSE 0 END) as stand,

                     (SELECT SUM(customer_paid_basic_charges + around_net_payable ) $condition ) AS sum_service_charge,

                     (SELECT SUM(customer_paid_extra_charges) $condition ) AS sum_addtional_charge,

                     (SELECT SUM(customer_paid_parts) $condition ) AS sum_parts_charge,

                     (SELECT SUM((customer_paid_basic_charges + around_net_payable) * 0.30) $condition ) AS calcutated_service_tax,

                     (SELECT SUM(customer_paid_extra_charges * 0.15) $condition ) AS calcutated_additional_tax,

                     (SELECT SUM(customer_paid_parts * 0.05) $condition ) AS calcutated_parts_tax,

                     (SELECT AVG(vendor_rating_stars) $condition ) AS avg_rating,

                     (SELECT SUM((customer_paid_basic_charges + customer_paid_extra_charges + customer_paid_parts)) $condition ) AS total_amount_paid,

                     (SELECT SUM(vendor_to_around) $condition ) AS amount_to_be_pay

                    $condition ";   

                 $query1 = $this->db->query($sql1);
                 $result1 = $query1->result_array();
                //print_r($result1);
                 array_push($array, $result1);
                 
            }
           
            $invoice['invoice'.$i] = $array;

           
        }
        
         return $invoice;
    }

    function getpartner_invoices($partner_id = "", $date_ragnge="" ){

        $where_partner_id  = "";
        
        if($partner_id !=""){
           
            $where_partner_id = " AND partner_id = '$partner_id'  ";
        
        } 
        if($date_ragnge !=""){
            $custom_date = explode("-", $date_ragnge);
            $from_date = $custom_date[0];
            $to_date = $custom_date[1];
            $where_partner_id .= " AND closed_date >= '$from_date' AND closed_date < '$to_date' ";

        }  else {

            $where_partner_id .= "  AND  booking_details.closed_date  >=  DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01')
AND booking_details.closed_date < DATE_FORMAT(NOW() ,'%Y-%m-01')  ";
        }

        $sql = "SELECT  count('booking_id') as booking_count, partner_id 
                FROM booking_details
                WHERE current_status = 'Completed' AND partner_id is not null $where_partner_id  Group BY partner_id
               ";
       
        $query = $this->db->query($sql);
        $result = $query->result_array(); 

       

        for($i =1; $i< 3; $i++){
            $array = array();
             $where = "";
            foreach ($result as $key => $value) {

                if( $date_ragnge != ""){
                     $where .= "  AND booking_details.closed_date >= '$from_date' AND booking_details.closed_date < '$to_date' ";
                     $date = "  '$from_date' as start_date,  '$to_date'  as end_date,  ";
        
                } else {
                     $where .=" AND  booking_details.closed_date  >=  DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01')
AND booking_details.closed_date < DATE_FORMAT(NOW() ,'%Y-%m-01') ";
                      $date = "  DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01') as start_date,  DATE_FORMAT(NOW() ,'%Y-%m-01') as end_date,  ";
                }


                $condition = "  From booking_details, booking_unit_details, services, partners
                          WHERE `booking_details`.booking_id = `booking_unit_details`.booking_id AND `services`.id = `booking_details`.service_id  AND current_status = 'Completed' AND booking_details.partner_id = $value[partner_id] AND booking_unit_details.booking_status = 'Completed' AND booking_unit_details.partner_paid_basic_charges > 0 AND booking_unit_details.partner_id = partners.id $where ";

                if($i ==1){

                  $sql1 = "SELECT `booking_details`.service_id, `booking_details`.booking_id, `booking_details`.order_id, `booking_details`.reference_date,  `booking_details`.partner_id, `booking_details`.city, `booking_details`.closed_date, price_tags, `partners`.company_name, `partners`.company_address, partner_paid_basic_charges, `booking_unit_details`.appliance_size, `booking_unit_details`.product_or_services, $date
 
                     (case when (`booking_unit_details`.product_or_services = 'Product' )  THEN (partner_paid_basic_charges  * (tax_rate/100) ) ELSE 0 END) as vat, 

                     (case when (`booking_unit_details`.product_or_services = 'Service' )  THEN (partner_paid_basic_charges * (tax_rate/100)) ELSE 0 END) as st, 

                     (case when (`booking_unit_details`.product_or_services = 'Service' )  THEN (partner_paid_basic_charges - ((partner_paid_basic_charges ) * (tax_rate/100))) ELSE 0 END) as installation_charge, 

                     (case when (`booking_unit_details`.product_or_services = 'Product' )  THEN  (partner_paid_basic_charges  - ((partner_paid_basic_charges ) * (tax_rate/100))) ELSE 0 END) as stand

                      $condition ";
                } else if($i == 2) {
                 
                $sql1 = "SELECT count(`booking_unit_details`.id) as count_booking, services, `booking_unit_details`.partner_id,  `booking_unit_details`.appliance_capacity, `booking_unit_details`.price_tags,  `partners`.company_name, `partners`.company_address, partner_paid_basic_charges, `booking_details`.source,

            
                     (case when (`booking_unit_details`.product_or_services = 'Service' )  THEN (SUM(partner_paid_basic_charges - ((partner_paid_basic_charges ) * (tax_rate/100)))) ELSE 0 END) as total_installation_charge, 

                     (case when (`booking_unit_details`.product_or_services = 'Service' )  THEN (SUM(partner_paid_basic_charges * (tax_rate/100))) ELSE 0 END) as total_st, 

                      (case when (`booking_unit_details`.product_or_services = 'Product' )  THEN  (SUM(partner_paid_basic_charges  - ((partner_paid_basic_charges ) * (tax_rate/100)))) ELSE 0 END) as total_stand_charge,

                       (case when (`booking_unit_details`.product_or_services = 'Product' )  THEN (SUM(partner_paid_basic_charges  * (tax_rate/100) )) ELSE 0 END) as total_vat_charge


                $condition Group By `booking_unit_details`.service_id, `booking_unit_details`.appliance_capacity, `booking_unit_details`.price_tags ";
                }



                $query1 = $this->db->query($sql1);
                $result1 = $query1->result_array();
                //print_r($result1);
                array_push($array, $result1);
                 
            }

            $invoice['invoice'.$i] = $array;

        }
         
            return $invoice;
    }

}

