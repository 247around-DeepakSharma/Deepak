<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class User extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();

        $this->load->model('user_model');
        $this->load->model('employee_model');
        $this->load->model('service_centers_model');
        $this->load->model('booking_model');
        $this->load->model('partner_model');
        $this->load->model('vendor_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library("pagination");
        $this->load->library("session");
        $this->load->library('s3');
        $this->load->library('miscelleneous');
        $this->load->library('notify');
        $this->load->library('booking_utilities');
        $this->load->library('warranty_utilities');
        // Mention those functions whom you want to skip from employee specific validations
        $arr_functions_skip_from_validation = ['finduser','check_warranty_booking_search'];
        $arr_url_segments = $this->uri->segments; 
        $allowedForPartner = 0;
        if(!empty(array_intersect($arr_functions_skip_from_validation, $arr_url_segments))){        
            $allowedForPartner = 1;
        }
        if(!$allowedForPartner){
            if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee') ) {
                return TRUE;
            } else {
                redirect(base_url() . "employee/login");
            }
        }else{
            if ($this->session->userdata('loggedIn') == TRUE) {
                return TRUE;
            } 
            else {
                log_message('info', __FUNCTION__. " Session Expire for Partner");
                $this->session->sess_destroy();
                redirect(base_url() . "employee/login");
            }
        }
    }

    public function index() {
        $data['partner'] = $this->partner_model->get_all_partner_source();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/finduser', $data);
    }

    /**
     * @desc : This function is to find/search user
     *
     * Searches user details with booking id, order id and partner code
     *
     * Also searches user details with user's name.
     *
     * Complete or partial detail entered to search will show all the matching users/bookings in a list,
     *      from which we can select the required one by looking at other details shown.
     *
     * @param: offset, per page number and phone number
     * @return : print Booking on Booking Page
     */
    
    function finduser(){
        // $serial_number=$this->input->get('serial_number');
         $serial_number = preg_replace('/[^A-Za-z0-9\-]/', '',trim($this->input->get('serial_number')));
        $booking_id = preg_replace('/[^A-Za-z0-9\-]/', '',trim($this->input->get('booking_id')));
        $order_id = preg_replace('/[^A-Za-z0-9\-]/', '',trim($this->input->get('order_id')));
        //$userName = preg_replace('/[^A-Za-z0-9\-]/', '',trim($this->input->get('userName')));
        $tempuserName = ltrim($this->input->get('userName'));
        $akai_tr_form = trim($this->input->get('akai_tr_form')) ? trim($this->input->get('akai_tr_form')) : 0 ;
        $userName = rtrim($tempuserName);
        $partner_id = $this->input->get('partner');
        $search = preg_replace('/[^A-Za-z0-9\-]/', '',trim($this->input->get('search_value')));
        $post['length'] = -1;
        $phone_number = preg_replace('/[^A-Za-z0-9\-]/', '',$this->input->get('phone_number'));
        $is_flag = true;
        if (!empty($search)) {
            if (preg_match("/^[6-9]{1}[0-9]{9}$/", $search)) {
                $phone_number = $search;
            } else {
                $booking_id = $search;
            }
        }
        if($akai_tr_form == 0){
        $select = "services.services, service_centres.name as service_centre_name,
            service_centres.primary_contact_phone_1, service_centres.primary_contact_name,
            users.phone_number, users.name as customername,booking_details.type,booking_details.internal_status,
            users.phone_number, booking_details.*,penalty_on_booking.active as penalty_active, users.user_id, booking_details.id as booking_primary_id";
        }else{
        $select = "services.services, service_centres.name as service_centre_name,
            service_centres.primary_contact_phone_1, service_centres.primary_contact_name,
            users.phone_number, users.name as customername,booking_details.type,
            users.phone_number, booking_details.*,penalty_on_booking.active as penalty_active, users.user_id,booking_unit_details.*, booking_details.id as booking_primary_id";
        }
        if(!empty($booking_id)){
            
            $post['search_value'] = $booking_id;
            $post['column_search'] = array('booking_details.booking_id');
            $post['order'] = array(array('column' => 0,'dir' => 'asc'));
            $post['order_performed_on_count'] = TRUE;
            $post['column_order'] = array('booking_details.booking_id');
            if($akai_tr_form == 0){
                $post['unit_not_required'] = true;
            }
            
            
            $view = "employee/search_result";
            
        } else if(!empty($order_id)){
            $post['search_value'] = $order_id;
            $post['column_search'] = array('booking_details.order_id');
            $post['where'] = array('booking_details.partner_id' =>$partner_id);
            $post['unit_not_required'] = true;
            
            $view = "employee/search_result";
           
            
        }
            //Search Booking from serial number when the number is equal to partner serial number or serial number. 

            else if(!empty($serial_number)){
            $post['search_value'] = $serial_number;
            $post['column_search'] = array('booking_unit_details.serial_number','booking_unit_details.partner_serial_number');
            $post['where'] = array('booking_unit_details.serial_number = "'.trim($serial_number, "'").'" OR booking_unit_details.partner_serial_number = "'.$serial_number.'"' => NULL);
            $view = "employee/search_result";
        
            
        }
         else if(!empty($userName)){
            
            $select = "users.name as customername,
            users.phone_number, users.user_email, users.home_address, users.pincode, users.account_email";
            $post['search_value'] = $userName;
            $post['column_search'] = array('users.name');
            $post['order'] = array(array('column' => 0,'dir' => 'asc'));
            $post['order_performed_on_count'] = TRUE;
            $post['column_order'] = array('users.name');
            $post['unit_not_required'] = true;
            $view = "employee/search_user_list";
            
            
        }  else if(!empty($phone_number)){
            
//            $post['search_value'] = $phone_number;
//            $post['column_search'] = array('booking_details.booking_primary_contact_no',
//                 'booking_alternate_contact_no', 'users.phone_number');
            $data['Bookings'] = $this->user_model->search_user($phone_number, "", "", TRUE);
            $is_flag = false;
            $view = "employee/bookinghistory";
            
        }
         else{
            echo "Please Select Atlease One Input Field.";
            exit();
        }
        if($is_flag){
            $data['Bookings'] = $this->booking_model->get_bookings_by_status($post,$select);
            $data['booking_status'] = $this->booking_model->get_booking_cancel_complete_status_from_scba($booking_id);
        } 
        if ($akai_tr_form == 1) {
            echo json_encode($data, true);
            exit();
        }
        
        if(!empty($phone_number) && empty($data['Bookings'])){
          //  $output['phone_number'] = $phone_number;
            redirect(base_url()."employee/booking/addbooking/".$phone_number);
            //$this->loadViews($output);
        } else {
             $this->load_search_view($data, $view);
        }
    }
    
    function get_sms_Send_detail_and_user_applinace($phone_number){
        log_message("info", __METHOD__);
        $data['appliance_details'] = $this->user_model->appliance_details($phone_number);
        $data['sms_sent_details'] = $this->booking_model->get_sms_sent_details_for_empty_bookings($phone_number);
        $this->load->view("employee/user_appliance_sms_send_detail", $data);
        
    }

    /**
     * @desc: this is used to load view on the basis of booking or query and its current status
     *  @param: Array
     */
    function load_search_view($data,$view){
        $this->miscelleneous->load_nav_header();
        $data['c2c'] = $this->booking_utilities->check_feature_enable_or_not(CALLING_FEATURE_IS_ENABLE);
        $data['saas_module'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view($view, $data);
    }

    /**
     * @desc : This function is used to check phone number validation
     *
     * @param : void
     * @return : returns true if validation is true else false
     */
    public function CheckPhoneValidation() {
        $this->form_validation->set_rules('phone_number', 'phone_number', 'required');
        $this->form_validation->set_error_delimiters('<div class="error" role="alert">', '</div>');

        if ($this->form_validation->run() == FALSE) {
            return FALSE;
        } else {
            return true;
        }
    }

    /**
     * @desc : This function is used to load the view to add user
     *
     * Also sends user's detail(phone no.) and all states list to view
     *
     * @param : user's details
     * @return : void
     */
    function loadViews($output) {

        $results['user'] = $output;
        
        //gets all states while adding user as users can be of any state
        $results['state'] = $this->vendor_model->get_allstates();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/adduser', $results);
    }

    /**
     * @desc : This function is used to add a new user
     *
     * @param : void
     * @return : takes to booking history page of newly added user
     */
    function adduser() {
        $user['phone_number'] = $this->input->post("phone_number");
        $user['name'] = $this->input->post('name');
        $user['user_email'] = $this->input->post('user_email');
        $user['home_address'] = $this->input->post('home_address');
        $user['city'] = $this->input->post('city');
        $user['state'] = $this->input->post('state');
        $user['pincode'] = $this->input->post('pincode');
        $user['alternate_phone_number'] = $this->input->post('alternate_phone_number');
        $user['create_date'] = date("Y-m-d H:i:s");

        //Add the user
        $user_id = $this->user_model->add_user($user);

        //Add sample appliances for this user
//        $output = $this->booking_model->addSampleAppliances($user_id, 5);
        $this->booking_model->addSampleAppliances($user_id, 5);

        //Below two queries are running for no use, remove after confermation
//        $data1 = $this->user_model->search_user($user['phone_number']);
//        $appliance_details = $this->user_model->appliance_details($user['phone_number']);
        redirect(base_url() . 'employee/user/finduser?phone_number=' . $user['phone_number']);
    }

    /**
     * @desc : This function is used to check validation for different fields
     *
     * @param : void
     * @return : returns true if validation satifies else false
     */
    public function checkValidation() {

        $this->form_validation->set_rules('phone_number', 'phone_number', 'trim|exact_length[10]|required');
        $this->form_validation->set_rules('name', 'name', 'required');
        $this->form_validation->set_rules('home_address', 'home_address', 'required');
        $this->form_validation->set_error_delimiters('<div class="error" role="alert">', '</div>');

        if ($this->form_validation->run() == FALSE) {
            //echo "Ok";
            return FALSE;
        } else {
            return true;
        }
    }

    /**
     *  @desc : This function is to get form to edit user's details.
     *  @param : phone number
     *  @return : user's details to view
     */
    function get_edit_user_form($phone_number = "") {
        if ($this->input->post('phone_number')) {

            $phone_number = $this->input->post('phone_number');
        }

        $data['user'] = $this->user_model->search_user($phone_number);
        $data['state'] = $this->vendor_model->get_allstates();
        $data['c2c'] = $this->booking_utilities->check_feature_enable_or_not(CALLING_FEATURE_IS_ENABLE);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/edituser', $data);
    }

    /**
     *  @desc : This function is to save edited user's details
     *  @param : void
     *  @return : edit user details and load view
     */
    function process_edit_user_form() {
        $edit['user_id'] = $this->input->post('user_id');
        $edit['home_address'] = $this->input->post('home_address');
        $edit['user_email'] = $this->input->post('user_email');
        $edit['phone_number'] = $this->input->post('phone_number');
        $edit['city'] = $this->input->post('city');
        $edit['state'] = $this->input->post('state');
        $edit['name'] = $this->input->post('name');
        $edit['alternate_phone_number'] = $this->input->post('alternate_phone_number');
        $edit['pincode'] = $this->input->post('pincode');
        //Edits user details
        $this->user_model->edit_user($edit);
        
        //Logging Details
        log_message('info',__FUNCTION__.' User Details has been updated '.print_r($edit, TRUE));
        
        redirect(base_url() . 'employee/user/finduser?phone_number=' . $edit['phone_number']);
    }

    /**
     *  @desc : This function is to get user's details
     *  @param : start, per page limit and phone number
     *  @return : user details and load view
     */
    function user_details($offset = 0, $page = 0, $phone_number = "") {

        $output = $this->user_model->search_user($phone_number);

        if (empty($output)) {
            $output['phone_number'] = $phone_number;
            $this->loadViews($output);
        } else {
            if ($page == 0) {
                $page = 50;
            }

            $offset = ($this->uri->segment(5) != '' ? $this->uri->segment(5) : 0);
            $config['base_url'] = base_url() . "employee/user/user_details/0/0/$phone_number";
            $config['total_rows'] = $this->booking_model->total_user_booking($output[0]['user_id']);
            $config['per_page'] = $page;
            $config['uri_segment'] = 5;
            $config['first_link'] = 'First';
            $config['last_link'] = 'Last';
            $this->pagination->initialize($config);
            $links = $this->pagination->create_links();
            $data1 = $output;
            $query = $this->user_model->booking_history($phone_number, $config['per_page'], $offset);
            $data = $query;
            $appliance_details = $this->user_model->appliance_details($phone_number);
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/bookinghistory', array('data1' => $data1, 'data' => $data, 'links' =>
                $links, 'appliance_details' => $appliance_details));
        }
    }

    /**
     * @desc : this function is used to load form to get user month wise
     * @param : void
     * @return : load view
     */
    function get_user_count_view() {
        $data = $this->user_model->get_city_source();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/getusers', $data);
    }

    /**
     * @desc : this function is used to count total user,  completed booking and cancelled booking
     * @param : void
     * @return : load table
     */
    function getusercount() {
        $data['city'] = $this->input->post('city');
        $data['type'] = $this->input->post('type');
        $data['source'] = $this->input->post('source');

        $user['user'] = $this->user_model->get_count_user($data);
        $this->load->view('employee/getusers', $user);
    }

    /**
     * @desc : this function is used to count all the transactional users
     * @param : void
     * @return : load table
     */
    function user_count() {
        $data = $this->user_model->get_city_source();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/transactionalusers', $data);
    }

    /**
     * @desc : this function is used to count all the trandactional users
     * @param : void
     * @return : load view
     */
    function post_transactional_users() {
        $data['type'] = $this->input->post('type');
        $data['source'] = $this->input->post('source');
        $user['user'] = $this->user_model->get_count_transactional_user($data);
        $this->load->view('employee/transactionalusers', $user);
    }
    
    /**
     * @Desc: This function is used for new employee registration
     * @params: void
     * @return: view
     * 
     */
    function add_employee(){
        $cond= array('where' => array('entity_type'=>'247Around'), 'order_by' => 'department');
        $data['employee_dept'] = $this->employee_model->get_entity_role('department',$cond);//$this->employee_model->get_employee_groups();
        $data['employee_list'] = $this->employee_model->get_employee();
        $data['regions'] = $this->employee_model->get_regions();
        $data['error'] = $this->session->flashdata('error');
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/employee_add_edit', $data);
    }
    
    /**
    * @Desc: This function is used for getting rm from region
    * @assumption funtion to be called by employee where group is regionalmanager or areasalesmanager
    * @return: view
     */
     function get_rm_from_region(){
        $region = $this->input->post('region');
        $data['employee_rm'] = $this->employee_model->get_rm_region($region);
        echo json_encode($data);
    }
    
    /**
     * @Desc: This function is used for mapping rm to state
     * @params: void
     * @return: view
     * @Developer: Pranjal
     * @Date: 8/22/2019
     */
    function get_rm_state(){
        $rmid = $this->input->post("rmid");
        echo json_encode($this->employee_model->get_rm_mapped_state($rmid));
    }
    
    /**
     * @Desc: This function is used for mapping rm to state
     * @params: void
     * @return: view
     * @Developer: Pranjal
     * @Date: 8/22/2019
     */
    function rm_state_mapping($rmid=null){
        if(empty($rmid)) {
            $data['employee_rm'] = $this->employee_model->get_rm_details();
        } else {
            $data['employee_rm'] = $this->employee_model->get_rm_details_by_id($rmid);
        }
           
        
        $data['state'] = $this->employee_model->get_states();
        $data['error'] = $this->session->flashdata('error');
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/rm_state_mapping', $data);
    }
    /**
     * @Desc: This function is used to process rm state mapping form
     * @parmas: POST Array
     * @return: void
     * @assumption funtion to be called by employee where group is regionalmanager or areasalesmanager
     * @Developer: Pranjal
     * @Date: 8/22/2019
     */
    function process_rm_state_mapping() {

        $data = $this->input->post();
        if ($data) {
            $isRM = count($this->employee_model->isRManager($data["rm_asm"])) > 0 ? true : false;

            $Submit = true;
            $statusFlg = true;


            if ($isRM) {
                $rm_ID = $data["rm_asm"];
                $asmID = 0;
            } else {
                $asmID = $data["rm_asm"];
                $rm_ID_Array = $this->employee_model->getemployeeManagerfromid(array('employee_id' => $asmID));
                $rm_ID = $rm_ID_Array[0]['manager_id'];
            }

            $reqState = array();
            $selState1 = array();
            foreach ($data['state_name'] as $key => $value) {
                array_push($reqState, $value);
            }

            $reqStateString = "'" . implode("','", $reqState) . "'";


            ##########################check if state served by other ASM#####################################
            if (!$isRM) {
                $result = $this->employee_model->get_state_of_rm_asm($reqState, _247AROUND_ASM, $asmID);
                if (is_array($result) && count($result) > 0) {
                    $stateString = implode(',', array_map(function ($entry) {
                                return $entry['state'];
                            }, $result));
                    $errormessage = "State $stateString already served by other asm you can not assign to this ASM.";
                    $statusFlg = false;
                }
            }
            #########################check if state served by other RM#######################################
            if ($rm_ID != 0 && $rm_ID != '' && $statusFlg) {
                $result = $this->employee_model->get_state_of_rm_asm($reqState, _247AROUND_RM, $rm_ID);
                if (is_array($result) && count($result) > 0) {
                    $stateString = implode(',', array_map(function ($entry) {
                                return $entry['state'];
                            }, $result));
                    $errormessage = "State $stateString already served by other RM you can not assign this RM.";
                    $statusFlg = false;
                }
            }
            ######################check rm is removing its asm state from itself###############################
            if ($isRM && $statusFlg) {
                $currentState = $this->employee_model->get_rm_mapped_state($rm_ID);
                $reqState = array();
                $selState1 = array();
                foreach ($data['state_name'] as $key => $value) {
                    array_push($reqState, $value);
                }
                if (!empty($currentState)) {
                    $selState1 = array_map(function ($entry) {
                        return $entry['state'];
                    }, $currentState);
                }
                $diffState = array_diff($selState1, $reqState);

                $diffStateString = "'" . implode("','", $diffState) . "'";

                $result=$this->employee_model->get_asm_from_rm($diffState,$rm_ID);
                if (is_array($result)) {
                    if (count($result) > 0) {
                        $stateString = implode(', ', array_map(function ($entry) {
                                    return $entry['state'];
                                }, $result));
                        $errormessage = "RM has ASM mapped with $stateString. you can not remove these states from RM, Remove from ASM first.";
                        $statusFlg = false;
                    }
                }
            }

            if ($statusFlg) {
                if ($asmID != 0) {
                    $currentState = $this->employee_model->get_rm_mapped_state($asmID);

                    $selState1 = array();

                    if (!empty($currentState)) {
                        $selState1 = array_map(function ($entry) {
                            return $entry['state'];
                        }, $currentState);
                    }

                    $diffState = array_diff($selState1, $reqState);

                    foreach ($diffState as $key => $value) {

                        $deleteResult = $this->employee_model->delete_agent_state_mapping($asmID, $value);
                        $this->service_centers_model->update_service_centers_by_state(array('asm_id' => null), array('asm_id' => $asmID, 'state' => $value));
                    }


                    $diffState1 = array_diff($reqState, $selState1);

                    foreach ($diffState1 as $key => $value) {
                        $this->employee_model->insert_agent_state_mapping($asmID, $value, $this->session->userdata('id'));
                        $this->service_centers_model->update_service_centers_by_state(array('asm_id' => $asmID), array('state' => $value));
                    }
                }

                if ($rm_ID != 0 && $rm_ID != '') {
                    $currentState = $this->employee_model->get_rm_mapped_state($rm_ID);
                    $reqState = array();
                    $selState1 = array();
                    foreach ($data['state_name'] as $key => $value) {
                        array_push($reqState, $value);
                    }
                    if (!empty($currentState)) {
                        $selState1 = array_map(function ($entry) {
                            return $entry['state'];
                        }, $currentState);
                    }

                    $diffState = array_diff($selState1, $reqState);

                    foreach ($diffState as $key => $value) {
                        if ($isRM) {
                            $deleteResult = $this->employee_model->delete_agent_state_mapping($rm_ID, $value);
                            $this->service_centers_model->update_service_centers_by_state(array('rm_id' => null), array('rm_id' => $rm_ID, 'state' => $value));
                        }
                    }
                    $diffState1 = array_diff($reqState, $selState1);

                    foreach ($diffState1 as $key => $value) {
                        $this->employee_model->insert_agent_state_mapping($rm_ID, $value, $this->session->userdata('id'));
                        $this->service_centers_model->update_service_centers_by_state(array('rm_id' => $rm_ID), array('state' => $value));
                    }
                }
                $data["msg"] = 'Data updated successfully.';
                $data['employee_rm'] = $this->employee_model->get_rm_details();
                $data['state'] = $this->employee_model->get_states();
                $data['error'] = $this->session->flashdata('error');
                $this->miscelleneous->load_nav_header();
                $this->load->view('employee/rm_state_mapping', $data);
            } else {
                $data["msg"] = $errormessage;
                $data['employee_rm'] = $this->employee_model->get_rm_details();
                $data['state'] = $this->employee_model->get_states();
                $data['error'] = $this->session->flashdata('error');
                $this->miscelleneous->load_nav_header();
                $this->load->view('employee/rm_state_mapping', $data);
            }
        } else {
            $data["msg"] = '';
            $data['employee_rm'] = $this->employee_model->get_rm_details();
            $data['state'] = $this->employee_model->get_states();
            $data['error'] = $this->session->flashdata('error');
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/rm_state_mapping', $data);
        }
    }

    /**
     * @Desc: This function is used to process employee add form
     * @parmas: POST Array
     * @return: void
     * 
     */
    function process_add_employee(){
        $data = $this->input->post();
        // Save value of region in a varibale if added employee is an RM
        if(!empty($data['region']))
        {
            $rm_region = $data['region'];
            unset($data['region']);
        }
        $removeKeys = array('manager', 'subordinate');
        $data1=array_diff_key($data, array_flip($removeKeys));        
        
        if($data == $data1)
            exit("Please add manager or subordinate!");
        
        $data1['groups']= str_replace(' ', '', $data1['groups']);
        
        $data1['clear_password'] = $this->randomPassword();
        $data1['employee_password'] = md5($data1['clear_password']);
        $data1['create_date'] = date('Y-m-d H:i:s');
        if($data['groups'] == 'inventory_manager'){
            $data1['warehouse_id'] = trim($data['warehouse_id']);
        }
        
        $maxid = $id = 0;
        $row = $this->db->query('SELECT MAX(id) maxid FROM employee')->row();
        if ($row) {
            $id = $row->maxid; 
        }

        ++$id;
        $maxid=10000+$id;

        do {
            $row = $this->db->query('SELECT * FROM employee where employee_id='.$maxid)->result_array();
        }while (count($row)>0);
        $data1['employee_id'] = $maxid;
        
        $data2 = $data4 = $emp_rel = array();
        
        if(!empty($data['subordinate'])) {
            $subordinate=$this->input->post('subordinate');
            
            foreach($subordinate as $key=>$val) {
                $data2[]=array("id" => $val, "manager" => $id);
            }
        }
        
        if(count($data2) > 0) {
            foreach($data2 as $key=>$val) {
                $data4=$this->employee_model->getemployeeManagerDetails("employee_hierarchy_mapping.*",array('employee_hierarchy_mapping.employee_id' => $val['id']));
                
                if(count($data4) > 0) {
                    $sub_data=$this->employee_model->getemployeefromid($val['id']);
                    //Logging error if there is already manager added to any subordinate
                    log_message('info', __FUNCTION__ . $sub_data[0]['full_name']." already has one Manager");
                    $this->session->set_flashdata('error',$sub_data[0]['full_name']." already has one Manager.");
                    redirect(base_url() . "employee/user/add_employee");
                }
            }
            
            //adding subordinate
            $this->employee_model->insertManagerData($data2);
        }

        $data2 = array();
        
        if(!empty($data['manager'])) {
            $manager=$this->input->post('manager');
            
            $data2[]=array("id" => $id, "manager" => $manager);
            
        }
        
        
        $id = $this->employee_model->insertData($data1);
        

        //adding managers
        if(count($data2) > 0) {
            $this->employee_model->insertManagerData($data2);
        }
        
        //add a blank row in employee_relation only when user is regional manager
        //code updated dt: 8/21/2019 by PB
        if (($data1['groups'] == _247AROUND_RM)) {
            //If the new added is RM and has subordinates it will update their mapping onto his
            $res = $this->employee_model->update_new_rm_mapping($id);
            if (!empty($data['subordinate'])) {
                $subOrdinate = $data['subordinate'];
                $arrayState = array();
                foreach ($subOrdinate as $key => $asmID) {
                    $currentState = $this->employee_model->get_rm_mapped_state($asmID);
                    $arrayState = array_merge($arrayState, $currentState);
                }
                if (!empty($arrayState)) {
                    $arrayStateStr = array_map(function($element) {
                        return $element['state'];
                    }, $arrayState);
                    $result = $this->employee_model->get_state_of_rm_asm($arrayStateStr, _247AROUND_RM, $id);
                    $resultArray = array();
                    if (!empty($result)) {
                        $resultArray = array_map(function($element) {
                            return $element['state'];
                        }, $result);
                    }
                    foreach ($arrayState as $key => $value) {
                        $state = $value['state'];
                        if (!in_array($state, $resultArray)) {
                            $this->employee_model->insert_agent_state_mapping($id, $state, $this->session->userdata('id'));
                            $this->service_centers_model->update_service_centers_by_state(array('rm_id' => $id), array('state' => $state));
                        }
                    }
                }
            }
        }
        //*********END
        
        // Update region (North,South,East,West) with its respective RM 
        if(($data1['groups'] == _247AROUND_RM) && !empty($rm_region) && $id){
            $this->employee_model->map_region_to_rm($rm_region, $id);
        }
        // END
        
        $tag='employee_login_details';
        if(!$this->process_mail_to_employee($tag,$id,$manager)) {
            //Logging error if there is some error in sending mail
            log_message('info', __FUNCTION__ . " Sending Mail Error..  ");
            $error = ' Employee Added Successfully but Mail can not be Sent.. ';
            $this->session->set_flashdata('error', $error);
            redirect(base_url() . "employee/user/add_employee");
        }
        
        $this->session->set_userdata('success','Employee Added Successfully.');
        
        if(($data1['groups'] == _247AROUND_RM) || ($data1['groups'] == _247AROUND_ASM)){
            redirect(base_url() . "employee/user/rm_state_mapping/".$id);
        }else {
             redirect(base_url() . "employee/user/show_employee_list");
        }
    }
    
    /**
     * @Desc: This function is used to show employee list
     * @params: void
     * @return: view
     * 
     */
    function show_employee_list(){
        $data['data'] = $this->employee_model->get_employee();
        foreach($data['data'] as $key => $value) {
            $data['data'][$key]['manager'] = $this->employee_model->getemployeeManagerDetails("employee.*",array('employee_hierarchy_mapping.employee_id' => $value['id']));
        }
        
        $data['session_data'] = $this->session->all_userdata();
        $data['c2c'] = $this->booking_utilities->check_feature_enable_or_not(CALLING_FEATURE_IS_ENABLE);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/employee_list',$data);
    }
    
    /**
     * @Desc: This function is used to edit an employee
     * @parmas: id of employee
     * @return: view
     * 
     */
    function update_employee($id = ""){   
        $data['id'] = $this->session->userdata('id');
        $data['user_group'] = $this->session->userdata('user_group');
        
        $id = ((!$id) ? $data['id'] : $id);
                
        $data['query'] = $this->employee_model->getemployeefromid($id);
        $employee_list = $this->employee_model->get_employee($id);
        $data['employee_list']=array_filter($employee_list, function($v) use($id) {return $v['id'] != $id;});
        
        $cond= array('where' => array('entity_type'=>'247Around'), 'order_by' => 'department');
        $data['employee_dept'] = $this->employee_model->get_entity_role('department',$cond);
        $data['regions'] = $this->employee_model->get_regions();
        if(!empty($data['employee_dept'])) {
            $cond= array('where' => array('entity_type'=>'247Around', 'department' => $data['query'][0]['department']), 'order_by' => 'role');
            $data['employee_role'] = $this->employee_model->get_entity_role('role',$cond);
        }
        
        $manager=$this->employee_model->getemployeeManagerDetails("employee_hierarchy_mapping.*",array('employee_hierarchy_mapping.employee_id' => $id));
        $subordinate=$this->employee_model->getemployeeManagerDetails("employee_hierarchy_mapping.*",array('employee_hierarchy_mapping.manager_id' => $id));
        
        if(!empty($manager)) {
            $data['manager']=$manager[0]['manager_id'];
        }
        if(!empty($subordinate)) {
            $data['subordinate']=$subordinate;
        }
        
        $data['error'] = $this->session->flashdata('error');
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/employee_add_edit',$data);
    }
    
    /**
     * 
     * @Desc: This function is used to prcess update employee form
     * @params: POST array
     * @return: view
     */
    function process_edit_employee(){
        $data = $this->input->post();
        $isRM = count($this->employee_model->isRManager($data['id'])) > 0 ? true : false;
        //If agent is not RM and is being promoted to RM then it has to unmap itself from all service centers and states
        if(!$isRM && $data['groups']==_247AROUND_RM){
            $result_district_mapped = $this->employee_model->get_rm_mapped_district($data['id']);
            if(!empty($result_district_mapped)){
                 $this->session->set_flashdata('error','Some district already mapped with this agent, Please remove them first.');
                 redirect(base_url() . "employee/user/update_employee/".$data['id']);
            }
        }
        $removeKeys = array('manager', 'subordinate');
        $data1=array_diff_key($data, array_flip($removeKeys));
        
        $data1['groups']= str_replace(' ', '', $data1['groups']);
        // Update region (North,South,East,West) with its respective RM 
        if(!empty($data1['region']))
        {
            if(($data1['groups'] == _247AROUND_RM) && !empty($data1['id'])){
                $this->employee_model->map_region_to_rm($data1['region'], $data1['id']);
            }
            // unset region from array to avoid insertion in employee table
            unset($data1['region']);
        }
        $this->employee_model->update($data1['id'],$data1);      
        $data2 = array();
        if(!empty($data['manager'])) {
            $manager=$this->input->post('manager');
            
            $data2[]=array("id" => $data1['id'], "manager" => $manager);
            
        }
        
        if(count($data2) > 0) {
            $data3=$this->employee_model->getemployeeManagerDetails("employee_hierarchy_mapping.*",array('employee_hierarchy_mapping.employee_id' => $data1['id']));

            if(count($data3) <= 0) {
                $this->employee_model->insertManagerData($data2);
            }
            else {
                $this->employee_model->updateManager($data2);
            }
        }
        
        
        $data2 = $data4 = array();
        
        if(!empty($data['subordinate'])) {
            $subordinate=$this->input->post('subordinate');
            
            foreach($subordinate as $key=>$val) {
                $data2[]=array("id" => $val, "manager" => $data1['id']);
            }
        }
        
        if(count($data2) > 0) {
            foreach($data2 as $key=>$val) {
                $data4=$this->employee_model->getemployeeManagerDetails("employee_hierarchy_mapping.*",array('employee_hierarchy_mapping.employee_id' => $val['id']));
                
                if((count($data4) > 0) && ($data4[0]['manager_id'] !== $data1['id'])) {
                    $sub_data=$this->employee_model->getemployeefromid($val['id']);
                    //Logging error if there is already manager added to any subordinate
                    log_message('info', __FUNCTION__ . $sub_data[0]['full_name']." already has one Manager");
                    $this->session->set_flashdata('error',$sub_data[0]['full_name']." already has one Manager.");
                    redirect(base_url() . "employee/user/update_employee/".$data1['id']);
                }
            }

            $data3=$this->employee_model->getemployeeManagerDetails("employee_hierarchy_mapping.*",array('employee_hierarchy_mapping.manager_id' => $data1['id']));
            
            if(count($data3) > 0) {
                $this->employee_model->deleteManager("manager_id in (".$data1['id'].")");
            }
            $this->employee_model->insertManagerData($data2);
        }
        
        if (($data1['groups'] == _247AROUND_RM)) {

            //If the  is RM and has subordinates it will update their mapping onto his
            if (!empty($data['subordinate'])) {
                $subOrdinate = $data['subordinate'];
                $arrayState = array();
                foreach ($subOrdinate as $key => $asmID) {
                    $currentState = $this->employee_model->get_rm_mapped_state($asmID);
                    $arrayState = array_merge($arrayState, $currentState);
                }
                $currentStateOfRm = $this->employee_model->get_rm_mapped_state($data['id']);
                if (!empty($currentStateOfRm)) {
                    $currentStateOfRm = array_map(function($element) {
                        return $element['state'];
                    },
                            $currentStateOfRm
                    );
                }

                if (!empty($arrayState)) {
                    $arrayStateStr = array_map(function($element) {
                        return $element['state'];
                    }, $arrayState);
                    //$arrayStateStr ="'".$arrayStateStr."'";
                    $result = $this->employee_model->get_state_of_rm_asm($arrayStateStr, _247AROUND_RM, $data['id']);
                    $resultArray = array();
                    if (!empty($result)) {
                        $resultArray = array_map(function($element) {
                            return $element['state'];
                        }, $result);
                    }
                    foreach ($arrayState as $key => $value) {
                        $state = $value['state'];
                        if (!in_array($state, $currentStateOfRm) && !in_array($state, $resultArray)) {
                            $this->employee_model->insert_agent_state_mapping($data['id'], $state, $this->session->userdata('id'));
                            $this->service_centers_model->update_service_centers_by_state(array('rm_id' => $data['id']), array('state' => $state));
                        }
                    }
                }
            }
        }

        $this->session->set_userdata('success','Employee Updated Successfully.');
        
        redirect(base_url() . "employee/user/show_employee_list");
    }
    
    /**
     * 
     * @Desc: This function is used to inactive employee
     * @params: employee id
     * @return: view
     */
    function deactive_employee($id){
        // check if any SF is associated with the Employee
        // If yes, Employee can not be de-activated
        $arr_vendors = $this->vendor_model->get_sf_associated_with_rm($id);
        if(!empty($arr_vendors[0]['individual_service_centres_id']))
        {
            $this->session->set_userdata('error',EMP_DEACTIVATION_ERROR);
            redirect(base_url() . "employee/user/show_employee_list");
        }
        $data = array("active"=>0);
        $this->employee_model->update($id,$data);
        // remove state mapping from employee
        $currentState = $this->employee_model->get_rm_mapped_state($id);
        if (!empty($currentState)) {
            foreach ($currentState as $key => $value) {
                $state = $value['state'];
                $deleteResult = $this->employee_model->delete_agent_state_mapping($id, $state);
            }
        }

        $this->session->set_userdata('success','Employee Updated Sucessfully.');
        redirect(base_url() . "employee/user/show_employee_list");
    }
    
    /**
     * 
     * @Desc: This function is used to reset employee password
     * @params: employee id
     * @return: view
     */
    function reset_password($id){
        $data['clear_password'] = $this->randomPassword();
        $data['employee_password'] = md5($data['clear_password']);
        $this->employee_model->update($id,$data);
        $manager=$this->employee_model->getemployeeManagerDetails("employee_hierarchy_mapping.*",array('employee_hierarchy_mapping.employee_id' => $id));
        if(!empty($manager))
            $manager = $manager[0]['manager_id'];
        $tag='employee_reset_password';
        if(!$this->process_mail_to_employee($tag,$id,$manager)) {
            //Logging error if there is some error in sending mail
            log_message('info', __FUNCTION__ . " Sending Mail Error..  ");
            echo json_encode(array('status' => "error", "message" => " Sending Mail Error..  "));
        }
        else {
            echo json_encode(array('status' => "success", "message" => "Password Reset Sucessfully."));
        }
    }
    /**
     *@Desc: This function is used to show holiday list to employees
     * @params: void
     * @return: void 
     * 
     */
    function show_holiday_list(){
        $data['data'] = $this->employee_model->get_holiday_list();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/show_holiday_list',$data);
    }
    
 /**
     * @desc function change password of admin.
     * @author Ankit Rajvanshi.
     * @since 17-May-2019.
     */
    function change_password() {
        
        if($this->input->is_ajax_request()) { // verify old password.
            echo $this->user_model->verify_entity_password(_247AROUND_EMPLOYEE_STRING, $this->session->userdata['id'], $this->input->post('old_password'));exit;
        } elseif($this->input->post()) {
            // Update password.
            $this->user_model->change_entity_password(_247AROUND_EMPLOYEE_STRING, $this->session->userdata['id'], $this->input->post('new_password'));
            // send change password mail.
            $to = (!empty($employee[0]['official_email']) ? $employee[0]['official_email'] : (!empty($employee[0]['personal_email']) ? $employee[0]['personal_email'] : NULL));
            if(!empty($to)) :
                $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", "Password Changed", "Password has been changed successfully.", "", CHANGE_PASSWORD);
                log_message('info', __FUNCTION__ . 'Change password mail sent.');
            endif;            
            
            // setting feedback message for user.
            $this->session->set_userdata(['success' => 'Password has been changed successfully.']);
            redirect(base_url() . "employee/user/change_password");
        }
        
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/change_password');
    }
    
     /**
     *@Desc: This function is used to get random password of length 8 
     * @params: void
     * @return: void 
     * 
     */
    function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
    /**
     * @desc: This function is used to send mail to newly created employee or if password is reset
     * params: employee id
     * return: void
     * 
     */
    function process_mail_to_employee($tag,$id,$manager_id) {
        //Setting flag as TRUE ->Success and FALSE -> Failure
        $flag = TRUE;
        $attachment = "";
        //Get email template values for employee
        $email = array();
        $email['where'] = array(
            'entity' => 'employee',
            'template' => $tag
        );
        $email['select'] = '*';
        $email_template = $this->vendor_model->get_247around_email_template($email);
        
        if (!empty($email_template)) {
            $template_value = $email_template[0]['template_values'];
            //Making array for template values 
            $template_array = explode(',', $template_value);
            
            //Getting value in array from template_values column
            foreach ($template_array as $val) {
                $table['table_name'] = explode('.', $val)[0];
                $table['column_name'] = explode('.', $val)[1];
                $table['primary_key'] = explode('.', $val)[2];
                $template[] = $table;
            }
            

            $employee_details = $this->employee_model->getemployeefromid($id);
            
            //Setting TO for Email
            $to = (($employee_details[0]['official_email'] != '')?$employee_details[0]['official_email']:'');
            
            if(!empty($manager_id)) {
                $manager_details = $this->employee_model->getemployeefromid($manager_id);
                // $to .= (($manager_details[0]['official_email'] != '')? (','.$manager_details[0]['official_email']):'');
            }
            
            if(!empty($email_template[0]['to'])) {
                $to .= ','.$email_template[0]['to'];
            }
            
            $to = trim($to,',');
            
            if($to == '') {
                //Logging error if there is some error in sending mail
                log_message('info', __FUNCTION__ . " No Sender for email.  ");
                return FALSE;
            }
            
            $temp=array();
            foreach ($template as $value) {
                $value['id'] = $id;
                //Getting employee details
                $employee_data = $this->vendor_model->get_data($value);

                if ($employee_data) {
                    $temp[] = $employee_data[0][$value['column_name']];
                } else {
                    //Logging error when values not found
                    log_message('info', __FUNCTION__ . ' Mail send Error. No data found to the following employee ID ' . $employee_details[0]['id']);
                    log_message('info', __FUNCTION__ . ' Template values are - ' . print_r($value, TRUE));
                    //Set Flag to check success or error of AJAX call
                    $flag = FALSE;
                }
            }
            //Sending Mail to the employee
            if (!empty($temp)) {
                $emailBody = vsprintf($email_template[0]['body'], $temp);
                //Sending Mail
                $this->notify->sendEmail($email_template[0]['from'], $to, $email_template[0]['cc'], $email_template[0]['bcc'], $email_template[0]['subject'], $emailBody, $attachment,$email_template[0]['template']);
                //Login send mail details
                log_message('info', __FUNCTION__ . ' Mail send to the following employee ID ' . $employee_details[0]['id']);
                //Set Flag to check success or error
                $flag = TRUE;
            }
        }
        return $flag;
    }
    /**
     * @Desc: This function is used to get role based on department
     * @params: void
     * @return: view
     * 
     */
    function get_role_on_department(){
        $department = $this->input->post('department');
        
        $data = array();
        if(!empty($department))
        {
            $cond= array('where' => array('entity_type'=>'247Around', 'department' => $department), 'order_by' => 'role');
            $data = $this->employee_model->get_entity_role('role',$cond);
        }
        echo json_encode($data);
    }
    /**
     * @Desc: This function is used to get districts from states
     * @params: void
     * @return: view
     * @ Ghanshyam Ji Gupta
     */
    function bring_district_from_state() {
        $state_selected = $this->input->post('state_name');
        $agent_ID = $this->input->post('agent_ID');
        $rm_ID="";
        if(!empty($state_selected) && !empty($agent_ID))
        {
        $state_selected_string = implode("','", $state_selected);
        $state_selected_string = "'$state_selected_string'";
        
        $currentDistrict = $this->employee_model->get_rm_mapped_district($agent_ID);
        $isRM = count($this->employee_model->isRManager($agent_ID)) > 0 ? true : false;
        $reqDistrict = $html = '';
        if ($isRM) {
        $rm_ID = $agent_ID;
        $asmID = 0;
        $result = $this->employee_model->get_district_of_rm_asm($reqDistrict, _247AROUND_RM, $agent_ID);
        } else {
        $asmID = $agent_ID;
        $rm_ID_Array = $this->employee_model->getemployeeManagerfromid(array('employee_id' => $agent_ID));
        if(!empty($rm_ID_Array[0]['manager_id'])){
            $rm_ID = $rm_ID_Array[0]['manager_id'];
        }
        $result1 = $this->employee_model->get_district_of_rm_asm($reqDistrict, _247AROUND_RM, $rm_ID);
        $result2 = $this->employee_model->get_district_of_rm_asm($reqDistrict, _247AROUND_ASM, $agent_ID);
        
        if (!empty($result1) && !empty($result2)) {
                $result = array_merge($result1, $result2);
            } else if (!empty($result1)) {
                $result = $result1;
            } else if (!empty($result2)) {
                $result = $result2;
            }
        }
        $zone_result = $this->employee_model->get_rm_details(array(_247AROUND_RM),$rm_ID);
        if(!empty($zone_result) ){
          $zone_id = $zone_result[0]['zone_id'];
        }
        if (!empty($result)) {
            $resultOtherAgentDistrict = array_map(function ($entry) {
                return $entry['id'];
            }, $result);
        } else {
            $resultOtherAgentDistrict = array();
        }
        $currentDistrictArray = array_map(function ($entry) { return $entry['id'];}, $currentDistrict);   
        $resultArray = $this->employee_model->get_district_from_states($state_selected_string);
        $array_state_district = array();
        foreach($resultArray as $key => $value)
        {
            $array_state_district[$value['state']][$value['id']]['district']    =   $value['district'];
            $array_state_district[$value['state']][$value['id']]['zone_id']     =   $value['zone_id'];
        }
        
        $count = 0;
        foreach($array_state_district as $key => $value)
        {
          
            $count = $count+1;
            $html .='<div class="card" style="margin-bottom: 4px;">
                  <div class="card-header" role="tab" id="headingOne'.$count.'">
                     <div class="mb-0">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne'.$count.'" aria-expanded="false" aria-controls="collapseOne'.$count.'" class="collapsed">
                           <i class="fa fa-file-text-o" aria-hidden="true"></i>
                           <h3>'.$key.' </h3>                           
                        </a>
                        <input type="checkbox"  id="selectall'.$count.'" onclick="selectall('.$count.')"> Select All
                     </div>
                  </div>
                  <div id="collapseOne'.$count.'" class="collapse in" role="tabpanel" aria-labelledby="headingOne'.$count.'" aria-expanded="false" style="">
                     <div class="card-block" style="height: auto;overflow:hidden">';
                     $countr=0;
                     foreach($value as $key_d => $value_d)
                     {
                         $countr = $countr+1;
                         $disabled ='';
                         $checked = '';
                         $style='';
                         $title ='';
                         if(in_array($key_d,$currentDistrictArray))
                         {
                            $checked = 'checked';
                         }
                         $class ="myselectall$count";

                         if(in_array($key_d,$resultOtherAgentDistrict))
                         {
                             $checked = '';
                             $disabled ='disabled';
                             $class = '';
                             $style=";color:#ccc";
                             $title ='You can not map this district as this is already mapped with other agent.';
                         }
                         if(!empty($zone_id) && $zone_id!=$value_d['zone_id']){
                             /*$checked = '';
                             $disabled ='disabled';
                             $class = '';
                             $style=";color:#ccc";
                             $title ='You can not map this district as this is district does not belong to zone of selected user.';*/
                         }

                        $html .="<div class='col-md-3' style='padding:5px 0px$style' title='$title'><span data-toggle='tooltip' title='$title' data-placement='right'><input type='checkbox' $checked class='$class ' $disabled name='district[]' value='$key_d'>&nbsp;&nbsp;".$value_d['district']."</span></div>"; 
                     }
                        
                  $html .='</div>
                  </div>
               </div>';
        }
        
        echo $html;
        }
        
    }
     /**
     * @Desc: This function is used to map rm and asm with district
     * @params: void
     * @return: view
     * @ Ghanshyam Ji Gupta
     */
    function rm_asm_district_mapping() {
        $data = $this->input->post();
        $rm_asm = $this->input->post('rm_asm');
        $state_name = $this->input->post('state_name');
        $district = $this->input->post('district');
        $array_return['status'] = 'success';
        $array_return['message'] = 'Mapping Updated Successfully.';
        $isRM = count($this->employee_model->isRManager($rm_asm)) > 0 ? true : false;
        $Submit = true;
        $statusFlg = true;
        if ($isRM) {
            $rm_ID = $data["rm_asm"];
            $asmID = 0;
        } else {
            $asmID = $data["rm_asm"];
            $rm_ID_Array = $this->employee_model->getemployeeManagerfromid(array('employee_id' => $rm_asm));
            $rm_ID = $rm_ID_Array[0]['manager_id'];
        }
        $reqDistrict = array();
        $selDistrict = array();
        if(!empty($district)){
        foreach ($district as $key => $value) {
            array_push($reqDistrict, $value);
        }}
        $reqDistrictString = implode("','", $reqDistrict);
        
        if(empty($district))
        {
            $district = array();
            //$errormessage = "No district selected.";
            //$statusFlg = false;
            //$array_return['status'] = 'error';
            //$array_return['message'] = $errormessage;
        }
        ##########################check if state served by other ASM#####################################
        if (!$isRM && $statusFlg && !empty($reqDistrict)) {
            $result = $this->employee_model->get_district_of_rm_asm($reqDistrict, _247AROUND_ASM, $asmID);
            if (count($result) > 0) {
                $stateString = implode(',', array_map(function ($entry) {
                            return $entry['district'];
                        }, $result));
                $errormessage = "District $stateString already served by other asm you can not assign to this ASM.";
                $statusFlg = false;
                $array_return['status'] = 'error';
                $array_return['message'] = $errormessage;
            }
        }
        #########################check if District served by other RM#######################################
        if ($rm_ID != 0 && $rm_ID != '' && $statusFlg && !empty($reqDistrict)) {
            $result = $this->employee_model->get_district_of_rm_asm($reqDistrict, _247AROUND_RM, $rm_ID);
            if (count($result) > 0) {
                $stateString = implode(',', array_map(function ($entry) {
                            return $entry['district'];
                        }, $result));
                $errormessage = "District $stateString already served by other RM you can not assign this RM.";
                $statusFlg = false;
                $array_return['status'] = 'error';
                $array_return['message'] = $errormessage;
            }
        }
        ######################check rm is removing its asm district from itself###############################
        if ($isRM && $statusFlg) {
            $currentDistrict = $this->employee_model->get_rm_mapped_district($rm_ID);
            $currentDistrictArray = array_map(function ($entry) {
                return $entry['id'];
            }, $currentDistrict);
            $diffDistrict = array_diff($currentDistrictArray, $reqDistrict);
            $result = $this->employee_model->get_asm_from_rm_district($diffDistrict, $rm_ID);
            if (!empty($result)) {
                $stateString = implode(', ', array_map(function ($entry) {
                            return $entry['district'];
                        }, $result));
                $errormessage = "RM has ASM mapped with $stateString. you can not remove these states from RM, Remove from ASM first.";
                $statusFlg = false;
                $array_return['status'] = 'error';
                $array_return['message'] = $errormessage;
            }
        }
        if ($statusFlg) {
            // print_r($reqDistrict); 
            if ($asmID != 0) {
                $currentDistrict = $this->employee_model->get_rm_mapped_district($asmID);
                $currentDistrictArray = array_map(function ($entry) {
                    return $entry['id'];
                }, $currentDistrict);
                $diffDistrict = array_diff($currentDistrictArray, $reqDistrict);
                // Above array contains all district which are currently mapped to user (ASM) but now unmapping
                foreach ($diffDistrict as $key => $value) {
                    $deleteResult = $this->employee_model->delete_agent_district_mapping($asmID, $value);
                    $update_string ="asm_id = null";
                    $this->employee_model->update_asm_rm_service_center($update_string,$value);
                }
                $diffNewDistrict = array_diff($reqDistrict, $currentDistrictArray);
                foreach ($diffNewDistrict as $key => $value) {
                    $this->employee_model->insert_agent_district_mapping($asmID, $value, $this->session->userdata('id'));
                    $update_string ="asm_id = $asmID";
                    $this->employee_model->update_asm_rm_service_center($update_string,$value);
                }
            }
            if ($rm_ID != 0 && $rm_ID != '') {
                $currentDistrict = $this->employee_model->get_rm_mapped_district($rm_ID);
                $currentDistrictArray = array_map(function ($entry) {
                    return $entry['id'];
                }, $currentDistrict);
                $diffDistrict = array_diff($currentDistrictArray, $reqDistrict);
                // Above array contains all district which are currently mapped to user but now unmapping
                foreach ($diffDistrict as $key => $value) {
                    if ($isRM) {
                        $deleteResult = $this->employee_model->delete_agent_district_mapping($rm_ID, $value);
                        $update_string ="rm_id = null";
                        $this->employee_model->update_asm_rm_service_center($update_string,$value);
                    }
                }
                $diffNewDistrict = array_diff($reqDistrict, $currentDistrictArray);
                foreach ($diffNewDistrict as $key => $value) {
                    $this->employee_model->insert_agent_district_mapping($rm_ID, $value, $this->session->userdata('id'));
                    $update_string ="rm_id = $rm_ID";
                    $this->employee_model->update_asm_rm_service_center($update_string,$value);
                }
            }
        }
        echo json_encode($array_return);
    }
    
    /*
     * @Desc: This function is used to get warehouse details
     * @params: void
     * @return: view
     */
    
    function get_warehouse_list() {
        $select = "service_centres.district, service_centres.id,service_centres.state, service_centres.name";
        $where = array('is_wh' => 1, 'active' => 1);
        $warehouse_list = $this->vendor_model->getVendorDetails($select, $where,'name', array(), array(),array());
        if (!empty($warehouse_list)) {
            $option = '<option selected="" disabled="">Select Warehouse</option>';
            foreach ($warehouse_list as $value) {
                $option .= "<option value='" . $value['id'] . "'";
                $option .= _247AROUND_EMPLOYEE_STRING . " " . $value['district'] . " ( <strong>" . $value['state'] . " </strong>) - (Central Warehouse)" . "</option>";
            }
        }
        echo $option;
    }
    /**
     * @desc this is used to show purchase date / warranty status as per booking Date / warranty status as per current date
     * @param int $booking_id
     * @author Ghanshyam
     * @created_on 18-04-2020
     */
    function check_warranty_booking_search() {
        $array['purchase_date'] = '';
        $array['booking_warranty_status'] = '';
        $array['current_warranty_status'] = '';
        $post_data = $this->input->post();
        $booking_id = $post_data['booking_id'];
        if (!empty($booking_id)) {
            $arrBookings = $this->warranty_utilities->get_warranty_specific_data_of_bookings(array($booking_id));
            if (!empty($arrBookings)) {
                $array['purchase_date'] = $arrBookings[0]['purchase_date'];
                $warranty_status_as_per_booking_date = $this->warranty_utilities->get_warranty_status_of_bookings($arrBookings);
                $array['booking_warranty_status'] = $warranty_status_as_per_booking_date[$booking_id];
                /*
                 * Changing Booking Date to current Date to get warranty status as per current Date
                 */
                $arrBookings[0]['booking_create_date'] = date('Y-m-d');
                $warranty_status_as_per_current_date = $this->warranty_utilities->get_warranty_status_of_bookings($arrBookings);
                $array['current_warranty_status'] = $warranty_status_as_per_current_date[$booking_id];
            }
        }
        echo json_encode($array);
    }

}
