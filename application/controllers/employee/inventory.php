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
        $this->load->model('partner_model');
        $this->load->model('inventory_model');
        $this->load->model('booking_model');
        $this->load->model('employee_model');
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->library('notify');
        $this->load->library('S3');
        $this->load->library("pagination");
	

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
               
                $service_center = $this->vendor_model->getVendorDetails();
                $sf_array = explode(',',$sf_list);
                foreach($service_center as $value){
                    if(array_search($value['id'],$sf_array)){
                        $data['vendor'][] = $value;
                    }
                }
            }else{
                //Getting all values
                $data['vendor'] = $this->vendor_model->getVendorDetails();
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
            foreach ($data['choice'] as $key=>$value) {

                // New Pattern for Order ID for brackets
                $order_id_array = $this->inventory_model->get_latest_order_id();
                if (empty($order_id_array[0]['order_id'])) {
                    $order_id = _247_AROUND_DEFAULT_BRACKETS_ORDER_ID;
                } else {
                    $order_id = $order_id_array[0]['order_id'] + 1;
                }

                //Making array for Brackets Database
                $data_post = array(
                    'order_id' => $order_id,
                    'order_received_from' => $data['order_received_from'][$key],
                    'order_given_to' => $data['order_given_to'][$key],
//                    '19_24_requested' => $data['_19_24'][$key],
                    '26_32_requested' => $data['_26_32'][$key],
                    '36_42_requested' => $data['_36_42'][$key],
//                    '43_requested' => $data['_43'][$key],
                    'order_date' => date('Y-m-d h:i:s'),
//                    'total_requested' => ($data['_19_24'][$key] + $data['_26_32'][$key] + $data['_36_42'][$key] + $data['_43'][$key]),
                    'total_requested' => ($data['_26_32'][$key] + $data['_36_42'][$key]),
                );

                //Inserting data in Brackets Database
                $check_flag = $this->inventory_model->insert_brackets($data_post);
                if ($check_flag) {
                    //Logging Success
                    log_message('info', __FUNCTION__ . ' Brackets have been added successfully ' . print_r($data_post, TRUE));

                    //Logging Success
                    log_message('info', __FUNCTION__ . ' Brackets Requested- Pending state have been added in Booking State Change ');

                    //Adding value in Booking State Change
                    $this->notify->insert_state_change($data_post['order_id'], _247AROUND_BRACKETS_PENDING, _247AROUND_BRACKETS_REQUESTED, "Brackets Requested", $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);

                    $vendor_requested = $this->vendor_model->getVendorContact($data_post['order_received_from']);
                    
                    $vendor_poc_mail = $vendor_requested[0]['primary_contact_email'];
                    $vendor_owner_mail = $vendor_requested[0]['owner_email'];
                    $to = $vendor_poc_mail . ',' . $vendor_owner_mail;

                    // Sending brackets confirmation details mail to Vendor using Template
                    $email_order_received_from = array();
                    //Getting template from Database
                    $template = $this->booking_model->get_booking_email_template("brackets_order_received_from_vendor");

                    if (!empty($template)) {
                        $email_order_received_from['order_id'] = $order_id;
//                        $email_order_received_from['19_24_requested'] = $data_post['19_24_requested'];
                        $email_order_received_from['26_32_requested'] = $data_post['26_32_requested'];
                        $email_order_received_from['36_42_requested'] = $data_post['36_42_requested'];
//                        $email_order_received_from['43_requested'] = $data_post['43_requested'];
                        $email_order_received_from['total_requested'] = $data_post['total_requested'];
                        $subject = "Brackets Requested by " . $vendor_requested[0]['company_name'];
                        $emailBody = vsprintf($template[0], $email_order_received_from);
                        $this->notify->sendEmail($template[2], $to, $template[3] . ',' . $this->get_rm_email($data_post['order_received_from']), '', $subject, $emailBody, "");
                    }
                    
                    //Logging Email Send to order received from vendor
                    log_message('info', __FUNCTION__ . ' Email has been sent to order_received_from vendor ' . $vendor_requested[0]['company_name']);

                    //Sending Mail to order given to
                    $vendor_requested_to = $this->vendor_model->getVendorContact($data_post['order_given_to'])[0];
                    $vendor_poc_mail = $vendor_requested_to['primary_contact_email'];
                    $vendor_owner_mail = $vendor_requested_to['owner_email'];
                    $to = $vendor_poc_mail . ',' . $vendor_owner_mail;

                    // Sending Mail to order given to vendor using Template
                    $email = array();
                    //Getting template from Database
                    $template = $this->booking_model->get_booking_email_template("brackets_requested_from_vendor");

                    if (!empty($template)) {
                        $email['order_id'] = $order_id;
//                        $email['19_24_requested'] = $data_post['19_24_requested'];
                        $email['26_32_requested'] = $data_post['26_32_requested'];
                        $email['36_42_requested'] = $data_post['36_42_requested'];
//                        $email['43_requested'] = $data_post['43_requested'];
                        $email['total_requested'] = $data_post['total_requested'];
                        $email['owner_name'] = $vendor_requested[0]['owner_name'];
                        $email['company_name'] = $vendor_requested[0]['company_name'];
                        $email['address'] = $vendor_requested[0]['address'];
                        $email['district'] = $vendor_requested[0]['district'];
                        $email['state'] = $vendor_requested[0]['state'];
                        $email['pincode'] = $vendor_requested[0]['pincode'];
                        $email['primary_contact_phone_1'] = $vendor_requested[0]['primary_contact_phone_1'];
                        $email['owner_phone_1'] = $vendor_requested[0]['owner_phone_1'];
                        $subject = "Brackets Requested by " . $vendor_requested[0]['company_name'];

                        $emailBody = vsprintf($template[0], $email);

                        $this->notify->sendEmail($template[2], $to, $template[3] . ',' . $this->get_rm_email($data_post['order_given_to']), '', $subject, $emailBody, "");
                    }
                    //Logging Email Send to order sent to vendor
                    log_message('info', __FUNCTION__ . ' Email has been sent to order_sent_to vendor ' . $vendor_requested_to['company_name']);
                } else {
                    //Logging Error
                    log_message('info', __FUNCTION__ . ' Err in adding Brackets ' . print_r($data_post, TRUE));
                    $this->session->set_userdata('brackets_error', 'Error in addding Brackets');
                    $this->get_bracket_add_form();
                }
            }
            //Setting success session data 
            $this->session->set_userdata('brackets_success', 'Brackets Added Successfully');

            redirect(base_url() . 'employee/inventory/get_bracket_add_form');
        }else{
            //Setting success session data 
            $this->session->set_userdata('brackets_error', 'Please select Vendor Details');

            redirect(base_url() . 'employee/inventory/get_bracket_add_form');
        }
    }
    
    /**
     * @Desc: This function is used to show brackets details list
     * @params: void
     * @return: void
     * 
     */
    function show_brackets_list($page = 0, $offset = '0'){
        $sf_list = "";
        //Getting ID of logged in user
        $id = $this->session->userdata('id');
            //Getting employee relation if present
            $sf_list_array = $this->vendor_model->get_employee_relation($id);
            if (!empty($sf_list_array)) {
                $sf_list = $sf_list_array[0]['service_centres_id'];
            }
        
        if ($page == 0) {
	    $page = 50;
	}
	// $offset = ($this->uri->segment(5) != '' ? $this->uri->segment(5) : 0);
   
	$config['base_url'] = base_url() . 'employee/inventory/show_brackets_list/'.$page;
	$config['total_rows'] = $this->inventory_model->get_total_brackets_count($sf_list);
	
	if($offset != "All"){
		$config['per_page'] = $page;
	} else {
		$config['per_page'] = $config['total_rows'];
	}	
	
	$config['uri_segment'] = 5;
	$config['first_link'] = 'First';
	$config['last_link'] = 'Last';

	$this->pagination->initialize($config);
	$data['links'] = $this->pagination->create_links();
        $data['Count'] = $config['total_rows'];        
        $data['brackets'] = $this->inventory_model->get_brackets($config['per_page'], $offset,$sf_list);
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
        
        if(($_FILES['shipment_receipt']['error'] != 4) && !empty($_FILES['shipment_receipt']['tmp_name'])){
            $tmpFile = $_FILES['shipment_receipt']['tmp_name'];
            //Assigning File Name for uploaded shipment receipt
            $fileName = "Shipment-Receipt-".$this->input->post('order_id').'.'.explode('.',$_FILES['shipment_receipt']['name'])[1];
            move_uploaded_file($tmpFile, TMP_FOLDER.$fileName);
            
             //Uploading images to S3 
            $bucket = BITBUCKET_DIRECTORY;
            $directory = "misc-images/" . $fileName;
            $this->s3->putObjectFile(TMP_FOLDER.$fileName, $bucket, $directory, S3::ACL_PUBLIC_READ);
            
            $data['shipment_receipt'] = $fileName;
        }
        $order_id = $this->input->post('order_id');
        $order_received_from = $this->input->post('order_received_from');
//        $data['19_24_shipped'] = $this->input->post('19_24_shipped');
        $data['19_24_shipped'] = '0';
        $data['26_32_shipped'] = $this->input->post('26_32_shipped');
        $data['36_42_shipped'] = $this->input->post('36_42_shipped');
//        $data['43_shipped'] = $this->input->post('43_shipped');
        $data['43_shipped'] = '0';
        $data['total_shipped'] = $this->input->post('total_shipped');
        $data['shipment_date'] = !empty($this->input->post('shipment_date'))?$this->input->post('shipment_date'):date('Y-m-d H:i:s');
        $data['is_shipped'] = 1;
        
        
        $attachment = "";
        if(!empty($fileName)){
            $data['shipment_receipt'] = $fileName;
             $attachment = TMP_FOLDER.$fileName;
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
                        $email['order_id'] = $order_id;
                        $subject = "Brackets Shipped by ".$order_received_from_email[0]['company_name'];
                        $emailBody = vsprintf($template[0], $email);
                        
                        $this->notify->sendEmail($template[2], $to , $template[3].','.$this->get_rm_email($order_received_from), '', $subject , $emailBody, $attachment);
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
//        $data['19_24_received'] = $this->input->post('19_24_received');
        $data['19_24_received'] = '0';
        $data['26_32_received'] = $this->input->post('26_32_received');
        $data['36_42_received'] = $this->input->post('36_42_received');
//        $data['43_received'] = $this->input->post('43_received');
        $data['43_received'] = '0';
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
                        $email['order_id'] = $order_id;
                        $subject = "Brackets Received by ".$order_received_from_email[0]['company_name'];
                        $emailBody = vsprintf($template[0], $email);
                        $this->notify->sendEmail($template[2], $order_received_from_email_to , $template[3].','.$this->get_rm_email($order_received_from), '', $subject , $emailBody, '');
                   }
            
            //Loggin send mail success
            log_message('info',__FUNCTION__.' Received mail has been sent to order_received_from vendor '. $emailBody);
            
            //2. Sending mail to order_given_to vendor
            
            $email = array();
                   //Getting template from Database
                   $template = $this->booking_model->get_booking_email_template("brackets_received_mail_vendor_order_given_to");
                   
                   if(!empty($template)){
                        $email['order_recieved_from'] = $order_received_from_email[0]['company_name'];
                        $email['order_id'] = $order_id;
                        $subject = "Brackets Received by ".$order_received_from_email[0]['company_name'];
                        $emailBody = vsprintf($template[0], $email);
                        
                        $this->notify->sendEmail($template[2], $order_given_to_email_to , $template[3].','.$this->get_rm_email($order_given_to), '', $subject , $emailBody, '');
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
//            '19_24_current_count' => $this->input->post('19_24_received'),
            '26_32_current_count' => $this->input->post('26_32_received'),
            '36_42_current_count' => $this->input->post('36_42_received'),
//            '43_current_count' => $this->input->post('43_received'),
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
//            '19_24_current_count' => $this->input->post('19_24_received') + $last_updated_array['19_24_current_count'],
//            '26_32_current_count' => $this->input->post('26_32_received') + $last_updated_array['26_32_current_count'],
//            '36_42_current_count' => $this->input->post('36_42_received') + $last_updated_array['36_42_current_count'],
//            '43_current_count' => $this->input->post('43_received') + $last_updated_array['43_current_count'],
                '19_24_current_count' => '0',
                '26_32_current_count' =>$this->input->post('26_32_received') + $last_updated_array['26_32_current_count']+$last_updated_array['19_24_current_count'],
                '36_42_current_count' => $this->input->post('36_42_received') + $last_updated_array['36_42_current_count']+$last_updated_array['43_current_count'],
                '43_current_count' => '0',
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
            $sf_list_array = $this->vendor_model->get_employee_relation($id);
            if (!empty($sf_list_array)) {
                $sf_list = $sf_list_array[0]['service_centres_id'];
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
//        $data['19_24_requested'] = $this->input->post('19_24_requested');
        $data['26_32_requested'] = $this->input->post('26_32_requested');
        $data['36_42_requested'] = $this->input->post('36_42_requested');
//        $data['43_requested'] = $this->input->post('43_requested');
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
                        $email['order_id'] = $order_id;
//                        $email['19_24_requested'] = $data['19_24_requested'];
                        $email['26_32_requested'] = $data['26_32_requested'];
                        $email['36_42_requested'] = $data['36_42_requested'];
//                        $email['43_requested'] = $data['43_requested'];
                        $email['total_requested'] = $data['total_requested'];
                        $subject = "Updated Brackets Requested by ".$order_received_from_email[0]['company_name'];
                        $emailBody = vsprintf($template[0], $email);
                        $this->notify->sendEmail($template[2], $to , $template[3].','.$this->get_rm_email($order_received_from), '', $subject , $emailBody, "");
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
                        $email['order_id'] = $order_id;
//                        $email['19_24_requested'] = $data['19_24_requested'];
                        $email['26_32_requested'] = $data['26_32_requested'];
                        $email['36_42_requested'] = $data['36_42_requested'];
//                        $email['43_requested'] = $data['43_requested'];
                        $email['total_requested'] = $data['total_requested'];
                        $email['owner_name'] = $order_received_from_email[0]['owner_name'];
                        $email['company_name'] = $order_received_from_email[0]['company_name'];
                        $email['address'] = $order_received_from_email[0]['address'];
                        $email['district'] = $order_received_from_email[0]['district'];
                        $email['state'] = $order_received_from_email[0]['state'];
                        $email['pincode'] = $order_received_from_email[0]['pincode'];
                        $email['primary_contact_phone_1'] = $order_received_from_email[0]['primary_contact_phone_1'];
                        $email['owner_phone_1'] = $order_received_from_email[0]['owner_phone_1'];
                        $subject = "Updated Brackets Requested by ".$order_received_from_email[0]['company_name'];

                        $emailBody = vsprintf($template[0], $email);
                        $this->notify->sendEmail($template[2], $to , $template[3].','.$this->get_rm_email($order_given_to), '', $subject , $emailBody, "");
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
     * @parmas: POST Array
     * @return: view
     */
    function cancel_brackets_requested(){
        $order_id = $this->input->post('order_id');
        $data['active'] = 0;
        $data['cancellation_reason'] = $this->input->post('cancellation_reason');
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
                        $email['order_id'] = $order_id;
                        $email['reason'] = $brackets_details[0]['cancellation_reason'];
//                        $email['19_24_requested'] = $brackets_details[0]['19_24_requested'];
                        $email['26_32_requested'] = $brackets_details[0]['26_32_requested'];
                        $email['36_42_requested'] = $brackets_details[0]['36_42_requested'];
//                        $email['43_requested'] = $brackets_details[0]['43_requested'];
                        $email['total_requested'] = $brackets_details[0]['total_requested'];
                        $subject = "Brackets Request Cancelled";
                        $emailBody = vsprintf($template[0], $email);
                        $this->notify->sendEmail($template[2], $to , $template[3].','.$this->get_rm_email($brackets_details[0]['order_received_from']), '', $subject , $emailBody, "");
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
                        $email['order_id'] = $order_id;
                        $email['reason'] = $brackets_details[0]['cancellation_reason'];
//                        $email['19_24_requested'] = $brackets_details[0]['19_24_requested'];
                        $email['26_32_requested'] = $brackets_details[0]['26_32_requested'];
                        $email['36_42_requested'] = $brackets_details[0]['36_42_requested'];
//                        $email['43_requested'] = $brackets_details[0]['43_requested'];
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
                        $this->notify->sendEmail($template[2], $to , $template[3].','.$this->get_rm_email($brackets_details[0]['order_given_to']), '', $subject , $emailBody, "");
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
    
    /**
     * @Desc: This function is used to get RM email (:POC) details for the corresponding vendor 
     * @params: vendor 
     * @return : string
     */
    private function get_rm_email($vendor_id) {
        $employee_rm_relation = $this->vendor_model->get_rm_sf_relation_by_sf_id($vendor_id);
        $rm_id = $employee_rm_relation[0]['agent_id'];
        $rm_details = $this->employee_model->getemployeefromid($rm_id);
        $rm_poc_email = $rm_details[0]['official_email'];
        return $rm_poc_email;
    }
    
    /**
     * @desc: This is used to display all spare parts booking
     */
    function get_spare_parts(){
        log_message('info', __FUNCTION__. "Entering... ");
        $this->checkUserSession();
//	$offset = ($this->uri->segment(4) != '' ? $this->uri->segment(4) : 0);
//       
//	$config['base_url'] = base_url() . 'employee/inventory/get_spare_parts/';
//        $total_rows =  $this->booking_model->get_spare_parts_booking(0, "All");
//        
//	$config['total_rows'] = $total_rows[0]['count'];
//
//	$config['per_page'] = 50;
//	
//	$config['uri_segment'] = 4;
//	$config['first_link'] = 'First';
//	$config['last_link'] = 'Last';
//
//	$this->pagination->initialize($config);
//	$data['links'] = $this->pagination->create_links();
//	$data['spare_parts'] = $this->booking_model->get_spare_parts_booking($config['per_page'], $offset);
//        echo "<pre>";
//        print_r($data);exit();
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/get_spare_parts');
    }
    /**
     * @desc: load to Spare parts booking by Admin Panel
     * @param type $booking_id
     */
    function update_spare_parts($id){
        log_message('info', __FUNCTION__. "Entering... And Booking_ID: " . $id);
        $this->checkUserSession();
        $where = "spare_parts_details.id = '".$id."' "
                . " AND booking_details.current_status IN ('Pending', 'Rescheduled', 'Completed', 'Cancelled') ";
        $data['bookinghistory'] = $this->partner_model->get_spare_parts_booking($where);
        
        if(!empty($data['bookinghistory'][0])){
            
            $this->load->view('employee/header/'.$this->session->userdata('user_group'));
            $this->load->view('employee/update_spare_parts', $data);
        } else{
            echo "Booking Not Found. Please Retry Again";
        }
    }
    /**
     * @desc: Process to update Spare parts booking by Admin Panel
     * @param type $booking_id
     */
    function process_update_booking($booking_id, $id){
        log_message('info', __FUNCTION__. "Entering... For Booking_id:" . $booking_id . " And Id: ". $id);
        $this->checkUserSession();
        if(!empty($booking_id) || !empty($id) || $id != 0){
        $data['model_number'] = $this->input->post('model_number');
        $data['serial_number'] = $this->input->post('serial_number');
        $data['parts_requested'] = $this->input->post('parts_name');
        $data['parts_shipped'] = $this->input->post('shipped_parts_name');
        $data['date_of_request'] = $this->input->post('dop');       
        $data['remarks_by_sc'] = $this->input->post('reason_text');
        $data['remarks_by_partner'] = $this->input->post('remarks_by_partner');
        $data['courier_name_by_partner'] = $this->input->post('courier_name');
        $data['awb_by_partner'] = $this->input->post('awb');
        $data['shipped_date'] = $this->input->post('shipment_date');
        $data['status'] = $this->input->post('status');
        
         if(($_FILES['invoice_image']['error'] != 4) && !empty($_FILES['invoice_image']['tmp_name'])){
            $invoice_name = $this->upload_spare_pic($_FILES["invoice_image"], "Invoice");
            if (isset($invoice_name)) {
                $data['invoice_pic'] = $invoice_name;
            }
        }
        
        if(($_FILES['serial_number_pic']['error'] != 4) && !empty($_FILES['serial_number_pic']['tmp_name'])){

            $serial_number_pic = $this->upload_spare_pic($_FILES["serial_number_pic"],"Serial_NO");
            if (isset($serial_number_pic)) {
                $data['serial_number_pic'] = $serial_number_pic;
            }
        }
        
         if(($_FILES['defective_parts_pic']['error'] != 4) && !empty($_FILES['defective_parts_pic']['tmp_name'])){

            $defective_parts_pic = $this->upload_spare_pic($_FILES["defective_parts_pic"],"Defective_Parts");
            if (isset($defective_parts_pic)) {
                $data['defective_parts_pic'] = $defective_parts_pic;
            }
        }
        
        $where = array('id'=> $id);
        $status_spare = $this->service_centers_model->update_spare_parts($where, $data);
        if($status_spare){
            log_message('info', __FUNCTION__. " Spare Parts Booking is updated");
            if($data['status'] == "Spare Parts Requested"){
                log_message('info', __FUNCTION__. " Change Current Status in Service Center Action table");
                $sc_data['current_status'] = "InProcess";
                $sc_data['internal_status'] = $data['status'];
                $sc_data['update_date'] = date("Y-m-d H:i:s");
          
                $this->vendor_model->update_service_center_action($booking_id,$sc_data);
            }
            
            $this->notify->insert_state_change($booking_id, $data['status'], "" , "Spare Parts Updated By ".$this->session->userdata('employee_id') , $this->session->userdata('id'), $this->session->userdata('employee_id'),_247AROUND);
            
        } else {
            log_message('info', __FUNCTION__. " Spare Parts Booking is not updated");
        }
        
        redirect(base_url()."employee/inventory/update_spare_parts/".$booking_id);
        } else {
            echo "Please Provide Booking Id";
        }
    }
    
    /**
     * @esc: This method upload invoice image OR panel image to S3
     * @param _FILE $file
     * @param String $type
     * @return boolean|string
     */
     public function upload_spare_pic($file, $type) {
        log_message('info', __FUNCTION__. " Enterring Service_center ID: ". $this->session->userdata('service_center_id'));
        $this->checkUserSession();
	$allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
	$temp = explode(".", $file['name']);
	$extension = end($temp);
	//$filename = prev($temp);

	if ($file["name"] != null) {
	    if (($file["size"] < 2e+6) && in_array($extension, $allowedExts)) {
		if ($file["error"] > 0) {
		    $this->form_validation->set_message('upload_spare_pic', $file["error"]);
		} else {
		    $pic = str_replace(' ', '-', $this->input->post('booking_id'));
		    $picName = $type.rand(10,100). $pic . "." . $extension;
		    $bucket = BITBUCKET_DIRECTORY;
                    
		    $directory = "misc-images/" . $picName;
		    $this->s3->putObjectFile($file["tmp_name"], $bucket, $directory, S3::ACL_PUBLIC_READ);

		    return $picName;
		}
	    } else {
		$this->form_validation->set_message('upload_spare_pic', 'File size or file type is not supported. Allowed extentions are "png", "jpg", "jpeg" and "pdf". '
		    . 'Maximum file size is 2 MB.');
		return FALSE;
	    }
	}
        
    }
    /**
     * @desc: Check user Seession
     * @return boolean
     */
    function checkUserSession(){
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
	    return TRUE;
	} else {
	    redirect(base_url() . "employee/login");
	}
    }
    
    /**
     * @Desc: This function is used to Uncancel Brackets Request
     * @params: Order ID
     * @return: void
     * 
     */
    function uncancel_brackets_request($order_id) {
        log_message('info', __FUNCTION__ . ' for order_id: '.$order_id );
        
        $data = array('active' => 1, 'cancellation_reason' => '');
        $brackets_id = $this->inventory_model->uncancel_brackets($order_id, $data);
        if (!empty($brackets_id)) {
            //Getiting brackets details
            $brackets_details = $this->inventory_model->get_brackets_by_id($order_id);

            //1. Now Send mail to vendor (order received from) for uncancellation Process
            // Sending mail to order_received_from vendor
            $order_received_from_email = $this->vendor_model->getVendorContact($brackets_details[0]['order_received_from']);

            $vendor_poc_mail = $order_received_from_email[0]['primary_contact_email'];
            $vendor_owner_mail = $order_received_from_email[0]['owner_email'];
            $to = $vendor_poc_mail . ',' . $vendor_owner_mail;


            //Getting template from Database
            $template_to = $this->booking_model->get_booking_email_template("un-cancel_brackets_order_received_from_vendor");
            if (!empty($template_to)) {
                $email_to['company_name'] = $order_received_from_email[0]['company_name'];
                $email_to['order_id'] = $order_id;
//                $email_to['19_24_requested'] = $brackets_details[0]['19_24_requested'];
                $email_to['26_32_requested'] = $brackets_details[0]['26_32_requested'];
                $email_to['36_42_requested'] = $brackets_details[0]['36_42_requested'];
//                $email_to['43_requested'] = $brackets_details[0]['43_requested'];
                $email_to['total_requested'] = $brackets_details[0]['total_requested'];
                $subject = "Brackets Request Un-Cancelled";
                $emailBody = vsprintf($template_to[0], $email_to);
                $this->notify->sendEmail($template_to[2], $to, $template_to[3] . ',' . $this->get_rm_email($brackets_details[0]['order_received_from']), '', $subject, $emailBody, "");
                //Loggin send mail success
                log_message('info', __FUNCTION__ . ' Un-Cancelled Brackets mail has been sent to order_received_from vendor ' . print_r($emailBody,TRUE));
            }

            // 2. Now sending mail to Order Received From Vendor
            //Sending Mail to order given to
            $vendor_requested_to = $this->vendor_model->getVendorContact($brackets_details[0]['order_given_to']);
            $vendor_poc_mail_1 = $vendor_requested_to[0]['primary_contact_email'];
            $vendor_owner_mail_1 = $vendor_requested_to[0]['owner_email'];
            $to = $vendor_poc_mail_1 . ',' . $vendor_owner_mail_1;

            //Getting template from Database
            $template_from = $this->booking_model->get_booking_email_template("un-cancel_brackets_requested_from_vendor");

            if (!empty($template_from)) {
                $email_from['order_id'] = $order_id;
//                $email_from['19_24_requested'] = $brackets_details[0]['19_24_requested'];
                $email_from['26_32_requested'] = $brackets_details[0]['26_32_requested'];
                $email_from['36_42_requested'] = $brackets_details[0]['36_42_requested'];
//                $email_from['43_requested'] = $brackets_details[0]['43_requested'];
                $email_from['total_requested'] = $brackets_details[0]['total_requested'];
                $email_from['owner_name'] = $order_received_from_email[0]['owner_name'];
                $email_from['company_name'] = $order_received_from_email[0]['company_name'];
                $email_from['address'] = $order_received_from_email[0]['address'];
                $email_from['district'] = $order_received_from_email[0]['district'];
                $email_from['state'] = $order_received_from_email[0]['state'];
                $email_from['pincode'] = $order_received_from_email[0]['pincode'];
                $email_from['primary_contact_phone_1'] = $order_received_from_email[0]['primary_contact_phone_1'];
                $email_from['owner_phone_1'] = $order_received_from_email[0]['owner_phone_1'];
                $subject = "Brackets Request Un-Cancelled";

                $emailBody = vsprintf($template_from[0], $email_from);
                $this->notify->sendEmail($template_from[2], $to, $template_from[3] . ',' . $this->get_rm_email($brackets_details[0]['order_given_to']), '', $subject, $emailBody, "");
                //Loggin send mail success
                log_message('info', __FUNCTION__ . '  Cancelled Brackets mail has been sent to order_given_to vendor ' . print_r($emailBody,TRUE));
            }


            //Setting success session data 
            $this->session->set_userdata('brackets_update_success', 'Brackets has been Un-Cancelled for Order ID ' . $order_id);
            //Logging
            log_message('info', __FUNCTION__ . ' Brackets Request has been Un cancelled for Order ID ' . $order_id);
        } else {
            //Setting error session data 
            $this->session->set_userdata('brackets_update_error', 'Error in Un-Cancellation of Brackets Requested for Order ID .' . $order_id);
            log_message('info', __FUNCTION__ . ' Error in Brackets Request un-Cancellation for Order ID ' . $order_id);
        }

        redirect(base_url() . 'employee/inventory/show_brackets_list');
    }
    
    function spare_part_booking_on_tab(){
        log_message('info', __FUNCTION__. "Entering... ");
	$offset = ($this->uri->segment(4) != '' ? $this->uri->segment(4) : 0);
        $total_rows =  $this->booking_model->get_spare_parts_booking(0, "All");
        
	$config['total_rows'] = $total_rows[0]['count'];
        $data['spare_parts'] = $this->booking_model->get_spare_parts_booking($config['total_rows'], $offset);
        $this->load->view('employee/sparepart_on_tab' , $data);
    }
    /**
     * @desc this used to cancelled Spare Part 
     * @param int $id
     * @param String $booking_id
     */
    function cancel_spare_parts($id, $booking_id){
        log_message('info', __FUNCTION__. "Entering... id ". $id." Booking ID ". $booking_id);
        if(!empty($id)){
            $remarks = $this->input->post("remarks");
            $this->service_centers_model->update_spare_parts(array('id' => $id, 'status NOT IN ("Completed","Cancelled")' =>NULL ), array('status' => "Cancelled"));
            $this->notify->insert_state_change($booking_id,"Spare Parts Cancelled","Spare Parts Requested", $remarks, 
                      $this->session->userdata('id'), $this->session->userdata('employee_id'), _247AROUND);
            $sc_data['current_status'] = "Pending";
            $sc_data['internal_status'] = "Pending";
            $sc_data['update_date'] = date("Y-m-d H:i:s");
          
            $this->vendor_model->update_service_center_action($booking_id,$sc_data);
            echo "Success";
            //redirect(base_url()."employee/inventory/get_spare_parts");
        } else {
            echo "Error";
        }
    }

    /**
     * @Desc: This function is used to filtered the brackets list in our crm
     * @params: void
     * @return: array
     * 
     */
    function get_filtered_brackets_list(){
        if($this->input->post('filter') === 'filter'){
            $sf_role = $this->input->post('sf_role');
            $sf_id = $this->input->post('sf_id');
            $start_date = date('Y-m-d 00:00:00', strtotime($this->input->post('start_date')));
            $end_date = date('Y-m-d 23:59:59', strtotime($this->input->post('end_date')));

            $data['brackets'] = $this->inventory_model->get_filtered_brackets($sf_role,$sf_id,$start_date,$end_date);
            if(!empty($data['brackets'])){
                //Getting name for order received from  to vendor
                foreach($data['brackets'] as $key=>$value){
                    $data['order_received_from'][$key] = $this->vendor_model->getVendorContact($value['order_received_from'])[0];

                    // Getting name for order given to vendor

                    $data['order_given_to'][$key] = $this->vendor_model->getVendorContact($value['order_given_to'])[0]['name'];
                }
                $response = $this->load->view('employee/show_filtered_brackets_list',$data);
                
                echo $response;
            }else{
                echo "No Data Found";
            }
        }else{
            echo "Invalid Request";
        }
    }
}