<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Warranty extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {

        parent::__Construct();
        $this->load->model('warranty_model');
        $this->load->model('partner_model');
        $this->load->model('booking_model');
        $this->load->library("session");
        $this->load->library('miscelleneous');
        $this->load->library('warranty_utilities');
        $this->load->model('reusable_model');
        if ($this->session->userdata('loggedIn') == TRUE) {
            return TRUE;
        } else {
            redirect(base_url() . "employee/login");
        }
    }

    /**
     *  @desc : This function is used to load warranty checker Panel
     *  @param: void
     *  @return : print warranty on warranty Page
     */
    public function index($partner_id = null, $service_id = null, $brand = null) {
        // If Logged-in from Partner Panel, show only current partner Data 
        if($this->session->userdata('partner_id')){
            $partner_id = $this->session->userdata('partner_id');
        }
        
        $partners = $this->partner_model->getpartner($partner_id, false);
        foreach ($partners as $partnersDetails) {
            $partnerArray[$partnersDetails['id']] = $partnersDetails['public_name'];
        }
        if($this->session->userdata('partner_id')){
            $this->miscelleneous->load_partner_nav_header();
        }else{
            $this->miscelleneous->load_nav_header();
        }
        $this->load->view('warranty/check_warranty', ['partnerArray' => $partnerArray, 'partner_id' => $partner_id, 'service_id' => $service_id, 'brand' => $brand]);
        if($this->session->userdata('partner_id')){
            $this->load->view('partner/partner_footer');
        }
    }

    /**
     * @desc This function is used to show existing warranty plans on the given appliance
     * This view is called from AJAX from warranty checker panel
     * Sub functions Used : in_warranty_data, warranty_data
     */
    public function get_warranty_list_data() {
        $post = $this->get_post_data();
        $post_data = $this->input->post();
        $list = $this->warranty_model->check_warranty($post_data);
        $data = array();
        $no = $post['start'];
        $date_period_start = $post_data['purchase_date'];
        $create_date = $post_data['create_date'];
        $InWarrantyTimePeriod = 12; // Make it 0 if want to take in-warranty data from table.
        $InWarrantyGracePeriod = 0;
        $activeInWarrantyPlans = 0;
        $activeExtendedWarrantyPlans = 0;
        
        // get In-Warrenty Period        
        if(!empty($list[0]['warranty_type']) && ($list[0]['warranty_type'] == 1))
        {
            $InWarrantyTimePeriod = !empty($list[0]['warranty_period']) ? $list[0]['warranty_period'] : 0;
            $InWarrantyGracePeriod = !empty($list[0]['warranty_grace_period']) ? $list[0]['warranty_grace_period'] : 0;
        }
        else
        {
            $no++;
            $row =  $this->in_warranty_data($no, $post_data['purchase_date'], $activeInWarrantyPlans, $create_date);
            $data[] = $row;
        }
             
        if(!empty($list)){
            foreach ($list as $key => $value) {
                $no++;
                $row =  $this->warranty_data($list[$key], $no, $date_period_start, $InWarrantyTimePeriod, $InWarrantyGracePeriod, $activeInWarrantyPlans, $activeExtendedWarrantyPlans, $create_date);
                $data[] = $row;
            }        
        }
        
        $new_post['length'] = -1;
        $count = count($data);
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $count,
            "recordsFiltered" =>  $count,
            "data" => $data,
            "activeInWarrantyPlans" => $activeInWarrantyPlans,
            "activeExtendedWarrantyPlans" => $activeExtendedWarrantyPlans
        );
        echo json_encode($output);
    }
    
    /**
     * @desc This function is used to get datatable post data from warranty checher panel
     * @return type
     */
    function get_post_data(){
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
        $post['status'] = $this->input->post('status');

        return $post;
    }
    
    /**
     * @desc This function is used to generate rows of warranty plans on given model in warranty checker panel
     * @parent_function : get_warranty_list_data
     * @param type $warranty_list
     * @param type $no
     * @param type $period_start
     * @param type $InWarrantyTimePeriod
     * @param type $InWarrantyGracePeriod
     * @param type $activeInWarrantyPlans
     * @param type $activeExtendedWarrantyPlans
     * @param type $create_date
     * @return string
     */
    function warranty_data($warranty_list, $no, $period_start, $InWarrantyTimePeriod, $InWarrantyGracePeriod, &$activeInWarrantyPlans,&$activeExtendedWarrantyPlans, $create_date){
        $warranty_end_period = $period_start;
        $in_warranty_end_period = $period_start;
        $warranty_period = $warranty_list['warranty_period'];
        $warranty_grace_period = $warranty_list['warranty_grace_period'];
        if($warranty_list['warranty_type'] == 2)
        {
            $warranty_period += $InWarrantyTimePeriod;
            $warranty_grace_period += $InWarrantyGracePeriod;
        }
        
        if(!empty($warranty_period)){
            $warranty_end_period = strtotime(date("Y-m-d", strtotime($warranty_end_period)) . " +".$warranty_period." months");
            $in_warranty_end_period = strtotime(date("Y-m-d", strtotime($in_warranty_end_period)) . " +".$InWarrantyTimePeriod." months");
        }
        if(!empty($warranty_grace_period)){
            $warranty_end_period = strtotime(date("Y-m-d", strtotime($warranty_end_period)) . " +".$warranty_grace_period." days");
            $in_warranty_end_period = strtotime(date("Y-m-d", strtotime($in_warranty_end_period)) . " +".$InWarrantyGracePeriod." days");
        }
        $warranty_end_period = strtotime(date("Y-m-d", $warranty_end_period) . " -1 day");
        $in_warranty_end_period = strtotime(date("Y-m-d", $in_warranty_end_period) . " -1 day");
        $date_booking_creation = strtotime(date("Y-m-d", strtotime($create_date)));
        
        $row = array();
        $row[] = $no;
        $row[] = $warranty_list['plan_name'];
        $row[] = date('d-M-Y', strtotime($warranty_list['period_start']));
        $row[] = date('d-M-Y', strtotime($warranty_list['period_end']));
        $row[] = $warranty_list['states'];
        $row[] = $warranty_list['part_types'];
//        $row[] = !empty($warranty_list['inclusive_svc_charge']) ? "No" : "Yes";
//        $row[] = !empty($warranty_list['inclusive_gas_charge']) ? "No" : "Yes";
        $row[] = (!empty($warranty_list['warranty_type']) && $warranty_list['warranty_type'] == 1) ? "In Warranty" : "Extended Warranty";
        $row[] = $warranty_list['warranty_period']. " Month(s)";
        $row[] = $warranty_list['warranty_grace_period']. " Day(s)";
        if($warranty_end_period < $date_booking_creation)
        {
            $row[] = date('d-M-Y', $warranty_end_period)."<p style='color: #f30;font-weight:bold;'>Expired</p>";
        }
        else
        {
            if(($warranty_list['warranty_type'] == 1) || ($in_warranty_end_period >= $date_booking_creation))
            {
                $activeInWarrantyPlans++;
            }
            else {
                $activeExtendedWarrantyPlans++;
            }
            $row[] = date('d-M-Y', $warranty_end_period)."<p style='color: green;font-weight:bold;'>Active</p>";
        }        
        return $row;        
    }
    
    /**
     * @desc This function is used to generate row of default 1 year In-warranty plan on warranty checker panel
     * @parent_function : get_warranty_list_data
     * @param type $no : serial number
     * @param type $purchase_date
     * @param type $activeInWarrantyPlans
     * @param type $create_date
     * @return string
     */
    function in_warranty_data($no, $purchase_date, &$activeInWarrantyPlans, $create_date){  
        $warranty_start_period = date('d-M-Y', strtotime($purchase_date));
        $warranty_end_period = date('d-m-Y', strtotime(date("Y-m-d", strtotime($purchase_date)) . " +1 year"));
        $warranty_end_period = date('d-M-Y', strtotime(date("Y-m-d", strtotime($warranty_end_period)) . " -1 day"));
        $date_booking_creation = strtotime(date("Y-m-d", strtotime($create_date)));
        $row = array();
        $row[] = $no;
        $row[] = 'In Warranty';
        $row[] = $warranty_start_period;
        $row[] = $warranty_end_period;
        $row[] = "All";
        $row[] = "All";
        // $row[] = "Yes";
        // $row[] = "Yes";
        $row[] = "In Warranty";
        $row[] = "12 Month(s)";
        $row[] = "0 Day(s)";
        if(strtotime($warranty_end_period) < $date_booking_creation)
        {
            $row[] = date('d-M-Y', strtotime($warranty_end_period))."<p style='color: #f30;font-weight:bold;'>Expired</p>";
        }
        else
        {
            $activeInWarrantyPlans++;
            $row[] = date('d-M-Y', strtotime($warranty_end_period))."<p style='color: green;font-weight:bold;'>Active</p>";
        }        
        return $row;        
    }
    
    /**
     * This function is called when Booking_id is entered in Warranty Checker panel (From Top Right Search Box)
     * This function is used to fetch all parameters that are required for fetching a booking warranty status, from the Booking Id     * 
    */
    public function get_warranty_specific_data_from_booking_id()
    {
        $booking_id = $this->input->post('booking_id');
        if($this->session->userdata('partner_id')){
            $partner_id = $this->session->userdata('partner_id');
        }
        $arrBookings = $this->warranty_utilities->get_warranty_specific_data_of_bookings([$booking_id]);
        
        // Get Service Name from Service Id
        if(!empty($arrBookings[0]['service_id'])){
            $arr_service = $this->reusable_model->get_search_result_data("services","services",array("id"=>  stripslashes($arrBookings[0]['service_id'])),NULL,NULL,NULL,NULL,NULL,array());        
            $service = !empty($arr_service[0]['services']) ? $arr_service[0]['services'] : "";
            $arrBookings[0]['service'] = $service;
        }
        // Get Model Id from Number
        if(!empty($arrBookings[0]['model_number'])){
            $arr_model = $this->reusable_model->get_search_result_data("appliance_model_details","id",array("model_number"=>  stripslashes($arrBookings[0]['model_number']), "active" => 1),NULL,NULL,NULL,NULL,NULL,array());        
            $model_id = !empty($arr_model[0]['id']) ? $arr_model[0]['id'] : "";
            $arrBookings[0]['model_id'] = $model_id;
        }
        if((!empty($partner_id) && $arrBookings[0]['partner_id'] != $partner_id) || empty($arrBookings)){
           $arrBookings = array('error'=>1,'err_msg'=>'Booking Not Found.');
        }
        echo json_encode($arrBookings);
    }

    /**
     * This view is called from 'Add Models to Plan' Menu
     * This view will show all existing warranty plans and the models mapped to these plans 
     * Data will be Shown Partner and Product wise (No data will be shown if partner and service is not selected)
    */

    
    public function plan_model_mapping($plan_id = "") {
        $this->miscelleneous->load_nav_header();
        $partner_id = "";
        $service_id = "";
        
        // Add filter of Partner
        if(!empty($this->input->post('partner_id')))
        {
            $partner_id = $this->input->post('partner_id');
        }
        
        // Add filter of Product
        if(!empty($this->input->post('service_id')))
        {
            $service_id = $this->input->post('service_id');
        }
        
        //set select and where conditions
         $where = "(warranty_plans.partner_id = '".$partner_id."' AND warranty_plans.service_id = '".$service_id."' ) OR warranty_plans.plan_id = '".$plan_id."'";
        $select = "warranty_plans.plan_id, warranty_plans.plan_name, warranty_plans.plan_description, warranty_plans.period_start, warranty_plans.period_end, warranty_plans.warranty_type, warranty_plans.warranty_period, warranty_plans.partner_id, warranty_plans.service_id, appliance_model_details.model_number, services.services, partners.public_name, warranty_plan_model_mapping.id as mapping_id, warranty_plan_model_mapping.is_active, warranty_plans.is_active as is_active_plan";        
        $order_by = "warranty_plans.plan_name,appliance_model_details.model_number";
        $join['services']  = 'warranty_plans.service_id = services.id';
        $join['partners']  = 'warranty_plans.partner_id = partners.id';
        $join['warranty_plan_model_mapping']  = 'warranty_plans.plan_id = warranty_plan_model_mapping.plan_id';
        $join['appliance_model_details']  = 'warranty_plan_model_mapping.model_id = appliance_model_details.id';
        
        // fetch Data to show on view
        $data['plan_data'] = $this->warranty_model->getPlanWiseModels($where, $select, $order_by, NULL, $join, NULL, $result_array = false);        
        $data['partners'] = $this->partner_model->getpartner();
        $data['services'] = $this->booking_model->selectservice(true);
        $data['selected_partner_id'] = $partner_id;
        $data['selected_service_id'] = $service_id;
        
        // load view
        if(!empty($plan_id))
        {
            $this->load->view('warranty/warranty_plan_model_list', $data);
        }
        else
        {
            $this->load->view('warranty/plan_wise_models_view', $data);
        }
    }
    
    /**
     * This function loads the view of Adding Models to warranty Plans
     * also this function is used to Add models to warranty plans
     */
    public function add_model_to_plan() {
        $arr_post = $this->input->post();
        $warranty_plans = $this->warranty_model->selectPlans();
        $appliance_models = [];
        $this->miscelleneous->load_nav_header();
        if(!empty($arr_post['plan_id']) && !empty($arr_post['model_id'])) 
        {
            $arr_model = explode("###", $arr_post['model_id']);
            $arr_post['model_id'] = $arr_model[0];
            $arr_post['service_id'] = $arr_model[1];
            $arr_post['created_by'] = $this->session->userdata("employee_id");
            $id = $this->warranty_model->map_model_to_plan($arr_post);
            if(!empty($id))
            {
                $this->session->set_userdata('success', 'Data Entered Successfully');
            }
            else
            {
                $this->session->set_userdata('failed', 'Data already exists.');
            }            
        }
        $this->load->view('warranty/add_model_to_plan', array('warranty_plans' => $warranty_plans, 'appliance_models' => $appliance_models));    
    }
    
    /**
     * This function is used to unmap a mapped model from a warranty Plan.
     * This function is called from AJAX
     * @param : Primary key of warranty_plan_model_mapping
     * @request_type : POST
     */
    public function remove_model_from_plan() {
        $arr_post = $this->input->post();
        if(!empty($arr_post['mapping_id']))
        {
            $this->warranty_model->remove_model_from_plan($arr_post['mapping_id']);
            echo "success";
        }
    }    
 
    /**
     * This function is used to again map an un-mapped model in a warranty Plan.
     * This function is called from AJAX
     * @param : Primary key of warranty_plan_model_mapping
     * @request_type : POST
     */
    public function activate_model_to_plan() {
        $arr_post = $this->input->post();
        if(!empty($arr_post['mapping_id']))
        {
            $this->warranty_model->activate_model_to_plan($arr_post['mapping_id']);
            echo "success";
        }
    }
    
    /**
     * This function is used to activate a Warranty Plan
     * This function is called from AJAX
     * @param : Primary key of warranty_plans
     * @request_type : POST
     */
    public function activate_plan() {
        $arr_post = $this->input->post();
        if(!empty($arr_post['plan_id']))
        {
            $this->warranty_model->activate_plan($arr_post['plan_id']);
            echo "success";
        }
    }
    
    /**
     * This function is used to de-activate a Warranty Plan
     * This function is called from AJAX
     * @param : Primary key of warranty_plans
     * @request_type : POST
     */
    public function deactivate_plan() {
        $arr_post = $this->input->post();
        if(!empty($arr_post['plan_id']))
        {
            $this->warranty_model->deactivate_plan($arr_post['plan_id']);
            echo "success";
        }
    }
    
     /**
     *  @desc : This function is used to display add warranty plan form
     *  @param : void
     *  @return : void
     */
    public function add_warranty_plan($data = array())
    {
        $this->miscelleneous->load_nav_header();
        $this->load->view('warranty/add_warranty_plan', $data);
    }
 
    /**
     *  @desc : This function is used return dropdown option containing all partners
     *  @param : void
     *  @return : array
     */
    function get_partner_list_dropdown()
    {
        try
        {
            if ($this->input->is_ajax_request()) {
                $data = '';
                $result = $this->warranty_model->get_partner_list();
                foreach($result as $row)
                {
                    $data.= "<option value='".$row['id']."'>".$row['name']."</option>";
                }
                echo $data;
             }
            else {
                echo '';
            }
            
        }
        catch(Exception $ex)
        {
            echo '';
        }
    }
    
    
    /**
     *  @desc : This function is used return product option containing products of particular partner
     *  @param : partner id
     *  @return : array
     */
    function get_partner_service_list_dropdown()
    {
        try
        {
            if ($this->input->is_ajax_request()) {
                $data = '<option value="0" selected>Select</option>';
                $partner_id = $this->input->post('partner_id', TRUE);
                $result = $this->warranty_model->get_partner_service_list($partner_id);
                foreach($result as $row)
                {
                    $data.= "<option value='".$row['service_id']."'>".$row['services']."</option>";
                }
                echo $data;
             }
            else {
                echo '';
            }
            
        }
        catch(Exception $ex)
        {
            echo '';
        }
    }
    
     
    /**
     *  @desc : This function is used return state option containing all states
     *  @param : void
     *  @return : array
     */
    function get_state_list_dropdown()
    {
        try
        {
            if ($this->input->is_ajax_request()) {
                $data = '';
                $result = $this->warranty_model->get_state_list();
                foreach($result as $row)
                {
                    $data.= "<option value='".$row['id']."'>".$row['name']."</option>";
                }
                echo $data;
             }
            else {
                echo '';
            }
            
        }
        catch(Exception $ex)
        {
            echo '';
        }
    }
    
    
     /**
     *  @desc : This function is used save warranty plan of partner in db
     *  @param : array
     *  @return : void
     */
    function save_warranty_plan()
    {
        try
        {
            if ($this->input->server('REQUEST_METHOD') == 'POST') {
                
                //start validation
                $this->form_validation->set_rules('plan_name', 'plan name', 'trim|required'); 
                $this->form_validation->set_rules('partner', 'partner', 'callback_validate_partner'); 
                $this->form_validation->set_rules('service', 'service', 'callback_validate_service'); 
                $this->form_validation->set_rules('state', 'state', 'callback_validate_state'); 
                $this->form_validation->set_rules('warranty_type', 'warranty type', 'callback_validate_warranty_type'); 
                $this->form_validation->set_rules('start_date', 'plan start date', 'callback_validate_start_date'); 
                $this->form_validation->set_rules('end_date', 'plan end date', 'callback_validate_end_date['.$this->input->post("start_date", TRUE).']'); 
                $this->form_validation->set_rules('warranty_period', 'warranty period', 'callback_validate_warranty_period'); 
                $this->form_validation->set_rules('warranty_grace_period', 'warranty grace period', 'callback_validate_warranty_grace_period'); 
                
                if ($this->form_validation->run() == FALSE) { 
                    //validation fail
                   // $this->session->set_flashdata('error','Please Fill All Mandatory Fields.'.validation_errors());
                    
                    //load view again with filled data
                    $this->add_warranty_plan($_POST);
                    
                    //redirect(base_url().'employee/warranty/add_warranty_plan');
                } 
                else { 
                    //validation success
                       $arr_data = array();
                       $arr_data['plan_name'] = $this->input->post('plan_name', TRUE);
                       $arr_data['partner_id'] = $this->input->post('partner', TRUE);
                       $arr_data['warranty_type'] = $this->input->post('warranty_type', TRUE);
                       $arr_data['service_id'] = $this->input->post('service', TRUE);
                       $arr_data['period_start'] = $this->input->post('start_date', TRUE);
                       $end_date = $this->input->post('end_date', TRUE);
                       $arr_data['period_end'] = $end_date." 23:59:59";
                       $arr_data['warranty_period'] = $this->input->post('warranty_period', TRUE);
                       $arr_data['warranty_grace_period'] = $this->input->post('warranty_grace_period', TRUE);
                       $state = $this->input->post('state');
                       $arr_data['plan_description'] = trim($this->input->post('description', TRUE));
                       
                       //check if user selected checkboxes or not
                       if(isset($_POST['service_charge']))
                       {
                           $arr_data['inclusive_svc_charge'] = $_POST['service_charge'];
                       }
                       else
                       {
                           $arr_data['inclusive_svc_charge'] = 0;
                       }  
                       
                       if(isset($_POST['gas_charge']))
                       {
                           $arr_data['inclusive_gas_charge'] = $_POST['gas_charge'];
                       }
                       else
                       {
                           $arr_data['inclusive_gas_charge'] = 0;
                       }  
                       
                       if(isset($_POST['transport_charge']))
                       {
                           $arr_data['inclusive_transport_charge'] = $_POST['transport_charge'];
                       }
                       else
                       {
                           $arr_data['inclusive_transport_charge'] = 0;
                       }  
                       
                       $arr_data['is_active'] = 1;
                       $arr_data['create_date'] = date("y-m-d H:i:s");
                       //get employee id from session
                       $created_by_name = $this->session->userdata('employee_id');
                       $created_by_id = $this->session->userdata('id');
                       $arr_data['created_by'] = $created_by_name;
                       $arr_data['plan_depends_on'] = 1;
                       
                       //transaction start
                       $this->db->trans_start();
                       
                       //insert data in warranty_plans table
                       $plan_id = $this->reusable_model->insert_into_table('warranty_plans', $arr_data);
                        if (empty($plan_id)) {
                            //Insert was not successful
                            $this->db->trans_rollback();
                            $this->session->set_flashdata('error','Something went wrong. Please try again after sometime.');    
                            redirect(base_url().'employee/warranty/add_warranty_plan');
                            return false;
                        }
                        else
                        {
                            //Insert was successful
                            //check if all states were selected or not
                            $all_selected = 0;
                            foreach($state as $state_code)
                            {
                                //all state code is 0
                                if($state_code == 0)
                                {
                                    $all_selected = 1;
                                    break;
                                }
                            }
                            
                            //Inserting data in warranty_plan_state_mapping table to map plan state wise
                            if($all_selected)
                            {
                                //all states were selected, so get list of all states from db and then save data in table
                                $all_states = $result = $this->warranty_model->get_state_list();
                                if($all_states)
                                {
                                    foreach($all_states as $state_code)
                                    {
                                        $state_data = [];
                                        $state_data['state_code'] = $state_code['id'];
                                        $state_data['plan_id'] = $plan_id;
                                        $state_data['create_date'] = date('Y-m-d H:i:s');
                                        $state_data['created_by'] = $created_by_id;
                                        $plan_state_mapping_id = $this->reusable_model->insert_into_table('warranty_plan_state_mapping', $state_data);                                    
                                        if(empty($plan_state_mapping_id))
                                        {
                                            $this->db->trans_rollback();
                                            $this->session->set_flashdata('error','Something went wrong. Please try again after sometime.');    
                                            redirect(base_url().'employee/warranty/add_warranty_plan');
                                            return false;
                                        }
                                    }
                                }
                                else
                                {
                                    //states not found
                                    $this->db->trans_rollback();
                                    $this->session->set_flashdata('error','Something went wrong. Please try again after sometime.');    
                                    redirect(base_url().'employee/warranty/add_warranty_plan');
                                    return false;
                                }    
                                
                            }
                            else
                            {
                                 //all states were not selected
                                foreach($state as $state_code)
                                {
                                    $state_data = [];
                                    $state_data['state_code'] = $state_code;
                                    $state_data['plan_id'] = $plan_id;
                                    $state_data['create_date'] = date('Y-m-d H:i:s');
                                    $state_data['created_by'] = $created_by_id;
                                    $plan_state_mapping_id = $this->reusable_model->insert_into_table('warranty_plan_state_mapping', $state_data);                                    
                                    if(empty($plan_state_mapping_id))
                                    {
                                        $this->db->trans_rollback();
                                        $this->session->set_flashdata('error','Something went wrong. Please try again after sometime.');    
                                        redirect(base_url().'employee/warranty/add_warranty_plan');
                                        return false;
                                    }
                                }
                                
                            }    
                            
                            
                        }
                        
                        $this->db->trans_complete();
                        if ($this->db->trans_status() === FALSE)
                        {
                            //transaction was unsuccessful, so rollback transaction
                            $this->db->trans_rollback();
                            $this->session->set_flashdata('error','Something went wrong. Please try again after sometime.');    
                            redirect(base_url().'employee/warranty/add_warranty_plan');
                            return false;
                        }
                        else
                        {
                            //transaction was successful, so commit transaction
                            $this->db->trans_commit();
                            $this->session->set_flashdata('success','Warranty plan saved successfully.');    
                            redirect(base_url().'employee/warranty/warranty_plan_list');
                        }    
                       
                }

             }
            else {
               // $this->session->set_flashdata('error','Something went wrong. Please try again after sometime');
                redirect(base_url().'employee/warranty/add_warranty_plan');
            }
            
        }
        catch(Exception $ex)
        {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error','Something went wrong. Please try again after sometime.');
            redirect(base_url().'employee/warranty/add_warranty_plan');
        }
    }
    
    
    /**
     *  @desc : This function is used validate warranty period that it must be valid integer value greater than 0
     *  @param : void
     *  @return : void
     */
    public function validate_warranty_period($integer)
    {
        $valid = 0;
        if(isset($integer))
        {
            $integer = trim($integer);
            if(ctype_digit($integer) && $integer > 0)
            {
                $valid = 1;
            }
            
        }
       
        
        if($valid)
        {
            return true;
        }
        else
        {
            $this->form_validation->set_message('validate_warranty_period', 'The warranty period field should be valid integer');
            return FALSE;
        }    
        
    }
    
    /**
     *  @desc : This function is used validate warranty grace period that it must be valid integer value greater than 0
     *  @param : void
     *  @return : void
     */
     public function validate_warranty_grace_period($integer)
    {
        $valid = 0;
        // if(isset($integer))
        // {
            $integer = trim($integer);
            if(ctype_digit($integer) && $integer >= 0 || empty($integer))
            {
                $valid = 1;
            }
            
        // }
       
        
        if($valid)
        {
            return true;
        }
        else
        {
            $this->form_validation->set_message('validate_warranty_grace_period', 'The warranty grace period field should be valid integer');
            return FALSE;
        }    
        
    }
    
    
    /**
     *  @desc : This function is used validate partner id that it must be valid integer value greater than 0
     *  @param : void
     *  @return : void
     */
     public function validate_partner($integer)
    {
        $valid = 0;
        if(isset($integer))
        {
            $integer = trim($integer);
            if(ctype_digit($integer) && $integer > 0)
            {
                $valid = 1;
            }
            
        }
       
        
        if($valid)
        {
            return true;
        }
        else
        {
            $this->form_validation->set_message('validate_partner', 'The partner field should be valid');
            return FALSE;
        }    
        
    }
    
    /**
     *  @desc : This function is used validate service id that it must be valid integer value greater than 0
     *  @param : void
     *  @return : void
     */
    public function validate_service($integer)
    {
        $valid = 0;
        if(isset($integer))
        {
            $integer = trim($integer);
            if(ctype_digit($integer) && $integer > 0)
            {
                $valid = 1;
            }
            
        }
       
        
        if($valid)
        {
            return true;
        }
        else
        {
            $this->form_validation->set_message('validate_service', 'The service field should be valid');
            return FALSE;
        }    
        
    }
    
    /**
     *  @desc : This function is used validate state code that it must be valid integer value
     *  @param : void
     *  @return : void
     */
     public function validate_state($state_array)
    {
        $valid = 1;
        if(isset($state_array))
        {
            //check if sttae array is not empty
            if(count($state_array)>0)
            {
                //check if each state code is integer
                foreach($state_array as $state_code)
                {
                    if(!ctype_digit($state_code))
                    {
                        $valid = 0;
                        break;
                    }
                }
            }
            else {
                 $valid = 0;
            }
            
            
        }
        else
        {
            $valid = 0;
        }    
        
        if($valid)
        {
            return true;
        }
        else
        {
            $this->form_validation->set_message('validate_state', 'The state field should be valid');
            return FALSE;
        }    
        
    }
    
    /**
     *  @desc : This function is used validate start date syntax
     *  @param : void
     *  @return : void
     */
    public function validate_start_date($date)
    {
        $valid = 0;
        if(isset($date))
        {
            $date = trim($date);
            $date = date("Y-m-d", strtotime($date));
            $date_arr  = explode('-', $date);
            if (count($date_arr) == 3) {
                if (checkdate($date_arr[1], $date_arr[2], $date_arr[0])) {
                    $valid = 1;
                } 
            } 
            
        }
       
        
        if($valid)
        {
            return true;
        }
        else
        {
            $this->form_validation->set_message('validate_start_date', 'The start date field should be valid date');
            return FALSE;
        }    
    }
    
    /**
     *  @desc : This function is used validate end date syntax and it should be greater than start date
     *  @param : void
     *  @return : void
     */
    public function validate_end_date($date, $start_date)
    {
        $valid = 0;
        if(isset($date))
        {
            $date = trim($date);
            $date = date("Y-m-d", strtotime($date));
            $date_arr  = explode('-', $date);
            if (count($date_arr) == 3) {
                if (checkdate($date_arr[1], $date_arr[2], $date_arr[0])) {
                    $valid = 1;
                } 
            } 
            
        }
       
        
        if($valid)
        {
            
            if($date<$start_date)
            {
                $this->form_validation->set_message('validate_end_date', 'The end date field should be greater than start date '.$start_date);
                return FALSE;
            }
            else
            {
                return true;
            }
        }
        else
        {
            $this->form_validation->set_message('validate_end_date', 'The end date field should be valid date');
            return FALSE;
        }    
    }
    
    
     /**
     *  @desc : This function is used validate warranty type
     *  @param : void
     *  @return : void
     */
     public function validate_warranty_type($integer)
    {
        $valid = 0;
        if(isset($integer))
        {
            $integer = trim($integer);
            if(ctype_digit($integer) && ($integer == 1 or $integer == 2))
            {
                $valid = 1;
            }
            
        }
       
        
        if($valid)
        {
            return true;
        }
        else
        {
            $this->form_validation->set_message('validate_warranty_type', 'The warranty type field should be valid');
            return FALSE;
        }    
        
    }
    
      
    /**
     *  @desc : This function is used to display list of all warranty plans
     *  @param : void
     *  @return : void
     */
    public function warranty_plan_list()
    {
        $this->checkUserSession();
        $this->miscelleneous->load_nav_header();
        $this->load->view("warranty/warranty_plan_list");
    }
    
    
     /**
     * @desc: Check user Seession
     * @return boolean
     */
    function checkUserSession() {
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            redirect(base_url() . "employee/login");
        }
    }
    
     /**
     *  @desc : This function is used to show warranty plan list
     *  @param : void
     *  @return : void
     */
    function get_warranty_plan_list() {


        $data = $this->get_warranty_plan_data();

        $post = $data['post'];
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->warranty_model->count_all_warranty_plan_list($post),
            "recordsFiltered" => $this->warranty_model->count_filtered_warranty_plan_list($post),
            "data" => $data['data'],
        );
       echo json_encode($output);
    }

     /**
     *  @desc : This function is used to get list of warranty plans
     *  @param : void
     *  @return : void
     */
    function get_warranty_plan_data() {
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');

        $post['column_order'] = array();
        //column which will be searchable in datatable search
        $post['column_search'] = array('wp.plan_name', 'wp.warranty_period', 's.services', 'p.public_name');

        $where = array();
        $i = 0;
        //adding partner filter for query     
        if(isset($_POST['partner_id']) && $_POST['partner_id'] != 0)
        {
            $where['p.id'] = $_POST['partner_id'];
        }
        //adding product filter for query     
        if(isset($_POST['service_id']) && $_POST['service_id'] != 0)
        {
            $where['s.id'] = $_POST['service_id'];
        }
        $post['where'] = $where;

        $select = "wp.plan_id, wp.plan_name, wp.plan_description, wp.period_start, wp.period_end, wp.warranty_period, wp.is_active, s.services, p.public_name, s.id as service_id, p.id as partner_id";
        $list = $this->warranty_model->get_warranty_plan_list($post, $select);
        $data = array();
        $no = $post['start'];
        //create table data for each row
        foreach ($list as $model_list) {
            $no++;
            $row = $this->get_warranty_plan_table($model_list, $no);
            $data[] = $row;
        }

        return array(
            'data' => $data,
            'post' => $post
        );
    }
    
     /**
     *  @desc : This function is used to get rows for warranty plans table
     *  @param : void
     *  @return : void
     */
     function get_warranty_plan_table($model_list, $no) {
        $row = array();
        $json_data = json_encode($model_list);
        $row[] = $no;
        $row[] = $model_list->plan_name;
        $row[] = $model_list->plan_description;
        $row[] = $this->miscelleneous->get_formatted_date($model_list->period_start);
        $row[] = $this->miscelleneous->get_formatted_date($model_list->period_end);
        $row[] = $model_list->public_name;
        $row[] = $model_list->services;
        $row[] = $model_list->warranty_period . " months";
        $row_number = $no - 1;
        
        if($model_list->is_active == 1)
        {
            //warranty plan is active
            $row[] = "Active";
            $row[] = "<button class='btn btn-warning btn-sm' data='" . $json_data . "' onclick='change_warranty_plan_status(".$model_list->plan_id.",0,".$row_number.")'>Inactive</button>";
        }
        else
        {
            //warranty plan is inactive
            $row[] = "Inactive";
            $row[] = "<button class='btn btn-success btn-sm' data='" . $json_data . "' onclick='change_warranty_plan_status(".$model_list->plan_id.",1,".$row_number.")'>Active</button>";
        }    
        $row[] = "<button class='btn btn-primary btn-sm' onclick='warranty_plan_details(".$model_list->plan_id.")'>Edit</button>";
        $row[] = "<button class='btn btn-info btn-sm' onclick='plan_model_mapping(".$model_list->plan_id.")' >Model</button>";

        return $row;
    }
    
    
     /**
     *  @desc : This function is used display details of a particular warranty plan
     *  @param : array
     *  @return : void
     */
    function warranty_plan_details($plan_id)
    {
        try {
            //check if valid plan id is passed
            if(ctype_digit($plan_id))
            {
                $this->checkUserSession();
                $this->miscelleneous->load_nav_header();
                //get warranty plan details on basis of plan id
                $data['details'] = $this->warranty_model->get_warranty_plan_details($plan_id);
                
                if($data['details'])
                {
                    $data['plan_id'] = base64_encode($data['details'][0]['plan_id']);
                    //get all partner list
                    $data['partner_list'] = $this->warranty_model->get_partner_list();
                    //get partner product list
                    $data['partner_service_list'] = $this->warranty_model->get_partner_service_list($data['details'][0]['partner_id']);
                    //get all state list
                    $data['state_list'] = $this->warranty_model->get_state_list();
                    //get list of states for plan
                    $data['warranty_plan_state_list'] = $this->warranty_model->get_warranty_plan_state_list($data['details'][0]['plan_id']);
                    $data['warranty_plan_state_list'] = array_column($data['warranty_plan_state_list'], "state_code");
                    $this->load->view("warranty/warranty_plan_details", $data);
                }
                else
                {
                    $this->session->set_flashdata('error','Something went wrong. Please try again after sometime.');
                    redirect(base_url().'employee/warranty/warranty_plan_list');
                }    
                
            }
            else
            {
                $this->session->set_flashdata('error','Warranty plan not found!');
                redirect(base_url() . "employee/warranty/warranty_plan_list");
            }    

        } 
        catch(Exception $ex)
        {
            $this->session->set_flashdata('error','Something went wrong. Please try again after sometime.');
            redirect(base_url().'employee/warranty/warranty_plan_list');
        }
        
    }
    
    
    
     /**
     *  @desc : This function is used update warranty plan of partner in db
     *  @param : array
     *  @return : void
     */
    function update_warranty_plan()
    {
        try
        {
            if ($this->input->server('REQUEST_METHOD') == 'POST') {
                //get plan_id from hidden field
                $plan_id = $this->input->post('plan_id', TRUE);
                if(!empty($plan_id))
                {
                    //decrypt plan_id
                    $plan_id = base64_decode($plan_id);
                    if(ctype_digit($plan_id))
                    {
                        //valid plan_id
                        //start validation
                        $this->form_validation->set_rules('plan_name', 'plan name', 'trim|required'); 
                        $this->form_validation->set_rules('partner', 'partner', 'callback_validate_partner'); 
                        $this->form_validation->set_rules('service', 'service', 'callback_validate_service'); 
                        $this->form_validation->set_rules('state', 'state', 'callback_validate_state'); 
                        $this->form_validation->set_rules('warranty_type', 'warranty type', 'callback_validate_warranty_type'); 
                        $this->form_validation->set_rules('start_date', 'plan start date', 'callback_validate_start_date'); 
                        $this->form_validation->set_rules('end_date', 'plan end date', 'callback_validate_end_date['.$this->input->post("start_date", TRUE).']'); 
                        $this->form_validation->set_rules('warranty_period', 'warranty period', 'callback_validate_warranty_period'); 
                        $this->form_validation->set_rules('warranty_grace_period', 'warranty grace period', 'callback_validate_warranty_grace_period'); 

                        if ($this->form_validation->run() == FALSE) { 
                            //validation fail
                            $this->session->set_flashdata('error','Please Fill All Mandatory Fields.'.validation_errors());    
                            redirect(base_url().'employee/warranty/warranty_plan_details/'.$plan_id);
                        } 
                        else { 
                            //validation success
                               $arr_data = array();
                               $arr_data['plan_name'] = $this->input->post('plan_name', TRUE);
                               $arr_data['partner_id'] = $this->input->post('partner', TRUE);
                               $arr_data['warranty_type'] = $this->input->post('warranty_type', TRUE);
                               $arr_data['service_id'] = $this->input->post('service', TRUE);
                               $arr_data['period_start'] = $this->input->post('start_date', TRUE);
                               $end_date = $this->input->post('end_date', TRUE);
                               $end_date = date("Y-m-d", strtotime($end_date));
                               $arr_data['period_end'] = $end_date." 23:59:59";
                               $arr_data['warranty_period'] = $this->input->post('warranty_period', TRUE);
                               $arr_data['warranty_grace_period'] = $this->input->post('warranty_grace_period', TRUE);
                               $state = $this->input->post('state');
                               $arr_data['plan_description'] = trim($this->input->post('description', TRUE));
                               
                               $created_by_name = $this->session->userdata('employee_id');
                               $created_by_id = $this->session->userdata('id');

                               //check if user selected checkboxes or not
                               if(isset($_POST['service_charge']))
                               {
                                   $arr_data['inclusive_svc_charge'] = $_POST['service_charge'];
                               }
                               else
                               {
                                   $arr_data['inclusive_svc_charge'] = 0;
                               }  

                               if(isset($_POST['gas_charge']))
                               {
                                   $arr_data['inclusive_gas_charge'] = $_POST['gas_charge'];
                               }
                               else
                               {
                                   $arr_data['inclusive_gas_charge'] = 0;
                               }  

                               if(isset($_POST['transport_charge']))
                               {
                                   $arr_data['inclusive_transport_charge'] = $_POST['transport_charge'];
                               }
                               else
                               {
                                   $arr_data['inclusive_transport_charge'] = 0;
                               }  

                               //transaction start
                               $this->db->trans_start();

                               //update data in warranty_plans table
                                $this->db->where('plan_id', $plan_id);
                                $this->db->update('warranty_plans', $arr_data);
                                
                                //delete existing mapping of states with warranty plan
                                $this->db->delete('warranty_plan_state_mapping', array('plan_id' => $plan_id));
                                
                                
                                
                                    //check if all states were selected or not
                                    $all_selected = 0;
                                    foreach($state as $state_code)
                                    {
                                        //all state code is 0
                                        if($state_code == 0)
                                        {
                                            $all_selected = 1;
                                            break;
                                        }
                                    }

                                    //Inserting data in warranty_plan_state_mapping table to map plan state wise
                                    if($all_selected)
                                    {
                                        //all states were selected, so get list of all states from db and then save data in table
                                        $all_states = $result = $this->warranty_model->get_state_list();
                                        if($all_states)
                                        {
                                            foreach($all_states as $state_code)
                                            {
                                                $state_data = [];
                                                $state_data['state_code'] = $state_code['id'];
                                                $state_data['plan_id'] = $plan_id;
                                                $state_data['create_date'] = date('Y-m-d H:i:s');
                                                $state_data['created_by'] = $created_by_id;
                                                $this->db->insert('warranty_plan_state_mapping', $state_data);                                  
                                            }
                                        }
                                        else
                                        {
                                            //states not found
                                            $this->db->trans_rollback();
                                            $this->session->set_flashdata('error','Something went wrong. Please try again after sometime.');    
                                            redirect(base_url().'employee/warranty/warranty_plan_details/'.$plan_id);
                                            return false;
                                        }    

                                    }
                                    else
                                    {
                                         //all states were not selected
                                        foreach($state as $state_code)
                                        {
                                            $state_data = [];
                                            $state_data['state_code'] = $state_code;
                                            $state_data['plan_id'] = $plan_id;
                                            $state_data['create_date'] = date('Y-m-d H:i:s');
                                            $state_data['created_by'] = $created_by_id;
                                            $this->db->insert('warranty_plan_state_mapping', $state_data);                                  
                                        }

                                    }    


                                

                                $this->db->trans_complete();
                                if ($this->db->trans_status() === FALSE)
                                {
                                    //transaction was unsuccessful, so rollback transaction
                                    $this->db->trans_rollback();
                                    $this->session->set_flashdata('error','Something went wrong. Please try again after sometime.');    
                                    redirect(base_url().'employee/warranty/warranty_plan_details/'.$plan_id);
                                    return false;
                                }
                                else
                                {
                                    //transaction was successful, so commit transaction
                                    $this->db->trans_commit();
                                    $this->session->set_flashdata('success','Warranty plan updated successfully.');    
                                    redirect(base_url().'employee/warranty/warranty_plan_list');
                                }    

                        }
                    }
                    else
                    {
                        //invalid plan_id
                        $this->session->set_flashdata('error','Cannot perform this action.');    
                        redirect(base_url().'employee/warranty/warranty_plan_details/'.$plan_id);
                    }    
                }
                else
                {
                    //plan_id not found
                    $this->session->set_flashdata('error','Cannot perform this action.');    
                    redirect(base_url().'employee/warranty/warranty_plan_details/'.$plan_id);
                }    
                

             }
            else {
                $this->session->set_flashdata('error','Something went wrong. Please try again after sometime');
                redirect(base_url().'employee/warranty/warranty_plan_list');
            }
            
        }
        catch(Exception $ex)
        {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error','Something went wrong. Please try again after sometime.');
            redirect(base_url().'employee/warranty/warranty_plan_details/'.$plan_id);
        }
    }
    
    /**
     * This function is used to get all Models that can be associated with the given plan on the basis of its partner Id and service Id
     * @param int plan_id
     * @author : Prity Sharma
     * @date : 03-04-2020
     */
    function getWarrantyPlanSpecificModels(){
        $plan_id = $this->input->post('plan_id');  
        $select = 'appliance_model_details.id, appliance_model_details.model_number, partner_appliance_details.service_id';
        $order_by = 'appliance_model_details.model_number';
        $where = array ('warranty_plans.plan_id' => $plan_id,
                        'partner_appliance_details.active' => 1,
                        'appliance_model_details.active' => 1, 
        );
        $join['partner_appliance_details']  = 'partner_appliance_details.service_id = warranty_plans.service_id AND partner_appliance_details.partner_id = warranty_plans.partner_id';
        $join['appliance_model_details']  = 'partner_appliance_details.model = appliance_model_details.id';
               
        $result = $this->warranty_model->getPlanWiseModels($where, $select, $order_by, NULL, $join, NULL, $result_array = true);        
        
        if(!empty($result)){
            $flag = false;
            $option = "<option selected disabled>Select Model Number</option>";
            foreach ($result as $value) {
                if(!empty(trim($value['model_number']))){
                    $flag = true;
                    $option .= "<option value='".$value['id']."###".$value['service_id']."'>".$value['model_number']."</option>";
                }                
            }
            if($flag)  {
                $res['status'] = TRUE;
                $res['msg'] = $option;
            } else {
                $res['status'] = FALSE;
                $res['msg'] = 'no data found';
            }
            
        }else{
            $res['status'] = FALSE;
            $res['msg'] = 'no data found';
        }
        echo json_encode($res);        
    }
}
