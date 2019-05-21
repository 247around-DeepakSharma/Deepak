<?php

if (!defined('BASEPATH')){
    exit('No direct script access allowed');
}

error_reporting(E_ALL);
            ini_set('display_errors', 1);
            ini_set('memory_limit', -1);

class Engineer extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();
        $this->load->model('service_centers_model');

        $this->load->model('partner_model');
        $this->load->model('vendor_model');
        $this->load->library("pagination");
        $this->load->library('asynchronous_lib');
        $this->load->library('booking_utilities');
        $this->load->library("session");
        $this->load->library('s3');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');


        $this->load->library("miscelleneous");
        
    }
    
    function index(){
        echo "ACESS DENIED";
    }
    
    function review_engineer_action_form(){
      
        $where['where'] = array("engineer_booking_action.current_status" => "InProcess");
        $data = $this->engineer_model->get_engineer_action_table_list($where, "engineer_booking_action.booking_id, amount_due, engineer_table_sign.amount_paid, engineer_table_sign.remarks, engineer_table_sign.mismatch_pincode");
       
        foreach ($data as $key => $value) {
            $unitWhere = array("engineer_booking_action.booking_id" => $value->booking_id);
            $ac_data = $this->engineer_model->getengineer_action_data("engineer_booking_action.engineer_id, internal_status,", $unitWhere);
            $status = _247AROUND_CANCELLED;
            foreach ($ac_data as $ac_table) {
                if($ac_table['internal_status'] == _247AROUND_COMPLETED){
                    $status = _247AROUND_COMPLETED;
                }
            }
           
            $data[$key]->status = $status;
            if(!empty($ac_data[0]['engineer_id'])){
                $data[$key]->engineer_name = $this->engineer_model->get_engineers_details(array("id" => $ac_data[0]['engineer_id']), "name");
                
            } else {
                $data[$key]->engineer_name = "";
            }
        }
        //$this->load->view('service_centers/header');
        $this->load->view("service_centers/review_engineer_action", array("data" => $data));
      
    }
    
    function get_approve_booking_form($booking_id) {

        $data['booking_id'] = $booking_id;
        $data['booking_history'] = $this->booking_model->getbooking_history($booking_id);

        $bookng_unit_details = $this->booking_model->getunit_details($booking_id);

        foreach($bookng_unit_details as $key1 => $b){
            $broken = 0;
            foreach ($b['quantity'] as $key2 => $u) {

                $unitWhere = array("engineer_booking_action.booking_id" => $booking_id, 
                    "engineer_booking_action.unit_details_id" => $u['unit_id'], "service_center_id" => $data['booking_history'][0]['assigned_vendor_id']);
                $en = $this->engineer_model->getengineer_action_data("engineer_booking_action.*", $unitWhere);
                $bookng_unit_details[$key1]['quantity'][$key2]['en_serial_number'] = $en[0]['serial_number'];
                $bookng_unit_details[$key1]['quantity'][$key2]['en_serial_number_pic'] = $en[0]['serial_number_pic'];
                $bookng_unit_details[$key1]['quantity'][$key2]['en_is_broken'] = $en[0]['is_broken'];
                $bookng_unit_details[$key1]['quantity'][$key2]['en_internal_status'] = $en[0]['internal_status'];
                if($en[0]['is_broken'] == 1){
                    $broken = 1;
                }
            }
           $bookng_unit_details[$key1]['is_broken'] = $broken; 
                 
        }
        $sig_table = $this->engineer_model->getengineer_sign_table_data("*", array("booking_id" => $booking_id,
            "service_center_id" => $data['booking_history'][0]['assigned_vendor_id']));
        if (!empty($sig_table)) {
            $data['signature'] = $sig_table[0]['signature'];
            $data['mismatch_pincode'] = $sig_table[0]['mismatch_pincode'];
            $data['amount_paid'] = $sig_table[0]['amount_paid'];
        } else {
             $data['amount_paid'] = 0;
             $data['signature'] ="";
             $data['mismatch_pincode'] = 0;
        }
        $isPaytmTxn = $this->paytm_payment_lib->get_paytm_transaction_data($booking_id);
        if(!empty($isPaytmTxn)){
            if($isPaytmTxn['status']){
                $data['booking_history'][0]['onlinePaymentAmount'] = $isPaytmTxn['total_amount'];
            }
        }

        $data['bookng_unit_details'] = $bookng_unit_details;

        //$this->load->view('service_centers/header');
        $this->load->view("service_centers/approve_booking", $data);
    }
    
    function review_engineer_action_by_admin(){
        
        $where['where_in'] = array("engineer_booking_action.current_status" => array("InProcess", "Completed", "Cancelled"),
            "booking_details.current_status" => array(_247AROUND_PENDING, _247AROUND_RESCHEDULED));
        
        $data = $this->engineer_model->get_engineer_action_table_list($where, "engineer_booking_action.booking_id, amount_due, engineer_table_sign.amount_paid,"
                . "engineer_table_sign.pincode as en_pincode, engineer_table_sign.address as en_address, "
                . "booking_details.booking_pincode, booking_details.assigned_vendor_id, booking_details.booking_address, engineer_table_sign.remarks");
       
        foreach ($data as $key => $value) {
            $is_broken = false;
            $unitWhere = array("engineer_booking_action.booking_id" => $value->booking_id);
            $ac_data = $this->engineer_model->getengineer_action_data("engineer_booking_action.engineer_id, internal_status,engineer_booking_action.is_broken", $unitWhere);
            $status = _247AROUND_CANCELLED;
            foreach ($ac_data as $ac_table) {
                if($ac_table['internal_status'] == _247AROUND_COMPLETED){
                    $status = _247AROUND_COMPLETED;
                }
                if($ac_table['is_broken'] ==  1){
                    $is_broken = true;
                }
            }
           
            $data[$key]->status = $status;
            $data[$key]->is_broken = $is_broken;
            if(!empty($ac_data[0]['engineer_id'])){
                $data[$key]->engineer_name = $this->engineer_model->get_engineers_details(array("id" => $ac_data[0]['engineer_id']), "name");
                
                
            } else {
                $data[$key]->engineer_name = "";
            }
            
            $data[$key]->sf_name = $this->vendor_model->getVendorDetails("name", array("id" => $data[0]->assigned_vendor_id))[0]['name'];
        }
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/review_engineer_action', array("data" => $data));

    }
    
    function get_service_based_engineer(){
        $response = array();
        $service_id = $this->input->post("service_id");
        $service_center_id = $this->input->post("service_center_id");
        $where = array(
            "engineer_details.service_center_id" => $service_center_id,
            "engineer_appliance_mapping.service_id" => $service_id,
            "engineer_appliance_mapping.is_active" => 1,
            "engineer_details.active" => 1,
        );
        if($service_id && $service_center_id){
            $engineer = $this->engineer_model->get_service_based_engineer($where, "engineer_details.id, name");
            $html = "<option disabled selected>Select Engineer</option>";
            if(!empty($engineer)){
                foreach ($engineer as $key => $value) {
                    $html .= "<option value='".$value['id']."'";
                    if($this->input->post("engineer_id") == $value['id']){
                        $html .= "selected";
                    }
                    $html .= ">".$value['name']."</option>";
                }
                $response['status'] = true;
                $response['html'] = $html;
                echo json_encode($response);
            }
            else{
                $response['status'] = false;
                $response['html'] = "<a href='".base_url()."service_center/add_engineer' class='btn btn-info btn-sm' target='_blank'><i class='fa fa-user' aria-hidden='true'></i></a>";
                echo json_encode($response);
            }
        }
        else {
            $response['status'] = false;
            $response['html'] = "<a href='".base_url()."service_center/add_engineer' class='btn btn-info btn-sm' target='_blank'><i class='fa fa-user-plus' aria-hidden='true'></i></a>";
            echo json_encode($response);
        }
    }
    
    function get_engineer_details(){
        $data = $this->get_engineer_details_data();
        $post = $data['post'];
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->reusable_model->count_all_result("engineer_details", $post['where']),
            "recordsFiltered" =>  $this->reusable_model->count_all_filtered_result("engineer_details", "count(engineer_details.id) as numrows", $post),
            "data" => $data['data'],
        );
        echo json_encode($output); die();
    }
    
    function get_engineer_details_data(){
        $service_center_id = "";
        if($this->input->post("service_center_id")){
            $service_center_id = $this->input->post("service_center_id");
        }
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = array("engineer_details.id" => "ASC");
        $post['draw'] = $this->input->post('draw');
        $post['column_order'] = array();
        $post['column_search'] = array("engineer_details.name", "service_centres.name", "engineer_details.phone", "engineer_details.alternate_phone");
        $post['join'] = array(
            "service_centres" => "service_centres.id = engineer_details.service_center_id",
        );
        $post['joinType'] = array("service_centres" => "LEFT");
        $post['where'] = array('delete' => 0);
        if($service_center_id){
            $post['where']['service_center_id'] = $service_center_id;
        }
        
        $data = array();
        $no = $post['start'];
        
        $list =  $this->reusable_model->get_datatable_data("engineer_details", "engineer_details.id, engineer_details.name, engineer_details.phone, engineer_details.alternate_phone, engineer_details.active, engineer_details.identity_proof, service_centres.name as company_name", $post);
        //echo $this->db->last_query(); die();
        foreach ($list as $key => $value) {
           $service_id  = $this->engineer_model->get_engineer_appliance(array("engineer_id"=>$value->id, "is_active"=>1), "service_id");
           $appliances = array();
           if(!empty($service_id)){
                foreach ($service_id as  $values) {
                     $service_name = $this->booking_model->selectservicebyid($values['service_id']);
                     if(!empty($service_name)){
                        array_push($appliances, $service_name[0]['services']); 
                     }
                }
           }
           $value->appliance_name = implode(",", $appliances);
            
           $no++;
           $row = $this->get_engineer_details_table($value, $no);
           $data[] = $row;
        }
        
        return array(
            'data' => $data,
            'post' => $post
        );
    }
    
    function get_engineer_details_table($engineer_list, $no){
        $row = array();
        $row_action = "";
        $phone_call_button =  $engineer_list->phone;
        $alternet_phone_call_button =  $engineer_list->alternate_phone;
        $c2c = $this->booking_utilities->check_feature_enable_or_not(CALLING_FEATURE_IS_ENABLE);
        if($engineer_list->phone && !empty($c2c)) { 
            $phone_call_button .= '<button type="button" onclick="outbound_call('.$engineer_list->phone.')" class="btn btn-sm btn-info"><i class = "fa fa-phone fa-lg" aria-hidden = "true"></i></button>';    
        } 
        if($engineer_list->alternate_phone && !empty($c2c)) { 
            $alternet_phone_call_button .= '<button type="button" onclick="outbound_call('.$engineer_list->alternate_phone.')" class="btn btn-sm btn-info"><i class = "fa fa-phone fa-lg" aria-hidden = "true"></i></button>';    
        } 
        if($engineer_list->active==1){
            $row_action .= "<a id='edit' class='btn btn-small btn-primary' href=" . base_url() . "employee/vendor/change_engineer_activation/".$engineer_list->id."/0>Disable</a>";
        }
        else{
            $row_action .= "<a id='edit' class='btn btn-small btn-success' href=" . base_url() . "employee/vendor/change_engineer_activation/".$engineer_list->id."/1>Enable</a>";
        }
        
        $row[] = $no;
        if(!$this->input->post("service_center_id")){
            $row[] = $engineer_list->company_name;
        }
        $row[] = "<a href='".base_url()."employee/vendor/get_edit_engineer_form/".$engineer_list->id."'>".$engineer_list->name."</a>"; 
        $row[] = $engineer_list->appliance_name;
        $row[] = $phone_call_button;
        $row[] = $alternet_phone_call_button;
        $row[] = $engineer_list->identity_proof;
        $row[] = $row_action;
        $row[] = "<a id='edit' class='btn btn-small btn-primary' href=" . base_url() . "employee/vendor/get_edit_engineer_form/".$engineer_list->id.">Edit</a>";
        $row[] = "<a onClick=\"javascript: return confirm('Delete Engineer?');\" id='edit' class='btn btn-small btn-danger' href=" . base_url() . "employee/vendor/delete_engineer/".$engineer_list->id.">Delete</a>";
        return $row;
    }
}
