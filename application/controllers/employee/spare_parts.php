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
        $this->load->model('employee_model');
        $this->load->model('vendor_model');
        
        $this->load->library('form_validation');
        $this->load->library('notify');
        $this->load->library('S3');
        $this->load->library('PHPReport');
        $this->load->library('booking_utilities');

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
        
        switch ($post['type']) {
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
                $this->get_defective_parts_pending($post);
                break; 
            case 10:
                $this->get_approval_pending($post);
                break;
            case 11:
                $this->get_spare_rejected($post);
                break;
            case 12:
                $this->get_courier_lost_spare_parts($post);
                break;
            case 13:
                $this->get_part_rejected_by_warehouse($post);
                break;
            case 15:
                /* Return Defective Part To Warehouse */
                $this->get_defective_part_out_of_tat_pending($post);
                break;
        }
    }
    /**
     * @desc used to create defective parts shipped by sf tab and whose courier price approved by Admin
     * @param Array $post
     */
    function get_approved_defective_part_shipped_by_sf($post){
        $post['select'] = "spare_parts_details.booking_id,spare_parts_details.partner_id,spare_parts_details.quantity, spare_parts_details.shipped_quantity,users.name, booking_primary_contact_no, service_centres.name as sc_name,"
                . "partners.public_name as source, defective_part_shipped, courier_name_by_sf, awb_by_sf, courier_charges_by_sf, spare_parts_details.is_micro_wh,"
                . "remarks_defective_part_by_sf, defective_courier_receipt,sf_challan_file, defective_part_required, spare_parts_details.id,"
                . "booking_details.request_type, parts_shipped, remarks_defective_part_by_partner, defactive_part_received_date_by_courier_api,inventory_master_list.part_number,im.part_number as shipped_part_number ";
        $post['column_order'] = array( NULL, NULL,NULL,NULL,NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,NULL, NULL,'age_defective_part_shipped_date', NULL);
        
        $post['column_search'] = array('spare_parts_details.booking_id','partners.public_name', 'service_centres.name', 'users.name', 'users.phone_number',
            'defective_part_shipped');
        if(!empty($post['where'])) {
            $post['where']["(spare_lost is null or spare_lost = 0)"] = NULL;
        }
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
        $post['select'] = "spare_parts_details.booking_id,spare_parts_details.partner_id, spare_parts_details.quantity,spare_parts_details.shipped_quantity,users.name, booking_primary_contact_no, service_centres.name as sc_name,"
                . "partners.public_name as source, defective_part_shipped, courier_name_by_sf, awb_by_sf, courier_charges_by_sf, spare_parts_details.is_micro_wh,"
                . "partners.public_name as source, defective_part_shipped, courier_name_by_sf, awb_by_sf, courier_charges_by_sf, spare_parts_details.defective_part_shipped_date, "
                . "remarks_defective_part_by_sf, defective_courier_receipt,sf_challan_file, defective_part_required, spare_parts_details.id, inventory_master_list.part_number, spare_consumption_status.consumed_status";

       // $post['column_order'] = array();
        $post['column_order'] = array( NULL, NULL,NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,NULL, NULL,NULL, NULL,NULL,'age_defective_part_shipped_date',NULL);
       
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
        
        $post['select'] = "spare_parts_details.booking_id,spare_parts_details.partner_id,spare_parts_details.quantity,spare_parts_details.shipped_quantity, users.name, booking_primary_contact_no, service_centres.name as sc_name,"
                . "partners.public_name as source, parts_requested, booking_details.request_type, spare_parts_details.id,"
                . "defective_part_required, spare_parts_details.shipped_date, parts_shipped, spare_parts_details.is_micro_wh,"
                . "spare_parts_details.acknowledge_date, challan_approx_value, status, defective_part_shipped,"
                . "remarks_defective_part_by_sf, remarks_defective_part_by_partner, defective_courier_receipt, inventory_master_list.part_number,im.part_number as shipped_part_number, spare_parts_details.challan_approx_value ";

        $post['column_order'] = array( NULL, NULL,NULL, NULL, NULL,NULL,NULL, NULL, NULL, NULL, NULL, NULL,NULL, NULL, NULL,'age_defective_part_shipped_date',NULL, NULL, NULL, NULL, NULL);
        
        
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
     * Defective parts shipped by sf but Warehouse rejected 
     * @param Array $post
     */
    function get_part_rejected_by_warehouse($post) {
        log_message('info', __METHOD__);

        $post['select'] = "spare_parts_details.booking_id,spare_parts_details.partner_id,spare_parts_details.quantity,spare_parts_details.shipped_quantity, users.name, booking_primary_contact_no, service_centres.name as sc_name,"
                . "partners.public_name as source, parts_requested, booking_details.request_type, spare_parts_details.id,"
                . "defective_part_required, spare_parts_details.shipped_date, parts_shipped, spare_parts_details.is_micro_wh,"
                . "spare_parts_details.acknowledge_date, challan_approx_value, status, defective_part_shipped, rejected_defective_part_pic_by_wh,"
                . "remarks_defective_part_by_sf, remarks_defective_part_by_partner, defective_courier_receipt, inventory_master_list.part_number,im.part_number as shipped_part_number, spare_parts_details.challan_approx_value ";

        $post['column_order'] = array(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'age_defective_part_shipped_date', NULL, NULL, NULL, NULL, NULL);


        $post['column_search'] = array('spare_parts_details.booking_id', 'partners.public_name', 'service_centres.name', 'parts_shipped',
            'users.name', 'users.phone_number', 'defective_part_shipped', 'booking_details.request_type', 'remarks_defective_part_by_sf', 'remarks_defective_part_by_partner');
        
        if(!empty($post['where']) && !empty($post['where']['status'])) {
            unset($post['where']['status']);
            $post['where']['status in ("'.DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE.'", "'.OK_PARTS_REJECTED_BY_WAREHOUSE.'")'] = NULL;
        }
        
        $list = $this->inventory_model->get_spare_parts_query($post);
        $no = $post['start'];
        $data = array();
        foreach ($list as $spare_list) {
            $no++;
            $row = $this->parts_rejected_by_wh_table_data($spare_list, $no);
            $data[] = $row;
        }
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->inventory_model->count_spare_parts($post),
            "recordsFiltered" => $this->inventory_model->count_spare_filtered($post),
            "data" => $data,
        );

        echo json_encode($output);
    }

      
     /**
     * @desc this function is used to create table row data for the spare parts rejected by partner tab
     * @param Array $spare_list
     * @param int $no
     * @return Array
     */
    
    function parts_rejected_by_wh_table_data($spare_list, $no) {
        $row = array();
        $row[] = $no;
        $row[] = '<a href="' . base_url() . 'employee/booking/viewdetails/' . $spare_list->booking_id . '" target= "_blank" >' . $spare_list->booking_id . '</a>';
        if ($spare_list->is_micro_wh == 1) {
            $spare_pending_on = 'Micro-warehouse';
        } elseif ($spare_list->is_micro_wh == 2) {
            $wh_details = $this->vendor_model->getVendorContact($spare_list->partner_id);
            if (!empty($wh_details)) {
                $spare_pending_on = $wh_details[0]['district'] . ' Warehouse';
            } else {
                $spare_pending_on = 'Warehouse';
            }
        } else {
            $spare_pending_on = 'Partner';
        }
        $row[] = $spare_pending_on;
        $row[] = $spare_list->name;
        $row[] = $spare_list->booking_primary_contact_no;
        $row[] = $spare_list->sc_name;
        $row[] = $spare_list->source;
        $row[] = "<span class='line_break'>" . $spare_list->parts_shipped . "</span>";
        $row[] = $spare_list->quantity;
        $row[] = $spare_list->shipped_quantity;
        $row[] = $spare_list->challan_approx_value;
        $row[] = "<span class='line_break'>" . $spare_list->part_number . "</span>";
        $row[] = "<span class='line_break'>" . $spare_list->defective_part_shipped . "</span>";
        $row[] = "<span class='line_break'>" . $spare_list->shipped_part_number . "</span>";
        $row[] = $spare_list->request_type;
        $row[] = (empty($spare_list->age_defective_part_shipped_date)) ? '0 Days' : $spare_list->age_defective_part_shipped_date . " Days";
        $row[] = $spare_list->remarks_defective_part_by_sf;
        $row[] = $spare_list->remarks_defective_part_by_partner;
        $row[] = '<a href="' . S3_WEBSITE_URL . 'misc-images/' . $spare_list->defective_courier_receipt . '" target="_blank">Click Here</a>';
        
        if(!empty($spare_list->rejected_defective_part_pic_by_wh)){
         $row[] = '<a href="' . S3_WEBSITE_URL . 'misc-images/' . $spare_list->rejected_defective_part_pic_by_wh . '" target="_blank">Click Here</a>';   
        }else{
         $row[] = '';   
        }
        
        if ($this->session->userdata('user_group') == "inventory_manager" || $this->session->userdata('user_group') == "admin" || $this->session->userdata('user_group') == "developer" || $this->session->userdata('user_group') == "accountmanager") {
            $row[] = '<button type="button" data-button="Mark Shipped"  data-booking_id="' . $spare_list->booking_id . '" data-url="' . base_url() . 'employee/inventory/update_action_on_spare_parts/' . $spare_list->id . '/' . $spare_list->booking_id . '/DEFECTIVE_PARTS_SHIPPED_BY_SF" class="btn btn-warning btn-sm open-adminremarks" data-toggle="modal" data-target="#myModal2"><i class="glyphicon glyphicon-send" style="font-size:16px;"></i></button>';
            if ($spare_list->defective_part_required == '0') {
                $required_parts = 'REQUIRED_PARTS';
                $text = '<i class="glyphicon glyphicon-ok-circle" style="font-size: 16px;"></i>';
                $cl = "btn-primary";
            } else {
                $text = '<i class="glyphicon glyphicon-ban-circle" style="font-size: 16px;"></i>';
                $required_parts = 'NOT_REQUIRED_PARTS_FOR_COMPLETED_BOOKING';
                $cl = "btn-danger";
            }
            $row[] = '<button type="button" data-booking_id="' . $spare_list->booking_id . '" data-url="' . base_url() . 'employee/inventory/update_action_on_spare_parts/' . $spare_list->id . '/' . $spare_list->booking_id . '/' . $required_parts . '" class="btn btn-sm ' . $cl . ' open-adminremarks" data-toggle="modal" data-target="#myModal2">' . $text . '</button>';
        } else {
            $row[] = "";
            $row[] = "";
        }

        return $row;
    }

    /**
     * @desc Used to create tab in which we are showing
     * Part delivered to sf 
     * @param Array $post
     */
    function get_part_delivered_to_sf($post){
        log_message('info', __METHOD__. json_encode($post, true));
        
        $post['select'] = "spare_parts_details.booking_id,spare_parts_details.partner_id,spare_parts_details.quantity,spare_parts_details.shipped_quantity, users.name, booking_primary_contact_no, service_centres.name as sc_name,"
                . "partners.public_name as source, parts_requested, booking_details.request_type, spare_parts_details.id, spare_parts_details.shipped_parts_type,"
                . "defective_part_required, spare_parts_details.shipped_date, parts_shipped, spare_parts_details.is_micro_wh,"
                . "spare_parts_details.acknowledge_date, challan_approx_value, status ,inventory_master_list.part_number,im.part_number as shipped_part_number,spare_parts_details.quantity,spare_parts_details.shipped_quantity";
        if ($this->input->post("status") == SPARE_DELIVERED_TO_SF) {
            $post['column_order'] = array(NULL, 'spare_parts_details.booking_id', NULL, NULL, NULL, 'service_centres.name', NULL, NULL, NULL, 'spare_parts_details.shipped_parts_type', NULL, NULL, NULL, NULL,NULL, NULL,NULL,NULL, 'age_of_delivered_to_sf', NULL);
        } else {
            $post['column_order'] = array( NULL, 'spare_parts_details.booking_id',NULL, NULL, NULL,'service_centres.name', NULL, NULL,NULL, 'spare_parts_details.shipped_parts_type', NULL, NULL,NULL, NULL, NULL,'age_part_pending_to_sf',NULL);
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
    
     /**
     * @desc Used to create tab in which we are showing
     * Part defective part pending 
     * @param Array $post
     */
    function get_defective_parts_pending($post){
        log_message('info', __METHOD__. json_encode($post, true));
        
        $post['select'] = "spare_parts_details.booking_id,spare_parts_details.partner_id,spare_parts_details.quantity,spare_parts_details.shipped_quantity, users.name, booking_primary_contact_no, service_centres.name as sc_name, service_centres.on_off, service_centres.active,"
                . "partners.public_name as source, parts_requested, booking_details.request_type, spare_parts_details.id, spare_parts_details.shipped_parts_type,"
                . "defective_part_required, spare_parts_details.shipped_date, parts_shipped, spare_parts_details.around_pickup_from_service_center,"
                . "spare_parts_details.acknowledge_date, spare_parts_details.around_pickup_courier, spare_parts_details.is_micro_wh, spare_parts_details.service_center_id, challan_approx_value, status, inventory_master_list.part_number,im.part_number as shipped_part_number, spare_consumption_status.consumed_status";
        if($this->input->post("status") == SPARE_DELIVERED_TO_SF){
            $post['column_order'] = array( NULL, 'spare_parts_details.booking_id', NULL, 'service_centres.name', NULL, NULL,NULL, NULL, NULL, 'spare_parts_details.shipped_parts_type', NULL, NULL, NULL, NULL, NULL, NULL, 'age_of_delivered_to_sf', NULL, NULL, NULL, NULL);
        } else {
            $post['column_order'] = array( NULL, 'spare_parts_details.booking_id', NULL, 'service_centres.name', NULL, NULL,NULL, NULL, NULL, 'spare_parts_details.shipped_parts_type', NULL, NULL, NULL, NULL, NULL, NULL, 'age_part_pending_to_sf', NULL, NULL, NULL, NULL);
        }
        $post['column_search'] = array('spare_parts_details.booking_id','partners.public_name', 'service_centres.name', 'parts_shipped', 
            'parts_requested', 'booking_details.request_type', 'spare_parts_details.shipped_date');
        
        if(!empty($post['where']) && $post['where']['status'] == DEFECTIVE_PARTS_PENDING) {
            unset($post['where']['status']);
            $post['where']['status in ("'.DEFECTIVE_PARTS_PENDING.'","'.OK_PART_TO_BE_SHIPPED.'","'.DAMAGE_PART_TO_BE_SHIPPED.'")'] = NULL;
        }
        $list = $this->inventory_model->get_spare_parts_query($post);
        $no = $post['start'];
        $data = array();
        foreach ($list as $spare_list) {
            $no++;
            $row =  $this->defective_parts_pending_table_data($spare_list, $no);
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
         $post['select'] = "spare_parts_details.booking_id,spare_parts_details.partner_id,spare_parts_details.id,spare_parts_details.quantity, users.name, booking_primary_contact_no, service_centres.name as sc_name,"
                . "partners.public_name as source, parts_shipped, booking_details.request_type, spare_parts_details.is_micro_wh, spare_parts_details.id, spare_parts_details.parts_requested_type,"
                . "defective_part_required, partner_challan_file, parts_requested, incoming_invoice_pdf, sell_invoice_id, booking_details.partner_id as booking_partner_id, purchase_price, inventory_master_list.part_number,oow_spare_invoice_details.invoice_id,oow_spare_invoice_details.invoice_pdf, spare_parts_details.courier_price_by_partner, spare_parts_details.defective_parts_pic, spare_parts_details.defective_back_parts_pic ";
        $post['column_order'] = array( NULL, NULL,NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,NULL,'age_of_shipped_date',NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
        $post['column_search'] = array('spare_parts_details.booking_id','partners.public_name', 'service_centres.name', 'parts_shipped', 
            'users.name', 'users.phone_number', 'parts_requested', 'booking_details.request_type');
        $post['spare_invoice_flag'] = true;
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

    function get_spare_requested_tab($post) {
        log_message('info', __METHOD__);
        $post['select'] = "employee.full_name,entity_login_table.agent_name,spare_parts_details.booking_id,spare_parts_details.partner_id,spare_parts_details.partner_id,spare_parts_details.partner_id,spare_parts_details.quantity,spare_parts_details.part_warranty_status,spare_parts_details.model_number, users.name, booking_primary_contact_no, service_centres.name as sc_name,"
                . "partners.public_name as source, parts_requested, booking_details.request_type, spare_parts_details.id,spare_parts_details.part_requested_on_approval, spare_parts_details.part_warranty_status,"
                . "defective_part_required, spare_parts_details.parts_requested_type,spare_parts_details.quantity,spare_parts_details.shipped_quantity,spare_parts_details.is_micro_wh,spare_parts_details.spare_approval_date,spare_parts_details.approval_entity_type, status, inventory_master_list.part_number ";
        if (isset($post['approved'])) {
            $post['approved'] = 1;
            $post['column_order'] = array(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'age_of_request', NULL, NULL, NULL);
        } else {
             $post['approved'] = 0;
            $post['column_order'] = array(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'age_of_request', NULL, NULL, NULL);
        }

        $post['column_search'] = array('spare_parts_details.booking_id', 'partners.public_name', 'service_centres.name',
            'parts_requested', 'users.name', 'users.phone_number', 'booking_details.request_type');
        $post['approval_date_and_id'] = TRUE;  
        $list = $this->inventory_model->get_spare_parts_query($post);

        $no = $post['start'];
        $data = array();
        foreach ($list as $spare_list) {
            $no++;
 
            $row = $this->spare_parts_requested_table_data($spare_list, $no, $post['request_type'],$post['approved']);
            $data[] = $row;
        }
        
        $spare_parts_list = $this->partner_model->get_spare_parts_by_any('spare_parts_details.id', array('spare_parts_details.status' => SPARE_PART_ON_APPROVAL, 'spare_parts_details.part_requested_on_approval' => 0), false, false, false);
        if (!empty($spare_parts_list)) {
            $total = count($spare_parts_list);
        } else {
            $total = 0;
        }

        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->inventory_model->count_spare_parts($post),
            "recordsFiltered" =>  $this->inventory_model->count_spare_filtered($post),
            "unapproved" => $total,
            "data" => $data,
            
        );
        
        echo json_encode($output);
        
    }




           /**
     * @desc Used to create tab in which we are showing
     * Parts rejected
     * @param Array $post
     */
    function get_spare_rejected($post){
        log_message('info', __METHOD__);       
        $post['select'] = "spare_parts_details.booking_id,spare_parts_details.partner_id,spare_parts_details.quantity,spare_parts_details.spare_cancelled_date,spare_parts_details.part_warranty_status,spare_parts_details.model_number, users.name, booking_primary_contact_no, service_centres.name as sc_name,"
                . "partners.public_name as source, parts_requested, booking_details.request_type, spare_parts_details.id,spare_parts_details.part_requested_on_approval, spare_parts_details.part_warranty_status,"
                . "defective_part_required, spare_parts_details.parts_requested_type,spare_parts_details.is_micro_wh, status, inventory_master_list.part_number, booking_cancellation_reasons.reason as part_cancel_reason, booking_details.state ";
        $post['column_order'] = array( NULL, NULL,NULL,NULL,NULL,NULL,NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'spare_cancelled_date',NULL, NULL);
        $post['column_search'] = array('spare_parts_details.booking_id','partners.public_name', 'service_centres.name', 
            'parts_requested', 'users.name', 'users.phone_number', 'booking_details.request_type', 'booking_details.state');
        $post['where_in']=array('booking_details.current_status'=>array(_247AROUND_PENDING,_247AROUND_RESCHEDULED));
        $post['spare_cancel_reason'] = 1;
        $list = $this->inventory_model->get_spare_parts_query($post);
        $no = $post['start'];  
        $data = array();
        foreach ($list as $spare_list) {
            $no++;
            $row =  $this->spare_parts_rejected_table_data($spare_list, $no, $post['request_type']);
            $data[] = $row;
        }
        
        $spare_parts_list = $this->partner_model->get_spare_parts_by_any('spare_parts_details.id', array('spare_parts_details.status' => _247AROUND_CANCELLED, 'spare_parts_details.part_requested_on_approval' => 0), false, false, false);
        if (!empty($spare_parts_list)) {
            $total = count($spare_parts_list);
        } else {
            $total = 0;
        }

        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->inventory_model->count_spare_parts($post),
            "recordsFiltered" =>  $this->inventory_model->count_spare_filtered($post),
            "unapproved" => $total,
            "data" => $data,
            
        );
        
        echo json_encode($output);
        
    }

    function get_courier_lost_spare_parts($post) {
        log_message('info', __METHOD__);       
        $post['select'] = "spare_parts_details.booking_id,spare_parts_details.partner_id,spare_parts_details.quantity,spare_parts_details.spare_cancelled_date,spare_parts_details.part_warranty_status,spare_parts_details.model_number, users.name, booking_primary_contact_no, service_centres.name as sc_name,"
                . "partners.public_name as source, parts_requested, booking_details.request_type, spare_parts_details.id,spare_parts_details.part_requested_on_approval, spare_parts_details.part_warranty_status,"
                . "defective_part_required, spare_parts_details.parts_shipped, spare_parts_details.shipped_quantity, spare_parts_details.parts_requested_type,spare_parts_details.is_micro_wh, status, inventory_master_list.part_number, booking_cancellation_reasons.reason as part_cancel_reason, booking_details.state ";
        $post['column_order'] = array( NULL, NULL,NULL,NULL,NULL,NULL,NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'age_of_request',NULL, 'age_of_request');
        $post['column_search'] = array('spare_parts_details.booking_id','partners.public_name', 'service_centres.name', 
            'parts_requested', 'users.name', 'users.phone_number', 'booking_details.request_type', 'booking_details.state');
        //$post['where_in']=array('booking_details.current_status'=>array(_247AROUND_PENDING,_247AROUND_RESCHEDULED));
        $post['spare_cancel_reason'] = 1;
        $list = $this->inventory_model->get_spare_parts_query($post);
        $no = $post['start'];  
        $data = array();
        foreach ($list as $spare_list) {
            $no++;
            $row =  $this->courier_lost_spare_parts_table_data($spare_list, $no, $post['request_type']);
            $data[] = $row;
        }
        
        $spare_parts_list = $this->partner_model->get_spare_parts_by_any('spare_parts_details.id', array('spare_parts_details.status' => _247AROUND_CANCELLED, 'spare_parts_details.part_requested_on_approval' => 0), false, false, false);
        if (!empty($spare_parts_list)) {
            $total = count($spare_parts_list);
        } else {
            $total = 0;
        }

        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->inventory_model->count_spare_parts($post),
            "recordsFiltered" =>  $this->inventory_model->count_spare_filtered($post),
            "unapproved" => $total,
            "data" => $data,
            
        );
        
        echo json_encode($output);
    }

    /**
     * @desc Used to create tab in which we are showing
     * Parts requested by Sf and pending fro approval
     * @param Array $post
     */
    function get_approval_pending($post){        
        log_message('info', __METHOD__);       
        $post['select'] = "spare_parts_details.booking_id,spare_parts_details.partner_id,spare_parts_details.part_warranty_status,spare_parts_details.model_number,spare_parts_details.date_of_purchase,STR_TO_DATE(booking_details.create_date, '%Y-%m-%d') as booking_create_date, users.name, booking_primary_contact_no, service_centres.name as sc_name,"
                . "partners.public_name as source, parts_requested, booking_details.request_type, spare_parts_details.id,spare_parts_details.part_requested_on_approval, spare_parts_details.part_warranty_status,"
                . "defective_part_required, spare_parts_details.parts_requested_type,spare_parts_details.is_micro_wh, status, inventory_master_list.part_number ";

        $post['column_order'] = array(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,NULL,NULL, 'age_of_request', NULL, NULL,NULL,NULL,NULL);
        $post['column_search'] = array('spare_parts_details.booking_id', 'partners.public_name', 'service_centres.name',
            'parts_requested', 'users.name', 'users.phone_number', 'booking_details.request_type');
        $post['where']['partners.spare_approval_by_partner'] = 0;
        $list = $this->inventory_model->get_spare_parts_query($post);
        $no = $post['start'];
        $data = array();
        
        // Function to get Bookings for calculating warranty status
        $arrBookingsData = [];
        $arrList = json_decode(json_encode($list), true); 
        if(!empty($arrList))
        { 
            $arrBookingsData = array_map(function($recData) {
                $arrData['partner_id'] = $recData['partner_id'];
                $arrData['booking_id'] = $recData['booking_id'];
                $arrData['booking_create_date'] = $recData['booking_create_date'];
                $arrData['model_number'] = $recData['model_number'];
                $arrData['purchase_date'] = $recData['date_of_purchase'];
                $arrData['in_warranty_period'] = 12;
                $arrData['extended_warranty_period'] = 0;
                // Choose only Videocon bookings whose model and dop exists
                // ADDED THIS CONDITION ALSO ($recData['partner_id'] != VIDEOCON_ID) 
                if(empty($arrData['model_number']) || empty($arrData['purchase_date']) || $arrData['purchase_date'] == '0000-00-00'):
                    return;
                endif;
                return $arrData;
            },$arrList);

            $arrBookingsData = array_filter($arrBookingsData);                
            $arrBookingsData = array_chunk($arrBookingsData, 50);
        } 
        // Function ends here

        foreach ($list as $spare_list) {
            $no++;
            $row =  $this->spare_parts_onapproval_table_data($spare_list, $no, $post['request_type']);
            $data[] = $row;
        }
        
        $total = $this->inventory_model->count_spare_filtered($post);
       
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->inventory_model->count_spare_parts($post),
            "recordsFiltered" =>  $total,
            "unapproved" => $total,
            "data" => $data,            
            "bookings_data" => $arrBookingsData
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
        
        $post['select'] = "spare_parts_details.booking_id,spare_parts_details.partner_id,spare_parts_details.spare_lost,spare_parts_details.quantity,spare_parts_details.shipped_quantity, users.name, booking_primary_contact_no, service_centres.name as sc_name,"
                . "partners.public_name as source, parts_shipped, booking_details.request_type,spare_parts_details.is_micro_wh, spare_parts_details.id, spare_parts_details.parts_requested_type,"

                . "defective_part_required, partner_challan_file, parts_requested, inventory_master_list.part_number,im.part_number as shipped_part_number ";
        $post['column_order'] = array(NULL, NULL, NULL, NULL,NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,NULL,NULL, 'age_of_shipped_date', NULL, NULL, NULL);
        $post['column_search'] = array('spare_parts_details.booking_id', 'partners.public_name', 'service_centres.name', 'parts_shipped',
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
        if($spare_list->is_micro_wh == 1){
         $spare_pending_on = 'Micro-warehouse';   
        }elseif ($spare_list->is_micro_wh == 2) {
            $wh_details = $this->vendor_model->getVendorContact($spare_list->partner_id);
            if(!empty($wh_details)){
              $spare_pending_on = $wh_details[0]['district'] . ' Warehouse';   
            }else{
              $spare_pending_on = ' Warehouse'; 
            }
            
        } else {
          $spare_pending_on = 'Partner';   
        }
        $row[] = $spare_pending_on;
        $row[] = $spare_list->name;
        $row[] = $spare_list->booking_primary_contact_no;
        $row[] = $spare_list->sc_name;
        $row[] = $spare_list->source;
        $row[] = $spare_list->quantity;
        $row[] = $spare_list->shipped_quantity;
        $row[] = "<span class='line_break'>" . $spare_list->parts_shipped . "</span>";
        $row[] = "<span class='line_break'>" . $spare_list->part_number . "</span>";
        $row[] = "<span class='line_break'>" . $spare_list->defective_part_shipped . "</span>";
        $row[] = "<span class='line_break'>" . $spare_list->shipped_part_number . "</span>";
        $row[] = $spare_list->request_type;
        $row[] = $spare_list->remarks_defective_part_by_partner;
        $row[] = !empty($spare_list->defactive_part_received_date_by_courier_api)?"Delivered":"In-Transit";
        $row[] = $spare_list->age_defective_part_shipped_date. " Days";
        
        if($this->session->userdata('user_group') == "inventory_manager" || $this->session->userdata('user_group') == "admin" || $this->session->userdata('user_group') == "developer" || $this->session->userdata('user_group') == "accountmanager"  ){  
            
            if($spare_list->defective_part_required == '0'){ $required_parts =  'REQUIRED_PARTS'; $text = '<i class="glyphicon glyphicon-ok-circle" style="font-size: 16px;"></i>'; $cl ="btn-primary";} else{ $text = '<i class="glyphicon glyphicon-ban-circle" style="font-size: 16px;"></i>'; $required_parts =  'NOT_REQUIRED_PARTS_FOR_COMPLETED_BOOKING'; $cl = "btn-danger"; }
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
        if($spare_list->is_micro_wh == 1){
         $spare_pending_on = 'Micro-warehouse';   
        }elseif ($spare_list->is_micro_wh == 2) {
            $wh_details = $this->vendor_model->getVendorContact($spare_list->partner_id);
            if(!empty($wh_details)){
               $spare_pending_on = $wh_details[0]['district'] . ' Warehouse';  
            }
        } else {
          $spare_pending_on = 'Partner';   
        }	

        $row[] = $spare_pending_on;
        $row[] = $spare_list->sc_name;
        $row[] = $spare_list->source;
        $row[] = "<span class='line_break'>". $spare_list->defective_part_shipped ."</span>";
        $row[] = "<span class='line_break'>". $spare_list->part_number ."</span>";
        $row[] = $spare_list->quantity;
        $row[] = $spare_list->shipped_quantity;
        $row[] = date("jS M, Y", strtotime($spare_list->defective_part_shipped_date));
        $row[] = $spare_list->consumed_status;
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


        if ($this->session->userdata('user_group') == "inventory_manager" || $this->session->userdata('user_group') == "admin" || $this->session->userdata('user_group') == "developer" || $this->session->userdata('user_group') == "accountmanager") {

            if ($spare_list->defective_part_required == '0') {
                $required_parts = 'REQUIRED_PARTS';
                $text = '<i class="glyphicon glyphicon-ok-circle" style="font-size: 16px;"></i>';
                $cl = "btn-primary";
            } else {
                $text = '<i class="glyphicon glyphicon-ban-circle" style="font-size: 16px;"></i>';
                $required_parts = 'NOT_REQUIRED_PARTS_FOR_COMPLETED_BOOKING';
                $cl = "btn-danger";
            }
            //$row[] = '<button type="button" data-booking_id="' . $spare_list->booking_id . '" data-url="' . base_url() . 'employee/inventory/update_action_on_spare_parts/' . $spare_list->id . '/' . $spare_list->booking_id . '/' . $required_parts . '" class="btn btn-sm ' . $cl . ' open-adminremarks" data-toggle="modal" data-target="#myModal2">' . $text . '</button>';
            $row[] = '<button type="button" data-button="Reject Courier" data-booking_id="' . $spare_list->booking_id . '"   data-url="' . base_url() . 'employee/inventory/update_action_on_spare_parts/' . $spare_list->id . '/' . $spare_list->booking_id . '/REJECT_COURIER_INVOICE" class="btn btn-warning btn-sm open-adminremarks" data-toggle="modal" data-target="#myModal2"> <i class="fa fa-truck" style="font-size:16px;"></i></button>';
            $row[] = '<button type="button" data-button="Approve Courier" data-charge="' . $spare_list->courier_charges_by_sf . '" data-booking_id="' . $spare_list->booking_id . '"   data-url="' . base_url() . 'employee/inventory/update_action_on_spare_parts/' . $spare_list->id . '/' . $spare_list->booking_id . '/APPROVE_COURIER_INVOICE" class="btn btn-primary btn-sm open-adminremarks" data-toggle="modal" data-target="#myModal2"><i class="glyphicon glyphicon-file" style="font-size:16px;"></i></button>';

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
        if($spare_list->is_micro_wh == 1){
         $spare_pending_on = 'Micro-warehouse';   
        }elseif ($spare_list->is_micro_wh == 2) {
            $wh_details = $this->vendor_model->getVendorContact($spare_list->partner_id);            
            if(!empty($wh_details)){
              $spare_pending_on = $wh_details[0]['district'] . ' Warehouse';   
            } else {
                $spare_pending_on = 'Warehouse';
            }
        } else {
          $spare_pending_on = 'Partner';   
        }
        $row[] = $spare_pending_on;
        $row[] = $spare_list->name;
        $row[] = $spare_list->booking_primary_contact_no;
        $row[] = $spare_list->sc_name;
        $row[] = $spare_list->source;
        $row[] = "<span class='line_break'>" . $spare_list->parts_shipped . "</span>";
        $row[] = $spare_list->quantity;
        $row[] = $spare_list->shipped_quantity;
        $row[] = $spare_list->challan_approx_value;
        $row[] = "<span class='line_break'>" . $spare_list->part_number . "</span>";
        $row[] = "<span class='line_break'>" . $spare_list->defective_part_shipped . "</span>";
        $row[] = "<span class='line_break'>" . $spare_list->shipped_part_number . "</span>";
        $row[] = $spare_list->request_type;
        $row[] = (empty($spare_list->age_defective_part_shipped_date))?'0 Days':$spare_list->age_defective_part_shipped_date." Days";
        $row[] = $spare_list->remarks_defective_part_by_sf;
        $row[] = $spare_list->remarks_defective_part_by_partner;
        $row[] = '<a href="'.S3_WEBSITE_URL.'misc-images/'.$spare_list->defective_courier_receipt.'" target="_blank">Click Here</a>';

        if($this->session->userdata('user_group') == "inventory_manager" || $this->session->userdata('user_group') == "admin" || $this->session->userdata('user_group') == "developer"  || $this->session->userdata('user_group') == "accountmanager" ){
            $row[] = '<button type="button" data-button="Mark Shipped"  data-booking_id="'.$spare_list->booking_id.'" data-url="'.base_url().'employee/inventory/update_action_on_spare_parts/'.$spare_list->id.'/'.$spare_list->booking_id.'/DEFECTIVE_PARTS_SHIPPED_BY_SF" class="btn btn-warning btn-sm open-adminremarks" data-toggle="modal" data-target="#myModal2"><i class="glyphicon glyphicon-send" style="font-size:16px;"></i></button>';
            if($spare_list->defective_part_required == '0'){ $required_parts =  'REQUIRED_PARTS'; $text = '<i class="glyphicon glyphicon-ok-circle" style="font-size: 16px;"></i>'; $cl ="btn-primary";} else{ $text = '<i class="glyphicon glyphicon-ban-circle" style="font-size: 16px;"></i>'; $required_parts =  'NOT_REQUIRED_PARTS_FOR_COMPLETED_BOOKING'; $cl = "btn-danger"; }
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
     function parts_delivered_to_sf_table_data($spare_list, $no) {
        $row = array();
        $row[] = $no;
        $row[] = '<a href="' . base_url() . 'employee/booking/viewdetails/' . $spare_list->booking_id . '" target= "_blank" >' . $spare_list->booking_id . '</a>';
        if ($spare_list->is_micro_wh == 1) {
            $spare_pending_on = 'Micro-warehouse';
        } elseif ($spare_list->is_micro_wh == 2) {
            $wh_details = $this->vendor_model->getVendorContact($spare_list->partner_id);
            if(!empty($wh_details)){
            $spare_pending_on = $wh_details[0]['district'] . ' Warehouse';
            }
        } else {
            $spare_pending_on = 'Partner';
        }
        $row[] = $spare_pending_on;
        $row[] = $spare_list->name;
        $row[] = $spare_list->booking_primary_contact_no;
        $row[] = $spare_list->sc_name;
        $row[] = $spare_list->source;
        $row[] = "<span class='line_break'>" . $spare_list->parts_requested . "</span>";
        $row[] = $spare_list->quantity;
        $row[] = $spare_list->shipped_quantity;
        $row[] = "<span class='line_break'>" . $spare_list->part_number . "</span>";
        $row[] = "<span class='line_break'>" . $spare_list->shipped_parts_type . "</span>";

        $row[] = "<span class='line_break'>" . $spare_list->parts_shipped . "</span>";
        $row[] = "<span class='line_break'>" . $spare_list->shipped_part_number . "</span>";
        $row[] = "<span class='line_break'>" . $spare_list->request_type . "</span>";
        $row[] = $this->miscelleneous->get_formatted_date($spare_list->shipped_date);
        $row[] = $this->miscelleneous->get_formatted_date($spare_list->acknowledge_date);
        $row[] = "<i class='fa fa-inr'></i> " . $spare_list->challan_approx_value;
        if ($this->input->post("status") == SPARE_DELIVERED_TO_SF) {
            $row[] = (empty($spare_list->age_of_delivered_to_sf)) ? '0 Days' : $spare_list->age_of_delivered_to_sf . " Days";
            ;
        } else {
            $row[] = (empty($spare_list->age_part_pending_to_sf)) ? '0 Days' : $spare_list->age_part_pending_to_sf . " Days";
        }

        if ($this->session->userdata('user_group') == "inventory_manager" || $this->session->userdata('user_group') == "admin" || $this->session->userdata('user_group') == "developer" || $this->session->userdata('user_group') == "accountmanager" ) {

            if ($spare_list->defective_part_required == '0') {
                $required_parts = 'REQUIRED_PARTS';
                $text = '<i class="glyphicon glyphicon-ok-circle" style="font-size: 16px;"></i>';
                $cl = "btn-primary";
            } else {
                $text = '<i class="glyphicon glyphicon-ban-circle" style="font-size: 16px;"></i>';
                $required_parts = 'NOT_REQUIRED_PARTS';
                $cl = "btn-danger";
            }
            $row[] = '<button type="button" data-booking_id="' . $spare_list->booking_id . '" data-url="' . base_url() . 'employee/inventory/update_action_on_spare_parts/' . $spare_list->id . '/' . $spare_list->booking_id . '/' . $required_parts . '" class="btn btn-sm ' . $cl . ' open-adminremarks" data-toggle="modal" data-target="#myModal2">' . $text . '</button>';
        } else {

            $row[] = "";
        }

        return $row;
    }
    
    
    /**
     * @desc this function is used to create table row data for the defective parts pending tab
     * @param Array $spare_list
     * @param int $no
     * @return Array
     */
    function defective_parts_pending_table_data($spare_list, $no){        
        $row = array();
        $sc_icon_style = "";
        $row[] = $no;
        $row[] = '<a href="'. base_url().'employee/booking/viewdetails/'.$spare_list->booking_id.'" target= "_blank" >'.$spare_list->booking_id.'</a>';
        	if($spare_list->is_micro_wh == 1){
         $spare_pending_on = 'Micro-warehouse';   
        }elseif ($spare_list->is_micro_wh == 2) {
            $wh_details = $this->vendor_model->getVendorContact($spare_list->partner_id);
            if(!empty($wh_details)){
              $spare_pending_on = $wh_details[0]['district'] . ' Warehouse';   
            }
             
        } else {
          $spare_pending_on = 'Partner';   
        }	
        
        if ($spare_list->active == 0) {
            $sc_icon_style = "color:#e10f0fd1;";
            $sf_status = "Permanently Off";
        } else if ($spare_list->on_off == 0) {
            $sc_icon_style = "color:#f1bc44;";
            $sf_status = "Temporary Off";
        } else {
            $sc_icon_style = "color:#14d914;";
            $sf_status = "On";
        }


        $row[] = $spare_pending_on; 
        $row[] = "<i class='fa fa-circle' aria-hidden='true' style='margin-right:5px;".$sc_icon_style."'></i>".$spare_list->sc_name."<p style='font-weight: bold;".$sc_icon_style."'> - ".$sf_status."</p>";
        $row[] = $spare_list->source;
        $row[] = $spare_list->parts_requested;
        $row[] = $spare_list->quantity;
        $row[] = $spare_list->shipped_quantity;
       // $row[] = "<span class='line_break'>" . $spare_list->parts_requested . "</span>";
        $row[] = "<span class='line_break'>" . $spare_list->part_number . "</span>";
        $row[] = "<span class='line_break'>" . $spare_list->shipped_parts_type . "</span>";
        $row[] = "<span class='line_break'>" . $spare_list->parts_shipped . "</spare>";
        $row[] = "<span class='line_break'>" . $spare_list->shipped_part_number . "</spare>";
        $row[] = "<span style='white-space:nowrap;'>" . $spare_list->consumed_status . "</spare>";
        $row[] = "<span class='line_break'>" . $spare_list->request_type . "</spare>";
        $row[] = $this->miscelleneous->get_formatted_date($spare_list->shipped_date);
        $row[] = $this->miscelleneous->get_formatted_date($spare_list->acknowledge_date);
        $row[] = "<i class='fa fa-inr'></i> ".$spare_list->challan_approx_value;
        if($this->input->post("status") == SPARE_DELIVERED_TO_SF){
            $row[] = (empty($spare_list->age_of_delivered_to_sf))?'0 Days':$spare_list->age_of_delivered_to_sf." Days";;
        } else {
            $row[] = (empty($spare_list->age_part_pending_to_sf))?'0 Days':$spare_list->age_part_pending_to_sf." Days";
        }
        
        if($spare_list->around_pickup_from_service_center == COURIER_PICKUP_REQUEST || $spare_list->around_pickup_from_service_center == COURIER_PICKUP_SCHEDULE){
            $row[] = 'Pickup Requested'; 
        } else {
            $row[] = '<input type="checkbox" class="form-control pickup_request" data-sf_id="'.$spare_list->service_center_id.'"  id="pickup_request_'.$no.'" onclick="uncheckedPickupScheduleCheckbox(this.id)" value="'.$spare_list->id.'" />';    
        }
        if($spare_list->around_pickup_from_service_center == COURIER_PICKUP_SCHEDULE){
            $row[] = 'Pickup Schedule'; 
        } else if( $spare_list->around_pickup_from_service_center == COURIER_PICKUP_REQUEST) {
            $row[] = '<input type="checkbox" class="form-control pickup_schedule" pickup_courier= "'.$spare_list->around_pickup_courier.'"  data-sf_id="'.$spare_list->service_center_id.'" id="pickup_schedule_'.$no.'" onclick="uncheckedPickupRequest(this.id)" value="'.$spare_list->id.'" />';
        } else {
            $row[] = '<i title="Not Allowed" style="font-size: 25px;color: #ca3030;" class="fa fa-ban" aria-hidden="true"></i>';
        }
        
        
        if($this->session->userdata('user_group') == "inventory_manager" || $this->session->userdata('user_group') == "admin" || $this->session->userdata('user_group') == "developer" || $this->session->userdata('user_group') == "accountmanager" ){
            
            if($spare_list->defective_part_required == '0'){ $required_parts =  'REQUIRED_PARTS'; $text = '<i class="glyphicon glyphicon-ok-circle" style="font-size: 16px;"></i>'; $cl ="btn-primary";} else{ $text = '<i class="glyphicon glyphicon-ban-circle" style="font-size: 16px;"></i>'; $required_parts =  'NOT_REQUIRED_PARTS_FOR_COMPLETED_BOOKING'; $cl = "btn-danger"; }
            $row[] = '<button type="button" data-booking_id="'.$spare_list->booking_id.'" data-url="'.base_url().'employee/inventory/update_action_on_spare_parts/'.$spare_list->id.'/'.$spare_list->booking_id.'/'.$required_parts.'" class="btn btn-sm '.$cl.' open-adminremarks" data-toggle="modal" data-target="#myModal2">'.$text.'</button>';
            $row[] = '<a href="'.base_url().'employee/spare_parts/defective_spare_invoice/'.$spare_list->booking_id.'" class="btn btn-sm btn-primary" style="margin-left:5px" target="_blank">Generate Invoice</a>';
        } else {
            $row[] = "";
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
        if($spare_list->is_micro_wh == 1){
         $spare_pending_on = 'Micro-warehouse';   
        }elseif ($spare_list->is_micro_wh == 2) {
            $wh_details = $this->vendor_model->getVendorContact($spare_list->partner_id);
            if(!empty($wh_details)){
            $spare_pending_on = $wh_details[0]['district'] . ' Warehouse'; 
            }
        } else {
          $spare_pending_on = 'Partner';   
        }
        $row[] = $spare_pending_on;
        $row[] = $spare_list->name;
        $row[] = $spare_list->booking_primary_contact_no;
        $row[] = $spare_list->sc_name;
        $row[] = $spare_list->source;
        $row[] = "<span class='line_break'>" . $spare_list->parts_requested . "</span>";
        $row[] = "<span class='line_break'>" . $spare_list->part_number . "</span>";
        $row[] = "<span class='line_break'>" . $spare_list->parts_requested_type . "</span>";
        $row[] = $spare_list->quantity;
        $row[] = $spare_list->shipped_quantity;
        $row[] = "<span class='line_break'>" . $spare_list->parts_shipped . "</span>";
        $row[] = "<span class='line_break'>" . $spare_list->shipped_part_number . "</span>";
        $row[] = $spare_list->request_type;
        $row[] = (empty($spare_list->age_of_shipped_date))?'0 Days':$spare_list->age_of_shipped_date." Days";
        if(!empty($spare_list->partner_challan_file)){
            $row[] = '<a href="'.S3_WEBSITE_URL.'vendor-partner-docs/'.$spare_list->partner_challan_file.' " target="_blank">Click Here to view</a>';
        } else {
            $row[] = "";
        }
        
        if($this->session->userdata('user_group') == "inventory_manager" || $this->session->userdata('user_group') == "admin" || $this->session->userdata('user_group') == "developer"  || $this->session->userdata('user_group') == "accountmanager" ){
            
            if($spare_list->defective_part_required == '0'){ $required_parts =  'REQUIRED_PARTS'; $text = '<i class="glyphicon glyphicon-ok-circle" style="font-size: 16px;"></i>'; $cl ="btn-primary";} else{ $text = '<i class="glyphicon glyphicon-ban-circle" style="font-size: 16px;"></i>'; $required_parts =  'NOT_REQUIRED_PARTS'; $cl = "btn-danger"; }
            $row[] = '<button type="button" data-booking_id="'.$spare_list->booking_id.'" data-url="'.base_url().'employee/inventory/update_action_on_spare_parts/'.$spare_list->id.'/'.$spare_list->booking_id.'/'.$required_parts.'" class="btn btn-sm '.$cl.' open-adminremarks" data-toggle="modal" data-target="#myModal2">'.$text.'</button>';
        } else {
            
            $row[] = "";
        }
        
        if($this->session->userdata('user_group') == "inventory_manager" || $this->session->userdata('user_group') == "admin" || $this->session->userdata('user_group') == "developer"){
            
            if($spare_list->spare_lost == '0'){ $required_parts = 'REQUIRED_PARTS'; $text = '<i class="glyphicon glyphicon-ok"></i>'; $cl ="btn-info"; $btn_status = "";} else{ $text = '<i class="glyphicon glyphicon-remove"></i>'; $btn_status = "disabled"; $required_parts =  'NOT_REQUIRED_PARTS'; $cl = "btn-danger"; }
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
        if($spare_list->is_micro_wh == 1){
         $spare_pending_on = 'Micro-warehouse';   
        }elseif ($spare_list->is_micro_wh == 2) {
            $wh_details = $this->vendor_model->getVendorContact($spare_list->partner_id);
            if(!empty($wh_details)){
            $spare_pending_on = $wh_details[0]['district'] . ' Warehouse'; 
            }
        } else {
          $spare_pending_on = 'Partner';   
        }
        $row[] = $spare_pending_on;
        $row[] = $spare_list->name;
        $row[] = $spare_list->booking_primary_contact_no;
        $row[] = $spare_list->sc_name;
        $row[] = $spare_list->source;
        $row[] = "<span class='line_break'>". $spare_list->parts_requested."</span>"; 
        $row[] = "<span class='line_break'>". $spare_list->part_number ."</span>";
        $row[] = "<span class='line_break'>". $spare_list->parts_requested_type ."</span>";
        $row[] = $spare_list->quantity;
        $row[] = "<span class='line_break'>".$spare_list->parts_shipped."</span>"; 
        $row[] = $spare_list->request_type;
        $row[] = $spare_list->purchase_price;
        $row[] = (empty($spare_list->age_of_shipped_date))?'0 Days':$spare_list->age_of_shipped_date." Days";
        $row[] = $spare_list->courier_price_by_partner;        
        if(!empty($spare_list->partner_challan_file)){
            $row[] = '<a href="'.S3_WEBSITE_URL.'vendor-partner-docs/'.$spare_list->partner_challan_file.' " target="_blank">Click Here to view</a>';
        } else {
            $row[] = "";
        }
        
        if($this->session->userdata('user_group') == "inventory_manager" || $this->session->userdata('user_group') == "admin" || $this->session->userdata('user_group') == "developer"  || $this->session->userdata('user_group') == "accountmanager" ){
            
            if($spare_list->defective_part_required == '0'){ $required_parts =  'REQUIRED_PARTS'; $text = '<i class="glyphicon glyphicon-ok-circle" style="font-size: 16px;"></i>'; $cl ="btn-primary";} else{ $text = '<i class="glyphicon glyphicon-ban-circle" style="font-size: 16px;"></i>'; $required_parts =  'NOT_REQUIRED_PARTS'; $cl = "btn-danger"; }
            $row[] = '<button type="button" data-booking_id="'.$spare_list->booking_id.'" data-url="'.base_url().'employee/inventory/update_action_on_spare_parts/'.$spare_list->id.'/'.$spare_list->booking_id.'/'.$required_parts.'" class="btn btn-sm '.$cl.' open-adminremarks" data-toggle="modal" data-target="#myModal2">'.$text.'</button>';
        } else {
            
            $row[] = "";
        }
        
        if(!empty($spare_list->sell_invoice_id)){
            $row[] = $spare_list->sell_invoice_id;
        } else {
            
            $row[] = '<a href="'.base_url().'employee/invoice/generate_oow_parts_invoice/'.$spare_list->id.'" id="btn_sell_invoice_'.$spare_list->id.'" onclick="disable_btn(this.id)"  class="btn btn-md btn-success">Generate Sale Invoice</a>';
        }
        
         if (!empty($spare_list->invoice_pdf)) {
           $invoice_pdf =  $spare_list->invoice_pdf;
        } else {
           $invoice_pdf =  $spare_list->incoming_invoice_pdf; 
        }

        if(!empty($invoice_pdf)){
            $row[] = '<a target="_blank" href="https://s3.amazonaws.com/'.BITBUCKET_DIRECTORY.'/invoices-excel/'.$invoice_pdf.'">
                            <img style="width:27px;" src="'.base_url().'images/invoice_icon.png"; /></a>';
        } else {
            $row[] = "";
        }
        $row[] = '<a href="'.S3_WEBSITE_URL.'misc-images/'.$spare_list->defective_parts_pic.'" target="_blank">Click Here</a>';
        $row[] = '<a href="'.S3_WEBSITE_URL.'misc-images/'.$spare_list->defective_back_parts_pic.'" target="_blank">Click Here</a>';
        $row[] = '<input id="'.$spare_list->id.'" type="checkbox" class="form-control spare_id" name="spare_id[]" data-booking_id="'.$spare_list->booking_id.'" data-partner_id = "'.$spare_list->booking_partner_id.'" value="'.$spare_list->id.'" />';
        return $row;
        
    }
      /**
     * @desc this function is used to create table row data for the spare parts requested tab
     * @param Array $spare_list
     * @param int $no
     * @return Array
     */
    
    
    
    function spare_parts_rejected_table_data($spare_list, $no, $request_type) {

        $row = array();
        $row[] = $no;
        $row[] = '<a href="' . base_url() . 'employee/booking/viewdetails/' . $spare_list->booking_id . '" target= "_blank" >' . $spare_list->booking_id . '</a>';
        if ($spare_list->is_micro_wh == 1) {
            $spare_pending_on = 'Micro-warehouse';
        } elseif ($spare_list->is_micro_wh == 2) {
            $wh_details = $this->vendor_model->getVendorContact($spare_list->partner_id);
            if(!empty($wh_details)){
            $spare_pending_on = $wh_details[0]['district'] . ' Warehouse';
            }
        } else {
            $spare_pending_on = 'Partner';
        }
        $row[] = $spare_pending_on;
        $row[] = $spare_list->name;
        $row[] = $spare_list->booking_primary_contact_no;
        $row[] = $spare_list->sc_name;
        $row[] = $spare_list->source;
        $row[] = '<center>'.$spare_list->state.'</center>';
        $row[] = "<span class='line_break'>" . $spare_list->model_number . "</span>";
        $row[] = "<span class='line_break'>" . $spare_list->parts_requested . "</span>";
        $row[] = "<span class='line_break'>" . $spare_list->part_number . "</span>";
        $row[] = $spare_list->quantity;
        $row[] = "<span class='line_break'>" . $spare_list->parts_requested_type . "</spare>";
        $row[] = $spare_list->request_type;
        if ($spare_list->part_warranty_status == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS) {
            $part_status_text = REPAIR_OOW_TAG;
        } else {
            $part_status_text = REPAIR_IN_WARRANTY_TAG;
        }
        $row[] = $part_status_text;
        $row[] = (empty($spare_list->spare_cancelled_date)) ? '0 Days' : $spare_list->spare_cancelled_date . " Days";
        $row[] = $spare_list->part_cancel_reason;
        $row[] = '<button class="btn btn-success open_spare_part"   data-bookingid="'.$spare_list->booking_id.'"   data-spareid="'.$spare_list->id.'">Open</button>';
        return $row;
    }

    function courier_lost_spare_parts_table_data($spare_list, $no, $request_type) {
        $row = array();
        $row[] = $no;
        $row[] = '<a href="' . base_url() . 'employee/booking/viewdetails/' . $spare_list->booking_id . '" target= "_blank" >' . $spare_list->booking_id . '</a>';
        if ($spare_list->is_micro_wh == 1) {
            $spare_pending_on = 'Micro-warehouse';
        } elseif ($spare_list->is_micro_wh == 2) {
            $wh_details = $this->vendor_model->getVendorContact($spare_list->partner_id);
            if(!empty($wh_details)){
            $spare_pending_on = $wh_details[0]['district'] . ' Warehouse';
            }
        } else {
            $spare_pending_on = 'Partner';
        }
        $row[] = $spare_pending_on;
        $row[] = $spare_list->name;
        $row[] = $spare_list->booking_primary_contact_no;
        $row[] = $spare_list->sc_name;
        $row[] = $spare_list->source;
        $row[] = '<center>'.$spare_list->state.'</center>';
        $row[] = "<span class='line_break'>" . $spare_list->model_number . "</span>";
        $row[] = "<span class='line_break'>" . $spare_list->parts_requested . "</span>";
        $row[] = "<span class='line_break'>" . $spare_list->part_number . "</span>";
        $row[] = "<span class='line_break'>" . $spare_list->parts_requested_type . "</spare>";
        $row[] = $spare_list->quantity;
        $row[] = $spare_list->parts_shipped;
        $row[] = $spare_list->shipped_quantity;
        $row[] = $spare_list->request_type;
        if ($spare_list->part_warranty_status == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS) {
            $part_status_text = REPAIR_OOW_TAG;
        } else {
            $part_status_text = REPAIR_IN_WARRANTY_TAG;
        }
        $row[] = $part_status_text;
        $row[] = (empty($spare_list->age_of_request)) ? '0 Days' : $spare_list->age_of_request . " Days";
        
        $row[] = '<a class="btn btn-success btn-sm approve-courier-lost-part" href="javascript:void(0);" onclick="approve_courier_lost_spare('.$spare_list->id.');"><span class="glyphicon glyphicon-ok"></span></a>';
        $row[] = '<a class="btn btn-danger btn-sm reject-courier-lost-part" style="margin-top:2px;" href="javascript:void(0);" onclick="reject_courier_lost_spare('.$spare_list->id.');"><span class="glyphicon glyphicon-remove"></span></a>';
        return $row;
    }
    
    /**
     * Function approves courier lost data from spare parts bookings.
     * @author Ankit Rajvanshi  
     */
    function approve_courier_lost_spare() {
        
        $post_data = $this->input->post();
        $spare_part_detail = $this->partner_model->get_spare_parts_by_any("spare_parts_details.*", array('spare_parts_details.id' => $post_data['courier_lost_spare_id']), true, false)[0];
        // update spare part details.
        $spare_data = [
            'status' => _247AROUND_COMPLETED,
            'defective_part_required' => 0,
            'spare_lost' => 1
        ];
        $this->service_centers_model->update_spare_parts(array('id' => $post_data['courier_lost_spare_id']), $spare_data);
        // update booking status
        $this->booking_model->update_booking($booking_id, ['internal_status' => COURIER_LOST_APPROVED_STATUS]);
        // state change entry.

        /* Insert Spare Tracking Details */
        if (!empty($post_data['courier_lost_spare_id'])) {
            $tracking_details = array('spare_id' => $post_data['courier_lost_spare_id'], 'action' => _247AROUND_COMPLETED, 'remarks' => $post_data['remarks']." ".COURIER_LOST_APPROVED_STATUS, 'agent_id' =>  $this->session->userdata('id'), 'entity_id' => _247AROUND, 'entity_type' => _247AROUND_EMPLOYEE_STRING);
            $this->service_centers_model->insert_spare_tracking_details($tracking_details);
        }
        $this->notify->insert_state_change($spare_part_detail['booking_id'], COURIER_LOST_APPROVED_STATUS, $spare_part_detail['status'], $post_data['remarks'], $this->session->userdata('id'), $this->session->userdata('employee_id'), '', '', NULL, $spare_part_detail['partner_id'], $post_data['courier_lost_spare_id']);

        // add courier lost spare entry with approve status.
        $data = [
            'spare_id' => $post_data['courier_lost_spare_id'],
            'remarks' => $post_data['remarks'],
            'status' => 1, // approve
            'agent_id' => $this->session->userdata['id']
        ];
        $this->service_centers_model->insert_courier_lost_spare_status($data);

        // check part pending to be shipped.
        $check_spare_part_pending = $this->partner_model->get_spare_parts_by_any("spare_parts_details.*", array("spare_parts_details.status IN ('" . OK_PART_TO_BE_SHIPPED . "','" . DAMAGE_PART_TO_BE_SHIPPED . "','" . DEFECTIVE_PARTS_PENDING . "','" . OK_PARTS_SHIPPED . "','" . DAMAGE_PARTS_SHIPPED . "','" . DEFECTIVE_PARTS_SHIPPED . "','".COURIER_LOST."')" => NULL, 'spare_parts_details.booking_id' => $spare_part_detail['booking_id']), true, false);
        if (empty($check_spare_part_pending)) {
            // update service center booking action.
            $this->vendor_model->update_service_center_action($spare_part_detail['booking_id'], ['current_status' => SF_BOOKING_INPROCESS_STATUS, 'internal_status' => _247AROUND_COMPLETED]);
            // update booking.
            $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, SF_BOOKING_COMPLETE_STATUS, $spare_part_detail['partner_id'], $spare_part_detail['booking_id']);
            $booking_detail_data = [];
            if (!empty($partner_status)) {
                $booking_detail_data['partner_current_status'] = $partner_status[0];
                $booking_detail_data['partner_internal_status'] = $partner_status[1];
                $booking_detail_data['actor'] = $partner_status[2];
                $booking_detail_data['next_action'] = $partner_status[3];
            }
            
            $booking_detail_data['current_status'] = _247AROUND_PENDING;
            $booking_detail_data['internal_status'] = SF_BOOKING_COMPLETE_STATUS;
            $this->booking_model->update_booking($spare_part_detail['booking_id'], $booking_detail_data);
        }
        
        return true;
    }

    /**
     * Function approves courier lost data from spare parts bookings.
     * @author Ankit Rajvanshi  
     */
    function reject_courier_lost_spare() {
        $post_data = $this->input->post();
        $spare_id = $data['spare_id'] = $post_data['spare_id'];

        if(!empty($post_data['reject'])) {
            $spare_part_detail = $this->partner_model->get_spare_parts_by_any("spare_parts_details.*", array('spare_parts_details.id' => $post_data['spare_id']), true, false)[0];
            // update spare part details.
            $spare_data = [
                'status' => OK_PART_TO_BE_SHIPPED,
                'consumed_part_status_id' => OK_PART_BUT_NOT_USED_CONSUMPTION_STATUS_ID,
                'defective_part_required' => 1
            ];
            $this->service_centers_model->update_spare_parts(array('id' => $post_data['spare_id']), $spare_data) ;
        
            /* Insert Spare Tracking Details */
            if (!empty($spare_id)) {
                $tracking_details = array('spare_id' => $spare_id, 'action' => OK_PART_TO_BE_SHIPPED, 'remarks' => $post_data['reject_courier_lost_spare_part_remarks'], 'agent_id' => $this->session->userdata('id'), 'entity_id' => _247AROUND, 'entity_type' => _247AROUND_EMPLOYEE_STRING);
                $this->service_centers_model->insert_spare_tracking_details($tracking_details);
            }
            $this->notify->insert_state_change($spare_part_detail['booking_id'], OK_PART_TO_BE_SHIPPED, $spare_part_detail['status'], $post_data['reject_courier_lost_spare_part_remarks'], $this->session->userdata('id'), $this->session->userdata('employee_id'), '', '', NULL, $spare_part_detail['partner_id'], $spare_id);

            $courier_spare_lost_file_name = '';
            if(!empty($_FILES['reject_courier_lost_spare_part_pod'])) {
                $courier_spare_lost_file_name = $this->upload_courier_spare_lost_pod($spare_part_detail['booking_id'], $_FILES['reject_courier_lost_spare_part_pod']['tmp_name'], ' ', $_FILES['reject_courier_lost_spare_part_pod']['name']);
            }
            
            // add courier lost spare entry with reject status.
            $data = [
                'spare_id' => $post_data['spare_id'],
                'remarks' => $post_data['reject_courier_lost_spare_part_remarks'],
                'status' => 2, // reject status
                'agent_id' => $this->session->userdata['id'],
                'pod' => (!empty($courier_spare_lost_file_name) ? $courier_spare_lost_file_name : NULL)
            ];
            
            $this->service_centers_model->insert_courier_lost_spare_status($data);
            
            return true;
        }
        
        $this->load->view('service_centers/reject_courier_lost_spare', $data);
    }

    /**
     *  @desc : This function is used to upload the purchase invoice to s3 and save into database
     *  @param : string $booking_primary_contact_no
     *  @return : boolean/string
     */
    function upload_courier_spare_lost_pod($booking_id, $tmp_name, $error, $name) {

        $support_file_name = false;

        if (($error != 4) && !empty($tmp_name)) {

            $tmpFile = $tmp_name;
            $support_file_name = $booking_id . '_courier_spare_lost_pod_' . substr(md5(uniqid(rand(0, 9))), 0, 15) . "." . explode(".", $name)[1];
            //move_uploaded_file($tmpFile, TMP_FOLDER . $support_file_name);
            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "courier-pod/" . $support_file_name;
            $upload_file_status = $this->s3->putObjectFile($tmpFile, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);

            if($upload_file_status){
                //Logging success for file uppload
                log_message('info', __METHOD__ . 'Courier spare lost pod has been uploaded sucessfully for booking_id: '.$booking_id);
                return $support_file_name;
            }else{
                //Logging success for file uppload
                log_message('info', __METHOD__ . 'Error In uploading courier spare lost pod file for booking_id: '.$booking_id);
                return False;
            }

        }
        
    }        

    
    /**
     * @desc this function is used to create table row data for the spare parts ron approval
     * @param Array $spare_list
     * @param int $no
     * @return Array
     */
    function spare_parts_onapproval_table_data($spare_list, $no, $request_type, $arr_warranty_status = []) {
        $row = array();
        $row[] = $no;
        $row[] = '<a href="' . base_url() . 'employee/booking/viewdetails/' . $spare_list->booking_id . '" target= "_blank" >' . $spare_list->booking_id . '</a>';
        if($spare_list->is_micro_wh == 1){
         $spare_pending_on = 'Micro-warehouse';   
        }elseif ($spare_list->is_micro_wh == 2) {
            $wh_details = $this->vendor_model->getVendorContact($spare_list->partner_id);
            if(!empty($wh_details)){
            $spare_pending_on = $wh_details[0]['district'] . ' Warehouse'; 
            }
        } else {
          $spare_pending_on = 'Partner';   
        }
        $row[] = $spare_pending_on;
        $row[] = $spare_list->name;
        $row[] = $spare_list->booking_primary_contact_no;
        $row[] = "<span class='line_break'>".$spare_list->sc_name."</span>";
        $row[] = $spare_list->source;
        $row[] = "<span class='line_break'>". $spare_list->model_number ."</span>";
        $row[] = "<span class='line_break'>". $spare_list->parts_requested ."</span>";
        $row[] = "<span class='line_break'>". $spare_list->part_number ."</span>";
        $row[] = "<span class='line_break'>". $spare_list->parts_requested_type ."</spare>";
         $row[] = $spare_list->quantity;
        $row[] = $spare_list->request_type;
        if( $spare_list->part_warranty_status == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS ){ $part_status_text = REPAIR_OOW_TAG;   }else{ $part_status_text = REPAIR_IN_WARRANTY_TAG; }
        $row[] =  $part_status_text; 
        $row[] =  '<div class="warranty-'.$spare_list->booking_id.' warranty-status"><i class="fa fa-spinner warranty-loader" aria-hidden="true"></i></div>'; 
        if($request_type == _247AROUND_CANCELLED){
          $row[] = (empty($spare_list->spare_cancelled_date)) ? '0 Days' : $spare_list->spare_cancelled_date . " Days";  
        }else{
        $row[] = (empty($spare_list->age_of_request)) ? '0 Days' : $spare_list->age_of_request . " Days";
        }  
        
        if ($spare_list->defective_part_required == '0') {
            $required_parts = 'REQUIRED_PARTS';
            $text = '<i class="glyphicon glyphicon-ok-circle" style="font-size: 16px;"></i>';
            $cl = "btn-primary";
        } else {
            $text = '<i class="glyphicon glyphicon-ban-circle" style="font-size: 16px;"></i>';
            $required_parts = 'NOT_REQUIRED_PARTS';
            $cl = "btn-danger";
        }

//        if ($request_type !=SPARE_PARTS_REQUESTED || $request_type != _247AROUND_CANCELLED) {
//            $row[] = '<button type="button" data-booking_id="' . $spare_list->booking_id . '" data-url="' . base_url() . 'employee/inventory/update_action_on_spare_parts/' . $spare_list->id . '/' . $spare_list->booking_id . '/' . $required_parts . '" class="btn btn-sm ' . $cl . ' open-adminremarks" data-toggle="modal" data-target="#myModal2">' . $text . '</button>';
//        }
        
        
        if ($this->session->userdata('user_group') == 'admin'  || $this->session->userdata('user_group') == 'inventory_manager' || $this->session->userdata('user_group') == 'developer' ) {
            if ($request_type == SPARE_PARTS_REQUESTED || $request_type == SPARE_PART_ON_APPROVAL|| $request_type==_247AROUND_CANCELLED ) {
                if ($spare_list->part_requested_on_approval == '0' && $spare_list->status == SPARE_PART_ON_APPROVAL) {
                    $appvl_text = '<i class="glyphicon glyphicon-ok-sign" style="font-size: 16px;"></i>';
                    $cl = " btn-success";
                    $row[] = '<a type="button"  class="btn btn-info" href="' . base_url() . 'employee/booking/get_edit_booking_form/' . $spare_list->booking_id . '" target="_blank"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    $row[] = '<button type="button" data-keys="' . $spare_list->part_warranty_status . '" data-booking_id="' . $spare_list->booking_id . '" data-url="' . base_url() . 'employee/spare_parts/spare_part_on_approval/' . $spare_list->id . '/' . $spare_list->booking_id . '" data-request_type="' . $spare_list->request_type . '" class="btn' . $cl . ' open-adminremarks" data-toggle="modal" id="approval_' . $no . '" data-target="#myModal2">' . $appvl_text . '</button>';
                } else {
                    $row[] = '<a  class="btn btn-primary" href="" disabled><i class="fa fa-edit" aria-hidden="true"></button>';
                    $row[] = '<a  class="btn btn-success" href="" disabled><i class="glyphicon glyphicon-ok-sign" aria-hidden="true"></button>';
                }
            }
            
            if ($spare_list->part_requested_on_approval == '0' && $spare_list->status == SPARE_PART_ON_APPROVAL) {
                $row[] = '<a  class="btn btn-primary" href="' . base_url() . 'employee/spare_parts/update_spare_parts_on_approval/' . urlencode(base64_encode($spare_list->id)) . '" target="_blank"><i class="fa fa-edit" aria-hidden="true"></i></a>';
            } else {
                $row[] = '<a  class="btn btn-primary" href="" disabled><i class="fa fa-edit" aria-hidden="true"></button>';
            }
        }
        
        $c_tag = ($spare_list->part_warranty_status == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS && $spare_list->status != SPARE_PARTS_REQUESTED) ? "QUOTE_REQUEST_REJECTED" : "CANCEL_PARTS";
        $row[] = '<button type="button" data-keys="spare_parts_cancel" data-booking_id="' . $spare_list->booking_id . '" data-url="' . base_url() . 'employee/inventory/update_action_on_spare_parts/' . $spare_list->id . '/' . $spare_list->booking_id . '/' . $c_tag . '" class="btn btn-danger btn-sm open-adminremarks" data-toggle="modal" data-target="#myModal2"><i class="glyphicon glyphicon-remove-sign" style="font-size: 17px;"></i></button>';
        return $row;
    }



    /**
     * @desc this function is used to create table row data for the spare parts requested tab
     * @param Array $spare_list
     * @param int $no
     * @return Array
     */
 
    function spare_parts_requested_table_data($spare_list, $no, $request_type,$approved) {
        $row = array();
        $row[] = $no;
        $row[] = '<a href="' . base_url() . 'employee/booking/viewdetails/' . $spare_list->booking_id . '" target= "_blank" >' . $spare_list->booking_id . '</a>';
        if($spare_list->is_micro_wh == 1){
         $spare_pending_on = 'Micro-warehouse';   
        }elseif ($spare_list->is_micro_wh == 2) {
            $wh_details = $this->vendor_model->getVendorContact($spare_list->partner_id);
            if(!empty($wh_details)){
              $spare_pending_on = $wh_details[0]['district'] . ' Warehouse';    
            }else{
              $spare_pending_on =' Warehouse';  
            }
            
        } else {
          $spare_pending_on = 'Partner';   
        }
        $row[] = $spare_pending_on;
        $row[] = $spare_list->name;
        $row[] = $spare_list->booking_primary_contact_no;
        $row[] = "<span class='line_break'>".$spare_list->sc_name."</span>";
        $row[] = $spare_list->source;
        $row[] = "<span class='line_break'>". $spare_list->model_number ."</span>";
        $row[] = "<span class='line_break'>". $spare_list->parts_requested ."</span>";
        $row[] = "<span class='line_break'>". $spare_list->part_number ."</span>";
        $row[] = "<span class='line_break'>". $spare_list->parts_requested_type ."</spare>";
        $row[] = $spare_list->quantity;
        $row[] = $spare_list->request_type;

        if ($spare_list->part_warranty_status == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS) {
            $part_status_text = REPAIR_OOW_TAG;
        } else {
            $part_status_text = REPAIR_IN_WARRANTY_TAG;
        }
        $row[] = $part_status_text;
        /* Approval Date and agent name */
        if($approved){
        $row[] = (empty($spare_list->spare_approval_date) || $spare_list->spare_approval_date=='0000-00-00') ? 'NA' : date_format(date_create($spare_list->spare_approval_date),'d-m-Y');
        if($spare_list->approval_entity_type == _247AROUND_EMPLOYEE_STRING){
            $row[] = (empty($spare_list->full_name)) ? 'NA' : $spare_list->full_name;
        }else{
            $row[] = (empty($spare_list->agent_name)) ? 'NA' : $spare_list->agent_name;
        }
        }

        $row[] = (empty($spare_list->age_of_request)) ? '0 Days' : $spare_list->age_of_request . " Days";


        $row[] = '<a  class="btn btn-primary" href="' . base_url() . 'employee/spare_parts/update_spare_parts_on_approval/' . urlencode(base64_encode($spare_list->id)) . '" target="_blank"><i class="fa fa-edit" aria-hidden="true"></i></a>';

        if ($spare_list->defective_part_required == '0') {
            $required_parts = 'REQUIRED_PARTS';
            $text = '<i class="glyphicon glyphicon-ok-circle" style="font-size: 16px;"></i>';
            $cl = "btn-primary";
        } else {
            $text = '<i class="glyphicon glyphicon-ban-circle" style="font-size: 16px;"></i>';
            $required_parts = 'NOT_REQUIRED_PARTS';
            $cl = "btn-danger";
        }
        $row[] = '<button type="button" data-booking_id="' . $spare_list->booking_id . '" data-url="' . base_url() . 'employee/inventory/update_action_on_spare_parts/' . $spare_list->id . '/' . $spare_list->booking_id . '/' . $required_parts . '" class="btn btn-sm ' . $cl . ' open-adminremarks" data-toggle="modal" data-target="#myModal2">' . $text . '</button>';
        
            
        $c_tag = ($spare_list->part_warranty_status == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS && $spare_list->status != SPARE_PARTS_REQUESTED) ? "QUOTE_REQUEST_REJECTED" : "CANCEL_PARTS";
        $row[] = '<button type="button" data-keys="spare_parts_cancel" data-booking_id="' . $spare_list->booking_id . '" data-url="' . base_url() . 'employee/inventory/update_action_on_spare_parts/' . $spare_list->id . '/' . $spare_list->booking_id . '/' . $c_tag . '" class="btn btn-danger btn-sm open-adminremarks" data-toggle="modal" data-target="#myModal2"><i class="glyphicon glyphicon-remove-sign" style="font-size: 17px;"></i></button>';
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
        
        $post['draw'] = $this->input->post('draw');
        $post['type'] = $this->input->post('type');
        $post['order'] = $this->input->post('order');
        $post['is_inventory'] = 1;        
        
        
        if($this->input->post("status") == SPARE_PARTS_REQUESTED){
            $post['where_in']['status'] = array(SPARE_PARTS_REQUESTED);
            $post['request_type'] = SPARE_PARTS_REQUESTED;  

        }else if($this->input->post("status") == SPARE_PART_ON_APPROVAL){
            $post['where_in']['status'] = array(SPARE_PART_ON_APPROVAL);
            $post['request_type'] = SPARE_PART_ON_APPROVAL; 
        }else if($this->input->post("status") == _247AROUND_CANCELLED){
            $post['where']['status'] = $this->input->post("status");  
            $post['request_type'] = _247AROUND_CANCELLED;  
        }else{         
            $post['where']['status'] = $this->input->post("status");  
            $post['request_type'] = SPARE_OOW_EST_REQUESTED; 
        }
        
        
        if(!empty($this->input->post('vendor_partner'))){
            $post['vendor_partner'] = $this->input->post('vendor_partner');
        }else{
           $sf = $this->vendor_model->get_employee_relation($this->session->userdata("id")); 
           $post['where']['status'] = $this->input->post("status"); 
        }
        $vendor_id = array();
        if(!empty($sf)){
            $vendor_id = explode(",", $sf[0]["service_centres_id"]);
            $post['where_in'] = array('service_center_id' => $vendor_id);
        }
        if(!empty($this->input->post('partner_id'))){
            $post['where']['booking_details.partner_id'] = $this->input->post('partner_id');
        }
        
        if( isset($post['where']['status']) && $post['where']['status'] ==  DEFECTIVE_PARTS_SHIPPED){
            unset($post['where']['status']);
            $post['where_in']['status'] = [DEFECTIVE_PARTS_SHIPPED, DAMAGE_PARTS_SHIPPED, OK_PARTS_SHIPPED];
            $post['where']['approved_defective_parts_by_admin'] = $this->input->post('approved_defective_parts_by_admin');
        }
        
        if(!empty($this->input->post('partner_wise_parts_requested'))){
            $post['where']['booking_details.partner_id'] = $this->input->post('partner_wise_parts_requested');
        }

        if (!empty($this->input->post('appliance_wise_parts_requested'))) {
            $post['where']['booking_details.service_id'] = $this->input->post('appliance_wise_parts_requested');
        }
        /*@desc:  Where Clause To Return Defective part From WH To Partner */
        if (!empty($this->input->post('awb_by_wh'))) {
            unset($post['where']['status']);
            unset($post['request_type']);
            $post['where']['spare_parts_details.awb_by_wh '.$this->input->post('awb_by_wh').' AND spare_parts_details.defective_parts_shippped_date_by_wh '.$this->input->post('defective_parts_shippped_date_by_wh').''] = NULL ;
        }
        /*  Set for approved tab */
        if(!empty($this->input->post('approved'))){
            $post['approved'] = $this->input->post('approved'); 
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
        ob_clean();
        $spare_id = $spare_parts_id = $this->input->post('spare_parts_id');
        $partner_id = $this->input->post('booking_partner_id');
        $entity_type = $this->input->post('entity_type');
        $booking_id = $this->input->post('booking_id');
        $state = $this->input->post('state');
        $service_center_id = $this->input->post('service_center_id');
        $requested_inventory = $this->input->post('requested_spare_id');
        $warehouse_id = $this->input->post('warehouse_id');
        $quantity = $this->input->post('quantity'); // // Quantity from DB
        $where = array('id' => $spare_parts_id);
        $row = "";
        if ($entity_type == _247AROUND_PARTNER_STRING) {
            $new_state = REQUESTED_SPARED_REMAP;
            $data['entity_type'] = $entity_type;
            $data['partner_id'] = $partner_id;
            $data['defective_return_to_entity_type'] = _247AROUND_PARTNER_STRING;
            $data['defective_return_to_entity_id'] = $partner_id;
            $data['is_micro_wh'] = 0;
            $data['quantity'] = $quantity; // Quantity
            //  $data['remarks'] = "Spare Transfer to Partner";
            $row = $this->service_centers_model->update_spare_parts($where, $data);
            if ($row) {
                if ($this->session->userdata('userType') == 'employee') {
                    $new_state = 'Spare Part Transferred to ' . $partner_id;
                    /* Insert Spare Tracking Details */
                    if (!empty($spare_id)) {
                        $tracking_details = array('spare_id' => $spare_id, 'action' => $new_state, 'remarks' => PARTNER_WILL_SEND_NEW_PARTS, 'agent_id' => $this->session->userdata('id'), 'entity_id' => _247AROUND, 'entity_type' => _247AROUND_EMPLOYEE_STRING);
                        $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                    }
                    $this->notify->insert_state_change($booking_id, $new_state, '', PARTNER_WILL_SEND_NEW_PARTS, $this->session->userdata('id'), $this->session->userdata('employee_id'), '', '', NULL, $partner_id, $spare_id);
                    $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $warehouse_id, $requested_inventory, -1);
                    echo 'success';
                } else {
                    /* Insert Spare Tracking Details */
                    if (!empty($spare_id)) {
                        $tracking_details = array('spare_id' => $spare_id, 'action' => $new_state, 'remarks' => PARTNER_WILL_SEND_NEW_PARTS, 'agent_id' => $this->session->userdata('service_center_agent_id'), 'entity_id' => $this->session->userdata('service_center_id'), 'entity_type' => _247AROUND_SF_STRING);
                        $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                    }
                    $this->notify->insert_state_change($booking_id, $new_state, '', PARTNER_WILL_SEND_NEW_PARTS, $this->session->userdata('service_center_id'), $this->session->userdata('service_center_name'), '', '', NULL, $partner_id, $spare_id);
                    $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $warehouse_id, $requested_inventory, -1);
                    echo 'success';
                }
            } else {
                echo 'fail';
            }
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
        $status = $this->input->post('status');
        $spare_update_flag =$this->input->post('spare_update');
        
        if(!empty($this->input->post('open_remark'))){
            $reason = 'Spare parts reopened By ' . $this->session->userdata('employee_id').' Reason : '.$this->input->post('open_remark'); 
        }else{
           $reason = 'Spare parts Copy By ' . $this->session->userdata('employee_id');
        }
                
$select = 'spare_parts_details.entity_type,spare_parts_details.quantity,spare_parts_details.quantity,spare_parts_details.booking_id,spare_parts_details.status,spare_parts_details.partner_id,spare_parts_details.date_of_request,'
                . ', spare_parts_details.service_center_id, spare_parts_details.model_number, spare_parts_details.serial_number,'
                . ' spare_parts_details.date_of_purchase, spare_parts_details.invoice_gst_rate, spare_parts_details.parts_requested, spare_parts_details.parts_requested_type, spare_parts_details.invoice_pic,'
                . ' spare_parts_details.defective_parts_pic, spare_parts_details.defective_back_parts_pic, spare_parts_details.serial_number_pic, spare_parts_details.requested_inventory_id, spare_parts_details.is_micro_wh,'
                . 'spare_parts_details.part_warranty_status,booking_details.partner_id as booking_partner_id,booking_details.service_id,booking_details.assigned_vendor_id';

                $b_select = "*";
                $new_booking =  $this->input->post('new_booking_id');
                $b_where = array('booking_id'=>$new_booking);
                $response_booking = $this->booking_model->get_booking_details($b_select,$b_where);
                
                if (!empty($response_booking)) {
                    if (!empty($response_booking[0]['service_center_closed_date'])) {
                         //print_r($response_booking);  exit;
                        echo 'fail_close';
                        exit;
                    }

                }

        if (!empty($spare_parts_id)) {

            $spare_parts_list = $this->partner_model->get_spare_parts_by_any($select, array('spare_parts_details.id' => $spare_parts_id), true, false);

                if (($spare_parts_list[0]['booking_partner_id']!=$response_booking[0]['partner_id']) || ($spare_parts_list[0]['assigned_vendor_id']!=$response_booking[0]['assigned_vendor_id'])) {
                        echo 'fail_close';
                        exit;
                }

            if (!empty($spare_parts_list)) {
                $new_booking_id = $this->input->post('new_booking_id');

                $spare_parts_list[0]['date_of_request'] = date('Y-m-d');
                $spare_parts_list[0]['booking_id'] = $this->input->post('new_booking_id');
                $booking_id = $this->input->post('new_booking_id');

                $spare_parts_list[0]['status'] = $status;
                $entity_type = $spare_parts_list[0]['entity_type'];
                $inventory_id = $spare_parts_list[0]['requested_inventory_id'];
                $partner_id = $spare_parts_list[0]['booking_partner_id'];
                $req_quantity = $spare_parts_list[0]['quantity'];

                if (!empty($spare_parts_list[0]) && !$spare_update_flag) {
                    unset($spare_parts_list[0]['booking_partner_id']);
                    unset($spare_parts_list[0]['service_id']);
                    $insert_id = $this->service_centers_model->insert_data_into_spare_parts($spare_parts_list[0]);
                    $spare_id = $spare_parts_id = $insert_id;
                } else if (!empty($this->input->post('spare_update'))) {
                    $spare_id = $spare_parts_id = $spare_parts_id;
                }

                $parts_stock_not_found = array();
                $delivered_sp = array();
                $partner_details = $this->partner_model->getpartner_details("is_def_spare_required,is_wh, is_defective_part_return_wh", array('partners.id' => $partner_id));

                $sf_state = $this->vendor_model->getVendorDetails("service_centres.state", array('service_centres.id' => $spare_parts_list[0]['service_center_id']));

                $is_warehouse = false;
                if (!empty($partner_details[0]['is_wh'])) {

                    $is_warehouse = TRUE;
                } else if (!empty($partner_details[0]['is_micro_wh'])) {
                    $is_warehouse = TRUE;
                }
                if (!empty($is_warehouse)) {

                    $warehouse_details = $this->get_warehouse_details(array('inventory_id' => $inventory_id, 'state' => $sf_state[0]['state'], 'service_center_id' => $spare_parts_list[0]['service_center_id'],'model_number' => $spare_parts_list[0]['model_number']), $partner_id);

                    if (!empty($warehouse_details) && $warehouse_details['stock']>=$req_quantity) {
                        $data['partner_id'] = $warehouse_details['entity_id'];
                        $data['entity_type'] = $warehouse_details['entity_type'];
                        $data['quantity'] = $req_quantity;
                        $data['defective_return_to_entity_type'] = $warehouse_details['defective_return_to_entity_type'];
                        $data['defective_return_to_entity_id'] = $warehouse_details['defective_return_to_entity_id'];
                        $data['is_micro_wh'] = $warehouse_details['is_micro_wh'];
                        $data['challan_approx_value'] = round($warehouse_details['challan_approx_value']*$req_quantity,2);
                        $data['invoice_gst_rate'] = $warehouse_details['gst_rate'];
                        $data['parts_requested'] = $warehouse_details['part_name'];
                        $data['parts_requested_type'] = $warehouse_details['type'];
                        if (!empty($warehouse_details['inventory_id'])) {
                            $data['requested_inventory_id'] = $warehouse_details['inventory_id'];
                        }

                        if ($warehouse_details['entity_type'] == _247AROUND_PARTNER_STRING) {
                            array_push($parts_stock_not_found, array('model_number' => $spare_parts_list[0]['model_number'], 'part_type' => $spare_parts_list[0]['parts_requested_type'], 'part_name' => $spare_parts_list[0]['parts_requested']));

                        }
                    } else {
                        $data['partner_id'] = $partner_id;
                        $data['entity_type'] = _247AROUND_PARTNER_STRING;
                        $data['is_micro_wh'] = 0;
                        if (isset($warehouse_details['challan_approx_value'])) {
                        $data['challan_approx_value'] = round($warehouse_details['challan_approx_value']*$req_quantity,2);   
                        }
                        $data['defective_return_to_entity_type'] = _247AROUND_PARTNER_STRING;
                        $data['defective_return_to_entity_id'] = $partner_id;
                        array_push($parts_stock_not_found, array('model_number' => $spare_parts_list[0]['model_number'], 'part_type' => $spare_parts_list[0]['parts_requested_type'], 'part_name' => $spare_parts_list[0]['parts_requested']));
                    }
                } else {
                    $data['partner_id'] = $partner_id;
                    $data['entity_type'] = _247AROUND_PARTNER_STRING;
                    $data['is_micro_wh'] = 0;
                    $data['defective_return_to_entity_type'] = _247AROUND_PARTNER_STRING;
                    $data['defective_return_to_entity_id'] = $partner_id;
                }

                if (!empty($parts_stock_not_found)) {
                    $this->send_out_of_stock_mail($parts_stock_not_found, $partner_id, $data,$booking_id);
                }

                if ($spare_update_flag) {
                    $data['status'] = SPARE_PARTS_REQUESTED;
                }

                if ($data['is_micro_wh']==1 || $data['is_micro_wh']==2){
                     $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $data['partner_id'], $data['requested_inventory_id'],$req_quantity);
                }
               
                if (!empty($spare_parts_id)) {

                    $affected_id = $this->service_centers_model->update_spare_parts(array('id' => $spare_parts_id), $data);
                }

                if ($affected_id) {
                    if (isset($data['is_micro_wh']) && $data['is_micro_wh'] == 1 ) {
                        $data['spare_id'] = $spare_parts_id;
                         $data['shipped_inventory_id'] = $spare_data['requested_inventory_id'];
                        array_push($delivered_sp, $data);
                    }

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
                    $this->miscelleneous->send_spare_requested_sms_to_customer($spare_parts_list[0]['parts_requested'], $this->input->post('new_booking_id'), SPARE_REQUESTED_CUSTOMER_SMS_TAG);

                    
                    /* Insert Spare Tracking Details */
                    if (!empty($spare_id)) {
                        $tracking_details = array('spare_id' => $spare_id, 'action' => SPARE_PARTS_REQUESTED, 'remarks' => $reason, 'agent_id' => $this->session->userdata('id'), 'entity_id' => _247AROUND, 'entity_type' => _247AROUND_EMPLOYEE_STRING);
                        $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                    }
                    $this->notify->insert_state_change($booking_id, SPARE_PARTS_REQUESTED, "", $reason, $this->session->userdata('id'), $this->session->userdata('emp_name'), $actor, $next_action, _247AROUND_DEFAULT_AGENT, NULL, $spare_id);

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

                    //$this->notify->insert_state_change($booking_id, PART_APPROVED_BY_ADMIN, $reason_text, $reason, $agent_id, $agent_name, $actor, $next_action, _247AROUND, NULL);
                    if (!empty($booking_id)) {
                        $affctd_id = $this->booking_model->update_booking($booking_id, $booking);
			            if (isset($data['is_micro_wh']) && $data['is_micro_wh'] == 1 ) {
                               $this->auto_delivered_for_micro_wh($delivered_sp, $partner_id);
                               unset($data['spare_id']);
                        }
                        $sc_data['current_status'] = "InProcess";
                        $sc_data['internal_status'] = SPARE_PARTS_REQUIRED;
                        $sc_data['service_center_remarks'] = date("F j") . ":- " . $reason;
                        $sc_data['update_date'] = date("Y-m-d H:i:s");
                        $this->vendor_model->update_service_center_action($booking_id, $sc_data);
                                        
                    }

                    if ($affected_id) {
                        echo 'success';
                    } else {
                        echo 'fail';
                    }
                }
            }
        }
    }



    /**
     * @desc this function is used to get the warehouse details
     * @param array $data this array contains the data for which we want warehouse details;
     * @return array $response
     */
    function get_warehouse_details($data, $partner_id){
        $response = array();
        
        if(!empty($data['inventory_id'])){
            return $this->miscelleneous->check_inventory_stock($data['inventory_id'], $partner_id, $data['state'], $data['service_center_id'],$data['model_number']);
        }
        
        return $response;
    }

    
    /**
     * @desc this function is used to trigger mail to partner(Invenotry Out of stock)
     * @param Array $parts_stock_not_found
     * @param Array $value1
     * @param Array $data
     */
        
    function send_out_of_stock_mail($parts_stock_not_found, $partner_id, $data, $booking_id) {
        if (!empty($parts_stock_not_found) && !empty($booking_id)) {
            //Getting template from Database
            $email_template = $this->booking_model->get_booking_email_template("out_of_stock_inventory");
            if (!empty($email_template)) {
                $join['service_centres'] = 'booking_details.assigned_vendor_id = service_centres.id';
                $JoinTypeTableArray['service_centres'] = 'left';
                $booking_state = $this->reusable_model->get_search_query('booking_details','service_centres.state',array('booking_details.booking_id' => $booking_id),$join,NULL,NULL,NULL,$JoinTypeTableArray)->result_array();
                
                //$get_partner_details = $this->partner_model->getpartner_details('partners.public_name,account_manager_id,primary_contact_email,owner_email', array('partners.id' => $partner_id));
                $get_partner_details = $this->partner_model->getpartner_data("partners.public_name,group_concat(distinct agent_filters.agent_id) as account_manager_id,primary_contact_email,owner_email", 
                            array('partners.id' => $partner_id, 'agent_filters.state' => $booking_state[0]['state']),"",0,1,1,"partners.id");
                if(!empty($get_partner_details)) {
                    $am_email = "";
                    if (!empty($get_partner_details[0]['account_manager_id'])) {
                        //$am_email = $this->employee_model->getemployeefromid($get_partner_details[0]['account_manager_id'])[0]['official_email'];
                        $am_email = $this->employee_model->getemployeeMailFromID($get_partner_details[0]['account_manager_id'])[0]['official_email'];
                    }

                    $this->load->library('table');
                    $template = array(
                        'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
                    );

                    $this->table->set_template($template);

                    $this->table->set_heading(array('Model Number', 'Part Type', 'Part Name'));
                    foreach ($parts_stock_not_found as $value) {
                        $this->table->add_row($value['model_number'], $value['part_type'], $value['part_name']);
                    }
                    $body_msg = $this->table->generate();
                    $to = $get_partner_details[0]['primary_contact_email'] . "," . $get_partner_details[0]['owner_email'];
                    $cc = $email_template[3] . "," . $am_email;
                    $subject = vsprintf($email_template[4], array($parts_stock_not_found[0]['model_number'], $parts_stock_not_found[0]['part_name']));
                    $emailBody = vsprintf($email_template[0], $body_msg);
                    $this->notify->sendEmail($email_template[2], $to, $cc, '', $subject, $emailBody, "", 'out_of_stock_inventory');
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
        if($this->session->userdata('user_group') == _247AROUND_RM || $this->session->userdata('user_group') == _247AROUND_ASM){
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
    function defective_spare_invoice($booking_id = ''){
        $this->miscelleneous->load_nav_header();
        $data['booking_id']=$booking_id;
        $this->load->view('employee/defective_spare_invoice_form',$data);
    }
    
     /*
     * @des - This function is used to load view for bill defective spare to service center
     * @param - void
     * @return - view
     */
    function defective_spare_oow_invoice(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/defective_spare_oow_invoice_form');
    }
    
     /*
     * @des - This function is used to get parts for booking
     * @param - booking_id
     * @return - array
     */
    function get_defective_spare_parts(){
        $where_internal_status = array("active" => '1');
        if($this->input->post("page")){
            $where_internal_status['page'] = $this->input->post("page");
        }
        $internal_status = $this->booking_model->get_internal_status($where_internal_status);
        $appliance_wise_hsn_code = array(_247AROUND_TV_SERVICE_ID => _247AROUND_TV_HSN_CODE , _247AROUND_WASHING_MACHINE_SERVICE_ID => _247AROUND_WASHING_MACHINE_HSN_CODE , _247AROUND_MICROWAVE_SERVICE_ID => _247AROUND_MICROWAVE_HSN_CODE ,
            _247AROUND_WATER_PURIFIER_SERVICE_ID => _247AROUND_WATER_PURIFIER_HSN_CODE , _247AROUND_AC_SERVICE_ID => _247AROUND_AC_HSN_CODE , _247AROUND_REFRIGERATOR_SERVICE_ID => _247AROUND_REFRIGERATOR_HSN_CODE , _247AROUND_GEYSER_SERVICE_ID => _247AROUND_GEYSER_HSN_CODE ,
            _247AROUND_AUDIO_SYSTEM_SERVICE_ID => _247AROUND_AUDIO_SYSTEM_HSN_CODE , _247AROUND_CHIMNEY_SERVICE_ID => _247AROUND_CHIMNEY_HSN_CODE);
        
        $appliance_wise_gst_rates = array(_247AROUND_TV_SERVICE_ID => _247AROUND_TV_GST_RATE , _247AROUND_WASHING_MACHINE_SERVICE_ID => _247AROUND_WASHING_MACHINE_GST_RATE , _247AROUND_MICROWAVE_SERVICE_ID => _247AROUND_MICROWAVE_GST_RATE ,
            _247AROUND_WATER_PURIFIER_SERVICE_ID => _247AROUND_WATER_PURIFIER_GST_RATE , _247AROUND_AC_SERVICE_ID => _247AROUND_AC_GST_RATE , _247AROUND_REFRIGERATOR_SERVICE_ID => _247AROUND_REFRIGERATOR_GST_RATE , _247AROUND_GEYSER_SERVICE_ID => _247AROUND_GEYSER_GST_RATE ,
            _247AROUND_AUDIO_SYSTEM_SERVICE_ID => _247AROUND_AUDIO_SYSTEM_GST_RATE , _247AROUND_CHIMNEY_SERVICE_ID => _247AROUND_CHIMNEY_GST_RATE);
        
        $select = "spare_parts_details.id, spare_parts_details.booking_id, parts_shipped, shipped_parts_type, challan_approx_value, service_center_id, spare_parts_details.status, partner_challan_file , hsn_code, gst_rate, price, shipped_quantity, booking_details.service_id";
        $booking_id = $this->input->post('booking_id');
        $part_warranty_status = $this->input->post('part_warranty_status');
        $where = array("spare_parts_details.booking_id"=>$booking_id, "spare_parts_details.status != 'Cancelled'"=>NULL, "sell_invoice_id IS NULL"=>NULL, "is_micro_wh != 1" => NULL, "parts_shipped IS NOT NULL"=>NULL, "part_warranty_status"=>$part_warranty_status);
        $data['data'] = $this->inventory_model->get_spare_parts_details($select, $where, true, true);
        $data['remarks'] = $internal_status;
        if(count($data['data']) > 0) {
            foreach($data['data'] as $key => $val) {
                $data['data'][$key]['hsn_code'] = (($val['hsn_code'] !== NULL) ? $val['hsn_code'] : $appliance_wise_hsn_code[$val['service_id']]);
                $data['data'][$key]['gst_rate'] = (($val['gst_rate'] !== NULL) ? $val['gst_rate'] : $appliance_wise_gst_rates[$val['service_id']]);
            }
        }
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
                    
                    /* Insert Spare Tracking Details */
                    if (!empty($spare_id)) {
                        $tracking_details = array('spare_id' => $spare_id, 'action' => $internal_status, 'remarks' => $reason." ".$new_state, 'agent_id' => $this->session->userdata('id'), 'entity_id' => _247AROUND, 'entity_type' => _247AROUND_EMPLOYEE_STRING);
                        $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                    }
                    
                    $this->notify->insert_state_change($booking_id, $new_state, "", $reason, $this->session->userdata('id'), $this->session->userdata('emp_name'), $actor, $next_action, $partner_id, NULL, $spare_id);

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
        log_message('info', __METHOD__. json_encode($this->input->post(), true));
        $part_warranty_status = $this->input->post('part_warranty_status');
        $reason = $this->input->post('remarks');    
        $data_to_insert = array();
        $delivered_sp = array();
        $sms_template_tag = '';
        $reason_text = '';
        
        $spare_approval_date = date('Y-m-d');
        $approval_agent_id = _247AROUND_DEFAULT_AGENT;

        $approval_entity_type = _247AROUND_EMPLOYEE_STRING;
        if ($this->session->userdata('emp_name') && $this->session->userdata('userType') != 'partner') {
            $agent_name = $this->session->userdata('emp_name');
            $agent_id   = $this->session->userdata('id');
            $approval_agent_id = $agent_id;
            $track_entity_type = $approval_entity_type = _247AROUND_EMPLOYEE_STRING;

            $approval_entity_type = _247AROUND_EMPLOYEE_STRING; /// Entity Type will be 247around 
        } else if ($this->session->userdata('userType') == 'partner') { //// Partner Session ////
            $agent_name = $this->session->userdata('partner_name');
            $agent_id   = $this->session->userdata('agent_id');
            $approval_agent_id = $agent_id;
            $track_entity_type = $approval_entity_type = _247AROUND_PARTNER_STRING;
        } else {
            $agent_id = _247AROUND_DEFAULT_AGENT;
            $agent_name = _247AROUND_DEFAULT_AGENT_NAME; 
            $approval_agent_id = _247AROUND_DEFAULT_AGENT;
            $approval_entity_type = _247AROUND_EMPLOYEE_STRING;
            $track_entity_type = _247AROUND_EMPLOYEE_STRING;
        }
        
 
        if (!empty($spare_id)) {
            
            $select = 'spare_parts_details.id,spare_parts_details.quantity,spare_parts_details.shipped_quantity,spare_parts_details.entity_type,spare_parts_details.booking_id,spare_parts_details.parts_requested,spare_parts_details.parts_requested_type,spare_parts_details.status,'
                    . 'spare_parts_details.requested_inventory_id,spare_parts_details.original_inventory_id,spare_parts_details.purchase_price,spare_parts_details.service_center_id,spare_parts_details.invoice_gst_rate, spare_parts_details.part_warranty_status,'
                    . 'spare_parts_details.is_micro_wh,spare_parts_details.model_number,spare_parts_details.serial_number,spare_parts_details.shipped_inventory_id,spare_parts_details.date_of_request,'
                    . 'booking_details.partner_id as booking_partner_id,booking_details.amount_due,booking_details.next_action,booking_details.internal_status, booking_details.service_id,spare_parts_details.serial_number_pic,spare_parts_details.invoice_pic, spare_parts_details.defective_return_to_entity_type';

            $spare_parts_details = $this->partner_model->get_spare_parts_by_any($select, array('spare_parts_details.id' => $spare_id), TRUE, TRUE, false);

            $data_unit_details['sf_model_number']= $spare_parts_details[0]['model_number'];  
            $data_unit_details['serial_number']=$spare_parts_details[0]['serial_number'];
            $data_unit_details['serial_number_pic']=$spare_parts_details[0]['serial_number_pic'];  
            //SF_PURCHASE_INVOICE_FILE_TYPE  
            $data_booking_file['booking_id']= $spare_parts_details[0]['booking_id'];
            $data_booking_file['file_description_id']=SF_PURCHASE_INVOICE_FILE_TYPE;
            $data_booking_file['file_name']= $spare_parts_details[0]['invoice_pic'];                              
            if (!empty($spare_parts_details)) {

                $partner_id = $spare_parts_details[0]['booking_partner_id'];
                $requested_inventory_id = $spare_parts_details[0]['requested_inventory_id'];
                $entity_type = $spare_parts_details[0]['entity_type'];
                $service_center_id = $spare_parts_details[0]['service_center_id'];
                $booking_id = $spare_parts_details[0]['booking_id'];
                $amount_due = $spare_parts_details[0]['amount_due'];

                //$invoice_gst_rate = $spare_parts_details[0]['invoice_gst_rate'];
                
                $data['model_number'] = $spare_parts_details[0]['model_number'];
                $data['parts_requested'] = $spare_parts_details[0]['parts_requested'];
                $data['parts_requested_type'] = $spare_parts_details[0]['parts_requested_type'];
                $data['date_of_request'] = $spare_parts_details[0]['date_of_request'];
                $data['requested_inventory_id'] = $requested_inventory_id;
                $data['service_center_id'] = $service_center_id;
                $data['booking_id'] = $booking_id;
                $data['entity_type'] = $entity_type;
                $data['partner_id'] = $partner_id;
                $data['quantity'] = $spare_parts_details[0]['quantity'];
                $is_micro_wh = $data['is_micro_wh'] = $spare_parts_details[0]['is_micro_wh'];
                
                $spare_data["defective_return_to_entity_type"] = $spare_parts_details[0]['defective_return_to_entity_type'];
                /* field part_warranty_status value 1 means in-warranty and 2 means out-warranty */
                if ($part_warranty_status == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS) {
                    $spare_data['status'] = SPARE_OOW_EST_REQUESTED;
                    $sc_data['internal_status'] = SPARE_OOW_EST_REQUESTED;
                } else {
                    $spare_data['status'] = SPARE_PARTS_REQUESTED;                    
                    $sms_template_tag = SPARE_ON_IN_WARRANTY_SMS_TAG;
                }
                $reason_text = "";
                if ($spare_parts_details[0]['part_warranty_status'] == SPARE_PART_IN_WARRANTY_STATUS && $part_warranty_status == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS) {

                    $sms_template_tag = SPARE_ON_OUT_OF_WARRANTY_SMS_TAG;
                    $reason_text = 'Sent Spare ' . REPAIR_IN_WARRANTY_TAG . ' to ' . REPAIR_OOW_TAG;
                    $this->send_email_on_change_part_warranty_status($part_warranty_status, $service_center_id, $data, 'spare_parts_oow_email_to_customer');
                } else if ($spare_parts_details[0]['part_warranty_status'] == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS && $part_warranty_status == SPARE_PART_IN_WARRANTY_STATUS) {

                    $sms_template_tag = SPARE_ON_IN_WARRANTY_SMS_TAG;
                    $reason_text = 'Sent Spare ' . REPAIR_OOW_TAG . ' to ' . REPAIR_IN_WARRANTY_TAG;
                    $this->send_email_on_change_part_warranty_status($part_warranty_status, $service_center_id, $data, 'spare_parts_in_warranty_email_to_customer');
                }
                
                if (!empty($sms_template_tag)) {
                    $this->miscelleneous->send_spare_requested_sms_to_customer($spare_parts_details[0]['parts_requested_type'], $booking_id, $sms_template_tag);
                }
                $partner_details = $this->partner_model->getpartner_details("is_def_spare_required,is_wh, is_defective_part_return_wh,is_micro_wh", array('partners.id' => $partner_id));
                if ($entity_type == _247AROUND_PARTNER_STRING && $part_warranty_status == SPARE_PART_IN_WARRANTY_STATUS) {
                    /** search if there is any warehouse for requested spare parts
                     * if any warehouse exist then assign this spare request to that service center otherwise assign
                     * assign to respective partner. 
                     * (need to discuss) what we will do if no warehouse have this inventory.
                     */
                    $sf_state = $this->vendor_model->getVendorDetails("service_centres.state", array('service_centres.id' => $service_center_id));

                    $is_warehouse = false;
                    if (!empty($partner_details[0]['is_wh'])) {

                        $is_warehouse = TRUE;
                    } else if (!empty($partner_details[0]['is_micro_wh'])) {
                        $is_warehouse = TRUE;
                    }

                    if (!empty($is_warehouse)) {
                            $warehouse_details = $this->get_warehouse_details(array('inventory_id' => $spare_parts_details[0]['requested_inventory_id'], 'state' => $sf_state[0]['state'], 'service_center_id' => $service_center_id,'model_number'=>$data['model_number']), $partner_id);
                            
                            if (!empty($warehouse_details) && $warehouse_details['stock'] >= $data['quantity']) {
                                $spare_data['partner_id'] = $warehouse_details['entity_id'];
                                $spare_data['entity_type'] = $warehouse_details['entity_type'];
                                $spare_data['defective_return_to_entity_type'] = $warehouse_details['defective_return_to_entity_type'];
                                $spare_data['defective_return_to_entity_id'] = $warehouse_details['defective_return_to_entity_id'];
                                $is_micro_wh = $spare_data['is_micro_wh'] = $warehouse_details['is_micro_wh'];
                                $spare_data['challan_approx_value'] = round(($warehouse_details['challan_approx_value']*$data['quantity']),2);
                                $spare_data['invoice_gst_rate'] = $warehouse_details['gst_rate'];
                                $spare_data['parts_requested'] = $warehouse_details['part_name'];
                                $spare_data['parts_requested_type'] = $warehouse_details['type'];
                                $spare_data['quantity'] = $data['quantity'];
                                $spare_data['requested_inventory_id'] = $warehouse_details['inventory_id'];
                                //$data['shipped_quantity'] = $data['quantity'];
                                // $spare_data['shipped_inventory_id'] = $warehouse_details['inventory_id'];
                                    
                                /*Checked Spare Approved By Admin Or Partner*/
                                $track_partner_id = _247AROUND;
                                if ($this->session->userdata('userType') == 'partner') {
                                    $track_partner_id = $partner_id;
                                }
                                /* Insert Spare Tracking Details When picked alternate inventory*/
                                if (!empty($spare_id)) {
                                    if ($spare_parts_details[0]['requested_inventory_id'] != $warehouse_details['inventory_id']) {
                                        $tracking_details = array('spare_id' => $spare_id, 'action' => ALTERNATE_PART_PICKED, 'remarks' => 'Requested Spare Part Stock Not Available Alternate Part Picked', 'agent_id' => $agent_id, 'entity_id' => $track_partner_id, 'entity_type' => $track_entity_type);
                                        $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                                    }
                                }
                                    

                            } else {
                                $spare_data['partner_id'] = $partner_id;
                                $spare_data['entity_type'] = _247AROUND_PARTNER_STRING;
                                $is_micro_wh = $spare_data['is_micro_wh'] = 0;
                                if (isset($warehouse_details['challan_approx_value'])) {
                                 $spare_data['challan_approx_value'] = round(($warehouse_details['challan_approx_value']*$data['quantity']),2);   
                                }
                                $spare_data['defective_return_to_entity_type'] = _247AROUND_PARTNER_STRING;
                                $spare_data['defective_return_to_entity_id'] = $partner_id;
                            }

                    } else {
                        $spare_data['partner_id'] = $partner_id;
                        $spare_data['entity_type'] = _247AROUND_PARTNER_STRING;
                        $is_micro_wh = $spare_data['is_micro_wh'] = 0;
                        $spare_data['defective_return_to_entity_type'] = _247AROUND_PARTNER_STRING;
                        $spare_data['defective_return_to_entity_id'] = $partner_id;
                    }
                } 
                       
                $spare_data['part_requested_on_approval'] = 1;
                $spare_data['part_warranty_status'] = $part_warranty_status; 
                
                if($part_warranty_status == SPARE_PART_IN_WARRANTY_STATUS){
                    $spare_data['defective_part_required'] = $partner_details[0]['is_def_spare_required'];
                } else {
                    $spare_data['defective_part_required'] = 0;
                }

                $is_saas = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
                
                if (empty($is_saas)) {
                    if ($spare_data['defective_return_to_entity_type'] == _247AROUND_PARTNER_STRING) {
                        $spare_data['defective_return_to_entity_type'] = _247AROUND_SF_STRING;
                        $spare_data['defective_return_to_entity_id'] = DEFAULT_WAREHOUSE_ID;
                    }
                }

                ///  setting approval date ,agent,entity type ////
                $spare_data['spare_approval_date'] = $spare_approval_date;
                $spare_data['approval_agent_id'] = $approval_agent_id;
                $spare_data['approval_entity_type'] = $approval_entity_type;
   
                $affected_id = $this->service_centers_model->update_spare_parts(array('id' => $spare_id), $spare_data);


                if ($spare_data['status'] == SPARE_OOW_EST_REQUESTED) {
                    
                    /* Checked Spare Approved By Admin Or Partner */
                    $track_partner_id = _247AROUND;
                    if ($this->session->userdata('userType') == 'partner') {
                        $track_partner_id = $partner_id;
                    }
                    /* Insert Spare Tracking Details */
                    if (!empty($spare_id)) {
                        if (!empty($spare_data['status'])) {
                            $tracking_details = array('spare_id' => $spare_id, 'action' => $spare_data['status'], 'remarks' => trim($reason), 'agent_id' => $agent_id, 'entity_id' => $track_partner_id, 'entity_type' => $track_entity_type);
                            $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                        }
                    }
                    if (isset($spare_data['requested_inventory_id']) && !empty($spare_data['requested_inventory_id'])) {
                        $requested_inventory_id = $spare_data['requested_inventory_id'];
                    } 
                    
                    $auto_estimate_approve = 1;
                    $saas_module = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
                    if($saas_module){
                        $access = $this->partner_model->get_partner_permission(array('partner_id' => $partner_id, 
            'permission_type' => AUTO_PICK_OOW_PART_ESTIMATE, 'is_on' => 1));
                        if(!empty($access)){
                            $auto_estimate_approve = 1;
                        } else {
                            $auto_estimate_approve = 0;
                        }
                    }
                    
                    if ($spare_data['status'] == SPARE_OOW_EST_REQUESTED &&
                            isset($requested_inventory_id) &&
                            !empty($requested_inventory_id)&& 
                            !empty($auto_estimate_approve)) {
                        
                        $inventory_master_details = $this->inventory_model->get_inventory_master_list_data('inventory_id, hsn_code, gst_rate, price', array('inventory_id' => $requested_inventory_id));

                        $cb_url = base_url() . "apiDataRequest/update_estimate_oow";
                        $pcb['booking_id'] = $booking_id;
                        $pcb['assigned_vendor_id'] = $service_center_id;
                        $pcb['amount_due'] = $amount_due;
                        $pcb['partner_id'] = $partner_id;
                        $pcb['sp_id'] = $spare_id;
                        $pcb['gst_rate'] = $inventory_master_details[0]['gst_rate'];

                        $pcb['estimate_cost'] = round((($inventory_master_details[0]['price'] + ( $inventory_master_details[0]['price'] * $inventory_master_details[0]['gst_rate']) / 100)*$data['quantity']),2);
                        $pcb['agent_id'] = $agent_id;

                        $this->asynchronous_lib->do_background_process($cb_url, $pcb);
                    }
                } else {
                    //Send Push Notification 
                    if($is_micro_wh == 1 || $is_micro_wh == 2){
                        $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $spare_data['partner_id'], $spare_data['requested_inventory_id'],$data['quantity']); 
                    }
                    
                    if (!empty($spare_data['status'])) {
                        $data['status'] = $spare_data['status'];
                    }
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
                                        
                    if (isset($is_micro_wh) && $is_micro_wh == 1) {
                     
                        $data['spare_id'] = $spare_id;
                        $data['requested_inventory_id'] = $spare_data['requested_inventory_id'];
                        $data['shipped_inventory_id'] = $spare_data['requested_inventory_id'];
                        array_push($delivered_sp, $data);
                    }
                }

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

                    $new_state = PART_APPROVED_BY_ADMIN;
                    $state_change_partner_id = _247AROUND;
                    if ($this->session->userdata('userType') == 'partner') { //// Stare A/C to Session
                        $new_state = PART_APPROVED_BY_ADMIN . " from Partner Panel";
                        $state_change_partner_id = $partner_id;
                    }
                    
                    /* Insert Spare Tracking Details */
                    if (!empty($spare_id)) {
                        if (!empty($data['status'])) {
                            $tracking_details = array('spare_id' => $spare_id, 'action' => $data['status'], 'remarks' => trim($reason." ".$new_state), 'agent_id' => $agent_id, 'entity_id' => $state_change_partner_id, 'entity_type' => $track_entity_type);
                            $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                        }
                    }
                    $this->notify->insert_state_change($booking_id, $new_state, $reason_text, $reason, $agent_id, $agent_name, $actor, $next_action, $state_change_partner_id, NULL, $spare_id);
                    if (!empty($booking_id)) {
                        $affctd_id = $this->booking_model->update_booking($booking_id, $booking);

                         if (!empty($spare_parts_details[0]['invoice_pic'])) {
                          $this->booking_model->update_booking_unit_details_by_any(array('booking_id' => trim($booking_id)),$data_unit_details);
                          $this->booking_model->insert_booking_file($data_booking_file);
                        }

		         	if (isset($is_micro_wh) && $is_micro_wh == 1) {
                        $this->auto_delivered_for_micro_wh($delivered_sp, $partner_id);
                        unset($data['spare_id']);

                    }
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
     * @desc this function is used to trigger mail to Customers on spare parts approval
     * @param  $part_warranty_status
     * @param  $email_template
     * @param Array $data
     * @param $booking_id
     */
    function send_email_on_change_part_warranty_status($part_warranty_status, $service_center_id, $data ,$email_template_tag) {
              
        if (!empty($part_warranty_status)) {
            //Getting template from Database
            $email_template = $this->booking_model->get_booking_email_template($email_template_tag);
                                       
            if (!empty($email_template)) {
                
               $vendor_details = $this->vendor_model->getVendorDetails('service_centres.name, service_centres.phone_1, service_centres.email', array('service_centres.id' => $service_center_id) ,'name',array());
                
                $join['service_centres'] = 'booking_details.assigned_vendor_id = service_centres.id';
                $JoinTypeTableArray['service_centres'] = 'left';
                $booking_state = $this->reusable_model->get_search_query('booking_details','service_centres.state',array('booking_details.booking_id' => $data['booking_id']),$join,NULL,NULL,NULL,$JoinTypeTableArray)->result_array();
                
                //$get_partner_details = $this->partner_model->getpartner_details('partners.public_name,account_manager_id,primary_contact_email,owner_email', array('partners.id' => $data['partner_id']));
                $get_partner_details = $this->partner_model->getpartner_data("partners.public_name,group_concat(distinct agent_filters.agent_id) as account_manager_id,primary_contact_email,owner_email", 
                            array('partners.id' => $data['partner_id'], 'agent_filters.state' => $booking_state[0]['state']),"",0,1,1,"partners.id");
                
                $am_email = "";
                if (!empty($get_partner_details[0]['account_manager_id'])) {
                    //$am_email = $this->employee_model->getemployeefromid($get_partner_details[0]['account_manager_id'])[0]['official_email'];
                    $am_email = $this->employee_model->getemployeeMailFromID($get_partner_details[0]['account_manager_id'])[0]['official_email'];
                }
                $to = $vendor_details[0]['email'];
                $cc = $am_email;
                if(!empty($email_template[3])){
                    $cc = $email_template[3] . "," . $am_email;
                }            
                $subject = vsprintf($email_template[4], array($data['parts_requested_type'], $data['booking_id']));
                
                $emailBody = vsprintf($email_template[0], array($data['parts_requested'],$data['booking_id']));
                $this->notify->sendEmail($email_template[2], $to, $cc, '', $subject, $emailBody, '', $email_template_tag, '', $data['booking_id']);
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
            if (!empty($value['service_center_id'])) {
                $data['model_number_shipped'] = $value['model_number'];
                $data['parts_shipped'] = $value['parts_requested'];
                $data['shipped_parts_type'] = $value['parts_requested_type'];
                $data['shipped_date'] = $value['date_of_request'];
                $data['status'] = SPARE_SHIPPED_BY_PARTNER;
                $data['shipped_inventory_id'] = $value['requested_inventory_id'];
                $data['shipped_quantity'] = $value['quantity'];

                $where = array('id' => $value['spare_id']);
                $this->service_centers_model->update_spare_parts($where, $data);

                $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $value['partner_id'], $value['requested_inventory_id'], -$value['quantity']);

                $in['receiver_entity_id'] = $value['service_center_id'];
                $in['receiver_entity_type'] = _247AROUND_SF_STRING;
                $in['sender_entity_id'] = $value['service_center_id'];
                $in['sender_entity_type'] = _247AROUND_SF_STRING;
                $in['stock'] = -$value['quantity'];
                $in['booking_id'] = $value['booking_id'];

                if ($this->session->userdata('userType')!='partner') {  //// Partner Session Handle ///
                $in['agent_id'] = $this->session->userdata('id');
                $in['agent_type'] = _247AROUND_SF_STRING;
                }else{
                $in['agent_id'] = _247AROUND_DEFAULT_AGENT;
                $in['agent_type'] = _247AROUND_SF_STRING;
                }

                $in['is_wh'] = TRUE;
                $in['inventory_id'] = $data['shipped_inventory_id'];
                $this->miscelleneous->process_inventory_stocks($in);
                $this->acknowledge_delivered_spare_parts($value['booking_id'], $value['service_center_id'], $value['spare_id'], $partner_id, true, FALSE);
            }
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
            
            $spare_id = $id;
            
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
                $is_requested = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id, spare_parts_details.status, spare_parts_details.booking_id", array('booking_id' => $booking_id, 'status IN ("' . SPARE_SHIPPED_BY_PARTNER . '", "'
                    . SPARE_PARTS_REQUESTED . '", "' . ESTIMATE_APPROVED_BY_CUSTOMER . '", "' . SPARE_PART_ON_APPROVAL . '", "' . SPARE_OOW_EST_GIVEN . '", "' . SPARE_OOW_EST_REQUESTED . '") ' => NULL));
                if ($this->session->userdata('service_center_id')) {
                    $agent_id = $this->session->userdata('service_center_agent_id');
                    $track_entity_id = $sc_entity_id = $this->session->userdata('service_center_id');
                    $p_entity_id = NULL;
                    $track_entity_type = _247AROUND_SF_STRING;
                } else if ($this->session->userdata('partner_id')) { //// Partner Session Handle ////
                    $agent_id = _247AROUND_DEFAULT_AGENT;
                    $sc_entity_id = NULL;
                    $track_entity_id = $p_entity_id = _247AROUND;
                    $track_entity_type = _247AROUND_EMPLOYEE_STRING;
                } else {
                    $agent_id = _247AROUND_DEFAULT_AGENT;
                    $track_entity_id = $p_entity_id = _247AROUND;
                    $track_entity_type = _247AROUND_EMPLOYEE_STRING;
                    $sc_entity_id = NULL;
                }
                if (empty($is_requested)) {
                    $booking['booking_date'] = date('d-m-Y', strtotime('+1 days'));
                    $booking['update_date'] = date("Y-m-d H:i:s");
                    $booking['internal_status'] = SPARE_DELIVERED_TO_SF;

                    $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, SPARE_DELIVERED_TO_SF, $partner_id, $booking_id);
                    $actor = $next_action = 'not_define';
                    if (!empty($partner_status)) {
                        $booking['partner_current_status'] = $partner_status[0];
                        $booking['partner_internal_status'] = $partner_status[1];
                        $actor = $booking['actor'] = $partner_status[2];
                        $next_action = $booking['next_action'] = $partner_status[3];
                    }
                    $b_status = $this->booking_model->update_booking($booking_id, $booking);
                    if ($b_status) {
                        
                        /* Insert Spare Tracking Details */
                        if (!empty($spare_id)) {
                            $tracking_details = array('spare_id' => $spare_id, 'action' => $sp_data['status'], 'remarks' => 'SF acknowledged to receive spare parts', 'agent_id' => $agent_id, 'entity_id' => $track_entity_id, 'entity_type' => $track_entity_type);
                            $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                        }

                        $this->notify->insert_state_change($booking_id, SPARE_DELIVERED_TO_SF, _247AROUND_PENDING, "SF acknowledged to receive spare parts", $agent_id, $agent_id, $actor, $next_action, $p_entity_id, $sc_entity_id, $spare_id);

                        $sc_data['current_status'] = _247AROUND_PENDING;
                        $sc_data['internal_status'] = SPARE_DELIVERED_TO_SF;
                        $sc_data['update_date'] = date("Y-m-d H:i:s");
                        $this->vendor_model->update_service_center_action($booking_id, $sc_data);
                        
                        $this->miscelleneous->send_spare_delivered_sms_to_customer($id, $booking_id);
                        
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
                    
                    /* Insert Spare Tracking Details */
                    if (!empty($spare_id)) {
                        $tracking_details = array('spare_id' => $spare_id, 'action' => $sp_data['status'], 'remarks' => 'SF acknowledged to receive spare parts', 'agent_id' => $agent_id, 'entity_id' => $this->session->userdata('service_center_id'), 'entity_type' => $track_entity_type);
                        $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                    }
                    $this->notify->insert_state_change($booking_id, SPARE_DELIVERED_TO_SF, _247AROUND_PENDING, "SF acknowledged to receive spare parts", $agent_id, $agent_id, $actor, $next_action, $p_entity_id, $sc_entity_id, $spare_id);
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
    
    /**
     *  @desc : This function is used to tag courier details by invoice ids
     *  @return : void();
     */
    function tag_courier_details_by_invoice_ids() {
        $data['courier_details'] = $this->inventory_model->get_courier_services('*');
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/tag_courier_details_by_invoice_id',$data);
    }     
    /*
     *  @desc : This function is used add create new courier service.
     *  @return : void();
     */
    function add_courier_service() {
        $data['courier_details'] = $this->inventory_model->get_courier_services('*');
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/add_courier_service_details',$data);
    }    
    
    /*
     *  @desc : This function is used manage courier service as add and edit.
     *  @status : Json data
     */
    
    function manage_courier_service() {
        log_message('info', __METHOD__ . json_encode($this->input->post(), true));
        $courier_id = $this->input->post("courier_services_id");
        $courier_name = $this->input->post("courier_name");
        $courier_code = $this->input->post("courier_code");
        $status = array();
        $data = array();
        if (!empty($courier_id)) {
            $data['courier_name'] = $courier_name;
            $data['courier_code'] = $courier_code;
            if (!empty($data)) {
                $where = array("courier_services.id" => $courier_id);
                $affected_id = $this->inventory_model->update_courier_services($data,$where);
                if ($affected_id) {
                    $status["message"] = "Courier service successfuly Updated.";
                } else {
                    $status["message"] = "Courier service not Updated.";
                }
            }
        } else {
            if (!empty($courier_name)) {
                $data['courier_name'] = $courier_name;
                $data['courier_code'] = $courier_code;
                $where = array("courier_services.courier_name = '".$courier_name."' OR courier_services.courier_code = '".$courier_code."' " => null );
                $courier_service = $this->inventory_model->get_courier_service_details('courier_services.id, courier_services.courier_name' ,$where);
                
                if(empty($courier_service)){
                if (!empty($data)) {
                    $insert_id = $this->inventory_model->insert_courier_services_data($data);
                    if (!empty($insert_id)) {
                        $status["message"] = "Courier service successfuly added.";
                    } else {
                        $status["message"] = "Courier service not added.";
                    }
                }
            }else{
             $status["message"] = "Courier service already exist our system.";   
            }
            }
        }
        echo json_encode($status);
    }    
   /*
    *  @desc : This function is used manage courier service edit.
    *  @status : Json data
    */
    function manage_courier_service_satus() {
        log_message('info', __METHOD__ . json_encode($this->input->post(), true));
        $error = array();
        $data = array();
        $courier_id = $this->input->post('data')['id'];
        $status = $this->input->post('data')['status'];
        if (!empty($courier_id)) {
            $where = array("courier_services.id" => $courier_id);
            if ($status == 1) {
                $active = 0;
            } else {
                $active = 1;
            }
            $data['status'] = $active;
            if (!empty($data)) {
                $affected_id = $this->inventory_model->update_courier_services($data, $where);
                if ($affected_id) {
                    $error["message"] = "Courier service successfuly Updated.";
                } else {
                    $error["message"] = "Courier service not Updated.";
                }
            }
        }else{
          $error["message"] = "Courier service id should not be blank.";  
        }
        echo json_encode($error);
    }

    /**
     *  @desc : This function is used to tag courier details by invoice ids
     *  @return : void();
     */
    function process_to_update_courier_details_by_invoice_ids() {

        $this->form_validation->set_rules('awb_by_wh', 'Enter AWB Number', 'required');
        $this->form_validation->set_rules('courier_name_by_wh', 'Select Courier Name', 'required');
        $this->form_validation->set_rules('courier_price_by_wh', 'Enter Courier Price', 'required');
        $this->form_validation->set_rules('defective_parts_shippped_date_by_wh', 'Enter Shipped Date', 'required');
        $this->form_validation->set_rules('invoice_ids', 'Enter Invoice Ids', 'required');
        $data = array();
        $spare_data = array();
        $courier_detail = array();
        if ($this->form_validation->run()) {
            $invoice_ids = trim($this->input->post('invoice_ids'));
            $courier_detail['AWB_no'] = $spare_data['awb_by_wh'] = $data['awb_number'] = $this->input->post('awb_by_wh');
            $courier_detail['courier_name'] = $spare_data['courier_name_by_wh'] = $data['company_name'] = $this->input->post('courier_name_by_wh');
            $courier_detail['shipment_date'] = $spare_data['defective_parts_shippped_date_by_wh'] = $data['shippment_date'] = $this->input->post('defective_parts_shippped_date_by_wh');
            $exist_courier_image = $this->input->post('exist_courier_image');
            $bulk_courier_price = $this->input->post('courier_price_by_wh');

            if (!empty($invoice_ids)) {
                $invoice_ids_arr = explode(',', $invoice_ids);
                $total_invoice_id = count($invoice_ids_arr);
            }

            $courier_id_arr = array();
            $select = 'id, invoice_id, courier_id';
            $flag = true;
            $eway_data = array();
            
            $upload_eway_file_name = str_replace(' ', '_', trim($_FILES['defective_parts_shippped_ewaybill_pic_by_wh']['name']));
            $eway_file_name = 'defective_spare_ewaybill_by_wh_' . rand(10, 100) . '_' . $upload_eway_file_name;
            //Upload files to AWS
            $directory_xls = "ewaybill/" . $eway_file_name;
            $this->s3->putObjectFile($_FILES['defective_parts_shippped_ewaybill_pic_by_wh']['tmp_name'], BITBUCKET_DIRECTORY, $directory_xls, S3::ACL_PUBLIC_READ);
           
            foreach ($invoice_ids_arr as $kay => $val) {
                $where = array("inventory_ledger.invoice_id ='".$val."' OR inventory_ledger.micro_invoice_id ='".$val."'" => NULL);
                $inventory_ledger = $this->inventory_model->get_inventory_ledger_details($select, $where);
                if (!empty($inventory_ledger)) {
                    $courier_id_arr[] = array('courier_id'=> $inventory_ledger[0]['courier_id'], 'invoice_id' => $val) ;
                    $eway_details = array();
                    $eway_details['courier_details_id'] = $inventory_ledger[0]['courier_id'];
                    $eway_details['ewaybill_no'] = $this->input->post("eway_bill_by_wh");
                    $eway_details['vehicle_number'] = $this->input->post("eway_vehicle_number");
                    $eway_details['ewaybill_file'] = $eway_file_name;
                    $eway_details['invoice_id'] = $val;
                    $eway_data[] = $eway_details;
                } else {
                    $flag = false;
                    break;
                }
            }


            if (!empty($eway_data)) {
                $this->inventory_model->insert_ewaybill_details_in_bulk($eway_data);
            }
            
            if ($flag) {

                if ($total_invoice_id > 1) {
                   $courier_detail['courier_charge'] =  $spare_data['courier_price_by_wh'] = $data['courier_charge'] = ($bulk_courier_price / $total_invoice_id);
                } else {
                    $courier_detail['courier_charge'] =  $spare_data['courier_price_by_wh'] = $data['courier_charge'] = $bulk_courier_price;
                }

                if (!empty($exist_courier_image)) {
                    $courier_file['message'] = $exist_courier_image;
                } else {
                    $courier_file = $this->upload_defective_parts_shipped_courier_file($_FILES['defective_parts_shippped_courier_pic_by_wh']);
                }

                $courier_detail['courier_file'] = $spare_data['defective_parts_shippped_courier_pic_by_wh'] = $data['courier_invoice_file'] = $courier_file['message'];
              
                foreach ($courier_id_arr as $val) {
                    
                    $courier_company_invoice = $this->inventory_model->get_courier_company_invoice_details("courier_company_invoice_details.id, courier_company_invoice_details.awb_number", array('courier_company_invoice_details.id' => $val['courier_id']), '');
                    
                    if (!empty($courier_company_invoice)) {

                        $this->inventory_model->update_courier_detail(array("courier_details.AWB_no" => $courier_company_invoice[0]['awb_number']), $courier_detail);
                    }

                    $affected_id = $this->inventory_model->update_courier_company_invoice_details(array('courier_company_invoice_details.id' => $val['courier_id']), $data);
                    
                    if ($affected_id) {
                        $this->service_centers_model->update_spare_parts(array('spare_parts_details.reverse_purchase_invoice_id' => $val['invoice_id']), $spare_data);
                    }
                }
            } else {
                $this->session->set_userdata(array('error' => 'Please Enter Valid Invoice ids.'));
                redirect(base_url() . "employee/spare_parts/tag_courier_details_by_invoice_ids");
            }

            if ($affected_id) {
                $this->session->set_userdata(array('success' => 'Successfuly Updated.'));
                redirect(base_url() . "employee/spare_parts/tag_courier_details_by_invoice_ids");
            }
        } else {
            //Setting success session data 
            $this->session->set_userdata(array('error' => 'Please Fill Form Details.'));
            redirect(base_url() . "employee/spare_parts/tag_courier_details_by_invoice_ids");
        }
    }

    /**
     *  @desc : This function is used to updater upload the defective spare shipped by warehouse courier file
     *  @param : $file_details array()
     *  @return :$res array
     */
    function upload_defective_parts_shipped_courier_file($file_details) {
        log_message("info",__METHOD__);
        $MB = 1048576;
        //check if upload file is empty or not
        if (!empty($file_details['name'])) {
            //check upload file size. it should not be greater than 2mb in size

            if ($file_details['size'] <= 2 * $MB) {
                $allowed = array('pdf', 'jpg', 'png', 'jpeg', 'JPG', 'JPEG', 'PNG', 'PDF');
                $ext = pathinfo($file_details['name'], PATHINFO_EXTENSION);
                //check upload file type. it should be pdf.
                if (in_array($ext, $allowed)) {
                    $upload_file_name = str_replace(' ', '_', trim($file_details['name']));

                    $file_name = 'defective_spare_courier_by_wh_' . rand(10, 100) . '_' . $upload_file_name;
                    //Upload files to AWS
                    $directory_xls = "vendor-partner-docs/" . $file_name;
                    $this->s3->putObjectFile($file_details['tmp_name'], BITBUCKET_DIRECTORY, $directory_xls, S3::ACL_PUBLIC_READ);

                    $res['status'] = true;
                    $res['message'] = $file_name;
                } else {
                    $res['status'] = false;
                    $res['message'] = 'Uploaded file type not valid.';
                }
            } else {
                $res['status'] = false;
                $res['message'] = 'Uploaded file size can not be greater than 2 mb';
            }
        } else {
            $res['status'] = false;
            $res['message'] = 'Please Upload File';
        }

        return $res;
    }
    
    /**
     *  @desc : This function is used to spare parts cancellation reason
     *  @param : $file_details array()
     *  @return :$res array
     */
    function get_spare_parts_cancellation_reasons($tag = 'spare_parts') {
        $spare_cancellation_reasons = $this->booking_model->cancelreason(array('reason_of' => $tag),"booking_cancellation_reasons.reason");
        $option = '<option selected disabled>Select Cancellation Reason</option>';
        foreach ($spare_cancellation_reasons as $value) {
            $option .= "<option value='" . $value->id . "'>" . $value->reason . "</option>";
        }
        echo $option;
    }
    
    /**
    *  @desc : It's Used to checked login
    */
    
    function checkUserSession() {
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            echo PHP_EOL . 'Terminal Access Not Allowed' . PHP_EOL;
            redirect(base_url() . "employee/login");
        }
    }
        
    /**
     *  @desc : This function is used to spare parts cancellation reason
     *  @param : $file_details array()
     *  @return :$res array
     */
    function pick_up_spare_parts() {
        log_message('info', json_encode($this->input->post(), true));

        $spare_parts_ids = $this->input->post('spare_parts_ids');
        $request_type = $this->input->post('request_type');
        $post['courier_name'] = $this->input->post('courier_name');
        $post['courier_to_email'] = $this->input->post('courier_to_email');
        $post['courier_cc_email'] = $this->input->post('courier_cc_email');
        $post['request_type'] = $request_type;

        if (!empty($spare_parts_ids)) {
            $spare_parts_id_list = explode(',', $spare_parts_ids);
        }
        
        if (!empty($spare_parts_id_list)) {
            $flag = false;
            $service_center_id_list = array();
            $pickup_email_details = array();
            foreach ($spare_parts_id_list as $spare_part_id) {
                $select = "spare_parts_details.id, spare_parts_details.status, spare_parts_details.service_center_id,"
                        . "service_centres.name, service_centres.phone_1, service_centres.email, service_centres.address, service_centres.state, "
                        . "service_centres.district, service_centres.pincode, service_centres.phone_2";
                $spare_parts_details = $this->partner_model->get_spare_parts_by_any($select, array('spare_parts_details.id' => $spare_part_id), false, true);

                if ($request_type == 2) {
                    $spare_data['around_pickup_from_service_center'] = COURIER_PICKUP_REQUEST;
                    $tracking_status = "Courier Pickup Request";
                } else {
                    $spare_data['around_pickup_from_service_center'] = COURIER_PICKUP_SCHEDULE;
                    $tracking_status = "Courier Pickup Schedule";
                }
                
                $spare_data['around_pickup_courier'] = $post['courier_name'];

                if (!in_array($spare_parts_details[0]['service_center_id'], $service_center_id_list)) {
                    array_push($service_center_id_list, $spare_parts_details[0]['service_center_id']);
                    array_push($pickup_email_details, $spare_parts_details[0]);
                }

                $affected_id = $this->service_centers_model->update_spare_parts(array('spare_parts_details.id' => $spare_part_id), $spare_data);
                if ($affected_id) {
                    /* Insert Spare Tracking Details */
                    if (!empty($spare_part_id)) {
                        $tracking_details = array('spare_id' => $spare_part_id, 'action' => $tracking_status, 'remarks' => '', 'agent_id' => $this->session->userdata('id'), 'entity_id' => _247AROUND, 'entity_type' => _247AROUND_EMPLOYEE_STRING);
                        $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                    }
                    $flag = true;
                }
            }

            $this->send_email_to_pickup_spare_parts($pickup_email_details, $post);

            if ($flag) {
                echo 'success';
            } else {
                echo 'failed';
            }
        }
    }

    /*
     * @desc this function is used to trigger mail to Customers on spare parts approval
     * @param  $part_warranty_status
     * @param  $email_template
     * @param Array $data
     * @param $booking_id
     */

    function send_email_to_pickup_spare_parts($pickup_email_details, $post) {

        foreach ($pickup_email_details as $value) {

            if ($post['request_type'] == 2) {
                $email_template_tag = 'courier_pickup_request';
            } else {
                $email_template_tag = 'courier_pickup_schedule';
            }

            $email_template = $this->booking_model->get_booking_email_template($email_template_tag);
                        
            if (!empty($email_template)) {
                $to = $value['email'] . "," . $post['courier_to_email'];
                $cc = $post['courier_cc_email'] . "," .$email_template[3];
            }

            $subject = vsprintf($email_template[4], array($value['name']));
            $template = array(
                'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
            );

            $this->table->set_template($template);
            $this->table->set_heading(array('Name', 'Address', 'City', 'State', 'District', 'Pincode', 'Phone 1', 'POC Email', 'POC Phone 1'));
            $this->table->add_row($value['name'], $value['address'], $value['district'], $value['state'], $value['district'], $value['pincode'], $value['phone_1'], $value['email'], $value['phone_2']);
            $body_msg = $this->table->generate();
            
            $emailBody = vsprintf($email_template[0], array( strtoupper($post['courier_name']), $body_msg));
            $this->notify->sendEmail($email_template[2], $to, $cc, '', $subject, $emailBody, '', $email_template_tag, '', '');
        }
    }
    
    /**
     * @desc: This function is used to check partner session.
     * @param: void
     * @return: true if details matches else session is destroyed.
     */
    function check_PartnerSession() {
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'partner')) {
            return TRUE;
        } else {
            log_message('info', __FUNCTION__ . " Session Expire for Partner");
            $this->session->sess_destroy();
            redirect(base_url() . "partner/login");
        }
    }
/* 
  *  @desc : This function is used to upload alternate spare parts 
    *  @param : void
    *  @return :void
    */

    function upload_alternate_spare_parts_file($isAdmin = 1) {     
        if($isAdmin == 1) {
            log_message('info', __FUNCTION__ . ' Function Start For Admin '.$this->session->userdata('id'));
            $this->checkUserSession();
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/upload_alternate_spare_parts_mapping');
        }
        else
        {
            log_message('info', __FUNCTION__ . ' Function Start For Partner '.$this->session->userdata('partner_id'));
            $this->check_PartnerSession();
            $this->miscelleneous->load_partner_nav_header();
            $this->load->view('partner/upload_alternate_spare_parts_mapping');
            $this->load->view('partner/partner_footer');
        }
    }
    
    /**
     * @desc This function is used to send MSL data to inventory manager
     */
    function get_msl_data($icwh = 1) {
        ini_set('memory_limit', -1);
        $date_365 = date('Y-m-d', strtotime("-365 Days"));
        $date_45 = date('Y-m-d', strtotime("-45 Days"));
        $date_30 = date('Y-m-d', strtotime("-30 Days"));
        $date_15 = date('Y-m-d', strtotime("-15 Days"));
        
        if($icwh == 1){
            $tmp_subject =  "CWH ";
            $temp_function = 'get_msl_data';
             $template = "msl_data.xlsx";
        } else {
            $tmp_subject =  "MWH ";
            $temp_function = 'get_microwarehouse_msl_data';
            $template = "mwh_msl_data.xlsx";
        }
        $data = $this->inventory_model->$temp_function($date_365);

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                
                $day_45 = $this->inventory_model->$temp_function($date_45, $value['inventory_id']);
                if (!empty($day_45)) {
                    $data[$key]['consumption_45_days'] = $day_45[0]['consumption'];
                } else {
                    $data[$key]['consumption_45_days'] = 0;
                }

                $day_30 = $this->inventory_model->$temp_function($date_30, $value['inventory_id']);
                if (!empty($day_30)) {
                    $data[$key]['consumption_30_days'] = $day_30[0]['consumption'];
                } else {
                    $data[$key]['consumption_30_days'] = 0;
                }

                $day_15 = $this->inventory_model->$temp_function($date_15, $value['inventory_id']);
                if (!empty($day_15)) {
                    $data[$key]['consumption_15_days'] = $day_15[0]['consumption'];
                } else {
                    $data[$key]['consumption_15_days'] = 0;
                }

                $recommended_30 = $data[$key]['consumption_30_days'] - $value['stock'];
                if ($recommended_30 > 0) {
                    $data[$key]['recommended_30_days'] = $recommended_30;
                    
                } else if($recommended_30 == 0){
                    
                    $data[$key]['recommended_30_days']  = $data[$key]['consumption_30_days'];
                    
                } else if($data[$key]['consumption_30_days'] == 0){ 
                    $recommended_45 = $data[$key]['consumption'] - $value['stock'];
                    if($recommended_45 > 0){
                        $data[$key]['recommended_30_days'] = $recommended_45;
                    } else {
                        $data[$key]['recommended_30_days'] = 0;
                    }
                } else {
                    $data[$key]['recommended_30_days'] = 0;
                }
            }
        }
        $user = $this->employee_model->get_employee_by_group(array('groups' => INVENTORY_USER_GROUP, 'active' => 1));

        $email = implode(', ', array_unique(array_map(function ($k) {
                            return $k['official_email'];
                        }, $user)));

        $templateDir = __DIR__ . "/../excel-templates/";

        $output_file_excel = "msldata_" . date('YmdHis') . ".xlsx";
        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );

        $R = new PHPReport($config);
        $R->load(array(
            array(
                'id' => 'parts',
                'repeat' => true,
                'data' => $data,
            )
                )
        );

        ob_end_clean();
        $res1 = 0;
        if (file_exists(TMP_FOLDER . $output_file_excel)) {

            system(" chmod 777 " . TMP_FOLDER . $output_file_excel, $res1);
              unlink($output_file_excel);
        }

        $R->render('excel', TMP_FOLDER . $output_file_excel);

        system(" chmod 777 " . TMP_FOLDER . $output_file_excel, $res1);

       if(!empty($this->session->userdata('session_id'))) {
        $this->load->helper('download');
        $data = file_get_contents(TMP_FOLDER . $output_file_excel);
        force_download($output_file_excel, $data);
        unlink(TMP_FOLDER . $output_file_excel);

       } else {
    
        $email_template = $this->booking_model->get_booking_email_template(SEND_MSL_FILE);
        if (!empty($email_template)) {
            $subject = $tmp_subject.$email_template[4];
            $message = $email_template[0];
            $email_from = $email_template[2];

            $to = $email;
            $cc = $email_template[3];
            $this->notify->sendEmail($email_from, $to, $cc, '', $subject, $message, TMP_FOLDER . $output_file_excel, SEND_MSL_FILE);
             unlink(TMP_FOLDER . $output_file_excel);
        }


       }



    }
    
        /**
     * @desc This function is used to view  spare transfer page
     */
    function bulkConversion(){
        
        
        if($this->session->userdata('userType') == 'service_center'){
            $this->load->view('service_centers/header');
        }else{
          $this->miscelleneous->load_nav_header();  
        }

        $this->load->view('employee/bulk_spare_transfer');
    }

 

    /**
     * @desc This function is used to process spare transfer
     */

    function bulkConversion_process() {
        ob_clean();
        if (empty($this->session->userdata('userType'))) {
         redirect(base_url() . "employee/login");
        }
    
        $agentid='';
        $agent_name='';
        $login_partner_id='';
        $login_service_center_id='';
        if ($this->session->userdata('userType') == 'employee') {
            $agentid=$this->session->userdata('id');
            $agent_name =$this->session->userdata('emp_name');
            $login_partner_id = _247AROUND;
            $login_service_center_id =NULL;
        }else if($this->session->userdata('userType') == 'service_center'){
            $agentid=$this->session->userdata('service_center_agent_id');
            $agent_name =$this->session->userdata('service_center_name');
            $login_service_center_id = $this->session->userdata('service_center_id');
            $login_partner_id =NULL;
           
        }
//        if(empty($agentid)){
//            echo 'fail_agent_id_not_set';
//            exit;
//        }
        $bookingidbulk = trim($this->input->post('bulk_input'));
        $bookingidbulk1 = str_replace("\r", "", $bookingidbulk);
        $bookingids = explode("\n", $bookingidbulk1);
        $bookigs = array();
        foreach ($bookingids as $bbok) {
            $bookigs[] = str_replace("\r", "", $bbok);
        }
        $where = array(
            'spare_parts_details.status' => SPARE_PARTS_REQUESTED,
            'spare_parts_details.entity_type' => _247AROUND_PARTNER_STRING,
            'spare_parts_details.requested_inventory_id IS NOT NULL ' => NULL
        );
        $select = "spare_parts_details.id,spare_parts_details.quantity,spare_parts_details.booking_id,spare_parts_details.model_number, spare_parts_details.entity_type, booking_details.state,spare_parts_details.service_center_id,inventory_master_list.part_number, spare_parts_details.partner_id, booking_details.partner_id as booking_partner_id,spare_parts_details.service_center_id,spare_parts_details.date_of_request,"
                . " requested_inventory_id";
        $post['where_in'] = array('spare_parts_details.booking_id' => $bookigs);
        $post['is_inventory'] = true;
        $bookings_spare = $this->partner_model->get_spare_parts_by_any($select, $where, TRUE, FALSE, false, $post);
        
        $tcount = 0;
        $booking_error_array = array();
        $template = array(
            'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
        );

        $this->table->set_template($template);
        $this->table->set_heading(array('Booking ID', 'Part Name','Spare part ID'));

        list($tcount, $booking_error_array, $add_row) = $this->miscelleneous->spareTransfer($bookings_spare, $agentid, $agent_name, $login_partner_id, $login_service_center_id);

        foreach($add_row as $row_values) {
            $this->table->add_row($row_values);
        }
        
        if (!empty($booking_error_array)) {
            $body_msg = $this->table->generate();
            $template = $this->booking_model->get_booking_email_template("spare_not_transfer_from_wh_to_wh");
            if (!empty($template)) {
                $emailBody = vsprintf($template[0], array($body_msg));
                $subject = "Spare Parts Not Transferred Detail Table";

                $to = '';
                if ($this->session->userdata('userType') == 'employee') {
                    $to = $this->session->userdata('official_email');
                } else if ($this->session->userdata('userType') == 'service_center') {
                    $to = $this->session->userdata('poc_email');
                } else {
                    $to = $template[1];
                }
                $this->notify->sendEmail($template[2], $to, $template[3], $template[5], $subject, $emailBody, "", 'spare_not_transfer_from_wh_to_wh', '');
                
                echo $body_msg;
            }
        } else {
            echo "success";
        }
    }

    /**
     *  @desc : This function is used to updater upload the defective spare shipped by warehouse courier file
     *  @param : $code
     */
 
 
    function update_spare_parts_on_approval($code) {
        log_message('info', __FUNCTION__ . " Spare Parts ID: " . base64_decode(urldecode($code)));
       // $this->checkUserSession();
        $spare_id = base64_decode(urldecode($code));       
        $where = array('spare_parts_details.id'=>$spare_id);
        $select = 'spare_parts_details.id,spare_parts_details.partner_id,spare_parts_details.shipped_quantity,spare_parts_details.quantity, spare_parts_details.date_of_request,spare_parts_details.entity_type,spare_parts_details.booking_id,spare_parts_details.date_of_purchase,spare_parts_details.model_number,'
                . 'spare_parts_details.serial_number,spare_parts_details.serial_number_pic,spare_parts_details.invoice_pic,'
                . 'spare_parts_details.parts_requested,spare_parts_details.parts_requested_type,spare_parts_details.invoice_pic,spare_parts_details.part_warranty_status,'
                . 'spare_parts_details.defective_parts_pic,spare_parts_details.defective_back_parts_pic,spare_parts_details.requested_inventory_id,spare_parts_details.serial_number_pic,spare_parts_details.remarks_by_sc,'
                . 'booking_details.service_id,booking_details.partner_id as booking_partner_id,booking_details.assigned_vendor_id';
        $spare_parts_details = $this->partner_model->get_spare_parts_by_any($select, $where, TRUE, TRUE, false);            
        $data['spare_parts_details'] = $spare_parts_details[0];   
        $where1 = array('entity_id' => $spare_parts_details[0]['booking_partner_id'], 'entity_type' => _247AROUND_PARTNER_STRING, 'service_id' => $spare_parts_details[0]['service_id'], 'inventory_model_mapping.active' => 1,'appliance_model_details.active' => 1);
        $data['inventory_details'] = $this->inventory_model->get_inventory_mapped_model_numbers('appliance_model_details.id,appliance_model_details.model_number', $where1);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/update_spare_parts_form_on_approval', $data);
    }
    
    function get_dispatch_msl_form(){
        log_message('info', __METHOD__);
        $this->load->view('service_centers/header');
        $data['courier_details'] = $this->inventory_model->get_courier_services('*');                     
        $data['saas'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);                
        $this->load->view("service_centers/tag_spare_invoice_send_by_warehouse", $data);
        
    }

    /**
     *  @desc : This function is used to get the post data for booking by status
     *  @param : void()
     *  @return : $post Array()
     */
    private function get_post_data() {
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');

        return $post;
    }

     /**
     *  @desc : This function is used to show all the spare list which was send by partner to warehouse 
     *  @param : void
     *  @return : $res JSON
     */
    function get_spare_send_by_partner_to_wh() {
        log_message('info', __METHOD__ . json_encode($this->input->post(), true));
        $post = $this->get_post_data();
        $post['is_courier_details_required'] = TRUE;
        $post['column_order'] = array();
        $post['column_search'] = array('inventory_master_list.part_name', 'inventory_master_list.type', 'courier_details.AWB_no', 'courier_details.courier_name', 'i.booking_id','sc.name','i.invoice_id');
        $post['where'] = array(
            'i.receiver_entity_type' => trim($this->input->post('receiver_entity_type')),
            'i.sender_entity_id' => trim($this->input->post('sender_entity_id')),
            'i.sender_entity_type' => trim($this->input->post('sender_entity_type')));
            // 'i.is_wh_ack' => $this->input->post('is_wh_ack'));

        $select = "services.services,sc.name as sname,inventory_master_list.*,CASE WHEN(sc.name IS NOT NULL) THEN (sc.name) 
                    WHEN(p.public_name IS NOT NULL) THEN (p.public_name) 
                    WHEN (e.full_name IS NOT NULL) THEN (e.full_name) END as receiver, 
                    CASE WHEN(sc1.name IS NOT NULL) THEN (sc1.name) 
                    WHEN(p1.public_name IS NOT NULL) THEN (p1.public_name) 
                    WHEN (e1.full_name IS NOT NULL) THEN (e1.full_name) END as sender,i.*,courier_details.AWB_no,courier_details.courier_name,courier_details.status";
        $list = $this->inventory_model->get_spare_need_to_acknowledge($post, $select);
        // print_r($this->db->last_query());
        $data = array();
        $no = $post['start'];
        foreach ($list as $inventory_list) {
            $no++;
            $row = $this->get_spare_send_by_partner_to_wh_table($inventory_list, $no);
            $data[] = $row;
        }

        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->inventory_model->count_spare_need_to_acknowledge($post),
            "recordsFiltered" => $this->inventory_model->count_filtered_spare_need_to_acknowledge($post),
            "data" => $data,
        );

        echo json_encode($output);
    }


     /**
     *  @desc : This function is used to generate data for the spare which send by partner to wh
     *  @param : $inventory_list array()
     *  @param : $no string
     *  @return :void
     */
    function get_spare_send_by_partner_to_wh_table($inventory_list, $no) {
        $row = array();

        $row[] = $no;
        if ($this->session->userdata('partner_id')) {
            $row[] = "<a href='" . base_url() . "partner/booking_details/" .$inventory_list->booking_id . "'target='_blank'>" . $inventory_list->booking_id . "</a>";
        } else if ($this->session->userdata('id')) {
            $row[] = "<a href='" . base_url() . "partner/booking_details/" . $inventory_list->booking_id . "'target='_blank'>" . $inventory_list->booking_id . "</a>";
        }
        $row[] = $inventory_list->services;
        $row[] = $inventory_list->invoice_id;
        $row[] = $inventory_list->sname;
        $row[] = $inventory_list->type;
        $row[] = $inventory_list->part_name;
        $row[] = "<span style='word-break: break-all;'>" . $inventory_list->part_number . "</span>";
        $row[] = $inventory_list->quantity;
        $row[] = $inventory_list->description;
        $row[] = $inventory_list->courier_name;
        $a = "<a href='javascript:void(0);' onclick='";
        $a .= "get_msl_awb_details(" . '"' . $inventory_list->courier_name . '"';
        $a .= ', "' . $inventory_list->AWB_no . '"';
        $a .= ', "' . $inventory_list->status . '"';
        $a .= ', "msl_awb_loader_' . $no . '"';
        $a .= ")'>" . $inventory_list->AWB_no . "</a>";
        $a .="<span id='msl_awb_loader_$no' style='display:none;'><i class='fa fa-spinner fa-spin'></i></span>";
        $row[] = $a;

        
        return $row;
    }

    
    
    
     /**
     * @desc function for display view of bulk transfer from warehouse to warehouse
     * @author Abhishek 
     * @since 31-May-2019
     */   
    
    function spare_transfer_from_wh_to_wh(){
        
        $this->load->view('service_centers/header');
        $this->load->view('service_centers/spare_part_transfer_from_wh_to_wh');  
    }
    
   
           /**
     * @desc function to process  bulk transfer from warehouse to warehouse
     * @author Abhishek 
     * @since 31-May-2019
     */    
    function spare_transfer_from_wh_to_wh_process() {

        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'service_center')) {
            
        } else {
            echo PHP_EOL . 'Terminal Access Not Allowed' . PHP_EOL;
            redirect(base_url() . "service_center/login");
        }
        log_message('info', __METHOD__);
        $bookingidbulk = trim($this->input->post('bulk_input'));
        $service_center = trim($this->input->post('service_center'));
        $service_center_to = trim($this->input->post('service_center_to'));
        $bookingidbulk1 = str_replace("\r", "", $bookingidbulk);
        $bookingids = explode("\n", $bookingidbulk1);
        $bookigs = array();
        $template = array(
            'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
        );
        $this->table->set_template($template);
        $this->table->set_heading(array('Booking ID', 'Part Name','Spare part ID'));
        foreach ($bookingids as $bbok) {
            $bookigs[] = str_replace("\r", "", $bbok);
        }
        $where = array(
            'spare_parts_details.status' => SPARE_PARTS_REQUESTED,
            'spare_parts_details.entity_type' => _247AROUND_SF_STRING,
            'spare_parts_details.partner_id' => $service_center,
            'spare_parts_details.requested_inventory_id IS NOT NULL ' => NULL
        );
        $select = "spare_parts_details.id,spare_parts_details.quantity,spare_parts_details.booking_id, booking_details.state,spare_parts_details.service_center_id,inventory_master_list.part_number, spare_parts_details.partner_id, spare_parts_details.model_number, booking_details.partner_id as booking_partner_id,"
                . " requested_inventory_id";
        $post['where_in'] = array('spare_parts_details.booking_id' => $bookingids);
        $post['is_inventory']=true;
        $bookings_spare = $this->partner_model->get_spare_parts_by_any($select, $where, TRUE, FALSE, false, $post);
        $tcount = 0;
        
        if (!empty($bookings_spare)) {
            $booking_error_array = array();
            foreach ($bookings_spare as $booking) {
                $spareid = $booking['id'];
                $state=$booking['state'];
                $requested_inventory = $booking['requested_inventory_id'];
                $requested_part_number = '';
                if(!empty($booking['part_number'])){
                 $requested_part_number = $booking['part_number'];   
                }else{
                 $requested_part_number = '-';  
                }
                $data = $this->miscelleneous->check_inventory_stock($booking['requested_inventory_id'], $booking['booking_partner_id'], $state, "",$booking['model_number']);
                
                if (!empty($data) && $data['stock']>=$booking['quantity']) {
                    if($data['stock']){
                        $dataupdate = array(
                        'is_micro_wh' => $data['is_micro_wh'],
                        'entity_type' => $data['entity_type'],
                        'defective_return_to_entity_id' => $service_center_to,
                        'partner_id' => $service_center_to,
                        'defective_return_to_entity_type' =>_247AROUND_SF_STRING,
                        'challan_approx_value' => $data['challan_approx_value'],
                        'requested_inventory_id' => $data['inventory_id'],
                        'parts_requested' => $data['part_name'],
                        'parts_requested_type' => $data['type']    
                    );
                        
                    $spare_pending_on_to='';
                    $wh_details_to = $this->vendor_model->getVendorContact($service_center_to);
                    if(!empty($wh_details_to)){
                    $spare_pending_on_to = $wh_details_to[0]['district'] . ' Warehouse';   
                    }else{
                    $spare_pending_on_to = ' Warehouse'; 
                    }
                    
                    $spare_pending_on='';
                    $wh_details = $this->vendor_model->getVendorContact($service_center);
                    if(!empty($wh_details)){
                    $spare_pending_on = $wh_details[0]['district'] . ' Warehouse';   
                    }else{
                    $spare_pending_on= ' Warehouse'; 
                    }

                    $remarks = _247AROUND_TRANSFERED_TO_WAREHOUSE;
                    $next_action = _247AROUND_TRANSFERED_TO_NEXT_ACTION;
                    $actor = 'Warehouse';
                    $new_state = 'Spare Part Transferred to ' . $spare_pending_on_to;
                    $old_state = 'Spare Part Transferred from ' . $spare_pending_on;
                    $this->inventory_model->update_spare_courier_details($spareid, $dataupdate);
                    if($this->db->affected_rows()>0){
                     $this->notify->insert_state_change($booking['booking_id'], $new_state, $old_state, $remarks, $this->session->userdata('agent_id'), $this->session->userdata('service_center_name'), $actor, $next_action, NULL,$this->session->userdata('service_center_id') );
                    if ($data['entity_type'] == _247AROUND_SF_STRING) {
                        $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $service_center_to, $data['inventory_id'], $booking['quantity']);
                        $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $service_center, $requested_inventory, -$booking['quantity']);
                    }
                    $tcount++; 
                    }                 
                    }else{
                    $this->table->add_row($booking['booking_id'], $booking['part_number'],$spareid);
                    array_push($booking_error_array, $booking['booking_id']);
                    }
            }else{
                    $this->table->add_row($booking['booking_id'], $booking['part_number'],$spareid);
                    array_push($booking_error_array, $booking['booking_id']);
            }   
        }
            if (!empty($booking_error_array)) {
            $body_msg = $this->table->generate();
            $template = $this->booking_model->get_booking_email_template("spare_not_transfer_from_wh_to_wh");
            if (!empty($template)) {
                $emailBody = vsprintf($template[0], array($body_msg));
                $subject = "Spare Parts Not Transferred Detail Table";

                $to = '';
                if ($this->session->userdata('userType') == 'employee') {
                    $to = $this->session->userdata('official_email');
                } else if ($this->session->userdata('userType') == 'service_center') {
                    $to = $this->session->userdata('poc_email');
                } else {
                    $to = $template[1];
                }
                $this->notify->sendEmail($template[2], $to, $template[3], $template[5], $subject, $emailBody, "", 'spare_not_transfer_from_wh_to_wh', '');
                
                echo $body_msg;
            }
        } else {
            echo "success";
        }
    }
    }



     /**
     *  @desc : This function is used to map GST number to Wareshouse and Partner

     */

     function add_gst_mapping(){

        $this->miscelleneous->load_nav_header();  
        $results['select_state'] = $this->reusable_model->get_search_result_data("state_code","DISTINCT UPPER( state) as state, state_code",NULL,NULL,NULL,array('state'=>'ASC'),NULL,NULL,array());
        $this->load->view('employee/add_gst_mapping',$results);

     }



         /**
    * @desc This is used to process and add gst_details
    *  
    */
    function process_add_gst_details_for_partner(){
        $gst_file = "";
        //Making process for file upload
        if(!empty($this->input->post('partner'))){
            if(!empty($_FILES['gst_file']['name'])){
                $tmpFile = $_FILES['gst_file']['tmp_name'];
                $gst_file = str_replace(' ', '', $this->input->post('gst_number')) . '_gstfile_' . substr(md5(uniqid(rand(0, 9))), 0, 15) . "." . explode(".", $_FILES['gst_file']['name'])[1];
                move_uploaded_file($tmpFile, TMP_FOLDER . $gst_file);

                //Upload files to AWS
                $bucket = BITBUCKET_DIRECTORY;
                $directory_xls = "vendor-partner-docs/" . $gst_file;
                $this->s3->putObjectFile(TMP_FOLDER . $gst_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                $_POST['gst_file'] = $gst_file;
                unlink(TMP_FOLDER . $gst_file);
                //$gst_file_path= "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $gst_file;
            }

            $data=array(
                'entity_type'=>_247AROUND_PARTNER_STRING,
                'entity_id'=>$this->input->post('partner'),
                'state'=>$this->input->post('state'),
                'gst_file'=>$gst_file,
                'gst_number'=>$this->input->post('gst_number')
            );

            $last_id = $this->inventory_model->insert_entity_gst_data($data);

            if ($last_id) {
               $this->session->set_flashdata('success_msg','GST Deatils added successfully !');
            }else{
                $this->session->set_flashdata('error_msg','GST Deatils not  added  !');
            }
        }
        else{
            $this->session->set_flashdata('error_msg', 'Please Select Partner!');
        }
        redirect(base_url() . "employee/spare_parts/add_gst_mapping");
    }
    

    function bulkPartnerConversion(){
        
        if($this->session->userdata('userType') == 'service_center'){
            $this->load->view('service_centers/header');
        }else{
          $this->miscelleneous->load_nav_header();  
        }
         $this->load->view('employee/bulkPartnerTransfer');

    }
    function bulkPartnerConversion_process(){
        if (empty($this->session->userdata('userType'))) {
         redirect(base_url() . "employee/login");
        }
    
        $agentid=_247AROUND_DEFAULT_AGENT;
        $agent_name=_247AROUND_DEFAULT_AGENT_NAME;
        $login_partner_id=_247AROUND;
        $login_service_center_id='';
        if ($this->session->userdata('userType') == 'employee') {
            $agentid=$this->session->userdata('id');
            $agent_name =$this->session->userdata('emp_name');
            $login_partner_id = _247AROUND;
            $login_service_center_id =NULL;
        }else if($this->session->userdata('userType') == 'service_center'){
            $agentid=$this->session->userdata('service_center_agent_id');
            $agent_name =$this->session->userdata('service_center_name');
            $login_service_center_id = $this->session->userdata('service_center_id');
            $login_partner_id =NULL;
           
        }

        $bookingidbulk = trim($this->input->post('bulk_input'));
        $bookingidbulk1 = str_replace("\r", "", $bookingidbulk);
        $bookingids = explode("\n", $bookingidbulk1);
        $bookigs = array();
        foreach ($bookingids as $bbok) {
            $bookigs[] = str_replace("\r", "", $bbok);
        }
        $where = array(
            'spare_parts_details.status IN ("'.SPARE_PARTS_REQUESTED.'") ' => NULL,
            'spare_parts_details.entity_type' => _247AROUND_SF_STRING,
            'spare_parts_details.requested_inventory_id IS NOT NULL ' => NULL
        );
        $select = "spare_parts_details.id,spare_parts_details.quantity,spare_parts_details.booking_id, spare_parts_details.entity_type, booking_details.state,spare_parts_details.service_center_id,inventory_master_list.part_number, spare_parts_details.model_number,spare_parts_details.partner_id, booking_details.partner_id as booking_partner_id,"
                . " requested_inventory_id";
        $post['where_in'] = array('spare_parts_details.booking_id' => $bookigs);
        $post['is_inventory'] = true;
        $bookings_spare = $this->partner_model->get_spare_parts_by_any($select, $where, TRUE, FALSE, false, $post);
        
        $template = array(
            'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
        );

        $this->table->set_template($template);
        $this->table->set_heading(array('Booking ID', 'Part Name','Spare part ID'));
        foreach ($bookings_spare as $booking) {
            $dataupdate=array();
            $delivered_sp=array();
            $data = $this->miscelleneous->check_inventory_stock($booking['requested_inventory_id'], $booking['booking_partner_id'], $booking['state'], $booking['service_center_id'],$booking['model_number']);
            if (!empty($data) && $data['stock'] >= $booking['quantity']) {
                $dataupdate = array(
                        'is_micro_wh' => $data['is_micro_wh'],
                        'entity_type' => $data['entity_type'],
                        'defective_return_to_entity_id' => $data['defective_return_to_entity_id'],
                        'partner_id' => $data['entity_id'],
                        'defective_return_to_entity_type' => $data['defective_return_to_entity_type'],
                        'challan_approx_value' => $data['challan_approx_value'],
                        'requested_inventory_id' => $data['inventory_id'],
                        'parts_requested' => $data['part_name'],
                        'parts_requested_type' => $data['type'],
                        'wh_ack_received_part'=>1
                    );                              
                    $next_action = _247AROUND_TRANSFERED_TO_NEXT_ACTION;

                    $spare_pending_on='';
                    $wh_details = $this->vendor_model->getVendorContact($data['entity_id']);
                    if(!empty($wh_details)){
                    $spare_pending_on = $wh_details[0]['district'] . ' Warehouse';   
                    }else{
                    $spare_pending_on = ' Warehouse'; 
                    }

                    $spare_pending_on2='';
                    $wh_details = $this->vendor_model->getVendorContact($booking['partner_id']);
                    if(!empty($wh_details)){
                    $spare_pending_on2 = $wh_details[0]['district'] . ' Warehouse';   
                    }else{
                    $spare_pending_on2= 'Partner'; 
                    }

                    $actor = '247Around';
                    $new_state = 'Spare Part Transferred to ' . $spare_pending_on;
                    $old_state = 'Spare Part Transferred from ' . $spare_pending_on2;
                    
                if($data['entity_type'] == _247AROUND_SF_STRING){
                    if($data['inventory_id'] != $booking['requested_inventory_id']){
                         $this->inventory_model->update_spare_courier_details($booking['id'], $dataupdate);
                         $remarks = $new_state;
                         $this->notify->insert_state_change($booking['booking_id'], $new_state, $old_state, $remarks, $agentid,$agent_name, $actor, $next_action, $login_partner_id, $login_service_center_id);


                         if ($data['is_micro_wh']==2) {
                         $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $data['entity_id'], $data['inventory_id'], $booking['quantity']);
                         $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $booking['partner_id'], $booking['requested_inventory_id'], -$booking['quantity']);

                         $data_booking=array(
                           'actor'=>$actor,
                           'next_action'=>_247AROUND_TRANSFERED_TO_NEXT_ACTION
                         );
                         $this->booking_model->update_booking($booking['booking_id'],$data_booking); 
                         }


                         if ($data['is_micro_wh']==1) {

                            $dataupdate['spare_id']=$value['id'];
                            $dataupdate['quantity']=$value['quantity'];
                            $dataupdate['model_number']=$booking['model_number'];
                            $dataupdate['date_of_request']=date('Y-m-d');
                            $dataupdate['service_center_id']=$booking['service_center_id'];
                            array_push($delivered_sp,$dataupdate);
                            $this->auto_delivered_for_micro_wh($delivered_sp, $partner_id);
                         }


                    } else if($data['entity_id'] != $booking['partner_id']){
                         $this->inventory_model->update_spare_courier_details($booking['id'], $dataupdate);
                         $remarks = $new_state;
                         $this->notify->insert_state_change($booking['booking_id'], $new_state, $old_state, $remarks, $agentid,$agent_name, $actor, $next_action, $login_partner_id, $login_service_center_id);
                         $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $data['entity_id'], $data['inventory_id'], $booking['quantity']);
                         $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $booking['partner_id'], $booking['requested_inventory_id'], -$booking['quantity']);
                    }
                } else if($data['entity_type'] == _247AROUND_PARTNER_STRING){
                    
                    $this->inventory_model->update_spare_courier_details($booking['id'], $dataupdate);
                    $remarks = $new_state;
                    $actor = 'Partner';
                    $this->notify->insert_state_change($booking['booking_id'], $new_state, $old_state, $remarks, $agentid,$agent_name, $actor, $next_action, $login_partner_id, $login_service_center_id);
                    $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $booking['partner_id'], $booking['requested_inventory_id'], -$booking['quantity']);
                    $data_booking=array(
                    'actor'=>$actor,
                    'next_action'=>'Send Spare Part'
                     );
                     $this->booking_model->update_booking($booking['booking_id'],$data_booking);    
                
                }
                    
            } else {
                
                
               if($booking['entity_type']==_247AROUND_SF_STRING && $data['entity_type'] ==_247AROUND_PARTNER_STRING){
                    
                    $next_action = _247AROUND_TRANSFERED_TO_NEXT_ACTION;
                    $spare_pending_on=_247AROUND_PARTNER_STRING;
                    $spare_pending_on2='Warehouse';

                    $actor = 'Partner';
                    $new_state = 'Spare Part Transferred to ' . $spare_pending_on;
                    $old_state = 'Spare Part Transferred from ' . $spare_pending_on2;   
                    $dataupdate = array(
                        'is_micro_wh' => $data['is_micro_wh'],
                        'entity_type' => $data['entity_type'],
                        'defective_return_to_entity_id' => $data['defective_return_to_entity_id'],
                        'partner_id' => $data['entity_id'],
                        'defective_return_to_entity_type' => $data['defective_return_to_entity_type'],
                        'challan_approx_value' => $data['challan_approx_value'],
                        'requested_inventory_id' => $data['inventory_id'],
                        'parts_requested' => $data['part_name'],
                        'parts_requested_type' => $data['type']
                    ); 
                $data_booking=array(
                    'actor'=>$actor,
                    'next_action'=>'Send Spare Part'
                );
                        
                $this->inventory_model->update_spare_courier_details($booking['id'], $dataupdate);
                $remarks = $new_state;
                $this->notify->insert_state_change($booking['booking_id'], $new_state, $old_state, $remarks, $agentid,$agent_name, $actor, $next_action, $login_partner_id, $login_service_center_id);
                $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $booking['partner_id'], $booking['requested_inventory_id'], -$booking['quantity']);
                $this->booking_model->update_booking($booking['booking_id'],$data_booking); 
                    
        }
                        

            }
        }
      
        echo "success";
    }
    
    /**
     *  @desc : This function is used to download alternate parts list
     *  @param : void
     *  @return : void
     */
    function download_alternate_part_list() {
        $this->checkUserSession();
        $this->miscelleneous->load_nav_header();
        $this->load->view("employee/download_alternet_part_list");
    }
    
     /**
     *  @desc : This function is used to download alternate parts
     *  @param : void
     *  @return : void
     */
    function process_download_alternate_parts(){
        $data = array();
        $partner_id = $this->input->post("partner_id"); 
        $service_id = $this->input->post("service_id"); 
        $select = "inventory_master_list.inventory_id, inventory_master_list.service_id, group_concat(inventory_master_list.part_number) as part_number,appliance_model_details.model_number";
        $where = array("inventory_master_list.entity_type" => _247AROUND_PARTNER_STRING, "inventory_master_list.entity_id" => $partner_id, "inventory_master_list.service_id" => $service_id,'alternate_inventory_set.status' => 1);
        $alternate_parts = $this->inventory_model->get_alternet_parts($select, $where);
        foreach ($alternate_parts as $alternates) {
            $model_number = $alternates['model_number'];
            $parts_array = explode(",", $alternates['part_number']);
            foreach ($parts_array as $key => $value){
                $main_part = $value;
                for($i=$key; $i<count($parts_array); $i++){
                    $parts = $parts_array[$i];
                    if($main_part != $parts){ 
                       array_push($data, array("main_part_code"=>$main_part, "alternate_part_code"=>$parts, "model_number" => $model_number ));   
                    }
                }
                /*
                foreach($parts_array as $parts){
                    if($main_part != $parts){
                        array_push($data, array("main_part_code"=>$main_part, "alternate_part_code"=>$parts)); 
                    }
                }
                */
            }
        }
        $headings = array("Main Part Code", "Alternate Part Code","Model Number");
        $this->miscelleneous->downloadCSV($data, $headings, "alternate_spare_parts");
    }
    
    /*
     *  @desc : This function is used to get spare history.
     *  @param : void
     *  @return : void
     */
    function get_spare_tracking_histroy() {
        $data = array();
        if ($this->input->post("spare_id")) {
            $data['spare_history'] = $this->partner_model->get_spare_state_change_tracking("spare_state_change_tracker.id,spare_state_change_tracker.spare_id,spare_state_change_tracker.action,spare_state_change_tracker.remarks,spare_state_change_tracker.agent_id,spare_state_change_tracker.entity_id,spare_state_change_tracker.entity_type, spare_state_change_tracker.create_date", array('spare_state_change_tracker.spare_id' => $this->input->post("spare_id")), false);
        }
        $this->load->view("employee/spare_history_details",$data);
    }
    
    
    
     /*
     * @desc: Used to create tab in which we are showing
     * Part defective part pending that are Out Of TAT
     * @param: Array $post
     */
     function get_defective_part_out_of_tat_pending($post) {

        $post['select'] = "spare_parts_details.id as spare_id, services.services as appliance,  booking_details.booking_id ,service_centres.name as sf_name,(CASE WHEN service_centres.active = 1 THEN 'Active' ELSE 'Inactive' END) as sf_status, partners.public_name as partner_name, booking_details.current_status as booking_status, "
                . "spare_parts_details.status as spare_status, (CASE WHEN spare_parts_details.part_warranty_status = 1 THEN 'In-Warranty' WHEN spare_parts_details.part_warranty_status = 2 THEN 'Out-Warranty' END) as spare_warranty_status, (CASE WHEN spare_parts_details.nrn_approv_by_partner = 1 THEN 'Approved' ELSE 'Not Approved' END) as nrn_status,  booking_details.request_type as booking_request_type, spare_parts_details.model_number as requested_model_umber, spare_parts_details.parts_requested as requested_part,spare_parts_details.parts_requested_type as requested_part_type, i.part_number as requested_part_number, DATE_FORMAT(spare_parts_details.date_of_request,'%d-%b-%Y') as spare_part_requested_date,"
                . "spare_parts_details.model_number_shipped as shipped_model_number, spare_parts_details.parts_shipped as shipped_part, spare_parts_details.shipped_parts_type, i.part_number as shipped_part_number, DATE_FORMAT(service_center_closed_date,'%d-%b-%Y') as service_center_closed_date,"
                . "DATE_FORMAT(spare_parts_details.shipped_date,'%d-%b-%Y') as spare_part_shipped_date, datediff(CURRENT_DATE,spare_parts_details.shipped_date) as spare_shipped_age,"
                . "challan_approx_value As parts_charge, spare_parts_details.awb_by_partner, spare_parts_details.awb_by_sf, spare_parts_details.awb_by_wh,"
                . "(CASE WHEN spare_parts_details.spare_lost = 1 THEN 'Yes' ELSE 'NO' END) AS spare_lost";

        $post['column_order'] = array(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'spare_parts_details.shipped_date', NULL, NULL, NULL, NULL, NULL);

        $post['column_search'] = array('spare_parts_details.booking_id', 'booking_details.request_type', 'spare_parts_details.awb_by_partner',
            'spare_parts_details.awb_by_sf', 'spare_parts_details.awb_by_wh');

        unset($post['where']['status']);

        $post['where'] = array("DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(spare_parts_details.shipped_date, '%Y-%m-%d')) >= 45" => NULL);
        $post['where']['defective_part_shipped_date IS NULL'] = NULL;
        $post['where']['defective_part_required'] = 1;
        $post['is_inventory'] = TRUE;

        $list = $this->inventory_model->get_out_tat_spare_parts_list($post);

        $no = $post['start'];
        $data = array();
        foreach ($list as $spare_list) {
            $no++;
            $row = $this->out_of_tat_defective_parts_pending_table_data($spare_list, $no);
            $data[] = $row;
        }
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->inventory_model->count_oot_spare_parts($post),
            "recordsFiltered" => $this->inventory_model->count_spare_oot_filtered($post),
            "data" => $data,
        );

        echo json_encode($output);
    }

    /**
     * @desc: This function is used to create table row data for pending Out of TAT spare parts
     * @param: Array $spare_list
     * @param: int $no
     * @return: Array
     */
      function out_of_tat_defective_parts_pending_table_data($spare_list, $no) {

        $row = array();
        $row[] = $no;
        $row[] = $spare_list->booking_id;
        $row[] = "<span class='line_break'>" . $spare_list->sf_name . "</span>";
        $row[] = $spare_list->sf_status;
        $row[] = $spare_list->partner_name;
        $row[] = "<span class='line_break'>" . $spare_list->spare_status . "</span>";
        $row[] = $spare_list->spare_warranty_status;
        $row[] = "<span class='line_break'>" . $spare_list->nrn_status . "</span>";
        $row[] = $spare_list->service_center_closed_date;
        $row[] = "<span class='line_break'>" . $spare_list->booking_request_type . "</span>";
        $row[] = "<span class='line_break'>" . $spare_list->shipped_model_number . "</span>";
        $row[] = "<span class='line_break'>" . $spare_list->shipped_part . "</span>";
        $row[] = "<span class='line_break'>" . $spare_list->shipped_parts_type . "</span>";
        $row[] = "<span class='line_break'>" . $spare_list->shipped_part_number . "</span>";
        $row[] = $spare_list->spare_part_shipped_date;
        $row[] = $spare_list->spare_shipped_age;
        if ($spare_list->spare_shipped_age > 60) {
            $tat = 'Out Of TAT';
        } else {
            $tat = 'Under TAT';
        }
        $row[] = $tat;
        $row[] = "<span class='line_break'>" . $spare_list->awb_by_partner . "</span>";
        $row[] = $spare_list->awb_by_sf;
        $row[] = $spare_list->parts_charge;
        $row[] = $spare_list->awb_by_wh;
        $row[] = $spare_list->spare_lost;

        return $row;
    }

    /*
     * @desc: This Function is used to download the OOT report
     * @param: void
     * @return : Download link
     */

    function download_spare_oot_data() {
        log_message('info', __METHOD__ . ' Processing...');
        ini_set('memory_limit', '256M');
        $download_flag = $this->input->post('download_flag');

      
        $post['select'] = "spare_parts_details.id as spare_id, services.services as 'Appliance',  booking_details.booking_id as 'Booking ID',  booking_details.assigned_vendor_id as 'Assigned Vendor Id', emply.full_name as 'RM Name',empl.full_name as 'ASM Name',service_centres.name as 'SF Name', service_centres.district as 'SF City', service_centres.state as 'SF State', (CASE WHEN service_centres.active = 1 THEN 'Active' ELSE 'Inactive' END) as 'SF Status', partners.public_name as 'Partner Name', GROUP_CONCAT(employee.full_name) as 'Account Manager Name', booking_details.current_status as 'Booking Status', booking_details.partner_current_status as 'Partner Status Level 1', booking_details.partner_internal_status as 'Partner Status Level 2',"
                . "spare_parts_details.status as 'Spare Status', (CASE WHEN spare_parts_details.part_warranty_status = 1 THEN 'In-Warranty' WHEN spare_parts_details.part_warranty_status = 2 THEN 'Out-Warranty' END) as 'Spare Warranty Status', (CASE WHEN spare_parts_details.nrn_approv_by_partner = 1 THEN 'Approved' ELSE 'Not Approved' END) as 'NRN Status', DATE_FORMAT(service_center_closed_date,'%d-%b-%Y') as 'Service Center Closed Date', DATE_FORMAT(booking_details.closed_date,'%d-%b-%Y') as 'Final Closing Date', DATE_FORMAT(spare_parts_details.spare_cancelled_date,'%d-%b-%Y')   as 'Spare Part Cancellation Date', bcr.reason as 'Spare Cancellation Reason', booking_details.request_type as 'Booking Request Type', spare_parts_details.model_number as 'Requested Model Number',spare_parts_details.parts_requested as 'Requested Part',spare_parts_details.parts_requested_type as 'Requested Part Type', i.part_number as 'Requested Part Number', DATE_FORMAT(spare_parts_details.date_of_request,'%d-%b-%Y') as 'Spare Part Requested Date',"
                . "if(spare_parts_details.is_micro_wh='0','Partner',if(spare_parts_details.is_micro_wh='1',concat('Microwarehouse - ',sc.name),sc.name)) as 'Requested On Partner/Warehouse',"
                . "spare_parts_details.model_number_shipped as 'Shipped Model Number',spare_parts_details.parts_shipped as 'Shipped Part',spare_parts_details.shipped_parts_type as 'Shipped Part Type',iml.part_number as 'Shipped Part Number',"
                . "DATE_FORMAT(spare_parts_details.shipped_date,'%d-%b-%Y') as 'Spare Part Shipped Date', datediff(CURRENT_DATE,spare_parts_details.shipped_date) as 'Spare Shipped Age', (CASE WHEN datediff(CURRENT_DATE,spare_parts_details.shipped_date) > 60 THEN 'Out Of TAT' ELSE 'Under TAT' END) as 'TAT', spare_parts_details.awb_by_partner as 'Partner AWB Number',"
                . "spare_parts_details.courier_name_by_partner as 'Partner Courier Name',spare_parts_details.courier_price_by_partner as 'Partner Courier Price',"
                . "partner_challan_number AS 'Partner Challan Number',spare_parts_details.awb_by_sf as 'SF AWB Number',spare_parts_details.courier_name_by_sf as 'SF Courier Name', spare_parts_details.courier_charges_by_sf as 'SF Courier Price', sf_challan_number as 'SF Challan Number',IF(wh.name !='' , wh.name, 'Partner') as 'SF Dispatch Defective Part To Warehouse/Partner',"
                . "DATE_FORMAT(spare_parts_details.acknowledge_date,'%d-%b-%Y') as 'Spare Received Date',spare_parts_details.auto_acknowledeged as 'Is Spare Auto Acknowledge',"
                . "spare_parts_details.defective_part_shipped as 'Part Shipped By SF',challan_approx_value As 'Parts Charge', "
                . " (CASE WHEN spare_parts_details.defective_part_required = 1 THEN 'Yes' ELSE 'NO' END) AS 'Defective Part Required', cci.billable_weight as 'Defective Packet Weight ', cci.box_count as 'Defective Packet Count',"
                . "remarks_defective_part_by_sf as 'Defective Parts Remarks By SF', DATE_FORMAT(defective_part_shipped_date,'%d-%b-%Y') as 'Defective Parts Shipped Date', DATE_FORMAT(received_defective_part_date,'%d-%b-%Y') as 'Partner Received Defective Parts Date', "
                . " (CASE WHEN spare_consumption_status.is_consumed = 1 THEN 'Yes' ELSE 'NO' END) as Consumption, spare_consumption_status.consumed_status as 'Consumption Reason', spare_parts_details.awb_by_wh as 'AWB Number Warehouse Dispatch Defective To Partner',spare_parts_details.courier_name_by_wh as 'Warehouse Dispatch Defective To Partner Courier Name', spare_parts_details.courier_price_by_wh as 'Warehouse Dispatch Defective To Partner Courier Price', spare_parts_details.wh_challan_number AS 'Warehouse Dispatch Defective To Partner Challan Number', DATE_FORMAT(spare_parts_details.wh_to_partner_defective_shipped_date,'%d-%b-%Y') as 'Warehouse Dispatch Defective Shipped Date To Partner',"
                . "if(spare_parts_details.reverse_sale_invoice_id is null,'',spare_parts_details.reverse_sale_invoice_id) as 'Reverse Sale Invoice', "
                . "if(spare_parts_details.reverse_purchase_invoice_id is null,'',spare_parts_details.reverse_purchase_invoice_id) as 'Reverse Purchased Invoice', "
                . "if(spare_parts_details.purchase_invoice_id is null,'',spare_parts_details.purchase_invoice_id) as 'Purchase Invoice', "
                . "if(spare_parts_details.sell_invoice_id is null,'',spare_parts_details.sell_invoice_id) as 'Sale Invoice', "
                . "if(spare_parts_details.warehouse_courier_invoice_id is null,'',spare_parts_details.warehouse_courier_invoice_id) as 'Warehouse Courier Invoice', "
                . "if(spare_parts_details.partner_warehouse_courier_invoice_id is null,'',spare_parts_details.partner_warehouse_courier_invoice_id) as 'Partner Warehouse Courier Invoice', "
                . "if(spare_parts_details.partner_courier_invoice_id is null,'',spare_parts_details.partner_courier_invoice_id) as 'Partner Courier Invoice', "
                . "if(spare_parts_details.vendor_courier_invoice_id is null,'',spare_parts_details.vendor_courier_invoice_id) as 'SF Courier Invoice', "
                . "if(spare_parts_details.partner_warehouse_packaging_invoice_id is null,'',spare_parts_details.partner_warehouse_packaging_invoice_id) as 'Partner Warehouse Packaging Courier Invoice', (CASE WHEN spare_parts_details.spare_lost = 1 THEN 'Yes' ELSE 'NO' END) AS 'Spare Lost'";
      
        $post['where'] = array("DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(spare_parts_details.shipped_date, '%Y-%m-%d')) >= 45" => NULL);
        $post['where']['defective_part_shipped_date IS NULL'] = NULL;
        $post['where']['defective_part_required'] = 1;
        $post['group_by'] = "spare_parts_details.id";

        if (!empty($download_flag)) {
            $spare_details = $this->inventory_model->download_oot_pending_defective_part($post);
            
            
            if ($spare_details) {

                $this->load->dbutil();
                $this->load->helper('file');

                $file_name = 'spare_out_of_tat_data_' . date('j-M-Y-H-i-s') . ".csv";
                $delimiter = ",";
                $newline = "\r\n";
                $new_report = $this->dbutil->csv_from_result($spare_details, $delimiter, $newline);
                write_file(TMP_FOLDER . $file_name, $new_report);

                if (file_exists(TMP_FOLDER . $file_name)) {
                    log_message('info', __FUNCTION__ . ' File created ' . $file_name);
                    $res1 = 0;
                    system(" chmod 777 " . TMP_FOLDER . $file_name, $res1);
                    $res['status'] = true;
                    $res['msg'] = base_url() . "file_process/downloadFile/" . $file_name;
                } else {
                    log_message('info', __FUNCTION__ . ' error in generating file ' . $file_name);
                    $res['status'] = FALSE;
                    $res['msg'] = 'error in generating file';
                }

                echo json_encode($res);
            }
        }
    }

}
