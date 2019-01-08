<?php

/**
 * Description of user_Invoice
 *
 * @author abhay
 */
class User_invoice extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();

        $this->load->helper(array('form', 'url'));
        $this->load->model("invoices_model");
        $this->load->model("booking_model");
        $this->load->model('reusable_model');
        $this->load->library("invoice_lib");
        $this->load->library('miscelleneous');
        $this->load->library("session");

    }
   
    function checkUserSession() {
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            echo PHP_EOL . 'Terminal Access Not Allowed' . PHP_EOL;
            redirect(base_url() . "employee/login");
        }
    }
    
    /**
     * @desc This method is used to generate Customer invoice on the behalf of Sf
     * @param String $booking_id
     * @param String $agent_id
     */
    function payment_invoice_for_customer($booking_id, $agent_id, $preinvoice_id = false) {
        log_message("info", __METHOD__ . " Enering .. for booking id " . $booking_id . " agent ID " . $agent_id . "Invoice ID " . $preinvoice_id);
        $select = "service_centres.company_name, service_centres.address as sf_address, "
                . "service_centres.pincode as sf_pincode, service_centres.district as sf_district, service_centres.state as sf_state, service_centres.gst_no, service_centres.owner_phone_1, "
                . "users.name, users.home_address, users.phone_number,users.user_email, users.pincode, users.city, users.state, booking_details.amount_due, "
                . "booking_details.amount_paid, booking_details.quantity, request_type, services, booking_details.quantity, booking_primary_contact_no,  "
                . "sc_code, booking_details.user_id, booking_details.closed_date, booking_details.assigned_vendor_id, owner_email, primary_contact_email";
        $request['where'] = array("booking_details.booking_id" => $booking_id, 'amount_paid > ' . MAKE_CUTOMER_PAYMENT_INVOICE_GREATER_THAN => NULL);
        $request['length'] = -1;
        $data = $this->booking_model->get_bookings_by_status($request, $select);

        if (!empty($data)) {
            $prod = '';
            if (empty($preinvoice_id)) {
                $invoice_id = $this->invoice_lib->create_invoice_id($data[0]->sc_code);
                $unit = $this->booking_model->get_unit_details(array("booking_id" => $booking_id, "booking_status != 'Cancelled' " => NULL, 'user_invoice_id IS NULL' => NULL));
            } else {
                $invoice_id = $preinvoice_id;
                $unit = $this->booking_model->get_unit_details(array("booking_id" => $booking_id, "booking_status != 'Cancelled' " => NULL));
            }

            if (!empty($unit)) {
                $unique = array_unique(array_map(function ($k) {
                            return $k['price_tags'];
                        }, $unit));

                $prod = $data[0]->services . " (" . implode(",", $unique) . ")";
            }
            if (!empty($prod)) {
                log_message("info", __METHOD__ . " Invoice ID created " . $invoice_id);
                $invoice = array();
                $invoice[0]['description'] = $prod;
                $tax_charge = $this->booking_model->get_calculated_tax_charge($data[0]->amount_paid, DEFAULT_TAX_RATE);
                $invoice[0]['taxable_value'] = ($data[0]->amount_paid - $tax_charge);

                $invoice[0]['product_or_services'] = "Service";
                $invoice[0]['gst_number'] = $data[0]->gst_no;
                $invoice[0]['company_name'] = $data[0]->company_name;
                $invoice[0]['company_address'] = $data[0]->sf_address;
                $invoice[0]['district'] = $data[0]->sf_district;
                $invoice[0]['pincode'] = $data[0]->sf_pincode;
                $invoice[0]['state'] = $data[0]->sf_state;
                $invoice[0]['rate'] = 0;
                $invoice[0]['qty'] = $data[0]->quantity;
                $invoice[0]['hsn_code'] = HSN_CODE;
                $invoice[0]['gst_rate'] = DEFAULT_TAX_RATE;

                $sd = $ed = $invoice_date = $data[0]->closed_date;

                $response = $this->invoices_model->_set_partner_excel_invoice_data($invoice, $sd, $ed, "Tax Invoice", $invoice_date, true, $data[0]->state);
                $response['meta']['customer_name'] = $data[0]->name;
                $response['meta']['customer_address'] = $data[0]->home_address . ", " . $data[0]->city . ", Pincode - " . $data[0]->pincode . ", " . $data[0]->state;
                $response['meta']['customer_phone_number'] = $data[0]->booking_primary_contact_no;

                $response['meta']['invoice_id'] = $invoice_id;
                $response['meta']['booking_id'] = $booking_id;
                $response['meta']['owner_phone_1'] = $data[0]->owner_phone_1;

                $status = $this->invoice_lib->send_request_to_create_main_excel($response, "final", true);
                if ($status) {
                    log_message('info', __FUNCTION__ . ' Invoice File is created. invoice id' . $response['meta']['invoice_id']);
                    //$convert = $this->invoice_lib->send_request_to_convert_excel_to_pdf($response['meta']['invoice_id'], "final", true, true);
                    $convert = $this->invoice_lib->convert_invoice_file_into_pdf($response, "final", true, true);
                    $this->invoice_lib->upload_invoice_to_S3($response['meta']['invoice_id'], false, true);

                    $output_pdf_file_name = $convert['main_pdf_file_name'];
                    $copy_pdf_file_name = $convert['copy_file'];
                    //$triplicate_pdf_file_name = $convert['triplicate_file'];
                    $pathinfo1 = pathinfo($output_pdf_file_name);
                    if ($pathinfo1['extension'] == 'xls' || $pathinfo1['extension'] == 'xlsx') {
                        log_message("info", __METHOD__ . " SF Invoice Pdf is not generated " . $output_pdf_file_name);
                    } else {
                        //Send invoice to SF
                        $email_template = $this->booking_model->get_booking_email_template("customer_paid_invoice_to_vendor");
                        $subject = vsprintf($email_template[4], array($booking_id));
                        $message = $email_template[0];
                        $email_from = $email_template[2];

                        $to = $data[0]->owner_email;
                        $cc = $email_template[3] . "," . $data[0]->primary_contact_email;
                        $bcc = $email_template[5];


                        $pdf_attachement_url = S3_WEBSITE_URL . 'invoices-excel/' . $output_pdf_file_name;
                        $this->notify->sendEmail($email_from, $to, $cc, $bcc, $subject, $message, $pdf_attachement_url, 'customer_paid_invoice_to_vendor', "", $booking_id);
                    }

                    $pathinfo = pathinfo($copy_pdf_file_name);
                    if ($pathinfo['extension'] == 'xls' || $pathinfo['extension'] == 'xlsx') {
                        log_message("info", __METHOD__ . " Invoice Pdf is not generated " . $copy_pdf_file_name);
                        $sms['tag'] = "customer_paid_invoice_pdf_not_generated";
                    } else {
                        $customer_attachement_url = S3_WEBSITE_URL . 'invoices-excel/' . $copy_pdf_file_name;

                        $tinyUrl = $this->miscelleneous->getShortUrl($customer_attachement_url);
                        // If invoice links is not generating then we will not send invoice Link in sms
                        if ($tinyUrl) {

                            $sms['tag'] = "customer_paid_invoice";
                        } else {
                            $sms['tag'] = "customer_paid_invoice_pdf_not_generated";

                            log_message("info", __METHOD__ . " Short url failed for booking id " . $booking_id);
                        }

                        // Send Invoice to Customer
                        if (filter_var($data[0]->user_email, FILTER_VALIDATE_EMAIL)) {
                            $email_template = $this->booking_model->get_booking_email_template("customer_paid_invoice");
                            $subject = vsprintf($email_template[4], array($booking_id));
                            $message = $email_template[0];
                            $email_from = $email_template[2];

                            $to = $data[0]->user_email;
                            $cc = $email_template[3];
                            $bcc = $email_template[5];

                            $this->notify->sendEmail($email_from, $to, $cc, $bcc, $subject, $message, $customer_attachement_url, 'customer_paid_invoice', "", $booking_id);
                        }
                    }
                    $sms['smsData']['amount'] = $data[0]->amount_paid;
                    $sms['smsData']['booking_id'] = $booking_id;
                    if ($sms['tag'] == "customer_paid_invoice") {

                        $sms['smsData']['url'] = $tinyUrl;
                    }

                    $sms['phone_no'] = $response['meta']['customer_phone_number'];
                    $sms['booking_id'] = $booking_id;
                    $sms['type'] = "user";
                    $sms['type_id'] = $data[0]->user_id;
                    $this->notify->send_sms_msg91($sms);

                    $this->insert_payment_invoice($booking_id, $response, $data[0]->assigned_vendor_id, 
                            $data[0]->closed_date, $agent_id, $convert, $data[0]->user_id,$preinvoice_id);
                    
                    if(file_exists(TMP_FOLDER.$response['meta']['invoice_id'] . '.xlsx')){
                        unlink(TMP_FOLDER.$response['meta']['invoice_id']. '.xlsx');
                    }
                    if(file_exists(TMP_FOLDER.$convert['triplicate_file'])){
                        unlink(TMP_FOLDER.$convert['triplicate_file']);
                    }
                    if(file_exists(TMP_FOLDER.$convert['copy_file'])){
                        unlink(TMP_FOLDER.$convert['copy_file']);
                    }
                    echo json_encode(array(
                        'status' => true,
                        'message' => $invoice_id
                    ));
                } else {
                    echo json_encode(array(
                        'status' => false,
                        'message' => 'Excel is not created'
                    ));
                    log_message("info" , __METHOD__ . " Excel Not Created Booking ID" . $booking_id);
                }
            } else {
                log_message("info", __METHOD__ . " User Invoice is Not Null" . $booking_id);
                echo json_encode(array(
                    'status' => false,
                    'message' => 'User Invoice Already Generated'
                ));
            }
        } else {
            echo json_encode(array(
                'status' => false,
                'message' => 'Booking Not Found'
            ));
            log_message("info", __METHOD__ . " Data Not Found Booking ID" . $booking_id);
        }
    }

    function insert_payment_invoice($booking_id, $invoice, $sf_id, $closed_date, $agent_id, $convert, $user_id, $isregenareate){
        $main_invoice = array(
                'invoice_id' => $invoice['meta']['invoice_id'],
                'entity_to' => 'user',
                'bill_to_party' => $user_id,
                'entity_from' => 'vendor',
                'bill_from_party' => $sf_id,
                'settle' => 1,
                'booking_id' => $booking_id,
                'total_qty' => $invoice['meta']["total_qty"],
                'main_invoice_file' => $convert['copy_file'],
                "duplicate_file" => $convert['main_pdf_file_name'],
                'invoice_excel' => $invoice['meta']['invoice_id'] . '.xlsx',
                'triplicate_file' => $convert['triplicate_file'],
                'invoice_date' => date("Y-m-d H:i:s", strtotime($closed_date)),
                'from_date' => date("Y-m-d H:i:s", strtotime($closed_date)),
                'to_date' => date("Y-m-d H:i:s", strtotime($closed_date)),
                'due_date' => date("Y-m-d H:i:s", strtotime($closed_date)),
                'total_basic_amount' => $invoice['meta']['total_taxable_value'],
                'total_invoice_amount' => $invoice['meta']['sub_total_amount'],
                'amount_paid' => $invoice['meta']['sub_total_amount'],
                'agent_id' => $agent_id,
                "total_igst_tax_amount" => $invoice['meta']["igst_total_tax_amount"],
                "total_sgst_tax_amount" => $invoice['meta']["sgst_total_tax_amount"],
                "total_cgst_tax_amount" => $invoice['meta']["cgst_total_tax_amount"],
                'create_date' => date('Y-m-d H:i:s'),
                "remarks" => '',
                "vertical" => SERVICE,
                "category" => INSTALLATION_AND_REPAIR,
                "sub_category" => CUSTOMER_PAYMENT,
                "accounting" => 0,
            );
        
        $invoice_breakup = array();
        foreach($invoice['booking'] as $value){
            $invoice_details = array(
                "invoice_id" => $invoice['meta']['invoice_id'],
                "description" => $value['description'],
                "qty" => $value['qty'],
                "product_or_services" => $value['product_or_services'],
                "rate" => $value['rate'],
                "taxable_value" => $value['taxable_value'],
                "cgst_tax_rate" => (isset($value['cgst_tax_rate']) ? $value['cgst_tax_rate'] : 0),
                "sgst_tax_rate" => (isset($value['sgst_tax_rate']) ? $value['sgst_tax_rate'] : 0),
                "igst_tax_rate" => (isset($value['igst_tax_rate']) ? $value['igst_tax_rate'] : 0),
                "cgst_tax_amount" => (isset($value['cgst_tax_amount']) ? $value['cgst_tax_amount'] : 0),
                "sgst_tax_amount" => (isset($value['sgst_tax_amount']) ? $value['sgst_tax_amount'] : 0),
                "igst_tax_amount" => (isset($value['igst_tax_amount']) ? $value['igst_tax_amount'] : 0),
                "hsn_code" => $value['hsn_code'],
                "total_amount" => $value['total_amount'],
                "create_date" => date('Y-m-d H:i:s')
                
            );
            
            array_push($invoice_breakup, $invoice_details);
        }
        
        if(!empty($isregenareate)){
            $this->invoices_model->update_invoice(array('invoice_id' =>$invoice['meta']['invoice_id']), $main_invoice);
            $this->invoices_model->update_invoice_breakup(array('invoice_id' =>$invoice['meta']['invoice_id']),$invoice_breakup[0]);
        } else {
            
            $this->invoices_model->insert_invoice($main_invoice);
            $this->invoices_model->insert_invoice_breakup($invoice_breakup);
        }
            
        $this->booking_model->update_booking_unit_details_by_any(array('booking_id' => $booking_id, "booking_status" => 'Completed'), array("user_invoice_id" => $invoice['meta']['invoice_id']));
            
        return true;
    }
    /**
     * @desc This function is used to generate sf credit note.
     * When customer paid through paytm and we will get tarnsaction callback
     * @param String $booking_id
     * @param String $amountPaid
     * @param String $txnID
     * @param String $agent_id
     */
    function sf_payment_creditnote($booking_id, $amountPaid, $txnID, $agent_id){
        log_message("info", __METHOD__ . " Enering .. for booking id " . $booking_id . " amount paid " . $amountPaid . " Txn ID " . $txnID . " agent ID " . $agent_id);
        $is_exist = $this->invoices_model->get_invoices_details(array('invoice_id' => $txnID));
        
        
        if (empty($is_exist)) {
            $select = "service_centres.company_name, service_centres.address as sf_address, "
                    . "service_centres.pincode as sf_pincode, service_centres.district as sf_district, service_centres.state as sf_state, service_centres.gst_no, service_centres.owner_phone_1, "
                    . "users.name, users.home_address, users.phone_number,users.user_email, users.pincode, users.city, users.state, booking_details.amount_due, "
                    . "booking_details.amount_paid, booking_details.quantity, request_type, services, booking_details.quantity, booking_primary_contact_no,  "
                    . "sc_code, booking_details.user_id, booking_details.closed_date, booking_details.assigned_vendor_id, owner_email, primary_contact_email";
            $request['where'] = array("booking_details.booking_id" => $booking_id);
            $request['length'] = -1;
            $data = $this->booking_model->get_bookings_by_status($request, $select);
            if (!empty($data)) {
                $invoice = array();

                $invoice_id = $txnID;
                $prod = $data[0]->services . " (" . $data[0]->request_type . ") ";
                $invoice[0]['description'] = $prod;
                $invoice[0]['taxable_value'] = $amountPaid;

                $invoice[0]['product_or_services'] = "Service";
                $invoice[0]['gst_number'] = "";
                $invoice[0]['company_name'] = $data[0]->company_name;
                $invoice[0]['company_address'] = $data[0]->sf_address;
                $invoice[0]['district'] = $data[0]->sf_district;
                $invoice[0]['pincode'] = $data[0]->sf_pincode;
                $invoice[0]['state'] = $data[0]->sf_state;
                $invoice[0]['rate'] = 0;
                $invoice[0]['qty'] = $data[0]->quantity;
                //As Aditya, No need to add hsn code
                $invoice[0]['hsn_code'] = "";
                $invoice[0]['gst_rate'] = DEFAULT_TAX_RATE;

                $sd = $ed = $invoice_date = date("Y-m-d");
                $response = $this->invoices_model->_set_partner_excel_invoice_data($invoice, $sd, $ed, "Payment Voucher", $invoice_date);
                $response['meta']['customer_name'] = $data[0]->name;
                $response['meta']['customer_address'] = $data[0]->home_address . ", " . $data[0]->city . ", Pincode - " . $data[0]->pincode . ", " . $data[0]->state;
                $response['meta']['customer_phone_number'] = $data[0]->booking_primary_contact_no;

                $response['meta']['invoice_id'] = $invoice_id;
                $response['meta']['booking_id'] = $booking_id;
                $response['meta']['owner_phone_1'] = $data[0]->owner_phone_1;
                $response['meta']['invoice_template'] = "paytm_payment_voucher.xlsx";
                $response['meta']['gst_number'] = $data[0]->gst_no;
                $response['meta']['booking_id'] = $booking_id;
                $status = $this->invoice_lib->send_request_to_create_main_excel($response, "final");

                if ($status) {
                    log_message('info', __FUNCTION__ . ' Invoice File is created. invoice id' . $response['meta']['invoice_id']);
                    //$convert = $this->invoice_lib->send_request_to_convert_excel_to_pdf($response['meta']['invoice_id'], "final", TRUE, FALSE);
                    $convert = $this->invoice_lib->convert_invoice_file_into_pdf($response, "final", true, FALSE);
                    $this->invoice_lib->upload_invoice_to_S3($response['meta']['invoice_id'], false, false);
                    log_message("info", __METHOD__ . " SF Credit note uploaded to s3 for booking ID " . $booking_id . " Invoice ID " . $response['meta']['invoice_id']);
                    //$output_pdf_file_name = $convert['main_pdf_file_name'];
//                $email_template = $this->booking_model->get_booking_email_template("paytm_payment_voucher");
//                $subject =  vsprintf($email_template[4], array($booking_id));
//                $message = $email_template[0];
//                $email_from = $email_template[2];
//
//                $to = $data[0]->owner_email;
//                $cc = $email_template[3].",".$data[0]->primary_contact_email;
//                $bcc = $email_template[5];
//
//
//                $pdf_attachement_url = S3_WEBSITE_URL. 'invoices-excel/' . $output_pdf_file_name;
                    //$this->notify->sendEmail($email_from, $to, $cc, $bcc, $subject, $message, $pdf_attachement_url);

                    $this->insert_sf_credit_note($booking_id, $response, $data[0]->assigned_vendor_id, $sd, $agent_id, $convert, $txnID);
                    
                    if(file_exists(TMP_FOLDER.$response['meta']['invoice_id'] . '.xlsx')){
                        unlink($response['meta']['invoice_id'] . '.xlsx');
                    }
                } else {
                    log_message("info" , __METHOD__ . " Excel Not Created Booking ID" . $booking_id);
                }
            } else {
                log_message("info" , __METHOD__ . " Booking ID Not found " . $booking_id);
            }
        } else {
            log_message("info" , __METHOD__ . " Invoice Already Exsit dor booking ID " . $booking_id . " Invoice Data " . $txnID);
        }
    }
    
    function insert_sf_credit_note($booking_id, $invoice, $sf_id, $invoice_date, $agent_id, $convert, $txnID) {
        $invoice_details = array(
            'invoice_id' => $invoice['meta']['invoice_id'],
            'type' => 'FOC',
            'type_code' => 'B',
            'vendor_partner' => 'vendor',
            'vendor_partner_id' => $sf_id,
            'invoice_file_main' => $convert['main_pdf_file_name'],
            'invoice_file_excel' => $invoice['meta']['invoice_id'] . '.xlsx',
            'invoice_detailed_excel' => '',
            'invoice_date' => $invoice_date,
            'from_date' => date("Y-m-d", strtotime($invoice_date)),
            'to_date' => date("Y-m-d", strtotime($invoice_date)),
            'due_date' => date("Y-m-d", strtotime($invoice_date . "+1 month")),
            'num_bookings' => 1,
            "parts_count" => 0,
            'total_service_charge' => $invoice['meta']['total_taxable_value'],
            'total_additional_service_charge' => 0,
            'parts_cost' => 0,
            'total_amount_collected' => $invoice['meta']['sub_total_amount'],
            'amount_collected_paid' => -$invoice['meta']['sub_total_amount'],
            'agent_id' => $agent_id,
            "cgst_tax_rate" => $invoice['meta']['cgst_tax_rate'],
            "sgst_tax_rate" => $invoice['meta']['sgst_tax_rate'],
            "igst_tax_rate" => $invoice['meta']['igst_tax_rate'],
            "igst_tax_amount" => $invoice['meta']["igst_total_tax_amount"],
            "sgst_tax_amount" => $invoice['meta']["sgst_total_tax_amount"],
            "cgst_tax_amount" => $invoice['meta']["cgst_total_tax_amount"],
            "invoice_file_pdf" => $convert['copy_file'],
            "remarks" => $booking_id,
            "vertical" => SERVICE,
            "category" => CREDIT_NOTE,
            "sub_category" => CUSTOMER_PAYMENT,
            "accounting" => 0,
        );
        //Insert Invoice
        $this->invoices_model->insert_new_invoice($invoice_details);
        $this->reusable_model->update_table("paytm_transaction_callback", array('vendor_invoice_id' => $invoice['meta']['invoice_id']),array('txn_id' => $txnID));
    }
    
    function resend_customer_invoice($booking_id, $invoice_id) {
       
        if (!empty($invoice_id) && !empty($booking_id)) {
            
            $invoiceData = $this->invoices_model->get_new_invoice_data(array('invoice_id' => $invoice_id), "main_invoice_file");

            $join["users"] = "users.user_id = booking_details.user_id";

            $data = $this->reusable_model->get_search_result_data("booking_details", "users.user_id,user_email, booking_primary_contact_no, amount_paid", array("booking_details.booking_id" => $booking_id), $join, NULL, NULL, NULL, NULL, array());

            // If invoice links is not generating then we will not send invoice Link in sms
            $pathinfo = pathinfo($invoiceData[0]['main_invoice_file']);
            if ($pathinfo['extension'] == 'xls' || $pathinfo['extension'] == 'xlsx') {
                log_message("info", __METHOD__ . " Invoice Pdf is not generated " . $invoiceData[0]['main_invoice_file']);

                $sms['tag'] = "customer_paid_invoice_pdf_not_generated";
            } else {

                $customer_attachement_url = S3_WEBSITE_URL . 'invoices-excel/' . $invoiceData[0]['main_invoice_file'];

                $tinyUrl = $this->miscelleneous->getShortUrl($customer_attachement_url);
                // If invoice links is not generating then we will not send invoice Link in sms
                if ($tinyUrl) {

                    $sms['tag'] = "customer_paid_invoice";
                } else {
                    $sms['tag'] = "customer_paid_invoice_pdf_not_generated";

                    log_message("info", __METHOD__ . " Short url failed for booking id " . $booking_id);
                }

                $sms['smsData']['amount'] = $data[0]['amount_paid'];
                $sms['smsData']['booking_id'] = $booking_id;
                if ($sms['tag'] == "customer_paid_invoice") {

                    $sms['smsData']['url'] = $tinyUrl;
                }

                $sms['phone_no'] = $data[0]['booking_primary_contact_no'];
                $sms['booking_id'] = $booking_id;
                $sms['type'] = "user";
                $sms['type_id'] = $data[0]['user_id'];
                $this->notify->send_sms_msg91($sms);

                if (filter_var($data[0]['user_email'], FILTER_VALIDATE_EMAIL)) {
                    $email_template = $this->booking_model->get_booking_email_template("customer_paid_invoice");
                    $subject = vsprintf($email_template[4], array($booking_id));
                    $message = $email_template[0];
                    $email_from = $email_template[2];

                    $to = $data[0]['user_email'];
                    $cc = $email_template[3];
                    $bcc = $email_template[5] . ", " . $this->session->userdata('official_email');

                    $this->notify->sendEmail($email_from, $to, $cc, $bcc, $subject, $message, $customer_attachement_url,'customer_paid_invoice', "", $booking_id);
                }
            }
            echo "success";
        } else {
            echo "Error";
        }
    }
    
    function regenerate_payment_invoice_for_customer($booking_id, $amount, $invoice_id, $agent_id) {
        log_message("info", __METHOD__ . " Enering .. for booking id " . $booking_id . " agent ID " . $agent_id . "Invoice ID " . $invoice_id);
        $data = $this->booking_model->get_bookings_count_by_any("booking_id, amount_paid", array('booking_id' => $booking_id));
        if (!empty($invoice_id) && !empty($booking_id)) {
            if (!empty($data)) {
                if ($data[0]['amount_paid'] == $amount) {
                    $output = "Invoice amount is same as previous amount";
                    $userSession = array('error' => $output);
                    $this->session->set_userdata($userSession);
                    redirect(base_url(). "employee/invoice/customer_invoice");
                    
                } else {
                    $response = $this->payment_invoice_for_customer($booking_id, $agent_id, $invoice_id);
                    $s = json_decode($response, TRUE);
                    if($s['status']){
                        $output = $s['message'];
                        $userSession = array('success' => $output);
                        $this->session->set_userdata($userSession);
                        redirect(base_url(). "employee/invoice/customer_invoice");
                        
                    } else {
                        $output = $s['message'];
                        $userSession = array('error' => $output);
                        $this->session->set_userdata($userSession);
                        redirect(base_url(). "employee/invoice/customer_invoice");
                    }
                }
            } else {
                
                $output = "Booking ID is not Found";
                $userSession = array('error' => $output);
                $this->session->set_userdata($userSession);
                redirect(base_url(). "employee/invoice/customer_invoice");
                    
            }
        } else {
            $output = "Booking ID/ Invoice ID is not Found";
            $userSession = array('error' => $output);
            $this->session->set_userdata($userSession);
            redirect(base_url(). "employee/invoice/customer_invoice");
        }
    }
    
     /*
     * @des - This function is used to process bill defective spare to SF
     * @param - form data
     * @return - boolean
     */
    function process_spare_invoice(){ 
        $postData = json_decode($this->input->post('postData'));
        $spare_parts_detail_ids = array();
        $data = array();
        $result = "";
        $email_parts_name = "";
        $partner_id = 0;
        $booking_id = $this->input->post('booking_id');
        $remarks = $this->input->post('remarks');
        $sd = $ed = $invoice_date = date("Y-m-d");
        $vendor_data = $this->vendor_model->getVendorDetails("service_centres.id, gst_no, "
                            . "state,address as company_address, "
                            . "company_name, pincode, "
                            . "district, owner_email as invoice_email_to, email as invoice_email_cc", array('id' => $postData[0]->service_center_ids))[0];
        $invoice_id = $this->invoice_lib->create_invoice_id("Around");
        foreach ($postData as $key=>$value){
                $spare_parts_detail_ids[] = $value->spare_detail_ids;
                $where = array('spare_parts_details.id' => $value->spare_detail_ids);
                $chech_spare = $this->partner_model->get_spare_parts_by_any('spare_parts_details.sell_invoice_id, spare_parts_details.is_micro_wh, booking_details.partner_id', $where, true);
                $partner_id = $chech_spare[0]['partner_id'];
                if(!$chech_spare[0]['sell_invoice_id'] && $chech_spare[0]['is_micro_wh'] != 2){
                        $email_parts_name .= $value->spare_product_name."(".$booking_id.") ";
                        $amount = $value->confirm_prices;
                        $hsn_code = $value->hsn_codes;
                        $gst_rate = $value->gst_rates;

                        $data[$key]['description'] =  $value->spare_product_name."(".$booking_id.")";
                        $tax_charge = $this->booking_model->get_calculated_tax_charge($amount, $gst_rate);
                        $data[$key]['taxable_value'] = ($amount  - $tax_charge);
                        $data[$key]['product_or_services'] = "Product";
                        if(!empty($vendor_data['gst_number'])){
                            $data[$key]['gst_number'] = $vendor_data['gst_number'];
                        } else {
                            $data[$key]['gst_number'] = TRUE;
                        }

                        $data[$key]['company_name'] = $vendor_data['company_name'];
                        $data[$key]['company_address'] = $vendor_data['company_address'];
                        $data[$key]['district'] = $vendor_data['district'];
                        $data[$key]['pincode'] = $vendor_data['pincode'];
                        $data[$key]['state'] = $vendor_data['state'];
                        $data[$key]['rate'] = $data[$key]['taxable_value'];
                        $data[$key]['qty'] = 1;
                        $data[$key]['hsn_code'] = $hsn_code;
                        $data[$key]['gst_rate'] = $gst_rate;
                }
                
                //insert entry into booking state change
                $booking_state_remarks = $remarks." Part Id - ".$value->spare_detail_ids;
                $this->notify->insert_state_change($booking_id, $value->reasons, "", $booking_state_remarks, $this->session->userdata('id'), $this->session->userdata('employee_id'), ACTOR_NOT_DEFINE, NEXT_ACTION_NOT_DEFINE, _247AROUND);
        }
        if(!empty($data)){
            $invoice_type = "Tax Invoice";
            $response = $this->invoices_model->_set_partner_excel_invoice_data($data, $sd, $ed, $invoice_type,$invoice_date);
            $response['meta']['invoice_id'] = $invoice_id;
            $status = $this->invoice_lib->send_request_to_create_main_excel($response, "final");
            if($status){

                $convert = $this->invoice_lib->convert_invoice_file_into_pdf($response, "final");
                $output_pdf_file_name = $convert['main_pdf_file_name'];
                $response['meta']['invoice_file_main'] = $output_pdf_file_name;
                $response['meta']['copy_file'] = $convert['copy_file'];
                $response['meta']['invoice_file_excel'] = $invoice_id.".xlsx";

                $this->invoice_lib->upload_invoice_to_S3($invoice_id, false);
                
                $email_tag = DEFECTIVE_SPARE_SALE_INVOICE;    
                $email_template = $this->booking_model->get_booking_email_template($email_tag);
                $subject = vsprintf($email_template[4], array($booking_id));
                $message = vsprintf($email_template[0], array($email_parts_name, $booking_id));
                $email_from = $email_template[2];
                //$to = $vendor_data['invoice_email_to'].",".$email_template[1];
                //$cc = $vendor_data['invoice_email_cc'].",".$email_template[3];
                $to = $email_template[1];
                $cc = $email_template[3];
                
                $cmd = "curl " . S3_WEBSITE_URL . "invoices-excel/" . $output_pdf_file_name . " -o " . TMP_FOLDER.$output_pdf_file_name;
                exec($cmd); 

                $this->notify->sendEmail($email_from, $to, $cc, $email_template[5], $subject, $message, TMP_FOLDER.$output_pdf_file_name, $email_tag, "", $booking_id);

                unlink(TMP_FOLDER.$output_pdf_file_name);


                unlink(TMP_FOLDER.$invoice_id.".xlsx");
                unlink(TMP_FOLDER."copy_".$invoice_id.".xlsx");

            }
            
            $response['meta']['invoice_id'] = $invoice_id;
            $response['meta']['vertical'] = SERVICE;
            $response['meta']['category'] = SPARES;
            $response['meta']['sub_category'] = DEFECTIVE_RETURN;
            $response['meta']['accounting'] = 1;
            
            $this->invoice_lib->insert_invoice_breackup($response);
            $invoice_details = $this->invoice_lib->insert_vendor_partner_main_invoice($response, "A", "Parts", _247AROUND_SF_STRING, $postData[0]->service_center_ids, $convert, $this->session->userdata('id'), $hsn_code);
            $inserted_invoice = $this->invoices_model->insert_new_invoice($invoice_details);

            if($inserted_invoice){
                /* Send mail to partner */
                $email_template = $this->booking_model->get_booking_email_template(DEFECTIVE_SPARE_SOLED_NOTIFICATION);
                $subject = vsprintf($email_template[4], array($booking_id));
                $message = vsprintf($email_template[0], array($email_parts_name, $booking_id)); 
                $email_from = $email_template[2];
                $booking_partner = $this->reusable_model->get_search_query('partners','invoice_email_to, invoice_email_cc', array("id"=>$partner_id), "", "", "", "", "")->result_array();
                //$to = $booking_partner[0]['invoice_email_to'].",".$email_template[1];
                //$cc = $booking_partner[0]['invoice_email_cc'].",".$email_template[3];
                $to = $email_template[1];
                $cc = $email_template[3];

                $this->notify->sendEmail($email_from, $to, $cc, $email_template[5], $subject, $message, "", DEFECTIVE_SPARE_SOLED_NOTIFICATION, "", $booking_id);
                
                
                $service_center_action = $this->booking_model->get_bookings_count_by_any('service_center_closed_date', array('booking_id'=>$booking_id));
                if($service_center_action[0]['service_center_closed_date']){
                    $sc_data['current_status'] = "InProcess";
                    $sc_data['internal_status'] = _247AROUND_COMPLETED;
                    $this->vendor_model->update_service_center_action($booking_id, $sc_data);
                }
                $spare_parts_detail_ids = array_filter($spare_parts_detail_ids);
                $where_in = array('id' => $spare_parts_detail_ids);
                $result  = $this->inventory_model->update_bluk_spare_data($where_in,array('defective_part_required'=>0, 'sell_invoice_id'=>$invoice_id));
            }

            echo $result;
        }
        else{
            echo false;
        }
    }
    
    /* 
     * @desc - this function is used to load view for partner refuse to paty form
     * @param - void
     * @return - view 
    */
    function partner_refuse_to_pay(){
        $this->checkUserSession();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/partner_refuse_to_pay_form');
    }
    
    /* 
     * @desc - this function is used to get booking unit detail
     * @param - booking_id
     * @return - array
    */
    function get_refuse_booking_detail(){
        $booking_id = trim($this->input->post("booking_id"));
        $select = "booking_unit_details.id, booking_unit_details.booking_id, booking_unit_details.partner_net_payable, booking_unit_details.vendor_basic_charges,"
                . "services.services,booking_unit_details.appliance_brand,booking_unit_details.appliance_category,booking_unit_details.appliance_capacity,booking_unit_details.price_tags,"
                . "booking_unit_details.product_or_services, partner_refuse_to_pay, vendor_basic_charges";
        $where = array('booking_unit_details.booking_id'=> $booking_id);
        $joinDataArray["services"] = "services.id=booking_unit_details.service_id";
        $JoinTypeTableArray = array('services'=>'left');
        $result['data'] = $this->booking_model->get_advance_search_result_data("booking_unit_details",$select,$where,$joinDataArray,"",array("booking_unit_details.booking_id"=>"ASC"),
                "",$JoinTypeTableArray);
        $where_internal_status = array("page" => "partner_refuse_to_pay", "active" => '1');
        $internal_status = $this->booking_model->get_internal_status($where_internal_status);
        $result['remarks'] = $internal_status;
        echo json_encode($result);
    }
    
    /* 
     * @desc - this function is used to generate credit note for partner
     * @param - booking_id, booking_unit_ids
     * @return - boolean
    */
    function process_refuse_to_pay(){
        $booking_id = $this->input->post('booking_id');
        $postData = json_decode($this->input->post('postData'));
        $remarks = $this->input->post('remarks');
        $unit_bookings = array();
        $data = array();
        $invoice = array();
        $vendor_invoice_data = array();
        $vendor_invoice = array();
        $partner_reference_invoice = array();
        $vendor_reference_invoice = array();
        $invoice_id = "";
        $sd = $ed = $invoice_date = date("Y-m-d");
        $booking_data = $this->booking_model->get_bookings_count_by_any('assigned_vendor_id, partner_id', array('booking_id'=>$booking_id));
        
        $partner_data = $this->partner_model->getpartner_details("gst_number,"
                        . "company_name, state, address as company_address, district, pincode, "
                        . "invoice_email_to,invoice_email_cc", array('partners.id' => $booking_data[0]['partner_id']));
        
        $partner_id = $booking_data[0]['partner_id'];
        
        $vendor_data = $this->vendor_model->getVendorDetails("gst_no as gst_number,"
                        . "company_name, state, address as company_address, district, pincode, "
                        . "primary_contact_email as invoice_email_to,owner_email as invoice_email_cc", array('id' => $booking_data[0]['assigned_vendor_id']));
         
        foreach ($postData as $key => $value){
            array_push($unit_bookings, $value->booking_unit_ids);
           
           $select = 'booking_unit_details.partner_net_payable, services.services as service'
                   . ', booking_unit_details.price_tags, booking_unit_details.appliance_category,booking_unit_details.appliance_capacity'
                   . ',booking_unit_details.partner_invoice_id, booking_unit_details.tax_rate, booking_unit_details.product_or_services, vendor_foc_invoice_id, vendor_basic_charges';
           $where = array('booking_unit_details.id'=>$value->booking_unit_ids);
           $joinDataArray["services"] = "services.id=booking_unit_details.service_id";
           $booking_unit_data = $this->reusable_model->get_search_query('booking_unit_details', $select, $where, $joinDataArray, "", "", "", "", "")->result_array();
            array_push($partner_reference_invoice, $booking_unit_data[0]['partner_invoice_id']);
            array_push($vendor_reference_invoice, $booking_unit_data[0]['vendor_foc_invoice_id']);
            if($booking_unit_data[0]['partner_invoice_id']){
                /* If partner_invoice_id exist we create credit note for partner */
                $description = $booking_unit_data[0]['service']." ".$booking_unit_data[0]['appliance_category']."(".$booking_unit_data[0]['appliance_capacity'].")";
               
                $data[$key]['description'] =  $description;
                $data[$key]['rate'] = $booking_unit_data[0]['partner_net_payable'];
                $data[$key]['qty'] = 1;
                $data[$key]['taxable_value'] = $booking_unit_data[0]['partner_net_payable'];
                $data[$key]['product_or_services'] = $booking_unit_data[0]['product_or_services'];
                if(!empty($partner_data[0]['gst_number'])){
                     $data[$key]['gst_number'] = $partner_data[0]['gst_number'];
                } 
                else{
                    $data[$key]['gst_number'] = TRUE;
                }
                
                $data[$key]['company_name'] = $partner_data[0]['company_name'];
                $data[$key]['company_address'] = $partner_data[0]['company_address'];
                $data[$key]['district'] = $partner_data[0]['district'];
                $data[$key]['pincode'] = $partner_data[0]['pincode'];
                $data[$key]['state'] = $partner_data[0]['state'];
                $data[$key]['hsn_code'] = HSN_CODE;
                $data[$key]['gst_rate'] = $booking_unit_data[0]['tax_rate'];
            }
            if($booking_unit_data[0]['vendor_foc_invoice_id']){ 
                /* If vendor_foc_invoice_id exist we create Debit Note for vendor */
                $description = $booking_unit_data[0]['service']." ".$booking_unit_data[0]['appliance_category']."(".$booking_unit_data[0]['appliance_capacity'].")";
                $vendor_invoice_data[$key]['description'] =  $description;
                $vendor_invoice_data[$key]['rate'] = $booking_unit_data[0]['vendor_basic_charges'];
                $vendor_invoice_data[$key]['qty'] = 1;
                $vendor_invoice_data[$key]['taxable_value'] = $booking_unit_data[0]['vendor_basic_charges'];
                $vendor_invoice_data[$key]['product_or_services'] = $booking_unit_data[0]['product_or_services'];
                if(!empty($partner_data[0]['gst_number'])){
                     $vendor_invoice_data[$key]['gst_number'] = $vendor_data[0]['gst_number'];
                }
                else {
                    $vendor_invoice_data[$key]['gst_number'] = TRUE;
                }
                
                $vendor_invoice_data[$key]['company_name'] = $vendor_data[0]['company_name'];
                $vendor_invoice_data[$key]['company_address'] = $vendor_data[0]['company_address'];
                $vendor_invoice_data[$key]['district'] = $vendor_data[0]['district'];
                $vendor_invoice_data[$key]['pincode'] = $vendor_data[0]['pincode'];
                $vendor_invoice_data[$key]['state'] = $vendor_data[0]['state'];
                $vendor_invoice_data[$key]['hsn_code'] = HSN_CODE;
                $vendor_invoice_data[$key]['gst_rate'] = $booking_unit_data[0]['tax_rate'];
            }
            
            //insert entry into booking state change
            $booking_state_remarks = $remarks." Booking unit id - ".$value->booking_unit_ids;
            $this->notify->insert_state_change($booking_id, $value->reasons, "", $booking_state_remarks, $this->session->userdata('id'), $this->session->userdata('employee_id'), ACTOR_NOT_DEFINE, NEXT_ACTION_NOT_DEFINE, _247AROUND);
        }
        if(!empty($data)){
            $invoice_id = $this->invoice_lib->create_invoice_id("ARD-CN");
            $response = $this->invoices_model->_set_partner_excel_invoice_data($data, $sd, $ed, "Credit Note", $invoice_date);
            $response['meta']['invoice_id'] = $invoice_id;
            $status = $this->invoice_lib->send_request_to_create_main_excel($response, "final"); 
            if (!empty($status)) { 
                $this->invoice_lib->send_request_to_convert_excel_to_pdf($invoice_id, "final");
                $convert = $this->invoice_lib->convert_invoice_file_into_pdf($response, "final");
                $output_pdf_file_name = $convert['main_pdf_file_name'];
                $response['meta']['invoice_file_main'] = $output_pdf_file_name;
                $response['meta']['copy_file'] = $convert['copy_file'];
                $response['meta']['invoice_file_excel'] = $invoice_id . ".xlsx";

                $this->invoice_lib->upload_invoice_to_S3($invoice_id, false);
                
                /* Send cn mail to partner */
                $email_tag = CREDIT_NOTE_ON_REFUSE_TO_PAY;
                $email_template = $this->booking_model->get_booking_email_template($email_tag);
                $subject = vsprintf($email_template[4], array($booking_id));
                $message = vsprintf($email_template[0], array($booking_id, $invoice_id));
                $email_from = $email_template[2];
                //$to = $partner_data[0]['invoice_email_to'].",".$email_template[1];
                //$cc = $partner_data[0]['invoice_email_cc'].",".$email_template[3];
                $to = $email_template[1];
                $cc = $email_template[3];

                $this->notify->sendEmail($email_from, $to, $cc, $email_template[5], $subject, $message, TMP_FOLDER.$output_pdf_file_name, $email_tag, "", $booking_id);
                
                
                unlink(TMP_FOLDER . $invoice_id . ".xlsx");
                unlink(TMP_FOLDER . "copy_" . $invoice_id . ".xlsx");
                
                $partner_reference_invoice_id = "";
                $partner_reference_array = array_unique($partner_reference_invoice);
                if(count($partner_reference_array)=='1'){
                    $partner_reference_invoice_id = $partner_reference_array[0];
                }
                
                $response['meta']['invoice_id'] = $invoice_id;
                $response['meta']['reference_invoice_id'] = $partner_reference_invoice_id;
                $response['meta']['vertical'] = SERVICE;
                $response['meta']['category'] = INSTALLATION_AND_REPAIR;
                $response['meta']['sub_category'] = CREDIT_NOTE;
                $response['meta']['accounting'] = 1;
                
                $this->invoice_lib->insert_invoice_breackup($response);
                $invoice = $this->invoice_lib->insert_vendor_partner_main_invoice($response, "B", "Credit Note", "partner", $partner_id, $convert, $this->session->userdata('id'), HSN_CODE);
                $last_invoice_id = $this->invoices_model->insert_new_invoice($invoice);
                if($last_invoice_id){
                    $i = 0;
                    foreach ($postData as $key => $value){ 
                        $booking_cn_dn_data = array(
                            "entity_type" => "partner",
                            "entity_id" => $partner_id,
                            "booking_id" => $booking_id,
                            "booking_unit_id" => $value->booking_unit_ids,
                            "invoice_type" => "Credit Note",
                            "invoice_id" => $invoice_id,
                            "reference_invoice_id" => $partner_reference_invoice[$i]
                        );
                        $booking_cn_dn_id = $this->invoices_model->insert_into_booking_debit_credit_detils($booking_cn_dn_data);
                        $i++;
                    }
                } 
            }
        }
        
        if(!empty($vendor_invoice_data)){
            $vendor_invoice_id = $this->invoice_lib->create_invoice_id("ARD-DN");
            $response = $this->invoices_model->_set_partner_excel_invoice_data($vendor_invoice_data, $sd, $ed, "Debit Note", $invoice_date);
            $response['meta']['invoice_id'] = $vendor_invoice_id;
            $status = $this->invoice_lib->send_request_to_create_main_excel($response, "final"); 
            if (!empty($status)) { 
                $this->invoice_lib->send_request_to_convert_excel_to_pdf($vendor_invoice_id, "final");
                $convert = $this->invoice_lib->convert_invoice_file_into_pdf($response, "final");
                $output_pdf_file_name = $convert['main_pdf_file_name'];
                $response['meta']['invoice_file_main'] = $output_pdf_file_name;
                $response['meta']['copy_file'] = $convert['copy_file'];
                $response['meta']['invoice_file_excel'] = $vendor_invoice_id . ".xlsx";

                $this->invoice_lib->upload_invoice_to_S3($vendor_invoice_id, false);
                
                 /* Send DN mail to vendor */
                $email_tag = DEBIT_NOTE_ON_REFUSE_TO_PAY;
                $email_template = $this->booking_model->get_booking_email_template($email_tag);
                $subject = vsprintf($email_template[4], array($booking_id));
                $message = vsprintf($email_template[0], array($booking_id, $vendor_invoice_id));
                $email_from = $email_template[2];
                //$to = $vendor_data[0]['invoice_email_to'].",".$email_template[1];
                //$cc = $vendor_data[0]['invoice_email_cc'].",".$email_template[3];
                $to = $email_template[1];
                $cc = $email_template[3];

                $this->notify->sendEmail($email_from, $to, $cc, $email_template[5], $subject, $message, TMP_FOLDER.$output_pdf_file_name, $email_tag, "", $booking_id);
                
                unlink(TMP_FOLDER . $vendor_invoice_id . ".xlsx");
                unlink(TMP_FOLDER . "copy_" . $vendor_invoice_id . ".xlsx");
                
                $vendor_reference_invoice_id = "";
                $vendor_reference_array = array_unique($vendor_reference_invoice);
                if(count($vendor_reference_array)=='1'){
                    $vendor_reference_invoice_id = $vendor_reference_array[0];
                }
                
                $response['meta']['invoice_id'] = $vendor_invoice_id;
                $response['meta']['reference_invoice_id'] = $vendor_reference_invoice_id;
                $response['meta']['vertical'] = SERVICE;
                $response['meta']['category'] = INSTALLATION_AND_REPAIR;
                $response['meta']['sub_category'] = DEBIT_NOTE;
                $response['meta']['accounting'] = 1;
                
                $this->invoice_lib->insert_invoice_breackup($response);
                $vendor_invoice = $this->invoice_lib->insert_vendor_partner_main_invoice($response, "A", "Debit Note", "vendor", $booking_data[0]['assigned_vendor_id'], $convert, $this->session->userdata('id'), HSN_CODE);
                $last_invoice_id = $this->invoices_model->insert_new_invoice($vendor_invoice);
                if($last_invoice_id){
                    $i = 0;
                    foreach ($postData as $key => $value){ 
                        $booking_cn_dn_data = array(
                            "entity_type" => "vendor",
                            "entity_id" => $booking_data[0]['assigned_vendor_id'],
                            "booking_id" => $booking_id,
                            "booking_unit_id" => $value->booking_unit_ids,
                            "invoice_type" => "Debit Note",
                            "invoice_id" => $vendor_invoice_id,
                            "reference_invoice_id" => $vendor_reference_invoice[$i]
                        );
                        $booking_cn_dn_id = $this->invoices_model->insert_into_booking_debit_credit_detils($booking_cn_dn_data);
                        $i++;
                    }
                } 
            }
        }
        
        $last_id = $this->reusable_model->update_table_where_in('booking_unit_details', array("pay_to_sf"=>0, "partner_refuse_to_pay"=>1), array('id'=>$unit_bookings));
        if($last_id){
            echo true;
        }
        else{
            echo false;
        }
    }
}
