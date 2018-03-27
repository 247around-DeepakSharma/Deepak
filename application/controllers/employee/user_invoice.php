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

     * @desc

    }
    /**
     * 

     * @desc This method is used to generate Customer invoice on the behalf of Sf
     * @param String $booking_id
     * @param String $agent_id
     */
    function payment_invoice_for_customer($booking_id, $agent_id) {
        log_message("info",__METHOD__. " Enering .. for booking id ". $booking_id. " agent ID ".$agent_id);
        $select = "service_centres.company_name, service_centres.address as sf_address, "
                . "service_centres.pincode as sf_pincode, service_centres.district as sf_district, service_centres.state as sf_state, service_centres.gst_no, service_centres.owner_phone_1, "
                . "users.name, users.home_address, users.phone_number,users.user_email, users.pincode, users.city, users.state, booking_details.amount_due, "
                . "booking_details.amount_paid, booking_details.quantity, request_type, services, booking_details.quantity, booking_primary_contact_no,  "
                . "sc_code, booking_details.user_id, booking_details.closed_date, booking_details.assigned_vendor_id, owner_email, primary_contact_email";

        $request['where'] = array("booking_details.booking_id" => $booking_id, 'amount_paid > ' . MAKE_CUTOMER_PAYMENT_INVOICE_GREATER_THAN => NULL);
        $request['length'] = -1;
        $data = $this->booking_model->get_bookings_by_status($request, $select);
        if (!empty($data)) {
            $prod =  ucwords($data[0]->services) . " (" . $data[0]->request_type . ") ";
           
            $unit = $this->booking_model->get_unit_details(array("booking_id" => $booking_id, "booking_status != 'Cancelled' " => NULL));
            if (!empty($unit)) {
                $unique = array_unique(array_map(function ($k) {
                            return $k['price_tags'];
                        }, $unit));

                $prod = $data[0]->services ." (" .implode(",", $unique).")";
            }

            $invoice_id = $this->invoice_lib->create_invoice_id($data[0]->sc_code);
            log_message("info", __METHOD__. " Invoice ID created ". $invoice_id);
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
            $invoice[0]['rate'] = "";
            $invoice[0]['qty'] = $data[0]->quantity;
            $invoice[0]['hsn_code'] = HSN_CODE;

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
                $convert = $this->invoice_lib->send_request_to_convert_excel_to_pdf($response['meta']['invoice_id'], "final", true, true);
                $this->invoice_lib->upload_invoice_to_S3($response['meta']['invoice_id'], false, true);
                
                $output_pdf_file_name = $convert['main_pdf_file_name'];

                $copy_pdf_file_name = $convert['copy_file'];
                //$triplicate_pdf_file_name = $convert['triplicate_file'];

                $pathinfo1 = pathinfo($output_pdf_file_name);
                if($pathinfo1['extension'] == 'xls' || $pathinfo1['extension'] == 'xlsx'){
                    log_message("info", __METHOD__. " SF Invoice Pdf is not generated ".$output_pdf_file_name );
                }  else {
                    //Send invoice to SF
                    $email_template = $this->booking_model->get_booking_email_template("customer_paid_invoice_to_vendor");
                    $subject =  vsprintf($email_template[4], array($booking_id));
                    $message = $email_template[0];
                    $email_from = $email_template[2];

                    $to = $data[0]->owner_email;
                    $cc = $email_template[3].",".$data[0]->primary_contact_email;
                    $bcc = $email_template[5];


                    $pdf_attachement_url = S3_WEBSITE_URL . 'invoices-excel/' . $output_pdf_file_name;
                    $this->notify->sendEmail($email_from, $to, $cc, $bcc, $subject, $message, $pdf_attachement_url);
                }
                
                $pathinfo = pathinfo($copy_pdf_file_name);
                if($pathinfo['extension'] == 'xls' || $pathinfo['extension'] == 'xlsx'){
                    log_message("info", __METHOD__. " Invoice Pdf is not generated ".$copy_pdf_file_name );
                    $sms['tag'] = "customer_paid_invoice_pdf_not_generated";
                    
                } else {
                    $customer_attachement_url = S3_WEBSITE_URL . 'invoices-excel/' .$copy_pdf_file_name ;

                    $tinyUrl = $this->miscelleneous->getShortUrl($customer_attachement_url);
                    // If invoice links is not generating then we will not send invoice Link in sms
                    if($tinyUrl){

                        $sms['tag'] = "customer_paid_invoice";

                    } else {
                        $sms['tag'] = "customer_paid_invoice_pdf_not_generated";
                        
                        log_message("info", __METHOD__. " Short url failed for booking id ". $booking_id);
                    }
                
                    // Send Invoice to Customer
                    if (filter_var($data[0]->user_email, FILTER_VALIDATE_EMAIL)) {
                        $email_template = $this->booking_model->get_booking_email_template("customer_paid_invoice");
                        $subject =  vsprintf($email_template[4], array($booking_id));
                        $message = $email_template[0];
                        $email_from = $email_template[2];

                        $to = $data[0]->user_email;
                        $cc = $email_template[3];
                        $bcc = $email_template[5];

                        $this->notify->sendEmail($email_from, $to, $cc, $bcc, $subject, $message, $customer_attachement_url);
                    }
                }

                $sms['smsData']['amount'] = $data[0]->amount_paid;
                $sms['smsData']['booking_id'] = $booking_id;
                if($sms['tag'] == "customer_paid_invoice"){
                    
                    $sms['smsData']['url'] = $tinyUrl;
                } 
                
                $sms['phone_no'] = $response['meta']['customer_phone_number'];
                $sms['booking_id'] = $booking_id;
                $sms['type'] = "user";
                $sms['type_id'] = $data[0]->user_id;
                $this->notify->send_sms_msg91($sms);
                
                $this->insert_payment_invoice($booking_id, $response, $data[0]->assigned_vendor_id, 
                      $data[0]->closed_date, $agent_id, $convert, $data[0]->user_id);

            } else {
                log_message("info" . __METHOD__ . " Excel Not Created Booking ID" . $booking_id);
            }
        } else {
            log_message("info" . __METHOD__ . " Data Not Found Booking ID" . $booking_id);
        }
    }

    function insert_payment_invoice($booking_id, $invoice, $sf_id, $closed_date, $agent_id, $convert, $user_id){
        $main_invoice = array(
                'invoice_id' => $invoice['meta']['invoice_id'],
                'entity_to' => 'user',
                'bill_to_party' => $user_id,
                'entity_from' => 'vendor',
                'bill_from_party' => $sf_id,
                'booking_id' => $booking_id,
                'main_invoice_file' => $convert['main_pdf_file_name'],
                "duplicate_file" => $convert['copy_file'],
                'invoice_excel' => $invoice['meta']['invoice_id'] . '.xlsx',
                'triplicate_file' => $convert['triplicate_file'],
                'invoice_date' => date("Y-m-d H:i:s", strtotime($closed_date)),
                'from_date' => date("Y-m-d H:i:s", strtotime($closed_date)),
                'to_date' => date("Y-m-d H:i:s", strtotime($closed_date)),
                'due_date' => date("Y-m-d H:i:s", strtotime($closed_date)),
                'total_basic_amount' => $invoice['meta']['total_taxable_value'],
                'total_invoice_amount' => $invoice['meta']['sub_total_amount'],
                'agent_id' => $agent_id,
                "total_igst_tax_amount" => $invoice['meta']["igst_total_tax_amount"],
                "total_sgst_tax_amount" => $invoice['meta']["sgst_total_tax_amount"],
                "total_cgst_tax_amount" => $invoice['meta']["cgst_total_tax_amount"],
                'create_date' => date('Y-m-d H:i:s'),
                "remarks" => ''
            );

        $this->invoices_model->insert_invoice($main_invoice);
        $invoice_breakup = array();
        foreach($invoice['booking'] as $value){
            $invoice_details = array(
                "invoice_id" => $invoice['meta']['invoice_id'],
                "description" => $value['description'],
                "qty" => $value['qty'],
                "rate" => $value['rate'],
                "taxable_value" => $value['taxable_value'],
                "cgst_tax_rate" => (isset($value['cgst_rate']) ? $value['cgst_rate'] : 0),
                "sgst_tax_rate" => (isset($value['sgst_rate']) ? $value['igst_rate'] : 0),
                "igst_tax_rate" => (isset($value['igst_rate']) ? $value['igst_rate'] : 0),
                "cgst_tax_amount" => (isset($value['cgst_tax_amount']) ? $value['cgst_tax_amount'] : 0),
                "sgst_tax_amount" => (isset($value['sgst_tax_amount']) ? $value['sgst_tax_amount'] : 0),
                "igst_tax_amount" => (isset($value['igst_tax_amount']) ? $value['igst_tax_amount'] : 0),
                "hsn_code" => $value['hsn_code'],
                "toal_amount" => $value['toal_amount'],
                "create_date" => date('Y-m-d H:i:s')
                
            );
            
            array_push($invoice_breakup, $invoice_details);
        }
            
            
        $this->invoices_model->insert_invoice_breakup($invoice_breakup);
            
        $this->booking_model->update_booking_unit_details_by_any(array('booking_id' => $booking_id), array("user_invoice_id" => $invoice['meta']['invoice_id']));
            
        return true;
    }
    }

}
