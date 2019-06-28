<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//For infinite memory
ini_set('memory_limit', '-1');
//3600 seconds = 60 minutes
ini_set('max_execution_time', 36000);

define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

class Upload_booking_file extends CI_Controller {

    Private $FilesData = array();
    Private $file_name = "";
    // Private $total_booking_inserted = 0;
    Private $total_booking_came_today = 0;
    Private $count_booking_updated = 0;
    Private $count_booking_not_updated = 0;

    function __Construct() {
        parent::__Construct();
        $this->load->helper(array('form', 'url'));
        $this->load->helper('download');

        $this->load->library('form_validation');
        $this->load->library('s3');
        $this->load->library('PHPReport');
        $this->load->library('notify');
        $this->load->library('partner_utilities');
        $this->load->library('booking_utilities');
        $this->load->library('miscelleneous');
        $this->load->library('table');

        $this->load->model('user_model');
        $this->load->model('booking_model');
        $this->load->model('partner_model');
        $this->load->model('vendor_model');
        $this->load->library('table');

        if (($this->session->userdata('loggedIn') == TRUE) && (($this->session->userdata('userType') == 'employee') || ($this->session->userdata('userType') == 'partner'))) {
            return TRUE;
        } else {
            redirect(base_url() . "employee/login");
        }
    }

    /**
     * @desc This is used to load Upload form
     */
    function upload_booking_files() {
        log_message('info', __FUNCTION__);
        $this->miscelleneous->load_nav_header();
        $this->load->view("employee/upload_booking_file");
    }

    /**
     * @desc This is used to process to upload form
     */
    function index() {
        log_message('info', __FUNCTION__);
        $this->form_validation->set_rules('file_type', 'File Type', 'required');
        $this->form_validation->set_rules('partner_id', 'Partner', 'required');
        if ($this->form_validation->run()) {
            $file_type = $this->input->post("file_type");
            $partner_id = $this->input->post("partner_id");

            if (!empty($_FILES['file']['name']) && $_FILES['file']['size'] > 0) {
                $pathinfo = pathinfo($_FILES["file"]["name"]);
                $this->file_name = $_FILES["file"]["name"];
                switch ($pathinfo['extension']) {
                    case 'xlsx':
                    case 'xls':
                        $data = $this->retrieve_data_from_excel($pathinfo['extension'], $file_type, $partner_id);
                        break;
                    case 'csv':
                        $data = $this->retrieve_data_from_csv_for_paytm_mall($partner_id);
                        break;
                }

                $count_booking_inserted = 0;

                if (!empty($data)) {
                    // SEND MAIl
                    $subject = $file_type . " data validated. File is under process";
                    $message = $this->file_name . " validation Pass. File is under process";
                    $this->send_mail_column($subject, $message, TRUE,FILE_VALIDATION_PASS);
                    foreach ($data as $value) {
                        log_message('info', __FUNCTION__ . " Data Found");
                        $this->FilesData = array();
                        $this->FilesData = $value;
                       
                        //Check whether order id exists or not
                        $partner_booking = $this->partner_model->get_order_id_for_partner($this->FilesData['partner_id'], $this->FilesData['order_id']);

                        //if order is not found
                        if (is_null($partner_booking)) {
                            // GET State, Taluk, District
                            $distict_details = $this->vendor_model->get_distict_details_from_india_pincode(trim($value['pincode']));

                            $this->FilesData['state'] = $distict_details['state'];
                            $this->FilesData['taluk'] = $distict_details['taluk'];
                            $this->FilesData['district'] = $distict_details['district'];
                            //Get User ID
                            $this->get_user_id($value);
                            // Set Appliance Brand
                            $this->FilesData['appliance_brand'] = isset($this->FilesData['appliance_data'][0]['brand']) ? $this->FilesData['appliance_data'][0]['brand'] : $this->FilesData['appliance_brand'];
                            $this->FilesData['appliance_brand'] = trim(str_replace("'", "", $this->FilesData['appliance_brand']));
                            if (!empty($this->FilesData['appliance_brand'])) {
                                //GET partner details. On the basis of service id, state, brand
                                $p_data = $this->miscelleneous->allot_partner_id_for_brand($this->FilesData['service_id'], $this->FilesData['state'], $this->FilesData['appliance_brand']);
                                
                                //if $p_data is false, then partner id is same as for which file upload is done
                                //else assign to new partner id
                                if (!empty($p_data)) {
                                    $this->FilesData['partner_id'] = $p_data['partner_id'];
                                    $this->FilesData['source'] = $p_data['source'];
                                }
                            }
                            
                            // Create Booking ID
                            $this->set_booking_id();

                            log_message('info', __FUNCTION__ . "=> File type: " .
                                    ", Order ID NOT found: " . $this->FilesData['order_id']);
                            
                            //if brand does not exist, it gets automatically to brands table as well
                            $this->check_brand();
                            
                            // Insert Booking Details
                            $status = $this->insert_booking_details();
                            if ($status) {
                                $unit = $this->insert_appliance_booking_unitDetails($file_type);
                                if ($unit) {
                                    if (isset($this->FilesData['sku'])) {
                                        $this->insert_sku_details();
                                    }
                                    $count_booking_inserted++;
                                    $this->insert_booking_in_partner_leads($this->FilesData);
                                    $this->notify->insert_state_change($this->FilesData['booking_id'], _247AROUND_FOLLOWUP, _247AROUND_NEW_QUERY, '', _247AROUND_DEFAULT_AGENT, 
                                            _247AROUND_DEFAULT_AGENT_NAME, ACTOR_FILE_UPLOAD_BOOKING_CREATE,NEXT_ACTION_FILE_UPLOAD_BOOKING_CREATE,_247AROUND);
                                } else {
                                    log_message('info', __FUNCTION__ . ' => ERROR: UNIT is not inserted: ' .
                                            print_r($this->FilesData, true));
                                }
                            } else {
                                log_message('info', __FUNCTION__ . ' => ERROR: Booking is not inserted: ' .
                                        print_r($this->FilesData, true));
                            }
                        } 
                        //If order id is found, then check whether partner is snapdeal or not
                        //if it is snapdeal, then proceed further to find file type
                        //if it is other partner, leave
                        else if ($partner_id == SNAPDEAL_ID) {
                            log_message('info', __FUNCTION__ . "=> File type: " . $file_type .
                                    ", Order ID found: " . $value['order_id']);
                            $status = $partner_booking['current_status'];
                            $int_status = $partner_booking['internal_status'];
                            switch ($file_type) {
                                case 'delivered':
                                    $this->order_id_exist_delivered_process($status, $partner_booking, $int_status, $file_type);
                                    break;
                                case 'shipped':
                                    $this->order_id_exist_shipped_process($partner_booking);
                                    break;
                            }
                        } else {
                            $this->count_booking_not_updated++;
                        }
                    }
                } 
                $row_data = array();
                $row_data['error']['total_booking_inserted'] = $count_booking_inserted;
                $row_data['error']['total_booking_came_today'] = $this->total_booking_came_today;
                $row_data['error']['count_booking_updated'] = $this->count_booking_updated;
                $row_data['error']['count_booking_not_updated'] = $this->count_booking_not_updated;

                if (isset($row_data['error'])) {
                    log_message('info', __FUNCTION__ . "=> File type: " . $file_type . " => Errors found, sending mail now");
                    $this->get_invalid_data($row_data['error'], $file_type, $this->file_name);
                } else {
                    log_message('info', __FUNCTION__ . "=> File type: " . $file_type . " => Wow, no errors found !!!");
                }
            } else {
                $this->upload_booking_files();
            }
        } else {
            $this->upload_booking_files();
        }
    }

