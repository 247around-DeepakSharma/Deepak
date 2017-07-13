<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Buyback_process extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('buyback');
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
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('buyback/get_order_details');
        $this->load->view('dashboard/dashboard_footer');
    }
    /**
     * @desc Used to get data as requested and also search 
     */
    function get_bb_order_details() {
        //log_message("info", print_r(json_encode($_POST, TRUE), TRUE));
       // $string = '{"draw":"1","columns":[{"data":"0","name":"","searchable":"true","orderable":"false","search":{"value":"","regex":"false"}},{"data":"1","name":"","searchable":"true","orderable":"false","search":{"value":"","regex":"false"}},{"data":"2","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"3","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"4","name":"","searchable":"true","orderable":"true","search":{"value":"","regex":"false"}},{"data":"5","name":"","searchable":"true","orderable":"false","search":{"value":"","regex":"false"}}],"start":"0","length":"50","search":{"value":"","regex":"false"},"status":"7"}';
       // $_POST = json_decode($string, true);
       
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
    
    function process_in_tansit(){
        $post = $this->get_bb_post_view_data();
        $post['where'] = array('assigned_cp_id IS NOT NULL' => NULL, 'order_date >= ' => date('Y-m-d', strtotime("-30 days")));
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
    
    function process_delivered(){
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
    
    function process_30_days_tat_breech_claim_submitted(){
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
        
        return $row;
    }
    
    function delivered_table_data($order_list, $no){
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
                              <li role='presentation'><a role='menuitem' tabindex='-1' target='_blank' href='javascript:void(0);'>Report Issue</a></li>
                            </ul>
                          </div>";
        
        return $row;
    }
    
    function unassigned_table_data($order_list, $no){
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
    
//    function others_table_data($order_list, $no){
//        $row = array();
//        $row[] = $no;
//        $row[] = "<a target='_blank' href='".base_url()."buyback/buyback_process/view_order_details/".
//                $order_list->partner_order_id."'>$order_list->partner_order_id</a>";
//
//        $row[] = $order_list->services;
//        $row[] = $order_list->city;
//        $row[] = $order_list->order_date;
//       
//        $row[] = $order_list->current_status;
//        $row[] = $order_list->partner_basic_charge;
//        
//         return $row;
//
//    }




    /**
     * @desc Used to show the view of buyback order detailed list for review
     * @param void
     * @return void
     */
    function bb_order_review(){
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
            $row[] = $order_list->category;
            $row[] = $order_list->brand;
            $row[] = $order_list->physical_condition;
            $row[] = $order_list->working_condition;
            $row[] = $order_list->status;
            $row[] = $order_list->remarks;
            $row[] = $order_list->current_status;
            $row[] = $order_list->name;
            $row[] = "<a class='btn btn-info btn-sm' target='_blank' href='".base_url()."buyback/buyback_process/get_bb_order_image_link/".$order_list->partner_order_id."/".$order_list->cp_id."'><i class='fa fa-camera'></i></a>";
            $row[] = "<label><input type='checkbox' class='flat check_single_row' id='approved_data' data-id='".$order_list->partner_order_id."' data-status='".$order_list->internal_status."'></label>";
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
    function approve_all_bb_order(){
        if ($this->input->post()) {
            $flag = FALSE;
            $order_ids = explode(',', $this->input->post('order_ids'));
            $status = explode(',',$this->input->post('status'));
            foreach($order_ids as $key => $value){
                $data['current_status'] = _247AROUND_BB_DELIVERED;
                $data['internal_status'] = $status[$key];
                
                $update_order_details = $this->bb_model->update_bb_order_details(array('partner_order_id' => $value),$data);
                
                if($update_order_details){
                    $update_cp_unit_details = $this->cp_model->update_bb_cp_order_action(array('partner_order_id' => $value),$data);
                    
                    if($update_cp_unit_details){
                        $this->buyback->insert_bb_state_change($value, $data['internal_status'], '', $this->session->userdata('id'), _247AROUND, NULL);
                        $flag = TRUE;
                    }else{
                        $flag = FALSE;
                    }
                }else{
                    $flag = FALSE;
                }
            }
            
            echo $flag;

        } else {
            echo "Invalid Request";
        }
    }
        
    function get_credit_amount(){
        echo "20000";
    }
    
    
    /**
     * @desc Used to get the order details data to take action 
     * @param $partner_order_id string
     * @return void
     */
    function view_order_details($partner_order_id){
        
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
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('buyback/get_disputed_auto_settle');
        $this->load->view('dashboard/dashboard_footer');
    }
    
    function disputed_30_days_breech(){
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('buyback/get_disputed_30_days_breech');
        $this->load->view('dashboard/dashboard_footer');
    }
    
    function download_bb_shop_address() {

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
        $post['search_value'] = $this->input->post('search');
        $post['column_search'] = array('bb_unit_details.partner_order_id');
        $post['where'] = array();
        $post['where_in'] = array();
        $post['column_order'] = array();
        $post['length'] = -1;
        
        $list['list'] = $this->bb_model->get_bb_order_list($post);
        
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
}
