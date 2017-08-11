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
        $this->load->library('PHPReport');


        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
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
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('buyback/get_order_details');
        $this->load->view('dashboard/dashboard_footer');
    }
    /**
     * @desc Used to get data as requested and also search 
     */
    function get_bb_order_details() {
        log_message("info",__METHOD__);
       // $tmp ='{"draw":"2","columns":[{"data":"0","name":"","searchable":"true","orderable":"false","search":{"value":"","regex":"false"}},{"data":"1","name":"","searchable":"true","orderable":"false","search":{"value":"","regex":"false"}},{"data":"2","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"3","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"4","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"5","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"6","name":"","searchable":"true","orderable":"false","search":{"value":"","regex":"false"}},{"data":"7","name":"","searchable":"true","orderable":"false","search":{"value":"","regex":"false"}}],"start":"0","length":"50","search":{"value":"","regex":"false"},"date_range":"2017\/07\/01 - 2017\/07\/31","city":"Ghaziabad\n","service_id":"","current_status":"","internal_status":"","status":"10"}';
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
                $data = $this->process_30_days_tat_breech_claim_submitted();
                break;
            
             case 9:
                $data = $this->process_30_days_tat_breech_claim_settled();
                break;
             case 10:
                $data = $this->advanced_bb_search();
                break;
            case 11:
                $data = $this->get_vendor_rejected_data();
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
        $post['where'] = array('assigned_cp_id IS NOT NULL' => NULL, 'order_date >= ' => date('Y-m-d',strtotime("-30 days")));
        $post['where_in'] = array('current_status' => array('In-Transit', 'New Item In-transit', 'Attempted'));
        $post['column_order'] = array( NULL, NULL,'services', 'city','order_date', 'current_status');
        $post['column_search'] = array('bb_unit_details.partner_order_id','services', 'city','order_date','current_status');
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
        
        $post = $this->get_bb_post_view_data();
        $date_range = $this->input->post("date_range");
        $delivery_date = $this->input->post("delivery_date");
        $city = $this->input->post("city");
        $service_id = $this->input->post("service_id");
        $current_status = $this->input->post("current_status");
        $internal_status = $this->input->post("internal_status");
        $cp_id = $this->input->post("cp_id");
        $post['where'] = array();
        if(!empty($date_range)){
            $order_date = explode("-", $date_range);
            $post['where']['order_date >= '] =  date("Y-m-d", strtotime(trim($order_date[0])));
            $post['where']['order_date < '] = date('Y-m-d', strtotime('+1 day', strtotime(trim($order_date[1]))));
        }
        if(!empty($delivery_date)){
            $d_date = explode("-", $delivery_date);
            $post['where']['delivery_date >= '] =  date("Y-m-d", strtotime(trim($d_date[0])));
            $post['where']['delivery_date < '] = date('Y-m-d', strtotime('+1 day', strtotime(trim($d_date[1]))));
        }
        if(!empty($city)){
             $post['where']['city'] = $city;
        }
        if(!empty($service_id)){
             $post['where']['service_id'] = $service_id;
        }
        if(!empty($internal_status)){
             $post['where']['internal_status'] = $internal_status;
        }
       
        if(!empty($current_status)){
             $post['where']['current_status'] = $current_status;
        }
        
        if(!empty($cp_id)){
             $post['where']['assigned_cp_id'] = $cp_id;
        }
        
        $post['where_in'] = array();
        $post['column_order'] = array( NULL, NULL,'services', 'city','order_date', 'current_status');
        $post['column_search'] = array('bb_unit_details.partner_order_id','services', 'city','order_date','current_status');
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
    
    function process_delivered(){
        log_message("info",__METHOD__);
        $post = $this->get_bb_post_view_data();
        $post['where'] = array('assigned_cp_id IS NOT NULL' => NULL);
        $post['where_in'] = array('current_status' => array('Delivered'), 'internal_status' => array('Delivered'));
        $post['column_order'] = array( NULL, NULL,'services', 'city','order_date', 'delivery_date', 'current_status');
        $post['column_search'] = array('bb_unit_details.partner_order_id','services', 'city','order_date','delivery_date','current_status');
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
        log_message("info",__METHOD__);
        $post = $this->get_bb_post_view_data();
        $post['where'] = array('assigned_cp_id IS NULL' => NULL, 'order_date >= ' => date('Y-m-d', strtotime("-30 days")));
        $post['where_in'] = array('current_status' => array('In-Transit', 'New Item In-transit', 'Attempted','Delivered'));
        $post['column_order'] = array( NULL, NULL,'services', 'city','order_date', 'current_status');
        $post['column_search'] = array('bb_unit_details.partner_order_id','services', 'city','order_date','current_status');
        $list = $this->bb_model->get_bb_order_list($post);
        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row =  $this->unassigned_table_data($order_list, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
            
                );

    }
    
    function process_lost_other(){
        log_message("info",__METHOD__);
        $post = $this->get_bb_post_view_data();
        $post['where'] = array('order_date >= ' => date('Y-m-d', strtotime("-30 days")));
        $post['where_in'] = array('current_status' => array('Lost', 'Unknown'));
        $post['column_order'] = array( NULL, NULL,'services', 'city','order_date', 'current_status');
        $post['column_search'] = array('bb_unit_details.partner_order_id','services', 'city','order_date','current_status');
        $list = $this->bb_model->get_bb_order_list($post);
        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row =  $this->unassigned_table_data($order_list, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
            
                );

    }
    
    function process_cancelled_not_claim(){
        log_message("info",__METHOD__);
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
            $row =  $this->unassigned_table_data($order_list, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
            
                );

    }
    
    function process_cancelled_claim_submitted(){
        log_message("info",__METHOD__);
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
            $row =  $this->unassigned_table_data($order_list, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
            
                );

    }
    
    function process_cancelled_claim_settled(){
        log_message("info",__METHOD__);
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
            $row =  $this->unassigned_table_data($order_list, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
            
                );

    }
    
    function process_30_days_tat_breech_not_claim(){
        log_message("info",__METHOD__);
        $post = $this->get_bb_post_view_data();
       
        $post['where_in'] = array('current_status' => array('In-Transit', 'New Item In-transit', 'Attempted','Lost', 'Unknown'),
            'internal_status' => array('In-Transit', 'New Item In-transit', 'Attempted','Lost', 'Unknown'));
        $post['column_order'] = array( NULL, NULL,'services', 'city','order_date', 'current_status');
        $post['where'] = array('order_date <= ' => date('Y-m-d', strtotime("-30 days")));
        $post['column_search'] = array('bb_unit_details.partner_order_id','services', 'city','order_date','current_status');
        $list = $this->bb_model->get_bb_order_list($post);
        
        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row =  $this->unassigned_table_data($order_list, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
            
                );
    }
    
    function get_vendor_rejected_data(){
        log_message("info",__METHOD__);
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
    
    function process_30_days_tat_breech_claim_submitted(){
        log_message("info",__METHOD__);
        $post = $this->get_bb_post_view_data();
       
        $post['where_in'] = array('current_status' => array('In-Transit', 'New Item In-transit', 'Attempted','Lost', 'Unknown'),
            'internal_status' => array(CLAIM_SUBMITTED));
        $post['column_order'] = array( NULL, NULL,'services', 'city','order_date', 'current_status');
        $post['where'] = array();
        $post['column_search'] = array('bb_unit_details.partner_order_id','services', 'city','order_date','current_status');
        $list = $this->bb_model->get_bb_order_list($post);
        
        $data = array();
        $no = $post['start'];
        foreach ($list as $order_list) {
            $no++;
            $row =  $this->unassigned_table_data($order_list, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
            
                );
    }
    
    function process_30_days_tat_breech_claim_settled(){
        log_message("info",__METHOD__);
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
            $row =  $this->unassigned_table_data($order_list, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
            
                );
    }
    
    function get_bb_post_view_data(){
        log_message("info",__METHOD__);
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
        log_message("info",__METHOD__);
        $row = array();
        $row[] = $no;
        $row[] = "<a target='_blank' href='".base_url()."buyback/buyback_process/view_order_details/".
                $order_list->partner_order_id."'>$order_list->partner_order_id</a>";

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
                              <li role='presentation'><a role='menuitem' tabindex='-1' target='_blank' href='".base_url()."buyback/buyback_process/update_received_bb_order/".urlencode($order_list->partner_order_id)."/".urlencode($order_list->service_id)."/".urlencode($order_list->city)."/".urlencode($order_list->assigned_cp_id)."'>Received</a></li>
                              <li role='presentation'><a role='menuitem' tabindex='-1' target='_blank' href='".base_url()."buyback/buyback_process/update_bb_report_issue_order_details/".urlencode($order_list->partner_order_id)."/".urlencode($order_list->service_id)."/".urlencode($order_list->city)."/".urlencode($order_list->assigned_cp_id)."'>Report Issue</a></li>
                            </ul>
                          </div>";
        
        return $row;
    }
    
    function delivered_table_data($order_list, $no){
        log_message("info",__METHOD__);
        $row = array();
        $row[] = $no;
        $row[] = "<a target='_blank' href='".base_url()."buyback/buyback_process/view_order_details/".
                $order_list->partner_order_id."'>$order_list->partner_order_id</a>";

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
                              <li role='presentation'><a role='menuitem' tabindex='-1' target='_blank' href='".base_url()."buyback/buyback_process/update_received_bb_order/".urlencode($order_list->partner_order_id)."/".urlencode($order_list->service_id)."/".urlencode($order_list->city)."/".urlencode($order_list->assigned_cp_id)."'>Received</a></li>
                              <li role='presentation'><a role='menuitem' tabindex='-1' onclick=showDialogueBox('".base_url()."buyback/buyback_process/update_not_received_bb_order/".urlencode($order_list->partner_order_id)."/".urlencode($order_list->service_id)."/".urlencode($order_list->city)."/".urlencode($order_list->assigned_cp_id)."')>Not Received</a></li>
                              <li role='presentation'><a role='menuitem' tabindex='-1' target='_blank' href='".base_url()."buyback/buyback_process/update_bb_report_issue_order_details/".urlencode($order_list->partner_order_id)."/".urlencode($order_list->service_id)."/".urlencode($order_list->city)."/".urlencode($order_list->assigned_cp_id)."'>Report Issue</a></li>
                            </ul>
                          </div>";
        
        return $row;
    }
    
    function unassigned_table_data($order_list, $no){
        log_message("info",__METHOD__);
        $row = array();
        $row[] = $no;
        $row[] = "<a target='_blank' href='".base_url()."buyback/buyback_process/view_order_details/".
                $order_list->partner_order_id."'>$order_list->partner_order_id</a>";
        $row[] = $order_list->services;
        $row[] = $order_list->city;
        $row[] = $order_list->order_date;
        $row[] = $order_list->current_status;
        $row[] = $order_list->partner_basic_charge;

        return $row;
    }
    
    function to_be_claimed_not_delivered($order_list, $no){
        log_message("info",__METHOD__);
        $row = array();
        $row[] = $no;
        $row[] = "<a target='_blank' href='".base_url()."buyback/buyback_process/view_order_details/".
                $order_list->partner_order_id."'>$order_list->partner_order_id</a>";
        $row[] = $order_list->services;
        $row[] = $order_list->city;
        $row[] = $order_list->order_date;
        $row[] = $order_list->internal_status;
        $row[] = $order_list->partner_basic_charge;

        return $row;
    }


    /**
     * @desc Used to show the view of buyback order detailed list for review
     * @param void
     * @return void
     */
    function bb_order_review(){
        log_message("info",__METHOD__);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('buyback/bb_order_review');
        $this->load->view('dashboard/dashboard_footer');
    }
    
    
    /**
     * @desc Used to get buyback order detailed list for review
     * @param void
     * @return $output json
     */
    function get_bb_review_order_details(){
        log_message("info",__METHOD__);
        $length = $this->input->post('length');
        $start = $this->input->post('start');
        $search = $this->input->post('search');
        $search_value = $search['value'];
        $order = $this->input->post('order');
        $draw = $this->input->post('draw');
        $list = $this->bb_model->get_bb_review_order_list($length, $start, $search_value, $order);

        $data = array();
        $no = $start;
        foreach ($list as $order_list) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $order_list->partner_order_id;
            $row[] = $order_list->name;
            $row[] = $order_list->category;
            $row[] = $order_list->brand;
            $row[] = $order_list->physical_condition;
            $row[] = $order_list->working_condition;
            $row[] = $order_list->cp_claimed_price;
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
            $row[] = "<label><input type='checkbox' class='flat check_single_row' id='approved_data' data-id='".$order_list->partner_order_id."' data-status='".$order_list->internal_status."' data-cp_claimed_price='".$order_list->cp_claimed_price."'></label>";
            $a = "<a class='btn btn-danger btn-sm' href='javascript:void(0)' onclick='";
            $a .= "open_reject_model(".'"'.$order_list->partner_order_id.'"';
            $a .= ', "'.$order_list->internal_status.'"';
            $a .= ', "'.$order_list->cp_claimed_price.'"';
            $a .= ")' ><i class='fa fa-times-circle'></i></a>";
            
            $row[] = $a;
            $data[] = $row;
        }


        $output = array(
            "draw" => $draw,
            "recordsTotal" => $this->bb_model->count_all_review_order(),
            "recordsFiltered" => $this->bb_model->count_filtered_review_order($search_value, $order),
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
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('buyback/bb_order_image_review',$data);
        $this->load->view('dashboard/dashboard_footer');
    }
    
    
    /**
     * @desc Used to approve buyback order in bulk
     * @param void
     * @return string
     */
    function approve_reject_bb_order() {
        log_message("info", __METHOD__);
        if ($this->input->post()) {
            $flag = FALSE;
            $order_ids = explode(',', $this->input->post('order_ids'));
            $status = explode(',', $this->input->post('status'));
            $type = $this->input->post('type');
            $cp_claimed_price = explode(',', $this->input->post('cp_claimed_price'));
            $remarks = $this->input->post('remarks');
            $order_details_data = array();
            $update_cp_claimed_price = '';
            if ($type === 'approved') {
                foreach ($order_ids as $key => $value) {
                    switch ($status[$key]) {
                        case _247AROUND_BB_ORDER_NOT_RECEIVED_INTERNAL_STATUS:

                            //update buyback order details
                            $order_details_data['current_status'] = _247AROUND_BB_TO_BE_CLAIMED;
                            $order_details_data['internal_status'] = _247AROUND_BB_NOT_DELIVERED;

                            //update buyback cp order action
                            $bb_cp_order_details_data['current_status'] = _247AROUND_BB_NOT_DELIVERED;
                            $bb_cp_order_details_data['internal_status'] = _247AROUND_BB_247APPROVED_STATUS;

                            break;

                        case _247AROUND_BB_Damaged_STATUS:

                            //update buyback order details
                            $order_details_data['current_status'] = _247AROUND_BB_TO_BE_CLAIMED;
                            $order_details_data['internal_status'] = _247AROUND_BB_ORDER_MISMATCH;

                            //update buyback cp order action
                            $bb_cp_order_details_data['current_status'] = _247AROUND_BB_Damaged_STATUS;
                            $bb_cp_order_details_data['internal_status'] = _247AROUND_BB_247APPROVED_STATUS;

                            //insert cp_claimed_price in bb_unit_details
                            $update_cp_claimed_price = $cp_claimed_price[$key];

                            break;
                    }

                    $flag = $this->process_approve_reject_bb_order($order_details_data, $bb_cp_order_details_data, $value, $update_cp_claimed_price);
                }
            } else if ($type === 'rejected') {
                $bb_cp_order_details_data['current_status'] = _247AROUND_PENDING;
                $bb_cp_order_details_data['internal_status'] = _247AROUND_BB_DELIVERED;
                $bb_cp_order_details_data['admin_remarks'] = $remarks;
                
                $flag = $this->process_approve_reject_bb_order($order_details_data, $bb_cp_order_details_data, $order_ids[0], $update_cp_claimed_price);
            }

            echo $flag;
        } else {
            echo "Invalid Request";
        }
    }

    function process_approve_reject_bb_order($order_details_data,$bb_cp_order_details_data,$partner_order_id,$update_cp_claimed_price) {
        
        $flag = FALSE;
        if(!empty($order_details_data)){
            $update_order_details = $this->bb_model->update_bb_order_details(array('partner_order_id' => $partner_order_id), $order_details_data);
            $new_state = $order_details_data['current_status'];
            $remarks = '';
            
        }else{
            $update_order_details = TRUE;
            $new_state = _247AROUND_BB_ORDER_REJECTED;
            $remarks = $bb_cp_order_details_data['admin_remarks'];
        }

        if ($update_order_details) {
            $update_cp_unit_details = $this->cp_model->update_bb_cp_order_action(array('partner_order_id' => $partner_order_id), $bb_cp_order_details_data);

            if ($update_cp_unit_details) {
                
                if(!empty($update_cp_claimed_price)){
                    $this->bb_model->update_bb_unit_details(array('partner_order_id' => $partner_order_id),array('cp_claimed_price'=>$update_cp_claimed_price));
                }
                
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

    function get_credit_amount(){
        log_message("info",__METHOD__);
        echo "20000";
    }
    
    
    /**
     * @desc Used to get the order details data to take action 
     * @param $partner_order_id string
     * @return void
     */
    function view_order_details($partner_order_id){
        log_message("info",__METHOD__);
        $data['partner_order_id'] = $partner_order_id;
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
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
                bb_unit.partner_sweetner_charges,s.services as service_name';
            $data = $this->bb_model->get_bb_order_appliance_details(array('partner_order_id' => $partner_order_id), $select);
            print_r(json_encode($data));
        }
    }
    
    function disputed_auto_settel(){
        log_message("info",__METHOD__);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('buyback/get_disputed_auto_settle');
        $this->load->view('dashboard/dashboard_footer');
    }
    
    function disputed_30_days_breech(){
        log_message("info",__METHOD__);
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
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
        $post['search_value'] = trim($this->input->post('search'));
        $post['column_search'] = array('bb_order_details.partner_order_id', 'bb_order_details.partner_tracking_id');
        $post['where'] = array();
        $post['where_in'] = array();
        $post['column_order'] = array();
        $post['length'] = -1;
        
        $list['list'] = $this->bb_model->get_bb_order_list($post);
        $select = "bb_shop_address.id, concat(name,'( ' ";
       
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
    
    function update_received_bb_order($order_id,$service_id,$city,$cp_id){
        log_message("info",__METHOD__);
        $data['order_id'] = urldecode($order_id);
        $data['service_id'] = urldecode($service_id);
        $data['city'] = urldecode($city);
        $data['cp_id'] = urldecode($cp_id);
        
        $return_data = $this->buyback->get_bb_physical_working_condition($data['order_id'],$data['service_id'],$data['cp_id']);
        $response_data = array_merge($data,$return_data);
        
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('buyback/update_received_bb_order_details',$response_data);
        $this->load->view('dashboard/dashboard_footer');
    }
    
    /**
     * @desc This function is used to update the buyback order when order received by
     *       the collection partner.
     * @param void();
     * @return void();
     */
    function process_received_bb_order_update(){ 
        log_message("info",__METHOD__);
        //check for validation
        $this->form_validation->set_rules('order_id', 'Order Id', 'trim|required');
        $this->form_validation->set_rules('remarks', 'Remarks', 'trim|required');
        $this->form_validation->set_rules('order_working_condition', 'Order Working Condition', 'trim|required');
        
        if($this->form_validation->run() === false){
            $msg = "Please fill all required field";
            $this->session->set_userdata('error',$msg);
            redirect(base_url().'buyback/buyback_process/update_received_bb_order/'.$this->input->post('order_id').'/'.$this->input->post('service_id').'/'.$this->input->post('city').'/'.$this->input->post('cp_id'));
        }else {
            $data = $this->input->post();
            $response = $this->buyback->process_update_received_bb_order_details($data);
            if($response['status'] === 'success'){
                $this->session->set_userdata('success',$response['msg']);
                redirect(base_url().'buyback/buyback_process/view_bb_order_details');
            }else if($response['status'] === 'error'){
                $this->session->set_userdata('error',$response['msg']);
                redirect(base_url().'buyback/buyback_process/update_received_bb_order/'.$this->input->post('order_id').'/'.$this->input->post('service_id').'/'.$this->input->post('city').'/'.$this->input->post('cp_id'));
            }
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
        $data['order_id'] = urldecode($order_id);
        $data['service_id'] = urldecode($service_id);
        $data['city'] = urldecode($city);
        $data['cp_id'] = urldecode($cp_id);
        $response = $this->buyback->process_update_not_received_bb_order_details($data);
        if($response['status'] === 'success'){
            $this->session->set_userdata('success',$response['msg']);
            redirect(base_url().'buyback/buyback_process/view_bb_order_details');
        }else if($response['status'] === 'error'){
            $this->session->set_userdata('error',$response['msg']);
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
                $cp_id = $this->buyback->get_cp_id_from_region($value->city);
                if(!empty($cp_id)){
                    //Get Charges list
                    $where_bb_charges = array('partner_id' => $value->partner_id,
                                              'city' => $value->city,
                                              'order_key' => $value->order_key,
                                              'cp_id' => $cp_id
                                    );
                   $status = $this->buyback->update_assign_cp_process($where_bb_charges, $value->partner_order_id, 1, $value->internal_status);
                   if(!$status['status']){
                      array_push($not_assigned, array('order_id' =>$value->partner_order_id,"message" => "Charges Not Found"));
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
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
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

        $row[] = $order_list->services;
        $row[] = $order_list->city;
        $row[] = $order_list->order_date;
        $row[] = $order_list->delivery_date;
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
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
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
    function update_bb_report_issue_order_details($order_id,$service_id,$city,$cp_id){
        $data['order_id'] = urldecode($order_id);
        $data['service_id'] = urldecode($service_id);
        $data['city'] = urldecode($city);
        $data['cp_id'] = urldecode($cp_id);
        $data['products'] = $this->booking_model->selectservice();
        $data['cp_basic_charge'] = $this->bb_model->get_bb_order_appliance_details(array('partner_order_id'=> $data['order_id']),'cp_basic_charge');
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('buyback/update_bb_order_details',$data);
        $this->load->view('dashboard/dashboard_footer');
    }
    
    
    /**
     * @desc Update Those order for which report issue was claimed by collection partner
     * @param $post_data array()
     * @return $response array()
     */
    
    function process_report_issue_bb_order_details(){
         log_message("info",__METHOD__);
        //check input field validation
        $this->form_validation->set_rules('order_id', 'Order Id', 'trim|required');
        $this->form_validation->set_rules('remarks', 'Remarks', 'trim|required');
        $this->form_validation->set_rules('order_working_condition', 'Order Working Condition', 'trim|required');
        $this->form_validation->set_rules('category', 'Category', 'trim|required');
        $this->form_validation->set_rules('cp_id', 'Collection Partner Id', 'trim|required');
        $this->form_validation->set_rules('claimed_price', 'Claimed Price', 'trim|required|callback_validate_claimed_price');
        
        if($this->form_validation->run() === false){
            $msg = "Please fill all required field";
            $this->session->set_userdata('error',$msg);
            redirect(base_url().'buyback/buyback_process/update_bb_report_issue_order_details/'.$this->input->post('order_id').'/'.$this->input->post('service_id').'/'.$this->input->post('city').'/'.$this->input->post('cp_id'));
        } else {

            $response = $this->buyback->process_bb_order_report_issue_update($this->input->post());
            if ($response['status'] === 'success') {
                $this->session->set_userdata('success', $response['msg']);
                redirect(base_url().'buyback/buyback_process/view_bb_order_details');
            } else if ($response['status'] === 'error') {
                $this->session->set_userdata('error', $response['msg']);
                redirect(base_url().'buyback/buyback_process/update_bb_report_issue_order_details/'.$this->input->post('order_id').'/'.$this->input->post('service_id').'/'.$this->input->post('city').'/'.$this->input->post('cp_id'));
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
    
    public function download_price_list_data() {
        $service_id = $this->service_centre_charges_model->get_bb_charges(array('partner_id' => '247024'), 'service_id', true);
        foreach ($service_id as $value) {
            $where = array('service_id' => $value['service_id'], 'partner_id' => '247024');
            $select = "category,brand, physical_condition, working_condition , city AS location , partner_total";
            $data = $this->service_centre_charges_model->get_bb_charges($where, $select);
            $excel_file[$value['service_id']] = $this->generate_bb_price_data($value['service_id'],$data);
            unset($data);
        }
        $main_excel = $this->combined_excel_sheets($excel_file);

        if ($main_excel) {
            
            foreach ($service_id as $value) {
                unlink(TMP_FOLDER . $value['service_id'] . '.xlsx');
            }
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($main_excel) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($main_excel));
            readfile($main_excel);
            exit;
        }
        unlink($main_excel);
    }
    
    private function generate_bb_price_data($service_id,$data){
        $template = 'bb_Price_Quotes_Template.xlsx';
        // directory
        $templateDir = __DIR__ . "/../excel-templates/";

        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );

        //load template
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
        $combined_excel = TMP_FOLDER.'AC-Wash-Ref-TV-Price Quotes Template.xlsx';
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
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
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
        $where['cp_id'] = $this->input->post('cp_id');
        $where['service_id'] = $this->input->post('service_id');
        $where['physical_condition'] = $this->input->post('physical_condition');
        $where['working_condition'] = $this->input->post('working_condition');
        
        $select = 'category , brand , city , partner_total , cp_total , around_total,visible_to_partner,visible_to_cp';
        
        $cp['charges_data'] = $this->bb_model->get_bb_price_data($select,$where);
        
        $this->load->view('buyback/show_bb_charges', $cp);
    }
    
}