    function insert_sku_details() {
        $check_sku = $this->booking_model->get_sku_transactions(array('order_id' => $this->FilesData['order_id']));
        if (empty($check_sku)) {
            $sku_details = array(
                "sku" => $this->FilesData['sku'],
                "order_id" => $this->FilesData['order_id'],
                "user_id" => $this->FilesData['user_id'],
                "final_price" => $this->FilesData['final_price']
            );

            $this->booking_model->insert_sku_transaction($sku_details);
        } else {
            log_message('info', __FUNCTION__ . " SKU Order Id Exist");
        }
    }

    function order_id_exist_shipped_process($partner_booking) {
        if (isset($this->FilesData['estimated_delivery_date'])) {
            $update_data['estimated_delivery_date'] = $this->FilesData['estimated_delivery_date'];
            $update_data['backup_estimated_delivery_date'] = $this->FilesData['backup_estimated_delivery_date'];
            $update_data['update_date'] = date("Y-m-d H:i:s");
            $this->booking_model->update_booking($partner_booking['booking_id'], $update_data);

            $$this->count_booking_updated++;

            unset($update_data);
        } else {
            $this->count_booking_not_updated++;
        }
        log_message('info', __FUNCTION__ . ' => Updated Partner Lead: ' . $partner_booking['booking_id']);
    }

    function order_id_exist_delivered_process($status, $partner_booking, $int_status, $file_type) {
        //If state is followup and booking date not empty, reset the date
        if ($status == "FollowUp" && $partner_booking['booking_date'] != '' &&
                $int_status == 'Missed_call_not_confirmed') {
            $update_data['delivery_date'] = $this->FilesData['delivery_date'];
            $update_data['backup_delivery_date'] = $this->FilesData['delivery_date'];
            $update_data['booking_date'] = '';
            $update_data['booking_timeslot'] = '';
            $update_data['update_date'] = date("Y-m-d H:i:s");

            $sms_count = 0;

            $category = isset($this->FilesData['appliance_data'][0]['category']) ? $this->FilesData['appliance_data'][0]['category'] : '';
            $capacity = isset($this->FilesData['appliance_data'][0]['capacity']) ? $this->FilesData['appliance_data'][0]['capacity'] : '';
            $brand = isset($this->FilesData['appliance_data'][0]['brand']) ? $this->FilesData['appliance_data'][0]['brand'] : $this->FilesData['appliance_brand'];

            $this->initialized_variable->fetch_partner_data($this->FilesData['partner_id']);

            if ($this->initialized_variable->get_partner_data()[0]['partner_type'] == OEM) {
                $prices = $this->partner_model->getPrices($partner_booking['service_id'], $category, $capacity, $this->FilesData['partner_id'], $this->FilesData['request_type'], $brand);
            } else {
                $prices = $this->partner_model->getPrices($partner_booking['service_id'], $category, $capacity, $this->FilesData['partner_id'], $this->FilesData['request_type'], "");
            }

            $is_price = array();
            if (!empty($prices)) {

                $is_price['customer_net_payable'] = $prices[0]['customer_net_payable'];
                $is_price['is_upcountry'] = $prices[0]['is_upcountry'];
            }

            $is_sms = $this->miscelleneous->check_upcountry($partner_booking, $this->FilesData['appliance'], $is_price, $file_type);
            if ($is_sms) {
                $sms_count = 1;
            } else {
                $update_data['internal_status'] = SF_UNAVAILABLE_SMS_NOT_SENT;

                log_message('info', __FUNCTION__ . ' =>  SMS not sent because of Vendor Unavailability for Booking ID: ' . $partner_booking['booking_id']);
            }

            $update_data['sms_count'] = $sms_count;

            $this->booking_model->update_booking($partner_booking['booking_id'], $update_data);
            $this->count_booking_updated = $this->count_booking_updated + 1;

            log_message('info', __FUNCTION__ . ' => Updated Partner Lead: ' . $partner_booking['booking_id']);

            unset($update_data);
        } else {
            $this->count_booking_not_updated++;
        }
    }

    function insert_appliance_booking_unitDetails($file_type) {
        log_message('info', "Entering: " . __METHOD__);
        $amount_due = 0;
        $is_price = array();

        $appliance_id = $this->insert_applianceDetails();

        $unit_details = array(
            "partner_id" => $this->FilesData['partner_id'],
            "service_id" => $this->FilesData['service_id'],
            "appliance_id" => $appliance_id,
            "partner_serial_number" => isset($this->FilesData['partner_serial_number']) ? $this->FilesData['partner_serial_number'] : NULL,
            "appliance_brand" => isset($this->FilesData['appliance_data'][0]['brand']) ? $this->FilesData['appliance_data'][0]['brand'] : $this->FilesData['appliance_brand'],
            "model_number" => $this->FilesData['model_number'],
            "booking_id" => $this->FilesData['booking_id'],
            "appliance_description" => $this->FilesData['appliance_description'],
            'purchase_date' => isset($this->FilesData['purchase_date']) ? $this->FilesData['purchase_date'] : date('Y-m-d'),
            'appliance_capacity' => isset($this->FilesData['appliance_data'][0]['capacity']) ? $this->FilesData['appliance_data'][0]['capacity'] : '',
            'appliance_category' => isset($this->FilesData['appliance_data'][0]['category']) ? $this->FilesData['appliance_data'][0]['category'] : ''
        );


        $this->initialized_variable->fetch_partner_data($this->FilesData['partner_id']);
        
        $prices = array();
        if ($this->initialized_variable->get_partner_data()[0]['partner_type'] == OEM) {
            //if partner type is OEM then send appliance brand in argument and get prices
            $prices = $this->partner_model->getPrices($unit_details['service_id'], $unit_details['appliance_category'], $unit_details['appliance_capacity'], $this->FilesData['partner_id'], $this->FilesData['price_tags'], $unit_details['appliance_brand']);
        } else {
            //if partner type is not OEM then dont send appliance brand in argument
            $prices = $this->partner_model->getPrices($unit_details['service_id'], $unit_details['appliance_category'], $unit_details['appliance_capacity'], $this->FilesData['partner_id'],  $this->FilesData['price_tags'], "");
        }

        //if price details are found in database
        if (!empty($prices)) {
            $unit_details['id'] = $prices[0]['id'];
            $unit_details['price_tags'] =  $this->FilesData['price_tags'];
            $unit_details['around_paid_basic_charges'] = $unit_details['around_net_payable'] = "0.00";
            $unit_details['partner_paid_basic_charges'] = $prices[0]['partner_net_payable'];
            $unit_details['partner_net_payable'] = $prices[0]['partner_net_payable'];
            $amount_due = $prices[0]['customer_net_payable'];
            $is_price['customer_net_payable'] = $prices[0]['customer_net_payable'];
            $is_price['is_upcountry'] = $prices[0]['is_upcountry'];

            $unit_id = $this->booking_model->insert_data_in_booking_unit_details($unit_details, $this->FilesData['state'], 0);
        } else {
            //if price details are not found
            $unit_id = $this->booking_model->addunitdetails($unit_details);
        }

        //check whether this is an upcountry booking or not
        if ($unit_id) {

            $is_sms = $this->miscelleneous->check_upcountry($this->FilesData, $this->FilesData['services'], $is_price, $file_type);
            //check whether sms is sent or not
            if (!$is_sms) {
                $booking['internal_status'] = SF_UNAVAILABLE_SMS_NOT_SENT;
                $booking['amount_due'] = $amount_due;
            } else {
                $booking['sms_count'] = 1;
                $booking['amount_due'] = $amount_due;
            }

            $this->booking_model->update_booking($this->FilesData['booking_id'], $booking);
        }
        
        return $unit_id;
    }

