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
    function getEmailIdForVendor($vendor_id){
      $this->db->select('primary_contact_email, owner_email');
      $this->db->where('id', $vendor_id);
      $query = $this->db->get('service_centres');
      return $query->result_array();
    }

     /**
     * Get unique name and id of service center
     */
    function getServiceCenter(){
        $this->db->distinct();
        $this->db->select('name, id');
	$this->db->order_by('name', 'ASC');
        $query = $this->db->get('service_centres');
        return $query->result_array();
    }

    /**
     * Get data from vendor_partner_invoices table where vendor id
     * @param : vendor partner id
     * @return :Array
     */
    function getInvoicingData($data){
        $this->db->select('*');
	$this->db->order_by('create_date', 'ASC');
        $this->db->where('vendor_partner', $data['source']);
        $this->db->where('vendor_partner_id', $data['vendor_partner_id']);

        $query = $this->db->get('vendor_partner_invoices');
        return $query->result_array();
    }

    /**
     * Get partner Email id
     * @param type $partnerId
     */
    function getEmailIdForPartner($partnerId){
        $this->db->select('partner_email_for_to');
        $this->db->where('partner_id', $partnerId);
        $query = $this->db->get('bookings_sources');
        return $query->result_array();

    }
    //Function to insert banks account/statement
    function bankAccountStatement($account_statement){
	$this->db->insert('bank_ac_statements', $account_statement);
    }

    function bank_statement_details($data){
      $this->db->select('bank_ac_statements.*');
      $this->db->from('vendor_partner_invoices');
      $this->db->join('bank_ac_statements', 'bank_ac_statements.invoice_id = vendor_partner_invoices.invoice_id');
      $this->db->where('vendor_partner_invoices.vendor_partner', $data['source']);
      $this->db->where('vendor_partner_invoices.vendor_partner_id', $data['vendor_partner_id']);
      $query = $this->db->get();
      return $query->result_array();
    }
}
