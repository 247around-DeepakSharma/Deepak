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

                        $this->notify->sendEmail($email_from, $to, $cc, $bcc, $subject, $message, $pdf_attachement_url,'customer_paid_invoice_to_vendor');



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


                            $this->notify->sendEmail($email_from, $to, $cc, $bcc, $subject, $message, $customer_attachement_url,'customer_paid_invoice');
                            // $this->notify->sendEmail($email_from, $to, $cc, $bcc, $subject, $message, $customer_attachement_url, 'customer_paid_invoice');

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
                "remarks" => ''
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
            "remarks" => $booking_id
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

                    $this->notify->sendEmail($email_from, $to, $cc, $bcc, $subject, $message, $customer_attachement_url,'customer_paid_invoice');
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


}