    function insert_applianceDetails() {
        log_message('info', "Entering: " . __METHOD__);
        // $appliance = array();
        $appliance = array(
            'user_id' => $this->FilesData['user_id'],
            'service_id' => $this->FilesData['service_id'],
            "brand" => isset($this->FilesData['appliance_data'][0]['brand']) ? $this->FilesData['appliance_data'][0]['brand'] : $this->FilesData['appliance_brand'],
            'model_number' => $this->FilesData['model_number'],
            'description' => $this->FilesData['appliance_description'],
            'purchase_date' => date('Y-m-d'),
            'last_service_date' => date('Y-m-d'),
            'tag' => $this->FilesData['appliance_brand'] . " " . $this->FilesData['services'],
            'capacity' => isset($this->FilesData['appliance_data'][0]['capacity']) ? $this->FilesData['appliance_data'][0]['capacity'] : '',
            'category' => isset($this->FilesData['appliance_data'][0]['category']) ? $this->FilesData['appliance_data'][0]['category'] : ''
        );

        return $this->booking_model->addappliance($appliance);
    }

    function insert_booking_details() {
        log_message('info', "Entering: " . __METHOD__);

        $booking = array(
            "booking_id" => $this->FilesData['booking_id'],
            "partner_id" => $this->FilesData['partner_id'],
            "source" => $this->FilesData['source'],
            "order_id" => $this->FilesData['order_id'],
            "type" => "Query",
            "user_id" => $this->FilesData['user_id'],
            "service_id" => $this->FilesData['service_id'],
            "quantity" => 1,
            "city" => $this->FilesData['city'],
            "state" => $this->FilesData['state'],
            "taluk" => $this->FilesData['taluk'],
            "district" => $this->FilesData['district'],
            "booking_date" => $this->FilesData['booking_date'],
            "booking_timeslot" => $this->FilesData['booking_timeslot'],
            "internal_status" => $this->FilesData['internal_status'],
            "partner_source" => $this->FilesData['partner_source'],
            "current_status" => _247AROUND_FOLLOWUP,
            "query_remarks" => isset($this->FilesData['query_remarks']) ? $this->FilesData['query_remarks'] : '',
            "booking_pincode" => $this->FilesData['pincode'],
            "delivery_date" => isset($this->FilesData['delivery_date']) ? $this->FilesData['delivery_date'] : '',
            "booking_address" => $this->FilesData['address'],
            "booking_primary_contact_no" => $this->FilesData["booking_primary_contact_no"],
            "booking_alternate_contact_no" => '',
            "request_type" => $this->FilesData['request_type'],
            "estimated_delivery_date" => isset($this->FilesData['estimated_delivery_date']) ? $this->FilesData['estimated_delivery_date'] : '',
            'backup_estimated_delivery_date' => isset($this->FilesData['backup_estimated_delivery_date']) ? $this->FilesData['backup_estimated_delivery_date'] : '',
            'backup_delivery_date' => isset($this->FilesData['backup_delivery_date']) ? $this->FilesData['backup_delivery_date'] : '',
            "create_date" => date("Y-m-d H:i:s"),
            "shipped_date" => $this->FilesData['shipped_date']
        );
        $this->FilesData['booking_pincode'] = $this->FilesData['pincode'];
        $partner_status = $this->booking_utilities->get_partner_status_mapping_data($booking['current_status'], $booking['internal_status'], $booking['partner_id'], $booking['booking_id']);
        if (!empty($partner_status)) {
            $booking['partner_current_status'] = $partner_status[0];
            $booking['partner_internal_status'] = $partner_status[1];
            $booking['actor'] = $partner_status[2];
            $booking['next_action'] = $partner_status[3];
        }

        $booking_details_id = $this->booking_model->addbooking($booking);
        if ($booking_details_id) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @desc If Appliance Brand is not exist then insert new brand
     * @return boolean
     */
    function check_brand() {
        if (!empty($this->FilesData['appliance_brand'])) {
            $where = array('service_id' => $this->FilesData['service_id'], 'brand_name' => trim($this->FilesData['appliance_brand']));
            $brand_id_array = $this->booking_model->get_brand($where);
            // If brand not exist then insert into table
            if (empty($brand_id_array)) {

                $inserted_brand_id = $this->booking_model->addNewApplianceBrand($this->FilesData['service_id'], trim($this->FilesData['appliance_brand']));
                if (!empty($inserted_brand_id)) {
                    log_message('info', __FUNCTION__ . ' Brand added successfully in Appliance Brands Table ' . $this->FilesData['appliance_brand']);
                } else {
                    log_message('info', __FUNCTION__ . ' Error in adding brands in Appliance Brands ' . $this->FilesData['appliance_brand']);
                }
            }
            return true;
        } else {
            return true;
        }
    }

    function set_booking_id() {
        log_message('info', "Entering: " . __METHOD__);
        $booking_id = '';
        if (!empty($this->FilesData['booking_date'])) {
            $yy = date("y", strtotime($this->FilesData['booking_date']));
            $mm = date("m", strtotime($this->FilesData['booking_date']));
            $dd = date("d", strtotime($this->FilesData['booking_date']));
        } else {
            $yy = date("y");
            $mm = date("m");
            $dd = date("d");
        }

        $booking_id_temp = str_pad($this->FilesData['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
        $booking_id_temp .= (intval($this->booking_model->getBookingCountByUser($this->FilesData['user_id'])) + 1);
        $random_code = mt_rand(100, 999);   //return 3 digit random code to make booking id unique
        $booking_id = "Q-" . $this->FilesData['source'] . "-" . $booking_id_temp . $random_code;

        $this->FilesData['booking_id'] = $booking_id;
    }

    function get_user_id() {
        log_message('info', "Entering: " . __METHOD__);
        //Search User by Mobile Number
        $output = $this->user_model->search_user($this->FilesData['booking_primary_contact_no']);

        if (empty($output)) {
            //User doesn't exist

            $user = array(
                "name" => $this->FilesData['name'],
                "phone_number" => $this->FilesData['booking_primary_contact_no'],
                "pincode" => $this->FilesData['pincode'],
                "user_email" => (isset($this->FilesData['email_id']) ? $this->FilesData['email_id'] : ""),
                "city" => $this->FilesData['city'],
                "state" => $this->FilesData['state'],
            );

            $this->FilesData['user_id'] = $this->user_model->add_user($user);

            //echo print_r($user, true), EOL;
            //Add sample appliances for this user
            $count = $this->booking_model->getApplianceCountByUser($this->FilesData['user_id']);

            //Add sample appliances if user has < 5 appliances in wallet
            if ($count < 5) {
                $this->booking_model->addSampleAppliances($this->FilesData['user_id'], 5 - intval($count));
            }
        } else {
            log_message('info', $this->FilesData['booking_primary_contact_no'] . ' exists');
            //User exists
            $this->FilesData['user_id'] = $output[0]['user_id'];
        }
    }

    function retrieve_data_from_excel($extension, $file_type, $partner_id) {

        switch ($extension) {
            case 'xlsx':
                $inputFileName = $_FILES['file']['tmp_name'];
                $inputFileExtn = 'Excel2007';
                break;
            case 'xls':
                $inputFileName = $_FILES['file']['tmp_name'];
                $inputFileExtn = 'Excel5';
                break;
        }

        try {
            //$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileExtn);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }

        //  Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        //Validation for Empty File
        if ($highestRow <= 1) {
            //Logging
            log_message('info', __FUNCTION__ . ' Empty File Uploaded');
            $this->session->set_flashdata('file_error', 'Empty file has been uploaded');

            redirect(base_url() . "employee/booking_excel/upload_shipped_products_excel");
        }

        $headings = $sheet->rangeToArray('A1:' . $highestColumn . 1, NULL, TRUE, FALSE);
        $headings_new = array();

        foreach ($headings as $heading) {
            $heading = str_replace(array("/", "(", ")", "."), "", $heading);
            array_push($headings_new, str_replace(array(" "), "_", $heading));
        }

        switch ($partner_id) {
            case SNAPDEAL_ID:
                return $this->get_rowdata_for_snapdeal($highestRow, $highestColumn, $sheet, $headings_new, $file_type, $partner_id);
            // break;
            case PAYTM:
                return $this->get_rowdata_for_paytm($highestRow, $highestColumn, $sheet, $headings_new, $partner_id);
            //break;
            case JEEVES_ID:
                return $this->get_rowdata_for_jeeves($highestRow, $highestColumn, $sheet, $headings_new, $partner_id);
        }
    }

    function retrieve_data_from_csv_for_paytm_mall($partner_id) {

        if ($_FILES['file']['size'] > 0) {
            $Filedata = array();
            $file = $_FILES['file']['tmp_name'];
            // $ph_invalid = array();
            $handle = fopen($file, "r");
            $escapeCounter = 0;
            $sku_invalid = array();
            $i = 0;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($escapeCounter > 0) {
                    $ph_validate = $this->validate_phone_number($data[10]);
                    if ($ph_validate) {
                        $sku_validate = $this->booking_model->get_sku_details(array('sku' => $data[3], 'sku_details.partner_id' => $partner_id));

                        if ($sku_validate) {

                            $data1['service_id'] = $sku_validate[0]['service_id'];
                            $data1['services'] = $sku_validate[0]['services'];
                            $data1['partner_id'] = $partner_id;
                            $data1['appliance_data'] = $sku_validate;
                            $data1['request_type'] = $sku_validate[0]['service_category'];
                            $data1['price_tags'] = $sku_validate[0]['service_category'];
                            $data1['reference_date'] = date('Y-m-d', strtotime($data[1]));
                            $data1['booking_date'] = date('d-m-Y', strtotime("+1 days", strtotime($data1['reference_date'])));
                            $data1['source'] = $sku_validate[0]['code'];
                            $data1['order_id'] = $data[2];
                            $data1['name'] = $data[8];
                            $data1['address'] = $data[9];
                            $data1['appliance_brand'] = '';
                            $data1['booking_primary_contact_no'] = $data[10];
                            $data1['pincode'] = $data[11];
                            $data1['sku'] = $data[3];
                            $data1['final_price'] = $data[7];
                            $data1['appliance_description'] = $data[5];
                            $data1['appliance_brand'] = '';
                            $data1['model_number'] = '';
                            $data1['city'] = '';
                            $data1['booking_timeslot'] = "4PM-7PM";
                            $data1['internal_status'] = _247AROUND_FOLLOWUP;
                            $data1['partner_source'] = "Paytm";
                            $data1['shipped_date'] = "";
                            $data1['delivery_date'] = "";
                            $data1['query_remarks'] = "";
                            $data1['estimated_delivery_date'] = '';
                            $data1['backup_estimated_delivery_date'] = '';
                            $data1['backup_delivery_date'] = '';
                            array_push($Filedata, $data1);
                            $data1 = array();
                            $i++;
                        } else {
                            array_push($sku_invalid, array($data[3]));
                        }
                    }
                }

                $escapeCounter = $escapeCounter + 1;
            }
            fclose($handle);
            $this->total_booking_came_today = $i;
            log_message('info', __FUNCTION__ . "=> exit loop...");
            if (!empty($sku_invalid)) {
                $subject = "Paytm Mall File Upload Failed. SKU NOT Found";
                $message = $this->file_name . " is not uploaded Agent Name: " . $this->session->userdata('employee_id');
                $message .= "<br/><br/>" . $this->table->generate($sku_invalid);
                $this->send_mail_column($subject, $message, false,PAYTM_MALL_FILE_FAILED);
            }
            return $Filedata;
        }
    }

    function get_rowdata_for_snapdeal($highestRow, $highestColumn, $sheet, $headings_new, $file_type, $partner_id) {
        log_message('info', __FUNCTION__ . "=> Entering For SnapDeal...");
        $ph_invalid_data = array();
        $ser_invalid_data = array();
        $pin_invalid_data = array();
        $ord_invalid_data = array();
        $FileData = array();

        for ($row = 2, $i = 0; $row <= $highestRow; $row++, $i++) {
            //  Read a row of data into an array
            $rowData_array = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            if (!empty($rowData_array[0][11])) {

                $rowData = array_combine($headings_new[0], $rowData_array[0]);


                if (!empty($rowData['Phone'])) {
                    //Sanitizing Brand Name
                    if (!empty($rowData['Brand'])) {
                        $rowData['Brand'] = preg_replace('/[^A-Za-z0-9 ]/', '', $rowData['Brand']);
                    }

                    if (isset($rowData['Delivery_Date'])) {

                        // Validate Phone Number
                        $ph_validate = $this->validate_phone_number($rowData['Phone']);
                        if ($ph_validate) {
                            // Validate Product
                            $ser_validate = $this->validate_product($rowData['Product'], $rowData['Product_Type']);
                            if ($ser_validate) {
                                if (isset($ser_validate['appliance_data'])) {
                                    $data['appliance_data'] = $ser_validate['appliance_data'];
                                }

                                $data['service_id'] = $ser_validate['service_id'];
                                $data['services'] = $ser_validate['services'];

                                // Validate Pincode
                                $pin_validate = $this->validate_pincode($rowData['Pincode']);
                                if ($pin_validate) {

                                    // Validate Order ID
                                    $ord_valiadte = $this->validate_order_id($rowData['Sub_Order_ID']);
                                    if ($ord_valiadte) {

                                        // Validate Product TYpe
                                        $pro_validate = $this->validate_product_type($rowData['Product_Type']);
                                        if ($pro_validate) {

                                            $data['order_id'] = $rowData['Sub_Order_ID'];
                                            $data['appliance_brand'] = $rowData['Brand'];
                                            $data['model_number'] = $rowData['Model'];
                                            $data['appliance_description'] = $rowData['Product_Type'];
                                            $data['name'] = $rowData['Customer_Name'];
                                            $data['address'] = $rowData['Customer_Address'];
                                            $data['pincode'] = $rowData['Pincode'];
                                            $data['city'] = $rowData['CITY'];
                                            $data['email_id'] = (isset($rowData['Email_ID']) ? $rowData['Email_ID'] : "");
                                            $data['booking_primary_contact_no'] = $rowData['Phone'];
                                            
                                            $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($rowData['Delivery_Date']);
                                            if ($file_type == "shipped") {

                                                if ($dateObj2->format('d') == date('d')) {
                                                    //If date is NULL, add 3 days from today in EDD.
                                                    $dateObj2 = date_create('+3days');
                                                }

                                                $data['booking_date'] = $dateObj2->format('d-m-Y');
                                                $data['partner_source'] = "Snapdeal-shipped-excel";

                                                // Set EDD only
                                                $data['estimated_delivery_date'] = $dateObj2->format('Y-m-d H:i:s');
                                                $data['delivery_date'] = '';
                                                $data['backup_estimated_delivery_date'] = $rowData['Delivery_Date'];
                                                $data['backup_delivery_date'] = '4AM-7PM';
                                                $data['internal_status'] = _247AROUND_FOLLOWUP;
                                                $data['booking_timeslot'] = '';
                                                $data['query_remarks'] = '';
                                            } else {


                                                $data['estimated_delivery_date'] = '';
                                                $data['partner_source'] = "Snapdeal-delivered-excel";
                                                $data['delivery_date'] = $dateObj2->format('Y-m-d H:i:s');
                                                $data['booking_date'] = '';
                                                $data['backup_delivery_date'] = $rowData['Delivery_Date'];
                                                $data['backup_estimated_delivery_date'] = '';
                                                $data['internal_status'] = "Missed_call_not_confirmed";
                                                $data['query_remarks'] = 'Product Delivered, Call Customer For Booking';
                                                $data['booking_remarks'] = '';
                                                $data['booking_timeslot'] = '4PM-7PM';
                                            }

                                            $data['shipped_date'] = '';

                                            $ref_date = PHPExcel_Shared_Date::ExcelToPHPObject($rowData['Referred_Date_and_Time']);
                                            $data['reference_date'] = $ref_date->format('Y-m-d H:i:s');
                                            $data['partner_id'] = $partner_id;
                                            $data['source'] = "SS";
                                            $data['request_type'] = "Installation & Demo";
                                            $data['price_tags'] = "Installation & Demo";
                                            array_push($FileData, $data);
                                            $data = array(); 
                                            log_message('info', __FUNCTION__ . "=> Data Set..");
                                        } else {
                                            //array_push($pro_invalid_data, $rowData); 
                                        }
                                    } else {
                                        $this->add_user_for_invalid($rowData);
                                        array_push($ord_invalid_data, $rowData);
                                    }
                                } else {
                                    $this->add_user_for_invalid($rowData);
                                    array_push($pin_invalid_data, $rowData);
                                }
                            } else {
                                $this->add_user_for_invalid($rowData);
                                array_push($ser_invalid_data, $rowData);
                            }
                        } else {
                            $this->add_user_for_invalid($rowData);
                            array_push($ph_invalid_data, $rowData);
                        }
                    } else {

                        $subject = "Delivery Date Column is not exist. SD Uploading Failed.";
                        $message = $this->file_name . " is not uploaded Agent Name: " . $this->session->userdata('employee_id');
                        $this->send_mail_column($subject, $message, false,SNAPDEAL_FAILED_FILE);
                    }
                } else {
                    log_message('info', __FUNCTION__ . "=> Phone Number empty...");
                    echo "Phone Number empty";
                }
            } else {
                log_message('info', __FUNCTION__ . "=> Empty Cell...");
            }
        }
        if (count($ph_invalid_data) > 4) {
            $status['invalid_phone'] = $ph_invalid_data;
            $this->get_invalid_data($status, $file_type, $this->file_name);
            log_message('info', __FUNCTION__ . "=> Exiting validation routine: Limit Crossed");

            exit();
        }

        if (count($ser_invalid_data) > 4) {
            $status['invalid_phone'] = $ser_invalid_data;
            $this->get_invalid_data($status, $file_type, $this->file_name);
            log_message('info', __FUNCTION__ . "=> Exiting validation routine: Limit Crossed");

            exit();
        }
        if (count($pin_invalid_data) > 4) {
            $status['invalid_phone'] = $pin_invalid_data;
            $this->get_invalid_data($status, $file_type, $this->file_name);
            log_message('info', __FUNCTION__ . "=> Exiting validation routine: Limit Crossed");

            exit();
        }
        if (count($ord_invalid_data) > 4) {
            $status['invalid_phone'] = $ord_invalid_data;
            $this->get_invalid_data($status, $file_type, $this->file_name);
            log_message('info', __FUNCTION__ . "=> Exiting validation routine: Limit Crossed");

            exit();
        }
        $this->total_booking_came_today = $i + 1;
        log_message('info', __FUNCTION__ . "=> exit loop...");
        return $FileData;
    }

    function get_rowdata_for_paytm($highestRow, $highestColumn, $sheet, $headings_new, $partner_id) {
        $data1 = array();
        for ($row = 2, $i = 0; $row <= $highestRow; $row++, $i++) {
            //  Read a row of data into an array
            $rowData_array = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            if (!empty($rowData_array[0][10])) {

                $rowData = array_combine($headings_new[0], $rowData_array[0]);

                if ($rowData['Customer_Contact_No'] == "") {
                    //echo print_r("Phone number null, break from this loop", true), EOL;
                    break;
                }

                //Sanitizing Brand Name
                if (!empty($rowData['Brand'])) {
                    $data['appliance_brand'] = preg_replace('/[^A-Za-z0-9 ]/', '', $rowData['Brand']);
                }
                $ser_validate = $this->validate_product(trim($rowData['Product_Category_L3']), trim($rowData['Product_Name']));

                if ($ser_validate) {
                    if (isset($ser_validate['appliance_data'])) {
                        $data['appliance_data'] = $ser_validate['appliance_data'];
                    }
                    $data['service_id'] = $ser_validate['service_id'];
                    $data['services'] = $ser_validate['services'];
                    $data['partner_id'] = $partner_id;

                    $user_name = $this->is_user_name_empty(trim($rowData['Customer_Name']), $rowData['customer_email'], $rowData['Customer_Contact_No']);
                    $data['name'] = $user_name;
                    $data['booking_primary_contact_no'] = $rowData['Customer_Contact_No'];
                    $data['email_id'] = $rowData['customer_email'];
                    $data['address'] = $rowData['Customer_Address_1'] . " ," . $rowData['Customer_Address_2'];
                    $data['pincode'] = $rowData['Customer_Pincode'];
                    $data['city'] = $rowData['Customer_City'];
                    $data['order_id'] = $rowData['Order_ID'];
                    $data['model_number'] = '';
                    $data['appliance_description'] = trim($rowData['Product_Name']);
                    $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($rowData['shipped_Date']);
                    $data['shipped_date'] = date('Y-m-d H:i:s', strtotime($dateObj2->format('d-m-Y')));
                    $data['estimated_delivery_date'] = date('d-m-Y', strtotime("+3 days", strtotime($data['shipped_date'])));
                    $data['delivery_date'] = '';
                    $data['backup_estimated_delivery_date'] = $rowData['shipped_Date'];
                    $data['backup_delivery_date'] = '';
                    $data['internal_status'] = _247AROUND_FOLLOWUP;
                    $data['booking_timeslot'] = '4AM-7PM';
                    $data['type'] = "Query";
                    $data['source'] = "SP";
                    $data['partner_source'] = "Paytm-delivered-excel";
                    $data['booking_date'] = date('d-m-Y', strtotime("+3 days", strtotime($data['shipped_date'])));
                    $data['request_type'] = 'Installation & Demo';
                    $data['price_tags'] = 'Installation & Demo';
                    $data['query_remarks'] = '';

                    array_push($data1, $data);
                    $data = array();
                }
            }
        }

        $this->total_booking_came_today = $i + 1;
        log_message('info', __FUNCTION__ . "=> exit loop...");
        return $data1;
    }

    function get_rowdata_for_jeeves($highestRow, $highestColumn, $sheet, $headings_new, $partner_id) {
        $data1 = array();
        $flag = 0;
        $this->table->set_heading('Phone Number');
        for ($row = 2, $i = 0; $row <= $highestRow; $row++, $i++) {
            //  Read a row of data into an array
            $rowData_array = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

            if (!empty($rowData_array[0][10])) {

                $rowData = array_combine($headings_new[0], $rowData_array[0]);

                $appliance_description = $rowData['Product_Name'] . ", " . $rowData['Model_Name'];
                $ser_validate = $this->validate_product(trim($rowData['Product_Name']), trim($appliance_description));
                if ($ser_validate) {
                    $data['appliance_description'] = $rowData['Product_Name'] . ", " . $rowData['Model_Name'];
                    $data['order_id'] = $rowData['CaseID'];
                    $data['name'] = $rowData['FirstName'];
                    $data['address'] = $rowData['Address'];
                    $data['city'] = $rowData['City'];
                    $data['pincode'] = $rowData['PinCode'];
                    $data['booking_primary_contact_no'] = $rowData['MobilePhone'];
                    $data['booking_alternate_booking_no'] = $rowData['HomePhone'];
                    $data['email_id'] = $rowData['Email'];
                    $data['appliance_brand'] = $rowData['Brand_Name'];


                    $data['model_number'] = $rowData['Model_Name'];

                    if (isset($ser_validate['appliance_data'])) {
                        $data['appliance_data'] = $ser_validate['appliance_data'];
                    }
                    $data['service_id'] = $ser_validate['service_id'];
                    $data['services'] = $ser_validate['services'];
                    $data['partner_id'] = $partner_id;
                    $data['request_type'] = 'Installation & Demo';
                    $data['booking_timeslot'] = "4PM-7PM";
                    $data['shipped_date'] = '';
                    $data['partner_serial_number'] = $rowData['SetSRLNo'];
                    $data['price_tags'] = '';
                    
                    if (stristr($rowData['ComplaintType'], "installation")){
                        $data['price_tags'] = "Installation & Demo";
                    }
                    
                    $data['query_remarks'] = $rowData['ServiceChargeType'];
                    $data['estimated_delivery_date'] = '';
                    $data['delivery_date'] = '';
                    $data['backup_estimated_delivery_date'] = '';
                    $data['backup_delivery_date'] = '';
                    $data['internal_status'] = _247AROUND_FOLLOWUP;
                    $data['current_status'] = _247AROUND_FOLLOWUP;
                    $data['booking_timeslot'] = '4AM-7PM';
                    $data['type'] = "Query";
                    $data['source'] = "SF";
                    $data['booking_date'] = "";
                    $data['partner_source'] = "Jeeves-delivered-excel";
                    array_push($data1, $data);
                    $data = array();
                     
                } else {
                    $this->table->add_row($rowData['MobilePhone']);
                }
            }
        }
       
        if ($flag == 1) {
            //SEND MAIl
            $to = ANUJ_EMAIL_ID . ", sales@247around.com, booking@247around.com";

            $cc = "";
            $message1 = "Appliance Not Found. Please chaeck File<br/>";
            $subject = "Appliance Not Found. Please chaeck File";
            $message1 .= $this->table->generate();

            $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $message1, "",APPLIANCE_NOT_FOUND);
        }
        
        return $data1;
    }

    /**
     * @desc: this is used to send mail while validation pass and column is not exist
     * @param String $subject
     * @param String $message
     * @param boolean $validation
     */
    function send_mail_column($subject, $message, $validation,$emailTag) {
        $to = ANUJ_EMAIL_ID . ", sales@247around.com, booking@247around.com";
        $from = NOREPLY_EMAIL_ID;
        $cc = "";
        $bcc = "";
        $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, "",$emailTag);
        log_message('info', __FUNCTION__ . "=> Validation " . $validation . "  " . $message);
        if ($validation == false) {
            exit();
        }
    }

    /**
     * @desc: This is used to send Json invalid data to mail
     * @param Array $invalid_data_with_reason
     * @param string $filetype
     */
    function get_invalid_data($invalid_data_with_reason, $filetype, $file_name) {

        $to = ANUJ_EMAIL_ID . ", sales@247around.com";
        $from = NOREPLY_EMAIL_ID;
        $cc = "";
        $bcc = "";
        $subject = "";

        if ($filetype == "shipped") {
            $subject = "Shipped File is uploaded";
            $message = " Please check shipped file data:<br/>" . " Agent Name " . $this->session->userdata('employee_id');
        } else {
            $subject = "Delivered File is uploaded";
            $message = " Please check delivered file data:<br/>" . " Agent Name " . $this->session->userdata('employee_id');
        }
        $invalid_data_with_reason['file_name'] = $file_name;

        $html = $this->load->view('employee/invalid_data', $invalid_data_with_reason, TRUE);
        // echo $html = $this->load->view('employee/invalid_data',$invalid_data_with_reason);
        $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $html, "",FILE_UPLOADED);
    }

