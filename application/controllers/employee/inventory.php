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
        $this->load->model('invoices_model');
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->library('PHPReport');
        $this->load->library('notify');
        $this->load->library('S3');
        $this->load->library("pagination");
        $this->load->library("miscelleneous");
        $this->load->library('booking_utilities');
        $this->load->library('invoice_lib');
        $this->load->library('table');
        $this->load->library('warranty_utilities');
    }

    public function index() {
        
    }

    /**
     * @desc: This function is used to get bracket add form
     * @params: void
     * @return: view
     * 
     */
    public function get_bracket_add_form($sf_id = null, $sf_name = null) {
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
            foreach ($data['choice'] as $key => $value) {

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
                    $this->notify->insert_state_change($data_post['order_id'], _247AROUND_BRACKETS_PENDING, _247AROUND_BRACKETS_REQUESTED, "Brackets Requested", $this->session->userdata('id'), $this->session->userdata('employee_id'), ACTOR_BRACKET_RECEIEVED_CONFORMATION, NEXT_ACTION_BRACKET_RECEIEVED_CONFORMATION, _247AROUND);
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

                        $this->notify->sendEmail($template[2], $to, $template[3] . ',' . $this->get_rm_email($data_post['order_received_from']), '', $subject, $emailBody, "", 'brackets_order_received_from_vendor');
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

                        $this->notify->sendEmail($template1[2], $to, $template1[3], '', $subject, $emailBody, "", 'brackets_requested_from_vendor');
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
        } else {
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
    function show_brackets_list($page = 0, $offset = '0') {
        $sf_list = "";
        //Getting ID of logged in user
        $id = $this->session->userdata('id');
        //Getting employee relation if present
        if($this->session->userdata('user_group') == _247AROUND_RM || $this->session->userdata('user_group') == _247AROUND_ASM){
            $sf_list_array = $this->vendor_model->get_employee_relation($id);
            if (!empty($sf_list_array)) {
                $sf_list = $sf_list_array[0]['service_centres_id'];
            }
        }
        if ($page == 0) {
            $page = 50;
        }
        // $offset = ($this->uri->segment(5) != '' ? $this->uri->segment(5) : 0);

        $config['base_url'] = base_url() . 'employee/inventory/show_brackets_list/' . $page;
        $config['total_rows'] = $this->inventory_model->get_total_brackets_count($sf_list);

        if ($offset != "All") {
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
        $data['brackets'] = $this->inventory_model->get_brackets($config['per_page'], $offset, $sf_list);
        //Getting name for order received from  to vendor
        foreach ($data['brackets'] as $key => $value) {
            $data['order_received_from'][$key] = $this->vendor_model->getVendorContact($value['order_received_from'])[0];

            // Getting name for order given to vendor

            $data['order_given_to'][$key] = $this->vendor_model->getVendorContact($value['order_given_to'])[0]['name'];
        }
        $this->miscelleneous->load_nav_header();
        $this->load->view("employee/show_brackets_list", $data);
    }

    function show_brackets_list_on_tab() {
        log_message('info', __FUNCTION__ . "Entering... ");
        $this->checkUserSession();

        $this->load->view("employee/multi_categories_show_brackets_list");
    }

    /**
     * @Desc: This function is used to update shipment
     * @params: Int order id
     * @return : view
     */
    function get_update_shipment_form($order_id) {
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
    function process_update_shipment_form() {
        //Saving Uploading file.

        if (($_FILES['shipment_receipt']['error'] != 4) && !empty($_FILES['shipment_receipt']['tmp_name'])) {
            $tmpFile = $_FILES['shipment_receipt']['tmp_name'];
            //Assigning File Name for uploaded shipment receipt
            $fileName = "Shipment-Receipt-" . $this->input->post('order_id') . '.' . explode('.', $_FILES['shipment_receipt']['name'])[1];
            move_uploaded_file($tmpFile, TMP_FOLDER . $fileName);

            //Uploading images to S3 
            $bucket = BITBUCKET_DIRECTORY;
            $directory = "misc-images/" . $fileName;
            $this->s3->putObjectFile(TMP_FOLDER . $fileName, $bucket, $directory, S3::ACL_PUBLIC_READ);

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
        $data['shipment_date'] = !empty($this->input->post('shipment_date')) ? $this->input->post('shipment_date') : date('Y-m-d H:i:s');
        $data['is_shipped'] = 1;


        $attachment = "";
        if (!empty($fileName)) {
            $data['shipment_receipt'] = $fileName;
            $attachment = TMP_FOLDER . $fileName;
        }

        //Updating value in Brackets
        $update_brackets = $this->inventory_model->update_brackets($data, array('order_id' => $order_id));
        if ($update_brackets) {
            //Loggin success
            log_message('info', __FUNCTION__ . ' Brackets Shipped has been updated ' . print_r($data, TRUE));

            //Adding value in Booking State Change
            $this->notify->insert_state_change($order_id, _247AROUND_BRACKETS_SHIPPED, _247AROUND_BRACKETS_PENDING, "Brackets Shipped", $this->session->userdata('id'), $this->session->userdata('employee_id'), ACTOR_NOT_DEFINE, NEXT_ACTION_NOT_DEFINE, _247AROUND);
            //Logging Success
            log_message('info', __FUNCTION__ . ' Brackets Pending - Shipped state have been added in Booking State Change ');

            // Sending mail to order_received_from vendor
            $order_received_from_email = $this->vendor_model->getVendorContact($order_received_from);
            $vendor_poc_mail = $order_received_from_email[0]['primary_contact_email'];
            $vendor_owner_mail = $order_received_from_email[0]['owner_email'];
            $to = $vendor_poc_mail . ',' . $vendor_owner_mail;

            // Sending brackets Shipped Mail to order received from vendor
            $email = array();
            //Getting template from Database
            $template = $this->booking_model->get_booking_email_template("brackets_shipment_mail");

            if (!empty($template)) {
                $email['order_id'] = $order_id;
                $subject = vsprintf($template[4], $order_received_from_email[0]['company_name']);
                $emailBody = vsprintf($template[0], $email);

                $this->notify->sendEmail($template[2], $to, $template[3] . ',' . $this->get_rm_email($order_received_from), '', $subject, $emailBody, $attachment, 'brackets_shipment_mail');

                //Loggin send mail success
                log_message('info', __FUNCTION__ . ' Shipped mail has been sent to order_received_from vendor ' . $emailBody);
            }

            //2. Sending mail to order_given_to vendor
            $order_given_to_email_to = $this->vendor_model->getVendorContact($order_given_to);
            $to = $order_given_to_email_to[0]['primary_contact_email'] . ',' . $order_given_to_email_to[0]['owner_email'];
            $order_given_to_email = array();
            //Getting template from Database
            $template1 = $this->booking_model->get_booking_email_template("brackets_shipment_mail_to_order_given_to");

            if (!empty($template)) {
                $order_given_to_email['order_recieved_from'] = $order_received_from_email[0]['company_name'];
                $order_given_to_email['order_id'] = $order_id;
                $subject = vsprintf($template1[4], $order_received_from_email[0]['company_name']);
                $emailBody = vsprintf($template1[0], $order_given_to_email);

                $this->notify->sendEmail($template1[2], $to, $template1[3], '', $subject, $emailBody, '', 'brackets_shipment_mail_to_order_given_to');

                //Loggin send mail success
                log_message('info', __FUNCTION__ . ' Shipped mail has been sent to order_given_to vendor ' . $emailBody);
            }

            //Setting success session data 
            $this->session->set_userdata('brackets_update_success', 'Brackets Shipped updated Successfully');

            redirect(base_url() . 'employee/inventory/show_brackets_list');
        } else {
            //Loggin error
            log_message('info', __FUNCTION__ . ' Brackets Shipped updated Error ' . print_r($data, TRUE));

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
    function get_update_receiving_form($order_id) {
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
            $this->notify->insert_state_change($order_id, _247AROUND_BRACKETS_RECEIVED, _247AROUND_BRACKETS_SHIPPED, "", $this->session->userdata('id'), $this->session->userdata('employee_id'), ACTOR_BRACKET_RECEIEVED_CONFORMATION, NEXT_ACTION_BRACKET_RECEIEVED_CONFORMATION, _247AROUND);
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
                $this->notify->sendEmail($template[2], $order_received_from_email_to, $template[3] . ',' . $this->get_rm_email($order_received_from), '', $subject, $emailBody, '', 'brackets_received_mail_vendor_order_requested_from');
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

                $this->notify->sendEmail($template[2], $order_given_to_email_to, $template[3], '', $subject, $emailBody, '', 'brackets_received_mail_vendor_order_given_to');
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
    function get_vendor_inventory_list_form() {
        $sf_list = "";
        //Getting ID of logged in user
        $id = $this->session->userdata('id');
        //Getting employee relation if present
        $sf_list_array = $this->vendor_model->get_employee_relation($id);
        if (!empty($sf_list_array)) {
            $sf_list = $sf_list_array[0]['service_centres_id'];
        }
        $data['distinct_vendor'] = $this->inventory_model->get_distict_vendor_from_inventory($sf_list);
        foreach ($data['distinct_vendor'] as $value) {
            $data['vendor_inventory'][] = $this->inventory_model->get_vendor_inventory_details($value['vendor_id']);
        }
        //Getting latest updated values of vendor
        foreach ($data['vendor_inventory'] as $key => $value) {
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
    function show_brackets_order_history($order_id) {
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
    function get_update_requested_form($order_id) {
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
    function process_update_requested_form() {

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
        if ($update_brackets) {
            //Loggin success
            log_message('info', __FUNCTION__ . ' Brackets Requested has been updated ' . print_r($data, TRUE));

            //Adding value in Booking State Change
            $this->notify->insert_state_change($order_id, _247AROUND_BRACKETS_PENDING, _247AROUND_BRACKETS_PENDING, "Brackets Shipped", $this->session->userdata('id'), $this->session->userdata('employee_id'), ACTOR_BRACKET_REQUESTED, NEXT_ACTION_BRACKET_REQUESTED, _247AROUND);
            //Logging Success
            log_message('info', __FUNCTION__ . ' Brackets Pending - Pending state have been added in Booking State Change ');

            // Sending mail to order_received_from vendor
            $order_received_from_email = $this->vendor_model->getVendorContact($order_received_from);
            $vendor_poc_mail = $order_received_from_email[0]['primary_contact_email'];
            $vendor_owner_mail = $order_received_from_email[0]['owner_email'];
            $to = $vendor_poc_mail . ',' . $vendor_owner_mail;

            // Sending updated brackets confirmation details mail to Vendor using Template
            $email = array();
            //Getting template from Database
            $template = $this->booking_model->get_booking_email_template("brackets_order_received_from_vendor");
            if (!empty($template)) {
                $email['order_id'] = $order_id;
//                        $email['19_24_requested'] = $data['19_24_requested'];
                $email['26_32_requested'] = $data['26_32_requested'];
                $email['36_42_requested'] = $data['36_42_requested'];
//                        $email['43_requested'] = $data['43_requested'];
                $email['total_requested'] = $data['total_requested'];
                $subject = "Updated Brackets Requested by " . $order_received_from_email[0]['company_name'];
                $emailBody = vsprintf($template[0], $email);
                $this->notify->sendEmail($template[2], $to, $template[3] . ',' . $this->get_rm_email($order_received_from), '', $subject, $emailBody, "", 'brackets_order_received_from_vendor');
            }

            //Loggin send mail success
            log_message('info', __FUNCTION__ . ' Changed Requested mail has been sent to order_received_from vendor ' . $to);
            //Sending Mail to order given to
            $vendor_requested_to = $this->vendor_model->getVendorContact($order_given_to)[0];
            $vendor_poc_mail = $vendor_requested_to['primary_contact_email'];
            $vendor_owner_mail = $vendor_requested_to['owner_email'];
            $to = $vendor_poc_mail . ',' . $vendor_owner_mail;

            // Sending Login details mail to Vendor using Template
            $email = array();
            //Getting template from Database
            $template = $this->booking_model->get_booking_email_template("brackets_requested_from_vendor");

            if (!empty($template)) {
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
                $subject = "Updated Brackets Requested by " . $order_received_from_email[0]['company_name'];

                $emailBody = vsprintf($template[0], $email);
                $this->notify->sendEmail($template[2], $to, $template[3], '', $subject, $emailBody, "", 'brackets_requested_from_vendor');
                //Loggin send mail success
                log_message('info', __FUNCTION__ . ' Changed Requested mail has been sent to order_given_to vendor ' . $to);
            }

            //Setting success session data 
            $this->session->set_userdata('brackets_update_success', 'Brackets Requested updated Successfully');

            redirect(base_url() . 'employee/inventory/show_brackets_list');
        } else {
            //Loggin error
            log_message('info', __FUNCTION__ . ' Brackets Shipped updated Error ' . print_r($data, TRUE));

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
    function cancel_brackets_requested() {
        $order_id = $this->input->post('order_id');
        $data['active'] = 0;
        $data['cancellation_reason'] = $this->input->post('cancellation_reason');
        $cancel = $this->inventory_model->update_brackets($data, array('order_id' => $order_id));
        if ($cancel) {
            //Loggging
            log_message('info', __FUNCTION__ . ' Brackets Requested has been cancelled ' . print_r($cancel));
            //Getiting brackets details
            $brackets_details = $this->inventory_model->get_brackets_by_id($order_id);

            // Sending mail to order_received_from vendor
            $order_received_from_email = $this->vendor_model->getVendorContact($brackets_details[0]['order_received_from']);

            $vendor_poc_mail = $order_received_from_email[0]['primary_contact_email'];
            $vendor_owner_mail = $order_received_from_email[0]['owner_email'];
            $to = $vendor_poc_mail . ',' . $vendor_owner_mail;

            // Sending updated brackets confirmation details mail to Vendor using Template
            $email = array();
            //Getting template from Database
            $template = $this->booking_model->get_booking_email_template("cancel_brackets_order_received_from_vendor");
            if (!empty($template)) {
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
                $this->notify->sendEmail($template[2], $to, $template[3] . ',' . $this->get_rm_email($brackets_details[0]['order_received_from']), '', $subject, $emailBody, "", 'cancel_brackets_order_received_from_vendor');
                //Loggin send mail success
                log_message('info', __FUNCTION__ . ' Cancelled Brackets mail has been sent to order_received_from vendor ' . $to);
            }

            //Sending Mail to order given to
            $vendor_requested_to = $this->vendor_model->getVendorContact($brackets_details[0]['order_given_to']);
            $vendor_poc_mail = $vendor_requested_to[0]['primary_contact_email'];
            $vendor_owner_mail = $vendor_requested_to[0]['owner_email'];
            $to = $vendor_poc_mail . ',' . $vendor_owner_mail;

            // Sending Login details mail to Vendor using Template
            $email = array();
            //Getting template from Database
            $template = $this->booking_model->get_booking_email_template("cancel_brackets_requested_from_vendor");

            if (!empty($template)) {
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
                $this->notify->sendEmail($template[2], $to, $template[3], '', $subject, $emailBody, "", 'cancel_brackets_requested_from_vendor');
                //Loggin send mail success
                log_message('info', __FUNCTION__ . '  Cancelled Brackets mail has been sent to order_given_to vendor ' . $to);
            }

            //Setting success session data 
            $this->session->set_userdata('brackets_update_success', 'Brackets Requested has been Cancelled.');
        } else {
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
    function get_spare_parts() {
        log_message('info', __FUNCTION__ . "Entering... ");
        $this->checkUserSession();
        $data['courier_details'] = $this->inventory_model->get_courier_services('*');
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/get_spare_parts', $data);
    }

    /**
     * @desc: load to Spare parts booking by Admin Panel
     * @param type $booking_id
     */
    function update_spare_parts($id) {
        log_message('info', __FUNCTION__ . "Entering... And Booking_ID: " . $id);
        $this->checkUserSession();
        $where = "spare_parts_details.id = '" . $id . "' "
                . " AND booking_details.current_status IN ('" . _247AROUND_PENDING . "', '" . _247AROUND_RESCHEDULED . "', 'Completed', 'Cancelled') ";
        $data['bookinghistory'] = $this->partner_model->get_spare_parts_booking($where);

        if (!empty($data['bookinghistory'][0])) {
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/update_spare_parts', $data);
        } else {
            echo "Booking Not Found. Please Retry Again";
        }
    }

    /**
     * @desc: Process to update Spare parts booking by Admin Panel
     * @param type $booking_id
     */
    function process_update_booking($booking_id, $id) {
        log_message('info', __FUNCTION__ . "Entering... For Booking_id:" . $booking_id . " And Id: " . $id);
        $this->checkUserSession();
        if (!empty($booking_id) || !empty($id) || $id != 0) {
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

            if (($_FILES['invoice_image']['error'] != 4) && !empty($_FILES['invoice_image']['tmp_name'])) {
                $invoice_name = $this->upload_spare_pic($_FILES["invoice_image"], "Invoice");
                if (isset($invoice_name)) {
                    $data['invoice_pic'] = $invoice_name;
                }
            }

            if (($_FILES['serial_number_pic']['error'] != 4) && !empty($_FILES['serial_number_pic']['tmp_name'])) {

                $serial_number_pic = $this->upload_spare_pic($_FILES["serial_number_pic"], "Serial_NO");
                if (isset($serial_number_pic)) {
                    $data['serial_number_pic'] = $serial_number_pic;
                }
            }

            if (($_FILES['defective_parts_pic']['error'] != 4) && !empty($_FILES['defective_parts_pic']['tmp_name'])) {

                $defective_parts_pic = $this->upload_spare_pic($_FILES["defective_parts_pic"], "Defective_Parts");
                if (isset($defective_parts_pic)) {
                    $data['defective_parts_pic'] = $defective_parts_pic;
                }
            }

            $where = array('id' => $id);
            $status_spare = $this->service_centers_model->update_spare_parts($where, $data);
            if ($status_spare) {
                log_message('info', __FUNCTION__ . " Spare Parts Booking is updated");
                if ($data['status'] == SPARE_PARTS_REQUESTED) {
                    log_message('info', __FUNCTION__ . " Change Current Status in Service Center Action table");
                    $sc_data['current_status'] = "InProcess";
                    $sc_data['internal_status'] = $data['status'];
                    $sc_data['update_date'] = date("Y-m-d H:i:s");

                    $this->vendor_model->update_service_center_action($booking_id, $sc_data);
                }

                $this->notify->insert_state_change($booking_id, $data['status'], "", "Spare Parts Updated By " . $this->session->userdata('employee_id'), $this->session->userdata('id'), $this->session->userdata('employee_id'), ACTOR_NOT_DEFINE, NEXT_ACTION_NOT_DEFINE, _247AROUND);
            } else {
                log_message('info', __FUNCTION__ . " Spare Parts Booking is not updated");
            }

            redirect(base_url() . "employee/inventory/update_spare_parts/" . $booking_id);
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
        log_message('info', __FUNCTION__ . " Enterring Service_center ID: " . $this->session->userdata('service_center_id'));
        $this->checkUserSession();
        $allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
        $temp = explode(".", $file['name']);
        $extension = end($temp);
        //$filename = prev($temp);

        if ($file["name"] != null) {
            if (($file["size"] < 5e+6) && in_array($extension, $allowedExts)) {
                if ($file["error"] > 0) {
                    $this->form_validation->set_message('upload_spare_pic', $file["error"]);
                } else {
                    $pic = str_replace(' ', '-', $this->input->post('booking_id'));
                    $picName = $type . rand(10, 100) . $pic . "." . $extension;
                    $bucket = BITBUCKET_DIRECTORY;

                    $directory = "misc-images/" . $picName;
                    $this->s3->putObjectFile($file["tmp_name"], $bucket, $directory, S3::ACL_PUBLIC_READ);

                    return $picName;
                }
            } else {
                $this->form_validation->set_message('upload_spare_pic', 'File size or file type is not supported. Allowed extentions are "png", "jpg", "jpeg" and "pdf". '
                        . 'Maximum file size is 5 MB.');
                return FALSE;
            }
        }
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
     * @Desc: This function is used to Uncancel Brackets Request
     * @params: Order ID
     * @return: void
     * 
     */
    function uncancel_brackets_request($order_id) {
        log_message('info', __FUNCTION__ . ' for order_id: ' . $order_id);

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
                $this->notify->sendEmail($template_to[2], $to, $template_to[3] . ',' . $this->get_rm_email($brackets_details[0]['order_received_from']), '', $subject, $emailBody, "", 'un-cancel_brackets_order_received_from_vendor');
                //Loggin send mail success
                log_message('info', __FUNCTION__ . ' Un-Cancelled Brackets mail has been sent to order_received_from vendor ' . print_r($emailBody, TRUE));
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
                $this->notify->sendEmail($template_from[2], $to, $template_from[3], '', $subject, $emailBody, "", 'un-cancel_brackets_requested_from_vendor');
                //Loggin send mail success
                log_message('info', __FUNCTION__ . '  Cancelled Brackets mail has been sent to order_given_to vendor ' . print_r($emailBody, TRUE));
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

    function spare_part_booking_on_tab() {
        log_message('info', __FUNCTION__ . "Entering... ");
        $this->checkUserSession();

        $data['partner_id'] = $this->input->post('partner_id');        
        $data['services'] = $this->booking_model->selectservice();		
        $data['partners'] = $this->partner_model->getpartner();
        $this->load->view('employee/sparepart_on_tab', $data);
    }

    /**
     * @desc this used to cancel Spare Part 
     * @param int $id
     * @param String $booking_id
     */
    function update_action_on_spare_parts($id, $booking_id, $requestType) {
        ob_end_clean();
        log_message('info', __FUNCTION__ . "Entering... id " . $id . " Booking ID " . $booking_id);
        if (!($this->session->userdata('partner_id') || $this->session->userdata('service_center_id'))) {
            $this->checkUserSession();
        }

        if (!empty($id)) {

            // fetch record from booking details of $booking_id.
            $booking_details = $this->booking_model->get_booking_details('*',['booking_id' => $booking_id])[0];
            $remarks = $this->input->post("remarks");
            if (!empty($this->input->post("spare_cancel_reason"))) {
                $remarks = $this->input->post("spare_cancel_reason") . " , " . $remarks;
            }
            $flag = true;
            $b = array();
            $line_items = '';
            
            $select = 'spare_parts_details.id,spare_parts_details.entity_type,booking_details.partner_id as booking_partner_id, spare_parts_details.status, spare_parts_details.inventory_invoice_on_booking, spare_parts_details.wh_ack_received_part';

            $spare_parts_details = $this->partner_model->get_spare_parts_by_any($select, array('spare_parts_details.booking_id' => $booking_id, 'status IN ("' . SPARE_PARTS_SHIPPED . '", "'
                . SPARE_PARTS_REQUESTED . '", "' . SPARE_PART_ON_APPROVAL . '", "' . SPARE_OOW_EST_REQUESTED . '", "' . SPARE_PARTS_SHIPPED_BY_WAREHOUSE . '", "' . SPARE_DELIVERED_TO_SF . '", "'.DEFECTIVE_PARTS_PENDING.'", "'.OK_PART_TO_BE_SHIPPED.'", "'.OK_PARTS_SHIPPED.'", "'.DEFECTIVE_PARTS_SHIPPED.'", "'.DEFECTIVE_PARTS_RECEIVED_BY_WAREHOUSE.'","'.DEFECTIVE_PARTS_REJECTED.'", "'.DEFECTIVE_PARTS_RECEIVED.'", "'.DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE.'") ' => NULL), TRUE, false, false);

            $line_items = count($spare_parts_details);
            
            switch ($requestType) {
                case 'CANCEL_PARTS':
                case 'QUOTE_REQUEST_REJECTED';
                case 'DELIVERED_PART_CANCELLED'; 
                    if (!empty($this->input->post("spare_cancel_id"))) {
                        $data['spare_cancellation_reason'] = $this->input->post("spare_cancel_id");
                    }
                    $where = array('id' => $id);
                    $track_status = $data['status'] = _247AROUND_CANCELLED;
                    $data['spare_cancelled_date'] = date("Y-m-d h:i:s");
                    $status_string = array();
                    if ($line_items >= 2) {
                        foreach ($spare_parts_details as $key => $value) {
                            if ($value['id'] !== $id) {
                                $status_string[$key] = $value['status'];
                            }
                        }
                        if (in_array(SPARE_PART_ON_APPROVAL, $status_string)) {
                            $booking_new_internal_status = SPARE_PART_ON_APPROVAL;
                        } else if (in_array(SPARE_OOW_EST_REQUESTED, $status_string)) {
                            $booking_new_internal_status = SPARE_OOW_EST_REQUESTED;
                        } else if (in_array(SPARE_PARTS_REQUESTED, $status_string)) {
                            foreach ($spare_parts_details as $key => $value) {
                                if ($value['id'] !== $id) {
                                    if ($value['status'] == SPARE_PARTS_REQUESTED && $value['inventory_invoice_on_booking'] == 0) {
                                        $booking_new_internal_status = SPARE_PARTS_REQUESTED;
                                        //If spare is requested and not shipped by partner
                                        break;
                                    }

                                    if ($value['status'] == SPARE_PARTS_REQUESTED && $value['inventory_invoice_on_booking'] == 1 && $value['wh_ack_received_part'] == 0) {
                                        $booking_new_internal_status = SPARE_SHIPPED_TO_WAREHOUSE;
                                        //If spare part is requested and shipped by partner and not acknowledgeby warehouse
                                        break;
                                    }
                                    if ($value['status'] == SPARE_PARTS_REQUESTED && $value['inventory_invoice_on_booking'] == 1 && $value['wh_ack_received_part'] == 1) {
                                        $booking_new_internal_status = WAREHOUSE_ACKNOWLEDGED_TO_RECEIVE_PARTS;
                                        //If spare part is requested and shipped by partner and  acknowledgeby warehouse
                                        break;
                                    }
                                }
                            }
                        } else if (in_array(SPARE_PARTS_SHIPPED, $status_string)) {
                            $booking_new_internal_status = SPARE_PARTS_SHIPPED;
                        } else if (in_array(SPARE_PARTS_SHIPPED_BY_WAREHOUSE, $status_string)) {
                            $booking_new_internal_status = SPARE_PARTS_SHIPPED_BY_WAREHOUSE;
                        } else {
                            if (!empty($booking_details['service_center_closed_date'])) {
                                if (in_array(DEFECTIVE_PARTS_PENDING, $status_string)) {
                                    $booking_new_internal_status = DEFECTIVE_PARTS_PENDING;
                                } else if (in_array(OK_PART_TO_BE_SHIPPED, $status_string)) {
                                    $booking_new_internal_status = OK_PART_TO_BE_SHIPPED;
                                } else if (in_array(DEFECTIVE_PARTS_SHIPPED, $status_string)) {
                                    $booking_new_internal_status = DEFECTIVE_PARTS_SHIPPED;
                                } else if (in_array(OK_PARTS_SHIPPED, $status_string)) {
                                    $booking_new_internal_status = OK_PARTS_SHIPPED;
                                } else if (in_array(DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE, $status_string)) {
                                    $booking_new_internal_status = DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE;
                                } else if (in_array(OK_PARTS_REJECTED_BY_WAREHOUSE, $status_string)) {
                                    $booking_new_internal_status = OK_PARTS_REJECTED_BY_WAREHOUSE;
                                } else if (in_array(DEFECTIVE_PARTS_RECEIVED_BY_WAREHOUSE, $status_string)) {
                                    $booking_new_internal_status = DEFECTIVE_PARTS_RECEIVED_BY_WAREHOUSE;
                                } else if (in_array(OK_PARTS_RECEIVED_BY_WAREHOUSE, $status_string)) {
                                    $booking_new_internal_status = OK_PARTS_RECEIVED_BY_WAREHOUSE;
                                } else if (in_array(DEFECTIVE_PARTS_REJECTED, $status_string)) {
                                    $booking_new_internal_status = DEFECTIVE_PARTS_REJECTED;
                                } else if (in_array(OK_PARTS_REJECTED, $status_string)) {
                                    $booking_new_internal_status = OK_PARTS_REJECTED;
                                } else if (in_array(DEFECTIVE_PARTS_RECEIVED, $status_string)) {
                                    $booking_new_internal_status = DEFECTIVE_PARTS_RECEIVED;
                                } else if (in_array(OK_PARTS_RECEIVED, $status_string)) {
                                    $booking_new_internal_status = OK_PARTS_RECEIVED;
                                }
                                //If booking is completed then move Booking Status & actor according to spare part status on priority
                            } else {
                                $booking_new_internal_status = _247AROUND_PENDING;
                                //Booking is either Pending or Reschedule, now update internal status as pending and assign actor action accordingly
                            }
                        }
                    }
                    

                    //////   Handle agents for cancellation /// Abhishek
                    $approval_agent_id = _247AROUND_DEFAULT_AGENT;
                    $approval_entity_type = _247AROUND_SF_STRING; 
                    if($this->session->userdata('emp_name') && $this->session->userdata('userType')!='partner'){
                    $agent_name = $this->session->userdata('emp_name');
                    $agent_id   = $this->session->userdata('id');
                    $approval_agent_id = $agent_id;
                    $approval_entity_type = _247AROUND_SF_STRING;  
                    } else if($this->session->userdata('userType')=='partner'){ //// Partner Session //// abhishek
                    $agent_name = $this->session->userdata('partner_name');
                    $agent_id   = $this->session->userdata('agent_id');  // the agent id must go not patner id // abhishek
                    $approval_agent_id = $this->session->userdata('agent_id');
                    $approval_entity_type = _247AROUND_PARTNER_STRING; 
                    
                    }else{
                    $agent_id = _247AROUND_DEFAULT_AGENT;
                    $agent_name = _247AROUND_DEFAULT_AGENT_NAME;   /// name must to instead of id // abhishek
                    }
                    
                    $data['approval_agent_id'] = $approval_agent_id;     /// the data is to be set by only one case and in this case array name is data // abhishek
                    $data['approval_entity_type'] = $approval_entity_type;

                    if ($requestType == "CANCEL_PARTS") {
                        if ($line_items < 2) {
                            $b['internal_status'] = SPARE_PARTS_CANCELLED;
                        }
                       $track_status =  $new_state = SPARE_PARTS_CANCELLED;
                        $data['old_status'] = SPARE_PARTS_REQUESTED;
                    } else {
                        if ($line_items < 2) {
                            $b['internal_status'] = REQUESTED_QUOTE_REJECTED;
                        }
                        $track_status = $new_state = REQUESTED_QUOTE_REJECTED;
                        $data['old_status'] = SPARE_PARTS_REQUESTED;
                    }
                    
                    if($requestType == 'CANCEL_PARTS'){
                        $old_state = SPARE_PARTS_REQUESTED;
                    }
                    else if($requestType == 'QUOTE_REQUEST_REJECTED'){
                        $old_state = SPARE_OOW_EST_REQUESTED;
                    }
                    else if($requestType == 'DELIVERED_PART_CANCELLED'){
                        $old_state = SPARE_DELIVERED_TO_SF;
                       $track_status = $new_state = REMOVE_PART_CONSUMPTION;
                    }
                    
                    $sc_data['current_status'] = _247AROUND_PENDING;
                    $sc_data['internal_status'] = _247AROUND_PENDING;
                    $sc_data['update_date'] = date("Y-m-d H:i:s");
                    $sc_data['admin_remarks'] = $remarks;
                    
                    if ($line_items < 2) {
                        $this->vendor_model->update_service_center_action($booking_id, $sc_data);
                    }
                    /*@des: spare cancelled on spare chnage actor and action  */
                    if ($requestType == 'CANCEL_PARTS' || $requestType == 'QUOTE_REQUEST_REJECTED') {
                        if (count($spare_parts_details) == 1) {
                            $partnerId = _247AROUND;
                            $current_status = _247AROUND_PENDING;
                            $internal_status = SPARE_PARTS_CANCELLED;
                            $partner_status = $this->booking_model->get_partner_status($partnerId, $current_status, $internal_status);
                            if (!empty($partner_status)) {
                                $this->booking_model->update_booking($booking_id, array("actor" => $partner_status[0]['actor'], "next_action" => $partner_status[0]['next_action']));
                            }
                        }
                    }
        
                    break;
                case 'CANCEL_COMPLETED_BOOKING_PARTS':
                    $where = array('id' => $id);
                    $data = array('status' => _247AROUND_CANCELLED, "spare_cancelled_date" => date("Y-m-d h:i:s"));
                    $track_status = $new_state = SPARE_PARTS_CANCELLED;
                    $old_state = SPARE_PARTS_REQUESTED;
                    $sc_data['current_status'] = "InProcess";
                    $sc_data['internal_status'] = _247AROUND_COMPLETED;
                    $sc_data['update_date'] = date("Y-m-d H:i:s");

                    $this->vendor_model->update_service_center_action($booking_id, $sc_data);
                    break;

                case 'REJECT_COURIER_INVOICE':
                    /**
                     * handle defective/ok part.
                     * @modifiedBy Ankit Rajvanshi
                     */
                    $where = array('id' => $id);
                    $spare_part_detail = $this->reusable_model->get_search_result_data('spare_parts_details', '*', $where, NULL, NULL, NULL, NULL, NULL)[0];                    
                    $is_spare_consumed = $this->reusable_model->get_search_result_data('spare_consumption_status', '*', ['id' => $spare_part_detail['consumed_part_status_id']], NULL, NULL, NULL, NULL, NULL)[0]['is_consumed'];
                    $spare_status = DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE;
                    $old_state = DEFECTIVE_PARTS_SHIPPED;
                    if(!empty($is_spare_consumed) && $is_spare_consumed == 1) {
                        $spare_status = DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE;
                        $old_state = DEFECTIVE_PARTS_SHIPPED;
                    } else {
                        $spare_status = OK_PARTS_REJECTED_BY_WAREHOUSE;
                        $old_state = OK_PARTS_SHIPPED;
                    }                    
                    
                    $data = array("approved_defective_parts_by_admin" => 0, 'status' => $spare_status,'remarks_defective_part_by_sf' => $remarks);
                    $track_status = $new_state = "Courier Invoice Rejected By Admin";
                    break;
                case 'APPROVE_COURIER_INVOICE':

                    $data['approved_defective_parts_by_admin'] = 1;
                    $courier_charge = $this->input->post("courier_charge");
                    if (!empty($courier_charge)) {
                        $data['courier_charges_by_sf'] = $courier_charge;
                    } else {
                        $data['courier_charges_by_sf'] = 0;
                    }
                    $this->service_centers_model->update_spare_parts(array("id" => $id), $data);
                    
                    $track_status = $new_state = "Courier Invoice Approved By Admin";
                    $old_state = DEFECTIVE_PARTS_SHIPPED;
                    $flag = FALSE;
                    break;

                case 'DEFECTIVE_PARTS_SHIPPED_BY_SF':
                    $where = array('id' => $id);
                    $data = array('status' => DEFECTIVE_PARTS_SHIPPED);

                    $old_state = DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE;
                    $track_status = $new_state = DEFECTIVE_PARTS_SHIPPED;
                    if($booking_details['current_status'] == _247AROUND_COMPLETED) {
                        $b['internal_status'] = DEFECTIVE_PARTS_SHIPPED;
                    }
                    break;

                CASE 'NOT_REQUIRED_PARTS':
                    $data['defective_part_required'] = 0;
                    $where = array('id' => $id);
                    $track_status = $new_state = "Spare Parts Not Required To Warehouse";
                    if ($this->session->userdata('partner_id')) {
                        $track_status = $new_state = "Spare Parts Not Required To Partner";
                    }
                    $old_state = SPARE_PARTS_REQUESTED;
                    
                    /**
                     * If defective part not required after booking completion then change spare status accordingly.
                     * @modifiedBy Ankit Rajvanshi
                     */
                    $spare_part_detail = $this->reusable_model->get_search_result_data('spare_parts_details', '*', $where, NULL, NULL, NULL, NULL, NULL)[0];                    
                    if(!empty($spare_part_detail['consumed_part_status_id'])) {
                        $data['status'] = _247AROUND_COMPLETED;
                        if($booking_details['current_status'] == _247AROUND_COMPLETED && $line_items < 2) {
                            $b['internal_status'] = $data['status'];
                        }
                    }
                    
                    break;

                CASE 'NOT_REQUIRED_PARTS_FOR_COMPLETED_BOOKING':
                    
                    $where = array('id' => $id);
                    $spare_part_detail = $this->reusable_model->get_search_result_data('spare_parts_details', '*', $where, NULL, NULL, NULL, NULL, NULL)[0];                    
                    
                    $track_status =  $new_state = "Spare Parts Not Required To Warehouse";
                    $old_state = $spare_part_detail['status'];
                    $data['status'] = _247AROUND_COMPLETED;
                    $data['defective_part_required'] = 0;

                    if(!empty($booking_details['service_center_closed_date'])) {
                        /* change booking internal status if completed by admin. */
                        if($booking_details['current_status'] == _247AROUND_COMPLETED && $line_items < 2) {
                            $b['internal_status'] = $data['status'];
                        }
                    }
                    break;
                CASE 'REQUIRED_PARTS':
                    $data['defective_part_required'] = 1;
                    $where = array('id' => $id);
                    /**
                     * If defective part required after booking completion then change spare status accordingly.
                     * @modifiedBy Ankit Rajvanshi
                     */
//                    if(!empty($booking_details['service_center_closed_date'])) {
                        $spare_part_detail = $this->reusable_model->get_search_result_data('spare_parts_details', '*', $where, NULL, NULL, NULL, NULL, NULL)[0];                    
                        if(!empty($spare_part_detail['consumed_part_status_id'])) {
                            $is_spare_consumed = $this->reusable_model->get_search_result_data('spare_consumption_status', '*', ['id' => $spare_part_detail['consumed_part_status_id']], NULL, NULL, NULL, NULL, NULL)[0]['is_consumed'];
                            if(!empty($is_spare_consumed) && $is_spare_consumed == 1) {
                                $data['status'] = DEFECTIVE_PARTS_PENDING;
                            } else {
                                $data['status'] = OK_PART_TO_BE_SHIPPED;
                            }                 
                        }
                        
                        if($booking_details['current_status'] == _247AROUND_COMPLETED && $line_items < 2) {
                            $b['internal_status'] = $data['status'];
                        }
//                   }
                    $track_status = $new_state = "Spare Parts Required To Warehouse";
                    if ($this->session->userdata('partner_id')) {
                        $track_status = $new_state = "Spare Parts Required To Partner";
                    }
                    $create_sf_challan_file = false;
                    $service_center_id_challan = '';
                    if($spare_part_detail['status']==_247AROUND_COMPLETED && empty($spare_part_detail['sf_challan_file'])){
                        $create_sf_challan_file = $id;
                        $service_center_id_challan = $spare_part_detail['service_center_id'];
                    }

                    $old_state = SPARE_PARTS_REQUESTED;
                    break;
                
                CASE 'COURIER_LOST':
                    /**
                     * Admin mark courier lost.
                     * @modifiedBy Ankit Rajvanshi
                     */
                    $where = array('id' => $id);
                    $spare_part_detail = $this->reusable_model->get_search_result_data('spare_parts_details', '*', $where, NULL, NULL, NULL, NULL, NULL)[0];                    
                    $data['status'] = InProcess_Courier_Lost;
                    $data['old_status'] = $spare_part_detail['status'];
                    $data['consumed_part_status_id'] = 2;
                    $track_status = $new_state = InProcess_Courier_Lost;
                    $remarks = "Courier lost marked by admin";
                    $old_state = $spare_part_detail['status'];
                    break;
            }
            if ($flag) {
                $response = $this->service_centers_model->update_spare_parts($where, $data);
                if ($response && ($requestType == "CANCEL_PARTS" || $requestType == "DELIVERED_PART_CANCELLED") || $requestType == "QUOTE_REQUEST_REJECTED" ) { 
                    $this->update_inventory_on_cancel_parts($id, $booking_id, $old_state);
                }
                if($requestType == "REQUIRED_PARTS"){
                    if(!empty($create_sf_challan_file) && !empty($service_center_id_challan)){
                        $this->invoice_lib->generate_challan_file($create_sf_challan_file, $service_center_id_challan, '',true);
                    }
                }
            }

            if ($this->session->userdata('employee_id')) {
                $agent_id = $this->session->userdata('id');
                $agent_name = $this->session->userdata('employee_id');
                $entity_id = _247AROUND;
                $track_entity_type = _247AROUND_EMPLOYEE_STRING;
                $this->notify->insert_state_change($booking_id, $new_state, $old_state, $remarks, $agent_id, $agent_name, ACTOR_NOT_DEFINE, NEXT_ACTION_NOT_DEFINE, $entity_id, "", $id);
            } else if ($this->session->userdata('partner_id')) {
                $agent_id = $this->session->userdata('agent_id');
                $agent_name = $this->session->userdata('partner_name');
                $entity_id = $this->session->userdata('partner_id');
                $track_entity_type = _247AROUND_PARTNER_STRING;
                $this->notify->insert_state_change($booking_id, $new_state, $old_state, $remarks, $agent_id, $agent_name, ACTOR_NOT_DEFINE, NEXT_ACTION_NOT_DEFINE, $entity_id, "", $id );
            } else if ($this->session->userdata('service_center_id')) {
                $agent_id = $this->session->userdata('service_center_agent_id');
                $agent_name = $this->session->userdata('service_center_name');
                $entity_id = $this->session->userdata('service_center_id');
                $track_entity_type = _247AROUND_SF_STRING;
                $this->notify->insert_state_change($booking_id, $new_state, $old_state, $remarks, $agent_id, $agent_name, ACTOR_NOT_DEFINE, NEXT_ACTION_NOT_DEFINE, NULL, $entity_id, $id);
            }
            
            /* Insert Spare Tracking Details */
                if (!empty($id)) {
                    $tracking_details = array('spare_id' => $id, 'action' => $track_status, 'remarks' => trim($remarks), 'agent_id' => $agent_id, 'entity_id' => $entity_id, 'entity_type' => $track_entity_type);
                    $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                }

            $partner_id = $this->reusable_model->get_search_query('booking_details', 'booking_details.partner_id', array('booking_details.booking_id' => trim($booking_id)), NULL, NULL, NULL, NULL, NULL)->result_array();
			if(empty($b['internal_status']) && !empty($booking_new_internal_status)){
                $b['internal_status'] = $booking_new_internal_status;
            }
            if (!empty($partner_id) && !empty($b['internal_status'])) {
                $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking_details['current_status'], $b['internal_status'], $partner_id[0]['partner_id'], $booking_id);
                if (!empty($partner_status)) {
                    if ($line_items < 2 || !empty($booking_new_internal_status)) {
                        $b['partner_current_status'] = $partner_status[0];
                        $b['partner_internal_status'] = $partner_status[1];
                        $b['actor'] = $partner_status[2];
                        $b['next_action'] = $partner_status[3];
                    }
                }

                if($b['internal_status'] == SPARE_PARTS_REQUESTED) {
                    /**
                     * Check spare part request pending on partner 
                     * If Yes then do not change actor
                     * Otherwise check spare request pending on warehouse
                     * If yes then change actor to 247around
                     */
                    $pending_spare_parts_details = $this->partner_model->get_spare_parts_by_any('spare_parts_details.*', array('spare_parts_details.booking_id' => $booking_id,'spare_parts_details.status' => SPARE_PARTS_REQUESTED), TRUE, TRUE, false);
                    if(!empty($pending_spare_parts_details)) {
                        $entity_types = array_unique(array_column($pending_spare_parts_details, 'entity_type'));
                        // if no part request pending on partner then set 247around.
                        if(!in_array(_247AROUND_PARTNER_STRING, $entity_types)) {
                            $b['actor'] = _247AROUND_EMPLOYEE_STRING;
                        }
                    }
                }
                
                $this->booking_model->update_booking($booking_id, $b);
            }


            echo "Success";
            //redirect(base_url()."employee/inventory/get_spare_parts");
        } else {
            echo json_encode(array('status'=>FALSE));
        }
    }

    /**
     * @Desc: This function is used to get the brackets list in our crm
     * @params: void
     * @return: array
     * 
     */
    function get_brackets_detailed_list() {
        if ($this->input->post()) {

            switch ($this->input->post('type')) {
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
        if (!empty($sf_id)) {
            if ($sf_role === 'order_received_from') {
                $where["order_received_from = '$sf_id'"] = NULL;
            } else if ($sf_role === 'order_given_to') {
                $where["order_given_to = '$sf_id'"] = NULL;
            }
        }

        //check daterange selected or not
        if (!empty($this->input->post('start_date') && !empty($this->input->post('end_date')))) {
            $start_date = date('Y-m-d 00:00:00', strtotime($this->input->post('start_date')));
            $end_date = date('Y-m-d 23:59:59', strtotime($this->input->post('end_date')));

            $where[" order_date >= '$start_date' AND order_date <= '$end_date'"] = NULL;
        }

        if (!empty($where)) {
            $brackets_data = $this->get_brackets_data_by($select, $where);
        } else {
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
            if ($this->input->post('sf_id')) {
                $response = $this->load->view('service_centers/show_filtered_brackets_list', $data);
            } else {
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
    function update_part_price_details() {
        $booking_id = trim($this->input->post("booking_id"));
        if (!empty($booking_id)) {
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
        $this->form_validation->set_rules('around_part_commission', 'around_part_commission', 'trim');
        $this->form_validation->set_rules('part_estimate_given', 'Estimate Part Given', 'callback_check_validation_update_parts_details');
        if ($this->form_validation->run()) {
//            log_message('info', __METHOD__. " ". json_encode($_POST, true));
//            $str = '{"part_name":"OEPN CELL","part_estimate_given":"100","booking_id":"SM-17948020111139","partner_id":"247018","assigned_vendor_id":"1",'
//                    . '"around_part_commission":"30","total_parts_charges":"130.00","service_charge":"120","around_service_commission":"25",'
//                    . '"total_service_charges":"150.00","transport_charge":"150","around_transport_commission":"20",'
//                    . '"total_transport_charges":"180.00","courier_charge":"200","around_courier_commission":"15",'
//                    . '"total_courier_charges":"230.00","remarks":"Testing Remarks","estimate_remarks":"OPEN CELL Parts",'
//                    . '"total_charges":"814.20","estimate_sent":"1","arrange_part_by":"2"}';
//            $_POST = json_decode($str, true);
            $data = $this->input->post();

            $unit = $this->booking_model->get_unit_details(array("booking_id" => $data['booking_id'], 'price_tags' => REPAIR_IN_WARRANTY_TAG));
            if (!empty($unit)) {

                $success = $this->insert_zopper_form_data();
                if ($success['success']) {
                    $customer_total = $data['service_charge'] + $data['transport_charge'] +
                            $data['courier_charge'] + $data['part_estimate_given'] + $data['around_part_commission'];
                    //same_diff_vendor 1 means different vendor arrange part
                    if ($data['arrange_part_by'] == 1) {

                        $sf_parts = 0;
                    } else if ($data['arrange_part_by'] == 2) { //same_diff_vendor 1 means Same vendor arrange part
                        $is_gst = $this->vendor_model->is_tax_for_booking($data['booking_id']);
                        if (empty($is_gst[0]['gst_no'])) {
                            $sf_parts = ($data['part_estimate_given'] ) * parts_percentage * (1 + SERVICE_TAX_RATE);
                        } else {
                            $sf_parts = ($data['part_estimate_given'] ) * parts_percentage;
                        }
                    }
                    $sf_service = $data['service_charge'] * basic_percentage * (1 + SERVICE_TAX_RATE);
                    $venor_percentage = (($sf_service + $sf_parts) / $customer_total) * 100;
                    $u['customer_total'] = $u['partner_net_payable'] = $unit[0]['customer_total'] = $unit[0]['partner_net_payable'] = $unit[0]['partner_paid_basic_charges'] = $u['partner_paid_basic_charges'] = $customer_total;
                    $unit[0]["vendor_basic_percentage"] = $venor_percentage;
                    $u['vendor_basic_percentage'] = $venor_percentage;
                    $unit[0]['around_paid_basic_charges'] = 0;
                    $u['customer_paid_basic_charges'] = $u['around_paid_basic_charges'] = $u["customer_paid_parts"] = $u["customer_paid_extra_charges"] = 0;
                    $u['customer_net_payable'] = 0;
                    $u['id'] = $unit[0]['id'];
                    $this->booking_model->update_price_in_unit_details($u, $unit);
                    if ($this->input->post("entity_id") && $this->input->post("entity") == "vendor") {
                        $assign_vendor_id = $this->input->post("entity_id");
                    } else {
                        $assign_vendor_id = $this->input->post("assigned_vendor_id");
                    }
                    $this->insert_update_spare_parts($assign_vendor_id, $data['booking_id'], $unit[0]['model_number'], $unit[0]['serial_number']);
                    $is_sent = $this->input->post('estimate_sent');

                    if ($is_sent == 1) {

                        $sent = $this->create_zopper_excel_sheet($unit, $success['id'], $data);
                        if ($sent) {
                            $userSession = array('success' => "Thanks To Update Booking Price. Estimate Sent to Partner");
                        } else {
                            $userSession = array('success' => "Thanks To Update Booking Price.  Estimate did not send to Partner");
                        }
                    } else {
                        $userSession = array('success' => "Thanks To Update Booking Price.");
                    }

                    $this->session->set_userdata($userSession);
                    redirect(base_url() . "employee/inventory/update_part_price_details");
                } else {
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

    function insert_update_spare_parts($assigned_vendor_id, $booking_id, $model_number, $serial_number) {
        $sp['parts_requested'] = $this->input->post("part_name");
        $sp['partner_id'] = ZOPPER_ID;
        $sp['defective_part_required'] = 0;
        $sp['date_of_request'] = $sp['create_date'] = date('Y-m-d H:i:s');
        $sp['booking_id'] = $booking_id;
        $sp['status'] = SPARE_DELIVERED_TO_SF;
        $sp['service_center_id'] = $assigned_vendor_id;
        $sp['model_number'] = $model_number;
        $sp['serial_number'] = $serial_number;
        $sp['purchase_price'] = $this->input->post('part_estimate_given');
        $sp['sell_price'] = $this->input->post('part_estimate_given') + $this->input->post('around_part_commission');
        $entity = "";
        if ($this->input->post("entity")) {
            $entity = $this->input->post("entity");
        }

        if ($this->input->post("entity_id")) {
            $entity_id = $this->input->post("entity_id");
            if ($entity == "partner") {
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
    function create_zopper_excel_sheet($unit_details, $id, $formdata) {
        $booking_id = $unit_details[0]['booking_id'];
        $where['length'] = -1;
        $where['where'] = array("booking_details.booking_id" => $booking_id);
        //RM Specific Bookings
        $sfIDArray = array();
        if($this->session->userdata('user_group') == _247AROUND_RM || $this->session->userdata('user_group') == _247AROUND_ASM){
            $rm_id = $this->session->userdata('id');
            $rmServiceCentersData = $this->reusable_model->get_search_result_data("service_centres", "group_concat(id) as service_centres_id", array("rm_id" => $rm_id), NULL, NULL, NULL, NULL, NULL);
            $sfIDList = $rmServiceCentersData[0]['service_centres_id'];
            $sfIDArray = explode(",", $sfIDList);
        }
        $booking_details = $this->booking_model->get_bookings_by_status($where, "users.name, services, order_id, booking_details.partner_id", $sfIDArray);

        $partner_data = $this->partner_model->getpartner_details("company_name, gst_number, address, state, district, pincode, public_name ", array("partners.id" => $booking_details[0]->partner_id));
        $meta['name'] = $booking_details[0]->name;
        $meta['booking_id'] = $booking_id;
        $meta['services'] = $booking_details[0]->services;
        $meta['category'] = $unit_details[0]['appliance_category'];
        $meta['capacity'] = $unit_details[0]['appliance_capacity'];
        $meta['remarks'] = $this->input->post("estimate_remarks");
        $meta['order_id'] = $booking_details[0]->order_id;
        $meta['date'] = date("d-M-Y");
        $meta['company_name'] = $partner_data[0]['company_name'];
        $meta['company_gst_number'] = $partner_data[0]['gst_number'];
        $meta['company_address'] = $partner_data[0]['address'] . ", " . $partner_data[0]['district'] . ", Pincode " . $partner_data[0]['state'];

        $l_data = array();

        $total_gst_tax_amount = $total_amount = $total_taxable_value = 0;
        if ($formdata["service_charge"] > 0) {
            $s = array();
            $s[0]['brand'] = $unit_details[0]['appliance_brand'];
            $s[0]['model_number'] = $unit_details[0]['model_number'];
            $s[0]['service_type'] = "Service";
            $s[0]['taxable_value'] = $formdata["total_service_charges"];
            $s[0]['gst_rate'] = DEFAULT_TAX_RATE;
            $s[0]['gst_tax_amount'] = ($formdata["service_charge"] * DEFAULT_TAX_RATE) / 100;

            $total_gst_tax_amount = $s[0]['gst_tax_amount'];
            $s[0]['total_amount'] = sprintf("%1\$.2f", ( $s[0]['gst_tax_amount'] + $formdata["total_service_charges"]));
            $total_amount = $s[0]['total_amount'];
            $total_taxable_value = $s[0]['taxable_value'];
            $l_data = array_merge($l_data, $s);
        }
        
        if ($formdata["part_estimate_given"] > 0) {
            $p = array();
            $p[0]['brand'] = $unit_details[0]['appliance_brand'];
            $p[0]['model_number'] = $unit_details[0]['model_number'];
            $p[0]['service_type'] = "Product";
            $p[0]['taxable_value'] = $formdata["total_parts_charges"];
            $p[0]['gst_rate'] = DEFAULT_TAX_RATE;
            $p[0]['gst_tax_amount'] = (($formdata["total_parts_charges"]) * DEFAULT_TAX_RATE) / 100;

            $total_gst_tax_amount = $total_gst_tax_amount + $p[0]['gst_tax_amount'];
            $p[0]['total_amount'] = sprintf("%1\$.2f", ($p[0]['gst_tax_amount'] + $formdata["total_parts_charges"]));
            $total_amount += $p[0]['total_amount'];
            $total_taxable_value += $p[0]['taxable_value'];
            $l_data = array_merge($l_data, $p);
        }

        if ($formdata["transport_charge"] > 0) {
            $t = array();
            $t[0]['brand'] = $unit_details[0]['appliance_brand'];
            $t[0]['model_number'] = $unit_details[0]['model_number'];
            $t[0]['service_type'] = "Transport Charge";
            $t[0]['taxable_value'] = $formdata["transport_charge"];
            $t[0]['gst_rate'] = DEFAULT_TAX_RATE;
            $t[0]['gst_tax_amount'] = ($formdata["total_transport_charges"] * DEFAULT_TAX_RATE) / 100;
            $total_gst_tax_amount = $total_gst_tax_amount + $t[0]['gst_tax_amount'];
            $t[0]['total_amount'] = sprintf("%1\$.2f", ($t[0]['gst_tax_amount'] + $formdata["total_transport_charges"]));
            $total_amount = $total_amount + $t[0]['total_amount'];
            $total_taxable_value += $t[0]['taxable_value'];
            $l_data = array_merge($l_data, $t);
        }
        if ($formdata["courier_charge"] > 0) {
            $c = array();
            $c[0]['brand'] = $unit_details[0]['appliance_brand'];
            $c[0]['model_number'] = $unit_details[0]['model_number'];
            $c[0]['service_type'] = "Courier Charge";
            $c[0]['taxable_value'] = $formdata["total_courier_charges"];
            $c[0]['gst_rate'] = DEFAULT_TAX_RATE;
            $c[0]['gst_tax_amount'] = ($formdata["total_courier_charges"] * DEFAULT_TAX_RATE) / 100;

            $total_gst_tax_amount = $total_gst_tax_amount + $c[0]['gst_tax_amount'];
            $c[0]['total_amount'] = sprintf("%1\$.2f", ($c[0]['gst_tax_amount'] + $formdata["total_courier_charges"]));
            $total_amount += $c[0]['total_amount'];
            $total_taxable_value += $c[0]['taxable_value'];
            $l_data = array_merge($l_data, $c);
        }

        
        $meta['price_inword'] = convert_number_to_words($total_amount);
        $meta['total_taxable_value'] = sprintf("%1\$.2f", ($total_taxable_value));
        $meta['total_gst_tax_amount'] = sprintf("%1\$.2f", ($total_gst_tax_amount));
        $meta['total_amount_with_gst'] = sprintf("%1\$.2f", ($total_amount));
        $template = 'Estimate_Sheet.xlsx';
        // directory
        $templateDir = __DIR__ . "/../excel-templates/";

        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );

        //load template
        if (ob_get_length() > 0) {
            ob_end_clean();
        }
        $R = new PHPReport($config);

        $R->load(array(
            array(
                'id' => 'meta',
                'repeat' => false,
                'data' => $meta,
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

        $output_file_excel = "Estimate_" . $booking_id . ".xlsx";
        $R->render('excel', TMP_FOLDER . $output_file_excel);
        $output_file_pdf = "Estimate_" . $booking_id . ".pdf";
        $emailtemplate = $this->booking_model->get_booking_email_template("zopper_estimate_send");
        if (!empty($template)) {

            $subject = vsprintf($emailtemplate[4], array($partner_data[0]['public_name'], $meta['name']));
            //  $emailBody = vsprintf($emailtemplate[0], $estimate_cost);
            $html = $this->load->view('templates/estimate_sheet', array("meta"=>$meta,'estimate' => $l_data),true);
            $json_result = $this->miscelleneous->convert_html_to_pdf($html,$booking_id,$output_file_pdf,"jobcards-pdf");
            $pdf_response = json_decode($json_result, TRUE);
            $output_pdf_file_name = $output_file_excel;
            $attachement_url = TMP_FOLDER . $output_file_excel;

            if ($pdf_response['response'] === 'Success') {
                $output_pdf_file_name = $pdf_response['output_pdf_file'];
                $attachement_url = 'https://s3.amazonaws.com/' . BITBUCKET_DIRECTORY . '/jobcards-pdf/' . $output_pdf_file_name;
                log_message('info', __FUNCTION__ . ' Generated PDF File Name' . $output_pdf_file_name);
            } else if ($pdf_response['response'] === 'Error') {


                log_message('info', __FUNCTION__ . ' Error in Generating PDF File');
            }

            $this->notify->sendEmail($emailtemplate[2], $emailtemplate[1], $emailtemplate[3], '', $subject, $emailtemplate[0], $attachement_url, 'zopper_estimate_send');

            $this->inventory_model->update_zopper_estimate(array('id' => $id), array(
                "estimate_sent" => 1,
                "estimate_file" => $output_pdf_file_name,
                "estimate_remarks" => $this->input->post("estimate_remarks")
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
    function insert_zopper_form_data() {
        $z['part_estimate_given'] = $this->input->post("part_estimate_given");
        $z['booking_id'] = $this->input->post("booking_id");
        $z['around_part_commission'] = $this->input->post("around_part_commission");
        $z['service_charge'] = $this->input->post("service_charge");
        $z['transport_charge'] = $this->input->post("transport_charge");
        $z['courier_charge'] = $this->input->post("courier_charge");
        $z['arrange_part_by'] = $this->input->post("arrange_part_by");
        $z['remarks'] = $this->input->post("remarks");
        $z['part_name'] = $this->input->post("part_name");
        if ($this->input->post("entity")) {
            $z['entity'] = $this->input->post("entity");
        }

        if ($this->input->post("entity_id")) {
            $z['entity_id'] = $this->input->post("entity_id");
        }

        $is_exist = $this->inventory_model->select_zopper_estimate(array("booking_id" => $z['booking_id']));
        if (!empty($is_exist)) {
            $this->inventory_model->update_zopper_estimate(array('id' => $is_exist[0]['id']), $z);
            return array('success' => true, 'is_exist' => true, "id" => $is_exist[0]['id']);
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
                } else if ($part_arrange_by == 2) {
                    $sf_id = $this->input->post("assigned_vendor_id");
                    $_POST["entity_id"] = $sf_id;
                    $_POST["entity"] = "vendor";
                    if (empty($sf_id)) {
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
    function get_brackets_details() {
        $where = array('order_given_to' => $this->input->post('sf_id'),
            'is_received' => 1,
            "received_date >= (DATE_FORMAT(CURDATE(), '%Y-%m-01') - INTERVAL 2 MONTH)" => NULL);
        $data = $this->reusable_model->get_search_query('brackets', "DATE_FORMAT(received_date, '%b') AS month,SUM(26_32_received) as l_32,SUM(36_42_received) as g_32", $where, NULL, NULL, array('month' => 'asc'), NULL, NULL, 'month')->result_array();
        $response = array();
        if (!empty($data)) {
            foreach ($data as $value) {
                switch ($value['month']) {
                    case date('M'):
                        $response['cm_less_than_32'] = !empty($value['l_32']) ? $value['l_32'] : 0;
                        $response['cm_greater_than_32'] = !empty($value['g_32']) ? $value['g_32'] : 0;
                        break;
                    case date("M", strtotime("last month")):
                        $response['lm_less_than_32'] = !empty($value['l_32']) ? $value['l_32'] : 0;
                        $response['lm_greater_than_32'] = !empty($value['g_32']) ? $value['g_32'] : 0;
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
    function show_inventory_ledger_list($page = 0, $entity_type = "", $entity_id = "", $inventory_id = "", $offset = 0) {
        //if ($page == 0) {
        //Pranjal: updated as per discussion with ABhay
            $page =10000;
            $offset = 0;
          //End
        //}
        $where = "";
        if (!empty($entity_id) && !empty($entity_type)) {
            $where .= " where (i.receiver_entity_id = $entity_id AND i.receiver_entity_type = '" . $entity_type . "' OR i.sender_entity_id = $entity_id AND i.sender_entity_type = '" . $entity_type . "')";
        }

        if (!empty($inventory_id)) {
            $where .= " and i.inventory_id = $inventory_id";
        }
        $config['base_url'] = base_url() . 'employee/inventory/show_inventory_ledger_list/' . $page . "/" . $entity_type . "/" . $entity_id . "/" . $inventory_id;
        $config['total_rows'] = $this->inventory_model->get_inventory_ledger_data($page, $offset, $where, true);

        if ($offset !== "All") {
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
        $data['brackets'] = $this->inventory_model->get_inventory_ledger_data($config['per_page'], $offset, $where);
        $data['entity_id'] = $entity_id;
        $data['entity_type'] = $entity_type;
        $data['inventory_id'] = $inventory_id;
        if (!empty($inventory_id)) {
            $data['total_spare'] = $this->reusable_model->get_search_result_data("inventory_ledger", "SUM(quantity) as 'total_spare_from_ledger'", array('inventory_id' => $inventory_id, 'is_defective' => 0), NULL, NULL, NULL, NULL, NULL);
        }

        if ($this->session->userdata('service_center_id')) {
            $this->load->view('service_centers/header');
            $this->load->view("service_centers/show_inventory_ledger_list", $data);
        } else if ($this->session->userdata('id')) {
            $this->miscelleneous->load_nav_header();
            $this->load->view("employee/show_inventory_ledger_list", $data);
        } else if ($this->session->userdata('partner_id')) {
            $this->miscelleneous->load_partner_nav_header();
            // $this->load->view('partner/header');
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
    function get_inventory_stock() {

        $select = 'stock,part_number,part_name,description,inventory_stocks.entity_id,inventory_stocks.entity_type,inventory_stocks.inventory_id';
        $post['length'] = -1;
        $post['where'] = array('inventory_stocks.entity_id' => trim($this->input->post('entity_id')), 'inventory_stocks.entity_type' => trim($this->input->post('entity_type')));
        $post['search_value'] = array();
        $post['order'] = "";

        $data['stock_details'] = $this->inventory_model->get_inventory_stock_list($post, $select);
        echo $this->load->view('employee/inventory_stock_details', $data);
    }
    /**
     * Get inventory stock with vendor and inventory
     * @param  integer $inventory_id
     * @param  integer $vendor_id
     * @prints json encoded array
     */
    function get_inventory_stocks_by_inventory_id(){
        $inventory_id = $this->input->get("inventory_id");
        $res =array();
        if(empty($inventory_id) || !intval($inventory_id)){
            $res['error'] = true;
            $res['errorMessage'] = "No inventory provided to get stocks.";
            echo json_encode($res);die();
        }
        $post = array(
            "where"=> array(
                'inventory_stocks.inventory_id'=> $inventory_id
            ),
        );
        $select = "inventory_stocks.stock as stock,service_centres.name as name";

        $stocks = $this->inventory_model->get_warehouse_stocks($post, $select)->result_array();
        if(empty($stocks)){
            $res['error'] = true;
            $res['errorMessage'] = 'No Stock found.';
            echo json_encode($res);die();
        }

        $res['error'] = false;
        $res['payload'] = $stocks;
        echo json_encode($res);die();
    }

    /**
     * @Desc: This function is used to show form add inventory stocks for service center
     * @params: void
     * @return: void
     * 
     */
    function update_inventory_stock($sf_id = "") {
        $data['sf'] = $this->vendor_model->getVendorDetails('id,name', array('active' => '1'));
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
    function show_inventory_stock_list() {
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/show_inventory_stock_list');
    }


    /**
     *  @desc : This function is used to show partner OOT  report
     *  @param : void
     *  @return : void
     */
    function show_partner_oot_report() {
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/show_partner_oot_report');
    }



    /**
     *  @desc : This function is used to show partner OOT repoprt
     *  @param : void
     *  @return : $output JSON
     */
    function get_partner_OOT_report() {
        $data = $this->get_partner_oot_list();

        $post = $data['post'];
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => count($this->dashboard_model->get_spare_tat_report_count_total("partner_spare_out_of_tat",array(), 0,$post)),
            "recordsFiltered" => count($this->dashboard_model->get_spare_tat_report_count_filter("partner_spare_out_of_tat",array(), 0,$post)),
            "data" => $data['data'],
        );

        echo json_encode($output);
    }


    /**
     *  @desc : This function is used to get inventory stocks data
     *  @param : void
     *  @return : Array()
     */
    function get_partner_oot_list() {
        $post = $this->get_post_data();
        $post['column_search'] = array('public_name');
        $post['order'] = array(array('column' => 0, 'dir' => 'ASC'));
        $post['column_order'] = array('public_name');
        if (!$this->input->post('is_show_all')) {
           // $post['having'] = 'total_stocks > 0';
        }
        // $select = "partner_spare_out_of_tat.public_name,partner_spare_out_of_tat.out_of_tat_part_count,partner_spare_out_of_tat.out_of_tat_amount "
        //         . "FROM partner_spare_out_of_tat";

        $list = $this->dashboard_model->get_spare_tat_report("partner_spare_out_of_tat",array(), 0,$post);
        $data = array();
        $no = $post['start'];
        //unset($post['having']);
        foreach ($list as $stock_list) {
            $no++;
            $row = $this->get_spare_tat_report_table($stock_list, $no);
            $data[] = $row;
        }

        return array(
            'data' => $data,
            'post' => $post
        );
    }



    /**
     *  @desc : This function is used to generate table for partner OOT 
     *  @param : $stock_list array
     *  @param : $no string
     *  @return : $row Array()
     */
    function get_spare_tat_report_table($stock_list, $no) {
        $row = array();
        $row[] = $no;
        $sf = "<a target='_blank' href='".base_url()."employee/inventory/show_rm_wise_tat_report' ";
        $sf .= ">" . $stock_list['public_name'] . "</a>";
        $row[] = $sf;


        $row[] = $stock_list['out_of_tat_part_count'];
        $row[] = $stock_list['out_of_tat_amount'];

        return $row;
    }

/*    RM WISE TAT REPOR /*



   /**
     *  @desc : This function is used to show partner OOT  report
     *  @param : void
     *  @return : void
     */
    function show_rm_wise_tat_report() {
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/show_rm_wise_tat_report');
    }



    /**
     *  @desc : This function is used to show partner OOT repoprt
     *  @param : void
     *  @return : $output JSON
     */
    function get_show_rm_wise_tat_report() {
        $data = $this->get_show_rm_wise_tat_report_list();

        $post = $data['post'];
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => count($this->dashboard_model->get_spare_tat_report_count_total("rm_wise_tat_report",array(), 0,$post)),
            "recordsFiltered" => count($this->dashboard_model->get_spare_tat_report_count_filter("rm_wise_tat_report",array(), 0,$post)),
            "data" => $data['data'],
        );

        echo json_encode($output);
    }


    /**
     *  @desc : This function is used to get inventory stocks data
     *  @param : void
     *  @return : Array()
     */
    function get_show_rm_wise_tat_report_list() {
        $post = $this->get_post_data();
        $post['column_search'] = array('sf_name');
        $post['order'] = array(array('column' => 0, 'dir' => 'ASC'));
        $post['column_order'] = array('sf_name');
        if (!$this->input->post('is_show_all')) {
           // $post['having'] = 'total_stocks > 0';
        }
        // $select = "partner_spare_out_of_tat.public_name,partner_spare_out_of_tat.out_of_tat_part_count,partner_spare_out_of_tat.out_of_tat_amount "
        //         . "FROM partner_spare_out_of_tat";

        $list = $this->dashboard_model->get_spare_tat_report("rm_wise_tat_report",array(), 0,$post);
        $data = array();
        $no = $post['start'];
        //unset($post['having']);
        foreach ($list as $stock_list) {
            $no++;
            $row = $this->get_show_rm_wise_tat_report_list_table($stock_list, $no);
            $data[] = $row;
        }

        return array(
            'data' => $data,
            'post' => $post
        );
    }



    /**
     *  @desc : This function is used to generate table for partner OOT 
     *  @param : $stock_list array
     *  @param : $no string
     *  @return : $row Array()
     */
    function get_show_rm_wise_tat_report_list_table($stock_list, $no) {
        $row = array();
        $row[] = $no;
        $row[] = $stock_list['manager_name'];
        $sf = "<a target='_blank' href='".base_url()."employee/inventory/show_sf_brand_wise_tat_report' ";
        $sf .= ">" . $stock_list['agent_name'] . "</a>";
        $row[] = $sf;
        $row[] = $stock_list['sf_name'];
        $row[] = $stock_list['out_tat_part_count'];
        $row[] = $stock_list['out_tat_amount'];

        return $row;
    }





/*    SF WISE /*

   /**
     *  @desc : This function is used to show SF wise TAT report 
     *  @param : void
     *  @return : void
     */
    function show_sf_brand_wise_tat_report() {
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/show_sf_brand_wise_tat_report');
    }



    /**
     *  @desc : This function is used to show show SF wise TAT report 
     *  @param : void
     *  @return : $output JSON
     */
    function get_show_sf_brand_wise_tat_report_data() {
        $data = $this->get_show_sf_brand_wise_tat_report();

        $post = $data['post'];
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => count($this->dashboard_model->get_spare_tat_report_count_total("sf_brand_wise_tat_report",array(), 0,$post)),
            "recordsFiltered" => count($this->dashboard_model->get_spare_tat_report_count_filter("sf_brand_wise_tat_report",array(), 0,$post)),  
            "data" => $data['data'],
        );

        echo json_encode($output);
    }


    /**
     *  @desc : This function is used to get show SF wise TAT report 
     *  @param : void
     *  @return : Array()
     */
    function get_show_sf_brand_wise_tat_report() {
        $post = $this->get_post_data();
        $post['column_search'] = array('sf_name');
        $post['order'] = array(array('column' => 0, 'dir' => 'ASC'));
        $post['column_order'] = array('sf_name');
        if (!$this->input->post('is_show_all')) {
           // $post['having'] = 'total_stocks > 0';
        }
        // $select = "partner_spare_out_of_tat.public_name,partner_spare_out_of_tat.out_of_tat_part_count,partner_spare_out_of_tat.out_of_tat_amount "
        //         . "FROM partner_spare_out_of_tat";

        $list = $this->dashboard_model->get_spare_tat_report("sf_brand_wise_tat_report",array(), 0,$post);
        $data = array();
        $no = $post['start'];
        //unset($post['having']);
        foreach ($list as $stock_list) {
            $no++;
            $row = $this->get_sf_brand_wise_tat_report_table($stock_list, $no);
            $data[] = $row;
        }

        return array(
            'data' => $data,
            'post' => $post
        );
    }



    /**
     *  @desc : This function is used to generate table for partner OOT 
     *  @param : $stock_list array
     *  @param : $no string
     *  @return : $row Array()
     */
    function get_sf_brand_wise_tat_report_table($stock_list, $no) {
        $row = array();
        $row[] = $no;
        $row[] = $stock_list['sf_name'];
        // $sf = "<a target='_blank' href='".base_url()."employee/inventory/show_sf_brand_wise_tat_report' ";
        // $sf .= ">" . $stock_list['agent_name'] . "</a>";
        // $row[] = $sf;
        $row[] = $stock_list['state'];
        $row[] = $stock_list['district'];
        $row[] = $stock_list['partner_name'];
        $row[] = $stock_list['parts_count_to_shipped'];
        $row[] = $stock_list['parts_charge'];

        return $row;
    }


/*******************************/




/*    SF WISE /*

   /**
     *  @desc : This function is used to show SF wise TAT report 
     *  @param : void
     *  @return : void
     */
    function show_in_def_transit_tat_report() {
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/show_in_def_transit_tat_report');
    }



    /**
     *  @desc : This function is used to show show SF wise TAT report 
     *  @param : void
     *  @return : $output JSON
     */
    function get_show_in_def_transit_tat_report_data() {
        $data = $this->get_show_in_def_transit_tat_report();

        $post = $data['post'];
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => count($this->dashboard_model->get_spare_tat_report_count_total("in_def_transit_tat_report",array(), 0,$post)),
            "recordsFiltered" => count($this->dashboard_model->get_spare_tat_report_count_filter("in_def_transit_tat_report",array(), 0,$post)),  
            "data" => $data['data'],
        );

        echo json_encode($output);
    }


    /**
     *  @desc : This function is used to get show SF wise TAT report 
     *  @param : void
     *  @return : Array()
     */
    function get_show_in_def_transit_tat_report() {
        $post = $this->get_post_data();
        $post['column_search'] = array('public_name');
        $post['order'] = array(array('column' => 0, 'dir' => 'ASC'));
        $post['column_order'] = array('public_name');
        if (!$this->input->post('is_show_all')) {
           // $post['having'] = 'total_stocks > 0';
        }
        // $select = "partner_spare_out_of_tat.public_name,partner_spare_out_of_tat.out_of_tat_part_count,partner_spare_out_of_tat.out_of_tat_amount "
        //         . "FROM partner_spare_out_of_tat";

        $list = $this->dashboard_model->get_spare_tat_report("in_def_transit_tat_report",array(), 0,$post);
        $data = array();
        $no = $post['start'];
        //unset($post['having']);
        foreach ($list as $stock_list) {
            $no++;
            $row = $this->get_show_in_def_transit_tat_report_table($stock_list, $no);
            $data[] = $row;
        }

        return array(
            'data' => $data,
            'post' => $post
        );
    }



    /**
     *  @desc : This function is used to generate table for partner OOT 
     *  @param : $stock_list array
     *  @param : $no string
     *  @return : $row Array()
     */
    function get_show_in_def_transit_tat_report_table($stock_list, $no) {
        $row = array();
        $row[] = $no;
        $row[] = $stock_list['public_name'];
        $row[] = $stock_list['in_transit_part_count'];
        $row[] = $stock_list['in_transit_amount'];

        return $row;
    }




/*******************************/


/*  ---------------------- /*

/*     --------------------      /*
    /**
     *  @desc : This function is used to show inventory stocks data
     *  @param : void
     *  @return : $output JSON
     */
    function get_inventory_stock_list() {
        $data = $this->get_stock();

        $post = $data['post'];
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->inventory_model->count_all_inventory_stocks_list($post),
            "recordsFiltered" => $this->inventory_model->count_filtered_inventory_stocks_list($post),
            "data" => $data['data'],
        );

        echo json_encode($output);
    }

    /**
     *  @desc : This function is used to get inventory stocks data
     *  @param : void
     *  @return : Array()
     */
    function get_stock() {
        $post = $this->get_post_data();
        $post['column_search'] = array('name');
        $post['order'] = array(array('column' => 0, 'dir' => 'ASC'));
        $post['column_order'] = array('name');
        if (!$this->input->post('is_show_all')) {
            $post['having'] = 'total_stocks > 0';
        }
        $select = "inventory_stocks.entity_id,inventory_stocks.entity_type, (SELECT SUM(stock) "
                . "FROM inventory_stocks as s "
                . "WHERE inventory_stocks.entity_id = s.entity_id ) as total_stocks,service_centres.name";

        //RM Specific stocks
        $sfIDArray = array();
        if($this->session->userdata('user_group') == _247AROUND_RM || $this->session->userdata('user_group') == _247AROUND_ASM){
            $rm_id = $this->session->userdata('id');
            $rmServiceCentersData = $this->reusable_model->get_search_result_data("service_centres", "group_concat(id) as service_centres_id", array("rm_id" => $rm_id), NULL, NULL, NULL, NULL, NULL);
            $sfIDList = $rmServiceCentersData[0]['service_centres_id'];
            $sfIDArray = explode(",", $sfIDList);
        }

        $list = $this->inventory_model->get_inventory_stock_list($post, $select, $sfIDArray);
        $data = array();
        $no = $post['start'];
        //unset($post['having']);
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
     *  @desc : This function is used to generate table for inventory stocks
     *  @param : $stock_list array
     *  @param : $no string
     *  @return : $row Array()
     */
    function get_inventory_stocks_table($stock_list, $no) {
        $row = array();

        $sf = "<a href='javascript:void(0);' onclick='";
        $sf .= "get_vendor_stocks(" . '"' . $stock_list->entity_id . '"';
        $sf .= ', "' . $stock_list->entity_type . '"';
        $sf .= ")'>" . $stock_list->name . "</a>";

        $row[] = $no;
        $row[] = $sf;
        $row[] = ($stock_list->total_stocks >= 0) ? $stock_list->total_stocks : 0;

        return $row;
    }

    function spare_invoice_list() {
        log_message("info", __METHOD__);
        $w['length'] = -1;
        $w['spare_invoice_flag'] = true;
        $w['where'] = array("spare_parts_details.part_warranty_status" => 2,
            "status != 'Cancelled'" => NULL,
            "spare_parts_details.create_date >= '2017-12-01'" => NULL,
            "(`purchase_invoice_id` IS NULL )" => NULL,
            "spare_parts_details.partner_id != '" . _247AROUND . "'" => NULL,
            "spare_parts_details.parts_shipped IS NOT NULL" => NULL,
            "spare_parts_details.is_micro_wh" => 0);
        $w['select'] = "spare_parts_details.id, spare_parts_details.part_warranty_status, spare_parts_details.booking_id, purchase_price, public_name,"
                . "purchase_invoice_id,sell_invoice_id, incoming_invoice_pdf, sell_price, booking_details.partner_id as booking_partner_id,booking_details.request_type, spare_parts_details.status,oow_spare_invoice_details.invoice_id,oow_spare_invoice_details.invoice_pdf, oow_spare_invoice_details.invoice_amount as basic_amount";
        $data['spare'] = $this->inventory_model->get_spare_parts_query($w);
        if($this->input->post('dashboard')){
            $data['dashboard']  =   $this->input->post('dashboard');
        }else{
        $this->miscelleneous->load_nav_header();
        }
        $this->load->view("employee/spare_invoice_list", $data);
    }

    /**
     *  @desc : This function is used to show inventory master list table
     *  @param : void
     *  @return : void
     */
    function inventory_master_list() {
        $this->checkUserSession();
        $this->miscelleneous->load_nav_header();
        $data['saas_module'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        if(!empty($this->input->post('entity_id')))
        {
            $data['entity_id']=$this->input->post('entity_id');
            $data['service_id']=$this->input->post('service_id');
            $data['search']=$this->input->post('search');
        }
        $this->load->view("employee/inventory_master_list", $data);
    }
    
     /**
     *  @desc : This function is used to show inventory alternate part master list table
     *  @param : $partner_id
     *  @param : $inventory_id
     *  @param : $service_id
     *  @return : void
     */
    function alternate_inventory_list($partner_id, $inventory_id, $service_id) {
        $this->checkUserSession();
        $this->miscelleneous->load_nav_header();
        $where = array(
            'inventory_master_list.entity_id' => $partner_id,
            'inventory_master_list.entity_type' => _247AROUND_PARTNER_STRING,
            'inventory_master_list.inventory_id' => $inventory_id,
            'inventory_master_list.service_id' => $service_id,
        );
        
        $inventory_list = $this->inventory_model->get_inventory_master_list_data('inventory_master_list.part_name', $where, array());
        $data = array();
        $data['saas_module'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $data['partner_id'] = $partner_id;
        $data['inventory_id'] = $inventory_id;
        $data['service_id'] = $service_id;
        if (!empty($inventory_list)) {
            $data['part_name'] = $inventory_list[0]['part_name'];
        }
        $this->load->view("employee/inventory_alternate_master_list", $data);
    }

    /**
     *  @desc : This function is used to show alternate parts inventory master list table
     *  @param : void
     *  @return : void
     */
    function alternate_parts_inventory_list() {
        $this->checkUserSession();
        $this->miscelleneous->load_nav_header();
        $this->load->view("employee/alternate_parts_inventory_list");
    }
    
    /**
     *  @desc : This function is used to show inventory master list data
     *  @param : void
     *  @return : void
     */
    function get_inventory_master_list() {
        $data = $this->get_master_list_data();

        $post = $data['post'];
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->inventory_model->count_all_inventory_master_list($post),
            "recordsFiltered" => $this->inventory_model->count_filtered_inventory_master_list($post),
            "data" => $data['data'],
        );

        echo json_encode($output);
    }

    function get_master_list_data() {
        $post = $this->get_post_data();
        $post['column_order'] = array();
        $post['column_search'] = array('part_name', 'part_number', 'services.services', 'services.id');
        $post['where'] = array('inventory_master_list.entity_id' => trim($this->input->post('entity_id')), 'inventory_master_list.entity_type' => trim($this->input->post('entity_type')));

        if ($this->input->post('service_id') && $this->input->post('service_id') !== 'all') {
            $post['where']['service_id'] = $this->input->post('service_id');
        }

        $select = "inventory_master_list.*,services.services";

        $list = $this->inventory_model->get_inventory_master_list($post, $select);

        $partners = array_column($this->partner_model->getpartner_details("partners.id,public_name", array()), 'public_name', 'id');
        $data = array();
        $no = $post['start'];
        foreach ($list as $stock_list) {
            $no++;
            $row = $this->get_inventory_master_list_table($stock_list, $no, $partners);
            $data[] = $row;
        }

        return array(
            'data' => $data,
            'post' => $post
        );
    }

    function get_inventory_master_list_table($stock_list, $no, $partners) {
        $row = array();
        if ($stock_list->entity_type === _247AROUND_PARTNER_STRING) {
            $stock_list->entity_public_name = $partners[$stock_list->entity_id ];
        }
        $json_data = json_encode($stock_list);

        $row[] = $no;
        $row[] = $stock_list->services;
        $row[] = $stock_list->type;
        $row[] = "<span style='word-break: break-all;'>" . $stock_list->part_name . "</span>";
        $row[] = "<span style='word-break: break-all;'>" . $stock_list->part_number . "</span>";
        $row[] = $stock_list->description;
        $row[] = $stock_list->size;
        $row[] = $stock_list->hsn_code;
        $row[] = "<i class ='fa fa-inr'></i> " . $stock_list->price;
        $row[] = $stock_list->gst_rate . "%";
        $total = number_format((float) ($stock_list->price + ($stock_list->price * ($stock_list->gst_rate / 100))), 2, '.', '');
        $row[] = "<i class ='fa fa-inr'></i> " . $total;
        
        $saas_partner = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
       
        if ($this->session->userdata('userType') == 'employee') {
            $row[] = $stock_list->oow_vendor_margin . " %";
            
            if(!$saas_partner){
                $row[] = $stock_list->oow_around_margin . " %";
            }

        }
        
        if ($this->session->userdata('userType') == 'employee') {
            $basic_price_with_around_margin = (float) $stock_list->price + ($stock_list->price * ($stock_list->oow_around_margin / 100));
            $sf_total = $basic_price_with_around_margin + ($basic_price_with_around_margin * ($stock_list->gst_rate / 100));
            $row[] = "<i class ='fa fa-inr'></i> " . number_format((float) ($sf_total), 2, '.', '');
        }
        if (!empty($stock_list->is_defective_required)) {
            $row[] = 'Yes';
        } else {
            $row[] = 'No';
        }


        if ($this->session->userdata('userType') == 'employee') {
            $row[] = "<i class ='fa fa-inr'></i> " . round(($total * ( 1 + ($stock_list->oow_vendor_margin + $stock_list->oow_around_margin) / 100 )), 0);
        }
        $row[] = "<a href='javascript:void(0)' class ='btn btn-primary' id='edit_master_details' data-id='$json_data' title='Edit Details'><i class = 'fa fa-edit'></i></a>";
        $row[] = "<a href='" . base_url() . "employee/inventory/get_bom_list_by_inventory_id/" . urlencode($stock_list->inventory_id) . "' class = 'btn btn-primary' title='Get Model Details' target='_blank'><i class ='fa fa-eye'></i></a>";
        $row[] = '<a href="' . base_url() . 'employee/inventory/alternate_inventory_list/' . $stock_list->entity_id . '/' . $stock_list->inventory_id . '/' . $stock_list->service_id . '" target="_blank" class="btn btn-info">View</a>';            


        return $row;
    }
    
    
    
    /**
     *  @desc : This function is used to show serviceable BOM list data
     *  @param : void
     *  @return : void
     */
    function get_serviceable_bom_details() {
        $data = $this->get_serviceable_bom_list_data();
        $post = $data['post'];
        if (!empty($data['data'])) {
            $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $this->inventory_model->count_all_serviceable_bom_list($post),
                "recordsFiltered" => $this->inventory_model->count_filtered_serviceable_bom_list($post),
                "data" => $data['data'],
            );
        } else {
            $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => $data['data'],
            );
        }
        echo json_encode($output);
    }

    function get_serviceable_bom_list_data() {
        $post = $this->get_post_data();

        $entity_type = _247AROUND_PARTNER_STRING;
        $entity_id = trim($this->input->post('partner_id'));
        $service_id = trim($this->input->post('service_id'));
        $model_number_id = trim($this->input->post('model_number_id'));

        $data = array();
        $post['column_order'] = array();
        $post['column_search'] = array('part_name', 'part_number', 'services.services', 'type');

        if (!empty($model_number_id)) {

            if (!empty($entity_id) && !empty($service_id) && !empty($model_number_id)) {
                $post['where'] = "inventory_master_list.entity_id = $entity_id AND inventory_master_list.entity_type ='" . $entity_type . "' AND  inventory_master_list.service_id = $service_id";
            }
            if (!empty($model_number_id)) {
                $post['where'] = "inventory_model_mapping.model_number_id = $model_number_id ";
            }

            $select = "inventory_master_list.*,appliance_model_details.model_number,services.services";
            $list = $this->inventory_model->get_serviceable_bom_master_list($post, $select);
            $no = $post['start'];
            foreach ($list as $stock_list) {
                $no++;
                $row = $this->get_serviceable_bom_list_table($stock_list, $no);
                $data[] = $row;
            }
        }
        return array(
            'data' => $data,
            'post' => $post
        );
    }
    
     function get_serviceable_bom_list_table($stock_list, $no) {
        $row = array();
        $row[] = $no;
        $row[] = $stock_list->services;
        $row[] = $stock_list->type;
        $row[] = "<span style='word-break: break-all;'>" . $stock_list->part_name . "</span>";
        $row[] = "<span style='word-break: break-all;'>" . $stock_list->part_number . "</span>";
        $row[] = $stock_list->description;
        $row[] = $stock_list->gst_rate;
        $sf_price = number_format((float)$stock_list->price+($stock_list->price*($stock_list->gst_rate)/100), 2, '.', '');
        $total = number_format((float) ($sf_price + ($sf_price * ($stock_list->oow_vendor_margin / 100))), 2, '.', '');
        $row[] = "<i class ='fa fa-inr'></i> " . $total;
        $row[] = $stock_list->oow_vendor_margin . " %";
        $saas_partner = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        if($saas_partner){
        $row[] = $stock_list->oow_around_margin . " %";
        }
        $row[] = number_format((float)$sf_price+($sf_price*($stock_list->oow_around_margin+$stock_list->oow_vendor_margin)/100), 2, '.', '');
        return $row;
    }
    
    /**
     *  @desc : This function is used to show alternate inventory master list data
     *  @param : void
     *  @return : void
     */
    function get_partner_wise_alternate_inventory_list() {
        $data = $this->get_alternate_master_list_data();
        $post = $data['post'];
        if (!empty($data['data'])) {
            $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $this->inventory_model->count_all_alternate_inventory_master_list($post),
                "recordsFiltered" => $this->inventory_model->count_filtered_alternate_inventory_master_list($post),
                "data" => $data['data'],
                "set_id" => $data['group_id']
            );
        } else {
            $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => $data['data'],
                "set_id" => $data['group_id']
            );
        }
        echo json_encode($output);
    }

    function get_alternate_master_list_data() {
        $post = $this->get_post_data();
        $where_type = ""; 
        $inventory_id = $this->input->post('inventory_id');
        $part_type = trim($this->input->post('part_type'));
        if(isset($part_type) && !empty($part_type)){
         $where_type =  "AND inventory_master_list.type='".$part_type."'";   
        }else{
         $where_type = "";    
        }
        $entity_type = trim($this->input->post('entity_type'));
        $entity_id = trim($this->input->post('entity_id'));
        $service_id = trim($this->input->post('service_id'));
        $request_type = false;
        if(!empty($this->input->post('request_type'))){
            $request_type = $this->input->post('request_type');
        }

        if (!empty($inventory_id)) {
            $where_clause = "AND alternate_inventory_set.inventory_id = ".$inventory_id;
            $flag = false;
            $group_inventory_id = $this->inventory_model->get_group_wise_inventory_id_detail('alternate_inventory_set.inventory_id,alternate_inventory_set.group_id', $where_clause, $flag);
            $inventory_ids = implode(',', array_map(function ($entry) {
                        return $entry['inventory_id'];
                    }, $group_inventory_id));
        }
        
     
        
        $data = array();
        $group_id = '';
        if (!empty($group_inventory_id)) {
            $group_id = $group_inventory_id[0]['group_id'];
            $post['column_order'] = array();
            $post['column_search'] = array('part_name', 'part_number', 'services.services', 'services.id', 'appliance_model_details.model_number');
            $post['where'] = "inventory_master_list.entity_id = $entity_id AND inventory_master_list.entity_type ='" . $entity_type . "' AND  inventory_master_list.service_id = $service_id AND inventory_master_list.inventory_id IN($inventory_ids)" . $where_type;
            $select = "inventory_master_list.*,services.services,alternate_inventory_set.status,appliance_model_details.id as model_id,appliance_model_details.model_number, alternate_inventory_set.group_id";
            $list = $this->inventory_model->get_alternate_inventory_master_list($post, $select);
            $partners = array_column($this->partner_model->getpartner_details("partners.id,public_name", array('partners.is_active' => 1, 'partners.is_wh' => 1)), 'public_name', 'id');
            $data = array();
            $no = $post['start'];
            foreach ($list as $stock_list) {
                $no++;
                $row = $this->get_alternate_inventory_master_list_table($stock_list, $no, $partners, $request_type);
                $data[] = $row;
            }
        }

        return array(
            'data' => $data,
            'group_id' => $group_id,
            'post' => $post
        );
    }

    function get_alternate_inventory_master_list_table($stock_list, $no, $partners,$request_type) {
        $row = array();
        if ($stock_list->entity_type === _247AROUND_PARTNER_STRING) {
            $stock_list->entity_public_name = $partners[$stock_list->entity_id];
        }
        $json_data = json_encode($stock_list);
       
        $row[] = $no;
        $row[] = $stock_list->services;
        $row[] = $stock_list->type;
        $row[] = "<span style='word-break: break-all;'>" . $stock_list->part_name . "</span>";
        $row[] = "<span style='word-break: break-all;'>" . $stock_list->part_number . "</span>";
        if(!empty($request_type)){
          $row[] = "<span style='word-break: break-all;'>" . $stock_list->model_number . "</span>";  
        }        
        $row[] = "<span style='word-break: break-all;'>" . $stock_list->description . "</span>";
        $row[] = $stock_list->size;
        $row[] = $stock_list->hsn_code;
        $row[] = "<i class ='fa fa-inr'></i> " . $stock_list->price;
        $row[] = $stock_list->gst_rate . "%";
        $total = number_format((float) ($stock_list->price + ($stock_list->price * ($stock_list->gst_rate / 100))), 2, '.', '');
        $row[] = "<i class ='fa fa-inr'></i> " . $total;
        
        $repair_oow_around_percentage = REPAIR_OOW_AROUND_PERCENTAGE;
        if ($stock_list->oow_around_margin > 0) {
            $repair_oow_around_percentage = $stock_list->oow_around_margin / 100;
        }
        
        if ($this->session->userdata('userType') == 'service_center') {
            $repair_oow_around_percentage_vendor1 = $stock_list->oow_vendor_margin / 100;
            $totalpriceforsf = number_format((float) (round($stock_list->price * ( 1 + $repair_oow_around_percentage_vendor1), 0) + (round($stock_list->price * ( 1 + $repair_oow_around_percentage_vendor1), 0) * ($stock_list->gst_rate / 100))), 2, '.', '');
            $row[] = '<span id="total_amount_' . $stock_list->inventory_id . '">' . number_format((float) (round($totalpriceforsf * ( 1 + $repair_oow_around_percentage), 0) + (round($totalpriceforsf * ( 1 + $repair_oow_around_percentage), 0) * ($repair_oow_around_percentage / 100))), 2, '.', '') . "</span>";
        }
        
        if ($this->session->userdata('userType') == 'employee') {
            $row[] = $stock_list->oow_vendor_margin . " %";
            $row[] = $stock_list->oow_around_margin . " %";

            $basic_price_with_around_margin = (float) $stock_list->price + ($stock_list->price * ($stock_list->oow_around_margin / 100));
            $sf_total = $basic_price_with_around_margin + ($basic_price_with_around_margin * ($stock_list->gst_rate / 100));
            $row[] = "<i class ='fa fa-inr'></i> " . number_format((float) ($sf_total), 2, '.', '');        
            
            $row[] = "<i class ='fa fa-inr'></i> " . round(($total * ( 1 + ($stock_list->oow_vendor_margin + $stock_list->oow_around_margin) / 100 )), 0);
        }
        
        if($this->session->userdata('userType') == 'partner'){
           $row[] = "<i class ='fa fa-inr'></i> " . round(($total * ( 1 + ($stock_list->oow_vendor_margin + $stock_list->oow_around_margin) / 100 )), 0); 
        }

        if ($stock_list->status == 1) {
            $icon = '<i class="glyphicon glyphicon-ban-circle" style="font-size: 16px;"></i>';
            $colour_class = 'btn-danger';
        } else {
            $icon = '<i class="glyphicon glyphicon-ok-circle" style="font-size: 16px;"></i>';
            $colour_class = 'btn-primary';
        }
        
        if ($stock_list->status == 1) {
            $row[]= " <span style='color:#01903a;'>Active</span>";
        } else {
            $row[] = "<span style='color:#d9534f;'>Inactive</span>";
        }
        
        if ($this->session->userdata('userType') == 'employee') {
            $json_data = json_encode(array('status' => $stock_list->status, 'group_id' => $stock_list->group_id,'inventory_id' => $stock_list->inventory_id, 'model_id' => $stock_list->model_id));
            $row[] = "<a href='javascript:void(0)' class ='btn $colour_class' data-alternate_spare_details='$json_data' id='change_status_alternate_spare_part'>" . $icon . "</a>";
        }
        return $row;
    }

    /**
     *  @desc : This function is used to perform add/edit action on the inventory_master_list table
     *  @param : void()
     *  @return : $response JSON
     */
    function process_inventoy_master_list_data() {
        $submit_type = $this->input->post('submit_type');
        if (!empty($submit_type)) {
            if ($this->session->userdata("userType") == _247AROUND_PARTNER_STRING) {
                $agent_id = $this->session->userdata('agent_id');
            } else {
                $agent_id = $this->session->userdata('id');
            }
           
            $data = array('part_name' => trim($this->input->post('part_name')),
                'part_number' => trim($this->input->post('part_number')),
                'size' => trim($this->input->post('size')),
                'price' => trim($this->input->post('price')),
                'hsn_code' => trim($this->input->post('hsn_code')),
                'gst_rate' => trim($this->input->post('gst_rate')),
                'type' => trim($this->input->post('type')),
                'description' => trim($this->input->post('description')),
                'service_id' => $this->input->post('service_id'),
                'entity_type' => $this->input->post('entity_type'),
                'entity_id' => $this->input->post('entity_id'),
                'oow_vendor_margin' => $this->input->post('oow_vendor_margin'),
                'oow_around_margin' => $this->input->post('oow_around_margin'),
                'is_defective_required' => $this->input->post('is_defective_required'),
                'agent_id' => $agent_id
            );

           
            if (!empty($data['service_id']) && !empty($data['part_name']) && !empty($data['part_number']) && !empty($data['type']) && !empty($data['entity_id']) && !empty($data['entity_type'])) {

                if (!empty($data['price']) && !empty($data['hsn_code']) && !empty($data['gst_rate'])) {

                    switch (strtolower($submit_type)) {
                        case 'add':
                            $data['create_date'] = date('Y-m-d H:i:s');
                            $response = $this->add_inventoy_master_list_data($data);
                            break;
                        case 'edit':
                            $response = $this->edit_inventoy_master_list_data($data);
                            break;
                    }
                } else {
                    $response['response'] = 'error';
                    $response['msg'] = 'Please Enter Valid Price/Hsn/Gst Rate.';
                }
            } else {
                $response['response'] = 'error';
                $response['msg'] = 'All fields are required';
            }
        } else {
            $response['response'] = 'error';
            $response['msg'] = 'Please Try Again!!!';
            log_message("info", __METHOD__ . 'Invalid request type');
        }


        echo json_encode($response);
    }

    /**
     *  @desc : This function is used to perform insert action on the partner_file_upload_header_mapping table
     *  @param : $data array()
     *  @return : $res array()
     */
    function add_inventoy_master_list_data($data) {


        if (!empty($data['part_number'])) {

            $where = array(
                'inventory_master_list.entity_id' => $data['entity_id'],
                'inventory_master_list.entity_type' => _247AROUND_PARTNER_STRING,
                'inventory_master_list.part_number' => trim($data['part_number'])
            );

            $part_number_detail = $this->inventory_model->get_generic_table_details('inventory_master_list', 'inventory_master_list.part_number', $where, array());
            if (empty($part_number_detail)) {
                $this->validate_part_picture_upload_file();
                $data['part_image']= $this->input->post('part_pic');
                $response = $this->inventory_model->insert_inventory_master_list_data($data);
                if ($response) {
                    log_message("info", __METHOD__ . ' Inventory added successfully');
                    if ($this->input->post('model_number_id')) {
                        //process inventory model mapping
                        $mapping_data = array();
                        $mapping_data['inventory_id'] = $response;
                        $mapping_data['model_number_id'] = trim($this->input->post('model_number_id'));
                        $insert_mapping = $this->inventory_model->insert_inventory_model_mapping($mapping_data);
                       
                        if ($insert_mapping) {
                            log_message("info", __METHOD__ . ' Inventory and mapping created successfully');
                            $res['response'] = 'success';
                            $res['msg'] = 'Inventory and mapping created successfully';
                        } else {
                            $res['response'] = 'error';
                            $res['msg'] = 'Inventory added successfully but mapping can not be created';
                        }
                    } else {
                        $res['response'] = 'success';
                        $res['msg'] = 'Inventory added successfully';
                    }
                } else {
                    $res['response'] = 'error';
                    $res['msg'] = 'Error in inserting inventory details';
                    log_message("info", __METHOD__ . 'Error in inserting inventory details');
                }
            } else {
                $res['response'] = 'error';
                $res['msg'] = 'Part Number is already exist in our database.';
            }
        } else {
            $res['response'] = 'error';
            $res['msg'] = 'Inventory part code should not be blanck';
        }

        return $res;
    }
    
    
    /**
     * @desc: This function is used to validate uploaded Part Picture
     * @params: void
     * @return: boolean
     */
    function validate_part_picture_upload_file() {
        $part_pic_exist = $this->input->post('part_pic_exist');
        if (!empty($_FILES['part_picture']['tmp_name'])) {
            $allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
            $random_number =  rand(0, 9);
            $part_image_receipt = $this->miscelleneous->upload_file_to_s3($_FILES["part_picture"], 
                    "part_pic", $allowedExts, $random_number, "misc-images", "part_pic");
            if($part_image_receipt){
                
               return true;
            } else {
                $this->form_validation->set_message('validate_serial_number_pic_upload_file', 'Serial Number, File size or file type is not supported. Allowed extentions are "png", "jpg", "jpeg" and "pdf". '
                        . 'Maximum file size is 5 MB.');
                return false;
            }
            
        } else if(!empty($part_pic_exist)){
            $_POST['part_pic'] = $part_pic_exist;
            return true;
        } else {
            $this->form_validation->set_message('validate_serial_number_pic_upload_file', 'Please Upload Serial Number Image');
                return FALSE;
        }
    }

    /**
     *  @desc : This function is used to perform edit action on the partner_file_upload_header_mapping table
     *  @param : $data array()
     *  @return : $res array()
     */
    function edit_inventoy_master_list_data($data) {

        $this->validate_part_picture_upload_file();
        $data['part_image'] = $this->input->post('part_pic');
        $response = $this->inventory_model->update_inventory_master_list_data(array('inventory_id' => $this->input->post('inventory_id')), $data);
        if (!empty($response)) {
            $res['response'] = 'success';
            $res['msg'] = 'Details has been updated successfully';
            log_message("info", __METHOD__ . 'Details has been updated successfully');
        } else {
            $res['response'] = 'error';
            $res['msg'] = 'Error in updating details';
            log_message("info", __METHOD__ . 'error in updating  details');
        }

        return $res;
    }

    /**
     *  @desc : This function is used to get inventory snapshot for each sf based on last 1 Month bookings 
     * for that sf 
     *  @param : void
     *  @return : $sf_list JSON
     */
    function get_inventory_snapshot() {
        $sf_list = array();

        //get avg booking for each sf in last 1 month
        $avg_booking_select = 'assigned_vendor_id,count(DISTINCT booking_details.booking_id),(count(DISTINCT booking_details.booking_id)/30) as avg_booking';
        $avg_booking_where = array('price_tags' => _247AROUND_WALL_MOUNT__PRICE_TAG,
            "booking_unit_details.create_date >= (NOW() - Interval 30 Day)" => NULL, 'assigned_vendor_id IS NOT NULL' => NULL);
        $sf_bookings_snapshot = $this->inventory_model->get_inventory_snapshot($avg_booking_select, $avg_booking_where, 'assigned_vendor_id');

        //get total stocks for each sf
        $inventory_select = "SUM(IF(inventory_stocks.inventory_id = 1, inventory_stocks.stock, 0)) AS l_32,SUM(IF(inventory_stocks.inventory_id = 2, inventory_stocks.stock, 0)) AS g_32,service_centres.name,service_centres.id";
        $inventory_where['length'] = -1;
        $inventory_where['group_by'] = 'inventory_stocks.entity_id';
        $inventory_count = $this->inventory_model->get_inventory_stock_list($inventory_where, $inventory_select, array(), false);

        //get no of days by which brackets whould be exhausted for the sf
        if (!empty($sf_bookings_snapshot)) {
            foreach ($sf_bookings_snapshot as $value) {

                $key = array_search($value['assigned_vendor_id'], array_column($inventory_count, 'id'));
                if ($key !== FALSE) {
                    $total_stocks = $inventory_count[$key]['l_32'] + $inventory_count[$key]['g_32'];
                    $no_of_days_brackets_exhausted = abs($total_stocks / $value['avg_booking']);

                    $tmp['sf_id'] = $value['assigned_vendor_id'];
                    $tmp['sf_name'] = $inventory_count[$key]['name'];
                    $tmp['brackets_exhausted_days'] = ($total_stocks > 0) ? round($no_of_days_brackets_exhausted) : 0;
                    $tmp['l_32'] = ($total_stocks > 0) ? $inventory_count[$key]['l_32'] : 0;
                    $tmp['g_32'] = ($total_stocks > 0) ? $inventory_count[$key]['g_32'] : 0;
                    array_push($sf_list, $tmp);
                }
            }
        }

        array_multisort(array_column($sf_list, 'brackets_exhausted_days'), SORT_ASC, $sf_list);

        echo json_encode($sf_list);
    }

    /** @desc: This function is used to upload the spare parts file. By this method we can add spare details in our inventory_mast_list table.
     * @param: void
     * @return void
     */
    function upload_inventory_details_file($isAdmin = 1) {     
        if($isAdmin == 1) {
            log_message('info', __FUNCTION__ . ' Function Start For Admin '.$this->session->userdata('id'));
            $this->checkUserSession();
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/upload_spare_part_details');
        }
        else
        {
            log_message('info', __FUNCTION__ . ' Function Start For Partner '.$this->session->userdata('partner_id'));
            $this->check_PartnerSession();
            $this->miscelleneous->load_partner_nav_header();
            $this->load->view('partner/upload_spare_part_details');
            $this->load->view('partner/partner_footer');
        }
    }
    
    /** @desc: This function is used to get the inventory list with stock details.
     * @param: void
     * @return void
     */
    function get_inventory_stocks_details() {
        $post = $this->get_post_data();
        $return_new_part_flag = trim($this->input->post('return_new_part_to_partner'));
        if (($this->input->post('receiver_entity_id') && $this->input->post('receiver_entity_type') && $this->input->post('sender_entity_id') && $this->input->post('sender_entity_type'))) {
            $post[''] = array();
            $post['column_order'] = array();
            $post['column_search'] = array('part_name', 'part_number', 'type','services.services');
            $post['where'] = array('inventory_stocks.stock <> 0' => NULL);

            if ($this->input->post('receiver_entity_id') && $this->input->post('receiver_entity_type')) {
                $post['where']['inventory_stocks.entity_id'] = trim($this->input->post('receiver_entity_id'));
                $post['where']['inventory_stocks.entity_type'] = trim($this->input->post('receiver_entity_type'));
            }
            if ($this->input->post('sender_entity_id') && $this->input->post('sender_entity_type')) {
                $post['where']['inventory_master_list.entity_id'] = trim($this->input->post('sender_entity_id'));
                $post['where']['inventory_master_list.entity_type'] = trim($this->input->post('sender_entity_type'));
            }
            if ($this->input->post('is_show_all')) {
                unset($post['where']['inventory_stocks.stock <> 0']);
            }
            if ($this->input->post('service_id')) {
                $post['where']['inventory_master_list.service_id'] = trim($this->input->post('service_id'));
            }
            $select = "inventory_master_list.*,inventory_stocks.stock,inventory_stocks.pending_request_count,services.services,inventory_stocks.entity_id as receiver_entity_id,inventory_stocks.entity_type as receiver_entity_type";

            $list = $this->inventory_model->get_inventory_stock_list($post, $select);
            $data = array();
            $no = $post['start'];
            $rowSums = array(
                "colCount"=>0,
                "colData"=>array(
                    0=> 'Total',
                    6=>0,                   //stock total occurs in col 7 in datatable
                    10=>0.00,               //total occurs in col 11 in datatable
                    11=>0.00                //customer total in col 11
                )
            );
            foreach ($list as $inventory_list) {
                $no++;
                $row = $this->get_inventory_stocks_details_table($inventory_list, $no, $return_new_part_flag);
                $data[] = $row;

                $tSum = $this->get_inventory_stock_total($inventory_list);
                $rowSums['colData'][6] += $tSum['stocks'];
                $rowSums['colData'][10] += $tSum['total'];
                $rowSums['colData'][11] += $tSum['customerTotal'];
                $rowSums["colCount"] = (count($row)>$rowSums['colCount'])?count($row):$rowSums["colCount"];
            }
            if(count($data)>0){
                $data[] = $this->draw_table_footer($rowSums);
            }
            $post['length'] = -1;
            $countlist = $this->inventory_model->get_inventory_stock_list($post, "sum(inventory_stocks.stock) as stock");

            $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $this->inventory_model->count_all_inventory_stocks($post),
                "recordsFiltered" => $this->inventory_model->count_filtered_inventory_stocks($post),
                'stock' => $countlist[0]->stock,
                "data" => $data,
            );
        } else {
            $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                'stock' => 0,
                "data" => array(),
            );
        }
        echo json_encode($output);
    }

    private function draw_table_footer($rowData){
        $res = array();
        for($i=0;$i<$rowData['colCount'];$i++){
            $res[$i] = '';
            if(isset($rowData['colData'])){
                if(isset($rowData['colData'][$i])){
                    $res[$i] = (is_float($rowData['colData'][$i]))?number_format($rowData['colData'][$i],2):$rowData['colData'][$i];
                }
            }
        }
        return $res;
    }

    private function get_inventory_stock_total($inventory){
        $res = array();
        $res['stocks'] = (isset($inventory->stock))?$inventory->stock:0;
        $repair_oow_around_percentage = REPAIR_OOW_AROUND_PERCENTAGE;

        if ($this->session->userdata('userType') == 'service_center' || $this->session->userdata('userType') == "employee") {
            $repair_oow_around_percentage_vendor = $inventory->oow_around_margin / 100;
            $res['total'] = (float) (round($inventory->price * ( 1 + $repair_oow_around_percentage_vendor), 0) + (round($inventory->price * ( 1 + $repair_oow_around_percentage_vendor), 0) * ($inventory->gst_rate / 100)));
        } else {
            $res['total'] = (float) ($inventory->price + ($inventory->price * ($inventory->gst_rate / 100)));
        }
        if ($this->session->userdata('userType') == 'service_center') {
            $repair_oow_around_percentage_vendor1 = $inventory->oow_vendor_margin / 100;
            $totalpriceforsf = number_format((float) (round($inventory->price * ( 1 + $repair_oow_around_percentage_vendor1), 0) + (round($inventory->price * ( 1 + $repair_oow_around_percentage_vendor1), 0) * ($inventory->gst_rate / 100))), 2, '.', '');
            $res['customerTotal'] = number_format((float) (round($totalpriceforsf * ( 1 + $repair_oow_around_percentage), 0) + (round($totalpriceforsf * ( 1 + $repair_oow_around_percentage), 0) * ($repair_oow_around_percentage / 100))), 2, '.', '');
        } else {
            $totalpricepartner = (float) ($inventory->price + ($inventory->price * ($inventory->gst_rate / 100)));
            $repair_oow_around_percentage_vendor2 = $inventory->oow_vendor_margin + $inventory->oow_around_margin;
            $totpartner = $totalpricepartner + ($totalpricepartner * $repair_oow_around_percentage_vendor2 / 100);
            $res['customerTotal'] = (float) ($totpartner);
        }
        return $res;
    }
    
     
     /** 
     * @desc: This function is used to search inventory stock on warehouse using part number.
     * @param: void
     * @return:Json
     */
    function search_inventory_stock_available_warehouse() {
        
        $post = $this->get_post_data();
        if (($this->input->post('sender_entity_id') && $this->input->post('part_number'))) {
            $post[''] = array();
            $post['column_order'] = array();
            $post['column_search'] = array('part_name', 'part_number', 'type', 'services.services');
            $post['where'] = array('inventory_stocks.stock <> 0' => NULL);

            if ($this->input->post('sender_entity_id') && $this->input->post('sender_entity_type')) {
                $post['where']['inventory_master_list.entity_id'] = trim($this->input->post('sender_entity_id'));
                $post['where']['inventory_master_list.entity_type'] = trim($this->input->post('sender_entity_type'));
            }
            
            if ( !empty($this->input->post('part_number'))) {
                $post['where']['inventory_master_list.part_number'] = trim($this->input->post('part_number'));
            }
            
            if ($this->input->post('stock_is')) {
                unset($post['where']['inventory_stocks.stock <> 0']);
            }
            
            $select = "inventory_master_list.*,inventory_stocks.stock,inventory_stocks.pending_request_count,services.services,service_centres.company_name as wh_name, service_centres.is_micro_wh";

            $list = $this->inventory_model->get_inventory_stock_list($post, $select);
            $data = array();
            $no = $post['start'];
            $rowSums = array(
                "colCount" => 0,
                "colData" => array(
                    0 => 'Total',
                    7 => 0, //stock total occurs in col 7 in datatable
                    11 => 0.00, //total occurs in col 11 in datatable
                    12 => 0.00  //customer total in col 12
                )
            );
            foreach ($list as $inventory_list) {
                $no++;
                $row = $this->get_inventory_warehouse_stock_details_table($inventory_list, $no);
                $data[] = $row;

                $tSum = $this->get_inventory_warehouse_stock_total($inventory_list);
                $rowSums['colData'][7] += $tSum['stocks'];
                $rowSums['colData'][11] += $tSum['total'];
                $rowSums['colData'][12] += $tSum['customerTotal'];
                $rowSums["colCount"] = (count($row) > $rowSums['colCount']) ? count($row) : $rowSums["colCount"];
            }
            if (count($data) > 0) {
                $data[] = $this->draw_table_warehouse_footer($rowSums);
            }
            $post['length'] = -1;
            $countlist = $this->inventory_model->get_inventory_stock_list($post, "sum(inventory_stocks.stock) as stock");

            $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $this->inventory_model->count_all_inventory_stocks($post),
                "recordsFiltered" => $this->inventory_model->count_filtered_inventory_stocks($post),
                'stock' => $countlist[0]->stock,
                "data" => $data,
            );
        } else {
            $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                'stock' => 0,
                "data" => array(),
            );
        }
        echo json_encode($output);
    }

    /** 
     * @desc: This function is used to draw the footer of table.
     * @param: rowData
     * @return: Array
     */
    private function draw_table_warehouse_footer($rowData) {
        $res = array();
        for ($i = 0; $i < $rowData['colCount']; $i++) {
            $res[$i] = '';
            if (isset($rowData['colData'])) {
                if (isset($rowData['colData'][$i])) {
                    $res[$i] = (is_float($rowData['colData'][$i])) ? number_format($rowData['colData'][$i], 2) : $rowData['colData'][$i];
                }
            }
        }
        return $res;
    }
    
    /** 
     * @desc: This function is used to create table body to table.
     * @param: Array
     * @return: Array
     */
    
    private function get_inventory_warehouse_stock_total($inventory) {
        $res = array();
        $res['stocks'] = (isset($inventory->stock)) ? $inventory->stock : 0;
        $repair_oow_around_percentage = REPAIR_OOW_AROUND_PERCENTAGE;

        if ($this->session->userdata('userType') == 'service_center' || $this->session->userdata('userType') == "employee") {
            $repair_oow_around_percentage_vendor = $inventory->oow_around_margin / 100;
            $res['total'] = (float) (round($inventory->price * ( 1 + $repair_oow_around_percentage_vendor), 0) + (round($inventory->price * ( 1 + $repair_oow_around_percentage_vendor), 0) * ($inventory->gst_rate / 100)));
        } else {
            $res['total'] = (float) ($inventory->price + ($inventory->price * ($inventory->gst_rate / 100)));
        }
        if ($this->session->userdata('userType') == 'service_center') {
            $repair_oow_around_percentage_vendor1 = $inventory->oow_vendor_margin / 100;
            $totalpriceforsf = number_format((float) (round($inventory->price * ( 1 + $repair_oow_around_percentage_vendor1), 0) + (round($inventory->price * ( 1 + $repair_oow_around_percentage_vendor1), 0) * ($inventory->gst_rate / 100))), 2, '.', '');
            $res['customerTotal'] = number_format((float) (round($totalpriceforsf * ( 1 + $repair_oow_around_percentage), 0) + (round($totalpriceforsf * ( 1 + $repair_oow_around_percentage), 0) * ($repair_oow_around_percentage / 100))), 2, '.', '');
        } else {
            $totalpricepartner = (float) ($inventory->price + ($inventory->price * ($inventory->gst_rate / 100)));
            $repair_oow_around_percentage_vendor2 = $inventory->oow_vendor_margin + $inventory->oow_around_margin;
            $totpartner = $totalpricepartner + ($totalpricepartner * $repair_oow_around_percentage_vendor2 / 100);
            $res['customerTotal'] = (float) ($totpartner);
        }
        return $res;
    }
    
    /** 
     * @desc: This function is used to create table body to table.
     * @param: Array
     * @return: Array
     */
    
    private function get_inventory_warehouse_stock_details_table($inventory_list, $sn) {
        $row = array();

        $row[] = $sn;
        
        if($inventory_list->is_micro_wh == 1){
          $row[] =  $inventory_list->wh_name."(Micro-Warehouse)" ;  
        }else{
           $row[] =  $inventory_list->wh_name ; 
        }
        $row[] = '<span id="services_' . $inventory_list->inventory_id . '">' . $inventory_list->services . '</span>';
        $row[] = '<span id="type_' . $inventory_list->inventory_id . '">' . $inventory_list->type . '</span>';
        $row[] = '<span id="part_name_' . $inventory_list->inventory_id . '" style="word-break: break-all;">' . $inventory_list->part_name . '</span>';
        $row[] = '<span id="part_number_' . $inventory_list->inventory_id . '" style="word-break: break-all;">' . $inventory_list->part_number . '</span>';
        $row[] = $inventory_list->description;
        $row[] = $inventory_list->stock;
        $row[] = $inventory_list->pending_request_count;

        $repair_oow_around_percentage = REPAIR_OOW_AROUND_PERCENTAGE;
        if ($inventory_list->oow_around_margin > 0) {
            $repair_oow_around_percentage = $inventory_list->oow_around_margin / 100;
        }

        $repair_oow_around_percentage_vendor = 0;
        $sfbaseprice = 0;
        $repair_oow_around_percentage_vendor = $inventory_list->oow_around_margin / 100;


        if ($this->session->userdata('userType') == 'service_center' || $this->session->userdata('userType') == "employee") {

            $row[] = '<span id="basic_' . $inventory_list->inventory_id . '">' . number_format(($inventory_list->price * ( 1 + $repair_oow_around_percentage_vendor)), 2) . '</span>';
        } else {

            $row[] = '<span id="basic_' . $inventory_list->inventory_id . '">' . round($inventory_list->price, 2) . '</span>';
        }

        $row[] = '<span id="gst_rate_' . $inventory_list->inventory_id . '">' . $inventory_list->gst_rate . '</span>';

        if ($this->session->userdata('userType') == 'service_center' || $this->session->userdata('userType') == "employee") {

            $repair_oow_around_percentage_vendor = $inventory_list->oow_around_margin / 100;


            $row[] = '<span id="total_amount_' . $inventory_list->inventory_id . '">' . number_format((float) (round($inventory_list->price * ( 1 + $repair_oow_around_percentage_vendor), 0) + (round($inventory_list->price * ( 1 + $repair_oow_around_percentage_vendor), 0) * ($inventory_list->gst_rate / 100))), 2, '.', '') . "</span>";
        } else {

            $row[] = '<span id="total_amount_' . $inventory_list->inventory_id . '">' . number_format((float) ($inventory_list->price + ($inventory_list->price * ($inventory_list->gst_rate / 100))), 2, '.', '') . "</span>";
        }

        if ($this->session->userdata('userType') == 'service_center') {

            $repair_oow_around_percentage_vendor1 = $inventory_list->oow_vendor_margin / 100;

            $totalpriceforsf = number_format((float) (round($inventory_list->price * ( 1 + $repair_oow_around_percentage_vendor1), 0) + (round($inventory_list->price * ( 1 + $repair_oow_around_percentage_vendor1), 0) * ($inventory_list->gst_rate / 100))), 2, '.', '');

            $row[] = '<span id="total_amount_' . $inventory_list->inventory_id . '">' . number_format((float) (round($totalpriceforsf * ( 1 + $repair_oow_around_percentage), 0) + (round($totalpriceforsf * ( 1 + $repair_oow_around_percentage), 0) * ($repair_oow_around_percentage / 100))), 2, '.', '') . "</span>";
        } else {

            $totalpricepartner = number_format((float) ($inventory_list->price + ($inventory_list->price * ($inventory_list->gst_rate / 100))), 2, '.', '');
            $repair_oow_around_percentage_vendor2 = $inventory_list->oow_vendor_margin + $inventory_list->oow_around_margin;
            $totpartner = $totalpricepartner + ($totalpricepartner * $repair_oow_around_percentage_vendor2 / 100);

            $row[] = '<span id="total_amount_' . $inventory_list->inventory_id . '">' . number_format((float) ($totpartner), 2, '.', '') . "</span>";
        }

        return $row;
    }
    
    

    function inventory_stock_list() {
        $this->load->view('employee/header');
        $this->load->view('employee/inventory_stock_list');
    }

    function get_inventory_stocks_details_for_warehouse() {
        $post = $this->get_post_data();
        $post[''] = array();
        $post['column_order'] = array();
        $post['column_search'] = array('part_name', 'part_number', 'type', 'services.id', 'services.services');
        $post['where'] = array('inventory_master_list.entity_id' => trim($this->input->post('entity_id')), 'inventory_master_list.entity_type' => trim($this->input->post('entity_type')), 'inventory_stocks.stock <> 0' => NULL);
        if ($this->input->post('is_show_all')) {
            unset($post['where']['inventory_stocks.stock <> 0']);
        }
        $select = "inventory_master_list.*,inventory_stocks.stock,services.services,inventory_stocks.entity_id as receiver_entity_id,inventory_stocks.entity_type as receiver_entity_type";

        //RM Specific stocks
        $sfIDArray = array();
        if($this->session->userdata('user_group') == _247AROUND_RM || $this->session->userdata('user_group') == _247AROUND_ASM){
            $rm_id = $this->session->userdata('id');
            $rmServiceCentersData = $this->reusable_model->get_search_result_data("service_centres", "group_concat(id) as service_centres_id", array("(rm_id = '".$rm_id."' || asm_id = '".$rm_id."')" => NULL), NULL, NULL, NULL, NULL, NULL);
            $sfIDList = $rmServiceCentersData[0]['service_centres_id'];
            $sfIDArray = explode(",", $sfIDList);
        }

        $list = $this->inventory_model->get_inventory_stock_list($post, $select, $sfIDArray);
        $data = array();
        $no = $post['start'];
        foreach ($list as $inventory_list) {
            $no++;
            $row = $this->get_inventory_stocks_details_table($inventory_list, $no, false);
            $data[] = $row;
        }

        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->inventory_model->count_all_inventory_stocks($post),
            "recordsFiltered" => $this->inventory_model->count_filtered_inventory_stocks($post),
            "data" => $data,
        );

        echo json_encode($output);
    }


    private function get_inventory_stocks_details_table($inventory_list, $sn, $return_new_part_flag) {
        $row = array();
       ///echo $return_new_part_flag;
        $row[] = $sn;
        $row[] = '<span id="services_' . $inventory_list->inventory_id . '">' . $inventory_list->services . '</span>';
        $row[] = '<span id="type_' . $inventory_list->inventory_id . '">' . $inventory_list->type . '</span>';
        $row[] = '<span id="part_name_' . $inventory_list->inventory_id . '" style="word-break: break-all;">' . $inventory_list->part_name . '</span>';
        $row[] = '<span id="part_number_' . $inventory_list->inventory_id . '" style="word-break: break-all;">' . $inventory_list->part_number . '</span>';
        $row[] = '<span style="word-break: break-all;">' . $inventory_list->description . '</span>';
        $row[] = '<a href="' . base_url() . 'employee/inventory/show_inventory_ledger_list/0/' . $inventory_list->receiver_entity_type . '/' . $inventory_list->receiver_entity_id . '/' . $inventory_list->inventory_id . '" target="_blank" title="Get Ledger Details">' . $inventory_list->stock . '<a>';
        $row[] = $inventory_list->pending_request_count;

        if ($this->session->userdata('userType') == 'service_center' || $this->session->userdata('userType') == "employee") {
            $sfbaseprice =  ($inventory_list->price * ( 1 + $inventory_list->oow_around_margin/100));
            $sf_buying_price = $sfbaseprice *(1 + $inventory_list->gst_rate/100);
            $cbasic = ($inventory_list->price * ( 1 + ($inventory_list->oow_around_margin + $inventory_list->oow_vendor_margin)/100));
            $ctotal = $cbasic *(1 + $inventory_list->gst_rate/100);
        
            $row[] = '<span id="basic_' . $inventory_list->inventory_id . '">' .  sprintf("%.2f",$sfbaseprice). '</span>';
            $row[] = '<span id="gst_rate_' . $inventory_list->inventory_id . '">' . $inventory_list->gst_rate . '</span>';
            $row[] =  '<span id="total_amount_' . $inventory_list->inventory_id . '">'. sprintf("%.2f",$sf_buying_price). '</span>';
            $row[] = sprintf("%.2f",$ctotal);
            
        } else {
            $row[] = '<span id="basic_' . $inventory_list->inventory_id . '">' . sprintf("%.2f", $inventory_list->price) . '</span>';
            $row[] = '<span id="gst_rate_' . $inventory_list->inventory_id . '">' . $inventory_list->gst_rate . '</span>';
            $row[] =  '<span id="total_amount_' . $inventory_list->inventory_id . '">'. sprintf("%.2f", $inventory_list->price *(1 + $inventory_list->gst_rate/100)). '</span>';
        }
        

        if ($this->session->userdata('userType') == "employee") {
            $row[] = '<input style="max-width: 87px;" readonly type="number" name="quantity[' . $inventory_list->inventory_id . '][]" class="form-control" id="qty_' . $inventory_list->inventory_id . '" />';
            $row[] = '<a href="javascript:void(0)" class="btn btn-primary btn-md add_inventory_to_return" onclick="addnewpart(' . $inventory_list->inventory_id . ', ' . $inventory_list->stock . ' )">ADD</a>';
        }
        

        if ($this->session->userdata('userType') == 'service_center') {
            $row[] = '<a href="' . base_url() . 'service_center/inventory/alternate_inventory_list/' . $inventory_list->entity_id . '/' . $inventory_list->inventory_id . '/' . $inventory_list->service_id . '" target="_blank" class="btn btn-info">View</a>';
        } else {
            $row[] = '<a href="' . base_url() . 'partner/inventory/alternate_inventory_list/' . $inventory_list->inventory_id . '/' . $inventory_list->service_id . '" target="_blank" class="btn btn-info">View</a>';
        }
        return $row;
    }


    /*
     *  @desc : This function is used to get inventory part name
     *  @param : void
     *  @return : $res array() // consist response message and response status
     */

    function get_parts_name($is_sf_request = false) {

 
        $model_number_id = $this->input->post('model_number_id');
        $part_type = $this->input->post('part_type');
        $requested_inventory_id = $this->input->post('requested_inventory_id');
        $where = array();
        if (!empty($model_number_id)) {
            $where['model_number_id'] = $model_number_id;
        }

        if (!empty($part_type)) {
            $where['type'] = $part_type;
        }

        if ($this->input->post('service_id')) {
            $where['inventory_master_list.service_id'] = $this->input->post('service_id');
        }

        if (!empty($this->input->post('entity_id'))) {
            $where['inventory_master_list.entity_id'] = $this->input->post('entity_id');
            $where['inventory_master_list.entity_type'] = $this->input->post('entity_type');
        }
        
        $where['inventory_model_mapping.active'] = 1;
        if($is_sf_request){
            $where['inventory_model_mapping.bom_main_part'] = 1;
        }
        $inventory_type = $this->inventory_model->get_inventory_model_mapping_data('inventory_master_list.part_name,inventory_master_list.inventory_id,inventory_model_mapping.max_quantity,inventory_master_list.part_image', $where);

        if ($this->input->post('is_option_selected')) {
            $option = '<option selected disabled>Select Part Name</option>';
        } else {
            $option = '';
        }

 
        foreach ($inventory_type as $value) {
            $option .= "<option  data-maxquantity='" . $value['max_quantity'] . "'  data-inventory='" . $value['inventory_id'] . "' data-partimage='" . $value['part_image'] . "' value='" . $value['part_name'] . "'";
            if($requested_inventory_id == $value['inventory_id']){
                $option .= " selected ";
            }
            $option .=" > ";
            $option .= $value['part_name'] . "</option>";
        }

        echo $option;
    }
    
    /*
     *  @desc : This function is used to get inventory part number
     *  @param : void
     *  @return : echo string 
     */

    function get_part_number($is_sf_request = false) {

 
        $model_number_id = $this->input->post('model_number_id');
        $part_type = $this->input->post('part_type');
        $requested_inventory_id = $this->input->post('requested_inventory_id');
        $part_name = $this->input->post('part_name');
        $where = array();
        if (!empty($model_number_id)) {
            $where['model_number_id'] = $model_number_id;
        }

        if (!empty($part_type)) {
            $where['type'] = $part_type;
        }

        if(!empty($part_name)){
            $where['inventory_master_list.part_name'] = $part_name;
        }

        if ($this->input->post('service_id')) {
            $where['inventory_master_list.service_id'] = $this->input->post('service_id');
        }

        if (!empty($this->input->post('entity_id'))) {
            $where['inventory_master_list.entity_id'] = $this->input->post('entity_id');
            $where['inventory_master_list.entity_type'] = $this->input->post('entity_type');
        }
        
        $where['inventory_model_mapping.active'] = 1;
        if($is_sf_request){
            $where['inventory_model_mapping.bom_main_part'] = 1;
        }
        
        $inventory_type = $this->inventory_model->get_inventory_model_mapping_data('inventory_master_list.part_number,inventory_master_list.inventory_id,inventory_model_mapping.max_quantity,inventory_master_list.part_image', $where);
        $option = '';
        if (!empty($this->input->post('text_input'))) {
            if(is_array($inventory_type)){
                $option = $inventory_type[0]['part_number'];
            }else{
                $option = $inventory_type['part_number'];
            }
        }else{
            foreach ($inventory_type as $value) {
                $option .= "<option  data-maxquantity='" . $value['max_quantity'] . "'  data-inventory='" . $value['inventory_id'] . "' data-partimage='" . $value['part_image'] . "' value='" . $value['part_number'] . "'";
                if($requested_inventory_id == $value['inventory_id']){
                    $option .= " selected ";
                }
                $option .=" > ";
                $option .= $value['part_number'] . "</option>";
            }
        }

        echo $option;
    }

    /*
     *  @desc : This function is used to get inventory part name without using model mapping
     *  @param : void
     *  @return : $res array() 
     */
    function get_parts_name_without_model_mapping() {

        if ($this->input->post('is_option_selected')) {
            $option = '<option selected disabled>Select Part Name</option>';
        } else {
            $option = '';
        }
        $where = array();
        if (!empty($this->input->post('entity_id'))) {
            $where['inventory_master_list.entity_id'] = $this->input->post('entity_id');
            $where['inventory_master_list.entity_type'] = $this->input->post('entity_type');
            $where['inventory_master_list.service_id'] = $this->input->post('service_id');
        }
        if (!empty($where)) {
            $inventory_master_list = $this->inventory_model->get_inventory_without_model_mapping_data('inventory_master_list.part_number,inventory_master_list.inventory_id', $where);
        }

        foreach ($inventory_master_list as $value) {
            $option .= "<option data-inventory='" . $value['inventory_id'] . "' value='" . $value['part_number'] . "'";

            $option .= " > ";
            $option .= $value['part_number'] . "</option>";
        }

        echo $option;
    }
    
    /*
     *  @desc : This function is used to get inventory part name
     *  @param : void
     *  @return : $res array() // consist response message and response status
     */
    function get_inventory_price() {

        $model_number_id = $this->input->post('model_number_id');
        $part_name = $this->input->post('part_name');
        if (!empty($model_number_id)) {
            $part_number_details = $this->inventory_model->get_inventory_model_mapping_data('inventory_master_list.part_number', array('model_number_id' => $model_number_id, 'part_name' => $part_name,'inventory_model_mapping.active' => 1));

            if (!empty($part_number_details)) {
                $part_number = $part_number_details[0]['part_number'];
            } else {
                $part_number = '';
            }
        } else {
            $part_number = $this->input->post('part_number');
        }

        $entity_id = $this->input->post('entity_id');
        $entity_type = $this->input->post('entity_type');
        $service_id = $this->input->post('service_id');

        if ($part_number && $entity_id && $entity_type && $service_id) {
            $where = array('entity_id' => $entity_id, 'entity_type' => $entity_type, 'service_id' => $service_id, 'part_number' => $part_number);
            $inventory_details = $this->inventory_model->get_inventory_master_list_data('inventory_master_list.price as price,inventory_master_list.inventory_id, hsn_code,gst_rate, inventory_master_list.oow_around_margin, inventory_master_list.service_id', $where);
            if (!empty($inventory_details)) {
                if($this->session->userdata('userType')=='service_center'){
                $select_stock = "*";
                $service_centres_id=$this->session->userdata('service_center_id');
                $where_stock=array('entity_id' => $service_centres_id, 'entity_type' =>_247AROUND_SF_STRING, 'inventory_id' => $inventory_details[0]['inventory_id']);
                $stock_details = $this->inventory_model->get_inventory_stock_count_details($select_stock,$where_stock);

                 if (!empty($stock_details)) {
                 $data['total_stock'] = ($stock_details[0]['stock'] - $stock_details[0]['pending_request_count']);
                 } else {
                 $data['total_stock'] = 0;
                 }
                }
                $data['price'] = $inventory_details[0]['price'];
                $data['inventory_id'] = $inventory_details[0]['inventory_id'];
                $data['gst_rate'] = $inventory_details[0]['gst_rate'];
                $data['hsn_code'] = $inventory_details[0]['hsn_code'];
                $data['oow_around_margin'] = $inventory_details[0]['oow_around_margin'];
                $data['service_id'] = $inventory_details[0]['service_id'];
            } else {
                $data['price'] = '';
                $data['inventory_id'] = '';
                $data['gst_rate'] = '';
                $data['hsn_code'] = '';
                $data['oow_around_margin'] = '';
                $data['total_stock'] = 0;
            }
        } else {
            $data['price'] = '';
            $data['inventory_id'] = '';
            $data['gst_rate'] = '';
            $data['hsn_code'] = '';
            $data['oow_around_margin'] = '';
            $data['total_stock'] = 0;

            //Getting template from Database
            $template = $this->booking_model->get_booking_email_template("inventory_details_mapping_not_found");

            if (!empty($template)) {
                $data = array();
                $data['partner_id'] = $entity_id;
                $data['model_number_id'] = $model_number_id;
                $data['service_id'] = $service_id;
                $data['part_name'] = $part_name;

                $body = vsprintf($template[0], $data);

                $this->notify->sendEmail($template[2], $template[1], $template[3], "", $template[4], $body, "", 'inventory_not_found');
            }
        }

        echo json_encode($data);
    }

    /**
     *  @desc : This function is used to show inventory count on SF CRM
     *  @param : void
     *  @return : $response array()
     */
    function get_sf_notification_data() {
        $this->checkSFSession();
        $response = array();
        if ($this->session->userdata('service_center_id')) {
            $sf_id = $this->session->userdata('service_center_id');
            $where = "spare_parts_details.partner_id = '" . $sf_id . "' AND  spare_parts_details.entity_type =  '" . _247AROUND_SF_STRING . "' AND status = '" . SPARE_PARTS_REQUESTED . "' "
                    . " AND booking_details.current_status IN ('" . _247AROUND_PENDING . "', '" . _247AROUND_RESCHEDULED . "') "
                    . " AND wh_ack_received_part != 0 ";

            $inventory_data = $this->service_centers_model->get_spare_parts_on_group($where, "spare_parts_details.booking_id", "spare_parts_details.booking_id", $this->session->userdata('service_center_id'));

            $brackets_data = $this->inventory_model->get_filtered_brackets('count(id) as total_brackets', array('order_given_to' => $this->session->userdata('service_center_id'), 'is_shipped' => 0));

            $response['inventory'] = count($inventory_data);
            $response['brackets'] = $brackets_data[0]['total_brackets'];
        }


        echo json_encode($response);
    }

    /**
     * @desc This is used to update spare related field. Just pass field name, value and spare ID
     */
    function update_spare_parts_column() {
        ob_end_clean();
        $this->form_validation->set_rules('data', 'Data', 'required');
        $this->form_validation->set_rules('id', 'id', 'required');
        $this->form_validation->set_rules('column', 'column', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post('data');
            $id = $this->input->post('id');
            $column = $this->input->post('column');
            if($column=='shipped_date'){
                $data = date('Y-m-d', strtotime($data));
            }
            $booking_id = $this->input->post('booking_id');

            //If we are updating awb_number and awb_number preexist then update shipped_date and courier pic , courier name etc
            if($column=='awb_by_partner'){
                $allSpares = $this->service_centers_model->get_spare_parts_booking(array('spare_parts_details.awb_by_partner' => $this->input->post('data')), "spare_parts_details.id,spare_parts_details.shipped_date,spare_parts_details.courier_pic_by_partner,spare_parts_details.courier_name_by_partner,spare_parts_details.awb_by_partner");
                $current_spare = $this->service_centers_model->get_spare_parts_booking(array('spare_parts_details.id' => $this->input->post('id')), "spare_parts_details.awb_by_partner");
                $other_fields_to_update = array();
                if(!empty($allSpares) && !empty($current_spare)){
                    //$other_fields_to_update['shipped_date'] = $allSpares[0]['shipped_date'];
                    $other_fields_to_update['courier_pic_by_partner'] = $allSpares[0]['courier_pic_by_partner'];
                    $other_fields_to_update['courier_name_by_partner'] = $allSpares[0]['courier_name_by_partner'];
                    $current_spare_awb = $current_spare[0]['awb_by_partner'];
                    if($allSpares[0]['awb_by_partner']!=$current_spare_awb){
                        $this->service_centers_model->update_spare_parts(array('id' => $id), $other_fields_to_update);
                    }
                }
                $agent_id = $this->session->userdata('id');
                $agent_name = $this->session->userdata('employee_id');
                $entity_id = _247AROUND;
                $track_entity_type = _247AROUND_EMPLOYEE_STRING;
                if (!empty($current_spare[0]['awb_by_partner']) && !empty($this->input->post('data')) && $current_spare[0]['awb_by_partner'] != $this->input->post('data')) {
                    $tracking_details = array('spare_id' => $id, 'action' => "AWB number changed from " . $current_spare[0]['awb_by_partner'] . " to " . $this->input->post('data'), 'remarks' => 'Partner AWB changed.', 'agent_id' => $agent_id, 'entity_id' => $entity_id, 'entity_type' => $track_entity_type);
                    $this->service_centers_model->insert_spare_tracking_details($tracking_details); // Insert into spare part tracking History
                }
            }

            if($column!='shipped_date'){
                $this->service_centers_model->update_spare_parts(array('id' => $id), array($column => $data));
            }
            // if serial number is changed , update in booking_unit_details table also.
            if(!empty($booking_id) && $column == 'serial_number')
            {
                $this->booking_model->update_booking_unit_details($booking_id, array($column => $data));
            }
            //Updating Shipment Date
            if (!empty($this->input->post('awb_number')) && $column == 'shipped_date') {
                $awb_number = $this->input->post('awb_number');
                $this->inventory_model->update_courier_company_invoice_details(array('awb_number' => $awb_number), array('shippment_date' => date('Y-m-d H:i:s', strtotime($data)))); // Update shipment Date on Courier company invoice detail table
                $allSpares = $this->service_centers_model->get_spare_parts_booking(array('awb_by_partner' => $awb_number), "spare_parts_details.id,spare_parts_details.shipped_date"); // Get all Spare parts in same Awb number and update All spare parts
                if (!empty($allSpares)) {
                    if ($this->session->userdata('employee_id')) {
                        $agent_id = $this->session->userdata('id');
                        $agent_name = $this->session->userdata('employee_id');
                        $entity_id = _247AROUND;
                        $track_entity_type = _247AROUND_EMPLOYEE_STRING;
                    } else if ($this->session->userdata('partner_id')) {
                        $agent_id = $this->session->userdata('agent_id');
                        $agent_name = $this->session->userdata('partner_name');
                        $entity_id = $this->session->userdata('partner_id');
                        $track_entity_type = _247AROUND_PARTNER_STRING;
                    } else if ($this->session->userdata('service_center_id')) {
                        $agent_id = $this->session->userdata('service_center_agent_id');
                        $agent_name = $this->session->userdata('service_center_name');
                        $entity_id = $this->session->userdata('service_center_id');
                        $track_entity_type = _247AROUND_SF_STRING;
                    }
                    foreach ($allSpares as $key => $spare_ids) {
                        if ($spare_ids['shipped_date'] != $data) {
                            $this->service_centers_model->update_spare_parts(array('id' => $spare_ids['id']), array($column => $data));
                            $tracking_details = array('spare_id' => $spare_ids['id'], 'action' => 'Shipment Date Changed to ' . $data, 'remarks' => '', 'agent_id' => $agent_id, 'entity_id' => $entity_id, 'entity_type' => $track_entity_type);
                            $this->service_centers_model->insert_spare_tracking_details($tracking_details); // Insert into spare part tracking History
                        }
                    }
                }
            }
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
    function get_parts_type($is_sf_request = false) {

        $model_number_id = $this->input->post('model_number_id');
        $where = array('model_number_id' => $model_number_id,'inventory_model_mapping.active' =>1);
        if($is_sf_request){
            $where['bom_main_part']= 1;
        }
                
        $inventory_type = $this->inventory_model->get_inventory_model_mapping_data('inventory_master_list.type,inventory_master_list.service_id', $where);
  
        $option = '<option selected disabled>Select Part Type</option>';

        foreach ($inventory_type as $value) {
            $option .= "<option data-service_id='" . $value['service_id'] . "' value='" . $value['type'] . "'";
            $option .=" > ";
            $option .= $value['type'] . "</option>";
        }

        echo $option;
    }

    /**
     * @desc This is used to upload spare related image. It is used from Booking view details page.
     */
    function processUploadSpareItem() {
        log_message('info', __METHOD__ . " " . print_r($this->input->post(), TRUE) . " " . print_r($_FILES, true));
        $allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
        $spareID = $this->input->post('spareID');
        $bookingID = $this->input->post('booking_id');
        $spareColumn = $this->input->post('spareColumn');
        if (!empty($this->input->post('directory_name')) && $this->input->post('directory_name') == 'courier-pod') {
            $file_dir = "courier-pod";
        }else if (!empty($this->input->post('directory_name')) && $this->input->post('directory_name') == 'purchase-invoices') {
            $file_dir = "purchase-invoices";
        }else if (!empty($this->input->post('directory_name'))) {
            $file_dir = "vendor-partner-docs";
        } else {
            $file_dir = "misc-images";
        }

        $defective_parts_pic = $this->miscelleneous->upload_file_to_s3($_FILES["file"], $spareColumn, $allowedExts, $bookingID, $file_dir, "sp_parts");
        if ($defective_parts_pic) {
            if($spareColumn != 'courier_pic_by_partner' && $spareColumn != 'courier_pod_file'){
                $this->service_centers_model->update_spare_parts(array('id' => $spareID), array($spareColumn => $defective_parts_pic));
            }
            // if serial number image is changed , update in booking_unit_details table also.
            if(!empty($bookingID) && $spareColumn == 'serial_number_pic')
            {
                $this->booking_model->update_booking_unit_details($bookingID, array($spareColumn => $defective_parts_pic));
            }
            if (!empty($this->input->post('awb_number')) && $spareColumn == 'courier_pic_by_partner') {
                $awb_number = $this->input->post('awb_number');
                $this->inventory_model->update_courier_company_invoice_details(array('awb_number' => $awb_number), array('courier_invoice_file' => $defective_parts_pic)); // Update Courier file on Courier company invoice detail table
                $allSpares = $this->service_centers_model->get_spare_parts_booking(array('awb_by_partner' => $awb_number), "spare_parts_details.id,spare_parts_details.shipped_date"); // Get all Spare parts in same Awb number and update All spare parts
                if (!empty($allSpares)) {
                    if ($this->session->userdata('employee_id')) {
                        $agent_id = $this->session->userdata('id');
                        $agent_name = $this->session->userdata('employee_id');
                        $entity_id = _247AROUND;
                        $track_entity_type = _247AROUND_EMPLOYEE_STRING;
                    } else if ($this->session->userdata('partner_id')) {
                        $agent_id = $this->session->userdata('agent_id');
                        $agent_name = $this->session->userdata('partner_name');
                        $entity_id = $this->session->userdata('partner_id');
                        $track_entity_type = _247AROUND_PARTNER_STRING;
                    } else if ($this->session->userdata('service_center_id')) {
                        $agent_id = $this->session->userdata('service_center_agent_id');
                        $agent_name = $this->session->userdata('service_center_name');
                        $entity_id = $this->session->userdata('service_center_id');
                        $track_entity_type = _247AROUND_SF_STRING;
                    }
                    foreach ($allSpares as $key => $spare_ids) {
                        $this->service_centers_model->update_spare_parts(array('id' => $spare_ids['id']), array($spareColumn => $defective_parts_pic));
                        $tracking_details = array('spare_id' => $spare_ids['id'], 'action' => 'Courier File Changed.', 'remarks' => '', 'agent_id' => $agent_id, 'entity_id' => $entity_id, 'entity_type' => $track_entity_type);
                        $this->service_centers_model->insert_spare_tracking_details($tracking_details); // Insert into spare part tracking History
                    }
                }
            }
            if (!empty($this->input->post('awb_number')) && $spareColumn == 'courier_pod_file') {
                $awb_number = $this->input->post('awb_number');
                $this->inventory_model->update_courier_company_invoice_details(array('awb_number' => $awb_number), array('courier_pod_file' => $defective_parts_pic)); // Update Courier POD File on Courier company invoice detail table
            }
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
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'service_center') && !empty($this->session->userdata('service_center_id')) && !empty($this->session->userdata('is_sf'))) {
            return TRUE;
        } else {
            log_message('info', __FUNCTION__ . " Session Expire for Service Center");
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
    function update_inventory_on_cancel_parts($spare_id, $booking_id, $old_status) {
        log_message("info", __METHOD__ . ' spare id ' . $spare_id . ' booking id' . $booking_id);
        $spare_details = $this->service_centers_model->get_spare_parts_booking(array('spare_parts_details.id' => $spare_id), 'spare_parts_details.booking_unit_details_id,spare_parts_details.shipped_inventory_id,spare_parts_details.partner_id,spare_parts_details.entity_type,spare_parts_details.requested_inventory_id');

        //update status in booking unit details to cancel
        if (!empty($spare_details) && !empty($spare_details[0]['booking_unit_details_id'])) {
            $update_unit_details = $this->booking_model->update_booking_unit_details_by_any(array('id' => $spare_details[0]['booking_unit_details_id']), array('booking_status' => _247AROUND_CANCELLED, 'ud_closed_date' => date("Y-m-d H:i:s")));
            if ($update_unit_details) {
                log_message("info", "Unit Details Updated Successfully");
                $booking_unit_details = $this->booking_model->get_unit_details(array('booking_id' => $booking_id, "booking_status NOT IN ('" . _247AROUND_CANCELLED . "')" => NULL), false, 'SUM(customer_net_payable) as customer_net_payable');
                if (!empty($booking_unit_details)) {
                    $booking_details = $this->booking_model->getbooking_history($booking_id);
                    $upcountry_price = 0;
                    if (!empty($booking_details) && $booking_details[0]['is_upcountry'] = 1 && $booking_details[0]['upcountry_paid_by_customer'] = 1) {
                        $upcountry_price = $booking_details[0]['partner_upcountry_rate'] * $booking_details[0]['upcountry_distance'];
                    }

                    $booking['amount_due'] = $booking_unit_details[0]['customer_net_payable'] + $upcountry_price;

                    // Update Booking Table
                    $this->booking_model->update_booking($booking_id, $booking);
                } else {
                    log_message('info', 'unit details not found');
                }
            } else {
                log_message("info", "Error in updating unit details");
            }
        } else {
            log_message('info', 'details not found ' . print_r($spare_details, true));
        }

        //We do not open cancel spare
//        //if stock consumend them increase the inventory stock
//        if (!empty($spare_details) && !empty($spare_details[0]['shipped_inventory_id'])) {
//            log_message('info','Spare part cancelled. Update spare details. '.  print_r($spare_details,true));
//            $data['receiver_entity_id'] = $spare_details[0]['partner_id'];
//            $data['receiver_entity_type'] = $spare_details[0]['entity_type'];
//            $data['stock'] = 1;
//            $data['booking_id'] = $booking_id;
//            $data['agent_id'] = $this->session->userdata('id');
//            $data['agent_type'] = _247AROUND_EMPLOYEE_STRING;
//            $data['is_wh'] = TRUE;
//            $data['inventory_id'] = $spare_details[0]['shipped_inventory_id'];
//            $data['is_cancel_part'] = TRUE;
//            $this->miscelleneous->process_inventory_stocks($data);
//        }

        if (!empty($spare_details) && $old_status == SPARE_PARTS_REQUESTED && $spare_details[0]['entity_type'] == _247AROUND_SF_STRING && !empty($spare_details[0]['requested_inventory_id'])) {
            $this->inventory_model->update_pending_inventory_stock_request($spare_details[0]['entity_type'], $spare_details[0]['partner_id'], $spare_details[0]['requested_inventory_id'], -1);
        }

        //create job card
        $this->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);
    }

    /**
     *  @desc : This function is used to show the view so that partner can tag spare invoice send by him
     *  @param : void
     *  @return :void
     */
    function tag_spare_invoice_send_by_partner() {
        $this->checkUserSession();
        $this->miscelleneous->load_nav_header();
        $data['courier_details'] = $this->inventory_model->get_courier_services('*');                     
        $data['saas'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);                
        $this->load->view("employee/tag_spare_invoice_send_by_partner", $data);
                        
    }

    /**
     *  @desc : This function is used to get inventory part model number
     *  @param : void
     *  @return : $res array() // consist response message and response status
     */
    function get_part_model_number() {
        $post['length'] = -1;
        $post['where'] = array('entity_id' => $this->input->get('entity_id'), 'entity_type' => $this->input->get('entity_type'), 'service_id' => $this->input->get('service_id'));
        $post['order'] = array(array('column' => 0, 'dir' => 'ASC'));
        $post['column_order'] = array('model_number');
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
     *  @desc : This function is used to Process send MSL data 
     *  @param : void
     *  @return : $res JSON 
     */

    function process_msl_upload_excel() {
        $input_d = file_get_contents('php://input');
        $_POST = json_decode($input_d, TRUE);
        $_FILES = $_POST['files'];
        $this->session->set_userdata($_POST['session']);
        if (!(json_last_error() === JSON_ERROR_NONE)) {
            log_message('info', __METHOD__ . ":: Invalid JSON", true);
        } else {

            $this->process_spare_invoice_tagging();
        }
    }

    /**
     *  @desc : This function is used to insert spare data send by partner to warehouse
     *  @param : void
     *  @return : $res JSON // consist response message and response status
     */

   function process_spare_invoice_tagging() {
        log_message("info", __METHOD__ . json_encode($this->input->post(), true));
//        $str = '{"is_wh_micro":"2","247around_gst_number":"09AAFCB1281J1ZM","partner_id":"247130","wh_id":"870","awb_number":"12587455","courier_name":"gati-kwe","courier_shipment_date":"04/07/2019","from_gst_number":"7","part":[{"shippingStatus":"1","service_id":"37","part_name":"TRAY,BOTTOM,ER180I,INSTA","part_number":"1100023151","booking_id":"","quantity":"1","part_total_price":"158.25","hsn_code":"39239090","gst_rate":"18","inventory_id":"6011"},{"shippingStatus":"1","service_id":"37","part_name":"LEG,ADJUSTABLE,27MM L,ER180I,INSTA","part_number":"1100028374","booking_id":"","quantity":"2","part_total_price":"15","hsn_code":"84189900","gst_rate":"18","inventory_id":"7463"}],"partner_name":" Videocon","wh_name":" Amritsar Baldev Electronics - (Micro Warehouse) ","dated":"04/07/2019","sender_entity_type":"vendor","sender_entity_id":"15","invoice_tag":"MSL","transfered_by":"2"}';
        //        $_POST = json_decode($str, true);  
//  
        $invoice_file_required = $this->input->post('invoice_file');

        if ($invoice_file_required) {
            $invoice_file_required = 0;
        } else {
            $invoice_file_required = 1;
        }
        $partner_id = $this->input->post('partner_id');
        $invoice_id = $this->input->post('invoice_id');
        $invoice_dated = $this->input->post('dated');
        $wh_id = $this->input->post('wh_id');
        $is_wh_micro = $this->input->post('is_wh_micro');
        $invoice_amount = $this->input->post('invoice_amount');
        $awb_number = $this->input->post('awb_number');
        $courier_name = $this->input->post('courier_name');
        $courier_shipment_date = $this->input->post('courier_shipment_date');
        $partner_name = trim($this->input->post('partner_name'));
        $wh_name = trim($this->input->post('wh_name'));
        $transfered_by = $this->input->post('transfered_by');
        $is_defective_part_return_wh = trim($this->input->post('is_defective_part_return_wh'));
        $from_gst_number = $this->input->post("from_gst_number");
        $to_gst_number = $this->input->post("to_gst_number");
        $box_count = $this->input->post("box_count");
        $small_box_count = $this->input->post("small_box_count");
        $tcs_rate = $this->input->post("tcs_rate");
        $req = TRUE;
        if (!empty($partner_id) && !empty($invoice_dated) && !empty($wh_id) && !empty($awb_number) && !empty($courier_name)) {
            if ($transfered_by == MSL_TRANSFERED_BY_PARTNER && (empty($invoice_id) || empty($invoice_amount))) {
                $req = FALSE;
            } else {
                $req = TRUE;
            }

            if ($transfered_by == MSL_TRANSFERED_BY_PARTNER) {
                $sender_enity_id = $partner_id;
                $sender_entity_type = _247AROUND_PARTNER_STRING;
            } else {
                $sender_entity_type = $this->input->post("sender_entity_type");
                $sender_enity_id = $this->input->post("sender_entity_id");
            }
            if ($req) {

                $parts_details = $this->input->post('part');


                if (!empty($parts_details)) {
                    /**
                     * Check if inventory_id is zero.
                     */
                    $inventory_ids = array_column($parts_details, 'inventory_id');
                    if(!in_array('0', $inventory_ids)) {
                                        
                    
                    if ($invoice_file_required) {

                        $invoice_file = $this->check_msl_invoice_id($transfered_by, $invoice_id);
                    } else {

                        $invoice_file['status'] = true;
                        $invoice_file['message'] = 'Invoice By Excel';
                    }
                    if ($invoice_file['status']) {

                        if ($invoice_file) {
                            $courier_file = $this->upload_spare_courier_file($_FILES);
                        } else {
                            $courier_file['status'] = true;
                            $invoice_file['message'] = 'Invoice By Excel';
                        }
                        $not_updated_data = array();
                        if ($courier_file['status']) {

                            $template1 = array(
                                'table_open' => '<table border="1" cellpadding="2" cellspacing="0" class="mytable">'
                            );

                            $this->table->set_template($template1);

                            $this->table->set_heading(array('Part Name', 'Part Number', 'Quantity', 'Booking Id', 'Basic Price', 'GST Rate', 'HSN Code'));
                            
                            if ($this->session->userdata('service_center_id')) {
                                $agent_id = $this->session->userdata('service_center_id');
                                $action_agent_id = $this->session->userdata('service_center_agent_id');
                                $action_entity_id = $this->session->userdata('service_center_id');
                                $agent_type = _247AROUND_SF_STRING;
                            } else if ($this->session->userdata('id')) {
                                $agent_id = $this->session->userdata('id');
                                $action_agent_id = $this->session->userdata('id');
                                $action_entity_id = _247AROUND;
                                $agent_type = _247AROUND_EMPLOYEE_STRING;
                            } else {
                                $agent_id = $this->session->userdata('partner_id');
                                $action_agent_id = $this->session->userdata('agent_id');
                                $action_entity_id = $this->session->userdata('partner_id');
                                $agent_type = _247AROUND_PARTNER_STRING;
                            }
                            $entity_details = $this->partner_model->getpartner_details("district, state", array('partners.id' => $partner_id));
                            $from_city = $entity_details[0]['district'];
                            $from_state = $entity_details[0]['state'];
                            if (!empty($from_gst_number)) {
                                $partner_gst = $this->inventory_model->get_entity_gst_data("entity_gst_details.*", array('entity_gst_details.id' => $from_gst_number));
                                $partner_state_code = $partner_gst[0]['state'];
                                $from_city = $partner_gst[0]['city'];
                                $from_state = $this->invoices_model->get_state_code(array('state_code' => $partner_state_code))[0]['state'];
                            } else {
                                $partner_state_code = $this->invoices_model->get_state_code(array('state' => $entity_details[0]['state']))[0]['state_code'];
                            }

                            $around_gst = $this->inventory_model->get_entity_gst_data("entity_gst_details.*", array('entity_gst_details.id' => $to_gst_number));

                            if ($around_gst[0]['state'] == $partner_state_code) {
                                $c_s_gst = true;
                            } else {
                                $c_s_gst = false;
                            }

                            $wh_details = $this->vendor_model->getVendorDetails("district, state, primary_contact_email as official_email", array('service_centres.id' => $wh_id));
                            $to_city = $wh_details[0]['district'];
                            $to_state = $wh_details[0]['state'];
                                
                            //$c_s_gst = $this->invoices_model->check_gst_tax_type($entity_details[0]['state']);
                            $booking_id_array = array_column($parts_details, 'booking_id');
                            $tqty = 0;
                            $total_basic_amount = 0;
                            $total_cgst_tax_amount = $total_sgst_tax_amount = $total_igst_tax_amount = 0;
                            $invoice = array();
                            $invoicebreakup = array();
                            //update courier details
                            $courier_data = array();
                            $courier_data['sender_entity_id'] = $sender_enity_id;
                            $courier_data['sender_entity_type'] = $sender_entity_type;
                            $courier_data['receiver_entity_id'] = $wh_id;
                            $courier_data['receiver_entity_type'] = _247AROUND_SF_STRING;
                            $courier_data['AWB_no'] = $awb_number;
                            $courier_data['courier_name'] = $courier_name;
                            $courier_data['create_date'] = date('Y-m-d H:i:s');
                            $courier_data['quantity'] = count($booking_id_array);
                            $courier_data['bill_to_partner'] = $partner_id;
                            $courier_data['status'] = COURIER_DETAILS_STATUS;
                            if (!empty($booking_id_array)) {
                                $courier_data['booking_id'] = implode(",", $booking_id_array);
                            }

                            if (!empty($courier_file['message'])) {
                                $courier_data['courier_file'] = $courier_file['message'];
                            }

                            if (!empty($courier_shipment_date)) {
                                $courier_data['shipment_date'] = date("Y-m-d", strtotime(str_replace('/', '-', $courier_shipment_date)));
                            }

                            $exist_courier_details = $this->inventory_model->get_generic_table_details('courier_company_invoice_details', '*', array('awb_number' => $awb_number), array());

                            if (!empty($exist_courier_details)) {
                                $courier_company_details_id = trim($exist_courier_details[0]['id']);
                               
                                //$awb_by_wh = trim($exist_courier_details[0]['awb_number']);
                                //$courier_name_by_wh = trim($exist_courier_details[0]['company_name']);
                                //$courier_price_by_wh = $exist_courier_details[0]['courier_charge'];
                            } else {
                                if ($transfered_by != MSL_TRANSFERED_BY_PARTNER) {
                                    $vendor_details = $this->vendor_model->getVendorDetails("district, state", array('service_centres.id' => $sender_enity_id));
                                    $from_city = $vendor_details[0]['district'];
                                    $from_state = $vendor_details[0]['state'];
                                }

                                $awb_data = array(
                                    'awb_number' => trim($awb_number),
                                    'company_name' => trim($courier_name),
                                    'partner_id' => $partner_id,
                                    'booking_id' => (!empty($booking_id_array) ? implode(",", $booking_id_array) : ''),
                                    'courier_charge' => '0.00',
                                    'box_count' => $box_count, //trim($this->input->post('shipped_spare_parts_boxes_count')),
                                    'small_box_count' => $small_box_count,
                                    'billable_weight' => '0.00', //trim($billable_weight),
                                    'actual_weight' => '0.00', //trim($billable_weight),
                                    'basic_billed_charge_to_partner' => '0.00',
                                    'courier_invoice_file' => $courier_file['message'],
                                    'shippment_date' => date("Y-m-d", strtotime(str_replace('/', '-', $courier_shipment_date))), //defective_part_shipped_date
                                    'created_by' => 1,
                                    'is_exist' => 1,
                                    'sender_city' => $from_city,
                                    'receiver_city' => $to_city,
                                    'sender_state' => $from_state,
                                    'receiver_state' => $to_state
                                );

                                $courier_company_details_id = $this->service_centers_model->insert_into_awb_details($awb_data);
                            }

                            $insert_courier_details = $this->inventory_model->insert_courier_details($courier_data);

                            if (!empty($insert_courier_details)) {
                                log_message('info', 'Courier Details added successfully.');

                                foreach ($parts_details as $value) {
                                    $request_type = '';
                                    if (isset($value['request_type']) && !empty($value['request_type'])) {
                                        $request_type = trim($value['request_type']);
                                    } else {
                                        $request_type = REPAIR_OOW_TAG;
                                    }
                                    if ($value['shippingStatus'] == 1) {
                                        //Parts shipped
                                        $this->table->add_row($value['part_name'], $value['part_number'], $value['quantity'], $value['booking_id'], $value['part_total_price'], $value['gst_rate'], $value['hsn_code']);

                                        $tqty += $value['quantity'];

                                        $invoice_annexure = $this->inventory_invoice_data($invoice_id, $c_s_gst, $value);
                                        $invoice_annexure['from_gst_number'] = $this->input->post("from_gst_number");
                                        $invoice_annexure['to_gst_number'] = $this->input->post("to_gst_number");

                                        $inventory_master_data = $this->inventory_model->get_inventory_master_list_data("*", array('inventory_id' => $value['inventory_id']));
                                        if (!empty($inventory_master_data) && ((floatval($inventory_master_data[0]['price']) != floatval($invoice_annexure['rate'])) || ($inventory_master_data[0]['hsn_code'] !== $value['hsn_code']) || ($inventory_master_data[0]['gst_rate'] !== $value['gst_rate']))) {
                                            $this->inventory_model->update_inventory_master_list_data(array('inventory_id' => $value['inventory_id']), array('price' => $invoice_annexure['rate'], 'hsn_code' => $value['hsn_code'], 'gst_rate' => $value['gst_rate'], 'is_invoice' => '1'));
                                        }
                                        $to_gst_number = $invoice_annexure['to_gst_number'];
                                        array_push($invoicebreakup, $invoice_annexure);
                                        $invoice_annexure['booking_id'] = trim($value['booking_id']);
                                        array_push($invoice, $invoice_annexure);
                                        unset($invoice_annexure['from_gst_number']);
                                        unset($invoice_annexure['to_gst_number']);
                                        unset($invoice_annexure['booking_id']); 

                                        $total_basic_amount += $invoice_annexure['taxable_value'];
                                        $total_cgst_tax_amount += $invoice_annexure['cgst_tax_amount'];
                                        $total_sgst_tax_amount += $invoice_annexure['sgst_tax_amount'];
                                        $total_igst_tax_amount += $invoice_annexure['igst_tax_amount'];

                                        If ($is_wh_micro == 1) {
                                            $ledger_data = array();

                                            $ledger_data['receiver_entity_id'] = $wh_id;
                                            $ledger_data['receiver_entity_type'] = _247AROUND_SF_STRING;
                                            $ledger_data['sender_entity_id'] = $sender_enity_id;
                                            $ledger_data['sender_entity_type'] = $sender_entity_type;
                                            $ledger_data['inventory_id'] = $value['inventory_id'];
                                            $ledger_data['quantity'] = $value['quantity'];
                                            $ledger_data['agent_id'] = $agent_id;
                                            $ledger_data['agent_type'] = $agent_type;
                                            $ledger_data['booking_id'] = trim($value['booking_id']);
                                            $ledger_data['invoice_id'] = $invoice_id;
                                            $ledger_data['is_wh_ack'] = 0;
                                            $ledger_data['courier_id'] = $courier_company_details_id;
                                            $ledger_data['is_wh_micro'] = $is_wh_micro;
                                            if(!empty($value['spare_id'])) {
                                                $ledger_data['spare_id'] = $value['spare_id'];
                                            }
                                            $insert_id = $this->inventory_model->insert_inventory_ledger($ledger_data);
                                            if (isset($value['request_type']) && !empty($value['request_type'])) {
                                                $ledger_data['request_type'] = trim($value['request_type']);
                                            }

                                            $ledger_data['is_defective_part_return_wh'] = $is_defective_part_return_wh;

                                            if ($insert_id) {
                                                log_message("info", "Ledger details added successfully");
                                                $this->move_inventory_to_warehouse($ledger_data, $value, $wh_id, $is_wh_micro, $action_agent_id);
                                            } else {
                                                array_push($not_updated_data, $value['part_number']);
                                                log_message("info", "error in adding inventory ledger details data: " . print_r($ledger_data, TRUE));
                                            }
                                        }
                                    } else if ($value['shippingStatus'] == 0) {
                                        if (isset($value['spare_id']) && !empty($value['spare_id']) && ($value['spare_id'] != "new_spare_id")) {
                                            //Cancelled Spare line item
                                            $this->service_centers_model->update_spare_parts(array('id' => $value['spare_id']), array('status' => _247AROUND_CANCELLED, "old_status" => SPARE_PARTS_REQUESTED));

                                            $this->notify->insert_state_change($value['booking_id'], SPARE_PARTS_CANCELLED, SPARE_PARTS_CANCELLED, "", $action_agent_id, "", ACTOR_NOT_DEFINE, NEXT_ACTION_NOT_DEFINE, $action_entity_id);
                                        }
                                    } else if ($value['shippingStatus'] == -1) {
                                        //Partner will shipe later
                                        $this->service_centers_model->update_spare_parts(array('id' => $value['spare_id']), array('remarks_by_partner' => "Part will be sent later"));
                                    }
                                }

                                if ($transfered_by == MSL_TRANSFERED_BY_PARTNER) {
                                    $this->insert_inventory_main_invoice($invoice_id, $partner_id, $booking_id_array, $tqty, str_replace('/', '-', $invoice_dated), $total_basic_amount, $total_cgst_tax_amount, $total_sgst_tax_amount, $total_igst_tax_amount, $invoice_file['message'], $wh_id);

                                    $this->invoices_model->insert_invoice_breakup($invoicebreakup);
                                } else {
                                    // $this->remove_inventory_from_warehouse($invoice, $sender_enity_id, $wh_id, $action_agent_id);
                                }


                                // 2 Means - this part send to Micro Warehouse And 1 means sent to warehouse
                                If ($is_wh_micro == 2) {
                                    $not_updated_data = $this->generate_micro_warehouse_invoice($invoice, $wh_id, $tqty, $partner_id, $to_gst_number, $sender_enity_id, $sender_entity_type, $agent_id, $agent_type, $courier_company_details_id, $action_agent_id);
                                }
                                //send email to 247around warehouse incharge
                                $email_template = $this->booking_model->get_booking_email_template("spare_send_by_partner_to_wh");
                                $wh_incharge_id = $this->reusable_model->get_search_result_data("entity_role", "id", array("entity_type" => _247AROUND_SF_STRING, 'role' => WAREHOUSE_INCHARCGE_CONSTANT), NULL, NULL, NULL, NULL, NULL, array());
                                if (!empty($wh_incharge_id)) {

                                    //get 247around warehouse incharge email
                                    $wh_where = array('contact_person.role' => $wh_incharge_id[0]['id'],
                                        'contact_person.entity_id' => $wh_id,
                                        'contact_person.entity_type' => _247AROUND_SF_STRING
                                    );

                                    $email_details = $this->inventory_model->get_warehouse_details('contact_person.official_email', $wh_where, FALSE, TRUE);
                                    if (empty($email_details)) {
                                        $email_details = $wh_details;
                                    }
                                    if (!empty($email_details) && !empty($email_template)) {
                                        //generate part details table                                        
                                        $parts_details_table = $this->table->generate();

                                        //generate courier details table
                                        $this->table->set_heading(array('Courier Name', 'AWB Number', 'Shipment Date', 'Invoice Amount', 'Invoice Number'));
                                        $this->table->add_row(array($courier_name, $awb_number, $courier_shipment_date, round($invoice_amount), $invoice_id));
                                        $courier_details_table = $this->table->generate();

                                        $to = $email_details[0]['official_email'];
                                        $cc = $email_template[3];
                                        $subject = vsprintf($email_template[4], array($partner_name, $wh_name));
                                        $message = vsprintf($email_template[0], array($partner_name, $parts_details_table, $courier_details_table));
                                        if (!empty($invoice_file['message'])) {
                                            $invoice_attchment = S3_WEBSITE_URL . "invoices-excel/" . $invoice_file['message'];
                                        } else {
                                            $invoice_attchment = '';
                                        }
                                        if (!empty($courier_file['message'])) {
                                            $courier_attchment = S3_WEBSITE_URL . "vendor-partner-docs/" . $courier_file['message'];
                                        } else {
                                            $courier_attchment = '';
                                        }
                                        $this->notify->sendEmail($email_template[2], $to, $cc, "", $subject, $message, $invoice_attchment, 'spare_send_by_partner_to_wh', $courier_attchment);
                                    }
                                }

                                if (empty($not_updated_data)) {
                                    $res['status'] = TRUE;
                                    $res['message'] = 'Details Updated Successfully';
                                    $res['warehouse_id'] = $wh_id;
                                    $res['total_quantity'] = $tqty;
                                    $res['partner_id'] = $partner_id;
                                } else {
                                    $res['status'] = false;
                                    $res['message'] = "For These Parts Details not updated :" . implode(',', $not_updated_data) . " Please Try again for these parts";
                                }
                            } else {
                                log_message('info', 'Error in inserting courier details.');
                                $res['status'] = false;
                                $res['message'] = 'Something went wrong. Please try again.';
                            }
                        }
                    } else {
                        $res['status'] = false;
                        $res['message'] = $invoice_file['message'];
                    }
                    } else {
                        $res['status'] = false;
                        $res['message'] = "We can't update msl. Please refresh and try again.";
                    }
                } else {
                    $res['status'] = false;
                    $res['message'] = 'Please select parts details';
                }
            } else {
                $res['status'] = false;
                $res['message'] = 'Please enter invoice id';
            }
        } else {
            $res['status'] = false;
            $res['message'] = 'All fields are required';
        }

        echo json_encode($res);
    }   
    
    function check_msl_invoice_id($transfered_by, $invoice_id) {
        if ($transfered_by == MSL_TRANSFERED_BY_PARTNER){
            if (strpos($invoice_id, '/') === false) {
                $is_invoice_exists = $this->check_invoice_id_exists($invoice_id);
                if (!$is_invoice_exists['status']) {
                    return $this->upload_spare_invoice_file($_FILES);
                } else {
                    $invoice_file['status'] = FALSE;
                    $invoice_file['message'] = "Entered invoice number already exists in our record.";
                }
            } else {
                $invoice_file['status'] = FALSE;
                $invoice_file['message'] = "Invoice ID is invalid.Please make sure invoice number does not contain '/'. You can replace '/' with '-'";
            }
        } else {
            $invoice_file['status'] = true;
            $invoice_file['message'] = "";
            
            return $invoice_file;
        }
    }





    /**
     * @desc
     * @param Array $ledger
     * @param int $wh_id
     */
     function move_inventory_to_warehouse($ledger, $fomData, $wh_id, $is_wh_micro, $action_agent_id) {
        log_message('info', __METHOD__ . " warehouse id " . $wh_id . " ledger " . json_encode($ledger, true) . " Form data " . json_encode($fomData) . " WH id " . $wh_id,true);
        $access = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $transfered_by = $this->input->post('transfered_by');
        if ($this->session->userdata("partner_id")) {
            $s_partner_id = $this->session->userdata("partner_id");

            $track_entity_type = _247AROUND_PARTNER_STRING;
        } else if ($this->session->userdata("service_center_id")) {
            $s_partner_id = $this->session->userdata("service_center_id");
            $track_entity_type = _247AROUND_SF_STRING;
        } else {
            $s_partner_id = _247AROUND;
            $track_entity_type = _247AROUND_EMPLOYEE_STRING;
        }
        if (!empty($ledger['booking_id'])) {
            if (isset($fomData['spare_id']) && $fomData['spare_id'] != 'new_spare_id') {

                $a = array('entity_type' => _247AROUND_SF_STRING, 'partner_id' => $wh_id,
                    'wh_ack_received_part' => 0, 'purchase_invoice_id' => $ledger['invoice_id'],
                    'sell_invoice_id' => (isset($ledger['micro_invoice_id'])? $ledger['micro_invoice_id'] : NULL),
                    'requested_inventory_id' => $ledger['inventory_id'],
                    'shipped_quantity'=>$ledger['quantity'],
                    'inventory_invoice_on_booking' => 1, 'defective_return_to_entity_id' => $wh_id,
                    'defective_return_to_entity_type' => _247AROUND_SF_STRING, 'is_micro_wh' => $is_wh_micro);
                
                $update_spare_part = $this->service_centers_model->update_spare_parts(array('id' => $fomData['spare_id']), $a);

                /* Insert Spare Tracking Details */
                if (!empty($fomData['spare_id'])) {
                    $tracking_details = array('spare_id' => $fomData['spare_id'], 'action' => SPARE_SHIPPED_TO_WAREHOUSE, 'remarks' => '', 'agent_id' => $action_agent_id, 'entity_id' => $s_partner_id, 'entity_type' => $track_entity_type);
                    $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                }
                if ($update_spare_part) {
                    $actor = $next_action = NULL;
                    $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, SPARE_SHIPPED_TO_WAREHOUSE, _247AROUND, $ledger['booking_id']);
                    if (!empty($partner_status)) {
                        $booking['partner_current_status'] = $partner_status[0];
                        $booking['internal_status'] = SPARE_SHIPPED_TO_WAREHOUSE;
                        $booking['partner_internal_status'] = $partner_status[1];
                        $actor = $booking['actor'] = $partner_status[2];
                        $next_action = $booking['next_action'] = $partner_status[3];

                        $this->booking_model->update_booking($ledger['booking_id'], $booking);
                    }
                    $this->notify->insert_state_change($ledger['booking_id'], SPARE_SHIPPED_TO_WAREHOUSE, "", SPARE_SHIPPED_TO_WAREHOUSE . " with invoice id " . $ledger['invoice_id'], $action_agent_id, $action_agent_id, $actor, $next_action, $s_partner_id, NULL, $fomData['spare_id']);
                    log_message('info', ' Spare mapped to warehouse successfully for booking id ' . trim($fomData['booking_id']) . " Spare ID " . $fomData['spare_id']);
                } else {
                    log_message('info', ' error in updating spare details');
                }
            } else if (isset($fomData['spare_id']) && $fomData['spare_id'] == 'new_spare_id') {
                $this->insert_new_spare_item($ledger, $fomData, $wh_id, $s_partner_id, $action_agent_id);
            }
        } else {
         /*   $array = array('requested_inventory_id' => $ledger['inventory_id'],
                'status' => SPARE_PARTS_REQUESTED
                ,
               // 'entity_type IN (' => _247AROUND_SF_STRING,_247AROUND_PARTNER_STRING.')',
                'wh_ack_received_part != "0"' => NULL);

            if ($is_wh_micro == 2) {
                $array['spare_parts_details.service_center_id'] = $wh_id;
            }
            
            $entity_array=array(_247AROUND_PARTNER_STRING);
            if($is_wh_micro == 2){
                $entity_array=array(_247AROUND_SF_STRING,_247AROUND_PARTNER_STRING);
            }
            
            $post['where_in'] = array('spare_parts_details.entity_type' => $entity_array);
            
            $spare = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id, spare_parts_details.booking_id,spare_parts_details.quantity, "
                    . "spare_parts_details.status,spare_parts_details.part_warranty_status, spare_parts_details.entity_type, spare_parts_details.partner_id, "
                    . "requested_inventory_id,spare_parts_details.courier_name_by_partner,spare_parts_details.model_number,spare_parts_details.parts_requested,spare_parts_details.parts_requested_type,spare_parts_details.shipped_date,spare_parts_details.shipped_inventory_id,spare_parts_details.shipped_quantity", $array, false,false,false,$post);
            
			
            log_message('info', __METHOD__ . " Spare Data " . json_encode($spare, true));
            if (!empty($spare)) {
                foreach ($spare as $value) {
                    $qty = $value['quantity'];
                    if ($ledger['quantity'] >= $qty) {
                        $data = array('entity_type' => _247AROUND_SF_STRING, 'partner_id' => $wh_id,
                            'wh_ack_received_part' => 0, 'inventory_invoice_on_booking' => 1, 'purchase_invoice_id' => $ledger['invoice_id'], 'sell_invoice_id' => (isset($ledger['micro_invoice_id'])? $ledger['micro_invoice_id'] : NULL));
                        if ($is_wh_micro == 2) {
                            log_message('info', 'is_micro 2 come means sending msl to warehouse.',true);
                            if ($ledger['is_defective_part_return_wh'] == 1) {
                                $sf_state = $this->vendor_model->getVendorDetails("service_centres.state", array('service_centres.id' => $wh_id));
                                $wh_address_details = $this->miscelleneous->get_247aroud_warehouse_in_sf_state($sf_state[0]['state']);
                                $data['defective_return_to_entity_type'] = $wh_address_details[0]['entity_type'];
                                $data['defective_return_to_entity_id'] = $wh_address_details[0]['entity_id'];
                                $data['status'] = SPARE_PARTS_SHIPPED;
                                $status = SPARE_PARTS_SHIPPED;
                            } else {
                                $data['defective_return_to_entity_type'] = _247AROUND_PARTNER_STRING;
                                $data['defective_return_to_entity_id'] = $ledger['sender_entity_id'];
                            }
                                $data['is_micro_wh'] = 1;
                                $data['awb_by_partner'] =$this->input->post('awb_number');
                                $data['courier_name_by_partner']=$this->input->post('courier_name');
                                $data['model_number_shipped'] =$value['model_number'];
                                $data['parts_shipped'] =$value['parts_requested'];
                                $data['shipped_parts_type'] =$value['parts_requested_type'];
                                $data['shipped_date'] =$this->input->post('courier_shipment_date');
                                $data['shipped_inventory_id'] =$value['requested_inventory_id']; 
                                $data['shipped_quantity'] = $qty;
                                if($value['part_warranty_status']==1){
                                $data['status'] = SPARE_PARTS_SHIPPED;	
                                }else{
                                $data['status'] = SPARE_OOW_SHIPPED;
                               }
                                
                                $status = SPARE_PARTS_SHIPPED;
                            
                        } else {
                            $data = array('entity_type' => _247AROUND_SF_STRING, 'partner_id' => $wh_id,
                                'wh_ack_received_part' => 0, 'purchase_invoice_id' => $ledger['invoice_id'],
                                'sell_invoice_id' => (isset($ledger['micro_invoice_id'])? $ledger['micro_invoice_id'] : NULL),
                                'requested_inventory_id' => $ledger['inventory_id'],
                                 'defective_return_to_entity_id' => $wh_id,
                                'defective_return_to_entity_type' => _247AROUND_SF_STRING, 'is_micro_wh' => 2);
                            $status = SPARE_SHIPPED_TO_WAREHOUSE;
                            
                            log_message('info', __METHOD__ ." ledger " . json_encode($data, true));
                        }

                       if(empty($access)) {
                         if ($data['defective_return_to_entity_type'] == _247AROUND_PARTNER_STRING) {
                              $data['defective_return_to_entity_type'] = _247AROUND_SF_STRING;
                              $data['defective_return_to_entity_id'] = DEFAULT_WAREHOUSE_ID;
                         }
                       }
                        $update_spare_part = $this->service_centers_model->update_spare_parts(array('id' => $value['id']), $data);
                        
                         log_message('info', __METHOD__ ."Spare Updated " . json_encode($data, true));
//                        if($transfered_by == MSL_TRANSFERED_BY_WAREHOUSE){
//                            $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $ledger['sender_entity_id'],$ledger['inventory_id'], -1);
//                        }
                        
                        if($value['entity_type']==_247AROUND_SF_STRING){
                            
                        $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $value['partner_id'], $value['requested_inventory_id'], -$qty);
                            
                        }
                        
                        $actor = $next_action = NULL;
                        $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, $status, _247AROUND, $value['booking_id']);
                        if (!empty($partner_status)) {
                            $booking['partner_current_status'] = $partner_status[0];
                            $booking['internal_status'] = $status;
                            $booking['partner_internal_status'] = $partner_status[1];
                            $actor = $booking['actor'] = $partner_status[2];
                            $next_action = $booking['next_action'] = $partner_status[3];
                            
                            $this->booking_model->update_booking($value['booking_id'], $booking);
                        }
                        $this->notify->insert_state_change($value['booking_id'], SPARE_SHIPPED_TO_WAREHOUSE, "", SPARE_SHIPPED_TO_WAREHOUSE . " with invoice id " . $ledger['invoice_id'], $action_agent_id, $action_agent_id, $actor, $next_action, $s_partner_id, NULL);
                        $qty = $qty + 1;
                    }
                }
            }*/
        }
    }

    function insert_new_spare_item($ledger, $fomData, $wh_id, $s_partner_id, $action_agent_id) {
        log_message('info', __METHOD__ . " ledger " . print_r($ledger, true) . " Form data " . json_encode($fomData, true) . " wh id " . $wh_id);
        $spare = $this->partner_model->get_spare_parts_by_any("*", array('spare_parts_details.booking_id' => $ledger['booking_id'],"spare_parts_details.purchase_invoice_id IS NOT NULL" => null), false);
        
        if (!empty($spare)) {
            $newdata['booking_id'] = $ledger['booking_id'];
            $newdata['entity_type'] = _247AROUND_SF_STRING;
            $newdata['partner_id'] = $wh_id;
            $newdata['defective_return_to_entity_id'] = $wh_id;
            $newdata['defective_return_to_entity_type'] = _247AROUND_SF_STRING;
            $newdata['service_center_id'] = $spare[0]['service_center_id'];
            $newdata['date_of_purchase'] = $spare[0]['date_of_purchase'];
            $newdata['purchase_invoice_id'] = $spare[0]['purchase_invoice_id'];
            $newdata['invoice_pic'] = $spare[0]['invoice_pic'];
            $newdata['defective_parts_pic'] = $spare[0]['defective_parts_pic'];
            $newdata['defective_back_parts_pic'] = $spare[0]['defective_back_parts_pic'];
            $newdata['serial_number_pic'] = $spare[0]['serial_number_pic'];
            $newdata['model_number'] = $spare[0]['model_number'];
            $newdata['serial_number'] = $spare[0]['serial_number'];
            $newdata['date_of_request'] = date('Y-m-d');
            $newdata['parts_requested'] = $fomData['part_name'];
            
            $inventory_master_deatails = $this->inventory_model->get_inventory_master_list_data('inventory_master_list.type,inventory_master_list.price, inventory_master_list.gst_rate, inventory_master_list.oow_around_margin', array('inventory_master_list.inventory_id' => $ledger['inventory_id']));
            if (!empty($inventory_master_deatails[0]['type'])) {
                $newdata['parts_requested_type'] = $inventory_master_deatails[0]['type'];
            } else {
                $newdata['parts_requested_type'] = $fomData['part_name'];
            }
            $newdata['create_date'] = date('Y-m-d H:i:s');
            $newdata['status'] = SPARE_PARTS_REQUESTED;
            $newdata['wh_ack_received_part'] = 0;
            $newdata['requested_inventory_id'] = $ledger['inventory_id'];
            
            $newdata['quantity'] = $ledger['quantity'];
            $challan_estimate = round($inventory_master_deatails[0]['price'] * ( 1 + $inventory_master_deatails[0]['gst_rate'] / 100), 0);
            $newdata['challan_approx_value'] = round($challan_estimate * ( 1 + $inventory_master_deatails[0]['oow_around_margin'] / 100), 0);
            $newdata['challan_approx_value'] = round($newdata['challan_approx_value']*$newdata['quantity'],2);
            $newdata['inventory_invoice_on_booking'] = 1;
            if($ledger['is_wh_micro']==1){
            $newdata['is_micro_wh'] = 2;   
            }
            if($ledger['is_wh_micro']==2){
            $newdata['is_micro_wh'] = 1;   
            }

            if (isset($ledger['request_type'])== REPAIR_OOW_TAG) {
                $newdata['part_warranty_status'] = 2;
            } else {
                $newdata['part_warranty_status'] = 1;
            }
            
            $spare_id = $this->service_centers_model->insert_data_into_spare_parts($newdata);
            if ($spare_id) {
                $this->notify->insert_state_change($ledger['booking_id'], SPARE_SHIPPED_TO_WAREHOUSE, "", SPARE_SHIPPED_TO_WAREHOUSE, $action_agent_id, $action_agent_id, NULL, NULL, $s_partner_id, NULL);
                log_message('info', __METHOD__ . " New Spare Inserted for booking id " . $ledger['booking_id'] . " Spare Line item " . $spare_id);
            } else {
                log_message('info', __METHOD__ . " failed new spare insert for booking id " . $ledger['booking_id']);
            }
        }
    }

    /**
     * @desc This function is used to generate Micro Warehouse Invoice. When Partner tag new invoice(Send MSL)
     * @param Array $invoice
     * @param int $wh_id
     * @param date $invoice_date
     * @param int $tqty
     * @param int $partner_id
     */
    function generate_micro_warehouse_invoice($invoice, $wh_id, $tqty, $partner_id, $from_gst_number, $sender_enity_id, $sender_entity_type, $agent_id, $agent_type, $courier_id, $action_agent_id) {
        log_message('info', __METHOD__);
        $invoice_date = date('Y-m-d');
        $entity_details = $this->vendor_model->getVendorDetails("gst_no as gst_number, sc_code,"
                . "state,address as company_address,company_name,district, pincode, owner_phone_1, primary_contact_phone_1", array("id" => $wh_id));
                        
        $not_updated_data = array();

        if (empty($entity_details[0]['gst_number'])) {

            $entity_details[0]['gst_number'] = true;
        }
        
        $around_gst = $this->inventory_model->get_entity_gst_data("entity_gst_details.*", array('entity_gst_details.id' => $from_gst_number));
        $invoice_id = $this->invoice_lib->create_invoice_id("ARD-".$around_gst[0]['state']); 
        $a = array();
        $main_company_state = $this->invoices_model->get_state_code(array('state_code' => $around_gst[0]['state']))[0]['state'];
        foreach ($invoice as $key => $value) {

            $select = "oow_vendor_margin, oow_around_margin";
            $post = array();

            $post['where'] = array('inventory_master_list.inventory_id' => $value['inventory_id']);
            $post['length'] = -1;

            $list = $this->inventory_model->get_inventory_master_list($post, $select);

            $repair_oow_around_percentage = REPAIR_OOW_AROUND_PERCENTAGE;
            if (!empty($list)) {
                if ($list[0]->oow_around_margin > 0) {
                    $repair_oow_around_percentage = $list[0]->oow_around_margin / 100;
                }
            }

            $a[$key]['invoice_id'] = $invoice_id;
            $a[$key]['description'] = $value['description'];
            $a[$key]['product_or_services'] = "Product";
            $a[$key]['hsn_code'] = $value['hsn_code'];
            $a[$key]['inventory_id'] = $value['inventory_id'];
            $a[$key]['rate'] = $value['rate'] * ( 1 + $repair_oow_around_percentage);
            $a[$key]['qty'] = $value['qty'];
            $a[$key]['company_name'] = $entity_details[0]['company_name']." (Ph No: ".
                    $entity_details[0]['primary_contact_phone_1'].", ". 
                    $entity_details[0]['owner_phone_1']. " )";
            $a[$key]['company_address'] = $entity_details[0]['company_address'];
            $a[$key]['district'] = $entity_details[0]['district'];
            $a[$key]['pincode'] = $entity_details[0]['pincode'];
            $a[$key]['state'] = $entity_details[0]['state'];
            
            if ((strcasecmp($main_company_state, $entity_details[0]['state']) == 0)){
                $a[$key]['c_s_gst'] = TRUE;
            } else {
               $a[$key]['c_s_gst'] = FALSE; 
            }

            $a[$key]['gst_number'] = $entity_details[0]['gst_number'];
            $a[$key]['gst_rate'] = $value['sgst_tax_rate'] + $value['igst_tax_rate'] + $value['cgst_tax_rate'];
            $margin_total = $value['taxable_value'] * ( 1 + $repair_oow_around_percentage);
            $a[$key]['taxable_value'] = $margin_total;
            $a[$key]['from_gst_number_id'] = $value['to_gst_number'];
        }
        $response = $this->invoices_model->_set_partner_excel_invoice_data($a, $invoice_date, $invoice_date, "Tax Invoice", $invoice_date);
        $response['meta']['main_company_gst_number'] = $around_gst[0]['gst_number'];
        $response['meta']['main_company_state'] = $main_company_state;
        $response['meta']['main_company_address'] = $around_gst[0]['address'] . "," . $around_gst[0]['city'];
        
        $response['meta']['main_company_pincode'] = $around_gst[0]['pincode'];
        $response['meta']['main_company_state_code'] = $around_gst[0]['state'];
        if(!empty($around_gst[0]['email_id'])){
            $response['meta']['main_company_email'] = $around_gst[0]['email_id'];
        }
        if(!empty($around_gst[0]['contact_number'])){
            $response['meta']['main_company_phone'] = $around_gst[0]['contact_number'];
        }
        if(!empty($around_gst[0]['state_stamp_picture'])){
            $response['meta']['main_company_seal'] = $around_gst[0]['state_stamp_picture'];
        }
        $response['meta']['invoice_id'] = $invoice_id;
        $status = $this->invoice_lib->send_request_to_create_main_excel($response, "final");
        if ($status) {
            log_message("info", __METHOD__ . " Vendor Spare Invoice SF ID" . $wh_id);

            $convert = $this->invoice_lib->convert_invoice_file_into_pdf($response, "final");
            $output_pdf_file_name = $convert['main_pdf_file_name'];
            $response['meta']['invoice_file_main'] = $output_pdf_file_name;
            $response['meta']['copy_file'] = $convert['copy_file'];

            $this->invoice_lib->upload_invoice_to_S3($response['meta']['invoice_id'], false);
            unlink(TMP_FOLDER . $output_pdf_file_name);
            unlink(TMP_FOLDER . $response['meta']['invoice_id'] . ".xlsx");
            unlink(TMP_FOLDER . "copy_" . $response['meta']['invoice_id'] . ".xlsx");
            unlink(TMP_FOLDER . "copy_" . $response['meta']['invoice_id'] . ".pdf");

            $invoice_details = array(
                'invoice_id' => $response['meta']['invoice_id'],
                'type_code' => 'A',
                'type' => "Parts",
                'vendor_partner' => 'vendor',
                'vendor_partner_id' => $wh_id,
                'third_party_entity' => "partner",
                'third_party_entity_id' => $partner_id,
                'invoice_file_main' => $response['meta']['invoice_file_main'],
                'invoice_file_excel' => $response['meta']['invoice_id'] . ".xlsx",
                'from_date' => date("Y-m-d", strtotime($invoice_date)), //??? Check this next time, format should be YYYY-MM-DD
                'to_date' => date("Y-m-d", strtotime($invoice_date)),
                'parts_cost' => $response['meta']['total_taxable_value'],
                'parts_count' => $tqty,
                'total_amount_collected' => $response['meta']['sub_total_amount'],
                'invoice_date' => date("Y-m-d"),
                'around_royalty' => $response['meta']['sub_total_amount'],
                'due_date' => date("Y-m-d"),
                //Amount needs to be collected from Vendor
                'amount_collected_paid' => $response['meta']['sub_total_amount'],
                //add agent_id
                'agent_id' => _247AROUND_DEFAULT_AGENT,
                "cgst_tax_rate" => $response['meta']['cgst_tax_rate'],
                "sgst_tax_rate" => $response['meta']['sgst_tax_rate'],
                "igst_tax_rate" => $response['meta']['igst_tax_rate'],
                "igst_tax_amount" => $response['meta']["igst_total_tax_amount"],
                "sgst_tax_amount" => $response['meta']["sgst_total_tax_amount"],
                "cgst_tax_amount" => $response['meta']["cgst_total_tax_amount"],
                "hsn_code" => SPARE_HSN_CODE,
                "invoice_file_pdf" => $response['meta']['copy_file'],
                "vertical" => SERVICE,
                "category" => SPARES,
                "sub_category" => $this->input->post('invoice_tag'),
                "accounting" => 1
            );
            $this->invoices_model->insert_new_invoice($invoice_details);
            log_message('info', __METHOD__ . ": Invoice ID inserted");
            $this->invoice_lib->insert_def_invoice_breakup($response, 0);
            
            foreach ($invoice as $key => $value) {
                $ledger_data = array();

                $ledger_data['receiver_entity_id'] = $wh_id;
                $ledger_data['receiver_entity_type'] = _247AROUND_SF_STRING;
                $ledger_data['sender_entity_id'] = $sender_enity_id;
                $ledger_data['sender_entity_type'] = $sender_entity_type;
                $ledger_data['inventory_id'] = $value['inventory_id'];
                $ledger_data['quantity'] = $value['qty'];
                $ledger_data['agent_id'] = $agent_id;
                $ledger_data['agent_type'] = $agent_type;
<<<<<<< HEAD
                $ledger_data['booking_id'] = "";
                $pin= $this->input->post('invoice_id');
                if(!empty($pin)){
=======
                $ledger_data['booking_id'] = $value['booking_id'];
                $pin = $this->input->post('invoice_id');
                if (!empty($pin)) {
>>>>>>> da39423f4... Allow msl on booking for partner to send part to microwarehouse #CRMS-2191
                    $ledger_data['invoice_id'] = $this->input->post('invoice_id');
                } else {
                    $ledger_data['invoice_id'] = NULL;
                }
                
                $ledger_data['micro_invoice_id'] = $invoice_id;
                $ledger_data['is_wh_ack'] = 0;
                $ledger_data['courier_id'] = $courier_id;
                $ledger_data['is_wh_micro'] = 2;
                if(!empty($value['spare_id'])) {
                    $ledger_data['spare_id'] = $value['spare_id'];
                }
                $insert_id = $this->inventory_model->insert_inventory_ledger($ledger_data);
                $ledger_data['is_defective_part_return_wh'] = trim($this->input->post('is_defective_part_return_wh'));
                
                if ($insert_id) {
                    log_message("info", "Ledger details added successfully");
                    // Don't uncomment below line
                    //$this->move_inventory_to_warehouse($ledger_data, $value, $wh_id, 2, $action_agent_id);
<<<<<<< HEAD
                    $stock = "stock - '" . $value['qty'] . "'";
                    $this->inventory_model->update_inventory_stock(array('entity_id' => $sender_enity_id, 'inventory_id' => $value['inventory_id']), $stock);
                    
=======
                    if($sender_entity_type == _247AROUND_SF_STRING){
                        $stock = "stock - '" . $value['qty'] . "'";
                        $this->inventory_model->update_inventory_stock(array('entity_id' => $sender_enity_id, 'inventory_id' => $value['inventory_id']), $stock);
                    }
>>>>>>> da39423f4... Allow msl on booking for partner to send part to microwarehouse #CRMS-2191
                } else {
                    array_push($not_updated_data, $value['part_number']);
                    log_message("info", "error in adding inventory ledger details data: " . print_r($ledger_data, TRUE));
                }
                
            }
        }
        
        return $not_updated_data;
    }

    /**
     * @desc This function is used to generate array data to insert main invoice table. 
     * @param String $invoice_id
     * @param int $partner_id
     * @param array $booking_id_array
     * @param int $tqty
     * @param date $invoice_dated
     * @param int $total_basic_amount
     * @param int $total_cgst_tax_amount
     * @param int $total_sgst_tax_amount
     * @param Int $total_igst_tax_amount
     */
    function insert_inventory_main_invoice($invoice_id, $partner_id, $booking_id_array, $tqty, $invoice_dated, $total_basic_amount, $total_cgst_tax_amount, $total_sgst_tax_amount, $total_igst_tax_amount, $invoice_file, $wh_id) {
        log_message('info', __METHOD__ . " For Invoice ID " . $invoice_id);
        $total_invoice_amount = ($total_basic_amount + $total_cgst_tax_amount + $total_sgst_tax_amount + $total_igst_tax_amount);
        $tcs_rate = $this->input->post('tcs_rate');
        $tcs_amount = 0;
        
        if($tcs_rate > 0){
            $tcs_amount = ($total_invoice_amount * $tcs_rate)/100;
        }
        
        if ($this->session->userdata('id')) {
            $agent_id = $this->session->userdata('id');
        } else {
            $agent_id = _247AROUND_DEFAULT_AGENT;
        }
        $invoice_details_insert = array(
            'invoice_id' => $invoice_id,
            'type' => 'Parts',
            'type_code' => 'B',
            'vendor_partner' => 'partner',
            "third_party_entity" => "vendor",
            "third_party_entity_id" => $wh_id,
            'vendor_partner_id' => $partner_id,
            'invoice_file_main' => $invoice_file,
            'invoice_date' => date("Y-m-d", strtotime($invoice_dated)),
            'from_date' => date("Y-m-d", strtotime($invoice_dated)),
            'to_date' => date("Y-m-d", strtotime($invoice_dated)),
            'due_date' => date("Y-m-d", strtotime($invoice_dated)),
            'parts_cost' => $total_basic_amount,
            "parts_count" => $tqty,
            'total_amount_collected' => ($total_invoice_amount + $tcs_amount),
            //Amount needs to be Paid to Vendor
            'amount_collected_paid' => (0 - ($total_invoice_amount +$tcs_amount)),
            'agent_id' => $agent_id,
            "cgst_tax_rate" => 0,
            "sgst_tax_rate" => 0,
            "igst_tax_rate" => 0,
            "remarks" => !empty($booking_id_array) ? implode(",", $booking_id_array) : '',
            "igst_tax_amount" => $total_igst_tax_amount,
            "sgst_tax_amount" => $total_sgst_tax_amount,
            "cgst_tax_amount" => $total_cgst_tax_amount,
            "vertical" => SERVICE,
            "category" => SPARES,
            "sub_category" => $this->input->post('invoice_tag'),
            "accounting" => 1,
            "tcs_rate" => $tcs_rate,
            "tcs_amount" => $tcs_amount
        );

        // insert invoice details into vendor partner invoices table
        $this->invoices_model->action_partner_invoice($invoice_details_insert);
        log_message('info', __METHOD__ . "Vendor Partner Invoice inserted ... " . $invoice_id);
    }

    /**
     * @desc This is used to generate invoice annexure line item
     * @param String $invoice_id
     * @param Array $value
     * @return Array
     */
    function inventory_invoice_data($invoice_id, $c_s_gst, $value) {
        $invoice = array();
        $invoice['invoice_id'] = $invoice_id;
        $invoice['description'] = $value['part_name'];
        $invoice['product_or_services'] = "Product";
        $invoice['hsn_code'] = $value['hsn_code'];
        $invoice['qty'] = $value['quantity'];
        $invoice['rate'] = $value['part_total_price'] / $value['quantity'];
        $invoice['inventory_id'] = $value['inventory_id'];
        $invoice['taxable_value'] = $value['part_total_price'];
        if (!empty($value['gst_rate'])) {
            $gst_amount = $invoice['taxable_value'] * ($value['gst_rate'] / 100 );
        } else {

            $gst_amount = $invoice['taxable_value'];
        }

        if (!empty($value['gst_rate'])) {
            if ($c_s_gst) {

                $invoice['cgst_tax_amount'] = $invoice['sgst_tax_amount'] = $gst_amount / 2;
                $invoice['cgst_tax_rate'] = $invoice['sgst_tax_rate'] = $value['gst_rate'] / 2;
                $invoice['igst_tax_amount'] = $invoice['igst_tax_rate'] = 0;
            } else {

                $invoice['igst_tax_amount'] = $gst_amount;
                $invoice['igst_tax_rate'] = $value['gst_rate'];
                $invoice['cgst_tax_amount'] = $invoice['sgst_tax_amount'] = 0;
                $invoice['cgst_tax_rate'] = $invoice['sgst_tax_rate'] = 0;
            }
        } else {
            $invoice['cgst_tax_amount'] = $invoice['sgst_tax_amount'] = $invoice['igst_tax_amount'] = 0;
            $invoice['cgst_tax_rate'] = $invoice['sgst_tax_rate'] = $invoice['igst_tax_rate'] = 0;
        }


        $invoice['total_amount'] = $invoice['taxable_value'] + $gst_amount;
        $invoice['create_date'] = date('Y-m-d H:i:s');

        return $invoice;
    }


     /**
     *  @desc : This function is used to show all the spare list which was send by partner to warehouse and rejected warehouse
     *  @param : void
     *  @return : $res JSON
     */
    function get_spare_send_by_partner_to_wh_rejected() {
        ob_end_clean();
        $post = $this->get_post_data();
        $post['is_courier_details_required'] = TRUE;
        $post['column_order'] = array();
        $sender = trim($this->input->post('sender_entity_id'));
        $post['column_search'] = array('inventory_master_list.part_name', 'inventory_master_list.type', 'courier_company_invoice_details.awb_number', 'courier_company_invoice_details.company_name', 'i.booking_id');

        $post['where'] = array('i.receiver_entity_id' => trim($this->input->post('receiver_entity_id')),
            'i.receiver_entity_type' => trim($this->input->post('receiver_entity_type')),
            'i.is_wh_ack' => $this->input->post('is_wh_ack'));

        if (trim($this->input->post('is_wh_micro'))) {
            $post['where']['vendor_partner_invoices.third_party_entity_id'] = trim($this->input->post('sender_entity_id'));

            $post['is_micro_wh'] = true;
        } else {
            $post['where']['i.sender_entity_id'] = trim($this->input->post('sender_entity_id'));
            $post['where']['i.sender_entity_type']=trim($this->input->post('sender_entity_type'));
            $post['is_micro_wh'] = false;
        }

        $select = "services.services,inventory_master_list.*,CASE WHEN(sc.name IS NOT NULL) THEN (sc.name) 
                    WHEN(p.public_name IS NOT NULL) THEN (p.public_name) 
                    WHEN (e.full_name IS NOT NULL) THEN (e.full_name) END as receiver, 
                    CASE WHEN(sc1.name IS NOT NULL) THEN (sc1.name) 
                    WHEN(p1.public_name IS NOT NULL) THEN (p1.public_name) 
                    WHEN (e1.full_name IS NOT NULL) THEN (e1.full_name) END as sender,i.*,courier_company_invoice_details.awb_number as AWB_no, courier_company_invoice_details.company_name as courier_name";
        $list = $this->inventory_model->get_spare_need_to_acknowledge($post, $select);
        $data = array();
        $no = $post['start'];
        foreach ($list as $inventory_list) {
            $no++;
            $row = $this->get_spare_send_by_partner_to_wh_table_rejected($inventory_list, $no);
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
     *  @desc : This function is used to show all the spare list which was send by partner to warehouse and not acknowledge by warehouse
     *  @param : void
     *  @return : $res JSON
     */
    function get_spare_send_by_partner_to_wh() {
        ob_end_clean();
        $post = $this->get_post_data();
        $post['is_courier_details_required'] = TRUE;
        $post['column_order'] = array();
        $sender = trim($this->input->post('sender_entity_id'));
        $post['column_search'] = array('inventory_master_list.part_name', 'inventory_master_list.type', 'courier_company_invoice_details.awb_number', 'courier_company_invoice_details.company_name', 'i.booking_id', 'i.invoice_id');

        $post['where'] = array('i.receiver_entity_id' => trim($this->input->post('receiver_entity_id')),
            'i.receiver_entity_type' => trim($this->input->post('receiver_entity_type')),
            'i.is_wh_ack' => $this->input->post('is_wh_ack'));

        if (trim($this->input->post('is_wh_micro'))) {
            $post['where']['vendor_partner_invoices.third_party_entity_id'] = trim($this->input->post('sender_entity_id'));

            $post['is_micro_wh'] = true;
        } else {
            $post['where']['i.sender_entity_id'] = trim($this->input->post('sender_entity_id'));
            $post['where']['i.sender_entity_type']=trim($this->input->post('sender_entity_type'));
            $post['is_micro_wh'] = false;
        }

        $select = "services.services,inventory_master_list.*,CASE WHEN(sc.name IS NOT NULL) THEN (sc.name) 
                    WHEN(p.public_name IS NOT NULL) THEN (p.public_name) 
                    WHEN (e.full_name IS NOT NULL) THEN (e.full_name) END as receiver, 
                    CASE WHEN(sc1.name IS NOT NULL) THEN (sc1.name) 
                    WHEN(p1.public_name IS NOT NULL) THEN (p1.public_name) 
                    WHEN (e1.full_name IS NOT NULL) THEN (e1.full_name) END as sender,i.*,courier_company_invoice_details.awb_number as AWB_no, courier_company_invoice_details.company_name as courier_name";
        $list = $this->inventory_model->get_spare_need_to_acknowledge($post, $select);
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
     * @desc this function is used to load datatable. When warehouse sent new inventory to partner, they have to acknowledge to receive
     */
    function get_msl_send_by_wh_to_partner() {
        log_message('info', __METHOD__ . json_encode($this->input->post(), true));
//        $str = '{"draw":"2","columns":[{"data":"0","name":"","searchable":"true","orderable":"false","search":{"value":"","regex":"false"}},{"data":"1","name":"","searchable":"true","orderable":"false","search":{"value":"","regex":"false"}},{"data":"2","name":"","searchable":"true","orderable":"false","search":{"value":"","regex":"false"}},{"data":"3","name":"","searchable":"true","orderable":"false","search":{"value":"","regex":"false"}},{"data":"4","name":"","searchable":"true","orderable":"false","search":{"value":"","regex":"false"}},{"data":"5","name":"","searchable":"true","orderable":"false","search":{"value":"","regex":"false"}},{"data":"6","name":"","searchable":"true","orderable":"false","search":{"value":"","regex":"false"}},{"data":"7","name":"","searchable":"true","orderable":"false","search":{"value":"","regex":"false"}},{"data":"8","name":"","searchable":"true","orderable":"false","search":{"value":"","regex":"false"}},{"data":"9","name":"","searchable":"true","orderable":"false","search":{"value":"","regex":"false"}}],"start":"0","length":"100","search":{"value":"","regex":"false"},"sender_entity_id":"10","sender_entity_type":"vendor","receiver_entity_id":"247073","receiver_entity_type":"partner","is_partner_ack":"3"}';
//        $_POST = json_decode($str, true);
        $post = $this->get_post_data();
        $post['is_courier_details_required'] = TRUE;
        $post['column_order'] = array();
        $post['column_search'] = array('inventory_master_list.part_name', 'inventory_master_list.type', 'courier_company_invoice_details.awb_number', 'courier_company_invoice_details.company_name', 'i.booking_id');
        $post['where'] = array('i.receiver_entity_id' => trim($this->input->post('receiver_entity_id')),
            'i.receiver_entity_type' => trim($this->input->post('receiver_entity_type')),
            'i.sender_entity_id' => trim($this->input->post('sender_entity_id')),
            'i.sender_entity_type' => trim($this->input->post('sender_entity_type')));

        $is_partner_ack = $this->input->post('is_partner_ack');
        $is_wh_ack = $this->input->post('is_wh_ack');

        if ($is_partner_ack == 3) {
            $post['where']['i.is_partner_ack'] = $is_partner_ack;
        }

        if ($is_wh_ack == 3) {
            $post['where']['i.is_wh_ack'] = $is_wh_ack;
        }

        $select = "services.services, inventory_master_list.*,CASE WHEN(sc.name IS NOT NULL) THEN (sc.name) 
                    WHEN(p.public_name IS NOT NULL) THEN (p.public_name) 
                    WHEN (e.full_name IS NOT NULL) THEN (e.full_name) END as receiver, 
                    CASE WHEN(sc1.name IS NOT NULL) THEN (sc1.name) 
                    WHEN(p1.public_name IS NOT NULL) THEN (p1.public_name) 
                    WHEN (e1.full_name IS NOT NULL) THEN (e1.full_name) END as sender,i.*,courier_company_invoice_details.awb_number as AWB_no, courier_company_invoice_details.company_name as courier_name";
        $list = $this->inventory_model->get_spare_need_to_acknowledge($post, $select);

        $data = array();
        $no = $post['start'];
        foreach ($list as $inventory_list) {
            $no++;
            $row = $this->get_msl_send_by_wh_to_partner_table($inventory_list, $no);
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
     * @desc This function is used to return datatable row
     * @param Array $inventory_list
     * @param int $no
     * @return Array
     */
    function get_msl_send_by_wh_to_partner_table($inventory_list, $no) {
        $row = array();

        $row[] = $no;

        $row[] = $inventory_list->services;
        $row[] = $inventory_list->invoice_id;
        $row[] = $inventory_list->type;
        $row[] = $inventory_list->part_name;
        $row[] = $inventory_list->part_number;
        $row[] = $inventory_list->quantity;
        $row[] = $inventory_list->courier_name;
        //$row[] = "<a href='#' onclick='get_msl_awb_details('".$inventory_list->courier_name."','".$inventory_list->AWB_no."','".$inventory_list->status."','msl_awb_loader_'".$inventory_list->AWB_no."')'>".$inventory_list->AWB_no."</a> <span id='msl_awb_loader_$inventory_list->AWB_no' style='display:none;'><i class='fa fa-spinner fa-spin'></i></span>"; 
        $a = "<a href='javascript:void(0);' onclick='";
        $a .= "get_msl_awb_details(" . '"' . $inventory_list->courier_name . '"';
        $a .= ', "' . $inventory_list->AWB_no . '"';
        $a .= ', "msl_awb_loader_' . $no . '"';
        $a .= ")'>" . $inventory_list->AWB_no . "</a>";
        $a .="<span id='msl_awb_loader_$no' style='display:none;'><i class='fa fa-spinner fa-spin'></i></span>";
        $row[] = $a;
        $row[] = "<input type='checkbox' class= 'check_single_row' id='ack_spare_$inventory_list->inventory_id'  data-inventory_id='" . $inventory_list->inventory_id . "' data-is_wh_micro='" . $inventory_list->is_wh_micro . "' data-quantity='" . $inventory_list->quantity . "' data-ledger_id = '" . $inventory_list->id . "' data-part_name = '" . $inventory_list->part_name . "' data-booking_id = '" . $inventory_list->booking_id . "' data-invoice_id = '" . $inventory_list->invoice_id . "' data-part_number = '" . $inventory_list->part_number . "'>";

        return $row;
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
        if ($this->session->userdata('service_center_id')) {
            $row[] = "<a href='" . base_url() . "service_center/booking_details/" . urlencode(base64_encode($inventory_list->booking_id)) . "'target='_blank'>" . $inventory_list->booking_id . "</a>";
        } else if ($this->session->userdata('id')) {
            $row[] = "<a href='" . base_url() . "employee/booking/viewdetails/" . $inventory_list->booking_id . "'target='_blank'>" . $inventory_list->booking_id . "</a>";
        }
        $row[] = $inventory_list->services;

        if($this->input->post('is_wh_micro')){
         $row[] = $inventory_list->micro_invoice_id;
        }else{
         $row[] = $inventory_list->invoice_id;	
        }
        $row[] = $inventory_list->type;
        $row[] = $inventory_list->part_name;
        $row[] = "<span style='word-break: break-all;'>" . $inventory_list->part_number . "</span>";
        $row[] = $inventory_list->quantity;
        $row[] = $inventory_list->description;
        $row[] = $inventory_list->courier_name;
        //$row[] = "<a href='#' onclick='get_msl_awb_details('".$inventory_list->courier_name."','".$inventory_list->AWB_no."','".$inventory_list->status."','msl_awb_loader_'".$inventory_list->AWB_no."')'>".$inventory_list->AWB_no."</a> <span id='msl_awb_loader_$inventory_list->AWB_no' style='display:none;'><i class='fa fa-spinner fa-spin'></i></span>"; 
        $a = "<a href='javascript:void(0);' onclick='";
        $a .= "get_msl_awb_details(" . '"' . $inventory_list->courier_name . '"';
        $a .= ', "' . $inventory_list->AWB_no . '"';
        $a .= ', "msl_awb_loader_' . $no . '"';
        $a .= ")'>" . $inventory_list->AWB_no . "</a>";
        $a .="<span id='msl_awb_loader_$no' style='display:none;'><i class='fa fa-spinner fa-spin'></i></span>";
        $row[] = $a;
        
        if($this->input->post('is_wh_micro')){
         $row[] = "<input type='checkbox' class= 'check_single_row' id='ack_spare_$inventory_list->inventory_id' data-inventory_id='" . $inventory_list->inventory_id . "' data-is_wh_micro='" . $inventory_list->is_wh_micro . "' data-quantity='" . $inventory_list->quantity . "' data-ledger_id = '" . $inventory_list->id . "' data-part_name = '" . $inventory_list->part_name . "' data-booking_id = '" . $inventory_list->booking_id . "' data-invoice_id = '" . $inventory_list->micro_invoice_id . "' data-part_number = '" . $inventory_list->part_number . "'>";
        }else{
        $row[] = "<input type='checkbox' class= 'check_single_row' id='ack_spare_$inventory_list->inventory_id' data-inventory_id='" . $inventory_list->inventory_id . "' data-is_wh_micro='" . $inventory_list->is_wh_micro . "' data-quantity='" . $inventory_list->quantity . "' data-ledger_id = '" . $inventory_list->id . "' data-part_name = '" . $inventory_list->part_name . "' data-booking_id = '" . $inventory_list->booking_id . "' data-invoice_id = '" . $inventory_list->invoice_id . "' data-part_number = '" . $inventory_list->part_number . "'>";	
        }

        
        if($this->input->post('is_wh_micro')){
           $row[] = "<input type='checkbox' class= 'check_reject_single_row' id='reject_spare_$inventory_list->inventory_id' data-inventory_id='" . $inventory_list->inventory_id . "' data-is_wh_micro='" . $inventory_list->is_wh_micro . "' data-quantity='" . $inventory_list->quantity . "' data-ledger_id = '" . $inventory_list->id . "' data-part_name = '" . $inventory_list->part_name . "' data-booking_id = '" . $inventory_list->booking_id . "' data-invoice_id = '" . $inventory_list->micro_invoice_id . "' data-part_number = '" . $inventory_list->part_number . "'>";
        }else{
           $row[] = "<input type='checkbox' class= 'check_reject_single_row' id='reject_spare_$inventory_list->inventory_id' data-inventory_id='" . $inventory_list->inventory_id . "' data-is_wh_micro='" . $inventory_list->is_wh_micro . "' data-quantity='" . $inventory_list->quantity . "' data-ledger_id = '" . $inventory_list->id . "' data-part_name = '" . $inventory_list->part_name . "' data-booking_id = '" . $inventory_list->booking_id . "' data-invoice_id = '" . $inventory_list->invoice_id . "' data-part_number = '" . $inventory_list->part_number . "'>";
        }
      
        return $row;
    }




    /**
     *  @desc : This function is used to generate data for the spare which send by partner to wh
     *  @param : $inventory_list array()
     *  @param : $no string
     *  @return :void
     */
    function get_spare_send_by_partner_to_wh_table_rejected($inventory_list, $no) {
        $row = array();

        $row[] = $no;
        if (!empty($this->session->userdata('service_center_id'))) {
            $row[] = "<a href='" . base_url() . "service_center/booking_details/" . urlencode(base64_encode($inventory_list->booking_id)) . "'target='_blank'>" . $inventory_list->booking_id . "</a>";
        } else if ($this->session->userdata('id')) {
            $row[] = "<a href='" . base_url() . "employee/booking/viewdetails/" . $inventory_list->booking_id . "'target='_blank'>" . $inventory_list->booking_id . "</a>";
        }
        $row[] = $inventory_list->services;

        if($this->input->post('is_wh_micro')){
         $row[] = $inventory_list->micro_invoice_id;
        }else{
         $row[] = $inventory_list->invoice_id;  
        }
        $row[] = $inventory_list->type;
        $row[] = $inventory_list->part_name;
        $row[] = "<span style='word-break: break-all;'>" . $inventory_list->part_number . "</span>";
        $row[] = $inventory_list->quantity;
        $row[] = $inventory_list->description;
        $row[] = $inventory_list->courier_name;
        //$row[] = "<a href='#' onclick='get_msl_awb_details('".$inventory_list->courier_name."','".$inventory_list->AWB_no."','".$inventory_list->status."','msl_awb_loader_'".$inventory_list->AWB_no."')'>".$inventory_list->AWB_no."</a> <span id='msl_awb_loader_$inventory_list->AWB_no' style='display:none;'><i class='fa fa-spinner fa-spin'></i></span>"; 
        $a = "<a href='javascript:void(0);' onclick='";
        $a .= "get_msl_awb_details(" . '"' . $inventory_list->courier_name . '"';
        $a .= ', "' . $inventory_list->AWB_no . '"';
        $a .= ', "msl_awb_loader_' . $no . '"';
        $a .= ")'>" . $inventory_list->AWB_no . "</a>";
        $a .="<span id='msl_awb_loader_$no' style='display:none;'><i class='fa fa-spinner fa-spin'></i></span>";
        $row[] = $a;
        
         $row[] = "<span style='color:red;font-weight: 900;'>Rejected</span>";
      
        return $row;
    }



    /**
     *  @desc : This function is used to acknowledge data for the spare which send by partner to WH
     *  @param : void
     *  @return :$res JSON
     */
    function process_acknowledge_spare_send_by_partner_to_wh() {
        log_message("info", __METHOD__ . json_encode($this->input->post()), true);
        if ($this->session->userdata('employee_id')) {
            $this->checkUserSession();
        } else if ($this->session->userdata('service_center_id')) {
            $this->check_WH_UserSession();
        }
        //$_POST = json_decode('{"data":"{\"0\":{\"inventory_id\":\"41390\",\"quantity\":\"1\",\"ledger_id\":\"76383\",\"part_name\":\"GOGGLE,3D,PR,AUO,T315HBV1-V1,SXL,BLACK\",\"part_number\":\"1100078055\",\"booking_id\":\"LP-5242592005134\",\"invoice_id\":\"TXS-CED-05202013\",\"is_wh_micro\":\"1\"},\"1\":{\"inventory_id\":\"48474\",\"quantity\":\"1\",\"ledger_id\":\"76384\",\"part_name\":\"ASSY,STAND BASE,32,METALLICA\",\"part_number\":\"1200051821\",\"booking_id\":\"LP-5242592005134\",\"invoice_id\":\"TXS-CED-05202013\",\"is_wh_micro\":\"1\"},\"2\":{\"inventory_id\":\"38938\",\"quantity\":\"1\",\"ledger_id\":\"76385\",\"part_name\":\"REMOTE,RF 2.4GHZ GYROSCP,TSY150,STV\",\"part_number\":\"1100065181\",\"booking_id\":\"LP-5242592005134\",\"invoice_id\":\"TXS-CED-05202013\",\"is_wh_micro\":\"1\"}}","sender_entity_id":"247130","sender_entity_type":"partner","receiver_entity_id":"15","receiver_entity_type":"vendor","sender_entity_name":" Videocon","receiver_entity_name":"247around Ghaziabad (Uttar Pradesh)"}', true);
        
        $sender_entity_id = $this->input->post('sender_entity_id');
        $sender_entity_type = $this->input->post('sender_entity_type');
        $receiver_entity_id = $this->input->post('receiver_entity_id');
        $receiver_entity_type = $this->input->post('receiver_entity_type');
        $postData = json_decode($this->input->post('data'));
        
        if (!empty($sender_entity_id) && !empty($sender_entity_type) && !empty($receiver_entity_id) && !empty($receiver_entity_type) && !empty($postData)) {
            $template1 = array(
                'table_open' => '<table border="1" cellpadding="2" cellspacing="0" class="mytable">'
            );
            $proceed_to_process_all_record = true;
            foreach ($postData as $value) {
                $get_ledger_detail = $this->inventory_model->get_inventory_ledger_details('id,is_wh_ack,wh_ack_date', array('id' => $value->ledger_id, 'wh_ack_date is not null' => null));
                if (!empty($get_ledger_detail)) {
                    $proceed_to_process_all_record = false;
                }
            }
//            $this->table->set_template($template1);
//
//            $this->table->set_heading(array('Part Name', 'Part Number', 'Quantity'));
            $is_any_ledger_updated = false;
            if(!empty($proceed_to_process_all_record)){
            foreach ($postData as $value) {
                $get_ledger_detail = $this->inventory_model->get_inventory_ledger_details('id,is_wh_ack,wh_ack_date', array('id' => $value->ledger_id, 'wh_ack_date is not null' => null));
                if (!empty($get_ledger_detail)) {
                    //This ledger is already acknowledge by warehouse
                    continue;
                }
                $is_any_ledger_updated = true;
                //acknowledge spare by setting is_wh_ack flag = 1 in inventory ledger table
                $update = $this->inventory_model->update_ledger_details(array('is_wh_ack' => 1, 'wh_ack_date' => date('Y-m-d H:i:s')), array('id' => $value->ledger_id));
                if ($update) {

                    //$this->table->add_row($value->part_name, $value->part_number, $value->quantity);
                    //update inventory stocks
                    $is_entity_exist = $this->reusable_model->get_search_query('inventory_stocks', 'inventory_stocks.id', array('entity_id' => $receiver_entity_id, 'entity_type' => $receiver_entity_type, 'inventory_id' => $value->inventory_id), NULL, NULL, NULL, NULL, NULL)->result_array();
                    if (!empty($is_entity_exist)) {
                        $stock = "stock + '" . $value->quantity . "'";
                        $update_stocks = $this->inventory_model->update_inventory_stock(array('id' => $is_entity_exist[0]['id']), $stock);
                        if ($update_stocks) {
                            $this->map_in_tansit_inventory_data_to_warehouse($value, $receiver_entity_id, $sender_entity_id);
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
                            $this->map_in_tansit_inventory_data_to_warehouse($value, $receiver_entity_id, $sender_entity_id);

                            log_message("info", __FUNCTION__ . " Stocks has been inserted successfully" . print_r($insert_data, true));
                            $flag = TRUE;
                        } else {
                            log_message("info", __FUNCTION__ . " Error in inserting stocks" . print_r($insert_data, true));
                        }
                    }

                    //update courier deliverd date on basis of inventory
                    $get_courier_id = $this->inventory_model->get_inventory_ledger_details('courier_id', array('id' => $value->ledger_id));
                    if (!empty($get_courier_id)) {
                        $courier_id_array = $get_courier_id[0];
                        $courier_id = $courier_id_array['courier_id'];
                        if (!empty($courier_id)) {
                            $this->inventory_model->update_courier_company_invoice_details(array('id' => $courier_id, 'delivered_date IS NULL' => NULL), array('delivered_date' => date('Y-m-d H:i:s')));
                        }
                    }
                }
            }
        }
            //for now comment this code as per discussion with anuj and abhay. No need to send email when wh/partner acknowledged that they received spare
//            //send email to partner warehouse incharge that 247around warehouse received spare
//            $email_template = $this->booking_model->get_booking_email_template("spare_received_by_wh_from_partner");
//            $wh_incharge_id = $this->reusable_model->get_search_result_data("entity_role", "id", array("entity_type" => _247AROUND_PARTNER_STRING, 'role' => WAREHOUSE_INCHARCGE_CONSTANT), NULL, NULL, NULL, NULL, NULL, array());
//            if (!empty($wh_incharge_id)) {
//
//                //get 247around warehouse incharge email
//                $wh_where = array('contact_person.role' => $wh_incharge_id[0]['id'],
//                    'contact_person.entity_id' => $sender_entity_id,
//                    'contact_person.entity_type' => _247AROUND_PARTNER_STRING
//                );
//                $email_details = $this->inventory_model->get_warehouse_details('contact_person.official_email', $wh_where, FALSE, TRUE);
//                if (!empty($email_details) && !empty($email_template)) {
//                    //generate part details table                                        
//                    $parts_details_table = $this->table->generate();
//
//                    $to = $email_details[0]['official_email'];
//                    $cc = $email_template[3];
//                    $subject = vsprintf($email_template[4], array($this->input->post('receiver_entity_name'), $this->input->post('sender_entity_name')));
//                    $message = vsprintf($email_template[0], array($this->input->post('receiver_entity_name'), $parts_details_table));
//
//                    $this->notify->sendEmail($email_template[2], $to, $cc, "", $subject, $message, "", 'spare_received_by_wh_from_partner');
//                }
//            }

            if (empty($is_any_ledger_updated)) {
                if(!empty($proceed_to_process_all_record)){
                    $res['status'] = FALSE;
                    $res['message'] = 'No Record found to update.';
                }else{
                    $res['status'] = FALSE;
                    $res['message'] = 'Some Spare already acknowledged, Please refresh page to continue.';
                }
            } else {
                $res['status'] = TRUE;
                $res['message'] = 'Details updated successfully';
            }
        } else {
            $res['status'] = false;
            $res['message'] = 'All fields are required';
        }

        echo json_encode($res);
    }






    /**
     * @desc This is used to update inventory acknowledge and inventory map to warehouse
     * @param Array $data
     * @param Int $receiver_entity_id
     * @param Int $sender_entity_id
     */
      function map_in_tansit_inventory_data_to_warehouse($data, $receiver_entity_id, $sender_entity_id) {
        log_message('info', __METHOD__);
        $where = array(
            'spare_parts_details.entity_type' => _247AROUND_SF_STRING,
            'spare_parts_details.partner_id' => $receiver_entity_id,
            'wh_ack_received_part' => 0,
            'requested_inventory_id' => $data->inventory_id
        );
        // 1 Means CWH & 2 Means MWH
        if ($data->is_wh_micro == 1) {
            $where['purchase_invoice_id'] = $data->invoice_id;
            $where['status'] = SPARE_PARTS_REQUESTED;
        }
        
        if ($data->is_wh_micro == 2) {
            $where['sell_invoice_id'] = $data->invoice_id;
            $where['status IN ("' . SPARE_PARTS_SHIPPED . '","' . SPARE_SHIPPED_BY_PARTNER . '","' . SPARE_OOW_SHIPPED . '", "'.SPARE_PARTS_SHIPPED_BY_WAREHOUSE.'")'] = NULL;
        }

        $update = array();
        $update['wh_ack_received_part'] = 1;
        $status = WAREHOUSE_ACKNOWLEDGED_TO_RECEIVE_PARTS;
        if ($data->is_wh_micro == 2) {
            $update['status'] = SPARE_DELIVERED_TO_SF;  /// update deliver date also
            $update['acknowledge_date'] = date('Y-m-d');
            $status = SPARE_DELIVERED_TO_SF;
            // $sc_data['current_status'] = _247AROUND_PENDING;
            // $sc_data['internal_status'] = SPARE_DELIVERED_TO_SF;
            // $this->vendor_model->update_service_center_action($data->booking_id, $sc_data);
        }
        if (!empty($data->booking_id)) {
            
            $where['spare_parts_details.booking_id'] = $data->booking_id;
            $update_spare_part = $this->service_centers_model->update_spare_parts($where, $update);
            /// if warehouse then only///
            if ($data->is_wh_micro == 1) {
                $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $receiver_entity_id, $data->inventory_id, $data->quantity);
            }
            ////
            /// in micro reduce stock ///
            if ($data->is_wh_micro == 2) {
                $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $receiver_entity_id, $data->inventory_id, -$data->quantity);
                $spare_booking = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id, spare_parts_details.booking_id, spare_parts_details.status, spare_parts_details.entity_type,spare_parts_details.quantity ,spare_parts_details.partner_id, requested_inventory_id", $where, false);
                foreach ($spare_booking as $spare_booking_value) {
                    //  reduce stock //
                    $in['receiver_entity_id'] = $receiver_entity_id;
                    $in['receiver_entity_type'] = _247AROUND_SF_STRING;
                    $in['sender_entity_id'] = $receiver_entity_id;
                    $in['sender_entity_type'] = _247AROUND_SF_STRING;
                    $in['stock'] = -$spare_booking_value['quantity']; //-1;
                    $in['booking_id'] = $data->booking_id;
                    if ($this->session->userdata('userType') == 'service_center') {
                    $in['agent_id'] = $this->session->userdata('service_center_agent_id');
                    } else {
                    $in['agent_id'] = $this->session->userdata('agent_id');
                    }
                    $in['agent_type'] = _247AROUND_SF_STRING;
                    $in['is_wh'] = TRUE;
                    $in['inventory_id'] = $data->inventory_id;
                    $in['spare_id'] = $spare_booking_value['id'];
                    $this->miscelleneous->process_inventory_stocks($in);
                    $pcb = array();
                    $cb_url = base_url() . "employee/service_centers/acknowledge_delivered_spare_parts/" . $data->booking_id . "/" . $receiver_entity_id . "/" . $spare_booking_value['id'] . "/" . $sender_entity_id . "/1/0";
                    $this->asynchronous_lib->do_background_process($cb_url, $pcb);
                }
            }


            $actor = $next_action = NULL;
            $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, $status, _247AROUND, $data->booking_id);
            if (!empty($partner_status)) {
                $booking['partner_current_status'] = $partner_status[0];
                $booking['internal_status'] = $status;
                $booking['partner_internal_status'] = $partner_status[1];
                $actor = $booking['actor'] = $partner_status[2];
                $next_action = $booking['next_action'] = $partner_status[3];
                $this->booking_model->update_booking($data->booking_id, $booking);
            }

            log_message('info', __METHOD__ . " Booking ID updated " . $data->booking_id);
        } else {
            if ($data->is_wh_micro == 2) {
                $where['service_center_id'] = $receiver_entity_id;
            }
            $spare = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id, spare_parts_details.booking_id, spare_parts_details.status,spare_parts_details.quantity, spare_parts_details.entity_type, spare_parts_details.partner_id, requested_inventory_id", $where, false);
            $qty = $data->quantity;
            if (!empty($spare)) {
                foreach ($spare as $value) {
                    if ($data->quantity >= $value['quantity']) {
                        $update_spare_part = $this->service_centers_model->update_spare_parts(array('id' => $value['id']), $update);
                        if ($data->is_wh_micro == 1) {
                            $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $receiver_entity_id, $data->inventory_id, $value['quantity']);
                        }

                        log_message('info', __METHOD__ . "Multi Booking Booking ID updated " . $data->booking_id . " requested inventory id " . $data->inventory_id);
                        $qty = $qty - 1;
                        $actor = $next_action = NULL;
                        $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, $status, _247AROUND, $value['booking_id']);
                        if (!empty($partner_status)) {
                            $booking['partner_current_status'] = $partner_status[0];
                            $booking['internal_status'] = $status;
                            $booking['partner_internal_status'] = $partner_status[1];
                            $actor = $booking['actor'] = $partner_status[2];
                            $next_action = $booking['next_action'] = $partner_status[3];
                            $this->booking_model->update_booking($data->booking_id, $booking);
                        }

                        if ($data->is_wh_micro == 2) {
                            $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $receiver_entity_id, $data->inventory_id, -1);
                            //// REDUCE STOCK ///
                            $in['receiver_entity_id'] = $receiver_entity_id;
                            $in['receiver_entity_type'] = _247AROUND_SF_STRING;
                            $in['sender_entity_id'] = $receiver_entity_id;
                            $in['sender_entity_type'] = _247AROUND_SF_STRING;
                            $in['stock'] = -$value['quantity']; //-1;
                            $in['booking_id'] = $value['booking_id'];
                            if ($this->session->userdata('userType') == 'service_center') {
                            $in['agent_id'] = $this->session->userdata('service_center_agent_id');
                            } else {
                            $in['agent_id'] = $this->session->userdata('agent_id');
                            }
                            $in['agent_type'] = _247AROUND_SF_STRING;
                            $in['is_wh'] = TRUE;
                            $in['inventory_id'] = $data->inventory_id;
                            $in['spare_id'] = $value['id'];
                            $this->miscelleneous->process_inventory_stocks($in);
                            $pcb = array();
                            $cb_url = base_url() . "employee/service_centers/acknowledge_delivered_spare_parts/" . $value['booking_id'] . "/" . $receiver_entity_id . "/" . $value['id'] . "/" . $sender_entity_id . "/1/0";
                            $this->asynchronous_lib->do_background_process($cb_url, $pcb);
                        }
                    }
                }
            }

            if ($data->quantity > $qty) {
                log_message('info', __METHOD__ . " Rest qty " . $qty . " requested inventory id " . $data->inventory_id);
                $where1 = array(
                    'requested_inventory_id' => $data->inventory_id);
                if ($data->is_wh_micro == 2) {
                    $where1['partner_id'] = $sender_entity_id;
                    $where1['status IN ("' . SPARE_PARTS_SHIPPED . '","' . SPARE_SHIPPED_BY_PARTNER . '","' . SPARE_OOW_SHIPPED . '", "'.SPARE_PARTS_SHIPPED_BY_WAREHOUSE.'")'] = NULL;
                    $where1['service_center_id'] = $receiver_entity_id;
                    $where1['spare_parts_details.entity_type IN ("' . _247AROUND_PARTNER_STRING . '","' . _247AROUND_SF_STRING . '")'] = NULL;
                }

                if ($data->is_wh_micro == 1) {
                    //  $where1['service_center_id'] = $receiver_entity_id;
                    $where1['status'] = SPARE_PARTS_SHIPPED;
                    $where1['spare_parts_details.entity_type'] = _247AROUND_PARTNER_STRING;
                    $where1['partner_id'] = $sender_entity_id;
                }

                $spare = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id, spare_parts_details.booking_id,spare_parts_details.quantity, spare_parts_details.status, spare_parts_details.entity_type, spare_parts_details.partner_id, requested_inventory_id", $where1, false);
                if (!empty($spare)) {
                    $update['entity_type'] = _247AROUND_SF_STRING;
                    $update['partner_id'] = $receiver_entity_id;
                    $update['acknowledge_date'] = date('Y-m-d');
                    foreach ($spare as $value) {
                        if ($data->quantity >= $value['quantity']) {
                            log_message('info', __METHOD__ . " Rest qty " . $qty . " spare id " . $value['id']);
                            $update_spare_part = $this->service_centers_model->update_spare_parts(array('id' => $value['id']), $update);
                            if ($data->is_wh_micro == 1) {
                                $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $receiver_entity_id, $data->inventory_id, 1);
                            }
                            $qty = $qty - 1;
                            $actor = $next_action = NULL;
                            $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, $status, _247AROUND, $value['booking_id']);
                            if (!empty($partner_status)) {
                                $booking['partner_current_status'] = $partner_status[0];
                                $booking['internal_status'] = $status;
                                $booking['partner_internal_status'] = $partner_status[1];
                                $actor = $booking['actor'] = $partner_status[2];
                                $next_action = $booking['next_action'] = $partner_status[3];
                                $this->booking_model->update_booking($data->booking_id, $booking);
                            }


                            if ($data->is_wh_micro == 2) {
                                $this->inventory_model->update_pending_inventory_stock_request(_247AROUND_SF_STRING, $receiver_entity_id, $data->inventory_id, -$value['quantity']);
                                //// STOCK UPDATE LEDGER//             $in['receiver_entity_id'] = $receiver_entity_id;
                                $in['receiver_entity_id'] = $receiver_entity_id;
                                $in['receiver_entity_type'] = _247AROUND_SF_STRING;
                                $in['sender_entity_id'] = $receiver_entity_id;
                                $in['sender_entity_type'] = _247AROUND_SF_STRING;
                                $in['stock'] = -$value['quantity']; //-1;
                                $in['booking_id'] = $value['booking_id'];
                                if ($this->session->userdata('userType') == 'service_center') {
                                $in['agent_id'] = $this->session->userdata('service_center_agent_id');
                                } else {
                                $in['agent_id'] = $this->session->userdata('agent_id');
                                }
                                $in['agent_type'] = _247AROUND_SF_STRING;
                                $in['is_wh'] = TRUE;
                                $in['inventory_id'] = $data->inventory_id;
                                $in['spare_id'] = $value['id'];
                                $this->miscelleneous->process_inventory_stocks($in);

                                $pcb = array();
                                $cb_url = base_url() . "employee/service_centers/acknowledge_delivered_spare_parts/" . $value['booking_id'] . "/" . $receiver_entity_id . "/" . $value['id'] . "/" . $sender_entity_id . "/1/0";
                                $this->asynchronous_lib->do_background_process($cb_url, $pcb);
                            }
                        }
                    }
                }
            }
        }
		
		
	  //  After all process stock update . Spare transfer run so that alternate can be mapped if partner send alter spare part //
	  
	    $select = "spare_parts_details.id,spare_parts_details.quantity,spare_parts_details.booking_id,spare_parts_details.model_number, spare_parts_details.entity_type, booking_details.state,spare_parts_details.service_center_id,inventory_master_list.part_number, spare_parts_details.partner_id, booking_details.partner_id as booking_partner_id,"
                . " requested_inventory_id";
        $where1 = array('spare_parts_details.requested_inventory_id' => $data->inventory_id, 'spare_parts_details.status' => SPARE_PARTS_REQUESTED);
        $entity_array = array(_247AROUND_SF_STRING, _247AROUND_PARTNER_STRING);
        $post['where_in'] = array('spare_parts_details.entity_type' => $entity_array);
        $post['is_inventory'] = true;
        $bookings_spare = $this->partner_model->get_spare_parts_by_any($select, $where1, TRUE, FALSE, false, $post);

        //$agentid = '';
        $agent_name = '';
        $login_partner_id = '';
        $login_service_center_id = '';
        if ($this->session->userdata('userType') == 'service_center') {
            $agentid = $this->session->userdata('service_center_agent_id');
            $agent_name = $this->session->userdata('service_center_name');
            $login_service_center_id = $this->session->userdata('service_center_id');
            $login_partner_id = NULL;
        }else{
            $agentid = $this->session->userdata('id');
        }
		
      $this->miscelleneous->spareTransfer($bookings_spare, $agentid, $agent_name, $login_partner_id, $login_service_center_id);
	
	
    }

    /**
     *  @desc : This function is used to send defective spare by WH to partner
     *  @param : void
     *  @return :$res JSON
     */
    function send_defective_parts_to_partner_from_wh() {
        log_message("info", __METHOD__ );
        //$str = '{"data":"{\"0\":{\"inventory_id\":\"50040\",\"is_micro_wh\":\"2\",\"booking_id\":\"LP-3606631903131\",\"partner_id\":\"15\",\"defective_return_to_entity_id\":\"15\",\"defective_return_to_entity_type\":\"vendor\",\"spare_id\":\"25987\",\"part_name\":\"ASSY,FIM,D-LED,LEDTVIVC24F2-A,CON5\",\"service_center_id\":\"239\",\"sent_entity_type\":\"vendor\",\"model\":\"LEDTVIVC24F02A\",\"booking_partner_id\":\"247130\",\"shipping_quantity\":\"1\"},\"1\":{\"inventory_id\":\"50047\",\"is_micro_wh\":\"2\",\"booking_id\":\"LP-3602551903121\",\"partner_id\":\"15\",\"defective_return_to_entity_id\":\"15\",\"defective_return_to_entity_type\":\"vendor\",\"spare_id\":\"27102\",\"part_name\":\"BAR,LED,40DLED,11LED,COREACH\",\"service_center_id\":\"58\",\"sent_entity_type\":\"vendor\",\"model\":\"LEDTVVNF43FH11FA\",\"booking_partner_id\":\"247130\",\"shipping_quantity\":\"1\"},\"2\":{\"inventory_id\":\"52269\",\"is_micro_wh\":\"2\",\"booking_id\":\"LP-3742741904131\",\"partner_id\":\"15\",\"defective_return_to_entity_id\":\"15\",\"defective_return_to_entity_type\":\"vendor\",\"spare_id\":\"29330\",\"part_name\":\"ASSY,PCB,SMPS,PWM-514C5502M65,VNV50\",\"service_center_id\":\"300\",\"sent_entity_type\":\"vendor\",\"model\":\"UHDTVVNV50Q549SAMP\",\"booking_partner_id\":\"247130\",\"shipping_quantity\":\"1\"},\"3\":{\"inventory_id\":\"51574\",\"is_micro_wh\":\"2\",\"booking_id\":\"LP-3753911904161\",\"partner_id\":\"15\",\"defective_return_to_entity_id\":\"15\",\"defective_return_to_entity_type\":\"vendor\",\"spare_id\":\"29420\",\"part_name\":\"ASSY,SMPS,M-10,TV5502-ZC02-01VNB43\/VNB50\",\"service_center_id\":\"300\",\"sent_entity_type\":\"vendor\",\"model\":\"UHDTVVNB50Q519SA\",\"booking_partner_id\":\"247130\",\"shipping_quantity\":\"1\"},\"4\":{\"inventory_id\":\"51066\",\"is_micro_wh\":\"2\",\"booking_id\":\"LP-3671351904143\",\"partner_id\":\"15\",\"defective_return_to_entity_id\":\"15\",\"defective_return_to_entity_type\":\"vendor\",\"spare_id\":\"29687\",\"part_name\":\"BAR,LED,50DLED,HCG,CR,7LED,BIG-H,MALE\",\"service_center_id\":\"54\",\"sent_entity_type\":\"vendor\",\"model\":\"UHDTVVNB43Q519SA\",\"booking_partner_id\":\"247130\",\"shipping_quantity\":\"1\"},\"5\":{\"inventory_id\":\"50092\",\"is_micro_wh\":\"2\",\"booking_id\":\"LP-3846281905021\",\"partner_id\":\"15\",\"defective_return_to_entity_id\":\"15\",\"defective_return_to_entity_type\":\"vendor\",\"spare_id\":\"31559\",\"part_name\":\"ASSY,SPEAKER,BOOM,BOX,40DLED,8227-C\",\"service_center_id\":\"265\",\"sent_entity_type\":\"vendor\",\"model\":\"DDBTVVJU40FH17XAH\",\"booking_partner_id\":\"247130\",\"shipping_quantity\":\"1\"},\"6\":{\"inventory_id\":\"51066\",\"is_micro_wh\":\"2\",\"booking_id\":\"LP-3897031905071\",\"partner_id\":\"15\",\"defective_return_to_entity_id\":\"15\",\"defective_return_to_entity_type\":\"vendor\",\"spare_id\":\"33261\",\"part_name\":\"BAR,LED,50DLED,HCG,CR,7LED,BIG-H,MALE\",\"service_center_id\":\"737\",\"sent_entity_type\":\"vendor\",\"model\":\"LEDTVVMD55FH0ZFAP\",\"booking_partner_id\":\"247130\",\"shipping_quantity\":\"1\"},\"7\":{\"inventory_id\":\"51128\",\"is_micro_wh\":\"2\",\"booking_id\":\"LP-3999461905141\",\"partner_id\":\"15\",\"defective_return_to_entity_id\":\"15\",\"defective_return_to_entity_type\":\"vendor\",\"spare_id\":\"34553\",\"part_name\":\"ASSY,SPEAKER,BOOM,BOX,50DLED,STRATA\",\"service_center_id\":\"855\",\"sent_entity_type\":\"vendor\",\"model\":\"LEDTVVKV50FH16XAH\",\"booking_partner_id\":\"247130\",\"shipping_quantity\":\"1\"},\"8\":{\"inventory_id\":\"51627\",\"is_micro_wh\":\"2\",\"booking_id\":\"LP-4009971905141\",\"partner_id\":\"15\",\"defective_return_to_entity_id\":\"15\",\"defective_return_to_entity_type\":\"vendor\",\"spare_id\":\"34831\",\"part_name\":\"ASSY,PCB,MT5507,SMART,N-COMBO,MT\",\"service_center_id\":\"855\",\"sent_entity_type\":\"vendor\",\"model\":\"SMTTVVNW32HH55SAF\",\"booking_partner_id\":\"247130\",\"shipping_quantity\":\"1\"},\"9\":{\"inventory_id\":\"50092\",\"is_micro_wh\":\"2\",\"booking_id\":\"LP-3990131905131\",\"partner_id\":\"15\",\"defective_return_to_entity_id\":\"15\",\"defective_return_to_entity_type\":\"vendor\",\"spare_id\":\"34896\",\"part_name\":\"ASSY,SPEAKER,BOOM,BOX,40DLED,8227-C\",\"service_center_id\":\"855\",\"sent_entity_type\":\"vendor\",\"model\":\"DDBTVVJU40FH17XAH\",\"booking_partner_id\":\"247130\",\"shipping_quantity\":\"1\"},\"10\":{\"inventory_id\":\"50510\",\"is_micro_wh\":\"2\",\"booking_id\":\"LP-3966061905111\",\"partner_id\":\"15\",\"defective_return_to_entity_id\":\"15\",\"defective_return_to_entity_type\":\"vendor\",\"spare_id\":\"35826\",\"part_name\":\"ASSY,SPEAKER,BOX,32DLED,FERRITE,COMBO\",\"service_center_id\":\"855\",\"sent_entity_type\":\"vendor\",\"model\":\"DDBTVVJU32HH18XKH\",\"booking_partner_id\":\"247130\",\"shipping_quantity\":\"1\"},\"11\":{\"inventory_id\":\"50510\",\"is_micro_wh\":\"2\",\"booking_id\":\"LP-4095641905201\",\"partner_id\":\"15\",\"defective_return_to_entity_id\":\"15\",\"defective_return_to_entity_type\":\"vendor\",\"spare_id\":\"38520\",\"part_name\":\"ASSY,SPEAKER,BOX,32DLED,FERRITE,COMBO\",\"service_center_id\":\"855\",\"sent_entity_type\":\"vendor\",\"model\":\"LEDTVIVC32F02K\",\"booking_partner_id\":\"247130\",\"shipping_quantity\":\"1\"},\"12\":{\"inventory_id\":\"51066\",\"is_micro_wh\":\"2\",\"booking_id\":\"LP-3966311905111\",\"partner_id\":\"15\",\"defective_return_to_entity_id\":\"15\",\"defective_return_to_entity_type\":\"vendor\",\"spare_id\":\"38751\",\"part_name\":\"BAR,LED,50DLED,HCG,CR,7LED,BIG-H,MALE\",\"service_center_id\":\"8\",\"sent_entity_type\":\"vendor\",\"model\":\"LEDTVVMD55FH0ZFAP\",\"booking_partner_id\":\"247130\",\"shipping_quantity\":\"1\"}}","sender_entity_id":"15","sender_entity_type":"vendor","wh_name":"247around Ghaziabad (Uttar Pradesh)","receiver_partner_id":"247130","from_gst_number":"7","to_gst_number":"5"}';
       // $_POST = json_decode($str, true);
        if (!empty($this->session->userdata('warehouse_id'))) {
            $this->checkUserSession();
        } else {
            $this->check_WH_UserSession();
        }
        $is_r = $this->is_reverse_purchase_invoice_generated();
        if (!empty($is_r)) {
            $res['status'] = false;
            $res['message'] = 'Reverse Invoice already generated for the booking id: ' . $is_r;
        } else {
            $sender_entity_id = $this->input->post('sender_entity_id');
            $sender_entity_type = $this->input->post('sender_entity_type');
            $postData = json_decode($this->input->post('data'));
            //$wh_name = $this->input->post('wh_name');
            if (!empty($sender_entity_id) && !empty($sender_entity_type) && !empty($postData)) {

                $vendor_details = $this->vendor_model->getVendorDetails("district, state, sc_code", array('service_centres.id' => $sender_entity_id));

                $invoice = $this->inventory_invoice_settlement($sender_entity_id, $sender_entity_type, $vendor_details);

                if (!empty($invoice['processData'])) {

                    if (empty($invoice['not_update_booking_id'])) {
                        $res['status'] = TRUE;
                        $res['message'] = 'Details Updated Successfully';
                    } else {
                        $res['status'] = false;
                        $res['message'] = "These Bookings not updated " . implode(',', $invoice['not_update_booking_id']) .
                                " Please Contact to backend team.";
                    }
                } else {
                    $res['status'] = false;
                    $res['message'] = "There is no inventory invoice to tag with your selected inventory.";
                }
            } else {
                $res['status'] = false;
                $res['message'] = 'All fields are required';
            }
        }

        echo json_encode($res);
    }

    /**
     * @desc This function is used to check 
     * @return String
     * TO DO - Need to add warehouse challan
     */
    function is_reverse_purchase_invoice_generated(){
        $postData1 = json_decode($this->input->post('data'), true);
        $booking_id = "";
        if (!empty($postData1)) {
            foreach ($postData1 as $value) {
                $spareData = $this->partner_model->get_spare_parts_by_any("reverse_purchase_invoice_id, wh_challan_number", array('spare_parts_details.id' => $value['spare_id']));
                if (!empty($spareData) && !empty($spareData[0]['reverse_purchase_invoice_id'])) {
                    $booking_id = $value['booking_id'];
                    break;
                }
                if (!empty($spareData) && !empty($spareData[0]['wh_challan_number'])) {
                    $booking_id = $value['booking_id'];
                    break;
                }
            }
        }
        
        return $booking_id;
    }
    
     /**
     *  @desc : This function is used to send defective spare by WH to partner
     *  @param : void
     *  @return :$res JSON
     */
     function send_defective_to_partner_from_wh_on_challan() {
        log_message("info", __METHOD__ . json_encode($this->input->post(), true));
        
        if(!empty($this->session->userdata('warehouse_id'))) {
            $this->checkUserSession();
            $sf_id = $this->session->userdata('warehouse_id');
        } else {
            $this->check_WH_UserSession();
            $sf_id = $this->session->userdata('service_center_id');
        }
        $awb_by_wh = $this->input->post('awb_by_wh');
        $courier_name_by_wh = $this->input->post('courier_name_by_wh');
        $courier_price_by_wh = $this->input->post('courier_price_by_wh');
        $defective_parts_shippped_date_by_wh = $this->input->post('defective_parts_shippped_date_by_wh');
        $kilo_gram = $this->input->post('shipped_spare_parts_weight_in_kg') ?: '0';
        $gram = $this->input->post('shipped_spare_parts_weight_in_gram') ?: '00';

        $billable_weight = $kilo_gram . "." . $gram;
        //$postData = json_decode('{"0":{"inventory_id":"326","is_micro_wh":"0","booking_id":"PV-4568991908222","partner_id":"247073","defective_return_to_entity_id":"15","defective_return_to_entity_type":"vendor","spare_id":"90728","part_name":"","service_center_id":"1","sent_entity_type":"partner","model":"CTV PLANUS 2100 DLX-1","booking_partner_id":"247073","shipping_quantity":"1"}}',TRUE);
        $postData = json_decode($this->input->post('data'), true);
        //$wh_name = $this->input->post('wh_name');
        if (!empty($postData) && !empty($awb_by_wh) && !empty($courier_name_by_wh) && !empty($defective_parts_shippped_date_by_wh)) {
            $this->upload_defective_spare_pic();
            $booking_id = $postData[0]['booking_id'];
            $exist_courier_image = $this->input->post("exist_courier_image");
            $data['defective_parts_shippped_date_by_wh'] = $this->input->post('defective_parts_shippped_date_by_wh');
            $data['courier_name_by_wh'] = $this->input->post('courier_name_by_wh');
            $data['courier_price_by_wh'] = $courier_price_by_wh;
            $data['awb_by_wh'] = $awb_by_wh;
            $data['defective_parts_shippped_courier_pic_by_wh'] = $exist_courier_image;
            $data['status'] = DEFECTIVE_PARTS_SEND_TO_PARTNER_BY_WH;
            //$courier_details = array();
            $exist_courier_details = $this->inventory_model->get_generic_table_details('courier_company_invoice_details', '*', array('awb_number' => $awb_by_wh), array());

            if (!empty($exist_courier_image)) {
                $data['defective_parts_shippped_courier_pic_by_wh'] = $exist_courier_image;
            } else {
                $data['defective_parts_shippped_courier_pic_by_wh'] = trim($this->input->post("sp_parts"));
                if (empty($exist_courier_details)) {
                    $vendor_details = $this->vendor_model->getVendorDetails("district, state", array('service_centres.id' => $this->input->post("sender_entity_id")));
                    $from_city = $vendor_details[0]['district'];
                    $from_state = $vendor_details[0]['state'];

                    $partner_details = $this->partner_model->getpartner($this->input->post('receiver_partner_id'));
                    $to_city = $partner_details[0]['district'];
                    $to_state = $partner_details[0]['state'];
                    
                    $awb_data = array(
                        'awb_number' => trim($awb_by_wh),
                        'company_name' => trim($courier_name_by_wh),
                        'partner_id' => $this->input->post('receiver_partner_id'),
                        'courier_charge' => trim($courier_price_by_wh),
                        'box_count' => trim($this->input->post('shipped_spare_parts_boxes_count')), //defective_parts_shipped_gram
                        'small_box_count' => trim($this->input->post('shipped_spare_parts_small_boxes_count')), //defective_parts_shipped_gram
                        'billable_weight' => trim($billable_weight),
                        'actual_weight' => trim($billable_weight),
                        'basic_billed_charge_to_partner' => trim($courier_price_by_wh),
                        'booking_id' => trim($booking_id),
                        'courier_invoice_file' => trim($this->input->post("sp_parts")),
                        'shippment_date' => trim($this->input->post('defective_parts_shippped_date_by_wh')), //defective_part_shipped_date
                        'created_by' => 3,
                        'is_exist' => 1,
                        'sender_city' => $from_city,
                        'receiver_city' => $to_city,
                        'sender_state' => $from_state,
                        'receiver_state' => $to_state
                    );

                    $this->service_centers_model->insert_into_awb_details($awb_data);
                }
            }

            foreach ($postData as $key => $val) {
                if (!empty($val['spare_id'])) {
                    /**
                     * @modifiedBy Ankit Rajvanshi
                     */
                    // Fetch spare details of $spare_id.
                    $spare_part_detail = $this->reusable_model->get_search_result_data('spare_parts_details', '*', ['id' => $val['spare_id']], NULL, NULL, NULL, NULL, NULL)[0];
                    $is_spare_consumed = $this->reusable_model->get_search_result_data('spare_consumption_status', '*', ['id' => $spare_part_detail['consumed_part_status_id']], NULL, NULL, NULL, NULL, NULL)[0]['is_consumed'];
                    $data['status'] = DEFECTIVE_PARTS_SEND_TO_PARTNER_BY_WH;
                    if(!empty($is_spare_consumed) && $is_spare_consumed == 1) {
                        $data['status'] = DEFECTIVE_PARTS_SEND_TO_PARTNER_BY_WH;
                    } else {
                        $data['status'] = OK_PARTS_SEND_TO_PARTNER_BY_WH;
                    }  
                    
                    $data["spare_parts_details.wh_to_partner_defective_shipped_date"] = date('Y-m-d H:i:s');
                    $affected_id = $this->service_centers_model->update_spare_parts(array('id' => $val['spare_id']), $data);
                    
                    if(!empty($this->session->userdata('warehouse_id'))) { 
                        $agent_id = $this->session->userdata('id');
                        $agent_name = $this->session->userdata('employee_id');
                        $entity_id = _247AROUND;
                        $entity_type = _247AROUND_EMPLOYEE_STRING;
                    } else {
                        $agent_id = $this->session->userdata('service_center_agent_id');
                        $agent_name = $this->session->userdata('service_center_name');
                        $entity_id = $this->session->userdata('service_center_id');
                        $entity_type = _247AROUND_SF_STRING;                        
                    }
                   
                    /* Insert Spare Tracking Details */
                    $tracking_details = array('spare_id' => $val['spare_id'], 'action' => $data['status'], 'remarks' => $data['status'], 'agent_id' => $agent_id, 'entity_id' => $entity_id, 'entity_type' => $entity_type);
                    $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                    $actor = ACTOR_NOT_DEFINE;
                    $next_action = NEXT_ACTION_NOT_DEFINE;
                    
                    // fetch record from booking details of $booking_id.
                    $booking_id = $spare_part_detail['booking_id'];
                    $partner_id = $spare_part_detail['partner_id'];
                    $booking_details = $this->booking_model->get_booking_details('*',['booking_id' => $booking_id])[0];

                    $is_exist = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id, spare_parts_details.status", array('spare_parts_details.booking_id' => $booking_id, 'spare_parts_details.defective_part_required' => 1, "status IN  (
                                    '" . DEFECTIVE_PARTS_RECEIVED . "', '" . DEFECTIVE_PARTS_RECEIVED_BY_WAREHOUSE . "', '" .Ok_PARTS_RECEIVED_BY_WAREHOUSE . "', '" . Ok_PARTS_RECEIVED . "', '".OK_PARTS_SHIPPED."', '".DEFECTIVE_PARTS_SHIPPED."') " => NULL));

                    $actor = $next_action = 'not_define';
                    if (empty($is_exist)) {
                        $booking_internal_status = $data['status'];
                    } else {
                        $booking_internal_status = $is_exist[0]['status'];
                    }

                    // Change booking internal status if booking is completed.
                    if($booking_details['current_status'] == _247AROUND_COMPLETED) {
                        $booking['internal_status'] = $booking_internal_status;
                        $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_COMPLETED, $booking['internal_status'], $partner_id, $booking_id);
                        if (!empty($partner_status)) {
                            $booking['partner_current_status'] = $partner_status[0];
                            $booking['partner_internal_status'] = $partner_status[1];
                            $actor = $booking['actor'] = $partner_status[2];
                            $next_action = $booking['next_action'] = $partner_status[3];
                        }
                        $this->booking_model->update_booking($booking_id, $booking);
                    }
                    
                    /**
                     * Check session and set entity id and agent id.
                     */
                    if(!empty($this->session->userdata('warehouse_id'))) { 
                        $this->notify->insert_state_change($val['booking_id'], $data['status'], "", $data['status'], $agent_id, $agent_name, $actor, $next_action, $entity_id, NULL, $val['spare_id']);
                    } else {
                        $this->notify->insert_state_change($val['booking_id'], $data['status'], "", $data['status'], $agent_id, $agent_name, $actor, $next_action, NULL, $entity_id, $val['spare_id']);
                    }
                    
                    log_message("info", "Booking State change inserted");
                }
            }

            if ($affected_id) {
                $res['status'] = TRUE;
                $res['message'] = 'Details Updated Successfully';
            } else {
                $res['status'] = TRUE;
                $res['message'] = 'Details Not Updated';
            }
        } else {
            $res['status'] = false;
            $res['message'] = 'All fields are required';
        }

        echo json_encode($res);
    }

    /**
     *  @desc : This function is used to upload courier image
     */
    function upload_defective_spare_pic() {
        $allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
        $booking_id = $this->input->post("booking_id");
        $exist_courier_image = $this->input->post("exist_courier_image");

        if (!empty($exist_courier_image)) {
            $_POST['sp_parts'] = $exist_courier_image;
            return true;
        } else {
            $defective_courier_receipt = $this->miscelleneous->upload_file_to_s3($_FILES["file"], "defective_courier_receipt", $allowedExts, $booking_id, "misc-images", "sp_parts");
            if ($defective_courier_receipt) {
                return true;
            } else {
                $this->form_validation->set_message('upload_defective_spare_pic', 'File size or file type is not supported. Allowed extentions are "png", "jpg", "jpeg" and "pdf". '
                        . 'Maximum file size is 5 MB.');
                return false;
            }
        }
    }

    /**
     * @desc This function is used to settle inventor invoice and insert into inventory invoice leadger
     * @param String $sender_entity_id
     * @param String $sender_entity_type
     * @return boolean
     */
    function inventory_invoice_settlement($sender_entity_id, $sender_entity_type, $vendor_details) {
        $postData1 = json_decode($this->input->post('data'), true);
        log_message('info', __METHOD__ . " " . print_r($postData1, true));
        $partner_spare = array();
        $warehouse_spare = array();
        $invoice = array();

        foreach ($postData1 as $value) {
            if ($value['is_micro_wh'] == 0) {
                //Partner Sent this part
                array_push($partner_spare, $value);
            } else {

                array_push($warehouse_spare, $value);
            }
        }

        if (!empty($warehouse_spare)) {
            $invoice = $this->generate_challan_return_parts_from_wh_to_brand_on_invoice($postData1, $sender_entity_id, $sender_entity_type, $vendor_details);
        }

        return $invoice;
    }

     /**
     * @desc This is used to generate challan for invoicing. Here we are generating only challan for PO.
     * @param Array $postData
     * @param int $sender_entity_id
     * @param String $sender_entity_type
     * @param Array $vendor_details
     * @return boolean
     */
    function generate_challan_return_parts_from_wh_to_brand_on_invoice($postData, $sender_entity_id, $sender_entity_type, $vendor_details) {
        log_message('info', __METHOD__ . " Data " . print_r($postData, TRUE) . " Entity id " . $sender_entity_id);
        $from_gst_id = $this->input->post('from_gst_number');
        $to_gst_id = $this->input->post('to_gst_number');
        $invoiceData = $this->invoice_lib->settle_inventory_invoice_annexure($postData, $from_gst_id, $to_gst_id);

        $booking_id_array = array();
        $sp_id = array();
        if (!empty($invoiceData['processData'])) {

            $entity_details = $this->partner_model->getpartner_details("gst_number, primary_contact_email,state, company_name, address, district, pincode,", array('partners.id' => $invoiceData['booking_partner_id']));
            foreach ($invoiceData['processData'] as $key => $invoiceValue) {
                
                $invoice = array();


                $template1 = array(
                    'table_open' => '<table border="1" cellpadding="2" cellspacing="0" class="mytable">'
                );
                $this->table->set_template($template1);
                $this->table->set_heading(array('Part Name', 'Invoice ID', 'Reference Invoice ID', 'Total Qty', 'Total Amount', 'Booking Id'));

                $tmp_k = explode('-', $key);
                $tmp_invoice = $vendor_details[0]['sc_code']."-" . $tmp_k[0];
                $invoice_id = $this->miscelleneous->create_sf_challan_id($tmp_invoice);

                foreach ($invoiceValue['mapping'] as $m) {
                    $m['outgoing_invoice_id'] = $invoice_id;
                    $this->invoices_model->insert_inventory_invoice($m);
                }

                foreach ($invoiceValue['data'] as $value) {
                    //Push booking ID
                    array_push($booking_id_array, $value['booking_id']);

                    if (!array_key_exists($value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0), $invoice)) {


                        $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['description'] = $value['part_number'] . " " . $value['part_name'] . "  Reference Invoice ID " . $value['incoming_invoice_id'];
                        $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['taxable_value'] = $value['rate'] * $value['qty'];
                        $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['invoice_id'] = $invoice_id;
                        $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['product_or_services'] = "Product";
                        $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['gst_number'] = $value['to_gst_number'];
                        if ($value['from_state_code'] == $value['to_state_code']) {
                            $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['c_s_gst'] = true;
                            $in_template ="247around_Challan_Intra_State.xlsx";
                        } else {
                            $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['c_s_gst'] = FALSE;
                            $in_template = "247around_Challan_Inter_State.xlsx";
                        }
                        $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['company_name'] = $entity_details[0]['company_name'];
                        $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['company_address'] = $value['to_address'];
                        $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['district'] = $value['to_city'];
                        $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['spare_id'] = $value['spare_id'];
                        $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['to_gst_number_id'] = $value['to_gst_number_id'];
                        $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['from_gst_number_id'] = $value['from_gst_number_id'];
                        $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['pincode'] = $value['to_pincode'];
                        $state = $this->invoices_model->get_state_code(array('state_code' => $value['to_state_code']))[0]['state'];
                        $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['state'] = $state;
                        $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['rate'] = $value['rate'];
                        $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['gst_rate'] = $value['gst_rate'];
                        $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['qty'] = $value['qty']; //1;
                        $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['hsn_code'] = $value['hsn_code'];
                        $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['inventory_id'] = $value['inventory_id'];
                        $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['partner_id'] = $value['booking_partner_id'];
                        $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['part_number'] = $value['part_number'];
                    } else {
                        $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['qty'] = $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['qty'] + $value['qty']; //1;
                        if (strpos($invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['description'], $value['incoming_invoice_id']) == false) {
                            $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['description'] = $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['description'] . " - " . $value['incoming_invoice_id'];
                        } else {
                            $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['description'] = $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['description'];
                        }

                        $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['taxable_value'] = $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['qty'] * $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['rate'];
                        
                        $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['spare_id'] = $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['spare_id'].",".$value['spare_id'];
                    }

                    $total_amount = ($invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['taxable_value'] + ($invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['taxable_value'] * ($value['gst_rate'] / 100)) );
                    $this->table->add_row($value['part_name'], $invoice_id, $value['incoming_invoice_id'], $invoice[$value['inventory_id'] . "_" . $value['gst_rate'] . "_" . round($value['rate'], 0)]['qty'], round($total_amount, 2), $value['booking_id']);

                    array_push($sp_id, $value['spare_id']);
                }

                $sd = $ed = $invoice_date = date("Y-m-d");
                $invoices = array_values($invoice);
                unset($invoice);

                log_message('info', __METHOD__ . " Spare Invoice Data " . print_r($invoices, TRUE) . " Entity id " . $sender_entity_id);
                $response = $this->invoices_model->_set_partner_excel_invoice_data($invoices, $sd, $ed, "Tax Invoice", $invoice_date);
                $response['meta']['invoice_template'] = $in_template;
                $response['meta']['invoice_id'] = $invoice_id;
                $response['meta']['invoice_type'] = "Delivery Challan";
                $response['booking'][0]['invoice_id'] = $response['meta']['invoice_id'];
                $response['meta']['main_company_gst_number'] = $invoiceValue['data'][0]['from_gst_number'];
                $response['meta']['main_company_state'] = $this->invoices_model->get_state_code(array('state_code' => $invoiceValue['data'][0]['from_state_code']))[0]['state'];
                $response['meta']['main_company_state_code'] = $invoiceValue['data'][0]['from_state_code'];
                $response['meta']['main_company_address'] = $invoiceValue['data'][0]['from_address'] . "," . $invoiceValue['data'][0]['from_city'];

                $response['meta']['main_company_pincode'] = $invoiceValue['data'][0]['from_pincode'];
                $response['meta']['main_company_seal'] = $invoiceValue['data'][0]['state_stamp_pic'];
                $response['meta']['meta_id'] = "Challan Number: ".$response['meta']['invoice_id'];
                $response['meta']['meta_date'] = "Date: ".$response['meta']['invoice_date'];

                $status = $this->invoice_lib->send_request_to_create_main_excel($response, "final");
                if ($status) {

                    log_message('info', __FUNCTION__ . ' Invoice File is created. invoice id' . $response['meta']['invoice_id']);
                    $convert = $this->invoice_lib->convert_invoice_file_into_pdf($response, "final", false, false, "vendor-partner-docs");
                    $output_file = "";
                    $template = "partner_inventory_invoice_annexure-v1.xlsx";
                    $output_file = $response['meta']['invoice_id'] . "-detailed.xlsx";

                    unset($response['meta']['main_company_logo_cell']);
                    unset($response['meta']['main_company_seal_cell']);
                    unset($response['meta']['main_company_sign_cell']);

                    $this->invoice_lib->generate_invoice_excel($template, $response['meta'], $invoiceValue['data'], TMP_FOLDER . $output_file);
                    
                    if(!empty($this->session->userdata('warehouse_id'))) {
                        $agent_id = $this->session->userdata('id');
                        $agent_type = _247AROUND_EMPLOYEE_STRING;
                    } else { 
                        $agent_id = $this->session->userdata('service_center_agent_id');
                        $agent_type = _247AROUND_SF_STRING;
                    }
                    
                    $challan_details = array(
                        'challan_id' => $response['meta']['invoice_id'],
                        'sender_entity_id' => $sender_entity_id,
                        'sender_entity_type' => $sender_entity_type,
                        'receiver_entity_id' => $response['booking'][0]['partner_id'],
                        'receiver_entity_type' => 'partner',
                        'main_file' => $convert['main_pdf_file_name'],
                        'annx_file' => $output_file,
                        'taxable_value' => $response['meta']['total_taxable_value'],
                        'gst_amount' => ($response['meta']["igst_total_tax_amount"] + $response['meta']["sgst_total_tax_amount"] + $response['meta']["cgst_total_tax_amount"]),
                        'quantity' => $response['meta']['parts_count'],
                        'agent_id' => $agent_id,
                        'agent_type' => $agent_type,
                        'type' => MSL_DEFECTIVE_CHALLAN_TYPE,
                        'from_gst_number_id' => $response['booking'][0]['from_gst_number_id'],
                        'to_gst_number_id' => $response['booking'][0]['to_gst_number_id']
                        
                    );
                    
                    
                    $challan_id = $this->invoices_model->insert_challan_details($challan_details);

                    $this->invoice_lib->insert_def_challan_breakup($response, $challan_id);
                    
                    $this->invoice_lib->upload_invoice_to_S3($response['meta']['invoice_id'], true, false, 'vendor-partner-docs');

                    log_message('info', __METHOD__ . "=> Insert Invoices in partner invoice table");

                    $invoiceData['invoice'][] = $response['meta']['invoice_id'];
                    $main_file = S3_WEBSITE_URL . "vendor-partner-docs/" . $convert['main_pdf_file_name'];

                    if (!empty($output_file)) {
                        $detailed_file = TMP_FOLDER . $output_file;
                    } else {
                        $detailed_file = "";
                    }

                    $parts_table = $this->table->generate();

                    $this->send_defective_return_mail($response['booking'][0]['partner_id'], $parts_table, $main_file, $detailed_file, $invoice_id);

                    foreach ($sp_id as $spare_id) {
                        
                        $this->service_centers_model->update_spare_parts(array('id' => $spare_id), array('wh_challan_number' =>  $response['meta']['invoice_id'], 'wh_challan_file' => $convert['main_pdf_file_name']));
                        /* Insert Spare Tracking Details */
                        $tracking_details = array('spare_id' => $spare_id, 'action' => CHALLAN_GENERATION_STATUS, 'remarks' => $response['meta']['invoice_id']);
                        if (!empty($this->session->userdata('warehouse_id'))) {
                            $tracking_details['agent_id'] = $this->session->userdata('id');
                            $tracking_details['entity_id'] = _247AROUND;
                            $tracking_details['entity_type'] = _247AROUND_EMPLOYEE_STRING;
                        } else {
                            $tracking_details['agent_id'] = $this->session->userdata('service_center_agent_id');
                            $tracking_details['entity_id'] = $this->session->userdata('service_center_id');
                            $tracking_details['entity_type'] = _247AROUND_SF_STRING;
                        }

                        $this->service_centers_model->insert_spare_tracking_details($tracking_details);
                    }
                    unset($response);
                    
                    $sp_id = array();
                    
                }
            }

            $invoiceData['booking_id_array'] = $booking_id_array;
            $invoiceData['spare_id_array'] = $sp_id;

            return $invoiceData;
        } else {

            return false;
        }
    }
    
    function send_defective_return_mail($booking_partner_id, $parts_table, $main_file, $detailed_file, $invoice_id) {

        //send email to partner warehouse incharge
        $email_template = $this->booking_model->get_booking_email_template(DEFECTIVE_SPARE_SEND_BY_WH_TO_PARTNER);
        $wh_incharge_id = $this->reusable_model->get_search_result_data("entity_role", "id", array("entity_type" => _247AROUND_PARTNER_STRING, 'role' => WAREHOUSE_INCHARCGE_CONSTANT), NULL, NULL, NULL, NULL, NULL, array());

        if (!empty($wh_incharge_id)) {
             $courier_name_by_wh = $this->input->post('courier_name_by_wh');

             $defective_parts_shippped_date_by_wh = $this->input->post('defective_parts_shippped_date_by_wh');
             $awb_by_wh = $this->input->post('awb_by_wh');
             $wh_name = $this->input->post('wh_name');

            //get 247around warehouse incharge email
            $wh_where = array('contact_person.role' => $wh_incharge_id[0]['id'],
                'contact_person.entity_id' => $booking_partner_id,
                'contact_person.entity_type' => _247AROUND_PARTNER_STRING
            );

            $email_details = $this->inventory_model->get_warehouse_details('contact_person.official_email', $wh_where, FALSE, TRUE);
            if (!empty($email_details) && !empty($email_template)) {
//                                    $wh_email = "";
//                                    $sf_wh_incharge_id = $this->reusable_model->get_search_result_data("entity_role", "id", array("entity_type" => _247AROUND_SF_STRING, 'role' => WAREHOUSE_INCHARCGE_CONSTANT), NULL, NULL, NULL, NULL, NULL, array());
//                                    // Sf warehouse
//                                    if (!empty($sf_wh_incharge_id)) {
//                                        $sf_wh_where = array('contact_person.role' => $sf_wh_incharge_id[0]['id'],
//                                            'contact_person.entity_id' => $sender_entity_id,
//                                            'contact_person.entity_type' => _247AROUND_SF_STRING
//                                        );
//
//                                        $sf_email_details = $this->inventory_model->get_warehouse_details('contact_person.official_email', $sf_wh_where, FALSE, TRUE);
//                                        $wh_email = ", " . $sf_email_details[0]['official_email'];
//                                    }
                //generate courier details table
                $this->table->set_heading(array('Courier Name', 'AWB Number', 'Shipment Date'));
                $this->table->add_row(array($courier_name_by_wh, $awb_by_wh, date('d-m-Y', strtotime($defective_parts_shippped_date_by_wh))));
                $courier_details_table = $this->table->generate();
                $partner_details = $this->partner_model->getpartner_details('public_name', array('partners.id' => $booking_partner_id));
                $partner_name = '';
                if (!empty($partner_details)) {
                    $partner_name = $partner_details[0]['public_name'];
                }
                $to = $email_details[0]['official_email'];
                $cc = $email_template[3];
                $subject = vsprintf($email_template[4], array($wh_name, $partner_name));
                $message = vsprintf($email_template[0], array($wh_name, $parts_table, $courier_details_table));
                $bcc = $email_template[5];
                $this->notify->sendEmail($email_template[2], $to, $cc, $bcc, $subject, $message, $main_file, DEFECTIVE_SPARE_SEND_BY_WH_TO_PARTNER, $detailed_file);
                
                unlink(TMP_FOLDER . $invoice_id."-detailed.xlsx");
                unlink(TMP_FOLDER . $invoice_id.".pdf");
                unlink(TMP_FOLDER . $invoice_id . ".xlsx");
                unlink(TMP_FOLDER . "copy_" . $invoice_id . ".xlsx");
                unlink(TMP_FOLDER . "copy_" . $invoice_id.".pdf");
                        
                
            }
        }
    }

    /**
     *  @desc : This function is used to get data for the spare which send by WH to partner
     *  @param : void
     *  @return :$res JSON
     */
    function get_defective_spare_send_by_wh_to_partner() {
        $post = $this->get_post_data();
        $post['column_order'] = array();
        $post['column_search'] = array('part_name', 'model_number', 'type');
        $post['where'] = array('i.receiver_entity_id' => trim($this->input->post('receiver_entity_id')),
            'i.receiver_entity_type' => trim($this->input->post('receiver_entity_type')),
            'i.is_defective' => 1,
            '(i.is_partner_ack IS NULL OR i.is_partner_ack = 0)' => null);

        $select = "services.services,inventory_master_list.*,CASE WHEN(sc.name IS NOT NULL) THEN (sc.name) 
                    WHEN(p.public_name IS NOT NULL) THEN (p.public_name) 
                    WHEN (e.full_name IS NOT NULL) THEN (e.full_name) END as receiver, 
                    CASE WHEN(sc1.name IS NOT NULL) THEN (sc1.name) 
                    WHEN(p1.public_name IS NOT NULL) THEN (p1.public_name) 
                    WHEN (e1.full_name IS NOT NULL) THEN (e1.full_name) END as sender,i.*";
        $list = $this->inventory_model->get_spare_need_to_acknowledge($post, $select);
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
            "recordsFiltered" => $this->inventory_model->count_filtered_spare_need_to_acknowledge($post),
            "data" => $data,
        );

        echo json_encode($output);
    }

    /**
     *  @desc : This function is used to generate data for the spare which send by WH to partner
     *  @param : void
     *  @return :$res JSON
     */
    function get_spare_send_by_wh_to_partner_table($inventory_list, $no) {
        $row = array();

        $row[] = $no;
        $row[] = "<a href='" . base_url() . "partner/booking_details/" . $inventory_list->booking_id . "'target='_blank'>" . $inventory_list->booking_id . "</a>";
        $row[] = $inventory_list->services;
        $row[] = $inventory_list->type;
        $row[] = $inventory_list->part_name;
        $row[] = $inventory_list->quantity;
        $row[] = $row[] = "<input type='checkbox' class= 'check_single_row' id='ack_spare_$inventory_list->inventory_id' data-inventory_id='" . $inventory_list->inventory_id . "' data-ledger_id = '" . $inventory_list->id . "' data-sender_entity_id = '" . $inventory_list->sender_entity_id . "' data-sender_entity_type = '" . $inventory_list->sender_entity_type . "' data-booking_id = '" . $inventory_list->booking_id . "' data-part_name = '" . $inventory_list->part_name . "'>";

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
        $receiver_entity_name = $this->input->post('receiver_entity_name');
        $postData = json_decode($this->input->post('data'));
        $sender_entity_id = array_unique(array_column((array) $postData, 'sender_entity_id'))[0];
        if (!empty($receiver_entity_id) && !empty($receiver_entity_type) && !empty($postData)) {
            $template1 = array(
                'table_open' => '<table border="1" cellpadding="2" cellspacing="0" class="mytable">'
            );

            $this->table->set_template($template1);

            $this->table->set_heading(array('Part Name', 'Booking ID'));
            foreach ($postData as $value) {
                //acknowledge spare by setting is_partner_ack flag = 1 in inventory ledger table
                $update = $this->inventory_model->update_ledger_details(array('is_partner_ack' => 1, 'partner_ack_date' => date('Y-m-d H:i:s')), array('id' => $value->ledger_id));
                if (!empty($update)) {
                    $this->table->add_row($value->part_name, $value->booking_id);
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

            //send email to partner warehouse incharge that 247around warehouse received spare
            $email_template = $this->booking_model->get_booking_email_template("defective_spare_received_by_partner_from_wh");
            $wh_incharge_id = $this->reusable_model->get_search_result_data("entity_role", "id", array("entity_type" => _247AROUND_SF_STRING, 'role' => WAREHOUSE_INCHARCGE_CONSTANT), NULL, NULL, NULL, NULL, NULL, array());
            if (!empty($wh_incharge_id)) {

                //get 247around warehouse incharge email
                $wh_where = array('contact_person.role' => $wh_incharge_id[0]['id'],
                    'contact_person.entity_id' => $sender_entity_id,
                    'contact_person.entity_type' => _247AROUND_SF_STRING
                );
                $email_details = $this->inventory_model->get_warehouse_details('contact_person.official_email', $wh_where, FALSE, TRUE);
                if (!empty($email_details) && !empty($email_template)) {
                    //generate part details table                                        
                    $parts_details_table = $this->table->generate();

                    $to = $email_details[0]['official_email'];
                    $cc = $email_template[3];
                    $subject = vsprintf($email_template[4], array($receiver_entity_name));
                    $message = vsprintf($email_template[0], array($receiver_entity_name, $parts_details_table));

                    $this->notify->sendEmail($email_template[2], $to, $cc, "", $subject, $message, "", 'defective_spare_received_by_partner_from_wh');
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
     *  @desc : This function is used to upload the spare invoice file which send by partner to warehouse
     *  @param : $file_details array()
     *  @return :$res array
     */
    function upload_spare_invoice_file($file_details) {

        $MB = 1048576;
        //check if upload file is empty or not
        if (!empty($file_details['invoice_file']['name'])) {
            //check upload file size. it should not be greater than 2mb in size
            if ($file_details['invoice_file']['size'] <= 5 * $MB) {
                $allowed = array('pdf', 'PDF');
                $ext = pathinfo($file_details['invoice_file']['name'], PATHINFO_EXTENSION);
                //check upload file type. it should be pdf.
                if (in_array($ext, $allowed)) {
                    $upload_file_name = str_replace(' ', '_', trim($file_details['invoice_file']['name']));

                    $file_name = 'spare_invoice_' . rand(10, 100) . '_' . $upload_file_name;
                    //Upload files to AWS
                    $directory_xls = "invoices-excel/" . $file_name;
                    $this->s3->putObjectFile($file_details['invoice_file']['tmp_name'], BITBUCKET_DIRECTORY, $directory_xls, S3::ACL_PUBLIC_READ);

                    $res['status'] = true;
                    $res['message'] = $file_name;
                } else {
                    $res['status'] = false;
                    $res['message'] = 'Uploaded file must in pdf.';
                }
            } else {
                $res['status'] = false;
                $res['message'] = 'Uploaded file size can not be greater than 5 mb';
            }
        } else {
            $res['status'] = false;
            $res['message'] = 'Please Upload File';
        }

        return $res;
    }

    /**
     *  @desc : This function is used to get inventory part number
     *  @param : void
     *  @return : $res array() // consist response message and response status
     */
    function get_parts_number() {
        
        $part_name = trim($this->input->post('part_name'));

        $part_number = trim($this->input->post('part_number'));
        
        $post['length'] = -1;
        $post['where'] = array('entity_id' => trim($this->input->post('entity_id')), 'entity_type' => trim($this->input->post('entity_type')), 'service_id' => trim($this->input->post('service_id')), 'part_number' => $part_number);
        $post['order'] = array(array('column' => 0, 'dir' => 'ASC'));
        $post['column_order'] = array('part_name');
        $inventory_details = $this->inventory_model->get_inventory_master_list($post, 'inventory_master_list.part_name', true);
        if (!empty($inventory_details)) {
            if ($this->input->post('is_option_selected')) {
                $option = '<option selected disabled>Select Part Name</option>';
            } else {
                $option = '';
            }


            foreach ($inventory_details as $value) {
                $option .= "<option value='" . $value['part_name'] . "'";
                $option .= " > ";
                $option .= $value['part_name'] . "</option>";
            }

            echo $option;
        } else {
            echo "Part Number Not Exist In Our System";
        }
    }

    /**
     *  @desc : This function is used to upload partner appliance model details in appliance_model_details table
     *  @param : void
     *  @return :void
     */
    function upload_appliance_model_details() {
        $this->checkUserSession();
        redirect(base_url() . "employee/service_centre_charges/upload_excel_form");
        //$data['services'] = $this->booking_model->selectservice();
        //$this->miscelleneous->load_nav_header();
        //$this->load->view('employee/upload_appliance_model_details',$data);
    }

    /**
     * @desc: This function is used to check warehouse session.
     * @param: void
     * @return: true if details matches else session is destroyed.
     */
    function check_WH_UserSession() {
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'service_center') && !empty($this->session->userdata('service_center_id')) && (!empty($this->session->userdata('is_wh')) || !empty($this->session->userdata('is_micro_wh')) )) {
            return TRUE;
        } else {
            log_message('info', __FUNCTION__ . " Session Expire for Service Center");
            $this->session->sess_destroy();
            redirect(base_url() . "service_center/login");
        }
    }

    /**
     *  @desc : This function is used to upload model and part number mapping file
     *  @param : void
     *  @return :void
     */
    function upload_bom_file($isAdmin = 1) {
        if($isAdmin == 1) {
            log_message('info', __FUNCTION__ . ' Function Start For Admin '.$this->session->userdata('id'));
            $this->checkUserSession();
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/upload_applinace_model_mapping_with_inventory');
        }
        else
        {
            log_message('info', __FUNCTION__ . ' Function Start For Partner '.$this->session->userdata('partner_id'));
            $this->check_PartnerSession();
            $this->miscelleneous->load_partner_nav_header();
            $this->load->view('partner/upload_appliance_model_mapping_with_inventory');
            $this->load->view('partner/partner_footer');
        }
    }

    /**
     *  @desc : This function is used to upload the spare courier file which send by partner to warehouse
     *  @param : $file_details array()
     *  @return :$res array
     */
    function upload_spare_courier_file($file_details) {

        $MB = 1048576;
        //check if upload file is empty or not
        if (!empty($file_details['courier_file']['name'])) {
            //check upload file size. it should not be greater than 2mb in size
            if ($file_details['courier_file']['size'] <= 5 * $MB) {
                $upload_file_name = str_replace(' ', '_', trim($file_details['courier_file']['name']));

                $file_name = 'spare_courier_' . rand(10, 100) . '_' . $upload_file_name;
                //Upload files to AWS
                $directory_xls = "vendor-partner-docs/" . $file_name;
                $this->s3->putObjectFile($file_details['courier_file']['tmp_name'], BITBUCKET_DIRECTORY, $directory_xls, S3::ACL_PUBLIC_READ);

                $res['status'] = true;
                $res['message'] = $file_name;
            } else {
                $res['status'] = false;
                $res['message'] = 'Uploaded file size can not be greater than 5 mb';
            }
        } else {
            $res['status'] = TRUE;
            $res['message'] = '';
        }

        return $res;
    }

    /**
     * @desc This function is used to check if invoice id exists in our database
     * @param void
     * @param $res array()
     */
    function check_invoice_id_exists($invoice_id_temp) {
        $res = array();
        if ($invoice_id_temp) {
            $invoice_id = str_replace("/", "-", $invoice_id_temp);
            $count = $this->invoices_model->get_invoices_details(array('invoice_id' => $invoice_id), 'count(invoice_id) as count');
            if (!empty($count[0]['count'])) {
                $res['status'] = TRUE;
                $res['msg'] = $count[0]['count'];
            } else {
                $res['status'] = FALSE;
                $res['msg'] = 'no data found';
            }
        } else {
            $res['status'] = FALSE;
            $res['msg'] = 'Invalid Request';
        }

        if ($this->input->post('is_ajax')) {
            echo json_encode($res);
        } else {
            return $res;
        }
    }

    /**
     * @desc This function is used to check if booking id exists in our database
     * @param void
     * @param $res array()
     */
    function check_booking_id_exists($booking_id) {
        $res = array();
        if ($booking_id) {
            $count = $this->booking_model->get_bookings_count_by_any('count(booking_id) as count', array('booking_id' => $booking_id));
            if (!empty($count[0]['count'])) {
                $res['status'] = TRUE;
                $res['msg'] = $count[0]['count'];
            } else {
                $res['status'] = FALSE;
                $res['msg'] = 'no data found';
            }
        } else {
            $res['status'] = FALSE;
            $res['msg'] = 'Invalid Request';
        }

        if ($this->input->post('is_ajax')) {
            echo json_encode($res);
        } else {
            return $res;
        }
    }

    /**
     *  @desc : This function is used to show appliance models
     *  @param : void
     *  @return : void
     */
    function appliance_model_list() {
        $this->checkUserSession();
        $this->miscelleneous->load_nav_header();
        $this->load->view("employee/appliance_model_details");
    }

    /**
     *  @desc : This function is used to show partner appliance's model list in data table
     *  @param : void
     *  @return : void
     */
    function get_partner_model_details() {
        $data = $this->get_partner_model_details_data();
        $post = $data['post'];
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->reusable_model->count_all_result("appliance_model_details", $post['where']),
            "recordsFiltered" => $this->reusable_model->count_all_filtered_result("appliance_model_details", "count(model_number) as numrows", $post),
            "data" => $data['data'],
        );
        echo json_encode($output);
    }

    function get_partner_model_details_data() {
        $post = $this->get_post_data();
        $post['column_order'] = array();

        $post['order'] = array('appliance_model_details.model_number' => "ASC", "services.services" => "ASC");
        $post['column_search'] = array('appliance_model_details.model_number', 'services.services');
        $post['where'] = array('appliance_model_details.entity_id' => $this->input->post('entity_id'));
        $post['join'] = array(
            "services" => "services.id = appliance_model_details.service_id"
        );
        $select = "appliance_model_details.*, services.services";

        $post['joinType'] = array("services" => "INNER");
        $list = $this->reusable_model->get_datatable_data("appliance_model_details", $select, $post);
        //log_message('info', __METHOD__. " kalyani ".$this->db->last_query());
        $data = array();
        $no = $post['start'];
        foreach ($list as $model_list) {
            $no++;
            $row = $this->get_partner_model_table($model_list, $no);
            $data[] = $row;
        }

        return array(
            'data' => $data,
            'post' => $post
        );
    }

    function get_partner_model_table($model_list, $no) {
        $row = array();
        $json_data = json_encode($model_list);
        $row[] = $no;
        $row[] = $model_list->model_number;
        $row[] = $model_list->services;
        if($model_list->active == 0){
            $row[] = "Inactive";
        }
        else{
            $row[] = "Active";
        }
        $row[] = "<a href='javascript:void(0)' class ='btn btn-primary' id='edit_appliance_model_details' data-id='$json_data' title='Edit Details'><i class = 'fa fa-edit'></i></a>";
        return $row;
    }

    /**
     *  @desc : This function is used to show appliance model list
     *  @param : void
     *  @return : void
     */
    function get_appliance_model_details() {


        $data = $this->get_appliance_model_data();

        $post = $data['post'];
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->inventory_model->count_all_appliance_model_list($post),
            "recordsFiltered" => $this->inventory_model->count_filtered_appliance_model_list($post),
            "data" => $data['data'],
        );

        echo json_encode($output);
    }

    function get_appliance_model_data() {
        $post = $this->get_post_data();

        $post['column_order'] = array();
        $post['column_search'] = array('model_number', 'services.services', 'partner_appliance_details.category','partner_appliance_details.capacity');

        $post['where'] = array('appliance_model_details.entity_id' => trim($this->input->post('partner_id')), 'appliance_model_details.entity_type' => trim($this->input->post('entity_type')));

        if ($this->input->post('service_id') && $this->input->post('service_id') !== 'all') {
            $post['where']['appliance_model_details.service_id'] = $this->input->post('service_id');
        }

        $select = "appliance_model_details.*,appliance_model_details.active as amd_active,services.services,partner_appliance_details.brand, partner_appliance_details.category, partner_appliance_details.capacity,partner_appliance_details.active";
        $list = $this->inventory_model->get_appliance_model_list($post, $select);
        $partners = array_column($this->partner_model->getpartner_details("partners.id,public_name", array('partners.is_active' => 1)), 'public_name', 'id');
        $data = array();
        $no = $post['start'];
        foreach ($list as $model_list) {
            $no++;
            $row = $this->get_appliance_model_table($model_list, $no, $partners);
            $data[] = $row;
        }

        return array(
            'data' => $data,
            'post' => $post
        );
    }

    function get_appliance_model_table($model_list, $no, $partners) {
        $row = array();
        if ($model_list->entity_type === _247AROUND_PARTNER_STRING) {
            $model_list->entity_public_name = $partners[$model_list->entity_id];
        }
        $json_data = json_encode($model_list);
        $row[] = $no;
        $row[] = $model_list->services;
        $row[] = $model_list->model_number;
        $row[] = $model_list->brand;
        $row[] = $model_list->category;
        $row[] = $model_list->capacity;
        if ($this->session->userdata('userType') == 'service_center') {
            
        } else {
            $row[] = "<a href='javascript:void(0)' class ='btn btn-primary' id='edit_appliance_model_details' data-id='$json_data' title='Edit Details'><i class = 'fa fa-edit'></i></a>";
        }

        if ($this->session->userdata('userType') == 'service_center') {
            $row[] = "<a href='" . base_url() . "service_center/inventory/inventory_list_by_model/" . urlencode($model_list->id) . "' class ='btn btn-primary' title='Get Part Details' target='_blank'><i class = 'fa fa-eye'></i></a>";
        } else {
            $row[] = "<a href='" . base_url() . "employee/inventory/get_inventory_by_model/" . urlencode($model_list->id) . "' class ='btn btn-primary' title='Get Part Details' target='_blank'><i class = 'fa fa-eye'></i></a>";
        }


         $row[] = "<button class='btn btn-primary btn-sm' data='" . $json_data . "' onclick='edit_mapped_model(this)'>Edit</button>";

        return $row;
    }

    /**
     *  @desc : This function is used to perform add/edit action on the appliance_model_details table
     *  @param : void()
     *  @return : $response JSON
     */
    function process_appliance_model_list_data() {
        $submit_type = $this->input->post('submit_type');
        if (!empty($submit_type)) {
            $data = array('service_id' => $this->input->post('service_id'),
                'model_number' => trim($this->input->post('model_number')),
                'entity_id' => $this->input->post('entity_id'),
                'entity_type' => $this->input->post('entity_type')
            );
            if ($this->input->post('status') || $this->input->post('status') == 0) {
                $data['active'] = $this->input->post('status');
            }
            if (!empty($data['service_id']) && !empty($data['model_number']) && !empty($data['entity_id']) && !empty($data['entity_type'])) {
                switch (strtolower($submit_type)) {
                    case 'add':
                        $data['create_date'] = date('Y-m-d H:i:s');
                        $response = $this->add_appliance_model_data($data);
                        break;
                    case 'edit':
                        $response = $this->edit_appliance_model_data($data);
                        break;
                }
            } else {
                $response['response'] = 'error';
                $response['msg'] = 'All fields are required.';
            }
        } else {
            $response['response'] = 'error';
            $response['msg'] = 'Please Try Again!!!';
            log_message("info", __METHOD__ . 'Invalid request type');
        }


        echo json_encode($response);
    }

    /**
     *  @desc : This function is used to perform insert action on the appliance_model_details table
     *  @param : $data array()
     *  @return : $res array()
     */
    function add_appliance_model_data($data) {
        $aplliance_model_where = array(
            'service_id' => $data['service_id'],
            'model_number' => $data['model_number'],
            'entity_type' => 'partner',
            'entity_id' => $data['entity_id']
        );
        $model_detail = $this->inventory_model->get_appliance_model_details("id", $aplliance_model_where);
        if (empty($model_detail)) {
            $response = $this->inventory_model->insert_appliance_model_data($data);
            if (!empty($response)) {
                $res['response'] = 'success';
                $res['id'] =$response;
                $res['msg'] = 'Model Number Inserted Successfully';
                log_message("info", __METHOD__ . ' Inventory added successfully');
            } else {
                $res['response'] = 'error';
                $res['msg'] = 'Error in Inserting Model Details';
                log_message("info", __METHOD__ . ' Error in inserting inventory details');
            }
        } else {
            $res['response'] = 'success';
            $res['msg'] = 'Model Number Already Exist';
            log_message("info", __METHOD__ . ' Inventory Already Exist');
        }
        return $res;
    }

    /**
     *  @desc : This function is used to perform edit action on the appliance_model_details table
     *  @param : $data array()
     *  @return : $res array()
     */
    function edit_appliance_model_data($data) {
        $aplliance_model_where = array(
            'service_id' => $data['service_id'],
            'model_number' => $data['model_number'],
            'entity_type' => 'partner',
            'entity_id' => $data['entity_id'],
            'id != "' . $this->input->post('model_id') . '"' => NULL
        );
        $model_detail = $this->inventory_model->get_appliance_model_details("id", $aplliance_model_where);
        if (empty($model_detail)) {
            $response = $this->inventory_model->update_appliance_model_data(array('id' => $this->input->post('model_id')), $data);
            if (!empty($response)) {
                $res['response'] = 'success';
                $res['msg'] = 'Details has been updated successfully';
                log_message("info", __METHOD__ . ' Details has been updated successfully');
            } else {
                $res['response'] = 'error';
                $res['msg'] = 'Error in updating details';
                log_message("info", __METHOD__ . ' error in updating  details');
            }
        } else {
            $res['response'] = 'error';
            $res['msg'] = 'Model Number Already Exist';
        }
        return $res;
    }

    /**
     *  @desc : This function is used to show the current stock of warehouse inventory
     *  @param : void
     *  @return : void
     */
    function get_wh_inventory_stock_list() {
        $this->checkUserSession();
        $gst_where = array(
            "entity_type" => _247AROUND_PARTNER_STRING,
            "entity_id" => _247AROUND,
        );
        $data['from_gst_number'] = $this->inventory_model->get_entity_gst_data("entity_gst_details.id as id, gst_number, state_code.state as state", $gst_where);
        $data['courier_details'] = $this->inventory_model->get_courier_services('*');
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/wh_inventory_stock_list', $data);
    }
      
     /*
     *  @desc : This function is used to display search warehouse stock using part number 
     *  @param : void
     *  @return : void
     */
    function search_inventory_stock_by_part_number_on_wh() {
        $this->checkUserSession();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/wareouse_inventory_stock_list');
    }

    /**
     *  @desc : This function is used to show the inventory details by appliance model
     *  @param : $model_number_id integer
     *  @return : void
     */
    function get_inventory_by_model($model_number_id) {      
        if ($model_number_id) {
            $model_number_id = urldecode($model_number_id);
            $data['inventory_details'] = $this->inventory_model->get_inventory_model_mapping_data('inventory_master_list.*,appliance_model_details.model_number,services.services,inventory_model_mapping.id, inventory_model_mapping.bom_main_part,inventory_model_mapping.create_date', array('inventory_model_mapping.model_number_id' => $model_number_id,'inventory_model_mapping.active' => 1,'appliance_model_details.active'=> 1));
        } else {
            $data['inventory_details'] = array();
        }

        if ($this->session->userdata('employee_id')) {
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/show_inventory_details_by_model', $data);
        } else if ($this->session->userdata('partner_id')) {
            $this->miscelleneous->load_partner_nav_header();
            $this->load->view('employee/show_inventory_details_by_model', $data);
            $this->load->view('partner/partner_footer');
        }
    }

    /**
     *  @desc : This function is used to show the inventory details by appliance model
     *  @param : $inventory_id integer
     *  @return : void
     */
function get_bom_list_by_inventory_id($inventory_id) {

        if ($inventory_id) {
            $inventory_id = urldecode($inventory_id);
            $data['model_details'] = $this->inventory_model->get_inventory_model_mapping_data('inventory_model_mapping.id,inventory_model_mapping.active,inventory_model_mapping.max_quantity,inventory_master_list.part_number,appliance_model_details.model_number,services.services', array('inventory_model_mapping.inventory_id' => $inventory_id));
        } else {
            $data['model_details'] = array();
        }
       
        if ($this->session->userdata('employee_id')) {
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/show_appliance_model_by_inventory_id', $data);
        } else if ($this->session->userdata('partner_id')) {
            $this->miscelleneous->load_partner_nav_header();
            $this->load->view('employee/show_appliance_model_by_inventory_id', $data);
            $this->load->view('partner/partner_footer');
        }
    }

    /**
     *  @desc : This function is used to get model from appliance_model_details table
     *  @param : void
     *  @return : void
     */
    function get_appliance_models() {
        $post['length'] = -1;
        $post['where'] = array('appliance_model_details.active' => 1);
        if ($this->input->post('entity_id') && $this->input->post('entity_type')) {
            $post['where']['appliance_model_details.entity_id'] = $this->input->post('entity_id');
            $post['where']['appliance_model_details.entity_type'] = trim($this->input->post('entity_type'));
        }

        if ($this->input->post('service_id')) {
            $post['where']['appliance_model_details.service_id'] = $this->input->post('service_id');
        }

        $models = $this->inventory_model->get_appliance_model_list($post, 'appliance_model_details.id,appliance_model_details.model_number');

        $data = array();

        foreach ($models as $value) {
            $data[] = array("id" => $value->id, "model_number" => $value->model_number);
        }

        echo json_encode($data);
    }

    /**
     *  @desc : This function is used to show those spare which need to be acknowledge by warehouse
     *  @param : void
     *  @return : void
     */
    function acknowledge_spares_send_by_partner_by_admin() {
        $this->checkUserSession();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/acknowledge_spares_send_by_partner_by_admin');
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

    /**
     * @desc: This function is used to upload the courier receipt for spare parts
     * @params: void
     * @return: returns true if file is uploaded 
     * 
     */
    function upload_defective_courier_receipt() {
        if (!empty($_FILES['defective_courier_receipt']['tmp_name'])) {

            $allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
            $booking_id = $this->input->post("booking_id");




            $defective_courier_receipt = $this->miscelleneous->upload_file_to_s3($_FILES["defective_courier_receipt"], "defective_courier_receipt", $allowedExts, $booking_id, "misc-images", "defective_courier_receipt");
            if ($defective_courier_receipt) {

                return true;
            } else {
                $this->form_validation->set_message('upload_defective_courier_receipt', 'Defective Front Parts, File size or file type is not supported. Allowed extentions are "png", "jpg", "jpeg" and "pdf". '
                        . 'Maximum file size is 5 MB.');
                return false;
            }
        } else {
            return true;
        }
    }

    public function update_tagged_invoice() {

        $data['inventory'] = $this->inventory_model->get_inventory_master_list_data('inventory_id,part_name,part_number');
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/update_tagged_invoice', $data);
    }

    /**
     * @desc: This Function is used to show the spare part corresponding to enter invoice
     * @param: void
     * @return : void
     */
    function show_spare_details_by_spare_invoice() {
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/search_spare_invoice_id');
    }

    /**
     * @desc: This Function is used to show the spare part corresponding to enter invoice
     * @param: void
     * @return : json
     */
    function search_spare_tagged_by_invoice_id() {
        $invoice_id = trim($this->input->post('invoice_id'));
        if (!empty($invoice_id)) {
            $where = " where i.invoice_id = '$invoice_id' ";
            $spare_details = $this->inventory_model->get_tagged_spare_part_details($where);
            if (!empty($spare_details)) {
                $res['status'] = TRUE;
                $res['msg'] = $spare_details;
            } else {
                $res['status'] = FALSE;
                $res['msg'] = 'No data found for invoice ' . $invoice_id;
            }
        } else {
            $res['status'] = FALSE;
            $res['msg'] = 'Invoice Id can not be empty';
        }

        echo json_encode($res);
    }

    /**
     * @desc: This Function is used to search the docket number
     * @param: void
     * @return : void
     */
    function search_docket_number() {
        $this->checkUserSession();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/search_docket_number');
    }

    /**
     * @desc: This Function is used to search the docket number
     * @param: void
     * @return : JSON
     */
    function process_search_docket_number() {
        $docket_number = $this->input->post('docket_number');
        $search_by = $this->input->post('search_by');
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');

        if (!empty($search_by)) {
            if (empty($docket_number) && empty($from_date) && empty($to_date)) {
                $res['status'] = false;
                $res['msg'] = 'Please Enter Either Docket Number or Date Range';
            } else {
                $select = "spare_parts_details.booking_id,spare_parts_details.partner_challan_number,spare_parts_details.sf_challan_number,"
                        . "spare_parts_details.partner_challan_file,spare_parts_details.sf_challan_file,spare_parts_details.awb_by_partner,spare_parts_details.awb_by_sf,"
                        . "spare_parts_details.courier_pic_by_partner,spare_parts_details.parts_shipped,im.part_number,spare_parts_details.shipped_parts_type,im.price,im.gst_rate,"
                        . "spare_parts_details.awb_by_wh, spare_parts_details.wh_challan_number, spare_parts_details.wh_challan_file, spare_parts_details.status, spare_parts_details.id";
                $where = array();

                if ($this->input->post('sf_id')) {
                    $where['service_center_id'] = $this->input->post('sf_id');
                }

                if ($this->input->post('partner_id')) {
                    $where['partner_id'] = $this->input->post('partner_id');
                }

                //if warehouse is selected then get data from courier details table else get data from spare part table
                if ($search_by === 'wh') {
                    $select = "courier_company_invoice_details.awb_number, courier_company_invoice_details.company_name, courier_company_invoice_details.courier_charge, courier_company_invoice_details.actual_weight,courier_company_invoice_details.box_count, inventory_ledger.invoice_id";
                    if (!empty($docket_number)) {
                        $docket_number_arr = explode(',', $docket_number);
                        $docket_number_arr_str = implode(',', array_map(function($val) {
                                    return "'" . trim($val) . "'";
                                }, $docket_number_arr));

                        $where["courier_company_invoice_details.awb_number IN ($docket_number_arr_str)"] = NULL;
                    }

                    if (!empty($from_date) && !empty($to_date)) {
                        $where["courier_company_invoice_details.shippment_date >= '" . date('Y-m-d', strtotime($from_date)) . "'  AND courier_company_invoice_details.shippment_date <='" . date('Y-m-d', strtotime($to_date . "+1 days")) . "' "] = NULL;
                    }

                    $docket_details = $this->inventory_model->get_spare_courier_details($select, $where);
                } else {
                    $select .= ",service_centres.name as 'sf_name', sc.name as wh_name, spare_consumption_status.consumed_status, spare_consumption_status.is_consumed";
                    if (!empty($docket_number)) {
                        $docket_number_arr = explode(',', $docket_number);
                        $docket_number_arr_str = implode(',', array_map(function($val) {
                                    return "'" . trim($val) . "'";
                                }, $docket_number_arr));

                        $where["$search_by IN ($docket_number_arr_str)"] = NULL;
                    }

                    if (!empty($from_date) && !empty($to_date)) {
                        if ($search_by == 'awb_by_partner') {
                            $where["spare_parts_details.shipped_date >= '" . date('Y-m-d', strtotime($from_date)) . "'  AND spare_parts_details.shipped_date <= '" . date('Y-m-d', strtotime($to_date . "+1 days")) . "' "] = NULL;
                        } else if ($search_by == 'awb_by_sf') {
                            $where["spare_parts_details.defective_part_shipped_date >= '" . date('Y-m-d', strtotime($from_date)) . "'  AND spare_parts_details.defective_part_shipped_date <='" . date('Y-m-d', strtotime($to_date)) . "' "] = NULL;
                        }else if ($search_by == 'awb_by_wh') {
                            $where["spare_parts_details.wh_to_partner_defective_shipped_date >= '" . date('Y-m-d', strtotime($from_date)) . "'  AND spare_parts_details.wh_to_partner_defective_shipped_date <= '" . date('Y-m-d', strtotime($to_date)) . "' "] = NULL;
                        }
                        
                    }
                    $post['is_inventory'] = TRUE;
                    $docket_details = $this->partner_model->get_spare_parts_by_any($select, $where, FALSE, TRUE, FALSE, $post,TRUE);
                }


                if (!empty($docket_details)) {
                    if ($this->input->post('sf_id')) {
                        foreach ($docket_details as $key => $value) {
                            $docket_details[$key]['booking_id_url_value'] = urlencode(base64_encode($value['booking_id']));
                        }
                    }

                    $res['status'] = true;
                    $res['msg'] = $docket_details;
                } else {
                    $res['status'] = false;
                    $res['msg'] = 'No Data Found';
                }
            }
        } else {
            $res['status'] = false;
            $res['msg'] = 'Please Select Partner Or Service Center Or Warehouse';
        }

        echo json_encode($res);
    }
    
    /*
     * @desc: This Function is used to download the consolidated report
     * @param: void
     * @return : Download link
     */

    function download_spare_consolidated_data() {
        log_message('info', __METHOD__ . ' Processing...');
        ini_set('memory_limit', -1);
        $partner_id = $this->input->post('partner_id');
        $service_center_id = $this->input->post('service_center_id');
        $spare_part_status = $this->input->post('spare_part_status');
        $select = "spare_parts_details.id as spare_id, services.services as 'Appliance',  booking_details.booking_id as 'Booking ID',  booking_details.assigned_vendor_id as 'Assigned Vendor Id', "
                . "emply.full_name as 'RM Name',empl.full_name as 'ASM Name',service_centres.name as 'SF Name', service_centres.district as 'SF City', service_centres.state as 'SF State', "
                . "(CASE WHEN service_centres.active = 1 THEN 'Active' ELSE 'Inactive' END) as 'SF Status', partners.public_name as 'Partner Name', "
                . "GROUP_CONCAT(employee.full_name) as 'Account Manager Name', booking_details.current_status as 'Booking Status', booking_details.partner_current_status as 'Partner Status Level 1', "
                . "booking_details.partner_internal_status as 'Partner Status Level 2',"
                . "spare_parts_details.status as 'Spare Status', (CASE WHEN spare_parts_details.part_warranty_status = 1 THEN 'In-Warranty' WHEN spare_parts_details.part_warranty_status = 2 THEN 'Out-Warranty' END) as 'Spare Warranty Status', (CASE WHEN spare_parts_details.nrn_approv_by_partner = 1 THEN 'Approved' ELSE 'Not Approved' END) as 'NRN Status', DATE_FORMAT(service_center_closed_date,'%d-%b-%Y') as 'Service Center Closed Date', DATE_FORMAT(booking_details.closed_date,'%d-%b-%Y') as 'Final Closing Date', DATE_FORMAT(spare_parts_details.spare_cancelled_date,'%d-%b-%Y')   as 'Spare Part Cancellation Date', bcr.reason as 'Spare Cancellation Reason', booking_details.request_type as 'Booking Request Type', spare_parts_details.model_number as 'Requested Model Number',spare_parts_details.parts_requested as 'Requested Part',"
                . "spare_parts_details.parts_requested_type as 'Requested Part Type', i.part_number as 'Requested Part Number', i.price as 'Requested Part Basic Price', "
                . "DATE_FORMAT(spare_parts_details.date_of_request,'%d-%b-%Y') as 'Spare Part Requested Date',"
                . "if(spare_parts_details.is_micro_wh='0','Partner',if(spare_parts_details.is_micro_wh='1',concat('Microwarehouse - ',sc.name),sc.name)) as 'Requested On Partner/Warehouse',"
                . "spare_parts_details.model_number_shipped as 'Shipped Model Number',spare_parts_details.parts_shipped as 'Shipped Part',"
                . "spare_parts_details.shipped_parts_type as 'Shipped Part Type',"
                . "iml.part_number as 'Shipped Part Number', iml.price as 'Shipped Part Basic Price', "
                . "DATE_FORMAT(spare_parts_details.shipped_date,'%d-%b-%Y') as 'Spare Part Shipped Date', datediff(CURRENT_DATE,spare_parts_details.shipped_date) as 'Spare Shipped Age', spare_parts_details.awb_by_partner as 'Partner AWB Number',"
                . "spare_parts_details.courier_name_by_partner as 'Partner Courier Name',spare_parts_details.courier_price_by_partner as 'Partner Courier Price', acknowledge_date as 'SF Received New Part Date', "
                . "partner_challan_number AS 'Partner Challan Number',spare_parts_details.awb_by_sf as 'SF AWB Number',spare_parts_details.courier_name_by_sf as 'SF Courier Name', spare_parts_details.courier_charges_by_sf as 'SF Courier Price', "
                . "sf_challan_number as 'SF Challan Number',IF(wh.name !='' , wh.name, 'Partner') as 'SF Dispatch Defective Part To Warehouse/Partner',"
                . "spare_parts_details.defective_part_received_date_by_wh as 'Defective Received Date By Warehouse',(CASE WHEN spare_parts_details.auto_acknowledeged = 1 THEN 'Auto' WHEN spare_parts_details.auto_acknowledeged = 2 THEN 'Courier API' WHEN spare_parts_details.auto_acknowledeged = 0 and acknowledge_date is not null THEN 'Self'  ELSE '' END)  as 'Is Spare Auto Acknowledge',"
                . "spare_parts_details.defective_part_shipped as 'Part Shipped By SF',challan_approx_value As 'Challan Charge (With Margin & TAX)', "
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
                . "if(spare_parts_details.partner_warehouse_packaging_invoice_id is null,'',spare_parts_details.partner_warehouse_packaging_invoice_id) as 'Partner Warehouse Packaging Courier Invoice', (CASE WHEN spare_parts_details.spare_lost = 1 THEN 'Yes' ELSE 'NO' END) AS 'Spare Lost', spare_parts_details.quantity as 'Requested Spare Quantity', spare_parts_details.shipped_quantity as 'Shipped Spare Quantity',dealer_details.dealer_name as 'Dealer Name',"
                . "(CASE WHEN courier_company_invoice_details.is_rto = 1 THEN 'Yes' ELSE 'No' END) as 'RTO'";
        //$where = array("spare_parts_details.status NOT IN('" . SPARE_PARTS_REQUESTED . "')" => NULL);
        $where = array();
        $group_by = "spare_parts_details.id";
        if (!empty($partner_id) && is_numeric($partner_id)) {
            $where['booking_details.partner_id'] = $partner_id;
        }

        if (!empty($service_center_id) && is_numeric($service_center_id)) {
            $where['booking_details.assigned_vendor_id'] = $service_center_id;
        }
               
        if (!empty($spare_part_status)) {
             $i = 0;
             $single_status = '';
             $multiple_status = '';
            foreach ($spare_part_status as $key => $value) {
                if($key < 1) {
                    $single_status .= "'" . $value . "'";
                } else {
                    $multiple_status .= ",'".$value."'";
                }
                $i++;
            }
            
            if ($i > 1) {
                $status = $single_status . $multiple_status;
            } else {
                $status = $single_status;
            }
            $where['spare_parts_details.status IN(' . $status . ')'] = NULL;
        }


        $spare_details = $this->inventory_model->get_spare_consolidated_data($select, $where, $group_by);

        

        $this->load->dbutil();
        $this->load->helper('file');

        $file_name = 'spare_consolidated_data_' . date('j-M-Y-H-i-s') . ".csv";
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

    function get_spare_line_item_for_tag_spare($booking_id, $count) {
        log_message('info', __METHOD__ . " Booking ID " . $booking_id);
        if (!empty($booking_id)) {
            $is_micro = $this->input->post('is_micro');
            $vendor_id = $this->input->post('vendor_id');

            $sc_close_date = $this->reusable_model->get_search_query('booking_details','booking_details.service_center_closed_date, booking_details.request_type, assigned_vendor_id',array('booking_id'=>$booking_id),NULL,NULL,NULL,NULL,NULL)->result_array();
            $data['add_more'] = false;
            if (isset($sc_close_date[0]['request_type']) && $sc_close_date[0]['request_type'] == REPAIR_OOW_TAG) {
                $s_change = $this->booking_model->getbooking_state_change_by_any(array('booking_id' => $booking_id, "new_state" => SPARE_OOW_EST_GIVEN));
                if (!empty($s_change)) {
                    $data['add_more'] = true;
                }
            }

            
            if (!empty($sc_close_date[0]['service_center_closed_date'])) {
<<<<<<< HEAD
               echo json_encode(array('code' => -247, "data" => "Booking already closed. Part shipping not allowed"));
            }else{
=======
                echo json_encode(array('code' => -247, "data" => "Booking already closed. Part shipping not allowed"));
                
            } else if($is_micro == 2 && $sc_close_date[0]['assigned_vendor_id'] != $vendor_id){
                
                echo json_encode(array('code' => -247, "data" => "Booking assigned another Warehouse. Part shipping not allowed"));
                
            } else {
>>>>>>> da39423f4... Allow msl on booking for partner to send part to microwarehouse #CRMS-2191

                $where = array('status' => SPARE_PARTS_REQUESTED,
                'spare_parts_details.entity_type' => _247AROUND_PARTNER_STRING,
                'spare_parts_details.booking_id' => $booking_id);
            if ($this->session->userdata('partner_id')) {
                $where['spare_parts_details.partner_id'] = $this->session->userdata('partner_id');
            }
            $data['data'] = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id,spare_parts_details.quantity, spare_parts_details.requested_inventory_id, booking_details.partner_id,"
                    . "spare_parts_details.booking_id, booking_details.service_id,spare_parts_details.model_number,booking_details.request_type,spare_parts_details.part_warranty_status", $where, true);
            
            if (!empty($data['data'])) {
                    $data['count'] = $count;
                    $data['inventory_master_list'] = $this->inventory_model->get_inventory_model_mapping_data('inventory_master_list.inventory_id,inventory_master_list.part_name,inventory_master_list.part_number, '
                            . 'inventory_master_list.gst_rate, inventory_master_list.hsn_code, inventory_master_list.price, inventory_master_list.type', array('inventory_model_mapping.active' => 1, 'appliance_model_details.model_number' => $data['data'][0]['model_number'], 'inventory_master_list.service_id' => $data['data'][0]['service_id']));
                    $split_data = preg_split("/\-/", $data['data'][0]['booking_id']);
                    $data['numeric_id'] = $split_data[1];
                    $html = $this->load->view('employee/tag_spare_line_item', $data, true);

                    echo json_encode(array('code' => 247, "data" => $html, "count" => count($data)));
                } else {
                    echo json_encode(array('code' => -247, "data" => "There is no any spare requested for this booking."));
                }
            }

        } else {
            echo json_encode(array('code' => -247, "data" => "Please attach Valid Booking ID"));
        }
    }

    function get_part_number_data() {
        log_message('info', __METHOD__ . json_encode($_POST, true));
        $part_name = trim($this->input->post('part_name'));

        $post['length'] = -1;
        $post['where'] = array('entity_id' => trim($this->input->post('entity_id')), 'entity_type' => trim($this->input->post('entity_type')), 'service_id' => trim($this->input->post('service_id')), 'part_name' => $part_name);
        $post['order'] = array(array('column' => 0, 'dir' => 'ASC'));
        $post['column_order'] = array('part_number');
        $inventory_details = $this->inventory_model->get_inventory_master_list($post, 'inventory_master_list.part_number, type, inventory_id,gst_rate, hsn_code, price', true);

        if ($this->input->post('is_option_selected')) {
            $option = '<option selected disabled>Select Part Number</option>';
        } else {
            $option = '';
        }

        $gst_rate = "";
        $hsn_code = "";
        $inventory_id = "";
        $total_price = "";
        $price = "";
        $type = "";

        foreach ($inventory_details as $value) {
            $option .= "<option value='" . $value['part_number'] . "'";
            if (count($inventory_details) == 1) {
                $gst_rate = $value['gst_rate'];
                $hsn_code = $value['hsn_code'];
                $price = $value['price'];
                $inventory_id = $value['inventory_id'];
                $type = $value['type'];
                $total_price = sprintf("%.2f", $price * (1 + $value['gst_rate'] / 100));
                $option .= " selected ";
            }
            $total_amount = sprintf("%.2f", $value['price'] * (1 + $value['gst_rate'] / 100));
            $option .="  data-inventory_id = '" . $value['inventory_id']
                    . "' data-gst_rate = '" . $value['gst_rate']
                    . "' data-hsn_code = '" . $value['hsn_code'] . "' data-basic_price = '" . $value['price']
                    . "' data-total_amount = '" . $total_amount . "' data-type ='" . $value['type'] . "' > ";
            $option .= $value['part_number'] . "</option>";
        }

        $array = array(
            "option" => $option,
            "gst_rate" => $gst_rate,
            "hsn_code" => $hsn_code,
            "basic_price" => $price,
            "total_price" => $total_price,
            "type" => $type,
            "inventory_id" => $inventory_id
        );
        echo json_encode($array, TRUE);
    }

    /**
     * @desc: This Function is used to view upload docket number
     * @param: void
     * @return : void
     */
    function upload_docket_number() {
        $this->checkUserSession();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/upload_docket_number');
    }

    /**
     * @desc: This Function is used to show view for rechecking docket number
     * @param: void
     * @return : view
     */
    function recheck_docket_number() {
        $this->checkUserSession();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/recheck_docket_number');
    }

    /**
     * @desc: This Function is used to process recheck docket number
     * @param: void
     * @return : boolean
     */
    function process_recheck_docket_number() {
        if (!empty($this->input->post('id'))) {
            $data = array(
                'awb_number' => $this->input->post('awb_no'),
                'tid' => $this->input->post('id'),
                'courier_charges' => $this->input->post('courier_charge'),
            );
            $return = $this->inventory_model->update_docket_price($data);
            if ($return['update_awb']) {
                echo true;
            } else {
                echo false;
            }
        }
    }

    /**
     * @desc: This Function is used to reject courier invoice with reject remark
     * @param: void
     * @return : boolean
     */
    function reject_courier_invoice() {
        if (!empty($this->input->post('id'))) {
            $data = array(
                'is_reject' => 1,
                'reject_remarks' => $this->input->post('reject_remark'),
            );
            $where = array(
                'id' => $this->input->post('id')
            );
            $return = $this->inventory_model->update_courier_company_invoice_details($where, $data);
            if ($return) {
                echo true;
            } else {
                echo false;
            }
        }
    }
    
    /**
     * @desc: This Function is used to get the lists of recheck docket number
     * @param: void
     * @return : boolean
     */
    function get_recheck_docket_number(){
        $post_data = array('length' => $this->input->post('length'),
            'start' => $this->input->post('start'),
            'file_type' => trim($this->input->post('file_type')),
            'order' => $this->input->post('order'),
            'draw' => $this->input->post('draw'),
            'search_value' => trim($this->input->post('search')['value'])
        );
        $post_data['where'] = array(
            'is_exist' => 0,
            'is_reject' => 0,
        );
        $post_data['column_search'] = array('awb_number', 'company_name', 'courier_charge', 'courier_invoice_id','billed_docket.invoice_id');
        $select = "billed_docket.invoice_id, courier_company_invoice_details.awb_number, courier_company_invoice_details.courier_invoice_id, courier_company_invoice_details.company_name, courier_company_invoice_details.courier_charge, courier_company_invoice_details.billable_weight, courier_company_invoice_details.basic_billed_charge_to_partner, courier_company_invoice_details.small_box_count, courier_company_invoice_details.shippment_date, courier_company_invoice_details.delivered_date, courier_company_invoice_details.box_count, courier_company_invoice_details.pickup_from, courier_company_invoice_details.actual_weight, courier_company_invoice_details.create_date,  courier_company_invoice_details.id";
        $list = $this->inventory_model->get_searched_courier_invoices($select, $post_data);
        $no = $post_data['start'];
        $data = array();
        $rowSums = array(
            "colCount" => 0,
            "colData" => array(
                0 => 'Total',
                8 => 0.00, //total occurs in col 11 in datatable
            )
        );

        foreach ($list as $invoice_list) {
            $row = array();
            $no++;
            $row[] = $no;
            $row[] = $invoice_list->invoice_id;
            $row[] = $invoice_list->courier_invoice_id;
            $row[] = $invoice_list->awb_number;
            $row[] = $invoice_list->company_name;
            $row[] = $invoice_list->billable_weight;
            $row[] = $invoice_list->actual_weight;
            $date = date('d-M-Y', strtotime($invoice_list->create_date));
            $row[] = $date;
            $row[] = $invoice_list->courier_charge;
            $a = "<a href='javascript:void(0);' class='btn btn-success btn-xs' onclick='";
            $a .= "recheck_docket_nember(" . '"' . $invoice_list->id . '"';
            $a .= ', "' . $invoice_list->awb_number . '"';
            $a .= ', "' . $invoice_list->courier_charge . '"';
            $a .= ")'>Recheck  </a>";
            $a .= '&nbsp;&nbsp;&nbsp; <button type="button" class="btn btn-warning btn-xs" onclick="open_reject_remark_model(' . $invoice_list->id . ')" data-toggle="modal" data-target="#rejectInvoiceModal">Ignore</button>';
            $row[] = $a;
            $data[] = $row;
            
            $tSum = $this->get_courier_charges_total($invoice_list);
            $rowSums['colData'][8] += $tSum['total'];
            $rowSums["colCount"] = (count($row) > $rowSums['colCount']) ? count($row) : $rowSums["colCount"];
        }
                   
        if (count($data) > 0) {
            $data[] = $this->draw_table_courier_docket_footer($rowSums);
        }

        $output = array(
            "draw" => $post_data['draw'],
            "recordsTotal" => $this->inventory_model->count_courier_invoices($post_data),
            "recordsFiltered" => $this->inventory_model->count_filtered_courier_invoices('courier_company_invoice_details.id', $post_data),
            "data" => $data,
        );

        echo json_encode($output);
    }
      
     /**
     * @desc: This Function is used to get the lists of recheck docket number
     * @param: void
     * @return : boolean
     */
    function get_ignored_invoice_list(){
        $post_data = array('length' => $this->input->post('length'),
            'start' => $this->input->post('start'),
            'file_type' => trim($this->input->post('file_type')),
            'order' => $this->input->post('order'),
            'draw' => $this->input->post('draw'),
            'search_value' => trim($this->input->post('search')['value'])
        );
        
        $post_data['where'] = array(
            'is_reject' => 1,
        );
        
        $post_data['column_search'] = array('awb_number', 'company_name', 'courier_charge', 'courier_invoice_id','billed_docket.invoice_id');
            
        $select = "billed_docket.invoice_id, courier_company_invoice_details.awb_number, courier_company_invoice_details.courier_invoice_id, courier_company_invoice_details.company_name, courier_company_invoice_details.courier_charge, courier_company_invoice_details.billable_weight, courier_company_invoice_details.basic_billed_charge_to_partner, courier_company_invoice_details.small_box_count, courier_company_invoice_details.shippment_date, courier_company_invoice_details.delivered_date, courier_company_invoice_details.box_count, courier_company_invoice_details.pickup_from, courier_company_invoice_details.actual_weight, courier_company_invoice_details.create_date, courier_company_invoice_details.reject_remarks";
        $list = $this->inventory_model->get_searched_courier_invoices($select, $post_data);

        $no = $post_data['start'];
        $data = array();
        $rowSums = array(
            "colCount" => 0,
            "colData" => array(
                0 => 'Total',
                6 => 0.00, //total occurs in col 11 in datatable
            )
        );

        foreach ($list as $invoice_list) {
            $row = array();
            $no++;
            $row[] = $no;
            $row[] = $invoice_list->invoice_id;
            $row[] = $invoice_list->courier_invoice_id;
            $row[] = $invoice_list->awb_number;
            $row[] = $invoice_list->company_name;
            $date = date('d-M-Y', strtotime($invoice_list->create_date));
            $row[] = $date;
            $row[] = $invoice_list->courier_charge;
            $row[] = $invoice_list->reject_remarks;
            $data[] = $row;
            
            $tSum = $this->get_courier_charges_total($invoice_list);
            $rowSums['colData'][6] += $tSum['total'];
            $rowSums["colCount"] = (count($row) > $rowSums['colCount']) ? count($row) : $rowSums["colCount"];
        }
                   
        if (count($data) > 0) {
            $data[] = $this->draw_table_courier_docket_footer($rowSums);
        }

        $output = array(
            "draw" => $post_data['draw'],
            "recordsTotal" => $this->inventory_model->count_courier_invoices($post_data),
            "recordsFiltered" => $this->inventory_model->count_filtered_courier_invoices('courier_company_invoice_details.id', $post_data),
            "data" => $data,
        );

        echo json_encode($output);
    }
    
    
     /** 
     * @desc: This function is used to create table body to table.
     * @param: Array
     * @return: Array
     */
    
    private function get_courier_charges_total($data_list) {
        $res = array();
        $res['total'] =  $data_list->courier_charge;
        return $res;
    }

    /** 
     * @desc: This function is used to draw the footer of table.
     * @param: rowData
     * @return: Array
     */
    private function draw_table_courier_docket_footer($rowData) {
        $res = array();
        for ($i = 0; $i < $rowData['colCount']; $i++) {
            $res[$i] = '';
            if (isset($rowData['colData'])) {
                if (isset($rowData['colData'][$i])) {
                    $res[$i] = (is_float($rowData['colData'][$i])) ? number_format($rowData['colData'][$i], 2) : $rowData['colData'][$i];
                }
            }
        }
        return $res;
    }
    
    /**
     * @desc: This Function is used to search docket number in bulk
     * @param: void
     * @return : view
     */
    function search_courier_invoices() {
        $this->checkUserSession();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/search_courier_invoices');
    }

    function process_search_courier_invoice() {
        $docket_no = $this->input->post("docket_no");
        $html = "";
        $notFoundData = array();
        if (!empty($docket_no)) {
            $docket_no = explode(",", $docket_no);
            $docket_no_array = array_filter($docket_no);
            $where_in = array('awb_number' => $docket_no_array);
            $data = $this->inventory_model->get_courier_company_invoice_details('*', array(), $where_in);
            if (!empty($data)) {
                $i = 1;
                foreach ($data as $key => $value) {
                    $foundedData[] = $value['awb_number'];
                    $html .= "<tr>";
                    $html .= "<td>" . $i++ . "</td><td>" . $value['awb_number'] . "</td><td>" . $value['company_name'] . "</td><td>" . $value['courier_charge'] . "</td><td>" . $value['courier_invoice_id'] . "</td><td>" . $value['billable_weight'] . "</td><td>" . $value['actual_weight'] . "</td><td>" . $value['update_date'] . "</td><td>" . $value['create_date'] . "</td>";
                    $html .= "</tr>";
                }
                $returndata['status'] = "success";
                $returndata['html'] = $html;
                $returndata['notFound'] = implode(", ", array_diff($docket_no_array, $foundedData));
                echo json_encode($returndata);
            } else {
                $returndata['status'] = "error";
                echo json_encode($returndata);
            }
        } else {
            $returndata['status'] = "error";
            echo json_encode($returndata);
        }
    }
    
    /**
     * @desc: This Function is print warehouse address from tag page using MSL.
     * @param: void
     * @return : view
     */
    function print_warehouse_address() {
        $partner_id = $this->uri->segment(4);
        $warehouse_id = $this->uri->segment(5);
        $total_quantity = $this->uri->segment(6);
        $meta = array();
        $partner_on_saas = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $main_partner = $this->partner_model->get_main_partner_invoice_detail($partner_on_saas);
        if(!empty($main_partner)){
            $meta['main_company_public_name'] = $main_partner['main_company_public_name'];
            $meta['main_company_logo'] = $main_partner['main_company_logo'];
        }
        
        if (!empty($warehouse_id)) {
            $select = "contact_person.name as  primary_contact_name,contact_person.official_contact_number as primary_contact_phone_1,contact_person.alternate_contact_number as primary_contact_phone_2,"
                    . "concat(warehouse_address_line1,',',warehouse_address_line2) as address,warehouse_details.warehouse_city as district,"
                    . "warehouse_details.warehouse_pincode as pincode,"
                    . "warehouse_details.warehouse_state as state";
            $where = array('warehouse_details.entity_type' => _247AROUND_SF_STRING,
                'warehouse_details.entity_id' => $warehouse_id);
            $wh_address_details = $this->inventory_model->get_warehouse_details($select, $where, false, true);
            $select1 = 'name as company_name,primary_contact_name,address,pincode,state,district,primary_contact_phone_1,primary_contact_phone_2';
            $sf_address_details = $this->vendor_model->getVendorDetails($select1, array('id' => $warehouse_id));
            if (empty($wh_address_details)) {
                $wh_address_details = $sf_address_details;
            } else {
                $wh_address_details[0]['company_name'] = $sf_address_details[0]['company_name'];
            }
            $wh_address_details[0]['total_quantity'] = $total_quantity;
            if (!empty($partner_id)) {
                $booking_details = $this->partner_model->getpartner($partner_id);
            }
            $wh_address_details[0]['vendor'] = $booking_details[0];
        }
        $this->load->view('service_centers/print_warehouse_address', array('details' => $wh_address_details, 'total_quantiry' => $total_quantity, 'meta'=>$meta));
    }

    /**
     * @desc: This Function is print warehouse address from tag page using MSL.
     * @param: void
     * @return : view
     */
    function get_inventory_stock_count() {
        $service_centres_id = $this->input->post('service_centres_id');
        $inventory_id = $this->input->post('inventory_id');
        $entity_type = $this->input->post('entity_type');
        if (!empty($inventory_id)) {
            $select = '*';
            $where = array('entity_id' => $service_centres_id, 'entity_type' => $entity_type, 'inventory_id' => $inventory_id);
            $inventory_stocks = $this->inventory_model->get_inventory_stock_count_details($select, $where);
            if (!empty($inventory_stocks)) {
                $data['total_stock'] = ($inventory_stocks[0]['stock'] - $inventory_stocks[0]['pending_request_count']);
            } else {
                $data['total_stock'] = 0;
            }
            echo json_encode($data);
        }
    }

    /**
     * @desc: This function is used to get all courier invoices 
     * @param: void
     * @return : data table
     */
    function get_courier_invoices() {
        $post_data = array('length' => $this->input->post('length'),
            'start' => $this->input->post('start'),
            'file_type' => trim($this->input->post('file_type')),
            'order' => $this->input->post('order'),
            'draw' => $this->input->post('draw'),
            'search_value' => trim($this->input->post('search')['value'])
        );
        $post_data['where'] = array(
            'is_exist' => 1,
            'is_reject' => 0,
        );
        $post_data['column_search'] = array('awb_number', 'company_name', 'courier_charge', 'courier_invoice_id', 'vendor_invoice_id', 'partner_invoice_id');
        $select = "billed_docket.invoice_id, courier_company_invoice_details.awb_number, courier_company_invoice_details.courier_invoice_id, courier_company_invoice_details.company_name, courier_company_invoice_details.courier_charge, courier_company_invoice_details.billable_weight, courier_company_invoice_details.basic_billed_charge_to_partner, courier_company_invoice_details.small_box_count, courier_company_invoice_details.shippment_date, courier_company_invoice_details.delivered_date, courier_company_invoice_details.box_count, courier_company_invoice_details.pickup_from, courier_company_invoice_details.actual_weight, courier_company_invoice_details.create_date, courier_company_invoice_details.id";
        $list = $this->inventory_model->get_searched_courier_invoices($select, $post_data);

        $no = $post_data['start'];
        $data = array();
        foreach ($list as $invoice_list) {
            $row = array();
            $no++;
            $row[] = $no;
            $row[] = $invoice_list->invoice_id;
            $row[] = $invoice_list->courier_invoice_id;
            $row[] = $invoice_list->awb_number;
            $row[] = $invoice_list->company_name;
            $row[] = $invoice_list->courier_charge;
            $row[] = $invoice_list->actual_weight;
            $row[] = $invoice_list->billable_weight;
            //$row[] = $invoice_list->vendor_invoice_id;
            //$row[] = $invoice_list->partner_invoice_id;
            $row[] = $invoice_list->pickup_from;
            $row[] = $invoice_list->create_date;
            $data[] = $row;
        }

        $output = array(
            "draw" => $post_data['draw'],
            "recordsTotal" => $this->inventory_model->count_courier_invoices($post_data),
            "recordsFiltered" => $this->inventory_model->count_filtered_courier_invoices('courier_company_invoice_details.id', $post_data),
            "data" => $data,
        );

        echo json_encode($output);
    }

    /**
     *  @desc : This function is used to get inventory part type 
     *  @param : void
     *  @return : $res array()
     */
    function get_inventory_parts_type() {
        
        /* Check if any record exists in inventory_master_list table if exists then return message.*/
        if(!empty($this->input->post('check_non_inventory'))) {
            $inventory_master_list = $this->inventory_model->get_inventory_master_list_data('inventory_id', array('entity_id' => $this->input->post('partner_id'),'entity_type' => _247AROUND_PARTNER_STRING, 'service_id' => $this->input->post('service_id')));
            if(!empty($inventory_master_list)) {
                $option = UPDATE_INVENTORY_MASTER_LIST_MSG;
            } else {
                $option = '<option selected disabled>Select Part Type</option>';
            }
        } else {
            $inventory_parts_type = $this->inventory_model->get_inventory_parts_type_details('inventory_parts_type.id,inventory_parts_type.service_id,inventory_parts_type.part_type,inventory_parts_type.hsn_code_details_id', array('inventory_parts_type.service_id' => $this->input->post('service_id')), TRUE);

            $option = '<option selected disabled>Select Part Type</option>';

            if (!empty($this->input->post('request_type'))) {
                foreach ($inventory_parts_type as $value) {
                    $option .= "<option value='" . $value['id'] . "'>";
                    $option .= $value['part_type'] . "</option>";
                }
            } else {
                foreach ($inventory_parts_type as $value) {
                    $option .= "<option data-hsn-code-details='" . $value['hsn_code_details_id'] . "' value='" . $value['part_type'] . "'";
                    $option .= " > ";
                    $option .= $value['part_type'] . "</option>";
                }
            }
        }
        echo $option;
    }

    /**
     *  @desc : This function is used to get HSN Code and GST Rate
     *  @param : void
     *  @return : json
     */
    function get_hsn_code_gst_details() {
        if (!empty($this->input->post('hsn_code_id'))) {
            $where = array('id' => $this->input->post('hsn_code_id'));
        } else {
            $where = array();
        }

        $hsncode_details = $this->invoices_model->get_hsncode_details('id,hsn_code,gst_rate', $where);

        if (!empty($hsncode_details)) {
            if (!empty($where) && sizeof($hsncode_details) == 1) {
                echo json_encode($hsncode_details[0]);
            } else {
                if ($this->input->post('is_option_selected')) {
                    $option = '<option  selected="" disabled="">Select HSN Code</option>';
                } else {
                    $option = '';
                }

                foreach ($hsncode_details as $value) {
                    $option .= "<option value='" . $value['id'] . "'";
                    $option .= " > ";
                    $option .= $value['hsn_code'] . "</option>";
                }
                echo $option;
            }
        } else {
            echo json_encode(array('result' => 'Data not found'));
        }
    }

    /**
     *  @desc : This function is used to add Inventory Part Type
     *  @param : void
     *  @return : json
     */
    function get_add_inventory_part_type() {

        $select = "inventory_parts_type.id, inventory_parts_type.part_type,inventory_parts_type.service_id,inventory_parts_type.hsn_code_details_id,services.services as service_name,hsn_code_details.hsn_code as hsn_code ";
        $inventory_parts_type = $this->inventory_model->get_inventory_parts_type_details($select, array(), TRUE);

        $data = array();
        if (!empty($inventory_parts_type)) {
            $data['parts_type'] = $inventory_parts_type;
        }

        $this->miscelleneous->load_nav_header();
        $this->load->view("employee/add_inventory_part_type", $data);
    }

    /**
     * @desc: This function is used to process add Inventory Part Type form
     * @params: Array
     * @return: void
     */
    function process_add_inventory_part_type_form() {
        //Form Validation
        $this->form_validation->set_rules('service_id', 'Select Appliance', 'required');
        $this->form_validation->set_rules('part_type', 'Enter Part Type', 'required');
        $this->form_validation->set_rules('hsn_code', 'Select HSN Code', 'required');
        if ($this->form_validation->run()) {
            $data['service_id'] = $this->input->post('service_id');
            $data['part_type'] = strtoupper($this->input->post('part_type'));
            $data['hsn_code_details_id'] = $this->input->post('hsn_code');
            if (!empty($this->input->post('service_id') && !empty($this->input->post('part_type')))) {
                $parts_type_details = $this->inventory_model->get_inventory_parts_type_details('*', array('inventory_parts_type.service_id' => $data['service_id'], 'part_type' => strtoupper($data['part_type'])), false);
                if (empty($parts_type_details)) {
                    $last_inserted_id = $this->inventory_model->insert_inventory_parts_type($data);
                    if ($last_inserted_id) {
                        $this->session->set_userdata('part_type_success', 'Inventory part type add successfully');
                        redirect(base_url() . 'employee/inventory/get_add_inventory_part_type');
                    }
                } else {
                    $this->session->set_userdata('part_type_error', 'Inventory part type already exist');
                    redirect(base_url() . 'employee/inventory/get_add_inventory_part_type');
                }
            }
        } else {
            //Setting success session data 
            $this->session->set_userdata('part_type_error', 'Please Fill Form  Details');
            redirect(base_url() . 'employee/inventory/get_add_inventory_part_type');
        }
    }

    /**
     * @desc: This function is used to process add Inventory Part Type form
     * @params: Array
     * @return: void
     */
    function process_edit_inventory_part_type_form() {
        //Form Validation
        $part_type_id = $this->input->post('part_type_id');
        if (!empty($part_type_id)) {
            $data['id'] = $part_type_id;
            $data['service_id'] = $this->input->post('service_id');
            $data['part_type'] = $this->input->post('part_type');
            $data['hsn_code_details_id'] = $this->input->post('hsn_code');
            if (!empty($data)) {
                $affected_id = $this->inventory_model->update_inventory_parts_type($data, array('id' => $part_type_id));

                if ($affected_id) {
                    $select = "inventory_parts_type.part_type,services.services as service_name,hsn_code_details.hsn_code as hsn_code ";
                    $inventory_parts_type = $this->inventory_model->get_inventory_parts_type_details($select, array('inventory_parts_type.id' => $part_type_id), TRUE);

                    if (!empty($inventory_parts_type)) {

                        echo json_encode($inventory_parts_type[0]);
                    }
                }
            }
        }
    }

    /**
     *  @desc : This function is used to get model number with select html only from appliance_model_detail table
     *  @param : void
     *  @return : html
     */
    function get_appliance_model_number() {

        $where = array(
            'entity_id' => $this->input->post("partner_id"),
            'entity_type' => _247AROUND_PARTNER_STRING,
            'service_id' => $this->input->post("service_id"),
            'active' => 1
        );
        $inventory_details = $this->inventory_model->get_appliance_model_details('id,model_number', $where);
        if (!empty($inventory_details)) {
            $option = '<option selected disabled>Select Model Number</option>';
            foreach ($inventory_details as $value) {
                $option .= "<option value='" . $value['id'] . "'";
                $option .=">";
                $option .= $value['model_number'] . "</option>";
            }
            echo $option;
        } else {
            echo false;
        }
    }

    /**
     *  @desc : This function is used to process mapping model number
     *  @param : $partner_id, $service_id, $brand, $category, $capacity, $model
     *  @return : array
     */
    function process_model_number_mapping() {
        $return = array();
        $details = array(
            "partner_id" => $this->input->post("partner_id"),
            "service_id" => $this->input->post("service_id"),
            "brand" => $this->input->post("brand"),
            "category" => $this->input->post("category"),
            "capacity" => $this->input->post("capacity"),
            "model" => $this->input->post("model"),
        );

        $data = $this->partner_model->get_partner_appliance_details($details, 'id');
        if (empty($data)) {
            $insert_id = $this->partner_model->insert_partner_appliance_detail($details);
            if ($insert_id) {
                $return['status'] = true;
                $return['message'] = "Model Number Mapped Successfully";
            } else {
                $return['status'] = false;
                $return['message'] = "Error Occured while Mapping Model Number";
            }
        } else {
            $return['status'] = false;
            $return['message'] = "Model Number Already Mapped";
        }
        echo json_encode($return);
    }

    /**
     * @desc: This function is used to get mapped model number for partner
     * @params: $partner_id
     * @return: array
     */
    function get_partner_mapped_model_details() {
        $data = $this->get_partner_mapped_model_data();
        $post = $data['post'];
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->reusable_model->count_all_result("partner_appliance_details", $post['where']),
            "recordsFiltered" => $this->reusable_model->count_all_filtered_result("partner_appliance_details", "count(model) as numrows", $post),
            "data" => $data['data'],
        );
        echo json_encode($output);
    }

    function get_partner_mapped_model_data() {
        $post = $this->get_post_data();
        $post['column_order'] = array();

        $post['order'] = array('appliance_model_details.model_number' => "ASC", "services.services" => "ASC");
        $post['column_search'] = array('appliance_model_details.model_number', 'partner_appliance_details.brand', 'services.services','partner_appliance_details.category', 'partner_appliance_details.capacity');

        if ($this->input->post('service_id') && $this->input->post('service_id') !== 'all') {
            $post['where'] = array('partner_appliance_details.partner_id' => $this->input->post('partner_id'),'partner_appliance_details.service_id'=>$this->input->post('service_id'));
        }else{
           $post['where'] = array('partner_appliance_details.partner_id' => $this->input->post('partner_id')); 
        }

        $post['join'] = array(
            "appliance_model_details" => "appliance_model_details.id = partner_appliance_details.model","services" => "services.id = partner_appliance_details.service_id"
        );
        $post['joinType'] = array("services" => "INNER", "appliance_model_details" => "LEFT");
        $select = "partner_appliance_details.id as tid, partner_appliance_details.model as model_id, partner_appliance_details.service_id, services.services, partner_appliance_details.brand, partner_appliance_details.category, partner_appliance_details.capacity, appliance_model_details.model_number, partner_appliance_details.active";


        $list = $this->reusable_model->get_datatable_data("partner_appliance_details", $select, $post);
        //log_message('info', __METHOD__. "kalyani ".$this->db->last_query());
        $data = array();
        $no = $post['start'];
        foreach ($list as $model_list) {
            $no++;
            $row = $this->get_partner_mapped_model_table($model_list, $no, $this->input->post('source'));
            $data[] = $row;
        }

        return array(
            'data' => $data,
            'post' => $post
        );
    }

    function get_partner_mapped_model_table($model_list, $no, $source) {
        $row = array();
        $json = json_encode(array("map_id" => $model_list->tid, "model" => $model_list->model_id, "service" => $model_list->service_id, "brand" => $model_list->brand, "category" => $model_list->category, "capacity" => $model_list->capacity, "model_number" => $model_list->model_number,"services"=>$model_list->services));
        $row[] = $no;
        $row[] = $model_list->services;
        if(!empty($model_list->model_number)){
            if ($this->session->userdata('userType') == 'service_center') {
            $row[] = $model_list->model_number; 
            } else {
//             $row[] =$model_list->model_number. "<a href='javascript:void(0)'  style='font-size: 20px;
//             padding-left: 10px;' id='appliance_model_details_dataeditmodel' data-id='$json' title='Edit Model'><i class = 'fa fa-edit'></i></a>";
                $row[] =$model_list->model_number;
           }  
        }else{
           $row[] = "<span>Not Available</span>"; 
        }
        $row[] = $model_list->brand;
        $row[] = $model_list->category;
        $row[] = $model_list->capacity;
      if ($source == "admin_crm") {
            if ($model_list->active == 1) {
                $row[] = "<button class='btn btn-warning btn-sm' onclick='update_mapping_status(" . $model_list->active . ", " . $model_list->tid . ")'>Active</button>";
            } else {
                $row[] = "<button class='btn btn-warning btn-sm' onclick='update_mapping_status(" . $model_list->active . ", " . $model_list->tid . ")'>Inactive</button>";
            }

            $row[] = "<button class='btn btn-primary btn-sm' data='" . $json . "' onclick='edit_mapped_model(this)'>Edit</button>";
        } else {
            if ($model_list->active == 1) {
                $row[] = "Active";
            } else {
                $row[] = "Inactive";
            }
            $row[] = "<button class='btn btn-primary btn-sm' data='" . $json . "' onclick='edit_mapped_model(this)'>Edit</button>";
        }

        if(!empty($model_list->model_id)){
            if ($this->session->userdata('userType') == 'service_center') {
            $row[] = "<a href='" . base_url() . "service_center/inventory/inventory_list_by_model/" . urlencode($model_list->model_id) . "' class ='btn btn-primary' title='Get Part Details' target='_blank'><i class = 'fa fa-eye'></i></a>";
            } else {
            $row[] = "<a href='" . base_url() . "employee/inventory/get_inventory_by_model/" . urlencode($model_list->model_id) . "' class ='btn btn-primary' title='Get Part Details' target='_blank'><i class = 'fa fa-eye'></i></a>";
           }  
        }else{
           $row[] = "<span>Not Available</span>"; 
        }

        return $row;
    }

    /**
     *  @desc : This function is used to update mapping model number
     *  @param : $service_id, $brand, $category, $capacity, $model
     *  @return : array
     */
    function update_model_number_mapping() {
        $return = array();
        ;
        $where = array(
            "partner_id" => $this->input->post("partner_id"),
            "service_id" => $this->input->post("service_id"),
            "brand" => $this->input->post("brand"),
            "category" => $this->input->post("category"),
            "model" => $this->input->post("model"),
            "id != '" . $this->input->post("partner_appliance_details_id") . "'" => null
        );
        if ($this->input->post("capacity")) {
            $where['capacity'] = $this->input->post("capacity");
        }
        $data = $this->partner_model->get_partner_appliance_details($where, 'id');
        if (empty($data)) {
            $details = array(
                "partner_id" => $this->input->post("partner_id"),
                "service_id" => $this->input->post("service_id"),
                "brand" => $this->input->post("brand"),
                "category" => $this->input->post("category"),
                "capacity" => $this->input->post("capacity"),
                "model" => $this->input->post("model"),
            );
            $this->partner_model->update_partner_appliance_details(array("id" => $this->input->post("partner_appliance_details_id")), $details);
            $return['status'] = true;
            $return['message'] = "Mapped Model Number Updated Successfully";
        } else {
            $return['status'] = false;
            $return['message'] = "Model Number Mapping Already Exist";
        }
        echo json_encode($return);
    }



       /**
     *  @desc : This function is used to update mapping model number
     *  @param : $service_id, $brand, $category, $capacity, $model
     *  @return : array
     */
    function add_model_number_mapping() {
        $return = array();

            $aplliance_model_where = array(
            'service_id' => $this->input->post("service_id"),
            'model_number' => trim($this->input->post('model')),
            'entity_type' => 'partner',
            'entity_id' => $this->input->post('entity_id'),
        );
            $model_detail = $this->inventory_model->get_appliance_model_details("id", $aplliance_model_where);
            if (empty($model_detail)) {

            $data = array('service_id' => $this->input->post('service_id'),
                'model_number' => trim($this->input->post('model')),
                'entity_id' => $this->input->post('entity_id'),
                'entity_type' => $this->input->post('entity_type')
            );
           $resp = $this->add_appliance_model_data($data);     
           $details = array(
           "partner_id" => $this->input->post("partner_id"),
           "service_id" => $this->input->post("service_id"),
           "brand" => $this->input->post("brand"),
           "category" => $this->input->post("category"),
           "capacity" => $this->input->post("capacity"),
           "model" => $resp['id'],
           );
            $this->partner_model->insert_partner_appliance_detail($details);
            $return['status'] = true;
            $return['message'] = "Model Number Added And  Mapping Successfully";
            }else{
            $return['status'] = false;
            $return['message'] = "Model Number Mapping Already Exist";
            }

      
        echo json_encode($return);
    }

    /**
     *  @desc : This function is used to update status of mapped model number
     *  @param : $status, $partner_aplliance_details_id
     *  @return : array
     */
    function update_mapped_model_number_status() {
        $return = array();
        $status = 0;
        if ($this->input->post("status") == "0") {
            $status = 1;
        }
        $details = array(
            "active" => $status,
        );

        $this->partner_model->update_partner_appliance_details(array("id" => $this->input->post("id")), $details);

        $return['status'] = true;
        $return['message'] = "Status Updated Successfully";
        echo json_encode($return);
    }

    /**
     *  @desc : This function is used to get Partner Wise Spare Parts List
     *  @param : $inventory_id, 
     *  @return : $res array
     */
    function partner_wise_inventory_spare_parts_list() {
        
        if (!empty($this->input->post("entity_id"))) {
            $where = array(
                'inventory_master_list.entity_id' => $this->input->post("entity_id"),
                'inventory_master_list.entity_type' => $this->input->post("entity_type"),
                'inventory_master_list.service_id' => $this->input->post("service_id"),
                'inventory_master_list.type' => $this->input->post("type")
            );
            $master_list = $this->inventory_model->get_inventory_master_list_data('inventory_master_list.inventory_id,inventory_master_list.part_number', $where);
        }

        $option = '<option selected disabled>Select Part Number</option>';

        if (!empty($master_list)) {
            foreach ($master_list as $value) {
                $option .= "<option data-inventory='" . $value['inventory_id'] . "' value='" . $value['part_number'] . "'>";
                $option .= $value['part_number'] . "</option>";
            }
        }
        echo $option;
    }
    
    
        /**
     *  @desc : This function is used to get Partner Wise Spare Parts List
     *  @param : $inventory_id, 
     *  @return : $res array
     */
    function partner_wise_inventory_spare_parts_list_type() {

        if (!empty($this->input->post("entity_id"))) {
            $where = array(
                'inventory_master_list.entity_id' => $this->input->post("entity_id"),
                'inventory_master_list.entity_type' => $this->input->post("entity_type"),
                'inventory_master_list.service_id' => $this->input->post("service_id")
            );
            $master_list = $this->inventory_model->get_inventory_master_list_data('inventory_master_list.type', $where);
        }

        $option = '<option selected disabled>Select Part Type</option>';

        if (!empty($master_list)) {
            foreach ($master_list as $value) {
                $option .= "<option data-inventorytype='" . $value['type'] . "' value='" . $value['type'] . "'>";
                $option .= $value['type'] . "</option>";
            }
        }
        echo $option;
    }

    /**
     *  @desc : This function is used to update alternate inventory set
     *  @param : void, 
     *  @return : json
     */
    function upate_alternate_inventory_set() {
        $res = array();
        if (!empty($this->input->post("inventory_id"))) {
            $data = array('alternate_inventory_set.status' => $this->input->post("status"));
            $where = array(
                'alternate_inventory_set.group_id' => $this->input->post("group_id"),
                'alternate_inventory_set.inventory_id' => $this->input->post("inventory_id"),
                'alternate_inventory_set.model_id' => $this->input->post("appliance_model_id")
            );
            $affect_row = $this->inventory_model->update_alternate_inventory_set($data, $where);
            if ($affect_row) {
                $res['status'] = TRUE;
            } else {
                $res['status'] = FALSE;
            }
        } else {
            $res['status'] = 'Inventory id not found';
        }

        echo json_encode($res);
    }
    /**
     * @desc This function is used to get success message when spare cancelled but this is not on priority.
     * @param String $booking_id, $is_reason_required (Return all cancellation reason)
     * $return JSON (Status and cancellation reason if required)
     */
    function get_spare_cancelled_status($booking_id, $is_reason_required = '') {
        log_message('info', __METHOD__ . " Booking ID " . $booking_id);

        $data = array();
        $cancellation_reason = array();
        $select = 'spare_parts_details.booking_id, status';
        if (!empty($is_reason_required) && $is_reason_required == 1) {
            $data['spare_cancel_reason'] = true;
            $select .= ", booking_cancellation_reasons.reason";
        }

        $spare = $this->partner_model->get_spare_parts_by_any($select, array('spare_parts_details.booking_id' => $booking_id), '', '', '', $data);

        if (!empty($spare)) {
            $is_cancelled = false;
            $not_can = false;
            foreach ($spare as $value) {
                if ($value['status'] == _247AROUND_CANCELLED) {
                    $is_cancelled = true;
                    if (!empty($value['reason'])) {
                        $cancellation_reason[] = $value['reason'];
                    }
                } else {
                    $not_can = true;
                }
            }

            if ($not_can) {
                $return = "Not Exist";
            } else if ($is_cancelled) {
                $return = "success";
            } else {
                $return = "Not Exist";
            }
        } else {
            $return = "Not Exist";
        }
        $response['status'] = $return;
        $response['reason'] = implode('<br>', array_filter($cancellation_reason));
        echo json_encode($response);
    }

    function get_spare_delivered_status($booking_id) {
        $spare = $this->partner_model->get_spare_parts_by_any('spare_parts_details.booking_id,spare_parts_details.is_micro_wh, status, auto_acknowledeged', array('spare_parts_details.booking_id' => $booking_id, 'status' => SPARE_DELIVERED_TO_SF));
        if (!empty($spare)) {
            echo json_encode($spare);
        } else {
            echo json_encode(array("Not Exist"));
        }
    }
    
    /**
     *  @desc : This function is used to get spare parts status
     *  @param : $booking_id
     *  @return : json
     */
    function get_spare_status($booking_id){
        $spare = $this->partner_model->get_spare_parts_by_any('spare_parts_details.booking_id, spare_parts_details.status', array('spare_parts_details.booking_id' => $booking_id));
        if(!empty($spare)){
            echo json_encode($spare);
        } else {
            echo json_encode(array("Not Exist"));
        }
    }
    
//    function remove_inventory_from_warehouse($invoice, $sender_enity_id, $wh_id, $agent_id){
//        foreach ($invoice as $value) {
//            $in['receiver_entity_id'] = $wh_id;
//            $in['receiver_entity_type'] = _247AROUND_SF_STRING;
//            $in['sender_entity_id'] = $sender_enity_id;
//            $in['sender_entity_type'] = _247AROUND_SF_STRING;
//            $in['stock'] = -$value['qty'];
//            if(isset($value['booking_id'])){
//                $in['booking_id'] = $value['booking_id'];
//            }
//            $in['agent_id'] = $agent_id;
//            $in['agent_type'] = _247AROUND_SF_STRING;
//            $in['is_wh'] = TRUE;
//            $in['inventory_id'] = $value['inventory_id'];
//            $this->miscelleneous->process_inventory_stocks($in);
//        }
//    }
    
    function get_partner_gst_number(){
        $html = "<option value='' selected disabled>Select GST Number</option>";
        $where = array(
            "entity_type" => _247AROUND_PARTNER_STRING,
            "entity_id" => $this->input->post("partner_id")
            );
        $gst_numbers = $this->inventory_model->get_entity_gst_data("entity_gst_details.id as id, gst_number, state_code.state as state", $where);
        foreach($gst_numbers as $key => $value){
            $html .= "<option value='".$value['id']."'>".$value['state']." - ".$value['gst_number']."</option>";
        }
        echo $html;
    }
    
    function get_247around_wh_gst_number(){

        $state_vendor = $this->vendor_model->viewvendor($this->session->userdata('service_center_id'));
        $html = "<option value='' selected disabled>Select GST Number</option>";
        $where = array(
            "entity_type" => _247AROUND_PARTNER_STRING,
            "entity_id" => _247AROUND,
            );
        if ($this->session->userdata('userType')=='service_center') {
         $where['state_code.state']  =ucwords(strtolower($state_vendor[0]['state']));
        }
        $gst_numbers = $this->inventory_model->get_entity_gst_data("entity_gst_details.id as id, entity_gst_details.state as state_code, gst_number, state_code.state as state", $where);
        foreach ($gst_numbers as $key => $value) {
            $html .= "<option data-state_code =  '".$value['state_code']."' value='" . $value['id'] . "'>" . $value['state'] . " - " . $value['gst_number'] . "</option>";
        }
        echo $html;
    }



                /**
    * @desc This function is used to get success message when spare cancelled but this is not on priority.
     * @param String $booking_id
     */
    function msl_excel_upload(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/msl_excel_upload');
       
        
    }
    
    /**
     * @desc This function is used to create the view page to upload msl file.
     * @param: null
     */
    function upload_msl_excel_file() {
        $data['courier_details'] = $this->inventory_model->get_courier_services('*');
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/upload_msl_excel_file',$data);
    }

    /**
     *  @desc : This function is used to appliance wise model number and inventory details
     *  @param : void
     *  @return : void
     */
    function download_serviceable_bom() {
        $this->checkUserSession();
        $this->miscelleneous->load_nav_header();
        $this->load->view("employee/download_serviceable_bom");
    }
       
     /**
     *  @desc : This function is used to Download the Serviceable BOM.
     *  @param : void
     *  @return : void
     */
    
     function download_serviceable_bom_data() {
        log_message('info', __METHOD__ . ' Processing...');

        $partner_id = $this->input->post('partner_id');
        $service_id = $this->input->post('service_id');

        $select = "services.services AS APPLIANCE, appliance_model_details.model_number AS MODEL_NUMBER, inventory_master_list.part_number AS PART_NUMBER, inventory_master_list.part_name AS PART_NAME";
        $where = array("inventory_master_list.entity_id" => $partner_id, "appliance_model_details.service_id" => $service_id);

        if (!empty($partner_id) && !empty($service_id)) {
            $bom_details = $this->inventory_model->get_serviceable_bom_data($select, $where);


            $this->load->dbutil();
            $this->load->helper('file');

            $file_name = 'serviceable_bom_data_' . date('j-M-Y-H-i-s') . ".csv";
            $delimiter = ",";
            $newline = "\r\n";
            $new_report = $this->dbutil->csv_from_result($bom_details, $delimiter, $newline);

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
        }
        echo json_encode($res);
    }
        
    /**
     *  @desc : This function is used to appliance wise Part details
     *  @param : void
     *  @return : void
     */
    function download_part_master() {
        $this->checkUserSession();
        $this->miscelleneous->load_nav_header();
        $this->load->view("employee/download_inventory_part_master");
    }
    
    
     /**
     *  @desc : This function is used to Download the Serviceable BOM.
     *  @param : void
     *  @return : void
     */
    
     function download_inventory_part_master_data() {
        log_message('info', __METHOD__ . ' Processing...');

        $partner_id = $this->input->post('partner_id');
        $service_id = $this->input->post('service_id');

        $select = "services.services AS APPLIANCE, inventory_master_list.part_number AS PART_NUMBER, "
                . "inventory_master_list.part_name AS PART_NAME, inventory_master_list.price AS PRICE, inventory_master_list.type AS TYPE, inventory_master_list.hsn_code AS HSN_CODE,"
                . " inventory_master_list.gst_rate AS GST_RATE";
        $where = array("inventory_master_list.entity_id" => $partner_id, "appliance_model_details.service_id" => $service_id);

        if (!empty($partner_id) && !empty($service_id)) {
            $bom_details = $this->inventory_model->get_serviceable_bom_data($select, $where);


            $this->load->dbutil();
            $this->load->helper('file');

            $file_name = 'inventory_part_master_' . date('j-M-Y-H-i-s') . ".csv";
            $delimiter = ",";
            $newline = "\r\n";
            $new_report = $this->dbutil->csv_from_result($bom_details, $delimiter, $newline);

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
        }
        echo json_encode($res);
    }
    
     /**
     *  @desc : This function is used download the missing BOM
     *  @param : void
     *  @return : void
     */
    function download_missing_serviceable_bom() {
        $this->checkUserSession();
        $this->miscelleneous->load_nav_header();
        $this->load->view("employee/download_missing_serviceable_bom");
    }
    
    
    
     /**
     *  @desc : This function is used to Download the Missing Serviceable BOM.
     *  @param : void
     *  @return : void
     */
    
     function download_missing_serviceable_bom_data() {
        log_message('info', __METHOD__ . ' Processing...');

        $partner_id = $this->input->post('partner_id');
        $service_id = $this->input->post('service_id');
        $select = "SELECT DISTINCT services AS APPLIANCE, `appliance_model_details`.`model_number` AS MODEL_NUMBER";
                
        if (!empty($partner_id) && !empty($service_id)) {
            $bom_details = $this->inventory_model->get_missing_serviceable_bom_data($select, $partner_id, $service_id);

            $this->load->dbutil();
            $this->load->helper('file');

            $file_name = 'missing_serviceable_bom_data_' . date('j-M-Y-H-i-s') . ".csv";
            $delimiter = ",";
            $newline = "\r\n";
            $new_report = $this->dbutil->csv_from_result($bom_details, $delimiter, $newline);

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
        }
        echo json_encode($res);
    }
     /**
     *  @desc : This function is used to Download the warehouse stocks.
     *  @param : void
     *  @return : void
     */
    function download_warehouse_stock_data($request_type_url ='') {
        log_message('info', __METHOD__ . ' Processing...');

        $request_type = $this->input->post('request_type');
        $fetch_array = false;
        if(empty($request_type) && !empty($request_type_url)){
           $request_type = $request_type_url;
           $fetch_array = true;
        }
        $partner_id = $this->input->post('partner_id');

        $select = "service_centres.name AS Warehouse, partners.public_name AS 'Partner', inventory_master_list.part_number AS 'Part Number', inventory_master_list.part_name AS 'Part Name', inventory_stocks.stock AS Stock";

        if ($request_type == 'warehouse') {

            $select .= ", inventory_master_list.price AS '247 Buying Price',inventory_master_list.gst_rate as 'GST Rate'";

            $post['where'] = array("service_centres.is_wh" => 1, "inventory_stocks.entity_type" => _247AROUND_SF_STRING, "inventory_master_list.inventory_id NOT IN (1,2)" => NULL);
        } else {

            $select .= ", inventory_master_list.price AS '247 Buying Price',inventory_master_list.gst_rate as 'GST Rate'";
            if (empty($partner_id)) {
                $select .= ",inventory_master_list.oow_around_margin as 'Around Margin', inventory_master_list.oow_vendor_margin as 'Vendor Margin'";
            }
            $post['where'] = array("service_centres.is_micro_wh" => 1, "inventory_stocks.entity_type" => _247AROUND_SF_STRING, "inventory_master_list.inventory_id NOT IN (1,2)" => NULL);
        }

        if (!empty($partner_id)) {
            $post['where']['inventory_master_list.entity_id'] = $this->input->post('partner_id');
            $post['where']['inventory_master_list.entity_type'] = _247AROUND_PARTNER_STRING;
        }

        if (!empty($request_type)) {

            $bom_details = $this->inventory_model->get_warehouse_stocks($post, $select,$fetch_array);
            if (!empty($request_type_url)) {
                $array = array();
                foreach ($bom_details as $key => $value) {
                    $array[$key][] = $value['Warehouse'];
                    $array[$key][] = $value['Partner'];
                    $array[$key][] = $value['Part Number'];
                    $array[$key][] = $value['Part Name'];
                    $array[$key][] = $value['Stock'];
                    $array[$key][] = $value['247 Buying Price'];
                    $array[$key][] = $value['GST Rate'];
                    $array[$key][] = $value['Around Margin'];
                    $array[$key][] = $value['Vendor Margin'];
                    $vendor_margin = $value['Vendor Margin'];
                    $around_margin = $value['Around Margin'];
                    $base_price = $value['247 Buying Price'];
                    $gst_rate = $value['GST Rate'];
                    $basic_price_with_around_margin = (float) $base_price + ($base_price * ($around_margin / 100));
                    $sf_total = $basic_price_with_around_margin + ($basic_price_with_around_margin * ($gst_rate / 100));
                    $array[$key][] = number_format((float) $sf_total, 2, '.', '');

                    $total = number_format((float) ($base_price + ($base_price * ($gst_rate / 100))), 2, '.', '');
                    $customer_price = $total * ( 1 + ($vendor_margin + $around_margin) / 100 );

                    $array[$key][] = number_format((float) $customer_price, 2, '.', '');
                }
                $headings = array("Warehouse", "Partner", "Part Number", "Part Name", "Stock", "247 Buying Price", "GST Rate", "Around Margin", "Vendor Margin", "SF Buying Price", "Customer Price");
                $file_name = $request_type . '_stock_data_' . date('j-M-Y-H-i-s');
                $this->miscelleneous->downloadCSV($array, $headings, $file_name);
            } else{

            $this->load->dbutil();
            $this->load->helper('file');

            $file_name = $request_type . '_stock_data_' . date('j-M-Y-H-i-s') . ".csv";
            $delimiter = ",";
            $newline = "\r\n";
            $new_report = $this->dbutil->csv_from_result($bom_details, $delimiter, $newline);

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
        }
        }
        echo json_encode($res);
    }
    
    /**
     *  @desc : This function is used to Download inventory ledger details
     *  @param : void
     *  @return : void
     */
    function download_msl_invoice() {
        $this->checkUserSession();
        $this->miscelleneous->load_nav_header();
        $data['invoice_details']=array();
        $this->load->view("employee/download_inventory_ledger",$data);
    }

    /**
     *  @desc : This function is used to get the list of sale purchase invoices.
     *  @param : partner_id
     *  @return : json
     */

    function get_list_sale_purchage_invoice_data(){
 
        $post = $this->post_sale_purchage_invoice();
        $partner_id = trim($_POST['partner_id']);
        $select = "invoice_details.id, invoice_details.invoice_id,invoice_details.inventory_id, date_format(vendor_partner_invoices.invoice_date, \"%d-%m-%Y %h:%i:%s\") AS 'invoice_date', case when (type_code = 'B') THEN 'Purchase Invoice' ELSE 'Sale Invoice' END AS 'invoice_type', part_number, "
                . "invoice_details.description, invoice_details.hsn_code, invoice_details.qty,invoice_details.settle_qty, rate, invoice_details.taxable_value, (invoice_details.cgst_tax_rate + invoice_details.igst_tax_rate + invoice_details.sgst_tax_rate) AS gst_rate,"
                . " (invoice_details.cgst_tax_amount + invoice_details.igst_tax_amount + invoice_details.sgst_tax_amount) AS gst_tax_amount, total_amount, vendor_partner_invoices.type, entt_gst_dtl.gst_number,entity_gst_details.gst_number as to_gst_number,"
                . "vendor_partner_invoices.sub_category";

        $where = array("sub_category IN ('" . MSL_DEFECTIVE_RETURN . "', '". MSL_Credit_Note . "', '"  . MSL_Debit_Note . "', '"  .  IN_WARRANTY . "', '" . MSL . "', '" . MSL_NEW_PART_RETURN . "')" => NULL, "vendor_partner_invoices.vendor_partner_id" => $partner_id);

        $post['column_search'] = array('invoice_details.invoice_id', 'invoice_details.description', 'entity_gst_details.gst_number','inventory_master_list.part_number');
        $list = $this->inventory_model->get_inventory_ledger_details_data_view($select, $where,$post);

        

        
        $no = $post['start'];
        $data = array();
        foreach ($list as $spare_list) {
            $no++;
            $row =  $this->get_list_sale_purchage_invoice_data_table($spare_list, $no);
            $data[] = $row;
        }
        $total  = $this->inventory_model->count_sale_purchase_msl_parts($select, $where,$post);
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => count($total),
            "recordsFiltered" =>  count($total),
            "data" => $data,
        );
        
        echo json_encode($output);



    }


        /**
     * @desc This function is used to get post data from datatable
     * @return Array
     */
    function post_sale_purchage_invoice(){
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search']['value'] = $search['value'];
        
        $post['draw'] = $this->input->post('draw');
        $post['type'] = $this->input->post('type');
        $post['order'] = $this->input->post('order');

        return $post;
    }

       /**
     * @desc This is view the settled qty 
     * @return Array
     */

    function view_invoice_data($invoice,$inventory){

        $data['invoice_details'] = $this->inventory_model->inventory_invoice_mapping_data($invoice,$inventory);
        $data['invoice'] = $invoice;
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/sale_purchase_settle_view',$data);

    }

      /**
     * @desc this is to print datatable
     * @return Array
     */

    function get_list_sale_purchage_invoice_data_table($spare_list, $no){

        $row = array();
        
        $row[] = $no;
        $row[] = $spare_list->invoice_id;
 
        $row[] = $spare_list->invoice_date;
        $row[] = $spare_list->invoice_type;
        $row[] = $spare_list->part_number;
        $row[] = $spare_list->description;
        $row[] = $spare_list->hsn_code;
        $row[] = $spare_list->qty;
        if ($spare_list->settle_qty==0) {
        $row[] = $spare_list->settle_qty;  
        }else{
        $row[] = '<a href="'. base_url().'employee/inventory/view_invoice_data/'.$spare_list->invoice_id.'/'.$spare_list->inventory_id.'" target= "_blank" >'.$spare_list->settle_qty.'</a>';  
        }
        
        $row[] = $spare_list->rate;
        $row[] = $spare_list->taxable_value;
        $row[] = $spare_list->gst_rate;
        $row[] = $spare_list->gst_tax_amount;
        $row[] = $spare_list->total_amount;
        $row[] = $spare_list->type;
        $row[] = $spare_list->gst_number;
        $row[] = $spare_list->to_gst_number;
        $row[] = $spare_list->sub_category;
//        $row[] = $spare_list->AWB_no;
//        $row[] = $spare_list->courier_name;
//        $row[] = $spare_list->shipment_date;
        return $row;



    }



     /**
     *  @desc : This function is used to Download the Inventory Ledger Details.
     *  @param : void
     *  @return : void
     */
    function  download_sale_purchage_invoice_data(){
        log_message('info', __METHOD__ . ' Processing...');

        $partner_id = $this->input->post('partner_id');
        
        $select = "invoice_details.invoice_id AS 'Invoice Id', date_format(vendor_partner_invoices.invoice_date, \"%d-%m-%Y %h:%i:%s\") AS 'Invoice Date', case when (type_code = 'B') THEN 'Purchase Invoice' ELSE 'Sale Invoice' END AS 'Invoice Type', part_number AS 'Part Number', "
                . "invoice_details.description AS 'Description', invoice_details.hsn_code AS 'HSN Code', invoice_details.qty AS 'Quantity',invoice_details.settle_qty AS 'Settled Quantity', rate AS 'Rate', invoice_details.taxable_value AS 'Taxable Value', (invoice_details.cgst_tax_rate + invoice_details.igst_tax_rate + invoice_details.sgst_tax_rate) AS 'GST Rate',"
                . " (invoice_details.cgst_tax_amount + invoice_details.igst_tax_amount + invoice_details.sgst_tax_amount) AS 'GST Tax Amount', total_amount AS 'Total Amount', vendor_partner_invoices.type AS Type, entt_gst_dtl.gst_number AS 'From GST Number',entity_gst_details.gst_number AS 'To GST Number',"
                . "vendor_partner_invoices.sub_category AS 'Sub Category',courier_details.AWB_no AS 'Awb_Number',courier_details.courier_name AS 'Courier Name',date_format(courier_details.shipment_date, \"%d-%b-%Y %H:%i:%s\") AS 'Shipment Date'";

        $where = array("sub_category IN ('" . MSL_DEFECTIVE_RETURN . "', '" .MSL_Credit_Note . "', '"  . MSL_Debit_Note . "', '"  . IN_WARRANTY . "', '" . MSL . "', '" . MSL_NEW_PART_RETURN . "')" => NULL, "vendor_partner_invoices.vendor_partner_id" => $partner_id);

        if (!empty($partner_id)) {
            $bom_details = $this->inventory_model->get_inventory_ledger_details_data($select, $where);
            $this->load->dbutil();
            $this->load->helper('file');

            $file_name = 'inventory_ledger_details_data_' . date('j-M-Y-H-i-s') . ".csv";
            $delimiter = ",";
            $newline = "\r\n";
            $new_report = $this->dbutil->csv_from_result($bom_details, $delimiter, $newline);

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
        }
        echo json_encode($res);
    }
     /**
     *  @desc : This function is used to deactive the model number.
     *  @param : void
     *  @return : void
     */
    function get_model_details_deactive(){
        $inventory_id = $this->input->post('inventory_id');
        $select = "inventory_model_mapping.id,inventory_model_mapping.inventory_id,inventory_model_mapping.active,inventory_master_list.part_number,inventory_master_list.part_name, appliance_model_details.model_number";
        $where = array("inventory_master_list.inventory_id" => $inventory_id);
        $model_details = $this->inventory_model->get_inventory_model_mapping_data($select, $where);
        echo json_encode($model_details);
        
    }
    
    /**
     *  @desc : This function is used to update alternate inventory set
     *  @param : void, 
     *  @return : json
     */
    
    function upate_inventory_model_mapping(){
       $res = array();
        if (!empty($this->input->post("model_mapping_id"))) {
            
            $data = array('inventory_model_mapping.active' => $this->input->post("status"));
            $where = array('inventory_model_mapping.id' => $this->input->post("model_mapping_id"));
            
            $affect_row = $this->inventory_model->update_inventory_model_mapping($data, $where);
            
            if ($affect_row) {
                $res['status'] = TRUE;
            } else {
                $res['status'] = FALSE;
            }
        } else {
            $res['status'] = 'inventory model mapping id not found';
        }

        echo json_encode($res);
    }




        /**
     *  @desc : This function is used to update max  quantity 
     *  @param : void, 
     *  @return : json
     */
    
    function upate_inventory_model_mapping_max_qty(){
       $res = array();
        if (!empty($this->input->post("model_mapping_id"))) {
            
            $data = array('inventory_model_mapping.max_quantity' => $this->input->post("max_qty"));
            $where = array('inventory_model_mapping.id' => $this->input->post("model_mapping_id"));

            
            $affect_row = $this->inventory_model->update_inventory_model_mapping($data, $where);
            
            if ($affect_row) {
                $res['status'] = TRUE;
            } else {
                $res['status'] = FALSE;
            }
        } else {
            $res['status'] = 'inventory model mapping id not found';
        }

        echo json_encode($res);
    }
    
     /**
     *  @desc : This function is used to remove msl consumption 
     *  @param : void
     *  @return : void
     */
    function remove_msl_consumption(){
        $spare_parts_id = $this->input->post("spare_parts_id");
        $booking_id= $this->input->post("booking_id");
        $spare_action = $this->update_action_on_spare_parts($spare_parts_id, $booking_id, "DELIVERED_PART_CANCELLED");
        
        /* Load data which is required i.e., remove all shipped details & courier details */
        $spare_data = $this->miscelleneous->load_data_to_cancel_micro_wh_part($spare_parts_id, _247AROUND_CANCELLED, NULL);
        /* update data of spare parts details */
        $this->service_centers_model->update_spare_parts(['id' => $spare_parts_id], $spare_data);

        
        //increase stock on cancel part
        $data = array(
            "receiver_entity_type" => _247AROUND_SF_STRING,
            "receiver_entity_id" => $this->session->userdata("service_center_id"),
            "sender_entity_type" => _247AROUND_SF_STRING,
            "sender_entity_id" => $this->session->userdata("service_center_id"),
            "stock" => $this->input->post("shipped_quantity"),
            "booking_id" => $this->input->post("booking_id"),
            "inventory_id" => $this->input->post("inventory_id"),
            "agent_id" => $this->session->userdata("service_center_agent_id"),
            "agent_type" => _247AROUND_SF_STRING,
            "is_wh" => TRUE,
            "is_cancel_part" => TRUE,
            "spare_id" => $spare_parts_id
        );
        $this->miscelleneous->process_inventory_stocks($data);
        echo $spare_action;
    }

    function get_rejected_from_review($booking_id) {
        $booking = $this->reusable_model->get_search_result_data('booking_details', 'booking_id, internal_status', ['booking_id' => $booking_id, 'internal_status' => REJECTED_FROM_REVIEW_STATUS], NULL, NULL, NULL, NULL, NULL);
        if(!empty($booking)){
            echo json_encode($booking);
        } else {
            echo json_encode(array("Not Exist"));
        }
    }
    
    /**
     *  @desc : This function is used to download MWH invoice ledger
     *  @param : void
     *  @return : void
     */
    function mwh_invoice_ledger(){
        log_message('info', __METHOD__ . ' Processing...');
        $where = array();
        $select = "service_centres.company_name as 'SF Name', public_name as Partner, vendor_partner_invoices.invoice_id, invoice_date, inventory_master_list.part_number, inventory_master_list.part_name, qty, rate, invoice_details.taxable_value, (invoice_details.cgst_tax_rate + invoice_details.sgst_tax_rate + invoice_details.igst_tax_rate ) as gst_rate, (invoice_details.cgst_tax_amount + invoice_details.sgst_tax_amount +invoice_details.igst_tax_amount) as tax_amount, invoice_details.total_amount, CASE when (inventory_ledger.is_wh_ack = 1) THEN 'YES' ELSE 'NO' END AS 'Is Ack', inventory_ledger.wh_ack_date  FROM `vendor_partner_invoices` JOIN invoice_details ON `vendor_partner` LIKE 'vendor' AND `sub_category` LIKE 'MSL' AND invoice_details.invoice_id = vendor_partner_invoices.invoice_id JOIN  inventory_master_list ON   inventory_master_list.inventory_id = invoice_details.inventory_id JOIN service_centres ON  service_centres.id = vendor_partner_id JOIN  partners ON partners.id =  vendor_partner_invoices.third_party_entity_id LEFT JOIN inventory_ledger ON inventory_ledger.inventory_id = invoice_details.inventory_id AND inventory_ledger.invoice_id = invoice_details.invoice_id";
        
        if(!empty($this->session->userdata("partner_id"))){
           $where = array('vendor_partner_invoices.third_party_entity_id' => $this->session->userdata("partner_id"),'vendor_partner_invoices.third_party_entity'=> _247AROUND_PARTNER_STRING );
        }
        
        if(!empty($this->session->userdata("service_center_id"))){
           $where = array('vendor_partner_invoices.third_party_entity_id' => $this->session->userdata("service_center_id"),'vendor_partner_invoices.third_party_entity'=> _247AROUND_SF_STRING );
        }
        
        $invoice_details = $this->inventory_model->get_mwh_invoice_ledger_data($select, $where);
                      
        if (!empty($invoice_details)) {
           
            $this->load->dbutil();
            $this->load->helper('file');

            $file_name = 'mwh_invoice_ledger_data_' . date('j-M-Y-H-i-s') . ".csv";
            $delimiter = ",";
            $newline = "\r\n";
            $new_report = $this->dbutil->csv_from_result($invoice_details, $delimiter, $newline);

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
        }
         redirect(base_url() . "file_process/downloadFile/" . $file_name);
        
    }
    
    /**
     *  @desc : This function is used to get HSN Code
     *  @param : void
     *  @return : $res array()
     */
    function get_hsn_code_list() {
        
        $service_id = $this->input->post("service_id");
        $hsn_code_arr = $this->inventory_model->get_hsn_code_details('hsn_code_details.id,hsn_code_details.hsn_code,hsn_code_details.gst_rate', array('hsn_code_details.service_id'=> $service_id));
        $option = '<option selected disabled>Select HSN Code </option>';

        foreach ($hsn_code_arr as $value) {
            $option .= "<option data-gst='" . $value['gst_rate'] . "' value='" . $value['hsn_code'] . "'>";
            $option .= $value['hsn_code'] . "</option>";
        }

        echo $option;
    }
    /**
     *  @desc : This function is used to Spare Invoice Details
     *  @param : void
     *  @return : $res array()
     */
    function get_spare_invoice_details(){
        $spare_id_array = $this->input->post("spare_id_array");

        if (!empty($spare_id_array)) {
            $spare_ids = implode(',', $spare_id_array);
            $select = 'spare_parts_details.booking_id,oow_spare_invoice_details.id,oow_spare_invoice_details.invoice_id,oow_spare_invoice_details.spare_id,oow_spare_invoice_details.invoice_date,oow_spare_invoice_details.hsn_code,oow_spare_invoice_details.invoice_amount as total_invoice_amount,oow_spare_invoice_details.gst_rate,oow_spare_invoice_details.invoice_pdf';
            $where = array("spare_parts_details.id IN(" . $spare_ids . ")" => NULL);
            $oow_invoice_data = $this->inventory_model->get_spare_invoice_details($select, $where);
            $invoice_details =array();
            foreach ($oow_invoice_data as $key => $val) {
                $tax_amount = $this->booking_model->get_calculated_tax_charge($val['total_invoice_amount'], $val['gst_rate']);
                $invoice_details[$key]['id'] = $val['id'];
                $invoice_details[$key]['spare_id'] = $val['spare_id'];
                $invoice_details[$key]['invoice_id'] = $val['invoice_id'];
                $invoice_details[$key]['booking_id'] = $val['booking_id'];
                $invoice_details[$key]['invoice_date'] = $val['invoice_date'];
                $invoice_details[$key]['hsn_code'] = $val['hsn_code'];
                $invoice_details[$key]['gst_rate'] = $val['gst_rate'];
                $invoice_details[$key]['invoice_pdf'] = $val['invoice_pdf'];
                $invoice_details[$key]['invoice_amount'] = $val['total_invoice_amount'] - $tax_amount;
            }
        }
        
        echo json_encode($invoice_details);
        
    }


    function view_part_type_return_mapping($action){
        $this->miscelleneous->load_nav_header();
        $data['action'] = $action;
        $this->load->view('employee/view_part_type_return_mapping',$data);
    }

    function get_parttype_data($action){
        log_message('info', __METHOD__.' Entering...');

        $partner_id = $this->input->post('partner_id');
        $service_id = $this->input->post('appliance_id');

        if($action == 'add'){
            if($partner_id && $service_id){
                $partner = $this->partner_model->getpartner($partner_id);
                if($partner[0]['is_wh']){
                    $part_type = $this->inventory_model->get_inventory_model_mapping_data('inventory_master_list.inventory_id as id,inventory_master_list.type as part_type', array('inventory_master_list.service_id' => $service_id,'inventory_model_mapping.active' => 1));
                }else{
                    $x = $this->inventory_model->get_inventory_parts_type_details('inventory_parts_type.id as id,inventory_parts_type.part_type as part_type', array('inventory_parts_type.service_id' => $service_id), TRUE);;
                }

                echo json_encode($part_type);
            }else{
                echo 'Both Partner And Appliance Need to be present';
            }
        }else if($action == 'edit'){
            $part_type = $this->inventory_model->get_return_part_type_data('*',array('partner_id' => $partner_id, 'appliance_id' => $service_id));

            echo json_encode($part_type);
        }else{
            echo 'Invalid Request';
        }
    }


    function add_part_type_mapping(){
        log_message('info', __METHOD__.' Entering...');

        $data = $this->input->post('dataToAdd');

        
        $insert_batch = $this->inventory_model->insert_part_type_mapping_batch($data);

        if($insert_batch){
            echo 'success';
        }else{
            echo 'failed';
        }
    }

    function update_part_type_mapping() {
        log_message('info', __METHOD__.' Entering...');

         $data = $this->input->post('dataToEdit');

         foreach ($data as $key => $value) {
             $data = array("is_return" => $value['is_return']);
             $where = array('id' => $value['id']);
             $update = $this->inventory_model->update_part_type_mapping($data,$where);
         }

         echo 'success';

    }
    
    /**
     * @desc: This function is used to SF download consumption 
     * @params: void
     * @return: void
     */
    
    function mwh_msl_details(){
        $this->checkUserSession();
        $this->miscelleneous->load_nav_header();
        $this->load->view("employee/service_centers_consumption_details");
    }
    
    
     /**
     *  @desc : This function is used to get Download service centers consumption details.
     *  @param : void
     *  @return : void
     */
    function get_partner_wise_service_centers_consumption_list() {
        $data = $this->get_service_centers_consumption_list_data();
       
        $post = $data['post'];
        if (!empty($data['data'])) {
            $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $this->inventory_model->count_all_service_centers_consumption_list($post),
                "recordsFiltered" => $this->inventory_model->count_filtered_service_centers_consumption_list($post),
                "data" => $data['data'],
            );
        } else {
            $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => $data['data'],
            );
        }
        echo json_encode($output);
    }
    
    
   function get_service_centers_consumption_list_data() {
        $post = $this->get_post_data();
        $entity_type = trim($this->input->post('entity_type'));
        $entity_id = trim($this->input->post('entity_id'));
        $service_centers_id = trim($this->input->post('service_centers_id'));
        $data = array();
        $where = '';
        if (!empty($entity_id)) {
            $post['column_order'] = array();
            $post['column_search'] = array('v.invoice_id', 'v.create_date');
            $where = "im.entity_id = $entity_id AND im.entity_type ='" . $entity_type . "' AND  v.sub_category IN('".MSL_DEFECTIVE_RETURN."', '".IN_WARRANTY."', '".MSL_Credit_Note . "', '"  . MSL_Debit_Note . "', '"  . MSL."', '".MSL_SECURITY_AMOUNT."', '".MSL_NEW_PART_RETURN."' ) ";
            if (!empty($service_centers_id)) {
                $where .= " AND v.vendor_partner_id = " . $service_centers_id;
            }

            $post['where'] = $where;

            $select = "v.invoice_id, v.create_date, case when (v.type_code = 'B') THEN 'Purchase Invoice' ELSE 'Sale Invoice' END AS type_code, im.part_number, i.description, sc.name as sf_name, im.hsn_code, i.qty, i.settle_qty, i.rate, i.taxable_value, (i.cgst_tax_rate + i.igst_tax_rate + i.sgst_tax_rate) AS gst_rate, (i.cgst_tax_amount + i.igst_tax_amount + i.sgst_tax_amount) AS gst_tax_amount, i.total_amount, v.type,i.from_gst_number, i.to_gst_number,v.vendor_partner_id, v.sub_category";
            $list = $this->inventory_model->get_service_centers_consumption_list($post, $select);
            $data = array();
            $no = $post['start'];
            foreach ($list as $consumption_list) {
                $no++;
                $row = $this->get_service_centers_consumption_list_table($consumption_list, $no);
                $data[] = $row;
            }
        }

        return array(
            'data' => $data,
            'post' => $post
        );
    }
    
    
    
    
     function get_service_centers_consumption_list_table($consumption_list, $no) {
        $row = array();
            
        $row[] = $no;
        //$row[] = "<a href='" .base_url(). "employee/inventory/invoice_details/" . $consumption_list->invoice_id."'>".$consumption_list->invoice_id."</a>";
        $row[] = $consumption_list->invoice_id;
        $row[] = $consumption_list->create_date;
        $row[] = "<span style='word-break: break-all;'>" . $consumption_list->type_code . "</span>";
        $row[] = "<span style='word-break: break-all;'>" . $consumption_list->part_number . "</span>";
        $row[] = "<span style='word-break: break-all;'>" . $consumption_list->description . "</span>";
        $row[] = "<span style='word-break: break-all;'>" . $consumption_list->sf_name . "</span>";
        $row[] = $consumption_list->hsn_code;
        $row[] = $consumption_list->qty;
        $row[] = $consumption_list->settle_qty;
        $row[] = "<i class ='fa fa-inr'></i> " . $consumption_list->rate;
        $row[] = $consumption_list->taxable_value;
        $row[] = $consumption_list->gst_rate;
        $row[] = $consumption_list->gst_tax_amount;
        $row[] = $consumption_list->total_amount;
        //$row[] = $consumption_list->type;
        $gst_array = $this->inventory_model->get_gst_number_details($consumption_list);
        $row[] = $gst_array['from_gst_number'];
        $row[] = $gst_array['to_gst_number'];
        $row[] = $consumption_list->sub_category;
    
        return $row;
    }
    
    
    
    
    function download_service_centers_consumption_data() {
        log_message('info', __METHOD__ . ' Processing...');

        $entity_id = $this->input->post('partner_id');
        $service_center_id = $this->input->post('service_center_id');

        if (!empty($entity_id)) {
            $select = "v.invoice_id AS 'Invoice Id', v.create_date AS 'Invoice Date', case when (v.type_code = 'B') THEN 'Purchase Invoice' ELSE 'Sale Invoice' END AS 'Invoice Type', im.part_number AS 'Part Number', i.description AS 'Description',sc.name as 'Service Center Name', im.hsn_code AS 'HSN Code', i.qty AS 'Quantity', i.settle_qty as 'Settled Quantity', i.rate AS 'Rate', i.taxable_value AS 'Taxable Value', (i.cgst_tax_rate + i.igst_tax_rate + i.sgst_tax_rate) AS 'GST Rate', (i.cgst_tax_amount + i.igst_tax_amount + i.sgst_tax_amount) AS 'GST Tax Amount', i.total_amount AS 'Total Amount', v.type AS 'Type', entt_gst_dtl.gst_number AS 'From GST Number', entity_gst_details.gst_number AS 'To GST Number', v.sub_category AS 'Sub Category'";
            $where["im.entity_id"] = $entity_id;
            $where["im.entity_type"] = _247AROUND_PARTNER_STRING;
            $where["v.sub_category IN('".MSL_DEFECTIVE_RETURN."', '".IN_WARRANTY."', '".MSL_Credit_Note . "', '"  . MSL_Debit_Note . "', '"  . MSL."', '".MSL_SECURITY_AMOUNT."', '".MSL_NEW_PART_RETURN."' )"] = null;

            if (!empty($service_center_id)) {
                $where["v.vendor_partner_id"] = $service_center_id;
            }

            $spare_details = $this->inventory_model->get_service_centers_consumption_data($select, $where);

            $this->load->dbutil();
            $this->load->helper('file');

            $file_name = 'mwh_msl_details_data_' . date('j-M-Y-H-i-s') . ".csv";
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
        } else {
            $res['msg'] = 'Please select partner and SF';
        }

        echo json_encode($res);
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
    function invoice_details($invoice_id) {
        log_message('info', __METHOD__ . ' Processing...');
        $this->miscelleneous->load_nav_header();
        $data = array();
        if(!empty($invoice_id)){
            $select = "v.invoice_id, v.create_date, case when (v.type_code = 'B') THEN 'Purchase Invoice' ELSE 'Sale Invoice' END AS type_code, im.part_number, im.description, im.hsn_code, i.qty, i.rate, i.taxable_value, (i.cgst_tax_rate + i.igst_tax_rate + i.sgst_tax_rate) AS gst_rate, (i.cgst_tax_amount + i.igst_tax_amount + i.sgst_tax_amount) AS gst_tax_amount, i.total_amount, v.type, entt_gst_dtl.gst_number as from_gst, entity_gst_details.gst_number as to_gst, v.sub_category";
            $post['where'] = array("v.invoice_id" => $invoice_id);
            $post['length'] = 100000;
            $post['start'] = 0;
            $data['invoice_details'] = $this->inventory_model->get_service_centers_consumption_list($post, $select , true);
        } else {
            $data['invoice_details'] = array();
        }
                      
        $this->load->view("employee/show_invoice_list", $data);
       
    }
    
    /*
     *  @desc : This function is used to get courier service list.
     *  @param : void
     *  @return : void
     */
    function get_courier_service_list() {
        $data = $this->get_courier_service_list_data();
        $post = $data['post'];
        if (!empty($data['data'])) {
            $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $this->inventory_model->count_all_courier_service_list($post),
                "recordsFiltered" => $this->inventory_model->count_courier_service_list($post),
                "data" => $data['data'],
            );
        } else {
            $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => $data['data'],
            );
        }
        echo json_encode($output);
    }

    function get_courier_service_list_data() {
        $post = $this->get_post_data();
        $courier_flag = trim($this->input->post('courier_flag'));
        $data = array();
        if (!empty($courier_flag)) {
            $post['column_order'] = array();
            $post['column_search'] = array('courier_services.courier_name', 'courier_services.courier_code');
            $post['where'] = "";
            $select = "courier_services.id, courier_services.courier_name, courier_services.courier_code, courier_services.status, courier_services.create_date";
            $list = $this->inventory_model->get_courier_service_list($post, $select);
            $data = array();
            $no = $post['start'];
            foreach ($list as $courier_list) {
                $no++;
                $row = $this->get_courier_service_list_table($courier_list, $no);
                $data[] = $row;
            }
        }
        return array(
            'data' => $data,
            'post' => $post
        );
    }
    
    function get_courier_service_list_table($courier_list, $no) {
        $row = array();
        $json_data = json_encode($courier_list);
        $row[] = $no;
        $row[] = $courier_list->courier_name;
        $row[] = $courier_list->courier_code;
        $row[] = date('d-F-Y', strtotime($courier_list->create_date));
        if ($courier_list->status == 1) {
         $update = "<a href='javascript:void(0)' class ='btn btn-primary' id='edit_courier_service' data-id='$json_data' title='Edit Details'><i class = 'fa fa-edit'></i></a>";  
        } else {
         $update = "<a href='javascript:void(0)' class ='btn btn-primary' id='edit_courier_service' data-id='$json_data' style='cursor:not-allowed' title='Edit not allowed account deactivated'><i class = 'fa fa-edit'></i></a>";    
        }
        $row[] = $update;
        if( $courier_list->status == 1){
          $action = "<button type='button' class='btn btn-default' style='background-color: #d9534f; border-color: #fff; width: 90px; color: #fff;' id='manage_courier_status' data-id='$json_data'>Deactivate</button>"; 
        }else{
          $action ="<button type='button' class='btn btn-danger' style='background-color: #01903a; border-color: #fff; width: 90px; color: #fff;' id='manage_courier_status' data-id='$json_data'>Activate</button>";
        }
        $row[] = $action;
        
        return $row;
    }
       
     /*
     *  @desc : This function is used to get courier serviceable area list.
     *  @param : void
     *  @return : Json
     */

    function get_courier_serviceable_area_list() {
        $data = $this->get_courier_serviceable_area_list_data();
        $post = $data['post'];
        if (!empty($data['data'])) {
            $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $this->inventory_model->count_all_courier_serviceable_area_list($post),
                "recordsFiltered" => $this->inventory_model->count_courier_serviceable_area_list($post),
                "data" => $data['data'],
            );
        } else {
            $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => $data['data'],
            );
        }
        echo json_encode($output);
    }
    
    /*
     *  @desc : This function is used to draw courier serviceable area list in bulk.
     *  @param : void
     *  @return : void
     */
    function get_courier_serviceable_area_list_data() {
        $post = $this->get_post_data();
        $courier_flag = trim($this->input->post('courier_flag'));
        $data = array();
        if (!empty($courier_flag)) {
            $post['column_order'] = array();
            $post['column_search'] = array('courier_serviceable_area.courier_company_name', 'courier_serviceable_area.pincode');
            $post['where'] = "";
            $select = "courier_serviceable_area.id, courier_serviceable_area.courier_company_name, courier_serviceable_area.pincode, courier_serviceable_area.status, courier_serviceable_area.create_date";
            $list = $this->inventory_model->get_courier_serviceable_area_list($post, $select);
            $data = array();
            $no = $post['start'];
            foreach ($list as $courier_serviceable_area_list) {
                $no++;
                $row = $this->get_courier_serviceable_area_list_table($courier_serviceable_area_list, $no);
                $data[] = $row;
            }
        }
        return array(
            'data' => $data,
            'post' => $post
        );
    }
     /*
     *  @desc : This function is used to draw courier serviceable area list in single line item.
     *  @param : $serviceable_area
      * @param : $no
     *  @return : array
     */
    function get_courier_serviceable_area_list_table($serviceable_area, $no) {
        $row = array();
        $json_data = json_encode($serviceable_area);
        $row[] = $no;
        $row[] = $serviceable_area->courier_company_name;
        $row[] = $serviceable_area->pincode;
        $row[] = date('d-F-Y', strtotime($serviceable_area->create_date));
        if ($serviceable_area->status == 1) {
            $update = "<a href='javascript:void(0)' class ='btn btn-primary' id='edit_serviceable_area' data-id='$json_data' title='Edit Details'><i class = 'fa fa-edit'></i></a>";
        } else {
            $update = "<a href='javascript:void(0)' class ='btn btn-primary' id='edit_serviceable_area' data-id='$json_data' style='cursor:not-allowed' title='Edit not allowed account deactivated'><i class = 'fa fa-edit'></i></a>";
        }
        $row[] = $update;
        if ($serviceable_area->status == 1) {
            $action = "<button type='button' class='btn btn-default' style='background-color: #d9534f; border-color: #fff; width: 90px; color: #fff;' id='serviceable_area_status' data-id='$json_data'>Deactivate</button>";
        } else {
            $action = "<button type='button' class='btn btn-danger' style='background-color: #01903a; border-color: #fff; width: 90px; color: #fff;' id='serviceable_area_status' data-id='$json_data'>Activate</button>";
        }
        $row[] = $action;

        return $row;
    }
    
    /*
     *  @desc : This function is used to open the download courier invoice page 
     *  @param : void
     *  @return : void
     */
    function download_courier_invoice() {
        $this->checkUserSession();
        $this->miscelleneous->load_nav_header();
        $this->load->view("employee/courier_invoice_list");
    }
    
    /*
     *  @desc : This function is used to download the courier invoice data
     *  @param : void
     *  @return : void
     */
    function download_courier_invoice_data(){

        log_message('info', __METHOD__ . ' Processing...');

        $partner_id = $this->input->post('partner_id');
        $date_range = $this->input->post('date_range');
        if(!empty($date_range)){
           $date_array = explode('-', $date_range); 
        }
        
        if(isset($date_array[0])){
          $from_date = date("Y-m-d", strtotime($date_array[0]));  
        }
        
        if(isset($date_array[1])){
          $to_date = date("Y-m-d", strtotime($date_array[1].  "+1 days"));  
        }

        if (!empty($partner_id)) {
            if (!empty($from_date) && !empty($to_date)) {
                $where = array();
                $select = "courier_company_invoice_details.awb_number as 'Docket Number', courier_company_invoice_details.company_name as 'Docket Company Name', "
                        . "partners.public_name as 'Partner Name',billed_docket.invoice_id as 'Invoice No.', courier_company_invoice_details.sender_city as 'Sender City', courier_company_invoice_details.receiver_city as 'Receiver City',"
                        . "courier_company_invoice_details.box_count as 'No. Of Boxes', courier_company_invoice_details.billable_weight as 'Weight', courier_company_invoice_details.courier_charge as 'Courier Charge', billed_docket.basic_charge as 'Billed To Partner Courier Charges', courier_company_invoice_details.courier_invoice_file as 'Courier Receipt Link'";
                if ($partner_id != 'all') {
                    $where['billed_docket.entity_id'] = $partner_id;
                }
                
                $where['billed_docket.create_date >= "' . $from_date . '" AND billed_docket.create_date <= "' . $to_date . '"'] = NULL;
                
                $courier_invoice_details = $this->inventory_model->get_courier_invoice_data($select, $where);
                $this->load->dbutil();
                $this->load->helper('file');

                $file_name = 'courier_invoice_data_' . date('j-M-Y-H-i-s') . ".csv";
                $delimiter = ",";
                $newline = "\r\n";
                $new_report = $this->dbutil->csv_from_result($courier_invoice_details, $delimiter, $newline);
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
            } else {
                $res['msg'] = 'Please Select Date Range';
            }
        } else {
            $res['msg'] = 'Please Select Partner';
        }

        echo json_encode($res);
    }
    /**
     * @Desc: This function is used for view page of bulk warrenty checker in partner panel
     * @params: void
     * @return: view
     * @author Ghanshyam
     * @date : 24-02-2020
     */
    function bulk_warranty_checker() {
        $this->check_PartnerSession();
        $warrentyStatus_pre = array();
        $warrentyStatus = array();
        $errormessage = '';
        $uploadSuccess = 0;
        if (!empty($_FILES)) {
            $file_status = $this->get_upload_file_type_partner();
            $data = array();
            if (!empty($file_status['file_ext'])) {
                if ($file_status['file_name_lenth']) {
                    if ($file_status['status']) {
                        $data = $this->read_upload_file_header_partner($file_status);
                        $data['post_data'] = $this->input->post();
                        $sheetUniqueRowData = array();
                        $msg = "";
                        //column which must be present in the  uploaded file
                        $header_column_need_to_be_present = array('booking_id');
                        //check if required column is present in upload file header
                        $is_all_header_present = array_diff(array_filter($data['header_data']), $header_column_need_to_be_present);
                        if (empty($is_all_header_present)) {
                            $check_header['status'] = true;
                        } else {
                            $check_header['status'] = false;
                        }
                        $arrBookings = array();
                        $bookingID = array();
                        if ($check_header['status']) {
                            if ($data['highest_row'] > 1) {
                                for ($row = 2, $i = 0; $row <= $data['highest_row']; $row++, $i++) {
                                    $rowData_array = $data['sheet']->rangeToArray('A' . $row . ':' . $data['highest_column'] . $row, NULL, TRUE, FALSE);
                                    $sanitizes_row_data = array_map('trim', $rowData_array[0]);
                                    if (!empty(array_filter($sanitizes_row_data))) {
                                        $rowData = array_combine($data['header_data'], $rowData_array[0]);
                                        $bookingID[] = trim($rowData['booking_id']);
                                    }
                                }
                                $uploadSuccess = 1;
                            } else {
                                $errormessage = "Empty File";
                            }
                        } else {
                            $errormessage = "Uploaded File format not matches with required file format";
                        }
                        if (!empty($bookingID)) {
                            $bookingID_chunks = array_chunk($bookingID, 50); // Divide Bookings in group of 50 to get warranty specific Data
                            $arrBookings = array();
                            $arrWarrantyData = array();
                            $arrModelWiseWarrantyData = array();
                            foreach($bookingID_chunks as $key => $booking_chunks){
                                $arrBookings_chunk = $this->warranty_utilities->get_warranty_specific_data_of_bookings($booking_chunks);
                                $arrWarrantyData_chunk = $this->warranty_utilities->get_warranty_data($arrBookings_chunk, true);
                                $arrModelWiseWarrantyData_chunk = $this->warranty_utilities->get_model_wise_warranty_data($arrWarrantyData_chunk);

                                $arrBookings = array_merge($arrBookings_chunk,$arrBookings);
                                $arrWarrantyData = array_merge($arrWarrantyData_chunk,$arrWarrantyData);
                                $arrModelWiseWarrantyData = array_merge($arrModelWiseWarrantyData_chunk,$arrModelWiseWarrantyData);
                            }
                            foreach ($arrBookings as $key => $value) {
                                if (!empty($arrModelWiseWarrantyData[$value['model_number']])) {
                                    $value = $this->warranty_utilities->map_warranty_period_to_booking($value, $arrModelWiseWarrantyData[$value['model_number']]);
                                }
                                $warrentyStatus_pre['warrenty_status'][$value['booking_id']] = $this->warranty_utilities->get_bookings_warranty_status(array($value))[0];
                            }
                        }
                        else{
                            if ($uploadSuccess) {
                                $uploadSuccess = 0;
                                $errormessage = "Empty File";
                            }
                        }
                    }
                }
            } else {
                $errormessage = "Invalid file type.";
            }
        }
        $partner_id = $this->session->userdata('partner_id');
        if ($uploadSuccess == 1 && !empty($bookingID)) {
            foreach ($bookingID as $key => $value) {

                $where = array('booking_id' => trim($value), 'partner_id' => $partner_id);
                $validBooking = $this->booking_model->get_bookings_count_by_any('booking_id', $where);
                if (!empty($validBooking)) {
                    if (!empty($warrentyStatus_pre['warrenty_status'][$value])) {
                        $warrentyStatus['warrenty_status'][$value] = $warrentyStatus_pre['warrenty_status'][$value];
                    } else {
                        $warrentyStatus['warrenty_status'][$value] = "No Data Found";
                    }
                } else {
                    $warrentyStatus['warrenty_status'][$value] = "Booking not found.";
                }
            }
            $this->miscelleneous->update_file_uploads($data['file_name'], TMP_FOLDER . $data['file_name'], BULK_CHECK_WARRANTY_STATUS, FILE_UPLOAD_SUCCESS_STATUS, "", "partner", $partner_id);
        }
        if (!empty($data['highest_row'])) {
            if ($data['highest_row'] > 1) {
                $this->miscelleneous->update_file_uploads($data['file_name'], TMP_FOLDER . $data['file_name'], BULK_CHECK_WARRANTY_STATUS, FILE_UPLOAD_FAILED_STATUS, "", "partner", $partner_id);
            }
        }
        if (!empty($data['file_name'])) {
                $fileName = TMP_FOLDER . $data['file_name'];
                if (file_exists($fileName)) {
                    unlink($fileName);
                }
            }
        $this->miscelleneous->load_partner_nav_header();
        $this->load->view('partner/bulk_warranty_checker', array('warrentyStatus' => $warrentyStatus, 'partner_id' => $partner_id, 'errormessage' => $errormessage));
        $this->load->view('partner/partner_footer');
    }
    /**
     * @desc: This function is used to get the file type
     * @param void
     * @param $response array   //consist file temporary name, file extension and status(file type is correct or not)
     */
    private function get_upload_file_type_partner() {
        log_message('info', __FUNCTION__ . "=> getting upload file type");
        if (!empty($_FILES['file']['name']) && strlen($_FILES['file']['name']) <= 100) {
            if (!empty($_FILES['file']['name']) && $_FILES['file']['size'] > 0) {
                $pathinfo = pathinfo($_FILES["file"]["name"]);

                switch ($pathinfo['extension']) {
                    case 'xlsx':
                        $response['file_tmp_name'] = $_FILES['file']['tmp_name'];
                        $response['file_ext'] = 'Excel2007';
                        break;
                    case 'xls':
                        $response['file_tmp_name'] = $_FILES['file']['tmp_name'];
                        $response['file_ext'] = 'Excel5';
                        break;
                }

                $response['status'] = True;
                $response['file_name_lenth'] = True;
            } else {
                log_message('info', __FUNCTION__ . ' Empty File Uploaded');
                $response['status'] = False;
                $response['file_name_lenth'] = True;
            }
        } else {
            log_message('info', __FUNCTION__ . 'File Name Length Is Long');
            $response['status'] = False;
            $response['file_name_lenth'] = false;
        }

        return $response;
    }

    /**
     * @desc: This function is used to get the file header
     * @param $file array  //consist file temporary name, file extension and status(file type is correct or not)
     * @param $response array  //consist file name,sheet name(in case of excel),header details,sheet highest row and highest column
     */
    private function read_upload_file_header_partner($file) {
        log_message('info', __FUNCTION__ . "=> getting upload file header");
        try {
            $objReader = PHPExcel_IOFactory::createReader($file['file_ext']);
            $objPHPExcel = $objReader->load($file['file_tmp_name']);
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($file['file_tmp_name'], PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }

        $file_name = $_FILES["file"]["name"];
        move_uploaded_file($file['file_tmp_name'], TMP_FOLDER . $file_name);
        chmod(TMP_FOLDER . $file_name, 0777);
        //  Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestDataRow();
        $highestColumn = $sheet->getHighestDataColumn();
        $response['status'] = TRUE;
        //Validation for Empty File
        if ($highestRow <= 1) {
            log_message('info', __FUNCTION__ . ' Empty File Uploaded');
            $response['status'] = False;
        }

        $headings = $sheet->rangeToArray('A1:' . $highestColumn . 1, NULL, TRUE, FALSE);
        $headings_new = array();
        foreach ($headings as $heading) {
            $heading = str_replace(array("/", "(", ")", "."), "", $heading);
            array_push($headings_new, str_replace(array(" "), "_", $heading));
        }

        $headings_new1 = array_map('strtolower', $headings_new[0]);

        $response['file_name'] = $file_name;
        $response['header_data'] = $headings_new1;
        $response['sheet'] = $sheet;
        $response['highest_row'] = $highestRow;
        $response['highest_column'] = $highestColumn;
        return $response;
    }

    /**
     * @desc : Method is used to resend the part which was rejected by partner on delivery challan.
     * @author : Ankit Rajvanshi
     */
    function resend_delivery_on_challan() {
        $post_data = $this->input->post();
        $spare_id = $post_data['spare_id'];
        
        if(empty($spare_id)) {
            return false;
        }
        /* get spare part details of $spare_id */
        $spare_part_detail = $this->reusable_model->get_search_result_data('spare_parts_details', '*', ['id' => $spare_id], NULL, NULL, NULL, NULL, NULL)[0];
        $is_spare_consumed = $this->reusable_model->get_search_result_data('spare_consumption_status', '*', ['id' => $spare_part_detail['consumed_part_status_id']], NULL, NULL, NULL, NULL, NULL)[0]['is_consumed'];
        
        // If part consumed status should defective part otherwise ok part.
        $spare_status = DEFECTIVE_PARTS_SEND_TO_PARTNER_BY_WH;
        if(!empty($is_spare_consumed) && $is_spare_consumed == 1) {
            $spare_status = DEFECTIVE_PARTS_SEND_TO_PARTNER_BY_WH;
        } else {
            $spare_status = OK_PARTS_SEND_TO_PARTNER_BY_WH;
        }
        
        /**
         * Update spare parts.
         */
        $this->service_centers_model->update_spare_parts(
            array('id' => $spare_id), 
            array(
                'status' => $spare_status,
                'remarks_defective_part_by_partner' => NULL,
                'defective_part_rejected_by_partner'=> 0,
            )
        );
        
        /**
         * log this change in spare tracking history
         */
        $tracking_details = array(
            'spare_id' => $spare_id, 
            'action' => $spare_status, 
            'remarks' => $spare_status, 
        );
        if(!empty($this->session->userdata('warehouse_id'))) {
            $tracking_details['agent_id'] = $this->session->userdata('id');
            $tracking_details['entity_id'] = _247AROUND;
            $tracking_details['entity_type'] = _247AROUND_EMPLOYEE_STRING;
        } else { 
            $tracking_details['agent_id'] = $this->session->userdata('service_center_agent_id');
            $tracking_details['entity_id'] = $this->session->userdata('service_center_id');
            $tracking_details['entity_type'] = _247AROUND_SF_STRING;
        }
        $this->service_centers_model->insert_spare_tracking_details($tracking_details);

        /**
         * Log this change in booking state change & update booking internal status.
         */
        $actor = ACTOR_NOT_DEFINE;
        $next_action = NEXT_ACTION_NOT_DEFINE;
                    
        // fetch record from booking details of $booking_id.
        $booking_id = $spare_part_detail['booking_id'];
        $partner_id = $spare_part_detail['partner_id'];
        $booking_details = $this->booking_model->get_booking_details('*',['booking_id' => $booking_id])[0];

        $is_exist = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id, spare_parts_details.status", array('spare_parts_details.booking_id' => $booking_id, 'spare_parts_details.defective_part_required' => 1, "status IN  (
                        '" . DEFECTIVE_PARTS_RECEIVED . "', '" . DEFECTIVE_PARTS_RECEIVED_BY_WAREHOUSE . "', '" .Ok_PARTS_RECEIVED_BY_WAREHOUSE . "', '" . Ok_PARTS_RECEIVED . "', '".OK_PARTS_SHIPPED."', '".DEFECTIVE_PARTS_SHIPPED."', '".DEFECTIVE_PARTS_REJECTED."', '".OK_PARTS_REJECTED."') " => NULL));

        if (empty($is_exist)) {
            $booking_internal_status = $spare_status;
        } else {
            $booking_internal_status = $is_exist[0]['status'];
        }

        // Change booking internal status if booking is completed.
        if($booking_details['current_status'] == _247AROUND_COMPLETED) {
            $booking = [];
            $booking['internal_status'] = $booking_internal_status;
            $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_COMPLETED, $booking['internal_status'], $partner_id, $booking_id);
            if (!empty($partner_status)) {
                $booking['partner_current_status'] = $partner_status[0];
                $booking['partner_internal_status'] = $partner_status[1];
                $actor = $booking['actor'] = $partner_status[2];
                $next_action = $booking['next_action'] = $partner_status[3];
            }
            $this->booking_model->update_booking($booking_id, $booking);
        }

        if(!empty($this->session->userdata('warehouse_id'))) {
            $this->notify->insert_state_change($booking_id, $spare_status, $spare_part_detail['status'], $spare_status, $this->session->userdata('id'), $this->session->userdata('employee_id'), $actor, $next_action, _247AROUND, NULL, $spare_id);
        } else {
            $this->notify->insert_state_change($booking_id, $spare_status, $spare_part_detail['status'], $spare_status, $this->session->userdata('service_center_agent_id'), $this->session->userdata('service_center_name'), $actor, $next_action, NULL, $this->session->userdata('service_center_id'), $spare_id);
        }
        
        return true;
    }
  /**
     *  @desc : This function is used to get the get_get_spare_parts
     *  @param : void()
     *  @return : JSON
     *  @Author : Abhishek Awasthi
     */
//    function get_no_return_parts_by_sf_list() {
//        $post = $this->get_post_data();
//        $search = $this->input->post('search');
//        
//        if(isset($_POST['partner_id']) && !empty($_POST['partner_id'])){
//            $partner_id = $this->input->post('partner_id') ;
//            $post['where']['booking_details.partner_id'] = $partner_id;
//        }
//       
//        $post['search'] = $search;
//        $post['select'] = "spare_parts_details.booking_id,defective_return_to_entity_id,spare_parts_details.shipped_inventory_id,spare_parts_details.id as sid,spare_parts_details.partner_id,spare_parts_details.shipped_quantity as shipping_quantity,spare_parts_details.parts_shipped,spare_parts_details.model_number_shipped, users.name, booking_primary_contact_no,service_centres.sc_code, service_centres.name as sc_name,"
//                . "partners.public_name as source, parts_requested, booking_details.request_type, spare_parts_details.id, spare_parts_details.part_warranty_status,"
//                . "spare_parts_details.defective_part_required, spare_parts_details.shipped_parts_type,spare_parts_details.is_micro_wh,status, inventory_master_list.part_number,inventory_master_list.part_name ";
//        $post['column_search'] = array('spare_parts_details.booking_id', 'partners.public_name', 'service_centres.name','parts_requested');
//        $post['search']['value'] = $post['search_value'];
//        $post['is_inventory'] = TRUE;
//        $post['where']['booking_details.current_status'] = _247AROUND_COMPLETED;
//        $post['where']['spare_parts_details.defective_part_required'] = 0;
//        $post['where']['spare_parts_details.part_warranty_status'] = 1;
//        $post['where']['spare_parts_details.status !="' . _247AROUND_CANCELLED . '"'] = NULL;
//        $post['where']['spare_parts_details.shipped_date IS NOT NULL'] = NULL;
//        $post['where']['spare_parts_details.defective_part_shipped_date IS NULL'] = NULL;
//        $post['where']['spare_parts_details.shipped_inventory_id IS NOT NULL'] = NULL;
//        $post['where_in']['spare_parts_details.is_micro_wh'] = array(1,2);
//        $list = $this->inventory_model->get_spare_parts_query($post);
//        $data = array();
//        $no = $post['start'];
//        foreach ($list as $spare) {
//            $no++;
//            $row = $this->get_get_spare_parts_query_list_table($spare, $no);
//            $data[] = $row;
//        }
//
//        $post['length'] = -1;
//        $output = array(
//            "draw" => $this->input->post('draw'),
//            "recordsTotal" => $this->inventory_model->count_spare_filtered($post),
//            "recordsFiltered" => $this->inventory_model->count_spare_filtered($post),
//            "data" => $data,
//        );
//
//        echo json_encode($output);
//    }


    /**
     *  @desc : This function is used to get the get_get_spare_parts_query_list_table
     *  @param : void()
     *  @return : Array
     *  @Author : Abhishek Awasthi
     */
//    function get_get_spare_parts_query_list_table($spare, $no) {
//
//
//        $row = array();
//        $row[] = $no;
//        $row[] = $spare->booking_id;
//        $row[] = $spare->sc_name;
//        $row[] = $spare->source;
//        $row[] = $spare->model_number_shipped;
//        $row[] = $spare->parts_shipped;
//        $row[] = $spare->part_number;
//        $row[] = $spare->shipped_parts_type;
//        $row[] = $spare->shipped_quantity;
//        $row[] = $spare->status;
//        $row[] = '<input id="selectbox" data-spare-id="' . $spare->sid . '" data-partner-id="'.$spare->partner_id.'" data-is-micro-wh="'.$spare->is_micro_wh.'" data-shipped-inventory-id="'.$spare->shipped_inventory_id.'" data-booking-id="'.$spare->booking_id.'" data-shipping-quantity="'.$spare->shipping_quantity.'" data-part-name="'.$spare->part_name.'" data-defective-return-to-entity-id="'.$spare->defective_return_to_entity_id.'" data-sc-code="'.$spare->sc_code.'" class="select_part"  type="checkbox"  />';
//        return $row;
//    }
 
    /**
     * @desc : Method is used to accept the parts which was rejected by partner on delivery challan.
     * After this action part will show on Delivery on challan send to partner tab
     * @author : Ankit Rajvanshi
     */
    function justify_delivery_on_challan() {
        $post_data = $this->input->post();
        $spare_id = $post_data['spare_id'];
        
        if(empty($spare_id)) {
            return false;
        }
        /* get spare part details of $spare_id */
        $spare_part_detail = $this->reusable_model->get_search_result_data('spare_parts_details', '*', ['id' => $spare_id], NULL, NULL, NULL, NULL, NULL)[0];
        $is_spare_consumed = $this->reusable_model->get_search_result_data('spare_consumption_status', '*', ['id' => $spare_part_detail['consumed_part_status_id']], NULL, NULL, NULL, NULL, NULL)[0]['is_consumed'];
        
        // If part consumed status should defective part otherwise ok part.
        $spare_status = DEFECTIVE_PARTS_RECEIVED_BY_WAREHOUSE;
        if(!empty($is_spare_consumed) && $is_spare_consumed == 1) {
            $spare_status = DEFECTIVE_PARTS_RECEIVED_BY_WAREHOUSE;
        } else {
            $spare_status = Ok_PARTS_RECEIVED_BY_WAREHOUSE;
        }
        
        /**
         * Update spare parts.
         */
        $this->service_centers_model->update_spare_parts(
            array('id' => $spare_id), 
            array(
                'status' => $spare_status,
                'wh_to_partner_defective_shipped_date' => NULL
            )
        );
        
        /**
         * log this change in spare tracking history
         */
        $tracking_details = array(
            'spare_id' => $spare_id, 
            'action' => $spare_status, 
            'remarks' => $spare_status, 
        );
        if(!empty($this->session->userdata('warehouse_id'))) {
            $tracking_details['agent_id'] = $this->session->userdata('id');
            $tracking_details['entity_id'] = _247AROUND;
            $tracking_details['entity_type'] = _247AROUND_EMPLOYEE_STRING;
        } else { 
            $tracking_details['agent_id'] = $this->session->userdata('service_center_agent_id');
            $tracking_details['entity_id'] = $this->session->userdata('service_center_id');
            $tracking_details['entity_type'] = _247AROUND_SF_STRING;
        }
        $this->service_centers_model->insert_spare_tracking_details($tracking_details);
        
        /**
         * Log this change in booking state change & update booking internal status.
         */
        $actor = ACTOR_NOT_DEFINE;
        $next_action = NEXT_ACTION_NOT_DEFINE;
                    
        // fetch record from booking details of $booking_id.
        $booking_id = $spare_part_detail['booking_id'];
        $partner_id = $spare_part_detail['partner_id'];
        $booking_details = $this->booking_model->get_booking_details('*',['booking_id' => $booking_id])[0];

        $is_exist = $this->partner_model->get_spare_parts_by_any("spare_parts_details.id, spare_parts_details.status", array('spare_parts_details.booking_id' => $booking_id, 'spare_parts_details.defective_part_required' => 1, "status IN  (
                        '" . DEFECTIVE_PARTS_RECEIVED . "', '" . DEFECTIVE_PARTS_RECEIVED_BY_WAREHOUSE . "', '" .Ok_PARTS_RECEIVED_BY_WAREHOUSE . "', '" . Ok_PARTS_RECEIVED . "', '".OK_PARTS_SHIPPED."', '".DEFECTIVE_PARTS_SHIPPED."', '".DEFECTIVE_PARTS_REJECTED."', '".OK_PARTS_REJECTED."') " => NULL));

        if (empty($is_exist)) {
            $booking_internal_status = $spare_status;
        } else {
            $booking_internal_status = $is_exist[0]['status'];
        }

        // Change booking internal status if booking is completed.
        if($booking_details['current_status'] == _247AROUND_COMPLETED) {
            $booking = [];
            $booking['internal_status'] = $booking_internal_status;
            $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_COMPLETED, $booking['internal_status'], $partner_id, $booking_id);
            if (!empty($partner_status)) {
                $booking['partner_current_status'] = $partner_status[0];
                $booking['partner_internal_status'] = $partner_status[1];
                $actor = $booking['actor'] = $partner_status[2];
                $next_action = $booking['next_action'] = $partner_status[3];
            }
            $this->booking_model->update_booking($booking_id, $booking);
        }
        
        if(!empty($this->session->userdata('warehouse_id'))) {
            $this->notify->insert_state_change($booking_id, $spare_status, $spare_part_detail['status'], $spare_status, $this->session->userdata('id'), $this->session->userdata('employee_id'), $actor, $next_action, _247AROUND, NULL, $spare_id);
        } else {
            $this->notify->insert_state_change($booking_id, $spare_status, $spare_part_detail['status'], $spare_status, $this->session->userdata('service_center_agent_id'), $this->session->userdata('service_center_name'), $actor, $next_action, NULL, $this->session->userdata('service_center_id'), $spare_id);
        }
        
        return true;
    }
 


  /**
     *  @desc : This function is used to get the view of table for non return part from SF
     *  @param : void()
     *  @return : View
     *  @Author : Abhishek Awasthi
     */

    function show_non_return_part(){

        $this->miscelleneous->load_nav_header();
        $data['saas'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view("employee/show_non_return_part_sf");

    }


    function get_no_return_parts_by_sf_list() {
        $post = $this->get_post_data();
        $post['select'] = "spare_parts_details.booking_id,spare_parts_details.id as sid,spare_parts_details.partner_id,spare_parts_details.shipped_quantity,spare_parts_details.parts_shipped,spare_parts_details.model_number_shipped, users.name, booking_primary_contact_no, service_centres.name as sc_name,"
                . "partners.public_name as source, parts_requested, booking_details.request_type, spare_parts_details.id, spare_parts_details.part_warranty_status,"
                . "spare_parts_details.defective_part_required, spare_parts_details.shipped_parts_type,spare_parts_details.is_micro_wh,status, inventory_master_list.part_number ";
        $post['column_search'] = array('spare_parts_details.booking_id', 'partners.public_name', 'service_centres.name','parts_requested');
        $post['search']['value'] = $post['search_value'];
        $post['is_inventory'] = TRUE;
        $post['where']['booking_details.current_status'] = _247AROUND_COMPLETED;

        $post['where']['spare_parts_details.defective_part_required'] = 0;
        $post['where']['spare_parts_details.part_warranty_status'] = 1;
        $post['where']['spare_parts_details.status !="' . _247AROUND_CANCELLED . '"'] = NULL;
        $post['where']['spare_parts_details.shipped_date IS NOT NULL'] = NULL;
        $post['where']['spare_parts_details.defective_part_shipped_date IS NULL'] = NULL;
        $post['where']['spare_parts_details.shipped_inventory_id IS NOT NULL'] = NULL;
        $post['where_in']['spare_parts_details.is_micro_wh'] = array(1,2);

        $list = $this->inventory_model->get_spare_parts_query($post);


        $data = array();
        $no = $post['start'];
        foreach ($list as $spare) {
            $no++;
            $row = $this->get_get_spare_parts_query_list_table($spare, $no);
            $data[] = $row;
        }

        $post['length'] = -1;
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->inventory_model->count_spare_filtered($post),
            "recordsFiltered" => $this->inventory_model->count_spare_filtered($post),
            "data" => $data,
        );

        echo json_encode($output);
    }



    /**
     *  @desc : This function is used to search inventory stocks on warehouse(as Micro-warehouse,central warehouse).
     *  @param : void
     *  @return : void
     */
    function warehouse_inventory_stock(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/wareouse_inventory_stock_list_part_number');
    }  
    /**
     *  @desc : This function is used to show view file of defective part Dashboard
     *  @param : void
     *  @return : View
     * @Author: Ghanshyam
     */
    function defective_spare_dashboard() {
        $data['employee_rm'] = $this->employee_model->get_rm_details();
        $data['state'] = $this->employee_model->get_states();
        $data['error'] = $this->session->flashdata('error');
        $this->miscelleneous->load_nav_header();
        $partner_not_like = '';
        $partnerType = '';
        if (!$partnerType) {
            $partner_not_like = INTERNALTYPE;
            $partnerType = array(OEM, EXTWARRANTYPROVIDERTYPE, ECOMMERCETYPE);
        }
        $partnerWhere['partners.is_active'] = 1;
        $data['partner_list'] = $this->partner_model->getpartner_data('distinct partners.id,partners.public_name', $partnerWhere, "", null, 1, '');
        //$data['partner_list'] = $query;
        $this->load->view('employee/defective_spare_dashboard', $data);
    }

    /**
     *  @desc : This function is used to show view file of defective part Dashboard
     *  @param : Partner ID
     *  @return : JSON Array
     * @Author: Ghanshyam
     */
    function defective_spare_dashboard_process_record() {
        $post_data = $this->input->post();
        $partner_id_array = $post_data['partner_id'];
        if (!empty($partner_id_array)) {
            /*
             * Initialte all variables grand total count of all partner as 0
             */
            $grandtotal_out_tat_c_n_c_count = 0;
            $grandtotal_out_tat_c_n_c_amount = 0;
            $grandtotal_out_tat_p_n_r_count = 0;
            $grandtotal_out_tat_p_n_r_amount = 0;
            $grandtotal_in_tat_c_n_c_count = 0;
            $grandtotal_in_tat_c_n_c_amount = 0;
            $grandtotal_in_tat_p_n_r_count = 0;
            $grandtotal_in_tat_p_n_r_amount = 0;
            $grand_total_part_count = 0;
            $grand_total_part_amount = 0;
            $grand_intransit_part_count = 0;
            $grand_intransit_part_amount = 0;
            $row_count = 0;

            foreach ($partner_id_array as $key => $partner_id) {
                $post['length'] = -1;
                $array_partner_detail = array();
                $total_Inward = 0;
                $total_Outward = 0;
                $difference = 0;
                $saleout_warranty = 0;
                /*
                 * Get total Inward (Purchased) and Outward(Sold) Iventory Stock List
                 */
                $partnerWhere['partners.id'] = $partner_id;
                $partner_details = $this->partner_model->getpartner_data('distinct partners.id,partners.public_name', $partnerWhere, "", null, 1, '');
                $publicName = $partner_details[0]['public_name'];
                $select = "invoice_details.id, case when (type_code = 'B') THEN 'purchase_invoice' ELSE 'sale_invoice' END AS 'invoice_type', total_amount,invoice_details.taxable_value";
                $where = array("sub_category IN ('" . MSL_DEFECTIVE_RETURN . "', '" .MSL_Credit_Note . "', '"  . MSL_Debit_Note . "', '"  . IN_WARRANTY . "', '" . MSL . "', '" . MSL_NEW_PART_RETURN . "')" => NULL, "vendor_partner_invoices.vendor_partner_id" => $partner_id);
                $post['column_search'] = array('invoice_details.invoice_id', 'invoice_details.description', 'entity_gst_details.gst_number', 'part_number');
                $list = $this->inventory_model->get_inventory_ledger_details_data_view($select, $where, $post);

                $purchasecount = 0;
                $salecount = 0;
                foreach ($list as $key => $value) {
                   
                    $invoice_type = $value->invoice_type;
                    $total_amount = $value->taxable_value;
                    if ($invoice_type == 'purchase_invoice') {
                        $total_Inward = $total_Inward + $total_amount;
                        $purchasecount = $purchasecount + 1;
                    }
                    if ($invoice_type == 'sale_invoice') {
                        $total_Outward = $total_Outward + $total_amount;
                        $salecount = $salecount + 1;
                    }
                }
                /*
                 * Get warehouse fresh stock amount
                 */
                $select = "service_centres.name AS Warehouse, partners.public_name AS 'Partner', inventory_master_list.part_number AS 'Part Number', inventory_master_list.part_name AS 'Part Name', inventory_stocks.stock AS Stock, inventory_master_list.price AS 'Basic Price', inventory_master_list.gst_rate as 'GST Rate'";
                $data_where['where'] = array("service_centres.is_wh" => 1, "inventory_stocks.entity_type" => _247AROUND_SF_STRING, "inventory_master_list.inventory_id NOT IN (1,2)" => NULL);
                $data_where['where']['inventory_master_list.entity_id'] = $partner_id;
                $data_where['where']['inventory_master_list.entity_type'] = _247AROUND_PARTNER_STRING;
                $data_where['is_access_to_sf_price'] = 1;

                $bom_details = $this->inventory_model->get_warehouse_stocks($data_where, $select);

                $whfreshStock_array = $bom_details->result_array();
                $whfreshStock_amount = 0;
                foreach ($whfreshStock_array as $key => $value) {
                    $whfreshStock_amount = $whfreshStock_amount + $value['Stock'] * $value['Basic Price'];
                }
                /*
                 * Get Micro warehouse fresh stock
                 */
                $select = "service_centres.name AS Warehouse, partners.public_name AS 'Partner', inventory_master_list.part_number AS 'Part Number', inventory_master_list.part_name AS 'Part Name', inventory_stocks.stock AS Stock, inventory_master_list.price AS 'Basic Price', inventory_master_list.gst_rate as 'GST Rate'";
                $data_where_mi['where'] = array("service_centres.is_micro_wh" => 1, "inventory_stocks.entity_type" => _247AROUND_SF_STRING, "inventory_master_list.inventory_id NOT IN (1,2)" => NULL);
                $data_where_mi['where']['inventory_master_list.entity_id'] = $partner_id;
                $data_where_mi['where']['inventory_master_list.entity_type'] = _247AROUND_PARTNER_STRING;
                $data_where_mi['is_access_to_sf_price'] = 1;

                $bom_details = $this->inventory_model->get_warehouse_stocks($data_where_mi, $select);

                $whfreshStock_array = $bom_details->result_array();
                $whfreshStock_amount_micro = 0;
                foreach ($whfreshStock_array as $key => $value) {
                    $whfreshStock_amount_micro = $whfreshStock_amount_micro + $value['Stock'] * $value['Basic Price'];
                }
                /*
                 * Below Four values fetched from Spare Consolidation Report Data (With help of spare status and Partner Level Status 2)
                 *
                 * Defective / Ok Part @ Warehouse
                 * Intransit Defective / ok Part Count and Amount
                 * Defective Pending/Rescheduled (In TAT, Out TAT)
                 * Defective Complete/Cancelled (In TAT, Out TAT)
                 *
                 * TAT Period: within 60 days(In TAT)
                 * TAT Period: More than 60 days Out tat
                 * $array_defective_item_at_warehouse => Defective / OK Item @ wharehouse
                 * $in_transit_item_array: Intransit Defective / OK Item
                 * $completed_cancelled_array (Partner level status 2): Defective / ok Item Competed or Cancelled
                 * $pending_rescheduled_array (Partner Level Status2): Defective / ok Item Pending or Rescheduled
                 */

                $defective_amount_at_wharehouse = 0;
                $in_transit_part_count = 0;
                $in_transit_part_amount = 0;

                $out_tat_part_completed_cancelled_count = 0;
                $out_tat_part_completed_cancelled_amount = 0;
                $out_tat_part_pending_rescheduled_count = 0;
                $out_tat_part_pending_rescheduled_amount = 0;

                $in_tat_part_completed_cancelled_count = 0;
                $in_tat_part_completed_cancelled_amount = 0;
                $in_tat_part_pending_rescheduled_count = 0;
                $in_tat_part_pending_rescheduled_amount = 0;
                $total_part_count = 0;
                $total_part_amount = 0;

                $select = 'SUM(spare_parts_details.sell_price)  as total_sell_price';
                $spare_parts_details = $this->partner_model->get_spare_parts_by_any($select, array('booking_details.partner_id'=>$partner_id,'spare_parts_details.sell_invoice_id is not null'=>NULL,'spare_parts_details.reverse_sale_invoice_id is  null'=>NULL),true);
                
                if(!empty($spare_parts_details)){
                    $saleout_warranty = $spare_parts_details[0]['total_sell_price'];
                }

                $array_defective_item_at_warehouse = array(strtoupper(DEFECTIVE_PARTS_RECEIVED_BY_WAREHOUSE), strtoupper(Ok_PARTS_RECEIVED_BY_WAREHOUSE));

                $in_transit_item_array = array(strtoupper(DEFECTIVE_PARTS_SHIPPED), strtoupper(OK_PARTS_SHIPPED), strtoupper(DAMAGE_PARTS_SHIPPED));

                $in_transit_item_array1 = array(strtoupper(DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE), strtoupper(DEFECTIVE_PARTS_PENDING), strtoupper(OK_PART_TO_BE_SHIPPED), strtoupper(SPARE_DELIVERED_TO_SF), strtoupper(OK_PARTS_REJECTED_BY_WAREHOUSE), strtoupper(SPARE_OOW_SHIPPED),strtoupper(SPARE_PARTS_SHIPPED),strtoupper(SPARE_PARTS_SHIPPED_BY_WAREHOUSE));

                $completed_cancelled_array = array(_247AROUND_CANCELLED, 'Booking Completed - Defective Part Shipped By SF', 'Booking Completed - Defective Part To Be Shipped By SF', SPARE_PARTS_CANCELLED, 'Part Lost', 'Booking Completed - Ok Part To Be Shipped By SF', 'Booking Completed - Defective Part Rejected By Partner', 'Booking Completed By Service Centre', _247AROUND_COMPLETED, 'Cancelled - Customer not reachable / Customer not picked phone', NRN_APPROVED_BY_PARTNER, BOOKING_COMPLETED_BY_ENGINEER_STATUS, 'Cancelled - Refused By Customer', 'Cancelled - Customer out of station', 'Booking Completed - Defective Part Received By Partner', 'Booking Completed - Defective Part Rejected By Warehouse', 'Booking Completed - Defective Part Received By Warehouse', BOOKING_CANCELLED_BY_ENGINEER_STATUS, 'Booking Cancelled By Service Centre', 'Cancelled (Customer Refused Service)', 'Booking In Progress - Spare Parts Cancelled');
                $completed_cancelled_array = array_map('strtoupper', $completed_cancelled_array);

                $pending_rescheduled_array = array(SPARE_PARTS_REQUESTED, 'Pending (Product Not Delivered To Customer)', SPARE_SHIPPED_BY_PARTNER, 'Spare Parts Requested By Service Centre', SPARE_DELIVERED_TO_SF, 'Booking In Progress', _247AROUND_RESCHEDULED, CUSTOMER_NOT_REACHABLE, SPARE_SHIPPED_TO_WAREHOUSE, 'Spare Parts Requested By Service Centre - Pending on Approval', 'Spare parts not received by SF', ESTIMATE_APPROVED_BY_CUSTOMER, 'Invoice not available', 'Booking In Progress - Service Centre Assigned', 'Booking In Progress - Engineer On Route', 'Booking In Progress - Service Center Reassigned', CUSTOMER_ASK_TO_RESCHEDULE, _247AROUND_PENDING, WAREHOUSE_ACKNOWLEDGED_TO_RECEIVE_PARTS, _247AROUND__SCHEDULED, 'Pending (Customer Not Responding)', 'Rescheduled - Upcountry Booking');
                $pending_rescheduled_array = array_map('strtoupper', $pending_rescheduled_array);


                $select = "spare_parts_details.id as spare_id, booking_details.partner_current_status as 'Partner Status Level 1', booking_details.partner_internal_status as 'Partner Status Level 2', spare_parts_details.status as 'Spare Status', "
                        . "datediff(CURRENT_DATE, spare_parts_details.shipped_date) as 'Spare Shipped Age', challan_approx_value As 'Parts Charge'";

                $where_consolidate_report = array();
                $where_consolidate_report['booking_details.partner_id'] = $partner_id;
                $group_by_consolidate_report = "spare_parts_details.id";
                $spare_details = $this->inventory_model->get_spare_consolidated_data($select, $where_consolidate_report, $group_by_consolidate_report);
                $spare_details_array = $spare_details->result_array();

                $count_defective_at_warehouse = 0;

                foreach ($spare_details_array as $key => $value) {
                    if (in_array(strtoupper($value['Spare Status']), $array_defective_item_at_warehouse)) {
                        $defective_amount_at_wharehouse = $defective_amount_at_wharehouse + $value['Parts Charge'];
                        $count_defective_at_warehouse++;
                    }
                    if (in_array(strtoupper($value['Spare Status']), $in_transit_item_array)) {
                        $in_transit_part_amount = $in_transit_part_amount + $value['Parts Charge'];
                        $in_transit_part_count = $in_transit_part_count + 1;
                    }
                    if (in_array(strtoupper($value['Partner Status Level 2']), $completed_cancelled_array) && $value['Spare Shipped Age'] > 60 && in_array(strtoupper($value['Spare Status']), $in_transit_item_array1)) {
                        $out_tat_part_completed_cancelled_count = $out_tat_part_completed_cancelled_count + 1;
                        $out_tat_part_completed_cancelled_amount = $out_tat_part_completed_cancelled_amount + $value['Parts Charge'];
                    }
                    if (in_array(strtoupper($value['Partner Status Level 2']), $pending_rescheduled_array) && $value['Spare Shipped Age'] > 60 && in_array(strtoupper($value['Spare Status']), $in_transit_item_array1)) {
                        $out_tat_part_pending_rescheduled_count = $out_tat_part_pending_rescheduled_count + 1;
                        $out_tat_part_pending_rescheduled_amount = $out_tat_part_pending_rescheduled_amount + $value['Parts Charge'];
                    }

                    if (in_array(strtoupper($value['Partner Status Level 2']), $completed_cancelled_array) && $value['Spare Shipped Age'] <= 60 && in_array(strtoupper($value['Spare Status']), $in_transit_item_array1)) {
                        $in_tat_part_completed_cancelled_count = $in_tat_part_completed_cancelled_count + 1;
                        $in_tat_part_completed_cancelled_amount = $in_tat_part_completed_cancelled_amount + $value['Parts Charge'];
                    }
                    if (in_array(strtoupper($value['Partner Status Level 2']), $pending_rescheduled_array) && $value['Spare Shipped Age'] <= 60 && in_array(strtoupper($value['Spare Status']), $in_transit_item_array1)) {
                        $in_tat_part_pending_rescheduled_count = $in_tat_part_pending_rescheduled_count + 1;
                        $in_tat_part_pending_rescheduled_amount = $in_tat_part_pending_rescheduled_amount + $value['Parts Charge'];
                    }
                }



                $total_part_count = $out_tat_part_completed_cancelled_count + $out_tat_part_pending_rescheduled_count + $in_tat_part_completed_cancelled_count + $in_tat_part_pending_rescheduled_count;
                $total_part_amount = $out_tat_part_completed_cancelled_amount + $out_tat_part_pending_rescheduled_amount + $in_tat_part_completed_cancelled_amount + $in_tat_part_pending_rescheduled_amount;



                $difference = $total_Inward-$total_Outward-$defective_amount_at_wharehouse-$in_transit_part_amount-$total_part_amount-$whfreshStock_amount-$whfreshStock_amount_micro-$saleout_warranty;

                /*
                 * Change All Amount values to 2 decimal places
                 */
                $total_Inward = number_format((float) $total_Inward, 2, '.', '');
                $total_Outward = number_format((float) $total_Outward, 2, '.', '');
                $difference = number_format((float) $difference, 2, '.', '');
                $whfreshStock_amount = number_format((float) $whfreshStock_amount, 2, '.', '');
                $whfreshStock_amount_micro = number_format((float) $whfreshStock_amount_micro, 2, '.', '');
                $defective_amount_at_wharehouse = number_format((float) $defective_amount_at_wharehouse, 2, '.', '');
                $out_tat_part_completed_cancelled_amount = number_format((float) $out_tat_part_completed_cancelled_amount, 2, '.', '');
                $out_tat_part_pending_rescheduled_amount = number_format((float) $out_tat_part_pending_rescheduled_amount, 2, '.', '');
                $in_tat_part_completed_cancelled_amount = number_format((float) $in_tat_part_completed_cancelled_amount, 2, '.', '');
                $in_tat_part_pending_rescheduled_amount = number_format((float) $in_tat_part_pending_rescheduled_amount, 2, '.', '');
                $in_transit_part_amount = number_format((float) $in_transit_part_amount, 2, '.', '');
                $total_part_amount = number_format((float) $total_part_amount, 2, '.', '');

                $grandtotal_out_tat_c_n_c_count = $grandtotal_out_tat_c_n_c_count + $out_tat_part_completed_cancelled_count;
                $grandtotal_out_tat_c_n_c_amount = $grandtotal_out_tat_c_n_c_amount + $out_tat_part_completed_cancelled_amount;

                $grandtotal_out_tat_p_n_r_count = $grandtotal_out_tat_p_n_r_count + $out_tat_part_pending_rescheduled_count;
                $grandtotal_out_tat_p_n_r_amount = $grandtotal_out_tat_p_n_r_amount + $out_tat_part_pending_rescheduled_amount;

                $grandtotal_in_tat_c_n_c_count = $grandtotal_in_tat_c_n_c_count + $in_tat_part_completed_cancelled_count;
                $grandtotal_in_tat_c_n_c_amount = $grandtotal_in_tat_c_n_c_amount + $in_tat_part_completed_cancelled_amount;

                $grandtotal_in_tat_p_n_r_count = $grandtotal_in_tat_p_n_r_count + $in_tat_part_pending_rescheduled_count;
                $grandtotal_in_tat_p_n_r_amount = $grandtotal_in_tat_p_n_r_amount + $in_tat_part_pending_rescheduled_amount;

                $grand_total_part_count = $grand_total_part_count + $total_part_count;
                $grand_total_part_amount = $grand_total_part_amount + $total_part_amount;

                $grand_intransit_part_count = $grand_intransit_part_count + $in_transit_part_count;
                $grand_intransit_part_amount = $grand_intransit_part_amount + $in_transit_part_amount;

                if ($in_tat_part_completed_cancelled_amount + $in_tat_part_pending_rescheduled_amount > 0) {
                    $percentage = ($out_tat_part_completed_cancelled_amount + $out_tat_part_pending_rescheduled_amount) / ($in_tat_part_completed_cancelled_amount + $in_tat_part_pending_rescheduled_amount);
                    $percentage = number_format((float) $percentage, 2, '.', '');
                } else {
                    $percentage = '';
                }
                $array['data'][] = array(++$row_count, $publicName, $percentage, $total_Inward, $total_Outward, $whfreshStock_amount, $whfreshStock_amount_micro,$saleout_warranty, $defective_amount_at_wharehouse, $out_tat_part_completed_cancelled_count, $out_tat_part_completed_cancelled_amount, $out_tat_part_pending_rescheduled_count, $out_tat_part_pending_rescheduled_amount, $in_transit_part_count, $in_transit_part_amount, $in_tat_part_completed_cancelled_count, $in_tat_part_completed_cancelled_amount, $in_tat_part_pending_rescheduled_count, $in_tat_part_pending_rescheduled_amount, $total_part_count, $total_part_amount, $difference);
            }
            $array['data'][] = array('', 'Grand Total', '', '', '', '', '', '','', $grandtotal_out_tat_c_n_c_count, $grandtotal_out_tat_c_n_c_amount, $grandtotal_out_tat_p_n_r_count, $grandtotal_out_tat_p_n_r_amount, $grand_intransit_part_count, $grand_intransit_part_amount, $grandtotal_in_tat_c_n_c_count, $grandtotal_in_tat_c_n_c_amount, $grandtotal_in_tat_p_n_r_count, $grandtotal_in_tat_p_n_r_amount, $grand_total_part_count, $grand_total_part_amount, '');
        } else {
            $array['data'][] = array('', '', '', '', '', '', '', '', '', '','', '', '', '', '', '', '', '', '', '', '', '');
        }

        $array['draw'] = $_POST['draw'];
        $array['recordsTotal'] = count($array['data']);
        $array['recordsFiltered'] = count($array['data']);


        echo json_encode($array);
    }

    /**
     *  @desc : This function is used to get the get_get_spare_parts_query_list_table
     *  @param : void()
     *  @return : Array
     *  @Author : Abhishek Awasthi
     */
    function get_get_spare_parts_query_list_table($spare, $no) {


        $row = array();
        $row[] = $no;
        $row[] = $spare->booking_id;
        $row[] = $spare->sc_name;
        $row[] = $spare->source;
        $row[] = $spare->model_number_shipped;
        $row[] = $spare->parts_shipped;
        $row[] = $spare->part_number;
        $row[] = $spare->shipped_parts_type;
        $row[] = $spare->shipped_quantity;
        $row[] = $spare->status;
        $row[] = '<input id="selectbox"' . $spare->sid . '  class="select_part"  type="checkbox"  />';
        return $row;
    }
    
    /*
     *  @desc : This function is used to get spare part chages
     *  @param : void
     *  @return : $res array()
     */

    function get_spare_part_charges() {
        $entity_type = $this->input->post('entity_type');
        $entity_id = $this->input->post('entity_id');
        $inventory_id = $this->input->post('inventory_id');
        $data = array();
        if ($entity_type == _247AROUND_PARTNER_STRING) {
            if (!empty($inventory_id)) {
                $where = array("inventory_master_list.entity_id" => $entity_id, "inventory_master_list.entity_type" => $entity_type, "inventory_master_list.inventory_id" => $inventory_id);
                $inventory_details = $this->inventory_model->get_inventory_master_list_data('inventory_master_list.price as price,inventory_master_list.gst_rate', $where);
                if (!empty($inventory_details)) {
                    $data['spare_part_price'] = sprintf("%.2f", $inventory_details[0]['price'] * (1 + $inventory_details[0]['gst_rate'] / 100));
                }
            }
        }
        echo json_encode($data);
    }
    /**
     *  @desc : This function is used to count number of times canceled status of booking rejected by admin 
     *  @param : booking_id
     *  @return : JSON /status(sucess / error), count
     * @Author: Ghanshyam
     */
    function booking_cancelled_rejected_count($booking_id) {
        $where['old_state'] = SF_BOOKING_CANCELLED_STATUS; // Booking cancelled by SF
        $where['new_state'] = _247AROUND_CANCELED_REJECTED; // Csncelled status rejected by Admin
        $data = $this->booking_model->get_booking_state_change($booking_id, $where);
        $count_cancelled_rejected = 0;
        $status = '';
        if (!empty($data)) {
            $status = 'success';
            $count_cancelled_rejected = count($data);
        } else {
            $status = 'error';
        }
        $return_array['status'] = $status;
        $return_array['count'] = $count_cancelled_rejected;
        echo json_encode($return_array);
    }

    /**
     * @desc This function is used to create the view page to upload courier serviceable area file.
     * @param: null
     */
    function upload_courier_serviceable_area_file() {
        $data['courier_details'] = $this->inventory_model->get_courier_services('*');
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/upload_courier_serviceable_area',$data);
    }
    /**
     * @desc This function is used to download all undelivered docket
     */
    function download_undelivered_docket(){
        $select ="`awb_number` as 'Docket Number', `courier_invoice_id` as 'Courier Company Invoice ID', `company_name` as 'Courier Name', "
                . "`courier_charge` as 'Courier Charges', `actual_weight` As 'Actual Weight', `billable_weight` As 'Billable Weight', `courier_invoice_file` As 'Courier Invoice File', "
                . "`pickup_from` As 'Picku From', `box_count` as 'Large Box Count', `small_box_count` AS 'Small Box Count', `shippment_date` as 'Shippment Date', `courier_pod_file` as 'POD File',"
                . " `is_rto`, `rto_file`, `create_date` AS 'Docket Entry Date'";
        $where = array('delivered_date IS NULL' => NULL);
        $data = $this->inventory_model->get_courier_company_invoice_details($select, $where, array(), true);
        
        $this->load->dbutil();
        $this->load->helper('file');
        $file_name = 'courier_data_' . date('jMYHis') . ".csv";
        $delimiter = ",";
        $newline = "\r\n";
        $new_report = $this->dbutil->csv_from_result($data, $delimiter, $newline);
        write_file(TMP_FOLDER . $file_name, $new_report);
        if (file_exists(TMP_FOLDER . $file_name)) {
            ob_start();
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header("Content-Disposition: attachment; filename=\"$file_name\"");
            if(ob_get_length()>0) {
                ob_end_flush();
            }
            $res1 = 0;
            if(file_exists(TMP_FOLDER . $file_name)) {
                system(" chmod 777 " . TMP_FOLDER . $file_name . '.zip ', $res1);
                readfile(TMP_FOLDER . $file_name); 
            }
        } else {
            log_message('info', __FUNCTION__ . ' error in generating file ' . $file_name);
        }

        exit();
    }
    
    /*
     *  @desc : This function is used to get HSN Code Details
     *  @param : void
     *  @return : $res array()
     */ 
    
    
     function get_hsn_code_details() {
         
        
        $post = $this->get_post_data();
              
        if (!empty($this->input->post('satus'))) {
            
        
            $post[''] = array();
            $post['column_order'] = 'hsn_code_details.create_date';
            $post['column_search'] = array('hsn_code_details.hsn_code', 'hsn_code_details.gst_rate');
            
            $select = "hsn_code_details.id, hsn_code_details.hsn_code, hsn_code_details.status, hsn_code_details.gst_rate, hsn_code_details.service_id, hsn_code_details.create_date, services.services";
            $list = $this->inventory_model->get_hsncode_list($post, $select, true);
           
            $data = array();
            $no = $post['start'];
            
            foreach ($list as $hsncode_list) {
                $no++;
                $data[] = $this->get_hsn_code_details_table($hsncode_list, $no);
            }
            
            $post['length'] = -1;

            $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $this->inventory_model->count_all_hsncode_list($post),
                "recordsFiltered" => $this->inventory_model->count_filtered_hsncode_list($post),
                "data" => $data,
            );
            
        } else {
            $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                'stock' => 0,
                "data" => array(),
            );
        }
        echo json_encode($output);
    }
    
    /*
     * @desc: This function is used to create table body to table.
     * @param: Array
     * @return: Array
     */
    
    private function get_hsn_code_details_table($hsncode_list, $sn) {
        $row = array();

        $row[] = $sn;

        $row[] = '<span id="services_' . $hsncode_list->service_id . '">' . $hsncode_list->services . '</span>';
        $row[] = '<span id="type_' . $hsncode_list->id . '">' . $hsncode_list->hsn_code . '</span>';
        $row[] = '<span id="part_name_' . $hsncode_list->id . '" style="word-break: break-all;">' . $hsncode_list->gst_rate . '%</span>';
        $row[] = '<span id="part_number_' . $hsncode_list->id . '" style="word-break: break-all;">' . $hsncode_list->create_date . '</span>';
        $row[] = '<a class="btn btn-info" href="'.base_url() . 'employee/invoice/get_add_new_hsn_code/' . $hsncode_list->id.'"><i class="fa fa-edit" aria-hidden="true"></i></a>';
        if($hsncode_list->status == 1){
        $row[] = '<button type="button" class="btn btn-default" style="background-color: #d9534f; border-color: #fff; width: 90px; color: #fff;" id="' . $hsncode_list->id.'" onclick="process_to_manage_status(this.id)">Deactivate</button>';
        }else{
        $row[] = '<button type="button" class="btn btn-danger" style="background-color: #01903a; border-color: #fff; width: 90px; color: #fff;" href="#" id="' . $hsncode_list->id.'" onclick="process_to_manage_status(this.id)">Activate</button>';
        }
        return $row;
    }

}

