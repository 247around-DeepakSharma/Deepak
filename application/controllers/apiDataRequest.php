<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//error_reporting(E_ERROR);
//ini_set('display_errors', '0');

class ApiDataRequest extends CI_Controller {

    private $requestData = array();
    private $requestUrl = NULL;
            

    function __Construct() {
        parent::__Construct();
        $this->load->model("inventory_model");
        $this->load->model("vendor_model");
        $this->load->model("engineer_model");
        $this->load->model("invoices_model");
        $this->load->model("service_centers_model");
        $this->load->library('form_validation');
        $this->load->library('notify');
        $this->load->library('booking_utilities');

    }
    
    function index() {
        log_message('info', "Entering: " . __METHOD__. json_encode($_POST, TRUE));
        $this->jsonResponseString = null;
      
        if ($this->input->post() && array_key_exists("requestType", $this->input->post())) {
              log_message('info', "request key exists");
              $this->requestData = $this->input->post();
              $this->requestUrl =  $this->requestData['requestType'];
              
              $this->processRequest();
            
        } else {
            log_message('info', "request key NOT exists");
            $this->sendJsonResponse(array('0001', 'failure'));
        }
    }
    
    private function processRequest(){
        log_message('info', "Entering: " . __METHOD__.$this->requestUrl);
        
        switch ($this->requestUrl){
            case SPARE_OOW_EST_REQUESTED:
                $this->spareOowEstRequestedData();
                break;
            
            case 'UPDATE_OOW_EST':
                $this->update_estimate_oow();
                break;
            
            case CUSTOMER_INVOICE_TAG:
                $this->get_customer_invoice();
               break;
        }
    }
   /**
    * @desc This function is used to get request for est. spare parts data from Admin, Partner, Service
    * It called from Ajax 
    */
    function spareOowEstRequestedData(){
        log_message('info', "Entering: " . __METHOD__);
        if(isset($this->requestData['where'])){
            $this->requestData['where'] = json_decode($this->requestData['where'], true);
            $this->requestData['is_inventory'] = 1;
        }
        
        $data = $this->inventory_model->get_spare_parts_query($this->requestData);
        $sp_list = array();
        switch ($this->requestData['crmType']){
            case 'Vendor':
                 $sp_list = $this->getVendorOOWView($data);
                break;
            case 'Partner':
                $sp_list = $this->getPartnerOOWView($data);
                break;
            case 'Admin':
                $sp_list = $this->getAdminOOWView($data);
                break;
        }
        
        
        $output = array(
            "draw" => $this->requestData['draw'],
            "recordsTotal" => $this->inventory_model->count_spare_parts($this->requestData),
            "recordsFiltered" =>  $this->inventory_model->count_spare_filtered($this->requestData),
            "data" => $sp_list,
        );
       
        echo json_encode($output);
       
    }
    
    function getPartnerOOWView($data){
        log_message('info', "Entering: " . __METHOD__);
        $no = $this->requestData['start'];
        $output = array();
        foreach ($data as $sp_list) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a style="color:blue;" href='.base_url().'partner/booking_details/'.$sp_list->booking_id.' target="_blank" title="View">'.$sp_list->booking_id.'</a>'; 
            $row[] = $sp_list->services ;
            $row[] = "<span style='word-break: break-all;'>". $sp_list->parts_requested ."</span>";
            $row[] = $sp_list->part_number ;
            $row[] = $sp_list->age_of_request;
            $row[] = $sp_list->model_number;
            $row[] = $sp_list->serial_number;
            $row[] = "<a style='color:#337ab7' href='https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$sp_list->defective_parts_pic."' target = '_blank' >Click Here</a>";
            $row[] = "<a style='color:#337ab7' href='https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$sp_list->serial_number_pic."' target = '_blank' >Click Here</a>";

            $c = '"'.$sp_list->id.'", "'.$sp_list->booking_id.'", "'.$sp_list->assigned_vendor_id.'", "'.$sp_list->amount_due.'" , "'.$sp_list->partner_id.'"';
            $row[] = '<input type="number" placeholder="Enter your Billing Price" style="width: fit-content;" step="0.01" id="estimate_cost_'.$sp_list->id.'" class="col-md-8"/>';
            $row[] = "<button id='btn_oow_".$sp_list->id."' "
                    . "class = 'btn btn-sm btn-info' onclick='update_spare_estimate_cost(".$c .")' >Submit</button>";

            $output[] = $row;
        }
        
