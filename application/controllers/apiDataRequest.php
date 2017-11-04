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
        $this->load->model("service_centers_model");
        $this->load->library('form_validation');
        $this->load->library('notify');
        $this->load->library('booking_utilities');

    }
    
    function index() {
        log_message('info', "Entering: " . __METHOD__);
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
            $row[] = $sp_list->parts_requested;
            $row[] = $sp_list->age_of_request;
            $row[] = $sp_list->model_number;
            $row[] = $sp_list->serial_number;
            $row[] = "<a style='color:#337ab7' href='https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$sp_list->defective_parts_pic."' target = '_blank' >Click Here</a>";
            $row[] = "<a style='color:#337ab7' href='https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$sp_list->serial_number_pic."' target = '_blank' >Click Here</a>";

            $c = '"'.$sp_list->id.'", "'.$sp_list->booking_id.'", "'.$sp_list->assigned_vendor_id.'", "'.$sp_list->amount_due.'" ';
            $row[] = '<input type="number" onkeypress="return isNumberKey(event)" id="estimate_cost" class="col-md-8"/>';
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
            $row[] = '<input type="number" onkeypress="return isNumberKey(event)" id="estimate_cost" class="col-md-8"/>';
            if($sp_list->partner_id == _247AROUND ){
                $row[] = "";
                
            } else {
                $row[] = "<button id='btn_oow_".$sp_list->id."' "
                    . "class = 'btn btn-sm btn-info' onclick='update_spare_estimate_cost(".$c .")' >Submit</button>";
            }
            

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
    function update_estimate_oow(){
        log_message("info", __METHOD__ );
        $this->form_validation->set_rules('estimate_cost', 'Estimate cost', 'trim|numeric|greater_than[1]|required');
        $this->form_validation->set_rules('booking_id', 'Booking ID', 'trim|required');
        $this->form_validation->set_rules('partner_id', 'Partner ID', 'trim|required');
        $this->form_validation->set_rules('sp_id', 'Spare ID', 'trim|required');
        $this->form_validation->set_rules('agent_id', 'Agent ID', 'trim|required');
        $this->form_validation->set_rules('assigned_vendor_id', 'Assigned ID', 'trim|required');
        $this->form_validation->set_rules('amount_due', 'Amount Due', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            log_message('info', __FUNCTION__ . '=> Form Validation is not updated by Partner '  .
                     " Data" . print_r($this->input->post(), true));
            echo "Error";
            
        } else {
            $booking_id = $this->input->post("booking_id");
            $vendor_id = $this->input->post("assigned_vendor_id");
            $amount_due = $this->input->post("amount_due");
            $id = $this->input->post("sp_id");
            $estimate_cost = $this->input->post("estimate_cost");
            $partner_id = $this->input->post("partner_id");
            $agent_id = $this->input->post("agent_id");
            $where = array('id' => $id);
            $data['status'] = SPARE_OOW_EST_GIVEN;
            $data['purchase_price'] = $estimate_cost;
            $data['sell_price'] = ($estimate_cost + $estimate_cost *SPARE_OOW_EST_MARGIN );
            $data['estimate_cost_given_date'] = date('Y-m-d');
            //Update Spare Parts Table
            $response = $this->service_centers_model->update_spare_parts($where, $data);
            if ($response) {
                //Update Unit Table
                $unit = $this->booking_model->get_unit_details(array('booking_id' => $booking_id));
                $unit[0]['price_tags'] = REPAIR_OOW_PARTS_PRICE_TAGS;
                $unit[0]['vendor_basic_percentage'] = REPAIR_OOW_VENDOR_PERCENTAGE;
                $unit[0]['customer_total'] = $data['sell_price'];
                $unit[0]['product_or_services'] = "Product";
                $unit[0]['tax_rate'] = DEFAULT_TAX_RATE;
                $unit[0]['create_date'] = date("Y-m-d H:i:s");
                $unit[0]['ud_update_date'] = date("Y-m-d H:i:s");
                $unit[0]['partner_net_payable'] = 0;
                $unit[0]['partner_paid_basic_charges'] = 0;
                $unit[0]['around_paid_basic_charges'] = 0;
                $unit[0]['around_net_payable'] = 0;
                unset($unit[0]['id']);
                //INSERT UNIT
                $result = $this->booking_model->_insert_data_in_booking_unit_details($unit[0], 1, 1);
                
                $booking['amount_due'] = ($amount_due + $data['sell_price']);
                // Update Booking Table
                $this->booking_model->update_booking($booking_id, $booking);
               
                $sc_data['unit_details_id'] = $result['unit_id'];
                $sc_data['booking_id'] = $booking_id;
                $sc_data['service_center_id'] = $vendor_id;
                $sc_data['current_status'] = "Pending";
                $sc_data['update_date'] = date('Y-m-d H:i:s');
                $sc_data['internal_status'] = SPARE_OOW_EST_GIVEN;
                //Update New item In SF Action Table 
                $this->vendor_model->insert_service_center_action($sc_data);
                
                //Update SF Action Table
                $this->vendor_model->update_service_center_action($booking_id, array("current_status" => 'Pending', 
                    'internal_status' =>SPARE_OOW_EST_GIVEN));
                 //Insert State Change
                $this->notify->insert_state_change($booking_id, SPARE_OOW_EST_GIVEN,"", "", $agent_id, "", $partner_id);
                $this->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);
                echo "Success";
            } else {
                echo "Error";
            }
        }

    }
   
}