<?php

if (!defined('BASEPATH')) {
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
        $this->load->library('paytmlib/encdec_paytm');
    }

    function index() {
        echo "ACESS DENIED";
    }

    function review_engineer_action_form() {
        $where['where'] = array("engineer_booking_action.current_status" => "InProcess");
        if ($this->session->userdata('service_center_id')) {
            $where['where']['engineer_booking_action.service_center_id'] = $this->session->userdata('service_center_id');
        }
        $data = $this->engineer_model->get_engineer_action_table_list($where, "engineer_booking_action.booking_id, amount_due, engineer_table_sign.amount_paid, engineer_table_sign.mismatch_pincode");

        foreach ($data as $key => $value) {
            $unitWhere = array("engineer_booking_action.booking_id" => $value->booking_id);
            $ac_data = $this->engineer_model->getengineer_action_data("engineer_booking_action.engineer_id, internal_status, cancellation_remark, closing_remark", $unitWhere);
            $status = _247AROUND_CANCELLED;
            $booking_remraks = $ac_data[0]['cancellation_remark'];
            foreach ($ac_data as $ac_table) {
                if ($ac_table['internal_status'] == _247AROUND_COMPLETED) {
                    $status = _247AROUND_COMPLETED;
                    $booking_remraks = $ac_data[0]['closing_remark'];
                }
            }
            $data[$key]->remarks = $booking_remraks;
            $data[$key]->status = $status;
            if (!empty($ac_data[0]['engineer_id'])) {
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

        foreach ($bookng_unit_details as $key1 => $b) {
            $broken = 0;
            foreach ($b['quantity'] as $key2 => $u) {

                $unitWhere = array("engineer_booking_action.booking_id" => $booking_id,
                    "engineer_booking_action.unit_details_id" => $u['unit_id'], "service_center_id" => $data['booking_history'][0]['assigned_vendor_id']);
                $en = $this->engineer_model->getengineer_action_data("engineer_booking_action.*", $unitWhere);
                $bookng_unit_details[$key1]['quantity'][$key2]['en_serial_number'] = $en[0]['serial_number'];
                $bookng_unit_details[$key1]['quantity'][$key2]['en_serial_number_pic'] = $en[0]['serial_number_pic'];
                $bookng_unit_details[$key1]['quantity'][$key2]['en_is_broken'] = $en[0]['is_broken'];
                $bookng_unit_details[$key1]['quantity'][$key2]['en_internal_status'] = $en[0]['internal_status'];
                if ($en[0]['is_broken'] == 1) {
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
            $data['signature'] = "";
            $data['mismatch_pincode'] = 0;
        }
        $isPaytmTxn = $this->paytm_payment_lib->get_paytm_transaction_data($booking_id);
        if (!empty($isPaytmTxn)) {
            if ($isPaytmTxn['status']) {
                $data['booking_history'][0]['onlinePaymentAmount'] = $isPaytmTxn['total_amount'];
            }
        }

        $data['bookng_unit_details'] = $bookng_unit_details;

        //$this->load->view('service_centers/header');
        $this->load->view("service_centers/approve_booking", $data);
    }

    function review_engineer_action_by_admin() {
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/review_engineer_action');
    }


    /**
     *  @desc : This function is used to get the post data for chat
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
     *  @desc : This function is used to get the review_engineer_action_by_admin_list
     *  @param : void()
     *  @return : JSON
     *  @Author : Abhishek Awasthi
     */
    function review_engineer_action_by_admin_list(){
        $post = $this->get_post_data();
        $post[''] = array();
        // $post['group_by'] = 'destination';          
        $post['where_in'] = array("engineer_booking_action.current_status" => array("InProcess", "Completed", "Cancelled"),
            "booking_details.current_status" => array(_247AROUND_PENDING, _247AROUND_RESCHEDULED));
        $post['column_search'] = array("engineer_booking_action.booking_id");

        $list = $this->engineer_model->get_engineer_action_table_list($post, "engineer_booking_action.booking_id, amount_due, engineer_table_sign.amount_paid,"
                . "engineer_table_sign.pincode as en_pincode, engineer_table_sign.address as en_address, "
                . "booking_details.booking_pincode, booking_details.assigned_vendor_id, booking_details.booking_address, engineer_table_sign.remarks");

        foreach ($list as $key => $value) {
            $is_broken = false;
            $unitWhere = array("engineer_booking_action.booking_id" => $value->booking_id);
            $ac_data = $this->engineer_model->getengineer_action_data("engineer_booking_action.engineer_id, internal_status,engineer_booking_action.is_broken", $unitWhere);
            $status = _247AROUND_CANCELLED;
            foreach ($ac_data as $ac_table) {
                if ($ac_table['internal_status'] == _247AROUND_COMPLETED) {
                    $status = _247AROUND_COMPLETED;
                }
                if ($ac_table['is_broken'] == 1) {
                    $is_broken = true;
                }
            }

            $list[$key]->status = $status;
            $list[$key]->is_broken = $is_broken;
            if (!empty($ac_data[0]['engineer_id'])) {
                $list[$key]->engineer_name = $this->engineer_model->get_engineers_details(array("id" => $ac_data[0]['engineer_id']), "name");
            } else {
                $list[$key]->engineer_name = "";
            }

            $list[$key]->sf_name = $this->vendor_model->getVendorDetails("name", array("id" => $list[0]->assigned_vendor_id))[0]['name'];
        }

        $data = array();
        $no = $post['start'];
        foreach ($list as $review_list) {
            $no++;
            $row = $this->get_review_engineer_action_by_admin_list_table($review_list, $no);
            $data[] = $row;
        }

        $post['length'] = -1;
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->engineer_model->count_all_review_engineer_action($post),
            "recordsFiltered" => $this->engineer_model->count_filtered_review_engineer_action($post),
            "data" => $data,
        );

        echo json_encode($output);

    }

  /**
     *  @desc : This function is used to get the review_engineer_action_by_admin_list
     *  @param : void()
     *  @return : Array
     *  @Author : Abhishek Awasthi
     */


function get_review_engineer_action_by_admin_list_table($review_list, $no){


        $row = array();
        $row[] = $no;
        $row[] = $review_list->booking_id;
        $row[] = $review_list->sf_name;
        if(isset($review_list->engineer_name[0]['name']) && !empty($review_list->engineer_name[0]['name'])){
         $row[] = $review_list->engineer_name[0]['name'];
        }else{
         $row[] = "-";

        }
        
        $row[] = $review_list->amount_due;
        $row[] = $review_list->amount_paid;
        if($review_list->is_broken==1){
        $row[] = "Yes";
        }else{
        $row[] = "No";
        }
        $row[] = $review_list->remarks;
        $row[] = $review_list->status;
        $row[] = $review_list->booking_address;
        
    
        return $row;

}




    function get_service_based_engineer() {
        $response = array();
        $service_id = $this->input->post("service_id");
        $service_center_id = $this->input->post("service_center_id");
        $where = array(
            "engineer_details.service_center_id" => $service_center_id,
            "engineer_appliance_mapping.service_id" => $service_id,
            "engineer_appliance_mapping.is_active" => 1,
            "engineer_details.active" => 1,
        );
        if ($service_id && $service_center_id) {
            $engineer = $this->engineer_model->get_service_based_engineer($where, "engineer_details.id, name");
            $html = "";
            $already_engg = 0;
            if (!empty($engineer)) {
                foreach ($engineer as $key => $value) {
                    $html .= "<option value='" . $value['id'] . "'";
                    if ($this->input->post("engineer_id") == $value['id']) {
                        $html .= "selected";
                        $already_engg = 1;
                    }
                    $html .= ">" . $value['name'] . "</option>";
                }
                $response['status'] = true;
                $response['already_engg'] = $already_engg;
                $response['html'] = $html;
                echo json_encode($response);
            } else {
                $response['status'] = false;
                $link = base_url() . "service_center/add_engineer";
                if ($this->input->post("booking_id")) {
                    $link = $link . "/" . urlencode(base64_encode($this->input->post("booking_id")));
                }
                $response['html'] = "<a href='" . $link . "' class='btn btn-info btn-sm' target='_blank'><i class='fa fa-user' aria-hidden='true'></i></a>";
                echo json_encode($response);
            }
        } else {
            $response['status'] = false;
            $response['html'] = "<a href='" . base_url() . "service_center/add_engineer' class='btn btn-info btn-sm' target='_blank'><i class='fa fa-user-plus' aria-hidden='true'></i></a>";
            echo json_encode($response);
        }
    }

    /*
     * @Desc - This function is used to get the engineer details in jSON
     * @param - 
     * @response - json
     * @Author  - Abhishek Awasthi
     */ 
    function get_engineer_details() {
        $data = $this->get_engineer_details_data();
        $post = $data['post'];
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->reusable_model->count_all_result("engineer_details", $post['where']),
            "recordsFiltered" => $this->reusable_model->count_all_filtered_result("engineer_details", "count(engineer_details.id) as numrows", $post),
            "data" => $data['data'],
        );
        echo json_encode($output);
        die();
    }

    /*
     * @Desc - This function is used to get the engineer detail row
     * @param - 
     * @response - Array
     * @Author  - Abhishek Awasthi
     */ 
    function get_engineer_details_data() {
        $service_center_id = "";
        if ($this->input->post("service_center_id")) {
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
            'entity_identity_proof' => 'entity_identity_proof.entity_id = engineer_details.id AND entity_identity_proof.entity_type = "engineer"',
        );
        $post['joinType'] = array("service_centres" => "LEFT", "entity_identity_proof", "LEFT");
        $post['where'] = array('delete' => 0);
        if ($service_center_id) {
            $post['where']['service_center_id'] = $service_center_id;
        }

        $data = array();
        $no = $post['start'];
        $list = $this->reusable_model->get_datatable_data("engineer_details", "engineer_details.id, engineer_details.name,engineer_details.installed, engineer_details.phone, engineer_details.alternate_phone, engineer_details.active, entity_identity_proof.identity_proof_type as identity_proof, engineer_details.varified, DATE_FORMAT(engineer_details.create_date,'%d-%b-%Y') as create_date, service_centres.name as company_name, service_centres.state, service_centres.district", $post);
        //echo $this->db->last_query(); die();
        foreach ($list as $key => $value) {
            $service_id = $this->engineer_model->get_engineer_appliance(array("engineer_id" => $value->id, "is_active" => 1), "service_id");
            $appliances = array();
            if (!empty($service_id)) {
                foreach ($service_id as $values) {
                    $service_name = $this->booking_model->selectservicebyid($values['service_id']);
                    if (!empty($service_name)) {
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

    /*
     * @Desc - This function is used to get the engineer details table structure
     * @param - 
     * @response - Array
     * @Author  - Abhishek Awasthi
     */ 

    function get_engineer_details_table($engineer_list, $no) {
        $row = array();
        $row_action = "";
        $row_action1=""; // Extra  coloumn for excel export //
        $phone_call_button = $engineer_list->phone;
        $alternet_phone_call_button = $engineer_list->alternate_phone;
        $c2c = $this->booking_utilities->check_feature_enable_or_not(CALLING_FEATURE_IS_ENABLE);
        if ($engineer_list->phone && !empty($c2c)) {
            $phone_call_button .= '<button type="button" onclick="outbound_call(' . $engineer_list->phone . ')" class="btn btn-sm btn-info"><i class = "fa fa-phone fa-lg" aria-hidden = "true"></i></button>';
        }
        if ($engineer_list->alternate_phone && !empty($c2c)) {
            $alternet_phone_call_button .= '<button type="button" onclick="outbound_call(' . $engineer_list->alternate_phone . ')" class="btn btn-sm btn-info"><i class = "fa fa-phone fa-lg" aria-hidden = "true"></i></button>';
        }
        if ($engineer_list->active == 1) {
            $row_action .= "<a id='edit' class='btn btn-small btn-primary' href=" . base_url() . "employee/vendor/change_engineer_activation/" . $engineer_list->id . "/0>Disable</a>";
        } else {
            $row_action .= "<a id='edit' class='btn btn-small btn-success' href=" . base_url() . "employee/vendor/change_engineer_activation/" . $engineer_list->id . "/1>Enable</a>";
        }

        $row[] = $no;
        if (!$this->input->post("service_center_id")) {
            $row[] = $engineer_list->company_name;
            $row[] = $engineer_list->state;
            $row[] = $engineer_list->district;
        }
        $row[] = "<a href='" . base_url() . "employee/vendor/get_edit_engineer_form/" . $engineer_list->id . "'>" . $engineer_list->name . "</a>";
        $row[] = $engineer_list->appliance_name;
        $row[] = $phone_call_button;
        $row[] = $alternet_phone_call_button;
        $row[] = $engineer_list->identity_proof;
        $row[] = date('Y-m-d', strtotime($engineer_list->create_date));
// Remove Duplicate code //
/*  Handle cases for verified and not verified  engg due to which datatable error was coming */
        if (!$this->input->post("service_center_id")) {
            if($this->session->userdata('user_group') == _247AROUND_RM || $this->session->userdata('user_group') == _247AROUND_ASM){
                if ($engineer_list->varified == 0) {
                    $row[] = "<button type='button' class='btn btn-danger btn-sm' onclick='verify_engineer(" . $engineer_list->id . ", 1)'>Not Verified</button>";
                } else {
                    $row[] = "<span class='label label-success'>Verified</span>";
                }
            } else {
                if ($engineer_list->varified == 0) {
                    $row[] = "<span class='label label-danger'>Not Verified</span>";
                } else {
                    $row[] = "<span class='label label-success'>Verified</span>";
                }
            }
        } else {
            if ($engineer_list->varified == 0) {
                $row[] = "<span class='label label-danger'>Not Verified</span>";
            } else {
                $row[] = "<span class='label label-success'>Verified</span>";
            }
        }


        $row[] = $row_action;
        $row[] = "<a id='edit' class='btn btn-small btn-primary' href=" . base_url() . "employee/vendor/get_edit_engineer_form/" . $engineer_list->id . ">Edit</a>";
        //$row[] = "<a onClick=\"javascript: return confirm('Delete Engineer?');\" id='edit' class='btn btn-small btn-danger' href=" . base_url() . "employee/vendor/delete_engineer/".$engineer_list->id.">Delete</a>";

/*  Extra coloumn only for excel export show current status of engg */
        if ($engineer_list->active == 0) {
            $row_action1 .= "<span class='label label-danger'>InActive</span>"; // No action needed Show status only //
        } else {
            $row_action1 .= "<span class='label label-success'>Active</span>";
        }

        $row[] = $row_action1;


        if($engineer_list->installed==1){
            $row[] = "<span class='label label-success'>Installed</span>";
        }else{
            $row[] = "<span class='label label-danger'>UnInstalled</span>";
        }
 
        $row[] = "<a id='' target='_blank' class=' ' href=" . base_url() . "employee/engineer/getEngineerHistory/" . $engineer_list->id . "><span class='label label-info'>History</span></a>";

        return $row;
    }


    /*
     * @Desc - This function is used to get the engineer history
     * @param - 
     * @response - View
     * @Author  - Abhishek Awasthi
     */
    function getEngineerHistory($engineer_id){
   
        $this->miscelleneous->load_nav_header();
        $data['engineer'] = $engineer_id;
        $this->load->view('employee/view_engineer_history',$data);
    }


    /*
     * @Desc - This function is used to get the engineer history
     * @param - 
     * @response - View
     * @Author  - Abhishek Awasthi
     */

    function get_engineer_history($engineer){

        $post = $this->get_post_data();
        $post['column_order'] = array();
        $post['column_search'] = array('booking_id', 'service_centres.name', 'engineer_details.name');
        $select = "booking_details.id,booking_details.booking_id,booking_details.assigned_vendor_id,booking_details.assigned_engineer_id,service_centres.name as service_center_name,engineer_details.name as engineer_name";
        $post['where']['booking_details.assigned_engineer_id'] = $engineer;
        $list = $this->engineer_model->get_engineer_history_list($post, $select);
        $data = array();
        $no = $post['start'];

        foreach ($list as $booking) {
            $no++;
            $row = $this->get_engineer_history_table($booking, $no);
            $data[] = $row;
        }

        $post['length'] = -1;
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->engineer_model->count_all_engineer_history($post),
            "recordsFiltered" => $this->engineer_model->count_filtered_engineer_history($post),
            "data" => $data,
        );

        echo json_encode($output);


    }


    /**
     * @desc this is used to generate  table
     * @Author Abhishek AWasthi
     */
    private function get_engineer_history_table($booking, $sn) {
        $row = array();
        $row[] = $sn;
        $row[] = $booking['service_center_name'];
        $row[] = $booking['engineer_name'];
        $row[] = $booking['booking_id'];
        return $row;
    }

//// Engineers for notification ////


    function get_engineer_details_for_notification() {
        $data = $this->get_engineer_details_data_for_notification();
        $post = $data['post'];
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->reusable_model->count_all_result("engineer_details", $post['where']),
            "recordsFiltered" => $this->reusable_model->count_all_filtered_result("engineer_details", "count(engineer_details.id) as numrows", $post),
            "data" => $data['data'],
        );
        echo json_encode($output);
        die();
    }

    function get_engineer_details_data_for_notification() {
        $service_center_id = "";
        if ($this->input->post("service_center_id")) {
            $service_center_id = $this->input->post("service_center_id");
        }

        //print_r($_POST['service_center_id']);  exit;
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
            'entity_identity_proof' => 'entity_identity_proof.entity_id = engineer_details.id AND entity_identity_proof.entity_type = "engineer"',
        );
        $post['joinType'] = array("service_centres" => "LEFT", "entity_identity_proof", "LEFT");
        $post['where'] = array('delete' => 0);
        if ($service_center_id) {
            $post['where']['service_center_id'] = $service_center_id;
        }

        $data = array();
        $no = $post['start'];

        $list = $this->reusable_model->get_datatable_data("engineer_details", "engineer_details.id, engineer_details.name, engineer_details.phone, engineer_details.alternate_phone,engineer_details.device_firebase_token, engineer_details.active, entity_identity_proof.identity_proof_type as identity_proof, engineer_details.varified, engineer_details.create_date, service_centres.name as company_name, service_centres.state, service_centres.district", $post);
        //echo $this->db->last_query(); die();
        foreach ($list as $key => $value) {
            $service_id = $this->engineer_model->get_engineer_appliance(array("engineer_id" => $value->id, "is_active" => 1), "service_id");
            $appliances = array();
            if (!empty($service_id)) {
                foreach ($service_id as $values) {
                    $service_name = $this->booking_model->selectservicebyid($values['service_id']);
                    if (!empty($service_name)) {
                        array_push($appliances, $service_name[0]['services']);
                    }
                }
            }
            $value->appliance_name = implode(",", $appliances);

            $no++;
            $row = $this->get_engineer_details_table_for_notification($value, $no);
            $data[] = $row;
        }

        return array(
            'data' => $data,
            'post' => $post
        );
    }

    function get_engineer_details_table_for_notification($engineer_list, $no) {
        $row = array();
        $row_action = "";
        $row[] = $no;
        //   if(!$this->input->post("service_center_id")){
        $row[] = $engineer_list->company_name;
        $row[] = $engineer_list->state;
        $row[] = $engineer_list->district;
        // }
        $row[] = "<a href='" . base_url() . "employee/vendor/get_edit_engineer_form/" . $engineer_list->id . "'>" . $engineer_list->name . "</a>";
        $row[] = $engineer_list->appliance_name;
        $row[] = $engineer_list->phone;
        $row[] = date('Y-m-d', strtotime($engineer_list->create_date));
        //$row[] = $engineer_list->device_firebase_token;
        if (!empty($engineer_list->device_firebase_token)) {
            $row[] = "<input type='checkbox' name='token' class='send_notification' data-check_firebase='" . $engineer_list->device_firebase_token . "'  /> <button  data-token_firebase='" . $engineer_list->device_firebase_token . "' class='btn btn-small btn-primary class='send_notification_btn''>Send</button>";
        } else {
            $row[] = "<button class='btn btn-small disabled btn-primary'>Send</button>";
        }


        return $row;
    }

    /**
     *  @desc  : This is used to load view of excel file for engg bulk notification
     *  @param : void
     *  @return : load view of Excel
     */
    function upload_engg_notification_excel($data = "") {
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/upload_engg_notification');
    }

    /**
     *  @desc  : This is used send engg bulk notification
     *  @param : void
     *  @return : load view of Excel
     */
    function send_notication($firebase_token = "") {

        $text = trim($_POST['text']);
        $phone = $_POST['phone'];

        $msg = array
            (
            'body' => $text,
            'title' => 'Message from 247Around',
            //'subtitle'  => 'This is a subtitle. subtitle',
            //'tickerText'    => 'Ticker text here...Ticker text here...Ticker text here',
            'vibrate' => 1,
            'sound' => 1,
            'largeIcon' => 'large_icon',
            'smallIcon' => 'small_icon'
        );
        $fields = array
            (
            'registration_ids' => array($firebase_token),
            'notification' => $msg
        );

        $headers = array
            (
            'Authorization: key=' . API_ACCESS_KEY_FIREBASE,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');  //https://fcm.googleapis.com/fcm
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);

        $rowData['fire_base_response'] = $result;
        $rowData['phone'] = $phone;
        $rowData['message'] = $text;
        $insert_id = $this->engineer_model->insert_engg_notification_data($rowData);
        $json_res = json_decode($result);
        if ($json_res->success) {
            $data_noti = array('notified' => 1);
            $this->engineer_model->update_engg_notification_data($data_noti, $insert_id);
        } else {
            $data_noti = array('notified' => 0);
            $this->engineer_model->update_engg_notification_data($data_noti, $insert_id);
        }
    }

/////////  END //////////////





    /* This function is used to load view for download booking details closed by engineer */
    function download_engineer_bookings() {
        $where['is_sf'] = 1;
        $where['active'] = 1;
        $where['isEngineerApp'] = 1;
        $data['vendor'] = $this->vendor_model->getVendorDetails("service_centres.name, service_centres.id", $where);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/download_bookings_closed_by_engineer', $data);
    }

    function download_engineer_closed_bookings() {
        $vendors = $this->input->post("service_center");
        $daterange = explode("-", $this->input->post("daterange"));
        $startDate = date("Y-m-d", strtotime(trim($daterange[0])));
        $endDate = date("Y-m-d", strtotime(trim($daterange[1])));
        $list = $this->engineer_model->get_engineer_closed_bookings($vendors, $startDate, $endDate);
        if (!empty($list)) {
            $headings = array("Booking Id", "Service Center Name", "Engineer Name", "Current Status", "Internal Status", "Partner Name", "Appliance Brand", "Cancellation Reason", "Cancellation Remark", "Closing Remark", "Initial Booking Date", "Closed Date", "PinCode Matched");
            $this->miscelleneous->downloadCSV($list, $headings, "engineer_bookings");
        } else {
            $this->session->set_userdata("error", "No data found");
        }
        redirect(base_url() . 'employee/engineer/download_engineer_bookings');
    }

    /*
     * @desc - This function is used to call curl for paytm api
     * @param - $order_id, $url, $post_data, $engineer_id
     * @return - $arr_response(array)
     */

    function paytm_curl_call($order_id, $url, $post_data, $engineer_id = "") {
        $arr_response = array();
        $x_mid = INCENTIVE_PAYTM_MERCHANT_MID;
        $request_param_list = array("MID" => INCENTIVE_PAYTM_MERCHANT_MID, "ORDERID" => $order_id);
        //$x_checksum = $this->encdec_paytm->getChecksumFromArray($request_param_list, INCENTIVE_PAYTM_MERCHANT_MID);
        $x_checksum = $this->encdec_paytm->getChecksumFromString(json_encode($request_param_list), INCENTIVE_PAYTM_MERCHANT_KEY);
        $header = array("Content-Type: application/json", "x-mid: " . $x_mid, "x-checksum: " . $x_checksum);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        //Capture both response as well as error messages
        $data['response'] = $response;
        $data['error'] = $err;

        $responseData = array("data" => $data);

        $activity = array(
            'activity' => __METHOD__,
            'header' => json_encode($header),
            'json_request_data' => json_encode($post_data),
            'json_response_string' => json_encode($responseData, JSON_UNESCAPED_SLASHES));

        if ($engineer_id) {
            $activity['entity_type'] = "engineer";
            $activity['partner_id'] = $engineer_id;
        }

        $this->partner_model->log_partner_activity($activity);

        if ($err) {
            log_message('info', "cURL Error #:" . $err);
            $arr_response['status'] = false;
            $arr_response['status_code'] = "0000";
            $arr_response['status_message'] = "cURL Error #:" . $err;
        } else {
            $response = json_decode($response, true);
            $arr_response['status'] = false;
            $arr_response['status_code'] = $response['statusCode'];
            $arr_response['status_message'] = $response['statusMessage'];
            if (isset($response['result'])) {
                $arr_response['result'] = $response['result'];
            }
        }
        return $arr_response;
    }

    /*
      @Desc - This function is used to transfer earning incentive to eangineer paytm wallet
     */

    function transfer_incentive_to_paytm_wallet($order_id, $mobile_no, $amount, $engineer_id) {

        $order_id = "kalyanitest1";
        $mobile_no = "7428747247";
        $amount = 1;
        $engineer_id = 2;

        $paytmParams = array();
        $paytmParams["subwalletGuid"] = INCENTIVE_SUBWALLET_GUID;
        $paytmParams["orderId"] = $order_id;
        $paytmParams["beneficiaryPhoneNo"] = $mobile_no;
        $paytmParams["amount"] = $amount;
        $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
        /* for Staging */
        //$url = "https://staging-dashboard.paytm.com/bpay/api/v1/disburse/order/wallet/gratification";
        /* for Production */
        $url = "https://dashboard.paytm.com/bpay/api/v1/disburse/order/wallet/gratification";
        $curl_response = $this->paytm_curl_call($order_id, $url, $post_data, $engineer_id);
        if ($curl_response['status']) {
            return true;
        } else {
            if ($curl_response['status_code'] == INVALID_BENEFICIARY_MOBILE_OR_EMAILID) {
                //need to send email or sms to engineer
            } else {
                //need to send email on 247 team
            }
            return false;
        }
    }

    /*
      @Desc - This function is used to get paytm wallet balance of merchant account
     */

    function get_paytm_wallet_balance() {
        $data = array();
        $post_data = json_encode(array("subwalletGuid" => INCENTIVE_SUBWALLET_GUID), JSON_UNESCAPED_SLASHES);
        /* for Staging */
        //$url = "https://staging-dashboard.paytm.com/bpay/api/v1/account/list";
        /* for Production */
        $url = "https://dashboard.paytm.com/bpay/api/v1/account/list";
        $curl_response = $this->paytm_curl_call(INCENTIVE_SUBWALLET_GUID, $url, $post_data);
        if ($curl_response['status']) {
            $data['status'] = true;
            $data['wallet_balance'] = $curl_response['result']['walletBalance'];
        } else {
            $data['status'] = false;
            $data['wallet_balance'] = 0;
        }
        return $data;
    }

    /*
      @Desc - This function is used to transfer earning incentive to engineers
     */

    function transfer_engineer_incentive_by_paytm() {
        $arr_engineer = array();
        $total_amount = $this->engineer_model->get_en_incentive_details("(sum(partner_incentive) + sum(247around_incentive)) as total_amount", array("is_paid" => 0, "is_active" => 1))[0]['total_amount'];
        if ($total_amount) {
            $wallet_check = $this->get_paytm_wallet_balance();
            if ($wallet_check['status']) {
                if ($wallet_check['wallet_balance'] >= $total_amount) {
                    $select = "engineer_incentive_details.partner_incentive, engineer_incentive_details.247around_incentive, engineer_details.phone, engineer_details.id as engineer_id, booking_details.id as booking_details_id";
                    $where = array("is_paid" => 0, "is_active" => 1);
                    $join = array(
                        "booking_details" => "booking_details.id = engineer_incentive_details.booking_details_id",
                        "engineer_details" => "engineer_details.id = booking_details.assigned_engineer_id",
                    );
                    $incentive_details = $this->reusable_model->get_search_result_data("engineer_incentive_details", $select, $where, $join, NULL, NULL, NULL, NULL);
                    foreach ($incentive_details as $key => $value) {
                        if (array_key_exists($value['engineer_id'], $arr_engineer)) {
                            $unique_eng = array(
                                "incentive_amount" => ($arr_engineer[$value['engineer_id']]['incentive_amount'] + ($value['partner_incentive'] + $value['247around_incentive'])),
                                "booking_details_id" => $arr_engineer[$value['engineer_id']]['booking_details_id'] . ',' . $value['booking_details_id'],
                                "mobile" => $value['phone'],
                            );
                            $arr_engineer[$value['engineer_id']] = $unique_eng;
                        } else {
                            $unique_eng = array(
                                "incentive_amount" => $value['partner_incentive'] + $value['247around_incentive'],
                                "booking_details_id" => $value['booking_details_id'],
                                "mobile" => $value['phone'],
                            );
                            $arr_engineer[$value['engineer_id']] = $unique_eng;
                        }
                    }
                    if (!empty($arr_engineer)) {
                        foreach ($arr_engineer as $key => $value) {
                            $order_id = $this->get_incentive_order_id($key);
                            $incentive_transffered = $this->transfer_incentive_to_paytm_wallet($order_id, $value['mobile'], $value['incentive_amount'], $key);
                            if ($incentive_transffered) {
                                $this->engineer_model->update_eng_incentive_details(array("is_paid" => 1), array("is_active" => 1), explode(",", $value['booking_details_id']));
                            }
                        }
                    }
                } else {
                    $template = $this->booking_model->get_booking_email_template(INSUFFICIENT_BALANCE_PAYTM_WALLET);
                    $body = $template[0];
                    $this->notify->sendEmail($template[2], $template[1], $template[3], "", $template[4], $body, "", INSUFFICIENT_BALANCE_PAYTM_WALLET);
                }
            } else {
                //Send error for wallete balance checking api
            }
        }
    }

    function transfer_incentive_to_paytm_wallet_with_notification($order_id, $mobile, $amount, $engg_id) {

        $order_id = $this->get_incentive_order_id($engg_id);
        $incentive_transffered = $this->transfer_incentive_to_paytm_wallet($order_id, $mobile, $amount, $engg_id);
    }

    /* @desc - This function is used to create order id for transffering engineer incenive. here INC = Incentive
     * @param - $engineer_id
     * @return - $order_id
     */

    function get_incentive_order_id($engineer_id) {
        $order_id = $engineer_id . "INC" . date("YmdHis");
        return $order_id;
    }

    /* @desc - This function is used to get list of enggineer and send them Firebase Notifications
     */

    function get_all_engineers_for_notification() {

        $this->miscelleneous->load_nav_header();
        $response = $this->engineer_model->get_engineer_config(FORCE_UPGRADE);
        $data['app_version'] = $response[0]->app_version;
        $this->load->view('employee/engineers_list_for_notifications',$data);
    }


    /* @desc - This function is used to get list of enggineer and send them Firebase Notifications
     */

    function configurations() {

        $this->miscelleneous->load_nav_header();
        $data['force_upgrade'] = $this->engineer_model->get_engineer_config(FORCE_UPGRADE);
        $data['whatsapp'] = $this->engineer_model->get_engineer_config(SEND_WHATSAPP);
        $this->load->view('employee/engineers_configurations',$data);
    }




/* @author Abhishek Awasthi
     *@Desc - This function is used to update config
     *@param -  
     *@return - json
     */


   function update_config(){

    $where = array(
        'configuration_type'=>trim($_POST['config_type'])
    );
    $data=array(
        'config_value'=>$_POST['config_value']
    );
    $response = $this->engineer_model->update_config_data($data,$where);

    if($response){

        echo json_encode(array('response'=>true));
    }else{

        echo json_encode(array('response'=>false));
    }


   }






}
