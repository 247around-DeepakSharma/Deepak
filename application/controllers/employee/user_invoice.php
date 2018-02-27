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
        $this->load->library("invoice_lib");
        $this->load->library('miscelleneous');

    }
    /**
     * @desc
     * @param String $booking_id
     */
    function payment_invoice_for_customer($booking_id) {

        $select = "service_centres.company_name, service_centres.address as sf_address, "
                . "service_centres.pincode as sf_pincode, service_centres.district as sf_district, service_centres.state as sf_state, service_centres.gst_no, service_centres.owner_phone_1, "
                . "users.name, users.home_address, users.phone_number,users.user_email, users.pincode, users.city, users.state, booking_details.amount_due, "
                . "booking_details.amount_paid, booking_details.quantity, request_type, services, booking_details.quantity, booking_primary_contact_no,  "
                . "sc_code, booking_details.user_id, booking_details.closed_date, booking_details.assigned_vendor_id, owner_email, primary_contact_email";
        $request['where'] = array("booking_details.booking_id" => $booking_id, 'amount_paid > ' . MAKE_CUTOMER_PAYMENT_INVOICE_GREATER_THAN => NULL);
        $request['length'] = -1;
        $data = $this->booking_model->get_bookings_by_status($request, $select);
        if (!empty($data)) {
            $exist_invoice_id = "";
            $unit = $this->booking_model->get_unit_details(array("booking_id" => $booking_id), false, "user_invoice_id");
            if (!empty($unit)) {
                $unique = array_unique(array_map(function ($k) {
                            return $k['user_invoice_id'];
                        }, $unit));

                foreach ($unique as $value) {
                    if (!empty($value)) {
                        $exist_invoice_id = $value;
                    }
                }
            }
            if (empty($exist_invoice_id)) {
                $invoice_id = $this->invoice_lib->create_invoice_id($data[0]->sc_code);
            } else {
                // Need to chnage
                $invoice_id = $exist_invoice_id;
            }
            $invoice = array();
            $invoice[0]['description'] = ucwords($data[0]->services) . " (" . $data[0]->request_type . ") ";
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
            $response['meta']['owner_phone_1'] = $data[0]->owner_phone_1;

            $status = $this->invoice_lib->send_request_to_create_main_excel($response, "final");
            if ($status) {
                log_message('info', __FUNCTION__ . ' Invoice File is created. invoice id' . $response['meta']['invoice_id']);
                $convert = $this->invoice_lib->send_request_to_convert_excel_to_pdf($response['meta']['invoice_id'], "final");
                $output_pdf_file_name = $convert['main_pdf_file_name'];
                
                $email_template = $this->booking_model->get_booking_email_template("customer_paid_invoice_to_vendor");
                $subject = $email_template[4];
                $message = $email_template[0];
                $email_from = $email_template[2];
//
                $to = $data[0]->owner_email;
                $cc = $data[0]->primary_contact_email;
                $this->invoice_lib->upload_invoice_to_S3($response['meta']['invoice_id'], false);
                
                $pdf_attachement_url = 'https://s3.amazonaws.com/' . BITBUCKET_DIRECTORY . '/invoices-excel/' . $output_pdf_file_name;
                
                $this->notify->sendEmail($email_from, $to, $cc, "", $subject, $message, $pdf_attachement_url);
                
                $sms['tag'] = "customer_paid_invoice";
                
                $tinyUrl = $this->miscelleneous->getShortUrl($pdf_attachement_url);
                if($tinyUrl){
                    $sms['smsData']['url'] = $tinyUrl;
                    $sms['phone_no'] = $response['meta']['customer_phone_number'];
                    $sms['booking_id'] = $booking_id;
                    $sms['type'] = "user";
                    $sms['type_id'] = $data[0]->user_id;

                    $this->notify->send_sms_msg91($sms);
                }
               
                $agent_id = $this->session->userdata('id');

                $this->insert_payment_invoice($booking_id, $response, $data[0]->user_id, $data[0]->closed_date, $agent_id, $convert);
            } else {
                log_message("info" . __METHOD__ . " Excel Not Created Booking ID" . $booking_id);
            }
        } else {
            log_message("info" . __METHOD__ . " Data Not Found Booking ID" . $booking_id);
        }
    }

    function insert_payment_invoice($booking_id, $invoice, $user_id, $closed_date, $agent_id, $convert){
       $invoice_details = array(
                'invoice_id' => $invoice['meta']['invoice_id'],
                'type' => 'Cash',
                'type_code' => 'A',
                'vendor_partner' => 'user',
                'vendor_partner_id' => $user_id,
                'invoice_file_main' => $convert['main_pdf_file_name'],
                'invoice_file_excel' => $invoice['meta']['invoice_id'] . '.xlsx',
                'invoice_detailed_excel' => '',
                'invoice_date' => $closed_date,
                'from_date' => date("Y-m-d", strtotime($closed_date)),
                'to_date' => date("Y-m-d", strtotime($closed_date)),
                'num_bookings' =>  1,
                "parts_count" => 0,
                'total_service_charge' => $invoice['meta']['total_taxable_value'],
                'total_additional_service_charge' => 0,
                'parts_cost' => 0,
                'vat' => 0, //No VAT here in Cash invoice
                'total_amount_collected' => $invoice['meta']['sub_total_amount'],
                'rating' => 0,
                'around_royalty' => $invoice['meta']['sub_total_amount'],
                'upcountry_price' => 0,
                'upcountry_distance' => 0,
                'upcountry_booking' => 0,
                
                //Amount needs to be collected from Vendor
                'amount_collected_paid' => $invoice['meta']['sub_total_amount'],
                //Mail has not 
                'mail_sent' => 1,
                //SMS has been sent or not
                'sms_sent' => 1,
                //Add 1 month to end date to calculate due date
                'due_date' =>$closed_date,
                //add agent_id
                'agent_id' => $agent_id,
                "cgst_tax_rate" => $invoice['meta']['cgst_tax_rate'],
                "sgst_tax_rate" => $invoice['meta']['sgst_tax_rate'],
                "igst_tax_rate" => $invoice['meta']['igst_tax_rate'],
                "igst_tax_amount" => $invoice['meta']["igst_total_tax_amount"],
                "sgst_tax_amount" => $invoice['meta']["sgst_total_tax_amount"],
                "cgst_tax_amount" => $invoice['meta']["cgst_total_tax_amount"],
                "hsn_code" => $invoice['booking'][0]['hsn_code'],
                "invoice_file_pdf" => $convert['copy_file'],
                "remarks" => $booking_id
            );

            $this->invoices_model->action_partner_invoice($invoice_details);
            
            $this->booking_model->update_booking_unit_details_by_any(array('booking_id' => $booking_id), array("user_invoice_id" => $invoice['meta']['invoice_id']));
            
            return true;
    }
    
}
