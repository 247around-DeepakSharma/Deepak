<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Spare_parts extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();

        $this->load->model('inventory_model');
        $this->load->model('service_centers_model');
        $this->load->model('invoices_model');
        
        
        $this->load->library('form_validation');
        $this->load->library('notify');
        $this->load->library('S3');

        $this->load->library('table');

    }
    /**
     * @desc This function is used to load view 
     * In this view, we will mark those booking whose pickup arranged by 247Around Team
     */
    function add_pickup_spare(){
        log_message('info', __METHOD__);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/update_spare_pickup');
    }
    /**
     * @desc used to update those booking whose pickup arranged by 247around
     */
    function process_update_pickup_for_booking(){
        log_message('info', __METHOD__. " ". json_encode($_POST, true));
        $vendor_partner = $this->input->post("vendor_partner");
        $in_booking_id = $this->input->post("booking_id");
        if(!empty($in_booking_id) && !empty($vendor_partner)){
           
            $booking_id = explode(",", $in_booking_id);
            $booking_id_array = array_filter($booking_id);
            if($vendor_partner == _247AROUND_PARTNER_STRING){
                
                $pickup_column = "around_pickup_from_partner";
                
            } else if($vendor_partner == _247AROUND_SF_STRING){
                
                $pickup_column = "around_pickup_from_service_center";
            }
            
            $data[$pickup_column] = 1;
            $where_in = array('booking_id' => $booking_id_array);
            
            $s = $this->inventory_model->update_bluk_spare_data($where_in, $data);
            if($s){
                echo "Success";
            } else {
                echo "Error";
            }
        } else {
            echo "Error";
        }
        
    }
    /**
     * @desc get list of booking whose pickup arranged by 247around
     */
    function get_pickup_marked_booking(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/get_pickup_marked_booking');
    }
    /**
     * @desc This function is used to load different spare tab data using datatable
     */
    function get_spare_parts_tab_details(){
        //log_message('info', __METHOD__ . print_r($_POST, true));
        
        $post = $this->get_spare_tab_datatable_data();  

        
        switch ($post['type']){
            case 0:
                $this->get_spare_requested_tab($post);
                break;
            case 1:
                $this->get_part_shipped_by_partner_tab($post);
                break;
             case 2:
                $this->get_part_delivered_to_sf($post);
                break;
            case 3:
                $this->get_part_rejected_by_partner($post);
                break;
            case 4:
                $this->get_defective_part_shipped_by_sf($post);
                break;
            case 5:
                $this->get_approved_defective_part_shipped_by_sf($post);
                break;
            case 6:
                $this->get_marked_pickup_booking($post);
                break;
            case 7:
                $this->get_edit_spare_pickup_by_247Around($post);
                break;
            case 8:
                $this->oow_parts_shipped_pending_approval($post);
                break;
            case 9:
                $this->part_requested_on_approval($post);
                break;
        }
    }
    
    /**
     * @desc used to create defective parts shipped by sf tab and whose courier price approved by Admin
     * @param Array $post
     */
    function get_approved_defective_part_shipped_by_sf($post){
        $post['select'] = "spare_parts_details.booking_id, users.name, booking_primary_contact_no, service_centres.name as sc_name,"
                . "partners.public_name as source, defective_part_shipped, courier_name_by_sf, awb_by_sf, courier_charges_by_sf, "
                . "remarks_defective_part_by_sf, defective_courier_receipt,sf_challan_file, defective_part_required, spare_parts_details.id,"
                . "booking_details.request_type, parts_shipped, remarks_defective_part_by_partner, defactive_part_received_date_by_courier_api";

        $post['column_order'] = array( NULL, NULL,NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,'age_defective_part_shipped_date', NULL);
        
        
        $post['column_search'] = array('spare_parts_details.booking_id','partners.public_name', 'service_centres.name', 'users.name', 'users.phone_number',
            'defective_part_shipped');
        $list = $this->inventory_model->get_spare_parts_query($post);
        $no = $post['start'];
        $data = array();
        foreach ($list as $spare_list) {
            $no++;
            $row =  $this->approved_defective_part_shipped_by_sf_table_data($spare_list, $no);
            $data[] = $row;
        }
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->inventory_model->count_spare_parts($post),
            "recordsFiltered" =>  $this->inventory_model->count_spare_filtered($post),
            "data" => $data,
        );
        
        echo json_encode($output);
    }
    /**
     * @desc used to create defective parts shipped by sf tab and whose courier price is not approved by Admin
     * @param Array $post
     */
    function get_defective_part_shipped_by_sf($post){
        $post['select'] = "spare_parts_details.booking_id, users.name, booking_primary_contact_no, service_centres.name as sc_name,"
                . "partners.public_name as source, defective_part_shipped, courier_name_by_sf, awb_by_sf, courier_charges_by_sf, "
                . "remarks_defective_part_by_sf, defective_courier_receipt,sf_challan_file, defective_part_required, spare_parts_details.id";

        $post['column_order'] = array();
        
        
        $post['column_search'] = array('spare_parts_details.booking_id','partners.public_name', 'service_centres.name', 'defective_part_shipped',
            'courier_name_by_sf', 'awb_by_sf', 'remarks_defective_part_by_sf', 'remarks_defective_part_by_partner');
        $list = $this->inventory_model->get_spare_parts_query($post);
        $no = $post['start'];
        $data = array();
        foreach ($list as $spare_list) {
            $no++;
            $row =  $this->defective_part_shipped_by_sf_table_data($spare_list, $no);
            $data[] = $row;
        }
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->inventory_model->count_spare_parts($post),
            "recordsFiltered" =>  $this->inventory_model->count_spare_filtered($post),
            "data" => $data,
        );
        
        echo json_encode($output);
    }
    /**
     * @desc Used to create tab in which we are showing
     * Defective parts shipped by sf but partner rejected 
     * @param Array $post
     */
    function get_part_rejected_by_partner($post){
        log_message('info', __METHOD__);
        
        $post['select'] = "spare_parts_details.booking_id, users.name, booking_primary_contact_no, service_centres.name as sc_name,"
                . "partners.public_name as source, parts_requested, booking_details.request_type, spare_parts_details.id,"
                . "defective_part_required, spare_parts_details.shipped_date, parts_shipped, "
                . "spare_parts_details.acknowledge_date, challan_approx_value, status, defective_part_shipped,"
                . "remarks_defective_part_by_sf, remarks_defective_part_by_partner, defective_courier_receipt";

        $post['column_order'] = array( NULL, NULL,NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'age_defective_part_shipped_date',NULL, NULL, NULL, NULL, NULL);
        
        
        $post['column_search'] = array('spare_parts_details.booking_id','partners.public_name', 'service_centres.name', 'parts_shipped', 
            'users.name', 'users.phone_number', 'defective_part_shipped', 'booking_details.request_type', 'remarks_defective_part_by_sf', 'remarks_defective_part_by_partner');
        $list = $this->inventory_model->get_spare_parts_query($post);
        $no = $post['start'];
        $data = array();
        foreach ($list as $spare_list) {
            $no++;
            $row =  $this->parts_rejected_by_partner_table_data($spare_list, $no);
            $data[] = $row;
        }
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->inventory_model->count_spare_parts($post),
            "recordsFiltered" =>  $this->inventory_model->count_spare_filtered($post),
            "data" => $data,
        );
        
        echo json_encode($output);
    }
    /**
     * @desc Used to create tab in which we are showing
     * Part delivered to sf 
     * @param Array $post
     */
    function get_part_delivered_to_sf($post){
        log_message('info', __METHOD__);
        
        $post['select'] = "spare_parts_details.booking_id, users.name, booking_primary_contact_no, service_centres.name as sc_name,"
                . "partners.public_name as source, parts_requested, booking_details.request_type, spare_parts_details.id,"
                . "defective_part_required, spare_parts_details.shipped_date, parts_shipped, "
                . "spare_parts_details.acknowledge_date, challan_approx_value, status";
        if($this->input->post("status") == SPARE_DELIVERED_TO_SF){
            $post['column_order'] = array( NULL, NULL,NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,NULL, NULL, 'age_of_delivered_to_sf',NULL);
        } else {
            $post['column_order'] = array( NULL, NULL,NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,NULL, NULL, 'age_part_pending_to_sf',NULL);
        }
        
        $post['column_search'] = array('spare_parts_details.booking_id','partners.public_name', 'service_centres.name', 'parts_shipped', 
            'users.name', 'users.phone_number', 'parts_requested', 'booking_details.request_type', 'spare_parts_details.shipped_date');
        $list = $this->inventory_model->get_spare_parts_query($post);
        $no = $post['start'];
        $data = array();
        foreach ($list as $spare_list) {
            $no++;
            $row =  $this->parts_delivered_to_sf_table_data($spare_list, $no);
            $data[] = $row;
        }
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->inventory_model->count_spare_parts($post),
            "recordsFiltered" =>  $this->inventory_model->count_spare_filtered($post),
            "data" => $data,
        );
        
        echo json_encode($output);
    }
    
    function oow_parts_shipped_pending_approval($post){
         $post['select'] = "spare_parts_details.booking_id,spare_parts_details.id, users.name, booking_primary_contact_no, service_centres.name as sc_name,"
                . "partners.public_name as source, parts_shipped, booking_details.request_type, spare_parts_details.id,"
                . "defective_part_required, partner_challan_file, parts_requested, incoming_invoice_pdf, sell_invoice_id, booking_details.partner_id as booking_partner_id, purchase_price";
        $post['column_order'] = array( NULL, NULL,NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'age_of_shipped_date',NULL, NULL, NULL, NULL, NULL);
        $post['column_search'] = array('spare_parts_details.booking_id','partners.public_name', 'service_centres.name', 'parts_shipped', 
            'users.name', 'users.phone_number', 'parts_requested', 'booking_details.request_type');
        $list = $this->inventory_model->get_spare_parts_query($post);

        $no = $post['start'];
        $data = array();
        foreach ($list as $spare_list) {
            $no++;
            $row =  $this->oow_parts_shipped_table_data($spare_list, $no);
            $data[] = $row;
        }
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->inventory_model->count_spare_parts($post),
            "recordsFiltered" =>  $this->inventory_model->count_spare_filtered($post),
            "data" => $data,
        );
        
        echo json_encode($output);

    }
    
    /**
     * @desc Used to create tab in which we are showing
     * Parts requested by Sf
     * @param Array $post
     */
    function part_requested_on_approval($post){
        log_message('info', __METHOD__);
        $post['select'] = "spare_parts_details.booking_id,spare_parts_details.part_warranty_status, users.name, booking_primary_contact_no, service_centres.name as sc_name,"
                . "partners.public_name as source, parts_requested, booking_details.request_type, spare_parts_details.id,spare_parts_details.part_requested_on_approval,"
                . "defective_part_required, status, symptom_spare_request.spare_request_symptom";
        $post['column_order'] = array( NULL, NULL,NULL, NULL, NULL, NULL, NULL, NULL, 'age_of_request',NULL, NULL);
        $post['column_search'] = array('spare_parts_details.booking_id','partners.public_name', 'service_centres.name', 
            'parts_requested', 'users.name', 'users.phone_number', 'booking_details.request_type');
        $list = $this->inventory_model->get_spare_parts_query($post);
               
        $no = $post['start'];
        $data = array();
        foreach ($list as $spare_list) {
            $no++;
            $row =  $this->part_requested_on_approval_table_data($spare_list, $no);
            $data[] = $row;
        }
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->inventory_model->count_spare_parts($post),
            "recordsFiltered" =>  $this->inventory_model->count_spare_filtered($post),
            "data" => $data,
        );
        
        echo json_encode($output);
        
    }
    
    
    
    /**
     * @desc Used to create tab in which we are showing
     * Parts requested by Sf
     * @param Array $post
     */
    function get_spare_requested_tab($post){
        log_message('info', __METHOD__);
        
        $post['select'] = "spare_parts_details.booking_id,spare_parts_details.part_warranty_status, users.name, booking_primary_contact_no, service_centres.name as sc_name,"
                . "partners.public_name as source, parts_requested, booking_details.request_type, spare_parts_details.id,"
                . "defective_part_required, status";
        $post['column_order'] = array( NULL, NULL,NULL, NULL, NULL, NULL, NULL, NULL, 'age_of_request',NULL, NULL);
        $post['column_search'] = array('spare_parts_details.booking_id','partners.public_name', 'service_centres.name', 
            'parts_requested', 'users.name', 'users.phone_number', 'booking_details.request_type');
        $list = $this->inventory_model->get_spare_parts_query($post);

        $no = $post['start'];
        $data = array();
        foreach ($list as $spare_list) {
            $no++;
            $row =  $this->spare_parts_requested_table_data($spare_list, $no);
            $data[] = $row;
        }
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->inventory_model->count_spare_parts($post),
            "recordsFiltered" =>  $this->inventory_model->count_spare_filtered($post),
            "data" => $data,
        );
        
        echo json_encode($output);
        
    }
    
    /**
     * @desc Used to create tab in which we are showing
     * Part Shipped by partner
     * @param Array $post
     */
    function get_part_shipped_by_partner_tab($post){
        log_message('info', __METHOD__);
        
        $post['select'] = "spare_parts_details.booking_id,spare_parts_details.spare_lost, users.name, booking_primary_contact_no, service_centres.name as sc_name,"
                . "partners.public_name as source, parts_shipped, booking_details.request_type, spare_parts_details.id,"
                . "defective_part_required, partner_challan_file, parts_requested";
        $post['column_order'] = array( NULL, NULL,NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'age_of_shipped_date',NULL, NULL);
        $post['column_search'] = array('spare_parts_details.booking_id','partners.public_name', 'service_centres.name', 'parts_shipped', 
            'users.name', 'users.phone_number', 'parts_requested', 'booking_details.request_type');
        $list = $this->inventory_model->get_spare_parts_query($post);

        $no = $post['start'];
        $data = array();
        foreach ($list as $spare_list) {
            $no++;
            $row =  $this->part_shipped_by_partner_table_data($spare_list, $no);
            $data[] = $row;
        }
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->inventory_model->count_spare_parts($post),
            "recordsFiltered" =>  $this->inventory_model->count_spare_filtered($post),
            "data" => $data,
        );
        
        echo json_encode($output);
    }
    
     /**
     * @desc This function is used to get data for edit spare pickup by @$&around
     * @param Array $post
     */
    function get_edit_spare_pickup_by_247Around($post){
        log_message('info', __METHOD__. print_r($post, TRUE));
        
        $post['column_order'] = array('spare_parts_details.id'=>NULL);
       
        if($post['vendor_partner'] == _247AROUND_PARTNER_STRING){
            $post['column_search'] = array('spare_parts_details.booking_id', 'awb_by_partner');
            $post['where'] = array('around_pickup_from_partner'=>1);
            $post['select'] = "spare_parts_details.id, spare_parts_details.booking_id, awb_by_partner as awb_no, courier_price_by_partner as courier_charges, courier_name_by_partner as courier_name";
        }
        else if($post['vendor_partner'] == _247AROUND_SF_STRING){
            $post['column_search'] = array('spare_parts_details.booking_id', 'awb_by_sf');
            $post['where'] = array('around_pickup_from_service_center'=>1);
            $post['select'] = "spare_parts_details.id, spare_parts_details.booking_id, awb_by_sf as awb_no, courier_charges_by_sf as courier_charges, courier_name_by_sf as courier_name";
        }
        $list = $this->inventory_model->get_spare_parts_query($post);
        

        $no = $post['start'];
        $data = array();
        foreach ($list as $spare_list) {
            $no++;
            $row =  $this->edit_spare_pickup_by_247Around_table_data($spare_list, $no);
            $data[] = $row;
        }
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->inventory_model->count_spare_parts($post),
            "recordsFiltered" =>  $this->inventory_model->count_spare_filtered($post),
            "data" => $data,
        );
        
        echo json_encode($output);
    }
    
    /**
     * @desc this function is used to create table row data for the spare pickup by 247Around
     * @param Array $spare_list
     * @param int $no
     * @return Array
     */
    function edit_spare_pickup_by_247Around_table_data($spare_list, $no){
        $row = array();
       
        $row[] = $no;
        $row[] = $spare_list->booking_id;
        $row[] = $spare_list->awb_no;
        $row[] = $spare_list->courier_charges;
        $row[] = $spare_list->courier_name;
        $row[] = '<button type="button" class="btn btn-danger btn-xs" onclick="update_spare_part_detail('.trim($spare_list->id).', this)">Remove</button>';
       
        return $row;
    }
    
    /**
     * @desc this function is used to create table row data for the approved courier charges by admin tab
     * @param Array $spare_list
     * @param int $no
     * @return Array
     */
    function approved_defective_part_shipped_by_sf_table_data($spare_list, $no){
         $row = array();
       
        $row[] = $no;
        $row[] = '<a href="'. base_url().'employee/booking/viewdetails/'.$spare_list->booking_id.'" target= "_blank" >'.$spare_list->booking_id.'</a>';
        $row[] = $spare_list->name;
        $row[] = $spare_list->booking_primary_contact_no;
        $row[] = $spare_list->sc_name;
        $row[] = $spare_list->source;

        $row[] = $spare_list->parts_shipped;
        $row[] = $spare_list->defective_part_shipped;
        $row[] = $spare_list->request_type;
        $row[] = $spare_list->remarks_defective_part_by_partner;
        $row[] = !empty($spare_list->defactive_part_received_date_by_courier_api)?"Delivered":"In-Transit";
        $row[] = $spare_list->age_defective_part_shipped_date. " Days";
        
        if($this->session->userdata('user_group') == "inventory_manager" || $this->session->userdata('user_group') == "admin"){
            
            if($spare_list->defective_part_required == '0'){ $required_parts =  'REQUIRED_PARTS'; $text = "Required"; $cl ="btn-primary";} else{ $text = "Not Required"; $required_parts =  'NOT_REQUIRED_PARTS_FOR_COMPLETED_BOOKING'; $cl = "btn-danger"; }
            $row[] = '<button type="button" data-booking_id="'.$spare_list->booking_id.'" data-url="'.base_url().'employee/inventory/update_action_on_spare_parts/'.$spare_list->id.'/'.$spare_list->booking_id.'/'.$required_parts.'" class="btn btn-sm '.$cl.' open-adminremarks" data-toggle="modal" data-target="#myModal2">'.$text.'</button>';
            
        } else {
            $row[] = "";
            
        }
        
        return $row;
    }
    /**
     * @desc this function is used to create table row data for the defective part shipped by sf tab
     * @param Array $spare_list
     * @param int $no
     * @return Array
     */
    function defective_part_shipped_by_sf_table_data($spare_list, $no){
        $row = array();
       
        $row[] = $no;
        $row[] = '<a href="'. base_url().'employee/booking/viewdetails/'.$spare_list->booking_id.'" target= "_blank" >'.$spare_list->booking_id.'</a>';
        $row[] = $spare_list->sc_name;
        $row[] = $spare_list->source;
        $row[] = $spare_list->defective_part_shipped;
        $row[] = $spare_list->courier_name_by_sf;
        $row[] = $spare_list->awb_by_sf;
 

        $row[] = "<i class='fa fa-inr'></i>".$spare_list->courier_charges_by_sf;
        $row[] = $spare_list->remarks_defective_part_by_sf;
        if(!empty($spare_list->defective_courier_receipt)){
            $row[] =  '<a href="'.S3_WEBSITE_URL.'misc-images/'.$spare_list->defective_courier_receipt.' " target="_blank">Click Here</a>';
        } else {
            $row[] = "";
        }
        
        if(!empty($spare_list->sf_challan_file)){
            $row[] =  '<a href="'.S3_WEBSITE_URL.'vendor-partner-docs/'.$spare_list->sf_challan_file.' " target="_blank">Click Here</a>';
        } else {
            $row[] = "";
        }

        if($this->session->userdata('user_group') == "inventory_manager" || $this->session->userdata('user_group') == "admin"){
            
            if($spare_list->defective_part_required == '0'){ $required_parts =  'REQUIRED_PARTS'; $text = "Required"; $cl ="btn-primary";} else{ $text = "Not Required"; $required_parts =  'NOT_REQUIRED_PARTS_FOR_COMPLETED_BOOKING'; $cl = "btn-danger"; }
            $row[] = '<button type="button" data-booking_id="'.$spare_list->booking_id.'" data-url="'.base_url().'employee/inventory/update_action_on_spare_parts/'.$spare_list->id.'/'.$spare_list->booking_id.'/'.$required_parts.'" class="btn btn-sm '.$cl.' open-adminremarks" data-toggle="modal" data-target="#myModal2">'.$text.'</button>';
            $row[] = '<button type="button" data-booking_id="'.$spare_list->booking_id.'"   data-url="'.base_url().'employee/inventory/update_action_on_spare_parts/'.$spare_list->id.'/'.$spare_list->booking_id.'/REJECT_COURIER_INVOICE" class="btn btn-warning btn-sm open-adminremarks" data-toggle="modal" data-target="#myModal2">Reject Invoice</button>';
            $row[] = '<button type="button" data-charge="'.$spare_list->courier_charges_by_sf.'" data-booking_id="'.$spare_list->booking_id.'"   data-url="'.base_url().'employee/inventory/update_action_on_spare_parts/'.$spare_list->id.'/'.$spare_list->booking_id.'/APPROVE_COURIER_INVOICE" class="btn btn-primary btn-sm open-adminremarks" data-toggle="modal" data-target="#myModal2">Approve Invoice</button>';
        } else {
            $row[] = "";
            $row[] = "";
            $row[] = "";
        }
        
        return $row;
    }
    /**
     * @desc this function is used to create table row data for the spare parts rejected by partner tab
     * @param Array $spare_list
     * @param int $no
     * @return Array
     */
    function parts_rejected_by_partner_table_data($spare_list, $no){
        $row = array();
        $row[] = $no;
        $row[] = '<a href="'. base_url().'employee/booking/viewdetails/'.$spare_list->booking_id.'" target= "_blank" >'.$spare_list->booking_id.'</a>';
        $row[] = $spare_list->name;
        $row[] = $spare_list->booking_primary_contact_no;
        $row[] = $spare_list->sc_name;
        $row[] = $spare_list->source;

        $row[] = $spare_list->parts_shipped;
        $row[] = $spare_list->defective_part_shipped;
        $row[] = $spare_list->request_type;
        $row[] = (empty($spare_list->age_defective_part_shipped_date))?'0 Days':$spare_list->age_defective_part_shipped_date." Days";
        $row[] = $spare_list->remarks_defective_part_by_sf;
        $row[] = $spare_list->remarks_defective_part_by_partner;
        $row[] = '<a href="'.S3_WEBSITE_URL.'misc-images/'.$spare_list->defective_courier_receipt.'" target="_blank">Click Here</a>';

        if($this->session->userdata('user_group') == "inventory_manager" || $this->session->userdata('user_group') == "admin"){
            $row[] = '<button type="button" data-booking_id="'.$spare_list->booking_id.'" data-url="'.base_url().'employee/inventory/update_action_on_spare_parts/'.$spare_list->id.'/'.$spare_list->booking_id.'/DEFECTIVE_PARTS_SHIPPED_BY_SF" class="btn btn-warning btn-sm open-adminremarks" data-toggle="modal" data-target="#myModal2">Parts Shipped</button>';
            if($spare_list->defective_part_required == '0'){ $required_parts =  'REQUIRED_PARTS'; $text = "Required"; $cl ="btn-primary";} else{ $text = "Not Required"; $required_parts =  'NOT_REQUIRED_PARTS_FOR_COMPLETED_BOOKING'; $cl = "btn-danger"; }
            $row[] = '<button type="button" data-booking_id="'.$spare_list->booking_id.'" data-url="'.base_url().'employee/inventory/update_action_on_spare_parts/'.$spare_list->id.'/'.$spare_list->booking_id.'/'.$required_parts.'" class="btn btn-sm '.$cl.' open-adminremarks" data-toggle="modal" data-target="#myModal2">'.$text.'</button>';
        } else {
            $row[] = "";
            $row[] = "";
        }
        
        return $row;
    }
    /**
     * @desc this function is used to create table row data for the spare parts delivered to sf tab
     * @param Array $spare_list
     * @param int $no
     * @return Array
     */
    function parts_delivered_to_sf_table_data($spare_list, $no){
        $row = array();
        $row[] = $no;
        $row[] = '<a href="'. base_url().'employee/booking/viewdetails/'.$spare_list->booking_id.'" target= "_blank" >'.$spare_list->booking_id.'</a>';
        $row[] = $spare_list->name;
        $row[] = $spare_list->booking_primary_contact_no;
        $row[] = $spare_list->sc_name;
        $row[] = $spare_list->source;
        $row[] = $spare_list->parts_requested;
        $row[] = $spare_list->parts_shipped;
        $row[] = $spare_list->request_type;
        $row[] = date('d-m-Y', strtotime($spare_list->shipped_date));
        $row[] = date('d-m-Y', strtotime($spare_list->acknowledge_date));
        $row[] = "<i class='fa fa-inr'></i> ".$spare_list->challan_approx_value;
        if($this->input->post("status") == SPARE_DELIVERED_TO_SF){
            $row[] = (empty($spare_list->age_of_delivered_to_sf))?'0 Days':$spare_list->age_of_delivered_to_sf." Days";;
        } else {
            $row[] = (empty($spare_list->age_part_pending_to_sf))?'0 Days':$spare_list->age_part_pending_to_sf." Days";
        }
        
        if($this->session->userdata('user_group') == "inventory_manager" || $this->session->userdata('user_group') == "admin"){
            
            if($spare_list->defective_part_required == '0'){ $required_parts =  'REQUIRED_PARTS'; $text = "Required"; $cl ="btn-primary";} else{ $text = "Not Required"; $required_parts =  'NOT_REQUIRED_PARTS_FOR_COMPLETED_BOOKING'; $cl = "btn-danger"; }
            $row[] = '<button type="button" data-booking_id="'.$spare_list->booking_id.'" data-url="'.base_url().'employee/inventory/update_action_on_spare_parts/'.$spare_list->id.'/'.$spare_list->booking_id.'/'.$required_parts.'" class="btn btn-sm '.$cl.' open-adminremarks" data-toggle="modal" data-target="#myModal2">'.$text.'</button>';
        } else {
            
            $row[] = "";
        }
        
        return $row;
    }
    /**
     * @desc this function is used to create table row data for the part shipped by partner tab
     * @param Array $spare_list
     * @param int $no
     * @return Array
     */
    function part_shipped_by_partner_table_data($spare_list, $no){
        
        $row = array();
        $row[] = $no;
        $row[] = '<a href="'. base_url().'employee/booking/viewdetails/'.$spare_list->booking_id.'" target= "_blank" >'.$spare_list->booking_id.'</a>';
        $row[] = $spare_list->name;
        $row[] = $spare_list->booking_primary_contact_no;
        $row[] = $spare_list->sc_name;
        $row[] = $spare_list->source;
        $row[] = $spare_list->parts_requested;
        $row[] = $spare_list->parts_shipped;
        $row[] = $spare_list->request_type;
        $row[] = (empty($spare_list->age_of_shipped_date))?'0 Days':$spare_list->age_of_shipped_date." Days";
        if(!empty($spare_list->partner_challan_file)){
            $row[] = '<a href="'.S3_WEBSITE_URL.'vendor-partner-docs/'.$spare_list->partner_challan_file.' " target="_blank">Click Here to view</a>';
        } else {
            $row[] = "";
        }
        
        if($this->session->userdata('user_group') == "inventory_manager" || $this->session->userdata('user_group') == "admin"){
            
            if($spare_list->defective_part_required == '0'){ $required_parts =  'REQUIRED_PARTS'; $text = "Required"; $cl ="btn-primary";} else{ $text = "Not Required"; $required_parts =  'NOT_REQUIRED_PARTS'; $cl = "btn-danger"; }
            $row[] = '<button type="button" data-booking_id="'.$spare_list->booking_id.'" data-url="'.base_url().'employee/inventory/update_action_on_spare_parts/'.$spare_list->id.'/'.$spare_list->booking_id.'/'.$required_parts.'" class="btn btn-sm '.$cl.' open-adminremarks" data-toggle="modal" data-target="#myModal2">'.$text.'</button>';
        } else {
            
            $row[] = "";
        }
        
        if($this->session->userdata('user_group') == "inventory_manager" || $this->session->userdata('user_group') == "admin"){
            
            if($spare_list->spare_lost == '0'){ $required_parts =  'REQUIRED_PARTS'; $text = "Request New Spare Part"; $cl ="btn-info"; $btn_status = "";} else{ $text = "Spare Part Not Required"; $btn_status = "disabled"; $required_parts =  'NOT_REQUIRED_PARTS'; $cl = "btn-success"; }
            $row[] = '<button type="button" onclick=courier_lost_required("'.$spare_list->id.'","'.$spare_list->booking_id.'") '.$btn_status.' class="btn btn-sm '.$cl.'">'.$text.'</button>';
        } else {
            
            $row[] = "";
        }
        
        return $row;
    }
    
    function oow_parts_shipped_table_data($spare_list, $no){
        
        $row = array();
        $row[] = $no;
        $row[] = '<a href="'. base_url().'employee/booking/viewdetails/'.$spare_list->booking_id.'" target= "_blank" >'.$spare_list->booking_id.'</a>';
        $row[] = $spare_list->name;
        $row[] = $spare_list->booking_primary_contact_no;
        $row[] = $spare_list->sc_name;
        $row[] = $spare_list->source;
        $row[] = $spare_list->parts_requested;
        $row[] = $spare_list->parts_shipped;
        $row[] = $spare_list->request_type;
        $row[] = $spare_list->purchase_price;
        $row[] = (empty($spare_list->age_of_shipped_date))?'0 Days':$spare_list->age_of_shipped_date." Days";
        if(!empty($spare_list->partner_challan_file)){
            $row[] = '<a href="'.S3_WEBSITE_URL.'vendor-partner-docs/'.$spare_list->partner_challan_file.' " target="_blank">Click Here to view</a>';
        } else {
            $row[] = "";
        }
        
        if($this->session->userdata('user_group') == "inventory_manager" || $this->session->userdata('user_group') == "admin"){
            
            if($spare_list->defective_part_required == '0'){ $required_parts =  'REQUIRED_PARTS'; $text = "Required"; $cl ="btn-primary";} else{ $text = "Not Required"; $required_parts =  'NOT_REQUIRED_PARTS'; $cl = "btn-danger"; }
            $row[] = '<button type="button" data-booking_id="'.$spare_list->booking_id.'" data-url="'.base_url().'employee/inventory/update_action_on_spare_parts/'.$spare_list->id.'/'.$spare_list->booking_id.'/'.$required_parts.'" class="btn btn-sm '.$cl.' open-adminremarks" data-toggle="modal" data-target="#myModal2">'.$text.'</button>';
        } else {
            
            $row[] = "";
        }
        
        if(!empty($spare_list->sell_invoice_id)){
            $row[] = $spare_list->sell_invoice_id;
        } else {
            
            $row[] = '<a href="'.base_url().'employee/invoice/generate_oow_parts_invoice/'.$spare_list->id.'"  class="btn btn-md btn-success">Generate Sale Invoice</a>';
        }
        
        
        if(!empty($spare_list->incoming_invoice_pdf)){
            $row[] = '<a target="_blank" href="https://s3.amazonaws.com/'.BITBUCKET_DIRECTORY.'/invoices-excel/'.$spare_list->incoming_invoice_pdf.'">
                            <img style="width:27px;" src="'.base_url().'images/invoice_icon.png"; /></a>';
        } else {
            $row[] = "";
        }
        $row[] = '<input id="'.$spare_list->id.'" type="checkbox" class="form-control spare_id" name="spare_id[]" data-booking_id="'.$spare_list->booking_id.'" data-partner_id = "'.$spare_list->booking_partner_id.'" value="'.$spare_list->id.'" />';
        return $row;
        
    }
    /**
     * @desc this function is used to create table row data for the spare parts requested tab
     * @param Array $spare_list
     * @param int $no
     * @return Array
     */
    function spare_parts_requested_table_data($spare_list, $no){
                
        $row = array();
        $row[] = $no;
        $row[] = '<a href="'. base_url().'employee/booking/viewdetails/'.$spare_list->booking_id.'" target= "_blank" >'.$spare_list->booking_id.'</a>';
        $row[] = $spare_list->name;
        $row[] = $spare_list->booking_primary_contact_no;
        $row[] = $spare_list->sc_name;
        $row[] = $spare_list->source;
        $row[] = $spare_list->parts_requested;
        $row[] = $spare_list->request_type;
        $row[] = (empty($spare_list->age_of_request))?'0 Days':$spare_list->age_of_request." Days";
        $c_tag = ($spare_list->part_warranty_status == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS && $spare_list->status != SPARE_PARTS_REQUESTED)? "QUOTE_REQUEST_REJECTED":"CANCEL_PARTS";
        $row[] = '<button type="button" data-booking_id="'.$spare_list->booking_id.'" data-url="'.base_url().'employee/inventory/update_action_on_spare_parts/'.$spare_list->id.'/'.$spare_list->booking_id.'/'.$c_tag.'" class="btn btn-primary btn-sm open-adminremarks" data-toggle="modal" data-target="#myModal2">Cancel</button>';
        if($spare_list->defective_part_required == '0'){ $required_parts =  'REQUIRED_PARTS'; $text = "Required"; $cl ="btn-primary";} else{ $text = "Not Required"; $required_parts =  'NOT_REQUIRED_PARTS'; $cl = "btn-danger"; }
        $row[] = '<button type="button" data-booking_id="'.$spare_list->booking_id.'" data-url="'.base_url().'employee/inventory/update_action_on_spare_parts/'.$spare_list->id.'/'.$spare_list->booking_id.'/'.$required_parts.'" class="btn btn-sm '.$cl.' open-adminremarks" data-toggle="modal" data-target="#myModal2">'.$text.'</button>';
        
        
        return $row;
    }
    
    /**
     * @desc this function is used to create table row data for the spare parts requested tab
     * @param Array $spare_list
     * @param int $no
     * @return Array
     */
    function part_requested_on_approval_table_data($spare_list, $no){
        
        $row = array();
        $row[] = $no;
        $row[] = '<a href="'. base_url().'employee/booking/viewdetails/'.$spare_list->booking_id.'" target= "_blank" >'.$spare_list->booking_id.'</a>';
        $row[] = $spare_list->name;
        $row[] = $spare_list->booking_primary_contact_no;
        $row[] = $spare_list->sc_name;
        $row[] = $spare_list->source;
        $row[] = $spare_list->parts_requested;
        $row[] = $spare_list->request_type;
        $row[] = (empty($spare_list->age_of_request))?'0 Days':$spare_list->age_of_request." Days";
        $row[] = $spare_list->spare_request_symptom;
        $c_tag = ($spare_list->part_warranty_status == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS && $spare_list->status != SPARE_PARTS_REQUESTED)? "QUOTE_REQUEST_REJECTED":"CANCEL_PARTS";
        $row[] = '<button type="button" data-booking_id="'.$spare_list->booking_id.'" data-url="'.base_url().'employee/inventory/update_action_on_spare_parts/'.$spare_list->id.'/'.$spare_list->booking_id.'/'.$c_tag.'" class="btn btn-primary btn-sm open-adminremarks" data-toggle="modal" data-target="#myModal2">Cancel</button>';
        if($spare_list->defective_part_required == '0'){ $required_parts =  'REQUIRED_PARTS'; $text = "Required"; $cl ="btn-primary";} else{ $text = "Not Required"; $required_parts =  'NOT_REQUIRED_PARTS'; $cl = "btn-danger"; }
        $row[] = '<button type="button" data-booking_id="'.$spare_list->booking_id.'" data-url="'.base_url().'employee/inventory/update_action_on_spare_parts/'.$spare_list->id.'/'.$spare_list->booking_id.'/'.$required_parts.'" class="btn btn-sm '.$cl.' open-adminremarks" data-toggle="modal" data-target="#myModal2">'.$text.'</button>';
        
        if($spare_list->part_requested_on_approval == '0'){ $appvl_text = 'Approve'; }else{ $appvl_text = 'Approved'; }
        $row[] = '<button type="button" data-keys="'.$spare_list->part_warranty_status.'" data-booking_id="'.$spare_list->booking_id.'" data-url="'.base_url().'employee/spare_parts/spare_part_on_approval/'.$spare_list->id.'/'.$spare_list->booking_id.'" class="btn btn-info open-adminremarks" data-toggle="modal" id="approval_'.$no.'" data-target="#myModal2">'.$appvl_text.'</button>';
        
      //$row[] = 'blank Text';
        
        
        return $row;
    }
    
    /**
     * @desc This function is used to get post data from datatable
     * @return Array
     */
    function get_spare_tab_datatable_data(){
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search']['value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
        $post['type'] = $this->input->post('type');
        $post['where']['status'] = $this->input->post("status");
        
        if (!empty($this->input->post('part_requested_approval_flag'))) {
            $post['where']['part_requested_on_approval'] = '0';
        }

        if(!empty($this->input->post('vendor_partner'))){
            $post['vendor_partner'] = $this->input->post('vendor_partner');
        }else{
           $sf = $this->vendor_model->get_employee_relation($this->session->userdata("id")); 
        }
        $vendor_id = array();
        if(!empty($sf)){
            $vendor_id = explode(",", $sf[0]["service_centres_id"]);
            $post['where_in'] = array('service_center_id' => $vendor_id);
        }
        if(!empty($this->input->post('partner_id'))){
            $post['where']['booking_details.partner_id'] = $this->input->post('partner_id');
        }
        
        if($post['where']['status'] == DEFECTIVE_PARTS_SHIPPED){
            $post['where']['approved_defective_parts_by_admin'] = $this->input->post('approved_defective_parts_by_admin');
        }
        
        return $post;
    }
    
     /**
     * @desc This function is used to load view 
     * In this view, we will update those booking whose pickup arranged by 247Around Team
     */
    function edit_spare_pickup(){
        log_message('info', __METHOD__);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/edit_spare_pickup');
    }
    
    function process_edit_spare_pickup(){
       $id = $this->input->post('id');
       $data = array($this->input->post('column')=>0);
       $row = $this->inventory_model->update_spare_courier_details(trim($id), $data); 
       echo $row;
    }
    
    
     
     /**
     * @desc: This function is used to Move Requested Spare to Partner/Vendor page.
     * @params: void
     * @return: string
     */
    function move_request_spare_to_partner_vendor() {
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/move_request_spare_to_partner_vendor');
    }

    /**
     * @desc: This function is used to get spare parts details by id.
     * @params: void
     * @return: string
     */
    function get_spare_parts_details_search_by_booking_id() {
        $booking_id = $this->input->post('booking_id');
        $spare_parts = $this->partner_model->get_spare_parts_by_any("booking_details.partner_id as booking_partner_id,"
                . "spare_parts_details.id, spare_parts_details.status,"
                . "spare_parts_details.parts_requested, entity_type, "
                . "spare_parts_details.partner_id, spare_parts_details.requested_inventory_id,"
                . "booking_details.booking_id", array('spare_parts_details.booking_id' => $booking_id), true, false);
        $data = array('spare_parts_list' => $spare_parts);
        $this->load->view('employee/search_spare_parts_details', $data);
    }

    /**
     * @desc: This function is used to Update entity_type and booking_id of spare parts details by id.
     * @params: void
     * @return: string
     */
    function move_to_update_spare_parts_details() {
        log_message('info', __METHOD__ . " " . json_encode($_POST, true));
        $spare_parts_id = $this->input->post('spare_parts_id');
        $partner_id = $this->input->post('booking_partner_id');
        $entity_type = $this->input->post('entity_type');
        $booking_id = $this->input->post('booking_id');
        $where = array('id' => $spare_parts_id);
        $data['entity_type'] = $entity_type;
        $data['partner_id'] = $partner_id;
        $data['defective_return_to_entity_type'] = _247AROUND_PARTNER_STRING;
        $data['defective_return_to_entity_id'] = $partner_id;
        $data['is_micro_wh'] = 0;
        
        $row = $this->service_centers_model->update_spare_parts($where, $data);
        if ($entity_type == _247AROUND_PARTNER_STRING) {
            $new_state = REQUESTED_SPARED_REMAP;
        } 
        
        if (!empty($row)) {
            $this->notify->insert_state_change($booking_id, $new_state, '', PARTNER_WILL_SEND_NEW_PARTS, $this->session->userdata('id'), $this->session->userdata('employee_id'), '', '', _247AROUND);
            echo 'success';
        }
    }

    /**
     * @desc: This function is used to copy booking id by spare parts id.
     * @params: void
     * @return: string
     */
    
    function copy_booking_details_by_spare_parts_id() {        
        log_message('info', __METHOD__ . " " . json_encode($_POST, true));
        $spare_parts_id = $this->input->post('spare_parts_id');
        $new_booking_id = $this->input->post('new_booking_id');
        $status = $this->input->post('status');

        $select = 'spare_parts_details.entity_type,spare_parts_details.booking_id,spare_parts_details.status,spare_parts_details.partner_id,'
                . 'spare_parts_details.defective_return_to_entity_type,spare_parts_details.defective_return_to_entity_id, spare_parts_details.service_center_id, spare_parts_details.model_number, spare_parts_details.serial_number,'
                . ' spare_parts_details.date_of_purchase, spare_parts_details.invoice_gst_rate, spare_parts_details.parts_requested, spare_parts_details.parts_requested_type, spare_parts_details.invoice_pic,'
                . ' spare_parts_details.defective_parts_pic, spare_parts_details.defective_back_parts_pic, spare_parts_details.serial_number_pic, spare_parts_details.requested_inventory_id, spare_parts_details.is_micro_wh,'
                . 'spare_parts_details.part_warranty_status,booking_details.partner_id as booking_partner_id';

        if (!empty($spare_parts_id)) {

            $spare_parts_list = $this->partner_model->get_spare_parts_by_any($select, array('spare_parts_details.id' => $spare_parts_id), true, false);
            
            if (!empty($spare_parts_list)) {

                $spare_parts_list[0]['date_of_request'] = date('Y-m-d');
                $spare_parts_list[0]['booking_id'] = $new_booking_id;
                $spare_parts_list[0]['status'] = $status;

                $partner_id = $spare_parts_list[0]['partner_id'];
                $entity_type = $spare_parts_list[0]['entity_type'];
                $inventory_id = $spare_parts_list[0]['requested_inventory_id'];
                $booking_partner_id = $spare_parts_list[0]['booking_partner_id'];

                if (!empty($inventory_id)) {
                    $select = "(stock - pending_request_count) as actual_stock";
                    $where = array('entity_id' => $partner_id, 'entity_type' => $entity_type, 'inventory_id' => $inventory_id);
                    $inventory_stock = $this->inventory_model->get_inventory_stock_count_details($select, $where);

                    if (!empty($inventory_stock)) {
                        $this->inventory_model->update_pending_inventory_stock_request($entity_type, $partner_id, $inventory_id, 1);
                    } else {
                        $spare_parts_list[0]['partner_id'] = $booking_partner_id;
                        $spare_parts_list[0]['entity_type'] = _247AROUND_PARTNER_STRING;
                        $spare_parts_list[0]['is_micro_wh'] = 0;
                        $spare_parts_list[0]['defective_return_to_entity_type'] = _247AROUND_PARTNER_STRING;
                        $spare_parts_list[0]['defective_return_to_entity_id'] = $booking_partner_id;
                    }
                } else {
                    $spare_parts_list[0]['partner_id'] = $booking_partner_id;
                    $spare_parts_list[0]['entity_type'] = _247AROUND_PARTNER_STRING;
                    $spare_parts_list[0]['is_micro_wh'] = 0;
                    $spare_parts_list[0]['defective_return_to_entity_type'] = _247AROUND_PARTNER_STRING;
                    $spare_parts_list[0]['defective_return_to_entity_id'] = $booking_partner_id;
                }

                $insert_id = $this->service_centers_model->insert_data_into_spare_parts($spare_parts_list[0]);

                if (!empty($insert_id)) {
                    echo 'success';
                } else {
                    echo 'fail';
                }
            }
        }
    }

    /**
     * @desc: This function is used to brackets data table.
     * @params: void
     * @return: string
     */
    
    
     function get_brackets_tab_datatable_data() {
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search']['value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
        $post['type'] = $this->input->post('type');
        $post['where']['is_shipped'] = $this->input->post("is_shipped");
        $post['where']['is_received'] = $this->input->post("is_received");
        if (!empty($this->input->post("sf_id")) && !empty($this->input->post("sf_role"))) {
            $sf_role = $this->input->post("sf_role");
            $post['where'][$sf_role] = $this->input->post("sf_id");
        }        
        if ($this->input->post("daterange")) {
            $daterange = explode("-", $this->input->post("daterange"));
            $post['where']['order_date >= "' . date('Y-m-d 00:00:00', strtotime($daterange[0])) . '" '] = NULL;
            $post['where']['order_date <= "' . date('Y-m-d 23:59:59', strtotime($daterange[1])) . '" '] = NULL;
        }
        $id = $this->session->userdata('id');
        //Getting employee relation if present
        if ($this->session->userdata('user_group') == 'regionalmanager') {
            $sf_list_array = $this->vendor_model->get_employee_relation($id);
            if (!empty($sf_list_array)) {
                $sf_list = $sf_list_array[0]['service_centres_id'];
                $post['where_in'] = array('order_received_from' => $sf_list);
            }
        }


        return $post;
    }

    function get_brackets_tab_details() {
        log_message('info', __METHOD__ . print_r($_POST, true));
        $post = $this->get_brackets_tab_datatable_data();
        switch ($post['type']) {
            case 0:
                $this->brackets_list_tabs($post);
                break;
        }
    }

    /**
     * @desc Used to create tab in which we are showing brackets
     * Parts requested by Sf
     * @param Array $post
     */
    
    function brackets_list_tabs($post) {
        log_message('info', __METHOD__);
        $post['select'] = "brackets.order_id,"
                . "brackets.order_received_from,"
                . "brackets.19_24_requested AS brackets_19_24_requested,"
                . "brackets.26_32_requested AS brackets_26_32_requested,"
                . "brackets.36_42_requested AS brackets_36_42_requested,"
                . "brackets.43_requested AS brackets_43_requested,"
                . "brackets.total_requested,"
                . "brackets.19_24_shipped AS brackets_19_24_shipped,"
                . "brackets.26_32_shipped AS brackets_26_32_shipped,"
                . "brackets.36_42_shipped AS brackets_36_42_shipped,"
                . "brackets.43_shipped AS brackets_43_shipped,"
                . "brackets.shipment_receipt,"
                . "brackets.total_shipped,"
                . "brackets.19_24_received AS brackets_19_24_received,"
                . "brackets.26_32_received AS brackets_26_32_received,"
                . "brackets.36_42_received AS brackets_36_42_received,"
                . "brackets.43_received AS brackets_43_received,"
                . "brackets.total_received,"
                . "brackets.order_date,"
                . "brackets.shipment_date,"
                . "brackets.received_date,"
                . "brackets.is_shipped,"
                . "brackets.is_received,"
                . "brackets.active,"
                . "service_centres.name,"
                . "service_centres.owner_name";

        $post['column_order'] = array(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
        $post['column_search'] = array('brackets.order_id', 'service_centres.name', 'service_centres.owner_name');
        $list = $this->inventory_model->get_brackets_query($post);

        $no = $post['start'];
        $data = array();
        foreach ($list as $brackets_list) {
            $no++;
            $row = $this->brackets_requested_table_data($brackets_list, $no);
            $data[] = $row;
        }
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->inventory_model->count_brackets_parts($post),
            "recordsFiltered" => $this->inventory_model->count_brackets_filtered($post),
            "data" => $data,
        );

        echo json_encode($output);
    }

    /**
     * @desc this function is used to create table row data for the spare parts requested tab
     * @param Array $brackets_list
     * @param int $no
     * @return Array
     */
    function brackets_requested_table_data($brackets_list, $no) {

        $date = "";
        if ($brackets_list->order_date > 0) {
            $date = $brackets_list->order_date;
        }
        if ($brackets_list->shipment_date > 0) {
            $date = $brackets_list->order_date;
        }
        if ($brackets_list->received_date > 0) {
            $date = $brackets_list->received_date;
        }

        $row = array();

        if ($brackets_list->is_shipped == 0 && $brackets_list->is_received == 0) {
            $re = "requested_order";
        } else if ($brackets_list->is_shipped == 1 && $brackets_list->is_received == 0) {
            $re = "shipped_order";
        } else if ($brackets_list->is_shipped == 1 && $brackets_list->is_received == 1) {
            $re = "received_order";
        }
        $row[] = "<span class='" . $re . "'>" . $no . "</span>";
        $row[] ='<a href="' . base_url() . 'employee/service_centers/show_brackets_order_history/' . $brackets_list->order_id . '" target="_blank">'.$brackets_list->order_id.'</a>';        
        $row[] = $brackets_list->owner_name . "<br>" . $brackets_list->name;
        $row[] = ($brackets_list->brackets_26_32_requested + $brackets_list->brackets_19_24_requested);
        $row[] = ($brackets_list->brackets_36_42_requested + $brackets_list->brackets_43_requested);
        $row[] = $brackets_list->total_requested;
        $row[] = ($brackets_list->brackets_26_32_shipped + $brackets_list->brackets_19_24_shipped);
        $row[] = ($brackets_list->brackets_36_42_shipped + $brackets_list->brackets_43_shipped);
        $row[] = $brackets_list->total_shipped;
        $row[] = ($brackets_list->brackets_26_32_received + $brackets_list->brackets_19_24_received);
        $row[] = ($brackets_list->brackets_36_42_received + $brackets_list->brackets_43_received);
        $row[] = $brackets_list->total_received;
        $date_timestamp = strtotime($date);
        $new_date = date('j M, Y g:i A', $date_timestamp);
        $row[] = $new_date;     
       
        $update_request_link = '<a  href="' . base_url() . 'employee/inventory/get_update_requested_form/' . $brackets_list->order_id . '" class="btn btn-sm btn-primary" title="Update Requested"';
        if ($brackets_list->is_shipped == 1 || $brackets_list->active == 0) {
            $update_request_link .= 'disabled';
        }
        $update_request_link .= '><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';

        
        $update_shipment_link = '<a href="' . base_url() . 'employee/inventory/get_update_shipment_form/' . $brackets_list->order_id . '" class="btn btn-sm btn-primary" title="Update Shipment"';
        if ($brackets_list->active == 0) {
            $update_shipment_link .= 'disabled';
        }
        $update_shipment_link .= 'style="margin-bottom: 3px;"><i class="fa fa-truck" aria-hidden="true"></i></a>';

       
        $update_receiving_link = '<a href="' . base_url() . 'employee/inventory/get_update_receiving_form/' . $brackets_list->order_id . '" class="btn btn-sm btn-primary" style="margin-bottom: 3px;" title="Update Receiving"';
        if ($brackets_list->is_shipped != 1 || $brackets_list->active == 0) {
            $update_receiving_link .= 'disabled';
        }
        $update_receiving_link .= '> <i class="fa fa-shopping-cart" aria-hidden="true"></i></a>';
        
        $un_cancel_request_link = '<a href="' . base_url() . 'employee/inventory/uncancel_brackets_request/' . $brackets_list->order_id . '" class="btn btn-sm btn-primary" style="margin-bottom: 3px;" title="Un-Cancel Request"';
        if ($brackets_list->active == 1) {
            $un_cancel_request_link .= 'disabled';
        }
        $un_cancel_request_link .= '><i class="fa fa-undo" aria-hidden="true"></i></a>';

        /*   
        if ($brackets_list->is_shipped == 1 || $brackets_list->active == 0) {
            $update_request_link = '<a  href="' . base_url() . 'employee/inventory/get_update_requested_form/' . $brackets_list->order_id . '" class="btn btn-sm btn-primary" title="Update Requested" disabled=TRUE><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
            $update_receiving_link = '<a href="' . base_url() . 'employee/inventory/get_update_receiving_form/' . $brackets_list->order_id . '" class="btn btn-sm btn-primary" style="margin-bottom: 3px;" title="Update Receiving"disabled=TRUE> <i class="fa fa-shopping-cart" aria-hidden="true"></i></a>';
        } else {
            $update_request_link = '<a  href="' . base_url() . 'employee/inventory/get_update_requested_form/' . $brackets_list->order_id . '" class="btn btn-sm btn-primary" title="Update Requested"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
            $update_receiving_link = '<a href="' . base_url() . 'employee/inventory/get_update_receiving_form/' . $brackets_list->order_id . '" class="btn btn-sm btn-primary" style="margin-bottom: 3px;" title="Update Receiving"> <i class="fa fa-shopping-cart" aria-hidden="true"></i></a>';
        }

        if ($brackets_list->active == 0) {
            $update_shipment_link = '<a href="' . base_url() . 'employee/inventory/get_update_shipment_form/' . $brackets_list->order_id . '" class="btn btn-sm btn-primary" title="Update Shipment" disabled=TRUE style="margin-bottom: 3px;"><i class="fa fa-truck" aria-hidden="true"></i></a>';
            $un_cancel_request_link = '<a href="' . base_url() . 'employee/inventory/uncancel_brackets_request/' . $brackets_list->order_id . '" class="btn btn-sm btn-primary" style="margin-bottom: 3px;" title="Un-Cancel Request"disabled=TRUE><i class="fa fa-undo" aria-hidden="true"></i></a>';
        } else {
            $update_shipment_link = '<a href="' . base_url() . 'employee/inventory/get_update_shipment_form/' . $brackets_list->order_id . '" class="btn btn-sm btn-primary" title="Update Shipment" style="margin-bottom: 3px;"><i class="fa fa-truck" aria-hidden="true"></i></a>';
            $un_cancel_request_link = '<a href="' . base_url() . 'employee/inventory/uncancel_brackets_request/' . $brackets_list->order_id . '" class="btn btn-sm btn-primary" style="margin-bottom: 3px;" title="Un-Cancel Request"><i class="fa fa-undo" aria-hidden="true"></i></a>';
        }
    */
        $row[] = $update_request_link . "&nbsp;" . $update_shipment_link . " " . $update_receiving_link . " " . $un_cancel_request_link;

        return $row;
        
    }
    /**
     * @desc This function is used to get micro partner list by Vendor
     * @param String $vendor_id
     */
    function get_micro_partner_list($vendor_id){
        $micro_wh_mapping_list = $this->inventory_model->get_micro_wh_mapping_list(array('micro_warehouse_state_mapping.vendor_id' => $vendor_id,
                'micro_warehouse_state_mapping.active' => 1), 
                'partners.id, partners.public_name');
        echo "<option value='' selected disabled>Select Entity</option>";
        foreach ($micro_wh_mapping_list as $p_name) {
            $option = "<option value='" . $p_name['id'] . "'";

            $option .=" > ";
            $option .= $p_name['public_name'] . "</option>";
            echo $option;
        }
    }
    
    /*
     * @des - This function is used to load view for bill defective spare to service center
     * @param - void
     * @return - view
     */
    function defective_spare_invoice(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/defective_spare_invoice_form');
    }
    
     /*
     * @des - This function is used to get parts for booking
     * @param - booking_id
     * @return - array
     */
    function get_defective_spare_parts(){
        $where_internal_status = array("page" => "bill_defective_spare", "active" => '1');
        $internal_status = $this->booking_model->get_internal_status($where_internal_status);
        $hsn_code = $this->invoices_model->get_hsncode_details('hsn_code, gst_rate', array());
        $hsn_code_html = "<option value='' selected disabled>Select HSN Code</option>";
        foreach ($hsn_code as $value) {
          $hsn_code_html .= "<option value='".$value['hsn_code']."' gst_rate='".$value['gst_rate']."'>".$value['hsn_code']."</option>"; 
        }
        $select = "id, booking_id, parts_shipped, shipped_parts_type, challan_approx_value, service_center_id, status, partner_challan_file";
        $booking_id = $this->input->post('booking_id');
        $where = array("booking_id"=>$booking_id, "status != 'Cancelled'"=>NULL, "sell_invoice_id IS NULL"=>NULL);
        $data['data'] = $this->inventory_model->get_spare_parts_details($select, $where);
        $data['remarks'] = $internal_status;
        $data['hsn_code'] = $hsn_code_html;
        echo json_encode($data);
    }
    /**
     * @desc This function is used to load acknowledge page when warehouse return new inventory to Partner
     */
    function partner_acknowledge_new_inventory(){
        log_message('info', __METHOD__);
        $this->miscelleneous->load_partner_nav_header();
        $this->load->view('partner/acknowledge_new_part');
    }
    /**
     * @desc This function is used to process when partner acknowledge to receive new part return inventory
     */
    function process_acknowledge_msl_send_by_wh_to_partner(){
        log_message('info', __METHOD__ . json_encode($this->input->post(), TRUE));
        
        $sender_entity_id = $this->input->post('sender_entity_id');
        $sender_entity_type = $this->input->post('sender_entity_type');
        $receiver_entity_id = $this->input->post('receiver_entity_id');
        $receiver_entity_type = $this->input->post('receiver_entity_type');
        $is_ack = $this->input->post('is_ack');
        $ack_date = $this->input->post('ack_date');
        $postData = json_decode($this->input->post('data'));
        
        if (!empty($sender_entity_id) && !empty($sender_entity_type) && !empty($receiver_entity_id) && !empty($receiver_entity_type) && !empty($postData)) {
            foreach ($postData as $value) {
                $this->inventory_model->update_ledger_details(array($is_ack => 1, $ack_date => date('Y-m-d H:i:s')), array('id' => $value->ledger_id));
            }
            $res['status'] = TRUE;
            $res['message'] = 'Details updated successfully';
        } else {
            $res['status'] = false;
            $res['message'] = 'All fields are required';
        }
        
        echo json_encode($res);
    }
    
        
     /*
     * @des - This function is used to get reject spare parts
     * @param - 
     * @return - array
     */

    function send_rejected_spare_to_partner() {
        log_message('info', json_encode($this->input->post(), true));

        $spare_data = json_decode($this->input->post('spares_data'), true);
        $sender_entity_id = $this->input->post('sender_entity_id');
        
        if (!empty($spare_data)) {
            $template = array(
                'table_open' => '<table border="1" cellpadding="2" cellspacing="0" class="mytable">'
            );
            $this->table->set_template($template);
            $this->table->set_heading(array('Invoice Id', 'Part Name', 'Quantity'));
            $flag = FALSE;
            foreach ($spare_data as $key => $val) {
                $this->table->add_row(array($val['invoice_id'], $val['part_name'], $val['quantity']));
                /* Here 2 is used to return spare type to partner as is_wh_ack value will 2 */
               $affected_id = $this->inventory_model->update_ledger_details(array('is_wh_ack' => 2), array('id' => $val['ledger_id']));
               if(!empty($affected_id)){
                   $flag = TRUE;
               }
            }
            
            $wh_incharge_id = $this->reusable_model->get_search_result_data("entity_role", "id", array("entity_type" => _247AROUND_PARTNER_STRING, 'role' => WAREHOUSE_INCHARCGE_CONSTANT), NULL, NULL, NULL, NULL, NULL, array());
            
            if (!empty($wh_incharge_id)) {
                //get 247around warehouse incharge email
                $wh_where = array('contact_person.role' => $wh_incharge_id[0]['id'],
                    'contact_person.entity_id' => $sender_entity_id,
                    'contact_person.entity_type' => _247AROUND_PARTNER_STRING
                );

                $email_details = $this->inventory_model->get_warehouse_details('contact_person.official_email', $wh_where, FALSE, TRUE);
                if (!empty($email_details)) {
                    $to = $email_details[0]['official_email'];
                    $rejectspare_details_table = $this->table->generate();
                    $this->send_alert_email_to_spare_part_rejected($rejectspare_details_table, $to);
                }
            }
                       
            if($flag){
                echo json_encode(array('status'=>TRUE));
            }else{
                echo json_encode(array('status'=>FALSE));
            }
        }
    }
    
     /*
     * @des - This function is used to send email
     * @param - 
     * @return - true or flase
     */

    function send_alert_email_to_spare_part_rejected($email_body_data, $to) {
        log_message('info', __METHOD__ . " email_body" . print_r($email_body_data, TRUE));
        $template = $this->booking_model->get_booking_email_template("spare_parts_rejected_email");
        if (!empty($template)) {
            if (empty($to)) {
                $to = $template[1];
            }
            $subject = $template[4];
            $emailBody = vsprintf($template[0], $email_body_data);
            $this->notify->sendEmail($template[2], $to, '', '', $subject, $emailBody, "", 'spare_parts_rejected_email', '');
        }
    }
    
    /*
     * @des - This function is used to Request New spare part form partner lost part cases
     * @param - array
     * @return - json
     */

    function lost_courier_request_new_spare_part_from_partner() {
        log_message('info', json_encode($this->input->post(), true));
        $spare_id = $this->input->post('spare_part_id');
        $reason = $this->input->post('reason');
        if (!empty($spare_id)) {

            $select = 'spare_parts_details.id,spare_parts_details.entity_type,spare_parts_details.booking_id,spare_parts_details.parts_requested,spare_parts_details.status,'
                    . 'booking_details.service_id,booking_details.partner_id as booking_partner_id,booking_details.actor,booking_details.next_action,booking_details.internal_status';
            $spare_parts_details = $this->partner_model->get_spare_parts_by_any($select, array('spare_parts_details.id' => $spare_id), TRUE, TRUE, false);
            if (!empty($spare_parts_details)) {
                $service_id = $spare_parts_details[0]['service_id'];
                $booking_id = $spare_parts_details[0]['booking_id'];
                $partner_id = $spare_parts_details[0]['booking_partner_id'];
                $parts_requested = $spare_parts_details[0]['parts_requested'];
                $internal_status = SPARE_PARTS_REQUIRED;
                $data = array('spare_lost'=>2, "old_status" => $spare_parts_details[0]['status']);
                $affected_id = $this->service_centers_model->update_spare_parts(array('id' => $spare_id), $data);
                if ($affected_id) {
                    $notificationTextArray['msg'] = array($spare_parts_details[0]['parts_requested'], $booking_id);
                    $this->push_notification_lib->create_and_send_push_notiifcation(SPARE_PART_REQUEST_TO_PARTNER, array(), $notificationTextArray);
                    $new_state = COURIER_LOST . " For The " . $parts_requested;

                    $actor = _247AROUND_PARTNER_STRING;
                    $next_action = PARTNER_WILL_SEND_NEW_PARTS;

                    $booking['internal_status'] = $internal_status;
                    $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, $booking['internal_status'], $partner_id, $booking_id);
                    if (!empty($partner_status)) {
                        $booking['partner_current_status'] = $partner_status[0];
                        $booking['partner_internal_status'] = $partner_status[1];
                        $actor = $booking['actor'] = $partner_status[2];
                        $next_action = $booking['next_action'] = $partner_status[3];
                    }
                    $this->notify->insert_state_change($booking_id, $new_state, "", $reason, $this->session->userdata('id'), $this->session->userdata('emp_name'), $actor, $next_action, $partner_id, NULL);

                    if (!empty($booking_id)) {
                        $affctd_id = $this->booking_model->update_booking($booking_id, $booking);
                        if ($affctd_id) {
                            echo json_encode(array('status' => TRUE));
                        } else {
                            echo json_encode(array('status' => FALSE));
                        }
                    }
                }
            }
        }
    }

    /*
     * @des - This function is used to Approve requested spare part 
     * @param - array
     * @return - json
     */

    function spare_part_on_approval($spare_id, $booking_id) {
        log_message('info', json_encode($this->input->post(), true));
            
        $part_warranty_status = $this->input->post('part_warranty_status');
        $reason = $this->input->post('remarks');    
        $data_to_insert = array();
        $delivered_sp = array();

        if (!empty($spare_id)) {

            $select = 'spare_parts_details.id,spare_parts_details.entity_type,spare_parts_details.booking_id,spare_parts_details.parts_requested,spare_parts_details.parts_requested_type,spare_parts_details.status,'
                    . 'spare_parts_details.requested_inventory_id,spare_parts_details.purchase_price,spare_parts_details.service_center_id,spare_parts_details.invoice_gst_rate,'
                    . 'spare_parts_details.is_micro_wh,spare_parts_details.model_number,spare_parts_details.serial_number,spare_parts_details.shipped_inventory_id,spare_parts_details.date_of_request,'
                    . 'booking_details.partner_id as booking_partner_id,booking_details.amount_due,booking_details.next_action,booking_details.internal_status';

            $spare_parts_details = $this->partner_model->get_spare_parts_by_any($select, array('spare_parts_details.id' => $spare_id), TRUE, TRUE, false);
                                    
            if (!empty($spare_parts_details)) {

                $partner_id = $spare_parts_details[0]['booking_partner_id'];
                $requested_inventory_id = $spare_parts_details[0]['requested_inventory_id'];             
                $entity_type = $spare_parts_details[0]['entity_type'];
                $service_center_id = $spare_parts_details[0]['service_center_id'];
                $booking_id = $spare_parts_details[0]['booking_id'];
                $amount_due = $spare_parts_details[0]['amount_due'];
                               
                $data['model_number'] = $spare_parts_details[0]['model_number'];
                $data['parts_requested'] = $spare_parts_details[0]['parts_requested'];
                $data['parts_requested_type'] = $spare_parts_details[0]['parts_requested_type'];
                $data['date_of_request'] = $spare_parts_details[0]['date_of_request'];
                $data['shipped_inventory_id'] = $spare_parts_details[0]['shipped_inventory_id'];
                $data['requested_inventory_id'] = $requested_inventory_id;
                $data['service_center_id'] = $service_center_id;
                $data['booking_id'] = $booking_id;
                $data['entity_type'] = $entity_type;
                $data['partner_id'] = $partner_id;
                $data['is_micro_wh'] = $spare_parts_details[0]['is_micro_wh'];
                $data['shipped_inventory_id'] = $spare_parts_details[0]['shipped_inventory_id'];
                
                /* field part_warranty_status value 1 means in-warranty and 2 means out-warranty*/
                
                 if ($part_warranty_status == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS) {
                    $spare_data['status'] = SPARE_OOW_EST_REQUESTED;
                    $sc_data['internal_status'] = SPARE_OOW_EST_REQUESTED;
                 }else{
                     $spare_data['status'] = SPARE_PARTS_REQUESTED;
                 }
                
                if ($spare_data['status'] == SPARE_OOW_EST_REQUESTED) {
                    
                    $inventory_master_details = $this->inventory_model->get_inventory_master_list_data('inventory_id, hsn_code, gst_rate, price', array('inventory_id'=> $requested_inventory_id));
                                       
                    if ($spare_data['status'] == SPARE_OOW_EST_REQUESTED &&
                            isset($requested_inventory_id) &&
                            !empty($requested_inventory_id) &&
                            $inventory_master_details[0]['price'] > 0 && $entity_type == _247AROUND_SF_STRING) {

                        $cb_url = base_url() . "apiDataRequest/update_estimate_oow";
                        $pcb['booking_id'] = $booking_id;
                        $pcb['assigned_vendor_id'] = $service_center_id;
                        $pcb['amount_due'] = $amount_due;
                        $pcb['partner_id'] = $partner_id;
                        $pcb['sp_id'] = $spare_id;
                        $pcb['gst_rate'] = $inventory_master_details[0]['gst_rate'];

                        $pcb['estimate_cost'] = ($inventory_master_details[0]['price'] + ( $inventory_master_details[0]['price'] * $inventory_master_details[0]['gst_rate']) / 100);
                        $pcb['agent_id'] = $this->session->userdata('id');
                        $this->asynchronous_lib->do_background_process($cb_url, $pcb);                                                
                                              
                    }
                    
                } else {                   
                        //Send Push Notification 
                     
                        array_push($data_to_insert, $data);
                    
                        $receiverArray[array_unique(array_column($data_to_insert, 'entity_type'))[0]] = array(array_unique(array_column($data_to_insert, 'partner_id'))[0]);
                        $notificationTextArray['msg'] = array($data['parts_requested_type'], $booking_id);                        
                        $this->push_notification_lib->create_and_send_push_notiifcation(SPARE_PART_REQUEST_TO_PARTNER, $receiverArray, $notificationTextArray);
                        //End Push Notification
                        $sc_data['current_status'] = "InProcess";
                        $sc_data['internal_status'] = SPARE_PARTS_REQUIRED;
                        $sc_data['service_center_remarks'] = date("F j") . ":- " . $reason;
                        $sc_data['update_date'] = date("Y-m-d H:i:s");
                        $this->vendor_model->update_service_center_action($booking_id, $sc_data);
                                                           
                       if (isset($data['is_micro_wh']) && $data['is_micro_wh'] == 1 && $part_warranty_status == SPARE_PART_IN_WARRANTY_STATUS) {
                        $data['spare_id'] = $spare_id;
                        array_push($delivered_sp, $data);
                        $this->auto_delivered_for_micro_wh($delivered_sp, $partner_id);
                    }
                }

                $spare_data['part_requested_on_approval'] = 1;
                if(!empty($spare_data['status'])){
                    $data['status'] = $spare_data['status'];
                }
                
                $affected_id = $this->service_centers_model->update_spare_parts(array('id' => $spare_id), $spare_data);
  
                  array_push($data_to_insert, $data);     
                  
                  if ($affected_id) {
                    $actor = _247AROUND_PARTNER_STRING;
                    $next_action = PARTNER_WILL_SEND_NEW_PARTS;
                    $booking['internal_status'] = SPARE_PARTS_REQUIRED;
                    $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, $booking['internal_status'], $partner_id, $booking_id);
                    if (!empty($partner_status)) {
                        $booking['partner_current_status'] = $partner_status[0];
                        $booking['partner_internal_status'] = $partner_status[1];
                        $actor = $booking['actor'] = $partner_status[2];
                        $next_action = $booking['next_action'] = $partner_status[3];
                    }

                    $this->notify->insert_state_change($booking_id, PART_APPROVED_BY_ADMIN, "", $reason, $this->session->userdata('id'), $this->session->userdata('emp_name'), $actor, $next_action, $partner_id, NULL);
                    if (!empty($booking_id)) {
                        $affctd_id = $this->booking_model->update_booking($booking_id, $booking);
                        if ($affctd_id) {
                            echo json_encode(array('status' => TRUE));
                        } else {
                            echo json_encode(array('status' => FALSE));
                        }
                    }
                }
            }
        }
    }

    /**
     * @desc this function is used to auto shipped for micro warehouse
     * @param Array $delivered_sp
     * @param int $partner_id
     */
    function auto_delivered_for_micro_wh($delivered_sp, $partner_id){
        log_message('info', __METHOD__);
      
        foreach ($delivered_sp as $value) {
            $data = array();
            $data['model_number_shipped'] = $value['model_number'];
            $data['parts_shipped'] = $value['parts_requested'];
            $data['shipped_parts_type'] = $value['parts_requested_type'];
            $data['shipped_date'] = $value['date_of_request'];
            $data['shipped_date'] = $value['date_of_request'];
            $data['status'] = SPARE_SHIPPED_BY_PARTNER;
            $data['shipped_inventory_id'] = $value['requested_inventory_id'];
            
            $where = array('id' => $value['spare_id']);
            $this->service_centers_model->update_spare_parts($where, $data);
            
            $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $value['service_center_id'], $value['requested_inventory_id'], -1);
            
            $in['receiver_entity_id'] = $value['service_center_id'];
            $in['receiver_entity_type'] = _247AROUND_SF_STRING;
            $in['sender_entity_id'] = $value['service_center_id'];
            $in['sender_entity_type'] = _247AROUND_SF_STRING;
            $in['stock'] = -1;
            $in['booking_id'] = $value['booking_id'];
            $in['agent_id'] = $this->session->userdata('id');
            $in['agent_type'] = _247AROUND_SF_STRING;
            $in['is_wh'] = TRUE;
            $in['inventory_id'] = $data['shipped_inventory_id'];
            $this->miscelleneous->process_inventory_stocks($in);
            $this->acknowledge_delivered_spare_parts($value['booking_id'], $value['service_center_id'], $value['spare_id'], $partner_id, TRUE);
        }
    }
    
    
    /**
     * @desc: This is used to update acknowledge date by SF
     * @param String $booking_id
     */
    function acknowledge_delivered_spare_parts($booking_id, $service_center_id, $id, $partner_id, $autoAck = false, $flag = TRUE) {
        log_message('info', __FUNCTION__ . " Booking ID: " . $booking_id . ' service_center_id: ' . $service_center_id . ' id: ' . $id);
        if (empty($autoAck)) {
            $this->checkUserSession();
        }
        if (!empty($booking_id)) {

            $where = array('id' => $id);
            $sp_data['service_center_id'] = $service_center_id;
            $sp_data['acknowledge_date'] = date('Y-m-d');
            $sp_data['status'] = SPARE_DELIVERED_TO_SF;
            if (!empty($autoAck)) {
                $sp_data['auto_acknowledeged'] = 1;
            } else {
                $sp_data['auto_acknowledeged'] = 0;
            }
            $actor = $next_action = NULL;
            //Update Spare Parts table
            $ss = $this->service_centers_model->update_spare_parts($where, $sp_data);
            if ($ss) { //if($ss){
                $is_requested = $this->partner_model->get_spare_parts_by_any("id, status, booking_id", array('booking_id' => $booking_id, 'status IN ("' . SPARE_SHIPPED_BY_PARTNER . '", "'
                    . SPARE_PARTS_REQUESTED . '", "' . ESTIMATE_APPROVED_BY_CUSTOMER . '", "' . SPARE_OOW_EST_GIVEN . '", "' . SPARE_OOW_EST_REQUESTED . '") ' => NULL));
                if ($this->session->userdata('service_center_id')) {
                    $agent_id = $this->session->userdata('service_center_agent_id');
                    $sc_entity_id = $this->session->userdata('service_center_id');
                    $p_entity_id = NULL;
                } else {
                    $agent_id = _247AROUND_DEFAULT_AGENT;
                    $p_entity_id = _247AROUND;
                    $sc_entity_id = NULL;
                }
                if (empty($is_requested)) {
                    $booking['booking_date'] = date('d-m-Y', strtotime('+1 days'));
                    $booking['update_date'] = date("Y-m-d H:i:s");
                    $booking['internal_status'] = SPARE_PARTS_DELIVERED;

                    $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, SPARE_PARTS_DELIVERED, $partner_id, $booking_id);
                    $actor = $next_action = 'not_define';
                    if (!empty($partner_status)) {
                        $booking['partner_current_status'] = $partner_status[0];
                        $booking['partner_internal_status'] = $partner_status[1];
                        $actor = $booking['actor'] = $partner_status[2];
                        $next_action = $booking['next_action'] = $partner_status[3];
                    }
                    $b_status = $this->booking_model->update_booking($booking_id, $booking);
                    if ($b_status) {

                        $this->notify->insert_state_change($booking_id, SPARE_PARTS_DELIVERED, _247AROUND_PENDING, "SF acknowledged to receive spare parts", $agent_id, $agent_id, $actor, $next_action, $p_entity_id, $sc_entity_id);


                        $sc_data['current_status'] = _247AROUND_PENDING;
                        $sc_data['internal_status'] = SPARE_PARTS_DELIVERED;
                        $sc_data['update_date'] = date("Y-m-d H:i:s");
                        $this->vendor_model->update_service_center_action($booking_id, $sc_data);
                        if ($this->session->userdata('service_center_id')) {
                            $userSession = array('success' => 'Booking Updated');
                            $this->session->set_userdata($userSession);
                        }
                        $cb_url = base_url() . "employee/do_background_process/send_request_for_partner_cb/" . $booking_id;
                        $pcb = array();
                        $this->asynchronous_lib->do_background_process($cb_url, $pcb);
                    } else {//if ($b_status) {
                        log_message('info', __FUNCTION__ . " Booking is not updated. Service_center ID: "
                                . $service_center_id .
                                "Booking ID: " . $booking_id);
                        if ($this->session->userdata('service_center_id')) {
                            $userSession = array('success' => 'Please Booking is not updated');
                            $this->session->set_userdata($userSession);
                        }
                    }
                } else {


                    $this->notify->insert_state_change($booking_id, SPARE_PARTS_DELIVERED, _247AROUND_PENDING, "SF acknowledged to receive spare parts", $agent_id, $agent_id, $actor, $next_action, $p_entity_id, $sc_entity_id);
                    if ($this->session->userdata('service_center_id')) {
                        $userSession = array('success' => 'Booking Updated');
                        $this->session->set_userdata($userSession);
                    }
                    $cb_url = base_url() . "employee/do_background_process/send_request_for_partner_cb/" . $booking_id;
                    $pcb = array();
                    $this->asynchronous_lib->do_background_process($cb_url, $pcb);
                }
            } else {
                log_message('info', __FUNCTION__ . " Spare parts ack date is not updated Service_center ID: "
                        . $service_center_id .
                        "Booking ID: " . $booking_id);
                if ($this->session->userdata('service_center_id')) {
                    $userSession = array('error' => 'Booking is not updated');
                    $this->session->set_userdata($userSession);
                }
            }
        }
        log_message('info', __FUNCTION__ . " Exit Service_center ID: " . $service_center_id);
        if ($this->session->userdata('service_center_id')) {
            if ($flag == TRUE) {
                redirect(base_url() . "service_center/pending_booking");
            }
        }
    }

}
