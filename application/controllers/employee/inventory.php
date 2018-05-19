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
         $this->load->model('reusable_model');
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->library('PHPReport');
        $this->load->library('notify');
        $this->load->library('S3');
        $this->load->library("pagination");
        $this->load->library("miscelleneous");
	$this->load->library('booking_utilities');

    }

    public function index() {
        
    }

    /**
     * @desc: This function is used to get bracket add form
     * @params: void
     * @return: view
     * 
     */
    public function get_bracket_add_form($sf_id = null,$sf_name = null) {
        //Getting ID from Session
        $id = $this->session->userdata('id');
        //Getting Employee Relation if present
        $sf_list = $this->vendor_model->get_employee_relation($id);
        if (!empty($sf_list)) {
            // Listing details acc to SF mapped
            $sf_list = $sf_list[0]['service_centres_id'];
            $select = "service_centres.name, service_centres.id";
            $service_center = $this->vendor_model->getVendorDetails($select);
            $sf_array = explode(',', $sf_list);
            foreach ($service_center as $value) {
                if (array_search($value['id'], $sf_array)) {
                    $data['vendor'][] = $value;
                }
            }
        } else {
            $select = "service_centres.name, service_centres.id";
            //Getting all values
            $data['vendor'] = $this->vendor_model->getVendorDetails($select);
        }
        
        $data['sf_id'] = urlencode($sf_id);
        $data['sf_name'] = urldecode($sf_name);
        $this->miscelleneous->load_nav_header();
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
                    $this->notify->insert_state_change($data_post['order_id'], _247AROUND_BRACKETS_PENDING, _247AROUND_BRACKETS_REQUESTED, "Brackets Requested", $this->session->userdata('id'), 
                            $this->session->userdata('employee_id'),ACTOR_BRACKET_RECEIEVED_CONFORMATION,NEXT_ACTION_BRACKET_RECEIEVED_CONFORMATION, _247AROUND);
                    $select = "primary_contact_email,owner_email, company_name, "
                            . "address, district, state, pincode,primary_contact_phone_1,owner_phone_1, owner_name";
                    $vendor_requested = $this->vendor_model->getVendorDetails($select, array('id' => $data_post['order_received_from']));
                    
                    $to = $vendor_requested[0]['primary_contact_email'] . ',' . $vendor_requested[0]['owner_email'];
                   
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
                        $subject = $template[4];
                        $emailBody = vsprintf($template[0], $email_order_received_from);
                        
                        $this->notify->sendEmail($template[2], $to, $template[3] . ',' . $this->get_rm_email($data_post['order_received_from']), '', $subject, $emailBody, "",'brackets_order_received_from_vendor');
                    }
                    
                    //Logging Email Send to order received from vendor
                    log_message('info', __FUNCTION__ . ' Email has been sent to order_received_from vendor ' . $vendor_requested[0]['company_name']);
                     //Sending Mail to order given to
                    $vendor_requested_to = $this->vendor_model->getVendorDetails($select, array('id' => $data_post['order_given_to']));
                    $to = $vendor_requested_to[0]['primary_contact_email'] . ',' . $vendor_requested_to[0]['owner_email'];

                    // Sending Mail to order given to vendor using Template
                    $email = array();
                    //Getting template from Database
                    $template1 = $this->booking_model->get_booking_email_template("brackets_requested_from_vendor");

                    if (!empty($template1)) {
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
                        $subject = vsprintf($template1[4], $vendor_requested[0]['company_name']);

                        $emailBody = vsprintf($template1[0], $email);

                        $this->notify->sendEmail($template1[2], $to, $template1[3], '', $subject, $emailBody, "",'brackets_requested_from_vendor');
                    }
                    //Logging Email Send to order sent to vendor
                    log_message('info', __FUNCTION__ . ' Email has been sent to order_sent_to vendor ' . $vendor_requested_to[0]['company_name']);
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
          if($this->session->userdata('user_group') == 'regionalmanager'){
            $sf_list_array = $this->vendor_model->get_employee_relation($id);
            if (!empty($sf_list_array)) {
                $sf_list = $sf_list_array[0]['service_centres_id'];
            }
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
        $this->miscelleneous->load_nav_header();
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
        $this->miscelleneous->load_nav_header();
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
        $order_given_to = $this->input->post('order_given_to');
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
                $this->notify->insert_state_change($order_id, _247AROUND_BRACKETS_SHIPPED, _247AROUND_BRACKETS_PENDING, "Brackets Shipped", $this->session->userdata('id'), 
                        $this->session->userdata('employee_id'), ACTOR_NOT_DEFINE,NEXT_ACTION_NOT_DEFINE,_247AROUND);
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
                        $subject = vsprintf($template[4], $order_received_from_email[0]['company_name']);
                        $emailBody = vsprintf($template[0], $email);
                        
                        $this->notify->sendEmail($template[2], $to , $template[3].','.$this->get_rm_email($order_received_from), '', $subject , $emailBody, $attachment,'brackets_shipment_mail');
                   
                        //Loggin send mail success
            log_message('info',__FUNCTION__.' Shipped mail has been sent to order_received_from vendor '. $emailBody);
                        
                   }
                   
                   //2. Sending mail to order_given_to vendor
            $order_given_to_email_to = $this->vendor_model->getVendorContact($order_given_to);
            $to = $order_given_to_email_to[0]['primary_contact_email'].','.$order_given_to_email_to[0]['owner_email'];
            $order_given_to_email = array();
                   //Getting template from Database
                   $template1 = $this->booking_model->get_booking_email_template("brackets_shipment_mail_to_order_given_to");
                   
                   if(!empty($template)){
                        $order_given_to_email['order_recieved_from'] = $order_received_from_email[0]['company_name'];
                        $order_given_to_email['order_id'] = $order_id;
                        $subject = vsprintf($template1[4], $order_received_from_email[0]['company_name']);
                        $emailBody = vsprintf($template1[0], $order_given_to_email);
                        
                        $this->notify->sendEmail($template1[2], $to , $template1[3], '', $subject , $emailBody, '','brackets_shipment_mail_to_order_given_to');
                   
                        //Loggin send mail success
                        log_message('info',__FUNCTION__.' Shipped mail has been sent to order_given_to vendor '. $emailBody);
                   }
            
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
        $this->miscelleneous->load_nav_header();
        $this->load->view("employee/update_brackets", $data);
    }
    
    /**
     * @Desc: This function is used to update receiving of brackets
     * @params: Array, Int
     * @return : view
     */
    function process_update_receiving_form() {
        $order_id = $this->input->post('order_id');
        $order_received_from = $this->input->post('order_received_from');
        $order_given_to = $this->input->post('order_given_to');
        $data['19_24_received'] = '0';
        $data['26_32_received'] = $this->input->post('26_32_received');
        $data['36_42_received'] = $this->input->post('36_42_received');
        $data['43_received'] = '0';
        $data['total_received'] = $this->input->post('total_received');
        $data['received_date'] = date('Y-m-d H:i:s');
        $data['is_received'] = 1;

        //Updating value in Brackets
        $update_brackets = $this->inventory_model->update_brackets($data, array('order_id' => $order_id));
        if (!empty($update_brackets)) {
            //Loggin success
            log_message('info', __FUNCTION__ . ' Brackets Received has been updated ' . print_r($data, TRUE));

            //Adding value in Booking State Change
            $this->notify->insert_state_change($order_id, _247AROUND_BRACKETS_RECEIVED, _247AROUND_BRACKETS_SHIPPED, "", $this->session->userdata('id'), 
                    $this->session->userdata('employee_id'), ACTOR_BRACKET_RECEIEVED_CONFORMATION,NEXT_ACTION_BRACKET_RECEIEVED_CONFORMATION,_247AROUND); 
            //Logging Success
            log_message('info', __FUNCTION__ . ' Brackets Shipped - Received state have been added in Booking State Change ');

            //update inventory stocks
            $inventory_stocks_data = array('receiver_entity_id' => $order_received_from,
                'receiver_entity_type' => _247AROUND_SF_STRING,
                'sender_entity_id' => $order_given_to,
                'sender_entity_type' => _247AROUND_SF_STRING,
                'order_id' => $order_id,
                'agent_id' => $this->session->userdata('id'),
                'agent_type' => _247AROUND_EMPLOYEE_STRING
            );
            $inventory_stocks_data['stock'] = $data['26_32_received'];
            $inventory_stocks_data['part_number'] = LESS_THAN_32_BRACKETS_PART_NUMBER;
            $return_response = $this->miscelleneous->process_inventory_stocks($inventory_stocks_data);
            if ($return_response) {
                $inventory_stocks_data['stock'] = $data['36_42_received'];
                $inventory_stocks_data['part_number'] = GREATER_THAN_32_BRACKETS_PART_NUMBER;
                $return_response = $this->miscelleneous->process_inventory_stocks($inventory_stocks_data);

                if ($return_response) {
                    log_message('info', __FUNCTION__ . ' Inventory Stoks Updated Successfully ' . print_r($inventory_stocks_data, true));
                } else {
                    log_message('info', __FUNCTION__ . ' Error In Updating Inventory Stoks ' . print_r($inventory_stocks_data, true));
                }
            } else {
                log_message('info', __FUNCTION__ . ' Error In Updating Received brackets Data ' . print_r($inventory_stocks_data, true));
            }

            //Sending mail to both vendor
            //1. Sending to Order received from vendore
            $order_received_from_email = $this->vendor_model->getVendorContact($order_received_from);
            $order_given_to_email = $this->vendor_model->getVendorContact($order_given_to);

            $vendor_poc_mail = $order_received_from_email[0]['primary_contact_email'];
            $vendor_owner_mail = $order_received_from_email[0]['owner_email'];
            $order_received_from_email_to = $vendor_poc_mail . ',' . $vendor_owner_mail;

            $vendor_poc_mail = $order_given_to_email[0]['primary_contact_email'];
            $vendor_owner_mail = $order_given_to_email[0]['owner_email'];
            $order_given_to_email_to = $vendor_poc_mail . ',' . $vendor_owner_mail;

            //1. Sending brackets Received Mail to order received from vendor
            $email = array();
            //Getting template from Database
            $template = $this->booking_model->get_booking_email_template("brackets_received_mail_vendor_order_requested_from");

            if (!empty($template)) {
                $email['order_id'] = $order_id;
                $subject = vsprintf($template[4], $order_received_from_email[0]['company_name']);
                $emailBody = vsprintf($template[0], $email);
                $this->notify->sendEmail($template[2], $order_received_from_email_to, $template[3] . ',' . $this->get_rm_email($order_received_from), '', $subject, $emailBody, '','brackets_received_mail_vendor_order_requested_from');
            }

            //Loggin send mail success
            log_message('info', __FUNCTION__ . ' Received mail has been sent to order_received_from vendor ' . $emailBody);

            //2. Sending mail to order_given_to vendor

            $email = array();
            //Getting template from Database
            $template = $this->booking_model->get_booking_email_template("brackets_received_mail_vendor_order_given_to");

            if (!empty($template)) {
                $email['order_recieved_from'] = $order_received_from_email[0]['company_name'];
                $email['order_id'] = $order_id;
                $subject = vsprintf($template[4], $order_received_from_email[0]['company_name']);
                $emailBody = vsprintf($template[0], $email);

                $this->notify->sendEmail($template[2], $order_given_to_email_to, $template[3], '', $subject, $emailBody, '','brackets_received_mail_vendor_order_given_to');
            }

            //Loggin send mail success
            log_message('info', __FUNCTION__ . ' Received mail has been sent to order_given_to vendor ' . $emailBody);

            //Setting success session data 
            $this->session->set_userdata('brackets_update_success', 'Brackets Received updated Successfully');
        } else {
            //Loggin error
            log_message('info', __FUNCTION__ . ' Brackets Received updated Error ' . print_r($data, TRUE));

            //Setting error session data 
            $this->session->set_userdata('brackets_update_error', 'No changes made to be updated.');
        }

        //Creating Received Data Array for Inventory Database
        $received_inventory_data[] = array(
            'vendor_id' => $this->input->post('order_given_to'),
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
        if ($received_check_order) {

            //Getting last row of return array from Database    
            $last_updated_array = end($received_check_order);
            //Updating data in Inventory Database for particular Order ID and remarks as  _247AROUND_BRACKETS_RECEIVED   
            $updated_received_data[] = array(
                'vendor_id' => $this->input->post('order_given_to'),
                'order_booking_id' => $order_id,
//            '19_24_current_count' => $this->input->post('19_24_received') + $last_updated_array['19_24_current_count'],
//            '26_32_current_count' => $this->input->post('26_32_received') + $last_updated_array['26_32_current_count'],
//            '36_42_current_count' => $this->input->post('36_42_received') + $last_updated_array['36_42_current_count'],
//            '43_current_count' => $this->input->post('43_received') + $last_updated_array['43_current_count'],
                '19_24_current_count' => '0',
                '26_32_current_count' => $this->input->post('26_32_received') + $last_updated_array['26_32_current_count'] + $last_updated_array['19_24_current_count'],
                '36_42_current_count' => $this->input->post('36_42_received') + $last_updated_array['36_42_current_count'] + $last_updated_array['43_current_count'],
                '43_current_count' => '0',
                'increment/decrement' => 1,
                'remarks' => 'Order ID'
            );

            $update_shipped_data_flag = $this->inventory_model->insert_inventory($updated_received_data);
            if ($update_shipped_data_flag) {
                //Logging Success
                log_message('info', __FUNCTION__ . ' Received Data has been Added in Inventory ' . print_r($updated_received_data, TRUE));
            } else {
                //Logging Error
                log_message('info', __FUNCTION__ . ' Error in Updating Received data in Inventory ' . print_r($updated_received_data, TRUE));
            }
        } else {

            //Inserting Data in Inventory Database
            $received_inventory_flag = $this->inventory_model->insert_inventory($received_inventory_data);
            if ($received_inventory_flag) {
                //Logging Success
                log_message('info', __FUNCTION__ . ' Received Data has been entered in Inventory ' . print_r($received_inventory_data, TRUE));
            } else {
                //Logging Error
                log_message('info', __FUNCTION__ . ' Error in addding Received data in Inventory ' . print_r($received_inventory_data, TRUE));
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
        $this->miscelleneous->load_nav_header();
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
        $this->miscelleneous->load_nav_header();
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
        $this->miscelleneous->load_nav_header();
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
                $this->notify->insert_state_change($order_id, _247AROUND_BRACKETS_PENDING, _247AROUND_BRACKETS_PENDING, "Brackets Shipped", $this->session->userdata('id'), 
                        $this->session->userdata('employee_id'), ACTOR_BRACKET_REQUESTED,NEXT_ACTION_BRACKET_REQUESTED,_247AROUND);
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
                        $this->notify->sendEmail($template[2], $to , $template[3].','.$this->get_rm_email($order_received_from), '', $subject , $emailBody, "",'brackets_order_received_from_vendor');
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
                        $this->notify->sendEmail($template[2], $to , $template[3], '', $subject , $emailBody, "",'brackets_requested_from_vendor');
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
                        $this->notify->sendEmail($template[2], $to , $template[3].','.$this->get_rm_email($brackets_details[0]['order_received_from']), '', $subject , $emailBody, "",'cancel_brackets_order_received_from_vendor');
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
                        $this->notify->sendEmail($template[2], $to , $template[3], '', $subject , $emailBody, "",'cancel_brackets_requested_from_vendor');
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
        $this->miscelleneous->load_nav_header();
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
            $this->miscelleneous->load_nav_header();
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
            
            $this->notify->insert_state_change($booking_id, $data['status'], "" , "Spare Parts Updated By ".$this->session->userdata('employee_id') , $this->session->userdata('id'), 
                    $this->session->userdata('employee_id'),ACTOR_NOT_DEFINE,NEXT_ACTION_NOT_DEFINE,_247AROUND);
            
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
                $this->notify->sendEmail($template_to[2], $to, $template_to[3] . ',' . $this->get_rm_email($brackets_details[0]['order_received_from']), '', $subject, $emailBody, "",'un-cancel_brackets_order_received_from_vendor');
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
                $this->notify->sendEmail($template_from[2], $to, $template_from[3], '', $subject, $emailBody, "",'un-cancel_brackets_requested_from_vendor');
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
        $this->checkUserSession();
	$offset = ($this->uri->segment(4) != '' ? $this->uri->segment(4) : 0);
            
        $sf = $this->vendor_model->get_employee_relation($this->session->userdata("id"));
        $vendor_id = array();
        if(!empty($sf)){
            $vendor_id = explode(",", $sf[0]["service_centres_id"]);
        }

        $data['spare_parts'] = $this->booking_model->get_spare_parts_booking(-1, $offset, $vendor_id);
        $this->load->view('employee/sparepart_on_tab' , $data);
    }
    /**
     * @desc this used to cancel Spare Part 
     * @param int $id
     * @param String $booking_id
     */
    function update_action_on_spare_parts($id, $booking_id, $requestType){
        log_message('info', __FUNCTION__. "Entering... id ". $id." Booking ID ". $booking_id);
        if(!($this->session->userdata('partner_id') || $this->session->userdata('service_center_id'))){
            $this->checkUserSession();
        } 
         
        if(!empty($id)){
            $remarks = $this->input->post("remarks");
            $flag = true;
            $b = array();
            switch ($requestType){
                case 'CANCEL_PARTS':
                case 'QUOTE_REQUEST_REJECTED';
                    $where = array('id' => $id );
                    $data = array('status' => "Cancelled");
                    if($requestType == "CANCEL_PARTS"){
                        $new_state = SPARE_PARTS_CANCELLED;
                        $b['internal_status'] = SPARE_PARTS_CANCELLED;
                    } else {
                        $new_state = REQUESTED_QUOTE_REJECTED;
                        $b['internal_status'] = REQUESTED_QUOTE_REJECTED;
                    }
                    
                    $old_state = "Spare Parts Requested";
                    $sc_data['current_status'] = "Pending";
                    $sc_data['internal_status'] = "Pending";
                    $sc_data['update_date'] = date("Y-m-d H:i:s");
          
                    $this->vendor_model->update_service_center_action($booking_id,$sc_data);
                    break;
                case 'CANCEL_COMPLETED_BOOKING_PARTS':
                    $where = array('id' => $id );
                    $data = array('status' => "Cancelled");
                    $new_state = SPARE_PARTS_CANCELLED;
                    $old_state = "Spare Parts Requested";
                    $sc_data['current_status'] = "InProcess";
                    $sc_data['internal_status'] = "Completed";
                    $sc_data['update_date'] = date("Y-m-d H:i:s");
          
                    $this->vendor_model->update_service_center_action($booking_id,$sc_data);
                    break;
                
                case 'REJECT_COURIER_INVOICE':
                    $where = array('booking_id' => $booking_id );
                    $data = array("approved_defective_parts_by_admin" => 0, 'status' => 'Defective Part Rejected By Partner', 'remarks_defective_part_by_sf' => $remarks);
                    $new_state = "Courier Invoice Rejected By Admin";
                    $old_state = "Defective Part Shipped By SF";
                    
                    $b['internal_status'] = "Courier Invoice Rejected By Admin";
                    
                    break;
                case 'APPROVE_COURIER_INVOICE':
                    $where_sp = "spare_parts_details.booking_id = '".$booking_id."' "
                    . " AND spare_parts_details.status NOT IN ('Completed', 'Cancelled') ";
                    $sp = $this->partner_model->get_spare_parts_booking($where_sp);
                    $data['status'] = "Defective Part Shipped By SF";
                    $data['approved_defective_parts_by_admin'] = 1;
                    $courier_charge = $this->input->post("courier_charge");
                    foreach ($sp as $key => $value) {
                        if($key == 0){
                            $data['courier_charges_by_sf'] = $courier_charge;
                        } else {
                             $data['courier_charges_by_sf'] = 0;
                        }
                        
                        $where = array("id" => $value['id']);
                        $this->service_centers_model->update_spare_parts($where, $data);
                    }
                    
                    $new_state = "Courier Invoice Approved By Admin";
                    $old_state = "Defective Part Shipped By SF";
                    
                    $b['internal_status'] = "Courier Invoice Approved By Admin";
                    $flag = FALSE;
                    break;
                    
                    case 'DEFECTIVE_PARTS_SHIPPED_BY_SF':
                        $where = array('id' => $id );
                        $data = array('status' => "Defective Part Shipped By SF");
                        $sc_data['current_status'] = "InProcess";
                        $sc_data['internal_status'] = "Defective Part Shipped By SF";
                        $sc_data['update_date'] = date("Y-m-d H:i:s");
                        
                        $this->vendor_model->update_service_center_action($booking_id,$sc_data);
                        
                        $old_state = "Defective Part Rejected By Partner";
                        $new_state = "Defective Part Shipped By SF";
                        
                        $b['internal_status'] = "Defective Part Shipped By SF";
                        break;
                    
                CASE 'NOT_REQUIRED_PARTS':
                    $data['defective_part_required'] = 0;
                    $where = array('id' => $id );
                    $new_state = "Spare Parts Not Required To Partner";
                    $old_state = "Spare Parts Requested";
                    break;
                
                CASE 'NOT_REQUIRED_PARTS_FOR_COMPLETED_BOOKING':
                    $data['defective_part_required'] = 0;
                    $where = array('id' => $id );
                    $new_state = "Spare Parts Not Required To Partner";
                    $old_state = "Spare Parts Requested";
                    $sc_data['current_status'] = "InProcess";
                    $sc_data['internal_status'] = "Completed";
                    $sc_data['update_date'] = date("Y-m-d H:i:s");
          
                    $this->vendor_model->update_service_center_action($booking_id,$sc_data);
                    break;
                
                CASE 'REQUIRED_PARTS':
                    $data['defective_part_required'] = 1;
                    $where = array('id' => $id );
                    $new_state = "Spare Parts Required To Partner";
                    $old_state = "Spare Parts Requested";
                    break;
            }
            if($flag){
                $response = $this->service_centers_model->update_spare_parts($where, $data);
                if($response && in_array($requestType,array('CANCEL_COMPLETED_BOOKING_PARTS','CANCEL_PARTS'))){
                   $this->update_inventory_on_cancel_parts($id,$booking_id);
                }
            }
            
            if($this->session->userdata('employee_id')){
                $agent_id = $this->session->userdata('id');
                $agent_name = $this->session->userdata('employee_id');
                $entity_id = _247AROUND;
                
                $this->notify->insert_state_change($booking_id, $new_state,$old_state, $remarks, 
                      $agent_id, $agent_name, ACTOR_NOT_DEFINE,NEXT_ACTION_NOT_DEFINE, $entity_id);
            }else if($this->session->userdata('partner_id')) {
                $agent_id = $this->session->userdata('agent_id');
                $agent_name = $this->session->userdata('partner_name');
                $entity_id = $this->session->userdata('partner_id');
                
                $this->notify->insert_state_change($booking_id, $new_state,$old_state, $remarks, 
                      $agent_id, $agent_name, ACTOR_NOT_DEFINE,NEXT_ACTION_NOT_DEFINE, $entity_id);
            }else if($this->session->userdata('service_center_id')){
                    $agent_id = $this->session->userdata('service_center_agent_id');
                    $agent_name = $this->session->userdata('service_center_name');
                    $entity_id = $this->session->userdata('service_center_id');
                    
                    $this->notify->insert_state_change($booking_id, $new_state,$old_state, $remarks, 
                      $agent_id, $agent_name, ACTOR_NOT_DEFINE,NEXT_ACTION_NOT_DEFINE,NULL, $entity_id);
            
            }
            
            $partner_id = $this->input->post('partner_id');
            $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, $b['internal_status'], $partner_id, $booking_id);
            if (!empty($partner_status)) {
                $b['partner_current_status'] = $partner_status[0];
                $b['partner_internal_status'] = $partner_status[1];
            }
            
            $this->booking_model->update_booking($booking_id, $b); 
           
            
            echo "Success";
            //redirect(base_url()."employee/inventory/get_spare_parts");
        } else {
            echo "Error";
        }
    }

    /**
     * @Desc: This function is used to get the brackets list in our crm
     * @params: void
     * @return: array
     * 
     */
    function get_brackets_detailed_list(){
        if($this->input->post()){
            
            switch($this->input->post('type')){
                case 'filter':
                    $response = $this->make_filter_data_for_brackets();
                    break;
                case 'search':
                    $response = $this->make_search_data_for_brackets();
                    break;
            }
            
            echo $response;
            
        } else {
            echo "Invalid Request";
        }
    }
    
    
    /**
     * @Desc: This function is used to get filtered  brackets list in our crm
     * @params: void
     * @return: array
     * 
     */
    private function make_filter_data_for_brackets() {
        $sf_role = $this->input->post('sf_role');
        $sf_id = $this->input->post('sf_id');
        
        $select = '*';
        
        //check sf_role 
        if(!empty($sf_id)){
            if($sf_role === 'order_received_from'){
                $where["order_received_from = '$sf_id'"] = NULL;
            }else if($sf_role === 'order_given_to'){
                $where["order_given_to = '$sf_id'"] = NULL;
            }
        }
        
        //check daterange selected or not
        if (!empty($this->input->post('start_date') && !empty($this->input->post('end_date')))) {
            $start_date = date('Y-m-d 00:00:00', strtotime($this->input->post('start_date')));
            $end_date = date('Y-m-d 23:59:59', strtotime($this->input->post('end_date')));   
            
            $where[" order_date >= '$start_date' AND order_date <= '$end_date'"] = NULL;
        }
        
        if(!empty($where)){
            $brackets_data = $this->get_brackets_data_by($select, $where);
        }else{
            $brackets_data = "<div class='text-danger text-center'> <b> No Data Found <b></div>";
        }
        
        
        return $brackets_data;
    }
    
    /**
     * @Desc: This function is used to get search  brackets list in our crm
     * by order id
     * @params: void
     * @return: array
     * 
     */
    private function make_search_data_for_brackets() {
        $order_id = trim($this->input->post('order_id'));
        
        $select = '*';
        
        $where = array('order_id' => $order_id);
        
        $brackets_data = $this->get_brackets_data_by($select, $where);
        
        return $brackets_data;
    }
    
    
    /**
     * @Desc: This function is used to get the brackets list in our crm by any option
     * @params: $select string
     * @return: $where string
     */
    private function get_brackets_data_by($select, $where) {
        $data['brackets'] = $this->inventory_model->get_filtered_brackets($select, $where);
        
        if (!empty($data['brackets'])) {
            //Getting name for order received from  to vendor
            foreach ($data['brackets'] as $key => $value) {
                $data['order_received_from'][$key] = $this->vendor_model->getVendorContact($value['order_received_from'])[0];

                // Getting name for order given to vendor

                $data['order_given_to'][$key] = $this->vendor_model->getVendorContact($value['order_given_to'])[0]['name'];
            }
            if($this->input->post('sf_id')){
                $response = $this->load->view('service_centers/show_filtered_brackets_list', $data);
            }else{
                $response = $this->load->view('employee/show_filtered_brackets_list', $data);
            }
            
        } else {
            $response = "No Data Found";
        }
        
        return $response;
    }
    /**
     * @desc This is used to load form.
     * This form helps to update booking price and assign parts who will send
     */
    function update_part_price_details(){
        $booking_id = trim($this->input->post("booking_id"));
        if(!empty($booking_id)){
            $data['zopper'] = $this->inventory_model->select_zopper_estimate(array("booking_id" => $booking_id));
            $data['data'] = $this->booking_model->getbooking_history($booking_id);
            $this->miscelleneous->load_nav_header();
            $this->load->view("employee/update_price_details_form", $data);
        } else {
            $this->miscelleneous->load_nav_header();
            $this->load->view("employee/update_price_details_form");
        }
    }
    
    function process_update_parts_details() {
        $this->form_validation->set_rules('booking_id', 'Booking ID', 'required|trim');
        $this->form_validation->set_rules('service_charge', 'Service Charge', 'trim');
        $this->form_validation->set_rules('transport_charge', 'Transport Charge', 'trim');
        $this->form_validation->set_rules('courier_charge', 'Courier_charge Charge', 'trim');
        $this->form_validation->set_rules('remarks', 'Remarks', 'trim|required');
        $this->form_validation->set_rules('around_part_commission', 'around_part_commission', 'trim|required');
        $this->form_validation->set_rules('part_estimate_given', 'Estimate Part Given', 'callback_check_validation_update_parts_details');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            
            $unit = $this->booking_model->get_unit_details(array("booking_id" => $data['booking_id'], 'price_tags' => REPAIR_IN_WARRANTY_TAG));
            if (!empty($unit)) {
               
                $success = $this->insert_zopper_form_data();
                if($success['success']){
                    $customer_total = $data['service_charge'] + $data['transport_charge'] +
                            $data['courier_charge'] + $data['part_estimate_given'] +$data['around_part_commission'];
                    //same_diff_vendor 1 means different vendor arrange part
                    if ($data['arrange_part_by'] == 1) {

                        $sf_parts = 0;
                    } else if ($data['arrange_part_by'] == 2) { //same_diff_vendor 1 means Same vendor arrange part

                        $is_gst = $this->vendor_model->is_tax_for_booking($data['booking_id']);
                        if(empty($is_gst[0]['gst_no']) ){
                            $sf_parts = ($data['part_estimate_given'] ) * parts_percentage * (1 + SERVICE_TAX_RATE);
                        } else {
                            $sf_parts = ($data['part_estimate_given'] ) * parts_percentage;
                        } 
                    }
                    $sf_service =  $data['service_charge'] * basic_percentage * (1 + SERVICE_TAX_RATE);
                    $venor_percentage = (($sf_service + $sf_parts)/$customer_total) * 100;
                     $u['customer_total'] = $u['partner_net_payable'] = $unit[0]['customer_total'] = $unit[0]['partner_net_payable'] =  
                            $unit[0]['partner_paid_basic_charges'] =
                        $u['partner_paid_basic_charges'] =    $customer_total;
                    $unit[0]["vendor_basic_percentage"] = $venor_percentage;
                    $u['vendor_basic_percentage'] = $venor_percentage;
                    $unit[0]['around_paid_basic_charges'] = 0;
                    $u['customer_paid_basic_charges'] = $u['around_paid_basic_charges'] = $u["customer_paid_parts"] = $u["customer_paid_extra_charges"] = 0;
                    $u['customer_net_payable'] = 0;
                    $u['id'] = $unit[0]['id'];
                    $this->booking_model->update_price_in_unit_details($u, $unit);  
                    if($this->input->post("entity_id") && $this->input->post("entity") == "vendor" ){
                        $assign_vendor_id = $this->input->post("entity_id");
                    } else {
                        $assign_vendor_id = $this->input->post("assigned_vendor_id");
                    }
                    $this->insert_update_spare_parts($assign_vendor_id, $data['booking_id'],$unit[0]['model_number'], $unit[0]['serial_number']);
                    $is_sent = $this->input->post('estimate_sent');
                   
                    if($is_sent == 1){

                        $sent  = $this->create_zopper_excel_sheet($unit,$success['id'], $data );
                        if($sent){
                            $userSession = array('success' => "Thanks To Update Booking Price. Estimate Sent to Partner");
                        } else {
                            $userSession = array('success' => "Thanks To Update Booking Price.  Estimate did not send to Partner");
                        }
                    } else {
                        $userSession = array('success' => "Thanks To Update Booking Price.");
                    }
                    
                    $this->session->set_userdata($userSession);
                    redirect(base_url() . "employee/inventory/update_part_price_details");

                } else{
                    $userSession = array('success' => "Please Check, Zopper Repair in warranty Booking Not Updated");
                    $this->session->set_userdata($userSession);
                    redirect(base_url() . "employee/inventory/update_part_price_details");
                }
               
            } else {
                $userSession = array('success' => "Please Check, Repair in warranty Booking only allow");
                $this->session->set_userdata($userSession);
                redirect(base_url() . "employee/inventory/update_part_price_details");
            }
        } else {
            $this->update_part_price_details();
        }
    }
    
    function insert_update_spare_parts($assigned_vendor_id, $booking_id,$model_number, $serial_number){
        $sp['parts_requested'] = $this->input->post("part_name");
        $sp['partner_id'] = ZOPPER_ID; 
        $sp['defective_part_required'] = 0;
        $sp['date_of_request'] = $sp['create_date'] = date('Y-m-d H:i:s');
        $sp['booking_id'] = $booking_id;
        $sp['status'] = "Delivered";
        $sp['service_center_id'] = $assigned_vendor_id; 
        $sp['model_number'] = $model_number;
        $sp['serial_number'] = $serial_number;
        $sp['purchase_price'] = $this->input->post('part_estimate_given');
        $sp['sell_price'] = $this->input->post('part_estimate_given') + $this->input->post('around_part_commission');
        $entity = "";
        if($this->input->post("entity")){
            $entity = $this->input->post("entity");
            
        }

        if($this->input->post("entity_id")){
            $entity_id = $this->input->post("entity_id");
            if($entity == "partner" ){
                $sp['partner_id'] = $entity_id; 
            }
        }
        
        $this->service_centers_model->spare_parts_action(array('booking_id' => $booking_id), $sp);
    }
    /**
     * 
     * @param type $unit_details
     * @param String $id
     * @return boolean
     */
    function create_zopper_excel_sheet($unit_details, $id, $formdata){
        $booking_id = $unit_details[0]['booking_id'];
        $where['length'] = -1;
        $where['where'] = array("booking_details.booking_id" => $booking_id);
        //RM Specific Bookings
         $sfIDArray =array();
        if($this->session->userdata('user_group') == 'regionalmanager'){
            $rm_id = $this->session->userdata('id');
            $rmServiceCentersData= $this->reusable_model->get_search_result_data("employee_relation","service_centres_id",array("agent_id"=>$rm_id),NULL,NULL,NULL,NULL,NULL);
            $sfIDList = $rmServiceCentersData[0]['service_centres_id'];
            $sfIDArray = explode(",",$sfIDList);
        }
        $booking_details = $this->booking_model->get_bookings_by_status($where, "users.name, services, order_id, booking_details.partner_id",$sfIDArray);
        
        $partner_data = $this->partner_model->getpartner_details("company_name, address, state, district, pincode, public_name ", array("partners.id" => $booking_details[0]->partner_id ));
        $data['name'] = $booking_details[0]->name;
        $data['booking_id'] = $booking_id;
        $data['services'] = $booking_details[0]->services;
        $data['category'] = $unit_details[0]['appliance_category'];
        $data['capacity'] = $unit_details[0]['appliance_capacity'];
        $data['remarks'] = $this->input->post("estimate_remarks");
        $data['order_id'] = $booking_details[0]->order_id;
        $data['date'] = date("jS M, Y");
        $data['company_name'] = $partner_data[0]['company_name'];
        $data['company_address'] = $partner_data[0]['address'].", ".$partner_data[0]['district'].", Pincode ".$partner_data[0]['state'];
        
        $l_data = array();
       
       $total_igst_tax_amount = $total_amount = 0;
        if($formdata["service_charge"] > 0){
           $data1 = array();
           $data1['brand'] = $unit_details[0]['appliance_brand']; 
           $data1['model_number'] = $unit_details[0]['model_number'];
           $data1['service_type'] = "Service Charge";
           $data1['taxable_value'] = $formdata["service_charge"];
           $data1['igst_rate'] = DEFAULT_TAX_RATE;
           $data1['igst_tax_amount'] = ($formdata["service_charge"] * DEFAULT_TAX_RATE)/100;
         
           $total_igst_tax_amount =  $total_igst_tax_amount + $data1['igst_tax_amount'];
           $data1['total_amount'] = sprintf("%1\$.2f",( $data1['igst_tax_amount'] + $formdata["service_charge"]));
           $total_amount = $total_amount + $data1['total_amount'];
           array_push($l_data, $data1);
        }
        
        if($formdata["transport_charge"] > 0){
           $data1 = array();
           $data1['brand'] = $unit_details[0]['appliance_brand']; 
           $data1['model_number'] = $unit_details[0]['model_number'];
           $data1['service_type'] = "Transport Charge";
           $data1['taxable_value'] = $formdata["transport_charge"];
           $data1['igst_rate'] = DEFAULT_TAX_RATE;
           $data1['igst_tax_amount']  = ($formdata["transport_charge"] * DEFAULT_TAX_RATE)/100;
           $total_igst_tax_amount =  $total_igst_tax_amount + $data1['igst_tax_amount'];
           $data1['total_amount'] = sprintf("%1\$.2f",($data1['igst_tax_amount']  + $formdata["transport_charge"]));
           $total_amount = $total_amount + $data1['total_amount'];
           array_push($l_data, $data1);
        }
        if($formdata["courier_charge"] > 0){
           $data1 = array();
           $data1['brand'] = $unit_details[0]['appliance_brand']; 
           $data1['model_number'] = $unit_details[0]['model_number'];
           $data1['service_type'] = "Courier Charge";
           $data1['taxable_value'] = $formdata["courier_charge"];
           $data1['igst_rate'] = DEFAULT_TAX_RATE;
           $data1['igst_tax_amount'] = ($formdata["courier_charge"] * DEFAULT_TAX_RATE)/100;
          
           $total_igst_tax_amount =  $total_igst_tax_amount + $data1['igst_tax_amount'];
           $data1['total_amount'] = sprintf("%1\$.2f",($data1['igst_tax_amount']  + $formdata["courier_charge"]));
           $total_amount += $data1['total_amount'];
           array_push($l_data, $data1);
        }
        
        if($formdata["part_estimate_given"] > 0){
           $data1 = array();
           $data1['brand'] = $unit_details[0]['appliance_brand']; 
           $data1['model_number'] = $unit_details[0]['model_number'];
           $data1['service_type'] = "Part Charge";
           $data1['taxable_value'] = $formdata["part_estimate_given"] + $formdata['around_part_commission'];
           $data1['igst_rate'] = DEFAULT_TAX_RATE;
           $data1['igst_tax_amount']  = (($formdata["part_estimate_given"] + $formdata['around_part_commission']) * DEFAULT_TAX_RATE)/100;
         
           $total_igst_tax_amount =  $total_igst_tax_amount + $data1['igst_tax_amount'];
           $data1['total_amount'] = sprintf("%1\$.2f",($data1['igst_tax_amount'] + $formdata["part_estimate_given"] + $formdata['around_part_commission']));
           $total_amount += $data1['total_amount'];
           array_push($l_data, $data1);
        }
        $data['price_inword'] = convert_number_to_words(round($total_amount,0));
        $data['taxable_value'] = sprintf("%1\$.2f",($unit_details[0]['customer_total']));
        $data['igst_tax_amount'] = sprintf("%1\$.2f",($total_igst_tax_amount));
        $data['total_amount'] = sprintf("%1\$.2f",($total_amount));
        $template = 'Estimate_Sheet.xlsx';
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
                'id' => 'estimate',
                'repeat' => false,
                'data' => $data,
                'format' => array(
                    'date' => array('datetime' => 'd/M/Y')
                )
            ),
            array(
                'id' => 'data',
                'repeat' => TRUE,
                'data' => $l_data,
                'format' => array(
                    'date' => array('datetime' => 'd/M/Y')
                )
            ),
            
                )
        );

        $output_file_excel = "Estimate_". $booking_id. ".xlsx";
        $R->render('excel', TMP_FOLDER.$output_file_excel);

        $emailtemplate = $this->booking_model->get_booking_email_template("zopper_estimate_send");
        if (!empty($template)) {
           
            $subject = vsprintf($emailtemplate[4], array($partner_data[0]['public_name'], $data['name']));
          //  $emailBody = vsprintf($emailtemplate[0], $estimate_cost);
            $json_result = $this->miscelleneous->convert_excel_to_pdf(TMP_FOLDER.$output_file_excel,$booking_id, "jobcards-pdf");
            $pdf_response = json_decode($json_result,TRUE);
            $output_pdf_file_name = $output_file_excel;
            $attachement_url = TMP_FOLDER.$output_file_excel;
           
            if($pdf_response['response'] === 'Success'){
                $output_pdf_file_name = $pdf_response['output_pdf_file'];
                $attachement_url = 'https://s3.amazonaws.com/' . BITBUCKET_DIRECTORY . '/jobcards-pdf/' . $output_pdf_file_name;
                log_message('info', __FUNCTION__ . ' Generated PDF File Name' . $output_pdf_file_name);
            } else if($pdf_response['response'] === 'Error'){
                

                log_message('info', __FUNCTION__ . ' Error in Generating PDF File');
           }
           
            $this->notify->sendEmail($emailtemplate[2], $emailtemplate[1], $emailtemplate[3], '', $subject, $emailtemplate[0], $attachement_url,'zopper_estimate_send');
           
            $this->inventory_model->update_zopper_estimate(array('id' => $id), array(
                "estimate_sent" => 1,
                "estimate_file" => $output_pdf_file_name,
                "estimate_remarks" =>  $this->input->post("estimate_remarks")
            ));
            
            return true;
        } else {
            return false;
        }
       
    }
    /**
     * @desc This is used to insert zopper form data
     * @return boolean
     */
    function insert_zopper_form_data(){
        $z['part_estimate_given'] = $this->input->post("part_estimate_given");
        $z['booking_id'] = $this->input->post("booking_id");
        $z['around_part_commission'] = $this->input->post("around_part_commission");
        $z['service_charge'] = $this->input->post("service_charge");
        $z['transport_charge'] = $this->input->post("transport_charge");
        $z['courier_charge'] = $this->input->post("courier_charge");
        $z['arrange_part_by'] = $this->input->post("arrange_part_by");
        $z['remarks'] = $this->input->post("remarks");
        $z['part_name'] = $this->input->post("part_name");
        if($this->input->post("entity")){
            $z['entity'] = $this->input->post("entity");
        }

        if($this->input->post("entity_id")){
            $z['entity_id'] = $this->input->post("entity_id");
        }
        
        $is_exist = $this->inventory_model->select_zopper_estimate(array("booking_id" => $z['booking_id']));
        if(!empty($is_exist)){
             $this->inventory_model->update_zopper_estimate(array('id' => $is_exist[0]['id']), $z);
             return array('success' => true, 'is_exist' => true, "id" =>$is_exist[0]['id']);
        } else {
            $z['create_date'] = date("Y-m-d H:i:s");
            $s = $this->inventory_model->insert_zopper_estimate($z); 
            return array('success' => $s, 'is_exist' => false, 'id' => $s);
        }
    }

    function check_validation_update_parts_details() {
        $part_charge = $this->input->post("part_charges");
        if ($part_charge > 0) {

            $this->form_validation->set_rules('same_diff_vendor', 'Part Will Arrange By Vendor Or Partner', 'required|trim');
            $part_arrange_by = $this->input->post("same_diff_vendor");
            if (!empty($part_arrange_by)) {
                if ($part_arrange_by == 1) {
//                    $spare_parts = $this->input->post("spare_parts");
//                    if (empty($spare_parts)) {
//                        $this->form_validation->set_message('check_validation_update_parts_details', 'Spare Parts has not requested By SF');
//                        return FALSE;
//                    }
                    $entity_id = $this->input->post("entity_id");
                    if (empty($entity_id)) {
                        $this->form_validation->set_message('check_validation_update_parts_details', 'Please Select Vendor Or Partner Name');
                        return false;
                    }
                } else if($part_arrange_by == 2){
                    $sf_id = $this->input->post("assigned_vendor_id");
                    $_POST["entity_id"] = $sf_id;
                    $_POST["entity"] = "vendor";
                    if(empty($sf_id)){
                        $this->form_validation->set_message('check_validation_update_parts_details', 'Please Assign Booking');
                        return FALSE;
                    }
                } 
                return TRUE;
                
            } else {
                $this->form_validation->set_message('check_validation_update_parts_details', 'Please Select Parts Arrange By');
                return FALSE;
            }
        } else {
            return true;
        }
    }
    
    /**
     * @desc: This function is used to get the last two month brackets order given to sf
     * @param: void
     * @return: JSON $response
     */
    function get_brackets_details(){
        $where= array('order_given_to' => $this->input->post('sf_id'),
                      'is_received' => 1,
                      "received_date >= (DATE_FORMAT(CURDATE(), '%Y-%m-01') - INTERVAL 2 MONTH)" => NULL);
        $data = $this->reusable_model->get_search_query('brackets',"DATE_FORMAT(received_date, '%b') AS month,SUM(26_32_received) as l_32,SUM(36_42_received) as g_32",$where,NULL,NULL,array('month'=> 'asc'),NULL,NULL,'month')->result_array();
        $response = array();
        if(!empty($data)){
            foreach($data as $value){
                switch ($value['month']){
                    case date('M'):
                        $response['cm_less_than_32'] = !empty($value['l_32'])?$value['l_32']:0;
                        $response['cm_greater_than_32'] = !empty($value['g_32'])?$value['g_32']:0;
                        break;
                    case date("M", strtotime("last month")):
                        $response['lm_less_than_32'] = !empty($value['l_32'])?$value['l_32']:0;
                        $response['lm_greater_than_32'] =!empty($value['g_32'])? $value['g_32']:0;
                        break;
                }
            }
        }
        
        echo json_encode($response);
    }
    
    /**
     * @Desc: This function is used to show data from the inventory ledger table
     * @params: $page string
     * @params: $entity_type string
     * @params: $entity_id string
     * @params: $inventory_id string
     * @params: $offset string
     * @return: void
     * 
     */
    function show_inventory_ledger_list($page = 0,$entity_type = "",$entity_id = "",$inventory_id = "",$offset = 0){
        if ($page == 0) {
	    $page = 50;
	}
        $where = "";
        if(!empty($entity_id) && !empty($entity_type)){
            $where .= " where (i.receiver_entity_id = $entity_id AND i.receiver_entity_type = '".$entity_type."' OR i.sender_entity_id = $entity_id AND i.sender_entity_type = '".$entity_type."')";
        }
        
        if(!empty($inventory_id)){
            $where .= " and i.inventory_id = $inventory_id";
        }
	$config['base_url'] = base_url() . 'employee/inventory/show_inventory_ledger_list/'.$page."/".$entity_type."/".$entity_id."/".$inventory_id;
	$config['total_rows'] = $this->inventory_model->get_inventory_ledger_data($page, $offset,$where,true);
	
	if($offset !== "All"){
		$config['per_page'] = $page;
	} else {
		$config['per_page'] = $config['total_rows'];
	}	
	
	$config['uri_segment'] = 8;
	$config['first_link'] = 'First';
	$config['last_link'] = 'Last';

	$this->pagination->initialize($config);
	$data['links'] = $this->pagination->create_links();
        $data['Count'] = $config['total_rows'];        
        $data['brackets'] = $this->inventory_model->get_inventory_ledger_data($config['per_page'], $offset,$where);
        $data['entity_id'] = $entity_id;
        $data['entity_type'] = $entity_type;
        $data['inventory_id'] = $inventory_id;
        
        if($this->session->userdata('service_center_id')){
            $this->load->view('service_centers/header');
            $this->load->view("service_centers/show_inventory_ledger_list", $data);
        }else if($this->session->userdata('id')){
            $this->miscelleneous->load_nav_header();
            $this->load->view("employee/show_inventory_ledger_list", $data);
        }else if($this->session->userdata('partner_id')){
            $this->load->view('partner/header');
            $this->load->view("partner/show_inventory_ledger_list", $data);
            $this->load->view('partner/partner_footer');
        }
        
    }
    
    /**
     * @Desc: This function is used to show current stock of the entity(vendor/employee)
     * @params: void
     * @return: void
     * 
     */
    function get_inventory_stock(){
        
        $select = 'stock,part_number,part_name,description,inventory_stocks.entity_id,inventory_stocks.entity_type,inventory_stocks.inventory_id';
        $post['length'] = -1;
        $post['where'] = array('inventory_stocks.entity_id'=>trim($this->input->post('entity_id')),'inventory_stocks.entity_type' => trim($this->input->post('entity_type')));
        $post['search_value'] = array();
        $post['order'] = "";
        
        $data['stock_details'] = $this->inventory_model->get_inventory_stock_list($post,$select);
        echo $this->load->view('employee/inventory_stock_details',$data);
    }
    
    /**
     * @Desc: This function is used to show form add inventory stocks for service center
     * @params: void
     * @return: void
     * 
     */
    function update_inventory_stock($sf_id = ""){
        $data['sf'] = $this->vendor_model->getVendorDetails('id,name',array('active' => '1'));
        $data['inventory'] = $this->inventory_model->get_inventory_master_list_data('inventory_id,part_name,part_number');
        $data['sf_id'] = $sf_id;
        $this->miscelleneous->load_nav_header();
        $this->load->view("employee/add_inventory_stock", $data);
    }
    
    /**
     * @Desc: This function is used to add inventory stocks for service center
     * @params: void
     * @return: void
     * 
     */
    function process_update_inventory_stock() {

        $this->form_validation->set_rules('sf_id', 'Service Center', 'trim|required');
        $this->form_validation->set_rules('inventory', 'Inventory type', 'trim|required');
        $this->form_validation->set_rules('quantity', 'Quantity', 'trim|required');

        if ($this->form_validation->run() === false) {
            $res['response'] = 'error';
            $res['msg'] = 'All fields are required';
        } else {
            $sf_id = $this->input->post('sf_id');
            if (!empty($sf_id)) {
                //update vendor brackets flag
                $this->vendor_model->edit_vendor(array('brackets_flag' => 1, 'agent_id' => $this->session->userdata('id')), $sf_id);
                $inventory_stocks_data = array('receiver_entity_id' => $sf_id,
                    'receiver_entity_type' => _247AROUND_SF_STRING,
                    'agent_id' => $this->session->userdata('id'),
                    'agent_type' => _247AROUND_EMPLOYEE_STRING,
                    'stock' => $this->input->post('quantity'),
                    'part_number' => $this->input->post('inventory')
                );
                
                $return_response = $this->miscelleneous->process_inventory_stocks($inventory_stocks_data);
//                //update inventory stocks for less than 32"
//                if ($this->input->post('l_32') !== "") {
//
//                    $inventory_stocks_data['stock'] = $this->input->post('l_32');
//                    $inventory_stocks_data['part_number'] = LESS_THAN_32_BRACKETS_PART_NUMBER;
//                    $return_response = $this->miscelleneous->process_inventory_stocks($inventory_stocks_data);
//                }
//                //update inventory stocks for greater than 32"
//                if ($this->input->post('g_32') !== "") {
//                    $inventory_stocks_data['stock'] = $this->input->post('g_32');
//                    $inventory_stocks_data['part_number'] = GREATER_THAN_32_BRACKETS_PART_NUMBER;
//                    $return_response = $this->miscelleneous->process_inventory_stocks($inventory_stocks_data);
//                }

                if ($return_response) {
                    $res['response'] = 'success';
                    $res['msg'] = 'Stocks details Updated Successfully';
                } else {
                    $res['response'] = 'error';
                    $res['msg'] = 'Error In updating stocks. Please Try Again';
                }
            } else {
                $res['response'] = 'error';
                $res['msg'] = 'Please Select Service Center';
            }
        }

        echo json_encode($res);
    }

    /**
     *  @desc : This function is used to show inventory stocks data
     *  @param : void
     *  @return : void
     */
    function show_inventory_stock_list(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/show_inventory_stock_list');
    }
    
    /**
     *  @desc : This function is used to show inventory stocks data
     *  @param : void
     *  @return : $output JSON
     */
    function get_inventory_stock_list(){
        $data = $this->get_stock();
        
        $post = $data['post'];
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->inventory_model->count_all_inventory_stocks($post),
            "recordsFiltered" =>  $this->inventory_model->count_filtered_inventory_stocks($post),
            "data" => $data['data'],
        );
        
        echo json_encode($output);
    }
    
    /**
     *  @desc : This function is used to get inventory stocks data
     *  @param : void
     *  @return : Array()
     */
    function get_stock(){
        $post = $this->get_post_data();
        $post['column_order'] = array();
        $post['column_search'] = array('name');
        
        $select = "inventory_stocks.entity_id,inventory_stocks.entity_type, (SELECT SUM(stock) FROM inventory_stocks as s WHERE inventory_stocks.entity_id = s.entity_id ) as total_stocks,service_centres.name";
        
        //RM Specific stocks
        $sfIDArray =array();
        if($this->session->userdata('user_group') == 'regionalmanager'){
            $rm_id = $this->session->userdata('id');
            $rmServiceCentersData= $this->reusable_model->get_search_result_data("employee_relation","service_centres_id",array("agent_id"=>$rm_id),NULL,NULL,NULL,NULL,NULL);
            $sfIDList = $rmServiceCentersData[0]['service_centres_id'];
            $sfIDArray = explode(",",$sfIDList);
        }
        
        $list = $this->inventory_model->get_inventory_stock_list($post,$select,$sfIDArray);
        $data = array();
        $no = $post['start'];
        foreach ($list as $stock_list) {
            $no++;
            $row = $this->get_inventory_stocks_table($stock_list, $no);
            $data[] = $row;
        }
        
        return array(
            'data' => $data,
            'post' => $post
            
        );
    }
    
    /**
     *  @desc : This function is used to get the post data for booking by status
     *  @param : void()
     *  @return : $post Array()
     */
    private function get_post_data(){
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');

        return $post;
    }
    
    /**
     *  @desc : This function is used to generate table for inventory stocks
     *  @param : $stock_list array
     *  @param : $no string
     *  @return : $row Array()
     */
    function get_inventory_stocks_table($stock_list, $no){
        $row = array();
        
        $sf = "<a href='javascript:void(0);' onclick='";
        $sf .= "get_vendor_stocks(".'"'.$stock_list->entity_id.'"';
        $sf .= ', "'.$stock_list->entity_type.'"';
        $sf .= ")'>".$stock_list->name."</a>";
        
        $row[] = $no;
        $row[] = $sf;
        $row[] = ($stock_list->total_stocks >= 0) ? $stock_list->total_stocks: 0;
        
        return $row;
    }

    function spare_invoice_list(){
        log_message("info", __METHOD__);
        $w['length'] =-1;
        $w['where'] = array("booking_details.request_type" => REPAIR_OOW_TAG, 
            "status != 'Cancelled'" => NULL, 
            "spare_parts_details.create_date >= '2017-12-01'" => NULL, 
            "(`purchase_invoice_id` IS NULL ||  `sell_invoice_id` IS NULL)" => NULL,
            "spare_parts_details.partner_id != '"._247AROUND."'" => NULL);
        $w['select'] = "spare_parts_details.id, spare_parts_details.booking_id, purchase_price, public_name,"
                . "purchase_invoice_id,sell_invoice_id, incoming_invoice_pdf, sell_price";
        $data['spare'] = $this->inventory_model->get_spare_parts_query($w);
        $this->miscelleneous->load_nav_header();
        $this->load->view("employee/spare_invoice_list", $data);

    }
    
    /**
     *  @desc : This function is used to show inventory master list table
     *  @param : void
     *  @return : void
     */
    function inventory_master_list(){
        $this->miscelleneous->load_nav_header();
        $this->load->view("employee/inventory_master_list");
    }
    
    /**
     *  @desc : This function is used to show inventory master list data
     *  @param : void
     *  @return : void
     */
    function get_inventory_master_list(){
        $data = $this->get_master_list_data();
        
        $post = $data['post'];
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->inventory_model->count_all_inventory_master_list($post),
            "recordsFiltered" =>  $this->inventory_model->count_filtered_inventory_master_list($post),
            "data" => $data['data'],
        );
        
        echo json_encode($output);
    }
    
    function get_master_list_data(){
        $post = $this->get_post_data();
        $post['column_order'] = array();
        $post['column_search'] = array('part_name','part_number','services.services','services.id','model_number','serial_number');
        
        $select = "inventory_master_list.*,services.services";
        
        $list = $this->inventory_model->get_inventory_master_list($post,$select);
        $data = array();
        $no = $post['start'];
        foreach ($list as $stock_list) {
            $no++;
            $row = $this->get_inventory_master_list_table($stock_list, $no);
            $data[] = $row;
        }
        
        return array(
            'data' => $data,
            'post' => $post
            
        );
    }
    
    function get_inventory_master_list_table($stock_list, $no){
        $row = array();
        $json_data = json_encode($stock_list);
        
        //get agent name
        if ($stock_list->entity_type === _247AROUND_EMPLOYEE_STRING) {
            $employe_details = $this->employee_model->getemployeefromid($stock_list->entity_id);
            $agent_name = $employe_details[0]['full_name'];
        } else if ($stock_list->entity_type === _247AROUND_PARTNER_STRING) {
            $partner_details = $this->partner_model->getpartner_details('public_name', array('partners.id' => $stock_list->entity_id));
            $agent_name = $partner_details[0]['public_name'];
        } else if ($stock_list->entity_type === _247AROUND_SF_STRING) {
            $vendor_details = $this->partner_model->getVendorDetails('name', array('id' => $stock_list->entity_id));
            $agent_name = $vendor_details[0]['public_name'];
        }else{
            $agent_name = "";
        }
        $row[] = $no;
        $row[] = $stock_list->services;
        $row[] = $stock_list->model_number;
        $row[] = $stock_list->type;
        $row[] = $stock_list->part_name;
        $row[] = $stock_list->part_number;
        $row[] = $stock_list->serial_number;
        $row[] = $stock_list->description;
        $row[] = $stock_list->size;
        $row[] = $stock_list->price;
        $row[] = $stock_list->entity_type;
        $row[] = $agent_name;
        $row[] = "<a href='javascript:void(0)' class ='btn btn-primary' id='edit_master_details' data-id='$json_data'>Edit</a>";
        
        return $row;
    }
    
    /**
     *  @desc : This function is used to perform add/edit action on the inventory_master_list table
     *  @param : void()
     *  @return : $response JSON
     */
    function process_inventoy_master_list_data() {
        $submit_type = $this->input->post('submit_type');
        if(!empty($submit_type)){
            $data = array('part_name' => $this->input->post('part_name'),
                      'part_number' => $this->input->post('part_number'),
                      'serial_number' => $this->input->post('serial_number'),
                      'model_number' => $this->input->post('model_number'),
                      'size' => $this->input->post('size'),
                      'price' => $this->input->post('price'),
                      'type' => $this->input->post('type'),
                      'description' => $this->input->post('description'),
            );
        
            $entity_type = $this->input->post('entity_type');
            $entity_id = $this->input->post('entity_id');
            $service_id = $this->input->post('service_id');
            if(!empty($entity_id) && !empty($entity_type)){
                $data['entity_id'] = $entity_id;
                $data['entity_type'] = $entity_type;
            }
            
            if(!empty($service_id)){
                $data['service_id'] = $service_id;
            }

            switch (strtolower($submit_type)) {
                case 'add':
                    $data['create_date'] = date('Y-m-d H:i:s');
                    $response = $this->add_inventoy_master_list_data($data);
                    break;
                case 'edit':
                    $response = $this->edit_inventoy_master_list_data($data);
                    break;
            }
        }else{
            $response['response'] = 'error';
            $response['msg'] = 'Please Try Again!!!';
            log_message("info", __METHOD__.'Invalid request type');
        }
        
        
        echo json_encode($response);
    }
    
    /**
     *  @desc : This function is used to perform insert action on the partner_file_upload_header_mapping table
     *  @param : $data array()
     *  @return : $res array()
     */
    function add_inventoy_master_list_data($data) {
        $response = $this->inventory_model->insert_inventory_master_list_data($data);
        if (!empty($response)) {
            $res['response'] = 'success';
            $res['msg'] = 'Inventory added successfully';
            log_message("info",  __METHOD__.'Inventory added successfully');
        } else {
            $res['response'] = 'error';
            $res['msg'] = 'Error in inserting inventory details';
            log_message("info",  __METHOD__.'Error in inserting inventory details');
        }
        
        return $res;
    }
    
    /**
     *  @desc : This function is used to perform edit action on the partner_file_upload_header_mapping table
     *  @param : $data array()
     *  @return : $res array()
     */
    function edit_inventoy_master_list_data($data) {
        $response = $this->inventory_model->update_inventory_master_list_data(array('inventory_id' => $this->input->post('inventory_id')),$data);;
        if (!empty($response)) {
            $res['response'] = 'success';
            $res['msg'] = 'Details has been updated successfully';
            log_message("info",  __METHOD__.'Details has been updated successfully');
        } else {
            $res['response'] = 'error';
            $res['msg'] = 'Error in updating details';
            log_message("info",  __METHOD__.'error in updating  details');
        }
        
        return $res;
    }
    
    /**
     *  @desc : This function is used to get inventory snapshot for each sf based on last 1 Month bookings 
     * for that sf 
     *  @param : void
     *  @return : $sf_list JSON
     */
    function get_inventory_snapshot(){
        $sf_list = array();
       
        //get avg booking for each sf in last 1 month
        $avg_booking_select = 'assigned_vendor_id,count(DISTINCT booking_details.booking_id),(count(DISTINCT booking_details.booking_id)/30) as avg_booking';
        $avg_booking_where = array('price_tags' => _247AROUND_WALL_MOUNT__PRICE_TAG,
            "booking_unit_details.create_date >= (NOW() - Interval 30 Day)" => NULL,'assigned_vendor_id IS NOT NULL' =>NULL);
        $sf_bookings_snapshot = $this->inventory_model->get_inventory_snapshot($avg_booking_select,$avg_booking_where,'assigned_vendor_id');
       
        //get total stocks for each sf
        $inventory_select = "SUM(IF(inventory_stocks.inventory_id = 1, inventory_stocks.stock, 0)) AS l_32,SUM(IF(inventory_stocks.inventory_id = 2, inventory_stocks.stock, 0)) AS g_32,service_centres.name,service_centres.id";
        $inventory_where['length'] = -1;
        $inventory_where['group_by'] = 'inventory_stocks.entity_id';
        $inventory_count = $this->inventory_model->get_inventory_stock_list($inventory_where,$inventory_select,array(), false);
      
        //get no of days by which brackets whould be exhausted for the sf
        if(!empty($sf_bookings_snapshot)){
            foreach ($sf_bookings_snapshot as $value){
               
                $key = array_search($value['assigned_vendor_id'], array_column($inventory_count, 'id'));
                if($key !== FALSE){
                    $total_stocks = $inventory_count[$key]['l_32'] + $inventory_count[$key]['g_32'];
                    $no_of_days_brackets_exhausted = abs($total_stocks/$value['avg_booking']);
                
                    $tmp['sf_id'] = $value['assigned_vendor_id'];
                    $tmp['sf_name'] = $inventory_count[$key]['name'];
                    $tmp['brackets_exhausted_days'] = ($total_stocks > 0)? round($no_of_days_brackets_exhausted) : 0;
                    $tmp['l_32'] = ($total_stocks > 0)? $inventory_count[$key]['l_32'] : 0;
                    $tmp['g_32'] = ($total_stocks > 0)? $inventory_count[$key]['g_32'] : 0;
                    array_push($sf_list, $tmp);
                }
            }
        }
        
        array_multisort(array_column($sf_list,'brackets_exhausted_days'), SORT_ASC, $sf_list);
        
        echo json_encode($sf_list);
    }
    
    /** @desc: This function is used to upload the spare parts file. By this method we can add spare details in our inventory_mast_list table.
     * @param: void
     * @return void
     */
    function upload_inventory_details_file(){
        
        $data['services'] = $this->vendor_model->selectservice();
        $this->miscelleneous->load_nav_header();
	$this->load->view('employee/upload_spare_part_details',$data);
    }
    
    function get_inventory_stocks_details(){
        $post = $this->get_post_data();
        $post[''] = array();
        $post['column_order'] = array();
        $post['column_search'] = array('part_name','part_number','serial_number','model_number','type');
        $post['where'] = array('inventory_stocks.entity_id'=>trim($this->input->post('entity_id')),'inventory_stocks.entity_type' => trim($this->input->post('entity_type')),'inventory_stocks.stock <> 0' => NULL);
        if($this->input->post('is_show_all')){
            unset($post['where']['inventory_stocks.stock <> 0']);
        }
        $select = "inventory_master_list.*,inventory_stocks.stock,services.services,inventory_stocks.entity_id as receiver_entity_id,inventory_stocks.entity_type as receiver_entity_type";
        
        //RM Specific stocks
        $sfIDArray =array();
        if($this->session->userdata('user_group') == 'regionalmanager'){
            $rm_id = $this->session->userdata('id');
            $rmServiceCentersData= $this->reusable_model->get_search_result_data("employee_relation","service_centres_id",array("agent_id"=>$rm_id),NULL,NULL,NULL,NULL,NULL);
            $sfIDList = $rmServiceCentersData[0]['service_centres_id'];
            $sfIDArray = explode(",",$sfIDList);
        }
        
        $list = $this->inventory_model->get_inventory_stock_list($post,$select,$sfIDArray);
        $data = array();
        $no = $post['start'];
        foreach ($list as $inventory_list) {
            $no++;
            $row = $this->get_inventory_stocks_details_table($inventory_list, $no);
            $data[] = $row;
        }
        
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->inventory_model->count_all_inventory_stocks($post),
            "recordsFiltered" =>  $this->inventory_model->count_filtered_inventory_stocks($post),
            "data" => $data,
        );
        
        echo json_encode($output);
    }
    
    function get_inventory_stocks_details_for_warehouse(){
        $post = $this->get_post_data();
        $post[''] = array();
        $post['column_order'] = array();
        $post['column_search'] = array('part_name','part_number','serial_number','model_number','type','services.id','services.services');
        $post['where'] = array('inventory_master_list.entity_id'=>trim($this->input->post('entity_id')),'inventory_master_list.entity_type' => trim($this->input->post('entity_type')),'inventory_stocks.stock <> 0' => NULL);
        if($this->input->post('is_show_all')){
            unset($post['where']['inventory_stocks.stock <> 0']);
        }
        $select = "inventory_master_list.*,inventory_stocks.stock,services.services,inventory_stocks.entity_id as receiver_entity_id,inventory_stocks.entity_type as receiver_entity_type";
        
        //RM Specific stocks
        $sfIDArray =array();
        if($this->session->userdata('user_group') == 'regionalmanager'){
            $rm_id = $this->session->userdata('id');
            $rmServiceCentersData= $this->reusable_model->get_search_result_data("employee_relation","service_centres_id",array("agent_id"=>$rm_id),NULL,NULL,NULL,NULL,NULL);
            $sfIDList = $rmServiceCentersData[0]['service_centres_id'];
            $sfIDArray = explode(",",$sfIDList);
        }
        
        $list = $this->inventory_model->get_inventory_stock_list($post,$select,$sfIDArray);
        $data = array();
        $no = $post['start'];
        foreach ($list as $inventory_list) {
            $no++;
            $row = $this->get_inventory_stocks_details_table($inventory_list, $no);
            $data[] = $row;
        }
        
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->inventory_model->count_all_inventory_stocks($post),
            "recordsFiltered" =>  $this->inventory_model->count_filtered_inventory_stocks($post),
            "data" => $data,
        );
        
        echo json_encode($output);
    }
    
    
    private function get_inventory_stocks_details_table($inventory_list,$sn){
        $row = array();
        
        $row[] = $sn;
        $row[] = $inventory_list->services;
        $row[] = $inventory_list->model_number;
        $row[] = $inventory_list->type;
        $row[] = $inventory_list->part_name;
        $row[] = $inventory_list->part_number;
        $row[] = $inventory_list->serial_number;
        if($inventory_list->stock){
           $row[] = '<a href="'. base_url().'employee/inventory/show_inventory_ledger_list/0/'.$inventory_list->receiver_entity_type.'/'.$inventory_list->receiver_entity_id.'/'.$inventory_list->inventory_id.'" target="_blank" title="Get Ledger Details">'.$inventory_list->stock.'<a>'; 
        }else{
            $row[] = '<a href="javascript:void(0);" title="Out Of Stock">0<a>';
        }
        
        $row[] = $inventory_list->size;
        $row[] = $inventory_list->price;
        
        
        return $row;
    }
    
    
    /**
     *  @desc : This function is used to get inventory part name
     *  @param : void
     *  @return : $res array() // consist response message and response status
     */
    function get_parts_name(){
        
        $model_number = $this->input->post('model_number');
        $part_type = $this->input->post('part_type');
        
        $post['length'] = -1;
        $post['where'] = array('entity_id' => $this->input->post('entity_id'), 'entity_type' => $this->input->post('entity_type'), 'service_id' => $this->input->post('service_id'), 'model_number' => $model_number,'type'=> $part_type);
        $inventory_details = $this->inventory_model->get_inventory_master_list($post, 'inventory_master_list.part_name', true);
        
        if($this->input->post('is_option_selected')){
            $option = '<option selected disabled>Select Part Name</option>';
        }else{
            $option = '';
        }
        

        foreach ($inventory_details as $value) {
            $option .= "<option value='" . $value['part_name'] . "'";
            $option .=" > ";
            $option .= $value['part_name'] . "</option>";
        }

        echo $option;
    }
    
    /**
     *  @desc : This function is used to get inventory part name
     *  @param : void
     *  @return : $res array() // consist response message and response status
     */
    function get_inventory_price(){
        
        $model_number = $this->input->post('model_number');
        $part_name = $this->input->post('part_name');
        $entity_id = $this->input->post('entity_id');
        $entity_type = $this->input->post('entity_type');
        $service_id = $this->input->post('service_id');
        
        if($model_number && $part_name && $entity_id && $entity_type && $service_id){
            $where= array('entity_id' => $entity_id, 'entity_type' => $entity_type, 'service_id' => $service_id, 'model_number' => $model_number,"part_name" => $part_name);
            $inventory_details = $this->inventory_model->get_inventory_master_list_data('SUM(inventory_master_list.price) as price,inventory_master_list.inventory_id', $where);

            if(!empty($inventory_details)){
                $data['price'] = $inventory_details[0]['price'];
                $data['inventory_id'] = $inventory_details[0]['inventory_id'];
            }else{
                $data['price'] = '';
                $data['inventory_id'] = '';
            }
        }else{
            $data['price'] = '';
            $data['inventory_id'] = '';
        }
        
        echo json_encode($data);
    }
    
    
    /**
     *  @desc : This function is used to show inventory count on SF CRM
     *  @param : void
     *  @return : $response array()
     */
    function get_sf_notification_data(){
        $this->checkSFSession();
        $response = array();
        if($this->session->userdata('service_center_id')){
            $post['where'] = "spare_parts_details.partner_id = '" . $this->session->userdata('service_center_id') . "' AND  entity_type =  '"._247AROUND_SF_STRING."' AND status = '" . SPARE_PARTS_REQUESTED . "' "
                . " AND booking_details.current_status IN ('Pending', 'Rescheduled') ";
            $inventory_data = $this->inventory_model->count_spare_parts($post);

            $brackets_data = $this->inventory_model->get_filtered_brackets('count(id) as total_brackets',array('order_given_to' =>$this->session->userdata('service_center_id'),'is_shipped' => 0 ));

            $response['inventory'] = $inventory_data;
            $response['brackets'] = $brackets_data[0]['total_brackets'];
        }
        
        
        echo json_encode($response);
    }
    
    /**
     * @desc This is used to update spare related field. Just pass field name, value and spare ID
     */
    function update_spare_parts_column(){
        $this->form_validation->set_rules('data', 'Data', 'required');
        $this->form_validation->set_rules('id', 'id', 'required');
        $this->form_validation->set_rules('column', 'column', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post('data');
            $id = $this->input->post('id');
            $column = $this->input->post('column');
            
            $this->service_centers_model->update_spare_parts(array('id' => $id), array($column => $data));
            echo "Success";
        } else {
            echo "Error";
        }
    }
    
    /**
     *  @desc : This function is used to get inventory part name
     *  @param : void
     *  @return : $res array() // consist response message and response status
     */
    function get_parts_type(){
        
        $model_number = $this->input->post('model_number');
        
        $post['length'] = -1;
        $post['where'] = array('entity_id' => $this->input->post('entity_id'), 'entity_type' => $this->input->post('entity_type'), 'service_id' => $this->input->post('service_id'), 'model_number' => $model_number);
        $inventory_details = $this->inventory_model->get_inventory_master_list($post, 'inventory_master_list.type', true);
        
        $option = '<option selected disabled>Select Part Type</option>';

        foreach ($inventory_details as $value) {
            $option .= "<option value='" . $value['type'] . "'";
            $option .=" > ";
            $option .= $value['type'] . "</option>";
        }

        echo $option;
    }

    /**
     *@desc This is used to upload spare related image. It is used from Booking view details page.
     */
    function processUploadSpareItem(){
        log_message('info', __METHOD__. " ". print_r($this->input->post(), TRUE). " ". print_r($_FILES, true)) ;
        $allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
        $spareID = $this->input->post('spareID');
        $bookingID = $this->input->post('booking_id');
        $spareColumn = $this->input->post('spareColumn');
        $defective_parts_pic = $this->miscelleneous->upload_file_to_s3($_FILES["file"], 
                        $spareColumn, $allowedExts, $bookingID, "misc-images", "sp_parts");
        if($defective_parts_pic){
            $this->service_centers_model->update_spare_parts(array('id' => $spareID), array($spareColumn => $defective_parts_pic));
            echo json_encode(array('code' => "success", "name" => $defective_parts_pic));
        } else {
            echo json_encode(array('code' => "error", "message" => "File size or file type is not supported"));
        }
    }
    
    /**
     * @desc: This function will check SF Session
     * @param: void
     * @return: true if details matches else session is destroy.
     */
    function checkSFSession() {
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'service_center') 
                && !empty($this->session->userdata('service_center_id')) && !empty($this->session->userdata('is_sf'))) {
            return TRUE;
        } else {
            log_message('info', __FUNCTION__. " Session Expire for Service Center");
            $this->session->sess_destroy();
            redirect(base_url() . "service_center/login");
        }
    }
    
    
    /**
     * @desc: This function is used to update the inventory on cancel spare parts and change status to cancelled in booking unit details
     * @param: $spare_id integer
     * @param: $booking_id integer
     * @return: $response boolean
     */
    function update_inventory_on_cancel_parts($spare_id, $booking_id){
        log_message("info", __METHOD__);
        $spare_details = $this->service_centers_model->get_spare_parts_booking(array('spare_parts_details.id' => $spare_id),'spare_parts_details.booking_unit_details_id,spare_parts_details.shipped_inventory_id,spare_parts_details.partner_id,spare_parts_details.entity_type,spare_parts_details.requested_inventory_id');
        
        //update status in booking unit details to cancel
        if(!empty($spare_details) && !empty($spare_details[0]['booking_unit_details_id'])){
            $update_unit_details = $this->booking_model->update_booking_unit_details_by_any(array('id' => $spare_details[0]['booking_unit_details_id']),array('booking_status' => 'Cancelled','ud_closed_date'=> date("Y-m-d H:i:s")));
            if($update_unit_details){
                log_message("info","Unit Details Updated Successfully");
            }else{
                log_message("info","Error in updating unit details");
            }
        }
        
        //if stock consumend them increase the inventory stock
        if (!empty($spare_details) && !empty($spare_details[0]['shipped_inventory_id']) && !empty($spare_details[0]['requested_inventory_id'])) {
            $data['receiver_entity_id'] = $spare_details[0]['partner_id'];
            $data['receiver_entity_type'] = $spare_details[0]['entity_type'];
            $data['stock'] = -1;
            $data['booking_id'] = $booking_id;
            $data['agent_id'] = $this->session->userdata('id');
            $data['agent_type'] = _247AROUND_EMPLOYEE_STRING;
            $data['is_wh'] = TRUE;
            $data['inventory_id'] = $spare_details[0]['shipped_inventory_id'];
            $this->miscelleneous->process_inventory_stocks($data);
        }
        
        //create job card
        $this->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);
    }
    
    
     /**
     *  @desc : This function is used to show the view so that partner can tag spare invoice send by him
     *  @param : void
     *  @return :void
     */
    function tag_spare_invoice_send_by_partner(){
        $this->miscelleneous->load_nav_header();
        $this->load->view("employee/tag_spare_invoice_send_by_partner");
    }
    
    /**
     *  @desc : This function is used to get inventory part model number
     *  @param : void
     *  @return : $res array() // consist response message and response status
     */
    function get_part_model_number(){
        $post['length'] = -1;
        $post['where'] = array('entity_id' => $this->input->get('entity_id'), 'entity_type' => $this->input->get('entity_type'), 'service_id' => $this->input->get('service_id'));
        $inventory_details = $this->inventory_model->get_inventory_master_list($post, 'inventory_master_list.model_number', true);
        
        $option = '<option selected disabled>Select Model Number</option>';

        foreach ($inventory_details as $value) {
            $option .= "<option value='" . $value['model_number'] . "'";
            $option .=" > ";
            $option .= $value['model_number'] . "</option>";
        }

        echo $option;
    }
    
    
    /**
     *  @desc : This function is used to insert spare data send by partner to warehouse
     *  @param : void
     *  @return : $res JSON // consist response message and response status
     */
    function process_spare_invoice_tagging(){
        log_message("info",__METHOD__);
        $partner_id = $this->input->post('partner_id');
        $invoice_id = $this->input->post('invoice_id');
        $invoice_dated = $this->input->post('dated');
        $wh_id = $this->input->post('wh_id');
        
        if(!empty($partner_id) && !empty($invoice_id) && !empty($invoice_dated) && !empty($wh_id)){
            $parts_details = $this->input->post('part');
            if(!empty($parts_details)){
                $data['invoice_id_in'] = $invoice_id;
                $data['inventory_id_in_date'] = $invoice_dated;
                $data['create_date'] = date('Y-m-d H:i:s');
                foreach ($parts_details as $value){
                    $data['quantity_in'] = $value['quantity'];
                    $data['inventory_id'] = $value['inventory_id'];
                    $data['booking_id'] = $value['booking_id'];
                    
                    //insert data into spare_invoice_mapping
                    $insert_id = $this->inventory_model->insert_data_in_spare_invoice_mapping($data);
                    
                    if($insert_id){
                        log_message("info", "Details added successfully");
                        
                        $ledger_data['receiver_entity_id'] = $wh_id;
                        $ledger_data['receiver_entity_type'] = _247AROUND_SF_STRING;
                        $ledger_data['sender_entity_id'] = $partner_id;
                        $ledger_data['sender_entity_type'] = _247AROUND_PARTNER_STRING;
                        $ledger_data['inventory_id'] = $value['inventory_id'];
                        $ledger_data['quantity'] = $value['quantity'];
                        $ledger_data['agent_id'] = $this->session->userdata('id');
                        $ledger_data['agent_type'] = _247AROUND_EMPLOYEE_STRING;
                        $ledger_data['booking_id'] = $value['booking_id'];
                        $ledger_data['invoice_id'] = $invoice_id;
                        $ledger_data['is_wh_ack'] = 0;
                        
                        $insert_id = $this->inventory_model->insert_inventory_ledger($ledger_data);
                        
                        if($insert_id){
                            log_message("info", "Ledger details added successfully");
                        }else{
                            log_message("info","error in adding inventory ledger details data: ". print_r($ledger_data));
                        }
                        
                    }else{
                        log_message("info","error in adding details data: ". print_r($data));
                    }
                }
                $res['status'] = TRUE;
                $res['message'] = 'Details updated successfully';
            }else{
                $res['status'] = false;
                $res['message'] = 'Please select parts details';
            }
        }else{
            $res['status'] = false;
            $res['message'] = 'All fields are requried';
        }
        
        echo json_encode($res);
    }
    
    
    /**
     *  @desc : This function is used to show all the spare list which was send by partner to warehouse and not acknowledge by warehouse
     *  @param : void
     *  @return : $res JSON
     */
    function get_spare_send_by_partner_to_wh(){
        $post = $this->get_post_data();
        $post['column_order'] = array();
        $post['column_search'] = array('part_name','model_number','type');
        $post['where'] = array('i.receiver_entity_id'=>trim($this->input->post('receiver_entity_id')),
                               'i.receiver_entity_type' => trim($this->input->post('receiver_entity_type')),
                               'i.sender_entity_id'=>trim($this->input->post('sender_entity_id')),
                               'i.sender_entity_type' => trim($this->input->post('sender_entity_type')),
                               'i.is_ack <> 1' => NULL);
        
        $select = "services.services,inventory_master_list.*,CASE WHEN(sc.name IS NOT NULL) THEN (sc.name) 
                    WHEN(p.public_name IS NOT NULL) THEN (p.public_name) 
                    WHEN (e.full_name IS NOT NULL) THEN (e.full_name) END as receiver, 
                    CASE WHEN(sc1.name IS NOT NULL) THEN (sc1.name) 
                    WHEN(p1.public_name IS NOT NULL) THEN (p1.public_name) 
                    WHEN (e1.full_name IS NOT NULL) THEN (e1.full_name) END as sender,i.*";
        $list = $this->inventory_model->get_spare_need_to_acknowledge($post,$select);
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
            "recordsFiltered" =>  $this->inventory_model->count_filtered_spare_need_to_acknowledge($post),
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
    function get_spare_send_by_partner_to_wh_table($inventory_list, $no){
        $row = array();
        
        $row[] = $no;
        $row[] = $inventory_list->services;
        $row[] = $inventory_list->model_number;
        $row[] = $inventory_list->type;
        $row[] = $inventory_list->part_name;
        $row[] = $inventory_list->quantity;
        $row[] = $row[] = "<input type='checkbox' class= 'check_single_row' id='ack_spare_$inventory_list->inventory_id' data-inventory_id='".$inventory_list->inventory_id."' data-quantity='".$inventory_list->quantity."' data-ledger_id = '".$inventory_list->id."'>";
        
        return $row;
    }
    
    /**
     *  @desc : This function is used to acknowledge data for the spare which send by partner to WH
     *  @param : void
     *  @return :$res JSON
     */
    function process_acknowledge_spare_send_by_partner_to_wh() {
        log_message("info", __METHOD__);

        $sender_entity_id = $this->input->post('sender_entity_id');
        $sender_entity_type = $this->input->post('sender_entity_type');
        $receiver_entity_id = $this->input->post('receiver_entity_id');
        $receiver_entity_type = $this->input->post('receiver_entity_type');
        $postData = json_decode($this->input->post('data'));

        if (!empty($sender_entity_id) && !empty($sender_entity_type) && !empty($receiver_entity_id) && !empty($receiver_entity_type) && !empty($postData)) {
            foreach ($postData as $value) {
                //acknowledge spare by setting is_ack flag = 1 in inventory ledger table
                $update = $this->inventory_model->update_ledger_details(array('is_wh_ack' => 1, 'wh_ack_date' => date('Y-m-d H:i:s')), array('id' => $value->ledger_id));
                if ($update) {
                    //update inventory stocks
                    $is_entity_exist = $is_entity_exist = $this->reusable_model->get_search_query('inventory_stocks', 'inventory_stocks.id', array('entity_id' => $receiver_entity_id, 'entity_type' => $receiver_entity_type, 'inventory_id' => $value->inventory_id), NULL, NULL, NULL, NULL, NULL)->result_array();
                    if (!empty($is_entity_exist)) {
                        $stock = "stock + '" . $value->quantity . "'";
                        $update_stocks = $this->inventory_model->update_inventory_stock(array('id' => $is_entity_exist[0]['id']), $stock);
                        if ($update_stocks) {
                            log_message("info", __FUNCTION__ . " Stocks has been updated successfully");
                            $flag = TRUE;
                        } else {
                            log_message("info", __FUNCTION__ . " Error in updating stocks");
                        }
                    } else {
                        $insert_data['entity_id'] = $receiver_entity_id;
                        $insert_data['entity_type'] = $receiver_entity_type;
                        $insert_data['inventory_id'] = $value->inventory_id;
                        $insert_data['stock'] = $value->quantity;
                        $insert_data['create_date'] = date('Y-m-d H:i:s');

                        $insert_id = $this->inventory_model->insert_inventory_stock($insert_data);
                        if (!empty($insert_id)) {
                            log_message("info", __FUNCTION__ . " Stocks has been inserted successfully" . print_r($insert_data, true));
                            $flag = TRUE;
                        } else {
                            log_message("info", __FUNCTION__ . " Error in inserting stocks" . print_r($insert_data, true));
                        }
                    }
                }
            }

            $res['status'] = TRUE;
            $res['message'] = 'Details updated successfully';
        } else {
            $res['status'] = false;
            $res['message'] = 'All fields are required';
        }

        echo json_encode($res);
    }
    
    /**
     *  @desc : This function is used to send defective spare by WH to partner
     *  @param : void
     *  @return :$res JSON
     */
    function send_defective_parts_to_partner_from_wh() {
        log_message("info", __METHOD__);

        $sender_entity_id = $this->input->post('sender_entity_id');
        $sender_entity_type = $this->input->post('sender_entity_type');
        $postData = json_decode($this->input->post('data'));

        if (!empty($sender_entity_id) && !empty($sender_entity_type) && !empty($postData)) {
            foreach ($postData as $value) {
                //acknowledge spare by setting is_ack flag = 1 in inventory ledger table
                if(!empty($value->inventory_id)){
                    $ledger_data['receiver_entity_id'] = $value->partner_id;
                    $ledger_data['receiver_entity_type'] = _247AROUND_PARTNER_STRING;
                    $ledger_data['sender_entity_id'] = $sender_entity_id;
                    $ledger_data['sender_entity_type'] = $sender_entity_type;
                    $ledger_data['inventory_id'] = $value->inventory_id;
                    $ledger_data['quantity'] = 1;
                    $ledger_data['agent_id'] = $this->session->userdata('service_center_id');
                    $ledger_data['agent_type'] = _247AROUND_SF_STRING;
                    $ledger_data['booking_id'] = $value->booking_id;
                    $ledger_data['is_defective'] = 1;

                    $insert_id = $this->inventory_model->insert_inventory_ledger($ledger_data);

                    if ($insert_id) {
                        log_message("info", "Ledger details added successfully");
                        $where = array('id' => $value->spare_id);
                        $data = array('status' => DEFECTIVE_PARTS_SEND_TO_PARTNER_BY_WH);
                        $update_spare_parts = $this->service_centers_model->update_spare_parts($where,$data);
                        if($update_spare_parts){
                            log_message("info", "Spare Status updated  successfully");
                            $agent_id = $this->session->userdata('service_center_agent_id');
                            $agent_name = $this->session->userdata('service_center_name');
                            $service_center_id =$this->session->userdata('service_center_id');
                            $actor = ACTOR_NOT_DEFINE;
                            $next_action = NEXT_ACTION_NOT_DEFINE;

                            $this->notify->insert_state_change($value->booking_id, DEFECTIVE_PARTS_SEND_TO_PARTNER_BY_WH, "", DEFECTIVE_PARTS_SEND_TO_PARTNER_BY_WH, $agent_id, $agent_name,$actor,$next_action, NULL, $service_center_id);
                        }else{
                            log_message("info", "Error in updating spare details");
                        }
                        
                    } else {
                        log_message("info", "error in adding inventory ledger details data: " . print_r($ledger_data));
                    }
                }
            }

            $res['status'] = TRUE;
            $res['message'] = 'Details updated successfully';
        } else {
            $res['status'] = false;
            $res['message'] = 'All fields are required';
        }

        echo json_encode($res);
    }
    
    
    /**
     *  @desc : This function is used to get data for the spare which send by WH to partner
     *  @param : void
     *  @return :$res JSON
     */
    function get_defective_spare_send_by_wh_to_partner(){
        $post = $this->get_post_data();
        $post['column_order'] = array();
        $post['column_search'] = array('part_name','model_number','type');
        $post['where'] = array('i.receiver_entity_id'=>trim($this->input->post('receiver_entity_id')),
                               'i.receiver_entity_type' => trim($this->input->post('receiver_entity_type')),
                               'i.is_defective' => 1,
                               '(i.is_partner_ack IS NULL OR i.is_partner_ack = 0)' => null);
        
        $select = "services.services,inventory_master_list.*,CASE WHEN(sc.name IS NOT NULL) THEN (sc.name) 
                    WHEN(p.public_name IS NOT NULL) THEN (p.public_name) 
                    WHEN (e.full_name IS NOT NULL) THEN (e.full_name) END as receiver, 
                    CASE WHEN(sc1.name IS NOT NULL) THEN (sc1.name) 
                    WHEN(p1.public_name IS NOT NULL) THEN (p1.public_name) 
                    WHEN (e1.full_name IS NOT NULL) THEN (e1.full_name) END as sender,i.*";
        $list = $this->inventory_model->get_spare_need_to_acknowledge($post,$select);
        $data = array();
        $no = $post['start'];
        foreach ($list as $inventory_list) {
            $no++;
            $row = $this->get_spare_send_by_wh_to_partner_table($inventory_list, $no);
            $data[] = $row;
        }
        
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->inventory_model->count_spare_need_to_acknowledge($post),
            "recordsFiltered" =>  $this->inventory_model->count_filtered_spare_need_to_acknowledge($post),
            "data" => $data,
        );
        
        echo json_encode($output);
    }
    
    /**
     *  @desc : This function is used to generate data for the spare which send by WH to partner
     *  @param : void
     *  @return :$res JSON
     */
    function get_spare_send_by_wh_to_partner_table($inventory_list, $no){
        $row = array();
        
        $row[] = $no;
        $row[] = $inventory_list->services;
        $row[] = $inventory_list->model_number;
        $row[] = $inventory_list->type;
        $row[] = $inventory_list->part_name;
        $row[] = $inventory_list->quantity;
        $row[] = $row[] = "<input type='checkbox' class= 'check_single_row' id='ack_spare_$inventory_list->inventory_id' data-inventory_id='".$inventory_list->inventory_id."' data-ledger_id = '".$inventory_list->id."' data-sender_entity_id = '".$inventory_list->sender_entity_id."' data-sender_entity_type = '".$inventory_list->sender_entity_type."'>";
        
        return $row;
    }
    
    /**
     *  @desc : This function is used to acknowledge defective parts received by partner which send from WH
     *  @param : void
     *  @return :$res JSON
     */
    function process_ack_spare_send_by_wh() {
        log_message("info", __METHOD__);

        $receiver_entity_id = $this->input->post('receiver_entity_id');
        $receiver_entity_type = $this->input->post('receiver_entity_type');
        $postData = json_decode($this->input->post('data'));

        if (!empty($receiver_entity_id) && !empty($receiver_entity_type) && !empty($postData)) {
            foreach ($postData as $value) {
                //acknowledge spare by setting is_ack flag = 1 in inventory ledger table
                $update = $this->inventory_model->update_ledger_details(array('is_partner_ack' => 1, 'partner_ack_date' => date('Y-m-d H:i:s')), array('id' => $value->ledger_id));
                if (!empty($update)) {
                    log_message("info", __FUNCTION__ . " Details updated successfully");
                    $agent_id = $this->session->userdata('agent_id');
                    $agent_name = $this->session->userdata('partner_name');
                    $partner_id = $this->session->userdata('partner_id');
                    $actor = ACTOR_NOT_DEFINE;
                    $next_action = NEXT_ACTION_NOT_DEFINE;

                    $this->notify->insert_state_change($value->booking_id, PARTNER_ACK_DEFECTIVE_PARTS_SEND_BY_WH, "", PARTNER_ACK_DEFECTIVE_PARTS_SEND_BY_WH, $agent_id, $agent_name, $actor, $next_action, $partner_id);
                } else {
                    log_message("info", __FUNCTION__ . " Error in inserting stocks");
                }
            }

            $res['status'] = TRUE;
            $res['message'] = 'Details updated successfully';
        } else {
            $res['status'] = false;
            $res['message'] = 'All fields are required';
        }

        echo json_encode($res);
    }

}