        return $output;
    }
    
    function getAdminOOWView($data){
        log_message('info', "Entering: " . __METHOD__);
        $no = $this->requestData['start'];
        $output = array();
        foreach ($data as $sp_list) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="'.  base_url().'employee/booking/viewdetails/'.$sp_list->booking_id.'" target="_blank">'.$sp_list->booking_id.'</a>';
            $row[] = $sp_list->parts_requested;
            $row[] = $sp_list->model_number;
            $row[] = $sp_list->serial_number;
            $row[] = $sp_list->age_of_request;
            
            $c = '"'.$sp_list->id.'", "'.$sp_list->booking_id.'", "'.$sp_list->assigned_vendor_id.'", "'.$sp_list->amount_due.'" ';
            $row[] = '<input type="number" step="0.01" id="estimate_cost_'.$sp_list->id.'" class="col-md-8"/>';
            if($sp_list->partner_id != _247AROUND ){
                $row[] = "";
                
            } else {
                $row[] = "<button id='btn_oow_".$sp_list->id."' "
                    . "class = 'btn btn-sm btn-info' onclick='update_spare_estimate_cost(".$c .")' >Submit</button>";
            }
            $row[] = "<button id='btn_reject_".$sp_list->id."' "
                    . "class = 'btn btn-sm btn-danger' onclick='reject_oow_model(".$c .")' >Reject</button>";

            $output[] = $row;
        }
        
        return $output;
    }
    
    
    function getVendorOOWView($data){
        log_message('info', "Entering: " . __METHOD__);
        $no = $this->requestData['start'];
        $output = array();
        foreach ($data as $sp_list) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = "<a href='".  base_url()."service_center/booking_details/".urlencode(base64_encode($sp_list->booking_id))."' target='_blank' >".$sp_list->booking_id."</a>";
            $row[] = $sp_list->name;
            if(!empty($sp_list->booking_alternate_contact_no)){
                $row[] = $sp_list->booking_primary_contact_no."/<br/>".$sp_list->booking_alternate_contact_no;
            } else {
                $row[] = $sp_list->booking_primary_contact_no;
            }
            $row[] = $sp_list->age_of_est_given;
            $row[] = ($sp_list->sell_price + $sp_list->amount_due);
            $a = '"'.$sp_list->id.'", "'.$sp_list->booking_id.'"';
            
            $row[] = "<button id='btn_oow_".$sp_list->id."_1' "
                    . "class = 'btn btn-sm btn-primary' onclick='spare_estimate_cost(".$a .")'  >Approve</button>";
            $row[] = "<a id='btn_oow_".$sp_list->id."_0' "
                    . "class = 'btn btn-sm btn-danger' "
                    . "href='".  base_url()."service_center/cancel_booking_form/".urlencode(base64_encode($sp_list->booking_id))."'  >Reject</a>";

            $output[] = $row;
        }
        
        return $output;
    }

    private function sendJsonResponse($code) {
        $this->jsonResponseString['code'] = $code[0];
        $this->jsonResponseString['result'] = $code[1];
        $responseData = array("data" => $this->jsonResponseString);
        $response = json_encode($responseData, JSON_UNESCAPED_SLASHES);
        echo $response;
    }
    
    /**
     * @desc This function is used to update estimate cost when Partner provide.
     * @param int $id - Spare Parts Id
     */
    function update_estimate_oow() {      
        log_message("info", __METHOD__ . json_encode($this->input->post()));

        $this->form_validation->set_rules('estimate_cost', 'Estimate cost', 'trim|numeric|greater_than[1]|required');
        $this->form_validation->set_rules('booking_id', 'Booking ID', 'trim|required');
        $this->form_validation->set_rules('partner_id', 'Partner ID', 'trim|required');
        $this->form_validation->set_rules('sp_id', 'Spare ID', 'trim|required');
        $this->form_validation->set_rules('agent_id', 'Agent ID', 'trim|required');
        $this->form_validation->set_rules('assigned_vendor_id', 'Assigned ID', 'trim|required');
        $this->form_validation->set_rules('amount_due', 'Amount Due', 'trim|required');

        if ($this->form_validation->run() == FALSE) {

            log_message('info', __FUNCTION__ . '=> Form Validation is not updated by Partner ' .
                    " Data" . print_r($this->input->post(), true) . " Validators " . validation_errors());
            echo "Error";
        } else {
            $id = $this->input->post("sp_id");
            $gst_rate = $this->input->post('gst_rate');
            $spare_data = $this->partner_model->get_spare_parts_by_any('parts_requested_type, booking_details.service_id, requested_inventory_id', array('spare_parts_details.id' => $id), true);
            if (!empty($spare_data)) {
                
                $margin = $this->inventory_model->get_oow_margin($spare_data[0]['requested_inventory_id'], array('part_type' => $spare_data[0]['parts_requested_type'],
                    'service_id' => $spare_data[0]['service_id']));
               
                $spare_oow_est_margin = $margin['oow_est_margin']/100;
                $repair_oow_vendor_percentage = $margin['oow_vendor_margin'];
                $gst_rate = !(empty($margin['gst_rate']))? $margin['gst_rate']: $gst_rate;

                $booking_id = $this->input->post("booking_id");

                $vendor_id = $this->input->post("assigned_vendor_id");
                $amount_due = $this->input->post("amount_due");

                $estimate_cost = $this->input->post("estimate_cost");
                $partner_id = $this->input->post("partner_id");
                $agent_id = $this->input->post("agent_id");
                $where = array('id' => $id);
                $data['status'] = SPARE_OOW_EST_GIVEN;
                $data['purchase_price'] = $estimate_cost;
                $data['sell_price'] = ($estimate_cost + $estimate_cost * $spare_oow_est_margin );
                $data['estimate_cost_given_date'] = date('Y-m-d');
                $data['invoice_gst_rate'] = $gst_rate;
                //Update Spare Parts Table
                $response = $this->service_centers_model->update_spare_parts($where, $data);

                if ($response) {
                    //Update Unit Table
                    $unit = $this->booking_model->get_unit_details(array('booking_id' => $booking_id));
                    $unit[0]['price_tags'] = REPAIR_OOW_PARTS_PRICE_TAGS;
                    $unit[0]['vendor_basic_percentage'] = ($estimate_cost * $repair_oow_vendor_percentage) / $data['sell_price'];
                    $unit[0]['customer_total'] = $data['sell_price'];
                    $unit[0]['product_or_services'] = "Product";
                    $unit[0]['tax_rate'] = $gst_rate;
                    $unit[0]['create_date'] = date("Y-m-d H:i:s");
                    $unit[0]['ud_update_date'] = date("Y-m-d H:i:s");
                    $unit[0]['partner_net_payable'] = 0;
                    $unit[0]['partner_paid_basic_charges'] = 0;
                    $unit[0]['around_paid_basic_charges'] = 0;
                    $unit[0]['around_net_payable'] = 0;
                    unset($unit[0]['id']);
                    //INSERT UNIT
                    $result = $this->booking_model->_insert_data_in_booking_unit_details($unit[0], 1, 1);

                    if (isset($result['unit_id']) && !empty($result['unit_id'])) {
                        //Update unit details in spare parts
                        $response = $this->service_centers_model->update_spare_parts(array('id' => $id), array('booking_unit_details_id' => $result['unit_id']));
                        if (!empty($response)) {
                            log_message("info", "Unit Id Updated in unit details");
                        } else {
                            log_message("info", "Error in Updating unit id in unit details");
                        }
                    }
                    $booking['amount_due'] = ($amount_due + $data['sell_price']);
                    $booking['internal_status'] = SPARE_OOW_EST_GIVEN;
                    $actor = $next_action = 'not_define';
                    $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, $booking['internal_status'], $unit[0]['partner_id'], $booking_id);
                    if (!empty($partner_status)) {
                        $booking['partner_current_status'] = $partner_status[0];
                        $booking['partner_internal_status'] = $partner_status[1];
                        $actor = $booking['actor'] = $partner_status[2];
                        $next_action = $booking['next_action'] = $partner_status[3];
                    }
                    // Update Booking Table
                    $this->booking_model->update_booking($booking_id, $booking);

                    $sc_data['unit_details_id'] = $result['unit_id'];
                    $sc_data['booking_id'] = $booking_id;
                    $sc_data['service_center_id'] = $vendor_id;
                    $sc_data['current_status'] = _247AROUND_PENDING;
                    $sc_data['update_date'] = date('Y-m-d H:i:s');
                    $sc_data['internal_status'] = SPARE_OOW_EST_GIVEN;
                    //Update New item In SF Action Table 
                    $this->vendor_model->insert_service_center_action($sc_data);

                    $isEn = $this->vendor_model->getVendorDetails("isEngineerApp", array("id" => $vendor_id));
                    if ($isEn[0]['isEngineerApp'] == 1) {
                        $en['current_status'] = _247AROUND_PENDING;
                        $en['create_date'] = date('Y-m-d H:i:s');
                        $en['internal_status'] = _247AROUND_PENDING;
                        $en['service_center_id'] = $vendor_id;
                        $en['booking_id'] = $booking_id;
                        $en['unit_details_id'] = $result['unit_id'];

                        $this->engineer_model->insert_engineer_action($en);
                    }

                    //Update SF Action Table
                    $this->vendor_model->update_service_center_action($booking_id, array("current_status" => _247AROUND_PENDING,
                        'internal_status' => SPARE_OOW_EST_GIVEN));
                    //Insert State Change
                    if ($this->session->userdata('partner_id')) {
                        $this->notify->insert_state_change($booking_id, SPARE_OOW_EST_GIVEN, SPARE_OOW_EST_REQUESTED, "", $agent_id, "", $actor, $next_action, $partner_id);
                    } else if ($this->session->userdata('service_center_id')) {
                        $this->notify->insert_state_change($booking_id, SPARE_OOW_EST_GIVEN, SPARE_OOW_EST_REQUESTED, "", $agent_id, "", $actor, $next_action, NULL, $this->session->userdata('service_center_id'));
                    } else {
                        $this->notify->insert_state_change($booking_id, SPARE_OOW_EST_GIVEN, SPARE_OOW_EST_REQUESTED, "", _247AROUND_DEFAULT_AGENT, "", $actor, $next_action, _247AROUND);
                    }
                    $this->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);

                    $template = $this->booking_model->get_booking_email_template("oow_estimate_given");
                    if (!empty($template)) {
                        $to = "";
                        $am_data = $this->miscelleneous->get_am_data($partner_id);
                        if (!empty($am_data)) {
                            $to = $am_data[0]['official_email'];
                        }
                        $rm_details = $this->vendor_model->get_rm_sf_relation_by_sf_id($vendor_id);
                        if (!empty($rm_details)) {
                            $to = (!empty($to)) ? $to . ", " . $rm_details[0]['official_email'] : $rm_details[0]['official_email'];
                        }

                        if (!empty($to)) {
                            $to = $am_data[0]['official_email'];
                            $subject = vsprintf($template[4], $booking_id);
                            $emailBody = vsprintf($template[0], $estimate_cost);

                            $this->notify->sendEmail($template[2], $to, $template[3], '', $subject, $emailBody, "", 'oow_estimate_given', "", $booking_id);
                        }
                    }
                    
                   $a = $this->auto_approve_requested_spare($booking_id, $partner_id );

                    echo "Success";
                } else {
                    echo "Error";
                }
            } else {
                echo 'Error';
            }
        }
    }
    
    function auto_approve_requested_spare($booking_id, $partner_id ){
        log_message('info', __METHOD__. " ". $booking_id);
        $access = $this->partner_model->get_partner_permission(array('partner_id' => $partner_id, 
            'permission_type' => AUTO_APPROVED_OOW_CHARGES_ON_BEHALF_CUSTOMER, 'is_on' => 1));
        
        if(!empty($access)){
            $url = base_url().'employee/service_centers/approve_oow/'.$booking_id;
            $fields = array();

            //url-ify the data for the POST
            $fields_string = http_build_query($fields);

            //open connection
            $ch = curl_init();

            //set the url, number of POST vars, POST data
            curl_setopt($ch,CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_POST, 1);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

            //execute post
            $result = curl_exec($ch);
            //close connection
            curl_close($ch);
            
            return json_decode($result, true);
            
        }
    }

    function get_customer_invoice(){
        log_message('info', __METHOD__);
        switch ($this->requestData['crmType']){
            case 'Vendor':
                 $where = array('entity_to' => "user", "entity_from" => "vendor", "bill_from_party" => $this->session->userdata('service_center_id'));
                 $sp_list = $this->get_cutomer_invoice_details($where);
                break;
            case 'Admin':
                $where = array('entity_to' => "user", "entity_from" => "vendor");
                $sp_list = $this->get_cutomer_invoice_details($where);
                break;
        }
    }
    
     function get_cutomer_invoice_details($where){
        log_message('info', __METHOD__);
        $post = $this->get_post_customer_data();
       
        $post['column_order'] = array( NULL, NULL,'services', 'city','order_date', 'current_status');
        $post['where'] = $where;
        $post['column_search'] = array('invoice.booking_id','invoice.invoice_id', 'total_invoice_amount');
        $list = $this->invoices_model->get_new_invoice_details($post, '*');
        $data = array();
        $no = $post['start'];
        foreach ($list as $invoice) {
            $no++;
            $row =  $this->customer_invoice_table_view($invoice, $no);
            $data[] = $row;
        }
        
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->invoices_model->new_invoice_count_all($post),
            "recordsFiltered" =>  $this->invoices_model->new_invoice_count_filtered($post),
            "data" => $data,
        );
        echo json_encode($output);
    }
    
    function customer_invoice_table_view($invoice, $no){
        $row = array();
        $row[] = $no;
        $row[] = "<a target='_blank' href='".S3_WEBSITE_URL."invoices-excel/".
                $invoice->main_invoice_file."'>$invoice->invoice_id</a>";
        if($this->session->userdata('service_center_id')){
            
             $row[] = "<a href='".  base_url()."service_center/booking_details/".urlencode(base64_encode($invoice->booking_id))."' target='_blank' >".$invoice->booking_id."</a>";
        } else {
             $row[] = "<a href='".  base_url()."employee/booking/viewdetails/".$invoice->booking_id."' target='_blank' >".$invoice->booking_id."</a>";
        }
       
        $row[] = date("jS M, Y", strtotime($invoice->invoice_date));
        
        $row[] = '<i class ="fa fa-inr"></i> '.$invoice->total_basic_amount;
        $row[] = '<i class ="fa fa-inr"></i> '.($invoice->total_cgst_tax_amount + $invoice->total_sgst_tax_amount + $invoice->total_igst_tax_amount);
        $row[] = '<i class ="fa fa-inr"></i> '.$invoice->total_invoice_amount;
        if($this->requestData['crmType'] == "Admin"){
            $agent_id = $this->session->userdata('id');
            $row[] = '<a href="'.  base_url().'employee/user_invoice/regenerate_payment_invoice_for_customer/'.$invoice->booking_id.'/'.$invoice->total_invoice_amount.'/'.$invoice->invoice_id.'/'.$agent_id.'" class="btn btn-sm btn-primary">Regenerate Invoice</a>';
        }
       
        return $row;
    }
    
     function get_post_customer_data(){
        //log_message("info",__METHOD__);
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
        
        return $post;
    }
   
}