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
                . "defective_part_required, partner_challan_file, parts_requested, incoming_invoice_pdf, sell_invoice_id, booking_details.partner_id as booking_partner_id";
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
    function get_spare_requested_tab($post){
        log_message('info', __METHOD__);
        
        $post['select'] = "spare_parts_details.booking_id, users.name, booking_primary_contact_no, service_centres.name as sc_name,"
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
        
        $post['select'] = "spare_parts_details.booking_id, users.name, booking_primary_contact_no, service_centres.name as sc_name,"
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
            
            if($spare_list->defective_part_required == '0'){ $required_parts =  'REQUIRED_PARTS'; $text = "Required"; $cl ="btn-primary";} else{ $text = "Not Required"; $required_parts =  'NOT_REQUIRED_PARTS'; $cl = "btn-danger"; }
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
        
        $row[] = $spare_list->sell_invoice_id;
        if(!empty($spare_list->incoming_invoice_pdf)){
            $row[] = '<a target="_blank" href="https://s3.amazonaws.com/'.BITBUCKET_DIRECTORY.'/invoices-excel/<?php echo $value->incoming_invoice_pdf;  ?>">
                            <img style="width:27px;" src="<?php echo base_url();?>images/invoice_icon.png"; /></a>';
        } else {
            $row[] = "";
        }
        $row[] = '<input type="checkbox" class="form-control spare_id" name="spare_id[]" value="'.$spare_list->id.'" />';
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
        $c_tag = ($spare_list->request_type == REPAIR_OOW_TAG && $spare_list->status != SPARE_PARTS_REQUESTED)? "QUOTE_REQUEST_REJECTED":"CANCEL_PARTS";
        $row[] = '<button type="button" data-booking_id="'.$spare_list->booking_id.'" data-url="'.base_url().'employee/inventory/update_action_on_spare_parts/'.$spare_list->id.'/'.$spare_list->booking_id.'/'.$c_tag.'" class="btn btn-primary btn-sm open-adminremarks" data-toggle="modal" data-target="#myModal2">Cancel</button>';
        if($spare_list->defective_part_required == '0'){ $required_parts =  'REQUIRED_PARTS'; $text = "Required"; $cl ="btn-primary";} else{ $text = "Not Required"; $required_parts =  'NOT_REQUIRED_PARTS'; $cl = "btn-danger"; }
        $row[] = '<button type="button" data-booking_id="'.$spare_list->booking_id.'" data-url="'.base_url().'employee/inventory/update_action_on_spare_parts/'.$spare_list->id.'/'.$spare_list->booking_id.'/'.$required_parts.'" class="btn btn-sm '.$cl.' open-adminremarks" data-toggle="modal" data-target="#myModal2">'.$text.'</button>';
        
        
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
        if(!empty($this->input->post('vendor_partner'))){
            $post['vendor_partner'] = $this->input->post('vendor_partner');
        }
        else{
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
}