    function validate_phone_number($phone_number) {
        log_message('info', __FUNCTION__ . "=> Entering validation routine...");

        // check mobile number validation
        if (!preg_match('/^\d{10}$/', $phone_number)) {

            return FALSE;
        } else {
            return TRUE;
        }
    }

    function validate_product($product, $product_type) {
        log_message('info', __FUNCTION__ . "=> Entering validation routine...");
        $appliance = "";
        $prod = trim($product);
        $app_data = array();
        $service_appliance_data = array();

        //check if service_id already exist or not by using product description
        if (!empty($product_type)) {
            $service_appliance_data = $this->booking_model->get_service_id_by_appliance_details(trim($product_type));
        }

        if (!empty($service_appliance_data)) {
            log_message('info', __FUNCTION__ . "=> Dsecription found");

            $app_data['appliance_data'] = $service_appliance_data;
            $app_data['service_id'] = $service_appliance_data[0]['service_id'];
            $app_data['services'] = $service_appliance_data[0]['services'];
            return $app_data;
        } else {
            log_message('info', __FUNCTION__ . "=> Dsecription not found");
            if (stristr($prod, "Washing Machine") || stristr($prod, "WashingMachine") || stristr($prod, "Dryer")) {
                $appliance = 'Washing Machine';
            }
            if (stristr($prod, "Television") || stristr($prod, "TV") || stristr($prod, "LED")) {
                $appliance = 'Television';
            }
            if (stristr($prod, "Airconditioner") || stristr($prod, "Air Conditioner") 
                    || stristr($prod, "WINDOW AIR CONDITIONER") || 
                    stristr($prod, "SPLIT AIR CONDITIONER") || 
                    stristr($prod, "AIR CONDITIONER")|| stristr($prod, "SPLIT AC") || stristr($prod, "WINDOW AC")) {
                $appliance = 'Air Conditioner';
            }
            if (stristr($prod, "Refrigerator")) {
                $appliance = 'Refrigerator';
            }
            if (stristr($prod, "Microwave")) {
                $appliance = 'Microwave';
            }
            if (stristr($prod, "Purifier")) {
                $appliance = 'Water Purifier';
            }
            if (stristr($prod, "Chimney")) {
                $appliance = 'Chimney';
            }
            if (stristr($prod, "Geyser")) {
                $appliance = 'Geyser';
            }
            // Block Microvare cooking. If its exist in the Excel file
            if (stristr($prod, "microwave cooking")) {

                return FALSE;
            }
            // Block Tds Meter. If its exist in the Excel file
            if (stristr($prod, "Tds Meter")) {
                return FALSE;
            }
            // Block Accessories. If its exist in the Excel file
            if (stristr($prod, "Accessories")) {
                return FALSE;
            }

            $service_id = $this->booking_model->getServiceId($appliance);
            if ($service_id) {
                $app_data['service_id'] = $service_id;
                $app_data['services'] = $appliance;
                return $app_data;
            } else {
                return FALSE;
            }
        }
    }

