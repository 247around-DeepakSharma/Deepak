<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Inventory extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();
        $this->load->model('service_centers_model');
        $this->load->model('vendor_model');
        $this->load->model('inventory_model');
        $this->load->model('booking_model');
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->library('notify');
    }

    public function index() {
        
    }

    /**
     * @desc: This function is used to get bracket add form
     * @params: void
     * @return: view
     * 
     */
    public function get_bracket_add_form() {
        //Getting ID from Session
        $id = $this->session->userdata('id');
        //Getting Employee Relation if present
        $sf_list = $this->vendor_model->get_employee_relation($id);
            if (!empty($sf_list)) {
                // Listing details acc to SF mapped
                $sf_list = $sf_list[0]['service_centres_id'];
                $service_center = $this->vendor_model->getActiveVendor("", 0);
                $sf_array = explode(',',$sf_list);
                foreach($service_center as $key=>$value){
                    if(array_search($value['id'],$sf_array)){
                        $data['vendor'][] = $value;
                    }
                }
            }else{
                //Getting all values
                $data['vendor'] = $this->vendor_model->getActiveVendor();
            }
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view("employee/add_brackets", $data);
    }

    /**
     * @desc: This function is used to process add brackets form
     * @params: Array
     * @return: void
     */
    public function process_add_brackets_form() {
        //Form Validation
        $this->form_validation->set_rules('order_received_from', 'Order Received From', 'required');
        $this->form_validation->set_rules('order_given_to', 'Order Given To', 'required');
        $this->form_validation->set_rules('choice', 'Choice', 'required');
        if ($this->form_validation->run()) {

            $data = $this->input->post();
            foreach ($data['choice'] as $key => $value) {
                
                // New Pattern for Order ID for brackets
                $order_id_array = $this->inventory_model->get_latest_order_id();
                if(empty($order_id_array[0]['order_id'])){
                   $order_id = _247_AROUND_DEFAULT_BRACKETS_ORDER_ID; 
                }else{
                    $order_id = $order_id_array[0]['order_id'] + 1;
                }
                
                //Making array for Brackets Database
                $data_post[] = array(
                    'order_id' => $order_id,
                    'order_received_from' => $data['order_received_from'][$value],
                    'order_given_to' => $data['order_given_to'][$value],
                    '19_24_requested' => $data['_19_24'][$value],
                    '26_32_requested' => $data['_26_32'][$value],
                    '36_42_requested' => $data['_36_42'][$value],
                    'order_date' => date('Y-m-d h:i:s'),
                    'total_requested' => ($data['_19_24'][$value] + $data['_26_32'][$value] + $data['_36_42'][$value]),
                );
            }

            //Inserting data in Brackets Database
            $check_flag = $this->inventory_model->insert_brackets($data_post);
            if ($check_flag) {
                //Logging Success
                log_message('info', __FUNCTION__ . ' Brackets have been added successfully ' . print_r($data_post, TRUE));

                //Logging Success
                log_message('info', __FUNCTION__ . ' Brackets Requested- Pending state have been added in Booking State Change ');

                // Sending Mail to order received from vendors
                foreach ($data_post as $val) {
                    //Adding value in Booking State Change
                    $this->notify->insert_state_change($val['order_id'], _247AROUND_BRACKETS_PENDING, _247AROUND_BRACKETS_REQUESTED, "Brackets Requested", $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);
                    
                    $vendor_requested = $this->vendor_model->getVendorContact($val['order_received_from']);
                    
                    $vendor_poc_mail = $vendor_requested[0]['primary_contact_email'];
                    $vendor_owner_mail = $vendor_requested[0]['owner_email'];
                    $to = $vendor_poc_mail.','.$vendor_owner_mail;
                    
                    // Sending brackets confirmation details mail to Vendor using Template
                   $email = array();
                   //Getting template from Database
                   $template = $this->booking_model->get_booking_email_template("brackets_order_received_from_vendor");
                   
                   if(!empty($template)){
                        $email['company_name'] = $vendor_requested[0]['company_name'];
                        $email['19_24_requested'] = $val['19_24_requested'];
                        $email['26_32_requested'] = $val['26_32_requested'];
                        $email['36_42_requested'] = $val['36_42_requested'];
                        $email['total_requested'] = $val['total_requested'];
                        $subject = "Brackets Requested";
                        $emailBody = vsprintf($template[0], $email);
                        $this->notify->sendEmail($template[2], $to , $template[3], '', $subject , $emailBody, "");
                   }
                    
                    //Logging Email Send to order received from vendor
                    log_message('info', __FUNCTION__ . ' Email has been sent to order_received_from vendors '.print_r($vendor_requested[0]['company_name']));
                
                    //Sending Mail to order given to
                    $vendor_requested_to = $this->vendor_model->getVendorContact($val['order_given_to'])[0];
                    
                    $vendor_poc_mail = $vendor_requested_to['primary_contact_email'];
                    $vendor_owner_mail = $vendor_requested_to['owner_email'];
                    $to = $vendor_poc_mail.','.$vendor_owner_mail;

                    // Sending Mail to order given to vendor using Template
                   $email = array();
                   //Getting template from Database
                   $template = $this->booking_model->get_booking_email_template("brackets_requested_from_vendor");
                   
                   if(!empty($template)){
                        $email['19_24_requested'] = $val['19_24_requested'];
                        $email['26_32_requested'] = $val['26_32_requested'];
                        $email['36_42_requested'] = $val['36_42_requested'];
                        $email['total_requested'] = $val['total_requested'];
                        $email['owner_name'] = $vendor_requested[0]['owner_name'];
                        $email['company_name'] = $vendor_requested[0]['company_name'];
                        $email['address'] = $vendor_requested[0]['address'];
                        $email['district'] = $vendor_requested[0]['district'];
                        $email['state'] = $vendor_requested[0]['state'];
                        $email['pincode'] = $vendor_requested[0]['pincode'];
                        $email['primary_contact_phone_1'] = $vendor_requested[0]['primary_contact_phone_1'];
                        $email['owner_phone_1'] = $vendor_requested[0]['owner_phone_1'];
                        $subject = "Brackets Requested";

                        $emailBody = vsprintf($template[0], $email);

                        $this->notify->sendEmail($template[2], $to , $template[3], '', $subject , $emailBody, "");
                   }
                    
                          //Logging Email Send to order sent to vendor
                    log_message('info', __FUNCTION__ . ' Email has been sent to order_sent_to vendor '.print_r($vendor_requested_to['company_name']));
                }

                //Setting success session data 
                $this->session->set_userdata('brackets_success', 'Brackets Added Successfully');
                
                redirect(base_url() . 'employee/inventory/get_bracket_add_form');
            } else {
                //Logging Error
                log_message('info', __FUNCTION__ . ' Err in adding Brackets ' . print_r($data_post, TRUE));
                $this->session->set_userdata('brackets_error', 'Error in addding Brackets');
                $this->get_bracket_add_form();
            }
            
        } else {
            //Return if validation error occurs
            $this->get_bracket_add_form();
        }
    }
    
    /**
     * @Desc: This function is used to show brackets details list
     * @params: void
     * @return: void
     * 
     */
    function show_brackets_list(){
        $sf_list = "";
        //Getting ID of logged in user
        $id = $this->session->userdata('id');
            //Getting employee relation if present
            $sf_list = $this->vendor_model->get_employee_relation($id);
            if (!empty($sf_list)) {
                $sf_list = $sf_list[0]['service_centres_id'];
            }
        $data['brackets'] = $this->inventory_model->get_brackets($sf_list);
        //Getting name for order received from  to vendor
        foreach($data['brackets'] as $key=>$value){
            $data['order_received_from'][$key] = $this->vendor_model->getVendorContact($value['order_received_from'])[0];
        
            // Getting name for order given to vendor
            
            $data['order_given_to'][$key] = $this->vendor_model->getVendorContact($value['order_given_to'])[0]['name'];
        }
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view("employee/show_brackets_list", $data);
    }
    
    /**
     * @Desc: This function is used to update shipment
     * @params: Int order id
     * @return : view
     */
    function get_update_shipment_form($order_id){
        $data['brackets'] = $this->inventory_model->get_brackets_by_id($order_id);
        $data['shipped_flag'] = TRUE;
        $data['order_id'] = $order_id;
        $data['order_given_to'] = $this->vendor_model->getVendorContact($data['brackets'][0]['order_given_to'])[0]['name'];
        $data['order_received_from'] = $this->vendor_model->getVendorContact($data['brackets'][0]['order_received_from'])[0]['name'];
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view("employee/update_brackets", $data);
    }
    
    /**
     * @Desc: This function is used to process update shipment form
     * @params: Array
     * @return: void
     */
    function process_update_shipment_form(){
        //Saving Uploading file.
        if(!empty($_FILES)){
            $tmpFile = $_FILES['shipment_receipt']['tmp_name'];
            $fileName = $_FILES['shipment_receipt']['name'];
            move_uploaded_file($tmpFile, "/tmp/$fileName");
            $data['shipment_receipt'] = $fileName;
        }
        $order_id = $this->input->post('order_id');
        $order_received_from = $this->input->post('order_received_from');
        $data['19_24_shipped'] = $this->input->post('19_24_shipped');
        $data['26_32_shipped'] = $this->input->post('26_32_shipped');
        $data['36_42_shipped'] = $this->input->post('36_42_shipped');
        $data['total_shipped'] = $this->input->post('total_shipped');
        $data['shipment_date'] = !empty($this->input->post('shipment_date'))?$this->input->post('shipment_date'):date('Y-m-d H:i:s');
        $data['is_shipped'] = 1;
        
        
        $attachment = "";
        if(!empty($fileName)){
            $data['shipment_receipt'] = $fileName;
             $attachment = "/tmp/$fileName";
        }

        //Updating value in Brackets
        $update_brackets = $this->inventory_model->update_brackets($data, array('order_id' => $order_id));
        if($update_brackets){
            //Loggin success
            log_message('info',__FUNCTION__.' Brackets Shipped has been updated '. print_r($data, TRUE));
            
            //Adding value in Booking State Change
                $this->notify->insert_state_change($order_id, _247AROUND_BRACKETS_SHIPPED, _247AROUND_BRACKETS_PENDING, "Brackets Shipped", $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);
            //Logging Success
            log_message('info', __FUNCTION__ . ' Brackets Pending - Shipped state have been added in Booking State Change ');
                
            // Sending mail to order_received_from vendor
            $order_received_from_email = $this->vendor_model->getVendorContact($order_received_from);
            $vendor_poc_mail = $order_received_from_email[0]['primary_contact_email'];
            $vendor_owner_mail = $order_received_from_email[0]['owner_email'];
            $to = $vendor_poc_mail.','.$vendor_owner_mail;
            
             // Sending brackets Shipped Mail to order received from vendor
                   $email = array();
                   //Getting template from Database
                   $template = $this->booking_model->get_booking_email_template("brackets_shipment_mail");
                   
                   if(!empty($template)){
                        $email['company_name'] = $order_received_from_email[0]['company_name'];
                        $subject = "Brackets Shipped";
                        $emailBody = vsprintf($template[0], $email);
                        
                        $this->notify->sendEmail($template[2], $to , $template[3], '', $subject , $emailBody, $attachment);
                   }
            
            //Loggin send mail success
            log_message('info',__FUNCTION__.' Shipped mail has been sent to order_received_from vendor '. $emailBody);
            
            //Setting success session data 
            $this->session->set_userdata('brackets_update_success', 'Brackets Shipped updated Successfully');
            
            redirect(base_url() . 'employee/inventory/show_brackets_list');
        }else{
            //Loggin error
            log_message('info',__FUNCTION__.' Brackets Shipped updated Error '. print_r($data, TRUE));
            
            //Setting error session data 
            $this->session->set_userdata('brackets_update_error', 'No changes made to be updated.');
            $this->get_update_shipment_form($order_id);
        }
    }
    
    /**
     * @Desc: This function is used to show update receiving form 
     * @parmas: Int order id
     * @return: Array 
     */
    function get_update_receiving_form($order_id){
        $data['brackets'] = $this->inventory_model->get_brackets_by_id($order_id);
        $data['receiving_flag'] = TRUE;
        $data['order_id'] = $order_id;
        $data['order_given_to'] = $this->vendor_model->getVendorContact($data['brackets'][0]['order_given_to'])[0]['name'];
        $data['order_received_from'] = $this->vendor_model->getVendorContact($data['brackets'][0]['order_received_from'])[0]['name'];
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view("employee/update_brackets", $data);
    }
    
    /**
     * @Desc: This function is used to update receiving of brackets
     * @params: Array, Int
     * @return : view
     */
    function process_update_receiving_form(){
        $order_id = $this->input->post('order_id');
        $order_received_from = $this->input->post('order_received_from');
        $order_given_to = $this->input->post('order_given_to');
        $data['19_24_received'] = $this->input->post('19_24_received');
        $data['26_32_received'] = $this->input->post('26_32_received');
        $data['36_42_received'] = $this->input->post('36_42_received');
        $data['total_received'] = $this->input->post('total_received');
        $data['received_date'] = date('Y-m-d H:i:s');
        $data['is_received'] = 1;
        
        //Updating value in Brackets
        $update_brackets = $this->inventory_model->update_brackets($data, array('order_id' => $order_id));
        if(!empty($update_brackets)){
            //Loggin success
            log_message('info',__FUNCTION__.' Brackets Received has been updated '. print_r($data, TRUE));
            
            //Adding value in Booking State Change
                $this->notify->insert_state_change($order_id, _247AROUND_BRACKETS_RECEIVED, _247AROUND_BRACKETS_SHIPPED, "", $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);
            //Logging Success
            log_message('info', __FUNCTION__ . ' Brackets Shipped - Received state have been added in Booking State Change ');
            
            //Sending mail to both vendor
            
            //1. Sending to Order received from vendor
            $order_received_from_email = $this->vendor_model->getVendorContact($order_received_from);
            $order_given_to_email = $this->vendor_model->getVendorContact($order_given_to);
            
            $vendor_poc_mail = $order_received_from_email[0]['primary_contact_email'];
            $vendor_owner_mail = $order_received_from_email[0]['owner_email'];
            $order_received_from_email_to = $vendor_poc_mail.','.$vendor_owner_mail;
            
            $vendor_poc_mail = $order_given_to_email[0]['primary_contact_email'];
            $vendor_owner_mail = $order_given_to_email[0]['owner_email'];
            $order_given_to_email_to = $vendor_poc_mail.','.$vendor_owner_mail;
            
            //1. Sending brackets Received Mail to order received from vendor
                   $email = array();
                   //Getting template from Database
                   $template = $this->booking_model->get_booking_email_template("brackets_received_mail_vendor_order_requested_from");
                   
                   if(!empty($template)){
                        $email['company_name'] = $order_received_from_email[0]['company_name'];
                        $subject = "Brackets Received";
                        $emailBody = vsprintf($template[0], $email);
                        $this->notify->sendEmail($template[2], $order_received_from_email_to , $template[3], '', $subject , $emailBody, '');
                   }
            
            //Loggin send mail success
            log_message('info',__FUNCTION__.' Received mail has been sent to order_received_from vendor '. $emailBody);
            
            //2. Sending mail to order_given_to vendor
            $body_order_given_to = $order_given_to_email[0]['company_name']." brackets has been delivered successfully to the following vendor ".$order_received_from_email[0]['company_name']."<br><br>"
                    . "Please contact us in case of any query.<br><br> "
                    . "247Around Team";
            
            $email = array();
                   //Getting template from Database
                   $template = $this->booking_model->get_booking_email_template("brackets_received_mail_vendor_order_given_to");
                   
                   if(!empty($template)){
                        $email['order_given_to'] = $order_given_to_email[0]['company_name'];
                        $email['order_recieved_from'] = $order_received_from_email[0]['company_name'];
                        $subject = "Brackets Received";
                        $emailBody = vsprintf($template[0], $email);
                        
                        $this->notify->sendEmail($template[2], $order_given_to_email_to , $template[3], '', $subject , $emailBody, '');
                   }
            
            //Loggin send mail success
            log_message('info',__FUNCTION__.' Received mail has been sent to order_given_to vendor '. $emailBody);
            
            //Setting success session data 
            $this->session->set_userdata('brackets_update_success', 'Brackets Received updated Successfully');
            
        }else{
            //Loggin error
            log_message('info',__FUNCTION__.' Brackets Received updated Error '. print_r($data, TRUE));
            
            //Setting error session data 
            $this->session->set_userdata('brackets_update_error', 'No changes made to be updated.');
        }
        
        //Creating Received Data Array for Inventory Database
        $received_inventory_data[] = array(
            'vendor_id'  => $this->input->post('order_given_to'),
            'order_booking_id' => $order_id,
            '19_24_current_count' => $this->input->post('19_24_received'),
            '26_32_current_count' => $this->input->post('26_32_received'),
            '36_42_current_count' => $this->input->post('36_42_received'),
            'increment/decrement' => 1,
            'remarks' => 'Order ID'
        );
        
        //Checking if OrderID data already exists in Inventory Database, then update the same else add a new row
        
        $received_check_order = $this->inventory_model->check_data($this->input->post('order_given_to'));
        if($received_check_order){
        
        //Getting last row of return array from Database    
            $last_updated_array = end($received_check_order);    
        //Updating data in Inventory Database for particular Order ID and remarks as  _247AROUND_BRACKETS_RECEIVED   
            $updated_received_data[] = array(
            'vendor_id'  => $this->input->post('order_given_to'),
            'order_booking_id' => $order_id,
            '19_24_current_count' => $this->input->post('19_24_received') + $last_updated_array['19_24_current_count'],
            '26_32_current_count' => $this->input->post('26_32_received') + $last_updated_array['26_32_current_count'],
            '36_42_current_count' => $this->input->post('36_42_received') + $last_updated_array['36_42_current_count'],
            'increment/decrement' => 1,
            'remarks' => 'Order ID'
            );
            
            $update_shipped_data_flag = $this->inventory_model->insert_inventory($updated_received_data);
            if($update_shipped_data_flag){
                //Logging Success
                log_message('info',__FUNCTION__.' Received Data has been Added in Inventory '.print_r($updated_received_data,TRUE));
            }else{
                //Logging Error
                log_message('info',__FUNCTION__.' Error in Updating Received data in Inventory '.print_r($updated_received_data,TRUE));
            }
            
        }else{
        
        //Inserting Data in Inventory Database
        $received_inventory_flag = $this->inventory_model->insert_inventory($received_inventory_data);
            if($received_inventory_flag){
                //Logging Success
                log_message('info',__FUNCTION__.' Received Data has been entered in Inventory '.print_r($received_inventory_data,TRUE));
            }else{
                //Logging Error
                log_message('info',__FUNCTION__.' Error in addding Received data in Inventory '.print_r($received_inventory_data,TRUE));
            }
        }
        
        redirect(base_url() . 'employee/inventory/show_brackets_list');
    }
    
    /**
     * @desc: This is used to show vendor inventory
     * @parmas: void
     * @return: void
     * 
     */
    function get_vendor_inventory_list_form(){
        $sf_list = "";
        //Getting ID of logged in user
        $id = $this->session->userdata('id');
            //Getting employee relation if present
            $sf_list = $this->vendor_model->get_employee_relation($id);
            if (!empty($sf_list)) {
                $sf_list = $sf_list[0]['service_centres_id'];
            }
        $data['distinct_vendor'] = $this->inventory_model->get_distict_vendor_from_inventory($sf_list);
        foreach($data['distinct_vendor'] as $value){
            $data['vendor_inventory'][] = $this->inventory_model->get_vendor_inventory_details($value['vendor_id']);
        }
        //Getting latest updated values of vendor
        foreach($data['vendor_inventory'] as $key=>$value){
            $data['final_array'][] = end($value);
        }
        
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view("employee/show_vendor_inventory_list", $data);
    }
    /**
     * @Desc: This function is used to show brackets order history for order_id
     * @params: Int order_id
     * @return :View
     * 
     */
    function show_brackets_order_history($order_id){
        $data['data'] = $this->inventory_model->get_brackets_by_order_id($order_id);
        $data['order_id'] = $order_id;
        $data['brackets'] = $this->inventory_model->get_brackets_by_id($order_id);
        $data['invoice_id'] = $this->inventory_model->get_brackets_invoice_by_order_id($order_id);
        $data['order_received_from'] = $this->vendor_model->getVendorContact($data['brackets'][0]['order_received_from'])[0]['name'];
        $data['order_given_to'] = $this->vendor_model->getVendorContact($data['brackets'][0]['order_given_to'])[0]['name'];
        
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view("employee/show_brackets_order_history", $data);
    }
    
    /**
     * @Desc:This function is used to update Brackets Requested
     * @params: INT order id
     * @return: view
     */
    function get_update_requested_form($order_id){
        $data['brackets'] = $this->inventory_model->get_brackets_by_id($order_id);
        $data['requested_flag'] = TRUE;
        $data['order_id'] = $order_id;
        $data['order_given_to'] = $this->vendor_model->getVendorContact($data['brackets'][0]['order_given_to'])[0]['name'];
        $data['order_received_from'] = $this->vendor_model->getVendorContact($data['brackets'][0]['order_received_from'])[0]['name'];
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view("employee/update_brackets", $data);
    }
    
    /**
     * @Desc: This function is used to process update requested form
     * @params: Array , int
     * @return : view
     * 
     */
    function process_update_requested_form(){
       
        $order_id = $this->input->post('order_id');
        $order_received_from = $this->input->post('order_received_from');
        $order_given_to = $this->input->post('order_given_to');
        $data['19_24_requested'] = $this->input->post('19_24_requested');
        $data['26_32_requested'] = $this->input->post('26_32_requested');
        $data['36_42_requested'] = $this->input->post('36_42_requested');
        $data['total_requested'] = $this->input->post('total_requested');
        
        //Updating value in Brackets
        $update_brackets = $this->inventory_model->update_brackets($data, array('order_id' => $order_id));
        if($update_brackets){
            //Loggin success
            log_message('info',__FUNCTION__.' Brackets Requested has been updated '. print_r($data, TRUE));
            
            //Adding value in Booking State Change
                $this->notify->insert_state_change($order_id, _247AROUND_BRACKETS_PENDING, _247AROUND_BRACKETS_PENDING, "Brackets Shipped", $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);
            //Logging Success
            log_message('info', __FUNCTION__ . ' Brackets Pending - Pending state have been added in Booking State Change ');
                
            // Sending mail to order_received_from vendor
            $order_received_from_email = $this->vendor_model->getVendorContact($order_received_from);
            $vendor_poc_mail = $order_received_from_email[0]['primary_contact_email'];
            $vendor_owner_mail = $order_received_from_email[0]['owner_email'];
            $to = $vendor_poc_mail.','.$vendor_owner_mail;
            
            // Sending updated brackets confirmation details mail to Vendor using Template
                   $email = array();
                   //Getting template from Database
                   $template = $this->booking_model->get_booking_email_template("brackets_order_received_from_vendor");
                   if(!empty($template)){
                        $email['company_name'] = $order_received_from_email[0]['company_name'];
                        $email['19_24_requested'] = $data['19_24_requested'];
                        $email['26_32_requested'] = $data['26_32_requested'];
                        $email['36_42_requested'] = $data['36_42_requested'];
                        $email['total_requested'] = $data['total_requested'];
                        $subject = "Updated Brackets Requested";
                        $emailBody = vsprintf($template[0], $email);
                        $this->notify->sendEmail($template[2], $to , $template[3], '', $subject , $emailBody, "");
                   }
            
            //Loggin send mail success
            log_message('info',__FUNCTION__.' Changed Requested mail has been sent to order_received_from vendor '. $to);
            //Sending Mail to order given to
                    $vendor_requested_to = $this->vendor_model->getVendorContact($order_given_to)[0];
                    $vendor_poc_mail = $vendor_requested_to['primary_contact_email'];
                    $vendor_owner_mail = $vendor_requested_to['owner_email'];
                    $to = $vendor_poc_mail.','.$vendor_owner_mail;

                    // Sending Login details mail to Vendor using Template
                   $email = array();
                   //Getting template from Database
                   $template = $this->booking_model->get_booking_email_template("brackets_requested_from_vendor");
                   
                   if(!empty($template)){
                        $email['19_24_requested'] = $data['19_24_requested'];
                        $email['26_32_requested'] = $data['26_32_requested'];
                        $email['36_42_requested'] = $data['36_42_requested'];
                        $email['total_requested'] = $data['total_requested'];
                        $email['owner_name'] = $order_received_from_email[0]['owner_name'];
                        $email['company_name'] = $order_received_from_email[0]['company_name'];
                        $email['address'] = $order_received_from_email[0]['address'];
                        $email['district'] = $order_received_from_email[0]['district'];
                        $email['state'] = $order_received_from_email[0]['state'];
                        $email['pincode'] = $order_received_from_email[0]['pincode'];
                        $email['primary_contact_phone_1'] = $order_received_from_email[0]['primary_contact_phone_1'];
                        $email['owner_phone_1'] = $order_received_from_email[0]['owner_phone_1'];
                        $subject = "Updated Brackets Requested";

                        $emailBody = vsprintf($template[0], $email);
                        $this->notify->sendEmail($template[2], $to , $template[3], '', $subject , $emailBody, "");
                        //Loggin send mail success
                        log_message('info',__FUNCTION__.' Changed Requested mail has been sent to order_given_to vendor '. $to);
                   }
            
            //Setting success session data 
            $this->session->set_userdata('brackets_update_success', 'Brackets Requested updated Successfully');
            
            redirect(base_url() . 'employee/inventory/show_brackets_list');
        }else{
            //Loggin error
            log_message('info',__FUNCTION__.' Brackets Shipped updated Error '. print_r($data, TRUE));
            
            //Setting error session data 
            $this->session->set_userdata('brackets_update_error', 'No changes made to be updated.');
            $this->get_update_requested_form($order_id);
        }
    }
    
    /**
     * 
     * @Desc: This function is used to cancel brackets order
     * @parmas: order id
     * @return: view
     */
    function cancel_brackets_requested($order_id){
        $data['active'] = 0;
        $cancel = $this->inventory_model->update_brackets($data, array('order_id' => $order_id));
        if($cancel){
            //Loggging
            log_message('info',__FUNCTION__.' Brackets Requested has been cancelled '. print_r($cancel));
            //Getiting brackets details
            $brackets_details = $this->inventory_model->get_brackets_by_id($order_id);
            
            // Sending mail to order_received_from vendor
            $order_received_from_email = $this->vendor_model->getVendorContact($brackets_details[0]['order_received_from']);
          
            $vendor_poc_mail = $order_received_from_email[0]['primary_contact_email'];
            $vendor_owner_mail = $order_received_from_email[0]['owner_email'];
            $to = $vendor_poc_mail.','.$vendor_owner_mail;
            
            // Sending updated brackets confirmation details mail to Vendor using Template
                   $email = array();
                   //Getting template from Database
                   $template = $this->booking_model->get_booking_email_template("cancel_brackets_order_received_from_vendor");
                   if(!empty($template)){
                        $email['company_name'] = $order_received_from_email[0]['company_name'];
                        $email['19_24_requested'] = $brackets_details[0]['19_24_requested'];
                        $email['26_32_requested'] = $brackets_details[0]['26_32_requested'];
                        $email['36_42_requested'] = $brackets_details[0]['36_42_requested'];
                        $email['total_requested'] = $brackets_details[0]['total_requested'];
                        $subject = "Brackets Request Cancelled";
                        $emailBody = vsprintf($template[0], $email);
                        $this->notify->sendEmail($template[2], $to , $template[3], '', $subject , $emailBody, "");
                        //Loggin send mail success
                        log_message('info',__FUNCTION__.' Cancelled Brackets mail has been sent to order_received_from vendor '. $to);
                   }
                   
                   //Sending Mail to order given to
                    $vendor_requested_to = $this->vendor_model->getVendorContact($brackets_details[0]['order_given_to']);
                    $vendor_poc_mail = $vendor_requested_to[0]['primary_contact_email'];
                    $vendor_owner_mail = $vendor_requested_to[0]['owner_email'];
                    $to = $vendor_poc_mail.','.$vendor_owner_mail;

                    // Sending Login details mail to Vendor using Template
                   $email = array();
                   //Getting template from Database
                   $template = $this->booking_model->get_booking_email_template("cancel_brackets_requested_from_vendor");
                   
                   if(!empty($template)){
                        $email['19_24_requested'] = $brackets_details[0]['19_24_requested'];
                        $email['26_32_requested'] = $brackets_details[0]['26_32_requested'];
                        $email['36_42_requested'] = $brackets_details[0]['36_42_requested'];
                        $email['total_requested'] = $brackets_details[0]['total_requested'];
                        $email['owner_name'] = $order_received_from_email[0]['owner_name'];
                        $email['company_name'] = $order_received_from_email[0]['company_name'];
                        $email['address'] = $order_received_from_email[0]['address'];
                        $email['district'] = $order_received_from_email[0]['district'];
                        $email['state'] = $order_received_from_email[0]['state'];
                        $email['pincode'] = $order_received_from_email[0]['pincode'];
                        $email['primary_contact_phone_1'] = $order_received_from_email[0]['primary_contact_phone_1'];
                        $email['owner_phone_1'] = $order_received_from_email[0]['owner_phone_1'];
                        $subject = "Brackets Request Cancelled";

                        $emailBody = vsprintf($template[0], $email);
                        $this->notify->sendEmail($template[2], $to , $template[3], '', $subject , $emailBody, "");
                        //Loggin send mail success
                        log_message('info',__FUNCTION__.'  Cancelled Brackets mail has been sent to order_given_to vendor '. $to);
                   }
                   
            //Setting success session data 
            $this->session->set_userdata('brackets_update_success', 'Brackets Requested has been Cancelled.');
        }else{
            //Setting success session data 
            $this->session->set_userdata('brackets_cancelled_error', 'Error in cancellation of Brackets Requested.');
        }
        
        
        redirect(base_url() . 'employee/inventory/show_brackets_list');
        
    }

}