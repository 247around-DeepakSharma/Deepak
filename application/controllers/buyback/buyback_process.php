<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}


error_reporting(E_ALL);
ini_set('display_errors', '1');

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 36000);

class Buyback_process extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('buyback');
        $this->load->model('vendor_model');
        $this->load->model('booking_model');
        $this->load->model('reusable_model');
        $this->load->model('invoices_model');
        $this->load->model("service_centre_charges_model");
        $this->load->library('PHPReport');
        $this->load->library('push_notification_lib');
        $this->load->library('booking_utilities');
        $this->load->dbutil();
        $this->load->helper('file');

        if (($this->session->userdata('loggedIn') == TRUE) && $this->session->userdata('userType') == 'employee') {
            return TRUE;
        } else {
            echo PHP_EOL . 'Terminal Access Not Allowed' . PHP_EOL;
            redirect(base_url() . "employee/login");
        }
    }
    /**
     * @desc Used to load order details view
     */
    function view_bb_order_details() {
        log_message("info",__METHOD__);
        $data['saas_flag'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'),$data);
        $this->load->view('buyback/get_order_details');
        $this->load->view('dashboard/dashboard_footer');
    }
    /*
     * @desc Used to get data as requested and also search 
     */
    function get_bb_order_details() {
        //log_message("info",__METHOD__);
       // $tmp ='{"draw":"2","columns":[{"data":"0","name":"","searchable":"true","orderable":"false","search":{"value":"","regex":"false"}},{"data":"1","name":"","searchable":"true","orderable":"false","search":{"value":"","regex":"false"}},{"data":"2","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"3","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"4","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"5","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"6","name":"","searchable":"true","orderable":"false","search":{"value":"","regex":"false"}},{"data":"7","name":"","searchable":"true","orderable":"false","search":{"value":"","regex":"false"}}],"start":"0","length":"50","search":{"value":"","regex":"false"},"date_range":"2017\/07\/01 - 2017\/07\/31","city":"Ghaziabad\n","service_id":"","current_status":"","internal_status":"","status":"2"}';
       // $_POST = json_decode($tmp, true);
        $data = array();
        switch ($this->input->post('status')){
            case 0:
                $data = $this->process_in_tansit();
                break;
            
            case 1:
                $data = $this->process_delivered();
                break;
            
            case 2:
                $data = $this->process_unassigned();
                break;
            case 3:
                $data = $this->process_lost_other();
                break;
            
            case 4:
                $data = $this->process_cancelled_not_claim();
                break;
            
            case 5:
                $data = $this->process_cancelled_claim_submitted();
                break;
            
             case 6:
                $data = $this->process_cancelled_claim_settled();
                break;
            
            case 7:
                $data = $this->process_30_days_tat_breech_not_claim();
                break;
            
             case 8:
                $data = $this->process_30_days_tat_breech_claimed_data(CLAIM_SUBMITTED);
                break;
            
             case 9:
                $data = $this->process_30_days_tat_breech_claimed_data(CLAIM_SETTLED);
                break;
             case 10:
                $data = $this->advanced_bb_search();
                break;
            case 11:
                $data = $this->get_vendor_rejected_data();
                break;
            case 12:
                $data = $this->get_vendor_rejected_order_claimed_data(CLAIM_SUBMITTED);
                break;
            case 13:
                $data = $this->get_vendor_rejected_order_claimed_data(CLAIM_APPROVED);
                break;
            case 14:
                $data = $this->get_vendor_rejected_order_claimed_data(CLAIM_REJECTED);
                break;
            case 15:
                $data = $this->get_vendor_rejected_order_claimed_data(CLAIM_SETTLED);
                break;
            case 16:
                $data = $this->process_30_days_tat_breech_claimed_data(CLAIM_APPROVED);
                break;
            case 17:
                $data = $this->process_30_days_tat_breech_claimed_data(CLAIM_REJECTED);
                break;
            case 18:
                $data = $this->process_30_days_tat_breech_claimed_data(CLAIM_DEBIT_NOTE_RAISED);
                break;
            case 19:
                $data = $this->get_vendor_rejected_order_claimed_data(CLAIM_DEBIT_NOTE_RAISED);
                break;
            case 20:
                $data = $this->get_bb_claimed_raised_order_data();
                break;
        }
        
        $post = $data['post'];
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->bb_model->count_all($post),
            "recordsFiltered" =>  $this->bb_model->count_filtered($post),
            "data" => $data['data'],
        );
        unset($post);
        unset($data);
        echo json_encode($output);
    }
    /**
     * @desc get In-Transit data
     * @return type
     */
    function process_in_tansit(){
       
        $post = $this->get_bb_post_view_data();
        $post['where'] = array('assigned_cp_id IS NOT NULL' => NULL, 'order_date >= ' => date('Y-m-d',strtotime(TAT_BREACH_DAYS)));
        $post['where_in'] = array('current_status' => array('In-Transit', 'New Item In-transit', 'Attempted'));
        $post['column_order'] = array( NULL, NULL,'services', 'city','order_date', 'current_status');
        $post['column_search'] = array('bb_unit_details.partner_order_id','bb_order_details.partner_tracking_id','services', 'city','order_date','current_status');
        $list = $this->bb_model->get_bb_order_list($post);
        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row =  $this->in_tansit_table_data($order_list, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
                );

    }
    
    function advanced_bb_search(){
       // log_message("info",__METHOD__.json_encode($_POST, TRUE));
        
        $post1 = $this->get_bb_post_view_data();
        $post = $this->_advanced_bb_search($post1);
        $list = $this->bb_model->get_bb_order_list($post);
        
        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row =  $this->search_table_data($order_list, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
                );
    }
    
    function _advanced_bb_search($post){
        $date_range = $this->input->post("order_date");
        $delivery_date = $this->input->post("delivery_date");
        $city = $this->input->post("city");
        $service_id = $this->input->post("service_id");
        $current_status = $this->input->post("current_status");
        $internal_status = $this->input->post("internal_status");
        $invoice = $this->input->post("invoice");
        $cp_id = $this->input->post("cp_id");
        $acknowledge_date = $this->input->post("acknowledge_date");
        $post['where'] = array();
        $post['where_in'] = array();
        if(!empty($date_range)){
            $order_date = explode("-", $date_range);
            $post['where']['order_date >= '] =  date("Y-m-d", strtotime(trim($order_date[0])));
            $post['where']['order_date < '] = date('Y-m-d', strtotime('+1 day', strtotime(trim($order_date[1]))));
        }
        if($invoice == "Yes"){
            $post['where']['cp_invoice_id IS NOT NULL '] =  NULL;
            
        } else if($invoice == "No"){
            $post['where']['cp_invoice_id IS NULL '] =  NULL;
        }
        if(!empty($delivery_date)){
            $d_date = explode("-", $delivery_date);
            $post['where']['delivery_date >= '] =  date("Y-m-d", strtotime(trim($d_date[0])));
            $post['where']['delivery_date < '] = date('Y-m-d', strtotime('+1 day', strtotime(trim($d_date[1]))));
        }
        if(!empty($acknowledge_date)){
            $d_date = explode("-", $acknowledge_date);
            $post['where']['bb_order_details.acknowledge_date >= '] =  date("Y-m-d", strtotime(trim($d_date[0])));
            $post['where']['bb_order_details.acknowledge_date < '] = date('Y-m-d', strtotime('+1 day', strtotime(trim($d_date[1]))));
        }
        if(!empty($city)){
             $post['where_in']['city'] = !is_array($city)?explode(',', $city):$city;
        }
        if(!empty($service_id)){
             $post['where_in']['service_id'] = !is_array($service_id)?explode(',', $service_id):$service_id;
        }
        if(!empty($internal_status)){
             $post['where_in']['internal_status'] = !is_array($internal_status)?explode(',', $internal_status):$internal_status;;
        }
       
        if(!empty($current_status)){
             $post['where_in']['current_status'] = !is_array($current_status)?explode(',', $current_status):$current_status;;
        }
        
        if(!empty($cp_id)){
             $post['where']['assigned_cp_id'] = $cp_id;
        }
        
        
        $post['column_order'] = array( NULL, NULL,NULL,'services','category', 'city','order_date',NULL, 'current_status');
        $post['column_search'] = array('bb_unit_details.partner_order_id','bb_order_details.partner_tracking_id','category','services', 'city','order_date','current_status');
        
        return $post;
    }
    
    function process_delivered(){
        //log_message("info",__METHOD__);
        $post = $this->get_bb_post_view_data();
        $post['where'] = array('assigned_cp_id IS NOT NULL' => NULL);
        $post['where_in'] = array('current_status' => array('Delivered'), 'internal_status' => array('Delivered'));
        $post['column_order'] = array( NULL, NULL,'services', 'city','order_date', 'delivery_date', 'current_status');
        $post['column_search'] = array('bb_unit_details.partner_order_id','bb_order_details.partner_tracking_id','services', 'city','order_date','delivery_date','current_status');
        $list = $this->bb_model->get_bb_order_list($post);
        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row =  $this->delivered_table_data($order_list, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
            
                );

    }
    
    function process_unassigned(){
        //log_message("info",__METHOD__);
        $post = $this->get_bb_post_view_data();
        $post['where'] = array('assigned_cp_id IS NULL' => NULL, 'order_date >= ' => date('Y-m-d', strtotime(TAT_BREACH_DAYS)));
        $post['where_in'] = array('current_status' => array('In-Transit', 'New Item In-transit', 'Attempted','Delivered'));
        $post['column_order'] = array( NULL, NULL,'services', 'city','order_date', 'current_status');
        $post['column_search'] = array('bb_unit_details.partner_order_id','bb_order_details.partner_tracking_id','services', 'city','order_date','current_status');
        $list = $this->bb_model->get_bb_order_list($post);
        $data = array();
        $no = $post['start'];
        $select = "bb_shop_address.id, concat(name,'( ' ";
       
        $select .= ",shop_address_region ";
        $select .= " ) as cp_name";
        $shop_list = $this->bb_model->get_cp_shop_address_details(array(), $select, "name");
        foreach ($list as $order_list) {
            $no++;
            $row =  $this->unassigned_table_data($order_list, $shop_list, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
            
                );

    }
    
    function process_lost_other(){
        //log_message("info",__METHOD__);
        $post = $this->get_bb_post_view_data();
        $post['where'] = array('order_date >= ' => date('Y-m-d', strtotime(TAT_BREACH_DAYS)));
        $post['where_in'] = array('current_status' => array('Lost', 'Unknown'));
        $post['column_order'] = array( NULL, NULL,'services', 'city','order_date', 'current_status');
        $post['column_search'] = array('bb_unit_details.partner_order_id','services', 'city','order_date','current_status');
        $list = $this->bb_model->get_bb_order_list($post);
        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row =  $this->generic_table_data($order_list, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
            
                );

    }
    
    function process_cancelled_not_claim(){
        //log_message("info",__METHOD__);
        $post = $this->get_bb_post_view_data();
        $post['where_in'] = array(
            'current_status' => array('Cancelled', 'Rejected'), 
            'internal_status' => array(TO_BE_CLAIMED));
        $post['column_order'] = array( NULL, NULL,'services', 'city','order_date', 'current_status');
        $post['column_search'] = array('bb_unit_details.partner_order_id','services', 'city','order_date','current_status');
        $post['where'] = array();
        $list = $this->bb_model->get_bb_order_list($post);
        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row =  $this->generic_table_data($order_list, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
            
                );

    }
    
    function process_cancelled_claim_submitted(){
        //log_message("info",__METHOD__);
        $post = $this->get_bb_post_view_data();
       
        $post['where_in'] = array(
            'current_status' => array('Cancelled', 'Rejected'), 
            'internal_status' => array(CLAIM_SUBMITTED));
        $post['column_order'] = array( NULL, NULL,'services', 'city','order_date', 'current_status');
        $post['column_search'] = array('bb_unit_details.partner_order_id','services', 'city','order_date','current_status');
        $post['where'] = array();
        $list = $this->bb_model->get_bb_order_list($post);
        
        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row =  $this->generic_table_data($order_list, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
            
                );

    }
    
    function process_cancelled_claim_settled(){
        //log_message("info",__METHOD__);
        $post = $this->get_bb_post_view_data();
       
        $post['where_in'] = array(
            'current_status' => array('Cancelled', 'Rejected'), 
            'internal_status' => array(CLAIM_SETTLED_BY_AMAZON));
        $post['column_order'] = array( NULL, NULL,'services', 'city','order_date', 'current_status');
        $post['column_search'] = array('bb_unit_details.partner_order_id','services', 'city','order_date','current_status');
        $post['where'] = array();
        $list = $this->bb_model->get_bb_order_list($post);
        
        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row =  $this->generic_table_data($order_list, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
            
                );

    }
    
    function process_30_days_tat_breech_not_claim(){
        //log_message("info",__METHOD__);
        $post = $this->get_bb_post_view_data();
       
        $post['where_in'] = array('current_status' => array(_247AROUND_BB_TO_BE_CLAIMED),
            'internal_status' => array(_247AROUND_BB_ORDER_TAT_BREACH));
        $post['column_order'] = array( NULL, NULL,'services', 'city','order_date', 'current_status');
        $post['where'] = array();
        $post['column_search'] = array('bb_unit_details.partner_order_id','services', 'city','order_date','current_status');
        $list = $this->bb_model->get_bb_order_list($post);
        
        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row =  $this->generic_table_data($order_list, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
            
                );
    }
    
    function get_vendor_rejected_data(){
        //log_message("info",__METHOD__);
        $post = $this->get_bb_post_view_data();
       
        $post['where_in'] = array('current_status' => array(_247AROUND_BB_DELIVERED,_247AROUND_BB_TO_BE_CLAIMED),
            'internal_status' => array(_247AROUND_BB_ORDER_NOT_RECEIVED_INTERNAL_STATUS,_247AROUND_BB_REPORT_ISSUE_INTERNAL_STATUS,_247AROUND_BB_NOT_DELIVERED,_247AROUND_BB_ORDER_MISMATCH));
        $post['column_order'] = array( NULL, NULL,'services', 'city','order_date', 'current_status');
        $post['where'] = array();
        $post['column_search'] = array('bb_unit_details.partner_order_id','services', 'city','order_date','current_status');
        $list = $this->bb_model->get_bb_order_list($post);
        
        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row =  $this->to_be_claimed_not_delivered($order_list, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
            
                );
    }
    
    function process_30_days_tat_breech_claimed_data($claimed_type){
        //log_message("info",__METHOD__);
        $post = $this->get_bb_post_view_data();
        
        switch ($claimed_type){
            case CLAIM_SUBMITTED:
                $post['where_in'] = array('current_status' => array(CLAIM_SUBMITTED),
                                          'internal_status' => array(_247AROUND_BB_ORDER_TAT_BREACH));
                $post['where'] = array();
                break;
            case CLAIM_APPROVED:
                $post['where_in'] = array('current_status' => array(CLAIM_APPROVED),
                    'internal_status' => array(_247AROUND_BB_ORDER_TAT_BREACH));
                $post['where'] = array();
                break;
            case CLAIM_REJECTED:
                $post['where_in'] = array('current_status' => array(CLAIM_REJECTED),
                    'internal_status' => array(_247AROUND_BB_ORDER_TAT_BREACH));
                $post['where'] = array();
                break;
            case CLAIM_SETTLED:
                $post['where_in'] = array('current_status' => array(CLAIM_SETTLED),
                    'internal_status' => array(_247AROUND_BB_ORDER_TAT_BREACH));
                $post['where'] = array();
                break;
            case CLAIM_DEBIT_NOTE_RAISED:
                $post['where_in'] = array('current_status' => array(CLAIM_DEBIT_NOTE_RAISED),
                    'internal_status' => array(_247AROUND_BB_ORDER_TAT_BREACH));
                $post['where'] = array();
                break;
                
        }
    
        $post['column_order'] = array( NULL, NULL,'services', 'city','order_date', 'current_status');
        $post['column_search'] = array('bb_unit_details.partner_order_id','services', 'city','order_date','current_status');
        $list = $this->bb_model->get_bb_order_list($post);
        
        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row =  $this->generic_table_data($order_list, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
            
                );
    }
    
    function process_30_days_tat_breech_claim_settled(){
        //log_message("info",__METHOD__);
         $post = $this->get_bb_post_view_data();
       
        $post['where_in'] = array('current_status' => array('In-Transit', 'New Item In-transit', 'Attempted','Lost', 'Unknown'),
            'internal_status' => array(CLAIM_SETTLED_BY_AMAZON));
        $post['column_order'] = array( NULL, NULL,'services', 'city','order_date', 'current_status');
        $post['where'] = array();
        $post['column_search'] = array('bb_unit_details.partner_order_id','services', 'city','order_date','current_status');
        $list = $this->bb_model->get_bb_order_list($post);
        
        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row =  $this->generic_table_data($order_list, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
            
                );
    }
    
    function get_bb_post_view_data(){
        //log_message("info",__METHOD__);
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
        $post['status'] = $this->input->post('status');
        
        return $post;
    }
    
    function in_tansit_table_data($order_list, $no){
        //log_message("info",__METHOD__);
        $row = array();
        $row[] = $no;
        $row[] = "<a target='_blank' href='".base_url()."buyback/buyback_process/view_order_details/".
                $order_list->partner_order_id."'>$order_list->partner_order_id</a>";
        $row[] = $order_list->tracking_id;
        $row[] = $order_list->services;
        $row[] = $order_list->city;
        $row[] = $order_list->order_date;
        $row[] = $order_list->current_status;
        $row[] = $order_list->partner_basic_charge;
        $row[] = ($order_list->cp_basic_charge + $order_list->cp_tax_charge);
        $row[] = "<div class='dropdown'>
                            <button class='btn btn-default dropdown-toggle' type='button' id='menu1' data-toggle='dropdown'>Actions
                            <span class='caret'></span></button>
                            <ul class='dropdown-menu' role='menu' aria-labelledby='menu1'>
                              <li role='presentation'><a role='menuitem' tabindex='-1' onclick=showDialogueBox('" . base_url() . "buyback/buyback_process/update_received_bb_order/" . rawurlencode($order_list->partner_order_id) . "/" . rawurlencode($order_list->service_id) . "/" . rawurlencode($order_list->city) . "/" . rawurlencode($order_list->assigned_cp_id) . "')>Received</a></li>
                              <li role='presentation'><a role='menuitem' tabindex='-1' target='_blank' href='".base_url()."buyback/buyback_process/update_bb_report_issue_order_details/".rawurlencode($order_list->partner_order_id)."/".rawurlencode($order_list->service_id)."/".rawurlencode($order_list->city)."/".rawurlencode($order_list->assigned_cp_id)."/".rawurlencode($order_list->current_status)."'>Broken/Wrong Product</a></li>
                            </ul>
                          </div>";
        
        return $row;
    }
    
    function delivered_table_data($order_list, $no){
        //log_message("info",__METHOD__);
        $row = array();
        $row[] = $no;
        $row[] = "<a target='_blank' href='".base_url()."buyback/buyback_process/view_order_details/".
                $order_list->partner_order_id."'>$order_list->partner_order_id</a>";
        $row[] = $order_list->tracking_id;
        $row[] = $order_list->services;
        $row[] = $order_list->city;
        $row[] = $order_list->order_date;
        $row[] = $order_list->delivery_date;
        $row[] = $order_list->current_status;
        $row[] = $order_list->partner_basic_charge;
        $row[] = ($order_list->cp_basic_charge + $order_list->cp_tax_charge);
        $row[] = "<div class='dropdown'>
                            <button class='btn btn-default dropdown-toggle' type='button' id='menu1' data-toggle='dropdown'>Actions
                            <span class='caret'></span></button>
                            <ul class='dropdown-menu' role='menu' aria-labelledby='menu1'>
                              <li role='presentation'><a role='menuitem' tabindex='-1' onclick=showDialogueBox('" . base_url() . "buyback/buyback_process/update_received_bb_order/" . rawurlencode($order_list->partner_order_id) . "/" . rawurlencode($order_list->service_id) . "/" . rawurlencode($order_list->city) . "/" . rawurlencode($order_list->assigned_cp_id) . "')>Received</a></li>
                              <li role='presentation'><a role='menuitem' tabindex='-1' onclick=showDialogueBox('".base_url()."buyback/buyback_process/update_not_received_bb_order/".rawurlencode($order_list->partner_order_id)."/".rawurlencode($order_list->service_id)."/".rawurlencode($order_list->city)."/".rawurlencode($order_list->assigned_cp_id)."')>Not Received</a></li>
                              <li role='presentation'><a role='menuitem' tabindex='-1' target='_blank' href='".base_url()."buyback/buyback_process/update_bb_report_issue_order_details/".rawurlencode($order_list->partner_order_id)."/".rawurlencode($order_list->service_id)."/".rawurlencode($order_list->city)."/".rawurlencode($order_list->assigned_cp_id)."/".rawurlencode($order_list->current_status)."'>Broken/Wrong Product</a></li>
                            </ul>
                          </div>";
        
        return $row;
    }
    
    function unassigned_table_data($order_list, $shop_list, $no){
        //log_message("info",__METHOD__);
        $row = array();
        $row[] = $no;
        $row[] = "<a target='_blank' href='".base_url()."buyback/buyback_process/view_order_details/".
                $order_list->partner_order_id."'>$order_list->partner_order_id</a>";
        $row[] = $order_list->tracking_id;
        $row[] = $order_list->services;
        $row[] = $order_list->city;
        $row[] = $order_list->order_date;
        $row[] = $order_list->current_status;
        $row[] = $order_list->partner_basic_charge;
        $cp_list =  '<select name="assign_cp_id['.$order_list->partner_order_id.']" ui-select2  class="assign_cp_id"  class="form-control" 
                data-placeholder="Select CP" style="width:200px;"><option value="" selected disabled>Select CP</option> ';
        foreach ($shop_list as $value) {
            $cp_list .= '<option value="'.$value['id'].'">'.$value['cp_name'].'</option>';
        }
        $cp_list.=  '</select>';
        
        $row[] = $cp_list;

        return $row;
    }
    
    function generic_table_data($order_list, $no, $is_new_row=""){
        //log_message("info",__METHOD__);
        $row = array();
        $row[] = $no;
        $row[] = "<a target='_blank' href='".base_url()."buyback/buyback_process/view_order_details/".
                $order_list->partner_order_id."'>$order_list->partner_order_id</a>";
        $row[] = $order_list->services;
        $row[] = $order_list->city;
        $row[] = $order_list->order_date;
        $row[] = $order_list->current_status;
        if($is_new_row){
            $row[] = $order_list->internal_status;
        }
        $row[] = $order_list->partner_basic_charge;
        $row[] = "<input type ='checkbox' class = 'form control'>";

        return $row;
    }
    
    function to_be_claimed_not_delivered($order_list, $no){
        //log_message("info",__METHOD__);
        $row = array();
        $row[] = $no;
        $row[] = "<a target='_blank' href='".base_url()."buyback/buyback_process/view_order_details/".
                $order_list->partner_order_id."'>$order_list->partner_order_id</a>";
        $row[] = $order_list->services;
        $row[] = $order_list->city;
        $row[] = $order_list->order_date;
        $row[] = $order_list->internal_status;
        $row[] = $order_list->partner_basic_charge;
        $row[] = "<input type = 'checkbox' class = 'form-control'>";

        return $row;
    }


    /**
     * @desc Used to show the view of buyback order detailed list for review
     * @param void
     * @return void
     */
    function bb_order_review($days = NULL){
        log_message("info",__METHOD__);
        $data['saas_flag'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'),$data);
        $this->load->view('buyback/bb_order_review',array('days'=>$days));
        $this->load->view('dashboard/dashboard_footer');
    }
    
    
    /**
     * @desc Used to get buyback order detailed list for review
     * @param void
     * @return $output json
     */
    function get_bb_review_order_details(){
        $where = NULL;
        if($this->input->post('days')){
            $days = urldecode($this->input->post('days'));
            if($days == 'Older than 30 Days'){
                $where['DATEDIFF(CURRENT_DATE,order_date) > 30'] = NULL;
            }
            else{
                 $where['DATEDIFF(CURRENT_DATE,order_date) <= 30'] = NULL;
            }
        }
        log_message("info",__METHOD__);
        $length = $this->input->post('length');
        $start = $this->input->post('start');
        $search = $this->input->post('search');
        $search_value = $search['value'];
        $order = $this->input->post('order');
        $draw = $this->input->post('draw');
        $list = $this->bb_model->get_bb_review_order_list($length, $start, $search_value, $order,NULL,$where);
        
        $data = array();
        $no = $start;
        foreach ($list as $order_list) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = "<a target='_blank' href='".base_url()."buyback/buyback_process/view_order_details/".
                $order_list->partner_order_id."'>$order_list->partner_order_id</a>";
            $row[] = $order_list->partner_tracking_id;
            $row[] = $order_list->name;
            $row[] = $order_list->category;
            $row[] = $order_list->brand;
            //$row[] = $order_list->physical_condition;
            //$row[] = $order_list->working_condition;
            $row[] = $order_list->cp_claimed_price;
            $row[] = $order_list->cp_price;
            $row[] = $order_list->partner_price;
            if($order_list->internal_status === _247AROUND_BB_ORDER_NOT_RECEIVED_INTERNAL_STATUS){
                $row[] = "<span class='label label-warning'>$order_list->internal_status</span>";
            }else if($order_list->internal_status === _247AROUND_BB_Damaged_STATUS){
                $row[] = "<span class='label label-danger'>$order_list->internal_status</span>";
            }
            $row[] = $order_list->remarks;
            
            if($order_list->internal_status === _247AROUND_BB_ORDER_NOT_RECEIVED_INTERNAL_STATUS){
                $row[] = "<a class='btn btn-info btn-sm' target='_blank' href='".base_url()."buyback/buyback_process/get_bb_order_image_link/".$order_list->partner_order_id."/".$order_list->cp_id."' disabled><i class='fa fa-camera'></i></a>";
            }else if($order_list->internal_status === _247AROUND_BB_Damaged_STATUS){
                $row[] = "<a class='btn btn-info btn-sm' target='_blank' href='".base_url()."buyback/buyback_process/get_bb_order_image_link/".$order_list->partner_order_id."/".$order_list->cp_id."'><i class='fa fa-camera'></i></a>";
            }
            $r = "<a class='btn btn-danger btn-sm' href='javascript:void(0)' onclick='";
            $r .= "open_reject_approve_model(".'"'.$order_list->partner_order_id.'"';
            $r .= ', "'.$order_list->internal_status.'"';
            $r .= ', "'.$order_list->cp_claimed_price.'", "Reject Order", "rejected"';
            $r .= ")' ><i class='fa fa-times-circle'></i></a>";
            
            $row[] = $r;
            $a = "<a class='btn btn-success btn-sm' href='javascript:void(0)' onclick='";
            $a .= "open_reject_approve_model(".'"'.$order_list->partner_order_id.'"';
            $a .= ', "'.$order_list->internal_status.'"';
            $a .= ', "'.$order_list->cp_claimed_price.'", "Approve Order", "approved"';
            $a .= " )' ><i class='glyphicon glyphicon-ok'></i></a>";
            
            $row[] = $a;
             $row[] = "<label><input type='checkbox' class='flat check_single_row' id='approved_data' data-id='".$order_list->partner_order_id."' data-status='".$order_list->internal_status."' data-cp_claimed_price='".$order_list->cp_claimed_price."'></label>";
           
            $data[] = $row;
        }


        $output = array(
            "draw" => $draw,
            "recordsTotal" => $this->bb_model->count_all_review_order(),
            "recordsFiltered" => $this->bb_model->count_filtered_review_order($search_value, $order,$where),
            "data" => $data,
        );

        //output to json format
        echo json_encode($output);
    }
    
    
    /**
     * @desc Used to get buyback order image link
     * @param $partner_order_id string
     * @param $cp_id string
     * @return void
     */
    function get_bb_order_image_link($partner_order_id, $cp_id) {
        log_message("info",__METHOD__);
        $select = "image_name";
        $where = array("partner_order_id" => $partner_order_id, "cp_id" => $cp_id);
        $data['image_list'] = $this->bb_model->get_bb_order_images($where, $select);
        $data['saas_flag'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'),$data);
        $this->load->view('buyback/bb_order_image_review',$data);
        $this->load->view('dashboard/dashboard_footer');
    }
    function get_order_appliance_and_price($orderID){
        $appliancePriceArray = $this->bb_model->get_bb_detail('service_id,(partner_basic_charge+partner_tax_charge) as price', array("partner_order_id" => $orderID));
        echo json_encode($appliancePriceArray[0]);
    }
    
    /**
     * @desc Used to approve buyback order in bulk
     * @param void
     * @return string
     */
    function approve_reject_bb_order() {
        log_message("info", __METHOD__);
        $order_details_data = array();
        $update_bb_unit_data = array();
        $bb_cp_order_details_data = array();
        if ($this->input->post()) {
            $flag = FALSE;
            $order_ids = explode(',', $this->input->post('order_ids'));
            $status = explode(',', $this->input->post('status'));
            $type = $this->input->post('type');
            $cp_claimed_price = explode(',', $this->input->post('cp_claimed_price'));
            $remarks = $this->input->post('remarks');
            
            if ($type === 'approved') {
                foreach ($order_ids as $key => $value) {
                    $order_details_data = array();
                    $update_bb_unit_data = array();
                    $bb_cp_order_details_data = array();
                    $appliancePriceArray = $this->bb_model->get_bb_detail('service_id,(partner_basic_charge+partner_tax_charge) as price', array("partner_order_id" => $value));
                    switch ($status[$key]) {
                        case _247AROUND_BB_ORDER_NOT_RECEIVED_INTERNAL_STATUS:

                            //update buyback order details
                            $order_details_data['current_status'] = _247AROUND_BB_TO_BE_CLAIMED;
                            $order_details_data['internal_status'] = _247AROUND_BB_NOT_DELIVERED;
                            $order_details_data['acknowledge_date'] = date('Y-m-d H:i:s');

                            //update buyback cp order action
                            $bb_cp_order_details_data['current_status'] = _247AROUND_BB_NOT_DELIVERED;
                            $bb_cp_order_details_data['internal_status'] = _247AROUND_BB_247APPROVED_STATUS;
                            $bb_cp_order_details_data['closed_date'] = date('Y-m-d H:i:s');
                            $bb_cp_order_details_data['admin_remarks'] = $remarks;
                            
                            $update_bb_unit_data['order_status'] = _247AROUND_BB_NOT_DELIVERED;
                            if($appliancePriceArray[0]['service_id'] == _247AROUND_TV_SERVICE_ID){
                                if($this->input->post('amazon_discount')){
                                    $update_bb_unit_data['partner_discount'] = $appliancePriceArray[0]['price'];
                                }
                            }

                            break;

                        case _247AROUND_BB_Damaged_STATUS:

                            //update buyback order details
                            $order_details_data['current_status'] = _247AROUND_BB_TO_BE_CLAIMED;
                            $order_details_data['internal_status'] = _247AROUND_BB_ORDER_MISMATCH;
                            $order_details_data['acknowledge_date'] = date('Y-m-d H:i:s');

                            //update buyback cp order action
                            $bb_cp_order_details_data['current_status'] = _247AROUND_BB_Damaged_STATUS;
                            $bb_cp_order_details_data['internal_status'] = _247AROUND_BB_247APPROVED_STATUS;
                            $bb_cp_order_details_data['admin_remarks'] = $remarks;
                            $bb_cp_order_details_data['closed_date'] = date('Y-m-d H:i:s');
                            $update_bb_unit_data['partner_discount'] = 0;
                            if($this->input->post('amazon_discount')){
                                $update_bb_unit_data['partner_discount'] = $appliancePriceArray[0]['price']-$this->input->post('amazon_discount');
                            }

                            $gst_amount = $this->buyback->gst_amount_on_profit($value, $cp_claimed_price[$key], $update_bb_unit_data['partner_discount']);
                            //insert cp_claimed_price in bb_unit_details
                            $update_bb_unit_data['cp_claimed_price'] = $cp_claimed_price[$key];
                            $update_bb_unit_data['order_status'] = _247AROUND_BB_DELIVERED;
                            $update_bb_unit_data['gst_amount'] = $gst_amount;
                            
                            break;
                    }
                    $flag = $this->process_approve_reject_bb_order($order_details_data, $bb_cp_order_details_data, $value, $update_bb_unit_data);
                }
            } else if ($type === 'rejected') {
                $bb_cp_order_details_data['current_status'] = _247AROUND_PENDING;
                $bb_cp_order_details_data['internal_status'] = _247AROUND_BB_DELIVERED;
                $bb_cp_order_details_data['admin_remarks'] = $remarks;
                $flag = $this->process_approve_reject_bb_order($order_details_data, $bb_cp_order_details_data, $order_ids[0], $update_bb_unit_data);
            }

            echo $flag;
        } else {
            echo "Invalid Request";
        }
    }

    function process_approve_reject_bb_order($order_details_data,$bb_cp_order_details_data,$partner_order_id,$update_bb_unit_data) {
        $flag = FALSE;
        if(!empty($order_details_data)){
            $actionType = "Approved";
            $update_order_details = $this->bb_model->update_bb_order_details(array('partner_order_id' => $partner_order_id), $order_details_data);
            $new_state = $order_details_data['current_status'];
            $remarks = $bb_cp_order_details_data['admin_remarks'];
            
        }else{
            $actionType = "Rejected";
            $update_order_details = TRUE;
            $new_state = _247AROUND_BB_ORDER_REJECTED;
            $remarks = $bb_cp_order_details_data['admin_remarks'];
        }

        if ($update_order_details) {
            $update_cp_unit_details = $this->cp_model->update_bb_cp_order_action(array('partner_order_id' => $partner_order_id), $bb_cp_order_details_data);

            if ($update_cp_unit_details) {
                
                if(!empty($update_bb_unit_data)){
                    $this->bb_model->update_bb_unit_details(array('partner_order_id' => $partner_order_id),$update_bb_unit_data);
                }
                
                $this->send_notification_for_updated_order($actionType,$bb_cp_order_details_data,$update_bb_unit_data,$partner_order_id);
                
                $this->buyback->insert_bb_state_change($partner_order_id, $new_state, $remarks, $this->session->userdata('id'), _247AROUND, NULL);
                $flag = TRUE;
                
            } else {
                $flag = FALSE;
            }
        } else {
            $flag = FALSE;
        }
        
        return $flag;
    }
    
    function send_notification_for_updated_order($actionType,$bb_cp_order_details_data,$update_bb_unit_data,$partner_order_id){
        $msg = "";
        $subject = "";
        if($actionType == 'Approved'){
            $cpBasicChargesArray = $this->reusable_model->get_search_result_data("bb_unit_details","cp_basic_charge,cp_tax_charge",array("partner_order_id"=>$partner_order_id),NULL,NULL,NULL,NULL,NULL,array());
            $finalAmount = $cpBasicChargesArray[0]['cp_basic_charge']+$cpBasicChargesArray[0]['cp_tax_charge'];
            $msg = $msg."Order ID : ".$partner_order_id;
            $msg = $msg."<br>Basic Charge : " .$finalAmount;
            $msg = $msg." <br>Claimed Price  : " .$update_bb_unit_data['cp_claimed_price'];
            $msg = $msg."<br>Remarks : ".$bb_cp_order_details_data['admin_remarks'];
            $subject  = $subject."Order Approved By 247Around";
        }
        else{
            $msg = $msg."Order ID : ".$partner_order_id;
            $msg = $msg."<br>Remarks : ".$bb_cp_order_details_data['admin_remarks'];
            $subject  = $subject."Order Rejected By 247Around";
        }
        $join['service_centres sc'] = "sc.id = bb.cp_id";
        $cpDetails = $this->reusable_model->get_search_result_data("bb_cp_order_action bb","bb.cp_id,sc.primary_contact_email",array("bb.partner_order_id"=>$partner_order_id),
                $join,NULL,NULL,NULL,NULL,array());
        //Send Email to poc
        $to = $cpDetails[0]['primary_contact_email'];
        $cc = $this->session->userdata('official_email');
        $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, NULL, $subject, $msg, NULL,"order_rejected_email_to_cp");
        //End Send Email
        //Send Notification 
//        $receiverArray['vendor'] = array($cpDetails[0]['cp_id']);
//        $notificationTextArray['msg'] = array($partner_order_id,$actionType);
//        $this->push_notification_lib->create_and_send_push_notiifcation(ORDER_REJECT_INFORM_TO_CP, $receiverArray, $notificationTextArray);
        //End Push Notification
    }

    
    
    /**
     * @desc Used to get the order details data to take action 
     * @param $partner_order_id string
     * @return void
     */
    function view_order_details($partner_order_id){
        log_message("info",__METHOD__);
        $data['partner_order_id'] = $partner_order_id;
        $data['saas_flag'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'),$data);
        $this->load->view('buyback/view_bb_order_details',$data);
        $this->load->view('dashboard/dashboard_footer');
    }
    
    
    /**
     * @desc Used to get the order details data to take action 
     * @param $partner_order_id string
     * @return $data json
     */
    function get_bb_order_details_data($partner_order_id){
        log_message("info",__METHOD__);
        if($partner_order_id){
            $data = $this->bb_model->get_bb_order_details(
                    array('bb_order_details.partner_order_id' =>$partner_order_id),
                    'bb_order_details.*, name as cp_name, public_name as partner_name');
            print_r(json_encode($data));
        }
        
    }
    
    
    /**
     * @desc Used to get order history data
     * @param $partner_order_id string
     * @return $data json
     */
    function get_bb_order_history_details($partner_order_id){
        log_message("info",__METHOD__);
        if($partner_order_id){
            $data = $this->bb_model->get_bb_order_history($partner_order_id);
            print_r(json_encode($data));
        }
    }
    
    
    /**
     * @desc Used to get the order appliance details
     * @param $partner_order_id string
     * @return $data json
     */
    function get_bb_order_appliance_details($partner_order_id){
        log_message("info",__METHOD__);
        if($partner_order_id){
            $select = 'bb_unit.category, bb_unit.physical_condition, 
                bb_unit.working_condition,
                round(bb_unit.partner_basic_charge + bb_unit.partner_tax_charge) as partner_charge,
                round(bb_unit.cp_basic_charge + bb_unit.cp_tax_charge) as cp_tax, 
                round(bb_unit.around_commision_basic_charge + bb_unit.around_commision_tax) as around_charges,
                bb_unit.partner_sweetner_charges,s.services as service_name,bb_unit.cp_claimed_price,bb_unit.order_status,bb_unit.partner_invoice_id,bb_unit.cp_invoice_id,
                bb_unit.partner_discount,bb_unit.cp_discount';
            $data = $this->bb_model->get_bb_order_appliance_details(array('partner_order_id' => $partner_order_id), $select);
            print_r(json_encode($data));
        }
    }
    
    function disputed_auto_settel(){
        log_message("info",__METHOD__);
        $data['saas_flag'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'),$data);
        $this->load->view('buyback/get_disputed_auto_settle');
        $this->load->view('dashboard/dashboard_footer');
    }
    
    function disputed_30_days_breech(){
        log_message("info",__METHOD__);
        $data['saas_flag'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'),$data);
        $this->load->view('buyback/get_disputed_30_days_breech');
        $this->load->view('dashboard/dashboard_footer');
    }
    function download_bb_shop_address() {
        log_message("info",__METHOD__);
        $shop_address_data = $this->bb_model->download_bb_shop_address_data();

        $shop_address_file = $this->generate_shop_address_data($shop_address_data);

        if (file_exists($shop_address_file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($shop_address_file) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($shop_address_file));
            readfile($shop_address_file);
            exit;
        }
    }

    private function generate_shop_address_data($shop_address_data){
        log_message("info",__METHOD__);
        $template = 'BB_Shop_Address.xlsx';
        //set absolute path to directory with template files
        $templateDir = FCPATH . "application/controllers/excel-templates/";

        //set config for report
        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );

        //load template
        if(ob_get_length() > 0) {
            ob_end_clean();
        }
        $R = new PHPReport($config);

        $R->load(array(
            array(
                'id' => 'data',
                'repeat' => true,
                'data' => $shop_address_data
            )
                )
        );

        //Get populated XLS with data
        $output_file = TMP_FOLDER . "Shop_Address_" . date('d-M-Y') . ".xlsx";
        $R->render('excel', $output_file);
        
        return $output_file;
    }
    function search_for_buyback(){
        log_message("info",__METHOD__);
        $search_data =   $this->input->post('search');
        //$search_data =  preg_replace('/[^A-Za-z0-9-]/', '', $this->input->post('search'));
        if(strpos($search_data,',')){
            $search_value = explode(',', $search_data);
        }else{
            $search_value = explode(" ", $search_data);
        }
        $post['column_search'] = array('bb_order_details.partner_order_id', 'bb_order_details.partner_tracking_id');
        $post['where'] = array();
        $post['where_in'] = array();
        $post['column_order'] = array();
        $post['length'] = -1;
        $list['list'] = array();
        $search_value = array_unique($search_value);
        foreach($search_value as $value){
            if(!empty($value)){
                $post['search_value'] = trim($value);
                $data = $this->bb_model->get_bb_order_list($post);
                if(!empty($data)){
                    array_push($list['list'], $data[0]);
                }
                
            }
        }
        $select = "bb_shop_address.id,bb_shop_address.cp_id, concat(name,'( ' ";
        $select = "bb_shop_address.id,bb_shop_address.cp_id,name,concat(name,'( ' ";
       
        $select .= ",shop_address_region ";
        $select .= " ) as cp_name";
        $list['shop_list'] = $this->bb_model->get_cp_shop_address_details(array(), $select, "name");
        $this->load->view('buyback/bb_search_result', $list);
    }
    /**
     * @desc This function is used to show update form for those buyback order which order received by
     *       the collection partner.
     * @param string $order_id
     * @param string $service_id
     * @param string $city
     * @param string $cp_id
     * @return void();
     */
    
    function update_received_bb_order($order_id, $service_id, $city, $cp_id) {
        log_message("info", __METHOD__);
        
        $data['order_id'] = rawurldecode($order_id);
        $data['service_id'] = rawurldecode($service_id);
        $data['city'] = rawurldecode($city);
        $data['cp_id'] = rawurldecode($cp_id);

        $response = $this->buyback->process_update_received_bb_order_details($data);

        if ($response['status'] === 'success') {
            $this->session->set_userdata('success', $response['msg']);
            redirect(base_url() . 'buyback/buyback_process/view_bb_order_details');
        } else if ($response['status'] === 'error') {
            $this->session->set_userdata('error', $response['msg']);
            redirect(base_url() . 'buyback/buyback_process/view_bb_order_details');
        }
    }
    
    
    /**
     * @desc This function is used to update the buyback order when order was not received by
     *       the collection partner.
     * @param string $order_id
     * @param string $service_id
     * @param string $city
     * @param string $cp_id
     * @return void();
     */
    function update_not_received_bb_order($order_id, $service_id, $city, $cp_id) {
        log_message("info",__METHOD__);
        $data['order_id'] = rawurldecode($order_id);
        $data['service_id'] = rawurldecode($service_id);
        $data['city'] = rawurldecode($city);
        $data['cp_id'] = rawurldecode($cp_id);
        
        $update_data = array('current_status' => _247AROUND_BB_NOT_DELIVERED,
                             'internal_status' => _247AROUND_BB_247APPROVED_STATUS,
                             'acknowledge_date' => date('Y-m-d H:i:s')
                            );
        
        $update_where = array('partner_order_id' => $data['order_id'], 'cp_id' => $data['cp_id']);
        
        //update cp action table
        $update_id = $this->cp_model->update_bb_cp_order_action($update_where,$update_data);
        
        if($update_id){
            
            //update order_details
            $where = array('partner_order_id' => $data['order_id']);
            $order_details_update_data = array('current_status' => _247AROUND_BB_TO_BE_CLAIMED, 'internal_status' => _247AROUND_BB_NOT_DELIVERED,'acknowledge_date' => date('Y-m-d H:i:s'));
            $order_details_update_id = $this->bb_model->update_bb_order_details($where, $order_details_update_data);
            if(!empty($order_details_update_id)){
                $this->bb_model->update_bb_unit_details(array('partner_order_id' => $data['order_id']),array('order_status' => _247AROUND_BB_NOT_DELIVERED));
                $this->buyback->insert_bb_state_change($data['order_id'], _247AROUND_BB_IN_PROCESS, '', $this->session->userdata('id'), _247AROUND, NULL);
                $this->session->set_userdata('success', 'Order has been updated successfully');
                redirect(base_url().'buyback/buyback_process/view_bb_order_details');
            }else{
                $this->session->set_userdata('error', 'Oops!!! There are some issue in updating order. Please Try Again...');
                redirect(base_url().'buyback/buyback_process/view_bb_order_details');
            }
            
        }else{
            $this->session->set_userdata('error', 'Oops!!! There are some issue in updating order. Please Try Again...');
            redirect(base_url().'buyback/buyback_process/view_bb_order_details');
        }
    }
    function assigned_bb_unassigned_data(){
        log_message("info",__METHOD__);
        $array['where'] = array('assigned_cp_id IS NULL' => NULL);
        $array['where_in'] = array('current_status' => array('In-Transit', 'New Item In-transit', 'Attempted','Delivered'));
        $array['column_order'] = array();
        $array['column_search'] = array();
        $array['length'] = -1;
        $array['search_value'] = "";
        $unassigned_order_data = $this->bb_model->get_bb_order_list($array);
        $not_assigned = array();
       
        if(!empty($unassigned_order_data)){
            foreach ($unassigned_order_data as  $value){

                //Get CP id from shop address table.
                $cp_data = $this->buyback->get_cp_id_from_region($value->city);
                if(!empty($cp_data)){
                    if(stripos($value->order_key,'IMEI') !== FALSE){
                        $order_key = rtrim(trim(explode('IMEI',$value->order_key)[0]),":");
                    }else{
                        $order_key = $value->order_key;
                    }
                    
                    $s_order_key = str_replace(":","",$order_key);
                    $s_order_key1 = str_replace("_","",$s_order_key);
                    
                    $b_charges = array();
                    //Get Charges list
                    foreach($cp_data as $cp_unique_data){
                        $is_exist = array('partner_id' => $value->partner_id,
                                              'city' => $cp_unique_data['shop_address_city'],
                                              'order_key' => $s_order_key1,
                                              'cp_id' => $cp_unique_data['cp_id']
                                    );
                        if(!empty($is_exist)){
                            array_push($b_charges, $is_exist);
                        }
                    }
                    
                    $status = $this->buyback->update_assign_cp_process($b_charges, $value->partner_order_id, 1, $value->internal_status);
                    if(!$status['status']){
                       array_push($not_assigned, array('order_id' =>$value->partner_order_id,"message" => $status['msg']));
                    }
                    
                }else{
                    array_push($not_assigned, array("order_id" => 
                            $value->partner_order_id, "message" => "City Not Found"));
                }
            }
        }
        if(!empty($not_assigned)){
            $output = array('status' => -247, 'error' =>$not_assigned);
        } else {
            $output = array('status' => 247);
        }
        
        echo json_encode($output);
     
    }  
        
    function bb_order_search(){
        log_message("info",__METHOD__);
        $data['saas_flag'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'),$data);
        $this->load->view('buyback/advanced_search');
        $this->load->view('dashboard/dashboard_footer');
    }
    
    function get_advanced_search_optionlist(){
        log_message("info",__METHOD__);
       
        $data['city'] = $this->bb_model->get_bb_order(array('city !=' => ''),"city as district", "city");
        $data['service'] = $this->booking_model->selectservice();
        $data['current_status'] = $this->bb_model->get_bb_order(array(),"current_status", "current_status");
        $data['internal_status'] = $this->bb_model->get_bb_order(array(),"internal_status",  "internal_status");
        $select = "bb_shop_address.id, concat(name,'( ' ";
       
        $select .= ",shop_address_region ";
        $select .= " ) as cp_name";
        $data['shop_list'] = $this->bb_model->get_cp_shop_address_details(array(), $select, "name");
        $data['cp_list'] = $this->bb_model->get_cp_shop_address_details(array(), "cp_id, name as cp_name", "name");
      
        echo json_encode($data);
    }
    
     function search_table_data($order_list, $no){
       // log_message("info",__METHOD__);
        $row = array();
        $row[] = $no;
        $row[] = "<a target='_blank' href='".base_url()."buyback/buyback_process/view_order_details/".
                $order_list->partner_order_id."'>$order_list->partner_order_id</a>";
        $row[] = $order_list->tracking_id;
        $row[] = $order_list->services;
        $row[] = $order_list->category;
        $row[] = $order_list->city;
        $row[] = $order_list->order_date;
        $row[] = $order_list->delivery_date;
        $row[] = $order_list->acknowledge_date;
        $row[] = $order_list->current_status;
        $row[] = $order_list->partner_basic_charge;
        $row[] = ($order_list->cp_basic_charge + $order_list->cp_tax_charge);
        $row[] =  '<select name="assign_cp_id['.$order_list->partner_order_id.']" ui-select2  class="assign_cp_id"  class="form-control" 
                data-placeholder="Select CP" style="width:200px;">
                <option value="" selected disabled>Select CP</option>   
                </select>';
        
        
        return $row;
    }
    
    function vendor_rejected(){
        log_message("info",__METHOD__);
        $data['saas_flag'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'),$data);
        $this->load->view('buyback/get_vendor_rejected');
        $this->load->view('dashboard/dashboard_footer');
    }
    
    /**
     * @desc Used to show the buyback order details on cp panel
     * @param $order_id string
     * @param $service_id string
     * @param $city string
     * @return void
     */
    function update_bb_report_issue_order_details($order_id,$service_id,$city,$cp_id,$current_status){
        $data['order_id'] = rawurldecode($order_id);
        $data['service_id'] = rawurldecode($service_id);
        $data['city'] = rawurldecode($city);
        $data['cp_id'] = rawurldecode($cp_id);
        $data['current_status'] = rawurldecode($current_status);
        $data['products'] = $this->booking_model->selectservice();
        $data['cp_basic_charge'] = $this->bb_model->get_bb_order_appliance_details(array('partner_order_id'=> $data['order_id']),'cp_basic_charge');
        $data['saas_flag'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'),$data);
        $this->load->view('buyback/update_bb_order_details',$data);
        $this->load->view('dashboard/dashboard_footer');
    }
    
    
    /**
     * @desc Update Those order for which report issue was claimed by collection partner
     * @param $post_data array()
     * @return $response array()
     */
    
    function process_report_issue_bb_order_details() {
        log_message("info", __METHOD__);
        $request_data['select'] = "bb_cp_order_action.current_status";
        $request_data['length'] = -1;
        $request_data['where_in'] = array();
        $request_data['where'] = array('bb_cp_order_action.current_status' => _247AROUND_BB_IN_PROCESS,
            "bb_cp_order_action.partner_order_id" => $this->input->post('order_id'));
        $is_inProcess = $this->cp_model->get_bb_cp_order_list($request_data);

        if (!empty($is_inProcess)) {
            $msg = "Order Already Uploaded";
            $this->session->set_userdata('error', $msg);
            redirect(base_url() . 'buyback/buyback_process/update_bb_report_issue_order_details/' .
                    $this->input->post('order_id') . '/' . $this->input->post('service_id') . '/' .
                    $this->input->post('city') . '/' . $this->input->post('cp_id'). '/' . $this->input->post('current_status'));
        } else {
            //check input field validation
            $this->form_validation->set_rules('order_id', 'Order Id', 'trim|required');
            $this->form_validation->set_rules('remarks', 'Remarks', 'trim|required');
            $this->form_validation->set_rules('order_working_condition', 'Order Working Condition', 'trim');
            $this->form_validation->set_rules('category', 'Category', 'trim|required');
            $this->form_validation->set_rules('cp_id', 'Collection Partner Id', 'trim|required');
            $this->form_validation->set_rules('claimed_price', 'Claimed Price', 'trim');

            if ($this->form_validation->run() === false) {
                $msg = "Please fill all required field";
                $this->session->set_userdata('error', $msg);
                redirect(base_url() . 'buyback/buyback_process/update_bb_report_issue_order_details/' . $this->input->post('order_id') . '/' . $this->input->post('service_id') . '/' . $this->input->post('city') . '/' . $this->input->post('cp_id') . '/' . $this->input->post('current_status'));
            } else {

                $order_id = $this->input->post('order_id');
                $remarks = $this->input->post('remarks');
                $working_condition = $this->input->post('order_working_condition');
                $category = $this->input->post('category');
                $cp_id = $this->input->post('cp_id');
                $cp_claimed_price = $this->input->post('claimed_price');
                $order_brand = $this->input->post('order_brand');
                $order_key = $this->input->post('partner_order_key');
                $physical_condition = $this->input->post('order_physical_condition');

                $upload_images = $this->buyback->process_bb_report_issue_upload_image($this->input->post());
                if (isset($upload_images['status']) && $upload_images['status'] == 'error') {
                    $this->session->set_userdata('error', $upload_images['msg']);
                    redirect(base_url() . 'buyback/buyback_process/update_bb_report_issue_order_details/' . $this->input->post('order_id') . '/' . $this->input->post('service_id') . '/' . $this->input->post('city') . '/' . $this->input->post('cp_id') . '/' . $this->input->post('current_status'));
                } else {

                    $physical_condition = isset($physical_condition) ? $physical_condition : '';
                    if (!empty($physical_condition)) {
                        $physical_condition = $physical_condition;
                    } else {
                        $physical_condition = '';
                    }
                    
                    $data = array(
                        'category' => $category,
                        'physical_condition' => $physical_condition,
                        'working_condition' => $working_condition,
                        'remarks' => $remarks,
                        'brand' => $order_brand,
                        'current_status' => _247AROUND_BB_NOT_DELIVERED,
                        'internal_status' => _247AROUND_BB_247APPROVED_STATUS,
                        'order_key' => $order_key,
                        'cp_claimed_price' => $cp_claimed_price,
                        'acknowledge_date' => date('Y-m-d H:i:s'),
                        'closed_date' => date('Y-m-d H:i:s'));

                    $where = array('partner_order_id' => $order_id, 'cp_id' => $cp_id);
                    //update bb_cp_action_table
                    $update_id = $this->cp_model->update_bb_cp_order_action($where, $data);
                    if ($update_id) {
                        log_message("info", __METHOD__ . "Cp Action table updated for order id: " . $order_id);
                        $order_details_where = array('partner_order_id' => $order_id, 'assigned_cp_id' => $cp_id);
                        $order_details_data['current_status'] = _247AROUND_BB_TO_BE_CLAIMED;
                        $order_details_data['internal_status'] = _247AROUND_BB_ORDER_MISMATCH;
                        $order_details_data['acknowledge_date'] = date('Y-m-d H:i:s');
                        $order_details_data['is_delivered'] = 1;
                         if($this->input->post('current_status') == _247AROUND_BB_IN_TRANSIT){
                             $order_details_data['delivery_date'] = date('Y-m-d');
                         }
                         
                        //update order details table
                        $order_details_update_id = $this->bb_model->update_bb_order_details($order_details_where, $order_details_data);
                        if (!empty($order_details_update_id)) {
                            log_message("info", __METHOD__ . "Order Details table updated for order id: " . $order_id);
                            $gst_amount = $this->buyback->gst_amount_on_profit($order_id, $cp_claimed_price, 0);
                            $this->bb_model->update_bb_unit_details(array('partner_order_id' => $order_id), 
                                    array('cp_claimed_price' => $cp_claimed_price,
                                        'gst_amount' =>$gst_amount,
                                        'order_status' => _247AROUND_BB_DELIVERED));
                            if($this->input->post('current_status') == _247AROUND_BB_IN_TRANSIT){
                                $this->buyback->insert_bb_state_change($order_id, _247AROUND_BB_DELIVERED, "Delivered", $this->session->userdata('id'), _247AROUND, Null);
                            }
                            $this->buyback->insert_bb_state_change($order_id, _247AROUND_BB_TO_BE_CLAIMED, $remarks, $this->session->userdata('id'), _247AROUND, Null);

                            $this->session->set_userdata('success', 'Order has been updated successfully');
                            redirect(base_url() . 'buyback/buyback_process/view_bb_order_details');
                        } else {
                            $this->session->set_userdata('error', 'Oops!!! There are some issue in updating order. Please Try Again...');
                            redirect(base_url() . 'buyback/buyback_process/update_bb_report_issue_order_details/' . $this->input->post('order_id') . '/' . $this->input->post('service_id') . '/' . $this->input->post('city') . '/' . $this->input->post('cp_id'). '/' . $this->input->post('current_status'));
                        }
                    } else {
                        $this->session->set_userdata('error', 'Oops!!! There are some issue in updating order. Please Try Again...');
                        redirect(base_url() . 'buyback/buyback_process/update_bb_report_issue_order_details/' . $this->input->post('order_id') . '/' . $this->input->post('service_id') . '/' . $this->input->post('city') . '/' . $this->input->post('cp_id'). '/' . $this->input->post('current_status'));
                    }
                }
            }
        }
    }

    function validate_claimed_price(){
        $cp_claimed_price = $this->input->post('claimed_price');
        $cp_basic_charge = $this->input->post('cp_basic_charge');
        $final_price = $cp_basic_charge * .30;
        
        if($cp_claimed_price < $final_price){
            $flag = FALSE;
        }else{
            $flag = TRUE;
        }
        
        return $flag;
    }
    
    
     /**
     * @desc This function is used to download the amazon price list
     * @param void
     * @return void
     */
     public function download_price_list_data() {
        $cp_id = $this->input->post("cp_id");
        $service_name_arr = $this->input->post('appliance_name');
        $service_cp_id = $this->input->post("service_cp_id");
        $csv_file = array();
        if (!empty($cp_id)) {
            //$key = cp_id, $value = cp_name
            foreach ($cp_id as $key => $value) {
                $select = "brand as Brand,category as Type, concat(physical_condition, ' | ',working_condition) as 'Product Condition' , city AS 'Location' , partner_total as 'Exchange Offer Value'";
                $csv_file_name = TMP_FOLDER . "buyback_price_list_" . strtolower(str_replace(" ", "_", $value));
                $where = array('bb_charges.partner_id' => AMAZON_SELLER_ID, 'visible_to_partner' => 1, 'bb_shop_address.active' => 1, 'bb_charges.cp_id' => $key);
                $data = $this->service_centre_charges_model->get_bb_charges($where, $select, true, true, "", "", TRUE);
                if (!empty($data)) {
                    $file_name = $csv_file_name . ".csv";
                    $csv_file[$file_name] = $this->generate_bb_csv_price_list($file_name, $data);
                }
            }
        } else if ($service_name_arr) {
            //If all is selected then download all appliance data
            $key = array_search('all', $service_name_arr);
            if ($key !== FALSE) {
                $service_name_arr = array_column($this->booking_model->selectservice(true), 'services', 'id');
            }
            $key2 = array_search('all', $service_cp_id);
            if ($key2 !== FALSE) {
                $service_cp_id = array_column($this->vendor_model->getVendorDetails("id, name", array('is_cp' => 1)), 'id');
            }
            $where_in['bb_charges.cp_id'] = $service_cp_id;
            foreach ($service_name_arr as $key => $value) {
                $csv_file_name = TMP_FOLDER . "buyback_price_list_" . strtolower(str_replace(" ", "_", $value));
                $where = array('bb_charges.partner_id' => AMAZON_SELLER_ID, 'visible_to_partner' => 1, 'bb_shop_address.active' => 1, 'bb_charges.service_id' => $key);
                //get total data and divide it from 500 to insert only 500 data at a time in one csv
                $total_data = $this->service_centre_charges_model->get_bb_charges($where, 'count(bb_charges.id) as total_data', true, true,null,null,false,$where_in);
                if (!empty($total_data)) {
                    $row_limit = 498;
                    $counter = ceil($total_data[0]['total_data'] / $row_limit);
                    $offset = 0;
                    for ($i = 0; $i < $counter; $i++) {
                        if ($key == _247AROUND_MOBILE_SERVICE_ID) {
                            $select = "brand as Brand, physical_condition as 'Model' , city AS 'Location' , partner_total as 'Exchange Offer Value'";
                        } else if ($key == _247AROUND_AC_SERVICE_ID) {
                            $select = "category as Type,brand as Brand, working_condition as 'Working Condition' , city AS 'Location' , partner_total as 'Exchange Offer Value'";
                        } else if ($key == _247AROUND_TV_SERVICE_ID) {
                            $select = "brand as Brand,SUBSTRING_INDEX(category,'_',1) as Type,SUBSTRING_INDEX(category,'_',-1) as 'Size', city AS 'Location' , partner_total as 'Exchange Offer Value'";
                        } else {
                            $select = "brand as Brand,category as Type, concat(physical_condition, ' | ',working_condition) as 'Product Condition' , city AS 'Location' , partner_total as 'Exchange Offer Value'";
                        }
                        $data = $this->service_centre_charges_model->get_bb_charges($where, $select, true, true, $offset, $row_limit, TRUE,$where_in);
                        if (!empty($data)) {
                            $file_name = $csv_file_name . "_" . $i . ".csv";
                            $csv_file[$file_name] = $this->generate_bb_csv_price_list($file_name, $data);
                        }
                        $offset += 498;
                    }
                }
            }
        }

        if (!empty($csv_file)) {
            //zipped all the files and download it
            $this->load->library('zip');

            foreach (array_keys($csv_file) as $value) {
                $this->zip->read_file($value);
                $res1 = 0;
                system("chmod 777" . $value, $res1);
                unlink($value);
            }

            $this->zip->download('buyback_price_list.zip');
        } else {
            echo "Empty data submitted";
        }
    }

    /**
     * @desc This is used to generate TV price sheet
     * @return String
     */                   
    function generate_tv_price_sheet() {
        //load PHPExcel Library
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Ariestania")
                ->setLastModifiedBy("Ariestania")
                ->setTitle("Sample Report");

        //set active sheet and the title. 0 means first sheet that I will use
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle('TV');

        $charges = $this->service_centre_charges_model->get_bb_charges(array("service_id" => 46, "visible_to_partner" => 1, 
            'bb_shop_address.active' =>1 ), "bb_charges.city, order_key, category, brand, physical_condition, partner_basic", true, true);


        $region = array_unique(array_map(function ($k) {
                    return $k['city'];
                }, $charges));
        $order_key = array_unique(array_map(function ($k) {
                    return $k['order_key'];
                }, $charges));
        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, "Category");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 1, "Brand");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, 1, "Type");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, 1, "Size");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, 1, "Note");
        
        $data = array();
        $k = 3; $col= 0;
        foreach ($order_key as $value) {
            $c = 5;
            foreach ($region as $city) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($c, 1, $city);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($c, 2, "Value");
                //echo "Search KEY ". $value. " -- ". $city."<br/>";
                $found = false;
                $searched_key = false;
                foreach ($charges as $key => $val) {

                    if ($val['city'] == $city && $val['order_key'] == $value) {
                        $searched_key = true;
                        $found = $key;

                        break;
                    }
                }
                if ($searched_key) {

                    if (!isset($data[$k])) {
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $k, "TV");
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $k, $charges[$found]['brand']);
                        $category = explode("_", $charges[$found]['category']);
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $k, $category[0]);
                        if (!empty($category[1])) {
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $k, $category[1]);
                        } else {
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $k, "");
                        }
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $k,  $charges[$found]['physical_condition']);
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($c, $k,  $charges[$found]['partner_basic']);
                       
                        $data[$k] = $charges[$found]['order_key'];

                    } else {
                        //   echo "city_".$c. "  ". $city."<br/>"; 
                         $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($c, $k,  $charges[$found]['partner_basic']);
                    }
                } else {

                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($c, $k,  "0.00");
                }

                $c++;
            }

            $k++; $col++;
        }
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        ob_end_clean() ;
        $objWriter->save(TMP_FOLDER."46.xlsx");
        $res1 =0;
        system(" chmod 777 " . TMP_FOLDER."46.xlsx" , $res1);
        return TMP_FOLDER."46.xlsx";
    }

    private  function generate_bb_price_data($service_id,$data){
        $template = 'bb_Price_Quotes_Template.xlsx';
        // directory
        $templateDir = __DIR__ . "/../excel-templates/";

        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );

        //load template
        if(ob_get_length() > 0) {
            ob_end_clean();
        }
        $R = new PHPReport($config);

        $R->load(array(
            array(
                'id' => 'bb_price_data',
                'repeat' => true,
                'data' => $data,
            )
                )
        );
        
        $output_file_excel = TMP_FOLDER . $service_id. ".xlsx";

        $res1 = 0;
        if (file_exists($output_file_excel)) {

            system(" chmod 777 " . $output_file_excel, $res1);
            unlink($output_file_excel);
        }

        $R->render('excel', $output_file_excel);
        
        return $output_file_excel;
    }
    
    function combined_excel_sheets($excel_file_list) {

        $objPHPExcel1 = PHPExcel_IOFactory::load($excel_file_list['50']);
        $objPHPExcel2 = PHPExcel_IOFactory::load($excel_file_list['46']);
        $objPHPExcel3 = PHPExcel_IOFactory::load($excel_file_list['28']);
        $objPHPExcel4 = PHPExcel_IOFactory::load($excel_file_list['37']);

        foreach ($objPHPExcel2->getSheetNames() as $sheetName) {
            $sheet = $objPHPExcel2->getSheetByName($sheetName);
            $sheet->setTitle('TV');
            $objPHPExcel1->addExternalSheet($sheet);
        }
        unset($objPHPExcel2);
        foreach ($objPHPExcel3->getSheetNames() as $sheetName) {
            $sheet = $objPHPExcel3->getSheetByName($sheetName);
            $sheet->setTitle('Washing Machine');
            $objPHPExcel1->addExternalSheet($sheet);
        }
        unset($objPHPExcel3);
        foreach ($objPHPExcel4->getSheetNames() as $sheetName) {
            $sheet = $objPHPExcel4->getSheetByName($sheetName);
            $sheet->setTitle('Ref');
            $objPHPExcel1->addExternalSheet($sheet);
        }
        unset($objPHPExcel4);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel1, 'Excel2007');
        $combined_excel = TMP_FOLDER.'AC-Wash-Ref-TV-Price_Quotes_Template.xlsx';
        $objWriter->save($combined_excel);
        
        return $combined_excel;
    }
    
    
    /**
     * @desc This function is used to show the view to get the charges list from bb_charges table
     * @param void()
     * @return void()
     */
    function filter_bb_price_list(){
        
        $select = "cp_id, concat(name,'( ' ";
       
        $select .= ",shop_address_region ";
        $select .= " ) as cp_name";
        $data['cp_list'] = $this->bb_model->get_cp_shop_address_details(array(), $select, "name");
        //echo "<pre>";        print_r($data);exit();
        $data['saas_flag'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'),$data);
        $this->load->view('buyback/filter_bb_price_list',$data);
        $this->load->view('dashboard/dashboard_footer');
    }
    
    
    /**
     * @desc This function is used to get appliance list from bb_charges table
     * by cp_id
     * @param void()
     * @return void()
     */
    function get_bb_cp_appliance() {
        $cp_id = $this->input->post('cp_id');
        $select = 'service_id,s.services';
        $where['cp_id'] = $cp_id;
        $appliance_list = $this->bb_model->get_bb_price_data($select, $where, true, true);
        $option = '<option selected disabled>Select Appliance</option>';

        foreach ($appliance_list as $value) {
            $option .= "<option value='" . $value['service_id'] . "'";
            $option .= " > ";
            $option .= $value['services'] . "</option>";
        }

        echo $option;
    }
    
    /**
     * @desc This function is used to get physical condition from bb_charges table
     * by cp_id and service_id
     * @param void()
     * @return void()
     */
    function get_bb_charges_physical_condition(){
        $cp_id = $this->input->post('cp_id');
        $service_id = $this->input->post('service_id');
        $select = 'physical_condition';
        $where['cp_id'] = $cp_id;
        $where['service_id'] = $service_id;
        $physical_condition_list = $this->bb_model->get_bb_price_data($select, $where, true);
        $option = '<option selected disabled>Select Physical Condition</option>';

        foreach ($physical_condition_list as $value) {
            $option .= "<option value='" . $value['physical_condition'] . "'";
            $option .= " > ";
            $option .= $value['physical_condition'] . "</option>";
        }

        echo $option;
    }
    
    
    /**
     * @desc This function is used to get working condition from bb_charges table
     * by cp_id and service_id
     * @param void()
     * @return void()
     */
    function get_bb_charges_working_condition(){
        $cp_id = $this->input->post('cp_id');
        $service_id = $this->input->post('service_id');
        $physical_condition = $this->input->post('physical_condition');
        $select = 'working_condition';
        $where['cp_id'] = $cp_id;
        $where['service_id'] = $service_id;
        $where['physical_condition'] = $physical_condition;
        $physical_condition_list = $this->bb_model->get_bb_price_data($select, $where, true);
        $option = '<option selected disabled>Select Working Condition</option>';

        foreach ($physical_condition_list as $value) {
            $option .= "<option value='" . $value['working_condition'] . "'";
            $option .= " > ";
            $option .= $value['working_condition'] . "</option>";
        }

        echo $option;
    }
    
    
    /**
     * @desc This function is used to the filtered charges data from bb_charges table
     * @param void()
     * @return void()
     */
    function get_bb_price_list(){
        $response = $this->buyback->get_bb_price_list($this->input->post());
        echo $response;
    }
    
    function download_order_snapshot() {

        $post = $this->get_bb_post_view_data();

        $post['length'] = -1;
        $post['search_value'] = $this->input->post('search_value');
        
        $post['order'] = "";
        $select = "bb_order_details.partner_order_id, service_centres.name,bb_order_details.partner_tracking_id as tracking_id,bb_order_details.acknowledge_date,services, bb_unit_details.category,city, order_date, delivery_date, bb_order_details.current_status AS Current_Status, "
                . "bb_order_details.internal_status AS Internal_Status, bb_cp_order_action.current_status AS CP_Current_Status, bb_cp_order_action.internal_status AS CP_Internal_Status, bb_cp_order_action.remarks, partner_basic_charge, cp_basic_charge,cp_tax_charge,gst_amount,partner_sweetner_charges,bb_unit_details.cp_claimed_price";
        $post1 = $this->_advanced_bb_search($post);

        $list = $this->bb_model->get_bb_order_list($post1,$select,1);
        $list1 = json_decode(json_encode($list, true), true);
        $template = "BuybackOrderSnapshot.xlsx";
        $templateDir = __DIR__ . "/../excel-templates/";
        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );

        //load template
        if(ob_get_length() > 0) {
            ob_end_clean();
        }
        $R = new PHPReport($config);

        $R->load(array(
            array(
                'id' => 'order',
                'repeat' => true,
                'data' => $list1
            ),
                )
        );
        $output_file_excel = TMP_FOLDER . "BuybackOrderSnaphot.xlsx";
        $res1 = 0;
        if (file_exists($output_file_excel)) {

            system(" chmod 777 " . $output_file_excel, $res1);
            unlink($output_file_excel);
        }
        $R->render('excel', $output_file_excel);
        if (file_exists($output_file_excel)) {
            system(" chmod 777 " . $output_file_excel, $res1);
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="BuybackOrderSnaphot.xlsx"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($output_file_excel));
            readfile($output_file_excel);
            exec("rm -rf " . escapeshellarg($output_file_excel));
            exit;
        }
    }
    
    
    /**
     * @desc This function is used to show the view for tagging/untagging buyback orders
     * @param void()
     * @return void()
     */
    function tag_untag_bb_orders(){
        $data['saas_flag'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'),$data);
        $this->load->view('buyback/tag_untag_bb_orders');
        $this->load->view('dashboard/dashboard_footer');
    }
    
    
    /**
     * @desc This function is used to process the tagging untagging buyback orders
     * @param void()
     * @return json
     */
    function process_tagging_untagging_bb_orders() {
        if (!empty($this->input->post('data'))) {
            $action_type = $this->input->post('data')['action_type'];
            $tag_untag_type = $this->input->post('data')['tag_untag_type'];
            $order_id = explode(PHP_EOL, $this->input->post('data')['order_id']);
            $invoice_id = isset($this->input->post('data')['invoice_id'])? $this->input->post('data')['invoice_id']:'';
            if (!empty($tag_untag_type) && !empty($order_id) && !empty($action_type)) {
                switch ($action_type) {
                    case 'tag':
                        $res = $this->tag_bb_order_id($tag_untag_type, $order_id,$invoice_id);
                        break;
                    
                }
                
                if($res['flag']){
                    $data['status'] = 'OK';
                    $data['msg'] = 'Order Details Has Been Updated Successfully';
                }else{
                    $data['status'] = 'ERR';
                    $data['msg'] = "Error In Updating Following Order Id's : <b> ".implode(',', $res['not_updated_order_id']). " </b>";
                }
                
            } else {
                $data['status'] = 'ERR';
                $data['msg'] = 'Please Select All Field';
            }
        } else {
            $data['status'] = 'ERR';
            $data['msg'] = 'Invalid Request';
        }
        echo json_encode($data);
    }

    /**
     * @desc This function is used to process the tagging buyback orders
     * @param void()
     * @return $return_data array()
     */
    private function tag_bb_order_id($tag_untag_type,$order_id,$invoice_id){
        
        //initialize variables
        $flag = FALSE;
        $order_details = [];
        $cp_action_details = []; 
        $not_updated_order_id = [];
        $partner_invoice_id = empty($invoice_id)?'':trim($invoice_id);
        
        //process the tagging
        switch ($tag_untag_type){
            case 'claim_submitted_not_delivered':
                //order details status
                    $order_details['current_status'] = CLAIM_SUBMITTED;
                    $order_details['internal_status'] = _247AROUND_BB_NOT_DELIVERED;
                break;
            case 'claim_submitted_broken':
                //order details status
                    $order_details['current_status'] = CLAIM_SUBMITTED;
                    $order_details['internal_status'] = _247AROUND_BB_TAG_CLAIMED_SUBMITTED_BROKEN;
                break;
            case 'claim_submitted_tat_breach':
                //order details status
                    $order_details['current_status'] = CLAIM_SUBMITTED;
                    $order_details['internal_status'] = _247AROUND_BB_ORDER_TAT_BREACH;
                    
                    //cp_action status
                    $cp_action_details['current_status'] = _247AROUND_BB_NOT_DELIVERED;
                    $cp_action_details['internal_status'] = _247AROUND_BB_247APPROVED_STATUS;
                break;
            case 'claim_approved_by_amazon':
                //order details status
                    $order_details['current_status'] = CLAIM_APPROVED;
                break;
            case 'claim_rejected_by_amazon':
                //order details status
                    $order_details['current_status'] = CLAIM_REJECTED;
                break;
            case 'claim_debit_note_raised':
                    //order details status
                    $order_details['current_status'] = CLAIM_DEBIT_NOTE_RAISED;
                break;
            case 'claim_settled_by_amazon':
                //order details status
                    $order_details['current_status'] = CLAIM_SETTLED;
                break;
        }

        foreach($order_id as $val){
            $partner_order_id = trim($val);
            //update order details table
            if(!empty($order_details)){
                $update_id = $this->bb_model->update_bb_order_details(array('partner_order_id' => $partner_order_id), $order_details);
                if(!empty($update_id)){
                    if(!empty($cp_action_details)){
                        $update_cp_order_action = $this->cp_model->update_bb_cp_order_action(array('partner_order_id' => $partner_order_id), $cp_action_details);
                        log_message('info','CP Order Action Updated For Order Id = '.$partner_order_id);
                    }
                    if(!empty($partner_invoice_id)){
                        $update_unit_details = $this->bb_model->update_bb_unit_details(array('partner_order_id' => $partner_order_id),array('partner_invoice_id' => $partner_invoice_id));
                        log_message('info','CP Order Action Updated For Order Id = '.$partner_order_id. " And Invoice Id = ".$partner_invoice_id);
                    }
                    
                    $this->buyback->insert_bb_state_change($partner_order_id, $order_details['current_status'], $order_details['current_status'], $this->session->userdata('id'), _247AROUND, NULL);
                    $flag = TRUE;
                    }else{
                        array_push($not_updated_order_id, $partner_order_id);
                        $flag = FALSE;
                    }
            }else{
                array_push($not_updated_order_id, $partner_order_id);
                $flag = FALSE;
            }
        }
        
        $return_data['flag'] = $flag;
        $return_data['not_updated_order_id'] = $not_updated_order_id;
        return $return_data;
    }
    
    /**
     * @desc This function is used to get those rejected data for which claim submit to amazon
     * @param void()
     * @return array()
     */
    function get_vendor_rejected_order_claimed_data($claimed_type){
        $post = $this->get_bb_post_view_data();
        
        switch ($claimed_type){
            case CLAIM_SUBMITTED:
                $post['where_in'] = array('current_status' => array(CLAIM_SUBMITTED),
                    'internal_status' => array(_247AROUND_BB_NOT_DELIVERED,_247AROUND_BB_TAG_CLAIMED_SUBMITTED_BROKEN));
                $post['where'] = array();
                break;
            case CLAIM_APPROVED:
                $post['where_in'] = array('current_status' => array(CLAIM_APPROVED),
                    'internal_status' => array(_247AROUND_BB_NOT_DELIVERED,_247AROUND_BB_TAG_CLAIMED_SUBMITTED_BROKEN));
                $post['where'] = array();
                break;
            case CLAIM_REJECTED:
                $post['where_in'] = array('current_status' => array(CLAIM_REJECTED),
                    'internal_status' => array(_247AROUND_BB_NOT_DELIVERED,_247AROUND_BB_TAG_CLAIMED_SUBMITTED_BROKEN));
                $post['where'] = array();
                break;
            case CLAIM_SETTLED:
                $post['where_in'] = array('current_status' => array(CLAIM_SETTLED),
                    'internal_status' => array(_247AROUND_BB_NOT_DELIVERED,_247AROUND_BB_TAG_CLAIMED_SUBMITTED_BROKEN));
                $post['where'] = array();
                break;
            case CLAIM_DEBIT_NOTE_RAISED:
                $post['where_in'] = array('current_status' => array(CLAIM_DEBIT_NOTE_RAISED),
                    'internal_status' => array(_247AROUND_BB_NOT_DELIVERED,_247AROUND_BB_TAG_CLAIMED_SUBMITTED_BROKEN));
                $post['where'] = array();
                break;
                
        }
        
        $post['column_order'] = array( NULL, NULL,'services', 'city','order_date', 'current_status');
        $post['column_search'] = array('bb_unit_details.partner_order_id','services', 'city','order_date','current_status');
        $list = $this->bb_model->get_bb_order_list($post);
        
        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row =  $this->generic_table_data($order_list, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
            
                );
    }
    
    /**
     * @desc This function is used to show bb claim raised data
     * @param void()
     * @return void()
     */
    
    function bb_claimed_raised_order_data(){
        $data['saas_flag'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'),$data);
        $this->load->view('buyback/bb_claimed_raised_order_data');
        $this->load->view('dashboard/dashboard_footer');
    }
    
    /**
     * @desc This function is used to get bb claim raised data
     * @param void()
     * @return array()
     */
    function get_bb_claimed_raised_order_data() {
        $post = $this->get_bb_post_view_data();
        $post['where_in'] = array('current_status' => array(CLAIM_DEBIT_NOTE_RAISED));
        $post['where'] = array();
        $post['column_order'] = array(NULL, NULL, 'services', 'city', 'order_date', 'current_status','internal_status');
        $post['column_search'] = array('bb_unit_details.partner_order_id', 'services', 'city', 'order_date', 'current_status','internal_status');
        
        $list = $this->bb_model->get_bb_order_list($post);
        
        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row = $this->generic_table_data($order_list, $no,TRUE);
            $data[] = $row;
        }

        return array(
            'data' => $data,
            'post' => $post
        );
    }
    
    
     /**
     * @desc This function is used to show dashboard header summary
     * @param void()
     * @return void()
     */
    function get_buyback_dashboard_summary(){
        
        $data_report['query'] = $this->vendor_model->get_around_dashboard_queries(array('active' => 1,'type'=> 'buyback'));
        $data_report['data'] = $this->vendor_model->execute_dashboard_query($data_report['query']);
        $this->load->view('dashboard/dashboard_title', $data_report);
    }
    
    function get_bb_svc_balance(){
        $where = array();
        if($this->input->post('dateRange')){
            $dateRangeArray = explode(" - ",$this->input->post('dateRange'));
            $where['create_date >= "'.date('Y-m-d',strtotime($dateRangeArray[0])).'"'] = NULL;
            $where['create_date <= "'.date('Y-m-d',strtotime($dateRangeArray[1])).'"'] = NULL;
        }
        $this->table = 'bb_svc_balance';
        $this->select = 'tv_balance,la_balance,mobile_balance,(tv_balance+la_balance+mobile_balance) as total_balance,date(create_date) as date';
        $this->order_by = array('create_date' => 'DESC');
        $data = $this->reusable_model->get_search_query($this->table,$this->select , $where,NULL, NULL ,$this->order_by,NULL,NULL);
        if(empty($where)){
            if(!empty($data)){
                $response = $data->result_array()[0];
            }else{
                $response = "no data found";
            }
            echo json_encode($response);
        }
        else{
            $balanceArray = $data->result_array();
            $count = count($data->result_array($balanceArray));
            $html ='';
            for($i=0;$i<$count;$i++){
                $html .= '<tr>';
                $html .= '<td>'.$balanceArray[$i]['date'].'</td>';
                $html .= '<td>'.$balanceArray[$i]['tv_balance'].'</td>';
                $html .= '<td>'.$balanceArray[$i]['la_balance'].'</td>';
                $html .= '<td>'.$balanceArray[$i]['mobile_balance'].'</td>';
                $html .= '<td>'.$balanceArray[$i]['total_balance'].'</td>';
                $html .= '<tr>';
            }
            echo $html;
        }
    }
    
    
    /**
     * @desc: This function is used to get buyback completed orders data 
     * based on month
     * @param string
     * @return array
     */
    function get_bb_acknowledge_data_by_month(){
        $sf_id = $this->input->post('sf_id');
        $data = $this->bb_model->get_bb_acknowledge_data_by_month($sf_id);
        $month = [];
        $year = [];
        $completed_orders = [];
        foreach ($data as $value){
            $temp_str = $value['month']."(".$value['year'].")";
            array_push($month, $temp_str);
            array_push($year, $value['year']);
            array_push($completed_orders, $value['count']);
        }
        array_shift($month);
        array_shift($completed_orders);
        $json_data['month'] = implode(",", $month);
        $json_data['count'] = implode(",", $completed_orders);
        echo json_encode($json_data);
    }
    
    function generate_bb_csv_price_list($file_name,$data){
        if(file_exists($file_name)){
            unlink($file_name);
        }
        $this->load->dbutil();
        $this->load->helper('file');
        $this->delimiter = ",";
        $this->newline = "\n";
        $this->new_report = $this->dbutil->csv_from_result($data, $this->delimiter, $this->newline);
        log_message('info', __FUNCTION__ . ' => Rendered CSV');
        $this->response =  write_file($file_name, $this->new_report);
    }
    
    function download_review_orders(){
        $newCSVFileName = "Buyback_review_orders_" . date('j-M-Y-H-i-s') . ".csv";
        $csv = TMP_FOLDER . $newCSVFileName;
        $list = $this->bb_model->get_bb_review_order_list(-1,NULL,NULL,NULL,1);
        $delimiter = ",";
        $newline = "\r\n";
        $new_report = $this->dbutil->csv_from_result($list, $delimiter, $newline);
        log_message('info', __FUNCTION__ . ' => Rendered CSV');
        write_file($csv, $new_report);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($csv) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($csv));
        readfile($csv);
        exec("rm -rf " . escapeshellarg($csv));
        exit;
    }
    
    function upload_tracking_file(){
        log_message("info",__METHOD__);
        $data['saas_flag'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'),$data);
        $this->load->view('buyback/upload_tracking_file');
        $this->load->view('dashboard/dashboard_footer');
    }
    function process_tracking_number_file(){
        $blankIndexes = array();
        $errormsg = "";
        $notFoundOrderID = array();
        $fileData['result'] = "SUCCESS";
        $file['file'] = $_FILES['tracking_file'];
        $excelArray = $this->miscelleneous->excel_to_Array_converter($file);
        if(!empty($excelArray)){
            foreach($excelArray as $index => $values){
                if($values['order_id'] && $values['tracking_number']){
                    $where['partner_order_id'] = trim($values['order_id']);
                    $data['partner_tracking_id'] = trim($values['tracking_number']);
                    $affected_rows = $this->reusable_model->update_table("bb_order_details",$data,$where);
                    if($affected_rows == 0){
                        $notFoundOrderID[] = $values['order_id'];
                    }
                }
                else{
                    $blankIndexes[] = $index+1;
                }
            }
        }
        else{
            $errormsg = "File is blank, Please upload file with order_id and tracking number";
        }
        if(!empty($blankIndexes)){
            $errormsg = "File Contains Blank Values. at lines ".(implode(",",$blankIndexes))." Except those lines all traking numbers has been updated<br>";
        }
//        if(!empty($notFoundOrderID)){
//            $errormsg = $errormsg." Not Found order IDs -  ".(implode(",",$notFoundOrderID)).", Please check";
//            $fileData['result'] = "SUCCESS";
//        }
        if(!$errormsg){
            $msg = "Tracking Number Updated Successfully";
            $this->session->set_userdata('tracking_success',$msg);
            $fileData['result'] = "SUCCESS";
        }
        else{
            $this->session->set_userdata('tracking_error',$errormsg);
        }
        $bucket = BITBUCKET_DIRECTORY;
        $directory_xls = "summary-excels/" . "buyback_tracking_file_".date("Y-m-d");
        $this->s3->putObjectFile($file['file']['tmp_name'], $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
        $fileData['entity_type'] = "247around";
        $fileData['entity_id'] = _247AROUND;
        $fileData['file_type'] = "Buyback_Tracking_File";
        $fileData['file_name'] = $directory_xls;
        $fileData['agent_id'] = $this->session->userdata('id');
        $this->reusable_model->insert_into_table("file_uploads",$fileData);
        redirect(base_url() . 'buyback/buyback_process/upload_tracking_file');
    }
    function download_tracking_sample_file(){
        $config = array('template' => "tracking_number_sample_file.xlsx", 'templateDir' => __DIR__ . "/../excel-templates/");
        $this->miscelleneous->downloadExcel(array(), $config);
    }
    
//    function test(){
//        $data = $this->bb_model->test();
//        foreach($data as $key => $value){
//            echo $key;
//            echo $value['id'];
//            $explode_brand  = explode("|", $value['brand']);
//           
//            foreach ($explode_brand as $key1 => $brand) {
//                $order_key =  str_replace($value['brand'],trim($brand),$value['order_key']);
//                if($key1 == 0){
//                    echo "update".PHP_EOL;
//                    $this->bb_model->test_update(array('brand' => trim($brand), "order_key" =>$order_key ), array('id' => $value['id']));
//                } else {
//                    unset($value['id']);
//                    $value['brand'] = trim($brand);
//                    $value['order_key'] = $order_key;
//                    echo $this->bb_model->insert_buyback_order($value);
//                    echo "insert".PHP_EOL;
//                }
//            }
//        }
//    }
        function buyback_full_balance(){
            $data['saas_flag'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
            $this->load->view('dashboard/header/' . $this->session->userdata('user_group'),$data);
            $this->load->view('buyback/balance_full_view');
            $this->load->view('dashboard/dashboard_footer');
        }
        function get_amazon_balance(){
        $select = " round(SUM(CASE WHEN partner_reimbursement_invoice IS NULL THEN partner_discount ELSE 0 END)) as expected_balance, "
                . "round(SUM(CASE WHEN partner_reimbursement_invoice IS NOT NULL THEN partner_discount ELSE 0 END)) as invoiced_balance";
        $data = $this->bb_model->get_bb_amazon_balace_details($select);
        $invoiceData = $this->invoices_model->get_buyback_paid_reimbursement_amount();
            if(!empty($data)){
                $data[0]['reimburse_amount'] = $invoiceData[0]['reimburse_amount'];
                $data[0]['total'] = $data[0]['expected_balance'] + $data[0]['invoiced_balance'] - $data[0]['reimburse_amount'];
                $response = $data[0];
            }else{
                $response = "no data found";
            }
            echo json_encode($response);
        }
        /**
     * @desc: This function is used to get buyback completed orders data 
     * based on month
     * @param string
     * @return array
     */
    function get_bb_order_detail_data_by_month(){
        //$total Order
        $table = "bb_order_details";
        $select = "DATE_FORMAT(order_date, '%b') AS month, DATE_FORMAT(order_date, '%Y') AS year, COUNT(bb_order_details.partner_order_id) as total, "
                . "(CASE "
                . "WHEN (bb_order_details.acknowledge_date IS NOT NULL AND bb_unit_details.order_status = 'Delivered' AND (bb_unit_details.partner_discount = 0 "
                . "OR (bb_unit_details.partner_discount > 0 AND partner_reimbursement_invoice IS NOT NULL))) "
                . "THEN 'completed' "
                . "WHEN bb_cp_order_action.current_status = 'InProcess'"
                . "THEN 'inprocess'"
                . "WHEN (bb_order_details.current_status = 'Cancelled' AND bb_order_details.internal_status = 'Cancelled') OR (bb_order_details.current_status = 'Rejected' AND bb_order_details.internal_status = 'Rejected') "
                . "THEN 'cancelled'"
                . "WHEN ((bb_order_details.current_status = 'Delivered' OR bb_order_details.current_status = 'In-Transit' "
                . "OR bb_order_details.current_status = 'New Item in In-Transit' OR bb_order_details.current_status = 'New Item In-transit') "
                . "AND bb_order_details.acknowledge_date IS NULL) "
                . "THEN 'pending'"
                . "ELSE 'disputed' END) as status";
        $where['order_date IS NOT NULL AND order_date >= (NOW() - INTERVAL 11 MONTH)'] = NULL;
        $orderBYArray["YEAR(order_date)"] = 'ASC';
        $orderBYArray["MONTH(order_date)"] = 'ASC';
        $orderBYArray["status"] = 'ASC';
        $groupBY = array('month','year','status');
        $join['bb_cp_order_action'] = 'bb_cp_order_action.partner_order_id = bb_order_details.partner_order_id';
        $join['bb_unit_details'] = 'bb_unit_details.partner_order_id = bb_order_details.partner_order_id';
        $data = $this->reusable_model->get_search_result_data($table,$select,$where,$join,NULL,$orderBYArray,NULL,NULL,$groupBY);
        foreach($data as $values){
              $structuredData[$values['month']][$values['status']] =  $values['total'];
        }
         foreach($structuredData as $key => $values){
                if(array_key_exists('pending', $values)){
                    $finalArray['pending'][] = (int)$values['pending'];
                }
                else{
                    $finalArray['pending'][] = $values['pending'] = 0;
                }
                if(array_key_exists('inprocess', $values)){
                    $finalArray['inprocess'][] = (int)$values['inprocess'];
                }
                else{
                    $finalArray['inprocess'][] = $values['inprocess'] = 0;
                }
                if(array_key_exists('completed', $values)){
                    $finalArray['completed'][] = (int)$values['completed'];
                }
                else{
                    $finalArray['completed'][] = $values['completed'] = 0;
                }
                if(array_key_exists('cancelled', $values)){
                    $finalArray['cancelled'][] = (int)$values['cancelled'];
                }
                else{
                    $finalArray['cancelled'][] = $values['cancelled'] = 0;
                }
                if(array_key_exists('disputed', $values)){
                    $finalArray['disputed'][] = (int)$values['disputed'];
                }
                else{
                    $finalArray['disputed'][] = $values['disputed'] = 0;
                }
                $finalArray['total'][] =$values['pending']+$values['inprocess']+$values['completed']+$values['cancelled']+$values['disputed'];
        }
        $finalArray['months'] = array_keys($structuredData);
        echo json_encode($finalArray);
    }
    
    function get_orders_without_invoices_and_without_reimbursement(){
         echo $this->buyback->get_orders_without_invoices_and_without_reimbursement();
    }
    function get_orders_with_cp_invoice_and_without_reimbursement(){
         echo $this->buyback->get_orders_with_cp_invoice_and_without_reimbursement();
    }
    function get_review_page_orders(){
         echo $this->buyback->get_review_page_orders();
    }
    function show_without_invoices_orders($status,$cp_invoice = false){
        $where = NULL;
        $select = 'bb.partner_order_id,bb_cp_order_action.admin_remarks as admin_remarks,bb.order_date as order_date,bb.delivery_date,bb.current_status,bb.internal_status,'
                . 'bb_cp_order_action.remarks as remarks,bb_cp_order_action.update_date';
        if($status != 'Total'){
            $where['bb_cp_order_action.current_status'] = urldecode ($status);
        }
        if($cp_invoice){
            $data['list'] = $this->bb_model->get_orders_without_invoices($select,NULL,$where,1,false);
        }
        else{
            $data['list'] = $this->bb_model->get_orders_without_invoices($select,NULL,$where,1);
        }
        $data['saas_flag'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'),$data);
        $this->load->view('buyback/bb_without_invoice_orders', $data);
        $this->load->view('dashboard/dashboard_footer');
    }
}