    function validate_pincode($pincode) {
        log_message('info', __FUNCTION__ . "=> Entering validation routine...");
        // check pincode is 6 digit
        if (!preg_match('/^\d{6}$/', $pincode)) {
            log_message('info', __FUNCTION__ . "=> Pincode Validation Failed..." . $pincode);
            return FALSE;
        }
        return TRUE;
    }

    function validate_order_id($Sub_Order_ID) {
        log_message('info', __FUNCTION__ . "=> Entering validation routine...");
        if (is_null($Sub_Order_ID) || $Sub_Order_ID = "") {

            return false;
        }
        return TRUE;
    }

    function validate_product_type($product_type) {
        log_message('info', __FUNCTION__ . "=> Entering validation routine...");

        // get unproductive description array
        $unproductive_description = $this->unproductive_product();

        $prod = trim($product_type);

        foreach ($unproductive_description as $un_description) {
            if (stristr($prod, $un_description)) {
                log_message('info', __FUNCTION__ . "=>  Invalid Product Description." . $un_description);
                return false;
            }
        }
        return true;
    }

    function unproductive_product() {
        $unproductive_description = array(
            'Tds Meter',
            'Water Purifier Accessories',
            'Room Heater',
            'Immersion Rod',
            '(PNG /LPG) Geyser',
            'Gas Geyser',
            'Set of 2',
            'Drinking Water Pump',
            'Set of 24 pcs',
            'Casseroles',
            'Spun Filter Cartridge',
            'Oil Filled Radiator',
            'Immersion Water Heater Rod',
            '10" Filter Housing Transparent',
            'Blow Hot Element Heater',
            'Bajaj Fan Heater',
            'Gas Geyser',
            'Ro Body Cover',
            'Pack Of 24 Pcs',
            'Mineral Water Pot Offline Non Electric Water Purifer Filter',
            'Membrane Ro Water Purifier',
            '15 Filter',
            'Hevy Duty 5000 Cartridge',
            'Cleanwell Filter',
            'CSM MEMBRANE 80 GPD',
            'Spun Filter pack of ',
            'Zero B Filter',
            'Tower Heater',
            'Oil Filled Heater'
        );

        return $unproductive_description;
    }

