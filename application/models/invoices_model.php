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
     *
     * This table is used to capture relation between booking id and invoices.
     * Any booking can go to vendor cash invoice and vendor foc invoice / partner
     * invoice. This table saves invoice IDs for bookings.
     *
     * When a booking is closed, a new entry is created here so that
     * invoices details can be updated later on at the time of invoice generation.
     */

    function insert_booking_invoice_mapping($details) {
	//Check whether booking id exists in this table or not
	//If it doesn't, insert it; else return
	$this->db->where('booking_id', $details['booking_id']);
	$query = $this->db->get('booking_invoices_mapping');
	if (count($query->result_array()) == 0)
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
//	$where_arr = array('vendor_partner' => $data[source],
//	    'vendor_partner_id' => $data[vendor_partner_id]);
//	$this->db->where($where_arr);
	$this->db->where($data);
	$this->db->order_by('create_date');
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
    }

    function get_bank_transactions_details($data) {
//	$this->db->where('partner_vendor', $data['source']);
//	$this->db->where('partner_vendor_id', $data['vendor_partner_id']);
	$this->db->where($data);
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

}