    function add_user_for_invalid($row_data) {
        foreach ($row_data as $value) {

            $output = $this->user_model->search_user(trim($value['Phone']));
            $distict_details = $this->vendor_model->get_distict_details_from_india_pincode(trim($value['Pincode']));

            if (empty($output)) {
                //User doesn't exist
                if (isset($value['Customer_Name']) || isset($value['Phone']) || isset($value['Customer_Address']) || isset($value['Pincode'])) {
                    $user['name'] = $value['Customer_Name'];
                    $user['phone_number'] = $value['Phone'];
                    $user['user_email'] = (isset($value['Email_ID']) ? $value['Email_ID'] : "");
                    $user['home_address'] = $value['Customer_Address'];
                    $user['pincode'] = $value['Pincode'];
                    $user['city'] = $value['CITY'];
                    $user['state'] = $distict_details['state'];

                    $user_id = $this->user_model->add_user($user);
                    //echo print_r($user, true), EOL;
                    //Add sample appliances for this user
                    $count = $this->booking_model->getApplianceCountByUser($user_id);
                    //Add sample appliances if user has < 5 appliances in wallet
                    if ($count < 5) {
                        $this->booking_model->addSampleAppliances($user_id, 5 - intval($count));
                    }
                }
            }
        }
    }

    /**
     * @desc: This method ued to insert data into partner leads table.
     */
    function insert_booking_in_partner_leads() {
        $partner_booking['PartnerID'] = $this->FilesData['partner_id'];
        $partner_booking['OrderID'] = $this->FilesData['order_id'];
        $partner_booking['247aroundBookingID'] = $this->FilesData['booking_id'];
        $partner_booking['Product'] = $this->FilesData['services'];
        $partner_booking['Brand'] = $this->FilesData['appliance_brand'];
        $partner_booking['Model'] = $this->FilesData['model_number'];
        $partner_booking['ProductType'] = $this->FilesData['appliance_description'];
        $partner_booking['Category'] = isset($this->FilesData['appliance_data'][0]['capacity']) ? $this->FilesData['appliance_data'][0]['capacity'] : '';
        $partner_booking['Name'] = $this->FilesData['name'];
        $partner_booking['Mobile'] = $this->FilesData['booking_primary_contact_no'];

        $partner_booking['Email'] = (isset($this->FilesData['email_id']) ? $this->FilesData['email_id'] : "");
        //$partner_booking['Landmark'] = $booking['booking_landmark'];
        $partner_booking['Address'] = $this->FilesData['address'];
        $partner_booking['Pincode'] = $this->FilesData['pincode'];
        $partner_booking['City'] = $this->FilesData['city'];
        $partner_booking['DeliveryDate'] = $this->FilesData['delivery_date'];
        $partner_booking['RequestType'] = $this->FilesData['request_type'];
        $partner_booking['ScheduledAppointmentDate'] = $this->FilesData['booking_date'];
        $partner_booking['ScheduledAppointmentTime'] = $this->FilesData['booking_timeslot'];
        $partner_booking['Remarks'] = '';
        $partner_booking['PartnerRequestStatus'] = "";
        $partner_booking['247aroundBookingStatus'] = "FollowUp";
        $partner_booking['247aroundBookingRemarks'] = "FollowUp";
        $partner_booking['create_date'] = date('Y-m-d H:i:s');

        $partner_leads_id = $this->partner_model->insert_partner_lead($partner_booking);
        if ($partner_leads_id) {
            return true;
        } else {
            log_message('info', __FUNCTION__ . " Booking is not inserted into Partner Leads table:" . print_r($partner_booking, true));
        }
    }

    /**
     * @Desc: This function is used to check if user name is empty or not
     * if user name is not empty then return username otherwise check if email is not
     * empty.if email is empty then return mobile number as username otherwise return email as username 
     * @params: String
     * @return: void
     * 
     */
    private function is_user_name_empty($userName, $userEmail, $userContactNo) {
        if (empty($userName)) {
            if (empty($userEmail)) {
                $user_name = $userContactNo;
            } else {
                $user_name = $userEmail;
            }
        } else {
            $user_name = $userName;
        }

        return $user_name;
    }
    
    
    /**
     * @Desc: This function is used to get the upload file history
     * @params: void
     * @return: void
     * 
     */
    public function get_upload_file_history()
    {





        $post_data = array('length' =>$this->input->post('length'),
                           'start' =>$this->input->post('start'),
                           'file_type' =>trim($this->input->post('file_type')),
                           'search_value' => trim($this->input->post('search')['value']),
                           'partner_id'=>$this->input->post('partner_id')
                        );
        
        $filtered_post_data = array(
                'length' =>NULL,
                'start' =>NULL,
                'file_type' =>trim($this->input->post('file_type')),
                'search_value' => trim($this->input->post('search')['value'])
        );
        
        if (!empty($this->input->post('file_source')) && $this->input->post('file_source') == "partner_file_upload") {
            $post_data['file_type_not_equal_to'] = 'Partner_Summary_Reports';
            $filtered_post_data['file_type_not_equal_to'] = 'Partner_Summary_Reports';
        }
        
        if(!empty($this->input->post('partner_id'))){
            $post_data['partner_id'] = $this->input->post("partner_id");
            $filtered_post_data['partner_id'] = $this->input->post("partner_id");
        }
        
    //    $post_data['partner_id']

        $list = $this->reporting_utils->get_uploaded_file_history($post_data);


        $table_data = array();
        $no = $post_data['start'];
        foreach ($list as $file_list) {
            $no++;
            $file_list->file_source = $this->input->post('file_source');
            $row =  $this->upload_file_table_data($file_list, $no);
            $table_data[] = $row;
        }



        $allRecords = $this->reporting_utils->get_uploaded_file_history();


        $allFilteredRecords = $this->reporting_utils->get_uploaded_file_history($filtered_post_data);
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => count($allRecords),
            "recordsFiltered" =>  count($allFilteredRecords),
            "data" => $table_data,
        );
        unset($post_data);
        echo json_encode($output);
    }
    
    /**
     * @Desc: This function is used to make the table data for upload file history
     * @params: void
     * @return: void
     * 
     */
    private function upload_file_table_data($file_list, $no)
    {
        if($file_list->result === FILE_UPLOAD_SUCCESS_STATUS){
            $result = "<div class='label label-success'>$file_list->result</div>";
        }else if($file_list->result === FILE_UPLOAD_FAILED_STATUS){
            $result = "<div class='label label-danger'>$file_list->result</div>";
        }else{
            $result = $file_list->result;
        }
        
        $row = array();
        $row[] = $no;

        // BITBUCKET_DIRECTORY

        $row[] = "<a target='_blank' href='https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/".$file_list->file_name."'>".$file_list->file_name."</a>";

             //  $row[] = "Abhishek";

        $row[] = $file_list->agent_name;

        $row[] = date('d M Y H:i:s', strtotime($file_list->upload_date));

        if($file_list->file_source == 'partner_file_upload'){
            if(!empty($file_list->revert_file_name)){
                $row[] = '<button type="button" onclick="view_revert_file('.$file_list->id.')" class="btn btn-xs btn-warning" data-toggle="modal" data-target="#revert_file_model">View Revert File</button>';
            }
            else{
                $row[] = '';
            }
        }
        $row[] = $result;
        
        return $row;
    }
    
    public function get_revert_file_details(){
        $id = $this->input->post('id');
        $select = "revert_file_name, revert_file_subject, revert_file_from, revert_file_to, revert_file_cc";
        $data = $this->reusable_model->get_search_result_data('file_uploads', $select, array('id'=>$id), '', '', '', '', '');
        echo json_encode($data);
    }
    
}
